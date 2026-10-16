<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Количества токен-видов одного события.
 *
 * Целые неотрицательные значения (все проверки — в загрузчике). Держит
 * форматируемую для Rate::calculate() структуру «ключ-тип => целое».
 */
final class Tokens
{
    public function __construct(
        private readonly int $input,
        private readonly int $output,
        private readonly int $cacheRead,
        private readonly int $cacheWrite
    ) {
    }

    public static function of(int $input, int $output, int $cacheRead, int $cacheWrite): self
    {
        return new self($input, $output, $cacheRead, $cacheWrite);
    }

    public function getInput(): int
    {
        return $this->input;
    }

    public function getOutput(): int
    {
        return $this->output;
    }

    public function getCacheRead(): int
    {
        return $this->cacheRead;
    }

    public function getCacheWrite(): int
    {
        return $this->cacheWrite;
    }

    public function getTotal(): int
    {
        return $this->input + $this->output + $this->cacheRead + $this->cacheWrite;
    }

    /**
     * Формат, ожидаемый Rate::calculate(): ключ — вид токена, значение — целое.
     *
     * @return array<string,int>
     */
    public function toArray(): array
    {
        return [
            'input' => $this->input,
            'output' => $this->output,
            'cache_read' => $this->cacheRead,
            'cache_write' => $this->cacheWrite,
        ];
    }
}
