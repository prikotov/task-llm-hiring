<?php

declare(strict_types=1);

namespace App\Import;

/** Провайдер из файла справочника цен. */
final class ProviderSpec
{
    /**
     * @param bool $offPeakTouched задано ли поле off_peak (в файле импорта «не задано» эквивалентно null —
     *                            у провайдера нет оф-пика; в файле коррекции «не задано» означает
     *                            «не трогать сохранённое значение»)
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        /// Минуты суток UTC начала оф-пикового окна (null — оф-пика нет).
        public readonly ?int $offPeakStartMinute,
        /// Минуты суток UTC конца оф-пикового окна, эксклюзивно (null — оф-пика нет).
        public readonly ?int $offPeakEndMinute,
        /// Множитель стоимости в оф-пике, десятичная строка (null — оф-пика нет).
        public readonly ?string $offPeakMultiplier,
        public readonly bool $offPeakTouched,
    ) {
    }
}
