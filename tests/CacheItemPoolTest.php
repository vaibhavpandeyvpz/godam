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

namespace Godam;

use Godam\Store\MemoryStore;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

final class CacheItemPoolTest extends TestCase
{
    private CacheItemPoolInterface $pool;

    protected function setUp(): void
    {
        $this->pool = new CacheItemPool(new MemoryStore);
    }

    protected function tearDown(): void
    {
        $this->pool->clear();
    }

    public function test_pool(): void
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

    public function test_pool_with_expiry(): void
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

    public function test_pool_multiple(): void
    {
        $item = $this->pool->getItem($key1 = 'somekey');
        $item->set($value1 = 'somevalue');
        $this->pool->save($item);
        $item = $this->pool->getItem($key2 = 'otherkey');
        $item->set($value2 = 'othervalue');
        $this->pool->save($item);
        /** @var CacheItemInterface[] $values */
        $values = $this->pool->getItems([$key1, $key2]);
        $this->assertEquals($value1, $values[$key1]->get());
        $this->assertEquals($value2, $values[$key2]->get());
        $this->pool->deleteItems([$key1, $key2]);
        $this->assertFalse($this->pool->hasItem($key1));
        $this->assertFalse($this->pool->hasItem($key2));
    }

    public function test_save_deferred(): void
    {
        $this->assertFalse($this->pool->hasItem($key = 'somekey'));
        $item = $this->pool->getItem($key);
        $item->set($value = 'something');
        $this->pool->saveDeferred($item);
        $this->assertFalse($this->pool->hasItem($key));
        $this->pool->commit();
        $this->assertTrue($this->pool->hasItem($key));
    }

    public function test_save_invalid_item(): void
    {
        $obj = $this->createMock(\Psr\Cache\CacheItemInterface::class);
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        $this->pool->save($obj);
    }

    public function test_clear(): void
    {
        $item = $this->pool->getItem('key1');
        $item->set('value1');
        $this->pool->save($item);
        $item = $this->pool->getItem('key2');
        $item->set('value2');
        $this->pool->save($item);
        $this->assertTrue($this->pool->hasItem('key1'));
        $this->assertTrue($this->pool->hasItem('key2'));
        $this->assertTrue($this->pool->clear());
        $this->assertFalse($this->pool->hasItem('key1'));
        $this->assertFalse($this->pool->hasItem('key2'));
    }

    public function test_commit_with_empty_deferred(): void
    {
        $this->assertTrue($this->pool->commit());
    }

    public function test_commit_with_multiple_deferred_items(): void
    {
        $item1 = $this->pool->getItem('key1');
        $item1->set('value1');
        $this->pool->saveDeferred($item1);

        $item2 = $this->pool->getItem('key2');
        $item2->set('value2');
        $this->pool->saveDeferred($item2);

        $this->assertFalse($this->pool->hasItem('key1'));
        $this->assertFalse($this->pool->hasItem('key2'));

        $this->assertTrue($this->pool->commit());

        $this->assertTrue($this->pool->hasItem('key1'));
        $this->assertTrue($this->pool->hasItem('key2'));
        $this->assertEquals('value1', $this->pool->getItem('key1')->get());
        $this->assertEquals('value2', $this->pool->getItem('key2')->get());
    }

    public function test_delete_items_with_empty_array(): void
    {
        $this->assertTrue($this->pool->deleteItems([]));
    }

    public function test_delete_items_with_non_existent_keys(): void
    {
        $this->assertTrue($this->pool->deleteItems(['nonexistent1', 'nonexistent2']));
    }

    public function test_delete_items_with_mixed_keys(): void
    {
        $item = $this->pool->getItem('key1');
        $item->set('value1');
        $this->pool->save($item);
        $this->assertTrue($this->pool->deleteItems(['key1', 'nonexistent']));
        $this->assertFalse($this->pool->hasItem('key1'));
    }

    public function test_get_items_with_empty_array(): void
    {
        $result = $this->pool->getItems([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_items_with_non_existent_keys(): void
    {
        $items = $this->pool->getItems(['key1', 'key2']);
        $this->assertCount(2, $items);
        $this->assertFalse($items['key1']->isHit());
        $this->assertFalse($items['key2']->isHit());
    }

    public function test_get_items_with_mixed_keys(): void
    {
        $item = $this->pool->getItem('key1');
        $item->set('value1');
        $this->pool->save($item);

        $items = $this->pool->getItems(['key1', 'nonexistent']);
        $this->assertTrue($items['key1']->isHit());
        $this->assertEquals('value1', $items['key1']->get());
        $this->assertFalse($items['nonexistent']->isHit());
    }

    public function test_get_item_with_expired_item(): void
    {
        $item = $this->pool->getItem('key');
        $item->set('value');
        $item->expiresAfter(1);
        $this->pool->save($item);
        $this->assertEquals('value', $this->pool->getItem('key')->get());
        sleep(2);
        $item = $this->pool->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
        $this->assertFalse($this->pool->hasItem('key'));
    }

    public function test_get_item_with_invalid_data_structure(): void
    {
        // This tests the case where store returns invalid data
        $store = new \Godam\Store\MemoryStore;
        $store->set('key', 'invalid_data');
        $pool = new CacheItemPool($store);
        $item = $pool->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function test_has_item_with_invalid_key(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        $this->pool->hasItem('invalid/key');
    }

    public function test_get_item_with_invalid_key(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        $this->pool->getItem('invalid/key');
    }

    public function test_delete_item_with_invalid_key(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        $this->pool->deleteItem('invalid/key');
    }

    public function test_save_deferred_multiple_times(): void
    {
        $item = $this->pool->getItem('key');
        $item->set('value');
        $this->assertTrue($this->pool->saveDeferred($item));
        $this->assertTrue($this->pool->saveDeferred($item));
        $this->assertTrue($this->pool->commit());
        $this->assertTrue($this->pool->hasItem('key'));
    }

    public function test_get_item_with_null_expiry(): void
    {
        $item = $this->pool->getItem('key');
        $item->set('value');
        $item->expiresAt(null);
        $this->pool->save($item);
        $retrieved = $this->pool->getItem('key');
        $this->assertTrue($retrieved->isHit());
        $this->assertEquals('value', $retrieved->get());
    }

    public function test_get_item_with_null_expiry_after(): void
    {
        $item = $this->pool->getItem('key');
        $item->set('value');
        $item->expiresAfter(null);
        $this->pool->save($item);
        $retrieved = $this->pool->getItem('key');
        $this->assertTrue($retrieved->isHit());
        $this->assertEquals('value', $retrieved->get());
    }

    public function test_get_item_with_date_interval_expiry(): void
    {
        $item = $this->pool->getItem('key');
        $item->set('value');
        $item->expiresAfter(new \DateInterval('PT1H'));
        $this->pool->save($item);
        $retrieved = $this->pool->getItem('key');
        $this->assertTrue($retrieved->isHit());
        $this->assertEquals('value', $retrieved->get());
    }
}
