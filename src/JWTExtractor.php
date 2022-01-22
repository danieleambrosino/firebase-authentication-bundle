<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTExtractorInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class JWTExtractor implements JWTExtractorInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function extract(Request $request): string
	{
		if (!$request->headers->has('Authorization')) {
			throw new TokenNotFoundException('Authorization header not found!');
		}

		$authorizationHeader = $request->headers->get('Authorization');
		$headerParts = explode(' ', $authorizationHeader, 3);

		if (count($headerParts) !== 2 || strcasecmp($headerParts[0], 'Bearer') !== 0) {
			throw new InvalidArgumentException('Invalid authorization header!');
		}

		return $headerParts[1];
	}
}
