<?php

namespace App\Tests\Unit\Command;

use App\Service\ConfigService;
use App\Command\ConfigLoadCommand;
use App\Exception\ConfigLoadException;
use App\Enum\ConfigLoadCommandArgsEnum;
use App\Logger\DisableableLoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;

class ConfigLoadCommandTest extends KernelTestCase
{

    private Application $application;
    private DisableableLoggerInterface $logger;

    private static string $filepath = 'file.xml';

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::createKernel();
        $this->application = new Application($kernel);
        $this->logger = $this->getMockForAbstractClass(DisableableLoggerInterface::class);
    }

    public function testCommandShouldBeRegistered(): void
    {
        $command = $this->application->find('config:load');

        $this->assertNotNull($command);
    }

    public function testArgumentFilepathShouldBeRequired(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $this->expectException(RuntimeException::class);
        $commandTester->execute([]);
    }

    public function testShouldExecuteCorrectlyWithRequiredArgument(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testShouldCallLoadFromFileWithCorrectArguments(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configLoaderServiceMock
            ->expects($this->once())
            ->method('loadFromFile')
            ->with(self::$filepath);

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);
    }

    public function testShouldDisplayErrorMessageOnFailure(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configLoaderServiceMock
            ->expects($this->once())
            ->method('loadFromFile')
            ->willThrowException(new ConfigLoadException(0));

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);

        $this->assertStringContainsString('Unknown error', $commandTester->getDisplay());
    }

    public function testShouldDisableLoggerWhenNoVerbosity(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger->expects($this->once())
            ->method('disableLogging');

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ]);
    }

    public function testShouldNotDisableLoggerWhenVerbosity(): void
    {
        $configLoaderServiceMock = $this->getMockBuilder(ConfigService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger->expects($this->never())
            ->method('disableLogging');

        $command = new ConfigLoadCommand($configLoaderServiceMock, $this->logger);
        $commandTester = new CommandTester($command);

        $commandTester->execute([ ConfigLoadCommandArgsEnum::FILEPATH => self::$filepath ], [
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ]);
    }
}
