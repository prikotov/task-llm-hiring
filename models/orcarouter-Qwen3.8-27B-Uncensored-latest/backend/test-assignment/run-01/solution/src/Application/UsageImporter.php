<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Parsers\UsageEventLine;
use App\Domain\Exception\ImportException;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём событий использования (JSONL) с идемпотентностью по
 * (provider, provider_request_id): повторная доставка игнорируется
 * целиком (без обновлений и доначислений).
 *
 * Сначала разбираются и валидируются ВСЕ строки, затем — запись в БД.
 */
final class UsageImporter
{
    use UsesTransactions;
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * @return array{inserted: int, duplicates: int}
     */
    public function import(string $jsonl): array
    {
        $events = [];
        $duplicates = 0;
        /** @var array<string, true> дубли внутри одной партии */
        $seen = [];

        foreach (self::lines($jsonl) as $lineNumber => $line) {
            if (trim($line) === '') {
                continue; // пустые строки (в т.ч. завершающий перевод строки) игнорируем
            }

            $data = UsageEventLine::fromJsonLine($line, $lineNumber);

            $key = $data['provider'].'\0'.$data['provider_request_id'];
            if (isset($seen[$key])) {
                ++$duplicates;
                continue;
            }

            $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
                'provider' => $data['provider'],
                'providerRequestId' => $data['provider_request_id'],
            ]);
            if ($existing !== null) {
                ++$duplicates;
                continue;
            }

            $seen[$key] = true;
            $events[] = $data;
        }

        if ($events !== []) {
            $this->inTransaction(function () use ($events): void {
                foreach ($events as $data) {
                    $event = new UsageEvent();
                    $event->provider = $data['provider'];
                    $event->providerRequestId = $data['provider_request_id'];
                    $event->modelCode = $data['model'];
                    $event->user = $data['user'];
                    $event->ts = $data['ts'];
                    $event->tokensInput = $data['tokens']['input'];
                    $event->tokensOutput = $data['tokens']['output'];
                    $event->tokensCacheRead = $data['tokens']['cache_read'];
                    $event->tokensCacheWrite = $data['tokens']['cache_write'];
                    $this->em->persist($event);
                }
            });
        }

        return ['inserted' => \count($events), 'duplicates' => $duplicates];
    }

    /**
     * Строки файла, пронумерованные со 2 (первая строка — 1).
     *
     * @return \Generator<int, string>
     */
    private static function lines(string $content): \Generator
    {
        $number = 1;
        foreach (preg_split('/\r\n|\r|\n/', $content) ?: [] as $line) {
            yield $number++ => $line;
        }
    }
}
