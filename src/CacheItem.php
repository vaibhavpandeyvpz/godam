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

use Psr\Cache\CacheItemInterface;

/**
 * Class CacheItem
 * @package Godam
 */
class CacheItem implements CacheItemInterface
{
    /**
     * @var int|null
     */
    protected $expiry;

    /**
     * @var bool
     */
    protected $hit = false;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * CacheItem constructor.
     * @param string $key
     * @param bool $hit
     */
    public function __construct($key, $hit = false)
    {
        $this->key = $key;
        $this->hit = $hit;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        $this->expiry = CacheValidations::normalizeExpiryInterval($time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        $this->expiry = CacheValidations::normalizeExpiry($expiration);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }
}
