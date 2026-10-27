<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\Decimals;
use App\Domain\Billing\ImportInputException;
use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\PriceUnit;
use App\Domain\Time\UtcInstant;
use App\Entity\Billing\Model;
use App\Entity\Billing\PriceRow;
use App\Entity\Billing\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт справочника цен (providers/models/prices) с идемпотентным upsert:
 *  - провайдер — по code;
 *  - модель — по (provider, code);
 *  - строка цен — по (provider, model, valid_from).
 *
 * Повторная загрузка тех же данных не изменяет содержимое БД.
 */
final class PricesImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @throws ImportInputException неверные структура или значения файла
     */
    public function import(string $filePath): void
    {
        $doc = self::parseFile($filePath);
        $providers = self::validateProviders($doc['providers'] ?? []);
        $models = self::validateModels($doc['models'] ?? [], $providers);

        $this->em->wrapInTransaction(function () use ($providers, $models, $doc): void {
            foreach ($providers as $data) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $data['code']]);

                if ($provider === null) {
                    $provider = new Provider($data['code'], $data['name'], null);
                    $this->em->persist($provider);
                    $this->em->flush();
                }

                $provider->setName($data['name']);
                $provider->setOffPeak($data['offPeak']);
            }

            $byCode = $this->em->getRepository(Provider::class)->findBy([], ['code' => 'ASC']);
            $providersByCode = [];
            foreach ($byCode as $provider) {
                $providersByCode[$provider->code()] = $provider;
            }

            foreach ($models as $key => $modelCode) {
                [$providerCode] = explode('|', $key, 2);
                $provider = $providersByCode[$providerCode];
                $model = $this->em->getRepository(Model::class)->findOneBy([
                    'provider' => $provider,
                    'code' => $modelCode,
                ]);

                if ($model === null) {
                    $model = new Model($provider, $modelCode);
                    $this->em->persist($model);
                    $this->em->flush();
                }
            }

            $rows = self::validatePriceRows($doc['prices'] ?? [], $providers, $models);

            foreach ($rows as $row) {
                $this->upsertPriceRow($providersByCode[$row['provider']], $row['validFrom'], $row['unit'], $row['rates'], $row['currency'], $row['code']);
            }
        });
    }

    /**
     * Применение ретро-коррекции цен: upsert строк цен по
     * (provider, model, valid_from) по данным, уже загруженным в базу.
     * Секции providers/models в файле могут отсутствовать.
     *
     * @return int[] идентификаторы строк цен, значения которых были заменены
     *               (новые строки тоже входят — они «заменяют» отсутствовавшую
     *               запись, что корректно для повторной тарификации)
     *
     * @throws ImportInputException
     */
    public function applyCorrections(string $filePath): array
    {
        $doc = self::parseFile($filePath);
        $rows = self::validatePriceRows($doc['prices'] ?? [], null, null);

        $replacedIds = [];
        $this->em->wrapInTransaction(function () use ($rows, &$replacedIds): void {
            foreach ($rows as $row) {
                $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $row['provider']]);
                if ($provider === null) {
                    throw ImportInputException::field(
                        sprintf('prices "%s/%s"', $row['provider'], $row['code']),
                        'provider does not exist in the catalog',
                    );
                }

                $model = $this->em->getRepository(Model::class)->findOneBy([
                    'provider' => $provider,
                    'code' => $row['code'],
                ]);
                if ($model === null) {
                    throw ImportInputException::field(
                        sprintf('prices "%s/%s"', $row['provider'], $row['code']),
                        'model does not exist in the catalog',
                    );
                }

                $existing = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'model' => $model,
                    'validFrom' => $row['validFrom'],
                ]);

                if ($existing === null) {
                    $existing = new PriceRow($model, $row['validFrom']);
                    $this->em->persist($existing);
                    $this->em->flush();
                }

                $existing->setQuote($row['unit'], $row['rates'], $row['currency']);
                $replacedIds[] = $existing->id();
            }
        });

        return $replacedIds;
    }

    private function upsertPriceRow(Provider $provider, \DateTimeImmutable $validFrom, PriceUnit $unit, array $rates, string $currency, string $modelCode): void
    {
        $model = $this->em->getRepository(Model::class)->findOneBy([
            'provider' => $provider,
            'code' => $modelCode,
        ]);

        if ($model === null) {
            $model = new Model($provider, $modelCode);
            $this->em->persist($model);
            $this->em->flush();
        }

        $row = $this->em->getRepository(PriceRow::class)->findOneBy([
            'model' => $model,
            'validFrom' => $validFrom,
        ]);

        if ($row === null) {
            $row = new PriceRow($model, $validFrom);
            $this->em->persist($row);
            $this->em->flush();
        }

        $row->setQuote($unit, $rates, $currency);
    }

    // ------------------------------------------------------------------
    // Парсинг и валидация входных данных
    // ------------------------------------------------------------------

    private static function parseFile(string $filePath): array
    {
        if (!is_file($filePath)) {
            throw ImportInputException::field($filePath, 'file not found');
        }

        $raw = file_get_contents($filePath);
        if ($raw === false || $raw === '') {
            throw ImportInputException::field($filePath, 'empty or unreadable file');
        }

        try {
            $decoded = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ImportInputException::field($filePath, 'invalid JSON: '.$e->getMessage());
        }

        if (!is_array($decoded)) {
            throw ImportInputException::field($filePath, 'top level must be a JSON object');
        }

        return $decoded;
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return array<string, array{code: string, name: string, offPeak: OffPeakWindow|null}>
     */
    private static function validateProviders(array $items): array
    {
        $providers = [];
        foreach ($items as $i => $item) {
            $where = sprintf('providers[%d]', $i);
            self::assertObject($item, $where);

            $code = self::requireNonNullString($item['code'] ?? null, $where.'.code');
            $name = self::requireNonNullString($item['name'] ?? null, $where.'.name');

            $offPeak = null;
            if (array_key_exists('off_peak', $item) && $item['off_peak'] !== null) {
                $ok = $item['off_peak'];
                self::assertObject($ok, $where.'.off_peak');
                $offPeak = new OffPeakWindow(
                    self::requireNonNullString($ok['window_start'] ?? null, $where.'.off_peak.window_start'),
                    self::requireNonNullString($ok['window_end'] ?? null, $where.'.off_peak.window_end'),
                    Decimals::assert((string) ($ok['multiplier'] ?? ''), $where.'.off_peak.multiplier'),
                );
            }

            if (isset($providers[$code])) {
                throw ImportInputException::field($where, sprintf('duplicate provider code "%s"', $code));
            }

            $providers[$code] = ['code' => $code, 'name' => $name, 'offPeak' => $offPeak];
        }

        return $providers;
    }

    /**
     * @param array<int, mixed> $items
     * @param array<string, array{code: string, name: string, offPeak: OffPeakWindow|null}> $providers
     *
     * @return array<string, string> ключ «provider|code» => code модели
     */
    private static function validateModels(array $items, array $providers): array
    {
        $models = [];
        foreach ($items as $i => $item) {
            $where = sprintf('models[%d]', $i);
            self::assertObject($item, $where);

            $provider = self::requireNonNullString($item['provider'] ?? null, $where.'.provider');
            $code = self::requireNonNullString($item['code'] ?? null, $where.'.code');

            if (!isset($providers[$provider])) {
                throw ImportInputException::field($where, sprintf('provider "%s" is not declared in this file', $provider));
            }

            $key = $provider.'|'.$code;
            if (isset($models[$key])) {
                throw ImportInputException::field($where, sprintf('duplicate model "%s"', $key));
            }

            $models[$key] = $code;
        }

        return $models;
    }

    /**
     * @param array<int, mixed> $items
     * @param array<string, array{code: string, name: string, offPeak: OffPeakWindow|null}>|null $providers null — не требовать объявлений (коррекции)
     * @param array<string, string>|null $models
     *
     * @return list<array{provider: string, code: string, validFrom: \DateTimeImmutable, currency: string, unit: PriceUnit, rates: array<string, string>}>
     */
    private static function validatePriceRows(array $items, ?array $providers, ?array $models): array
    {
        $rows = [];
        $seen = [];
        foreach ($items as $i => $item) {
            $where = sprintf('prices[%d]', $i);
            self::assertObject($item, $where);

            $provider = self::requireNonNullString($item['provider'] ?? null, $where.'.provider');
            $code = self::requireNonNullString($item['model'] ?? null, $where.'.model');
            $validFrom = UtcInstant::parse((string) ($item['valid_from'] ?? ''));
            $currency = $item['currency'] ?? 'USD';

            if ($providers !== null) {
                if (!isset($providers[$provider])) {
                    throw ImportInputException::field($where, sprintf('provider "%s" is not declared in this file', $provider));
                }

                if (!isset($models[$provider.'|'.$code])) {
                    throw ImportInputException::field($where, sprintf('model "%s/%s" is not declared in this file', $provider, $code));
                }
            }

            $unit = PriceUnit::fromString((string) ($item['unit'] ?? ''));
            $ratesRaw = $item['rates'] ?? null;
            self::assertObject($ratesRaw, $where.'.rates');

            $rates = match ($unit) {
                PriceUnit::Per1MToken => [
                    'input' => Decimals::assert((string) ($ratesRaw['input'] ?? ''), $where.'.rates.input'),
                    'output' => Decimals::assert((string) ($ratesRaw['output'] ?? ''), $where.'.rates.output'),
                    'cache_read' => Decimals::assert((string) ($ratesRaw['cache_read'] ?? ''), $where.'.rates.cache_read'),
                    'cache_write' => Decimals::assert((string) ($ratesRaw['cache_write'] ?? ''), $where.'.rates.cache_write'),
                ],
                PriceUnit::Per1MTokenUnified => [
                    'any' => Decimals::assert((string) ($ratesRaw['any'] ?? ''), $where.'.rates.any'),
                ],
            };

            $key = $provider.'|'.$code.'|'.self::formatUtc($validFrom);
            if (isset($seen[$key])) {
                throw ImportInputException::field($where, sprintf('duplicate price row for "%s/%s" valid from %s', $provider, $code, self::formatUtc($validFrom)));
            }
            $seen[$key] = true;

            $rows[] = compact('provider', 'code', 'validFrom', 'currency', 'unit', 'rates');
        }

        return $rows;
    }

    private static function assertObject(mixed $value, string $where): void
    {
        if (!is_array($value)) {
            throw ImportInputException::field($where, 'must be a JSON object');
        }
    }

    private static function requireNonNullString(mixed $value, string $where): string
    {
        if (!is_string($value) || $value === '') {
            throw ImportInputException::field($where, 'must be a non-empty string');
        }

        return $value;
    }

    private static function formatUtc(\DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:s\Z');
    }
}
