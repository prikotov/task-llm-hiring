<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Приёмочный сквозной тест (ТЗ): на чистой БД app_test разворачивается схема
 * миграцией, файлы fixtures/ проходят полный путь:
 * импорт цен → импорт событий → rate → report, плюс правила из ТЗ:
 * идемпотентность, повторные попытки unrated, reprice, период отчёта.
 * Тесты выполняются в объявленном порядке (нумерация в именах).
 */
final class EndToEndTest extends TestCase
{
    private const TEST_DSN = 'postgresql://app:app@127.0.0.1:5544/app_test?serverVersion=17&charset=utf8';
    private const PG = '127.0.0.1:5544';

    private static function adminPdo(): \PDO
    {
        return new \PDO('pgsql:host=127.0.0.1;port=5544;dbname=postgres', 'app', 'app', [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function setUpBeforeClass(): void
    {
        $pdo = self::adminPdo();
        $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = 'app_test' AND pid <> pg_backend_pid()");
        $pdo->exec('DROP DATABASE IF EXISTS app_test');
        $pdo->exec('CREATE DATABASE app_test');

        self::bin(['doctrine:database:create', '--if-not-exists']);
        self::bin(['doctrine:migrations:migrate', '-n']);
    }

    public static function tearDownAfterClass(): void
    {
        try {
            $pdo = self::adminPdo();
            $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = 'app_test' AND pid <> pg_backend_pid()");
            $pdo->exec('DROP DATABASE IF EXISTS app_test');
        } catch (\PDOException) {
            // БД уже удалена или недоступна — не страшно
        }
    }

    /**
     * Запускает bin/console в отдельном процессе с DATABASE_URL на app_test.
     *
     * @param list<string> $args
     * @return array{rc:int, out:string, err:string}
     */
    private static function bin(array $args): array
    {
        $root = dirname(__DIR__);
        $cmd = 'cd '.escapeshellarg($root).' && DATABASE_URL='.escapeshellarg(self::TEST_DSN)
            .' php bin/console '.implode(' ', array_map('escapeshellarg', $args))
            .' > /tmp/e2e_stdout.txt 2> /tmp/e2e_stderr.txt';
        exec($cmd, $output, $rc);
        $out = (string) file_get_contents('/tmp/e2e_stdout.txt');
        $err = (string) file_get_contents('/tmp/e2e_stderr.txt');
        @unlink('/tmp/e2e_stdout.txt');
        @unlink('/tmp/e2e_stderr.txt');

        return ['rc' => $rc, 'out' => $out, 'err' => $err];
    }

    private static function query(string $sql): array
    {
        $pdo = new \PDO('pgsql:host=127.0.0.1;port=5544;dbname=app_test', 'app', 'app', [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

        return $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function test_10_importsSucceed(): void
    {
        $prices = self::bin(['app:prices:import', dirname(__DIR__).'/fixtures/prices.json']);
        self::assertSame(0, $prices['rc'], 'импорт цен: '.$prices['err']);

        $usage = self::bin(['app:usage:import', dirname(__DIR__).'/fixtures/usage.jsonl']);
        self::assertSame(0, $usage['rc'], 'импорт событий: '.$usage['err']);

        $rows = self::query('SELECT count(*) c FROM usage_events');
        self::assertSame(3, (int) $rows[0]['c']);
        $rows = self::query("SELECT count(*) c FROM usage_events WHERE status = 'pending'");
        self::assertSame(3, (int) $rows[0]['c']);
    }

    public function test_11_usageImportIsIdempotent(): void
    {
        $usage = self::bin(['app:usage:import', dirname(__DIR__).'/fixtures/usage.jsonl']);
        self::assertSame(0, $usage['rc'], $usage['err']);

        $rows = self::query('SELECT count(*) c FROM usage_events');
        self::assertSame(3, (int) $rows[0]['c'], 'повторная доставка не добавляет строк');
    }

    public function test_12_pricesImportIsIdempotent(): void
    {
        $before = (int) self::query('SELECT count(*) c FROM price_rows')[0]['c'];
        $prices = self::bin(['app:prices:import', dirname(__DIR__).'/fixtures/prices.json']);
        self::assertSame(0, $prices['rc'], $prices['err']);
        $after = (int) self::query('SELECT count(*) c FROM price_rows')[0]['c'];
        self::assertSame($before, $after, 'повторный импорт цен не дублирует строки');
    }

    public function test_20_rateRatesAllThreeEvents(): void
    {
        $rate = self::bin(['app:billing:rate']);
        self::assertSame(0, $rate['rc'], $rate['err']);
        self::assertStringContainsString('RATED=3 UNRATED=0', $rate['out']);
    }

    public function test_21_rateAgainChangesNothing(): void
    {
        $before = self::query('SELECT provider, provider_request_id, cost_usd FROM usage_events ORDER BY provider_request_id');
        $rate = self::bin(['app:billing:rate']);
        self::assertSame(0, $rate['rc'], $rate['err']);
        self::assertStringContainsString('RATED=0 UNRATED=0', $rate['out']);
        $after = self::query('SELECT provider, provider_request_id, cost_usd FROM usage_events ORDER BY provider_request_id');
        self::assertSame($before, $after, 'уже рассчитанное событие не пересчитывается');
    }

    public function test_30_reportMatchesTaskExampleExactly(): void
    {
        $report = self::bin(['app:billing:report', '--from=2026-09-01T00:00:00Z', '--to=2026-10-01T00:00:00Z']);
        self::assertSame(0, $report['rc'], $report['err']);

        $expected = implode("\n", [
            'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd',
            'u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499',
            'u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000',
            'u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000',
            'TOTAL,,6200000,2300000,2500000,83333,33.662499',
        ]) . "\n";

        self::assertSame($expected, $report['out']);
    }

    public function test_31_reportWithoutOptionsCoversAll(): void
    {
        $report = self::bin(['app:billing:report']);
        self::assertSame(0, $report['rc'], $report['err']);
        self::assertStringContainsString('TOTAL,,6200000,2300000,2500000,83333,33.662499', $report['out']);
    }

    public function test_40_unratedEventsAreSkippedThenRetried(): void
    {
        $late = sys_get_temp_dir().'/e2e-event-before.jsonl';
        file_put_contents($late, json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'e0-old',
            'model' => 'lumen-8',
            'user' => 'u-carol',
            'ts' => '2026-07-10T09:00:00Z',
            'tokens_input' => 1000,
            'tokens_output' => 1000,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ])."
        ");
        $withOld = sys_get_temp_dir().'/e2e-prices-with-old.json';
        $data = json_decode((string) file_get_contents(dirname(__DIR__).'/fixtures/prices.json'), true);
        $data['prices'][] = [
            'provider' => 'prism',
            'model' => 'lumen-8',
            'valid_from' => '2026-07-01T00:00:00Z',
            'currency' => 'USD',
            'unit' => 'per_1m_tokens',
            'rates' => ['input' => '2.0', 'output' => '10.0', 'cache_read' => '0.2', 'cache_write' => '2.5'],
        ];
        file_put_contents($withOld, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        try {
            $import = self::bin(['app:usage:import', $late]);
            self::assertSame(0, $import['rc'], $import['err']);

            $rate = self::bin(['app:billing:rate']);
            self::assertSame(0, $rate['rc'], $rate['err']);
            self::assertStringContainsString('RATED=0 UNRATED=1', $rate['out']);
            self::assertStringContainsString('UNRATED prism/e0-old 2026-07-10T09:00:00Z no_price', $rate['err']);

            $report = self::bin(['app:billing:report']);
            self::assertStringNotContainsString('u-carol', $report['out'], 'unrated-событие не попадает в отчёт');

            // Появляется старая строка цены — повторный rate добирает событие.
            $prices = self::bin(['app:prices:import', $withOld]);
            self::assertSame(0, $prices['rc'], $prices['err']);

            $rate2 = self::bin(['app:billing:rate']);
            self::assertSame(0, $rate2['rc'], $rate2['err']);
            self::assertStringContainsString('RATED=1 UNRATED=0', $rate2['out']);

            $cost = self::query("SELECT cost_usd FROM usage_events WHERE provider_request_id = 'e0-old'");
            self::assertSame('0.012000', $cost[0]['cost_usd']);
        } finally {
            @unlink($late);
            @unlink($withOld);
        }
    }

    public function test_50_repriceRetrocorrectsEvents(): void
    {
        $fixed = sys_get_temp_dir().'/e2e-prices-fixed.json';
        $data = json_decode((string) file_get_contents(dirname(__DIR__).'/fixtures/prices.json'), true);
        foreach ($data['prices'] as &$price) {
            if ($price['model'] === 'lumen-8' && $price['valid_from'] === '2026-08-01T00:00:00Z') {
                $price['rates']['output'] = '13.000000';
                $price['rates']['cache_read'] = '0.350000';
                $price['rates']['cache_write'] = '3.800000';
            }
        }
        unset($price);
        file_put_contents($fixed, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        try {
            $reprice = self::bin(['app:billing:reprice', $fixed]);
            self::assertSame(0, $reprice['rc'], $reprice['err']);
            self::assertStringContainsString('REPRICED_EVENTS=1', $reprice['out']);
            self::assertStringContainsString('REPRICE_DELTA_USD=0.929166', $reprice['out']);

            $e1 = self::query("SELECT cost_usd FROM usage_events WHERE provider_request_id = 'e1'");
            self::assertSame('15.191665', $e1[0]['cost_usd']);

            $report = self::bin(['app:billing:report', '--from=2026-09-01T00:00:00Z', '--to=2026-10-01T00:00:00Z']);
            self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,15.191665', $report['out']);

            // Повторный reprice тех же данных — без дельты.
            $reprice2 = self::bin(['app:billing:reprice', $fixed]);
            self::assertSame(0, $reprice2['rc'], $reprice2['err']);
            self::assertStringContainsString('REPRICED_EVENTS=0', $reprice2['out']);
            self::assertStringContainsString('REPRICE_DELTA_USD=0.000000', $reprice2['out']);
        } finally {
            @unlink($fixed);
        }
    }
}
