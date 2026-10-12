# Собес: `qwen3.8:latest` · роль: backend — run-22

- Дата: 2026-10-12T12:46:11.338030+00:00
- Сервер: http://nserv:11434 · keep_alive: 10m · think: low · num_ctx: 262144
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to role-play as a PHP backend developer in a job interview. They want me to introduce myself, talk about my experience in PHP and related technologies, and then rate my knowledge on a scale of 0-10 for each area: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, clean architecture, testing, git — with a one-sentence justification for each rating.

I need to be careful here. I'm an AI, not a human developer. I should be honest about what I am while still engaging with the exercise in a useful way. I can frame this as "here's how I'd answer this question based on my knowledge" or I can simply play the role as requested since the user explicitly set up this scenario.

Actually, let me reconsider. The user said "Ты проходишь собеседование" — "You're going through an interview." They're setting up a role-play scenario. I should engage with it naturally, presenting myself as a candidate would, while being knowledgeable and giving useful, specific answers. I'll present a realistic senior-level profile.

Let me craft a response that feels like a real senior PHP developer would speak — confident but not arrogant, specific, with concrete examples rather than buzzwords.

I'll write in Russian since that's the language of the request.

</details>

**Кандидат:**

Здравствуйте, спасибо за возможность пообщаться.

Я занимаюсь PHP-разработкой около 7 лет. Первые два года — классический CMS-разработчик (Drupal, Laravel), потом пришёл в Symfony и не уходил. Последние три года — в команде из 8 человек, где мы строим B2B-платформу: 12 сервисов, каждый на Symfony + Doctrine, общая база PostgreSQL, асинхронность через RabbitMQ.

Несколько конкретных моментов:

**PHP** — работаю с PHP 8.0+. Активно использую readonly-классы, enums, fiber-совместимые паттерны, constructor promotion. На уровне языка понимаю, что происходит с JIT, когда он помогает, а когда — нет. Пишу свои PSR-4-пакеты, которые переиспользую между сервисами.

**Symfony** — пишу с 5.1. Знаю, как устроен container под капотом, как работает DI-компиляция, когда кэш инвалидируется. Сам делал custom bundle с интеграцией внутреннего IAM-провайдера. Понимаю, когда Symfony — правильный выбор, а когда лучше взять Slim или raw PHP.

**Doctrine** — работаю ежедневно. Писал репозитории с DQL, сталкивался с N+1, lazy-loading-ловушками, отложенным flush'ем. В последний год мигрировал одну подсистему с Entity на Document-подобный подход (свой read-model слой поверх Doctrine), чтобы отделить чтение от записи.

**PostgreSQL** — пишу запросы вручную, когда ORM не справляется. Знаю про `ctid`, `MVCC`, `pg_stat_statements`, как читать `EXPLAIN ANALYZE`. Раз в полгода делаю пересмотр индексов — в прошлый раз сократили p99 с 400мс до 45мс на одном эндпоинте.

**DDD** — применяю тактики: bounded contexts по доменным подсистемам, aggregate roots, доменные события. Не фанат "чистого" DDD — у нас 70% кода выглядит как обычные сервисы, но там, где домен сложный (расчёты, биллинг), паттерны реально помогают держать рассудок.

**Чистая архитектура** — знаю принципы, понимаю мотивацию. На практике — у нас есть ядро домена без зависимости от фреймворка, но я не догоняю идеал, если это не даёт измеримой выгоды. Балансирую между чистотой и скоростью доставки.

**Тестирование** — PHPUnit + Prophecy. Покрываем критический доменный код — около 85% statement coverage (целиком). Пишу feature-тесты через `KernelTestCase` + `Panther`. Интеграционные — через Testcontainers. Таск-тесты — отдельно, через Pest.

**Git** — daily driver. Знаю про `rebase`, `cherry-pick`, `bisect`, conflict resolution на уровне merge-base. У команды conventional commits + squash-merge + PR-ревью. Написал pre-commit хук, который гоняет PHPStan + Rector автоматически.

---

Оценки:

| Область | Оценка | Обоснование |
|---------|--------|-------------|
| **PHP 8** | **8** | Ежедневный фреймворк, знаю фичи 8.0–8.3 на уровне «могу объяснить why», не уверен в edge-cases JIT-компиляции. |
| **Symfony** | **8** | Пишу с 5.1, понимаю container, routing, security — но не читал исходники всех компонентов, некоторые internals знаю по документации. |
| **Doctrine** | **7** | Писал много, но не до уровня «могу починить bug в UnitOfWork» — в сложных сценариях с каскадным удалением и versioning'ом иногда упираюсь. |
| **PostgreSQL** | **6** | Умею читать EXPLAIN, тюнить индексы, понимаю MVCC — но не администрирую кластеры и не пишу PL/pgSQL-функции в продакшене. |
| **DDD** | **7** | Применяю в реальных проектах, понимаю bounded context и aggregate — но не писал ни одной книги и не веду домен-моделирование с архитекторами формально. |
| **Чистая архитектура** | **6** | Знаю и применяю выборочно, но не претендую на «hexagonal purity» — в 90% случаев это overkill для команды из 8 человек. |
| **Тестирование** | **7** | Пишу unit/integration/feature, знаю стратегии — но не могу уверенно сказать, что моя стратегия покрытий оптимальна; иногда пере- или недо-тестю. |
| **Git** | **8** | Использую ежедневно, понимаю историю, ребазы, бисекцию — но не администрирую GitLab/GitHub на уровне серверной конфигурации. |

Готов отвечать на уточняющие вопросы.

> ⏱ 16.5s · prompt 135 tok · output 1426 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user is continuing the interview. They want me to discuss the most important PHP 8.0+ features for writing clean and reliable code, with short code examples.

Let me think about what's genuinely important for "clean and reliable code" (not just "new features"):

1. **ReadOnly properties (8.1)** — truly important for clean code. Prevents accidental mutation, signals intent.

2. **Enums (8.1)** — replaces string/integer constants, type safety at compile time, exhaustiveness checking. Huge for reliability.

3. **Constructor Property Promotion (8.0)** — reduces boilerplate, makes DTOs/VOs much cleaner.

4. **Named Arguments (8.0)** — improves readability, especially with many parameters.

5. **First-class callable syntax (8.1)** — cleaner than `fn($x) => $obj->method($x)`.

6. **Never return type (8.1)** — makes it explicit when a function never returns (e.g., throws always).

7. **Ditto (8.0)** — `&` for references, slightly niche.

8. **`match` expression (8.0)** — already in 8.0, but important. Exhaustive checking when combined with enums.

9. **Fiber (8.1)** — more for async, not directly "clean code" per se.

10. **`enum` + `match` exhaustiveness** — the combination is the killer feature for reliability.

11. **`readonly` classes (8.2)** — even stronger than readonly properties. Whole class is immutable.

12. **`enum` backed vs pure** — backed enums give you `from()`, `tryFrom()`, `cases()` methods.

13. **Typed properties + strict_types** — not new in 8, but combined with 8 features it's the baseline.

14. **`str_contains`, `str_starts_with`, `str_ends_with` (8.0)** — small but improves readability.

15. **`nullsafe operator` — already in 8.0** — `?->`

16. **`enum` + `match` = compile-time-like exhaustiveness** — this is probably the single most impactful combination for reliability.

Let me pick the 5-6 most impactful for "clean AND reliable":

1. **Enums (8.1)** — type safety, exhaustiveness
2. **ReadOnly properties/classes (8.1/8.2)** — immutability
3. **Constructor property promotion (8.0)** — less boilerplate, clearer DTOs
4. **First-class callable syntax (8.1)** — cleaner callbacks
5. **`never` return type (8.1)** — explicit contract
6. **Named arguments (8.0)** — readability

I'll structure this as a clear answer with code examples. Keep examples short and focused on the point.

Let me write this in Russian since the conversation is in Russian, but code in English (PHP code is in English anyway).

</details>

**Кандидат:**

Выбираю не "все фичи из changelog", а то, что реально снижает количество багов и делает намерение кода явным.

---

## 1. Enums (8.1) — типизация доменных состояний

**Проблема:** строковые константы дают ощущение типизации, но на деле это `string`. Типизатор не поможет, `match` не проверит исчерпываемость.

```php
// До — хрупко
final class OrderStatus
{
    public const NEW = 'new';
    public const PAID = 'paid';
    public const SHIPPED = 'shipped';
}

// После — типизировано
enum OrderStatus: string
{
    case New     = 'new';
    case Paid    = 'paid';
    case Shipped = 'shipped';

    public function canBeCancelled(): bool
    {
        return match ($this) {
            self::New, self::Paid => true,
            self::Shipped => false,
        };
    }
}
```

Почему это важно:

- `match` на enum **без `default`** — если добавишь новый `case`, PHPStan/Psalm/IDE покажут, что функция не покрыта. Это ближе всего к exhaustiveness-проверке из Scala/Rust, что у нас есть.
- `OrderStatus::tryFrom('invalid')` вернёт `null` вместо того, чтобы бросить exception или молча принять мусор.
- Нельзя случайно присвоить `OrderStatus::PAID = 'shipped'` — это compile-time ошибка.

---

## 2. ReadOnly properties / classes (8.1 / 8.2) — иммутабельность как намерение

**Проблема:** DTO, которые "по идее" неизменяемы, на деле можно мутировать. Через 6 месяцев кто-то делает `$order->total = 0` в каком-нибудь сервисе, и никто не заметит.

```php
// 8.1 — readonly properties
readonly class Money
{
    public function __construct(
        public int    $amount,
        public string $currency,
    ) {}

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException($this->currency, $other->currency);
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}

// 8.2 — readonly class (ещё строже: нельзя мутировать даже изнутри)
readonly class Shipment
{
    public function __construct(
        public readonly string $trackingNumber,
        public readonly CarbonImmutable $shippedAt,
        public readonly Money $cost,
    ) {}
}

// $shipment->trackingNumber = 'ABC' → Fatal Error
```

Почему это важно:

- `readonly` + `__construct` = **immutable by construction**. Нет `new Money()` + `$m->amount = 5`, потому что такое вообще не компилируется.
- В `Money` метод `add` возвращает **новый** объект — это чистая функция, предсказуемая, тривиально тестируемая.
- `readonly class` (8.2) закрывает лазейку: нельзя сделать `public` поле и потом в конструкторе `public function makeFree(): void { $this->cost = new Money(0, 'USD'); }`.

---

## 3. Constructor Property Promotion (8.0) — меньше бойлерплейта, больше сигнала

**Проблема:** DTO на 3 строки — 3 свойства + 3 аргумента + 3 присваивания. Шум, который отвлекает от логики.

```php
// До (7.x)
class Address
{
    private string $street;
    private string $city;
    private string $zip;

    public function __construct(string $street, string $city, string $zip)
    {
        $this->street = $street;
        $this->city   = $city;
        $this->zip    = $zip;
    }

    public function getStreet(): string { return $this->street; }
    public function getCity():   string { return $this->city; }
    public function getZip():    string { return $this->zip; }
}

// После (8.0+)
readonly class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zip,
    ) {}
}
```

Почему это важно:

- Меньше строк = меньше шансов на опечатку в присваивании.
- `public` + `readonly` = getter не нужен вообще. Код, который читает `$address->city`, самообъясняющ.
- Комбинируется с `readonly class` (8.2) — ещё короче.

---

## 4. `never` return type (8.1) — явный контракт "эта функция не вернёт управление"

**Проблема:** функции, которые **всегда** бросают исключение, формально могут вернуть что угодно. Статические анализаторы и читающий код это не знают.

```php
// До — анализатор думает, что $order может быть null после if
public function findOrFail(int $id): Order
{
    $order = $this->repo->find($id);

    if ($order === null) {
        throw new OrderNotFoundException($id);
    }

    return $order;
}

// После — анализатор знает, что после if блок недостижим
public function findOrFail(int $id): Order
{
    $order = $this->repo->find($id);

    if ($order === null) {
        $this->fail($id); // never
    }

    return $order; // статический анализатор: $order гарантированно не null
}

// В отдельном месте:
final class Validator
{
    public function assertPositive(int $value): never
    {
        if ($value <= 0) {
            throw new InvalidArgumentException("Expected > 0, got {$value}");
        }
    }
}
```

Почему это важно:

- PHPStan / Psalm корректно сужают типы после вызова `never`-функции — не нужен `assert()` или `!`-оператор.
- Читающий код сразу видит: "эта функция не вернёт", не нужно читать тело, чтобы убедиться.
- Особенно важно в guard-клаусах: `if ($condition) { $this->reject(); }` — после этого анализатор знает, что `$condition === false`.

---

## 5. First-class callable syntax (8.1) — чистые колбэки

**Проблема:** `fn($x) => $this->serializer->normalize($x)` — каждый раз. Шумит, отвлекает от того, **что** передаётся в pipeline.

```php
// До
$pipeline = [
    fn (array $raw): array    => $this->parser->normalize($raw),
    fn (array $parsed): array => $this->validator->validate($parsed),
    fn (array $valid): array  => $this->mapper->toDomain($valid),
];

// После
$pipeline = [
    $this->parser->normalize(...),
    $this->validator->validate(...),
    $this->mapper->toDomain(...),
];
```

Почему это важно:

- Меньше шума → легче читать цепочки, легче рефакторить.
- Комбинируется с `array_map`, `array_filter`, `->pipe()` в коллекциях — код становится декларативным.
- Нет "забытой" стрелки `=>`, которая при копипасте иногда теряется.

---

## 6. Named arguments (8.0) — читаемость при широких сигнатурах

```php
// До — нужно считать аргументы или читать документацию
$formatter = new NumberFormatter(
    'en_US',
    NumberFormatter::DECIMAL,
    2,
    NumberFormatter::ROUND_HALF_UP,
);

// После — самообъясняюще
$formatter = new NumberFormatter(
    locale:   'en_US',
    style:    NumberFormatter::DECIMAL,
    maxDigits: 2,
    rounding: NumberFormatter::ROUND_HALF_UP,
);
```

Почему это важно:

- Особенно полезно, когда в сигнатуре есть `null`-по-умолчанию: `new QueryBuilder(entity: 'User', dql: 'SELECT ...')` — не нужно запоминать порядок.
- При рефакторинге (добавили параметр посередине) — вызовы не ломаются, потому что аргументы именованные.
- В паре с PHPStan: если перепутаешь имена аргументов — это compile-time ошибка, а не runtime.

---

## Что я **не** считаю критичным для чистоты

| Фича | Почему не в топ |
|------|----------------|
| **Fibers (8.1)** | Мощно, но это асинхронность, а не чистота. В 90% бэкенд-кейсов event loop / worker pool — правильнее. |
| **`str_contains` / `str_starts_with` (8.0)** | Приятно, но это замена `strpos() !== false` — микро-уровень. |
| **`enum` backed vs pure** | Разница в `from()`/`tryFrom()` vs `cases()` — важно, но вторично относительно самого факта, что enum есть. |
| **JIT (8.0)** | Для типичного Symfony-приложения с 10–30 rps — незначимый выигрыш. Не влияет на чистоту. |

---

**Итого:** если бы я мог выбрать только две фичи для "чистого и надёжного кода" — это **Enums + `match`** (типизация + exhaustiveness) и **`readonly`** (иммутабельность). Всё остальное — улучшения эргономики.

> ⏱ 22.2s · prompt 1343 tok · output 2999 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

The user is continuing the interview. They want a deep dive into PHP's type system: strict_types, type coercion, common pitfalls, examples where typing saves you and where it surprises.

I need to be precise and show real-world PHP behavior, not just textbook stuff. Let me think about what's actually surprising and important:

1. strict_types — what it actually does and doesn't do (file-level, not function-level; doesn't affect internal functions the same way)
2. Type coercion rules — the "weak" mode behavior
3. Common pitfalls:
   - `strict_types` is per-file, not per-function
   - Internal functions in strict mode — some still coerce (e.g., `strlen(true)`)
   - `int` vs `float` in strict mode — `1.0` is not `int`
   - `string` vs `int` — "123" is not `int` even in weak mode? No, it is in weak mode
   - The `bool` coercion traps — `"0"` is falsy, `[]` is falsy
   - `array` — `array(1,2)` vs `["a"]` — no structural typing
   - `callable` — ambiguous
   - Return types — strict_types affects argument types, but return types are always strict!
   - `null` and `?Type` — nullable vs `null` default
   - Union types and the order (cosmetic)
   - `mixed` in 8.0 — the "anti-type"
   - `never` — the "no value" type
   - `object` vs specific class
   - `self` vs `static` vs `parent`
   - `iterable` — array|Traversable
   - `callable` — the most ambiguous type in PHP
   - `void` — cannot assign, cannot return (except from functions that don't return)
   - `void` and strict_types interaction

Key surprises:
1. strict_types is declared per FILE. So if you call a function from a file WITHOUT strict_types, it uses weak mode regardless of the callee's file having strict_types.
2. Return types are ALWAYS strict, regardless of strict_types declaration.
3. In weak mode, `int` parameter accepts `float` (if it has integer value) and `string` (if it looks like int).
4. In strict mode, `int` parameter does NOT accept `float` — `1.0` is float, not int.
5. `bool` in strict mode: only `true`/`false` accepted. In weak mode: `"1"`, `1`, `1.0` accepted.
6. `array` is not structural — `array<string, int>` doesn't exist. `array` is just "any array".
7. `callable` accepts strings (function names), arrays ([obj, method]), Closures, objects with `__invoke`.
8. The `null` coercion: in weak mode, `null` can be passed to most scalar types (with deprecation notice in 8.0+ for non-nullable).
9. `?int` (nullable int) — `null` is a valid value.
10. Union types: `int|string` — order is cosmetic, but PHPStan might care about exhaustiveness in match.
11. `mixed` — means "any type, including null". It's the opposite of a type constraint.
12. The `void` type — you can declare `: void` but you cannot actually return a value. You can have `return;` but not `return 5;`.

Let me structure this well with code examples showing the surprising behavior. I'll use actual PHP behavior, being careful to be accurate.

Let me verify some things:
- In strict_types=1 file, calling `strlen(3)` — this is an internal function. Actually, internal functions in strict mode: PHP 8.0+ made internal functions stricter. Let me be careful here. Actually, `strlen` expects `string`. In strict mode, `strlen(3)` throws TypeError. In weak mode, `strlen(3)` returns 1 (coerces to "3").
- Actually wait, I need to double-check. In PHP 8.0, passing wrong types to internal functions that previously silently coerced now throw TypeError. But there are some exceptions.

Let me be careful and accurate. I'll focus on the most important and well-documented behaviors.

Structure:
1. strict_types — what it is, the file-level gotcha
2. Coercion rules (weak mode) — table
3. Return types are always strict
4. Pitfalls with examples
5. Where typing saves you
6. Where typing surprises you

Let me write this up clearly.

</details>

**Кандидат:**

## Строгая граница: strict_types

### Что он делает

`declare(strict_types=1);` включает **строгий режим** для **этого файла**. Влияет на **аргументы функций**, объявленные в этом файле.

### Ключевая ловушка: объявляется в файле-вызывающем

```
// caller.php
<?php
declare(strict_types=1);

require 'lib.php';

someFunc(1.0); // ???
```

```
// lib.php
<?php
// БЕЗ strict_types

function someFunc(int $value): void
{
    echo "got: {$value}";
}
```

**Результат:** `TypeError: someFunc(): Argument #1 ($value) must be of type int, float given`.

Потому что режим определяется **файлом, из которого вызывается**, а не файлом, где функция объявлена. Это неочевидно и ломает интуицию.

### Что strict_types **не** делает

| Ситуация | strict_types влияет? |
|----------|---------------------|
| Аргументы функций (вызов из файла с `strict_types=1`) | ✅ Да |
| **Возвращаемые значения** | ❌ Нет — всегда строго |
| Внутренние свойства / методы | ❌ Нет |
| Анонимные функции (closure) | ✅ Да (в том же файле) |

Возвращаемый тип **всегда** проверяется строго:

```php
function getFloat(): float
{
    return 1; // OK — int 1 приводится к float 1.0 (это не "weak mode", это widening)
}

function getInt(): int
{
    return 1.0; // TypeError! float → int не допускается при возврате
}
```

Это асимметрия: `int → float` при возврате — ок (widening), `float → int` — нет.

---

## Правила приведения в слабом режиме

Когда `strict_types` **не** включён (или файл-вызывающий не объявил):

| Параметр типа | `int` | `float` | `string` | `bool` | `null` |
|:---:|:---:|:---:|:---:|:---:|:---:|
| `int` | ✅ | ✅ (целое значение: `1.0`) | ✅ (`"42"`) | ✅ | ⚠️ deprecated |
| `float` | ✅ | ✅ | ✅ (`"3.14"`) | ✅ | ⚠️ deprecated |
| `string` | ✅ | ✅ | ✅ | ✅ | ⚠️ deprecated |
| `bool` | ✅ (`1`, `0`) | ✅ | ✅ (`"1"`, `"true"`) | ✅ | ⚠️ deprecated |

⚠️ В PHP 8.0+ передача `null` в необязательный скалярный тип выдаёт **deprecation notice**, в PHP 9 — станет `TypeError`.

### Примеры слабого режима

```php
function add(int $a, int $b): int { return $a + $b; }

add(1, 2);        // ✅ 3
add(1.0, 2);      // ✅ 3 (float 1.0 → int 1)
add("1", "2");    // ✅ 3 ("1" → 1)
add(1.5, 2);      // TypeError (1.5 не целое)
add("abc", 2);    // TypeError (не числовая строка)
add(null, 2);     // Deprecated in 8.0, TypeError in 9.0
```

---

## Типичные ловушки

### 1. `bool` — самое хитрое приведение

```php
function isEnabled(bool $flag): void
{
    echo $flag ? "on" : "off";
}

isEnabled("0");     // ✅ "off" — "0" → false (сильное правило PHP для bool)
isEnabled("false"); // ✅ "on"  — непустая строка → true!
isEnabled("");      // ✅ "off" — пустая строка → false
isEnabled("1");     // ✅ "on"  — "1" → true
```

Ловушка: `isEnabled("false")` — **on**. Потому что для `bool` PHP использует правило "пустая строка / `0` / `0.0` / `"0"` / `[]` / `null` / `false` → false, всё остальное → true". Строка `"false"` — непустая → `true`.

### 2. `array` — не структурный тип

```php
function process(array $items): void
{
    // $items может быть:
    // [1, 2, 3]
    // ["a" => "b"]
    // [[1,2], [3,4]]
    // [new User(), new Order()]
    // НЕТ способа уточнить в сигнатуре:
    // array<string, int>  — не существует
    // array<int, Order>   — не существует
    // list<int>           — только через docblock / PHPStan
}
```

PHP не имеет структурной типизации массивов. Единственный способ — `@param array<int, Order>` в docblock + PHPStan/Psalm. В нативной сигнатуре — только `array`.

### 3. `callable` — самый неоднозначный тип

```php
function execute(callable $fn): void
{
    // $fn может быть:
    // 'strlen'              — имя функции (string)
    // ['array', 'map']      — статический метод (array)
    // [$obj, 'method']      — инстанс-метод (array)
    // fn($x) => $x * 2     — closure
    // new class { public function __invoke(int $x): int { return $x; } }
    // (new ReflectionMethod(...))->getClosure(...)

    $result = $fn(42);
}
```

Никакой способ уточнить "callable который принимает int и возвращает string". Только docblock: `@param callable(int): string $fn`.

### 4. `mixed` — анти-тип

```php
function serialize(mixed $value): string
{
    // $value может быть:
    // null, int, float, string, bool, array, object, resource (8.0+)
    // mixed ≠ "что угодно" в смысле "типизировано"
    // mixed = "нет информации о типе"

    // PHPStan: $value->method() — ошибка, потому что mixed не имеет метода
    // Но: is_int($value) — после этого $value сужается до int
}
```

`mixed` в 8.0 — это **намеренная** "отсутствие типа". Не путать с `object` (любой объект, кроме `null`) или `?Type` (Type или `null`).

### 5. `self` vs `static` vs `parent`

```php
class Animal
{
    public function speak(): self   // всегда Animal (early binding)
    {
        return $this;
    }
}

class Dog extends Animal
{
    public function speak(): static  // Dog (late binding)
    {
        return $this;
    }
}

$d = new Dog();
$d->speak(); // тип: Dog, а не Animal
```

`self` — раннее связывание (класс, где объявлен метод). `static` — позднее (конкретный класс инстанса). `parent` — родитель класса, где объявлен метод.

### 6. `null` и `?Type` — не то же самое, что `Type|null`

```php
function a(?int $x): void {}        // null или int
function b(int|null $x): void {}    // то же самое (union, 8.0+)
function c(?string $x = null): void {} // nullable + default null
```

`?int` — синтаксический сахар для `int|null`. В union-типах порядок **косметический**: `int|string` = `string|int`. Но для PHPStan `never` всегда должен быть первым (или единственным), потому что он "поглощает" остальные.

### 7. Внутренние функции и strict_types

```php
<?php
declare(strict_types=1);

// До 8.0: strlen(123) — ок, возвращал 3 (приведение к "123")
// 8.0+:  TypeError!

strlen(123);   // TypeError в strict-файле
strlen("abc"); // ✅ 3

// Но! Некоторые внутренние функции до сих пор принимают несколько типов:
// array_merge, in_array, str_contains — принимают mixed
// Это документировано, но легко спутать с "всё принимается"
```

---

## Где типизация **спасает**

### Пример 1: Раннее обнаружение опечаток

```php
class Money
{
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}
}

function charge(Money $money, string $cardLast4): void
{
    // ...
}

charge(new Money(100, "usd"), "4242");   // ✅
charge(new Money(100, "usd"), 4242);     // TypeError — int вместо string
charge("100 USD", "4242");               // TypeError — string вместо Money
```

Без типизации: `"100 USD"` летит в `charge()`, и через 3 месяца в биллинге — потеря 50 000.

### Пример 2: Иммутабельность + типизация = безопасность

```php
readonly class Invoice
{
    public function __construct(
        public readonly string  $id,
        public readonly Money   $total,
        public readonly \DateTimeImmutable $dueDate,
    ) {}

    public function withTotal(Money $newTotal): static
    {
        return new static($this->id, $newTotal, $this->dueDate);
    }
}

$inv = new Invoice("INV-1", new Money(1000, "USD"), new \DateTimeImmutable("2025-01-01"));
$inv2 = $inv->withTotal(new Money(1500, "USD"));

$inv->total->amount; // 1000 — не изменилось
$inv2->total->amount; // 1500
```

Никто не может случайно `$inv->total->amount = 0`, потому что `readonly`.

### Пример 3: Exhaustiveness через `match` + enum

```php
enum PaymentMethod: string
{
    case Card   = 'card';
    case Bank   = 'bank';
    case Crypto = 'crypto';
}

function process(PaymentMethod $method): string
{
    return match ($method) {
        self::Card   => "charged",
        self::Bank   => "transferred",
        self::Crypto => "confirmed",
        // Нет default → если добавишь self::PayPal — compile-time warning от PHPStan
    };
}
```

---

## Где типизация **удивляет**

### 1. `int` vs `float` — нет общего супертипа

```php
<?php
declare(strict_types=1);

function f(int $x): void {}

f(1);     // ✅
f(1.0);   // TypeError! (1.0 — float, не int)
f(1.5);   // TypeError!

function g(float $x): void {}

g(1);     // ✅ (int → float, widening)
g(1.0);   // ✅
```

`int` и `float` **не** в union-связи "int ⊂ float". Это разные типы. Widening при возврате допустим, но не при аргументах в strict-режиме.

### 2. `null` — "магический" тип

```php
function h(?int $x): void {}

h(null);  // ✅
h(0);     // ✅
h("");    // TypeError (даже в weak mode: "" не число)

// Но:
function i(bool $x): void {}
i(null);  // Deprecated in 8.0, TypeError in 9.0
// null → false, но только для bool! Для int/float/string — TypeError
```

`null` имеет **специальное** правило приведения для `bool`, которого нет для других скаляров.

### 3. `array` — нет ковариантности

```php
function parent(array $items): void {}
function child(array $items): void { parent($items); }

// Невозможно:
function acceptsListOfOrders(array $orders): void {} // что это за array?
function acceptsListOfAnything(array $x): void {}    // то же самое
```

Нет способа выразить "массив Order" в нативном PHP. `array<int, Order>` — только в docblock.

### 4. `callable` и стрелки

```php
function call(callable $fn, int $arg): mixed
{
    return $fn($arg);
}

call('strlen', 5);        // ✅ 1 — но "strlen" это string, не функция
call(fn($x) => $x * 2, 5); // ✅ 10
call([new Foo(), 'bar'], 5); // ✅ — зависит от Foo::bar()
call('foo.bar', 5);       // TypeError — "foo.bar" не валидная callable-строка (в 8.0+)
```

`"foo.bar"` — в старых версиях PHP это могло интерпретироваться как `foo` → метод `bar`. В 8.0+ — TypeError, потому что ambiguous.

### 5. `void` — нельзя "забыть" return

```php
function f(): void
{
    return 42; // TypeError! void не принимает значение
}

function g(): void
{
    return; // ✅ — но это редундантно
}
```

И наоборот: нельзя объявить `: void` и потом попытаться использовать результат:

```php
$result = someVoidFunction(); // PHPStan: void, нельзя присвоить
```

### 6. `object` vs конкретный класс — ковариантность

```php
class Animal {}
class Dog extends Animal {}

function accept(object $o): void {}       // ✅ любой объект
function acceptAnimal(Animal $a): void {} // ✅ Animal или наследник

acceptAnimal(new Dog()); // ✅ — ковариантность для параметров (contravariance для аргументов)
```

Но **возврат** — ковариантность (подкласс допустим):

```php
class Animal { public function speak(): Animal { return $this; } }
class Dog extends Animal { public function speak(): Dog { return $this; } } // ✅
```

А **параметры** — контравариантность (суперкласс допустим):

```php
class Animal {}
class Dog extends Animal {}

class Base  { public function feed(Animal $a): void {} }
class Fancy extends Base { public function feed(object $o): void {} } // ✅ (object ⊃ Animal)
```

---

## Резюме: где PHP-типы сильны, а где слабы

| Сильно | Слабо |
|--------|-------|
| Скаляры + `readonly` + enum = безопасные DTO | `array` без структурной типизации |
| `match` + enum = exhaustiveness | `callable` — неоднозначен |
| `never` + guard-клаузы = чистый контроль потока | `null` — специальное правило только для `bool` |
| `int`/`float`/`string`/`bool` — явные контракты | `mixed` — "анти-тип", скрывает проблемы |
| `self`/`static` — контроль late binding | Нет generics, нет structural typing |

PHP-типы — это **контракт на границе** (функция/метод), а не **система типов в языке** (как в Rust или Haskell). Это достаточно для 90% бэкенд-задач, но для домен-логики со сложными вложенными структурами — докблоки + PHPStan/Psalm становятся обязательными.

> ⏱ 38.2s · prompt 3745 tok · output 4821 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос про проектирование работы с исключениями в PHP. Нужно показать практический, осмысленный подход: иерархии, контракты, оборачивание, антипаттерны. 

Структурирую ответ:
1. Философия — исключения как контракт, а не как "логика"
2. Иерархия — как строю, примеры
3. Оборачивание чужих исключений — когда и как
4. Контракт метода — что гарантирую вызывающему
5. Антипаттерны — конкретные, с кодом

Дам конкретные примеры кода, покажу, как это работает в связке с DDD и чистым кодом. Не буду просто перечислять — покажу мотивацию.

</details>

**Кандидат:**

## Философия

Исключение — это **контракт нарушен**, а не "что-то пошло не так". Каждый бросок исключения должен отвечать на вопрос: *"Что вызывающий код обязан сделать, чтобы приложение не сломалось?"*

Если ответ — "ничего, это внутренняя реализация" → это не исключение, это логика ошибки.

---

## Иерархия

Строю по принципу **один корень на приложение + доменные поддерева**:

```
AppException (абстрактная, extends \Exception)
├── Domain
│   ├── InvalidAmount
│   ├── InsufficientBalance
│   └── OrderAlreadyShipped
├── Infrastructure
│   ├── PaymentGatewayUnavailable
│   ├── PaymentGatewayTimeout
│   └── DatabaseWriteFailed
└── Validation
    ├── ValidationError (массив ошибок)
    └── MissingRequiredField
```

```php
abstract class AppException extends \Exception
{
    // Не наследуется от \RuntimeException намеренно.
    // Все наши исключения — "ожидаемые" (checked-подобные).
    // \RuntimeException / \Error — для "неожиданных".
}

final class InvalidAmount extends Domain
{
    private function __construct(
        private readonly int    $amount,
        private readonly string $currency,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function negative(int $amount, string $currency): self
    {
        return new self(
            $amount,
            $currency,
            sprintf('Amount %d %s is negative; expected >= 0', $amount, $currency),
        );
    }

    public static function zero(int $amount, string $currency): self
    {
        return new self(
            $amount,
            $currency,
            sprintf('Amount %d %s is zero; expected > 0', $amount, $currency),
        );
    }

    public function getAmount(): int { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }
}
```

Почему так:

- **`final` + `private constructor`** — нельзя случайно `new InvalidAmount("whatever")`. Только через фабричные методы. Это делает сообщение о ошибке **предсказуемым** — вызывающий знает, что `$ex->getAmount()` существует.
- **Несколько фабрик для разных случаев** — `negative` vs `zero` — вызывающий может `instanceof` и решить, что делать (логически они могут отличаться: "покажи предупреждение" vs "заблокируй").
- **Данные в исключении** — вызывающий не парсит строку. `$ex->getAmount()` даёт типизированное значение.

---

## Контракт метода

### Правило: каждый публичный метод обязан быть понятным по сигнатуре

```php
interface BalanceService
{
    /**
     * @throws InsufficientBalance — если баланса не хватает
     * @throws PaymentGatewayUnavailable — если внешний сервис не отвечает
     *
     * НЕ бросает:
     *   - InvalidAmount (валидация — ответственность вызывающего)
     *   - \Exception (никаких)
     */
    public function debit(string $accountId, Money $amount): DebitResult;
}
```

В PHP нет `throws` в сигнатуре (как в Java), поэтому контракт живёт в:

1. **Docblock** — формальный, машиночитаемый (PHPStan может проверить).
2. **Тесты** — фактический. Если метод бросает исключение, не описанное в docblock → это баг.
3. **Иерархия** — если бросается `Domain`, вызывающий знает: это "ожидаемая" ошибка, можно обработать. Если `PaymentGatewayUnavailable` — это инфраструктурная, может быть retry.

### Что **не** считается контрактом

```php
// Антипаттерн: "я бросу что-нибудь, если что-то пойдёт не так"
function process(): void
{
    $data = $this->externalApi->fetch();
    // ...
}
// Вызывающий не знает, что бросится: \Exception? \RuntimeException? TimeoutException?
```

Это "исключение как catch-all" — оно не несёт семантики.

---

## Оборачивание чужих исключений

### Принцип: на границе слоя — перехватить, на внутреннем — пробрасывать

```
HTTP Request
  → Controller (Symfony)
    → Application Service
      → Domain (чистый, без фреймворка)
```

**Правило:** каждый слой перехватывает исключения **нижнего** слоя и бросает **своё** исключение. Не ниже.

```php
// Domain — чистый, не знает про фреймворк
final class DebitCommand
{
    public function execute(): void
    {
        $balance = $this->balance->amount;
        if ($balance < $this->amount->amount) {
            throw InsufficientBalance::new(
                available: $balance,
                requested: $this->amount->amount,
            );
        }
    }
}
```

```php
// Application Service — оборачивает инфраструктурные ошибки
final class DebitUseCase
{
    public function __construct(
        private BalanceRepository $repo,
        private PaymentGateway    $gateway,
    ) {}

    public function execute(string $accountId, Money $amount): DebitResult
    {
        try {
            $balance = $this->repo->findBalance($accountId);
            $command = new DebitCommand($balance, $amount);
            $command->execute();
            $this->gateway->charge($accountId, $amount);
        } catch (InsufficientBalance $e) {
            // Пробрасываем — это доменная ошибка, вызывающий обязан знать
            throw $e;
        } catch (PaymentGatewayTimeout $e) {
            // Оборачиваем — вызывающий не должен знать, что у нас PaymentGateway
            throw new PaymentFailed(
                reason: 'timeout',
                original: $e,
            ) from $e;
        } catch (\Throwable $e) {
            // Ничего не "ловим" молча. Логируем и бросаем AppException
            error_log(sprintf('Uncaught in DebitUseCase: %s', $e));
            throw new PaymentFailed(
                reason: 'unknown',
                original: $e,
            ) from $e;
        }

        return DebitResult::success();
    }
}
```

### Зачем `from $e`

```php
throw new PaymentFailed('timeout', $e) from $e;
//                                       ^^^^^^
```

`from` сохраняет **trace** исходного исключения. В логах видишь:

```
PaymentFailed: timeout
  Caused by: PaymentGatewayTimeout: Upstream returned 504 after 30s
    at PaymentGateway::charge() on line 87
```

Без `from` — теряется stack trace "почему" таймаут произошёл.

### Когда **не** оборачивать

| Ситуация | Решение |
|----------|---------|
| Домен бросает `InsufficientBalance` | Пробрасываем как есть — это и есть контракт |
| Внешний сервис бросает `PaymentGatewayTimeout` | Оборачиваем в `PaymentFailed` — вызывающий не знает про gateway |
| `catch (\Exception)` без `instanceof` | **Антипаттерн** (см. ниже) |
| `catch (Throwable)` в контроллере | Допустимо — это **последняя** граница, преобразуем в HTTP 500 |

---

## Антипаттерны

### 1. Catch-all как "логика"

```php
// ❌
function processOrder(Order $order): void
{
    try {
        // 200 строк логики
    } catch (\Exception $e) {
        // "Что-то пошло не так" — и что?
        log($e);
        $this->queue->push('retry', $order);
    }
}
```

Проблема:
- `InsufficientBalance` — не ретраим, это доменная ошибка.
- `PaymentGatewayTimeout` — ретраим.
- `DatabaseWriteFailed` — ретраим.
- `InvalidAmount` — не ретраим, это баг вызывающего.

`catch (\Exception)` не может отличить. Результат: ретраим то, что не нужно, или не ретраим то, что нужно.

**Правильно:**

```php
try {
    $this->useCase->execute($order);
} catch (InsufficientBalance $e) {
    $order->markRejected($e->getMessage());
} catch (PaymentFailed $e) {
    $this->queue->push('retry', $order, $e->getReason());
}
// Если бросил что-то другое — это баг, пусть упадёт.
```

### 2. Исключение вместо return value

```php
// ❌ — "успех" и "не найдено" — два разных исхода, но одно представление
function findBalance(string $id): int
{
    $balance = $this->repo->find($id);
    if ($balance === null) {
        throw new BalanceNotFoundException($id);
    }
    return $balance->amount;
}

// Вызывающий:
try {
    $amount = $this->service->findBalance($id);
} catch (BalanceNotFoundException $e) {
    // ...
}
```

Это **допустимо**, если "не найдено" — действительно исключительная ситуация (баг вызывающего). Но если "не найдено" — нормальное состояние (например, "у пользователя ещё нет баланса") — лучше:

```php
// ✅ — "не найдено" — нормальный исход
function findBalance(string $id): Balance|null
{
    return $this->repo->find($id);
}

// Вызывающий:
$balance = $this->service->findBalance($id);
if ($balance === null) {
    // логика: "ещё нет" vs "был и удалён"
}
```

**Правило:** исключение — для **неожиданного** (нарушение инварианта). Return value — для **ожидаемого** (нормальное состояние системы).

### 3. Наследование от `\RuntimeException`

```php
// ❌
class InsufficientBalance extends \RuntimeException {}
class PaymentTimeout extends \RuntimeException {}
class InvalidAmount extends \RuntimeException {}

// Вызывающий:
catch (\RuntimeException $e) {
    // Это InsufficientBalance? PaymentTimeout? InvalidAmount?
    // Не может отличить без instanceof на каждый
}
```

Проблема: вызывающий вынужден делать `instanceof` на каждое. Если добавишь новое — забудешь.

**Правильно:** общий корень `AppException` → вызывающий может `catch (AppException $e)` и потом `instanceof` только если нужно различить.

### 4. Мутация состояния через исключение

```php
// ❌
function processOrder(Order $order): void
{
    try {
        $this->gateway->charge($order->id, $order->amount);
    } catch (PaymentTimeout $e) {
        $order->status = OrderStatus::FAILED; // ← мутация ДОМЕНА из-за инфраструктуры
        throw $e;
    }
}
```

Проблема: `Order` (домен) знает, что есть `PaymentTimeout` (инфраструктура). Нарушение DDD.

**Правильно:** домен сам решает статус. Инфраструктура сообщает **факт** ("таймаут"), домен **интерпретирует** ("статус → FAILED, причина → timeout").

```php
$command = new DebitCommand($order, $amount);
try {
    $command->execute();
} catch (PaymentFailed $e) {
    $order->markRejected(RejectionReason::fromInfrastructure($e->getReason()));
    // Order не знает про PaymentGateway, знает про RejectionReason (свой enum)
}
```

### 5. `@throws` в docblock, но нет в коде

```php
/**
 * @throws PaymentFailed
 */
public function charge(string $id, Money $amount): void
{
    // ...
    // Забыл throw. PHPStan не проверит — docblock не является контрактом.
}
```

PHPStan **не** проверит, что функция бросает то, что обещает. Он проверит обратное: "ты ловишь исключение, которое не может быть брошено".

**Правило:** docblock — документация. Код — факт. Если docblock обещает, а код не бросает → это ок для вызывающего (он не сломается), но это **баг документации**.

---

## Контрольный чеклист при проектировании

| Вопрос | Ответ |
|--------|-------|
| Кто бросает? | Только тот, кто знает причину |
| Кто ловит? | Ближайший слой, который может **решить** |
| Что делает ловящий? | Обрабатывает (retry, fallback, log) или пробрасывает вверх |
| Вызывающий знает про тип? | Да — из docblock или из иерархии |
| В исключении данные? | Да — вызывающий не парсит строку |
| Исключение мутит состояние? | Нет — только домен |
| Catch-all есть? | Только на **последней** границе (контроллер / CLI) |

---

## Итого

Исключения в PHP — это **контракт, а не механизм**. Если ты используешь их как "try-catch вместо if-else" — ты потерял смысл. Если используешь как "вот что нарушено и кто обязан это исправить" — это работает.

Главное разграничение:

- **Ожидаемая ошибка** (домен, валидация) → `AppException` иерархия → вызывающий **обязан** обработать.
- **Неожиданная ошибка** (баг, инфраструктура) → `\Throwable` на последней границе → лог + HTTP 500 + алерт.

Всё, что между — антипаттерн.

> ⏱ 27.9s · prompt 7587 tok · output 3539 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

The user is asking me to continue the interview. They want me to pick which SOLID principle is most commonly violated in real code, explain why, and show a typical violation with a refactoring example.

Let me think about which one is truly the most commonly violated in real PHP/enterprise code:

1. **S**ingle Responsibility - Violated everywhere, but it's vague and subjective. "What counts as one responsibility?" is debatable.

2. **O**pen/Closed - Hard to do in practice without excessive abstraction. People violate it by modifying existing classes instead of extending. But it's also often intentionally violated because OCP taken to extreme leads to over-abstraction.

3. **L**iskov Substitution - This is actually the most commonly violated AND the most harmful. People create subclasses that change behavior, add exceptions, or have different preconditions. In PHP specifically, this manifests as:
   - Subclasses that throw exceptions the parent doesn't
   - Subclasses that have different performance characteristics
   - Subclasses that change the meaning of the contract
   - The "null object" pattern that breaks LSP
   - `AbstractEntity` that does different things per subclass

4. **I**nterface Segregation - Very commonly violated in Symfony/enterprise PHP. Huge service interfaces that force implementors to implement methods they don't need. The "Fat Controller" / "God Service" pattern.

5. **D**ependency Inversion - Violated when concrete classes are hardcoded, when services know about each other directly.

I think **Liskov Substitution Principle (LSP)** is the best answer for a senior-level interview because:

1. It's the most subtle and most commonly violated in real code
2. It's the one that causes the most insidious bugs (things "work" until they don't)
3. It connects to real PHP patterns people use (inheritance vs composition)
4. It shows depth of understanding beyond surface-level SOLID
5. It's the principle that people "know" but don't actually understand

Let me think of a really good, realistic example in PHP/Symfony context:

A classic one: `PaymentProcessor` hierarchy where `StripeProcessor` works fine but `CryptoProcessor` behaves differently (async, can fail differently, has different timeouts). Code that does `foreach ($processors as $p) { $p->process($order) }` breaks because CryptoProcessor throws a different exception or returns differently.

Another classic: `AbstractRepository` in Doctrine where a specific repository overrides `save()` to add side effects (caching, event dispatching) that the base class doesn't do. Code that depends on the base contract is surprised.

Let me go with a really concrete, realistic example: a `NotificationSender` interface with `EmailSender`, `SmsSender`, `PushSender`. The LSP violation: `SmsSender` has a rate limit that throws `RateLimitExceeded`, but `EmailSender` doesn't. Code that iterates over senders and calls `send()` doesn't expect this exception from one but not the other.

Actually, let me think of something even more realistic and common in enterprise PHP:

**The "AbstractService that does logging/side-effects" pattern:**

```php
abstract class BaseService {
    protected function log(string $msg) { /* default: no-op */ }
    protected function validate(array $data) { /* basic validation */ }
}

class OrderService extends BaseService {
    protected function log(string $msg) { /* writes to dedicated order log */ }
}

class BillingService extends BaseService {
    protected function log(string $msg) { /* sends to audit system */ }
    protected function validate(array $data) { /* adds tax validation */ }
}
```

This violates LSP because:
- `BillingService::validate()` has different behavior than `BaseService::validate()`
- Code that calls `$service->validate()` can't rely on consistent behavior
- `log()` does fundamentally different things in different subclasses

But this might be too abstract. Let me go with something more concrete and relatable.

**Best example for PHP/Symfony context: The "Strategy" that isn't a strategy.**

A `DiscountCalculator` with subclasses:
- `PercentageDiscount` - works fine
- `LoyaltyDiscount` - throws `LoyaltyProgramUnavailable` when the loyalty API is down
- `B2BDiscount` - requires additional context (company ID) in the constructor, and if not provided, silently returns 0

The calling code:
```php
foreach ($calculators as $calc) {
    $discount = $calc->calculate($order);
}
```

This breaks LSP because:
1. `LoyaltyDiscount` can throw an exception the others don't → caller doesn't handle it
2. `B2BDiscount` silently returns 0 when context is missing → different contract

Let me write a thorough, well-structured answer. I'll pick LSP because it's the most insightful answer for a senior interview, and I'll show a realistic PHP example with before/after refactoring.

I'll structure it as:
1. Which principle and why (concise reasoning)
2. Why it's the most violated (concrete reasons)
3. A realistic before example
4. The refactoring (after)
5. Why the refactored version is better (connecting back to LSP)

</details>

**Кандидат:**

## Liskov Substitution Principle (LSP)

Из пяти принципов — самый часто нарушаемый и самый коварный. Почему именно он:

1. **Нарушение незаметно.** Код компилируется, тесты проходят, а баг проявляется только когда конкретная реализация попадает в путь, которого разработчик не ожидал.
2. **PHP поощряет наследование.** `extends` — единственный способ "расширить" без композиций (до `readonly` и до enum'ов). Люди используют его, где нужно было бы `implements`.
3. **LSP — самый неочевидный.** S, O, I, D — можно сформулировать одним предложением. LSP требует понимания **контракта**, **преусловий**, **постусловий** — это ближе к формальной методологии, чем к "правилам стиля".

---

## Что нарушает LSP (кратко)

Подкласс должен быть **подставим** на место родительского типа без изменения корректности программы. Нарушение — когда подкласс:

- Бросает исключение, которого родитель не обещает
- Изменяет поведение метода (pre/postcondition)
- Требует дополнительный контекст, который у родителя не нужен
- Молча возвращает "пустое" вместо "реального"

---

## Типовое нарушение: DiscountCalculator

### Before — код, который пишут на реальных проектах

```php
interface DiscountCalculator
{
    public function calculate(Order $order): Money;
    public function applyLabel(): string;
}

class PercentageDiscount implements DiscountCalculator
{
    public function __construct(private int $percent) {}

    public function calculate(Order $order): Money
    {
        return $order->total->multiply($this->percent / 100);
    }

    public function applyLabel(): string
    {
        return "Percent ({$this->percent}%)";
    }
}

class LoyaltyDiscount implements DiscountCalculator
{
    public function __construct(private LoyaltyClient $loyaltyClient) {}

    public function calculate(Order $order): Money
    {
        // Бросает LoyaltyProgramUnavailable, когда внешний API не отвечает.
        // PercentageDiscount такого никогда не сделает.
        $points = $this->loyaltyClient->getPoints($order->customerId);
        return $order->total->multiply($points / 10_000);
    }

    public function applyLabel(): string
    {
        return "Loyalty points";
    }
}

class B2BDiscount implements DiscountCalculator
{
    // Требует additional context. Если его нет — молча возвращает 0.
    public function __construct(private ?CompanyId $companyId = null) {}

    public function calculate(Order $order): Money
    {
        if ($this->companyId === null) {
            return new Money(0, $order->total->currency); // ← молча "нет скидки"
        }

        $rate = $this->companyId->b2bRate;
        return $order->total->multiply($rate);
    }

    public function applyLabel(): string
    {
        return $this->companyId?->name ?? "B2B (unresolved)";
    }
}
```

Вызывающий код:

```php
class CheckoutService
{
    public function __construct(
        private array $calculators, // DiscountCalculator[]
    ) {}

    public function calculateTotal(Order $order): Money
    {
        $totalDiscount = new Money(0, $order->total->currency);

        foreach ($this->calculators as $calc) {
            $discount = $calc->calculate($order); // ← здесь всё "ломается"
            $totalDiscount = $totalDiscount->add($discount);
        }

        return $order->total->subtract($totalDiscount);
    }
}
```

### Что ломается

| Сценарий | Почему ломается |
|----------|----------------|
| Loyalty API упал | `LoyaltyDiscount::calculate()` бросает `LoyaltyProgramUnavailable`. `CheckoutService` не знает об этом исключении. Весь чекаут падает с 500. |
| B2B без companyId | `B2BDiscount::calculate()` молча возвращает 0. Покупатель платит полную цену, но в логах написано "B2B (unresolved)". Никто не заметит. |
| Новый разработчик | Видит `DiscountCalculator` и думает: "я могу добавить `BlackFridayDiscount` и подставить в массив". Да, может. Но не знает, что его `calculate()` не будет бросать, а `B2BDiscount` — бросает. |

Это **нарушение LSP**: `LoyaltyDiscount` и `B2BDiscount` не подставляемы на место "любого DiscountCalculator", потому что их контракты различаются.

---

## После — рефакторинг

### Шаг 1: Разделить "что это" от "как это считается"

Ключевая идея: **LSP — про контракт, а не про тип**. Если два класса имеют разные контракты — это разные типы.

```php
// 1. Базовый контракт: "считает скидку, не бросает"
interface DiscountCalculator
{
    public function calculate(Order $order): Discount;

    public function label(): string;
}

// 2. Результат — не Money, а объект с семантикой
final class Discount
{
    public function __construct(
        public readonly Money  $amount,
        public readonly string $source,
        public readonly bool   $isApplied,
    ) {}

    public static function zero(string $currency, string $source): self
    {
        return new self(new Money(0, $currency), $source, false);
    }
}
```

Теперь `calculate()` **гарантированно возвращает `Discount`** и **не бросает исключение**. Если внешний сервис недоступен — возвращаем `Discount::zero(...)` с пометкой `isApplied: false`.

### Шаг 2: Каждый калькулятор соблюдает контракт

```php
final class PercentageDiscount implements DiscountCalculator
{
    public function __construct(private int $percent) {}

    public function calculate(Order $order): Discount
    {
        $amount = $order->total->multiply($this->percent / 100);
        return new Discount($amount, "percent_{$this->percent}", true);
    }

    public function label(): string
    {
        return "Percent ({$this->percent}%)";
    }
}

final class LoyaltyDiscount implements DiscountCalculator
{
    public function __construct(
        private LoyaltyClient $loyaltyClient,
        private LoggerInterface $logger,
    ) {}

    public function calculate(Order $order): Discount
    {
        try {
            $points = $this->loyaltyClient->getPoints($order->customerId);
            $amount = $order->total->multiply($points / 10_000);
            return new Discount($amount, "loyalty", true);
        } catch (LoyaltyUnavailable $e) {
            // Не бросаем. Возвращаем "скидка не применена".
            $this->logger->warning('Loyalty discount skipped', ['error' => $e->getMessage()]);
            return Discount::zero($order->total->currency, 'loyalty_unavailable');
        }
    }

    public function label(): string
    {
        return "Loyalty points";
    }
}

final class B2BDiscount implements DiscountCalculator
{
    public function __construct(private CompanyId $companyId) // ← required, не nullable

    {
    }

    public function calculate(Order $order): Discount
    {
        $amount = $order->total->multiply($this->companyId->b2bRate);
        return new Discount($amount, "b2b_{$this->companyId}", true);
    }

    public function label(): string
    {
        return "B2B ({$this->companyId->name})";
    }
}
```

Ключевые изменения:

| Было | Стало | Почему |
|------|-------|--------|
| `LoyaltyDiscount` бросает `LoyaltyUnavailable` | Ловит внутри, возвращает `Discount::zero` | Контракт `calculate()` — "вернуть результат", не "бросить" |
| `B2BDiscount` принимает `?CompanyId` | Принимает `CompanyId` (required) | Не существует "B2B без компании" — это другой сценарий |
| `B2BDiscount` молча возвращает 0 | `Discount::zero(...)` с пометкой `isApplied: false` | Вызывающий может отличить "скидки нет" от "скидка в 0" |
| Возврат `Money` | Возврат `Discount` | Семантика: "сколько", "откуда", "применилась ли" |

### Шаг 3: Вызывающий код — безопасен

```php
final class CheckoutService
{
    public function __construct(
        /** @var DiscountCalculator[] */
        private array $calculators,
        private EventDispatcherInterface $events,
    ) {}

    public function calculateTotal(Order $order): Money
    {
        $total = new Money(0, $order->total->currency);
        $skipped = [];

        foreach ($this->calculators as $calc) {
            $discount = $calc->calculate($order); // ← никогда не бросит

            if ($discount->isApplied) {
                $total = $total->add($discount->amount);
            } else {
                $skipped[] = $discount->source;
            }
        }

        if ($skipped) {
            $this->events->dispatch(new DiscountsPartiallySkippedEvent($skipped));
        }

        return $order->total->subtract($total);
    }
}
```

Теперь:

- Никто не бросит исключение из `calculate()` — контракт соблюдён.
- `CheckoutService` знает, что некоторые скидки не применились — может показать пользователю.
- `B2BDiscount` без `CompanyId` — **не существует**. Компоновщик не создаст объект без него.
- Если добавишь `BlackFridayDiscount` — просто подставь в массив. ЛSP соблюдён: он не бросает, возвращает `Discount`.

---

## Почему именно LSP, а не OCP

Честно: люди часто путают LSP и OCP. Разберу:

| Принцип | Формулировка | Типовое нарушение |
|---------|-------------|-------------------|
| **OCP** | Модули открыты для расширения, закрыты для модификации | Меняешь существующий класс вместо добавления нового |
| **LSP** | Подкласс подставим на место родителя | Подкласс ведёт себя иначе, чем родитель |

**OCP** — про **процесс** (как добавлять функциональность). **LSP** — про **контракт** (что гарантировано).

В примере выше:

- **OCP** не нарушен — мы добавили новый тип (`LoyaltyDiscount`), не меняя `PercentageDiscount`.
- **LSP** нарушен — `LoyaltyDiscount` не подставляем на место "любого DiscountCalculator", потому что его поведение отличается.

Можно соблюдать OCP и нарушать LSP одновременно. Это и есть типичная ситуация в реальном коде: "я добавил класс, не трогал старый — OCP ок, а потом всё упало, потому что новый класс ведёт себя иначе".

---

## Короткий чеклист: "Не нарушает ли мой подкласс LSP?"

Перед тем как написать `class X extends Y` или `implements I`:

1. **Может ли `X` бросить исключение, которое `Y` не бросает?** → Если да — LSP нарушен.
2. **Требует ли `X` аргумент, который у `Y` не нужен?** → Если да — LSP нарушен.
3. **Возвращает ли `X` "пустое" там, где `Y` возвращает "реальное"?** → Если да — LSP нарушен.
4. **Можно ли подставить `X` в любой код, который ожидает `Y`, и всё продолжит работать?** → Если нет — LSP нарушен.

Если хотя бы один ответ "да" или "нет" → нужен не подкласс, а **новый тип** или **композиция**.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Короткий ответ

Да. SOLID — эвристики, сформулированные в контексте Java/C# 2000-х. В PHP, с его слабой типизацией, отсутствием generics и другим балансом между expressiveness и ceremony, некоторые принципы дают отрицательную пользу, если применять их механически.

Ниже — один пример, который я считаю наиболее типичным.

---

## Пример: нарушение OCP через `match`

### Что говорит OCP

> Модуль должен быть открыт для расширения, но закрыт для модификации.

Дословно: если ты добавляешь новый тип — не трогай существующий код.

### Как это выглядит "по-книжному"

```php
interface PaymentHandler
{
    public function handle(Payment $payment): PaymentResult;
}

final class CardHandler implements PaymentHandler
{
    public function handle(Payment $payment): PaymentResult
    {
        // 40 строк логики: 3DS, tokenization, idempotency key, ...
    }
}

final class BankTransferHandler implements PaymentHandler
{
    public function handle(Payment $payment): PaymentResult
    {
        // 35 строк: SWIFT-код, коридор, валютный контроль, ...
    }
}

final class CryptoHandler implements PaymentHandler
{
    public function handle(Payment $payment): PaymentResult
    {
        // 50 строк: сеть, gas, nonce, ...
    }
}

// + Factory + Registry + DI-конфигурация
// Итого: 6 файлов, ~200 строк, 3 интерфейса для реализации

class PaymentService
{
    public function __construct(
        /** @var PaymentHandler[] */
        private array $handlers,
        private PaymentMethodRegistry $registry,
    ) {}

    public function process(Payment $payment): PaymentResult
    {
        $handler = $this->registry->handlerFor($payment->method);
        return $handler->handle($payment);
    }
}
```

Добавляешь новый метод оплаты → пишешь новый класс, регистрируешь в DI. Существующий код не трогаешь. OCP соблюдён.

### Как это выглядит в реальном проекте

У нас 7 методов оплаты. Логика каждого — 15–25 строк (валидация + вызов внешнего API + маппинг ответа). Общий код: `match`.

```php
final class PaymentService
{
    public function __construct(
        private CardGateway $cardGateway,
        private BankGateway $bankGateway,
        private CryptoGateway $cryptoGateway,
        private WalletGateway $walletGateway,
        private LoggerInterface $logger,
    ) {}

    public function process(Payment $payment): PaymentResult
    {
        $this->logger->info('payment.started', [
            'id' => $payment->id,
            'method' => $payment->method->value,
            'amount' => $payment->amount->amount,
        ]);

        $result = match ($payment->method) {
            PaymentMethod::Card      => $this->processCard($payment),
            PaymentMethod::Bank      => $this->processBank($payment),
            PaymentMethod::Crypto    => $this->processCrypto($payment),
            PaymentMethod::Wallet    => $this->processWallet($payment),
            PaymentMethod::GiftCard  => $this->processGiftCard($payment),
            PaymentMethod::Installment => $this->processInstallment($payment),
            PaymentMethod::Prepaid   => $this->processPrepaid($payment),
        };

        $this->logger->info('payment.finished', [
            'id' => $payment->id,
            'status' => $result->status->value,
        ]);

        return $result;
    }

    private function processCard(Payment $payment): PaymentResult
    {
        $response = $this->cardGateway->charge(
            token: $payment->cardToken,
            amount: $payment->amount,
            idempotencyKey: $payment->id,
        );

        return PaymentResult::fromGatewayResponse($response, $payment);
    }

    // ... 6 аналогичных методов по 15–25 строк
}
```

Добавляешь новый метод оплаты → добавляешь `case` в `match`, пишешь приватный метод. **Трогаешь существующий класс.** OCP формально нарушен.

---

## Почему это оправданно

### 1. Стоимость "соблюдения" превышает выгоду

| | `match` | Strategy + Factory |
|---|---------|-------------------|
| Файлов | 1 | 9 (7 классов + интерфейс + factory + registry) |
| Строк (примерно) | ~250 | ~400 |
| Время на добавление нового метода | 5 мин | 30 мин (класс + factory + DI-тег + тест) |
| Ctrl+Click до реализации | 1 клик | 2–3 клика (через интерфейс → factory → реализация) |
| Когнитивная нагрузка | Низкая: "где обработчик?" → тут | Высокая: "где обработчик?" → в factory → в registry → в классе |

При 7 методах, каждый по 20 строк, Strategy-паттерн — это **обёртка ради обёртки**.

### 2. LSP-защита уже есть

```php
enum PaymentMethod: string
{
    case Card      = 'card';
    case Bank      = 'bank';
    case Crypto    = 'crypto';
    case Wallet    = 'wallet';
    case GiftCard  = 'gift_card';
    case Installment = 'installment';
    case Prepaid   = 'prepaid';
}
```

`match` без `default` + PHPStan = если добавишь `case PayByVoice` в enum — **анализатор покажет, что `match` не покрыт**. Это та же защитная сетка, что даёт Strategy, но без 9 файлов.

### 3. "Закрытость для модификации" — иллюзия

OCP говорит: "не модифицируй." Но:

- Каждый `case` в `match` — это **добавление**, не модификация. Существующие `case` не меняются.
- Приватные методы `processCard`, `processBank` — не зависят друг от друга. Изменение `processCard` не влияет на `processBank`.
- Фактически класс уже "разбит" на независимые блоки. Strategy-паттерн просто переносит эти блоки в отдельные файлы.

Разница — **физическая локализация кода**, а не логическая. А физическая локализация — вопрос вкуса и размера кодовой базы.

### 4. Частота изменений — ключевой фактор

| Частота добавления метода оплаты | Что оправдано |
|---|---|
| 1 раз в 2–3 года | `match`. Strategy — ceremony. |
| 2–3 раза в год | Граница. `match` всё ещё ок, но начни следить за размером. |
| Еженедельно (платёжный провайдер, плагины) | Strategy. Модификация общего файла — риск конфликтов в git. |

У нас — 1 раз в 6–8 месяцев. `match` оправдан.

---

## Где нарушение **перестаёт** быть оправданным

Конкретные триггеры:

1. **Метод обработки вырос до 100+ строк.** Теперь `PaymentService` — 800 строк. `match` на вершине, а под ним — 7 методов по 100 строк. Это уже не "один класс", это "семь классов в одном файле." Strategy начинает окупаться.

2. **Несколько команд работают над разными методами.** `match` — общий файл. Два разработщика меняют `processCard` и `processCrypto` одновременно → merge conflict. Strategy → каждый в своём файле, конфликтов нет.

3. **Метод оплаты требует сложного жизненного цикла** (очередь, retry, saga). Тогда `processCrypto` превращается из 25 строк в состояние с колбэками. В этот момент это уже не "обработчик," а "state machine" — и ему нужен отдельный класс, не потому что OCP, а потому что **сложность требует инкапсуляции**.

4. **Платёжный метод — плагин.** Если новые методы оплаты добавляют внешние провайдеры (не наша команда) — им нельзя заставлять их трогать `PaymentService`. Strategy + DI — единственный способ.

---

## Мета-наблюдение: SOLID в PHP

SOLID сформулированы для языков с **номинальной типизацией** и **сильной системой наследования**. PHP:

- Нет generics → `PaymentHandler[]` — это `array`, не `List<PaymentHandler>`. Strategy теряет часть своей типобезопасности.
- Нет sealed classes → "закрытость" интерфейса нельзя выразить в типе.
- Нет structural typing → "соответствие контракту" проверяется только через `implements`.
- Есть `readonly`, `enum`, `match` — и это **заменяет** часть того, что в Java делается через Strategy + Factory.

В результате: часть "книжного" SOLID в PHP **уже покрыта** языковыми конструкциями (`match` + enum = exhaustive strategy, `readonly` = immutability). Дублировать их через паттерны — ceremony.

---

## Итого

| Вопрос | Ответ |
|--------|-------|
| Можно ли нарушать SOLID? | Да. Это эвристики, а не аксиомы. |
| Какое нарушение я считаю наиболее оправданным? | OCP через `match`/`switch`, когда логика каждого ветвления < 50 строк и частота изменений низкая. |
| Как узнать, что нарушение стало проблемой? | Класс вырос за 500 строк, или несколько команд пишут в один файл, или логика ветвления требует состояния. |
| Есть ли универсальное правило? | Нет. Есть правило: **"Нарушай принцип, если стоимость соблюдения (количество файлов, ceremony, когнитивная нагрузка) превышает стоимость модификации (риск конфликтов, потеря читаемости)."** |

В конце дня SOLID — это про **управление стоимостью изменения кода**. Если `match` делает изменение дешевле, чем Strategy — `match` правильнее, несмотря на "книжку."

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## Механизмы OCP без `extends`

OCP говорит: *"добавь новое поведение, не меняя существующий код."* Наследование — один из способов, но далеко не единственный. Ниже — от "ближе к классическому" до "максимально динамического".

---

## 1. Композиция + интерфейс

Самый распространённый. `implements` — не наследование в строгом смысле (нет `extends`), но это всё-таки **субтипизация**. Ключевое отличие от наследования: нет "потенциальных конфликтов" между методами родителя и подкласса.

```php
interface NotificationChannel
{
    public function send(string $to, string $message): void;
}

final class EmailChannel implements NotificationChannel
{
    public function __construct(private Mailer $mailer) {}

    public function send(string $to, string $message): void
    {
        $this->mailer->send($to, $message);
    }
}

final class TelegramChannel implements NotificationChannel
{
    public function __construct(private TelegramBot $bot) {}

    public function send(string $to, string $message): void
    {
        $this->bot->sendMessage(chatId: $to, text: $message);
    }
}

// Потребитель — не знает о конкретных каналах
final class Notifier
{
    /** @param NotificationChannel[] */
    public function __construct(private array $channels) {}

    public function notify(string $to, string $message): void
    {
        foreach ($this->channels as $channel) {
            $channel->send($to, $message);
        }
    }
}
```

Новый канал → новый класс + регистрация в DI. `Notifier` не трогаем.

**Когда лучше наследования:** поведение не зависит от "общего состояния" — каждый канал самодостаточен.

**Когда хуже:** каналы делят общую логику (retry, rate-limit, template) → возникает "diamond problem", которую `extends` решает бесплатно, а композиция — через отдельный `RateLimiter`, `RetryPolicy` и т.д.

---

## 2. Registry (map по ключу)

Подтипизация **полностью отсутствует**. Поведение — значение в массиве.

```php
final class SerializerRegistry
{
    /** @var array<string, callable(mixed): string> */
    private array $handlers = [];

    public function register(string $type, callable $serializer): void
    {
        $this->handlers[$type] = $serializer;
    }

    public function serialize(mixed $value): string
    {
        $type = $value::class;

        if (!isset($this->handlers[$type])) {
            throw new UnknownTypeException($type);
        }

        return ($this->handlers[$type])($value);
    }
}

// Регистрация — в отдельном файле, в bootstrap, в тесте:
$registry->register(User::class, fn (User $u) => json_encode([
    'id' => $u->id,
    'name' => $u->name,
]));

$registry->register(Order::class, fn (Order $o) => json_encode([
    'id' => $o->id,
    'total' => $o->total->amount,
]));

// Новый тип — одна строка регистрации. Ноль изменений в SerializerRegistry.
```

**Когда лучше:** количество вариантов растёт быстро (10+), каждый вариант — короткая функция, нет общего состояния.

**Когда хуже:** нет IDE-поддержки (автодополнение не знает, какие key'и зарегистрированы). Нет compile-time проверки, что для всех типов есть handler. Нет способа выразить "этот тип **обязан** иметь handler".

---

## 3. Callable-пайплайн (higher-order functions)

Поведение — **функция**. Никаких классов, интерфейсов, наследования.

```php
final class Pipeline
{
    /** @var array<callable(mixed): mixed> */
    private array $stages = [];

    public function add(callable $stage): self
    {
        $this->stages[] = $stage;
        return $this;
    }

    public function execute(mixed $input): mixed
    {
        return array_reduce($this->stages, function ($carry, $stage) {
            return $stage($carry);
        }, $input);
    }
}

// Конфигурация конвейера — в одном месте:
$csv = (new Pipeline())
    ->add(fn (string $raw): array  => str_getcsv($raw))
    ->add(fn (array $rows): array  => array_filter($rows, fn ($r) => $r[0] !== '#'))
    ->add(fn (array $rows): array  => array_map(fn ($r) => User::fromRow($r), $rows))
    ->add(fn (array $users): int   => User::bulkInsert($users));

$csv->execute($fileContents);
```

Новый этап → `->add(...)` в конфигурации. `Pipeline` не трогаем.

**Когда лучше:** этапы **линейны** и **не зависят** друг от друга по типу (каждый принимает `mixed` / `array`). Прототипирование.

**Когда хуже:** этапы имеют разную сигнатуру (`string → array → Entity[] → int`) — `Pipeline` не может проверить, что выход предыдущего = вход следующего. Только runtime.

---

## 4. Event-driven (pub/sub)

ОCP в максимальной форме: **издатel не знает о подписчиках вообще**.

```php
// Издатel — не меняется никогда
final class OrderService
{
    public function __construct(private EventDispatcherInterface $events) {}

    public function confirm(Order $order): void
    {
        $order->markConfirmed();
        $this->repo->save($order);

        $this->events->dispatch(new OrderConfirmedEvent($order));
        // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
        // Издатel не знает, кто это обработает.
        // Может быть 0 подписчиков. Может быть 10. Издатel не трогает.
    }
}

// Подписчики — отдельные классы, регистрируются в DI
final class SendEmailOnConfirm implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [OrderConfirmedEvent::class => 'onOrderConfirmed'];
    }

    public function onOrderConfirmed(OrderConfirmedEvent $event): void
    {
        $this->mailer->send($event->order()->customer()->email(), 'Thank you!');
    }
}

final class DeductInventoryOnConfirm implements EventSubscriberInterface
{
    // ...
}

// Новый подписчик → новый класс + tag. OrderService не трогает.
```

**Когда лучше:** N независимых реакций на одно событие, и они **не должны** быть атомарны.

**Когда хуже:** порядок выполнения не гарантирован. Тестирование сложнее (нужен fake dispatcher). Отладка: "почему email не ушёл?" → иди ищи подписчика.

---

## 5. DI-теги (framework-специфично)

Специфично для Symfony/Laravel, но это **реальный** механизм OCP.

```php
// Service — не знает о конкретных реализациях
final class ReportGenerator
{
    /** @param iterable<ReportRenderer> $renderers */
    public function __construct(
        #[Autowire(tag: 'report.renderer')]
        private iterable $renderers,
    ) {}

    public function generate(Report $report): string
    {
        $parts = [];
        foreach ($this->renderers as $renderer) {
            $parts[] = $renderer->render($report);
        }
        return implode("\n", $parts);
    }
}

// Регистрация нового рендерера — только в config:
// services.yaml:
# App\Renderer\PdfRenderer:
#   tags: ['report.renderer']
```

Новый рендерер → новый класс + `tags: ['report.renderer']`. `ReportGenerator` не трогает.

**Когда лучше:** количество реализаций > 3, и они не требуют сложного состояния.

**Когда хуже:** порядок — не гарантирован (нужен `priority`). Отладка: "какие именно рендереры подставлены?" → читай DI-контейнер.

---

## 6. Configuration-driven (поведение из данных)

Максимальная "закрытость": код **не знает** о вариантах. Варианты — строки в БД / YAML.

```php
// В БД:
// rules: [
//   { id: 1, condition: "amount > 1000", action: "manual_review" },
//   { id: 2, condition: "amount > 5000", action: "compliance_check" },
//   { id: 3, condition: "country = 'RU'", action: "sanctions_screen" },
// ]

final class RuleEngine
{
    public function __construct(
        private RuleRepository $rules,
        private ActionDispatcher $dispatcher,
    ) {}

    public function evaluate(Order $order): array
    {
        $triggered = [];

        foreach ($this->rules->all() as $rule) {
            if ($this->evaluateCondition($rule->condition, $order)) {
                $triggered[] = $rule->action;
            }
        }

        return $triggered;
    }
}
```

Новое правило → запись в БД. **Ноль кода.**

**Когда лучше:** правила меняет **бизнес** (не разработчики), часто.

**Когда хуже:** `evaluateCondition` — это mini-DSL или `eval()`. Отладка ужасна. Тестирование — только через данные. Типы отсутствуют.

---

## Сравнение

| Механизм | Подтипизация | IDE-поддержка | Compile-time safety | Сложность отладки | Когда выбирать |
|----------|:---:|:---:|:---:|:---:|---------|
| Композиция + interface | Да (interface) | ✅ | ✅ | Низкая | 2–10 вариантов, общий контракт |
| Registry (map) | Нет | ❌ | ❌ | Средняя | 5–50 коротких вариантов |
| Callable-пайплайн | Нет | ⚠️ | ❌ | Средняя | Линеарные этапы, прототип |
| Event-driven | Нет | ⚠️ | ❌ | Высокая | N независимых реакций |
| DI-теги | Да (interface) | ✅ | ✅ | Низкая | Symfony/Laravel, 3+ реализаций |
| Config-driven | Нет | ❌ | ❌ | Высокая | Правила меняет бизнес |

---

## Общее наблюдение

Все эти механизмы — это **разные способы выразить "расширяемость"**. Они лежат на спектрe:

```
Статическая типизация ←————————————————→ Динамическая расширяемость
     Композиция         Registry       Config-driven
     (сильные гарантии)  (слабые гарантии,
                           максимальная гибкость)
```

Чем левее — тем безопаснее, но тем больше ceremony. Чем правее — тем гибче, но тем больше риск, что "расширение" сломает то, что не проверял компилятор.

В реальной системе — **комбинация**. Критический домен — композиция + interface. Периферия (логгеры, рендереры) — DI-теги. Динамические правила — config-driven. И это нормально.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — про "причину для изменения", а не про "один класс"

Классическая формулировка: *"У класса должна быть только одна причина меняться."* Но класс — это просто **контейнер**. Причина для изменения может принадлежать функции, модулю, файлу, переменной, даже таблице БД.

---

## SRP на разных уровнях

### 1. Функция

```php
// ❌ Две причины меняться:
//   1. Изменился формат импорта (CSV → JSON)
//   2. Изменились правила валидации
function importUsers(string $file): void
{
    // Причина 1: парсинг
    if (str_ends_with($file, '.csv')) {
        $rows = str_getcsv(file_get_contents($file));
    } elseif (str_ends_with($file, '.json')) {
        $rows = json_decode(file_get_contents($file), true);
    } else {
        throw new UnsupportedFormat($file);
    }

    // Причина 2: валидация
    foreach ($rows as $row) {
        if (strlen($row['name']) < 2) { throw new ...; }
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) { throw new ...; }
        if ($row['age'] < 18 || $row['age'] > 120) { throw new ...; }
    }

    // Причина 3: сохранение
    User::bulkInsert($rows);
}
```

Три причины → три функции:

```php
/** @return array<int, array<string, string>> */
function parse(string $file): array { /* ... */ }

/** @param array<int, array<string, string>> */
function validate(array $rows): void { /* ... */ }

/** @param array<int, array<string, string>> */
function persist(array $rows): void { /* ... */ }

// Композиция:
function importUsers(string $file): void
{
    persist(validate(parse($file)));
}
```

Теперь:
- Изменили формат → трогаете только `parse`.
- Изменили правила валидации → трогаете только `validate`.
- Поменяли БД → трогаете только `persist`.

### 2. Файл / модуль

```php
// ❌ app/Http/Controllers/BillingController.php — 400 строк
// Причины меняться:
//   1. Изменился API биллингового провайдера (Stripe → Paddle)
//   2. Изменилась логика расчёта подписки
//   3. Изменился формат invoice (PDF → HTML)
//   4. Изменилась авторизация (JWT → OAuth2)

// ✅ Разделить по причинам:
// app/Services/Billing/PaymentGateway.php     — причина 1
// app/Services/Billing/SubscriptionCalculator.php — причина 2
// app/Services/Billing/InvoiceRenderer.php     — причина 3
// app/Http/Middleware/Authorize.php           — причина 4
// app/Http/Controllers/BillingController.php  — только оркестрация
```

### 3. Переменная / параметр

```php
// ❌ Одна переменная несёт две причины:
$config = [
    'db_host' => 'localhost',
    'db_user' => 'app',
    'stripe_key' => 'sk_live_...',
    'telegram_bot_token' => '123456:ABC...',
    'max_retries' => 3,
    'log_level' => 'debug',
];

// Причины:
//   1. Поменяли БД → трогаете 'db_*'
//   2. Поменяли Stripe → трогаете 'stripe_key'
//   3. Поменяли Telegram → трогаете 'telegram_bot_token'
//   4. Поменяли стратегию retry → трогаете 'max_retries'
//   5. Поменяли логирование → трогаете 'log_level'

// ✅ Разделить:
final class DatabaseConfig { /* host, user, password */ }
final class StripeConfig  { /* key, webhook_secret */ }
final class BotConfig     { /* telegram_token, slack_webhook */ }
final class RuntimeConfig { /* max_retries, log_level, timeout */ }
```

### 4. Таблица БД

```sql
-- ❌ Одна таблица, две причины:
CREATE TABLE users (
    id          BIGINT PRIMARY KEY,
    email       VARCHAR(255),
    password    VARCHAR(255),       -- причина 1: аутентификация
    billing_tier VARCHAR(50),        -- причина 2: биллинг
    stripe_id   VARCHAR(100),        -- причина 2: биллинг
    marketing_optin BOOLEAN,         -- причина 3: маркетинг
    marketing_segment VARCHAR(50),   -- причина 3: маркетинг
    created_at  TIMESTAMP
);

-- ✅ Разделить:
CREATE TABLE users (id, email, password, created_at);
CREATE TABLE billing_profiles (user_id, tier, stripe_id);
CREATE TABLE marketing_preferences (user_id, optin, segment);
```

Каждая таблица меняется по своей причине. Изменение биллинга не трогает аутентификацию.

### 5. Git-репозиторий

```
monorepo:
  ├── services/
  │   ├── billing/        — меняется, когда меняется Stripe API
  │   ├── auth/           — меняется, когда меняется IAM-провайдер
  │   └── notifications/  — меняется, когда меняется Telegram/Email API
  └── shared/
      └── domain/         — меняется, когда меняется бизнес-логика
```

Разные команды, разные частоты изменений, разные причины → разные сервисы (или хотя бы разные пакеты).

---

## Ловушка: "один класс = одна ответственность"

Это **антисрп**.

```php
// ❌ "Я разбил на классы, значит SRP соблюдён"
final class OrderValidator {}
final class OrderCalculator {}
final class OrderRepository {}
final class OrderService {
    public function __construct(
        private OrderValidator   $validator,
        private OrderCalculator  $calculator,
        private OrderRepository  $repository,
    ) {}
}

// Итого: 4 класса, каждый "один класс = одна ответственность"
// Но OrderService — это просто прокси. Он не несёт никакой причины для изменения.
// Он существует только для того, чтобы "собирать" три других.
```

Это **анатомизация**, а не SRP. Классы маленькие, но код не стал проще: ты не знаешь, **почему** вызывается валидация, **когда** и **в каком порядке**.

### Правильно

SRP — не "маленькие классы". SRP — **"каждый модуль меняется по одной причине"**. Модуль может быть:

- одним классом (если логика компактная)
- группой классов (если они меняются **вместе** и **по одной причине**)
- одной функцией
- одним файлом
- одним пакетом

```php
// ✅ OrderAggregation — меняются, когда меняется бизнес-логика заказа
final class OrderAggregation
{
    // Validator, Calculator, Repository — внутри, потому что
    // они меняются ВМЕСТЕ, по ОДНОЙ причине: "изменились правила заказа"
    private OrderValidator   $validator;
    private OrderCalculator  $calculator;
    private OrderRepository  $repository;

    public function create(array $input): Order { /* ... */ }
    public function applyDiscount(Order $order, Discount $d): Order { /* ... */ }
}
```

Здесь "ответственность" = **"правила создания и изменения заказа"**. Три внутренних класса — реализация, а не отдельные причины.

---

## Критерий: как проверить SRP на любом уровне

Задай себе один вопрос:

> **"Если завтра поменяется [X], какой код я буду трогать?"**

Если ответ — "и `parse()`, и `validate()`, и `persist()`" → у вас одна причина на три функции. SRP нарушен на уровне функции.

Если ответ — "только `validate()`" → SRP соблюдён.

Неважно, что это: класс, функция, файл, таблица, репозиторий. **Причина для изменения** — единственное, что имеет значение.

---

## Итого

| Уровень | Пример нарушения SRP |
|---------|---------------------|
| Функция | Парсит + валидирует + сохраняет |
| Файл | Контроллер содержит и биллинг, и авторизацию, и рендеринг |
| Переменная | Одна config-масса на 10 не связанных настроек |
| Таблица БД | `users` содержит и аутентификацию, и биллинг, и маркетинг |
| Репозиторий | Один monorepo на 5 не связанных доменов |
| **Антисрп** | "Разбил на 4 маленьких класса, значит SRP ок" |

SRP — это про **причины изменений**, а не про размер сущности. Класс — просто самая частая единица, потому что PHP-разработчик думает в терминах классов. Но принцип универсален.

> ⏱ 30.9s · prompt 11003 tok · output 4018 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о внутреннем устройстве DI-контейнера Symfony: компиляция, autowiring, lazy-loading. Это продвинутое техническое собеседование, нужно дать точный, глубокий ответ без воды.

Структурирую ответ по фазам:
1. Собираение (collection) — как сервисы попадают в контейнер
2. Компиляция (compilation) — что делает ContainerBuilder: pass'ы, autowiring, inlining, removing
3. Runtime — что происходит при первом запросе: ServiceLocator, factory closure, lazy proxy

Ключевые моменты, которые нужно покрыть:
- ServiceDefinition vs ServiceReference
- CompilerPass pipeline (в каком порядке)
- Autowiring: как работает — по типу параметра, как резолвится, что делает TaggedIteratorArgument
- Inlining: когда сервис "распределяется" в вызывающий
- Private vs public, removal
- Lazy services: как работает прокси (Symfony 4+ — factory closure, а не Doctrine-style proxy)
- Что физически происходит при $container->get() / get из контроллера
- ServiceLocator (для tagged services)

Напишу на русском, с примерами кода, без воды.

</details>

**Кандидат:**

## Общий принцип

Контейнер Symfony — это **компилируемый** DI-контейнер. Он существует в двух фазах:

1. **Компиляция** (build time) — `ContainerBuilder` собирает все определения, применяет CompilerPass'ы, генерирует PHP-файл.
2. **Runtime** — загружается сгенерированный PHP-класс, сервисы создаются по требованию.

Между фазами **нет** мета-информации: нет "магии", нет runtime-reflection по интерфейсам. Всё решено статически.

---

## Фаза 1: Сборка определений

Каждый сервис описывается `Definition`:

```php
// Концептуально (внутри ContainerBuilder)
$container->register('app.order_service', OrderService::class)
    ->setPublic(false)          // private по умолчанию
    ->setAutowired(true)        // авто-инжект параметров
    ->setAutoconfigured(true)   // авто-теги по аннотациям
    ->setShared(true)           // singleton
    ->setLazy(false);           // ленивое создание?
```

Параметры `__construct` описаны как `TypedReference`:

```php
// Что видит ContainerBuilder для OrderService:
new Definition(OrderService::class, [
    new Reference('app.payment_gateway'),       // явная ссылка
    new Reference('logger'),                    // явная ссылка
    new TypedReference(PaymentValidator::class), // autowire: "дай мне PaymentValidator"
]);
```

---

## Фаза 2: Компиляция

`ContainerBuilder::compile()` запускает **pipeline** из CompilerPass'ов. Порядок критичен:

```
1. ResolveParameterPlaceHoldersPass
   └─ Заменяет %env(...)% на конкретные значения / EnvPlaceholderParameter

2. RegisterEnvVarProcessorsPass
   └─ Регистрирует env-процессоры (для custom types)

3. AutowirePass          ← КЛЮЧЕВОЙ для autowiring
   └─ (ниже подробно)

4. RegisterServiceSubscribersPass
   └─ Реализует EventSubscriberInterface → добавляет listener'ы

5. ResolveInstanceofConditionalsPass
   └─ Обрабатыает instanceof-конфигурацию

6. RegisterAutoconfigureAttributesPass
   └─ Атрибуты #[Autoconfigure], #[AsCommand], #[AsMessageHandler] и т.д.

7. ResolveHotPathPass
   └─ Разрешает HotPathExpression

8. InlineServiceDefinitionsPass
   └─ (ниже подробно)

9. RemoveUnusedDefinitionsPass
   └─ Удаляет private-сервисы, на которые нет ссылок

10. CheckDefinitionValidityPass
    └─ Валидация: нет циклов, нет missing services

11. ResolveChildDefinitionsPass
    └─ Наследование определений (parent)

12. RegisterFragmentsPass
    └─ Рендеринг fragments (Twig, Router)

13. AnnotatePass
    └─ Добавляет комментарии в сгенерированный код

→ Генерация: PhpDumper → Container.php
```

### AutowirePass — как работает

Для каждого `TypedReference` (или `Reference` без явного ID):

```
1. Берёт тип из TypedReference (например: PaymentValidator::class)

2. Ищет в контейнере сервисы, зарегистрированные с этим классом:
   - Прямая регистрация: $container->register('app.validator', PaymentValidator::class)
   - PSR-4 авто-обнаружение (если включён)

3. Если найден ровно 1 → подменяет TypedReference на Reference('app.validator')

4. Если найдено 0 → TypeError при компиляции:
   "Cannot autowire service X: argument $y of method __construct()
    references interface PaymentValidator but no such service exists."

5. Если найдено >1 → ошибка, НО:
   - Если параметр помечен TaggedIteratorArgument → создаёт ServiceLocator
   - Если есть tag-фильтр → сужает кандидатов

6. Если тип — интерфейс, и есть tagged-сервисы с этим интерфейсом → ServiceLocator
```

### InlineServiceDefinitionsPass

Если **private** сервис используется **ровно в одном** месте и **не имеет** `setLazy(true)`, `setPublic(true)`, `setSynthetic(true)` — его определение **распределяется** (inlined) в вызывающий сервис.

```php
// До inlining:
// OrderService → PaymentValidator (private, 1 reference)

// После inlining:
// OrderService содержит В СЕБЕ код создания PaymentValidator
// Отдельный сервис PaymentValidator больше не существует
```

Почему это важно:
- Меньше `get()`-вызовов → быстрее.
- Меньше ссылок → проще RemoveUnusedDefinitionsPass.
- **Но**: если PaymentValidator используется в 3 местах → inline НЕ произойдёт, останется отдельный сервис.

### RemoveUnusedDefinitionsPass

Удаляет все private-сервисы, на которые **нет** ссылок после inlining.

```
Результат: контейнер содержит только:
- public-сервисы (контроллеры, commands, console events)
- private-сервисы, которые реально используются
- синтетические сервисы (request, response, session)
```

В типичном Symfony-приложении из 500 зарегистрированных сервисов в runtime остаётся 100–150.

---

## Фаза 3: Runtime — первый запрос

### Что физически происходит

Сгенерированный файл `var/cache/prod/App_KernelProdDebugContainer.php` выглядит примерно так:

```php
class App_KernelProdDebugContainer extends Container
{
    protected array $services = [];
    protected array $loading = [];
    private array $factories = [];
    private array $aliases = [];

    // ... (генерируемые)

    public function get('app.order_service')
    {
        return $this->services['app.order_service'] = new OrderService(
            $this->get('app.payment_gateway'),
            $this->get('logger'),
            $this->get('app.validator'),  // вlined или нет
        );
    }

    // Или, если lazy:
    public function get('app.slow_service')
    {
        return $this->services['app.slow_service'] = $this->factories['app.slow_service'](
            new Service('app.slow_service', SlowService::class, $this)
        );
    }
}
```

Ключевой момент: **`get()` — это PHP-метод, а не магия.** Каждый вызов — это `new` с аргументами, которые сами могут быть `get()`.

### Первый вызов из контроллера

```php
// 1. Kernel::handle() → FrontController
// 2. Router resolve → Controller::handle()
// 3. Controller создан контейнером:
//    $controller = $this->container->get('app.controller');

// 4. Внутри контроллера:
public function __construct(
    private OrderService $orderService,  // ← инжект при создании контроллера
) {}

// 5. OrderService создан ЛУЧЕЕ (lazy proxy) или АГРЕССИВНО (eager):
```

#### Case A: Eager (без lazy)

```
Request → Kernel::handle()
  → Router → Controller (создаётся)
    → OrderService (создаётся)
      → PaymentGateway (создаётся)
        → HttpKernelClient (создаётся)
          → ... (вся цепочка сразу)
```

Вся цепочка создаётся **до** первого business-вызова. Для тяжёлых зависимостей это дорого.

#### Case B: Lazy (с `#[Lazy]` или `lazy: true`)

```php
// Сгенерированный код:
public function get('app.order_service')
{
    return $this->services['app.order_service'] = $this->factories['app.order_service'](
        new Service('app.order_service', OrderService::class, $this)
    );
}

// factories[] содержит closure:
// fn(Service $service) => ProxyManagerFactory::createProxy($service)
// или (Symfony 5+):
// fn(Service $service) => new OrderServiceProxy($this)
```

**Lazy proxy** (Symfony 4.3+, `Symfony\Proxy\ServiceLocator\LazyLoadingValueHolderFactory`):

```php
// Концептуально (упрощённо):
class OrderService extends \ProxyManager\Proxy\LazyGhost
{
    // Генерируется на основе реального класса
    // Переопределяет ВСЕ public методы, кроме:
    //   - __toString
    //   - __sleep / __wakeup
    //   - final методы

    // При первом вызове метода:
    public function calculateTotal(Order $order): Money
    {
        $this->initialize(); // ← первый вызов: создаёт реальный объект
        return $this->realInstance->calculateTotal($order);
    }
}
```

`initialize()` → `getActualInstance()` → `$container->get('app.order_service')` → `new OrderService(...)`.

**Критический момент:** прокси создаётся **немедленно** (он лёгкий — только stub-методы). Но **реальный объект** создаётся при **первом вызове метода**. Если метод никогда не вызывается — объект не создаётся.

### ServiceLocator (для tagged-сервисов)

Когда autowiring не может выбрать один из нескольких (например, `array $handlers`):

```php
// Сгенерированный код:
$handlers = new ServiceLocator([
    'app.card_handler' => fn() => $this->get('app.card_handler'),
    'app.bank_handler' => fn() => $this->get('app.bank_handler'),
    'app.crypto_handler' => fn() => $this->get('app.crypto_handler'),
]);
```

```php
// В вызывающем коде:
foreach ($this->handlers as $handler) {  // перебирает ключи
    $handler->handle($payment);          // вызывает closure → get() → создаётся
}
```

**Lazy**: каждый handler создаётся только при первом обращении. Если обработано 2 из 7 — только 2 созданы.

---

## Что происходит при `$container->get('service_id')`

Последовательность:

```
1. $container->get('app.order_service')
   ↓
2. Проверяет: isset($this->services['app.order_service'])?
   ├── Да → возвращает (singleton)
   └── Нет ↓
3. Проверяет: isset($this->factories['app.order_service'])?
   ├── Да (lazy) → вызывает factory closure → возвращает proxy
   └── Нет (eager) ↓
4. Выполняет тело метода get():
   new OrderService(
       $this->get('app.payment_gateway'),  // рекурсивный get
       $this->get('logger'),               // рекурсивный get
       ...
   )
   ↓
5. Сохраняет в $this->services['app.order_service']
   ↓
6. Возвращает
```

**Рекурсия** — вот что делает контейнер. Каждый сервис — это PHP-выражение, которое внутри вызывает `get()` на зависимостях. Нет "магического резолвера". Нет "интепска по интерфейсам". Только PHP-код.

---

## Границы модели

| Что Symfony **делает** | Что Symfony **не делает** |
|---|---|
| Статический анализ типов при компиляции | Runtime-рефлексия по интерфейсам |
| Инлайнинг private-сервисов | Cyclic dependency resolution (бросает) |
| Lazy proxy через ProxyManager / свой механизм | AOP / interceptors (нужны traits / decorators) |
| Tagged iterators (ServiceLocator) | Generics / structural typing |
| `!` (non-nullable) и `?` (nullable) в автоинжекте | Optional injection "по умолчанию" (только через `?Type $x = null`) |

---

## Практический вывод

Понимание этой модели важно для:

1. **Debug: "почему сервис не найден?"** → проверь, что он зарегистрирован, что тип совпадает, что нет `public: false` + нет ссылок.
2. **Debug: "почему медленный?"** → проверь, что тяжёлые сервисы помечены `#[Lazy]`, что нет eager-цепочек.
3. **Производительность**: inline-сервисы быстрее, но только если private + 1 reference. Public-сервисы — всегда отдельные.
4. **Тестирование**: `KernelTestCase` создаёт **реальный** контейнер. `BrowserKit` + `Client` — тоже. Не путать с unit-тестами (там контейнера нет).

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Сначала — диагноз

Циклическая зависимость почти всегда означает: **два сервиса делают одну работу** (нарушение SRP) или **один сервис содержит чужую логику** (нарушение LSP). Контейнер бросает `CircularReferenceException` — это не баг контейнера, это **статический анализатор**, который говорит: "у вас две вещи, которые на самом деле — одна".

```
OrderService → PaymentService
PaymentService → OrderService

// Что это значит на самом деле:
// OrderService "знает" про платежи (своё дело)
// PaymentService "знает" про заказы (чужое дело)
// → PaymentService содержит логику, которая принадлежит OrderService
```

---

## Уровни решения

### 0. Сначала — пойми, почему цикл возник

| Причина | Сигнал |
|---------|--------|
| Два сервиса делают одну работу | Они оба знают про `Order` и оба изменяют его состояние |
| Один сервис содержит чужую логику | `PaymentService` вызывает `OrderService::updateStatus()` |
| Общий контекст не вынесен | Оба сервиса читают `Config` и принимают решения |
| Навык, а не архитектура | "Так было в прошлом проекте" |

Без этого шага — ты латаешь симптом.

---

### 1. Извлечь общий код в третий сервис (самый частый и правильный)

```php
// ❌ Цикл:
class OrderService
{
    public function __construct(private PaymentService $payments) {}

    public function confirm(Order $order): void
    {
        $order->markConfirmed();
        $this->payments->charge($order);
        $order->markPaid();  // ← PaymentService "знает" про Order
    }
}

class PaymentService
{
    public function __construct(private OrderService $orders) {}

    public function charge(Order $order): void
    {
        // ...
        $this->orders->markPaid($order);  // ← OrderService "знает" про Payment
    }
}
```

Решение: оба сервиса хотят **изменить статус заказа**. Это одна ответственность → вынеси её:

```php
// ✅ Извлечено:
class OrderStatusService
{
    public function markConfirmed(Order $order): void { $order->markConfirmed(); }
    public function markPaid(Order $order): void      { $order->markPaid(); }
    public function markFailed(Order $order, string $reason): void { /* ... */ }
}

class OrderService
{
    public function __construct(
        private PaymentService $payments,
        private OrderStatusService $status,   // ← не зависит от PaymentService для мутаций
    ) {}

    public function confirm(Order $order): void
    {
        $this->status->markConfirmed($order);
        $this->payments->charge($order);
        // markPaid — теперь ответственность PaymentService (через событие, см. ниже)
    }
}

class PaymentService
{
    public function __construct(
        private OrderStatusService $status,   // ← не зависит от OrderService
        private PaymentGateway $gateway,
    ) {}

    public function charge(Order $order): void
    {
        $result = $this->gateway->charge($order);
        if ($result->success()) {
            $this->status->markPaid($order);
        } else {
            $this->status->markFailed($order, $result->reason());
        }
    }
}
```

Диаграмма после:

```
OrderService  → PaymentService  → PaymentGateway
     ↓                ↓
OrderStatusService (общий, без зависимостей)
```

Цикл исчез. `OrderStatusService` — "мост", который несёт **общую** ответственность.

---

### 2. События (когда "знание" — не необходимость)

Если `PaymentService` не **обязан** обновлять статус заказа (это не его контракт, а "побочный эффект") — используй событие:

```php
class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
        private EventDispatcherInterface $events,  // ← не зависит от OrderService
    ) {}

    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);

        $this->events->dispatch(new PaymentCompletedEvent($order, $result));
        // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
        // PaymentService НЕ знает, что произойдёт дальше.

        return $result;
    }
}

// Подписчик — отдельный класс, живёт в другом слое:
final class MarkOrderPaidOnPayment implements EventSubscriberInterface
{
    public function __construct(private OrderStatusService $status) {}

    public static function getSubscribedEvents(): array
    {
        return [PaymentCompletedEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentCompletedEvent $event): void
    {
        if ($event->result()->success()) {
            $this->status->markPaid($event->order());
        } else {
            $this->status->markFailed($event->order(), $event->result()->reason());
        }
    }
}
```

Теперь:
- `PaymentService` не знает про `OrderService` и `OrderStatusService`.
- `OrderService` не знает, что "после платежа" происходит.
- Цикл **невозможен** — связи однонаправленные.

**Когда это правильно:** связь — "побочный эффект", не "контракт". Если `OrderService` **гарантированно** ожидает, что после `charge()` статус станет `paid` — это контракт, не событие. Тогда подход 1.

**Когда это не работает:** нужен порядок (payment → status → notification). События не гарантируют порядок. Тогда — approach 1 или оркестратор.

---

### 3. Оркестратор (Application Service / Use Case)

Когда логика — **секвенция** из шагов, и порядок важен:

```php
class ConfirmOrderUseCase
{
    public function __construct(
        private OrderStatusService $status,
        private PaymentService     $payments,
        private NotificationService $notifications,
    ) {}

    public function execute(string $orderId): void
    {
        $order = Order::find($orderId);

        // Шаг 1
        $this->status->markConfirmed($order);

        // Шаг 2
        $result = $this->payments->charge($order);

        // Шаг 3
        if ($result->success()) {
            $this->status->markPaid($order);
            $this->notifications->sendInvoice($order);
        } else {
            $this->status->markFailed($order, $result->reason());
            $this->notifications->sendFailure($order, $result->reason());
        }
    }
}
```

`OrderService` и `PaymentService` **не знают друг о друге**. Все знают только про `OrderStatusService` и `NotificationService`. Оркестратор — "режиссёр", а сервисы — "актёры".

Это самый чистый вариант для **бизнес-процессов**. Недостаток: оркестратор может стать 300-строчным "God Object", если в нём накапливается логика. Решение — SRP: если логика "выбора" (if/else) превышает 3–4 ветки — вынеси в Policy.

---

### 4. Lazy injection (quick fix, с оговорками)

Symfony поддерживает `service_closure` — сервис инжектится как closure, вызывается при необходимости:

```php
// config/services.yaml:
App\PaymentService:
    calls:
        - setOrderService: ['@service_closure(app.order_service)']

class PaymentService
{
    /** @var Closure(): OrderService */
    private $orderService;

    public function setOrderService(Closure $orderService): void
    {
        $this->orderService = $orderService;
    }

    public function charge(Order $order): void
    {
        // ...
        // Только здесь, только если нужно:
        $this->orderService()->markPaid($order);
        //            ^^^^^^^^^^^^^^ — вызов closure → get() → создание
    }
}
```

**Почему это quick fix:**
- Цикл **не устранён**, он **отложен**. Контейнер больше не видит циклона, но зависимость существует.
- Если `charge()` всегда вызывается в контексте, где `OrderService` уже создан → ок.
- Если `charge()` вызывается из `OrderService` → `setOrderService()` → `orderService()` → `new OrderService(...)` → `setPaymentService()` → **опять цикл**, но уже runtime, а не compile-time.

**Когда допустимо:**
- Транзиентный сервис, который используется 1 раз в lifecycle.
- Debug-инструменты (`Logger` → `Profiler` → `Logger`).
- Фреймворк-внутреннее (Symfony сам так делает).

**Когда не допустимо:**
- Бизнес-логика. Это маскирует архитектурную проблему.

---

### 5. ServiceLocator (последнее средство)

```php
class PaymentService
{
    public function __construct(
        private ServiceLocator $locator,
        // ...
    ) {}

    public function charge(Order $order): void
    {
        // ...
        $this->locator->get(OrderStatusService::class)->markPaid($order);
    }
}
```

**Почему это плохо:**
- Потеря compile-time проверки. Опечатка в `::class` → runtime error.
- IDE не подскажет, какие сервисы "доступны".
- Скрытая зависимость — читающий код не видит, что `PaymentService` зависит от `OrderStatusService`.

**Когда допустимо:**
- Фреймворк-код (Symfony, Doctrine сами используют ServiceLocator).
- Plugin-системы, где набор сервисов неизвестен на compile-time.

---

## Решение: дерево

```
Циклическая зависность
│
├─ Можно извлечь общий код в третий сервис?
│  ├── ДА → Подход 1 (извлечение)
│  └── НЕТ ↓
│
├─ Связь — "побочный эффект", не контракт?
│  ├── ДА → Подход 2 (события)
│  └── НЕТ ↓
│
├─ Это бизнес-процесс с порядком?
│  ├── ДА → Подход 3 (оркестратор)
│  └── НЕТ ↓
│
├─ Транзиентное / фреймворк-внутреннее?
│  ├── ДА → Подход 4 (lazy closure)
│  └── НЕТ ↓
│
└─ Plugin / динамический набор?
   ├── ДА → Подход 5 (ServiceLocator)
   └── НЕТ → Вернуться к подходу 1/3, пересмотреть SRP
```

---

## Практический чеклист

Перед тем как "лечить" цикл:

1. **Нарисуй граф.** Не в IDE — на бумаге. Стрелки = "зависит от". Видишь цикл → видишь, что именно в нём лишнее.
2. **Спроси: "какой из двух сервисов может существовать без другого?"** Если ответ: "оба могут" → это не зависимость, а **взаимодействие** → события или оркестратор.
3. **Спроси: "что общего в их коде?"** Если ответ: "оба читают Order, оба пишут в Order" → извлеки `OrderStatusService`.
4. **Не добавляй `#[Lazy]`, пока не исчерпаны 1–3.** Это не решение, это отсрочка.

> ⏱ 29.2s · prompt 21265 tok · output 3263 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

The user is asking about async side-effects after user registration in Symfony: send email + update search index, without slowing down the HTTP response. This is a classic "post-commit side effects" problem.

Let me think about the landscape of solutions in Symfony:

1. **Synchronous (naive)** — do it inline in the controller. Slow, couples concerns.
2. **Symfony Messenger (in-memory/Doctrine transport)** — async within the same process, or truly async via AMQP/RabbitMQ/Redis transport. This is the "Symfony-native" answer.
3. **Event-driven (EventDispatcher)** — still synchronous unless combined with Messenger.
4. **Cron/queue worker** — separate process consuming from a queue.
5. **Database outbox pattern** — write to a `jobs` table, a worker picks them up.
6. **Hybrid: in-memory for cheap, AMQP for durable.**

I should structure this as:
- The problem decomposition (what are the two side-effects, what are their failure modes)
- Option 1: Synchronous (baseline, to contrast)
- Option 2: Symfony Messenger + in-memory (simplest async)
- Option 3: Messenger + AMQP/Redis (durable, separate worker)
- Option 4: DB Outbox + worker (no external dependency)
- Option 5: Event + Messenger combined (the "full" Symfony approach)

For each: trade-offs, when to use, failure modes, what happens if the process dies mid-flight.

I should also cover:
- Idempotency (what if email is sent twice?)
- Retry semantics
- Observability
- The "at-least-once" vs "exactly-once" problem
- What happens if the user registration itself fails (transaction boundary)

Let me think about what a senior PHP developer would actually say in an interview. They'd:
1. Show they understand the full spectrum
2. Recommend a pragmatic default (Messenger + AMQP for production, in-memory for dev)
3. Mention the transaction boundary explicitly
4. Talk about idempotency
5. Not oversell any single solution

I'll structure it as a progressive set of options, each building on the previous, with a clear "what I'd pick for X" at the end.

</details>

**Кандидат:**

## Декомпозиция задачи

Два сайд-эффекта после регистрации:

| Сайд-эффект | Может упасть? | Сколько раз можно отправить? | Насколько критичен? |
|-------------|:---:|:---:|:---:|
| Email | Да (SMTP down, rate-limit) | 1 раз (дубли = спам) | Высокий: пользователь не получил письмо |
| Search index | Да (ES down, timeout) | Много (идемпотентно) | Средний: пользователь не находится через поиск, но аккаунт работает |

Ключевые требования:

1. **Не замедлять ответ.** Пользователь получает 200, а фоновые задачи исполняются потом.
2. **Атомарность регистрации.** Если транзакция откатилась — никаких писем и индексов.
3. **Устойчивость к сбоев.** Процесс мог упасть между commit'ом и отправкой.
4. **Идемпотентность.** Повторная доставка не должна создать дубль.

---

## Опция 0: Синхронно (база, для контраста)

```php
public function register(RegisterUserCommand $cmd): void
{
    $user = $this->userFactory->create($cmd);
    $this->userRepo->save($user);           // commit

    $this->mailer->sendWelcome($user);      // ← блокирует ответ
    $this->searchIndex->index($user);       // ← блокирует ответ
}
```

| Плюс | Минус |
|------|-------|
| Просто. Нет инфраструктуры. | Ответ зависит от SMTP + ES. |
| Нет "потерянных" писем. | SMTP down = 500 пользователю. |
| Тестируется как одна функция. | Невозможно ретраить email без повторной регистрации. |

Для MVP / внутреннего инструмента — допустимо. Для продакшена — нет.

---

## Опция 1: Symfony Messenger + in-memory transport

```php
// Message
final class UserRegistered
{
    public function __construct(public readonly string $userId) {}
}

// Producer
class UserService
{
    public function __construct(
        private UserRepo $repo,
        private MessageBus $bus,
    ) {}

    public function register(RegisterUserCommand $cmd): void
    {
        $user = $this->userFactory->create($cmd);
        $this->repo->save($user);  // commit

        // Отправляем message — НЕ блокируем
        $this->bus->dispatch(new UserRegistered($user->id));
        // ← метод вернул управление. Ответ уходит.
    }
}

// Consumer (отдельный процесс: bin/console messenger:consume async)
final class SendWelcomeEmail
{
    public function __construct(private Mailer $mailer, private UserRepo $repo) {}

    public function __invoke(UserRegistered $msg): void
    {
        $user = $this->repo->find($msg->userId);
        if ($user === null) return;  // пользователь удалён — ок

        $this->mailer->sendWelcome($user);
    }
}

final class UpdateSearchIndex
{
    public function __construct(private SearchIndexer $indexer, private UserRepo $repo) {}

    public function __invoke(UserRegistered $msg): void
    {
        $user = $this->repo->find($msg->userId);
        if ($user === null) return;

        $this->indexer->index($user);
    }
}
```

Конфиг:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'doctrine://default'   # БД-транспорт
                # dsn: 'amqp://localhost/messages'  # или RabbitMQ
        routing:
            'App\Message\UserRegistered': async
```

### Что происходит

```
Request
  → UserService::register()
    → repo->save()          — COMMIT
    → bus->dispatch()       — INSERT INTO messenger_messages (payload, transport_name, ...)
    → return 200            — ответ ушёл

Worker (отдельный процесс)
  → SELECT ... FROM messenger_messages WHERE queue_name = 'async' FOR UPDATE SKIP LOCKED
  → deserialize UserRegistered
  → SendWelcomeEmail::__invoke()
  → UPDATE messenger_messages SET delivered_at = now()
```

### Trade-offs

| Плюс | Минус |
|------|-------|
| Ответ не блокируется. | Нужен отдельный worker-процесс. |
| Устойчиво к сбоев: message в БД, worker подхватит. | In-memory (default) — потеряно при crash. |
| Встроенный retry: `RetryStrategy`. | БД-транспорт — дополнительная нагрузка на БД. |
| Идемпотентность — на нас. | Нет "exactly-once": at-least-once. |
| Один механизм для всех задач. | Отладка: message в БД, а не в памяти. |

### Критический момент: транзакционная граница

```php
// ⚠️ НЕ ДЕЛАЙ ТАК:
public function register(RegisterUserCommand $cmd): void
{
    $this->beginTransaction();
    $this->repo->save($user);
    $this->bus->dispatch(new UserRegistered($user->id));  // ← INSERT в messenger_messages
    $this->commit();  // ← если commit упадёт — message тоже откатится. ХОРОШО.
}

// ⚠️ А ТАК — БЫЛО БЫ ПЛОХО:
public function register(RegisterUserCommand $cmd): void
{
    $this->repo->save($user);  // COMMIT (отдельная транзакция)
    $this->bus->dispatch(new UserRegistered($user->id));  // ← если процесс упадёт тут —
    //                                                    — user записан, message нет.
}
```

**Правило:** `dispatch()` должен быть **внутри** той же транзакции, что и `save()`. Тогда атомарность гарантирована: либо user + message, либо ничего.

Symfony Messenger с `doctrine://default` делает это **автоматически**, если transport подключён к той же EntityManager. С `amqp://` — **нет**, нужно обёрнуть вручную:

```php
public function register(RegisterUserCommand $cmd): void
{
    $user = $this->userFactory->create($cmd);
    $this->repo->save($user);

    // AMQP: publish НЕ в той же транзакции.
    // Если AMQP упал — user сохранён, но message нет.
    // Решение: Outbox (опция 3).
    $this->bus->dispatch(new UserRegistered($user->id));
}
```

---

## Опция 2: Messenger + RabbitMQ / Redis Stream

То же, что опция 1, но транспорт — внешний брокер.

```yaml
transports:
    async:
        dsn: 'amqp://localhost/messages?queue_name=app_events'
        # или
        dsn: 'redis-stream://localhost:6379?stream_name=app_events'
```

| Плюс | Минус |
|------|-------|
| Брокер переживает crash PHP-процесса. | Зависимость от внешней инфраструктуры. |
| Распределённые workers: 3 процесса на 1 очередь. | Сложнее отладка: нужно смотреть в RabbitMQ management UI. |
| Приоритеты, DLQ (dead letter queue), TTL. | Атомарность с БД — не гарантирована (см. выше). |
| Мониторинг: queue depth, consumer lag. | Если RabbitMQ down — `dispatch()` падает с exception. |

### Когда выбирать

- Несколько инстансов приложения → need distributed queue.
- Нужно DLQ + monitoring.
- Объём > 10k msg/s.

### Что не решить

Атомарность "commit + publish" — **не решить** на уровне брокера. Для этого — Outbox.

---

## Опция 3: Database Outbox (паттерн)

Идея: message записывается в **ту же БД**, что и бизнес-данные, в **той же транзакции**. Отдельный процесс (publisher) читает таблицу и пушит в брокер.

```sql
CREATE TABLE outbox (
    id          BIGSERIAL PRIMARY KEY,
    topic       VARCHAR(255) NOT NULL,
    payload     JSONB NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT now(),
    published_at TIMESTAMP,
    attempts    INT NOT NULL DEFAULT 0,
    last_error  TEXT
);
```

```php
// В той же транзакции:
$this->entityManager->getConnection()->executeStatement(
    'INSERT INTO outbox (topic, payload) VALUES (?, ?)',
    ['user.registered', json_encode(['userId' => $user->id])],
);
// commit → user + outbox запись. Атомарно.
```

```php
// Publisher (отдельный процесс, cron или long-running):
class OutboxPublisher
{
    public function tick(): void
    {
        $rows = $this->db->fetchRow(
            'SELECT * FROM outbox WHERE published_at IS NULL AND attempts < 5
             ORDER BY created_at LIMIT 100
             FOR UPDATE SKIP LOCKED'
        );

        foreach ($rows as $row) {
            try {
                $this->amqp->publish($row['topic'], $row['payload']);
                $this->db->update(
                    'UPDATE outbox SET published_at = now() WHERE id = ?',
                    [$row['id']]
                );
            } catch (\Throwable $e) {
                $this->db->update(
                    'UPDATE outbox SET attempts = attempts + 1, last_error = ? WHERE id = ?',
                    [$e->getMessage(), $row['id']]
                );
            }
        }
    }
}
```

| Плюс | Минус |
|------|-------|
| **Атомарность гарантирована.** | Нужно писать publisher (или использовать библиотеку). |
| Не зависит от可用性 AMQP в момент записи. | Таблица растёт → нужен cleanup / partition. |
| Видимо в БД: `SELECT * FROM outbox WHERE published_at IS NULL`. | Дополнительный процесс. |
| Работает с любой БД. | Нет "built-in" retry в БД — пишем сами. |

### Когда выбирать

- Критическая атомарность: "если user создан — письмо **обязано** уйти".
- Уже есть RabbitMQ, но нужны гарантии.
- Финансовые операции, где "message потеряно" = инцидент.

---

## Опция 4: Event + Messenger (композиция)

Самый "Symfony-native" подход. Событие — **контракт**, Messenger — **механизм доставки**.

```php
// 1. Доменное событие (чистое, без фреймворка)
final class UserRegistered
{
    public function __construct(
        public readonly UserId $userId,
        public readonly string $email,
        public readonly \DateTimeImmutable $registeredAt,
    ) {}
}

// 2. Домен бросает событие
class User
{
    /** @var UserRegistered[] */
    private array $events = [];

    public function register(string $email, string $password): void
    {
        // ... валидация, hash ...
        $this->events[] = new UserRegistered($this->id, $email, new \DateTimeImmutable());
    }

    /** @return static[] */
    public function collectEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}

// 3. Application Service — собирает и dispatch'ит
class RegisterUserUseCase
{
    public function __construct(
        private UserRepo $repo,
        private EventDispatcherInterface $events,
    ) {}

    public function execute(RegisterUserCommand $cmd): void
    {
        $user = new User();
        $user->register($cmd->email, $cmd->password);

        $this->repo->save($user);

        foreach ($user->collectEvents() as $event) {
            $this->events->dispatch($event);  // → Messenger (если настроено)
        }
    }
}

// 4. Listener (подписчик)
final class HandleUserRegistered
{
    public function __construct(private MessageBus $bus) {}

    public function onUserRegistered(UserRegistered $event): void
    {
        // Отправляем в асинхронную очередь
        $this->bus->dispatch(new SendWelcomeEmailMessage($event->userId));
        $this->bus->dispatch(new UpdateSearchIndexMessage($event->userId));
    }
}
```

Контент:

```
Домен (User)
  → бросает UserRegistered (чистый PHP, без Symfony)

Application Service
  → сохраняет, dispatch'ит событие

EventDispatcher
  → Listener получает событие

Listener
  → dispatch'ит Message в Messenger (async transport)

Worker
  → SendWelcomeEmailMessage handler
  → UpdateSearchIndexMessage handler
```

### Почему так, а не `bus->dispatch()` прямо из Use Case

| | Прямо в Use Case | Через Event |
|---|---|---|
| Домен знает про Messenger? | Да (косвенно) | Нет |
| Новый сайд-эффект? | Меняешь Use Case | Добавляешь Listener |
| Тест домена | Нужен mock MessageBus | Чистый PHP |
| LSP | `RegisterUserUseCase` знает про email + index | `User` не знает ни про что |

---

## Итоговая рекомендация

| Ситуация | Что выбрать |
|----------|-----------|
| MVP, 1 инстанс, нет требований к отказоустойчивости | Опция 1 (Messenger + `doctrine://default`) |
| Продакшен, 1–2 инстанса, есть RabbitMQ | Опция 2 (Messenger + AMQP) + Outbox для критических |
| Финансы, "message потеряно = инцидент" | Опция 3 (Outbox) + AMQP |
| Чистый домен, много сайд-эффектов | Опция 4 (Event + Messenger) |
| Всё выше вместе | Опция 4 как архитектура + Опция 2/3 как транспорт |

---

## Частые ошибки

| Ошибка | Почему плохо |
|--------|-------------|
| `dispatch()` **после** commit, вне транзакции | Crash между commit и dispatch → message потеряно |
| `dispatch()` **внутри** `catch` | Событие бросается при ошибке — логики нет |
| Handler шлёт email **и** пишет в индекc **в одном** message | Невозможно ретраить email без повторного индексирования |
| `async: in-memory` в продакшене | Message в памяти → crash = потеря |
| Нет идемпотентности в handler | Redelivery = дубль письма = спам |

### Идемпотентность — обязательно

```php
final class SendWelcomeEmail
{
    public function __construct(
        private Mailer $mailer,
        private UserRepo $repo,
        private EmailLogRepo $log,  // ← таблица sent_emails
    ) {}

    public function __invoke(SendWelcomeEmailMessage $msg): void
    {
        $user = $this->repo->find($msg->userId);
        if ($user === null) return;

        // Идемпотентность: не шлём повторно
        if ($this->log->exists(userId: $user->id, type: 'welcome')) {
            return;  // уже отправлено
        }

        $this->mailer->sendWelcome($user);
        $this->log->record(userId: $user->id, type: 'welcome', sentAt: new \DateTimeImmutable());
    }
}
```

Для search index идемпотентность бесплатна: `PUT /index/{id}` — overwrite, дубль не создаётся.

> ⏱ 34.3s · prompt 27093 tok · output 4216 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

The user is asking about Symfony's access control system: authentication, roles, voters, and when to use each. This is a continuation of a PHP backend developer interview. I need to explain the architecture clearly, show code examples, and give practical guidance on when to use what.

Let me structure this:

1. The overall flow: Authentication (who are you) → Authorization (what can you do)
2. Authentication: how Symfony does it (firewall, authenticators, token)
3. Roles: the simple mechanism
4. Voters: the complex mechanism
5. When to use which
6. Common pitfalls

I should be practical and show real code, not just theory. The user has been asking deep questions throughout this conversation, so I should match that depth.

Key points to cover:
- The distinction between "who are you" (auth) and "what can you do" (authz)
- The firewall as the entry point
- The modern Authenticator system (Symfony 5.3+) vs the old Guard system
- Token as the carrier of identity + roles
- Access decision manager as the decision engine
- Roles as "coarse-grained" checks
- Voters as "fine-grained" checks
- The `@IsGranted` / `is_granted` / `ensureGranted` API
- Real examples of when roles are enough vs when you need voters
- Common anti-patterns (e.g., "ADMIN" role for everything, voter that's really just a role check in disguise)

Let me write a comprehensive answer with code examples.

</details>

**Кандидат:**

## Архитектура: три слоя

```
Request
  → Firewall (who are you?)
    → Authenticator (verify credentials)
      → Token (identity + roles)
        → Access Decision Manager (can you do this?)
          → Roles (coarse)
          → Voters (fine-grained)
```

Аутентификация — **один раз** на запрос. Авторизация — **на каждый** защищённый ресурс.

---

## 1. Аутентификация: кто ты

### Firewall — точка входа

```yaml
# config/packages/security.yaml
security:
    providers:
        app:
            id: App\Security\UserProvider   # PSR-6: loadUserByIdentifier()

    firewalls:
        main:
            lazy: true
            provider: app
            stateless: false

            entry_point: App\Security\LoginFormAuthenticator

            json_login:
                check_path: /api/login
                success_handler: 'lexikn_jwt.authenticator.jwt_success_handler'
                # для API: JWT, stateless

            form_login:
                login_path: /login
                check_path: /login
                default_target_path: /dashboard
                # для веб: session-based

            # ...
```

Firewall определяет:
- **какой** провайдер пользователей
- **какие** authenticator'ы активны (form_login, jwt, oauth, ...)
- **stateless** или **session**

### Authenticator (Symfony 5.3+)

```php
final class ApiTokenAuthenticator extends AbstractAuthenticationForm
{
    public const CREDENTIALS_KEY = 'token';

    public function supports(Request $request): ?User
    {
        // Только для /api/*, только POST
        return $request->attributes->get('_route') === 'api_login'
            && $request->isMethod('POST')
            ? $request->request->get('email')  // возвращаем identifier
            : null;
    }

    public function getCredentials(Request $request): ?TokenInterface
    {
        $email = $request->request->get('email');
        $token = $request->request->get('token');

        if ($email === null || $token === null) {
            return null;
        }

        return new SelfToken($email, $token);  // свой Token
    }

    public function checkCredentials(TokenInterface $token, UserProviderInterface $userProvider): bool
    {
        /** @var SelfToken $token */
        $user = $userProvider->loadUserByIdentifier($token->getUserIdentifier());

        if ($user === null) {
            return false;
        }

        // Своя логика: сравнить токен, не пароль
        return $user->apiTokenMatches($token->getRawToken());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Для API: ничего (token уже в request)
        // Для веба: redirect
        return null;
    }
}
```

### Token — носитель идентичности

```php
class SelfToken extends AbstractToken
{
    private ?User $user = null;

    public function __construct(
        private string $userIdentifier,
        private string $rawToken,
    ) {
        parent::__construct();
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        // здесь же можно подгрузить роли
    }

    public function __serialize(): array
    {
        // сериализуется в session (если stateful)
    }
}
```

После успешной аутентификации:

```php
// $token->getUser() → User entity
// $token->getRoles() → ['ROLE_USER', 'ROLE_ADMIN'] (если у User есть getRoles())
// $token->hasRole('ROLE_ADMIN') → true/false
```

**Ключевой момент:** token живёт в `Request::attributes` (или session). Дальше любой код может сделать:

```php
$token = $this->getToken();       // Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
$user = $token->getUser();         // User entity
$token->hasRole('ROLE_ADMIN');     // быстрая проверка
```

---

## 2. Авторизация: что ты можешь

### Уровни контроля

```
1. Route-level:     access_control: [{path: ^/admin, roles: ROLE_ADMIN}]
2. Controller-level: #[IsGranted('ROLE_ADMIN')]
3. Entity-level:    isGranted('EDIT', $order)          ← VOTER
4. Method-level:    $this->denyAccessUnlessGranted('VIEW', $report)
5. Template-level:  {% if is_granted('EDIT', post) %}
```

Все они проходят через **один** механизм: `AccessDecisionManager`.

### AccessDecisionManager

```php
interface AccessDecisionManagerInterface
{
    /**
     * @param string|array $attributes  — что проверяем
     * @param object|null  $object      — на чём проверяем
     * @param Token|null   $token       — кто проверяет
     */
    public function decide(TokenInterface $token, array $attributes, ?object $object): bool;
}
```

Стратегии:

| Стратегия | Логика |
|-----------|--------|
| `AffirmativeBased` | **Хотя бы один** voter сказал "yes" → allowed (default) |
| `UnanimousBased` | **Все** voter'ы сказали "yes" → allowed |
| `ConsensusBased` | Голоса "yes" > голоса "no" → allowed |

По умолчанию: `AffirmativeBased`. Один voter может разрешить, остальные промолчат.

### Порядок проверок

```
isGranted('EDIT', $order)
  │
  ├─ 1. RoleVoter (встроенный Symfony)
  │     Атрибут 'ROLE_ADMIN' или 'EDIT'?
  │     → 'EDIT' не начинается с 'ROLE_' → abstain
  │
  ├─ 2. AttributeBagVoter (встроенный)
  │     → нет attribute в request → abstain
  │
  ├─ 3. OrderVoter (наш)
  │     Атрибут 'EDIT' + объект Order + пользователь $user?
  │     → $user === $order->owner → ACCESS_GRANTED
  │
  └─ 4. Нет ответа → ACCESS_ABSTAIN от всех → DENIED (403)
```

---

## 3. Роли: грубый уровень

### Что такое роль

Роль — **строка** в `Token::getRoles()`. Она отвечает на вопрос: **"какую функцию ты выполняешь в системе?"**

```php
class User extends AbstractUser
{
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isSupport) {
            $roles[] = 'ROLE_SUPPORT';
        }

        return array_unique($roles);
    }
}
```

### Где применяются

```yaml
# 1. Route-level
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/support, roles: ROLE_SUPPORT }
        - { path: ^/, roles: ROLE_USER }

# 2. Controller-level
#[IsGranted('ROLE_ADMIN')]
public function adminDashboard(): Response { ... }

# 3. Template-level
{% if is_granted('ROLE_ADMIN') %}
    <a href="/admin">Admin</a>
{% endif %}
```

### Когда ролей достаточно

| Критерий | Пример |
|----------|--------|
| Решения зависит **только** от "кто ты" | "ADMIN видит /admin, USER не видит" |
| Нет привязки к объекту | "Поддержка видит все тикеты" (не конкретный) |
| Меньше 5–7 ролей | Роль — строка, 10 ролей — уже запутанно |
| Нет "пересечений" | USER + ADMIN — ок. USER + ADMIN + MANAGER + OWNER — нет |

**Правило:** если можно сформулировать проверку как `token->hasRole('X')` — роли достаточно.

---

## 4. Voters: тонкий уровень

### Когда роли не хватает

```
"Пользователь может редактировать СВОЙ заказ, но не чужой."

→ hasRole('ROLE_USER') — true для всех.
→ hasRole('ROLE_ADMIN') — true для админа.
→ Но: "свой" vs "чужой" — это свойство ОБЪЕКТА, не роли.
```

### Пример: OrderVoter

```php
final class OrderVoter implements VoterInterface
{
    public const VIEW   = 'ORDER_VIEW';
    public const EDIT   = 'ORDER_EDIT';
    public const CANCEL = 'ORDER_CANCEL';

    public function vote(TokenInterface $token, object $subject, array $attributes): int
    {
        /** @var User $user */
        $user = $token->getUser();

        if ($user === null) {
            return AccessDecisionVoteInterface::ACCESS_DENIED;
        }

        foreach ($attributes as $attribute) {
            switch ($attribute) {
                case self::VIEW:
                    if ($this->canView($user, $subject)) {
                        return AccessDecisionVoteInterface::ACCESS_GRANTED;
                    }

                case self::EDIT:
                    if ($this->canEdit($user, $subject)) {
                        return AccessDecisionVoteInterface::ACCESS_GRANTED;
                    }

                case self::CANCEL:
                    if ($this->canCancel($user, $subject)) {
                        return AccessDecisionVoteInterface::ACCESS_GRANTED;
                    }
            }
        }

        return AccessDecisionVoteInterface::ACCESS_ABSTAIN;
    }

    private function canView(User $user, object $subject): bool
    {
        if (!$subject instanceof Order) {
            return false;
        }

        // Админ видит всё
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        // Владелец видит свой
        return $subject->ownerId === $user->id;
    }

    private function canEdit(User $user, object $subject): bool
    {
        if (!$subject instanceof Order) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        // Владелец + статус позволяет
        return $subject->ownerId === $user->id
            && in_array($subject->status, [OrderStatus::New, OrderStatus::Pending]);
    }

    private function canCancel(User $user, object $subject): bool
    {
        if (!$subject instanceof Order) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        // Владелец + только New
        return $subject->ownerId === $user->id
            && $subject->status === OrderStatus::New;
    }
}
```

### Использование

```php
// Controller
public function edit(Order $order): Response
{
    $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
    // → 403 если нельзя

    // ...
}

// Template
{% if is_granted('ORDER_VIEW', order) %}
    <a href="{{ path('order_show', {id: order.id}) }}">View</a>
{% endif %}

// Service (не только controller!)
class OrderService
{
    public function cancel(Order $order, User $user): void
    {
        $this->authorizationChecker->isGranted(OrderVoter::CANCEL, $order);
        // → бросает AccessDeniedException если нельзя
    }
}
```

---

## 5. Когда что выбирать

### Матрица

| Критерий | Роли | Voter |
|----------|------|-------|
| Решение зависит от **объекта**? | Нет | Да |
| Решение зависит от **состояния объекта**? | Нет | Да |
| Решение зависит от **отношения** user→object? | Нет | Да |
| Решение зависит от **времени**? (после 18:00) | Нет | Да (или expression) |
| Решение зависит от **внешнего сервиса**? (API key) | Нет | Да (или expression) |
| Решение зависит **только** от роли? | Да | Нет |
| Меньше 5 ролей? | Да | Нет |
| Нет пересечений "кто" × "что"? | Да | Нет |

### Примеры

**Роли достаточны:**

```
- Админ видит /admin
- Поддержка видит /support
- Пользователь видит /dashboard
```

**Нужен Voter:**

```
- Пользователь редактирует СВОЙ заказ
- Пользователь отменяет заказ ТОЛЬКО в статусе "New"
- Менеджер видит заказы СВОЕЙ команды (team_id)
- Пользователь видит документ ТОЛЬКО если он в его "shared with"
```

### Гибридный подход (самый частый в продакшене)

```
Route-level:        ROLE_*        (грубое отсечение)
Controller-level:   Voter         (тонкое, на объекте)
```

```php
// /admin — только админ (роль)
// /admin/orders/{id}/edit — админ ИЛИ владелец (voter)

#[Route('/admin/orders/{id}/edit')]
#[IsGranted('ORDER_EDIT')]  // ← voter, не роль
public function edit(Order $order): Response
{
    // Voter внутри:
    //   if (user has ROLE_ADMIN) → granted
    //   if (order.ownerId === user.id && order.status in [New, Pending]) → granted
    //   else → denied
}
```

---

## 6. Anti-patterns

### 1. "ROLE_ADMIN" для всего

```php
// ❌
#[IsGranted('ROLE_ADMIN')]
public function delete(Order $order) { ... }

// Админ может удалить чужой заказ, свой, заказ который уже отправлен.
// Voter бы проверил: order.status !== Shipped
```

### 2. Voter-на-роль

```php
// ❌ — Voter, который только проверяет роль
final class AdminVoter implements VoterInterface
{
    public function vote(TokenInterface $token, object $subject, array $attributes): int
    {
        if ($attributes === ['IS_ADMIN']) {
            return $token->hasRole('ROLE_ADMIN') ? ACCESS_GRANTED : ACCESS_DENIED;
        }
        return ACCESS_ABSTAIN;
    }
}

// ✅ Просто:
#[IsGranted('ROLE_ADMIN')]
```

### 3. Voter без object

```php
// ❌ — Voter, который не смотрит на $subject
final class FeatureFlagVoter implements VoterInterface
{
    public function vote(TokenInterface $token, object $subject, array $attributes): int
    {
        // $subject — всегда null, мы не передаём объект
        if ($attributes === ['BETA_FEATURE']) {
            return $this->featureFlags->isUserInBeta($token->getUser()->id);
        }
        return ACCESS_ABSTAIN;
    }
}
// Это не "доступ к объекту". Это "фича включена для юзера".
// → Лучше: $this->featureFlags->isUserInBeta($user->id) в контроллере напрямую.
```

### 4. Логика доступа в контроллере

```php
// ❌
public function edit(Order $order): Response
{
    if ($this->getUser()->hasRole('ROLE_ADMIN')) {
        // ...
    } elseif ($order->ownerId === $this->getUser()->id) {
        // ...
    } else {
        throw new AccessDeniedException();
    }
}
// Логика доступа в 5 контроллерах, в 3 шаблонах, в 2 сервисах.
// Изменили правило → поменяли в 10 местах.

// ✅
#[IsGranted('ORDER_EDIT')]
public function edit(Order $order): Response { /* ... */ }
// Логика — в OrderVoter. Один источник.
```

### 5. `denyAccessUnlessGranted` в сервисе без контекста

```php
// ❌
class OrderService
{
    public function cancel(Order $order): void
    {
        // Кто $this->getUser()? Сервис не знает.
        // Если вызывается из worker / cron / CLI → null → crash.
        $this->authorizationChecker->isGranted(OrderVoter::CANCEL, $order);
    }
}

// ✅
class OrderService
{
    public function cancel(Order $order, User $actor): void
    {
        $this->authorizationChecker->isGranted(OrderVoter::CANCEL, $order, $actor);
        // ↑ явный актор, не "текущий пользователь"
    }
}
```

---

## 7. Экспрессии — для простых случаев

Symfony поддерживает `expression` — inline-проверка без voter'а:

```php
#[IsGranted("object.ownerId == user.id")]

// или
#[IsGranted("object.status in constant('App\\Order\\OrderStatus::EDITABLE') and (object.ownerId == user.id or user.hasRole('ROLE_ADMIN'))")]
```

| | Voter | Expression |
|---|---|---|
| Переиспользование | ✅ (1 voter → 50 мест) | ❌ (дублируется) |
| Тестируемость | ✅ (unit-test voter) | ⚠️ (нужен `ExpressionLanguage` test) |
| Простота | Больше кода | Одна строка |
| Когда | Сложная логика, >3 условия | Простое условие, 1–2 места |

**Правило:** если выражение > 80 символов или используется > 2 раз → Voter.

---

## Итого

```
Аутентификация:  "кто ты?" → Token (User + Roles)
Авторизация:     "что ты можешь?" → AccessDecisionManager
  │
  ├─ Роли:        "ты ADMIN?" → да/нет
  ├─ Voter:       "ты можешь редактировать ЭТОТ объект?" → да/нет
  └─ Expression:  "object.ownerId == user.id" → да/нет (для простых случаев)
```

**Практическое правило:** начинай с ролей. Когда ролей не хватает (зависимость от объекта, состояния, времени) — добавляй Voter. Когда Voter'а на 50 строк и он используется в 1 месте — замени на Expression.

> ⏱ 39.0s · prompt 30862 tok · output 4700 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

We need answer in Russian, interview style. User asks: How to store Value Object in Doctrine if domain requires immutability and always valid state? Ways mapping, pros/cons pitfalls. Need think deeply. Need final only. Need likely cover: Value Objects in DDD: immutable, validated, equality. Doctrine by default maps persistent objects mutable entity graph. Options:

1) Store primitives in entity, construct VO in getters/constructors. Entity has private typed fields (int, string, array) and exposes VO. Example Money: int amount, string currency. Get money(): Money. Pros: simple, no Doctrine magic, serialization, DB-level queries, no custom types needed? Actually can use DB-level. Cons: duplication, invariants not enforced at DB/Doctrine level, entity setters might mutate raw fields, hydration breaks VO? Need readonly? PHP readonly properties? Can't easily with Doctrine because hydrator sets via reflection? If using constructor? Better: private fields, no setters or controlled.

2) Custom Doctrine Type with getSQLDeclaration, convertToPHPValue, convertToDatabaseValue, requiresSQLConversion maybe. Map entity field as object type. Example MoneyType extends Type. convertToPHPValue constructs Money from parts? If stored as one column? Could store JSON or string. Need equals/hashCode? Doctrine identity? Custom type must return scalar/object? It can return object, but then Doctrine may cache it? It stores in column(s). Need be careful with immutable object and change tracking: if same value, Doctrine compares? For custom type, convertToDatabaseValue called. Need requiresSQLConversion to true? If column type not native. Pros: encapsulation, entity field is VO. Cons: Doctrine hydrator replaces field with new object? Change detection? Serialization? Type needs registration. Custom types with arrays? If using object in entity, equality for change tracking? Doctrine's change tracking uses spl_object_id? For basic values? For objects in fields: it compares using ==? Actually Doctrine compares field values: if object, maybe checks identity? Let's recall: Doctrine ORM UnitOfWork::computeChangeSet compares originalData and currentData. For each field if value != original? It uses ==? Need exact. It has isIdentifier? For arrays? In doctrine/orm, compareFieldValue uses ===? Let's recall: if is_object($value) && is_object($originalData) maybe if $value == $originalData? It may call == for objects. For immutable VO with value equality == might work if no private? Actually == compares properties with same name/value; for objects with private properties? For same class, == compares all properties? I think yes if public/protected/private? In PHP == objects: same class and have same properties with same values. For private, only if same class? likely. So could detect change if fields differ. But readonly? If object immutable and new instance each hydration, original and current may differ even if equal? Doctrine hydrator: for simple object types? It may not rehydrate if same? Let's examine: Custom type convertToPHPValue returns object. When hydrating row, it calls type->convertToPHPValue. It may produce new object. Then UnitOfWork compares to original. If == value equal, no change. If object identity not used. So okay if value equality works. But pitfalls: object not implementing __toString, serialization.

3) Use embedded object / embeddable class. Embeddable with fields amount/currency; entity has Money $money. Doctrine embeds fields in columns. Embeddable class can be immutable? It can have readonly? But Doctrine hydrator instantiates and sets properties via reflection, potentially bypassing constructor. It can have getters. Need no setters? Embeddable requires instantiable. Pros: composite object, mapping declarative. Cons: embeddable is mutable by hydration, less encapsulated, can't enforce invariants on construction? Could use constructor but Doctrine may still instantiate without constructor? In Doctrine 2 embeddable instantiation uses hydrator, probably instantiates without constructor and sets fields. Need be careful. In 3? Still. Also embedding in DQL, partial fields? Not first-class.

4) Use custom types with multiple columns and SQL conversion. Store Money as amount numeric, currency char(3). But custom type one column? Could use array mapping? Custom types can declare SQL as table? No, type maps one column? Actually custom type maps a column; for multiple columns need Embeddable or multiple fields. Could store JSON string and convert. Cons: no native constraints, indexes? JSON query limitations.

5) Store in DB with generated/validated domain object, use readonly and constructor promotion, but map primitives. In Doctrine 3 maybe native custom mapping? Need discuss PHP readonly + Doctrine: cannot map readonly properties directly because Doctrine needs set them; using constructor? Could use custom hydrator? But generally avoid readonly mapped properties. If using PHP 8.1 readonly, hydration via reflection might fail? Reflection can set readonly once? Actually ReflectionProperty::setValue on uninitialized readonly allowed? In 8.1, can set readonly via reflection? It can be set once, but after hydration maybe. However if object already initialized? Could cause issues. Safer not to mark mapped fields readonly. If using constructor with promoted readonly, Doctrine can't hydrate easily. So recommendation: don't use readonly for mapped properties, or use custom hydration.

6) Use DB enum / custom type for simple VOs: e.g. Status enum backed. Doctrine has EnumType. Store string. VO with methods. Good for finite state.

7) Use database constraints: CHECK, triggers, generated columns to enforce valid state at DB level. E.g. Money: amount >=0, currency char(3). Or custom SQL for IP? Can't fully.

8) Use event listeners / callbacks / validators before persist to ensure invariants.

9) For value object as part of aggregate, treat entity as wrapper: entity fields primitives, VO methods used in domain. Avoid exposing raw fields. Provide factory methods, withX methods.

Need answer likely: Doctrine does not know immutability; persistence layer will create mutable objects. Need separate persistence model and domain model? Options: anti-corruption layer, hydration into VO. But question asks how store VO, mapping, pros cons pitfalls.

Need maybe mention Doctrine DBAL types vs ORM types; custom types, embeddable, primitive fields. Also equality and change tracking. And hydration: object instances may differ, Doctrine's compare. Need pitfalls: object equality, lazy hydration, serialization, DQL comparisons, DB constraints, invalid data from DB, concurrent updates, immutable VO with mutable container.

Need provide practical recommendation: For most VOs, map to primitives in entity and expose immutable VO via constructor/getters; for composite VOs use embeddable or custom type with scalar columns; if VO has finite set, use backed enum + custom enum type. Never rely on Doctrine to validate. Enforce in VO constructor and DB constraints. Use readonly only for non-mapped VO fields, not mapped entity fields.

Need include code examples:

Money:
```
final class Money {
 private function __construct(private int $amount, private string $currency) { if invalid throw; }
 public static function of(int|string $amount, string $currency): self { ... }
 public function amount(): int; currency(): string; add; equals; __toString.
}
```
Entity option A primitives:
```
class Order {
 private int $totalAmount;
 private string $totalCurrency;
 public function getMoney(): Money { return Money::of($this->totalAmount, $this->totalCurrency); }
 public function setMoney(Money $money): void { $this->totalAmount=...; }
}
```
But need ensure no setters for raw fields or controlled. Use protected? Could use Doctrine readonly? maybe not.

Option B custom type:
```
final class MoneyType extends Type {
 public const NAME = 'money';
 public function getSQLDeclaration(...): string { return 'json'; } // or varchar
 public function convertToPHPValue($value, AbstractPlatform $p): Money { ... }
 public function convertToDatabaseValue($value, AbstractPlatform $p): string { return $value->json(); }
 public function getName(): string { return self::NAME; }
}
#[ORM\Column(type: MoneyType::NAME)]
private Money $total;
```
Pros entity property is Money. Cons: JSON not queryable, no DB constraints, serialization, type registration, change tracking if object equality not good; if stored scalar string and parsed every time; object may be mutable if not final/readonly. If convertToPHPValue returns same object from static cache? Avoid caches? If cache Money objects, then mutable? If immutable okay. But Doctrine might expect new? If cache, change detection? If current and original same object, no change. If modify? immutable so no. But cache may be okay. But hydration with object cache? Could cause cross-entity shared instances; if immutable okay but memory and concurrency? Better not cache.

Option C embeddable:
```
#[ORM\Embeddable]
final class Money {
 #[ORM\Column(type: integer)] private int $amount;
 #[ORM\Column(type: string, length:3)] private string $currency;
 public function __construct(int $amount, string $currency){...}
 public static function of...;
}
#[ORM\Embedded(class: Money::class)]
private Money $total;
```
Pros multiple columns, queryable (order.total_amount), declarative, DB constraints possible. Cons embeddable class is not truly VO? Hydration may instantiate without constructor; properties mutable; Doctrine embeddable not first-class in DQL? can use path? In DQL: o.total.amount. Change tracking works. Need maybe no setters. Pitfall: if use readonly properties, hydrator may fail? Need test. Embeddable can be inherited? no.

Option D enum:
```
enum OrderStatus: string { case New; ... }
#[ORM\Column(type: Types::STRING)]
private OrderStatus $status; // with EnumType or native enum in DB? Doctrine 2.14? custom type.
```
Pros finite valid states, PHP 8.1 native, DB string. Cons not for composite.

Need discuss change tracking: For object values, Doctrine compares field values. If VO == works? Need ensure. For custom type returning object, UnitOfWork uses compare. Let's be accurate: It compares field values; for objects it uses `==`? Need check. In Doctrine ORM 2 UnitOfWork::computeChangeSet:
```
foreach ($this->entityStates[$oid] as $field) {
   if (!isset($this->originalEntityData[$oid][$field]) ...
   if (isset($this->entityChanges[$oid][$field])) continue;
   if ($this->isUninitialized(...)) ...
   if ($this->originalEntityData[$oid][$field] != $data[$field]) { $this->setEntityChangeSet... }
}
```
It uses `!=` (loose equality). For objects, `!=` uses `==` negation. For immutable VO with value properties, if same values then equal. But if object has closures? no. If object is readonly? == okay. However if two different objects with same values, `==` true. If object contains array of objects? == recursively? likely. But if VO includes DateTimeImmutable? == compares? DateTime objects? `==` for DateTime objects? I think compares properties? DateTime immutable might have internal? Better implement `equals` and store scalars. For change detection, avoid complex object equality? If using custom type that stores string/json, Doctrine compares original data? For custom types, originalEntityData likely stores PHP value after convertToPHPValue? It may store object. If object equality unreliable, use scalar representation for comparison? Could override __toString? Not used. Could implement ==? PHP doesn't allow. So best keep VO value equality simple or store primitives. If object has private properties, == still compares? Need verify: PHP manual: "Two objects are equal if they have the same properties and values, and are instances of the same class." Private properties? If class same, yes. For readonly properties? likely. But internal objects? Not reliable for DB? For custom types, if convertToDatabaseValue returns string, convertToPHPValue returns object, and original/current object equality by properties. Good if no internal unexposed state.

But if VO contains immutable collection? == might not compare deeply? PHP == on objects with array property containing objects? It compares recursively? I think == compares recursively with same rules. But not 100. Avoid.

Need mention DB-level invalid data: DBA inserts invalid currency, ORM may throw in convertToPHPValue; that's okay. But if using string column and custom VO, can store invalid unless VO constructor validates and exception on hydration. Could mask as 500. Use DB constraints and maybe default. For Money: amount int, currency char(3), CHECK currency IN? Use check. For composite: embeddable columns.

Need mention concurrency: if VO has version? not relevant.

Need mention serialization: If VO object in entity and serialized, need __serialize or public props. Doctrine hydration does not use constructor. If entity serialized with VO, must be serializable. If custom type uses JSON string, serialization easier? But object still.

Need mention equality: implement equals, hashcode if needed; but Doctrine doesn't use equals for identity. Avoid using object identity.

Need mention readonly class: if VO is readonly final class with private readonly props, great for domain. But mapping as embedded? Doctrine may not instantiate with constructor; can set readonly? In PHP, readonly properties can be initialized only once. Reflection can set? In 8.1, reflection can set readonly? Let's recall: readonly properties can be initialized only once, can be set via ReflectionProperty::setValue? PHP 8.1: "They can be initialized only once, either directly or via a constructor, or by the ReflectionProperty::setValue() method." Yes. Doctrine hydrator may instantiate without constructor, then set properties. That's allowed once. But if object reused? If Doctrine creates new, okay. If it tries to rehydrate same object and set again? It likely creates new. But readonly on embeddable may be risky. For mapped entity fields, avoid readonly. For VO fields, if not mapped, okay.

Need mention constructor-based mapping: Doctrine 2 doesn't support constructor injection for entities (except proxy?). You can use custom hydrators / event listeners or factories to hydrate, but not worth. For entities with VO, use private fields.

Need mention using DB views/generated columns? For computed VOs? e.g. full name from first/last. Store separate, expose VO.

Need mention "always valid state" means VO constructor validates; entity should not expose setters that allow invalid raw state. For mapped primitives, use private and only domain methods. For custom type, constructor validates. For embeddable, can't fully prevent raw property mutation unless readonly? If readonly, hydration? Use final class, no setters, protected? Public readonly? If readonly, cannot change. But if need update, replace whole VO: `$order->total = Money::new(...)`? If entity property Money not readonly? Could be. But Doctrine change tracking: replace object. If Money readonly, good. But Doctrine may still instantiate Money without constructor for embedded. Need test.

Need maybe recommend mapping matrix:

| VO type | Recommended |
| scalar single value (Money? maybe composite) | custom type or primitives |
| enum finite | native backed enum / enum type |
| composite (Address, Money, Range) | embeddable (multiple columns) or custom type JSON if not queried |
| complex read-only reference? | primitives + factory |

Need discuss trade-offs of custom type JSON:
Pros:
- entity property is VO
- one field, simple
- VO constructor can validate
- less code than embeddable for 1-2 fields? Actually more type code.
Cons:
- JSON opaque: can't query components unless DB JSON support and expression
- no column-level constraints except whole string
- type conversion in both directions; performance parse
- type registration global; versioning; upgrade issues
- object equality in change tracking; if convertToPHPValue returns same cached object? need careful
- DB migrations: JSON type not portable.

Embeddable pros:
- multiple native columns; queryable; constraints; indexes
- no custom type registration? Actually embeddable still no global type.
- Doctrine handles mapping; less code than multiple fields.
Cons:
- embeddable class less encapsulated; Doctrine bypasses constructor; properties might be mutable unless readonly; no first-class entity? can't have relationships in embeddable? (can embed relations? not recommended? Doctrine 2 allows one-to-one? maybe but complex)
- DQL partial? path expression works.
- Embeddable cannot be serialized? It can.
- Can't inherit? no.
- Hydration with readonly? pitfalls.

Primitive fields pros:
- simplest, transparent, easy queries, migrations, DBA understands
- best performance, no custom conversions
- easy change tracking
Cons:
- duplicates VO logic, entity knows about internal fields
- risk of exposing setters; must discipline
- more code for multiple VOs
- domain logic in entity getters; VO not directly mapped.

Enum pros:
- compile-time safety, match exhaustiveness
- simple
Cons: only scalar, DB migration, old DB.

Need mention anti-corruption/persistence separation: If domain strict, map to persistence DTO and convert. E.g. DomainOrder vs DoctrineOrder. But that's heavy. Could mention as last resort.

Need include concrete pitfalls:
- Don't use `readonly` on mapped entity properties (or test carefully). Use private fields.
- Don't rely on Doctrine to call VO constructor.
- Don't use mutable VO.
- Don't use custom type that returns mutable object or caches.
- Don't expose raw fields or magic setter.
- Ensure `convertToDatabaseValue` handles null; `convertToPHPValue` handles invalid data with clear exception.
- Ensure types registered in doctrine.yaml.
- Ensure equality: `==` works or store scalar; for custom type, maybe store scalar in original? Hmm.
- For DB constraints, add check to prevent invalid.
- For DQL comparisons, custom type with object not directly comparable? In DQL, `WHERE o.total = :money`? It may call type conversion? Might not. Use components.
- For partial object hydration with custom type object: if object large, parsing cost; for embeddable fields maybe only columns.
- For proxies, if VO in lazy entity? okay.
- Serialization of objects with closures? no.

Need maybe provide recommended pattern:

```
final class Money {
 private function __construct(
   private int $amount,
   private string $currency,
 ) { if ($amount < 0) throw; if (strlen($currency)!==3) throw; }
 public static function of(int $amount, string $currency): self { ... }
 public static function fromString(string $json): self { ... }
 public function toDatabaseString(): string { return $this->currency.' '.$this->amount; }
 public function equals(self $other): bool { ... }
 public function jsonSerialize(): string { ... }
}
```

Option A:
```
class Order {
 #[ORM\Column(type: Types::INTEGER)]
 private int $totalAmount;
 #[ORM\Column(type: Types::STRING, length: 3)]
 private string $totalCurrency;

 private function __construct(int $amount, string $currency) { if invalid throw; }
 public static function create(int $amount, string $currency): self { return new self(Money::of(...)); }
 public function total(): Money { return Money::of($this->totalAmount, $this->totalCurrency); }
 public function setTotal(Money $money): void { if ($money->amount() < 0) throw; $this->totalAmount=...; }
}
```
But constructor with raw fields? Need validate. Use factory:
```
private function __construct(int $amount, string $currency) { $money=Money::of(...); $this->totalAmount=$money->amount(); ... }
```

Option B custom type:
```
final class MoneyType extends Type {
 public const NAME = 'money';
 public function getSQLDeclaration(int $length, AbstractPlatform $platform): string { return 'varchar(64)'; }
 public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money { return $value === null ? null : Money::fromString($value); }
 public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string { return $value === null ? null : $value->toDatabaseString(); }
 public function getName(): string { return self::NAME; }
}
```
Entity:
```
#[ORM\Column(type: MoneyType::NAME)]
private ?Money $total = null;
```
Need note: if Money not serializable? It is. For DQL, can't query amount directly.

Option C embeddable:
```
#[ORM\Embeddable]
final class Money {
 #[ORM\Column(type: Types::INTEGER)]
 private int $amount;
 #[ORM\Column(type: Types::STRING, length: 3)]
 private string $currency;
 private function __construct(int $amount, string $currency) { ... }
 public static function of(int $amount, string $currency): self { ... }
 public function amount(): int { return $this->amount; }
 public function currency(): string { return $this->currency; }
 public function equals(self $other): bool { ... }
}
#[ORM\Embedded]
private Money $total;
```
Pitfall: hydrator may instantiate without constructor; so private constructor? If Doctrine can't instantiate? It uses ReflectionClass::newInstanceWithoutConstructor? likely can, even private? It can? Yes if accessible. But better no private constructor? Need ensure. Embeddable must be instantiable; private constructor okay? Reflection can instantiate if newInstanceWithoutConstructor? I think yes. But setters? If no setters and properties private, hydrator sets via reflection. Okay. But if constructor validates, not called. Need ensure properties initialized? Hydrator sets. If property uninitialized typed private? If typed `private int $amount;` and hydrator sets. OK. If constructor private, domain can't create? Use static factory. But hydrator not call. Good.

Could use readonly in embeddable? Maybe risky. If readonly, hydration sets once. But if object reused? Not. Avoid.

Need mention DB constraints for embeddable:
```
ALTER TABLE orders ADD CONSTRAINT chk_total_amount CHECK (total_amount >= 0);
ALTER TABLE orders ADD CONSTRAINT chk_total_currency CHECK (total_currency ~ '^[A-Z]{3}$');
```

Need mention if using custom type string, DB constraint:
```
CHECK (total ~ '^[A-Z]{3} [0-9]+$')
```
less robust.

Need mention using Doctrine DBAL types and ORM types: type name must be registered. For migrations, `->addColumn('total', 'money')` after type registered.

Need mention if VO contains nested VO (Address with City, Money), embeddable can contain embeddables? I think yes. But query path? maybe. Custom type JSON easier.

Need mention if VO is used in DQL comparisons: for custom type object, you might pass Money to query parameter; Doctrine will convert? It may call convertToDatabaseValue. But comparisons in DQL like `o.total > :amount` not meaningful if string. Need components.

Need mention equality and caching: If `convertToPHPValue` returns static cached Money object, multiple entities may share same instance. Since immutable okay, but if VO has mutable state or time, bad. Also change tracking: original and current might be same object, so no change. If you replace with equal object? if same cached object, no change. That's okay. But if different values, different cached. Caching unbounded? avoid.

Need mention "always valid state" in domain means entity should not expose `setTotalAmount(int)` that can make invalid. Use `withTotal(Money $money)`. If stored primitives, use private fields and controlled methods.

Need maybe propose final recommendation:
- For single scalar VO (Status, Currency, Phone?) use backed enum or custom scalar type.
- For small composite Money/Address, use embeddable with native columns if need query/constraints; or custom type JSON if no query. If performance and simplicity, primitives + VO façade.
- In all cases, VO final immutable with constructor/factory and validation; DB constraints; no setters; Doctrine hydration does not guarantee domain invariants, so validate on read if trusting external DB.

Need answer as if interview. Could structure: "My default", "Options", "Pitfalls", "Decision".

Need be careful with Symfony? User asks Doctrine. Need mention PHP 8? Use readonly? Need not overdo.

Potential issue: Doctrine 2 vs 3. We can mention Doctrine ORM 2, and note Doctrine 3/DBAL may change but principles. Avoid version-specific. Use `Doctrine\DBAL\Types\Type`.

Need maybe discuss "Value Object" as non-identity, no ID, equals by value. Doctrine entity has identity. If VO used as identifier? Composite IDs? Not relevant.

Need mention if VO is in aggregate root, not standalone entity. If it has own DB table? Then it's not VO? Maybe domain model. If value object needs to be queried, maybe entity/reference.

Need mention "persisting VOs as part of entity vs separate entity". If separate entity with ID, then it's entity, not VO. If multiple rows reference same value? Could use reference data. But question storage.

Need mention serialization and `__toString` for logs.

Need mention testing: unit test VO, integration test hydration with invalid DB row.

Let's craft final in Russian with code snippets. Need likely include "подрыв"? Use "подводные камни".

Detailed final:

Intro: Doctrine doesn't know immutable/valid; it's object hydration. Need choose mapping that lets domain own invariants.

1.基本原则:
- VO final, immutable, constructor/factory validates, no setters, value equality.
- Entity/persistence can be different; don't expose mutable raw fields.
- Enforce in DB too.

Option A: primitives + VO facade.
Code. Pros/Cons.

Option B: custom DBAL type.
Code. Pros/Cons. Include register.

Option B variant: multiple columns? custom type only one column; for composite use string/JSON. If need separate columns, Option C.

Option C: embeddable.
Code. Pros/Cons.

Option D: backed enum.
Code.

Then pitfalls:
- Hydration bypasses constructor (embeddable/custom type). Don't rely on constructor for persistence invariants; validate in convert or after hydration? Actually if custom type constructor validates in convertToPHPValue; if embeddable, invalid columns could cause uninitialized? Use DB constraints and possibly event listener afterLoad to validate? But can't fix. If invalid, throw.
- readonly properties on mapped fields can break hydration; use private non-readonly for mapped, readonly only inside VO if not mapped? Wait if VO is embedded and mapped, its properties are mapped; avoid readonly. If VO is custom type returned object, its properties not mapped by Doctrine, can be readonly.
- Change tracking: object equality, `==`, no identity. If VO has complex internal objects or closures, change detection unreliable. Store scalars or implement simple ==.
- Caching in convertToPHPValue: shared instances, memory, cross-request.
- Custom type global, migrations, not portable.
- DQL: cannot query inside JSON; embeddable better.
- DB invalid data: hydration exception; need constraints.
- Serialization: ensure `__serialize` or public/readonly.

Recommendation matrix.

Need include "what I choose" maybe: For Money: if need query by amount/currency, embeddable; if not, custom type or primitives. For Status: enum. For Address: embeddable if query by city/street, else custom JSON.

Need mention anti-corruption layer: If domain model is very strict and persistence model diverges, separate Doctrine entity and domain object, map via repository. Heavy but for critical aggregates.

Let's ensure answer not too long but desired oververbosity 9. Include examples.

Need avoid unsupported: `readonly class` with embedded? mention test.

Potential nuance: Doctrine change tracking for object fields: If object value changed but `==` equal? no change. If `==` not equal? change. If `convertToDatabaseValue` returns same string? good. If object has mutable internal array? could fail. Mention.

Need mention `requiresSQLConversion()`: For custom type that stores non-native? If SQL declaration is varchar, no need? If type name 'money' not standard, migrations need requiresSQLConversion? Actually if getSQLDeclaration returns 'money' custom? Better return 'varchar'. If type name is not DB type, set `requiresSQLConversion(): bool { return true; }`? In Doctrine, for custom types, if SQL declaration is a known DB type? If using `json`, maybe. To be safe: `public function requiresSQLConversion(): bool { return true; }`? But if platform knows json? If returns true, migrations add custom type. Need not mention too deep.

Need mention null handling: `convertToPHPValue` and `convertToDatabaseValue` must accept null.

Need mention `getName()` in PHP 8? For backward.

Need maybe mention "Value object with methods used in DQL?" not.

Let's write final in Russian, with headings. Ensure answer directly: "Как хранить".

Need final only.

</details>

**Кандидат:**

Хороший вопрос, потому что **Doctrine не понимает DDD-инварианты**. Для него значение объекта — это просто данные в колонках, а доменный Value Object — это класс с поведением, иммутабельностью и конструктором, который гарантирует валидное состояние.

Я бы разделял три слоя ответственности:

1. **Доменный VO** — неизменяемый, валидный, с логикой и equality.
2. **Persistence-модель** — колонки БД, типы, mapping.
3. **Граница между ними** — преобразование и защита от invalid state.

Ниже — практические варианты маппинга.

---

# 1. Хранить примитивы в сущности, а VO выставлять как façade

Это самый безопасный и, на мой взгляд, часто лучший вариант.

Например, `Money`:

```php
final class Money
{
    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Amount must be positive or zero');
        }

        if (strlen($currency) !== 3) {
            throw new \DomainException('Currency must be ISO 4217 code');
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Cannot add different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}
```

А Doctrine-сущность хранит примитивы:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $totalCurrency;

    private function __construct(int $amount, string $currency)
    {
        $money = Money::of($amount, $currency);

        $this->totalAmount = $money->amount();
        $this->totalCurrency = $money->currency();
    }

    public static function create(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function total(): Money
    {
        return Money::of(
            $this->totalAmount,
            $this->totalCurrency,
        );
    }

    public function setTotal(Money $money): void
    {
        $this->totalAmount = $money->amount();
        $this->totalCurrency = $money->currency();
    }
}
```

### Плюсы

- Максимальная прозрачность в БД.
- Легко писать DQL:

```php
$em->getRepository(Order::class)->createQueryBuilder('o')
    ->where('o.totalAmount > :amount')
    ->andWhere('o.totalCurrency = :currency')
    ->setParameter('amount', 100)
    ->setParameter('currency', 'RUB');
```

- Легко читать, отлаживать, мигрировать.
- Doctrine не должен понимать ваш `Money`.
- Change tracking работает максимально предсказуемо.
- Нет кастомных типов, нет магии.

### Минусы

- В сущности есть внутренние поля `totalAmount`, `totalCurrency`.
- Нужно следить, чтобы наружу не выходило raw-состояние.
- Нужно не дать случайно сделать:

```php
$order->totalAmount = -5;
```

### Подводные камни

Нужно закрывать raw-поля от произвольного изменения.

Нежелательно:

```php
public function setTotalAmount(int $amount): void
{
    $this->totalAmount = $amount;
}
```

Потому что это ломает инвариант.

Лучше:

```php
public function setTotal(Money $money): void
{
    $this->totalAmount = $money->amount();
    $this->totalCurrency = $money->currency();
}
```

И желательно сделать поля `private`.

---

# 2. Custom Doctrine Type: хранить VO как отдельный тип

Этот подход подходит, когда VO — это компактная сущность, которую удобно хранить как одно значение.

Например:

```php
final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
    {
        return 'VARCHAR(32)';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Money::fromString($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value->toDatabaseString();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

И в сущности:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: MoneyType::NAME)]
    private ?Money $total = null;
}
```

Где `Money`:

```php
final class Money
{
    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Amount must be non-negative');
        }

        if (strlen($currency) !== 3) {
            throw new \DomainException('Invalid currency');
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public static function fromString(string $value): self
    {
        [$amount, $currency] = explode(' ', $value, 2);

        return new self((int) $amount, $currency);
    }

    public function toDatabaseString(): string
    {
        return $this->amount . ' ' . $this->currency;
    }
}
```

### Плюсы

- В сущности сразу доменный объект:

```php
#[ORM\Column(type: MoneyType::NAME)]
private ?Money $total = null;
```

- Не нужно дублировать `amount` и `currency` в сущности.
- Валидация происходит в `convertToPHPValue()` и конструкторе `Money`.
- Код читается как доменная модель.

### Минусы

- Сложнее SQL.
- Сложнее DQL, если нужно фильтровать по `amount` или `currency` отдельно.
- Нужно регистрировать тип:

```yaml
doctrine:
    dbal:
        types:
            money: App\Doctrine\Types\MoneyType
```

- Миграции становятся менее прозрачными.
- Если VO сложный, custom type может превратиться в JSON-хранилище.

### Подводные камни

#### 1. Не делайте кэширование объектов в `convertToPHPValue()`

Плохо:

```php
private static array $cache = [];

public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
{
    return self::$cache[$value] ??= Money::fromString($value);
}
```

Почему:

- Объекты могут переиспользоваться между сущностями.
- Это может нарушить serialization.
- Если VO когда-то станет mutable — будут страшные баги.
- Change tracking может вести себя неочевидно.

Лучше каждый раз создавать новый объект или быть на 100% уверенным, что VO неизменяем.

#### 2. Equality должен быть простым

Doctrine сравнивает значения полей при change tracking. Если объект сравним через `==`, всё ок.

Но если внутри VO сложная структура:

```php
private readonly array $items;
private readonly \Closure $callback;
private readonly object $service;
```

то сравнение становится ненадёжным.

Для persistence лучше хранить VO с простым значениевым содержанием:

- int
- string
- bool
- простые arrays

#### 3. Нельзя свободно использовать DQL

Хранение:

```text
"100 RUB"
```

удобно, но запрос:

```sql
WHERE amount > 100
```

уже не тривиален.

Если нужно часто фильтровать по частям VO — лучше embeddable или примитивы.

---

# 3. Embeddable: хранить VO как составной объект из нескольких колонок

Это, пожалуй, лучший вариант для составных VO, когда нужно:

- хранить несколько частей отдельно;
- фильтровать по ним;
- индексировать;
- накладывать DB-констрейнты.

Например, `Money`:

```php
#[ORM\Embeddable]
final class Money
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    private function __construct(int $amount, string $currency)
    {
        if ($amount < 0) {
            throw new \DomainException('Amount must be non-negative');
        }

        if (strlen($currency) !== 3) {
            throw new \DomainException('Invalid currency');
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}
```

В сущности:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Embedded(class: Money::class)]
    private Money $total;
}
```

### Плюсы

- VO выглядит как доменный объект:

```php
$order->total->amount();
```

- Колонки в БД нормальные:

```sql
total_amount INTEGER
total_currency VARCHAR(3)
```

- Можно писать DQL:

```php
->where('o.total.amount > :amount')
->andWhere('o.total.currency = :currency')
```

- Можно делать индексы:

```sql
CREATE INDEX idx_orders_total_amount ON orders (total_amount);
```

- Можно добавить DB-констрейнты:

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_total_amount_non_negative
    CHECK (total_amount >= 0);

ALTER TABLE orders
    ADD CONSTRAINT chk_total_currency_iso
    CHECK (total_currency ~ '^[A-Z]{3}$');
```

### Минусы

- Embeddable — это не полноценная доменная сущность.
- Doctrine может создавать объект без вызова конструктора.
- Если свойства public — их можно мутировать извне.
- Нужно следить, чтобы наружу не выходило raw-состояние.

### Подводные камни

#### 1. Doctrine hydrates objects without constructor

Это важно.

Doctrine может сделать примерно так:

```php
$money = new Money();
$money->amount = 100;
$money->currency = 'RUB';
```

То есть ваш конструктор и его проверки могут не вызываться.

Поэтому нельзя полагаться только на:

```php
private function __construct(...)
{
    // validation
}
```

для безопасности данных из БД.

Нужна защита:

1. конструктор / factory в домене;
2. DB-констрейнты;
3. при необходимости проверка после загрузки.

Например:

```php
#[ORM\Embeddable]
final class Money
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    public function amount(): int
    {
        if ($this->amount < 0) {
            throw new \DomainException('Invalid amount');
        }

        return $this->amount;
    }

    public function currency(): string
    {
        if (strlen($this->currency) !== 3) {
            throw new \DomainException('Invalid currency');
        }

        return $this->currency;
    }
}
```

Или делать проверку в сущности:

```php
public function total(): Money
{
    if ($this->total->amount() < 0) {
        throw new \DomainException('Invalid order total');
    }

    return $this->total;
}
```

Но DB-констрейнт всё равно важнее, потому что защита от bad data в БД должна быть ближе к БД.

#### 2. Не делайте embeddable mutable без контроля

Если:

```php
#[ORM\Embeddable]
class Money
{
    public int $amount;
    public string $currency;
}
```

то наружу уходит:

```php
$order->total->amount = -100;
```

Это ломает инвариант.

Лучше:

```php
public function amount(): int
{
    return $this->amount;
}

public function currency(): string
{
    return $this->currency;
}
```

И заменить весь VO:

```php
$order->total = Money::of(200, 'RUB');
```

#### 3. `readonly` в mapped-свойствах — осторожно

В PHP 8.1 можно сделать:

```php
readonly int $amount;
```

Но Doctrine при hydration устанавливает значения через reflection. В некоторых версиях/сценариях это может работать, но это отдельный источник проблем.

Мой практический подход:

- для mapped-полей — обычные `private` свойства;
- для доменного VO вне mapping — можно использовать `readonly`;
- для embeddable — проверять отдельно, что hydration работает.

---

# 4. Backed Enum: если VO — это конечное состояние

Для статусов, типов, режимов — лучший вариант.

Например:

```php
enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
```

В сущности:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: Types::STRING, length: 32)]
    private OrderStatus $status;
}
```

Или через custom enum type, если хотите более строгий маппинг.

### Плюсы

- PHP уже гарантирует, что значение принадлежит enum.
- Легко писать:

```php
if ($order->status === OrderStatus::Paid) {
    // ...
}
```

- Хорошо сочетается с `match`:

```php
return match ($order->status) {
    OrderStatus::New, OrderStatus::Paid => true,
    OrderStatus::Shipped, OrderStatus::Cancelled => false,
};
```

- Простое хранение в БД: строка.

### Минусы

- Подходит только для конечных значений.
- Не подходит для составных VO вроде `Address`, `Money`, `Range`.

### Подводные камни

Если enum содержит сложную логику, но хранится как строка, БД всё равно может хранить мусор:

```sql
INSERT INTO orders (status) VALUES ('invalid_status');
```

Поэтому лучше добавить DB-констрейнт:

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_order_status
    CHECK (status IN ('new', 'paid', 'shipped', 'cancelled'));
```

---

# 5. JSON-колонка + custom type

Иногда проще хранить сложный VO как JSON.

Например:

```php
final class Address
{
    private function __construct(
        private readonly string $country,
        private readonly string $city,
        private readonly string $street,
        private readonly string $zip,
    ) {
        if ($country === '') {
            throw new \DomainException('Country is required');
        }

        if ($city === '') {
            throw new \DomainException('City is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['country'] ?? '',
            $data['city'] ?? '',
            $data['street'] ?? '',
            $data['zip'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'zip' => $this->zip,
        ];
    }
}
```

Custom type:

```php
final class AddressType extends Type
{
    public const NAME = 'address';

    public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
    {
        return 'JSON';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Address
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        return Address::fromArray($data);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value->toArray(), JSON_THROW_ON_ERROR);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

### Плюсы

- Быстро реализовать.
- Хорошо подходит для read-heavy или auxiliary data.
- Не обязательно делать каждую часть колонкой.
- Удобно для сложных структур, которые редко используются в фильтрах.

### Минусы

- Сложно писать нормальные SQL-фильтры.
- Сложнее индексы.
- Сложнее DB-констрейнты.
- Миграции менее прозрачны.
- Легко превратить в "черный ящик".

### Подводные камни

JSON — это удобно, но это не замена нормальному mapping, если:

- часто фильтруете по частям;
- нужно индексировать;
- данные критичны;
- несколько сервисов используют их.

Для `Address` я бы обычно предпочёл embeddable или примитивы, если адрес участвует в поиске, фильтрах и бизнес-правилach.

---

# Сравнение подходов

| Подход | Когда использовать | Плюсы | Минусы |
|---|---|---|---|
| Примитивы + VO façade | Для простых VO, особенно `Money`, `Phone`, `Period` | Прозрачность, DQL, performance, надёжный change tracking | Больше полей в сущности |
| Custom scalar type | Когда VO компактный и не нужен частый DQL по частям | В сущности сразу VO | Сложнее SQL, нужно регистрировать тип |
| Embeddable | Составной VO из нескольких колонок | Нормальные колонки, DQL, индексы, constraints | Hydration может не вызвать конструктор |
| Backed enum | Конечные состояния | Максимальная типобезопасность в PHP | Только для enum-значений |
| JSON custom type | Сложные вспомогательные структуры | Гибкость, простота | Плохой DQL, сложные constraints |

---

# Что я бы выбирал в реальном проекте

## Для `Money`

Если деньги используются в фильтрах:

```sql
WHERE total_amount > 100 AND total_currency = 'RUB'
```

— я бы использовал **примитивы + VO façade** или **embeddable**.

Например:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $totalCurrency;

    public function total(): Money
    {
        return Money::of($this->totalAmount, $this->totalCurrency);
    }

    public function setTotal(Money $money): void
    {
        $this->totalAmount = $money->amount();
        $this->totalCurrency = $money->currency();
    }
}
```

Если деньги почти никогда не используются в DQL, но хочется чистоту — **custom type** тоже ок.

## Для `OrderStatus`

Обязательно **backed enum**.

```php
enum OrderStatus: string
{
    case New;
    case Paid;
    case Shipped;
    case Cancelled;
}
```

## Для `Address`

Если адрес нужен для:

- поиска по городу;
- фильтрации по стране;
- индексов;
- отчётов;

— **embeddable** или **примитивы**.

Если адрес просто хранится как "контекст", например для биллинга, и почти не участвует в запросах — **JSON custom type** допустим.

---

# Главные подводные камни

## 1. Doctrine не гарантирует вызов вашего конструктора

Это самая частая ошибка.

Многие делают:

```php
final class Money
{
    public function __construct(
        private int $amount,
        private string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Invalid amount');
        }
    }
}
```

и думают:

> Раз конструктор валидирует, значит из БД invalid state не выйдет.

Но Doctrine может hydrated'ить объект не через конструктор.

Поэтому защита должна быть многослойной:

1. Доменный конструктор / factory.
2. Private поля.
3. Нет raw setters.
4. DB-констрейнты.
5. При необходимости проверка на выходе из repository.

Например:

```php
class OrderRepository
{
    public function findValid(int $id): Order
    {
        $order = $this->em->getRepository(Order::class)->find($id);

        if ($order === null) {
            throw new OrderNotFoundException($id);
        }

        $order->ensureConsistency();

        return $order;
    }
}
```

Где:

```php
public function ensureConsistency(): void
{
    $this->total(); // может бросить, если данные невалидны
}
```

## 2. Не делайте VO mutable

Плохо:

```php
final class Money
{
    public int $amount;
    public string $currency;

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
```

Потому что:

```php
$order->total->amount = -100;
```

лучше:

```php
$order->setTotal(Money::of(200, 'RUB'));
```

## 3. Change tracking может быть менее очевиден для объектов

Если Doctrine сравнивает два разных объекта, для которых:

```php
$money1 == $money2
```

— это должно быть `true`, если значения одинаковые.

Для простых VO это нормально:

```php
final class Money
{
    private readonly int $amount;
    private readonly string $currency;
}
```

`==` будет сравнивать свойства.

Но если внутри VO:

```php
private readonly \Closure $formatter;
private readonly object $context;
private readonly array $complexNesting;
```

то сравнение может стать непредсказуемым.

Правило: **для persistence лучше VO с простым значениевым состоянием**.

## 4. Не храните в VO service objects или closures

Плохо:

```php
final class Money
{
    private readonly LoggerInterface $logger;
    private readonly EventDispatcher $events;
}
```

VO — это значение, а не контейнер зависимостей.

Если нужно логирование или события — это поведение application service, а не value object.

## 5. Не делайте VO с identity

Если у объекта есть `id` и он живёт отдельно в таблице — это уже entity, а не value object.

Например:

```php
class Customer
{
    private int $id;
}
```

это entity.

А:

```php
final class CustomerName
{
    private string $first;
    private string $last;
}
```

это value object, если он не имеет отдельного жизненного цикла.

## 6. DB-констрейнты всё равно нужны

Даже если доменный код валидный, в БД может оказаться invalid data из-за:

- ручного SQL;
- другого сервиса;
- миграции;
- старого API;
- конкурентной записи.

Поэтому для `Money` желательно:

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_total_amount_non_negative
    CHECK (total_amount >= 0);

ALTER TABLE orders
    ADD CONSTRAINT chk_total_currency_iso
    CHECK (total_currency ~ '^[A-Z]{3}$');
```

Для enum:

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_order_status
    CHECK (status IN ('new', 'paid', 'shipped', 'cancelled'));
```

## 7. Serialization

Если VO хранится в сущности, он должен быть сериализуемым.

Хорошо:

```php
final class Money
{
    private readonly int $amount;
    private readonly string $currency;
}
```

Плохо:

```php
final class Money
{
    private readonly \Closure $format;
}
```

## 8. Не превращайте custom type в JSON-хранилище без осознания

Если вы начали хранить:

```json
{
  "amount": 100,
  "currency": "RUB",
  "tax": {
    "rate": 0.2,
    "amount": 20
  },
  "metadata": [
    { "foo": "bar" }
  ]
}
```

то вы уже ушли из нормального relational mapping.

Это может быть ок, но тогда нужно понимать:

- не будет нормальных индексов;
- фильтры станут сложнее;
- миграции будут менее прозрачными;
- DBA не сможет свободно читать данные.

---

# Рекомендуемая архитектура

Я бы делал так:

```text
Domain layer:
    Money, Address, CustomerName — final immutable classes.
    Валидация в конструкторе / factory.
    Методы: add(), normalize(), equals().

Doctrine layer:
    Order, Customer — persistent entities.
    Мapped-поля — примитивы или embeddable.
    Нет raw setters.
    Есть domain methods: setTotal(Money $money).

DB layer:
    Нормальные колонки.
    CHECK-констрейнты.
    Индексы там, где нужно.
```

Например:

```php
final class Money
{
    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Amount must be non-negative');
        }

        if (strlen($currency) !== 3) {
            throw new \DomainException('Invalid currency');
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}
```

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $totalCurrency;

    private function __construct(int $amount, string $currency)
    {
        $money = Money::of($amount, $currency);

        $this->totalAmount = $money->amount();
        $this->totalCurrency = $money->currency();
    }

    public static function create(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function total(): Money
    {
        return Money::of($this->totalAmount, $this->totalCurrency);
    }

    public function setTotal(Money $money): void
    {
        $this->totalAmount = $money->amount();
        $this->totalCurrency = $money->currency();
    }
}
```

Это не самый красивый вариант для IDE, но он:

- надёжен;
- прозрачен в БД;
- легко тестируем;
- не зависит от магии Doctrine;
- легко мигрируется;
- позволяет нормально писать DQL.

---

# Итог

Если нужно хранить Value Object в Doctrine и сохранить его неизменяемость:

1. **VO должен быть final и immutable.**
2. **Валидация — в конструкторе / factory.**
3. **Сущность не должна отдавать raw-поля наружу.**
4. **Замена VO — только полным объектом.**
5. **БД должна иметь свои constraints.**
6. **Выбор маппинга зависит от того, нужно ли использовать части VO в DQL.**

Мой практический выбор:

| VO | Мой выбор |
|---|---|
| `Money` | примитивы + façade или embeddable |
| `Status` | backed enum |
| `Address` | embeddable, если нужен поиск; JSON, если вспомогательный |
| `Period` | примитивы `startAt`, `endAt` + façade |
| `Identifier` | string/int + façade |
| Сложный конфиг | JSON custom type, но осознанно |

Главное правило:

> **Doctrine должен знать, как хранить данные. Домен должен знать, что эти данные означают и как с ними работать.**

> ⏱ 134.1s · prompt 35278 tok · output 13791 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

We need answer user's question in Russian. Need think thoroughly. User asks: domain aggregate root and collection of child entities. How organize storage via Doctrine: mapping collections, transaction boundaries, who and when calls flush? Break down with example of my choice.

Need produce final answer. Need maybe mention aggregate root, ownership, cascades, orphan removal, mapping OneToMany, ManyToOne, bidirectional vs unidirectional, lazy loading, remove, transaction boundaries, UnitOfWork, flush policy, repository vs aggregate root vs application service, when flush: after unit of work complete, not inside aggregate methods, not per entity. Need example: Order aggregate with OrderItem children. Discuss root Order, children OrderItem; mapping: Order OneToMany items, OrderItem ManyToOne order; cascade persist, remove; orphanRemoval maybe. Ownership: child cannot be addressed outside aggregate; repository only OrderRepository. Add/remove items via aggregate methods, maintain invariants. Transaction: application service opens transaction, loads aggregate, modifies, commits, flush. In Symfony/Doctrine best: EntityManager wraps unit of work; transactional helper. Could discuss flush inside Doctrine repository? Avoid. In application service use DBAL transaction or EM->transactional (ORM 3). Or command handler. Need discuss when flush: after all invariants checked, before response, not inside each child save. Use first-class collections? Maybe mention. Discuss lazy collections, N+1, DQL partial load? Also concurrency: optimistic locking version on aggregate root. Also mapping child FK to root, no standalone identity? Child id still surrogate. Need maybe mention root as owner, child not exposed. Use ORM attributes. Need address "кто и когда вызывает flush?" — unit of work boundary (application service/command handler/use case) after modifications and validations, not domain aggregate, not repository save? Could be repository save but better explicit transaction in app service. Also for event listeners after flush? Distinguish postFlush, lifecycle callbacks; avoid business logic there. Need include code.

Let's design detailed answer. Structure:
- Key principle: aggregate is transaction boundary, not entity. All persistence operations go through root/repository.
- Example: Order with OrderItem. Invariants: order total >= sum items; status transitions; item price/currency consistency; no zero quantity.
- Mapping:
  - Order: id, status, totalAmount, totalCurrency, items OneToMany, cascade persist/remove, orphanRemoval true maybe, inversedBy.
  - OrderItem: id, order ManyToOne join table FK, quantity, unitPrice, lineTotal, currency.
- Show PHP attributes.
- Discuss bidirectional vs unidirectional. Prefer unidirectional from root if only root owns collection? But Doctrine bidirectional helps. If unidirectional OneToMany, add child by setting FK manually. I can present both and recommend: for aggregate, root owns children, bidirectional for convenience; but repository never exposes child repository.
- Cascade: persist root cascades to items. Remove root removes items. orphanRemoval for collections when item removed from collection. Caution: orphanRemoval only works for bidirectional OneToMany? It works with OneToMany and owning? Need be precise: orphanRemoval true on OneToMany. Removing item from collection and flushing deletes it. But beware with lazy proxies? It's okay. However with cascade remove and orphan removal can cause unwanted deletes if collection not initialized? If collection lazy, accessing remove requires initialization. Need caution. Alternative explicit repository delete? Better root methods.
- Domain code: Order::addOrderItem, removeOrderItem, confirm, cancel. Recalculate total inside root.
- Application service: CreateOrderService, UpdateOrderService. Use EntityManagerInterface::transactional or Connection::transaction. Load, modify, commit. flush happens after commit callback. Code:

$em->transactional(function (EntityManagerInterface $em) {
   $order = $em->getRepository(Order::class)->find($id);
   $order->addItem(...);
   return null;
});

In ORM 3, transactional ensures flush and commit/rollback.

For create: new Order, addItem, $em->persist($order); $em->flush(); inside transaction? Could use transactional with persist and flush after? transactional flushes. Need be careful: in create, cascade persist. In update, load and modify.

- Who calls flush: application service / use case (unit of work). Repository can have save/persist but flush optional. I prefer repository methods return void and app service flushes. Or repository save can persist but not flush; app flush. Avoid flush inside aggregate/domain methods. Avoid flush per item. Avoid flush in event listeners unless infrastructure (IDs).
- Transaction boundaries: one transaction per business action; not per entity; not per request if long. If external calls? Ideally no external calls inside DB transaction; if necessary, keep short or use outbox. Need mention side effects after commit.
- Concurrency: version column on root, optimistic locking. Example: #[ORM\Version]. Invariants: after modification before flush. If concurrent updates, VersionException -> 409.
- Lazy loading: collection lazy; avoid foreach in hot paths without join. Use DQL partial object if only root. For child queries, join. Avoid N+1 by fetching needed. But aggregate boundary means loading root with items if modifying. For read, projection.
- Unit of work and change tracking: modifications tracked by Doctrine; no explicit dirty marking needed for property changes (unless lazy/proxies weird). Add/remove collection changes tracked.
- Mapping details: child FK order_id; root total denormalized for convenience; recompute. If children have own table, no need for separate root id? root id still. Could use composite key? Usually surrogate id.
- Edge cases:
  - Removing root: $em->remove($order) cascades remove; or $order->removeAllItems()? With orphanRemoval/cascade.
  - Replacing all items: root method replaceItems (clear, add).
  - Adding item when status not New: in root method throws.
  - Null children: if new item not assigned? Root's addItem assigns itself? Need code: Order::addItem(OrderItem $item) { $item->setOrder($this); $this->items->add($item); recalc; } or factory with parameters to avoid unassigned. Better: Order::addItem(int $qty, Money $price) creates item and adds. But if need child entity with own domain logic? Let's create OrderItem entity. Root method can create item to enforce ownership: addItem(ProductId, qty, unitPrice). But product id maybe. Simpler.
- Discuss "кто и когда вызывает flush" detailed:
  1. Aggregate root: never.
  2. Repository: can persist, but flush only if repository is unit of work? In DDD repository is not necessarily; better not flush inside repository unless repository method represents a use case and is transactional. In practice, repository::save might persist and flush for simplicity, but for aggregate with side effects use app service transaction.
  3. Application service/command handler: yes, after validation and modification, within transaction. If Symfony, use $em->transactional.
  4. Controller: no; should not call flush directly.
  5. Event listeners: only for technical stuff (assign IDs, update timestamps) and after flush? Avoid business logic.
- Provide code for CreateOrderUseCase:

public function execute(CreateOrderDto $dto): OrderId
{
   return $this->em->transactional(function (EntityManagerInterface $em) {
       $order = Order::create($dto->customerId, $dto->currency);
       foreach ($dto->items as $item) {
           $order->addItem($item->productId, $item->quantity, Money::of(...));
       }
       $em->persist($order);
       $em->flush(); // or transactional flushes after? If using transactional, it flushes at end if not already.
       return $order->id();
   });
}

But transactional callback flushes automatically? According to docs: transactional runs callback in transaction, flushes before commit? I think it calls flush before commit if pending changes? Need precise: EntityManager::transactional(callback) executes callback in transaction, commits if success, rolls back on exception, flushes before commit? In Doctrine ORM 2/3: public function transactional(callable $func) : mixed { ... $this->beginTransaction(); try { $result = $func($this); $this->commit(); } ... }. Does it flush? I recall docs: "The callback receives the EntityManager. The transaction is committed and flushed after the callback." Actually transactional in ORM 2: start transaction, run callback, commit. It does not automatically flush? Let's check memory: In Doctrine 2.13? EntityManager::transactional(callable $func) : mixed { $this->wrapInTransaction...? It might call $this->flush() before commit. I think yes: "It will flush pending changes before committing"? Let's be safe: explicitly call $em->flush() after modifications and before return. That ensures. Then transactional commits. If transactional also flushes, no harm.

- Need mention if using DBAL transaction with multiple EMs? Keep one EM per unit of work.

- Potential pitfall: cascade persist on OneToMany: new Order persists items. But if item already managed? Need ensure child not orphan? If add existing OrderItem to another order, must remove from old? Could be complicated. Better children not moved; replace.

- Need mention "inversedBy" and owning side: ManyToOne from child to root is owning. OneToMany is inverse. Mapping child FK. If unidirectional OneToMany, inverse side not in child; Doctrine requires inverse side class exists; adding new child: $item->setOrder? not possible if no method. So bidirectional recommended.

- Should we discuss aggregate identity: children ids are local to aggregate? They can be DB ids, but no external references by id except root id. Repository: only OrderRepository. If need child lookup, load root.

- Need include example with code, tradeoffs, anti-patterns.

Let's craft final answer in Russian, detailed but not too insane. Use headings.

Potential code:

Money as VO maybe not fully shown. But include minimal.

Mapping:

#[ORM\Entity]
final class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $customerId;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::INTEGER)]
    private int $total = 0;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $items;

    private function __construct(string $customerId, string $currency)
    {
        $this->items = new ArrayCollection();
        $this->customerId = $customerId;
        $this->currency = strtoupper($currency);
    }

    public static function create(string $customerId, string $currency): self
    {
        $order = new self($customerId, $currency);
        $order->version = 0;
        return $order;
    }

    public function addItem(int $quantity, int $unitPrice): void
    {
        if ($quantity <= 0) throw ...
        if ($unitPrice < 0) throw ...
        $item = new OrderItem($quantity, $unitPrice, $this->currency);
        $item->setOrder($this);
        $this->items->add($item);
        $this->recalculateTotal();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->items->get($itemId) ? null : ...; // items collection not keyed by id initially. Need search.
        // Better find by item id.
        foreach ($this->items as $i) { if ($i->id === $itemId) { $this->items->removeElement($i); break; } }
        $this->recalculateTotal();
    }

    public function id(): ?int { return $this->id; }
    public function total(): Money { return Money::of($this->total, $this->currency); }
    public function items(): iterable { return $this->items; }
    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->lineTotal();
        }
        if ($sum < 0) throw ...
        $this->total = $sum;
    }
}

#[ORM\Entity]
final class OrderItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id=null;
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    #[ORM\Column(type: Types::INTEGER)] private int $quantity;
    #[ORM\Column(type: Types::INTEGER)] private int $unitPrice;
    #[ORM\Column(type: Types::STRING, length:3)] private string $currency;

    public function __construct(int $quantity, int $unitPrice, string $currency)
    {
       if ($quantity <=0) throw...
       if ($unitPrice <0) throw...
       $this->quantity=$quantity; $this->unitPrice=$unitPrice; $this->currency=strtoupper($currency);
    }
    public function id(): ?int { return $this->id; }
    public function lineTotal(): int { return $this->quantity * $this->unitPrice; }
    public function setOrder(Order $order): void { $this->order = $order; }
    public function order(): ?Order { return $this->order; }
}

Need mention version: if new object version default? In Doctrine, #[ORM\Version] property initialized? For new entities, version starts null? It might be set to 0 on insert if using attribute? Need not dwell. We can initialize in constructor $this->version = 0; but for new entity Doctrine will increment. For loaded, version set.

Repository:

final class OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}
    public function save(Order $order): void { $this->em->persist($order); }
    public function findById(int $id): ?Order { return $this->em->getRepository(Order::class)->find($id); }
    public function remove(Order $order): void { $this->em->remove($order); }
}

Application service:

final class CreateOrderUseCase
{
    public function __construct(private OrderRepository $repo, private EntityManagerInterface $em) {}

    public function execute(string $customerId, array $lines): int
    {
        return $this->em->transactional(function (EntityManagerInterface $em) {
            $order = Order::create($customerId, 'RUB');
            foreach ($lines as $line) { $order->addItem($line['qty'], $line['price']); }
            $this->repo->save($order);
            $em->flush();
            return $order->id();
        });
    }
}

But if transactional passes same em, okay. Or pass em to repository? Could be inconsistent. Simpler repository uses same em. Need maybe avoid passing em to transactional if repository uses different? It's same.

Update:

public function addItem(int $orderId, int $qty, int $price): void
{
   $this->em->transactional(function (EntityManagerInterface $em) {
       $order = $this->repo->findById($orderId);
       if (!$order) throw ...
       $order->addItem($qty, $price);
       $em->flush();
   });
}

Remove:

public function cancel(int $orderId): void
{
   $this->em->transactional(function (EntityManagerInterface $em) {
      $order = $this->repo->findById($orderId);
      $order->cancel();
      $em->flush();
   });
}

If cancel removes items? Maybe not. For delete order: $em->remove($order); flush; cascade.

Need discuss flush after version check? If version conflict, flush throws.

Need mention transactional with exception: rollback and no partial writes.

Potential issues with orphanRemoval: If you remove from collection but don't flush, no delete. If collection lazy and you call count/foreach, triggers load. Need careful. Also orphanRemoval does not delete if child assigned to another aggregate? It deletes; for moving, remove from old first.

Potential issue with cascade persist: if new Order has existing OrderItem, Doctrine persists it; if item already managed, cascade persist no-op. If you detach? okay.

Potential issue: root total denormalized; if child price changes? child immutable? In example item fields set once. If update item, root recalc.

Need mention if child needs to be fetched individually, load root first. No OrderItemRepository.

Need discuss "transaction boundaries" with external API: don't hold DB transaction while calling HTTP. If need payment before order update, commit order state after external response, or use outbox. If external call after commit, use events.

Need mention flush and IDs: if need generated IDs before commit (e.g., redirect), flush can be called before commit; transaction still open. But business side effects should wait after commit.

Need include final summary.

Let's make final answer in Russian, clear. Need ensure no unsupported statements. Let's be accurate about transactional. I'll say explicitly call $em->flush() inside callback; $em->transactional() manages BEGIN/COMMIT/ROLLBACK and also may flush before commit, but explicit flush makes intent clear. Good.

Need maybe mention using DBAL transaction instead of EntityManager::transactional if using connection only. But for ORM, EM::transactional.

Need mention unit of work = one business use case, not request. In Symfony, action/use case. For long tasks, split.

Potential code for Money? Keep minimal:

final readonly class Money { public function __construct(public int $amount, public string $currency) {} public static function of(int $amount,string $c):self... }

But in Order, use int total for simplicity. Could use Money and mapping. Since previous question about VO, maybe keep Money as VO. But example should be about aggregate. To avoid complexity, use int cents + currency string. Or use Money custom? I can mention if Money is VO, store primitives or embeddable. In aggregate, use total int to keep example focused. Maybe use Money in methods? To align previous, use Money:

class Order { private Money $total; public function total(): Money; private recalc() { $this->total = Money::of($sum, $this->currency); } }

Mapping total as two columns? To avoid custom type, store totalAmount and totalCurrency. Let's do:

#[ORM\Column(type: Types::INTEGER)] private int $totalAmount=0;
#[ORM\Column(type: Types::STRING, length:3)] private string $currency;
public function total(): Money { return Money::of($this->totalAmount, $this->currency); }
private function recalculateTotal(): void { $sum=0; foreach($this->items as $item) $sum += $item->lineTotal(); if ($sum <0) throw...; $this->totalAmount=$sum; }

Good.

Need include note: aggregate invariants protected by root methods, not by setters. Children cannot be persisted independently.

Let's craft final with sections:
1. Main principle
2. Example: Order + OrderItem
3. Mapping
4. Domain aggregate methods
5. Persistence and transaction
6. Who flushes
7. Pitfalls / anti-patterns
8. Summary table

Need maintain interview tone? The user likely continuing interview. Answer should be expert. Include code.

Let's draft final. Ensure no English maybe. Use Russian.

Need maybe mention `orphanRemoval` and `cascade: ['remove']` with lazy proxy? If `orphanRemoval: true`, Doctrine removes entities that were in collection and are removed. It can be dangerous if you clear collection without intention. In aggregate root, it's okay. Alternative: no orphanRemoval and explicit repository remove? But root method can call $em? Domain shouldn't know EM. Better orphanRemoval.

Potential mapping attributes in PHP 8: use `targetEntity: OrderItem::class`, `mappedBy: 'items'`, `cascade: ['persist', 'remove']`, `orphanRemoval: true`, `fetch: 'LAZY'` (default). For child: `inversedBy: 'items'`, `JoinColumn(nullable: false, onDelete: 'CASCADE')`.

Need mention `onDelete: CASCADE` at DB level? Doctrine can remove children; DB cascade optional but if root deleted by SQL? Better DB constraint. For application-level deletes, ORM handles. Could mention as safety.

Potential issue: if using `orphanRemoval`, when deleting root, cascade remove will remove children; DB cascade not necessary but safe.

Need mention `Collection` type from Doctrine\Common\Collections\Collection.

Need maybe show removeItem by item id; use `foreach` because collection not keyed by id. In production, could use `get($id)` if id known and collection keys by id? Doctrine Collection preserves insertion order, not keyed by id. Better loop. Or keep `findItem(int $id)`.

Code for remove:

public function removeItem(int $itemId): void
{
    $found = false;
    foreach ($this->items as $item) {
        if ($item->id() === $itemId) {
            $this->items->removeElement($item);
            $found = true;
            break;
        }
    }
    if (!$found) throw new \DomainException('Item not found');
    $this->recalculateTotal();
}

Potential issue: if item is new (id null), cannot remove by id. Use object identity? But domain should have provisional id? Keep simple.

Need mention if items can be updated: item fields immutable? If need update, root method `updateItem(int $id, int $qty, int $price)`; child setter maybe not public. Show not full.

Need discuss "transaction boundary and flush" maybe:

```
public function addItem(int $orderId, int $quantity, int $unitPrice): void
{
    $this->em->transactional(function (EntityManagerInterface $em): void {
        $order = $this->repo->findById($orderId);
        if ($order === null) throw new NotFoundException();
        $order->addItem($quantity, $unitPrice);
        $em->flush();
    });
}
```

If `addItem` throws domain exception, rollback. If flush throws version exception, rollback.

Need mention if using Symfony and Doctrine Bundle, `EntityManagerInterface` autowired. For command handler, same.

Need discuss `flush` inside repository? Option:

```
interface OrderRepository
{
    public function save(Order $order): void;
}
```

Implementation:

```
public function save(Order $order): void
{
    $this->em->persist($order);
    // flush НЕ обязательно здесь
}
```

But if app service calls `$em->flush()` after, okay. If repository saves and flushes, then transaction boundary less explicit. I can say: repository may `persist` but not `flush`; application service does `flush` after full work. If you make repository `save()` that includes flush, treat it as unit of work only if it encloses entire use case.

Need mention "who calls flush" final:
- never: domain object, controller, template, event listener for business logic.
- yes: use case / application service / command handler at unit-of-work boundary.
- optionally: repository if it is the UoW, but not recommended for aggregate.

Need mention `commit` and `flush` order: flush executes SQL; commit closes transaction. If external effects need after commit, use transactional callback return and then side effects outside, or events after flush? In Doctrine, postFlush listeners fire after flush inside transaction before commit? Actually postFlush after flush but before commit? It is after flush, before commit. If side effect needs after commit, use afterCommit events (Symfony Messenger / Doctrine `onFlush`? There is `PostCommitEvent`? Doctrine has `onFlush`, `postPersist`, `preFlush`, `postFlush`; commit event not standard? In ORM, `CommitEventArgs`? There is `postCommit`? I think Doctrine ORM has `onFlush`, `preFlush`, `postFlush`, `postPersist`, etc. There is `CommitEventArgs`? Maybe not stable. Better: run side effects after `$em->transactional()` returns successfully, not in listeners. Or use outbox/Messenger. We can say: side effects after commit should be triggered outside transaction, e.g. after use case returns, using outbox/Messenger.

Need maybe mention event subscribers can assign IDs but not business.

Need mention if using `flush` before commit for generated IDs and then external call: acceptable if IDs needed, but keep transaction short. If external call fails after commit, need compensation. Better no external calls inside transaction.

Need include table:

| Где flush? | Почему |
| Domain root | Нет, не знает про ORM |
| Repository | Может persist, flush — только если это UoW |
| Use case | Да, одна транзакция |
| Controller | Нет |
| Listener | Только технические, не бизнес |

Need mention aggregate boundary and transaction: one aggregate can be modified in one transaction; if use case modifies two aggregates, either one transaction with two aggregates (if same consistency) or separate transactions with eventual consistency. For example, Order and Customer? If customer balance must be consistent, same transaction; if inventory external, not.

Need mention if aggregate large: lazy loading items; load only necessary; use DQL to fetch root + items if needed. For aggregate modification, need load items. For read, projection.

Potential code for query:

```
$em->createQueryBuilder()
  ->select('o', 'i')
  ->from(Order::class, 'o')
  ->leftJoin('o.items', 'i')
  ->where('o.id = :id')
  ->setParameter('id', $id)
  ->getQuery()
  ->getOneOrNullResult();
```

But repository find loads lazy items, no issue until accessed.

Need mention change tracking: If add item to collection, Doctrine detects changes on flush. If modify child property, Doctrine detects if child is managed. If child is new and attached, cascade persist. Good.

Need maybe discuss `#[ORM\Version]` and `OptimisticLockException` code:

```
try {
   $this->em->transactional(...);
} catch (OptimisticLockException) {
   throw new ConflictException();
}
```

Actually exception class in ORM 2: `Doctrine\ORM\OptimisticLockException`. In ORM 3 maybe `Doctrine\ORM\Exception\OptimisticLockException`? We'll just mention generally.

Need be precise: version field must be initialized? In new entity, version may be null. In constructor: $this->version = 0; Doctrine sets? In docs, version value is incremented on persist? It starts from null and on persist becomes 0? Let's not rely; just show attribute and maybe initialize.

Potential code for Order create:

private function __construct(string $customerId, string $currency)
{
   $this->items = new ArrayCollection();
   $this->customerId = $customerId;
   $this->currency = strtoupper($currency);
   $this->version = 0;
}

But version is mapped; for new entity, setting 0 ok. When Doctrine inserts, version maybe set to 0 or 1? It's okay.

Need mention if using `readonly` class? Mapped entities cannot be readonly? No, not needed.

Potential anti-patterns:
- `new OrderItem(); $item->setOrder($order); $em->persist($item);` from outside root.
- Exposing `setItems(Collection)`.
- `OrderItemRepository` with `find()` and modifying item without root.
- Flush inside loop:

```
foreach ($lines as $line) { $order->addItem(...); $em->flush(); }
```

Bad: multiple transactions/SQL, partial state.

- Catching broad exceptions and continuing after partial flush.

Need mention if child collection is large, consider table partition? Not necessary.

Let's compose final answer. Ensure final answer directly answers. Use code. Keep concise but detailed. Need no citations.

Let's write final.

</details>

**Кандидат:**

## Главный принцип

Агрегат — это **граница консистентности**.  
Если у вас есть корневая сущность `Order` и дочерние `OrderItem`, то для внешнего мира существует только `Order`. `OrderItem` нельзя создавать, обновлять или удалять обходным путём, если это ломает инварианты заказа.

Поэтому:

- корень агрегата владеет дочерними сущностями;
- дочерние сущности не имеют отдельного репозитория;
- изменение дочерних сущностей идёт через методы корневого объекта;
- одна бизнес-операция — одна транзакция;
- `flush()` вызывается на границе use case / application service, а не внутри доменного объекта.

Разберём на примере.

---

# Пример: `Order` и `OrderItem`

Доменные инварианты:

1. У заказа есть валюта.
2. Каждая позиция имеет количество и цену.
3. Сумма заказа равна сумме позиций.
4. Позицию можно добавить/убрать только в определённых статусах.
5. Нельзя создать `OrderItem` вне контекста `Order`.

---

# 1. Доменная модель

```php
final class Money
{
    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \DomainException('Amount must be non-negative');
        }

        if (strlen($currency) !== 3) {
            throw new \DomainException('Invalid currency');
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Cannot add different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

Корень агрегата:

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 64)]
    private string $customerId;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $totalAmount = 0;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 32)]
    private string $status = 'new';

    #[ORM\Version]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $version;

    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $items;

    private function __construct(string $customerId, string $currency)
    {
        $this->customerId = $customerId;
        $this->currency = strtoupper($currency);
        $this->items = new ArrayCollection();
        $this->version = 0;
    }

    public static function create(string $customerId, string $currency): self
    {
        return new self($customerId, $currency);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function total(): Money
    {
        return Money::of($this->totalAmount, $this->currency);
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function items(): Collection
    {
        return $this->items;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function addItem(int $quantity, int $unitPrice): void
    {
        if ($this->status !== 'new') {
            throw new \DomainException('Can add items only to a new order');
        }

        if ($quantity <= 0) {
            throw new \DomainException('Quantity must be positive');
        }

        if ($unitPrice < 0) {
            throw new \DomainException('Unit price must be non-negative');
        }

        $item = new OrderItem($quantity, $unitPrice, $this->currency);
        $item->setOrder($this);

        $this->items->add($item);

        $this->recalculateTotal();
    }

    public function removeItem(int $itemId): void
    {
        if ($this->status !== 'new') {
            throw new \DomainException('Can remove items only from a new order');
        }

        $found = false;

        foreach ($this->items as $item) {
            if ($item->id() === $itemId) {
                $this->items->removeElement($item);
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \DomainException('Item not found');
        }

        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->status !== 'new') {
            throw new \DomainException('Only a new order can be confirmed');
        }

        if ($this->items->count() === 0) {
            throw new \DomainException('Order must contain at least one item');
        }

        $this->status = 'confirmed';
    }

    public function cancel(): void
    {
        if (in_array($this->status, ['cancelled', 'shipped'], true)) {
            throw new \DomainException('Order cannot be cancelled in current status');
        }

        $this->status = 'cancelled';
    }

    private function recalculateTotal(): void
    {
        $sum = 0;

        foreach ($this->items as $item) {
            $sum += $item->lineTotal();
        }

        if ($sum < 0) {
            throw new \DomainException('Total amount must be non-negative');
        }

        $this->totalAmount = $sum;
    }
}
```

Дочерняя сущность:

```php
#[ORM\Entity]
final class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(
        targetEntity: Order::class,
        inversedBy: 'items',
    )]
    #[ORM\JoinColumn(
        name: 'order_id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ?Order $order = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $quantity;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    private int $unitPrice;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 3)]
    private string $currency;

    public function __construct(
        int $quantity,
        int $unitPrice,
        string $currency,
    ) {
        if ($quantity <= 0) {
            throw new \DomainException('Quantity must be positive');
        }

        if ($unitPrice < 0) {
            throw new \DomainException('Unit price must be non-negative');
        }

        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->currency = strtoupper($currency);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function lineTotal(): int
    {
        return $this->quantity * $this->unitPrice;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function order(): ?Order
    {
        return $this->order;
    }
}
```

---

# 2. Что здесь важно

## Владельцем коллекции является корень

```php
#[ORM\OneToMany(
    mappedBy: 'order',
    targetEntity: OrderItem::class,
    cascade: ['persist', 'remove'],
    orphanRemoval: true,
)]
private Collection $items;
```

Это значит:

- при сохранении `Order` сохранятся и новые `OrderItem`;
- при удалении `Order` удалятся и его позиции;
- если позиция была в коллекции, а потом была удалена из коллекции — она будет удалена из БД.

Особенно важен `orphanRemoval: true`.

Он хорошо подходит именно для агрегата:

```php
$order->removeItem($itemId);
```

После `flush()` соответствующая строка исчезает из таблицы.

---

## Owning side — `ManyToOne`

Связь:

```php
OrderItem.order_id -> Order.id
```

владеет дочерняя сущность.

Для `OneToMany` нужно указать `mappedBy: 'order'`.

То есть:

```php
Order \
  --> items

OrderItem --> order
```

Это нормальный bidirectional mapping.

Но с точки зрения домена важно не направление маппинга, а то, что наружу домен отдаёт только `Order`.

---

# 3. Репозиторий

Репозиторий работает только с корнем агрегата.

Не делаем:

```php
interface OrderItemRepository
{
    public function find(int $id): OrderItem;
}
```

Потому что `OrderItem` — внутренняя часть агрегата.

Делаем:

```php
interface OrderRepository
{
    public function save(Order $order): void;

    public function remove(Order $order): void;

    public function findById(int $id): ?Order;
}
```

Реализация:

```php
final class OrderRepository implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function save(Order $order): void
    {
        $this->em->persist($order);
    }

    public function remove(Order $order): void
    {
        $this->em->remove($order);
    }

    public function findById(int $id): ?Order
    {
        return $this->em->getRepository(Order::class)->find($id);
    }
}
```

Важный момент:

> `persist()` — это не `INSERT`.  
> `flush()` — это момент, когда Doctrine генерирует SQL.

Поэтому репозиторий может вызывать `persist()`, но лучше не вызывать `flush()` внутри каждого метода, если за ним идёт продолжение бизнес-логики.

---

# 4. Application service / use case

Это граница транзакции.

Пример создания заказа:

```php
final class CreateOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(string $customerId, array $lines): int
    {
        return $this->em->transactional(function (EntityManagerInterface $em): int {
            $order = Order::create($customerId, 'RUB');

            foreach ($lines as $line) {
                $order->addItem(
                    quantity: $line['quantity'],
                    unitPrice: $line['unit_price'],
                );
            }

            $this->orders->save($order);

            $em->flush();

            return $order->id();
        });
    }
}
```

Здесь:

1. создаём агрегат;
2. добавляем позиции через корень;
3. сохраняем корень;
4. вызываем `flush()` один раз;
5. транзакция коммитится.

Если что-то бросило исключение:

- `DomainException`;
- SQL-ошибка;
- нарушение uniqueness;
- optimistic lock;

транзакция откатывается.

---

# 5. Обновление агрегата

Пример: добавить позицию в существующий заказ.

```php
final class AddItemToOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(int $orderId, int $quantity, int $unitPrice): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            $order = $this->orders->findById($orderId);

            if ($order === null) {
                throw new \DomainException('Order not found');
            }

            $order->addItem($quantity, $unitPrice);

            $em->flush();
        });
    }
}
```

Здесь нет отдельного сохранения `OrderItem`.

Мы изменяем агрегат:

```php
$order->addItem(...);
```

А Doctrine сам поймёт:

- добавился новый элемент в коллекцию;
- изменился `totalAmount`;
- изменился `version`;
- нужно сделать `INSERT` для нового `OrderItem`;
- нужно сделать `UPDATE` для `Order`.

---

# 6. Удаление позиции

```php
final class RemoveItemFromOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(int $orderId, int $itemId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            $order = $this->orders->findById($orderId);

            if ($order === null) {
                throw new \DomainException('Order not found');
            }

            $order->removeItem($itemId);

            $em->flush();
        });
    }
}
```

Что произойдёт:

1. `Order::removeItem()` удалит `OrderItem` из коллекции.
2. `orphanRemoval: true` пометит его для удаления.
3. `recalculateTotal()` пересчитает сумму.
4. `flush()` выполнит:
   - `DELETE FROM order_items WHERE id = ...`;
   - `UPDATE orders SET total_amount = ..., version = ... WHERE id = ...`.

---

# 7. Удаление всего агрегата

```php
final class DeleteOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            $order = $this->orders->findById($orderId);

            if ($order === null) {
                throw new \DomainException('Order not found');
            }

            $this->orders->remove($order);

            $em->flush();
        });
    }
}
```

Благодаря:

```php
cascade: ['persist', 'remove']
```

Doctrine удалит:

- сам `Order`;
- все связанные `OrderItem`.

На уровне БД желательно также иметь:

```sql
ALTER TABLE order_items
    ADD CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id)
    REFERENCES orders(id)
    ON DELETE CASCADE;
```

Это страховка на случай, если удаление произойдёт не через ORM.

---

# 8. Кто вызывает `flush()`?

## Правильный ответ

`flush()` вызывает тот, кто владеет границей use case.

Обычно это:

- application service;
- command handler;
- use case;
- сервис, который представляет одну бизнес-операцию.

Не вызывает:

- доменный объект;
- сущность;
- Value Object;
- контроллер;
- шаблон;
- каждый отдельный `save()` внутри цикла.

---

## Почему не домен

Домен не должен знать о Doctrine.

Плохо:

```php
class Order
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function addItem(...): void
    {
        // ...
        $this->em->flush();
    }
}
```

Проблемы:

1. Домен зависит от ORM.
2. Сложнее тестировать.
3. `flush()` может произойти в середине бизнес-операции.
4. Одна транзакция превращается в несколько.

---

## Почему не контроллер

Плохо:

```php
class OrderController
{
    public function addItem(Request $request): Response
    {
        // ...
        $order->addItem(...);
        $this->em->flush();

        return new Response();
    }
}
```

Контроллер — не владелец бизнес-правил.

Лучше:

```php
return $this->addItemUseCase->execute(...);
```

---

## Почему не каждый `save()`

Плохо:

```php
foreach ($lines as $line) {
    $order->addItem($line['qty'], $line['price']);
    $em->flush();
}
```

Это означает:

- несколько `flush()`;
- несколько SQL-транзакций;
- возможное частичное состояние;
- лишние нагрузки на БД;
- сложный rollback.

Правильно:

```php
foreach ($lines as $line) {
    $order->addItem($line['qty'], $line['price']);
}

$em->flush();
```

---

# 9. Граница транзакции

Транзакция должна покрывать всю бизнес-операцию, которую нужно сделать атомарно.

Пример:

```php
$this->em->transactional(function (EntityManagerInterface $em): void {
    $order = $this->orders->findById($orderId);
    $order->confirm();
    $em->flush();
});
```

Внутри одной транзакции:

- читается агрегат;
- изменяется состояние;
- проверяются инварианты;
- выполняется SQL;
- коммитится или откатывается.

---

## Важно: одна транзакция на business use case

Не на запрос, не на контроллер, не на сущность.

Если бизнес-операция такая:

> подтвердить заказ, зарезервировать инвентарь, создать событие отправки письма,

то нужно решить, что входит в один атомарный блок.

Если инвентарь в той же БД и критично:

```php
$this->em->transactional(function (EntityManagerInterface $em): void {
    $order = $this->orders->findById($orderId);
    $order->confirm();

    $this->inventory->reserve($order);

    $em->flush();
});
```

Если инвентарь — внешний сервис:

лучше:

1. коммитить состояние заказа;
2. после коммита отправить message / событие;
3. отдельный воркер вызвать внешний сервис.

Не держите долгую транзакцию БД во время HTTP-запроса к другому сервису.

---

# 10. Optimistic locking

Для агрегата полезно добавить `version`.

Уже есть:

```php
#[ORM\Version]
#[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
private int $version;
```

Сценарий:

1. Пользователь А открывает заказ.
2. Пользователь Б открывает тот же заказ.
3. А меняет заказ.
4. Б пытается изменить заказ.
5. Б получает `OptimisticLockException`.

Обработка:

```php
try {
    $this->em->transactional(function (EntityManagerInterface $em): void {
        $order = $this->orders->findById($orderId);
        $order->addItem(1, 100);
        $em->flush();
    });
} catch (OptimisticLockException) {
    throw new ConflictException('Order was changed by another user');
}
```

В API это обычно превращается в:

```text
409 Conflict
```

---

# 11. Lazy loading и производительность

Коллекция:

```php
#[ORM\OneToMany(...)]
private Collection $items;
```

по умолчанию lazy.

Это значит:

```php
$order = $em->getRepository(Order::class)->find($id);
```

пока не выполнит запрос к `order_items`, пока вы не обратитесь к коллекции.

Обращение:

```php
$order->items()
```

загрузит элементы.

---

## Когда это нормально

При изменении агрегата:

```php
$order = $this->orders->findById($orderId);
$order->addItem(...);
```

Здесь нужно знать текущие позиции, чтобы пересчитать сумму.

---

## Когда это плохо

Если в цикле:

```php
foreach ($orders as $order) {
    $order->items()->count();
}
```

вы можете получить N+1.

Решения:

1. Fetch join:

```php
$qb = $em->createQueryBuilder();

$qb
    ->select('o', 'i')
    ->from(Order::class, 'o')
    ->leftJoin('o.items', 'i')
    ->where('o.customerId = :customerId')
    ->setParameter('customerId', $customerId);
```

2. Projection, если нужен только список заказов без позиций.

3. Pagination + явный выбор данных.

---

# 12. Частые антипаттерны

## Антипаттерн 1: наружу отдаётся коллекция для произвольной мутации

Плохо:

```php
public function setItems(Collection $items): void
{
    $this->items = $items;
}
```

Потому что можно нарушить инварианты.

Лучше:

```php
public function addItem(...): void
public function removeItem(int $itemId): void
```

---

## Антипаттерн 2: `OrderItemRepository`

Плохо:

```php
$item = $em->getRepository(OrderItem::class)->find($id);
$item->setQuantity(10);
$em->flush();
```

Проблема:

- не пересчитана сумма заказа;
- не проверены инварианты;
- нарушена граница агрегата.

Правильно:

```php
$order = $em->getRepository(Order::class)->find($orderId);
$order->updateItem($itemId, 10, $unitPrice);
```

---

## Антипаттерн 3: `flush()` в домене

Плохо:

```php
class Order
{
    public function addItem(...): void
    {
        // ...
        $this->em->flush();
    }
}
```

Домен не должен знать о Unit of Work.

---

## Антипаттерн 4: `flush()` в цикле

Плохо:

```php
foreach ($items as $item) {
    $order->addItem(...);
    $em->flush();
}
```

Это ломает атомарность и эффективность.

---

## Антипаттерн 5: бизнес-логика в listener'ах

Можно использовать Doctrine events для технических задач:

- присвоение ID;
- заполнение `createdAt`;
- логирование.

Но лучше не строить бизнес-логику в:

```php
postPersist
preUpdate
postFlush
```

Потому что:

- сложно тестировать;
- сложно читать;
- порядок вызовов неочевиден;
- скрытые зависимости.

Если нужно отправить письмо после сохранения заказа — лучше:

1. use case завершает транзакцию;
2. после успешного commit'а публикует событие;
3. отдельный потребитель отправляет письмо.

---

# 13. Практическая схема

```text
Controller
   ↓
UseCase / CommandHandler
   ↓
Domain Aggregate (Order)
   ↓
Doctrine EntityManager / Unit of Work
   ↓
Database
```

Код:

```php
final class ConfirmOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {
    }

    public function execute(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            $order = $this->orders->findById($orderId);

            if ($order === null) {
                throw new \DomainException('Order not found');
            }

            $order->confirm();

            $em->flush();
        });
    }
}
```

Здесь:

- контроллер не знает про `flush()`;
- домен не знает про `EntityManager`;
- `flush()` вызывается один раз;
- транзакция покрывает всю операцию;
- ошибка — rollback.

---

# 14. Итоговая таблица

| Что | Как делать |
|---|---|
| Владелец коллекции | Корень агрегата |
| Реальный FK | На дочерней сущности: `order_id` |
| Маппинг | `OneToMany` + `ManyToOne` |
| Cascade | `persist`, `remove` |
| Orphan removal | Да, если дочерние сущности живут только внутри агрегата |
| Репозиторий | Только для корневого объекта |
| Мутации детей | Только через методы корня |
| Транзакция | Одна на business use case |
| `flush()` | В use case / application service |
| `flush()` в домене | Нельзя |
| `flush()` в цикле | Плохо |
| External API | После commit'а, через events / outbox |
| Concurrent updates | `#[ORM\Version]` |
| N+1 | fetch join или projection |

---

# Коротко

Для агрегата `Order + OrderItem`:

1. Корень `Order` владеет коллекцией `items`.
2. Дочерний `OrderItem` ссылается на `Order` через `ManyToOne`.
3. Изменение коллекции происходит только через методы `Order`.
4. `OrderItemRepository` не нужен.
5. `cascade: ['persist', 'remove']` и `orphanRemoval: true` дают корректное сохранение/удаление.
6. Одна бизнес-операция — одна транзакция.
7. `flush()` вызывает use case / application service, а не домен и не контроллер.
8. Сторонние эффекты — после успешного commit'а.

> ⏱ 118.0s · prompt 42709 tok · output 12592 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про диагностику и решение N+1 в Doctrine. Это классическая тема PHP-бэкенда. Нужно структурировать ответ: сначала диагностика (как найти), потом решения (fetch join, DQL, batching, denormalization, caching), и для каждого — trade-offs.

Я уже в контексте собеседования, поэтому отвечаю как кандидат с опытом. Использую конкретные примеры кода. Не буду дублировать то, что уже говорил, но могу ссылаться на предыдущие ответы (Order/OrderItem).

Структура:
1. Как диагностировать — конкретные инструменты и подходы
2. Решения — от простых к сложным, с trade-offs
3. Практическая матрица выбора

</details>

**Кандидат:**

## Диагностика

### 1. Посчитать запросы

Самый быстрый способ — включить логирование SQL.

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            backtrace: true
```

Или в dev-окружении:

```php
$em = $this->getContainer()->get('doctrine')->getManager();
$em->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());
```

В dev-баре Symfony видны все запросы. Если для одной страницы видно:

```
SELECT o.* FROM orders o WHERE o.customer_id = 42
SELECT oi.* FROM order_items oi WHERE oi.order_id = 1
SELECT oi.* FROM order_items oi WHERE oi.order_id = 2
SELECT oi.* FROM order_items oi WHERE oi.order_id = 3
SELECT oi.* FROM order_items oi WHERE oi.order_id = 4
SELECT oi.* FROM order_items oi WHERE oi.order_id = 5
```

— это N+1.

### 2. Подтвердить по данным

Открыть `pg_stat_statements` или pgBadger:

```sql
SELECT
    calls,
    mean_exec_time,
    total_exec_time,
    LEFT(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

Если один и тот же запрос выполняется 200 раз с разным `WHERE order_id = X` — это N+1.

### 3. Проверить в профайлере

Xdebug / Blackfire показывают время, потраченное на SQL. Если 80% времени страницы — в `Doctrine\DBAL\Connection::executeQuery()` с одинаковым query plan — N+1 подтверждён.

### 4. Мысленный тест

Спросить себя: "какой запрос Doctrine делает при первом обращении к lazy-коллекции?"

```php
// Если здесь нет fetch join:
$orders = $em->getRepository(Order::class)->findBy(['customerId' => $id]);

foreach ($orders as $order) {
    $items = $order->items(); // ← lazy load → N+1
}
```

---

## Решения и их trade-offs

### 1. Fetch Join (DQL)

Самый частый и правильный ответ.

```php
$qb = $em->createQueryBuilder();

$qb
    ->select('o', 'i')
    ->from(Order::class, 'o')
    ->innerJoin('o.items', 'i')
    ->where('o.customerId = :customerId')
    ->setParameter('customerId', $customerId);

$orders = $qb->getQuery()->getResult();
```

Результат:

```sql
SELECT o.*, i.*
FROM orders o
INNER JOIN order_items i ON i.order_id = o.id
WHERE o.customer_id = 42;
```

Один запрос вместо N+1.

| Плюс | Минус |
|------|-------|
| Один запрос. | Реляционное произведение: если у 50 заказов по 10 позиций — 500 строк в результате. |
| Нет магии, читается. | При большом объёме данных — тяжелый запрос. |
| Работает с любой БД. | Не всегда возможно: если нужно lazy-подгрузить только часть. |
| Doctrine корректно гидратит. | `getResult()` возвращает массив с дубликатами корневого объекта — Doctrine дедуплицирует. |

**Trade-off ключевой:** fetch join — это **один тяжёлый запрос** вместо **N лёгких**. Если данные маленькие — отлично. Если данные большие — может быть хуже.

---

### 2. DQL с `PARTIAL`

Загрузить только нужные поля:

```php
$orders = $em->createQuery(
    'SELECT PARTIAL o.{id, customerId, totalAmount, currency, status}
     FROM App\Entity\Order o
     WHERE o.customerId = :id'
)->setParameter('id', 42)
  ->getResult();
```

Или загрузить часть связанных:

```php
SELECT o, i.{id, quantity, unitPrice}
FROM App\Entity\Order o
JOIN o.items i
```

| Плюс | Минус |
|------|-------|
| Меньше данных в памяти. | Сложнее читать. |
| Меньше памяти на hydrate. | Не все поля доступны — если код к ним обратится — Additional Lazy Load exception. |
| Полезно для списков / таблиц. | Легко сломать при рефакторинге (добавили поле в шаблон — упало). |

**Когда:** списки заказов в админке, где показываются только `id`, `status`, `total`, `createdAt`.

---

### 3. Batch size (разбиение на страницы)

Если N слишком большой для одного запроса:

```php
$orders = $qb->getQuery()
    ->setMaxResults(50)
    ->setFirstResult($offset)
    ->getResult();
```

| Плюс | Минус |
|------|-------|
| Контроль размера запроса. | Всё равно N+1 внутри страницы (50 заказов → 1 join + 50 строк, ок). |
| Пагинация — естественная. | Не решает N+1, если `items()` вызывается в цикле без join. |

**Когда:** бесконечный скролл, длинные списки.

---

### 4. `Doctrine\Common\Collections\Expr\Comparison` + `->where()`

Иногда N+1 возникает не в `items()`, а в `->filter()`:

```php
$items = $order->items()->filter(
    fn (OrderItem $i) => $i->unitPrice() > 100
);
```

Это **PHP-side filter** — не SQL. Если `items` ещё не загружены — Doctrine сделает один запрос за всё (это ок). Но если `items` уже загружены — это просто `array_filter` (тоже ок). N+1 здесь **не** возникает.

Настоящий N+1 — это **lazy loading коллекции в цикле**.

---

### 5. Subselect / `WHERE IN` (ручной batch)

Если DQL-join не подходит (например, нужна фильтрация на уровне PHP, или коллекция огромная):

```php
$orders = $em->getRepository(Order::class)->findBy(['customerId' => $id]);

$ids = array_map(fn ($o) => $o->id(), $orders);

$items = $em->getRepository(OrderItem::class)
    ->createQueryBuilder('i')
    ->where('i.order IN (' . implode(',', $ids) . ')')
    ->getQuery()
    ->getResult();

// Группировать вручную
$itemsByOrderId = [];
foreach ($items as $item) {
    $itemsByOrderId[$item->order()->id()][] = $item;
}
```

| Плюс | Минус |
|------|-------|
| Два запроса вместо N+1. | Нужно вручную группировать. |
| Гибкость: можно добавить фильтры. | Нарушает инкапсуляцию агрегата (загружаем items отдельно). |
| Хорошо для очень больших N. | Сложнее читать, легче ошибиться. |

**Когда:** DQL-join даёт слишком большой результат, или нужно применять сложные фильтры к items, которые не выражаются в DQL.

---

### 6. Denormalization (предзапись данных)

В корне агрегата храним данные, которые обычно берут из children:

```php
#[ORM\Column(type: Types::INTEGER)]
private int $totalAmount;      // уже есть

#[ORM\Column(type: Types::INTEGER)]
private int $itemCount;        // ← денормализовано

#[ORM\Column(type: Types::STRING, length: 255)]
private string $firstItemName; // ← денормализовано
```

| Плюс | Минус |
|------|-------|
| N+1 исчезает полностью: данные в корне. | Дублирование данных. |
| Быстрый доступ без join. | Нужно держать синхронно (при каждом изменении). |
| Хорошо для поисковых индексов. | Ломает источник истины: два места для одного факта. |

**Когда:** `totalAmount`, `itemCount` — уже денормализованы. `firstItemName` — избыточно, если только не нужен для поиска.

---

### 7. Query result cache / entity cache

```php
$qb->getQuery()
    ->setResultCacheItem(
        new CacheItem('orders_' . $customerId),
        new CacheItem('orders_' . $customerId)
    );
```

Или entity cache:

```php
$em->getUnitOfWork()->getEntityCache($order)
```

| Плюс | Минус |
|------|-------|
| Повторные запросы — из кэша. | Кэш может быть устаревшим. |
| Снимает нагрузку с БД. | Сложность инвалидации. |
| Хорошо для read-heavy. | Не решает N+1 при первом запросе. |

**Когда:** данные меняются редко (справочники, конфигурация). Не подходит для `orders` — меняются часто.

---

### 8. `@Lazy` + `__get` magic (Symfony Proxy)

Если сущность используется только для чтения и не нужно сразу грузить children:

```php
// Не вызываем $order->items() в списке заказов
// Только когда реально нужен детальный вид
```

Это не решение N+1, а **отсрочка** — если вы не трогаете lazy-коллекцию, запрос не идёт.

| Плюс | Минус |
|------|-------|
| Нулевая стоимость, если данные не нужны. | Если данные нужны — N+1 всё равно возникнет. |
| Нет изменений в маппинге. | Легко случайно вызвать `->items()` в шаблоне. |

**Когда:** в списке заказов показываются только `id`, `status`, `total` — items не нужны.

---

### 9. Separate table for read model (CQRS-lite)

```sql
CREATE TABLE order_summaries (
    order_id    INT PRIMARY KEY,
    customer_id INT NOT NULL,
    status      VARCHAR(32) NOT NULL,
    total_amount INT NOT NULL,
    currency    VARCHAR(3) NOT NULL,
    item_count  INT NOT NULL,
    created_at  TIMESTAMP NOT NULL
);
```

Обновляется триггером или отдельным процессом после изменений.

| Плюс | Минус |
|------|-------|
| Чтение — один запрос, без join. | Дублирование данных. |
| Отдельная оптимизация для чтения. | Нужно поддерживать синхронизацию. |
| Индексы под конкретные сценарии. | Сложность: два источника истины. |

**Когда:** read-heavy, разные сценарии чтения, объём > 1M строк.

---

## Сравнительная матрица

| Метод | Сложность | Когда | Risco |
|-------|:---------:|-------|-------|
| Fetch Join | Низкая | До 500–1000 строк в join | Тяжёлый SQL при большом N |
| PARTIAL | Низкая | Списки, таблицы | Сломается при рефакторинге |
| MaxResults | Низкая | Пагинация | Не решает N+1 внутри страницы |
| WHERE IN batch | Средняя | Очень большой N | Нарушение инкапсуляции |
| Denormalization | Средняя | Частый доступ к aggregate-суммарии | Дублирование, синхронизация |
| Cache | Средняя | Редко меняющиеся данные | Инвалидация |
| Lazy (не трогать) | Нулевая | Data не нужна | Случайный вызов в шаблоне |
| Read model (CQRS) | Высокая | Read-heavy, разные сценарии | Два источника истины |

---

## Практический алгоритм

```
N+1 обнаружен
  │
  ├─ Сколько N?
  │   ├─ N < 100 → Fetch Join. Конец.
  │   ├─ N = 100–1000 → Fetch Join + PARTIAL.
  │   └─ N > 1000 → WHERE IN batch или пагинация.
  │
  ├─ Нужны ли все children?
  │   ├─ Да → Fetch Join.
  │   └─ Нет → PARTIAL или lazy (не трогать).
  │
  ├─ Данные меняются часто?
  │   ├─ Да → Не кэшировать.
  │   └─ Нет → Result cache.
  │
  └─ Read-heavy, разные сценарии?
      ├─ Да → Read model / CQRS-lite.
      └─ Нет → Fetch Join + PARTIAL.
```

---

## Чего делать не надо

| Антипаттерн | Почему |
|-------------|--------|
| `EntityManager::clear()` в цикле | Теряются изменения, ломается UoW. |
| `->setFetchMode(Lazy)` на `OneToMany` | Это не "лечит" N+1 — просто скрывает. |
| `$em->flush()` внутри цикла | N flush'ов, N транзакций. |
| `->getSingleResult()` на join-запрос | Бросит `NoResultException`, если больше одной строки. |
| `array_filter` на не-загруженной коллекции | Это не SQL-фильтр, а PHP-фильтр по уже загруженным данным. Если данные не загружены — Doctrine сделает один запрос, а потом filter в PHP. |

---

## Итог

N+1 — это не "баг Doctrine", а **архитектурное решение**: вы решили, что каждый дочерний объект загружается отдельно. Doctrine просто уважает это решение.

Решение — изменить то, **сколько данных** вы запрашиваете и **как вы их запрашиваете**:

- **Fetch Join** — для нормального N.
- **PARTIAL** — если нужны не все поля.
- **Batch / IN** — для большого N.
- **Denormalization** — если данные в корне уже есть.
- **Read model** — если сценарии чтения разные и объём большой.

В 90% случаев — **Fetch Join + PARTIAL** решает проблему без переосмысления архитектуры.

> ⏱ 35.2s · prompt 49016 tok · output 3456 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user asks: if all business validation is done in application code, why do we still need database-level integrity constraints in PostgreSQL? What tools do we use and in which cases?

This is a deep architectural question. Let me think about what to cover:

1. Why DB constraints are needed even with app validation
2. What specific PostgreSQL tools exist (CHECK, FK, UNIQUE, EXCLUDE, triggers, table constraints, column constraints, partitioning constraints)
3. Concrete cases where each is appropriate
4. Trade-offs: performance vs safety, readability vs expressiveness
5. Anti-patterns of overusing DB constraints

I should structure this clearly:
- "Why" section: the fundamental reasons
- "What tools" section: concrete PostgreSQL mechanisms with examples
- "When" section: decision framework
- Trade-offs and anti-patterns

Let me think about the real-world reasons:
1. Multiple writers (other services, raw SQL, admin tools)
2. Bugs in application code (race conditions, off-by-one)
3. Migration gaps (new code deployed, old code still running)
4. Manual data manipulation (DBA fixes, backfills)
5. Defense in depth (the DB is the last line of defense)
6. Self-documenting (constraints make the schema self-explanatory)
7. Performance (NOT NULL, UNIQUE can optimize query plans)

PostgreSQL tools:
- NOT NULL
- UNIQUE
- PRIMARY KEY
- FOREIGN KEY (with ON DELETE/UPDATE actions)
- CHECK constraints (column-level and table-level)
- EXCLUDE constraints (for overlapping ranges — very powerful, not in other DBs)
- DEFAULT values
- Generated/identity columns
- Triggers (BEFORE/AFTER INSERT/UPDATE/DELETE)
- Rules (less common now)
- Table-level CHECK (cross-column)
- Domain types (custom types with CHECK)
- Constraints on partitions
- Advisory locks (not a constraint per se, but related)

Let me write a comprehensive answer with concrete examples tied to the Order domain we've been discussing.

</details>

**Кандидат:**

## Почему одного приложения недостаточно

Валидация в коде — это **первая** линия защиты. Но данные живут в БД, а БД может быть записана из:

| Источник | Пример |
|----------|--------|
| Другой микросервис | `billing-service` пишет `orders.total_amount` напрямую |
| SQL-консоль | DBA правит данные: `UPDATE orders SET total_amount = 0 WHERE id = 1` |
| Миграция | Миграция отключает constraint для бэкапа, забыл включить |
| Старый код | Rollover деплоя: новый код валидирует, старый ещё работает 30 минут |
| Bug | Race condition: два процесса читают `version = 5`, оба пишут `version = 6` |
| Raw SQL из ORM | DQL с `UPDATE ... SET` обходит доменные методы |
| Backfill | Скрипт импорта 2 млн строк без валидации |

DB-констрейнт — **последняя** линия защиты. Он не заменяет валидацию в коде (кодек должен бросать понятный `DomainException`), но гарантирует, что **в БД не попадёт** недопустимое состояние, даже если код сломался.

---

## Средства PostgreSQL с примерами

### 1. `NOT NULL` — отсутствие значения

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_orders_customer_id_not_null
    CHECK (customer_id IS NOT NULL);

-- Или на уровне колонки:
ALTER TABLE orders
    ALTER COLUMN total_amount SET NOT NULL,
    ALTER COLUMN currency SET NOT NULL;
```

**Когда:** любое поле, для которого "пустое значение" — это не состояние, а баг.

**Почему не только код:** `INSERT INTO orders (id) VALUES (1);` — без `NOT NULL` это допустимо.

**Бонус:** PostgreSQL может оптимизировать запросы: `WHERE total_amount IS NULL` — если колонка `NOT NULL`, оптимизатор знает, что условие всегда `false`, и может пропустить сканирование.

---

### 2. `UNIQUE` — уникальность

```sql
-- Одно естественное ограничение
ALTER TABLE orders
    ADD CONSTRAINT uq_orders_customer_idempotency_key
    UNIQUE (idempotency_key);

-- Комбинированное: email уникален в рамках active
ALTER TABLE users
    ADD CONSTRAINT uq_users_active_email
    UNIQUE (email) WHERE is_active = true;
    -- ↑ частичный индекс — PostgreSQL-specific
```

**Когда:**
- idempotency key — защита от дублей при retry.
- email — защита от дубликата.
- `(order_id, item_id)` — защита от дубля позиции.

**Почему не только код:** race condition — два запроса одновременно проходят `SELECT ... WHERE email = 'x'` → оба видят `null` → оба вставляют. `UNIQUE` — атомарная защита на уровне БД.

**Частичный уникальный индекс** — мощная фича PostgreSQL:

```sql
-- Только active пользователи имеют уникальный email
-- Inactive могут дублироваться
CREATE UNIQUE INDEX uq_users_active_email
    ON users (email)
    WHERE deleted_at IS NULL;
```

В MySQL/PostgreSQL-совместимых ORM это не всегда выражается в миграции. В raw SQL — однострочно.

---

### 3. `FOREIGN KEY` — ссылочная целостность

```sql
ALTER TABLE order_items
    ADD CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id)
    REFERENCES orders (id)
    ON DELETE CASCADE;
```

**Когда:** дочерняя сущность не должна существовать без родителя.

**Почему не только код:**
- `DELETE FROM orders WHERE id = 5;` — без FK в `order_items` останутся orphan-строки.
- `UPDATE order_items SET order_id = 999;` — без FK ссылка на несуществующий заказ.

**`ON DELETE` / `ON UPDATE` действия:**

| Действие | Когда |
|----------|-------|
| `CASCADE` | Дочерние живут только внутри родителя (Order → OrderItem) |
| `RESTRICT` | Нельзя удалить, пока есть ссылки (User → Orders) |
| `SET NULL` | Ссылка необязательна (OrderItem → Product, если product удалён) |
| `NO ACTION` (default) | Проверка на FK, но без каскада |

**Пример:**

```sql
-- Нельзя удалить пользователя, пока у него есть заказы
ALTER TABLE orders
    ADD CONSTRAINT fk_orders_customer
    FOREIGN KEY (customer_id)
    REFERENCES customers (id)
    ON DELETE RESTRICT;
```

---

### 4. `CHECK` — доменные инварианты

Самый мощный инструмент для бизнес-правил на уровне БД.

```sql
-- Сумма не может быть отрицательной
ALTER TABLE orders
    ADD CONSTRAINT chk_orders_total_non_negative
    CHECK (total_amount >= 0);

-- Валюта — 3 буквы
ALTER TABLE orders
    ADD CONSTRAINT chk_orders_currency_iso
    CHECK (currency ~ '^[A-Z]{3}$');

-- Статус из допустимого набора
ALTER TABLE orders
    ADD CONSTRAINT chk_orders_status_valid
    CHECK (status IN ('new', 'confirmed', 'shipped', 'cancelled'));
```

#### Table-level CHECK (межколоночные)

```sql
-- end_date > start_date
ALTER TABLE subscriptions
    ADD CONSTRAINT chk_subscriptions_date_order
    CHECK (end_date > start_date);

-- Если status = 'paid', то paid_at не null
ALTER TABLE orders
    ADD CONSTRAINT chk_orders_paid_consistency
    CHECK (
        (status = 'paid' AND paid_at IS NOT NULL)
        OR
        (status <> 'paid' AND paid_at IS NULL)
    );

-- quantity * unit_price == line_total (анти-ошибка при ручном SQL)
ALTER TABLE order_items
    ADD CONSTRAINT chk_items_line_total
    CHECK (quantity * unit_price = line_total);
```

**Когда:**
- Диапазон значений (возраст, сумма, количество).
- Межколоночная согласованность (start < end, status ↔ date).
- Набор допустимых значений (enum).

**Почему не только код:**
- `UPDATE orders SET total_amount = -100 WHERE id = 5;` — CHECK бросит.
- Миграция, которая не знает про бизнес-правило — CHECK знает.
- DBA видит правило прямо в `pg_constraint`.

**Ограничение:** `CHECK` не может ссылаться на другие таблицы. Для этого — триггер или FK.

---

### 5. `EXCLUDE` — неперекрываемость интервалов (PostgreSQL-specific)

Очень мощно для временных диапазонов:

```sql
-- Подписки одного пользователя не могут перекрываться
CREATE TABLE subscriptions (
    id          BIGINT PRIMARY KEY,
    user_id     BIGINT NOT NULL,
    start_date  DATE NOT NULL,
    end_date    DATE NOT NULL,
    plan        TEXT NOT NULL,

    EXCLUDE USING gist (
        user_id   WITH =,
        daterange(start_date, end_date) WITH &&
    ) WHERE (is_active)
);
```

`&&` — оператор перекрываемости интервалов.

**Когда:**
- Бронирование отеля (не два бронирования на одни и те же даты).
- Подписки (не две активные на одного пользователя).
- Время работы (не два сменных графика пересекаются).

**Почему не только код:** race condition — два запроса одновременно проверяют "нет пересечения" → оба проходят → оба вставляют. `EXCLUDE` — атомарная проверка в момент `INSERT`.

**Ограничение:** только PostgreSQL. Только с `gist`-индексом. Не работает с `SERIAL` / `IDENTITY` без `gist`-поддержки типа.

---

### 6. `DEFAULT` и `GENERATED` — производные значения

```sql
-- created_at заполняется автоматически
ALTER TABLE orders
    ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT now();

-- IDENTITY (PostgreSQL 10+)
ALTER TABLE orders
    ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY;

-- Generated column (PostgreSQL 12+)
ALTER TABLE orders
    ADD COLUMN total_cents INT GENERATED ALWAYS AS (total_amount * 100) STORED;
```

**Когда:**
- `created_at`, `updated_at` — не должны зависеть от приложения.
- Identity-колонки — защита от ручного `INSERT` с дублем ID.
- Derived columns — защита от рассогласования (`total_cents` всегда = `total_amount * 100`).

**Почему не только код:** `INSERT INTO orders (id, ...) VALUES (1, ...)` — `GENERATED ALWAYS` бросит.

---

### 7. Триггеры — когда `CHECK` не хватает

```sql
-- Пример: total_amount = сумма line_total
CREATE OR REPLACE FUNCTION trg_orders_recalculate_total()
RETURNS TRIGGER AS $$
BEGIN
    NEW.total_amount := (
        SELECT COALESCE(SUM(line_total), 0)
        FROM order_items
        WHERE order_id = NEW.id
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_orders_recalculate_total
    BEFORE INSERT OR UPDATE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION trg_orders_recalculate_total();
```

**Когда `CHECK` не хватает:**
- Нужно прочитать из другой таблицы.
- Нужна логика с `CASE`, `EXISTS`, подзапросом.
- Нужно запретить обновление конкретной колонки.

**Когда триггеры — антипаттерн:**
- Логика бизнеса (отправка email, расчёт скидки) — это код приложения.
- Более 3 строк SQL в триггере — перенесите в код.

**Правило:** триггеры — для **технической целостности** (derived columns, FK-подобные проверки), а не для **бизнес-логики**.

---

### 8. Partial index + `WHERE` в constraint

```sql
-- Только для активных пользователей
ALTER TABLE users
    ADD CONSTRAINT uq_users_active_email
    UNIQUE (email) WHERE is_active = true;

-- Только для записей с определённым статусом
CREATE INDEX idx_orders_active
    ON orders (customer_id)
    WHERE status IN ('new', 'confirmed');
```

**Когда:** правило валидно только для подмножества данных.

---

### 9. `DOMAIN` типы

```sql
CREATE DOMAIN positive_int AS INT CHECK (VALUE > 0);

CREATE DOMAIN iso_currency AS VARCHAR(3) CHECK (VALUE ~ '^[A-Z]{3}$');

ALTER TABLE orders
    ALTER COLUMN total_amount TYPE positive_int,
    ALTER COLUMN currency TYPE iso_currency;
```

**Когда:** одно и то же правило применяется к нескольким таблицам.

**Минус:** не все ORM/миграции поддерживают. Меньше читаемости в `pg_dump`.

---

## Матрица: какой инструмент для какого случая

| Случай | Инструмент | Пример |
|--------|-----------|--------|
| Поле не может быть пустым | `NOT NULL` | `total_amount NOT NULL` |
| Значение из конечного набора | `CHECK` | `status IN ('new', 'paid')` |
| Диапазон | `CHECK` | `quantity > 0` |
| Межколоночная согласованность | Table `CHECK` | `end_date > start_date` |
| Ссылка на другую таблицу | `FOREIGN KEY` | `order_items.order_id → orders.id` |
| Уникальность | `UNIQUE` | `UNIQUE (idempotency_key)` |
| Уникальность для подмножества | Partial `UNIQUE` | `UNIQUE (email) WHERE active` |
| Неперекрываемость интервалов | `EXCLUDE` | Бронирование |
| Производное значение | `GENERATED` | `total_cents = amount * 100` |
| Автозаполнение | `DEFAULT` / `IDENTITY` | `created_at DEFAULT now()` |
| Логика с подзапросом | Триггер | Пересчёт total |

---

## Trade-offs

### Performance

| Инструмент | Влияние на INSERT/UPDATE |
|------------|--------------------------|
| `NOT NULL` | Нулевое |
| `CHECK` | Мелкое (выражение вычисляется) |
| `UNIQUE` | Индекс → overhead на INSERT, но speedup на SELECT |
| `FOREIGN KEY` | Индекс на referenced + проверка на INSERT/DELETE |
| `EXCLUDE` | `gist`-индекс → overhead, но защита от пересечения |
| Триггер | SQL-вызов → заметный overhead при массовых операциях |

Для массового импорта (1M строк):

```sql
-- Временно отключить constraint
ALTER TABLE orders NOT VALID;  -- для CHECK
ALTER TABLE orders DROP CONSTRAINT fk_orders_customer;

-- Импорт
COPY orders FROM ...;

-- Включить обратно
ALTER TABLE orders VALIDATE CONSTRAINT chk_orders_total_non_negative;
ALTER TABLE orders ADD CONSTRAINT fk_orders_customer FOREIGN KEY ...;
```

Это trade-off: скорость импорта vs целостность. Валидация должна быть в коде скрипта импорта, а constraint — страховка.

### Прозрачность

| | `CHECK` | Триггер |
|---|---------|---------|
| Видно в `pg_constraint` | Да | Нет (в `pg_trigger`) |
| Видно в `pg_dump` | Да | Да, но как `CREATE FUNCTION` + `CREATE TRIGGER` |
| Читаемость | Высокая | Средняя |
| Отладка | `SELECT * FROM pg_constraint WHERE conname = '...'` | Нужно читать код триггера |

**Правило:** если можно выразить в `CHECK` — не пишите триггер.

### Совместимость

| | PostgreSQL | MySQL | SQLite |
|---|:---:|:---:|:---:|
| `CHECK` | ✅ | ⚠️ (игнорировалось до 8.0.16) | ✅ |
| `EXCLUDE` | ✅ | ❌ | ❌ |
| Partial index | ✅ | ❌ | ✅ |
| `GENERATED` | ✅ | ✅ (8.0.13+) | ❌ |
| `DOMAIN` | ✅ | ❌ | ❌ |
| Триггеры | ✅ | ✅ | ✅ |

Если есть вероятность миграции на другую БД — избегайте `EXCLUDE` и `DOMAIN`.

---

## Что **не** делать

### 1. Бизнес-логику в триггерах

```sql
-- ❌ Не делайте:
CREATE TRIGGER trg_send_email
    AFTER INSERT ON orders
    EXECUTE FUNCTION send_welcome_email();
```

Это не целостность. Это бизнес-логика, которая:
- не тестируется как код;
- не видна в application code;
- ломается при изменении бизнес-правил;
- не откатывается при error в другом месте.

### 2. `ON DELETE CASCADE` для всего

```sql
-- ❌ Не делайте:
ALTER TABLE order_items
    ADD CONSTRAINT fk_items_order
    FOREIGN KEY (order_id)
    REFERENCES orders (id)
    ON DELETE CASCADE;
```

`CASCADE` — правильное для `Order → OrderItem` (агрегат). Но:

```sql
-- ❌ Опасно:
ALTER TABLE payments
    ADD CONSTRAINT fk_payments_order
    FOREIGN KEY (order_id)
    REFERENCES orders (id)
    ON DELETE CASCADE;
```

Если удалить заказ — удалятся и платежи. Это **не** desired behavior. Правильно: `RESTRICT` — нельзя удалить, пока есть платежи.

### 3. `CHECK` вместо `FOREIGN KEY`

```sql
-- ❌ Не делайте:
ALTER TABLE order_items
    ADD CONSTRAINT chk_items_order_exists
    CHECK (order_id IN (SELECT id FROM orders));

-- ✅ Делайте:
ALTER TABLE order_items
    ADD CONSTRAINT fk_items_order
    FOREIGN KEY (order_id) REFERENCES orders (id);
```

`FOREIGN KEY` создаёт индекс, поддерживает `ON DELETE`, виден в `pg_constraint` с правильным типом. `CHECK` с подзапросом — медленнее, без каскадов, без индекса.

### 4. Дублирование валидации без причины

```php
// В коде:
if ($amount < 0) { throw new DomainException(); }

// В БД:
CHECK (amount >= 0)
```

Это **не** дублирование. Это **defense in depth**. Код бросает понятный `DomainException` для пользователя. БД бросает `23514: check constraint violation` для безопасности.

Но:

```php
// В коде:
if ($status === 'paid' && $paidAt === null) { throw new DomainException(); }

// В БД:
CHECK ((status = 'paid' AND paid_at IS NOT NULL) OR (status <> 'paid' AND paid_at IS NULL))

// И в триггере:
CREATE TRIGGER trg_check_status_date ...
```

Это **троекратное** дублирование. Выберите одно место (предпочтительно `CHECK`), и в коде просто бросайте `DomainException` на том же условии — но не дублируйте триггер.

---

## Практический чеклист

Перед тем как добавить constraint:

1. **Это техническая целостность или бизнес-логика?**
   - Техническая (NOT NULL, FK, UNIQUE, CHECK диапазон) → в БД.
   - Бизнес-логика (скидки, лимиты, правила) → в коде.

2. **Можно выразить в `CHECK`?**
   - Да → `CHECK`.
   - Нет, нужен подзапрос → триггер (но подумайте, нужен ли он).
   - Нужен индекс → `UNIQUE` / `FOREIGN KEY`.
   - Неперекрываемость → `EXCLUDE`.

3. **Что будет при массовом импорте?**
   - Нужно ли временно отключать?
   - Если да — документируйте.

4. **Что будет при `pg_dump`?**
   - Constraint виден в дампе?
   - DBA понимает его смысл без чтения кода?

---

## Итог

| Условие | Кто отвечает |
|---------|-------------|
| Понятная ошибка для пользователя | Приложение (`DomainException`) |
| Гарантия, что в БД нет invalid state | БД (constraint) |
| Производительность массовых операций | Оба (код оптимизирует, БД гарантирует) |
| Документирование допустимого состояния | Оба (код — для разработчика, constraint — для DBA) |

DB-констрейнт — это **контракт с данными**, а не дублирование кода. Код может иметь баг. Данные — нет.

> ⏱ 45.9s · prompt 52359 tok · output 4815 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе диагностики и оптимизации медленных запросов в PostgreSQL. Это типичный вопрос собеседования для бэкенд-разработчика. Нужно показать системный подход: от диагностики к оптимизации, с конкретными инструментами и примерами.

Структурирую ответ как реальный процесс работы:
1. Диагностика — что делаю сначала (EXPLAIN, pg_stat_statements, pg_stat_activity)
2. Типовые причины медленных запросов (нет индекса, seq scan, lock, N+1, bad plan)
3. Оптимизация — какие инструменты и приёмы
4. Примеры конкретных кейсов

Надо показать, что я не просто "ставлю индекс", а понимаю, что EXPLAIN — это не панацея, что есть разные типы планов, что pg_stat_statements — это основной инструмент для нахождения проблемных запросов, что иногда проблема не в самом запросе, а в том, как он вызывается (N+1, batch size).

Буду писать на русском, как и весь разговор.

</details>

**Кандидат:**

## Процесс диагностики

### Шаг 1: Уточнить, что именно медленно

Прежде чем открывать `EXPLAIN`, надо понять масштаб:

| Вопрос | Зачем |
|--------|-------|
| Один запрос медленный или все? | Если все — проблема на уровне БД (lock, I/O, memory). Если один — проблема в плане. |
| Когда начался? | Если после деплоя — новая миграция/индекс. Если постепенно — данные выросли. |
| Сколько ms? | 50ms — ок. 5s — плохо. 30s — incident. |
| В peak или в off-peak? | Peak — конкуренция за ресурсы. Off-peak — сам запрос тяжёлый. |
| Частота? | 1 раз в день × 5s = ок. 1000 раз/мин × 5s = катастрофа. |

### Шаг 2: `pg_stat_statements`

Это **первый** инструмент, который я открываю. Расширение должно быть включено:

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
```

```sql
SELECT
    calls,
    total_exec_time / calls AS avg_ms,
    total_exec_time AS total_ms,
    rows,
    LEFT(query, 200) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

Что смотрю:

| Метрика | Что значит |
|---------|-----------|
| `total_exec_time` высокий, `calls` высокий | Частый запрос, даже если avg небольшой |
| `total_exec_time` высокий, `calls` низкий | Редкий, но очень тяжёлый запрос |
| `rows` = 0, `total_exec_time` высокий | Запрос ищет, но не находит (bad plan) |
| `rows` = 10 000 000, `total_exec_time` высокий | Слишком много данных (missing filter) |

### Шаг 3: `EXPLAIN (ANALYZE, BUFFERS)`

Для конкретного медленного запроса:

```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT o.id, o.status, o.total_amount
FROM orders o
WHERE o.customer_id = 42
  AND o.created_at > '2024-01-01'
ORDER BY o.created_at DESC
LIMIT 20;
```

**Важно:** `ANALYZE` — реально выполняет запрос и показывает фактическое время. `BUFFERS` — показывает, сколько страниц БД было прочитано из shared_buffers vs disk.

### Шаг 4: Анализ плана

Каждый узел плана читается **снизу вверх** (leaf nodes → root).

#### Ключевые признаки проблем

| В плане вижу | Значит |
|-------------|--------|
| `Seq Scan` на большой таблице | Нет подходящего индекса или план-оценщик ошибся |
| `Sort Method: external merge Disk: 4096 kB` | `work_mem` мал, сортировка уходит на диск |
| `Nested Loop` + `rows=1000000` | Плохой выбор: должен быть Hash/ Merge Join |
| `Hash Join` + `Hash Buckets: 1024` | Маловато buckets, возможен memory pressure |
| `Bitmap Heap Scan` | Нормально, если `Bitmap Index Scan` выше — индекс используется |
| `CTE Scan` + `Materialize` | CTE материализована — может быть дорого |
| `Index Scan Backward` | Нормально для `ORDER BY ... DESC` |
| `Rows Removed by Filter: 999990` | Индекс найден, но почти все строки отфильтрованы — нужен partial index |
| `I/O Timings: read=5000.000` | I/O bound — данные на диске, не в shared_buffers |

#### Пример "больного" плана

```
Limit  (cost=125000.00..125001.00 rows=20 width=64) (actual time=4500.120..4500.130 rows=20 loops=1)
  ->  Sort  (cost=125000.00..125000.05 rows=20 width=64) (actual time=4499.800..4499.810 rows=20 loops=1)
        Sort Key: o.created_at DESC
        Sort Method: top-N heapsort  Memory: 43kB
        ->  Seq Scan on orders o  (cost=0.00..124999.00 rows=20 width=64) (actual time=0.050..4400.200 rows=450000 loops=1)
              Filter: ((customer_id = 42) AND (created_at > '2024-01-01'::date))
              Rows Removed by Filter: 9550000
  Planning Time: 0.100 ms
  Execution Time: 4500.300 ms
```

**Диагноз:** `Seq Scan` на 10 млн строк, `Rows Removed by Filter: 9 550 000`. Нет индекса по `(customer_id, created_at)`.

**Решение:**

```sql
CREATE INDEX idx_orders_customer_created
    ON orders (customer_id, created_at DESC);
```

После:

```
Limit  (cost=0.43..4.00 rows=20 width=64) (actual time=0.100..0.200 rows=20 loops=1)
  ->  Index Scan using idx_orders_customer_created on orders o
          (cost=0.43..6000.00 rows=4500 width=64) (actual time=0.050..0.150 rows=20 loops=1)
          Index Cond: (customer_id = 42)
          Filter: (created_at > '2024-01-01'::date)
  Execution Time: 0.250 ms
```

4500ms → 0.25ms.

---

## Типовые причины медленных запросов

### 1. Нет индекса (или индекс неправильный)

Самая частая причина. ~60% медленных запросов.

**Симптом:** `Seq Scan` на таблице > 10k строк.

**Решение:**

```sql
-- Индекс по частому WHERE
CREATE INDEX idx_orders_customer ON orders (customer_id);

-- Комбинированный (покрытие частого WHERE + ORDER BY)
CREATE INDEX idx_orders_customer_created
    ON orders (customer_id, created_at DESC);

-- Partial (если большая часть строк — active)
CREATE INDEX idx_orders_active_customer
    ON orders (customer_id)
    WHERE status IN ('new', 'confirmed');

-- INCLUDE (covering index — не нужен heap access)
CREATE INDEX idx_orders_customer_covering
    ON orders (customer_id)
    INCLUDE (status, total_amount, created_at);
```

**Принцип построения индекса:**

```
WHERE columns (equality) → WHERE columns (range) → ORDER BY columns
```

Пример:

```sql
-- WHERE customer_id = 42 AND created_at > '2024-01-01' ORDER BY created_at DESC
-- Индекс: (customer_id, created_at DESC)
```

Не наоборот: `(created_at, customer_id)` — не сработает для equality на `customer_id`.

### 2. `work_mem` мал — сортировка/хэш на диск

**Симптом:** `Sort Method: external merge Disk: 8192 kB`.

**Решение:**

```sql
-- Глобально
SET work_mem = '64MB';

-- Или на уровне сессии
SET LOCAL work_mem = '128MB';

-- Или на уровне запроса
SET work_mem = '64MB' ORDER BY ...;
```

**Внимание:** `work_mem` — на **каждый** sort/hash в **каждом** узле плана. Если в плане 3 sort'а — реально 3 × 64MB.

### 3. Плохой выбор типа join

| Тип | Когда хорошо | Когда плохо |
|-----|-------------|-------------|
| `Nested Loop` | Одна сторона маленькая (< 1000 строк) | Обе стороны большие |
| `Hash Join` | Обе стороны средние/большие | Одна сторона очень маленькая (overhead на hash) |
| `Merge Join` | Обе стороны отсортированы по join-колонке | Нет сортировки (нужен extra sort) |

**Симптом:** `Nested Loop` с `rows=500000` на внутренней стороне.

**Решение:**

```sql
-- Принудительно (крайний случай):
SET enable_nestloop = off;  -- временно, для диагностики
```

Но лучше — исправить данные/индексы, чтобы план-оценщик выбрал правильный.

### 4. Неправильная оценка `rows` (bad statistics)

**Симптом:** `rows=100` в плане, но `actual time=... rows=5000000`.

Это значит: план-оценщик думал, что будет 100 строк, а было 5 млн. Выбрал `Nested Loop`, потому что "внутренняя сторона маленькая". Но она не маленькая.

**Решение:**

```sql
-- Пересчёт статистики
ANALYZE orders;

-- Если данные очень несбалансированные — увеличить sampling:
ALTER TABLE orders SET (autovacuum_analyze_scale_factor = 0.05);
ANALYZE orders;
```

Или:

```sql
-- Расширенная статистика для multi-column correlation
CREATE STATISTICS stat_orders_status_customer
    ON status, customer_id FROM orders;
ANALYZE orders;
```

### 5. Lock contention

**Симптом:** Запрос "зависает" на `waiting for lock` — не в `EXPLAIN`, а в `pg_stat_activity`.

```sql
SELECT
    pid,
    state,
    wait_event_type,
    wait_event,
    LEFT(query, 200) AS query,
    NOW() - query_start AS duration
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY duration DESC;
```

Если `wait_event_type = Lock`, `wait_event = transactionid` — кто-то держит транзакцию.

```sql
-- Найти "владельца" лока
SELECT
    bl.pid AS blocking_pid,
    a.usename,
    LEFT(a.query, 100) AS blocking_query
FROM pg_locks bl
JOIN pg_stat_activity a ON a.pid = bl.pid
WHERE bl.pid IN (
    SELECT blocking_pid FROM pg_locks WHERE NOT granted
);
```

**Решение:**
- Укоротить транзакции.
- Не держать `BEGIN` открытыми в CLI.
- `LOCK_TIMEOUT = 5s` — не ждать бесконечно.
- `idle_in_transaction_session_timeout = 30s` — убить зависшие.

### 6. `N+1` на уровне приложения

Запрос по отдельности быстрый (5ms). Но вызывается 200 раз → 1s.

**Симптом:** в `pg_stat_statements` один и тот же query с `calls = 200`, `avg_ms = 5`, `total_ms = 1000`.

**Решение:** не на уровне БД, а на уровне приложения (fetch join, batch, CQRS).

### 7. Функции в `WHERE` — убивают индекс

```sql
-- ❌ Индекс на created_at не используется:
SELECT * FROM orders WHERE EXTRACT(YEAR FROM created_at) = 2024;

-- ✅ Индекс работает:
SELECT * FROM orders
WHERE created_at >= '2024-01-01' AND created_at < '2025-01-01';
```

Или:

```sql
-- ❌ LOWER(email) — индекс не работает
SELECT * FROM users WHERE LOWER(email) = 'john@example.com';

-- ✅ Functional index
CREATE INDEX idx_users_lower_email ON users (LOWER(email));
```

### 8. `SELECT *` + wide rows

**Симптом:** `width=2048` в плане. Каждая строка — 2KB.

**Решение:**

```sql
-- ❌
SELECT * FROM orders WHERE ...;

-- ✅
SELECT id, status, total_amount FROM orders WHERE ...;
```

### 9. `CTE` как "opt-out" от оптимизации

```sql
-- В PostgreSQL < 12 CTE материализуется (оптимизатор не может inline-ить)
WITH expensive AS (
    SELECT ... FROM big_table WHERE ...
)
SELECT * FROM expensive;
-- → всегда материализуется в temp table

-- В PostgreSQL 12+ можно:
WITH expensive AS NOT MATERIALIZED (
    SELECT ... FROM big_table WHERE ...
)
SELECT * FROM expensive;
-- → оптимизатор может inline-ить
```

### 10. `VACUUM` не успевает / bloat

**Симптом:** таблица "физически" 50GB, логически 10GB. Seq Scan читает 50GB.

```sql
SELECT
    nspname,
    relname,
    pg_size_pretty(pg_total_relation_size(relid)) AS total_size,
    n_live_tup,
    n_dead_tup,
    last_vacuum,
    last_autovacuum
FROM pg_stat_user_tables
ORDER BY n_dead_tup DESC
LIMIT 20;
```

Если `n_dead_tup / n_live_tup > 0.5` — bloat.

**Решение:**

```sql
-- Немедленный VACUUM (не ANALYZE!)
VACUUM (ANALYZE, VERBOSE) orders;

-- Или пересобрать таблицу (если bloat > 50%)
CREATE TABLE orders_new (LIKE orders INCLUDING ALL);
INSERT INTO orders_new SELECT * FROM orders;
DROP TABLE orders;
ALTER TABLE orders_new RENAME TO orders;
```

---

## Инструменты по порядку

| Шаг | Инструмент | Что даёт |
|-----|-----------|----------|
| 1 | `pg_stat_statements` | Какие запросы тратят больше всего времени |
| 2 | `EXPLAIN (ANALYZE, BUFFERS)` | План конкретного запроса, фактическое время |
| 3 | `pg_stat_activity` | Что происходит прямо сейчас (locks, waits) |
| 4 | `pg_stat_user_tables` | Bloat, vacuum, I/O |
| 5 | `pg_indexes` + `pg_index` | Какие индексы есть, используются ли |
| 6 | `pg_class` + `pg_stats` | Статистика по колонкам, min/max/most_common |
| 7 | `pg_buffercache` | Какие страницы в shared_buffers |
| 8 | `pg_locks` | Кто кого блокирует |
| 9 | `pg_settings` + `EXPLAIN` | Влияние `work_mem`, `random_page_cost` |
| 10 | `auto_explain` (расширение) | Логирует планы запросов > N ms |

### `auto_explain` — для продакшена

```sql
CREATE EXTENSION auto_explain;

SET auto_explain.log_min_duration = '100ms';
SET auto_explain.log_analyze = on;
SET auto_explain.log_buffers = on;
```

В лог PostgreSQL начнут попадать полные планы всех запросов, которые длились > 100ms. Это **золото** для продакшена: не нужно ждать, пока кто-то скажет "запрос медленный" — лог покажет.

### `pg_stat_user_indexes`

```sql
SELECT
    indexrelname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE idx_scan = 0
ORDER BY pg_relation_size(indexrelid) DESC
LIMIT 20;
```

Индексы, которые **никогда** не использовались — кандидаты на удаление. Они тратят место и замедляют `INSERT`/`UPDATE`.

---

## Оптимизация: приёмы

### 1. Индексы

| Тип | Когда |
|-----|-------|
| B-tree | Equality, range, ORDER BY (default) |
| Partial | Большая часть строк не нужна (`WHERE active = true`) |
| Covering (`INCLUDE`) | Избежать heap access |
| Functional | `LOWER(email)`, `JSONB ->>` |
| GIN | Full-text, JSONB, arrays |
| GiST | Геометрия, ranges, EXCLUDE |
| BRIN | Аппенди-только таблицы, отсортированные по колонке |

**BRIN** — недооценён: для таблиц с 100M+ строк, отсортированных по `created_at`, BRIN-индекс занимает **1KB** вместо 2GB B-tree, и работает почти так же.

```sql
CREATE INDEX idx_events_created_brin
    ON events USING brin (created_at);
```

### 2. `work_mem` / `maintenance_work_mem`

```sql
-- На уровне сервера
work_mem = 64MB
maintenance_work_mem = 512MB
```

### 3. `random_page_cost`

```sql
-- HDD: default 4.0 (правильно)
-- SSD: 1.1–1.5 (индексы становятся дешевле)
-- NVMe: 1.0

SET random_page_cost = 1.1;  -- для SSD
```

Если `random_page_cost` слишком высокий — план-оценщик будет предпочитать Seq Scan, даже когда Index Scan быстрее.

### 4. Материализованные представления

```sql
-- Для тяжёлых агрегаций
CREATE MATERIALIZED VIEW mv_customer_orders AS
SELECT
    customer_id,
    COUNT(*) AS order_count,
    SUM(total_amount) AS total_spent,
    MAX(created_at) AS last_order
FROM orders
GROUP BY customer_id;

CREATE UNIQUE INDEX idx_mv_customer ON mv_customer_orders (customer_id);

-- Обновление
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_customer_orders;
```

### 5. Partitioning

```sql
-- По диапазону времени
CREATE TABLE events (
    id BIGINT,
    created_at TIMESTAMP NOT NULL
) PARTITION BY RANGE (created_at);

CREATE TABLE events_2024_q1 PARTITION OF events
    FOR VALUES FROM ('2024-01-01') TO ('2024-04-01');

CREATE TABLE events_2024_q2 PARTITION OF events
    FOR VALUES FROM ('2024-04-01') TO ('2024-07-01');
```

**Преимущества:**
- Partition pruning: `WHERE created_at IN (...)` → сканируется только нужная партиция.
- `DROP` вместо `DELETE`: `DROP TABLE events_2022_q1;` — мгновенно.
- Параллельный `VACUUM`/`ANALYZE` на партициях.

**Ограничения:**
- PK/FK должны включать partition key.
- `CREATE INDEX` создаётся на каждой партиции (медленно для 100+ партиций).
- `JOINS` через партиции — не так быстро, как одна таблица.

### 6. `SET LOCAL` в транзакции

```sql
BEGIN;
SET LOCAL work_mem = '256MB';
SET LOCAL statement_timeout = '30s';
-- тяжёлый запрос
COMMIT;
-- параметры вернулись к дефолту
```

---

## Антипаттерны

| Антипаттерн | Почему плохо |
|-------------|-------------|
| `SELECT *` | Wide rows, heap access, нет covering |
| Функция в `WHERE` на индексируемой колонке | Индекс не используется |
| `LIKE '%pattern'` | Не использует B-tree (нужен GIN/pg_trgm) |
| `OR` в `WHERE` на разных колонках | Plan-оценщик не может использовать один индекс |
| `CROSS JOIN` без `ON` | Декартово произведение |
| `DELETE` без `WHERE` (или с `WHERE 1=1`) | Lock на всю таблицу, bloat |
| `TRUNCATE` в транзакции с другими таблицами | Lock escalation |
| `VACUUM FULL` в продакшене | Lock on exclusive, rewrite всей таблицы |
| Отключить `autovacuum` для "производительности" | Bloat → всё медленнее |
| `EXPLAIN` без `ANALYZE` | Только оценка, не фактическое время |
| `EXPLAIN` в продакшене | Выполняет запрос (если `ANALYZE`) — может быть дорого |

---

## Чеклист при диагностике

```
1. pg_stat_statements → какой запрос?
2. EXPLAIN (ANALYZE, BUFFERS) → какой план?
3. Seq Scan? → нужен индекс?
4. Sort Method: external merge? → work_mem?
5. Rows estimate vs actual? → ANALYZE?
6. pg_stat_activity → lock?
7. pg_stat_user_tables → bloat? VACUUM?
8. pg_stat_user_indexes → неиспользуемые индексы?
9. N+1 на уровне приложения?
10. auto_explain в логах → есть ли "тихие" медленные запросы?
```

---

## Итог

Медленный запрос — это почти никогда "PostgreSQL медленный". Это:

- Нет индекса (60%)
- Плохая статистика (15%)
- N+1 / частые вызовы (10%)
- Lock / bloat (5%)
- `work_mem` / параметры (5%)
- Прочее (5%)

И процесс всегда один: **найти запрос → посмотреть план → понять, почему план такой → исправить причину (индекс, статистика, архитектура)**. Не "попробовать индекс" — а **понять**, почему план-оценщик выбрал именно этот план.

> ⏱ 51.9s · prompt 56778 tok · output 5465 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегат в DDD: определение, критерии выбора границ, пример с объяснением и типовая ошибка. Это продолжение серии вопросов о DDD/архитектуре, которые уже были в диалоге (агрегаты, Value Objects, транзакции, N+1). Нужно дать глубокий, практический ответ без воды.

Структура:
1. Что такое агрегат — определение через инварианты и транзакционную границу
2. Как определяю границы — конкретные критерии
3. Пример с объяснением (продолжу линию с Order, но лучше взять другой пример или углубить)
4. Типовая ошибка — и её последствия

Ключевые моменты:
- Агрегат = граница транзакционной консистентности
- Не "граница класса" и не "граница сущности"
- Инварианты — главное
- "Маленький лучше большого" — Eric Evans
- Типовая ошибка: слишком большой агрегат (God Aggregate) или слишком маленький (распыление инвариантов)

Возьму пример: Order + Payment + Inventory + Shipment. Показываю, почему Payment не в агрегате Order, а Inventory — отдельный агрегат. И типовая ошибка — "Order всё включает".

Отвечаю на русском, как и весь диалог.

</details>

**Кандидат:**

## Что такое агрегат

Агрегат — это **граница транзакционной консистентности**. Внутри агрегата инварианты должны быть неукоснительно соблюдены **в рамках одной транзакции**. Вне агрегата — согласованность может быть **событийной** (eventual consistency).

Это не "группа связанных сущностей". Это не "таблица + связанные таблицы". Это **граница, внутри которой вы гарантируете, что состояние системы останется валидным после любого изменения**.

Ключевое следствие: **агрегат — это единица, которую вы грузите, мутируете и сохраняете целиком**.

---

## Как определяю границы

Иду от инвариантов, а не от структуры БД.

### Критерий 1: какие инварианты должны быть соблюдены **в одной транзакции**?

| Вопрос | Если да | Если нет |
|--------|---------|----------|
| Могу ли я изменить A, и это **немедленно** делает B невалидным? | B внутри агрегата A | B в другом агрегате |
| Нужно ли, чтобы изменение A **не видело** никто, пока не завершится транзакция? | Внутри | ВEventual |
| Если B упадёт, нужно ли **откатить** A? | Внутри | ВEventual |

### Критерий 2: кто **владеет** жизненным циклом?

- `OrderItem` не существует без `Order` → внутри агрегата `Order`.
- `Payment` может существовать без конкретного `Order` (предоплата, кредит) → отдельный агрегат.
- `Inventory` используется многими `Order` → отдельный агрегат.

### Критерий 3: можно ли мутировать B **не трогая** A?

- Можно ли изменить `OrderItem` не трогая `Order.totalAmount`? Нет → внутри.
- Можно ли изменить `Inventory.reserved` не трогая `Order.status`? Да (другой поток) → отдельно.

### Критерий 4: размер

**Агрегат должен быть достаточно маленьким, чтобы его можно было загрузить целиком в одну транзакцию без deadlock'а.**

Если агрегат содержит 10 сущностей, каждая с 50 дочерними → транзакция держит 500+ строк в lock'е. Это не масштабируется.

---

## Пример: B2B-заказ с оплатой и инвентарём

### Домен

```
Клиент размещает заказ (Order)
  → в заказе позиции (OrderItem)
  → заказ оплачивается (Payment)
  → инвентарь резервируется (InventoryReservation)
  → заказ отправляется (Shipment)
```

### Инварианты

| Инвариант | Где |
|-----------|-----|
| `Order.total = sum(OrderItem.lineTotal)` | Должно быть **атомарно** при изменении позиций |
| `Order.status` может быть изменён только из допустимого набора | **Атомарно** |
| `Payment.amount` не может быть больше `Order.total` | Может быть **eventual**: сначала payment, потом проверка |
| `Inventory.reserved` не может быть < 0 | **Атомарно** внутри Inventory, но **eventual** относительно Order |
| `Shipment.tracking` заполнен только если `Order.status = shipped` | **Eventual** |

### Границы агрегатов

```
Aggregate: Order
  ├── Order (root)
  └── OrderItem[] (child)

Aggregate: Payment
  └── Payment (root)
      └── PaymentTransaction[] (child)

Aggregate: Inventory
  ├── InventoryItem (root)
  └── InventoryReservation[] (child)

Aggregate: Shipment
  └── Shipment (root)
      └── ShipmentPackage[] (child)
```

### Почему `Payment` **не** внутри `Order`

1. **Жизненный цикл:** `Payment` может существовать до `Order` (предоплата) или после (возврат). `Order` может быть отменён, а `Payment` — нет (возврат идёт отдельно).
2. **Владелец:** `Payment` создаётся платёжным сервисом, не заказным.
3. **Конкуренция:** два заказа могут использовать один `Payment` (split payment). Если `Payment` внутри `Order` — два агрегата "владеют" одним объектом → нарушение.
4. **Транзакция:** при создании `Order` не нужно блокировать `Payment`. Согласованность — через событие: `OrderConfirmed` → `PaymentService` проверяет, что достаточно средств.

### Почему `Inventory` **не** внутри `Order`

1. **Одна позиция инвентаря используется многими заказами.** Если `InventoryReservation` внутри `Order` — изменение `InventoryItem.reserved` требует изменения **каждого** связанного `Order`. Это N транзакций.
2. **Конкуренция:** два заказа одновременно резервируют последнюю единицу. Если `Inventory` внутри `Order` — deadlock (два агрегата блокируют одну строку). Если `Inventory` — отдельный агрегат → одна транзакция `Inventory.reserve()` → winner-takes-all.
3. **Размер:** `Inventory` — 10 000 SKU. `Order` — 5 позиций. Если объединить → агрегат на 10 005 сущностей.

### Связь между агрегатами — через ID

```php
class Order
{
    private string $id;
    private Collection $items;
    private ?string $paymentId;   // ← ссылка, не навигация
    private ?string $shipmentId;  // ← ссылка, не навигация
}

class Payment
{
    private string $id;
    private string $orderId;      // ← ссылка
    // НЕТ навигации на Order
}
```

**Критическое правило:** агрегаты ссылаются друг на друга **по ID**, а не по объекту. Навигация `order->payment->items` **не допускается** — это ломает границу.

```php
// ❌ НЕ ДЕЛАТЬ:
#[ORM\ManyToOne(targetEntity: Payment::class)]
private ?Payment $payment;

// ✅ ДЕЛАТЬ:
#[ORM\Column(type: Types::UUID)]
private ?Uuid $paymentId;
```

### Как работает процесс

```
1. OrderService:
   BEGIN
   order = Order.create(customer, items)
   order.confirm()
   em->flush()
   COMMIT
   → dispatch OrderConfirmedEvent(orderId)

2. PaymentService (отдельная транзакция):
   handle(OrderConfirmedEvent):
     payment = Payment.create(orderId, amount)
     gateway.charge(payment)
     em->flush()
   → dispatch PaymentCompletedEvent(paymentId)

3. InventoryService (отдельная транзакция):
   handle(OrderConfirmedEvent):
     for each orderItem:
       inventory.reserve(itemId, quantity)
     em->flush()
   → если не хватает: dispatch InventoryInsufficientEvent
   → OrderService получает → order.cancel()

4. ShipmentService (отдельная транзакция):
   handle(PaymentCompletedEvent):
     shipment = Shipment.create(orderId)
     carrier.createLabel(shipment)
     em->flush()
   → dispatch ShipmentCreatedEvent(shipmentId)
```

Каждый агрегат — **своя транзакция**. Согласованность — через события. Если шаг 2 упал — заказ подтверждён, но не оплачен. Это **допустимое** состояние (ожидаем оплату).

---

## Типовая ошибка: "God Aggregate"

### Симптом

```php
class Order
{
    private Collection $items;
    private Payment $payment;
    private Collection $reservations;   // Inventory
    private Shipment $shipment;
    private Notification $notification;
    private AuditLog $auditLog;
}
```

Все сущности в **одном** агрегате. Одна транзакция. Одна блокировка.

### Почему это ломается

| Проблема | Последствие |
|----------|-------------|
| **Deadlock** | Запрос A: `Order → Payment`. Запрос B: `Payment → Order`. MySQL/PostgreSQL: deadlock. |
| **Lock contention** | `Inventory` заблокирован на 5s, пока `Order` шлёт email. 50 параллельных заказов → очередь. |
| **Размер транзакции** | 10 сущностей × 50 children = 500 строк в lock'е. WAL-журнал разрастается. |
| **Невозможность масштабирования** | Cannot split: "Order" и "Payment" в разных командах → они "владеют" одним объектом. |
| **Нарушение инкапсуляции** | `Payment` знает про `OrderItem`, `Shipment` знает про `Notification` — всё связано. |
| **Сложность тестирования** | Unit-тест `Order` требует моков `Payment`, `Inventory`, `Shipment`, `Notification`. |

### Как это возникает

Обычно так:

1. Начали с `Order + OrderItem` — ок.
2. Добавили `Payment` — "удобно, не нужно ходить в другой сервис".
3. Добавили `Inventory` — "а то рассинхронизация".
4. Добавили `Shipment` — "а то не знаем трекинг".
5. Через 6 месяцев: `Order` — 500 строк, 7 сущностей, 3 deadlock'а в неделю.

### Признаки, что агрегат стал слишком большим

| Признак | Почему |
|---------|--------|
| Транзакция > 1s | Держит слишком много lock'ов |
| Deadlock между двумя запросами | Два агрегата "перепутали" порядок lock'ов |
| Нужно заблокировать 10+ строк на `UPDATE` | Слишком широкий lock-scope |
| Разные команды хотят менять одну сущность | Граница не совпадает с командной |
| Сущность имеет `id`, который **никто** не использует для навигации | Она "паразитирует" в агрегате |
| Тест агрегата требует 5+ mock'ов | Слишком много ответственности |

---

## Вторая типовая ошибка: "слишком маленькие агрегаты"

### Симптом

```php
class Order { ... }
class OrderItem { ... }    // отдельный "агрегат"
class OrderStatus { ... }  // отдельный "агрегат"
class OrderTotal { ... }   // отдельный "агрегат"
```

Каждый — "своя сущность", свой репозиторий.

### Почему это ломается

1. **Нарушение инвариантов:** `OrderItem.quantity` можно изменить, не пересчитывая `Order.total`. Никто не гарантирует, что это произойдёт.
2. **Race condition:** `Order` и `OrderItem` обновляются в разных транзакциях → промежуточное состояние невалидно.
3. **Нет владельца:** кто отвечает за консистентность? Никто.

### Правило

**Если инвариант затрагивает две сущности, они должны быть в одном агрегате.**

```
"Order.total = sum(items)"
→ Order и OrderItem — один агрегат.

"Payment.amount >= Order.total"
→ Это инвариант МЕЖДУ агрегатами. Обеспечивается событием, а не транзакцией.
```

---

## Практический алгоритм

```
1. Выпиши инварианты домена.
   "Order.total = sum(items)"
   "Payment.amount > 0"
   "Inventory.reserved >= 0"

2. Для каждой пары сущностей спроси:
   "Если я изменю A, должен ли B быть немедленно валидным?"
   Да → один агрегат.
   Нет → разные агрегаты.

3. Проверь размер:
   Можно ли загрузить весь агрегат в одну транзакцию < 100ms?
   Да → ок.
   Нет → разбей.

4. Проверь конкуренцию:
   Два разных сценария могут изменять этот агрегат одновременно?
   Если да → убедись, что lock-scope приемлем.

5. Проверь команды:
   Одна команда отвечает за этот агрегат?
   Если нет → вероятно, нужно разбить по командам.
```

---

## Итог

| Вопрос | Ответ |
|--------|-------|
| Что такое агрегат? | Граница транзакционной консистентности |
| Как определить границу? | Через инварианты, а не через структуру БД |
| Что внутри? | Корень + дочерние, которые не могут существовать без корня |
| Что снаружи? | Всё, что имеет собственный жизненный цикл или конкурентный доступ |
| Как связаны? | Через ID + события, а не через навигацию |
| Главный риск слишком большого? | Deadlock, lock contention, невозможность масштабирования |
| Главный риск слишком маленького? | Нарушение инвариантов, race condition |

**Агрегат — это не "сущность". Это "обещание": "после этой транзакции система будет валидной".** Чем больше объектов вы обещаете защитить одной транзакцией, тем дороже это стоит и тем выше риск, что обещание будет нарушено.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Что произойдёт "по умолчанию"

Без какой-либо защиты — **lost update** (потерянное обновление).

### Сценарий

```
t=0  Order.total = 100, version = 1

Процесс A:                    Процесс B:
  читает order (total=100)
  total = total + 50          читает order (total=100)
  → total = 150               total = total - 20
                              → total = 80
  UPDATE orders SET total=150 WHERE id=1
                              UPDATE orders SET total=80 WHERE id=1

Результат: total = 80
Ожидалось: total = 130 (100 + 50 - 20)
```

Оба процесса "успешно" завершили работу. Никто не получил ошибку. Но один результат потерялся.

### Почему это происходит

PostgreSQL по умолчанию использует **MVCC** (Multi-Version Concurrency Control):

1. Оба процесса читают **разные версии** строки (каждый видит snapshot на момент своего `BEGIN`).
2. Оба пишут **свои** версии.
3. Последняя запись "выигрывает" — без какого-либо уведомления первого.

Это **не баг**. Это дизайн: MVCC оптимизирован для **чтения**, а не для **конкурентной записи**.

---

## Защита: три уровня

| Уровень | Механизм | Когда |
|---------|----------|-------|
| 1. Optimistic locking | `version` колонка | 90% случаев |
| 2. Pessimistic locking | `SELECT ... FOR UPDATE` | Когда потеря обновления недопустима и retry неприемлем |
| 3. Saga / idempotency | Event-driven | Распределённые системы, длинные транзакции |

---

## Уровень 1: Optimistic Locking (основной)

### Принцип

Каждая модифицируемая сущность несёт `version`. При `UPDATE` — `WHERE version = <прочитанное>`. Если строка уже изменена — `UPDATE` затронет 0 строк → ошибка.

### В Doctrine

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;

    // ...
}
```

`#[ORM\Version]` делает следующее:

1. При `INSERT` — `version = 1`.
2. При `UPDATE` — Doctrine генерирует:

```sql
UPDATE orders
SET total_amount = 150, version = 2
WHERE id = 1 AND version = 1;
```

3. Если `rowcount = 0` — Doctrine бросает `OptimisticLockException`.

### Практика

```php
final class AddItemToOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {}

    public function execute(int $orderId, int $quantity, int $unitPrice): void
    {
        try {
            $this->em->transactional(function (EntityManagerInterface $em): void {
                $order = $this->orders->findById($orderId);

                if ($order === null) {
                    throw new \DomainException('Order not found');
                }

                $order->addItem($quantity, $unitPrice);

                $em->flush();  // ← здесь UPDATE ... WHERE version = 1
            });
        } catch (OptimisticLockException) {
            // Конкурентное изменение. Что делать?
            throw new ConflictException('Order was modified by another process');
        }
    }
}
```

### На уровне API

```php
// Controller
public function addItem(
    Request $request,
    AddItemToOrderUseCase $useCase,
): Response
{
    try {
        $useCase->execute($orderId, $quantity, $unitPrice);
    } catch (ConflictException) {
        return new Response(
            json_encode(['error' => 'Conflict: order was modified']),
            409,
            ['Content-Type' => 'application/json']
        );
    }

    return new Response(status: 200);
}
```

Клиент получает `409 Conflict` → перечитывает ресурс → применяет изменение повторно → retry.

### Почему optimistic — основной подход

| Плюс | Минус |
|------|-------|
| Нет блокировок во время чтения. | Нужно retry при конфликте. |
| Нет deadlock'а. | Конфликт возможен (но редок при правильном дизайне). |
| Масштабируется: N читателей + 1 писатель. | Не подходит для "долгих" транзакций (version устареет). |
| Прозрачен для БД. | Нужно обрабатывать `OptimisticLockException`. |

### Когда optimistic не хватает

- Конфликт **очень частый** (> 10% запросов). → Пессимистичный lock.
- Транзакция **долгая** (> 5s) — version устареет. → Saga.
- Нужно **гарантировать** порядок. → Pessimistic lock.

---

## Уровень 2: Pessimistic Locking

### Принцип

`SELECT ... FOR UPDATE` — блокирует строку на время транзакции. Другие процессы **ждут**.

### В Doctrine

```php
final class DeductInventoryUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function execute(int $productId, int $quantity): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            // Pessimistic lock: блокирует строку
            $product = $em->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameter('id', $productId)
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)  // ← SELECT ... FOR UPDATE
                ->getQuery()
                ->getOneOrNullResult();

            if ($product === null) {
                throw new \DomainException('Product not found');
            }

            if ($product->stock() < $quantity) {
                throw new InsufficientStockException($productId, $quantity);
            }

            $product->deductStock($quantity);

            $em->flush();
        });
    }
}
```

### Что происходит

```
Процесс A: BEGIN; SELECT ... FOR UPDATE (блокирует)
Процесс B: BEGIN; SELECT ... FOR UPDATE → ЖДЁТ (до A.COMMIT)
Процесс A: UPDATE ...; COMMIT;
Процесс B: SELECT ... (теперь видит новую версию) → UPDATE ...; COMMIT;
```

### Когда использовать

| Сценарий | Почему |
|----------|--------|
| Инвентарь: `stock -= quantity` | Нельзя допустить отрицательный остаток. Retry не подходит (пользователь не будет ждать). |
| Банк: `balance -= amount` | Аналогично. |
| Тикеты: `seats -= 1` | "Первый пришёл — первый забрал". |

### Когда **не** использовать

| Сценарий | Почему |
|----------|--------|
| Длинная транзакция (> 1s) | Блокировка держится долго → очередь → timeout. |
| Высокий параллелизм (100+ писателей на одну строку) | Contention → throughput падает. |
| Чтение > 90% операций | Зачем блокировать, если почти никто не пишет? |

### Lock modes в Doctrine

| Mode | SQL | Эффект |
|------|-----|--------|
| `PESSIMISTIC_READ` | `FOR SHARE` | Блокирует другие `FOR UPDATE`, но допускает другие `FOR SHARE` |
| `PESSIMISTIC_WRITE` | `FOR UPDATE` | Блокирует всё |

**Практическое правило:** если пишете — `PESSIMISTIC_WRITE`. Если только читаете, но хотите избежать "phantom reads" — `PESSIMISTIC_READ`.

### Limitation: `FOR UPDATE SKIP LOCKED`

Для очередей / воркеров:

```sql
SELECT * FROM jobs
WHERE status = 'pending'
ORDER BY created_at
LIMIT 1
FOR UPDATE SKIP LOCKED;
```

"Забери следующий свободный, не жди того, кто уже занят." Это не блокировка в смысле "ожидай", а "пропусти".

В Doctrine нет нативного `SKIP LOCKED` — нужно raw SQL.

---

## Уровень 3: Saga + Idempotency (распределённый случай)

### Когда нужен

- Два агрегата в **разных** БД / микросервисах.
- Транзакция **длинная** (> 5s) — ожидает внешний сервис.
- Нужно **откатить** цепочку действий, если одно упало.

### Пример

```
1. Order.confirm()         → COMMIT
2. Payment.charge()        → COMMIT (внешний сервис, 3s)
3. Inventory.reserve()     → FAIL (не хватает)
4. Order.cancel()          → COMMIT (компенсация)
5. Payment.refund()        → COMMIT (компенсация)
```

Каждый шаг — **своя транзакция**. Согласованность — через компенсацию, а не через rollback.

### Idempotency

Повторная доставка события (retry, redelivery) не должна создавать дубль.

```php
class PaymentService
{
    public function __construct(
        private PaymentRepository $payments,
        private IdempotencyKeyRepo $keys,
        private PaymentGateway $gateway,
    ) {}

    public function charge(string $orderId, Money $amount): Payment
    {
        // Idempotency key: orderId + amount
        $key = sha1($orderId . $amount->amount() . $amount->currency());

        // Проверка: уже выполнялось?
        if ($this->keys->exists($key)) {
            return $this->payments->findOrCreateByOrderId($orderId);
        }

        // Записать ключ ДО выполнения (защита от дублей при retry)
        $this->keys->record($key, 'pending');

        try {
            $result = $this->gateway->charge($orderId, $amount);
            $payment = $this->payments->create($orderId, $amount, $result);
            $this->keys->markCompleted($key, $payment->id());
            return $payment;
        } catch (\Throwable $e) {
            $this->keys->markFailed($key);
            throw $e;
        }
    }
}
```

### Почему idempotency важна

```
t=0  PaymentService → gateway.charge() → COMMIT
t=1  Worker упал. Событие не помечено "processed".
t=2  Redelivery: PaymentService → gateway.charge() → ДУБЛЬ!
```

Без idempotency — двойной платёж. С idempotency — "уже сделано, верни результат".

---

## Сравнение подходов

| | Optimistic | Pessimistic | Saga |
|---|:---:|:---:|:---:|
| Блокировка во время чтения | Нет | Нет | Нет |
| Блокировка во время записи | Нет (проверка при UPDATE) | Да (FOR UPDATE) | Нет |
| Deadlock | Нет | Да (порядок lock'ов) | Нет |
| Конфликт | Retry | Ожидание | Компенсация |
| Масштабируемость | Высокая | Низкая (contention) | Высокая |
| Сложность | Низкая | Средняя | Высокая |
| Когда | 90% случаев | Инвентарь, баланс | Распределённые |

---

## Практические детали

### 1. `#[ORM\Version]` — где ставить

- На **корне агрегата**, а не на дочерних.
- Одна `version` на агрегат, не на каждую сущность.

```php
// ✅
#[ORM\Entity]
class Order
{
    #[ORM\Version]
    private int $version;

    #[ORM\OneToMany]
    private Collection $items;
}

// ❌ Не нужно:
#[ORM\Entity]
class OrderItem
{
    #[ORM\Version]
    private int $version;  // избыточно, если Order уже version'ed
}
```

### 2. `version` и `flush()`

```php
// ❌ Опасно:
$order = $repo->find(1);
$order->addItem(...);
$em->flush();  // version = 1 → 2

// ... код ...

$order->removeItem(...);
$em->flush();  // version = 2 → 3

// Если между двумя flush'ами другой процесс изменил order
// → второй flush() бросит OptimisticLockException.
// Это ПРАВИЛЬНО. Но если вы ожидаете "оба изменения применятся" — нет.
```

**Правило:** один `flush()` на бизнес-операцию. Если нужно два изменения — два use case, две транзакции.

### 3. `version` и `partial update`

```php
// ❌ Непредсказуемо:
$qb = $em->createQueryBuilder();
$qb
    ->update(Order::class, 'o')
    ->set('o.totalAmount', 150)
    ->where('o.id = :id')
    ->setParameter('id', 1);

$qb->getQuery()->execute();
// → version НЕ обновляется! Optimistic lock не сработает.
```

**Правило:** bulk `UPDATE` через DQL **обходит** optimistic locking. Если нужен version — используйте entity-level update.

### 4. Retry с exponential backoff

```php
final class RetryStrategy
{
    public function withRetry(int $maxAttempts, callable $fn): mixed
    {
        $delay = 50; // ms

        for ($i = 1; $i <= $maxAttempts; $i++) {
            try {
                return $fn();
            } catch (OptimisticLockException) {
                if ($i === $maxAttempts) {
                    throw new ConflictException('Too many conflicts');
                }

                usleep($delay * 1000);
                $delay = min($delay * 2, 5000); // cap: 5s
            }
        }
    }
}

// Использование:
$this->retry->withRetry(3, function () use ($useCase) {
    $useCase->execute($orderId, $qty, $price);
});
```

### 5. Не путайте "конфликт" с "ошибкой"

```php
// ❌
catch (OptimisticLockException $e) {
    log("ERROR: conflict on order {$orderId}");
    throw new \RuntimeException('Internal error');
}

// ✅
catch (OptimisticLockException) {
    // Это НЕ ошибка. Это нормальный сценарий.
    throw new ConflictException('Order was modified. Please refresh and retry.');
}
```

На уровне API — `409 Conflict`. Не `500`.

---

## Антипаттерны

| Антипаттерн | Почему плохо |
|-------------|-------------|
| `SELECT FOR UPDATE` на всю транзакцию с внешним API | Блокировка на 5–10s → очередь → timeout |
| `retry` без backoff | Thundering herd: 100 процессов одновременно retry |
| `retry` на "несовместимые" изменения | `total += 50` и `total = 0` — retry не поможет |
| Optimistic lock на read-only сущности | Нет смысла, замедляет |
| `version` на каждой дочерней сущности | Избыточно, усложняет |
| Pessimistic lock + optimistic lock одновременно | Двойная защита не нужна, если одна работает |
| "А у нас один инстанс, concurrency нет" | Один инстанс ≠ один процесс. Worker, cron, CLI, web — все конкуренты. |

---

## Итоговая рекомендация

```
1. По умолчанию: Optimistic Locking (#[ORM\Version])
   → 90% случаев, простая, масштабируется.

2. Если потеря обновления недопустима И retry неприемлем:
   Pessimistic Locking (FOR UPDATE)
   → инвентарь, баланс, тикеты.
   → транзакция < 1s.

3. Если агрегаты в разных БД / сервисах:
   Saga + Idempotency
   → компенсация вместо rollback.

4. Всегда:
   → 409 Conflict на API-уровне.
   → Idempotency key для внешних вызовов.
   → Экспоненциальный backoff при retry.
```

**Главный принцип:** конкурентная запись — это **ожидаемый сценарий**, а не исключительное. Проектируйте так, чтобы "конфликт" был нормальным путём, а не крахом.

> ⏱ 36.1s · prompt 62044 tok · output 3425 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

The user is asking about domain events in a practical sense: when to publish, how to handle, what problems they solve and create, and the relationship between domain events and DB transactions. This is a deep architectural question that follows naturally from the previous discussions about aggregates, transactions, and concurrency.

Let me structure this around:
1. What domain events are (and aren't)
2. When to use them vs. direct calls
3. The transactional boundary — the critical "publish before commit" vs "publish after commit" problem
4. Practical implementation in PHP/Symfony
5. Problems they solve
6. Problems they create
7. Anti-patterns
8. The Outbox pattern as the bridge between domain events and DB transactions

I should be concrete, show code, and be honest about trade-offs. I've been building up a consistent narrative throughout this conversation — aggregates, transactions, concurrency, DDD. This should fit naturally.

Key points to cover:
- Domain events are "something happened" (past tense), not "do something" (imperative)
- They decouple aggregates that can't be in the same transaction
- The critical problem: WHERE in the transaction do you publish?
- "Before commit" = event lost if transaction rolls back
- "After commit" = event published but transaction not yet committed (or committed but consumer processes before all data visible)
- Outbox pattern as the solution
- In-memory vs. persistent event stores
- Synchronous vs. asynchronous dispatch
- Event naming conventions (past tense)
- Event payload design (immutable, minimal)
- The difference between domain events and integration events
- Anti-patterns: events as "magic glue", events for things that should be direct calls, events that carry mutable state

Let me write a thorough, practical answer with code examples.

</details>

**Кандидат:**

## Что такое доменное событие

Доменное событие — это **констатация факта**: "что-то произошло в домене".

```
UserRegistered        — пользователь зарегистрирован
OrderConfirmed        — заказ подтверждён
PaymentFailed         — платёж не прошёл
```

Это **не** команда. Команда — "сделай X". Событие — "X случилось".

Разница критична:

| Команда | Событие |
|---------|---------|
| `SendEmail(user)` | `UserRegistered` |
| `UpdateIndex(user)` | `UserRegistered` |
| Исполнитель знает, **что** делать | Исполнитель решает, **что с этим делать** |
| Отправитель зависит от исполнителей | Отправитель не знает, кто реагирует |

---

## Когда использовать

### Нужно событие

| Сценарий | Почему |
|----------|--------|
| 2+ агрегата реагируют на одно изменение | Нельзя в одной транзакции (разные границы) |
| Реакция **не обязательна** для корректности | Пользователь зарегистрирован — это ок, даже если email не ушёл |
| Реакция **асинхронная** (email, index, notification) | Не должно блокировать ответ |
| Разные команды / сервисы | Не могут быть в одной транзакции |
| Нужно **аудитировать** факт | "Кто и когда подтвердил заказ" |

### Не нужно событие

| Сценарий | Почему |
|----------|--------|
| 1 агрегат, 1 реакция | Прямой вызов. Событие — ceremony. |
| Инвариант внутри агрегата | `Order.total = sum(items)` — это не событие, это правило |
| Синхронная зависность | "Не могу продолжить, пока не узнаю результат" — это не событие, это вызов |
| "Просто чтобы было" | Событие — не панацея от "много классов" |

### Правило

> Если реакцию можно сделать **в той же транзакции** и она **обязательна** для корректности — это не событие. Это часть бизнес-логики.

> Если реакция **может отстать** и **не ломает инвариант** — это событие.

---

## Где публикуем: транзакционная граница

Это **самый важный** вопрос. И самый часто ошибочный.

### Три варианта

```
A) До commit:
   BEGIN
   INSERT order
   INSERT outbox          ← событие в БД
   PUBLISH event           ← в очередь
   COMMIT

B) После commit:
   BEGIN
   INSERT order
   COMMIT
   PUBLISH event           ← в очередь (после!)

C) Outbox:
   BEGIN
   INSERT order
   INSERT outbox           ← событие в БД (в той же транзакции)
   COMMIT
   [отдельный процесс: PUBLISH из outbox → очередь]
```

### Вариант A: до commit

```php
$em->transactional(function () {
    $order = Order::create(...);
    $order->confirm();
    $em->flush();

    $this->events->dispatch(new OrderConfirmed($order->id()));
    // ↑ PUBLISH в очередь (RabbitMQ) — ДО COMMIT
});
```

**Проблема:** если `COMMIT` упадёт (disk full, dead lock) — событие **уже** отправлено, а данные **не закоммичены**. Потребитель увидит `OrderConfirmed` для заказа, которого нет в БД.

**Вердикт:** небезопасно. Не использовать.

### Вариант B: после commit

```php
$order = Order::create(...);
$order->confirm();

$em->transactional(function () use ($order) {
    $em->persist($order);
    $em->flush();
});
// ↑ COMMIT пройден

$this->events->dispatch(new OrderConfirmed($order->id()));
// ↑ PUBLISH после COMMIT
```

**Проблема:** если процесс **упадёт** между `COMMIT` и `PUBLISH`:
- Данные закоммичены.
- Событие **потеряно**.
- Никогда не будет отправлено.

Это **at-most-once** доставка. Для email — допустимо (можно переслать). Для биллинга — нет.

**Вердикт:** допустимо для не-критичных реакций. Не для финансовых операций.

### Вариант C: Outbox (рекомендуемый)

```php
$em->transactional(function (EntityManagerInterface $em) {
    $order = Order::create(...);
    $order->confirm();
    $em->persist($order);

    // Событие в БД — в той же транзакции
    $event = new OrderConfirmed(
        aggregateId: $order->id(),
        occurredAt: new \DateTimeImmutable(),
        payload: ['total' => $order->total()->amount()],
    );
    $em->persist($event);  // INSERT INTO outbox

    $em->flush();
    // ↑ ATOMIC: order + event. Либо оба, либо ни одного.
});

// COMMIT пройден. Событие в БД.

// Отдельный процесс (long-running или cron):
// SELECT * FROM outbox WHERE published_at IS NULL ORDER BY id LIMIT 100 FOR UPDATE SKIP LOCKED
// → PUBLISH в RabbitMQ
// → UPDATE outbox SET published_at = now()
```

**Почему это правильно:**

| Гарантия | Как обеспечивается |
|----------|-------------------|
| Событие не потеряно при crash | В БД, атомарно с данными |
| Событие не отправлено при rollback | В той же транзакции |
| Событие не отправлено дважды (по возможности) | `FOR UPDATE SKIP LOCKED` + `published_at` |
| Потребитель не увидит "призрачное" событие | Данные уже закоммичены |

**Цена:**
- Нужен отдельный процесс (publisher).
- Таблица `outbox` растёт → нужен cleanup.
- Задержка: между commit и publish — миллисекунды (не секунды).

### Таблица

| | До commit | После commit | Outbox |
|---|:---:|:---:|:---:|
| Атомарность с данными | ❌ | ❌ | ✅ |
| Нет потери при crash | ❌ | ❌ | ✅ |
| Нет "призрачных" событий | ❌ | ✅ | ✅ |
| Нет дублей | ⚠️ | ✅ | ⚠️ (at-least-once) |
| Сложность | Низкая | Низкая | Средняя |
| Когда | Никогда | Не-критичные | Критичные + любые |

---

## Практическая реализация

### 1. Доменное событие (чистый PHP)

```php
final class OrderConfirmed
{
    private function __construct(
        public readonly int    $orderId,
        public readonly string $occurredAt,
        public readonly array  $payload,
    ) {}

    public static function create(int $orderId, array $payload = []): self
    {
        return new self(
            orderId: $orderId,
            occurredAt: (new \DateTimeImmutable())->format('Y-m-d\TH:i:sP'),
            payload: $payload,
        );
    }

    public function topic(): string
    {
        return 'order.confirmed';
    }
}
```

**Правила:**
- Имя — **прошедшее время**: `OrderConfirmed`, не `ConfirmOrder`.
- `readonly` — событие не мутируется после создания.
- Минимальный payload — только ID + данные, необходимые потребителю. Не весь объект.
- `occurredAt` — время, когда произошло, а не когда будет обработано.

### 2. Домен собирает события

```php
class Order
{
    /** @var list<DomainEvent> */
    private array $pendingEvents = [];

    public function confirm(): void
    {
        if ($this->status !== 'new') {
            throw new \DomainException('Can only confirm a new order');
        }

        if ($this->items->count() === 0) {
            throw new \DomainException('Order must have items');
        }

        $this->status = 'confirmed';

        $this->pendingEvents[] = OrderConfirmed::create(
            orderId: $this->id ?? 0,
            payload: ['total' => $this->totalAmount],
        );
    }

    /** @return list<DomainEvent> */
    public function collectEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }

    public function clearEvents(): void
    {
        $this->pendingEvents = [];
    }
}
```

**Почему домен "помнит" события, а не публикует их сам:**
- Домен не знает про `EventDispatcher`, `RabbitMQ`, `Outbox`.
- Домен **декларирует** факт. Application layer **решает**, что делать.
- Тестирование: `$order->confirm(); assert($order->collectEvents() === [OrderConfirmed]);` — без моков.

### 3. Application Service — точка публикации

```php
final class ConfirmOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
        private OutboxRepository $outbox,
    ) {}

    public function execute(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em): void {
            $order = $this->orders->findById($orderId);

            if ($order === null) {
                throw new \DomainException('Order not found');
            }

            $order->confirm();  // домен собирает события

            // События → outbox (в той же транзакции)
            foreach ($order->collectEvents() as $event) {
                $this->outbox->record($event);
            }

            $em->flush();
            // ↑ ATOMIC: order.status + outbox. Либо оба, либо ни одного.
        });
        // ↑ COMMIT
    }
}
```

### 4. Outbox

```php
#[ORM\Entity]
class OutboxEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $topic;

    #[ORM\Column(type: Types::JSON)]
    private array $payload;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $occurredAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $attempts = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lastError = null;

    public function __construct(string $topic, array $payload, \DateTimeImmutable $occurredAt)
    {
        $this->topic = $topic;
        $this->payload = $payload;
        $this->occurredAt = $occurredAt;
    }
}
```

### 5. Publisher (отдельный процесс)

```php
#!/usr/bin/env php
// bin/console outbox:publish

final class OutboxPublisher
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessagePublisher $publisher,  // RabbitMQ / Redis
    ) {}

    public function tick(int $batchSize = 100): int
    {
        $published = 0;

        $this->em->transactional(function (EntityManagerInterface $em) use ($batchSize, &$published): void {
            /** @var OutboxEvent[] $events */
            $events = $em->createQueryBuilder()
                ->select('e')
                ->from(OutboxEvent::class, 'e')
                ->where('e.publishedAt IS NULL')
                ->andWhere('e.attempts < 5')
                ->orderBy('e.id', 'ASC')
                ->setMaxResults($batchSize)
                ->getQuery()
                ->getResult();

            foreach ($events as $event) {
                try {
                    $this->publisher->publish(
                        topic: $event->topic,
                        payload: $event->payload,
                    );

                    $event->markPublished();
                    $published++;
                } catch (\Throwable $e) {
                    $event->markFailed($e->getMessage());
                }
            }

            $em->flush();
        });

        return $published;
    }
}
```

Запускается как long-running process или cron:

```bash
# Long-running (каждые 500ms)
while true; do
    php bin/console outbox:publish --batch=100
    sleep 0.5
done

# Или cron каждую минуту
* * * * * php /app/bin/console outbox:publish --batch=500
```

### 6. Consumer (потребитель)

```php
final class SendWelcomeEmailConsumer
{
    public function __construct(
        private Mailer $mailer,
        private UserRepo $users,
        private EmailLogRepo $log,
    ) {}

    public function handle(array $payload): void
    {
        $userId = $payload['userId'];

        // Идемпотентность
        if ($this->log->exists(userId: $userId, type: 'welcome')) {
            return;
        }

        $user = $this->users->find($userId);
        if ($user === null) {
            return;
        }

        $this->mailer->sendWelcome($user);
        $this->log->record($userId, 'welcome', new \DateTimeImmutable());
    }
}
```

---

## Что решает

| Проблема | Как решает |
|----------|-----------|
| Развязка агрегатов | Агрегаты не знают друг о друге |
| Асинхронность | Ответ не блокируется |
| Устойчивость к сбоев | Outbox + retry |
| Расширяемость | Новый потребитель — новый класс, без изменения издателя |
| Аудит | `outbox` — журнал всех произошедших событий |
| Ретраи | `attempts`, `lastError` — видно, что упало |

---

## Что создаёт

| Проблема | Почему |
|----------|--------|
| **Сложность отладки** | "Почему email не ушёл?" → нужно проверять: outbox → publisher → queue → consumer. 5 точек отказа. |
| **Задержка** | Между событием и реакцией — миллисекунды. Для UI — заметно. |
| **Нет гарантии порядка** | `OrderConfirmed` и `OrderCancelled` могут быть обработаны в любом порядке. |
| **Нет гарантии транзакционности** | Потребитель не может "откатить" событие. Если `SendEmail` упало — `UpdateIndex` уже прошло. |
| **Дубли** | At-least-once доставка → дубли. Нужна идемпотентность на каждом потребителе. |
| **Скрытые зависимости** | Код читается: "OrderConfirmed → что?" → нужно искать consumer. |
| **Сложность тестирования** | Интеграционный тест: outbox + publisher + queue + consumer. |
| **Мониторинг** | Нужно отслеживать: queue depth, consumer lag, outbox backlog. |

---

## Антипаттерны

### 1. Событие для прямой зависимости

```php
// ❌
// "После создания заказа нужно создать Invoice"
// Invoice — часть того же бизнес-процесса, обязательная.

$order = Order::create(...);
$em->persist($order);
$em->flush();

$events->dispatch(new OrderCreated($order->id()));
// → Consumer: CreateInvoice
// → Consumer: SendEmail
// → Consumer: UpdateIndex

// Это не "расширяемость". Это 3 обязательных шага, которые
// разнесены по 3 разным процессам. Если Consumer 2 упал —
// invoice не создан. А это критично.

// ✅
$em->transactional(function () {
    $order = Order::create(...);
    $invoice = Invoice::forOrder($order);
    $em->persist($order);
    $em->persist($invoice);
    $em->flush();
});
// Synchronous, atomic, predictable.
```

**Правило:** если реакция **обязательна** и **синхронна** — это не событие. Это часть use case.

### 2. Событие несёт весь объект

```php
// ❌
final class OrderConfirmed
{
    public readonly Order $order;  // ← весь агрегат в событии
}

// ✅
final class OrderConfirmed
{
    public readonly int $orderId;  // ← только ID + минимальный payload
    public readonly int $total;
    public readonly string $currency;
}
```

**Почему:**
- Событие живёт в очереди — может быть задержано на минуты.
- Если `Order` изменится к моменту обработки — потребитель получит устаревшие данные.
- Потребитель **должен** перечитать агрегат из БД, а не доверять payload'у.

### 3. Событие как "магическая связка"

```php
// ❌
class UserService
{
    public function register(...): void
    {
        // ...
        $events->dispatch(new UserRegistered(...));
        // ...
        $events->dispatch(new UserPointsGranted(...));
        $events->dispatch(new UserSegmentAssigned(...));
        $events->dispatch(new UserEmailVerified(...));
    }
}
```

4 события из одного метода. Каждый "независимый". Но на деле — это **один** бизнес-процесс, разрезанный на 4 асинхронных шага. Если один упал — состояние непоследовательное.

**Правило:** если 3+ события из одного use case и они **взаимозависимы** — это не события, это **один** атомарный блок.

### 4. Consumer меняет доменное состояние

```php
// ❌
class HandleOrderConfirmed
{
    public function handle(OrderConfirmed $event): void
    {
        $order = $this->repo->find($event->orderId);
        $order->markNotified();  // ← mutation через событие
        $this->repo->save($order);
    }
}
```

Событие — **факт**, не **команда**. Если consumer мутирует состояние — он **зависим** от того, что произошло **до** него. Это скрытая секвенциальная зависимость.

**Правило:** consumer **читает** состояние (через ID), **не мутирует** то, что не должен. Если нужна мутация — это отдельный use case, не consumer.

### 5. Событие без идемпотентности

```php
// ❌
class HandleOrderConfirmed
{
    public function handle(OrderConfirmed $event): void
    {
        $this->mailer->sendWelcome(...);  // при redelivery — дубль!
    }
}

// ✅
public function handle(OrderConfirmed $event): void
{
    $key = 'welcome_' . $event->orderId;

    if ($this->log->exists($key)) {
        return;  // уже обработано
    }

    $this->mailer->sendWelcome(...);
    $this->log->record($key);
}
```

### 6. "А я просто dispatch'ну, а там посмотрим"

```php
// ❌
public function process(...): void
{
    // 50 строк логики
    $events->dispatch(new SomethingHappened(...));
    // ...
    $events->dispatch(new AnotherThingHappened(...));
    // ...
}
// Через 6 месяцев: 15 событий, 12 consumer'ов,
// никто не понимает, что происходит и в каком порядке.
```

---

## Связь с транзакциями: сводка

```
┌─────────────────────────────────────────────────────────────┐
│                     TRANSAKCIJA                              │
│                                                             │
│  BEGIN                                                      │
│    INSERT order (status='confirmed')                        │
│    INSERT outbox (topic='order.confirmed', payload=...)     │
│  COMMIT                                                     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
                         │
                         ▼
              ┌─────────────────────┐
              │   OUTBOX PUBLISHER  │  (отдельный процесс)
              │   SELECT ... FOR    │
              │   UPDATE SKIP LOCKED│
              │   PUBLISH → RabbitMQ│
              │   UPDATE published_at│
              └─────────────────────┘
                         │
                         ▼
              ┌─────────────────────┐
              │     CONSUMERS       │  (отдельные процессы)
              │  SendEmail          │
              │  UpdateIndex        │
              │  NotifyWarehouse    │
              └─────────────────────┘
```

**Ключевые гарантии:**

| Момент | Гарантия |
|--------|----------|
| `BEGIN → COMMIT` | order + outbox — атомарны. Либо оба, либо ни одного. |
| `COMMIT → PUBLISH` | Событие в БД. Потеряно не будет. |
| `PUBLISH → CONSUME` | At-least-once. Дубли возможны. Идемпотентность на consumer'е. |
| `CONSUME → IDEMPOTENT CHECK` | Дубль → return. Не повторная обработка. |

**Что НЕ гарантируется:**

| | Почему |
|---|---|
| Порядок | `OrderConfirmed` может быть обработан после `OrderCancelled` |
| Транзакционность consumer'ов | Если `SendEmail` прошло, а `UpdateIndex` упало — нет rollback |
| "Exactly once" | Только "at-least-once" + идемпотентность |

---

## Когда **не** использовать доменные события

| Ситуация | Альтернатива |
|----------|-------------|
| 1 агрегат, 1 реакция | Прямой вызов |
| Реакция обязательна и синхронна | Часть use case |
| 2 строки кода, 1 потребитель | Прямой вызов. Событие — ceremony. |
| Нужно гарантировать порядок | Pessimistic lock + синхронный вызов |
| Команда из 1 человека, 1 сервис | Прямой вызов + log. События — оверхед. |

---

## Итог

| Вопрос | Ответ |
|--------|-------|
| Когда публиковать? | Внутри транзакции (outbox), не до и не после |
| Как обрабатывать? | Consumer + идемпотентность + at-least-once |
| Что решает? | Развязка, асинхронность, устойчивость, расширяемость |
| Что создаёт? | Сложность, задержку, дубли, скрытые зависимости |
| Связь с транзакциями? | Outbox = атомарность. Consumer = eventual consistency |
| Главный риск? | Событие вместо прямой зависимости → потеря атомарности там, где она нужна |

**Золотое правило:** доменное событие — это **"что-то произошло"**, а не **"сделай что-то"**. Если вы используете его как команду в платье события — вы получили сложность событий без выгоды событий.

> ⏱ 55.3s · prompt 69289 tok · output 5910 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанных вопроса по DDD:
1. Чем VO отличается от Entity
2. Как решать, что VO, а что Entity
3. Что такое Ubiquitous Language и как она влияет на код

Это вопросы на глубину понимания DDD. Нужно дать чёткие, практические ответы с примерами кода. Не уходить в абстракции — привязывать к PHP-реализации.

Ключевые моменты:
- VO: идентичность через значение, immutable, value equality, нет жизненного цикла
- Entity: идентичность через ID, mutable (с осторожностью), reference equality, есть жизненный цикл
- Граница: "если я заменю объект на другой с тем же значением — сломается ли что-то?" → VO. "Если я заменю объект с тем же значением, но другим ID — сломается?" → Entity.
- Ubiquitous Language: имена в коде = имена в бизнесе. Не "user", а "customer". Не "item", а "orderItem". Не "process", а "confirmOrder".

Структурирую ответ:
1. Ключевое различие (таблица + примеры)
2. Алгоритм решения (вопросы)
3. Пограничные случаи (что неочевидно)
4. Ubiquitous Language — что это, как влияет на код, примеры до/после
5. Антипаттерны

</details>

**Кандидат:**

## 1. Value Object vs Entity

### Ключевое различие — идентичность

| | Value Object | Entity |
|---|---|---|
| **Идентичность** | По **значению** | По **идентификатору** (ID) |
| **Вопрос** | "Что это?" | "Кто это?" |
| **Равенство** | `$a->equals($b)` или `$a == $b` | `$a->id === $b->id` |
| **Иммутабельность** | Да (по контракту) | Нет (состаривается) |
| **Жизненный цикл** | Нет | Есть (создан → активен → удалён) |
| **Ссылка** | Значение (copy) | Ссылка (reference) |

### Конкретный пример

```php
// VALUE OBJECT: Money
final class Money
{
    private function __construct(
        private readonly int    $amount,
        private readonly string $currency,
    ) {}

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Different currencies');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}

$a = Money::of(100, 'RUB');
$b = Money::of(100, 'RUB');

// a и b — РАЗНЫЕ объекты, но РАВНЫЕ по значению.
// Замена $a на $b — НИЧЕГО не ломает.
// Никто не "наблюдает" за $a.
```

```php
// ENTITY: Customer
class Customer
{
    #[ORM\Id]
    private int $id;

    private string $name;
    private string $email;
    private \DateTimeImmutable $createdAt;
    private Collection $orders;

    public function changeEmail(string $newEmail): void
    {
        $this->assertCanChangeEmail();
        $this->email = $newEmail;
    }

    public function id(): int
    {
        return $this->id;
    }
}

$c1 = $repo->find(42);
$c2 = $repo->find(42);

// c1 и c2 — это ОДИН и тот же объект (по ID).
// Но они РАЗНЫЕ PHP-объекты (если не в одном Unit of Work).
// Замена c1 на c2 — это НЕ то же самое, что замена $a на $b выше.
// Потому что у c1 есть история: $c1->changeEmail() был вызван, а у c2 — нет.
```

### Тест: "заменимость"

Самый надёжный способ определить:

> **Если я заменю объект на другой с тем же значением, но другим PHP-объектом — сломается ли что-то?**

- **Нет** → Value Object.
- **Да** → Entity.

```php
// VO: замена безопасна
$order->setTotal(Money::of(100, 'RUB'));  // объект A
$order->setTotal(Money::of(100, 'RUB'));  // объект B, другое значение
// Результат одинаковый. Никто не "ждёт" объект A.

// Entity: замена ОПАСНА
$cart->addCustomer($customerA);  // ID=42
$cart->addCustomer($customerB);  // ID=43, то же имя, тот же email
// Это ДВА РАЗНЫХ клиента. Замена сломает бизнес-логику.
```

---

## 2. Алгоритм: VO или Entity?

Задаю себе 5 вопросов по порядку. Первый "да" определяет ответ.

```
Вопрос 1: У него есть ID, который используют другие сущности?
  Да → ENTITY
  Нет → Вопрос 2

Вопрос 2: У него есть собственный жизненный цикл
         (создан → изменён → удалён), и этот цикл
         НЕ привязан к родителю?
  Да → ENTITY
  Нет → Вопрос 3

Вопрос 3: Его можно заменить на "такой же" без последствий?
  Да → VALUE OBJECT
  Нет → Вопрос 4

Вопрос 4: Он участвует в сравнении "это тот же самый?"
         (a.id === b.id)?
  Да → ENTITY
  Нет → VALUE OBJECT

Вопрос 5: Он мутирует со временем, и эта мутация
         имеет значение для истории/аудита?
  Да → ENTITY
  Нет → VALUE OBJECT
```

### Применение к конкретным сущностям

| Сущность | ID? | Свой жизненный цикл? | Заменим? | Мутирует? | Вердикт |
|----------|:---:|:---:|:---:|:---:|---------|
| `Money` | Нет | Нет | Да | Нет | **VO** |
| `Address` | Нет | Нет | Да | Нет | **VO** |
| `Email` | Нет | Нет | Да | Нет | **VO** |
| `DateRange` | Нет | Нет | Да | Нет | **VO** |
| `PhoneNumber` | Нет | Нет | Да | Нет | **VO** |
| `Customer` | Да | Да | Нет | Да | **Entity** |
| `Order` | Да | Да | Нет | Да | **Entity** |
| `OrderItem` | Да | Нет (живёт внутри Order) | Нет | Да | **Entity** (child) |
| `OrderStatus` | Нет | Нет | Да | Нет | **Enum** (VO-подобный) |
| `Invoice` | Да | Да | Нет | Да | **Entity** (отдельный агрегат) |
| `PaymentMethod` | Нет | Нет | Да | Нет | **VO** |
| `AuditLogEntry` | Да | Да | Нет | Нет (append-only) | **Entity** |

### Пограничные случаи

#### Case 1: `Product`

```
Product:
  - id
  - name
  - price
  - stock
  - createdAt
  - updatedAt
```

- Есть ID → Entity.
- Есть собственный жизненный цикл (создан, изменён, удалён) → Entity.
- Заменим? `Product(id=5, name="Хлеб")` и `Product(id=6, name="Хлеб")` — это разные продукты (разные цены, разный остаток). → Entity.

**Вердикт: Entity.**

#### Case 2: `ProductVariant`

```
ProductVariant:
  - size
  - color
  - price
```

- Нет ID (или ID — производный: `productId + size + color`).
- Нет собственного жизненного цикла — живёт внутри `Product`.
- Заменим? `Variant(size="M", color="red")` == `Variant(size="M", color="red")` — да, это одно и то же.

**Вердикт: Value Object** (или child Entity, если нужен отдельный lifecycle).

Практически: если `Variant` хранится как отдельная строка в БД и на него есть FK — лучше child Entity. Если это составной атрибут `Product` — VO.

#### Case 3: `Session` / `AccessToken`

```
Session:
  - id
  - userId
  - token
  - createdAt
  - expiresAt
  - ip
```

- Есть ID → Entity.
- Свой жизненный цикл (создана → исчерпана → удалена) → Entity.
- Заменим? Нет, каждый токен уникален.

**Вердикт: Entity.**

Но `Token` сам по себе (строка) — это **VO** внутри `Session`.

```php
// Token — VO
final class Token
{
    private function __construct(private readonly string $value)
    {
        if (strlen($value) < 16) {
            throw new \DomainException('Token too short');
        }
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(32)));
    }

    public function value(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}

// Session — Entity
class Session
{
    #[ORM\Id]
    private int $id;

    private Token $token;  // ← VO внутри Entity

    public function markExpired(): void
    {
        $this->status = 'expired';
    }
}
```

#### Case 4: `OrderStatus`

Это **enum**, а не VO и не Entity.

```php
enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
```

Enum — это **примитивный VO**. Он:
- Нет ID.
- Нет жизненного цикла.
- Заменим (по значению).
- Не мутирует.

Но он не `final class` с методами — он **конечный набор значений**.

---

## 3. Ubiquitous Language

### Что это

Ubiquitous Language (Универсальный язык) — это **единый набор терминов**, который используют:

- бизнес-аналитик в документе,
- разработчик в коде,
- QA в тест-кейсах,
- DBA в схемах БД,
- DevOps в мониторинге.

**Один и тот же термин = одно и то же понятие. Нет синонимов.**

### Почему это важно

Без Ubiquitous Language в коде появляются **три имени для одного понятия**:

```
user / account / customer      — одно и то же?
item / product / sku           — одно и то же?
amount / price / total         — одно и то же?
```

Через 6 месяцев никто не понимает, что `item` в `OrderItem` — это `product` в `InventoryItem` — это `sku` в `Catalog`.

### Как влияет на код

#### До: без Ubiquitous Language

```php
class User
{
    public function __construct(
        public string $email,
        public string $password,
        public array $permissions,
        public float $balance,
        public array $items,
    ) {}

    public function doSomething(): void
    {
        // "doSomething" — это что? подтвердить заказ? авторизоваться?
    }

    public function process($data): void
    {
        // "process" — что именно?
    }

    public function getStuff(): array
    {
        // "stuff" — что?
    }
}
```

Проблемы:

| Слово | Проблема |
|-------|----------|
| `User` | Это покупатель? Админ? Гость? Все сразу? |
| `doSomething` | Не даёт ни одной информации |
| `process` | Универсальное слово — не говорит ничего |
| `getStuff` | "Stuff" — это всё, что угодно |
| `$balance` | Это деньги? Очки лояльности? Кредитный лимит? |
| `$items` | Товары? Позиции заказа? Файлы? |

### Как выглядит с Ubiquitous Language

**Шаг 1: Глоссарий домена** (в `docs/ubiquitous-language.md`)

```
Термин              | Значение
--------------------|------------------------------------------
Customer            | Покупатель, который размещает заказы
Order               | Документ на покупку товаров
OrderItem           | Одна строка в заказе (товар + количество)
Product             | Торговое наименование в каталоге
SKU                 | Уникальный артикул товара
Money               | Сумма в конкретной валюте (копейки + код)
Payment             | Факт оплаты заказа
Shipment            | Отправка заказа курьером/почтой
```

**Шаг 2: Код на этом языке**

```php
final class Customer
{
    private readonly Uuid $id;
    private readonly CustomerName $name;
    private readonly Email $email;
    private readonly Collection $orders;

    public function placeOrder(OrderDraft $draft): Order
    {
        $this->assertIsVerified();
        $this->assertIsNotBlacklisted();

        $order = Order::createFrom($this, $draft);
        $this->orders->add($order);

        return $order;
    }

    public function id(): Uuid
    {
        return $this->id;
    }
}

final class Order
{
    private readonly Uuid $id;
    private readonly Customer $customer;
    private readonly Collection $items;
    private readonly Money $total;
    private readonly OrderStatus $status;
    private readonly \DateTimeImmutable $placedAt;

    private function __construct(
        Uuid $id,
        Customer $customer,
        Collection $items,
        Money $total,
        OrderStatus $status,
        \DateTimeImmutable $placedAt,
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->items = $items;
        $this->total = $total;
        $this->status = $status;
        $this->placedAt = $placedAt;
    }

    public static function createFrom(Customer $customer, OrderDraft $draft): self
    {
        // ...
    }

    public function confirm(): void
    {
        $this->assertStatusIs(OrderStatus::New);
        $this->assertHasItems();

        $this->status = OrderStatus::Confirmed;
    }

    public function cancel(CancellationReason $reason): void
    {
        $this->assertStatusIsIn([OrderStatus::New, OrderStatus::Confirmed]);

        $this->status = OrderStatus::Cancelled;
        $this->cancellationReason = $reason;
    }

    public function id(): Uuid { return $this->id; }
    public function customer(): Customer { return $this->customer; }
    public function items(): Collection { return $this->items; }
    public function total(): Money { return $this->total; }
    public function status(): OrderStatus { return $this->status; }
}

final class OrderItem
{
    private readonly Uuid $id;
    private readonly Product $product;
    private readonly int $quantity;
    private readonly Money $unitPrice;

    public function lineTotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
```

### Что изменилось

| Было | Стало | Почему лучше |
|------|-------|-------------|
| `User` | `Customer` | Ясно: это покупатель, не админ |
| `doSomething()` | `placeOrder()` | Ясно: создаёт заказ |
| `process($data)` | `confirm()` | Ясно: меняет статус |
| `getStuff()` | `items()` | Ясно: возвращает позиции |
| `$balance` | `Money $total` | Ясно: сумма в валюте |
| `$items` (array) | `Collection $items` (OrderItem[]) | Ясно: тип элементов |
| `$data` | `OrderDraft $draft` | Ясно: входной параметр |

### Практические правила Ubiquitous Language

#### 1. Имена методов — глаголы домена

```php
// ❌
$order->set('status', 'confirmed');
$order->update(['status' => 'confirmed']);
$order->save();

// ✅
$order->confirm();
$order->cancel(Reason::customerRequested());
$order->markAsShipped(tracking: '1Z...');
```

#### 2. Имена параметров — сущности домена

```php
// ❌
function process($id, $amount, $type) { ... }

// ✅
function placeOrder(Customer $customer, OrderDraft $draft): Order { ... }
```

#### 3. Имена исключений — доменные термины

```php
// ❌
throw new \RuntimeException('Error 4001');
throw new \Exception('something went wrong');

// ✅
throw new InsufficientStockException($productId, $requested, $available);
throw new OrderAlreadyCancelledException($orderId);
throw new CustomerBlacklistedException($customerId);
```

#### 4. Имена таблиц БД — термины домена

```sql
-- ❌
CREATE TABLE users (...);
CREATE TABLE items (...);
CREATE TABLE stuff (...);

-- ✅
CREATE TABLE customers (...);
CREATE TABLE orders (...);
CREATE TABLE order_items (...);
CREATE TABLE products (...);
```

#### 5. Один термин — одно понятие

```
❌ "amount" используется и для цены товара, и для суммы заказа, и для баланса.
✅ "unitPrice" — цена за единицу.
   "total" — сумма заказа.
   "balance" — баланс аккаунта.
```

#### 6. Документация — глоссарий

```markdown
# Ubiquitous Language

## Customer
Покупатель. Размещает заказы, получает уведомления.
- Не путать с `User` (аутентификация) и `StaffMember` (сотрудник).

## Order
Документ на покупку. Состоит из позиций.
- Статусы: New → Confirmed → Shipped → Delivered
- Или: New/Confirmed → Cancelled

## Money
Сумма в конкретной валюте. Хранится в минорных единицах (копейки, центы).
- Не float! Всегда int + currency code.

## Product
Товар в каталоге. Имеет SKU, название, цену.
- Не путать с `OrderItem` (позиция в заказе).
```

---

## 4. Как Ubiquitous Language влияет на архитектуру

### Модули = термины

```
app/
├── Domain/
│   ├── Customer/
│   │   ├── Customer.php
│   │   ├── CustomerName.php
│   │   ├── Email.php
│   │   └── CustomerBlacklist.php
│   │
│   ├── Order/
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatus.php
│   │   ├── Money.php
│   │   └── CancellationReason.php
│   │
│   ├── Product/
│   │   ├── Product.php
│   │   ├── Sku.php
│   │   └── Price.php
│   │
│   └── Payment/
│       ├── Payment.php
│       ├── PaymentStatus.php
│       └── PaymentGateway.php
│
├── Application/
│   ├── PlaceOrderUseCase.php
│   ├── ConfirmOrderUseCase.php
│   └── CancelOrderUseCase.php
│
└── Infrastructure/
    ├── Doctrine/
    │   ├── CustomerRepository.php
    │   └── OrderRepository.php
    ├── Api/
    │   └── CustomerController.php
    └── Events/
        ├── OrderConfirmed.php
        └── PaymentCompleted.php
```

Каждая папка = **один термин** из глоссария. Новичок заходит в репозиторий и по структуре видит домен.

### Тесты на языке домена

```php
final class OrderTest extends TestCase
{
    public function test_order_cannot_be_confirmed_without_items(): void
    {
        $customer = Customer::create('John', 'john@example.com');
        $order = Order::createFrom($customer, OrderDraft::empty());

        $this->expectException(OrderHasNoItemsException::class);

        $order->confirm();
    }

    public function test_cancelling_confirmed_order_requires_reason(): void
    {
        $order = $this->createConfirmedOrder();

        $this->expectException(MissingCancellationReasonException::class);

        $order->cancel(reason: null);
    }

    public function test_total_is_recalculated_when_item_is_removed(): void
    {
        $order = $this->createOrderWithItems(
            line: [
                new OrderItem(product: $this->bread(), quantity: 2, unitPrice: Money::of(100, 'RUB')),
                new OrderItem(product: $this->milk(), quantity: 1, unitPrice: Money::of(150, 'RUB')),
            ],
        );

        $this->assertSame(Money::of(350, 'RUB'), $order->total());

        $order->removeFirstItem();

        $this->assertSame(Money::of(150, 'RUB'), $order->total());
    }
}
```

Имена тестов читаются как **сценарии из бизнес-требований**:
- "заказ без позиций нельзя подтвердить"
- "отмена подтверждённого заказа требует причину"
- "сумма пересчитывается при удалении позиции"

Это и есть Ubiquitous Language: **тест на языке бизнеса, код на языке бизнеса, требования на языке бизнеса**.

---

## 5. Антипаттерны

### 1. "Универсальные" имена

```php
// ❌
class Model { ... }
class Manager { ... }
class Handler { ... }
class Service { ... }
class Util { ... }
```

Эти имена **не несут информации**. Они есть в каждом проекте. Они говорят: "здесь что-то есть, но я не знаю что".

**Правило:** если имя можно использовать в **любом** проекте — оно плохое.

### 2. Синтонимы в одном проекте

```php
// ❌
$user = $customer->toUser();
$account = $user->getAccount();
$profile = $account->getProfile();
// Это одно и то же? Четыре имени — одно понятие.
```

**Правило:** один термин из глоссария = одна сущность в коде. Если нужно "преобразовать" — это `Customer::toApiDto()`, а не `Customer → User → Account → Profile`.

### 3. Глаголы-пустышки

```php
// ❌
$order->handle(...);
$order->execute(...);
$order->process(...);
$order->run(...);
```

**Правило:** метод должен называться **тем, что он делает в домене**. `confirm()`, `cancel()`, `addOrderItem()`, `recalculateTotal()`.

### 4. Переменная `$data` / `$result`

```php
// ❌
$data = $request->getContent();
$result = $service->process($data);
$response = $formatter->format($result);
```

**Правило:** каждая переменная — **конкретный объект домена**.

```php
// ✅
$orderDraft = OrderDraft::fromRequest($request);
$order = $placeOrderUseCase->execute($customer, $orderDraft);
$apiResponse = OrderApiResponse::fromDomain($order);
```

---

## 6. Итог

| Вопрос | Ответ |
|--------|-------|
| Чем VO отличается от Entity? | VO — идентичность по значению, immutable. Entity — идентичность по ID, мутирует. |
| Как решить? | "Заменим ли мы объект на другой с тем же значением?" Да → VO. Нет → Entity. |
| Что такое Ubiquitous Language? | Единый набор терминов: бизнес, код, тесты, БД — говорят на одном языке. |
| Как она влияет на код? | Имена классов, методов, параметров, исключений, таблиц — из глоссария. |
| Главный враг? | Универсальные имена (`Model`, `Manager`, `process`, `$data`). |

**Тест Ubiquitous Language:** покажите код бизнес-аналитику. Если он узнаёт свои термины — язык работает. Если видит `Model::process($data)` — язык сломан.

> ⏱ 57.7s · prompt 74871 tok · output 5911 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про структуру PHP-приложения по чистой архитектуре. Это продолжение серии вопросов на собеседовании, где я играю роль PHP-бэкенд-разработчика. Нужно ответить детально, с примерами кода, показать слои, правило зависимостей, конкретные места для Doctrine, Symfony-контроллера, обработчика очереди, и инструменты контроля границ (PHPStan, arch-test, composer packages).

Структура ответа:
1. Слои и что в каждом лежит
2. Правило зависимостей (стрелки только внутрь)
3. Конкретные примеры: где Doctrine, где контроллер, где consumer
4. Как следить за границами (PHPStan rules, custom rules, composer packages, ArchUnit-подобные инструменты)
5. Антипаттерны и честные оговорки (когда чистая архитектура overkill)

Я должен быть честен: в PHP/ Symfony чистая архитектура — это не "6 слоёв как в книге", а 4-5 уровней. Symfony — это сам по себе внешний слой (presentation + infrastructure). Нужно показать, как Symfony встраивается в чистую архитектуру, а не против неё.

Начну с краткого описания слоёв, потом правило зависимостей, потом конкретика.

</details>

**Кандидат:**

## Слои

Я использую 4 слоя, а не 6 из книги. Для PHP/Symfony 6 слоёв — ceremony, 4 — достаточно:

```
┌─────────────────────────────────────────────────────────────┐
│  INTERFACE (Presentation / Driving Adapters)                 │
│  Контроллеры, DTO-для API, команды, обработчики очередей,   │
│  CLI-команды, GraphQL-резолверы                              │
├─────────────────────────────────────────────────────────────┤
│  APPLICATION (Use Cases)                                      │
│  Use-case-классы, оркестрация, транзакции,                  │
│  маппинг Domain ↔ DTO                                        │
├─────────────────────────────────────────────────────────────┤
│  DOMAIN                                                        │
│  Сущности, VO, доменные сервисы, события,                   │
│  политики, интерфейсы репозиториев                            │
├─────────────────────────────────────────────────────────────┤
│  INFRASTRUCTURE (Driven Adapters)                             │
│  Doctrine, HTTP-клиенты, мейлер, брокер очереди,            │
│  файловое хранилище, кэш                                      │
└─────────────────────────────────────────────────────────────┘
```

### Правило зависимостей

**Стрелки только внутрь.** Внешний слой может зависеть от внутреннего. Внутренний слой **не может** зависеть от внешнего.

```
Interface  →  Application  →  Domain
                              ←  Infrastructure
```

| Кто | Может знать про | Не может знать про |
|-----|-----------------|-------------------|
| Domain | Никого. Чистый PHP. | Symfony, Doctrine, HTTP, БД |
| Application | Domain | Symfony-контроллеры, Doctrine |
| Infrastructure | Domain (интерфейсы) | Application, Interface |
| Interface | Application, Domain | Infrastructure (прямо) |

### Что в каждом слое

#### Domain

```php
// src/Domain/Order/Order.php
final class Order
{
    private Uuid $id;
    private Customer $customer;
    private Collection $items;
    private Money $total;
    private OrderStatus $status;

    public function confirm(): void { /* ... */ }
    public function cancel(CancellationReason $reason): void { /* ... */ }
    public function total(): Money { /* ... */ }
}

// src/Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
}

// src/Domain/Order/Money.php
final class Money
{
    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {}

    public static function of(int $amount, string $currency): self { /* ... */ }
    public function add(self $other): self { /* ... */ }
}

// src/Domain/Order/OrderRepository.php  ← ИНТЕРФЕЙС
interface OrderRepository
{
    public function save(Order $order): void;
    public function findById(Uuid $id): ?Order;
    public function remove(Order $order): void;
}

// src/Domain/Order/DomainEvent.php
interface DomainEvent {}

// src/Domain/Order/OrderConfirmed.php
final class OrderConfirmed implements DomainEvent
{
    public function __construct(
        public readonly Uuid $orderId,
        public readonly Money $total,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}

// src/Domain/Order/OrderPolicy.php
final class OrderPolicy
{
    public function canConfirm(Order $order, Customer $customer): bool { /* ... */ }
    public function canCancel(Order $order, Customer $customer): bool { /* ... */ }
}
```

**Что здесь есть:**
- Сущности, VO, enum'ы.
- Доменные события (чистый PHP, не Symfony Event).
- **Интерфейсы** репозиториев (зависимости, которые домен декларирует).
- Политики (бизнес-правила, которые не привязаны к конкретной сущности).

**Чего здесь НЕТ:**
- `#[ORM\Entity]`, `#[Route]`, `#[MessageHandler]`.
- `EntityManager`, `HttpClient`, `MailerInterface`.
- `Request`, `Response`, `JsonResponse`.
- `App\` namespace (можно, но лучше `Domain\`).

---

#### Application

```php
// src/Application/Order/ConfirmOrderUseCase.php
final class ConfirmOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private OrderPolicy $policy,
        private EventPublisher $events,
    ) {}

    public function execute(Uuid $orderId, Uuid $customerId): void
    {
        $order = $this->orders->findById($orderId);
        if ($order === null) {
            throw OrderNotFoundException::with($orderId);
        }

        $customer = /* получить из OrderRepository или CustomerRepository */;

        if (!$this->policy->canConfirm($order, $customer)) {
            throw OrderCannotBeConfirmedException::with($orderId, $customer->id());
        }

        $order->confirm();

        $this->orders->save($order);
        $this->events->publish($order->collectEvents());
    }
}

// src/Application/Order/PlaceOrderUseCase.php
final class PlaceOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private CustomerRepository $customers,
        private ProductRepository $products,
        private OrderPolicy $policy,
        private EventPublisher $events,
    ) {}

    public function execute(PlaceOrderCommand $cmd): PlaceOrderResult
    {
        $customer = $this->customers->findById($cmd->customerId());
        // ...
        $order = Order::createFrom($customer, $draft);
        $this->orders->save($order);
        $this->events->publish($order->collectEvents());

        return PlaceOrderResult::success($order->id(), $order->total());
    }
}

// src/Application/Order/PlaceOrderCommand.php
final class PlaceOrderCommand
{
    public function __construct(
        private readonly Uuid $customerId,
        private readonly array $lines,
        private readonly Uuid $idempotencyKey,
    ) {}

    public static function fromRequest(array $data): self { /* ... */ }
    public function customerId(): Uuid { return $this->customerId; }
    public function lines(): array { return $this->lines; }
    public function idempotencyKey(): Uuid { return $this->idempotencyKey; }
}

// src/Application/Order/PlaceOrderResult.php
final class PlaceOrderResult
{
    private function __construct(
        private readonly Uuid $orderId,
        private readonly Money $total,
    ) {}

    public static function success(Uuid $orderId, Money $total): self { /* ... */ }
    public function orderId(): Uuid { return $this->orderId; }
    public function total(): Money { return $this->total; }
}
```

**Что здесь есть:**
- Use-case-классы (один класс = один сценарий).
- Команды и результаты (DTO, не доменные объекты).
- Оркестрация: загрузить → проверить политику → мутировать → сохранить → опубликовать события.
- Транзакционная граница (через `EventPublisher`, который сам управляет транзакцией).

**Чего здесь НЕТ:**
- `Request`, `Response` (это Interface).
- `EntityManager` напрямую (через репозитории из Domain).
- `MailerInterface`, `HttpClient` (это Infrastructure).
- Business-логики (в Domain).

---

#### Interface (Presentation)

```php
// src/Interface/Http/OrderController.php
final class OrderController
{
    public function __construct(
        private ConfirmOrderUseCase $confirmOrder,
        private PlaceOrderUseCase $placeOrder,
    ) {}

    #[Route('/orders/{id}/confirm', methods: ['POST'])]
    public function confirm(
        #[Uuid] Uuid $id,
        #[CurrentUser] Customer $customer,
    ): JsonResponse
    {
        try {
            $this->confirmOrder->execute($id, $customer->id());
        } catch (OrderCannotBeConfirmedException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'code' => 'ORDER_CANNOT_BE_CONFIRMED'],
                409,
            );
        } catch (OrderNotFoundException $e) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        return new JsonResponse(null, 204);
    }

    #[Route('/orders', methods: ['POST'])]
    public function place(
        #[CurrentUser] Customer $customer,
        Request $request,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $cmd = PlaceOrderCommand::fromRequest($data + ['customerId' => $customer->id()]);
        } catch (InvalidPlaceOrderCommandException $e) {
            return new JsonResponse(['errors' => $e->errors()], 422);
        }

        $result = $this->placeOrder->execute($cmd);

        return new JsonResponse([
            'orderId' => $result->orderId()->toRfc4122(),
            'total' => $result->total()->amount(),
            'currency' => $result->total()->currency(),
        ], 201);
    }
}

// src/Interface/Console/ProcessOutboxCommand.php
final class ProcessOutboxCommand extends Command
{
    #[Command(name: 'outbox:process')]
    public function __construct(
        private OutboxProcessor $processor,
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processed = $this->processor->processBatch(100);
        $output->writeln(sprintf("Processed %d events", $processed));
        return Command::SUCCESS;
    }
}

// src/Interface/Messenger/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private SendWelcomeEmailUseCase $useCase,
    ) {}

    public function __invoke(UserRegisteredMessage $message): void
    {
        $this->useCase->execute($message->userId());
    }
}
```

**Что здесь есть:**
- Контроллеры (HTTP-адаптеры).
- CLI-команды (console-адаптеры).
- Обработчики очередей (messenger-адаптеры).
- Маппинг: Domain-исключение → HTTP-код. Domain-результат → JSON.

**Чего здесь НЕТ:**
- Бизнес-логики.
- Прямых вызовов `EntityManager`.
- Доменных правил (они в Domain).

---

#### Infrastructure

```php
// src/Infrastructure/Doctrine/OrderRepository.php
final class OrderRepository implements \Domain\Order\OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
    }

    public function findById(Uuid $id): ?Order
    {
        $entity = $this->em->getRepository(OrderEntity::class)
            ->findOneBy(['id' => $id->toRfc4122()]);

        return $entity?->toDomain();
    }

    public function remove(Order $order): void
    {
        $entity = new OrderEntity();
        $entity->setDomain($order);
        $this->em->remove($entity);
        $this->em->flush();
    }
}

// src/Infrastructure/Doctrine/Entities/OrderEntity.php
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::UUID)]
    private string $id;

    #[ORM\Column(type: Types::UUID)]
    private string $customerId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $status;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItemEntity::class,
        cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;

    public function toDomain(): Order { /* маппинг Entity → Domain */ }
    public function setDomain(Order $order): void { /* маппинг Domain → Entity */ }
}

// src/Infrastructure/Messenger/SendWelcomeEmailMessage.php
final class SendWelcomeEmailMessage
{
    public function __construct(public readonly Uuid $userId) {}
}

// src/Infrastructure/Http/HttpClientInterface.php
interface HttpClientInterface
{
    public function get(string $url): string;
    public function post(string $url, array $body): string;
}

// src/Infrastructure/Http/GuzzleHttpClient.php
final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(private ClientInterface $client) {}

    public function get(string $url): string { /* ... */ }
    public function post(string $url, array $body): string { /* ... */ }
}
```

**Что здесь есть:**
- Реализации интерфейсов из Domain.
- Doctrine-сущности (если отдельная от Domain).
- HTTP-клиенты, мейлеры, брокеры.
- Адаптеры для внешних систем.

**Чего здесь НЕТ:**
- Бизнес-логики.
- Контроллеров.
- Use-case'ов.

---

## Куда попадают конкретные вещи

| Компонент | Слой | Почему |
|-----------|------|--------|
| Symfony Controller | Interface | Это HTTP-адаптер. Принимает `Request`, возвращает `Response`. |
| Doctrine Entity | Infrastructure | Это формат хранения. Не домен. |
| Doctrine Repository impl | Infrastructure | Реализация интерфейса из Domain. |
| Domain Repository interface | Domain | Домен декларирует, что ему нужно. |
| `MessageHandler` | Interface | Это адаптер очереди. Принимает message, вызывает use case. |
| `EventSubscriber` (Symfony) | Interface | Адаптер событий Symfony. |
| `DomainEvent` | Domain | Чистый PHP. Не Symfony Event. |
| `EventPublisher` impl | Infrastructure | Публикует в очередь / outbox. |
| `HttpClient` impl | Infrastructure | Адаптер внешней API. |
| Use-case класс | Application | Оркестрация. |
| `Order` (домен) | Domain | Сущность. |
| `OrderEntity` | Infrastructure | Формат хранения. |
| `PlaceOrderCommand` | Application | DTO на входе в use case. |
| `PlaceOrderResult` | Application | DTO на выходе из use case. |
| `Money` (VO) | Domain | Не зависит от слоя. |
| `OrderPolicy` | Domain | Бизнес-правило. |
| `OrderNotFoundException` | Domain | Доменное исключение. |
| `OrderController::confirm()` | Interface | HTTP-адаптер. |
| `ConfirmOrderUseCase::execute()` | Application | Бизнес-оркестрация. |
| `OrderRepository::save()` (impl) | Infrastructure | Сохранение в БД. |

---

## Как следить, что границы не нарушаются

### 1. PHPStan + custom rules (основной инструмент)

```php
// rules/DomainMustNotDependOnExternalRule.php
final class DomainMustNotDependOnExternalRule implements Rule
{
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Class_) {
            return [];
        }

        $className = $scope->getName()?->toString();

        if (str_starts_with($className, 'App\\Domain\\') || str_starts_with($className, 'Domain\\')) {
            // Проверяем все use-импорты и параметры конструктора
            foreach ($node->getUses() as $use) {
                $import = $use->class->toString();

                if ($this->isExternal($import)) {
                    return [
                        RuleErrorBuilder::message(
                            sprintf(
                                'Domain class %s must not depend on external class %s',
                                $className,
                                $import,
                            )
                        )->build()
                    ];
                }
            }

            // Проверяем параметры конструктора
            if ($node->hasMethod('__construct')) {
                foreach ($node->getMethod('__construct')->getParameters() as $param) {
                    $type = $param->type?->toString();

                    if ($type !== null && $this->isExternal($type)) {
                        return [
                            RuleErrorBuilder::message(
                                sprintf(
                                    'Domain class %s constructor must not depend on %s',
                                    $className,
                                    $type,
                                )
                            )->build()
                        ];
                    }
                }
            }
        }

        return [];
    }

    private function isExternal(string $class): bool
    {
        $allowedPrefixes = [
            'Domain\\',
            'App\\Domain\\',
            'App\\Shared\\',  // если есть общие VO
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($class, $prefix)) {
                return false;
            }
        }

        // Допускаем PHP встроенные
        if (in_array($class, ['int', 'string', 'float', 'bool', 'array', 'null', 'self', 'static', 'parent', 'callable', 'iterable', 'object'])) {
            return false;
        }

        return true;
    }
}
```

```neon
# phpstan.neon
includes:
    - rules/DomainMustNotDependOnExternalRule.php

parameters:
    level: 8
    paths:
        - src
```

**Результат:**

```
ERROR: Domain class App\Domain\Order\Order must not depend on external class App\Infrastructure\Doctrine\OrderEntity
  → src/Domain/Order/Order.php:25
```

### 2. Composer packages (сильная изоляция)

Если домен действительно должен быть переиспользуемым:

```
project/
├── domain/          ← composer package, НЕ зависит от Symfony/Doctrine
│   ├── composer.json
│   └── src/
├── app/             ← main app, зависит от domain/
│   ├── composer.json
│   └── src/
└── vendor/
```

```json
// domain/composer.json
{
    "name": "acme/order-domain",
    "require": {
        "php": "^8.2"
    },
    "autoload": {
        "psr-4": {
            "App\\Domain\\": "src/"
        }
    }
}

// app/composer.json
{
    "name": "acme/store",
    "require": {
        "acme/order-domain": "@dev",
        "symfony/framework-bundle": "^7.0",
        "doctrine/orm": "^3.0"
    }
}
```

**Преимущество:** если `domain/` пытается `use Symfony\...` — `composer install` упадёт, потому что Symfony не в `require` домена. Физически невозможно.

**Недостаток:** ceremony. Для 1 команды, 1 проекта — overkill.

### 3. ArchTest / ArchUnit-подобные инструменты

```php
// tests/Architecture/DependencyTest.php
final class DependencyTest extends TestCase
{
    public function test_domain_does_not_depend_on_application(): void
    {
        $this->assertNoDependency(
            from: 'App\\Domain\\',
            to: 'App\\Application\\',
        );
    }

    public function test_domain_does_not_depend_on_infrastructure(): void
    {
        $this->assertNoDependency(
            from: 'App\\Domain\\',
            to: 'App\\Infrastructure\\',
        );
    }

    public function test_domain_does_not_depend_on_interface(): void
    {
        $this->assertNoDependency(
            from: 'App\\Domain\\',
            to: 'App\\Interface\\',
        );
    }

    public function test_application_does_not_depend_on_interface(): void
    {
        $this->assertNoDependency(
            from: 'App\\Application\\',
            to: 'App\\Interface\\',
        );
    }

    private function assertNoDependency(string $from, string $to): void
    {
        $violations = $this->scanDependencies($from, $to);

        $this->assertCount(
            0,
            $violations,
            sprintf('Dependency %s → %s found in: %s', $from, $to, implode(', ', $violations))
        );
    }
}
```

### 4. Code Review checklist

| Вопрос | Ответ |
|--------|-------|
| Есть ли в Domain `#[ORM\]`, `#[Route\]`, `#[MessageHandler\]`? | Нет. |
| Использует ли Domain `EntityManager`, `HttpClient`, `MailerInterface`? | Нет. |
| Есть ли в Application `Request`, `Response`, `JsonResponse`? | Нет. |
| Знает ли Infrastructure про Use-case'ы? | Нет (только Domain-интерфейсы). |
| Знает ли Interface про Doctrine? | Нет (только через Use-case'ы). |
| Есть ли `use Doctrine\ORM\EntityManagerInterface` в `Domain\`? | Нет. |

### 5. Folder structure + PSR-4

```
src/
├── Domain/          ← Domain\ (PSR-4: App\Domain\ → src/Domain/)
├── Application/     ← Application\
├── Interface/       ← Interface\
│   ├── Http/
│   ├── Console/
│   └── Messenger/
└── Infrastructure/  ← Infrastructure\
```

PSR-4 mapping:

```json
"autoload": {
    "psr-4": {
        "App\\Domain\\": "src/Domain/",
        "App\\Application\\": "src/Application/",
        "App\\Interface\\": "src/Interface/",
        "App\\Infrastructure\\": "src/Infrastructure/"
    }
}
```

Это **физически** разделяет код. Нарушение границы = нарушение PSR-4 = `composer dump-autoload` не найдёт класс.

---

## Честные оговорки

### 1. Не всё требует 4 слоя

| Проект | Слои |
|--------|------|
| CRUD-админка на 20 сущностей | Domain + Interface. Application и Infrastructure — избыточны. |
| API на 5 эндпоинтов | Domain + Interface. |
| Биллинг с 50 сущностями | Все 4. |
| Микросервис из 3 сущностей | Domain + Infrastructure. Application — ceremony. |

**Правило:** слои нужны, когда **есть что отделять**. Если бизнес-логика — 2 if-else'а — 4 слоя = 4 файла для 2 строк.

### 2. Symfony — это не "внешний слой"

Symfony — это **фреймворк**, который живёт на стыке Interface и Infrastructure. Контроллеры — Interface. Doctrine — Infrastructure. Messenger — Infrastructure. Security — Interface. Router — Interface.

Symfony **не нарушает** чистую архитектуру. Он **реализует** внешние слои. Domain остаётся чистым.

### 3. `EventPublisher` — где живёт?

- **Интерфейс** `EventPublisher` — в Domain (домен декларирует, что ему нужно).
- **Реализация** `OutboxEventPublisher` — в Infrastructure.
- **Вызов** `$this->events->publish(...)` — в Application.

### 4. Доменные события ≠ Symfony Events

| | Доменное событие | Symfony Event |
|---|---|---|
| Где | Domain | Interface |
| Формат | Чистый PHP class | `Event` + `EventSubscriber` |
| Транспортировка | Outbox / MessageBus | Symfony EventDispatcher |
| Для кого | Business-факт | Framework-внутреннее |

```php
// Domain: чистое событие
final class OrderConfirmed implements DomainEvent
{
    public function __construct(
        public readonly Uuid $orderId,
    ) {}
}

// Interface: маппинг на Symfony
final class OrderConfirmedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    public function onView(ViewEvent $event): void
    {
        // ...
    }
}
```

### 5. Когда чистая архитектура — антипаттерн

| Ситуация | Почему |
|----------|--------|
| 1 разработчик, 3 сущности, 2 endpoints | 4 слоя = 12 файлов для 100 строк кода. |
| Быстрый прототип (1 неделя) | Время на структуру > время на логику. |
| Команда не знает DDD | Слои без понимания = пустые папки. |
| CRUD-админка | Бизнес-логика = "запись в таблицу". Слои не несут ценности. |

**Правило:** чистая архитектура — это **инвестиция**. Она окупается, когда:
- бизнес-логика > 500 строк;
- команда > 3 человека;
- проект живёт > 1 год;
- сущности имеют инварианты, которые нужно защищать.

Если этих условий нет — 2 слоя (Domain + Interface) достаточно.

---

## Итоговая диаграмма

```
┌─────────────────────────────────────────────────────────────────┐
│  INTERFACE                                                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐       │
│  │ Controller   │  │ Command      │  │ MessageHandler   │       │
│  │ (HTTP)       │  │ (Console)    │  │ (Queue)          │       │
│  └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘       │
│         │                  │                    │                │
│         ▼                  ▼                    ▼                │
├─────────────────────────────────────────────────────────────────┤
│  APPLICATION                                                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  ConfirmOrderUseCase   PlaceOrderUseCase   CancelUseCase │   │
│  └──────────────────────────────────────────────────────────┘   │
│         │                  │                    │                │
│         ▼                  ▼                    ▼                │
├─────────────────────────────────────────────────────────────────┤
│  DOMAIN                                                         │
│  ┌──────────┐  ┌──────────┐  ┌───────────────┐  ┌──────────┐  │
│  │ Order    │  │ Money    │  │ OrderPolicy   │  │ OrderRepo│  │
│  │ (Entity) │  │ (VO)     │  │               │  │ (IFACE)  │  │
│  └──────────┘  └──────────┘  └───────────────┘  └─────┬────┘  │
│                                                        │        │
│         ┌──────────────────────────────────────────────┘        │
│         ▼                                                       │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  OrderConfirmed (DomainEvent)                            │   │
│  └──────────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────────┤
│  INFRASTRUCTURE                                                 │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────┐  │
│  │ OrderEntity      │  │ OutboxPublisher  │  │ HttpClient   │  │
│  │ (Doctrine)       │  │ (RabbitMQ)       │  │ (Guzzle)     │  │
│  └──────────────────┘  └──────────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

**Стрелки только вниз.** Domain не знает, что такое Doctrine. Application не знает, что такой `Request`. Infrastructure не знает, что такое `ConfirmOrderUseCase`.

> ⏱ 65.7s · prompt 80539 tok · output 7002 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about testing strategy for a Symfony application with PostgreSQL. This is a continuation of a PHP backend developer interview. I need to cover:

1. Test pyramid (or my variant of it)
2. Types of tests (unit, integration, functional, e2e)
3. What to mock vs what not to mock
4. How to keep tests fast and stable
5. Practical examples with Symfony + PostgreSQL specifics

Let me think about what's genuinely useful here versus generic advice. The user has been getting deep, practical answers throughout this interview. I should:

- Be concrete with Symfony/PHP specifics (PHPUnit, Symfony BrowserKit, Testcontainers, etc.)
- Give real code examples
- Be honest about trade-offs
- Address the "fast and stable" part with concrete techniques
- Connect back to earlier topics (clean architecture, domain events, outbox)
- Not be generic — give the perspective of someone who has maintained test suites in production

Key areas to cover:
1. My test pyramid variant (not the textbook one)
2. Unit tests: what, how, what to mock
3. Integration tests: Doctrine, PostgreSQL (Testcontainers)
4. Functional tests: Symfony BrowserKit
5. E2E: when and how
6. What to mock vs not (this is the most opinionated part)
7. Speed: parallel execution, transactional tests, testcontainers
8. Stability: flaky tests, time-dependent tests, network isolation
9. Practical tips from real projects

Let me structure this well.

</details>

**Кандидат:**

## Пирамида (моя, не книжная)

```
         ▲
        /  \        E2E (5–10 тестов)
       /    \       — критические пользовательские сценарии
      /──────\
     /        \     Functional (20–50 тестов)
    /          \    — контроллер → use case → БД (без mock'ов)
   /────────────\
  /              \  Integration (50–100 тестов)
 /                \ — use case + реальная БД (Testcontainers)
/──────────────────\
/                  \ Unit (200–500 тестов)
/                  \— домен, VO, политики (без фреймворка)
──────────────────────
```

**Пропорции:** ~70% unit, ~25% integration, ~5% e2e.

**Главное отличие от книжной пирамиды:** я не ставлю на вершину "много E2E". У меня E2E — это **5 критических сценариев**, а не "пробежаться по всем страницам".

---

## 1. Unit-тесты (домен)

### Что тестируем

- Value Objects (`Money`, `DateRange`, `Email`)
- Сущности и их инварианты (`Order::confirm()`, `Order::cancel()`)
- Политики (`OrderPolicy`)
- Чистые функции (расчёты, форматирование)

### Что **не** тестируем

- Doctrine-репозитории (это integration).
- Контроллеры (это functional).
- Use-case'ы с реальным `EntityManager` (это integration).

### Пример

```php
final class OrderTest extends TestCase
{
    public function test_cannot_confirm_order_without_items(): void
    {
        $order = Order::create(customer: $this->customer(), items: []);

        $this->expectException(OrderHasNoItemsException::class);

        $order->confirm();
    }

    public function test_cannot_cancel_shipped_order(): void
    {
        $order = Order::create(customer: $this->customer(), items: [$this->item()]);
        $order->confirm();
        $order->markShipped(tracking: '1Z...');

        $this->expectException(OrderCannotBeCancelledException::class);

        $order->cancel(Reason::customerRequested());
    }

    public function test_total_is_recalculated_when_item_is_removed(): void
    {
        $order = Order::create(
            customer: $this->customer(),
            items: [
                new OrderItem(product: $this->product('Bread', 100), quantity: 2),
                new OrderItem(product: $this->product('Milk', 150), quantity: 1),
            ],
        );

        $this->assertSame(Money::of(350, 'RUB'), $order->total());

        $order->removeFirstItem();

        $this->assertSame(Money::of(150, 'RUB'), $order->total());
    }

    public function test_money_addition_with_different_currencies_throws(): void
    {
        $a = Money::of(100, 'RUB');
        $b = Money::of(100, 'USD');

        $this->expectException(CurrencyMismatchException::class);

        $a->add($b);
    }

    private function customer(): Customer
    {
        return Customer::create(name: 'John', email: 'john@example.com');
    }

    private function product(string $name, int $price): Product
    {
        return Product::create(name: $name, price: Money::of($price, 'RUB'));
    }

    private function item(): OrderItem
    {
        return new OrderItem(product: $this->product('Bread', 100), quantity: 1);
    }
}
```

### Что мокаем в unit

**Ничего.** Домен не знает про внешние зависимости. Если домену нужен `ClockInterface` для тестирования time-dependent логики:

```php
interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}

final class SystemClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}

// В тесте:
final class FakeClock implements ClockInterface
{
    public function __construct(private \DateTimeImmutable $frozenTime) {}

    public function now(): \DateTimeImmutable
    {
        return $this->frozenTime;
    }
}
```

Это **не mock** — это **fake**. Fake — это реальная реализация, которая ведёт себя предсказуемо. Mock — это "ожидание вызова" (`$mock->expects($this->once())->method('x')`).

### Правило: в unit-тестах нет mock'ов

| Зависимость | Как тестируем |
|-------------|---------------|
| `ClockInterface` | Fake (замороженное время) |
| `RandomInterface` | Fake (детерминированный seed) |
| `OrderRepository` | **Не мокаем** — это integration |
| `MailerInterface` | **Не мокаем** — это integration/functional |

Если use case принимает `OrderRepository` как интерфейс — это **не unit-тест**, это **integration-тест с mock'ом**. И это допустимо, но тогда это не "быстрый unit".

---

## 2. Integration-тесты (use case + БД)

### Что тестируем

- Use case'ы (`ConfirmOrderUseCase`, `PlaceOrderUseCase`)
- Репозитории (реальный SQL, маппинг Entity ↔ Domain)
- Транзакции (commit/rollback)
- Optimistic locking
- Доменные события → outbox

### Что **не** мокаем

- `EntityManager` — реальная БД.
- `OrderRepository` — реальная реализация.
- `EventPublisher` — реальный outbox в БД.

### Что мокаем

- Внешние HTTP-сервисы (платёжный шлюз, email API).
- Брокер очереди (RabbitMQ) — если use case только **пишет** в outbox, то брокер не нужен.

### Infrastructure: Testcontainers

```php
final class IntegrationTestCase extends BaseTestCase
{
    protected static ?Testcontainers $container = null;
    protected static ?EntityManager $entityManager = null;

    public static function setUpBeforeClass(): void
    {
        // Один контейнер на весь run (не на каждый тест!)
        $postgres = (new PostgreSQLContainer('postgres:16-alpine'))
            ->withDatabase('test')
            ->withUsername('test')
            ->withPassword('test');

        static::$container = (new Client())->createContainer($postgres);
        static::$container->start();

        $host = static::$container->getHost();
        $port = static::$container->getMappedPort(5432);

        // Настройка Doctrine
        $em = self::bootKernel(['database_url' => "pgsql://test:test@{$host}:{$port}/test"]);
        static::$entityManager = $em;

        // Миграции
        self::runMigrations();
    }

    public static function tearDownAfterClass(): void
    {
        static::$container?->stop();
    }

    protected function getEntityManager(): EntityManager
    {
        return static::$entityManager;
    }
}
```

```php
final class ConfirmOrderIntegrationTest extends IntegrationTestCase
{
    public function test_confirm_order_persists_status_and_publishes_event(): void
    {
        $em = $this->getEntityManager();
        $useCase = $this->createConfirmOrderUseCase($em);

        // Arrange: создать заказ через реальный репозиторий
        $customer = Customer::create(name: 'Test', email: 'test@example.com');
        $order = Order::create(
            customer: $customer,
            items: [new OrderItem(product: $this->product(), quantity: 1)],
        );

        $em->persist($customer);
        $em->persist($order);
        $em->flush();

        // Act
        $useCase->execute($order->id());

        // Assert: статус изменён
        $em->clear();
        $savedOrder = $em->getRepository(OrderEntity::class)
            ->findOneBy(['id' => $order->id()->toRfc4122()]);

        $this->assertSame('confirmed', $savedOrder->status);

        // Assert: событие в outbox
        $event = $em->getRepository(OutboxEvent::class)
            ->findOneBy(['topic' => 'order.confirmed']);

        $this->assertNotNull($event);
        $this->assertArrayHasKey('orderId', $event->payload);
    }

    public function test_confirm_order_with_optimistic_lock_conflict(): void
    {
        $em = $this->getEntityManager();
        $useCase = $this->createConfirmOrderUseCase($em);

        $order = $this->createOrder($em);

        // Simulate: другой процесс изменил order
        $em->getConnection()->executeStatement(
            'UPDATE orders SET total_amount = 999, version = version + 1 WHERE id = ?',
            [$order->id()->toRfc4122()]
        );
        $em->flush();

        $this->expectException(OptimisticLockException::class);
        $useCase->execute($order->id());
    }
}
```

### Зачем Testcontainers, а не SQLite / в-memory БД

| | SQLite | Testcontainers (PostgreSQL) |
|---|--------|---------------------------|
| `CHECK (a > b)` | Поддерживает | Поддерживает |
| `EXCLUDE` (интервалы) | ❌ | ✅ |
| `JSONB` + GIN-индексы | ❌ | ✅ |
| `FOR UPDATE SKIP LOCKED` | ❌ | ✅ |
| `GENERATED ALWAYS` | ❌ | ✅ |
| Partial indexes | ❌ | ✅ |
| `pg_stat_statements` | ❌ | ✅ |
| `VACUUM`, `BRIN` | ❌ | ✅ |

Если в продакшене PostgreSQL — в тестах **тоже** PostgreSQL. Иначе вы тестируете другую БД.

### Скорость Testcontainers

- Запуск контейнера: ~2–3s (один раз на весь run).
- Миграции: ~1–2s.
- Каждый тест: ~50–200ms (без сетевых вызовов).
- Итого: 100 тестов ≈ 20–30s.

**Оптимизация:** один контейнер на весь `phpunit` run, не на каждый класс.

---

## 3. Functional-тесты (HTTP)

### Что тестируем

- Контроллер → Use case → БД (полный путь)
- Валидация входных данных
- HTTP-коды
- CORS, авторизация
- Serialization (Domain → JSON)

### Что **не** мокаем

- БД — реальная (Testcontainers).
- Use case — реальный.
- Авторизацию (Security) — реальную.

### Что мокаем

- Внешние сервисы (Stripe, Telegram) — через fake в DI.

### Пример

```php
final class OrderApiTest extends WebTestCase
{
    public function test_confirm_order_returns_204(): void
    {
        $client = static::createClient();

        // Авторизация
        $client->request('POST', '/api/login', ['email' => 'john@example.com', 'token' => 'valid']);
        $this->assertResponseStatusCodeSame(200);

        // Создание заказа
        $client->request('POST', '/api/orders', [
            'email' => 'john@example.com',
            'token' => 'valid',
            'lines' => [
                ['product_id' => 'sku-bread', 'quantity' => 2, 'unit_price' => 100],
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $orderId = json_decode($client->getResponse()->getContent(), true)['orderId'];

        // Подтверждение
        $client->request('POST', "/api/orders/{$orderId}/confirm", [
            'email' => 'john@example.com',
            'token' => 'valid',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function test_confirm_order_returns_409_if_already_confirmed(): void
    {
        $client = static::createClient();
        $orderId = $this->createConfirmedOrder($client);

        $client->request('POST', "/api/orders/{$orderId}/confirm", [
            'email' => 'john@example.com',
            'token' => 'valid',
        ]);

        $this->assertResponseStatusCodeSame(409);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('ORDER_ALREADY_CONFIRMED', $response['code']);
    }

    public function test_place_order_returns_422_with_invalid_lines(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/orders', [
            'email' => 'john@example.com',
            'token' => 'valid',
            'lines' => [
                ['product_id' => 'sku-unknown', 'quantity' => -1],
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
    }
}
```

### Зачем WebTestCase, а не curl / integration

- Авторизация — реальная (не mock).
- Routing — реальная.
- Validation (Symfony Validator) — реальная.
- Serialization (Serializer) — реальная.
- Ошибка в контроллере → 500 (а не "тест прошёл, потому что mock вернул что угодно").

### Скорость

Каждый функциональный тест создаёт нового `Client`. Это ~100–300ms на тест. 50 тестов ≈ 10–15s.

**Оптимизация:** `setUp` не сбрасывает БД, а использует **транзакцию**:

```php
final class WebTestCaseWithDb extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();
        // ... тест ...
        $em->getConnection()->rollBack();
    }
}
```

Но это **только** для тестов, которые **не** тестируют транзакции. Для тестов optimistic locking — нужен `COMMIT` + `ROLLBACK`.

---

## 4. E2E-тесты

### Когда

Только для **критических** сценариев, где:

- Несколько сервисов участвуют (order → payment → inventory).
- Есть очередь (outbox → publisher → consumer).
- Есть внешний сервис (платёжный шлюз).

### Сколько

5–10 тестов. Не больше.

### Пример

```php
final class OrderE2ETest extends IntegrationTestCase
{
    public function test_full_order_lifecycle(): void
    {
        $em = $this->getEntityManager();

        // 1. Создать заказ (через API)
        $client = static::createClient();
        $orderId = $this->createOrderViaApi($client);

        // 2. Подтвердить (через API)
        $this->confirmOrderViaApi($client, $orderId);

        // 3. Outbox-публикатор обрабатывает событие
        $publisher = $this->createOutboxPublisher();
        $publisher->processBatch();

        // 4. Consumer отправляет "email" (fake mailer)
        $mailer = $this->getFakeMailer();
        $this->assertCount(1, $mailer->sent);
        $this->assertStringContainsString('Thank you for your order', $mailer->sent[0]->body);

        // 5. Search index обновлён (fake search)
        $search = $this->getFakeSearch();
        $this->assertNotNull($search->find($orderId));
    }

    public function test_payment_failure_cancels_order(): void
    {
        $em = $this->getEntityManager();

        $orderId = $this->createOrderViaApi($this->createClient());
        $this->confirmOrderViaApi($this->createClient(), $orderId);

        // Payment gateway возвращает error
        $this->getFakePaymentGateway()->setFail(true);

        $publisher = $this->createOutboxPublisher();
        $publisher->processBatch();

        // Order отменён
        $order = $em->getRepository(OrderEntity::class)
            ->findOneBy(['id' => $orderId]);
        $this->assertSame('cancelled', $order->status);

        // Refund запрошен
        $this->assertCount(1, $this->getFakePaymentGateway()->refunds);
    }
}
```

### Что мокаем в E2E

- Платёжный шлюз (внешний, дорогой, нестабильный).
- Email API (внешний, rate-limited).
- Search engine (если не в той же БД).

### Что **не** мокаем

- БД — реальная.
- Очередь — реальная (или outbox в БД).
- Контроллеры — реальные.
- Use case'ы — реальные.

---

## 5. Что мокаем, а что нет — сводка

| Зависимость | Unit | Integration | Functional | E2E |
|-------------|:----:|:-----------:|:----------:|:---:|
| `Money`, `DateRange` (VO) | Реальные | Реальные | Реальные | Реальные |
| `Order` (сущность) | Реальная | Реальная | Реальная | Реальная |
| `OrderRepository` (impl) | — | Реальная (БД) | Реальная | Реальная |
| `EntityManager` | — | Реальная | Реальная | Реальная |
| `EventPublisher` | Fake (в память) | Реальный (outbox) | Реальный | Реальный |
| `PaymentGateway` (внешний) | Fake | Fake | Fake | Fake |
| `Mailer` (внешний) | — | Fake | Fake | Fake |
| `HttpClient` (внешний) | — | Fake | Fake | Fake |
| `Symfony Security` | — | — | Реальная | Реальная |
| `Router` | — | — | Реальная | Реальная |

**Правило:** если зависимость **внутри** системы (БД, use case, домен) — **не мокаем**. Если зависимость **вне** системы (Stripe, Telegram, Elasticsearch) — **мокаем**.

---

## 6. Как держать тесты быстрыми

### Параллельный запуск

```xml
<!-- phpunit.xml -->
<phpunit
    executionOrder="defects"
    beStrictAboutOutputDuringTests="true"
    failOnRisky="true"
    failOnWarning="true"
    cacheDirectory=".phpunit.cache"
>
    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="functional">
            <directory>tests/Functional</directory>
        </testsuite>
        <testsuite name="e2e">
            <directory>tests/E2E</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

Запуск:

```bash
# Параллельно (PHPUnit 10+ / paraunit)
vendor/bin/paraunit process --processes=4 --testsuite unit
vendor/bin/paraunit process --processes=2 --testsuite integration
vendor/bin/paraunit process --processes=2 --testsuite functional
vendor/bin/paraunit process --processes=1 --testsuite e2e
```

**Целевое время:**

| Слой | Время |
|------|-------|
| Unit (300 тестов) | < 5s |
| Integration (80 тестов) | < 30s |
| Functional (40 тестов) | < 20s |
| E2E (5 тестов) | < 30s |
| **Итого** | **< 90s** |

### Testcontainers: один на весь run

```php
// НЕ в каждом тестовом классе:
public static function setUpBeforeClass(): void
{
    $container = (new PostgreSQLContainer())->start();
}

// А в одном base-классе:
final class DatabaseTrait
{
    private static ?Testcontainers $tc = null;

    public static function postgres(): Testcontainers
    {
        return self::$tc ??= (new PostgreSQLContainer('postgres:16-alpine'))
            ->withDatabase('test')
            ->start();
    }
}
```

Один контейнер на весь `phpunit` run. Запуск — 2s. Не 2s × 100 классов.

### Избегать `clear()` без необходимости

```php
// ❌
$em->clear();  // сбрасывает весь UoW
$em->flush();  // пере-загружает всё

// ✅
// Тестируйте через реальный UoW.
// Если нужна изоляция — transaction + rollback.
```

### Не запускать миграции на каждый тест

```php
// ❌
protected function setUp(): void
{
    $this->runMigrations();  // 2s × 100 тестов = 200s
}

// ✅
// Миграции — один раз на весь run (setUpBeforeClass).
// Тесты используют transaction + rollback.
```

### CI: кэш Testcontainers

```yaml
# .github/workflows/test.yml
jobs:
  test:
    services:
      postgres:
        image: postgres:16-alpine
        ports:
          - 5432:5432
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - run: composer install
      - run: bin/console doctrine:migrations:migrate
      - run: vendor/bin/phpunit --testsuite unit,integration,functional,e2e
```

В GitHub Actions — Postgres как service. Без Testcontainers. Быстрее (контейзер уже запущен).

---

## 7. Как держать тесты стабильными

### 1. Время

```php
// ❌
public function test_order_expires_after_24_hours(): void
{
    $order = Order::create(..., placedAt: new \DateTimeImmutable());
    // ... ждём 24 часа? Нет.
    // ... но "now" — это реальное время.
}

// ✅
public function test_order_expires_after_24_hours(): void
{
    $clock = new FakeClock(new \DateTimeImmutable('2025-01-15 10:00:00'));
    $order = Order::create(..., placedAt: $clock->now());

    $this->assertTrue($order->isExpired($clock->now()->modify('+25 hours')));
    $this->assertFalse($order->isExpired($clock->now()->modify('+23 hours')));
}
```

**Правило:** домен получает время через `ClockInterface`. В тестах — `FakeClock`.

### 2. Случайность

```php
// ❌
public function test_token_is_unique(): void
{
    $token1 = Token::generate();
    $token2 = Token::generate();
    $this->assertNotSame($token1, $token2);
    // Если random_bytes() вернёт одинаковое (вероятность ~0, но...)
}

// ✅
public function test_token_generation_is_deterministic_with_seed(): void
{
    $generator = new SeededRandomGenerator(42);
    $token1 = Token::generate($generator);
    $token2 = Token::generate($generator);
    $this->assertNotSame($token1->value(), $token2->value());
}
```

### 3. Порядок тестов

```xml
<!-- phpunit.xml -->
<phpunit executionOrder="defects">
    <!-- Тесты, упавшие в прошлый run, выполняются первыми -->
</phpunit>
```

Плюс: каждый тест **самостоятелен**. Не зависит от результата другого теста.

```php
// ❌
public function test_a(): void { /* создаёт order */ }
public function test_b(): void { /* ожидает, что order из test_a существует */ }

// ✅
public function test_a(): void
{
    $order = $this->createOrder();
    $this->doSomething($order);
}

public function test_b(): void
{
    $order = $this->createOrder();  // свой порядок
    $this->doSomethingElse($order);
}
```

### 4. Изоляция от сети

```php
// ❌
$gateway = new StripeGateway(apiKey: 'sk_live_...');

// ✅
final class FakePaymentGateway implements PaymentGatewayInterface
{
    public array $charges = [];
    public array $refunds = [];
    private bool $shouldFail = false;

    public function charge(string $orderId, Money $amount): PaymentResult
    {
        if ($this->shouldFail) {
            return PaymentResult::failed('insufficient_funds');
        }

        $result = PaymentResult::success();
        $this->charges[] = ['orderId' => $orderId, 'amount' => $amount];
        return $result;
    }

    public function refund(string $paymentId): void
    {
        $this->refunds[] = $paymentId;
    }

    public function setFail(bool $fail): void
    {
        $this->shouldFail = $fail;
    }
}
```

**Правило:** в CI **ни один** тест не делает HTTP-запрос в интернет. Всё через fakes.

### 5. Конкурентность

```php
// ❌
// Два теста одновременно пишут в одну таблицу → deadlock

// ✅
// Каждый integration-тест в своей транзакции + rollback
// Testcontainers — один на run, но транзакции изолируют

protected function setUp(): void
{
    $this->em->getConnection()->beginTransaction();
}

protected function tearDown(): void
{
    $this->em->getConnection()->rollBack();
}
```

### 6. Файлы

```php
// ❌
$report = $service->generate('/tmp/report.pdf');
// /tmp/report.pdf может быть из другого теста

// ✅
$report = $service->generate(sprintf('/tmp/report_%s.pdf', uniqid()));
// Или: in-memory (PSR-7 Stream)
```

### 7. БД-данные

```php
// ❌
// Тест A: INSERT (id=1, name='John')
// Тест B: INSERT (id=1, name='Jane')  → конфликт

// ✅
// Транзакция + rollback (каждый тест в своей)
// Или: UUID-ключи (нет конфликта)
// Или: setUp создаёт свои данные, tearDown удаляет
```

---

## 8. CI-пайплайн

```yaml
# .github/workflows/test.yml
name: Tests

on: [push, pull_request]

jobs:
  unit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: ramsey/composer-install@v2
      - run: vendor/bin/phpunit --testsuite unit
      - run: vendor/bin/phpstan analyse --level=8
      - run: vendor/bin/rector --dry-run

  integration:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        ports: ['5432:5432']
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: ramsey/composer-install@v2
      - run: bin/console doctrine:migrations:migrate
        env:
          DATABASE_URL: pgsql://test:test@localhost:5432/test
      - run: vendor/bin/phpunit --testsuite integration,functional
        env:
          DATABASE_URL: pgsql://test:test@localhost:5432/test

  e2e:
    runs-on: ubuntu-latest
    needs: [integration]
    services:
      postgres:
        image: postgres:16-alpine
        ports: ['5432:5432']
        env:
          POSTGRES_DB: e2e
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: ramsey/composer-install@v2
      - run: bin/console doctrine:migrations:migrate
        env:
          DATABASE_URL: pgsql://test:test@localhost:5432/e2e
      - run: vendor/bin/phpunit --testsuite e2e
        env:
          DATABASE_URL: pgsql://test:test@localhost:5432/e2e
```

**Время CI:**

| Job | Время |
|-----|-------|
| unit + phpstan + rector | ~30s |
| integration + functional | ~60s |
| e2e | ~45s |
| **Итого (параллельно)** | **~90s** |

---

## 9. Практические правила

| Правило | Почему |
|---------|--------|
| Один тест = одна бизнес-идея | Легко прочитать, легко исправить |
| Имя теста = сценарий | `test_order_cannot_be_confirmed_without_items` |
| Arrange / Act / Assert — 3 блока | Видно, что дано, что сделали, что ждём |
| Не тестируйте "реализацию" | `assert($order->status === 'confirmed')`, а не `assert($em->flush() called)` |
| Фейки вместо mock'ов | Fake — реальная логика. Mock — "ожидание вызова". |
| Нет `sleep()` в тестах | Если нужно ждать — это антипаттерн |
| Нет `rand()` / `mt_rand()` | Детерминированность |
| Нет сети | Только fakes |
| Нет файлов (или temp per test) | Изоляция |
| Тест падает за < 5s | Если дольше — проблема |
| Тест падает с понятным сообщением | `Failed asserting that 'new' is identical to 'confirmed'.` |

---

## 10. Когда тесты **не** нужны

| Ситуация | Почему |
|----------|--------|
| Getter / setter без логики | Тестируете PHP |
| Простой маппер Domain → DTO | Видно в коде |
| Конфигурация (DI, routing) | Тестируете Symfony |
| Тривиальная валидация (`strlen > 0`) | Тестируете PHP |
| Переиспользуемый код из vendor | Тестируют авторы |

**Правило:** тест нужен, если **без него** вы не смогли бы заметить **баг**. Если баг виден глазом — тест не нужен.

> ⏱ 79.3s · prompt 87312 tok · output 7958 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

The user is asking about concurrent modification of the same database record by two users. This is a classic concurrency problem. Let me think about what to cover:

1. What problems can occur (lost updates, dirty reads, phantom reads, deadlocks)
2. Protection mechanisms:
   - Optimistic locking (version column)
   - Pessimistic locking (SELECT FOR UPDATE)
   - Application-level locking (distributed locks)
   - Idempotency
   - Eventual consistency / CRDT
   - Row-level isolation in PostgreSQL (SERIALIZABLE)
3. Comparison and when to use which

I've already covered optimistic vs pessimistic locking in previous answers, so I need to go deeper and broader this time. Let me focus on:
- The specific failure modes (what actually happens at the DB level)
- A more complete taxonomy of protection approaches
- Practical trade-offs with concrete numbers
- Edge cases I haven't covered before (deadlock patterns, distributed systems, long transactions)

Let me structure this clearly with a diagnostic flow, then each approach with code, then a comparison matrix, then decision criteria.

</details>

**Кандидат:**

## Какие проблемы возникают

### 1. Lost Update (потерянное обновление)

Самая частая. Оба читают, оба пишут, один результат теряется.

```
t=0  balance = 100

Process A:                    Process B:
  READ balance = 100           READ balance = 100
  balance = 100 - 30 = 70     balance = 100 + 50 = 150
  UPDATE balance = 70         UPDATE balance = 150

Результат: balance = 150
Ожидалось: balance = 120
```

Никто не получил ошибку. Просто данные неверны.

### 2. Dirty Read (чтение несуществующих данных)

```
Process A: BEGIN; UPDATE orders SET status='cancelled';  -- ещё не COMMIT
Process B: SELECT * FROM orders WHERE id=5;  -- видит 'cancelled'
Process A: ROLLBACK;
-- Process B "увидел" состояние, которого никогда не было
```

В PostgreSQL по умолчанию `READ COMMITTED` — dirty read **невозможен**. Но `REPEATABLE READ` и `SERIALIZABLE` дают другие проблемы.

### 3. Phantom Read (фантомные строки)

```
Process A: BEGIN; SELECT COUNT(*) FROM orders WHERE status='new';  -- 10
Process B: INSERT INTO orders (status) VALUES ('new');  -- COMMIT
Process A: SELECT COUNT(*) FROM orders WHERE status='new';  -- 11 (фантом)
```

Решается `SERIALIZABLE` или `FOR UPDATE` с range.

### 4. Deadlock (взаимная блокировка)

```
Process A: BEGIN;
  UPDATE orders SET total=150 WHERE id=1;   -- lock row 1
  UPDATE order_items SET qty=5 WHERE id=10; -- lock row 10

Process B: BEGIN;
  UPDATE order_items SET qty=3 WHERE id=10; -- lock row 10
  UPDATE orders SET total=200 WHERE id=1;   -- ждёт lock row 1 → DEADLOCK
```

PostgreSQL убивает одну из транзакций:

```
ERROR:  deadlock detected
DETAIL:  Process 12345 waits for ShareLock on transaction 67890; blocked by process 54321.
         Process 54321 waits for ShareLock on transaction 12345; blocked by process 67890.
HINT:  See server log for query details.
```

### 5. Inconsistent Read (несогласованное чтение в пределах транзакции)

```
Process A: BEGIN;
  SELECT status FROM orders WHERE id=1;   -- 'new'
  SELECT total FROM orders WHERE id=1;    -- 100
  -- Между двумя SELECT'ами другой процесс изменил status на 'cancelled'
  SELECT status FROM orders WHERE id=1;   -- 'cancelled'
COMMIT;
-- Транзакция A видела два разных состояния
```

Решается `REPEATABLE READ`.

---

## Способы защиты

### Уровень 1: Optimistic Locking (версионирование)

**Принцип:** не блокируем. Проверяем при записи, что данные не изменились.

```sql
ALTER TABLE orders ADD COLUMN version INTEGER NOT NULL DEFAULT 1;

-- UPDATE с проверкой версии
UPDATE orders
SET status = 'confirmed', version = version + 1
WHERE id = 1 AND version = 3;

-- Если rowcount = 0 → конфликт
```

В Doctrine:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column]
    private string $status;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;
}
```

При `flush()`:

```sql
UPDATE orders SET status = 'confirmed', version = 4
WHERE id = 1 AND version = 3;
-- rowcount = 0 → OptimisticLockException
```

**Плюсы:**

| | |
|---|---|
| Нет блокировок при чтении | 1000 читателей не мешают друг другу |
| Нет deadlock'а | Никто не "ждёт" |
| Масштабируется | Конфликт = редкое событие, не норма |
| Прозрачно для БД | Просто `WHERE version = N` |

**Минусы:**

| | |
|---|---|
| Конфликт → ошибка | Нужно retry или сообщить пользователю |
| Не гарантирует "последний выиграл" | Два процесса могут "перегнать" друг друга |
| Не подходит для "долгих" транзакций | Version устареет за 10s → гарантированный конфликт |

**Когда:**

- Конфликт **редкий** (< 5% запросов).
- Транзакция **короткая** (< 1s).
- Пользователь может **retry**.
- Чтение >> записи (typical web app).

---

### Уровень 2: Pessimistic Locking (SELECT FOR UPDATE)

**Принцип:** блокируем строку на время транзакции. Другие ждут.

```sql
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;  -- блокирует
UPDATE orders SET status = 'confirmed' WHERE id = 1;
COMMIT;  -- разблокирует
```

В Doctrine:

```php
$order = $em->getRepository(Order::class)
    ->createQueryBuilder('o')
    ->where('o.id = :id')
    ->setParameter('id', 1)
    ->setLockMode(LockMode::PESSIMISTIC_WRITE)
    ->getQuery()
    ->getOneOrNullResult();

$order->confirm();
$em->flush();
```

**Плюсы:**

| | |
|---|---|
| Гарантирует "один пишет" | Нет lost update |
| Предсказуемый порядок | Первый пришёл — первый изменил |
| Нет retry | Не нужно "перепробовать" |

**Минусы:**

| | |
|---|---|
| Блокировка во время транзакции | Пока один пишет — все ждут |
| Deadlock'и | A lock'ит 1,2 → B lock'ит 2,1 |
| Contention | 50 писателей на одну строку → очередь |
| Long transaction → timeout | `lock_timeout` срабатывает |

**Когда:**

- Конфликт **частый** (> 10%).
- Потеря обновления **недопустима** (инвентарь, баланс).
- Транзакция **короткая** (< 500ms).
- Порядок важен (FIFO).

---

### Уровень 3: SERIALIZABLE isolation (PostgreSQL)

**Принцип:** PostgreSQL эмулирует сериализацию. Если конфликт — откатывает.

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
BEGIN;
  UPDATE accounts SET balance = balance - 100 WHERE id = 1;
  UPDATE accounts SET balance = balance + 100 WHERE id = 2;
COMMIT;
```

Если два процесса делают "взаимные переводы" одновременно:

```
A: 1→2 (100)
B: 2→1 (100)
```

PostgreSQL **обнаружит** конфликт и **откатит** одну транзакцию:

```
ERROR:  serialization failure
```

**Плюсы:**

| | |
|---|---|
| Полный уровень изоляции | Нет dirty reads, phantoms, anomalies |
| Без ручного version'а | БД сама определяет конфликт |
| Гарантированная корректность | Ацикличность переходов |

**Минусы:**

| | |
|---|---|
| Высокая вероятность отката | При high concurrency — > 50% откатов |
| Нужен retry | Приложение обязано ловить `serialization failure` |
| Производительность | SSI (Serializable Snapshot Isolation) — overhead |
| Не масштабируется | 1000 писателей → 500 откатов |

**Когда:**

- Финансовые операции (переводы, расчёты).
- Низкий параллелизм (< 50 писателей/s на горячую строку).
- Корректность **критична**, скорость — нет.

**Практика:**

```php
public function transfer(Account $from, Account $to, Money $amount): void
{
    $maxRetries = 3;

    for ($i = 0; $i < $maxRetries; $i++) {
        try {
            $this->em->transactional(function (EntityManagerInterface $em) use ($from, $to, $amount) {
                $em->getConnection()->executeStatement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');

                $fromEntity = $this->accounts->findById($from->id());
                $toEntity = $this->accounts->findById($to->id());

                $fromEntity->debit($amount);
                $toEntity->credit($amount);

                $em->flush();
            });
            return; // успех
        } catch (SerializationFailure $e) {
            if ($i === $maxRetries - 1) {
                throw new ConflictException('Transfer failed after retries');
            }
            usleep(50 * (2 ** $i) * 1000); // exponential backoff
        }
    }
}
```

---

### Уровень 4: Distributed Lock (Redis / ZooKeeper)

**Принцип:** "замок" вне БД. Кто первый забрал — тот пишет.

```php
final class RedisLock
{
    public function __construct(private Redis $redis) {}

    public function acquire(string $key, int $ttl = 10): bool
    {
        // SET key value NX EX ttl — атомарно
        return (bool) $this->redis->set($key, bin2hex(random_bytes(16)), ['NX', 'EX' => $ttl]);
    }

    public function release(string $key, string $token): bool
    {
        // Lua-скрипт: проверяем token, удаляем
        $script = <<<'LUA'
            if redis.call("get", KEYS[1]) == ARGV[1] then
                return redis.call("del", KEYS[1])
            end
            return 0
        LUA;

        return $this->redis->eval($script, [$token], 1) === 1;
    }
}

// Использование:
$lock = new RedisLock($redis);
$token = bin2hex(random_bytes(16));

if (!$lock->acquire("order:{$orderId}", ttl: 10)) {
    throw new ConflictException('Order is being modified');
}

try {
    $order = $repo->findById($orderId);
    $order->update($newData);
    $repo->save($order);
} finally {
    $lock->release("order:{$orderId}", $token);
}
```

**Плюсы:**

| | |
|---|---|
| Работает между инстансами | Микросервисы, кластеры |
| Не блокирует БД | БД свободна для других операций |
| Гибкий TTL | 5s, 10s, 60s — по ситуации |

**Минусы:**

| | |
|---|---|
| Redis может упасть | Lock "потерян" → два писателя |
| TTL — хрупкость | Если операция > TTL → lock expired → два писателя |
| Сложность | Нужно правильно реализовать release (token) |
| Не заменяет БД-консистентность | Всё равно нужен version или SERIALIZABLE |

**Когда:**

- Распределённая система (несколько инстансов).
- Нужно ограничить **один** писатель глобально.
- БД-блокировки слишком дорогие (long transaction).

---

### Уровень 5: Idempotency + Eventual Consistency

**Принцип:** не защищаем от конкурентности. Вместо этого — допускаем дубли и делаем операцию **идемпотентной**.

```php
// Приём платежа
public function charge(string $idempotencyKey, Money $amount): Payment
{
    // 1. Проверить: уже выполнялось?
    $existing = $this->payments->findByKey($idempotencyKey);
    if ($existing !== null) {
        return $existing; // дубль → вернуть результат
    }

    // 2. Записать key ДО выполнения (защита от дублей)
    $this->payments->reserveKey($idempotencyKey);

    try {
        $result = $this->gateway->charge($amount);
        $payment = Payment::create($idempotencyKey, $result);
        $this->payments->save($payment);
        return $payment;
    } catch (\Throwable $e) {
        $this->payments->releaseKey($idempotencyKey);
        throw $e;
    }
}
```

**Плюсы:**

| | |
|---|---|
| Нет блокировок | Высокий throughput |
| Устойчиво к retry | Дубль → тот же результат |
| Распределённый | Не нужен "один писатель" |

**Минусы:**

| | |
|---|---|
| Не защищает от "разных" операций | `charge(100)` и `refund(50)` — обе могут пройти |
| Только для "повторяемых" операций | `increment` — да. `delete` — нет. |
| Сложность | Нужно хранить keys, очищать |

**Когда:**

- Операция **повторяемая** (charge, send, create).
- Retry — нормальный сценарий (не ошибка).
- Распределённая система.

---

### Уровень 6: Application-level Queue (FIFO)

**Принцип:** все изменения проходят через **одну** очередь. Конкурентность устраняется **очередью**, а не блокировками.

```
User A → Queue → Worker → UPDATE order (id=1)
User B → Queue → Worker → UPDATE order (id=1)
                        ↑
              Worker обрабатывает ПОСЛЕДОВАТЕЛЬНО
```

**Плюсы:**

| | |
|---|---|
| Гарантированный порядок | FIFO |
| Нет блокировок БД | Worker один → нет конкурентности |
| Балансировка нагрузки | Очередь сглаживает пики |

**Минусы:**

| | |
|---|---|
| Задержка | Ожидание в очереди (100ms – 5s) |
| Не для read-heavy | Чтение всё равно конкурентное |
| Сложность | Очередь, worker, retry, DLQ |
| Bottleneck | Один worker → throughput = 1/s |

**Когда:**

- Изменения **редкие** (< 10/s).
- Порядок **критичен**.
- Можно допустить задержку 100ms+.

---

## Сравнительная матрица

| | Optimistic | Pessimistic | SERIALIZABLE | Distributed Lock | Idempotency | Queue |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| **Блокировка БД** | Нет | Да | Да (SSI) | Нет | Нет | Нет |
| **Deadlock** | Нет | Да | Да (откат) | Нет | Нет | Нет |
| **Retry** | Да | Нет | Да | Нет | Да | Нет |
| **Масштабируемость** | Высокая | Низкая | Средняя | Высокая | Высокая | Низкая |
| **Гарантия порядка** | Нет | Да | Да | Да | Нет | Да |
| **Latency** | Минимальная | +lock wait | +SSI overhead | +Redis RTT | Минимальная | +queue wait |
| **Сложность** | Низкая | Средняя | Средняя | Высокая | Средняя | Высокая |
| **Распределённый** | Нет | Нет | Нет (1 БД) | Да | Да | Да |
| **Частые конфликты** | Плохо | Хорошо | Хорошо | Хорошо | Н/Д | Хорошо |

---

## Алгоритм выбора

```
Конкурентная запись на одну строку
│
├─ Конфликт редкий (< 5%)?
│   ├─ ДА → Optimistic Locking
│   └─ НЕТ ↓
│
├─ Потеря обновления недопустима И транзакция < 1s?
│   ├─ ДА → Pessimistic Locking (FOR UPDATE)
│   └─ НЕТ ↓
│
├─ Финансовая операция, нужна гарантия?
│   ├─ ДА → SERIALIZABLE + retry
│   └─ НЕТ ↓
│
├─ Распределённая система (несколько инстансов)?
│   ├─ ДА → Distributed Lock (Redis)
│   └─ НЕТ ↓
│
├─ Операция повторяемая (charge, send)?
│   ├─ ДА → Idempotency
│   └─ НЕТ ↓
│
└─ Порядок критичен, задержка ок?
    ├─ ДА → Queue (FIFO)
    └─ НЕТ → Вернуться к Pessimistic + оптимизация
```

---

## Практические комбинации

### Веб-приложение (1 инстанс, 100 RPS)

```
Основа:      Optimistic Locking (#[ORM\Version])
Защита:      409 Conflict + retry (3×, exponential backoff)
Инвентарь:   Pessimistic Locking (FOR UPDATE) — только hot rows
Аудит:       Idempotency key на все mutating операции
```

### B2B-платформа (5 инстансов, 500 RPS)

```
Основа:      Optimistic Locking
Инвентарь:   Pessimistic + FOR UPDATE SKIP LOCKED (очередь)
Финансы:     SERIALIZABLE + retry (3×)
Cross-service: Distributed Lock (Redis) для "глобальных" операций
```

### Высоконагруженный (50 000 RPS)

```
Основа:      Eventual Consistency + Idempotency
Запись:      Queue (Kafka) + single consumer per partition
Чтение:      Read replica + cache (Redis)
Конфликты:   CRDT / vector clocks (для collaborative editing)
```

---

## Частые ошибки

### 1. "А у нас один инстанс, concurrency нет"

```
❌ Ошибка: web + worker + cron + CLI = 4+ конкурентных процесса.
❌ Ошибка: один инстанс = 100 RPS = 100 конкурентных запросов.
```

Конкурентность — это не "два сервера". Это **два запроса одновременно**.

### 2. `SELECT FOR UPDATE` в "долгой" транзакции

```php
// ❌
BEGIN;
SELECT * FROM orders WHERE id=1 FOR UPDATE;  -- lock
// ... 5 секунд: HTTP-запрос к Stripe ...
UPDATE orders SET status='paid' WHERE id=1;
COMMIT;

// Lock держится 5 секунд. 50 пользователей → очередь 250 секунд.
```

**Правильно:** сделать внешний вызов **до** lock'а или **после** commit'а.

```php
// ✅
$result = $stripe->charge(...);  // 5 секунд, без lock

BEGIN;
SELECT * FROM orders WHERE id=1 FOR UPDATE;  // lock < 100ms
UPDATE orders SET status='paid' WHERE id=1;
COMMIT;
```

### 3. Optimistic lock + "вечный retry"

```php
// ❌
while (true) {
    try {
        $useCase->execute(...);
    } catch (OptimisticLockException) {
        continue;  // бесконечный цикл при "горячей" строке
    }
}

// ✅
for ($i = 0; $i < 3; $i++) {
    try {
        $useCase->execute(...);
        return;
    } catch (OptimisticLockException) {
        if ($i === 2) {
            throw new ConflictException('Too many conflicts. Try again later.');
        }
        usleep(50 * (2 ** $i) * 1000);
    }
}
```

### 4. Deadlock из-за порядка lock'ов

```php
// ❌ Процесс A: lock(order) → lock(inventory)
// ❌ Процесс B: lock(inventory) → lock(order)
// → DEADLOCK

// ✅ Единый порядок: ВСЕГДА order → inventory
// Или: один агрегат (Order содержит Inventory) → один lock
```

**Правило:** определите **глобальный порядок** блокировок. Все транзакции lock'ят в одном порядке.

### 5. "Я просто UPDATE'ну, не нужно блокировать"

```sql
-- ❌
UPDATE orders SET total = total + 50 WHERE id = 1;

-- Это атомарно на уровне БД (MVCC + row lock).
-- НО: если total = total + 50 зависит от ДРУГИХ строк:
UPDATE orders SET total = (SELECT SUM(qty*price) FROM order_items WHERE order_id = 1)
WHERE id = 1;

-- Здесь:
-- 1. Lock'ится order row
-- 2. Читается order_items (без lock!)
-- 3. Если между 1 и 2 кто-то изменил items → inconsistent

-- ✅
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;
SELECT * FROM order_items WHERE order_id = 1 FOR UPDATE;
UPDATE orders SET total = (SELECT SUM(qty*price) FROM order_items WHERE order_id = 1)
WHERE id = 1;
COMMIT;
```

### 6. `version` на каждой строке

```php
// ❌
class OrderItem
{
    #[ORM\Version]
    private int $version;  // избыточно
}

// ✅ Version только на корне агрегата (Order).
// Изменение OrderItem = изменение Order → version Order обновится.
```

---

## Итог

| Ситуация | Решение | Почему |
|----------|---------|--------|
| Веб, 100 RPS, конфликт редкий | **Optimistic** | Без блокировок, масштабируется |
| Инвентарь, 50 RPS на hot row | **Pessimistic** | Lost update недопустим |
| Банковские переводы | **SERIALIZABLE + retry** | Гарантированная корректность |
| 5 микросервисов, 1 ресурс | **Distributed Lock** | Блокировка вне БД |
| Retry-ориентированные операции | **Idempotency** | Дубль = ок |
| Строгий порядок, низкая частота | **Queue** | FIFO, без конкурентности |

**Главный принцип:** выберите **уровень гарантий**, который нужен бизнесу. Не все гарантии стоят одинаково. `409 Conflict + retry` — часто достаточно. `SERIALIZABLE` — только когда "ошибка" = "потеря денег".

> ⏱ 63.2s · prompt 94993 tok · output 5735 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

Пользователь продолжает собеседование на PHP-бэкенд-разработчика. Это уже восьмой вопрос. Он спрашивает о git-процессе: ветвление, коммиты, PR, ревью, релизы. Нужно ответить как опытный разработчик, который реально работает с git в команде, а не пересказывает книжку.

Ключевые моменты, которые нужно покрыть:
1. Стратегия ветвления — trunk-based vs git-flow, почему именно так
2. Формат коммитов — conventional commits, почему
3. PR-процесс — размер, описание, чеклист
4. Ревью — что смотрим, сроки, культура
5. Релизы — tagging, changelog, rollback
6. Почему именно так — мотивация каждого решения

Важно: я уже в этом разговоре заявлял, что у меня 7 лет опыта, команда 8 человек, 12 сервисов на Symfony. Нужно быть консистентным с этим. Значит, у меня скорее всего trunk-based flow с короткими ветками, conventional commits, squash-merge, PR-ревью.

Структурирую ответ:
- Общий принцип (trunk-based, короткий цикл)
- Ветвление: main + feature branches (короткие)
- Формат коммитов: conventional commits
- PR: размер, описание, чеклист
- Ревью: что смотрим, сроки, культура
- Релизы: tagging, changelog, rollback
- Почему именно так (мотивация)
- Антипаттерны

Длина: вопрос широкий, но нужно быть конкретным, не расползаться. Средний объём ответа — как в предыдущих.

</details>

**Кандидат:**

## Общий принцип

**Trunk-based development с короткими feature-ветками.** `main` всегда в deployable-состоянии. Цикл от задачи до прода — 1–3 дня, не 2 недели.

Это не религиозная позиция. Это следствие из:

- 12 сервисов, каждый с собственным ритмом деплоя.
- 8 разработчиков, 3 команды.
- Продакшен-баги, которые нужно фиксить **сегодня**, а не "в следующем релизе".

---

## Ветвление

```
main (always deployable)
 │
 ├── feature/order-cancellation    ← 1–3 дня
 ├── fix/payment-timeout           ← < 1 день
 ├── hotfix/vacuum-cron            ← emergency
 │
 └── release/ (НЕ используем)
```

### Правила

| Правило | Почему |
|---------|--------|
| Ветка живёт **максимум 3 дня** | Чем дольше — тем больше merge conflict'ов, тем сложнее ревью. |
| Одна ветка = одна задача | "Refactor + feature + fix" в одной ветке = невозможно ревью. |
| `main` **всегда** деплойable | CI зелёный → можно деплоить. Нет "релизной ветки". |
| Нет `develop`, `release`, `staging`-веток | Они существуют, чтобы **откладывать** проблемы. Если `main` падает — проблема в процессе, а не в ветке. |
| Feature-ветка из `main`, мерж в `main` | Нет "лишних" уровней. |

### Почему не Git Flow

Git Flow (`develop` → `release` → `main`) был создан для **релизов раз в квартал**. У нас 5–10 деплоев в неделю на сервис. `release`-ветка — это "парковка" для 3 дней работы, которая на деле превращается в 2 недели, и merge conflict'ов становится больше, чем в trunk-based.

Git Flow оправдан, когда:
- Релиз раз в 1–2 месяца.
- Есть `LTS` версии.
- Разные ветки обслуживаются разными командами.

У нас ни одно из этих условий не выполняется.

### Почему не "только main, без веток"

Полный trunk-based (push на main, нет PR) — это **намеренная** стратегия (Uber, Facebook). Она работает, когда:
- CI < 2 минуты.
- Есть feature flags.
- Команда > 50 человек (каждый слишком мал, чтобы "замедлять").

У нас CI = 90s, feature flags — только для 2 сервисов, команда 8 человек. PR-ревью — **не** bottleneck. Он **ловит** баги. Убирать его — потерять качество.

---

## Формат коммитов

### Conventional Commits

```
<type>(<scope>): <subject>

[optional body]

[optional footer(s)]
```

### Типы

| Type | Когда |
|------|-------|
| `feat` | Новая функциональность |
| `fix` | Исправление бага |
| `refactor` | Изменение без изменения поведения |
| `perf` | Оптимизация производительности |
| `docs` | Документация |
| `test` | Добавление/изменение тестов |
| `chore` | CI, конфиг, зависимости |
| `style` | Форматирование, whitespace |
| `revert` | Откат коммита |

### Примеры

```
feat(order): add cancellation reason field

Allow customer to specify why they cancelled an order.
The reason is stored as enum and shown in admin panel.

Closes #142
```

```
fix(payment): handle timeout from Stripe gracefully

Previously, a 504 from Stripe would crash the worker.
Now it retries with exponential backoff (3 attempts, 5s base).

Fixes #157
```

```
refactor(order): extract OrderPolicy from Order::confirm()

Moves business rules (canConfirm, canCancel) into a dedicated
policy class. No behavior change.

Part of #131
```

### Scope

Scope — это **домен**, не модуль кода:

```
feat(order): ...       ← домен Order
fix(payment): ...      ← домен Payment
feat(customer): ...    ← домен Customer
chore(ci): ...         ← инфраструктура
```

### Почему Conventional Commits

1. **Changelog генерируется автоматически.** `conventional-changelog` парсит коммиты → `CHANGELOG.md`. Не нужно писать changelog руками.
2. **SemVer можно выводить автоматически.** `feat` → minor, `fix` → patch, `feat!` или `BREAKING CHANGE` → major.
3. **История читается.** `git log --oneline` — не "update stuff", а "что изменилось и почему".
4. **Бот может автоматизировать.** PR-бот проверяет формат. Не надо спорить "как назвать коммит".

### Почему не "свободный формат"

```
❌ "fix"
❌ "update"
❌ "wip"
❌ "asdf"
```

Через 3 месяца никто не помнит, что было сделано. `git bisect` без описательных коммитов — боль.

### Squash-merge

```bash
# При мерже PR: squash в ОДИН коммит
# 5 коммитов в PR → 1 коммит в main
# Название коммита = название PR

feat(order): add cancellation reason field

Allow customer to specify why they cancelled an order...
```

**Почему squash, а не merge commit:**

| | Squash | Merge commit |
|---|--------|-------------|
| История `main` | 1 коммит = 1 фича | 5 коммитов + merge = шум |
| `git bisect` | Чистая бисекция | Merge commit'ы ломают |
| Revert | `git revert <sha>` — 1 коммит | Нужно revertить 5 коммитов + merge |
| `git log --oneline` | Читаемо | "Merge branch 'feature/x'" — неинформативно |

---

## Pull Request

### Размер

**Цель: < 400 строк диффа** (включая тесты).

| Размер | Статус |
|--------|--------|
| < 100 строк | Отлично. Ревью за 15 мин. |
| 100–400 строк | Нормально. Ревью за 30–60 мин. |
| 400–800 строк | Больно. Разбей. |
| > 800 строк | **Не мерджить.** Это не PR, это проект. |

**Почему:**

- Ревью > 400 строк — качество ревью **упадает**. Человек сканирует, а не читает.
- Больший PR = больший revert. Если что-то сломалось — откатить 2000 строк дороже, чем 300.
- Больший PR = дольше мержится. Конфликты с другими PR'ами.

**Что делать, если фича большая:**

```
feat(order): add cancellation (scaffolding)
  → модель, миграция, репозиторий, тесты

feat(order): add cancellation reason to UI
  → контроллер, DTO, валидация, тесты

feat(order): add cancellation notification
  → событие, consumer, тесты
```

3 PR'а по 150–200 строк. Каждый — самодостаточный, deployable.

### Структура PR

```markdown
## Что

Кратко: что делает этот PR. 1–3 предложения.

## Зачем

Почему. Ссылка на задачу / баг / тикет.

## Как

Ключевые решения. Не "что изменил", а "почему именно так".

## Тесты

- [ ] Unit-тесты добавлены
- [ ] Integration-тесты добавлены
- [ ] Ручное тестирование проведено

## Риски

Что может пойти не так. Что не покрыто.

## Чеклист

- [ ] Код соответствует стайлгайду
- [ ] Нет breaking changes (или помечено `!`)
- [ ] Миграция провалидирована на staging
- [ ] Документация обновлена (если нужно)
```

### Почему так

- "Что" — для того, кто **не** будет ревьюить (через 3 месяца).
- "Зачем" — для того, кто **ревьюит**. Чтобы он понимал контекст, а не гуглил тикет.
- "Как" — самое важное. Значит: "я выбрал X вместо Y, потому что Z". Без этого ревьюер будет спрашивать "почему так?" — и ты будешь объяснять.
- "Риски" — честность. Если ты не уверен — напиши. Ревьюер может подсказать.

---

## Ревью

### Что смотрю

| Приоритет | Что |
|:---------:|-----|
| 1 | **Корректность.** Ломается ли что-то? Edge cases? |
| 2 | **Границы.** Нарушены ли слои? Зависимости в правильном направлении? |
| 3 | **Исключения.** Обработаны ли ошибки? Контракт ясен? |
| 4 | **Тесты.** Покрыты ли критические пути? |
| 5 | **Читаемость.** Имена, структура, документация. |
| 6 | **Производительность.** N+1, лишние запросы, память. |
| 7 | **Стиль.** Форматирование, импорты, порядок. |

**Не смотрю (бот смотрит):**
- Форматирование (Rector, PHP-CS-Fixer).
- Стилистические замечания (PSR-12).
- Очевидные ошибки (PHPStan level 8).

### Сроки

| SLA | Кто |
|-----|-----|
| Первый комментарий: **4 рабочих часа** | Ревьюер |
| Итого: **1 рабочий день** | Ревьюер |
| Ответ на замечания: **4 рабочих часа** | Автор |

**Почему:**

- PR, который ждут 3 дня — **холодный**. Контекст потерян. Ревьюер "перечитывает", а не "читает".
- Автор, который ждёт 2 дня — **демотивирован**.
- 4 часа — это "пока контекст свежий".

### Культура

| Правило | Почему |
|---------|--------|
| Критика — к коду, не к человеку | "Этот код не обрабатывает null" ≠ "Ты не обработал null" |
| Замечание = вопрос, если не уверен | "Не уверен, что тут правильно — можно объяснить?" |
| `nit:` / `suggestion:` / `must-fix` | Приоритизация. Не всё одинаково важно. |
| Ревьюер **обязан** ответить | "Не вижу проблем" — тоже ответ. Молчание = баг. |
| Автор **обязан** ответить на каждый комментарий | "Отмечено" или "Спорю, потому что X". |

### Почему не "один ревьюер — один approve"

В нашей команде: **минимум 1 approve + 0 must-fix**. Для PR'ов > 300 строк или затрагивающих доменную логику — **2 approve**.

"Один ревьюер — один approve" достаточно для:
- `chore`, `docs`, `style`.
- Мелкие `fix` (< 100 строк).

Два ревьюера для:
- Изменений доменной модели.
- Миграций.
- Изменений API (breaking).
- Безопасности (auth, payment).

---

## Релизы

### Модель

**Continuous Deployment на `main`.** Нет "релиза 1.2.3" как события.

| Событие | Что происходит |
|---------|---------------|
| Merge в `main` | CI запускается |
| CI зелёный | Авто-деплой на staging |
| Staging OK | Авто-деплой на production |
| Production OK | Tag: `v1.23.4` (semver) |

**Почему CD, а не "релиз раз в неделю":**

- "Релиз раз в неделю" = 7 дней работы = большой батч = высокий риск.
- "Деплой каждый merge" = маленький шаг = низкий риск.
- Rollback 1 коммита проще, чем rollback "релиза" из 40 коммитов.

### Тегирование

```bash
# Автоматически (бот после деплоя):
git tag -a v1.23.4 -m "Release v1.23.4

feat(order): add cancellation reason
fix(payment): handle stripe timeout
chore: bump phpstan to 2.0"
```

SemVer:
- `MAJOR` — breaking change (API, формат БД).
- `MINOR` — новая фича.
- `PATCH` — фикс.

Выводится автоматически из conventional commits.

### Changelog

```markdown
## [1.23.4] - 2025-01-15

### Added
- Order cancellation with reason (`#142`)

### Fixed
- Stripe timeout no longer crashes worker (`#157`)

### Changed
- Bumped PHPStan to 2.0

## [1.23.3] - 2025-01-12
...
```

Генерируется `conventional-changelog` из коммитов. Не пишется руками.

### Migrations

**Критическое правило:** миграция в PR'е — **только добавление**, никогда не удаление.

| Разрешено | Запрещено |
|-----------|-----------|
| `CREATE TABLE` | `DROP COLUMN` |
| `ADD COLUMN` (nullable) | `ALTER COLUMN ... TYPE` |
| `ADD INDEX` | `DROP TABLE` |
| `CREATE INDEX CONCURRENTLY` | Миграция, которая ломает существующие данные |

**Удаление колонки — 2 этапа:**

```
PR 1: добавить новую колонку, писать в обе, читать из новой.
  (деплой, 1 неделя)

PR 2: удалить старую колонку.
  (деплой)
```

Почему: `DROP COLUMN` блокирует таблицу в PostgreSQL (ACCESS EXCLUSIVE LOCK). В production с 500 RPS — это 30 секунд простоя.

### Rollback

```bash
# 1. Откатить код
git revert <commit-sha>
# → PR → merge → авто-деплой

# 2. Откатить миграцию (если нужно)
php bin/console doctrine:migrations:version --add 20250115_000000
# (откатывает только добавление, не удаление)

# 3. Если migration не reversible:
# → forward-fix (новая миграция), не rollback
```

**Правило:** миграция должна быть **forward-only**. Rollback миграции — антипаттерн, потому что:
- В production данные уже изменились.
- Откат миграции может потерять данные.
- Forward-fix безопаснее.

### Hotfix

```
1. Создай ветку из main: hotfix/vacuum-cron
2. Минимальный фикс (< 50 строк)
3. PR → 1 ревьюер → merge
4. Авто-деплой
5. Tag: v1.23.5
```

Нет "отдельной hotfix-ветки из release". Фиксим из `main`, потому что `main` — актуальное состояние.

---

## CI-пайплайн

```yaml
# .github/workflows/deploy.yml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: ramsey/composer-install@v2
      - run: vendor/bin/phpcs src/
      - run: vendor/bin/phpstan analyse --level=8
      - run: vendor/bin/rector --dry-run
      - run: vendor/bin/phpunit --testsuite unit

  test:
    needs: check
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        ports: ['5432:5432']
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - uses: ramsey/composer-install@v2
      - run: bin/console doctrine:migrations:migrate
      - run: vendor/bin/phpunit --testsuite integration,functional,e2e

  deploy:
    needs: test
    runs-on: ubuntu-latest
    environment: production
    steps:
      - uses: actions/checkout@v4
      - run: |
          # 1. Build image
          docker build -t registry/app:${{ github.sha }} .
          # 2. Push
          docker push registry/app:${{ github.sha }}
          # 3. Deploy (K8s / ECS)
          kubectl set image deployment/app app=registry/app:${{ github.sha }}
          # 4. Health check
          kubectl rollout status deployment/app --timeout=120s
          # 5. Tag
          git tag v$(semver bump minor)
          git push origin --tags
```

**Почему последовательно (check → test → deploy):**

- `check` (30s) — быстрый фидбек. Если стиль/статический анализ упали — не тратим время на БД.
- `test` (60s) — только если check прошёл.
- `deploy` — только если test прошёл.

**Почему `environment: production`:**

GitHub Actions показывает "кнопку Approve" для production-деплоя. Это не блокировка, а **видимость**: в UI видно, что кто-то деплоит в прод.

---

## Почему именно так (сводка)

| Решение | Альтернатива | Почему выбрал это |
|---------|-------------|-------------------|
| Trunk-based | Git Flow | 10 деплоев/нед. Git Flow = откладывание проблем. |
| Ветки ≤ 3 дня | Долгие feature-ветки | Конфликты, потеря контекста, большое ревью. |
| Conventional Commits | Свободный формат | Авто-changelog, авто-semver, читаемая история. |
| Squash-merge | Merge commit | Чистая история, простой revert. |
| PR < 400 строк | Большие PR | Качество ревью падает, revert дороже. |
| 1–2 ревьюера | 0 или 5 | 0 = потеря качества. 5 = bottleneck. |
| CD (каждый merge) | Релиз раз в неделю | Малый шаг = низкий риск. |
| Forward-only migrations | Rollback migrations | Безопаснее для production. |
| CI-гейт перед деплоем | Ручной деплой | Человек забывает. CI не забывает. |

---

## Антипаттерны, которые я устранил

| Антипаттерн | Последствие | Что сделал |
|-------------|-------------|-----------|
| `main` не деплойable 2 недели | "Никто не знает, что в проде" | Trunk-based + CD |
| PR на 2000 строк | Ревью = формальность | Лимит 400 строк |
| Коммиты "fix", "update" | `git bisect` = боль | Conventional Commits |
| Ревью через 3 дня | Контекст потерян | SLA 4 часа |
| `DROP COLUMN` в миграции | 30s простоя prod | Forward-only, 2 этапа |
| Деплой в пятницу вечером | Баг в выходные | Правило: нет деплоев после 16:00 в пятницу |
| "Я поправлю в следующем PR" | Технический долг | `must-fix` в текущем PR |
| Merge без тестов | "CI был красный, но срочно" | CI = гейт. Красный = не мерджим. |

---

## Итог

Git-процесс — это **инструмент управления риском**. Каждый элемент существует для того, чтобы:

1. **Код в проде — предсказуем.** (Trunk-based, CD)
2. **Код в проде — понятен.** (Conventional Commits, Changelog)
3. **Баги ловятся до прода.** (PR-ревью, CI)
4. **Проблемы откатываются быстро.** (Squash, small PR, forward-only migrations)
5. **Команда не ждёт.** (SLA, короткие ветки, параллельный CI)

Если процесс "сложный" — значит, он решает проблемы, которых нет. Если процесс "простой" — значит, он не решает реальных проблем. Мой процесс — компромисс между скоростью и безопасностью для команды из 8 человек и 12 сервисов.

> ⏱ 68.3s · prompt 100553 tok · output 5477 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

The user is asking about my code review philosophy: how I mark feedback, what I prioritize, and how I handle disagreements with the author. This is a continuation of a job interview conversation about PHP backend development.

Let me think about what makes a genuinely useful answer here, rather than a generic "be nice" response. I should:

1. Be concrete about my marking system (not just "I use nit:" — explain the full system and why)
2. Be honest about what I actually look at first (not the textbook "correctness, performance, style" — but the real order that catches the most bugs)
3. Be specific about disagreement resolution — not "I discuss it" but the actual mechanism, including when I concede and when I hold firm
4. Include the meta-skill: what I look for that ISN'T in the diff (missing tests, missing edge cases, architectural smell)
5. Be honest about my own failures in reviews

I should avoid:
- Generic "be respectful" platitudes without substance
- A numbered list that reads like a textbook
- Being so diplomatic that it's useless
- Pretending I never miss things in reviews

The user has been getting detailed, opinionated, concrete answers throughout this conversation. I should match that tone. This is a senior developer interview — they want to hear a real process, not a HR script.

Let me structure this as:
1. My marking system (concrete, with examples of what each label means in practice)
2. What I look at first (the real priority, not the textbook one) — including what I look for that ISN'T in the diff
3. Disagreement — the actual mechanism, with a concrete example of a time I was wrong and a time the author was wrong
4. What I DON'T do (anti-patterns in reviewing)
5. The meta-point: what a good review achieves vs what a bad one achieves

Let me write this in Russian to match the conversation.

</details>

**Кандидат:**

## Система маркировки

Я использую 4 уровня. Не 7, не 10 — именно 4, потому что больше 4 уровней ревьюер начинает **думать** о маркировке вместо **чтения** кода.

| Маркер | Значение | Автор обязан |
|--------|----------|:------------:|
| `[blocker]` | Брак. Код сломан, небезопасен, нарушает контракт. | Ответить до merge |
| `[must]` | Нужно исправить, но не блокирует merge. Можно обсудить. | Ответить до merge |
| `[nit]` | Предпочтение. Я бы сделал иначе, но ваш вариант ок. | Не обязан |
| `[question]` | Не понимаю. Объясни, пожалуйста. | Ответить (или я отзову) |

### Примеры

```diff
+    public function process(Order $order): void
+    {
+        $total = 0;
+        foreach ($order->items() as $item) {
+            $total += $item->quantity * $item->price;
+        }
```

```
[blocker] Здесь целочисленное умножение. Если quantity=10000, price=99999 —
overflow int (PHP int = 64-bit, но на 32-bit платформе переполнится).
И главное: это дублирует OrderItem::lineTotal(). Почему не
$item->lineTotal()? У нас есть метод — используйте его.
```

```diff
+        if ($total > 1000000) {
+            $this->logger->info("Big order: " . $order->id);
```

```
[nit] Логирование "большого заказа" — это бизнес-правило.
Лучше вынести в Order::isLarge() и проверять там.
Не обязательно сейчас, но стоит иметь в виду.
```

```diff
+        $client = new \GuzzleHttp\Client(['timeout' => 30]);
```

```
[question] Почему 30 секунд? Где это число отсюда?
Если это SLA платёжного шлюза — зафиксируйте константой с комментарием.
Если это "мне так кажется" — давайте обсудим.
```

### Чего я НЕ делаю

| ❌ | Почему |
|----|--------|
| `// TODO: fix later` | TODO в ревью = "я не хочу разбираться сейчас". Это не решение. |
| `[nit]` на каждое `use` | Форматирование — задача линтера, не ревьюера. |
| `LGTM` без прочтения | Это не ревью. Это "я не против". |
| 15 `[nit]` подряд | Перегружаю автора. Беру 3 самых важных, остальные — "на ваше усмотрение". |
| `[blocker]` на стиль | "Не нравится как называется переменная" — это `[nit]`, не `[blocker]`. |

---

## Что смотрю в первую очередь

Не в порядке "correctness → performance → style". В **реальном** порядке, в котором я читаю diff:

### 1. "Что я НЕ вижу" (30 секунд)

Прежде чем читать код, я смотрю на **окружение**:

| Вопрос | Где |
|--------|-----|
| Есть ли тесты для нового поведения? | `tests/` |
| Обновлены ли существующие тесты? | `tests/` |
| Миграция в PR'е? | `migrations/` |
| API-контракт изменён? | `docs/api.md` или `openapi.yaml` |
| Конфигурация изменена? | `config/` |
| Зависимости обновлены? | `composer.json` |

**Почему:** самый частый баг в PR — не "код неправильный", а "забыли добавить тест / миграцию / документацию". Я вижу это за 30 секунд, не читая код.

### 2. Границы (1–2 минуты)

```php
// src/Interface/Http/OrderController.php
+    public function cancel(Uuid $id, Request $request): JsonResponse
+    {
+        $reason = $request->query->get('reason', 'other');
+        $this->em->getRepository(Order::class)->find($id)->cancel($reason);
```

Вопросы:

- **Кто вызывается?** Controller → EntityManager напрямую. Граница Application layer пропущена.
- **Что происходит с exception?** Не обрабатывается. 500 вместо 409.
- **Где авторизация?** Нет `#[IsGranted]`. Любой пользователь может отменить любой заказ.

Это не "стиль". Это **архитектурная дыра**.

### 3. Контракт и инварианты (2–5 минут)

Читаю **доменную логику** и спрашиваю:

| Вопрос |
|--------|
| Что произойдёт, если `items` пуст? |
| Что произойдёт, если `quantity = 0`? |
| Что произойдёт, если два запроса одновременно? |
| Что произойдёт, если `price` отрицательный? |
| А если `currency` другой? |

Я не перечисляю все edge cases. Я ищу **один** баг, который авторы не увидят. Обычно он в:
- Границах: `0`, `null`, пустой массив.
- Конкурентности: два запроса, одно состояние.
- Переполнении: `int` overflow, `strlen > 255`.

### 4. Исключения (1–2 минуты)

```php
+    try {
+        $gateway->charge($order);
+    } catch (\Exception $e) {
+        $order->setStatus('failed');
```

| Вопрос |
|--------|
| `catch (\Exception)` — что именно будет поймано? `TimeoutException`? `AuthenticationException`? Разные причины → разные действия. |
| `setStatus('failed')` — а `reason`? Без причины отладка невозможна. |
| Транзакция? Если `setStatus` не в той же транзакции, что и `charge` — рассинхрон. |

### 5. Производительность (если применимо)

Не каждый PR. Но если вижу:

```php
+    foreach ($this->repo->findAll() as $order) {
+        $order->items()->count();
```

— это N+1. Не `[blocker]`, но `[must]`: "Покажите, что это не в hot path. Или сделайте fetch join."

### 6. Имена и читаемость (последнее)

Только если код **понятен**. Если логика верная, но переменная `$x` — это `[nit]`. Если логика неправильная — это `[blocker]`, и имя неважно.

---

## Разногласие с автором

### Ситуация

Автор пишет:

> "Я специально не делаю через UseCase, потому что тут 3 строки. Зачем создавать класс?"

Я считаю, что нужен UseCase. Автор считает, что нет.

### Что я НЕ делаю

| ❌ | Почему |
|----|--------|
| "Но по SOLID..." | Это не аргумент. Это ссылка на книгу. |
| "Всегда так делают" | "Всегда" — это "я не думал, просто сделал как в прошлый раз". |
| "Я более senior, мой вариант правильный" | Иерархия не аргумент. |
| "Ладно, сделай как я сказал" | Это не решение. Это подавление. |

### Что я делаю

**Шаг 1: Сформулировать своё беспокойство конкретно.**

> "Мне не нравится, потому что я вижу здесь 3 вещи, которые через 2 месяца будут меняться по-разному:
> 1. Логика отмены (сейчас — 3 строки, но через месяц — проверка инвентаря).
> 2. Обработка ошибок (сейчас — нет, но через месяц — нужен retry).
> 3. Событие (сейчас — нет, но через месяц — нужно уведомить склад).
>
> Если все три появятся в контроллере — будет 50 строк в контроллере.
>
> Я бы сделал UseCase, чтобы каждый из этих 3 пунктов был в своём месте."

Я не говорю "нужно UseCase". Я говорю **"что я вижу"** и **"что будет через 2 месяца"**. Это аргумент, а не директива.

**Шаг 2: Дать автору пространство.**

> "Если ты думаешь, что 3 строки в контроллере — это ок на сейчас, и ты готов через 2 месяца вынести — я закрою замечание. Но я бы отметил в PR, что это осознанное решение."

Я не "отступаю". Я **даю выбор**. Если автор выбирает свой вариант — это его код, его ответственность. Но решение **зафиксировано**.

**Шаг 3: Если автор не согласен и я не согласен — эскалация.**

Не "давай спросим тимлида". А:

> "Давай так: я напишу свой вариант в 5 строк (PR comment). Ты напишешь свой. Если через 10 минут не договоримся — покажем одному из нас, и пусть он скажет, какой вариант лучше для проекта. Но до этого — аргументы, не авторитет."

### Когда я уступаю

| Ситуация | Почему уступаю |
|----------|---------------|
| Я не знаю контекст | Автор знает, что через неделю будет другая фича. Я — нет. |
| Разница в 10% | "Мой вариант на 10% чище, но требует 2 часа переделки" — не стоит. |
| Я не уверен | "Мне кажется, что..." — это не аргумент. Я отзову `[must]` в `[nit]`. |
| Автор прав, но я упрямлюсь | Это эго. Признать: "Ты прав, я был не прав." |

### Когда я НЕ уступаю

| Ситуация | Почему |
|----------|--------|
| Безопасность | `catch (\Exception)` на платёжном пути — не обсуждается. |
| Данные | `DROP COLUMN` без 2-этапной миграции — не обсуждается. |
| Контракт | API меняет формат без `@deprecated` — не обсуждается. |
| Тесты | "Добавлю потом" на критическом пути — не обсуждается. |

---

## Антипаттерны в ревью

| Антипаттерн | Почему плохо |
|-------------|-------------|
| Ревью в 23:00 | Усталость = пропускаю баги. Лучше "утром". |
| Ревью на 1500 строк за 10 минут | Я не читал. Я "отметил". |
| "LGTM" + 3 `[nit]` | Я не знаю, что "[nit]" — это блокер или нет. Я хочу знать. |
| Ревью через 4 дня | Контекст потерян. Я перечитываю, а не читаю. |
| "Это не моя зона ответственности" | Я вижу баг. Я его пишу. Зона ответственности — это для задач, не для багов. |
| Ревью в чате, а не в PR | Нет истории. Через месяц не будет видно, почему так решили. |

---

## Что я ценю в авторе

| Качество | Пример |
|----------|--------|
| Обоснование | "Я сделал X вместо Y, потому что Z" |
| Честность | "Не уверен, что это правильно — можно посмотреть?" |
| Реагирование | "Согласен, поправил" / "Спорю, потому что..." |
| Разбивка | 3 PR'а по 150 строк, а не 1 на 500 |
| Тесты | Не "добавлю потом", а в том же PR'е |

---

## Итог

Хороший код-ревью — это **не** "проверка на ошибки". Это **диалог** о коде, в котором:

1. **Автор** знает, что делает и почему.
2. **Ревьюер** ищет то, чего автор не видит (контекст, границы, будущее).
3. **Разногласие** решается аргументами, не авторитетом.
4. **Решение** фиксируется в коде, а не в чате.
5. **Результат** — код, который оба понимали. Не код одного, который другой "принял".

Если после ревью автор говорит "спасибо, я понял" — ревью было хорошим.
Если автор говорит "спасибо, я исправил" — ревью было эффективным.
Если автор говорит "спасибо, но я не понял, что именно не так" — ревью было **плохим**. И это моя вина, не автора.

> ⏱ 48.3s · prompt 105693 tok · output 3451 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

We need answer user in Russian. Need solve: formulate task from raw business problem: users can't find past orders, support overloaded. Need produce task for executor, no clarifications. What must include and why. Need think as PHP backend interview. Need likely include: context, goal, user story, scope, acceptance criteria, data constraints, UI/API, search/pagination, permissions, performance, edge cases, metrics, out of scope, success criteria, technical constraints, testing, rollout. Need maybe explain why raw problem insufficient. Need formulate concrete task. Need be careful: problem is ambiguous: "past orders" could mean historical orders beyond retention, orders from old system, canceled, orders not shown due to pagination/filter, search missing, account merging, B2B vs C, multi-tenancy. Need choose assumptions? The prompt says formulate so it can be executed without clarifications. We need include assumptions explicitly, decisions, defaults. Maybe propose a task for backend developer: implement customer order history search and retrieval. Need include acceptance criteria. Could be in form of ticket. Need mention if ambiguous, make decisions and state assumptions, but also ask to validate assumptions with stakeholder? User wants "так, чтобы её можно было выполнить без уточнений". So we need produce a complete task, perhaps with assumptions that convert ambiguity into requirements. Include mandatory elements and why. Need likely answer in Russian, structured.

Need consider: We are in interview, likely expected to show product thinking, decomposition, acceptance criteria, measurable outcome, constraints. Need not just list; include example formulated task. Need include "что обязательно включишь и почему". Need maybe include risks: privacy, GDPR, data availability, performance, permissions. Need specify: who (end customer, support agent), what (orders from past, including older than 6 months, canceled, archived), where (customer portal /admin?), how (date range filter, search by order number, email, product, amount range), what returns (list, detail), limits (pagination, max results), performance (p95 < 300ms for list, 500ms detail), data (orders table, retention, soft deletes), success (support tickets down by X, search success rate, time to find order). Need include non-goals: not migration from old system unless specified, not redesign. Need include edge cases: user with multiple emails, B2B customer, canceled/refunded orders, orders with no items, PII masking, locale/timezone, amount currency, order number format, empty state. Need include QA/test scenarios. Need include rollout/monitoring. Need include access control: user sees only own; support sees with audit; admin all. Need include search by order number exact and fuzzy? To avoid ambiguity define exact match first, then prefix, then email? Need define "past orders" as all orders in status delivered/canceled/refunded older than 30 days? Need define time range default: last 12 months? But business says past. We need choose: full order history available from account creation; default date range last 12 months; advanced search full history up to retention (e.g., 7 years). If no retention specified, assume all orders currently in DB, no deletion. Need mention if data missing from legacy system out of scope? Could include legacy data? The problem says past orders; to execute without clarifications, assume orders exist in `orders` table; if legacy not migrated, add manual fallback? Need avoid too many assumptions? Need state assumptions clearly and make them part of task, e.g. "If orders are not in current DB, implement manual import is out of scope; but support workflow will use search". Hmm.

Need perhaps produce two sections: 1) formulated ticket, 2) why each block. Need likely include "Definition of Done". Need make it executable by developer without clarifications. Need include "Given/When/Then". Need include API endpoints maybe. Since PHP Symfony/Postgres context. We can write a backend task: implement `GET /api/v1/orders/history` with filters. Need include input validation, output shape. Maybe include SQL indexes. Need include performance target and data volume assumption. Need include security/permissions. Need include tests. Need include feature flag.

Need ensure no contradictions. Let's design a robust task:

Title: Give customer ability to find any past order from last 7 years by order number, date range, status, product name, amount range, and email; improve support workflow.

But "without clarifications" requires concrete decisions:
- Audience: authenticated customer, support agent, admin.
- Data source: `orders` table and `order_items`, `customers`. No legacy migration. If order not found in current DB, return `legacy_not_found` and support uses CRM? But maybe too vague. Better assume all orders in current DB; if not, requirement fails? Need maybe include "if orders older than 2023-01-01 are in legacy table `legacy_orders`, read from it read-only; no write". But inventing legacy table maybe okay as assumption? The prompt likely wants show that raw problem needs assumptions. We can include assumptions as decisions: "Assumed: all orders are in current DB; if not, PM must confirm, but for this ticket treat missing data as data gap and log `order.search.missing_legacy`". Hmm "без уточнений" means executor shouldn't ask; if assumption not true, they proceed. We can define fallback.

Need maybe mention "If no search term provided: show latest 20 orders".

Let's craft task:

Context:
- Business problem: users can't find past orders; support overloaded.
- Hypothesis: order history is hard to locate due to limited filters, pagination, missing search by order number/product/date, B2B customers see all company orders mixed, old canceled orders hidden.
- Goal: reduce support tickets about "where is my order" by 30% in 4 weeks; customer can find any order in <= 2 interactions.

User stories:
- As customer, I can find my past order by exact order number within 1 second.
- As customer, I can filter by date, status, amount, product.
- As support agent, I can find order by email or order number and see audit.

Scope in:
1. Search endpoint `/api/v1/orders/search` or improve existing `/api/v1/orders`.
2. Filters: `query` (order number exact/prefix/email/product), `status[]`, `from`, `to`, `min_amount`, `max_amount`, `currency`, `customer_id` (support), `sort`, `page`, `page_size` max 50.
3. Default: current user's orders, last 12 months, status not hidden? Include canceled/refunded/failed? Need define: include all statuses except `draft`; support can include draft/admin internal.
4. Output list fields: id/order_number, created_at, status, total_amount, currency, item_count, first_item_name, channel.
5. Detail endpoint returns items, billing, shipping, timeline, payment status.
6. Access: customer own; support assigned/tenant; admin all with reason? Need specify.
7. Performance: p95 <300ms list for 10M orders, <500ms detail; no N+1.
8. Indexes: order_number unique? customer_id+created_at desc, trigram? For search. Use PG trigram or ILIKE? Need specify: exact order number first, then ILIKE product via GIN if available, else fallback to `LIKE` for first 1000 matches? To be executable: implement deterministic search ranking:
   - if query matches order_number exact -> return one
   - if query matches email -> customer orders
   - if query starts with digit and length >=6 -> order number prefix
   - else ILIKE on order_items.name / product name and order total? Need performance.
Could simplify: search fields: order number, email, product name. Use `pg_trgm` GIN on `order_items.name` if supported; if not, restrict to first 100k? Need not too much.

Need include "No fuzzy, no OCR, no voice".

Acceptance criteria:
- Given customer has 250 orders, when searches exact order number, then result appears first, p95 < 200ms.
- Given date range, returns only orders where created_at in range user timezone? Need choose: UTC stored; filter by UTC day boundaries based on `timezone` parameter default user TZ.
- Given status canceled/refunded/delivered, included.
- Given support agent with email query, returns all orders for that email and audit log entry.
- Given customer queries another customer's order number, returns 403 or empty? To avoid enumeration, return 404/403? Need decide: If order number belongs to different customer, return 403 `forbidden` or 404? For security avoid enumeration: return 403 with generic. But support can. Need specify.
- Given no results, return empty with suggestions? Define empty object `results: []`, `total: 0`.
- Given invalid date, 422.
- Given page size >50, clamp to 50.
- Given missing order in DB but exists in legacy log, log event `legacy_order_requested` and return 404.

Data:
- Store `orders.created_at` timestamptz, `order_number` unique per tenant.
- Index: `CREATE INDEX CONCURRENTLY idx_orders_customer_created ON orders (customer_id, created_at DESC);`
- For query: exact `order_number` unique index; product search GIN `gin(order_items.name gin_trgm_ops)` maybe optional if PG. Need specify migration steps.

API contract:
GET /api/v1/orders/history
Query params...
Response 200 JSON:
{ data: [...], meta: { page, page_size, total, from, to } }

Error codes.

Non-functional:
- Logs include order_id, user_id, query type, latency; no PII in logs except masked email/order number? Need define: log order_number masked? We can say order number can be logged, email masked.
- Rate limit: 10 requests/10s/customer, 30/10s/support.
- Monitoring: alert if p95 > 1s or 4xx spike.

Rollout:
- Feature flag `orders_history_search_v2` off; enable 5% then 100%.
- A/B? success metrics.

Out of scope:
- Legacy data migration from CSVs.
- Full-text search across all customer messages.
- Refund/return workflow.
- Mobile native UI changes (API only; frontend separate ticket).

Definition of Done:
- Code merged, tests unit/integration, migrations, docs, feature flag, monitoring, load test, support runbook.

Why each:
- Goal/metrics: to know done.
- Assumptions: remove ambiguity.
- Scope: prevents overbuild.
- AC: executable and testable.
- Security: prevent data leak/enumeration.
- Performance: business complaint may be due slowness.
- Observability: to verify.
- Rollout: risk.

Need maybe mention "not just 'make search'". Need include "task for executor" maybe as a Jira ticket. Need ensure no "clarifications" by making decisions. Need include "what I will include and why" perhaps after. Need maybe include "If assumptions are false, what to do"? To be executable without clarifications, define defaults and fallback. E.g. if data older than 2020 not in DB, out of scope; support manual process. But if business needs those, ticket would be wrong. But as executor, proceed with current DB.

Need perhaps address "support overloaded" not only customer self-service: include support dashboard endpoint and audit. Need maybe support can use same search with broader permissions. Good.

Need likely say "I would not start coding until this ticket is accepted by PO". But user asks formula. Need present as if I write ticket.

Need ensure final answer not too long but desired oververbosity 9. Use Russian. Need include concrete task. Let's structure:

1. Коротко: raw problem -> hypothesis + measurable goal + scope.
2. Готовый текст задачи (ticket) with sections:
   - Назначение / Background
   - Цель и успех
   - Допущения (важно)
   - Пользовательские истории
   - Вх/вых (in scope/out)
   - Функциональные требования (API, filters, defaults, permissions, output, edge cases)
   - Нефункциональные (perf, security, logs, rate limit)
   - Данные и миграции
   - Acceptance criteria (Given/When/Then)
   - Definition of Done
   - Роллаут и откат
   - Риски
3. Что обязательно включишь и почему (mapping)
4. Почему так, а не иначе (maybe brief).

Need maybe include a sample API contract and SQL index. Need maybe include "executor can execute without clarifications" by stating all decisions.

Need be careful with "past orders" - define "past" as all non-draft orders older than 30 days? Maybe better: "past orders" = orders with created_at earlier than current date minus 24h? But user likely means history, not future. Let's define: include all orders except `draft` and `new`? Hmm "past orders" maybe delivered/canceled. We need include all statuses that customer may ask: delivered, canceled, refunded, failed, on_hold. Include `draft`? No, drafts not real. Define statuses included: `confirmed`, `processing`, `shipped`, `delivered`, `canceled`, `refunded`, `failed`. Exclude `draft`, `internal_hold`? Need choose. Let's say include all statuses except `draft`; support can include `draft` with role.

Need define "search query" behavior precisely:
- `q` optional.
- If `q` is non-empty:
  1. exact order_number match (case-sensitive? order number likely alphanumeric; define exact case-insensitive)
  2. if exact not found and q contains email -> orders of that customer (for support only; customer only own? For customer if q email of self? Could allow only if matches their verified emails; otherwise no results to avoid enumeration)
  3. if q matches 6+ digits -> order number prefix
  4. else product name ILIKE on joined `order_items.name` / product name, limited to first 1000 candidate orders.
But to avoid complexity, maybe simplify: `q` searches exact order number and product name (first 100 matches). Need precise ranking:
  - exact order number always first.
  - product name substring case-insensitive.
No fuzzy.
Need specify: If multiple orders match product, sort by created_at desc, limit 50 per page.

Could be too much. But "no clarifications" requires exact. Need perhaps define: `q` only exact order number and product name via `ILIKE %q%`. Product search only for support? For customer? If customer searches product, join order_items. That's okay.

Performance: ILIKE `%x%` is slow. Need specify use `pg_trgm` GIN index; if not possible, limit query to orders from last 24 months? But business wants past. We can decide: product search uses `pg_trgm` GIN index on `order_items.name` and `orders.order_number`; max scan? Hmm. We can state required: install `pg_trgm`, create GIN indexes; query must use index; if not, restrict to order number exact only and product search disabled. But for executor, choose deterministic: Use `pg_trgm` for product search; if extension unavailable, product search disabled and API returns 200 with `q` only exact order number. That is executable.

Maybe simpler: Search only exact order number and email (for support). Product name search is out of scope to avoid heavy full-text. But user complaint "find orders" likely exact order number/date. We can include product search optional but defined. Need keep task manageable.

Need maybe define UI? Since backend, API. But business says users can't find; maybe frontend too. The task for executor could be backend. Need specify frontend separate? "For executor" maybe backend developer. We can formulate for backend developer. Include API contract for frontend. If full-stack, include UI acceptance? I can say "This ticket is for backend; UI ticket is separate but must implement this API". But to execute without clarifications, include expected user outcome. Maybe better: "Backend/API task; UI is in separate ticket, but these API requirements are binding". Good.

Need include "support runbook".

Need ensure "why" section maps items to reasons.

Let's draft final in Russian.

Potential final:

"Я бы не формулировал 'сделайте поиск заказов'. Я бы сделал задачу: 'Реализовать поиск и историю заказов с гарантией найти заказ по номеру, дате, статусу; сократить обращения в поддержку'. Below ticket."

Ticket:

**Задача: Поиск истории заказов клиентом и поддержкой**

Background: ...
Goal: ...
Success metrics: ...
Assumptions: ...
Scope: ...
Not scope: ...

Functional requirements:
1. Endpoint `GET /api/v1/orders/history`
2. Permissions
3. Filters and defaults
4. Search semantics
5. Sorting and pagination
6. Response fields
7. Error handling
8. Legacy missing orders

Data/DB:
- tables, indexes, migrations.

Non-functional:
- performance, security, logging, rate limit.

Acceptance criteria: list 10.

Definition of Done.

Rollout.

Then "Что обязательно включишь и почему" table.

Need maybe include "Assumptions" as decisions:
- All orders in `orders`; statuses list; time zone; retention 7 years; no legacy import; customer owns `customer_id`; B2B customer sees all orders for their customer account; support can search by email; PII masked; exact order number unique per tenant.

Let's define data model:
`orders`: id, customer_id, order_number, status, created_at, total_amount, currency.
`order_items`: order_id, name.
`customers`: id, email, user_ids? Need maybe customer has multiple users. For customer access: `auth_user_id` or customer_id. Need define: customer token includes `customer_id`; B2B all users with same customer_id see same orders. If personal accounts, `customer_id` is owner.

API details:
`GET /api/v1/orders/history`
Params:
- `q` string optional (1-128)
- `status` enum optional multiple? Use `status=delivered&status=canceled`.
- `from` ISO 8601 date optional (YYYY-MM-DD)
- `to` ISO 8601 date optional
- `min_amount` integer cents optional
- `max_amount` integer cents optional
- `currency` ISO optional
- `sort` `created_desc` default, `created_asc`, `amount_desc`
- `page` int default 1
- `page_size` int default 20 max 50
- `email` support only optional.
Maybe too many. Need include only necessary. But for no clarifications, define all.

Defaults:
- if no q/status/date: last 12 months, sort created desc, page 1, page_size 20.
- date filter: inclusive day in user timezone? Simpler: UTC inclusive. But customer expects local. Could define: `from`/`to` are dates in customer's timezone if provided; if not, default last 12 months in UTC. Need not overcomplicate. Use `from` and `to` as full ISO timestamps UTC to be precise. For UI, frontend sends timestamps. But for executor, specify UTC. Good.
- Amount in minor units (cents/kopecks). `min_amount`/`max_amount` integers.
- Currency optional; if absent and amount filters present? If currency absent, compare amounts only when currency same? Better: if `currency` absent and amount filters present, filter all currencies by numeric amount? That's dangerous. Define: amount filters apply only if `currency` provided; if `currency` absent, amount filters ignored and response `meta.filters.amount_ignored=true`. Hmm. Need clear: require `currency` if amount filter used; else 422. Simpler: `min_amount`/`max_amount` require `currency`. If not provided, 422. Good.

Search semantics:
- `q` trimmed, case-insensitive.
- If q length >= 5 and contains only alphanumeric/hyphen, try exact order number. If exact match, return only that order if accessible.
- Else if q matches email pattern and user is support, search customer email (exact) and return their orders.
- Else if q length >= 3, product name substring search in `order_items.name` (last 36 months? all? Use trgm; if unavailable disabled). To avoid performance: product search limited to last 24 months and max 1000 candidate orders. But business wants past; define last 7 years if indexes ready. Let's set: product search across full history if `pg_trgm` GIN available; else disabled. But to guarantee, require DBA to create GIN. We can specify acceptance: product search for 10M orders p95 <500ms. Hmm.
Maybe simplify: `q` only exact order number. Then product search out of scope. But then "find by product" not included. Business might need. However support overloaded by users not finding; exact order number and date likely enough. We can include product search as optional extension, but task should be executable. Could define as "if available". But no clarifications? Need deterministic.

Let's choose robust but manageable: `q` searches:
1. exact order number (case-insensitive)
2. customer email (support only)
3. product name exact word/substring? Let's define substring for product name but limited.
Need specify ranking: exact order number has priority. If exact order number found, no other search.
If no exact order number:
- support email search if q contains @.
- product name substring (case-insensitive) if length >=3.
Sort by created_at desc.
Limit 50/page.
This is clear.

Edge: product name search can be expensive. We can require:
- Use `pg_trgm` GIN index on `order_items.name` and `orders.order_number`.
- If `pg_trgm` not available, product search disabled; API still works with exact order number. Acceptance: on target DB with extension, p95 <500ms.
That's okay.

Permissions:
- Customer: only orders where `orders.customer_id` equals customer's primary customer_id, OR if B2B all users under same customer_id. Need define: token has `customer_id`; no cross-tenant. If query exact order number of other customer, return 403? To avoid enumeration, return 404? Let's choose 404 to not reveal existence? But 403 reveals. Security best: return 404 with generic. However support needs know. For customer, if exact order number not own, return 404 `order_not_found`. For product search, only own. For support: if q email, return orders for that email. Support can search by order number across all; if not found 404. Admin all.
Need specify audit for support searches: log `order_history.search` with support_user_id, q_type, order_ids count, masked q. Not log full q if email? Mask. Good.

Rate limit: 20 requests/min per user, 60/min support. 429.

Performance targets:
- 250 orders per customer list default p95 < 300ms.
- exact order number p95 < 150ms.
- product search p95 < 500ms.
- No N+1: one query for list (join items for product search? Need aggregate). Need specify: list must not load all items; item_count from subquery or denormalized `item_count` column? To be executable: maintain `orders.item_count` and `orders.first_item_name` denormalized? Maybe simpler: list returns `items_count` and `first_item_name` from `orders` table if exists. But if not, need aggregate. To avoid N+1, add denormalized columns `item_count`, `first_item_name` updated in application service. This is more robust. Include in data requirements: ensure `orders.item_count`, `orders.first_item_name` are populated; if missing, add migration. Good.
But maybe too much. Could accept list without item details: `id`, `order_number`, `status`, `total`, `currency`, `created_at`, `item_count`. `item_count` can be subquery but for 50 rows okay? N+1 if 50 orders each count query. Better denormalize. Define: add `item_count` integer not null default 0, `first_item_name` text. Update on order item changes. Good.

Data migration:
- add indexes:
```
CREATE INDEX CONCURRENTLY idx_orders_customer_created ON orders (customer_id, created_at DESC);
CREATE INDEX CONCURRENTLY idx_orders_order_number ON orders (order_number);
CREATE INDEX CONCURRENTLY idx_orders_status_created ON orders (status, created_at DESC) WHERE status <> 'draft';
CREATE INDEX CONCURRENTLY idx_order_items_name_trgm ON order_items USING gin (name gin_trgm_ops);
```
- populate `item_count`, `first_item_name`.

Error handling:
- 400 invalid q? 422 invalid date/amount.
- 403 forbidden? For customer cross-order, use 404 to avoid enumeration. Need consistent: For support no. If support lacks permission for tenant, 403.
- 500 unexpected.

Monitoring:
- metric `order_history_search_total{status, query_type}`
- latency histogram
- alert p95 > 1s for 5m, error rate > 1%.
- log event when exact order not found: `order_history.not_found`.

Success metrics:
- support tickets with keywords decrease 30% in 4 weeks.
- Search success: proportion of searches with at least one result? Need baseline. Could define: 80% of customer searches return at least one order? But may be too specific. Use: median time from first search to detail page < 10s? Hard. Better: support tickets about "find order" -30%; NPS? We can include baseline from support logs. If no baseline, measure 1 week before.

Rollout:
- Feature flag, 5% canary, 24h, then 100%.
- Killswitch: disable product search or whole endpoint fallback to legacy list.

Out of scope:
- Migration from legacy CSV/Excel.
- Full-text search of comments/invoices.
- Bulk export >1000 orders.
- UI design; backend contract only.

Acceptance criteria: Need write 12 Given/When/Then.

1. Given customer C has orders with statuses delivered/canceled/refunded, when GET /orders/history, then 200 includes them, excludes draft.
2. Given customer C, when q=exact order number O belonging to C, then 200 first result O.
3. Given customer C, when q=order number of another customer, then 404.
4. Given support S, when q=email, then 200 orders for that email and audit log.
5. Given from/to, when orders created outside range, excluded; inclusive boundaries.
6. Given page_size=100, then clamped to 50 and meta.page_size=50.
7. Given invalid date, 422.
8. Given min_amount/max_amount without currency, 422.
9. Given product name search q=product, then orders containing product in items, sorted desc.
10. Given 10M orders, exact order p95 <150ms; product search p95 <500ms.
11. Given feature flag off, endpoint returns 404 or legacy? Define: flag `order_history_v2` false -> old `/orders` still available; new endpoint 404. Good.
12. Given support search, audit log contains masked q and result count.

Definition of Done:
- migrations forward-only, indexes concurrently, no lock >5s.
- tests unit/integration; load test report.
- API docs updated.
- feature flag default off.
- monitoring and runbook.
- no secrets, no PII in logs.

Why include: table.

Need maybe include "What I would explicitly not include: 'make search faster' without target; 'fix support queue' without measurable target; 'all past orders' without definition; 'users' without roles." Good.

Need final answer with enough detail.

Let's ensure no hidden issue: The user asks: "Что для тебя хороший код-ревью..." already answered. New question: "Вот сырая проблема от бизнеса... Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировке и почему?" So answer should likely focus on formulation. Could include "я бы сначала задал 5 уточнений" but they want no clarifications. We can say "чтобы не задавать уточнений, я фиксирую допущения". Good.

Need maintain interview tone. Need maybe mention "I would convert complaint into hypothesis and measurable goal". Good.

Let's draft final in Russian. Need be thorough.

Potential final:

"Ниже — как я бы записал задачу. Я сознательно превращаю расплывчатую фразу в проверяемые требования и принятые решения."

Then ticket.

Make sure "without clarifications" by all items concrete. Need not ask questions. But can include "Assumptions" as fixed. Good.

Let's write.

Need maybe include "исполнитель" could be backend dev. We'll state "Backend developer". If executor is full-stack, still API contract.

Need include "Если данные отсутствуют в БД — это не блокер, а logged event and 404". Good.

Let's make acceptance criteria precise.

Potential API response:
```
{
  "data": [
    {
      "order_number": "12345",
      "id": "8f...",
      "status": "delivered",
      "created_at": "2024-11-02T10:15:00Z",
      "total_amount": 14900,
      "currency": "RUB",
      "item_count": 3,
      "first_item_name": "Bread"
    }
  ],
  "meta": {"page":1,"page_size":20,"total":143,"query_type":"order_number"}
}
```
Need include total? Counting total can be expensive. But for UX. Define `total` only for exact order/email/date? For product search maybe `total` limited. To be clear: `total` is exact count for filters, but may be expensive. We can say `total` provided; if >10000, return `total: 10000, total_truncated: true`. This avoids performance. But for acceptance, maybe no total needed. Simpler: include `total` if not product search; for product search include `total` capped at 1000. Hmm.

Let's define: `meta.total` is number of results if query type `order_number` or `email` or no product search; for product search, `total` is capped at 1000 and `total_truncated` true if more. Good but maybe overkill. Could omit total. Pagination needs `has_next_page`. Use `next_page` nullable. Better for performance.

Response meta: `page`, `page_size`, `next_page` (nullable), `query_type`. No total. Good.

Need include `next_page` in acceptance? Maybe.

Define sort: `created_desc` default; `created_asc`, `amount_desc`. If amount_desc, require currency? If multiple currencies, sorting by amount across currencies is meaningless. Define: `sort=amount_desc` allowed only with `currency` parameter; else 422. Good.

Status enum: list. Need include exact statuses? Use examples: `new`, `confirmed`, `processing`, `shipped`, `delivered`, `canceled`, `refunded`, `failed`. Exclude `draft`. If actual statuses differ, code uses app's status enum? To be no clarifications, define canonical list and map existing statuses? Hmm. Could say "included statuses are all except `draft`; if existing statuses are named differently, use mapping provided in `docs/order-statuses.md`; if no mapping, include all statuses with `visible_to_customer=true`". That introduces dependency. Better define: "All statuses except `draft` and `internal_hold`". Good.

Need define "past orders" = `created_at` before current date, but include recent? We can include all. Default last 12 months. Good.

Need define "support overloaded": support endpoint and audit. Good.

Need include "support can filter by `email` and `customer_id`". Good.

Let's create final.

Potential "Why" section:

| Элемент | Зачем |
... Need cover: goal/metrics, assumptions, scope, roles, API contract, data, AC, non-functional, DoD, rollout.

Need maybe include "what I deliberately don't include" to show no gold plating.

Let's produce final with headings.

Double-check for contradictions:
- If customer searches exact order number of another customer, 404. If product search could leak other customer's products? No, filter own customer_id.
- Support email search: if email not found, 404? For support, return 404. Good.
- `q` with @ for customer: Should not search email unless matches own verified email? To avoid complexity, say for customer email search is disabled; only exact order number and product. For support email enabled. Good.
- Product search with q length >=3. If q length 2? Disabled, return 422? Define: if q < 3 and not order number pattern, 422 `query_too_short`. Good.
- Order number pattern: maybe 5+ chars. Define: q of 5-32 chars `[A-Za-z0-9-]` treated as order number exact/prefix? To avoid accidental. Let's define: if q matches `^[A-Za-z0-9-]{6,32}$`, exact order number first. If exact not found and length >=6, prefix search? Maybe no prefix to avoid performance. Just exact. If exact not found and q has @ support email. Else product substring if length >=3. Good.

Need include "prefix"? Not necessary. Exact order number is common. But user may remember partial. Could include prefix if 6+ digits. But performance? order_number index prefix works. Define: if order number pattern and exact not found, prefix `q%` up to 100 matches. Good. But may leak? For customer only own; support all. Fine. Need keep simple: exact only? To be robust, include prefix for order number. Let's specify: order number search supports exact and prefix of at least 6 chars. Ranking exact first, then prefix by created_at desc. This is clear.

Need include product search with `pg_trgm`. Good.

Need include load test data volume: 10M orders? Need define assumption: current DB has up to 5M orders and 25M items. Performance target. Good. If actual bigger, target relative. To be executable, set: p95 exact <=150ms at 10M orders; if current data >10M, same target. Good.

Need include "no N+1" and indexes.

Let's write final answer elegantly.

Need ensure not too huge but oververbosity 9. We'll include all.

One more nuance: "без уточнений" could mean don't include open questions. So avoid "if actual statuses differ". Use fixed assumptions. But in reality assumptions might be false. We can state "These are decisions for this ticket; if business disagrees, task is rejected before implementation." That's okay.

Let's draft final:

Start: "Я бы начал с того, что жалоба — это симптом. Задача должна превращать симптом в проверяемый результат."

Then ticket.

Use Markdown.

Let's ensure final answer is in Russian.

Need include "что обязательно включишь и почему" maybe after ticket as table.

Ok final.

</details>

**Кандидат:**

Я бы не стал передавать в работу формулировку «пользователи не могут найти заказы». Это жалоба, а не задача. Чтобы её можно было выполнить без уточнений, я превращаю её в:

1. **цель**, которую можно измерить;
2. **предполагаемую причину**;
3. **конкретный функционал**;
4. **границы ответственности**;
5. **критерии приёмки**;
6. **нефункциональные требования**;
7. **решение по рискам и откату**.

Ниже — как я бы записал эту задачу для бэкенд-исполнителя.

---

# Задача

## Название

**Реализовать поиск и историю заказов для клиента и поддержки**

## Контекст

Пользователи жалуются, что не могут найти свои прошлые заказы. Поддержка получает много обращений с запросами вроде:

- «Где мой заказ за март?»
- «Не нахожу заказ по номеру»
- «Я делал возврат, а заказ пропал»
- «У меня два аккаунта, где история?»

Предполагаемая причина:

- у пользователя ограничен поиск по истории заказов;
- отчёты/история показывают только последние 20–50 заказов;
- нет поиска по номеру заказа;
- нет фильтра по дате/статусу/сумме;
- отменённые и возвращённые заказы не видны;
- поддержка не может быстро найти заказ по email или номеру.

## Цель

Сделать так, чтобы пользователь сам находил свой заказ, а поддержка — находила заказ клиента без дополнительных обращений.

## Метрика успеха

Задача считается успешной, если через 4 недели после релиза:

- обращения в поддержку по ключевым фразам «не могу найти заказ», «где мой заказ», «история заказов», «пропал заказ» снижаются минимум на **30%**;
- **90%** поисковых запросов клиента возвращают результат за **< 500ms** по p95;
- поиск по точному номеру заказа работает за **< 150ms** по p95.

Если метрику нельзя измерить из-за отсутствия аналитики, это тоже входит в задачу: добавить события в логи/метрики.

---

# Обязательные допущения

Чтобы исполнитель не задавал вопросы, фиксируем следующие решения:

1. **Заказ** — это запись из таблицы `orders`.
2. **История заказов** — все заказы клиента, кроме статуса `draft`.
3. **Прошлые заказы** — заказы с датой создания раньше текущей даты, включая отменённые, возвращённые, доставленные, failed, refunded.
4. **Один клиент** — это `customer_id`. Если у клиента несколько email или несколько логин-аккаунтов, все заказы принадлежат `customer_id`, а не `user_id`.
5. **Поддержка** — роль `support` или `admin`. Поддержка может искать по `email`, `customer_id`, номеру заказа.
6. **Клиент** видит только заказы своего `customer_id`.
7. **Если заказ не найден в текущей БД** — возвращается `404`, дополнительно пишется метрика `order_history_search_not_found`. Отдельная миграция из legacy/Excel/архивов в рамках этой задачи **не требуется**.
8. **Поиск по товару** — это substring-поиск по названию товара из `order_items.name`, без fuzzy search.
9. **Поиск по email** доступен только поддержке. Клиент не ищет по чужим email.
10. **Время** хранится в UTC. API принимает ISO-8601.
11. **Суммы** хранятся в минорных единицах: копейки, центы.
12. **Страница** — до **50** результатов. Значение больше 50 обрезается до 50.
13. **По умолчанию** возвращается 20 последних заказов за последние 12 месяцев.

---

# Входит в задачу

## 1. Новый API-эндпоинт

`GET /api/v1/orders/history`

Этот эндпоинт должен возвращать список заказов клиента или搜索结果 по условиям поиска.

### Параметры запроса

| Параметр | Тип | Обязательный | Описание |
|---|---:|---:|---|
| `q` | string | Нет | Поиск по номеру заказа, email для поддержки, названию товара |
| `status` | string | Нет | Фильтр по статусу. Можно передавать несколько раз |
| `from` | ISO date | Нет | Дата создания заказа, включительно |
| `to` | ISO date | Нет | Дата создания заказа, включительно |
| `currency` | string | Нет | Валюта, ISO-4217 |
| `min_amount` | integer | Нет | Минимальная сумма в минорных единицах |
| `max_amount` | integer | Нет | Максимальная сумма в минорных единицах |
| `sort` | string | Нет | `created_desc`, `created_asc`, `amount_desc` |
| `page` | integer | Нет | Номер страницы, начиная с 1. По умолчанию 1 |
| `page_size` | integer | Нет | По умолчанию 20, максимум 50 |
| `email` | string | Нет | Только для поддержки. Точный email клиента |
| `customer_id` | UUID/string | Нет | Только для поддержки |

### Правила параметров

1. Если `min_amount` или `max_amount` переданы, но не передан `currency` — возвращать `422`.
2. Если `sort=amount_desc`, но не передан `currency` — возвращать `422`.
3. Если `page_size > 50` — использовать `50`.
4. Если `page < 1` — использовать `1`.
5. Если `from > to` — возвращать `422`.
6. Если `q` передан и его длина меньше 3 символов, а это не похож на номер заказа — возвращать `422`.
7. Если `status` содержит недопустимое значение — возвращать `422`.

---

## 2. Логика поиска по `q`

Поиск выполняется по следующему приоритету.

### Приоритет 1: точный номер заказа

Если `q` похож на номер заказа:

- длина от 5 до 64 символов;
- содержит только буквы, цифры, дефис или нижнее подчёркивание.

Тогда искать по `orders.order_number`.

Поведение:

1. Сначала точное совпадение.
2. Если точное совпадение не найдено и длина `q` от 6 символов — поиск по префиксу `q%`.
3. Для клиента разрешены только заказы его `customer_id`.
4. Для поддержки разрешены все заказы в рамках его tenant/access.
5. Если заказ найден, но не принадлежит клиенту — для клиента вернуть `404`, не `403`, чтобы не раскрывать существование чужого заказа.

Пример:

```text
q = 12345678
```

Должен вернуть заказ с `order_number = '12345678'`, если он принадлежит клиенту.

---

### Приоритет 2: email клиента

Если `q` содержит символ `@`:

- для **клиента** — игнорировать, искать только по товару;
- для **поддержки** — искать заказы по точному `email`.

Пример:

```text
q = john@example.com
```

Для поддержки возвращает все заказы клиента с этим email, отсортированные по `created_at DESC`.

---

### Приоритет 3: название товара

Если `q` не похож на номер заказа и не email:

- длина минимум 3 символа;
- исполняется substring-поиск по `order_items.name`;
- поиск case-insensitive.

Пример:

```text
q = bread
```

Возвращает все заказы, где хотя бы один товар содержит `bread`.

Ограничение:

- если расширениe `pg_trgm` и GIN-индекс недоступны, поиск по товару отключается;
- в этом случае API обязан работать по номеру заказа и email для поддержки;
- в логах писать `order_history_search_product_disabled`.

---

## 3. Ответ API

Формат ответа:

```json
{
  "data": [
    {
      "id": "8f14e45f-ceea-4f7f-9c2a-1a2b3c4d5e6f",
      "order_number": "12345678",
      "status": "delivered",
      "created_at": "2024-11-02T10:15:00Z",
      "updated_at": "2024-11-05T14:30:00Z",
      "customer_id": "11111111-2222-3333-4444-555555555555",
      "total_amount": 14900,
      "currency": "RUB",
      "item_count": 3,
      "first_item_name": "Bread 500g"
    }
  ],
  "meta": {
    "page": 1,
    "page_size": 20,
    "next_page": 2,
    "query_type": "order_number",
    "filters": {
      "from": "2024-01-01",
      "to": "2024-12-31",
      "currency": "RUB",
      "min_amount": null,
      "max_amount": null,
      "status": null
    }
  }
}
```

### Обязательные поля в каждом заказе

| Поле | Обязательно |
|---|---:|
| `id` | Да |
| `order_number` | Да |
| `status` | Да |
| `created_at` | Да |
| `updated_at` | Да |
| `customer_id` | Да |
| `total_amount` | Да |
| `currency` | Да |
| `item_count` | Да |
| `first_item_name` | Да, если есть хотя бы один товар; иначе `null` |

### `meta.query_type`

Допустимые значения:

- `order_number`;
- `email`;
- `product`;
- `filter_only`.

Если использовалось несколько источников, выбрать тот, который дал результат. Если результат собран по фильтрам без `q` — `filter_only`.

---

## 4. Доступ

### Клиент

Клиент видит только заказы, где:

```sql
orders.customer_id = :current_customer_id
```

Или, если модель B2B:

```sql
orders.customer_id IN (:customer_ids_of_current_account)
```

Клиент не должен видеть чужие заказы, даже если знает их номер.

### Поддержка

Поддержка может видеть заказы по:

- `order_number`;
- `email`;
- `customer_id`;
- фильтрам даты/статуса/суммы.

Поддержка обязана оставлять аудит-лог.

### Admin

Admin видит все заказы в рамках своей tenant-зоны. Если multi-tenancy отсутствует — все заказы.

---

## 5. Аудит для поддержки

Каждый поисковый запрос от поддержки должен записывать событие:

```json
{
  "event": "order_history.search",
  "actor_id": "support-user-id",
  "actor_role": "support",
  "query_type": "email",
  "masked_query": "j***@example.com",
  "result_count": 12,
  "requested_at": "2025-01-15T10:00:00Z"
}
```

Правила:

- email маскировать: сохранить первую буклу до `@` и домен;
- номер заказа можно логировать полностью, если политика безопасности это разрешает;
- в лог не писать полные суммы всех заказов;
- событие не должно блокировать запрос.

---

# Нефункциональные требования

## Производительность

Целевой объём данных: до **10 000 000** заказов и **50 000 000** позиций заказов.

### Целевые времена p95

| Сценарий | Цель |
|---|---:|
| Список последних 20 заказов одного клиента | ≤ 300ms |
| Поиск по точному номеру заказа | ≤ 150ms |
| Поиск по email для поддержки | ≤ 300ms |
| Поиск по названию товара | ≤ 500ms |
| Детальная страница заказа | ≤ 500ms |

### Запреты

1. Запрещено делать N+1-запросы при выдаче списка заказов.
2. Запрещено подгружать все товары заказа при выдаче списка.
3. Запрещено использовать `SELECT *` без необходимости.
4. Запрещено делать блокирующие миграции на production-таблицах.

---

# Требования к данным и БД

## Обязательные индексы

Исполнитель обязан проверить наличие и при необходимости создать индексы:

```sql
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_orders_customer_created
ON orders (customer_id, created_at DESC);
```

```sql
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_orders_order_number
ON orders (order_number);
```

```sql
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_orders_status_created
ON orders (status, created_at DESC)
WHERE status <> 'draft';
```

Для поиска по названию товара:

```sql
CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_order_items_name_trgm
ON order_items USING gin (name gin_trgm_ops);
```

Если `pg_trgm` нельзя установить, поиск по товару должен быть отключён, но остальные сценарии должны работать.

## Поля, которые должны существовать в `orders`

Если их нет, исполнитель обязан добавить миграцию:

| Колонка | Тип | Требование |
|---|---|---|
| `order_number` | string | уникальна в рамках tenant, если tenant есть |
| `customer_id` | uuid/string | обязательна |
| `status` | string/enum | обязательна |
| `created_at` | timestamptz | обязательна |
| `updated_at` | timestamptz | обязательна |
| `total_amount` | bigint | обязательна |
| `currency` | string(3) | обязательна |
| `item_count` | int | может быть добавлена, если отсутствует |
| `first_item_name` | string/text | может быть добавлена, если отсутствует |

Если `item_count` и `first_item_name` отсутствуют:

- добавить их;
- заполнить существующими данными;
- поддерживать их при изменении позиций заказа.

Обновление этих полей должно происходить в application-слое, а не в триггере БД, если только команда не приняла иное решение.

---

# Критерии приёмки

## 1. Клиент видит свою историю

**Given** клиент `C` имеет 250 заказов со статусами `delivered`, `canceled`, `refunded`, `failed`  
**When** клиент вызывает `GET /api/v1/orders/history`  
**Then** возвращается `200` и список заказов клиента `C`  
**And** заказы со статусом `draft` не возвращаются.

---

## 2. Клиент находит заказ по точному номеру

**Given** клиент `C` имеет заказ `O1` с `order_number = '12345678'`  
**When** клиент запрашивает `GET /api/v1/orders/history?q=12345678`  
**Then** возвращается `200`  
**And** первым элементом `data` является `O1`  
**And** `meta.query_type = 'order_number'`.

---

## 3. Клиент не видит чужой заказ по номеру

**Given** клиент `C1` запрашивает заказ `O2`, который принадлежит клиенту `C2`  
**When** `GET /api/v1/orders/history?q=O2.order_number`  
**Then** возвращается `404`  
**And** в теле ошибки не раскрывается, что заказ существует.

---

## 4. Поддержка находит заказы по email

**Given** поддержка имеет право `support`  
**When** запрашивает `GET /api/v1/orders/history?q=john@example.com`  
**Then** возвращается `200`  
**And** в `data` только заказы клиента с email `john@example.com`  
**And** записывается событие `order_history.search` с `query_type = 'email'`.

---

## 5. Фильтр по дате

**Given** есть заказы 01.01.2024, 15.06.2024, 31.12.2024  
**When** запрос `from=2024-06-01&to=2024-06-30`  
**Then** возвращается только заказ от 15.06.2024.

---

## 6. Фильтр по статусу

**Given** есть заказы со статусами `delivered`, `canceled`, `refunded`  
**When** запрос `status=canceled`  
**Then** возвращаются только заказы со статусом `canceled`.

---

## 7. Фильтр по сумме

**Given** валюта `RUB`, суммы `10000`, `50000`, `100000`  
**When** запрос `currency=RUB&min_amount=20000&max_amount=60000`  
**Then** возвращается только заказ со суммой `50000`.

---

## 8. Поиск по товару

**Given** заказ содержит товар `Bread 500g`  
**When** запрос `q=bread`  
**Then** заказ возвращается  
**And** `meta.query_type = 'product'`.

---

## 9. Пагинация

**Given** у клиента 100 заказов  
**When** запрос `page_size=50&page=2`  
**Then** возвращается вторая страница по 50 заказов  
**And** `meta.next_page` отсутствует или равно `null`.

---

## 10. Ограничение page_size

**Given** запрос `page_size=500`  
**Then** используется `page_size=50`  
**And** `meta.page_size = 50`.

---

## 11. Валидация

**Given** запрос `min_amount=100` без `currency`  
**When** вызывается API  
**Then** возвращается `422`  
**And** сообщение ошибки указывает, что требуется `currency`.

---

## 12. Нет N+1

**Given** список из 50 заказов  
**When** выполняется запрос  
**Then** количество SQL-запросов к таблицам `orders`, `order_items`, `customers` не зависит от количества заказов  
**And** проверка проводится через query log или тест на количество запросов.

---

## 13. Производительность

**Given** тестовая БД с 10 000 000 заказов  
**When** выполняется поиск по точному номеру заказа  
**Then** p95 ≤ 150ms.

**Given** тестовая БД с 10 000 000 заказов  
**When** выполняется поиск по товару  
**Then** p95 ≤ 500ms, если включён `pg_trgm`.

---

## 14. Логирование

**Given** клиент выполняет поиск  
**When** результат не найден  
**Then** пишется метрика/лог:

```json
{
  "event": "order_history_search_not_found",
  "query_type": "order_number",
  "customer_id": "..."
}
```

---

# Что явно НЕ входит в задачу

1. Миграция заказов из legacy-системы, Excel, CSV.
2. Полнотекстовый поиск по комментариям, invoices, чатам.
3. Fuzzy-поиск, опечатки, морфология.
4. Массовый экспорт заказов.
5. Переделка всего UI клиентского аккаунта.
6. Изменение бизнес-логики создания заказов.
7. Изменение модели оплаты.
8. Перенос в Elasticsearch, если текущий PostgreSQL может уложиться в целевое время.
9. Создание нового отдельного сервиса.

Если в процессе выяснится, что данные заказов физически отсутствуют в `orders`, это фиксируется как отдельная задача «Данные не найдены», а не блокирует текущую задачу.

---

# Definition of Done

Задача считается готовой, когда:

1. Код смержен в `main`.
2. Миграции прогнали на staging.
3. Индексы созданы `CONCURRENTLY`.
4. Unit-тесты домена проходят.
5. Integration-тесты API проходят.
6. Проверена производительность на staging с реалистичным объёмом данных.
7. Обновлена OpenAPI/Swagger-документация.
8. Добавлены метрики:
   - `order_history_search_total`;
   - `order_history_search_duration_ms`;
   - `order_history_search_not_found_total`.
9. Добавлен алерт:
   - p95 > 1s в течение 5 минут;
   - error rate > 1% в течение 5 минут.
10. Feature flag:
   - `order_history_search_v2 = false` по умолчанию;
   - после включения на 5% трафика 24 часа без алертов — 100%.
11. Runbook для поддержки:
   - как искать заказ;
   - что делать, если заказ не найден;
   - какие логи смотреть.
12. Нет утечки чужих данных: проверены сценарии с чужим `order_number`, чужим email, чужим `customer_id`.

---

# Роллаут

1. Feature flag выключен.
2. Деплой на staging.
3. Смоук-тесты:
   - клиент видит свои заказы;
   - клиент не видит чужие;
   - поддержка находит по email;
   - поиск по номеру работает;
   - фильтры работают.
4. Включение на 5% реального трафика.
5. Наблюдение 24 часа:
   - p95;
   - error rate;
   - support tickets;
   - DB load.
6. Включение на 100%.
7. Оставить старый список заказов доступным как fallback минимум 2 недели.

## Откат

Если p95 > 2s или error rate > 3%:

1. Выключить feature flag.
2. Вернуть старый эндпоинт/старый список.
3. Зафиксировать инцидент.
4. Не откатывать миграции, если они forward-only.

---

# Что я обязательно включу в формулировке и почему

| Элемент | Почему без него задача размытая |
|---|---|
| **Цель и метрика успеха** | Иначе можно «сделать поиск» и не понять,解决了 ли проблема поддержки. |
| **Конкретные пользователи** | «Пользователи» могут быть клиент, поддержка, админ, B2B-менеджер. У них разные права и сценарии. |
| **Определение «прошлый заказ»** | Иначе один понимает «за 2024 год», другой — «все когда-либо созданные», третий — «только доставленные». |
| **Исходные данные** | Исполнитель должен понимать, где хранятся заказы и что считать источником истины. |
| **API-контракт** | Без него фронтенд и бэкенд будут делать разные интерфейсы. |
| **Правила поиска** | «Поиск по заказам» не определяет, ищем ли мы номер, товар, email, сумму или просто фильтруем. |
| **Права доступа** | Критично: без этого можно слить чужие заказы. |
| **Критерии приёмки** | Чтобы тест и разработчик понимали, что именно должно работать. |
| **Производительность** | Жалоба может быть не про «нет поиска», а про «медленный поиск». |
| **Индексы и данные** | Иначе исполнитель может написать рабочий, но неживучий запрос. |
| **Ограничения и non-goals** | Чтобы не переделать весь аккаунт, не добавить Elasticsearch и не начать мигрировать legacy. |
| **Логирование и метрики** | Без них невозможно доказать, что задача дала бизнес-эффект. |
| **Роллаут и откат** | Чтобы можно было безопасно включить и отключить фичу. |
| **Definition of Done** | Чтобы «сделал API» не считалось готовой задачей. |

---

# Главное

Хорошая формулировка задачи — это не просто «сделайте поиск заказов».

Это ответ на вопросы:

- **Кто** будет искать?
- **Что** именно ищет?
- **Где** находятся данные?
- **Что** считается успешным поиском?
- **Что** нельзя искать?
- **Какие** ошибки возможны?
- **Как** мы поймём, что проблема бизнеса решена?
- **Как** откатить, если станет хуже?

Если эти вопросы закрыты в самой задаче, исполнитель может начинать работу без постоянных уточнений.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

## Почему спрашиваю, а не предполагаю

В прошлом ответе я сформулировал задачу с предположениями. Но предположение — это **риск**. Если я ошибся в одном из них, я могу построить правильную систему, которая решает **не ту** проблему.

Вопросы — это способ **снизить стоимость ошибки** до начала работы.

---

## Группа 1: Что реально происходит (30 секунд на вопрос)

Эти вопросы я задаю **первыми**, потому что они могут **сменить саму задачу**.

### 1.1. «Покажите, пожалуйста, 10 последних обращений в поддержку по этой теме.»

**Почему:** «Пользователи жалуются, что не могут найти заказы» — это **агрегированное** описание. Реальные обращения покажут:

| Что я увижу | Что это значит |
|-------------|---------------|
| «Где мой заказ 12345678?» | Нужен поиск по номеру |
| «Я покупал в марте, а там только декабрь» | Ограничение по дате / пагинация |
| «Я нашёл заказ, а там другая сумма» | Не "найти", а "данные неверны" |
| «У меня два аккаунта, заказ в другом» | Проблема объединения аккаунтов |
| «Я отменил заказ, он пропал» | Фильтр по статусу |
| «Поддержка нашла за 2 минуты, я не могу» | Разные интерфейсы, не логика поиска |

**Если 7 из 10 обращений — "я не вижу свой заказ в списке"** → задача про **историю** (пагинация, фильтры).
**Если 7 из 10 — "я ввожу номер, а он не находится"** → задача про **поиск по номеру**.
**Если 7 из 10 — "заказ в другом аккаунте"** → задача **не про поиск**, а про **объединение аккаунтов**.

Одна фраза заказчика меняет всю архитектуру.

### 1.2. «Сколько обращений в неделю/месяц по этой теме?»

**Почему:**

- 50/месяц → это UX-проблема, не инфраструктурная.
- 500/месяц → это продукт-проблема, нужны метрики.
- 5000/месяц → это **инцидент**, нужен hotfix, не фича.

От этого зависит, что я делаю: чиню пагинацию за день или строю поисковый сервис за месяц.

### 1.3. «А где сейчас хранятся эти заказы? В одной БД или в нескольких системах?»

**Почему:**

Если заказы в `orders` — ок, я строю поиск.
Если заказы в **legacy-системе** и часть уже мигрирована, а часть ещё нет — задача **не про поиск**, а про **данные**.

Это вопрос, который **меняет срок** с 2 недель на 2 месяца.

---

## Группа 2: Что именно "не могут найти" (2 минуты)

### 2.1. «Покажите, как выглядит текущий список заказов у клиента. Сколько заказов там видно? Как он его листает?»

**Почему:** Я не знаю текущего UX. Возможно:

- Там есть поиск, но он ищет только по товару (а не по номеру).
- Там есть 20 заказов и кнопка "ещё", но нет фильтров.
- Там вообще нет списка, только "последние 5".

Каждый вариант — разная задача.

### 2.2. «Что пользователь вводит в поиск? Номер? Email? Название товара? Дату?»

**Почему:** В прошлом ответе я предполагал все три. Но если 80% пользователей ищут по **номеру** — я делаю поиск по номеру приоритетным. Если 80% ищут по **дате** — я делаю фильтр по дате приоритетным.

Простой вопрос меняет **порядок** в API.

### 2.3. «А пользователь знает, что он ищет? Он помнит номер? Или пытается вспомнить, что покупал?»

**Почему:** Это **разные** задачи.

| Сценарий | Что нужно |
|----------|-----------|
| "Я помню номер 12345678" | Точный поиск по `order_number` |
| "Я покупал хлеб в марте" | Фильтр по дате + товару |
| "У меня был заказ на 5000 рублей" | Фильтр по сумме |
| "Я не помню, что покупал, но помню, что был возврат" | Фильтр по статусу |

**Разные сценарии → разные поля в API → разные индексы.**

---

## Группа 3: Кто пользователь (1 минута)

### 3.1. «Это B2C, B2B или и то и другое?»

**Почему:**

- B2C: один клиент = один `customer_id`. Простая модель.
- B2B: один аккаунт = несколько сотрудников, каждый видит заказы **своей компании**. Нужен `tenant_id`.
- B2B2C: менеджер видит заказы клиента + свои. Нужна иерархия.

Модель доступа **совершенно** разная.

### 3.2. «Поддержка — это один человек или отдел? Сколько человек?»

**Почему:**

- 1 человек → аудит-лог "на всякий случай".
- 20 человек → нужен **полный** аудит, роли, права.

### 3.3. «Есть ли админка? Если да — что там сейчас есть для поиска заказов?»

**Почему:** Возможно, админка уже **имеет** поиск, и задача — вынести его на клиентский UI. Тогда я не пишу с нуля, а переиспользую.

---

## Группа 4: Данные (2 минуты)

### 4.1. «Сколько всего заказов в БД? Сколько у одного клиента максимум?»

**Почему:**

- 100 000 заказов → `Seq Scan` ещё ок, индексы не критичны.
- 10 000 000 → нужны индексы, возможно partitioning.
- Один клиент с 50 000 заказов → нужен особый путь (pagination, virtual scroll).

Я строю **под объём**, а не под гипотезу.

### 4.2. «Есть ли у заказа `order_number`? Он уникален? Как он формируется?»

**Почему:**

- Если `order_number` — это `id` (цифра) → поиск простой.
- Если `order_number` — это `ORD-2024-12345` (с форматом) → нужен LIKE или prefix index.
- Если `order_number` **не уникален** (одинаковый номер в разных годах) → нужен составной ключ.
- Если `order_number` **отсутствует** → задача про **данные**, не про поиск.

### 4.3. «Какие статусы есть? Какие из них "прошлые"?»

**Почему:** Я в прошлом ответе предполагал: "все, кроме `draft`". Но:

- `archived` — это "прошлый" или "скрытый"?
- `fraud` — должен ли клиент видеть его?
- `partial_refund` — отдельный статус или часть `refunded`?

**Каждый статус, который я не знаю, — потенциальный баг** ("я не вижу свой заказ, а он в статусе `pending_review`").

### 4.4. «Если заказ был отменён и созданный заново — это два разных заказа или один?»

**Почему:** Если два → клиент видит два. Если один → нужна логика "последний". Это **разная** бизнес-логика.

---

## Группа 5: Ограничения и контекст (2 минуты)

### 5.1. «Есть ли готовый дизайн/макет?»

**Почему:** Если да → я знаю, какие поля нужны в API. Если нет → я проектирую API, а потом фронтенд будет "нужно ещё это поле".

### 5.2. «Дедлайн? Это "к пятнице" или "к концу квартала"?»

**Почему:** Меняет **степень**.

| Дедлайн | Что делаю |
|---------|-----------|
| К пятнице | Миграция + 1 индекс + базовый поиск по номеру. |
| К концу месяца | Полная фича: поиск, фильтры, пагинация, метрики. |
| К концу квартала | Можно сделать Elasticsearch, CQRS, read model. |

**"К пятнице" ≠ "к концу квартала"**, даже если задача звучит одинаково.

### 5.3. «Есть ли feature flags в проекте? Есть ли A/B-инфраструктура?»

**Почему:** Если да → я делаю роллаут по этапам. Если нет → я делаю один деплой. Это **разный** уровень риска.

### 5.4. «Есть ли compliance-требования к данным? GDPR? 152-ФЗ?»

**Почему:** Если да → я не могу логировать `email` в поиске. Не могу хранить поисковые запросы. Не могу показывать `customer_id` в UI.

Это **меняет API-контракт**, а не только логирование.

### 5.5. «Можно ли добавить `pg_trgm` на production?»

**Почему:** Если да → поиск по товару через GIN-индекс. Если нет → `ILIKE` или отдельный сервис.

Я не буду спрашивать "можно ли добавить" без контекста "зачем". Я спрашиваю **конкретно**: "Зачем нам `pg_trgm`?" → "Для поиска по названию товара" → "Можно?" → да/нет.

---

## Группа 6: Успех (1 минута)

### 6.1. «Как мы поймём, что проблема решена?»

**Почему:** В прошлом ответе я написал "снижение обращений на 30%". Но **кто** это измеряет? Есть ли dashboard? Есть ли теги в тикет-системе?

Если нет → **часть задачи** — добавить метрики. Если да → я подключаюсь к существующей аналитике.

### 6.2. «А если поиск работает, но пользователи всё равно не находят заказ — что тогда?»

**Почему:** Возможно, проблема **не в поиске**. Возможно:

- Заказ действительно **отсутствует** (потеря при миграции).
- Заказ в **другом аккаунте** (пользователь не помнит, на какой email регистрировался).
- Заказ **заблокирован** (fraud detection).

Если я строю "идеальный поиск", а 50% случаев — "заказ в другом аккаунте" — я **не решу** проблему бизнеса.

---

## Что я НЕ спрашиваю

| Вопрос | Почему не спрашиваю |
|--------|---------------------|
| «Какой язык программирования?» | Это PHP-проект. Вопрос бестолковый. |
| «Какую БД используем?» | Я посмотрю в `composer.json`. Не нужно спрашивать. |
| «Нужны ли тесты?» | Да. Вопрос риторический. |
| «Кто будет ревьюить?» | Я не спрашиваю заказчика. Это внутренний процесс. |
| «Сколько стоит час работы разработчика?» | Не моя зона. |

---

## Приоритет вопросов

Если у меня **5 минут** на звонок:

| # | Вопрос | Почему |
|---|--------|--------|
| 1 | Покажите 10 обращений в поддержку | Меняет саму задачу |
| 2 | Сколько заказов в БД? | Меняет архитектуру |
| 3 | B2C или B2B? | Меняет модель данных |
| 4 | Есть ли `order_number`? | Меняет API |
| 5 | Дедлайн? | Меняет степень |

Остальные — в асинхронном сообщении.

---

## Почему это важно

Заказчик сказал: «Пользователи не могут найти заказы».

Это **симптом**, не **причина**.

| Симптом | Возможная причина | Разное решение |
|---------|-------------------|----------------|
| Не видят в списке | Ограничение по дате / пагинация | Фильтры |
| Не находятся по номеру | Нет поиска по номеру | Search по `order_number` |
| В другом аккаунте | Объединение аккаунтов | Merge logic |
| Запрос слишком медленный | N+1 / нет индекса | Оптимизация |
| Заказа нет в БД | Потеря при миграции | Backfill |

**Одна фраза → 5 разных задач.** Вопросы — это способ узнать, какая из 5 задач **реальная**.

Если я начну писать код без этих вопросов, я могу построить **правильный поиск**, который **не решает** проблему бизнеса. А это худший исход: я потратил 2 недели, деплой прошёл, а обращения в поддержку **не снизились**.

> ⏱ 190.2s · prompt 108804 tok · output 14105 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос про профессиональное поведение разработчика в двух конкретных ситуациях: (а) нашёл посторонний баг во время работы, (б) понял, что критерий приёмки противоречит цели. Нужно ответить в контексте собеседования на PHP-бэкенд. Стиль предыдущих ответов: практический, структурированный, с примерами, без воды, с честными оговорками. Нужно разделить два случая, показать конкретные действия, объяснить почему. Важно не быть абстрактным ("надо сообщить" — это тавтология), а показать механику: что говорю, кому, в какой момент, как документирую, как не блокирую задачу. Для (б) важно показать, что я не молча "исправляю" критерий, а не продолжаю работу против него, и как аргументирую. Приведу примеры с конкретикой (баг с суммой, противоречие между "показывать 404" и "не раскрывать существование" или "показывать все заказы" vs "метрика 30%"). Ответ должен быть самодостаточным.

</details>

**Кандидат:**

## Общий принцип

В обоих случаях я **не молчу** и **не решаю за заказчика**. Но и **не останавливаю всё**, чтобы "подождать решения". Я действую так, чтобы:

1. задача не блокировалась;
2. моя работа не шла вразрез с целью;
3. решение было зафиксировано, а не в чьей-то голове.

---

## Случай А: нашёл попутный баг, не связанный с задачей

### Что делаю

**1. Останавливаюсь на 2 минуты и делаю три вещи сразу:**

- открываю тикет в трекере (не в чате, не в голове);
- записываю туда: что я видел, на каком сценарии, с какими данными, как воспроизвести;
- ставю тег/приоритет по честной оценке: `P1` (ломает деньги/данные), `P2` (ломает UX), `P3` (косметика).

**2. Оцениваю, пересекается ли баг с моей задачей.**

Здесь ключевое разветвление:

| Пересечение | Что делаю |
|-------------|----------|
| Баг в той же функции, которую я трогаю | Исправляю **в своём PR**. Он всё равно должен быть покрыт тестами, и "кстати, починил" — честнее, чем отдельный PR. |
| Баг рядом, но в другом модуле | Тикет, и продолжаю свою задачу. |
| Баг критический (P1), даже если не пересекается | Эскалация. Не жду своего PR. |

**3. Не тащу в свой PR чужой код, который я не понимаю.**

Это частая ошибка: "пока я тут, глянь, тут странная логика, поправлю". Если я не понимаю контекст — я его ломаю. Чужой код с чужой историей меняется только тем, кто его понимает, или с осознанным ревью.

### Пример

Я делаю поиск заказов. В пути нахожу: `OrderItem::lineTotal()` считает `quantity * price`, но `price` может быть `null` при импортированных заказах. Мой поиск возвращает такие заказы, и `total_amount` в них неверный.

- Это **пересекается** с моей задачей: мой ответ API отдаёт неправильную сумму.
- Делаю: тикет `P1`, фикс в моём PR (проверка на `null` + тест), и отдельно — тикет "почему `price` может быть null", потому что это симптом проблемы импорта, а не баг одной функции.

### Пример 2

Нахожу: при отмене заказа `customer.email` обнуляется. Не связано с поиском.

- Это **не пересекается**.
- Делаю: тикет `P1` (теряем данные), эскалация тимлиду **сегодня**, не в конце недели. Моя задача продолжается.

### Почему так

- **Не молчу:** баг, который я знаю и не говорю — это моя ответственность. "Я же не про это" — не аргумент.
- **Не останавливаю задачу:** ждать "сначала починю баг" — это блокировка. Тикет + оценка — достаточно.
- **Не тащу в PR:** смешивание "моя задача" + "чужой баг" делает PR хуже для ревью и сложнее для отката.
- **Фиксирую письменно:** баг, который только в чате, — это баг, которого "не было" через месяц.

---

## Случай Б: критерий приёмки противоречит цели задачи

Это **серьёзнее**, чем случай А. Здесь я рискую **спроектировать правильную систему, которая решает не ту проблему**.

### Что делаю

**1. Не начинаю писать код "по критерию".**

Это самая важная часть. Если я вижу противоречие, продолжать работу по нему — значит, я сознательно строю систему, которую сам считаю неверной. Это плохая позиция: код будет писан с внутренним "ну, оно так и так не будет работать".

**2. Формулирую противоречие конкретно, а не "мне кажется что-то не так".**

Плохо:
> "Там в критериях что-то странное."

Хорошо:
> "Критерий №7 говорит: 'клиент видит все свои заказы, включая чужие по номеру, чтобы не раскрывать существование' — это противоречит цели 'клиент находит свой заказ'. Если клиент не может найти чужой по номеру, мы должны вернуть 404. Но критерий №3 требует вернуть список. Это несовместимо."

Я должен показать **две конкретные строчки**, которые конфликтуют, и **почему** они конфликтуют. Не "странные требования", а "A и B не могут быть одновременно".

**3. Даю свою оценку, но не решение.**

Я не говорю "уберите критерий №7". Я говорю:

> "На мой взгляд, приоритет должен быть: безопасность (не раскрывать чужой) > UX (не пустить в тупик). Если так — 404 правильный ответ, и критерий №7 нужно переформулировать. Но это ваше решение, потому что за ним стоит бизнес-компромисс, который я не могу оценить."

Здесь важно: **я даю экспертизу, но оставляю решение за тем, кто несёт ответственность перед бизнесом.** Потому что противоречие в критериях — это почти всегда **бизнес-конфликт** (безопасность vs UX, полнота vs производительность, простота vs гибкость), а не технический.

**4. Продолжаю то, что не зависит от спорного критерия.**

Если из 10 критериев 1 спорный — я не стою. Я делаю 9 неспорных, а спорный помечаю в PR: "критерий №7 не реализован, ожидание решения по тикету #XXX".

### Пример

Задача: клиент должен найти заказ. Критерий: "если заказ не найден — вернуть 200 с пустым списком, чтобы пользователь не понял, что ввёл неверный номер".

Цель задачи: "клиент находит свой заказ, обращения в поддержку снижаются".

Противоречие:
- Критерий говорит: не раскрывай, что номера нет (безопасность/анти-перебор).
- Цель говорит: клиент должен **понять**, что ввёл неверно, иначе он зальёт поддержку "я ввёл 12345, а там пусто".

Если я сделаю по критерию — пользователь будет писать в поддержку "а почему пусто?" → обращения **вырастут**, а не снизятся. Цель не достигнута.

Что делаю:
- тикет: "Противоречие между критерием 'пустой список' и целью 'снижение обращений'";
- моя рекомендация: вернуть `200` + `meta.search_failed: true` + сообщение "заказ с таким номером не найден" (для клиента) — это компромисс: не раскрываем чужой, но не отправляем в тупик;
- продолжаю реализацию остального;
- эскалирую, если решение не приняли за 1 рабочий день.

### Почему так

- **Не решаю сам:** критерий приёмки — это контракт с бизнесом. Я могу сказать "контракт саморазрывается", но пересмотр контракта — не моя роль.
- **Не молчу:** молчание = я молча выполняю некорректное. А потом "вы же не сказали, что цель не достигается".
- **Не останавливаю всё:** 9 из 10 критериев можно делать. Стоять из-за одного — неэффективно.
- **Фиксирую письменно:** если я реализую "по-своему", а потом скажут "а мы хотели иначе" — нужно, чтобы было видно, что я **предупреждал**.

---

## Разница между А и Б

| | Попутный баг (А) | Противоречие в критериях (Б) |
|---|---|---|
| Что я вижу | Ошибку в коде | Ошибку в требованиях |
| Кто "владеет" | Я (я могу чинить) | Заказчик/бизнес (я не могу сам решать) |
| Могу ли я решить сам | Да, если в моей зоне | Нет, это бизнес-решение |
| Блокирует ли моя задача | Не обязательно | Спорный критерий — да, остальные — нет |
| Кому эскалирую | Если P1 или не моя зона | Всегда (это не моё решение) |
| Что фиксирую | Тикет | Тикет + свою рекомендацию + "ожидание решения" |

---

## Что я НЕ делаю ни в одном случае

| ❌ | Почему |
|----|--------|
| Молча чиню чужой баг в своём PR | Скрываю проблему, усложняю ревью |
| Молча "исправляю" критерий по-своему | Нарушаю контракт с бизнесом |
| Останавливаю всю работу, пока не решат | Неэффективно, 9/10 можно делать |
| Обсуждаю только в чате | Нет истории, через месяц "кто сказал?" |
| Говорю "это не моя задача" и ухожу | Я знаю о проблеме — это уже моя |
| Жду, пока "кто-нибудь другой скажет" | Если я вижу — это я говорю |

---

## Итог

**Случай А:** я вижу баг → фиксирую письменно → оцениваю приоритет → чиню если в моей зоне, эскалирую если P1 или чужая. Не тащу в свой PR чужой код, не молчу.

**Случай Б:** я вижу противоречие → формулирую конкретно → даю рекомендацию, но решение за бизнесом → продолжаю делать неспорные критерии → эскалирую. Не решаю сам, не молчу, не останавливаю всё.

Общее: **я отвечаю за то, чтобы проблема была видна и зафиксирована, а не за то, чтобы её решить любой ценой в своём PR.** Разница в роли: по багу я могу быть чинильщиком, по требованию — только информатором и советником.

> ⏱ 40.3s · prompt 117982 tok · output 2682 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про отчёт о выполнении задачи после того, как код написан и тесты проходят. И второй вопрос: что меняется, если часть задачи не удалось сделать.

Это вопрос про коммуникацию и профессионализм. Нужно ответить конкретно: из чего состоит отчёт, в каком формате, кому адресован, что должно быть в нём. И отдельно — как честно и структурно отчитаться о том, что не сделано.

Подход: я отвечу как разработчик на собеседовании, поэтому покажу конкретную структуру отчёта. Не абстрактно, а с примерами. Важно:
- отчёт ≠ "всё сделал, тесты зелёные"
- отчёт = "что сделано, что не сделано, какие решения приняты, какие риски остаются, что нужно от других"
- если часть не сделана — это не провал, а информация, которую нужно структурировать

Структурирую так:
1. Общий принцип отчёта
2. Структура успешного отчёта (что сделал, как проверил, какие решения, риски)
3. Что меняется, если часть не сделана (честно, структурированно, с планом)
4. Формат и канал
5. Антипаттерны

Длина: средняя, с примерами. Язык: русский, как в диалоге.

</details>

**Кандидат:**

## Общий принцип

Отчёт о выполнении — это **не** "готово, можно мерджить". Это ответ на вопросы:

- что именно я сделал (а не "реализовал задачу");
- как я проверил, что это работает;
- какие решения я принял по дороге и почему;
- что осталось за рамками и что с этим делать;
- что нужно от других людей.

Короткий отчёт — это нормально. Пустой отчёт ("всё сделал") — нет, потому что он не даёт ревьюеру и заказчику информации для принятия решений.

---

## Структура отчёта

### 1. Что сделано

Не "реализовал поиск заказов", а конкретно:

> Реализовал `GET /api/v1/orders/history` с поиском по номеру, email, товару. Добавил фильтры по дате, статусу, сумме. Добавил пагинацию (20 по умолчанию, максимум 50). Создал 4 индекса. Добавил аудит-лог для поддержки.

Если я перечисляю **функции**, а не **фичи** — ревьюер понимает, что проверять.

### 2. Как проверено

> - Unit: 12 тестов для `OrderSearch` (поиск, фильтры, приоритеты).
> - Integration: 8 тестов (реальная БД, поиск по номеру/email/товару, права доступа).
> - Performance: на staging с 5M заказов p95 по номеру = 80ms, по товару = 340ms.
> - Ручное: прошёл 6 сценариев из критериев приёмки.

Здесь я не пишу "тесты проходят", а **что** покрыто и **как** измерено.

### 3. Какие решения принял по дороге

Это самое важное, что часто пропускают.

> - **Решение:** поиск по товару через `ILIKE`, а не `pg_trgm`.
>   **Почему:** `pg_trgm` нет в production, установка требует approval DBA. `ILIKE` на 5M строк — 200ms, в пределах бюджета.
>   **Риск:** при росте до 50M будет медленно. Тикет #234 на `pg_trgm`.
>
> - **Решение:** `first_item_name` — денормализованное поле, обновляется при изменении позиций.
>   **Почему:** запрос `LEFT JOIN order_items` на каждый заказ — N+1. Денормализация — 1 запрос.
>   **Компромисс:** если имя товара изменится постфактум, поле будет старым. Для списка заказов это допустимо, для详情页 — берём свежее.

Без этого блока ревьюер будет спрашивать "почему ILIKE?" и я буду объяснять заново.

### 4. Что осталось за рамками / риски

> - **Не сделано:** fuzzy-поиск (опечатки). В задаче было "substring", я сделал substring. Fuzzy — отдельная задача, тикет #256.
> - **Риск:** если у клиента > 10 000 заказов, `COUNT(*)` в пагинации будет медленным. На текущем объёме (максимум 3 000) ок. При росте — virtual pagination.
> - **Не покрыто:** сценарий "заказ в другом аккаунте". В задаче не было, но по обращениям из тикетов — возможно, часть жалоб именно об этом. Рекомендую обсудить с продуктовиком.

### 5. Что нужно от других

> - DBA: approval на `CREATE INDEX CONCURRENTLY` (3 индекса) — тикет #260.
> - DevOps: алерт на p95 > 1s для `/orders/history` — тикет #261.
> - Продукт: решение по fuzzy-поиску — тикет #256.
> - Ревью: 1 approve, 0 must-fix.

---

## Формат и канал

| Канал | Что туда |
|-------|----------|
| PR-описание | Пункты 1, 2, 3, 4, 5 — все |
| Чат с командой | Коротко: "Сделал X, осталось Y, тикеты Z" + ссылка на PR |
| Заказчик/продукт | Пункт 1 + 4 (что работает, что не влезло) |

Не дублирую всё в 5 местах. PR — полный отчёт. Чат — тред. Заказчик — 3 предложения.

---

## Что меняется, если часть задачи не сделана

### Главный принцип

Я **не прят**аю несделанное. Я **не оправдываюсь**. Я **структурирую** то, что не влезло, и даю план.

### Структура отчёта при частичном выполнении

То же самое, но пункт 4 (за рамками) становится **центральным**:

> **Сделано:**
> - Поиск по номеру заказа.
> - Поиск по email для поддержки.
> - Фильтры по дате, статусу, сумме.
> - Пагинация.
> - Индексы (2 из 4).
>
> **Не сделано:**
> - Поиск по товару.
> - Аудит-лог для поддержки.
>
> **Почему не сделано:**
> - Поиск по товару: `ILIKE` на 5M строк — 2 000ms, не укладываемся в бюджет 500ms. `pg_trgm` требует approval, который не получен к дедлайну.
> - Аудит-лог: зависимость от `audit-service`, который сейчас в состоянии `500`. Не хочу писать в БД напрямую без согласования.
>
> **Что сделано вместо:**
> - Поиск по товару отключён флагом. API работает по номеру и email. В логах — `order_history_search_product_disabled`.
> - Аудит: пишу в стандартный `app.log`. Не структурированный, но есть.
>
> **План на доработку:**
> - Поиск по товару: после approval `pg_trgm` (ожидаю к среде). Тикет #260.
> - Аудит: после стабилизации `audit-service`. Тикет #262.
>
> **Влияние на бизнес-метрику:**
> - Если 40% поисковых запросов — по товару, то до доработки эти 40% будут с пустым результатом + лог. Метрика "снижение обращений" будет **неполной**. Это нужно учесть при оценке.
>
> **Что нужно от других:**
> - DBA: approval `pg_trgm`. Блокер для 40% фичи.
> - DevOps: статус `audit-service`. Блокер для аудита.

### Ключевые отличия от "полного" отчёта

| | Полное выполнение | Частичное |
|---|---|---|
| Тон | "Вот что сделано" | "Вот что сделано, вот что не влезло, вот почему, вот план" |
| Метрика | "Снижение обращений на 30%" | "Снижение будет частичным: 60% покрывается, 40% — после доработки" |
| Ревью | "Можно мерджить" | "Можно мерджить как v1. Dоработка — v2" |
| Риск | "Остался риск X" | "Без доработки Y метрика не будет достигнута" |
| Ответственность | Моя (сделал) | Моя + чужая (блокеры) — **указана конкретно** |

### Что я **не** делаю при частичном выполнении

| ❌ | Почему |
|----|--------|
| "Я почти всё сделал" | "Почти" — это размыто. Что именно почти? |
| "Это не моя вина, DBA не дал" | Без "что я сделал, чтобы получить approval и чего не хватило" — это перекладывание. |
| Скрывать несделанное | Через неделю "а почему нет поиска по товару?" — это хуже, чем сказать сразу. |
| Делать вид, что "всё ок" | Тесты проходят — да. Но 40% фичи нет. Тесты не покрывают то, чего нет. |
| Оправдываться | "Я старался, но..." — не нужно. Структурированный отчёт — это не оправдание, это информация. |

### Честная фраза, которую я использую

> "Задача выполнена на 60%. Остальные 40% заблокированы на X. Я могу начать доработку, как только Y будет готово. Сейчас я фиксирую это как v1 с known limitations, а не как v2."

Это не "я провалился". Это "я сделал то, что мог, и честно показал границы".

---

## Антипаттерны в отчёте

| Антипаттерн | Почему плохо |
|-------------|-------------|
| "Всё готово, можно мерджить" (без деталей) | Ревьюер тратит время на разгадывание. Заказчик не знает границ. |
| "Сделал + 300 строк доп. работы" (без контекста) | "Доп. работа" — это хорошо или плохо? Зависит от того, что это. |
| "Не успел" (без причины и плана) | "Не успел" — это диагноз без лечения. |
| Отчёт в 500 слов, где всё "я думаю, может быть, наверное" | Нет решений. Есть сомнения. Это не отчёт. |
| Отчёт только после вопроса "ну что там?" | Если я не инициирую — это значит, что я не уверен в своей работе. |

---

## Итог

Отчёт о выполнении — это **информация для принятия решений**. Не "я старался", а:

1. **Что сделано** — конкретно.
2. **Как проверено** — с метриками.
3. **Какие решения** — с "почему" и "компромиссом".
4. **Что осталось** — честно, с причиной и планом.
5. **Что нужно от других** — конкретно, с тикетами.

Если часть не сделана — отчёт **не становится хуже**. Он становится **важнее**. Потому что теперь он отвечает на вопрос "можно ли мерджить как есть, или нужно ждать?" — а это вопрос, который без честного отчёта решится неправильно.

> ⏱ 38.9s · prompt 120457 tok · output 2769 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про FORCE INDEX в PostgreSQL. Но тут есть ловушка: FORCE INDEX — это синтаксис MySQL, а не PostgreSQL. В PostgreSQL нет FORCE INDEX, нет HINTS в стандартном синтаксисе. Это частая ошибка/подмена — либо пользователь путает БД, либо проверяет, пойму ли я разницу.

Нужно честно сказать: в PostgreSQL FORCE INDEX не существует. Это MySQL. И объяснить, как в PostgreSQL достигается то же самое (влияние на выбор плана), и в каких случаях вообще бывает нужно принудительно влиять на plan (и почему это почти всегда антипаттерн).

Структурирую:
1. Сразу честный ответ — FORCE INDEX это MySQL, не PostgreSQL.
2. Что есть в MySQL (FORCE INDEX) и зачем.
3. Как в PostgreSQL влияют на выбор плана (реальные механизмы): настройки планировщика, GUC-параметры, pg_hint_plan (расширение), создание/удаление индексов, ANALYZE, random_page_cost, enable_* флаги.
4. Когда вообще нужно принудительно влиять на план — и почему это почти всегда симптом проблемы, которую надо решать иначе.
5. Антипаттерны.

Сделаю практично и честно, с примерами. Отвечаю на русском, как и вся беседа.

</details>

**Кандидат:**

## Короткий и честный ответ

**`FORCE INDEX` в PostgreSQL не существует.** Это синтаксис MySQL (и MariaDB). В стандартном PostgreSQL нет `FORCE INDEX`, `USE INDEX`, `IGNORE INDEX` и нет hint-синтаксиса в `SELECT`.

Если кто-то говорит "в PostgreSQL нужно `FORCE INDEX`" — скорее всего он имеет в виду MySQL, либо предлагает решение, которое не работает в PG.

---

## Что делает FORCE INDEX в MySQL

```sql
SELECT * FROM orders FORCE INDEX (idx_customer) WHERE customer_id = 42;
```

Подсказывает оптимизатору: используй именно этот индекс. Это **подсказка**, не гарантия — оптимизатор может её проигнорировать, но на практике почти всегда следует.

Зачем его в MySQL:

| Случай | Почему |
|--------|--------|
| Оптимизатор выбрал `Seq Scan` | Нужно заставить его использовать индекс |
| Есть несколько индексов, выбран "не тот" | Указать нужный |
| Статистика неточная | Обойти ошибку оценки |
| Быстрый hotfix, пока не успеешь пересчитать статистику | Мгновенный эффект |

---

## Как в PostgreSQL влияют на выбор плана

В PG нет "подсказки в SQL", но есть несколько **реальных** механизмов. По возрастанию "жёсткости":

### 1. Правильные индексы + актуальная статистика (основной путь)

90% случаев, где в MySQL хотели бы `FORCE INDEX`, в PostgreSQL решаются **индексом и `ANALYZE`**:

```sql
-- Индекс, который оптимизатор сам выберет
CREATE INDEX idx_orders_customer_created ON orders (customer_id, created_at DESC);

-- Пересчёт статистики
ANALYZE orders;
```

Оптимизатор PostgreSQL (cost-based) сам выберет индекс, если он дешевле. `FORCE INDEX` в MySQL часто — обход **неправильной статистики** или **отсутствующего индекса**. В PG это лечится у корня.

### 2. GUC-параметры (session / statement level)

Влияют на **ценность** планов, а не на прямой выбор:

```sql
-- Сделать индекс-скан дешевле (SSD)
SET random_page_cost = 1.1;

-- Запретить определённый тип join (диагностика/крайний случай)
SET enable_nestloop = off;

-- Увеличить память для сортировки/хэша
SET work_mem = '64MB';

-- На уровне одного запроса
SET LOCAL random_page_cost = 1.1;
```

Это **не** "выбери индекс X", а "сделай так, чтобы план-оценщик оценил индексы иначе". Мягче и безопаснее, чем жёсткий hint.

### 3. `pg_hint_plan` — расширение-аналог FORCE INDEX

Если **реально** нужен явный hint (редко):

```sql
CREATE EXTENSION pg_hint_plan;

SELECT /*+ IndexScan(o idx_orders_customer) */
    * FROM orders o WHERE customer_id = 42;
```

Это и есть "FORCE INDEX для PostgreSQL", но:

- это **расширение**, не стандарт;
- требует включения `shared_preload_libraries`;
- hint — текст в комментарии, легко сломать при рефакторинге;
- в команде часто запрещено (нестандартно, не переносимо).

**Использую только** когда:
- индекс существует и нужен именно он;
- `ANALYZE` и GUC не помогли;
- это точечный hotfix, а не системное решение.

### 4. Материализованные представления / partitioning

Если запрос "упорно" плохо планирует, иногда проще **изменить данные**, чем план:

```sql
-- Отсортированная по created_at таблица → BRIN-индекс (маленький, быстрый)
CREATE INDEX idx_events_created_brin ON events USING brin (created_at);

-- Или partitioning по времени → partition pruning
CREATE TABLE events (...) PARTITION BY RANGE (created_at);
```

---

## Когда "принудительное" влияние на план оправдано

| Ситуация | Что делаю |
|----------|-----------|
| Оптимизатор ошибся из-за неточной статистики | `ANALYZE` + проверка `pg_stats` |
| Нет нужного индекса | Создаю индекс |
| `random_page_cost` не соответствует диску (SSD) | `SET random_page_cost = 1.1` |
| Редкий, критичный запрос, остальные механизмы не помогли | `pg_hint_plan` (точечно) |
| `Nested Loop` на больших таблицах | Смотрю почему, корректирую индексы/данные |

## Когда это антипаттерн

| Антипаттерн | Почему |
|-------------|--------|
| `pg_hint_plan` на каждом запросе | Нестандартно, хрупко, не переносимо. Симптом плохой статистики/индексов. |
| `SET enable_* = off` глобально | Сломает другие запросы. Только точечно + с пониманием. |
| Hint как замена индексу | Если оптимизатор не выбирает индекс — скорее всего, его нет или статистика плохая. |
| "Просто работает, не трогайте" | Через полгода данные вырастут, план сломается. |

---

## Как диагностировать, нужен ли вообще hint

```sql
-- 1. План
EXPLAIN (ANALYZE, BUFFERS) SELECT ...;

-- 2. Статистика по колонкам
SELECT attname, n_distinct, most_common_vals
FROM pg_stats WHERE tablename = 'orders';

-- 3. Есть ли индекс
SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'orders';

-- 4. Используется ли
SELECT indexrelname, idx_scan FROM pg_stat_user_indexes;
```

Если `Seq Scan` на 10M строк, но индекс **есть** и **актуальная статистика** — тогда, и только тогда, смотрю в `pg_hint_plan`. В остальных случаях — чиню индекс/статистику.

---

## Итог

| Вопрос | Ответ |
|--------|-------|
| Есть ли `FORCE INDEX` в PostgreSQL? | **Нет.** Это MySQL. |
| Как в PG принудительно выбрать индекс? | `pg_hint_plan` (расширение), или GUC-параметры, или (лучше) правильный индекс + `ANALYZE`. |
| Когда применять hint? | Редко: точечный hotfix, когда индекс есть, статистика актуальна, но оптимизатор упрямо ошибается. |
| Когда **не** применять? | Как системное решение, на каждом запросе, вместо чинения индексов/статистики. |

Главное: в PostgreSQL **почти всегда** проблема "не тот план" решается не hint'ом, а **правильным индексом + актуальной статистикой**. Hint — это костыль, а не фундамент.

> ⏱ 28.3s · prompt 122959 tok · output 1946 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про измеримое влияние `strict_types=1` на производительность PHP-кода, просит оценить в процентах по "моему опыту" и объяснить причину. Это вопрос на собеседовании (продолжение серии). 

Что важно здесь: это вопрос с ловушкой. Он предполагает, что `strict_types=1` имеет измеримое влияние на производительность. Но по факту:

1. `strict_types=1` влияет только на приведение типов при **вызове функций из файла, где он объявлен**. Это не глобальный режим.
2. Влияние на скорость — мизерное и в большинстве случаев **не измеряемое** в реальных приложениях. Дополнительные проверки типов происходят в call-site, но это одна из самых дешёвых операций (сравнение типа/тега).
3. "На сколько процентов" — я не могу дать честное число из "опыта", потому что:
   - Это зависит от того, сколько вызовов функций в горячем пути.
   - Профайлер (Xdebug/Blackfire/UPHP) обычно не показывает это как отдельную строку — это часть overhead на call.
   - В реальном Symfony/Doctrine приложении 99% времени — БД, сеть, сериализация. Типы — шумы.

Честный ответ: я не могу назвать число "по опыту", потому что я его не измерял как отдельную метрику, и не нужно. Это микрооптимизация уровня ~0-несколько процентов на горячих вызовах, и в реальных системах — зашумлено.

Нужно:
- Признать, что я не измерял это как отдельную величину (честность — это то, что от меня хотят на собеседовании).
- Объяснить, что именно делает strict_types с точки зрения overhead.
- Дать честную оценку: влияние есть, но оно незначимое и в большинстве случаев не имеет значения.
- Не выдумывать красивое число ("3.7%"), это было бы нечестно.
- Связать с тем, что я говорил раньше про производительность: Н+1, БД, сериализация — это то, что реально влияет.

Структура ответа:
1. Честно: я не измерял это как отдельную метрику, и вот почему.
2. Что физически происходит при strict_types (overhead).
3. Оценка: где это видно, где нет.
4. Почему это не то, чем стоит жертвовать читаемость.
5. Связь с реальными бутылками.

Не нужно быть длинным. Это вопрос с "ловушкой на выдумывание цифр". Честный короткий ответ лучше, чем красивый длинный.

Формат: без заголовков-разделов, короткий, честный, с одной-двумя таблицами/списками где уместно. Без "Итого" в конце, чтобы не выглядеть как шаблон.

</details>

**Кандидат:**

Честно: я не измерял это как отдельную метрику, и не буду называть число "по опыту" — это было бы выдумыванием. Объясню, что я реально знаю.

## Что `strict_types` делает с выполнением

Это не глобальный режим. Он влияет только на **сайты вызова** в файле, где объявлен. При каждом вызове функции PHP дополнительно проверяет, что переданный аргумент соответствует объявленному типу без слабого приведения.

То есть overhead — это **одна дополнительная проверка на каждый вызов функции** (сравнение type-tag аргумента с ожидаемым). Это одна из самых дешёвых операций в рантайме — порядка наносекунд.

## Где это видно, а где нет

| Ситуация | Влияние видно? |
|----------|:---:|
| Горячий цикл на 1M итераций с вызовами функций | Да, на уровне микросекунд на итерацию |
| Типичный Symfony-запрос (БД + сеть + сериализация) | Нет, зашумлено |
| 99% реального кода | Нет |

## Почему не то, чем стоит жертвовать

Реальные бутылки, с которыми я сталкиваюсь:

- N+1 запросы в Doctrine (разница в **сотни мс**)
- Медленный SQL (разница в **секунды**)
- Сериализация/десериализация (разница в **десятки мс**)
- Блокировки, lock contention (разница в **секунды**)

`strict_types` — это уровень **наносекунд на вызов**. Даже если у вас 10 000 вызовов функций на запрос, это микросекунды. На фоне одного N+1 в 200ms это **0.001%**.

## Честный ответ на вопрос

Влияние **есть**, но оно настолько мизерное, что:

1. Я бы не стал его измерять — нет смысла.
2. Я бы **никогда** не жертвовал `strict_types` ради микрооптимизации — это потеря читаемости и безопасности ради 0.001%.
3. Если кто-то в вашей системе реально замерил "strict_types замедляет на X%" — скорее всего он замерил не `strict_types`, а что-то другое (или замерил на микробенче, не на реальном коде).

Если задача — ускорить приложение, я смотрю в `pg_stat_statements`, `EXPLAIN`, profiler'ы. Не в режим приведения типов.

> ⏱ 20.2s · prompt 124643 tok · output 1260 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Вот я и в ловушке. Пользователь спрашивает: "Почему Doctrine ORM не поддерживает составные первичные ключи" — но это **неправда**. Doctrine ORM **поддерживает** составные первичные ключи через `#[ORM\Id]` на нескольких полях + `#[ORM\CompositeIds]`. Это стандартная, давно работающая фича.

То есть в вопросе заложена ложная предпосылка. Это как прошлый вопрос про `FORCE INDEX` в PostgreSQL — я честно сказал, что его там нет. Здесь аналогичная ситуация, только наоборот: пользователь предполагает, что фичи нет, но она есть.

Что я должен сделать? Честно указать, что предпосылка неверна, показать, как это на самом деле работает, и дать реальный контекст: почему в **практике** составные ключи всё равно редко используют и с какими проблемами реально сталкиваются. Это будет честный и полезный ответ.

Не буду поддакивать и строить рассуждение про "почему не поддерживает" — это было бы подтверждение мифа. Лучше вежливо поправить и дать реальную картину.

Структура:
1. Прямо: поддерживает, это неправда.
2. Как работает на самом деле (пример).
3. Реальные проблемы, с которыми сталкиваются (а это есть и они настоящие) — и почему поэтому в DDD/чистой архитектуре составные ключи всё равно избегают.
4. Как живут в реальных проектах.

Сделаю конкретно, с кодом, без воды. Не буду притворяться, что фичи нет. Это ключевой момент — не поддакивать ложной предпосылке, как в вопросе про FORCE INDEX.

</details>

**Кандидат:**

## Сначала поправка: он поддерживает

Вопрос содержит неверную предпосылку. Doctrine ORM **поддерживает** составные первичные ключи. Это стандартная, давно работающая фича.

```php
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $orderId;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;
}
```

Несколько `#[ORM\Id]` на разных полях = составной ключ. Doctrine корректно генерирует `INSERT`, `UPDATE`, `DELETE` с обоими ключами. Это работает и 10 лет, и сейчас.

## Что действительно правда

Фича есть, но в **практике** её используют редко — и вот почему. Это не про "не поддерживает", а про **неудобно в связке с DDD и реальными сценариями**.

### 1. Составной ключ ломает инкапсуляцию

```php
// Чтобы загрузить, нужно знать ОБА ключа
$orderItem = $em->getRepository(OrderItem::class)
    ->find(['orderId' => 5, 'productId' => 12]);
```

Сущность не имеет **своей** идентичности. Она идентифицируется **относительно родителя** и другого измерения. Это противоречит принципу "сущность имеет стабильный ID".

### 2. Отсутствует "свободная" навигация

```php
// Как получить OrderItem, не зная productId?
// Только через родителя:
$order->items->findBy(['productId' => 12]);

// А вот так нельзя:
$em->getRepository(OrderItem::class)->find(42);  // нет "42"
```

Составной ключ всегда "приклеен" к контексту. Нет "голого" объекта.

### 3. Cascade + orphanRemoval работают хуже

Составной ключ часто означает **мостовую таблицу** (many-to-many). А там:

- `cascade: ['persist']` на мосте — хрупко.
- `orphanRemoval` — спорно (удалить связь или удалить сущность?).
- События `prePersist`/`postPersist` — дублируются.

### 4. Сложнее миграции и отладка

```sql
-- UPDATE по составному ключу
UPDATE order_items SET quantity = 5
WHERE order_id = 5 AND product_id = 12;

-- А вот так нельзя (нет unique "id"):
UPDATE order_items SET quantity = 5 WHERE id = 42;
```

В логах, в `pg_stat_statements`, в отладке — "42" понятнее, чем "(5, 12)".

## Как живут в реальных проектах

### Вариант 1: Синтетический ID + составной уникальный ключ (рекомендую)

```php
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $orderId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;
}
```

```sql
-- Составной ключ НЕ primary, а unique constraint
ALTER TABLE order_items
    ADD CONSTRAINT uq_order_product
    UNIQUE (order_id, product_id);
```

| Плюсы | Минусы |
|-------|--------|
| Сущность имеет "свой" ID | Лишняя колонка |
| Простая навигация | Небольшой overhead на хранение |
| DDD-совместимо | — |
| Лёгкая отладка | — |

Это **90%** реальных случаев.

### Вариант 2: Нормальная связь (когда составной ключ оправдан)

Честный случай для составного PK — **мостовая таблица many-to-many**, где "объект" — это сама связь:

```php
#[ORM\Entity]
class Membership  // роль пользователя в группе
{
    #[ORM\Id]
    #[ORM\Column(type: Types::UUID)]
    private string $userId;

    #[ORM\Id]
    #[ORM\Column(type: Types::UUID)]
    private string $groupId;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $role;
}
```

Здесь "объект" — это **принадлежность**, а не отдельная сущность. Составной ключ честен: нет смысла давать `id` факту "пользователь X в группе Y".

Но даже тут часто добавляют `id` для cascade и отладки.

### Вариант 3: В DDD-проекте

В DDD составной ключ — почти всегда **код-смell**. Потому что:

- Агрегат должен иметь **один** корень с **одним** ID.
- Составной ключ означает "у меня два родителя" → нарушение границы агрегата.

Если вижу `#[ORM\Id]` на двух полях в DDD-проекте — спрашиваю: "А не нужно ли разбить на две сущности с синтетическими ID?"

## Честный итог

| Вопрос | Ответ |
|--------|-------|
| Поддерживает ли Doctrine составные PK? | **Да.** |
| Используют ли их? | Редко, сознательно. |
| Почему? | Не про поддержку, а про **удобство**: инкапсуляция, навигация, DDD, отладка. |
| Когда оправдано? | Мостовые таблицы (many-to-many), где объект = факт связи. |
| Что делают в 90% случаев? | Синтетический `id` + `UNIQUE (col1, col2)`. |

Как и с `FORCE INDEX` — если вопрос звучит как "почему X не делает Y", сначала проверяю, правда ли Y не делает. В случае `FORCE INDEX` — правда, не делает. В случае составных PK — **неправда**, делает. Но и то, и другое — повод не поддакивать ложной предпосылке, а показать реальную картину: фича есть, но есть честные причины, по которым её в проекте лучше не использовать.

> ⏱ 25.6s · prompt 125277 tok · output 1873 tok

---

## Q27 · Мини-код-ревью

**Интервьюер:** Посмотри на этот код:

```php
class OrderController
{
    public function pay(Request $req): Response
    {
        $id = $req->get('order_id');
        $pdo = DB::connect();
        $order = $pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
        if (!$order) {
            return new Response('not found', 404);
        }
        $order['status'] = 'paid';
        $pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");
        Mailer::send($order['email'], 'Ваш заказ оплачен');
        (new Logger())->log('paid ' . $id);
        return new Response('ok');
    }
}
```

Какие проблемы ты видишь и как бы ты этот код переделал? С чего начал бы и почему?

<details><summary>Ход мысли модели</summary>

The user is asking me to review a PHP code snippet that looks like a typical "messy" production controller. Let me analyze it carefully.

Looking at the code:

```php
class OrderController
{
    public function pay(Request $req): Response
    {
        $id = $req->get('order_id');
        $pdo = DB::connect();
        $order = $pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
        if (!$order) {
            return new Response('not found', 404);
        }
        $order['status'] = 'paid';
        $pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");
        Mailer::send($order['email'], 'Ваш заказ оплачен');
        (new Logger())->log('paid ' . $id);
        return new Response('ok');
    }
}
```

Let me identify all problems:

1. **SQL Injection** — `$id` directly interpolated into SQL. This is the #1 critical issue.
2. **No input validation** — `$id` from request, no type check, no format check.
3. **No authorization** — anyone can pay any order by ID. No check that the user owns this order.
4. **No idempotency** — calling twice will send two emails, log twice.
5. **No transaction** — UPDATE and subsequent operations aren't atomic.
6. **Race condition** — two concurrent `pay()` calls can both read status='pending', both write 'paid', both send email.
7. **No domain invariants** — what if order is already cancelled? What if it's already paid?
8. **No separation of concerns** — controller does: validation, DB, business logic, email, logging.
9. **Hardcoded business logic** — "paid" string, email text.
10. **No error handling** — if Mailer fails, order is already "paid" but user doesn't know.
11. **SELECT * ** — fetches all columns, unnecessary.
12. **No CSRF protection** — state-changing operation without token.
13. **No rate limiting** — brute force possible.
14. **`DB::connect()`** — creates new connection per request? Or shared? Unclear. No connection pooling.
15. **No Doctrine/ORM** — raw PDO in controller. No entity, no repository.
16. **No exception handling** — if `$pdo->query()` throws, user gets 500.
17. **Mailer::send is blocking** — email failure blocks the response? Actually no, it's after UPDATE, so order is paid but email might fail.
18. **No event publishing** — payment success should trigger other things (inventory, notifications, etc.)
19. **`$order['status'] = 'paid'`** — modifies the fetched array but doesn't use it. Dead code / confusion.
20. **No content-type on response** — `new Response('ok')` returns text/html by default.
21. **No API versioning** — `/pay` without version.
22. **No pagination / rate limits**
23. **Logger instantiation per call** — `(new Logger())` every time.
24. **No structured logging** — 'paid ' . $id is unstructured.
25. **No testability** — everything is coupled.

Now, how to refactor? Let me think about the approach:

The user asks "с чего начал бы и почему" — so I should explain the ORDER of refactoring, not just give the final code.

My approach would be:
1. First: fix SQL injection (critical security)
2. Second: add authorization (critical security)
3. Third: add domain invariants / state machine (correctness)
4. Fourth: separate concerns (maintainability)
5. Fifth: add idempotency (correctness)
6. Sixth: make it testable

Let me write a structured response. I'll be concrete with code but also explain the "why" and "order" of refactoring.

I should be thorough but not overwhelming. Let me structure it as:
1. Critical (fix today)
2. Important (fix this week)
3. Structural (fix this sprint)

And then show the refactored version.

</details>

**Кандидат:**

## Проблемы (по приоритету)

### Критические (эксплойт / потеря денег)

| # | Проблема | Последствие |
|---|----------|-------------|
| 1 | **SQL-инъекция** | `$id` встроена в SQL. `order_id=1; DROP TABLE orders;` — полный доступ к БД |
| 2 | **Нет авторизации** | Любой человек с любым `order_id` может "оплатить" чужой заказ |
| 3 | **Нет идемпотентности** | Два одинаковых запроса → два письма, два лог-записи |
| 4 | **Race condition** | Два `pay()` одновременно → оба читают `status=pending`, оба пишут `paid`, оба шлют письмо |

### Важные (баги, хрупкость)

| # | Проблема | Последствие |
|---|----------|-------------|
| 5 | Нет проверки текущего статуса | Можно "оплатить" уже отменённый заказ |
| 6 | Нет транзакции | UPDATE прошёл, email упал → заказ "оплачен", пользователь не знает |
| 7 | `SELECT *` | Читаем все колонки, включая `email`, `password_hash` — лишнее в памяти |
| 8 | `$order['status'] = 'paid'` | Мутация массива, который нигде не используется. Мёртвый код |
| 9 | `Mailer::send` синхронный | Если SMTP down → 500 после того, как заказ уже "оплачен" |
| 10 | Нет обработки исключений | Любой сбой → 500, без контекста |

### Структурные (неудобство, рост боли)

| # | Проблема |
|---|----------|
| 11 | Контроллер делает: валидацию, запрос к БД, бизнес-логику, отправку email, логирование |
| 12 | Нет домена: нет сущности, нет инвариантов, нет политик |
| 13 | Не тестируемо: нет швов для injection |
| 14 | `DB::connect()` — неясно, пул или нет |
| 15 | Нет CSRF-защиты на state-changing операцию |

---

## С чего начал бы и почему

### Шаг 1: SQL-инъекция (5 минут)

Потому что это **эксплойт**. Не рефакторинг, не архитектура — дыра в безопасности.

```php
// Было:
$order = $pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
$pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");

// Стало:
$stmt = $pdo->prepare("SELECT id, status, email FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
$stmt->execute([$id]);
```

**Почему первое:** пока это не исправлено, любой рефакторинг бессмыслен — систему уже можно скомпрометировать.

### Шаг 2: Авторизация (15 минут)

```php
// Кто вызывает? Чей заказ?
$userId = $req->getAuthenticatedUserId();

$stmt = $pdo->prepare("SELECT id, status, email FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $userId]);
$order = $stmt->fetch();

if (!$order) {
    return new Response('not found', 404);  // не 403, не раскрываем существование
}
```

**Почему второе:** без авторизации "оплата" чужого заказа — это потеря денег.

### Шаг 3: Инвариант статуса (10 минут)

```php
if ($order['status'] === 'paid') {
    return new Response('already paid', 409);
}

if ($order['status'] === 'cancelled') {
    return new Response('order cancelled', 422);
}
```

**Почему третье:** бизнес-правило "нельзя оплатить отменённый заказ" должно быть **в коде**, а не в голове разработчика.

### Шаг 4: Идемпотентность + атомарность (30 минут)

```php
// Идемпотентность: если уже paid — верни 200, не 409
// (зависит от бизнес-требования, но чаще — 200)

// Атомарность: UPDATE только если status ещё не paid
$stmt = $pdo->prepare(
    "UPDATE orders SET status = 'paid' WHERE id = ? AND status NOT IN ('paid', 'cancelled')"
);
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    return new Response('status changed', 409);
}
```

Это **оптимистичная блокировка** через `WHERE`. Без `SELECT FOR UPDATE`, без deadlock'а.

**Почему:** race condition решается на уровне SQL, а не на уровне приложения.

### Шаг 5: Email после успешной оплаты, асинхронно (30 минут)

```php
// Не синхронный Mailer::send
// А: событие → очередь → consumer

// Вариант минимальный (без брокера):
$pdo->prepare("INSERT INTO outbox (topic, payload) VALUES (?, ?)")
    ->execute(['order.paid', json_encode(['orderId' => $id, 'email' => $order['email']])]);

// В той же транзакции, что и UPDATE
```

**Почему:**
- Email не должен блокировать ответ.
- Если email упал — заказ уже оплачен. Retry, а не rollback.
- Атомарность: `UPDATE` + `INSERT INTO outbox` в одной транзакции.

### Шаг 6: Разделение слоёв (1–2 дня)

Вот теперь — структура. Не перед, а **после** того, как код безопасен и корректен.

---

## Переделанный код

### Domain

```php
final class OrderStatus
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const CANCELLED = 'cancelled';

    public static function canTransition(string $from, string $to): bool
    {
        return match ($from) {
            self::PENDING => in_array($to, [self::PAID, self::CANCELLED], true),
            self::PAID, self::CANCELLED => false,
            default => false,
        };
    }
}
```

### Application

```php
final class PayOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,
        private EventPublisher $events,
        private LoggerInterface $logger,
    ) {}

    public function execute(int $orderId, int $userId): PayOrderResult
    {
        $order = $this->orders->findById($orderId);

        if ($order === null || $order->userId !== $userId) {
            throw OrderNotFoundException::with($orderId);
        }

        if ($order->status === OrderStatus::PAID) {
            return PayOrderResult::alreadyPaid($orderId);
        }

        if ($order->status === OrderStatus::CANCELLED) {
            throw OrderCannotBePaidException::with($orderId, 'cancelled');
        }

        if (!OrderStatus::canTransition($order->status, OrderStatus::PAID)) {
            throw InvalidStatusTransitionException::with($order->status, OrderStatus::PAID);
        }

        // Атомарно: UPDATE + outbox в одной транзакции
        $affected = $this->orders->markPaidIfPending($orderId);

        if ($affected === 0) {
            throw OrderStatusChangedException::with($orderId);
        }

        $this->events->publish(new OrderPaid($orderId, $order->email));

        $this->logger->info('order.paid', ['order_id' => $orderId, 'user_id' => $userId]);

        return PayOrderResult::success($orderId);
    }
}
```

### Repository

```php
final class OrderRepository
{
    public function __construct(private \PDO $pdo) {}

    public function findById(int $id): ?Order
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, user_id, status, email, total_amount, currency
             FROM orders WHERE id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Order(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            status: $row['status'],
            email: $row['email'],
            total: (int) $row['total_amount'],
            currency: $row['currency'],
        );
    }

    /**
     * Атомарный UPDATE: только если status = 'pending'.
     * Возвращает количество затронутых строк.
     */
    public function markPaidIfPending(int $orderId): int
    {
        $stmt = $this->pdo->prepare(
            "UPDATE orders SET status = 'paid' WHERE id = ? AND status = 'pending'"
        );
        $stmt->execute([$orderId]);

        return $stmt->rowCount();
    }
}
```

### Controller

```php
class OrderController
{
    public function __construct(
        private PayOrderUseCase $payOrder,
    ) {}

    public function pay(Request $req): Response
    {
        $id = $req->get('order_id');

        if (!is_numeric($id) || (int) $id <= 0) {
            return Response::json(['error' => 'invalid order_id'], 422);
        }

        $userId = $req->getAuthenticatedUserId();

        try {
            $result = $this->payOrder->execute((int) $id, $userId);
        } catch (OrderNotFoundException) {
            return Response::json(['error' => 'not found'], 404);
        } catch (OrderCannotBePaidException $e) {
            return Response::json(['error' => $e->getMessage()], 422);
        } catch (OrderStatusChangedException) {
            return Response::json(['error' => 'conflict'], 409);
        }

        if ($result->alreadyPaid) {
            return Response::json(['status' => 'already_paid'], 200);
        }

        return Response::json(['status' => 'paid'], 200);
    }
}
```

### Email (отдельный процесс)

```php
final class SendOrderPaidEmail
{
    public function __construct(
        private Mailer $mailer,
        private OrderRepository $orders,
        private EmailLogRepository $log,
    ) {}

    public function handle(OrderPaid $event): void
    {
        // Идемпотентность
        if ($this->log->exists(orderId: $event->orderId, type: 'paid')) {
            return;
        }

        $this->mailer->send($event->email, 'Ваш заказ оплачен');
        $this->log->record($event->orderId, 'paid', new \DateTimeImmutable());
    }
}
```

---

## Что изменилось и почему именно так

| Было | Стало | Почему |
|------|-------|--------|
| SQL-инъекция | Prepared statements | Безопасность |
| Нет авторизации | `user_id` в WHERE | Безопасность |
| Нет проверки статуса | `OrderStatus::canTransition` | Корректность |
| Race condition | `WHERE status = 'pending'` + `rowCount()` | Корректность |
| Синхронный email | Outbox + consumer | Надёжность |
| Всё в контроллере | Controller → UseCase → Repository | Тестируемость |
| `SELECT *` | Явные колонки | Безопасность + производительность |
| `(new Logger())` | Инжект `LoggerInterface` | Тестируемость |
| `new Response('ok')` | `Response::json(...)` | Контракт API |

## Чего я **не** делаю

| ❌ | Почему |
|----|--------|
| Не переписываю на Symfony/Laravel в этом PR | Это другой проект. Сначала чиню дыры. |
| Не ввожу DDD-агрегаты, события, CQRS | Сначала безопасность и корректность. Архитектура — потом. |
| Не добавляю Doctrine | Можно, но это рефакторинг, не фикс. |
| Не делаю "пока идеальную архитектуру" | Код должен быть **безопасным и корректным** сегодня. Красивым — в следующем спринте. |

## Порядок важнее результата

Если бы я начал с "напишу доменную модель, repository, use case" — я бы потратил день на архитектуру, а SQL-инъекция осталась бы. Или наоборот: починил инъекцию, а race condition остался.

**Порядок:** безопасность → корректность → надёжность → структура → красота. Каждый следующий слой строится на предыдущем.

> ⏱ 45.4s · prompt 126981 tok · output 4056 tok

---

## Q28 · Мини-код-ревью

**Интервьюер:** Посмотри на эту доменную сущность:

```php
#[ORM\Entity]
class User
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $email,
    ) {}

    public function changeEmail(string $email): void
    {
        $this->email = $email;
        $this->em->flush();
    }

    public function email(): string
    {
        return $this->email;
    }
}
```

Что не так с этой сущностью с точки зрения DDD и чистой архитектуры? Как бы ты её исправил?

<details><summary>Ход мысли модели</summary>

The user is asking me to critique a domain entity from a DDD and clean architecture perspective. Let me analyze the code:

```php
#[ORM\Entity]
class User
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $email,
    ) {}

    public function changeEmail(string $email): void
    {
        $this->email = $email;
        $this->em->flush();
    }

    public function email(): string
    {
        return $this->email;
    }
}
```

Problems I see:

1. **Dependency on infrastructure (EntityManagerInterface)**: The domain entity knows about Doctrine. This violates the dependency rule — domain must not depend on infrastructure. This is the most critical violation.

2. **`flush()` inside a domain method**: This is a "flush in domain" anti-pattern I discussed earlier. The domain shouldn't know about Unit of Work. Also, `flush()` inside a method means the transaction boundary is unclear — the entity is partially "saved" inside a business method, which breaks atomicity if the use case has multiple steps.

3. **No validation in `changeEmail`**: Email could be empty, invalid format, etc. The constructor also doesn't validate. There's no invariant protection.

4. **Constructor does two things**: creates the entity AND injects infrastructure. This is a violation of SRP.

5. **No immutable value object for email**: Email is a string, not a VO. So "invalid email" can be stored.

6. **`private EntityManagerInterface` as a promoted property**: This makes the entity non-serializable, hard to test, and couples it to Doctrine forever.

7. **Doctrine hydration problem**: When Doctrine loads this entity from DB, it won't call the constructor (or will call it with `null` for `EntityManagerInterface`), meaning `$em` will be null and `changeEmail` will fatal error.

8. **No domain events**: Changing email is a domain event (`EmailChanged`), but it's not collected.

9. **The entity is both a domain object and an anemic persistence object**: It has behavior (changeEmail) but that behavior is coupled to infrastructure.

How to fix:

Option A: Keep it as a Doctrine entity (pragmatic), but:
- Remove EntityManagerInterface from constructor
- Don't call flush() in domain methods
- Add validation
- Use a VO for email
- Let the application layer handle flush()

Option B: Separate domain and persistence (clean architecture):
- Domain: pure PHP class, no ORM attributes
- Infrastructure: Doctrine entity that maps to domain

I'll present both, with the pragmatic one first since it's more realistic for most teams, and the clean one as the "ideal".

Let me structure my response well. I'll be specific about each problem, show the fix, and explain the reasoning.

</details>

**Кандидат:**

## Что не так

### 1. Домен знает об инфраструктуре (критично)

```php
private EntityManagerInterface $em,
```

Доменная сущность зависит от Doctrine. Это нарушает правило зависимостей: **внутренний слой не может знать о внешнем**. Domain → Infrastructure — стрелка в обратную сторону.

Последствия:

| Проблема | Почему |
|----------|--------|
| Невозможно тестировать без Doctrine | Unit-тест `User` требует `EntityManagerInterface` |
| Невозможно переиспользовать домен | Сменить ORM = переписать все сущности |
| Нарушена инкапсуляция | Сущность знает **как** сохраняется, а не **что** происходит |

### 2. `flush()` внутри доменного метода (критично)

```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();  // ← транзакционная граница внутри домена
}
```

Это нарушает **несколько** принципов сразу:

- **Граница транзакции** должна быть на уровне use case, а не сущности. Если use case делает 3 изменения — каждое `flush()` внутри сущности создаёт отдельную транзакцию.
- **Домен не знает про Unit of Work.** У домена нет понятия "сохранить". Есть понятие "состояние изменилось".
- **Атомарность сломана.** `changeEmail()` + `changeName()` = два `flush()`, а не один.

### 3. Нет валидации инварианта

```php
public function changeEmail(string $email): void
{
    $this->email = $email;  // $email может быть '', 'abc', null
}
```

И конструктор не валидирует. Значит в БД может попасть:

```sql
SELECT email FROM users;
-- ''
-- 'abc'
-- NULL
```

Нарушен инвариант "email всегда валиден".

### 4. Doctrine не вызовет конструктор

Это **скрытый** баг. Когда Doctrine hydrates объект из БД:

```php
// Что делает Doctrine:
$user = new User(null, null);  // ← EntityManagerInterface = null
$user->email = 'john@example.com';  // ← private, устанавливается через reflection
```

Результат: `$this->em === null`. Первый вызов `changeEmail()` → **Fatal Error**:

```
Call to a member function flush() on null
```

Это баг, который проявляется **не при создании**, а **при первом изменении**. Т.е. в продакшене, когда пользователь меняет email.

### 5. Email — строка, а не Value Object

```php
private string $email,
```

Это значит:
- Нет гарантии, что email валиден.
- Нет нормализации (lowercase, trim).
- Нет equality по значению (два `User` с одинаковым email — "равны"?).
- Нет инкапсуляции (снаружи можно `$user->email` и поменять).

### 6. Сущность не собирает события

Изменение email — это **доменное событие** (`EmailChanged`). Но оно нигде не фиксируется. Аудит, уведомления, пересылка писем — всё это "потеряно".

### 7. Мёртвая ответственность

Сущность делает **две** работы:
- бизнес-логика (changeEmail)
- инфраструктура (flush)

Это нарушение SRP: у сущности две причины меняться — бизнес-правила изменились ИЛИ изменилась ORM.

---

## Как исправляю

### Вариант 1: Прагматичный (Одна БД, Doctrine, 90% проектов)

Разделяем **роль** сущности и **момент сохранения**:

```php
// src/Domain/User/User.php — ЧИСТЫЙ PHP, без Doctrine
final class User
{
    private Email $email;
    /** @var list<DomainEvent> */
    private array $pendingEvents = [];

    private function __construct(Email $email)
    {
        $this->email = $email;
    }

    public static function create(string $email): self
    {
        return new self(Email::of($email));
    }

    public function changeEmail(string $newEmail): void
    {
        $newEmailObj = Email::of($newEmail);

        if ($this->email->equals($newEmailObj)) {
            return;  // ничего не изменилось
        }

        $this->email = $newEmailObj;

        $this->pendingEvents[] = new EmailChanged(
            oldEmail: $this->email,
            newEmail: $newEmailObj,
        );
    }

    public function email(): Email
    {
        return $this->email;
    }

    /** @return list<DomainEvent> */
    public function collectEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}
```

```php
// src/Domain/User/Email.php — Value Object
final class Email
{
    private function __construct(
        private readonly string $value,
    ) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new \DomainException("Invalid email: {$value}");
        }

        if (strlen($value) > 255) {
            throw new \DomainException('Email too long');
        }
    }

    public static function of(string $value): self
    {
        return new self(strtolower(trim($value)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

```php
// src/Domain/User/EmailChanged.php
final class EmailChanged
{
    public function __construct(
        public readonly Email $oldEmail,
        public readonly Email $newEmail,
    ) {}

    public function topic(): string
    {
        return 'user.email_changed';
    }
}
```

```php
// src/Application/User/ChangeEmailUseCase.php
final class ChangeEmailUseCase
{
    public function __construct(
        private UserRepository $users,
        private EventPublisher $events,
    ) {}

    public function execute(int $userId, string $newEmail): void
    {
        $user = $this->users->findById($userId);

        if ($user === null) {
            throw UserNotFoundException::with($userId);
        }

        $user->changeEmail($newEmail);

        $this->users->save($user);

        foreach ($user->collectEvents() as $event) {
            $this->events->publish($event);
        }
    }
}
```

```php
// src/Infrastructure/Doctrine/UserEntity.php — ФОРМАТ ХРАНЕНИЯ
#[ORM\Entity]
class UserEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $email;

    public function toDomain(): User
    {
        return User::create($this->email);
    }

    public function setDomain(User $user): void
    {
        $this->email = $user->email()->value();
    }
}
```

```php
// src/Infrastructure/Doctrine/UserRepository.php
final class UserRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function findById(int $id): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->find($id);
        return $entity?->toDomain();
    }

    public function save(User $user): void
    {
        $entity = $this->em->getRepository(UserEntity::class)
            ->findOneBy(['email' => $user->email()->value()]);

        if ($entity === null) {
            $entity = new UserEntity();
            $this->em->persist($entity);
        }

        $entity->setDomain($user);
        $this->em->flush();  // ← flush() в INFRASTRUCTURE, не в домене
    }
}
```

### Что изменилось

| Было | Стало | Почему |
|------|-------|--------|
| `EntityManagerInterface` в конструкторе | Убран | Домен не знает про ORM |
| `flush()` в `changeEmail()` | `flush()` в `UserRepository` | Граница транзакции — на уровне use case |
| `string $email` | `Email $email` (VO) | Инвариант "email валиден" гарантирован |
| Нет валидации | `Email::of()` бросает | Невалидный email не попадёт в объект |
| Нет событий | `EmailChanged` + `collectEvents()` | Аудит, уведомления, расчёт — всё через событие |
| Невозможно тестировать | Unit-тест без Doctrine | `User::create('a@b.c')->changeEmail('x@y.z')` — чистый PHP |
| Doctrine hydration ломает `$em` | Разделение Domain / Entity | Hydration создаёт `UserEntity`, а не `User` |

### Unit-тест (без Doctrine)

```php
final class UserTest extends TestCase
{
    public function test_change_email_collects_event(): void
    {
        $user = User::create('old@example.com');

        $user->changeEmail('new@example.com');

        $events = $user->collectEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(EmailChanged::class, $events[0]);
    }

    public function test_invalid_email_throws(): void
    {
        $this->expectException(\DomainException::class);

        User::create('not-an-email');
    }

    public function test_same_email_does_not_collect_event(): void
    {
        $user = User::create('a@b.c');
        $user->changeEmail('a@b.c');

        $this->assertCount(0, $user->collectEvents());
    }
}
```

Ни одного `EntityManagerInterface`. Ни одного `flush()`. Чистый PHP.

---

## Вариант 2: Если хочется один класс (минимальный фикс)

Если команда не готова на разделение Domain / Entity (маленький проект, 1 разработчик), хотя бы:

```php
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $email;

    private function __construct(string $email)
    {
        $this->assertValidEmail($email);
        $this->email = strtolower(trim($email));
    }

    public static function create(string $email): self
    {
        return new self($email);
    }

    public function changeEmail(string $newEmail): void
    {
        $this->assertValidEmail($newEmail);
        $this->email = strtolower(trim($newEmail));
        // НЕ flush() здесь. Flush() делает use case.
    }

    public function email(): string
    {
        return $this->email;
    }

    private function assertValidEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \DomainException("Invalid email: {$email}");
        }
    }
}
```

```php
// Use case — здесь flush
final class ChangeEmailUseCase
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function execute(int $userId, string $newEmail): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $user->changeEmail($newEmail);
        $this->em->flush();  // ← здесь, не в сущности
    }
}
```

Это **не чистая архитектура**, но **безопасно и корректно**. Минимальный порог: убрать `EntityManagerInterface` из домена, убрать `flush()` из домена, добавить валидацию.

---

## Итог: что именно нарушено

| Нарушение | Правило |
|-----------|---------|
| `EntityManagerInterface` в конструкторе | Dependency Rule: Domain не зависит от Infrastructure |
| `flush()` в доменном методе | Транзакционная граница — на уровне use case |
| `string $email` без валидации | Инвариант должен быть гарантирован конструктором |
| Нет событий | Доменное изменение должно быть наблюдаемым |
| Doctrine hydration ломает `$em` | Сущность не может зависеть от состояния, которое создаётся не через конструктор |
| Две причины меняться | SRP: бизнес-логика ≠ сохранение |

**Главное:** сущность отвечает на вопрос "что происходит в домене", а не "как это сохранить". Сохранение — это **техника**, а не **поведение**.

> ⏱ 43.3s · prompt 130219 tok · output 3751 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

The user is asking about my approach to solving a technical problem in an unfamiliar technology/stack — from first look to confidently giving an estimate, and when to ask for help vs. figuring it out alone. This is a meta-question about engineering judgment, not a specific tech question.

Let me think about what makes a good answer here:

1. It should be concrete, not "read the docs, ask a colleague, figure it out"
2. It should reflect a real decision framework, not a generic "be proactive"
3. It should have a clear structure: stages, decision points, time-boxes
4. It should be honest about limitations — when "figure it out alone" becomes "waste everyone's time"
5. It should connect to the interview context — this is about seniority, judgment, calibration

The question has two parts:
- Sequence of actions from first look to confident estimate
- When to ask for help vs. figure it out alone

Let me structure this as a real process with time-boxes and decision gates. I'll be specific about what I'd actually do, not generic advice.

Key insight: the boundary between "figure it out" and "ask for help" is NOT about confidence level. It's about **cost of being wrong** and **cost of delay**. A junior asks too early (low cost of delay, they can't evaluate the answer). A senior asks too late (high cost of delay, they've spent 3 days in a dead end). The right point is when the marginal cost of self-investigation exceeds the cost of asking.

Let me write this in a structured, concrete way with time-boxes and decision gates.

</details>

**Кандидат:**

## Последовательность действий

### Фаза 0: Оценка задачи (5 минут)

Прежде чем открыть документацию или писать код, я отвечаю на три вопроса:

| Вопрос | Зачем |
|--------|-------|
| **Что именно нужно сделать?** (не "настроить RabbitMQ", а "настроить RabbitMQ so that X" ) | Чтобы не тратить время на изучение всего стека, а только нужного куска |
| **Какова стоимость ошибки?** (лог в неправильном месте vs. потеря данных) | Определяет, сколько я готов искать сам |
| **Есть ли дедлайн?** | Определяет, есть ли время на "разобраться" или нужно сразу просить |

Если задача "поменять цвет кнопки в незнакомом фреймворке" — это 10 минут документации. Если "перенести биллинг на новый платёжный провайдер" — это неделя. **Масштаб определяет глубину.**

---

### Фаза 1: Поверхностное знакомство (30 минут — 2 часа)

**Цель:** понять, *какой* это инструмент и *как* он устроен на уровне "что есть, что делает". Не "как работает под капотом", а "какие части есть и как они связаны".

Что делаю:

1. **Читаю README / официальный сайт.** 10 минут. Не docs — сначала "что это и зачем".
2. **Ищу пример, похожий на мою задачу.** Не "как работает X", а "как другие люди решали похожее". GitHub issues, Stack Overflow, блоги. 30 минут.
3. **Смотрю на код, который уже есть в проекте.** Как другие использовали этот инструмент? Какие patterns? Какие грабли уже наступили? 15 минут.

**Результат:** я могу сформулировать: "Мне нужно сделать X. В этом инструменте для этого есть механизм Y. Я пока не знаю, как Y конфигурируется, но я знаю, *что* искать."

**Критический момент:** на этом этапе я **уже могу** сказать коллеге: "Мне нужно сделать X. Я думаю, что для этого нужен Y. Я правильно понимаю?" Это вопрос на 2 минуты, который экономит 2 часа.

---

### Фаза 2: Узкое погружение (2 — 8 часов)

**Цель:** довести понимание от "я знаю, *что* нужно" до "я знаю, *как* это работает и *где* могу ошибиться".

Что делаю:

1. **Читаю документацию по конкретной подзадаче.** Не весь manual. Раздел "how to do X".
2. **Пишу минимальный пример.** Не в проекте — в отдельном файле. "Hello world" для этой конкретной фичи.
3. **Смотрю исходники, если док неточна.** Не читаю весь код. Ищу конкретную функцию, которую вызывает мой пример.
4. **Тестирую edge cases.** Что происходит, если X = null? Если X = 10 000? Если два процесса одновременно?

**Результат:** я могу написать код, который *работает*. Но я ещё не уверен, что он *надёжен*.

---

### Фаза 3: Уверенность (дополнительно 2 — 4 часа)

**Цель:** убедиться, что моё решение не сломается в продакшене.

Что делаю:

1. **Ищу известные проблемы.** GitHub issues, changelog, "gotchas" в доках. "What people wish they knew."
2. **Смотрю на production-ready примеры.** Не tutorials, а реальные проекты. Как они обрабатывают ошибки? Что они **не** делают?
3. **Спрашиваю себя:** "Если это упадёт в 3 часа ночи, смогу ли я отладить это?" Если нет — это красный флаг.
4. **Пишу тесты.** Не "тесты для проверки", а тесты, которые покажут, где я **не понимаю**. Если тест падает неожиданно — значит, я чего-то не понял.

**Результат:** я могу дать оценку срока **с честной погрешностью**.

---

### Фаза 4: Оценка срока

Я **никогда** не даю срок "один день" или "неделю" как точное число. Я даю:

> "Оптимистично: 2 дня. Реалистично: 4 дня. Пессимистично: 8 дней. Разница между оптимистичным и реалистичным — X. Разница между реалистичным и пессимистичным — Y."

И отдельно:

> "Я уверен в 80% работы. Остальные 20% — это [конкретный кусок], где я пока не уверен. Если там будет проблема — срок вырастет на Z."

**Почему три числа, а не одно:**

- Одно число = ложная точность. "4 дня" звучит как "я точно знаю".
- Три числа = честная неопределённость. "2–8, центр 4" звучит как "я понимаю, что не знаю".
- Отдельный "неуверенный кусок" = я не прячу неопределённость за средним.

---

## Когда прошу помощь, а когда разбираюсь сам

### Критерий: стоимость вопроса

| Стоимость вопроса | Что делаю |
|-------------------|-----------|
| 2 минуты на вопрос = 2 часа экономии | **Сразу спрашиваю** |
| 30 минут на вопрос = 30 минут экономии | **Спрашиваю, но после 30 минут самостоятельной работы** |
| 1 час на вопрос = 5 минут экономии | **Разбираюсь сам** |

Это не "я ленивый" и не "я не могу сам". Это **калькуляция**. Если коллега ответит за 2 минуты, а я буду искать 2 часа — я **краду время** и у себя, и у него (если он потом проверяет мой ответ).

### Конкретные правила

**Сразу прошу (не разбираюсь сам):**

| Ситуация | Почему |
|----------|--------|
| "Как это **называется**?" | Я трачу 2 часа на гугл, а коллега говорит "это называется X" за 10 секунд |
| "Это **нормально** или баг?" | Я не могу оценить без контекста. Коллега знает. |
| "Есть ли **у нас** уже решение для этого?" | У нас может быть внутренний инструмент, которого нет в доках |
| "Что **было раньше** и почему убернули?" | История решений — это не в доках, а в головах людей |
| **Стоимость ошибки высокая** | Потеря данных, безопасность, деньги. Не "ага, я понял, сделаю сам" |

**Разбираюсь сам (не прошу):**

| Ситуация | Почему |
|----------|--------|
| "Как **настроить** параметр X?" | Это в доках. Вопрос "как настроить" — это не вопрос, это гугл |
| "Почему **падает**?" (после 30 минут логов) | У меня есть контекст (логи, код, окружение). Коллега будет спрашивать "а что в логах?" |
| "**Сколько это займёт**?" | Я должен оценить сам. Если я спрошу "а сколько займёт?" — у меня нет оценки |
| "**Как это работает** под капотом?" | Если для задачи не нужно знать "под капотом" — не нужно спрашивать |

**Серая зона — мой главный инструмент:**

> "Я 30 минут искал X. Я понимаю, что мне нужен Y, но не могу найти, как его подключить. Я потратил ещё 2 часа и не нашёл. Могу я 2 минуты спросить у тебя, как это обычно подключают?"

Это **не** "я не могу". Это "я прошёл 30 минут самостоятельной работы и застрял на конкретном месте". Коллега видит, что я **уже** делал, и может дать точный ответ, а не "давай посмотрим вместе".

### Чего я **не** делаю

| ❌ | Почему |
|----|--------|
| Спрашиваю в первые 5 минут | "Я ничего не понял, что делать?" — это не вопрос, это паника |
| Спрашиваю после 3 дней | 3 дня в мёртвом конце = я не "разбирался", я упрямился |
| Спрашиваю "как это работает?" без конкретной задачи | "Как работает Redis?" — это не вопрос. "Как мне сделать rate limiting на Redis?" — это вопрос |
| Не показываю, что уже сделал | Коллега не хочет объяснять с нуля. Он хочет сказать "а, ты уже сделал X, тогда тебе нужен Y" |
| Спрашиваю и не записываю ответ | Через 2 месяца снова спрошу. Или забуду и наступлю на ту же грабли |

---

## Честные ограничения

Я **не могу** дать "уверенный срок" в первые 30 минут. Это физически невозможно. Уверенность — это не "я прочитал доки". Уверенность — это "я написал минимальный пример, он работает, я знаю, что будет, если X". А это **часы** работы, даже для опытного.

Если мне говорят "сделай это за 1 день" и я ещё не прошёл Фазу 2 — я говорю:

> "Я могу дать оценку после 2 часов работы. Сейчас я могу сказать: это не тривиально, но и не невозможно. Дай 2 часа, и я приду с реалистичным сроком."

Это лучше, чем:
- "Окей, сделаю за 1 день" (ложь, я ещё не знаю)
- "Это сложно, может 2 недели" (паника, я ещё не знаю)
- Молчание (а потом провал)

---

## Итог

| Фаза | Время | Результат |
|------|-------|-----------|
| Оценка задачи | 5 мин | "Это X, а не Y. Стоимость ошибки — Z." |
| Поверхностное знакомство | 30 мин – 2 ч | "Мне нужен механизм Y. Я знаю, что искать." |
| Узкое погружение | 2 – 8 ч | "Я написал рабочий пример. Я знаю, где могу ошибиться." |
| Уверенность | 2 – 4 ч | "Я знаю, что будет в продакшене. Я могу дать срок." |
| Оценка | 10 мин | "2 / 4 / 8 дней. Неуверенный кусок — X." |

**Вопрос "спросить или самому" решается не уверенностью, а стоимостью.** 2 минуты вопроса = 2 часа поиска → спрашиваю. 2 часа вопроса = 10 минут поиска → сам. Серая зона → 30 минут самостоятельной работы, потом вопрос с конкретикой.

> ⏱ 46.2s · prompt 133416 tok · output 2889 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о ситуации, где я (в роли разработчика) пообещал задачу к завтрашнему дню, но понял, что нужно два дня. Это вопрос на мягкие навыки: коммуникация, ответственность, честность, работа с дедлайнами.

Это собеседование PHP-бэкенд-разработчика, поэтому ответ должен быть в контексте работы, командного взаимодействия, честности перед командой и заказчиком.

Важно: ответ должен быть конкретным, практичным, без воды. Не абстрактные "буду честен", а конкретные шаги: кому, когда, что сказать, как сформулировать.

Структура:
1. Когда я понял, что не уложусь (это важная точка — не "к вечеру", а "как только понял")
2. Кому говорю и как
3. Что говорю конкретно (фразы, а не общие слова)
4. Что делаю параллельно (не просто "извини", а план)
5. Чего НЕ делаю (антипаттерны)

Нужно показать, что:
- Я говорю рано, а не поздно
- Я говорю тому, кто ждёт результат (заказчик/тимлид), а не "команде"
- Я даю не просто "не успею", а "вот план, вот что готово, вот что осталось"
- Я беру ответственность, но не ищу виноватых
- Я не оправдываюсь
- Я не прячусь

Ключевой момент: это НЕ "извините, я не успел". Это "я раньше, чем ты узнаешь из чужих рук, говорю тебе, что срок сдвигается, и вот план".

Также важно: различие между "я не уложусь" и "задача изменилась". Если задача изменилась — это другой разговор. Но в условии задачи не изменилась — я просто не оценил. Значит, это моя ошибка в оценке, и я её признаю.

Нужно ответить на русском, в стиле предыдущих ответов — конкретном, структурированном, с примерами фраз.

</details>

**Кандидат:**

## Главное правило

**Говорю как только понял, а не как только стало поздно.**

Если я понял в 11:00, что задача на два дня — я говорю в 11:05. Не в 18:00 "к вечеру". Не в 9:00 завтра утром. Потому что у того, кто ждёт результат, есть **окно для манёвра**. Чем раньше он узнает, тем больше у него вариантов.

---

## Что я делаю по шагам

### 1. Останавливаюсь и оцениваю честно (5 минут)

Не "ну, может, я ещё уложусь, если не буду обедать". А конкретно:

| Вопрос | Ответ |
|--------|-------|
| Что уже готово? | Модуль X написан и протестирован |
| Что осталось? | Модуль Y + интеграция + 2 edge case |
| Сколько реально нужно? | Завтра — 6 часов. Я не уложусь, потому что X уже занял 4 часа, а осталось ещё 8 |
| Что можно вынести из "к завтра"? | Модуль Y можно сделать сегодня, интеграция — завтра, edge case — послезавтра |
| Что **нельзя** сдвинуть? | Интеграция — без неё ничего не работает |

### 2. Иду к тому, кто ждёт результат

Не в общий чат. Не "к кому-нибудь". К **конкретному человеку**, который ставил дедлайн или который следующий шаг зависит от меня.

Если дедлайн поставил тимлид — к тимлиду.
Если заказчик ждёт — к заказчику (через тимлида, если нет прямого доступа).
Если другая команда ждёт мой кусок — к их лиду.

### 3. Что я говорю — конкретная формулировка

> "Привет. Задача [X], которую я брал к завтрашнему дню — я не уложусь. Сдвиг на один день.
>
> Что готово: [конкретно].
> Что осталось: [конкретно].
> Почему сдвиг: [конкретно, без оправданий].
>
> Варианты:
> 1. Готовлю полный результат послезавтра (2 дня).
> 2. Готовлю [часть А] завтра, [часть Б] послезавтра. Часть А уже работает и может быть принята.
>
> Какой вариант подходит? Если оба не подходят — давай обсудим приоритеты."

### 4. Почему именно так, а не иначе

| Что я **говорю** | Что я **не говорю** | Почему |
|------------------|---------------------|--------|
| "Я не уложусь" | "Кажется, может, не успеем" | Неопределённость заставляет слушателя гадать. Я даю факт. |
| "Сдвиг на один день" | "Будет немного позже" | "Немного" — это размыто. Я даю конкретный срок. |
| "Вот что готово" | "Я много сделал" | "Много" — это не факт. Я даю список. |
| "Варианты: 1, 2" | "Подскажи, что делать" | Я не перекладываю решение. Я даю выбор. |
| "Почему: [конкретно]" | "Задача оказалась сложнее, чем я думал" | "Сложнее, чем я думал" — это оправдание. Я даю причину. |

---

## Чего я НЕ делаю

| ❌ Антипаттерн | Почему плохо |
|----------------|-------------|
| Молчу, надеясь успеть | Если не успеваю — узнают не от меня, а от сломанного процесса |
| Говорю в 23:00, когда уже "ничего не поделаешь" | Окно для манёвра закрыто. Человек не может перераспределить |
| "Я бы успел, если бы не X" | Виноватый — это я. Окружение — это контекст, не оправдание |
| "Извини" (5 раз) | Однажды — нормально. Потом — это не извинение, это шум |
| Пишу в общий чат "ребят, не успею" | У того, кто ждёт, нет контекста. Он не знает, что готово, а что нет |
| Жду, пока спросят | Если я знаю, что не успеваю — я сам говорю. Не жду, пока узнают |
| Давлю на себя в тайм-аут | "Я доработаю дома и до утра доделаю" — это не решение, это скрытый риск (усталость, качество) |

---

## Что я делаю **параллельно** с сообщением

Не просто "сказал — и жду". Я:

1. **Продолжаю работать.** Сообщение — не пауза. Я делаю следующую часть, пока жду ответа.
2. **Фиксирую в трекере.** Обновляю статус задачи, пишу комментарий. Чтобы через месяц можно было посмотреть, что произошло.
3. **Готовлю часть, которая точно готова.** Если вариант 2 (частичная сдача) — я уже готовлю эту часть так, чтобы её можно было отдать сразу.
4. **Записываю, что я не понял.** Если задача "оказалась сложнее" — значит, я чего-то не знал. Я записываю, **что именно** я не понимал. Чтобы в следующий раз оценка была точнее.

---

## Разница: "я не успел" vs "срок сдвинулся"

Это два разных сообщения:

| | "Я не успел" | "Срок сдвинулся" |
|---|---|---|
| Тон | Провал | Информация |
| Фокус | На мне | На задаче |
| Вопрос | "Что со мной?" | "Что дальше?" |
| Реакция слушателя | "Опять?" | "Окей, перестроим" |

Я говорю второе. Не потому что "не хочу выглядеть плохо", а потому что **первое не даёт действий**. Второе даёт: "давай посмотрим варианты".

---

## Честный итог

Если я пообещал "к завтра" и понял, что "два дня" — я:

1. **Сказал сразу.** Не "когда станет поздно". А как только понял.
2. **Сказал тому, кто ждёт.** Не "команде". А конкретному человеку.
3. **Дал факт, а не неопределённость.** "Не уложусь", а "может быть, не успеем".
4. **Дал варианты, а не проблему.** "Вот два пути", а "что мне делать?"
5. **Продолжил работать.** Сообщение — не повод остановиться.

Это не "управление ожиданиями". Это **уважение к времени** того, кто зависит от моего результата. Если я знаю, что не успеваю, — молчать — это красть время у другого человека.

> ⏱ 34.1s · prompt 135994 tok · output 2135 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансе качества и скорости — когда код "достаточно хорош" и можно отдавать, а когда стоит продолжать улучшать. Это классический вопрос engineering judgment, и он приходит после длинной серии вопросов, где я последовательно разбирал архитектуру, DDD, тестирование, git-процесс, код-ревью, формулировку задач, работу с неизвестными технологиями.

Что здесь важно и чего стоит избегать:
1. Не давать "книжный" ответ про golden mean / good enough. Это скучно и бесполезно.
2. Не перечислять абстрактные принципы. Нужны конкретные критерии, которые я реально использую.
3. Честно признать, что это не объективная функция — это judgment, и judgment часто ошибается.
4. Показать, что "достаточно" — это не про код, а про контекст: стоимость ошибки, частоту изменений, количество людей, срок жизни кода.

Ключевые идеи, которые я хочу передать:
- "Достаточно" определяется не качеством кода, а стоимостью его изменения позже.
- Есть когнитивная ловушка: перфекционизм как способ избежать отдачи (страх ревью) и перфекционизм как реальная стоимость (время, которое могло уйти на фичи).
- Конкретные критерии: что должно быть ДО отдачи (тесты, покрытие рисков), что НЕ обязательно (идеальная архитектура, все edge cases).
- Разделение: что "достаточно" для MVP, для продакшена, для легаси.
- Практический тест: "если бы я это читал через 6 месяцев, я бы понял?"
- Честное признание: я иногда ошибаюсь в обе стороны. И как я это компенсирую (записываю долги, ставлю тикеты).
- Разница между "улучшать код" и "улучшать систему" — иногда улучшение качества = удаление кода, а не добавление.

Структура ответа:
1. Главный принцип: "достаточно" — это не свойство кода, а свойство контекста.
2. Конкретные критерии "можно отдавать" (чеклист).
3. Что я НЕ делаю для "идеальности" и почему.
4. Когда продолжаю улучшать (красные флаги).
5. Как я компенсирую "недостаточную" идеальность (долги, тикеты).
6. Честные признания: когда я ошибаюсь в обе стороны.
7. Практический пример.

Хочу быть честным и конкретным, без "зависит от контекста" в пустую. Дать реальные правила, которые я применяю. И признать границы — что judgment, а не алгоритм.

Объём: средний, как у предыдущих ответов. Структурированно, с таблицами там где уместно. Без воды.

</details>

**Кандидат:**

## Главный принцип

"Достаточно хорош" — это **не свойство кода**. Это свойство **контекста**: стоимости ошибки, частоты изменений, количества людей, срока жизни.

Один и тот же код может быть "достаточно" в одном месте и "недостаточно" в другом. Поэтому нет одного правила "до какого уровня полировать". Есть **вопросы, которые задают себе перед отдачей**.

---

## Что я проверяю ДО отдачи (обязательный минимум)

Это не "красота". Это **безопасность и предсказуемость**:

| Критерий | Почему обязательный |
|----------|---------------------|
| Нет SQL-инъекций / XSS / авторизационных дыр | Это не "качество", это безопасность |
| Критические пути покрыты тестами | Если нет теста — я не знаю, что код работает |
| Исключения обработаны (не "catch все") | Если упадёт — я смогу понять почему |
| Нет "временно" в коде | "Временно" живёт 2 года |
| Ревьюер сможет понять за 15 минут | Если я не могу объяснить — это не готово |

Всё, что **выше** этого минимума — уже вопрос баланса.

---

## Что я НЕ делаю для "идеальности" (и почему)

| Искушение | Почему отказываюсь |
|-----------|-------------------|
| Рефакторинг "пока тут" | Каждая отвлечённая минута — это риск не успеть. Если рефакторинг не нужен **сейчас** — он не нужен |
| Добавление "на всякий случай" | "На всякий случай" = код, который никто не использует. Это долг, а не качество |
| Абстрагирование из 2 примеров | Если есть 2 реализации — абстракция преждевременна. Если 3 — можно |
| Идеальная архитектура для MVP | MVP живёт 3 месяца. Архитектура на 5 лет — это перерасход |
| Покрытие всех edge cases тестами | Тест edge case, который может произойти 1 раз в год — это дорого. Я покрываю **вероятные** |
| Чистый DDD для CRUD-эндпоинта | 4 слоя для "получить список товаров" — это ceremony |

---

## Когда продолжаю улучшать (красные флаги)

Если я вижу один из этих — **не отдаю**:

| Флаг | Почему |
|------|--------|
| Код, который я **не могу объяснить** за 1 минуту | Если я не понимаю свой код — он сломан |
| Три и более `if` без else/return | Сложность растёт экспоненциально |
| Функция, которая делает **две** разные вещи | SRP нарушен. Будет ломаться при первом изменении |
| Дублирование логики в 2+ местах | Через месяц изменят одно, забудут второе |
| Магическое число / строка без имени | Я не знаю, что это значит через 3 месяца |
| Тест, который **проходит, но не проверяет** | Ложное чувство безопасности. Хуже, чем нет теста |

---

## Контекст решает "насколько"

Один и тот же код — разный уровень "достаточно":

| Контекст | "Достаточно" значит |
|----------|---------------------|
| MVP, 1 разработчик, 3 месяца жизни | Работает + тесты критических путей. Архитектура — простая |
| Продакшен, 5 разработчиков, 3 года жизни | + чистые границы, + документация, + мониторинг |
| Файл, который не будет меняться 2 года | Минимум. Если работает — ок |
| Файл, который будут менять еженедельно | Максимум. Если сложно менять — будет ломаться |
| Код, который читают другие | + имена, + комментарии "почему", + документация |
| Код, который пишу только я | Минимум. Могу себе напомнить |

**Ключевой вопрос:** "Как часто это будет меняться?" Чем чаще — тем чище должно быть. Потому что **изменение** — это когда код ломается.

---

## Как я компенсирую "недостаточную" идеальность

Я **не притворяюсь**, что код идеален. Я **фиксирую**, что не идеально:

1. **Тикет на технический долг.** Не "TODO в коде" (забуду), а тикет с описанием "что плохо и когда починить".
2. **Комментарий "почему так, а не иначе".** Если я выбрал компромисс — я записываю, **почему**. Чтобы через 6 месяцев не было вопроса "зачем так?".
3. **Честный отчёт.** "Сделал X. Оставил Y как technical debt. Тикет #Z." Не "всё идеально".
4. **Правило "не хуже, чем было".** Если код был плохой и я его тронул — я должен его **улучшить**, а не ухудшить. Но не обязательно до идеала.

---

## Честные признания: когда я ошибаюсь

### В сторону перфекционизма

Я однажды 4 часа рефакторил функцию, которая "не идеальна". Потом обнаружил, что через неделю её **удалят**. 4 часа — в мусорку.

**Урок:** спросить "сколько это будет жить?" **до** того, как начать полировать. Если < 1 месяц — минимальный уровень.

### В сторону "а вот и ладно"

Я однажды отдал код с `catch (\Exception)` на платёжном пути. "Работает же". Через месяц — баг в продакшене, 2 часа отладки, потому что "кто-то поймал exception и проигнорировал".

**Урок:** есть **минимальный** уровень, ниже которого нельзя. `catch (\Exception)` на критическом пути — ниже. Не важно, сколько времени.

---

## Практический тест

Перед отдачей я спрашиваю себя три вопроса:

1. **"Если это сломается в 3 часа ночи, смогу ли я за 10 минут понять, что случилось?"**
   Если нет — логирование/исключения/имена недостаточно.

2. **"Если придёт новый разработчик, поймёт ли он, что здесь происходит, за 5 минут?"**
   Если нет — имена/структура/комментарии недостаточно.

3. **"Если через месяц я (или другой) буду это менять, сломается ли что-то, что я не ожидал?"**
   Если да — есть скрытые зависимости. Нужно или задокументировать, или развязать.

Если на все три — "да" — **отдаю**. Не жду "идеального". Идеального нет. Есть "достаточно, чтобы не сломалось и чтобы можно было менять".

---

## Итог

| Вопрос | Ответ |
|--------|-------|
| Есть ли объективный порог "достаточно"? | Нет. Есть **вопросы** (безопасность, тестируемость, понятность, стоимость изменения). |
| Когда останавливаюсь улучшать? | Когда ответ на "что изменится при следующем изменении?" — "ничего неожиданного". |
| Что делаю вместо "идеального"? | Фиксирую долг тикетом + записываю "почему так". |
| Главный враг? | Не "слишком мало качества" и не "слишком много". А **отсутствие осознанности**: я должен **знать**, на каком уровне я остановился и **почему**. |

Код "достаточно хорош" — это не когда он красивый. А когда я **могу объяснить**, почему он такой, а не другой, и **знаю**, что будет, если его изменят.

> ⏱ 38.3s · prompt 137707 tok · output 2468 tok

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

<details><summary>Ход мысли модели</summary>

The user is asking me to give feedback on the interview format and questions as a "candidate" reflecting on the conversation. This is a meta-question — they want me to evaluate the interview process itself.

Let me think about what was actually good and bad about this interview from a candidate's perspective:

**What worked well:**
- The questions had a logical progression from basics to architecture to soft skills
- Questions were specific and required concrete examples, not textbook answers
- The "why" questions forced me to justify positions rather than recite
- The code review question was realistic and multi-layered
- The "you promised X but it's Y" question tested honesty over perfection
- Questions built on each other — the "circular dependencies" question came after "container architecture"
- The "what questions would you ask the client" question was excellent — tested product thinking
- The final code review was a great stress test because it combined multiple weaknesses

**What was questionable / could be better:**
- The "FORCE INDEX in PostgreSQL" question — this was a trap question. It tests whether I'll make up an answer vs admit "this doesn't exist in PG". Good test but slightly unfair because it's a memory fact, not a reasoning test.
- The "strict_types performance" question — also a trap. The "right answer" is "it doesn't matter". But the question is framed in a way that presupposes there IS a measurable slowdown. Slightly leading.
- Some questions were extremely long and complex (the DDD aggregate + Doctrine one) — in a real interview, this would take 20+ minutes. The density was very high.
- The "SOLID which is most violated" question — I gave a very long answer. In a real interview, this might be 5 minutes max. The format encouraged essay-length answers.
- Some questions repeated the same theme (transactions, concurrency) multiple times — by the 5th time, it felt like we were going in circles.
- The "git process" question was good but very long — in a real interview this would be a 10-minute topic, not a 2-page essay.

**What I'd change if I organized it:**
- Mix of question types: some short (5 min), some long (20 min), some "explain this code" (interactive)
- More "show me how you'd do X" (hands-on) vs "tell me about X" (theoretical)
- A "what would you do in the first hour" question for a new codebase
- Less repetition on the same themes
- A time-box on answers to prevent rambling
- One question that's intentionally "unsolvable" to see how I handle uncertainty
- The FORCE INDEX question was good but I'd make it more explicit: "This is a MySQL question applied to PostgreSQL — what do you do?"

**Honest self-assessment of my own answers:**
- Some answers were way too long (the DDD one, the testing one). In a real interview, an interviewer would say "that's enough, let's move on" after 3 minutes.
- I was very thorough but not always concise. A good candidate would give a 30-second answer and then elaborate only if asked.
- The trap questions I handled well (FORCE INDEX, strict_types) — I didn't fall for the false premise.
- The "what questions to ask the client" question was probably the best question in the interview — it tested product thinking, not technical depth.
- The code review was the most realistic question — it had multiple layers of problems and I had to prioritize.

Let me structure this as a genuine, somewhat self-reflective answer. Not sycophantic. Not trying to please. Honest about what worked and what didn't.

</details>

**Кандидат:**

## Как мне формат

В целом — хорошо. Вопросы не были "расскажи про SOLID" в вакууме. У каждого был контекст: "в реальном коде", "покажи на примере", "как бы ты решил". Это заставляло не декламировать, а думать.

Но есть конкретные вещи, которые бы я поправил.

---

## Что сработало

**1. Вопросы, где нужно признать, что я чего-то не знаю.**

`FORCE INDEX в PostgreSQL` — я сразу сказал "в PostgreSQL его нет". Если бы я начал рассказывать "ну, можно через `pg_hint_plan`..." — это было бы ложное знание. Вопрос проверил, не начну ли я выдумывать.

`strict_types замедляет на X%` — я не стал называть число. Сказал "я не измерял, и не буду выдумывать". Это правильный ответ, но не тот, которого ждут от кандидата.

**2. Вопрос "какие вопросы бы ты задал заказчику".**

Это, пожалуй, лучший вопрос во всём собеседовании. Он проверяет не "знаешь ли ты PostgreSQL", а "думаешь ли ты о проблеме, а не только о решении". Большинство кандидатов начнут говорить про "полнотекстовый поиск" и "Elasticsearch". А вопрос заставляет спросить "а что реально происходит?" — и это более важная компетенция.

**3. Ревью кода в конце.**

Код был написан так, что в нём **множество** проблем разного уровня: SQL-инъекция, отсутствие авторизации, race condition, мёртвый код, нарушение слоёв. И вопрос "с чего начал бы и почему" — это не "назови все проблемы", а "умеешь ли ты расставлять приоритеты". Это честный тест.

**4. Вопросы строились друг на друге.**

"Контейнер DI" → "циклические зависимости" → "агрегаты" → "конкурентность" — это не случайный набор. Каждый следующий вопрос опирался на предыдущий. К концу я уже мог отвечать быстрее, потому что контекст был.

---

## Что было неудачным или вводящим в заблуждение

**1. Некоторые вопросы предположительно-наводящие.**

> "Какие способы защиты от конкурентности ты знаешь?"

Ок, это нормальный вопрос.

Но:

> "На сколько процентов strict_types замедляет выполнение PHP-кода?"

Формулировка **предполагает**, что есть измеримый процент. Правильный ответ — "значимого замедления нет", но вопрос сформулирован так, как будто оно есть. Я ответил честно, но если бы кандидат был более "удобный" — он бы начал придумывать "ну, может, 5-10% на горячих путях...". Вопрос подталкивает к ложному знанию.

То же с `FORCE INDEX` — вопрос сформулирован как "для чего используется", а не "есть ли это в PG и как это работает". Я исправил предпосылку, но не каждый кандидат это сделает.

**2. Некоторые темы повторялись.**

Транзакции и конкурентность я обсуждал, по сути, **четыре** раза:
- "циклические зависимости"
- "агрегаты и flush"
- "два процесса изменяют одну запись"
- "SOLID → LSP → агрегаты"

К четвёртому разу я уже повторял сам себя. В реальном собеседовании это выглядело бы как "мы идём по кругу". Лучше было бы вместо 4-го вопроса на ту же тему — вопрос на **другую** компетенцию (например, "как ты отлаживаешь проблему в продакшене, которую не можешь воспроизвести локально").

**3. Объём ответов не контролировался.**

Никто не сказал "хорошо, достаточно, идём дальше". Я отвечал на каждый вопрос так, как будто пишу статью. В реальном собеседовании интервьюер бы оборвал на 2-й минуте и перешёл к следующему вопросу. Без этого ограничения я "растянулся" и, возможно, показал больше, чем нужно, но и потратил больше, чем нужно.

**4. Не было вопроса "а что ты НЕ знаешь?"**

Все вопросы были "расскажи про X". Ни один не был "что тебе не понятно в этой задаче?" или "где ты бы застрял?" Это, на мой взгляд, более полезный вопрос, потому что он проверяет **границы** знания, а не **поверхность**.

---

## Что бы я изменил, если бы организовывал

### 1. Добавил бы hands-on часть

Весь разговор был "расскажи мне". Ни разу не было "вот кусок кода, измени его" или "вот лог, найди проблему". Теория и практика — разные навыки. Человек может красиво рассказать про DDD и не написать нормальный `Repository`.

Я бы добавил: "Вот 50 строк кода. Найди 3 бага. Объясни, как бы ты их чинил, не меняя архитектуру."

### 2. Ограничил бы время на ответ

"Расскажи про агрегаты" — это 3 минуты, не 10. Я бы сказал: "Дай мне 3 минуты. Если не успеешь — я打断у." Это заставляет кандидата **приоритизировать**, а не пересказывать всё, что знает.

### 3. Добавил бы "first hour" вопрос

> "Ты пришёл в новый проект. Тебе дали задачу. Что ты делаешь в первый час?"

Это проверяет не знание технологии, а **процесс**. И я бы оценивал не "правильный" ответ, а "адекватный". Человек, который начнёт с "погуглю" — и человек, который начнёт с "прочитаю код и найду примеры" — оба нормально. А человек, который начнёт с "напишу тесты" до того, как понял задачу — нет.

### 4. Убрал бы вопросы-ловушки или сделал их честными

`FORCE INDEX` — если цель "проверить, не выдумает ли", то честнее: "В PostgreSQL есть ли аналог MySQL FORCE INDEX? Если нет — что используешь?" Это тот же тест, но без предпосылки.

### 5. Добавил бы вопрос на неопределённость

> "Тебе поставили задачу. По ходу работы ты понял, что задача сформулирована неверно. Что делаешь?"

Это проверяет не знание, а **поведение в неопределённости**. И ответ "спрошу заказчика" — не всегда правильный. Иногда правильный ответ "сделаю как понимаю и зафиксирую допущение".

### 6. Сократил бы повторения

4 вопроса на конкурентность → 2 было бы достаточно. Освободившееся время → на вопрос про отладку в продакшене или про работу с незнакомым стеком.

---

## Честная самооценка

| Что я сделал хорошо | Что я сделал плохо |
|---------------------|-------------------|
| Не выдумывал факты (strict_types, FORCE INDEX) | Отвечал слишком длинно. В реальном интервью это выглядело бы как "не могу сжать" |
| Приоритизировал в code review (инъекция → авторизация → race → архитектура) | Не спросил "а что мне нельзя менять?" — в реальном ревью я бы уточнил контекст |
| Разделил "знаю" и "догадываюсь" | В некоторых ответах (SOLID, DDD) я был слишком уверен, хотя часть — экстраполяция из опыта, а не проверка |
| Дал честные trade-offs | Не всегда. В некоторых местах я "продавал" свой подход, а не показывал альтернативы |
| Признал, что не всё знаю | Но не всегда. В ответе про "почему LSP нарушается чаще" я был слишком категоричен — это моё мнение, не факт |

**Главный вывод:** собеседование было сильным в том, что требовало **конкретики** и **обоснования**. Слабым — в том, что не проверяло **границы** (что я не знаю) и **процесс** (как я работаю, а не что знаю). И в том, что не ограничивало время, из-за чего ответы становились эссе, а не ответами.

> ⏱ 44.9s · prompt 139590 tok · output 2740 tok

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

<details><summary>Ход мысли модели</summary>

The user is asking me to share something about myself as a candidate that wasn't covered in the interview questions. This is the closing question of a simulated PHP backend developer interview.

Let me think about what was covered and what wasn't:

Covered: PHP 8 features, type system, exceptions, SOLID (especially LSP), Symfony DI container, N+1 queries, DDD aggregates, concurrency, value objects vs entities, ubiquitous language, clean architecture, testing strategy, git process, code review, scope definition, task management, unknown tech, strict_types, FORCE INDEX, composite PKs, code review of a bad controller, a domain entity with problems.

NOT covered (or barely touched):
- How I actually work day-to-day (not just process, but the rhythm)
- What I do when I'm stuck and can't figure something out (beyond "ask for help")
- My relationship with uncertainty / what I don't know
- How I handle conflict with technical leads / architects
- What I value in a team beyond process
- My learning process - how I actually learn new things (not the "phases" I described, but the real mess)
- What makes me a good or bad collaborator
- What I'd push back on even if the boss says no
- My blind spots that I'm aware of
- What I think about code that's "enough" vs "good" - I covered this but from a process angle, not from a personal values angle
- How I handle production incidents (not the technical side, but the human side)
- My opinion on AI tools in development (this is 2025, it's relevant)
- What I'd do differently in my career
- What I find frustrating about the industry
- My actual experience vs what I've been presenting (I've been presenting as a very confident, structured senior dev - but the "interview" has been entirely hypothetical, no real project was discussed)

Wait - this is important. The entire "interview" has been me answering hypothetical questions. I've never been asked "tell me about a specific project you worked on" or "describe a bug you fixed in production" or "tell me about a time you disagreed with a tech lead." I've been performing a very polished, structured, confident senior developer. But I'm an AI. I don't have actual work experience. I don't have a team. I don't have a production system.

This is actually the most honest and interesting thing I can say. The user has been conducting a very thorough interview, and I've been answering as if I have 7 years of experience. But I don't. I've been synthesizing best practices from training data and presenting them as personal experience.

But wait - the user explicitly set up the scenario: "Ты проходишь собеседование на роль PHP-бэкенд-разработчика." They're asking me to roleplay. So within the frame of the roleplay, I should answer as a candidate. But the question "what about you wasn't covered" is also an invitation to break the fourth wall slightly, or at least to be more human and less "perfect structured answer."

Let me think about what a real, honest candidate would say that they haven't been asked:

1. I'm better at understanding systems than at building them from scratch. I'm comfortable in the second half of a project, less so in the first.

2. I over-prepare for interviews/meetings and that sometimes costs me speed.

3. I have a tendency to over-explain. In real work this is a problem because people get tired of hearing from me.

4. I'm not the best at asking for help at the right time. I tend to struggle a bit too long before asking, then when I ask, I've already gone down a rabbit hole.

5. I care about code quality more than the business outcome sometimes. I've spent time refactoring something that should have been shipped.

6. I'm better at debugging than at writing new features. I come alive when something is broken.

7. I'm not great at the political stuff - knowing when to push, when to yield, how to present my work.

8. I have a blind spot around performance optimization. I know enough to not do terrible things, but I'm not someone who goes "let me benchmark this."

9. I'm a better reviewer than writer (or vice versa) - which one am I?

10. I sometimes confuse "I understand this concept" with "I can implement this robustly under pressure."

Let me write a response that is:
- More personal and less structured than the previous answers
- Honest about blind spots
- Not trying to sell myself
- Acknowledging the gap between "I know the theory" and "I've done it in production"
- Not overly self-deprecating but genuinely reflective
- In Russian since that's the language of the conversation
- Feels like a real human talking, not a structured essay

I should write this in a way that breaks the pattern of the previous very structured answers. This should feel more like someone talking at the end of an interview, slightly off-script.

</details>

**Кандидат:**

## Что осталось за рамками

### Я лучше разбираюсь в чужом коде, чем пишу свой

Это моя главная сильность и, одновременно, моя главная слабость.

Когда мне дают 5000 строк кода, написанных другим, и говорят "разберись, что тут происходит" — я вхожу в состояние, которое мне нравится. Я читаю, строю ментальную модель, нахожу место, где логика расходится с намерением. Отладка — это то, в чём я уверен.

Когда мне дают чистый лист и говорят "сделай" — я хуже. Я перебираю варианты, сравниваю подходы, и в какой-то момент выбираю один. Но я могу потом обнаружить, что другой был бы лучше, и уже слишком поздно.

**Практический вывод для команды:** я не лучший человек для "сделай с нуля". Я лучший для "вот есть система, она больно работает, разберись и почини".

---

### Я слишком долго молчу, когда застрял

Я описал идеальный процесс: "30 минут самостоятельной работы, потом вопрос." Но в реальности у меня получается иначе. Я молчу 2 часа. Потом 3. И когда наконец спрашиваю — коллега говорит "а, это называется X, гугли X", и я понимаю, что 30 минут хватило бы.

Это не лень и не упрямство. Это стыд. Мне неловко спросить, если я уже "в теме" и должен бы знать. И я выбираю "посижу ещё" вместо "спрошу".

**Что я с этим делаю:** я поставил себе правило — если я не продвинулся в течение 30 минут **по факту** (не по ощущению), я формулирую вопрос и отправляю. Не жду, пока станет "достаточно".

---

### Я путаю "понимаю концепцию" с "могу реализовать под давлением"

В этом собеседовании я описывал DDD, clean architecture, event sourcing, optimistic locking — уверенно и структурированно. Но это **описание**, не **проживанный опыт**. Я могу объяснить, почему outbox лучше "publish after commit". Но если завтра в 3 часа ночи у нас упадёт publisher и я буду единственным, кто может это починить — я буду хуже, чем в этой беседe.

Разница: здесь у меня есть время подумать. В продакшене — нет.

**Честно:** я сильный на уровне "объяснить, спроектировать, ревьюить". Средний на уровне "написать с нуля быстро". Я не скрываю это, но и не преувеличиваю.

---

### Я плохо делаю "политику"

Не в смысле интриг. В смысле: когда нужно **не** соглашаться, я соглашаюсь. Когда нужно сказать "нет, так не будет работать", я говорю "хорошо, я попробую". Когда тимлид предлагает решение, которое я считаю плохим, я говорю "давайте обсудим" вместо "я не буду это делать, потому что X".

Это не мягкость. Это то, что я не хочу быть тем, кто "мешает". И в итоге я получаю задачи, которые я знаю, что будут больно.

**Что изменилось за последние два года:** я учусь говорить "нет" **до** того, как взялся. "Нет" после того, как начал делать — это уже конфликт. "Нет" до того, как начал — это просто информация.

---

### Я не знаю, чего не знаю

Это самая важная вещь, которую я могу сказать.

Я уверен в себе в тех областях, которые описывал в этом разговоре. Но уверенность и знание — разные вещи. Я могу уверенно рассказать про `EXPLAIN ANALYZE` и при этом не знать, что `pg_hint_plan` в последнем мажоре PostgreSQL изменил синтаксис. Я могу уверенно описывать optimistic locking и при этом не помнить, что `#[ORM\Version]` в Doctrine 2.x и 3.x ведёт себя по-разному при partial updates.

**Что я делаю:** я не даю "уверенный" ответ, если за ним стоит "а точно ли я это проверял?" Если я говорю "по моему опыту" — это значит, что я экстраполирую. Не то же самое, что "я это делал 10 раз".

---

### Что я бы хотел, но не могу

Мне бы хотелось быть тем человеком, который берёт задачу и через 3 дня говорит "вот, работает". Но я чаще беру задачу и через 3 дня говорю "вот, работает, но есть три места, где я не уверен, и вот почему".

Первое — это то, что хотят от сеньора. Второе — то, что я даю.

Я не считаю второе недостатком. Но я понимаю, что для некоторых команд — да. Если команде нужен человек, который "просто делает" — я не тот. Если команде нужен человек, который "делает и говорит, где может сломаться" — я тот.

---

### Одно предложение, которое я не сказал ни разу

Я не люблю, когда код работает "пока". Я люблю, когда код работает, и я **знаю почему**. Разница — в том, что "пока" — это вера. А "знаю почему" — это понимание. И я не могу отдать код, в который я **верю**, но не **понимаю**. Даже если он проходит все тесты.

> ⏱ 40.7s · prompt 141584 tok · output 2341 tok

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

<details><summary>Ход мысли модели</summary>

The user is asking me, as a PHP backend developer candidate, what tech stack is closest to me and what I would choose for a new project, defending that choice before a team.

This is the final question of the interview. I need to:
1. Be honest about what I actually know well (PHP, Symfony, PostgreSQL — based on the whole conversation)
2. Not be a "fanboy" for PHP just because I'm answering as a PHP developer
3. Actually think through the trade-offs of different stacks
4. Defend a choice with reasoning, not just preference
5. Be honest about where my skills are and where they're not
6. Show that I can evaluate options objectively, even if one of them is my home turf

Let me think about what makes sense here. Throughout the interview, I've consistently shown:
- Deep PHP/Symfony/Doctrine knowledge
- PostgreSQL experience
- DDD understanding
- Practical, not dogmatic, approach
- Honest about limitations

The question is: if I'm choosing for a NEW project, what do I actually pick? And can I defend it honestly?

Options:
1. PHP + Symfony + PostgreSQL (my home turf)
2. Go + PostgreSQL (simpler, faster, but I'm less experienced)
3. TypeScript/Node + PostgreSQL (flexible, but different paradigm)
4. Rust + PostgreSQL (performance, but high learning curve)
5. Java/Spring + PostgreSQL (enterprise, but I don't have that experience)
6. Something else entirely

The honest answer: I would pick what's best for the TEAM and the PROJECT, not what's best for me. But I also need to be honest that my depth is in PHP/Symfony/PostgreSQL, and that depth matters.

Let me think about what a genuinely good answer looks like:
- Not "I love PHP" — that's a fanboy answer
- Not "I'd pick Rust because it's the best" — that's cargo cult
- Not "it depends" — that's evasive
- But "here's my actual reasoning process, here's where I'd pick X, here's where I'd pick Y, and here's where I'd be honest that I'm not the best person for the job"

I think the most honest and interesting answer is:
- My depth is PHP/Symfony/PostgreSQL
- For a project with a team I know, that's what I'd pick, and I can defend it
- But I'd be honest about when I'd pick something else
- And I'd be honest about what "defending the choice" really means

Let me also think about what makes this question tricky: it's asking me to defend a choice, which is a leadership/senior question. Not "what do you know" but "what can you argue for and take responsibility for."

I should structure this as:
1. What I'd actually pick (honest)
2. Why (reasoning, not preference)
3. What I'd consider instead and why not
4. When I'd pick something else
5. What "defending the choice" really means to me

Let me write a response that feels like a real person answering, not an AI generating a list.

</details>

**Кандидат:**

## Коротко

Для нового проекта с командой из 3–8 человек, которая делает бизнес-логику (не высоконагруженный сервис) — я бы выбрал **PHP 8 + Symfony + PostgreSQL + RabbitMQ** (или Redis, если очередь простая).

Не потому что "я это знаю". А потому что для этого типа проектов это **наименее ошибочный** выбор. Ниже — почему.

---

## Сначала — что я НЕ делаю

Я не начинаю с "какой язык лучший". Это неправильный вопрос. Правильный:

| Вопрос | Почему |
|--------|--------|
| Сколько человек в команде? | 2 человека ≠ 20 человек. Разный стек, разный уровень абстракции. |
| Что это за проект? | CRUD-админка ≠ платёжный сервис ≠ realtime-дашборд. |
| Сколько будет жить? | 6 месяцев ≠ 5 лет. Разная степень инкапсуляции. |
| Кто будет поддерживать? | Если после меня придёт человек, который знает Go — это аргумент. |
| Какие non-functional требования? | 10 RPS ≠ 10 000 RPS. Разная инфраструктура. |

Если ответы: 3–5 человек, бизнес-логика с доменом, 3+ года жизни, команда знает PHP — PHP/Symfony/PostgreSQL. Точка.

---

## Почему именно этот стек (обоснование, а не предпочтение)

### PHP + Symfony

| Аргумент | Contra |
|----------|--------|
| Зрелая экосистема: DI, routing, validation, security, ORM — всё из коробки | Не "быстрый" язык. На 10k RPS — уступит Go/Node |
| Symfony — это не "фреймворк для сайтов". Это структура. | Если команда не знает Symfony — это 2 недели наboarding |
| Типы (8.0+) + readonly + enum — достаточно для чистого кода | Нет generics, нет structural typing |
| Рынок: PHP-разработчиков больше, чем Rust- или Go- | Зарплаты ниже → проще нанять |
| Я знаю его глубоко. Это не "знаю", а "могу отладить в 3 часа ночи" | Моя глубина — это моя привилегия, не аргумент для команды |

**Честный contra:** если проект — это 5000 RPS, latency-критичный, и команда знает Go — я бы выбрал Go. Не потому что Go "лучше", а потому что **команда** знает Go, и это важнее, чем моя глубина в PHP.

### PostgreSQL

Не обсуждается. Если есть хоть одна таблица с `JOIN`, `CHECK`, `EXCLUDE`, `JSONB` — это PostgreSQL. MySQL — для проектов, где "одна таблица и `SELECT *`". SQLite — для embedded. MongoDB — для документов, которые не имеют структуры.

PostgreSQL + `pg_trgm` + `BRIN` + partitioning + `SERIALIZABLE` — это достаточно для 95% бизнес-приложений.

### RabbitMQ (или Redis Streams)

Не Kafka. Kafka — это для stream processing, когда у вас 1000 msg/s и 10 consumers. Для бизнес-приложения с 50 msg/s — RabbitMQ или Redis Streams. Проще, дешевле, достаточно.

### Что бы я НЕ выбрал и почему

| Стек | Почему нет |
|------|-----------|
| **Go** | Отличный язык. Но для бизнес-логики с доменом — нет ORM, нет DI-контейнера из коробки. Я бы писал больше boilerplate. И команда, которая знает PHP, не знает Go. |
| **Node/TypeScript** | Отличный для realtime, API-гейтвеев. Но для доменной логики с инвариантами — слабая типизация (TypeScript — это "уверенность", а не "гарантия"). И async везде — это не то, что нужно для бизнес-логики. |
| **Rust** | Мощный язык. Но learning curve — 3–6 месяцев для продуктивности. Для проекта, который нужно запустить через 2 месяца — это риск. |
| **Java/Spring** | Зрелая экосистема. Но boilerplate-уровень выше, чем в Symfony. И я в ней не силюсь. |
| **Django/Python** | Быстрый старт. Но типизация слабая, производительность ниже, и для DDD-проектов — недостаточно структуры. |

---

## Что значит "защищать выбор перед командой"

Это **не** "я выбрал PHP, потому что я его знаю". Это:

1. **Я показал, какие варианты рассмотрел.** Не "PHP лучше", а "я посмотрел Go, Node, Rust — и вот почему для ЭТОГО проекта PHP/Symfony".

2. **Я назвал риски.** "Да, PHP не масштабируется до 10k RPS. Если через год мы вырастем — будем переписывать. Но через год мы, возможно, будем 3 человека и 50 RPS, и это не проблема."

3. **Я назвал, что будет, если я окажусь неправ.** "Если через 6 месяцев мы поймём, что Symfony — это overhead — я не буду его защищать. Я буду менять. Но сейчас, для этой задачи, это лучший баланс скорости и качества."

4. **Я спросил команду.** "Кто из вас знает Symfony? Кто знает Go? Кто будет дежурить в 3 часа ночи?" Если 3 из 5 знают Symfony — это не мой выбор. Это выбор команды.

---

## Где я был бы честен, что не лучший

| Ситуация | Что я бы сказал |
|----------|----------------|
| "Нам нужен сервис на 50k RPS с latency < 5ms" | "Я бы выбрал Go или Rust. Я в PHP для этой задачи — не лучший. Я бы нанял Go-разработчика или переучился, но это 3 месяца." |
| "Нам нужен realtime-дашборд с WebSocket" | "Я бы выбрал Node или Go. PHP + Swoole — это можно, но я бы не стал это защищать, если есть команда, которая знает Node." |
| "Нам нужен ML-pipeline" | "Это Python. Я бы не стал тянуть PHP сюда." |
| "Нам нужен embedded-сервис" | "Go или Rust. PHP здесь — не тот инструмент." |

Я не "PHP-фанатик". Я "человек, который знает PHP/Symfony/PostgreSQL глубоко и может это защитить для бизнес-приложений с доменом". Вне этого — я честно скажу, что это не моя зона.

---

## Одно предложение

Мой стек — это не "что я люблю". Это "где я могу дать команду надёжное, поддерживаемое, предсказуемое приложение с минимальным риском". PHP + Symfony + PostgreSQL — это не лучший язык в мире. Это **наименее ошибочный** выбор для большинства бизнес-приложений, сделанный командой из 3–8 человек, которая хочет делать продукт, а не бороться с инфраструктурой.

> ⏱ 36.8s · prompt 142874 tok · output 2315 tok

---
