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

/**
 * Interface StoreInterface
 * @package Godam
 */
interface StoreInterface
{
    /**
     * @return bool
     */
    public function clear();

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value);
}
