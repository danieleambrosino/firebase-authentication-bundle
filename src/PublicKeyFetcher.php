<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyFetcherInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PublicKeyFetcher implements PublicKeyFetcherInterface
{
	const GOOGLE_PUBLIC_KEYS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

	public function __construct(
		private CacheItemPoolInterface $cache,
		private HttpClientInterface $httpClient
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
		$cacheItemKey = md5(__CLASS__);
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
		$response = $this->httpClient->request('GET', self::GOOGLE_PUBLIC_KEYS_URL);

		$serializedKeys = $response->getContent();
		$maxAge = $this->getMaxAge($response);

		$keys = json_decode($serializedKeys, true);

		$keyCollection = new PublicKeyCollection($keys);

		$cacheItemKey = md5(__CLASS__);
		$cacheItem = $this->cache->getItem($cacheItemKey);
		$cacheItem->set(serialize($keyCollection));
		$cacheItem->expiresAfter($maxAge);
		$this->cache->save($cacheItem);

		return $keyCollection;
	}

	private function getMaxAge(ResponseInterface $response): int
	{
		$cacheControlHeader = ($response->getHeaders())['cache-control'][0];
		preg_match('/max-age=(\d+)/', $cacheControlHeader, $matches);
		
		$maxAge = 0;
		if (count($matches) >= 2) {
			$maxAge = $matches[1];
		}

		return (int) $maxAge;
	}
}
