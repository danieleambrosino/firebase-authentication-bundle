<?php

namespace DanieleAmbrosino\FirebaseAuthenticationBundle\Collections;

use DanieleAmbrosino\FirebaseAuthenticationBundle\Contracts\PublicKeyCollectionInterface;
use InvalidArgumentException;

class PublicKeyCollection implements PublicKeyCollectionInterface
{

	/**
	 * @var array<string,string>
	 */
	private array $collection = [];

	/**
	 * @param array $data The public keys indexed by ID.
	 */
	public function __construct(array $data)
	{
		foreach ($data as $key => $value) {
			if (!is_string($key) || !is_string($value)) {
				throw new InvalidArgumentException('Either keys and values in a PublicKeyCollection must be strings.');
			}
			$this->collection[$key] = $value;
		}
	}

	public function get(string $id): string
	{
		if (!isset($this->collection[$id])) {
			throw new InvalidArgumentException("Unable to find the public key with ID $id");
		}
		return $this->collection[$id];
	}

	public function set(string $id, string $value): PublicKeyCollectionInterface
	{
		$this->collection[$id] = $value;
		return $this;
	}

	public function __serialize(): array
	{
		return $this->collection;
	}

	public function __unserialize(array $data): void
	{
		$this->collection = $data;
	}
}
