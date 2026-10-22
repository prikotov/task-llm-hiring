<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Shared\InvalidInput;
use App\Usage\Application\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импорт событий использования (JSONL, с дедупликацией)')]
final class ImportUsageCommand extends Command
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
        $err = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $r = $this->importer->import(FileReader::lines((string) $input->getArgument('file')));
        } catch (InvalidInput $e) {
            $err->writeln('ERROR '.$e->getMessage());

            return Command::FAILURE;
        }
        $err->writeln(sprintf('Загружено событий: %d, дублей пропущено: %d', $r['inserted'], $r['duplicates']));

        return Command::SUCCESS;
    }
}
