<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all pending and previously unrated usage events')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RateEngine $engine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->engine->rateAll();

        // Stderr: unrated events
        $err = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://stderr', 'w'));
        foreach ($result['unrated_events'] as $evt) {
            $err->writeln(sprintf('UNRATED %s/%s %s no_price', $evt['provider'], $evt['id'], $evt['ts']));
        }

        // Stdout: summary
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        return Command::SUCCESS;
    }
}
