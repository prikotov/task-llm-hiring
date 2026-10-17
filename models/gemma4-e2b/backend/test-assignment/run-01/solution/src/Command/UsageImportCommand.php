<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\UsageService;

class UsageImportCommand extends Command
{
    protected static $defaultName = 'app:usage:import';

    public function __construct(private UsageService $usageService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file.')
            ->setHelp('Loads usage events, handling deduplication.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>Error: File not found at {$filePath}</error>");
            return Command::FAILURE;
        }

        try {
            $this->usageService->importUsage($filePath);
            $output->writeln("<info>Usage events imported successfully.</info>");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error during usage import: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}
