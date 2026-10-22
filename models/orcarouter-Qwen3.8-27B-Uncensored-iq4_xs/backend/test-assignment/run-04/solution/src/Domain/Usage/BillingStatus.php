<?php

declare(strict_types=1);

namespace App\Domain\Usage;

enum BillingStatus: string
{
    /** Цена на момент ts ещё не определилась (unrated). Повторная попытка каждого запуска rate. */
    case Unrated = 'unrated';

    /** Стоимость зафиксирована (округлена до 6 знаков), цена, по которой рассчитано — в priceRow. */
    case Rated = 'rated';
}
