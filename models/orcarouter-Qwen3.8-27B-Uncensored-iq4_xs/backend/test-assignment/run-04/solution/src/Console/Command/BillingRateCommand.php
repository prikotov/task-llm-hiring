<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Application\Billing\EventRater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события (с повторными попытками для unrated).',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly EventRater $rater,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->ratePending();

        foreach ($result->unrated as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider(),
                $event->providerRequestId(),
                $event->ts()->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->ratedCount, count($result->unrated)));

        return Command::SUCCESS;
    }
}
