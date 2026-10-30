<?php

declare(strict_types=1);

namespace App\Application\Parse;

use App\Domain\Shared\TimeOfDay;
use App\Exception\InvalidPricesFileException;
use App\Exception\InvalidUsageFileException;
use DateTimeImmutable;

/**
 * Общие проверки полей входных JSON/JSONL-файлов (история цен и события).
 * Все ошибки — предметные исключения с контекстом (раздел/строка/поле).
 */
final class JsonInput
{
    /**
     * Строка «HH:MM» (UTC) → минуты от 00:00.
     *
     * @throws InvalidPricesFileException|InvalidUsageFileException
     */
    public static function minutesHhmm(
        array $data,
        string $field,
        string $context,
        string $exception,
    ): int {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new $exception(sprintf('%s: field "%s" must be a string "HH:MM" (UTC)', $context, $field));
        }

        try {
            return TimeOfDay::parseHhmm($data[$field], sprintf('%s: field "%s"', $context, $field));
        } catch (\InvalidArgumentException $e) {
            throw new $exception($e->getMessage());
        }
    }

    /**
     * Десятичная строка-ставка: не отрицательная, не больше 32 знаков,
     * не больше 10 знаков после точки — бcmath-масштаб расчёта.
     *
     * @throws InvalidPricesFileException|InvalidUsageFileException
     */
    public static function decimalString(
        array $data,
        string $field,
        string $context,
        string $exception,
    ): string {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new $exception(sprintf('%s: field "%s" must be a decimal string', $context, $field));
        }

        $value = $data[$field];
        if (!preg_match('/^\d{1,21}(\.\d{1,10})?$/', $value)) {
            throw new $exception(sprintf('%s: field "%s" must be a non-negative decimal string (e.g. "3.000000"), got "%s"', $context, $field, $value));
        }

        return $value;
    }

    /**
     * ISO-8601 время, всегда UTC (с суффиксом Z) — момент по п. задания.
     *
     * @throws InvalidPricesFileException|InvalidUsageFileException
     */
    public static function isoUtcDateTime(
        array $data,
        string $field,
        string $context,
        string $exception,
    ): DateTimeImmutable {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new $exception(sprintf('%s: field "%s" must be an ISO-8601 UTC datetime string', $context, $field));
        }

        $value = $data[$field];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:[.,]\d{1,6})?Z$/', $value)) {
            throw new $exception(sprintf('%s: field "%s" must be ISO-8601 UTC (…Z), got "%s"', $context, $field, $value));
        }

        $normalized = str_replace(',', '.', $value);

        return (new DateTimeImmutable($normalized))->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * Строка (не пустая после trim), обязательная.
     *
     * @throws InvalidPricesFileException|InvalidUsageFileException
     */
    public static function requiredString(
        array $data,
        string $field,
        string $context,
        string $exception,
    ): string {
        if (!array_key_exists($field, $data) || !is_string($data[$field]) || trim($data[$field]) === '') {
            throw new $exception(sprintf('%s: field "%s" must be a non-empty string', $context, $field));
        }

        return $data[$field];
    }

    /**
     * Целое ≥ 0 (JSON int, без float и строк).
     *
     * @throws InvalidPricesFileException|InvalidUsageFileException
     */
    public static function nonNegativeInt(
        array $data,
        string $field,
        string $context,
        string $exception,
    ): int {
        if (!array_key_exists($field, $data) || !is_int($data[$field]) || $data[$field] < 0) {
            throw new $exception(sprintf('%s: field "%s" must be an integer >= 0', $context, $field));
        }

        return $data[$field];
    }
}
