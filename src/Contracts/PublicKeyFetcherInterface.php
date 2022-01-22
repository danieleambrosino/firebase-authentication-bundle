<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;

/**
 * Retrieves Google's public keys from the appropriate source.
 */
interface PublicKeyFetcherInterface
{
	/**
	 * Gets all the available public keys.
	 * 
	 * @return PublicKeyCollectionInterface
	 */
	public function getKeys(): PublicKeyCollectionInterface;
}
