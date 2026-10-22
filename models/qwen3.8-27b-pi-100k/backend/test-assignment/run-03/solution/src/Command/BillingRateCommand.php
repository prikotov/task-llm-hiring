<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateService;
use App\Shared\ConsoleStreams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует все ещё не рассчитанные события (повторяет попытку для unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RateService $rateService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $summary = $this->rateService->rate();
        } catch (\Throwable $e) {
            ConsoleStreams::stderr($output)->writeln('app:billing:rate: '.$e->getMessage());

            return 1;
        }

        $unrated = $summary->unratedEvents;
        foreach ($unrated as $event) {
            ConsoleStreams::stderr($output)->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->providerCode(),
                $event->providerRequestId(),
                $event->tsRaw(),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $summary->rated, count($unrated)));

        return 0;
    }
}
