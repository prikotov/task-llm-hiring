<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен: действовала с valid_from до появления следующей строки
 * той же модели. Естественный ключ — (provider, model, valid_from).
 * rates — JSON: {"input": "3.000000", ...} или {"any": "2.400000"}.
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_lines')]
#[ORM\UniqueConstraint(name: 'uq_price_lines_provider_model_valid_from', columns: ['provider_code', 'model_code', 'valid_from'])]
class PriceLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 128)]
    public string $providerCode = '';

    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    public string $modelCode = '';

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    public string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    public string $unit = '';

    /** JSON-строка, значения — десятичные строки (нет float в схеме). */
    #[ORM\Column(type: 'json')]
    public string $rates = '{}';
}
