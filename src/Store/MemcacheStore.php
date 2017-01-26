<?php

/*
 * This file is part of vaibhavpandeyvpz/godam package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Godam\Store;

use Godam\StoreInterface;

/**
 * Class MemcacheStore
 * @package Godam\Store
 * @requires extension memcache
 */
class MemcacheStore implements StoreInterface
{
    /**
     * @var \Memcache
     */
    protected $memcache;

    /**
     * MemcacheStore constructor.
     * @param \Memcache $memcache
     */
    public function __construct(\Memcache $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->memcache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->memcache->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = $this->memcache->get($key);
        if ($data !== false) {
            return unserialize($data);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return false !== $this->memcache->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return $this->memcache->set($key, serialize($value));
    }
}
