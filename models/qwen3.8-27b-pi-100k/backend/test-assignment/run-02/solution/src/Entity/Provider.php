<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM (справочник). Естественный ключ — code.
 *
 * Оф-пик задаётся на уровне провайдера и действует для всех его моделей:
 * окно по времени суток UTC [window_start, window_end) и множитель
 * стоимости события, попавшего в окно.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /** Код провайдера (естественный ключ, например "prism"). */
    #[ORM\Column(length: 128)]
    private string $code;

    /** Отображаемое имя (например "Prism AI"). */
    #[ORM\Column(length: 255)]
    private string $name;

    /** Начало окна оф-пика, «HH:MM», включительно (UTC-время суток). */
    #[ORM\Column(length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    /** Конец окна оф-пика, «HH:MM», исключительно (UTC-время суток). */
    #[ORM\Column(length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** Множитель стоимости событий в окне оф-пика (десятичная строка). */
    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 12, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /** Записывает или счищает окно оф-пика как целое (null — скидки нет). */
    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): void
    {
        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;
    }
}
