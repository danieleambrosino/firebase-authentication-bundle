<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\JWSValidatorInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWSValidator\HeaderValidatorTrait;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWSValidator\PayloadValidatorTrait;
use DanieleAmbrosino\FirebaseAuthenticationBundle\JWSValidator\SignatureValidatorTrait;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use JsonException;
use OpenSSLAsymmetricKey;

/**
 * Validates JWS using RSASSA-PKCS1-v1_5 SHA-256.
 * @link https://datatracker.ietf.org/doc/html/rfc7515#appendix-A.2
 */
class RS256JWSValidator implements JWSValidatorInterface
{
	use HeaderValidatorTrait, PayloadValidatorTrait, SignatureValidatorTrait;

	/**
	 * The header of the JWS.
	 */
	private ?array $header = null;

	/**
	 * The payload of the JWS.
	 */
	private ?array $payload = null;

	/**
	 * The signature of the JWS.
	 */
	private ?string $signature = null;

	/**
	 * The initial substring of the JWS Compact Serialization representation
	 * up until but not including the second period character.
	 * 
	 * @link https://datatracker.ietf.org/doc/html/rfc7515#section-2
	 */
	private ?string $signingInput = null;

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
	 * @link https://datatracker.ietf.org/doc/html/rfc7519#section-4.1.4
	 */
	private ?DateInterval $leeway = null;


	public function __construct(
		/**
		 * The ID of the Firebase project.
		 */
		private string $firebaseProjectId
	) {
		// By default, set the leeway to 0 seconds
		$this->leeway = new DateInterval('PT0S');
	}

	/**
	 * @inheritdoc
	 */
	public function validate()
	{
		$this->checkJWSAndPublicKeysAreLoaded();
		$this->now = new DateTimeImmutable();

		$this->verifyHeaderClaims();
		$this->verifyPayloadClaims();
		$this->verifySignature();
	}

	/**
	 * @inheritdoc
	 */
	public function setJWS(string $token): self
	{
		[$this->header, $this->payload, $this->signature] = self::decode($token);
		$this->signingInput = self::getSigningInput($token);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setPublicKeys(PublicKeyCollectionInterface $publicKeyCandidates): self
	{
		$this->publicKeyCandidates = $publicKeyCandidates;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setLeeway(int $leeway): self
	{
		if ($leeway < 0) {
			throw new InvalidArgumentException('Leeway must be a positive number');
		}

		$this->leeway = new DateInterval('PT' . $leeway . 'S');
		return $this;
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

	/**
	 * Decodes the token splitting it into header, payload and signature.
	 * All the three parts are base64 decoded; the header and the payload 
	 * are also JSON decoded as associative arrays.
	 * 
	 * @param string $token The encoded JWT.
	 * @return array An array with `[$header, $payload, $signature]`
	 */
	private static function decode(string $token): array
	{
		$explodedToken = explode('.', $token, 3);
		if (count($explodedToken) !== 3) {
			throw new InvalidArgumentException('Invalid JWT');
		}

		[$header, $payload, $signature] = $explodedToken;
		try {
			[$header, $payload, $signature] = [
				json_decode(self::base64UrlDecode($header), true, flags: JSON_THROW_ON_ERROR),
				json_decode(self::base64UrlDecode($payload), true, flags: JSON_THROW_ON_ERROR),
				self::base64UrlDecode($signature),
			];
		} catch (InvalidArgumentException | JsonException $e) {
			throw new InvalidArgumentException('The JWT cannot be decoded because it was not properly encoded');
		}

		return [$header, $payload, $signature];
	}

	/**
	 * Returns the encoded header and payload concatenated with a '.',
	 * which is the cryptographically signed data.
	 * 
	 * @param string $token The encoded token.
	 * @return string The signed data.
	 */
	private static function getSigningInput(string $token): string
	{
		return implode('.', explode('.', $token, -1));
	}

	/**
	 * Implements the "base64url" decoding algorithm.
	 * 
	 * @param string $input The encoded input.
	 * @return string The decoded output.
	 * @throws InvalidArgumentException If the input cannot be decoded.
	 * @link https://datatracker.ietf.org/doc/html/rfc4648#section-5
	 */
	private static function base64UrlDecode(string $input): string
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$paddingLength = 4 - $remainder;
			$input .= str_repeat('=', $paddingLength);
		}
		$decoded = base64_decode(strtr($input, '-_', '+/'));
		if ($decoded === false) {
			throw new InvalidArgumentException('The token is not correctly base64 encoded');
		}
		return $decoded;
	}

	/**
	 * Checks if all the parts of the JWT and the public keys
	 * have been correctly set.
	 * @throws InvalidArgumentException If one or more of the components have not been loaded.
	 */
	private function checkJWSAndPublicKeysAreLoaded()
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
