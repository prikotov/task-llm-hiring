<?php

declare(strict_types=1);

namespace App\Billing\Import;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Типизированный доступ к разобранному JSON с понятными ошибками.
 */
final class Json
{
    /**
     * @param mixed  $data
     * @param string $context человекоразбираемая позиция (например "providers[2].code")
     *
     * @throws ImportException
     */
    public static function array(mixed $data, string $context, bool $nonEmpty = false): array
    {
        if (!\is_array($data)) {
            throw new ImportException(sprintf('%s: ожидался массив (объект/массив JSON), получен тип "%s".', $context, \gettype($data)));
        }
        if ($nonEmpty && $data === []) {
            throw new ImportException(sprintf('%s: ожидался непустой массив.', $context));
        }

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @throws ImportException
     */
    public static function stringField(array $data, string $field, string $context): string
    {
        if (!\array_key_exists($field, $data)) {
            throw new ImportException(sprintf('%s: отсутствует обязательное поле "%s".', $context, $field));
        }
        $value = $data[$field];
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" должно быть непустой строкой.', $context, $field));
        }

        return $value;
    }

    /**
     * Десятичная строка: непараметризованное неотрицательное число, до 16 знаков после точки.
     *
     * @throws ImportException
     */
    public static function decimalField(array $data, string $field, string $context): string
    {
        $value = self::stringField($data, $field, $context);
        if (preg_match('/^\d+(\.\d{1,16})?$/', $value) !== 1) {
            throw new ImportException(
                sprintf('%s: поле "%s" должно быть неотрицательным десятичным числом (до 16 знаков после точки), получено "%s".', $context, $field, $value)
            );
        }

        return $value;
    }

    /**
     * Целое число ≥ 0 (int из JSON или строка с целым — при JSON_BIGINT_AS_STRING).
     *
     * @param mixed $value
     *
     * @throws ImportException
     */
    public static function nonNegativeInt(mixed $value, string $context): int
    {
        if (\is_int($value)) {
            if ($value < 0) {
                throw new ImportException(sprintf('%s: целое должно быть ≥ 0, получено %d.', $context, $value));
            }

            return $value;
        }
        if (\is_string($value) && preg_match('/^\d+$/', $value) === 1 && $value <= (string) \PHP_INT_MAX) {
            return (int) $value;
        }

        throw new ImportException(sprintf('%s: ожидается целое число ≥ 0, получен тип "%s".', $context, \is_scalar($value) ? \gettype($value) : 'объект'));
    }

    /**
     * Мгновенное время: строка ISO-8601 (с офсетом/«Z» или без), приводится к UTC.
     * Чистые даты (без времени) не принимаются: это моменты времени.
     *
     * @param mixed $value
     *
     * @throws ImportException
     */
    public static function utcDateTime(mixed $value, string $context, string $field): DateTimeImmutable
    {
        if (!\is_string($value) || !preg_match(
            '/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}(:\d{2}(\.\d{1,9})?)?(Z|\+\d{2}:?\d{2}|\-\d{2}:?\d{2})?$/',
            $value
        ) === 1) {
            throw new ImportException(
                sprintf('%s: поле "%s" должно быть моментом времени ISO-8601 (например "2026-08-01T00:00:00Z"), получено "%s".', $context, $field, \is_string($value) ? $value : \gettype($value))
            );
        }

        try {
            $dt = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ImportException(sprintf('%s: поле "%s" — некорректное значение "%s": %s.', $context, $field, $value, $e->getMessage()));
        }

        return $dt->setTimezone(new DateTimeZone('UTC'));
    }
}
