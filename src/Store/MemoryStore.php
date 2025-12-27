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
 * In-memory cache store implementation.
 *
 * Stores cache items in a PHP array. Data is lost when the process ends.
 * Useful for testing or single-request caching scenarios.
 *
 * @implements StoreInterface
 */
final class MemoryStore implements StoreInterface
{
    /** @var array<string, mixed> Cache storage array */
    private array $values = [];

    /**
     * Clears all items from the memory store.
     *
     * @return bool Always returns true
     */
    public function clear(): bool
    {
        $this->values = [];

        return true;
    }

    /**
     * Deletes an item from the memory store.
     *
     * @param  string  $key  The key of the item to delete
     * @return bool Always returns true
     */
    public function delete(string $key): bool
    {
        unset($this->values[$key]);

        return true;
    }

    /**
     * Retrieves an item from the memory store.
     *
     * @param  string  $key  The key of the item to retrieve
     * @return mixed The stored value, or null if the key does not exist
     */
    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Checks if an item exists in the memory store.
     *
     * @param  string  $key  The key to check
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Stores an item in the memory store.
     *
     * @param  string  $key  The key under which to store the value
     * @param  mixed  $value  The value to store
     * @return bool Always returns true
     */
    public function set(string $key, mixed $value): bool
    {
        $this->values[$key] = $value;

        return true;
    }
}
