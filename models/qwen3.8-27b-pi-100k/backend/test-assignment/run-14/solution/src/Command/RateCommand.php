<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Rate all not-yet-rated usage events (retries unrated).',
)]
final class RateCommand extends Command
{
    public function __construct(
        private RateService $svc,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        try {
            $r = $this->svc->rateAll();
        } catch (\Throwable $e) {
            $out->getErrorOutput()->writeln('billing:rate failed: ' . $e->getMessage());
            return 1;
        }

        [$rated, $unrated, $unratedItems] = $r;
        $out->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));
        foreach ($unratedItems as $u) {
            $out->getErrorOutput()->writeln(
                sprintf('UNRATED %s/%s %s no_price', $u['provider'], $u['requestId'], $u['ts'])
            );
        }

        return self::SUCCESS;    }
}
