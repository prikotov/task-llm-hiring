<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportBuilder;
use App\Domain\Common\Iso8601Utc;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт: затраты по группам (user, provider/model) за период',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportBuilder $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода включительно, ISO-8601 UTC, напр. 2026-02-01T00:00:00Z')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода исключительно, ISO-8601 UTC, напр. 2026-03-01T00:00:00Z')
            ->setHelp(
                'Печатает в stdout CSV (заголовок, строки "user,provider/model", строка TOTAL). '.
                'stdout содержит только CSV; служебные сообщения — в stderr.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromOption = $input->getOption('from');
        $toOption = $input->getOption('to');

        if ($fromOption !== null) {
            $from = self::parseInstant((string) $fromOption);
            if ($from === null) {
                $output->getErrorOutput()->writeln('Некорректное значение --from (ожидается ISO-8601 UTC, напр. 2026-02-01T00:00:00Z)');

                return Command::FAILURE;
            }
        } else {
            $from = null;
        }

        if ($toOption !== null) {
            $to = self::parseInstant((string) $toOption);
            if ($to === null) {
                $output->getErrorOutput()->writeln('Некорректное значение --to (ожидается ISO-8601 UTC, напр. 2026-03-01T00:00:00Z)');

                return Command::FAILURE;
            }
        } else {
            $to = null;
        }

        $output->write($this->report->build($from, $to));

        return Command::SUCCESS;
    }

    /** Строгий ISO-8601 с обязательным смещением или Z; момент — в UTC. */
    private static function parseInstant(string $value): ?DateTimeImmutable
    {
        try {
            return Iso8601Utc::parse(trim($value));
        } catch (Exception) {
            return null;
        }
    }
}
