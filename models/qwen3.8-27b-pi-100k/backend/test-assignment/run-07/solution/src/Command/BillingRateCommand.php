<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\TimestampFormatter;
use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать ещё не рассчитанные события (повторные попытки для unrated).',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly BillingService $service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $run = $this->service->rate();

        $stderr = $output->getErrorOutput();
        foreach ($run->unrated as $event) {
            $stderr->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProvider(),
                $event->getProviderRequestId(),
                TimestampFormatter::formatUtc($event->getTs()),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $run->rated, $run->leftUnrated));

        return Command::SUCCESS;
    }
}
