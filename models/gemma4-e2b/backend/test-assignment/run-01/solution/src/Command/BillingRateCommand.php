<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\BillingService;

class BillingRateCommand extends Command
{
    protected static $defaultName = 'app:billing:rate';

    public function __construct(private BillingService $billingService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Rates all unrated usage events.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->billingService->rateAllEvents($output);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error during rating: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}
