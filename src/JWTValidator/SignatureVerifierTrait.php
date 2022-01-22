<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\JWTValidator;

use InvalidArgumentException;

/**
 * @property array $header The header of the JWT.
 * @property PublicKeyCollectionInterface $publicKeyCandidates The collection of candidate public keys.
 */
trait SignatureVerifierTrait
{
	private function verifySignature()
	{
		$publicKeyInPEMFormat = $this->publicKeyCandidates->get($this->header['kid']);
		$this->publicKey = openssl_pkey_get_public($publicKeyInPEMFormat);

		$this->signatureUsesRSA();
		$this->signaturesMatch();
	}

	private function signatureUsesRSA()
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

	private function signaturesMatch()
	{
		$result = openssl_verify($this->signedData, $this->signature, $this->publicKey, OPENSSL_ALGO_SHA256);
		if ($result !== 1) {
			throw new InvalidArgumentException('Invalid public key');
		}
	}
}
