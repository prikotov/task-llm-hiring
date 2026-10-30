<?php

declare(strict_types=1);

namespace App\Infra\Input;

use DateTimeImmutable;
use JsonException;

/** Читает JSON / JSONL файлы, возвращает массив данных (без валидации). */
final class FileReader
{
    /** @return array<string, mixed> массив из JSON-объекта */
    public static function readJson(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('file not found: %s', $path));
        }
        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            throw new \RuntimeException(sprintf('file is empty: %s', $path));
        }
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException(sprintf('invalid JSON: %s', $e->getMessage()), 1, $e);
        }
        if (!is_array($data)) {
            throw new \RuntimeException('JSON root must be an object');
        }

        return $data;
    }

    /** @return array<int, array<string, mixed>> каждый элемент = одна строка JSONL (объект) */
    public static function readJsonl(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('file not found: %s', $path));
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException(sprintf('cannot read: %s', $path));
        }

        $lines = preg_split('/\r?\n/', $raw) ?: [''];
        $result = [];
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            try {
                $obj = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new \RuntimeException(sprintf('invalid JSONL on line %d: %s', $i + 1, $e->getMessage()), 1, $e);
            }
            if (!is_array($obj)) {
                throw new \RuntimeException(sprintf('JSONL line %d: object expected', $i + 1));
            }
            $result[] = $obj;
        }

        return $result;
    }
}
