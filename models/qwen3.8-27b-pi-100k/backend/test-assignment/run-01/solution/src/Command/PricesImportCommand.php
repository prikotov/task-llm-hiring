<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\ImportException;
use App\Import\PriceFileParser;
use App\Import\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузить провайдеров, модели и строки цен из JSON-файла (идемпотентный upsert)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly PriceImporter $importer,
    ) {
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
            $priceFile = $this->parser->parseFile($file);
            $this->importer->import($priceFile);
        } catch (ImportException|\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка импорта цен: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(
            sprintf('Цены загружены: %d провайдеров, %d моделей, %d строк цен (файл: %s)',
                count($priceFile->getProviders()),
                count($priceFile->getModels()),
                count($priceFile->getPrices()),
                $file,
            ),
        );

        return Command::SUCCESS;
    }
}
