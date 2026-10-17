<?php

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
class RateCommand extends Command
{
    public function __construct(
        private BillingService $billingService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billingService->rateAll();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        foreach ($result['unratedEvents'] as $event) {
            $output->getErrorOutput()->writeln(sprintf('UNRATED %s %s no_price', $event['id'], $event['ts']));
        }

        return Command::SUCCESS;
    }
}
