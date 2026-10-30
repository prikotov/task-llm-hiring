<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\ImportInputException;
use App\Domain\Billing\UsageEventDraft;
use App\Domain\Time\UtcInstant;
use App\Entity\Billing\UsageEvent;
use App\Repository\Billing\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт журнала использования (JSONL, одно событие на строку).
 *
 * Идемпотентность: события дедуплицируются по (provider, provider_request_id),
 * повторная доставка уже зарегистрированного события целиком игнорируется
 * (без обновлений и доначислений).
 */
final class UsageImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {}

    /**
     * @return array{inserted: int, duplicates: int}
     *
     * @throws ImportInputException неверная строка JSONL или значения
     */
    public function import(string $filePath): array
    {
        $file = new \SplFileObject($filePath, 'r');
        $inserted = 0;
        $duplicates = 0;

        try {
            $this->em->beginTransaction();

            try {
                foreach ($this->parseLines((string) $filePath, $file) as $draft) {
                    if ($this->events->countByNaturalKey(
                        $draft->provider,
                        $draft->providerRequestId,
                    ) > 0) {
                        ++$duplicates; // дубль — игнорируем целиком
                        continue;
                    }

                    $event = UsageEvent::fromDraft($draft);
                    $this->em->persist($event);
                    ++$inserted;
                }

                $this->em->flush();
            } catch (\Throwable $e) {
                $this->em->rollBack();

                throw $e;
            }

            $this->em->commit();
        } catch (ImportInputException $e) {
            throw $e;
        } catch (\RuntimeException $e) {
            throw ImportInputException::field($filePath, $e->getMessage());
        } finally {
            unset($file);
        }

        return ['inserted' => $inserted, 'duplicates' => $duplicates];
    }

    /**
     * @return \Generator<UsageEventDraft>
     *
     * @throws ImportInputException
     */
    private function parseLines(string $filePath, \SplFileObject $handle): \Generator
    {
        $handle->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $lineNo = 0;

        while (!$handle->eof()) {
            $lineNo = $handle->key() + 1;
            $raw = $handle->fgets();
            $where = sprintf('%s: line %d', $filePath, $lineNo);

            if (trim($raw) === '') {
                continue; // пустая строка (например, хвост файла)
            }

            try {
                $decoded = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw ImportInputException::field($where, 'invalid JSON: '.$e->getMessage());
            }

            if (!is_array($decoded)) {
                throw ImportInputException::field($where, 'event must be a JSON object');
            }

            yield self::draftFromObject($decoded, $where);
        }
    }

    private static function draftFromObject(array $item, string $where): UsageEventDraft
    {
        $requireString = function (mixed $value, string $field) use ($where): string {
            if (!is_string($value) || $value === '') {
                throw ImportInputException::field(sprintf('%s.%s', $where, $field), 'must be a non-empty string');
            }

            return $value;
        };

        $requireNonNegativeInt = function (mixed $value, string $field) use ($where): int {
            if (!is_int($value) || $value < 0) {
                throw ImportInputException::field(sprintf('%s.%s', $where, $field), 'must be an integer >= 0');
            }

            return $value;
        };

        try {
            $ts = UtcInstant::parse((string) ($item['ts'] ?? ''));
        } catch (\InvalidArgumentException $e) {
            throw ImportInputException::field($where.'.ts', $e->getMessage());
        }

        return new UsageEventDraft(
            $requireString($item['provider'] ?? null, 'provider'),
            $requireString($item['provider_request_id'] ?? null, 'provider_request_id'),
            $requireString($item['model'] ?? null, 'model'),
            $requireString($item['user'] ?? null, 'user'),
            $ts,
            $requireNonNegativeInt($item['tokens_input'] ?? 0, 'tokens_input'),
            $requireNonNegativeInt($item['tokens_output'] ?? 0, 'tokens_output'),
            $requireNonNegativeInt($item['tokens_cache_read'] ?? 0, 'tokens_cache_read'),
            $requireNonNegativeInt($item['tokens_cache_write'] ?? 0, 'tokens_cache_write'),
        );
    }
}
