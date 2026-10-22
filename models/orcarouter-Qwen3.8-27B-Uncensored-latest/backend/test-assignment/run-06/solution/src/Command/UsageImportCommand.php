<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Service\UsageImporter;
use App\Support\ConsoleWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования из JSONL (дубли по (provider, provider_request_id) игнорируются)',
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
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий (одно событие на строку)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->importer->import((string) $input->getArgument('file'));
        } catch (DomainException $e) {
            ConsoleWriter::note($output, 'Ошибка: '.$e->getMessage());

            return 1;
        }

        ConsoleWriter::note($output, sprintf(
            'ОК: импортировано событий: %d, пропущено дублей: %d',
            $result['imported'],
            $result['skippedDuplicates'],
        ));

        // Код выхода 0 даже если все строки — дубли.
        return Command::SUCCESS;
    }
}
