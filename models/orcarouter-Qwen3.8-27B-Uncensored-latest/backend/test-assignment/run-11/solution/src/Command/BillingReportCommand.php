<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Reports\ReportService;
use App\Domain\ImportDataException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Отчёт по расходам (CSV в stdout)')]
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
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601 UTC, включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601 UTC, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = null;
        $to = null;

        if ($input->getOption('from') !== null) {
            try {
                $from = new \DateTimeImmutable((string) $input->getOption('from'));
            } catch (\Exception) {
                $output->getErrorOutput()->writeln('Ошибка: неверный формат --from');
                return 1;
            }
        }

        if ($input->getOption('to') !== null) {
            try {
                $to = new \DateTimeImmutable((string) $input->getOption('to'));
            } catch (\Exception) {
                $output->getErrorOutput()->writeln('Ошибка: неверный формат --to');
                return 1;
            }
        }

        $csv = $this->service->build($from, $to);
        $output->write($csv."\n");

        return 0;
    }
}
