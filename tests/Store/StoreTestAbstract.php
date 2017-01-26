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
 * Class StoreTestAbstract
 * @package Godam\Store
 */
abstract class StoreTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StoreInterface
     */
    protected $store;

    protected function tearDown()
    {
        $this->store->clear();
    }

    public function testStore()
    {
        $this->assertFalse($this->store->has($key = 'somekey'));
        $this->assertNull($this->store->get($key));
        $this->store->set($key, $value = 'somevalue');
        $this->assertTrue($this->store->has($key));
        $this->assertEquals($value, $this->store->get($key));
        $this->store->delete($key);
        $this->assertFalse($this->store->has($key));
    }
}
