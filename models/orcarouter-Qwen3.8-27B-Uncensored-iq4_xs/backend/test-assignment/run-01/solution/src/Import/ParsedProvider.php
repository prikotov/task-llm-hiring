<?php

declare(strict_types=1);

namespace App\Import;

use App\Billing\OffPeakWindow;
use App\Exception\ImportException;

/** Провайдер, разобранный из файла цен. */
final class ParsedProvider
{
    public function __construct(
        private readonly string $code,
        private readonly string $name,
        private readonly ?OffPeakWindow $offPeak,
    ) {
    }

    /** @param array<string, mixed> $p */
    public static function fromArray(array $p, string $ctx): self
    {
        $code = self::nonEmptyString($p, 'code', $ctx);
        $name = self::nonEmptyString($p, 'name', $ctx);
        $offPeak = OffPeakWindow::fromSpec($p['off_peak'] ?? null, $ctx);

        return new self($code, $name, $offPeak);
    }

    private static function nonEmptyString(array $item, string $field, string $ctx): string
    {
        $value = $item[$field] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" — обязательная непустая строка', $ctx, $field));
        }

        return $value;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function offPeak(): ?OffPeakWindow
    {
        return $this->offPeak;
    }
}
