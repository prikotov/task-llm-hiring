<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Entity\Model;
use App\Entity\Provider;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт истории цен (upsert): провайдеры по `code`, модели по
 * `(provider, code)`, строки цен по `(provider, model, valid_from)`.
 *
 * Повторная загрузка с теми же суммами — не изменяет данных (idempotent).
 *
 * @see TASK.md → `app:prices:import`
 */
final class PricesImportService
{
    public function __construct(
        private readonly PricesParser $parser,
        private readonly CatalogRepository $catalog,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return array{providers: int, models: int, prices: int}
     *
     * @throws ImportException при невалидном файле
     */
    public function importFile(string $file): array
    {
        if (!is_file($file)) {
            throw ImportException::json(sprintf('файл не найден: %s', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw ImportException::json(sprintf('не удалось прочитать: %s', $file));
        }

        $data = $this->parser->parse($raw);
        $this->apply($data);

        return [
            'providers' => \count($data['providers']),
            'models' => \count($data['models']),
            'prices' => \count($data['prices']),
        ];
    }

    /**
     * @param array{
     *   providers: array<int, array{code: string, name: string, offPeak: null|array{window_start: string, window_end: string, multiplier: string}}>,
     *   models: array<int, array{provider: string, code: string}>,
     *   prices: array<int, array{provider: string, model: string, valid_from: string, currency: string, unit: string, rates: array<string,string>}>
     * } $data
     *
     * @throws ImportException если провайдер/модель не найдены в базе
     */
    public function apply(array $data): void
    {
        $this->em->getConnection()->transactional(function () use ($data) {
            // 1. Провайдеры.
            $providers = [];
            foreach ($data['providers'] as $p) {
                $providers = $this->upsertProvider($p, $providers);
            }

            // 2. Модели.
            foreach ($data['models'] as $m) {
                $this->upsertModel($m, $providers);
            }

            // 3. Строки цен.
            foreach ($data['prices'] as $pr) {
                $this->upsertPrice($pr, $providers);
            }

            $this->em->flush();
        });
    }

    /**
     * @param array<int, Provider> $cache
     *
     * @return array<int, Provider> обновлённый кэш кода → провайдер
     * @throws ImportException
     */
    private function upsertProvider(
        array $p,
        array $cache,
    ): array {
        $code = $p['code'];
        if (isset($cache[$code])) {
            $this->syncProvider($cache[$code], $p);

            return $cache;
        }

        $provider = $this->catalog->providerByCode($code) ?? new Provider($code, $p['name']);
        $this->syncProvider($provider, $p);
        $this->em->persist($provider);
        $this->em->flush();

        $cache[$code] = $provider;

        return $cache;
    }

    /**
     * @throws ImportException
     */
    private function syncProvider(Provider $provider, array $p): void
    {
        $provider->setName($p['name']);

        if ($p['offPeak'] === null) {
            $provider->setOffPeak(null, null, null);

            return;
        }

        $start = self::hhmmToMinutes($p['offPeak']['window_start']);
        $end = self::hhmmToMinutes($p['offPeak']['window_end']);
        $provider->setOffPeak($start, $end, $p['offPeak']['multiplier']);
    }

    /**
     * @throws ImportException
     */
    private function upsertModel(
        array $m,
        array $providerCache,
    ): void {
        $providerCode = $m['provider'];
        if (!isset($providerCache[$providerCode])) {
            $provider = $this->catalog->providerByCode($providerCode);
            if ($provider === null) {
                throw ImportException::missingProvider($providerCode);
            }
            $providerCache[$providerCode] = $provider;
        }

        $provider = $providerCache[$providerCode];
        $model = $this->findModel($provider, $m['code']);
        if ($model === null) {
            $this->em->persist($model = new Model($provider, $m['code']));
            $this->em->flush();
        }
    }

    private function findModel(Provider $provider, string $code): ?Model
    {
        foreach ($provider->getModels() as $existing) {
            if ($existing->getCode() === $code) {
                return $existing;
            }
        }

        return null;
    }

    /**
     * @throws ImportException
     */
    private function upsertPrice(
        array $pr,
        array $providerCache,
    ): void {
        $providerCode = $pr['provider'];
        if (!isset($providerCache[$providerCode])) {
            $provider = $this->catalog->providerByCode($providerCode);
            if ($provider === null) {
                throw ImportException::missingProvider($providerCode);
            }
            $providerCache[$providerCode] = $provider;
        }

        $provider = $providerCache[$providerCode];
        $model = $this->modelByCodeInCollection($provider, $pr['model'])
            ?? $this->catalog->modelByProviderAndCode($providerCode, $pr['model']);
        $validFrom = new \DateTimeImmutable($pr['valid_from']);

        $existing = $this->catalog->priceByProviderModelValidFrom($provider, $model, $validFrom);
        if ($existing !== null) {
            $existing->setUnitAndRates($pr['unit'], $pr['rates']);

            return;
        }

        $this->em->persist(new Price(
            $provider,
            $model,
            $validFrom,
            $pr['currency'],
            $pr['unit'],
            $pr['rates'],
        ));
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

    private static function hhmmToMinutes(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm, 2));

        return $h * 60 + $m;
    }
}
