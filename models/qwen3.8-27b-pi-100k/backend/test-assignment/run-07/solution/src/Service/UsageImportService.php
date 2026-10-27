<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\UsageEventRow;
use App\Domain\UsageParser;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования (JSONL) с идемпотентностью:
 *
 * событие дедуплицируется по (provider, provider_request_id) —
 * повторная доставка уже известного события целиком игнорируется
 * (никаких обновлений и доначислений). Новые события сохраняются
 * со статусом pending — тарификация отдельной командой.
 */
final class UsageImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageParser $parser,
    ) {
    }

    /**
     * @return array{inserted: int, skipped: int}
     */
    public function import(string $filePath): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new ImportException("Файл не найден или недоступен: {$filePath}");
        }

        $raw = file_get_contents($filePath);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: {$filePath}");
        }

        // Сначала проверяем весь файл, затем пишем: битая строка => код 1
        // и ни одна строка не попадает в базу.
        $rows = $this->parser->parseFile($raw);
        $existing = self::existingKeys($this->em, $rows);

        $inserted = 0;
        $skipped = 0;
        $this->em->wrapInTransaction(function () use ($rows, $existing, &$inserted, &$skipped): void {
            foreach ($rows as $row) {
                if (isset($existing[$row->provider . "\0" . $row->providerRequestId])) {
                    $skipped++;
                    continue;
                }

                $this->em->persist(self::toEntity($row));
                $inserted++;
            }

            $this->em->flush();
        });

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    /**
     * Множество ключей (provider, provider_request_id) строк файла,
     * которые уже есть в базе.
     *
     * @param list<UsageEventRow> $rows
     *
     * @return array<string, true>
     */
    private static function existingKeys(EntityManagerInterface $em, array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $byProvider = [];
        foreach ($rows as $row) {
            $byProvider[$row->provider][] = $row->providerRequestId;
        }

        $existing = [];
        foreach ($byProvider as $provider => $requestIds) {
            $requestIds = array_values(array_unique($requestIds));
            $found = $em->createQuery(
                'SELECT e.providerRequestId FROM ' . UsageEvent::class . ' e '
                . 'WHERE e.provider = :provider AND e.providerRequestId IN (:requestIds)'
            )
                ->setParameter('provider', $provider)
                ->setParameter('requestIds', $requestIds)
                ->getSingleColumnResult();

            foreach ($found as $requestId) {
                $existing[$provider . "\0" . $requestId] = true;
            }
        }

        return $existing;
    }

    private static function toEntity(UsageEventRow $row): UsageEvent
    {
        return new UsageEvent(
            $row->provider,
            $row->providerRequestId,
            $row->model,
            $row->user,
            $row->ts,
            $row->tokensInput,
            $row->tokensOutput,
            $row->tokensCacheRead,
            $row->tokensCacheWrite,
        );
    }
}
