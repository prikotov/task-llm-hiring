<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Import\CatalogRepository;
use App\Application\Import\ImportException;
use App\Application\Import\PricesParser;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Применение исправленной строки цен (упsert по (provider, model, valid_from)).
 *
 * Файл коррекции имеет тот же формат, что и `prices.json` — но
 * секции `providers` / `models` могут отсутствовать или быть пустыми.
 *
 * @see TASK.md → «Бонус»
 */
final class RepriceService
{
    public function __construct(
        private readonly PricesParser $parser,
        private readonly CatalogRepository $catalog,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Применяем файл коррекции и возвращаем список затронутых строк цен
     * (после обновления в БД).
     *
     * @return list<Price>
     *
     * @throws ImportException
     */
    public function repriceFile(string $file): array
    {
        if (!is_file($file)) {
            throw ImportException::json(sprintf('файл не найден: %s', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw ImportException::json(sprintf('не удалось прочитать: %s', $file));
        }

        $data = $this->parser->parse($raw);

        /** @var list<Price> $affected */
        $affected = [];

        $this->em->getConnection()->transactional(function () use ($data, &$affected) {
            foreach ($data['prices'] as $pr) {
                $provider = $this->catalog->providerByCode($pr['provider']);
                if ($provider === null) {
                    throw ImportException::missingProvider($pr['provider']);
                }

                $model = $this->catalog->modelByProviderAndCode($pr['provider'], $pr['model']);
                $validFrom = new \DateTimeImmutable($pr['valid_from']);

                $existing = $this->catalog->priceByProviderModelValidFrom($provider, $model, $validFrom);
                if ($existing === null) {
                    throw new ImportException(sprintf(
                        'Строка (provider=%s, model=%s, valid_from=%s) не найдена в справочнике — коррекция неприменима.',
                        $pr['provider'],
                        $pr['model'],
                        $validFrom->format(\DateTimeInterface::ATOM),
                    ));
                }

                $existing->setUnitAndRates($pr['unit'], $pr['rates']);
                $affected[] = $existing;
            }

            $this->em->flush();
        });

        return $affected;
    }
}
