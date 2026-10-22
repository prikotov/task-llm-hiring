<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Application\Catalog\CatalogImporter;
use App\Exception\DataImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузить провайдеров, модели и строки цен (идемпотентный upsert).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly CatalogImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Pуть к JSON-файлу истории цен (формат — см. TASK.md / fixtures/prices.json).',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $path = (string) $input->getArgument('file');
            $this->importer->import($path);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка импорта: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
