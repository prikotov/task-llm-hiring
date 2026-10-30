<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Inbound\PricesInputParser;
use App\Domain\Pricing\OffPeakWindow;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Exception\InvalidInputException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт истории цен (Providers / Models / Price rows).
 * Идемпотентный upsert по natural keys.
 *
 *   - providers:     upsert по code
 *   - models:        upsert по (provider, code)
 *   - prices:        upsert по (provider, model, valid_from)
 */
final class PricesImportService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProviderRepository $providers,
        private ModelRepository $models,
        private PriceRepository $prices,
    ) {
    }

    /**
     * @throws InvalidInputException
     * @throws \RuntimeException
     */
    public function importFile(string $path): void
    {
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $path));
        }
        $json = file_get_contents($path);
        if ($json === false) {
            throw new \RuntimeException(sprintf('Cannot read: %s', $path));
        }

        $this->import($json);
    }

    /**
     * @throws InvalidInputException
     */
    public function import(string $json): void
    {
        $data = PricesInputParser::parse($json);

        // 1. Провайдеры (upsert по code).
        $providers = [];
        foreach ($data['providers'] as $row) {
            $code = $row['code'];
            /** @var Provider $provider */
            $provider = $this->providers->findOneBy(['code' => $code]) ?? new Provider();
            if ($provider->id() === null) {
                $this->em->persist($provider);
            }
            $provider->withIdentity($code, $row['name']);
            $off = $row['off_peak'];
            if ($off === null) {
                $provider->setOffPeak(null, null, null);
            } else {
                /** @var OffPeakWindow $off */
                $provider->setOffPeak($off->windowStart, $off->windowEnd, $off->multiplier);
            }
            $providers[$code] = $provider;
        }

        // 2. Модели (upsert по (provider, code)).
        $models = [];
        foreach ($data['models'] as $row) {
            $key = $row['provider'].'|'.$row['code'];
            /** @var Model $model */
            $model = $this->models->findOneBy([
                'providerCode' => $row['provider'],
                'code' => $row['code'],
            ]) ?? new Model();
            if ($model->id() === null) {
                $this->em->persist($model);
            }
            $model->withIdentity($row['provider'], $row['code']);
            $models[$key] = $model;
        }

        // 3. Строки цен (upsert по (provider, model, valid_from)).
        foreach ($data['prices'] as $row) {
            $existing = $this->prices->findOneBy([
                'providerCode' => $row['provider'],
                'modelCode' => $row['model'],
                'validFrom' => $row['valid_from'],
            ]);

            if ($existing === null) {
                /** @var Price $rowEntity */
                $rowEntity = Price::create(
                    $row['provider'],
                    $row['model'],
                    $row['valid_from'],
                    $row['currency'],
                    $row['unit'],
                    $row['rates'],
                );
                $this->em->persist($rowEntity);
            } else {
                // Повторный импорт: перепищим только если есть изменения.
                $changed = $existing->currency() !== $row['currency']
                    || $existing->unit() !== $row['unit']
                    || $existing->rateInput() !== ($row['rates']['input'] ?? null)
                    || $existing->rateOutput() !== ($row['rates']['output'] ?? null)
                    || $existing->rateCacheRead() !== ($row['rates']['cache_read'] ?? null)
                    || $existing->rateCacheWrite() !== ($row['rates']['cache_write'] ?? null)
                    || $existing->rateAny() !== ($row['rates']['any'] ?? null);

                if ($changed) {
                    $existing->replaceRates($row['currency'], $row['unit'], $row['rates']);
                }
            }
        }

        $this->em->flush();
        $this->em->clear();
    }
}
