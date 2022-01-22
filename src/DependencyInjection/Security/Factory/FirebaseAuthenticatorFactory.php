<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
	 * Sets the configuration definition for the FirebaseAuthenticator
	 * on a per-firewall basis.
	 * 
	 * @param ArrayNodeDefinition $builder The configuration definition builder.
	 */
	public function addConfiguration(NodeDefinition $builder)
	{
		$builder
			->children()
				->scalarNode('leeway')
					->defaultValue(0)
					->end()
			->end();
	}

	/**
	 * @inheritdoc
	 */
	public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string|array
	{
		$authenticatorId = 'security.authenticator.firebase.' . $firewallName;
		$leeway = $config['leeway'];
		$container
			->setDefinition($authenticatorId, new ChildDefinition('firebase_authentication.authenticator'))
			->setArgument('$leeway', $leeway);
		return $authenticatorId;
	}
}
