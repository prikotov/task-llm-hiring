#!/usr/bin/env bash
# Вспомогательная программа: вычисляет стоимость события ровно как PricingCalculator
# и сверяет результат с эталонным (6 знаков, HALF_UP). Запуск:
#   bash bin/verify-event-cost.bash <provider> <model> <ts-iso-utc> <in> <out> <cr> <cw> <expected>
set -euo pipefail

cd "$(dirname "$0")/.."

provider=$1; model=$2; ts=$3; in=$4; out=$5; cr=$6; cw=$7; expected=$8

rows=$(psql "postgresql://app:app@127.0.0.1:5544/app" -Atc "
  SELECT json_agg(json_build_object('valid_from', p.valid_from, 'unit', p.unit, 'rates', p.rates) ORDER BY p.valid_from)
  FROM prices p JOIN models m ON m.id = p.model_id JOIN providers pr ON pr.id = m.provider_id
  WHERE pr.code = '$provider' AND m.code = '$model';" | head -n 1)
win=$(psql "postgresql://app:app@127.0.0.1:5544/app" -Atc "
  SELECT coalesce(off_peak_start || '|' || off_peak_end || '|' || off_peak_multiplier, '')
  FROM providers WHERE code = '$provider';" | head -n 1)

php -r '
require "vendor/autoload.php";
[, $rows, $win, $ts, $in, $out, $cr, $cw, $expected] = $argv;
$rows = array_map(
  fn ($r) => new \App\Entity\Price(
    new \App\Entity\Model(new \App\Entity\Provider("verify", "verify"), "verify"),
    new DateTimeImmutable($r["valid_from"]),
    "USD",
    $r["unit"],
    $r["rates"]),
  json_decode($rows ?: "null", true) ?: []);
$win = array_values(array_filter(explode("|", $win), "strlen"));
$policy = (count($win) === 3)
  ? new \App\Domain\Billing\OffPeakPolicy($win[0], $win[1], $win[2])
  : null;
$best = \App\Domain\Billing\PriceSelection::best(new DateTimeImmutable($ts), $rows);
if ($best === null) { fwrite(STDERR, "NO_PRICE\n"); exit(2); }
$calc = new \App\Domain\Billing\PricingCalculator();
$actual = $calc->price($best, new \App\Domain\Billing\TokenUsage((int) $in, (int) $out, (int) $cr, (int) $cw), $policy, new DateTimeImmutable($ts));
echo "ACTUAL=$actual EXPECTED=$expected\n";
exit($actual === $expected ? 0 : 1);
' "$rows" "$win" "$ts" "$in" "$out" "$cr" "$cw" "$expected"
