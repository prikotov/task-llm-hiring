<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

/**
 * Разбивка израсходованных токенов по видам — то, что приходит в событии
 * использования. Все четыре вида присутствуют всегда (могут быть нулевыми),
 * чтобы не различать "нет поля" и "ноль".
 */
final readonly class TokenUsage
{
    public function __construct(
        public TokenCount $input,
        public TokenCount $output,
        public TokenCount $cacheRead,
        public TokenCount $cacheWrite,
    ) {
    }

    public static function of(int $input, int $output, int $cacheRead, int $cacheWrite): self
    {
        return new self(
            new TokenCount($input),
            new TokenCount($output),
            new TokenCount($cacheRead),
            new TokenCount($cacheWrite),
        );
    }

    public static function empty(): self
    {
        return self::of(0, 0, 0, 0);
    }

    public function total(): int
    {
        return $this->input->value
            + $this->output->value
            + $this->cacheRead->value
            + $this->cacheWrite->value;
    }

    public function plus(self $other): self
    {
        return new self(
            new TokenCount($this->input->value + $other->input->value),
            new TokenCount($this->output->value + $other->output->value),
            new TokenCount($this->cacheRead->value + $other->cacheRead->value),
            new TokenCount($this->cacheWrite->value + $other->cacheWrite->value),
        );
    }
}
