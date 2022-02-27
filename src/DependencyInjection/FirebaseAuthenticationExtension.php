<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class FirebaseAuthenticationExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		$container->setParameter('firebase_authentication.project_id',  $config['project_id']);
		$container->setParameter('firebase_authentication.leeway',      $config['leeway']);

		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
		$loader->load('services.yaml');
	}
}
