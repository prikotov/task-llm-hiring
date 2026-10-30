<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\IsoTime;
use App\Domain\ReportBuilder;
use App\Repository\EventRatingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по (user, model) за период. --from включительно, --to исключительно; unrated не попадают.',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly EventRatingRepository $eventRatingRepository,
        private readonly ReportBuilder $reportBuilder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, ISO-8601 UTC (включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, ISO-8601 UTC (исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseOption($input, 'from', $output);
        if ($from === false) {
            return 1;
        }

        $to = $this->parseOption($input, 'to', $output);
        if ($to === false) {
            return 1;
        }

        $rows = $this->eventRatingRepository->reportRows($from, $to);
        $output->write($this->reportBuilder->build($rows));

        return 0;
    }

    /** @return (\DateTimeImmutable|null)|bool null — опция не задана; false — ошибка формата */
    private function parseOption(InputInterface $input, string $name, OutputInterface $output): \DateTimeImmutable|bool|null
    {
        $value = $input->getOption($name);
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return IsoTime::parseUtc((string) $value);
        } catch (\ValueError $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return false;
        }
    }
}
