<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Сквозной сценарий на реальной PostgreSQL: импорт → тарификация → отчёт →
 * идемпотентность → ретро-коррекция. Методы исполняются в порядке объявления.
 *
 * БД должна быть запущена (`make db-up`); иначе тест пропускается.
 */
final class BillingWorkflowTest extends TestCase
{
    private static ?EntityManagerInterface $em = null;
    private static bool $available = false;

    private const EXPECTED_REPORT = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
        ."u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
        ."u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
        ."u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
        ."TOTAL,,6200000,2300000,2500000,83333,33.662499\n";

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        try {
            $kernel = new Kernel('test', false);
            $kernel->boot();
            self::$em = $kernel->getContainer()->get('app.entity_manager');

            // соединение?
            self::$em->getConnection()->executeQuery('SELECT 1');

            // разворачиваем схему (идемпотентно)
            $app = new Application($kernel);
            $app->setAutoExit(false);
            self::assertSame(0, $app->run(new ArrayInput(['command' => 'doctrine:database:create', '--if-not-exists' => true]), new BufferedOutput()));
            self::assertSame(0, $app->run(new ArrayInput(['command' => 'doctrine:migrations:migrate', '-n' => true]), new BufferedOutput()));
        } catch (\Throwable) {
            self::$available = false;

            return;
        }

        self::$available = true;
        self::resetTables();
    }

    private static function resetTables(): void
    {
        self::$em->getConnection()->executeStatement('TRUNCATE reprice_entries, usage_events, price_rows, catalog_models, providers RESTART IDENTITY CASCADE');
    }

    private static function exec(string $name, array $params = []): array
    {
        self::assertTrue(self::$available, 'тест должен быть пропущен, если БД недоступна');

        $argv = array_merge(['php', 'bin/console', '--env=test', $name]);
        foreach ($params as $k => $v) {
            if (is_string($k) && str_starts_with($k, '--')) {
                $argv[] = $k.'='.$v;
            } else {
                $argv[] = (string) $v;
            }
        }

        $env = array_merge($_ENV, [
            'APP_ENV' => 'test',
            'APP_DEBUG' => '0',
            'PATH' => $_SERVER['PATH'] ?? getcwd().'/vendor/bin:/usr/bin:/bin',
        ]);
        $proc = proc_open($argv, [0 => ['file', '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, __DIR__.'/../..', $env);
        if (!is_resource($proc)) {
            self::fail('не удалось запустить bin/console');
        }
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($proc);

        return ['exit' => $exit, 'stdout' => (string) $stdout, 'stderr' => (string) $stderr];
    }

    private function report(): string
    {
        $r = self::exec('app:billing:report', [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        self::assertSame(0, $r['exit'], 'report должен завершиться с кодом 0: '.$r['stdout']);

        return $r['stdout'];
    }

    public function test1ImportRateAndReport(): void
    {
        if (!self::$available) {
            self::markTestSkipped('PostgreSQL не запущен (make db-up)');
        }

        $repo = dirname(__DIR__, 2);

        $r = self::exec('app:prices:import', ['file' => $repo.'/fixtures/prices.json']);
        self::assertSame(0, $r['exit']);

        $r = self::exec('app:usage:import', ['file' => $repo.'/fixtures/usage.jsonl']);
        self::assertSame(0, $r['exit']);

        $r = self::exec('app:billing:rate');
        self::assertSame(0, $r['exit']);
        self::assertStringContainsString('RATED=3 UNRATED=0', $r['stdout']);

        self::assertSame(self::EXPECTED_REPORT, $this->report());
    }

    public function test2ReimportIsIdempotent(): void
    {
        if (!self::$available) {
            self::markTestSkipped('PostgreSQL не запущен (make db-up)');
        }
        $repo = dirname(__DIR__, 2);

        self::assertSame(0, self::exec('app:prices:import', ['file' => $repo.'/fixtures/prices.json'])['exit']);
        self::assertSame(0, self::exec('app:usage:import', ['file' => $repo.'/fixtures/usage.jsonl'])['exit']);

        $r = self::exec('app:billing:rate');
        self::assertStringContainsString('RATED=0 UNRATED=0', $r['stdout']);

        self::assertSame(self::EXPECTED_REPORT, $this->report(), 'повторный импорт не должен менять отчёт');

        // событий не задвоилось
        self::assertSame(
            3,
            (int) self::$em->getConnection()->fetchOne('SELECT count(*) FROM usage_events'),
        );
    }

    public function test3UsageDedupKeepsSingleEvent(): void
    {
        if (!self::$available) {
            self::markTestSkipped('PostgreSQL не запущен (make db-up)');
        }
        self::assertSame(
            1,
            (int) self::$em->getConnection()->fetchOne('SELECT count(*) FROM usage_events WHERE provider=\'prism\' AND provider_request_id=\'e1\''),
            'дубликат (prism, e1) не должен задваиваться',
        );
    }

    public function test4RepriceRetallyAffectedEventsOnly(): void
    {
        if (!self::$available) {
            self::markTestSkipped('PostgreSQL не запущен (make db-up)');
        }
        $repo = dirname(__DIR__, 2);

        $r = self::exec('app:billing:reprice', ['file' => $repo.'/fixtures/prices-corrected.json']);
        self::assertSame(0, $r['exit']);
        self::assertStringContainsString('REPRICED_EVENTS=1', $r['stdout']);
        self::assertStringContainsString('REPRICE_DELTA_USD=-2.200000', $r['stdout']);

        $lines = explode("\n", $this->report());
        self::assertSame('u-alice,prism/lumen-8,1200000,800000,2500000,83333,12.062499', $lines[1]);
        self::assertSame('u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000', $lines[3], 'e2 (строка от 09-15T12:00) не затрагивается');
        self::assertSame('TOTAL,,6200000,2300000,2500000,83333,31.462499', $lines[4]);

        // журнал коррекции: ровно одна запись
        self::assertSame(
            1,
            (int) self::$em->getConnection()->fetchOne('SELECT count(*) FROM reprice_entries'),
        );
    }

    public function test5UnratedStaysUnratedWithStderrLine(): void
    {
        if (!self::$available) {
            self::markTestSkipped('PostgreSQL не запущен (make db-up)');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'event-');
        file_put_contents($tmp, json_encode([
            'provider' => 'ghost',
            'provider_request_id' => 'g1',
            'model' => 'nope',
            'user' => 'u-x',
            'ts' => '2026-09-16T03:00:00Z',
            'tokens_input' => 1_000_000,
            'tokens_output' => 100,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]));

        self::assertSame(0, self::exec('app:usage:import', ['file' => $tmp])['exit']);
        $r = self::exec('app:billing:rate');
        self::assertSame(0, $r['exit'], 'rate без доступных цен — код 0');
        self::assertStringContainsString('RATED=0 UNRATED=1', $r['stdout']);
        self::assertStringContainsString('UNRATED ghost/g1 2026-09-16T03:00:00Z no_price', $r['stderr']);

        unlink($tmp);
    }
}
