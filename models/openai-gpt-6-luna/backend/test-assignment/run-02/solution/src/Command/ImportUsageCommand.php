<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загрузить события использования')]
final class ImportUsageCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->importer->import((string) $input->getArgument('file'));
            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->getErrorOutput()->writeln('ERROR: '.$exception->getMessage());
            return Command::FAILURE;
        }
    }
}
