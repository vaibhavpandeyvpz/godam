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
 * Redis cache store implementation using the Redis PHP extension.
 *
 * Stores cache items in a Redis database. Requires the redis PHP extension.
 *
 * @requires extension redis
 *
 * @implements StoreInterface
 */
final class RedisStore implements StoreInterface
{
    /**
     * @param  \Redis  $redis  A connected Redis instance
     */
    public function __construct(
        private readonly \Redis $redis
    ) {}

    /**
     * Clears all items from the current Redis database.
     *
     * @return bool True if the database was successfully flushed, false otherwise
     */
    public function clear(): bool
    {
        return $this->redis->flushdb();
    }

    /**
     * Deletes an item from Redis.
     *
     * @param  string  $key  The key of the item to delete
     * @return bool True if the item was successfully deleted, false otherwise
     */
    public function delete(string $key): bool
    {
        return $this->redis->del($key) === 1;
    }

    /**
     * Retrieves an item from Redis.
     *
     * @param  string  $key  The key of the item to retrieve
     * @return mixed The unserialized value, or null if the key doesn't exist
     */
    public function get(string $key): mixed
    {
        $data = $this->redis->get($key);

        return $data !== false ? unserialize($data) : null;
    }

    /**
     * Checks if an item exists in Redis.
     *
     * @param  string  $key  The key to check
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return $this->redis->exists($key) === 1;
    }

    /**
     * Stores an item in Redis.
     *
     * @param  string  $key  The key under which to store the value
     * @param  mixed  $value  The value to store (will be serialized)
     * @return bool True if the item was successfully stored, false otherwise
     */
    public function set(string $key, mixed $value): bool
    {
        return $this->redis->set($key, serialize($value));
    }
}
