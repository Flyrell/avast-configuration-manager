<?php

namespace App\Tests\Unit\Validator;

use DOMDocument;
use App\Validator\XMLConfigValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class XMLConfigValidatorTest extends KernelTestCase
{

    public function testShouldValidateCorrectDocument()
    {
        $mapping = [];
        $xml = new DOMDocument();
        $validator = new XMLConfigValidator($mapping);

        $result = $validator->validate($xml);
        $this->assertTrue($result);
    }

    public function testShouldNotValidateDocumentWithoutConfigElement()
    {
        $mapping = [
            'testing_key' => [
                'type' => 'string'
            ],
        ];
        $xml = new DOMDocument();
        $xml->append($xml->createElement('nothing'));

        $validator = new XMLConfigValidator($mapping);

        $result = $validator->validate($xml);
        $this->assertFalse($result);
    }

    public function testShouldNotValidateIncorrectDocumentWithConfigElement()
    {
        $mapping = [
            'testing_key' => [
                'type' => 'string'
            ],
        ];
        $xml = new DOMDocument();
        $xml->append($xml->createElement('config'));

        $validator = new XMLConfigValidator($mapping);

        $result = $validator->validate($xml);
        $this->assertFalse($result);
    }

    public function testShouldGetConfigElementFromDocument()
    {
        $xml = new DOMDocument();
        $xml->append($xml->createElement('config'));

        $mockDocument = $this->getMockBuilder(DOMDocument::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockDocument->expects($this->once())
            ->method('getElementsByTagName')
            ->with('config')
            ->willReturn($xml->getElementsByTagName('config'));

        $validator = new XMLConfigValidator([]);
        $validator->validate($mockDocument);
    }
}
