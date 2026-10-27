<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class Model
{
    /**
     * @ORM\Id
     * @ORM\Column(type: "string", length: 255)
     */
    private string $provider;

    /**
     * @ORM\Id
     * @ORM\Column(type: "string", length: 255)
     */
    private string $code;

    /**
     * Служебный ключ для отчёта: "prism/lumen-8".
     */
    public function __construct(string $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getModelLabel(): string
    {
        return $this->provider . '/' . $this->code;
    }

    public function __toString(): string
    {
        return $this->getModelLabel();
    }
}
