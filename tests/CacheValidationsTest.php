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
 * Class CacheValidationsTest
 * @package Godam
 */
class CacheValidationsTest extends \PHPUnit_Framework_TestCase
{
    public function testNonStringKey()
    {
        $this->setExpectedException('Psr\\Cache\\InvalidArgumentException');
        CacheValidations::assertKey(array('key' => 'something'));
    }

    public function testInvalidKey()
    {
        $this->setExpectedException('Psr\\Cache\\InvalidArgumentException');
        CacheValidations::assertKey('MyNS\\Key');
    }
}
