<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\OffPeakWindow;
use App\Domain\WindowMinutes;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Entity\Price;
use App\Domain\PricingUnit;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка справочника: провайдеры, модели, строки цен.
 *
 * Идемпотентный upsert:
 *   провайдеры — по `code`;
 *   модели     — по `(provider, code)`;
 *   цены       — по `(provider, model, valid_from)`.
 * Повторная загрузка тех же сумм — не изменяет данных.
 *
 * Валидация: строка цен — JSON, обязательные поля (provider, model,
 * valid_from ISO-8601, unit, rates по схеме). Некорректный файл — бросает
 * DomainException/InvalidArgumentException, который превращается в exit(1)
 * на уровне команды.
 */
final class PricesImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
    ) {
    }

    /** @param string $file путь к JSON-файлу */
    public function import(string $file): array
    {
        if (!is_file($file)) {
            throw new \DomainException(sprintf('Файл не найден: %s', $file));
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new \DomainException(sprintf('Не удалось прочитать файл: %s', $file));
        }
        $decoded = json_decode($raw, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \DomainException(sprintf('Некорректый JSON: %s (код %d)', json_last_error_msg(), json_last_error()));
        }
        if (!\is_array($decoded)) {
            throw new \DomainException('JSON-документ должен быть объектом с ключами providers/models/prices.');
        }

        $providers = $decoded['providers'] ?? [];
        $models    = $decoded['models'] ?? [];
        $prices    = $decoded['prices'] ?? [];

        $providerByCode = [];
        foreach ($providers as $p) {
            if (!\is_array($p) || !isset($p['code'], $p['name']) || $p['code'] === '' || $p['name'] === '') {
                throw new \DomainException(sprintf('Секция providers[?]: отсутствуют обязательные поля code/name: %s', json_encode($p)));
            }
            $offPeak = null;
            if (isset($p['off_peak']) && \is_array($p['off_peak'])) {
                $offPeak = $this->parseOffPeak($p['off_peak']);
            }
            $provider = $this->providers->upsert((string) $p['code'], (string) $p['name'], $offPeak);
            $providerByCode[(string) $p['code']] = $provider;
        }

        $modelByPair = [];
        foreach ($models as $m) {
            if (!\is_array($m) || !isset($m['provider'], $m['code'])) {
                throw new \DomainException(sprintf('Секция models[?]: отсутствуют обязательные поля provider/code: %s', json_encode($m)));
            }
            $providerCode = (string) $m['provider'];
            $modelCode = (string) $m['code'];
            if (!isset($providerByCode[$providerCode])) {
                $provider = $this->providers->findByCode($providerCode);
                if ($provider === null) {
                    throw new \DomainException(sprintf('Секция models: провайдер не найден в справочнике: %s', $providerCode));
                }
                $providerByCode[$providerCode] = $provider;
            }
            $model = $this->models->upsertWithProvider($providerCode, $modelCode, $providerByCode[$providerCode]);
            $modelByPair[$providerCode.'|'.$modelCode] = $model;
        }

        $importedPrices = 0;
        foreach ($prices as $p) {
            $this->upsertPriceRow($p, $providerByCode, $modelByPair);
            $importedPrices++;
        }

        $this->em->flush();

        return [
            'providers' => \count($providers),
            'models'    => \count($models),
            'prices'    => $importedPrices,
        ];
    }

    private function parseOffPeak(array $raw): OffPeakWindow
    {
        if (!isset($raw['window_start'], $raw['window_end'], $raw['multiplier'])) {
            throw new \DomainException(sprintf('off_peak: обязательные поля window_start/window_end/multiplier отсутствуют: %s', json_encode($raw)));
        }
        if (!\is_string($raw['window_start']) || !\is_string($raw['window_end']) || !\is_string($raw['multiplier'])) {
            throw new \DomainException(sprintf('off_peak: window_start/window_end/multiplier должны быть строками: %s', json_encode($raw)));
        }
        $multiplier = $raw['multiplier'];
        if (!preg_match('/^\d+(\.\d+)?$/', $multiplier)) {
            throw new \DomainException(sprintf('off_peak: multiplier должен быть неотрицательным десятичным числом: %s', $multiplier));
        }

        return new OffPeakWindow(
            WindowMinutes::from($raw['window_start']),
            WindowMinutes::from($raw['window_end']),
            $multiplier,
        );
    }

    /** @param array<int, mixed> $row */
    private function upsertPriceRow(
        array $row,
        array $providerByCode,
        array $modelByPair,
    ): void {
        foreach (['provider', 'model', 'valid_from', 'unit', 'rates'] as $k) {
            if (!\array_key_exists($k, $row)) {
                throw new \DomainException(sprintf('Секция prices: отсутствует поле %s: %s', $k, json_encode($row)));
            }
        }

        $providerCode = (string) $row['provider'];
        $modelCode = (string) $row['model'];
        $pair = $providerCode.'|'.$modelCode;

        if (!isset($providerByCode[$providerCode])) {
            $provider = $this->providers->findByCode($providerCode);
            if ($provider === null) {
                throw new \DomainException(sprintf('Секция prices: провайдер не найден: %s', $providerCode));
            }
            $providerByCode[$providerCode] = $provider;
        }

        if (!isset($modelByPair[$pair])) {
            $modelByPair[$pair] = $this->models->upsertWithProvider($providerCode, $modelCode, $providerByCode[$providerCode]);
        }

        $model = $modelByPair[$pair];

        $validFrom = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, (string) $row['valid_from']);
        if ($validFrom === false) {
            throw new \DomainException(sprintf('Секция prices: valid_from не в ISO-8601 (ATOM): %s', $row['valid_from'] ?? ''));
        }
        $validFrom = $validFrom->setTimezone(new \DateTimeZone('UTC'));

        $unit = PricingUnit::fromString((string) $row['unit']);
        $rates = $this->parseRates($row['rates'], $unit);

        $existing = $this->prices->byModelAndValidFrom($model, $validFrom);
        if ($existing === null) {
            $existing = new Price($model, $validFrom);
            $this->em->persist($existing);
        }
        $existing->setCurrency((string) ($row['currency'] ?? 'USD'));
        $existing->setRates($unit->value, $rates);
    }

    /**
     * Провайдер присылает rates как десятичные строки (TASK.md): "6 знаков
     * после точки". Допускаем числа (int/float), нормализуем в строки.
     *
     * @return array<string, string> — всегда содержит ключи схемы; лишние — игнорируются
     */
    private function parseRates(mixed $raw, PricingUnit $unit): array
    {
        if (!\is_array($raw)) {
            throw new \DomainException(sprintf('rates: ожидается объект: %s', json_encode($raw)));
        }

        $need = $unit === PricingUnit::PerToken
            ? ['input', 'output', 'cache_read', 'cache_write']
            : ['any'];

        $out = [];
        foreach ($need as $k) {
            if (!\array_key_exists($k, $raw)) {
                throw new \DomainException(sprintf('rates: отсутствует ставка «%s» (схема %s)', $k, $unit->value));
            }
            $v = $raw[$k];
            if (!\is_string($v) && !\is_int($v) && !\is_float($v)) {
                throw new \DomainException(sprintf('rates[%s]: ожидается строка/число', $k));
            }
            $v = (string) $v;
            if (!preg_match('/^\d+(\.\d+)?$/', $v)) {
                throw new \DomainException(sprintf('rates[%s]: неотрицательное десятичное число (строка): %s', $k, $v));
            }
            $out[$k] = $v;
        }

        return $out;
    }
}
