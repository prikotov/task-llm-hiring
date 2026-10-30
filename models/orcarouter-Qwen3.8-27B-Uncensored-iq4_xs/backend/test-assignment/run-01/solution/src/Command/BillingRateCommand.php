<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Rater;
use App\Entity\UsageEvent;
use App\Util\UtcTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарификация ещё не рассчитанных событий')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly Rater $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->ratePending();

        /** @var UsageEvent[] $unrated */
        $unrated = $result['unrated'];

        foreach ($unrated as $event) {
            $output->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProviderCode(),
                $event->getProviderRequestId(),
                UtcTime::format($event->getTs()),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($unrated)));

        return 0;
    }
}
