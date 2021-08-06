<?php

namespace App\Tests\Unit\Service;

use App\Service\FileService;
use App\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileServiceTest extends KernelTestCase
{

    private Filesystem $filesystemService;
    private static string $existingFilepath = __DIR__ . '/Resources/file.txt';
    private static string $nonexistentFilepath = __DIR__ . '/Resources/no_file.txt';

    protected function setUp(): void
    {
        $this->filesystemService = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testShouldReadExistingFile(): void
    {
        $this->filesystemService
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $service = new FileService($this->filesystemService);

        try {
            $content = $service->read(self::$existingFilepath);
        } catch (FileException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertStringContainsString('Testing file', $content);
    }

    public function testShouldTrimFileContents(): void
    {
        $this->filesystemService
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $service = new FileService($this->filesystemService);

        try {
            $content = $service->read(self::$existingFilepath);
        } catch (FileException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertEquals('Testing file', $content);
    }

    public function testShouldFailToReadNonexistentFile(): void
    {
        $this->filesystemService
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $service = new FileService($this->filesystemService);

        $this->expectException(FileException::class);
        $service->read(self::$nonexistentFilepath);
    }
}
