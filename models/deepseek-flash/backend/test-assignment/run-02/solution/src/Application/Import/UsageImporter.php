<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Usage\Entity\UsageEvent;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Throwable;

final readonly class UsageImporter
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @return array{imported: int, duplicates: int}
     */
    public function import(string $path): array
    {
        $events = $this->parseFile($path);

        return $this->em->wrapInTransaction(function () use ($events): array {
            $repository = $this->em->getRepository(UsageEvent::class);
            $imported = 0;
            $duplicates = 0;
            $seenInFile = [];

            foreach ($events as $event) {
                $key = $event->providerCode."\0".$event->providerRequestId;
                if (isset($seenInFile[$key])) {
                    ++$duplicates;

                    continue;
                }
                $seenInFile[$key] = true;

                $existing = $repository->findOneBy([
                    'providerCode' => $event->providerCode,
                    'providerRequestId' => $event->providerRequestId,
                ]);
                if ($existing !== null) {
                    ++$duplicates;

                    continue;
                }

                $this->em->persist(new UsageEvent(
                    $event->providerCode,
                    $event->providerRequestId,
                    $event->modelCode,
                    $event->userId,
                    $event->occurredAt,
                    $event->tokensInput,
                    $event->tokensOutput,
                    $event->tokensCacheRead,
                    $event->tokensCacheWrite,
                ));
                ++$imported;
            }

            return ['imported' => $imported, 'duplicates' => $duplicates];
        });
    }

    /** @return list<UsageEventData> */
    private function parseFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('File "%s" does not exist or is not readable.', $path));
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new ImportException(sprintf('Unable to read file "%s".', $path));
        }

        $events = [];
        $lineNumber = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                ++$lineNumber;
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                try {
                    $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $exception) {
                    throw new ImportException(sprintf('Line %d contains invalid JSON: %s', $lineNumber, $exception->getMessage()), 0, $exception);
                }

                if (!is_array($row)) {
                    throw new ImportException(sprintf('Line %d must contain a JSON object.', $lineNumber));
                }

                try {
                    $events[] = $this->parseRow($row);
                } catch (ImportException $exception) {
                    throw new ImportException(sprintf('Line %d: %s', $lineNumber, $exception->getMessage()), 0, $exception);
                }
            }
        } finally {
            fclose($handle);
        }

        return $events;
    }

    /** @param array<string, mixed> $row */
    private function parseRow(array $row): UsageEventData
    {
        return new UsageEventData(
            providerCode: $this->requiredString($row, 'provider'),
            providerRequestId: $this->requiredString($row, 'provider_request_id'),
            modelCode: $this->requiredString($row, 'model'),
            userId: $this->requiredString($row, 'user'),
            occurredAt: $this->parseDate($row['ts'] ?? null),
            tokensInput: $this->tokenCount($row, 'tokens_input'),
            tokensOutput: $this->tokenCount($row, 'tokens_output'),
            tokensCacheRead: $this->tokenCount($row, 'tokens_cache_read'),
            tokensCacheWrite: $this->tokenCount($row, 'tokens_cache_write'),
        );
    }

    private function parseDate(mixed $value): DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException('"ts" must be an ISO-8601 string.');
        }

        try {
            return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'));
        } catch (Throwable $exception) {
            throw new ImportException(sprintf('"ts" has invalid date "%s".', $value), 0, $exception);
        }
    }

    /** @param array<string, mixed> $row */
    private function tokenCount(array $row, string $field): int
    {
        $value = $row[$field] ?? null;
        if (!is_int($value) || $value < 0) {
            throw new ImportException(sprintf('"%s" must be a non-negative integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $row */
    private function requiredString(array $row, string $field): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('"%s" must be a non-empty string.', $field));
        }

        return $value;
    }
}
