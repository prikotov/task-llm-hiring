<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по рассчитанным событиям за период (--from включительно, --to исключительно)',
)]
final class BillingReportCommand extends BaseCommand
{
    public function __construct(
        private readonly ReportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, UTC, включительно)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, UTC, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->withErrorHandling($input, $output, function () use ($input, $output): int {
            $from = self::parsePeriodOption((string) $input->getOption('from'), 'from');
            $to = self::parsePeriodOption((string) $input->getOption('to'), 'to');

            $output->write($this->service->generate($from, $to));
            return self::SUCCESS;
        });
    }

    private static function parsePeriodOption(string $value, string $optionName): ?\DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }
        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ImportException(sprintf('Некорректное значение --%s: "%s"', $optionName, $value));
        }
    }
}
