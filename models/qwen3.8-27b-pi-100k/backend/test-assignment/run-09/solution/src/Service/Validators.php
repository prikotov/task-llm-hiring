<?php

declare(strict_types=1);

namespace App\Service;

use function is_string;

/**
 * Общие валидаторы входящих данных (provider/model codes, decimals, assoc-объекты).
 */
final class Validators
{
    /**
     * Обязательное строковое поле (непустое).
     *
     * @param array<string, mixed> $arr
     */
    public static function string(array $arr, string $key): string
    {
        if (!\array_key_exists($key, $arr) || !is_string($arr[$key]) || '' === trim($arr[$key])) {
            throw new \DomainException(sprintf('Поле "%s" отсутствует или не является строкой', $key));
        }

        return trim($arr[$key]);
    }

    /**
     * Обязательное десятичное неотрицательное значение (строкой).
     *
     * @param array<string, mixed> $arr
     */
    public static function decimal(array $arr, string $key): string
    {
        if (!\array_key_exists($key, $arr)) {
            throw new \DomainException(sprintf('Поле "%s" отсутствует', $key));
        }

        $v = $arr[$key];
        if (!is_string($v) && !is_int($v) && !is_float($v)) {
            throw new \DomainException(sprintf('Поле "%s" должно быть строкой/числом', $key));
        }

        return self::normalize((string) $v, $key);
    }

    /**
     * Обязательный непустой объект (ассоциативный массив).
     *
     * @return array<string, mixed>
     */
    public static function assoc(string $key, mixed $value): array
    {
        if (!\is_array($value) || [] === $value) {
            throw new \DomainException(sprintf('Поле "%s" должно быть непустым объектом', $key));
        }

        return $value;
    }

    public static function normalize(string $raw, string $label): string
    {
        $raw = trim($raw);
        if (!preg_match('/^\d{1,20}(\.\d{1,16})?$/', $raw)) {
            throw new \DomainException(sprintf('Некорректное десятичное значение (%s): "%s" — требуется неотрицательное число', $label, $raw));
        }

        return $raw;
    }

    /**
     * Обязательный список (массив) объектов; допускается пустой.
     *
     * @param array<string, mixed> $arr
     *
     * @return list<array<string, mixed>>
     */
    public static function list(array $arr, string $key): array
    {
        if (!\array_key_exists($key, $arr)) {
            return [];
        }

        $value = $arr[$key];
        if (!\is_array($value)) {
            throw new \DomainException(sprintf('Поле "%s" должно быть списком объектов', $key));
        }

        return array_values($value);
    }
}
