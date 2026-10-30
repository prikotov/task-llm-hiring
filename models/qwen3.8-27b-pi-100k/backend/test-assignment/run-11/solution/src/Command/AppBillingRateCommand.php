<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Service\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Рассчитать стоимость событий (тарификация pending/retryable)',
)]
final class AppBillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingService $service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output->getErrorOutput();
        try {
            $stats = $this->service->rate();
        } catch (\Throwable $e) {
            $err->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
        foreach ($stats['unratedLines'] as $line) {
            $err->writeln($line);
        }
        // stdout по ТЗ: RATED=<n> UNRATED=<m>
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $stats['rated'], $stats['unrated']));
        return Command::SUCCESS;
    }
}
