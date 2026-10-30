<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\ReportService;
use App\Support\Iso8601;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт расходов по (user, model); unrated-события не попадают')]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $report,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromOpt = (string) $input->getOption('from');
        $toOpt = (string) $input->getOption('to');

        try {
            $from = $fromOpt === '' ? null : Iso8601::parseUtc($fromOpt);
            $to = $toOpt === '' ? null : Iso8601::parseUtc($toOpt);
        } catch (\InvalidArgumentException $e) {
            $which = $fromOpt !== '' && $toOpt === '' ? 'from' : 'to';
            if ($fromOpt === '') {
                $which = 'to';
            }
            return CommandSupport::fail($output, sprintf('--%s: %s', $which, $e->getMessage()));
        }

        try {
            $output->write($this->report->build($from, $to));
        } catch (\Throwable $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        return 0;
    }
}
