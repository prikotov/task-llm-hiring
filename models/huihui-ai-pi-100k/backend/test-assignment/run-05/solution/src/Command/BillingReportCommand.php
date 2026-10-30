<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportBuilder;
use App\Application\Exception\InputException;
use App\Support\IsoUtcTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по расходам: группировка по (user, model), период [from, to)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportBuilder $reporter,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $input->getOption('from') !== null
                ? IsoUtcTime::parse((string) $input->getOption('from'), '--from')
                : null;
            $to = $input->getOption('to') !== null
                ? IsoUtcTime::parse((string) $input->getOption('to'), '--to')
                : null;

            if ($from !== null && $to !== null && $from >= $to) {
                throw new InputException('--from должен быть строго меньше --to');
            }
        } catch (InputException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . ltrim($e->getMessage(), ' '));
            return Command::FAILURE;
        }

        // В stdout — только CSV, и ничего больше.
        $output->write($this->reporter->build($from, $to));
        return Command::SUCCESS;
    }
}
