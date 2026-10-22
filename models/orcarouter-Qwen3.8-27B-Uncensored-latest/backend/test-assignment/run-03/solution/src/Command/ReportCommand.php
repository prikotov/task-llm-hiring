<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\IsoTime;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report')]
final class ReportCommand extends AppCommand
{
    public function __construct(private readonly ReportService $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Отчёт расходов (CSV) в stdout')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'начало периода (ISO-8601, включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'конец периода (ISO-8601, эксклюзивно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $input->getOption('from') !== null ? IsoTime::parseUtc((string) $input->getOption('from')) : null;
            $to = $input->getOption('to') !== null ? IsoTime::parseUtc((string) $input->getOption('to')) : null;
        } catch (\RuntimeException $e) {
            $this->error($output, 'app:billing:report: ' . $e->getMessage());

            return self::FAILURE;
        }

        $data = $this->report->report($from, $to);

        $lines = [];
        $lines[] = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

        foreach ($data['rows'] as $row) {
            $lines[] = implode(',', [
                $row['user'],
                $row['model'],
                (string) $row['tokens'][0],
                (string) $row['tokens'][1],
                (string) $row['tokens'][2],
                (string) $row['tokens'][3],
                $row['cost'],
            ]);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            $data['total']['input'],
            $data['total']['output'],
            $data['total']['cache_read'],
            $data['total']['cache_write'],
            $data['total']['cost'],
        ]);

        // CSV: перевод строки \n, без лишнего хвостового перевода строка по строчке.
        $output->write(implode("\n", $lines) . "\n");

        return self::SUCCESS;
    }
}
