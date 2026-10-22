<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Domain\Shared\Time;
use App\Billing\Domain\Usage\TokenUsage;
use App\Billing\Domain\Usage\UsageEvent;
use App\Billing\Infrastructure\Persistence\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка событий JSONL. Дедупликация по (provider, provider_request_id):
 * известное событие игнорируется целиком. Файл валидируется полностью до записи,
 * поэтому при ошибке в любой строке ничего не сохраняется.
 */
final class UsageEventImporter
{
    private const REQUIRED_STRING = ['provider', 'provider_request_id', 'model', 'user', 'ts'];
    private const REQUIRED_INT = ['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
    ) {
    }

    /** @return array{new: int, duplicates: int} */
    public function importFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidInputException(sprintf('Файл не найден или не читается: %s', $path));
        }
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidInputException(sprintf('Не удалось открыть файл: %s', $path));
        }

        $parsed = [];
        $lineNo = 0;
        try {
            while (($line = fgets($handle)) !== false) {
                ++$lineNo;
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parsed[] = $this->parseLine($line, $lineNo);
            }
        } finally {
            fclose($handle);
        }

        return $this->store($parsed);
    }

    /**
     * @param list<UsageEvent> $parsed
     *
     * @return array{new: int, duplicates: int}
     */
    private function store(array $parsed): array
    {
        $new = 0;
        $duplicates = 0;
        $seenInFile = [];

        $this->em->wrapInTransaction(function () use ($parsed, &$new, &$duplicates, &$seenInFile): void {
            foreach ($parsed as $event) {
                $key = $event->provider().'/'.$event->providerRequestId();
                if (isset($seenInFile[$key]) || $this->events->existsByKey($event->provider(), $event->providerRequestId())) {
                    ++$duplicates;
                    continue;
                }
                $seenInFile[$key] = true;
                $this->em->persist($event);
                ++$new;
            }
            $this->em->flush();
        });

        return ['new' => $new, 'duplicates' => $duplicates];
    }

    private function parseLine(string $line, int $lineNo): UsageEvent
    {
        try {
            $data = json_decode($line, true, 16, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidInputException(sprintf('Строка %d: некорректный JSON: %s', $lineNo, $e->getMessage()), 0, $e);
        }
        if (!is_array($data)) {
            throw new InvalidInputException(sprintf('Строка %d: ожидается JSON-объект', $lineNo));
        }

        foreach (self::REQUIRED_STRING as $key) {
            if (!isset($data[$key]) || !is_string($data[$key]) || trim($data[$key]) === '') {
                throw new InvalidInputException(sprintf('Строка %d: поле "%s" должно быть непустой строкой', $lineNo, $key));
            }
        }
        $ints = [];
        foreach (self::REQUIRED_INT as $key) {
            if (!isset($data[$key]) || !is_int($data[$key]) || $data[$key] < 0) {
                throw new InvalidInputException(sprintf('Строка %d: поле "%s" должно быть целым неотрицательным числом', $lineNo, $key));
            }
            $ints[$key] = $data[$key];
        }

        try {
            $ts = Time::parseIso($data['ts']);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidInputException(sprintf('Строка %d: %s', $lineNo, $e->getMessage()), 0, $e);
        }

        return new UsageEvent(
            $data['provider'],
            $data['provider_request_id'],
            $data['model'],
            $data['user'],
            $ts,
            new TokenUsage(
                $ints['tokens_input'],
                $ints['tokens_output'],
                $ints['tokens_cache_read'],
                $ints['tokens_cache_write'],
            ),
        );
    }
}
