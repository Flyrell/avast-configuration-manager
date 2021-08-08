<?php

namespace App\Cache;

use App\Cache\Collection\CacheableCollectionInterface;

interface CacheInterface
{

    /**
     * Saves collection to cache by iteration over its items.
     *
     * @param CacheableCollectionInterface $cacheableCollection
     * @return iterable
     */
    public function saveCollection(CacheableCollectionInterface $cacheableCollection): iterable;
}
