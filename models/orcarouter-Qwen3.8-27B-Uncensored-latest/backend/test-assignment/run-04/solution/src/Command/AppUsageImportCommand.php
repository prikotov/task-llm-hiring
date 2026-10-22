<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageEventImporter;
use App\Shared\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Idempotently loads usage events from a JSONL file (duplicates are ignored)',
)]
final class AppUsageImportCommand extends Command
{
    public function __construct(private readonly UsageEventImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the usage events JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->importer->importFile($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(\sprintf(
            'usage: %d line(s), %d imported, %d duplicate(s) skipped (file %s)',
            $result['lines'],
            $result['imported'],
            $result['duplicates'],
            $file,
        ));

        return Command::SUCCESS;
    }
}
