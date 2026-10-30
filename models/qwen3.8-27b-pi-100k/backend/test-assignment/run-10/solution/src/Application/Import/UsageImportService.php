<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Entity\Model;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт событий (JSONL) с дедупликацией по (provider, provider_request_id).
 *
 * Повторная доставка уже известного события — игнорируется целиком
 * (без обновлений, без доначислений).
 *
 * @see TASK.md → `app:usage:import`
 */
final class UsageImportService
{
    public function __construct(
        private readonly UsageParser $parser,
        private readonly CatalogRepository $catalog,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{total: int, new: int, dup: int, bad: int}
     *
     * @throws ImportException
     */
    public function importFile(string $file): array
    {
        if (!is_file($file)) {
            throw ImportException::json(sprintf('файл не найден: %s', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new ImportException(sprintf('не удалось прочитать: %s', $file));
        }

        $events = $this->parser->parse($raw);
        $stats = [0, 0, 0];
        $this->apply($events, $stats);

        return [
            'total' => \count($events),
            'new' => $stats[0],
            'dup' => $stats[1],
            'bad' => $stats[2],
        ];
    }

    /**
     * @param list<array<string, mixed>> $events
     * @param list<int> $stats [new, dup, bad]
     *
     * @throws ImportException
     */
    public function apply(array $events, array &$stats = null): void
    {
        $this->em->getConnection()->transactional(function () use ($events, &$stats) {
            $providers = [];
            /** @var array<int, Model> $models */
            $models = [];

            foreach ($events as $row) {
                $providerCode = $row['provider'];
                if (!isset($providers[$providerCode])) {
                    $provider = $this->catalog->providerByCode($providerCode);
                    if ($provider === null) {
                        throw ImportException::missingProvider($providerCode);
                    }
                    $providers[$providerCode] = $provider;
                }
                $provider = $providers[$providerCode];

                $modelKey = $provider->getId() . '/' . $row['model'];
                if (!isset($models[$modelKey])) {
                    $models[$modelKey] = $this->modelByCodeInCollection($provider, $row['model'])
                        ?? $this->catalog->modelByProviderAndCode($providerCode, $row['model']);
                }
                $model = $models[$modelKey];

                $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
                    'provider' => $provider,
                    'providerRequestId' => $row['provider_request_id'],
                ]);
                if ($existing !== null) {
                    // Дубль — игнорируется по правилу п. 5.
                    if (\is_array($stats)) {
                        $stats[1]++;
                    }
                    continue;
                }

                $this->em->persist(new UsageEvent(
                    $provider,
                    $row['provider_request_id'],
                    $model,
                    $row['user'],
                    new \DateTimeImmutable($row['ts']),
                    (int) $row['tokens_input'],
                    (int) $row['tokens_output'],
                    (int) $row['tokens_cache_read'],
                    (int) $row['tokens_cache_write'],
                    UsageEvent::STATUS_NEW,
                ));
                if (\is_array($stats)) {
                    $stats[0]++;
                }
            }

            $this->em->flush();
        });
    }

    private function modelByCodeInCollection(Provider $provider, string $code): ?Model
    {
        foreach ($provider->getModels() as $m) {
            if ($m->getCode() === $code) {
                return $m;
            }
        }

        return null;
    }
}
