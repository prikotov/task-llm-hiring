<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий использования (JSONL).
 *
 * Идемпотентность (п. 5): дедупликация по (provider, provider_request_id)
 * — уже известные события игнорируются целиком, обновлений нет.
 * Все новые строки добавляются в одной транзакции.
 */
final class UsageImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return Result со счётчиком принятых (новых) событий
     * @throws ImportException при некорректной строке
     */
    public function importFile(string $path): Result
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или нечитаем: %s', $path));
        }

        $events = $this->parseFile($path);

        $result = new Result();

        if ($events === []) {
            return $result;
        }

        $em = $this->em;
        $em->wrapInTransaction(function (EntityManagerInterface $em) use ($events, $result) {
            $known = $this->knownKeys($em);

            foreach ($events as $event) {
                $key = $this->key($event);
                if (\array_key_exists($key, $known)) {
                    continue; // дубликат — игнорируем
                }
                $known[$key] = true;
                $em->persist($event);
                $result->importedEvent();
            }

            $em->flush();
        });

        return $result;
    }

    /** Читаем файл целиком до начала записи: при ошибке в любой строке ничего не сохраняется.
     *
     * @return list<UsageEvent>
     */
    private function parseFile(string $path): array
    {
        $events = [];
        $lineNo = 0;

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new ImportException(sprintf('Не удалось открыть файл: %s', $path));
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $lineNo++;
                if (trim($line) === '') {
                    continue; // пустые строки (в т.ч. завершающий перевод строки) не считаются ошибкой
                }

                try {
                    $decoded = json_decode($line, true, 32, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    throw new ImportException(sprintf('Строка %d: некорректный JSON: %s', $lineNo, $e->getMessage()));
                }

                if (!\is_array($decoded)) {
                    throw new ImportException(sprintf('Строка %d: событие должно быть объектом JSON', $lineNo));
                }

                $events[] = UsageLineParser::parse($decoded, sprintf('строка %d', $lineNo));
            }
        } finally {
            fclose($handle);
        }

        return $events;
    }

    /** @return array<string, true> */
    private function knownKeys(EntityManagerInterface $em): array
    {
        $rows = $em->getConnection()->executeQuery(
            'SELECT provider_code, provider_request_id FROM usage_events'
        )->fetchAllAssociative();

        $known = [];
        foreach ($rows as $row) {
            $key = $row['provider_code']."\x00".$row['provider_request_id'];
            $known[$key] = true;
        }

        return $known;
    }

    private function key(UsageEvent $event): string
    {
        return $event->getProviderCode()."\x00".$event->getProviderRequestId();
    }
}
