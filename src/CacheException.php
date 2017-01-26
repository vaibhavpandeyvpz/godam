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

use Psr\Cache\InvalidArgumentException as PsrException;
use Psr\SimpleCache\CacheException as PsrException2;

/**
 * Class CacheException
 * @package Godam
 */
class CacheException extends \Exception implements PsrException, PsrException2
{
}
