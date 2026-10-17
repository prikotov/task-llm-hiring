<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Result\UsageImportSummary;
use App\Domain\Billing\Iso8601;
use App\Domain\Exception\BillingException;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования из JSONL-файла (по одному событию в строке).
 *
 * Правила приёма (п. 5 задания):
 *  - дедупликация по (provider, provider_request_id): повторная доставка
 *    уже известного события целиком игнорируется — без обновлений и
 *    доначислений (и внутри файла, и относительно базы);
 *  - одинаковые provider_request_id у разных провайдеров — разные события;
 *  - события не обязаны ссылаться на существующего провайдера/модель:
 *    поток событий может опережать справку, такие события просто
 *    остаются unrated до появления цен;
 *  - некорректная строка — ошибка всего импорта (код выхода 1).
 */
final class UsageEventImporter
{
    private const TOKEN_FIELDS = [
        'tokens_input',
        'tokens_output',
        'tokens_cache_read',
        'tokens_cache_write',
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function import(string $file): UsageImportSummary
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new BillingException(sprintf('Файл не найден или недоступен для чтения: %s', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new BillingException(sprintf('Не удалось прочитать файл: %s', $file));
        }

        /** @var list<array{0: string, 1: UsageEvent}> $uniqueLines уникальные (по ключу) строки файла */
        $uniqueLines = [];
        $inFileDuplicates = 0;
        $seen = [];

        $lineNo = 0;
        foreach (preg_split('/\R/', $raw) ?: [] as $line) {
            $lineNo++;
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue; // пустые строки (в т.ч. завершающий перевод строки) не считаются строками данных
            }

            $event = $this->parseLine($trimmed, $lineNo);
            $key = $event->providerCode()."\0".$event->providerRequestId();
            if (isset($seen[$key])) {
                $inFileDuplicates++; // повторное вхождение в том же файле
                continue;
            }
            $seen[$key] = true;
            $uniqueLines[] = [$key, $event];
        }

        // Уже принятые события с таким же (provider, provider_request_id) — дубли.
        $existing = $this->findExistingKeys(array_keys($seen));
        $created = 0;
        $databaseDuplicates = 0;
        $newEvents = [];
        foreach ($uniqueLines as [$key, $event]) {
            if (\array_key_exists($key, $existing)) {
                $databaseDuplicates++;
                continue;
            }
            $newEvents[] = $event;
            $created++;
        }

        if ($newEvents !== []) {
            $this->em->wrapInTransaction(function () use ($newEvents): void {
                foreach ($newEvents as $event) {
                    $this->em->persist($event);
                }
            });
        }

        return new UsageImportSummary(
            \count($uniqueLines) + $inFileDuplicates, // всего строк данных в файле
            $created, // создано в базе
            $inFileDuplicates + $databaseDuplicates, // проигнорировано как дубли
        );
    }

    /**
     * Разбирает и проверяет одну строку; возвращает сформированное событие.
     */
    private function parseLine(string $line, int $lineNo): UsageEvent
    {
        $context = sprintf('строка %d JSONL', $lineNo);

        try {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BillingException(sprintf('Некорректный JSON на %s: %s', $context, $exception->getMessage()), 0, $exception);
        }

        if (!\is_array($data) || array_is_list($data)) {
            throw new BillingException(sprintf('%s: событие должно быть JSON-объектом', $context));
        }

        $providerCode = self::nonEmptyString($data, 'provider', $context);
        $providerRequestId = self::nonEmptyString($data, 'provider_request_id', $context);
        $modelCode = self::nonEmptyString($data, 'model', $context);
        $user = self::nonEmptyString($data, 'user', $context);
        $ts = Iso8601::parseUtc(self::nonEmptyString($data, 'ts', $context));

        $tokens = [];
        foreach (self::TOKEN_FIELDS as $field) {
            $value = $data[$field] ?? null;
            if (!\is_int($value) || $value < 0) {
                throw new BillingException(sprintf('%s: поле "%s" должно быть целым числом >= 0', $context, $field));
            }
            $tokens[] = $value;
        }

        return new UsageEvent(
            $providerCode,
            $providerRequestId,
            $modelCode,
            $user,
            $ts,
            ...$tokens,
        );
    }

    /**
     * Находит уже принятые ключи (provider, provider_request_id).
     *
     * @param string[] $pairKeys строки "provider\0requestId"
     *
     * @return array<string, true> карта найденных ключей
     */
    private function findExistingKeys(array $pairKeys): array
    {
        if ($pairKeys === []) {
            return [];
        }

        $providerCodes = [];
        $requestIds = [];
        foreach ($pairKeys as $pairKey) {
            [$providerCode, $requestId] = explode("\0", $pairKey, 2);
            $providerCodes[] = $providerCode;
            $requestIds[] = $requestId;
        }

        $qb = $this->events->createQueryBuilder('e')
            ->where('e.providerCode IN (:providerCodes)')
            ->andWhere('e.providerRequestId IN (:requestIds)')
            ->setParameter('providerCodes', array_values(array_unique($providerCodes)))
            ->setParameter('requestIds', array_values(array_unique($requestIds)));

        $existing = [];
        foreach ($qb->getQuery()->getResult() as $event) {
            $existing[$event->providerCode()."\0".$event->providerRequestId()] = true;
        }

        return $existing;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function nonEmptyString(array $data, string $field, string $context): string
    {
        $value = $data[$field] ?? null;
        if (!\is_string($value) || trim($value) === '') {
            throw new BillingException(sprintf('%s: поле "%s" должно быть непустой строкой', $context, $field));
        }

        return $value;
    }
}
