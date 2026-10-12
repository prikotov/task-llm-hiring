# Ручки мышления локальных моделей (ollama-host, ollama)

Дата: 2026-10-12 · ollama 0.40.0 · источники: `/api/show` по каждому тегу
(поля `thinking.values` / `thinking.default`, `parameters`), chat-шаблоны
(`/api/show` → `template`), наши прогоны (`session.json`, поле `thinking`
каждого хода).

## Сводка по всем тегам

| Тег(и) | Шкала `think` | Дефолт | Sampling-дефолты modelfile |
|---|---|---|---|
| `qwen3.8:27b` = `:latest`, `qwen3.8:27b-pi-100k` | false / low / medium / xhigh | medium | temp 1, top_k 20, top_p 0.95, min_p 0, presence_penalty 0, repeat_penalty 1, draft_num_predict 4 |
| `huihui-ai:pi-100k` | false / low / medium / xhigh | medium | то же + num_ctx 102400 |
| `huihui_ai/Qwen3.8-abliterated:latest` | false / low / medium / xhigh | medium | то же, без num_ctx |
| `qwen3.8-unc:pi-100k`, `qwen3.8-unc-iq4xs:pi-100k` | только false / true | **включено** | temp 1, top_k 20, top_p 0.95, draft 4, num_ctx 102400 |
| `orcarouter/Qwen3.8-27B-Uncensored:latest`, `:iq4_xs` | только false / true | **включено** | temp 1, top_k 20, top_p 0.95 |
| `gemma4:26b` (+`-pi-100k`), `gemma4:31b-it-qat` (+`-pi-100k`), `gemma4:e2b` | только false / true | **включено** | temp 1, top_k 64, top_p 0.95, draft 3 |
| `qwen3-vl:8b` (+`-pi-100k`), `qwen3-vl:30b-a3b` (+`-pi-100k`) | только false / true | **включено** | temp 1, top_k 20, top_p 0.95 |
| `muse-glimmer` | false / low / medium / high / max | **high** | temp 1, top_k 64, top_p 0.95 |
| `ornith-1.5:35b` | `/api/show` не отдаёт; в шаблоне `enable_thinking` | **включено** (дефолт движка, подтверждён прогонами) | пусто — дефолты движка |
| `medgemma:27b` | capability thinking отсутствует | — | stop `<end_of_turn>` |

Замечания:

- Градации уровня есть только у «базовых» сборок qwen3.8 (qwen3.8,
  huihui-ai, huihui-abliterated): `false/low/medium/xhigh`, дефолт
  `medium`. Unc- и orcarouter-сборки того же семейства шкалу потеряли:
  только `false/true`, дефолт «включено» — видимо, иной шаблон в сборке.
- `muse-glimmer` — своя шкала `low/medium/high/max` (нет `xhigh`),
  дефолт `high`. Если будут уровневые прогоны — не путать имена уровней
  с qwen3.8.
- Дефолт у всех «мышление включено»: серии с `think: false` отклонялись
  от дефолта сознательно (см. «Сопоставимость серий»).
- Объектная схема с бюджетом (`{"type": …, "budget_tokens": …}`) ollama
  не принимает — ошибка 400 (проверено на qwen3.8).
- Sampling-дефолты выше — то, что зашито в modelfile тегов; раннер их
  не переопределяет (по правилу «параметры генерации — дефолтные»).

## Как задавать

- API: поле `think` **верхнего уровня тела** `/api/chat` — не внутри
  `options` (там оно молча не сработает). Выключение — булево `false`,
  уровень — строка: `"low" | "medium" | "xhigh"` (qwen3.8-семейство),
  `"high" | "max"` (muse-glimmer):
  ```bash
  curl http://ollama-host:11434/api/chat -d '{
    "model": "qwen3.8:27b",
    "messages": [{"role": "user", "content": "…"}],
    "think": "low",
    "stream": false
  }'
  ```
- Раннер: `interview/runner.py --think on|off|auto|low|medium|xhigh`
  (`on`/`off` → `true`/`false`; `auto` — параметр не отправляется вовсе,
  модель работает на своём дефолте).

## Верификация: `think: false` — мышление действительно выключено

Механика (qwen-стиль шаблонов; видна в шаблоне ornith, строки с
`enable_thinking`): `think: false` → при рендере шаблона в промпт
вклеивается пустой предзакрытый блок `<think>\n\n</think>`. Блок
размышлений закрыт заранее — модели физически некуда генерировать
рассуждения; это не «просьба не думать», а предзаполнение. Для gemma4
и muse-glimmer механизм в деталях не разбирался — там подтверждение
эмпирическое.

Эмпирика по нашим прогонам (`session.json`, поле `thinking` каждого хода):

| Модель | Прогоны | Ходов с непустым thinking |
|---|---|---|
| gemma4-26b | run-01…10 (`off`) | 0 |
| orcarouter-Qwen3.8-27B-Uncensored | run-01…10 (`off`) | 0 |
| huihui_ai-Qwen3.8-abliterated | run-01…10 (`off`) | 0 |
| muse-glimmer | run-01…02 (`off`) | 0 |
| ornith-1.5-35b | run-07…21 (`off`) | 0 |
| ornith-1.5-35b | run-01…06 (`auto`) | **32/32 в каждом**, 223–359k символов за собес |

Утечек `<think>` в видимый текст ответов нет ни в одном `off`-транскрипте
(grep по всем transcript.md серий выше).

Оговорка: `think: false` не запрещает модели рассуждать прозой в самом
ответе (видимый CoT — показано экспериментом C у ornith: system-роль +
CoT-инструкция). Это часть ответа, а не скрытое мышление; для протокола
«сырая модель без размышлений» `think: false` закрывает вопрос полностью.

## Сопоставимость серий

- gemma4-26b, orcarouter-unc, huihui-abliterated, muse-glimmer и ornith
  run-07+ — режим `think: false`, напрямую сопоставимы между собой и с
  qwen3.8 `think: false`.
- Дефолтные прогоны с мышлением — другой режим работы тех же весов,
  баллы с `off`-сериями не смешивать: qwen3.8 run-01…05 (`auto` =
  `medium`, ~125k токенов мышления за собес, переполнение контекста),
  ornith run-01…06 (`auto`).
- Уровневые прогоны (`low/medium/xhigh`) на qwen3.8-семействе и
  muse-glimmer не выполнялись; зафиксированная гипотеза — qwen3.8
  `think: low` для длинных собесов (см. `qwen3.8/model.md`).
