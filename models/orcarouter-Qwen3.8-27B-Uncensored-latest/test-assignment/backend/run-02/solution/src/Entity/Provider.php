<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM. off_peak — JSON-объект окна скидки
 * (OffPeakWindow::encode()): {"start": 59400, "end": 180, "multiplier": "0.5"}
 * или null, если скидки нет.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    public string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $name = '';

    #[ORM\Column(name: 'off_peak', type: 'json', nullable: true)]
    public ?string $offPeak = null;
}
