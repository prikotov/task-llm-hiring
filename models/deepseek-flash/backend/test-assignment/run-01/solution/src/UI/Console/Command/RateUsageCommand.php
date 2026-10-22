<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Billing\RateUsageEvents;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all usage events that have no cost yet')]
final class RateUsageCommand extends AbstractCommand
{
    public function __construct(private readonly RateUsageEvents $service)
    {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->service->rate();

        foreach ($result->unratedEvents as $event) {
            $this->writeError($output, sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->occurredAt->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unrated));

        return self::SUCCESS;
    }
}
