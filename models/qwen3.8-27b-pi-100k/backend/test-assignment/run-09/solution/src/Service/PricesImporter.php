<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\OffPeakWindow;
use App\Entity\Provider;
use App\Entity\TokenModel;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентная загрузка справочника: провайдеры (по code),
 * модели (по provider+code), строки цен (по provider+model+valid_from).
 */
final class PricesImporter
{
    /** @var list<string> */
    public array $errors = [];

    public function __construct(
        private readonly BillingRepository $repo,
        private readonly PriceRowWriter $priceRowWriter,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function import(string $json): void
    {
        $data = json_decode($json, true, 128, \JSON_THROW_ON_ERROR);

        if (!\is_array($data)) {
            throw new \RuntimeException('Некорректный JSON: ожидается объект верхнего уровня');
        }

        $this->importProviders(Validators::list($data, 'providers'));
        $this->em->flush();
        $this->importModels(Validators::list($data, 'models'));
        $this->em->flush();
        $this->importPrices(Validators::list($data, 'prices'));
        $this->em->flush();
    }

    /**
     * @param list<array<string, mixed>> $providers
     */
    private function importProviders(array $providers): void
    {
        foreach ($providers as $raw) {
            $code = Validators::string($raw, 'code');
            $name = ($raw['name'] ?? null) ?: $code;
            if (!\is_string($name) || '' === trim($name)) {
                throw new \DomainException(sprintf('Некорректное имя провайдера "%s"', $name));
            }
            $name = trim($name);

            $provider = $this->repo->findProviderByCode($code) ?? new Provider();
            if (null === $provider->getId()) {
                $this->em->persist($provider);
            }

            $provider->setCode($code)->setName($name);

            $offPeak = $raw['off_peak'] ?? null;
            if (null === $offPeak) {
                $provider->setOffPeak(null, null, null);
            } else {
                \is_array($offPeak) || throw new \DomainException(sprintf('Некорректный off_peak у провайдера "%s"', $code));
                $ws = Validators::string($offPeak, 'window_start');
                $we = Validators::string($offPeak, 'window_end');
                OffPeakWindow::parseMinutes($ws);
                OffPeakWindow::parseMinutes($we);
                $mult = Validators::decimal($offPeak, 'multiplier');
                $provider->setOffPeak($ws, $we, $mult);
            }
        }
    }

    /**
     * @param list<array<string, mixed>> $models
     */
    private function importModels(array $models): void
    {
        foreach ($models as $raw) {
            $providerCode = Validators::string($raw, 'provider');
            $code = Validators::string($raw, 'code');

            $provider = $this->repo->findProviderByCode($providerCode);
            if (null === $provider) {
                throw new \DomainException(sprintf('Модель "%s/%s" ссылается на неизвестного провайдера "%s"', $providerCode, $code, $providerCode));
            }

            $model = $this->repo->findModelByProviderAndCode($provider, $code);
            if (null === $model) {
                $model = new TokenModel();
                $model->setProvider($provider)->setCode($code);
                $this->em->persist($model);
            }
        }
    }

    /**
     * @param list<array<string, mixed>> $prices
     */
    private function importPrices(array $prices): void
    {
        foreach ($prices as $raw) {
            if (!\is_array($raw)) {
                throw new \DomainException('Строка цены должна быть объектом');
            }

            $this->priceRowWriter->upsert($raw);
        }
    }
}
