<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\ModelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_model', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    public Provider $provider;

    #[ORM\Column(type: 'string')]
    public string $code = '';

    public function getId(): ?int
    {
        return $this->id;
    }
}
