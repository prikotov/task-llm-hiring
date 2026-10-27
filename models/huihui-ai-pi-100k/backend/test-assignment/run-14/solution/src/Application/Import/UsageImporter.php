<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Entity\UsageEvent;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка журнала событий (JSONL).
 *
 * Правила:
 *  - идемпотентный по key (provider, providerRequestId) — п. 5;
 *  - некорректная строка — бросает DomainException → exit(1) на уровне
 *    команды; корректная строка — не зависит от наличия справочника
 *    (провайдер/модель/цены могут приходить позже — unrated до rate).
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly UsageEventRepository $events,
    ) {
    }

    /** @return array{total: int, inserted: int, duplicated: int} */
    public function import(string $file): array
    {
        if (!is_readable($file)) {
            throw new \DomainException(sprintf('Файл не найден: %s', $file));
        }
        $fh = fopen($file, 'rb');
        if ($fh === false) {
            throw new \DomainException(sprintf('Не удалось открыть файл: %s', $file));
        }

        $lineNo = 0;
        $inserted = 0;
        $duplicated = 0;

        try {
            while (($line = fgets($fh)) !== false) {
                $lineNo++;
                $line = rtrim($line, "\r\n");
                if ($line === '' || $line === "") {
                    continue; // пустые строки допускаем (хвост, EOF-перенос)
                }

                $raw = json_decode($line, true);
                if (!\is_array($raw) || (\is_string($raw))) {
                    throw new \DomainException(sprintf(
                        'Строка %d: некорректный JSON: %s',
                        $lineNo,
                        mb_substr($line, 0, 160),
                    ));
                }

                $event = $this->parseEvent($raw, $lineNo);

                $existing = $this->events->byNaturalKey($event->provider, $event->providerRequestId);
                if ($existing !== null) {
                    $duplicated++;
                    continue; // п. 5: всяко игнорируем; никаких обновлений
                }

                $this->em->persist($event);
                $inserted++;
            }

            $this->em->flush();

            return ['total' => $lineNo, 'inserted' => $inserted, 'duplicated' => $duplicated];
        } finally {
            fclose($fh);
        }
    }

    /** @param array<int, mixed> $raw */
    private function parseEvent(array $raw, int $lineNo): UsageEvent
    {
        $required = ['provider', 'provider_request_id', 'model', 'user', 'ts'];
        foreach ($required as $k) {
            if (!\array_key_exists($k, $raw) || $raw[$k] === null || $raw[$k] === '') {
                throw new \DomainException(sprintf('Строка %d: отсутствует поле %s', $lineNo, $k));
            }
        }
        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $k) {
            if (!\array_key_exists($k, $raw) || $raw[$k] === null) {
                throw new \DomainException(sprintf('Строка %d: отсутствует поле %s', $lineNo, $k));
            }
        }

        $provider = (string) $raw['provider'];
        $requestId = (string) $raw['provider_request_id'];
        $model = (string) $raw['model'];
        $user = (string) $raw['user'];
        $tsRaw = (string) $raw['ts'];
        if ($provider === '' || $requestId === '' || $model === '' || $user === '') {
            throw new \DomainException(sprintf('Строка %d: пустое значение обязательного поля', $lineNo));
        }
        // Отчёт ожидает `model` в форме «provider/code» — фиксируем при импорте.
        $model = $provider.'/'.$model;

        $ts = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $tsRaw);
        if ($ts === false) {
            throw new \DomainException(sprintf('Строка %d: ts не в ISO-8601 ISO: %s', $lineNo, $tsRaw));
        }
        $ts = $ts->setTimezone(new \DateTimeZone('UTC'));

        $tokens = ['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'];
        $values = [];
        foreach ($tokens as $k) {
            $v = $raw[$k];
            if (\is_float($v) || \is_bool($v)) {
                throw new \DomainException(sprintf('Строка %d: %s должно быть целым ≥ 0', $lineNo, $k));
            }
            if (!\is_int($v) && !preg_match('/^\d+$/', (string) $v)) {
                throw new \DomainException(sprintf('Строка %d: %s должно быть целым ≥ 0 (число/строка), получено: %s', $lineNo, $k, json_encode($v)));
            }
            $values[$k] = (int) $v;
        }

        return new UsageEvent(
            provider: $provider,
            providerRequestId: $requestId,
            model: $model,
            user: $user,
            ts: $ts,
            tokensInput: $values['tokens_input'],
            tokensOutput: $values['tokens_output'],
            tokensCacheRead: $values['tokens_cache_read'],
            tokensCacheWrite: $values['tokens_cache_write'],
        );
    }
}
