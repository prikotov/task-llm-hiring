<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Command\BillingRateCommand;
use App\Command\BillingReportCommand;
use App\Command\PricesImportCommand;
use App\Command\UsageImportCommand;

/**
 * Приёмочный сценарий из TASK.md + идемпотентность + дедупликация на тестовой базе.
 */
final class AcceptanceFlowTest extends BillingIntegrationTestCase
{
    private const EXPECTED_REPORT = <<<'CSV'
        user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
        u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
        u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
        u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
        TOTAL,,6200000,2300000,2500000,83333,33.662499
        CSV;

    public function testFullAcceptanceFlow(): void
    {
        $this->tester(PricesImportCommand::class, ['file' => $this->fixturePath('prices.json')])
            ->assertCommandIsSuccessful();
        $this->tester(UsageImportCommand::class, ['file' => $this->fixturePath('usage.jsonl')])
            ->assertCommandIsSuccessful();
        $rate = $this->tester(BillingRateCommand::class);

        $rate->assertCommandIsSuccessful();
        self::assertSame('RATED=3 UNRATED=0', trim($rate->getDisplay()));

        $report = $this->tester(BillingReportCommand::class, [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);

        $report->assertCommandIsSuccessful();
        self::assertSame(self::EXPECTED_REPORT, trim($report->getDisplay()));
    }

    public function testIsIdempotentOnReimport(): void
    {
        $prices = $this->fixturePath('prices.json');
        $usage = $this->fixturePath('usage.jsonl');

        foreach ([0, 1] as $_) {
            $this->tester(PricesImportCommand::class, ['file' => $prices])->assertCommandIsSuccessful();
            $this->tester(UsageImportCommand::class, ['file' => $usage])->assertCommandIsSuccessful();
        }

        $rate = $this->tester(BillingRateCommand::class);
        $rate->assertCommandIsSuccessful();
        self::assertStringContainsString('UNRATED=0', $rate->getDisplay());

        $report = $this->tester(BillingReportCommand::class, [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        $report->assertCommandIsSuccessful();
        self::assertSame(self::EXPECTED_REPORT, trim($report->getDisplay()));
    }

    public function testUsageBeforePricesGetsRatedAfterCatalogImport(): void
    {
        $this->tester(UsageImportCommand::class, ['file' => $this->fixturePath('usage.jsonl')])
            ->assertCommandIsSuccessful();

        $early = $this->tester(BillingRateCommand::class);
        $early->assertCommandIsSuccessful();
        self::assertStringContainsString('UNRATED=3', $early->getDisplay());

        $this->tester(PricesImportCommand::class, ['file' => $this->fixturePath('prices.json')])
            ->assertCommandIsSuccessful();

        $late = $this->tester(BillingRateCommand::class);
        $late->assertCommandIsSuccessful();
        self::assertSame('RATED=3 UNRATED=0', trim($late->getDisplay()));
    }

    public function testInvalidPricesFileFailsWithExitCode(): void
    {
        $badFile = sys_get_temp_dir().'/bad_prices.'.uniqid().'.json';
        file_put_contents($badFile, '{ не json }');

        $tester = $this->tester(PricesImportCommand::class, ['file' => $badFile]);
        self::assertNotSame(0, $tester->getStatusCode());
        self::assertSame(1, $tester->getStatusCode());
        self::assertStringContainsString('Ошибка', $tester->getDisplay());

        @unlink($badFile);
    }

    public function testInvalidUsageLineFailsWithExitCode(): void
    {
        $badFile = sys_get_temp_dir().'/bad_usage.'.uniqid().'.jsonl';
        file_put_contents($badFile, '{"provider": "p"}'."\n");

        $tester = $this->tester(UsageImportCommand::class, ['file' => $badFile]);
        self::assertNotSame(0, $tester->getStatusCode());
        self::assertSame(1, $tester->getStatusCode());

        @unlink($badFile);
    }

    public function testReportRejectsBadInstant(): void
    {
        $tester = $this->tester(BillingReportCommand::class, ['--from' => '15.09.2026']);
        self::assertNotSame(0, $tester->getStatusCode());
        self::assertSame(1, $tester->getStatusCode());
    }
}
