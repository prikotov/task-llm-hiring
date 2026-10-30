<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * End-to-end smoke test: full pipeline against a clean DB.
 */
final class PipelineSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        exec('PGPASSWORD=app psql -h 127.0.0.1 -p 5544 -U app -d app -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public; GRANT ALL ON SCHEMA public TO public;" 2>/dev/null');
    }

    public function testPipeline(): void
    {
        $dir = dirname(__DIR__);

        $this->execConsole('app:db:setup', $dir, 0);
        $this->execConsole('app:prices:import fixtures/prices.json', $dir, 0);
        $this->execConsole('app:usage:import fixtures/usage.jsonl', $dir, 0);

        $out = $this->execConsole('app:billing:rate', $dir, 0);
        $this->assertStringContainsString('RATED=3 UNRATED=0', $out);

        $stdout = $this->execConsole('app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z', $dir, 0);
        $expected = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
            ."u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
            ."u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
            ."u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
            ."TOTAL,,6200000,2300000,2500000,83333,33.662499";
        $this->assertSame($expected, trim($stdout), "report:\n{$stdout}");

        // idempotency
        $this->execConsole('app:prices:import fixtures/prices.json', $dir, 0);
        $this->execConsole('app:usage:import fixtures/usage.jsonl', $dir, 0);
        $this->execConsole('app:billing:rate', $dir, 0);
        $stdout2 = $this->execConsole('app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z', $dir, 0);
        $this->assertSame($expected, trim($stdout2), 'idempotency');
    }

    private function execConsole(string $args, string $dir, int $expectedRc): string
    {
        $scriptFile = tempnam(sys_get_temp_dir(), 'arg');
        file_put_contents($scriptFile, $args);
        $command = sprintf(
            'cd %s && DATABASE_URL=%s APP_SECRET=%s APP_ENV=%s /usr/bin/env php bin/console $(cat %s) 2>&1',
            escapeshellarg($dir),
            escapeshellarg('postgresql://app:app@127.0.0.1:5544/app?serverVersion=17&charset=utf8'),
            escapeshellarg('devsecret'),
            escapeshellarg('dev'),
            escapeshellarg($scriptFile),
        );
        $output = [];
        exec($command, $output, $rc);
        @unlink($scriptFile);
        $this->assertSame($expectedRc, $rc, 'command failed: '.implode("\n", $output));

        return implode("\n", $output);
    }
}
