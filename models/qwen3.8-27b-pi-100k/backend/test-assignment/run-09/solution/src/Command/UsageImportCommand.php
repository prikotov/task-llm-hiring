<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузка событий использования (JSONL), дедупликация по (provider, provider_request_id).',
)]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями использования');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        if (!\is_file($file) || !\is_readable($file)) {
            $output->getErrorOutput()->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return Command::FAILURE;
        }

        $content = \file_get_contents($file);
        if (false === $content) {
            $output->getErrorOutput()->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return Command::FAILURE;
        }

        try {
            [$imported, $skipped] = $this->importer->import($content);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка импорта событий: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(sprintf('События: импортировано %d, дубли пропущено %d.', $imported, $skipped));

        return Command::SUCCESS;
    }
}
