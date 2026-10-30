<?php

declare(strict_types=1);

namespace App\Billing\Parser;

use App\Billing\Entity\EntityId;
use App\Billing\Entity\UsageEvent;
use App\Billing\Store\Store;
use JsonException;

/**
 * Парсинг JSONL файла событий использования (TASK.md § «События использования»).
 */
final class UsageLoader
{
    public function __construct(
        private readonly Store $store,
    ) {}

    /**
     * @return array{inserted:int, duplicates:int, invalid:int}
     */
    public function load(string $path, int $strictInvalid = 1): array
    {
        $stats = ['inserted' => 0, 'duplicates' => 0, 'invalid' => 0];
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $path));
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException(sprintf('Cannot open file: %s', $path));
        }

        $lineno = 0;
        while (($line = fgets($handle)) !== false) {
            $lineno++;
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            try {
                $event = $this->parseLine($line);
            } catch (\Throwable $e) {
                if ($strictInvalid !== 0) {
                    fclose($handle);
                    throw new \RuntimeException(sprintf('Invalid usage event at line %d: %s', $lineno, $e->getMessage()), 0, $e);
                }
                $stats['invalid']++;
                continue;
            }

            try {
                $new = $this->store->insertUsageEventIfAbsent($event);
            } catch (\Throwable $e) {
                fclose($handle);
                throw new \RuntimeException(sprintf('Cannot insert event at line %d: %s', $lineno, $e->getMessage()), 0, $e);
            }

            if ($new) {
                $stats['inserted']++;
            } else {
                $stats['duplicates']++;
            }
        }

        fclose($handle);
        $this->store->flush();

        return $stats;
    }

    private function parseLine(string $line): UsageEvent
    {
        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \JsonException('Invalid JSON: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($data)) {
            throw new \RuntimeException('Event must be a JSON object.');
        }

        $provider = self::stringField($data, 'provider');
        $requestId = self::stringField($data, 'provider_request_id');
        $model = self::stringField($data, 'model');
        $user = self::stringField($data, 'user');
        $tsRaw = self::stringField($data, 'ts');

        try {
            $ts = new \DateTimeImmutable($tsRaw, new \DateTimeZone('UTC'));
            $ts = $ts->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Invalid ts "%s".', $tsRaw), 0, $e);
        }

        // Валидация ключа события
        $id = EntityId::event($provider, $requestId);

        // Валидация модельной принадлежности
        $modelId = EntityId::model($provider, $model);
        if ($this->store->model($modelId) === null) {
            throw new \RuntimeException(sprintf('Unknown model "%s/%s" (provider, code).', $provider, $model));
        }

        // Валидация providers
        if ($this->store->provider($provider) === null) {
            throw new \RuntimeException(sprintf('Unknown provider "%s".', $provider));
        }

        $in = self::tokenCount($data, 'tokens_input');
        $out = self::tokenCount($data, 'tokens_output');
        $cr = self::tokenCount($data, 'tokens_cache_read');
        $cw = self::tokenCount($data, 'tokens_cache_write');

        $event = new UsageEvent($id);
        $event->providerCode = $provider;
        $event->requestId = $requestId;
        $event->modelCode = $model;
        $event->user = $user;
        $event->ts = $ts;
        $event->tokens = [$in, $out, $cr, $cw];
        $event->isRated = false;

        return $event;
    }

    /** @param array<string, mixed> $data */
    private static function tokenCount(array $data, string $key): int
    {
        $v = $data[$key] ?? 0;
        if (is_bool($v) || $v === null) {
            throw new \RuntimeException(sprintf('Token count "%s" is invalid.', $key));
        }
        if (is_int($v)) {
            if ($v < 0) {
                throw new \RuntimeException(sprintf('Token count "%s" must be >= 0.', $key));
            }

            return $v;
        }
        if (is_string($v)) {
            if (!preg_match('/^\d+$/', $v)) {
                throw new \RuntimeException(sprintf('Token count "%s" must be a non-negative integer.', $key));
            }

            return (int) $v;
        }
        throw new \RuntimeException(sprintf('Token count "%s" must be an integer.', $key));
    }

    /** @param array<string, mixed> $a */
    private static function stringField(array $a, string $key): string
    {
        if (!array_key_exists($key, $a)) {
            throw new \RuntimeException(sprintf('Missing key "%s".', $key));
        }
        $v = $a[$key];
        if (is_string($v) && $v !== '') {
            return $v;
        }

        throw new \RuntimeException(sprintf('Field "%s" must be a non-empty string.', $key));
    }
}
