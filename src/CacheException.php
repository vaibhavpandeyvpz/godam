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

use Psr\Cache\CacheException as PsrCacheException;
use Psr\SimpleCache\CacheException as PsrSimpleCacheException;

/**
 * Base exception class for cache-related errors.
 *
 * Implements both PSR-6 and PSR-16 cache exception interfaces.
 *
 * @implements PsrCacheException
 * @implements PsrSimpleCacheException
 */
class CacheException extends \Exception implements PsrCacheException, PsrSimpleCacheException {}
