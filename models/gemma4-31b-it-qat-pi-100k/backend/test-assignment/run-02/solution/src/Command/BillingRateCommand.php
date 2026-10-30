<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingRatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rates unrated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(
        private BillingRatingService $billingRatingService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billingRatingService->rateEvents();
        
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        foreach ($result['unrated_events'] as $event) {
            $stderr = $output; // In Symfony Command, we might need a different way for stderr
            // Actually, output->getErrorOutput() is for stderr.
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProvider()->getCode(),
                $event->getProviderRequestId(),
                $event->getTs()->format('Y-m-d\TH:i:s\Z')
            ));
        }

        return Command::SUCCESS;
    }
}
