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

/**
 * Class RedisStoreTest
 * @package Godam\Store
 * @requires extension redis
 */
class RedisStoreTest extends StoreTestAbstract
{
    protected function setUp()
    {
        $redis = new \Redis();
        $redis->connect('localhost', 6379);
        $this->store = new RedisStore($redis);
    }
}
