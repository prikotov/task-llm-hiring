<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use App\Application\Prices\PriceFileParser;
use App\Exception\InvalidPricesFileException;
use App\Exception\InvalidRepriceFileException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Retroactively apply a price correction and recompute already-rated events.',
)]
final class RepriceCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly RepriceService $reprice,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the price-correction JSON file (same format as the price history)')
            ->setHelp(
                'Upserts provider/model/price rows from the file (providers by code, price rows by '
                .'(provider, model, valid_from); every referenced model and provider must already exist '
                .'in the loaded reference data). Then every already-rated event that was charged with a '
                .'touched price row is recomputed with the new rates (and the current off-peak policy); '
                .'each recomputation is audited in the price_corrections journal (old and new cost). '
                .'Unrated events are left pending for the next app:billing:rate. '
                .'Exit code: 0 on success, 1 on invalid file or unknown provider/model.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errors = $output->getErrorOutput();

        $path = (string) $input->getArgument('file');

        if (!is_file($path)) {
            $errors->writeln(sprintf('<error>file not found: %s</error>', $path));

            return Command::FAILURE;
        }

        $json = @file_get_contents($path);
        if ($json === false) {
            $errors->writeln(sprintf('<error>cannot read file: %s</error>', $path));

            return Command::FAILURE;
        }

        try {
            $spec = $this->parser->parse($json);
        } catch (InvalidPricesFileException $e) {
            $errors->writeln(sprintf('<error>invalid price-correction file: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        try {
            $result = $this->reprice->reprice($spec);
        } catch (InvalidRepriceFileException $e) {
            $errors->writeln(sprintf('<error>reprice failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $errors->writeln(sprintf('<error>reprice failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', \App\Domain\Shared\Decimal::format6($result['delta_usd'])));

        return Command::SUCCESS;
    }
}
