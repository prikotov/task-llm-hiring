<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\InputException;
use App\Application\Import\UsageEventImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL-файл, дубли игнорируются)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageEventImporter $importer,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->importer->import($file);
        } catch (InputException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . ltrim($e->getMessage(), ' '));
            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'OK: принято событий %d, дубли по (provider, provider_request_id) пропущено %d',
            $result->inserted,
            $result->duplicated(),
        ));
        return Command::SUCCESS;
    }
}
