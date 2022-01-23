<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services\RS256JWSValidator;

use DateTime;
use InvalidArgumentException;

/**
 * @property array $payload The payload of the JWT.
 */
trait PayloadValidatorTrait
{

	private function verifyPayloadClaims()
	{
		// exp
		$this->assertIsNotExpired();
		// iat
		$this->assertHasBeenIssuedInThePast();
		// aud
		$this->assertIsIntendedForThisProject();
		// iss
		$this->assertHasBeenIssuedByGoogle();
		// sub
		$this->assertHasSubject();
		// auth_time
		$this->assertAuthTimeIsInThePast();
		// email_verified
		$this->assertEmailIsVerified();
	}

	private function assertIsNotExpired()
	{
		if (!isset($this->payload['exp'])) {
			throw new InvalidArgumentException('Expiration time is not set');
		}
		$expirationTime = DateTime::createFromFormat('U', $this->payload['exp'])->add($this->leeway);
		if ($this->now >= $expirationTime) {
			throw new InvalidArgumentException('The JWT is expired');
		}
	}

	private function assertHasBeenIssuedInThePast()
	{
		if (!isset($this->payload['iat'])) {
			throw new InvalidArgumentException('Issue time is not set');
		}

		$issueTime = DateTime::createFromFormat('U', $this->payload['iat'])->sub($this->leeway);
		if ($issueTime >= $this->now) {
			throw new InvalidArgumentException('The JWT has been issued in the future');
		}
	}

	private function assertIsIntendedForThisProject()
	{
		if (!isset($this->payload['aud'])) {
			throw new InvalidArgumentException('Audience is not set');
		}

		if ($this->firebaseProjectId !== $this->payload['aud']) {
			throw new InvalidArgumentException('The JWT is not for the intended audience');
		}
	}

	private function assertHasBeenIssuedByGoogle()
	{
		if (!isset($this->payload['iss'])) {
			throw new InvalidArgumentException('Issuer is not set');
		}

		if ($this->payload['iss'] !== "https://securetoken.google.com/{$this->firebaseProjectId}") {
			throw new InvalidArgumentException('The JWT has not been issued by Google');
		}
	}

	private function assertHasSubject()
	{
		if (!isset($this->payload['sub'])) {
			throw new InvalidArgumentException('Subject is not set');
		}

		if (
			!is_string($this->payload['sub']) ||
			$this->payload['sub'] === ''
		) {
			throw new InvalidArgumentException('The subject of the JWT is not valid');
		}
	}

	private function assertAuthTimeIsInThePast()
	{
		if (!isset($this->payload['auth_time'])) {
			throw new InvalidArgumentException('Authentication time is not set');
		}

		$authenticationTime = DateTime::createFromFormat('U', $this->payload['auth_time'])->sub($this->leeway);
		if ($authenticationTime >= $this->now) {
			throw new InvalidArgumentException('Authentication time is in the future');
		}
	}

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
