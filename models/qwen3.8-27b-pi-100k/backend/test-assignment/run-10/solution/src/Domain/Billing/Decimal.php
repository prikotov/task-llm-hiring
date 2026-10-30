<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Точное десятичное число: рациональная величина p / 10^k над целыми,
 * арифметика — bcmath (float для денег запрещён).
 *
 * Хранимые/отчётные денежные значения — ровно 6 знаков после точки.
 * Единственное округление в домене — HALF_UP до 6 знаков при оформлении
 * итерговой стоимости события.
 */
final class Decimal
{
    private string $p;
    private int $k;
    private bool $negative;

    private function __construct(string $p, int $k, bool $negative)
    {
        $this->p = $p;
        $this->k = $k;
        $this->negative = $negative && $p !== '0';
    }

    public static function fromInt(int $i): self
    {
        return new self((string) abs($i), 0, $i < 0);
    }

    public static function fromString(string $s): self
    {
        $s = trim($s);

        if (!preg_match('/^[+-]?(?:\d+|0\.\d+|\d+\.\d*)$/', $s)) {
            throw new \InvalidArgumentException(sprintf('Нераспознанное десятичное число: "%s".', $s));
        }

        $negative = $s[0] === '-';
        $s = preg_replace('/^[+-]/', '', $s) ?? '';

        [$intPart, $fracPart] = array_pad(explode('.', $s, 2), 2, '');
        $digits = ltrim($intPart . $fracPart, '0');
        $p = $digits === '' ? '0' : $digits;

        return new self($p, strlen($fracPart), $negative);
    }

    /**
     * Число вида p / 10^k, делённое на целый знаменатель $den;
     * остаточное расширение точности — не более 12 дополнительных знаков,
     * сверх того — ошибка (в домене знаменатели конечны и малы).
     */
    public static function exactDivide(string $p, int $k, int $den): self
    {
        if ($den === 0) {
            throw new \DivisionByZeroError('Деление на ноль запрещено.');
        }

        foreach (range(0, 11) as $i) {
            $r = (string) bcmod($p, (string) $den, 0);
            if ($r === '0') {
                $q = (string) bcdiv($p, (string) $den, 0);

                return new self($q, $k, false);
            }
            $p .= '0';
            $k++;
        }

        throw new \RangeException('Знаменатель не делит значение — арифметическая ошибка.');
    }

    public function isZero(): bool
    {
        return $this->p === '0';
    }

    /**
     * Разложение p / 10^k.
     *
     * @return array{p: string, k: int, negative: bool}
     */
    public function parts(): array
    {
        return ['p' => $this->p, 'k' => $this->k, 'negative' => $this->negative];
    }

    /**
     * Отрицание.
     */
    public function negate(): self
    {
        return new self($this->p, $this->k, !$this->negative);
    }

    /**
     * Сложение, без округления.
     */
    public function add(self $o): self
    {
        if ($this->isZero()) {
            return clone $o;
        }
        if ($o->isZero()) {
            return clone $this;
        }

        if ($this->negative === $o->negative) {
            $k = max($this->k, $o->k);
            $lp = $this->p . str_repeat('0', $k - $this->k);
            $rp = $o->p . str_repeat('0', $k - $o->k);
            $p = (string) bcadd($lp, $rp, 0);

            return new self($p, $k, $this->negative);
        }

        $k = max($this->k, $o->k);
        $lp = $this->p . str_repeat('0', $k - $this->k);
        $rp = $o->p . str_repeat('0', $k - $o->k);
        $c = bccomp($lp, $rp, 0);

        if ($c === 0) {
            return new self('0', 0, false);
        }

        if ($c > 0) {
            return new self((string) bcsub($lp, $rp, 0), $k, $this->negative);
        }

        return new self((string) bcsub($rp, $lp, 0), $k, $o->negative);
    }

    /**
     * Умножение, без округления.
     */
    public function multiply(self $o): self
    {
        if ($this->isZero() || $o->isZero()) {
            return new self('0', 0, false);
        }

        $p = (string) bcmul($this->p, $o->p, 0);

        return new self($p, $this->k + $o->k, $this->negative !== $o->negative);
    }

    /**
     * Разность: $this - $o.
     */
    public function subtract(self $o): self
    {
        return $this->add($o->negate());
    }

    /**
     * ЕДИНСТВЕННОЕ округление в домене: HALF_UP ровно до 6 знаков после точки.
     */
    public function roundHalfUp6(): self
    {
        return $this->roundToScale(6);
    }

    private function roundToScale(int $scale): self
    {
        if ($this->k <= $scale) {
            return clone $this;
        }

        $drop = $this->k - $scale;
        $div = '1' . str_repeat('0', $drop);

        $qRaw = (string) bcdiv($this->p, $div, 0);
        $r = (string) bcmod($this->p, $div, 0);
        $r2 = (string) bcmul($r, '2', 0);
        $carry = bccomp($r2, $div, 0) >= 0 ? '1' : '0';
        $q = (string) bcadd($qRaw, $carry, 0);

        return new self($q, $scale, $this->negative);
    }

    /**
     * Строка с ровно $scale знаками после точки.
     */
    public function toFixed(int $scale): string
    {
        $v = $this->k > $scale ? $this->roundToScale($scale) : $this;

        $s = str_pad($v->p, $v->k + 1, '0', STR_PAD_LEFT);
        $int = $v->k > 0 ? substr($s, 0, -$v->k) : $s;
        $frac = $v->k > 0 ? substr($s, strlen($s) - $v->k) : '';
        $frac = str_pad($frac, $scale, '0', STR_PAD_RIGHT);

        $int = ltrim($int, '0');
        if ($int === '') {
            $int = '0';
        }

        $out = ($v->negative ? '-' : '') . $int;
        if ($scale > 0) {
            $out .= '.' . $frac;
        }

        return $out;
    }

    public function __toString(): string
    {
        return $this->toFixed(max($this->k, 6));
    }
}
