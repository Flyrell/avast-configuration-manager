<?php

namespace App\Tests\Unit\Cache\Provider;

use App\Cache\Provider\RedisCacheAdapterFactory;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RedisCacheAdapterFactoryTest extends KernelTestCase
{

    public function testShouldReturnCorrectInstance()
    {
        $result = RedisCacheAdapterFactory::createConnection('redis://example');

        $this->assertInstanceOf(TagAwareCacheInterface::class, $result);
    }
}
