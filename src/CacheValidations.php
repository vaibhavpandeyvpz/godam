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
 * Utility class for cache-related validations and normalizations.
 *
 * Provides static methods for validating cache keys and normalizing expiration times.
 */
final class CacheValidations
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct() {}

    /**
     * Validates that a cache key is a string and contains only path-safe characters.
     *
     * @param  mixed  $key  The key to validate
     *
     * @throws InvalidArgumentException If the key is not a string or contains invalid characters
     */
    public static function assertKey(mixed $key): void
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'Cache key must be string, "%s" given',
                is_object($key) ? $key::class : gettype($key)
            ));
        }

        if (preg_match('~[\(\)\{\}\[\]/\\\:;]~', $key)) {
            throw new InvalidArgumentException("Key '{$key}' must contain path-safe characters.");
        }
    }

    /**
     * Normalizes a DateTimeInterface to a Unix timestamp.
     *
     * @param  \DateTimeInterface|null  $expiration  The expiration date/time
     * @return int|null Unix timestamp, or null if expiration is null
     */
    public static function normalizeExpiry(?\DateTimeInterface $expiration): ?int
    {
        return $expiration?->getTimestamp();
    }

    /**
     * Normalizes an expiration interval to a Unix timestamp.
     *
     * @param  \DateInterval|int|null  $time  The expiration time as DateInterval, seconds (int), or null
     * @return int|null Unix timestamp when the item should expire, or null if no expiration
     */
    public static function normalizeExpiryInterval(\DateInterval|int|null $time): ?int
    {
        return match (true) {
            $time instanceof \DateInterval => (new \DateTime)->add($time)->getTimestamp(),
            is_int($time) => time() + $time,
            default => null,
        };
    }
}
