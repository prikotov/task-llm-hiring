<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from a JSONL file')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        try {
            $inserted = $this->importer->import($file);
            $io->success("Import complete: {$inserted} event(s) inserted");
            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
