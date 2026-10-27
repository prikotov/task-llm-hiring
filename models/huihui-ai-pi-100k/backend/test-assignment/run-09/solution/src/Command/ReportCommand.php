<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportService;
use App\Exception\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable;
use DateTimeZone;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам: CSV по (user, модель) за период [--from, --to)',
)]
final class ReportCommand extends Command
{
    public function __construct(private readonly ReportService $reports)
    {
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
        try {
            $from = $input->getOption('from') !== null ? $this->parseTs((string) $input->getOption('from'), '--from') : null;
            $to = $input->getOption('to') !== null ? $this->parseTs((string) $input->getOption('to'), '--to') : null;

            $lines = $this->reports->build($from, $to);
            $output->writeln(implode("\n", $lines));
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function parseTs(string $ts, string $option): DateTimeImmutable
    {
        try {
            $v = new DateTimeImmutable($ts, new DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException("{$option}: ожидается ISO-8601 время UTC, получено: $ts");
        }
        if ($v->getTimezone()->getOffset($v) !== 0) {
            throw new ImportException("{$option}: время должно быть UTC (Z), получено: $ts");
        }

        return $v->setTimezone(new DateTimeZone('UTC'));
    }
}
