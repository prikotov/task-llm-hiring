<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Загрузить справочник и историю цен')]
final class PricesImportCommand extends BillingCommand
{
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл цен');
    }

    protected function runBilling(InputInterface $input, OutputInterface $output, OutputInterface $error): void
    {
        $this->billing->importPrices($input->getArgument('file'));
    }
}
