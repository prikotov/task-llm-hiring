<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Прогон полного сценария ТЗ: импорт цен → импорт событий (с дублем) →
 * тарификация → отчёт, совпадающий с эталоном из TASK.md; повторные
 * проходы идемпотентны.
 */
final class BillingFlowTest extends IntegrationTestCase
{
    private const EXPECTED_REPORT = <<<'CSV'
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
CSV;

    /**
     * @return array{status: int, output: string}
     */
    private function exec(string $name, array $input = []): array
    {
        $application = new Application(self::$kernel);
        $tester = new CommandTester($application->find($name));
        $status = $tester->execute($input);

        return ['status' => $status, 'output' => $tester->getDisplay()];
    }

    public function testAcceptanceFlowOnCleanDatabase(): void
    {
        $r = $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')]);
        self::assertSame(0, $r['status']);

        $r = $this->exec('app:usage:import', ['file' => $this->fixture('usage.jsonl')]);
        self::assertSame(0, $r['status']);
        self::assertStringContainsString('inserted=3', $r['output']);

        $r = $this->exec('app:billing:rate');
        self::assertSame(0, $r['status']);
        self::assertStringContainsString('RATED=3', $r['output']);
        self::assertStringContainsString('UNRATED=0', $r['output']);

        $r = $this->exec('app:billing:report', [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        self::assertSame(0, $r['status']);
        self::assertStringContainsString(self::EXPECTED_REPORT, $r['output']);
    }

    public function testReimportIsIdempotent(): void
    {
        self::assertSame(0, $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status']);
        self::assertSame(0, $this->exec('app:usage:import', ['file' => $this->fixture('usage.jsonl')])['status']);
        self::assertSame(0, $this->exec('app:billing:rate')['status']);

        $r = $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')]);
        self::assertSame(0, $r['status']);
        // Повторный импорт событий — дубли пропущены, новых строк нет
        $r = $this->exec('app:usage:import', ['file' => $this->fixture('usage.jsonl')]);
        self::assertSame(0, $r['status']);
        self::assertStringContainsString('inserted=0', $r['output']);
        self::assertStringContainsString('duplicates_skipped=3', $r['output']);
        self::assertSame(
            '3',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM usage_events'),
        );

        // Повторная тарификация новых событий не создаёт
        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=0', $r['output']);

        $r = $this->exec('app:billing:report', [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        self::assertStringContainsString(self::EXPECTED_REPORT, $r['output']);
    }

    public function testUnratedEventPickedUpAfterPriceAppears(): void
    {
        // Модель lumen-mini существует, но цена valid only с 2026-08-01;
        // событие раньше этого -> без цены; затем появляется более ранняя цена.
        self::assertSame(0, $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status']);

        $early = [
            'provider' => 'prism',
            'provider_request_id' => 'early-req',
            'model' => 'lumen-mini',
            'user' => 'u-early',
            'ts' => '2026-07-01T10:00:00Z', // раньше любой цены lumen-mini
            'tokens_input' => 1_000_000,
            'tokens_output' => 1_000_000,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];
        $path = $this->writeTemp('early.jsonl', json_encode($early, JSON_THROW_ON_ERROR)."\n");
        self::assertSame(0, $this->exec('app:usage:import', ['file' => $path])['status']);

        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=0', $r['output']);
        self::assertStringContainsString('UNRATED=1', $r['output']);

        // Появляется цена с 2026-06-01 (до события) -> повторный проход находит
        $correction = [
            'prices' => [
                [
                    'provider' => 'prism',
                    'model' => 'lumen-mini',
                    'valid_from' => '2026-06-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens',
                    'rates' => ['input' => '2.000000', 'output' => '2.000000',
                        'cache_read' => '0.200000', 'cache_write' => '0.200000'],
                ],
            ],
        ];
        $cpath = $this->writeTemp('early_prices.json', json_encode($correction, JSON_THROW_ON_ERROR));
        self::assertSame(0, $this->exec('app:billing:reprice', ['file' => $cpath])['status']);

        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=1', $r['output']);

        // 2M total tokens * 2.0/1M = 4.0
        $r = $this->exec('app:billing:report');
        self::assertStringContainsString('u-early,prism/lumen-mini', $r['output']);
        self::assertStringContainsString('4.000000', $r['output']);
    }

    public function testOffPeakWindowBoundariesAndMultipliers(): void
    {
        self::assertSame(0, $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status']);

        $mk = static fn (string $id, string $ts): array => [
            'provider' => 'prism',
            'provider_request_id' => $id,
            'model' => 'lumen-8',
            'user' => 'u-peak',
            'ts' => $ts,
            'tokens_input' => 1_000_000,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];

        // В окне: 16:30 включительно, 23:30, 00:29 (окно заходит за полночь)
        $inWindow = ['w-in-1' => $mk('w-in-1', '2026-09-01T16:30:00Z'),
                     'w-in-2' => $mk('w-in-2', '2026-09-01T23:30:00Z'),
                     'w-in-3' => $mk('w-in-3', '2026-09-02T00:29:00Z')];
        // Вне окна: 16:29:59, ровно 00:30, 09:00
        $outWindow = ['w-out-1' => $mk('w-out-1', '2026-09-01T16:29:00Z'),
                      'w-out-2' => $mk('w-out-2', '2026-09-02T00:30:00Z'),
                      'w-out-3' => $mk('w-out-3', '2026-09-01T09:00:00Z')];

        $lines = array_values(array_merge($inWindow, $outWindow));
        $path = $this->writeTemp(
            'peak.jsonl',
            implode("\n", array_map(fn (array $e): string => json_encode($e, JSON_THROW_ON_ERROR), $lines))."\n",
        );
        self::assertSame(0, $this->exec('app:usage:import', ['file' => $path])['status']);

        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=6', $r['output']);
        self::assertStringContainsString('UNRATED=0', $r['output']);

        // Тариф строки (valid_from 2026-07-01): input 3.0; в окне x0.5 = 1.5, вне = 3.0
        // 3 в окне * 1.5 + 3 вне * 3.0 = 4.5 + 9.0 = 13.5
        $r = $this->exec('app:billing:report');
        self::assertStringContainsString('u-peak,prism/lumen-8,6000000,0,0,0,13.500000', $r['output']);
    }

    public function testRepriceRetrievalRecalculatesExistingRatedEvents(): void
    {
        self::assertSame(0, $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status']);
        self::assertSame(0, $this->exec('app:usage:import', ['file' => $this->fixture('usage.jsonl')])['status']);
        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=3', $r['output']);

        // Меняем rate input lumen-8 строки от 2026-08-01: 3.0 -> 3.2.
        // Использует её только u-alice/alice-req-1 (ts до 12:00):
        // cost 14.262499 -> 1.2M*3.2 + 0.8M*12 + 2.5M*0.3 + 83333*3.75/1M = 14.502499, дельта +0.240000.
        // u-bob (строка от 2026-09-15T12) — без изменений.
        $correction = [
            'prices' => [
                [
                    'provider' => 'prism',
                    'model' => 'lumen-8',
                    'valid_from' => '2026-08-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens',
                    'rates' => ['input' => '3.200000', 'output' => '12.000000',
                        'cache_read' => '0.300000', 'cache_write' => '3.750000'],
                ],
            ],
        ];
        $cpath = $this->writeTemp('correction.json', json_encode($correction, JSON_THROW_ON_ERROR));
        $r = $this->exec('app:billing:reprice', ['file' => $cpath]);
        self::assertSame(0, $r['status']);
        self::assertStringContainsString('REPRICED_EVENTS=1', $r['output']);
        self::assertStringContainsString('REPRICE_DELTA_USD=0.240000', $r['output']);

        $r = $this->exec('app:billing:report');
        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.502499', $r['output']);
        self::assertStringContainsString('u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000', $r['output']);
        self::assertStringContainsString('TOTAL,,6200000,2300000,2500000,83333,33.902499', $r['output']);
    }

    private function writeTemp(string $name, string $content): string
    {
        $path = sys_get_temp_dir().'/billing_it_'.$name;
        file_put_contents($path, $content);

        return $path;
    }
}
