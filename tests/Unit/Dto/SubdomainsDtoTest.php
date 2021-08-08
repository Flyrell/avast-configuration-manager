<?php

namespace App\Tests\Unit\Dto;

use App\Dto\SubdomainsDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Cache\Collection\CacheableCollectionItemInterface;

class SubdomainsDtoTest extends KernelTestCase
{

    public function testShouldImplementCorrectInterface(): void
    {
        $subdomains = new SubdomainsDto('subs', []);

        $this->assertInstanceOf(CacheableCollectionItemInterface::class, $subdomains);
    }

    public function testShouldAddSubdomain(): void
    {
        $subdomain = 'https://subdomain.com';
        $subdomains = new SubdomainsDto('subs', []);
        $subdomains->add($subdomain);

        $this->assertEquals("[\"$subdomain\"]", $subdomains->getCacheValue());
    }

    public function testShouldAddMultipleSubdomains(): void
    {
        $subdomain1 = 'https://subdomain1.com';
        $subdomain2 = 'https://subdomain2.com';
        $subdomains = new SubdomainsDto('subs', []);
        $subdomains->add($subdomain1);
        $subdomains->add($subdomain2);

        $this->assertEquals("[\"$subdomain1\",\"$subdomain2\"]", $subdomains->getCacheValue());
    }

    public function testShouldNotAddDuplicatedSubdomains(): void
    {
        $subdomain1 = 'https://subdomain.com';
        $subdomain2 = 'https://subdomain.com';
        $subdomains = new SubdomainsDto('subs', []);
        $subdomains->add($subdomain1);
        $subdomains->add($subdomain2);

        $this->assertEquals("[\"$subdomain1\"]", $subdomains->getCacheValue());
    }

    public function testShouldCorrectlyEncodeValue(): void
    {
        $subdomain1 = 'https://subdomain.com';
        $subdomains = new SubdomainsDto('subs', []);
        $subdomains->add($subdomain1);

        $this->assertEquals("[\"$subdomain1\"]", $subdomains->getCacheValue());
    }

    public function testShouldCorrectlyReturnEmptyValue(): void
    {
        $subdomains = new SubdomainsDto('subs', []);

        $this->assertEquals("[]", $subdomains->getCacheValue());
    }
}
