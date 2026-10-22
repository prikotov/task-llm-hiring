<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RaterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * app:billing:rate
 *
 * Тарификарует все ещё не рассчитанные события (включая повторные попытки
 * для ранее unrated). stdout: строго «RATED=<n> UNRATED=<m>»;
 * stderr: по строке «UNRATED <provider>/<id> <ts> no_price» на unrated.
 */
#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация не рассчитанных событий (повторные попытки для unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RaterService $rater,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->ratePending();

        foreach ($result->unrated as $item) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $item['provider'],
                $item['providerRequestId'],
                $item['ts']->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unratedCount()));

        return Command::SUCCESS;
    }
}
