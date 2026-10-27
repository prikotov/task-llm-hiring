<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Сводка стоимости: CSV (у пользователя — модель — стоимость)',
)]
final class AppBillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периоде ISO-8601 (UTC), включительно');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периоде ISO-8601 (UTC), исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = null;
        $to = null;
        $fromArg = (string) $input->getOption('from');
        $toArg = (string) $input->getOption('to');
        try {
            if ($fromArg !== '') {
                $from = new \DateTimeImmutable($fromArg, new \DateTimeZone('UTC'));
            }
            if ($toArg !== '') {
                $to = new \DateTimeImmutable($toArg, new \DateTimeZone('UTC'));
            }
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>некорректная ISO-8601 дата: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        $err = $output->getErrorOutput();
        try {
            $report = $this->service->report($from, $to);
        } catch (\Throwable $e) {
            $err->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        // CSV в stdout
        $out = $output;
        $out->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($report['rows'] as $r) {
            $out->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $r['user'],
                $r['model'],
                $r['ti'],
                $r['to_'],
                $r['cr'],
                $r['cw'],
                $r['cost']->format(6)
            ));
        }
        $out->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $report['total']['ti'],
            $report['total']['to_'],
            $report['total']['cr'],
            $report['total']['cw'],
            $report['total']['cost']->format(6),
        ));

        return Command::SUCCESS;
    }
}
