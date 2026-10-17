<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\UsageRow;

/**
 * Загрузка события использования из JSONL-файла: одна строка — одно событие,
 * идемпотентный upsert по естественному ключу (provider, provider_request_id).
 *
 * Первая строка с данным ключом выигрывает; последующие дубли
 * полностью игнорируются (п. 5).
 */
final class UsageLineLoader
{
    public function load(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException("usage file not found: $path");
        }

        $rows = [];
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException("cannot open usage file: $path");
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $row = $this->parseLine($line);
                $key = $row->getProvider() . '.' . $row->getProviderRequestId();
                if (!isset($rows[$key])) {
                    $rows[$key] = $row;
                }
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    /**
     * @return array<string, UsageRow>
     */
    private function parseLine(string $line): UsageRow
    {
        $data = json_decode($line, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \InvalidArgumentException("invalid usage line: " . json_last_errmsg());
        }

        foreach (['provider', 'provider_request_id', 'model', 'user', 'ts',
            'tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null) {
                throw new \InvalidArgumentException("missing field in usage line: $field");
            }
        }

        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $field) {
            $value = $data[$field];
            if (!is_int($value) || $value < 0) {
                throw new \RuntimeException("field must be a non-negative integer: $field");
            }
        }

        return new UsageRow(
            (string) $data['provider'],
            (string) $data['provider_request_id'],
            (string) $data['model'],
            (string) $data['user'],
            (string) $data['ts'],
            (int) $data['tokens_input'],
            (int) $data['tokens_output'],
            (int) $data['tokens_cache_read'],
            (int) $data['tokens_cache_write'],
        );
    }
}
