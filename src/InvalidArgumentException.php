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

use Psr\Cache\InvalidArgumentException as PsrCacheInvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PsrSimpleCacheInvalidArgumentException;

/**
 * Exception thrown when an invalid argument is provided to a cache method.
 *
 * Typically thrown when cache keys are invalid or when incompatible types are passed.
 *
 * @implements PsrCacheInvalidArgumentException
 * @implements PsrSimpleCacheInvalidArgumentException
 */
final class InvalidArgumentException extends CacheException implements PsrCacheInvalidArgumentException, PsrSimpleCacheInvalidArgumentException {}
