<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Billing\Reprice\RepriceService;
use App\Domain\DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Apply corrected price rows and recompute affected events.')]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the corrected price JSON (same shape as app:prices:import input).')
            ->setHelp('Idempotent. Providers and models must already exist in the DB.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $err = $output->getErrorOutput();

        try {
            $result = $this->service->reprice($file);
        } catch (DomainException $e) {
            $err->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('REPRICED_EVENTS=' . $result['events']);
        $delta = $result['delta'];
        $output->writeln('REPRICE_DELTA_USD=' . $delta->toSixDecimalsTruncated());

        return Command::SUCCESS;
    }
}
