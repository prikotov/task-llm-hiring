<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM. Натуральный ключ — `code`.
 *
 * Оф-пиковая скидка: окно `[window_start, window_end)` по UTC
 * `window_start` включительно, `window_end` исключительно; окно может
 * переходить через полночь. `multiplier` — множитель стоимости события,
 * попавшего в окно (десятичная строка). Если поля null — скидки нет.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    public string $name = '';

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakWindowStart = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
