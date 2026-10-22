<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class InputData
{
    /** @return array<string, mixed> */
    public static function decodeObject(string $json, string $context): array
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new InvalidArgumentException(sprintf('%s: некорректный JSON: %s', $context, $exception->getMessage()), 0, $exception);
        }

        if (!is_array($data) || array_is_list($data)) {
            throw new InvalidArgumentException(sprintf('%s: ожидается JSON-объект', $context));
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    public static function requiredString(array $data, string $key, string $context): string
    {
        $value = $data[$key] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException(sprintf('%s: поле "%s" должно быть непустой строкой', $context, $key));
        }

        return $value;
    }

    public static function utcTimestamp(mixed $value, string $context): DateTimeImmutable
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/D', $value)) {
            throw new InvalidArgumentException(sprintf('%s: timestamp должен быть ISO-8601 в UTC с суффиксом Z', $context));
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s.u\Z', self::withMicroseconds($value), new DateTimeZone('UTC'));
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new InvalidArgumentException(sprintf('%s: некорректная дата "%s"', $context, $value));
        }

        return $date;
    }

    public static function decimal(mixed $value, string $context, ?int $requiredScale = null): string
    {
        if (!is_string($value) || !preg_match('/^\d+(?:\.\d+)?$/D', $value)) {
            throw new InvalidArgumentException(sprintf('%s: ожидается неотрицательная десятичная строка', $context));
        }
        if ($requiredScale !== null && !preg_match('/^\d+\.\d{'.$requiredScale.'}$/D', $value)) {
            throw new InvalidArgumentException(sprintf('%s: требуется ровно %d знаков после точки', $context, $requiredScale));
        }
        if (strlen($value) > 36) {
            throw new InvalidArgumentException(sprintf('%s: слишком большое десятичное значение', $context));
        }

        return $value;
    }

    public static function timeOfDay(mixed $value, string $context): string
    {
        if (!is_string($value) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $value)) {
            throw new InvalidArgumentException(sprintf('%s: время должно иметь формат HH:MM', $context));
        }

        return $value;
    }

    public static function sqlTimestamp(DateTimeImmutable $date): string
    {
        return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.uP');
    }

    private static function withMicroseconds(string $value): string
    {
        $withoutZulu = substr($value, 0, -1);
        if (!str_contains($withoutZulu, '.')) {
            return $withoutZulu.'.000000Z';
        }

        [$seconds, $fraction] = explode('.', $withoutZulu, 2);
        return $seconds.'.'.str_pad($fraction, 6, '0'). 'Z';
    }
}
