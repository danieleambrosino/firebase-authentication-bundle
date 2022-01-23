<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts;

use InvalidArgumentException;

/**
 * Validates a JWS, verifying its claims and its signature.
 * @link https://datatracker.ietf.org/doc/html/rfc7515
 */
interface JWSValidatorInterface
{
	/**
	 * Sets the token and decodes it.
	 * 
	 * @return self The updated Validator.
	 * @throws InvalidArgumentException If the decoding fails.
	 */
	public function setJWS(string $token): self;

	/**
	 * Sets the collection of candidate public keys to verify the signature.
	 * 
	 * @return self The updated Validator.
	 */
	public function setPublicKeys(PublicKeyCollectionInterface $publicKeys): self;

	/**
	 * Sets the leeway (in seconds) used to account for clock skew with Google's servers.
	 * 
	 * @return self The updated Validator.
	 * @throws InvalidArgumentException If a negative leeway is set.
	 */
	public function setLeeway(int $leeway): self;
	
	/**
	 * Validates the currently set token, verifying that the claims conform to their constraints
	 * and that the signature is valid using the appropriate public key.
	 * 
	 * @throws InvalidArgumentException If the validation fails.
	 * @link https://firebase.google.com/docs/auth/admin/verify-id-tokens#verify_id_tokens_using_a_third-party_JWS_library
	 */
	public function validate();

	/**
	 * Returns the email from the validated payload.
	 */
	public function getEmail(): string;
}
