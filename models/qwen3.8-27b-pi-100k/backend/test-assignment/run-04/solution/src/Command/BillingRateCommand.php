<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарификация всех ещё не рассчитанных событий (включая повторные попытки unrated)')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RateService $rateService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rateService->rateAllPending();

        foreach ($result['unrated'] as $event) {
            $output->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->ts->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        return Command::SUCCESS;
    }
}
