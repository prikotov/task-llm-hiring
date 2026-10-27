<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    public string $code;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $offPeak = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Model::class, cascade: ['persist', 'remove'])]
    public Collection $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }
}
