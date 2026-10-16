<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    public Provider $provider;

    /** Код модели в пределах провайдера. */
    #[ORM\Column(type: 'string', length: 128)]
    public string $code = '';

    /** Отчётное имя вида `<provider>/<code>`. */
    public function displayCode(): string
    {
        return $this->provider->code.'/'.$this->code;
    }
}
