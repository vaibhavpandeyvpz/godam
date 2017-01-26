<?php

/*
 * This file is part of vaibhavpandeyvpz/godam package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Godam\Store;

use Godam\StoreInterface;

/**
 * Class MemoryStore
 * @package Godam\Store
 */
class MemoryStore implements StoreInterface
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->values = array();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->values[$key]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->has($key) ? $this->values[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
        return true;
    }
}
