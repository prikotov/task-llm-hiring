<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Загрузка справочника: провайдеры, модели, строки цен (JSON)')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл истории цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $counts = $this->importer->import($file);
        } catch (ImportException $e) {
            $this->error($output)->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $this->error($output)->writeln(sprintf(
            'Справочник загружен: providers=%d, models=%d, price_rows=%d',
            $counts['providers'],
            $counts['models'],
            $counts['prices'],
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
