<?php

/*
 * This file is part of vaibhavpandeyvpz/godam package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Godam;

/**
 * Class CacheValidations
 * @package Godam
 */
class CacheValidations
{
    /**
     * CacheValidations constructor.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     */
    public static function assertKey($key)
    {
        if (is_string($key)) {
            if (preg_match('~[\(\)\{\}\[\]/\\\:;]~', $key)) {
                throw new InvalidArgumentException("Key '{$key}' contains invalid characters");
            }
        } else {
            throw new InvalidArgumentException(sprintf(
                'Cache key must be string, "%s" given',
                is_object($key) ? get_class($key) : gettype($key)
            ));
        }
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return int|null
     */
    public static function normalizeExpiry($expiration)
    {
        if (is_null($expiration)) {
            return $expiration;
        }
        return (int)$expiration->format('U');
    }

    /**
     * @param \DateInterval|int|null $time
     * @return int|null
     */
    public static function normalizeExpiryInterval($time)
    {
        if ($time instanceof \DateInterval) {
            $time = \DateTime::createFromFormat('U', time())
                ->add($time)
                ->format('U');
            return (int)$time;
        } elseif (is_int($time)) {
            return $time + time();
        }
    }
}
