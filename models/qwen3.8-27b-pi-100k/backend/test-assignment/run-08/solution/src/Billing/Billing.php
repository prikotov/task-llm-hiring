<?php

declare(strict_types=1);

namespace App\Billing;

/** Общие константы модуля биллинга. */
final class Billing
{
    /** Число знаков после точки в итоговой стоимости события и суммах отчёта (HALF_UP, правило 4). */
    public const COST_DECIMALS = 6;

    /** Валюта (зарезервировано на будущее; на сегодня везде USD). */
    public const CURRENCY = 'USD';
}
