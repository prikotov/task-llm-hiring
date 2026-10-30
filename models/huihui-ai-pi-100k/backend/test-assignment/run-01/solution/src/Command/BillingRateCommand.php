<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация всех ещё не рассчитанных событий (повторная попытка для unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RateService $rateService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOut = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $result = $this->rateService->ratePendingEvents();

            // Сводка — строго одна строка в stdout.
            $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->ratedCount, $result->unratedCount()));

            foreach ($result->unratedLines as $line) {
                $errorOut->writeln($line);
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $errorOut->writeln('Ошибка тарификации: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
