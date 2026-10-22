<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money\Decimal;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

use function array_merge;
use function implode;
use function str_contains;
use function str_replace;

final readonly class ReportService
{
    private const string HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function generateCsv(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): string
    {
        $conditions = ['cost_usd IS NOT NULL'];
        $parameters = [];
        $types = [];

        if ($from !== null) {
            $conditions[] = 'occurred_at >= :from';
            $parameters['from'] = $from;
            $types['from'] = Types::DATETIMETZ_IMMUTABLE;
        }

        if ($to !== null) {
            $conditions[] = 'occurred_at < :to';
            $parameters['to'] = $to;
            $types['to'] = Types::DATETIMETZ_IMMUTABLE;
        }

        $sql = 'SELECT user_id, provider_code, model_code,'
            . ' SUM(tokens_input) AS tokens_input,'
            . ' SUM(tokens_output) AS tokens_output,'
            . ' SUM(tokens_cache_read) AS tokens_cache_read,'
            . ' SUM(tokens_cache_write) AS tokens_cache_write,'
            . ' SUM(cost_usd) AS cost_usd'
            . ' FROM usage_events'
            . ' WHERE ' . implode(' AND ', $conditions)
            . ' GROUP BY user_id, provider_code, model_code'
            . ' ORDER BY user_id COLLATE "C", provider_code COLLATE "C", model_code COLLATE "C"';

        $rows = $this->entityManager->getConnection()->executeQuery($sql, $parameters, $types)->fetchAllAssociative();

        $lines = [self::HEADER];
        $totalInput = Decimal::zero();
        $totalOutput = Decimal::zero();
        $totalCacheRead = Decimal::zero();
        $totalCacheWrite = Decimal::zero();
        $totalCost = Decimal::zero();

        foreach ($rows as $row) {
            $input = Decimal::fromString((string) $row['tokens_input']);
            $output = Decimal::fromString((string) $row['tokens_output']);
            $cacheRead = Decimal::fromString((string) $row['tokens_cache_read']);
            $cacheWrite = Decimal::fromString((string) $row['tokens_cache_write']);
            $cost = Decimal::fromString((string) $row['cost_usd']);

            $totalInput = $totalInput->add($input);
            $totalOutput = $totalOutput->add($output);
            $totalCacheRead = $totalCacheRead->add($cacheRead);
            $totalCacheWrite = $totalCacheWrite->add($cacheWrite);
            $totalCost = $totalCost->add($cost);

            $model = $row['provider_code'] . '/' . $row['model_code'];
            $lines[] = implode(',', array_merge(
                [self::escapeCsv((string) $row['user_id']), self::escapeCsv($model)],
                [
                    $input->format(0),
                    $output->format(0),
                    $cacheRead->format(0),
                    $cacheWrite->format(0),
                    $cost->format(6),
                ],
            ));
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            $totalInput->format(0),
            $totalOutput->format(0),
            $totalCacheRead->format(0),
            $totalCacheWrite->format(0),
            $totalCost->format(6),
        ]);

        return implode("\n", $lines) . "\n";
    }

    private static function escapeCsv(string $value): string
    {
        if (
            !str_contains($value, ',')
            && !str_contains($value, '"')
            && !str_contains($value, "\n")
            && !str_contains($value, "\r")
        ) {
            return $value;
        }

        return '"' . str_replace('"', '""', $value) . '"';
    }
}
