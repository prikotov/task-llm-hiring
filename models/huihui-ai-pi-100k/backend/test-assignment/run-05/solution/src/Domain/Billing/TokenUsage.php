<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Расход токенов за событие: все значения — целые ≥ 0.
 */
final class TokenUsage
{
    private function __construct(
        public readonly int $input,
        public readonly int $output,
        public readonly int $cacheRead,
        public readonly int $cacheWrite,
    ) {
    }

    public static function create(int $input, int $output, int $cacheRead, int $cacheWrite): self
    {
        $kinds = ['input' => $input, 'output' => $output, 'cacheRead' => $cacheRead, 'cacheWrite' => $cacheWrite];

        foreach ($kinds as $kind => $value) {
            if ($value < 0) {
                throw new \DomainException(sprintf('Количество токенов (%s) не может быть отрицательным', $kind));
            }
        }

        return new self($input, $output, $cacheRead, $cacheWrite);
    }

    public function total(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }
}
