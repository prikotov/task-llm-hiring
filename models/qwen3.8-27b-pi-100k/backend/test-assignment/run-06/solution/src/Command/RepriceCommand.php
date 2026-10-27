<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\ImportInputException;
use App\Service\Billing\RepriceService;
use App\Service\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленные строки и пересчитать по ним рассчитанные события',
)]
final class RepriceCommand extends Command
{
    public function __construct(
        private readonly PricesImporter $pricesImporter,
        private readonly RepriceService $reprice,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $replacedRowIds = $this->pricesImporter->applyCorrections($file);
            $result = $this->reprice->repricingByRows($replacedRowIds);
        } catch (ImportInputException $e) {
            $output->writeln('reprice error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->write(sprintf("REPRICED_EVENTS=%d\n", $result['count']));
        $output->write(sprintf("REPRICE_DELTA_USD=%s\n", $result['deltaUsd']));

        return Command::SUCCESS;
    }
}
