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
use Predis\Client;

/**
 * Class PredisStore
 * @package Godam\Store
 */
class PredisStore implements StoreInterface
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * PredisStore constructor.
     * @param Client $redis
     */
    public function __construct(Client $redis)
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
        return $this->redis->del(array($key)) === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = $this->redis->get($key);
        if (is_string($data)) {
            return unserialize($data);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->redis->exists($key) === 1;
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
