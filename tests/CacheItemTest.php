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

use PHPUnit\Framework\TestCase;

final class CacheItemTest extends TestCase
{
    public function test_key(): void
    {
        $item = new CacheItem($key = 'somekey');
        $this->assertEquals($key, $item->getKey());
    }

    public function test_expires_after_interval(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $di = new \DateInterval('P30M');
        $item->expiresAfter($di);
        $this->assertNotNull($item->getExpiry());
        $this->assertGreaterThan(time(), $item->getExpiry());
    }

    public function test_expires_after_seconds(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAfter(3600);
        $this->assertNotNull($item->getExpiry());
        $this->assertGreaterThan(time(), $item->getExpiry());
    }

    public function test_expires_after_null(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAfter(null);
        $this->assertNull($item->getExpiry());
    }

    public function test_expires_at(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAt($dt = date_create());
        $this->assertNotNull($item->getExpiry());
        $this->assertEquals($dt->format('U'), $item->getExpiry());
    }

    public function test_expires_at_null(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());
        $item->expiresAt(null);
        $this->assertNull($item->getExpiry());
    }

    public function test_hit(): void
    {
        $item = new CacheItem($key = 'somekey');
        $this->assertFalse($item->isHit());
        $item = new CacheItem($key, true);
        $this->assertTrue($item->isHit());
    }

    public function test_set(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->get());
        $item->set($value = 'somevalue');
        $this->assertEquals($value, $item->get());
    }

    public function test_value(): void
    {
        $item = new CacheItem('somekey');
        $item->set($value = 'somevalue');
        $this->assertEquals($value, $item->get());
    }

    public function test_method_chaining(): void
    {
        $item = new CacheItem('somekey');
        $result = $item->set('value')->expiresAfter(3600);
        $this->assertSame($item, $result);
        $this->assertEquals('value', $item->get());
        $this->assertNotNull($item->getExpiry());
    }

    public function test_expires_at_with_date_time_immutable(): void
    {
        $item = new CacheItem('somekey');
        $dt = new \DateTimeImmutable('+1 hour');
        $item->expiresAt($dt);
        $this->assertEquals($dt->getTimestamp(), $item->getExpiry());
    }

    public function test_expires_at_with_past_date(): void
    {
        $item = new CacheItem('somekey');
        $dt = new \DateTime('-1 hour');
        $item->expiresAt($dt);
        $this->assertLessThan(time(), $item->getExpiry());
    }

    public function test_set_with_different_value_types(): void
    {
        $item = new CacheItem('somekey');

        $item->set('string');
        $this->assertEquals('string', $item->get());

        $item->set(123);
        $this->assertEquals(123, $item->get());

        $item->set(123.45);
        $this->assertEquals(123.45, $item->get());

        $item->set(true);
        $this->assertTrue($item->get());

        $item->set(false);
        $this->assertFalse($item->get());

        $item->set(['a' => 'b']);
        $this->assertEquals(['a' => 'b'], $item->get());

        $item->set(null);
        $this->assertNull($item->get());

        $obj = new \stdClass;
        $obj->prop = 'value';
        $item->set($obj);
        $this->assertEquals($obj, $item->get());
    }

    public function test_get_returns_null_initially(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->get());
    }

    public function test_expires_after_with_zero(): void
    {
        $item = new CacheItem('somekey');
        $item->expiresAfter(0);
        $this->assertNotNull($item->getExpiry());
        $this->assertEquals(time(), $item->getExpiry());
    }

    public function test_expires_after_with_negative_value(): void
    {
        $item = new CacheItem('somekey');
        $item->expiresAfter(-3600);
        $this->assertNotNull($item->getExpiry());
        $this->assertLessThan(time(), $item->getExpiry());
    }

    public function test_expires_after_chaining(): void
    {
        $item = new CacheItem('somekey');
        $item->expiresAfter(3600)->set('value');
        $this->assertEquals('value', $item->get());
        $this->assertNotNull($item->getExpiry());
    }

    public function test_expires_at_chaining(): void
    {
        $item = new CacheItem('somekey');
        $dt = new \DateTime('+1 hour');
        $item->expiresAt($dt)->set('value');
        $this->assertEquals('value', $item->get());
        $this->assertEquals($dt->getTimestamp(), $item->getExpiry());
    }

    public function test_get_expiry_returns_correct_value(): void
    {
        $item = new CacheItem('somekey');
        $this->assertNull($item->getExpiry());

        $item->expiresAfter(3600);
        $expected = time() + 3600;
        $this->assertGreaterThanOrEqual($expected - 1, $item->getExpiry());
        $this->assertLessThanOrEqual($expected + 1, $item->getExpiry());
    }
}
