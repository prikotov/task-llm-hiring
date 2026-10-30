<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\IsoUtc;
use App\Service\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация всех ещё не рассчитанных событий (unrated-события повторяются)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(private readonly RateService $rateService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->rateService->rate();
        $unrated = \count($result['unrated']);

        foreach ($result['unrated'] as $u) {
            $output->getErrorOutput()->writeln(
                sprintf(
                    'UNRATED %s/%s %s no_price',
                    $u['provider'],
                    $u['provider_request_id'],
                    IsoUtc::format($u['ts'])
                )
            );
        }

        $output->write(sprintf('RATED=%d UNRATED=%d', $result['rated'], $unrated) . "\n");

        return Command::SUCCESS;
    }
}
