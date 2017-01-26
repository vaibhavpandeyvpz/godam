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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheItemPool
 * @package Godam
 */
class CacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]
     */
    protected $deferred = array();

    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * CacheItemPool constructor.
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->store->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $saved = true;
        foreach ($this->deferred as $item) {
            $saved = $this->save($item);
        }
        $this->deferred = array();
        return $saved;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        CacheValidations::assertKey($key);
        return $this->store->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $deleted = true;
        foreach ($keys as $key) {
            $deleted = $this->deleteItem($key);
        }
        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        CacheValidations::assertKey($key);
        if ($this->store->has($key)) {
            $data = $this->store->get($key);
            extract($data);
            /** @var int $expiry */
            /** @var mixed $value */
            if (is_null($expiry) || ($expiry > time())) {
                $item = new CacheItem($key, true);
                $item->set($value);
                return $item;
            }
            $this->deleteItem($key);
        }
        return new CacheItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = array())
    {
        $items = array();
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return $this->store->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        if ($item instanceof CacheItem) {
            return $this->store->set($item->getKey(), array(
                'value' => $item->get(),
                'expiry' => $item->getExpiry(),
            ));
        }
        throw new InvalidArgumentException(sprintf(
            "Cannot only handle items of type 'Vidyut\\Cache\\CacheItem'; '%s' given", get_class($item)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[] = $item;
        return true;
    }
}
