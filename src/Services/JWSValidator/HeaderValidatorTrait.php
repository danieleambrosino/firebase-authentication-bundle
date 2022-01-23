<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services\JWSValidator;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use InvalidArgumentException;

/**
 * @property array $header The header of the JWT.
 * @property PublicKeyCollectionInterface $publicKeyCandidates The collection of candidate public keys.
 */
trait HeaderValidatorTrait
{
	/**
	 * Verifies that all the claims in the header
	 * conform to the appropriate constraints.
	 * 
	 * @throws InvalidArgumentException If any of the contraints is not respected.
	 */
	private function verifyHeaderClaims()
	{
		//alg
		$this->assertIsSignedUsingRS256();
		//kid
		$this->assertKeyExists();
	}

	private function assertIsSignedUsingRS256()
	{
		if (!isset($this->header['alg'])) {
			throw new InvalidArgumentException('alg header claim is not set');
		}
		if ($this->header['alg'] !== 'RS256') {
			throw new InvalidArgumentException('The algorithm used to sign the JWT is not RS256');
		}
	}

	private function assertKeyExists()
	{
		if (!isset($this->header['kid'])) {
			throw new InvalidArgumentException('Key ID is not set');
		}
		$this->publicKeyCandidates->get($this->header['kid']);
	}
}
