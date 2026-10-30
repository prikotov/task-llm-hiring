<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'billing_model')]
#[ORM\UniqueConstraint(name: 'uq_billing_model', columns: ['provider_code', 'code'])]
#[ORM\Index(name: 'idx_billing_model_lookup', columns: ['provider_code', 'code'])]
final class Model
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $providerCode;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $code;

    public function __construct(string $providerCode, string $code)
    {
        $this->providerCode = $providerCode;
        $this->code = $code;
    }

    public function id(): ?int { return $this->id; }
    public function providerCodeValue(): string { return $this->providerCode; }
    public function codeValue(): string { return $this->code; }
}
