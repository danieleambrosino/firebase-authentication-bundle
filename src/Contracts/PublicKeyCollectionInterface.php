<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts;

use InvalidArgumentException;
use Serializable;

/**
 * Stores a set of public keys indexed by their ID.
 */
interface PublicKeyCollectionInterface extends Serializable
{
	/**
	 * Gets the public key corresponding to the given ID.
	 * 
	 * @return string The corresponding public key.
	 * @throws InvalidArgumentException If no corresponding key is found.
	 */
	public function get(string $id): string;

	/**
	 * Sets a public key with the given ID.
	 * If the given ID does not exist, the public key is added to the collection.
	 * Otherwise, the public key is overwritten.
	 * 
	 * @return PublicKeyCollectionInterface The updated collection.
	 */
	public function set(string $id, string $value): self;
}
