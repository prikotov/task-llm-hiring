<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Ошибки импорта: некорректный ввод прерывает весь импорт
 * (не-zero-код, в базу ничего не попадает).
 */
final class ImportErrorsTest extends IntegrationTestCase
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

    private function writeTemp(string $name, array $event): string
    {
        $path = sys_get_temp_dir().'/billing_it_'.preg_replace('/\W/', '_', $name).'.jsonl';
        file_put_contents($path, json_encode($event, JSON_THROW_ON_ERROR)."\n");

        return $path;
    }

    private function baseEvent(array $over = []): array
    {
        return array_merge([
            'provider' => 'p',
            'provider_request_id' => 'a',
            'model' => 'm',
            'user' => 'u',
            'ts' => '2026-09-01T10:00:00Z',
            'tokens_input' => 1,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ], $over);
    }

    public function testBadUsageLineAbortsWholeFile(): void
    {
        $good = $this->baseEvent(['provider_request_id' => 'a']);
        $bad = $this->baseEvent(['provider_request_id' => 'b', 'tokens_input' => 'NAN']);

        $path = sys_get_temp_dir().'/billing_it_bad_usage.jsonl';
        file_put_contents($path, json_encode($good, JSON_THROW_ON_ERROR)."\n".json_encode($bad, JSON_THROW_ON_ERROR)."\n");

        $r = $this->exec('app:usage:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
        self::assertSame(
            '0',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM usage_events'),
        );
    }

    public function testInvalidTimestampIsRejected(): void
    {
        $path = $this->writeTemp('bad_ts', $this->baseEvent(['ts' => '2026-09-01T10:00:00+02:00']));
        $r = $this->exec('app:usage:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
        self::assertStringContainsString('ts', $r['output']);
    }

    public function testNegativeTokensAreRejected(): void
    {
        $path = $this->writeTemp('neg_tokens', $this->baseEvent(['tokens_output' => -5]));
        $r = $this->exec('app:usage:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
    }

    public function testMissingFieldIsRejected(): void
    {
        $event = $this->baseEvent();
        unset($event['provider_request_id']);
        $path = $this->writeTemp('missing_field', $event);
        $r = $this->exec('app:usage:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
    }

    public function testNegativePriceIsRejected(): void
    {
        $doc = [
            'providers' => [['code' => 'neg', 'name' => 'Neg', 'off_peak' => null]],
            'models' => [['code' => 'n-1', 'provider' => 'neg']],
            'prices' => [[
                'provider' => 'neg', 'model' => 'n-1',
                'valid_from' => '2026-01-01T00:00:00Z',
                'currency' => 'USD', 'unit' => 'per_1m_tokens_unified',
                'rates' => ['any' => '-1.000000'],
            ]],
        ];
        $path = sys_get_temp_dir().'/billing_it_neg_price.json';
        file_put_contents($path, json_encode($doc, JSON_THROW_ON_ERROR));

        $r = $this->exec('app:prices:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
        self::assertSame(
            '0',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM price_rows'),
        );
    }

    public function testBrokenJsonIsRejected(): void
    {
        $path = sys_get_temp_dir().'/billing_it_broken.json';
        file_put_contents($path, '{ invalid json ]');

        $r = $this->exec('app:prices:import', ['file' => $path]);
        self::assertNotSame(0, $r['status']);
    }

    public function testPricesImportIsUpsertByNaturalKey(): void
    {
        self::assertSame(
            0,
            $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status'],
        );
        self::assertSame(
            '4',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM price_rows'),
        );

        // Повторный импорт того же файла — строки не дублируются
        self::assertSame(
            0,
            $this->exec('app:prices:import', ['file' => $this->fixture('prices.json')])['status'],
        );
        self::assertSame(
            '4',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM price_rows'),
        );

        // Изменение тарифа по тому же (provider, model, valid_from) — замена,
        // а не новая строка; новая valid_from — добавление
        $changed = [
            'providers' => [['code' => 'prism', 'name' => 'Prism', 'off_peak' => null]],
            'models' => [['code' => 'lumen-8', 'provider' => 'prism']],
            'prices' => [
                [
                    'provider' => 'prism', 'model' => 'lumen-8',
                    'valid_from' => '2026-08-01T00:00:00Z',
                    'currency' => 'USD', 'unit' => 'per_1m_tokens',
                    'rates' => [
                        'input' => '9.990000', 'output' => '1.000000',
                        'cache_read' => '0.100000', 'cache_write' => '0.200000',
                    ],
                ],
                [
                    'provider' => 'prism', 'model' => 'lumen-8',
                    'valid_from' => '2026-09-20T00:00:00Z',
                    'currency' => 'USD', 'unit' => 'per_1m_tokens',
                    'rates' => [
                        'input' => '1.500000', 'output' => '8.000000',
                        'cache_read' => '0.1500000', 'cache_write' => '1.875000',
                    ],
                ],
            ],
        ];
        $path = sys_get_temp_dir().'/billing_it_modified_prices.json';
        file_put_contents($path, json_encode($changed, JSON_THROW_ON_ERROR));

        $r = $this->exec('app:prices:import', ['file' => $path]);
        self::assertSame(0, $r['status']);
        self::assertSame(
            '5',
            (string) $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM price_rows'),
        );
    }
}
