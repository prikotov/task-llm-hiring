<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\EventRater;
use App\Application\Exception\InputException;
use App\Infrastructure\Persistence\Entity\UsageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация всех ещё не рассчитанных событий',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly EventRater $rater,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rater->rate();
        } catch (InputException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . ltrim($e->getMessage(), ' '));
            return Command::FAILURE;
        }

        // Сводка — строго в stdout, служебные строки — в stderr.
        foreach ($result->unratedEvents as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->requestId,
                $event->ts->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unratedCount()));
        return Command::SUCCESS;
    }
}
