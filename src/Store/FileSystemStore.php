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

use Godam\StoreInterface;

/**
 * File system-based cache store implementation.
 *
 * Stores cache items as serialized files on the filesystem using a two-level
 * directory structure based on key hash to avoid too many files in a single directory.
 *
 * @implements StoreInterface
 */
final class FileSystemStore implements StoreInterface
{
    /**
     * @param  string  $directory  The base directory where cache files will be stored
     */
    public function __construct(
        private readonly string $directory
    ) {}

    /**
     * Clears all cache files from the directory.
     *
     * @return bool True if the directory was successfully cleared, false otherwise
     */
    public function clear(): bool
    {
        if (is_dir($this->directory)) {
            self::deleteDirectoryContents($this->directory);
        }

        return true;
    }

    /**
     * Deletes a cache file from the filesystem.
     *
     * @param  string  $key  The key of the item to delete
     * @return bool True if the file was successfully deleted, false if it didn't exist
     */
    public function delete(string $key): bool
    {
        $file = self::getPathToFile($this->directory, $key);

        return is_file($file) && unlink($file);
    }

    /**
     * Recursively deletes all contents of a directory.
     *
     * @param  string  $directory  The directory to clear
     */
    private static function deleteDirectoryContents(string $directory): void
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
     * Retrieves a cache item from the filesystem.
     *
     * @param  string  $key  The key of the item to retrieve
     * @return mixed The unserialized value, or null if the file doesn't exist
     */
    public function get(string $key): mixed
    {
        $file = self::getPathToFile($this->directory, $key);
        if (! is_file($file)) {
            return null;
        }

        $contents = file_get_contents($file);

        return $contents !== false ? unserialize($contents) : null;
    }

    /**
     * Generates the file path for a given cache key.
     *
     * Uses a two-level directory structure based on key hash to distribute files.
     *
     * @param  string  $directory  The base directory
     * @param  string  $key  The cache key
     * @return string The full path to the cache file
     */
    private static function getPathToFile(string $directory, string $key): string
    {
        $hash = crc32($key);
        $l1 = intdiv($hash, 100) % 100;
        $l2 = $hash % 100;

        return sprintf('%s%s%d%s%d%s%s', $directory, DIRECTORY_SEPARATOR, $l1, DIRECTORY_SEPARATOR, $l2, DIRECTORY_SEPARATOR, $key);
    }

    /**
     * Checks if a cache file exists.
     *
     * @param  string  $key  The key to check
     * @return bool True if the file exists, false otherwise
     */
    public function has(string $key): bool
    {
        $file = self::getPathToFile($this->directory, $key);

        return is_file($file);
    }

    /**
     * Stores a cache item to the filesystem.
     *
     * Creates parent directories if they don't exist and serializes the value.
     *
     * @param  string  $key  The key under which to store the value
     * @param  mixed  $value  The value to store (will be serialized)
     * @return bool True if the file was successfully written, false otherwise
     */
    public function set(string $key, mixed $value): bool
    {
        $file = self::getPathToFile($this->directory, $key);
        $parent = pathinfo($file, PATHINFO_DIRNAME);
        if (! is_dir($parent)) {
            mkdir($parent, 0755, true);
        }

        return file_put_contents($file, serialize($value)) !== false;
    }
}
