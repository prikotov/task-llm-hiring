<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\BillingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly BillingService $billing,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:rate')
            ->setDescription('Тарифицирует все ещё не рассчитанные события');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billing->ratePending();
        $rated = $result['rated'];
        $unrated = $result['unrated'];

        foreach ($unrated as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProvider(),
                $event->getProviderRequestId(),
                $event->getTs()->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, count($unrated)));

        return Command::SUCCESS;
    }
}
