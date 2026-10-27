<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Apply repriced price lines and recalculate affected events')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceEngine $engine)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the price correction JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        try {
            $result = $this->engine->reprice($file);
            $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
            $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));
            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());
            return Command::FAILURE;
        }
    }
}
