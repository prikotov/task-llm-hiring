<?php

declare(strict_types=1);

namespace App\Tests;

use App\Import\ImportException;
use App\Import\PriceFileParser;
use PHPUnit\Framework\TestCase;

final class PriceFileParserTest extends TestCase
{
    private PriceFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PriceFileParser();
    }

    public function testFixtureFileParses(): void
    {
        $file = $this->parser->parseFile(dirname(__DIR__).'/fixtures/prices.json');

        self::assertCount(2, $file->getProviders());
        self::assertCount(3, $file->getModels());
        self::assertCount(4, $file->getPrices());
        self::assertNotNull($file->findProvider('prism'));
        self::assertNotNull($file->findModel('prism', 'lumen-8'));
    }

    private function validData(): array
    {
        return [
            'providers' => [
                ['code' => 'p1', 'name' => 'P1', 'off_peak' => null],
            ],
            'models' => [
                ['provider' => 'p1', 'code' => 'm1'],
            ],
            'prices' => [
                [
                    'provider' => 'p1',
                    'model' => 'm1',
                    'valid_from' => '2026-08-01T00:00:00Z',
                    'currency' => 'USD',
                    'unit' => 'per_1m_tokens',
                    'rates' => ['input' => '1.5', 'output' => '2.5', 'cache_read' => '0.1', 'cache_write' => '1.0'],
                ],
            ],
        ];
    }

    public function testValidDataParses(): void
    {
        $file = $this->parser->parseData($this->validData());
        self::assertCount(1, $file->getPrices());
        self::assertSame('1.5', $file->getPrices()[0]->getRates()['input']);
    }

    /** Контракт: mutator принимает и возвращает массив данных. */
    private function assertRejected(callable $mutate, string $substring): void
    {
        $data = $mutate($this->validData());

        $caught = null;
        try {
            $this->parser->parseData($data);
        } catch (ImportException $e) {
            $caught = $e;
        }

        self::assertNotNull($caught, 'ожидалась ImportException (фрагмент "'.$substring.'"); изменения: '.json_encode($data, JSON_UNESCAPED_UNICODE));
        self::assertStringContainsString($substring, (string) $caught);
    }

    public function testMissingModelsSection(): void
    {
        $this->assertRejected(function (array $d): array {
            unset($d['models']);

            return $d;
        }, 'models');
    }

    public function testUnknownUnit(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['unit'] = 'split';

            return $d;
        }, '"split"');
    }

    public function testUnknownUnitMessage(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['unit'] = 'split';

            return $d;
        }, 'unit');
    }

    public function testModelNotListed(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['model'] = 'nope';

            return $d;
        }, 'p1/nope');
    }

    public function testProviderNotListedInModels(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['models'][0]['provider'] = 'ghost';

            return $d;
        }, 'ghost');
    }

    public function testNegativeRate(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['rates']['input'] = '-1.5';

            return $d;
        }, 'input');
    }

    public function testRateAsNumber(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['rates']['input'] = 1.5;

            return $d;
        }, 'input');
    }

    public function testMissingRateKey(): void
    {
        $this->assertRejected(function (array $d): array {
            unset($d['prices'][0]['rates']['cache_write']);

            return $d;
        }, 'cache_write');
    }

    public function testExtraRateKey(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['rates']['bonus'] = '1';

            return $d;
        }, 'bonus');
    }

    public function testBadValidFrom(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['valid_from'] = '2026-08-01 00:00:00';

            return $d;
        }, 'valid_from');
    }

    public function testBadCurrency(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['prices'][0]['currency'] = 'EUR';

            return $d;
        }, 'USD');
    }

    public function testOffPeakMultiplierZero(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['providers'][0]['off_peak'] = ['window_start' => '16:30', 'window_end' => '00:30', 'multiplier' => '0'];

            return $d;
        }, 'multiplier');
    }

    public function testOffPeakBadTime(): void
    {
        $this->assertRejected(function (array $d): array {
            $d['providers'][0]['off_peak'] = ['window_start' => '25:00', 'window_end' => '00:30', 'multiplier' => '0.5'];

            return $d;
        }, '25:00');
    }

    public function testDuplicatePriceSameKeyLastWins(): void
    {
        $data = $this->validData();
        $dup = $data['prices'][0];
        $dup['rates']['input'] = '9.9';
        $data['prices'][] = $dup;

        $file = $this->parser->parseData($data);
        self::assertCount(1, $file->getPrices());
        self::assertSame('9.9', $file->getPrices()[0]->getRates()['input']);
    }
}
