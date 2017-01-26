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
 * Class MemcacheStoreTest
 * @package Godam\Store
 */
class MemcacheStoreTest extends StoreTestAbstract
{
    protected function setUp()
    {
        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);
        $this->store = new MemcacheStore($memcache);
    }
}
