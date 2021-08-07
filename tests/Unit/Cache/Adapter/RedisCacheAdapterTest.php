<?php

namespace App\Tests\Unit\Cache\Adapter;

use Psr\Cache\InvalidArgumentException;
use App\Cache\Adapter\RedisCacheAdapter;
use App\Cache\Collection\CacheableCollection;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use App\Cache\Collection\CacheableCollectionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Cache\Collection\CacheableCollectionItemInterface;

class RedisCacheAdapterTest extends KernelTestCase
{

    private TagAwareCacheInterface $cacheAdapter;

    protected function setUp(): void
    {
        $this->cacheAdapter = $this->getMockForAbstractClass(TagAwareCacheInterface::class);
    }

    public function testShouldGetAllItemsFromCollection(): void
    {
        $collection = $this->getMockForAbstractClass(CacheableCollectionInterface::class);
        $collection->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $adapter = new RedisCacheAdapter($this->cacheAdapter);

        try {
            $adapter->saveCollection($collection);
        } catch (InvalidArgumentException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }

    public function testShouldTrySavingToCache(): void
    {
        $item = $this->getMockForAbstractClass(CacheableCollectionItemInterface::class);
        $collection = $this->getMockForAbstractClass(CacheableCollectionInterface::class);
        $collection->expects($this->once())
            ->method('getAll')
            ->willReturn([ $item ]);

        $this->cacheAdapter->expects($this->once())
            ->method('get');

        $adapter = new RedisCacheAdapter($this->cacheAdapter);

        try {
            $adapter->saveCollection($collection);
        } catch (InvalidArgumentException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }
}
