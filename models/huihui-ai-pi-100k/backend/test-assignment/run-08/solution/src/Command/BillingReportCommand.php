<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:billing:report', description: 'CSV report (user,model) over rated usage events.')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC, report start (inclusive).');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC, report end (exclusive).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseIso($input, 'from');
        $to = $this->parseIso($input, 'to');
        $stderr = new SymfonyStyle($input, $output->getErrorOutput());

        if ($from !== null && $to !== null && $from >= $to) {
            $stderr->error('--from must be < --to.');

            return Command::FAILURE;
        }

        $rows = $this->report->report($from, $to);

        foreach ($rows as $row) {
            $output->writeln($row);
        }

        return Command::SUCCESS;
    }

    /** @var \DateTimeImmutable|null */
    private function parseIso(InputInterface $input, string $option): ?\DateTimeImmutable
    {
        $value = $input->getOption($option);
        if ($value === null || $value === '') {
            return null;
        }

        $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, (string) $value)
            ?? \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', (string) $value);

        if ($dt === false) {
            throw new \RuntimeException(sprintf('--%s must be ISO-8601 UTC, got "%s".', $option, $value));
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }
}
