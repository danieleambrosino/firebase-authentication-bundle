<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('firebase_authentication');
		$treeBuilder->getRootNode()
			->children()
				->scalarNode('project_id')
				->defaultValue('%env(string:FIREBASE_PROJECT_ID)%')
				->end()
			->end()
		->end();

		return $treeBuilder;
	}
}
