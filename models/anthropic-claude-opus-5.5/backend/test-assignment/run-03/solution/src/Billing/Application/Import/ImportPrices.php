<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InputException;
use App\Billing\Domain\Catalog\Catalog;
use App\Billing\Domain\Pricing\PriceCatalog;
use App\Billing\Infrastructure\Persistence\Doctrine\DoctrinePriceCatalog;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт истории цен: провайдеры, модели, строки цен.
 *
 * Файл разбирается целиком до первой записи, применение — в одной транзакции:
 * ошибка в данных не оставит базу в полуимпортированном состоянии.
 * Идемпотентность — по натуральным ключам (code / provider+code /
 * provider+model+valid_from).
 */
final class ImportPrices
{
    public function __construct(
        private readonly Catalog $catalog,
        private readonly PriceCatalog $prices,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(string $path): PriceImportResult
    {
        $file = (new PriceFileParser())->parse($path);

        $providers = 0;
        $models = 0;
        $prices = 0;

        $this->em->wrapInTransaction(function () use ($file, &$providers, &$models, &$prices): void {
            foreach ($file->providers as $provider) {
                $this->catalog->upsertProvider($provider);
                $this->em->flush();
                ++$providers;
            }

            foreach ($file->models as $model) {
                $this->catalog->upsertModel($model);
                $this->em->flush();
                ++$models;
            }

            foreach ($file->prices as $row) {
                // Строку цен нельзя привязать к модели, которой нет в справочнике:
                // явная ошибка лучше молчаливого пропуска.
                if (!$this->catalog->hasModel($row->model)) {
                    throw new InputException(sprintf(
                        'Цена для "%s" от %s: модель отсутствует в справочнике',
                        (string) $row->model,
                        $row->validFrom->format('Y-m-d\TH:i:s\Z'),
                    ));
                }

                $this->prices->upsert($row);
                ++$prices;
            }
        });

        return new PriceImportResult($providers, $models, $prices);
    }
}

final readonly class PriceImportResult
{
    public function __construct(
        public int $providers,
        public int $models,
        public int $prices,
    ) {
    }
}
