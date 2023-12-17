<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class FirebaseAuthenticatorFactory implements AuthenticatorFactoryInterface
{
	/**
	 * @inheritdoc
	 */
	public function getPriority(): int
	{
		return 0;
	}

	/**
	 * @inheritdoc
	 */
	public function getKey(): string
	{
		return 'firebase';
	}

	/**
	 * @param ArrayNodeDefinition $builder The configuration definition builder.
	 */
	public function addConfiguration(NodeDefinition $builder): void
	{
		$builder->children()
			->enumNode('strategy')
				->values(['bearer', 'cookie'])
				->defaultValue('bearer')
				->end()
			->booleanNode('verify_email')
				->defaultFalse();
	}

	/**
	 * @inheritdoc
	 */
	public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string|array
	{
		$authenticatorId = 'security.authenticator.firebase.' . $firewallName;

		$fetcherDefinition = $container->setDefinition($authenticatorId . '.public_key_fetcher', new ChildDefinition('firebase_authentication.public_key_fetcher'));
		$fetcherDefinition
			->setArgument('$strategy', $config['strategy']);

		$validatorDefinition = $container->setDefinition($authenticatorId . '.jws_validator', new ChildDefinition('firebase_authentication.jws_validator'));
		$validatorDefinition
			->setArgument('$strategy', $config['strategy'])
			->setArgument('$verifyEmail', $config['verify_email'])
		;

		$container
			->setDefinition($authenticatorId, new ChildDefinition('firebase_authentication.authenticator'))
				->setArgument('$jwtExtractor', new Reference('firebase_authentication.extractor.' . $config['strategy']))
				->setArgument('$publicKeyFetcher', $fetcherDefinition)
				->setArgument('$jwsValidator', $validatorDefinition)
				->setArgument('$leeway', new Parameter('firebase_authentication.leeway'));

		return $authenticatorId;
	}
}
