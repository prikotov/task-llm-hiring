<?php

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice')]
class RepriceCommand extends Command
{
    public function __construct(
        private BillingService $billingService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the price correction file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln("File not found: $file");
            return Command::FAILURE;
        }

        $result = $this->billingService->reprice($file);

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['count']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return Command::SUCCESS;
    }
}
