<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
class Model
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $code;

    public function getProviderCode(): string { return $this->providerCode; }
    public function setProviderCode(string $c): void { $this->providerCode = $c; }
    public function getCode(): string { return $this->code; }
    public function setCode(string $c): void { $this->code = $c; }
}
