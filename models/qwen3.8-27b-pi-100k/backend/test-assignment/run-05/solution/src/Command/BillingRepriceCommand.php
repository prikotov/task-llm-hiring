<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepricingService;
use App\Shared\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цены: применить исправленные строки и пересчитать тарифицировавшиеся ими события')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepricingService $repricingService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        try {
            [$recounted, $delta] = $this->repricingService->reprice($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return 1;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $recounted));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $delta));

        return 0;
    }
}
