<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Вывести расходы в CSV')]
final class ReportCommand extends BillingCommand
{
    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, включительно (UTC)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, исключительно (UTC)');
    }

    protected function runBilling(InputInterface $input, OutputInterface $output, OutputInterface $error): void
    {
        $rows = $this->billing->report($input->getOption('from'), $input->getOption('to'));
        array_unshift($rows, ['user', 'model', 'tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write', 'cost_usd']);
        $stream = fopen('php://temp', 'w+');
        try {
            foreach ($rows as $row) {
                fputcsv($stream, $row, ',', '"', '', "\n");
            }
            rewind($stream);
            $output->write(stream_get_contents($stream), false, OutputInterface::OUTPUT_RAW);
        } finally {
            fclose($stream);
        }
    }
}
