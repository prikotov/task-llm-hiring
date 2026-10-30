<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\Import\PricesFileParser;
use App\Billing\Import\UsageFileParser;
use App\Billing\Service\PriceListImporter;
use App\Billing\Service\RepriceService;
use App\Billing\Service\RateEngine;
use App\Billing\Service\ReportService;
use App\Billing\Service\UsageImporter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Интеграционный прогон полного цикла на отдельной test-БД (billing_test):
 * импорт цен → импорт событий (дедуп) → тарификация → отчёт → ретро-коррекция.
 *
 * Требование: поднят PostgreSQL из docker-compose (make db-up).
 */
final class BillingFlowTest extends KernelTestCase
{
    private static EntityManagerInterface $em;
    private static PricesFileParser $priceParser;
    private static UsageFileParser $usageParser;
    private static PriceListImporter $pricesImporter;
    private static UsageImporter $usageImporter;
    private static RateEngine $rateEngine;
    private static ReportService $reportService;
    private static RepriceService $repriceService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        if (!isset(self::$priceParser)) {
            self::$priceParser = $container->get(PricesFileParser::class);
            self::$usageParser = $container->get(UsageFileParser::class);
            self::$pricesImporter = $container->get(PriceListImporter::class);
            self::$usageImporter = $container->get(UsageImporter::class);
            self::$rateEngine = $container->get(RateEngine::class);
            self::$reportService = $container->get(ReportService::class);
            self::$repriceService = $container->get(RepriceService::class);
            self::$em = $container->get(EntityManagerInterface::class);

            self::createSchema();
        }

        self::$em->clear();
    }

    private static function createSchema(): void
    {
        // Явный сброс в порядке, безопасном для FK (SchemaTool::dropSchema глотает ошибки дропа,
        // а при нарушенном порядке зависимостей часть таблиц остаётся) + создание по метаданным.
        $conn = self::$em->getConnection();
        foreach (['usage_events', 'price_rows', 'models', 'providers'] as $table) {
            $conn->executeStatement('DROP TABLE IF EXISTS "'.$table.'" CASCADE');
        }

        $metadata = self::$em->getMetadataFactory()->getAllMetadata();
        (new SchemaTool(self::$em))->createSchema($metadata);
    }

    private static function rootPath(string $name): string
    {
        return dirname(__DIR__, 2).'/'.$name;
    }

    public function testCanonicalAcceptanceFlow(): void
    {
        // 1. Импорт справочника цен.
        $pricesFile = self::$priceParser->parse(file_get_contents(self::rootPath('fixtures/prices.json')));
        self::$pricesImporter->import($pricesFile);

        // 2. Импорт событий + идемпотентный повтор.
        $usage = self::$usageParser->parse(file_get_contents(self::rootPath('fixtures/usage.jsonl')));
        $first = self::$usageImporter->import($usage);
        self::assertSame(3, $first->accepted);
        self::assertSame(0, $first->duplicatesIgnored);

        $second = self::$usageImporter->import($usage);
        self::assertSame(0, $second->accepted);
        self::assertSame(3, $second->duplicatesIgnored);

        // 3. Повторный импорт цен — данные не меняются.
        self::$pricesImporter->import(self::$priceParser->parse(file_get_contents(self::rootPath('fixtures/prices.json'))));

        // 4. Тарификация: 3 события, все рассчитаны.
        $run = self::$rateEngine->ratePending();
        self::assertSame(3, $run->rated);
        self::assertCount(0, $run->unrated);

        // Повторный запуск — новых событий нет.
        $again = self::$rateEngine->ratePending();
        self::assertSame(0, $again->rated);

        // 5. Отчёт за сентябрь — в точности эталонный.
        $expected = <<<'CSV'
        user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
        u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
        u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
        u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
        TOTAL,,6200000,2300000,2500000,83333,33.662499
        CSV;
        $from = new \DateTimeImmutable('2026-09-01T00:00:00Z', new \DateTimeZone('UTC'));
        $to = new \DateTimeImmutable('2026-10-01T00:00:00Z', new \DateTimeZone('UTC'));
        self::assertSame($expected."
", self::$reportService->build($from, $to));

        // 6. Ретро-коррекция: output-rate строки 2026-08-01: 12.0 → 24.0.
        $correction = self::$priceParser->parse(<<<'JSON'
        {
          "prices": [
            {
              "provider": "prism",
              "model": "lumen-8",
              "valid_from": "2026-08-01T00:00:00Z",
              "currency": "USD",
              "unit": "per_1m_tokens",
              "rates": {
                "input": "3.000000",
                "output": "24.000000",
                "cache_read": "0.300000",
                "cache_write": "3.750000"
              }
            }
          ]
        }
        JSON);
        $reprice = self::$repriceService->reprice($correction);
        self::assertSame(1, $reprice->repricedEvents);
        self::assertSame('9.600000', $reprice->deltaUsd);

        // Отчёт обновлён, задвоений нет: e1 = 14.262499 + 9.6 = 23.862499.
        $expectedAfter = <<<'CSV'
        user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
        u-alice,prism/lumen-8,1200000,800000,2500000,83333,23.862499
        u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
        u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
        TOTAL,,6200000,2300000,2500000,83333,43.262499
        CSV;
        self::assertSame($expectedAfter."
", self::$reportService->build($from, $to));
    }

    public function testUnratedRetriedOnNextRun(): void
    {
        // Событие до появления первой строки цен → unrated.
        $event = self::$usageParser->parse(
            '{"provider": "prism", "provider_request_id": "x-early", "model": "lumen-mini", '
            . '"user": "u-x", "ts": "2026-07-01T09:00:00Z", "tokens_input": 1000000, "tokens_output": 0, '
            . '"tokens_cache_read": 0, "tokens_cache_write": 0}',
        )[0];
        self::assertSame(1, self::$usageImporter->import([$event])->accepted);

        $first = self::$rateEngine->ratePending();
        $unratedIds = array_map(static fn (\App\Billing\Entity\UsageEvent $e) => $e->providerRequestId, $first->unrated);
        self::assertContains('x-early', $unratedIds, 'x-early должно остаться unrated, пока нет строки цен');

        // Подтянулась строка цен, действующая с 2026-06-01 → повторная попытка успешна.
        $latePrice = self::$priceParser->parse(<<<'JSON'
        {
          "prices": [
            {
              "provider": "prism",
              "model": "lumen-mini",
              "valid_from": "2026-06-01T00:00:00Z",
              "currency": "USD",
              "unit": "per_1m_tokens",
              "rates": { "input": "0.180000", "output": "0.720000", "cache_read": "0.018000", "cache_write": "0.225000" }
            }
          ]
        }
        JSON);
        self::$pricesImporter->import($latePrice);

        $second = self::$rateEngine->ratePending();
        $unratedIds = array_map(static fn (\App\Billing\Entity\UsageEvent $e) => $e->providerRequestId, $second->unrated);
        self::assertNotContains('x-early', $unratedIds, 'повторное rate добирает unrated-событие');

        // 1M × 0.18 / 1M = 0.18 → 0.180000
        $events = self::$em->getRepository(\App\Billing\Entity\UsageEvent::class)
            ->findBy(['providerRequestId' => 'x-early']);
        self::assertCount(1, $events);
        self::assertSame('0.180000', $events[0]->cost);
    }

    public function testOffPeakWindowInRealFlow(): void
    {
        // Изолированный провайдер со своим окном 16:30–00:30 (×0.5) и моделью.
        self::$pricesImporter->import(self::$priceParser->parse(<<<'JSON'
        {
          "providers": [
            { "code": "op-test", "name": "OffPeak Test",
              "off_peak": { "window_start": "16:30", "window_end": "00:30", "multiplier": "0.5" } }
          ],
          "models": [ { "provider": "op-test", "code": "m1" } ],
          "prices": [
            { "provider": "op-test", "model": "m1", "valid_from": "2026-01-01T00:00:00Z",
              "currency": "USD", "unit": "per_1m_tokens",
              "rates": { "input": "4.000000", "output": "4.000000", "cache_read": "0.400000", "cache_write": "4.000000" } }
          ]
        }
        JSON));

        $batch = [
            self::$usageParser->parse(self::event('op-in', '17:00:00'))[0],
            self::$usageParser->parse(self::event('op-edge-end', '00:30:00'))[0],
            self::$usageParser->parse(self::event('op-edge-start', '16:30:00'))[0],
            self::$usageParser->parse(self::event('op-peak', '12:00:00'))[0],
        ];
        self::assertSame(4, self::$usageImporter->import($batch)->accepted);

        // Базовая стоимость 1M input = 4.0; в окне ×0.5 = 2.0.
        self::assertSame(4, self::$rateEngine->ratePending()->rated);

        $find = static fn (string $id): ?\App\Billing\Entity\UsageEvent
            => self::$em->getRepository(\App\Billing\Entity\UsageEvent::class)
                ->find(['provider' => 'op-test', 'providerRequestId' => $id]);

        self::assertSame('2.000000', $find('op-in')->cost, '17:00 — в окне: 4.0 × 0.5');
        self::assertSame('4.000000', $find('op-edge-end')->cost, '00:30 — конец окна эксклюзивен: полная цена');
        self::assertSame('2.000000', $find('op-edge-start')->cost, '16:30 — начало окна включительно: 4.0 × 0.5');
        self::assertSame('4.000000', $find('op-peak')->cost, '12:00 — пик: без скидки');
    }

    /** Лёгкий генератор события op-test/m1 с ts 05.07.2026 в указанное время дня UTC. */
    private static function event(string $id, string $timeOfDay): string
    {
        return sprintf(
            '{"provider": "op-test", "provider_request_id": "%s", "model": "m1", '
            . '"user": "u-w", "ts": "2026-07-05T%sZ", "tokens_input": 1000000, "tokens_output": 0, '
            . '"tokens_cache_read": 0, "tokens_cache_write": 0}',
            $id,
            $timeOfDay,
        );
    }
}
