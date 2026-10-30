<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Повторная тарификация и дедупликация на уровне БД.
 */
final class ReprocessingTest extends IntegrationTestCase
{
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

    private function writeJsonl(string $name, array $events): string
    {
        $path = sys_get_temp_dir().'/billing_it_'.preg_replace('/\W/', '_', $name).'.jsonl';
        file_put_contents(
            $path,
            implode("\n", array_map(fn (array $e): string => json_encode($e, JSON_THROW_ON_ERROR), $events))."\n",
        );

        return $path;
    }

    public function testDuplicateNaturalKeyIsSkippedEvenAcrossFiles(): void
    {
        $line = [
            'provider' => 'prism',
            'provider_request_id' => 'dup-1',
            'model' => 'lumen-8',
            'user' => 'u',
            'ts' => '2026-09-01T10:00:00Z',
            'tokens_input' => 5,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];

        self::assertSame(
            0,
            $this->exec('app:usage:import', ['file' => $this->writeJsonl('a', [$line])])['status'],
        );
        $r = $this->exec('app:usage:import', ['file' => $this->writeJsonl('b', [$line])]);
        self::assertSame(0, $r['status']);
        self::assertStringContainsString('inserted=0', $r['output']);
        self::assertStringContainsString('duplicates_skipped=1', $r['output']);
        self::assertSame(
            '1',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM usage_events'),
        );
    }

    public function testReportTimeWindowFilter(): void
    {
        self::assertSame(
            0,
            $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status'],
        );

        $mk = fn (string $id, string $ts): array => [
            'provider' => 'prism',
            'provider_request_id' => $id,
            'model' => 'lumen-8',
            'user' => 'u',
            'ts' => $ts,
            'tokens_input' => 1_000_000,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];

        $path = $this->writeJsonl('window', [
            $mk('e1', '2026-09-01T10:00:00Z'), // 1M * 3.0 = 3.0 (строка от 2026-08-01)
            $mk('e2', '2026-09-10T10:00:00Z'), // 3.0
            $mk('e3', '2026-09-20T10:00:00Z'), // 1.5 (строка от 2026-09-15T12)
        ]);
        self::assertSame(0, $this->exec('app:usage:import', ['file' => $path])['status']);
        $r = $this->exec('app:billing:rate');
        self::assertStringContainsString('RATED=3', $r['output']);

        // Интервал [from, to): e1 и e2 = 6.0
        $r = $this->exec('app:billing:report', [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-09-15T00:00:00Z',
        ]);
        self::assertStringContainsString('u,prism/lumen-8,2000000,0,0,0,6.000000', $r['output']);

        // to без from — ошибка использования
        $r = $this->exec('app:billing:report', ['--to' => '2026-09-15T00:00:00Z']);
        self::assertNotSame(0, $r['status']);
    }
}
