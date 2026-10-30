<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ApplicationServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all pending and previously-unrated usage events.')]
final class BillingRateCommand extends Command
{
    protected static string $defaultName = 'app:billing:rate';

    public function __construct(
        private readonly ApplicationServices $app,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stderr = $output->getErrorOutput();

        $result = $this->app->rate();

        // Per-event stderr lines for unrated events (this run)
        foreach ($result->stderrLines as $line) {
            $stderr->writeln($line);
        }

        // Single-line stdout summary
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->ratedCount, $result->unratedCount));

        return Command::SUCCESS;
    }
}
