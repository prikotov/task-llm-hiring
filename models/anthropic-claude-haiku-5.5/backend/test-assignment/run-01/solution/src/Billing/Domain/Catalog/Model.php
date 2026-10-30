<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_model')]
#[ORM\UniqueConstraint(name: 'uniq_billing_model_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'code', nullable: false)]
    private Provider $provider;

    #[ORM\Column(length: 128)]
    private string $code;

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function code(): string
    {
        return $this->code;
    }
}
