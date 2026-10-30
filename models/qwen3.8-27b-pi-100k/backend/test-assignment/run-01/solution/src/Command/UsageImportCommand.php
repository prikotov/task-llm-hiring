<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\ImportException;
use App\Import\UsageImporter;
use App\Import\UsageParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загружает события использования (JSONL). Дубли по (provider, provider_request_id) игнорируются.',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageParser $usageParser,
        private readonly UsageImporter $usageImporter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий использования');
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
            $lines = $this->usageParser->parseFile($raw);
            $counters = $this->usageImporter->import($lines);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return 1;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'Принято событий: %d, отклонено дублей: %d.',
            $counters['inserted'],
            $counters['duplicates'],
        ));

        return 0;
    }
}
