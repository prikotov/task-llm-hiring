<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\BillingCalculator;
use App\Domain\Money;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\PriceUnit;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Entity\UsageEventStatus;
use App\Shared\ImportException;
use App\Shared\InputValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Применяет исправленные строки цен (upsert по (provider, model, valid_from);
 * провайдер и модель должны существовать в базе — секции providers/models
 * в файле коррекции игнорируются), затем пересчитывает события,
 * тарифицировавшиеся заменёнными строками.
 */
final class RepricingService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @return array{0: int, 1: string} [число пересчитанных событий, дельта «новая минус старая» в 6 знаках] */
    public function reprice(string $path): array
    {
        $data = $this->readJsonFile($path);
        $rawPrices = InputValidator::requireStringArray($data, 'prices', 'reprice');
        $priceFieldParser = new PriceFields();

        $providerByCode = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $provider) {
            $providerByCode[$provider->getCode()] = $provider;
        }

        $replacedRowIds = [];
        $result = ['recounted' => 0, 'oldSum' => '0.000000', 'newSum' => '0.000000'];

        $this->em->wrapInTransaction(function () use ($rawPrices, $providerByCode, $priceFieldParser, &$replacedRowIds, &$result): void {
            $rows = [];
            foreach ($rawPrices as $i => $entry) {
                $ctx = sprintf('reprice: prices[%d]', $i);
                $providerCode = InputValidator::requireString((array) $entry, 'provider', $ctx);
                $modelCode = InputValidator::requireString((array) $entry, 'model', $ctx);
                $validFrom = InputValidator::utcDateTime(InputValidator::requireString((array) $entry, 'valid_from', $ctx), $ctx);

                [$unit, $rateInput, $rateOutput, $rateCacheRead, $rateCacheWrite, $rateAny] =
                    $priceFieldParser->parse((array) $entry, $ctx);

                $provider = $providerByCode[$providerCode] ?? null;
                if ($provider === null) {
                    throw new ImportException(sprintf('%s: провайдер "%s" не существует в справочнике', $ctx, $providerCode));
                }
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $modelCode]);
                if ($model === null) {
                    throw new ImportException(sprintf('%s: модель "%s/%s" не существует в справочнике', $ctx, $providerCode, $modelCode));
                }

                $row = $this->em->getRepository(PriceRow::class)->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
                if ($row === null) {
                    $row = new PriceRow($model, $validFrom);
                } else {
                    $replacedRowIds[$row->getId()] = true; // строка существовала и была заменена
                }
                $row->setCurrency(InputValidator::requireString((array) $entry, 'currency', $ctx));
                $row->applyUnitAndRates($unit, $rateInput, $rateOutput, $rateCacheRead, $rateCacheWrite, $rateAny);
                $this->em->persist($row);
            }
            $this->em->flush();

            $replacedRowMap = [];
            foreach (array_keys($replacedRowIds) as $id) {
                $replacedRowMap[$id] = $this->em->find(PriceRow::class, $id);
            }

            if (count($replacedRowMap) === 0) {
                return;
            }

            foreach ($this->em->getRepository(UsageEvent::class)->findBy(['status' => UsageEventStatus::RATED]) as $event) {
                $row = $event->getPriceRow();
                if ($row === null || !isset($replacedRowMap[$row->getId()])) {
                    continue;
                }

                $provider = $providerByCode[$event->getProvider()] ?? null;
                $exact = BillingCalculator::computeExact(
                    $row,
                    $provider,
                    $event->getTs(),
                    $event->getTokensInput(),
                    $event->getTokensOutput(),
                    $event->getTokensCacheRead(),
                    $event->getTokensCacheWrite(),
                );
                if ($exact === null) {
                    continue; // не ожидается; событие не трогаем
                }

                $newCost = Money::round6($exact);
                $result['oldSum'] = Money::add($result['oldSum'], (string) $event->getCostUsd());
                $result['newSum'] = Money::add($result['newSum'], $newCost);
                $result['recounted']++;
                $event->markRated($newCost, $row);
            }
            $this->em->flush();
        });

        $delta = bcsub($result['newSum'], $result['oldSum'], 6);
        return [$result['recounted'], Money::format6($delta)];
    }

    /** @return array<string, mixed> */
    private function readJsonFile(string $path): array
    {
        if (!is_readable($path)) {
            throw new ImportException(sprintf('reprice: файл "%s" недоступен для чтения', $path));
        }
        try {
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('reprice: некорректный JSON в "%s": %s', $path, $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException('reprice: корень JSON должен быть объектом');
        }

        return $data;
    }
}
