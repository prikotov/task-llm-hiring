<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Событие без применимой строки цен остаётся pending (unrated) и
 * рассчитывается в следующем запуске, как только строка цен появилась.
 *
 * Событие e10 (sfera/gyro-2): цены на gyro-2 ещё не загружены -> pending.
 * Событие e11 (sfera/gyro-2): ts раньше valid_from единственной строки
 * (2026-08-01 > 2026-01-10) -> также pending.
 * После импорта строки цен на sfera/gyro-mini оба события рассчитываются:
 *  - e10: (5e6 + 1e6) * 0.12 / 1e6 = 0.72,
 *  - e11: ts=2026-01-10T23:00Z, valid_from=2026-02-01 => строка НЕ применяемa
 *    (valid_from > ts), e11 остаётся pending.
 * Итог: RATED=1, e10=0.720000, e11 pending.
 */
final class LatePriceImportTest extends KernelTestCase
{
    private ?Application $application = null;
    /** @var \Doctrine\DBAL\Connection */
    private $connection = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->application = new Application(self::getKernel());
        $this->connection = self::getContainer()->get(\Doctrine\DBAL\Connection::class);
        foreach (['usage_events', 'reprice_log'] as $t) {
            $this->connection->executeStatement("TRUNCATE $t RESTART IDENTITY");
        }
        foreach (['prices', 'models', 'providers'] as $t) {
            $this->connection->executeStatement("DELETE FROM $t");
        }
    }

    public function testPendingUntilPriceExists(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $pricesFile = $projectDir . '/fixtures/prices.json';

        // 1. Справочник (без цен на gyro-2).
        $this->assertCommandSucceeds('app:prices:import', ['file' => $pricesFile]);

        // 2. Два события с моделью gyro-2 — цены нет вовсе и цена «позже» события.
        $this->connection->executeStatement(
            'INSERT INTO usage_events
               (provider_code, provider_request_id, model_code, user_id, ts,
                tokens_input, tokens_output, tokens_cache_read, tokens_cache_write)
             VALUES
               (:p, :id, :m, :u, :ts, 5000000, 1000000, 0, 0)',
            ['p' => 'sfera', 'id' => 'e10', 'm' => 'gyro-2', 'u' => 'u-carol',
             'ts' => new \DateTimeImmutable('2026-09-21T10:00:00Z', new \DateTimeZone('UTC'))],
            ['ts' => \Doctrine\DBAL\Types\Types::DATETIMETZ_IMMUTABLE]
        );
        $this->connection->executeStatement(
            'INSERT INTO usage_events
               (provider_code, provider_request_id, model_code, user_id, ts,
                tokens_input, tokens_output, tokens_cache_read, tokens_cache_write)
             VALUES
               (:p, :id, :m, :u, :ts, 2000000, 400000, 0, 0)',
            ['p' => 'sfera', 'id' => 'e11', 'm' => 'gyro-2', 'u' => 'u-carol',
             'ts' => new \DateTimeImmutable('2026-01-10T23:00:00Z', new \DateTimeZone('UTC'))],
            ['ts' => \Doctrine\DBAL\Types\Types::DATETIMETZ_IMMUTABLE]
        );

        // 3. Первый тарифицирующий запуск обоих -> pending.
        $out = $this->commandOutput('app:billing:rate');
        self::assertStringContainsString('RATED=0', $out);
        $pending = $this->connection->fetchFirstColumn(
            "SELECT provider_request_id FROM usage_events WHERE status = 'pending' ORDER BY 1"
        );
        self::assertEquals(['e10', 'e11'], array_values($pending));

        // 4. Импорт строки цен для gyro-2, valid_from=2026-08-01 (е10 <= ts,
        //    e11 < ts — значит, для e10 строка применима, а для e11 нет).
        $correction = tempnam(sys_get_temp_dir(), 'late') . '.json';
        file_put_contents($correction, json_encode([
            'providers' => [],
            'models' => [['provider' => 'sfera', 'code' => 'gyro-2']],
            'prices' => [
                [
                    'provider' => 'sfera',
                    'model' => 'gyro-2',
                    'valid_from' => '2026-08-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens_unified',
                    'rates' => ['any' => '0.120000'],
                ],
            ],
        ]));
        $this->assertCommandSucceeds('app:prices:import', ['file' => $correction]);
        unlink($correction);

        // 5. Повторный запуск: e10 рассчитан, e11 по-прежнему pending.
        $out = $this->commandOutput('app:billing:rate');
        self::assertStringContainsString('RATED=1', $out);
        $row = $this->connection->fetchAssociative(
            'SELECT status, cost_usd FROM usage_events WHERE provider_code = :p AND provider_request_id = :id',
            ['p' => 'sfera', 'id' => 'e10']
        );
        self::assertSame('rated', $row['status']);
        self::assertSame('0.720000', $row['cost_usd']);
        $row2 = $this->connection->fetchAssociative(
            'SELECT status, cost_usd FROM usage_events WHERE provider_code = :p AND provider_request_id = :id',
            ['p' => 'sfera', 'id' => 'e11']
        );
        self::assertSame('pending', $row2['status']);
        self::assertNull($row2['cost_usd']);
    }

    // ---------- helpers ----------

    private function assertCommandSucceeds(string $name, array $params = []): void
    {
        $tester = new CommandTester($this->application->find($name));
        $tester->execute($params);
        if ($tester->getStatusCode() !== 0) {
            self::fail(sprintf('%s: код %d, вывод: %s', $name, $tester->getStatusCode(), $tester->getDisplay()));
        }
    }

    private function commandOutput(string $name, array $params = []): string
    {
        $tester = new CommandTester($this->application->find($name));
        $tester->execute($params);
        if ($tester->getStatusCode() !== 0) {
            self::fail(sprintf('%s: код %d', $name, $tester->getStatusCode()));
        }

        return $tester->getDisplay();
    }
}
