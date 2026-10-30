<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Provider;
use PHPUnit\Framework\TestCase;

/**
 * Тесты логики оф-пикового окна провайдера.
 *
 * Оконные правила (TASK.md 4):
 *   [start, end) — включительно в start, исключительно в end;
 *   окно может переходить полночь.
 */
final class OffPeakWindowTest extends TestCase
{
    private function provider(int $start, int $end): Provider
    {
        $p = new Provider('prism', 'Prism');
        $p->setOffPeak($start, $end, '0.5');

        return $p;
    }

    public function testWindowWithinDay(): void
    {
        $p = $this->provider(720, 900); // 12:00..15:00
        self::assertTrue($p->isOffPeak(720));
        self::assertTrue($p->isOffPeak(840));
        self::assertFalse($p->isOffPeak(900));
        self::assertFalse($p->isOffPeak(360));
        self::assertFalse($p->isOffPeak(1139));
    }

    public function testWrapAroundWindow(): void
    {
        $p = $this->provider(990, 30); // 16:30..00:30
        self::assertTrue($p->isOffPeak(990));
        self::assertTrue($p->isOffPeak(1079));
        self::assertTrue($p->isOffPeak(1380));
        self::assertTrue($p->isOffPeak(0));
        self::assertTrue($p->isOffPeak(29));
        self::assertFalse($p->isOffPeak(30));
        self::assertFalse($p->isOffPeak(600));
    }

    public function testNoOffPeak(): void
    {
        $p = new Provider('prism', 'Prism');
        self::assertFalse($p->isOffPeak(0));
        self::assertFalse($p->isOffPeak(1439));
    }
}
