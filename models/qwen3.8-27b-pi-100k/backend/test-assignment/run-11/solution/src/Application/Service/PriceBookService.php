<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Exception\BillingException;
use App\Domain\Money;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника цен (ценовые данные провайдеров).
 * Идемпотентный upsert по (provider|model|valid_from).
 *
 * Принятое допущение: если модель/провайдер ещё не создан в базе —
 * создаётся по-новому. Для корректного справочника это не должно
 * случаться на валидных данных, но защищает от расхождений.
 */
final class PriceBookService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param array{providers?: array, models?: array, prices?: array} $data
     *
     * @return array{providers:int,models:int,prices:int} число записанных
     */
    public function import(array $data): array
    {
        $providers = ($data['providers'] ?? []) !== null ? (array)($data['providers'] ?? []) : [];
        $models = ($data['models'] ?? []) !== null ? (array)($data['models'] ?? []) : [];
        $prices = ($data['prices'] ?? []) !== null ? (array)($data['prices'] ?? []) : [];

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $providerMap = [];
        $writtenProviders = 0;
        foreach ($providers as $p) {
            if (!is_array($p) || !isset($p['code'], $p['name'])) {
                throw BillingException::parseError('providers[]: ожидался {code, name, off_peak?}');
            }
            $code = (string) $p['code'];
            $name = (string) $p['name'];
            $off = $p['off_peak'] ?? null;

            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
            if ($provider === null) {
                $provider = new Provider($code, $name);
                if ($off !== null) {
                    $provider->setOffPeak(
                        (int) self::timeToMinutes((string) $off['window_start']),
                        (int) self::timeToMinutes((string) $off['window_end']),
                        (string) $off['multiplier']
                    );
                }
                $this->em->persist($provider);
                $writtenProviders++;
            } else {
                // обновляем только name + off_peak
                $provider->setName($name);
                if ($off !== null) {
                    $provider->setOffPeak(
                        (int) self::timeToMinutes((string) $off['window_start']),
                        (int) self::timeToMinutes((string) $off['window_end']),
                        (string) $off['multiplier']
                    );
                }
            }
            $providerMap[$code] = $provider;
        }

        $writtenModels = 0;
        $modelMap = [];
        foreach ($models as $m) {
            if (!isset($m['provider'], $m['code'])) {
                throw BillingException::parseError('models[]: ожидался {provider, code}');
            }
            $providerCode = (string) $m['provider'];
            $modelCode = (string) $m['code'];
            $provider = $providerMap[$providerCode]
                ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode])
                ?? throw BillingException::notFound('провайдер', $providerCode);
            $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
            if ($model === null) {
                $model = new Model($provider, $modelCode);
                $this->em->persist($model);
                $writtenModels++;
            }
            $modelMap[$providerCode . '/' . $modelCode] = $model;
        }

        $writtenPrices = 0;
        foreach ($prices as $price) {
            foreach (['provider', 'model', 'valid_from', 'unit', 'rates'] as $key) {
                if (!isset($price[$key])) {
                    throw BillingException::parseError(sprintf('prices[]: отсутствует поле %s', $key));
                }
            }
            $providerCode = (string) $price['provider'];
            $modelCode = (string) $price['model'];
            $validFrom = self::parseIsoUtz((string) $price['valid_from']);
            $currency = (string) ($price['currency'] ?? 'USD');
            $unit = (string) $price['unit'];
            $rates = self::parseRates($price['rates'], $unit);

            $modelKey = $providerCode . '/' . $modelCode;
            if (!isset($modelMap[$modelKey])) {
                $provider = $providerMap[$providerCode]
                    ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
                if ($model === null) {
                    throw BillingException::notFound('модель', $modelKey);
                }
                $modelMap[$modelKey] = $model;
            }
            $model = $modelMap[$modelKey];

            $existing = $this->em->getRepository(Price::class)->findOneBy([
                'model' => $model,
                'validFrom' => $validFrom,
            ]);
            if ($existing === null) {
                $priceEntity = new Price($model, $validFrom, $currency, $unit, $rates);
                $this->em->persist($priceEntity);
                $writtenPrices++;
            } else {
                $existing->replace($currency, $unit, $rates);
            }
        }

        $this->em->flush();

        return [
            'providers' => count($providers),
            'models' => count($models),
            'prices' => count($prices),
        ];
    }

    /** "HH:MM" -> минуты от начала суток */
    public static function parseIsoUtzPublic(string $iso): \DateTimeImmutable
    {
        return self::parseIsoUtz($iso);
    }

    /** @return array<string,string> */
    public static function parseRatesPublic(mixed $raw, string $unit): array
    {
        return self::parseRates($raw, $unit);
    }

    private static function timeToMinutes(string $hhmm): int
    {
        if (!preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $hhmm, $m)) {
            throw BillingException::parseError(sprintf('Оф-пик: время "HH:MM" не распознано: %s', $hhmm));
        }
        return (int) $m[1] * 60 + (int) $m[2];
    }

    private static function parseIsoUtz(string $iso): \DateTimeImmutable
    {
        try {
            $dt = new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw BillingException::parseError(sprintf('Некорректная дата/время ISO-8601: %s', $iso));
        }
        // если ISO не содержит явного UTC-сдвига — доверяем, что это UTC
        // (parse по умолчанию использует UTC timezone)
        return $dt;
    }

    /**
     * @param mixed $raw
     * @return array<string,string>
     */
    private static function parseRates(mixed $raw, string $unit): array
    {
        if (!is_array($raw)) {
            throw BillingException::parseError('rates должен быть object');
        }
        $allowed = ($unit === Price::UNIT_PER_TOKEN)
            ? ['input', 'output', 'cache_read', 'cache_write']
            : ['any'];
        foreach ($allowed as $key) {
            if (!array_key_exists($key, $raw)) {
                throw BillingException::parseError(sprintf('rates: отсутствует поле "%s" для unit=%s', $key, $unit));
            }
            $value = $raw[$key];
            if (!is_string($value)) {
                throw BillingException::parseError(sprintf('rates["%s"] должна быть десятичной строкой', $key));
            }
            if (!preg_match('/^\d+(\.\d+)?$/', $value)) {
                throw BillingException::parseError(sprintf('rates["%s"] должно быть неотрицательным десятичным числом, получено: %s', $key, $value));
            }
        }
        // нормализация до 6 знаков (при импорте, не при расчёте)
        foreach ($allowed as $key) {
            $raw[$key] = Money::of($raw[$key])->format(6);
        }
        return array_intersect_key($raw, array_flip($allowed));
    }

    /**
     * Возвращает «набор данных справочника» (в т.ч. off_peak провайдеров) для
     * RateCalculator по event'у: модель + off_peak провайдера.
     */
    public function priceSheetAt(\DateTimeImmutable $ts, string $providerCode, string $modelCode): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
    }

    public function modelFor(string $providerCode, string $modelCode): ?Model
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
        if ($provider === null) {
            return null;
        }
        return $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
    }

    /**
     * Возвращает Price с наибольшим valid_from <= $ts для модели.
     */
    public function effectivePrice(Model $model, \DateTimeImmutable $ts): ?Price
    {
        $rows = $this->em->getRepository(Price::class)->findBy(
            ['model' => $model],
            ['validFrom' => 'DESC']
        );
        // найти максимальный validFrom <= ts
        foreach ($rows as $row) {
            if ($row->validFrom() <= $ts) {
                return $row;
            }
        }
        return null;
    }

    public function offPeakForProvider($provider): ?array
    {
        if ($provider->hasOffPeak()) {
            return [
                'start' => (int) $provider->offPeakStart(),
                'end' => (int) $provider->offPeakEnd(),
                'multiplier' => (string) $provider->offPeakMultiplier(),
            ];
        }
        return null;
    }
}
