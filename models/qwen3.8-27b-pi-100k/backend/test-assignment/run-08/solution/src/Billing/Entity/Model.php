<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Естественный ключ — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
final class Model
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $code;

    public function __construct(string $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    /** Отображаемый идентификатор вида «prism/lumen-8». */
    public function displayId(): string
    {
        return $this->provider.'/'.$this->code;
    }
}
