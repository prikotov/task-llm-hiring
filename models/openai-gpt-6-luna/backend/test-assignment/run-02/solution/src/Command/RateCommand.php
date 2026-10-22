<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BillingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицировать новые события использования')]
final class RateCommand extends Command
{
    public function __construct(private readonly BillingService $billing)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->billing->rate();
            foreach ($result['unrated'] as $event) {
                $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $event['provider'], $event['request_id'], $event['ts']));
            }
            $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])));
            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->getErrorOutput()->writeln('ERROR: '.$exception->getMessage());
            return Command::FAILURE;
        }
    }
}
