<?php

namespace App\Tests\Unit\Parser\XMLNodeParser;

use App\Parser\XMLNodeParser\SubdomainsParser;
use App\Parser\XMLNodeParser\XMLNodeParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubdomainsParserTest extends KernelTestCase
{

    public function testShouldImplementXMLNodeParserInterface(): void
    {
        $parser = new SubdomainsParser();
        $this->assertInstanceOf(XMLNodeParserInterface::class, $parser);
    }
}
