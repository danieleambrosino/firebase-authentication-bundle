<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\JWSValidator;

use InvalidArgumentException;

/**
 * @property array $header The header of the JWT.
 * @property PublicKeyCollectionInterface $publicKeyCandidates The collection of candidate public keys.
 */
trait SignatureValidatorTrait
{
	private function verifySignature()
	{
		$publicKeyInPEMFormat = $this->publicKeyCandidates->get($this->header['kid']);
		$this->publicKey = openssl_pkey_get_public($publicKeyInPEMFormat);

		$this->assertSignatureUsesRSA();
		$this->assertSignaturesMatch();
	}

	private function assertSignatureUsesRSA()
	{
		$details = openssl_pkey_get_details($this->publicKey);
		if (
			!isset($details['key']) ||
			!isset($details['type']) ||
			$details['type'] !== OPENSSL_KEYTYPE_RSA
		) {
			throw new InvalidArgumentException('The key specified is not an RSA public key');
		}
	}

	private function assertSignaturesMatch()
	{
		$result = openssl_verify($this->signingInput, $this->signature, $this->publicKey, OPENSSL_ALGO_SHA256);
		if ($result !== 1) {
			throw new InvalidArgumentException('Invalid JWT signature');
		}
	}
}
