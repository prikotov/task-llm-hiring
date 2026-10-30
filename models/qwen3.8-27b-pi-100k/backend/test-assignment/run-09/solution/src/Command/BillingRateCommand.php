<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\IsoDateTime;
use App\Service\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация всех ещё не рассчитанных событий (повторные попытки для ранее unrated).'
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RateService $rateService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            [$rated, $unrated] = $this->rateService->rate();
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка тарификации: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $unratedCount = \count($unrated);

        // Сводка — строго в stdout, одной строкой.
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unratedCount));

        // Служебные строки про unrated — в stderr.
        foreach ($unrated as $event) {
            $provider = $event->getProvider()->getCode();
            $reqId = $event->getProviderRequestId();
            $ts = IsoDateTime::formatUtc($event->getTs());
            $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $provider, $reqId, $ts));
        }

        return Command::SUCCESS;
    }
}
