<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\BillingService;
use App\Billing\Money;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class BillingReportCommand extends Command
{
    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly BillingService $billing,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:report')
            ->setDescription('CSV-отчёт расходов (stdout)')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601 UTC), включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601 UTC), исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $fromTs = $this->parseBound($from, '--from');
        $toTs = $this->parseBound($to, '--to');

        if ($fromTs !== null && $toTs !== null && $fromTs >= $toTs) {
            $output->getErrorOutput()->writeln('<error>--from должен быть раньше --to</error>');

            return Command::FAILURE;
        }

        $data = $this->billing->report(
            $fromTs === null ? null : $fromTs->format('Y-m-d H:i:s'),
            $toTs === null ? null : $toTs->format('Y-m-d H:i:s'),
        );

        $lines = [self::HEADER];
        foreach ($data['rows'] as $row) {
            $lines[] = implode(',', [
                $this->csvField($row['user']),
                $this->csvField($row['model']),
                (string) $row['in'],
                (string) $row['out'],
                (string) $row['cr'],
                (string) $row['cw'],
                Money::toSix($row['cost']),
            ]);
        }
        $total = $data['total'];
        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $total['in'],
            (string) $total['out'],
            (string) $total['cr'],
            (string) $total['cw'],
            Money::toSix($total['cost']),
        ]);

        // Статически \n; вывод только в stdout.
        $output->write(implode("\n", $lines)."\n", false, OutputInterface::OUTPUT_NORMAL);

        return Command::SUCCESS;
    }

    private function parseBound($value, string $option): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', (string) $value, new \DateTimeZone('UTC'));
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $value, new \DateTimeZone('UTC'));
        }
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, (string) $value, new \DateTimeZone('UTC'));
        }

        if ($dt === false) {
            throw new \InvalidArgumentException('Некорректное значение периода '.$option.': '.$value);
        }

        return $dt;
    }

    private function csvField(string $value): string
    {
        if (preg_match('/["\n,]/', $value) === 1) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
