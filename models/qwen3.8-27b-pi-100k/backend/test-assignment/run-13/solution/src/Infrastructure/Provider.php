<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM. Естественный ключ — code.
 */
#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'billing_provider')]
#[ORM\UniqueConstraint(name: 'uq_billing_provider_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    /** ЧЧ:ММ UTC; null — скидка не настроена. */
    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    /** ЧЧ:ММ UTC; null — скидка не настроена. */
    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** Множитель стоимости события в оф-пик; null — скидка не настроена. */
    #[ORM\Column(type: Types::STRING, nullable: true, columnDefinition: 'numeric(10,6)')]
    private ?string $offPeakMultiplier = null;

    public function __construct(
        string $code,
        string $name,
        ?string $offPeakWindowStart = null,
        ?string $offPeakWindowEnd = null,
        ?string $offPeakMultiplier = null,
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->offPeakWindowStart = $offPeakWindowStart;
        $this->offPeakWindowEnd = $offPeakWindowEnd;
        $this->offPeakMultiplier = $offPeakMultiplier === null ? null : Money::format($offPeakMultiplier);
    }

    public function id(): ?int { return $this->id; }
    public function codeValue(): string { return $this->code; }
    public function nameValue(): string { return $this->name; }
    public function offPeakWindowStartValue(): ?string { return $this->offPeakWindowStart; }
    public function offPeakWindowEndValue(): ?string { return $this->offPeakWindowEnd; }
    public function offPeakMultiplierValue(): ?string { return $this->offPeakMultiplier; }

    public function update(
        string $name,
        ?string $offPeakWindowStart,
        ?string $offPeakWindowEnd,
        ?string $offPeakMultiplier,
    ): void {
        $this->name = $name;
        $this->offPeakWindowStart = $offPeakWindowStart;
        $this->offPeakWindowEnd = $offPeakWindowEnd;
        $this->offPeakMultiplier = $offPeakMultiplier === null ? null : Money::format($offPeakMultiplier);
    }
}
