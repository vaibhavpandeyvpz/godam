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

use Godam\Store\MemoryStore;
use Psr\SimpleCache\CacheInterface;

/**
 * Class CacheTest
 * @package Godam
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    protected function setUp()
    {
        $this->cache = new Cache(new MemoryStore());
    }

    protected function tearDown()
    {
        $this->cache->clear();
    }

    public function testCache()
    {
        $this->assertFalse($this->cache->has($key = 'somekey'));
        $this->assertNull($this->cache->get($key));
        $this->cache->set($key, $value = 'somevalue');
        $this->assertTrue($this->cache->has($key));
        $this->assertEquals($value, $this->cache->get($key));
        $this->cache->delete($key);
        $this->assertFalse($this->cache->has($key));
    }

    public function testCacheWithExpiry()
    {
        $this->cache->set($key = 'somekey', $value = 'somevalue');
        $this->assertEquals($value, $this->cache->get($key));
        $this->cache->delete($key);
        $this->cache->set($key, $value, 3600);
        $this->assertEquals($value, $this->cache->get($key));
        $this->cache->delete($key);
        $this->cache->set($key, $value, 1);
        sleep(2);
        $this->assertNull($this->cache->get($key));
    }

    public function testCacheMultiple()
    {
        $this->cache->set($key1 = 'somekey', $value1 = 'somevalue');
        $this->cache->set($key2 = 'otherkey', $value2 = 'othervalue');
        $values = $this->cache->getMultiple(array($key1, $key2));
        $this->assertEquals($value1, $values[$key1]);
        $this->assertEquals($value2, $values[$key2]);
        $this->cache->deleteMultiple(array($key1, $key2));
        $this->assertFalse($this->cache->has($key1));
        $this->assertFalse($this->cache->has($key2));
        $this->cache->setMultiple(array(
            $key1 => $value1,
            $key2 => $value2,
        ));
        $this->assertTrue($this->cache->has($key1));
        $this->assertTrue($this->cache->has($key2));
        $this->assertEquals($value1, $this->cache->get($key1));
        $this->assertEquals($value2, $this->cache->get($key2));
    }
}
