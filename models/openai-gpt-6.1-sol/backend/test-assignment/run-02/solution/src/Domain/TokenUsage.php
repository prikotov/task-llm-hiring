<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class TokenUsage
{
    public const KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    public function __construct(
        #[ORM\Column(type: 'bigint')]
        public readonly int $input,
        #[ORM\Column(type: 'bigint')]
        public readonly int $output,
        #[ORM\Column(type: 'bigint')]
        public readonly int $cache_read,
        #[ORM\Column(type: 'bigint')]
        public readonly int $cache_write,
    ) {
        foreach (self::KINDS as $kind) {
            if ($this->$kind < 0) {
                throw new \InvalidArgumentException('Количество токенов не может быть отрицательным.');
            }
        }
    }
}
