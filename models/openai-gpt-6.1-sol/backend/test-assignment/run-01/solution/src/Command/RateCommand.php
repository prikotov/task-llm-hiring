<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Рассчитать ещё не тарифицированные события')]
final class RateCommand extends BillingCommand
{
    protected function runBilling(InputInterface $input, OutputInterface $output, OutputInterface $error): void
    {
        $result = $this->billing->rate();
        foreach ($result['unrated'] as $message) {
            $error->writeln($message, OutputInterface::OUTPUT_RAW);
        }
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])), OutputInterface::OUTPUT_RAW);
    }
}
