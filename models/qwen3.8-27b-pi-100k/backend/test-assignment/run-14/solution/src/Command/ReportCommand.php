<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Print the CSV report grouped by (user, model) plus TOTAL.',
)]
final class ReportCommand extends Command
{
    public function __construct(
        private ReportService $svc,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start time of the report period (ISO-8601 UTC, inclusive).');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'End time of the report period (ISO-8601 UTC, exclusive).');
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $from = $in->getOption('from');
        $to = $in->getOption('to');
        $fromIso = is_string($from) && $from !== null ? $from : null;
        $toIso = is_string($to) && $to !== null ? $to : null;

        try {
            $csv = $this->svc->render($fromIso, $toIso);
        } catch (\Throwable $e) {
            $out->getErrorOutput()->writeln('billing:report failed: ' . $e->getMessage());
            return 1;
        }

        $out->write($csv);
        return self::SUCCESS;
    }
}
