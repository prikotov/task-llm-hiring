<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ReportService;
use App\Domain\Decimal;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по пользователям и моделям (stdout)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода включительно, ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода исключительно, ISO-8601 UTC')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = self::parseBound($input->getOption('from'), 'from');
        $to = self::parseBound($input->getOption('to'), 'to');

        try {
            $res = $this->service->report($from, $to);
        } catch (DbalException|\InvalidArgumentException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $emit = static function (string $user, string $model, array $tokens, string $cost) use ($output): void {
            $output->write(implode(',', [
                $user,
                $model,
                (string) $tokens['input'],
                (string) $tokens['output'],
                (string) $tokens['cache_read'],
                (string) $tokens['cache_write'],
                Decimal::format($cost, 6),
            ])."\n");
        };

        $output->write("user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n");
        foreach ($res['rows'] as $row) {
            $emit($row['user'], $row['model'], $row['tokens'], $row['cost']);
        }
        $emit('TOTAL', '', $res['total_tokens'], $res['total_cost']);

        return Command::SUCCESS;
    }

    private static function parseBound(mixed $raw, string $opt): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (!\is_string($raw)) {
            throw new \InvalidArgumentException(sprintf('--%s must be an ISO-8601 string', $opt));
        }

        try {
            return (new DateTimeImmutable($raw))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        } catch (\Exception) {
            throw new \InvalidArgumentException(sprintf('--%s is not a valid ISO-8601 date "%s"', $opt, $raw));
        }
    }
}
