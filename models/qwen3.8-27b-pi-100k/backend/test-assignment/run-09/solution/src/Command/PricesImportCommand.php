<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузка справочника цен: провайдеры, модели, строки цен (JSON, идемпотентный upsert).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        $json = $this->readFile($file, $output);
        if (null === $json) {
            return Command::FAILURE;
        }

        try {
            $this->importer->import($json);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка импорта цен: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln('Справочник цен: загрузка завершена.');

        return Command::SUCCESS;
    }

    private function readFile(string $file, OutputInterface $output): ?string
    {
        if (!\is_file($file) || !\is_readable($file)) {
            $output->getErrorOutput()->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return null;
        }

        $contents = \file_get_contents($file);
        if (false === $contents) {
            $output->getErrorOutput()->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return null;
        }

        return $contents;
    }
}
