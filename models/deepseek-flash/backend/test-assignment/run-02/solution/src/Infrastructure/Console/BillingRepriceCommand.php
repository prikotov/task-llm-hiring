<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Billing\RepriceService;
use App\Application\Import\ImportException;
use App\Application\Import\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Применяет ретро-коррекцию цен')]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PricesImporter $pricesImporter,
        private readonly RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $changedPrices = $this->pricesImporter->importCorrections((string) $input->getArgument('file'));
            $result = $this->repriceService->reprice($changedPrices);
        } catch (ImportException $exception) {
            $this->error($output, $exception->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->events));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaUsd));

        return Command::SUCCESS;
    }

    private function error(OutputInterface $output, string $message): void
    {
        $target = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $target->writeln('ERROR: '.$message);
    }
}
