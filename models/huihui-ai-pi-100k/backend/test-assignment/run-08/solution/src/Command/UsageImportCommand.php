<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageIngestService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:usage:import', description: 'Load usage events (idempotent, natural-key dedupe).')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageIngestService $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSONL usage events file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        $stderr = new SymfonyStyle($input, $output->getErrorOutput());

        try {
            $result = $this->importer->import($file);
        } catch (\App\Import\InputFormatException $e) {
            $stderr->error($e->getMessage());

            return Command::FAILURE;
        }

        $stderr->writeln(sprintf(
            'usage:import — rows inserted=%d duplicates=%d',
            $result['inserted'],
            $result['duplicates'],
        ));

        return Command::SUCCESS;
    }
}
