<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\BuildReport;
use App\Infrastructure\Input;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Вывести расходы по пользователям и моделям в CSV.')]
final class ReportCommand extends BillingCommand
{
    public function __construct(private BuildReport $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода включительно (UTC)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода исключительно (UTC)');
    }

    protected function perform(InputInterface $input, OutputInterface $output): void
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');
        $buffer = fopen('php://temp', 'w+b');
        try {
            foreach ($this->report->rows($from === null ? null : Input::instant($from), $to === null ? null : Input::instant($to)) as $row) {
                rewind($buffer);
                ftruncate($buffer, 0);
                fputcsv($buffer, $row, ',', '"', '', "\n");
                rewind($buffer);
                $output->write(stream_get_contents($buffer), false, OutputInterface::OUTPUT_RAW);
            }
        } finally {
            fclose($buffer);
        }
    }
}
