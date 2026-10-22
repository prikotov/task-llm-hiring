<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Import\ImportException;
use App\Application\Import\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импортирует события использования')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->importer->import((string) $input->getArgument('file'));
        } catch (ImportException $exception) {
            $this->error($output, $exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function error(OutputInterface $output, string $message): void
    {
        $target = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $target->writeln('ERROR: '.$message);
    }
}
