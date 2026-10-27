<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицирует все ещё не рассчитанные события (с повторными попытками unrated)')]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RateService $rate,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rate->rateAll();
        } catch (\Throwable $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        foreach ($result['unratedLines'] as $line) {
            CommandSupport::errOut($output)->writeln($line);
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        return 0;
    }
}
