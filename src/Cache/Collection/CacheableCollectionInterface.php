<?php

namespace App\Cache\Collection;

use DateInterval;

interface CacheableCollectionInterface
{

    /**
     * Adds the item to the collection.
     *
     * @param CacheableCollectionItemInterface $item
     * @return CacheableCollectionInterface
     */
    public function add(CacheableCollectionItemInterface $item): CacheableCollectionInterface;

    /**
     * Returns a single item based on provided key.
     *
     * @param string $key
     * @return CacheableCollectionItemInterface|null
     */
    public function get(string $key): ?CacheableCollectionItemInterface;

    /**
     * Returns all items in collection.
     *
     * @return CacheableCollectionItemInterface[]
     */
    public function getAll(): iterable;

    /**
     * Adds tag for all items inside the collection.
     *
     * @param string $tag
     * @return CacheableCollectionInterface
     */
    public function addTag(string $tag): CacheableCollectionInterface;

    /**
     * Returns tags used for all items inside the collection.
     *
     * @return array
     */
    public function getTags(): array;

    /**
     * Sets cache expiration for all items inside the collection.
     *
     * @param DateInterval|string $interval
     * @return CacheableCollectionInterface
     */
    public function setExpiresAfter(DateInterval|string $interval): CacheableCollectionInterface;

    /**
     * Returns the expiration used for all items inside the collection.
     *
     * @return DateInterval
     */
    public function expiresAfter(): DateInterval;
}
