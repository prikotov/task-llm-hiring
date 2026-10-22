<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Domain\Pricing\BillingService;
use App\Domain\Usage\UsageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function count;

/**
 * app:billing:rate — тарификация ещё не рассчитанных событий
 * (включая повторные попытки unrated).
 */
#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события',
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly BillingService $billing)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->billing->ratePendingEvents();
        $unrated = $result['unrated'];

        $err = $output->getErrorOutput();
        foreach ($unrated as $event) {
            /** @var UsageEvent $event */
            $err->writeln(sprintf(
                'UNRATED %s %s no_price',
                $event->key(),
                $event->ts()->format('Y-m-d\TH:i:s\Z'),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($unrated)));

        return Command::SUCCESS;
    }
}
