<?php

namespace App\Tests\Unit\Dto;

use App\Dto\CookieDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Cache\Collection\CacheableCollectionItemInterface;

class CookieDtoTest extends KernelTestCase
{

    public function testShouldImplementCorrectInterface(): void
    {
        $cookie = new CookieDto('cookie', '213');

        $this->assertInstanceOf(CacheableCollectionItemInterface::class, $cookie);
    }
}
