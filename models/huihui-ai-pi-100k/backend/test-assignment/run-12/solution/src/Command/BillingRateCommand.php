<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Engine\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует ещё не оценённые события (повторные попытки для unrated).',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RateService $rateService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rateService->rateAll();
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln('ERROR: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $rated = count($result['rated']);
        $unrated = count($result['unrated']);

        // В stdout — строго одна строка.
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));

        // В stderr — по одной строке на unrated-событие.
        foreach ($result['unrated'] as $u) {
            $output->getErrorOutput()->writeln($u['reason'] ?? 'no price');
        }

        return Command::SUCCESS;
    }
}
