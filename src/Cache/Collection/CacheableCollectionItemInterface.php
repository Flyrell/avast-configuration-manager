<?php

namespace App\Cache\Collection;

interface CacheableCollectionItemInterface
{

    /**
     * Construct the instance of an item with mandatory fields.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value);

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
