<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\ImportInputException;
use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загружает события использования из JSONL; дубли (provider + provider_request_id) игнорируются',
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
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->importer->import($file);
        } catch (ImportInputException $e) {
            $output->writeln('import error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf(
            'usage: inserted=%d duplicates_skipped=%d',
            $result['inserted'],
            $result['duplicates'],
        ));

        return Command::SUCCESS;
    }
}
