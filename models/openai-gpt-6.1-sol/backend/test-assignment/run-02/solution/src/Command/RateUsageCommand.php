<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RateUsage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Рассчитать все ещё не тарифицированные события.')]
final class RateUsageCommand extends BillingCommand
{
    public function __construct(private RateUsage $rate)
    {
        parent::__construct();
    }

    protected function perform(InputInterface $input, OutputInterface $output): void
    {
        $result = $this->rate->run();
        foreach ($result['warnings'] as $warning) {
            $this->stderr($output)->writeln($warning, OutputInterface::OUTPUT_RAW);
        }
        $output->writeln('RATED=' . $result['rated'] . ' UNRATED=' . count($result['warnings']), OutputInterface::OUTPUT_RAW);
    }
}
