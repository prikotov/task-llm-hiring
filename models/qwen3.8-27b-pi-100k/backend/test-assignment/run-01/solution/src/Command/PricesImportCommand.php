<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\ImportException;
use App\Import\PricesImporter;
use App\Import\PricesParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импортирует справочник: провайдеров, модели и строки цен (JSON-файл). Идемпотентный upsert.',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesParser $pricesParser,
        private readonly PricesImporter $pricesImporter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        if (!is_file($path) || !is_readable($path)) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: файл не найден или не читается: %s', $path));

            return 1;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: невозможно прочитать файл: %s', $path));

            return 1;
        }

        try {
            $payload = $this->pricesParser->parseImport($raw);
            $counters = $this->pricesImporter->import($payload);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return 1;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'Импортировано: провайдеров — %d, моделей — %d, строк цен — %d.',
            $counters['providers'],
            $counters['models'],
            $counters['priceRows'],
        ));

        return 0;
    }
}
