<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\ReportBuilder;
use App\Exception\ImportException;
use App\Util\UtcTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Отчёт по расходам (CSV)')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportBuilder $builder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC', null)
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC', null)
            ->setHelp('CSV в stdout: строки по (user, model), стоимость — сумма округлённых стоимостей событий. Unrated-события исключены.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = self::parseBound($input->getOption('from'), '--from', $output);
        if ($from === false) {
            return 1;
        }
        $to = self::parseBound($input->getOption('to'), '--to', $output);
        if ($to === false) {
            return 1;
        }

        try {
            $csv = $this->builder->build($from, $to);
        } catch (ImportException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>', OutputInterface::VERBOSITY_QUIET);

            return 1;
        }

        $output->write($csv);

        return 0;
    }

    /** @param mixed $value */
    private static function parseBound($value, string $option, OutputInterface $output): \DateTimeImmutable|null|false
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return UtcTime::parse($value, $option);
        } catch (ImportException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>', OutputInterface::VERBOSITY_QUIET);

            return false;
        }
    }
}
