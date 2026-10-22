<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BillingCommand extends Command
{
    public function __construct(protected Billing $billing)
    {
        parent::__construct();
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $error = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $this->runBilling($input, $output, $error);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $error->writeln('Ошибка: ' . $e->getMessage(), OutputInterface::OUTPUT_RAW);

            return Command::FAILURE;
        }
    }

    abstract protected function runBilling(InputInterface $input, OutputInterface $output, OutputInterface $error): void;
}
