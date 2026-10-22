<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\InputData;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Сформировать CSV-отчёт по расходам')]
final class ReportCommand extends Command
{
    public function __construct(private readonly ReportService $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, включительно');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fromValue = $input->getOption('from');
            $toValue = $input->getOption('to');
            $from = $fromValue === null ? null : InputData::utcTimestamp($fromValue, '--from');
            $to = $toValue === null ? null : InputData::utcTimestamp($toValue, '--to');
            if ($from !== null && $to !== null && $from >= $to) {
                throw new \InvalidArgumentException('--from должен быть раньше --to');
            }
            $output->write($this->report->create($from, $to));
            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->getErrorOutput()->writeln('ERROR: '.$exception->getMessage());
            return Command::FAILURE;
        }
    }
}
