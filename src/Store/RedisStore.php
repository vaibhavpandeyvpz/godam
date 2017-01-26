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
 * Class RedisStore
 * @package Godam\Store
 * @requires extension redis
 */
class RedisStore implements StoreInterface
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * RedisStore constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->redis->flushdb();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->redis->del($key) === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = $this->redis->get($key);
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
        return $this->redis->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->redis->set($key, serialize($value));
        return true;
    }
}
