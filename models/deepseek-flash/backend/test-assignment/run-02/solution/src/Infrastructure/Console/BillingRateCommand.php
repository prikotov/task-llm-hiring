<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Billing\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицирует нерассчитанные события')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RatingService $ratingService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->ratingService->ratePending();
        $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        foreach ($result->unratedEvents as $event) {
            $errorOutput->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->providerCode(),
                $event->providerRequestId(),
                $event->occurredAt()->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unrated()));

        return Command::SUCCESS;
    }
}
