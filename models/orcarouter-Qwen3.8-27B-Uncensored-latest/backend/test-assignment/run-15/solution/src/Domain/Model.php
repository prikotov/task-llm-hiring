<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\ValueObject\Price;
use App\Repository\ModelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A model offered by a provider.
 */
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_code', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $code;

    public function __construct(string $providerCode, string $code)
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

    /** Display form used in reports: provider/code. */
    public function displayName(): string
    {
        return $this->providerCode.'/'.$this->code;
    }
}
