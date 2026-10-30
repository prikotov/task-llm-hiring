<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ApplicationServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from a JSONL file.')]
final class UsageImportCommand extends Command
{
    protected static string $defaultName = 'app:usage:import';

    public function __construct(
        private readonly ApplicationServices $app,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $stderr = $output->getErrorOutput();

        try {
            $result = $this->app->importUsage($path);
        } catch (\RuntimeException $e) {
            $stderr->writeln('ERROR: ' . $e->getMessage());
            return Command::FAILURE;
        }

        foreach ($result->errors as $err) {
            $stderr->writeln($err);
        }

        $exitCode = $result->errors !== [] ? Command::FAILURE : Command::SUCCESS;

        $output->writeln(sprintf('IMPORTED_EVENTS=%d', $result->usageEvents));

        return $exitCode;
    }
}
