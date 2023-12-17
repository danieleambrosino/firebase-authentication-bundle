<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UserProviderFactory implements UserProviderFactoryInterface
{
	public function getKey(): string
	{
		return 'firebase';
	}

	/**
	 * @param ArrayNodeDefinition $builder
	 */
	public function addConfiguration(NodeDefinition $builder): void
	{
		$builder->children()
			->booleanNode('active')->defaultTrue()
			->end();
	}

	public function create(ContainerBuilder $container, string $id, array $config): void
	{
		$container->setDefinition($id, new ChildDefinition('firebase_authentication.user_provider'));
	}
}
