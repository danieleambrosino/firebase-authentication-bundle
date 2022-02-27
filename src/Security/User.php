<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{

	public function __construct(
		private string $identifier,
		private array $roles = []
	) {
	}

	public function getUserIdentifier(): string
	{
		return $this->identifier;
	}

	public function getRoles(): array
	{
		return $this->roles;
	}

	public function eraseCredentials()
	{
	}
}
