<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Catalog\Model;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\Provider;
use App\Domain\Usage\UsageEvent;
use App\Domain\ValueObject\OffPeak;
use App\Importer\ImportException;
use App\Importer\PriceFile;
use Doctrine\ORM\EntityManagerInterface;

use function bcsub;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Файл коррекции — тот же формат, что файл цен; секции providers/models
 * могут отсутствовать или быть пустыми: провайдер и модель должны
 * существовать в справочнике БД.
 *
 * Механизм:
 *  1) строки цен из файла применяются upsert-ом по (provider, model,
 *     valid_from) по данным уже загруженного справочника;
 *  2) события, тарифицировавшиеся именно этими строками (ключ
 *     применённой строки хранится в applied_price_valid_from),
 *     пересчитываются по актуальной строке цены;
 *  3) стоимость события перезаписывается; дельта = новое − старая сумма.
 * События, рассчитанные по другим строкам, не изменяются.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator,
    ) {
    }

    /**
     * @throws ImportException
     */
    public function reprice(string $path): array
    {
        $file = PriceFile::parse($path, requirePrices: false);

        return $this->em->wrapInTransaction(function () use ($file): array {
            $conn = $this->em->getConnection();
            $providerCodes = $conn->executeQuery('SELECT code FROM providers')->fetchFirstColumn();
            $modelKeys = $conn->executeQuery("SELECT provider || '|' || code FROM models")->fetchFirstColumn();

            $affectedKeys = [];
            foreach ($file->prices as $spec) {
                if (!in_array($spec['provider'], $providerCodes, true)) {
                    throw new ImportException(sprintf('prices: провайдер "%s" не найден в справочнике', $spec['provider']));
                }
                if (!in_array($spec['provider'].'|'.$spec['model'], $modelKeys, true)) {
                    throw new ImportException(sprintf('prices: модель "%s" не найдена в справочнике', $spec['provider'].'/'.$spec['model']));
                }
                $this->upsertPrice($spec);
                $affectedKeys[] = $spec['provider'].'|'.$spec['model'].'|'.$spec['validFrom']->getTimestamp();
            }

            $recomputed = 0;
            $delta = '0';
            foreach ($this->findAffectedEvents($affectedKeys) as $event) {
                $price = $this->em->getRepository(PriceRow::class)->findOneBy([
                    'provider' => $event->provider(),
                    'model' => $event->model(),
                    'validFrom' => $event->appliedPriceValidFrom(),
                ]);
                if ($price === null) {
                    throw new \LogicException('Заменённая строка цен не найдена после upsert');
                }

                $newCost = $this->calculator->compute(
                    $price,
                    $event->tokens(),
                    $this->findOffPeak($event->provider()),
                    $event->ts(),
                );
                $old = (string) $event->cost();
                $new = $newCost->format6();
                $delta = bcadd($delta, bcsub($new, $old, 24), 24);
                $event->setCost($new);
                $recomputed++;
            }

            return ['events' => $recomputed, 'delta' => bcadd($delta, '0', 6)];
        });
    }

    /**
     * События, рассчитанные по строкам, затронутым коррекцией.
     *
     * @param list<string> $affectedKeys "provider|model|unix-ts valid_from"
     *
     * @return UsageEvent[]
     */
    private function findAffectedEvents(array $affectedKeys): array
    {
        if ($affectedKeys === []) {
            return [];
        }
        $affected = array_flip($affectedKeys);

        /** @var UsageEvent[] $events */
        $events = $this->em->getRepository(UsageEvent::class)->findBy(['rated' => true]);

        return array_values(array_filter($events, static function (UsageEvent $event) use ($affected): bool {
            $applied = $event->appliedPriceValidFrom();
            if ($applied === null) {
                return false;
            }

            $key = $event->provider().'|'.$event->model().'|'.$applied->getTimestamp();

            return isset($affected[$key]);
        }));
    }

    private function findOffPeak(string $provider): ?OffPeak
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $provider]);

        return $provider?->offPeak();
    }

    private function upsertPrice(array $spec): void
    {
        $repository = $this->em->getRepository(PriceRow::class);
        $row = $repository->findOneBy([
            'provider' => $spec['provider'],
            'model' => $spec['model'],
            'validFrom' => $spec['validFrom'],
        ]);

        if ($row === null) {
            $row = new PriceRow();
            $row->setProvider($spec['provider']);
            $row->setModel($spec['model']);
            $row->setValidFrom($spec['validFrom']);
            $this->em->persist($row);
        }
        $row->set($spec['currency'], $spec['unit'], $spec['rates']);
    }
}
