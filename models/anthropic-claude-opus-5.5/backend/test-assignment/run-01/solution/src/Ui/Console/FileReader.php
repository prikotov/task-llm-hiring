<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Shared\InvalidInput;

final class FileReader
{
    public static function contents(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidInput(sprintf('Файл не найден или недоступен: %s', $path));
        }
        $c = file_get_contents($path);
        if ($c === false) {
            throw new InvalidInput(sprintf('Не удалось прочитать файл: %s', $path));
        }

        return $c;
    }

    /** @return \Generator<string> */
    public static function lines(string $path): \Generator
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidInput(sprintf('Файл не найден или недоступен: %s', $path));
        }
        $h = fopen($path, 'rb');
        if ($h === false) {
            throw new InvalidInput(sprintf('Не удалось открыть файл: %s', $path));
        }
        try {
            while (($line = fgets($h)) !== false) {
                yield rtrim($line, "\r\n");
            }
        } finally {
            fclose($h);
        }
    }
}
