<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Application\Catalog\CatalogParser;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём журнала событий. Дедупликация по (provider, provider_request_id):
 * повторная доставка уже известной строки игнорируется целиком — без
 * обновлений и доначислений (зафиксированная стоимость не меняется).
 */
final class UsageImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(string $file): void
    {
        if (!is_file($file)) {
            throw new ImportException("Файл не найден: $file");
        }
        $raw = @file($file, flags: FILE_IGNORE_NEW_LINES);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: $file");
        }

        $newEvents = [];
        // Ключи, уже принятые в этом прогоне: финальный flush один раз в конце
        // файла, поэтому не сфлашированные строки не видны из SQL-проверки,
        // и дубли в пределах одного файла ловим здесь.
        $seenInRun = [];
        foreach ($raw as $number => $line) {
            $lineNo = $number + 1;
            if (trim($line) === '') {
                continue; // пустые строки допускаем
            }
            $where = sprintf('строка %d', $lineNo);
            $spec = json_decode($line, true);
            if (!is_array($spec)) {
                throw new ImportException("{$where}: некорректный JSON (" . json_last_error_msg() . ")");
            }

            $provider = self::string($spec, 'provider', $where);
            $requestId = self::string($spec, 'provider_request_id', $where);
            $model = self::string($spec, 'model', $where);
            $user = self::string($spec, 'user', $where);
            $ts = CatalogParser::parseTimestamp($spec['ts'] ?? null, 'ts', $where);

            $key = $provider . "\u{0001}" . $requestId;
            if (isset($seenInRun[$key])) {
                continue; // дубль внутри этого же файла
            }

            $tokens = [
                'input' => self::token($spec, 'tokens_input', $where),
                'output' => self::token($spec, 'tokens_output', $where),
                'cache_read' => self::token($spec, 'tokens_cache_read', $where),
                'cache_write' => self::token($spec, 'tokens_cache_write', $where),
            ];

            // Натуральный запрос к БД: ORM findBy не видит ещё не сфлашеные
            // сущестии этой же транзакции, а повторный INSERT упёрся бы
            // в уникальное ограничение (provider_code, provider_request_id).
            $stmt = $this->em->getConnection()
                ->executeQuery(
                    'SELECT 1 FROM usage_events WHERE provider_code = :p AND provider_request_id = :r LIMIT 1',
                    ['p' => $provider, 'r' => $requestId],
                );

            if ($stmt->fetchOne() !== false) {
                $seenInRun[$key] = true;
                continue; // дубль — игнорируем целиком
            }
            $seenInRun[$key] = true;

            $event = new UsageEvent();
            $event->setProviderCode($provider);
            $event->setProviderRequestId($requestId);
            $event->setModelCode($model);
            $event->setUser($user);
            $event->setTs($ts);
            $event->setTokens($tokens['input'], $tokens['output'], $tokens['cache_read'], $tokens['cache_write']);
            $this->em->persist($event);
            $newEvents[] = $event;
        }

        if ($newEvents !== []) {
            $this->em->flush();
        }
    }

    private static function string(array $spec, string $field, string $where): string
    {
        $value = $spec[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new ImportException("$where.$field: ожидается непустая строка");
        }

        return $value;
    }

    private static function token(array $spec, string $field, string $where): int
    {
        $value = $spec[$field] ?? 0;
        if (is_bool($value) || (!is_int($value) && !(is_float($value) && $value == floor($value) && $value < PHP_INT_MAX))) {
            throw new ImportException("$where.$field: ожидаются целые токены >= 0");
        }
        $value = (int) $value;
        if ($value < 0) {
            throw new ImportException("$where.$field: количество токенов не может быть отрицательным");
        }

        return $value;
    }
}
