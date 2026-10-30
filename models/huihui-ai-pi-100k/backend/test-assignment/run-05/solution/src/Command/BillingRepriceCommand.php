<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use App\Application\Exception\InputException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленные строки и пересчитать затронутые события',
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
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->repriceService->reprice($file);
        } catch (InputException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . ltrim($e->getMessage(), ' '));
            return Command::FAILURE;
        }

        // В stdout — ровно две строки итого.
        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->repricedEvents));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaUsd));
        return Command::SUCCESS;
    }
}
