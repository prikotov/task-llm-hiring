<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\Provider;
use App\Billing\Domain\Pricing\PriceRow;

/**
 * Разобранное содержимое файла истории цен (или файла ретро-коррекции):
 * данные уже валидны и превращены в доменные объекты.
 */
final readonly class PriceFile
{
    /**
     * @param list<Provider>  $providers
     * @param list<ModelReference> $models
     * @param list<PriceRow>  $prices
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $prices,
    ) {
    }
}
