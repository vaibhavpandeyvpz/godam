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
 * Class FileSystemStore
 * @package Godam\Store
 */
class FileSystemStore implements StoreInterface
{
    protected $directory;

    /**
     * FileSystemStore constructor.
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        if (is_dir($this->directory)) {
            self::deleteDirectoryContents($this->directory);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $file = self::getPathToFile($this->directory, $key);
        return is_file($file) ? unlink($file) : false;
    }

    /**
     * @param $directory
     */
    private static function deleteDirectoryContents($directory)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            /** @var \SplFileInfo $file */
            $path = $file->getPathname();
            if ($file->isDir()) {
                self::deleteDirectoryContents($path);
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $file = self::getPathToFile($this->directory, $key);
        if (is_file($file)) {
            $contents = file_get_contents($file);
            return unserialize($contents);
        }
        return null;
    }

    /**
     * @param string $directory
     * @param string $key
     * @return string
     */
    private static function getPathToFile($directory, $key)
    {
        $hash = crc32($key);
        $l1 = $hash / 100 % 100;
        $l2 = $hash % 100;
        return $directory . DIRECTORY_SEPARATOR . $l1 . DIRECTORY_SEPARATOR . $l2 . DIRECTORY_SEPARATOR . $key;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $file = self::getPathToFile($this->directory, $key);
        return is_file($file);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $file = self::getPathToFile($this->directory, $key);
        $parent = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($parent)) {
            mkdir($parent, 0755, true);
        }
        return file_put_contents($file, serialize($value)) !== false;
    }
}
