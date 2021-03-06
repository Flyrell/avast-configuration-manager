<?php

namespace App\Cache\Adapter;

use App\Cache\CacheInterface;
use App\Exception\CacheException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use App\Cache\Collection\CacheableCollectionInterface;

class RedisCacheAdapter implements CacheInterface
{

    public function __construct(private TagAwareCacheInterface $cache) {}

    /**
     * @inheritDoc
     */
    public function saveCollection(CacheableCollectionInterface $cacheableCollection): iterable
    {
        foreach ($cacheableCollection->getAll() as &$cacheableItem) {
            try {
                $this->cache->get(
                    $cacheableItem->getCacheKey(),
                    function (ItemInterface $item) use (&$cacheableItem, &$cacheableCollection) {
                        $item->tag($cacheableCollection->getTags());
                        $item->expiresAfter($cacheableCollection->expiresAfter());

                        return $cacheableItem->getCacheValue();
                    }
                );
                yield $cacheableItem->getCacheKey();
            } catch (InvalidArgumentException $e) {
                throw new CacheException(CacheException::COULD_NOT_SAVE, [
                    $cacheableItem->getCacheKey(),
                ]);
            }
        }
    }
}
