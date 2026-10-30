<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate unrated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(private BillingService $billing) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billing->rateAll();
        $rated = $result['rated'];
        $unrated = $result['unrated'];
        $output->writeln("RATED=$rated UNRATED=$unrated");
        foreach ($result['details'] as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProvider(),
                $event->getProviderRequestId(),
                $event->getTs()->format('c')
            ));
        }
        return Command::SUCCESS;
    }
}
