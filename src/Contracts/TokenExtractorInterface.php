<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Extracts the encoded JWT from the `Authorization` HTTP header.
 */
interface TokenExtractorInterface
{
	/**
	 * Extracts an authentication token from an HTTP request.
	 * Returns a string containing the encoded token,
	 * or throws an exception if the extraction fails.
	 * 
	 * @param Request $request The request to analyze.
	 * @return string The extracted token.
	 * 
	 * @throws InvalidArgumentException If the extraction fails.
	 */
	public function extract(Request $request): string;
}
