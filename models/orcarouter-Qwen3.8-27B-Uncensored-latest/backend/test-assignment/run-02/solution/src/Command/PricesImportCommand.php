<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PriceCatalogImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузить справочник цен (провайдеры, модели, строки цен) из JSON-файла',
)]
final class PricesImportCommand extends BaseCommand
{
    public function __construct(
        private readonly PriceCatalogImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу справочника цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        return $this->withErrorHandling($input, $output, function () use ($file, $input, $output): int {
            $this->importer->import($file);
            $output->getErrorOutput()->writeln(sprintf('Справочник цен загружен из %s', $file));
            return self::SUCCESS;
        });
    }
}
