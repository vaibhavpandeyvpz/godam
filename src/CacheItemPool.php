<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/godam package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Godam;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PSR-6 Cache Item Pool implementation.
 *
 * Manages a collection of cache items and provides methods to retrieve, save, and delete them.
 * Supports deferred saves that can be committed in a single operation.
 *
 * @implements CacheItemPoolInterface
 */
final class CacheItemPool implements CacheItemPoolInterface
{
    /** @var CacheItemInterface[] Items queued for deferred saving */
    private array $deferred = [];

    /**
     * @param  StoreInterface  $store  The storage backend to use for caching
     */
    public function __construct(
        private readonly StoreInterface $store
    ) {}

    /**
     * Deletes all items in the pool.
     *
     * @return bool True if the pool was successfully cleared, false otherwise
     */
    public function clear(): bool
    {
        return $this->store->clear();
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool True if all deferred items were successfully saved, false otherwise
     */
    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            if (! $this->save($item)) {
                $this->deferred = [];

                return false;
            }
        }
        $this->deferred = [];

        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param  string  $key  The key for which to delete the cache item
     * @return bool True if the item was successfully removed, false if there was an error
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function deleteItem(string $key): bool
    {
        CacheValidations::assertKey($key);

        return $this->store->delete($key);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param  array<string>  $keys  An array of keys that should be removed from the pool
     * @return bool True if the items were successfully removed, false if there was an error
     *
     * @throws InvalidArgumentException If any of the keys are not a legal value
     */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            if (! $this->deleteItem($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * @param  string  $key  The key for which to return the corresponding Cache Item
     * @return CacheItemInterface The corresponding Cache Item
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function getItem(string $key): CacheItemInterface
    {
        CacheValidations::assertKey($key);

        if (! $this->store->has($key)) {
            return new CacheItem($key);
        }

        $data = $this->store->get($key);
        if (! is_array($data) || ! array_key_exists('value', $data) || ! array_key_exists('expiry', $data)) {
            return new CacheItem($key);
        }

        $expiry = $data['expiry'];
        $value = $data['value'];

        if ($expiry !== null && $expiry <= time()) {
            $this->deleteItem($key);

            return new CacheItem($key);
        }

        return (new CacheItem($key, true))->set($value);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param  array<string>  $keys  An indexed array of keys of items to retrieve
     * @return iterable<string, CacheItemInterface> A traversable collection of Cache Items keyed by the cache keys of each item
     *
     * @throws InvalidArgumentException If any of the keys are not a legal value
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * @param  string  $key  The key for which to check existence
     * @return bool True if item exists in the cache and is not expired, false otherwise
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function hasItem(string $key): bool
    {
        CacheValidations::assertKey($key);

        return $this->store->has($key);
    }

    /**
     * Persists a cache item immediately.
     *
     * @param  CacheItemInterface  $item  The cache item to save
     * @return bool True if the item was successfully persisted, false if there was an error
     *
     * @throws InvalidArgumentException If the item is not an instance of CacheItem
     */
    public function save(CacheItemInterface $item): bool
    {
        if ($item instanceof CacheItem) {
            return $this->store->set($item->getKey(), [
                'value' => $item->get(),
                'expiry' => $item->getExpiry(),
            ]);
        }
        throw new InvalidArgumentException(sprintf(
            "Cannot only handle items of type 'Godam\\CacheItem'; '%s' given",
            $item::class
        ));
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param  CacheItemInterface  $item  The cache item to save
     * @return bool False if the item could not be queued or if a commit was attempted and failed, true otherwise
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;

        return true;
    }
}
