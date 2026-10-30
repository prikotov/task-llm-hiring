<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RatingService;
use App\Support\ConsoleWriter;
use App\Support\IsoTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация всех ещё не рассчитанных событий (включая повторные попытки unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingService $rating,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rating->ratePending();

        // unrated-события — служебные строки в stderr.
        foreach ($result['unrated'] as $event) {
            ConsoleWriter::note($output, sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                IsoTime::formatUtc($event->ts),
            ));
        }

        // Сводка — строго в stdout.
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])));

        return Command::SUCCESS;
    }
}
