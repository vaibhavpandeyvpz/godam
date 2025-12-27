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

final class CacheValidationsTest extends TestCase
{
    public function test_non_string_key(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(['key' => 'something']);
    }

    public function test_invalid_key(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('MyNS\\Key');
    }

    public function test_invalid_key_with_parentheses(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key(with)parentheses');
    }

    public function test_invalid_key_with_braces(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key{with}braces');
    }

    public function test_invalid_key_with_brackets(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key[with]brackets');
    }

    public function test_invalid_key_with_slash(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key/with/slash');
    }

    public function test_invalid_key_with_backslash(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key\\with\\backslash');
    }

    public function test_invalid_key_with_colon(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key:with:colon');
    }

    public function test_invalid_key_with_semicolon(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey('key;with;semicolon');
    }

    public function test_valid_key(): void
    {
        CacheValidations::assertKey('valid_key');
        CacheValidations::assertKey('valid-key');
        CacheValidations::assertKey('valid.key');
        CacheValidations::assertKey('valid123');
        CacheValidations::assertKey('123valid');
        CacheValidations::assertKey('ValidKey');
        CacheValidations::assertKey('valid_key_123');
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function test_assert_key_with_integer(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(123);
    }

    public function test_assert_key_with_float(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(123.45);
    }

    public function test_assert_key_with_boolean(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(true);
    }

    public function test_assert_key_with_null(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(null);
    }

    public function test_assert_key_with_array(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(['key' => 'value']);
    }

    public function test_assert_key_with_object(): void
    {
        $this->expectException(\Psr\Cache\InvalidArgumentException::class);
        CacheValidations::assertKey(new \stdClass);
    }

    public function test_normalize_expiry_with_null(): void
    {
        $result = CacheValidations::normalizeExpiry(null);
        $this->assertNull($result);
    }

    public function test_normalize_expiry_with_date_time(): void
    {
        $dt = new \DateTime('+1 hour');
        $result = CacheValidations::normalizeExpiry($dt);
        $this->assertEquals($dt->getTimestamp(), $result);
    }

    public function test_normalize_expiry_with_date_time_immutable(): void
    {
        $dt = new \DateTimeImmutable('+1 hour');
        $result = CacheValidations::normalizeExpiry($dt);
        $this->assertEquals($dt->getTimestamp(), $result);
    }

    public function test_normalize_expiry_interval_with_null(): void
    {
        $result = CacheValidations::normalizeExpiryInterval(null);
        $this->assertNull($result);
    }

    public function test_normalize_expiry_interval_with_integer(): void
    {
        $result = CacheValidations::normalizeExpiryInterval(3600);
        $expected = time() + 3600;
        $this->assertGreaterThanOrEqual($expected - 1, $result);
        $this->assertLessThanOrEqual($expected + 1, $result);
    }

    public function test_normalize_expiry_interval_with_zero(): void
    {
        $result = CacheValidations::normalizeExpiryInterval(0);
        $expected = time();
        $this->assertGreaterThanOrEqual($expected - 1, $result);
        $this->assertLessThanOrEqual($expected + 1, $result);
    }

    public function test_normalize_expiry_interval_with_negative_integer(): void
    {
        $result = CacheValidations::normalizeExpiryInterval(-3600);
        $expected = time() - 3600;
        $this->assertLessThan(time(), $result);
    }

    public function test_normalize_expiry_interval_with_date_interval(): void
    {
        $interval = new \DateInterval('PT1H');
        $result = CacheValidations::normalizeExpiryInterval($interval);
        $expected = (new \DateTime)->add($interval)->getTimestamp();
        $this->assertGreaterThanOrEqual($expected - 1, $result);
        $this->assertLessThanOrEqual($expected + 1, $result);
    }

    public function test_normalize_expiry_interval_with_various_date_interval_formats(): void
    {
        $intervals = [
            new \DateInterval('P1D'), // 1 day
            new \DateInterval('PT1H'), // 1 hour
            new \DateInterval('PT30M'), // 30 minutes
            new \DateInterval('PT1S'), // 1 second
        ];

        foreach ($intervals as $interval) {
            $result = CacheValidations::normalizeExpiryInterval($interval);
            $this->assertIsInt($result);
            $this->assertGreaterThan(time(), $result);
        }
    }
}
