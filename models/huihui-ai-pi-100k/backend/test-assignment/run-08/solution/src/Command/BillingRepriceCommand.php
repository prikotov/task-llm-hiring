<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:billing:reprice', description: 'Retro-correction of price rows (bonus). Prints REPRICED_EVENTS / REPRICE_DELTA_USD.')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Corrections file (same format as prices history).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        $stderr = new SymfonyStyle($input, $output->getErrorOutput());

        try {
            $result = $this->reprice->apply($file);
        } catch (\App\Import\InputFormatException $e) {
            $stderr->error($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return Command::SUCCESS;
    }
}
