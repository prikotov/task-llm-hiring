# Commit message

```text
учёт расходов на LLM-токены: цены, события, тарификация, отчёт, reprice

Две входные последовательности:
- история цен (JSON) — провайдеры, модели, строки цен по valid_from;
- события использования (JSONL) — токен-разбивка запросов.

Правила тарификации:
- схемы per_1m_tokens и per_1m_tokens_unified;
- событие тарифицируется ценой, действовавшей в его момент
  (max valid_from <= ts, включительно);
- оф-пик провайдера [старт, конец) с переходом через полночь,
  множитель применяется ко всей стоимости события;
- точная десятичная арифметика (bcmath, масштаб 20), округление
  ровно одно — HALF_UP до 6 знаков на итог события; float не используется.

Надёжность приёма:
- дедупликация событий по (provider, provider_request_id), повторная
  доставка игнорируется целиком;
- идемпотентный upsert справочника (провайдер — code, модель — (provider, code),
  строка цены — (provider, model, valid_from));
- unrated-события не являются ошибкой: каждый run rate повторяет попытки,
  в отчёт не попадают.

Команды (bin/console):
  app:prices:import <file>      загрузка истории цен
  app:usage:import <file>       приём событий (JSONL)
  app:billing:rate              тарификация нерасчитанных событий
                                (RATED=<n> UNRATED=<m>; unrated — в stderr)
  app:billing:report            CSV-отчёт по (user, provider/model) + TOTAL,
                                --from/--to (UTC, from включительно, to исключ.),
                                вывод совпадает с примером TASK.md байт-в-байт
  app:billing:reprice <file>    бонус: ретро-коррекция цены и пересчёт
                                затронутых событий (REPRICED_EVENTS /
                                REPRICE_DELTA_USD)

Схема БД разворачивается миграцией на чистой базе
(migrations/Version20261027140000.php; make migrate).
Тесты: 45 PHPUnit-тестов домена (Money, OffPeakWindow, BillingComputer,
парсеры, рендер CSV).
```
