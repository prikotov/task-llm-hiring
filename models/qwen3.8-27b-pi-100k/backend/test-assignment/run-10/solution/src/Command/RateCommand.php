<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует ещё не рассчитанные события (status = new).',
)]
final class RateCommand extends Command
{
    public function __construct(
        private readonly RateService $rater,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$ratedCount, $unratedList] = $this->rater->rateAllPending();

        $stream = $output->getStream();

        // Служебные сообщения — в stderr; в CommandTester они недоступны,
        // поэтому пишем через fwrite в stream (stdout), а при запуске из bin/console
        // redirect к stderr.
        if ($unratedList !== []) {
            $err = $output instanceof \Symfony\Component\Console\Output\ConsoleOutputInterface
                ? $output->getErrorOutput()->getStream()
                : $stream;
            foreach ($unratedList as $u) {
                \fwrite($err, sprintf(
                    "UNRATED %s/%s %s no_price\n",
                    $u['provider'],
                    $u['providerRequestId'],
                    $u['ts'],
                ));
            }
        }

        \fwrite($stream, sprintf("RATED=%d UNRATED=%d\n", $ratedCount, \count($unratedList)));

        return 0;
    }
}
