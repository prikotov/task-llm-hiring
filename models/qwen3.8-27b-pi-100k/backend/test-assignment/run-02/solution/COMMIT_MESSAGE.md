# COMMIT MESSAGE

```
billing: полный конвейер расчёта стоимости + ретро-коррекция цены

Шесть команд через bin/console поверх каркаса (Symfony Console + Doctrine ORM 3
+ PostgreSQL 17): app:prices:import, app:usage:import, app:billing:rate,
app:billing:report, app:billing:reprice (бонус), app:billing:status.

- Доменная модель и миграция: providers, models, price_rows, usage_events,
  reprice_runs, reprice_event_corrections (снимки «до/после» коррекции).
- Строгий вход: ISO-8601 UTC с заглавной Z (без других форматов), HH:MM,
  целые неотрицательные токены, строки цен и множители — десятичные литералы;
  любая некорректная строка — stderr и exit 1, частичных записей нет.
- Деньги: точный bcmath; единственное округление — итоговой стоимости события
  (HALF_UP, 6 знаков); окно оф-пика: начало включительно, конец исключительно,
  переход через полночь, множитель применяется до единого округления.
- Идемпотентность: провайдер по code, модель по (provider, code), строка цен по
  (provider, model, valid_from); дубли использования по
  (provider, provider_request_id) игнорируются целиком.
- Выбор цены: строка модели с наибольшим valid_from <= ts (включительно).
- Отчёт: CSV строго в stdout (UTF-8, ',', \n), unrated никогда не попадают;
  сводка rate — stdout, диагностика unrated — stderr; прочие выводы — stderr.
- Ретро-коррекция цены (reprice): upsert строк по данным БД (providers/models в файле не обязательны),
  пересчёт затронутых rated-событий с журналом, stdout ровно две строки
  REPRICED_EVENTS/REPRICE_DELTA_USD; повторный запуск — те же события, дельта 0.

Проверено: 33 юнит-теста (phpunit), полный цикл на чистой БД, отчёт байт-в-байт
совпадает с примером TASK.md, повторный импорт + rate + report — неизменен,
exit 0/1 на всех типовых сценариях.
```
