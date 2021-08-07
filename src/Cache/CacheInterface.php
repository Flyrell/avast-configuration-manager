<?php

namespace App\Cache;

use App\Cache\Collection\CacheableCollectionInterface;

interface CacheInterface
{

    /**
     * Saves collection to cache by iteration over its items.
     *
     * @param CacheableCollectionInterface $cacheableCollection
     */
    public function saveCollection(CacheableCollectionInterface $cacheableCollection): void;
}
