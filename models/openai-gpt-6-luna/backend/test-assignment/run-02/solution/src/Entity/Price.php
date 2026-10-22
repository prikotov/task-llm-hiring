<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
class Price
{
    #[ORM\Id]
    #[ORM\Column(name: 'provider_code', type: 'string', length: 120)]
    public string $providerCode;

    #[ORM\Id]
    #[ORM\Column(name: 'model_code', type: 'string', length: 120)]
    public string $modelCode;

    #[ORM\Id]
    #[ORM\Column(name: 'valid_from', type: 'datetimetz_immutable', precision: 6)]
    public DateTimeImmutable $validFrom;

    #[ORM\Column(name: 'currency', type: 'string', length: 3)]
    public string $currency;

    #[ORM\Column(name: 'unit', type: 'string', length: 40)]
    public string $unit;

    #[ORM\Column(name: 'rate_input', type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'decimal', precision: 50, scale: 6, nullable: true)]
    public ?string $rateAny = null;
}
