<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Domain\Common\Input;
use App\Domain\Pricing\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * app:billing:report [--from=<iso>] [--to=<iso>] — CSV в stdout.
 * Период: --from включительно, --to эксклюзивно.
 */
#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (CSV в stdout, агрегация по (user, model))',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reports)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Начало периода, ISO-8601 (UTC), включительно');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Конец периода, ISO-8601 (UTC), эксклюзивно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $from = $this->parseBound((string) $input->getOption('from'), '--from', $io);
        if ($from === false) {
            return Command::FAILURE;
        }
        $to = $this->parseBound((string) $input->getOption('to'), '--to', $io);
        if ($to === false) {
            return Command::FAILURE;
        }

        if ($from !== null && $to !== null && $from >= $to) {
            $io->error('--from должен быть раньше --to');

            return Command::FAILURE;
        }

        // CSV — строго в stdout (без декораций SymfonyStyle).
        $stream = $output->getStream();
        fwrite($stream, $this->reports->buildCsv($from, $to));

        return Command::SUCCESS;
    }

    private function parseBound(string $value, string $option, SymfonyStyle $io): \DateTimeImmutable|false|null
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $ts = Input::parseIsoUtc($value);
        if ($ts === null) {
            $io->error($option.': ожидается ISO-8601 (UTC, "Z")');

            return false;
        }

        return $ts;
    }
}
