<?php

declare(strict_types=1);

namespace App\Support;

use App\Shared\Exception\BadInputException;

/**
 * Чтение JSON-файлов справочника (история цен, файлы коррекции).
 * Все ошибки валидации — BadInputException (команда завершится с кодом 1).
 */
final class JsonFile
{
    /** @return array<string, mixed> */
    public static function readObject(string $path): array
    {
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            throw new BadInputException("Не удалось прочитать файл: {$path}");
        }

        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadInputException(sprintf(
                'Файл %s: некорректный JSON (%s)',
                $path,
                json_last_error_msg(),
            ));
        }

        if (!is_array($data) || array_is_list($data)) {
            throw new BadInputException("Файл {$path}: на верхнем уровне ожидается JSON-объект");
        }

        return $data;
    }

    /** @return list<mixed> */
    public static function listSection(array $data, string $key, bool $required = true): array
    {
        if (!array_key_exists($key, $data)) {
            if ($required) {
                throw new BadInputException("Отсутствует секция «{$key}»");
            }

            return [];
        }

        $value = $data[$key];
        if (!is_array($value) || !array_is_list($value)) {
            throw new BadInputException("Секция «{$key}» должна быть массивом объектов");
        }

        return $value;
    }

    /** @return array<string, mixed> */
    public static function item(array $list, int $index, string $section): array
    {
        $item = $list[$index];
        if (!is_array($item) || array_is_list($item)) {
            throw new BadInputException("{$section}[{$index}]: ожидается JSON-объект");
        }

        return $item;
    }
}
