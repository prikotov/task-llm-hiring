<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\PricesCatalogParser;
use App\Service\PriceCatalogImporter;
use App\Shared\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Idempotently loads the price catalog (providers, models, price rows) from a JSON file',
)]
final class AppPricesImportCommand extends Command
{
    public function __construct(private readonly PriceCatalogImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the price catalog JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $counts = $this->importer->importFile($file, PricesCatalogParser::MODE_IMPORT);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(\sprintf(
            'prices: %d provider(s), %d model(s), %d price row(s) upserted (file %s)',
            $counts['providers'],
            $counts['models'],
            $counts['prices'],
            $file,
        ));

        return Command::SUCCESS;
    }
}
