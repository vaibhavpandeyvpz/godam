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
use Psr\SimpleCache\InvalidArgumentException as PsrException2;

/**
 * Class InvalidArgumentException
 * @package Godam
 */
class InvalidArgumentException extends CacheException implements PsrException, PsrException2
{
}
