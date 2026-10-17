#!/usr/bin/env python3
"""Референсная реализация тарификации и генератор golden-выходов чекера.

Единый источник истины арифметики тестового задания «биллинг LLM-токенов»:
правила здесь = правила в TASK.md. Чекер (checker/check.py) сравнивает вывод
команд кандидата с golden-файлами, которые генерирует этот скрипт.

Семантика (должна совпадать с текстом задания TASK.md слово в слово):

1. Цена события — строка прайса с наибольшим valid_from <= ts события
   (valid_from включительно). Нет такой строки — событие unrated.
2. Схемы тарификации:
   - per_1m_tokens: cost = Σ tokens_k × price_k / 1_000_000 по видам токенов
     (input, output, cache_read, cache_write);
   - per_1m_tokens_unified: cost = (Σ всех токенов) × price_any / 1_000_000.
3. Оф-пик провайдера: если время суток ts (UTC) попадает в окно
   [window_start, window_end) (окно может переходить через полночь),
   стоимость события умножается на multiplier. Учитывается всегда, включая
   output-компоненту. Окно задаётся только у провайдера, не у модели.
4. Все промежуточные вычисления — без округления (точная десятичная
   арифметика). Округление ровно одно: итоговая стоимость события
   округляется HALF_UP до 6 знаков после точки. Суммы отчёта — сумма уже
   округлённых стоимостей событий.
5. Идемпотентность: ключ события (provider, provider_request_id);
   повторная доставка игнорируется целиком.
6. Период отчёта: from включительно, to исключительно; unrated-события
   в отчёт не попадают никогда.

Запуск: python3 reference_calc.py   (из каталога billing/, пишет golden/).
"""

from __future__ import annotations

import json
from decimal import Decimal, ROUND_HALF_UP, getcontext
from pathlib import Path

getcontext().prec = 60

Q6 = Decimal("0.000001")
MILLION = Decimal(1_000_000)
TOKEN_KINDS = ("input", "output", "cache_read", "cache_write")

HERE = Path(__file__).resolve().parent
FIXTURES = HERE / "fixtures"
GOLDEN = FIXTURES / "golden"

REPORT_FROM = "2026-07-01T00:00:00Z"
REPORT_TO = "2026-10-01T00:00:00Z"


def parse_ts(value: str) -> Decimal:
    """ISO-8601 UTC ('...Z') -> Decimal POSIX-секунд; сравнивать можно точно."""
    import datetime as dt

    moment = dt.datetime.strptime(value, "%Y-%m-%dT%H:%M:%SZ").replace(tzinfo=dt.timezone.utc)
    return Decimal(moment.timestamp())


def parse_hhmm(value: str) -> int:
    h, m = value.split(":")
    return int(h) * 3600 + int(m) * 60


def tod_seconds(value: str) -> int:
    hh, mm, ss = value[11:13], value[14:16], value[17:19]
    return int(hh) * 3600 + int(mm) * 60 + int(ss)


def q6(value: Decimal) -> Decimal:
    return value.quantize(Q6, rounding=ROUND_HALF_UP)


def fmt6(value: Decimal) -> str:
    return f"{q6(value):.6f}"


class Catalog:
    """Провайдеры, модели и история цен (то, что грузит app:prices:import)."""

    def __init__(self) -> None:
        self.providers: dict[str, dict] = {}
        self.models: set[tuple[str, str]] = set()
        self.prices: dict[tuple[str, str], list[dict]] = {}  # (provider, model) -> строки по valid_from

    def load(self, path: Path) -> None:
        data = json.loads(path.read_text(encoding="utf-8"))
        for provider in data.get("providers", []):
            self.providers[provider["code"]] = provider
        for model in data.get("models", []):
            self.models.add((model["provider"], model["code"]))
        for price in data.get("prices", []):
            key = (price["provider"], price["model"])
            rows = self.prices.setdefault(key, [])
            stamped = parse_ts(price["valid_from"])
            rows[:] = [r for r in rows if r[0] != stamped]  # upsert по (модель, valid_from)
            rows.append((stamped, price))
            rows.sort(key=lambda r: r[0])

    def price_for(self, provider: str, model: str, ts: Decimal) -> dict | None:
        best = None
        for stamped, price in self.prices.get((provider, model), []):
            if stamped <= ts:
                best = price
        return best

    def in_off_peak(self, provider: str, ts_raw: str) -> bool:
        cfg = self.providers.get(provider, {}).get("off_peak")
        if not cfg:
            return False
        start = parse_hhmm(cfg["window_start"])
        end = parse_hhmm(cfg["window_end"])
        tod = tod_seconds(ts_raw)
        if start > end:  # окно переходит через полночь: [16:30, 00:30)
            return tod >= start or tod < end
        return start <= tod < end

    def multiplier(self, provider: str, ts_raw: str) -> Decimal:
        return Decimal(self.providers[provider]["off_peak"]["multiplier"]) if self.in_off_peak(provider, ts_raw) else Decimal(1)


class Ledger:
    """События использования + тарификация (то, что делают app:usage:import и app:billing:rate)."""

    def __init__(self, catalog: Catalog) -> None:
        self.catalog = catalog
        self.events: dict[tuple[str, str], dict] = {}  # (provider, request_id) -> событие
        self.costs: dict[tuple[str, str], Decimal] = {}  # ключ события -> стоимость (6 знаков)
        self.rated_order: list[tuple[str, str]] = []  # порядок тарификации (по ts, затем id)

    def import_usage(self, path: Path) -> list[str]:
        accepted: list[str] = []
        for line in path.read_text(encoding="utf-8").splitlines():
            if not line.strip():
                continue
            event = json.loads(line)
            key = (event["provider"], event["provider_request_id"])
            if key in self.events:
                continue  # идемпотентность: повторная доставка игнорируется
            self.events[key] = event
            accepted.append(event["provider_request_id"])
        return accepted

    def rate(self) -> tuple[int, list[str]]:
        unrated: list[str] = []
        pending = [
            (parse_ts(e["ts"]), e["provider_request_id"], key)
            for key, e in self.events.items()
            if key not in self.costs
        ]
        pending.sort(key=lambda item: (item[0], item[1]))
        for _, _, key in pending:
            event = self.events[key]
            provider, model = event["provider"], event["model"]
            price = self.catalog.price_for(provider, model, parse_ts(event["ts"]))
            if price is None or (provider, model) not in self.catalog.models:
                unrated.append(f"UNRATED {provider}/{event['provider_request_id']} {event['ts']} no_price")
                continue
            cost = self.event_cost(event, price)
            self.costs[key] = q6(cost)
            self.rated_order.append(key)
        return len(pending) - len(unrated), unrated

    def event_cost(self, event: dict, price: dict) -> Decimal:
        rates = {k: Decimal(v) for k, v in price["rates"].items()}
        tokens = {
            "input": event["tokens_input"],
            "output": event["tokens_output"],
            "cache_read": event["tokens_cache_read"],
            "cache_write": event["tokens_cache_write"],
        }
        if price["unit"] == "per_1m_tokens_unified":
            total = Decimal(sum(tokens.values()))
            cost = total * rates["any"] / MILLION
        else:
            cost = Decimal(0)
            for kind in TOKEN_KINDS:
                cost += Decimal(tokens[kind]) * rates[kind] / MILLION
        return cost * self.catalog.multiplier(event["provider"], event["ts"])

    def reprice(self, catalog_after: Catalog) -> tuple[int, Decimal]:
        """Бонус: применить исправленные цены и пересчитать затронутые события."""
        affected = [
            key
            for key in self.rated_order
            if self.catalog.price_for(*self._pm(key), parse_ts(self.events[key]["ts"]))
            != catalog_after.price_for(*self._pm(key), parse_ts(self.events[key]["ts"]))
        ]
        old_total = Decimal(0)
        new_total = Decimal(0)
        for key in affected:
            event = self.events[key]
            provider, model = self._pm(key)
            old_total += self.costs[key]
            price = catalog_after.price_for(provider, model, parse_ts(event["ts"]))
            self.costs[key] = q6(self.event_cost(event, price))
            new_total += self.costs[key]
        return len(affected), q6(new_total) - q6(old_total)

    def _pm(self, key: tuple[str, str]) -> tuple[str, str]:
        event = self.events[key]
        return event["provider"], event["model"]

    def report(self, ts_from: str | None, ts_to: str | None) -> str:
        lo = parse_ts(ts_from) if ts_from else None
        hi = parse_ts(ts_to) if ts_to else None
        groups: dict[tuple[str, str], dict] = {}
        for key in self.rated_order:
            event = self.events[key]
            ts = parse_ts(event["ts"])
            if (lo is not None and ts < lo) or (hi is not None and ts >= hi):
                continue
            gk = (event["user"], f"{event['provider']}/{event['model']}")
            g = groups.setdefault(
                gk,
                {"input": 0, "output": 0, "cache_read": 0, "cache_write": 0, "cost": Decimal(0)},
            )
            g["input"] += event["tokens_input"]
            g["output"] += event["tokens_output"]
            g["cache_read"] += event["tokens_cache_read"]
            g["cache_write"] += event["tokens_cache_write"]
            g["cost"] += self.costs[key]

        lines = ["user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd"]
        totals = {"input": 0, "output": 0, "cache_read": 0, "cache_write": 0, "cost": Decimal(0)}
        for (user, model), g in sorted(groups.items()):
            lines.append(
                f"{user},{model},{g['input']},{g['output']},{g['cache_read']},{g['cache_write']},{fmt6(g['cost'])}"
            )
            for k in totals:
                totals[k] += g[k]
        lines.append(
            f"TOTAL,,{totals['input']},{totals['output']},{totals['cache_read']},{totals['cache_write']},{fmt6(totals['cost'])}"
        )
        return "\n".join(lines) + "\n"


def main() -> None:
    GOLDEN.mkdir(parents=True, exist_ok=True)

    # --- базовый сценарий: прайс + базовые события + тарификация -------------
    catalog = Catalog()
    catalog.load(FIXTURES / "prices.json")
    ledger = Ledger(catalog)
    ledger.import_usage(FIXTURES / "usage-base.jsonl")
    rated, unrated = ledger.rate()
    (GOLDEN / "report-base.csv").write_text(
        ledger.report(REPORT_FROM, REPORT_TO), encoding="utf-8"
    )
    (GOLDEN / "rate-stderr.txt").write_text(
        "\n".join(sorted(unrated)) + ("\n" if unrated else ""), encoding="utf-8"
    )
    print(f"base: rated={rated} unrated={len(unrated)}")

    # --- дубликаты: повторная доставка + коллизия id между провайдерами ------
    ledger.import_usage(FIXTURES / "usage-duplicates.jsonl")
    rated2, unrated2 = ledger.rate()
    # unrated-события ретраятся каждым запуском rate (цены могли подтянуться):
    # набор unrated не должен измениться, новых появиться не должно
    assert sorted(unrated2) == sorted(unrated), "после дубликатов набор unrated изменился"
    (GOLDEN / "report-duplicates.csv").write_text(
        ledger.report(REPORT_FROM, REPORT_TO), encoding="utf-8"
    )
    print(f"duplicates: rated={rated2} (1 новое событие sfera/h02; 3 точных дубля проигнорированы)")

    # --- бонус: ретро-коррекция цены lumen-mini и пересчёт -------------------
    catalog_after = Catalog()
    catalog_after.load(FIXTURES / "prices.json")
    catalog_after.load(FIXTURES / "prices-retro.json")
    count, delta = ledger.reprice(catalog_after)
    (GOLDEN / "report-retro.csv").write_text(
        ledger.report(REPORT_FROM, REPORT_TO), encoding="utf-8"
    )
    (GOLDEN / "reprice-stdout.txt").write_text(
        f"REPRICED_EVENTS={count}\nREPRICE_DELTA_USD={fmt6(delta)}\n", encoding="utf-8"
    )
    print(f"retro: re-priced={count} delta={fmt6(delta)}")

    # --- sample-сценарий для worked example в TASK.md ------------------------
    sample_catalog = Catalog()
    sample_catalog.load(HERE / "sample" / "prices.json")
    sample_ledger = Ledger(sample_catalog)
    sample_ledger.import_usage(HERE / "sample" / "usage.jsonl")
    sample_ledger.rate()
    print("--- sample report (TASK.md worked example, окно 2026-09-01..2026-10-01) ---")
    print(sample_ledger.report("2026-09-01T00:00:00Z", "2026-10-01T00:00:00Z"), end="")
    for key in sample_ledger.rated_order:
        event = sample_ledger.events[key]
        price = sample_catalog.price_for(event["provider"], event["model"], parse_ts(event["ts"]))
        detail = sample_ledger.event_cost(event, price)
        print(
            f"  {event['provider_request_id']}: точная={detail} -> округл.={fmt6(detail)}"
            f" off_peak_mult={sample_catalog.multiplier(event['provider'], event['ts'])}"
        )


if __name__ == "__main__":
    main()
