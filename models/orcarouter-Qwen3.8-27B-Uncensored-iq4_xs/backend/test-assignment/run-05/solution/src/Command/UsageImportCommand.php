<?php

declare(strict_types=1);

namespace App\Command;

use App\Importer\ImportException;
use App\Importer\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * app:usage:import <file>
 *
 * Импорт событий использования (JSONL); дубли по (provider,
 * provider_request_id) игнорируются, код 0 даже если все строки — дубли.
 */
#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL, дедупликация по eстественному ключу)',
)]
final class UsageImportCommand extends Command
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

        try {
            $this->importer->importFile($file);
        } catch (ImportException|\DomainException $e) {
            $output->getErrorOutput()->writeln('ОШИБКА ИМПОРТА: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln('OK: события импортированы ('.$file.')');

        return Command::SUCCESS;
    }
}
