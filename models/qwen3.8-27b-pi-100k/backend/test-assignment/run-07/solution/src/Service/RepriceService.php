<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\BillingComputer;
use App\Domain\CatalogParser;
use App\Domain\Money;
use App\Domain\PriceUnit;
use App\Domain\RepriceResult;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Retro-коррекция цены (app:billing:reprice).
 *
 * Файл-коррекция формата истории цен; секции providers/models в нём
 * могут отсутствовать и не участвуют в загрузке:
 *  - провайдер и модель должны существовать в справочнике БД
 *    (иначе ошибка, ничего не применяется);
 *  - строки цен upsert-ятся по (provider, model, valid_from).
 *
 * Пересчитываются события, тарифицировавшиеся заменёнными строками
 * (у события зафиксирована применённая строка цены): новая стоимость
 * считается теми же правилами — по токен-разбивке события, новой схеме/
 * ставкам строки, оф-пику провайдера и его моменту. Механизм —
 * перезапись стоимости рассчитанных событий (без задвоений в отчёте).
 *
 * Вывод: REPRICED_EVENTS=<n> и REPRICE_DELTA_USD=<delta, 6 знаков>
 * (дельта — новая минус старая сумма их стоимостей).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogParser $parser,
        private readonly BillingComputer $computer,
    ) {
    }

    public function reprice(string $filePath): RepriceResult
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new ImportException("Файл не найден или недоступен: {$filePath}");
        }

        $raw = file_get_contents($filePath);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: {$filePath}");
        }

        $catalog = $this->parser->parse($raw);
        if ($catalog->prices === []) {
            throw new ImportException('В файле коррекции не найдено ни одной строки цены');
        }

        return $this->em->wrapInTransaction(function () use ($catalog): RepriceResult {
            $affected = $this->collectAffectedEventsAndUpsert($catalog->prices);

            $snapshot = CatalogSnapshot::load($this->em);

            $oldTotal = Money::zero();
            $newTotal = Money::zero();

            foreach ($affected as $event) {
                $oldTotal = $oldTotal->add(Money::fromDecimal((string) $event->getCost()));

                $priceRow = $event->getPriceRow();
                $newCost = $this->computer
                    ->calculate(
                        $event->getTokensInput(),
                        $event->getTokensOutput(),
                        $event->getTokensCacheRead(),
                        $event->getTokensCacheWrite(),
                        PriceUnit::from($priceRow->getUnit()),
                        $priceRow->getRates(),
                        $snapshot->offPeakFor($event->getProvider()),
                        $event->getTs(),
                    )
                    ->roundHalfUp();

                $event->resetCost($newCost);
                $newTotal = $newTotal->add($newCost);
            }

            $this->em->flush();

            $delta = bcsub($newTotal->toString(), $oldTotal->toString(), 6);

            return new RepriceResult(count($affected), bcadd($delta, '0', 6));
        });
    }

    /**
     * Ссылки проверяем до применения (провайдер/модель — из базы),
     * собираем события, рассчитанные затронутыми строками, и upsert-им
     * строки цен.
     *
     * @param list<\App\Domain\Catalog\PriceSpec> $specs
     *
     * @return UsageEvent[] ключевыми id
     */
    private function collectAffectedEventsAndUpsert(array $specs): array
    {
        $em = $this->em;
        /** @var array<int, UsageEvent> $affected */
        $affected = [];

        foreach ($specs as $spec) {
            $provider = $em->getRepository(Provider::class)->findOneBy(['code' => $spec->provider]);
            if ($provider === null) {
                throw new ImportException(
                    "Строка цены \"{$spec->provider}/{$spec->model}\": провайдер \"{$spec->provider}\" "
                    . 'не найден в справочнике'
                );
            }

            $model = $em->createQuery(
                'SELECT m FROM ' . Model::class . ' m WHERE m.provider = :provider AND m.code = :code'
            )
                ->setParameter('provider', $provider)
                ->setParameter('code', $spec->model)
                ->getOneOrNullResult();
            if ($model === null) {
                throw new ImportException(
                    "Строка цены \"{$spec->provider}/{$spec->model}\": модель не найдена в справочнике"
                );
            }

            $existing = $em->createQuery(
                'SELECT pr FROM ' . PriceRow::class . ' pr WHERE pr.model = :model AND pr.validFrom = :validFrom'
            )
                ->setParameter('model', $model)
                ->setParameter('validFrom', $spec->validFrom)
                ->getOneOrNullResult();

            if ($existing !== null) {
                $events = $em->createQuery(
                    'SELECT e FROM ' . UsageEvent::class . ' e '
                    . 'WHERE e.priceRow = :priceRow AND e.status = :status'
                )
                    ->setParameter('priceRow', $existing)
                    ->setParameter('status', UsageEvent::STATUS_RATED)
                    ->getResult();

                foreach ($events as $event) {
                    $affected[$event->getId()] = $event;
                }
            }

            if ($existing === null) {
                $em->persist($existing = new PriceRow($model, $spec));
            } else {
                $existing->replaceWith($spec);
            }
        }

        return array_values($affected);
    }
}
