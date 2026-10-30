<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Domain\Billing\BillingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all still-unrated usage events.')]
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
        $stderr = $output->getErrorOutput();
        try {
            $result = $this->em->wrapInTransaction(function () {
                return $this->service->rateAllUnrated();
            });
        } catch (\Throwable $e) {
            $stderr->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $stdout = $output; // по ТЗ: stdout — сводка одной строкой, stderr — unrated
        $stdout->writeln(sprintf(
            'RATED=%d UNRATED=%d',
            $result->ratedCount,
            count($result->unrated)
        ));

        foreach ($result->unrated as $event) {
            $stderr->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->ts->format('c'),
            ));
        }

        return Command::SUCCESS;
    }
}
