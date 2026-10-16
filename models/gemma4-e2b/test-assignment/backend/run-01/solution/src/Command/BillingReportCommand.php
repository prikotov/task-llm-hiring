<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\BillingService;

class BillingReportCommand extends Command
{
    protected static $defaultName = 'app:billing:report';

    public function __construct(private BillingService $billingService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start time (ISO-8601 UTC)', null)
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'End time (ISO-8601 UTC)', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        try {
            $this->billingService->generateReport($from, $to, $output);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error during report generation: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}
