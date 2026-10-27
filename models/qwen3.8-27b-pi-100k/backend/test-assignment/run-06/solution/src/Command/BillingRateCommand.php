<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Billing\BillingRater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует все ещё не рассчитанные события (вкл. повторную попытку ранее unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly BillingRater $rater,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rater->ratePending();

        foreach ($result['unrated'] as $u) {
            $output->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $u['provider'],
                $u['request_id'],
                $u['ts'],
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        return Command::SUCCESS;
    }
}
