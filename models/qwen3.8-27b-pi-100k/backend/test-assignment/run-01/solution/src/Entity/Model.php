<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/** Модель провайдера. Натуральный ключ — (providerCode, code). */
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_code', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
    private string $providerCode;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $code;

    public function __construct(string $providerCode, string $code)
    {
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /** @internal Только для восстановления сущности из результата SQL-запроса. */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
