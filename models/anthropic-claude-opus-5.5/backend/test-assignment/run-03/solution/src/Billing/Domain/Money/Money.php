<?php

declare(strict_types=1);

namespace App\Billing\Domain\Money;

/**
 * Точное десятичное число в виде строки с фиксированной шкалой.
 *
 * Внутри — только bcmath; никаких float на всём пути вычислений (см. TASK п. 4).
 * Отдельный тип "money" не вводится: одна и та же арифметика обслуживает
 * ставки за токены, стоимости и промежуточные произведения.
 */
final readonly class Money implements \Stringable
{
    /**
     * Шкала промежуточных вычислений. Ставка имеет 6 знаков, токены — целые,
     * множитель оф-пика — до 6 знаков; запас с большим избытком.
     */
    public const INTERNAL_SCALE = 20;

    /** Шкала денежного результата (TASK п. 4). */
    public const MONEY_SCALE = 6;

    public const ZERO = '0';

    private function __construct(
        private string $amount,
        private int $scale,
    ) {
    }

    public static function zero(int $scale = self::MONEY_SCALE): self
    {
        return new self(self::format(self::ZERO, $scale), $scale);
    }

    public static function fromInt(int $value, ?int $scale = null): self
    {
        return self::fromString((string) $value, $scale);
    }

    /**
     * Принимает десятичную строку ("3.75", "-0.5", "14.26249875").
     * Шкала по умолчанию равна числу знаков в записи — значение сохраняется
     * точно, без неявного округления. Экспоненциальная запись не допускается.
     * Если шкала задана явно, лишние значащие знаки — ошибка (скрытое округление).
     */
    public static function fromString(string $value, ?int $scale = null): self
    {
        $value = trim($value);

        if (1 !== preg_match('/^(?<sign>[+-]?)(?<int>\d+)(?:\.(?<frac>\d+))?$/', $value, $m)) {
            throw new MoneyException(sprintf('Некорректное десятичное число: "%s"', $value));
        }

        if (null !== $scale && $scale < 0) {
            throw new MoneyException('Шкала не может быть отрицательной');
        }

        $sign = '-' === $m['sign'] ? '-' : '';
        $integer = ltrim($m['int'], '0');
        $integer = '' === $integer ? '0' : $integer;
        $fraction = $m['frac'] ?? '';
        $scale ??= strlen($fraction);

        $fraction = self::resizeFraction($fraction, $scale, $value);
        $normalized = self::format($sign.$integer.'.'.$fraction, $scale);

        return new self($normalized, $scale);
    }

    /**
     * Каноническая строка: знак (только для ненулевых), целая часть без ведущих
     * нулей, ровно $scale знаков после точки.
     */
    public function toString(): string
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    public function scale(): int
    {
        return $this->scale;
    }

    public function add(self $other): self
    {
        return $this->binary($other, bcadd(...));
    }

    public function subtract(self $other): self
    {
        return $this->binary($other, bcsub(...));
    }

    public function multiply(self $other, ?int $scale = null): self
    {
        $scale ??= max($this->scale, $other->scale, self::INTERNAL_SCALE);

        return new self(
            self::format(bcmul($this->amount, $other->amount, $scale), $scale),
            $scale,
        );
    }

    public function divide(self $other, ?int $scale = null): self
    {
        if ($other->isZero()) {
            throw new MoneyException('Деление на ноль');
        }

        $scale ??= max($this->scale, $other->scale, self::INTERNAL_SCALE);

        return new self(
            self::format(bcdiv($this->amount, $other->amount, $scale), $scale),
            $scale,
        );
    }

    /**
     * Округление HALF_UP (2.5 → 3, -2.5 → -3) до заданной шкалы.
     * Округление выполняется один раз — над итоговой стоимостью события.
     */
    public function roundHalfUp(int $scale = self::MONEY_SCALE): self
    {
        if ($scale >= $this->scale) {
            return new self(self::format($this->amount, $scale), $scale);
        }

        $negative = str_starts_with($this->amount, '-');
        $absolute = $negative ? substr($this->amount, 1) : $this->amount;

        [$integer, $fraction] = explode('.', $absolute, 2);

        $kept = substr($fraction, 0, $scale);
        $dropped = substr($fraction, $scale);

        // HALF_UP по модулю: смотрим только на первую отбрасываемую цифру.
        if ('' !== $dropped && (int) $dropped[0] >= 5) {
            $truncated = '' === $kept ? $integer : $integer.'.'.$kept;
            $rounded = bcadd($truncated, self::oneAtScale($scale), $scale);

            return new self(self::format(($negative ? '-' : '').$rounded, $scale), $scale);
        }

        return new self(
            self::format(($negative ? '-' : '').$integer.('' === $kept ? '' : '.'.$kept), $scale),
            $scale,
        );
    }

    public function compareTo(self $other): int
    {
        return bccomp($this->amount, $other->amount, max($this->scale, $other->scale));
    }

    public function isZero(): bool
    {
        return 0 === bccomp($this->amount, '0', $this->scale);
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->amount, '-');
    }

    public function equals(self $other): bool
    {
        return 0 === $this->compareTo($other);
    }

    public function addScaledInt(int $value): self
    {
        return $this->add(self::fromString((string) $value, $this->scale));
    }

    private function binary(self $other, callable $operation): self
    {
        $scale = max($this->scale, $other->scale);

        return new self(
            self::format($operation($this->amount, $other->amount, $scale), $scale),
            $scale,
        );
    }

    private static function oneAtScale(int $scale): string
    {
        return 0 === $scale ? '1' : '0.'.str_pad('1', $scale, '0', STR_PAD_LEFT);
    }

    /**
     * Приводит дробную часть к нужной длине: обрезает лишние знаки,
     * дополняет нулями недостающие. Обрезка — только при импорте данных,
     * в самих вычислениях округление происходит ровно один раз (roundHalfUp).
     */
    private static function resizeFraction(string $fraction, int $scale, string $original): string
    {
        if (strlen($fraction) > $scale) {
            // Молча терять значимые цифры нельзя — это скрытое округление.
            if ('' !== trim(substr($fraction, $scale), '0')) {
                throw new MoneyException(sprintf(
                    'Число "%s" имеет больше %d знаков после точки и будет усечено',
                    $original,
                    $scale,
                ));
            }
            $fraction = substr($fraction, 0, $scale);
        }

        return str_pad($fraction, $scale, '0');
    }

    private static function format(string $value, int $scale): string
    {
        if (0 === $scale) {
            return self::normalizeInteger($value);
        }

        $negative = str_starts_with($value, '-');
        $unsigned = $negative ? substr($value, 1) : $value;

        $parts = explode('.', $unsigned, 2);
        $integer = $parts[0];
        $fraction = str_pad($parts[1] ?? '', $scale, '0');
        $fraction = substr($fraction, 0, $scale);

        $normalizedInteger = self::normalizeInteger($integer);
        $isZero = '0' === $normalizedInteger && '' === trim($fraction, '0');

        return ($negative && !$isZero ? '-' : '').$normalizedInteger.'.'.$fraction;
    }

    private static function normalizeInteger(string $value): string
    {
        $negative = str_starts_with($value, '-');
        $digits = ltrim($negative ? substr($value, 1) : $value, '0');
        $digits = '' === $digits ? '0' : $digits;

        return ($negative && '0' !== $digits ? '-' : '').$digits;
    }
}
