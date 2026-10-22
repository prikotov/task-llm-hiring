<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Report;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * Формирование сводного отчёта (CSV) по тарифицированным событиям.
 *
 * Вывод — строго в stdout без финального переноса (см. print).
 */
#[AsCommand(name: 'app:billing:report', description: 'Формирует сводный отчёт об расходах (CSV)')]
final class BillingReportCommand extends Command
{
    public function __construct(private Report $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601 UTC), включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601 UTC), исключительно')
            ->setHelp("Отчёт группирует тарифицированные события по (user, model). unrated — не учитываются.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $from = $this->parseOptionalDate($input->getOption('from'));
            $to = $this->parseOptionalDate($input->getOption('to'));

            $this->report->build($from, $to);
        } catch (BadRequestHttpException $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        } catch (Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        $lines = [];
        $lines[] = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

        foreach ($this->report->rows() as $row) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['input'],
                $row['output'],
                $row['cache_read'],
                $row['cache_write'],
                $row['cost'],
            );
        }

        $total = $this->report->total();
        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            (int) $total['input'],
            (int) $total['output'],
            (int) $total['cache_read'],
            (int) $total['cache_write'],
            $total['cost'],
        );

        // вывод строками через \n, без финального переноса
        fwrite(STDOUT, implode("\n", $lines));
        fflush(STDOUT);

        return Command::SUCCESS;
    }

    private function parseOptionalDate(?string $value): ?\DateTimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (Throwable $e) {
            throw new BadRequestHttpException("Неверный формат даты: {$value}.");
        }
    }
}
