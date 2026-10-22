<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingRater;
use App\Shared\IsoUtc;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Rates all not-yet-rated usage events (retries previously unrated ones)',
)]
final class AppBillingRateCommand extends Command
{
    public function __construct(private readonly BillingRater $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->rate();

        foreach ($result['unrated'] as $event) {
            $output->getErrorOutput()->writeln(\sprintf(
                'UNRATED %s/%s %s no_price',
                $event['provider'],
                $event['request_id'],
                IsoUtc::format($event['ts']),
            ));
        }

        $output->writeln(\sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        return Command::SUCCESS;
    }
}
