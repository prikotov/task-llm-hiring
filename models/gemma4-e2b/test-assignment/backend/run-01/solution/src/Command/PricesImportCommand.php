<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\PriceService;

class PricesImportCommand extends Command
{
    protected static $defaultName = 'app:prices:import';

    public function __construct(private PriceService $priceService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the prices JSON file.')
            ->setHelp('Loads providers, models, and prices into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>Error: File not found at {$filePath}</error>");
            return Command::FAILURE;
        }

        try {
            $this->priceService->importPrices($filePath);
            $output->writeln("<info>Prices imported successfully.</info>");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error during price import: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}
