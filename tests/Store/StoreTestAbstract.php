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

use Godam\StoreInterface;
use PHPUnit\Framework\TestCase;

abstract class StoreTestAbstract extends TestCase
{
    protected StoreInterface $store;

    protected function tearDown(): void
    {
        $this->store->clear();
    }

    public function test_store(): void
    {
        $this->assertFalse($this->store->has($key = 'somekey'));
        $this->assertNull($this->store->get($key));
        $this->store->set($key, $value = 'somevalue');
        $this->assertTrue($this->store->has($key));
        $this->assertEquals($value, $this->store->get($key));
        $this->store->delete($key);
        $this->assertFalse($this->store->has($key));
    }

    public function test_clear(): void
    {
        $this->store->set('key1', 'value1');
        $this->store->set('key2', 'value2');
        $this->assertTrue($this->store->has('key1'));
        $this->assertTrue($this->store->has('key2'));
        $this->assertTrue($this->store->clear());
        $this->assertFalse($this->store->has('key1'));
        $this->assertFalse($this->store->has('key2'));
    }

    public function test_delete_non_existent_key(): void
    {
        $this->assertFalse($this->store->has('nonexistent'));
        $result = $this->store->delete('nonexistent');
        // Delete should return false for non-existent keys (or true if it's idempotent)
        $this->assertIsBool($result);
    }

    public function test_set_and_get_with_different_value_types(): void
    {
        $this->store->set('string', 'value');
        $this->store->set('int', 123);
        $this->store->set('float', 123.45);
        $this->store->set('bool', true);
        $this->store->set('array', ['a' => 'b']);
        $this->store->set('null', null);

        $this->assertEquals('value', $this->store->get('string'));
        $this->assertEquals(123, $this->store->get('int'));
        $this->assertEquals(123.45, $this->store->get('float'));
        $this->assertTrue($this->store->get('bool'));
        $this->assertEquals(['a' => 'b'], $this->store->get('array'));
        $this->assertNull($this->store->get('null'));
    }

    public function test_set_overwrites_existing_value(): void
    {
        $this->store->set('key', 'value1');
        $this->assertEquals('value1', $this->store->get('key'));
        $this->store->set('key', 'value2');
        $this->assertEquals('value2', $this->store->get('key'));
    }

    public function test_get_returns_null_for_non_existent_key(): void
    {
        $this->assertNull($this->store->get('nonexistent'));
    }

    public function test_has_returns_false_for_non_existent_key(): void
    {
        $this->assertFalse($this->store->has('nonexistent'));
    }

    public function test_clear_on_empty_store(): void
    {
        $this->assertTrue($this->store->clear());
        $this->assertFalse($this->store->has('anykey'));
    }
}
