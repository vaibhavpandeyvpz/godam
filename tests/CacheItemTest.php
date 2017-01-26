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
 * Class CacheItemTest
 * @package Godam
 */
class CacheItemTest extends \PHPUnit_Framework_TestCase
{
    public function testKey()
    {
        $item = new CacheItem($key = 'somekey');
        $this->assertEquals($key, $item->getKey());
    }

    public function testExpiresAfterInterval()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $di = new \DateInterval('P30M');
        $item->expiresAfter($di);
        $this->assertNotNull($item->getExpiry());
        $this->assertGreaterThan(time(), $item->getExpiry());
    }

    public function testExpiresAfterSeconds()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAfter(3600);
        $this->assertNotNull($item->getExpiry());
        $this->assertGreaterThan(time(), $item->getExpiry());
    }

    public function testExpiresAfterNull()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAfter(null);
        $this->assertNull($item->getExpiry());
    }

    public function testExpiresAt()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAt($dt = date_create());
        $this->assertNotNull($item->getExpiry());
        $this->assertEquals($dt->format('U'), $item->getExpiry());
    }

    public function testExpiresAtNull()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAt(null);
        $this->assertNull($item->getExpiry());
    }

    public function testHit()
    {
        $item = new CacheItem($key = 'somekey');
        $this->assertFalse($item->isHit());
        $item = new CacheItem($key, true);
        $this->assertTrue($item->isHit());
    }

    public function testSet()
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->get());
        $item->set($value = 'somevalue');
        $this->assertEquals($value, $item->get());
    }

    public function testValue()
    {
        $item = new CacheItem('somekey');
        $item->set($value = 'somevalue');
        $this->assertEquals($value, $item->get());
    }
}
