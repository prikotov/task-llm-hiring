<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(columns: ['provider_code','code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $providerCode;

    #[ORM\Column(length: 100)]
    private string $code;

    public function __construct(string $providerCode, string $code){
        $this->providerCode=$providerCode;
        $this->code=$code;
    }

    public function getProviderCode(): string { return $this->providerCode; }
    public function getCode(): string { return $this->code; }
}
