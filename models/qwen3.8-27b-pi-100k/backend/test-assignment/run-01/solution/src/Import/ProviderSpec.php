<?php

declare(strict_types=1);

namespace App\Import;

/** Провайдер из файла истории цен (проверенный парсером). */
final class ProviderSpec
{
    /** @var array{windowStart:string, windowEnd:string, multiplier:string}|null */
    private readonly ?array $offPeak;

    /**
     * @param array{windowStart:string, windowEnd:string, multiplier:string}|null $offPeak
     */
    public function __construct(
        private readonly string $code,
        private readonly string $name,
        ?array $offPeak,
    ) {
        $this->offPeak = $offPeak;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array{windowStart:string, windowEnd:string, multiplier:string}|null */
    public function getOffPeak(): ?array
    {
        return $this->offPeak;
    }
}
