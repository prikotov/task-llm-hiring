<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Money;
use App\Domain\PriceUnit;
use App\Infrastructure\Model;
use App\Infrastructure\Provider;
use App\Infrastructure\PriceRow;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;

/**
 * Импорт провайдеров, моделей и строк цен (JSON-файл).
 *
 * Идемпотентен: повторная загрузка тех же сумм — данные не изменяются.
 */
final readonly class PriceImportService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws JsonException
     * @throws \InvalidArgumentException
     */
    public function importFile(string $file): PricesDocument
    {
        if (! is_file($file) || ! is_readable($file)) {
            throw new \InvalidArgumentException(sprintf('Файл не найден: "%s"', $file));
        }
        $raw = file_get_contents($file, false, null);
        if ($raw === false) {
            throw new \InvalidArgumentException(sprintf('Не удалось прочитать файл: "%s"', $file));
        }

        $doc = self::parseString($raw);

        $providerRepo = $this->em->getRepository(Provider::class);
        $modelRepo = $this->em->getRepository(Model::class);
        $priceRepo = $this->em->getRepository(PriceRow::class);

        // Провайдеры
        foreach ($doc->providers as $p) {
            $existing = $providerRepo->findByCode($p->code);
            if ($existing === null) {
                $this->em->persist(new Provider(
                    code: $p->code,
                    name: $p->name,
                    offPeakWindowStart: $p->offPeak?->windowStart,
                    offPeakWindowEnd: $p->offPeak?->windowEnd,
                    offPeakMultiplier: $p->offPeak?->multiplier,
                ));
            } else {
                $existing->update(
                    name: $p->name,
                    offPeakWindowStart: $p->offPeak?->windowStart,
                    offPeakWindowEnd: $p->offPeak?->windowEnd,
                    offPeakMultiplier: $p->offPeak?->multiplier,
                );
            }
        }

        // Модели
        foreach ($doc->models as $m) {
            if ($modelRepo->findByKeys($m->provider, $m->code) === null) {
                $this->em->persist(new Model(providerCode: $m->provider, code: $m->code));
            }
        }

        // Строки цен
        foreach ($doc->prices as $p) {
            $validFrom = self::parseUtc($p->validFrom);
            $validFromEpoch = (clone $validFrom)->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
            $rates = PriceUnit::normalizeRates($p->unit, $p->rates);

            $existing = $this->findPriceByNaturalKey($priceRepo, $p->provider, $p->model, $validFromEpoch);
            if ($existing !== null) {
                $existing->update(currency: $p->currency, unit: $p->unit, rates: $rates);
            } else {
                $this->em->persist(new PriceRow(
                    provider: $p->provider,
                    model: $p->model,
                    validFrom: $validFrom,
                    currency: $p->currency,
                    unit: $p->unit,
                    rates: $rates,
                ));
            }
        }

        $this->em->flush();

        return $doc;
    }

    /**
     * Парсит строку цен, валидирует и возвращает PricesDocument.
     *
     * @throws JsonException
     * @throws \InvalidArgumentException
     */
    public static function parseString(string $raw): PricesDocument
    {
        $dec = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($dec)) {
            throw new \InvalidArgumentException('Ожидался JSON-объект');
        }

        $providers = [];
        foreach ($dec['providers'] ?? [] as $p) {
            if (! is_array($p)) {
                throw new \InvalidArgumentException('providers[]: требуется объект');
            }
            if (! isset($p['code']) || ! is_string($p['code']) || $p['code'] === '') {
                throw new \InvalidArgumentException('providers[]: требуется "code" (не пустая строка)');
            }
            if (! isset($p['name']) || ! is_string($p['name'])) {
                throw new \InvalidArgumentException('providers[]: требуется "name" (строка)');
            }

            $offPeak = null;
            if (array_key_exists('off_peak', $p) && is_array($p['off_peak'])) {
                if (! isset($p['off_peak']['window_start'], $p['off_peak']['window_end'], $p['off_peak']['multiplier'])) {
                    throw new \InvalidArgumentException('off_peak: требуется window_start/window_end/multiplier');
                }
                $offPeak = new PricesOffPeak(
                    windowStart: (string) $p['off_peak']['window_start'],
                    windowEnd: (string) $p['off_peak']['window_end'],
                    multiplier: Money::format((string) $p['off_peak']['multiplier']),
                );
            } elseif (array_key_exists('off_peak', $p) && $p['off_peak'] !== null) {
                throw new \InvalidArgumentException('off_peak: ожидается объект или null');
            }

            $providers[] = new PricesProvider(
                code: (string) $p['code'],
                name: (string) $p['name'],
                offPeak: $offPeak,
            );
        }

        $models = [];
        foreach ($dec['models'] ?? [] as $m) {
            if (! is_array($m) || ! isset($m['provider'], $m['code'])
                || ! is_string($m['provider']) || ! is_string($m['code'])
                || $m['provider'] === '' || $m['code'] === '') {
                throw new \InvalidArgumentException('models[]: требуется "provider" и "code" (не пустые строки)');
            }
            $models[] = new PricesModel(
                provider: (string) $m['provider'],
                code: (string) $m['code'],
            );
        }

        $prices = [];
        foreach ($dec['prices'] ?? [] as $p) {
            if (! is_array($p)) {
                throw new \InvalidArgumentException('prices[]: требуется объект');
            }
            $missing = array_filter([
                'provider', 'model', 'valid_from', 'currency', 'unit', 'rates',
                static fn ($k) => ! array_key_exists($k, $p),
            ]);
            foreach (['provider', 'model', 'valid_from', 'currency', 'unit', 'rates'] as $k) {
                if (! array_key_exists($k, $p)) {
                    throw new \InvalidArgumentException(sprintf('prices[]: отсутствует "%s"', $k));
                }
            }
            if (! is_array($p['rates'])) {
                throw new \InvalidArgumentException('prices[].rates: требуется объект');
            }

            $prices[] = new PricesRowItem(
                provider: (string) ($p['provider'] ?? ''),
                model: (string) ($p['model'] ?? ''),
                validFrom: (string) ($p['valid_from'] ?? ''),
                currency: (string) ($p['currency'] ?? ''),
                unit: (string) ($p['unit'] ?? ''),
                rates: $p['rates'],
            );
        }

        return new PricesDocument(
            providers: $providers,
            models: $models,
            prices: $prices,
        );
    }

    public static function parseFile(string $file): PricesDocument
    {
        if (! is_file($file) || ! is_readable($file)) {
            throw new \InvalidArgumentException(sprintf('Файл не найден: "%s"', $file));
        }
        $raw = file_get_contents($file, false, null);
        if ($raw === false) {
            throw new \InvalidArgumentException(sprintf('Не удалось прочитать файл: "%s"', $file));
        }

        try {
            return self::parseString($raw);
        } catch (JsonException $e) {
            throw new \InvalidArgumentException(sprintf('Некорректный JSON: %s', $e->getMessage()), 0, $e);
        }
    }

    public static function parseUtc(string $iso): \DateTimeImmutable
    {
        try {
            return (new \DateTimeImmutable($iso))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Некорректное время (ISO-8601): "%s"', $iso));
        }
    }

    private function findPriceByNaturalKey(
        \Doctrine\ORM\EntityRepository $repo,
        string $provider,
        string $model,
        int $validFromEpoch,
    ): ?PriceRow {
        return $repo->findOneBy([
            'provider' => $provider,
            'model' => $model,
            'validFromEpoch' => $validFromEpoch,
        ]);
    }
}
