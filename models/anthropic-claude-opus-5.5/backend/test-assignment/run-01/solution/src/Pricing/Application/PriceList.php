<?php

declare(strict_types=1);

namespace App\Pricing\Application;

use App\Pricing\Domain\OffPeakWindow;
use App\Pricing\Domain\PricingUnit;
use App\Pricing\Domain\Rates;
use App\Shared\InvalidInput;
use App\Shared\UtcTime;
use DateTimeImmutable;
use JsonException;
use Throwable;

/**
 * Разобранный и провалидированный файл истории цен.
 */
final readonly class PriceList
{
    /**
     * @param list<array{code: string, name: string, offPeak: ?OffPeakWindow}> $providers
     * @param list<array{provider: string, code: string}> $models
     * @param list<array{provider: string, model: string, validFrom: DateTimeImmutable, currency: string, rates: Rates}> $prices
     */
    public function __construct(public array $providers, public array $models, public array $prices)
    {
    }

    public static function fromJson(string $json): self
    {
        try {
            $data = json_decode($json, true, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidInput('Некорректный JSON: '.$e->getMessage(), 0, $e);
        }
        if (!is_array($data)) {
            throw new InvalidInput('Ожидается JSON-объект');
        }

        $providers = [];
        foreach (self::list($data, 'providers') as $i => $p) {
            $ctx = "providers[$i]";
            $offPeak = null;
            $raw = $p['off_peak'] ?? null;
            if ($raw !== null) {
                if (!is_array($raw)) {
                    throw new InvalidInput("$ctx.off_peak: ожидается объект или null");
                }
                $offPeak = self::wrap($ctx, static fn () => OffPeakWindow::create(
                    self::str($raw, 'window_start', $ctx),
                    self::str($raw, 'window_end', $ctx),
                    self::str($raw, 'multiplier', $ctx),
                ));
            }
            $providers[] = ['code' => self::str($p, 'code', $ctx), 'name' => self::str($p, 'name', $ctx), 'offPeak' => $offPeak];
        }

        $models = [];
        foreach (self::list($data, 'models') as $i => $m) {
            $models[] = ['provider' => self::str($m, 'provider', "models[$i]"), 'code' => self::str($m, 'code', "models[$i]")];
        }

        $prices = [];
        foreach (self::list($data, 'prices') as $i => $r) {
            $ctx = "prices[$i]";
            $unit = PricingUnit::tryFrom(self::str($r, 'unit', $ctx))
                ?? throw new InvalidInput("$ctx.unit: неизвестная схема тарификации");
            $currency = self::str($r, 'currency', $ctx);
            if ($currency !== 'USD') {
                throw new InvalidInput("$ctx.currency: поддерживается только USD");
            }
            $rates = $r['rates'] ?? null;
            if (!is_array($rates)) {
                throw new InvalidInput("$ctx.rates: ожидается объект");
            }
            $prices[] = [
                'provider' => self::str($r, 'provider', $ctx),
                'model' => self::str($r, 'model', $ctx),
                'validFrom' => self::wrap($ctx, static fn () => UtcTime::parse(self::str($r, 'valid_from', $ctx))),
                'currency' => $currency,
                'rates' => self::wrap($ctx, static fn () => Rates::fromArray($unit, $rates)),
            ];
        }

        return new self($providers, $models, $prices);
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<int, array<array-key, mixed>>
     */
    private static function list(array $data, string $key): array
    {
        $v = $data[$key] ?? [];
        if (!is_array($v) || !array_is_list($v)) {
            throw new InvalidInput("$key: ожидается массив");
        }
        foreach ($v as $i => $item) {
            if (!is_array($item)) {
                throw new InvalidInput("{$key}[$i]: ожидается объект");
            }
        }

        /** @var array<int, array<array-key, mixed>> $v */
        return $v;
    }

    /** @param array<array-key, mixed> $a */
    private static function str(array $a, string $key, string $ctx): string
    {
        $v = $a[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new InvalidInput("$ctx.$key: ожидается непустая строка");
        }

        return $v;
    }

    /**
     * @template T
     * @param callable(): T $fn
     * @return T
     */
    private static function wrap(string $ctx, callable $fn): mixed
    {
        try {
            return $fn();
        } catch (InvalidInput $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new InvalidInput("$ctx: ".$e->getMessage(), 0, $e);
        }
    }
}
