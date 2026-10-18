<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateResult;
use App\Application\Billing\Rater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует все ещё не рассчитанные события',
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly Rater $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->doRate();
        if ($result instanceof Throwable) {
            $output->getErrorOutput()->writeln('Ошибка тарификации: '.$result->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unrated));

        // Служебные детали — только в stderr, stdout отдаёт ровно одну сводную строку.
        $stderr = $output->getErrorOutput();
        foreach ($result->unratedEvents as $event) {
            $stderr->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->tsUtc()->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        return Command::SUCCESS;
    }

    private function doRate(): RateResult|Throwable
    {
        try {
            return $this->rater->rateAll();
        } catch (Throwable $e) {
            return $e;
        }
    }
}
