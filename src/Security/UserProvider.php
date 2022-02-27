<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
	public function loadUserByIdentifier(string $identifier): UserInterface
	{
		return new User($identifier);
	}

	public function supportsClass(string $class): bool
	{
		return $class === User::class;
	}

	public function refreshUser(UserInterface $user): UserInterface
	{
		return $user;
	}
}
