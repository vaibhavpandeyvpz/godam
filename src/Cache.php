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

use Psr\SimpleCache\CacheInterface;

/**
 * Class Cache
 * @package Godam
 */
class Cache implements CacheInterface
{
    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * Cache constructor.
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->store->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        CacheValidations::assertKey($key);
        return $this->store->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $deleted = true;
        foreach ($keys as $key) {
            $deleted = $this->delete($key);
        }
        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        CacheValidations::assertKey($key);
        if ($this->has($key)) {
            $data = $this->store->get($key);
            extract($data);
            /** @var int $expiry */
            /** @var mixed $value */
            if (is_null($expiry) || ($expiry > time())) {
                return $value;
            }
            $this->delete($key);
        }
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $items = array();
        foreach ($keys as $key) {
            $items[$key] = $this->get($key, $default);
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        CacheValidations::assertKey($key);
        return $this->store->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        CacheValidations::assertKey($key);
        $expiry = is_int($ttl) ? (time() + $ttl) : null;
        return $this->store->set($key, compact('value', 'expiry'));
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $saved = true;
        foreach ($values as $key => $value) {
            $saved = $this->set($key, $value, $ttl);
        }
        return $saved;
    }
}
