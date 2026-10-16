<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RateEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события (повторные попытки для unrated)',
)]
final class RateCommand extends Command
{
    public function __construct(private readonly RateEngine $engine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->engine->rateAll();
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка тарификации: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        foreach ($result['unrated'] as $u) {
            $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $u['provider'], $u['requestId'], $u['ts']));
        }

        $output->write(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated']))."\n");

        return Command::SUCCESS;
    }
}
