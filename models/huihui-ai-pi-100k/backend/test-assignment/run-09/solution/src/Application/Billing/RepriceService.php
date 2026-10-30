<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Catalog\CatalogParser;
use App\Application\Catalog\CatalogWriter;
use App\Domain\Decimal;
use App\Domain\Rater;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Применяются исправленные строки цен (upsert по
 * (provider, model, valid_from); провайдер и модель должны
 * существовать в базе — секции providers/models в файле коррекции
 * необязательны), затем пересчитываются события, тарифицированные
 * именно этой строкой: стоимость события перезаписывается, поэтому
 * итоговый отчёт показывает новые суммы без задвоений.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogWriter $writer,
    ) {
    }

    /**
     * @return array{repriced: int, delta: string}
     */
    public function reprice(string $file): array
    {
        $data = self::readFile($file);
        // providers/models в файле коррекции допустимы, но не обязательны.
        $prices = CatalogParser::parsePrices($data['prices'] ?? []);
        if ($prices === []) {
            throw new ImportException('В файле коррекции нет ни одной строки цен (секция prices)');
        }

        return \App\Infrastructure\Transactor::run($this->em, function () use ($prices) {
            $count = 0;
            $delta = '0';

            foreach ($prices as $spec) {
                $providerId = $this->writer->providerId($spec['provider']);
                if ($providerId === null) {
                    throw new ImportException(sprintf('репрайс: провайдер "%s" не найден в справочнике', $spec['provider']));
                }
                $modelId = $this->writer->modelId($providerId, $spec['model']);
                if ($modelId === null) {
                    throw new ImportException(sprintf('репрайс: модель "%s/%s" не найдена в справочнике', $spec['provider'], $spec['model']));
                }

                $row = $this->writer->upsertPriceRow(
                    $providerId,
                    $modelId,
                    $spec['validFrom'],
                    $spec['currency'],
                    $spec['rates'],
                );

                $provider = $this->em->find(Provider::class, $providerId);
                $offPeak = $provider?->offPeak();

                /** @var UsageEvent[] $events */
                $events = $this->em->createQuery(
                    'SELECT e FROM App\Entity\UsageEvent e WHERE e.priceRowId = :id AND e.status = :status ORDER BY e.id ASC'
                )
                    ->setParameter('id', $row->getId())
                    ->setParameter('status', UsageEvent::STATUS_RATED)
                    ->getResult();

                foreach ($events as $event) {
                    $newCost = Rater::cost($event->tokens(), $row->rates(), $offPeak, $event->getTs());
                    $oldCost = $event->getCostUsd() ?? '0';
                    $delta = bcadd($delta, bcsub($newCost, $oldCost, Decimal::SCALE), Decimal::SCALE);
                    $event->setCostUsd($newCost);
                    $count++;
                }
            }

            return ['repriced' => $count, 'delta' => Decimal::format6($delta)];
        });
    }

    private static function readFile(string $file): array
    {
        if (!is_file($file)) {
            throw new ImportException("Файл не найден: $file");
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: $file");
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new ImportException('Некорректный JSON: верхний уровень должен быть объектом; ' . json_last_error_msg());
        }

        return $data;
    }
}
