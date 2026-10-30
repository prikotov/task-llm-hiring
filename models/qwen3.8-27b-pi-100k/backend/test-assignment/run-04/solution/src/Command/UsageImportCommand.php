<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загрузка журнала событий использования (JSONL, дубли игнорируются)')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий использования');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->importer->import($file);
        } catch (ImportException $e) {
            $this->error($output)->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $this->error($output)->writeln(sprintf(
            'События загружены: %d новых, %d дублей проигнорировано',
            $result['imported'],
            $result['duplicates'],
        ));

        return Command::SUCCESS;
    }

    /** Ошибки/статусы пишутся в stderr при настоящем консольном запуске. */
    private function error(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;
    }
}
