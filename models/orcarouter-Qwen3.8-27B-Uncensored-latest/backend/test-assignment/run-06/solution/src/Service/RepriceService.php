<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\DomainException;
use App\Entity\PriceRow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цен (бонус TASK.md).
 *
 * Применяет исправленные строки цен (upsert по (provider, model, valid_from)),
 * где провайдер и модель обязаны существовать в базе, а не в файле; затем
 * RatingService пересчитывает события, тарифицированные этими строками.
 */
final class RepriceService
{
    public function __construct(
        private readonly PricesImporter $importer,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return list<PriceRow> затронутые строки цен
     */
    public function applyCorrections(string $file): array
    {
        $touched = $this->importer->import($file, true);
        if ($touched === []) {
            throw new DomainException('Файл коррекции: секция "prices" обязательна и не может быть пустой');
        }

        return $touched;
    }
}
