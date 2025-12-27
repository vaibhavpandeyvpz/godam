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

/**
 * Interface for cache storage backends.
 *
 * Defines the contract for storage implementations that can be used
 * with the Cache and CacheItemPool classes.
 */
interface StoreInterface
{
    /**
     * Clears all items from the store.
     *
     * @return bool True if the store was successfully cleared, false otherwise
     */
    public function clear(): bool;

    /**
     * Deletes an item from the store by its key.
     *
     * @param  string  $key  The key of the item to delete
     * @return bool True if the item was successfully deleted, false otherwise
     */
    public function delete(string $key): bool;

    /**
     * Retrieves an item from the store by its key.
     *
     * @param  string  $key  The key of the item to retrieve
     * @return mixed The stored value, or null if the key does not exist
     */
    public function get(string $key): mixed;

    /**
     * Checks if an item exists in the store.
     *
     * @param  string  $key  The key to check
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool;

    /**
     * Stores an item in the store.
     *
     * @param  string  $key  The key under which to store the value
     * @param  mixed  $value  The value to store (must be serializable)
     * @return bool True if the item was successfully stored, false otherwise
     */
    public function set(string $key, mixed $value): bool;
}
