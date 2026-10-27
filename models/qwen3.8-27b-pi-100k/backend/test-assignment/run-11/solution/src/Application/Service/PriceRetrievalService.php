<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Entity\Model;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Вспомогательная сервисная обёртка на репозиторий цен. Удобно для
 * unit-тестов и внешних потреблителей.
 */
final class PriceRetrievalService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function get(Model $model, \DateTimeImmutable $ts): ?Price
    {
        $rows = $this->em->getRepository(Price::class)->findBy(
            ['model' => $model],
            ['validFrom' => 'DESC']
        );
        foreach ($rows as $row) {
            if ($row->validFrom() <= $ts) {
                return $row;
            }
        }
        return null;
    }
}
