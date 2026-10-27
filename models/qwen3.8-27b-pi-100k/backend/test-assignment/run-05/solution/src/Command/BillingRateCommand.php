<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарификация ещё не рассчитанных событий (включая повторные попытки unrated)')]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RatingService $ratingService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$rated, $unrated] = $this->ratingService->rateAll(
            static function (string $provider, string $requestId, \DateTimeImmutable $ts) use ($output): void {
                $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $provider, $requestId, $ts->format('Y-m-d\TH:i:s\Z')));
            }
        );

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));

        return 0;
    }
}
