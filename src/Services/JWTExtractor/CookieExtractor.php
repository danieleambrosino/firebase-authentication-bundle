<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services\JWTExtractor;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class CookieExtractor implements JWTExtractorInterface
{
	public function __construct(
		private string $cookieName
	) {
	}

	public function supports(Request $request): bool
	{
		return $request->cookies->has($this->cookieName);
	}

	public function extract(Request $request): string
	{
		return $request->cookies->get($this->cookieName);
	}
}
