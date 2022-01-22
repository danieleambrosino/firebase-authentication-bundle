<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWTValidatorInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWTValidator\HeaderValidatorTrait;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWTValidator\PayloadValidatorTrait;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWTValidator\SignatureVerifierTrait;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use OpenSSLAsymmetricKey;

class JWTValidator implements JWTValidatorInterface
{

	use HeaderValidatorTrait, PayloadValidatorTrait, SignatureVerifierTrait;

	/**
	 * The header of the JWT.
	 */
	private ?array $header = null;

	/**
	 * The payload of the JWT.
	 */
	private ?array $payload = null;

	/**
	 * The signature of the JWT.
	 */
	private ?string $signature = null;

	/**
	 * The encoded header and payload concatenated with a '.',
	 * which is the data cryptographycally signed.
	 */
	private ?string $signedData = null;

	/**
	 * The collection of candidate public keys.
	 */
	private ?PublicKeyCollectionInterface $publicKeyCandidates = null;

	/**
	 * The public key used to verify the signature.
	 */
	private ?OpenSSLAsymmetricKey $publicKey = null;

	/**
	 * Current time updated each time the validation process starts.
	 */
	private ?DateTimeInterface $now = null;

	/**
	 * The leeway used to account for clock skew.
	 */
	private ?DateInterval $leeway = null;


	public function __construct(
		/**
		 * The ID of the Firebase project.
		 */
		private string $firebaseProjectId
	) {
	}

	/**
	 * @inheritdoc
	 */
	public function validate()
	{
		$this->checkJWTAndPublicKeysAreLoaded();
		$this->now = new DateTimeImmutable();

		$this->verifyHeaderClaims();
		$this->verifyPayloadClaims();
		$this->verifySignature();
	}

	/**
	 * @inheritdoc
	 */
	public function getEmail(): string
	{
		if (
			!isset($this->payload['email']) ||
			!is_string($this->payload['email'])
		) {
			throw new InvalidArgumentException('The JWT does not contain an email');
		}
		return $this->payload['email'];
	}

	public function setJWT(string $token): self
	{
		[$this->header, $this->payload, $this->signature] = $this->decode($token);
		$this->signedData = $this->getSignedData($token);

		return $this;
	}

	private function getSignedData(string $token): string
	{
		return implode('.', explode('.', $token, -1));
	}

	public function setPublicKeys(PublicKeyCollectionInterface $publicKeyCandidates): self
	{
		$this->publicKeyCandidates = $publicKeyCandidates;
		return $this;
	}

	public function setLeeway(int $leeway): self
	{
		if ($leeway < 0) {
			throw new InvalidArgumentException('Leeway must be a positive number');
		}

		$this->leeway = new DateInterval('PT' . $leeway . 'S');
		return $this;
	}

	private function decode(string $token): array
	{
		$explodedToken = explode('.', $token, 3);
		if (count($explodedToken) !== 3) {
			throw new InvalidArgumentException('Invalid JWT');
		}

		[$header, $payload, $signature] = $explodedToken;
		[$header, $payload, $signature] = [
			json_decode($this->urlSafeBase64Decode($header), true),
			json_decode($this->urlSafeBase64Decode($payload), true),
			$this->urlSafeBase64Decode($signature),
		];

		if ($header === false || $payload === false || $signature === false) {
			throw new InvalidArgumentException('Invalid JWT');
		}

		return [$header, $payload, $signature];
	}

	private function urlSafeBase64Decode(string $input): string
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$paddingLength = 4 - $remainder;
			$input .= str_repeat('=', $paddingLength);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}

	private function checkJWTAndPublicKeysAreLoaded()
	{
		if (
			$this->header === null ||
			$this->payload === null ||
			$this->signature === null ||
			$this->publicKeyCandidates === null
		) {
			throw new InvalidArgumentException('JWT and public keys are not properly loaded');
		}
	}

}
