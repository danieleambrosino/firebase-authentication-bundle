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
	 * Gets the public key corresponding to the given id.
	 * 
	 * @return string The corresponding public key.
	 * @throws InvalidArgumentException If no corresponding key is found.
	 */
	public function get(string $id): string;

	/**
	 * Sets a public key with the given id.
	 * If the given id does not exist, the public key it is added to the collection.
	 * Otherwise, the public key is overwritten.
	 * 
	 * @return PublicKeyCollectionInterface The updated collection.
	 */
	public function set(string $id, string $value): self;
}
