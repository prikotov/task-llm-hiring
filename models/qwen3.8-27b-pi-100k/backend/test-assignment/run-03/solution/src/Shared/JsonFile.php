<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Чтение JSON-файла: верхний уровень — объект.
 * Любая ошибка (файл отсутствует, некорректный JSON) — InputFormatException.
 */
final class JsonFile
{
    public static function loadObject(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InputFormatException('файл не найден или недоступен: '.$path);
        }

        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new InputFormatException('не удалось прочитать файл: '.$path);
        }

        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        }

        try {
            $data = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputFormatException('некорректный JSON в файле '.$path.': '.$e->getMessage(), previous: $e);
        }

        if (!\is_array($data) || array_is_list($data)) {
            throw new InputFormatException('корень файла '.$path.' должен быть объектом JSON');
        }

        return $data;
    }
}
