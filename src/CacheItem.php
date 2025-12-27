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

/**
 * PSR-6 Cache Item implementation.
 *
 * Represents a single cache item with its key, value, and expiration information.
 *
 * @implements CacheItemInterface
 */
final class CacheItem implements CacheItemInterface
{
    /** @var int|null Unix timestamp when the item expires, or null if no expiration */
    private ?int $expiry = null;

    /** @var mixed The cached value */
    private mixed $value = null;

    /**
     * @param  string  $key  The key for this cache item
     * @param  bool  $hit  Whether this item represents a cache hit
     */
    public function __construct(
        private readonly string $key,
        private bool $hit = false
    ) {}

    /**
     * Retrieves the value of the item from the cache.
     *
     * @return mixed The value corresponding to this cache item's key, or null if not found
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * Gets the expiration timestamp for this cache item.
     *
     * @return int|null Unix timestamp when the item expires, or null if no expiration
     */
    public function getExpiry(): ?int
    {
        return $this->expiry;
    }

    /**
     * Returns the key for the current cache item.
     *
     * @return string The key string for this cache item
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param  int|\DateInterval|null  $time  The period of time from the present after which the item MUST be considered expired
     * @return static The called object
     */
    public function expiresAfter(int|\DateInterval|null $time): static
    {
        $this->expiry = CacheValidations::normalizeExpiryInterval($time);

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param  \DateTimeInterface|null  $expiration  The point in time after which the item MUST be considered expired
     * @return static The called object
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiry = CacheValidations::normalizeExpiry($expiration);

        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return bool True if the request resulted in a cache hit, false otherwise
     */
    public function isHit(): bool
    {
        return $this->hit;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * @param  mixed  $value  The serializable value to be stored
     * @return static The called object
     */
    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }
}
