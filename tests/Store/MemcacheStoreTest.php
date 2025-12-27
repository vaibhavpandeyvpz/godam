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

namespace Godam\Store;

/**
 * @requires extension memcache
 */
final class MemcacheStoreTest extends StoreTestAbstract
{
    protected function setUp(): void
    {
        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $this->store = new MemcacheStore($memcache);
    }
}
