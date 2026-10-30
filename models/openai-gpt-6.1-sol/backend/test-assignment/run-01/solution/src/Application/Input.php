<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Tariff;

final class Input
{
    public static function timestamp(mixed $value): string
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/D', $value)) {
            throw new \InvalidArgumentException('Ожидается ISO-8601 UTC с суффиксом Z (до 6 знаков долей секунды).');
        }
        $date = new \DateTimeImmutable($value);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($errors !== false && ($errors['warning_count'] || $errors['error_count'])) {
            throw new \InvalidArgumentException('Некорректная дата: ' . $value);
        }

        return $date->format('Y-m-d\TH:i:s.u\Z');
    }

    public static function text(array $row, string $key, ?int $max = 255): string
    {
        $value = $row[$key] ?? null;
        if (!is_string($value) || $value === '' || str_contains($value, "\0") || ($max !== null && mb_strlen($value) > $max)) {
            throw new \InvalidArgumentException('Некорректное строковое поле: ' . $key);
        }

        return $value;
    }

    public static function object(mixed $value): array
    {
        if (!is_array($value) || array_is_list($value) && $value !== []) {
            throw new \InvalidArgumentException('Ожидается объект JSON.');
        }

        return $value;
    }

    public static function json(string $json): array
    {
        return self::object(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
    }

    public static function file(string $path): array
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            throw new \InvalidArgumentException('Не удалось прочитать файл: ' . $path);
        }

        return self::json($data);
    }

    public static function rates(array $row): array
    {
        $unit = $row['unit'] ?? null;
        $kinds = match ($unit) {
            'per_1m_tokens' => Tariff::KINDS,
            'per_1m_tokens_unified' => ['any'],
            default => throw new \InvalidArgumentException('Неизвестная схема тарификации.'),
        };
        if (($row['currency'] ?? null) !== 'USD') {
            throw new \InvalidArgumentException('Поддерживается только USD.');
        }
        $rates = self::object($row['rates'] ?? null);
        foreach ($kinds as $kind) {
            if (!isset($rates[$kind]) || !is_string($rates[$kind]) || !preg_match('/^\d+\.\d{6}$/D', $rates[$kind])) {
                throw new \InvalidArgumentException('Цена должна быть неотрицательной десятичной строкой с 6 знаками: ' . $kind);
            }
        }
        if (count($rates) !== count($kinds)) {
            throw new \InvalidArgumentException('Лишние виды токенов в цене.');
        }

        return $rates;
    }

    public static function offPeak(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }
        $value = self::object($value);
        foreach (['window_start', 'window_end'] as $key) {
            if (!is_string($value[$key] ?? null) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $value[$key])) {
                throw new \InvalidArgumentException('Некорректное окно скидки.');
            }
        }
        if (!is_string($value['multiplier'] ?? null) || !preg_match('/^\d+(?:\.\d+)?$/D', $value['multiplier'])) {
            throw new \InvalidArgumentException('Множитель должен быть неотрицательной десятичной строкой.');
        }

        return $value;
    }
}
