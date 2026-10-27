<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Natural key: (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'ux_models_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    /** code провайдера (natural FK, не ORM-ассоциация — см. допущения). */
    #[ORM\Column(type: 'string', length: 64)]
    private string $providerCode = '';

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    public function id(): ?int
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }

    public function withIdentity(string $providerCode, string $code): void
    {
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function label(): string
    {
        return sprintf('%s/%s', $this->providerCode, $this->code);
    }
}
