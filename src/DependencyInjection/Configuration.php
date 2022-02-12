<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('firebase_authentication');
		$treeBuilder
			->getRootNode()
				->children()
					->scalarNode('project_id')
					->defaultValue('%env(string:FIREBASE_PROJECT_ID)%')
				->end()
					->scalarNode('leeway')
					->info('The leeway to account for clock skew with Google servers')
					->defaultValue(0)
				->end()
					->enumNode('extractor')
					->info('The strategy used to extract the token from the HTTP request')
					->values(['bearer', 'cookie'])
					->defaultValue('bearer')
				->end()
					->scalarNode('cookie_name')
					->info('If the extractor is of type "cookie", sets the name of the cookie to be extracted')
					->defaultValue('sessionToken')
				->end()
			->end()
		->end();

		return $treeBuilder;
	}
}
