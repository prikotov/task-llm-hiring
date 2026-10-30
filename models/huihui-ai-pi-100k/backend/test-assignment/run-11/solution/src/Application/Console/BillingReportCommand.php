<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Ingestion\Parsers;
use App\Application\Reporting\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Emit CSV of billing costs per (user, model).')]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', 'f', InputOption::VALUE_REQUIRED, 'ISO-8601 UTC start (inclusive), e.g. 2026-09-01T00:00:00Z')
            ->addOption('to', 't', InputOption::VALUE_REQUIRED, 'ISO-8601 UTC end (exclusive), e.g. 2026-10-01T00:00:00Z')
            ->setHelp('Emit CSV. Headers, lines, and TOTAL exactly as in TASK.md «Example».');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $to = null;
        if ($f = (string) $input->getOption('from')) {
            try {
                $from = Parsers::isoUtc($f);
            } catch (\Throwable $e) {
                $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');
                return Command::FAILURE;
            }
        }
        if ($t = (string) $input->getOption('to')) {
            try {
                $to = Parsers::isoUtc($t);
            } catch (\Throwable $e) {
                $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');
                return Command::FAILURE;
            }
        }

        $lines = $this->service->buildCsv($from, $to);
        foreach ($lines as $line) {
            $output->writeln($line);
        }

        return Command::SUCCESS;
    }
}
