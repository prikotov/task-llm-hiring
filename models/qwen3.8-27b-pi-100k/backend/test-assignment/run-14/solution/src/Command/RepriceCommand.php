<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'BONUS: apply price-row corrections and recompute affected rated events.',
)]
final class RepriceCommand extends Command
{
    public function __construct(
        private RepriceService $svc,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the corrected prices file (same format as prices.json).');
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $file = (string) $in->getArgument('file');
        if (!is_file($file)) {
            $out->getErrorOutput()->writeln('reprice: file not found: ' . $file);
            return 1;
        }
        $json = @file_get_contents($file) ?: '';
        try {
            [$n, $delta] = $this->svc->reprice($json);
        } catch (\Throwable $e) {
            $out->getErrorOutput()->writeln('reprice failed: ' . $e->getMessage());
            return 1;
        }
        $out->writeln('REPRICED_EVENTS=' . $n);
        $out->writeln('REPRICE_DELTA_USD=' . $delta->format());
        return self::SUCCESS;
    }
}
