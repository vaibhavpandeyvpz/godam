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
use Psr\SimpleCache\CacheInterface;

final class CacheTest extends TestCase
{
    private CacheInterface $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache(new MemoryStore);
    }

    protected function tearDown(): void
    {
        $this->cache->clear();
    }

    public function test_cache(): void
    {
        $this->assertFalse($this->cache->has($key = 'somekey'));
        $this->assertNull($this->cache->get($key));
        $this->cache->set($key, $value = 'somevalue');
        $this->assertTrue($this->cache->has($key));
        $this->assertEquals($value, $this->cache->get($key));
        $this->cache->delete($key);
        $this->assertFalse($this->cache->has($key));
    }

    public function test_cache_with_expiry(): void
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

    public function test_cache_multiple(): void
    {
        $this->cache->set($key1 = 'somekey', $value1 = 'somevalue');
        $this->cache->set($key2 = 'otherkey', $value2 = 'othervalue');
        $values = $this->cache->getMultiple([$key1, $key2]);
        $this->assertEquals($value1, $values[$key1]);
        $this->assertEquals($value2, $values[$key2]);
        $this->cache->deleteMultiple([$key1, $key2]);
        $this->assertFalse($this->cache->has($key1));
        $this->assertFalse($this->cache->has($key2));
        $this->cache->setMultiple([
            $key1 => $value1,
            $key2 => $value2,
        ]);
        $this->assertTrue($this->cache->has($key1));
        $this->assertTrue($this->cache->has($key2));
        $this->assertEquals($value1, $this->cache->get($key1));
        $this->assertEquals($value2, $this->cache->get($key2));
    }

    public function test_clear(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');
        $this->assertTrue($this->cache->has('key1'));
        $this->assertTrue($this->cache->has('key2'));
        $this->assertTrue($this->cache->clear());
        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }

    public function test_get_with_default(): void
    {
        $this->assertNull($this->cache->get('nonexistent'));
        $this->assertEquals('default', $this->cache->get('nonexistent', 'default'));
        $this->assertEquals(123, $this->cache->get('nonexistent', 123));
        $this->assertEquals([], $this->cache->get('nonexistent', []));
    }

    public function test_get_multiple_with_empty_array(): void
    {
        $result = $this->cache->getMultiple([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_multiple_with_non_existent_keys(): void
    {
        $result = $this->cache->getMultiple(['key1', 'key2'], 'default');
        $this->assertEquals(['key1' => 'default', 'key2' => 'default'], $result);
    }

    public function test_get_multiple_with_mixed_keys(): void
    {
        $this->cache->set('key1', 'value1');
        $result = $this->cache->getMultiple(['key1', 'nonexistent'], 'default');
        $this->assertEquals(['key1' => 'value1', 'nonexistent' => 'default'], $result);
    }

    public function test_delete_multiple_with_empty_array(): void
    {
        $this->assertTrue($this->cache->deleteMultiple([]));
    }

    public function test_delete_multiple_with_non_existent_keys(): void
    {
        $this->assertTrue($this->cache->deleteMultiple(['nonexistent1', 'nonexistent2']));
    }

    public function test_set_with_date_interval(): void
    {
        $interval = new \DateInterval('PT1H');
        $this->assertTrue($this->cache->set('key', 'value', $interval));
        $this->assertEquals('value', $this->cache->get('key'));
    }

    public function test_set_with_null_ttl(): void
    {
        $this->assertTrue($this->cache->set('key', 'value', null));
        $this->assertEquals('value', $this->cache->get('key'));
    }

    public function test_set_multiple_with_date_interval(): void
    {
        $interval = new \DateInterval('PT1H');
        $this->assertTrue($this->cache->setMultiple([
            'key1' => 'value1',
            'key2' => 'value2',
        ], $interval));
        $this->assertEquals('value1', $this->cache->get('key1'));
        $this->assertEquals('value2', $this->cache->get('key2'));
    }

    public function test_set_multiple_with_invalid_keys(): void
    {
        $this->assertFalse($this->cache->setMultiple([
            'valid' => 'value',
            123 => 'invalid',
        ]));
    }

    public function test_set_multiple_with_empty_array(): void
    {
        $this->assertTrue($this->cache->setMultiple([]));
    }

    public function test_get_with_expired_item(): void
    {
        $this->cache->set('key', 'value', 1);
        $this->assertEquals('value', $this->cache->get('key'));
        sleep(2);
        $this->assertNull($this->cache->get('key'));
        $this->assertFalse($this->cache->has('key'));
    }

    public function test_delete_non_existent_key(): void
    {
        $this->assertTrue($this->cache->delete('nonexistent'));
    }

    public function test_get_with_invalid_data_structure(): void
    {
        // This tests the case where store returns invalid data
        $store = new \Godam\Store\MemoryStore;
        $store->set('key', 'invalid_data');
        $cache = new Cache($store);
        $this->assertNull($cache->get('key'));
    }

    public function test_has_with_invalid_key(): void
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->has('invalid/key');
    }

    public function test_get_with_invalid_key(): void
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->get('invalid/key');
    }

    public function test_set_with_invalid_key(): void
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set('invalid/key', 'value');
    }

    public function test_delete_with_invalid_key(): void
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->delete('invalid/key');
    }

    public function test_set_with_different_value_types(): void
    {
        $this->cache->set('string', 'value');
        $this->cache->set('int', 123);
        $this->cache->set('float', 123.45);
        $this->cache->set('bool', true);
        $this->cache->set('array', ['a' => 'b']);
        $this->cache->set('null', null);

        $this->assertEquals('value', $this->cache->get('string'));
        $this->assertEquals(123, $this->cache->get('int'));
        $this->assertEquals(123.45, $this->cache->get('float'));
        $this->assertTrue($this->cache->get('bool'));
        $this->assertEquals(['a' => 'b'], $this->cache->get('array'));
        $this->assertNull($this->cache->get('null'));
    }
}
