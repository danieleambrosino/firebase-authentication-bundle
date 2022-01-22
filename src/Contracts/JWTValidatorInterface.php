<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts;

use InvalidArgumentException;

/**
 * Validates a JWT, verifying its claims and its signature.
 */
interface JWTValidatorInterface
{

	/**
	 * Sets the token to decode and validate.
	 * 
	 */
	public function setJWT(string $token): self;

	/**
	 * Sets the collection of candidate public keys to verify the signature.
	 */
	public function setPublicKeys(PublicKeyCollectionInterface $publicKeys): self;

	/**
	 * Sets the leeway (in seconds) used to account for clock skew with Google's servers.
	 */
	public function setLeeway(int $leeway): self;
	
	/**
	 * Validates the currently set token using the appropriate public key.
	 * 
	 * @throws InvalidArgumentException if the validation fails.
	 */
	public function validate();

	/**
	 * Returns the email from the validated payload.
	 */
	public function getEmail(): string;
}
