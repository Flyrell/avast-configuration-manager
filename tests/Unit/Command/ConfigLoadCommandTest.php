<?php

namespace App\Tests\Unit\Command;

use App\Command\ConfigLoadCommand;
use App\Service\ConfigLoaderService;
use App\Exception\ConfigLoadException;
use App\Enum\ConfigLoadCommandArgsEnum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;

class ConfigLoadCommandTest extends KernelTestCase
{

    private Application $application;

    private static string $filepath = 'file.xml';

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::createKernel();
        $this->application = new Application($kernel);
    }

    public function testCommandShouldBeRegistered(): void
    {
        $command = $this->application->find('config:load');

        $this->assertNotNull($command);
    }

    public function testArgumentFilepathShouldBeRequired(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigLoaderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new ConfigLoadCommand($configLoaderServiceMock);
        $commandTester = new CommandTester($command);

        $this->expectException(RuntimeException::class);
        $commandTester->execute([]);
    }

    public function testShouldExecuteCorrectlyWithRequiredArgument(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigLoaderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new ConfigLoadCommand($configLoaderServiceMock);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testShouldCallLoadFromFileWithCorrectArguments(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigLoaderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configLoaderServiceMock
            ->expects($this->once())
            ->method('loadFromFile')
            ->with(self::$filepath);

        $command = new ConfigLoadCommand($configLoaderServiceMock);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);
    }

    public function testShouldDisplayErrorMessageOnFailure(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigLoaderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configLoaderServiceMock
            ->expects($this->once())
            ->method('loadFromFile')
            ->willThrowException(new ConfigLoadException(0));

        $command = new ConfigLoadCommand($configLoaderServiceMock);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);

        $this->assertStringContainsString('Unknown error', $commandTester->getDisplay());
    }
}
