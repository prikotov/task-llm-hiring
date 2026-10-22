<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Pricing\PriceImportService;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import providers, models and price history')]
final class ImportPricesCommand extends AbstractCommand
{
    public function __construct(private readonly PriceImportService $service)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the price history JSON file');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->service->importFile((string) $input->getArgument('file'));
        } catch (InvalidArgumentException $exception) {
            $this->writeError($output, $exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
