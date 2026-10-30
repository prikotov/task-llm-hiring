<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\InputException;
use App\Application\Import\PriceCatalogImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника: провайдеры, модели, строки цен (JSON-файл)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceCatalogImporter $importer,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу справочника цен');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $this->importer->import($file);
        } catch (InputException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . ltrim($e->getMessage(), ' '));
            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln('OK: справочник цен импортирован');
        return Command::SUCCESS;
    }
}
