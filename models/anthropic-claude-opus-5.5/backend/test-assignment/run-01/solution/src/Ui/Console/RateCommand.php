<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Billing\Application\RatingService;
use App\Shared\UtcTime;
use App\Usage\Domain\UsageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Тарифицировать нерассчитанные события')]
final class RateCommand extends Command
{
    public function __construct(private readonly RatingService $rating)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $r = $this->rating->rateAll(static function (UsageEvent $e) use ($err): void {
            $err->writeln(sprintf('UNRATED %s/%s %s no_price', $e->providerCode(), $e->providerRequestId(), UtcTime::format($e->ts())), OutputInterface::OUTPUT_RAW);
        });
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $r['rated'], $r['unrated']), OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
    }
}
