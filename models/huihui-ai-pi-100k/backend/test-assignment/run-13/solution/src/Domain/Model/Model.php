<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Provider\Provider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'provider', referencedColumnName: 'code', onDelete: 'CASCADE', unique: true)]
    private Provider $provider;

    #[ORM\Column(type: 'string')]
    private string $code;

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function id(): ?int
    {
        return $this->id;
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
