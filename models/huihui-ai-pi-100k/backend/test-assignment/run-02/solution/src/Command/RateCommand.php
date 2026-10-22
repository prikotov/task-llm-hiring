<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Rate pending usage events (idempotent; every run retries unrated events).',
)]
final class RateCommand extends Command
{
    public function __construct(
        private readonly RatingService $rater,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(
            'For every pending event, picks the price row of the event model with the greatest '
            .'`valid_from <= event ts` (no row yet => event stays unrated). '
            .'Cost is computed with exact decimal math, rounded exactly once (HALF_UP, 6 places). '
            .'Each (provider, request) is charged at most once (dedup is done at import). '
            .'Every run retries all still-unrated events, so a late price import unblocks them. '
            .'Exit code: 0 (even when some events stay unrated — they are reported), 1 on error.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errors = $output->getErrorOutput();

        try {
            $result = $this->rater->rateAll();
        } catch (\Throwable $e) {
            $errors->writeln(sprintf('<error>rating failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        foreach ($result->unrated as $event) {
            $errors->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->ts->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->ratedCount, count($result->unrated)));

        return Command::SUCCESS;
    }
}
