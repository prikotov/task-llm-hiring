<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Command\BillingRateCommand;
use App\Command\BillingRepriceCommand;
use App\Command\BillingReportCommand;
use App\Command\PricesImportCommand;
use App\Command\UsageImportCommand;

/**
 * Оф-пик (в т.ч. окно через полночь), unrated-события, дедупликация по (provider, request id),
 * ретро-коррекция цен.
 */
final class OffPeakAndRepriceTest extends BillingIntegrationTestCase
{
    private const OFFPEAK_REPORT = <<<'CSV'
        user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
        u-x,nocturne/midnight-1,5000000,0,0,0,8.000000
        TOTAL,,5000000,0,0,0,8.000000
        CSV;

    public function testOffPeakMidnightCrossingBoundaries(): void
    {
        $this->tester(UsageImportCommand::class, ['file' => $this->fixturePath('offpeak_usage.jsonl')])
            ->assertCommandIsSuccessful();

        // Без каталога все 7 событий — unrated, команда завершается успешно.
        $early = $this->tester(BillingRateCommand::class);
        $early->assertCommandIsSuccessful();
        self::assertStringContainsString('UNRATED=7', $early->getDisplay());

        $this->tester(PricesImportCommand::class, ['file' => $this->fixturePath('offpeak_prices.json')])
            ->assertCommandIsSuccessful();

        // Расчётные: 5 (а1–а5). orphan-1 (модели нет цены) и mirror/a1 (такого же
        // provider_request_id, но другой провайдер) остаются unrated — дедупликация
        // идёт по паре (provider_code, provider_request_id).
        $rate = $this->tester(BillingRateCommand::class);
        $rate->assertCommandIsSuccessful();
        self::assertStringContainsString('RATED=5', $rate->getDisplay());
        self::assertStringContainsString('UNRATED=2', $rate->getDisplay());
        self::assertStringContainsString('no_price', $rate->getDisplay());

        // a1 22:59 → 2.000000 (вне окна)
        // a2 23:00 → 1.000000 (граница начала включается)
        // a3 00:59:59 → 1.000000 (окно переходит через полночь)
        // a4 01:00 → 2.000000 (граница конца не включается)
        // a5 13:00 → 2.000000 (вне окна)
        $report = $this->tester(BillingReportCommand::class);
        $report->assertCommandIsSuccessful();
        self::assertSame(self::OFFPEAK_REPORT, trim($report->getDisplay()));
    }

    public function testRepriceCorrectsOnlyAffectedEvents(): void
    {
        $this->tester(PricesImportCommand::class, ['file' => $this->fixturePath('prices.json')])
            ->assertCommandIsSuccessful();
        $this->tester(UsageImportCommand::class, ['file' => $this->fixturePath('usage.jsonl')])
            ->assertCommandIsSuccessful();
        $this->tester(BillingRateCommand::class)->assertCommandIsSuccessful();

        // В файле коррекции меняется ставка лumen-8 (valid_from 2026-08-01): input 3 → 4.
        // Затрагивается только то событие, для которого применимой стала именно эта строка:
        //   u-alice (2026-09-15T11:00 — раньше строки от 12:00) → 15.462499 (было 14.262499);
        // остальные лежат в строке от 2026-09-15T12:00 — не затрагиваются.
        $reprice = $this->tester(BillingRepriceCommand::class, ['file' => $this->fixturePath('reprice.json')]);
        $reprice->assertCommandIsSuccessful();
        self::assertStringContainsString('REPRICED_EVENTS=1', $reprice->getDisplay());
        self::assertStringContainsString('REPRICE_DELTA_USD=1.200000', $reprice->getDisplay());

        $report = $this->tester(BillingReportCommand::class, [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        $report->assertCommandIsSuccessful();

        $expected = <<<'CSV'
            user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
            u-alice,prism/lumen-8,1200000,800000,2500000,83333,15.462499
            u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
            u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
            TOTAL,,6200000,2300000,2500000,83333,34.862499
            CSV;
        self::assertSame($expected, trim($report->getDisplay()));

        // Повторная коррекция той же строки: дельта нулевая.
        $again = $this->tester(BillingRepriceCommand::class, ['file' => $this->fixturePath('reprice.json')]);
        $again->assertCommandIsSuccessful();
        self::assertStringContainsString('REPRICE_DELTA_USD=0.000000', $again->getDisplay());
    }

    public function testRepriceRejectsUnknownModel(): void
    {
        $file = sys_get_temp_dir().'/reprice_unknown.'.uniqid().'.json';
        file_put_contents($file, (static fn (): string => json_encode([
            'providers' => [],
            'models' => [],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'never-heard-of',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '4.000000', 'output' => '12.000000', 'cache_read' => '0.300000', 'cache_write' => '3.750000'],
            ]],
        ], JSON_THROW_ON_ERROR))(
        ));

        $tester = $this->tester(BillingRepriceCommand::class, ['file' => $file]);
        self::assertNotSame(0, $tester->getStatusCode());
        self::assertSame(1, $tester->getStatusCode());

        @unlink($file);
    }
}
