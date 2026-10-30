<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Приёмочный сценарий (TASK.md → «Приёмка»).
 * Каждый шаг исполняется в отдельном процессе (bin/console).
 */
final class AcceptanceTest extends KernelTestCase
{
    private function exec(string $cmd): array
    {
        exec($cmd . ' 2>&1', $lines, $code);

        return [$code, implode("\n", $lines)];
    }

    public function testFullScenario(): void
    {
        $repo = __DIR__ . '/..';
        $qrepo = escapeshellarg($repo);

        // 0. Clean schema + create
        [$code, $out] = $this->exec(sprintf(
            'cd %s && PGPASSWORD=app psql -h 127.0.0.1 -p 5544 -U app -d app -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" && php bin/console doctrine:schema:create',
            $qrepo
        ));
        $this->assertSame(0, $code, 'Schema setup failed: ' . $out);

        // 1. Import prices
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:prices:import %s',
            $qrepo, escapeshellarg($repo . '/fixtures/prices.json')
        ));
        $this->assertSame(0, $code, 'Prices import failed: ' . $out);
        $this->assertStringContainsString('provs=2', $out);
        $this->assertStringContainsString('price_rows=4', $out);

        // 2. Import usage
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:usage:import %s',
            $qrepo, escapeshellarg($repo . '/fixtures/usage.jsonl')
        ));
        $this->assertSame(0, $code, 'Usage import failed: ' . $out);
        $this->assertStringContainsString('new=3', $out);
        $this->assertStringContainsString('dup=0', $out);

        // 3. Rate
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:billing:rate',
            $qrepo
        ));
        $this->assertSame(0, $code, 'Rate failed: ' . $out);
        $this->assertStringContainsString('RATED=3', $out);
        $this->assertStringContainsString('UNRATED=0', $out);

        // 4. Report
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z',
            $qrepo
        ));
        $this->assertSame(0, $code, 'Report failed: ' . $out);
        $this->assertStringContainsString('u-alice,prism/lumen-8', $out);
        $this->assertStringContainsString('14.262499', $out);
        $this->assertStringContainsString('u-bob,prism/lumen-8', $out);
        $this->assertStringContainsString('11.000000', $out);
        $this->assertStringContainsString('33.662499', $out);

        // 5. Idempotent re-import
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:usage:import %s',
            $qrepo, escapeshellarg($repo . '/fixtures/usage.jsonl')
        ));
        $this->assertSame(0, $code, 'Usage re-import failed: ' . $out);
        $this->assertStringContainsString('new=0', $out);
        $this->assertStringContainsString('dup=3', $out);

        // 6. Report after re-import (unchanged)
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z',
            $qrepo
        ));
        $this->assertStringContainsString('14.262499', $out);

        // 7. Reprice
        $fixFile = sys_get_temp_dir() . '/test_reprice.json';
        file_put_contents($fixFile, json_encode([
            'prices' => [[
                'provider'   => 'prism',
                'model'      => 'lumen-8',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency'   => 'USD',
                'unit'       => 'per_1m_tokens',
                'rates'      => [
                    'input'       => '4.000000',
                    'output'      => '16.000000',
                    'cache_read'  => '0.400000',
                    'cache_write' => '5.000000',
                ],
            ]],
        ], JSON_PRETTY_PRINT));

        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:billing:reprice %s',
            $qrepo, escapeshellarg($fixFile)
        ));
        $this->assertSame(0, $code, 'Reprice failed: ' . $out);
        $this->assertStringContainsString('REPRICED_EVENTS=1', $out);
        $this->assertStringContainsString('REPRICE_DELTA_USD=', $out);

        // 8. Report after reprice (19.016665)
        [$code, $out] = $this->exec(sprintf(
            'cd %s && php bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z',
            $qrepo
        ));
        $this->assertSame(0, $code, 'Post-reprice report failed: ' . $out);
        $this->assertStringContainsString('19.016665', $out);

        @unlink($fixFile);
    }
}
