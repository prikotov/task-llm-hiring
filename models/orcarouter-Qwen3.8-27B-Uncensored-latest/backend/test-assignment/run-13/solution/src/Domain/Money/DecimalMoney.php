<?php

declare(strict_types=1);

namespace App\Domain\Money;

/**
 * Immutable decimal amount backed by exact BCMath arithmetic.
 *
 * Invariants:
 *  - float is never used anywhere in money paths;
 *  - add()/subtract()/multiply()/multiplyByInteger()/divideByInteger() are
 *    exact for domain input sizes (rates and multipliers have ≤ 6
 *    fractional digits, token counts and their sums are ≤ 18-digit
 *    integers);
 *  - the single allowed rounding step is roundTo(): HALF_UP (2.5 → 3) to
 *    ROUND_SCALE = 6 digits, applied once to the exact per-event cost.
 */
final class DecimalMoney
{
    /** The one allowed rounding scale of the domain. */
    public const ROUND_SCALE = 6;

    /** Working scale for intermediates — ample headroom. */
    private const WORK_SCALE = 24;

    /** @var string canonical decimal value ("0", "14.26249875", "-3" ...) */
    private readonly string $value;

    private function __construct(string $value)
    {
        $this->value = self::canonical($value);
    }

    public static function fromDecimalString(string $value): self
    {
        $value = trim($value);
        if (preg_match('/^-?(0|[1-9]\d*)(\.\d+)?$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid decimal value: "%s"', $value));
        }

        return new self($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function isZero(): bool
    {
        return self::valueIsZero($this->value);
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-') && !self::valueIsZero($this->value);
    }

    public function add(self $other): self
    {
        return new self(self::sum($this->value, $other->value));
    }

    public function subtract(self $other): self
    {
        return new self(self::sum($this->value, self::negate($other->value)));
    }

    /** Exact product. */
    public function multiply(self $other): self
    {
        return new self(self::mul($this->value, $other->value));
    }

    /** Exact: multiplying a decimal by an integer adds no fractional digits. */
    public function multiplyByInteger(int $factor): self
    {
        return new self(self::mul($this->value, (string) $factor));
    }

    /** Exact division by a non-zero integer (domain: "tokens·rate" ÷ 1 000 000). */
    public function divideByInteger(int $divisor): self
    {
        if ($divisor === 0) {
            throw new \DivisionByZeroError('Division by zero.');
        }

        return new self(self::divInt($this->value, $divisor));
    }

    /**
     * Round to $scale (≤ ROUND_SCALE) fractional digits, HALF_UP (2.5 → 3).
     * Pure digit arithmetic on the exact value — nothing is truncated before
     * the rounding decision.
     */
    public function roundTo(int $scale = self::ROUND_SCALE): self
    {
        if ($scale < 0 || $scale > self::ROUND_SCALE) {
            throw new \InvalidArgumentException('Scale out of range.');
        }

        $sign = str_starts_with($this->value, '-') ? '-' : '';
        $mag = ltrim($this->value, '-');
        $dot = strpos($mag, '.');
        $int = $dot === false ? $mag : substr($mag, 0, $dot);
        $frac = $dot === false ? '' : substr($mag, $dot + 1);
        // value is canonical: int has no leading zeros (unless "0"), frac has no trailing zeros.
        $D = $int . $frac;
        $p = strlen($frac);

        if (bccomp($D, '0', 0) === 0) {
            return self::zero();
        }

        if ($p >= $scale) {
            $drop = $p - $scale;
            $keepLen = strlen($D) - $drop;
            $keepCandidate = substr($D, 0, $keepLen);
            $tail = substr($D, $keepLen);
            $roundUp = false;
            if ($drop > 0) {
                $threshold = '5' . str_repeat('0', $drop - 1);
                $roundUp = self::cmpNum($tail, $threshold) >= 0;
            }
            $keep = $roundUp ? self::inc($keepCandidate) : $keepCandidate;
        } else {
            // exact, no rounding digit to consider
            $keep = $D . str_repeat('0', $scale - $p);
        }

        // Format `keep` (an integer representing value × 10^scale) as scale fractional digits.
        $keep = ltrim($keep, '0');
        if ($keep === '') {
            return self::zero();
        }
        $len = strlen($keep);
        if ($len <= $scale) {
            $intPart = '0';
            $fracPart = str_pad($keep, $scale, '0', STR_PAD_LEFT);
        } else {
            $intPart = substr($keep, 0, $len - $scale);
            $fracPart = substr($keep, $len - $scale);
        }
        if ($intPart === '0' && ltrim($fracPart, '0') === '') {
            return self::zero();
        }

        return new self($sign . $intPart . '.' . $fracPart);
    }

    private static function cmpNum(string $a, string $b): int
    {
        $a = ltrim($a, '0');
        $b = ltrim($b, '0');
        if (strlen($a) !== strlen($b)) {
            return strlen($a) <=> strlen($b);
        }

        return strcmp($a, $b);
    }

    /** Increment a decimal digit string (may have leading zeros) by 1, preserving length. */
    private static function inc(string $s): string
    {
        $chars = str_split($s);
        $i = count($chars) - 1;
        $carry = 1;
        $overflow = false;
        while ($i >= 0 && $carry) {
            $d = (int) $chars[$i] + $carry;
            $chars[$i] = (string) ($d % 10);
            $carry = intdiv($d, 10);
            $i--;
        }
        if ($carry) {
            $overflow = true;
        }
        $result = implode('', $chars);
        if ($overflow) {
            $result = '1' . $result;
        }

        return $result;
    }

    public function abs(): self
    {
        return new self(ltrim($this->value, '-'));
    }

    /** Exact value in canonical form, e.g. "14.26249875". */
    public function toBcString(): string
    {
        return $this->value;
    }

    /** Render with exactly $scale digits after the point, e.g. "14.262499". */
    public function format(int $scale = self::ROUND_SCALE): string
    {
        if ($scale < 0 || $scale > self::ROUND_SCALE) {
            throw new \InvalidArgumentException('Scale out of range.');
        }
        $rounded = $this->roundTo($scale)->value;
        if (self::valueIsZero($rounded)) {
            return '0.' . str_repeat('0', $scale);
        }
        $sign = str_starts_with($rounded, '-') ? '-' : '';
        $mag = ltrim($rounded, '-');
        $dot = strpos($mag, '.');
        $int = $dot === false ? $mag : substr($mag, 0, $dot);
        $frac = $dot === false ? '' : substr($mag, $dot + 1);
        $int = ltrim($int, '0');
        if ($int === '') {
            $int = '0';
        }

        return $sign . $int . '.' . str_pad($frac, $scale, '0', STR_PAD_RIGHT);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    // ------------------------------------------------------------------

    private static function valueIsZero(string $v): bool
    {
        $t = ltrim($v, '-');

        return ltrim(rtrim($t, '0'), '.') === '';
    }

    private static function negate(string $v): string
    {
        if (self::valueIsZero($v)) {
            return '0';
        }

        return str_starts_with($v, '-') ? substr($v, 1) : '-' . $v;
    }

    /** Canonical form: "0", "14.26249875", "-3" (no padding zeros). */
    private static function canonical(string $v): string
    {
        $v = trim($v);
        if ($v === '' || $v === '-' || $v === '.') {
            return '0';
        }
        $sign = str_starts_with($v, '-') ? '-' : '';
        $v = ltrim($v, '-');
        if (str_contains($v, '.')) {
            $p = strpos($v, '.');
            $int = substr($v, 0, $p);
            $frac = substr($v, $p + 1);
        } else {
            $int = $v;
            $frac = '';
        }
        $int = ltrim($int, '0');
        $frac = rtrim($frac, '0');
        if ($int === '' && $frac === '') {
            return '0';
        }
        $int = $int === '' ? '0' : $int;
        if ($int === '0' && $frac === '') {
            return '0';
        }

        return $sign . $int . ($frac !== '' ? '.' . $frac : '');
    }

    /** @return array{0:string, 1:string, 2:string} [sign, int digits, frac digits] */
    private static function parts(string $v): array
    {
        if (self::valueIsZero($v)) {
            return ['', '0', ''];
        }
        $sign = str_starts_with($v, '-') ? '-' : '';
        $v = ltrim($v, '-');
        $dot = strpos($v, '.');
        if ($dot === false) {
            return [$sign, $v, ''];
        }

        return [$sign, substr($v, 0, $dot), substr($v, $dot + 1)];
    }

    /** $a + $b — exact, arbitrary size. */
    private static function sum(string $a, string $b): string
    {
        if (self::valueIsZero($a)) {
            return self::canonical($b);
        }
        if (self::valueIsZero($b)) {
            return self::canonical($a);
        }

        [$signA, $intA, $fracA] = self::parts($a);
        [$signB, $intB, $fracB] = self::parts($b);

        if ($signA !== $signB) {
            $c = self::cmp($intA, $fracA, $intB, $fracB);
            if ($c === 0) {
                return '0';
            }
            $result = $c > 0
                ? self::subMag($intA, $fracA, $intB, $fracB)
                : self::subMag($intB, $fracB, $intA, $fracA);

            return self::canonical(($result !== '0' ? ($c > 0 ? $signA : $signB) : '') . $result);
        }

        $len = max(strlen($fracA), strlen($fracB));
        $fA = str_pad($fracA, $len, '0', STR_PAD_RIGHT);
        $fB = str_pad($fracB, $len, '0', STR_PAD_RIGHT);
        $sumFrac = bcadd($fA, $fB, 0);
        $carry = (strlen($sumFrac) > $len) ? (int) $sumFrac[0] : 0;
        $sumFrac = (strlen($sumFrac) > $len) ? substr($sumFrac, 1) : $sumFrac;
        $sumInt = bcadd($intA, $intB, 0);
        if ($carry > 0) {
            $sumInt = bcadd($sumInt, (string) $carry, 0);
        }
        $sumInt = ltrim($sumInt, '0');
        if ($sumInt === '') {
            $sumInt = '0';
        }
        $sumFrac = rtrim($sumFrac, '0');

        return self::canonical(($signA !== '' && $sumInt !== '0' ? '-' : '') . $sumInt . ($sumFrac !== '' ? '.' . $sumFrac : ''));
    }

    /** $bigger − $smaller (bigger ≥ smaller in magnitude), returns magnitude. */
    private static function subMag(string $bigInt, string $bigFrac, string $smlInt, string $smlFrac): string
    {
        $len = max(strlen($bigFrac), strlen($smlFrac));
        $fBig = str_pad($bigFrac, $len, '0', STR_PAD_RIGHT);
        $fSml = str_pad($smlFrac, $len, '0', STR_PAD_RIGHT);
        $borrow = bccomp($fBig, $fSml, 0) < 0;
        if ($borrow) {
            // (10^len + fBig) - fSml  — both are integers of exactly $len digits
            $fRes = bcsub('1' . $fBig, $fSml, 0);
            $iRes = bcsub($bigInt, $smlInt, 0);
            $iRes = bcsub($iRes, '1', 0);
        } else {
            $fRes = bcsub($fBig, $fSml, 0);
            $iRes = bcsub($bigInt, $smlInt, 0);
        }
        $iRes = ltrim((string) $iRes, '-');
        $iRes = ltrim($iRes, '0');
        $fRes = rtrim((string) $fRes, '0');
        if ($iRes === '' || $iRes === '0') {
            $iRes = '0';
        }
        if ($iRes === '0' && ($fRes === '' || ltrim($fRes, '0') === '')) {
            return '0';
        }

        return $iRes . ($fRes !== '' ? '.' . $fRes : '');
    }

    private static function cmp(string $intA, string $fracA, string $intB, string $fracB): int
    {
        $len = max(strlen($fracA), strlen($fracB));
        $a = ltrim($intA, '0') . str_pad($fracA, $len, '0', STR_PAD_RIGHT);
        $b = ltrim($intB, '0') . str_pad($fracB, $len, '0', STR_PAD_RIGHT);
        if (strlen($a) !== strlen($b)) {
            return strlen($a) <=> strlen($b);
        }

        return strcasecmp($a, $b);
    }

    /** $a × $b — exact, arbitrary size. */
    private static function mul(string $a, string $b): string
    {
        if (self::valueIsZero($a) || self::valueIsZero($b)) {
            return '0';
        }

        $sign = (str_starts_with($a, '-') !== str_starts_with($b, '-')) ? '-' : '';
        $vA = ltrim($a, '-');
        $vB = ltrim($b, '-');
        $dotA = strpos($vA, '.');
        $dotB = strpos($vB, '.');
        $intA = $dotA === false ? $vA : substr($vA, 0, $dotA);
        $fracA = $dotA === false ? '' : substr($vA, $dotA + 1);
        $intB = $dotB === false ? $vB : substr($vB, 0, $dotB);
        $fracB = $dotB === false ? '' : substr($vB, $dotB + 1);
        $totalFrac = strlen($fracA) + strlen($fracB);

        $digits = bcmul(ltrim($intA . $fracA, '0') ?: '0', ltrim($intB . $fracB, '0') ?: '0', 0);
        $digits = str_pad($digits, $totalFrac + 1, '0', STR_PAD_LEFT);
        $len = strlen($digits);
        $int = $totalFrac === 0 ? $digits : substr($digits, 0, max(1, $len - $totalFrac));
        $frac = $totalFrac === 0 ? '' : substr($digits, $len - $totalFrac);
        $int = ltrim($int, '0');
        $frac = rtrim($frac, '0');
        if ($int === '') {
            $int = '0';
        }
        if ($int === '0' && $frac === '') {
            return '0';
        }

        return self::canonical($sign . $int . ($frac !== '' ? '.' . $frac : ''));
    }

    /**
     * $a ÷ $n (n ≠ 0), exact where the quotient is a terminating decimal with
     * ≤ WORK_SCALE fractional digits — the only case the domain needs
     * ("tokens · rate" ÷ 1 000 000).
     */
    private static function divInt(string $a, int $n): string
    {
        $neg = str_starts_with($a, '-');
        $mag = ltrim($a, '-');
        if (self::valueIsZero($mag)) {
            return '0';
        }
        if ($n === 1) {
            return self::canonical(($neg ? '-' : '') . $mag);
        }

        $dot = strpos($mag, '.');
        $int = $dot === false ? $mag : substr($mag, 0, $dot);
        $frac = $dot === false ? '' : substr($mag, $dot + 1);

        $scale = max(self::WORK_SCALE, strlen($frac));
        $scaled = ltrim($int . $frac . str_repeat('0', $scale - strlen($frac)), '0');
        if ($scaled === '') {
            return '0';
        }
        $ns = (string) abs($n);
        $q = bcdiv($scaled, $ns, 0);
        $r = bcmod($scaled, $ns);
        if ($r !== '0') {
            throw new \DivisionByZeroError('Non-exact division (not expected in this domain): ' . $a . ' / ' . $n);
        }

        $q = str_pad($q, $scale + 1, '0', STR_PAD_LEFT);
        $len = strlen($q);
        $intPart = substr($q, 0, $len - $scale);
        $fracPart = substr($q, $len - $scale);
        $intPart = ltrim($intPart, '0');
        $fracPart = rtrim($fracPart, '0');
        if ($intPart === '') {
            $intPart = '0';
        }
        if ($intPart === '0' && $fracPart === '') {
            return '0';
        }

        return self::canonical(($neg ? '-' : '') . $intPart . ($fracPart !== '' ? '.' . $fracPart : ''));
    }
}
