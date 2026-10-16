<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RatingService;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация ещё не рассчитанных событий (повторяет unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RatingService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $res = $this->service->ratePending();
        } catch (DbalException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());

            return Command::FAILURE;
        }

        foreach ($res['unrated'] as $u) {
            $output->getErrorOutput()->writeln(
                sprintf('UNRATED %s/%s %s no_price', $u['provider'], $u['id'], $u['ts'])
            );
        }

        // Строгая сводка в stdout одной строкой.
        $output->write(sprintf("RATED=%d UNRATED=%d\n", $res['rated'], \count($res['unrated'])));

        return Command::SUCCESS;
    }
}
