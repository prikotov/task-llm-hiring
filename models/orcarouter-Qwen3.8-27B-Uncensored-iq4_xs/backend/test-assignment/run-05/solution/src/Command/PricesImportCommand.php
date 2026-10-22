<?php

declare(strict_types=1);

namespace App\Command;

use App\Importer\ImportException;
use App\Importer\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * app:prices:import <file>
 *
 * Идемпотентный upsert справочника цен. Служебные сообщения — в stderr;
 * stdout остаётся чистым. Выход: 0 — успех, 1 — некорректные данные.
 */
#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт (upsert) справочника: провайдеры, модели, история цен',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу истории цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $this->importer->importFile($file);
        } catch (ImportException|\DomainException $e) {
            $output->getErrorOutput()->writeln('ОШИБКА ИМПОРТА: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln('OK: справочник импортирован ('.$file.')');

        return Command::SUCCESS;
    }
}
