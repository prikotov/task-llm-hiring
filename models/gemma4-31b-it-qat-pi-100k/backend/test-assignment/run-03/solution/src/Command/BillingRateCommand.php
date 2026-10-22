<?php

namespace App\Command;

use App\Service\BillingManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
class BillingRateCommand extends Command
{
    public function __construct(private BillingManager $billingManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billingManager->rateEvents();
        
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])));
        
        foreach ($result['unrated'] as $unrated) {
            fwrite(STDERR, sprintf('UNRATED %s %s no_price\n', $unrated['id'], $unrated['ts']));
        }

        return Command::SUCCESS;
    }
}
