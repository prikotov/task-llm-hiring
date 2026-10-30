<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Entity\UsageEvent;
use App\Billing\Parser\PricesCatalogLoader;
use App\Billing\Store\Store;
use App\Domain\Billing\Money;
use Doctrine\DBAL\ArrayParameterType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Пересчитывает события по заменённым строкам цен (файл коррекции).',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly Store $store,
        private readonly PricesCatalogLoader $loader,
        private readonly \App\Billing\Engine\RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = (string) $input->getArgument('file');
        if (!is_file($path)) {
            $io->getErrorStyle()->error(sprintf('Файл не найден: %s', $path));

            return Command::FAILURE;
        }

        $affectedRows = [];

        // Парсим коррекции: prices[] обязательна; providers/models необязательно
        $raw = @file_get_contents($path);
        if ($raw === false) {
            $io->getErrorStyle()->error('Не могу открыть файл коррекции.');

            return Command::FAILURE;
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $io->getErrorStyle()->error('Некорректный JSON коррекции: ' . $e->getMessage());

            return Command::FAILURE;
        }

        if (!is_array($data)) {
            $io->getErrorStyle()->error('JSON коррекции должен быть объектом.');

            return Command::FAILURE;
        }

        // Сначала — провайдеры (если есть)
        $providers = $data['providers'] ?? [];
        if (is_array($providers)) {
            foreach ($providers as $p) {
                $this->upsertProvider($p);
            }
        }

        // Затем — модели (если есть)
        $models = $data['models'] ?? [];
        if (is_array($models)) {
            foreach ($models as $m) {
                $this->upsertModel($m);
            }
        }

        // Далее — сами строки цен (обязательная часть)
        $prices = $data['prices'] ?? [];
        if (is_array($prices)) {
            foreach ($prices as $r) {
                $rowId = $this->upsertPriceRow($r);
                $affectedRows[] = $rowId;
            }
        } else {
            $io->getErrorStyle()->error('Файл коррекции должен содержать секцию "prices".');

            return Command::FAILURE;
        }

        $this->store->flush();

        // Пересчёт
        try {
            $stats = $this->repriceService->repricePriceRows($affectedRows);
        } catch (\Throwable $e) {
            $io->getErrorStyle()->error('Ошибка пересчёта: ' . $e->getMessage());

            return Command::FAILURE;
        }

        // Вывод ровно две строки в stdout.
        $output->writeln('REPRICED_EVENTS=' . $stats['events']);
        $output->writeln('REPRICE_DELTA_USD=' . Money::from($stats['delta_usd'])->formatMicros());

        return Command::SUCCESS;
    }

    /** @param array<string, mixed> $p */
    private function upsertProvider(array $p): void
    {
        $provider = new \App\Billing\Entity\Provider((string) $p['code']);
        $provider->name = (string) ($p['name'] ?? '');
        $offPeak = $p['off_peak'] ?? null;
        if (is_array($offPeak)) {
            $provider->offPeakStartMinutes = self::minutes((string) $offPeak['window_start']);
            $provider->offPeakEndMinutes = self::minutes((string) $offPeak['window_end']);
            $provider->offPeakMultiplier = (string) ($offPeak['multiplier'] ?? '1');
        }
        $this->store->upsertProvider($provider);
    }

    /** @param array<string, mixed> $m */
    private function upsertModel(array $m): void
    {
        $provider = (string) $m['provider'];
        $code = (string) $m['code'];
        $id = \App\Billing\Entity\EntityId::model($provider, $code);
        $model = new \App\Billing\Entity\Model($id);
        $model->providerCode = $provider;
        $model->code = $code;
        $this->store->upsertModel($model);
    }

    /** @param array<string, mixed> $r @return string priceRow id */
    private function upsertPriceRow(array $r): string
    {
        $provider = (string) $r['provider'];
        $model = (string) $r['model'];
        $validFrom = new \DateTimeImmutable((string) $r['valid_from'], new \DateTimeZone('UTC'));
        $id = \App\Billing\Entity\EntityId::priceRow($provider, $model, $validFrom);

        if ($this->store->provider($provider) === null) {
            throw new \RuntimeException(sprintf('Provider "%s" не найден в справочнике.', $provider));
        }
        $modelId = \App\Billing\Entity\EntityId::model($provider, $model);
        if ($this->store->model($modelId) === null) {
            throw new \RuntimeException(sprintf('Model "%s/%s" не найдена в справочнике.', $provider, $model));
        }

        $row = new \App\Billing\Entity\PriceRow($id);
        $row->providerCode = $provider;
        $row->modelCode = $model;
        $row->validFrom = $validFrom;
        $row->currency = (string) ($r['currency'] ?? 'USD');
        $row->unit = (string) $r['unit'];
        $rates = $r['rates'] ?? [];
        foreach ($rates as $k => $v) {
            $row->rates[(string) $k] = is_int($v) ? (string) $v : (string) $v;
        }
        $this->store->upsertPriceRow($row);

        return $id;
    }

    private static function minutes(string $hhmm): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $m)) {
            throw new \RuntimeException(sprintf('Invalid off-peak time "%s".', $hhmm));
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}
