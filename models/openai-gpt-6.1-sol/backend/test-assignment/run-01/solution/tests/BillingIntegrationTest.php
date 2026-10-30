<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use PHPUnit\Framework\TestCase;

/** TEST_DATABASE_URL — подключение PostgreSQL с правом CREATE DATABASE; исходная БД не меняется. */
final class BillingIntegrationTest extends TestCase
{
    private Connection $admin;
    private string $database;
    private string $url;
    private array $files = [];

    protected function setUp(): void
    {
        $url = getenv('TEST_DATABASE_URL');
        if (!$url) {
            self::markTestSkipped('Для интеграционных тестов задайте TEST_DATABASE_URL (PostgreSQL с правом CREATE DATABASE).');
        }
        $params = (new DsnParser(['postgresql' => 'pdo_pgsql']))->parse($url);
        $this->admin = DriverManager::getConnection($params);
        $this->database = 'billing_test_' . bin2hex(random_bytes(6));
        $this->admin->executeStatement('CREATE DATABASE ' . $this->database);
        $this->url = preg_replace('~/[^/?]+(?:\?|$)~', '/' . $this->database . '?', $url);
        $this->ok(['doctrine:migrations:migrate', '-n']);
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $path) {
            unlink($path);
        }
        if (isset($this->admin)) {
            $this->admin->executeStatement('DROP DATABASE ' . $this->database . ' WITH (FORCE)');
            $this->admin->close();
        }
    }

    private function command(array $args): array
    {
        $process = proc_open(
            [PHP_BINARY, 'bin/console', ...$args],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__),
            array_merge(getenv(), ['DATABASE_URL' => $this->url, 'APP_ENV' => 'test', 'APP_DEBUG' => '0'])
        );
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $stdout, $stderr];
    }

    private function ok(array $args): string
    {
        [$code, $stdout, $stderr] = $this->command($args);
        self::assertSame(0, $code, $stderr);

        return $stdout;
    }

    private function file(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'billing_');
        file_put_contents($path, $content);
        $this->files[] = $path;

        return $path;
    }

    private function event(string $id, string $provider = 'prism', string $ts = '2026-09-15T11:00:00Z', int $input = 1000000): array
    {
        return ['provider' => $provider, 'provider_request_id' => $id, 'model' => $provider === 'prism' ? 'lumen-8' : 'gyro-pro',
            'user' => 'u-test', 'ts' => $ts, 'tokens_input' => $input, 'tokens_output' => 0, 'tokens_cache_read' => 0, 'tokens_cache_write' => 0];
    }

    private function usage(array $events): string
    {
        return $this->file(implode("\n", array_map(static fn (array $row): string => json_encode($row, JSON_THROW_ON_ERROR), $events)) . "\n");
    }

    public function testAcceptanceAndIdempotence(): void
    {
        $expected = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            . "u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
            . "u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
            . "u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
            . "TOTAL,,6200000,2300000,2500000,83333,33.662499\n";
        for ($i = 0; $i < 2; ++$i) {
            $this->ok(['app:prices:import', 'fixtures/prices.json']);
            $this->ok(['app:usage:import', 'fixtures/usage.jsonl']);
            self::assertSame($i === 0 ? "RATED=3 UNRATED=0\n" : "RATED=0 UNRATED=0\n", $this->ok(['app:billing:rate']));
            self::assertSame($expected, $this->ok(['app:billing:report', '--from=2026-09-01T00:00:00Z', '--to=2026-10-01T00:00:00Z']));
        }
        self::assertSame(
            "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\nu-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\nTOTAL,,2000000,1000000,0,0,11.000000\n",
            $this->ok(['app:billing:report', '--from=2026-09-15T12:00:00Z', '--to=2026-09-20T17:10:00Z'])
        );
    }

    public function testUnratedRetryDuplicatesAndProviderKey(): void
    {
        $file = $this->usage([$this->event('same'), $this->event('same', 'sfera')]);
        $this->ok(['app:usage:import', $file]);
        [$code, $out, $err] = $this->command(['app:billing:rate']);
        self::assertSame(0, $code);
        self::assertSame("RATED=0 UNRATED=2\n", $out);
        self::assertSame("UNRATED prism/same 2026-09-15T11:00:00Z no_price\nUNRATED sfera/same 2026-09-15T11:00:00Z no_price\n", $err);
        self::assertStringContainsString("TOTAL,,0,0,0,0,0.000000\n", $this->ok(['app:billing:report']));
        $changed = $this->event('same', input: 9000000);
        $changed['user'] = 'ignored';
        $this->ok(['app:usage:import', $this->usage([$changed])]);
        $this->ok(['app:prices:import', 'fixtures/prices.json']);
        self::assertSame("RATED=2 UNRATED=0\n", $this->ok(['app:billing:rate']));
        self::assertStringContainsString("TOTAL,,2000000,0,0,0,5.400000\n", $this->ok(['app:billing:report']));
        $this->ok(['app:usage:import', $file]);
        self::assertSame("RATED=0 UNRATED=0\n", $this->ok(['app:billing:rate']));
    }

    public function testWindowsPriceBoundaryZeroAndNoEarlierPrice(): void
    {
        $this->ok(['app:prices:import', 'fixtures/prices.json']);
        $events = [];
        foreach (['16:29:59', '16:30:00', '23:59:59', '00:00:00', '00:29:59', '00:30:00', '12:00:00'] as $i => $time) {
            $events[] = $this->event((string) $i, ts: '2026-09-15T' . $time . 'Z');
        }
        $events[] = $this->event('zero', input: 0);
        $events[] = $this->event('early', ts: '2026-07-31T23:59:59Z');
        $this->ok(['app:usage:import', $this->usage($events)]);
        [$code, $out, $err] = $this->command(['app:billing:rate']);
        self::assertSame(0, $code);
        self::assertSame("RATED=8 UNRATED=1\n", $out);
        self::assertSame("UNRATED prism/early 2026-07-31T23:59:59Z no_price\n", $err);
        self::assertStringContainsString("TOTAL,,7000000,0,0,0,10.500000\n", $this->ok(['app:billing:report']));
    }

    public function testFractionalBoundaryAndSumOfRoundedEvents(): void
    {
        $prices = ['providers' => [['code' => 'sfera', 'name' => 'Сфера', 'off_peak' => null]],
            'models' => [['provider' => 'sfera', 'code' => 'gyro-pro']],
            'prices' => [['provider' => 'sfera', 'model' => 'gyro-pro', 'valid_from' => '2026-09-15T11:00:00.1Z',
                'currency' => 'USD', 'unit' => 'per_1m_tokens_unified', 'rates' => ['any' => '0.500000']]]];
        $this->ok(['app:prices:import', $this->file(json_encode($prices))]);
        $this->ok(['app:usage:import', $this->usage([
            $this->event('a', 'sfera', '2026-09-15T11:00:00.100000Z', 1),
            $this->event('b', 'sfera', '2026-09-15T11:00:00.2Z', 1),
            $this->event('early', 'sfera', '2026-09-15T11:00:00.099999Z', 1),
        ])]);
        self::assertSame("RATED=2 UNRATED=1\n", $this->ok(['app:billing:rate']));
        self::assertStringContainsString("TOTAL,,2,0,0,0,0.000002\n", $this->ok(['app:billing:report']));
        self::assertStringContainsString("TOTAL,,1,0,0,0,0.000001\n", $this->ok(['app:billing:report',
            '--from=2026-09-15T11:00:00.1Z', '--to=2026-09-15T11:00:00.2Z']));
        // Обычный upsert меняет справочник, но не переписывает уже показанные расходы.
        $prices['prices'][0]['rates']['any'] = '2.000000';
        $this->ok(['app:prices:import', $this->file(json_encode($prices))]);
        self::assertStringContainsString("TOTAL,,2,0,0,0,0.000002\n", $this->ok(['app:billing:report']));
    }

    public function testMalformedInputRollsBackAndUsesStderr(): void
    {
        $file = $this->file(json_encode($this->event('rollback')) . "\n{broken\n");
        [$code, $out, $err] = $this->command(['app:usage:import', $file]);
        self::assertSame(1, $code);
        self::assertSame('', $out);
        self::assertStringContainsString('Строка 2:', $err);
        self::assertSame("RATED=0 UNRATED=0\n", $this->ok(['app:billing:rate']));
        [$code, $out, $err] = $this->command(['app:prices:import', $this->file('{broken')]);
        self::assertSame(1, $code);
        self::assertSame('', $out);
        self::assertNotSame('', $err);
        $prices = json_decode(file_get_contents(dirname(__DIR__) . '/fixtures/prices.json'), true);
        $prices['prices'][0]['rates']['input'] = 3.0;
        self::assertSame(1, $this->command(['app:prices:import', $this->file(json_encode($prices))])[0]);
        // Провайдеры и модели не должны сохраниться после ошибки в последней секции.
        $prices['providers'] = [];
        $prices['models'] = [];
        $prices['prices'][0]['rates']['input'] = '3.000000';
        self::assertSame(1, $this->command(['app:prices:import', $this->file(json_encode($prices))])[0]);
    }
}
