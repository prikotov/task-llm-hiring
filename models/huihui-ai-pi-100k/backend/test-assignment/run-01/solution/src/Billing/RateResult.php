<?php

declare(strict_types=1);

namespace App\Billing;

/** Результат одного запуска app:billing:rate. */
final class RateResult
{
    public int $ratedCount = 0;

    /** Готовые строки stderr: "UNRATED <provider>/<id> <ts> no_price". */
    public array $unratedLines = [];

    public function addUnrated(string $line): void
    {
        $this->unratedLines[] = $line;
    }

    public function unratedCount(): int
    {
        return count($this->unratedLines);
    }
}
