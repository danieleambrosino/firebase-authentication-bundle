<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services\JWTExtractor;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTExtractorInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class BearerExtractor implements JWTExtractorInterface
{

	public function supports(Request $request): bool
	{
		return $request->headers->has('Authorization');
	}
	
	/**
	 * @inheritdoc
	 * According to the specification, the token is parsed
	 * assuming that exactly one whitespace character separates
	 * the authentication scheme ('Bearer') and the token.
	 * @link https://datatracker.ietf.org/doc/html/rfc6750#section-2.1
	 */
	public function extract(Request $request): string
	{
		$authorizationHeader = $request->headers->get('Authorization');
		$headerParts = explode(' ', $authorizationHeader, 3);

		if (count($headerParts) !== 2 || $headerParts[0] !== 'Bearer') {
			throw new InvalidArgumentException('Invalid authorization header!');
		}

		return $headerParts[1];
	}
}
