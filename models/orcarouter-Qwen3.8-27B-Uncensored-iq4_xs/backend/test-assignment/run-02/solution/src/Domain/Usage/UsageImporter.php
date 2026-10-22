<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Common\Input;
use App\Domain\Common\ImportFormatException;
use Doctrine\ORM\EntityManagerInterface;

use function count;
use function json_decode;

/**
 * Импорт событий использования (JSONL).
 *
 * Дедупликация по (provider, provider_request_id): повторная доставка
 * уже известного события целиком игнорируется (никаких обновлений).
 */
final class UsageImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array{imported:int, duplicates:int}
     */
    public function importFile(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportFormatException('Не удалось прочитать файл: '.$path);
        }

        $imported = 0;
        $duplicates = 0;
        $repo = $this->em->getRepository(UsageEvent::class);

        $this->em->wrapInTransaction(function () use ($raw, $path, $repo, &$imported, &$duplicates): void {
            $lines = preg_split('/\R/', str_replace("\r\n", "\n", $raw));
            if ($lines === false) {
                $lines = [];
            }

            foreach ($lines as $no => $line) {
                if (trim($line) === '') {
                    continue; // пустые строки (в т.ч. завершающая) не считаются событиями
                }

                $event = $this->parseLine($line, $no, $path);

                $existing = $repo->findByKey($event->provider(), $event->requestId());
                if ($existing !== null) {
                    $duplicates++;
                    continue;
                }

                try {
                    $this->em->persist($event);
                    $this->em->flush();
                } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException) {
                    // Гонка/повтор в пределах одного прогона — дедуплицируем.
                    $this->em->clear($event);
                    $duplicates++;
                    continue;
                }

                $imported++;
            }
        });

        return ['imported' => $imported, 'duplicates' => $duplicates];
    }

    private function parseLine(string $line, int $lineNo, string $path): UsageEvent
    {
        $label = "{$path}: строка ".($lineNo + 1);

        try {
            $json = json_decode($line, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportFormatException("{$label}: некорректный JSON («{$e->getMessage()}»)");
        }

        $row = Input::asRow($json, $label);

        $provider = Input::asNonEmptyString($row['provider'] ?? null, $label.'[provider]');
        $requestId = Input::asNonEmptyString($row['provider_request_id'] ?? null, $label.'[provider_request_id]');
        $model = Input::asNonEmptyString($row['model'] ?? null, $label.'[model]');
        $user = Input::asNonEmptyString($row['user'] ?? null, $label.'[user]');

        $ts = Input::parseIsoUtc((string) ($row['ts'] ?? ''));
        if ($ts === null) {
            throw new ImportFormatException($label.'[ts]: ожидается ISO-8601 (UTC, "Z")');
        }

        $tokens = [
            'tokens_input' => Input::asNonNegativeInt($row['tokens_input'] ?? null, $label.'[tokens_input]'),
            'tokens_output' => Input::asNonNegativeInt($row['tokens_output'] ?? null, $label.'[tokens_output]'),
            'tokens_cache_read' => Input::asNonNegativeInt($row['tokens_cache_read'] ?? null, $label.'[tokens_cache_read]'),
            'tokens_cache_write' => Input::asNonNegativeInt($row['tokens_cache_write'] ?? null, $label.'[tokens_cache_write]'),
        ];

        return new UsageEvent($provider, $requestId, $model, $user, $ts, $tokens);
    }
}
