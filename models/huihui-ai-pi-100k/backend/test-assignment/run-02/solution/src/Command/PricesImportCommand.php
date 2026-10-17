<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Import\PriceFileParser;
use App\Service\PricesImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Загрузка справочника цен (провайдеры, модели, строки цен) — idempotent upsert.
 * Служебная информация — в stderr; код возврата 0 (успех) / 1 (ошибка).
 */
#[AsCommand(
    name: 'app:prices:import',
    description: 'Загружает провайдеров, модели и строки цен из JSON-файла (идемпотентный upsert)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesImportService $pricesImportService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу истории цен')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Только валидация файла, без записи в БД')
            ->setHelp(<<<'HELP'
                Пример: app:prices:import fixtures/prices.json

                Повторная загрузка тех же данных не изменяет содержимое БД
                (провайдеры — по code, модели — по (provider, code),
                строки цен — по (provider, model, valid_from)).
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');
        $path = (string) $input->getArgument('file');
        $errorOutput = $output->getErrorOutput();

        try {
            $priceFile = PriceFileParser::parseFile($path);
        } catch (ImportException $e) {
            $errorOutput->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput->writeln(sprintf(
            'Файл %s: провайдеров %d, моделей %d, строк цен %d.',
            $path,
            \count($priceFile->providers),
            \count($priceFile->models),
            \count($priceFile->prices),
        ));

        if ($dryRun) {
            $errorOutput->writeln('dry-run: запись в БД не выполнялась.');

            return Command::SUCCESS;
        }

        try {
            $stats = $this->pricesImportService->apply($priceFile);
        } catch (ImportException $e) {
            $errorOutput->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput->writeln(sprintf(
            'Импортировано: провайдеров %d, моделей %d, строк цен %d '
            . ' (создано: провайдеров %d, моделей %d, строк цен %d).',
            $stats['providers'],
            $stats['models'],
            $stats['prices'],
            $stats['created_providers'],
            $stats['created_models'],
            $stats['created_prices'],
        ));

        return Command::SUCCESS;
    }
}
