<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Количество израсходованных токенов по видам. Общий язык контекстов
 * Usage (что израсходовано) и Pricing (как это стоит).
 */
#[ORM\Embeddable]
final readonly class TokenUsage
{
    public function __construct(
        #[ORM\Column(name: 'input', type: 'bigint')]
        public int $input,
        #[ORM\Column(name: 'output', type: 'bigint')]
        public int $output,
        #[ORM\Column(name: 'cache_read', type: 'bigint')]
        public int $cacheRead,
        #[ORM\Column(name: 'cache_write', type: 'bigint')]
        public int $cacheWrite,
    ) {
        foreach (['input' => $input, 'output' => $output, 'cache_read' => $cacheRead, 'cache_write' => $cacheWrite] as $kind => $count) {
            if ($count < 0) {
                throw new InvalidArgumentException(sprintf('Количество токенов "%s" не может быть отрицательным: %d.', $kind, $count));
            }
        }
    }

    /** Сумма всех видов токенов — точно, без риска переполнения int. */
    public function total(): Decimal
    {
        return Decimal::fromInt($this->input)
            ->add(Decimal::fromInt($this->output))
            ->add(Decimal::fromInt($this->cacheRead))
            ->add(Decimal::fromInt($this->cacheWrite));
    }
}
