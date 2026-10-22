<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загрузить события без повторного приёма дублей')]
final class UsageImportCommand extends BillingCommand
{
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий');
    }

    protected function runBilling(InputInterface $input, OutputInterface $output, OutputInterface $error): void
    {
        $this->billing->importUsage($input->getArgument('file'));
    }
}
