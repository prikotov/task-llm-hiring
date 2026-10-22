<?php

declare(strict_types=1);

namespace App\Billing\Application\Reporting;

use App\Billing\Domain\Money\Money;

/**
 * Порт запроса агрегатов для отчёта.
 *
 * Агрегация выполняется в БД (NUMERIC в PostgreSQL точен), но наружу
 * отдаются строки — в PHP нет ни одного float на пути к отчёту.
 */
interface ReportQuery
{
    /**
     * Группы (user, model) с суммой стоимостей и токенов за период.
     * Только рассчитанные события; сортировка по (user, model).
     *
     * @return list<ReportGroupRow>
     */
    public function groups(ReportPeriod $period): array;
}

/**
 * Строка отчёта по паре (user, model): суммы токенов по видам и сумма
 * уже округлённых стоимостей событий.
 */
final readonly class ReportGroupRow
{
    public function __construct(
        public string $user,
        public string $providerCode,
        public string $modelCode,
        public int $tokensInput,
        public int $tokensOutput,
        public int $tokensCacheRead,
        public int $tokensCacheWrite,
        public Money $cost,
    ) {
    }

    public function model(): string
    {
        return $this->providerCode.'/'.$this->modelCode;
    }
}

/**
 * Период отчёта: --from включительно, --to исключительно.
 */
final readonly class ReportPeriod
{
    private function __construct(
        public ?\DateTimeImmutable $from,
        public ?\DateTimeImmutable $to,
    ) {
        if (null !== $from && null !== $to && $from >= $to) {
            throw new \InvalidArgumentException(sprintf(
                'Начало периода (%s) должно быть раньше конца (%s)',
                $from->format('Y-m-d\TH:i:s\Z'),
                $to->format('Y-m-d\TH:i:s\Z'),
            ));
        }
    }

    /**
     * Без --from/--to отчёт покрывает все события.
     */
    public static function between(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): self
    {
        return new self($from, $to);
    }
}

/**
 * Готовый отчёт: строки (уже отсортированные) и итоговая строка.
 */
final readonly class Report
{
    /**
     * @param list<ReportGroupRow> $rows
     */
    public function __construct(
        public array $rows,
        public ReportGroupRow $total,
    ) {
    }
}
