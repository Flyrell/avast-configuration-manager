<?php

namespace App\Cache\Collection;

interface CacheableCollectionItemInterface
{

    /**
     * Returns the key under which item's value will be stored in cache.
     *
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * Returns the value which will be stored in cache.
     *
     * @return mixed
     */
    public function getCacheValue(): mixed;
}
