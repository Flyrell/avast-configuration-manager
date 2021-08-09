<?php

namespace App\Cache;

use App\Exception\CacheException;
use App\Cache\Collection\CacheableCollectionInterface;

interface CacheInterface
{

    /**
     * Saves collection to cache by iteration over its items.
     *
     * @param CacheableCollectionInterface $cacheableCollection
     * @return iterable
     * @throws CacheException
     */
    public function saveCollection(CacheableCollectionInterface $cacheableCollection): iterable;
}
