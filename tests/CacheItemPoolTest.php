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
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheItemPoolTest
 * @package Godam
 */
class CacheItemPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $pool;

    protected function setUp()
    {
        $this->pool = new CacheItemPool(new MemoryStore());
    }

    protected function tearDown()
    {
        $this->pool->clear();
    }

    public function testPool()
    {
        $this->assertFalse($this->pool->hasItem($key = 'somekey'));
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $this->pool->save($item);
        $this->assertTrue($this->pool->hasItem($key));
        $item = $this->pool->getItem($key);
        $this->assertEquals($value, $item->get());
        $this->assertTrue($item->isHit());
        $this->pool->deleteItem($key);
        $this->assertFalse($this->pool->hasItem($key));
    }

    public function testPoolWithExpiry()
    {
        $item = $this->pool->getItem($key = 'somekey');
        $item->set($value = 'something');
        $this->pool->save($item);
        $item = $this->pool->getItem($key);
        $this->assertEquals($value, $item->get());
        $this->pool->deleteItem($key);
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $item->expiresAfter(new \DateInterval('P30M'));
        $this->pool->save($item);
        $item = $this->pool->getItem($key);
        $this->assertEquals($value, $item->get());
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $item->expiresAfter(3600);
        $this->pool->save($item);
        $item = $this->pool->getItem($key);
        $this->assertEquals($value, $item->get());
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $item->expiresAfter(1);
        sleep(2);
        $this->pool->save($item);
        $item = $this->pool->getItem($key);
        $this->assertNull($item->get());
    }

    public function testPoolMultiple()
    {
        $item = $this->pool->getItem($key1 = 'somekey');
        $item->set($value1 = 'somevalue');
        $this->pool->save($item);
        $item = $this->pool->getItem($key2 = 'otherkey');
        $item->set($value2 = 'othervalue');
        $this->pool->save($item);
        /** @var CacheItemInterface[] $values */
        $values = $this->pool->getItems(array($key1, $key2));
        $this->assertEquals($value1, $values[$key1]->get());
        $this->assertEquals($value2, $values[$key2]->get());
        $this->pool->deleteItems(array($key1, $key2));
        $this->assertFalse($this->pool->hasItem($key1));
        $this->assertFalse($this->pool->hasItem($key2));
    }

    public function testSaveDeferred()
    {
        $this->assertFalse($this->pool->hasItem($key = 'somekey'));
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $this->pool->saveDeferred($item);
        $this->assertFalse($this->pool->hasItem($key));
        $this->pool->commit();
        $this->assertTrue($this->pool->hasItem($key));
    }

    public function testSaveInvalidItem()
    {
        $obj = $this->getMockBuilder('Psr\\Cache\\CacheItemInterface')->getMock();
        $this->setExpectedException('Psr\\Cache\\InvalidArgumentException');
        $this->pool->save($obj);
    }
}
