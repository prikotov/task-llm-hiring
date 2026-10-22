<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Application\Catalog\CatalogInput;
use App\Domain\Usage\TokenKind;
use App\Domain\Usage\UsageEvent;
use App\Exception\DataImportException;
use App\Infrastructure\Persistence\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования (JSONL) с идемпотентностью по
 * (provider, provider_request_id): повторная доставка игнорируется
 * целиком (без обновлений и доначислений).
 */
final class UsageEventImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function import(string $file): void
    {
        if (is_file($file) === false || is_readable($file) === false) {
            throw new DataImportException(sprintf('File is not readable: %s', $file));
        }

        $this->em->wrapInTransaction(function () use ($file): void {
            $handle = fopen($file, 'rb');
            if ($handle === false) {
                throw new DataImportException(sprintf('Cannot open file: %s', $file));
            }

            $lineNo = 0;
            while (($line = fgets($handle)) !== false) {
                $lineNo++;
                $raw = trim($line);
                if ($raw === '') {
                    continue; // пустые строки игнорируются (например, завершение файла)
                }

                try {
                    $obj = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    throw new DataImportException(sprintf('%s:%d: invalid JSON: %s', $file, $lineNo, $e->getMessage()));
                }

                if (!is_array($obj)) {
                    throw new DataImportException(sprintf('%s:%d: each line must be a JSON object.', $file, $lineNo));
                }

                $event = $this->parseEvent($obj, $file, $lineNo);
                $existing = $this->events->findByNaturalKey($event->provider(), $event->providerRequestId());
                if ($existing !== null) {
                    continue; // дубль: игнорируем целиком
                }

                $this->em->persist($event);
            }

            fclose($handle);
        });
    }

    /**
     * @param array<string, mixed> $obj
     */
    private function parseEvent(array $obj, string $file, int $lineNo): UsageEvent
    {
        $ctx = sprintf('%s:%d', $file, $lineNo);

        $provider = self::nonEmptyString($obj['provider'] ?? null, $ctx.'.provider');
        $requestId = self::nonEmptyString($obj['provider_request_id'] ?? null, $ctx.'.provider_request_id');
        $modelCode = self::nonEmptyString($obj['model'] ?? null, $ctx.'.model');
        $user = self::nonEmptyString($obj['user'] ?? null, $ctx.'.user');
        $ts = CatalogInput::parseUtcTimestamp(self::nonEmptyString($obj['ts'] ?? null, $ctx.'.ts'), $ctx.'.ts');

        // Событие ссылается на модель своего провайдера (справочник должен быть загружен раньше).
        $model = $this->em->getRepository(\App\Domain\Catalog\Model::class)->findOneBy([
            'providerCode' => $provider,
            'code' => $modelCode,
        ]);
        if ($model === null) {
            throw new DataImportException(sprintf(
                '%s: model "%s/%s" is not found in the reference data.',
                $ctx,
                $provider,
                $modelCode,
            ));
        }

        $tokens = [];
        foreach (TokenKind::cases() as $kind) {
            $tokens[$kind->name] = self::nonNegativeInt($obj[$kind->tokenField()] ?? 0, $ctx.'.'.$kind->tokenField());
        }

        return new UsageEvent(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $tokens[TokenKind::Input->name],
            $tokens[TokenKind::Output->name],
            $tokens[TokenKind::CacheRead->name],
            $tokens[TokenKind::CacheWrite->name],
        );
    }

    /**
     * @param mixed $value
     */
    private static function nonEmptyString(mixed $value, string $context): string
    {
        if (!is_string($value) || $value === '') {
            throw new DataImportException(sprintf('%s: expected a non-empty string.', $context));
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    private static function nonNegativeInt(mixed $value, string $context): int
    {
        if (is_string($value)) {
            if (preg_match('/^\d+$/', $value) !== 1) {
                throw new DataImportException(sprintf('%s: expected a non-negative integer, got "%s".', $context, $value));
            }

            return (int) $value;
        }

        if (!is_int($value) || $value < 0) {
            throw new DataImportException(sprintf('%s: expected a non-negative integer.', $context));
        }

        return $value;
    }
}
