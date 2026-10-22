<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Применить исправленные цены и пересчитать затронутые события')]
final class RepriceCommand extends Command
{
    public function __construct(private readonly BillingService $billing)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными ценами');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->billing->reprice((string) $input->getArgument('file'));
            $output->writeln('REPRICED_EVENTS='.$result['count']);
            $output->writeln('REPRICE_DELTA_USD='.$result['delta']);
            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->getErrorOutput()->writeln('ERROR: '.$exception->getMessage());
            return Command::FAILURE;
        }
    }
}
