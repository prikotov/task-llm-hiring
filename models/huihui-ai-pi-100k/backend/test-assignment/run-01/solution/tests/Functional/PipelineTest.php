<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Decimal;
use App\Domain\Rater;
use App\Ingest\PriceFileParser;
use App\Ingest\UsageLineParser;
use App\Service\CatalogService;
use App\Service\PriceImportService;
use App\Service\RatingService;
use App\Service\RepriceService;
use App\Service\ReportService;
use App\Service\UsageImportService;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный прогон полного цикла на реальной базе (BILLING_PG_*, по умолчанию 127.0.0.1:5544).
 * Состояние таблиц очищается до и после каждого теста.
 */
final class PipelineTest extends TestCase
{
    private \Doctrine\ORM\EntityManagerInterface $em;

    protected function setUp(): void
    {
        $connection = \Doctrine\DBAL\DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => getenv('BILLING_PG_HOST') ?: '127.0.0.1',
            'port' => getenv('BILLING_PG_PORT') ?: 5544,
            'user' => getenv('BILLING_PG_USER') ?: 'app',
            'password' => getenv('BILLING_PG_PASSWORD') ?: 'app',
            'dbname' => getenv('BILLING_PG_DB') ?: 'app',
        ]);
        $config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../../src/Entity'], true);
        $config->enableNativeLazyObjects(true); // PHP 8.4+ и symfony/var-exporter 8.x
        $config->setNamingStrategy(new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy()); // как в приложении (underscore_number_aware)
        $this->em = new \Doctrine\ORM\EntityManager($connection, $config);
        $this->em->getConnection()->executeStatement('TRUNCATE usage_event, price_row, model, provider CASCADE');
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->executeStatement('TRUNCATE usage_event, price_row, model, provider CASCADE');
    }

    public function testFullCycleMatchesTaskExampleAndIsIdempotent(): void
    {
        $catalog = new CatalogService();
        $priceImport = new PriceImportService($catalog);
        $usageImport = new UsageImportService(new UsageLineParser(), $catalog);
        $rating = new RatingService(new Rater());
        $report = new ReportService();

        $root = \dirname(__DIR__, 2);
        $pricesPath = $root . '/fixtures/prices.json';
        $usagePath = $root . '/fixtures/usage.jsonl';

        // 1) импорт цен, журнала; повторный импорт — без изменений данных
        $priceImport->import($this->em, (new PriceFileParser())->parse($pricesPath));

        $res = $usageImport->importFile($this->em, $usagePath);
        self::assertSame(3, $res->imported);
        self::assertSame(0, $res->duplicates);

        $res2 = $usageImport->importFile($this->em, $usagePath);
        self::assertSame(0, $res2->imported);
        self::assertSame(3, $res2->duplicates);

        // 2) тарификация: все три события рассчитаны
        $result1 = $rating->rateUnrated($this->em);
        self::assertSame(3, $result1->rated);
        self::assertSame(0, \count($result1->unrated));

        // повторный rate — новых нет
        $result2 = $rating->rateUnrated($this->em);
        self::assertSame(0, $result2->rated);

        // 3) отчёт = примеру из TASK.md байт-в-байт
        $expected = <<<'CSV'
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
CSV;
        $csv = $report->build(
            $this->em,
            new \DateTimeImmutable('2026-09-01T00:00:00Z', new \DateTimeZone('UTC')),
            new \DateTimeImmutable('2026-10-01T00:00:00Z', new \DateTimeZone('UTC')),
        );
        self::assertSame($expected . "\n", $csv);

        // 4) повторный импорт цен + rate + отчёт: тот же результат (двойного расчёта нет)
        $priceImport->import($this->em, (new PriceFileParser())->parse($pricesPath));
        $result3 = $rating->rateUnrated($this->em);
        self::assertSame(0, $result3->rated);
        self::assertSame($expected . "\n", $report->build(
            $this->em,
            new \DateTimeImmutable('2026-09-01T00:00:00Z', new \DateTimeZone('UTC')),
            new \DateTimeImmutable('2026-10-01T00:00:00Z', new \DateTimeZone('UTC')),
        ));

        // точный пример расчёта: e1
        self::assertSame('14.262499', $this->eventCost('prism', 'e1'));
    }

    public function testUnratedThenRatedAfterPriceAppears(): void
    {
        $dir = self::tmpDir();
        file_put_contents($dir . '/p1.json', json_encode([
            'providers' => [['code' => 'prism', 'name' => 'Prism AI', 'off_peak' => null]],
            'models' => [['provider' => 'prism', 'code' => 'lumen-8']],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                // цена действует только с 2026-02-01: на момент события (2026-01-10) применимой ставки нет => unrated
                'valid_from' => '2026-02-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '0.500000', 'output' => '0.500000', 'cache_read' => '0.050000', 'cache_write' => '0.050000'],
            ]],
        ], \JSON_UNESCAPED_UNICODE));
        file_put_contents($dir . '/u.jsonl', json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'no-price',
            'model' => 'lumen-8',
            'user' => 'u1',
            'ts' => '2026-01-10T10:00:00Z',
            'tokens_input' => 1000000,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]) . "\n");

        $catalog = new CatalogService();
        $priceImport = new PriceImportService($catalog);
        $usageImport = new UsageImportService(new UsageLineParser(), $catalog);
        $rating = new RatingService(new Rater());

        $priceImport->import($this->em, (new PriceFileParser())->parse($dir . '/p1.json'));
        $usageImport->importFile($this->em, $dir . '/u.jsonl');

        $r1 = $rating->rateUnrated($this->em);
        self::assertSame(0, $r1->rated);
        self::assertSame(1, \count($r1->unrated));

        // цена появляется -> повторный rate считает событие по ставке 0.5 за 1M
        file_put_contents($dir . '/p2.json', json_encode([
            'providers' => [['code' => 'prism', 'name' => 'Prism AI', 'off_peak' => null]],
            'models' => [['provider' => 'prism', 'code' => 'lumen-8']],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '0.500000', 'output' => '0.500000', 'cache_read' => '0.050000', 'cache_write' => '0.050000'],
            ]],
        ], \JSON_UNESCAPED_UNICODE));
        $priceImport->import($this->em, (new PriceFileParser())->parse($dir . '/p2.json'));
        $r2 = $rating->rateUnrated($this->em);
        self::assertSame(1, $r2->rated);
        self::assertSame('0.500000', $this->eventCost('prism', 'no-price'));

        // reprice: ставка удвоена -> стоимость 1.0, дельта +0.5
        file_put_contents($dir . '/p3.json', json_encode([
            'providers' => [['code' => 'prism', 'name' => 'Prism AI', 'off_peak' => null]],
            'models' => [['provider' => 'prism', 'code' => 'lumen-8']],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '1.000000', 'output' => '0.500000', 'cache_read' => '0.050000', 'cache_write' => '0.050000'],
            ]],
        ], \JSON_UNESCAPED_UNICODE));
        $reprice = new RepriceService($priceImport, new Rater());
        $rep = $reprice->reprice($this->em, (new PriceFileParser())->parse($dir . '/p3.json'));
        self::assertSame(1, $rep->repriced);
        self::assertSame('0.500000', $rep->delta);
        self::assertSame('1.000000', $this->eventCost('prism', 'no-price'));

        self::assertSame('1.000000', Decimal::canonicalMoney($this->eventCost('prism', 'no-price')));
    }

    public function testOffPeakWindowBoundaries(): void
    {
        $dir = self::tmpDir();
        file_put_contents($dir . '/p.json', json_encode([
            'providers' => [['code' => 'prism', 'name' => 'Prism AI', 'off_peak' => ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0.5']]],
            'models' => [['provider' => 'prism', 'code' => 'lumen-8']],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '1.000000', 'output' => '1.000000', 'cache_read' => '0.100000', 'cache_write' => '0.100000'],
            ]],
        ], \JSON_UNESCAPED_UNICODE));

        // ts: в окне / до окна / после окна (следующий день) / граница end / граница start
        $cases = [
            ['id' => 'in-window', 'ts' => '2026-09-16T17:00:00Z', 'expect' => '0.500000'],
            ['id' => 'before-window', 'ts' => '2026-09-16T10:00:00Z', 'expect' => '1.000000'],
            ['id' => 'end-exclusive', 'ts' => '2026-09-16T00:30:00Z', 'expect' => '1.000000'],
            ['id' => 'start-inclusive', 'ts' => '2026-09-16T16:30:00Z', 'expect' => '0.500000'],
        ];
        $lines = '';
        foreach ($cases as $c) {
            $lines .= json_encode([
                'provider' => 'prism',
                'provider_request_id' => $c['id'],
                'model' => 'lumen-8',
                'user' => 'u1',
                'ts' => $c['ts'],
                'tokens_input' => 1000000,
                'tokens_output' => 0,
                'tokens_cache_read' => 0,
                'tokens_cache_write' => 0,
            ]) . "\n";
        }
        file_put_contents($dir . '/u.jsonl', $lines);

        $catalog = new CatalogService();
        $priceImport = new PriceImportService($catalog);
        $usageImport = new UsageImportService(new UsageLineParser(), $catalog);
        $rating = new RatingService(new Rater());

        $priceImport->import($this->em, (new PriceFileParser())->parse($dir . '/p.json'));
        $usageImport->importFile($this->em, $dir . '/u.jsonl');
        $res = $rating->rateUnrated($this->em);
        self::assertSame(4, $res->rated);
        self::assertSame(0, \count($res->unrated));

        foreach ($cases as $c) {
            self::assertSame($c['expect'], $this->eventCost('prism', $c['id']), $c['id']);
        }
    }

    private function eventCost(string $providerCode, string $requestId): ?string
    {
        $cost = $this->em->createQuery(
            'SELECT e.cost FROM ' . \App\Entity\UsageEvent::class . ' e'
            .' WHERE e.providerRequestId = :r'
        )->setParameter('r', $requestId)->getOneOrNullResult();

        if (is_array($cost)) {
            return (string) array_values($cost)[0];
        }
        self::assertNotNull($cost, sprintf('event %s/%s не найдено', $providerCode, $requestId));

        return $cost;
    }

    private static function tmpDir(): string
    {
        $dir = \sys_get_temp_dir() . '/billing_test_' . \uniqid();
        \mkdir($dir, 0775, true);

        return $dir;
    }
}
