# vaibhavpandeyvpz/godam

A modern, PSR-6 and PSR-16 compliant caching library for PHP 8.2+ with support for multiple storage backends.

> **Godam** (`गोदाम`) means "Warehouse" in Hindi

[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/godam.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/godam)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/godam.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/godam)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/godam.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/godam)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/godam.svg?style=flat-square)](LICENSE)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg?style=flat-square)](https://github.com/vaibhavpandeyvpz/godam)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vaibhavpandeyvpz/godam/tests.yml?branch=main&style=flat-square)](https://github.com/vaibhavpandeyvpz/godam/actions)

## Features

- ✅ **PSR-6** (Cache Item Pool Interface) compliant
- ✅ **PSR-16** (Simple Cache Interface) compliant
- ✅ Multiple storage backends: Memory, FileSystem, Redis, Memcache, Predis
- ✅ TTL (Time To Live) support with flexible expiration
- ✅ Type-safe with PHP 8.2+ features
- ✅ 100% test coverage
- ✅ Zero dependencies (except PSR interfaces)

## Installation

Install via Composer:

```bash
composer require vaibhavpandeyvpz/godam
```

### Optional Dependencies

For Redis support, you can use either:

- `ext-redis` (PECL extension)
- `predis/predis` (pure PHP client)

For Memcache support:

- `ext-memcache` (PECL extension)

## Storage Backends

Godam supports multiple storage backends through the `StoreInterface`:

### MemoryStore

In-memory cache that stores data in PHP arrays. Perfect for testing or single-request caching.

```php
use Godam\Store\MemoryStore;

$store = new MemoryStore();
```

### FileSystemStore

File-based cache that stores data on disk. Ideal for applications without external cache servers.

```php
use Godam\Store\FileSystemStore;

$store = new FileSystemStore('/path/to/cache/directory');
```

### RedisStore

Redis cache using the `ext-redis` extension.

```php
use Godam\Store\RedisStore;

$redis = new Redis();
$redis->connect('localhost', 6379);
$store = new RedisStore($redis);
```

### PredisStore

Redis cache using the `predis/predis` library (pure PHP, no extension required).

```php
use Godam\Store\PredisStore;
use Predis\Client;

$redis = new Client('tcp://127.0.0.1:6379');
$store = new PredisStore($redis);
```

### MemcacheStore

Memcache cache using the `ext-memcache` extension.

```php
use Godam\Store\MemcacheStore;

$memcache = new Memcache();
$memcache->connect('localhost', 11211);
$store = new MemcacheStore($memcache);
```

## Usage

### PSR-16 Simple Cache Interface

The `Cache` class provides a simple, straightforward caching interface:

```php
use Godam\Cache;
use Godam\Store\MemoryStore;

$cache = new Cache(new MemoryStore());

// Store a value with TTL (time to live in seconds)
$cache->set('user:123', ['name' => 'John', 'email' => 'john@example.com'], 3600);

// Retrieve a value
$user = $cache->get('user:123');

// Get with default value if key doesn't exist
$user = $cache->get('user:456', ['name' => 'Guest']);

// Check if a key exists
$exists = $cache->has('user:123');

// Delete a single key
$cache->delete('user:123');

// Delete multiple keys
$cache->deleteMultiple(['user:123', 'user:456']);

// Store multiple values at once
$cache->setMultiple([
    'key1' => 'value1',
    'key2' => 'value2',
], 3600);

// Get multiple values at once
$values = $cache->getMultiple(['key1', 'key2'], []);

// Clear all cache
$cache->clear();
```

### PSR-6 Cache Item Pool Interface

The `CacheItemPool` class provides a more advanced caching interface with cache items:

```php
use Godam\CacheItemPool;
use Godam\Store\FileSystemStore;

$pool = new CacheItemPool(new FileSystemStore(__DIR__ . '/cache'));

// Get a cache item
$item = $pool->getItem('user:123');

if ($item->isHit()) {
    // Cache hit - item exists and is not expired
    $user = $item->get();
} else {
    // Cache miss - set the value
    $item->set(['name' => 'John', 'email' => 'john@example.com']);

    // Set expiration time (in seconds)
    $item->expiresAfter(3600);

    // Or set expiration to a specific date/time
    // $item->expiresAt(new \DateTime('+1 hour'));

    // Save the item
    $pool->save($item);
}

// Get multiple items at once
$items = $pool->getItems(['user:123', 'user:456']);

// Save a deferred item (will be saved on commit)
$item = $pool->getItem('user:789');
$item->set(['name' => 'Jane']);
$item->expiresAfter(1800);
$pool->saveDeferred($item);

// Commit all deferred items
$pool->commit();

// Delete an item
$pool->deleteItem('user:123');

// Delete multiple items
$pool->deleteItems(['user:123', 'user:456']);

// Clear all items
$pool->clear();
```

### TTL (Time To Live) Examples

```php
use Godam\Cache;
use Godam\Store\MemoryStore;

$cache = new Cache(new MemoryStore());

// Cache for 1 hour (3600 seconds)
$cache->set('key1', 'value1', 3600);

// Cache forever (no expiration)
$cache->set('key2', 'value2', null);

// Cache for 30 minutes
$cache->set('key3', 'value3', 1800);

// Using DateInterval
$cache->set('key4', 'value4', new \DateInterval('PT1H')); // 1 hour
```

## Advanced Usage

### Custom Storage Backend

You can create your own storage backend by implementing the `StoreInterface`:

```php
use Godam\StoreInterface;

class CustomStore implements StoreInterface
{
    public function get(string $key): mixed { /* ... */ }
    public function set(string $key, mixed $value, ?int $ttl = null): bool { /* ... */ }
    public function delete(string $key): bool { /* ... */ }
    public function clear(): bool { /* ... */ }
    public function has(string $key): bool { /* ... */ }
}
```

### Error Handling

```php
use Godam\Cache;
use Godam\InvalidArgumentException;

try {
    $cache->set('', 'value'); // Empty key throws InvalidArgumentException
} catch (InvalidArgumentException $e) {
    // Handle invalid key
}
```

## Requirements

- PHP >= 8.2
- PSR Cache interfaces (automatically installed via Composer)

## Testing

Run the test suite:

```bash
composer test
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit
```

## License

This project is open-sourced software licensed under the [MIT License](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Links

- [GitHub Repository](https://github.com/vaibhavpandeyvpz/godam)
- [Packagist](https://packagist.org/packages/vaibhavpandeyvpz/godam)
- [PSR-6 Specification](https://www.php-fig.org/psr/psr-6/)
- [PSR-16 Specification](https://www.php-fig.org/psr/psr-16/)
