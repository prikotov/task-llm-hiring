# qwen4-max-preview-off (alibabacloud qwen-token-plan, via pi)

## Характеристики

- Модель: `qwen4-max-preview` — флагманская Qwen следующего поколения
  на подписке alibabacloud (Model Studio token plan); в pi-каталоге
  пока не числится, запускалась как custom id через
  `--provider qwen-token-plan-individual`
- Транспорт: `knowledge-map/runner_pi.py` (новый адаптер — pi -p
  --no-session per клетка, тот же протокол и грейдинг, что в
  runner_map; usage-статистики токенов транспорт не отдаёт)
- Тест: 1 прогон, ru, thinking off, вопросник v8 (240) + deep v4 (56)

## Карта знаний PHP (2026-11-12, 1 прогон off)

- База (L1–L8): **236/240 = 98.3%** — лучший результат карты
- Глубина (L9/L10): **54/56 = 96.4%** — уровень лидеров (deepseek ru 96.4)
- Всего: **290/296 = 98.0%**
- Скорость: ~5 с/клетку, 24.3 мин на 296 клеток (thinking off)

Ошибки (6): T07-L8 (PHPUnit-детали), VC-L8 и VF-L1 (версии «текущая
минорная» — ответил 8.5 вместо 8.6), VF-L3, T12-L10 (бит E_STRICT),
T21-L10 (Twig-исключение). Ни одной ошибки на да/нет-базе уровней
L1–L7 PHP-тем.

Выводы: верх лидерборда Глубины сразу после прогона — при
thinking off. Для честного сравнения с qwen3.8/ornith (у которых
low/medium) нужен прогон с thinking on — по команде.
