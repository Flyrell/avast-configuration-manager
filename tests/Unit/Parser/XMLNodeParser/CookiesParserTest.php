<?php

namespace App\Tests\Unit\Parser\XMLNodeParser;

use App\Parser\XMLNodeParser\CookiesParser;
use App\Parser\XMLNodeParser\XMLNodeParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CookiesParserTest extends KernelTestCase
{

    public function testShouldImplementXMLNodeParserInterface(): void
    {
        $parser = new CookiesParser();
        $this->assertInstanceOf(XMLNodeParserInterface::class, $parser);
    }
}
