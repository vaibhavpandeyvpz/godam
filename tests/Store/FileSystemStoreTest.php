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

namespace Godam\Store;

final class FileSystemStoreTest extends StoreTestAbstract
{
    protected function setUp(): void
    {
        $this->store = new FileSystemStore(sys_get_temp_dir().DIRECTORY_SEPARATOR.'godam');
    }
}
