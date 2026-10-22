<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицировать ещё не рассчитанные события')]
final class AppBillingRateCommand extends Command
{
    public function __construct(private readonly RatingService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->service->rateUnrated();
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln('Ошибка тарификации: '.$e->getMessage());
            return Command::FAILURE;
        }

        /** В stdout — только сводка; unrated-детали — в stderr. */
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        foreach ($result['unrated'] as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider,
                $event->providerRequestId,
                $event->ts->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        return Command::SUCCESS;
    }
}
