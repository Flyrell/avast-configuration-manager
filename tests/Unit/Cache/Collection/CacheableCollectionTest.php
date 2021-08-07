<?php

namespace App\Tests\Unit\Cache\Collection;

use DateInterval;
use App\Cache\Collection\CacheableCollection;
use App\Cache\Collection\CacheableCollectionItem;
use App\Cache\Collection\CacheableCollectionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CacheableCollectionTest extends KernelTestCase
{

    public function testShouldImplementCorrectInterface(): void
    {
        $collection = new CacheableCollection();

        $this->assertInstanceOf(CacheableCollectionInterface::class, $collection);
    }

    public function testShouldAddItem(): void
    {
        $item = new CacheableCollectionItem('123', null);
        $collection = new CacheableCollection();
        $collection->add($item);

        $this->assertSame($item, $collection->get('123'));
    }

    public function testShouldAddMultipleItems(): void
    {
        $item1 = new CacheableCollectionItem('123', null);
        $item2 = new CacheableCollectionItem('234', null);
        $collection = new CacheableCollection();
        $collection->add($item1)->add($item2);

        $this->assertSame($item1, $collection->get('123'));
        $this->assertSame($item2, $collection->get('234'));
    }

    public function testShouldNotAddMultipleItemsWithEqualKeys(): void
    {
        $item1 = new CacheableCollectionItem('123', null);
        $item2 = new CacheableCollectionItem('123', null);
        $collection = new CacheableCollection();
        $collection->add($item1)->add($item2);

        $this->assertCount(1, $collection->getAll());
    }

    public function testShouldGetTheKey(): void
    {
        $key = '123';
        $item1 = new CacheableCollectionItem($key, null);
        $collection = new CacheableCollection();
        $collection->add($item1);

        $this->assertSame($item1, $collection->get($key));
    }

    public function testShouldReturnNullIfItemDoesNotExist(): void
    {
        $key = '123';
        $collection = new CacheableCollection();

        $this->assertNull($collection->get($key));
    }

    public function testShouldReturnLatestItemWhenItemsHadTheSameKey(): void
    {
        $key = '123';
        $item1 = new CacheableCollectionItem($key, 1);
        $item2 = new CacheableCollectionItem($key, 2);
        $collection = new CacheableCollection();
        $collection->add($item1)->add($item2);

        $this->assertSame($item2, $collection->get($key));
    }

    public function testShouldReturnAllItems(): void
    {
        $item1 = new CacheableCollectionItem('123', 1);
        $item2 = new CacheableCollectionItem('456', 2);
        $collection = new CacheableCollection();
        $collection->add($item1)->add($item2);

        $this->assertCount(2, $collection->getAll());
        $this->assertSame($item1, $collection->getAll()[0]);
        $this->assertSame($item2, $collection->getAll()[1]);
    }

    public function testShouldReturnEmptyArrayWhenNoItems(): void
    {
        $collection = new CacheableCollection();

        $this->assertEmpty($collection->getAll());
    }

    public function testShouldAddTag(): void
    {
        $tag = 'tag';
        $collection = new CacheableCollection();
        $collection->addTag($tag);

        $this->assertEquals($tag, $collection->getTags()[0]);
    }

    public function testShouldAddMultipleTags(): void
    {
        $tag1 = 'tag';
        $tag2 = 'mag';
        $collection = new CacheableCollection();
        $collection->addTag($tag1)->addTag($tag2);

        $this->assertCount(2, $collection->getTags());
        $this->assertEquals($tag1, $collection->getTags()[0]);
        $this->assertEquals($tag2, $collection->getTags()[1]);
    }

    public function testShouldReturnEmptyArrayWhenNoTags(): void
    {
        $collection = new CacheableCollection();

        $this->assertEmpty($collection->getTags());
    }

    public function testShouldSetExpiresAfterWithStringAndConvertItToDateInterval(): void
    {
        $collection = new CacheableCollection();
        $collection->setExpiresAfter('+1 hour');

        $dateInterval = DateInterval::createFromDateString('+1 hour');
        $expected = $dateInterval->format('Y-m-d H:i:s');

        $this->assertInstanceOf(DateInterval::class, $collection->expiresAfter());
        $this->assertEquals($expected, $collection->expiresAfter()->format('Y-m-d H:i:s'));
    }

    public function testShouldSetExpiresAfterWithDateInterval(): void
    {
        $dateInterval = DateInterval::createFromDateString('+1 hour');

        $collection = new CacheableCollection();
        $collection->setExpiresAfter($dateInterval);

        $this->assertSame($dateInterval, $collection->expiresAfter());
    }
}
