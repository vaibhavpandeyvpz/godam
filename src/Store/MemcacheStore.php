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

namespace Godam\Store;

use Godam\StoreInterface;

/**
 * Memcache cache store implementation using the Memcache PHP extension.
 *
 * Stores cache items in a Memcached server. Requires the memcache PHP extension.
 *
 * @requires extension memcache
 *
 * @implements StoreInterface
 */
final class MemcacheStore implements StoreInterface
{
    /**
     * @param  \Memcache  $memcache  A connected Memcache instance
     */
    public function __construct(
        private readonly \Memcache $memcache
    ) {}

    /**
     * Clears all items from the Memcache server.
     *
     * @return bool True if the server was successfully flushed, false otherwise
     */
    public function clear(): bool
    {
        return $this->memcache->flush();
    }

    /**
     * Deletes an item from Memcache.
     *
     * @param  string  $key  The key of the item to delete
     * @return bool True if the item was successfully deleted, false otherwise
     */
    public function delete(string $key): bool
    {
        return $this->memcache->delete($key);
    }

    /**
     * Retrieves an item from Memcache.
     *
     * @param  string  $key  The key of the item to retrieve
     * @return mixed The unserialized value, or null if the key doesn't exist
     */
    public function get(string $key): mixed
    {
        $data = $this->memcache->get($key);

        return $data !== false ? unserialize($data) : null;
    }

    /**
     * Checks if an item exists in Memcache.
     *
     * @param  string  $key  The key to check
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return $this->memcache->get($key) !== false;
    }

    /**
     * Stores an item in Memcache.
     *
     * @param  string  $key  The key under which to store the value
     * @param  mixed  $value  The value to store (will be serialized)
     * @return bool True if the item was successfully stored, false otherwise
     */
    public function set(string $key, mixed $value): bool
    {
        return $this->memcache->set($key, serialize($value));
    }
}
