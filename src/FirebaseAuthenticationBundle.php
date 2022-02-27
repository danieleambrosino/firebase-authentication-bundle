<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle;

use DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection\Security\Factory\FirebaseAuthenticatorFactory;
use DanieleAmbrosino\FirebaseAuthenticationBundle\DependencyInjection\Security\Factory\UserProviderFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FirebaseAuthenticationBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
		/** @var SecurityExtension $securityExtension */
		$securityExtension = $container->getExtension('security');
		$securityExtension->addAuthenticatorFactory(new FirebaseAuthenticatorFactory());
		$securityExtension->addUserProviderFactory(new UserProviderFactory());
	}
}
