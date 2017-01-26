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

use Predis\Client;

/**
 * Class PredisStoreTest
 * @package Godam\Store
 */
class PredisStoreTest extends StoreTestAbstract
{
    protected function setUp()
    {
        $redis = new Client('tcp://127.0.0.1:6379');
        $this->store = new PredisStore($redis);
    }
}
