<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Rating\RateUsageEvents;
use App\Infrastructure\Doctrine\Timestamp;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события',
)]
final class RateCommand extends Command
{
    public function __construct(private readonly RateUsageEvents $rate)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stderr = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        try {
            $result = ($this->rate)();
        } catch (\Throwable $e) {
            $stderr->writeln(sprintf('<error>Ошибка тарификации: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        foreach ($result['unratedEvents'] as $event) {
            $stderr->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->providerCode(),
                $event->providerRequestId(),
                Timestamp::iso($event->occurredAt()),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        return Command::SUCCESS;
    }
}
