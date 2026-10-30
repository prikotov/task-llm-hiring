<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования из JSONL (дедупликация по provider + provider_request_id)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImporter $importer,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий (формат — как в fixtures/usage.jsonl)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOut = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $result = $this->importer->importFile((string) $input->getArgument('file'));

            $errorOut->writeln(sprintf(
                'События импортированы: новых — %d, пропущено дубликатов — %d.',
                $result['imported'],
                $result['duplicates'],
            ));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $errorOut->writeln('Ошибка импорта событий: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
