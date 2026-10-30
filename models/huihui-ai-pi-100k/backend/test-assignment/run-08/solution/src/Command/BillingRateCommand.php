<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all not-yet-rated usage events (idempotent; retries unrated).')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly BillingService $billing)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billing->rateAllPending();

        // Строка в stdout строго в форме "RATED=<n> UNRATED=<m>".
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        // Для каждого unrated-события этого запуска — строка в stderr.
        foreach ($result['unratedEvents'] as $u) {
            $tsIso = (new \DateTimeImmutable($u['ts']))->format('Y-m-d\TH:i:s\Z');
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $u['provider'],
                $u['requestId'],
                $tsIso,
            ));
        }

        return Command::SUCCESS;
    }
}
