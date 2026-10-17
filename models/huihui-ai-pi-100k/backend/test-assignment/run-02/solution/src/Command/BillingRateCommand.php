<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Тарификация всех нерассчитанных событий (включая повторные попытки unrated).
 *
 * stdout: ровно одна строка «RATED=<n> UNRATED=<m>»;
 * stderr: для каждого unrated-события — «UNRATED <provider>/<id> <ts> no_price».
 */
#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует нерассчитанные события (повторные попытки для unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingService $ratingService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(<<<'HELP'
            Событие тарифицируется строкой цен своей модели с наибольшим
            valid_from <= ts (valid_from включительно). Если такой строки нет —
            событие остаётся unrated (повторно будет пробоваться на каждом запуске;
            цены могли подтянуться позже).

            stdout: RATED=<n> UNRATED=<m>
            stderr: UNRATED <provider>/<provider_request_id> <ts> no_price
            HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->ratingService->rateAll();
        } catch (\RuntimeException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput = $output->getErrorOutput();
        foreach ($result['unrated'] as $event) {
            $errorOutput->writeln(RatingService::unratedLine($event));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        return Command::SUCCESS;
    }
}
