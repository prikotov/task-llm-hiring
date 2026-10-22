<?php

namespace App\Command;

use App\Service\BillingManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice')]
class BillingRepriceCommand extends Command
{
    public function __construct(private BillingManager $billingManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to correction prices file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        try {
            $result = $this->billingManager->reprice($file);
            $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['count']));
            $output->writeln(sprintf('REPRICE_DELTA_USD=%s', number_format((float)$result['delta'], 6, '.', '')));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage(), OutputInterface::VERBOSITY_VERBOSE);
            return Command::FAILURE;
        }
    }
}
