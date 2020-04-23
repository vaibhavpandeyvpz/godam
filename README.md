# vaibhavpandeyvpz/godam
Simple [PSR-6](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-6-cache.md)/[PSR-16](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md) cache implementations [PHP](http://php.net) >= 5.3.

> Godam: `गोदाम` (Warehouse)

[![Build status][build-status-image]][build-status-url]
[![Code Coverage][code-coverage-image]][code-coverage-url]
[![Latest Version][latest-version-image]][latest-version-url]
[![Downloads][downloads-image]][downloads-url]
[![PHP Version][php-version-image]][php-version-url]
[![License][license-image]][license-url]

[![SensioLabsInsight][insights-image]][insights-url]

Install
-------
```bash
composer require vaibhavpandeyvpz/godam
```

Usage
-----
```php
<?php

/**
 * @desc Create an instance of Godam\StoreInterface
 */
$store = new Godam\Store\MemoryStore();
// Or
$store = new Godam\Store\FileSystemStore(__DIR__ . '/cache');
// Or
$memcache = new Memcache();
$memcache->connect('localhost', 11211);
$store = new Godam\Store\MemcacheStore($memcache);
// Or
$redis = new Predis\Client('tcp://127.0.0.1:6379');
$store = new Godam\Store\PredisStore($redis);
// Or
$redis = new Redis();
$redis->connect('localhost', 6379);
$store = new Godam\Store\RedisStore($redis);

/*
 * @desc Using the simpler, PSR-16 cache
 */
$cache = new Godam\Cache($store);
$cache->set('somekey', 'somevalue', 3600 /** ttl in second(s), can be null */);
$value = $cache->get('somekey');
$cache->delete('somekey');

/*
 * @desc Or the older, PSR-6 item pool
 */
$cache = new Godam\CacheItemPool($store);
$item = $cache->getItem('somekey');
if ($item->isHit()) {
    $value = $item->get();
} else {
    $item->set('somevalue');
    $cache->save($item);
}
$cache->deleteItem('somekey');
```

License
------
See [LICENSE.md][license-url] file.

[build-status-image]: https://img.shields.io/travis/vaibhavpandeyvpz/godam.svg?style=flat-square
[build-status-url]: https://travis-ci.org/vaibhavpandeyvpz/godam
[code-coverage-image]: https://img.shields.io/codecov/c/github/vaibhavpandeyvpz/godam.svg?style=flat-square
[code-coverage-url]: https://codecov.io/gh/vaibhavpandeyvpz/godam
[latest-version-image]: https://img.shields.io/github/release/vaibhavpandeyvpz/godam.svg?style=flat-square
[latest-version-url]: https://github.com/vaibhavpandeyvpz/godam/releases
[downloads-image]: https://img.shields.io/packagist/dt/vaibhavpandeyvpz/godam.svg?style=flat-square
[downloads-url]: https://packagist.org/packages/vaibhavpandeyvpz/godam
[php-version-image]: http://img.shields.io/badge/php-5.3+-8892be.svg?style=flat-square
[php-version-url]: https://packagist.org/packages/vaibhavpandeyvpz/godam
[license-image]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[license-url]: LICENSE.md
[insights-image]: https://insight.sensiolabs.com/projects/9926ffe6-653c-4074-96c4-0f2198773195/small.png
[insights-url]: https://insight.sensiolabs.com/projects/9926ffe6-653c-4074-96c4-0f2198773195
