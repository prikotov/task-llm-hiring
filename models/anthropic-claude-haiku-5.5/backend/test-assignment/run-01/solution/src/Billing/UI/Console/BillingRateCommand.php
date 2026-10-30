<?php

declare(strict_types=1);

namespace App\Billing\UI\Console;

use App\Billing\Application\Rating\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицировать все ещё не рассчитанные события')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RatingService $rating)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rating->ratePending();

        foreach ($result['unrated'] as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider(),
                $event->providerRequestId(),
                \App\Billing\Domain\Shared\Time::formatIso($event->ts()),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])));

        return Command::SUCCESS;
    }
}
