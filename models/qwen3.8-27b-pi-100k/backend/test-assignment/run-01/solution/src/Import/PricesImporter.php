<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Model;
use App\Entity\Provider;
use App\Repository\ModelRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Загрузка справочника (провайдеры, модели, строки цен) атомарным upsert'ом.
 * Повторная загрузка тех же данных не изменяет хранимые значения — обновление
 * происходит только при фактическом расхождении значений.
 */
final class PricesImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providerRepository,
        private readonly ModelRepository $modelRepository,
        private readonly PriceCatalog $priceCatalog,
    ) {
    }

    /** @return array{providers:int, models:int, priceRows:int} */
    public function import(PricesPayload $payload): array
    {
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $counters = ['providers' => 0, 'models' => 0, 'priceRows' => 0];
            $providerCodesInFile = [];

            foreach ($payload->providers as $spec) {
                $providerCodesInFile[] = $spec->code;
                $this->upsertProvider($spec);
                ++$counters['providers'];
            }

            foreach ($payload->models as $spec) {
                $this->requireKnownProvider($spec->provider, $providerCodesInFile);
                $this->upsertModel($spec);
                ++$counters['models'];
            }

            // Сначала фиксируем провайдеры и модели: строки цен ссылаются на них,
            // а их сущности на этом этапе ещё только в очереди persist.
            $this->em->flush();

            foreach ($payload->priceRows as $spec) {
                $this->requireKnownProvider($spec->provider, $providerCodesInFile);
                $this->requireKnownModel($spec->provider, $spec->model, $payload);
                $this->priceCatalog->upsert($spec);
                ++$counters['priceRows'];
            }

            $this->em->flush();
            $conn->commit();

            return $counters;
        } catch (ImportException $e) {
            $this->rollback($conn->isTransactionActive());
            throw $e;
        } catch (Throwable $e) {
            $this->rollback($conn->isTransactionActive());
            throw new ImportException('Не удалось загрузить справочник цен: ' . $e->getMessage(), 0, $e);
        }
    }

    private function upsertProvider(ProviderSpec $spec): void
    {
        $provider = $this->providerRepository->findByCode($spec->code) ?? new Provider($spec->code, $spec->name);
        $provider->setName($spec->name);
        if ($spec->offPeakTouched) {
            $provider->applyOffPeak($spec->offPeakStartMinute, $spec->offPeakEndMinute, $spec->offPeakMultiplier);
        }
        $this->em->persist($provider);
    }

    private function upsertModel(ModelSpec $spec): void
    {
        $model = $this->modelRepository->findOneByProviderAndCode($spec->provider, $spec->code);
        if ($model === null) {
            $this->em->persist(new Model($spec->provider, $spec->code));
        }
    }

    /** @param list<string> $providerCodesInFile @param PricesPayload $payload */
    private function requireKnownProvider(string $providerCode, array $providerCodesInFile): void
    {
        if (\in_array($providerCode, $providerCodesInFile, true) || $this->providerRepository->findByCode($providerCode) !== null) {
            return;
        }

        throw new ImportException(sprintf('Провайдер "%s" не найден в файле и в существующем справочнике.', $providerCode));
    }

    /** @param PricesPayload $payload */
    private function requireKnownModel(string $providerCode, string $modelCode, PricesPayload $payload): void
    {
        foreach ($payload->models as $spec) {
            if ($spec->provider === $providerCode && $spec->code === $modelCode) {
                return;
            }
        }
        if ($this->modelRepository->findOneByProviderAndCode($providerCode, $modelCode) !== null) {
            return;
        }

        throw new ImportException(sprintf('Модель %s/%s не найдена в файле и в существующем справочнике.', $providerCode, $modelCode));
    }

    private function rollback(bool $inTransaction): void
    {
        if ($inTransaction) {
            $this->em->getConnection()->rollBack();
        }
    }
}
