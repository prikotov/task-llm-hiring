<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Exception\BillingException;
use App\Application\Service\PriceBookService;
use App\Application\Service\RatingService;
use App\Application\Service\RepriceService;
use App\Application\Service\ReportService;
use App\Application\Service\UsageImportService;
use App\Domain\Money;
use App\Domain\RateCalculator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Runtime\SymfonyRuntime;

/**
 * Интеграционные проверки (без Kernel):
 * - импорт цен + событий (fixtures);
 * - тарификация: 3 события → RATED=3;
 * - отчёт за сентябрь совпадает с эталонным;
 * - повторный импорт + rate не меняет результатов (идемпотентность);
 * - репорт с --from/--to работает;
 * - reprice — пересчёт и дельта;
 * - unrated-события (нет цены) → UNRATED=1, отчёт их не содержит.
 *
 * Использует реальную БД — для запуска поднять `make db-up`.
 */
final class IntegrationTest extends TestCase
{
    private static \Doctrine\ORM\EntityManagerInterface $em;

    protected function setUp(): void
    {
        // БД должна быть поднята; иначе пропускаем.
        // Загружаем .env, если он ещё не загружен.
        if (!isset($_SERVER['DATABASE_URL'])) {
            (new \Symfony\Component\Dotenv\Dotenv())->load(__DIR__ . '/../../.env');
        }
        $kernel = new \App\Kernel('dev', true);
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        try {
            $em->getConnection()->executeQuery('SELECT 1');
        } catch (\Throwable $e) {
            self::markTestSkipped(sprintf('PostgreSQL недоступен (запустить `make db-up`): %s', $e->getMessage()));
        }
        self::$em = $em;
        self::dropAndCreateSchema();
    }

    public function testFullAcceptanceFlow(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);
        $usageImport = new UsageImportService($em);
        $rating = new RatingService($em, $priceBook, new RateCalculator());
        $report = new ReportService($em);

        $pricesFile = __DIR__ . '/../../fixtures/prices.json';
        $usageFile = __DIR__ . '/../../fixtures/usage.jsonl';
        $data = json_decode(file_get_contents($pricesFile) ?: '', true);
        self::assertIsArray($data);

        $priceBook->import($data);
        $usageImport->import($usageFile);
        $stats = $rating->rate();

        self::assertSame(3, $stats['rated']);
        self::assertSame(0, $stats['unrated']);

        $expected = implode("\n", [
            'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd',
            'u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499',
            'u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000',
            'u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000',
            'TOTAL,,6200000,2300000,2500000,83333,33.662499',
        ]);
        $csv = self::renderReport($report, '2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');
        self::assertSame($expected, $csv);

        // Повторный импорт + rate → без изменений.
        $priceBook->import($data);
        $usageImport->import($usageFile);
        $stats2 = $rating->rate();
        self::assertSame(0, $stats2['rated']);
        $csv2 = self::renderReport($report, '2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');
        self::assertSame($expected, $csv2);
    }

    public function testPeriodFiltering(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);
        $usageImport = new UsageImportService($em);
        $rating = new RatingService($em, $priceBook, new RateCalculator());
        $report = new ReportService($em);

        $data = json_decode(file_get_contents(__DIR__ . '/../../fixtures/prices.json') ?: '', true);
        $priceBook->import($data);
        $usageImport->import(__DIR__ . '/../../fixtures/usage.jsonl');
        $rating->rate();

        $csv = self::renderReport($report, '2026-09-15T00:00:00Z', '2026-09-16T00:00:00Z');
        $expected = implode("\n", [
            'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd',
            'u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499',
            'u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000',
            'TOTAL,,3200000,1800000,2500000,83333,25.262499',
        ]);
        self::assertSame($expected, $csv);
    }

    public function testUnratedEvents(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);
        $usageImport = new UsageImportService($em);
        $rating = new RatingService($em, $priceBook, new RateCalculator());

        // Цена действует с 2026-09-01, а событие раньше → unrated.
        $prices = [
            'providers' => [
                ['code' => 'prism', 'name' => 'P', 'off_peak' => null],
            ],
            'models' => [
                ['provider' => 'prism', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'prism', 'model' => 'm1',
                    'valid_from' => '2026-09-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => '1.0'],
                ],
            ],
        ];
        $priceBook->import($prices);

        $tmp = tempnam(sys_get_temp_dir(), 'usage_');
        file_put_contents($tmp, json_encode([
            'provider' => 'prism', 'provider_request_id' => 'r1', 'model' => 'm1',
            'user' => 'u', 'ts' => '2026-08-01T00:00:00Z',
            'tokens_input' => 1000000, 'tokens_output' => 0,
            'tokens_cache_read' => 0, 'tokens_cache_write' => 0,
        ]), LOCK_EX);
        $usageImport->import($tmp);
        @unlink($tmp);

        $stats = $rating->rate();
        self::assertSame(0, $stats['rated']);
        self::assertSame(1, $stats['unrated']);
    }

    public function testReprice(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);
        $usageImport = new UsageImportService($em);
        $rating = new RatingService($em, $priceBook, new RateCalculator());
        $reprice = new RepriceService($em, $priceBook, new RateCalculator());

        // Цена 1.0 / 1M на входе
        $prices = [
            'providers' => [
                ['code' => 'prism', 'name' => 'P', 'off_peak' => null],
            ],
            'models' => [
                ['provider' => 'prism', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'prism', 'model' => 'm1',
                    'valid_from' => '2026-09-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => '1.0'],
                ],
            ],
        ];
        $priceBook->import($prices);

        $tmp = tempnam(sys_get_temp_dir(), 'usage_');
        file_put_contents($tmp, json_encode([
            'provider' => 'prism', 'provider_request_id' => 'r1', 'model' => 'm1',
            'user' => 'u', 'ts' => '2026-09-15T00:00:00Z',
            'tokens_input' => 1000000, 'tokens_output' => 0,
            'tokens_cache_read' => 0, 'tokens_cache_write' => 0,
        ]), LOCK_EX);
        $usageImport->import($tmp);
        @unlink($tmp);

        $stats = $rating->rate();
        self::assertSame(1, $stats['rated']); // 1.0 М × 1.0 = 1.0

        // Reprice: цена 0.5 → новая стоимость 0.5 (дельта -0.5)
        $repriceData = [
            'prices' => [
                [
                    'provider' => 'prism', 'model' => 'm1',
                    'valid_from' => '2026-09-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => '0.5'],
                ],
            ],
        ];
        $stats = $reprice->reprice($repriceData);
        self::assertSame(1, $stats['n']);
        self::assertSame('-0.500000', $stats['deltaUsd']->format(6));

        // Проверим отчёт.
        $report = new ReportService($em);
        $csv = self::renderReport($report, '2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');
        $expected = implode("\n", [
            'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd',
            'u,prism/m1,1000000,0,0,0,0.500000',
            'TOTAL,,1000000,0,0,0,0.500000',
        ]);
        self::assertSame($expected, $csv);
    }

    public function testPriceUpsert(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);

        // Первый импорт: 1.0
        $data = [
            'providers' => [[ 'code' => 'p1', 'name' => 'X', 'off_peak' => null ]],
            'models' => [[ 'provider' => 'p1', 'code' => 'm1' ]],
            'prices' => [[
                'provider' => 'p1', 'model' => 'm1',
                'valid_from' => '2026-09-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens_unified',
                'rates' => ['any' => '1.0'],
            ]],
        ];
        $priceBook->import($data);
        $price = $em->getRepository(\App\Entity\Price::class)->findOneBy([
            'model' => $em->getRepository(\App\Entity\Model::class)->findOneBy(['code' => 'm1']),
        ]);
        self::assertNotNull($price);
        self::assertSame('1.000000', $price->rates()['any']);

        // Повторный импорт — с другой ценой (idempotent upsert)
        $data2 = $data;
        $data2['prices'][0]['rates']['any'] = '2.0';
        $priceBook->import($data2);
        // Price обновлён, count не изменился.
        $count = $em->getRepository(\App\Entity\Price::class)->createQueryBuilder('p')->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        self::assertSame(1, (int) $count);

        $price2 = $em->getRepository(\App\Entity\Price::class)->findOneBy([
            'model' => $em->getRepository(\App\Entity\Model::class)->findOneBy(['code' => 'm1']),
        ]);
        self::assertNotNull($price2);
        self::assertSame('2.000000', $price2->rates()['any']);
    }

    public function testInvalidPriceImportFails(): void
    {
        $em = self::$em;
        $priceBook = new PriceBookService($em);
        $this->expectException(BillingException::class);
        $priceBook->import([
            'providers' => [['code' => 'p1', 'name' => 'X', 'off_peak' => null]],
            'models' => [['provider' => 'p1', 'code' => 'm1']],
            'prices' => [['provider' => 'p1', 'model' => 'm1', 'valid_from' => 'bad', 'currency' => 'USD', 'unit' => 'per_1m_tokens_unified', 'rates' => ['any' => '1.0']]],
        ]);
    }

    private static function renderReport(ReportService $report, string $from, string $to): string
    {
        $r = $report->report(
            new \DateTimeImmutable($from),
            new \DateTimeImmutable($to),
        );
        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($r['rows'] as $row) {
            $lines[] = sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['ti'],
                $row['to_'],
                $row['cr'],
                $row['cw'],
                $row['cost']->format(6)
            );
        }
        $lines[] = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $r['total']['ti'],
            $r['total']['to_'],
            $r['total']['cr'],
            $r['total']['cw'],
            $r['total']['cost']->format(6)
        );
        return implode("\n", $lines);
    }

    private static function dropAndCreateSchema(): void
    {
        $em = self::$em;
        $schema = new \Doctrine\ORM\Tools\SchemaTool($em);
        $metadatas = [];
        foreach ([
            \App\Entity\UsageRating::class,
            \App\Entity\UsageEvent::class,
            \App\Entity\Price::class,
            \App\Entity\Model::class,
            \App\Entity\Provider::class,
        ] as $class) {
            $metadatas[] = $em->getClassMetadata($class);
        }
        $schema->dropSchema($metadatas);
        $schema->createSchema($metadatas);
    }
}
