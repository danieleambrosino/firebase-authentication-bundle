<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Security;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTValidatorInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyFetcherInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTExtractorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Throwable;

class FirebaseAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{

	public function __construct(
		private JWTExtractorInterface $jwtExtractor,
		private PublicKeyFetcherInterface $publicKeyFetcher,
		private JWTValidatorInterface $jwtValidator,
		private int $leeway = 0
	) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function start(Request $request, AuthenticationException $authException = null): Response
	{
		return new JsonResponse([
			'message' => 'JWT required'
		], 401);
	}

	/**
	 * Kick-in only if an `Authorization` header is found.
	 * @inheritdoc
	 */
	public function supports(Request $request): ?bool
	{
		return $request->headers->has('Authorization');
	}

	/**
	 * @inheritdoc
	 */
	public function authenticate(Request $request): Passport
	{
		try {
			$token = $this->jwtExtractor->extract($request);
		$publicKeyCollection = $this->publicKeyFetcher->getKeys();

			$this->jwtValidator
				->setJWT($token)
				->setPublicKeys($publicKeyCollection)
				->setLeeway($this->leeway);

			$this->jwtValidator->validate();

			$email = $this->jwtValidator->getEmail();
		} catch (InvalidArgumentException $e) {
			throw new AuthenticationException($e->getMessage());
		}

		return new SelfValidatingPassport(new UserBadge($email));
	}

	/**
	 * Return null to pass the request to the next handler.
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		return null;
	}

	/**
	 * Return the appropriate 401 HTTP response.
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		return new JsonResponse([
			'message' => $exception->getMessage()
		], 401);
	}
}
