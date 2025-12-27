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

use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 Simple Cache implementation.
 *
 * Provides a simple caching interface that supports basic cache operations
 * including get, set, delete, and clear with TTL support.
 *
 * @implements CacheInterface
 */
final class Cache implements CacheInterface
{
    /**
     * @param  StoreInterface  $store  The storage backend to use for caching
     */
    public function __construct(
        private readonly StoreInterface $store
    ) {}

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True if the cache was successfully cleared, false otherwise
     */
    public function clear(): bool
    {
        return $this->store->clear();
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param  string  $key  The unique cache key of the item to delete
     * @return bool True if the item was successfully removed, false otherwise
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function delete(string $key): bool
    {
        CacheValidations::assertKey($key);

        return $this->store->delete($key);
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param  iterable<string>  $keys  A list of string-based keys to be deleted
     * @return bool True if all items were successfully removed, false if any deletion failed
     *
     * @throws InvalidArgumentException If any of the keys are not a legal value
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            if (! $this->delete($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param  string  $key  The unique key of this item in the cache
     * @param  mixed  $default  Default value to return if the key does not exist
     * @return mixed The value of the item from the cache, or $default in case of cache miss
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        CacheValidations::assertKey($key);

        if (! $this->has($key)) {
            return $default;
        }

        $data = $this->store->get($key);
        if (! is_array($data) || ! array_key_exists('value', $data) || ! array_key_exists('expiry', $data)) {
            return $default;
        }

        $expiry = $data['expiry'];
        $value = $data['value'];

        if ($expiry !== null && $expiry <= time()) {
            $this->delete($key);

            return $default;
        }

        return $value;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param  iterable<string>  $keys  A list of keys that can be obtained in a single operation
     * @param  mixed  $default  Default value to return for keys that do not exist
     * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value
     *
     * @throws InvalidArgumentException If any of the keys are not a legal value
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->get($key, $default);
        }

        return $items;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param  string  $key  The cache item key
     * @return bool True if the item exists and is not expired, false otherwise
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function has(string $key): bool
    {
        CacheValidations::assertKey($key);

        return $this->store->has($key);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param  string  $key  The key of the item to store
     * @param  mixed  $value  The value of the item to store, must be serializable
     * @param  int|\DateInterval|null  $ttl  Optional. The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that
     * @return bool True on success and false on failure
     *
     * @throws InvalidArgumentException If the key is not a legal value
     */
    public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
    {
        CacheValidations::assertKey($key);
        $expiry = CacheValidations::normalizeExpiryInterval($ttl);

        return $this->store->set($key, ['value' => $value, 'expiry' => $expiry]);
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param  iterable<string, mixed>  $values  A list of key => value pairs for a multiple-set operation
     * @param  int|\DateInterval|null  $ttl  Optional. The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that
     * @return bool True on success and false on failure
     *
     * @throws InvalidArgumentException If any of the keys are not a legal value
     */
    public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            if (! is_string($key) || ! $this->set($key, $value, $ttl)) {
                return false;
            }
        }

        return true;
    }
}
