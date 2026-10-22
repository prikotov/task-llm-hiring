<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Money\Money;
use App\Import\PricesCatalogParser;
use App\Service\PriceCatalogImporter;
use App\Service\Repricer;
use App\Shared\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Applies corrected price rows and re-prices the events charged with them (bonus)',
)]
final class AppBillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PriceCatalogImporter $catalogImporter,
        private readonly PricesCatalogParser $parser,
        private readonly Repricer $repricer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the price correction JSON file (same format as the catalog)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $decoded = PriceCatalogImporter::readJsonFile($file);
            $catalog = $this->parser->parse($decoded, PricesCatalogParser::MODE_REPRICE);
            $result = $this->repricer->reprice($catalog->prices);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(\sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(\sprintf('REPRICE_DELTA_USD=%s', Money::format6($result['delta'])));

        return Command::SUCCESS;
    }
}
