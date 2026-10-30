<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Приёмочный сценарий целиком (см. TASK.md): импорт цен и событий,
 * идемпотентность повторного импорта, тарификация, отчёт (полный и
 * периодный), ретро-коррекция (reprice) и повторная тарификация.
 *
 * Стоимости сверяются с вручную рассчитанными значениями:
 *  - e1: (1200000*3 + 800000*12 + 2500000*0.3 + 83333*3.75)/1e6
 *        = 14.26249875 -> 14.262499 (цена от 2026-08-01),
 *  - e2: ts=12:00 => включается строка от 2026-09-15T12:00Z (valid_from ≤ ts):
 *        (2000000*1.5 + 1000000*8)/1e6 = 11.000000,
 *  - e3: unified: (3500000)*2.4/1e6 = 8.400000 (оф-пика у sfera нет).
 */
final class BillingPipelineTest extends KernelTestCase
{
    private ?Application $application = null;
    private $connection = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->application = new Application(self::getKernel());
        $this->connection = self::getContainer()->get(\Doctrine\DBAL\Connection::class);
        $this->resetTables();
    }

    protected function tearDown(): void
    {
        $this->resetTables();
    }

    public function testFullAcceptancePipeline(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $pricesFile = $projectDir . '/fixtures/prices.json';
        $usageFile = $projectDir . '/fixtures/usage.jsonl';

        // 1. Импорт цен.
        $this->executeCommand('app:prices:import', ['file' => $pricesFile]);
        $this->assertSame(4, $this->scalar('SELECT count(*) FROM prices'));
        $this->assertSame(2, $this->scalar('SELECT count(*) FROM providers'));
        $this->assertSame(3, $this->scalar('SELECT count(*) FROM models'));

        // 2. Импорт событий.
        $this->executeCommand('app:usage:import', ['file' => $usageFile]);
        $this->assertSame(3, $this->scalar('SELECT count(*) FROM usage_events'));
        $this->assertSame(3, $this->scalar("SELECT count(*) FROM usage_events WHERE status = 'pending'"));

        // 3. Повторный импорт — идемпотентность (дубликаты не заносятся,
        //    ничего не пересчитывается).
        $this->executeCommand('app:usage:import', ['file' => $usageFile]);
        $this->assertSame(3, $this->scalar('SELECT count(*) FROM usage_events'));
        $this->assertSame(
            3,
            $this->scalar("SELECT count(*) FROM usage_events WHERE status = 'pending' AND cost_usd IS NULL")
        );

        // 4. Тарификация.
        $this->executeCommand('app:billing:rate');
        $this->assertSame(
            0,
            $this->scalar("SELECT count(*) FROM usage_events WHERE status = 'pending'")
        );
        $costs = $this->connection->fetchAllAssociative(
            'SELECT provider_request_id, cost_usd
             FROM usage_events
             ORDER BY provider_request_id'
        );
        $byId = array_column($costs, 'cost_usd', 'provider_request_id');
        self::assertSame('14.262499', $byId['e1'] ?? null);
        self::assertSame('11.000000', $byId['e2'] ?? null);
        self::assertSame('8.400000', $byId['e3'] ?? null);

        // 5. Повторная тарификация — RATED=0 (идемпотентность).
        $output = $this->captureOutput('app:billing:rate');
        self::assertStringContainsString('RATED=0', $output);
        $costs2 = $this->connection->fetchAllAssociative(
            'SELECT provider_request_id, cost_usd FROM usage_events ORDER BY provider_request_id'
        );
        self::assertSame($byId, array_column($costs2, 'cost_usd', 'provider_request_id'));

        // 6. Отчёт (полный период), сверка строк и TOTAL.
        $report = $this->captureOutput('app:billing:report');
        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499', $report);
        self::assertStringContainsString('u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000', $report);
        self::assertStringContainsString('u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000', $report);
        self::assertStringContainsString('TOTAL,,6200000,2300000,2500000,83333,33.662499', $report);

        // 7. Отчёт за период [2026-08-01 .. 2026-09-15T12:00] — только e1.
        $report = $this->captureOutput('app:billing:report', [
            '--from' => '2026-08-01T00:00:00Z',
            '--to' => '2026-09-15T12:00:00Z',
        ]);
        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499', $report);
        self::assertStringNotContainsString('gyro-pro', $report);
        self::assertStringContainsString('TOTAL,,1200000,800000,2500000,83333,14.262499', $report);

        // 8. Ретро-коррекция (reprice) строки цен для e1.
        $correctionFile = tempnam(sys_get_temp_dir(), 'correction') . '.json';
        file_put_contents($correctionFile, json_encode([
            'providers' => [],
            'models' => [],
            'prices' => [
                [
                    'provider' => 'prism',
                    'model' => 'lumen-8',
                    'valid_from' => '2026-08-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens',
                    'rates' => [
                        'input' => '4.000000',
                        'output' => '12.000000',
                        'cache_read' => '0.300000',
                        'cache_write' => '3.750000',
                    ],
                ],
            ],
        ]));
        $output = $this->captureOutput('app:billing:reprice', ['file' => $correctionFile]);
        unlink($correctionFile);

        // Новая цена e1: (1200000*4 + 800000*12 + 2500000*0.3 + 83333*3.75)/1e6
        // = (4800000+9600000+750000+312498.75)/1e6 = 15.462499 -> dельта = +1.200001
        $costs3 = $this->connection->fetchAllAssociative(
            'SELECT provider_request_id, cost_usd FROM usage_events ORDER BY provider_request_id'
        );
        $byId3 = array_column($costs3, 'cost_usd', 'provider_request_id');
        self::assertSame('15.462499', $byId3['e1'] ?? null, 'Стоимость e1 пересчитана по новой строке цен');
        self::assertSame('11.000000', $byId3['e2'] ?? null, 'e2 не затронут коррекцией чужой строки');
        self::assertSame('8.400000', $byId3['e3'] ?? null, 'e3 не затронут коррекцией');

        $journal = $this->scalar('SELECT count(*) FROM reprice_log');
        self::assertSame(1, $journal, 'В журнале ровно одна корректировка');

        // 9. Отчёт после коррекции — новые суммы, без задвоений.
        $report = $this->captureOutput('app:billing:report');
        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,15.462499', $report);
        self::assertStringContainsString('TOTAL,,6200000,2300000,2500000,83333,34.862499', $report);

        // 10. Повторная тарификация после коррекции — RATED=0, суммы не поменялись.
        $this->assertStringContainsString('RATED=0', $this->captureOutput('app:billing:rate'));
        $costs4 = $this->connection->fetchAllAssociative(
            'SELECT provider_request_id, cost_usd FROM usage_events ORDER BY provider_request_id'
        );
        self::assertSame($byId3, array_column($costs4, 'cost_usd', 'provider_request_id'));
    }

    // ---------- helpers ----------

    private function resetTables(): void
    {
        $conn = $this->connection;
        if ($conn !== null) {
            $this->connection->transactional(function () use ($conn): void {
                $conn->executeStatement('TRUNCATE usage_events, reprice_log RESTART IDENTITY');
                $conn->executeStatement('DELETE FROM prices');
                $conn->executeStatement('DELETE FROM models');
                $conn->executeStatement('DELETE FROM providers');
            });
        }
    }

    private function executeCommand(string $name, array $params = []): void
    {
        $tester = new CommandTester($this->application->find($name));
        $status = $tester->execute($params);
        if ($status !== 0) {
            self::fail(sprintf(
                'Команда "%s" завершилась с кодом %d. Вывод: %s',
                $name,
                $status,
                $tester->getDisplay()
            ));
        }
    }

    private function captureOutput(string $name, array $params = []): string
    {
        $tester = new CommandTester($this->application->find($name));
        $status = $tester->execute($params);
        if ($status !== 0) {
            self::fail(sprintf('Команда "%s" завершилась с кодом %d', $name, $status));
        }

        return $tester->getDisplay();
    }

    private function scalar(string $sql): int
    {
        $value = $this->connection->fetchOne($sql);

        return (int) $value;
    }
}
