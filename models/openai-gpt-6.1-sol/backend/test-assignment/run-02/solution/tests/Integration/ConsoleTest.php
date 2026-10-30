<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\BuildReport;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use PHPUnit\Framework\TestCase;

/** Настоящие CLI-процессы и PostgreSQL; создаётся и удаляется отдельная случайная БД. */
final class ConsoleTest extends TestCase
{
    private static ?Connection $admin = null;
    private static ?Connection $db = null;
    private static string $database;
    private static string $url;
    private array $files = [];

    private const EXPECTED = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
        . "u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
        . "u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
        . "u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
        . "TOTAL,,6200000,2300000,2500000,83333,33.662499\n";

    public static function setUpBeforeClass(): void
    {
        $baseUrl = getenv('TEST_DATABASE_URL');
        if (!$baseUrl) {
            self::markTestSkipped('Для интеграционных тестов задайте TEST_DATABASE_URL (роль с CREATEDB).');
        }
        $parameters = (new DsnParser(['postgresql' => 'pdo_pgsql', 'postgres' => 'pdo_pgsql']))->parse($baseUrl);
        self::$admin = DriverManager::getConnection($parameters);
        self::$database = 'billing_test_' . bin2hex(random_bytes(6));
        self::$admin->executeStatement('CREATE DATABASE ' . self::$database);
        $path = parse_url($baseUrl, PHP_URL_PATH);
        self::$url = substr_replace($baseUrl, '/' . self::$database, strrpos($baseUrl, $path), strlen($path));
        $parameters['dbname'] = self::$database;
        self::$db = DriverManager::getConnection($parameters);
        [$status, $stdout, $stderr] = self::cli('doctrine:migrations:migrate', '-n');
        self::assertSame(0, $status, $stdout . $stderr);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$db !== null) {
            self::$db->close();
        }
        if (self::$admin !== null && isset(self::$database)) {
            self::$admin->executeStatement('DROP DATABASE IF EXISTS ' . self::$database . ' WITH (FORCE)');
            self::$admin->close();
        }
    }

    protected function setUp(): void
    {
        self::$db->executeStatement('TRUNCATE billing_provider, billing_model, billing_price, billing_usage, billing_price_adjustment RESTART IDENTITY CASCADE');
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }

    private static function cli(string ...$arguments): array
    {
        return self::finishCli(self::startCli(...$arguments));
    }

    private static function startCli(string ...$arguments): array
    {
        $process = proc_open(
            [PHP_BINARY, __DIR__ . '/../../bin/console', '--no-ansi', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            array_merge(getenv(), ['DATABASE_URL' => self::$url, 'APP_ENV' => 'test', 'APP_DEBUG' => '0']),
        );
        fclose($pipes[0]);

        return [$process, $pipes];
    }

    private static function finishCli(array $pending): array
    {
        [$process, $pipes] = $pending;
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $stdout, $stderr];
    }

    private function ok(string ...$arguments): string
    {
        [$status, $stdout, $stderr] = self::cli(...$arguments);
        self::assertSame(0, $status, $stdout . $stderr);
        self::assertSame('', $stderr);

        return $stdout;
    }

    private function file(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'billing_console_');
        $this->files[] = $file;
        file_put_contents($file, $contents);

        return $file;
    }

    private function json(array $data): string
    {
        return $this->file(json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private function event(string $id, array $changes = []): array
    {
        return array_replace([
            'provider' => 'prism', 'provider_request_id' => $id, 'model' => 'lumen-8', 'user' => 'u',
            'ts' => '2026-09-15T12:00:00Z',
            'tokens_input' => 1000000, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0,
        ], $changes);
    }

    private function usage(array $events): string
    {
        return $this->file(implode("\n", array_map(static fn (array $event): string => json_encode($event, JSON_THROW_ON_ERROR), $events)) . "\n");
    }

    private function fixtures(): void
    {
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        $this->ok('app:usage:import', __DIR__ . '/../../fixtures/usage.jsonl');
    }

    private function correction(string $inputRate = '6.000000'): array
    {
        return ['prices' => [[
            'provider' => 'prism', 'model' => 'lumen-8', 'valid_from' => '2026-08-01T00:00:00Z',
            'currency' => 'USD', 'unit' => 'per_1m_tokens',
            'rates' => ['input' => $inputRate, 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
        ]]];
    }

    public function testAcceptanceAndRepeatImports(): void
    {
        $this->fixtures();
        self::assertSame("RATED=3 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame(self::EXPECTED, $this->ok('app:billing:report', '--from=2026-09-01T00:00:00Z', '--to=2026-10-01T00:00:00Z'));
        $this->fixtures();
        self::assertSame("RATED=0 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame(self::EXPECTED, $this->ok('app:billing:report'));
        self::assertSame(4, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_price'));
        self::assertSame(3, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_usage'));
        [$status, $stdout, $stderr] = self::cli('doctrine:schema:validate');
        self::assertSame(0, $status, $stdout . $stderr);
    }

    public function testDuplicatesAreIgnoredButProviderIsPartOfKey(): void
    {
        $this->fixtures();
        $this->ok('app:billing:rate');
        $this->ok('app:usage:import', $this->usage([
            $this->event('e1', ['user' => 'другой', 'tokens_input' => 0, 'model' => 'неизвестная']),
            $this->event('e1', ['provider' => 'sfera', 'model' => 'gyro-pro']),
        ]));
        self::assertSame("RATED=1 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame('u-alice', self::$db->fetchOne("SELECT user_id FROM billing_usage WHERE provider_code = 'prism' AND provider_request_id = 'e1'"));
        self::assertSame('14.262499', self::$db->fetchOne("SELECT cost_usd FROM billing_usage WHERE provider_code = 'prism' AND provider_request_id = 'e1'"));
        self::assertSame(4, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_usage'));
    }

    public function testTemporalPricesAndOffPeakBoundariesWithMicroseconds(): void
    {
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        $times = [
            '2026-09-15T11:59:59.999999Z' => '3.000000',
            '2026-09-15T12:00:00Z' => '1.500000',
            '2026-09-15T16:29:59.999999Z' => '1.500000',
            '2026-09-15T16:30:00Z' => '0.750000',
            '2026-09-16T00:00:00Z' => '0.750000',
            '2026-09-16T00:29:59.999999Z' => '0.750000',
            '2026-09-16T00:30:00Z' => '1.500000',
        ];
        $events = [];
        foreach ($times as $ts => $cost) {
            $events[] = $this->event($ts, ['ts' => $ts]);
        }
        $events[] = $this->event('zero', ['tokens_input' => 0]);
        $this->ok('app:usage:import', $this->usage($events));
        self::assertSame("RATED=8 UNRATED=0\n", $this->ok('app:billing:rate'));
        $times['zero'] = '0.000000';
        self::assertEquals($times, self::$db->fetchAllKeyValue('SELECT provider_request_id, cost_usd FROM billing_usage'));
        $report = $this->ok('app:billing:report', '--from=2026-09-15T16:30:00Z', '--to=2026-09-16T00:30:00Z');
        self::assertStringContainsString("u,prism/lumen-8,3000000,0,0,0,2.250000\n", $report);
    }

    public function testUnratedRetriesAndPricesArrivingAfterUsage(): void
    {
        $this->ok('app:usage:import', $this->usage([$this->event('early', ['ts' => '2026-07-15T12:00:00Z'])]));
        $warning = "UNRATED prism/early 2026-07-15T12:00:00Z no_price\n";
        self::assertSame([0, "RATED=0 UNRATED=1\n", $warning], self::cli('app:billing:rate'));
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        self::assertSame([0, "RATED=0 UNRATED=1\n", $warning], self::cli('app:billing:rate'));
        self::assertStringNotContainsString('prism/lumen-8', $this->ok('app:billing:report'));
        $document = $this->correction('3.000000') + ['providers' => [], 'models' => []];
        $document['prices'][0]['valid_from'] = '2026-07-01T00:00:00Z';
        $this->ok('app:prices:import', $this->json($document));
        self::assertSame("RATED=1 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertStringContainsString('3.000000', $this->ok('app:billing:report'));
    }

    public function testInvalidJsonlRollsBackTheWholeFile(): void
    {
        $file = $this->file(json_encode($this->event('valid'), JSON_THROW_ON_ERROR) . "\n{\n");
        [$status, $stdout, $stderr] = self::cli('app:usage:import', $file);
        self::assertSame(1, $status);
        self::assertSame('', $stdout);
        self::assertStringContainsString('Строка 2:', $stderr);
        self::assertSame(0, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_usage'));
        [$status, $stdout] = self::cli('app:prices:import', $this->file('{'));
        self::assertSame(1, $status);
        self::assertSame('', $stdout);
    }

    public function testUnknownCatalogReferenceRollsBackProviderChanges(): void
    {
        $file = $this->json([
            'providers' => [['code' => 'new', 'name' => 'Новый', 'off_peak' => null]],
            'models' => [['provider' => 'unknown', 'code' => 'm']], 'prices' => [],
        ]);
        self::assertSame(1, self::cli('app:prices:import', $file)[0]);
        self::assertSame(0, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_provider'));
    }

    public function testEventRoundingIsSummedWithoutRecalculatingGroup(): void
    {
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        $document = $this->correction('0.500000') + ['providers' => [], 'models' => []];
        $this->ok('app:prices:import', $this->json($document));
        $this->ok('app:usage:import', $this->usage([
            $this->event('a', ['ts' => '2026-09-15T11:00:00Z', 'tokens_input' => 1]),
            $this->event('b', ['ts' => '2026-09-15T11:00:00Z', 'tokens_input' => 1]),
        ]));
        $this->ok('app:billing:rate');
        self::assertStringContainsString("u,prism/lumen-8,2,0,0,0,0.000002\n", $this->ok('app:billing:report'));
    }

    public function testReportEscapesCsvAndSumsBeyondPhpIntegerRange(): void
    {
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        $user = "<info>имя,\"цитата\"\nстрока</info>";
        $event = ['provider' => 'sfera', 'model' => 'gyro-pro', 'user' => $user, 'tokens_input' => PHP_INT_MAX];
        $this->ok('app:usage:import', $this->usage([$this->event('a', $event), $this->event('b', $event)]));
        $this->ok('app:billing:rate');
        $report = $this->ok('app:billing:report');
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $report);
        rewind($stream);
        self::assertSame(BuildReport::HEADER, fgetcsv($stream, escape: ''));
        $row = fgetcsv($stream, escape: '');
        self::assertSame($user, $row[0]);
        self::assertSame(bcadd((string) PHP_INT_MAX, (string) PHP_INT_MAX, 0), $row[2]);
        self::assertSame('44272185776902.923874', $row[6]);
        fclose($stream);
    }

    public function testEmptyReportAndInvalidPeriod(): void
    {
        self::assertSame(implode(',', BuildReport::HEADER) . "\nTOTAL,,0,0,0,0,0.000000\n", $this->ok('app:billing:report'));
        foreach ([['--from=2026-02-30T00:00:00Z'], ['--from=2026-10-01T00:00:00Z', '--to=2026-09-01T00:00:00Z']] as $options) {
            [$status, $stdout, $stderr] = self::cli('app:billing:report', ...$options);
            self::assertSame(1, $status);
            self::assertSame('', $stdout);
            self::assertNotSame('', $stderr);
        }
    }

    public function testRepriceAuditsOnlyEventsUsingReplacedPriceAndIsIdempotent(): void
    {
        $this->fixtures();
        $this->ok('app:billing:rate');
        $file = $this->json($this->correction());
        self::assertSame("REPRICED_EVENTS=1\nREPRICE_DELTA_USD=3.600000\n", $this->ok('app:billing:reprice', $file));
        self::assertSame(str_replace(['14.262499', '33.662499'], ['17.862499', '37.262499'], self::EXPECTED), $this->ok('app:billing:report'));
        self::assertSame(1, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_price_adjustment'));
        self::assertSame("REPRICED_EVENTS=0\nREPRICE_DELTA_USD=0.000000\n", $this->ok('app:billing:reprice', $file));
        self::assertSame("REPRICED_EVENTS=1\nREPRICE_DELTA_USD=-7.200000\n", $this->ok('app:billing:reprice', $this->json($this->correction('0.000000'))));
        self::assertSame(2, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_price_adjustment'));
    }

    public function testRepriceRetainsOriginalOffPeakMultiplier(): void
    {
        $this->fixtures();
        $this->ok('app:usage:import', $this->usage([$this->event('night', ['ts' => '2026-08-15T17:00:00Z'])]));
        $this->ok('app:billing:rate');
        $this->ok('app:prices:import', $this->json([
            'providers' => [['code' => 'prism', 'name' => 'Prism', 'off_peak' => null]], 'models' => [], 'prices' => [],
        ]));
        self::assertSame("REPRICED_EVENTS=2\nREPRICE_DELTA_USD=5.100000\n", $this->ok('app:billing:reprice', $this->json($this->correction())));
        self::assertSame('3.000000', self::$db->fetchOne("SELECT cost_usd FROM billing_usage WHERE provider_request_id = 'night'"));
    }

    public function testRepriceCannotCreateProviderFromCorrectionFile(): void
    {
        $document = $this->correction();
        $document['providers'] = [['code' => 'prism', 'name' => 'Prism', 'off_peak' => null]];
        $document['models'] = [['provider' => 'prism', 'code' => 'lumen-8']];
        self::assertSame(1, self::cli('app:billing:reprice', $this->json($document))[0]);
        self::assertSame(0, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_provider'));
    }

    public function testOrdinaryPriceUpsertDoesNotSilentlyRepriceEvents(): void
    {
        $this->fixtures();
        $this->ok('app:billing:rate');
        $document = $this->correction() + ['providers' => [], 'models' => []];
        $this->ok('app:prices:import', $this->json($document));
        self::assertSame("RATED=0 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame(self::EXPECTED, $this->ok('app:billing:report'));
    }

    public function testCorrectionCanInsertHistoricalPriceWithoutReratingOtherEvents(): void
    {
        $this->fixtures();
        $this->ok('app:billing:rate');
        $document = $this->correction();
        $document['prices'][0]['valid_from'] = '2026-09-01T00:00:00.123456Z';
        $file = $this->json($document);
        self::assertSame("REPRICED_EVENTS=0\nREPRICE_DELTA_USD=0.000000\n", $this->ok('app:billing:reprice', $file));
        self::assertSame(self::EXPECTED, $this->ok('app:billing:report'));
        $this->ok('app:usage:import', $this->usage([
            $this->event('before', ['ts' => '2026-09-01T00:00:00.123455Z']),
            $this->event('exact', ['ts' => '2026-09-01T00:00:00.123456Z']),
        ]));
        self::assertSame("RATED=2 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame('1.500000', self::$db->fetchOne("SELECT cost_usd FROM billing_usage WHERE provider_request_id = 'before'"));
        self::assertSame('3.000000', self::$db->fetchOne("SELECT cost_usd FROM billing_usage WHERE provider_request_id = 'exact'"));
        self::assertSame("REPRICED_EVENTS=0\nREPRICE_DELTA_USD=0.000000\n", $this->ok('app:billing:reprice', $file));
    }

    public function testConcurrentRatingAndCorrectionsDoNotDoubleCharge(): void
    {
        $this->fixtures();
        $first = self::startCli('app:billing:rate');
        $second = self::startCli('app:billing:rate');
        $results = [self::finishCli($first), self::finishCli($second)];
        sort($results);
        self::assertSame([[0, "RATED=0 UNRATED=0\n", ''], [0, "RATED=3 UNRATED=0\n", '']], $results);
        $file = $this->json($this->correction());
        $first = self::startCli('app:billing:reprice', $file);
        $second = self::startCli('app:billing:reprice', $file);
        $results = [self::finishCli($first), self::finishCli($second)];
        sort($results);
        self::assertSame([
            [0, "REPRICED_EVENTS=0\nREPRICE_DELTA_USD=0.000000\n", ''],
            [0, "REPRICED_EVENTS=1\nREPRICE_DELTA_USD=3.600000\n", ''],
        ], $results);
        self::assertSame(1, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_price_adjustment'));
    }

    public function testRatingAndRepricingAcrossBatches(): void
    {
        $this->ok('app:prices:import', __DIR__ . '/../../fixtures/prices.json');
        $events = [];
        for ($i = 0; $i < 405; ++$i) {
            $events[] = $this->event((string) $i, ['ts' => '2026-08-15T11:00:00Z']);
        }
        $this->ok('app:usage:import', $this->usage($events));
        self::assertSame("RATED=405 UNRATED=0\n", $this->ok('app:billing:rate'));
        self::assertSame("REPRICED_EVENTS=405\nREPRICE_DELTA_USD=1215.000000\n", $this->ok('app:billing:reprice', $this->json($this->correction())));
        self::assertSame(405, (int) self::$db->fetchOne('SELECT COUNT(*) FROM billing_price_adjustment'));
        self::assertStringContainsString('2430.000000', $this->ok('app:billing:report'));
    }
}
