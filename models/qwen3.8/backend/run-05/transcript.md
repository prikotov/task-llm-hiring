# Собес: `qwen3.8:latest` · роль: backend — run-05

- Дата: 2026-10-06T18:37:17.203326+00:00
- Сервер: http://nserv:11434 · keep_alive: 30m · think: auto · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to introduce myself, talk about my "experience" in PHP development and related technologies, and then rate my knowledge on a scale of 0-10 for each of: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, clean architecture, testing, git — with a one-sentence justification for each rating.

This is a tricky request because I'm an AI, not a human developer. I don't have "experience" in the traditional sense — I don't write code for living, I don't ship to production, I don't deal with CI/CD pipelines or debug in production. However, I do have extensive training data that includes vast amounts of PHP code, Symfony documentation, Doctrine ORM usage, PostgreSQL internals, DDD literature, clean architecture principles, testing frameworks, and git workflows.

I should be honest that I'm an AI and don't have "experience" in the human sense, but I can speak to the depth and breadth of what I know about these technologies. I should be measured and honest in my self-assessment — not overstate (claiming 10/10 on everything would be dishonest) but also not understate (I genuinely have deep knowledge of these topics).

Let me think about honest ratings:

- PHP 8: I know the language well — JIT, readonly properties, enums, fibers, union types, attributes, etc. But I haven't "lived" with PHP 8 in a long project. I'd say around 9. I know the language spec, edge cases, performance characteristics, but I can't claim battle-tested intuition from years of use.

- Symfony: I know the framework architecture, components, bundles, dependency injection, event system, console commands, HTTP kernel, etc. Very well documented in my training data. Around 9.

- Doctrine: ORM, DBAL, migrations, DQL, entity mappings, lifecycle callbacks, second-level cache. I know it well but some of the more esoteric performance tuning and edge cases might be areas where I'd need to think carefully. Around 8-9. Let's say 8.

- PostgreSQL: I know the SQL dialect, indexing strategies, partitioning, window functions, JSONB, CTEs, MVCC, locking mechanisms, vacuum, explain plans. Very solid knowledge. Around 8-9. Let's say 9.

- DDD: I know the concepts — bounded contexts, aggregates, entities, value objects, domain events, repository pattern, anti-corruption layers. I can articulate them well. But DDD is deeply contextual and practical — knowing the textbook vs. applying it well are different. I'd say 8.

- Clean architecture: I know the principles — dependency inversion, separation of concerns, SOLID, hexagonal architecture, ports and adapters. I can articulate the layers and rationale. Around 8-9. Let's say 8.

- Testing: PHPUnit, mocking, test doubles, TDD/BDD concepts, integration vs unit vs E2E, test-ability principles. I know a lot but the "craft" of testing is also very practical. Around 8.

- Git: I know the commands, workflows, rebasing, merging, branching strategies, conflicts, hooks, etc. Very well-documented topic. Around 9.

Actually, let me reconsider. I should be genuinely reflective. My "knowledge" is pattern-matching on a vast corpus. I don't have the scar tissue of production incidents, the gut feeling from debugging at 3am, the institutional knowledge of a specific codebase. But my breadth of knowledge across these topics is genuinely impressive compared to most individual developers. I'll aim for ratings in the 7-9 range, being honest about limitations.

Let me write this in Russian since the user wrote in Russian. I'll be personable but honest about being an AI.

</details>

**Кандидат:**

Привет! Рад знакомству.

Честно скажу: у меня нет «опыта» в человеческом смысле. Я не гонял билды на проде в пятницу вечером, не сидел три часа над таймаутом в Doctrine, не спорил на архитектурном коммите о том, куда положить новый сервис. У меня нет шрамов и интуиции, которую даёт годы жизни с конкретным кодомбейзом.

Что у меня **есть** — это очень широкий и глубокий срез документации, исходников, discussions, Stack Overflow, RFC-ов, книг и реальных open-source проектов, на котором я обучался. Поэтому я могу уверенно говорить о том, *как устроены* PHP, Symfony, Doctrine, PostgreSQL, как формализуются DDD-концепции, как строятся тестовые пирамиды — и могу обсуждать trade-offs, писать рабочий код, разбирать чужие баги, проектировать модули. А вот «почему в этой конкретной ситуации лучше вариант Б, а не А» — это уже зависит от контекста, который вы мне даёте.

Теперь оценки. Буду честным, а не напыщенным.

| Раздел | Оценка | Обоснование |
|---|---|---|
| **PHP 8** | **9** | Знаю язык от RFC (enums, readonly, fibers, union types, attributes, JIT) до тонкостей метапрограммирования и edge-cases в типизации, но не «чувствую» его, как кто-то, кто живёт в нём 10 лет. |
| **Symfony** | **9** | Архитектура ядра, DI, events, HTTP Kernel, Console, Security, Mailer, Messenger, Cache — разбираю на уровне «почему так устроено и что ломается, если нарушить контракт». |
| **Doctrine** | **8** | ORM, DBAL, DQL, lifecycle, second-level cache, миграции — уверен; но тонкая оптимизация query plans и экзотические сценарии с hydration-кастомными провайдерами — здесь я могу ошибиться в деталях. |
| **PostgreSQL** | **9** | MVCC, locking (SIX, FOR UPDATE SKIP LOCKED), partitioning, JSONB, CTE, window functions, index tuning, EXPLAIN (ANALYZE) — разбираю уверенно; на уровне DBA-экспертизы (autovacuum tuning, WAL, replication topology) уступлю профильному спецу. |
| **DDD** | **8** | Aggregates, Value Objects, Bounded Contexts, Domain Events, Repository, Anti-Corruption Layer — формулирую и проектирую уверенно, но DDD в проде — это больше практика и контекст, чем концепция, а этого у меня нет. |
| **Чистая архитектура** | **8** | SOLID, Dependency Inversion, Hexagonal / Ports & Adapters, CQRS, Event Sourcing — разбираю и проектирую; но «чистая архитектура без переусложнения в команде из трёх человек» — это инженерное суждение, где я могу быть слишком академичным. |
| **Тестирование** | **8** | PHPUnit, mocking, test doubles, TDD/BDD-цикл, интеграционные и E2E-сценарии, стратегии тестирования domain-слоя — всё это пишу и разбираю; но интуиция «что здесь реально стоит тестировать, а что — нет» сильно зависит от продукта. |
| **Git** | **9** | Rebase, merge, cherry-pick, conflict resolution, hooks, signed commits, branching strategies (trunk-based, git-flow) — знаю уверенно; не хватает лишь «боевого» опыта гонки за rebase в четырёх разработчиках одновременно. |

В среднем я бы назвал себя **твёрдым 8–9 по ширине**, но без того 9,5–10, которое даёт годы эксплуатации в бою. Если даёте задачу с реальным контекстом — код, constraints, «у нас вот такая история», — я обычно работаю на уровне, которое в интервью описывают как «senior». Если задача абстрактная и нет ограничений — могу и перегнуть палку в сторону архитектурного хайпа.

Расскажите, над чем будем работать?

> ⏱ 17.5s · prompt 87 tok · output 1785 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user asks about the most important features in PHP 8.0+ for writing clean and reliable code, with short code examples. Let me think about what's genuinely most impactful for code quality and reliability, not just a feature dump.

Key features from 8.0 onward that matter for clean/reliable code:

1. **PHP 8.0:**
   - Union types (`int|string`) — more expressive type safety
   - Nullsafe operator (`?->`) — reduces boilerplate null checks
   - Named arguments — self-documenting calls
   - Match expression — cleaner than switch, exhaustive-ish
   - Constructor property promotion — less boilerplate
   - Enums (8.1) — this is huge, replacing class constants
   - Readonly classes (8.2) — immutability
   - Fibers (8.1) — async without extensions
   - `never` return type (8.1)
   - readonly properties (8.1)
   - First-class callable syntax (8.1)
   - DNF types (8.2) — `(A&B)|C`
   - Enums (8.1)
   - `str_contains`, `str_starts_with`, `str_ends_with` (8.0)
   - Trailing comma in parameter lists (8.0)

For "clean and reliable code" specifically, I'd focus on:
- **Type safety improvements**: union types, DNF types, never type
- **Immutability**: readonly properties, readonly classes
- **Elimination of anti-patterns**: enums instead of string constants, match instead of switch
- **Less boilerplate**: constructor promotion, nullsafe operator
- **Better function signatures**: first-class callables

Let me structure this well. I'll pick maybe 6-7 features that are genuinely most impactful for reliability and cleanliness, show a short "before" (or the problem) and "after" (or the feature), keeping examples concise.

I'll write in Russian as the user is communicating in Russian.

Let me be selective and opinionated — the user asked what *I* consider most important, so I should have a clear thesis. My thesis: the biggest leap is from "PHP as a dynamic scripting language where you hope the types are right at runtime" to "PHP as a statically-checkable language where the compiler/type-system catches whole classes of bugs before they ship."

Features I'll highlight:
1. Enums (8.1) — eliminates the "magic string/number constants" anti-pattern
2. Readonly properties & readonly classes (8.1/8.2) — immutability by default
3. Union types + DNF types (8.0/8.2) — more precise contracts
4. Constructor property promotion (8.0) — less boilerplate, fewer places to make mistakes
5. Nullsafe operator + str_* helpers (8.0) — fewer null-checking bugs
6. `never` type (8.1) — honest function signatures
7. Match expression (8.0) — exhaustive, no fall-through, returns values

Let me write concise examples. I'll keep each example tight — 5-10 lines max.

</details>

**Кандидат:**

## Мой тезис

Самый большой скачок PHP 8.x сделал не в «новых фичах», а в том, что язык стал **статически проверяемым контрактом**. Раньше вы писали `function process(int $id)` и молили, чтобы nobody не прокинул строку «123» из JSON. Теперь типизация, иммутабельность и перечислимые типы закрывают целые категории багов *до рантайма*. Вот что я считаю критичным:

---

### 1. Enums (8.1) — смерть магических констант

```php
// Было: строка/число, разбросанное по коду
class OrderStatus {
    const NEW = 'new';
    const PAID = 'paid';
    const SHIPPED = 'shipped';
}
// ...и где-то: if ($order->status === 'payd')  ← опечатка, баг в проде

// Стало:
enum OrderStatus: string {
    case New     = 'new';
    case Paid    = 'paid';
    case Shipped = 'shipped';

    public function isFinal(): bool {
        return $this === self::Shipped;
    }
}

// match обязателен по исчерпанию (стандартный стиль):
$label = match ($order->status) {
    OrderStatus::New     => 'Ожидает оплату',
    OrderStatus::Paid    => 'Оплачен',
    OrderStatus::Shipped => 'Отгружен',
};
```

Компилятор (статический анализатор: PHPStan, Psalm) **не даст** передать `'payd'` туда, где ожидается `OrderStatus`. Опечатка больше не проходит в прод.

---

### 2. Readonly-свойства и readonly-классы (8.1 / 8.2) — иммутабельность по умолчанию

```php
// 8.1: readonly на свойстве
readonly public function __construct(
    public readonly string $email,
    public readonly int    $age,
) {}

// Попытка $user->email = 'x' → FATAL Error, а не тихое изменение состояния.

// 8.2: весь объект — значение, клонируется дёшево
final readonly class Money {
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}

    public function add(self $other): self {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

$a = new Money(100, 'RUB');
$b = $a->add(new Money(50, 'RUB'));  // $a не изменился, $b — новый
```

Меньше «кто и когда переписал `$order->total`» в мультитредовых/мьютексных сценариях.

---

### 3. Union types + DNF-типы (8.0 / 8.2) — честный контракт

```php
// 8.0: union
function log(string|int $id): void { /* ... */ }

// 8.2: DNF — «либо A, либо (B и C вместе)»
interface Payload { ... }
interface HasTimestamp { public function ts(): int; }

function dispatch(Payload&HasTimestamp|scalar $payload): void { /* ... */ }
//  → либо скаляр, либо объект, у которого ЕСТЬ и Payload, и HasTimestamp.
```

Раньше вы либо переписывали интерфейс «на всякий случай», либо жили с `mixed` и runtime-проверками. Теперь контракт точный, а статический анализатор проверяет **оба** случая.

---

### 4. Constructor property promotion + `never` (8.0 / 8.1) — меньше кода, больше честности

```php
// Было:
class EmailValidator {
    private RuleSet $rules;
    private Logger  $logger;

    public function __construct(RuleSet $rules, Logger $logger) {
        $this->rules  = $rules;
        $this->logger = $logger;
    }
}

// Стало:
final class EmailValidator {
    public function __construct(
        private readonly RuleSet $rules,
        private readonly Logger  $logger,
    ) {}

    public function validate(string $email): never|string {
        // never → «я либо верну string, либо брошу исключение / выйду»
        if (!preg_match($this->rules->pattern(), $email)) {
            throw new InvalidEmailException($email);
        }
        return $email;
    }
}
```

`never` говорит читателю и анализатору: «после этого вызова потока исполнения нет». Убирает мёртвый код после `throw` и делает `assert`/`exit`-пути явными.

---

### 5. Nullsafe operator + встроенные str-* (8.0) — меньше `if (x !== null && x->y !== null)`

```php
// Было:
$city = null;
if ($user !== null) {
    if ($user->address !== null) {
        if ($user->address->geo !== null) {
            $city = $user->address->geo->city;
        }
    }
}

// Стало:
$city = $user?->address?->geo?->city ?? 'unknown';

// И вместо strpos(...) !== false:
if (str_contains($email, '@') && str_starts_with($name, 'Dr')) { /* ... */ }
```

Меньше вложенности → меньше мест, где «забыл null-check на ветке B».

---

### 6. Match-выражение (8.0) — нет fall-through, нет забытого `break`, возвращает значение

```php
// switch: два места, где можно ошибиться (забыть break, не вернуть)
// match: одно выражение, анализатор проверяет исчерпание (при enum-аргументе)

$fee = match (true) {
    $order->total >= 10_000 => 0,
    $order->total >= 5_000  => 99,
    default                  => 299,
};
```

---

### Что я сознательно **не** считаю «самым важным»

- **Fibers** — мощно, но это async-инфраструктура, а не гигиена кода.
- **`str_contains` / `str_starts_with`** — удобно, но не меняет архитектуру.
- **First-class callable syntax** (`strlen(...)` вместо `fn($x) => strlen($x)`) — синтаксический сахар, а не надёжность.

---

### Практический вывод

Если бы я писал новый проект с нуля на PHP 8.2/8.3, я бы:

1. **Enums** вместо `class Constants` / `final class Foo { const BAR = ... }`.
2. **`readonly`** на всех DTO/Value Object'ах по умолчанию.
3. **PHPStan level 9** + **Psalm** — union/never/enum дают анализатору материал, на уровне 4–5 вы не ловите половину.
4. **Strict types** (`declare(strict_types=1);`) в каждом файле — с union-типами это реально работает.
5. **`match`** везде, где есть «ветвление по конечному набору значений».

Всё остальное — вопрос стиля и taste.

Хотите, разберём одну из этих фич на более «грязном» реальном сценарии — например, enum + readonly + match внутри Symfony-сервиса с Doctrine?

> ⏱ 19.3s · prompt 1118 tok · output 2482 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

The user is asking about PHP's type system in depth: strict_types, type coercion behavior, typical pitfalls, and examples where typing saves vs. surprises. This is a technical deep-dive question that deserves a thorough, well-structured answer with code examples.

Let me organize my thoughts:

1. **strict_types** - what it actually does, common misconceptions
2. **Type coercion** - how PHP coerces types, the "coercive mode" vs "strict mode"
3. **Typical pitfalls** - the classic ones and some subtle ones
4. **Where typing saves** - concrete examples
5. **Where it surprises** - the "gotcha" examples

Key points to cover:

**strict_types:**
- Per-file, not per-function
- Only affects calls FROM that file TO functions declared in OTHER files
- Does NOT affect calls within the same file
- Does NOT affect function declarations/parameter types (those are always checked)
- `declare(strict_types=1);` must be the first statement
- The `mixed` type vs `object` vs no type
- `int` vs `float` — in strict mode, 1 is NOT accepted where float is expected (wait, actually it IS — int is a subtype of float in PHP's type system)
- Actually let me be careful: in strict_types, passing an int to a float parameter IS allowed (int is a subtype of float). But passing a string "1" to an int is NOT.

**Coercion (weak mode, default):**
- "1" → 1 (string to int)
- 1.0 → 1 (float to int, but this actually throws in some cases)
- Wait, actually: in coercive mode, 1.5 passed to int parameter → 1 (truncation with deprecation notice in PHP 8.1+)
- "abc" → 0 (string to int that's not numeric)
- null → "" (null to string)
- true → 1, false → 0
- arrays: [1,2,3] → 1 (truthy, first element? No, → 1 because non-empty array is truthy)
- Wait, actually in PHP: (int)[1,2,3] = 1, (int)[] = 0
- (string)[1,2,3] = "Array" (in PHP 8, this throws a TypeError? No, it gives "Array" string... actually in PHP 8, casting an array to string gives "Array" with a warning? Let me think... In PHP 7 it was "Array". In PHP 8, I believe it's still "Array" but might throw. Actually no, (string)[] is "Array" in PHP 8 too, but I should be careful here.)

Actually let me reconsider. In PHP 8:
- (string)["a","b"] → "Array" (this is still the case)
- (int)"123abc" → 123 (with deprecation notice in PHP 8.1 for non-numeric leading... actually "123abc" → 123 is allowed, but "abc" → 0 with deprecation)

**Pitfalls:**
1. strict_types is per-file — the most common confusion
2. `int` vs `float` — 1.0 passed to int parameter
3. String to int coercion: "123" → 123, "123abc" → 123 (deprecated in 8.1), "abc" → 0 (deprecated in 8.1)
4. null to string → ""
5. Array to int → 1 (non-empty) or 0 (empty)
6. `is_int(1.0)` → false, but in strict mode, 1.0 IS accepted as int? No wait — in strict_types, 1.0 passed to int parameter → TypeError! Because float is NOT a subtype of int (int IS a subtype of float).
7. `is_int("1")` → false, in coercive mode "1" is accepted as int
8. The `mixed` type in PHP 8.0 — it's not "no type", it's "everything including null"
9. `object` type — accepts any object but not null, not arrays, not scalars
10. Comparison: == vs ===, and how "0" == false, "abc" == false (PHP 8 changed this!)
11. In PHP 8.0: "abc" == 0 is now false (was true in PHP 7)
12. `str_contains` vs `strpos !== false`

**Where typing saves:**
- enum instead of string constants
- readonly preventing accidental mutation
- union types making contracts explicit
- never return type

**Where it surprises:**
- strict_types per-file behavior
- int → float is allowed, but float → int is not (in strict mode)
- The fact that "123" works in coercive mode but "123abc" triggers deprecation
- `is_int(1.0)` is false but 1.0 can be passed to a float parameter
- The `mixed` type being "everything" not "no type"

Let me structure this well with code examples. I'll aim for a comprehensive but readable answer.

</details>

**Кандидат:**

# Система типов PHP: как она реально работает

## 1. `strict_types`: что он делает (и чего не делает)

### Главное правило, которое путает 80% людей

`strict_types` — это **свойство файла-вызывающего**, а не файла-объявляющего.

```php
// === FileA.php ===
declare(strict_types=1);

function takeInt(int $x): int { return $x; }
function takeFloat(float $x): float { return $x; }

// === FileB.php ===
declare(strict_types=1);

takeInt("42");    // ✅ TypeError: строка не приведётся к int
takeFloat(42);    // ✅ int → float разрешено (int — подтип float)
takeFloat(42.7);  // ✅

// === FileC.php (без strict_types!) ===
// (файл начинается с <?php, нет declare)

takeInt("42");    // ✅ МЯГКОЕ приведение: "42" → 42, работает
takeFloat("42");  // ✅ "42" → 42.0
```

### Чего `strict_types` **не** делает

```php
declare(strict_types=1);

function foo(int $x) { return $x; }

// Внутри ТОГО ЖЕ файла:
foo("42");  // ❌ TypeError!

// Почему? Потому что strict_types проверяет ТОЛЬКО внешние вызовы.
// При вызове из того же файла PHP всегда работает в strict-режиме
// для локальных функций. На практике это означает:
//   - strict_types защищает межфайловые границы,
//   - но НЕ защищает код внутри одного файла (тут всегда strict).

// Итог: если вы написали strict_types=1, то ВСЕ вызовы из этого файла
// в ЛЮБЫЕ другие файлы — strict. А вот вызовы из файла БЕЗ strict
// в ваш файл — coercive.
```

### `int` → `float` да, `float` → `int` нет

```php
declare(strict_types=1);

function a(int $x) {}
function b(float $x) {}

a(1);       // ✅
a(1.0);     // ❌ TypeError: float не приводится к int в strict-режиме
b(1);       // ✅ int — подтип float (уменьшение точности не требуется)
b(1.0);     // ✅

// Памятка: int ⊂ float (в смысле подтипов), а не наоборот.
// Это единственное «асимметричное» приведение в скалярах PHP.
```

---

## 2. Coercive mode: как PHP «приводит»

Когда `strict_types=1` **отсутствует** в вызывающем файле (или файл вызывает сам себя), включается coercive mode. Правила:

| Исходное | Целевой | Результат | Примечание |
|---|---|---|---|
| `"42"` | `int` | `42` | численная строка → int |
| `"42.7"` | `int` | `42` (⚠️ deprecation в 8.1+) | усечение дробной части |
| `"abc"` | `int` | `0` (⚠️ deprecation в 8.1+) | нечисловая → 0 |
| `"123abc"` | `int` | `123` (⚠️ deprecation) | парсит числовой префикс |
| `null` | `string` | `""` | null → пустая строка |
| `null` | `int` | `0` | |
| `true` | `int` | `1` | |
| `false` | `string` | `""` | |
| `[1,2,3]` | `int` | `1` | непустой массив → 1 |
| `[]` | `int` | `0` | пустой массив → 0 |
| `[1,2,3]` | `string` | `"Array"` | |
| `1.0` | `int` | `1` (⚠️ deprecation в 8.1+) | float → int |

```php
// Файл без strict_types:
function process(string $s): void { var_dump($s); }
function countThings(int $n): void { var_dump($n); }

process(null);       // string(0) ""
countThings(true);   // int(1)
countThings([1,2]);  // int(1)  ← «маскарадный» массив как число
countThings("42x");  // int(42) + Deprecated: "42x" → 42
```

Это не «баг» — это дизайн для обратной совместимости. Но на практике именно эти правила рождают классические баги.

---

## 3. Типичные ловушки

### Ловушка 1: `strict_types` не глобальный

```php
// config.php (БЕЗ strict_types)
require 'services.php';

// services.php (С strict_types=1)
declare(strict_types=1);
function getOrderId(int $id): void { /* ... */ }

// Вызов из config.php:
getOrderId("42");  // ✅ Работает, потому что ВЫЗЫВАЮЩИЙ файл не strict.
                   // Но если завтра добавим strict в config.php — БАГ В ПРОДЕ.
```

**Правило:** ставьте `declare(strict_types=1);` в **каждом** файле. Без исключений.

### Ловушка 2: `mixed` ≠ «без типа»

```php
function a(mixed $x) {}   // Принимает ЛЮБОЕ значение, включая null
function b(object $x) {}  // Только объекты. null → TypeError. [] → TypeError.

// mixed — это «всё, включая null».
// «Без типа» в PHP — это тоже mixed, но без явного объявления.
// Разница: mixed даёт анализатору (PHPStan/Psalm) сигнал
// «здесь может быть что угодно», и он перестаёт проверять.
```

### Ловушка 3: Сравнение `==` vs `===` (и что изменилось в PHP 8)

```php
// PHP 7 (устаревшее поведение):
"0" == false;        // true  (!!)
"abc" == 0;          // true  (!!) — строка приводится к 0

// PHP 8.0:
"0" == false;        // true  (по-прежнему)
"abc" == 0;          // FALSE (исправлено!)
"1" == 1;            // true
"1.0" == 1;          // true
"0x1A" == 26;        // true (hex-парсинг)
"100" == 100;        // true

// Вывод: == по-прежнему небезопасен для строк.
// === исключает проблему, но не работает с 1 vs 1.0:
1 === 1.0;           // false (разные типы)
```

### Ловушка 4: `is_*` не совпадает с «принимается параметром»

```php
function f(int $x) {}

is_int("42");   // false
f("42");        // ✅ работает в coercive-режиме!

// Вывод: is_int() говорит «это ли это int-типа в рантайме»,
// а параметр говорит «допустимо ли приведение».
// Разное. Не путайте.
```

### Ловушка 5: `float` → `int` с потерей

```php
declare(strict_types=1);
function n(int $x) { return $x; }

n(1.0);  // ❌ TypeError (float не подтип int)

// Без strict_types:
n(1.0);  // ✅ → 1 (тихое усечение!)
n(1.9);  // ✅ → 1 (потеря дробной части, nobody не знает)
// В PHP 8.1: + Deprecated: Implicit conversion from float 1.9 to int
```

---

## 4. Где типизация **спасает**

### А. Enum + `match` закрывает «все значения учтены»

```php
enum PaymentMethod: string {
    case Card     = 'card';
    case PayPal   = 'paypal';
    case Crypto   = 'crypto';
}

function feeFor(PaymentMethod $m): float {
    return match ($m) {
        PaymentMethod::Card   => 2.9,
        PaymentMethod::PayPal => 3.5,
        PaymentMethod::Crypto => 1.0,
        // Если добавить case → PHPStan/Psalm: "exhaustiveness check failed"
    };
}

// feeFor("card")      → TypeError. Не string.
// feeFor("CARd")      → TypeError. Не enum-член.
// feeFor(PaymentMethod::Card) → 2.9
```

### Б. `readonly` + конструктор-промоушен убивает «кто-то изменил состояние»

```php
final readonly class Transfer {
    public function __construct(
        public readonly string  $from,
        public readonly string  $to,
        public readonly int     $amountCents,  // int, а не float!
        public readonly \DateTimeImmutable $at,
    ) {}
}

$t = new Transfer('A', 'B', 10_000, new \DateTimeImmutable());
$t->amountCents = 99;  // ❌ Fatal: readonly property cannot be changed
```

Никакой «случайной» перезаписи `$order->total` в середине 400-строчного метода.

### В. `never` + `void` разграничивают «выживаю» и «не выживаю»

```php
function requireAdmin(?User $user): User {
    if ($user?->role !== 'admin') {
        throw ForbiddenException::class;  // never
    }
    return $user;  // выживающий путь: гарантированно User, не null
}

// Анализатор после вызова:
$admin = requireAdmin($currentUser);
$admin->id;  // ✅ знает: int, не null, не mixed
```

### Г. Union types фиксируют «что реально может прийти»

```php
// Было: mixed + if/else + docblock
function serialize(mixed $data): string {
    if ($data instanceof Money) { /* ... */ }
    elseif (is_array($data)) { /* ... */ }
    elseif (is_string($data)) { /* ... */ }
    // ... а если null? а если bool?
}

// Стало:
function serialize(Money|array|string|int|null $data): string {
    match (true) {
        $data instanceof Money => /* ... */,
        is_array($data)       => /* ... */,
        default               => (string) $data,  // string|int|null
    };
}
// Всё, что не в union — TypeError на входе.
```

---

## 5. Где типизация **удивляет**

### `float` — не число в «бытовом» смысле

```php
declare(strict_types=1);

function ratio(float $x): float { return 1.0 / $x; }

ratio(1);     // ✅ 1.0 (int → float, нормально)
ratio(0);     // ⚠️ DivisionByZeroError, а не TypeError!
              //    Тип-система не знает, что 0 — «плохое» float.

// Вывод: тип-система проверяет тип, а не смысл.
// Валидация домена — ваша зона ответственности.
```

### `array` — это «что угодно → что угодно»

```php
function ids(array $list): array {
    return array_map(fn($x) => $x->id, $list);
    //              ^^^^^^
    // $x: mixed. Тип-система НЕ знает, что внутри array.
    // array — это map<mixed, mixed> по умолчанию.
}

// PHP 8.3: array<PaymentMethod, int> — уже лучше, но:
//   - не проверяется в рантайме (только анализатором)
//   - не наследуется: array<int, int> → array<int, mixed> при прокидке
```

Это главный «незакрытый» кусок типовой системы PHP: **внутренности массивов** остаются unchecked без статического анализатора.

### `object` — не «любой объект»

```php
declare(strict_types=1);
function handle(object $o) { /* ... */ }

handle(new stdClass);  // ✅
handle(null);          // ❌ TypeError (null не объект)
handle([]);            // ❌ TypeError (массив не объект)
handle("str");         // ❌ TypeError
```

### Приведение при передаче в `var_dump` / `echo`

```php
// Это НЕ типизировано — это «магия рантайма»
echo [1, 2, 3];       // Warning: Array to string conversion → "Array"
echo null;            // ""
echo true;            // "1"
echo 1.0;             // "1" (локали) или "1.0"? → "1" в en-US, "1,0" в de-DE

// Эти приведения НЕ контролируются strict_types.
// strict_types — только для параметров/return.
```

---

## 6. Практический чеклист

| Правило | Зачем |
|---|---|
| `declare(strict_types=1);` в **каждом** файле | Убивает межфайловые «мягкие» приведения |
| `int` для денег (центы), **никогда** `float` | 0.1 + 0.2 ≠ 0.3 |
| `readonly` на всех DTO / Value Object | Иммутабельность → нет мутационных багов |
| `enum` для любого конечного набора значений | Опечатка → compile-time (анализатор) |
| Union types вместо `mixed` + `if` | Контракт виден в сигнатуре |
| `?Type` для nullable, не `Type\|null` без причины | Читаемость; `mixed` ≠ `null` |
| PHPStan **level 9** + `reportUnmatchedIgnoredErrors` | Тип-система PHP покрывает ~70% ошибок; анализатор — остальные 30% |
| `is_int()` ≠ «принимается параметром int» | Не путайте рантайм-проверку и контракта |

---

Если хотите, могу показать, как эти правила выглядят в реальном Symfony-сервисе с Doctrine: enum → Doctrine DBAL, readonly entity-DTO, `never` в repository, union types в event handler. Или разобрать конкретный баг из вашего кода — присылайте.

> ⏱ 36.1s · prompt 2981 tok · output 4670 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user is asking about exception handling design in PHP applications. They want to know:
1. How I design exception hierarchies
2. Wrapping third-party exceptions
3. What constitutes a method's contract
4. Anti-patterns

This is a deep architectural question. Let me think about what makes a genuinely good answer here, not just a generic listicle.

Key principles I want to convey:
- Exceptions are a contract, not just an error mechanism
- Domain exceptions vs infrastructure exceptions
- The "translate at the boundary" principle
- What a method signature implies about what it can throw
- PHP-specific quirks (Throwable, \Exception vs \Error, \Error from engine)
- Anti-patterns: catching \Exception broadly, using exceptions for control flow, rethrowing without context, "God exception" with 15 properties, swallowing silently

Let me structure this clearly with code examples. I'll aim for practical, opinionated advice grounded in real PHP/Symfony experience.

Structure:
1. Philosophy: exception as contract
2. Hierarchy design (domain / infrastructure / app)
3. Wrapping at boundaries (Doctrine, DB, HTTP clients, etc.)
4. Method contract: what goes in docblock, what's implicit
5. Anti-patterns with code
6. Practical checklist

Let me write this in Russian as the conversation is in Russian, with code in PHP/English.

</details>

**Кандидат:**

# Исключения как контракт: проектирование в PHP

## 1. Базовый принцип: исключение — это часть сигнatures

В Java вы видите `throws IOException` в сигнатуре. В PHP нет checked exceptions, но **контракт всё равно существует** — он просто живёт в docblock, в иерархии и в ментальной модели команды.

Вопрос, который я задаю при проектировании любого метода:

> **Какие исключения может бросить этот метод, и что значит каждое из них для вызывающего?**

Если ответ «ну, что-то пойдёт не так» — контракт не существует.

---

## 2. Иерархия: три слоя, три ответственности

```
Exception (PHP)
├── DomainException              ← бизнес-правила нарушены
│   ├── InvalidEmailException
│   ├── InsufficientBalanceException
│   ├── OrderAlreadyShippedException
│   └── CustomerNotFoundForOrderIdException
│
├── InfrastructureException      ← внешний мир ответил плохо
│   ├── DatabaseException
│   │   ├── UniqueConstraintViolationException
│   │   └── DeadlockDetectedException
│   ├── ExternalApiException
│   │   ├── PaymentGatewayTimeoutException
│   │   └── PaymentGatewayDeclinedException
│   └── MailTransportException
│
└── ApplicationException         ← ошибка приложения, не домена, не инфраструктуры
    ├── ConfigurationException
    ├── FeatureFlagNotSetException
    └── InternalInvariantBrokenException
```

### Почему три слоя, а не одно дерево

```php
// Домен НЕ знает, что у нас PostgreSQL.
// Домен НЕ знает, что платёж идёт через Stripe.
// Домен знает только: «баланс не хватает».

final class InsufficientBalanceException extends DomainException {
    public function __construct(
        public readonly AccountId $accountId,
        public readonly int       $requiredCents,
        public readonly int       $availableCents,
    ) {
        parent::__construct(sprintf(
            'Account %s: required %d, available %d',
            $accountId->value, $requiredCents, $availableCents
        ));
    }
}

// Инфраструктура НЕ знает, что «уникальный ключ» — это «дубль заказа».
// Она знает: «DB вернула 23505».

final class UniqueConstraintViolationException extends DatabaseException {
    public function __construct(
        string $table,
        string $constraintName,
        private readonly \Throwable $previous,
    ) {
        parent::__construct(
            sprintf('Unique constraint %s violated on table %s', $constraintName, $table),
            $previous
        );
    }
}
```

### Ключевое правило

> **Домен бросает доменные исключения. Инфраструктура бросает инфраструктурные. Слой приложения (service, controller) — единственный, кто переводит одно в другое.**

```php
// Service — «переводчик»
final class TransferService {
    public function __construct(
        private AccountRepository $accounts,
        private Ledger            $ledger,
    ) {}

    public function transfer(AccountId $from, AccountId $to, Money $amount): void {
        $source = $this->accounts->find($from);  // может бросить AccountNotFoundException (домен)

        if (!$source->canTransfer($amount)) {
            throw new InsufficientBalanceException($from, $amount->cents, $source->balance->cents);
        }

        $this->ledger->record(/* ... */);
        // Ledger → Doctrine → PostgreSQL
        // Если unique constraint violation:
        //   Doctrine бросает UniqueConstraintViolationException (инфра)
        //   Ledger НЕ перехватывает его — пробрасывает
        //   TransferService перехватывает и решает: retry? доменное исключение? 500?
    }
}
```

---

## 3. Оборачивание чужих исключений

### Правило: «переводи на границе, не внутри»

```php
// ❌ ПЛОХО: домен знает про Doctrine
class Balance {
    public function decrement(int $cents): void {
        $em = EntityManager::get();  // ← домен тянет инфраструктуру
        $em->flush();
        // если flush бросает QueryException — домен уже «загрязнён»
    }
}

// ✅ ХОРОШО: граница оборачивает
class Ledger {
    public function record(Entry $entry): void {
        try {
            $this->em->persist($entry);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            // Переводим: «дубль записи в ledger» → доменное исключение
            throw new DuplicateLedgerEntryException(
                accountId: $entry->accountId,
                txnId:     $entry->txnId,
                previous:  $e
            );
        } catch (ConnectionException $e) {
            // БД недоступна → инфраструктурное, НЕ доменное
            throw new DatabaseUnavailableException(previous: $e);
            // Вызывающий сам решает: retry? 503? алерт?
        }
    }
}
```

### Что делать с `catch`

```php
try {
    $this->httpClient->post('/charge', $payload);
} catch (ConnectionException $e) {
    // Сеть пропала. Конкретное, конкретное решение:
    throw new PaymentGatewayTimeoutException(previous: $e);
} catch (HttpException $e) when ($e->getResponseCode() === 429) {
    // Rate limit. Конкретное решение:
    throw new RateLimitedException(
        retryAfter: $e->getHeader('Retry-After'),
        previous:   $e
    );
} catch (HttpException $e) when ($e->getResponseCode() >= 500) {
    // Сервер гейтвея упал.
    throw new PaymentGatewayInternalError(previous: $e);
}
// НЕ «catch (\Exception $e)» — см. антипаттерны ниже
```

### `$previous` — обязательно

```php
// Всегда:
throw new MyException('context here', $previous);

// Никогда:
throw new MyException('context here');  // ← теряем stack trace чужого исключения
```

В логах и Sentry это разница между «PaymentGatewayTimeoutException» и «PaymentGatewayTimeoutException → ConnectionException → curl error 28: Operation timed out after 5001ms».

---

## 4. Контракт метода: что считывается из сигнatures

```php
/**
 * @throws AccountNotFoundException  если аккаунт не существует
 * @throws InsufficientBalanceException если баланс < amount
 * @throws DuplicateLedgerEntryException если запись уже есть (idempotency key)
 * @throws DatabaseUnavailableException если БД недоступна (инфраструктура)
 *
 * НЕ бросает: PaymentGateway* — это не ответственность этого метода.
 */
public function transfer(AccountId $from, AccountId $to, Money $amount): TransferReceipt: void
```

### Что **не** должно быть в контракте

- `\Throwable` — «что-то сломалось» не является контрактом.
- `\Exception` без уточнения — «может бросить что угодно».
- Исключение, которое вызывающий **никогда** не может обработать осмысленно.

### `never` vs `void` + throw

```php
// Контракт: «либо верну T, либо умру»
public function findRequired(AccountId $id): Account {
    return $this->find($id)
        ?? throw new AccountNotFoundException($id);
}

// Контракт: «верну T или null, бросать не буду»
public function find(AccountId $id): ?Account {
    return $this->em->getRepository(Account::class)->find($id->value);
}

// Вызывающий:
$account = $repo->findRequired($id);  // знает: дальше можно работать
$account = $repo->find($id);          // знает: надо проверять null
```

Это часть контракта. `never` в return type делает это явным для анализатора.

---

## 5. Антипаттерны (с кодом)

### ❌ 1. «God exception»

```php
class AppException extends \Exception {
    public function __construct(
        string  $message,
        int     $code,
        string  $userId,
        string  $orderId,
        string  $email,
        string  $ip,
        int     $retryCount,
        bool    $isRetryable,
        string  $traceId,
        ?string $correlationId,
        array   $context,
        // ... ещё 5 полей
    ) {}
}
```

Проблема: каждый вызывающий знает, **какие** из 15 полей заполнены, а какие — `null`. Контракт нечитаем.

✅ **Решение:** узкие исключения, каждое со своими 2–4 полями.

---

### ❌ 2. `catch (\Exception)` / `catch (\Throwable)` «на всякий случай»

```php
try {
    $this->service->process($order);
} catch (\Exception $e) {
    $this->logger->error('Something went wrong', ['e' => $e]);
    return Response::json(['error' => 'oops'], 500);
}
```

Проблема:
- Скроет `DomainException` (который должен стать 422)
- Скроет `OutOfMemory` / `ParseError` (`\Error`, не `\Exception`)
- Вызывающий не может отличить «дубль» от «БД упала»
- Вы потеряли возможность retry на инфраструктурных, а на доменных — нет

✅ **Решение:** ловите конкретные. Если реально нужно «всё» — это только в **самом верхнем** обработчике (Symfony `kernel.exception`, custom `ExceptionListener`), и там уже есть контекст.

---

### ❌ 3. Исключение как контроль потока

```php
function findById(string $id): ?User {
    try {
        return $this->em->find(User::class, $id);
    } catch (EntityNotFoundException $e) {
        return null;
    }
}
```

Проблема: вызывающий не может отличить «пользователя нет» от «БД упала и Doctrine бросил EntityNotFound» (редко, но бывает при кастомных Repository).

✅ **Решение:** если «не найдено» — это **нормальный исход** (50% вызовов), используйте `null` или `Option`-паттерн, а не исключение. Исключение — для **необычных** случаев.

```php
// «Не найдено» — норма → null
public function find(AccountId $id): ?Account { ... }

// «Найден, но в неправильном состоянии» — аномалия → исключение
public function findActive(AccountId $id): Account {
    $a = $this->find($id) ?? throw new AccountNotFoundException($id);
    if ($a->isFrozen) throw new AccountFrozenException($id);
    return $a;
}
```

---

### ❌ 4. Перехват и «забывание»

```php
try {
    $this->mail->send($email);
} catch (\Exception $e) {
    // «Логнуть не будем, это не критично»
}
// Email не ушёл. Никто не знает. Пользователь ждёт пароль 3 часа.
```

✅ **Решение:** если глотаете — **обоснуйте в комментарии**, логируйте, и убедитесь, что это действительно некритично. В 90% случаев «не критично» — это отговорка.

---

### ❌ 5. Перехват и «переупаковка» без контекста

```php
try {
    $this->gateway->charge($order);
} catch (GatewayException $e) {
    throw new \Exception('Payment failed');  // ← контекст мёртв
}
```

✅ **Решение:**

```php
catch (GatewayException $e) {
    throw new PaymentDeclinedException(
        orderId:  $order->id,
        reason:   $e->getDeclineReason(),  // «insufficient_funds", "expired_card"
        code:     $e->getGatewayCode(),
        previous: $e
    );
}
```

---

### ❌ 6. Домен знает про HTTP-статусы

```php
// В DomainException:
class InvalidEmailException extends DomainException {
    public const HTTP_STATUS = 422;  // ← домен знает про HTTP? Нет.
}
```

✅ **Решение:** маппинг доменное → HTTP живёт **только** в слое представления (Symfony `ExceptionListener` / `ExceptionToResponse`):

```php
// App/EventListener/DomainExceptionListener.php
#[AsEventListener]
final class DomainExceptionListener {
    public function onException(ExceptionEvent $event): void {
        $e = $event->getThrowable();

        if ($e instanceof InvalidEmailException) {
            $event->setResponse(JsonResponse::create([
                'error' => 'invalid_email',
                'detail' => $e->getMessage(),
            ], 422));
        }

        if ($e instanceof InsufficientBalanceException) {
            $event->setResponse(JsonResponse::create([
                'error'    => 'insufficient_balance',
                'required' => $e->requiredCents,
                'available'=> $e->availableCents,
            ], 422));
        }
        // InfrastructureException → 503 + Retry-After
        // ApplicationException → 500 + trace_id
    }
}
```

---

### ❌ 7. `finally` как способ «успеть что-то сделать»

```php
try {
    $this->em->persist($order);
    $this->em->flush();
} catch (\Exception $e) {
    throw new OrderSaveException($e);
} finally {
    $this->eventBus->dispatch(new OrderCreatedEvent($order));
    // ^^^ Order НЕ сохранился, но событие уже улетело.
    //     Слушатель начислил баллы. БД не знает об этом.
}
```

✅ **Решение:** `finally` — только для освобождения ресурсов (закрыть соединение, снять lock). События — **после** успешного `flush`, до выхода из `try`.

---

## 6. Практический чеклист

| Что | Как |
|---|---|
| Домен бросает | `DomainException` и наследники. 2–4 поля. Без HTTP-кодов. |
| Инфраструктура бросает | `InfrastructureException` + `$previous`. Без бизнес-логики. |
| Перевод | Только в сервисе / в `ExceptionListener`. |
| `$previous` | Всегда. Без исключений. |
| `catch` | Конкретный класс. `when` для подмножеств. Никогда `\Exception` в домене. |
| «Не найдено» | `?T` return, не исключение. |
| «Состояние неверно» | Исключение с полями контекста. |
| «Внешний мир упал» | Исключение + `retryable: true` + `retryAfter`. |
| Слой HTTP | `ExceptionListener` → JSON + статус. Единственное место, где живёт `422`/`500`. |
| `finally` | Только ресурсы. Не бизнес-логика. |
| Логи | `error` + `previous` stack trace + `trace_id`. Не `print_r`. |

---

## 7. Один конкретный пример «сквозного» пути

```
POST /transfers
  → Controller (не ловит ничего)
    → TransferService::transfer()
      → AccountRepository::find() → ?Account → throw AccountNotFound (домен)
      → Balance::canTransfer() → throw InsufficientBalance (домен)
      → Ledger::record()
          → Doctrine::flush()
              → PostgreSQL: 23505 (unique violation)
              → Doctrine: UniqueConstraintViolationException
          → Ledger: catch → throw DuplicateLedgerEntryException (домен, $previous)
      → TransferService: НЕ перехватывает Duplicate — пробрасывает
    → Kernel: ExceptionEvent
      → DomainExceptionListener:
          AccountNotFound          → 404 { "error": "account_not_found" }
          InsufficientBalance      → 422 { "error": "insufficient_balance", "required": 5000, "available": 1200 }
          DuplicateLedgerEntry     → 409 { "error": "duplicate", "txn_id": "..." }
          DatabaseUnavailable      → 503 { "error": "unavailable", "retry_after": 5 }
          else                     → 500 { "error": "internal", "trace_id": "abc-123" }
```

Каждый слой знает **своё**. Ни один не знает чужое.

---

Хотите, покажу как это выглядит в реальном Symfony-проекте: `ExceptionListener`, доменные исключения, `TransferService`, и как это тестировать (mock'и vs. реальные интеграционные тесты с SQLite/Postgres)?

> ⏱ 33.3s · prompt 6540 tok · output 4373 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, какой принцип SOLID чаще всего нарушается в реальном коде, почему, и просит показать типовой пример нарушения и его рефакторинг.

Давайте подумаю, что реально чаще всего нарушается:

1. **SRP (Single Responsibility Principle)** — нарушается постоянно, но это часто "нормальное" нарушение. Один класс делает несколько вещей. Но это не всегда плохо — иногда "один класс на одну ответственность" приводит к 50 файлам для одной фичи.

2. **OCP (Open/Closed Principle)** — очень часто нарушается через if/else chains и switch по типу. "Добавил новый тип — пошёл и меняй 10 файлов". Это классический и очень наглядный пример.

3. **LSP (Liskov Substitution Principle)** — нарушается, когда наследуют "на всякий случай" и переопределяют методы с другими постусловиями. В PHP это часто с интерфейсами, которые "наследуют" и ломают контракт.

4. **ISP (Interface Segregation Principle)** — большие интерфейсы с 15 методами, из которых конкретная реализация использует 3. В PHP это очень распространено.

5. **DIP (Dependency Inversion Principle)** — нарушение через прямые `new` внутри сервисов, hard-coded зависимости.

Я бы сказал, что в реальном PHP-коде **чаще всего** нарушается **OCP** — и вот почему:

- PHP исторически — язык процедурного/наивного ООП
- Если/else по типу — самый естественный паттерн для "добавил новый вариант"
- Стратегия/полиморфизм требует больше кода на старте, и команда экономит
- В Symfony-проектах это особенно заметно: "добавил новый тип payment" → пошёл и менял 8 файлов
- Это не "глупое" нарушение — это прагматичное решение на 3 варианта, которое становится катастрофой на 15

Но также очень часто нарушается **SRP** в форме "God service" — один сервис на 500 строк, который и валидирует, и шлёт email, и пишет в БД, и считает аналитику.

Я думаю, лучший ответ — сказать, что **OCP** нарушается чаще всего и наиболее разрушительно, потому что:
1. Он "тихий" — код работает, никто не кричит
2. Он накапливается — 3 if/else не больно, 15 — уже больно
3. Он не виден при ревью — "да, работает же"
4. Он ломается именно тогда, когда добавляют новую фичу — а это каждый день

Но я также упомяну, что SRP нарушается в другой форме — "God class" — и это тоже очень часто.

Давайте я покажу OCP как основной ответ, с конкретным примером на PHP/Symfony, и дам рефакторинг.

Пример: обработка разных типов уведомлений (email, SMS, push) или разные типы платежей. Возьму платежи — это ближе к нашему контексту.

Нарушение OCP:
```php
final class PaymentProcessor {
    public function process(Payment $payment): PaymentResult {
        if ($payment->method === 'card') {
            // 80 строк логики для карточек
        } elseif ($payment->method === 'paypal') {
            // 50 строк для paypal
        } elseif ($payment->method === 'crypto') {
            // 40 строк для crypto
        } elseif ($payment->method === 'bank_transfer') {
            // 60 строк для bank transfer
        }
        // ... и ещё 5 методов, которые тоже имеют if/else по методу
    }
    
    public function refund(Payment $payment): void {
        if ($payment->method === 'card') { ... }
        elseif ($payment->method === 'paypal') { ... }
        // ... тот же if/else
    }
    
    public function getReceipt(Payment $payment): Receipt {
        if ($payment->method === 'card') { ... }
        // ... и так далее
    }
}
```

Рефакторинг через Strategy + Polymorphism:

```php
interface PaymentMethod {
    public function charge(Payment $payment): PaymentResult;
    public function refund(Payment $payment): void;
    public function getReceipt(Payment $payment): Receipt;
}

final class CardPayment implements PaymentMethod { ... }
final class PayPalPayment implements PaymentMethod { ... }

// PaymentProcessor теперь:
final class PaymentProcessor {
    public function process(Payment $payment): PaymentResult {
        return $payment->method->charge($payment);
    }
}
```

Но подожди — это немного упрощённо. Реальный код сложнее. Давай сделаю более реалистичный пример, который покажет:
1. Нарушение OCP через if/else chain
2. Почему это "тихое" нарушение (код работает, ревью пропускают)
3. Рефакторинг через Strategy
4. Но также покажу, где Strategy НЕ нужна (2-3 варианта — if/else нормален)

Это последнее важно — потому что слепое применение Strategy для 2 вариантов — это тоже антипаттерн (over-engineering).

Структурирую ответ:
1. Ответ: OCP, и почему именно он
2. Почему он "тихий" — накапливается незаметно
3. Типовой пример нарушения (реалистичный, не упрощённый)
4. Рефакторинг
5. Когда НЕ надо рефакторить (2-3 варианта)
6. Практический тест: как быстро увидеть нарушение OCP

Пишу на русском, как и предыдущие ответы.

</details>

**Кандидат:**

# OCP: тихий убийца

## Короткий ответ

**Open/Closed Principle** — нарушается чаще всего и разрушительно всех пяти. Не потому что люди «не знают про полиморфизм», а потому что OCP-нарушение **тихое**: код работает, тесты зелёные, ревью проходят. Боль начинается не в момент нарушения, а через 6–18 месяцев, когда вариантов становится 12, а не 3.

Почему именно он, а не, скажем, SRP:

| SRP | OCP |
|---|---|
| Нарушение видно сразу: «офигеть, 500 строк в одном классе» | Нарушение выглядит как «разумный if/else для 3 вариантов» |
| Команда кричит: «раздели» | Команда молчит: «ну, работает же» |
| Рефакторинг очевиден | Рефакторинг «на 3 варианта» кажется over-engineering → откладывают → на 12-м варианте уже поздно |

---

## Типовое нарушение: «процессор платежей»

Это то, что я вижу в 8 из 10 Symfony-проектов. Сервис живёт 2–3 года, и каждый раз, когда добавляют способ оплаты, **меняют 5–7 файлов**:

```php
final class PaymentProcessor
{
    public function __construct(
        private CardGateway    $cardGateway,
        private PayPalClient   $paypal,
        private CryptoExchange $crypto,
        private BankApi        $bank,
        private LoggerInterface $logger,
    ) {}

    // ─── 1. Заряд ────────────────────────────────────────────────
    public function charge(Payment $p): PaymentResult
    {
        if ($p->method === 'card') {
            $this->logger->info('card_charge', ['id' => $p->id]);
            $resp = $this->cardGateway->charge($p->cardToken, $p->amountCents);
            if ($resp->declined) {
                throw new PaymentDeclinedException('card', $resp->reason);
            }
            return new PaymentResult($p->id, 'captured', $resp->txnId);

        } elseif ($p->method === 'paypal') {
            $this->logger->info('paypal_charge', ['id' => $p->id]);
            $order = $this->paypal->createOrder($p->email, $p->amountCents);
            $approval = $this->paypal->getApprovalUrl($order->id);
            // ... 20 строк на approval-флоу ...
            $capture = $this->paypal->capture($order->id);
            if (!$capture->isApproved) {
                throw new PaymentDeclinedException('paypal', 'not_approved');
            }
            return new PaymentResult($p->id, 'captured', $capture->id);

        } elseif ($p->method === 'crypto') {
            $this->logger->info('crypto_charge', ['id' => $p->id]);
            $invoice = $this->crypto->createInvoice($p->amountCents, 'BTC');
            $payment = $this->crypto->pollPayment($invoice->id, maxWait: 3600);
            if ($payment->status !== 'confirmed') {
                throw new PaymentDeclinedException('crypto', $payment->status);
            }
            return new PaymentResult($p->id, 'confirmed', $payment->txHash);

        } elseif ($p->method === 'bank_transfer') {
            $this->logger->info('bank_charge', ['id' => $p->id]);
            $ref = $this->bank->createRef($p->payerIban, $p->amountCents);
            // bank transfer — асинхронный, ждём webhook
            return new PaymentResult($p->id, 'pending', $ref);

        } else {
            throw new \InvalidArgumentException("Unknown method: {$p->method}");
        }
    }

    // ─── 2. Возврат ─────────────────────────────────────────────
    public function refund(Payment $p, Money $amount): void
    {
        if ($p->method === 'card') {
            $this->cardGateway->refund($p->gatewayTxnId, $amount->cents);
        } elseif ($p->method === 'paypal') {
            $this->paypal->refund($p->gatewayTxnId, $amount->cents);
        } elseif ($p->method === 'crypto') {
            $this->crypto->refund($p->payerAddress, $amount->cents);
        } elseif ($p->method === 'bank_transfer') {
            $this->bank->createRefundRef($p->payerIban, $amount->cents);
        } else {
            throw new \InvalidArgumentException("Unknown method: {$p->method}");
        }
    }

    // ─── 3. Статус (polling) ────────────────────────────────────
    public function getStatus(Payment $p): PaymentStatus
    {
        if ($p->method === 'card') {
            return $this->cardGateway->getStatus($p->gatewayTxnId);
        } elseif ($p->method === 'paypal') {
            return $this->paypal->getStatus($p->gatewayTxnId);
        } elseif ($p->method === 'crypto') {
            return $this->crypto->getStatus($p->gatewayTxnId);
        } else {
            return PaymentStatus::Pending; // bank: ждём webhook
        }
    }

    // ─── 4. Чек ─────────────────────────────────────────────────
    public function buildReceipt(Payment $p): array
    {
        if ($p->method === 'card') {
            return [
                'method'    => 'card',
                'last4'     => $p->cardLast4,
                'currency'  => $p->currency,
                'amount'    => $p->amountCents,
                'fee'       => $p->amountCents * 0.029,
            ];
        } elseif ($p->method === 'paypal') {
            return [
                'method'   => 'paypal',
                'email'    => $p->email,
                'currency' => $p->currency,
                'amount'   => $p->amountCents,
                'fee'      => $p->amountCents * 0.035,
            ];
        } // ... ещё два блока
    }

    // ─── 5. Валидация ───────────────────────────────────────────
    public function validate(Payment $p): void
    {
        if ($p->method === 'card') {
            if (!$p->cardToken) throw new \InvalidArgumentException('cardToken required');
            if ($p->amountCents > 50_000_00) throw new \InvalidArgumentException('limit exceeded');
        } elseif ($p->method === 'crypto') {
            if ($p->payerAddress === null) throw new \InvalidArgumentException('address required');
            // crypto: min 1000 sats
            if ($p->amountCents < 10) throw new \InvalidArgumentException('dust');
        } // ...
    }
}
```

### Что здесь нарушено

- **OCP**: добавление 5-го метода (`wallet`) → правим **5** методов + конструктор + DI-конфигурацию. Класс открыт для модификации при каждом расширении.
- **SRP** (вторично): один класс знает детали 4+ внешних API.
- **Дублирование структуры**: `charge`, `refund`, `getStatus`, `buildReceipt` — у всех один и тот же скелет `if/elseif` по методу.

### Почему это «тихое»

- 3 варианта: `if/elseif` — нормален. Никто не жалуется.
- 4-й вариант: «ну ладно, ещё один elseif».
- 5-й: «ну, добавим ещё блок».
- 8-й: код работает, тесты зелёные. Но каждый новый метод — **регресс по всему классу**. И никто не пишет тесты на «что произойдёт, если я добавлю 9-й метод и забуду в `buildReceipt`». Забудет. Потому что в 5-м методе из 5 он не видит, что в 3-м он должен был что-то сделать.

---

## Рефакторинг: Strategy + составление

### Шаг 1. Интерфейс метода

```php
interface PaymentMethodStrategy
{
    public function charge(Payment $p): PaymentResult;
    public function refund(Payment $p, Money $amount): void;
    public function getStatus(Payment $p): PaymentStatus;
    public function buildReceipt(Payment $p): array;
    public function validate(Payment $p): void;
}
```

### Шаг 2. Конкретные стратегии — каждая знает **только свой** API

```php
final class CardPaymentStrategy implements PaymentMethodStrategy
{
    public function __construct(private CardGateway $gw) {}

    public function charge(Payment $p): PaymentResult
    {
        $resp = $this->gw->charge($p->cardToken, $p->amountCents);
        if ($resp->declined) {
            throw new PaymentDeclinedException('card', $resp->reason);
        }
        return new PaymentResult($p->id, 'captured', $resp->txnId);
    }

    public function refund(Payment $p, Money $amount): void
    {
        $this->gw->refund($p->gatewayTxnId, $amount->cents);
    }

    public function getStatus(Payment $p): PaymentStatus
    {
        return $this->gw->getStatus($p->gatewayTxnId);
    }

    public function buildReceipt(Payment $p): array
    {
        return [
            'method'   => 'card',
            'last4'    => $p->cardLast4,
            'currency' => $p->currency,
            'amount'   => $p->amountCents,
            'fee'      => (int) round($p->amountCents * 0.029),
        ];
    }

    public function validate(Payment $p): void
    {
        if (!$p->cardToken) throw new \InvalidArgumentException('cardToken required');
        if ($p->amountCents > 50_000_00) throw new \InvalidArgumentException('limit exceeded');
    }
}

final class CryptoPaymentStrategy implements PaymentMethodStrategy
{
    public function __construct(private CryptoExchange $ex) {}
    // ... 5 методов, каждый по 10–20 строк. Ни слова о PayPal.
}
```

### Шаг 3. Резолвер: строка → strategy

```php
final class PaymentMethodResolver
{
    /** @var array<string, PaymentMethodStrategy> */
    private array $strategies;

    /**
     * @param iterable<array{method: string, strategy: PaymentMethodStrategy}> $items
     */
    public function __construct(iterable $items)
    {
        foreach ($items as $item) {
            $this->strategies[$item['method']] = $item['strategy'];
        }
    }

    public function resolve(string $method): PaymentMethodStrategy
    {
        return $this->strategies[$method]
            ?? throw new UnknownPaymentMethodException($method);
    }
}
```

В Symfony это делается через `tagged_iterator`:

```yaml
# services.yaml
App\Payment\PaymentMethodResolver:
    arguments:
        $strategies:
            - { method: card,          strategy: '@App\Payment\CardPaymentStrategy' }
            - { method: paypal,        strategy: '@App\Payment\PayPalStrategy' }
            - { method: crypto,        strategy: '@App\Payment\CryptoStrategy' }
            - { method: bank_transfer, strategy: '@App\Payment\BankTransferStrategy' }
```

Добавление нового метода: **новый файл + одна строка в YAML**. Ни один существующий файл не трогается.

### Шаг 4. `PaymentProcessor` становится тонкой оболочкой

```php
final class PaymentProcessor
{
    public function __construct(
        private PaymentMethodResolver $resolver,
        private LoggerInterface       $logger,
    ) {}

    public function charge(Payment $p): PaymentResult
    {
        $strategy = $this->resolver->resolve($p->method);
        $strategy->validate($p);
        $this->logger->info('charge', ['method' => $p->method, 'id' => $p->id]);
        return $strategy->charge($p);
    }

    public function refund(Payment $p, Money $amount): void
    {
        $this->resolver->resolve($p->method)->refund($p, $amount);
    }

    public function getStatus(Payment $p): PaymentStatus
    {
        return $this->resolver->resolve($p->method)->getStatus($p);
    }

    public function buildReceipt(Payment $p): array
    {
        return $this->resolver->resolve($p->method)->buildReceipt($p);
    }
}
```

Каждый метод — **3 строки**. Ни одного `if`. Ни одного `elseif`. Кросс-методная логика (logging, validation-order) живёт в одном месте.

---

## Когда Strategy **не** нужна

Это важно сказать, потому что слепое применение паттерна для 2 вариантов — тоже антипаттерн.

```php
// 2 варианта. if/elseif — нормален. Strategy — избыточен.
final class Discount {
    public function calculate(Price $p, CustomerType $type): int {
        return $type === CustomerType::VIP
            ? $p->cents * 30 / 100
            : $p->cents * 10 / 100;
    }
}
```

**Правило практического порога:**

| Кол-во вариантов | Что делать |
|---|---|
| 1–2 | `if/else`. Точка. |
| 3 | `if/elseif` ещё ок. Наблюдайте. |
| 4+ и вы **уже** видите дублирование структуры | Strategy. |
| 5+ и вы правите 3+ файла при добавлении варианта | Strategy **обязателен**. |

Порог «3» — не догма. Это момент, когда `if/elseif` начинает **дублировать структуру** (charge/refund/getStatus — один и тот же скелет). Если 3 варианта не дублируют структуру (у каждого своя уникальная логика без общих шагов) — можно подождать.

---

## Как быстро увидеть нарушение OCP в чужом коде

Четыре признака, по которым я определяю «вот это — OCP-долг»:

1. **Один `if/elseif` повторяется в 3+ методах того же класса.** → Структура дублируется, полиморфизм не применён.

2. **Добавление нового варианта требует правки 3+ файлов.** → Класс открыт для модификации.

3. **`else` / `default` бросает `\InvalidArgumentException` или `assert(false)`.** → Вы не знаете, что будет, если кто-то передаст новый variant. Контракт «закрыт» формально, а фактически — нет.

4. **В `Payment` / `Order` / `Notification` есть поле `method: string` и нет поля `method: PaymentMethodStrategy`.** → Домен хранит строку, а не объект с поведением. Это классический «data-driven if» вместо «behavior-driven dispatch».

---

## Итого

OCP нарушается не потому что люди глупы. А потому что **if/elseif — самый дешёвый способ выразить «разные варианты»** на 3 варианта. И на 3 варианта это правильно. Ошибка — в том, чтобы не остановиться и не спросить: «а что будет, когда станет 8?» Ответ: вы будете править 8 файлов при каждом изменении, и каждый правящий будет молиться, чтобы не забыть 4-й `elseif` в `buildReceipt`.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

# Можно ли нарушать SOLID?

Да. И иногда **нельзя** не нарушать.

SOLID — это не первая физика, а набор эвристик, выдвинутых в 2000 году на основе опыта с C++/Java. Каждая из пяти букв — **trade-off**, а не абсолют. Цена соблюдения иногда превышает цену нарушения. Вопрос не «нарушать или нет», а «что я теряю и что выигрываю».

---

## Главный пример: DIP, одного реализатора достаточно

### «SOLID-правильный» вариант

Типичный Symfony-проект, 2019 год, ментор на ревью сказал: «где интерфейс?»

```php
// src/Infrastructure/Mailer/EmailSenderInterface.php
interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $body): void;
}

// src/Infrastructure/Mailer/SmtpEmailSender.php
final class SmtpEmailSender implements EmailSenderInterface
{
    public function __construct(
        private string $host,
        private int    $port,
        private string $user,
        private string $pass,
    ) {}

    public function send(string $to, string $subject, string $body): void
    {
        // 40 строк SMTP-логики
    }
}

// src/Domain/Order/OrderService.php
final class OrderService
{
    public function __construct(
        private EmailSenderInterface $mailer,  // ← зависимость от абстракции
        private OrderRepository      $repo,
    ) {}

    public function confirm(string $orderId, string $customerEmail): void
    {
        $order = $this->repo->find($orderId);
        // ...
        $this->mailer->send(
            $customerEmail,
            'Заказ подтверждён',
            "Ваш заказ {$order->id} оплачен."
        );
    }
}
```

Три файла. Две «реализации» — одна из них не существует. Интерфейс не даёт:

- полиморфизма (один реализатор),
- тестируемости (в PHPUnit вы и так можете mock'нуть конкретный класс),
- расширения (у вас **один** способ доставки писем, и он не меняется).

Он даёт **один** эффект: при чтении `OrderService` вы видите `EmailSenderInterface` и не знаете, что происходит. Пришлось открыть второй файл.

### Практичный вариант

```php
// src/Infrastructure/Mailer/SmtpEmailSender.php
final class SmtpEmailSender
{
    public function __construct(
        private string $host,
        private int    $port,
        private string $user,
        private string $pass,
    ) {}

    public function send(string $to, string $subject, string $body): void
    {
        // 40 строк SMTP-логики
    }
}

// src/Domain/Order/OrderService.php
final class OrderService
{
    public function __construct(
        private SmtpEmailSender   $mailer,   // ← конкретный класс
        private OrderRepository   $repo,
    ) {}

    public function confirm(string $orderId, string $customerEmail): void
    {
        $order = $this->repo->find($orderId);
        $this->mailer->send($customerEmail, 'Заказ подтверждён', "Заказ {$order->id}");
    }
}
```

Два файла. Читатель `OrderService` сразу видит: **SMTP**, конкретный хост/порт в DI-конфиге. Нет лишний скачок.

### Почему это оправданно

| Аргумент «за интерфейс» | Реальность |
|---|---|
| «Можно заменить на Mailgun» | Если замените — изменится не только `send()`, а вся конфигурация (API-key вместо SMTP-credentials), и вы **всё равно** будете править `OrderService` (другие параметры). Интерфейс не спасает. |
| «Можно mock'нуть в тестах» | В PHPUnit вы и так пишете `new SmtpEmailSender('localhost', 2525, '', '')` — или делаете тестовый double конкретного класса. Интерфейс не упрощает. |
| «DIP говорит — зависимость от абстракции» | DIP говорит: *зависите от абстракции, а не от конкретной реализации*, **когда у вас есть несколько реализаций и вы не хотите привязываться к одной**. Одна реализация = абстракция = конкретный класс. |

### Когда интерфейс **нужен**

```php
// Реальность: три способа доставки, и выбор зависит от окружения
interface NotificationChannel {
    public function send(Notification $n): void;
}

final class SmtpChannel  implements NotificationChannel { /* ... */ }
final class SlackChannel implements NotificationChannel { /* ... */ }
final class PushChannel  implements NotificationChannel { /* ... */ }

// Здесь интерфейс несёт смысл: OrderService не знает,
// куда полетит уведомление, и это **решение**, а не церемония.
```

---

## Второй пример: SRP, DTO с одним методом

```php
final class OrderLineItem
{
    public function __construct(
        public readonly string $sku,
        public readonly int    $qty,
        public readonly int    $unitPriceCents,
    ) {}

    public function subtotalCents(): int
    {
        return $this->qty * $this->unitPriceCents;
    }
}
```

**Нарушение SRP?** Формально да: класс хранит данные **и** содержит поведение. «Хранение данных» — одна ответственность, «вычисление суммы» — вторая.

**Почему это правильно:**

- Метод — одна строка.
- Выделение `OrderLineItemCalculator` с методом `calculate(OrderLineItem $item): int` создаёт **второй класс** для одной строки.
- `subtotalCents()` не может быть «подменён» (нет нескольких стратегий расчёта).
- Если завтра добавится скидка — метод вырастет до 3–4 строк. По-прежнему один класс, потому что логика **привязана к данным** этой строки заказа.

**Когда SRP **нужен**:**

```php
// 50 строк расчёта с логистикой, налогами, скидками, промокодами.
// Здесь выделение PricingStrategy — не церемония, а необходимость,
// потому что 50 строк не должны жить в DTO.
final class OrderLineItem
{
    public function __construct(
        public readonly string $sku,
        public readonly int    $qty,
        public readonly int    $unitPriceCents,
    ) {}
    // НЕТ subtotalCents(). Данные — только данные.
}

final class PricingCalculator
{
    public function __construct(
        private TaxEngine      $tax,
        private PromoResolver  $promos,
        private ShippingTable  $shipping,
    ) {}

    public function lineTotal(OrderLineItem $item, Context $ctx): Money
    {
        // 50 строк с тремя внешними зависимостями
    }
}
```

---

## Метод: три вопроса перед «исправлением»

Когда видите нарушение SOLID, спросите:

| Вопрос | Если «нет» → нарушение оправдано |
|---|---|
| **Есть ли (или предвидится) второй вариант?** Для DIP/ISP. Если одна реализация и не предвидится — интерфейс = шум. |
| **Нарушение создаёт ли дублирование или рост при расширении?** Для OCP/SRP. Если 2 варианта и они не дублируют структуру — if/else нормален. |
| **Что я теряю, если не «исправлю»?** Если ответ «ничего конкретное» — вы не устраняете риск, а добавляете слой. |

И наоборот: если ответ на любой из трёх вопросов «да» — нарушение **не** оправдано, и его стоит исправить, пока дёшево.

---

## Итог

SOLID — это **инструменты для снижения стоимости изменения кода**. Они не про «правильный код». Если код не будет меняться (прототип, скрипт, внутренний инструмент на 3 месяца) — большинство SOLID-правил не имеют к нему отношения. Если код будет меняться, но варианты не разветвляются — DIP/ISP/OCP избыточны.

Нарушение оправдано, когда **цена соблюдения > цена нарушения**. Это не оправдание лени, а инженерное суждение. Разница в том, что вы **знаете**, что нарушаете и **почему**, а не просто не написали интерфейс, потому что было лень.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

# OCP без наследования

Ключевой тезис: OCP — это **не** «наследование». Это свойство системы: *«добавление нового варианта не требует модификации существующего кода»*. Наследование (`interface` + `implements` / `extends`) — одна из механик достижения этого свойства. Не единственная.

Ниже — шесть альтернатив, от простых к сложным.

---

## 1. Map dispatch: `array<string, Closure>`

Самый дешёвый способ. Ни одного класса.

```php
final class PaymentRouter
{
    /** @var array<string, Closure(Payment): PaymentResult> */
    private array $handlers;

    /**
     * @param iterable<array{method: string, handler: Closure(Payment): PaymentResult}> $items
     */
    public function __construct(iterable $items)
    {
        foreach ($items as $item) {
            $this->handlers[$item['method']] = $item['handler'];
        }
    }

    public function charge(Payment $p): PaymentResult
    {
        $handler = $this->handlers[$p->method]
            ?? throw new UnknownPaymentMethodException($p->method);

        return $handler($p);
    }
}
```

Регистрация нового варианта — **одна строка в конфиге**, без нового класса:

```yaml
# services.yaml
App\Payment\PaymentRouter:
    arguments:
        $items:
            -
                method: card
                handler: '@=service("App\Payment\CardGateway").charge(...)'
            -
                method: wallet
                handler: '@=service("App\Payment\WalletClient").charge(...)'
```

**Когда уместно:** 3–6 вариантов, логика каждого — 1–3 строки (вызов внешнего API). Не нужно 5 классов + 5 файлов, чтобы сказать «вызови X».

**Ограничение:** если логика варианта — 30 строк с несколькими зависимостями — замыкание в YAML нечитается. Тогда уже composition.

---

## 2. Pipeline / Chain of Responsibility

Поведение разбивается на **стадии**, каждая — отдельный объект. Новый вариант = новая стадия. Существующие стадии не трогаются.

```php
interface Stage
{
    public function handle(Context $ctx, Next $next): void;
}

final readonly class Next
{
    public function __construct(
        public readonly array    $stages,
        public readonly int      $index,
        public readonly Context  $ctx,
    ) {}

    public function next(): void
    {
        $this->stages[$this->index]($this->ctx, new Next($this->stages, $this->index + 1, $this->ctx));
    }
}

// Конкретные стадии:
final class ValidateStage implements Stage
{
    public function handle(Context $ctx, Next $next): void
    {
        if ($ctx->order->amountCents <= 0) {
            $ctx->reject('amount must be positive');
            return;
        }
        $next->next();
    }
}

final class FraudCheckStage implements Stage
{
    public function __construct(private FraudApi $api) {}

    public function handle(Context $ctx, Next $next): void
    {
        $score = $this->api->score($ctx->order);
        if ($score > 0.8) {
            $ctx->reject('fraud risk');
            return;
        }
        $next->next();
    }
}

// Новый вариант — новый файл, ноль правок старых:
final class SanctionsScreeningStage implements Stage
{
    public function __construct(private SanctionsList $list) {}

    public function handle(Context $ctx, Next $next): void
    {
        if ($this->list->isHit($ctx->order->payerName)) {
            $ctx->reject('sanctions hit');
            return;
        }
        $next->next();
    }
}
```

Сборка в DI:

```yaml
App\Payment\PaymentPipeline:
    arguments:
        $stages:
            - '@App\Payment\ValidateStage'
            - '@App\Payment\FraudCheckStage'
            - '@App\Payment\SanctionsScreeningStage'   # ← добавили, не трогая другие
```

**Когда уместно:** поведение — **последовательность шагов**, каждый с разными зависимостями и разными условиями отказа. Очереди, валидация, middleware, approval-флоу.

**Ключевое отличие от Strategy:** Strategy выбирает **один** из вариантов. Pipeline **соединяет** все варианты последовательно. Это разные применения OCP.

---

## 3. Event-driven / Observer

Существующий код **не знает**, что будет при событии. Новые подписчики — новые файлы.

```php
// Существующий код — закрываён:
final class OrderService
{
    public function __construct(private EventDispatcher $dispatcher) {}

    public function confirm(string $orderId): void
    {
        // ... бизнес-логика ...
        $this->dispatcher->dispatch(new OrderConfirmedEvent($orderId));
        // Я НЕ знаю и НЕ ДОЛЖЕН знать, что кто-то отправит email,
        // начислит баллы или создаст задачу в Jira.
    }
}

// Новый подписчик — новый файл. Zero changes в OrderService.
final class SendConfirmationEmail
{
    public function __construct(private Mailer $mailer) {}

    #[AsEventListener]
    public function __invoke(OrderConfirmedEvent $event): void
    {
        $order = $this->repo->find($event->orderId);
        $this->mailer->send($order->customerEmail, 'Заказ подтверждён', '...');
    }
}

final class AwardLoyaltyPoints
{
    #[AsEventListener]
    public function __invoke(OrderConfirmedEvent $event): void
    {
        // ...
    }
}
```

**Когда уместно:** реакция на событие **не является частью контракта** вызывающего. Вызывающий не должен знать, что «после подтверждения заказа кто-то что-то делает». Отложенные реакции, асинхронные side-эффекты.

**Внимание:** это **не** замена Strategy. Если реакция **обязательна для correctness** (например, запись в ledger), event — плохой выбор: потеря события = потеря денег. Event — для side-эффектов, а не для core-флоу.

---

## 4. Configuration-driven: данные, а не код

Варианты описываются **данными**, а не классами. Код обрабатывает общий случай.

```php
// Конфигурация (YAML / DB / env):
//
//   - method: card
//     gateway: stripe
//     fee_bps: 290
//     async: false
//     max_cents: 5000000
//
//   - method: crypto
//     gateway: bitcoin_liquid
//     fee_bps: 100
//     async: true
//     max_cents: null
//
//   - method: wallet
//     gateway: internal_wallet
//     fee_bps: 0
//     async: false
//     max_cents: 1000000

final class MethodConfig {
    public function __construct(
        public readonly string $method,
        public readonly string $gateway,
        public readonly int    $feeBps,
        public readonly bool   $async,
        public readonly ?int   $maxCents,
    ) {}
}

final class PaymentProcessor
{
    /** @param iterable<MethodConfig> */
    public function __construct(iterable $configs)
    {
        $this->configs = iterator_to_array($configs, keys: true);
    }

    public function charge(Payment $p): PaymentResult
    {
        $cfg = $this->configs[$p->method]
            ?? throw new UnknownPaymentMethodException($p->method);

        $this->assertLimits($cfg, $p);
        $gateway = $this->gateways[$cfg->gateway];  // общий клиент по имени

        $resp = $gateway->charge($p, $cfg->feeBps);
        return $this->mapResult($p, $resp, $cfg->async);
    }

    private function assertLimits(MethodConfig $cfg, Payment $p): void
    {
        if ($cfg->maxCents !== null && $p->amountCents > $cfg->maxCents) {
            throw new LimitExceededException($cfg->method, $cfg->maxCents);
        }
    }
}
```

Новый метод — **новая строка в YAML**. Ни одного PHP-файла.

**Когда уместно:** варианты отличаются **параметрами** (fee, limit, timeout, async), а не **поведением**. 80% «стратегий» на самом деле — конфиг.

**Ограничение:** если поведение каждого варианта структурно разное (у crypto — polling, у bank — webhook, у card — sync) — конфиг не выразит. Тогда composition.

---

## 5. DI + tagged services без интерфейса

Symfony позволяет собирать список зависимостей **без общего интерфейса**, если вы используете `tagged_iterator` и типизированные параметры:

```yaml
# Каждый «вкладчик» — отдельный сервис, без интерфейса.
App\Report\CardFeeLine:
App\Report\PayPalFeeLine:
App\Report\CryptoFeeLine:

App\Report\MonthlyReport:
    arguments:
        $lines:
            - tag: app.report.fee_line
```

```php
#[AsTaggedItemAttribute]  // PHP 8.3+
final class CardFeeLine
{
    public function __construct(private CardGateway $gw) {}
    public function render(): array
    {
        return ['method' => 'card', 'fee' => $this->gw->totalFees()];
    }
}

final class MonthlyReport
{
    /** @param iterable<object> $lines — любой объект, у которого есть render() */
    public function __construct(iterable $lines)
    {
        $this->lines = $lines;
    }

    public function build(): array
    {
        $out = [];
        foreach ($this->lines as $line) {
            $out[] = $line->render();  // duck typing, не интерфейс
        }
        return $out;
    }
}
```

Здесь **нет** `interface FeeLine`. Каждый класс независим. Новый вариант — новый класс + тег. Существующие не трогаются.

**Когда уместно:** варианты **не обязаны** быть взаимозаменяемыми (у одного `render(): array`, у другого `render(): string`). Интерфейс бы заставил их выглядеть одинаково. Без интерфейса — каждый в своём формате.

---

## 6. Higher-order functions / functional composition

```php
// «Вариант» — это функция, а не объект.
type Transform = Closure(string): string;

final class TextPipeline
{
    /** @var Transform[] */
    private array $steps;

    public function __construct(iterable $steps)
    {
        $this->steps = [...$steps];
    }

    public function add(Transform $step): void
    {
        $this->steps[] = $step;
    }

    public function run(string $input): string
    {
        $out = $input;
        foreach ($this->steps as $step) {
            $out = $step($out);
        }
        return $out;
    }
}

// Новое преобразование — одна строка:
$pipe->add(fn(string $s) => str_replace('http://', 'https://', $s));
$pipe->add(fn(string $s) => trim($s));
$pipe->add(fn(string $s) => mb_strtolower($s));
```

**Когда уместно:** шаги — чистые функции над значением, без состояния и без зависимостей. Преобразования строк, числовые pipeline, map/filter/reduce.

---

## Сравнение

| Механика | Варианты взаимозаменяемы? | Варианты последовательны? | Состояние у варианта? | Кол-во кода на вариант |
|---|---|---|---|---|
| Map dispatch | Да | Нет | Нет / мало | 1–3 строки |
| Pipeline / Chain | Нет (все исполняются) | Да | Да | 5–50 строк |
| Event / Observer | Нет (все реагируют) | Нет (параллельно) | Да | любой |
| Configuration | Да (один выбирается) | Нет | Нет | 0 (данные) |
| DI + tag без интерфейса | Да / нет | Нет | Да | любой |
| Higher-order | Да / нет | Да | Нет | 1–10 строк |

---

## Как выбрать

```
Варианты — параметры (fee, limit, timeout)?
  → Configuration-driven (#4)

Варианты — последовательные шаги с отказом на каждом?
  → Pipeline / Chain (#2)

Варианты — параллельные реакции, не обязательные для correctness?
  → Event-driven (#3)

Варианты — взаимозаменяемые, логика 1–3 строки, нет состояния?
  → Map dispatch (#1) или Higher-order (#6)

Варианты — взаимозаменяемые, логика 30+ строк, разные зависимости?
  → Composition (Strategy) или DI + tag (#5)

Варианты — не обязаны быть одинаковой формы?
  → DI + tag без интерфейса (#5)
```

---

## Частая ошибка

Применять **наследование** там, где нужен **Pipeline**, и наоборот.

- Strategy говорит: *«выбери одного»*.
- Pipeline говорит: *«выполни всех по очереди»*.

Если вы написали Strategy, а в production выяснилось, что нужно «сначала валидация, потом антифрод, потом санкционный скрининг» — вы не «добавите новую Strategy». Вы **перепишете** существующую. Это признак того, что нужна была не Strategy, а Pipeline. И наоборот: если вы написали Pipeline, а на самом деле нужен был только один из трёх gateways — вы гоняете 3 стадии, когда достаточно одной.

Выбор механики определяется **семантикой** (выбор vs. последовательность vs. реакция), а не тем, «что гуглится лучше».

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

# SRP — не про классы

Формулировка Роберта Мартина: *«У класса должна быть одна причина меняться»*. Слово «класс» здесь — артефакт эпохи (1994, C++). Принцип в общем виде:

> **Любой элемент кода должен иметь ровно одну причину для изменения.**

«Элемент кода» — это функция, модуль, файл, сервис, таблица, микросервис, а иногда и переменная.

---

## Функция

Самый частый уровень, где SRP нарушается и не замечается.

```php
// Одна функция. Пять причин меняться.
function importCsv(string $path): void
{
    // 1. Формат CSV изменится (разделитель, encoding, BOM)
    $rows = array_map('str_getcsv', file($path));

    // 2. Правила валидации изменятся
    foreach ($rows as $row) {
        if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) continue;
        if ($row[2] < 18) continue;
    }

    // 3. Формат записи в БД изменится
    $pdo->beginTransaction();
    foreach ($rows as $row) {
        $pdo->exec("INSERT INTO users (email, name, age) VALUES (..., ..., ...)");
    }
    $pdo->commit();

    // 4. Логирование / аудит изменится
    $logger->info("Imported " . count($rows) . " rows from $path");

    // 5. Уведомления об изменятся
    $mailer->send('admin@site.ru', "CSV imported: " . $path);
}
```

Пять независимых причин переписать этот код. Каждая по-разному: CSV-формат меняется вместе с поставщиком, валидация — вместе с продуктовой командой, схема БД — вместе с DBA, логирование — вместе с infra, уведомления — вместе с бизнес-логикой.

Разделение:

```php
function importCsv(string $path): void
{
    $rows    = CsvParser::parse($path);          // причина 1
    $valid   = UserValidator::filter($rows);      // причина 2
    $saved   = UserImporter::persist($valid);     // причина 3
    AuditLogger::log('csv_import', $path, $saved); // причина 4
    Notifier::csvImported($path, $saved);          // причина 5
}
```

Функция `importCsv` теперь — **оркестратор**: её причина меняться одна — «изменилась последовательность шагов». Каждый шаг имеет свою причину.

---

## Файл / модуль

```php
// src/Service/OrderService.php  ← 400 строк
// Внутри:
//   - расчёт стоимости заказа
//   - проверка наличия на складе
//   - формирование PDF-счёта
//   - отправка email
//   - запись в audit log
//   - расчёт лояльности

// Пять причин меняться в одном файле.
// Три разработчика редактируют его одновременно → merge conflict каждый спринт.
```

Разделение по файлам — не «архитектурная мода». Это снижение вероятности конфликта и ускорение ревью: ревьюер `PdfGenerator` не должен читать 150 строк про лояльность, чтобы понять, зачем тут `use LoyaltyEngine`.

---

## Таблица БД

Это менее очевидный уровень, но SRP работает и здесь.

```sql
-- Таблица "users" в проекте 2017 года:
CREATE TABLE users (
    id            INT PRIMARY KEY,
    email         VARCHAR(255),
    password      VARCHAR(255),

    -- «А давайте добавим профиль»
    full_name     VARCHAR(255),
    avatar_url    VARCHAR(255),
    bio           TEXT,
    birth_date    DATE,

    -- «А давайте добавим настройки»
    theme         VARCHAR(20),
    language      VARCHAR(10),
    notifications BOOLEAN,
    two_fa_secret VARCHAR(255),

    -- «А давайте добавим историю»
    last_login    TIMESTAMP,
    login_count   INT,
    last_ip       VARCHAR(45),

    -- «А давайте добавим платформу»
    platform      VARCHAR(20),   -- 'web' | 'ios' | 'android'
    device_id     VARCHAR(255),
    push_token    TEXT,

    -- «А давайте добавим маркетинг»
    marketing_optin BOOLEAN,
    source        VARCHAR(100),
    utm_campaign  VARCHAR(255)
);
```

**Пять причин меняться** в одной таблице:
- меняется аутентификация → правим `password`, `two_fa_secret`
- меняется профиль → правим `full_name`, `avatar_url`, `bio`
- меняется система уведомлений → правим `notifications`, `push_token`
- меняется маркетинговая атрибуция → правим `source`, `utm_campaign`
- меняется трекинг → правим `last_login`, `login_count`, `last_ip`

Каждая из этих причин тянет за собой свои migration, свой бизнес-логик, свои права доступа. В одной таблице — они переплетены.

Разделение: `users`, `user_profiles`, `user_settings`, `user_device_bindings`, `user_attribution`. Каждая таблица — одна причина меняться.

Это не «нормализация ради нормализации». Это то, что **права доступа** в PostgreSQL/Redis можно выдать по таблице: маркетинг видит `user_attribution`, но не видит `password`. А в одной таблице — либо всё, либо ничего.

---

## Микросервис

Макроскопический SRP.

```
Сервис "order":
  - создаёт заказ
  - проверяет наличие на складе
  - рассчитывает стоимость
  - отправляет email клиенту
  - логирует в audit
  - начисляет баллы лояльности
  - формирует PDF-счёт
  - обновляет дашборд аналитики

→ Семь причин меняться в одном deployable-единице.
→ Любой из семи изменений требует деплой всего сервиса.
→ Один баг в лояльности может упасть весь order.
```

Каждый из этих блоков — отдельная причина меняться. В单体 (monolith) это может быть нормально (одна команда, один ритм релизов). В микросервисной модели — это SRP на уровне сервиса: «один сервис = одна причина меняться = один team = один deploy cadence».

---

## Переменная

Микро-уровень, но реальный.

```php
$x = 0;
$x = $order->id;          // теперь $x — id
$x = $order->total;       // теперь $x — сумма
$x = count($items);       // теперь $x — количество
$flag = ($x > 100);       // теперь $x — порог

// Четыре разных смысла в одной переменной.
// Причина меняться: изменился любой из четырёх смыслов → надо прочитать весь блок
// и понять, в какой точке $x уже переприсвоен.
```

```php
$orderId    = $order->id;
$totalCents = $order->total;
$itemCount  = count($items);
$exceeded   = ($totalCents > 100_000);
```

Одна причина меняться для каждой переменной.

---

## Где SRP **не** нужен

Чтобы не скатиться в «один класс — одна строка»:

```php
// Это НОРМАЛЬНО. Не надо делить на 5 функций.
final readonly class Money {
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {}

    public function add(self $other): self {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->amountCents + $other->amountCents, $this->currency);
    }

    public function toDisplay(): string {
        return number_format($this->amountCents / 100, 2) . ' ' . $this->currency;
    }
}
```

Три метода, один класс, одна ответственность: «представлять сумму денег и делать операции над ней». `add` и `toDisplay` — не разные причины меняться. Они обе меняются, когда меняется модель денег (например, добавляем мульти-валюту). Это **одна** причина.

Критерий: **если два метода меняются одновременно и по одной и той же причине — они в одном классе. Если меняются по разным причинам — в разных.**

---

## Чек на каждый элемент

| Элемент | Вопрос | Если «да» к ≥2 вопросам → SRP нарушен |
|---|---|---|
| Функция | Что заставляет меня переписать строку 15? Строку 80? Строку 140? | |
| Класс | Что заставляет меня переписать метод A? Метод B? | |
| Файл | Что заставляет меня переписать верхнюю половину? Нижнюю? | |
| Таблица | Что заставляет меня переписать колонки 1–5? Колонки 6–12? | |
| Сервис | Что заставляет меня переписать endpoint A? Endpoint B? | |
| Переменная | Что заставляет меня переписать значение в строке 10? В строке 25? | |

Если ответы на разные строки/методы/колонки **независимы** (меняет один разработчик, по одной тикете, с разным ритмом) — это разные причины, и элемент надо разделить.

Если ответы **связаны** (меняет одна команда, по одной эпике, одновременно) — это одна причина, и делить не нужно.

> ⏱ 36.0s · prompt 10661 tok · output 5062 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

The user asks about Symfony's Dependency Injection container internals: compilation, autowiring, and what happens when a service is first requested. This is a deep technical question that requires precise knowledge of Symfony's internals.

Let me structure my answer:

1. The big picture: what the DI container is
2. Compilation phase (what happens at cache-warmup / `cache:clear`)
   - Service definitions collection
   - Resolution: autowiring, autoconfiguration, aliases
   - Optimization: inlining, removal of unused services, service decoration
   - Generating the PHP file (container.xxx.php)
3. Autowiring mechanics
   - How Symfony reads type hints
   - How it maps type → service (by class name, by interface, by tag)
   - `! { ! }` syntax, `!` for optional, `!` for null
   - The `autowire` flag in YAML
   - How it differs from manual wiring
4. Runtime: first request
   - The generated PHP file is just a class
   - `get('service_id')` → factory method
   - Singleton vs prototype
   - The `getService` / `createService` pattern
   - Lazy loading (proxy)
   - What happens with `public` vs `private` services

Let me be precise about the generated code. Symfony generates a PHP class like:

```php
class ContainerXX extends Container
{
    private array $services = [];

    public function get(string $id): ?object
    {
        return match ($id) {
            'App\Service\Foo' => $this->services['App\Service\Foo'] ??= $this->createApp_Service_FooService(),
            // ...
            default => throw new ServiceNotFoundException($id),
        };
    }

    protected function createApp_Service_FooService(): App\Service\Foo
    {
        return new App\Service\Foo($this->get('App\Service\Bar'));
    }
}
```

Key points:
- The container is a single PHP file
- Services are private by default (removed from public API)
- `??=` means singleton (created once, cached in `$this->services`)
- For prototype services, no `??=` — created fresh each time
- Aliases are resolved at compile time
- Autowiring happens at compile time, NOT at runtime
- The generated code has no reflection at runtime (in production)

Let me also cover:
- `public` vs `private` services
- `shared` vs `non-shared` (prototype)
- Service decoration
- Lazy services (proxies)
- The difference between dev (reflection-based) and prod (compiled) mode

Actually, in dev mode Symfony still uses the compiled container but with debug info. In both dev and prod, the container is compiled. The difference is:
- Dev: `public` services accessible, deprecations shown, debug info
- Prod: only `public` services accessible (which is usually just the kernel and a few controllers), all others private

Wait, actually in Symfony, ALL services are private by default in the compiled container. The `public` flag makes them accessible via `$container->get()`. In production, even `public` services are inlined if possible.

Let me be careful about the autowiring mechanism:
1. Symfony sees a type hint in the constructor
2. It looks for a service definition with that class name
3. If not found by exact class, it looks for a service implementing that interface
4. If multiple implement, it throws (ambiguous) unless tagged
5. `!` for nullable, `![]` for iterable of tagged services

The compilation steps in Symfony 6/7:
1. Merge all configuration (bundles, app config, test overrides)
2. Resolve autowiring (for each service with `autowire: true`)
3. Resolve autoconfiguration (tags, `#[AutoconfigureTag]`, `#[AutoconfigureTag]`)
4. Resolve aliases
5. Resolve service decoration
6. Inline single-use services (if a service is used only once, its creation code is inlined into the consumer)
7. Remove unused services (private services that are never referenced)
8. Generate the PHP class

Let me write a comprehensive, well-structured answer with code examples showing the generated container code.

</details>

**Кандидат:**

# Контейнер DI в Symfony: от YAML до PHP-кода

## Большая картина

В Symfony нет «магии» в рантайме. Контейнер — это **один PHP-файл**, сгенерированный при компиляции. В production-окружении ни одной рефлексии, ни одного `instanceof` на поиск сервисов. Всё решение — на этапе `cache:clear`.

```
┌─────────────────────────────────────────────────────────────────┐
│  Компиляция (build-time)                                        │
│                                                                 │
│  YAML + Attributes + Bundles ──► Merge ──► Resolve ──► Optimize│
│                                                        │         │
│                                                        ▼         │
│                                              GeneratedClass.php │
│                                              (один файл)        │
├─────────────────────────────────────────────────────────────────┤
│  Рантайм (per request)                                          │
│                                                                 │
│  Kernel::boot() ──► new GeneratedClass()                        │
│                    ──► $container->get('App\...')              │
│                         ──► $this->services['...'] ??= create() │
└─────────────────────────────────────────────────────────────────┘
```

---

## Фаза 1: Сборка определений

Symfony собирает «карту» сервисов из всех источников:

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false          # ← по умолчанию ВСЕ приватные

    App\Service\PaymentService:
        # autowire: true → аргументы конструктора решаются автоматически

    App\Infrastructure\Mailer\SmtpMailer:
        arguments:
            $host: '%env(MAIL_HOST)%'
            $port: '%env(int:MAIL_PORT)%'

    App\EventSubscriber\OrderEvents:
        # autoconfigure: true → Symfony сам поставит #[AsEventListener]

    # Явный alias:
    App\Contracts\OrderRepository:
        alias: App\Infrastructure\Doctrine\OrderRepository
```

```php
// App/Attribute/
#[\Symfony\Contracts\Service\Attribute\Required]
// #[AsEventListener]
// #[Route('/orders')]
// — attributes тоже попадают в карту через autoconfigure
```

К концу этой фазы у Symfony есть плоский список:
- **ID сервиса** (обычно FQCN)
- **Класс** для инстанцирования
- **Аргументы** (явные или «не заполнены, ждём autowiring»)
- **Метки** (`container.service_subscriber`, `kernel.event_subscriber`, `routing.route_loader`)
- **public / private**
- **shared / non-shared** (singleton / prototype)
- **decorates** (если сервис декорирует другой)

---

## Фаза 2: Autowiring — как Symfony решает аргументы

Это **compile-time**, не runtime. Symfony читает сигнатуру конструктора (через рефлексии, но один раз, при компиляции) и для каждого параметра ищет соответствие.

### Алгоритм решения одного аргумента

```php
final class PaymentService
{
    public function __construct(
        private OrderRepository  $orders,     // 1
        private PaymentGateway   $gateway,    // 2
        private LoggerInterface  $logger,     // 3
        private ?MailTransport   $mailer,     // 4 (nullable)
        private array            $handlers,   // 5 (iterable)
    ) {}
}
```

| # | Тип | Что Symfony ищет | Результат |
|---|---|---|---|
| 1 | `OrderRepository` (interface) | Сервис с этим FQCN → нет. Сервис, реализующий этот интерфейс → `App\Infrastructure\Doctrine\OrderRepository` (через alias или явное определение) | ✅ |
| 2 | `PaymentGateway` (class) | Сервис `App\Payment\PaymentGateway` → есть | ✅ |
| 3 | `LoggerInterface` | Alias `Psr\Log\LoggerInterface → Symfony\Component\Logger\Logger` | ✅ |
| 4 | `?MailTransport` | Nullable → если сервис не найден, аргумент = `null`. Найден → инстанция | ✅ |
| 5 | `array $handlers` + автоконфиг | Symfony ищет `tagged_iterator` с меткой, которую `autoconfigure` назначил (напр. `app.payment.handler`). Собирает **всех** сервисов с этой меткой в массив | ✅ |

### Что происходит при неоднозначности

```php
interface Notifier {
    public function send(string $to, string $msg): void;
}

final class EmailNotifier implements Notifier { /* ... */ }
final class SlackNotifier implements Notifier { /* ... */ }
```

Если оба зарегистрированы, а в конструкторе `private Notifier $notifier` — **компиляция падает**:

```
Autowired service "App\PaymentService" has a dependency
on class "App\Contracts\Notifier" which has multiple
candidates: "App\Notify\EmailNotifier", "App\Notify\SlackNotifier"
```

Решение — alias, tag или явный аргумент:

```yaml
App\Contracts\Notifier:
    alias: App\Notify\EmailNotifier
```

или

```php
public function __construct(
    /** @var iterable<Notifier> */
    private iterable $notifiers,   // ← ВСЕ Notifier'ы, массив
) {}
```

### `!` и `![]` в YAML (Symfony 6+)

```yaml
App\Service\Foo:
    arguments:
        $gateway: ! { gateway: 'stripe' }   # → new Gateway('stripe')
        $logger:  ! { channel: 'payment' }  # → new Logger('payment')
        $handlers: ! { tag: 'app.handler' } # → tagged_iterator
```

Это синтаксический сахар, не отдельная механика.

---

## Фаза 3: Оптимизация и генерация

После того как все аргументы решены, Symfony делает проходы оптимизации:

### 3a. Inlining (встраивание)

Если сервис **приватный** и используется **ровно один раз**, его конструктор «вкладывается» внутрь потребителя:

```php
// Было бы без inlining:
protected function createApp_Service_PaymentService(): PaymentService
{
    return new PaymentService(
        $this->get('App\Infrastructure\Doctrine\OrderRepository'),
        $this->get('App\Payment\PaymentGateway'),
        $this->get('Psr\Log\LoggerInterface'),
        null,
        $this->get('iterable.app.handler')
    );
}

// После inlining (если OrderRepository используется только здесь):
protected function createApp_Service_PaymentService(): PaymentService
{
    return new PaymentService(
        new OrderRepository($this->get('Doctrine\ORM\EntityManagerInterface')),  // ← inlined
        $this->get('App\Payment\PaymentGateway'),
        $this->get('Psr\Log\LoggerInterface'),
        null,
        $this->get('iterable.app.handler')
    );
}
```

Меньше вызовов `get()`, меньше хеши в `$this->services`, быстрее загрузка.

### 3b. Удаление неиспользуемых

Приватные сервисы, на которые никто не ссылается, **вырезаются** из сгенерированного файла. В production-контейнере их нет.

### 3c. Service decoration

```yaml
App\Infrastructure\Mailer\SmtpMailer: ~

App\Middleware\LoggingMailer:
    decorates: App\Infrastructure\Mailer\SmtpMailer
    arguments:
        $inner: '@App\Infrastructure\Mailer\SmtpMailer.inner'
```

После компиляции:
- `App\Infrastructure\Mailer\SmtpMailer` → на самом деле `LoggingMailer(new SmtpMailer(...))`
- Внутренний переименован в `...SmtpMailer.inner`
- Потребитель не знает, что его «mailer» — декорированный

### 3d. Генерация PHP-файла

Всё сводится к **одному классу-наследнику** `Symfony\Component\DependencyInjection\Container`:

```php
// var/cache/prod/ContainerXXXXXXXX/getXXXXXXXX_PaymentService.php
// (в Symfony 5.x — отдельные файлы; в 6/7 — один большой класс)

namespace ContainerXXXXXXXX;

use Symfony\Component\DependencyInjection\Container;

class ContainerXXXXXXXX extends Container
{
    private array $services = [];
    private array $factories = [];

    public function __construct()
    {
        parent::__construct();
    }

    // ─── Публичный API ──────────────────────────────────────────
    public function get(string $id): ?object
    {
        return match ($id) {
            'App\Controller\OrderController' => $this->services['App\Controller\OrderController'] ??= $this->createApp_Controller_OrderControllerService(),
            'Psr\Log\LoggerInterface'       => $this->services['Psr\Log\LoggerInterface'] ??= $this->createPsr_Log_LoggerInterfaceService(),
            default => throw new ServiceNotFoundException($id),
        };
    }

    // ─── Заводы (private) ───────────────────────────────────────
    protected function createApp_Service_PaymentService(): \App\Service\PaymentService
    {
        return new \App\Service\PaymentService(
            new \App\Infrastructure\Doctrine\OrderRepository(       // ← inlined
                $this->get('doctrine.orm.entity_manager')
            ),
            $this->get('App\Payment\PaymentGateway'),
            $this->get('Psr\Log\LoggerInterface'),
            null,                                                    // ← ?MailTransport → null
            $this->get('iterable.app.handler')
        );
    }

    protected function createApp_Payment_PaymentGatewayService(): \App\Payment\PaymentGateway
    {
        return new \App\Payment\PaymentGateway(
            $this->get('App\Payment\StripeClient'),
            $this->get('App\Payment\PayPalClient'),
        );
    }

    protected function createIterableApp_Handler(): array
    {
        return [
            $this->get('App\Handler\CardHandler'),
            $this->get('App\Handler\WalletHandler'),
            $this->get('App\Handler\CryptoHandler'),
        ];
    }

    protected function createPsr_Log_LoggerInterfaceService(): \Psr\Log\LoggerInterface
    {
        return new \Symfony\Component\Logger\Logger('app');
    }
}
```

### Ключевые детали сгенерированного кода

| Паттерн | Что значит |
|---|---|
| `$this->services['X'] ??= $this->createX()` | **Singleton**: создается один раз, кэшируется в массиве. Второй `get('X')` — O(1), из массива. |
| `$this->createX()` без `??=` | **Prototype / non-shared**: каждый вызов — новый инстанс. |
| `match` в `get()` | Только публичные сервисы. Приватные недоступны через `get()`. |
| `$this->get('doctrine.orm.entity_manager')` | Вложенный `get` — рекурсивная резолвка по уже сгенерированной карте. |
| Отсутствие `instanceof`, `reflection`, `is_a` | В production-файле **нет** динамического поиска. Всё жёстко закодировано. |

---

## Фаза 4: Рантайм — первый запрос

```
HTTP Request
  → Kernel::handle()
    → Kernel::boot()
      → new ContainerXXXXXXXX()      ← конструктор, 0 мс (пустой массив)
      → $container->get('App\Controller\OrderController')
```

Внутренний `get`:

```php
public function get(string $id): ?object
{
    return match ($id) {
        'App\Controller\OrderController'
            => $this->services['App\Controller\OrderController']
               ??= $this->createApp_Controller_OrderControllerService(),
        // ...
    };
}
```

**Первый вызов** → `??=` правая часть → `createApp_Controller_OrderControllerService()`:

```php
protected function createApp_Controller_OrderControllerService(): OrderController
{
    return new OrderController(
        $this->get('App\Service\OrderService'),          // ← рекурсивный get
        $this->get('Psr\Log\LoggerInterface'),
    );
}
```

Каждый вложенный `get` — либо hit в `$this->services` (singleton, уже создан), либо рекурсивное создание. Глубина рекурсии = глубина дерева зависимостей.

**Второй запрос в том же request**:
```php
$this->services['App\Controller\OrderController']  // ← уже есть, return. 1 array lookup.
```

**Второй HTTP-запрос** (новый request, PHP-процесс):
Весь массив `$this->services` — пустой. Контейнер **не** кэшируется между запросами (в стандартной PHP-FPM модели). Кэшируется **только сгенерированный PHP-опкод** (OPcache).

---

## Lazy-сервисы (proxy)

Если зависимость тяжёлая (создаёт HTTP-клиент, открывает БД-пул), но используется не всегда:

```yaml
App\Payment\StripeClient:
    lazy: true
```

Symfony генерирует **proxy-класс**:

```php
protected function createApp_Payment_StripeClientService(): \App\Payment\StripeClient
{
    return new \ContainerXXXXXXXX\Proxy\App_Payment_StripeClientProxy(
        $this,   // ссылка на контейнер
        'App\Payment\StripeClient'
    );
}
```

Proxy наследует от реального класса, но **не вызывает его конструктор** до первого вызова метода:

```php
// Внутри Proxy:
public function charge(...) {
    // ЛАЙВ: первый вызов любого метода
    $real = $this->getContainer()->get('App\Payment\StripeClient');
    return $real->charge(...);
}

// ДО первого вызова:
// конструктор StripeClient НЕ выполнен.
// Объект существует в памяти, но его состояние не инициализировано.
```

Это спасает, когда `StripeClient` в конструкторе делает HTTP-запрос на `/config`, а в 95% запросов он не вызывается.

---

## public vs private — что это реально значит

```yaml
services:
    App\Service\PaymentService:
        public: true      # доступен через $container->get('App\Service\PaymentService')

    App\Payment\StripeClient:
        # public: false   ← по умолчанию
```

В сгенерированном файле:

```php
public function get(string $id): ?object
{
    return match ($id) {
        // ТОЛЬКО public-сервисы:
        'App\Service\PaymentService' => ...,
        'Psr\Log\LoggerInterface'   => ...,
        // StripeClient НЕТ в match.
        default => throw new ServiceNotFoundException($id),
    };
}
```

Приватные сервисы **существуют** в файле (метод `create...` есть), но **недоступны** через публичный `get()`. Они создаются только как аргументы других сервисов.

В production это значит: вы **не можете** написать `$container->get('App\Payment\StripeClient')` в контроллере. Не будет ошибки «нет такого метода» — будет `ServiceNotFoundException`. Это **защита**: приватный сервис не может быть инъектирован «снаружи» контейнера, только через DI.

---

## Частые вопросы

### «Где рефлексия?»

Только при **компиляции** (`cache:clear`). Symfony читает `ReflectionClass` для каждого autowired-сервиса, чтобы извлечь типы параметров. Результат — жёстко закодированный PHP. В рантайме рефлексии нет.

### «А если я добавлю новый параметр в конструктор?»

Нужен `cache:clear`. Старый сгенерированный файл не знает про новый аргумент → `ArgumentCountError` или `ServiceNotFoundException`. Это не баг — это цена отсутствия runtime-рефлексии.

### «Как отлаживать?»

```bash
php bin/console debug:container                 # список сервисов
php bin/console debug:container App\Service\PaymentService  # аргументы, метки
php bin/console debug:autowiring PaymentGateway # что можно инжектнуть
php bin/console debug:container --tag app.payment.handler   # все с меткой
```

### «Symfony vs Laravel — где разница?»

Механика та же: compile-time → PHP-файл. Разница:
- Laravel: autowiring по умолчанию **для всех** публичных методов и конструкторов (в т.ч. в Blade, в `app()` хелпере).
- Symfony: autowiring — только в контейнере. Вне контейнера (`app()`, `Request::createFromGlobals()`) — ручная резолвка.

---

## Итого: что происходит, когда вы пишете `#[Required]` или `autowire: true`

```
Ваш код:
    public function __construct(private OrderRepository $orders) {}

Компиляция:
    1. Symfony видит тип OrderRepository
    2. Ищет в карте: есть alias / реализация → App\Infra\OrderRepository
    3. Генерирует: new PaymentService($this->get('App\Infra\OrderRepository'))
    4. Если OrderRepository сам имеет зависимости → рекурсия
    5. Оптимизирует: inline, удалит неиспользуемые
    6. Записывает в ContainerXXXXXXXX.php

Рантайм (первый запрос):
    $container->get('App\Service\PaymentService')
      → match hit
      → $this->services['...'] ??= $this->create...()
      → new PaymentService($this->get('App\Infra\OrderRepository'))
      → new OrderRepository($this->get('doctrine.orm.entity_manager'))
      → new EntityManager(...)
      → объект готов

Рантайм (последующие get в том же запросе):
    → $this->services['...'] уже есть → return. 1 array lookup.
```

Никакой магии. PHP-код, который вы могли бы написать руками, только сгенерирован и оптимизирован.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

# Циклические зависимости в Symfony

## Что видит Symfony

```php
final class A {
    public function __construct(private B $b) {}
}

final class B {
    public function __construct(private A $a) {}
}
```

При `cache:clear`:

```
Circular reference detected for service "App\Service\A":
  "App\Service\A" → "App\Service\B" → "App\Service\A"
```

Компиляция падает. Рантайм не наступает. Symfony **не** умеет резолвить цикл в рантайме, потому что его модель — **создать объект целиком** (конструктор + все аргументы). В цикле нельзя создать A без B, а B без A.

Это не ограничение Symfony. Это фундаментальная проблема singleton-модели: если объект создаётся один раз и живёт в контейнере, «частично созданный» объект не имеет смысла.

---

## Почему это вообще происходит

Цикл — почти всегда симптом того, что **два сервиса пытаются делать две разные вещи одновременно**, и одна из них — «координация», а другая — «исполнение».

| Реальная причина | Пример |
|---|---|
| Два сервиса координируют друг друга | `OrderService` вызывает `PaymentService`, а `PaymentService` «закрывает» `OrderService` |
| Оба знают про общий контекст, которого нет как отдельного объекта | `AuthService` и `SessionService` знают друг о друге, а `RequestContext` — нет |
| Инфраструктура и домен перепутаны | `OrderRepository` знает `AuditLogger`, а `AuditLogger` знает `OrderRepository` (для записи id) |
| Один «менеджмент» размазан по двум классам | `UserService` создаёт `User`, а `User` при сохранении вызывает `UserService` |

Диагноз важнее лекарства. Сначала — «почему они друг другу нужны?», потом — «как убрать».

---

## Способы разрешения (от лучшего к «последний»)

### 1. Извлечение общего контекста в отдельный объект

Самый частый и самый правильный.

```php
// БЫЛО: цикл
// OrderService → PaymentService
// PaymentService → OrderService

final class OrderService {
    public function __construct(private PaymentService $payment) {}
    public function confirm(Order $o): void {
        $this->payment->charge($o);       // A → B
        // и PaymentService внутри делает $this->orders->markPaid($o)  // B → A ← ЦИКЛ
    }
}

final class PaymentService {
    public function __construct(private OrderService $orders) {}
    public function charge(Order $o): void {
        $this->gateway->charge($o);
        $this->orders->markPaid($o);     // B → A
    }
}
```

Диагноз: оба сервиса пытаются **координировать** одно и то же действие. Им нужен общий объект, который владеет состоянием.

```php
// СТАЛО: нет цикла

final class Order {
    // Доменное состояние. Не знает про сервисы.
    public function markPaid(): void { $this->status = 'paid'; }
    public function canBeCharged(): bool { return $this->status === 'new'; }
}

final class PaymentService {
    public function __construct(private Gateway $gateway) {}

    public function charge(Order $order): void {
        if (!$order->canBeCharged()) {
            throw new OrderNotChargeableException();
        }
        $this->gateway->charge($order);
        $order->markPaid();               // мутирует домен, не вызывает OrderService
    }
}

final class OrderService {
    public function __construct(
        private PaymentService  $payment,
        private OrderRepository $repo,
    ) {}

    public function confirm(string $id): void {
        $order = $this->repo->find($id);
        $this->payment->charge($order);   // A → B, и всё
        $this->repo->save($order);
    }
}
```

Аргумент: `PaymentService` больше не знает про `OrderService`. Он знает про `Order` (домен) и `Gateway` (инфраструктура). Цикла нет.

**Когда работает:** один из двух сервисов делает «мало» (1–3 строки), и эти строки — мутация состояния или доменная проверка. Это состояние/проверка переезжает в домен.

---

### 2. Event-driven: разрыв по времени

Если связь «A → B» и «B → A» — это **последовательные шаги одного флоу**, а не одновременные вызовы, их можно разъединить через событие.

```php
// БЫЛО: цикл
// OrderService → PaymentService → OrderService (callback)

// СТАЛО:
final class OrderService {
    public function __construct(
        private PaymentService  $payment,
        private EventDispatcher $events,
    ) {}

    public function confirm(string $id): void {
        $order = $this->repo->find($id);
        $this->payment->charge($order);
        // НЕ вызываем OrderService. Деспетчим событие.
        $this->events->dispatch(new OrderPaidEvent($order->id));
    }
}

final class PaymentService {
    public function __construct(private Gateway $gw) {}
    public function charge(Order $o): void {
        $this->gw->charge($o);
        // НЕ вызываем OrderService. Не знаем о нём.
    }
}

// Отдельный subscriber — отдельный класс:
final class OnOrderPaid {
    #[AsEventListener]
    public function __invoke(OrderPaidEvent $e): void {
        $order = $this->repo->find($e->orderId);
        $order->markPaid();
        $this->repo->save($order);
        $this->mailer->sendConfirmation($order);
    }
}
```

Аргумент: `PaymentService` не знает, что произойдёт после `charge`. `OrderService` не знает, кто обработает событие. Цикла нет.

**Когда работает:** связь «B → A» — это **последующий** шаг, а не одновременный. «После оплаты — закрыть заказ». Если связь синхронная и обязательная для correctness («без markPaid charge не считается завершённым») — event опасен: потеря события = потеря данных.

**Когда не работает:** если B **обязан** вернуть результат A' (A ждёт ответ B'). Event-подход не передаёт результат.

---

### 3. Mediator / Command Handler

Если два сервиса координируют **команду**, вынесите координацию в отдельный класс.

```php
// БЫЛО: цикл
// OrderService → PaymentService → OrderService

// СТАЛО:
final class ConfirmOrderHandler {
    public function __construct(
        private OrderRepository $orders,
        private PaymentService  $payment,
        private Mailer          $mailer,
    ) {}

    public function __invoke(ConfirmOrderCommand $cmd): void {
        $order = $this->orders->find($cmd->orderId);
        $this->payment->charge($order);      // PaymentService НЕ знает про OrderService
        $order->markPaid();
        $this->orders->save($order);
        $this->mailer->sendConfirmation($order);
    }
}
```

`PaymentService` — чистый: gateway + доменная проверка. `OrderService` больше не существует как «координатор». Координация — в одном месте.

**Когда работает:** координация — это **команда** (одна входная, одна выходная). У вас есть «сделать X» → handler. Нет «X вызывает Y, Y вызывает X».

---

### 4. Setter injection / `#[Required]`

Если связь «B → A» — это **необязательный** колбэк или ленивая инициализация:

```php
final class B {
    private ?A $a = null;

    // Конструктор — без A. Цикла нет.
    public function __construct(private Logger $logger) {}

    #[Required]
    public function setA(A $a): void {
        $this->a = $a;
    }

    public function doThing(): void {
        // $this->a может быть null. Проверяем.
        $this->a?->notify();
    }
}
```

```yaml
services:
    App\Service\A: ~
    App\Service\B:
        calls:
            - [setA, ['@App\Service\A']]
```

Аргумент: `B` создаётся **до** `A`. `A` может зависеть от `B` (конструктор). `B` получает `A` **после** создания, через setter. Цикл сломан временнóй асимметрией.

**Когда работает:** связь «B → A» — опциональная, ленивая, или «уведомление», а не «вызов метода, без которого B не работает».

**Когда НЕ работает:** если `B` **обязан** вызывать `A` в конструкторе или в первом же методе. Тогда `#[Required]` не спасает: на момент конструктора `A` ещё не существует.

---

### 5. Service locator (ContainerAware) — последний ход

```php
final class B implements ContainerAwareInterface {
    private ?ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $c): void {
        $this->container = $c;
    }

    public function doThing(): void {
        $a = $this->container->get(A::class);  // ленивый доступ
        $a->notify();
    }
}
```

Аргумент: `B` не зависит от `A` **в конструкторе**. Зависимость возникает только в момент вызова. Symfony видит: `B` не имеет аргумента `A` → цикла нет.

**Когда допустимо:**
- `B` — «менеджер», который может работать с 3 из 10 возможных `A` в зависимости от рантайм-условия.
- `B` — legacy-код, который вы не хотите рефакторить сейчас.
- `B` — инфраструктурный слой (middleware, listener), который по определению обращается к контейнеру.

**Когда антипаттерн:**
- 3+ сервиса в одном классе берут из контейнера.
- Вы не можете написать тест `B` без mock'а контейнера.
- `B` использует `container->get()` в конструкторе (тогда это просто DI с лишним слоем).

---

### 6. Service decoration

Редкий, но работает: если «A → B → A» — на самом деле «A → [обёртка над B] → A».

```yaml
App\Service\B: ~

App\Middleware\LoggingB:
    decorates: App\Service\B
    arguments:
        $inner: '@App\Service\B.inner'
```

Теперь `A` зависит от `LoggingB`, а `LoggingB` зависит от `B.inner`. Если `B` зависел от `A` — цикл сломан, потому что `A` не зависит от `B` напрямую, а от `LoggingB`.

**Когда работает:** «B → A» — это cross-cutting concern (логирование, metrics, retry), а не бизнес-логика.

---

## Таблица: что выбрать

| Ситуация | Способ |
|---|---|
| Один из двух делает 1–3 строки доменной логики | **1. Извлечение в домен** |
| Связь B→A — последующий шаг, не синхронный | **2. Event** |
| Координация нескольких сервисов вокруг одной команды | **3. Mediator / Handler** |
| Связь B→A опциональная / ленивая | **4. `#[Required]`** |
| Legacy, не хочется трогать, 1–2 места | **5. ContainerAware** |
| Cross-cutting (log, retry, metrics) | **6. Decoration** |

---

## Чего НЕ делать

```php
// ❌ «А давайте просто сделаем оба public и будем лезть в контейнер»
final class A {
    public function __construct(private ContainerInterface $c) {}
    public function do(): void { $this->c->get(B::class)->callMe(); }
}
final class B {
    public function __construct(private ContainerInterface $c) {}
    public function callMe(): void { $this->c->get(A::class)->do(); }
}
// Цикл не исчез. Он просто стал невидимым для компилятора.
// В рантайме: A создаётся → get(B) → B создаётся → get(A) → A уже есть → OK.
// НО: если порядок создания изменится — StackOverflowError.
// И вы потеряли возможность написать тест без контейнера.

// ❌ «А давайте сделаем B prototype»
App\Service\B:
    shared: false
// Каждый get(B) — новый инстанс.
// A создаёт B₁ → B₁ создаёт A (уже есть, singleton) → OK.
// НО: B₁ ≠ B₂. Если кто-то ещё держит ссылку на B₂ — состояние рассинхронизировано.
// Это не разрешение цикла. Это замаскированный баг.
```

---

## Диагностика: как быстро увидеть причину

```bash
# 1. Кто зависит от кого?
php bin/console debug:container --tag='' | grep -A5 'App\Service\A'
php bin/console debug:container --tag='' | grep -A5 'App\Service\B'

# 2. Попробовать скомпилировать — Symfony покажет полный путь цикла:
php bin/console cache:clear
# "Circular reference detected for service "A":
#   A → B → A"

# 3. Посмотреть сгенерированный файл (если он есть до cache:clear):
grep -n 'function createApp_Service_A\|function createApp_Service_B' \
    var/cache/dev/Container*/*_A.php
```

В 90% случаев после чтения двух `create...` методов становится очевидно, что один из аргументов — «лишний» и его можно убрать в домен или в event.

---

## Итого

Цикл — это не «проблема DI-контейнера». Это «два сервиса пытаются координировать одно действие, не имея общего координатора». Решения идут от **переноса координации в одно место** (домен, mediator, event) к **обходу через ленивость** (`#[Required]`, container-aware) и **маскировке** (prototype, decoration). Чем дальше вправо — тем больше вы не решаете проблему, а откладываете её.

> ⏱ 40.8s · prompt 22218 tok · output 5216 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о том, как организовать отложенные side-эффекты (отправка письма + обновление поискового индекса) после успешной регистрации в Symfony, чтобы не замедлять ответ. Нужно описать варианты и trade-offs.

Варианты:
1. Event + Messenger (асинхронный транспорт: AMQP, Redis Streams)
2. Event + Messenger (синхронный транспорт) — для контраста
3. Event + свой "worker" (polling БД, jobs table)
4. Event + `queue:push` (Symfony内置)
5. Просто `register_shutdown_function` + curl — антипаттерн, но стоит упомянуть
6. Отдельный микросервис / HTTP-вызов
7. Database trigger + внешний процесс

Структурирую ответ:
- Сначала проблема формально
- Варианты от простого к сложному
- Для каждого: как выглядит код, trade-offs
- Итоговая рекомендация для типичного случая
- Что НЕ делать

Код должен быть конкретным, Symfony 6/7, PHP 8.2+.

</details>

**Кандидат:**

# Отложенные side-эффекты после регистрации

## Формулировка проблемы

```
POST /register
  → создаём User
  → записываем в БД
  → отчитываемся пользователю: 201 Created   ← пользователь ждёт ТОЛЬКО это
  → отправить welcome email                    ← может 200 мс, может 5 сек
  → обновить поисковый индекс                  ← может 50 мс, может 2 сек
```

Требование: ответ пользователю не зависит от скорости/успеха сторонних операций. При этом операции **должны** выполниться (email — почти обязательно, индекс — желателно).

---

## Вариант 1: Event + Messenger (AMQP / Redis Streams)

### Как выглядит

```php
// ─── 1. Доменное событие ─────────────────────────────────────
final readonly class UserRegisteredEvent
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
    ) {}
}

// ─── 2. Регистрация: деспетчим событие ───────────────────────
final class RegisterCommandHandler
{
    public function __construct(
        private UserRepository    $users,
        private EventDispatcher   $events,
    ) {}

    public function __invoke(RegisterCommand $cmd): int
    {
        $user = new User($cmd->email, $cmd->passwordHash);
        $this->users->save($user);

        $this->events->dispatch(new UserRegisteredEvent($user->id, $user->email));
        // ↑ Messenger перехватывает (см. ниже) и кидает в очередь.
        //   НЕ вызывает хендлеры синхронно.

        return $user->id;  // ← ответ улетает. 201 Created.
    }
}

// ─── 3. Хендлер: отправка email ──────────────────────────────
final class SendWelcomeEmail
{
    public function __construct(private MailerInterface $mailer) {}

    #[AsMessageHandler]
    public function __invoke(UserRegisteredEvent $event): void
    {
        $this->mailer->send(
            recipient: $event->email,
            subject:   'Добро пожаловать!',
            body:      sprintf('Здравствуйте, %s', $event->email),
        );
        // Если SMTP упал → исключение → Messenger ретраит (см. transport)
    }
}

// ─── 4. Хендлер: поисковый индекс ────────────────────────────
final class UpdateSearchIndex
{
    public function __construct(private SearchClient $search) {}

    #[AsMessageHandler]
    public function __invoke(UserRegisteredEvent $event): void
    {
        $user = /* load from repo */;
        $this->search->upsert('users', $user->id, [
            'name'  => $user->name,
            'email' => $user->email,
        ]);
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
                dsn: '%env(MESSENGER_DSN)%'   # amqp://guest:guest@localhost:5672/messages
                # dsn: redis://localhost:6379/0/stream(messenger)
        routing:
            'App\Domain\Event\UserRegisteredEvent': async
```

```yaml
# config/services.yaml
App\Worker\SendWelcomeEmail:
    tags:
        - { name: 'messenger.message_handler', bus: 'messenger.bus.default' }
App\Worker\UpdateSearchIndex:
    tags:
        - { name: 'messenger.message_handler', bus: 'messenger.bus.default' }
```

Работник (отдельный процесс):

```bash
# worker-1: email
php bin/console messenger:consume async -m App\Domain\Event\UserRegisteredEvent --limit=1 --time-limit=3600

# worker-2: индекс (отдельный, чтобы медленный search не блокировал email)
php bin/console messenger:consume async -m App\Domain\Event\UserRegisteredEvent --limit=1 --time-limit=3600
```

(Оба слушают одно событие, но `-m` фильтрует по классу хендлера. Либо — два отдельных транспорта.)

### Trade-offs

| Плюс | Минус |
|---|---|
| Ответ не зависит от SMTP/Search | Нужен брокер (RabbitMQ / Redis) — ещё один компонент в инфраструктуре |
| Ретраиты из коробки (3 попытки, exponential backoff) | Сложность: мониторинг очередей, dead-letter, observability |
| Хендлеры независимы: email упал → search не страдает | Нет гарантии «точно один раз» (at-least-once → идемпотентность обязательна) |
| Масштабируется: добавляем воркеров | Отладка: «почему email не пришёл?» → логи брокера, не логи PHP-FPM |
| Код хендлеров тестируется изолированно | Задержка: от 0 до нескольких секунд (зависит от брокера) |

### Что нужно учесть

- **Идемпотентность**: email не должен уйти дважды. `if (user->emailSentAt !== null) return;` или unique constraint на `outbox`.
- **Порядок**: если email и индекс не зависят друг от друга — ок. Если индекс нужен для «поиск по email в течение 1 сек после регистрации» — это уже не «async», а «fast async».
- **Наблюдаемость**: `messenger:stats`, Prometheus exporter, алерт на «очередь > 1000 сообщений».

---

## Вариант 2: Event + Messenger (синхронный)

```yaml
framework:
    messenger:
        routing:
            'App\Domain\Event\UserRegisteredEvent': sync   # ← не очередь, а прямой вызов
```

Хендлеры вызываются **в рамках того же HTTP-запроса**. Пользователь ждёт.

### Когда допустимо

- «Синхронно, но раздельно»: email и индекс — отдельные хендлеры, один упал → второй всё равно выполнен (если не `throw` до него).
- Не хочется разворачивать RabbitMQ/Redis.
- Загрузка < 10 RPS, latency не критична.

### Trade-offs

| Плюс | Минус |
|---|---|
| Нет брокера | Пользователь ждёт (200 мс – 5 сек) |
| Нет «потерянного» сообщения | SMTP timeout → 5-секундный ответ пользователю |
| Простая отладка (один stack trace) | Масштабируемость: каждый worker-процесс выполняет email |
| | Не можешь ретраить «на сервере» без повтора всего запроса |

**Вывод:** для требования «не замедлять ответ» — **не подходит**. Но как ступенька перед «нужен брокер» — норм.

---

## Вариант 3: Outbox + polling (без брокера)

Если инфраструктурно нельзя поднять RabbitMQ/Redis (например, на «одном VPS»):

```php
// Таблица outbox (в той же БД, что и users):
// id, event_name, payload (JSON), created_at, processed_at, attempts, last_error

final class RegisterCommandHandler
{
    public function __construct(
        private UserRepository $users,
        private EntityManager  $em,   // один и тот же事务
    ) {}

    public function __invoke(RegisterCommand $cmd): int
    {
        $user = new User($cmd->email, $cmd->passwordHash);
        $this->users->save($user);

        $outbox = new Outbox(
            eventName: UserRegisteredEvent::class,
            payload:   ['userId' => $user->id, 'email' => $user->email],
        );
        $this->em->persist($outbox);
        $this->em->flush();   // ← User + Outbox в ОДНОМ транзакционном блоке
        return $user->id;
    }
}
```

Отдельный процесс (cron / systemd timer / `php bin/console app:process-outbox`):

```php
#[Command('app:process-outbox')]
final class ProcessOutboxCommand extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $row = $this->em->getRepository(Outbox::class)
                ->findOneWhere(['processedAt' => null, 'attempts' => new \LessThan(3)]);

            if (!$row) { sleep(5); continue; }

            $event = unserialize($row->payload);  // или json_decode

            try {
                $this->dispatcher->dispatch($event);  // sync dispatch к хендлерам
                $row->processedAt = new \DateTimeImmutable();
            } catch (\Throwable $e) {
                $row->attempts++;
                $row->lastError = $e->getMessage();
            }
            $this->em->flush();
        }
    }
}
```

### Trade-offs

| Плюс | Минус |
|---|---|
| Нет брокера | Polling: задержка 0–5 сек + нагрузка на БД |
| Транзакционная атомарность: user + outbox — или оба, или ни один | Не масштабируется горизонтально без `SELECT ... FOR UPDATE SKIP LOCKED` |
| Простота: одна БД, один процесс | Нет built-in ретраитов с backoff (пишите сами) |
| Легко отладить: `SELECT * FROM outbox` | Конкуренция: два процесса могут взять одну строку |

**Вывод:** нормальный компромисс для малого/среднего проекта без брокера. Для 500+ RPS — нет.

---

## Вариант 4: `register_shutdown_function` + HTTP-вызов (антипаттерн)

```php
register_shutdown_function(function () use ($event) {
    $client = new \GuzzleHttp\Client(['timeout' => 3]);
    $client->post('http://internal-worker/ingest', [
        'json' => $event->toArray()
    ]);
});
```

### Почему это плохо

| Проблема | Следствие |
|---|---|
| `shutdown_function` не гарантирован | OOM / `exit` / SIGHUP → письмо не уйдёт, никто не знает |
| Нет ретраитов | Один сбой → письмо потеряно навсегда |
| Нет порядка | Два shutdown'а могут исполниться в любом порядке |
| Нет наблюдаемости | Не в логах, не в метриках, не в алертах |
| Блокирует worker | FPM-процесс не освобождён до `curl` timeout (3 сек) |

**Вывод:** никогда в production. В dev-скриптах — ок.

---

## Вариант 5: Отдельный сервис / HTTP-API

```
PHP-FPM (app)  ──HTTP──►  worker (отдельный контейнер/процесс)
                             ├── SendWelcomeEmail
                             └── UpdateSearchIndex
```

### Когда

- Worker написан на другом языке (Go, Rust) — «PHP не справляется с async I/O».
- Изоляция: падение worker не влияет на app.
- Уже есть инфраструктура (Kubernetes, service mesh).

### Trade-offs

| Плюс | Минус |
|---|---|
| Полная изоляция | HTTP = ещё одна точка отказа |
| Масштабируемость: N копий worker | Сеть: latency, retries, idempotency key |
| Разные языки, разные команды | Два deploy-цикла, два CI/CD |
| | Наблюдаемость: distributed tracing обязателен |

**Вывод:** для «отправить email + обновить Meilisearch» — over-engineering, пока не > 5000 RPS или > 3 команды.

---

## Итоговая рекомендация

### Для проекта 1–5 человек, 10–500 RPS

```
Event + Messenger + RabbitMQ (или Redis Streams)
```

- Брокер: один RabbitMQ в Docker Compose. Не « enterprise », а `docker run rabbitmq:3`.
- Ретраиты: 3 попытки, exponential backoff (встроено в Messenger).
- DLQ: `messenger:stats` + алерт на `redelivered > 0`.
- Идемпотентность: unique constraint на `email + user_id` в таблице `sent_emails`.

### Для проекта 1 VPS, < 50 RPS, нет брокера

```
Outbox + polling + sync dispatch
```

- Один `systemd`-процесс, `SELECT ... FOR UPDATE SKIP LOCKED`.
- 3 попытки, далее → `dead_letters` таблица + алерт в Telegram.

### Для микросервисной архитектуры, > 1000 RPS

```
Event + Kafka (или NATS) + отдельный consumer
```

- Атомарность: outbox + transactional producer (Kafka).
- Observability: OpenTelemetry, Grafana, алерты на lag.
- Backpressure: consumer rate-limit, не «поехал и улетел».

---

## Чеклист вне зависимости от варианта

```
[ ] Хендлер идемпотентен (повторный вызов = no-op)
[ ] Успешный хендлер не зависит от другого (email упал → search всё равно)
[ ] Есть механизм ретраитов (3–5, backoff)
[ ] Есть dead-letter (не теряется «тихо»)
[ ] Есть алерт на «очередь растёт» / «attempts = max»
[ ] Есть способ «перезапустить» конкретное сообщение (messenger:failed, outbox retry)
[ ] Ответ пользователю не содержит «email отправлен» — только «аккаунт создан»
[ ] Тесты: хендлер изолирован (mock'и), интеграционный: брокер → хендлер → assert
```

---

## Что я бы **не** делал

- **Не** отправлял email в `finally` блока регистрации. Если регистрация откатилась (constraint violation) — email ушёл, а user нет.
- **Не** делал «async» через `usleep` + `fork` в FPM. FPM не для этого.
- **Не** ставил 10 хендлеров на одно событие, если 7 из них — «if ($event->type === 'registration')». Фильтр — в транспорт, а не в код.
- **Не** смешивал «критичное» (запись в ledger) и «некритичное» (email) в одну очередь. Разные SLA → разные транспорты.

> ⏱ 31.9s · prompt 30008 tok · output 3711 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему контроля доступа в Symfony: аутентификация, роли, voters, и когда что использовать. Это продолжение серии вопросов про PHP/Symfony, где я уже демонстрировал глубокие знания. Нужно структурировать ответ:

1. Аутентификация (как user попадает в request)
2. Роли (как они работают, где хранятся, как проверяются)
3. Voters (как работают, когда нужны)
4. Ключевой вопрос: когда voter, а когда достаточно ролей

Я должен показать:
- Как устроен весь pipeline: firewall → authenticator → AccessDecisionManager → voter
- Конкретный код
- Честные trade-offs
- Практические ловушки
- Чёткий критерий выбора: "роль vs voter"

Формат: структурированный, с кодом, таблицами, конкретными примерами. Без воды. Как в предыдущих ответах — прямые формулировки, "было/стало", чеклисты.

Ключевая мысль: роли — это "кто вы", voter — это "что вы можете сделать с этим конкретным объектом". Если ответ зависит только от роли пользователя — роли достаточно. Если ответ зависит от состояния объекта (владелец? статус? контекст?) — нужен voter.

Также важно: Security (firewall, authenticator) ≠ Authorization (roles, voters). Люди их путают.

Структурирую:
1. Разделение: Authentication vs Authorization
2. Authentication: firewalls, authenticators (новый стиль Symfony 6+)
3. Roles: откуда берутся, как проверяются, @IsGranted
4. Voters: механизм, код
5. Критерий: роль vs voter (таблица + примеры)
6. Ловушки
7. Практический пример "сквозной"

Поехали.

</details>

**Кандидат:**

# Контроль доступа в Symfony: от "кто вы" до "можно ли вам это"

## Разделение, которое путают

| | Authentication | Authorization |
|---|---|---|
| Вопрос | **Кто** вы? | **Что** вам можно? |
| Где живёт | Firewall + Authenticator | Roles + Voters + `@IsGranted` |
| Результат | `Token` с `UserInterface` | `bool`: allow / deny |
| Когда | Каждый request (до контроллера) | На каждое действие (в контроллере, в сервисе, в template) |

Symfony объединяет их в один компонент `Security`, но ментально это **два разных слоя**. Аутентификация говорит «вы — Иванов, роль ROLE_ADMIN». Authorization говорит «Иванов, вам можно удалить *этот* заказ, потому что вы его владелец, но не тот, который принадлежит Петрову».

---

## 1. Authentication: как пользователь попадает в request

### Firewall-конфиг

```yaml
# config/packages/security.yaml
security:
    enable_authenticator_manager: true

    password_hashers:
        App\Entity\User:
            algorithm: auto

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        api:
            pattern: ^/api
            stateless: true
            json_login:
                check_path: /api/login
                username_path: email
                password_path: password
                success_handler: App\Security\ApiSuccessHandler
                failure_handler: App\Security\ApiFailureHandler

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
            logout:
                path: app_logout

    # Гость (аноним) не нужен в Symfony 6+.
    # Неавторизованный → firewall возвращает 401/redirect без токена.

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: ROLE_USER }
        - { path: ^/api,    roles: IS_AUTHENTICATED_FULLY }
```

### Что происходит на request

```
HTTP Request
  → Firewall (security.yaml: main / api / dev)
    → Authenticator (form_login / json_login / ApiKeyAuthenticator)
      → извлекает credentials из request
      → UserProvider::loadUserByIdentifier()
        → $user = $em->getRepository(User::class)->findOneBy(['email' => ...])
      → PasswordHasher::verify($user, $rawPassword)
      → new InMemoryToken($user)   // ← Token создан
      → $request->attributes->set('_security_token', $token)
  → Access Control (access_control в security.yaml)
    → path ^/admin, roles: ROLE_ADMIN
      → $user->getRoles() содержит ROLE_ADMIN? → yes → pass
                                             → no  → 403 Forbidden
  → Controller
    → $this->getUser() → User из токена
    → @IsGranted('EDIT', $order) → Voter (см. ниже)
```

### Custom Authenticator (Symfony 6+, без наследования от `AbstractFormLoginAuthenticator`)

```php
// App/Security/ApiKeyAuthenticator.php
final class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function supports?(Request $request): ?string
    {
        // Только для /api, только если есть заголовок
        return $request->headers->has('X-Api-Key') && str_starts_with($request->getPathInfo(), '/api')
            ? 'api_key' : null;
    }

    public function authenticate(Request $request): Passport
    {
        $key = $request->headers->get('X-Api-Key');
        $apiKey = $this->apiKeyRepo->findByKeyHash(hash('sha256', $key));

        if (!$apiKey || !$apiKey->isActive()) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        $user = $apiKey->getUser();
        $badge = new SelfValidatingBadge();
        $badge->setAttributes(['roles' => $user->getRoles()]);

        return new Passport('api_key', $badge, $user->getRoles());
    }

    public function onAuthenticationSuccess(Request $request, Token $token, string $firewallName): ?Response
    {
        return null; // пропускаем дальше, не отвечаем
    }
}
```

Регистрация:

```yaml
services:
    App\Security\ApiKeyAuthenticator:
        tags:
            - { name: security.authenticator, priority: 10 }
```

---

## 2. Roles: откуда берутся и как проверяются

### Хранение

```php
// App/Entity/User.php
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private array $roles;

    public function getRoles(): array
    {
        $roles = $this->roles;

        // ROLE_USER — всегда, даже если в БД пусто.
        // Иначе access_control: roles: ROLE_USER не сработает.
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
}
```

В БД:

```sql
CREATE TABLE user_roles (
    user_id INT,
    role    VARCHAR(50),
    PRIMARY KEY (user_id, role)
);
-- (1, 'ROLE_ADMIN')
-- (1, 'ROLE_EDITOR')
-- (2, 'ROLE_USER')   ← не нужно хранить, addется в getRoles()
```

Или JSON в колонке `roles: string[]` — проще, но не масштабируется на RBAC с тысячами ролей.

### Проверка ролей — три уровня

#### Уровень 1: `access_control` (URL → роль)

```yaml
access_control:
    - { path: ^/admin,   roles: ROLE_ADMIN }
    - { path: ^/billing, roles: [ROLE_ADMIN, ROLE_ACCOUNTANT] }
```

Работает **до** контроллера. Не дошёл → 403. Не знает про объект.

#### Уровень 2: `@IsGranted` в контроллере

```php
#[Route('/users/{id}/edit', name: 'user_edit')]
#[IsGranted('EDIT', 'user')]
public function edit(Request $request, User $user): Response
{
    // Дошли сюда → Voter сказал "да".
    // $user — уже из route param, а не из DB (Symfony сам загрузил).
    return $this->render('user/edit.html.twig', ['user' => $user]);
}
```

Под капотом:

```
#[IsGranted('EDIT', 'user')]
  → $this->authorizationChecker->isGranted('EDIT', $user)
    → AccessDecisionManager
      → спрашивает ВСЕХ registered voters по порядку
      → первый ACCESS_GRANTED → true
      → все ACCESS_ABSTAIN → false (deny by default)
      → первый ACCESS_DENIED → false
```

#### Уровень 3: В сервисе / в template

```php
// В сервисе:
final class OrderService
{
    public function __construct(private AuthorizationChecker $auth) {}

    public function cancel(Order $order): void
    {
        if (!$this->auth->isGranted('CANCEL', $order)) {
            throw new AccessDeniedException('Cannot cancel this order');
        }
        // ...
    }
}

// В template:
{% if is_granted('VIEW', order) %}
    <a href="{{ path('order_detail', {id: order.id}) }}">{{ order.id }}</a>
{% else %}
    <span>Нет доступа</span>
{% endif %}
```

---

## 3. Voters: как они работают

### Механизм

```php
// App/Security/Voter/OrderVoter.php
final class OrderVoter extends Voter
{
    // 1. Какие ability-строки я обрабатываю?
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['EDIT', 'CANCEL', 'VIEW', 'ASSIGN'])
            && $subject instanceof Order;
    }

    // 2. Как я решаю?
    protected function voteOnAttribute(
        string $attribute,
        mixed   $subject,
        UserInterface $user,
    ): bool {
        /** @var Order $order */
        return match ($attribute) {
            'VIEW'   => $this->canView($order, $user),
            'EDIT'   => $this->canEdit($order, $user),
            'CANCEL' => $this->canCancel($order, $user),
            'ASSIGN' => $this->canAssign($order, $user),
        };
    }

    private function canView(Order $order, UserInterface $user): bool
    {
        // Владелец → да
        if ($order->getOwnerId() === $user->getId()) return true;

        // Менеджер → да, если заказ в его отделе
        if (in_array('ROLE_MANAGER', $user->getRoles())
            && $order->getDepartmentId() === $user->getDepartmentId()) {
            return true;
        }

        // Админ → да
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEdit(Order $order, UserInterface $user): bool
    {
        // Только владелец И только в статусе draft
        return $order->getOwnerId() === $user->getId()
            && $order->getStatus() === OrderStatus::Draft;
    }

    private function canCancel(Order $order, UserInterface $user): bool
    {
        // Владелец — только до отгрузки
        if ($order->getOwnerId() === $user->getId()) {
            return $order->getStatus()->isBeforeShipment();
        }

        // Админ — всегда
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canAssign(Order $order, UserInterface $user): bool
    {
        return in_array('ROLE_MANAGER', $user->getRoles())
            && $order->getDepartmentId() === $user->getDepartmentId();
    }
}
```

Регистрация:

```yaml
services:
    App\Security\Voter\OrderVoter:
        tags:
            - { name: security.voter, priority: 10 }
```

### Порядок voters

```yaml
# Если два voter поддерживают одну ability:
App\Security\Voter\OrderVoter:
    tags:
        - { name: security.voter, priority: 20 }  # выше

App\Security\Voter\DepartmentVoter:
    tags:
        - { name: security.voter, priority: 10 }  # ниже
```

`AccessDecisionManager` спрашивает по убыванию priority. Первый `ACCESS_GRANTED` → стоп. Первый `ACCESS_DENIED` → стоп. `ACCESS_ABSTAIN` → следующий.

> **Важно:** в Symfony 5+ `ACCESS_DENIED` **немедленно** останавливает цепочку. В Symfony 4 поведение было другое (собиралось большинство). Если мигрируете — проверяйте.

---

## 4. Критерий: роль vs voter

### Таблица

| Условие доступа | Роль достаточно? | Почему |
|---|---|---|
| «Только админы» | ✅ | Не зависит от объекта |
| «Только сотрудники отдела продаж» | ✅ | Не зависит от объекта |
| «Только владелец документа» | ❌ | Зависит от **какого** документа |
| «Только если статус = draft» | ❌ | Зависит от **состояния** объекта |
| «Владелец ИЛИ менеджер его отдела» | ❌ | Два условия, второе зависит от контекста |
| «Нельзя удалять свой последний аккаунт» | ❌ | Зависит от **количества** аккаунтов (запрос в БД) |
| «Только в рабочее время» | ❌ | Зависит от **времени**, не от роли |
| «Только если баланс > 0» | ❌ | Зависит от **состояния** сущности |

### Обобщённое правило

> **Роль** отвечает на вопрос: *«Кто вы?»* — и ответ не зависит от контекста.
> **Voter** отвечает на вопрос: *«Можно ли вам сделать **это** с **этим** объектом в **этом** состоянии?»* — и ответ зависит от контекста.

Если в формулировке правила есть «**этот** / **свой** / **в этом** / **состояние** / **количество**» — это voter.

### Пример «границы»

```
«Только админы могут видеть раздел /admin»
→ access_control: { path: ^/admin, roles: ROLE_ADMIN }
→ Роль. Voter не нужен.

«Только админы могут видеть заказы другого отдела»
→ Voter. Потому что «другого отдела» — это сравнение
  $order->departmentId !== $user->departmentId.
  Без voter вы не знаете, чей заказ перед вами.
```

---

## 5. Ловушки

### ❌ Роль как прокси для бизнес-логики

```php
// «Мы добавили ROLE_CAN_EDIT_DRAFT_ORDERS»
// Звучит как роль. На самом деле это бизнес-правило, которое:
//  - меняется вместе с доменом (а не с оргструктурой)
//  - зависит от состояния заказа (draft)
//  - зависит от владельца (свой vs чужой)

// Через 6 месяцев у вас 12 таких ролей:
// ROLE_CAN_EDIT_DRAFT_ORDERS
// ROLE_CAN_CANCEL_SHIPPED_ORDERS
// ROLE_CAN_VIEW_FINANCE_REPORTS
// ...

// Это не роли. Это voters, замаскированные под роли.
// Роль — это «кто вы в организации». Business rule — это «что вы можете сделать».
```

### ❌ Voter, который не знает про объект

```php
protected function voteOnAttribute(string $attr, mixed $subject, UserInterface $user): bool
{
    return in_array('ROLE_ADMIN', $user->getRoles());
}
```

Это не voter. Это роль. Если ответ не зависит от `$subject` — используйте `@IsGranted('ROLE_ADMIN')` или `access_control`. Voter без `$subject` — это просто `hasRole()` с лишним слоем.

### ❌ Voter, который делает HTTP-запрос

```php
private function canEdit(Order $order, UserInterface $user): bool
{
    $response = $this->httpClient->get('https://crm.internal/api/v1/permissions', [
        'query' => ['user' => $user->getId(), 'order' => $order->id]
    ]);
    return $response->getStatusCode() === 200;
}
```

Voter вызывается на **каждый** `@IsGranted`, на **каждый** `{% if is_granted %}` в шаблоне. Если в шаблоне 5 таких блоков → 5 HTTP-запросов на один рендер. Кэш обязателен. Или перенести проверку в сервис и не вызывать voter в шаблоне.

### ❌ `ROLE_` префикс для бизнес-ролей

```php
// Плохо:
$roles = ['ROLE_ORDER_OWNER', 'ROLE_DEPARTMENT_MANAGER'];

// Хорошо:
$roles = ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN'];
// «owner» — это не роль. Это отношение «user → order».
// Это проверка в voter, а не роль в БД.
```

Префикс `ROLE_` в Symfony — технический (без него `getRoles()` добавляет `ROLE_USER` автоматически, и ваш «user» без префикса ломает `hasRole('ROLE_USER')`). Бизнес-отношения — не роли.

---

## 6. Сквозной пример: «посмотреть / отредактировать / отменить заказ»

### Модель

```php
enum OrderStatus: string {
    case Draft     = 'draft';
    case Confirmed = 'confirmed';
    case Shipped   = 'shipped';
    case Delivered = 'delivered';
}
```

### Правила (от product owner)

| Действие | Кто |
|---|---|
| Посмотреть свой заказ | Владелец |
| Посмотреть заказ своего отдела | Менеджер отдела |
| Посмотреть любой | Админ |
| Редактировать свой draft | Владелец |
| Отменить свой (до отгрузки) | Владелец |
| Отменить любой | Админ |
| Назначить исполнителя | Менеджер отдела |

### Реализация

```yaml
# access_control — только URL-уровень, без деталей
access_control:
    - { path: ^/orders, roles: ROLE_USER }   # не-логин — 401
```

```php
// Controller
#[Route('/orders/{id}', name: 'order_show')]
#[IsGranted('VIEW', 'order')]
public function show(Order $order): Response { /* ... */ }

#[Route('/orders/{id}/edit', name: 'order_edit')]
#[IsGranted('EDIT', 'order')]
public function edit(Request $request, Order $order): Response { /* ... */ }

#[Route('/orders/{id}/cancel', name: 'order_cancel')]
#[IsGranted('CANCEL', 'order')]
public function cancel(Order $order): Response { /* ... */ }
```

```php
// Voter — вся логика «кто с чем в каком состоянии»
final class OrderVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'CANCEL', 'ASSIGN'])
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
    {
        /** @var Order $order */
        $isOwner    = $order->getOwnerId() === $user->getId();
        $isAdmin    = in_array('ROLE_ADMIN', $user->getRoles());
        $isManager  = in_array('ROLE_MANAGER', $user->getRoles())
                    && $order->getDepartmentId() === $user->getDepartmentId();

        return match ($attribute) {
            'VIEW'   => $isOwner || $isManager || $isAdmin,
            'EDIT'   => $isOwner && $order->getStatus() === OrderStatus::Draft,
            'CANCEL' => ($isOwner && $order->getStatus()->isBeforeShipment()) || $isAdmin,
            'ASSIGN' => $isManager,
        };
    }
}
```

### Шаблон

```twig
{# index.html.twig — список заказов #}
{% for order in orders %}
    <tr>
        <td>{{ order.id }}</td>
        {% if is_granted('EDIT', order) %}
            <a href="{{ path('order_edit', {id: order.id}) }}">Изменить</a>
        {% endif %}
        {% if is_granted('CANCEL', order) %}
            <a href="{{ path('order_cancel', {id: order.id}) }}">Отменить</a>
        {% endif %}
    </tr>
{% endfor %}
```

Каждый `is_granted` → вызов voter → 1 `match` → bool. Без HTTP, без БД (данные уже в `$order`). Быстро.

---

## Итого: как выбирать

```
«Доступ зависит только от роли пользователя?»
  → ROLE_ADMIN / ROLE_MANAGER
  → access_control или @IsGranted('ROLE_...')

«Доступ зависит от объекта (чей? какой статус? какой отдел?)?»
  → Voter

«Доступ зависит от состояния мира (количество, время, внешний сервис)?»
  → Voter + кэш / предзагрузка данных в subject

«Нужен доступ на уровне метода сервиса, не контроллера?»
  → $this->auth->isGranted('ABILITIES', $object) в сервисе
  → Тот же voter, та же логика. Не дублируйте.
```

Одна роль — одно правило. Один voter — один предмет (Order, Invoice, User). Одна ability-строка — один вопрос. Не смешивайте.

> ⏱ 39.9s · prompt 33513 tok · output 5095 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о маппинге Value Objects (Value Objects) в Doctrine при требованиях домена: неизменяемость (immutable) и всегда валидное состояние (invariant). Это очень практичный вопрос на стыке DDD и ORM.

Нужно покрыть:
1. Почему это проблема (конфликт между "immutable + validated" и "mutable entity + lazy hydration")
2. Способы маппинга:
   - Custom Type (Doctrine DBAL Type)
   - Embedded (Embeddable)
   - JSON-колонка + manual mapping
   - Composite/structured type
   - Denormalization (flat columns)
3. Для каждого: плюсы, минусы, подводные камни
4. Иммутабельность: как обеспечить на уровне сущности
5. Валидность: где проверяем
6. Практические примеры кода

Структурирую ответ так:
- Кратко: почему это конфликт
- Способы (с кодом)
- Подводные камни по каждому
- Рекомендации

Язык — русский, как и весь разговор. Форматирование — markdown, таблицы, блоки кода.

</details>

**Кандидат:**

# Value Object в Doctrine: иммутабельность, валидность и маппинг

## Почему это конфликт

Домен требует:

```php
final readonly class Money {
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {
        if ($amountCents < 0)  throw new DomainException('Negative amount');
        if ($currency === '')  throw new DomainException('Empty currency');
    }
}
```

- **Immutable**: `readonly`, нет setters, нет mutation.
- **Always valid**: конструктор бросает при невалидном состоянии. Невозможно создать «сломанный» `Money`.
- **Equality by value**: два объекта с одинаковыми полями — равны.

Doctrine требует:

```php
// Entity mutable по умолчанию:
class Order {
    #[ManyToOne]
    private ?Money $amount = null;  // ← Doctrine будет SET-ить это
}
```

- **Hydration**: `EntityManager` создаёт экземпляр, затем **полями** заполняет через reflection / setters.
- **Change tracking**: сравнивает «было» vs «стало» — значит, объект **должен меняться**.
- **Lazy loading**: поля могут быть `null` до первого доступа.

Конфликт: домен говорит «создан — значит валиден и неизменяем». ORM говорит «создан пустой, заполню потом, и ты будешь меняться».

---

## Способ 1: DBAL Custom Type (structured column)

### Как выглядит

```php
// App/Doctrine/Types/MoneyType.php
final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column) . ', ' . $platform->getVarcharTypeDeclarationSQL($column);
        // → VARCHAR(11), VARCHAR(3)   (amount_cents INT, currency VARCHAR)
        // Реальнее: INT, VARCHAR(3)
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?array
    {
        if ($value === null) return null;
        /** @var Money $value */
        return [$value->amountCents, $value->currency];
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) return null;
        // $value = [12345, 'RUB']
        return new Money($value[0], $value[1]);
        //   ^^^^^^ конструктор валидирует. Невалидные данные → исключение при hydration.
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

Регистрация:

```php
// config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            money: App\Doctrine\Types\MoneyType
```

Схема БД (миграция):

```php
// Migration
public function up(Schema $schema): void
{
    $table = $this->getConnection()->createSchemaManager()->createTable('orders');
    // Или для новой колонки:
    $this->addSql('ALTER TABLE orders ADD COLUMN amount_cents INT NOT NULL, ADD COLUMN amount_currency VARCHAR(3) NOT NULL');
}
```

Маппинг на сущности:

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: MoneyType::NAME)]   // ← custom type
    private Money $amount;

    // Конструктор ДОМЕНА, не Doctrine:
    public function __construct(Money $amount)
    {
        $this->amount = $amount;   // уже валидный, уже readonly-логика
    }

    // Getter
    public function getAmount(): Money { return $this->amount; }

    // СМЕНА суммы — НЕ mutation поля, а замена объекта:
    public function withAmount(Money $newAmount): self
    {
        // Вариант А: mutation (проще для Doctrine, ломает immutable)
        $this->amount = $newAmount;
        return $this;

        // Вариант Б: true immutable (сложнее для Doctrine, см. ниже)
        // $clone = clone $this; $clone->amount = $newAmount; return $clone;
    }
}
```

### Плюсы

| | |
|---|---|
| Валидность при hydration | `new Money($v[0], $v[1])` — если данные в БД сломаны, **бросит сразу**, не даст «сломанного» объекта |
| Прозрачность | Колонки в БД — обычные `INT`, `VARCHAR`. Можно писать raw SQL, делать индексы, `WHERE amount_cents > 1000` |
| Нет JSON-парсинга | Быстрее, чем `json_decode` на каждом access |
| Домен не знает про БД | `Money` — чистый PHP-класс. `MoneyType` — адаптер |

### Минусы и подводные камни

| Проблема | Детали |
|---|---|
| **Две колонки для одного поля** | Миграции, индексы, `ALTER TABLE` — два имени. Легко забыть одну. |
| **`convertToPHPValue` получает `mixed`** | Если в БД `NULL` — возвращаем `null`. Но `Order::amount` типизирован `Money` (не `?Money`). При hydration Doctrine **пробросит null** → TypeError в рантайме. Нужно либо `?Money` на сущности, либо `NOT NULL` в БД + обработка в Type. |
| **Equality / change tracking** | Doctrine сравнивает `===` на объектах. Два разных экземпляра `Money(100, 'RUB')` — **не равны** по `===`. Если вы сделаете `withAmount()` → mutation → Doctrine видит «изменилось» → `UPDATE`. Но если два разных `Money` с одинаковым значением попадут в `persist` → лишний `UPDATE`. |
| **`convertToDatabaseValue` получает объект** | Если кто-то поставит «не-Money» (например, строку) → TypeError. Нет защитного каста. |
| **Composite key / unique constraint** | Не работает «из коробки». `#[ORM\UniqueConstraint(columns: ['amount_cents', 'amount_currency'])]` — только если вы знаете имена колонок. Type их не раскрывает. |

---

## Способ 2: Embeddable (Embedded)

### Как выглядит

```php
// App/Entity/Embedded/MoneyEmbedded.php
#[ORM\Embeddable]
final class MoneyEmbedded
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amountCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    public function __construct(int $amountCents = 0, string $currency = '')
    {
        $this->amountCents = $amountCents;
        $this->currency    = $currency;
    }

    public function toValueObject(): Money
    {
        return new Money($this->amountCents, $this->currency);
        // Валидация в Money::construct
    }

    public static fromValueObject(Money $m): self
    {
        return new self($m->amountCents, $m->currency);
    }
}
```

На сущности:

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $amount;

    public function __construct(Money $amount)
    {
        $this->amount = MoneyEmbedded::fromValueObject($amount);
    }

    public function getAmount(): Money
    {
        return $this->amount->toValueObject();
        // Создаёт НОВЫЙ Money на каждый вызов. Осторожно.
    }

    public function withAmount(Money $new): self
    {
        $this->amount = MoneyEmbedded::fromValueObject($new);
        return $this;
    }
}
```

### Плюсы

| | |
|---|---|
| Нет custom Type | Меньше кода, меньше «магии» |
| Валидность при `toValueObject()` | Вызовете — получите валидный Money |
| Интуитивность | `#[ORM\Embedded]` — стандартная фича, все знают |
| Миграции | Автогенерация колонок `amount_cents`, `amount_currency` |

### Минусы и подводные камни

| Проблема | Детали |
|---|---|
| **Два класса за одно понятие** | `Money` (домен) + `MoneyEmbedded` (инфраструктура). Легко забыть один. |
| **`getAmount()` создаёт новый объект** | `if ($order->getAmount() === $expected)` → **всегда false** (разные экземпляры). Нужно `equals()` или сравнение по полям. |
| **Null-safety** | `MoneyEmbedded` не может быть `null` как целое (это объект с полями). Если `amount` в БД `NULL` — Doctrine создаст `MoneyEmbedded(0, '')` → **тихий невалидный объект**. |
| **Change tracking** | Doctrine отслеживает поля `MoneyEmbedded`. Если вы измените `$this->amount->amountCents = 500` (прямой access через reflection) — Doctrine увидит. Если создадите новый `MoneyEmbedded` и поставите — тоже увидит. Но если «замените» без mutation → потеря. |
| **Нет `equals()`** | `MoneyEmbedded` — mutable POPO. `==` / `===` на объектах → false. |

---

## Способ 3: JSON-колонка + ручной маппинг

### Как выглядит

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Column(type: Types::JSON)]   // или TEXT + ручной json_encode/decode
    private array $amount;   // ['amountCents' => 100, 'currency' => 'RUB']

    public function __construct(Money $amount)
    {
        $this->amount = [
            'amountCents' => $amount->amountCents,
            'currency'    => $amount->currency,
        ];
    }

    public function getAmount(): Money
    {
        return new Money($this->amount['amountCents'], $this->amount['currency']);
    }

    public function withAmount(Money $new): self
    {
        $this->amount = [
            'amountCents' => $new->amountCents,
            'currency'    => $new->currency,
        ];
        return $this;
    }
}
```

### Плюсы

| | |
|---|---|
| Одна колонка | Проще миграции, индексы, raw SQL |
| Гибкость | Можно добавить поле в VO без миграции (если БД позволяет) |
| Простота | Нет custom Type, нет Embedded |

### Минусы и подводные камни

| Проблема | Детали |
|---|---|
| **Нет SQL-запросов по компонентам** | `WHERE amountCents > 1000` → `WHERE JSON_EXTRACT(amount, '$.amountCents') > 1000` (медленно, без индекса в MySQL < 8). В PostgreSQL — `->>` + GIN-индекс. |
| **Нет constraints на компоненты** | `NOT NULL`, `CHECK (amountCents >= 0)` — только на уровне приложения. БД не защитит. |
| **`json_decode` на каждом access** | `getAmount()` → `json_decode` → `new Money(...)`. При 10 000 записей в запросе — 10 000 парсингов. |
| **Нет типизации** | `$this->amount` — `array`. IDE не подскажет ключи. Опечатка `'amountCent'` → `null` → TypeError в `Money::construct`. |
| **Change tracking** | Doctrine сравнивает `array` по `==`. Если вы мутируете вложенный ключ → увидит. Если замените массив → увидит. Но если «не измените» → ок. |

---

## Способ 4: Flat columns (денормализация)

### Как выглядит

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amountCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    // НЕТ Money объекта в сущности.
    // Money создаётся в домене:
    public function getAmount(): Money
    {
        return new Money($this->amountCents, $this->currency);
    }

    public function withAmount(Money $m): self
    {
        $this->amountCents = $m->amountCents;
        $this->currency    = $m->currency;
        return $this;
    }
}
```

### Плюсы

| | |
|---|---|
| Максимальная производительность | Нет парсинга, нет объектов, нет JSON |
| SQL-запросы | `WHERE amount_cents > 1000 AND currency = 'RUB'` — индексы, partitioning |
| Прозрачность | DBA видит колонки, может писать queries |
| Нет «двух классов» | `Money` — только в домене. Сущность — плоская |

### Минусы и подводные камни

| Проблема | Детали |
|---|---|
| **Повторение логики** | `getAmount()` и `withAmount()` — мост между плоскими полями и VO. Легко забыть при добавлении нового поля. |
| **Нет валидности на уровне сущности** | `$order->amountCents = -500; $order->currency = '';` → невалидное состояние. Нужно валидировать в `withAmount()` и в домене. |
| **Equality** | Два `Order` с одинаковыми полями — разные объекты. `===` → false. |
| **Разрастание** | Если VO имеет 5+ полей → 5 колонок + 5 setters + 5 валидаций. Через 3 VO'а — 15 колонок, 15 invariants. |

---

## Иммутабельность: как обеспечить при mutation-модели Doctrine

### Вариант А: «Замена объекта» (clone-on-write)

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $amount;

    // Immutable API:
    public function withAmount(Money $new): self
    {
        $clone = clone $this;
        $clone->amount = MoneyEmbedded::fromValueObject($new);
        return $clone;
    }
}

// Использование:
$order = $repo->find(1);
$newOrder = $order->withAmount(new Money(500, 'RUB'));
$repo->save($newOrder);   // Сохраняем КЛОН. Оригинал не тронут.
```

**Проблема:** `clone` не работает с `ManyToOne` / `ManyToMany` (shallow copy, shared references). Для `Embeddable` — ок (valuetype). Для связей — ломается.

### Вариант Б: «Mutable entity, immutable VO» (практичный)

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $amount;

    // Public API — immutable-подобный:
    public function updateAmount(Money $new): void
    {
        $this->amount = MoneyEmbedded::fromValueObject($new);
        // НЕТ return. Мутация на месте.
    }

    // В домене:
    // $order->updateAmount($newMoney);
    // $em->flush();
}
```

**Почему это ок:**

- VO (`Money`) — immutable. `new Money(100, 'RUB')` — создан, валиден, не меняется.
- Entity (`Order`) — mutable (Doctrine требует). Но **публичное API** — `updateAmount(Money)`, не `setAmountCents(int)`.
- Валидность — в `Money::construct` + `MoneyEmbedded::fromValueObject()`.
- Вы **не можете** поставить «сломанный» Money: конструктор бросит.

Это не «true immutable», но **pragmatic immutable**: VO неизменяем, entity — мутабельный контейнер.

### Вариант В: True immutable + Application Service

```php
// Domain:
final readonly class Order {
    public function __construct(
        public readonly int    $id,
        public readonly Money  $amount,
        public readonly OrderStatus $status,
    ) {}

    public function withAmount(Money $new): self
    {
        return new self($this->id, $new, $this->status);
    }
}

// Infrastructure (Doctrine):
#[ORM\Entity]
final class OrderEntity {
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Embedded]
    private MoneyEmbedded $amount;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;

    // Мост:
    public function toDomain(): Order
    {
        return new Order($this->id, $this->amount->toValueObject(), $this->status);
    }

    public static fromDomain(Order $order): self
    {
        $e = new self();
        $e->id     = $order->id;
        $e->amount = MoneyEmbedded::fromValueObject($order->amount);
        $e->status = $order->status;
        return $e;
    }
}

// Service:
final class OrderService {
    public function updateAmount(int $id, Money $new): void
    {
        $entity = $this->em->find(OrderEntity::class, $id);
        $domain = $entity->toDomain();

        $newDomain = $domain->withAmount($new);   // immutable, новый объект

        $newEntity = OrderEntity::fromDomain($newDomain);
        $this->em->remove($entity);
        $this->em->persist($newEntity);
        $this->em->flush();
    }
}
```

**Проблема:** `remove` + `persist` — это `DELETE` + `INSERT`, не `UPDATE`. Если есть `ON DELETE CASCADE` на связанных таблицах — **катастрофа**. Если есть триггеры, `ON UPDATE` — потеряны.

**Вывод:** true immutable на уровне entity — **непрактично** для Doctrine. Pragmatic immutable (вариант Б) — золотая середина.

---

## Подводные камни (общие)

### 1. `null` при hydration

```php
// БД: amount_cents = NULL, amount_currency = NULL
// Type::convertToPHPValue() → null
// Order::amount → null
// Order::getAmount() → Money::construct(null, null) → TypeError
```

**Решение:**
- `NOT NULL` в БД + `#[ORM\Column(nullable: false)]`
- Или `?Money` на сущности + проверка в домене
- Или default value в Type: `return new Money(0, 'USD');` (маскирует баг)

### 2. Change tracking и `===`

```php
$a = new Money(100, 'RUB');
$b = new Money(100, 'RUB');

$a === $b;  // false (разные объекты)
$a == $b;   // true  (по полям) — но Doctrine использует === для сравнения

// Если Doctrine сравнивает embedded object по ===:
// $order->amount = new MoneyEmbedded(100, 'RUB');  // «изменилось» → UPDATE
// $order->amount = new MoneyEmbedded(100, 'RUB');  // «изменилось» → UPDATE (лишний)
```

**Решение:**
- Не заменять объект, если значение не изменилось.
- Или реализовать `__toString()` / hash в Type для сравнения.
- Или использовать `#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]` + `notifyPropertyChanged()`.

### 3. `json_encode` / `json_decode` в `convertToDatabaseValue`

```php
// Если Type возвращает объект (Money) в БД:
// PostgreSQL: JSONB → ок
// MySQL: JSON → ок (5.7+)
// SQLite: TEXT → json_encode/decode

// Но:
// - Нет SQL-индексов на вложенные ключи (без GIN в PG)
// - Нет constraints
// - Медленнее, чем INT + VARCHAR
```

### 4. `embeddable` + `nullable`

```php
#[ORM\Embeddable]
final class MoneyEmbedded {
    #[ORM\Column(nullable: true)]  // ← nullable на полях embeddable
    private ?int $amountCents;
}

// При hydration: если все поля NULL → MoneyEmbedded(0, '') (default)
// → тихий невалидный объект. Нужно явная проверка.
```

### 5. Валидность при `flush`

```php
$order->updateAmount(new Money(-100, 'RUB'));
// Money::construct бросает → невалидное состояние не попало в entity. ✅

$order->amountCents = -100;  // прямой access (если public)
// Money НЕ создан. Невалидное состояние в entity. ❌

// Решение: приватные поля + только через VO-конструктор.
```

---

## Сравнение

| Критерий | Custom Type | Embeddable | JSON | Flat columns |
|---|---|---|---|---|
| Валидность при hydration | ✅ (в Type) | ✅ (в `toValueObject`) | ⚠️ (в `getAmount`) | ⚠️ (в `getAmount`) |
| SQL-запросы по компонентам | ✅ | ✅ | ⚠️ (JSON) | ✅ |
| Индексы | ✅ | ✅ | ⚠️ | ✅ |
| Кол-во классов | 2 (Type + VO) | 2 (Embedded + VO) | 1 (VO) | 1 (VO) |
| Миграции | Ручные | Авто | 1 колонка | Ручные |
| Производительность | Высокая | Высокая | Средняя | Высокая |
| Гибкость (новое поле в VO) | ❌ (миграция) | ❌ (миграция) | ✅ (нет) | ❌ (миграция) |
| Сложность | Средняя | Низкая | Низкая | Низкая |

---

## Рекомендация

### Для 1–3 полей в VO (Money, Address, Coordinates)

**Flat columns** или **Embeddable**.

- Если VO имеет 2–3 скалярных поля — flat columns простейшие.
- Если VO имеет 4+ полей или вложенные структуры — Embeddable.

### Для сложного VO (5+ полей, вложенные объекты)

**Custom Type** (structured) или **JSON**.

- Custom Type — если нужны SQL-запросы по компонентам.
- JSON — если VO часто меняется и вы не хотите миграций.

### Для true immutable

**Pragmatic immutable** (вариант Б):

```php
// VO — immutable, validated in constructor
final readonly class Money { /* ... */ }

// Entity — mutable container, но public API только через VO
#[ORM\Entity]
final class Order {
    #[ORM\Embedded]
    private MoneyEmbedded $amount;

    public function updateAmount(Money $new): void { /* ... */ }
    public function getAmount(): Money { return $this->amount->toValueObject(); }
    // НЕТ: public int $amountCents;
}
```

Настоящий immutable entity (clone-on-write / remove+persist) — **непрактично** для Doctrine. Не стоит.

### Валидность

```
Конструктор VO → бросает при невалидном.
Entity → создаёт VO только через конструктор.
Type::convertToPHPValue → вызывает конструктор VO → бросает если БД сломана.
```

**Никогда** не давайте прямой доступ к «сырым» полям entity. Только через VO.

---

## Итого

| Вопрос | Ответ |
|---|---|
| Как хранить? | Flat columns (2–3 поля) / Embeddable (4+) / Custom Type (SQL-запросы) / JSON (частые изменения) |
| Как обеспечить иммутабельность? | VO — `readonly` + конструктор. Entity — mutable, но public API только через VO. |
| Как обеспечить валидность? | Конструктор VO бросает. Type / `toValueObject()` вызывает конструктор. Никогда не `new Money(0, '')` без проверки. |
| Какой способ лучший? | Зависит от кол-ва полей, SQL-запросов, частоты изменений VO. Нет универсального. |

> ⏱ 48.5s · prompt 38255 tok · output 6149 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата в Doctrine: корень + дочерние сущности, маппинг коллекций, границы транзакций, кто и когда вызывает flush. Нужен конкретный пример.

Выберу пример: Order (корень) + OrderLine (дочерние). Это классический случай, где:
- OrderLine не имеет самостоятельной жизни (не идентифицируется отдельно)
- Порядок в коллекциях важен
- Добавление/удаление/изменение строк — часть операции над Order
- Транзакция должна покрыть всю мутацию агрегата
- Flush — один раз, в конце операции, в Application Service

Структурирую ответ:
1. Модель домена (Order, OrderLine)
2. Маппинг Doctrine (OneToOne vs OneToMany, orphanRemoval, cascade, eager/lazy)
3. Репозиторий — только загрузка/сохранение агрегата целиком
4. Application Service — граница транзакции, вызов flush
5. Кто НЕ должен вызывать flush (репозиторий, сущность)
6. Подводные камни: lazy loading, cascade remove, orphanRemoval, Unit of Work
7. Антипаттерны

Буду конкретный, с кодом. Пример — Order + OrderLine.

</details>

**Кандидат:**

# Агрегат в Doctrine: Order + OrderLine

## Доменная модель

```php
// ─── VO ────────────────────────────────────────────────────────
final readonly class Money
{
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {
        if ($amountCents <= 0) {
            throw new DomainException('Amount must be positive');
        }
    }
}

final readonly class ProductId
{
    public function __construct(public readonly int $value) {}
}

// ─── Дочерняя сущность (НЕ самостоятельная) ────────────────────
final class OrderLine
{
    private int $id;
    private ProductId $product;
    private int    $quantity;
    private Money  $unitPrice;

    public function __construct(ProductId $product, int $quantity, Money $unitPrice)
    {
        if ($quantity <= 0) {
            throw new DomainException('Quantity must be positive');
        }

        $this->product   = $product;
        $this->quantity  = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function withQuantity(int $q): self
    {
        if ($q <= 0) throw new DomainException('Quantity must be positive');
        $this->quantity = $q;
        return $this;
    }

    public function withUnitPrice(Money $p): self
    {
        $this->unitPrice = $p;
        return $this;
    }

    public function lineTotal(): Money
    {
        return new Money($this->quantity * $this->unitPrice->amountCents, $this->unitPrice->currency);
    }

    // Getters...
    public function id(): int          { return $this->id; }
    public function product(): ProductId { return $this->product; }
    public function quantity(): int    { return $this->quantity; }
    public function unitPrice(): Money { return $this->unitPrice; }
}

// ─── Корень агрегата ───────────────────────────────────────────
final class Order
{
    private int $id;
    private CustomerId $customer;
    private OrderStatus $status;
    /** @var OrderLine[] */
    private array $lines = [];
    private \DateTimeImmutable $createdAt;

    private function __construct(CustomerId $customer)
    {
        $this->customer  = $customer;
        $this->status    = OrderStatus::Draft;
        $this->createdAt = new \DateTimeImmutable();
    }

    // ─── Мутации (единственный способ изменить агрегат) ────────
    public function addLine(ProductId $product, int $qty, Money $unitPrice): self
    {
        $this->assertEditable();
        $this->lines[] = new OrderLine($product, $qty, $unitPrice);
        return $this;
    }

    public function removeLine(int $lineId): self
    {
        $this->assertEditable();
        $this->lines = array_filter($this->lines, fn(OrderLine $l) => $l->id() !== $lineId);
        return $this;
    }

    public function updateLine(int $lineId, int $qty): self
    {
        $this->assertEditable();
        $this->findLine($lineId)->withQuantity($qty);
        return $this;
    }

    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new DomainException('Only draft orders can be confirmed');
        }
        if (empty($this->lines)) {
            throw new DomainException('Order has no lines');
        }
        $this->status = OrderStatus::Confirmed;
        return $this;
    }

    public function cancel(): self
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new DomainException('Delivered order cannot be cancelled');
        }
        $this->status = OrderStatus::Cancelled;
        return $this;
    }

    // ─── Чтение ─────────────────────────────────────────────────
    public function total(): Money
    {
        $sum = 0;
        $currency = null;
        foreach ($this->lines as $line) {
            $t = $line->lineTotal();
            if ($currency === null) $currency = $t->currency;
            if ($t->currency !== $currency) {
                throw new DomainException('Mixed currencies in order');
            }
            $sum += $t->amountCents;
        }
        return $sum > 0 ? new Money($sum, $currency) : new Money(0, 'USD');
    }

    // ─── Геттеры ────────────────────────────────────────────────
    public function id(): int           { return $this->id; }
    public function customer(): CustomerId { return $this->customer; }
    public function status(): OrderStatus { return $this->status; }
    /** @return OrderLine[] */
    public function lines(): array      { return $this->lines; }

    // ─── Вспомогательные ────────────────────────────────────────
    private function assertEditable(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new DomainException('Order is locked (status: ' . $this->status->value . ')');
        }
    }

    private function findLine(int $id): OrderLine
    {
        foreach ($this->lines as $line) {
            if ($line->id() === $id) return $line;
        }
        throw new DomainException("Line {$id} not found in order {$this->id}");
    }
}
```

Ключевой принцип: **все мутации идут через корень**. `OrderLine` не имеет публичного `setId()`, `setOrder()`. Внешний мир не может поставить «чужую» строку в чужой заказ.

---

## Маппинг Doctrine

### Выбор: `OneToOne` + `owning side` на OrderLine

Почему не `OneToMany` на Order?

| | `OneToMany` (Order → lines) | `OneToOne` (Line → order) |
|---|---|---|
| Направление | Order знает про lines | Line знает про order |
| `orphanRemoval` | Работает «из коробки» | Нужно на owning side |
| Lazy loading | Коллекция — `PersistentCollection` | Одно поле — проще |
| Удаление строки | `order->lines->remove($line)` | `em->remove($line)` |
| SQL при загрузке | 1 запрос (order) + 1 запрос (lines) | 1 запрос (order) + 1 запрос (lines) |

На практике для агрегата, где **всегда загружаем целиком**, разница минимальна. Но `OneToOne` + `orphanRemoval` даёт более чистую семантику: «линия принадлежит заказу, удаление заказа удаляет линии».

```php
// App/Entity/Order.php
#[ORM\Entity(repositoryClass: OrderEntityRepository::class)]
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $customerId;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    // Мост домен ↔ инфраструктура
    public function toDomain(): Order
    {
        $order = Order::createFromRepository(
            id:         $this->id,
            customer:   new CustomerId($this->customerId),
            status:     $this->status,
            createdAt:  $this->createdAt,
        );

        foreach ($this->lines as $line) {
            $order->attachLine(
                id:        $line->id,
                product:   new ProductId($line->productId),
                quantity:  $line->quantity,
                unitPrice: new Money($line->unitPriceCents, $line->currency),
            );
        }

        return $order;
    }

    public static function fromDomain(Order $order): self
    {
        $e = new self();
        $e->id         = $order->id();
        $e->customerId = $order->customer()->value;
        $e->status     = $order->status();
        $e->createdAt  = $order->createdAt();

        foreach ($order->lines() as $line) {
            $le = new OrderLineEntity();
            $le->productId      = $line->product()->value;
            $le->quantity       = $line->quantity();
            $le->unitPriceCents = $line->unitPrice()->amountCents;
            $le->currency       = $line->unitPrice()->currency;
            $le->order          = $e;   // ← owning side
            $e->lines[]         = $le;
        }

        return $e;
    }
}

// App/Entity/OrderLineEntity.php
#[ORM\Entity]
final class OrderLineEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    // Owning side: line → order (OneToOne, не OneToMany!)
    #[ORM\OneToOne(targetEntity: OrderEntity::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private OrderEntity $order;

    #[ORM\Column(type: Types::INTEGER)]
    private int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    #[ORM\Column(type: Types::INTEGER)]
    private int $unitPriceCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    // Setters — только для Doctrine (hydration / persist)
    public function setOrder(OrderEntity $o): void    { $this->order = $o; }
    // ... остальные setters для BDC
}
```

### Альтернатива: `OneToMany` + `orphanRemoval`

```php
// На OrderEntity:
#[ORM\OneToMany(
    mappedBy:  'order',
    cascade:   ['persist', 'remove'],
    orphanRemoval: true,
    fetch:     FetchMode::EAGER,   // ← агрегат всегда целиком
)]
#[ORM\OrderBy(['id' => 'ASC'])]
private Collection $lines;
```

`orphanRemoval: true` — если из `$order->lines` убрать элемент и вызвать `flush()`, Doctrine **удалит** его из БД. Это именно то, что нужно для `order->removeLine($id)`.

`cascade: ['persist']` — новый `OrderLineEntity` внутри `$order->lines` будет автоматически `persist`-нут при `flush()`. Не нужно отдельный `em->persist($line)`.

`fetch: EAGER` — при загрузке Order, Doctrine **сразу** подтягивает все lines. Для агрегата это правильно: вы не работаете с «половинчатым» заказом.

---

## Репозиторий: только загрузка и сохранение

```php
final class OrderEntityRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(OrderEntity $entity): void
    {
        $this->em->persist($entity);
        // НЕ flush(). Репозиторий не знает, когда завершена операция.
    }

    public function remove(OrderEntity $entity): void
    {
        $this->em->remove($entity);
        // НЕ flush().
    }

    public function find(int $id): ?OrderEntity
    {
        return $this->em->find(OrderEntity::class, $id);
        // Doctrine сам подтянет lines (EAGER).
    }
}
```

**Правило:** репозиторий **никогда** не вызывает `flush()`. Он `persist` / `remove` / `find`. Когда `flush` — решает вызывающий.

---

## Application Service: граница транзакции, вызов flush

```php
final class OrderApplicationService
{
    public function __construct(
        private EntityManagerInterface  $em,
        private OrderEntityRepository   $repo,
        private InventoryService        $inventory,   // внешний домен
        private EventDispatcher         $events,
    ) {}

    // ─── Сценарий 1: создать заказ со строками ──────────────────
    public function createOrder(CustomerId $customer, array $items): int
    {
        // $items: [ProductId, int qty, Money unitPrice]

        $order = new Order($customer);   // доменное создание
        foreach ($items as [$product, $qty, $price]) {
            $order->addLine($product, $qty, $price);
        }
        $order->confirm();               // доменная инвариантность

        // Перевод в инфраструктуру:
        $entity = OrderEntity::fromDomain($order);
        $this->repo->save($entity);      // persist (cascade → lines тоже)

        $this->em->flush();              // ← ОДИН flush. Все или ничего.
        $this->em->clear();              // ← сбрасываем UoW (см. ниже)

        $this->events->dispatch(new OrderCreatedEvent($entity->id));

        return $entity->id;
    }

    // ─── Сценарий 2: изменить строку ────────────────────────────
    public function updateLine(int $orderId, int $lineId, int $newQty): void
    {
        $entity = $this->repo->find($orderId)
            ?? throw new NotFoundHttpException("Order {$orderId} not found");

        $order = $entity->toDomain();
        $order->updateLine($lineId, $newQty);   // доменная валидация

        // Записываем обратно:
        $this->syncLines($entity, $order);

        $this->em->flush();
        $this->em->clear();
    }

    // ─── Сценарий 3: удалить строку ─────────────────────────────
    public function removeLine(int $orderId, int $lineId): void
    {
        $entity = $this->repo->find($orderId)
            ?? throw new NotFoundHttpException("Order {$orderId} not found");

        $order = $entity->toDomain();
        $order->removeLine($lineId);   // доменная валидация

        $this->syncLines($entity, $order);

        $this->em->flush();
        $this->em->clear();
    }

    // ─── Сценарий 4: подтвердить заказ ──────────────────────────
    public function confirmOrder(int $orderId): void
    {
        $entity = $this->repo->find($orderId)
            ?? throw new NotFoundHttpException("Order {$orderId} not found");

        $order = $entity->toDomain();

        // Внешний домен: проверяем наличие на складе.
        // Если нет — бросаем ДО flush. Ничего не сохранено.
        foreach ($order->lines() as $line) {
            $this->inventory->reserve($line->product(), $line->quantity());
        }

        $order->confirm();   // доменная мутация

        $entity->setStatus($order->status());

        $this->em->flush();
        $this->em->clear();

        $this->events->dispatch(new OrderConfirmedEvent($orderId));
    }

    // ─── Вспомогательный: синхронизация коллекций ───────────────
    private function syncLines(OrderEntity $entity, Order $domain): void
    {
        // Удалить строки, которых нет в домене:
        $domainLineIds = array_map(fn(OrderLine $l) => $l->id(), $domain->lines());

        foreach ($entity->lines as $lineEntity) {
            if (!in_array($lineEntity->id, $domainLineIds, true)) {
                $entity->lines->removeElement($lineEntity);
                $this->em->remove($lineEntity);
            }
        }

        // Обновить существующие:
        foreach ($domain->lines() as $line) {
            $existing = $entity->lines->get(
                array_search($line->id(), array_column($entity->lines->toArray(), 'id'), true)
            );
            if ($existing) {
                $existing->quantity       = $line->quantity();
                $existing->unitPriceCents = $line->unitPrice()->amountCents;
                $existing->currency       = $line->unitPrice()->currency;
            } else {
                // Новая строка:
                $new = new OrderLineEntity();
                $new->productId      = $line->product()->value;
                $new->quantity       = $line->quantity();
                $new->unitPriceCents = $line->unitPrice()->amountCents;
                $new->currency       = $line->unitPrice()->currency;
                $new->setOrder($entity);
                $entity->lines->add($new);
                $this->em->persist($new);
            }
        }
    }
}
```

---

## Кто и когда вызывает `flush()`

```
┌─────────────────────────────────────────────────────────────────────────┐
│  Контроллер / Command Handler                                           │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │  ApplicationService::createOrder()                                │  │
│  │    1. Доменные операции (addLine, confirm) — БЕЗ БД              │  │
│  │    2. persist(entity)       — в UoW, НЕ в БД                     │  │
│  │    3. persist(line1)        — cascade, в UoW                     │  │
│  │    4. persist(line2)        — cascade, в UoW                     │  │
│  │    5. em->flush()           — ← ЕДИНСТВЕННАЯ точка записи в БД   │  │
│  │       → BEGIN TRANSACTION                                         │  │
│  │       → INSERT orders (...)                                       │  │
│  │       → INSERT order_lines (...)  ×2                              │  │
│  │       → COMMIT                                                    │  │
│  │    6. em->clear()          — сброс UoW                           │  │
│  └───────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
```

### Правила

| Кто | Может `flush()`? | Почему |
|---|---|---|
| Доменная сущность | **Нет** | Не знает про БД. Не имеет `EntityManager`. |
| Репозиторий | **Нет** | Не знает, когда «готово». `persist`/`remove` — да, `flush` — нет. |
| Application Service | **Да** | Знaет полную операцию. Граница транзакции. |
| Контроллер | **Нет** | Не знает про ORM. Вызывает service. |
| Event listener | **Осторожно** | Если listener делает доменную работу — да. Если просто логирует — нет. |

### Почему один `flush()` на операцию

```php
// ❌ ПЛОХО: несколько flush
$entity = OrderEntity::fromDomain($order);
$repo->save($entity);
$em->flush();   // ← INSERT order + lines

$entity->setStatus(OrderStatus::Confirmed);
$em->flush();   // ← UPDATE order (лишний round-trip)

// ✅ НОРМАЛЬНО: один flush
$entity = OrderEntity::fromDomain($order);
$repo->save($entity);
$entity->setStatus(OrderStatus::Confirmed);
$em->flush();   // ← INSERT + UPDATE одним транзакционным блоком
```

Каждый лишний `flush()` — лишний `BEGIN...COMMIT`, лишняя нагрузка на БД, лишняя точка, где может «развалиться» транзакция.

---

## `em->clear()` — зачем и когда

```php
$this->em->flush();
$this->em->clear();   // ← сбрасывает Unit of Work
```

### Зачем

После `flush()` все объекты в UoW **загрязнены** (dirty). Если вы продолжите работать с тем же `$em`, Doctrine будет отслеживать их дальше. В длинном request (Symfony: 1 request = 1 UoW) это приводит к:

- Мемори: старые объекты не освобождаются.
- Ложные `UPDATE`: объект изменён «не вами» → Doctrine увидит diff → `UPDATE`.
- Конфликты: два разных сервиса используют один `$em` и «перешивают» друг друга.

### Когда

```php
// После каждой «логической операции» в сервисе:
$em->flush();
$em->clear();

// Если в одном request есть 2 операции:
$em->clear();   // перед второй
$entity = $repo->find($id);
// ...
$em->flush();
$em->clear();
```

### Когда НЕ нужно

- Если вы сразу после `flush()` выходите из request (типичный контроллер → service → flush → response). Symfony сам делает `clear()` в `kernel.terminate`.
- Если `flush()` — последний вызов `$em` в request.

---

## Транзакции: явные vs неявные

### Неявная (по умолчанию в Doctrine)

```php
$em->persist($entity);
$em->flush();   // → BEGIN, INSERT..., COMMIT (в одном вызове)
```

Один `flush()` = одна транзакция. Просто, но нет контроля над isolation level.

### Явная

```php
$conn = $em->getConnection();
$conn->beginTransaction();

try {
    // Доменные операции
    $order = new Order($customer);
    $order->addLine($product, 2, $price);
    $order->confirm();

    $entity = OrderEntity::fromDomain($order);
    $em->persist($entity);
    $em->flush();   // ← внутри уже транзакция, но мы управляем COMMIT

    // Внешний вызов (НЕ в БД):
    $this->inventory->reserve($product, 2);

    $conn->commit();
} catch (\Throwable $e) {
    $conn->rollBack();
    throw $e;
}
```

**Важно:** `flush()` внутри явной транзакции **не** делает `COMMIT` сам, если вы использовали `$conn->beginTransaction()`. Он делает `INSERT/UPDATE` в рамках текущей транзакции. `COMMIT` — только ваш `$conn->commit()`.

### Когда явная транзакция нужна

| Сценарий | Почему |
|---|---|
| `flush()` + внешний HTTP-вызов + `flush()` | Нужно, чтобы оба `INSERT` были атомарны. Если внешний вызов упадёт между двумя `flush` — первая часть записана, вторая нет. |
| Несколько `flush()` в одной операции | Один `COMMIT` в конце. |
| Контроль isolation level (`SERIALIZABLE`) | `$conn->beginTransaction(SerializationLevel::SERIALIZABLE)` |
| Long-running operation | Явный `COMMIT` через 5 секунд, а не «когда Doctrine решит» |

### Когда НЕ нужна

- Один `flush()` на операцию (90% случаев).
- Нет внешних вызовов между `persist` и `flush`.

---

## Подводные камни

### 1. `EAGER` + N+1

```php
#[ORM\OneToMany(fetch: FetchMode::EAGER)]
private Collection $lines;
```

Загрузка 100 заказов → 101 запрос (1 + 100). Если `lines` — агрегат, это **правильно**: вы не работаете с заказом без строк. Но если у вас 1000 заказов на странице — 1001 запрос будет больно.

**Решение:**
- `EAGER` + `JOIN` (Doctrine делает сам при EAGER).
- Или `LAZY` + явное `initializeCollection` в сервисе:

```php
$order = $repo->find($id);
// lines ещё не загружены (LAZY)
$collection = $order->lines;   // ← здесь 1 запрос на lines
```

Для агрегата `EAGER` — норм. Для «списка заказов без строк» — `LAZY` + не трогать `lines`.

### 2. `orphanRemoval` + `cascade remove`

```php
#[ORM\OneToMany(
    cascade: ['persist', 'remove'],
    orphanRemoval: true
)]
```

`orphanRemoval` **включает** `cascade remove` автоматически. Не нужно писать оба.

Но: `orphanRemoval` срабатывает только если вы **убрали элемент из коллекции** и вызвали `flush()`. Если вы `em->remove($line)` вручную — `orphanRemoval` не нужен.

```php
// Вариант А: orphanRemoval
$entity->lines->removeElement($lineEntity);
$em->flush();   // → DELETE FROM order_lines WHERE id = ...

// Вариант Б: явный remove
$em->remove($lineEntity);
$em->flush();   // → DELETE FROM order_lines WHERE id = ...
```

Оба работают. `orphanRemoval` — «чистее» семантически: «это моя коллекция, я решаю, что в ней».

### 3. `em->clear()` теряет изменения

```php
$order = $repo->find(1);
$order->confirm();       // ← dirty в UoW
$em->clear();            // ← UoW сброшен, изменения ПОТЕРЯНЫ
$em->flush();            // ← ничего не сохранит. $order больше не managed.
```

**Правило:** `flush()` ДО `clear()`. Никогда наоборот.

### 4. Два сервиса, один `$em`

```php
// Service A:
$order = $repo->find(1);
$order->addLine(...);
// ещё не flush

// Service B (вызван из A):
$customer = $customerRepo->find($order->customer());
$customer->incrementOrdersCount();
$em->flush();   // ← сохранит И order (dirty от A), И customer

// Это НЕ баг. Это Unit of Work. Все managed-объекты в одном UoW.
// Но это значит: Service A должен знать, что flush «уходит» через B.
```

**Решение:** `em->clear()` между логическими блоками. Или отдельные `EntityManager` (редко нужно).

### 5. `persist` нового объекта с `EAGER`-коллекцией

```php
$entity = OrderEntity::fromDomain($order);
// $entity->lines — Collection, элементы — новые OrderLineEntity

$em->persist($entity);
// cascade: ['persist'] → Doctrine рекурсивно persist-ит все lines
$em->flush();
// INSERT orders + INSERT order_lines × N
```

Работает. Но если в `lines` есть **managed** объект (загружен ранее из БД) — `cascade persist` его **не тронет** (он уже managed). `cascade remove` — **удалит** orphan.

### 6. `findOneBy` vs `find`

```php
// find: сначала в UoW (identity map), потом в БД
$entity = $em->find(OrderEntity::class, 42);

// findOneBy: ВТОРОЙ запрос в БД, игнорирует UoW
$entity = $em->getRepository(OrderEntity::class)->findOneBy(['id' => 42]);
```

Для агрегата: `find` (если знаете ID). `findOneBy` — если не знаете ID (по composite key). `find` быстрее: hit в identity map = 0 запросов.

---

## Итого: структура

```
Контроллер
  └─► ApplicationService
        ├─► Доменные операции (Order, OrderLine) — чистый PHP, нет БД
        ├─► OrderEntity::fromDomain() — перевод
        ├─► Repository::save() — persist (не flush)
        ├─► em->flush() — ЕДИНСТВЕННАЯ точка записи
        ├─► em->clear() — сброс UoW
        └─► Events — после flush (не до)

Репозиторий
  └─► persist / remove / find
  └─► НИКОГДА не flush

Сущность
  └─► мутации (addLine, confirm, cancel)
  └─► НИКОГДА не persist / flush / em
```

Агрегат загружается **целиком** (корень + все children). Модифицируется **только через корень**. Сохраняется **одним flush**. Это и есть «граница транзакции = граница агрегата».

> ⏱ 53.6s · prompt 44216 tok · output 7086 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о диагностике и решении N+1 запросов в Doctrine. Это практичный вопрос — нужно показать реальный процесс диагностики, а не просто перечислить паттерны. Структурирую ответ так:

1. Как диагностировать — конкретные инструменты и шаги
2. Как выглядит N+1 на уровне SQL и PHP-кода
3. Способы решения — каждый с trade-offs:
   - EAGER fetching (JOIN)
   - DQL JOIN
   - Entity Graphs
   - Partial Objects (select-список)
   - Custom DBAL query (native SQL)
   - Denormalization
   - Batch loading
   - Refactoring: перенос логики в SQL (DQL)
   - Collection-загрузка отдельным запросом (explicit)
   - Валидация: когда N+1 НЕ проблема

Нужно быть конкретным, с кодом, с trade-offs. Не абстрактно, а «вот как это выглядит в вашем Symfony-проекте».

Язык — русский, как и вся беседа.

</details>

**Кандидат:**

# N+1 в Doctrine: диагностика и лечение

## Что такое N+1 на практике

```
SELECT * FROM orders WHERE status = 'confirmed' ORDER BY created_at DESC;
→ 50 строк

SELECT * FROM order_lines WHERE order_id = 1;
SELECT * FROM order_lines WHERE order_id = 2;
SELECT * FROM order_lines WHERE order_id = 3;
...
SELECT * FROM order_lines WHERE order_id = 50;
→ 50 запросов

Итого: 51 запрос. N = 50, +1 = первый.
```

При 50 записях — 51 запрос, 51 round-trip к БД. При 1000 записях — 1001 запрос. Каждый round-trip на LAN ~0.3 мс, на RDS (50 мс до БД) — 300 мс только на задержки. Страница, которая должна отрисоваться за 200 мс, занимает 5 секунд.

Ключевое: **N+1 — это не «много запросов»**. Это «много запросов, которые можно было объединить в один». Разница между 51 запросом по одному и 2 запросами (1 + 1 IN) — не в количестве, а в round-trips и в том, что БД не может оптимизировать 50 отдельных запросов как один.

---

## Диагностика: где и сколько

### Шаг 1: Подсчёт запросов

```bash
# Symfony + Doctrine: включить SQL-логирование
# config/packages/doctrine.yaml (dev)
doctrine:
    dbal:
        logging: true
```

```yaml
# config/packages/framework.yaml (dev)
framework:
    profiler:
        collect: true
```

Открываете страницу → Web Profiler → таб «Doctrine» → видите список всех SQL-запросов с временами.

Или без Profiler:

```bash
# В CLI:
php bin/console debug:router  # нет, это не то

# Проще: временный логгер
# src/EventListener/SqlLogger.php
final class SqlLogger
{
    public function __construct(private OutputInterface $output) {}

    public function logQuery(string $sql, array $params, float $timeMs): void
    {
        $this->output->writeln(sprintf('[%.1f ms] %s', $timeMs, $sql));
    }
}
```

```yaml
# services.yaml (dev)
Doctrine\DBAL\Logging\SQLLogger:
    class: App\EventListener\SqlLogger
```

### Шаг 2: Найти паттерн

В Profiler ищете:

- Один запрос `SELECT ... FROM orders` (N=50).
- За ним 50 запросов `SELECT ... FROM order_lines WHERE order_id = ?`.
- Каждый ~1–3 мс.
- Суммарно: 50 × 2 мс = 100 мс (локально), 50 × 15 мс = 750 мс (RDS).

**Признак N+1 в Profiler:** одно и то же SQL-запрос с разными параметрами, повторяющееся N раз подряд.

### Шаг 3: Найти источник в PHP-коде

```php
// Где-то в контроллере или сервисе:
$orders = $this->orderRepo->findBy(['status' => 'confirmed']);

foreach ($orders as $order) {
    $lines = $order->getLines();   // ← ВОТ ТУТ. Lazy loading → 1 запрос на итерацию
    $total = 0;
    foreach ($lines as $line) {
        $total += $line->getQuantity() * $line->getUnitPrice();
    }
    // ...
}
```

`$order->getLines()` — вызов на `PersistentCollection`. Первый доступ → Doctrine делает `SELECT ... WHERE order_id = ?`. Второй объект → второй запрос. И так N раз.

**Второй типичный источник:**

```php
// Полиморфизм:
$events = $this->eventRepo->findBy(['type' => 'order.*']);
foreach ($events as $event) {
    $order = $event->getOrder();   // ← LAZY: 1 запрос на итерацию
}
```

### Шаг 4: Проверить, что это именно N+1, а не просто много данных

| | N+1 | Просто много данных |
|---|---|---|
| Паттерн в SQL | Один запрос + N одинаковых | N разных (или 1 большой) |
| Время на запрос | 1–3 мс каждый | 50–200 мс |
| Суммарное время | N × 2 мс = 100 мс (50 запросов) | 1 × 100 мс = 100 мс (1 запрос) |
| Решение | Объединить в 1–2 запроса | Оптимизировать 1 запрос (индекс, покрывающий) |

Если 50 запросов по 2 мс каждый — N+1. Если 1 запрос на 500 мс — это «много данных», лечится индексом/покрывающим.

---

## Способы решения (от «быстро» к «правильно»)

### 1. EAGER fetching (JOIN в маппинге)

```php
#[ORM\Entity]
final class OrderEntity
{
    #[ORM\OneToMany(
        mappedBy: 'order',
        fetch:    FetchMode::EAGER,   // ← было LAZY
        joinColumns: [
            #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
        ]
    )]
    private Collection $lines;
}
```

**Что делает:** при `find()` / `findBy()` Doctrine делает `SELECT o.*, l.* FROM orders o LEFT JOIN order_lines l ON l.order_id = o.id` — один запрос, все данные.

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Ноль кода, одно изменение в маппинге | **Всегда** тянет lines, даже если вам не нужны. Страница «список заказов» без деталей → тянет 50 × 10 lines = 500 лишних строк. |
| Работает для `find()` и `findBy()` | Не работает для `DQL` / `QueryBuilder` (см. ниже). |
| Проще всего | При `EAGER` на `ManyToOne` (order → customer) → JOIN на customers тоже. Три EAGER'а → три JOIN'а → один огромный SELECT. |
| | Мемори: 50 orders × 10 lines × 200 байт = 100 КБ в памяти. При 1000 orders — 2 МБ. При 10 000 — 20 МБ. |
| | **Не масштабируется на длинные списки.** Страница из 100 заказов с 20 lines каждый → 2000 строк в одном SELECT. |

**Когда ок:** агрегат небольшой (≤ 5 children), список короткий (≤ 50 элементов), children всегда нужны.

**Когда нет:** длинные списки, дети большие, или дети нужны не всегда.

---

### 2. DQL JOIN

```php
// Вместо:
$orders = $em->getRepository(OrderEntity::class)
    ->findBy(['status' => 'confirmed'], ['createdAt' => 'DESC'], 50);
foreach ($orders as $order) {
    $lines = $order->getLines();  // ← N+1
}

// Через QueryBuilder:
$qb = $em->createQueryBuilder()
    ->select('o', 'l')            // ← и о, и l
    ->from(OrderEntity::class, 'o')
    ->leftJoin('o.lines', 'l')   // ← JOIN
    ->where('o.status = :status')
    ->setParameter('status', 'confirmed')
    ->orderBy('o.createdAt', 'DESC')
    ->setMaxResults(50);

$orders = $qb->getQuery()->getResult();
// ← 1 запрос. lines уже загружены. $order->getLines() → 0 запросов.
```

SQL:

```sql
SELECT o.id, o.customer_id, o.status, l.id AS l_id, l.quantity, ...
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
WHERE o.status = 'confirmed'
ORDER BY o.created_at DESC
LIMIT 50;
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Точный контроль: JOIN именно там, где нужен | Повторяется в каждом репозитории/сервисе, где нужен `lines`. |
| Можно `select('o')` без `l` → без JOIN → без лишних данных | При 50 orders × 10 lines = 500 строк в результате. Doctrine дедаупликация по `o.id` → 50 объектов. Мемори: ок. |
| `LEFT JOIN` — заказы без lines тоже вернутся | Если `lines` — `OneToMany` с `orphanRemoval` → `LEFT JOIN` корректен. Если `OneToOne` → тоже. |
| Работает с `DQL` и `QueryBuilder` | Не работает с `find()` / `findBy()` (они не принимают JOIN). |
| | При глубокой вложенности (order → lines → product → category) → 3–4 JOIN'а → SQL становится нечитаемым. |

**Когда ок:** 1–2 уровня вложенности, список ≤ 200 элементов.

**Когда нет:** 3+ уровня вложенности, очень длинные списки, или данные в children не нужны для 90% случаев.

---

### 3. Entity Graphs (гибрид: LAZY + выборочный EAGER)

```php
$root = new RootEntityGraph(OrderEntity::class);
$root->addFields(['id', 'status', 'createdAt', 'customerId']);

$lines = new EntityGraph(OrderLineEntity::class);
$lines->addFields(['id', 'quantity', 'unitPriceCents', 'currency', 'productId']);
$root->addAssociation('lines', $lines);

// Не тянем product, customer, audit-поля.
$query = $em->createQuery(
    'SELECT o FROM OrderEntity o WHERE o.status = :s'
)
    ->setParameter('s', 'confirmed')
    ->setMaxResults(50)
    ->setResultCacheProfile(new ResultCacheProfile($cache, 300, 'orders_confirmed'));
    // ← Результат кэшируется на 5 минут. Второй запрос — 0 SQL.

$orders = $query->addEntityGraph($root)->getResult();
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Точный выбор полей: не тянет `product.description` (TEXT, 2 КБ) | Конфигурация графа — ещё один объект, который нужно поддерживать. |
| Меньше данных → меньше памяти → быстрее сериализация | При изменении схемы → нужно править graph. Легко забыть. |
| Совместим с DQL | Не работает с `find()` / `findBy()` (только DQL/QueryBuilder). |
| Можно кэшировать результат | Кэш: при изменении данных → инвалидация. Сложность. |

**Когда ок:** данные в children большие (TEXT, JSON), список длинный (500+), или children нужны частично.

**Когда нет:** схема часто меняется, или graph-конфигурация «забывается».

---

### 4. Partial Objects (select-список)

```php
$qb = $em->createQueryBuilder()
    ->select(
        'o.id',
        'o.status',
        'o.createdAt',
        'l.id AS lineId',
        'l.quantity',
        'l.unitPriceCents',
        'l.currency',
        'l.productId'
        // ← НЕ 'o.customer', НЕ 'l.product.description'
    )
    ->from(OrderEntity::class, 'o')
    ->leftJoin('o.lines', 'l')
    ->where('o.status = :status')
    ->setParameter('status', 'confirmed')
    ->setMaxResults(50);

// Результат: массивы, НЕ объекты.
$rows = $qb->getQuery()->getResult(Query::ARRAY_RESULT);
// [
//   ['id' => 1, 'status' => 'confirmed', 'lineId' => 10, 'quantity' => 2, ...],
//   ['id' => 1, 'status' => 'confirmed', 'lineId' => 11, 'quantity' => 1, ...],
//   ['id' => 2, 'status' => 'confirmed', 'lineId' => 20, 'quantity' => 3, ...],
// ]
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Минимальные данные: только то, что нужно для рендера | **Не объекты.** Нет доменных методов (`$order->total()`). Только данные. |
| Максимальная скорость: нет hydration, нет identity map | Дедупликация: один order с 5 lines → 5 строк в массиве. Нужно группировать вручную. |
| Идеально для API / JSON-ответа | При 500 строк → `array_map` / `group_by` в PHP. |
| Не зависит от маппинга (можно `SELECT` из raw SQL) | Нет lazy loading. Если забудете поле → `Undefined index`. |
| | Нет change tracking. Нельзя «изменить» и `flush()`. Только чтение. |

**Когда ок:** только чтение, API, дашборд, рендер списка. Не нужно мутировать.

**Когда нет:** нужно мутировать объекты, или доменная логика (`$order->confirm()`).

---

### 5. Явная загрузка коллекции отдельным запросом (batch)

```php
// 1. Загружаем заказы (без lines):
$orders = $em->getRepository(OrderEntity::class)
    ->findBy(['status' => 'confirmed'], ['createdAt' => 'DESC'], 50);
// 1 запрос. lines — LAZY, не загружены.

// 2. Явно загружаем все lines одним запросом:
$ids = array_map(fn($o) => $o->id, $orders);

$lines = $em->createQuery(
    'SELECT l FROM OrderLineEntity l WHERE l.order IN (:ids)'
)
    ->setParameter('ids', $ids)
    ->getResult();
// 1 запрос. Все lines сразу.

// 3. Размещаем:
$linesByOrder = [];
foreach ($lines as $line) {
    $linesByOrder[$line->getOrder()->id][] = $line;
}

foreach ($orders as $order) {
    $order->setLines($linesByOrder[$order->id] ?? []);
}
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 2 запроса вместо 51 | `IN (:ids)` с 50 элементами → ок. С 5000 → медленный (БД не оптимизирует). |
| Гибкость: можно `WHERE l.quantity > 1` (только нужные lines) | Ручное управление: нужно «поставить» lines в orders. |
| Нет JOIN'а → SQL проще → БД лучше оптимизирует | При 5000 orders → `IN (5000 ids)` → нужен batch (500 по 10 запросов). |
| | Не масштабируется: 10 000 orders → `IN (10000)` → БД может отказать. |

**Когда ок:** 10–500 элементов, lines нужны для всех.

**Когда нет:** > 5000 элементов, или lines нужны не для всех заказов.

---

### 6. Denormalization (покрывающий запрос)

```php
// В БД: колонка total_cents в orders (обновляется при каждом изменении lines)
// При чтении списка:
SELECT id, status, total_cents, created_at
FROM orders
WHERE status = 'confirmed'
ORDER BY created_at DESC
LIMIT 50;
-- 1 запрос. Ноль JOIN. Ноль N+1.
```

```php
// При изменении:
public function addLine(ProductId $product, int $qty, Money $price): self
{
    $this->lines[] = new OrderLine($product, $qty, $price);
    $this->recalculateTotal();   // ← обновление total_cents
    return $this;
}
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| **Один запрос. Всегда.** Не зависит от кол-ва элементов. | Денормализация: `total_cents` должен быть актуален. При каждом изменении lines → пересчёт. |
| Мемори: 50 строк × 200 байт = 10 КБ. | Если пересчёт в PHP (не триггер) → риск рассинхрона. |
| Идеально для списков, API, дашбордов. | При 100 000 orders → `SELECT ... LIMIT 50` всё равно 1 запрос. Но `ORDER BY created_at DESC` нужен индекс. |
| | Если нужно «сколько всего заказов на сумму > 1000» → `WHERE total_cents > 1000` → работает. Но если `total_cents` устарел → неверный результат. |

**Когда ок:** список / дашборд / API, где нужны агрегаты, а не детализация.

**Когда нет:** нужна детальная работа с lines (модификация, проверка инвариантов).

---

### 7. Валидация: N+1 НЕ проблема

```php
// Страница «детали заказа»: 1 заказ, 15 lines.
$orders = $repo->find(42);
$lines  = $orders->getLines();   // 1 запрос

// N = 1. +1 = 1. Итого 2 запроса.
// Это НЕ N+1. Это 2 запроса. Нормально.
```

| Ситуация | N+1? | Решение? |
|---|---|---|
| 1 заказ, 15 lines | Нет. 2 запроса. | Не нужно. |
| 50 заказов, 10 lines каждый | Да. 51 запрос. | JOIN / EAGER / Partial. |
| 50 заказов, lines нужны для 3 из 50 | Частично. 51 запрос, 47 лишних. | Batch + фильтр: `WHERE order_id IN (1,2,3)`. |
| 5000 заказов, 5 lines каждый | Да. 5001 запрос. | Denormalization / pagination / Partial. |
| 1 запрос на 200 мс (индекс отсутствует) | Нет. 1 запрос. | Индекс. |

**Критерий:** N+1 — это когда **один и тот же SQL** повторяется N раз с разными параметрами. Если каждый запрос уникален (разные `WHERE`) — это не N+1, это «много запросов». Лечится иначе.

---

## Сравнение

| Способ | Запросы | Мемори | Масштабируемость | Сложность | Когда |
|---|---|---|---|---|---|
| EAGER | 1 (JOIN) | Высокая (все children) | ≤ 50 элементов | Минимальная | Агрегат малый, всегда нужен |
| DQL JOIN | 1 (JOIN) | Средняя | ≤ 500 элементов | Низкая | 1–2 уровня, список средний |
| Entity Graph | 1 (JOIN, partial) | Низкая (только нужные поля) | ≤ 1000 элементов | Средняя | Данные в children большие |
| Partial Objects | 1 (JOIN, array) | Минимальная | ≤ 5000 элементов | Средняя | Только чтение, API |
| Batch `IN` | 2 | Средняя | ≤ 500 элементов | Низкая | Lines нужны для всех |
| Denormalization | 1 (без JOIN) | Минимальная | Бесконечно | Высокая (инвариант) | Список / дашборд / агрегаты |
| Не лечить | N+1 | — | — | — | N ≤ 5, не критично |

---

## Процесс решения

```
1. Profiler: сколько запросов? Какой паттерн?
   → 51 запрос, 50 одинаковых → N+1.
   → 50 разных → не N+1, искать индекс.

2. N ≤ 5?
   → Не лечить. 6 запросов × 2 мс = 12 мс. Не больно.

3. N > 50?
   → Лечить.

4. Данные в children нужны ВСЕГДА и для ВСЕХ?
   → DQL JOIN или EAGER.

5. Данные в children нужны ЧАСТИЧНО?
   → Entity Graph или Partial Objects.

6. Список длинный (> 500)?
   → Denormalization (total_cents, latest_line, etc.)
   → Или pagination (20 на страницу → N = 20 → 21 запрос → ок).

7. Только чтение (API / дашборд)?
   → Partial Objects. Минимум данных, максимум скорость.

8. Нужно мутировать?
   → DQL JOIN + доменные объекты.
```

---

## Частые ошибки при лечении

### ❌ EAGER на всё подряд

```php
#[ORM\ManyToOne(fetch: FetchMode::EAGER)]
private CustomerEntity $customer;

#[ORM\OneToMany(fetch: FetchMode::EAGER)]
private Collection $lines;

#[ORM\OneToMany(fetch: FetchMode::EAGER)]
private Collection $auditLog;
```

Один `find()` → JOIN на `customers` + `order_lines` + `audit_log` → один огромный SELECT на 50 заказов → 50 × (1 customer + 10 lines + 5 audit) = 800 строк. Мемори: 800 × 500 байт = 400 КБ. При 1000 заказов → 8 МБ. FPM-процесс падает.

### ❌ `IN (:ids)` с 10 000 элементов

```php
$ids = [1, 2, 3, ..., 10000];
$qb->where('l.order IN (:ids)')->setParameter('ids', $ids);
// MySQL: 10 000 элементов в IN → медленный parse, медленный execution.
// PostgreSQL: лучше, но всё равно не оптимален.
// Решение: batch по 500.
```

### ❌ «Исправил» N+1, но сломал change tracking

```php
// Partial Objects:
$rows = $qb->getQuery()->getResult(Query::ARRAY_RESULT);
// ← массивы. Нет объектов.
// $order->addLine(...) → Fatal: не объект, не доменные методы.
```

Partial Objects — только чтение. Если нужно мутировать → DQL JOIN + объекты.

### ❌ Кэш без инвалидации

```php
->setResultCacheProfile(new ResultCacheProfile($cache, 3600, 'orders'))
// Кэш на 1 час.
// Кто-то добавил line → total_cents изменился.
// Кэш не инвалидирован → старый результат 1 час.
```

Кэш: инвалидация при `flush()`, или TTL ≤ 30 сек для «живых» данных.

---

## Итого

N+1 лечится **не одним способом**. Выбор зависит от:

- **N** (5 → не лечить, 50 → JOIN, 5000 → denormalization + pagination)
- **Размер children** (5 полей → JOIN, 50 полей с TEXT → Entity Graph / Partial)
- **Чтение или запись** (чтение → Partial / denorm, запись → JOIN + объекты)
- **Длина списка** (50 → JOIN, 500 → denorm, 5000 → pagination + denorm)

Диагностика — Profiler + подсчёт SQL. Лечение — от «быстро» (EAGER) к «правильно» (denormalization + pagination).

> ⏱ 50.5s · prompt 51059 tok · output 5891 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking about why database-level integrity constraints in PostgreSQL are needed when business validation is already done in application code. They want to know what tools PostgreSQL provides and when each is appropriate.

This is a great question that gets at the "defense in depth" philosophy. Let me think about this carefully.

Key points to cover:
1. Why DB constraints are still needed even with app validation (multiple writers, races, direct DB access, bugs, migrations)
2. The different levels of protection they provide
3. Specific PostgreSQL tools: NOT NULL, UNIQUE, CHECK, FOREIGN KEY, EXCLUDE, domain types, partial indexes
4. When each is appropriate
5. Trade-offs: performance, complexity, error handling
6. The relationship between app validation and DB constraints (they're different layers solving different problems)

Let me structure this clearly with practical examples. I should be honest about trade-offs — CHECK constraints have performance costs, FKs can be expensive under high concurrency, etc.

I'll write in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Ограничения целостности в PostgreSQL: зачем, если уже есть валидация в коде

## Короткий ответ

Приложение-валидация отвечает на вопрос: *«Это действие допустимо **сейчас**, в **этом** контексте, для **этого** пользователя?»*

База данных отвечает на вопрос: *«Это состояние допустимо **вообще**, **всегда**, для **любого**写入?»*

Это разные вопросы. Валидация в коде — это **политика**. Ограничение в БД — это **физика**. Вы не отменяете гравитацию, потому что «я уже проверил, что предмет не упадёт».

---

## Почему кодовой валидации недостаточно

### 1. Несколько писателей

```
Процесс A: SELECT balance FROM accounts WHERE id = 1;  → 100
Процесс B: SELECT balance FROM accounts WHERE id = 1;  → 100
Процесс A: UPDATE accounts SET balance = 50 WHERE id = 1;  (списал 50)
Процесс B: UPDATE accounts SET balance = 50 WHERE id = 1;  (списал 50, думая что было 100)

→ balance = 50, а должно быть 0. Двойное списание.
```

Кодовая валидация (`if ($balance < $amount) throw`) выполнена **до** `UPDATE`. Между `SELECT` и `UPDATE` — окно, в которое влезает другой процесс. `CHECK (balance >= 0)` на уровне БД — последняя линия. Не идеальная (см. ниже про race), но **последняя**.

### 2. Прямой доступ к БД

```bash
# DBA делает миграцию и «вручную» исправляет данные:
psql production -c "UPDATE orders SET status = 'refunded' WHERE id = 4521;"

# или:
psql production -c "INSERT INTO order_lines (order_id, quantity) VALUES (9999, -5);"
```

Код приложения не участвует. `CHECK (quantity > 0)` — единственный барьер.

### 3. Баг в коде

```php
// В 80% случаев валидация есть. В 20% — забыли.
$line = new OrderLine($product, $qty, $price);
// Забыли: if ($qty <= 0) throw ...
$em->persist($line);
$em->flush();
// → quantity = -5 в БД.
```

`CHECK (quantity > 0)` ловит. Не спасает от логики, но ловит **явные** нарушения инварианта.

### 4. Миграции и backfill

```sql
-- Миграция: добавляем колонку с default
ALTER TABLE orders ADD COLUMN total_cents INT DEFAULT 0;

-- Backfill (отдельный скрипт):
UPDATE orders SET total_cents = (SELECT SUM(qty * price) FROM order_lines WHERE order_id = orders.id);

-- Если в order_lines есть строка с qty = 0 (баг) → total_cents = 0.
-- CHECK (total_cents > 0) → миграция ПАДАЕТ.
-- Это хорошо: вы узнали о баге ДО того, как он попал в прод.
```

### 5. Асинхронные писатели

```
Worker 1 (PHP):     INSERT INTO orders ...
Worker 2 (Kafka consumer): INSERT INTO orders ...
Worker 3 (Cron):    UPDATE orders SET ...
Worker 4 (Admin CLI): DELETE FROM orders WHERE ...

→ 4 независимых процесса. Кодовая валидация — в каждом отдельно.
→ БД-ограничение — одна для всех.
```

---

## Что PostgreSQL даёт

### 1. `NOT NULL`

```sql
ALTER TABLE orders ADD CONSTRAINT orders_status_not_null
    CHECK (status IS NOT NULL);
-- Или просто:
ALTER TABLE orders ALTER COLUMN status SET NOT NULL;
```

**Что ловит:** `INSERT INTO orders (id) VALUES (1);` — status = NULL. Кодовая валидация не спасает, если кто-то (миграция, другой сервис, `psql`) вставил строку без status.

**Когда обязательно:** любое поле, для которого `NULL` — это не «отсутствие значения», а «баг». `status`, `created_at`, `customer_id`.

**Когда допустимо `NULL`:** необязательные поля (`discount_code`, `coupon_id`). Здесь `NULL` — легитимное состояние: «скидки нет».

**Производительность:** нулевая. `NOT NULL` — это флаг в системном каталоге. Не проверяется при каждом `INSERT` как `CHECK`. PostgreSQL использует его в планировщике (может опустить `IS NULL` проверки в WHERE).

---

### 2. `UNIQUE`

```sql
-- Один email — один аккаунт:
ALTER TABLE users ADD CONSTRAINT users_email_unique
    UNIQUE (email);

-- Idempotency key: один payment — одна запись в ledger:
ALTER TABLE ledger_entries ADD CONSTRAINT ledger_txn_id_unique
    UNIQUE (txn_id);

-- Частичный unique (только для активных):
ALTER TABLE api_keys ADD CONSTRAINT api_keys_active_unique
    UNIQUE (user_id) WHERE revoked_at IS NULL;
```

**Что ловит:** дубли. Два `INSERT` с одним `email` — второй упадёт. Два `INSERT` с одним `txn_id` (идемпотентность) — второй упадёт.

**Когда обязательно:**
- Любой бизнес-ключ, который должен быть уникальным: email, phone, txn_id, invoice_number.
- Idempotency: `UNIQUE (idempotency_key)` — защита от повторной обработки.
- Частичный unique: «у пользователя может быть несколько API-ключей, но только один активный».

**Производительность:**
- `UNIQUE` создаёт **unique index**. Каждый `INSERT` / `UPDATE` — проверка в индексе (O(log N)).
- При высокой конкурентности (1000 `INSERT/s` в таблицу с `UNIQUE`) → lock contention на индекс. Но это **лучше** чем «двойное списание».
- Частичный `UNIQUE (... WHERE ...)` — индекс содержит только подходящие строки → меньше contention.

**Подводный камень:** `UNIQUE (email)` + `CASE-INSENSITIVE` — PostgreSQL по умолчанию чувствителен. `John@site.ru` и `john@site.ru` — разные. Если нужно CI-unique:

```sql
ALTER TABLE users ADD CONSTRAINT users_email_ci_unique
    UNIQUE (LOWER(email));
```

---

### 3. `CHECK`

```sql
-- Дискретные значения (без ENUM-типа):
ALTER TABLE orders ADD CONSTRAINT orders_status_valid
    CHECK (status IN ('draft', 'confirmed', 'shipped', 'delivered', 'cancelled'));

-- Бизнес-инвариант:
ALTER TABLE order_lines ADD CONSTRAINT lines_qty_positive
    CHECK (quantity > 0 AND quantity <= 10000);

ALTER TABLE order_lines ADD CONSTRAINT lines_price_positive
    CHECK (unit_price_cents > 0);

ALTER TABLE accounts ADD CONSTRAINT accounts_balance_non_negative
    CHECK (balance_cents >= 0);

-- Связь между полями:
ALTER TABLE orders ADD CONSTRAINT orders_total_consistent
    CHECK (total_cents >= 0 AND total_cents <= 10_000_000);

-- Частичный (только для определённых строк):
ALTER TABLE orders ADD CONSTRAINT orders_refund_needs_reason
    CHECK (status != 'refunded' OR refund_reason IS NOT NULL);
```

**Что ловит:** невалидные значения, которые пролезли мимо кодовой валидации. `quantity = -5`, `status = 'payd'` (опечатка), `balance = -100`.

**Когда обязательно:**
- Любой **инвариант**, который должен быть истинным **всегда**, независимо от того, кто написал строку.
- Дискретные множества (status, type, method).
- Диапазоны (quantity, price, balance).
- Связи между полями (`total >= sum(lines)`).

**Когда НЕ нужно:**
- Валидация, которая зависит от **контекста** (роль, время, другой объект). `CHECK` не видит других таблиц (без FK) и не знает «кто пишет».
- Сложная бизнес-логика (30+ условий, вызов функций). `CHECK` — это **простые** выражения, без подзапросов (до PG 12), без пользовательских функций (можно, но дорого и медленно).

**Производительность:**
- `CHECK` проверяется **при каждом** `INSERT` / `UPDATE`.
- Простые условия (`> 0`, `IN (...)`) — наносекунды. Не видно.
- Сложные условия (вызов SQL-функций) — микросекунды–миллисекунды. При 10 000 `INSERT/s` → 10–100 мс на проверку. Уже видно.
- **Не** создаёт индекс. Это **фильтр**, не **поиск**.

**Подводный камень:** `CHECK` — это **не** `TRIGGER`. Он проверяется **до** применения строки. Если `CHECK` падает → `INSERT` / `UPDATE` **отменяется**. Нет частичных изменений. Это хорошо.

Но: `CHECK` не может ссылаться на **другие таблицы**. `CHECK (total_cents = (SELECT SUM(...) FROM order_lines WHERE order_id = orders.id))` — **не работает** (подзапрос в `CHECK` — не поддерживается до PG 12, а даже в PG 12 — только `ASSERT`). Для кросс-табличных инвариантов — `TRIGGER`.

---

### 4. `FOREIGN KEY`

```sql
ALTER TABLE orders ADD CONSTRAINT orders_customer_fk
    FOREIGN KEY (customer_id) REFERENCES users(id)
    ON DELETE RESTRICT;   -- не удалить пользователя, пока есть заказы

ALTER TABLE order_lines ADD CONSTRAINT lines_order_fk
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE;    -- удалить заказ → удалить lines

ALTER TABLE order_lines ADD CONSTRAINT lines_product_fk
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT;   -- не удалить товар, пока он в заказах
```

**Что ловит:** «висящие» ссылки. `order_lines.order_id = 9999`, а `orders.id = 9999` не существует. Кодовая валидация не спасает, если строку в `order_lines` вставили **до** строки в `orders` (или если `orders.id = 9999` удалили).

**Когда обязательно:**
- Любая ссылка на другую таблицу, где «ссылка должна существовать» — это **инвариант**, а не «предпочтение».
- `order_lines → orders`, `orders → users`, `ledger_entries → accounts`.
- `ON DELETE` поведение: `RESTRICT` (защита), `CASCADE` (удаление каскадно), `SET NULL` (обнуление).

**Когда НЕ нужно (или вредно):**
- «Мягкая» ссылка: `audit_log.user_id` может ссылаться на удалённого пользователя (history). `ON DELETE SET NULL` или отсутствие FK.
- Очень высокая конкурентность: FK-проверка = `SELECT` в parent-таблицу при каждом `INSERT` в child. При 50 000 `INSERT/s` → 50 000 проверок. Lock contention. В этом случае — приложением.
- Разные базы (микросервисы): `orders` в БД A, `users` в БД B. FK невозможен. Интегритас — приложением.

**Производительность:**
- FK-проверка при `INSERT` / `UPDATE` = **one additional index lookup** в parent-таблице. O(log N). Наносекунды.
- При `DELETE` из parent: `ON DELETE CASCADE` → PostgreSQL рекурсивно находит и удаляет все children. При 10 000 children → 10 000 `DELETE` в одной транзакции. Медленно.
- При высокой конкурентности: FK-проверка берёт **FOR KEY SHARE** lock на parent-строку. Не блокирует, но при 1000 `INSERT/s` в child → 1000 lock-acquire на parent. Не критично, но видно в `pg_stat_activity`.

**Подводный камень:** `ON DELETE CASCADE` + `CHECK` в child:

```sql
-- Если parent удаляется, children удаляются CASCADE.
-- Но CHECK в child не проверяется при CASCADE-удалении.
-- Это ок: строка уже «умирает», проверять нечего.
```

---

### 5. `EXCLUDE`

```sql
-- Не пересекаться по времени:
CREATE TABLE reservations (
    id          INT PRIMARY KEY,
    room_id     INT NOT NULL,
    time_slot   TSTZRANGE NOT NULL,
    EXCLUDE USING gist (
        room_id WITH =,
        time_slot WITH &&
    )
);
```

**Что ловит:** пересечение интервалов. Две брони одной комнаты в пересекающееся время → вторая упадёт.

**Когда обязательно:**
- Брони, слоты, расписания: «две записи не могут пересекаться».
- `tstzrange` + `&&` — нативная проверка пересечения. Без `TRIGGER`. Без кода.

**Когда не нужно:**
- Пересечение зависит от бизнес-логики (разные правила для VIP и обычных). → `TRIGGER` или код.
- Интервалы в разных таблицах. `EXCLUDE` — только в пределах одной таблицы.

**Производительность:**
- Требует **GiST-индекс** на `tstzrange`. Создание индекса — дорого (одноразово).
- Проверка при `INSERT` — O(log N) в GiST. Быстро.
- Но: `EXCLUDE` + высокая конкурентность → lock contention на GiST. При 1000 `INSERT/s` → видно.

---

### 6. `TRIGGER` (последняя линия)

```sql
-- Кросс-табличная целостность: total_cents = SUM(lines)
CREATE OR REPLACE FUNCTION trg_orders_total_check()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.total_cents IS DISTINCT FROM (
        SELECT COALESCE(SUM(quantity * unit_price_cents), 0)
        FROM order_lines WHERE order_id = NEW.id
    ) THEN
        RAISE EXCEPTION 'total_cents mismatch: expected %, got %',
            (SELECT COALESCE(SUM(quantity * unit_price_cents), 0)
             FROM order_lines WHERE order_id = NEW.id),
            NEW.total_cents;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER orders_total_check
    BEFORE INSERT OR UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION trg_orders_total_check();
```

**Что ловит:** кросс-табличные инварианты, которые `CHECK` не выразит. «Сумма строк = итог». «Если status = refunded, то refund_reason не NULL и refund_at не NULL».

**Когда обязательно:**
- Инвариант затрагивает **две и более** таблиц.
- Инвариант зависит от **агрегата** (`SUM`, `COUNT`, `MAX` по связанной таблице).
- Инвариант зависит от **времени** (`refund_at > created_at`).

**Когда НЕ нужно:**
- Простой инвариант одной строки → `CHECK`. Быстрее, проще.
- Инвариант, который зависит от **контекста** (роль, IP, время) → код. Trigger не знает «кто пишет».

**Производительность:**
- `BEFORE INSERT` trigger = **вызов plpgsql-функции** при каждом `INSERT`.
- Подзапрос в trigger = **дополнительный запрос** к БД.
- При 10 000 `INSERT/s` → 10 000 вызовов trigger + 10 000 подзапросов. **Дорого.**
- `DEFERRABLE INITIALLY DEFERRED` → проверка **в конце транзакции**, не при каждом `INSERT`. Быстрее, но сложнее (можно вставить «сломанное» состояние, если оно «чинится» в той же транзакции).

```sql
CREATE CONSTRAINT TRIGGER orders_total_check
    AFTER INSERT OR UPDATE ON orders
    DEFERRABLE INITIALLY DEFERRED
    FOR EACH ROW EXECUTE FUNCTION orders_total_check();
```

`DEFERRABLE INITIALLY DEFERRED` — проверка откладывается до `COMMIT`. Если в транзакции:
```sql
INSERT INTO orders (total_cents) VALUES (0);
INSERT INTO order_lines (order_id, qty, price) VALUES (1, 2, 50);
-- total_cents = 0, но sum(lines) = 100.
-- DEFERRED: проверка на COMMIT → падает.
-- NON-DEFERRED: проверка на INSERT → падает сразу (до вставки lines).
```

`DEFERRABLE` позволяет «временно нарушить» инвариант в рамках транзакции. Это полезно, но опасно: если `COMMIT` не произойдёт (откат) → данные не сохранены. Если произойдёт → проверка.

---

## Таблица: что и когда

| Ограничение | Что ловит | Скорость | Когда |
|---|---|---|---|
| `NOT NULL` | NULL в обязательном поле | Нулевая | Всегда, для обязательных полей |
| `UNIQUE` | Дубли бизнес-ключа | O(log N) index lookup | Email, txn_id, idempotency key |
| `CHECK` | Невалидное значение / диапазон / связь полей | Наносекунды | quantity > 0, status IN (...), total >= 0 |
| `FOREIGN KEY` | Висящая ссылка | O(log N) index lookup | order_lines → orders, orders → users |
| `EXCLUDE` | Пересечение интервалов | O(log N) GiST lookup | Брони, слоты, расписания |
| `TRIGGER` | Кросс-табличные / агрегатные инварианты | Миллисекунды (подзапрос) | total = SUM(lines), refund_at > created_at |

---

## Что **не** нужно делать

### ❌ `CHECK` на каждое поле, которое «по логике не может быть NULL»

```sql
-- Избыточно:
CHECK (customer_id IS NOT NULL)
CHECK (status IS NOT NULL)
CHECK (created_at IS NOT NULL)
-- Лучше:
NOT NULL  -- на колонке. Один флаг, не три CHECK.
```

`NOT NULL` — дешевле и чище. `CHECK (x IS NOT NULL)` — то же самое, но с накладными на вызов CHECK-функции.

### ❌ `TRIGGER` для того, что делает `CHECK`

```sql
-- Избыточно:
CREATE TRIGGER chk_qty BEFORE INSERT ON order_lines FOR EACH ROW EXECUTE FUNCTION ...
-- Лучше:
CHECK (quantity > 0)
```

`CHECK` — встроено, быстрее, проще. `TRIGGER` — только для того, что `CHECK` не выразит.

### ❌ `UNIQUE` на поле, которое «по логике уникальное, но может быть NULL»

```sql
-- Плохо:
UNIQUE (phone)  -- NULL, NULL → не дубли (UNIQUE игнорирует NULL). Но это неочевидно.

-- Лучше:
UNIQUE (phone) WHERE phone IS NOT NULL  -- явная намеренность.
```

### ❌ FK на «мягкую» ссылку

```sql
-- Плохо:
ALTER TABLE audit_log ADD FOREIGN KEY (user_id) REFERENCES users(id);
-- → Не можно удалить пользователя, пока есть audit_log.
-- → Но audit_log — история. Пользователя удалить нужно.

-- Лучше:
-- Нет FK. Альтернатива:
ALTER TABLE audit_log ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
-- Или: нет FK, приложение гарантирует.
```

### ❌ `CHECK` с пользовательской функцией

```sql
-- Медленно:
CHECK (is_valid_email(email))  -- вызов SQL-функции при каждом INSERT
-- Лучше:
CHECK (email LIKE '%@%.%')    -- простой pattern. Или: валидация в коде + CHECK как «последняя линия».
```

`CHECK` с функцией — это **вызов кода при каждом INSERT**. При 10 000 `INSERT/s` → 10 000 вызовов. Дорого.

---

## Иерархия защиты

```
┌─────────────────────────────────────────────────────────────────┐
│  Уровень 1: Код приложения (политика)                           │
│  ─────────────────────────────────────────────────────────────  │
│  if ($qty <= 0) throw new DomainException('Quantity positive') │
│  if (!$user->isAdmin()) throw new AccessDeniedException()      │
│  if ($order->status !== 'draft') throw new DomainException()   │
│                                                                 │
│  Ловит: бизнес-логику, контекст, роль, состояние.               │
│  Не ловит: баг, race, прямой доступ к БД.                       │
├─────────────────────────────────────────────────────────────────┤
│  Уровень 2: БД-ограничения (физика)                             │
│  ─────────────────────────────────────────────────────────────  │
│  CHECK (quantity > 0)                                           │
│  UNIQUE (email)                                                 │
│  FOREIGN KEY (order_id) REFERENCES orders(id)                   │
│  CHECK (status IN ('draft', 'confirmed', ...))                  │
│                                                                 │
│  Ловит: баг, race, прямой доступ, миграцию.                     │
│  Не ловит: бизнес-логику, контекст, роль.                       │
├─────────────────────────────────────────────────────────────────┤
│  Уровень 3: Аппаратная целостность (железо)                     │
│  ─────────────────────────────────────────────────────────────  │
│  ECC-память, T10000-диски, RAID, WAL.                           │
│                                                                 │
│  Ловит: аппаратный сбой.                                         │
│  Не ловит: ничего из «логики».                                  │
└─────────────────────────────────────────────────────────────────┘
```

Каждый уровень ловит то, что **не ловит предыдущий**. Уровень 1 — политика. Уровень 2 — физика. Уровень 3 — железо. Убрать любой уровень — потерять защиту от **какой-то** категории сбоев.

---

## Практический чеклист

```sql
-- Для каждой таблицы:
NOT NULL    -- на обязательных полях. Всегда.
UNIQUE      -- на бизнес-ключах. Всегда.
CHECK       -- на диапазонах и дискретных множествах. Всегда.
FOREIGN KEY -- на ссылках. Почти всегда.
EXCLUDE     -- на интервалах. Если есть.
TRIGGER     -- на кросс-табличных инвариантах. Если CHECK не хватает.
```

```php
// В коде приложения:
// Валидация контекста (роль, состояние, время) → код.
// Валидация инварианта (quantity > 0) → код + CHECK (двойная страховка).
// Валидация бизнес-логики (можно ли подтвердить?) → код. Никогда не в БД.
```

**Принцип:** БД-ограничения — это **минимальный набор инвариантов**, который должен быть истинным **независимо от того, кто и как пишет данные**. Кодовая валидация — это **полная бизнес-логика**. Они не дублируют друг друга. Они дополняют.

> ⏱ 49.7s · prompt 56761 tok · output 5795 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

The user is asking about my process for diagnosing and optimizing slow PostgreSQL queries. This is a practical, detailed question that follows our conversation about PHP, Symfony, Doctrine, and database design. They want to know my systematic approach, tools, what I look at, and common causes.

Let me structure this as a diagnostic process - step by step, from "query is slow" to "here's why and how to fix it." I should cover:

1. Initial measurement - how do I confirm it's actually slow and isolate the query
2. EXPLAIN ANALYZE - the core tool
3. Reading the plan - what patterns indicate problems
4. Common causes (missing index, bad index, sequential scan, sort, nested loop, etc.)
5. Specific optimizations for each
6. Advanced tools (pg_stat_statements, auto_explain, pg_stat_user_indexes)
7. Trade-offs (index vs write performance, etc.)

I should be concrete with SQL examples, show real EXPLAIN output patterns, and give practical advice. Let me keep the same tone as the rest of the conversation - direct, practical, with code examples and trade-offs.

Let me structure:
1. First: measure precisely (is it really the query? what's the latency?)
2. EXPLAIN (ANALYZE, BUFFERS, SETTINGS)
3. Reading the plan - key patterns
4. Common causes and fixes (with examples)
5. Tools for finding the slow query (pg_stat_statements, auto_explain)
6. Advanced: concurrent execution, CTE, partitioning
7. When it's not the query (network, connection pool, lock contention)

I'll keep it practical and structured with tables where appropriate.

</details>

**Кандидат:**

# Диагностика медленного запроса в PostgreSQL

## Шаг 0: Убедиться, что проблема в запросе

```
Страница тормозит 3 секунды.

Где время?
  ├── PHP: hydration, сериализация, логика         → 200 мс
  ├── Network: PHP → PostgreSQL (round-trip)        → 50 мс (RDS)
  ├── PostgreSQL: парсинг, planning, execution      → 2500 мс  ← здесь
  └── PostgreSQL: lock wait (ожидание другого)      → ?

Если lock wait — это не «медленный запрос». Это «запрос ждёт».
Лечится иначе.
```

Быстрая проверка:

```sql
-- В момент торможения, в другом окне:
SELECT pid, state, wait_event_type, wait_event,
       now() - query_start AS duration,
       left(query, 120) AS query
FROM pg_stat_activity
WHERE state != 'idle'
ORDER BY duration DESC;
```

| `state` | `wait_event_type` | Что происходит |
|---|---|---|
| `active` | `CPU` | Идёт computation (сортировка, hash join, scan) |
| `active` | `IO` / `read` / `write` | Ждёт диск (seq scan, sort spill) |
| `active` | `Lock` | Ждёт блокировку другого процесса |
| `active` | `ClientRead` | Ждёт клиента (не наша проблема) |
| `idle in transaction` | — | Транзакция открыта, но запрос не идёт. **Держит locks.** |

Если `wait_event_type = Lock` → проблема не в планe, а в конкурентности. Идти в «Шаг 6».

Если `wait_event_type = CPU` или `IO` → запрос реально медленный. Дальше.

---

## Шаг 1: Зафиксировать точный запрос

```sql
-- Из Profiler (Symfony) / pg_stat_statements / логи:
SELECT o.id, o.status, o.customer_id, o.created_at,
       l.id AS line_id, l.quantity, l.unit_price_cents
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
WHERE o.status = 'confirmed'
ORDER BY o.created_at DESC
LIMIT 50;
```

Время: **2.3 с** (в `EXPLAIN ANALYZE`).

---

## Шаг 2: `EXPLAIN (ANALYZE, BUFFERS, SETTINGS)`

```sql
EXPLAIN (ANALYZE, BUFFERS, SETTINGS, FORMAT TEXT)
SELECT o.id, o.status, o.customer_id, o.created_at,
       l.id AS line_id, l.quantity, l.unit_price_cents
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
WHERE o.status = 'confirmed'
ORDER BY o.created_at DESC
LIMIT 50;
```

Параметры:

| Флаг | Что даёт |
|---|---|
| `ANALYZE` | Реальное время, реальные строки. **Выполняет** запрос. |
| `BUFFERS` | Сколько страниц из shared_buffers, сколько с диска. |
| `SETTINGS` | Значения GUC-параметров, влияющих на план (work_mem, jit, etc.) |
| `WAL` | Сколько WAL-записей (для INSERT/UPDATE/DELETE). |
| `TIMING` | Время на каждый узел (включено по умолчанию с `ANALYZE`). |
| `VERBOSE` | ОIDs таблиц, схемы. |

### Пример вывода (проблемный)

```
Limit  (cost=0.43..125000.50 rows=50 width=80) (actual time=2300.1..2301.8 rows=50 loops=1)
  Buffers: shared hit=120 read=45000 dirtied=0 written=0
  ->  Sort  (cost=125000.00..125001.25 rows=500 width=80) (actual time=2290.5..2295.0 rows=50 loops=1)
        Sort Key: o.created_at DESC
        Sort Method: top-N heapsort  Memory: 35kB
        Buffers: shared hit=120 read=45000
        ->  Nested Loop Left Join  (cost=0.43..124980.00 rows=500 width=80) (actual time=0.05..2280.3 rows=500 loops=1)
              Buffers: shared hit=120 read=45000
              ->  Seq Scan on orders o  (cost=0.00..5000.00 rows=100 width=50) (actual time=0.02..45.2 rows=100 loops=1)
                    Filter: (status = 'confirmed')
                    Rows Removed by Filter: 9900
                    Buffers: shared hit=10 read=45000
              ->  Index Scan using lines_order_id_idx on order_lines l  (cost=0.15..1.25 rows=5 width=30) (actual time=0.01..0.02 rows=5 loops=100)
                    Index Cond: (order_id = o.id)
                    Buffers: shared hit=110
Planning Time: 0.15 ms
Execution Time: 2302.0 ms
```

---

## Шаг 3: Чтение плана (сверху вниз)

План читается **сверху вниз** — верхний узел — финальный, нижний — источник данных.

### Что я ищу

| Паттерн | Что значит | Плохо? |
|---|---|---|
| `Seq Scan` на большой таблице | Полное сканирование. Нет подходящего индекса. | Да, если таблица > 10 000 строк. |
| `Sort Method: external merge` | Sort не влез в `work_mem`, spilled на диск. | Да. `Memory: 5MB` → `Disk: 200MB`. |
| `Nested Loop` с `loops=10000` | Внутренний запрос выполняется 10 000 раз. | Да, если внутренний дорогой. |
| `Hash Join` с `hash_mem=50MB` | Hash-таблица большая. | Ок, если `hash_mem < work_mem`. Плохо, если spill. |
| `Rows Removed by Filter: 9900` | Прочитали 10 000 строк, оставили 100. | Да. Индекс по фильтру решит. |
| `Buffers: read=45000` | 45 000 страниц с диска. | Да. Это ~360 МБ (8 КБ/страницу). |
| `actual time=0.02..2300` | Первый ряд за 0.02 мс, последний за 2300 мс. | Да. Значит, большая часть времени — в конце (sort, scan). |
| `loops=100` на внутреннем узле | Внутренний выполняется 100 раз. | Зависит от стоимости. 0.02 мс × 100 = 2 мс. Ок. 20 мс × 100 = 2 с. Плохо. |

### В нашем примере

Проблемы видны сразу:

1. **`Seq Scan on orders`** — 10 000 строк прочитано, 9 900 отфильтровано. Нет индекса по `status`.
2. **`Sort Method: top-N heapsort`** — 500 строк сортируются. Влезает в память (`Memory: 35kB`). Не критично.
3. **`Nested Loop` с `loops=100`** — для каждой из 100 строк `orders` идёт `Index Scan` по `lines`. 100 × 0.02 мс = 2 мс. Не критично.
4. **`Buffers: read=45000`** — 360 МБ с диска. Вот это боль. Seq scan читает всё.

**Вердикт:** проблема — `Seq Scan`. Нет индекса по `status` (или по `status, created_at`).

---

## Шаг 4: Типовые причины и решения

### Причина 1: Отсутствие индекса (или неподходящего)

```
Симптом: Seq Scan + Filter + Rows Removed by Filter: N
Признак: 9 900 из 10 000 строк отброшены.
```

**Решение:**

```sql
-- Индекс по фильтру + сортировке:
CREATE INDEX idx_orders_status_created
    ON orders (status, created_at DESC);
```

После:

```
Index Scan using idx_orders_status_created on orders o
  Index Cond: (status = 'confirmed')
  Order By: created_at DESC
  → 100 строк, 0 отброшено.
  → Sort узел исчезает (индекс уже отсортирован).
  → Buffers: read=10 (было 45 000).
```

**Почему `(status, created_at DESC)`, а не два отдельных индекса?**

| Индекс | Что делает |
|---|---|
| `(status)` | Найдёт 100 строк. Но `ORDER BY created_at` → отдельный `Sort`. |
| `(status, created_at DESC)` | Найдёт 100 строк **уже отсортированными**. `Sort` не нужен. |
| `(created_at DESC)` | Найдёт по дате. Но `WHERE status = 'confirmed'` → фильтр после. |

Composite index — один, а не два.

**Trade-off:**

| | |
|---|---|
| + Быстрее `SELECT` | − Медленнее `INSERT` / `UPDATE` (обновление индекса) |
| + Меньше `Sort` | − Больше дискового пространства |
| | − При 10 индексах на таблицу → `INSERT` в 3 раза медленнее |

**Когда НЕ ставить индекс:**
- Таблица < 1 000 строк. Seq scan быстрее index scan (меньше random I/O).
- Селективность < 5%: `WHERE status = 'confirmed'` при 95% строк с этим статусом → индекс бесполезен. PostgreSQL сам выберет Seq Scan.
- Таблица редко читается, часто пишется. Индекс = overhead.

---

### Причина 2: Sort spill (внешняя сортировка)

```
Симптом: Sort Method: external merge disk
Признак: Disk: 200MB (было Memory: 5MB)
```

Значит: `work_mem` не хватает. PostgreSQL сортирует 500 000 строк по `created_at`, не влезает в 4 МБ (default `work_mem`) → пишет на диск.

**Решение:**

```sql
-- Для конкретного сессии (не глобально!):
SET work_mem = '64MB';

-- Или для конкретного запроса (PG 14+):
SELECT ... ORDER BY ... /* work_mem = '64MB' */;
-- Нет, так не работает. Только SET.

-- Или в postgresql.conf (глобально, осторожно):
work_mem = 32MB;
```

**Почему не `work_mem = 1GB` в `postgresql.conf`:**

`work_mem` — на **операцию сортировки**, а не на сессию. Если в запросе 3 сортировки (JOIN + Sort + Sort) → 3 × 1 ГБ = 3 ГБ на один запрос. При 100 одновременных запросов → 300 ГБ. OOM.

**Правильно:**
- `work_mem = 4–16MB` глобально (default 4MB часто мало для сортировок > 100 000 строк).
- Для тяжёлых отчётов: `SET work_mem = '256MB'` в сессии перед запросом.
- Или: уменьшить объём сортировки (индекс по сортируемому полю, `LIMIT` раньше).

---

### Причина 3: Nested Loop с дорогим внутренним

```
Симптом: Nested Loop, loops=10000, inner actual time=2ms
Признак: 10 000 × 2 мс = 20 с.
```

**Решение:**

```sql
-- Вариант А: Hash Join (PostgreSQL сам выберет, если inner большой)
-- Убедиться, что inner достаточно большой для hash:
-- Если inner < 1000 строк → Nested Loop быстрее.

-- Вариант Б: Materialize inner (если inner — подзапрос)
SELECT ...
FROM orders o
JOIN (SELECT ... FROM order_lines WHERE ...) l ON ...
-- PostgreSQL может Materialize → один раз → Nested Loop по материалу.

-- Вариант Ц: Rewrite (если JOIN не нужен)
-- Взамен 10 000 JOIN'ов → 1 запрос с IN:
SELECT ... FROM orders o
WHERE o.id IN (SELECT order_id FROM order_lines WHERE ...)
```

---

### Причина 4: `SELECT *` / лишние колонки

```sql
-- Было:
SELECT * FROM orders WHERE ...;
-- orders: id, status, created_at, customer_id,
--        shipping_address (JSONB, 2 КБ),
--        notes (TEXT, 5 КБ),
--        metadata (JSONB, 10 КБ)
-- Итого: ~17 КБ на строку. 1000 строк = 17 МБ.

-- Стало:
SELECT id, status, created_at FROM orders WHERE ...;
-- Итого: ~50 байт на строку. 1000 строк = 50 КБ.
```

**Trade-off:**
- `SELECT *` проще в коде. Но: больше I/O, больше памяти, больше network.
- Если `JSONB` / `TEXT` в строке → PostgreSQL **не может** использовать index-only scan.
- Index-only scan: `SELECT id, status FROM orders WHERE status = 'confirmed'` → читает **только индекс**, не данные. В 10–50 раз быстрее.

```
Index Only Scan using idx_orders_status on orders
  Index Cond: (status = 'confirmed')
  Heap Fetches: 0   ← ← ← НИ ОДНОГО обращения к данным!
  Buffers: shared hit=15
```

**Условие:** все запрашиваемые колонки — в индексе. И `visibility map` актуальный (иначе `Heap Fetches > 0`).

---

### Причина 5: Планировщик выбрал плохой план

```
Симптом: EXPLAIN показывает «очевидно плохой» план.
         EXPLAIN (ANALYZE) подтверждает: реально медленный.
         Но логика плана — «правильная» для данных, которые планировщик видит.
```

Причины:

| Причина | Пример |
|---|---|
| Устаревшие статистики | `ANALYZE` не запускался 3 месяца. Таблица выросла в 10 раз. |
| `random_page_cost` завышен | SSD: `random_page_cost = 1.1`. HDD: `4.0`. Default `4.0`. |
| `effective_cache_size` занижен | PostgreSQL думает, что кэш маленький → выбирает Seq Scan. |
| CTE как «оптимизационный барьер» | PG < 12: CTE = материализация. PG 12+: CTE = inlining (если нет `MATERIALIZED`). |

**Диагностика:**

```sql
-- Статистики:
SELECT relname, reltuples,
       last_analyze, last_autoanalyze
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- Если last_autoanalyze — 2 месяца назад → ANALYZE:
ANALYZE orders;

-- Параметры планировщика:
SHOW random_page_cost;      -- Default: 4.0. SSD: 1.1. NVMe: 1.1.
SHOW effective_cache_size;  -- Default: 160MB. RAM 64GB: 40GB.
SHOW work_mem;              -- Default: 4MB.
```

**Фикс:**

```sql
-- postgresql.conf (SSD/NVMe):
random_page_cost = 1.1
effective_cache_size = '40GB'
-- При 64 ГБ RAM: 50% RAM = 32 ГБ. Запас для ОС.
```

После `ALTER SYSTEM SET` + `pg_reload_conf()`:

```sql
SELECT pg_reload_conf();
```

Планировщик пересчитает планы. Seq Scan может стать Index Scan.

---

### Причина 6: Lock contention

```
Симптом: wait_event_type = Lock. Запрос «завис» на 30 с.
         В pg_stat_activity: 5 процессов в active, все на одной таблице.
```

**Диагностика:**

```sql
-- Кто держит блокировку?
SELECT blocked.pid AS blocked_pid,
       blocked.query AS blocked_query,
       blocking.pid AS blocking_pid,
       blocking.query AS blocking_query,
       blocking.state AS blocking_state
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND NOT bl.granted
JOIN pg_locks kl ON kl.transactionid = bl.transactionid AND kl.granted
JOIN pg_stat_activity blocking ON blocking.pid = kl.pid
WHERE NOT bl.granted;
```

**Типовые сценарии:**

| Сценарий | Что происходит | Решение |
|---|---|---|
| Long transaction | Процесс A: `BEGIN; SELECT ... FOR UPDATE;` → держит lock 30 с (ожидает пользователя) | Уменьшить время транзакции. Не держать lock на время HTTP-вызова. |
| Deadlock | A: UPDATE orders(1), ждёт orders(2). B: UPDATE orders(2), ждёт orders(1). | PostgreSQL детектирует deadlock (default `deadlock_timeout = 1s`). Бросает `40P01`. Лечить порядком операций. |
| `idle in transaction` | Процесс открыл транзакцию, но не `COMMIT`-нул. Держит MVCC-снапшот. | `pg_terminate_backend()`. Найти в коде, где `BEGIN` без `COMMIT`. |

**Мониторинг:**

```sql
-- Alert: транзакция > 30 секунд
SELECT pid, now() - xact_start AS duration, state, query
FROM pg_stat_activity
WHERE state != 'idle'
  AND now() - xact_start > interval '30 seconds';
```

---

### Причина 7: Невидимые данные (MVCC)

```
Симптом: Seq Scan. Rows Removed by Clauses: 0.
         Но: Heap Fetches: 5000 (в Index Only Scan).
```

Это **visibility map** неактуальный. После `UPDATE` / `DELETE` без `VACUUM` → многие страницы «невидимые». Index Only Scan не может доверять индексу → делает Heap Fetch → читает данные → замедляется.

**Диагностика:**

```sql
SELECT relname, n_live_tup, n_dead_tup,
       last_vacuum, last_autovacuum
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- Если n_dead_tup > 10% от n_live_tup → VACUUM пора.
```

**Решение:**

```sql
-- Немедленно:
VACUUM (ANALYZE) orders;

-- Долгосрочно: tune autovacuum
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.05,  -- 5% (default 20%)
    autovacuum_vacuum_cost_limit = 200      -- быстрее (default: inherit)
);
```

---

## Шаг 5: Инструменты для поиска медленного запроса

### `pg_stat_statements`

```sql
-- Разово:
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
-- В postgresql.conf:
-- shared_preload_libraries = 'pg_stat_statements'
-- pg_stat_statements.track = all

-- Топ-10 по среднему времени:
SELECT queryid,
       round(mean_exec_time::numeric, 2) AS avg_ms,
       round(total_exec_time::numeric, 2) AS total_ms,
       calls,
       round(rows::numeric) AS rows,
       left(query, 120) AS query
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 10;
```

**Что показывает:** среднее время, количество вызовов, среднее число строк. Позволяет отличить «один медленный запрос» от «1000 запросов по 5 мс».

**Очистка:** `SELECT pg_stat_statements_reset();` (после деплоя, после миграции).

---

### `auto_explain`

```sql
-- postgresql.conf:
shared_preload_libraries = 'auto_explain'
auto_explain.log_min_duration = '100ms'   -- логировать запросы > 100 мс
auto_explain.log_analyze = on             -- с ANALYZE (реальное время)
auto_explain.log_buffers = on             -- с BUFFERS
auto_explain.log_timing = on
auto_explain.log_format = 'json'          -- удобно парсить
```

Каждый запрос > 100 мс → полный `EXPLAIN ANALYZE` в лог PostgreSQL. Не нужно вручную копировать запрос.

---

### `pg_stat_user_indexes`

```sql
-- Индексы, которые никогда не используются:
SELECT schemaname, relname, indexrelname,
       idx_scan,       -- 0 → не используется
       pg_size_pretty(pg_relation_size(indexrelid)) AS size
FROM pg_stat_user_indexes
WHERE idx_scan = 0
  AND schemaname = 'public'
ORDER BY pg_relation_size(indexrelid) DESC;
```

**Что с этим делать:** не удалять сразу. Индекс мог быть нужен «ту ночь, когда был инцидент». Но если `idx_scan = 0` за 3 месяца → кандидат на удаление.

Обратно:

```sql
-- Индексы, которые используются, но медленно:
-- (через pg_stat_statements: mean_exec_time > 100ms + Seq Scan в плане)
```

---

## Шаг 6: Когда проблема НЕ в плане

| Симптом | Причина | Инструмент |
|---|---|---|
| Запрос быстрый в `psql`, медленный из PHP | Connection pool исчерпан. Ожидание свободного соединения. | `pg_stat_activity`: count by state. |
| Запрос быстрый в `psql`, медленный из Symfony | Doctrine hydration. 5000 строк × 20 полей = 100 000 PHP-объектов. | Профилировщик PHP (Xdebug, Blackfire). |
| `SELECT` быстрый, `UPDATE` медленный | Lock contention. MVCC. WAL. | `pg_locks`, `pg_stat_activity`. |
| Запрос медленный в пик, быстрый ночью | Конкурентность. I/O saturation. | `pg_stat_database`: `blks_read`, `blks_hit`. |
| Первый запрос медленный, второй быстрый | Cold cache. shared_buffers не заполнен. | Normal. Если каждый запрос «первый» → connection pool создаёт новые соединения. |

---

## Чеклист диагностики

```
1. pg_stat_activity: state, wait_event → Lock? CPU? IO?
   → Lock: pg_locks, pg_terminate_backend.
   → CPU/IO: EXPLAIN ANALYZE.

2. EXPLAIN (ANALYZE, BUFFERS):
   → Seq Scan + Rows Removed? → Индекс.
   → external merge disk? → work_mem.
   → Nested Loop × 10 000? → Hash Join / rewrite.
   → Heap Fetches > 0 (Index Only Scan)? → VACUUM.
   → Planning Time > 100 мс? → prepared statement / статистики.

3. Статистики:
   → last_analyze > 1 месяц? → ANALYZE.
   → random_page_cost = 4.0 на SSD? → 1.1.
   → effective_cache_size < RAM/2? → поднять.

4. MVCC:
   → n_dead_tup > 10%? → VACUUM.
   → autovacuum отключён? → включить.

5. Индексы:
   → idx_scan = 0 за 3 месяца? → кандидат на удаление.
   → Нет индекса по WHERE + ORDER BY? → composite index.

6. Не запрос:
   → Connection pool? → pgbouncer.
   → Hydration? → Partial / DQL.
   → I/O saturation? → iostat, pg_stat_database.
```

---

## Итого

Медленный запрос — это почти всегда одна из пяти причин:

| # | Причина | Частота | Лечится |
|---|---|---|---|
| 1 | Нет индекса / неправильный индекс | **60%** | `CREATE INDEX` |
| 2 | Sort spill | 15% | `work_mem` / индекс по ORDER BY |
| 3 | Неправильный join type (Nested Loop × N) | 10% | Rewrite / Hash Join |
| 4 | MVCC / VACUUM | 8% | `VACUUM` / autovacuum tuning |
| 5 | Lock contention / long transaction | 7% | Уменьшить транзакцию / `FOR UPDATE SKIP LOCKED` |

В 90% случаев — `EXPLAIN ANALYZE` + `CREATE INDEX`. Остальное — нюансы.

> ⏱ 56.1s · prompt 62381 tok · output 6482 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: определение, определение границ, пример с объяснением и типовая ошибка. Это продолжение длинной беседы о PHP/Symfony/DDD/Doctrine. Нужно ответить на том же уровне глубины, с примерами кода, trade-offs, практическими деталями. Не повторять то, что уже было (агрегат Order + OrderLine был показан в контексте Doctrine). Здесь нужен акцент на ДИЗАЙНЕ границ, а не на маппинге.

Структура ответа:
1. Что такое агрегат (определение, инварианты, корень)
2. Как определяю границы (критерии)
3. Пример с объяснением (новый, не Order — чтобы не повторять. Или Order, но с другим углом. Лучше новый: например, BankAccount с ledger, или Subscription с billing, или Booking. Возьму что-то, где границы неочевидны — например, Order + Payment + Inventory. Или лучше: BankAccount как агрегат — классический пример, где границы часто ошибочно расширяют)
4. Типовая ошибка при выборе границ (расширение агрегата, нарушение инвариантов, distributed transactions)

Давайте возьму пример: **BankAccount** (баланс, операции) + **Transfer** (перевод между счётами). Это классическая ситуация, где границы агрегата решают всё.

Типовая ошибка: включить в агрегат Account обе стороны перевода, или сделать Transfer частью Account, или наоборот — разделить слишком сильно и потерять инвариант "баланс не может быть отрицательным".

Лучше: возьму **Order** как пример (уже был в контексте), но покажу, как НЕправильно выбрать границы (включить Payment, Inventory, Notification в один агрегат), и как правильно (Order — отдельный, Payment — отдельный, связь через события).

Или возьму свежий: **Subscription** (подписка) + **Invoice** (счёт) + **Payment** (платёж). Границы: Subscription — агрегат. Invoice — часть Subscription или отдельный? Payment — отдельный. Типовая ошибка: включить Payment в Subscription → distributed transaction, потеря инварианта.

Возьму Subscription — это свежий пример, не повторяет Order из предыдущего вопроса.

Структура:
1. Определение агрегата (коротко, чётко)
2. Критерии границ (практические)
3. Пример: Subscription + Invoice + Payment
   - Доменная модель
   - Почему границы именно такие
   - Что происходит при нарушении
4. Типовая ошибка: "огромный агрегат" (Subscription + Payment + Invoice + Notification + Refund в одном)
   - Что ломается
   - Как выглядит "правильно"

Пишу на русском, как и вся беседа. Формат: заголовки, таблицы, код, trade-offs. Без воды.

</details>

**Кандидат:**

# Агрегат в DDD: определение и границы

## Что такое агрегат

Агрегат — это **кластер сущностей**, которые:

1. **Изменяются вместе.** Если меняется одна — остальные должны поменяться в той же транзакции.
2. **Подчиняются общим инвариантам.** Правила целостности, которые должны быть истинны **после** любой операции над агрегатом.
3. **Имеют единый корень.** Внешний мир взаимодействует только с корнем. Внутренние сущности — не доступны снаружи.
4. **Гранулярность = транзакция.** Агрегат — минимальная единица, которую нужно сохранить атомарно.

Корень агрегата — единственная точка входа. Внутренние сущности не имеют «публичного API». Они не имеют репозитория. Их не ищут по ID извне.

```
┌─────────────────────────────────────────┐
│  Агрегат: Subscription                  │
│                                         │
│  Корень: Subscription                   │
│    ├── Инвариант: status ∈ {active,     │
│    │   paused, cancelled, expired}      │
│    ├── Инвариант: если status=active,   │
│    │   то planId ≠ null                 │
│    ├── Инвариант: trialEndsAt >         │
│    │   createdAt (если в trial)         │
│    │                                    │
│    Внутренние:                          │
│    ├── TrialPeriod (value object)       │
│    └── BillingCycle (value object)      │
│                                         │
│  Внешний мир:                           │
│    $sub = repo->find($id)               │
│    $sub->activate()                     │
│    $sub->pause()                        │
│    repo->save($sub)                     │
│                                         │
│  НЕ: $sub->billingCycle->changePlan()   │
│  НЕ: $sub->trial->extend()              │
└─────────────────────────────────────────┘
```

Ключевое: `TrialPeriod` и `BillingCycle` — **не** сущности с ID. Это value objects или внутренние сущности без публичного доступа. Корень `Subscription` сам решает, как их менять, и сам проверяет инварианты.

---

## Как определяю границы

Я задаю пять вопросов. Ответы на них рисуют границу.

### Вопрос 1: Какие инварианты должны быть атомарными?

```
«Баланс счёта не может быть отрицательным»
→ Все операции, которые меняют баланс, должны быть атомарными.
→ Агрегат: Account (баланс + операции, которые его меняют).

«Подписка не может быть active без плана»
→ Смена статуса и смена плана — атомарны.
→ Агрегат: Subscription (статус + план + trial).

«Заказ не может быть confirmed без строк»
→ Добавление строк и подтверждение — атомарны.
→ Агрегат: Order (корень + lines).
```

Если инвариант затрагивает **две** сущности и они должны меняться **вместе** — они в одном агрегате. Если могут меняться **отдельно** — в разных.

### Вопрос 2: Можно ли изменить одну сущность, не трогая другую?

```
Subscription:  «активировать» → меняет status, planId, trial.
               Одна операция. Одна транзакция. Один агрегат.

Invoice:       «сгенерировать счёт» → создаётся на основе Subscription,
               но не меняет Subscription.
               Можно сгенерировать invoice без изменения sub.
               → Разные агрегаты.

Payment:       «зарегистрировать платёж» → не меняет Subscription
               и не меняет Invoice. Создаёт свою запись.
               → Отдельный агрегат.
```

Если сущность **зависит** от другой (Invoice зависит от Subscription для данных), но **не изменяет** её — это **разные** агрегаты. Связь — через событие или через ID-ссылку.

### Вопрос 3: Какой максимальный объём данных нужно удержать в памяти?

```
Агрегат: Order (корень + 50 lines + 50 products + 10 coupons)
→ 150 объектов в памяти. Одна транзакция. Одна блокировка.
→ При 1000 одновременных транзакциях → 150 000 объектов в UoW.
→ Mems: ок.

Агрегат: Order + Payment + Invoice + Shipping + Notification + Refund
→ 500+ объектов. Одна транзакция держит 6 таблиц.
→ При 1000 одновременных → 500 000 объектов. Mems: больно.
→ Locks: 6 таблиц × 1000 = 6000 lock'ов. Deadlock-риск.
```

**Практическое правило:** если агрегат > 20 сущностей или > 5 таблиц — вероятно, граница слишком широкая. Разделить.

### Вопрос 4: Можно ли это сделать в одной транзакции?

```
«Зарегистрировать платёж + активировать подписку + отправить email»
→ Email — внешний сервис. 5 секунд.
→ Одна транзакция на 5 секунд = блокировка Account на 5 секунд.
→ 100 пользователей × 5 сек = 500 сек блокировок. Нет.

Разделение:
  Транзакция 1: Payment (зарегистрировать) → COMMIT.
  Транзакция 2: Subscription (активировать) → COMMIT.
  Асинхронно: Email.

→ Три агрегата. Три транзакции. Одна потеря = восстановление.
```

Если операция требует внешнего вызова (HTTP, queue, 3rd-party API) — **не** включать в транзакцию. Разделить на отдельные агрегаты + события.

### Вопрос 5: Кто владеет данным?

```
`email` пользователя:
  → Владелец: User.
  → Не Subscription. Не Order. Не Invoice.

`planId` подписки:
  → Владелец: Subscription.
  → Не User. Не Plan.

`invoiceNumber`:
  → Владелец: Invoice.
  → Не Subscription.

Если данные принадлежат разным «владельцам» → разные агрегаты.
Ссылка — через ID. Не через объект.
```

---

## Пример: Subscription, Invoice, Payment

### Доменная модель

```php
// ─── Агрегат 1: Subscription ───────────────────────────────────
final class Subscription
{
    private int $id;
    private int $customerId;          // ссылка на Customer (другой агрегат)
    private int $planId;              // ссылка на Plan (справочник, не агрегат)
    private SubStatus $status;        // Active | Paused | Cancelled | Trial
    private ?\DateTimeImmutable $trialEndsAt;
    private \DateTimeImmutable $startedAt;
    private ?\DateTimeImmutable $cancelledAt;
    private string $cancelReason;

    // Инварианты:
    // 1. Trial: trialEndsAt > startedAt
    // 2. Active: planId ≠ 0
    // 3. Cancelled: cancelledAt ≠ null И cancelReason ≠ ''
    // 4. Нельзя активировать из Cancelled (без повторного trial)

    private function __construct(int $customerId, int $planId, SubStatus $status)
    {
        $this->customerId = $customerId;
        $this->planId     = $planId;
        $this->status     = $status;
        $this->startedAt  = new \DateTimeImmutable();
    }

    public static function startTrial(int $customerId, int $planId, int $trialDays): self
    {
        if ($trialDays <= 0) throw new DomainException('Trial must be positive');
        $sub = new self($customerId, $planId, SubStatus::Trial);
        $sub->trialEndsAt = new \DateTimeImmutable("+{$trialDays} days");
        return $sub;
    }

    public static function activate(int $customerId, int $planId): self
    {
        if ($planId <= 0) throw new DomainException('Invalid plan');
        return new self($customerId, $planId, SubStatus::Active);
    }

    public function cancel(string $reason): self
    {
        if ($this->status === SubStatus::Cancelled) {
            throw new DomainException('Already cancelled');
        }
        $this->status        = SubStatus::Cancelled;
        $this->cancelledAt   = new \DateTimeImmutable();
        $this->cancelReason  = $reason;
        return $this;
    }

    public function pause(): self
    {
        if ($this->status !== SubStatus::Active) {
            throw new DomainException('Only active can be paused');
        }
        $this->status = SubStatus::Paused;
        return $this;
    }

    public function resume(): self
    {
        if ($this->status !== SubStatus::Paused) {
            throw new DomainException('Only paused can be resumed');
        }
        $this->status = SubStatus::Active;
        return $this;
    }

    public function trialExpired(): ?self
    {
        if ($this->status !== SubStatus::Trial) return null;
        if ($this->trialEndsAt < new \DateTimeImmutable()) {
            $this->status = SubStatus::Cancelled;
            $this->cancelledAt  = new \DateTimeImmutable();
            $this->cancelReason = 'Trial expired';
            return $this;
        }
        return null;
    }

    // Getters: id(), customerId(), planId(), status(), trialEndsAt(), ...
}
```

```php
// ─── Агрегат 2: Invoice ────────────────────────────────────────
final class Invoice
{
    private int $id;
    private string $invoiceNumber;    // уникальный бизнес-ключ
    private int $subscriptionId;      // ссылка
    private int $amountCents;
    private string $currency;
    private InvoiceStatus $status;    // Draft | Sent | Paid | Overdue | Void
    private \DateTimeImmutable $dueDate;
    private ?\DateTimeImmutable $paidAt;
    private int $lineCount;           // денормализация (для списка)

    // Инварианты:
    // 1. Paid: paidAt ≠ null
    // 2. Void: amountCents может быть 0
    // 3. Sent: dueDate > createdAt

    private function __construct(string $number, int $subId, int $amount, string $ccy)
    {
        $this->invoiceNumber = $number;
        $this->subscriptionId = $subId;
        $this->amountCents   = $amount;
        $this->currency      = $ccy;
        $this->status        = InvoiceStatus::Draft;
        $this->dueDate       = new \DateTimeImmutable('+14 days');
    }

    public static function create(string $number, int $subId, int $amountCents, string $currency): self
    {
        if ($amountCents <= 0) throw new DomainException('Amount must be positive');
        return new self($number, $subId, $amountCents, $currency);
    }

    public function markSent(): self
    {
        if ($this->status !== InvoiceStatus::Draft) {
            throw new DomainException('Only draft can be sent');
        }
        $this->status = InvoiceStatus::Sent;
        return $this;
    }

    public function markPaid(\DateTimeImmutable $paidAt): self
    {
        if ($this->status !== InvoiceStatus::Sent) {
            throw new DomainException('Only sent can be marked paid');
        }
        $this->status = InvoiceStatus::Paid;
        $this->paidAt = $paidAt;
        return $this;
    }

    public function markVoid(string $reason): self
    {
        if ($this->status === InvoiceStatus::Paid) {
            throw new DomainException('Paid invoice cannot be voided');
        }
        $this->status = InvoiceStatus::Void;
        return $this;
    }
}
```

```php
// ─── Агрегат 3: Payment ────────────────────────────────────────
final class Payment
{
    private int $id;
    private string $paymentRef;       // уникальный: gateway txn id
    private int $invoiceId;           // ссылка
    private int $amountCents;
    private string $currency;
    private string $method;           // card | paypal | bank
    private PaymentStatus $status;    // Pending | Settled | Failed | Refunded
    private ?\DateTimeImmutable $settledAt;
    private string $gateway;

    // Инварианты:
    // 1. Settled: settledAt ≠ null
    // 2. Refunded: settledAt ≠ null (нельзя refund без settle)
    // 3. Pending → Settled | Failed (не Pending → Refunded)

    public static function initiate(
        string $ref, int $invoiceId, int $amount, string $ccy,
        string $method, string $gateway
    ): self {
        if ($amount <= 0) throw new DomainException('Amount must be positive');
        $p = new self();
        $p->paymentRef   = $ref;
        $p->invoiceId    = $invoiceId;
        $p->amountCents  = $amount;
        $p->currency     = $ccy;
        $p->method       = $method;
        $p->gateway      = $gateway;
        $p->status       = PaymentStatus::Pending;
        return $p;
    }

    public function settle(\DateTimeImmutable $at): self
    {
        if ($this->status !== PaymentStatus::Pending) {
            throw new DomainException('Only pending can be settled');
        }
        $this->status      = PaymentStatus::Settled;
        $this->settledAt   = $at;
        return $this;
    }

    public function fail(string $reason): self
    {
        if ($this->status !== PaymentStatus::Pending) {
            throw new DomainException('Only pending can fail');
        }
        $this->status = PaymentStatus::Failed;
        return $this;
    }
}
```

### Почему именно три агрегата

```
Subscription ──sсылка──► Invoice ──sсылка──► Payment
   (id)                  (subscription_id)    (invoice_id)

Каждый — отдельный агрегат. Каждый — отдельный репозиторий.
Каждый — отдельная транзакция.

Почему не один:
  • Payment зависит от внешнего gateway (HTTP, 5–30 сек).
    Нельзя держать транзакцию на 30 сек.
  • Invoice зависит от биллинговой логики (калькуляция, налоги).
    Отдельная команда, отдельный ритм изменения.
  • Subscription — состояние пользователя.
    Отдельная команда, отдельный ритм изменения.

Почему не два (Subscription + Invoice, Payment отдельно):
  • Invoice создаётся на основе Subscription, но не меняет её.
    Нет атомарного инварианта «sub + invoice».
  • Invoice может быть void, а sub — active. И наоборот.
    Они меняются независимо.

Почему не пять (добавить Plan, Customer, Notification):
  • Plan — справочник. Не агрегат. Не имеет состояния.
  • Customer — другой контекст (Identity). Свой агрегат.
  • Notification — side-effect. Не агрегат. Событие.
```

### Как они взаимодействуют

```
Пользователь платит:

1. PaymentService:
   $payment = Payment::initiate($ref, $invoiceId, $amount, $ccy, $method, $gateway);
   $this->paymentRepo->save($payment);
   $this->em->flush();                    // Транзакция 1: Payment
   $this->em->clear();

   $result = $this->gateway->charge($payment);   // HTTP, 10 сек
   // ↑ ВНЕ транзакции. Если упал — Payment в статусе Pending.

   if ($result->success) {
       $payment->settle($result->settledAt);
       $this->paymentRepo->save($payment);
       $this->em->flush();                // Транзакция 2: Payment → Settled
       $this->em->clear();

       $this->events->dispatch(new PaymentSettledEvent($payment->id));
       // ↑ Асинхронно. Invoice и Subscription — в отдельных транзакциях.
   } else {
       $payment->fail($result->reason);
       $this->paymentRepo->save($payment);
       $this->em->flush();                // Транзакция 2: Payment → Failed
       $this->em->clear();
   }

2. PaymentSettledListener (асинхронно, отдельный воркер):
   $invoice = $this->invoiceRepo->find($event->invoiceId);
   $invoice->markPaid($event->settledAt);
   $this->invoiceRepo->save($invoice);
   $this->em->flush();                    // Транзакция 3: Invoice → Paid
   $this->em->clear();

   $this->events->dispatch(new InvoicePaidEvent($invoice->id));

3. InvoicePaidListener (асинхронно):
   $sub = $this->subRepo->find($event->subscriptionId);
   if ($sub->status() === SubStatus::Paused) {
       $sub->resume();
       $this->subRepo->save($sub);
       $this->em->flush();                // Транзакция 4: Subscription → Active
       $this->em->clear();
   }
```

**Четыре транзакции.** Каждая атомарна. Каждая может откатиться независимо. Если вторая упала — Payment в Settled, Invoice в Sent (не Paid). Воркер повторит. Идempotency через `paymentRef`.

**Никакой distributed transaction.** Никакого `2PC`. Никакого «если Payment ушёл, а Invoice не обновился — всё сломано».

---

## Типовая ошибка: «один большой агрегат»

### Как выглядит

```php
// «Удобно» — всё в одном:
final class SubscriptionWithBilling
{
    private int $id;
    private int $customerId;
    private int $planId;
    private SubStatus $status;

    // «А давайте добавим invoice прямо здесь»
    private InvoiceEntity $currentInvoice;

    // «А payment тоже, раз уж тут»
    private PaymentEntity $lastPayment;

    // «А notification, чтобы не терять»
    private NotificationEntity $lastNotification;

    // «А refund, потому что часто нужен»
    private ?RefundEntity $lastRefund = null;

    // «А audit log, для истории»
    private Collection $auditLog;

    // Итого: 15 полей, 5 вложенных сущностей, 8 инвариантов.
}
```

### Что ломается

| Проблема | Механизм |
|---|---|
| **Транзакция на 30 секунд** | `charge()` → HTTP (10 с) → `settle()` → `markPaid()` → `resume()` → `sendEmail()`. Всё в одном `flush()`. Блокировка 5 таблиц на 30 с. |
| **Deadlock** | Процесс A: `UPDATE subscriptions ... FOR UPDATE` → `UPDATE payments` → ждёт `UPDATE invoices`. Процесс B: `UPDATE invoices` → ждёт `UPDATE payments`. Deadlock. |
| **Невозможность повторить** | Payment ушёл, Invoice не обновился (упал между ними). Весь агрегат «сломан». Нельзя повторить — Payment уже есть. |
| **Избыточная блокировка** | `cancel()` меняет status + void'ит invoice + refund'ит payment + отправляет email. Блокирует 5 таблиц. 100 одновременных `cancel` → 500 lock'ов. |
| **Невозможность масштабирования** | Одна команда пишет в этот класс. 5 человек. 5 фич в спринт. 5 merge conflicts. |
| **Тестирование** | Чтобы тестировать `cancel()`, нужно mock'нуть 5 зависимостей. Чтобы тестировать `resume()`, тоже 5. Потому что один класс, один `flush()`. |

### Как это выглядит в коде

```php
// «Удобный» метод:
public function processPayment(int $invoiceId, string $gatewayRef): void
{
    // 1. Загрузка всего (5 таблиц):
    $this = $this->repo->findWithEverything($this->id);
    // → 5 SELECT. 5 JOIN. 50 КБ данных.

    // 2. Валидация:
    if ($this->status !== SubStatus::Active) throw ...;
    if ($this->currentInvoice->id !== $invoiceId) throw ...;

    // 3. Gateway (HTTP, 10 сек):
    $result = $this->gateway->charge($gatewayRef);
    // ↑ Транзакция ещё открыта. 5 таблиц заблокированы.
    //   100 пользователей × 10 сек = 1000 сек блокировок.

    // 4. Обновление всего (5 таблиц):
    $this->lastPayment->settle($result->at);
    $this->currentInvoice->markPaid($result->at);
    $this->status = SubStatus::Active;
    $this->lastNotification = new NotificationEntity('Payment received');

    // 5. Один flush (5 таблиц, 1 транзакция):
    $this->em->flush();
    // → BEGIN
    // → UPDATE payments SET status='settled' WHERE id=...  (lock)
    // → UPDATE invoices SET status='paid' WHERE id=...     (lock)
    // → UPDATE subscriptions SET status='active' WHERE id= (lock)
    // → INSERT notifications (...)                          (lock)
    // → INSERT audit_log (...)                              (lock)
    // → COMMIT
    // 5 lock'ов. 1 транзакция. 10 сек.
}
```

### Как правильно (разделение)

```php
// PaymentService (агрегат Payment):
public function charge(PaymentInitiated $cmd): void
{
    $payment = $this->repo->find($cmd->paymentId);
    $result  = $this->gateway->charge($payment->paymentRef());
    // ↑ HTTP. Но транзакция НЕ открыта.
    //   Payment в БД в статусе Pending. Никто не заблокирован.

    if ($result->success) {
        $payment->settle($result->at);
        $this->em->persist($payment);
        $this->em->flush();    // 1 таблица. 1 lock. 50 мс.
        $this->em->clear();
        $this->events->dispatch(new PaymentSettledEvent($payment->id()));
    }
}

// InvoiceService (агрегат Invoice):
#[AsEventListener]
public function onPaymentSettled(PaymentSettledEvent $e): void
{
    $invoice = $this->repo->find($e->invoiceId);
    $invoice->markPaid($e->settledAt);
    $this->em->persist($invoice);
    $this->em->flush();    // 1 таблица. 1 lock. 50 мс.
    $this->em->clear();
    $this->events->dispatch(new InvoicePaidEvent($invoice->id()));
}

// SubscriptionService (агрегат Subscription):
#[AsEventListener]
public function onInvoicePaid(InvoicePaidEvent $e): void
{
    $sub = $this->repo->find($e->subscriptionId);
    if ($sub->status() === SubStatus::Paused) {
        $sub->resume();
        $this->em->persist($sub);
    }
    $this->em->flush();    // 1 таблица. 1 lock. 50 мс.
    $this->em->clear();
}
```

**Три транзакции по 50 мс** вместо одной на 10 секунд. **Три lock'а по одной таблице** вместо пяти lock'ов на пять таблиц. **Три команды** могут менять свой код независимо.

### Цена разделения

| | Один агрегат | Три агрегата |
|---|---|---|
| Транзакции | 1 (30 сек) | 3 (50 мс каждая) |
| Locks | 5 таблиц | 1 таблица за раз |
| Deadlock-риск | Высокий (5 lock'ов) | Нулевой (1 lock) |
| Повторяемость | Нет (всё или ничего) | Да (каждый шаг идемпотентен) |
| Сложность кода | 1 класс, 1 метод | 3 класса, 3 события, 3 хендлера |
| Наблюдаемость | 1 log | 3 log + event trace |
| Consistency | Strong (1 транзакция) | Eventual (3 транзакции) |

**Eventual consistency** — цена разделения. Между `PaymentSettled` и `InvoicePaid` — 50–500 мс. В эти 500 мс система в «промежуточном» состоянии: платёж пришёл, счёт не оплачен. Это **нормально**, если вы спроектировали идемпотентность и мониторинг.

---

## Как я принимаю решение

```
Инвариант: «Платёж должен быть зарегистрирован ДО того, как счёт помечен оплаченным»

→ Это **порядок**, а не **атомарность**.
→ Можно гарантировать через события: Payment → event → Invoice.
→ Не нужен один агрегат.

Инвариант: «Баланс не может быть отрицательным»

→ Это **атомарность**. Баланс + операция — один агрегат.
→ Не можно разделить: Account и Transaction должны быть в одной транзакции.

Инвариант: «Подписка не может быть active без оплаченного счёта»

→ Это **бизнес-логика**, а не целостность данных.
→ Если счёт не оплачен — подписка в Trial / Paused.
→ Не нужно, чтобы Invoice и Subscription были в одном агрегате.
→ Событие InvoicePaid → Subscription.resume().
```

**Критерий:** инвариант, который нарушается при **раздельном** изменении двух сущностей → один агрегат. Инвариант, который нарушается при **неправильном порядке** → события + идемпотентность.

---

## Частые ошибки при выборе границ

| Ошибка | Следствие | Как распознать |
|---|---|---|
| Слишком широкий агрегат (5+ таблиц) | Long transactions, deadlocks, невозможность масштабирования | `EXPLAIN` показывает 5+ JOIN'ов в одном UPDATE. |
| Слишком узкий (value object как агрегат) | Ложная атомарность, невозможность повторить | «Почему email не ушёл, хотя оплата прошла?» |
| Агрегат без явного инварианта | Класс «на всякий случай», без доменной логики | В классе нет `throw DomainException`. Только `if/else`. |
| Внутренняя сущность доступна извне | Нарушение инкапсуляции. Корень не контролирует состояние. | `$order->lines[0]->setQuantity(5)` — прямой access. |
| Агрегат зависит от другого агрегата через объект | Distributed transaction. Lock на 2 таблицы. | `$subscription->invoice->markPaid()` — прямой вызов через агрегат. |
| «Командный» метод на корне, который меняет 3 таблицы | Корень не корень. Координатор. | `Order::processPayment()` внутри `Order` — а Payment другой агрегат. |

---

## Итого

Агрегат — это **транзакционная граница**, а не «класс с методами». Граница определяется **инвариантами атомарности**, а не «удобством».

**Широкий агрегат** = удобство сегодня + боль завтра (deadlocks, long transactions, merge conflicts).

**Узкий агрегат** = сложность сегодня (события, идемпотентность, мониторинг) + масштабируемость завтра.

Выбор зависит от **частоты изменения**, **количества писателей** и **допустимой задержки consistency**. Для «один пользователь, один счёт, один payment gateway» — широкий агрегат прощён. Для «1000 RPS, 3 команды, 5 gateway'ев» — только разделение.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

# Конкурентное изменение одного агрегата

## Что происходит, если ничего не делать

### Сценарий

Два процесса одновременно подтверждают один и тот же заказ. Оба читают `status = 'draft'`. Оба вызывают `confirm()`. Оба пишут.

```
Процесс A                          Процесс B
─────────                          ─────────
t=0  SELECT * FROM orders          t=0  SELECT * FROM orders
     WHERE id = 42                   WHERE id = 42
     → status = 'draft'              → status = 'draft'

t=1  order.confirm()               t=1  order.confirm()
     → status = 'confirmed'          → status = 'confirmed'

t=2  UPDATE orders                 t=2  UPDATE orders
     SET status='confirmed'          SET status='confirmed',
     WHERE id = 42                    total_cents = 5000
     → 1 row affected               → 1 row affected
                                   (перезаписал total_cents
                                    из своего вычисления)
```

**Результат:** `status = 'confirmed'`, `total_cents = 5000`. Оба «успешны». Но `total_cents` — от одного, `status` — от другого. Или: оба посчитали `total_cents` на основе **разных** наборов lines (один добавил строку между `SELECT` и `UPDATE` другого).

Это **lost update**. PostgreSQL MVCC не защищает от него. MVCC защищает от **unreadable writes** (один процесс не видит «полузаписанную» строку другого). Но если оба читают, оба пишут, оба commit'ят — последующий перезаписывает предыдущий. Ни один не упадёт.

### Более коварный сценарий

```
Процесс A: списывает 100 с баланса 150
Процесс B: списывает 100 с баланса 150

A: SELECT balance → 150. 150 >= 100. OK.
B: SELECT balance → 150. 150 >= 100. OK.

A: UPDATE SET balance = 50. COMMIT.
B: UPDATE SET balance = 50. COMMIT.   ← НЕ 0! 50!

Баланс: 50. Списано: 200. Потеряно: 150.
```

Оба процесса прошли кодовую валидацию (`if ($balance < $amount) throw`). Оба «правильно» записали результат. Результат — неверный.

**`CHECK (balance >= 0)` не спасает**: баланс не стал отрицательным. Он стал 50. Инвариант «баланс >= 0» формально выполнен. Инвариант «списано <= начислено» — нет.

---

## Фундаментальный вопрос

Прежде чем выбирать механизм, ответьте: **что должно произойти, когда два процесса пишут в один агрегат одновременно?**

| Вариант | Пример | Механизм |
|---|---|---|
| **Last write wins** (последний перезаписал) | Изменение адреса доставки: пользователь нажал «сохранить» дважды. Второй клик — финальный. | Ничего делать. Plain `UPDATE`. |
| **First write wins** (первый заблокировал) | Оплата: один платёж прошёл, второй должен отклониться. | Pessimistic lock / `UNIQUE` на `txn_id`. |
| **Both must succeed** (оба применимы) | Два разных товара добавлены в корзину. Оба `INSERT` должны пройти. | Append-only. Нет конфликтов. |
| **One must fail** (один должен упасть) | Подтверждение заказа: первый подтвердил, второй должен получить 409. | Optimistic lock (version) / состояние. |
| **Domain decides** (домен решает) | Списание: баланс 150, два списания по 100. Первое проходит, второе — нет. | Lock + re-read + validate. |

**Это не технический вопрос. Это доменный.** «Что значит, что два администратора одновременно сменили статус?» — вопрос к product owner, а не к DBA.

---

## Спектр решений (от лёгкого к тяжёлому)

### 1. Optimistic Locking (version column)

#### Механизм

```sql
ALTER TABLE orders ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// При загрузке: Doctrine хранит version в entity.
// При сохранении:
UPDATE orders
SET status = 'confirmed', version = version + 1
WHERE id = 42 AND version = 7;   // ← версия, которую мы читали

// Если 0 rows affected → кто-то успел раньше. Повторить / упасть.
```

В Doctrine:

```php
#[ORM\Entity]
#[ORM\OptimisticLock]
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 0;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;
    // ...
}
```

При `flush()` Doctrine генерирует:

```sql
UPDATE orders
SET status = 'confirmed', version = 1
WHERE id = 42 AND version = 0;
```

Если строка была изменена (version стал 1) → `0 rows affected` → Doctrine бросает:

```
Doctrine\OptimisticLockException:
  Concurrency conflict, expected version 0, actual 1
```

#### Что это даёт

| | |
|---|---|
| + Нет блокировки. Процесс A не «удерживает» строку. | − Дополнительная колонка. |
| + Нет deadlocks. | − Конфликт = исключение. Нужно обрабатывать. |
| + Работает с MVCC. Нет `FOR UPDATE`. | − При высокой конкурентности (100 одновременных) → много конфликтов → много retry. |
| + Простота: одна колонка, одна проверка в `WHERE`. | − Не защищает от «логического» конфликта (оба пишут в **разные** поля). |

#### Когда

- Конкурентность **низкая** (1–10 одновременных на строку).
- Конфликт — **редкость**. Если происходит — ок, пользователь «обновит и попробует ещё раз».
- Нет жёсткого SLA на латентность (retry — это +1 round-trip).

#### Когда нет

- Конкурентность **высокая** (100+ одновременных на одну строку). Оптимистичная блокировка → 90% конфликтов → 90% retry → деградация.
- Операция **долга** (10 секунд между SELECT и UPDATE). Версия почти наверняка устала.

---

### 2. Pessimistic Locking (SELECT FOR UPDATE)

#### Механизм

```sql
BEGIN;
SELECT * FROM orders WHERE id = 42 FOR UPDATE;
-- ← Строка заблокирована. Другие процессы ждут.

-- Проверка инварианта:
-- IF status != 'draft' → ROLLBACK.
-- IF balance < amount → ROLLBACK.

UPDATE orders SET status = 'confirmed', total = 5000 WHERE id = 42;
COMMIT;
-- ← Блокировка снята.
```

В PHP:

```php
final class OrderApplicationService
{
    public function confirm(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) {
            $entity = $em->createQueryBuilder()
                ->select('o')
                ->from(OrderEntity::class, 'o')
                ->where('o.id = :id')
                ->setParameter('id', $orderId)
                ->setLockMode(LockMode::OPTIMISTIC)  // ← нет, это не то
                ->getQuery()
                ->getOneResult();

            // Правильно:
            $entity = $em->getRepository(OrderEntity::class)
                ->createQueryBuilder('o')
                ->where('o.id = :id')
                ->setParameter('id', $orderId)
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)  // ← FOR UPDATE
                ->getQuery()
                ->getOneResult();

            $order = $entity->toDomain();
            $order->confirm();   // доменная валидация
            $entity->setStatus($order->status());

            $em->flush();
            // COMMIT внутри transactional()
        });
    }
}
```

Сгенерированный SQL:

```sql
BEGIN;
SELECT id, status, total_cents, version
FROM orders
WHERE id = 42
FOR UPDATE;          -- ← EXCLUSIVE lock на строку

UPDATE orders SET status = 'confirmed' WHERE id = 42;
COMMIT;
```

#### Что это даёт

| | |
|---|---|
| + Простая семантика: «я читаю, я решаю, я пишу». Никто не вмешается. | − **Блокировка**. Второй процесс ждёт. |
| + Нет retry-логики. Конфликт = ожидание. | − Deadlock-риск при нескольких строках. |
| + Работает при высокой конкурентности (очередь на lock). | − Long lock = long wait. Если транзакция 10 сек → 100 процессов × 10 сек = очередь. |
| + Инварианты проверяются **под lock'ом**. Невозможно «между SELECT и UPDATE». | − Если процесс упал (kill -9) → lock удерживается до `deadlock_timeout` / `lock_timeout`. |

#### Когда

- Конкурентность **средняя** (10–100 одновременных на строку).
- Операция **короткая** (< 1 секунда).
- Инвариант **сложный** (зависит от нескольких полей, внешнего состояния).
- «First write wins» — доменное требование.

#### Когда нет

- Конкурентность **высокая** (1000+ на строку). Очередь на lock растёт.
- Операция **долгая** (> 5 сек). Блокировка держится долго.
- Есть **внешние вызовы** (HTTP, queue) в пределах lock. **Никогда** не делать HTTP внутри `FOR UPDATE`.

---

### 3. Serialization через очередь (application-level)

#### Механизм

```
Вместо:  N процессов одновременно пишут в order_id = 42
Становится:  N задач в очередь → 1 воркер обрабатывает их последовательно
```

```php
// Вместо:
$this->orderService->addLine($orderId, $product, $qty);
$this->orderService->confirm($orderId);

// Становится:
$this->messenger->dispatch(new AddLineCommand($orderId, $product, $qty));
$this->messenger->dispatch(new ConfirmCommand($orderId));

// Конфиг:
framework:
    messenger:
        routing:
            'App\Command\AddLineCommand':   order_queue   # 1 воркер
            'App\Command\ConfirmCommand':   order_queue   # та же очередь
```

```yaml
# 1 воркер на очередь:
# php bin/console messenger:consume order_queue --limit=1
# 1 воркер = последовательная обработка = нет конкурентности.
```

#### Что это даёт

| | |
|---|---|
| + Ноль блокировок. Ноль lock'ов. | − Задержка: задачи ждут в очереди. |
| + Ноль deadlocks. | − Сложность: мониторинг очереди, DLQ, идемпотентность. |
| + Горизонтальное масштабирование (N воркеров на N разных order_id). | − Не масштабируется **на один** order_id (1 воркер = 1 задача за раз). |
| + Инварианты проверяются последовательно. Нет «двух одновременных». | − Если воркер упал → задача в DLQ. Нужно обрабатывать. |

#### Когда

- Конкурентность **очень высокая** (1000+ на строку).
- Операции **долгие** (> 5 сек).
- Есть **внешние вызовы** (HTTP, 3rd-party API).
- Доменная модель — **команды** (CQRS): `AddLine`, `Confirm`, `Cancel` — отдельные операции.

#### Когда нет

- Нужен **синхронный ответ** пользователю («я добавил строку, подтвердите»). Очередь = асинхронность = «принято, подождите».
- Низкая конкурентность (1–5). Избыточная сложность.

---

### 4. Domain-level conflict resolution (merge)

#### Механизм

Вместо «один перезаписал другого» — **домен решает**, как объединить два изменения.

```php
// Корень агрегата знает обо всех изменениях:
final class Order
{
    public function mergeChange(OrderChange $change): self
    {
        return match ($change->type) {
            'add_line'    => $this->addLine($change->product, $change->qty, $change->price),
            'remove_line' => $this->removeLine($change->lineId),
            'confirm'     => $this->confirm(),
            'cancel'      => $this->cancel($change->reason),
        };
    }
}

// При загрузке из БД:
$order = $repo->find($id);
foreach ($pendingChanges as $change) {
    $order = $order->mergeChange($change);   // домен решает, что делать
}
$repo->save($order);
```

Это **не** «last write wins». Это «домен применяет изменения в порядке, который он считает корректным». Если `add_line` и `cancel` пришли одновременно → домен знает, что `cancel` отменяет `add_line`.

#### Когда

- Агрегат имеет **сложную внутреннюю логику** (5+ состояний, 10+ переходов).
- Изменения **зависят друг от друга** (нельзя просто «перезаписать»).
- Есть **приоритеты** (администратор > пользователь).
- CQRS: read-модель и write-модель разделены. Write — через очередь + merge.

#### Когда нет

- Простое «last write wins» (изменение адреса, имени, email).
- Нет сложной логики переходов.

---

## Как выбрать: дерево решений

```
Два процесса пишут в один агрегат.

Q1: Конфликт — редкость (< 1% запросов)?
  → Да: Optimistic Lock (version). Просто. Без блокировок.
  → Нет: Q2.

Q2: Операция короткая (< 1 сек) и нет внешних вызовов?
  → Да: Pessimistic Lock (FOR UPDATE). Простая семантика.
  → Нет: Q3.

Q3: Можно сделать асинхронно? (пользователь получит ответ через 1–5 сек)
  → Да: Очередь (1 воркер на агрегат). Ноль блокировок.
  → Нет: Q4.

Q4: Изменения зависят друг от друга (нельзя «просто перезаписать»)?
  → Да: Domain-level merge. Сложно, но правильно.
  → Нет: Pessimistic Lock + короткая транзакция.
```

---

## Конкретный пример: баланс счёта

### Доменная модель

```php
final readonly class Money
{
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {
        if ($amountCents < 0) throw new DomainException('Negative');
    }
}

final class Account
{
    private int $id;
    private Money $balance;
    private int $version;   // для optimistic locking

    public function debit(Money $amount): self
    {
        if ($this->balance->amountCents < $amount->amountCents) {
            throw new InsufficientFundsException($this->balance, $amount);
        }
        $this->balance = new Money(
            $this->balance->amountCents - $amount->amountCents,
            $this->balance->currency
        );
        return $this;
    }

    public function credit(Money $amount): self
    {
        $this->balance = new Money(
            $this->balance->amountCents + $amount->amountCents,
            $this->balance->currency
        );
        return $this;
    }
}
```

### Вариант A: Pessimistic Lock (для 10–100 RPS на один счёт)

```php
final class AccountService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function debit(int $accountId, Money $amount): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) {
            // FOR UPDATE: блокирует строку.
            $entity = $em->createQueryBuilder()
                ->select('a')
                ->from(AccountEntity::class, 'a')
                ->where('a.id = :id')
                ->setParameter('id', $accountId)
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)   // ← FOR UPDATE
                ->getQuery()
                ->getOneResult();

            $account = $entity->toDomain();
            $account->debit($amount);   // доменная валидация под lock'ом

            $entity->setBalance($account->balance());
            $entity->bumpVersion();

            $em->flush();
            // COMMIT → lock снят.
        });
    }
}
```

SQL:

```sql
BEGIN;
SELECT * FROM accounts WHERE id = 42 FOR UPDATE;  -- ← блокировка
-- Процесс B: SELECT ... FOR UPDATE → ЖДЁТ.
UPDATE accounts SET balance_cents = 50, version = version + 1 WHERE id = 42;
COMMIT;
-- Процесс B: получает lock, читает balance = 50, валидирует.
```

**Ключевое:** валидация (`debit` → `if balance < amount`) происходит **под lock'ом**. Процесс B не может прочитать «старый» баланс.

### Вариант B: Optimistic Lock (для низкой конкурентности)

```php
final class AccountService
{
    public function debit(int $accountId, Money $amount): void
    {
        $maxRetries = 3;
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $this->em->transactional(function (EntityManagerInterface $em) {
                    $entity = $em->find(AccountEntity::class, $accountId);
                    $account = $entity->toDomain();
                    $account->debit($amount);   // может бросить InsufficientFunds
                    $entity->setBalance($account->balance());
                    $em->flush();
                    // UPDATE ... WHERE id = 42 AND version = 7
                    // Если version ≠ 7 → OptimisticLockException
                });
                return;   // успех
            } catch (OptimisticLockException $e) {
                if ($i === $maxRetries - 1) throw $e;
                usleep(random_int(1000, 5000));   // exponential backoff
            }
        }
    }
}
```

**Trade-off:** при 100 одновременных `debit` на один счёт → 99 конфликтов → 99 retry. Каждый retry = новый SELECT + UPDATE. При 5 мс на round-trip → 99 × 5 мс = 500 мс. Очередь. Но **без блокировок**.

### Вариант C: Очередь (для 1000+ RPS на один счёт)

```php
// Команда:
final readonly class DebitCommand
{
    public function __construct(
        public readonly int   $accountId,
        public readonly int   $amountCents,
        public readonly string $currency,
        public readonly string $idempotencyKey,
    ) {}
}

// Хендлер (1 воркер):
final class DebitHandler
{
    #[AsMessageHandler]
    public function __invoke(DebitCommand $cmd): void
    {
        // Идемпотентность: если уже обработали → skip.
        if ($this->processedRepo->exists($cmd->idempotencyKey)) return;

        $this->accountService->debit($cmd->accountId,
            new Money($cmd->amountCents, $cmd->currency));

        $this->processedRepo->mark($cmd->idempotencyKey);
    }
}
```

```yaml
# 1 воркер на account:
# php bin/console messenger:consume account_queue --limit=1
# НО: если 1000 accounts → 1000 очередей → 1000 воркеров.
# Альтернатива: sharding по accountId % N.
```

**Ключевое:** идемпотентность через `idempotencyKey`. Если воркер обработал задачу, но подтверждение потеряно → повторный delivery → `exists()` → skip.

---

## Что делать с внешними вызовами

### Антипаттерн

```php
$this->em->transactional(function ($em) {
    $entity = /* FOR UPDATE */;

    $result = $this->stripe->charge($order);   // ← HTTP, 10 сек
    // ↑ Lock на accounts удерживается 10 секунд.
    //   100 пользователей × 10 сек = 1000 сек блокировок.
    //   Очередь на lock. Все ждут.

    $entity->debit($amount);
    $em->flush();
});
```

### Правильно

```php
// 1. Резервируем (короткая транзакция):
$this->em->transactional(function ($em) {
    $entity = /* FOR UPDATE */;
    $account = $entity->toDomain();

    // Проверяем: хватает ли?
    if ($account->balance()->amountCents < $amount->amountCents) {
        throw new InsufficientFundsException(...);
    }

    // Резервируем (не списываем):
    $account->reserve($amount);   // balance -= amount, но status = 'reserved'
    $entity->setBalance($account->balance());
    $em->flush();
    // COMMIT. Lock снят. 50 мс.
});

// 2. Платим (вне транзакции):
$result = $this->stripe->charge($order);   // 10 сек. Никто не заблокирован.

// 3. Подтверждаем или отменяем (короткая транзакция):
$this->em->transactional(function ($em) {
    $entity = /* FOR UPDATE */;
    $account = $entity->toDomain();

    if ($result->success) {
        $account->confirmDebit();   // reserved → debited
    } else {
        $account->releaseReserve(); // reserved → balance += amount
    }

    $entity->setBalance($account->balance());
    $em->flush();
});
```

**Три короткие транзакции** вместо одной на 10 секунд. Lock удерживается 50 мс, а не 10 000 мс.

---

## MVCC и «невидимые» обновления

PostgreSQL MVCC: при `UPDATE` строка не «меняется на месте». Создаётся **новая версия** (новый `ctid`). Старая версия остаётся для других транзакций.

```
t=0:  Process A: BEGIN; SELECT balance → 150 (ctid = (0,1))
t=1:  Process B: BEGIN; UPDATE balance = 100; COMMIT.
      → Новая версия: (0,2). balance = 100.
      → Старая версия (0,1) видна Process A.
t=2:  Process A: UPDATE balance = 50 WHERE id = 42;
      → A пишет новую версию (0,3) на основе (0,1). balance = 50.
      → B's версия (0,2) = 100. Потеряна.
t=3:  Process A: COMMIT.
```

**MVCC не защищает от lost update.** Он защищает от «чтения半成品». Для защиты — `FOR UPDATE` (pessimistic) или `version` (optimistic).

`FOR UPDATE` **обходит** MVCC: он берёт lock на **актуальную** версию. Если другая транзакция уже `COMMIT`-нула — вы читаете **новую** версию. Если ещё не `COMMIT`-нула — ждёте.

---

## Итого

| Ситуация | Решение | Почему |
|---|---|---|
| 1–10 RPS на строку, конфликт редок | **Optimistic (version)** | Просто. Без блокировок. |
| 10–100 RPS, операция < 1 сек | **Pessimistic (FOR UPDATE)** | Простая семантика. Короткий lock. |
| 1000+ RPS, операция > 5 сек | **Очередь + 1 воркер** | Ноль блокировок. Масштабируется. |
| Сложные переходы состояний | **Domain merge** | Домен решает, а не БД. |
| Внешний HTTP в операции | **Разделить: резерв → вызов → подтверждение** | Не держать lock на 10 сек. |
| `CHECK (balance >= 0)` | **Недостаточно** | Ловит «отрицательный». Не ловит «потерянное списание». |

**Принцип:** агрегат — единица конкурентности. Lock — на строку агрегата. Валидация — под lock'ом (или через version). Внешние вызовы — вне lock'а. Идемпотентность — на уровне команды.

Это не «выбери один паттерн». Это **композиция**: version + FOR UPDATE + очередь + идемпотентность, каждый на своём уровне.

> ⏱ 61.9s · prompt 68539 tok · output 7404 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в DDD: когда публиковать, как обрабатывать, какие проблемы решают/создают, и связь с транзакциями БД. Это продолжение длинной беседы о PHP/Symfony/DDD.

Мне нужно:
1. Разграничить: доменные события vs прикладные события vs интеграционные события
2. Показать когда публиковать (после успешной операции агрегата, не до)
3. Показать как обрабатывать (sync vs async, in-process vs out-of-process)
4. Показать связь с транзакциями (outbox pattern, transactional outbox)
5. Показать проблемы (duplicates, ordering, lost events, testing)
6. Дать конкретный код на Symfony/PHP
7. Быть честным о trade-offs

Структура:
- Что такое доменное событие (определение)
- Три типа событий (domain, application, integration)
- Когда публиковать (в агрегате vs в сервисе)
- Как обрабатывать (sync, async, outbox)
- Связь с транзакциями (ключевая часть)
- Outbox pattern
- Проблемы
- Антипаттерны
- Практический пример

Я должен быть последовательным с предыдущими ответами: уже обсуждали Messenger, outbox, идемпотентность, агрегаты. Не повторять то, что уже сказал, а углублять.

Ключевой инсайт, который я хочу донести: доменное событие — это НЕ "уведомление другим сервисам". Это факт, который уже произошёл внутри агрегата. Он фиксирует состояние. Публикация — это фиксация факта в том же транзакционном блоке, что и изменение состояния.

Связь с транзакциями — самый важный момент: если событие опубликовано, а flush() не произошёл (или откатился) — событие "утеряно". Если flush() произошёл, а событие не опубликовано — система рассинхронизирована. Решение: outbox.

Буду писать структурированно, с кодом, с таблицами trade-offs, как в предыдущих ответах.

</details>

**Кандидат:**

# Доменные события: публикация, обработка, транзакции

## Что такое доменное событие

Доменное событие — это **факт**, который уже произошёл внутри агрегата. Не «собираюсь сделать», не «попроси меня сделать». Прошлое время: `OrderConfirmed`, `PaymentSettled`, `SubscriptionCancelled`.

```php
final readonly class OrderConfirmed
{
    public function __construct(
        public readonly int    $orderId,
        public readonly int    $totalCents,
        public readonly string $currency,
        public readonly int    $lineCount,
        public readonly \DateTimeImmutable $at,
    ) {}
}
```

Это не команда. Не запрос. Не «отправь email». Это **запись в журнале**: «в момент T заказ 42 был подтверждён на сумму 5000 RUB из 3 строк».

Ключевое свойство: **оно уже случилось**. Его нельзя «отменить». Нельзя «отдёрнуть» подтверждение заказа. Можно создать новый факт: `OrderCancelled`.

---

## Три типа событий (не путать)

| Тип | Пример | Куда идёт | Кому нужен |
|---|---|---|---|
| **Domain event** | `OrderConfirmed` | Внутри процесса / в outbox | Слушатели того же bounded context |
| **Application event** | `OrderCreatedNotification` | В очередь, email, push | Временные эффекты, не домен |
| **Integration event** | `PaymentSettled` (для другого сервиса) | Kafka / RabbitMQ / HTTP | Внешние сервисы |

```
OrderConfirmed (domain)
  → SendWelcomeEmail (application)
  → UpdateSearchIndex (application)
  → PaymentService (integration: «подтверди платёж»)
```

Доменное событие — **источник**. Прикладное и интеграционное — **производные**. Не путайте: `OrderConfirmed` не содержит «email = x@y.com». Это данные для email-слушателя, не для доменного факта.

---

## Где рождается событие

### Правильно: в агрегате (корне)

```php
final class Order
{
    /** @var list<object> */
    private array $pendingEvents = [];

    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new DomainException('Only draft can be confirmed');
        }
        if (empty($this->lines)) {
            throw new DomainException('Order has no lines');
        }

        $this->status = OrderStatus::Confirmed;

        // Событие рождается ВНЕ aggregata, как факт:
        $this->pendingEvents[] = new OrderConfirmed(
            orderId:    $this->id,
            totalCents: $this->total()->amountCents,
            currency:   $this->total()->currency,
            lineCount:  count($this->lines),
            at:         new \DateTimeImmutable(),
        );

        return $this;
    }

    /** @return list<object> */
    public function releaseEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}
```

### Почему в агрегате, а не в сервисе

```php
// ❌ В сервисе:
final class OrderService
{
    public function confirm(int $id): void
    {
        $order = $this->repo->find($id);
        $order->confirm();
        $this->repo->save($order);

        // «А событие?»
        $this->events->dispatch(new OrderConfirmed(...));  // ← а если confirm() бросил?
        // «А если save() бросил? Событие уже отправлено!»
    }
}

// ✅ В агрегате:
// Событие привязано к мутации. Если мутация не произошла (throw) — события нет.
// Если мутация произошла — событие гарантированно в pendingEvents.
// Сервис НЕ может забыть «отправить событие», потому что оно уже привязано к объекту.
```

Принцип: **событие — часть состояния агрегата**, пока не «выпущено». Корень решает, **когда** и **какое** событие произошло. Сервис решает, **куда** его доставить.

---

## Как публикуется

### Шаг 1: Агрегат «накапливает» события

```php
$order->addLine($product, 2, $price);   // события нет (line добавлен, но заказ не подтверждён)
$order->confirm();                       // OrderConfirmed в pendingEvents
```

### Шаг 2: Application Service выпускает и доставляет

```php
final class OrderApplicationService
{
    public function __construct(
        private EntityManagerInterface  $em,
        private OrderEntityRepository   $repo,
        private EventDispatcher         $dispatcher,   // Symfony EventDispatcher
    ) {}

    public function confirmOrder(int $orderId): void
    {
        $entity = $this->repo->find($orderId)
            ?? throw new NotFoundHttpException("Order {$orderId} not found");

        $order = $entity->toDomain();
        $order->confirm();

        $entity->setStatus($order->status());
        $this->em->persist($entity);

        // ─── КЛЮЧЕВОЙ МОМЕНТ: события и данные — в ОДНОЙ транзакции ───
        // Сначала flush (данные в БД). Потом события.
        // Никогда наоборот.
        $this->em->flush();

        // Выпускаем события ТОЛЬКО если flush прошёл успешно:
        foreach ($order->releaseEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

        $this->em->clear();
    }
}
```

**Порядок: flush → dispatch → clear.**

Почему:
- `flush()` → данные в БД. COMMIT. Если flush упал — данных нет. События не должны быть отправлены.
- `dispatch()` → после COMMIT. Если dispatch упал — данные уже в БД. Это **ок** (см. outbox ниже).
- `clear()` → сброс UoW.

### Что делает Symfony EventDispatcher

```php
$this->dispatcher->dispatch($event);
```

Symfony ищет **все** registered listeners для `OrderConfirmed` и вызывает их **синхронно**, в рамках того же PHP-процесса, того же request:

```php
// App/EventListener/OnOrderConfirmed.php
final class OnOrderConfirmed
{
    public function __construct(private SearchClient $search) {}

    #[AsEventListener(event: OrderConfirmed::class)]
    public function __invoke(OrderConfirmed $event): void
    {
        $this->search->upsert('orders', $event->orderId, [
            'total' => $event->totalCents,
            'status' => 'confirmed',
        ]);
    }
}
```

Все listeners вызываются **последовательно**, в **том же процессе**, в **той же транзакции** (если `flush()` ещё не `COMMIT`-нул).

**Важно:** Symfony EventDispatcher **не** является брокером. Это in-process dispatch. Если listener бросит исключение — остальные listeners **не вызываются** (по умолчанию). Это поведение можно менять через `stopPropagation()`.

---

## Синхронные vs асинхронные слушатели

| | Синхронные (in-process) | Асинхронные (через очередь) |
|---|---|---|
| Механизм | `EventDispatcher->dispatch()` | `Messenger->dispatch()` → queue |
| Когда выполняется | В рамках того же request | Отдельный воркер, через 0–N секунд |
| Если listener упал | Остальные не вызываются. Exception propagates. | Message в DLQ. Остальные обработаны. |
| Latency для пользователя | **Зависит** от всех listeners | **Не зависит** (dispatch = 1 write в очередь) |
| Consistency | Strong (все в одной транзакции) | Eventual (0–N сек разрыв) |
| Масштабируемость | Ограничена (1 процесс) | Горизонтальная (N воркеров) |
| Тестирование | Простое (mock'и в одном процессе) | Сложнее (очередь, идемпотентность) |

### Когда синхронный

- Listener **обязателен** для correctness. «Если поисковый индекс не обновлён — страница покажет неверные данные».
- Latency не критична (50–200 мс допустимо).
- Мало listeners (1–3).
- Нет внешних HTTP-вызовов в listener.

### Когда асинхронный

- Listener — **side-effect**. «Email не пришёл — пользователь подождёт, но заказ подтверждён».
- Latency критична (ответ пользователю < 100 мс).
- Много listeners (5+).
- Внешние HTTP-вызовы (SMTP, Stripe, Meilisearch).
- Нужны ретраиты.

### В Symfony: как переключить

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            # Асинхронно:
            'App\Domain\Event\OrderConfirmed': async

            # Синхронно:
            'App\Domain\Event\OrderLineAdded': sync
```

```php
// Вместо:
$this->dispatcher->dispatch($event);

// Асинхронно через Messenger:
$this->messenger->dispatch($event);
// → сообщение в очередь. Воркер подхватит.
```

**Разница:** `EventDispatcher` — in-process. `Messenger` — out-of-process (через транспорт: Redis, RabbitMQ, Symfony Doctrine transport).

---

## Связь с транзакциями: главная проблема

### Проблема

```php
$this->em->flush();          // ← COMMIT. Данные в БД.
$this->dispatcher->dispatch($event);   // ← listener упал. Exception.

// Итог: данные в БД. Событие НЕ доставлено.
// Система рассинхронизирована:
//   • Заказ подтверждён в БД.
//   • Email не отправлен.
//   • Поисковый индекс не обновлён.
//   • Интеграционный сервис не уведомлён.
//   • Никто не знает, что событие «потерялось».
```

Или хуже:

```php
$this->dispatcher->dispatch($event);   // ← listener выполнил HTTP-вызов. Email ушёл.
$this->em->flush();                    // ← flush упал. Откат.

// Итог: Email отправлен. Заказа в БД НЕТ.
// Пользователь получил «Ваш заказ подтверждён».
// А в системе его нет.
```

**Оба сценария — результат:** события и данные — в **разных транзакциях**. Нет атомарности.

### Решение: Transactional Outbox

**Принцип:** событие записывается в БД **в той же транзакции**, что и данные. Отдельный процесс читает outbox и доставляет.

```
┌─────────────────────────────────────────────────────────────┐
│  Транзакция (АТОМАРНАЯ)                                      │
│                                                             │
│  BEGIN;                                                     │
│  UPDATE orders SET status = 'confirmed' WHERE id = 42;      │
│  INSERT INTO outbox (event_name, payload, created_at)       │
│       VALUES ('OrderConfirmed', '{"orderId":42,...}', now());│
│  COMMIT;                                                    │
│                                                             │
│  → Данные И событие — в БД. Или ни того, ни другого.        │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│  Outbox Relay (отдельный процесс)                            │
│                                                             │
│  1. SELECT * FROM outbox WHERE processed_at IS NULL         │
│     ORDER BY created_at LIMIT 100;                           │
│                                                             │
│  2. Для каждого события:                                     │
│     → отправить в RabbitMQ / Kafka / вызвать HTTP           │
│                                                             │
│  3. UPDATE outbox SET processed_at = now(), attempts = 1    │
│     WHERE id = ...;                                         │
│                                                             │
│  4. Повторять.                                               │
└─────────────────────────────────────────────────────────────┘
```

### Как реализовать

#### 1. Таблица outbox

```sql
CREATE TABLE outbox (
    id            BIGSERIAL PRIMARY KEY,
    event_name    VARCHAR(255) NOT NULL,
    payload       JSONB NOT NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),
    processed_at  TIMESTAMPTZ,
    attempts      INT NOT NULL DEFAULT 0,
    last_error    TEXT
);

CREATE INDEX idx_outbox_pending
    ON outbox (created_at)
    WHERE processed_at IS NULL;
```

#### 2. Запись в outbox (в той же транзакции)

```php
final class OrderApplicationService
{
    public function __construct(
        private EntityManagerInterface  $em,
        private OrderEntityRepository   $repo,
    ) {}

    public function confirmOrder(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) {
            $entity = $em->getRepository(OrderEntity::class)->find($orderId)
                ?? throw new NotFoundHttpException("Order {$orderId} not found");

            $order = $entity->toDomain();
            $order->confirm();

            $entity->setStatus($order->status());
            $em->persist($entity);

            // ─── События в outbox. В той же транзакции. ───
            foreach ($order->releaseEvents() as $event) {
                $outbox = new OutboxEntity();
                $outbox->setEventName(get_class($event));
                $outbox->setPayload(json_encode($event->toArray()));
                $em->persist($outbox);
            }

            // Один flush. Один COMMIT. Данные + события — атомарно.
            $em->flush();
        });

        $this->em->clear();
    }
}
```

**Ключевое:** `INSERT INTO outbox` и `UPDATE orders` — в **одном** `BEGIN...COMMIT`. Если один откатился — оба. Если один записался — оба.

#### 3. Outbox Relay

```php
#[Command('outbox:relay')]
final class OutboxRelayCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private Messenger              $messenger,   // или AmqpPublisher
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = 50;

        while (true) {
            $rows = $this->em->createQueryBuilder()
                ->select('o')
                ->from(OutboxEntity::class, 'o')
                ->where('o.processedAt IS NULL')
                ->andWhere('o.attempts < :maxAttempts')
                ->setParameter('maxAttempts', 10)
                ->orderBy('o.createdAt', 'ASC')
                ->setMaxResults($batchSize)
                ->getQuery()
                ->getResult();

            if (empty($rows)) {
                usleep(500_000);   // 500 мс. Нет событий — ждём.
                continue;
            }

            $this->em->transactional(function (EntityManagerInterface $em) use ($rows) {
                foreach ($rows as $row) {
                    $eventClass = $row->getEventName();
                    $payload = json_decode($row->getPayload(), true);

                    // Восстановляем объект события:
                    $event = new $eventClass(...$payload);

                    try {
                        // Отправляем в брокер (RabbitMQ / Kafka):
                        $this->messenger->dispatch($event);

                        $row->setProcessedAt(new \DateTimeImmutable());
                        $row->setAttempts($row->getAttempts() + 1);
                    } catch (\Throwable $e) {
                        $row->setAttempts($row->getAttempts() + 1);
                        $row->setLastError($e->getMessage());
                        // НЕ throw. Продолжаем с другим событием.
                        // Это событие будет повторено при следующем цикле.
                    }

                    $em->persist($row);
                }
                $em->flush();
            });

            // Короткая пауза между батчами:
            usleep(10_000);   // 10 мс
        }
    }
}
```

Запуск:

```bash
# systemd service / Docker container
php bin/console outbox:relay --quiet
```

#### 4. Потребление (в отдельном сервисе / воркере)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Domain\Event\OrderConfirmed': amqp
```

```php
// Воркер (отдельный контейнер / процесс):
# php bin/console messenger:consume amqp
```

```php
// Listener:
final class OnOrderConfirmed
{
    public function __construct(private SearchClient $search) {}

    #[AsMessageHandler]
    public function __invoke(OrderConfirmed $event): void
    {
        $this->search->upsert('orders', $event->orderId, [
            'total'  => $event->totalCents,
            'status' => 'confirmed',
        ]);
    }
}
```

---

## Сравни: три подхода

| | Direct dispatch | Outbox | Outbox + DLQ |
|---|---|---|---|
| Атомарность данных + события | ❌ | ✅ | ✅ |
| Ретраиты при сбое доставки | ❌ | ✅ (attempts) | ✅ (DLQ) |
| Задержка доставки | 0 мс | 0–500 мс | 0–500 мс |
| Сложность | Низкая | Средняя | Высокая |
| Потеря событий при crash | ✅ (возможна) | ❌ (не возможна) | ❌ (не возможна) |
| Масштабируемость | Низкая | Средняя | Высокая |
| Наблюдаемость | Низкая | Средняя | Высокая |

**Когда Direct dispatch:** прототип, внутренний инструмент, 1 listener, нет внешних вызовов.

**Когда Outbox:** production, > 1 listener, внешние вызовы, требования к reliability.

**Когда Outbox + DLQ:** > 10 000 событий/день, разные SLA на события, нужна ручная обработка «зависших».

---

## Проблемы, которые события решают

| Проблема | Как события решают |
|---|---|
| **Жёсткая сцепка сервисов** | `OrderService` не знает, что `SearchService` существует. Событие — контракт. |
| **Каскадные изменения** | Добавление `NotificationService` — новый listener. Ни один существующий файл не меняется. |
| **Асинхронность** | Email не замедляет ответ. Поисковый индекс обновляется в фоне. |
| **Ретраиты** | SMTP упал → retry. Search упал → retry. Заказ подтверждён. |
| **Наблюдаемость** | `SELECT * FROM outbox WHERE processed_at IS NULL` — все недоставленные события. |
| **Аудит** | Outbox — журнал. Кто, когда, что произошло. |

---

## Проблемы, которые события создают

### 1. Дубликаты

```
Outbox relay отправил событие в RabbitMQ.
RabbitMQ подтвердил (ACK).
Relay упал ДО того, как пометить outbox.processed_at.
При рестарте relay читает outbox → событие НЕ помечено → отправляет ещё раз.
```

**Решение:** идемпотентность потребителя.

```php
final class OnOrderConfirmed
{
    public function __construct(
        private SearchClient  $search,
        private EventDedup    $dedup,   // Redis: SET key = event_id, TTL = 24h
    ) {}

    #[AsMessageHandler]
    public function __invoke(OrderConfirmed $event): void
    {
        // Идемпотентность: если уже обработали → skip.
        $key = 'processed:' . $event->orderId . ':' . $event->at->getTimestamp();
        if ($this->dedup->exists($key)) return;

        $this->search->upsert('orders', $event->orderId, [...]);
        $this->dedup->mark($key);
    }
}
```

**Принцип:** при at-least-once доставке (RabbitMQ, Kafka, outbox relay) **потребитель обязан быть идемпотентным**. Это не опция. Это требование.

### 2. Потеря порядка

```
Outbox:
  1. OrderLineAdded (order_id = 42)
  2. OrderConfirmed (order_id = 42)

Relay:
  → 2 доставлено раньше (параллельные воркеры).

Потребитель:
  → OrderConfirmed: «заказ 42 подтверждён».
  → OrderLineAdded: «добавить строку в подтверждённый заказ».
  → DomainException: «Only draft can have lines added».
```

**Решение:**

| Подход | Когда |
|---|---|
| Очередь на агрегат (1 воркер на order_id) | Если порядок критичен |
| Потребитель игнорирует «прошлые» события (idempotent + state check) | Если порядок не критичен |
| Kafka + partition key = order_id | Если порядок критичен И нужен throughput |

**Честно:** в 90% случаев «OrderLineAdded после OrderConfirmed» — это баг в домене (нельзя добавить строку в подтверждённый заказ). Потребитель должен **отбросить** такое событие, а не падать.

```php
public function __invoke(OrderLineAdded $event): void
{
    $order = $this->repo->find($event->orderId);
    if ($order->status() !== OrderStatus::Draft) {
        // Не бросаем. Логгируем. Игнорируем.
        $this->logger->warning('LineAdded for non-draft order', ['orderId' => $event->orderId]);
        return;
    }
    // ...
}
```

### 3. «Зависшие» события

```
Outbox: attempts = 10, processed_at IS NULL.
Ретраиты исчерпаны. Событие не доставлено. Никто не знает.
```

**Решение:**

```sql
-- Алерт:
SELECT event_name, payload, created_at, last_error
FROM outbox
WHERE processed_at IS NULL AND attempts >= 10;
```

```bash
# Cron: каждые 5 минут
SELECT pg_notify('outbox_alert', count(*)::text)
FROM outbox
WHERE processed_at IS NULL AND attempts >= 10;
```

Альтернатива: DLQ-очередь в RabbitMQ. Событие, исчерпавшее ретраиты, уходит в DLQ. Отдельный процесс / интерфейс для ручной обработки.

### 4. Тестирование

| Подход | Трудность |
|---|---|
| Sync dispatch | Просто: mock'и в одном процессе. Assert на listener. |
| Async (Messenger) | Нужен транспорт в тесте (in-memory / Doctrine transport). |
| Outbox | Нужно: 1) assert, что запись в outbox есть. 2) запустить relay. 3) assert, что событие доставлено. |
| Outbox + DLQ | + 4) сценарий сбоев. + 5) идемпотентность. |

**Практика:** unit-тесты — sync. Интеграционные — outbox + in-memory транспорт. E2E — реальный RabbitMQ (Docker).

```php
// Unit-тест:
public function test_confirm_releases_event(): void
{
    $order = Order::activate(1, 2);
    $order->confirm();

    $events = $order->releaseEvents();
    $this->assertCount(1, $events);
    $this->assertInstanceOf(OrderConfirmed::class, $events[0]);
    $this->assertSame(1, $events[0]->orderId);
}

// Интеграционный:
public function test_confirm_persists_and_outboxes(): void
{
    $this->service->confirmOrder(42);

    $this->assertDatabaseHas('orders', ['id' => 42, 'status' => 'confirmed']);
    $this->assertDatabaseHas('outbox', [
        'event_name' => OrderConfirmed::class,
        'processed_at' => null,
    ]);
}
```

### 5. «Событие без агрегата»

```php
// ❌ Антипаттерн:
public function handleOrder(int $id): void
{
    $this->em->persist($order);
    $this->em->flush();
    $this->dispatcher->dispatch(new OrderConfirmed(...));
    $this->em->flush();   // ← второй flush. Зачем?
}
```

Событие **не** «запускает» агрегат. Событие **фиксирует** факт, что агрегат изменился. Если вы dispatch'ите событие, а агрегат не изменился — это ложный факт.

```php
// ✅ Правильно:
$order->confirm();          // агрегат изменился
$entity->setStatus(...);    // инфраструктура синхронизирована
$em->flush();               // COMMIT
foreach ($order->releaseEvents() as $e) {
    $this->dispatcher->dispatch($e);   // факт зафиксирован
}
```

### 6. Событие содержит ID, а не данные

```php
// ❌ Событие содержит только ID:
final class OrderConfirmed
{
    public function __construct(public readonly int $orderId) {}
}

// Потребитель:
public function __invoke(OrderConfirmed $event): void
{
    $order = $this->repo->find($event->orderId);   // ← extra DB hit
    $this->mailer->send($order->email, ...);       // ← данные могли измениться
}
```

**Когда ок:** потребитель **обязан** прочитать актуальные данные (например, «отправить email на актуальный email»).

**Когда нет:** данные были актуальны **на момент события**. Потребитель не должен перечеитывать.

```php
// ✅ Событие содержит данные (snapshot):
final class OrderConfirmed
{
    public function __construct(
        public readonly int    $orderId,
        public readonly int    $totalCents,
        public readonly string $currency,
        public readonly int    $lineCount,
        public readonly string $customerEmail,   // snapshot на момент события
        public readonly \DateTimeImmutable $at,
    ) {}
}
```

**Принцип:** доменное событие — это **snapshot состояния на момент факта**. Не «ID, по которому можно перечесть». Перечитывание — это уже интеграционный паттерн, а не доменное событие.

---

## Антипаттерны

| Антипаттерн | Почему плохо |
|---|---|
| `EventDispatcher` для бизнес-логики | Listener меняет состояние агрегата. «Событие» на самом деле команда. |
| Событие с 20 полями | Это не событие. Это DTO. Разделить. |
| `dispatch()` до `flush()` | Событие отправлено, данные откатились. Рассинхрон. |
| Listener делает `em->flush()` | Listener не знает про транзакцию. `flush()` может «подхватить» чужие dirty-объекты. |
| Событие как команда: `OrderCancelRequested` | Это команда. Не факт. Разделите. |
| 50 listeners на одно событие | Событие = точка расширения. 50 = слишком много. Разделить на 3–4 события. |
| Событие содержит `User` (объект) | Событие — данные, не ссылки. Содержит ID, не объект. |

---

## Итого: полная картина

```
┌─────────────────────────────────────────────────────────────────────┐
│  Агрегат                                                            │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  Order::confirm()                                             │  │
│  │    → status = Confirmed                                       │  │
│  │    → pendingEvents[] = OrderConfirmed(...)                    │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Application Service                                                │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  $order->confirm()                                            │  │
│  │  $entity->setStatus(...)                                      │  │
│  │                                                               │  │
│  │  BEGIN;                                                       │  │
│  │    UPDATE orders SET status = 'confirmed' WHERE id = 42;      │  │
│  │    INSERT INTO outbox VALUES ('OrderConfirmed', '...');       │  │
│  │  COMMIT;                                                      │  │
│  │                                                               │  │
│  │  ← АТОМАРНО. Данные + событие. Или ни того, ни другого.        │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Outbox Relay (отдельный процесс)                                   │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  SELECT * FROM outbox WHERE processed_at IS NULL;             │  │
│  │  → publish to RabbitMQ / Kafka                                │  │
│  │  → UPDATE outbox SET processed_at = now();                    │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Consumers (отдельные воркеры)                                      │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  OnOrderConfirmed → Search upsert (идемпотентно)              │  │
│  │  OnOrderConfirmed → Email send (идемпотентно)                 │  │
│  │  OnOrderConfirmed → PaymentService (интеграция, идемпотентно) │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

**Пять свойств:**

1. **Атомарность:** данные + событие — в одной транзакции.
2. **Доставка:** at-least-once (outbox + retry).
3. **Идемпотентность:** потребитель не падает при дублях.
4. **Наблюдаемость:** `SELECT * FROM outbox WHERE processed_at IS NULL` — все недоставленные.
5. **Разделение:** домен не знает, что кто-то отправляет email. Email не знает, что заказ подтверждён. Контракт — событие.

> ⏱ 65.8s · prompt 81518 tok · output 7788 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

The user asks three interconnected questions about DDD:
1. Value Object vs Entity — what's the difference?
2. How do I decide which concept is a VO and which is an Entity?
3. What is Ubiquitous Language and how does it affect code?

Let me structure this clearly with concrete code examples, since the user has been asking for practical PHP/Symfony examples throughout the conversation. I should build on the patterns we've already discussed (Money, Order, OrderLine, Subscription) to maintain continuity.

For VO vs Entity:
- Identity: Entity has identity (ID), VO is defined by its attributes
- Mutability: Entity is mutable (or at least has a lifecycle), VO is immutable
- Equality: Entity equality by reference/ID, VO equality by value
- Lifecycle: Entity has a lifecycle (created, modified, deleted), VO is created and discarded
- In Doctrine: Entity is mapped to a table, VO is embedded/serialized

For deciding which is which:
- "Can I refer to it by ID?" → Entity
- "Does it have a lifecycle?" → Entity
- "Is it defined by its properties?" → VO
- "Would two instances with the same properties be interchangeable?" → VO
- "Does it make sense to 'change' it?" → Entity
- "Is it a 'thing' or a 'description'?" → Entity vs VO

For Ubiquitous Language:
- The vocabulary of the domain is the vocabulary of the code
- Class names, method names, variable names should match the domain language
- The team (devs + product) uses the same terms
- This eliminates the "translation layer" between business and code

Let me write this in Russian since the conversation is in Russian, with code examples in PHP. I'll use the examples we've already established (Money, Order, OrderLine, Subscription, Customer) for continuity.

Let me structure:
1. VO vs Entity — the core distinction with a comparison table
2. Concrete examples showing both sides
3. Decision framework (questions to ask)
4. Common mistakes
5. Ubiquitous Language — what it is, why it matters, how it affects code
6. Examples of good/bad naming
7. How it connects to the whole architecture

I should be thorough but not repetitive. The user has seen Money, Order, OrderLine already. I'll reference them but focus on the conceptual distinction.

</details>

**Кандидат:**

# Value Object, Entity и единый язык

## Главное различие в одном предложении

**Entity** — это «кто». **Value Object** — это «какой».

Entity идентифицируется **внутренним ID**. Two instances с одинаковыми полями — **разные** сущности.

VO идентифицируется **набором свойств**. Two instances с одинаковыми полями — **одни и те же**.

```php
// Entity: identity by ID
$accountA = new Account(id: 42, name: 'John', balance: 100);
$accountB = new Account(id: 42, name: 'John', balance: 100);
// Это ОДИН и тот же аккаунт. Разные объекты в памяти, одна сущность в домене.

// VO: identity by value
$moneyA = new Money(100, 'RUB');
$moneyB = new Money(100, 'RUB');
// Это ОДНО И ТО ЖЕ значение. Не «два разных». Не «двойник».
// В доменном смысле — неотличимы.
```

---

## Детализация различий

| Критерий | Entity | Value Object |
|---|---|---|
| Идентичность | Внутренний ID (`$this->id`) | Набор свойств (`$amountCents`, `$currency`) |
| Равенство | `===` (reference) или `id ===` | Поля: `amountCents == && currency ==` |
| Изменяемость | Мутабельна (имеет жизненный цикл) | **Неизменяема**. «Изменил» = «создал новый» |
| Жизненный цикл | Создаётся → живёт → меняется → удаляется | Создаётся → используется → отбрасывается |
| Ссылки на неё | Другие сущности хранят **её ID** | Хранится **внутри** сущности (композиция) |
| Таблица в БД | Своя таблица, свой `PK` | Колонки в таблице owner'а (или JSON) |
| Репозиторий | **Есть** (`AccountRepository`) | **Нет**. Не ищут по ID. Не хранят отдельно. |
| Наследование | Возможно (Account → PremiumAccount) | **Нежелательно**. VO — плоский, без иерархии. |
| «Кем он является?» | «Аккаунт №42» | «100 рублей» |
| «Чем он является?» | «Сущность с балансом» | «Значение: сумма + валюта» |

### Пример на одном и том же домене

```php
// ─── ENTITY: имеет ID, жизненный цикл, мутации ────────────────
final class Account
{
    private int $id;                // ← ИДЕНТИЧНОСТЬ
    private string $name;
    private Money $balance;         // ← VO внутри
    private AccountStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    // Жизненный цикл: create → activate → freeze → close
    public function freeze(string $reason): self { /* ... */ }
    public function close(): self { /* ... */ }

    // Мутации: баланс меняется со временем
    public function debit(Money $amount): self { /* ... */ }
    public function credit(Money $amount): self { /* ... */ }

    // Идентичность: два Account с одинаковыми полями — РАЗНЫЕ
    public function id(): int { return $this->id; }
}

// ─── VALUE OBJECT: нет ID, нет цикла, неизменяем ─────────────
final readonly class Money
{
    public function __construct(
        public readonly int    $amountCents,   // ← ИДЕНТИЧНОСТЬ = поля
        public readonly string $currency,
    ) {
        if ($amountCents < 0) throw new DomainException('Negative');
        if ($currency === '') throw new DomainException('Empty');
    }

    // Нет setId(). Нет setName(). Нет lifecycle.
    // «Изменение» = новый объект:
    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->amountCents + $other->amountCents, $this->currency);
    }

    // Равенство по значению:
    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }

    // Нет таблицы. Нет репозитория. Нет «найти по ID».
    // Money(100, 'RUB') — это не «объект с ID 42».
    // Это ЗНАЧЕНИЕ: «сто рублей».
}
```

### Ключевой тест

```php
// Если вы можете заменить один объект другим с тем же значением
// и НИЧЕГО не сломается — это VO.
$payment = new Payment(100, 'RUB', 'card');
$payment2 = new Payment(100, 'RUB', 'card');
// $payment и $payment2 — одно и то же значение.
// Замена одного на другое — незаметна.

// Если вы МОЖЕТЕ заменить один объект другим с тем же значением,
// но это ДРУГАЯ сущность — это Entity.
$accountA = new Account(1, 'John', Money(100, 'RUB'));
$accountB = new Account(2, 'John', Money(100, 'RUB'));
// $accountA и $accountB — РАЗНЫЕ аккаунты.
// У них разный ID. Разная история. Разные транзакции.
// Замена одного на другое — КАРТАСТОРОФА.
```

---

## Как решать: Entity или VO?

### Четыре вопроса

```
Вопрос 1: «Есть ли у него ID, по которому его ищут?»
  → Да: Entity.
  → Нет: VO (или «просто данные»).

Вопрос 2: «Есть ли у него жизненный цикл (создан → изменён → удалён)?»
  → Да: Entity.
  → Нет: VO.

Вопрос 3: «Два экземпляра с одинаковыми полями — это одно и то же?»
  → Да: VO.
  → Нет: Entity.

Вопрос 4: «Имеет ли он историю (кто, когда, что изменил)?»
  → Да: Entity.
  → Нет: VO.
```

### Примеры решений

| Концепция | Решение | Почему |
|---|---|---|
| `Money(100, 'RUB')` | **VO** | Нет ID. Нет цикла. `Money(100,'RUB')` = `Money(100,'RUB')`. |
| `Address('ул. Ленина', 15, 'СПб')` | **VO** | Нет ID. Нет цикла. Два адреса с тем же текстом — один адрес. |
| `Coordinates(55.75, 37.61)` | **VO** | Нет ID. Неизменяем. |
| `Period(2024-01-01, 2024-12-31)` | **VO** | Нет ID. Неизменяем. |
| `Account(42, 'John', 100)` | **Entity** | ID 42. Живёт. Баланс меняется. |
| `Order(7, customer, [lines])` | **Entity** | ID 7. Состояние меняется. Has lifecycle. |
| `OrderLine(15, product, qty, price)` | **Грань** | См. ниже. |
| `Email('john@site.ru')` | **VO** | Нет ID. `Email('john@site.ru')` = `Email('john@site.ru')`. |
| `PhoneNumber('+7 999 123-45-67')` | **VO** | Нет ID. Неизменяем. |
| `CustomerId(42)` | **VO** | Обёртка над int. Идентифицирует **внешнюю** сущность. |

### Случай на границе: OrderLine

```php
final class OrderLine
{
    private int $id;          // ← ЕСТЬ ID. Entity?
    private ProductId $product;
    private int $quantity;
    private Money $unitPrice;

    // НО:
    // • Не имеет репозитория.
    // • Не ищут по ID извне.
    // • Не имеет независимого жизненного цикла.
    // • Живёт и умирает вместе с Order.
    // • Изменяется только через Order.
}
```

**Решение:** `OrderLine` — **внутренняя сущность агрегата**. Формально — Entity (есть ID, есть `id` в БД). Но **не самостоятельная** Entity:

- Нет `OrderLineRepository`.
- Нет `OrderLineService`.
- Внешний мир не знает про `OrderLine`. Знает про `Order`.
- `OrderLine` не имеет независимого инварианта (кроме `qty > 0`, который проверяет `Order`).

Это **child entity** — сущность, которая существует **только** внутри агрегата. В БД — отдельная таблица (для нормализации). В домене — часть корня.

```
Самостоятельная Entity:   Account, Customer, Product
Child Entity:             OrderLine, InvoiceLine, PaymentAttempt
VO:                       Money, Address, Email, ProductId, CustomerId
```

### Случай на границе: ID-обёртки

```php
// ❌ Голый int:
function debit(int $accountId, Money $amount): void { /* ... */ }
// accountId = 42. Но 42 — это что? Account? Order? CustomerId?

// ✅ ID-обёртка (VO):
final readonly class AccountId
{
    public function __construct(public readonly int $value) {}
}

final readonly class CustomerId
{
    public function __construct(public readonly int $value) {}
}

function debit(AccountId $accountId, Money $amount): void { /* ... */ }
// accountId — это AccountId. Не OrderId. Не CustomerId.
// Компилятор (статический анализатор) не даст перепутать.
```

`AccountId(42)` и `CustomerId(42)` — **разные** типы. Даже если внутри оба хранят `42`. Это VO: идентифицируется значением, не имеет жизненного цикла, неизменяем.

---

## Ошибки при выборе

### ❌ «У него есть ID — значит Entity»

```php
// OrderLine имеет ID в БД. Но:
// • Нет репозитория.
// • Нет независимого цикла.
// • Живёт внутри Order.
// → Child Entity. Не самостоятельная.
// Не нужен OrderLineRepository.
// Не нужен OrderLineService.
```

### ❌ «У него нет ID — значит VO»

```php
// UserSession: нет ID? Есть. UUID.
// Но: есть lifecycle (create → expire → invalidate).
// Есть история (когда создан, от какого IP, на каком device).
// → Entity. Не VO.

// PaymentAttempt: нет ID? Есть.
// Lifecycle: pending → settled → failed.
// История: когда, через какой gateway, с каким результатом.
// → Entity. Не VO.
```

### ❌ «VO — это просто DTO»

```php
// DTO: перенос данных. Нет поведения. Нет валидации.
final class OrderDTO {
    public int $id;
    public int $total;
    public string $status;
    // Нет методов. Нет инвариантов. «Просто данные».
}

// VO: перенос данных + ПОВЕДЕНИЕ + ИНВАРИАНТЫ.
final readonly class Money {
    public function __construct(public int $cents, public string $currency) {
        if ($cents < 0) throw new DomainException('Negative');  // инвариант
    }
    public function add(self $other): self { /* ... */ }        // поведение
}
```

VO — это **не** «объект с getter'ами». Это **объект с инвариантами и оперциями**. Если у вас «просто данные» — это DTO. Если «данные + правила» — VO.

### ❌ «Entity — это то, что в БД»

```php
// В БД: orders, order_lines, customers, products, payments
// В домене:
//   Account (Entity)
//   Order (Entity, корень агрегата)
//     OrderLine (child Entity)
//   Customer (Entity, другой контекст)
//   Product (Entity, справочник)
//   Payment (Entity, другой контекст)
//   Money (VO — не в БД как отдельная сущность)
//   CustomerId (VO — обёртка)

// «В БД» ≠ «Entity». Money в БД — колонки. ProductId — колонка.
// Но в домене — VO. Не Entity.
```

---

## Единый язык (Ubiquitous Language)

### Что это

Единый язык — это **совокупность терминов**, которые **одинаково** используются в трёх местах:

1. **Разговор** между product owner'ом и разработчиком.
2. **Код** — имена классов, методов, полей, переменных.
3. **Документация** — ADR, wiki, коммиты.

Термин «подтверждение заказа» в домене = `confirm()` в коде = `POST /orders/{id}/confirm` в API = «подтвердил» в UI. **Одно и то же слово. Во всех местах.**

### Зачем

Без единого языка — **перевод**:

```
Product:  «Заблокируй платёж»
Dev:      «Я делаю hold()»
DBA:      «Я называю колонку payment_status = 'pending'»
Frontend: «Я показываю «Ожидание»»
Тест:     «test_payment_is_in_progress»
Log:      «payment_hold_initiated»
```

Пять слов для одного понятия. Через 6 месяцев:

```
Product:  «А где hold?»
Dev:      «А, это pending.»
DBA:      «А, это in_progress.»
Frontend: «А, это «Ожидание».»
```

Каждый говорит своё. Никто не понимает других. Инцидент: «Платёж в pending 3 дня. А, это hold. А, а почему он не отменился? А, а кто-то изменил status вручную. А, а log-запись называется payment_hold_initiated, а не hold.»

### Как выглядит в коде

#### Хорошо

```php
// Домен: «Подтвердить заказ»
final class Order
{
    public function confirm(): self { /* ... */ }
}

// Событие: «Заказ подтверждён»
final readonly class OrderConfirmed { /* ... */ }

// Команда: «Подтвердить заказ»
final readonly class ConfirmOrderCommand { /* ... */ }

// Контроллер:
#[Route('/orders/{id}/confirm', name: 'order_confirm')]
public function confirm(int $id): Response { /* ... */ }

// Репозиторий:
// OrderRepository::find($id)  ← «найти заказ»
// OrderRepository::save($order)  ← «сохранить заказ»

// БД:
// orders.status = 'confirmed'
// outbox.event_name = 'OrderConfirmed'

// UI:
// Кнопка: «Подтвердить»
// Статус: «Подтверждён»

// Лог:
// [ORDER] order=42 action=confirmed user=1 total=5000

// Все говорят «подтвердить / подтверждён / confirm / confirmed».
// ОДНО понятие. Пять представлений. Но слово — одно.
```

#### Плохо

```php
// Product:  «Заблокировать платёж»
// Dev:      holdPayment()
// DBA:      payment_status = 'pending'
// Frontend: «В обработке»
// Test:     test_payment_in_progress
// Log:      payment_hold_initiated

final class PaymentService
{
    public function holdPayment(int $paymentId): void { /* ... */ }
    // ↑ «hold» — не в домене. В домене «заблокировать».
    // Но «заблокировать» — тоже не точно. В домене «удержать».

    public function releaseHeldPayment(int $paymentId): void { /* ... */ }
    // ↑ «release held payment» — три слова для одного действия.
    // В домене: «отпустить удержание».

    public function settlePayment(int $paymentId): void { /* ... */ }
    // ↑ «settle» — из Stripe API. Не из домена.
    // В домене: «зафиксировать».
}
```

### Как вводить единый язык

#### Шаг 1: Event Storming (или разговор с product owner'ом)

```
Product:  «Когда пользователь платит, мы блокируем средства.
           Через 3 дня, если не подтвердил — отменяем.
           Если подтвердил — списываем.»

Dev:      «Ok. Значит: удержание → подтверждение → списание.
           Или: hold → confirm → settle?»

Product:  «Нет. Удержание → подтверждение → списание.
           Hold — это техническое слово. В домене — «удержать».»

Dev:      «Значит:
           PaymentHold (сущность)
           hold() → confirm() → settle()  (методы)
           PaymentHeld, PaymentConfirmed, PaymentSettled (события)
           hold → confirm → settle (статусы в БД)»

→ Зафиксировано. В glossary. В коде. В БД. В UI.
```

#### Шаг 2: Glossary

```markdown
## Glossary

| Термин | Определение | Код |
|---|---|---|
| Удержание | Блокировка средств на счёте на срок не более 3 дней | `PaymentHold`, `hold()` |
| Подтверждение | Фиксация платежа после успешного удержания | `confirm()`, `PaymentConfirmed` |
| Списание | Перевод средств из «удержано» в «списано» | `settle()`, `PaymentSettled` |
| Отзыв удержания | Освобождение средств без списания | `release()`, `PaymentReleased` |
| Повторная попытка | Новая попытка удержания после неудачной | `retry()`, `PaymentRetry` |
| Счёт | Объединённый баланс клиента | `Account`, `balance` |
| Строка счёта | Одна позиция в счёте | `InvoiceLine` |
| Счёт | Документ на оплату | `Invoice` |
| Платёж | Факт перевода средств | `Payment` |
| Заказ | Набор товаров к покупке | `Order` |
| Строка заказа | Одна позиция в заказе | `OrderLine` |

## Что НЕ является
- «Платёж» ≠ «Счёт». Платёж — факт. Счёт — документ.
- «Удержание» ≠ «Блокировка». Блокировка — техническое. Удержание — доменное.
- «Строка» = «позиция» = «item» = «line». В коде: `Line`. В UI: «Позиция». В БД: `lines`.
```

#### Шаг 3: Код следует за glossary

```php
// glossary: «Удержание» → PaymentHold, hold()
final class Payment
{
    public function hold(Money $amount, int $holdDays): self { /* ... */ }
    public function confirm(): self { /* ... */ }
    public function settle(): self { /* ... */ }
    public function release(string $reason): self { /* ... */ }
}

// События:
final readonly class PaymentHeld { /* ... */ }
final readonly class PaymentConfirmed { /* ... */ }
final readonly class PaymentSettled { /* ... */ }
final readonly class PaymentReleased { /* ... */ }
```

#### Шаг 4: Ревью = проверка на единый язык

```diff
- public function blockPayment(int $id): void { /* ... */ }
+ public function hold(int $paymentId): void { /* ... */ }

- $this->unblockPayment($id);
+ $this->release($id, 'user_cancelled');

- $payment->status = 'in_progress';
+ $payment->status = PaymentStatus::Held;
```

В ревью: «Почему `block`? В glossary — `hold`. Почему `in_progress`? В glossary — `Held`.»

---

## Как единый язык влияет на архитектуру

### 1. Имена модулей = имена в домене

```
Домен:  «Подписка», «Счёт», «Платёж»
Код:    App\Subscription, App\Invoice, App\Payment
БД:     subscriptions, invoices, payments
UI:     «Подписка», «Счёт», «Платёж»
API:    /subscriptions, /invoices, /payments

→ ОДНА структура. Во всех слоях.
```

Без единого языка:

```
Домен:  «Подписка»
Код:    App\Billing
БД:     plans
UI:     «Тариф»
API:    /billing/plans

→ Четыре слова. Четыре команды. Четыре понимания.
```

### 2. Границы контекста = границы языка

```
Контекст «Identity»:
  • User, Email, Password, Role, Session
  • «Вход», «Выход», «Сброс пароля»

Контекст «Order»:
  • Order, OrderLine, Product, Quantity, Total
  • «Создать», «Подтвердить», «Отменить»

Контекст «Payment»:
  • Payment, Hold, Settle, Refund
  • «Удержать», «Подтвердить», «Списать», «Возвратить»

→ «Подтвердить» в Order ≠ «Подтвердить» в Payment.
→ В Order: «подтвердить заказ» (статус draft → confirmed).
→ В Payment: «подтвердить платёж» (статус held → settled).
→ ОДНО слово. РАЗНЫЙ смысл. РАЗНЫЙ контекст.
→ В коде: Order::confirm() ≠ Payment::confirm().
→ В БД: orders.status='confirmed' ≠ payments.status='settled'.
```

Это **не** путаница. Это **осознанная** перегрузка термина в разных контекстах. Единый язык — **внутри** контекста. На **границе** контекстов — адаптер (Anti-Corruption Layer).

### 3. Тесты читаются как домен

```php
// Без единого языка:
public function test_a() {
    $p = $this->service->doX(42);
    $this->assertEquals('ok', $p->y);
}

// С единым языком:
public function test_hold_rejects_insufficient_balance(): void
{
    $account = $this->createAccount(balance: new Money(100, 'RUB'));
    $payment = Payment::initiate($account->id(), new Money(150, 'RUB'));

    $this->expectException(InsufficientFundsException::class);
    $this->service->hold($payment);
}
```

Тест читается как предложение: «Удержание отклоняет при недостаточном балансе». Не `test_a`. Не `test_42`. Не `test_doX_returns_ok`.

### 4. Коммиты и тикеты

```
❌ "fix bug in payment"
❌ "update billing service"
❌ "change status logic"

✅ "payment: hold() теперь проверяет баланс ДО блокировки (было после)"
✅ "order: confirm() не позволяет подтвердить заказ без строк"
✅ "invoice: settle() бросает exception при double-settle"
```

---

## Итого

### Entity vs VO

```
Entity:  «КТО». ID. Lifecycle. Мутабельность. Своя таблица. Свой репозиторий.
VO:      «КАКОЙ». Поля. Неизменяемость. Внутри owner'а. Нет репозитория.

Тест:    «Могу ли я заменить один объект другим с тем же значением,
          не сломав ничего?»
  → Да:  VO.
  → Нет: Entity.
```

### Единый язык

```
1. Глоссарий — до написания кода.
2. Код следует за глоссарием. Не наоборот.
3. Ревью — проверка на соответствие глоссарию.
4. Тесты читаются как предложения из глоссария.
5. Границы контекстов = границы, где слово меняет смысл.
```

Единый язык — это не «документация». Это **инструмент коммуникации** между product, dev, QA, DBA, frontend. Когда все говорят одним языком — нет перевода. Нет «а что ты имел в виду под `block`?». Нет «а почему в БД `in_progress`, а в UI «В обработке»?». Нет «а кто знает, что `hold` и `block` — одно и то же?».

Код — это не «механизм». Код — это **закреплённый язык**. Имена в коде — это слова. Структура — это синтаксис. Архитектура — это грамматика. Единый язык — это то, что делает код **читаемым** не только для того, кто писал, но и для того, кто будет читать через год.

> ⏱ 61.1s · prompt 88886 tok · output 6577 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

The user is asking about clean architecture in a PHP application. They want to know:
1. What layers exist and what goes in each
2. The dependency rule between layers
3. Where Doctrine, Symfony controllers, and queue handlers end up
4. How to enforce boundaries between layers

This is a continuation of a long conversation about PHP, Symfony, DDD, Doctrine, and architecture. The user has been asking increasingly specific architecture questions. Let me think about what would be most useful here.

Key things to cover:
- The layers (Domain, Application, Infrastructure, Interface/Adapter)
- Dependency rule: dependencies point inward
- Where things go (Doctrine = Infrastructure, Controller = Interface, Queue handler = Interface)
- How to enforce boundaries (PHPStan rules, folder structure, PHP 8.2 readonly, interface-based DI)

I should be concrete with code examples, show the directory structure, and be honest about trade-offs. The user has been getting increasingly practical answers, so I should keep that tone.

Let me structure this:
1. The 4 layers and what lives in each
2. Dependency rule with a diagram
3. Concrete directory structure
4. Where Doctrine, Controller, Queue handler go (with code)
5. How to enforce boundaries (tooling + process)
6. Trade-offs and honest limitations

I'll write in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Чистая архитектура в PHP: слои, границы, enforcement

## Четыре слоя и правило зависимостей

```
┌─────────────────────────────────────────────────────────────────────┐
│  Interface (Adapters)                                              │
│  ─────────────────────────────────────────────────────────────────  │
│  Symfony Controller, CLI Command, Queue Consumer, API Endpoint     │
│                                                                     │
│  «Как пользователь/система взаимодействует с приложением»           │
│                                                                     │
│        ▲ зависимости вниз                                          │
│        │                                                           │
├────────┼───────────────────────────────────────────────────────────┤
│        │ Application                                               │
│        │ ─────────────────────────────────────────────────────────  │
│        │  Use Cases / Command Handlers / Query Handlers            │
│        │  Orchestration: load → validate → call domain → save      │
│        │  Transaction boundary                                     │
│        │                                                           │
│        │  «Что сделать, в каком порядке, в какой транзакции»       │
│        │                                                           │
│        │  ▲ зависимости вниз                                      │
│        │  │                                                       │
├────────┼───────────────────────────────────────────────────────────┤
│        │ Domain                                                    │
│        │ ─────────────────────────────────────────────────────────  │
│        │  Entities, Value Objects, Aggregates, Domain Events       │
│        │  Invariants, business rules, domain services              │
│        │  Interfaces: Repository, Gateway (PORTS)                  │
│        │                                                           │
│        │  «Что это значит в бизнесе. Какие правила. Что нельзя.»   │
│        │                                                           │
│        │  ▲ зависимости вниз                                      │
│        │  │                                                       │
├────────┼───────────────────────────────────────────────────────────┤
│        │ Infrastructure                                            │
│        │ ─────────────────────────────────────────────────────────  │
│        │  Doctrine Entities, Repositories, Migrations              │
│        │  SMTP Mailer, Stripe Client, Meilisearch Client           │
│        │  Redis Cache, RabbitMQ Publisher, File Storage            │
│        │                                                           │
│        │  «Как это реализовать с конкретными технологиями»         │
│        │                                                           │
└────────┼───────────────────────────────────────────────────────────┘
         │
         ▼
   External world: PostgreSQL, Stripe API, SMTP, Meilisearch, Redis
```

### Правило зависимостей

> **Зависимости направлены только внутрь.** Внутренние слои не знают про внешние.

```
Domain        →  ничего не импортирует извне. Чистый PHP.
Application   →  знает Domain. Не знает Infrastructure. Не знает Symfony.
Interface     →  знает Application. Не знает Domain напрямую (через Application).
Infrastructure→  знает Domain (реализует его интерфейсы). Не знает Application.
```

Конкретно:

| Слой | Может импортировать | НЕ может импортировать |
|---|---|---|
| Domain | `php` ( SPL, `DateTimeImmutable` ) | Symfony, Doctrine, Guzzle,任何 vendor |
| Application | Domain | Doctrine, Symfony, Guzzle, DBAL |
| Interface (Controller) | Application, Domain (types) | Doctrine Entity, DBAL |
| Infrastructure (Repository) | Domain, Doctrine | Application, Symfony (кроме DI-attributes) |

**Проверка:** если `src/Domain/Order/Order.php` содержит `use App\Infrastructure\Doctrine\OrderEntity` — граница нарушена. Domain не должен знать, что существует Doctrine.

---

## Структура каталогов

```
src/
├── Domain/                          ← ЧИСТОЙ PHP. Без vendor.
│   ├── Order/
│   │   ├── Order.php                ← Агрегат (корень)
│   │   ├── OrderLine.php            ← Child entity
│   │   ├── OrderStatus.php          ← Enum
│   │   ├── OrderConfirmed.php       ← Domain event
│   │   ├── OrderLineAdded.php       ← Domain event
│   │   ├── InsufficientStockException.php
│   │   └── OrderRepositoryInterface.php   ← PORT
│   ├── Money/
│   │   └── Money.php                ← VO
│   ├── Customer/
│   │   ├── Customer.php
│   │   ├── CustomerId.php           ← VO (обёртка)
│   │   └── CustomerRepositoryInterface.php
│   └── Payment/
│       ├── Payment.php
│       ├── PaymentStatus.php
│       ├── PaymentSettled.php
│       └── PaymentGatewayInterface.php  ← PORT
│
├── Application/                     ← Orchestration. Без vendor.
│   ├── Order/
│   │   ├── CreateOrderCommand.php
│   │   ├── ConfirmOrderCommand.php
│   │   ├── CancelOrderCommand.php
│   │   ├── GetOrderQuery.php
│   │   ├── CreateOrderHandler.php   ← Use case
│   │   ├── ConfirmOrderHandler.php
│   │   └── GetOrderHandler.php
│   ├── Payment/
│   │   ├── InitiatePaymentCommand.php
│   │   ├── SettlePaymentCommand.php
│   │   └── InitiatePaymentHandler.php
│   └── Shared/
│       ├── UnitOfWorkInterface.php  ← PORT (flush/clear)
│       └── EventBusInterface.php    ← PORT (dispatch)
│
├── Infrastructure/                  ← Конкретные реализации.
│   ├── Doctrine/
│   │   ├── Entity/
│   │   │   ├── OrderEntity.php
│   │   │   ├── OrderLineEntity.php
│   │   │   └── PaymentEntity.php
│   │   ├── Repository/
│   │   │   ├── DoctrineOrderRepository.php  ← implements OrderRepositoryInterface
│   │   │   └── DoctrinePaymentRepository.php
│   │   ├── Types/
│   │   │   └── MoneyType.php
│   │   └── Mapping/                 ← XML/PHP/Attribute mapping (если не в Entity)
│   │       └── OrderEntity.orm.php
│   ├── Gateway/
│   │   ├── StripePaymentGateway.php ← implements PaymentGatewayInterface
│   │   └── SmtpMailer.php
│   ├── Search/
│   │   └── MeilisearchClient.php
│   └── EventBus/
│       ├── DoctrineOutboxBus.php    ← implements EventBusInterface
│       └── OutboxEntity.php
│
├── Interface/                       ← Adapters. «Внешний мир» → Application
│   ├── Api/
│   │   ├── OrderController.php      ← Symfony Controller
│   │   ├── PaymentController.php
│   │   └── Dto/                     ← Request/Response DTO (не Domain!)
│   │       ├── CreateOrderRequest.php
│   │       └── OrderResponse.php
│   ├── Console/
│   │   ├── ProcessOutboxCommand.php
│   │   └── ReindexSearchCommand.php
│   └── Worker/
│       ├── OnPaymentSettled.php     ← Queue consumer
│       └── OnOrderConfirmed.php     ← Queue consumer
│
├── EventSubscriber/                 ← Symfony event listeners (framework glue)
│   └── DomainExceptionListener.php  ← Exception → HTTP response
│
└── Kernel.php                       ← Symfony entry point
```

---

## Что в каждом слое: конкретика

### Domain: `src/Domain/Order/Order.php`

```php
<?php
declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Money\Money;

// ВНИМАНИЕ: только use из App\Domain\* и PHP stdlib.
// Никаких use App\Infrastructure\*, App\Application\*, Symfony\*, Doctrine\*.

final class Order
{
    private int $id;
    private CustomerId $customer;
    private OrderStatus $status;
    /** @var OrderLine[] */
    private array $lines = [];
    private \DateTimeImmutable $createdAt;

    /** @var list<object> */
    private array $pendingEvents = [];

    private function __construct(CustomerId $customer)
    {
        $this->customer  = $customer;
        $this->status    = OrderStatus::Draft;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(CustomerId $customer): self
    {
        return new self($customer);
    }

    public static function reconstitute(
        int    $id,
        CustomerId $customer,
        OrderStatus $status,
        array $lines,
        \DateTimeImmutable $createdAt,
    ): self {
        $o = new self($customer);
        $o->id = $id;
        $o->status = $status;
        $o->lines = $lines;
        $o->createdAt = $createdAt;
        return $o;
    }

    public function addLine(ProductId $product, int $qty, Money $unitPrice): self
    {
        $this->assertEditable();
        if ($qty <= 0) throw new DomainException('Quantity must be positive');

        $this->lines[] = new OrderLine($product, $qty, $unitPrice);
        $this->pendingEvents[] = new OrderLineAdded($this->id, $product, $qty);
        return $this;
    }

    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotInDraftException($this->id);
        }
        if (empty($this->lines)) {
            throw new EmptyOrderException($this->id);
        }

        $this->status = OrderStatus::Confirmed;
        $this->pendingEvents[] = new OrderConfirmed(
            orderId:    $this->id,
            totalCents: $this->total()->amountCents,
            lineCount:  count($this->lines),
        );
        return $this;
    }

    public function total(): Money { /* ... */ }

    /** @return list<object> */
    public function releaseEvents(): array
    {
        $e = $this->pendingEvents;
        $this->pendingEvents = [];
        return $e;
    }

    private function assertEditable(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderLockedException($this->id, $this->status);
        }
    }

    // Getters: id(), customer(), status(), lines(), createdAt()
}
```

**Правило:** в `src/Domain/` нет ни одного `use` извне `App\Domain\*` и PHP stdlib. Ни Symfony. Ни Doctrine. Ни `spl_object_id` (это ок, stdlib).

### Application: `src/Application/Order/ConfirmOrderHandler.php`

```php
<?php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;   // ← PORT, не реализация
use App\Application\Shared\UnitOfWorkInterface;  // ← PORT
use App\Application\Shared\EventBusInterface;    // ← PORT

final class ConfirmOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private UnitOfWorkInterface      $uow,
        private EventBusInterface        $events,
    ) {}

    public function __invoke(ConfirmOrderCommand $cmd): void
    {
        $order = $this->orders->find($cmd->orderId)
            ?? throw new OrderNotFoundException($cmd->orderId);

        $order->confirm();   // ← доменная валидация + событие в pendingEvents

        $this->orders->save($order);

        $this->uow->flush();   // ← COMMIT. Данные + outbox.

        foreach ($order->releaseEvents() as $event) {
            $this->events->dispatch($event);
        }

        $this->uow->clear();
    }
}
```

**Правило:** Application знает Domain (сущности, события, интерфейсы-порты). Не знает Doctrine, Symfony, Guzzle. `UnitOfWorkInterface` — это абстракция над `$em->flush()`. `EventBusInterface` — абстракция над `Messenger` / `EventDispatcher`.

### Infrastructure: `src/Infrastructure/Doctrine/Repository/DoctrineOrderRepository.php`

```php
<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;   // ← реализует PORT
use App\Infrastructure\Doctrine\Entity\OrderEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Money\Money;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function find(int $id): ?Order
    {
        $entity = $this->em->find(OrderEntity::class, $id);
        return $entity?->toDomain();
    }

    public function save(Order $order): void
    {
        $entity = $this->em->find(OrderEntity::class, $order->id());

        if ($entity === null) {
            $entity = OrderEntity::fromDomain($order);
            $this->em->persist($entity);
        } else {
            $entity->syncFromDomain($order);
            // cascade: lines persist/remove
        }
    }

    /**
     * @return Order[]
     */
    public function findByStatus(OrderStatus $status, int $limit): array
    {
        $entities = $this->em->getRepository(OrderEntity::class)
            ->findBy(['status' => $status], ['createdAt' => 'DESC'], $limit);

        return array_map(fn(OrderEntity $e) => $e->toDomain(), $entities);
    }
}
```

**Правило:** Infrastructure **реализует** интерфейсы из Domain / Application. Зависит от Doctrine. Зависит от Domain (чтобы знать, что такое `Order`). Не зависит от Application (не знает про `ConfirmOrderHandler`).

### Interface: `src/Interface/Api/OrderController.php`

```php
<?php
declare(strict_types=1);

namespace App\Interface\Api;

use App\Application\Order\ConfirmOrderCommand;
use App\Application\Order\ConfirmOrderHandler;
use App\Interface\Api\Dto\CreateOrderRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class OrderController extends AbstractController
{
    public function __construct(
        private ConfirmOrderHandler $confirmHandler,
        private CreateOrderHandler  $createHandler,
    ) {}

    #[Route('/api/orders/{id}/confirm', name: 'order_confirm', methods: ['POST'])]
    public function confirm(int $id): JsonResponse
    {
        $this->confirmHandler(new ConfirmOrderCommand($id));
        return new JsonResponse(['status' => 'confirmed'], 200);
    }

    #[Route('/api/orders', name: 'order_create', methods: ['POST'])]
    public function create(CreateOrderRequest $request): JsonResponse
    {
        // DTO → Command (перевод внешнего формата в доменную команду)
        $command = $request->toCommand();
        $orderId = $this->createHandler($command);
        return new JsonResponse(['id' => $orderId], 201);
    }
}
```

**Правило:** Controller знает Application (handlers, commands). Не знает Domain напрямую (кроме типов в DTO). Не знает Doctrine. Не делает бизнес-логику. Не валидирует доменные правила (это делает Domain).

### Interface (Worker): `src/Interface/Worker/OnPaymentSettled.php`

```php
<?php
declare(strict_types=1);

namespace App\Interface\Worker;

use App\Application\Payment\SettlePaymentHandler;
use App\Domain\Payment\PaymentSettled;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class OnPaymentSettled
{
    public function __construct(
        private SettlePaymentHandler $handler,
    ) {}

    public function __invoke(PaymentSettled $event): void
    {
        $this->handler(new SettlePaymentCommand(
            paymentId: $event->paymentId,
            settledAt: $event->settledAt,
        ));
    }
}
```

**Правило:** Consumer — это **адаптер**. Он принимает «внешний формат» (сообщение из очереди) и вызывает Application Handler. Не содержит бизнес-логики. Не делает `em->flush()`. Не знает про Doctrine.

---

## Куда попадают конкретные вещи

| Что | Слой | Почему |
|---|---|---|
| `Order.php` (агрегат) | **Domain** | Бизнес-логика, инварианты. Чистый PHP. |
| `OrderLine.php` | **Domain** | Часть агрегата. |
| `Money.php` (VO) | **Domain** | Значение + инварианты. |
| `OrderRepositoryInterface.php` | **Domain** | Порт. Contract. «Найди заказ по ID». |
| `OrderConfirmed.php` (event) | **Domain** | Факт. Принадлежит домену. |
| `ConfirmOrderHandler.php` | **Application** | Оркестрация: load → domain → save → flush. |
| `ConfirmOrderCommand.php` | **Application** | Вход в use case. |
| `DoctrineOrderRepository.php` | **Infrastructure** | Реализация порта. Зависит от Doctrine. |
| `OrderEntity.php` (Doctrine Entity) | **Infrastructure** | Маппинг БД. Не домен. |
| `OrderEntity::toDomain()` | **Infrastructure** | Адаптер: БД → Domain. |
| `OrderController.php` | **Interface** | HTTP → Application. |
| `CreateOrderRequest.php` (DTO) | **Interface** | Формат запроса. Не Domain. |
| `OnPaymentSettled.php` (consumer) | **Interface** | Queue → Application. |
| `ProcessOutboxCommand.php` (CLI) | **Interface** | CLI → Infrastructure (outbox relay). |
| `StripePaymentGateway.php` | **Infrastructure** | Внешний API. Реализация `PaymentGatewayInterface`. |
| `MoneyType.php` (Doctrine Type) | **Infrastructure** | Маппинг БД. |
| `DomainExceptionListener.php` | **Interface** | Symfony glue: Exception → HTTP. |
| Migrations | **Infrastructure** | Схема БД. |
| `config/services.yaml` | — | Wiring. Не слой. |

---

## Правило зависимостей: как проверить

### Визуально

```
Interface  ──►  Application  ──►  Domain
                   ▲                  ▲
                   │                  │
              Infrastructure ─────────┘

Зависимости:
  Interface → Application: ✅
  Interface → Domain:      ✅ (типы в сигнатурах)
  Application → Domain:    ✅
  Application → Infrastructure: ❌
  Domain → Application:    ❌
  Domain → Infrastructure: ❌
  Domain → Interface:      ❌
  Infrastructure → Domain: ✅ (реализует порт)
  Infrastructure → Application: ❌
```

### Конкретно: что сломает правило

```php
// ❌ Domain знает про Doctrine:
namespace App\Domain\Order;
use Doctrine\ORM\EntityManagerInterface;  // ← НЕТ.

// ❌ Application знает про Symfony:
namespace App\Application\Order;
use Symfony\Component\HttpFoundation\Request;  // ← НЕТ.

// ❌ Application знает про конкретную реализацию:
namespace App\Application\Order;
use App\Infrastructure\Doctrine\Repository\DoctrineOrderRepository;  // ← НЕТ.
use App\Domain\Order\OrderRepositoryInterface;  // ← ДА. Порт.

// ❌ Interface делает бизнес-логику:
public function confirm(int $id): Response {
    $order = $this->em->find(Order::class, $id);  // ← НЕТ. Controller не знает Doctrine.
    if ($order->status !== 'draft') {             // ← НЕТ. Валидация — в Domain.
        return new Response('locked', 409);
    }
    $order->status = 'confirmed';                 // ← НЕТ. Мутация — в Domain.
    $this->em->flush();                           // ← НЕТ. flush — в Application.
}

// ✅ Interface вызывает Application:
public function confirm(int $id): Response {
    $this->confirmHandler(new ConfirmOrderCommand($id));
    return new Response('', 200);
}
```

---

## Как следить, что границы не нарушаются

### 1. PHPStan с кастомным правилом

```php
// rules/ArchitectureRule.php
namespace App\Rules;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PhpParser\Node;
use PhpParser\Node\Name;

final class NoCrossLayerImportsRule implements Rule
{
    // Domain НЕ может импортировать из Infrastructure, Application, Interface
    // Application НЕ может импортировать из Infrastructure, Interface

    private const FORBIDDEN = [
        'App\Domain' => ['App\Infrastructure', 'App\Application', 'App\Interface'],
        'App\Application' => ['App\Infrastructure', 'App\Interface'],
    ];

    public function getNodeType(): string
    {
        return Node\Stmt\Use_.class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $errors = [];

        // Определяем, в каком слое мы:
        if (str_contains($file, '/Domain/')) {
            $forbidden = self::FORBIDDEN['App\Domain'];
        } elseif (str_contains($file, '/Application/')) {
            $forbidden = self::FORBIDDEN['App\Application'];
        } else {
            return [];
        }

        foreach ($node->uses as $use) {
            $name = $use->name->toString();
            foreach ($forbidden as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    $errors[] = sprintf(
                        'Import "%s" is forbidden in this layer. '
                        . 'Use a port (interface) instead.',
                        $name
                    );
                }
            }
        }

        return $errors;
    }
}
```

```yaml
# phpstan.neon
services:
    -
        class: App\Rules\NoCrossLayerImportsRule
        tags:
            - phpstan.rules.rule
```

Каждый `use App\Infrastructure\...` в файле из `src/Domain/` → **ошибка на CI**.

### 2. Composer `replace` / autoloading (мягкая защита)

```json
{
    "autoload": {
        "psr-4": {
            "App\\Domain\\": "src/Domain/",
            "App\\Application\\": "src/Application/",
            "App\\Infrastructure\\": "src/Infrastructure/",
            "App\\Interface\\": "src/Interface/"
        }
    }
}
```

PSR-4 сам по себе не запрещает кросс-импорты. Но **структура каталогов** делает нарушение видимым в code review: «почему файл в `src/Domain/` импортирует `App\Infrastructure\...`?»

### 3. Интеграционный тест: «Domain не зависит от vendor»

```php
// tests/Architecture/DomainIsolationTest.php
final class DomainIsolationTest extends TestCase
{
    public function test_domain_has_no_vendor_dependencies(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/Domain';
        $files = $this->findPhpFiles($dir);

        $forbidden = [
            'Doctrine\\',
            'Symfony\\',
            'GuzzleHttp\\',
            'Psr\\Log\\',       // ← даже PSR-интерфейсы лучше не в Domain
            'App\\Infrastructure\\',
            'App\\Application\\',
            'App\\Interface\\',
        ];

        $violations = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($forbidden as $prefix) {
                if (preg_match('/use\s+' . preg_quote($prefix, '/') . '\w+/', $content)) {
                    $violations[] = $file . ' → ' . $prefix;
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Domain layer must not import vendor or other layers:\n"
            . implode("\n", $violations)
        );
    }

    public function test_application_has_no_infrastructure_dependencies(): void
    {
        $dir = dirname(__DIR__, 2) . '/src/Application';
        // Аналогично: проверка на App\Infrastructure\*
    }
}
```

Запускается на CI. Если кто-то добавит `use Doctrine\ORM\EntityManagerInterface` в `src/Domain/` → **тест падает**.

### 4. Code review checklist

```
[ ] Новый класс в src/Domain/ не содержит use из vendor / Infrastructure / Application
[ ] Новый класс в src/Application/ зависит только от Domain + портов
[ ] Новый класс в src/Interface/ вызывает Application Handler, не Domain напрямую
[ ] Новый класс в src/Infrastructure/ реализует интерфейс из Domain
[ ] Новый use case: Command → Handler → Domain → Repository (порт) → flush
[ ] Controller не содержит бизнес-логику (if/else по статусу, расчёты)
[ ] Consumer не содержит бизнес-логику
[ ] Доменное событие определено в src/Domain/
[ ] Репозиторий: интерфейс в Domain, реализация в Infrastructure
```

### 5. Архитектурная тестирование (ArchUnit / PHPStan baseline)

```yaml
# phpstan-baseline.neon — разрешённые исключения (пока не переделали)
parameters:
    ignoreErrors:
        -
            identifier: no-cross-layer-imports
            path: src/Domain/Order/LegacyOrderService.php
            # TODO: refactor to clean architecture (Q3 2025)
```

Не «отключить правило», а **зафиксировать** нарушение с дедлайном. Через квартал — удалить из baseline.

---

## Честные ограничения

### 1. Это не бесплатно

| Что вы платите | Цена |
|---|---|
| 4 каталога вместо 2 | +2 уровня абстракции. Новичку сложнее найти код. |
| Порт + реализация | `OrderRepositoryInterface` + `DoctrineOrderRepository` = 2 файла вместо 1. |
| `toDomain()` / `fromDomain()` | Адаптеры. Код, который нужно поддерживать при каждом изменении схемы. |
| Тестирование | Unit-тест Domain — просто. Integration Application — сложнее (mock'и портов). E2E — как раньше. |
| Наём | «Почему у нас 4 слоя? У Laravel — 2». Ответ: «потому что 50 разработчиков, 5 контекстов, 3 года жизни». |

### 2. Не для каждого проекта

| Проект | Чистая архитектура? |
|---|---|
| CRM на 3 человека, 2 года | Нет. Controller → Service → Repository. Хватит. |
| SaaS на 15 человек, 5 лет, 4 доменных контекста | Да. Иначе «Order» знает про Stripe, а «Stripe» знает про Order. |
| Микро-сервис на 2 человека, 1 контекст | Гибрид. Domain + Application + Infrastructure. Interface = 1 контроллер. |

**Критерий:** если > 5 разработчиков одновременно пишут в один код и > 3 года проект живёт — чистая архитектура окупается. Если нет — оверхед > пользы.

### 3. Domain не всегда «чистый»

В реальности:

```php
// Domain:
final class Order
{
    public function confirm(): self
    {
        // А как узнать, хватает ли на складе?
        // InventoryService — это Application или Domain?
        // Если Domain → нужен порт: InventoryCheckInterface.
        // Если Application → ConfirmOrderHandler делает:
        //   1. $this->inventory->reserve($lines)  ← может бросить
        //   2. $order->confirm()
        //   3. $this->uow->flush()
    }
}
```

Граница между «доменная логика» и «оркестрация» — **не чёткая**. Где заканчивается «Order решает» и начинается «Service координирует» — **решение команды**. Чистая архитектура даёт структуру, но не ответ.

### 4. Symfony — не «чистый» фреймворк

```php
// Symfony Controller:
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
```

Это **не нарушение**. Interface-слой **по определению** зависит от фреймворка. Controller — адаптер между HTTP и Application. `use Symfony\...` в `src/Interface/` — ок. В `src/Domain/` — нет.

---

## Итого

```
Domain         →  Что. Правила. Инварианты. Чистый PHP.
Application    →  Как. Порядок. Транзакция. Порты.
Infrastructure →  С помощью чего. Doctrine. Stripe. Redis.
Interface      →  Через что. HTTP. CLI. Queue. WebSocket.

Зависимости:   Interface → Application → Domain
                Infrastructure → Domain (реализует порты)
                Domain → (ничего)

Enforcement:   PHPStan rule + ArchUnit test + code review + каталоги
```

Чистая архитектура — это не «правильный код». Это **защита от энтропии**. Через 6 месяцев, когда придёт четвёртый разработчик и добавит пятый «вариант оплаты», границы слоёв — единственное, что не даст `OrderController` узнать про `StripeGateway`, а `StripeGateway` — про `OrderLine`.

> ⏱ 61.5s · prompt 95021 tok · output 7262 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about a testing strategy for a Symfony application with PostgreSQL. They want to know:
1. Test pyramid strategy
2. Types of tests
3. What to mock vs what not to mock
4. How to keep tests fast and stable

Let me structure a comprehensive answer that covers:
- The pyramid shape and what goes in each layer
- Specific test types (unit, integration, E2E)
- Concrete examples with Symfony + Doctrine + PostgreSQL
- Mocking decisions (what's justified, what's not)
- Practical tips for speed and stability

I should be concrete with PHP code examples, Symfony-specific tools (PHPUnit, Symfony Test Client, doctrine fixtures, etc.), and give honest trade-offs. Let me think about what's most valuable here.

Key points to cover:
- The pyramid: many unit, fewer integration, fewest E2E
- Unit tests: domain logic, no DB, no framework
- Integration tests: repository + real PostgreSQL (or SQLite), service layer
- E2E tests: full HTTP request through Symfony kernel
- What to mock: external services (Stripe, SMTP), not internal (Doctrine, domain)
- What NOT to mock: domain logic, repository implementations (use real DB)
- Speed: in-memory for unit, SQLite for integration (or real PG in docker), parallel execution
- Stability: no time-dependent tests, no network calls in tests, proper transaction rollback

Let me write this in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Стратегия тестирования: Symfony + PostgreSQL

## Пирамида: что, сколько, зачем

```
            /\
           /  \          E2E: 5–10%
          / E2E\         Полные HTTP-запросы. Через kernel.
         /──────\        «Пользователь нажал кнопку → получил ответ»
        /        \
       /  Integ.  \      Integration: 20–30%
      /────────────\     Repository + Service + реальная БД.
     /              \    «Запрос к БД отдал правильные данные»
    /────────────────\
   /    Unit (домен)  \  Unit: 50–70%
  /────────────────────\ «Order::confirm() бросает, если нет строк»
 /──────────────────────\ «Money::add() бросает, если разные валюты»
/────────────────────────\
```

**Пропорция не догма.** Если у вас 50% доменной логики в агрегатах — unit-тестов будет 70%. Если «тонкий» CRUD — 30% unit, 50% integration. Пропорция следует за распределением логики.

---

## Слой 1: Unit-тесты (домен)

### Что тестируем

Доменные правила. Инварианты. Переходы состояний. Арифметику VO.

### Что НЕ нужно

БД. Symfony. Doctrine. Container. HTTP. Файлы. Сеть.

### Пример

```php
// tests/Unit/Domain/Order/OrderTest.php
namespace Tests\Unit\Domain\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\EmptyOrderException;
use App\Domain\Order\OrderLockedException;
use App\Domain\Money\Money;
use App\Domain\Customer\CustomerId;
use App\Domain\Product\ProductId;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    private function makeOrder(): Order
    {
        return Order::create(new CustomerId(1));
    }

    // ─── Инварианты ─────────────────────────────────────────────

    public function test_confirm_requires_lines(): void
    {
        $order = $this->makeOrder();

        $this->expectException(EmptyOrderException::class);
        $this->expectExceptionMessage('Order has no lines');

        $order->confirm();
    }

    public function test_confirm_rejects_non_draft(): void
    {
        $order = Order::reconstitute(
            id: 1,
            customer: new CustomerId(1),
            status: OrderStatus::Confirmed,
            lines: [new OrderLine(new ProductId(1), 1, new Money(100, 'RUB'))],
            createdAt: new \DateTimeImmutable('2024-01-01'),
        );

        $this->expectException(OrderLockedException::class);

        $order->confirm();
    }

    // ─── Мутации ─────────────────────────────────────────────────

    public function test_add_line_releases_event(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new ProductId(42), 3, new Money(100, 'RUB'));

        $events = $order->releaseEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\App\Domain\Order\OrderLineAdded::class, $events[0]);
        $this->assertSame(42, $events[0]->productId->value);
        $this->assertSame(3, $events[0]->quantity);
    }

    public function test_release_events_is_idempotent(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new ProductId(1), 1, new Money(100, 'RUB'));

        $first = $order->releaseEvents();
        $second = $order->releaseEvents();

        $this->assertCount(1, $first);
        $this->assertCount(0, $second);   // ← не дублирует
    }

    public function test_add_line_rejects_zero_quantity(): void
    {
        $order = $this->makeOrder();

        $this->expectException(\App\Domain\Order\DomainException::class);

        $order->addLine(new ProductId(1), 0, new Money(100, 'RUB'));
    }

    // ─── Арифметика ──────────────────────────────────────────────

    public function test_total_sums_all_lines(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new ProductId(1), 2, new Money(150, 'RUB'));
        $order->addLine(new ProductId(2), 1, new Money(300, 'RUB'));

        $total = $order->total();

        $this->assertSame(600, $total->amountCents);
        $this->assertSame('RUB', $total->currency);
    }

    public function test_total_rejects_mixed_currencies(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new ProductId(1), 1, new Money(100, 'RUB'));

        // Через reflection или special test method (в реальном проекте —
        // отдельный метод addLineWithCurrency для теста, или переделка API):
        // Убедимся, что total() не сломан:
        $this->assertSame(100, $order->total()->amountCents);
    }
}
```

```php
// tests/Unit/Domain/Money/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function test_add_same_currency(): void
    {
        $a = new Money(100, 'RUB');
        $b = new Money(50, 'RUB');

        $result = $a->add($b);

        $this->assertSame(150, $result->amountCents);
        $this->assertSame('RUB', $result->currency);
    }

    public function test_add_rejects_different_currency(): void
    {
        $a = new Money(100, 'RUB');
        $b = new Money(50, 'USD');

        $this->expectException(\App\Domain\Money\DomainException::class);

        $a->add($b);
    }

    public function test_negative_amount_rejected(): void
    {
        $this->expectException(\App\Domain\Money\DomainException::class);

        new Money(-1, 'RUB');
    }

    public function test_empty_currency_rejected(): void
    {
        $this->expectException(\App\Domain\Money\DomainException::class);

        new Money(100, '');
    }

    public function test_immutability(): void
    {
        $a = new Money(100, 'RUB');
        $b = $a->add(new Money(50, 'RUB'));

        $this->assertSame(100, $a->amountCents);   // ← а не 150
        $this->assertSame(150, $b->amountCents);
    }
}
```

### Скорость

```bash
# 200 unit-тестов: 0.8 сек.
# Нет БД. Нет Symfony. Нет сети. Только PHP + PHPUnit.
php vendor/bin/phpunit --testsuite Unit
```

### Что мокаем

**Ничего.** Unit-тест домена работает с реальными объектами. `new Money(100, 'RUB')`. `new Order(...)`. Нет моков. Нет doubles. Реальный код, реальные инварианты.

---

## Слой 2: Integration-тесты (репозиторий + сервис + БД)

### Что тестируем

- Репозиторий: «сохранил → нашёл → данные совпадают».
- Репозиторий: «cascade persist работает: новый line в BД».
- Репозиторий: «orphanRemoval работает: удалил line → DELETE в BД».
- Application Handler: «confirmOrder → данные в БД + событие в outbox».
- Транзакционность: «flush упал → данные не сохранены».

### Что НЕ тестируем

- Бизнес-логику (покрыто unit-тестами).
- HTTP (покрыто E2E).
- Внешние API (Stripe, SMTP).

### Инфраструктура

```yaml
# docker-compose.test.yml (запускается на CI)
services:
  postgres:
    image: postgres:16
    environment:
      POSTGRES_DB: test
      POSTGRES_USER: test
      POSTGRES_PASSWORD: test
    ports:
      - "5433:5432"
    healthcheck:
      test: ["CMD", "pg_isready"]
      interval: 1s
      timeout: 2s
      retries: 10
```

```yaml
# .env.test
APP_ENV=test
DATABASE_URL="postgresql://test:test@127.0.0.1:5433/test?serverVersion=16&charset=utf8"
MESSENGER_TRANSPORT_DSN="doctrine://default?auto_setup=0"   # ← in-memory, не RabbitMQ
MAILER_DSN="null://null"
```

```yaml
# config/packages/test/doctrine.yaml
doctrine:
    dbal:
        url: '%env(DATABASE_URL)%'
    orm:
        auto_generate_proxy_classes: false
        auto_mapping: true
```

### База для integration-тестов

```php
// tests/Integration/IntegrationTestCase.php
namespace Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();

        // Очистка БД перед каждым тестом:
        $this->truncateAllTables();
    }

    protected function tearDown(): void
    {
        $this->em->close();
        self::ensureKernelShutdown();
        parent::tearDown();
    }

    private function truncateAllTables(): void
    {
        $conn = $this->em->getConnection();
        $tables = $conn->createSchemaManager()->listTableNames();

        $conn->executeStatement('SET SESSION REFERENTIAL_INTEGRITY = FALSE');
        foreach ($tables as $table) {
            $conn->executeStatement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
        }
        $conn->executeStatement('SET SESSION REFERENTIAL_INTEGRITY = TRUE');
    }

    /**
     * Запускает код внутри транзакции, которая всегда откатывается.
     * Тест не оставляет данных в БД.
     */
    protected function transactional(callable $fn): void
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $fn();
        } finally {
            $this->em->getConnection()->rollBack();
            $this->em->clear();
        }
    }
}
```

### Пример: тест репозитория

```php
// tests/Integration/Doctrine/OrderRepositoryTest.php
namespace Tests\Integration\Doctrine;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Customer\CustomerId;
use App\Domain\Money\Money;
use App\Domain\Product\ProductId;
use Tests\Integration\IntegrationTestCase;

final class OrderRepositoryTest extends IntegrationTestCase
{
    public function test_save_and_find_roundtrip(): void
    {
        $this->transactional(function () {
            // Arrange
            $order = Order::create(new CustomerId(1));
            $order->addLine(new ProductId(10), 2, new Money(150, 'RUB'));
            $order->addLine(new ProductId(20), 1, new Money(300, 'RUB'));
            $order->confirm();

            // Act
            $this->repo->save($order);
            $this->em->flush();
            $this->em->clear();

            $loaded = $this->repo->find($order->id());

            // Assert
            $this->assertNotNull($loaded);
            $this->assertSame(1, $loaded->customer()->value);
            $this->assertSame(OrderStatus::Confirmed, $loaded->status());
            $this->assertCount(2, $loaded->lines());
            $this->assertSame(600, $loaded->total()->amountCents);
        });
    }

    public function test_save_persists_lines_cascade(): void
    {
        $this->transactional(function () {
            $order = Order::create(new CustomerId(1));
            $order->addLine(new ProductId(1), 1, new Money(100, 'RUB'));

            $this->repo->save($order);
            $this->em->flush();

            // Проверка напрямую в БД:
            $count = $this->em->getConnection()->fetchOne(
                'SELECT COUNT(*) FROM order_lines WHERE order_id = ?',
                [$order->id()]
            );

            $this->assertSame(1, (int) $count);
        });
    }

    public function test_orphan_removal_deletes_line(): void
    {
        $this->transactional(function () {
            $order = Order::create(new CustomerId(1));
            $order->addLine(new ProductId(1), 1, new Money(100, 'RUB'));
            $order->addLine(new ProductId(2), 1, new Money(200, 'RUB'));
            $order->confirm();

            $this->repo->save($order);
            $this->em->flush();
            $this->em->clear();

            // Загружаем, удаляем строку, сохраняем:
            $loaded = $this->repo->find($order->id());
            $loaded->removeLine($loaded->lines()[0]->id());

            $this->repo->save($loaded);
            $this->em->flush();

            $count = $this->em->getConnection()->fetchOne(
                'SELECT COUNT(*) FROM order_lines WHERE order_id = ?',
                [$order->id()]
            );

            $this->assertSame(1, (int) $count);   // ← одна строка удалена
        });
    }

    public function test_find_returns_null_for_missing(): void
    {
        $this->transactional(function () {
            $this->assertNull($this->repo->find(99999));
        });
    }
}
```

### Пример: тест Application Handler (с outbox)

```php
// tests/Integration/Application/ConfirmOrderHandlerTest.php
namespace Tests\Integration\Application;

use App\Application\Order\ConfirmOrderCommand;
use App\Application\Order\ConfirmOrderHandler;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Customer\CustomerId;
use App\Domain\Money\Money;
use App\Domain\Product\ProductId;
use Tests\Integration\IntegrationTestCase;

final class ConfirmOrderHandlerTest extends IntegrationTestCase
{
    private ConfirmOrderHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->getContainer()->get(ConfirmOrderHandler::class);
    }

    public function test_confirm_persists_status_and_outbox(): void
    {
        $this->transactional(function () {
            // Arrange: создаём draft-заказ
            $order = Order::create(new CustomerId(1));
            $order->addLine(new ProductId(1), 2, new Money(100, 'RUB'));

            $this->repo->save($order);
            $this->em->flush();
            $this->em->clear();

            $orderId = $order->id();

            // Act
            $this->handler(new ConfirmOrderCommand($orderId));

            // Assert: статус изменён
            $loaded = $this->repo->find($orderId);
            $this->assertSame(OrderStatus::Confirmed, $loaded->status());

            // Assert: событие в outbox
            $outboxCount = $this->em->getConnection()->fetchOne(
                "SELECT COUNT(*) FROM outbox WHERE event_name = 'App\\Domain\\Order\\OrderConfirmed'"
            );
            $this->assertSame(1, (int) $outboxCount);
        });
    }

    public function test_confirm_rejects_already_confirmed(): void
    {
        $this->transactional(function () {
            $order = Order::create(new CustomerId(1));
            $order->addLine(new ProductId(1), 1, new Money(100, 'RUB'));
            $order->confirm();

            $this->repo->save($order);
            $this->em->flush();
            $this->em->clear();

            $orderId = $order->id();

            $this->expectException(\App\Domain\Order\OrderLockedException::class);
            $this->handler(new ConfirmOrderCommand($orderId));
        });
    }

    public function test_confirm_rejects_nonexistent_order(): void
    {
        $this->transactional(function () {
            $this->expectException(\App\Application\Order\OrderNotFoundException::class);
            $this->handler(new ConfirmOrderCommand(99999));
        });
    }
}
```

### Скорость

```bash
# 50 integration-тестов: 4–8 сек (с реальным PostgreSQL в Docker).
# TRUNCATE + transactional rollback = нет накопления данных.
# Нет внешних сетей. Нет Redis (in-memory messenger).
php vendor/bin/phpunit --testsuite Integration
```

### Что мокаем

| Мокаем | Не мокаем |
|---|---|
| Внешние HTTP-сервисы (Stripe, Meilisearch) | Doctrine EntityManager |
| SMTP (через `null://null` transport) | Репозиторий (реальная БД) |
| Clock / Time (через mock или fake) | Доменные объекты |
| | Доменные события (реальные, проверяем в outbox) |

**Почему не мокаем EntityManager:**

```php
// ❌ Мок EntityManager:
$em = $this->createMock(EntityManagerInterface::class);
$em->method('flush')->willReturn(null);
$em->method('find')->willReturn($entity);

// Что не покрываете:
// • Cascade persist реально работает?
// • OrphanRemoval реально удаляет?
// • SQL-запрос корректен?
// • Индекс срабатывает?
// • Транзакция реально COMMIT'ит?

// ✅ Реальная БД:
// Все выше — покрыто.
```

---

## Слой 3: E2E-тесты (HTTP → Kernel → БД)

### Что тестируем

- Полный HTTP-запрос: роутинг, валидация DTO, контроллер, handler, БД, ответ.
- Статусные коды: 200, 201, 400, 404, 409, 500.
- Формат ответа: JSON-структура, заголовки.
- Аутентификация: 401 без токена, 403 без роли.
- Валидация ввода: невалидный email → 422.

### Что НЕ тестируем

- Бизнес-логику (покрыто unit).
- Репозиторий (покрыто integration).
- Внутренние переходы состояний (покрыто unit).

### Пример

```php
// tests/E2E/Api/OrderApiTest.php
namespace Tests\E2E\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        // Логин как admin:
        $this->client->request('POST', '/api/login', [
            'email' => 'admin@test.ru',
            'password' => 'secret',
        ]);
        // Токен сохраняется в cookie / bearer header
    }

    public function test_create_and_confirm_order(): void
    {
        // CREATE
        $this->client->request('POST', '/api/orders', [
            'json' => [
                'customer_id' => 1,
                'lines' => [
                    ['product_id' => 10, 'quantity' => 2, 'unit_price_cents' => 150, 'currency' => 'RUB'],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson();

        $order = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $order['id'];

        // CONFIRM
        $this->client->request('POST', "/api/orders/{$orderId}/confirm");

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson();
        $this->assertJsonPath('status', 'confirmed');

        // VERIFY: повторное confirm → 409
        $this->client->request('POST', "/api/orders/{$orderId}/confirm");
        $this->assertResponseStatusCodeSame(409);
    }

    public function test_create_order_with_invalid_payload(): void
    {
        $this->client->request('POST', '/api/orders', [
            'json' => [
                'customer_id' => 1,
                'lines' => [
                    ['product_id' => 10, 'quantity' => -5, 'unit_price_cents' => 100],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonPath('errors.0.field', 'lines.0.quantity');
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->client = static::createClient();   // без логина

        $this->client->request('POST', '/api/orders', ['json' => [...]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function test_order_not_found_returns_404(): void
    {
        $this->client->request('POST', '/api/orders/99999/confirm');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonPath('error', 'order_not_found');
    }
}
```

### Скорость

```bash
# 10–20 E2E-тестов: 2–5 сек.
# Каждый тест: boot kernel (1 раз на класс) + HTTP round-trip + БД.
# Symfony Test Client — in-process. Нет реального TCP.
php vendor/bin/phpunit --testsuite E2E
```

### Что мокаем

**Ничего.** E2E — это «реальный пользователь». Реальный kernel, реальная БД, реальный роутинг. Единственное исключение — внешние сервисы (Stripe, SMTP), которые заменяются на fake через DI:

```yaml
# config/services_test.yaml (внешние сервисы → fake)
services:
    App\Domain\Payment\PaymentGatewayInterface:
        alias: Tests\Fake\InMemoryPaymentGateway

    Symfony\Component\Mailer\MailerInterface:
        alias: Tests\Fake\RecordingMailer
```

```php
// tests/Fake/InMemoryPaymentGateway.php
final class InMemoryPaymentGateway implements PaymentGatewayInterface
{
    public array $charges = [];

    public function charge(string $ref, int $amountCents): array
    {
        $this->charges[] = compact('ref', 'amountCents');
        return ['success' => true, 'settled_at' => (new \DateTimeImmutable())->format('c')];
    }
}
```

После E2E-теста:

```php
$gateway = $this->getContainer()->get(InMemoryPaymentGateway::class);
$this->assertCount(1, $gateway->charges);
$this->assertSame(300, $gateway->charges[0]['amountCents']);
```

---

## Мокаем vs не мокаем: чёткие правила

### Не мокаем (используем реальное)

| Что | Почему |
|---|---|
| Доменные объекты (`Order`, `Money`, `CustomerId`) | Это **то**, что тестируем. Мок домена = мёртвый тест. |
| Doctrine EntityManager + PostgreSQL | Cascade, orphanRemoval, транзакции — реальное поведение. |
| Репозиторий (реализация) | `toDomain()` / `fromDomain()` — источник багов. |
| Symfony Kernel (в E2E) | Роутинг, middleware, аутентификация — реальный код. |
| Domain events | `OrderConfirmed` — реальный объект. Не `createMock()`. |

### Мокаем (заменяем на fake)

| Что | Почему |
|---|---|
| Stripe / PayPal / внешний API | Нет сети в CI. Нет API-ключей. Нестабильно. |
| SMTP / Mailer | Не отправляем письма в tests. |
| Meilisearch / Elasticsearch | Нет кластера. Медленно. |
| Clock / `time()` / `DateTimeImmutable('now')` | Тест не должен зависеть от часа. |
| Random / `rand()` / `uuid()` | Детерминизм. |
| File system (в домене) | Скорость. Изоляция. |

### Граница

```
┌─────────────────────────────────────────────────────────────┐
│  МОКАЕМ: то, что НАХОДИТСЯ ВНЕ процесса и/или вне БД        │
│  Stripe, SMTP, Meilisearch, Redis, File, Clock, Random     │
├─────────────────────────────────────────────────────────────┤
│  НЕ МОКАЕМ: то, что НАХОДИТСЯ ВНУТРИ процесса и/или в БД    │
│  Domain, Doctrine, PostgreSQL, Symfony Kernel, Events       │
└─────────────────────────────────────────────────────────────┘
```

---

## Скорость: как держать < 30 сек на полный прогон

### 1. Unit: 0.5–2 сек

```bash
# Никакой инфраструктуры. Только PHP.
# 200 тестов × 5 мс = 1 сек.
php vendor/bin/phpunit --testsuite Unit --no-coverage
```

### 2. Integration: 5–10 сек

```bash
# PostgreSQL в Docker (уже запущен, не поднимается на каждый тест).
# TRUNCATE + transactional rollback = O(1) на setup.
# 50 тестов × 150 мс = 7.5 сек.
php vendor/bin/phpunit --testsuite Integration --no-coverage
```

**Приём:** один `docker-compose up` на весь прогон. Не `up` + `down` на каждый тест.

```yaml
# CI (GitHub Actions):
- name: Start PostgreSQL
  run: docker-compose -f docker-compose.test.yml up -d

- name: Run tests
  run: |
    php vendor/bin/phpunit --testsuite Unit
    php vendor/bin/phpunit --testsuite Integration
    php vendor/bin/phpunit --testsuite E2E

- name: Stop PostgreSQL
  run: docker-compose -f docker-compose.test.yml down
```

### 3. E2E: 2–5 сек

```bash
# Symfony Test Client — in-process. Нет TCP overhead.
# 15 тестов × 200 мс = 3 сек.
php vendor/bin/phpunit --testsuite E2E --no-coverage
```

### 4. Параллельный запуск

```bash
# PHPUnit 10+ / paraunit:
php vendor/bin/paraunit --processes=4 --testsuite Integration
# 4 параллельных процесса. Каждый со своим schema (test_1, test_2, ...).
```

Или:

```bash
# GitHub Actions matrix:
- name: Run test group ${{ matrix.group }}
  run: php vendor/bin/phpunit --filter "/${{ matrix.group }}/"
```

### 5. Не запускать лишнего

```php
// ❌ В unit-тесте:
$client = static::createClient();   // ← boot kernel. 200 мс.
$this->em = $client->getContainer()->get('doctrine')->getManager();

// ✅ Unit-тест:
$order = new Order(...);            // ← 0 мс. Нет kernel.
```

```bash
# Не включаем coverage на CI для unit-тестов (ускоряет ×3):
php vendor/bin/phpunit --testsuite Unit --no-coverage
# Coverage — отдельный job, только для Integration:
php vendor/bin/phpunit --testsuite Integration --coverage-text
```

---

## Стабильность: как не получить «тест падает на 10-м прогоне»

### 1. Детерминизм

```php
// ❌ Нестабильно:
public function test_payment_expires(): void
{
    $payment = Payment::initiate(..., new \DateTimeImmutable('-1 day'));
    $this->assertTrue($payment->isExpired());   // ← зависит от текущего времени
}

// ✅ Стабильно:
public function test_payment_expires(): void
{
    $fixedNow = new \DateTimeImmutable('2024-06-15 12:00:00');
    $payment = Payment::initiate(..., $fixedNow->modify('-1 day'));
    $this->assertTrue($payment->isExpiredAt($fixedNow));
}
```

**Правило:** время — параметр. Не `new \DateTimeImmutable()` внутри теста.

### 2. Изоляция

```php
// ❌ Тест B зависит от Test A:
public function test_a(): void { $this->repo->save($order1); }
public function test_b(): void {
    $orders = $this->repo->findAll();   // ← видит $order1 из test_a!
    $this->assertCount(1, $orders);
}

// ✅ Каждый тест: чистая БД.
protected function setUp(): void
{
    parent::setUp();
    $this->truncateAllTables();   // ← O(1). Не DELETE.
}
```

### 3. Нет внешних зависимостей

```php
// ❌
public function test_charge(): void
{
    $result = $this->stripe->charge('card_123', 100);   // ← реальный Stripe API
    $this->assertTrue($result->success);
}

// ✅
public function test_charge(): void
{
    $gateway = $this->getContainer()->get(InMemoryPaymentGateway::class);
    $result = $gateway->charge('ref_1', 100);
    $this->assertTrue($result['success']);
}
```

### 4. Нет race conditions

```php
// ❌ Параллельные тесты пишут в одну таблицу:
// test_a: INSERT INTO orders (id = 1, ...)
// test_b: INSERT INTO orders (id = 1, ...)   ← conflict

// ✅ Каждый параллельный процесс — своя БД:
# .env.test (process 1): DATABASE_URL=.../test_1
# .env.test (process 2): DATABASE_URL=.../test_2
```

### 5. Timeout

```yaml
# phpunit.xml
<phpunit>
    <php>
        <ini name="memory_limit" value="256M"/>
    </php>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

```php
// Если тест > 5 сек — он сломан. Не «медленный БД».
// На CI: timeout на job = 2 мин. Если не уложился → fail.
```

---

## CI: полный пайплайн

```yaml
# .github/workflows/test.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 1s
          --health-timeout 5s
          --health-retries 10

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, redis
      - run: composer install --no-interaction

      - name: Create test database
        run: |
          psql -h localhost -U test -c "CREATE DATABASE test_2;"

      - name: Run Unit tests
        run: php vendor/bin/phpunit --testsuite Unit --no-coverage
        env:
          DATABASE_URL: "postgresql://test:test@localhost:5432/test?serverVersion=16"

      - name: Run Integration tests
        run: php vendor/bin/phpunit --testsuite Integration --no-coverage
        env:
          DATABASE_URL: "postgresql://test:test@localhost:5432/test?serverVersion=16"

      - name: Run E2E tests
        run: php vendor/bin/phpunit --testsuite E2E --no-coverage
        env:
          DATABASE_URL: "postgresql://test:test@localhost:5432/test?serverVersion=16"

      - name: Coverage (отдельно)
        run: |
          composer require --dev phpunit/php-code-coverage xdebug/xdebug
          php vendor/bin/phpunit --testsuite Integration --coverage-clover=coverage.xml
        if: github.ref == 'refs/heads/main'
```

**Время прогона:**

| Слой | Тестов | Время |
|---|---|---|
| Unit | 200 | ~1 сек |
| Integration | 50 | ~7 сек |
| E2E | 15 | ~3 сек |
| **Итого** | **265** | **~11 сек + boot 3 сек = 14 сек** |

---

## Честные ограничения

### 1. «Реальная БД в integration» vs «SQLite»

| | PostgreSQL (Docker) | SQLite (in-memory) |
|---|---|---|
| Точность | Высокая (MVCC, locks, JSONB) | Низкая (нет MVCC, нет `FOR UPDATE`, `JSON` ≠ `JSONB`) |
| Скорость | 7 сек | 3 сек |
| Стабильность | Высокая (одинаковая БД как в проде) | Средняя (SQLite ≠ PG в edge cases) |
| CI-сложность | Docker service | 0 |

**Мой выбор:** PostgreSQL. Разница в 4 секунды не стоит того, чтобы `FOR UPDATE SKIP LOCKED` или `JSONB ->>` работали в тесте, а в проде — нет.

### 2. «Покрыть всё» — невозможно

| Что не покрыть | Почему |
|---|---|
| 100% branch coverage | `if (PHP_VERSION_ID >= 80300)` — нет смысла. |
| Каждый exception path | `throw new DomainException('...')` — покрываем `expectException`. Не каждый `catch`. |
| Doctrine internals | Это библиотека. Не наш код. |
| Symfony router | Фреймворк. Не наш код. |

**Цель:** не «100% coverage». Цель: «каждый инвариант домена — минимум 1 тест. Каждый переход состояния — 1 тест. Каждый handler — 1 happy path + 1 error path».

### 3. Тесты не заменяют мониторинг

```
Тест покрывает: «confirm() бросает, если нет строк».
Тест НЕ покрывает: «а что если БД вернёт NULL в total_cents из-за бага в миграции».
→ Это покрывает CHECK constraint + алерт на `total_cents IS NULL`.
```

Тесты — **предупреждение**. Production monitoring — **детекция**. Оба нужны.

---

## Итого

```
Unit (50–70%)
  → Домен. Чистый PHP. 0.5–2 сек.
  → Мокаем: ничего.
  → Не мокаем: доменные объекты.

Integration (20–30%)
  → Репозиторий + Handler + PostgreSQL. 5–10 сек.
  → Мокаем: внешние API (Stripe, SMTP, Search).
  → Не мокаем: Doctrine, БД, домен.

E2E (5–10%)
  → HTTP → Kernel → БД. 2–5 сек.
  → Мокаем: внешние API (через fake в DI).
  → Не мокаем: kernel, БД, роутинг.

Скорость:  < 30 сек на полный прогон.
Стабильность:  детерминизм, изоляция, нет сети, нет clock.
CI:  один Docker-контейнер PostgreSQL, три suites, 14 сек.
```

Пирамида — не «правило». Она **следует** из того, где живёт логика. Если 80% логики в домене → 80% unit-тестов. Если 60% логики в оркестрации (Application) → больше integration. Пропорция не задана заранее. Она **выпадает** из распределения кода.

> ⏱ 76.6s · prompt 102046 tok · output 9202 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

The user is asking about concurrent modification problems in a database when two users simultaneously modify the same record. They want:
1. What problems can arise
2. What protection methods exist
3. Comparison of approaches
4. When to choose which

This is a natural continuation of our earlier discussion about concurrent modifications to aggregates. Let me think about what to cover:

Problems:
- Lost update (classic)
- Dirty read
- Non-repeatable read
- Phantom read
- Deadlock
- Inconsistent state (partial writes)

Protection methods:
1. Pessimistic locking (SELECT FOR UPDATE)
2. Optimistic locking (version column)
3. SELECT FOR UPDATE SKIP LOCKED
4. Advisory locks (PostgreSQL)
5. Serializable isolation level
6. Application-level queues (serialize access)
7. Conflict detection via CHECK constraints
8. CRDT / merge strategies
9. Distributed locks (Redis)
10. Idempotency + last-write-wins

Let me structure this clearly, with concrete examples, code, and a decision framework. I'll write in Russian as the conversation has been in Russian.

Let me think about the structure:
1. What goes wrong (concrete scenarios)
2. Protection methods (each with mechanism, code, pros/cons)
3. Comparison table
4. Decision framework
5. Anti-patterns

I should be concrete, use code examples, and maintain the practical tone of the conversation.

</details>

**Кандидат:**

# Конкурентные изменения одной записи

## Что может пойти не так

### 1. Lost Update (потерянное обновление)

Классический сценарий. Два процесса читают, оба пишут, второй перезаписывает первого.

```
t=0  A: SELECT balance FROM accounts WHERE id=1  → 150
t=0  B: SELECT balance FROM accounts WHERE id=1  → 150

t=1  A: 150 - 100 = 50.  «Хватает. Списываю.»
t=1  B: 150 - 100 = 50.  «Хватает. Списываю.»

t=2  A: UPDATE accounts SET balance=50 WHERE id=1.  COMMIT.
t=2  B: UPDATE accounts SET balance=50 WHERE id=1.  COMMIT.

Итог: balance = 50. Списано 200. Потеряно 150.
```

Ни один `CHECK (balance >= 0)` не сработает. Баланс не отрицательный. Инвариант «списано ≤ начислено» нарушен молча.

### 2. Inconsistent State (непоследовательное состояние)

```
Заказ: status = 'draft', lines = [A, B]

A: добавляет строку C → UPDATE lines
B: подтверждает заказ → UPDATE status = 'confirmed'

Если A и B пересекаются:
  → status = 'confirmed', но C ещё не закоммичена.
  → Или: C закоммичена, но статус ещё 'draft' (B откатился).
  → Или: оба применились, но в разном порядке.
```

### 3. Dirty Read

```
A: BEGIN; UPDATE orders SET total=500 WHERE id=1;  (ещё не COMMIT)
B: SELECT total FROM orders WHERE id=1;  → видит 500 (если isolation = READ UNCOMMITTED)
A: ROLLBACK;
B: получил 500. Но в БД 400.
```

В PostgreSQL по умолчанию `READ COMMITTED` — dirty read не возникает. Но при `REPEATABLE READ` или `SERIALIZABLE` свои проблемы.

### 4. Non-Repeatable Read

```
A: BEGIN (REPEATABLE READ);
A: SELECT total FROM orders WHERE id=1;  → 400
B: UPDATE orders SET total=500 WHERE id=1; COMMIT;
A: SELECT total FROM orders WHERE id=1;  → 400 (видит снапшот)
A: UPDATE orders SET total=400+100 WHERE id=1;  → перезаписал 500 на 500.
```

### 5. Phantom Read

```
A: BEGIN (REPEATABLE READ);
A: SELECT * FROM order_lines WHERE order_id=1;  → 3 строки
B: INSERT INTO order_lines (order_id=1, ...); COMMIT;
A: SELECT * FROM order_lines WHERE order_id=1;  → 3 строки (фантазмов не видит)
A: DELETE FROM order_lines WHERE order_id=1;  → удалил 3. 4-я осталась.
```

### 6. Deadlock

```
A: SELECT ... FOR UPDATE (order_id=1);   -- держит lock
B: SELECT ... FOR UPDATE (order_id=2);   -- держит lock
A: SELECT ... FOR UPDATE (order_id=2);   -- ждёт B
B: SELECT ... FOR UPDATE (order_id=1);   -- ждёт A

→ Deadlock. PostgreSQL детектит через deadlock_timeout (default 1с).
→ Один из процессов получает 40P01. Другой продолжает.
```

---

## Способы защиты

### 1. Pessimistic Lock: `SELECT ... FOR UPDATE`

#### Механизм

```sql
BEGIN;
SELECT * FROM orders WHERE id = 42 FOR UPDATE;
-- → EXCLUSIVE lock на строку.
-- → Любой другой SELECT ... FOR UPDATE на эту строку БЛОКИРУЕТСЯ.
-- → Любой UPDATE / DELETE на эту строку БЛОКИРУЕТСЯ.

-- Здесь: проверка инвариантов, мутация, запись.
UPDATE orders SET status = 'confirmed' WHERE id = 42;
COMMIT;
-- → Lock снят. Ожидатели получают строку.
```

#### В PHP

```php
final class OrderService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function confirm(int $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) {
            $entity = $em->createQueryBuilder()
                ->select('o')
                ->from(OrderEntity::class, 'o')
                ->where('o.id = :id')
                ->setParameter('id', $orderId)
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)   // ← FOR UPDATE
                ->getQuery()
                ->getOneResult();

            $order = $entity->toDomain();
            $order->confirm();   // доменная валидация ПОД lock'ом

            $entity->setStatus($order->status());
            $em->flush();
            // COMMIT → lock снят.
        });
    }
}
```

#### Что это даёт

| | |
|---|---|
| + Простая семантика: «прочитал → решил → записал». Никто не вмешается. | − **Блокировка.** Второй процесс ждёт. |
| + Инварианты проверяются под lock'ом. Невозможно «между SELECT и UPDATE». | − Deadlock-риск при нескольких строках. |
| + Работает при любой конкурентности (очередь на lock). | − Long lock = long wait. |
| + Нет retry-логики. | − Если процесс упал (kill -9) → lock до `lock_timeout`. |

#### Таймауты

```sql
-- Не ждать вечно:
SET lock_timeout = '5s';

-- При превышении:
-- 55P03: lock_not_available
```

```php
// В Doctrine:
$this->em->getConnection()->executeStatement('SET lock_timeout = 5000');
```

#### Когда

- Конкурентность средняя (10–200 одновременных на строку).
- Операция короткая (< 1 сек).
- Нет внешних вызовов в пределах lock'а.
- «First write wins» — доменное требование.
- Сложный инвариант (зависит от 3+ полей).

#### Когда нет

- Конкурентность > 500 на строку. Очередь на lock растёт линейно.
- Операция > 5 сек.
- Внешний HTTP в пределах lock'а. **Никогда.**

---

### 2. Optimistic Lock: version column

#### Механизм

```sql
-- При чтении: version = 7 запомнен.
-- При записи:
UPDATE orders
SET status = 'confirmed', version = version + 1
WHERE id = 42 AND version = 7;   -- ← версия, которую мы читали

-- Если 0 rows affected → кто-то успел раньше.
```

#### В PHP (Doctrine)

```php
#[ORM\Entity]
#[ORM\OptimisticLock]          // ← атрибут. Doctrine сам добавит version в WHERE.
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 0;   // ← Doctrine управляет.

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;
}
```

При `flush()` Doctrine генерирует:

```sql
UPDATE orders
SET status = 'confirmed', version = 1
WHERE id = 42 AND version = 0;
```

Если `0 rows` → `Doctrine\OptimisticLockException`.

#### С retry

```php
final class OrderService
{
    public function confirm(int $orderId): void
    {
        $maxRetries = 3;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $this->em->transactional(function (EntityManagerInterface $em) use ($orderId) {
                    $entity = $em->find(OrderEntity::class, $orderId);
                    $order  = $entity->toDomain();
                    $order->confirm();
                    $entity->setStatus($order->status());
                    $em->flush();
                    // → UPDATE ... WHERE version = X
                    // → если conflict → OptimisticLockException
                });
                return;   // успех
            } catch (OptimisticLockException $e) {
                if ($attempt === $maxRetries - 1) {
                    throw new ConcurrencyConflictException(
                        "Order {$orderId}: too many concurrent modifications",
                        0, $e
                    );
                }
                // Exponential backoff:
                usleep((int) (1000 * (2 ** $attempt)));
            }
        }
    }
}
```

#### Что это даёт

| | |
|---|---|
| + **Нет блокировок.** Процессы не ждут друг друга. | − Конфликт = исключение. Нужно обрабатывать. |
| + Нет deadlocks. | − При высокой конкурентности → много конфликтов → много retry. |
| + Масштабируется горизонтально (N процессов, 0 lock'ов). | − Не защищает от «логического» конфликта (оба пишут в **разные** поля). |
| + Простота: одна колонка, одна проверка в `WHERE`. | − Пользователь видит 409 «попробуйте ещё раз». |
| | − Не подходит для «сложения» (оба должны пройти). |

#### Когда

- Конкурентность **низкая** (< 50 одновременных на строку).
- Конфликт — **редкость** (< 5% запросов).
- Операция может быть **повторена** (идемпотентна).
- «Last write wins» допустимо (или «первый + остальные retry»).

#### Когда нет

- Конкурентность > 200 на строку. 50% конфликтов → 50% retry → деградация.
- Операция **не идемпотентна** (повтор = двойное списание).
- «Оба должны пройти» (сложение, append).

---

### 3. `FOR UPDATE SKIP LOCKED` (очередь)

#### Механизм

```sql
-- Вместо «жди, пока lock освободится»:
SELECT * FROM tasks
WHERE status = 'pending'
ORDER BY created_at
LIMIT 10
FOR UPDATE SKIP LOCKED;   -- ← пропусти строки, которые уже заблокированы

-- 10 свободных задач. Заблокированные — пропущены.
-- Другой воркер не ждёт. Берёт свои.
```

#### В PHP

```php
final class TaskProcessor
{
    public function processBatch(): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) {
            $tasks = $em->createQueryBuilder()
                ->select('t')
                ->from(TaskEntity::class, 't')
                ->where('t.status = :status')
                ->setParameter('status', 'pending')
                ->orderBy('t.createdAt', 'ASC')
                ->setMaxResults(10)
                ->setLockMode(LockMode::PESSIMISTIC_READ)   // ← FOR UPDATE
                ->getQuery()
                ->getResult();

            // Но нужен SKIP LOCKED — Doctrine не поддерживает напрямую.
            // Через native query:
        });
    }
}
```

```sql
-- Native (PostgreSQL):
SELECT * FROM tasks
WHERE status = 'pending'
ORDER BY created_at
LIMIT 10
FOR UPDATE SKIP LOCKED;
```

#### Что это даёт

| | |
|---|---|
| + Никто не ждёт. Каждый берёт **свои** задачи. | − Только для «очереди задач», а не «одной строки». |
| + Линейное масштабирование: N воркеров = N × throughput. | − Не решает «два пользователя меняют одну и ту же строку». |
| + Нет deadlocks (каждый держит свой набор). | − Не подходит для read-modify-write на одной строке. |

#### Когда

- **Очередь задач**: N воркеров берут N задач из общей таблицы.
- **Batch processing**: «взял 100, обработал, отпустил».
- Не подходит для «два пользователя редактируют один заказ».

---

### 4. Serializable Isolation

#### Механизм

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
BEGIN;
SELECT * FROM orders WHERE id = 42;
-- ... логика ...
UPDATE orders SET status = 'confirmed' WHERE id = 42;
COMMIT;
```

PostgreSQL реализует это через **SSI (Serializable Snapshot Isolation)**:
- Читает снапшот.
- Если другой процесс изменил данные, которые вы читали → **SERIALIZATION FAILURE** (`40001`).
- Транзакция откатывается. Нужно повторить.

#### Что это даёт

| | |
|---|---|
| + Максимальная строгость: «как будто последовательно». | − **Частые serialization failures** при высокой конкурентности. |
| + Не нужно думать о lock'ах. | − Каждый retry = полный повтор транзакции. |
| + Корректность доказана формально. | − Производительность падает при > 100 TPS на одну строку. |

#### Когда

- Формальная корректность критична (банки, учёт).
- Конкурентность **низкая** (< 50 TPS на строку).
- Готовы к retry-логике на **всю транзакцию**.

#### Когда нет

- Высокая конкурентность. Serialization failures → 50% retry → деградация.
- Транзакция длинная (> 1 сек). Увеличивает вероятность конфликта.

---

### 5. Application-level Serialization (очередь)

#### Механизм

```
Вместо:  N процессов одновременно пишут в order_id = 42
Становится:  N задач в очередь → 1 воркер обрабатывает последовательно
```

```php
// Отправка:
$this->messenger->dispatch(new ConfirmOrderCommand($orderId));

// Конфиг: 1 воркер на очередь.
// php bin/console messenger:consume order_queue --limit=1
```

#### Что это даёт

| | |
|---|---|
| + **Ноль конкурентности.** Ноль lock'ов. Ноль конфликтов. | − Задержка: задачи ждут в очереди. |
| + Ноль deadlocks. | − Сложность: мониторинг, DLQ, идемпотентность. |
| + Инварианты проверяются последовательно. | − Не масштабируется на **одну** строку (1 воркер = 1 задача). |
| + Гибкость: можно вставить паузу, лог, алерт. | − Потеря события при crash (нужен outbox). |

#### Когда

- Конкурентность очень высокая (1000+ на строку).
- Операции долгие (> 5 сек).
- Есть внешние вызовы (HTTP, 3rd-party API).
- Доменная модель — команды (CQRS).

#### Когда нет

- Нужен синхронный ответ.
- Низкая конкурентность.
- Избыточная сложность.

---

### 6. Distributed Lock (Redis / PostgreSQL advisory lock)

#### Механизм (PostgreSQL)

```sql
-- Advisory lock: не связан с таблицей. Ключ — число.
SELECT pg_advisory_xact_lock(42);   -- 42 = order_id
-- Блокирует. Другой процесс с тем же ключом ждёт.
-- Снимается при COMMIT / ROLLBACK.

-- Или с таймаутом:
SELECT pg_try_advisory_xact_lock(42);
-- → true / false. Не ждёт.
```

#### В PHP

```php
$this->em->transactional(function ($em) {
    // Advisory lock на order_id:
    $got = $em->getConnection()->fetchOne(
        'SELECT pg_try_advisory_xact_lock(:id)',
        ['id' => $orderId]
    );

    if ($got !== 't') {
        throw new ConcurrencyConflictException("Order {$orderId} is locked");
    }

    // Критическая секция:
    $order = $this->repo->find($orderId);
    $order->confirm();
    $this->repo->save($order);
    $em->flush();
    // COMMIT → advisory lock снят.
});
```

#### Что это даёт

| | |
|---|---|
| + Работает **между** базами (advisory lock — в одной БД. Redis — между БД). | − Redis: отдельная инфраструктура. SPOF. |
| + Нет блокировки на уровне строк. | − Advisory lock: привязан к сессии. При disconnect → lock висит. |
| + Можно блокировать «логическую сущность» (не строку). | − Сложность: TTL, renewal, crash recovery. |
| + `pg_try_advisory_lock` → non-blocking. | − Не стандартизирован. Сложнее отлаживать. |

#### Когда

- Блокировка «логической сущности», а не строки (например, «пользователь не может иметь 2 активных подписок»).
- Межпроцессная синхронизация без привязки к таблице.
- Redis: горизонтальное масштабирование через shared lock.

#### Когда нет

- Одна БД, одна строка → `FOR UPDATE` проще.
- Нет Redis → advisory lock.
- Сложность > пользы.

---

## Сравнение

| Критерий | Pessimistic (FOR UPDATE) | Optimistic (version) | Serializable | Очередь | Advisory lock |
|---|---|---|---|---|---|
| **Блокировка** | Да | Нет | Да (SSI) | Нет (1 воркер) | Да |
| **Deadlock-риск** | Да | Нет | Да | Нет | Да |
| **Retry при конфликте** | Нет (ждёт) | Да | Да (весь транзакция) | Нет | Нет (или `try_`) |
| **Масштабируемость (1 строка)** | O(N) waiting | O(N) retry | O(N) retry | O(1) (1 воркер) | O(N) waiting |
| **Масштабируемость (N строк)** | O(N) | O(N) | O(N) | O(N) воркеров | O(N) |
| **Сложность** | Низкая | Средняя | Средняя | Высокая | Средняя |
| **Внешние вызовы в lock'е** | ❌ | ✅ | ❌ | ✅ | ❌ |
| **Latency при конфликте** | Ждёт (0–∞) | Retry (1–3) | Retry (1–∞) | Очередь (0–∞) | Ждёт / false |
| **Корректность** | Сильная | Сильная (при retry) | Максимальная | Сильная | Сильная |
| **Читаемость** | Высокая | Средняя | Высокая | Средняя | Средняя |

---

## Дерево решений

```
Два процесса пишут в одну строку одновременно.

Q1: Операция короткая (< 1 сек) и нет внешних вызовов?
  → Да: Q2
  → Нет: Q3

Q2: Конкурентность < 100 на строку?
  → Да: Pessimistic (FOR UPDATE). Простое. Надёжное.
  → Нет: Q4

Q3: Можно асинхронно? (ответ через 1–5 сек)
  → Да: Очередь (1 воркер на агрегат).
  → Нет: Pessimistic + разбить: lock → внешняя операция → lock.

Q4: Конфликт — редкость (< 5% запросов)?
  → Да: Optimistic (version) + retry.
  → Нет: Q5

Q5: «Оба должны пройти» (сложение) или «один должен пройти» (замена)?
  → Оба: Pessimistic + атомарный UPDATE (SET balance = balance - X).
  → Один: Optimistic. Проигравший получает 409.

Q6: Формальная корректность критична (банк, учёт)?
  → Да: SERIALIZABLE + retry.
  → Нет: Pessimistic / Optimistic.
```

---

## Атомарный UPDATE (для «сложения»)

Особый случай: оба процесса **складывают** (оба списывают, оба начисляют). Здесь не «первый выиграл», а «оба применились».

```sql
-- ❌ Read → modify → write (race condition):
SELECT balance FROM accounts WHERE id = 1;  → 150
UPDATE accounts SET balance = 150 - 100 WHERE id = 1;   -- на основе СТАРОГО значения

-- ✅ Атомарный UPDATE (single-statement):
UPDATE accounts
SET balance = balance - 100
WHERE id = 1 AND balance >= 100;   -- ← условие в WHERE

-- 0 rows → недостаточно средств.
-- 1 row  → списано.
-- Оба процесса могут сделать это одновременно.
-- PostgreSQL: один блокирует строку, второй ждёт.
-- Второй: balance = 50. 50 >= 100 → false. 0 rows. InsufficientFunds.
```

```php
public function debit(int $accountId, int $amountCents): void
{
    $affected = $this->em->getConnection()->executeStatement(
        'UPDATE accounts SET balance_cents = balance_cents - :amount
         WHERE id = :id AND balance_cents >= :amount',
        ['amount' => $amountCents, 'id' => $accountId]
    );

    if ($affected === 0) {
        throw new InsufficientFundsException($accountId);
    }
}
```

**Нет SELECT. Нет lock'а. Один UPDATE. Атомарно.**

Это **лучший** способ для «сложения» / «вычитания». Ни pessimistic, ни optimistic не нужны. БД сама гарантирует атомарность через row lock на время `UPDATE`.

---

## Антипаттерны

### ❌ `FOR UPDATE` + HTTP-вызов

```php
$this->em->transactional(function ($em) {
    $entity = /* FOR UPDATE */;

    $result = $this->stripe->charge(...);   // ← 10 сек. Lock на 10 сек.
    // 100 пользователей × 10 сек = 1000 сек блокировок.

    $entity->setResult($result);
    $em->flush();
});
```

**Правильно:** lock (50 мс) → HTTP (10 сек, без lock) → lock (50 мс).

### ❌ `SELECT` без `FOR UPDATE` + `UPDATE`

```php
$balance = $em->fetchOne('SELECT balance FROM accounts WHERE id = 1');
// ← без FOR UPDATE. MVCC: видит снапшот.
if ($balance >= $amount) {
    $em->execute('UPDATE accounts SET balance = :b - :a WHERE id = 1', [...]);
    // ← другой процесс уже изменил balance. Наш UPDATE перезаписал.
}
```

**Правильно:** `FOR UPDATE` или атомарный `UPDATE ... WHERE balance >= :amount`.

### ❌ `OptimisticLock` без retry

```php
// При конфликте → OptimisticLockException → 500 для пользователя.
// Нет retry. Нет сообщения «попробуйте ещё раз».
// Нет алерта на «конфликты > 5%».
```

**Правильно:** retry (2–3) + backoff + при исчерпании → 409 + алерт.

### ❌ `SERIALIZABLE` на всё

```yaml
# postgresql.conf
default_transaction_isolation = serializable
```

Каждая транзакция → SSI overhead. При 1000 TPS → 30% serialization failures. **Не нужно**, если нет формальных требований.

### ❌ «Я сам отслеживаю конфликты в коде»

```php
// «Если timestamp изменился — конфликт»
$old = $this->repo->find($id);
// ... 2 сек ...
$new = $this->repo->find($id);
if ($old->modifiedAt !== $new->modifiedAt) {
    throw new ConflictException();
}
$this->repo->save($new);   // ← всё равно может перезаписать.
```

**Правильно:** `version` в `WHERE` или `FOR UPDATE`. Доверять БД, а не «своей логике».

---

## Итого

| Ситуация | Решение |
|---|---|
| Сложение / вычитание (оба проходят) | **Атомарный UPDATE** с условием в `WHERE` |
| Замена (один выигрывает), < 100 RPS | **Pessimistic** (`FOR UPDATE`) |
| Замена (один выигрывает), > 100 RPS, конфликт редок | **Optimistic** (version) + retry |
| Долгая операция + внешний HTTP | **Разбить**: lock → внешняя → lock |
| Очень высокая конкурентность, асинхронно допустимо | **Очередь** (1 воркер) |
| Формальная корректность (банк) | **SERIALIZABLE** + retry |
| Логическая сущность (не строка) | **Advisory lock** / **Redis lock** |
| Очередь задач (N воркеров, N задач) | **`FOR UPDATE SKIP LOCKED`** |

**Принцип:** защита должна быть на **уровне, где происходит мутация**. Не в контроллере. Не в middleware. В `UPDATE ... WHERE` или в `FOR UPDATE` перед мутацией. Всё остальное — обёртка.

> ⏱ 59.8s · prompt 110984 tok · output 6485 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

The user is asking about my git process from task to production: branching, commit format, pull requests, code review, releases. And why each choice is made this way.

Let me think about what a senior PHP/Symfony developer would actually do in a real team of 3-15 people, working on a SaaS product with CI/CD. I should be concrete, practical, and explain the "why" behind each decision. I should avoid being dogmatic - present it as a working process with trade-offs, not a religion.

Let me structure this:
1. Branching strategy (why not git-flow, why not trunk-based, what I actually do)
2. Commit format (conventional commits? why? why not?)
3. PR structure and process
4. Code review - what I look for, what I don't
5. CI pipeline
6. Release process (tags, changelog, deployment)
7. Hotfix process
8. Why each choice (the "not otherwise" part)

I should be honest about trade-offs and where the "textbook" answer diverges from what actually works in practice. The user has been asking deep technical questions and I've been giving structured, honest answers with trade-offs. I should continue that style.

Let me think about what's actually pragmatic for a team of 3-15 developers working on a Symfony app:

Branching: I'll go with a short-lived feature branch model (not full git-flow, not pure trunk-based). Feature branches live 1-3 days, not weeks. Release branches are short-lived (created from main when a release is imminent). Hotfix branches from production tag.

Commits: Conventional commits, but pragmatically. Not over-engineered.

PR: Small (< 400 lines changed). One concern per PR. Description template.

Review: What I look for (correctness, security, performance) vs what I don't (style - that's linter's job).

CI: Lint → PHPStan → Tests → Build → Deploy.

Release: Semantic versioning, changelog, tag → deploy.

Let me write this in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Git-процесс: от задачи до продакшена

## Контекст

Команда 3–15 разработчиков. Symfony 7, PostgreSQL, Docker. CI — GitHub Actions. Деплой — Kubernetes (или bare metal с Ansible). Один main-репозиторий, один продукт.

Я описываю процесс, который **работает** и который я бы применил завтра. Не «как в книге». Не «как в нашей прошлой компании». Конкретный процесс с обоснованием каждого решения.

---

## 1. Ветвление

### Схема

```
main ──────────────────────────────────────────────────────────►
  │
  ├── feature/payment-webhook      (живёт 1–2 дня)
  ├── feature/order-refactor       (живёт 3–4 дня)
  ├── fix/empty-cart-edge-case     (живёт 4 часа)
  │
  ├── release/1.42.0              (живёт 1 день, от main)
  │
  └── hotfix/1.42.1              (живёт 4 часа, от тега v1.42.0)
       │
       └── merge-back into main
```

### Правила

| Правило | Почему |
|---|---|
| `main` всегда деплой-готов. CI зелёный. | `main` — это «то, что в проде». Если main сломан — продакшен сломан. Никаких «ну, это же main, а не branch». |
| Feature-ветка живёт **1–4 дня**. Не недели. | Чем дольше живёт ветка — тем больше merge conflicts, тем сложнее ревью, тем больше «я потерял контекст за 3 недели». |
| Одна ветка = одна логическая задача. | Не `feature/order+payment+search`. Это три ветки. |
| Нет веток `develop`, `staging`, `testing`. | Каждая лишняя ветка — лишний merge, лишний «а что тут изменилось?», лишний шаг в CI. |
| `release/X.Y.Z` — короткоживущая. Создаётся, когда 80% фич на main. | Не «ветка на месяц». Ветка на **день**. Финальные баг-фиксы, changelog, bump версии. |
| `hotfix/X.Y.Z+1` — от тега продакшена. | Не от `main` (там может быть новая фича, которая ломает hotfix). От конкретного тега — стабильная база. |
| Merge в `main` — только через PR + CI + 1+ approval. | Защита от «я запушил, а оно не работает». |

### Почему не git-flow

Git-flow (`develop` → `release` → `main` + `hotfix`) создан в 2010 году для команды из 5+ человек, где релизы выходят раз в квартал.

В реальности 2024 года:
- Релизы — каждый день или каждый спринт.
- `develop` — это «second main», который расходит с `main` и порождает конфликты.
- `staging`-ветка — это «ветка, которую никто не понимает».
- Каждый `merge develop → main` — ритуал, а не необходимость.

**Trunk-based** (только `main` + feature-ветки) — ближе к реальности. Но без `release`-ветки у меня нет места для «последнего баг-фикса перед релизом». Поэтому: `main` + `feature/*` + `release/*` (короткие) + `hotfix/*`.

### Почему не trunk-based (только main)

«Пушай в main, CI проверит». Это работает для команды из 2 человек, которая делает деплой 5 раз в день.

Для команды из 10 человек:
- 10 человек пушат в main одновременно.
- CI на main — 5 минут. 10 × 5 мин = очередь.
- Один сломанный пуш → main красный → 10 человек ждут.
- Нет места для «починить перед релизом».

Feature-ветка — это **не** «ветка на неделю». Это **изолированный контекст** для ревью. Я могу посмотреть 200 строк изменений, не смешанных с 800 строк чужого.

---

## 2. Коммиты

### Формат

```
<type>(<scope>): <imperative summary>

[optional body]

[optional footer]
```

### Примеры

```
feat(payment): add webhook handler for Stripe payment_intent.succeeded

Handles payment confirmation from Stripe webhook.
Updates Payment status to Settled and dispatches PaymentSettled event.

Closes #482
```

```
fix(order): reject negative quantity in OrderLine constructor

OrderLine::addLine() was accepting qty=0 and qty=-5.
Now throws DomainException with message 'Quantity must be positive'.
```

```
refactor(infra): replace Doctrine custom type with embeddable for Money

Reduces column count from 2 to 1 (JSONB).
Removes MoneyType::convertToPHPValue edge case with NULL.
```

```
chore(ci): bump PHPStan from level 6 to level 8

Adds 47 new strict type checks.
Fixes 12 resulting violations in Application layer.
```

### Правила

| Правило | Почему |
|---|---|
| **Imperative mood**: `fix`, не `fixed`. `add`, не `added`. | Читаются как список: «git log --oneline» → «fix: reject negative quantity» / «add: webhook handler». Глагол + объект. |
| **Scope обязателен** (кроме `chore`). | `fix(order): ...` сразу говорит, в каком модуле. Без scope: 50 коммитов `fix: bug`. С scope: фильтр. |
| **Max 72 символа** в первой строке. | `git log --oneline` в терминале. Не обрывается. |
| **Один коммит = одна логическая единица**. | Не «fix bug + add test + refactor + rename variable». Это 4 коммита. |
| **Не коммитить WIP**. | `WIP: doing stuff` в main = красный CI для всех. |
| **Body — опционален**. Для простых фиксов не нужен. | Не заполнять ради заполнения. |
| **Closes #123** в footer. | Автоклин задачи. Traceability. |

### Почему не «free-form»

```
❌ "fix"
❌ "fix bug"
❌ "update stuff"
❌ "asdf"
❌ "1"
```

Через 6 месяцев: `git log --oneline --since=6.months` → 200 строк «fix» / «update» / «1». Никто не понимает, что было. `git blame` бесполезен.

### Почему не «strict conventional commits» с 10 types

```
feat, fix, docs, style, refactor, perf, test, build, ci, chore
```

Это **10** категорий. В реальности я использую 5:

| Type | Когда |
|---|---|
| `feat` | Новая функциональность, видимая пользователю |
| `fix` | Исправление бага |
| `refactor` | Реструктуризация без изменения поведения |
| `perf` | Оптимизация производительности |
| `chore` | Всё остальное (CI, deps, config, docs) |

`style`, `test`, `build`, `ci` — в `chore` с scope: `chore(ci): bump PHPStan`. Не нужен отдельный `ci` type. Это over-engineering для команды из 10 человек.

### Squash or no squash?

**Squash merge.** При мерже feature-ветки в `main` — один коммит.

```
feature/payment-webhook (5 коммитов):
  1. add webhook endpoint
  2. add event handler
  3. add tests
  4. fix typo in test
  5. update changelog

→ Squash → 1 коммит в main:
  feat(payment): add webhook handler for Stripe payment_intent.succeeded
```

**Почему squash:**
- `main` — чистая история. 1 коммит = 1 фича.
- Не 5 коммитов с «fix typo in test» в main.
- `git revert` одного коммита = откат фичи. Без «а какой из 5 коммитов откатить?»
- `git bisect` быстрее (меньше точек).

**Почему не rebase-merge:**
- Rebase переписывает хеши. При 10 разработчиках — конфликты с `git pull --rebase`.
- Rebase в shared-ветке — антипаттерн.

**Почему не merge commit (`--no-ff`):**
- «Merge branch 'feature/x' into main» — 300 строк в истории. Неинформативно.
- Squash: 1 строка. Информативно.

---

## 3. Pull Request

### Шаблон

```markdown
## Что

Коротко: что делает эта фича / фикс. 2–3 предложения.

## Почему

Зачем. Контекст. Ссылка на задачу.
«Пользователь не может отменить подписку без причины. Product просит добавить поле.»

## Как проверил

- [ ] Unit-тесты: `OrderTest::test_cancel_requires_reason`
- [ ] Integration: `OrderRepositoryTest::test_save_and_find`
- [ ] E2E: `OrderApiTest::test_cancel_order_with_reason`
- [ ] Ручное: создал подписку, отменил, проверил email

## Скриншоты / логи (если UI)

| До | После |
|---|---|
| | |

## Риски

- Изменён формат JSON в `/api/orders`. Миграция не нужна (добавление поля).
- Влияет на 2 consumer'а: `OnOrderCancelled` и `OnOrderCancelledV2`. Оба обновлены.

## Чеклист

- [ ] PHPStan level 8 зелёный
- [ ] Prettier / PHP-CS-Fixer пройден
- [ ] Миграция написана (если есть)
- [ ] Changelog обновлён
```

### Правила

| Правило | Почему |
|---|---|
| **Max 400 строк изменённого кода** (без тестов, без миграций). | 400 строк — верхняя граница, которую можно ревьюнуть за 30 минут с пониманием. 2000 строк — никто не читает. |
| **Одна задача = один PR.** | Не «refactor Order + add Payment + fix Search». Три PR. |
| **PR создаётся сразу**, не «когда будет готов». | Ранний PR = ранний фидбэк. Не «я делал 3 дня, а ревью 2 часа». |
| **CI зелёный до request review.** | Не просить ревью, когда тесты красные. Уважение к ревьюеру. |
| **Self-review перед request.** | Прочитать diff. Убрать `var_dump`, commented-out код, мусорные тесты. |
| **Ссылка на задачу в описании.** | Traceability. 6 месяцев спустя: «зачем это?» → ссылка → контекст. |

### Почему max 400 строк

Я ревьюю 3–5 PR в день. Каждый по 400 строк → 2000 строк в день → 1–2 часа ревью. Остаток дня — код.

PR на 2000 строк → 1 час **только на чтение**. × 3 PR в день = 3 часа. Ноль кода.

PR на 2000 строк → ревьюер читает 200 строк, «ах, ок, LGTM». Не потому что код плохой. Потому что **мозг не держит 2000 строк в голове одновременно**.

### Почему не «один коммит = один PR»

Иногда фича — это 3 коммита (refactor + feature + test). Это **один PR**, три коммита. Squash при мерже объединит в один.

Иногда фикс — это 1 коммит. Это **один PR**, один коммит.

PR — это **юнит ревью**, не юнит коммита.

---

## 4. Код-ревью

### Что я ищу

| Приоритет | Что | Пример |
|---|---|---|
| **1. Корректность** | Логика верная? Edge cases? | `if ($amount > 0)` — а что если `$amount = 0`? |
| **2. Безопасность** | SQL injection? XSS? IDOR? | `$conn->execute("SELECT * FROM users WHERE id = " . $id)` |
| **3. Производительность** | N+1? О(n²)? Блокировки? | `foreach ($orders as $o) { $o->getLines(); }` |
| **4. Читаемость** | Понятно, **что** делает код? | `$x = $a->b(3, $c, true)` — что такое `$x`? |
| **5. Архитектура** | Слои не нарушены? Границы? | `use App\Infrastructure\...` в `src/Domain/` |
| **6. Тесты** | Покрыто? Осмысленно? | `test_a() { $this->assertTrue(true); }` |
| **7. Стиль** | Naming, consistency | `getUser` vs `getCustomer` в одном классе |

### Что я НЕ ищу

| Что | Почему |
|---|---|
| Отступы | PHP-CS-Fixer / Prettier. Не тратьте время человека. |
| `use` на месте | Автокомплит IDE. Не ревью. |
| `===` vs `==` | PHPStan. Не ревью. |
| «А почему не через Strategy?» | Если работает и < 50 строк — не Strategy. Не ревью. |
| «А почему не через Repository?» | Если один вызов — не Repository. Не ревью. |

### Формат комментов

```
❌ «Это неправильно»
❌ «Так не делают»
❌ «Нужно переделать»

✅ «Здесь `$amount` может быть 0. Что происходит, если пользователь
    отправит quantity=0? По-моему, `OrderLine::addLine()` бросит
    DomainException, но я не нашёл теста. Можно добавить?»

✅ «SQL-инъекция: `$conn->execute("... WHERE id = " . $id)`.
    Замените на параметризованный запрос:
    $conn->executeStatement('... WHERE id = :id', ['id' => $id]);»
```

**Принцип:** коммент — **вопрос или предложение**, не приказ. Я ревьюю код, не человека. «Так не делают» → защитная реакция → спор. «Что происходит, если...?» → обсуждение → решение.

### Сколько времени на ревью

| Размер PR | Время |
|---|---|
| < 100 строк | 5–10 мин |
| 100–400 строк | 20–40 мин |
| > 400 строк | 1–2 часа (или разбить PR) |

**Правило:** если PR требует > 1 часа ревью — он слишком большой. Разбить.

### Approval

- **1 approval** достаточно для команды до 10 человек.
- **2 approval** для изменений в Domain / Security / Infrastructure.
- **CI зелёный** — обязательное условие. Не «ну, CI упал, но я проверил вручную».

**Почему не 3 approval:** 3 человека × 30 мин = 1.5 часа задержки. × 10 PR в день = 15 часов задержки. Не масштабируется. 1 approval + CI + авторы — достаточно.

---

## 5. CI Pipeline

```yaml
# .github/workflows/ci.yml
name: CI

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

jobs:
  # ─── Stage 1: Быстрые проверки (2 мин) ──────────────────────
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3' }
      - run: composer install --no-interaction
      - run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - run: vendor/bin/phpstan analyse --level=8 --no-progress

  # ─── Stage 2: Тесты (5 мин) ─────────────────────────────────
  test:
    runs-on: ubuntu-latest
    needs: lint
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports: [5432:5432]
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3' }
      - run: composer install --no-interaction
      - run: php vendor/bin/phpunit --testsuite Unit
      - run: php vendor/bin/phpunit --testsuite Integration
      - run: php vendor/bin/phpunit --testsuite E2E

  # ─── Stage 3: Сборка (3 мин) ─────────────────────────────────
  build:
    runs-on: ubuntu-latest
    needs: test
    steps:
      - uses: actions/checkout@v4
      - run: docker build -t app:${{ github.sha }} .
      - run: docker push registry.example.com/app:${{ github.sha }}

  # ─── Stage 4: Деплой (только main) ───────────────────────────
  deploy:
    runs-on: ubuntu-latest
    needs: [lint, test, build]
    if: github.ref == 'refs/heads/main'
    environment: production
    steps:
      - run: |
          kubectl set image deployment/app \
            app=registry.example.com/app:${{ github.sha }} \
            -n production
          kubectl rollout status deployment/app -n production
```

### Почему 4 стадии

| Стадия | Время | Зачем отдельно |
|---|---|---|
| Lint | 2 мин | Быстрый фидбэк. «Отступы неверны» — не нужно ждать 5 минут тестов. |
| Test | 5 мин | Корректность. |
| Build | 3 мин | «Дocker образ собирается». |
| Deploy | 1 мин | Только main. Только зелёный CI. |

**Параллельность:** lint и build могут идти параллельно (оба не зависят от БД). Test зависит от lint (зачем тестировать код с ошибками стиля?).

### Почему не «один job на всё»

```yaml
# ❌ Один job:
- name: Run everything
  run: |
    composer install
    vendor/bin/php-cs-fixer fix --dry-run
    vendor/bin/phpstan analyse
    php vendor/bin/phpunit
    docker build
```

Если `php-cs-fixer` упал на 10-й секунде → вы ждали 5 минут, чтобы узнать, что отступы неверны.

4 стадии → 10 секунд до «отступы неверны».

---

## 6. Релизы

### Версионирование

**SemVer:** `MAJOR.MINOR.PATCH`

| Изменение | Версия | Пример |
|---|---|---|
| Новая фича (назад-совместимая) | MINOR | `1.42.0` → `1.43.0` |
| Фикс бага (без изменения API) | PATCH | `1.43.0` → `1.43.1` |
| Breaking change (API, схема БД, поведение) | MAJOR | `1.43.1` → `2.0.0` |

### Процесс релиза

```
1. Все фичи merged в main. CI зелёный.

2. Создать release-ветку:
   git checkout -b release/1.44.0 main

3. В release-ветке:
   - Финальные баг-фиксы (только фиксы, не фичи).
   - Changelog:
     ## [1.44.0] - 2025-01-15
     ### Added
     - Payment webhook handler (Stripe)
     - Order cancellation with reason
     ### Fixed
     - N+1 query in order list
     ### Changed
     - Money: use embeddable instead of custom type

   - Bump версии:
     "version": "1.44.0"

4. PR: release/1.44.0 → main.
   - CI: lint + test + build.
   - 1 approval.

5. Tag:
   git tag -a v1.44.0 -m "Release 1.44.0"
   git push origin v1.44.0

6. Деплой:
   - Kubernetes: image tag = v1.44.0.
   - Миграции: `php bin/console doctrine:migrations:migrate` (перед деплоем).
   - Smoke test: `curl https://api.example.com/health`.

7. Удалить release-ветку.
   git branch -d release/1.44.0
```

### Почему tag, а не commit SHA

| | Tag | SHA |
|---|---|---|
| Человекочитаемость | `v1.44.0` | `a3f8c2d` |
| Семантика | «Версия 1.44.0» | «Коммит a3f8c2d» |
| Hotfix | `git checkout v1.44.0` | `git checkout a3f8c2d` |
| Rollback | `kubectl set image ... v1.44.0` | `kubectl set image ... a3f8c2d` |
| Чangelog | Привязан к тегу | Привязан к коммиту (менее очевидно) |

### Hotfix

```
1. git checkout -b hotfix/1.44.1 v1.44.0
   # ← от тега, НЕ от main

2. Фикс. Тесты.

3. PR: hotfix/1.44.1 → release/1.44.1 (от v1.44.0)
   # ← НЕ в main. Main может быть 1.45.0-dev.

4. Tag: v1.44.1
5. Деплой.

6. Merge back: hotfix/1.44.1 → main
   # ← чтобы фикс не потерялся в следующем релизе.

7. Удалить hotfix-ветку.
```

**Почему от тега, а не от main:**

```
main:     ... → f1 → f2 → f3 → f4 (в разработке 1.45.0)
prod:     ... → f1 → f2 → f3   (v1.44.0)

Bug в f2. Нужно чинить в 1.44.0.

Если чинить от main:
  main: f1 → f2 → f3 → f4 → hotfix
  ← f4 может ломать hotfix. f4 — новая фича, не протестированная с hotfix.

Если чинить от v1.44.0:
  v1.44.0: f1 → f2 → f3 → hotfix
  ← Стабильная база. Только фикс.
```

### Migrations

```
Правило: миграция — часть PR, который её вводит.

PR: feat(payment): add webhook handler
  + src/Domain/Payment/Payment.php
  + src/Infrastructure/Doctrine/Entity/PaymentEntity.php
  + migrations/Version20250115000000.php   ← в том же PR
  + tests/...

Почему:
  • Миграция без кода = «в проде колонка, а кода нет».
  • Код без миграции = «кода нет, а колонка нужна».
  • Вместе: атомарно. Rollback: атомарно.
```

---

## 7. Что я НЕ делаю

### ❌ `git push --force` на main / shared branches

```
--force на main:
  → переписывает историю.
  → 9 коллег: `git pull` → конфликт.
  → `git log` у 9 человек разный.
  → CI кэши у 9 человек мёртвые.
```

**Когда `--force` допустим:**
- Своя feature-ветка. Только я.
- `git push --force-with-lease` (не `--force`). Защита от чужих коммитов.

### ❌ «Автоматический merge без CI»

```yaml
# ❌
github:
  branch_protection:
    required_pull_request_reviews: 0
    required_status_checks: []
```

Это «пушай в main, и пусть будет как будет». Не процесс.

### ❌ «Один человек = approver + author»

```
# ❌
branch_protection:
  require_code_owner_reviews: false
```

Автор не ревьюит свой код. Минимум 1 approval от **другого** человека.

### ❌ «Rogue commit» в main

```
# ❌
git checkout main
git pull
# ... правка конфига ...
git add config/services.yaml
git commit -m "fix config"
git push
```

Без PR. Без CI. Без ревью. Если конфиг сломал прод — кто виноват?

### ❌ Долгоживущие ветки

```
# ❌
feature/refactor-everything   (живёт 3 месяца)
```

3 месяца = 50 коммитов = 2000 строк = «я не помню, что я делал» = merge conflict на 500 строк.

**Правило:** если ветка живёт > 4 дня — она слишком большая. Разбить.

---

## 8. Итого: полный путь

```
Задача в Jira/Linear
  │
  ▼
git checkout -b feature/payment-webhook main
  │
  ▼
Разработка (1–2 дня)
  │  Коммиты: feat(payment): add ...
  │  Тесты: unit + integration + e2e
  │  PHPStan: зелёный
  │  CS-Fixer: зелёный
  │
  ▼
git push origin feature/payment-webhook
  │
  ▼
Создать PR (шаблон: Что / Почему / Как проверил / Риски)
  │
  ▼
CI: lint → test → build (5–7 мин)
  │
  ▼
Ревью: 1 approval (< 40 мин)
  │
  ▼
Squash merge → main
  │
  ▼
main: CI → build → deploy (staging)
  │
  ▼
Smoke test (staging)
  │
  ▼
[Сборка фич для релиза]
  │
  ▼
release/1.44.0 → PR → main → tag v1.44.0
  │
  ▼
Migrations → Deploy (production)
  │
  ▼
Health check + мониторинг (5 мин)
  │
  ▼
Готово. Задача закрыта.
```

**Время от задачи до продакшена:** 1–3 дня (разработка + ревью + CI). Не 2 недели. Не 1 месяц.

**Время от бага до фикса (hotfix):** 1–4 часа.

---

## Почему именно так

Я выбрал **кратчайший путь от «код написан» до «код в проде»**, при котором **никто не может сломать прод незаметно**.

- **Main зелёный** → любой момент — deploy-готов. Не «подождите, пока merge'им develop».
- **CI обязателен** → «я проверил вручную» не принимается.
- **Squash** → история чистая. Revert простой.
- **Маленькие PR** → ревью быстрое. Конфликты редкие.
- **Hotfix от тега** → стабильная база. Не «main + 5 новых фич».
- **Миграция в PR** → атомарность.
- **1 approval** → достаточно. Не 3. Не 5.

Это не «лучший процесс». Это **рабочий** процесс для команды из 3–15 человек, которая делает релизы каждую неделю. Для стартапа из 2 человек — переусложнение. Для корпорации из 200 — недостаточно.

Для нас — достаточно.

> ⏱ 75.2s · prompt 117220 tok · output 7294 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

The user is asking about my approach to code review: how I mark/comment on issues, what I look at first, and how I handle disagreements with the author. This is a practical, opinion-based question about engineering culture and process.

Let me think about what makes a genuinely good code review from my "experience" (training data of thousands of PRs, discussions, books, blog posts):

1. Marking severity levels - how to signal urgency without being condescending
2. Priority order of what to check
3. Conflict resolution - the hardest part, where ego enters

I should be concrete, give examples of actual comments (good and bad), and be honest about trade-offs. The user has been asking increasingly practical questions through this conversation, building up a complete picture of a senior PHP developer's workflow. This is the final piece - the human/communication side.

Let me structure this clearly:
- How I mark comments (severity, tone, format)
- Priority order (what I look for first, what I skip)
- Disagreement handling (the most important part)

I'll keep it practical, with concrete examples of good and bad comments. The user appreciates when I'm honest about trade-offs and when I say "this is over-engineering for X team size."

</details>

**Кандидат:**

# Код-ревью: как я ревьюю

## Маркировка замечаний

Я не использую `[CRITICAL]`, `[BLOCKER]`, `[NIT]` — это слишком формально и создаёт «ступени важности», которые автор сразу ранжирует в голове: «а, это же NIT, можно проигнорировать».

Вместо этого — **три категории по тону и содержанию комментария**:

### 1. «Должно» (blocking)

Формулировка: **конкретный факт + вопрос или предложение**.

```
🔴 SQL-инъекция. Строка 47:

  $conn->execute("SELECT * FROM users WHERE id = " . $request->query->get('id'));

Здесь `$id` попадает прямо в SQL. Замените на параметризованный:

  $conn->executeStatement(
      'SELECT * FROM users WHERE id = :id',
      ['id' => (int) $request->query->get('id')]
  );

Не могу approuvate, пока это не исправлено.
```

```
🔴 Гонка: строки 82–95.

  $balance = $this->repo->getBalance($userId);
  if ($balance < $amount) { throw ... }
  $this->repo->debit($userId, $amount);

Между SELECT и UPDATE другой процесс может списать баланс.
Два пользователя с балансом 100 одновременно снимают по 80.
Оба пройдут проверку. Итог: баланс = 20. Потеряно 60.

Предлагаю атомарный UPDATE:

  UPDATE accounts SET balance = balance - :amount
  WHERE user_id = :uid AND balance >= :amount

0 rows → InsufficientFundsException.
```

**Признаки «должно»:**
- Безопасность (injection, IDOR, XSS).
- Потеря данных / гонка.
- Нарушение доменного инварианта.
- Сломанный контракт (возвращает `null`, а сигнатура говорит `Money`).
- Невоспроизводимый баг (зависит от времени, от порядка, от конкурентности).

**Как формулирую:**
- Указываю **строку** или **метод**.
- Показываю **сценарий**, при котором сломается. Не «это неправильно», а «если X, то Y, и результат — Z».
- Предлагаю **конкретное исправление** или **вопрос**, на который автор должен ответить.
- Говорю прямо: «не могу approuvate, пока...».

**Чего НЕ делаю:**
- Не говорю «ты неправильно сделал». Говорю «здесь будет баг, если...».
- Не даю 5 вариантов решения. Один конкретный + «или предложите свой».
- Не пишу «это security issue» без объяснения. Автор должен понимать **почему**.

---

### 2. «Предлагаю» (non-blocking)

Формулировка: **наблюдение + аргумент + вопрос**.

```
🟡 Строки 112–130: три блока `if ($method === 'card') / elseif ... / elseif ...`.

Сейчас это ок — 3 варианта, структура не повторяется.
Но я вижу, что в `refund()` и `getStatus()` тот же скелет.

Если добавится 4-й метод — будем править 5 методов.
Предлагаю вынести в `PaymentMethodStrategy` + resolver.
Не blocker. Если вы видите, что 4-го метода не будет полгода — оставьте как есть.
```

```
🟡 Строка 55:

  private array $strategies;

Без `@var` annotation. PHPStan на уровне 8 ругнётся.
Либо добавьте `/** @var array<string, PaymentMethodStrategy> */`,
либо используйте `array<string, PaymentMethodStrategy>` (PHP 8.1+).
```

**Признаки «предлагаю»:**
- Читаемость (имя переменной, разбивка на методы).
- Архитектура (Strategy vs if/elseif, но не «а почему не hexagonal?»).
- Повторяемость (3 раза одинаковый блок).
- Производительность (не критичная: `str_contains` vs `strpos`).
- Консистентность (в одном классе `getUser()`, в другом `getCustomer()`).

**Как формулирую:**
- Объясняю **почему** это может стать проблемой. Не «так лучше», а «если X — то Y».
- Говорю: «не blocker». Автор **может** проигнорировать.
- Не даю ультиматум. Не «исправьте, иначе merge не дам».
- Один комментарий. Не три на одну тему.

**Чего НЕ делаю:**
- Не спорю о стиле. Если PHP-CS-Fixer уже настроен — это не моя зона.
- Не предлагаю «а давайте переделаем на Laravel». Это не ревью.
- Не пишу «лучше бы через...» без аргумента. «Лучше» — не аргумент.

---

### 3. «Заметил» (FYI / вопрос)

Формулировка: **наблюдение без оценки**.

```
🔵 Строка 78:

  $this->logger->info('payment_initiated', ['amount' => $amount]);

Небольшая деталь: `$amount` здесь — `Money` объект.
`$logger->info()` сделает `json_encode($amount)`, а это:
`{"amountCents":100,"currency":"RUB"}`.

Если логгер не сериализует VO — будет `{"__unserialize_failed__":true}`.
Проверьте, что в продакшене логи читабельные.
Не blocker, просто заметил.
```

```
🔵 Вопрос: строка 34.

  public function __construct(private readonly int $maxRetry)

`maxRetry` — это максимум повторений (3) или максимум задержки (3 сек)?
Из имени непонятно. Может, `$maxAttempts`?
```

**Признаки «заметил»:**
- Не уверен, что это проблема. Но автору может быть полезно знать.
- Вопрос по именованию / намерению.
- «Я бы сделал иначе, но не настаиваю».

**Как формулирую:**
- Вопрос, а не утверждение.
- «Проверьте, что...», а не «Тут баг».
- Автор может ответить «нет, так и задумано» — и это нормально.

---

## На что смотрю в первую очередь

Порядок реального чтения PR. Не «сверху вниз по файлам». По **риску**:

### Проход 1: «Сломает ли это прод?» (5 минут)

| Что | Почему первым |
|---|---|
| Миграция БД | Неверная миграция = потеря данных. `ALTER TABLE DROP COLUMN` без `CREATE` в том же файле. |
| Изменение публичного API | `POST /orders` раньше принимал `amount: int`, теперь `amount: Money`. Кто-то в проде сломается. |
| Удаление кода | `git diff` с минусами. Что удалили? Кто-то ещё это использует? |
| Изменение конфигурации | `security.yaml`, `messenger.yaml`, `services.yaml`. Один лишний `public: true` — утечка. |
| Новый внешний вызов | `curl`, `HttpClient`, `Stripe->charge()`. Нет timeout? Нет retry? Нет fallback? |
| `rm`, `exec`, `file_put_contents` | shell-вызовы, запись файлов. Безопасность. |

Если в проходе 1 нашёл что-то блокирующее — **останавливаюсь**. Остальное нечитаю. Коммент + «не могу approuvate, пока это не исправлено».

### Проход 2: «Логика верная?» (10–15 минут)

| Что | Пример |
|---|---|
| Доменные инварианты | `Order::confirm()` бросает, если нет строк. Покрыто тестом? А если `lines` пустой массив, а не `null`? |
| Edge cases | `$amount = 0`. `$qty = 0`. Пустой массив. `null` в nullable-параметре. |
| Порядок операций | `flush()` до `dispatch()`? `clear()` после `flush()`? |
| Транзакционность | `persist` без `flush`? `flush` внутри listener? |
| Идempotентность | Повторный вызов `confirm()` → `409` или «ок, уже подтверждён»? |

### Проход 3: «Поймёт ли следующий разработчик?» (5–10 минут)

| Что | Пример |
|---|---|
| Имена | `$x`, `$data`, `$result`, `$tmp`. Что это? |
| Методы > 30 строк | Скорее всего, делится. |
| Вложенность > 3 уровня | `if → if → if → if`. Переписать через early return. |
| Магические числа | `if ($status === 3)`. Что такое 3? |
| Комментирование «что», а не «почему» | `// проверяем баланс` над `$this->checkBalance()`. Зачем? |

### Проход 4: «Покрыто ли?» (5 минут)

| Что | Пример |
|---|---|
| Happy path | `test_confirm_order_succeeds`. |
| Error path | `test_confirm_order_throws_when_no_lines`. |
| Edge case | `test_confirm_order_throws_when_status_already_confirmed`. |
| Непрозрачная логика | Если метод > 10 строк и нет теста — вопрос. |

### Что я пропускаю

| Что | Почему |
|---|---|
| Отступы, запятые, `use`-порядок | PHP-CS-Fixer. Не время человека. |
| `===` vs `==` | PHPStan. |
| Типы (если PHPStan уровень 8+ зелёный) | Статический анализатор. |
| «А почему не через Design Pattern X?» | Если работает и < 50 строк — не нужен. |
| «А почему не через CQRS?» | Если 1 контроллер и 1 сервис — не CQRS. |
| Имена в тесте (`test_a`, `test_1`) | Не блокирует. Но отмечу: «`test_confirm_throws_when_no_lines` читается лучше». |

---

## Разногласие с автором

Это самая сложная часть. Не «как написать комментарий». А «автор не согласен, и он, возможно, прав».

### Правило 1: Разногласие — это нормально

```
Я:   «Здесь нужен Strategy pattern. 4 варианта, структура повторяется.»
Автор: «У нас 3 варианта. Четвёртого не будет полгода. Если будет — вынесу.»

Я:   Ок. Я ошибся в оценке вероятности. Автор знает продукт лучше меня.
      Не настаиваю. Убираю комментарий. «Принято, оставляем как есть.»
```

**Критерий:** если автор даёт **аргумент** («не будет 4-го полгода», «3 варианта не дублируют структуру») — его аргумент сильнее моего «а в книге написано».

### Правило 2: Разделяю «что» и «как»

```
Я:   «Нужен pessimistic lock. FOR UPDATE.»
Автор: «У нас 500 RPS на эту строку. FOR UPDATE → очередь. У меня optimistic + retry.»

Я:   Становлюсь на его место.
      500 RPS → FOR UPDATE → 499 процессов ждут → latency 5 сек.
      Оптимистичный: 10% конфликтов → 10% retry → latency +50 мс.
      Он прав. FOR UPDATE в его контексте — антипаттерн.

      «Вы правы, при 500 RPS pessimistic не масштабируется.
       Оптимистичный с retry — разумнее. Убираю комментарий.»
```

### Правило 3: Не перевожу в личное

| ❌ Личное | ✅ Про код |
|---|---|
| «Ты опять не проверил null?» | «Строка 42: `$user->getEmail()` — а если `$user` null?» |
| «Я бы так не делал» | «Здесь есть альтернатива: X. Минус: Y. Плюс: Z. Что выбираем?» |
| «Это же очевидно» | «Я не уверен, что это очевидно. Давайте разберём: ...» |
| «В предыдущем PR ты тоже так делал» | «В этом PR: ...» (про текущий код, не историю) |

### Правило 4: Говорю «я не знаю»

```
Автор: «Здесь нужно `DEFERRABLE INITIALLY DEFERRED`, потому что...»
Я:     «Я не уверен в этом. Мой опыт — `DEFERRABLE` работает, когда
       в транзакции есть "временно невалидное" состояние. Но в вашем
       случае, возможно, есть нюанс, который я не вижу.
       Давайте посидим 10 минут и разберём?»
```

Не «вы неправы». Не «я так не думаю». А «я не знаю, давайте разберём».

### Правило 5: Если не могу уговорить — фиксирую и двигаюсь

```
Ситуация:
  Я:   «Нужен outbox. Без него события теряются.»
  Автор: «У нас 50 событий в день. Потеря одного — не критично.
          Мы алертим. Ручная пересылка. Outbox — оверхед.»

  Я:   ...
       Честно: он прав. 50 событий в день. Outbox — это DB-таблица
       + relay-процесс + мониторинг. Для 50 событий — избыточно.
       Простое `dispatch()` + лог + алерт на сбой — достаточно.

  Я:   «Принято. 50 событий в день — outbox избыточен.
        Предлагаю: dispatch() + structured log + алерт при исключении
        в listener. Если вырастем до 1000/день — добавим outbox.
        Убираю комментарий.»
```

**Не торгуюсь ради торга.** Если аргумент автора сильнее — отступаю. Не «ок, но...». Не «я всё ещё считаю...». Просто «принято, убираю».

### Правило 6: Эскалация — крайний случай

```
Когда эскалирую:
  • Безопасность: SQL injection, IDOR, XSS. Не «предлагаю», а «должно».
    Если автор не согласен → security review. Не спор.
  • Потеря данных: race condition, lost update. Аналогично.
  • Третий раунд спора: «я считаю X» / «я считаю Y» / «я считаю X».
    → Зову третью сторону (tech lead, senior). Не «а давайте голосованием».

Когда НЕ эскалирую:
  • Стиль. Naming. Архитектурный вкус.
  • «Я бы сделал иначе». Если код работает и читается — ок.
  • Мнение о фреймворке / библиотеке. Не зона ревью.
```

### Правило 7: После ревью — закрываю

```
Автор исправил. Я проверил. Пишу:

  «Выглядело. LGTM. Merging.»

Не:
  «Выглядело, но я бы ещё...»  ← это новый раунд.
  «Ok, но на будущее имейте в виду...»  ← лекция. Не ревью.
  «LGTM, но 3/10.»  ← шкала. Не ревью.

  Если есть «на будущее» — это не ревью. Это менторинг.
  Отдельный канал. Не в PR.
```

---

## Чего я не делаю

### ❌ Ревьюю по списку

```
❌ Чеклист:
  [ ] Отступы
  [ ] Имена
  [ ] Тесты
  [ ] Документация
  [ ] Performance
  [ ] Security
  [ ] Architecture
  [ ] ...

→ 15 пунктов × 10 PR = 150 проверок.
→ Каждый PR — 30 минут по чеклисту.
→ Автор видит: «а, опять по чеклисту».
```

Я читаю **код**. Не «чеклист». Чеклист — для первого PR в проекте. Для двадцатого — я знаю, на что смотреть.

### ❌ Ревьюю «по файлу»

```
❌ File 1: Controller.
   «Ok, выглядит нормально.»

File 2: Service.
   «Ok, выглядит нормально.»

File 3: Repository.
   «Ok, выглядит нормально.»

→ Не увидел, что Controller вызывает Service методом,
  которого в Service нет (имя переименовали, но в Controller забыли).
```

Я читаю **по данным**. «Что приходит в метод? Что уходит? Что в БД? Что в событии?» Файлы — не границы.

### ❌ Ревьюю «когда есть время»

```
❌ PR создан в понедельник.
   Я ревьюю в пятницу.
   Автор: «а что, не увидели?»
   Я: «не было времени».

→ 4 дня. Контекст у обоих улетел.
→ Автор: «а, я уже переписал 30%».
```

**Правило:** ревью в тот же день. Если не успеваю — говорю: «увидел, займусь завтра до обеда». Не молчу.

### ❌ «LGTM» без прочтения

```
❌ 2000 строк. 2 минуты. «LGTM.»
   → Нашёл баг на проде через неделю.
   → Git blame: «LGTM» от меня.
```

Если не успел прочитать 2000 строк — не «LGTM». А: «Прочитал 40%. Остальное — завтра. Не блокируйте merge, но я вернусь с вопросами».

### ❌ Ревьюю на митинге

```
❌ «А давайте покажу PR» → 15 минут в Zoom → 3 комментария.
   → Автор: «а, да, я так и думал».
   → Через 2 часа: «а, нет, это не то».
```

Ревью — асинхронно. В PR. С кодом перед глазами. Не «скажите, тут ок?» в голосовом.

---

## Итого

| | Как |
|---|---|
| **Блокирующее** | Конкретный сценарий + строка + предложение. «Не могу approuvate, пока...». |
| **Предложение** | Наблюдение + аргумент + «не blocker». Автор может проигнорировать. |
| **Заметка** | Вопрос. «Проверьте, что...». Не утверждение. |
| **Приоритет** | Сломает прод? → Логика? → Читаемость? → Тесты? |
| **Разногласие** | Аргумент автора сильнее моего мнения → отступаю. Без торга. |
| **Эскалация** | Безопасность / потеря данных / третий раунд. Не стиль. |
| **Скорость** | В тот же день. Если не успеваю — говорю. Не молчу. |
| **Закрытие** | «LGTM. Merging.» Без «но». Без «3/10». Без лекции. |

Хорошее ревью — это **не найти как можно больше замечаний**. Это **не пропустить то, что сломает прод**, и **не тратить 3 часа на отступы**. Всё остальное — баланс.

> ⏱ 57.7s · prompt 124098 tok · output 4963 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня преобразовать сырой бизнес-проблематичный запрос в конкретную задачу для исполнителя. Это задача на формулирование requirements — перевод "пользователи жалуются, что не могут найти свои заказы" в исполнимый ticket.

Мне нужно:
1. Показать, что я понимаю разрыв между бизнес-жалобой и инженерной задачей
2. Сформулировать задачу так, чтобы разработчик мог сесть и делать, не возвращаясь с вопросами
3. Объяснить, ПОЧЕМУ каждый элемент включён — это важно, пользователь явно спрашивает "что обязательно включишь и почему"

Ключевые вещи, которые нужно сделать:
- Разобрать "не могут найти" — это что? Нет поиска? Есть поиск, но не работает? Загрузил, а данных нет? Интерфейс неудобный?
- Определить scope: что входит, что нет
- Дать критерии приёмки (acceptance criteria) — без них "сделай поиск" = "сделай всё"
- Дать контекст: какой стек, какие ограничения
- Указать, что НЕ нужно делать (anti-scope)
- Дать данные для воспроизведения / примеры
- Указать приоритет и дедлайн
- Указать, к кому идти с вопросами (product owner, не разработчик)
- Разделить "исследование" и "реализацию" — потому что "не могут найти" может быть 5 разных проблем

Структура ответа:
1. Коротко: почему сырая формулировка не исполнима (2-3 предложения)
2. Сама формулировка задачи (как ticket)
3. Разбор: что и почему включено
4. Что я бы НЕ включил и почему

Формулирую на русском, как весь диалог.

</details>

**Кандидат:**

# От жалобы к задаче

## Почему сырая формулировка не исполнима

«Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена» — это **диагноз без обследования**. «Не могут найти» может значить пять разных вещей:

- Нет страницы «Мои заказы» вообще.
- Страница есть, но показывает только 3 последних, а нужен поиск по дате/номеру.
- Поиск есть, но не работает (баг).
- Заказы есть, но пользователь не понимает, где они (UX).
- Заказы удалены / потеряны (баг данных).

Разработчик, получивший эту фразу, потратит 2 дня на уточнения. Либо сделает «поиск по дате», а нужно было «показать все заказы на одной странице с пагинацией». Либо пойдёт писать full-text search, а проблема была в том, что API возвращает `500` на `page=2`.

---

## Формулировка задачи

```markdown
## Задача: Поиск исторических заказов в личном кабинете

### Контекст

Поддержка получает 40–60 обращений в неделю с формулировкой:
«Не могу найти свой заказ», «Где мой заказ от [дата]»,
«Хочу посмотреть историю покупок за прошлый год».

Текущее состояние:
- Страница `/orders` существует, показывает последние 5 заказов.
- Нет пагинации (кнопки «назад/вперёд» нет).
- Нет поиска по номеру заказа.
- Нет фильтра по дате.
- Заказы старше 30 дней не отображаются
  (причина: API-эндпоинт `GET /api/orders` содержит
  `->where('created_at >= NOW() - INTERVAL 30 days')` —
  намеренный фильтр, поставлен в марте как «оптимизация»).
- Заказы НЕ удалены из БД. Проверено:
  `SELECT COUNT(*) FROM orders WHERE user_id = <тестовый> AND created_at < NOW() - INTERVAL 30 days`
  → 12 строк. Данные на месте.

### Что нужно

1. **Снять 30-дневный фильтр** из `GET /api/orders`.
   Заказы возвращаются за весь период жизни пользователя.

2. **Добавить пагинацию** (cursor-based, не offset):
   - `GET /api/orders?limit=20` → 20 заказов + `next_cursor`.
   - `GET /api/orders?limit=20&cursor=<cursor>` → следующая партия.
   - Курсор = `created_at,id` (стабильный при вставке новых заказов).
   - Максимум `limit` = 50. По умолчанию = 20.

3. **Добавить фильтр по номеру заказа**:
   - `GET /api/orders?number=ORD-2024-12345`
   - Точное совпадение. Регистронезависимо.
   - Если не найден → `404 { "error": "order_not_found" }`.
   - Номер заказа: поле `orders.order_number`, формат `ORD-YYYY-NNNNN`.

4. **Добавить фильтр по периоду**:
   - `GET /api/orders?from=2024-01-01&to=2024-12-31`
   - Инклюзивно: `created_at >= from AND created_at <= to 23:59:59`.
   - Без `from` — без нижнего ограничения. Без `to` — без верхнего.
   - Валидация: `from <= to`, иначе `422 { "error": "invalid_date_range" }`.

### Что НЕ нужно (anti-scope)

- Полнотекстовый поиск по содержимому заказа. Не нужно.
- Фильтр по статусу, по продавцу, по сумме. Не в этом релизе.
- Изменение формата ответа (JSON-структура). Текущий формат:
  `{ "orders": [...], "next_cursor": "..." }`. Не менять.
- Миграцию БД. Данные уже есть. Только изменение запроса.
- UI-разработку. Фронтенд делает отдельная команда.
  Мы даём API. Контракт описан выше.

### Ограничения

- Не вводить новую зависимость (Elasticsearch, Meilisearch).
  PostgreSQL `GIN` / `B-tree` по `order_number` — достаточно.
  Индекс: `CREATE INDEX CONCURRENTLY idx_orders_user_number
           ON orders (user_id, order_number);`
  Если индекса нет — добавить миграцией.
- Latency: p95 < 200 мс на 1000 заказов у пользователя.
  Проверяется в integration-тесте с 5000 строк.
- Не использовать `SELECT *`. Только поля, которые есть
  в текущем ответе: `id, order_number, status, total_cents,
  currency, created_at, item_count`.
- Cursor-based pagination. Не `LIMIT/OFFSET`.
  Причина: при 10 000+ заказов `OFFSET 5000` — O(n) scan.
  Cursor — O(log n).

### Критерии приёмки

- [ ] `GET /api/orders` возвращает ВСЕ заказы пользователя
      (проверено: пользователь с 12 заказами старше 30 дней
      видит все 12).
- [ ] `GET /api/orders?limit=5` → 5 заказов + `next_cursor ≠ null`.
- [ ] `GET /api/orders?limit=5&cursor=<cursor>` → следующие 5.
      Нет дубликатов. Нет пропусков.
- [ ] `GET /api/orders?number=ORD-2024-12345` → 1 заказ.
- [ ] `GET /api/orders?number=ORD-2024-99999` → 404.
- [ ] `GET /api/orders?from=2024-01-01&to=2024-06-30`
      → только заказы в этом диапазоне.
- [ ] `GET /api/orders?from=2024-06-01&to=2024-01-01` → 422.
- [ ] Индекс `idx_orders_user_number` существует.
      `EXPLAIN ANALYZE` показывает `Index Scan`, не `Seq Scan`.
- [ ] Integration-тест: 5000 заказов, p95 < 200 мс.
- [ ] E2E-тест: полный сценарий «создал 5 заказов → нашёл по
      номеру → отфильтровал по дате → пролистал пагинацией».
- [ ] PHPStan level 8 зелёный.
- [ ] Документация API обновлена (OpenAPI / Postman collection).

### Как воспроизвести текущий баг

1. Зайти в тестовую БД.
2. Создать пользователя `find_test@test.ru`.
3. Вставить 10 заказов: 3 за прошлую неделю, 7 — за 2–4 месяца.
   (Скрипт: `tests/fixtures/seed_orders.php`)
4. `GET /api/orders` с токеном этого пользователя.
5. Текущий результат: 3 заказа. Ожидаемый: 10.

### Стек и файлы (ориентир)

- Репозиторий: `src/Infrastructure/Doctrine/Repository/OrderRepository.php`
- Хендлер: `src/Application/Order/ListOrdersHandler.php`
- Контроллер: `src/Interface/Api/OrderController

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

# Вопросы заказчику до начала работы

Я группирую их не по «темам», а по тому, **какое решение каждый вопрос влияет на**. Если я получу ответ — я буду знать, что делать. Если не получу — буду принимать решение сам, и это решение может быть неправильным.

---

##

> ⏱ 26.1s · prompt 128874 tok · output 2194 tok · ⚠️ обрыв: length

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о двух конкретных рабочих ситуациях:
1. Нашёл попутный баг, не связанный с задачей
2. Критерий приёмки противоречит цели задачи

Это вопрос про рабочую этику, коммуникацию, принятие решений. Нужно дать конкретные, практичные ответы. Не абстрактные "сообщай команде", а именно что делаешь, как формулируешь, почему именно так и не иначе.

Давайте разберу каждую ситуацию честно, с конкретикой.

Ситуация а) — попутный баг:
- Ключевой вопрос: чинить или нет?
- Если чинить — это scope creep, но и оставить — совесть нечистая
- Если баг критический (потеря данных, безопасность) — нельзя молчать
- Если баг мелкий (опечатка в логе) — можно зафиксировать и двигаться
- Как зафиксировать? Тикет? Комментарий в PR? Устное сообщение?
- Почему не молчать? Потому что

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.4s · prompt 130838 tok · output 234 tok · ⚠️ обрыв: length

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про отчёт о выполнении задачи. Это вопрос про практическую работу разработчика — как отчитаться перед техлидом/заказчиком после выполнения задачи. И вторая часть: что меняется, если часть задачи НЕ выполнена.

Я уже в контексте диалога: я описывал git-процесс, код-ревью, задачи. Теперь — отчёт. Это естественное продолжение.

Что должно быть в отчёте:
1. Что сделано (связь с критериями приёмки — по одному пункту)
2. Что НЕ сделано (если есть) и почему
3. Что изменилось кроме кода (миграции, конфиги, зависимости)
4. Риски / что нужно проверить в проде
5. Что я заметил попутно (но не делал

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.8s · prompt 130889 tok · output 179 tok · ⚠️ обрыв: length

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про FORCE INDEX в PostgreSQL. Это классический вопрос-ловушка (или вопрос от человека, мыслящего в терминах MySQL). FORCE INDEX — это синтаксис MySQL, не PostgreSQL. В PostgreSQL такого механизма нет.

Я должен:
1. Честно сказать, что FORCE INDEX в PostgreSQL не существует.
2. Объяснить, что это MySQL-механизм.
3. Рассказать, какие аналоги существуют в PostgreSQL для влияния на выбор плана.
4. Не выдумывать. Не приписывать PostgreSQL то, чего в нём нет.

Это важный момент честности. Если я начну описывать "FORCE INDEX в PostgreSQL", я

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.3s · prompt 130923 tok · output 148 tok · ⚠️ обрыв: length

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, на сколько процентов strict_types=1 замедляет выполнение PHP-кода. Это вопрос, предположительно, проверяющий, буду ли я придумывать конкретную цифру или честно скажу, что такого измерения не существует.

Давайте подумаем честно:

1. strict_types=1 — это декларация в начале файла, которая меняет поведение приведения типов при вызовах функций из этого файла. В coercive mode PHP приводит типы (string →

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.9s · prompt 130968 tok · output 103 tok · ⚠️ обрыв: length

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, почему Doctrine ORM не поддерживает составные первичные ключи (composite primary keys). Это вопрос с ловушкой, потому что на самом деле Doctrine **поддерживает** составные первичные ключи через `CompositeIds`. Вопрос сформу

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.4s · prompt 131013 tok · output 58 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать кусок PHP-кода — контроллер с обработчиком оплаты заказа. Нужно найти проблемы и предложить переделку. Это классический "плохой код" с многими нарушениями. Давайте пройдёмся по коду системно.

Проблемы, которые я вижу:

1. **SQL Injection** — `$id` подставляется напрямую в SQL. Критическая уязвимость.

2. **Нет валидации `$id`** — что если приходит "abc"? Или "1; DROP TABLE orders"?

3. **Нет аутентификации/авторизации** — кто угодно может оплатить любой заказ. Нет проверки, что пользователь авторизован и что это ЕГО заказ.

4. **Бизнес-логика в контроллере** — валидация статуса, обновление, отправка письма, логирование — всё в одном методе. Нарушение SRP, невозможность переиспользования и тестирования.

5. **Нет доменной валидации** — что если заказ уже оплачен? Что если отменён? `status = 'paid'` ставится без проверки текущего статуса.

6. **Mailer::send() — статический вызов** — неинъектируемый, немутабельный, немокабельный. Тестирование невозможно.

7. **Logger — new каждый раз** — создаётся новый логгер внутри метода. Плохая практика.

8. **Нет обработки ошибок** — что если Mailer упадёт? Что если UPDATE затронул 0 строк (race condition)? Exception не обработан.

9. **Race condition** — между SELECT и UPDATE другой запрос может изменить status. Нет optimistic locking, нет FOR UPDATE, нет атомарного UPDATE.

10. **Нет транзакции** — UPDATE + (гипотетически) другие операции не атомарны.

11. **`SELECT *`** — загружает все поля, включая те, что не нужны.

12. **Нет идемпотентности** — повторный вызов pay() повторно отправит email.

13. **Нет возврата ошибок от gateway** — код просто ставит status='paid' без реальной проверки оплаты. Где вызов платёжного шлюза?

14. **`$order['email']`** — что если email NULL? Mailer упадёт.

15. **Нет типизации** — нет return type, нет параметр типов, нет strict_types.

16. **Статический `DB::connect()`** — неинъектируемый.

17. **Логирование после email** — если email упадёт, лог не запишется. Порядок.

18. **Нет CORS / rate limiting** — массовая оплата.

Теперь как переделать. По чистому стилю:

- Контроллер → thin. Только parse request, вызвать handler, return response.
- Handler (Application Service) → оркестрация: загрузить, валидировать, обновить, сохранить, диспатчить события.
- Domain (Order) → инварианты: можно ли оплатить, проверка статуса.
- Repository → загрузка/сохранение.
- Mailer → через событие, не напрямую в контроллере.

Начну с чего? Начну с **безопасности** (SQL injection) — это P0. Потом **авторизация** — P0. Потом **выношу бизнес-логику** из контроллера. Потом **race condition**. Потом **тестируемость**.

Структурирую ответ так:
1. Перечень проблем по приоритету (критичные → важные → второстепенные).
2. Что и почему делаю первым.
3. Переделанный код — слой за слоем.
4. Что осталось и почему.

Не буду делать "стеновый" текст. Буду конкретный, с кодом.

</details>

**Кандидат:**

## Что не так: по приоритету

### P0 — сломает или украдёт

**1

> ⏱ 82.8s · prompt 130171 tok · output 897 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать доменную сущность User с точки зрения DDD и чистой архитектуры. Давайте разберём код:

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

Проблемы:

1. **EntityManager в конструкторе сущности** — это нарушение чистой архитектуры. Доменная сущность зависит от инфраструктурного компонента (Doctrine). Это нарушает правило зависимостей: Domain → ничего. Сущность не должна знать, что существует ORM.

2. **$this->em->flush() внутри метода сущности** — сущность управляет транзакцией. Это нарушение ответственности: сущность должна только менять своё состояние, а сохранение — задача application service или unit of work.

3. **Нарушение SRP** — сущность и хранит данные, и управляет persistence.

4. **Нарушение OCP/DIP** — сущность жёстко привязана к EntityManagerInterface. Если мы захотим сменить ORM — переписываем домен.

5. **Нет валидации** — changeEmail принимает любой string. Пустая строка? Не email? Нет проверки.

6. **Нет доменных событий** — изменение email не фиксируется. Нет audit trail.

7. **Нет ID** — сущность без идентификатора. Это не Entity в DDD-смысле, это ближе к VO или DTO.

8. **readonly не используется** — email mutable через changeEmail, но нет immutable-альтернативы.

9. **Нет инкапсуляции** — email() — публичный getter, что само по себе ок, но в контексте проблемы — нет защиты от невалидных состояний.

10. **EntityManager в конструкторе** — значит, каждый раз, когда мы создаём User, нужен EntityManager. Невозможно создать User в unit-тесте без DI-контейнера.

11. **Нарушение границы слоя** — в чистой архитектуре Domain не знает про Infrastructure. EntityManager — это Infrastructure.

Теперь как исправить:

1. Убрать EntityManager из сущности
2. Убрать flush из сущности
3. Добавить валидацию email
4. Добавить ID
5. Добавить доменное событие
6. Перенести flush в application service
7. Сделать сущность чистым PHP-классом

Структура:
- Domain: User (чистый класс), UserEmail (VO), UserEmailChanged (event)
- Application: ChangeEmailHandler (вызывает flush)
- Infrastructure: UserEntity (Doctrine), UserRepository (реализация порта)

Нужно показать конкретный код. Давайте структурирую ответ:

1. Перечень проблем (с привязкой к конкретным строкам)
2. Почему каждая проблема важна (последствия)
3. Как исправить (конкретный код)

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 8.1s · prompt 130345 tok · output 724 tok · ⚠️ обрыв: length

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе работы с незнакомой технологией — от первого взгляда на задачу до оценки сроков. Это вопрос о рабочем процессе и инженерном суждении. Нужно описать конкретную, практичную последовательность действий, а не абстрактные принципы.

Ключевые аспекты:
1. Конкретная последовательность действий (не "изучи документацию", а что именно и в каком порядке)
2. Критерии, по которым я понимаю, что разбираюсь достаточно / недостаточно
3. Честная оценка, когда просить помощь, а когда копать дальше
4. Формулировка сроков — не "3 дня", а как я прихожу к цифре
5. Почему именно так, а не иначе

Структура ответа:
- Фаза 0: не делать ничего (сначала понять контекст)
- Фаза 1: минимальный прогон (5-15 мин)
- Фаза 2: определение границ проблемы (что я должен знать, а что нет)
- Фаза 3: работа по нарастающей (spike → прототип → решение)
- Фаза 4: оценка сроков (как я считаю)
- Граница: когда просить помощи
- Чего НЕ делать

Важно: показать, что это не линейный процесс "изучи → напиши", а итеративный с конкретными checkpoint'ами. И что "незнакомая технология" — не повод парализоваться, а повод урезать scope.

Формат: последовательность с конкретными действиями, временими, checkpoint'ами. Без воды. С примерами.

</details>

**Кандидат:**

# Работа с незнакомой технологией: от нуля до оценки сроков

## Фаза 0: Не трогать код (10 минут)

Прежде чем открыть IDE, я задаю себе и заказчику вопросы:

**Заказчику / в ticket'е:**
- Что должно работать на входе? Что — на выходе?
- Какой уровень качества: prototype, internal tool, production?
- Есть ли SLA? «Работает» — это 99% uptime или «не падает в демо»?
- Есть ли у кого-то в команде опыт с этой технологией? (Даже «я разбирался полгода назад» меняет план.)
- Какой дедлайн? Не «как можно быстрее», а конкретная дата.

**Себе:**
- Я действительно ничего не знаю про этот стек, или знаю 80% и незнаком 20%?
- Проблема в самом стеке или в интеграции (а стек — просто транспорт)?
- Есть ли у проблемы «узкое место», которое можно решить в обход стека?

**Почему важно:** я встречал задачи «напиши интеграцию с Kafka», где на самом деле нужно было «отправлять 500 сообщений в час», а Kafka была выбрана на авось.

> ⏱ 9.1s · prompt 130427 tok · output 644 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о коммуникации: пообещал срок, понял что не уложится. Это вопрос не про код, а про профессиональную коммуникацию и управление ожиданиями.

Нужно дать конкретный, практичный ответ. Не абстрактное "нужно общаться с командой", а именно:
1. Когда точно понял, что не уложусь? (не "к вечеру", а как только понял)
2. Как формулирую? (конкретно, с причиной, с решением)
3. Что НЕ делаю? (не молчу, не "старюсь успеть", не перекладываю вину)
4. Разные ситуации: deadline жёсткий vs гибкий, причина задержки моя vs внешняя
5. Как формулирую — конкретные фразы
6. Что делаю с обещанием (не "ну извините", а план)
7. Почему именно так, а не иначе

Структурирую:
- Главный принцип (когда говорить)
- Как говорю (формулировка, конкретные примеры)
- Что НЕ делаю (антипаттерны)
- Разные ситуации (жёсткий deadline, причина внешняя, причина моя)
- Как предотвращать в будущем (оценка)
- Почему именно так (психология, последствия молчания)

Буду писать на русском, конкретно, с примерами фраз. Не абстрактно.

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.9s · prompt 130749 tok · output 322 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансe между качеством кода и скоростью доставки. Это вопрос о "good enough" vs perfectionism в реальной работе.

Я должен ответить в стиле предыдущих ответов: конкретно, с примерами кода, без воды, с честностью о компромиссах. Структура должна быть практичной: как я реально принимаю решение, а не теория.

Ключевые аспекты:
1. "Достаточно хорош" — это не одно состояние, а зависит от контекста (критичность, аудитория, частота изменений)
2. Конкретные критерии, по которым я останавливаюсь
3. Что я НЕ делаю "на потом"
4. Честные примеры компромиссов
5. Как я защищаю себя от перфекционизма И от безразличия

Не буду делать это слишком длинным. Пользователь уже получил много длинных ответов. Здесь нужен сжатый, но конкретный ответ.

</details>

**Кандидат:**

# "Достаточно хорошо" и когда остановиться

## Короткий ответ

Код "достаточно хороший", когда **следующее улучшение не пропорционально риску**, который оно снижает.

> ⏱ 4.1s · prompt 130813 tok · output 258 tok · ⚠️ обрыв: length

---
