<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Загрузка входных файлов (JSON / JSONL) с контролируемой ошибкой.
 */
final class Input
{
    /**
     * @return array<string, mixed>
     */
    public static function loadJson(string $path): array
    {
        $content = self::readFile($path);
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \RuntimeException('expect a JSON object at the top level');
        }

        return $data;
    }

    /**
     * JSONL: объект на каждой непустой строке.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function loadJsonl(string $path): array
    {
        $lines = self::readFile($path);
        $rows = [];

        foreach (explode("\n", $lines) as $lineNo => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            try {
                $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException('line ' . ($lineNo + 1) . ': invalid JSON — ' . $e->getMessage());
            }

            if (!is_array($row)) {
                throw new \RuntimeException('line ' . ($lineNo + 1) . ': expect a JSON object');
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private static function readFile(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException("file not found or not readable: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("cannot read file: {$path}");
        }

        return $content;
    }
}
