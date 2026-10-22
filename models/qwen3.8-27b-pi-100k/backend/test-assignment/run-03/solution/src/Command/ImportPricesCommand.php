<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Catalog\CatalogImporter;
use App\Shared\ConsoleStreams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузка провайдеров, моделей и строк цен из JSON-файла (идемпотентный upsert)',
)]
final class ImportPricesCommand extends Command
{
    public function __construct(private readonly CatalogImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSON-файлу истории цен');
        $this->setHelp(<<<'HELP'
Загружает провайдеров, модели и строки цен из JSON-файла.

Повторная загрузка тех же данных не изменяет справочник;
некорректный JSON или структура — ошибка, код выхода 1.
HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $summary = $this->importer->import($file);
        } catch (\Throwable $e) {
            ConsoleStreams::stderr($output)->writeln('app:prices:import: '.$e->getMessage());

            return 1;
        }

        ConsoleStreams::stderr($output)->writeln($summary->line());

        return 0;
    }
}
