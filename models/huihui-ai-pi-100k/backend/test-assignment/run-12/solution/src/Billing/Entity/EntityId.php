<?php

declare(strict_types=1);

namespace App\Billing\Entity;

/**
 * Детерминированные естественные ключи сущностей.
 * Символ-разделитель \x01 не ожидается в данных (provider/model/request-id/user/ts).
 */
final class EntityId
{
    private const SEP = "\x01";

    public static function event(string $providerCode, string $requestId): string
    {
        return $providerCode . self::SEP . $requestId;
    }

    public static function model(string $providerCode, string $code): string
    {
        return $providerCode . self::SEP . $code;
    }

    public static function priceRow(string $providerCode, string $modelCode, \DateTimeImmutable $validFromUtc): string
    {
        return $providerCode . self::SEP
            . $modelCode . self::SEP
            . $validFromUtc->format('Y-m-d\TH:i:s\Z');
    }

    /** @return array{0:string,1:string} (provider, priceRowId parts) */
    public static function decodePriceRow(string $id): array
    {
        $parts = explode(self::SEP, $id);

        return [$parts[0], $parts[1] ?? '', $parts[2] ?? ''];
    }
}
