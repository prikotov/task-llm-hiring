<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RepriceService;
use App\Import\PriceDoc;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: upsert исправленных строк и пересчёт затронутых событий',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен (формат — как для app:prices:import; секции providers/models могут отсутствовать)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOut = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $doc = PriceDoc::fromFile((string) $input->getArgument('file'));
            $result = $this->repriceService->reprice($doc);

            // В stdout — ровно две строки.
            $output->writeln('REPRICED_EVENTS=' . $result->events);
            $output->writeln('REPRICE_DELTA_USD=' . $result->delta->format(6));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $errorOut->writeln('Ошибка ретро-коррекции цены: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
