<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Billing\PriceRowSelector;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PriceRowSelectorTest extends TestCase
{
    /**
     * Строки lumen-8 из fixtures/prices.json + одна будущая (2026-09-20).
     *
     * @return list<PriceRow>
     */
    private function rows(): array
    {
        $provider = new Provider('prism', 'Prism AI');
        $model = new Model($provider, 'lumen-8');
        $mk = static fn (string $validFrom, string $input, string $output): PriceRow => new PriceRow(
            $provider,
            $model,
            new DateTimeImmutable($validFrom . 'Z', new \DateTimeZone('UTC')),
            'USD',
            \App\Domain\Unit::PerToken,
            ['input' => $input, 'output' => $output, 'cache_read' => '0.3', 'cache_write' => '3.75'],
        );

        // сознательно вперемешку, порядок списка не должен влиять на выбор
        return [
            $mk('2026-09-20T00:00:00', '1.0', '9.0'),
            $mk('2026-08-01T00:00:00', '3', '12'),
            $mk('2026-09-15T12:00:00', '1.5', '8'),
        ];
    }

    private function ts(string $iso): DateTimeImmutable
    {
        return new DateTimeImmutable($iso, new \DateTimeZone('UTC'));
    }

    public function testSelectsNewestRowNotLaterThanEventTs(): void
    {
        $selector = new PriceRowSelector();
        $rows = $this->rows();

        // до 09-15 12:00 действует строка 08-01
        self::assertSame('3', $selector->select($rows, $this->ts('2026-09-15T11:59:59'))->getInputRate());
        // 09-15T12:00:00 — граница включительно: уже новая строка (сценарий e2)
        self::assertSame('1.5', $selector->select($rows, $this->ts('2026-09-15T12:00:00'))->getInputRate());
        self::assertSame('1.5', $selector->select($rows, $this->ts('2026-09-16T00:00:00'))->getInputRate());
        // строка 09-20 действует с момента своей valid_from (включительно)
        self::assertSame('1.0', $selector->select($rows, $this->ts('2026-09-20T00:00:00'))->getInputRate());
        self::assertSame('1.0', $selector->select($rows, $this->ts('2026-10-01T00:00:00'))->getInputRate());
    }

    public function testNoRowBeforeFirstValidFrom(): void
    {
        $selector = new PriceRowSelector();
        self::assertNull($selector->select($this->rows(), $this->ts('2026-07-31T23:59:59')));
    }

    public function testEmptyList(): void
    {
        self::assertNull((new PriceRowSelector())->select([], $this->ts('2026-09-15T00:00:00')));
    }

    private function rowByValidFrom(array $rows, string $iso): PriceRow
    {
        foreach ($rows as $row) {
            if ($row->getValidFrom()->getTimestamp() === $this->ts($iso . 'Z')->getTimestamp()) {
                return $row;
            }
        }

        throw new \LogicException('row not found: ' . $iso);
    }
}
