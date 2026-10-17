<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Entity\Model;
use App\Domain\Billing\Entity\PriceRow;
use App\Domain\Billing\Entity\Provider;
use App\Domain\Billing\Exception\BillingException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идентичный (idempotent) импорт файла цен: upsert провайдеров, моделей
 * и строк цен по их натуральным ключам.
 *
 * Порядок импорта важен: сначала провайдеры, затем модели и строки цен —
 * чтобы записи могли ссылаться на сущности, объявленные в том же файле.
 */
final class PriceImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @throws BillingException при неверном формате данных */
    public function import(string $path): void
    {
        $data = $this->readJson($path);

        $providers = $this->section($data, 'providers');
        $models = $this->section($data, 'models');
        $prices = $this->section($data, 'prices');

        $this->em->wrapInTransaction( function () use ($providers, $models, $prices): void {
            foreach ($providers as $item) {
                $this->upsertProvider((array) $item);
            }
            $this->em->flush();

            foreach ($models as $item) {
                $this->upsertModel((array) $item);
            }
            $this->em->flush();

            foreach ($prices as $item) {
                $this->upsertPriceRow((array) $item);
            }
        });
    }

    /** @param array<string, mixed> $data */
    private function upsertProvider(array $data): void
    {
        $code = $this->nonEmptyString($data['code'] ?? null, 'providers[].code');
        $name = $this->nonEmptyString($data['name'] ?? null, 'providers[].name');

        if (array_key_exists('off_peak', $data) && $data['off_peak'] !== null) {
            if (!\is_array($data['off_peak'])) {
                throw new BillingException('providers[].off_peak: должен быть объектом или null.');
            }
        }

        $entity = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($entity === null) {
            $entity = new Provider($code, $name);
            $this->em->persist($entity);
        }

        $entity->name = $name;
        $entity->setOffPeak($data['off_peak'] ?? null);
    }

    /** @param array<string, mixed> $data */
    private function upsertModel(array $data): void
    {
        $provider = $this->nonEmptyString($data['provider'] ?? null, 'models[].provider');
        $code = $this->nonEmptyString($data['code'] ?? null, 'models[].code');

        if ($this->em->getRepository(Provider::class)->findOneBy(['code' => $provider]) === null) {
            throw new BillingException(sprintf('models: провайдер "%s" не найден.', $provider));
        }

        if ($this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $code]) === null) {
            $this->em->persist(new Model($provider, $code));
        }
    }

    /**
     * Обновление/создание одной строки цен по её натуральному ключу
     * (provider, model, valid_from). Провайдер и модель должны уже
     * существовать в справочнике — иначе ошибка. Используется и штатным
     * импортом, и reprice.
     *
     * @param array<string, mixed>|mixed $data
     */
    public function upsertPriceRow($data): PriceRow
    {
        if (!\is_array($data)) {
            throw new BillingException('prices: элемент должен быть объектом.');
        }

        $provider = $this->nonEmptyString($data['provider'] ?? null, 'prices[].provider');
        $model = $this->nonEmptyString($data['model'] ?? null, 'prices[].model');
        $validFrom = $this->parseTimestamp($data['valid_from'] ?? null, 'prices[].valid_from');
        $unit = $this->nonEmptyString($data['unit'] ?? null, 'prices[].unit');
        $currency = $data['currency'] ?? 'USD';
        $rates = $data['rates'];

        if (!\is_array($rates)) {
            throw new BillingException('prices[].rates: должен быть объектом.');
        }

        if ($this->em->getRepository(Provider::class)->findOneBy(['code' => $provider]) === null) {
            throw new BillingException(sprintf('prices: провайдер "%s" не найден.', $provider));
        }
        if ($this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $model]) === null) {
            throw new BillingException(sprintf('prices: модель "%s" провайдера "%s" не найдена.', $model, $provider));
        }

        $key = ['provider' => $provider, 'model' => $model, 'validFrom' => $validFrom];
        $entity = $this->em->getRepository(PriceRow::class)->findOneBy($key);
        if ($entity === null) {
            $entity = new PriceRow($provider, $model, $validFrom, $unit);
            $this->em->persist($entity);
        }

        try {
            $entity->apply($this->nonEmptyString($currency, 'prices[].currency'), $unit, $rates);
        } catch (\InvalidArgumentException $e) {
            throw new BillingException($e->getMessage());
        }

        return $entity;
    }

    /** @param array<string, mixed>|mixed $data */
    private function upsertPrice(array $data): void
    {
        $this->upsertPriceRow($data);
    }

    /** @return array<int, mixed> */
    private function section(array $data, string $name): array
    {
        $section = $data[$name] ?? [];
        if (!\is_array($section)) {
            throw new BillingException(sprintf('%s: должен быть массивом.', $name));
        }

        return $section;
    }

    /** @param mixed $value */
    private function nonEmptyString($value, string $context): string
    {
        if (!\is_string($value) && !\is_int($value) && !\is_float($value)) {
            throw new BillingException(sprintf('%s: обязательна непустая строка.', $context));
        }
        $value = \trim((string) $value);
        if ($value === '') {
            throw new BillingException(sprintf('%s: обязательна непустая строка.', $context));
        }

        return $value;
    }

    /** @param mixed $value */
    private function parseTimestamp($value, string $context): \DateTimeImmutable
    {
        if (!\is_string($value) || $value === '') {
            throw new BillingException(sprintf('%s: обязательна дата ISO-8601.', $context));
        }

        try {
            // Всегда интерпретируем без timezone — время в базе хранится UTC.
            return new \DateTimeImmutable($value, new \DateTimeZone("UTC"));
        } catch (\Exception $e) {
            throw new BillingException(sprintf('%s: некорректная дата ISO-8601 "%s".', $context, $value));
        }
    }

    /** @return array<string, mixed> */
    private function readJson(string $path): array
    {
        if (!\is_file($path)) {
            throw new BillingException(sprintf('Файл не найден: %s', $path));
        }

        $raw = \file_get_contents($path);
        if ($raw === false) {
            throw new BillingException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        $data = \json_decode($raw, true);
        if (!\is_array($data)) {
            throw new BillingException('JSON: корневой элемент должен быть объектом.');
        }

        return $data;
    }
}
