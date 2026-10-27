<?php

declare(strict_types=1);

namespace App\Billing\Command;

use App\Billing\Service\RateEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Рассчитать все ещё не рассчитанные события (повторные попытки unrated)',
)]
final class BillingRateCommand extends Command
{
    public const TS_FORMAT = 'Y-m-d\TH:i:s\Z';

    public function __construct(private readonly RateEngine $engine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->engine->ratePending();

        // Служебные строки об unrated — в stderr (stdout зарезервирован за сводкой).
        foreach ($result->unrated as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->ts->format(self::TS_FORMAT),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, \count($result->unrated)));

        return 0;
    }
}
