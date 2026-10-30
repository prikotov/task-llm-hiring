<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use App\Shared\ConsoleStreams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применяет исправленные строки и пересчитывает события, тарифицировавшиеся ими',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $repriceService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSON-файлу с исправленными строками цен');
        $this->setHelp(<<<'HELP'
Применяет исправленные строки цен (формат тот же, что у истории цен;
секции providers/models игнорируются) и пересчитывает события,
тарифицировавшиеся заменёнными строками.

В stdout ровно две строки:
  REPRICED_EVENTS=<n>
  REPRICE_DELTA_USD=<x.xxxxxx>
HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $summary = $this->repriceService->reprice($file);
        } catch (\Throwable $e) {
            ConsoleStreams::stderr($output)->writeln('app:billing:reprice: '.$e->getMessage());

            return 1;
        }

        $output->writeln('REPRICED_EVENTS='.$summary->repricedEvents);
        $output->writeln('REPRICE_DELTA_USD='.$summary->deltaUsd);

        return 0;
    }
}
