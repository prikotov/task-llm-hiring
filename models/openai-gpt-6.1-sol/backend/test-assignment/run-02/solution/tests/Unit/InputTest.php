<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Infrastructure\Input;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InputTest extends TestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }

    private function file(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'billing_input_');
        $this->files[] = $file;
        file_put_contents($file, $contents);

        return $file;
    }

    #[DataProvider('invalidDocuments')]
    public function testInvalidPriceDocument(string $json): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Input())->prices($this->file($json));
    }

    public static function invalidDocuments(): iterable
    {
        yield ['[]'];
        yield ['{}'];
        yield ['{"providers":{},"models":[],"prices":[]}'];
        yield ['{"providers":[{"code":"p","name":"Имя","off_peak":{"window_start":"25:00","window_end":"00:30","multiplier":"0.5"}}],"models":[],"prices":[]}'];
        yield ['{"providers":[{"code":"p","name":"Имя","off_peak":{"window_start":"16:30","window_end":"00:30","multiplier":0.5}}],"models":[],"prices":[]}'];
    }

    public function testMissingOffPeakMeansNoDiscount(): void
    {
        $document = (new Input())->prices($this->file('{"providers":[{"code":"p","name":"Провайдер"}],"models":[],"prices":[]}'));
        self::assertNull($document['providers']['p']['off_peak']);
    }

    public function testCorrectionMayOmitCatalog(): void
    {
        self::assertSame(['providers' => [], 'models' => [], 'prices' => []], (new Input())->prices($this->file('{"prices":[]}'), true));
    }

    public function testInvalidJsonIsAnError(): void
    {
        $this->expectException(\JsonException::class);
        (new Input())->prices($this->file('{'));
    }

    public function testEmptyLineIsAnInvalidJsonlRecordWithLineNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Строка 1:');
        iterator_to_array((new Input())->usage($this->file("\n")));
    }

    public function testMissingFileIsAnError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Input())->prices(__DIR__ . '/несуществующий.json');
    }
}
