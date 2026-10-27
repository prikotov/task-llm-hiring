<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\BillingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарификация pending/unrated событий')]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly BillingService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->service->rate();
        $this->em->flush();

        // unrated события — в stderr
        foreach ($result['unrated_events'] as $event) {
            $err = $output->getErrorOutput();
            $ts = $event->getTs()->format('c');
            $err->writeln("UNRATED {$event->getProviderCode()}/{$event->getProviderRequestId()} {$ts} no_price");
        }

        // сводка — в stdout
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        return 0;
    }
}
