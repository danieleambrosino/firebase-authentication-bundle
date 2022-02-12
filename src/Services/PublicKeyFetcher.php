<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Services;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Collections\PublicKeyCollection;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyFetcherInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PublicKeyFetcher implements PublicKeyFetcherInterface
{
	const GOOGLE_ID_TOKEN_PUBLIC_KEYS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
	const GOOGLE_SESSION_COOKIE_PUBLIC_KEYS_URL = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys';

	public function __construct(
		private CacheItemPoolInterface $cache,
		private HttpClientInterface $httpClient,
		private string $strategy
	) {
	}

	public function getKeys(): PublicKeyCollectionInterface
	{
		$keys = $this->getKeysFromCache();
		if ($keys !== null) {
			return $keys;
		}

		return $this->fetchKeys();
	}

	private function getKeysFromCache(): ?PublicKeyCollectionInterface
	{
		$cacheItemKey = self::getCacheKey();
		if ($this->cache->hasItem($cacheItemKey) === false) {
			return null;
		}
		$cacheItem = $this->cache->getItem($cacheItemKey);

		$serializedKeys = $cacheItem->get();
		$keys = unserialize($serializedKeys);
		return $keys;
	}

	private function fetchKeys(): ?PublicKeyCollectionInterface
	{
		$url = $this->getPublicKeysUrl();
		$response = $this->httpClient->request('GET', $url);

		$serializedKeys = $response->getContent();
		$maxAge = self::getMaxAge($response);

		$keys = json_decode($serializedKeys, true);

		$keyCollection = new PublicKeyCollection($keys);

		$cacheItemKey = self::getCacheKey();
		$cacheItem = $this->cache->getItem($cacheItemKey);
		$cacheItem->set(serialize($keyCollection));
		$cacheItem->expiresAfter($maxAge);
		$this->cache->save($cacheItem);

		return $keyCollection;
	}

	private function getPublicKeysUrl(): string
	{
		if ($this->strategy === 'bearer') {
			return self::GOOGLE_ID_TOKEN_PUBLIC_KEYS_URL;
		}
		if ($this->strategy === 'cookie') {
			return self::GOOGLE_SESSION_COOKIE_PUBLIC_KEYS_URL;
		}
		throw new InvalidConfigurationException('Public key strategy should correspond to the header extraction method (e.g. "bearer" or "cookie"), given ' . $this->strategy);
	}

	private static function getMaxAge(ResponseInterface $response): int
	{
		$cacheControlHeader = ($response->getHeaders())['cache-control'][0];
		preg_match('/max-age=(\d+)/', $cacheControlHeader, $matches);

		$maxAge = 0;
		if (count($matches) >= 2) {
			$maxAge = $matches[1];
		}

		return (int) $maxAge;
	}

	private static function getCacheKey(): string
	{
		return md5(__CLASS__);
	}
}
