<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services\RS256JWSValidator;

use InvalidArgumentException;

trait EmailValidatorTrait
{
	private function assertEmailIsVerified()
	{
		if (!isset($this->payload['email_verified'])) {
			throw new InvalidArgumentException('Cannot tell if the email address has been verified ("email_verified" claim missing)');
		}

		if ($this->payload['email_verified'] !== true) {
			throw new InvalidArgumentException('Email address is not verified');
		}
	}
}