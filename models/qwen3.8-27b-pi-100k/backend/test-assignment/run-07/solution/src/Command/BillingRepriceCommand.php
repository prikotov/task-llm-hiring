<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Retro-коррекция цены: применить исправленные строки цен и пересчитать посчитанные ими события.',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с исправленными строками цен (JSON, формат истории цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->service->reprice($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        }

        $output->writeln('REPRICED_EVENTS=' . $result->repricedEvents);
        $output->writeln('REPRICE_DELTA_USD=' . $result->deltaUsd);

        return Command::SUCCESS;
    }
}
