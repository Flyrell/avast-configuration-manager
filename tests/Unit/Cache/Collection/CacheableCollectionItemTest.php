<?php

namespace App\Tests\Unit\Cache\Collection;

use App\Cache\Collection\CacheableCollectionItem;
use App\Cache\Collection\CacheableCollectionItemInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CacheableCollectionItemTest extends KernelTestCase
{

    public function testShouldImplementCorrectInterface(): void
    {
        $item = new CacheableCollectionItem('123', 'value');

        $this->assertInstanceOf(CacheableCollectionItemInterface::class, $item);
    }

    public function testShouldReturnCorrectValues(): void
    {
        $key = '123';
        $value = 'value';
        $item = new CacheableCollectionItem($key, $value);

        $this->assertEquals($key, $item->getCacheKey());
        $this->assertEquals($value, $item->getCacheValue());
    }

    public function testShouldWorkWithDifferentValueTypes(): void
    {
        $item1 = new CacheableCollectionItem('123', '123');
        $item2 = new CacheableCollectionItem('123', 123);
        $item3 = new CacheableCollectionItem('123', []);
        $item4 = new CacheableCollectionItem('123', $item3);

        $this->assertEquals('123', $item1->getCacheValue());
        $this->assertEquals(123, $item2->getCacheValue());
        $this->assertEquals([], $item3->getCacheValue());
        $this->assertSame($item3, $item4->getCacheValue());
    }
}
