<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузить события использования (JSONL) с дедупликацией по (provider, provider_request_id)',
)]
final class UsageImportCommand extends BaseCommand
{
    public function __construct(
        private readonly UsageImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        return $this->withErrorHandling($input, $output, function () use ($file, $input, $output): int {
            $result = $this->importer->import($file);

            // Частичная ошибка: по каждой некорректной строке — отдельная запись в stderr.
            foreach ($result['errors'] as $error) {
                $output->getErrorOutput()->writeln($error);
            }
            $output->getErrorOutput()->writeln(
                sprintf('Принято: %d, Ошибки: %d', $result['inserted'], \count($result['errors'])),
            );

            // Любая некорректная строка JSONL — ошибка (код 1).
            // Все строки — дубли и ни одной некорректной — код 0.
            return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
        });
    }
}
