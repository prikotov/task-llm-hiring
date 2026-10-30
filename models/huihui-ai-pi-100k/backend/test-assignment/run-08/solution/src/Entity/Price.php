<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели, действующая с valid_from (включительно).
 *
 * Цены — строки-числа (bcmath), схема тарификации — unit:
 *  - per_1m_tokens: ставки по видам input/output/cache_read/cache_write;
 *  - per_1m_tokens_unified: единый any.
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uq_prices_row', columns: ['provider_code', 'model_code', 'valid_from'])]
final class Price
{
    #[ORM\Id]
    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[ORM\Id]
    #[ORM\Column(name: 'model_code', type: 'string', length: 64)]
    public string $modelCode = '';

    /** Время начала действия (UTC, «Y-m-d H:i:sO»). */
    #[ORM\Id]
    #[ORM\Column(name: 'valid_from', type: 'tz_date_string')]
    public string $validFrom = '';

    #[ORM\Column(type: 'string', length: 3, options: ['default' => 'USD'])]
    public string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    public string $unit = '';

    /**
     * Карта «вид → строка-число», напр.
     * ['input' => '3.000000', 'output' => '12.000000', ...]
     * или ['any' => '2.400000'].
     */
    #[ORM\Column(type: 'json')]
    public array $rates = [];
}
