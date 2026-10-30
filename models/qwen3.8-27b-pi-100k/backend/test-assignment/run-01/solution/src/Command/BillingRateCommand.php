<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\IsoTime;
use App\Domain\RatingRunner;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует все ещё не рассчитанные события (повторные попытки unrated включаются автоматически).',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingRunner $ratingRunner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $summary = $this->ratingRunner->rateAll();
        } catch (RuntimeException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return 1;
        }

        foreach ($summary->unrated as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->requestId,
                IsoTime::toUtcZString($event->ts),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $summary->ratedCount, $summary->unratedCount()));

        return 0;
    }
}
