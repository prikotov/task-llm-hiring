<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Ingestion\PricesImportService;
use App\Domain\DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import the price reference (JSON) into the database.')]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesImportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, "Path to the JSON file (e.g. fixtures/prices.json)")
            ->setHelp('Idempotent upsert of providers, models and price rows.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $err = $output->getErrorOutput();

        try {
            $this->service->import($file);
        } catch (DomainException $e) {
            $err->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
