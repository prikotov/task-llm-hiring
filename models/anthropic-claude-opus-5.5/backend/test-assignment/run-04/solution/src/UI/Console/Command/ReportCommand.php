<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Report\BuildReport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам в CSV (stdout)',
)]
final class ReportCommand extends Command
{
    public function __construct(private readonly BuildReport $buildReport)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода включительно (ISO-8601 UTC)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода исключительно (ISO-8601 UTC)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stderr = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        try {
            /** @var string|null $from */
            $from = $input->getOption('from');
            /** @var string|null $to */
            $to = $input->getOption('to');

            $fromDate = BuildReport::parseBound($from, '--from');
            $toDate = BuildReport::parseBound($to, '--to');

            if (null !== $fromDate && null !== $toDate && $fromDate > $toDate) {
                throw new \InvalidArgumentException('--from должен быть раньше --to.');
            }

            $report = ($this->buildReport)($fromDate, $toDate);
        } catch (\Throwable $e) {
            $stderr->writeln(sprintf('<error>Ошибка отчёта: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->write($this->buildReport->toCsv($report), false, OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
    }
}
