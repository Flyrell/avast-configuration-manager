<?php

namespace App\Command;

use Throwable;
use App\Service\ConfigService;
use App\Enum\ConfigLoadCommandArgsEnum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigLoadCommand extends Command
{

    protected static $defaultName = 'config:load';
    protected static $defaultDescription = 'Command used for parsing configuration files';

    public function __construct(private ConfigService $configLoaderService)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->addArgument(
            ConfigLoadCommandArgsEnum::FILEPATH,
            InputArgument::REQUIRED,
            'Path to the file with configuration'
        );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filepath = $input->getArgument(ConfigLoadCommandArgsEnum::FILEPATH);
        $verbosityEnabled = $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;

        try {
            foreach ($this->configLoaderService->loadFromFile($filepath) as $savedKey) {
                if ($verbosityEnabled) {
                    echo $savedKey . PHP_EOL;
                }
            }
        } catch (Throwable $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
