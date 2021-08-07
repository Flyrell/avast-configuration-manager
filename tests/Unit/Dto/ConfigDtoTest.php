<?php

namespace App\Tests\Unit\Dto;

use DateInterval;
use App\Dto\ConfigDto;
use App\Enum\CacheTagsEnum;
use App\Parser\ConfigInterface;
use App\Cache\Collection\CacheableCollectionItem;
use App\Cache\Collection\CacheableCollectionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigDtoTest extends KernelTestCase
{

    public function testShouldImplementCorrectInterfaces(): void
    {
        $config = new ConfigDto();

        $this->assertInstanceOf(ConfigInterface::class, $config);
        $this->assertInstanceOf(CacheableCollectionInterface::class, $config);
    }

    public function testShouldSetCacheableItem(): void
    {
        $config = new ConfigDto();

        $key = '123';
        $item = new CacheableCollectionItem($key, 'example');
        $config->set($key, $item);

        $this->assertSame($item, $config->get($key));
    }

    public function testShouldSetAndConvertNonCacheableItem(): void
    {
        $config = new ConfigDto();

        $key = '123';
        $config->set($key, 'value');

        $this->assertInstanceOf(CacheableCollectionItem::class, $config->get($key));
    }

    public function testShouldGetCorrectItem(): void
    {
        $config = new ConfigDto();

        $key = '123';
        $item = new CacheableCollectionItem($key, 'example');
        $config->set($key, $item);

        $this->assertSame($item, $config->get($key));
    }

    public function testShouldReturnNullWhenItemDoesNotExist(): void
    {
        $config = new ConfigDto();

        $this->assertNull($config->get('123'));
    }

    public function testShouldReturnCorrectTags(): void
    {
        $config = new ConfigDto();
        $tags = [ CacheTagsEnum::CONFIG ];

        $this->assertEquals($tags, $config->getTags());
    }

    public function testShouldReturnCorrectExpiresAfter(): void
    {
        $config = new ConfigDto();
        $expireAfter = DateInterval::createFromDateString('+3 hours');
        $expected = $expireAfter->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $config->expiresAfter()->format('Y-m-d H:i:s'));
    }
}
