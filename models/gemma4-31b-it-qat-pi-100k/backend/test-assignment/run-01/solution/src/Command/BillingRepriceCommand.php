<?php

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Correct pricing and recalculate events')]
class BillingRepriceCommand extends Command
{
    public function __construct(private BillingService $billingService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the correction JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("File not found: $filePath");
            return 1;
        }

        $data = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln("Invalid JSON: " . json_last_error_msg());
            return 1;
        }

        $result = $this->billingService->reprice($data);

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['count']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return 0;
    }
}
