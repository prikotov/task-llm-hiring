<?php

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate uncalculated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(private BillingService $billingService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billingService->rateUnratedEvents();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        foreach ($result['unrated_events'] as $event) {
            // stderr: UNRATED <provider>/<provider_request_id> <ts> no_price
            // For now we use $output->getErrorOutput() which is stderr
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->providerCode,
                $event->providerRequestId,
                $event->ts->format(\DateTimeInterface::ATOM)
            ));
        }

        return 0;
    }
}
