<?php

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rates unrated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(private BillingService $billingService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billingService->rateEvents();

        $output->writeln(sprintf("RATED=%d UNRATED=%d", $result['rated'], $result['unrated']));

        foreach ($result['unrated_details'] as $detail) {
            $output->getErrorOutput()->writeln($detail);
        }

        return Command::SUCCESS;
    }
}
