<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\CostCalculator;
use App\Domain\Money\Decimal;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Exception\InputError;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (app:billing:reprice).
 *
 * - применяет исправленные строки цен (upsert по (provider, model, valid_from));
 *   провайдер и модель должны уже существовать в базе;
 * - пересчитывает рассчитанные события, тарифицировавшиеся заменёнными
 *   строками; дельта — новая минус старая сумма их стоимостей;
 * - итоговый app:billing:report показывает пересчитанные суммы без задвоений
 *   (стоимость события перезаписывается, а не доначисляется).
 */
final class RepriceService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PriceFileParser $parser,
        private UsageEventRepository $events,
        private CostCalculator $calc,
    ) {
    }

    /**
     * @return array{events:int, delta:string}
     */
    public function reprice(string $path): array
    {
        $parsed = $this->readAndParse($path);

        $result = ['events' => 0, 'delta' => '0'];

        $this->em->wrapInTransaction(function () use ($parsed, &$result): void {
            foreach ($parsed['prices'] as $pData) {
                $provider = $this->em->getRepository(Provider::class)->findByCode($pData->provider);
                if ($provider === null) {
                    throw InputError::because(string: 'reprice ' . $pData->provider . '/' . $pData->model
                        . ': провайдер "' . $pData->provider . '" не найден в справочнике');
                }

                $model = $this->em->getRepository(\App\Entity\Model::class)
                    ->findOneBy(['provider' => $provider, 'code' => $pData->model]);
                if ($model === null) {
                    throw InputError::because(string: 'reprice ' . $pData->provider . '/' . $pData->model
                        . ': модель "' . $pData->model . '" не найдена в справочнике');
                }

                $priceRow = $this->em->getRepository(PriceRow::class)
                    ->findOneBy(['model' => $model, 'validFrom' => $pData->validFrom]);

                if ($priceRow === null) {
                    $priceRow = new PriceRow($provider, $model, $pData->validFrom, $pData->unit->value, $pData->rates, $pData->currency);
                    $this->em->persist($priceRow);
                    // новая строка — старых событий по ней нет
                    continue;
                }

                // 1) Собираем рассчитанные события этой строки (до обновления ставок).
                if ($priceRow->id() === null) {
                    // в рамках одной транзакции id ещё не назначена
                    $this->em->flush([$priceRow]);
                }
                $affected = $this->events->findRatedByPriceRow((int) $priceRow->id());

                // 2) Применяем исправление в месте (та же строка, тот же id).
                $priceRow->update($pData->unit->value, $pData->rates, $pData->currency);

                // 3) Пересчитываем и перезаписываем стоимость каждого события.
                foreach ($affected as $event) {
                    $old = (string) ($event->cost() ?? '0');
                    $new = $this->calc->rate(
                        $event->counts(),
                        $priceRow->unit(),
                        $priceRow->rates(),
                        $priceRow->provider()->offPeak(),
                        $event->minuteOfDayUtc(),
                    );

                    $event->setCost($new);
                    $result['events']++;
                    $result['delta'] = Decimal::add($result['delta'], Decimal::sub($new, $old));
                }
            }
        });

        // Дельта — 6 знаков, со знаком.
        $result['delta'] = Decimal::toSixDecimals($result['delta']);

        return $result;
    }

    private function readAndParse(string $path): array
    {
        if (!is_file($path)) {
            throw InputError::because(string: 'Файл коррекции не найден: ' . $path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw InputError::because(string: 'Файл коррекции не читается: ' . $path);
        }

        return $this->parser->parse($json);
    }
}
