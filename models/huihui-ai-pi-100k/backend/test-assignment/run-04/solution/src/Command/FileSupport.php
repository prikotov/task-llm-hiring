<?php

declare(strict_types=1);

namespace App\Command;

final class FileSupport
{
    public static function read(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException(sprintf('Файл недоступен: %s', $path));
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        return $content;
    }
}
