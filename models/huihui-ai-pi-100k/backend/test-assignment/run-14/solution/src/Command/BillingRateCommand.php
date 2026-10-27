<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\Rater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарификация ещё не рассчитанных событий (включая повторные попытки unrated). Вывод: RATED=<n> UNRATED=<m>.')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly Rater $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->run();

        $stderr = $output->getErrorOutput();
        foreach ($result['unratedLines'] as $line) {
            $stderr->writeln($line);
        }

        $output->writeln(sprintf(
            'RATED=%d UNRATED=%d',
            $result['rated'],
            $result['unrated'],
        ));

        return self::SUCCESS;
    }
}
