<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Application\Usage\UsageEventImporter;
use App\Exception\DataImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузить события использования (JSONL; дубли по (provider, provider_request_id) игнорируются).',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageEventImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Pуть к JSONL-файлу событий использования (формат — см. TASK.md / fixtures/usage.jsonl).',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $path = (string) $input->getArgument('file');
            $this->importer->import($path);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка импорта: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
