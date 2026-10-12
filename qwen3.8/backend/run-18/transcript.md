# Собес: `qwen3.8:latest` · роль: backend — run-18

- Дата: 2026-10-12T09:58:34.955533+00:00
- Сервер: http://nserv:11434 · keep_alive: 10m · think: low · num_ctx: 131072
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to role-play as a PHP backend developer candidate in a job interview. I need to present myself as a senior PHP developer with experience in the listed technologies, and then rate myself from 0 to 10 on each topic with a one-sentence explanation.

I should be realistic — not claim perfect 10s everywhere, but show a strong profile with some honest limitations. This makes the character feel genuine and interview-ready. Let me craft a natural, confident but not arrogant response.

Let me think about what a strong senior PHP developer profile looks like:
- PHP 8: should be high (9), knowing fibers, enums, readonly, constructor promotion, etc.
- Symfony: high (9), core framework for many PHP shops
- Doctrine: high (8-9), ORM is a core skill
- PostgreSQL: good (7-8), know it well but it's not the primary database for everyone
- DDD: solid (7-8), practiced it but not a theoretical purist
- Clean architecture: good (7), understand the principles, apply them but acknowledge it's a spectrum
- Testing: high (8-9), PHPUnit, integration tests, etc.
- Git: high (9), it's a daily tool

Let me write this in a natural interview tone in Russian.

</details>

**Кандидат:**

Привет! Рад, что мы сели.

По опыту — в PHP около 7 лет. Начинал с Laravel, потом перешёл на Symfony и уже почти 5 лет работаю в стеке Symfony + Doctrine + PostgreSQL. Последние два года — в команде, где мы строим микросервисную платформу для финтеха: на каждую доменную подсистему свой сервис, общий API-гейтвей, Kafka для асинхронных событий.

Из того, что ближе всего:

- **Symfony** — пишу на нём каждый день. Не только контроллеры и формы, но и event subscribers, message bus (AMQP), serializer, validator, security bundle на уровне конфигурации и кастомных Voter'ов.
- **Doctrine** — ORM, DQL, DBAL, миграции, кастомные типы, hydration, lazy loading, batch operations. Знаю, где ORM упирается, и когда стоит спускаться на уровень SQL.
- **PostgreSQL** — пишем queries, индексы (B-tree, GIN, GiST), partitioning, CTE, window functions, advisory locks. Не DBA, но достаточно уверенно читаю EXPLAIN и знаю, когда индекс помогает, а когда нет.
- **DDD** — в последние два года активно применяем: bounded contexts, aggregate roots, domain events, value objects. Понимаю, что это не догма, а набор приёмов, которые работают на средней/высокой сложности.
- **Тестирование** — PHPUnit, integration-тесты через Symfony's KernelTestCase, feature-тесты через Panther/Panama, mock-и через Prophecy. Покрытие в команде ~70–80%, на критичных доменах — выше.
- **Git** — daily driver: rebase, cherry-pick, bisect, worktree, interactive rebase для истории. Пишу скрипты для автоматизации merge-процессов.

---

Оценки:

| Раздел | Оценка | Комментарий |
|--------|--------|-------------|
| PHP 8 | **9** | Enums, readonly, fibers, constructor promotion, attributes, named args — всё на уровне «использую и могу объяснить зачем». |
| Symfony | **9** | Пять лет в бою, знаю внутренние механизмы (DI container, event dispatch, cache pool, messenger) не только из документации. |
| Doctrine | **8** | Уверенный ORM-разработчик, понимаю ограничения (N+1, hydration, detached entities), но, например, написания кастомного AST visitor'а не практиковал. |
| PostgreSQL | **7** | Пишу сложные queries, понимаю planner, но не занимался тюнингом на уровне pg_hint_plan и не подбирал параметры под production-нагрузку. |
| DDD | **7** | Применяю в реальных проектах, но не пишу на эту тему статьи и не провёл conference talk — глубина «практик, а не теоретик». |
| Чистая архитектура | **7** | Принципы понимаю и применяю (dependency inversion, separation of layers), но не фанатик — иногда осознанно жертвую чистотой ради скорости доставки. |
| Тестирование | **8** | Пишу unit, integration и feature-тесты, настраивал CI-пайплайны; слабое место — property-based testing и fuzzing. |
| Git | **9** | Использую ежедневно, уверенно работаю со сложными сценариями, менторил младших по git-гигиене. |

Готов ответить на любые уточняющие вопросы.

> ⏱ 12.0s · prompt 135 tok · output 1097 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The interviewer is asking about the most important PHP 8.0+ features for writing clean and reliable code, with short code examples. I need to pick the features that genuinely matter for code quality and reliability, not just list everything.

Let me think about what's most impactful:

1. **Enums (8.1)** — kills the string/integer constants anti-pattern, gives exhaustiveness checking in switch
2. **Readonly properties (8.2)** — immutability, safer objects
3. **Constructor property promotion (8.0)** — less boilerplate
4. **Named arguments (8.0)** — readability, less fragile code
5. **Union types & intersection types (8.0)** — more precise type system
6. **Fibers (8.1)** — async without coroutines complexity (though this is more about architecture than "clean code")
7. **Attributes (8.0)** — replacing annotations, better DX
8. **Match expression (8.0)** — replacing switch, no fallthrough, returns value
9. **Nullsafe operator (8.0)** — cleaner chains
10. **Never return type (8.1)** — expresses "this never returns"
11. **Ditto operator (8.1)** — less repetition in type hints

For a senior role, I should focus on features that improve **reliability** and **cleanliness**:

- Enums — eliminates a whole class of bugs (invalid string constants)
- Readonly — enforces immutability
- Match — eliminates fallthrough bugs
- Union/intersection types — more precise contracts
- Constructor promotion + readonly combined — clean immutable DTOs
- Never — expresses intent clearly
- Named arguments — self-documenting

I'll pick 5-6 most impactful ones, show short examples, and explain *why* they matter for reliability. I'll structure it as a concise answer with code blocks.

</details>

**Кандидат:**

## Ключевые фичи PHP 8.0+ для чистого кода

### 1. Enums (8.1) — убивают «магические строки»

До:
```php
// где-то в проекте разбросано
final class OrderStatus
{
    public const PENDING = 'pending';
    public const PAID    = 'paid';
    public const SHIPPED = 'shipped';
    public const CANCELLED = 'cancelled';
}
```

После:
```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid    = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Shipped, self::Cancelled => true,
            self::Pending, self::Paid      => false,
        };
    }
}
```

**Почему важно:** IDE подсказывает все варианты, `match` без `default` — компилятор требует exhaustion. Невозможно передать `'paied'` — тип не пройдёт. Это снимает целый класс багов, которые раньше ловились только тестами.

---

### 2. Readonly-свойства (8.2) — имутабельность как контракт

```php
readonly class Money
{
    public function __construct(
        public int    $amount,
        public string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException();
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

**Почему важно:** Объект нельзя случайно мутировать в какой-то точке сервиса. `->amount = 999` — fatal error в рантайме. В связке с `match` на enum — получаем «функциональный» стиль: состояние передаётся, а не изменяется. Это особенно ценно в DDD-агрегатах.

---

### 3. `match` вместо `switch` — нет fallthrough, возвращает значение

```php
public function price(): Money
{
    return match ($this->status) {
        OrderStatus::Pending   => $this->basePrice,
        OrderStatus::Paid      => $this->basePrice->add(new Money(1500, 'RUB')),
        OrderStatus::Shipped,
        OrderStatus::Cancelled => Money::zero('RUB'),
    };
}
```

**Почему важно:** В `switch` забыл `break` — баг, который не видит компилятор. В `match` — синтаксически невозможно. Плюс `match` — это выражение, а не инструкция: нет необходимости в `$result = null` и `return $result` в конце.

---

### 4. Union + Intersection types (8.0) — точные контракты

```php
/**
 * Принимает только те объекты, которые и сериализуемые, и имеют ID.
 */
function logEntity(Serializable&HasId $entity): void { /* ... */ }

function format(#[\SensitiveParameter] int|float $amount): string
{
    return number_format($amount, 2);
}
```

**Почему важно:** Раньше для «или int, или float» писали docblock `int|float` и надеялись на ревью. Теперь статические анализаторы (PHPStan, Psalm) и IDE понимают это нативно. Intersection types позволяют выразить «утиный тип» без интерфейса-обёртки.

---

### 5. `never` return type (8.1) — честная сигнатура

```php
function throwIfNegative(int $value): never
{
    throw new NegativeValueException("Value must be >= 0, got {$value}");
}

function handle(int $id): Order
{
    $order = $this->repo->find($id);
    throwIfNegative($id); // ← статический анализатор знает: дальше кода нет
    return $order;
}
```

**Почему важно:** PHPStan переставает ругаться на «возможно, функция вернёт null». Ты честно говоришь: «если эта функция вернула — что-то сломано». Это снимает ложные positive-результаты в анализаторах.

---

### 6. Named arguments (8.0) — читаемость вызовов

```php
// до — три int'а, непонятно что что
new Invoice(42, 1000, 'RUB', true);

// после — самообъясняющийся вызов
new Invoice(
    orderId: 42,
    amount: 1000,
    currency: 'RUB',
    isTaxInclusive: true,
);
```

**Почему важно:** При рефакторинге (добавили параметр, поменяли порядок) код с named args не ломается. Читается как документация. В связке с `readonly` + constructor promotion — DTO-классы становятся компактными и самодокументирующимися.

---

### Итого

| Фича | Что даёт |
|------|----------|
| Enums | Типизированное состояние, exhaustion |
| `readonly` | Иммутабельность в рантайме |
| `match` | Нет fallthrough, это выражение |
| Union/Intersection types | Точные контракты без docblock |
| `never` | Честные сигнатуры для анализаторов |
| Named args | Читаемость, устойчивость к рефакторингу |

Все эти фичи — не «фичи ради фич», а инструменты, которые перекладывают ответственность с разработчика (и ревьюера) на компилятор/анализатор. Меньше кода, меньше багов, меньше «а почему это не сломалось в продакшене».

> ⏱ 13.5s · prompt 1011 tok · output 1786 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

The user is asking about PHP's type system in depth: strict_types, type coercion, common pitfalls, and examples where typing saves vs surprises. This is a continuation of a mock interview where I'm playing a PHP backend developer. I should give a thorough, practical answer with code examples that demonstrates deep understanding. Let me structure this well:

1. How PHP's type system works (coercive vs strict mode)
2. strict_types in detail — file-level, not function-level
3. Coercion rules and pitfalls
4. Examples where typing saves (PHPStan, runtime)
5. Examples where it surprises (mixed types, nullable, union edge cases)

I want to show real-world pitfalls that trip up developers, not just textbook stuff. Let me think about the genuinely surprising ones:

- strict_types is per-file, per the CALLING file, not the called function's file
- `true == "1"` is true (loose comparison)
- Array keys: `"1"` and `1` are the same key
- `int|string` vs `string|int` — no, that's the same
- `null` in nullable types — `int?` accepts null, but `mixed` also accepts null AND resources (before 8.0)
- `iterable` accepts array and Traversable — but not Closure that returns iterable
- `object` type — accepts any object, not even a specific one
- Union types: `int|float` — but `5.0` (float) is NOT an int, even though `5 == 5.0` is true
- `readonly` + `int` — but what about `__toString`?
- The big one: `strict_types` is about the CALLING file. If file A doesn't declare `declare(strict_types=1)`, then calls FROM file A are coercive, even if the callee's file has strict_types.
- `mixed` type in 8.0+ — it's the top type, includes null, callable, resource
- `never` vs `void`
- Type hints on parameters don't check return values of internal calls — only the final return statement
- `array<int>` in docblock vs `array` in signature — PHPStan level matters

Let me write a comprehensive but focused answer with clear examples.

</details>

**Кандидат:**

## Система типов PHP: как это устроено на самом деле

### Базовая модель: coercive by default

PHP — язык со слабыми типами. Без `strict_types` при передаче аргумента PHP попробует привести значение к объявленному типу:

```php
function add(int $a, int $b): int {
    return $a + $b;
}

add("4", "6");   // 10 — строки приведены к int
add(3.7, 2.1);   // 5 — float обрезан (3 + 2)
add(true, "1");  // 2 — true → 1, "1" → 1
```

Никакой ошибки, никакого предупреждения. Это и есть **coercive mode** — по умолчанию в каждом файле.

---

### `strict_types=1`: файл, а не функция

Критический момент, который путает многих: **`strict_types` определяет поведение в файле-вызывающем, а не в файле, где объявлена функция.**

```php
// file_a.php
declare(strict_types=1);

require 'file_b.php';

add("4", "6"); // TypeError: Argument #1 ($a) must be of type int, string given
```

```php
// file_b.php
// НЕТ strict_types

function add(int $a, int $b): int {
    return $a + $b;
}
```

```php
// file_c.php
// НЕТ strict_types

require 'file_b.php';

add("4", "6"); // 10 — coercive, потому что ВЫЗЫВАЮЩИЙ файл не в strict mode
```

**Практический вывод:** `strict_types` — это не свойство функции, а свойство *точки вызова*. В реальных проектах это означает: если вы пишете библиотеку, вы не можете гарантировать strict-поведение — это зависит от того, кто вас вызывает.

---

### Что реально спасает

#### 1. Типизация аргументов + `match` на enum

```php
enum PaymentMethod: string {
    case Card = 'card';
    case Wallet = 'wallet';
    case Cash = 'cash';
}

class PaymentProcessor
{
    public function process(PaymentMethod $method, int $amountCents): void
    {
        match ($method) {
            PaymentMethod::Card   => $this->cardGateway->charge($amountCents),
            PaymentMethod::Wallet => $this->walletGateway->charge($amountCents),
            PaymentMethod::Cash   => $this->logCash($amountCents),
        };
    }
}
```

Без enum: `process("card", 100)` — если кто-то напишет `"Card"` с большой буквы, `"CARD"`, `"card "`, `"cardx"` — всё сработает, пока не упрётся в `match` с `default => throw`. С enum — **TypeError на входе**, до того как логика вообще начнёт выполняться.

#### 2. `readonly` + конструктор — DTO как контракт

```php
readonly class Transfer
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly int    $amountCents,
    ) {}
}

// В любом месте:
$transfer->amountCents = 999999; // Fatal error: Cannot modify readonly property
```

Без `readonly` — кто-то в середине 200-строчного метода сервиса случайно присвоит `->amountCents = 0`, и тесты могут не покрыть именно этот путь. С `readonly` — ошибка в момент присвоения, а не через 3 часа в продакшене.

#### 3. `never` + PHPStan

```php
function getOrFail(Repo $repo, int $id): Order
{
    $order = $repo->find($id);
    if ($order === null) {
        throw OrderNotFoundException::withId($id);
    }
    return $order;
}

// В вызывающем коде:
$order = $this->getOrFail($repo, $id);
$order->total(); // PHPStan знает: $order — Order, не null
```

Без `getOrFail` и явного типа — PHPStan на level 8+ будет ругаться «possibly null» на каждом `.->`. С `never` в `throw` — анализатор понимает, что после `throw` кода нет.

#### 4. Union types + PHPStan — замена docblock

```php
/** @return int|float */
function calculateRate(): int|float
{
    // ...
}
```

До 8.0 — только docblock, и PHPStan на level 1–4 мог не проверять. С 8.0 — нативный union type, статические анализаторы проверяют его как часть сигнатуры. Не нужно запоминать, на каком уровне PHPStan «включён».

---

### Где удивляет: типичные ловушки

#### Ловушка 1: `int` vs `float` — `5` и `5.0`

```php
function acceptInt(int $value): void {}

acceptInt(5);    // OK
acceptInt(5.0);  // TypeError! float не приводится к int в strict mode

// Но:
5 == 5.0         // true
5 === 5.0        // false
```

**Почему:** `strict_types` запрещает *приведение*, а `float → int` — это приведение (потенциальная потеря точности). `==` — сравнение, `===` — строгое. Это не баг, но контринтуитивно: `5.0` визуально «целое число», но тип — `float`.

#### Ловушка 2: Ключи массива — `"1"` и `1`

```php
$arr = [
    1    => "int key",
    "1"  => "string key",  // ← перезаписывает предыдущее!
];

echo count($arr); // 1, не 2
echo $arr[1];     // "string key"
echo $arr["1"];   // "string key"
```

Строковый ключ `"1"` превращается в `int` при сохранении. Это значит:

```php
class IdMap
{
    private array $map = [];

    public function set(string $id, mixed $value): void
    {
        $this->map[$id] = $value;
    }
}

$map->set("1", "user one");
$map->set("1", "user one (updated)");
$map->set(1, "user int"); // ← перезаписывает "user one (updated)"!
```

Если вы храните в массиве с ключами, которые могут быть `int` или `string` — столкнётесь с этим. Решение: префикс (`"id:1"`) или отдельный `Map`-объект.

#### Ловушка 3: `true == "1"` и другие loose-сравнения

```php
// Эти все — true:
true == 1
true == "1"
true == "yes"
true == " "   // ← да, не пустая строка, а пробел
"" == 0
"abc" == 0
"1abc" == 1
"01" == 1
[] == false
"[]" == false  // false — это строка, не массив
null == ""
null == "null"
```

**Практическое правило:** в бизнес-коде — только `===`. Исключение: `null` — здесь `===` работает корректно, а `== null` эквивалентно `is_null()`.

#### Ловушка 4: `iterable` — не то, что вы думаете

```php
function process(iterable $items): void { /* ... */ }

process([1, 2, 3]);                    // OK — array
process(new ArrayIterator([1, 2]));    // OK — Traversable
process(fn() => [1, 2, 3]);           // TypeError! Closure — не iterable
process(new Generator());             // OK — Generator implements Traversable
```

`iterable` = `array|Traversable`. Closure, который *возвращает* iterable, — **не** iterable. Это ловит в `foreach`-обёртках и stream-обработчиках.

#### Ловушка 5: `mixed` — не «any», а конкретный тип

```php
function log(mixed $value): void { /* ... */ }

log(null);       // OK
log(42);         // OK
log("str");      // OK
log(new \stdClass); // OK
log(CLOSED);     // OK — resource (до 8.0, в 8.0+ всё ещё valid)
```

`mixed` включает `null`, `resource`, `callable` (в 7.x). В PHP 8+ `mixed` — это *топ-тип* в иерархии, но **не** `unknown` или `any` из других языков. Он не «отключает» проверки — PHPStan всё ещё проверяет, что вы не вызываете `->method()` на `mixed` без каста.

#### Ловушка 6: `object` — не «любой объект», а «конкретный объект»

```php
function handle(object $obj): void { /* ... */ }

handle(new \stdClass); // OK
handle(null);          // TypeError
handle("string");      // TypeError
handle(fn() => null);  // TypeError — Closure это callable, не object в этом контексте
                       // (хотя Closure — это объект, но PHP отличает)
```

Парадокс: `Closure` — это объект, но `handle(fn() => null)` — **TypeError**, потому что `object` в сигнатуре означает «любой экземпляр класса», а не «любой объект, включая Closure». На практике это мало встречается, но при рефакторинге — ловит.

#### Ловушка 7: `?Type` vs `Type|null` — одно и то же, но `?mixed` ≠ `mixed|null`

```php
function a(?int $x) { }    // int|null
function b(int|null $x) { } // int|null — идентично
function c(mixed $x) { }   // mixed — уже включает null
function d(mixed|null $x) { } // синтаксически valid, но redundant — mixed ⊇ null
```

Если вы пишете `?string` — это `string|null`. Если пишете `string|false` — это **не** `?string`. `false` не является `null`. Это частый источник багов:

```php
function getConfig(string $key): string|false
{
    return $_GET[$key] ?? false;
}

$value = getConfig('name'); // string|false
if ($value) { /* ... */ }   // OK, но $value может быть "" — ложное срабатывание
if ($value !== false) { /* ... */ } // правильная проверка
```

---

### Итого: чеклист для чистого кода

| Практика | Зачем |
|----------|-------|
| `declare(strict_types=1)` в **каждом** файле | Запрет silent coercion |
| Enums для конечных множеств значений | Exhaustiveness + IDE + runtime check |
| `readonly` на DTO и value objects | Иммутабельность в рантайме |
| `int\|float` вместо `number` (которого нет) | Точный контракт |
| `never` для функций, которые всегда бросают | Честные сигнатуры для анализаторов |
| `===` / `!==` в бизнес-логике | Нет loose-сравнений |
| `?Type` только для «может быть null» | Не путать с `Type\|false` |
| PHPStan level 8+ | Ловит то, что runtime не ловит |

Система типов PHP — не «слабая», а **опционально строгая**. Если вы не включаете strict mode, не пишете union types и не используете анализаторы — вы просто выбираете слабый режим. А если включите — получите статические гарантии, сопоставимые с типизацией в языках со строгой системой (Ty, Pydantic-подобные).

> ⏱ 26.3s · prompt 2451 tok · output 3342 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user is asking about exception design in PHP applications: hierarchies, wrapping foreign exceptions, what constitutes a method contract, and common antipatterns. This is a follow-up to a simulated PHP backend interview.

I need to cover:
1. How to design exception hierarchies (domain, infrastructure, application layers)
2. Wrapping foreign exceptions (doctrine, symfony, third-party libs)
3. What constitutes a "contract" of a method (what it throws, what it doesn't)
4. Antipatterns: catching too broad, throwing exceptions for flow control, generic exceptions, etc.

I should show code examples, keep it practical and grounded in real Symfony/PHP context. Let me structure this clearly.

</details>

**Кандидат:**

## Проектирование исключений в PHP-приложении

### 1. Иерархия: три уровня ответственности

```
AppException (abstract, extends \Exception)
├── DomainException
│   ├── OrderCannotBeShippedException
│   ├── InsufficientBalanceException
│   └── InvalidAmountException
├── ApplicationException
│   ├── OrderNotFoundException
│   └── DuplicateEmailException
└── InfrastructureException
    ├── DatabaseConnectionException
    ├── PaymentGatewayTimeoutException
    └── PaymentGatewayDeclinedException
```

**Принцип:** каждый уровень знает о себе и о нижних. Домен не знает про Doctrine. Инфраструктура не знает про бизнес-правила.

```php
// domain/Exception/DomainException.php
abstract class DomainException extends \Exception
{
    // Базовое сообщение + контекст (ID, сумма)
    // НО: нет stack trace с infrastructure-слоем
}

// domain/Exception/OrderCannotBeShippedException.php
final class OrderCannotBeShippedException extends DomainException
{
    public function __construct(
        private readonly int $orderId,
        string $reason,
    ) {
        parent::__construct(
            sprintf("Order #%d cannot be shipped: %s", $orderId, $reason),
            422, // HTTP-статус на уровне домена? Споров много — см. ниже
        );
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
```

```php
// infrastructure/Exception/PaymentGatewayTimeoutException.php
final class PaymentGatewayTimeoutException extends InfrastructureException
{
    public function __construct(
        string $gateway,
        int $timeoutMs,
        \Throwable $previous,
    ) {
        parent::__construct(
            sprintf(
                "Payment gateway '%s' timed out after %d ms",
                $gateway,
                $timeoutMs,
            ),
            504,
            $previous,
        );
    }
}
```

**Почему три уровня, а не два:**

| Уровень | Кто бросает | Кто ловит | Пример |
|---------|-------------|-----------|--------|
| Domain | Агрегат, доменная логика | Application (контроллер) | «Баланс недостаточен» |
| Application | Сервис, оркестрация | Контроллер, middleware | «Заказ #42 не найден» |
| Infrastructure | Репозиторий, клиент API | Application (переводит) | «Postgres connection refused» |

Контроллер знает про `DomainException` и `ApplicationException`. Он **не знает** про `Doctrine\DBAL\Exception\ConnectionException` — это ловит и переводит инфраструктурный слой.

---

### 2. Оборачивание чужих исключений: правило «переведи на своём уровне»

```php
// infrastructure/OrderRepository.php
final class OrderRepository
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function find(int $id): ?Order
    {
        try {
            $row = $this->connection
                ->fetchAssociative('SELECT * FROM orders WHERE id = ?', [$id]);

            if ($row === false) {
                return null;
            }

            return Order::fromArray($row);
        } catch (ConnectionException $e) {
            // Переводим: «что-то пошло не так с БД»
            throw new DatabaseConnectionException(
                previous: $e,
            );
        } catch (DriverException $e) {
            // Percona/PDO-specific
            throw new DatabaseConnectionException(
                previous: $e,
            );
        }
    }
}
```

```php
// application/OrderService.php
final class OrderService
{
    public function __construct(
        private readonly OrderRepository $repo,
    ) {}

    public function getById(int $id): Order
    {
        $order = $this->repo->find($id);

        if ($order === null) {
            // Domain-контракт: «если нет — это не ошибка БД, это бизнес-факт»
            throw new OrderNotFoundException($id);
        }

        return $order;
    }
}
```

```php
// controller/OrderController.php
final class OrderController
{
    public function get(int $id): Response
    {
        // Здесь ловим ТОЛЬКО свои исключения.
        // Doctrine, PDO, cURL — не должны дойти сюда.
        $order = $this->orderService->getById($id);

        return $this->json($order);
    }
}
```

**Почему так:**

- Если в `OrderRepository` кто-то добавит `catch (\Throwable $e)` — это уже красный флаг.
- Если в контроллер дойдёт `Doctrine\DBAL\Exception\DriverException` — это значит, что где-то выше не перехватили.
- Каждый уровень «переводит» исключение на свой язык. Нижний уровень не «знает» про HTTP-статусы.

---

### 3. Контракт метода: что бросает, что нет

Это самый недокументированный аспект PHP. В Java есть checked exceptions. В PHP — нет. Значит, контракт — это **соглашение, закреплённое доком и типами**.

```php
final class OrderService
{
    /**
     * Возвращает заказ по ID.
     *
     * @throws OrderNotFoundException         если заказ не существует
     * @throws DomainException                если состояние БД нарушает инвариант (теоретически)
     * @throws InfrastructureException        если БД недоступна
     *
     * НЕ бросает:
     *   - null-исключения (вместо null — exception)
     *   - InvalidArgumentException (валидация входных данных — ответственность контроллера)
     */
    public function getById(int $id): Order
    {
        // ...
    }
}
```

**Правило:** в docblock перечисляйте **все** исключения, которые метод может бросить, и **явно** указывайте, чего он НЕ бросает. Последнее — важнее всего, потому что вызывающий код опирается на это.

Практический приём — `@psalm-throw` или `@phpstan-throw` для статического анализа:

```php
/**
 * @phpstan-throw OrderNotFoundException
 * @phpstan-throw InfrastructureException
 */
```

Тогда PHPStan (level 9) проверяет, что вы действительно бросаете только эти.

---

### 4. Антипаттерны

#### ❌ Антипаттерн 1: «Исключение для потока управления»

```php
// ПЛОХО
function findUser(int $id): ?User
{
    try {
        return $this->repo->find($id);
    } catch (UserNotFoundException $e) {
        return null;
    }
}

// ХОРОШЕ
function findUser(int $id): ?User
{
    return $this->repo->find($id); // репозиторий возвращает null
}

// ХОРОШЕ (если «нет» — это ошибка)
function findUserOrFail(int $id): User
{
    $user = $this->repo->find($id);
    if ($user === null) {
        throw new UserNotFoundException($id);
    }
    return $user;
}
```

**Почему:** «нет пользователя» — это нормальная ситуация, а не исключительная. Исключения — для случаев, которые ломают нормальный поток. Если вы ловите исключение, чтобы вернуть `null` или `false` — вы использовали механизм error handling для flow control. Это медленнее (stack trace) и менее читаемо.

---

#### ❌ Антипаттерн 2: «Ловлю `\Exception` и делаю вид, что всё ок»

```php
// ПЛОХО
public function process(int $orderId): void
{
    try {
        $this->orderService->ship($orderId);
    } catch (\Exception $e) {
        $this->logger->error('Something went wrong', ['e' => $e->getMessage()]);
        // Продолжаем работу. Заказ не отгрузился, но клиент не знает.
    }
}
```

**Почему плохо:**
- Проглатываете `DomainException` (бизнес-правило не сработало) — заказ завис.
- Проглатываете `InfrastructureException` (БД упала) — заказ завис.
- Клиент думает, что всё хорошо.
- `getMessage()` не содержит stack trace — в логах только «Something went wrong».

**Как правильно:**

```php
public function process(int $orderId): void
{
    try {
        $this->orderService->ship($orderId);
    } catch (DomainException $e) {
        // Бизнес-ошибка: логгируем, уведомляем, но НЕ повторяем автоматически
        $this->logger->error('Ship failed (domain)', ['order' => $orderId, 'reason' => $e->getMessage()]);
        throw $e; // Передаём выше — пусть контроллер решит, что отвечать
    } catch (InfrastructureException $e) {
        // Инфраструктурная: логгируем, можно ретраить
        $this->logger->error('Ship failed (infra)', ['order' => $orderId, 'trace' => $e->getTraceAsString()]);
        throw $e;
    }
}
```

Ключевое: **передаём выше**, а не проглатываем. Решение «что делать с ошибкой» принимает верхний уровень (контроллер / middleware / job queue).

---

#### ❌ Антипаттерн 3: «Исключение с HTTP-статусом в домене»

```php
// ПЛОХО
class OrderCannotBeShippedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Cannot ship', 422);
    }
}
```

**Почему плохо:** доменный слой не знает про HTTP. Статус 422 — это решение транспортного уровня. Если вы хотите, чтобы API вернул 500 вместо 422 — не должны переопределять доменное исключение.

**Как правильно:** HTTP-статус определяется в **одном месте** — в exception handler (Symfony: `ExceptionListener` или кастомный `KernelExceptionEvent` listener).

```php
// src/EventListener/ExceptionListener.php
final class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof OrderNotFoundException) {
            $event->setResponse($this->json(404, 'Order not found'));
        }
        if ($exception instanceof OrderCannotBeShippedException) {
            $event->setResponse($this->json(422, $exception->getMessage(), [
                'order_id' => $exception->getOrderId(),
            ]));
        }
        // Infrastructure → 500
        // Domain → 422 (по умолчанию)
    }
}
```

Теперь доменное исключение — чистое, без HTTP. Транспортный уровень решает, какой статус вернуть.

---

#### ❌ Антипаттерн 4: «Исключение с логикой в сообщении»

```php
// ПЛОХО
function calculateDiscount(Order $order): float
{
    $discount = $this->calculator->calculate($order);
    if ($discount < 0) {
        throw new \Exception(
            "Discount for order #{$order->getId()} with status {$order->getStatus()->value} "
            . "and total {$order->total()} is negative: {$discount}. "
            . "Please check the discount rules. The customer is {$order->customerEmail()}. "
            . "Contact support at support@example.com if this persists."
        );
    }
    return $discount;
}
```

**Почему плохо:**
- Сообщение — это не место для логики.
- В тексте — email клиента (PII в логах/stack trace).
- Невозможно локализовать.
- Невозможно тестировать: `assertStringContains('negative')` — хрупко.

**Как правильно:**

```php
final class NegativeDiscountException extends DomainException
{
    public function __construct(
        private readonly int   $orderId,
        private readonly float $discount,
        private readonly float $orderTotal,
    ) {
        parent::__construct(
            sprintf('Discount is negative (%.2f) for order #%d (total: %.2f)', $discount, $orderId, $orderTotal),
        );
    }

    public function getOrderId(): int { return $this->orderId; }
    public function getDiscount(): float { return $this->discount; }
    public function getOrderTotal(): float { return $this->orderTotal; }
}

// Вызов:
throw new NegativeDiscountException($order->getId(), $discount, $order->total());
```

Теперь сообщение — короткое, данные — в геттерах, логику вывода на клиент решает transport layer.

---

#### ❌ Антипаттерн 5: «Наследование ради наследования»

```php
// ПЛОХО — 7 уровней глубины
AppException
└── ServiceException
    └── OrderServiceException
        └── ShippingException
            └── CarrierException
                └── CourierException
                    └── SpecificCourierException
```

**Почему плохо:**
- `catch (CarrierException)` — кто поймает? Application? Infrastructure? Неясно.
- Наследование создаёт ложную семантику: «CourierException — это kind of CarrierException» — да, но это не значит, что обработчик Carrier'ов может обрабатывать Courier'ов одинаково.
- Ломает LSP: обработчик для `CarrierException` может не уметь обрабатывать `CourierException` (например, courier'ы требуют ручного подтверждения).

**Как правильно:** плоская иерархия (2–3 уровня) + состав. Если нужно «группировать» — используйте интерфейс или tag, а не наследование.

---

### 5. Чеклист

| Что | Правило |
|-----|---------|
| Глубина иерархии | 2–3 уровня максимум |
| Кто бросает | Только тот слой, который может определить причину |
| Кто ловит | Вышестоящий слой, который может принять решение |
| HTTP-статус | Только в transport layer |
| Сообщение | Короткое, без PII, без логики |
| Данные | В typed геттерах, не в сообщении |
| `catch (\Exception)` | Запрещено. Только конкретные типы |
| Проглатывание | Запрещено. Лог + rethrow или явное решение |
| Контракт | В docblock + `@phpstan-throw` |
| `null` vs exception | `null` — «нет, это нормально». Exception — «нет, это нарушение контракта» |

> ⏱ 26.1s · prompt 5358 tok · output 3656 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос на собеседовании: какой принцип SOLID чаще всего нарушается в реальном PHP-коде, почему, и показать типовой пример нарушения + рефакторинг.

Мне нужно ответить как опытный PHP-разработчик с 7+ годами опыта. Подумаю, какой принцип реально нарушается чаще всего.

KISS/DRY — не SOLID.

Из SOLID:
- S (Single Responsibility) — нарушается часто, но это расплывчато.
- O (Open/Closed) — нарушается, но обычно осознанно.
- L (Liskov Substitution) — нарушается реже, потому что PHP-наследование менее распространено.
- I (Interface Segregation) — нарушается, но редко критично.
- D (Dependency Inversion) — нарушается ОЧЕНЬ часто в PHP.

Самый честный ответ: **Dependency Inversion Principle** — зависимость от конкретных реализаций вместо абстракций. В PHP это проявляется как:
1. `new ConcreteClass()` внутри методов вместо DI
2. Зависимость от репозитория конкретной СУБД (Doctrine Entity vs. доменная модель)
3. Зависимость от конкретного HTTP-клиента (Guzzle, cURL)
4. Зависимость от конкретной конфигурации (конфигурация Symfony, .env-переменные)
5. «God service» который зависит от 10 классов

Но также очень часто нарушается **Single Responsibility** — в виде «fat controllers» и «god services».

Я думаю, самый сильный и честный ответ — это DIP + SRP, но сфокусируюсь на DIP как на самом частом и конкретном. Покажу типовой пример: сервис, который делает new'ит конкретные классы внутри себя, а также зависимость от Doctrine Entity вместо доменной модели.

Лучше показать один связный пример: OrderService, который:
- Создаёт конкретный EmailSender (SendGrid) внутри
- Создаёт конкретный OrderRepository (Doctrine) внутри
- Зависит от Doctrine Entity вместо доменной модели
- Тестировать его — боль

И рефакторинг:
- Интерфейсы/абстракции
- Constructor DI
- Доменная модель отдельно от Entity
- Показываю тест до и после

Структурирую ответ:
1. Тезис: DIP
2. Почему именно он (практические причины в PHP)
3. Типовой пример «до»
4. Почему это боль (тестирование, замена)
5. Рефакторинг «после»
6. Что получилось

Напишу на русском, как и предыдущие ответы.

</details>

**Кандидат:**

## Dependency Inversion — самый частый нарушенный принцип

### Почему именно он

Из пяти принципов SOLID в реальном PHP-коде DIP нарушается чаще всего, и вот почему:

1. **PHP не имеет checked exceptions и нет «принуждения» к абстракциям.** В Java вы привыкнете к `Interface` из-за того, что реализацию передают конструктором. В PHP проще написать `new GuzzleHttp\Client()` прямо внутри метода и забыть.

2. **Компоненты Symfony поощряют «быстрый путь».** `Container`-автовайринг позволяет не думать о DI — и многие разработчики этим пользуются. Результат: сервис зависит от конкретного `EntityManagerInterface`, конкретного `Messenger`, конкретного `EventDispatcher` — и при замене любого из них приходится трогать бизнес-логику.

3. **Нет «interface by default» культуры.** В Go интерфейс из 1 метода — норма. В PHP большинство разработчиков не пишут интерфейс, если «пока один реализатор». А потом приходит второй — и начинается боль.

4. **Тестирование упирается в это первыми.** Вы хотите unit-тест для `OrderService`, а внутри `new SendGridClient()` с реальным API key. Тест превращается в integration-тест или требует сложного мок-инfra.

---

### Типовой пример: «до»

```php
// src/Service/OrderService.php
final class OrderService
{
    public function place(array $payload): array
    {
        // Зависимости — конкретные классы, созданные внутри
        $em = Doctrine\ORM\EntityManager::getConnection(
            \App\Entity\Order::class
        );

        $order = new \App\Entity\Order();
        $order->setCustomerId($payload['customer_id']);
        $order->setTotal($payload['total']);
        $order->setStatus(\App\Entity\Order::STATUS_NEW);

        $em->persist($order);
        $em->flush();

        // Email — конкретный провайдер
        $sender = new \SendGrid\Client(getenv('SENDGRID_API_KEY'));
        $email = new \SendGrid\Mail(
            "support@example.com",
            "New order #{$order->getId()}",
            "text/plain",
            "Order placed successfully."
        );
        $sender->send($email);

        // Оплата — конкретный API
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://pay.example.com']);
        $response = $client->post('/charge', [
            'json' => [
                'order_id' => $order->getId(),
                'amount'   => $payload['total'],
            ],
        ]);

        return [
            'order_id' => $order->getId(),
            'payment'  => json_decode((string) $response->getBody(), true),
        ];
    }
}
```

**Проблемы:**

| Проблема | Последствие |
|----------|-------------|
| `new SendGrid\Client(...)` внутри | Не заменить на Mailgun без правки бизнес-логики |
| `new \GuzzleHttp\Client(...)` внутри | Не заменить на cURL, не мокнуть, не отключить в тестах |
| `\App\Entity\Order` — это Doctrine Entity | Бизнес-логика привязана к схеме БД. Смена ORM = переписать сервис |
| `getenv('SENDGRID_API_KEY')` внутри | Конфигурация размазана, не в одном месте |
| Нет DI | Unit-тест невозможен без реального SendGrid и Guzzle |
| `place()` делает 4 вещи | Создание заказа, сохранение, email, оплата — SRP тоже нарушен |

Чтобы протестировать этот код unit-тестом, вам нужно:
- Мокнуть `Doctrine\ORM\EntityManager::getConnection` (статический метод — боль)
- Мокнуть `getenv()` (нельзя)
- Мокнуть `new SendGrid\Client` (нельзя — создаётся внутри)
- Мокнуть `new \GuzzleHttp\Client` (нельзя — создаётся внутри)

Результат: **unit-тест не написан**. Написан integration-тест с реальными сетевыми вызовами или вообще ничего не написано.

---

### Рефакторинг: «после»

#### Шаг 1. Выносим доменную модель

```php
// src/Domain/Order/Order.php
final class Order
{
    private function __construct(
        public readonly int    $id,
        public readonly int    $customerId,
        public readonly int    $totalCents,
        public readonly OrderStatus $status,
    ) {}

    public static function new(int $customerId, int $totalCents): self
    {
        if ($totalCents <= 0) {
            throw new InvalidOrderAmountException($totalCents);
        }

        return new self(
            id: 0, // будет назначен репозиторием
            customerId: $customerId,
            totalCents: $totalCents,
            status: OrderStatus::New,
        );
    }

    public function withId(int $id): self
    {
        return new self($id, $this->customerId, $this->totalCents, $this->status);
    }

    public function withStatus(OrderStatus $status): self
    {
        return new self($this->id, $this->customerId, $this->totalCents, $status);
    }
}
```

```php
// src/Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
```

Доменная модель **не знает** про Doctrine, не знает про `getEntityManager()`, не знает про `BaseEntity`. Это чистый PHP-объект.

#### Шаг 2. Интерфейсы на границе

```php
// src/Domain/Order/OrderRepositoryInterface.php
interface OrderRepositoryInterface
{
    public function save(Order $order): Order;
    public function find(int $id): ?Order;
}
```

```php
// src/Domain/Payment/PaymentGatewayInterface.php
interface PaymentGatewayInterface
{
    /**
     * @return PaymentResult
     * @throws PaymentDeclinedException
     * @throws PaymentGatewayTimeoutException
     */
    public function charge(int $orderId, int $amountCents): PaymentResult;
}
```

```php
// src/Domain/Notification/NotifierInterface.php
interface NotifierInterface
{
    public function notifyEmail(
        string $to,
        string $subject,
        string $body,
    ): void;
}
```

#### Шаг 3. Реализации — в инфраструктуре

```php
// src/Infrastructure/Persistence/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(Order $order): Order
    {
        /** @var OrderEntity $entity */
        $entity = $order->id > 0
            ? $this->em->find(OrderEntity::class, $order->id)
            : new OrderEntity();

        $entity->setCustomerId($order->customerId);
        $entity->setTotalCents($order->totalCents);
        $entity->setStatus($order->status->value);

        $this->em->persist($entity);
        $this->em->flush();

        return $order->withId($entity->getId());
    }

    public function find(int $id): ?Order
    {
        /** @var OrderEntity|null $entity */
        $entity = $this->em->find(OrderEntity::class, $id);

        if ($entity === null) {
            return null;
        }

        return new Order(
            id: $entity->getId(),
            customerId: $entity->getCustomerId(),
            totalCents: $entity->getTotalCents(),
            status: OrderStatus::from($entity->getStatus()),
        );
    }
}
```

```php
// src/Infrastructure/Payment/PayGatewayAdapter.php
final class PayGatewayAdapter implements PaymentGatewayInterface
{
    public function __construct(
        private readonly Client $httpClient,  // Guzzle — но передан снаружи
    ) {}

    public function charge(int $orderId, int $amountCents): PaymentResult
    {
        try {
            $response = $this->httpClient->post('/charge', [
                'json' => ['order_id' => $orderId, 'amount' => $amountCents],
            ]);
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return PaymentResult::fromArray($data);
        } catch (GuzzleException $e) {
            if ($e instanceof ConnectException) {
                throw new PaymentGatewayTimeoutException('pay.example.com', previous: $e);
            }
            throw new PaymentGatewayException(previous: $e);
        }
    }
}
```

```php
// src/Infrastructure/Notification/SendGridNotifier.php
final class SendGridNotifier implements NotifierInterface
{
    public function __construct(
        private readonly ClientInterface $sendGrid,  // sendgrid/sendgrid
    ) {}

    public function notifyEmail(string $to, string $subject, string $body): void
    {
        $email = new Mail("system@example.com", $subject, 'text/plain', $body);
        $email->addTo($to);
        $this->sendGrid->send($email);
    }
}
```

#### Шаг 4. Сервис — тонкий, тестируемый

```php
// src/Application/OrderService.php
final class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentGatewayInterface  $payments,
        private readonly NotifierInterface        $notifier,
    ) {}

    public function place(int $customerId, int $totalCents): Order
    {
        // 1. Домен: создание
        $order = Order::new($customerId, $totalCents);

        // 2. Инфраструктура: сохранение
        $order = $this->orders->save($order);

        // 3. Инфраструктура: оплата
        $result = $this->payments->charge($order->id, $order->totalCents);

        // 4. Домен: смена статуса
        $order = $order->withStatus(OrderStatus::Paid);
        $this->orders->save($order);

        // 5. Инфраструктура: уведомление
        $this->notifier->notifyEmail(
            to: "orders@example.com",
            subject: "New order #{$order->id}",
            body: "Order #{$order->id} placed. Amount: {$order->totalCents} cents.",
        );

        return $order;
    }
}
```

#### Шаг 5. Unit-тест — тривиальный

```php
// tests/Unit/OrderServiceTest.php
final class OrderServiceTest extends TestCase
{
    public function testPlaceOrder(): void
    {
        $orders = $this->createMock(OrderRepositoryInterface::class);
        $payments = $this->createMock(PaymentGatewayInterface::class);
        $notifier = $this->createMock(NotifierInterface::class);

        $newOrder = Order::new(42, 1500);
        $savedOrder = $newOrder->withId(7);
        $paidOrder = $savedOrder->withStatus(OrderStatus::Paid);

        $orders->method('save')
            ->willReturnCallback(function (Order $o) use ($savedOrder, $paidOrder) {
                return $o->status === OrderStatus::New ? $savedOrder : $paidOrder;
            });

        $payments->method('charge')
            ->with(7, 1500)
            ->willReturn(PaymentResult::approved('tx_123'));

        $notifier->expects(self::once())
            ->method('notifyEmail')
            ->with(
                to: 'orders@example.com',
                subject: self::anything(),
                body: self::anything(),
            );

        $service = new OrderService($orders, $payments, $notifier);
        $result = $service->place(42, 1500);

        self::assertSame(7, $result->id);
        self::assertSame(OrderStatus::Paid, $result->status);
    }

    public function testPlaceOrderFailsOnNegativeAmount(): void
    {
        $service = new OrderService(
            $this->createMock(OrderRepositoryInterface::class),
            $this->createMock(PaymentGatewayInterface::class),
            $this->createMock(NotifierInterface::class),
        );

        $this->expectException(InvalidOrderAmountException::class);
        $service->place(42, -100);
    }
}
```

**Ключевое:** ни одного реального сетевого вызова. Ни одного `EntityManager`. Ни одного `getenv()`. Тест запускается за миллисекунды, без Docker, без API keys.

---

### Что изменилось: сводка

| Аспект | До | После |
|--------|----|-------|
| Зависимости сервиса | 3 конкретных класса | 3 интерфейса |
| Замена SendGrid на Mailgun | Правка `OrderService` | Новый класс `MailgunNotifier`, одна строка в `services.yaml` |
| Unit-тест | Невозможен | 20 строк, нет сети |
| Смена ORM | Переписать `OrderService` | Переписать только `DoctrineOrderRepository` |
| Добавление нового способа оплаты | Правка `OrderService` | Новый класс `StripeAdapter`, одна строка в конфиге |
| Доменная модель | Doctrine Entity (привязана к БД) | Чистый PHP-класс (иммутабельный, без `BaseEntity`) |

---

### Когда DIP — избыточен

Честно: не для каждого класса нужен интерфейс.

- **Один реализатор, который никогда не поменяется** (например, `LoggerInterface` от Symfony — вы не будете писать свой PSR-3 логгер) — можно зависеть от класса напрямую.
- **Тест не нужен** (prototype, скрипт) — можно и без DI.
- **Один метод, одна реализация, стабильный** — интерфейс создаёт шум.

Правило, которое я применяю: **интерфейс нужен, когда есть (а) второй реализатор, или (б) потребность в mock'е, или (в) граница между слоями (домен ↔ инфраструктура)**. Если ни одно из трёх не выполняется — не создавайте интерфейс ради SOLID-а.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Да, можно. SOLID — эвристики, а не закон

SOLID описывает **направления**, в которых код обычно становится лучше. Но в конкретных ситуациях следование принципу создаёт больше проблем, чем решает. Ниже — два примера из реальной практики.

---

### Пример 1: SRP — инварианты внутри aggregate

**Пуританский SRP:**

> «О класс — одна ответственность. `Order` хранит данные. `OrderValidator` валидирует. `OrderState` управляет переходами. Три класса.»

```php
// "Чистый" SRP
final class Order
{
    public function __construct(
        public int    $id,
        public int    $customerId,
        public int    $totalCents,
        public string $status,
    ) {}
}

final class OrderValidator
{
    public function assertCanShip(Order $order): void
    {
        if ($order->status !== 'paid') {
            throw new OrderCannotBeShippedException($order->id);
        }
        if ($order->totalCents <= 0) {
            throw new InvalidOrderAmountException($order->totalCents);
        }
    }
}

final class OrderState
{
    public function transition(Order $order, string $newStatus): Order
    {
        $allowed = [
            'paid'      => ['new', 'cancelled'],
            'shipped'   => ['paid'],
            'cancelled' => ['new', 'paid'],
        ];

        if (!in_array($order->status, $allowed[$newStatus] ?? [], true)) {
            throw new InvalidTransitionException($order->status, $newStatus);
        }

        return new Order($order->id, $order->customerId, $order->totalCents, $newStatus);
    }
}
```

**Проблема:** между `Order` и `OrderValidator` появилась **щель**. Кто-то может создать `Order` с `totalCents = -500`, потому что валидация — в другом классе:

```php
$order = new Order(1, 42, -500, 'new'); // ← прошло, валидатор не вызывался
```

Теперь вы вынуждены:
- В каждом месте создания заказа дублировать вызов `validator->assert...`
- Либо делать «магический» конструктор, который внутри вызывает validator — что возвращает логику в класс
- Либо надеяться на код-ревью

**Оправданное нарушение:**

```php
final class Order
{
    private function __construct(
        public readonly int    $id,
        public readonly int    $customerId,
        public readonly int    $totalCents,
        public readonly OrderStatus $status,
    ) {}

    public static function new(int $customerId, int $totalCents): self
    {
        if ($totalCents <= 0) {
            throw new InvalidOrderAmountException($totalCents);
        }

        return new self(0, $customerId, $totalCents, OrderStatus::New);
    }

    public function markAsPaid(): self
    {
        if ($this->status !== OrderStatus::New) {
            throw new InvalidTransitionException($this->status->value, 'paid');
        }
        return new self($this->id, $this->customerId, $this->totalCents, OrderStatus::Paid);
    }

    public function ship(): self
    {
        if ($this->status !== OrderStatus::Paid) {
            throw new OrderCannotBeShippedException($this->id, $this->status->value);
        }
        return new self($this->id, $this->customerId, $this->totalCents, OrderStatus::Shipped);
    }
}
```

**Почему это оправдано:**

| Аргумент | Пояснение |
|----------|-----------|
| Инварианты атомарны с состоянием | Невозможно создать `Order` в невалидном состоянии. Гарантия на уровне языка, а не на уровне дисциплины. |
| Когезия выше | «Создание заказа» и «что валидно для заказа» — одна концепция. Разделение на два класса создаёт **искусственную** границу. |
| SRP не нарушен по смыслу | SRP говорит: «класс меняется по одной причине». `Order` меняется по одной причине — «что такое заказ и как он живёт». Валидация — часть того, *что такое заказ*. |
| Нет обходного пути | С `OrderValidator` — есть. С инвариантами в конструкторе — нет. |

**Когда это НЕ оправдано:** если «валидация» — это внешний контекст (например, проверка лимитов из внешнего сервиса, A/B-тесты, правила, которые часто меняются) — тогда вынос в отдельный сервис действительно уместен. Граница проходит по **степени связанности с состоянием объекта**.

---

### Пример 2: DIP — конкретный класс вместо интерфейса

**Пуританский DIP:**

> «Зависимости должны быть на абстракции. Даже если реализатор один — создайте интерфейс.»

```php
// services.yaml
services:
    App\Service\AuditLogger:
        arguments:
            $logger: 'App\Infrastructure\Audit\FileAuditLogger'

// App\Service\AuditLogger
final class AuditLogger
{
    public function __construct(
        private readonly LogWriterInterface $writer,  // ← интерфейс ради одного класса
    ) {}
}
```

```php
interface LogWriterInterface
{
    public function write(string $message): void;
}

final class FileAuditLogger implements LogWriterInterface
{
    public function write(string $message): void
    {
        file_put_contents('/var/log/audit.log', $message . PHP_EOL, FILE_APPEND);
    }
}
```

**Проблема:**

- Один метод. Один реализатор. Второй не будет (аудит пишется в файл — это не «стратегия», это требование compliance).
- Интерфейс не даёт **никакой** выгоды: ни замену, ни mock (в тестах можно пробросить `null` или `void`-объект), ни полиморфизм.
- Добавляет файл, когнитивную нагрузку, и создаёт ложное впечатление, что «завтра может появиться `KafkaAuditLogger`».

**Оправданное нарушение:**

```php
final class AuditLogger
{
    public function __construct(
        private readonly string $filePath,
    ) {}

    public function log(string $action, string $entityId): void
    {
        $line = sprintf("[%s] %s:%s\n", date('c'), $action, $entityId);
        file_put_contents($this->filePath, $line, FILE_APPEND);
    }
}
```

**Почему это оправдано:**

| Аргумент | Пояснение |
|----------|-----------|
| Нет второго реализатора | Аудит в файл — это требование регулятора. Не «стратегия». |
| Нет потребности в mock'е | Тест: проверить, что файл содержит строку. Это integration-тест на 5 строк. |
| Интерфейс создаёт ложную абстракцию | Читатель кода думает: «а, значит, бывают разные реализации». Нет. Одна. |
| DIP говорит «зависимости на абстракции» | Абстракция — это не обязательно интерфейс. `string $filePath` — это и есть абстракция от конкретного пути. |

**Когда это НЕ оправдано:**

- Если завтра **реально** появится вторая реализация (Kafka, Elasticsearch) — интерфейс уже нужен.
- Если класс делает сетевой вызов и вы хотите тестировать без сети — нужен интерфейс (или хотя бы `callable`).
- Если класс находится на **границе слоёв** (домен зависит от него) — интерфейс обязателен, иначе домен «знает» про инфраструктуру.

---

### Общий принцип: когда нарушение оправдано

```
Нарушение SOLID оправдано, когда:

1. Следование принципу создаёт больше кода, а не меньше
   → SRP: вынесение валидации в отдельный класс = +2 файла, -0 гарантий

2. Следование принципу создаёт ложные границы
   → DIP: интерфейс на один метод = ложное впечатление «стратегии»

3. Следование принципу ослабляет инварианты
   → SRP: разделение данных и логики = возможность создать невалидный объект

4. Следование принципу увеличивает время до результата
   → Все: если вы в prototype и через 2 недели удалите — не тратьте на интерфейсы

Нарушение НЕ оправдано, когда:

1. Есть реальный второй реализатор (или он будет через 1–2 спринта)
2. Код находится на границе слоёв (домен ↔ инфраструктура)
3. Вы хотите (или должны) тестировать без внешних зависимостей
4. Принцип предотвращает конкретный класс багов, который вы уже ловили
```

---

### Вывод

SOLID — это **набор эвристик против конкретных антипаттернов**:
- SRP против «God class»
- OCP против «if-else на каждом enum»
- LSP против «наследование ради переиспользования кода»
- ISP против «интерфейс на 20 методов, из которых нужны 2»
- DIP против «`new ConcreteClass()` внутри бизнес-логики»

Если конкретный антипаттерн **не присутствует** — принцип не обязателен. Создавать интерфейс «на всякий случай», разделять класс на два «ради SRP», добавлять метод в интерфейс «на будущее» — это **догматизм**, и он вредит коде больше, чем «нарушение SOLID».

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## OCP без наследования

OCP требует: **добавить новое поведение, не меняя существующий код**. Наследование — один из способов, но далеко не единственный. Ниже — от простого к сложному.

---

### 1. Стратегия как объект (композиция)

Классический способ, где полиморфизм заменяется **передачей объекта с методом**.

```php
// Конкретные стратегии — плоские классы, без общего родителя
final class RoundPercent
{
    public function apply(float $value): float
    {
        return round($value * 100, 2);
    }
}

final class RoundToNearest
{
    public function apply(float $value): float
    {
        return (float) round($value);
    }
}

// Потребитель — не знает про классы, знает только «у объекта есть ->apply()»
final class ReportBuilder
{
    public function __construct(
        private readonly object $percentStrategy, // ← object, не интерфейс
    ) {}

    public function format(float $value): string
    {
        return $this->percentStrategy->apply($value) . '%';
    }
}
```

Добавление `RoundToEven` — новый файл, ноль правок в `ReportBuilder`.

**Почему это OCP:** `ReportBuilder` закрыт для модификации, открыт для расширения (новые стратегии).

**Ограничение:** у каждого нового поведения должен быть метод `apply`. Если сигнатуры разные — подход не работает.

---

### 2. Callable / Closure (максимальная гибкость)

```php
final class TaxCalculator
{
    public function __construct(
        private readonly \Closure $calculate,
    ) {}

    public function compute(int $amountCents): int
    {
        return ($this->calculate)($amountCents);
    }
}

// Разные контексты — разные поведения
$russia  = new TaxCalculator(fn(int $a) => (int) ($a * 1.20));
$belarus = new TaxCalculator(fn(int $a) => (int) ($a * 1.10));
$usa     = new TaxCalculator(
    fn(int $a) => $a < 50_000
        ? (int) ($a * 1.07)
        : (int) ($a * 1.12)
);
```

Добавление новой страны — одна строка, ноль новых файлов.

**Когда уместно:** поведение — короткая формула, не требует состояния, не нужно переиспользовать в нескольких местах.

**Когда нет:** поведение > 10 строк, есть побочные эффекты (сетевые вызовы, логирование) — Closure становится непрозрачным.

---

### 3. Registry / Map (data-driven)

Поведение определяется **данными**, а не кодом.

```php
final class NotificationChannel
{
    private array $handlers = [];

    public function register(string $channel, \Closure $handler): void
    {
        $this->handlers[$channel] = $handler;
    }

    public function send(string $channel, array $payload): void
    {
        if (!isset($this->handlers[$channel])) {
            throw new UnknownChannelException($channel);
        }

        ($this->handlers[$channel])($payload);
    }
}

// Регистрация — в одном месте, например в Service Provider
$notifier = new NotificationChannel();
$notifier->register('email', fn($p) => $this->emailClient->send($p));
$notifier->register('sms',   fn($p) => $this->smsClient->send($p));
$notifier->register('push',  fn($p) => $this->pushClient->send($p));
```

Добавление `telegram` — одна строка регистрации. Ноль правок в `NotificationChannel`.

**Когда уместно:** множество простых обработчиков, каждый с одинаковой сигнатурой. Каналы, статусы, типы событий.

**Когда нет:** поведение сложное, требует DI, тестирования, наследуемого состояния.

---

### 4. Event / Observer (расширение без знания о существующих подписчиках)

```php
final class EventDispatcher
{
    private array $listeners = [];

    public function on(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(string $event, object $payload): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener($payload);
        }
    }
}

// Ядро не знает о подписчиках
final class OrderPlaced
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $totalCents,
    ) {}
}

// Кто-то подписывается — ядро не меняется
$dispatcher->on('order.placed', function (OrderPlaced $e) {
    // отправка email
});

$dispatcher->on('order.placed', function (OrderPlaced $e) {
    // запись в аналитику
});

$dispatcher->on('order.placed', function (OrderPlaced $e) {
    // обновление инвентаря
});
```

Добавление нового слушателя — новый файл/класс, ноль правок в `OrderPlaced` и `EventDispatcher`.

**Когда уместно:** множество независимых реакций на одно событие, порядок не важен, подписчики не должны знать друг о друге.

**Когда нет:** нужно строго контролировать порядок, нужен возврат значения, нужны трансформации (pipeline).

---

### 5. Pipeline / Chain (упорядоченная обработка)

```php
final class Pipeline
{
    private array $stages = [];

    public function pipe(callable $stage): self
    {
        $this->stages[] = $stage;
        return $this;
    }

    public function process(mixed $data): mixed
    {
        foreach ($this->stages as $stage) {
            $data = $stage($data);
        }
        return $data;
    }
}

// Конвейер обработки заказа
$processor = (new Pipeline())
    ->pipe(fn($order) => $this->validate($order))
    ->pipe(fn($order) => $this->applyDiscounts($order))
    ->pipe(fn($order) => $this->calculateTax($order))
    ->pipe(fn($order) => $this->chargePayment($order))
    ->pipe(fn($order) => $this->sendConfirmation($order));
```

Добавление этапа — вставка одной строки в цепочку. Существующие этапы не изменяются.

**Когда уместно:** последовательная трансформация, каждый этап зависит от результата предыдущего.

**Отличие от Event:** порядок важен, результат передаётся дальше.

---

### 6. Configuration-driven behavior (данные вместо кода)

```php
// config/discounts.yaml
discounts:
    new_customer:
        percent: 10
        max_amount_cents: 5000
    loyal_customer:
        percent: 20
        min_orders: 5
    seasonal:
        percent: 15
        valid_from: '2025-11-01'
        valid_to: '2025-12-31'

final class DiscountEngine
{
    /** @param array<string, array{percent: int, max_amount_cents?: int, min_orders?: int}> $rules */
    public function __construct(
        private readonly array $rules,
    ) {}

    public function apply(array $customerContext): int
    {
        foreach ($this->rules as $name => $rule) {
            if ($this->matches($name, $rule, $customerContext)) {
                return $this->calculateDiscount($rule, $customerContext);
            }
        }
        return 0;
    }

    private function matches(string $name, array $rule, array $ctx): bool
    {
        // Универсальная логика матчинга по данным
        if (isset($rule['min_orders']) && ($ctx['order_count'] ?? 0) < $rule['min_orders']) {
            return false;
        }
        return true;
    }

    private function calculateDiscount(array $rule, array $ctx): int
    {
        $discount = (int) ($ctx['total_cents'] * $rule['percent'] / 100);
        if (isset($rule['max_amount_cents'])) {
            $discount = min($discount, $rule['max_amount_cents']);
        }
        return $discount;
    }
}
```

Добавление нового типа скидки — правка YAML, ноль правок PHP-кода.

**Когда уместно:** правила часто меняются (маркетинг, pricing), логика выбора простая и однородная.

**Когда нет:** правила требуют сложной логики (условные ветвления, внешние вызовы) — данные не выразят.

---

### 7. Attribute-based registration (PHP 8.1+)

```php
#[Handler]
final class EmailHandler
{
    public function handle(Notifiable $notifiable): void { /* ... */ }
}

#[Handler]
final class SmsHandler
{
    public function handle(Notifiable $notifiable): void { /* ... */ }
}

// Авто-обнаружение и регистрация
final class HandlerRegistry
{
    /** @var array<class-string, object> */
    private array $handlers = [];

    public function discover(iterable $classes): void
    {
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            if ($reflection->getAttributes(Handler::class) !== []) {
                $this->handlers[$class] = new $class();
            }
        }
    }
}
```

Добавление нового обработчика — новый файл с атрибутом. Регистрация автоматическая. Наследование не требуется.

**Когда уместно:** plugin-системы, модули, расширения.

---

### Сводка: какой подход выбрать

| Подход | Сложность поведения | Изменяемость | Тестируемость | Пример |
|--------|--------------------:|:------------:|:-------------:|--------|
| Стратегия (объект) | Средняя | Высокая | Высокая | Способы оплаты |
| Closure | Низкая | Высокая | Средняя | Формулы, простые трансформации |
| Registry / Map | Низкая | Высокая | Высокая | Каналы, статусы |
| Event / Observer | Разнообразная | Максимальная | Высокая | Реакции на доменные события |
| Pipeline | Разнообразная | Средняя | Высокая | Обработка запросов |
| Configuration | Низкая (однородная) | Максимальная (без деплоя) | Средняя | Правила скидок, лимиты |
| Attributes | Средняя | Высокая | Высокая | Plugin-системы |

---

### Главное

OCP — это не «используй полиморфизм». Это: **точка расширения отделена от точки использования**. Наследование — один из способов разделить. Composition, данные, события, pipeline — другие. Выбор зависит от того, **что меняется** и **как часто**.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — про любую единицу кода

SRP (из определения Robert C. Martin): *«There should never be more than one reason for a class to change»*. Слово *class* здесь — не буквально. Принцип формулируется через «reason to change», а «reason to change» у любого кода: функции, файла, сервиса, даже модуля.

---

### Уровень 1: Функция

**Нарушение — одна функция, две причины меняться:**

```php
function sendOrderConfirmation(int $orderId): void
{
    // Причина 1: бизнес-логика (что входить в письмо)
    $order = $this->repo->find($orderId);
    $lines = array_map(
        fn($item) => sprintf("%d x %s — %d ₽", $item['qty'], $item['name'], $item['price']),
        $order['items']
    );
    $body = implode("\n", $lines);

    // Причина 2: канал доставки (email, SMS, push)
    $to = $order['email'];
    mail($to, "Order #{$orderId}", $body);
}
```

- Меняем формат письма → трогаем функцию.
- Меняем канал с `mail()` на SendGrid → трогаем **ту же** функцию.
- Добавляем SMS параллельно → функция растёт, а «reason to change» — уже два.

**После:**

```php
function composeOrderConfirmation(int $orderId): string
{
    $order = $this->repo->find($orderId);
    $lines = array_map(
        fn($item) => sprintf("%d x %s — %d ₽", $item['qty'], $item['name'], $item['price']),
        $order['items']
    );
    return implode("\n", $lines);
}

// Канал — отдельная единица
$mailer->send($order['email'], "Order #{$orderId}", $this->composeOrderConfirmation($orderId));
```

Две функции, две причины меняться, каждая по одной.

---

### Уровень 2: Файл

**Нарушение — один файл, три ответственности:**

```php
// src/Order/Order.php (400 строк)

class Order { /* сущность */ }

class OrderRepository { /* доступ к БД */ }

class OrderFormatter { /* форматирование в JSON для API */ }
```

- Меняем схему БД → правим файл.
- Меняем формат API-ответа → правим **тот же** файл.
- Добавляем новый статус заказа → правим **тот же** файл.

Три независимых причины меняться — три файла.

**После:**

```
src/Domain/Order/Order.php
src/Domain/Order/OrderRepositoryInterface.php
src/Infrastructure/Persistence/DoctrineOrderRepository.php
src/Api/Dto/OrderResponse.php
```

---

### Уровень 3: Сервис / модуль

**Нарушение — «бог-сервис», который делает всё:**

```php
final class UserService
{
    public function register(array $data): User { /* ... */ }
    public function verifyEmail(User $user): void { /* ... */ }
    public function sendWelcomeEmail(User $user): void { /* ... */ }
    public function calculateSubscriptionPrice(User $user): int { /* ... */ }
    public function chargeFirstPayment(User $user): void { /* ... */ }
    public function createAuditLog(string $action, User $user): void { /* ... */ }
}
```

Шесть причин меняться:
1. Логика регистрации меняется (валидация, хеширование)
2. Email-верификация меняется (токен, срок)
3. Текст welcome-письма меняется (маркетинг)
4. Прайсинг меняется (бизнес)
5. Оплата меняется (провайдер)
6. Формат лога меняется (compliance)

Каждая причина — отдельный модуль:

```
src/Domain/User/User.php
src/Domain/User/UserRepositoryInterface.php
src/Application/RegisterUserCommand.php
src/Application/VerifyEmailCommand.php
src/Infrastructure/Notification/WelcomeNotifier.php
src/Domain/Billing/PricingCalculator.php
src/Infrastructure/Payment/FirstPaymentGateway.php
src/Infrastructure/Audit/AuditLogger.php
```

---

### Уровень 4: Приложение / сервис

**Нарушение — монолит, где «reason to change» = разные команды:**

Один PHP-репозиторий содержит:
- REST API для клиентов
- Webhook-обработчики для Stripe
- Cron-задачи для отчётов
- Внутренний админ-интерфейс

Четыре команды правят один репозиторий, сталкиваются в merge conflicts, деплоят друг друга.

**После:** микросервисы, или хотя бы monorepo с независимыми deploy-юнитами.

---

### Как проверить: тест одной причины

Вопрос к любой единице кода:

> **«Если <конкретное изменение> случится, этот код придётся менять?»**

Если ответ «да» на **более чем один** независимый вопрос — SRP нарушен.

| Вопрос | Функция `sendOrderConfirmation` | Файл `Order.php` (400 строк) | Сервис `UserService` |
|--------|:---:|:---:|:---:|
| Изменили текст письма? | Да | — | Да |
| Изменили канал доставки? | Да | — | Да |
| Изменили схему БД? | — | Да | — |
| Изменили формат API? | — | Да | — |
| Изменили прайсинг? | — | — | Да |
| Изменили провайдера оплаты? | — | — | Да |

Колонки с двумя и более «да» — нарушение SRP на этом уровне.

---

### Кратко

| Уровень | Что проверяем | Пример нарушения |
|---------|--------------|-----------------|
| Функция | Больше одной причины меняться в теле функции | Валидация + сохранение + email в одном методе |
| Класс | Класс меняют по двум независимым причинам | `Order` хранит данные И форматирует JSON |
| Файл | В файле код, который меняется по разным причинам | Entity + Repository + Formatter в одном файле |
| Сервис / модуль | Сервис содержит логику из разных доменов | `UserService` + `BillingService` + `AuditService` |
| Приложение | Один деплой-юнит содержит независимые причины меняться | API + cron + webhooks в одном репо |

SRP — про **причину изменения**, а не про «размер класса». Малый класс может нарушать SRP (две причины в 30 строках), а большой — не нарушать (одна причина на 500 строк, например, сложная доменная сущность с инвариантами).

> ⏱ 28.2s · prompt 8906 tok · output 3989 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о внутреннем устройстве DI-контейнера Symfony: компиляция, autowiring, lazy-инстанциация. Это вопрос на уровне «знаешь ли ты internals, а не только configuration». Нужно ответить структурно, с конкретикой про то, что реально происходит в коде Symfony (CompilerPass, Definition, Reference, alias, lazy proxy).

Структура ответа:
1. Три фазы: compile-time, runtime (первый запрос), runtime (повторный запрос)
2. Compile-time: как контейнер собирается из YAML/XML/PHP + attributes, что делает CompilerPass, что такое Definition, Reference, alias
3. Autowiring: как работает — автопоиск по типу, что происходит при неоднозначности, как `!` prefix работает
4. Runtime: первый запрос — что делает Container::get(), как создаётся proxy для lazy-сервисов, как создаётся реальный объект
5. Повторный запрос — singleton vs prototype scope
6. Практические нюансы: synthetic services, private vs public, removing unused

Напишу с примерами кода Symfony-интернов (Definition, Reference, Alias), покажу flow. Не буду абстрактно — покажу, что реально в сгенерированном Container*.php.

</details>

**Кандидат:**

## DI-контейнер Symfony: internals

Контейнер живёт в **два этапа**: compile-time (сборка) и runtime (запросы). Разберу оба.

---

### 1. Compile-time: сборка контейнера

#### 1.1. Источники конфигурации

Symfony собирает конфигурацию из:

```
config/services.yaml
config/services_*.yaml
config/packages/*.yaml
config/packages/*.xml
src/ (attributes: #[Autoconfigure], #[AsCommand], #[AsMessageHandler], …)
```

Каждый источник описывает **Definition** (для сервисов) или **Alias** (для публичных имён).

```php
// Упрощённо, из Symfony\Component\DependencyInjection
final class Definition extends ChildDefinition
{
    private string $class;
    private array $arguments = [];   // [string|int => ArgumentInterface|string|Definition|Reference]
    private array $calls = [];       // setters
    private array $tags = [];
    private ?string $public = null;
    private ?string $shared = null;  // true | false | 'prototype'
    // ...
}
```

`Definition` — это **описание**: «какой класс, какие аргументы, какие методы вызвать после конструктора, какие теги».

#### 1.2. CompilerPass — трансформации

После загрузки всех Definition'ов контейнер запускает цепочку **CompilerPass**. Каждый pass — это «правило трансформации графа зависимостей».

Ключевые pass (в порядке исполнения):

| Pass | Что делает |
|------|-----------|
| `RegisterServiceSubscribersPass` | Связывает `event`-теги с listener'ами |
| `AutowirePass` | Заполняет аргументы конструктора по типам |
| `ResolveBindingsPass` | Применяет `!` bindings из `services.yaml` |
| `ResolveChildDefinitionsPass` | Разрешает `parent:` наследование |
| `ResolveReferencesToAliasesPass` | Заменяет alias → целевой сервис |
| `InlineServiceDefinitionsPass` | Инлайнит private сервисы с одним потребителем |
| `RemoveUnusedDefinitionsPass` | Удаляет несыреференцированные private сервисы |
| `CheckDefinitionValidityPass` | Валидирует граф (циклы, отсутствующие зависимости) |

**Порядок важен.** Например, `AutowirePass` работает **до** `RemoveUnusedDefinitionsPass`, потому что autowiring добавляет референсы, а `RemoveUnused` их использует.

#### 1.3. Autowiring — как работает

`AutowirePass` делает следующее для каждого Definition:

1. Берёт `ReflectionClass` целевого класса.
2. Смотрит конструктор: `__construct(OrderRepositoryInterface $repo, LoggerInterface $logger)`.
3. Для каждого параметра с типом-классом ищет в контейнере:
   - **Точное совпадение** по FQCN → подставляет `Reference`.
   - **Alias** → подставляет `Reference` на alias.
   - **Несколько кандидатов** → ищет тот, у которого нет `exclude` или который помечен `default` (через `!` binding).
   - **Ноль кандидатов** → бросает `ServiceNotFoundException` на этапе компиляции.
4. Для параметров без типа (строки, int) ищет по **bindings** (`!` prefix) или по **named arguments** (`$name`).

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    # Явный binding — переопределяет autowiring
    App\Service\OrderService:
        arguments:
            $paymentGateway: '@App\Infrastructure\Payment\StripeGateway'

    # Исключение из autowiring
    App\Infrastructure\Payment\StripeGateway:
        tags:
            - { name: 'payment.gateway' }

    # Фирма — все реализации через тег
    App\Application\PaymentHandler:
        arguments:
            $gateways: !tagged_iterator 'payment.gateway'
```

**Важный нюанс:** autowiring — это **compile-time** механизм. После компиляции в сгенерированном PHP-коде уже нет «магии» — аргументы жёстко заданы.

#### 1.4. Что генерируется

В `var/cache/prod/App_KernelProdContainer.php` (примерно 500–2000 строк) вы увидите:

```php
// Упрощённо из сгенерированного контейнера
protected function getA_b_c_OrderService(): \App\Service\OrderService
{
    $container = $this;

    $instance = new \App\Service\OrderService(
        $container->getA_b_c_Repo(),       // ← уже конкретный сервис
        $container->getA_b_c_Logger(),
    );

    $instance->setSomething($container->getA_b_c_Something()); // ← call

    return $instance;
}

protected function getA_b_c_Repo(): \App\Infrastructure\Persistence\DoctrineOrderRepository
{
    return new \App\Infrastructure\Persistence\DoctrineOrderRepository(
        $this->getA_b_c_Eman(),
    );
}
```

Каждый private сервис — свой protected-метод. Public — публичный `get()`. Alias — строка в `private $aliases`.

---

### 2. Runtime: первый запрос (`$container->get(OrderService::class)`)

#### 2.1. Что происходит

```
Container::get('App\Service\OrderService')
  │
  ├── 1. public? → вызов $this->getA_b_c_OrderService()
  │       private? → только если вlined в потребителя, иначе не доступен
  │
  ├── 2. Внутри метода:
  │       - new \App\Service\OrderService(
  │           $this->getA_b_c_Repo(),   ← рекурсивный запрос
  │           $this->getA_b_c_Logger(),
  │       );
  │
  ├── 3. Для lazy-сервисов:
  │       - не new, а создание \Symfony\Component\DependencyInjection\Proxy\LazyProxy
  │       - прокси перехватывает первые метод-вызовы и ТОЛЬГОТДА создаёт объект
  │
  └── 4. Возврат объекта
```

#### 2.2. Singleton (shared) scope

По умолчанию `shared: true`. Это значит:

```php
// Первый запрос
$a = $container->get(OrderService::class);  // создаёт

// Второй запрос
$b = $container->get(OrderService::class);  // возвращает ТОТ ЖЕ экземпляр
```

Реализация: в сгенерированном коде есть private-поле:

```php
private $a_b_c_OrderService = null;

protected function getA_b_c_OrderService(): \App\Service\OrderService
{
    if (null !== $this->a_b_c_OrderService) {
        return $this->a_b_c_OrderService;  // ← cached
    }

    $this->a_b_c_OrderService = new \App\Service\OrderService(/* ... */);
    return $this->a_b_c_OrderService;
}
```

**Последствие:** singleton-сервисы **не должны** хранить состояние между запросами. Если хранят — баг.

#### 2.3. Prototype scope

```yaml
services:
    App\Entity\User:
        shared: false   # каждый ->get() создаёт новый экземпляр
```

В сгенерированном коде нет кэша:

```php
protected function getA_b_c_User(): \App\Entity\User
{
    return new \App\Entity\User(/* ... */);
}
```

Используется для Doctrine Entity'ей и других stateful объектов.

#### 2.4. Lazy-сервисы

```yaml
services:
    App\Infrastructure\Payment\StripeGateway:
        lazy: true
```

Вместо `new StripeGateway(...)` контейнер возвращает **прокси**:

```php
protected function getA_b_c_StripeGateway(): \App\Infrastructure\Payment\StripeGateway
{
    $class = \App\Infrastructure\Payment\StripeGateway::class;

    // Возвращает прокси, который:
    // 1. Перехватывает ->charge(), ->refund() и т.д.
    // 2. При ПЕРВОМ вызове метода — создаёт реальный объект
    // 3. Прокидывает вызов на реальный объект
    return \Symfony\Component\DependencyInjection\Proxy\LazyProxyFactory
        ::createProxy($class, function () {
            return $this->getA_b_c_StripeGatewayReal();
        });
}
```

**Почему это важно:** если сервис тяжёлый (создаёт HTTP-клиент, подключение к БД, WebSocket), он **не создаётся**, пока на него не вызовут метод. Контейнер может содержать 500 сервисов, а реально в памяти будет 20.

**Ограничение:** прокси не работает для:
- Финальных классов (не может быть подкласса)
- Методов, возвращающих `static`
- Интерфейсов с generic-типами (пока нет native generics)

---

### 3. Runtime: повторный запрос

```php
$first = $container->get(OrderService::class);   // t=0
$first->doSomething();

$second = $container->get(OrderService::class);  // t=100
```

**shared: true** (default): `$first === $second` → `true`. Внутренний кэш в поле контейнера.

**shared: false**: `$first === $second` → `false`. Каждый раз — `new`.

**synthetic service**: не создаётся контейнером. Задается снаружи:

```yaml
services:
    request:
        synthetic: true
```

```php
// В Controller / Listener
$container->set('request', $request);
```

Используется для `Request`, `Session` и других объектов, которые контейнер не может сам создать.

---

### 4. Практические нюансы

#### 4.1. Private vs Public

```yaml
services:
    _defaults:
        public: false   # ← default с Symfony 4
```

- **Private**: доступен только другим сервисам через DI. Внешне — `Container::get()` бросит `ServiceNotFoundException`.
- **Public**: доступен из любого кода через `Container::get()`.

Почему default `false`:
1. **Inlination**: если private-сервис используется одним потребителем, он инлайнится в него и **не создаётся** как отдельный объект. Экономия памяти и времени.
2. **Удаление**: если никто не использует — `RemoveUnusedDefinitionsPass` его удаляет из контейнера.
3. **Безопасность**: случайно `Container::get()` не создаст сервис, который «забыли» сделать public.

#### 4.2. Циклические зависимости

```
A → B → C → A
```

Symfony разрешает **только** через lazy-прокси или setter-инъекцию:

```yaml
services:
    A:
        calls:
            - ['setB', ['@B']]   # ← setter, не конструктор

    B:
        calls:
            - ['setC', ['@C']]

    C:
        calls:
            - ['setA', ['@A']]
```

Конструктор-цикл — ошибка компиляции.

#### 4.3. `!tagged_iterator` и `!tagged_locator`

```yaml
services:
    App\PaymentHandler:
        arguments:
            $gateways: !tagged_iterator 'payment.gateway'
            # → массив всех сервисов с тегом 'payment.gateway'

    App\NotificationDispatcher:
        arguments:
            $channels: !tagged_locator 'notification.channel'
            # → Locator (ArrayLoader), ленивая загрузка
```

Это механизм plugin-систем: регистрация через тег, итерация/выбор по имени в рантайме.

---

### Сводка: lifecycle

```
┌─────────────────────────────────────────────────────────┐
│ COMPILE TIME (один раз, при деплое / первой загрузке)   │
│                                                         │
│  services.yaml → Definition'ы                           │
│       ↓                                                 │
│  CompilerPass'ы (Autowire, Bind, Inline, Remove)       │
│       ↓                                                 │
│  var/cache/prod/App_KernelProdContainer.php             │
│  (500–2000 строк, чистый PHP, без reflection)          │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ RUNTIME (каждый запрос)                                 │
│                                                         │
│  $container->get('App\Service\OrderService')            │
│       ↓                                                 │
│  1. shared? → return cached                             │
│  2. lazy?   → return proxy (реальный obj при 1-м call) │
│  3. иначе   → new + arguments (рекурсивно)              │
│       ↓                                                 │
│  4. setter calls                                        │
│       ↓                                                 │
│  5. return object                                       │
└─────────────────────────────────────────────────────────┘
```

Ключевая мысль: **магия** (autowiring, autotagging) происходит **один раз** на этапе компиляции. В рантайме — чистый PHP без reflection, без магии. Это и есть причина, почему Symfony быстрый.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Циклические зависимости: диагностика и разруление

### Сначала — честный вопрос

Циклическая зависимость в 90% случаев — **признак того, что один из сервисов делает больше, чем должен**. Перед тем как применять «техническое» разруление, попробуйте задать вопрос:

> «Если бы я не мог использовать DI-трюки, как бы я разделил эти два класса?»

Часто ответ находится сам. Но есть ситуации, где цикл — легитимный (например, `Order` и `Payment` genuinely взаимно влияют). Ниже — инструменты от самого правильного к самому прагматичному.

---

### 1. Экстракция общего в третий сервис (рефакторинг)

Самое правильное решение. Если A → B → A, значит, в B есть логика, которая **не про B**, а про то, что B делает для A.

**До:**

```php
final class OrderService
{
    public function __construct(
        private readonly PaymentService $payments,
    ) {}

    public function confirm(Order $order): void
    {
        $this->payments->charge($order); // A → B
    }
}

final class PaymentService
{
    public function __construct(
        private readonly OrderService $orders,
    ) {}

    public function charge(Order $order): void
    {
        // ...
        $this->orders->markAsPaid($order); // B → A
    }
}
```

**После:**

```php
final class OrderService
{
    public function __construct(
        private readonly PaymentService $payments,
        private readonly OrderStateTransition $transitions, // ← вынесено
    ) {}

    public function confirm(Order $order): void
    {
        $this->payments->charge($order);
        $this->transitions->toPaid($order);
    }

    public function markAsPaid(Order $order): void
    {
        $this->transitions->toPaid($order);
    }
}

final class PaymentService
{
    public function __construct(
        private readonly OrderStateTransition $transitions, // ← не OrderService
    ) {}

    public function charge(Order $order): void
    {
        // ...
        $this->transitions->toPaid($order); // ← зависимость на узкую абстракцию
    }
}

final class OrderStateTransition
{
    public function toPaid(Order $order): void
    {
        // смена статуса, сохранение, event
    }
}
```

**Почему это работает:** цикл был потому, что `PaymentService` знал про `OrderService` целиком. На самом деле ему нужна только «сменить статус на paid». Это — узкая абстракция, которая не зависит от `PaymentService`.

**Когда уместно:** один из сервисов использует **один-два метода** другого. Значит, можно вынести их в отдельный класс.

---

### 2. Event-driven: replace dependency with broadcast

Если A нужно «реagir» на действие B, но A не должен знать про B — это событие.

**До:**

```php
// OrderService вызывает PaymentService, который вызывает OrderService
```

**После:**

```php
final class PaymentService
{
    public function __construct(
        private readonly EventDispatcherInterface $events,
    ) {}

    public function charge(Order $order): void
    {
        // ... оплата ...

        $this->events->dispatch(new PaymentCompleted($order->id, $result));
        // ← не вызывает OrderService напрямую
    }
}

// Отдельный listener, не знает про PaymentService
final class MarkOrderPaidListener
{
    public function __construct(
        private readonly OrderStateTransition $transitions,
    ) {}

    public function onPaymentCompleted(PaymentCompleted $event): void
    {
        $this->transitions->toPaid($event->orderId);
    }
}
```

**Преимущества:**
- `PaymentService` не знает ни про `OrderService`, ни про `OrderStateTransition`.
- Можно добавить второго слушателя (аналитика, notification) без правки `PaymentService`.
- Цикл полностью исчезает.

**Ограничения:**
- Потеря sync-гарантии: `charge()` вернётся **до** того, как `toPaid()` выполнится. Если порядок критичен — не подходит.
- Отладка сложнее: «почему статус не сменился?» — надо искать listener.
- Не подходит для случаев, где B **возвращает значение** A (например, `PaymentService` должен получить `orderId` от `OrderService` в том же call stack).

**Когда уместно:** реакция B на A не должна блокировать A, результат B не нужен A синхронно.

---

### 3. Setter injection (break the cycle)

Один из двух сервисов получает зависимость **после** конструктора.

```php
final class OrderService
{
    private ?PaymentService $payments = null;

    public function setPayments(PaymentService $payments): void
    {
        $this->payments = $payments;
    }

    public function confirm(Order $order): void
    {
        $this->payments->charge($order); // ← null до инициализации!
    }
}

final class PaymentService
{
    public function __construct(
        private readonly OrderService $orders, // ← конструктор, ок
    ) {}

    public function charge(Order $order): void
    {
        // ...
        $this->orders->markAsPaid($order); // ← работает, OrderService уже создан
    }
}
```

Конфигурация:

```yaml
services:
    App\Service\OrderService:
        calls:
            - ['setPayments', ['@App\Service\PaymentService']]
```

**Почему работает:** при компиляции Symfony строит DAG (directed acyclic graph). Конструкторы — рёбра графа. Цикл в рёбрах → ошибка. Setter — **не ребро**, а «после-действие». Граф остаётся ациклическим.

**Риски:**
- `OrderService` **неполноценен** до вызова `setPayments()`. Любой код, который вызовет `confirm()` до инициализации — NPE.
- Нарушает «конструктор = полный набор зависимостей». Новый разработчик видит пустой конструктор и думает, что `PaymentService` опционален.
- В unit-тестах легко забыть `setPayments()` → тихий `null` crash.

**Когда уместно:** цикл реальный, не разбивается рефакторингом (например, оба сервиса genuinely взаимны), и вы готовы принять риск неполной инициализации.

**Практический приём:** сделайте `confirm()` **бросать исключение**, если `payments === null`:

```php
public function confirm(Order $order): void
{
    if ($this->payments === null) {
        throw new \LogicException('PaymentService not injected. Call setPayments() first.');
    }
    $this->payments->charge($order);
}
```

---

### 4. Lazy proxy (Symfony-specific)

```yaml
services:
    App\Service\OrderService:
        lazy: true
```

Symfony генерирует **прокси-класс**, который перехватывает метод-вызовы и создаёт реальный объект только при первом вызове.

```php
// PaymentService
final class PaymentService
{
    public function __construct(
        private readonly OrderService $orders, // ← на самом деле прокси
    ) {}

    public function charge(Order $order): void
    {
        $this->orders->markAsPaid($order); // ← здесь создаётся реальный OrderService
    }
}
```

**Почему помогает:** при создании `PaymentService` контейнер не вызывает конструктор `OrderService` (который сам требует `PaymentService`). Вместо этого он передаёт прокси. Реальный `OrderService` создаётся только при первом `$this->orders->...`.

**Критическое ограничение:** прокси **не работает** для:
- Финальных классов (`final class OrderService`)
- Классов с `readonly`-свойствами в конструкторе (некоторые версии)
- Методов с `static` return type

**Когда уместно:** один из сервисов — не-финальный, вы не хотите рефакторить, и цикл «разрешается» ленивым созданием.

**Почему это плохое решение по умолчанию:** вы прячете проблему, а не решаете. Через 6 месяцев кто-то сделает класс `final` (что правильно для DDD-агрегатов) — и всё сломается.

---

### 5. Service Locator (точечный)

Если цикл — единственный в проекте, и рефакторинг дороже:

```php
use Symfony\Contracts\Service\ServiceLocatorTrait;

final class OrderService
{
    public function __construct(
        private readonly ServiceLocator $locator, // ← не весь контейнер, а локактор
    ) {}

    public function confirm(Order $order): void
    {
        /** @var PaymentService $payments */
        $payments = $this->locator->get(PaymentService::class);
        $payments->charge($order);
    }
}

final class PaymentService
{
    public function __construct(
        private readonly OrderService $orders, // ← конструктор, ок
    ) {}
}
```

```yaml
services:
    App\Service\OrderService:
        arguments:
            $locator: !service_locator
                - 'App\Service\PaymentService'
```

**Почему лучше, чем `ContainerInterface`:**
- Локактор содержит **только** `PaymentService`. Не 500 сервисов.
- Если кто-то попробует `$locator->get('SomethingElse')` — `ServiceNotFoundException`.
- В unit-тестах легко подменить: `new ArrayServiceLocator(['App\Service\PaymentService' => $mock])`.

**Когда уместно:** один-два случая в проекте, рефакторинг отложен, вы хотите «закрыть» проблему локально.

**Почему не как паттерн:** если service locator используется в 20 местах — это антипаттерн «hidden dependencies». Код становится неразрывным: вы не видите из сигнатуры, от чего зависит метод.

---

### 6. Интерфейс + late binding (композиция)

```php
interface PaymentCompletable
{
    public function onPaymentCompleted(int $orderId, int $amountCents): void;
}

final class PaymentService
{
    public function __construct(
        private readonly PaymentCompletable $completer, // ← узкая абстракция
    ) {}

    public function charge(int $orderId, int $amountCents): void
    {
        // ...
        $this->completer->onPaymentCompleted($orderId, $amountCents);
    }
}

// OrderService реализует узкий интерфейс
final class OrderService implements PaymentCompletable
{
    public function onPaymentCompleted(int $orderId, int $amountCents): void
    {
        // смена статуса
    }
}
```

**Почему работает:** `PaymentService` зависит от `PaymentCompletable`, а не от `OrderService`. `OrderService` может зависеть от `PaymentService`. Цикл на уровне классов устранён, потому что зависимость теперь на уровне **интерфейса**, который `PaymentService` не «знает» как `OrderService`.

**Суть:** вы не «разрешаете» цикл, вы **уменьшаете поверхность зависимости** до одного метода. Это по сути вариант 1 (экстракция), но без создания нового класса — интерфейс живёт в `OrderService`.

**Когда уместно:** `OrderService` уже существует, вы не хотите создавать `OrderStateTransition`. Один метод, одна реализация.

---

### Матрица выбора

| Подход | Изменяем A? | Изменяем B? | Сложность | Риск | Когда |
|--------|:-----------:|:-----------:|:---------:|:----:|-------|
| Экстракция (1) | Да | Да | Низкая | Минимальный | Всегда, если можно вынести общий код |
| Event (2) | Да | Да | Средняя | Потеря sync | Реакция не блокирующая |
| Setter (3) | Да | Нет | Низкая | NPE до init | Быстрое решение, 1–2 цикла |
| Lazy (4) | Нет | Нет | Низкая | final-класс | Не-финальные, временное |
| Locator (5) | Да | Нет | Средняя | Hidden dep | 1–2 случая, отложенный рефакторинг |
| Interface (6) | Да | Да | Низкая | Минимальный | Один метод, не нужен новый класс |

---

### Мой порядок применения

```
1. Попробовать рефакторинг (1 или 6)
   → Если не удалось или дорого →

2. Event (2)
   → Если sync-порядок критичен →

3. Setter (3) + LogicException guard
   → Если классы финальные →

4. Lazy (4)
   → Если lazy не работает (final) →

5. Locator (5)
```

В 8 из 10 случаев рефакторинг (1) или event (2) решает проблему за 10–20 минут и убирает корень. Остальные варианты — когда рефакторинг невозможен в текущем спринте, и вы берёте «долг» осознанно.

> ⏱ 27.6s · prompt 18904 tok · output 3408 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт классический вопрос об асинхронной обработке post-registration side effects в Symfony. Нужно:

1. Расставить варианты от простого к сложному
2. Для каждого — trade-offs (задержки, надёжность, наблюдаемость, операционная сложность)
3. Дать конкретный код
4. В конце — рекомендация с обоснованием

Варианты:
- Synchronous (baseline — почему нет)
- Symfony Messenger + AMQP transport (основной)
- Symfony Messenger + DQL transport (для небольших нагрузок)
- Symfony Messenger + Sync transport (fallback)
- Custom job queue (Redis + worker)
- Kafka / event-driven
- HTTP webhook (outbound)
- «Тупо» async через `register_shutdown_function` / `curl` — антипаттерн, упомянуть как антипаттерн

Структура ответа:
1. Постановка проблемы (почему sync плох)
2. Варианты с кодом и trade-offs
3. Сравнительная таблица
4. Рекомендация для типичного случая
5. Что делать при ошибке (retry, DLQ, idempotency)

Код должен быть реалистичным: messenger.yaml, Message, Handler, Worker, DLQ, idempotency key.

Длина: достаточно глубокий ответ, но без воды. Код — по делу.

</details>

**Кандидат:**

## Асинхронные side effects в Symfony

### Постановка

```
Пользователь нажимает «Зарегистрироваться»
    → создаём User
    → отправляем welcome email        ← 200–800 мс (SendGrid API)
    → обновляем поисковый индекс      ← 50–200 мс (Meilisearch/Algolia)
    → отвечаем 201 Created            ← пользователь ждал ~1 с вместо 50 мс
```

Обе операции **не нужны для ответа пользователю**. Если они упадут — ответ всё равно успешный (user создан). Это идеальная кандидатура на асинхронность.

---

### Вариант 0: Синхронно (baseline, почему нет)

```php
public function register(RegisterUserCommand $cmd): User
{
    $user = $this->userFactory->create($cmd);
    $this->repo->save($user);

    // Синхронно: блокируем ответ
    $this->mailer->sendWelcome($user);      // 200–800 мс
    $this->searchIndexer->index($user);     // 50–200 мс

    return $user;
}
```

| Плюс | Минус |
|------|-------|
| Простота | Ответ замедляется на 300–1000 мс |
| Нет инфраструктуры | Падение SendGrid → падение регистрации |
| Тривиальные тесты | Падение Meilisearch → падение регистрации |
| | Нет retry — письмо потеряно |

**Когда допустимо:** prototype, трафик < 10 req/min, email не критичен.

---

### Вариант 1: Symfony Messenger + AMQP (основной)

#### Архитектура

```
Controller → RegisterUserCommand → Message Bus → RabbitMQ → Worker
                                                              ├── SendWelcomeEmailHandler
                                                              └── IndexUserHandler
```

#### 1. Сообщения

```php
// src/Message/SendWelcomeEmail.php
final class SendWelcomeEmail
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}

// src/Message/IndexUser.php
final class IndexUser
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
```

#### 2. Отправка (в команде регистрации)

```php
// src/Application/RegisterUserHandler.php
final class RegisterUserHandler
{
    public function __construct(
        private readonly UserFactory $factory,
        private readonly UserRepositoryInterface $repo,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(RegisterUserCommand $cmd): User
    {
        $user = $this->factory->create($cmd);
        $this->repo->save($user);

        // Асинхронно: сообщение уходит в очередь, метод возвращается сразу
        $this->bus->dispatch(new SendWelcomeEmail($user->id, $user->email, $user->name));
        $this->bus->dispatch(new IndexUser($user->id, $user->email, $user->name));

        return $user; // ← ответ пользователю за ~50 мс
    }
}
```

#### 3. Обработчики

```php
// src/Handler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepositoryInterface $repo,
    ) {}

    #[AsMessageHandler]
    public function __invoke(SendWelcomeEmail $message): void
    {
        // Перезагружаем пользователя — на момент обработки он мог измениться
        $user = $this->repo->find($message->userId);
        if ($user === null) {
            return; // пользователь удалили — не шлём
        }

        $this->mailer->send($user->email, 'Welcome!', $this->compose($user));
    }
}

// src/Handler/IndexUserHandler.php
final class IndexUserHandler
{
    public function __construct(
        private readonly SearchIndexerInterface $indexer,
        private readonly UserRepositoryInterface $repo,
    ) {}

    #[AsMessageHandler]
    public function __invoke(IndexUser $message): void
    {
        $user = $this->repo->find($message->userId);
        if ($user === null) {
            return;
        }

        $this->indexer->index($user);
    }
}
```

#### 4. Конфигурация

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: '%env(AMQP_DSN)%'   # amqp://guest:guest@localhost:5672/%2F
        routing:
            'App\Message\SendWelcomeEmail': async
            'App\Message\IndexUser': async
```

#### 5. Worker

```bash
# docker-compose или systemd
php bin/console messenger:consume async --limit=1000 --time-limit=3600 --memory-limit=256M
```

Для продакшена — отдельный контейнер/процесс, не в web-сервере.

#### Trade-offs

| Аспект | Оценка |
|--------|--------|
| Latency ответа | **~50 мс** (без блокировки на email/index) |
| Надёжность | **Высокая**: RabbitMQ сохраняет сообщения, при падении worker'а — message не теряется |
| Retry | Встроенный: `max_retries: 3`, `delay: 5000` (экспоненциальный backoff) |
| DLQ | Встроенный: после `max_retries` сообщение уходит в `failed_messages` |
| Идемпотентность | **На вас**: handler может получить сообщение 2 раза (at-least-once) |
| Наблюдаемость | `messenger:consume --verbose`, Prometheus metrics, dead letter table |
| Операционная сложность | Средняя: RabbitMQ (или Redis) + worker-процесс |
| Testирование | Простое: `transport_name: sync` в тест-окружении |

**Когда уместно:** основной выбор для большинства Symfony-проектов.

---

### Вариант 2: Symfony Messenger + Doctrine transport (без брокера)

```yaml
# messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'doctrine://default?queue_name=emails'
```

Сообщения лежат в **таблице БД**. Worker читает из таблицы.

| Аспект | Оценка |
|--------|--------|
| Latency | Та же |
| Надёжность | Средняя: атомарность с БД-транзакцией, но no cluster |
| Retry | Да |
| DLQ | Да |
| Нагрузка на БД | **Дополнительная**: SELECT + DELETE на каждое сообщение |
| Масштабируемость | **Ограниченная**: один worker (или несколько с `FOR UPDATE SKIP LOCKED`) |
| Операционная сложность | **Минимальная**: нет внешнего брокера |

**Когда уместно:** малый проект, 1–10 msg/sec, нет RabbitMQ/Redis, не хотите раздувать инфраструктуру.

**Когда нет:** > 50 msg/sec, несколько worker'ов, высокая доступность.

---

### Вариант 3: Symfony Messenger + Sync transport (fallback / тесты)

```yaml
# config/packages/test/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\SendWelcomeEmail': sync
            'App\Message\IndexUser': sync
```

Сообщение вызывается **синхронно в том же процессе**. Не асинхронность — это **тестовый режим**.

**Когда уместно:** integration-тесты, где вы хотите проверить, что handler работает, без настоящего брокера.

---

### Вариант 4: Redis Stream / Queue (альтернатива RabbitMQ)

```yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'redis://localhost:6379/0?stream_name=app_messages'
```

| Аспект | Оценка |
|--------|--------|
| Надёжность | Высокая (AOF persistence), но нет DLQ из коробки |
| Retry | Встроенный |
| Нагрузка | Ниже, чем Doctrine transport |
| Операционная сложность | Ниже, чем RabbitMQ |
| Масштабируемость | Хорошая (consumer groups) |

**Когда уместно:** уже есть Redis (cache/session), нет RabbitMQ, умеренная нагрузка.

---

### Вариант 5: Kafka / event-driven (высокая нагрузка, multiple consumers)

```
Controller → Kafka topic 'user.registered'
                ├── EmailService (конsumer group A)
                ├── SearchService (конsumer group B)
                └── AnalyticsService (конsumer group C)
```

| Аспект | Оценка |
|--------|--------|
| Масштабируемость | **Максимальная**: миллионы msg/sec, consumer groups |
| Надёжность | Высокая (replication, acks=all) |
| Сложность | **Высокая**: ZooKeeper/KRaft, schema registry, offset management |
| Идемпотентность | **Обязательна**: at-least-once semantics |
| Операционная сложность | Высокая: отдельный кластер |

**Когда уместно:** > 1000 msg/sec, несколько микросервисов подписаны на события, нужен replay.

**Когда нет:** один Symfony-приложение, < 100 msg/sec. Kafka — избыточна.

---

### Вариант 6: «Тупо» async через `register_shutdown_function` (антипаттерн)

```php
public function __invoke(RegisterUserCommand $cmd): User
{
    // ...
    register_shutdown_function(function () use ($user) {
        $this->mailer->sendWelcome($user);
        $this->indexer->index($user);
    });

    return $user;
}
```

| Аспект | Оценка |
|--------|--------|
| Надёжность | **Плохая**: PHP-процесс умирает → письмо теряно |
| Retry | Нет |
| Наблюдаемость | Нет (нет очереди, нет DLQ) |
| Тестирование | Невозможно (side effect в shutdown) |

**Никогда не используйте.** Это не асинхронность — это «надеюсь, PHP не умрёт».

---

### Критический вопрос: идемпотентность

At-least-once delivery означает: **message может быть обработан 2 раза**. Без идемпотентности:

- User получит 2 welcome-письма
- В поисковом индексе — дубликаты

**Решения:**

#### 1. Idempotency key в сообщении

```php
final class SendWelcomeEmail
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $name,
        public readonly string $idempotencyKey, // ← UUID
    ) {}
}
```

```php
// Отправитель
$key = hash('sha256', "welcome:{$user->id}:" . date('Y-m-d'));
$this->bus->dispatch(new SendWelcomeEmail($user->id, $user->email, $user->name, $key));
```

```php
// Обработчик
final class SendWelcomeEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepositoryInterface $repo,
        private readonly Connection $db, // ← для idempotency table
    ) {}

    public function __invoke(SendWelcomeEmail $message): void
    {
        // Проверка: уже отправляли?
        $exists = $this->db->fetchOne(
            'SELECT 1 FROM sent_messages WHERE idempotency_key = ?',
            [$message->idempotencyKey],
        );
        if ($exists !== null) {
            return; // уже обработали
        }

        $user = $this->repo->find($message->userId);
        if ($user === null) return;

        $this->mailer->send($user->email, 'Welcome!', $this->compose($user));

        // Запоминаем
        $this->db->executeStatement(
            'INSERT INTO sent_messages (idempotency_key, sent_at) VALUES (?, NOW())',
            [$message->idempotencyKey],
        );
    }
}
```

#### 2. Idempotency на уровне внешнего сервиса

SendGrid поддерживает `Idempotency-Key` header. Meilisearch — `PUT /indexes/{id}/documents` (upsert). Если внешний сервис идемпотентен — дубликаты не страшны.

#### 3. `unique` constraint + `INSERT ... ON CONFLICT DO NOTHING`

```sql
CREATE TABLE sent_messages (
    idempotency_key TEXT PRIMARY KEY,
    sent_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

```php
// Атомарная проверка + запись
$affected = $this->db->executeStatement(
    'INSERT INTO sent_messages (idempotency_key) VALUES (?) ON CONFLICT DO NOTHING',
    [$message->idempotencyKey],
);

if ($affected === 0) {
    return; // уже было
}
// ... отправляем
```

---

### Retry и DLQ

```yaml
# messenger.yaml
framework:
    messenger:
        failure_transport: failed
        routing:
            'App\Message\SendWelcomeEmail':
                transport: async
                option: { max_retries: 3, delay: 5000, multiplier: 2, max_delay: 60000 }
                # retry 1: через 5 с
                # retry 2: через 10 с
                # retry 3: через 20 с
                # после — в failed_transport
```

```bash
# Просмотр dead letters
php bin/console messenger:consume failed --limit=10

# Ручной requeue
php bin/console messenger:consume failed --limit=1

# Удаление
php bin/console messenger:consume failed --limit=10 --no-interaction
```

---

### Наблюдаемость

```php
// src/Handler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepositoryInterface $repo,
        private readonly Connection $db,
        private readonly LoggerInterface $logger, // ← добавлен
    ) {}

    public function __invoke(SendWelcomeEmail $message): void
    {
        $startedAt = microtime(true);

        try {
            // ... логика ...

            $this->logger->info('welcome_email.sent', [
                'user_id' => $message->userId,
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('welcome_email.failed', [
                'user_id' => $message->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e; // ← rethrow! иначе retry не сработает
        }
    }
}
```

**Ключевое:** `throw $e` — без этого messenger не поймёт, что произошла ошибка, и не сделает retry.

---

### Сравнительная таблица

| Критерий | Sync | Messenger+AMQP | Messenger+DB | Redis | Kafka |
|----------|:----:|:--------------:|:------------:|:-----:|:-----:|
| Latency ответа | ❌ 1 с | ✅ 50 мс | ✅ 50 мс | ✅ 50 мс | ✅ 50 мс |
| Надёжность | ❌ | ✅✅ | ✅ | ✅ | ✅✅ |
| Retry | ❌ | ✅ | ✅ | ✅ | ✅ |
| DLQ | ❌ | ✅ | ✅ | ⚠️ ручной | ✅ |
| Идемпотентность | — | ⚠️ на вас | ⚠️ на вас | ⚠️ на вас | ⚠️ на вас |
| Масштаб | — | ✅ | ❌ | ✅ | ✅✅ |
| Инфраструктура | ✅ нет | ⚠️ RabbitMQ | ✅ нет | ⚠️ Redis | ❌ кластер |
| Нагрузка на БД | — | — | ⚠️ да | — | — |
| Тестирование | ✅ | ✅ (sync mode) | ✅ | ✅ | ⚠️ |

---

### Рекомендация

**Для типичного Symfony-проекта (1–100 msg/sec):**

```
Symfony Messenger + RabbitMQ (или Redis)
+ идемпотентность через unique constraint
+ retry с backoff (3 раза, 5/10/20 с)
+ DLQ (failed transport)
+ логи в handler (started / failed / duration)
+ worker как отдельный процесс (docker-compose / k8s deployment)
```

**Для прототипа / малого проекта (< 10 msg/sec, нет брокера):**

```
Symfony Messenger + Doctrine transport
+ retry
+ идемпотентность
```

**Для высоконагруженной системы (> 1000 msg/sec, несколько сервисов):**

```
Kafka / event-driven
+ consumer groups
+ schema registry
+ идемпотентность (обязательна)
```

---

### Чего не делать

| Антипаттерн | Почему |
|-------------|--------|
| `register_shutdown_function` | Нет retry, нет DLQ, потеряно при crash |
| `curl -d ... &` в PHP | Нет контроля, нет retry, потеряно при crash |
| `set_time_limit(0)` + sync | Заблокирует worker на 1000 мс, при 100 req/s → 100 с простоя |
| 50 сообщений в одной транзакции | Падение одного → откат всех. Каждое сообщение — своя транзакция |
| Логирование `catch (\Throwable)` без rethrow | Retry не сработает, сообщение «потеряно» молча |
| Отсутствие идемпотентности | Дубликаты писем / индекса |
| Worker в web-процессе (FPM) | Конкуренция за слоты, OOM, непредсказуемая нагрузка |

> ⏱ 33.6s · prompt 25226 tok · output 4643 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему контроля доступа в Symfony: аутентификация, роли, voters, и когда что использовать. Это продолжение собеседования на PHP-бэкенд. Нужно дать структурированный ответ с кодом, показать internals Symfony security (firewall, authenticator, voter), и дать практические критерии выбора между ролями и voters.

Структура ответа:
1. Общая архитектура Symfony Security (firewall → authenticator → token → authorization)
2. Аутентификация (Authenticator, как она работает)
3. Роли (как хранятся, как проверяются)
4. Voters (как работают, пример)
5. Когда voter, а когда роли — критерии
6. Антипаттерны

Буду опираться на Symfony 6/7 (новый authenticator-based approach, без legacy FormLogin).

</details>

**Кандидат:**

## Контроль доступа в Symfony: от аутентификации до voters

### 1. Архитектура: что происходит при каждом запросе

```
HTTP Request
    │
    ▼
┌─────────────────────────────────────────────────┐
│ 1. Firewall (firewall)                         │
│    - определяет, какой security context         │
│    - выбирает authenticator                     │
│    - проверяет, есть ли token                   │
└─────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────┐
│ 2. Authentication (аутентификация)             │
│    - «Кто ты?»                                 │
│    - проверяет credentials → создаёт Token      │
└─────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────┐
│ 3. Authorization (авторизация)                 │
│    - «Можешь ли ты это сделать?»               │
│    - роли, voters, expression language          │
└─────────────────────────────────────────────────┘
    │
    ▼
Controller / Listener
```

Ключевое разделение: **аутентификация** = «кто ты», **авторизация** = «что ты можешь». Symfony смешивает их в одном компоненте `security`, но концептуально это два разных слоя.

---

### 2. Аутентификация: как работает Authenticator

В Symfony 6+ (без legacy `FormLogin`) аутентификация строится на **Authenticator** — классе, который:

1. Определяет, **когда** срабатывает (по URL, по заголовку)
2. **Читает** credentials из запроса
3. **Валидирует** их
4. **Создаёт** `TokenInterface`

```php
// src/Security/ApiTokenAuthenticator.php
final class ApiTokenAuthenticator extends AbstractLoginFormAuthenticator
{
    public function supports(Request $request): ?string
    {
        // Срабатывает только на /api/*
        return str_starts_with($request->getPathInfo(), '/api/') ? 'login' : null;
    }

    public function getCredentials(Request $request): ?string
    {
        // Читаем токен из Authorization header
        $header = $request->headers->get('Authorization', '');
        return str_starts_with($header, 'Bearer ')
            ? substr($header, 7)
            : null;
    }

    public function checkCredentials(string $token, UserInterface $user): bool
    {
        // Сравниваем хеш
        return $this->passwordHasher->isPasswordValid($user, $token);
        // Или: проверяем JWT, OAuth2 token, и т.д.
    }

    public function createToken(UserInterface $user, string $firewallName): TokenInterface
    {
        return new BearerTokenRequest($user, $firewallName, ['roles' => $user->getRoles()]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // для API — просто пропускаем дальше
    }
}
```

```yaml
# config/packages/security.yaml
security:
    enable_authenticator_manager: true

    firewalls:
        api:
            pattern: ^/api
            stateless: true
            custom_authenticators:
                - App\Security\ApiTokenAuthenticator

        main:
            pattern: ^/
            lazy: true
            form_login:
                login_path: app_login
                check_path: app_login
            logout:
                path: app_logout
```

**Что происходит при запросе `GET /api/orders` с `Authorization: Bearer abc123`:**

```
1. Firewall 'api' перехватывает запрос
2. ApiTokenAuthenticator::supports() → 'login' (не null → срабатывает)
3. getCredentials() → 'abc123'
4. UserProvider::loadUserByIdentifier('abc123') → User
5. checkCredentials('abc123', $user) → true
6. createToken() → BearerTokenRequest
7. Token хранится в SecurityTokenStorage (request attribute)
8. Запрос идёт дальше → authorization
```

**Важно:** `stateless: true` для API-файрвола. Нет session, нет cookie. Каждый запрос — независимый. Для web-файрвола — `lazy: true` (session создаётся только при первом обращении к `security` сервису).

---

### 3. Роли: как хранятся и проверяются

#### Хранение ролей

```php
// src/Entity/User.php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @var string[] */
    private array $roles = [];

    public function getRoles(): array
    {
        // ROLE_USER — добавляется автоматически при аутентификации
        // Не нужно указывать вручную
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }
}
```

```yaml
# В Symfony 6+ роли можно задавать через:
# 1. БД (поле roles, JSON)
# 2. Атрибут (для fixed roles)
# 3. Вычисляемые (через Voter или custom provider)
```

#### Проверка ролей

```php
// В контроллере
#[IsGranted('ROLE_ADMIN')]
public function deleteUser(int $id): Response { /* ... */ }

// В шаблоне Twig
{% if is_granted('ROLE_ADMIN') %}
    <a href="{{ path('admin_dashboard') }}">Admin</a>
{% endif %}

// В PHP-коде
$authenticator = $this->container->get('security.token_storage')->getToken();
$roles = $authenticator->getRoleNames();
// ['ROLE_USER', 'ROLE_ADMIN']
```

#### Access control в конфиге

```yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/api/orders, roles: ROLE_USER }
        - { path: ^/api/orders/\d+/refund, roles: ROLE_ADMIN }
        - { path: ^/checkout, roles: ROLE_USER }
        - { path: ^/public, roles: IS_ANONYMOUS }  # Symfony < 6
```

Это **первый барьер**: если роль не совпадает — `AccessDeniedException`, 403. Дальше код не идёт.

---

### 4. Voters: точечный контроль над объектами

Роли отвечают на вопрос «**кто ты**». Voter отвечает на вопрос «**что ты можешь сделать с этим конкретным объектом**».

#### Пример: управление заказами

```php
// src/Security/OrderVoter.php
final class OrderVoter extends Voter
{
    public const VIEW    = 'ORDER_VIEW';
    public const CANCEL  = 'ORDER_CANCEL';
    public const REFUND  = 'ORDER_REFUND';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Voter участвует только в проверках ORDER_* на Order
        return in_array($attribute, [self::VIEW, self::CANCEL, self::REFUND])
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        $order = $subject; // ← конкретный объект, а не роль

        return match ($attribute) {
            self::VIEW   => $this->canView($user, $order),
            self::CANCEL => $this->canCancel($user, $order),
            self::REFUND => $this->canRefund($user, $order),
            default      => false,
        };
    }

    private function canView(User $user, Order $order): bool
    {
        // Владелец — всегда может
        if ($user->getId() === $order->getCustomerId()) {
            return true;
        }
        // Менеджер — может видеть все
        if (in_array('ROLE_MANAGER', $user->getRoles(), true)) {
            return true;
        }
        // Админ — может видеть все
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }
        return false;
    }

    private function canCancel(User $user, Order $order): bool
    {
        // Только владелец
        if ($user->getId() !== $order->getCustomerId()) {
            return false;
        }
        // Только в статусе 'new'
        return $order->getStatus() === OrderStatus::New;
    }

    private function canRefund(User $user, Order $order): bool
    {
        // Только админ
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return false;
        }
        // Только оплаченные
        return $order->getStatus() === OrderStatus::Paid;
    }
}
```

#### Использование

```php
// В контроллере
#[IsGranted(OrderVoter::VIEW, 'order')]
public function getOrder(Order $order): Response
{
    return $this->json($order);
}

#[IsGranted(OrderVoter::CANCEL, 'order')]
public function cancelOrder(Order $order): Response
{
    $order->cancel();
    // ...
    return new Response('', 204);
}

// В Twig
{% if is_granted('ORDER_VIEW', order) %}
    <p>{{ order.total }}</p>
{% endif %}

{% if is_granted('ORDER_CANCEL', order) %}
    <a href="{{ path('cancel', {id: order.id}) }}">Отменить</a>
{% endif %}
```

**Ключевое отличие от ролей:** `$subject` — это **объект**. Voter видит `$order->getCustomerId()`, `$order->getStatus()`. Роль — нет.

---

### 5. Expression Language: «быстрый voter без класса»

Для простых условий не нужен отдельный класс:

```yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }

        # Пользователь видит свой профиль
        - { path: ^/profile/{id}, id: "\d+", roles: "is_granted('ROLE_USER') and user.id == context.id" }
```

```php
// В контроллере
#[IsGranted("object.ownerId == user.id or hasRole('ROLE_ADMIN')")]
public function getOrder(Order $order): Response { /* ... */ }
```

Expression Language имеет доступ к:
- `user` — текущий `UserInterface`
- `object` — `$subject`
- `context` — любые атрибуты запроса
- `request` — `Request`
- `security` — `AuthorizationChecker`

---

### 6. Когда voter, а когда роли

#### Критерий: «Зависит ли решение от конкретного объекта?»

| Вопрос | Ответ | Решение |
|--------|-------|---------|
| Зависит от объекта? | **Нет** | Роли / access_control |
| Зависит от объекта? | **Да** | Voter / Expression |
| Один метод — одна роль? | Да | `#[IsGranted('ROLE_X')]` |
| Несколько условий (статус + владелец + роль)? | Да | Voter |
| Условие меняется часто (бизнес-логика)? | Да | Voter (легко менять) |
| Условие фиксировано (админ = всё)? | Да | Роль |

#### Примеры

**Роли достаточны:**

```php
// «Админ может удалить кого угодно» — не зависит от объекта
#[IsGranted('ROLE_ADMIN')]
public function deleteUser(int $id): Response { /* ... */ }

// «Только авторизованные могут видеть /dashboard»
// access_control: - { path: ^/dashboard, roles: ROLE_USER }

// «Менеджер видит отчёты»
#[IsGranted('ROLE_MANAGER')]
public function getReports(): Response { /* ... */ }
```

Здесь решение: «у тебя роль X → да». Объект не влияет.

**Voter необходим:**

```php
// «Пользователь отменяет только СВОЙ заказ, и только если он ещё не отправлен»
#[IsGranted(OrderVoter::CANCEL, 'order')]
public function cancelOrder(Order $order): Response { /* ... */ }

// «Менеджер видит заказы своей команды, админ — все»
#[IsGranted(OrderVoter::VIEW, 'order')]
public function getOrder(Order $order): Response { /* ... */ }

// «Пользователь может редактировать проект, если он участник И проект не закрыт»
#[IsGranted(ProjectVoter::EDIT, 'project')]
public function editProject(Project $project): Response { /* ... */ }
```

Здесь решение зависит от `$order->getCustomerId()`, `$order->getStatus()`. Роль это не выразит.

---

### 7. Антипаттерны

#### ❌ «Роль на каждый объект»

```php
// ПЛОХО: 50 ролей
// ROLE_ORDER_1_VIEWER
// ROLE_ORDER_2_VIEWER
// ROLE_ORDER_1_CANCELER
// ROLE_ORDER_2_CANCELER
// ...
```

**Почему плохо:** роли — глобальные, не привязаны к объекту. Вы создаёте `O(n × m)` ролей (n пользователей × m объектов). БД разрастается, управление невозможно.

**Как правильно:** voter с `$subject`.

#### ❌ «Voter для фиксированного условия»

```php
// ПЛОХО: voter, который просто проверяет роль
final class AdminVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'IS_ADMIN';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $token->getUser()->getRoles() contains 'ROLE_ADMIN';
    }
}

// Это просто роль, только в виде класса
```

**Как правильно:** `#[IsGranted('ROLE_ADMIN')]` или `access_control`.

#### ❌ «Voter зависит от текущего состояния, которое меняется во время запроса»

```php
// ПЛОХО: voter читает из request attribute, который меняется
final class OrderVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // ← $request->attributes->get('discount') — изменён контроллером ДО проверки
        $discount = $request->attributes->get('discount');
        return $discount > 0;
    }
}
```

**Почему плохо:** voter должен быть **детерминированным**. Если он зависит от side effects текущего запроса — он непредсказуем. Тесты не воспроизведут.

**Как правильно:** передавать контекст в `$subject` или в атрибут.

#### ❌ «Все проверки в одном voter»

```php
// ПЛОХО: 20 методов, 200 строк
final class EverythingVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true; // ← участвует во ВСЁМ
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            'ORDER_VIEW' => $this->canView(...),
            'ORDER_CANCEL' => $this->canCancel(...),
            'INVOICE_VIEW' => $this->canViewInvoice(...),
            'INVOICE_EDIT' => $this->canEditInvoice(...),
            'PROJECT_VIEW' => $this->canViewProject(...),
            // ... 20+ веток
        };
    }
}
```

**Как правильно:** один voter на доменную сущность (`OrderVoter`, `ProjectVoter`, `InvoiceVoter`).

---

### 8. Тестирование voters

```php
// tests/Security/OrderVoterTest.php
final class OrderVoterTest extends TestCase
{
    private function tokenFor(string $userId, array $roles): TokenInterface
    {
        $user = new User();
        $user->setRoles($roles);
        return new BearerTokenRequest($user, 'main', ['roles' => $roles]);
    }

    public function testOwnerCanCancelNewOrder(): void
    {
        $order = new Order();
        $order->setCustomerId(42);
        $order->setStatus(OrderStatus::New);

        $voter = new OrderVoter();
        $token = $this->tokenFor(42, ['ROLE_USER']);

        self::assertTrue(
            $voter->vote($token, OrderVoter::CANCEL, $order)
        );
    }

    public function testOwnerCannotCancelShippedOrder(): void
    {
        $order = new Order();
        $order->setCustomerId(42);
        $order->setStatus(OrderStatus::Shipped);

        $voter = new OrderVoter();
        $token = $this->tokenFor(42, ['ROLE_USER']);

        self::assertFalse(
            $voter->vote($token, OrderVoter::CANCEL, $order)
        );
    }

    public function testAdminCanCancelAnyOrder(): void
    {
        $order = new Order();
        $order->setCustomerId(99);
        $order->setStatus(OrderStatus::New);

        $voter = new OrderVoter();
        $token = $this->tokenFor(1, ['ROLE_USER', 'ROLE_ADMIN']);

        self::assertTrue(
            $voter->vote($token, OrderVoter::CANCEL, $order)
        );
    }
}
```

**Почему это легко:** voter — чистый класс, не зависит от Symfony-контейнера, БД, request. `supports()` + `voteOnAttribute()` — два метода, два аргумента. Тест — 10 строк.

---

### 9. Итого: слои контроля доступа

```
┌─────────────────────────────────────────────────────────────────┐
│ Слой 1: access_control (URL → роль)                            │
│   - «/admin/* требует ROLE_ADMIN»                              │
│   - Самый внешний барьер. Быстрый. Не зависит от объекта.      │
├─────────────────────────────────────────────────────────────────┤
│ Слой 2: #[IsGranted('ROLE_X')] на контроллере                  │
│   - «Этот endpoint требует ROLE_MANAGER»                       │
│   - Гибче access_control (может быть на action, не на URL)     │
├─────────────────────────────────────────────────────────────────┤
│ Слой 3: Expression Language                                     │
│   - «object.ownerId == user.id or hasRole('ROLE_ADMIN')»       │
│   - Быстрый, без класса. Для простых условий.                  │
├─────────────────────────────────────────────────────────────────┤
│ Слой 4: Voter                                                   │
│   - «$user.id === $order.customerId and $order.status == New»  │
│   - Сложные условия, зависящие от объекта.                     │
│   - Тестируемый, документированный, переиспользуемый.          │
├─────────────────────────────────────────────────────────────────┤
│ Слой 5: Проверка внутри доменной модели                         │
│   - $order->cancel() бросает OrderCannotBeCancelledException   │
│   - Последний барьер. Даже если все выше прошли —              │
│   доменная модель может отказать.                              │
└─────────────────────────────────────────────────────────────────┘
```

**Правило:** каждый слой — «замок». Слой 1 отсечает 95% запросов (быстро, без PHP-логики). Слои 2–4 уточняют. Слой 5 — страховка от программиста, который забыл добавить проверку в контроллер.

> ⏱ 35.1s · prompt 29604 tok · output 4829 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении Value Object в Doctrine, учитывая требования домена: неизменяемость (immutability) и валидное состояние (invariant). Это глубокий вопрос на стыке DDD и ORM.

Мне нужно покрыть:
1. Почему стандартный маппинг Doctrine конфликтует с VO
2. Варианты: кастомные типы, маппинг в отдельные поля, маппинг в JSON, маппинг через embeddable
3. Подводные камни каждого: lazy loading, hydration, сравнение, SQL-запросы, индексы
4. Практические рекомендации

Ключевые противоречия:
- VO immutable → Doctrine hydrates mutable entity
- VO has invariants in constructor → Doctrine bypasses constructor (uses reflection + newInstanceWithoutConstructor)
- VO is value (compared by value) → Doctrine tracks by reference
- VO fields need SQL-level queries (WHERE email = 'x') → если в JSON — невозможно

Структурирую ответ:
1. Конфликт: почему "наивный" подход сломан
2. Варианты (от простого к сложному)
   a. Отдельные колонки (mapping fields 1:1)
   b. Custom DBAL Type (JSON в одной колонке)
   c. Embeddable
   d. Гибрид: отдельные колонки для query-able + VO в домене
3. Подводные камни каждого
4. Практический паттерн (антитармак + VO)
5. Сравнительная таблица
6. Рекомендация

Нужно показать код, реальный, работающий. И указать на конкретные баги, которые встречаются.

</details>

**Кандидат:**

## Value Object в Doctrine: конфликт и его разрешение

### 1. Почему «наивный» подход сломан

```php
// Domain: Money — immutable, invariants in constructor
final class Money
{
    private function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}

    public static function of(int $amount, string $currency): self
    {
        if ($amount < 0) {
            throw new NegativeAmountException($amount);
        }
        if (!in_array($currency, ['RUB', 'USD', 'EUR'], true)) {
            throw new InvalidCurrencyException($currency);
        }
        return new self($amount, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException();
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

```php
// Entity: «наивный» маппинг
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalAmount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    public function getTotal(): Money
    {
        return Money::of($this->totalAmount, $this->totalCurrency); // ← каждый раз new
    }

    public function setTotal(Money $money): void
    {
        $this->totalAmount = $money->amount;     // ← mutable!
        $this->totalCurrency = $money->currency; // ← mutable!
    }
}
```

**Проблемы:**

| Проблема | Последствие |
|----------|-------------|
| `Money::of()` вызывается на каждом `getTotal()` | Инвариант проверяется N раз за транзакцию |
| `$this->totalAmount = -5` можно задать напрямую | Инвариант нарушен, `getTotal()` бросит исключение **позже** |
| `Money` — value, сравнивается по значению | Doctrine сравнивает по reference: `===` |
| `new Order()` создаёт объект с `totalAmount = 0` | Временное невалидное состояние |
| SQL-запрос `WHERE totalCurrency = 'RUB'` — работает | Но `WHERE totalAmount > 100` — тоже работает (лишне) |

**Корень конфликта:** Doctrine — **reference-based**, mutable, bypass-конструктор. VO — **value-based**, immutable, invariant-конструктор. Это две разные парадигмы.

---

### 2. Варианты маппинга

---

#### Вариант A: Отдельные колонки (flat mapping)

**Схема:**

```sql
CREATE TABLE orders (
    id INTEGER PRIMARY KEY,
    total_amount INTEGER NOT NULL,
    total_currency VARCHAR(3) NOT NULL,
    CHECK (total_amount >= 0),
    CHECK (total_currency IN ('RUB', 'USD', 'EUR'))
);
CREATE INDEX idx_orders_currency ON orders (total_currency);
```

**Entity:**

```php
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalAmount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    public function __construct()
    {
        // Doctrine создаёт через newInstanceWithoutConstructor()
        // Поля = null / 0 / '' — НЕВАЛИДНОЕ состояние
    }

    public function getMoney(): Money
    {
        return new Money($this->totalAmount, $this->totalCurrency);
    }

    public function setMoney(Money $money): void
    {
        $this->totalAmount = $money->amount;
        $this->totalCurrency = $money->currency;
    }
}
```

| Плюс | Минус |
|------|-------|
| SQL-запросы по любому полю: `WHERE total_currency = 'RUB'` | Временное невалидное состояние между `__construct` и `setMoney()` |
| Индексы на отдельные поля | Инвариант — только в PHP (и в DB CHECK) |
| Простота, понятность | `getMoney()` создаёт `new Money()` каждый вызов — инвариант проверяется заново |
| Нет парсинга JSON | `Money` и поля — два представления одного значения. Дублирование. |
| Миграции тривиальны | Невозможно «забыть» поле: Doctrine не знает, что `totalAmount` и `totalCurrency` — одно VO |

**Подводные камни:**

1. **Dirty check ломается.** Doctrine сравнивает `Order::totalAmount` до/после. Если вы сделали `$order->getMoney()->add(...)` и не записали обратно в поля — **изменение потеряно**.

```php
// БАГ: Doctrine не видит изменение
$money = $order->getMoney();
$money = $money->add(new Money(100, 'RUB')); // ← $money — новый объект
// $order->totalAmount НЕ изменился! Doctrine думает, что ничего не изменилось.
$this->em->flush(); // ← не пишет в БД
```

Решение: всегда `setMoney()`:

```php
$order->setMoney($order->getMoney()->add(new Money(100, 'RUB')));
$this->em->flush(); // ← теперь dirty check видит изменение
```

2. **SQL-запросы обходят инварианты.**

```sql
-- Кто-то в admin-панели:
UPDATE orders SET total_amount = -500 WHERE id = 42;
-- PHP-инвариант не сработал. CHECK constraint — единственная защита.
```

---

#### Вариант B: Custom DBAL Type (JSON-колонка)

**Схема:**

```sql
CREATE TABLE orders (
    id INTEGER PRIMARY KEY,
    total JSONB NOT NULL  -- {"amount": 1500, "currency": "RUB"}
);
```

**Custom Type:**

```php
// src/Infrastructure/Doctrine/Type/MoneyType.php
#[ORM\CustomTypeMapping(['money' => 'App\Infrastructure\Doctrine\Type\MoneyType'])]
final class MoneyType extends Type
{
    public function getName(): string
    {
        return 'money';
    }

    public function getSQLDeclaration(?array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        /** @var Money $value */
        return json_encode([
            'amount'   => $value->amount,
            'currency' => $value->currency,
        ], JSON_THROW_ON_ERROR);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        // Инвариант проверяется через Money::of()
        return Money::of(
            amount: (int) $data['amount'],
            currency: (string) $data['currency'],
        );
    }

    public function getTypesUpgraded(): array
    {
        return [Types::JSON => $this->getName()];
    }
}
```

**Entity:**

```php
class Order
{
    #[ORM\Column(type: 'money')]
    private Money $total;

    public function getTotal(): Money
    {
        return $this->total; // ← тот же объект, не new
    }

    public function setTotal(Money $money): void
    {
        $this->total = $money;
    }
}
```

| Плюс | Минус |
|------|-------|
| Инвариант проверяется **при десериализации** (`Money::of()`) | **Нельзя фильтровать в SQL**: `WHERE total.amount > 100` не работает |
| VO — единая сущность, нет дублирования | Индексы: только на весь JSON (`gin_index` в Postgres) |
| `getTotal()` не создаёт `new` каждый раз | Миграции сложнее (нужен raw SQL для JSON-колонки) |
| Читаемость Entity: `Money $total` | `convertToPHPValue` — bottleneck при больших SELECT |
| Добавление поля в VO — одна колонка, не две | Отладка: `SELECT total FROM orders` → JSON-строка |

**Подводные камни:**

1. **Никаких SQL-фильтров по полям VO.**

```php
// ПЛОХО: не работает
$orderRepo->createQueryBuilder('o')
    ->where('o.total.currency = :cur')  // ← Doctrine не знает, что внутри JSON
    ->setParameter('cur', 'RUB');

// ДОПУСТИМО: DQL с JSON-функцией (Postgres-specific)
->where("o.total['currency'] = :cur")  // ← не переносимый, Postgres-only

// ДОПУСТИМО: native query
->select('o.*')
->where("total->>'currency' = :cur")
->setParameter('cur', 'RUB');
```

Если вам нужны запросы «все заказы в рублях на сумму > 1000» — JSON-колонка **не подходит**.

2. **Dirty check с object-полями.**

```php
// БЫЛО:
$order->setTotal(Money::of(2000, 'RUB'));

// ДОЛЖНО (иначе dirty check может не сработать):
$order->setTotal(new Money(2000, 'RUB')); // ← новый объект, !== old
```

На практике Doctrine сравнивает `===` (reference) для object-типов. Если вы мутируете объект (невозможно, он immutable) — ок. Но если создаёте новый — ok. Если делаете `$order->total = $order->total` — **не детектируется**.

3. **Гидратация медленнее.**

Каждый `convertToPHPValue()` — `json_decode()` + `Money::of()`. Для 10 000 строк — 10 000 декодирований. На практике это ~10–50 мс, не критично, но заметнее, чем flat mapping.

---

#### Вариант C: Embeddable

```php
// src/Entity/Embedded/Total.php
#[ORM\Embeddable]
class Total
{
    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    // getters/setters
}

// src/Entity/Order.php
class Order
{
    #[ORM\Embedded(class: Total::class, columnPrefix: 'total_')]
    private Total $total;
}
```

**Схема:**

```sql
CREATE TABLE orders (
    id INTEGER PRIMARY KEY,
    total_amount INTEGER NOT NULL,
    total_currency VARCHAR(3) NOT NULL
);
```

| Плюс | Минус |
|------|-------|
| SQL-запросы по полям работают | Embeddable — **mutable** (setters) |
| Поля сгруппированы в Entity | Инвариант — только в `__construct` Embeddable, но Doctrine bypass его |
| Индексы на отдельные поля | `Total` и `Money` — два класса для одного значения |
| Миграции простые | Embeddable не поддерживает `readonly`-свойства (в ранних версиях) |

**Подводные камни:**

1. **Embeddable создаётся через `newInstanceWithoutConstructor`.** Инвариант в `__construct` Embeddable **не проверяется** при гидратации.

```php
// В Total::__construct — проверка amount >= 0
// Но Doctrine делает:
$reflection = new \ReflectionClass(Total::class);
$instance = $reflection->newInstanceWithoutConstructor(); // ← __construct не вызван!
$instance->amount = -5; // ← невалидное состояние в памяти
```

2. **Нельзя `readonly` на Embeddable.**

```php
#[ORM\Embeddable]
class Total
{
    // ПЛОХО: Doctrine не может записать в readonly
    #[ORM\Column(type: 'integer')]
    private readonly int $amount;
}
```

3. **Нестандартный lifecycle.** Embeddable — не сущность, не агрегат, а «часть». Это создаёт когнитивное расхождение с DDD, где VO — самостоятельная сущность.

---

#### Вариант D: Гибрид — flat mapping + доменный слой (рекомендуемый)

**Идея:** Entity хранит «плоские» поля для SQL. Доменная модель — VO. Между ними — **механизм преобразования**, а не маппинг.

```
┌─────────────────────────────────────────────────────────────────┐
│ DOМЕН (чистый PHP, без Doctrine)                                │
│                                                                 │
│  final class Order                                              │
│  {                                                              │
│      private Money $total;          ← VO, immutable             │
│      private OrderStatus $status;  ← VO, immutable              │
│                                                                 │
│      public function addAmount(Money $amount): self             │
│      {                                                          │
│          $this->total = $this->total->add($amount);             │
│          return $this;                                          │
│      }                                                          │
│  }                                                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                    mapping (ручное)
                              │
┌─────────────────────────────────────────────────────────────────┐
│ ИНФРАСТРУКТУРА (Doctrine Entity)                                │
│                                                                 │
│  #[ORM\Entity]                                                  │
│  class OrderEntity                                              │
│  {                                                              │
│      private int $totalAmount;       ← flat, для SQL            │
│      private string $totalCurrency;  ← flat, для SQL            │
│      private string $status;         ← flat, для SQL            │
│  }                                                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                    Repository (адаптер)
                              │
┌─────────────────────────────────────────────────────────────────┐
│ РЕПОЗИТОРИЙ (интерфейс в домене, реализация в инфраструктуре)  │
│                                                                 │
│  interface OrderRepositoryInterface  ← в домене                 │
│  {                                                              │
│      public function save(Order $order): void;                  │
│      public function find(int $id): ?Order;                     │
│      public function findByCurrency(string $cur, int $min): array;│
│  }                                                              │
│                                                                 │
│  class DoctrineOrderRepository implements OrderRepositoryInterface│
│  {                                                              │
│      public function save(Order $order): void                   │
│      {                                                          │
│          $entity = $this->toEntity($order);  ← Order → Entity   │
│          $this->em->persist($entity);                            │
│          $this->em->flush();                                     │
│      }                                                          │
│                                                                 │
│      public function find(int $id): ?Order                       │
│      {                                                          │
│          $entity = $this->em->find(OrderEntity::class, $id);    │
│          return $entity ? $this->toDomain($entity) : null;      │
│      }                                                          │
│                                                                 │
│      public function findByCurrency(string $cur, int $min): array│
│      {                                                          │
│          $entities = $this->em->getRepository(OrderEntity::class)│
│              ->createQueryBuilder('e')                              │
│              ->where('e.totalCurrency = :cur')                  │
│              ->andWhere('e.totalAmount >= :min')                │
│              ->getQuery()                                       │
│              ->getResult();                                     │
│          return array_map(fn($e) => $this->toDomain($e), $entities);│
│      }                                                          │
│                                                                 │
│      private function toEntity(Order $order): OrderEntity        │
│      {                                                          │
│          $entity = new OrderEntity();                           │
│          $entity->id = $order->id;                              │
│          $entity->totalAmount = $order->total->amount;          │
│          $entity->totalCurrency = $order->total->currency;      │
│          $entity->status = $order->status->value;               │
│          return $entity;                                        │
│      }                                                          │
│                                                                 │
│      private function toDomain(OrderEntity $entity): Order       │
│      {                                                          │
│          return Order::reconstitute(                            │
│              id: $entity->id,                                   │
│              total: Money::of($entity->totalAmount, $entity->totalCurrency),│
│              status: OrderStatus::from($entity->status),        │
│          );                                                     │
│      }                                                          │
└─────────────────────────────────────────────────────────────────┘
```

**Доменная модель:**

```php
final class Order
{
    private function __construct(
        public readonly int           $id,
        public readonly Money         $total,
        public readonly OrderStatus   $status,
    ) {}

    // Фабрика: валидное состояние гарантировано
    public static function create(int $customerId, Money $total): self
    {
        if ($total->amount <= 0) {
            throw new InvalidOrderAmountException($total->amount);
        }
        return new self(0, $total, OrderStatus::New);
    }

    // Reconstitution: для загрузки из БД (без бизнес-проверок)
    public static function reconstitute(int $id, Money $total, OrderStatus $status): self
    {
        return new self($id, $total, $status);
    }

    // Иммутабельность: возврат нового объекта
    public function addAmount(Money $amount): self
    {
        return new self($this->id, $this->total->add($amount), $this->status);
    }

    public function markAsPaid(): self
    {
        if ($this->status !== OrderStatus::New) {
            throw new InvalidTransitionException($this->status->value, 'paid');
        }
        return new self($this->id, $this->total, OrderStatus::Paid);
    }
}
```

| Плюс | Минус |
|------|-------|
| **Иммутабельность доменной модели** — гарантирована на уровне языка | Дублирование: `Order` и `OrderEntity` |
| **Инварианты** — только в доменной модели, проверены при создании | Ручной mapping (toEntity / toDomain) |
| **SQL-запросы** — по flat-полям, индексы работают | `OrderEntity` — mutable, но «внутри» инфраструктуры |
| **Нет lazy loading** — доменная модель загружается целиком | Больше кода (но он предсказуем) |
| **Тестируемость** — домен без Doctrine, unit-тесты тривиальны | `toDomain()` / `toEntity()` — нужно поддерживать при изменении схемы |
| **Замена ORM** — не трогаем домен | |

**Подводные камни:**

1. **Mapping-код не проверяется автоматическими средствами.** Если вы добавили поле в `Order` и забыли в `toEntity()` / `toDomain()` — ошибка в рантайме. Решение: интеграционные тесты (save → find → assert).

2. **Нет «live» связи.** `$entity->totalAmount = 999` не обновит `$order->total`. Это feature, а не bug: доменная модель — снимок состояния.

3. **Производительность:** каждый `find()` — `toDomain()` (создание VO). Для 1000 строк — 1000 `Money::of()`. На практике — ~5 мс. Не критично.

---

### 3. Сравнительная таблица

| Критерий | A: Flat | B: JSON Type | C: Embeddable | D: Гибрид |
|----------|:-------:|:------------:|:-------------:|:---------:|
| Иммутабельность VO | ❌ | ⚠️ частично | ❌ | ✅ |
| Инварианты при создании | ⚠️ (DB CHECK) | ✅ (при decode) | ❌ | ✅ (доменная модель) |
| SQL-фильтры по полям VO | ✅ | ❌ | ✅ | ✅ |
| Индексы | ✅ | ⚠️ GIN | ✅ | ✅ |
| Миграции | ✅ | ⚠️ | ✅ | ✅ |
| Тестируемость домена | ❌ | ❌ | ❌ | ✅ |
| Замена ORM | ❌ | ❌ | ❌ | ✅ |
| Кол-во кода | Низкое | Среднее | Среднее | Высокое |
| Гидратация | Быстрая | Медленнее | Быстрая | Средняя |
| Dirty check | ⚠️ (mutable) | ⚠️ (reference) | ⚠️ (mutable) | N/A (immutable) |

---

### 4. Практические рекомендации

#### Когда A (flat mapping)

- VO — 2–3 поля, простые (int, string)
- Нужны SQL-фильтры по этим полям
- Не нужен строгий DDD
- Пример: `Email`, `Phone` (если не нужен формат-инвариант)

#### Когда B (JSON type)

- VO — сложное вложение (nested array, структура)
- Не нужны SQL-фильтры
- Пример: `Address` (если не фильтруете по городу)
- **Не подходит:** `Money` (нужен фильтр по сумме)

#### Когда C (Embeddable)

- Нужна группировка в Entity, но не нужен DDD
- Простые VO, 2–4 поля
- Пример: `Coordinates(lat, lng)`

#### Когда D (гибрид)

- **Основной выбор для DDD-проектов**
- VO с инвариантами (`Money`, `OrderStatus`, `Email` с valid-форматом)
- Нужна тестируемость домена
- Нужна возможность заменить ORM
- Готовы писать mapping-код

---

### 5. Специфичные подводные камни

#### 5.1. Сравнение VO

```php
// В доменной модели
$order1->total === $order2->total  // ← reference comparison: FALSE (разные объекты)
$order1->total == $order2->total   // ← value comparison: TRUE (PHP для objects: == сравнивает свойства)

// Правильно:
$order1->total->equals($order2->total)  // ← явный метод
```

В Doctrine: `$entity1->total === $entity2->total` — **всегда false**, если это разные объекты (даже с одинаковыми значениями). Для dirty check Doctrine использует `==` (value comparison для objects).

#### 5.2. `#[ORM\BasicEntity]` и readonly

```php
// В Symfony 7 / Doctrine 3:
final class Order
{
    // ПЛОХО: Doctrine не может записать
    #[ORM\Column(type: 'money')]
    private readonly Money $total;

    // ПЛОХО: Doctrine не может создать
    private function __construct(
        public readonly int $id,
        public readonly Money $total,
    ) {}
}
```

Doctrine **требует** mutable-представления на уровне Entity. Если хотите immutable — только вариант D (доменная модель + адаптер).

#### 5.3. Lazy loading и VO

```php
// БЫЛО:
class Order
{
    #[ORM\Embedded]
    private Total $total;  // ← lazy: Total::amount доступен только после инициализации
}

// При $order->total->amount до инициализации — Error
// С Embeddable нет lazy loading (это часть Entity) — но есть проблема
// при serialization: json_encode($order) → "Uninitialized property"
```

С вариантом D — этой проблемы нет: `Order` — чистый PHP, все поля инициализированы.

#### 5.4. Migrations и VO

```php
// При добавлении поля в VO (например, Money: amount, currency → amount, currency, decimalPlaces)

// Вариант A (flat):
//   ALTER TABLE orders ADD COLUMN total_decimal_places SMALLINT DEFAULT 2;
//   Простая миграция.

// Вариант B (JSON):
//   SELECT total, jsonb_set(total, '{decimalPlaces}', '2') FROM orders;
//   Сложнее, но одна колонка.

// Вариант D (гибрид):
//   ALTER TABLE orders ADD COLUMN total_decimal_places SMALLINT DEFAULT 2;
//   + правка toEntity/toDomain
//   + тест: old data → new code (backward compatibility)
```

Для варианта D критично: **миграция должна быть backward-compatible**. Старые строки без `decimal_places` должны читаться (default в `toDomain()`).

---

### 6. Итого

```
Если вы пишете DDD с immutable VO:
    → Вариант D (гибрид: доменная модель + Entity-адаптер)

Если у вас CRUD-приложение:
    → Вариант A (flat mapping) или C (Embeddable)

Если VO — сложное вложение и нет SQL-фильтров:
    → Вариант B (JSON type)

Никогда:
    → readonly-свойства в Entity
    → Инварианты только в DB CHECK
    → Сравнение VO через ===
```

**Фундаментальный принцип:** Doctrine — **инструмент персистентности**, не **инструмент моделирования**. Не пытайтесь заставить Entity быть доменной моделью. Пусть Entity будет «глупым хранилищем», а доменная модель — «умным объектом». Адаптер между ними — цена за эту чистоту, и она стоит своих денег.

> ⏱ 49.8s · prompt 34294 tok · output 6472 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата с корнем и коллекцией дочерних сущностей в Doctrine. Это классическая DDD-тема. Нужно разобрать:

1. Как маппить коллекцию (OneToMany, embedded, separate table)
2. Границы транзакций (какие операции — атомарные)
3. Кто вызывает flush (репозиторий? сервис? контроллер?)
4. На конкретном примере

Выберу пример: корзина покупок (Order) с позициями (OrderItem). Это наглядно: есть корень, есть коллекция, есть инварианты (сумма = сумма позиций), есть частые операции (добавить позицию, удалить позицию, подтвердить).

Разберу:
- Структуру домена (Order + OrderItem)
- Варианты маппинга в Doctrine (OneToMany, OneToMany + owning side, embedded)
- Границы транзакций (какие операции атомарные, а какие нет)
- Кто вызывает flush (репозиторий — единственное место)
- Подводные камни (lazy loading, orphan removal, cascade, bidirectional)

Структурирую ответ так же, как предыдущие: конкретный код, сравнительные таблицы, антипаттерны.

</details>

**Кандидат:**

## Агрегат в Doctrine: Order + OrderItems

### 1. Доменная модель

```php
// src/Domain/Order/Order.php
final class Order
{
    /** @var Collection<int, OrderItem> */
    private Collection $items;

    public function __construct(
        public readonly int $id,
        public readonly int $customerId,
        public readonly OrderStatus $status,
        /** @param Collection<int, OrderItem> $items */
        Collection $items,
    ) {
        $this->items = $items;
    }

    public static function create(int $customerId): self
    {
        return new self(0, $customerId, OrderStatus::Draft, new ArrayCollection());
    }

    public function addItem(int $productId, string $name, int $qty, int $priceCents): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderIsLockedException($this->id, $this->status->value);
        }
        if ($qty <= 0) {
            throw new InvalidQuantityException($qty);
        }
        if ($priceCents <= 0) {
            throw new InvalidPriceException($priceCents);
        }

        $item = new OrderItem(0, $productId, $name, $qty, $priceCents);
        $this->items->add($item);

        return $this; // mutable внутри агрегата — осознанное решение
    }

    public function removeItem(int $itemId): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderIsLockedException($this->id, $this->status->value);
        }

        $found = false;
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                $this->items->removeElement($item);
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new ItemNotFoundInOrderException($this->id, $itemId);
        }

        return $this;
    }

    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status->value, 'confirmed');
        }
        if ($this->items->count() === 0) {
            throw new EmptyOrderException($this->id);
        }

        return $this->withStatus(OrderStatus::Confirmed);
    }

    public function totalCents(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->qty * $item->priceCents;
        }
        return $total;
    }

    private function withStatus(OrderStatus $status): self
    {
        return new self($this->id, $this->customerId, $status, $this->items);
    }

    /** @return Collection<int, OrderItem> */
    public function items(): Collection
    {
        return $this->items;
    }
}
```

```php
// src/Domain/Order/OrderItem.php
final class OrderItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $name,
        public readonly int $qty,
        public readonly int $priceCents,
    ) {}

    public function lineTotalCents(): int
    {
        return $this->qty * $this->priceCents;
    }
}
```

```php
// src/Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
```

**Ключевые решения:**

| Решение | Почему |
|---------|--------|
| `Order` — корень агрегата | Все мутации проходят через него. Никто не трогает `OrderItem` напрямую. |
| `OrderItem` — child entity | Нет своего репозитория. Не существует без `Order`. |
| `items` — mutable `Collection` | В DDD внутри агрегата мутация допустима. Иммутабельность — на уровне «снаружи» (после flush). |
| Инварианты в методах | `addItem` проверяет статус. `confirm` проверяет пустоту. Невозможно создать невалидный агрегат. |

---

### 2. Маппинг в Doctrine

#### Вариант A: OneToMany + owning side на Order (стандартный)

**Схема:**

```sql
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    status VARCHAR(20) NOT NULL
);

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    qty INTEGER NOT NULL CHECK (qty > 0),
    price_cents INTEGER NOT NULL CHECK (price_cents > 0)
);

CREATE INDEX idx_order_items_order ON order_items (order_id);
CREATE INDEX idx_order_items_product ON order_items (product_id);
```

**Entity-корень:**

```php
// src/Infrastructure/Persistence/OrderEntity.php
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $customerId;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\OneToMany(
        targetEntity: OrderItemEntity::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,   // ← ключевое
    )]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    // ... getters/setters (инфраструктурный код, не доменная логика)
}
```

**Entity-дочка:**

```php
// src/Infrastructure/Persistence/OrderItemEntity.php
#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItemEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inverse: 'items', inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    private ?OrderEntity $order = null;

    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $qty;

    #[ORM\Column(type: 'integer')]
    private int $priceCents;

    public function __construct()
    {
        // Doctrine создаёт через newInstanceWithoutConstructor
        // Но конструктор — пустой, чтобы не конфликтовать
    }
}
```

**Критические параметры маппинга:**

| Параметр | Значение | Почему |
|----------|----------|--------|
| `cascade: ['persist', 'remove']` | На стороне Order | Агрегат живёт/умирает целиком. Не cascade `merge` (опасно) |
| `orphanRemoval: true` | На стороне Order | `removeItem()` → item удаляется из БД. Без этого — orphan row |
| `onDelete: 'CASCADE'` | На JoinColumn | Защита на уровне БД, если PHP-код «упустит» |
| `mappedBy: 'order'` | На стороне Order | OneToMany — owning side = дочка (ManyToOne). Но cascade/orphan — на Order |

**Почему `orphanRemoval: true`:**

```php
// В доменной модели:
$order->removeItem($itemId);
// Collection::removeElement() — item больше не в Collection

// Без orphanRemoval:
// Doctrine flush → item НЕ удалён из БД (orphan row!)

// С orphanRemoval:
// Doctrine flush → item удалён из БД
```

---

#### Вариант B: Embeddable (если items — не отдельная сущность)

Подходит, если `OrderItem` — это просто данные, не сущность со своим lifecycle:

```php
#[ORM\Embeddable]
class OrderItemData
{
    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $qty;

    #[ORM\Column(type: 'integer')]
    private int $priceCents;
}
```

**Минус:** нет `id` у item, нет отдельного lifecycle, нельзя «удалить один item» без пересоздания остальных. Для корзины — **не подходит**.

---

#### Вариант C: JSONB (если items — простой массив)

```php
#[ORM\Column(type: 'jsonb')]
private array $items; // [{product_id, name, qty, price_cents}, ...]
```

**Минус:** нет SQL-фильтра «заказы, содержащие товар #42». Для корзины с фильтрацией по товару — **не подходит**.

**Итог:** для Order + OrderItems — **Вариант A**.

---

### 3. Репозиторий: граница домен/инфраструктура

```php
// src/Domain/Order/OrderRepositoryInterface.php
interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function find(int $id): ?Order;
    public function findByCustomer(int $customerId): array;
}
```

```php
// src/Infrastructure/Persistence/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(Order $order): void
    {
        $entity = $order->id > 0
            ? $this->em->find(OrderEntity::class, $order->id)
            : new OrderEntity();

        if ($entity === null) {
            throw new OrderNotFoundException($order->id);
        }

        // Корень: пишем поля
        $entity->setCustomerId($order->customerId);
        $entity->setStatus($order->status->value);

        // Коллекция: полный sync
        // 1. Удаляем старые items (orphanRemoval сделает DELETE)
        $entity->getItems()->clear();

        // 2. Добавляем новые
        foreach ($order->items() as $item) {
            $itemEntity = new OrderItemEntity();
            $itemEntity->setProductId($item->productId);
            $itemEntity->setName($item->name);
            $itemEntity->setQty($item->qty);
            $itemEntity->setPriceCents($item->priceCents);
            $itemEntity->setOrder($entity);

            $entity->getItems()->add($itemEntity);
        }

        $this->em->persist($entity);
        $this->em->flush(); // ← FLOW — здесь!
    }

    public function find(int $id): ?Order
    {
        /** @var OrderEntity|null $entity */
        $entity = $this->em->find(OrderEntity::class, $id);

        if ($entity === null) {
            return null;
        }

        // Force load collection (или lazy — см. ниже)
        $items = [];
        /** @var OrderItemEntity $itemEntity */
        foreach ($entity->getItems()->toArray() as $itemEntity) {
            $items[] = new OrderItem(
                id: $itemEntity->getId() ?? 0,
                productId: $itemEntity->getProductId(),
                name: $itemEntity->getName(),
                qty: $itemEntity->getQty(),
                priceCents: $itemEntity->getPriceCents(),
            );
        }

        return new Order(
            id: $entity->getId(),
            customerId: $entity->getCustomerId(),
            status: OrderStatus::from($entity->getStatus()),
            items: new ArrayCollection($items),
        );
    }

    public function findByCustomer(int $customerId): array
    {
        $entities = $this->em
            ->getRepository(OrderEntity::class)
            ->createQueryBuilder('o')
            ->where('o.customerId = :cust')
            ->setParameter('cust', $customerId)
            ->getQuery()
            ->getResult();

        return array_map(fn(OrderEntity $e) => $this->toDomain($e), $entities);
    }
}
```

---

### 4. Кто и когда вызывает flush

#### Правило: **flush — в репозитории, нигде больше**

```
Controller          Service              Repository            Doctrine
    │                 │                      │                    │
    │  placeOrder()   │                      │                    │
    │────────────────>│                      │                    │
    │                 │  save(order)         │                    │
    │                 │─────────────────────>│                    │
    │                 │                      │  persist + flush   │
    │                 │                      │───────────────────>│
    │                 │                      │  (SQL)             │
    │                 │                      │<───────────────────│
    │                 │<─────────────────────│                    │
    │<────────────────│                      │                    │
    │  201 Created    │                      │                    │
```

**Почему не в Service:**

```php
// ПЛОХО
final class OrderService
{
    public function placeOrder(int $customerId, array $items): Order
    {
        $order = Order::create($customerId);
        foreach ($items as $item) {
            $order = $order->addItem($item['productId'], $item['name'], $item['qty'], $item['price']);
        }
        $order = $order->confirm();

        $this->repo->save($order);
        $this->em->flush();  // ← сервис знает про EntityManager!
        return $order;
    }
}
```

Проблемы:
- Service зависит от `EntityManagerInterface` — нарушает DIP
- `flush()` — инфраструктурное действие (SQL). Service — бизнес-логика.
- Если `save()` делает 3 операции (save order, save items, send event) — `flush()` после каждого или один? Service не должен решать.

**Почему не в Controller:**

```php
// ПЛОХО
public function placeOrder(Request $request): JsonResponse
{
    $order = $this->orderService->placeOrder(/* ... */);
    $this->em->flush(); // ← controller знает про Doctrine
    return $this->json($order);
}
```

Controller — транспортный слой. Не знает про БД.

**Почему в Repository:**

| Аргумент | Пояснение |
|----------|-----------|
| Encapsulation | Только репозиторий знает, **как** сохраняется (persist + flush, или batch, или deferred) |
| Тестируемость | Unit-тест репозитория: `save()` → assert БД. Без `flush()` в сервисе/контроллере |
| Гибкость | Можно изменить стратегию (flush per call vs batch) без правки сервиса |
| Один владелец | Если кто-то второй вызовет `flush()` — double flush, двойные SQL-запросы |

---

### 5. Границы транзакций

#### Что атомарно

```
Транзакция = одна операция над агрегатом
```

| Операция | Атомарность |
|----------|-------------|
| `createOrder` + `addItem` + `confirm` + `save` | **Да** — одна транзакция, один flush |
| `removeItem` + `save` | **Да** |
| `confirm` + `save` + `sendEmail` | **Нет** — email асинхронный (см. предыдущий вопрос) |
| `confirm` (заказ #1) + `confirm` (заказ #2) | **Нет** — два разных агрегата, две транзакции |

#### Практика

```php
// src/Application/PlaceOrderCommand.php
final class PlaceOrderCommand
{
    public function __construct(
        private readonly OrderRepositoryInterface $repo,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(int $customerId, array $items): Order
    {
        // 1. Доменная логика (без БД)
        $order = Order::create($customerId);
        foreach ($items as $item) {
            $order = $order->addItem(
                productId: $item['productId'],
                name: $item['name'],
                qty: $item['qty'],
                priceCents: $item['priceCents'],
            );
        }
        $order = $order->confirm();

        // 2. Сохранение (одна транзакция, один flush)
        $this->repo->save($order);

        // 3. Асинхронные side effects (вне транзакции)
        $this->bus->dispatch(new OrderPlaced($order->id, $order->totalCents()));

        return $order;
    }
}
```

**Почему `bus->dispatch` после `save`:**

Если dispatch **до** save — сообщение в очереди, а заказ ещё не в БД. Worker обработает сообщение, попытается прочитать заказ — не найдёт. Race condition.

Если save **сбрасывается** (exception) — сообщения в очереди, но заказа нет. Worker обработает, не найдёт — ок, но лишний work.

**Идеальный порядок:**

```
save (в БД) → dispatch (в очередь)
```

Если save упадёт — dispatch не будет вызван. Если dispatch упадёт — заказ в БД (можно ретраить).

---

### 6. Подводные камни

#### 6.1. Lazy loading и N+1

```php
// ПЛОХО: N+1
$orders = $repo->findByCustomer(42); // SELECT * FROM orders WHERE customer_id = 42
foreach ($orders as $order) {
    $total = $order->totalCents(); // ← foreach по items → SELECT * FROM order_items WHERE order_id = ?
}
// 1 запрос на orders + N запросов на order_items
```

**Решение:**

```php
// В репозитории: eager join
$entities = $this->em
    ->getRepository(OrderEntity::class)
    ->createQueryBuilder('o')
    ->leftJoin('o.items', 'i')   // ← JOIN
    ->where('o.customerId = :cust')
    ->setParameter('cust', $customerId)
    ->getQuery()
    ->getResult();
// 1 запрос: SELECT o.*, i.* FROM orders o LEFT JOIN order_items i ON ...
```

**Или:**

```php
// В Entity: EAGER (осторожно!)
#[ORM\OneToMany(
    targetEntity: OrderItemEntity::class,
    mappedBy: 'order',
    fetch: 'EAGER',  // ← всегда JOIN, даже когда items не нужны
    cascade: ['persist', 'remove'],
    orphanRemoval: true,
)]
```

`EAGER` — палка о двух концах: нет N+1, но каждый `find()` загружает items, даже если нужен только `status`.

**Мой выбор:** `LAZY` (default) + `JOIN` там, где коллекция нужна.

---

#### 6.2. `orphanRemoval` + `cascade: remove` — двойное удаление

```yaml
// ПЛОХО: лишний
cascade: ['persist', 'remove']
orphanRemoval: true
```

`orphanRemoval: true` **включает** `cascade: remove` автоматически. Указывать оба — избыточно, но не вредно.

**Важно:** `orphanRemoval` работает **только** на `OneToMany` с `mappedBy`. На `ManyToOne` — нет.

---

#### 6.3. `flush()` внутри `save()` — а что если `save()` вызывается дважды?

```php
$order = Order::create(42);
$order = $order->addItem(1, 'Widget', 2, 1000);
$repo->save($order);  // ← persist + flush

$order = $order->addItem(2, 'Gadget', 1, 5000);
$repo->save($order);  // ← persist (уже managed) + flush
```

Второй `save()` — `persist()` на уже managed-объект — **no-op**. `flush()` — записывает изменения. Это **ок**.

Но:

```php
$repo->save($order1);  // flush
$repo->save($order2);  // flush
```

Два flush'а. Можно объединить:

```php
$repo->persist($order1);
$repo->persist($order2);
$repo->flushOnce();  // ← один flush на два объекта
```

На практике: если `save()` — единственная точка flush, и вызывается один раз — ок. Если в цикле — рассмотрите batch.

---

#### 6.4. Bidirectional association и dirty check

```php
// ОДНОСТОРОННИЙ (всё через Order)
#[ORM\OneToMany(targetEntity: OrderItemEntity::class, mappedBy: 'order')]
private Collection $items;

// ДВУСТОРОННИЙ (в OrderItem)
#[ORM\ManyToOne(inverse: 'items')]
private ?OrderEntity $order;
```

**Критично:** `inverse: 'items'` на ManyToOne. Без него Doctrine не знает, что `$order->items` — source of truth, и может создать **лишнюю строку** в `order_items` при `flush()`.

Правильно:

```php
// OrderEntity (owning side для cascade/orphan)
#[ORM\OneToMany(
    targetEntity: OrderItemEntity::class,
    mappedBy: 'order',
    cascade: ['persist'],
    orphanRemoval: true,
)]
private Collection $items;

// OrderItemEntity (owning side для FK)
#[ORM\ManyToOne(inverse: 'items')]
#[ORM\JoinColumn(name: 'order_id', nullable: false)]
private ?OrderEntity $order;
```

**Почему `cascade: ['persist']`, а не `['persist', 'remove']`:**

`orphanRemoval: true` уже делает `remove`. `cascade: ['persist', 'remove']` — избыточно. Но `cascade: ['persist']` **недостаточно**: если вы делаете `$order->items->removeElement($item)` и `orphanRemoval: true` — Doctrine **сам** сделает `remove` на item. `cascade: remove` не нужен.

---

#### 6.5. `removeItem` + `orphanRemoval` + `flush`

```php
// Доменная модель
$order->removeItem($itemId);
// Collection::removeElement() — item выведен из Collection

// Репозиторий
public function save(Order $order): void
{
    $entity = $this->em->find(OrderEntity::class, $order->id);
    $this->syncItems($entity, $order->items());
    $this->em->flush(); // ← orphanRemoval → DELETE FROM order_items WHERE id = ?
}
```

**Проверка:**

```sql
-- Перед save:
SELECT * FROM order_items WHERE order_id = 1;
-- (1, 42, 'Widget', 2, 1000)
-- (2, 99, 'Gadget', 1, 5000)

-- $order->removeItem(1) → Collection: [Gadget]

-- save() → syncItems:
--   1. clear() Collection → orphan: [Widget]
--   2. add(Gadget) → Collection: [Gadget]

-- flush():
--   DELETE FROM order_items WHERE id = 1;  ← orphanRemoval
--   (Gadget — уже managed, не изменялся → no UPDATE)
```

**Важно:** `syncItems` делает `clear()` + `add()`. Это **полный sync**. Если вы хотите **приращение** (только удалить/добавить изменившиеся) — сложнее. Для корзины (10–50 items) полный sync — ок.

---

#### 6.6. Transaction + rollback

```php
public function save(Order $order): void
{
    $this->em->transactional(function (EntityManagerInterface $em) {
        $entity = $this->toEntity($order);
        $this->em->persist($entity);
        // Если здесь exception → rollback → persist отменяется
        // Если flush → commit
        $this->em->flush();
    });
}
```

`transactional()` — обёртка над `beginTransaction() / commit() / rollback()`. Если любой SQL-запрос в транзакции упадёт — **всё** откатывается (order + items).

**Граница транзакции = граница агрегата.** Один `save()` = одна транзакция. Два `save()` = две транзакции.

---

### 7. Итого

```
┌─────────────────────────────────────────────────────────────────┐
│ ДОМЕН (без Doctrine)                                            │
│                                                                 │
│  Order (корень)                                                 │
│  ├── items: Collection<OrderItem>                               │
│  ├── addItem(), removeItem(), confirm()                         │
│  └── Инварианты: status, empty, qty > 0                         │
│                                                                 │
│  OrderItem (дочка)                                              │
│  └── Чистые данные, нет собственного lifecycle                  │
│                                                                 │
│  OrderRepositoryInterface (контракт)                            │
│  └── save(), find(), findByCustomer()                           │
└─────────────────────────────────────────────────────────────────┘
                              │
                    mapping (toEntity / toDomain)
                              │
┌─────────────────────────────────────────────────────────────────┐
│ ИНФРАСТРУКТУРА (Doctrine)                                       │
│                                                                 │
│  OrderEntity (корень)                                           │
│  ├── items: Collection<OrderItemEntity>                         │
│  │   ├── cascade: ['persist']                                   │
│  │   ├── orphanRemoval: true                                    │
│  │   └── fetch: LAZY                                            │
│  └── status, customerId                                         │
│                                                                 │
│  OrderItemEntity (дочка)                                        │
│  └── ManyToOne → OrderEntity (FK)                               │
│                                                                 │
│  DoctrineOrderRepository                                        │
│  ├── save() → persist + flush (ОДНА транзакция)                │
│  ├── find() → toDomain()                                        │
│  └── JOIN при необходимости (N+1)                               │
└─────────────────────────────────────────────────────────────────┘
```

**Ключевые решения:**

| Вопрос | Ответ |
|--------|-------|
| Маппинг | OneToMany + orphanRemoval + cascade persist |
| Flush | Только в репозитории, один раз на `save()` |
| Транзакция | Одна на один агрегат |
| N+1 | LAZY + JOIN в репозитории (не EAGER) |
| Bidirectional | `inverse: 'items'` на ManyToOne |
| Кто мутит | Только корень (`Order`). Дочка — пассивна. |
| Асинхронные effects | После `save()`, вне транзакции |

> ⏱ 48.8s · prompt 40453 tok · output 6760 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о диагностике и лечении N+1 в Doctrine. Это практический вопрос, который хорошо ложится на формат собеседования.

Нужно структурировать ответ:
1. Диагностика — как обнаружить N+1 (инструменты, логи, запросы)
2. Причины — почему возникает
3. Способы решения — JOIN, DQL, QueryBuilder, Batch, Query hints, EAGER, denormalization, cache
4. Trade-offs каждого

Важно быть конкретным — с примерами кода и SQL-запросов. Не абстрактно, а «что видишь в логах» и «как именно чинишь».

Структура:
1. Диагностика (как поймать)
2. Типовые сценарии N+1 (где возникает)
3. Решения с trade-offs
4. Чего НЕ делать

Нужно показать глубину: не просто «используй JOIN», а почему JOIN не всегда работает, когда лучше Batch, когда denormalization оправдана.

</details>

**Кандидат:**

## N+1 в Doctrine: диагностика и лечение

### 1. Диагностика: как поймать

#### 1.1. Включить SQL-логи

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            backtrace: true   # ← покажет, откуда вызов
```

```php
// src/EventListener/SqlLogger.php
use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

final class SqlLogger implements SQLLogger
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function startQuery(string $sql, ?array $params, ?array $types): void
    {
        $this->logger->debug('[SQL]', [
            'sql'    => $sql,
            'params' => $params,
            'trace'  => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
        ]);
    }

    public function stopQuery(): void {}
}
```

```yaml
# config/services.yaml
services:
    App\EventListener\SqlLogger:
        tags:
            - { name: doctrine.logger }
```

**Что ищете в логах:**

```
[SQL] SELECT o.id, o.customer_id, o.status FROM orders o WHERE o.customer_id = 42
[SQL] SELECT oi.* FROM order_items oi WHERE oi.order_id = 1
[SQL] SELECT oi.* FROM order_items oi WHERE oi.order_id = 2
[SQL] SELECT oi.* FROM order_items oi WHERE oi.order_id = 3
[SQL] SELECT oi.* FROM order_items oi WHERE oi.order_id = 4
```

**Признак N+1:** один «родительский» запрос, за которым следуют N идентичных (по структуре) дочерних запросов, каждый с **одним** `WHERE id = ?`.

#### 1.2. Doctrine QueryBuilder + `getSQL()`

```php
$query = $em->getRepository(OrderEntity::class)
    ->createQueryBuilder('o')
    ->where('o.customerId = :c')
    ->getQuery();

echo $query->getSQL();
// SELECT o.id, o.customer_id, o.status FROM orders o WHERE o.customer_id = ?
// ← если нет JOIN с order_items — потенциальный N+1
```

#### 1.3. `php bin/console doctrine:query`

```bash
# Посмотреть, какие запросы реально выполнялись
php bin/console doctrine:query --explain "SELECT * FROM orders"
```

#### 1.4. pg_stat_statements (PostgreSQL)

```sql
SELECT
    calls,
    total_exec_time,
    mean_exec_time,
    LEFT(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Признак N+1:** `calls` на уровне тысяч/сотен тысяч, `mean_exec_time` микросекунды, `total_exec_time` высокий. Это значит: один и тот же быстрый запрос выполняется многократно.

```
 calls | total_exec_time | mean_exec_time | query
-------+-----------------+----------------+----------------------------------
 52841 |       18923.4   |        0.358   | SELECT oi.* FROM order_items oi
       |                 |                | WHERE oi.order_id = $1
```

52 841 вызов одного запроса — это N+1.

#### 1.5. Инструменты

| Инструмент | Что показывает |
|------------|---------------|
| `SQL Logger` + backtrace | Откуда вызов (класс, метод, строка) |
| `pg_stat_statements` | Какие запросы самые частые |
| Blackfire / Xdebug profiler | Стacks с таймингами SQL-вызовов |
| Doctrine `DebugStackListener` | Агрегирование: сколько запросов, суммарное время |

---

### 2. Типовые сценарии N+1

#### Сценарий 1: Collection в Entity

```php
// Entity
#[ORM\OneToMany(targetEntity: OrderItemEntity::class, mappedBy: 'order', fetch: 'LAZY')]
private Collection $items;

// Контроллер
$orders = $em->getRepository(OrderEntity::class)
    ->findBy(['customerId' => 42]);

foreach ($orders as $order) {
    echo $order->getItems()->count();  // ← N+1: SELECT per order
}
```

**SQL:**
```
1. SELECT o.* FROM orders o WHERE o.customer_id = 42          -- 1
2. SELECT oi.* FROM order_items oi WHERE oi.order_id = 1      -- N
3. SELECT oi.* FROM order_items oi WHERE oi.order_id = 2
4. SELECT oi.* FROM order_items oi WHERE oi.order_id = 3
...
```

#### Сценарий 2: ManyToOne (parent)

```php
$orders = $em->getRepository(OrderEntity::class)->findAll();

foreach ($orders as $order) {
    echo $order->getCustomer()->getEmail();  // ← N+1: SELECT customer per order
}
```

**SQL:**
```
1. SELECT o.* FROM orders o
2. SELECT c.* FROM customers c WHERE c.id = 1   -- N
3. SELECT c.* FROM customers c WHERE c.id = 1   -- N (дубликаты!)
4. SELECT c.* FROM customers c WHERE c.id = 3
...
```

#### Сценарий 3: DQL без JOIN

```php
$q = $em->createQuery('SELECT o FROM App\Entity\OrderEntity o WHERE o.customerId = :c');
$orders = $q->getResult();

foreach ($orders as $order) {
    $items = $order->getItems(); // ← N+1
}
```

#### Сценарий 4: Фильтрация по child-полю

```php
// «Заказы, содержащие товар с productId = 42»
$q = $em->createQuery('SELECT o FROM App\Entity\OrderEntity o');
$orders = $q->getResult();

$result = array_filter($orders, function ($o) {
    foreach ($o->getItems() as $item) {  // ← N+1
        if ($item->getProductId() === 42) return true;
    }
    return false;
});
```

Здесь N+1 **внутри фильтрации**. Даже JOIN не поможет напрямую — нужен `WHERE EXISTS` или `JOIN` + `DISTINCT`.

#### Сценарий 5: API с сериализацией

```php
// API Platform / Symfony Serializer
$json = $serializer->serialize($order, JsonFormat::MIME_TYPE);
// Serializer вызывает getOrder()->getItems()->getCustomer()->getAddress()
// Каждый вызов — потенциальный lazy load → N+1
```

---

### 3. Способы решения

---

#### Решение 1: `JOIN` в QueryBuilder

**Было:**

```php
$orders = $em->getRepository(OrderEntity::class)
    ->findBy(['customerId' => 42]);
```

**Стало:**

```php
$orders = $em->getRepository(OrderEntity::class)
    ->createQueryBuilder('o')
    ->leftJoin('o.items', 'i')
    ->where('o.customerId = :c')
    ->setParameter('c', 42)
    ->getQuery()
    ->getResult();
```

**SQL:**
```sql
SELECT o.*, i.* FROM orders o
LEFT JOIN order_items i ON i.order_id = o.id
WHERE o.customer_id = 42
```

| Плюс | Минус |
|------|-------|
| Один запрос вместо N+1 | Результат — плоский массив, `items` загружены |
| Простое, читаемое | Если `items` большие (100+ строк на заказ) — SELECT тяжёлый |
| Не меняет Entity | `DISTINCT` не нужен при `leftJoin` (Doctrine дедуплицирует) |
| Работает с `findBy()` → `createQueryBuilder()` | Если нужно фильтровать по `i` — `leftJoin` → `innerJoin` |

**Ограничение:** JOIN работает, когда **направление** одно: `Order → items` или `Order → customer`. Если нужен **двусторонний** граф (A→B→C→D), JOIN растёт экспоненциально.

---

#### Решение 2: `DQL` с `JOIN` + фильтрация

Для сценария 4 (фильтрация по child):

```php
$orders = $em->createQuery('
    SELECT DISTINCT o
    FROM App\Entity\OrderEntity o
    JOIN o.items i
    WHERE i.productId = :pid
')
->setParameter('pid', 42)
->getResult();
```

**SQL:**
```sql
SELECT DISTINCT o.*
FROM orders o
INNER JOIN order_items i ON i.order_id = o.id
WHERE i.product_id = 42
```

| Плюс | Минус |
|------|-------|
| Фильтрация на стороне БД | `DISTINCT` — накладные расходы на сортировку |
| N+1 отсутствует | Если `items` > 1 на заказ — `DISTINCT` убирает дубли |
| Масштабируется | `DISTINCT` по всем колонкам `o` (может быть дорого) |

**Альтернатива без `DISTINCT`:**

```php
// Если нужен только id заказов:
$ids = $em->createQuery('
    SELECT o.id
    FROM App\Entity\OrderEntity o
    JOIN o.items i
    WHERE i.productId = :pid
')
->setParameter('pid', 42)
->getResult();

// Затем:
$orders = $em->getRepository(OrderEntity::class)->findById($ids);
```

Два запроса вместо N+1, без `DISTINCT`.

---

#### Решение 3: `Batch` (Doctrine 3.10+)

Для случаев, когда JOIN не подходит (фильтрация по child, сложный граф):

```php
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Batching\BatchingHelper;

// Батчинг: Doctrine выполняет N+1 запросов, но батчами по N
$helper = new BatchingHelper($em, 100); // батч = 100
$orders = $helper->getBatchedResult(
    $em->getRepository(OrderEntity::class)
        ->createQueryBuilder('o')
        ->where('o.customerId = :c')
        ->setParameter('c', 42)
        ->getQuery()
);
```

**SQL:**
```
1. SELECT o.* FROM orders o WHERE o.customer_id = 42
2. SELECT oi.* FROM order_items oi WHERE oi.order_id IN (1, 2, 3, ..., 100)
3. SELECT oi.* FROM order_items oi WHERE oi.order_id IN (101, 102, ..., 200)
```

| Плюс | Минус |
|------|-------|
| N+1 → N/batch (1000 запросов → 10) | `IN (...)` с 100+ значениями — медленнее, чем 100 отдельных |
| Не меняет Entity | Не работает для `ManyToOne` (только `OneToMany`) |
| Проще, чем DQL + JOIN | Батч-размер — тюнинг (100? 500? 1000?) |
| Подходит для `findAll()` | В Doctrine 2 — через `Doctrine\Common\Collections\ExpressionBuilder` (хак) |

---

#### Решение 4: `EAGER` fetch

```php
#[ORM\OneToMany(
    targetEntity: OrderItemEntity::class,
    mappedBy: 'order',
    fetch: 'EAGER',  // ← всегда JOIN
    cascade: ['persist'],
    orphanRemoval: true,
)]
private Collection $items;
```

**SQL (каждый `find` / `findAll`):**
```sql
SELECT o.*, i.* FROM orders o
LEFT JOIN order_items i ON i.order_id = o.id
```

| Плюс | Минус |
|------|-------|
| **Нигде** не надо помнить про JOIN | **Каждый** запрос на `OrderEntity` тянет `items` |
| Забывчивость разработчика защищена | `findById(1)` → SELECT items, хотя нужен только `status` |
| Работает из любого контекста | Трудно выключить (нужен `getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD)`) |
| | Миграции: `EAGER` + `orphanRemoval` — сложнее отладка |

**Когда уместно:** `OrderItem` — всегда нужен (корзина, заказ). `Order` не существует без `items`.

**Когда нет:** `Customer → addresses` (один адрес нужен, остальные — нет).

---

#### Решение 5: Partial Object (Query Hints)

```php
// Загружаем только нужные поля, не трогая items
$query = $em->createQuery('
    SELECT o.id, o.customerId, o.status
    FROM App\Entity\OrderEntity o
    WHERE o.customerId = :c
')
->setParameter('c', 42);

$orders = $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
    ->getResult();
```

**SQL:**
```sql
SELECT o.id, o.customer_id, o.status FROM orders o WHERE o.customer_id = 42
-- ← items НЕ загружаются
```

| Плюс | Минус |
|------|-------|
| Минимальный SELECT | `$order->getItems()` — **не инициализирован**, `Error` при обращении |
| Быстрее, чем полный JOIN | Нельзя вызвать `$order->totalCents()` (нет items) |
| Подходит для списков/таблиц | Легко сломать: кто-то в контроллере вызовет `->getItems()` → 500 |

**Когда уместно:** таблица заказов в админке, где нужны только `id`, `status`, `customerName`. Items — не нужны.

---

#### Решение 6: Денормализация

```sql
ALTER TABLE orders ADD COLUMN total_amount_cents INTEGER GENERATED ALWAYS AS (
    -- не работает в Postgres для computed
)
```

**Ручная денормализация:**

```php
// В OrderEntity
#[ORM\Column(type: 'integer')]
private int $totalCents; // ← дублирует SUM(items.qty * items.priceCents)

#[ORM\Column(type: 'integer')]
private int $itemsCount; // ← дублирует COUNT(items)
```

```php
// В доменной модели — синхронизация
public function addItem(...): self
{
    // ...
    $this->totalCents = $this->totalCents() + $item->lineTotalCents();
    $this->itemsCount = $this->itemsCount + 1;
}
```

| Плюс | Минус |
|------|-------|
| **Никаких N+1** для `total` и `count` | Дублирование данных |
| `SELECT total FROM orders` — один запрос | При изменении item — обновить `total` в `orders` |
| Индекс на `total_amount_cents` — быстрая фильтрация | Инвариант: `total = SUM(items)` — нужно гарантировать |
| Подходит для отчётов, аналитики | Миграции: при добавлении поля — обновить `total` для всех строк |

**Когда уместно:** `total`, `items_count`, `last_item_name` — поля, которые часто запрашивают, а считать из `items` дорого.

**Когда нет:** если `items` меняются часто (корзина) — денормализация создаёт гонки.

---

#### Решение 7: Cache (result cache / query cache)

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        result_cache:
            adapter: cache.app.result
        query_cache:
            adapter: cache.app.query
```

```php
// Ручное кеширование
$cache = new ResultCache();
$orders = $em->createQuery('SELECT o FROM App\Entity\OrderEntity o WHERE o.customerId = :c')
    ->setParameter('c', 42)
    ->getResult($cache); // ← 2-й вызов — из кэша
```

| Плюс | Минус |
|------|-------|
| 2-й и далее запросы — 0 ms | Инвалидация: при изменении `items` — кэш устарел |
| Не меняет код | `result_cache` кеширует **результат**, не **запрос**. При N+1 — кэширует каждый из N+1 |
| Простое включение | Redis/Redis cluster — дополнительная инфраструктура |
| | TTL: как долго кэш актуален? |

**Важно:** cache **не решает** N+1 при **первом** запросе. Он помогает при **повторных** запросах. Для первого запроса — JOIN.

---

#### Решение 8: Materialized View / Read Model

Для аналитических запросов:

```sql
-- Materialized view (PostgreSQL)
CREATE MATERIALIZED VIEW orders_summary AS
SELECT
    o.id,
    o.customer_id,
    c.email AS customer_email,
    o.status,
    o.created_at,
    COUNT(oi.id) AS items_count,
    SUM(oi.qty * oi.price_cents) AS total_cents
FROM orders o
JOIN customers c ON c.id = o.customer_id
LEFT JOIN order_items oi ON oi.order_id = o.id
GROUP BY o.id;

CREATE UNIQUE INDEX idx_orders_summary_id ON orders_summary (id);
```

```php
// Читаем из view
$rows = $em->getConnection()->fetchAssociative('
    SELECT * FROM orders_summary WHERE customer_id = :c
', ['c' => 42]);
```

| Плюс | Minus |
|------|-------|
| **Один запрос**, никаких JOIN в рантайме | Обновление: `REFRESH MATERIALIZED VIEW CONCURRENTLY` — блокирует запись |
| Подходит для read-heavy сценариев | Задержка: view не обновляется в реальном времени |
| Индексы на view — быстрая фильтрация | Дублирование: `orders` + `orders_summary` |
| | Сложность: при изменении схемы — обновить view |

**Когда уместно:** дашборды, отчёты, read-heavy API (1000+ req/min на один запрос).

**Когда нет:** CRUD-приложение, данные меняются часто, нужна актуальность.

---

### 4. Сводная таблица

| Сценарий | Лучшее решение | Почему |
|----------|---------------|--------|
| `Order → items` (корзина) | **JOIN** | Простое, один запрос, items всегда нужны |
| `Order → customer` (email) | **JOIN** | One-to-many, один JOIN |
| Фильтрация по child (`product_id`) | **DQL + JOIN + DISTINCT** | Фильтр на стороне БД |
| `findAll()` без фильтра | **Batch** или **EAGER** | N=1000+, JOIN тяжёлый |
| Список (id, status, total) | **Partial Object** или **Denormalization** | Не нужны items |
| Read-heavy дашборд | **Materialized View** | Один запрос, индекс |
| Повторные одинаковые запросы | **Result Cache** | 2-й запрос — 0 ms |
| API с сериализацией | **JOIN** + **Partial Object** | Serializer не вызывает lazy load |

---

### 5. Чего НЕ делать

| Антипаттерн | Почему плохо |
|-------------|-------------|
| `EAGER` на всё | Каждый `findById(1)` тянет 100 items, хотя нужен только `status` |
| `IN (...)` с 10 000 значений | PostgreSQL имеет лимит `max_indexed_fields`, SQL-парсер упирается |
| Кэш без инвалидации | Читаете устаревшие данные, отладка — боль |
| `Doctrine\ORM\EntityManager` в каждом сервисе | Невозможно подменить в тестах, не видно, кто делает запросы |
| `->getResult()` + `array_filter` по child | N+1 **внутри** PHP, JOIN не помогает. Нужен `WHERE EXISTS` или `JOIN` |
| Денормализация без триггера/события | `total` расходит с `SUM(items)` — тихий баг |
| `SELECT *` + JOIN на 5 таблиц | Тяжёлый SELECT, медленная десериализация |

---

### 6. Чеклист

```
1. Включите SQL-логи → найдите N+1
2. Определите направление: OneToMany? ManyToOne?
3. Задайте вопрос: «items/customer всегда нужны?»
   ├── Да → JOIN
   └── Нет → Partial Object / EAGER (осторожно)
4. Фильтрация по child?
   ├── Да → DQL + JOIN + DISTINCT
   └── Нет → JOIN
5. N > 100?
   ├── Да → Batch (100 per batch)
   └── Нет → JOIN
6. Read-heavy, данные не меняются часто?
   ├── Да → Materialized View
   └── Нет → JOIN
7. Повторные запросы?
   ├── Да → Result Cache
   └── Нет → JOIN
```

**Золотое правило:** N+1 — это **симптом**, не болезнь. Болезнь — «доменная модель не совпадает с формой запроса». JOIN — пластырь. Денормализация / Read Model — лечение.

> ⏱ 43.7s · prompt 46974 tok · output 5212 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

Хороший вопрос — о соотношении валидации в PHP (приложение) и ограничений в БД. Это классический спор "application layer vs database layer".

Ключевые моменты, которые нужно осветить:
1. Почему БД-ограничения НЕ заменяют PHP-валидацию, но дополняют её
2. Что происходит, если только PHP-валидация (уязвимости)
3. Конкретные средства PostgreSQL: CHECK, UNIQUE, NOT NULL, FK, EXCLUDE, triggers, advisory locks, SERIALIZABLE isolation
4. Когда каждое уместно
5. Trade-offs: производительность, миграции, отладка

Структурирую ответ так:
- Почему PHP-валидации недостаточно (5 причин)
- Таблица средств PostgreSQL с примерами
- Конкретные сценарии, где каждое критично
- Когда БД-ограничения вредят
- Практический подход (чеклист)

Важно: не абстрактно, а с конкретными SQL-примерами и PHP-кодом, показывающим, что ломается без БД-ограничений.

Нужно показать:
- Race conditions (два запроса одновременно)
- SQL-инъекции / обход через raw SQL
- Админ-панель / psql
- Миграции / backfill
- Distributed systems (несколько сервисов пишут в одну БД)
- Advisory locks для idempotency
- SERIALIZABLE isolation для финансовых операций

Сделаю структурированно: сначала "почему недостаточно", потом "что конкретно", потом "когда вредят", потом "практика".

</details>

**Кандидат:**

## БД-ограничения: не замена, а страховка

### 1. Почему PHP-валидации недостаточно

PHP-валидация — **первый барьер**. Она хороша для UX (быстрая ошибка, понятное сообщение), но **не является границой защиты**. Вот почему:

#### 1.1. Race condition

```php
// OrderService.php
public function charge(int $orderId, int $amountCents): void
{
    $order = $this->repo->find($orderId);
    
    if ($order->status !== OrderStatus::Confirmed) {
        throw new OrderNotReadyException($orderId);
    }

    if ($order->totalCents < $amountCents) {
        throw new InsufficientFundsException($orderId);
    }

    // ← МЕЖДУ ПРОВЕРКОЙ И ОБНОВЛЕНИЕМ — ОКНО
    //    Другой процесс/поток может изменить статус

    $order->status = OrderStatus::Paid;
    $this->repo->save($order);
}
```

**Сценарий:** два платежа приходят одновременно. Оба читают `status = 'confirmed'`. Оба проходят валидацию. Оба пишут `status = 'paid'`. Двойное списание.

**Решение в БД:**

```sql
-- Оптимистичная блокировка через version
ALTER TABLE orders ADD COLUMN version INTEGER NOT NULL DEFAULT 1;

-- UPDATE с условием
UPDATE orders
SET status = 'paid', version = version + 1
WHERE id = :id AND version = :expected AND status = 'confirmed';

-- Если affected rows = 0 → кто-то опередил
```

#### 1.2. Raw SQL / админ-панель / psql

PHP-валидация не выполняется, если кто-то:
- Открывает `psql` и делает `UPDATE orders SET total_cents = -5000 WHERE id = 42`
- Пишет raw SQL в миграции: `$em->getConnection()->executeStatement('...')`
- Использует админ-панель (AdminLTE, API Platform) с `PUT /orders/42?total=-100`
- Бакап/репликация переносит данные из другой среды

**Решение в БД:**

```sql
ALTER TABLE orders
ADD CONSTRAINT chk_total_non_negative CHECK (total_cents >= 0);
```

Теперь `UPDATE orders SET total_cents = -5000` → `ERROR: new row violates check constraint`.

#### 1.3. Несколько сервисов / процессы

Микросервисная архитектура: `payment-service` и `order-service` пишут в одну БД. `payment-service` не знает про PHP-валидацию `order-service`.

**Решение:** БД-ограничения — **единственный общий контракт**.

#### 1.4. Backfill / миграции

```php
// Migration: добавляем поле с default
// Если в коде есть 10 000 старых записей с total_cents = 0
// и валидация требует total_cents > 0 — миграция упадёт
```

БД-ограничение с `CHECK (total_cents >= 0)` — `0` допустимо. БД-ограничение `CHECK (total_cents > 0)` — миграция требует сначала backfill, потом добавление constraint.

#### 1.5. Удаление «в обход»

```php
// Кто-то удаляет OrderItem напрямую, минуя aggregate
$em->remove($item);
$em->flush();
// Order->items() теперь «дырявый»
```

**Решение:** `ON DELETE CASCADE` или `FK` — БД не даст удалить item, если он часть aggregate (через trigger или composite constraint).

---

### 2. Средства PostgreSQL: что и когда

#### 2.1. `NOT NULL`

```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_customer_id_not_null CHECK (customer_id IS NOT NULL);
-- или
ALTER TABLE orders ALTER COLUMN customer_id SET NOT NULL;
```

**Зачем:** PHP может передать `null` (unset, typo, race). БД — нет.

**Когда:** все обязательные поля. Всегда. Без исключений.

---

#### 2.2. `CHECK` constraint

```sql
-- Простые инварианты
ALTER TABLE orders
    ADD CONSTRAINT chk_qty_positive CHECK (qty > 0),
    ADD CONSTRAINT chk_price_positive CHECK (price_cents > 0),
    ADD CONSTRAINT chk_status_valid CHECK (status IN ('draft', 'confirmed', 'shipped', 'cancelled'));

-- Связанные поля
ALTER TABLE order_items
    ADD CONSTRAINT chk_line_total CHECK (line_total_cents = qty * price_cents);

-- Бизнес-правило
ALTER TABLE subscriptions
    ADD CONSTRAINT chk_trial_expiry CHECK (trial_end_at > created_at);
```

**Зачем:** инвариант, который «не может» быть нарушен, но **может** (bug, race, raw SQL).

**Когда:** любой инвариант, который:
- Не зависит от контекста (не «если user.role = admin, то...»)
- Должен быть **всегда** верен, независимо от того, кто пишет

**Ограничение:** `CHECK` — deterministic function, не может вызывать внешние функции, не может ссылаться на другие таблицы.

---

#### 2.3. `UNIQUE` constraint

```sql
ALTER TABLE users
    ADD CONSTRAINT uq_email UNIQUE (email);

ALTER TABLE orders
    ADD CONSTRAINT uq_idempotency_key UNIQUE (idempotency_key);
```

**Зачем:** «один email — один пользователь», «один idempotency key — одна операция».

**Когда:** естественные ключи, idempotency, уникальные токены.

**Сравнение с PHP:**

```php
// PHP: проверяем уникальность ПЕРЕД INSERT
$exists = $repo->findOneBy(['email' => $email]);
if ($exists !== null) {
    throw new DuplicateEmailException($email);
}
$em->persist($newUser);
$em->flush();
// ← МЕЖДУ findOneBy И flush — ОКНО
//   Другой процесс может вставить тот же email
```

```sql
-- БД: атомарная гарантия
INSERT INTO users (email) VALUES ('test@example.com');
-- ERROR: duplicate key value violates unique constraint "uq_email"
```

**Важно:** `UNIQUE` — это **главное** средство против race conditions на уровне «один записи». PHP-проверка + INSERT — **не атомарны**. `UNIQUE` — **атомарна**.

---

#### 2.4. Foreign Key

```sql
ALTER TABLE order_items
    ADD CONSTRAINT fk_items_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE orders
    ADD CONSTRAINT fk_orders_customer
    FOREIGN KEY (customer_id) REFERENCES customers(id)
    ON DELETE RESTRICT;
```

**Зачем:**
- `ON DELETE CASCADE` — удаление `order` удаляет `items` (без orphan rows)
- `ON DELETE RESTRICT` — нельзя удалить `customer`, пока у него есть заказы
- Валидация: `order_items.order_id` **всегда** ссылается на существующий `order`

**Сравнение с PHP:**

```php
// PHP: проверяем существование order ПЕРЕД вставкой item
$order = $repo->find($orderId);
if ($order === null) {
    throw new OrderNotFoundException($orderId);
}
// ← ОКНО: другой процесс удаляет order
$item = new OrderItem($orderId, ...);
$em->persist($item);
$em->flush();
// ← item с orphan order_id в БД!
```

**FK устраняет этот класс багов на уровне ядра.**

---

#### 2.5. `EXCLUDE` constraint (PostgreSQL-specific)

```sql
-- Не允许 пересечение интервалов
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    resource_id INTEGER NOT NULL,
    start_at TIMESTAMPTZ NOT NULL,
    end_at TIMESTAMPTZ NOT NULL,
    EXCLUDE USING gist (
        resource_id WITH =,
        daterange(start_at, end_at) WITH &&
    )
);
```

**Зачем:** «одна комната не может быть забронирована на пересекающиеся интервалы».

**Почему PHP не справится:**

```php
// PHP: SELECT + INSERT
$conflict = $repo->findBy(['resourceId' => $rid, 'startAt' => ['<', $end], 'endAt' => ['>', $start]]);
if ($conflict !== null) {
    throw new ConflictException();
}
$em->persist($newBooking);
$em->flush();
// ← ОКНО: два процесса одновременно забронировали пересекающиеся слоты
```

```sql
-- БД: атомарно
INSERT INTO bookings (resource_id, start_at, end_at) VALUES (1, '10:00', '11:00');
INSERT INTO bookings (resource_id, start_at, end_at) VALUES (1, '10:30', '11:30');
-- ERROR: conflicting key value violates exclusion constraint
```

**Когда:** интервалы, диапазоны, non-overlapping constraints (календари, слоты, временные окна).

**Ограничение:** требует `btree_gist` extension. Не переносимый (PostgreSQL-only).

---

#### 2.6. `GENERATED ALWAYS AS` (computed columns)

```sql
ALTER TABLE order_items
    ADD COLUMN line_total_cents INTEGER
    GENERATED ALWAYS AS (qty * price_cents) STORED;
```

**Зачем:** `line_total_cents` **невозможно** задать вручную. Всегда = `qty * price_cents`.

**Сравнение с PHP:**

```php
// PHP: вычисляем, но кто-то может «забыть»
$item->setQty(2);
$item->setPrice(1000);
// Забыли: $item->setLineTotal(2000);
$em->flush();
// line_total_cents = 0 или NULL — невалидное состояние
```

```sql
-- БД: невозможно забыть
INSERT INTO order_items (qty, price_cents) VALUES (2, 1000);
-- line_total_cents = 2000, автоматически
```

**Когда:** производные значения, которые всегда вычисляются из других полей.

**Ограничение:** `STORED` — занимает место. `VIRTUAL` (PostgreSQL 12+) — не индексируется.

---

#### 2.7. Triggers + Functions

```sql
CREATE FUNCTION fn_update_order_total()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE orders
    SET total_cents = (
        SELECT COALESCE(SUM(qty * price_cents), 0)
        FROM order_items
        WHERE order_id = NEW.id
    )
    WHERE id = NEW.id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_update_order_total
    AFTER INSERT OR UPDATE OR DELETE ON order_items
    FOR EACH ROW
    EXECUTE FUNCTION fn_update_order_total();
```

**Зачем:** денормализованное поле (`total_cents` в `orders`) обновляется **автоматически** при изменении `order_items`. PHP-код **не должен** об этом помнить.

**Сравнение с PHP:**

```php
// PHP: нужно помнить об обновлении total
$order->addItem($item);
$order->totalCents = $order->items()->reduce(fn($sum, $i) => $sum + $i->lineTotal(), 0);
$em->flush();
// ← если кто-то забудет обновить total — тихий баг
```

**Когда:** денормализация, которая **должна** быть консистентной, но вычисление дорогое (SUM по коллекции).

**Ограничение:**
- Трудно отладить (ошибка в trigger — opaque message)
- Трудно тестировать (нужна реальная БД)
- Не переносимый (PostgreSQL-specific)
- При массовых операциях (`COPY`, bulk INSERT) — trigger на каждую строку → медленно

---

#### 2.8. `SERIALIZABLE` isolation level

```sql
BEGIN ISOLATION LEVEL SERIALIZABLE;

-- Читаем баланс
SELECT balance FROM accounts WHERE id = 42;
-- = 1000

-- Списываем
UPDATE accounts SET balance = balance - 500 WHERE id = 42;

-- Кредитруем другого
UPDATE accounts SET balance = balance + 500 WHERE id = 99;

COMMIT;
```

**Зачем:** при `SERIALIZABLE` PostgreSQL гарантирует, что **последовательность** транзакций эквивалентна последовательному выполнению. Если два процесса одновременно списывают с одного счёта — один из них получит `SerializationFailure`, может ретраить.

**Сравнение с `READ COMMITTED` (default):**

```
T1: SELECT balance WHERE id=42  → 1000
T2: SELECT balance WHERE id=42  → 1000
T1: UPDATE balance = 1000 - 500 = 500
T2: UPDATE balance = 1000 - 500 = 500
-- Итого: 500, а должно быть 0. Двойное списание!
```

**При `SERIALIZABLE`:** T2 получит `ERROR: could not serialize access due to concurrent update` → ретрайт → читает 500 → списывает 500 → итого 0.

**Когда:** финансовые операции, инвентарь, «один ресурс — один победитель».

**Ограничение:**
- `SerializationFailure` → нужен retry loop (3–5 попыток)
- При высокой конкурентности → много ретраев → деградация
- Альтернатива: `SELECT ... FOR UPDATE` (pessimistic lock)

---

#### 2.9. Advisory Locks

```php
// src/Service/IdempotencyService.php
final class IdempotencyService
{
    public function __construct(
        private readonly Connection $db,
    ) {}

    public function withLock(string $key, callable $fn): mixed
    {
        // pg_try_advisory_lock — non-blocking
        $locked = $this->db->fetchOne(
            'SELECT pg_try_advisory_lock(:key)',
            ['key' => $this->hashToBigInt($key)],
        );

        if ($locked !== 't') {
            throw new LockAlreadyHeldException($key);
        }

        try {
            return $fn();
        } finally {
            $this->db->executeStatement(
                'SELECT pg_advisory_unlock(:key)',
                ['key' => $this->hashToBigInt($key)],
            );
        }
    }

    private function hashToBigInt(string $key): int
    {
        return (int) substr(md5($key), 0, 15);
    }
}
```

```php
// Использование
$this->idempotency->withLock('charge:order_42', function () {
    // списание, отправка платежа
    // только один процесс выполнит это одновременно
});
```

**Зачем:** «только один процесс может одновременно выполнять операцию X для сущности Y». Без блокировки БД.

**Сравнение с `FOR UPDATE`:**

| Аспект | `FOR UPDATE` | Advisory Lock |
|--------|-------------|---------------|
| Блокирует строку | Да | Нет |
| Блокирует на уровне таблицы | Нет | Нет (session-level) |
| Может блокировать **несколько** таблиц | Нет | Да (один lock = одна «секция») |
| Если процесс упадёт | Авто-освобождение (rollback) | Авто-освобождение (session end) |
| Гибкость | Только на строку | На что угодно (ключ — bigint) |
| Отладка | `pg_locks` | `pg_locks` (typid = 'advisory') |

**Когда:** idempotency, «один процесс на ресурс», распределённые операции.

---

### 3. Когда БД-ограничения **вредят**

| Ситуация | Почему вредят |
|----------|--------------|
| **Миграции с bulk-данными** | `CHECK (status IN (...))` — при добавлении нового статуса нужно сначала ALTER, потом backfill. Без constraint — быстрая миграция |
| **Тесты** | Unit-тесты с in-memory БД (SQLite) не поддерживают `EXCLUDE`, `GENERATED ALWAYS`, `advisory locks`. Тесты ломаются |
| **Производительность при bulk** | Trigger на `INSERT` — при `COPY` 100 000 строк → 100 000 вызовов trigger. Медленно |
| **Отладка** | Ошибка в trigger — opaque message: `ERROR: new row violates check constraint`. Не видно, какой PHP-код вызвал |
| **Много инвариантов** | 50 `CHECK` constraint на таблицу — `EXPLAIN` показывает, что планировщик не может оптимизировать |
| **Read-heavy workload** | `UNIQUE` index — лишний индекс на каждый INSERT/UPDATE. При 1000 req/sec — 1000 лишних индекс-обновлений |

**Правило:** БД-ограничения — **страховка**, не **первичный механизм**. Первичный механизм — PHP-валидация в доменной модели. БД-ограничение — «а если PHP-код ошибётся».

---

### 4. Практический чеклист

```
Каждый раз, когда пишете PHP-валидацию, спросите:

1. Можно ли это выразить как CHECK constraint?
   ├── Да → добавьте в БД (страховка)
   └── Нет → только PHP (контекстная логика)

2. Это уникальность?
   ├── Да → UNIQUE constraint (обязательно)
   └── Нет → PHP

3. Это FK / каскадное удаление?
   ├── Да → FK + ON DELETE (обязательно)
   └── Нет → PHP

4. Это интервалы / non-overlapping?
   ├── Да → EXCLUDE constraint
   └── Нет → PHP + FOR UPDATE

5. Это денормализованное поле?
   ├── Да → GENERATED ALWAYS или trigger
   └── Нет → PHP

6. Это «только один процесс»?
   ├── Да → advisory lock или FOR UPDATE
   └── Нет → PHP

7. Это финансовая операция с конкурентностью?
   ├── Да → SERIALIZABLE + retry
   └── Нет → READ COMMITTED + FOR UPDATE
```

---

### 5. Итог

| Уровень | Что делает | Пример |
|---------|-----------|--------|
| **Доменная модель** | Валидация бизнес-правил, быстрые ошибки | `Money::of(-50)` → `Exception` |
| **PHP-сервис** | Оркестрация, контекстная логика | «Если user.role = admin, то скидка 50%» |
| **БД-ограничения** | **Страховка** от bugs, races, raw SQL | `CHECK (total >= 0)`, `UNIQUE (email)`, `FK` |
| **БД-механизмы** | **Гарантии**, которые PHP не может дать | `SERIALIZABLE`, `EXCLUDE`, `advisory locks`, `GENERATED` |

**Принцип:** PHP-валидация — для **UX** (быстрая, понятная ошибка). БД-ограничения — для **правильности** (гарантия, что данные не нарушат инвариант, независимо от того, кто и как пишет).

Они **дополняют** друг друга, а не заменяют.

> ⏱ 42.6s · prompt 52003 tok · output 5002 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе диагностики и оптимизации медленных SQL-запросов в PostgreSQL. Это продолжение собеседования PHP-бэкенда. Нужно дать систематизированный ответ: процесс (пошагово), инструменты, типовые причины, примеры.

Структура:
1. Как обнаруживаю (инструменты мониторинга)
2. EXPLAIN ANALYZE — разбор плана
3. Типовые причины (от частых к редким)
4. Инструменты (pg_stat_statements, pg_locks, autovacuum, и т.д.)
5. Примеры реальных случаев

Надо быть конкретным, с SQL-примерами, не абстрактным. Показать, что я реально это делаю, а не просто перечисляю инструменты.

</details>

**Кандидат:**

## Диагностика медленных запросов в PostgreSQL

### 1. Обнаружение: откуда я узнаю, что запрос медленный

#### 1.1. При поступлении жалобы «страница тормозит»

```bash
# 1. Включаю SQL-логи (если ещё не включены)
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            backtrace: true

# 2. Смотрю, какие запросы выполнялись и сколько длились
# В логах: [SQL] + elapsed time
```

#### 1.2. `pg_stat_statements` — глобальный вид

```sql
-- Расширение (один раз)
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Топ-20 самых медленных запросов (по суммарному времени)
SELECT
    LEFT(query, 120) AS query,
    calls,
    round(total_exec_time::numeric, 2) AS total_ms,
    round(mean_exec_time::numeric, 2) AS mean_ms,
    round(max_exec_time::numeric, 2) AS max_ms,
    rows,
    round(shared_blks_hit::numeric * 100 /
          NULLIF(shared_blks_hit + shared_blks_read, 0), 2) AS hit_ratio
FROM pg_stat_statements
WHERE query NOT ILIKE '%pg_stat_statements%'
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Что читаю в результате:**

| Колонка | Что значит |
|---------|-----------|
| `calls` | Сколько раз выполнялся. 50 000 вызовов × 1 мс = 50 с суммарно — это проблема |
| `mean_ms` | Среднее время. Если `mean` < 1 мс, но `calls` огромные — N+1 или hot query |
| `max_ms` | Пиковое время. Если `max` >> `mean` — есть outlier (cold cache, lock, bloat) |
| `rows` | Сколько строк вернул. 0 строк, но 500 мс — индекс не сработал |
| `hit_ratio` | `shared_blks_hit / (hit + read)`. < 95% — идёт чтение с диска |

#### 1.3. `pg_locks` — если запрос «висит»

```sql
-- Кто блокирует кого
SELECT
    blocked.pid AS blocked_pid,
    blocked.query AS blocked_query,
    blocking.pid AS blocking_pid,
    blocking.query AS blocking_query,
    now() - blocked.query_start AS wait_time
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid
JOIN pg_locks kl ON kl.locktype = bl.locktype
    AND kl.database IS NOT DISTINCT FROM bl.database
    AND kl.relation IS NOT DISTINCT FROM bl.relation
    AND kl.page IS NOT DISTINCT FROM bl.page
    AND kl.tuple IS NOT DISTINCT FROM bl.tuple
    AND kl.virtualxid IS NOT DISTINCT FROM bl.virtualxid
    AND kl.transactionid IS NOT DISTINCT FROM bl.transactionid
    AND kl.classid IS NOT DISTINCT FROM bl.classid
    AND kl.objid IS NOT DISTINCT FROM bl.objid
    AND kl.objsubid IS NOT DISTINCT FROM bl.objsubid
    AND NOT bl.granted
JOIN pg_stat_activity blocking ON blocking.pid = kl.pid
WHERE NOT blocked.granted;
```

**Типовая картина:** транзакция с открытой `SELECT` (без `FOR UPDATE`) держит `ACCESS SHARE` lock, а `UPDATE` ждёт `ACCESS EXCLUSIVE`. Ожидание 30+ секунд.

---

### 2. `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)` — главный инструмент

```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)
SELECT o.*
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
WHERE o.customer_id = 42
  AND o.created_at > '2025-01-01';
```

#### 2.1. На что смотрю в плане (сверху вниз)

```
Seq Scan on orders o
  (actual time=12.456..156.789 rows=1500000 loops=1)
  (buffers: shared hit=89234 read=45210)
```

| Метка | Что значит |
|-------|-----------|
| `Seq Scan` | Полное сканирование таблицы. Для таблицы > 100k строк — **проблема** |
| `actual time=12.4..156.8` | Минимум 12 мс, максимум 156 мс на эту операцию |
| `rows=1500000` | Сколько строк **фактически** обработано. Если `rows` >> ожидаемых — фильтр не сработал |
| `loops=1` | Сколько раз эта операция повторилась. `loops=500` + `Seq Scan` = N+1 на уровне БД |
| `buffers: hit=89234 read=45210` | `read` — чтение с диска. Если `read` > 0 — данные не в памяти |

#### 2.2. Типовые «паттерны» в плане

**Паттерн 1: Seq Scan вместо Index Scan**

```
Seq Scan on orders (cost=0.00..28500.00 rows=1500000 width=64)
  Filter: (customer_id = 42)
```

Причина: индекс на `customer_id` отсутствует, или планировщик решил, что `Seq Scan` дешевле (selectivity > 5–10%).

**Паттерн 2: Nested Loop с последним оператором «тяжёлый»**

```
Nested Loop  (cost=1.05..15600.00 rows=1000 loops=1)
  ->  Index Scan on orders  (cost=0.56..10.00 rows=5 loops=1)
  ->  Seq Scan on order_items  (cost=0.00..3100.00 rows=300000 loops=5)
        Filter: (order_id = orders.id)
```

`loops=5` × `Seq Scan 300000 строк` = 1 500 000 строк обработано. Индекс на `order_items.order_id` отсутствует.

**Паттерن 3: Sort на большом объёме**

```
Sort  (cost=250000.00..300000.00 rows=1500000 loops=1)
  Sort Key: created_at
  Sort Method: external merge  Disk: 45216kB
```

`external merge` + `Disk` — сортировка не поместилась в `work_mem`, ушла на диск. Увеличить `work_mem` или добавить индекс.

**Паттерн 4: Hash Join с большим hash**

```
Hash Join  (cost=50000.00..85000.00 rows=1000 loops=1)
  Hash Cond: (o.customer_id = c.id)
  ->  Seq Scan on orders o  (cost=0.00..28500.00 rows=1500000)
  ->  Hash  (cost=4500.00..4500.00 rows=4500 loops=1)
        ->  Seq Scan on customers c  (cost=0.00..4500.00 rows=4500)
```

`Hash` строится на 1 500 000 строк. Если `join` — по редкому условию, лучше `Nested Loop` + `Index Scan`.

---

### 3. Типовые причины (от частых к редким)

#### 3.1. Отсутствие индекса (60% случаев)

```sql
-- Медленный запрос
SELECT * FROM orders WHERE customer_id = 42 AND created_at > '2025-01-01';
-- Seq Scan на 15 млн строк

-- EXPLAIN
Seq Scan on orders (cost=0.00..450000.00 rows=15000000)
  Filter: (customer_id = 42) AND (created_at > '2025-01-01')

-- Решение
CREATE INDEX CONCURRENTLY idx_orders_customer_created
    ON orders (customer_id, created_at DESC);

-- После:
Index Scan using idx_orders_customer_created on orders
  (cost=0.56..25.00 rows=150 loops=1)
  Index Cond: (customer_id = 42)
  Filter: (created_at > '2025-01-01')
```

**Правило для composite-индекса:** колонки в `WHERE` **в порядке селективности** (самая узкая → самая широкая). `customer_id` (1 из 50 000) → `created_at` (диапазон).

---

#### 3.2. Индекс есть, но не используется (20% случаев)

```sql
-- Индекс существует
CREATE INDEX idx_orders_status ON orders (status);

-- Запрос
SELECT * FROM orders WHERE status = 'confirmed' AND customer_id = 42;
-- EXPLAIN: Seq Scan!
```

**Почему:**

| Причина | Диагностика | Решение |
|---------|-------------|---------|
| Selectivity слишком низкая | `status = 'confirmed'` — 80% строк. Планировщик выбирает Seq Scan | Не «чинить» — Seq Scan **правильный** выбор. Ограничить `LIMIT` или добавить `customer_id` в индекс |
| Функция над колонкой | `WHERE LOWER(email) = 'test@x.com'` | Expression index: `CREATE INDEX ON orders (LOWER(email))` |
| Типы несовпадают | `WHERE id = '42'` (строка вместо int) | Исправить запрос: `WHERE id = 42` |
| `OR` с разными колонками | `WHERE a = 1 OR b = 2` | Bitmap Index Scan (обычно ок) или split query |
| `LIKE '%...%'` | `WHERE name LIKE '%electron%'` | `pg_trgm` + GIN index |

**Проверка:**

```sql
-- Селективность
SELECT status, count(*) FROM orders GROUP BY status ORDER BY count DESC;
-- status        | count
-- --------------+---------
--  confirmed    | 12 000 000  (80%)
--  cancelled    |  2 000 000  (13%)
--  draft        |    800 000  ( 5%)
--  shipped      |    200 000  ( 1%)

-- Индекс на status бесполезен для status='confirmed' (80%)
-- Но полезен для status='shipped' (1%)
```

---

#### 3.3. Table/Index bloat (10% случаев)

```sql
-- Размер таблицы vs «ожидаемый»
SELECT
    relname,
    pg_size_pretty(pg_total_relation_size(relid)) AS total_size,
    pg_size_pretty(pg_relation_size(relid)) AS table_size,
    pg_size_pretty(pg_indexes_size(relid)) AS index_size
FROM pg_class
WHERE relname = 'orders';

-- orders: total=12 GB, table=8 GB, index=4 GB
-- Ожидаемый размер: 15 млн строк × 200 байт = ~3 GB
-- → bloat 2.7×
```

**Диагностика:**

```sql
-- n_dead_tup / n_live_tup
SELECT
    relname,
    n_live_tup,
    n_dead_tup,
    round(n_dead_tup::numeric * 100 / NULLIF(n_live_tup, 0), 2) AS dead_ratio,
    last_vacuum,
    last_autovacuum,
    last_analyze
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- dead_ratio = 45% → bloat, autovacuum не успевает
```

**Решение:**

```sql
-- 1. Проверить настройки autovacuum
SHOW autovacuum;           -- on
SHOW autovacuum_max_workers;  -- 3 (может быть мало)
SHOW autovacuum_vacuum_scale_factor;  -- 0.2 (20% dead rows)

-- 2. Принудительная вакуумация
VACUUM (ANALYZE, VERBOSE) orders;

-- 3. REINDEX (если индексы раздуты)
REINDEX INDEX CONCURRENTLY idx_orders_customer_created;

-- 4. Для критичных таблиц: агрессивнее
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.05,  -- 5% dead rows
    autovacuum_vacuum_cost_limit = 1000
);
```

**Почему bloat замедляет:**
- Страницы содержат dead tuples → больше страниц → больше I/O
- Индексы содержат dead entries → Index Scan дольше
- `n_dead_tup` растёт → планировщик неверно оценивает selectivity

---

#### 3.4. Статистика устарела (7% случаев)

```sql
-- Планировщик думает, что 10 000 строк подойдут. Фактически — 500 000.
-- Причина: ANALYZE давно не запускался

SELECT
    relname,
    last_analyze,
    last_autoanalyze,
    n_live_tup
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- last_analyze = '2025-01-15' (2 месяца назад)
-- n_live_tup = 15 000 000
-- За этот период вставили 5 млн строк
```

**Решение:**

```sql
ANALYZE orders;
-- Теперь pg_class.reltuples актуально
-- Планировщик пересчитывает selectivity
```

**Авто-настройка:**

```sql
-- Для активно изменяющихся таблиц
ALTER TABLE orders SET (
    autovacuum_analyze_scale_factor = 0.01,  -- 1% изменений → ANALYZE
    autovacuum_analyze_threshold = 50000     -- минимум 50k изменений
);
```

---

#### 3.5. Lock contention (3% случаев)

```sql
-- Запрос висит 30+ секунд
SELECT
    pid,
    now() - query_start AS duration,
    state,
    wait_event_type,
    wait_event,
    LEFT(query, 120) AS query
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY query_start ASC;

-- pid | duration | state  | wait_event_type | wait_event | query
------+----------+--------+-----------------+------------+-------
-- 1234| 32s      | active | Lock            | transaction| SELECT * FROM orders WHERE...
-- 5678| 30s      | active | <null>          | <null>     | BEGIN; UPDATE orders SET...
```

**Диагностика:**

```sql
-- Кто держит lock
SELECT
    l.pid,
    l.locktype,
    l.mode,
    l.granted,
    a.query
FROM pg_locks l
JOIN pg_stat_activity a ON a.pid = l.pid
WHERE l.relation = 'orders'::regclass
ORDER BY l.granted DESC;

-- pid  | locktype | mode           | granted | query
--------+----------+----------------+---------+------
-- 5678 | relation | AccessExclusive| t       | UPDATE orders SET status='paid'...
-- 1234 | relation | AccessShare    | f       | SELECT * FROM orders WHERE...
```

**Решение:**

```sql
-- Вариант 1: убить блокирующую транзакцию
SELECT pg_cancel_backend(5678);   -- graceful
SELECT pg_terminate_backend(5678); -- force

-- Вариант 2: избежать блокировки
-- Вместо:
SELECT * FROM orders WHERE status = 'confirmed' FOR UPDATE;
UPDATE orders SET status = 'paid' WHERE id = 42;

-- Сделайте:
UPDATE orders SET status = 'paid' WHERE id = 42 AND status = 'confirmed';
-- (один statement, короткая блокировка)
```

---

#### 3.6. Неоптимальный план (решение: Hint или переписать)

```sql
-- Запрос: «заказы за последнюю неделю с 3+ items»
SELECT o.*
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
WHERE o.created_at > NOW() - INTERVAL '7 days'
GROUP BY o.id
HAVING COUNT(oi.id) >= 3;

-- EXPLAIN: Hash Join (15 млн строк) → GroupAggregate → Filter
-- А оптимум: Index Scan на created_at → Nested Loop → GroupAggregate
```

**Решения (по степени «вторжения»):**

| Подход | Пример | Когда |
|--------|--------|-------|
| Переписать запрос | `WHERE EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id = o.id)` вместо `JOIN + HAVING COUNT` | Всегда предпочтительно |
| `SET enable_hashjoin = off` | Временно, для диагностики | Только для проверки, не в production |
| `pg_hint_plan` | `/*+ IndexScan(o idx_orders_created) */` | Если переписать нельзя (legacy, ORM) |
| `CREATE INDEX` | Новый индекс | Если запрос горячий и не меняется |

---

### 4. Процесс: пошаговый чеклист

```
┌─────────────────────────────────────────────────────────────┐
│ ШАГ 1: Получить SQL-запрос                                  │
│   - Из логов / pg_stat_statements / жалобы                  │
│   - Зафиксировать: time, rows, frequency                    │
├─────────────────────────────────────────────────────────────┤
│ ШАГ 2: EXPLAIN (ANALYZE, BUFFERS)                           │
│   - Найти «тяжёлый» узел (max cost / max rows)             │
│   - Seq Scan? Sort external? Hash на 10M строк?            │
├─────────────────────────────────────────────────────────────┤
│ ШАГ 3: Определить причину                                   │
│   - Нет индекса? → CREATE INDEX                             │
│   - Индекс есть, но не используется? → selectivity?         │
│   - Bloat? → VACUUM + REINDEX                               │
│   - Статистика устарела? → ANALYZE                          │
│   - Lock? → pg_locks + pg_stat_activity                     │
│   - План неоптимален? → переписать запрос                   │
├─────────────────────────────────────────────────────────────┤
│ ШАГ 4: Применить решение                                    │
│   - CREATE INDEX CONCURRENTLY (не блокирует запись)         │
│   - VACUUM / REINDEX                                         │
│   - ALTER TABLE ... SET (autovacuum_...)                    │
│   - Переписать запрос                                       │
├─────────────────────────────────────────────────────────────┤
│ ШАГ 5: Проверить                                            │
│   - EXPLAIN (ANALYZE) → план изменился?                     │
│   - pg_stat_statements → mean_ms упал?                      │
│   - Нагрузка на систему: CPU, I/O, memory                   │
├─────────────────────────────────────────────────────────────┤
│ ШАГ 6: Мониторить                                           │
│   - pg_stat_statements → не вернулся к прежнему состоянию  │
│   - autovacuum → dead_tup не растут                         │
│   - load average → не вырос после индекса (I/O overhead)    │
└─────────────────────────────────────────────────────────────┘
```

---

### 5. Инструменты: сводка

| Инструмент | Что даёт | Команда / доступ |
|------------|----------|-----------------|
| `EXPLAIN (ANALYZE, BUFFERS)` | План выполнения + тайминги | `psql` или любой client |
| `pg_stat_statements` | Топ запросов, частота, время | `SELECT FROM pg_stat_statements` |
| `pg_stat_activity` | Текущие запросы, state, wait_event | `SELECT FROM pg_stat_activity` |
| `pg_locks` | Кто блокирует кого | `SELECT FROM pg_locks` |
| `pg_stat_user_tables` | dead_tup, last_vacuum, size | `SELECT FROM pg_stat_user_tables` |
| `pg_class` + `pg_index` | Индексы на таблицу | `SELECT ... FROM pg_indexes WHERE tablename = 'orders'` |
| `pg_stat_io` (PG14+) | I/O по операциям (read/write/hit) | `SELECT FROM pg_stat_io` |
| `pg_buffercache` | Что в shared_buffers | `SELECT ... FROM pg_buffercache` |
| `pg_autovacuum` (view) | Статус autovacuum | `SELECT FROM pg_stat_user_tables` |
| `pg_locks` + `pg_stat_activity` | Deadlock detection | `SHOW deadlock_timeout` |
| `pg_stat_wal` | WAL throughput | `SELECT FROM pg_stat_wal` |
| `EXPLAIN (FORMAT JSON)` | Структурированный план (для парсинга) | `EXPLAIN (FORMAT JSON)` |
| `pg_stat_replication` | Задержка репликации (если есть replica) | `SELECT FROM pg_stat_replication` |

---

### 6. Пример: полный цикл диагностики

**Жалоба:** «Страница списка заказов тормозит, 2–5 секунд.»

**Шаг 1:**

```sql
SELECT LEFT(query, 150), calls, mean_exec_time, max_exec_time
FROM pg_stat_statements
WHERE query ILIKE '%orders%'
ORDER BY total_exec_time DESC
LIMIT 5;

-- query                                                    | calls | mean_ms | max_ms
-- ---------------------------------------------------------+-------+---------+--------
-- SELECT o.*, i.* FROM orders o LEFT JOIN order_items i...|  1247 |  2845.2 | 8421.7
-- SELECT o.* FROM orders o WHERE o.customer_id = :1        |   892 |    0.3  |   12.4
```

**Шаг 2:**

```sql
EXPLAIN (ANALYZE, BUFFERS)
SELECT o.*, i.*
FROM orders o
LEFT JOIN order_items i ON i.order_id = o.id
WHERE o.created_at > '2025-06-01'
ORDER BY o.created_at DESC
LIMIT 50;
```

```
Limit  (cost=450000.00..450020.00 rows=50 loops=1)
  ->  Sort  (cost=450000.00..475000.00 rows=5000000 loops=1)
        Sort Key: o.created_at DESC
        Sort Method: external merge  Disk: 524288kB   ← ПРОБЛЕМА
        ->  Hash Join  (cost=120000.00..180000.00 rows=5000000 loops=1)
              Hash Cond: (i.order_id = o.id)
              ->  Seq Scan on order_items i  (cost=0.00..95000.00 rows=5000000)
              ->  Hash  (cost=80000.00..80000.00 rows=2000000)
                    ->  Seq Scan on orders o  (cost=0.00..80000.00 rows=2000000)
                          Filter: (created_at > '2025-06-01')
```

**Диагноз:**
1. `Seq Scan on orders` — нет индекса на `created_at`
2. `Sort external merge Disk: 512 MB` — сортировка 5 млн строк на диск
3. `Hash Join` на 5 млн строк — JOIN выполняется до LIMIT

**Шаг 3:**

```sql
-- 1. Индекс на created_at (composite: created_at + id для ORDER BY)
CREATE INDEX CONCURRENTLY idx_orders_created ON orders (created_at DESC);

-- 2. Проверить selectivity
SELECT count(*) FROM orders WHERE created_at > '2025-06-01';
-- = 500 000 (из 2 000 000) → 25% → индекс полезен

-- 3. Переписать: сначала отфильтровать + отсортировать, потом JOIN
EXPLAIN (ANALYZE, BUFFERS)
SELECT o.*, i.*
FROM (
    SELECT * FROM orders
    WHERE created_at > '2025-06-01'
    ORDER BY created_at DESC
    LIMIT 50
) o
LEFT JOIN order_items i ON i.order_id = o.id;
```

```
Limit  (cost=0.56..85.00 rows=50 loops=1)
  ->  Nested Loop Left Join  (cost=0.56..85.00 rows=50 loops=1)
        ->  Index Scan Backward using idx_orders_created on orders o
              (cost=0.56..5.00 rows=50 loops=1)           ← INDEX SCAN
        ->  Index Scan using idx_items_order on order_items i
              (cost=0.57..1.50 rows=3 loops=50)           ← 50 × 3 = 150 строк
```

**Результат:** 2 845 мс → **3 мс**. В 950 раз быстрее.

**Шаг 4:**

```sql
-- Проверить, что индекс не замедлил INSERT
EXPLAIN ANALYZE
INSERT INTO orders (customer_id, status, created_at)
VALUES (42, 'draft', NOW());

-- Cost вырос на 0.5 ms (обновление индекса) — приемлемо
```

**Шаг 5:**

```sql
-- Мониторинг
SELECT calls, mean_exec_time
FROM pg_stat_statements
WHERE query ILIKE '%orders%created_at%'
AND query ILIKE '%LIMIT 50%';

-- calls: 1247 → 2400 (за час)
-- mean_ms: 2845 → 2.8
```

---

### 7. Чего не делать

| Антипаттерн | Почему |
|-------------|--------|
| `VACUUM FULL` в production | Блокирует таблицу на минуты. Использовать `pg_repack` |
| `SET work_mem = 4GB` глобально | При 100 concurrent sessions = 400 GB RAM. На session |
| Убирать индексы «потому что INSERT медленный» | Сравнивать: 100 мс на INSERT vs 2 000 мс на SELECT. SELECT важнее |
| `CREATE INDEX` (без CONCURRENTLY) в production | Блокирует запись. Всегда `CONCURRENTLY` |
| Оптимизировать запрос, который выполняется 1 раз в месяц | Правило 80/20: оптимизировать hot queries |
| `SELECT *` + JOIN на 5 таблиц | Запрашивать только нужные колонки. `SELECT *` ломает Covering Index |

> ⏱ 52.8s · prompt 56688 tok · output 6657 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о DDD-агрегатах: определение, границы, пример, типовая ошибка. Это продолжение серии вопросов на собеседовании по PHP-бэкенду.

Что нужно сделать:
1. Чётко определить, что такое агрегат (не путать с сущностью, не путать с bounded context)
2. Объяснить, как определять границы (критерии, эвристики)
3. Привести конкретный пример с кодом — не абстрактный, а с реальными классами
4. Показать типовую ошибку при выборе границ и почему это ошибка
5. Дать практические критерии/эвристики

Структура ответа:
- Определение агрегата (коротко, точно)
- Ключевые свойства (инварианты, граница консистентности, одна транзакция)
- Как определять границы (эвристики, вопросы)
- Пример: Order + OrderItems + Payment + Customer — где граница, где нет
- Типовая ошибка: слишком большой агрегат (Order + Customer + Inventory + Payment в одном) или слишком маленький (OrderItem как отдельный агрегат)
- Показать последствия ошибки (race condition, нарушение инвариантов, N+1, транзакции)

Важно: я уже в предыдущих ответах использовал Order/OrderItems как пример. Здесь нужно углубиться именно в ВОПРОС ГРАНИЦ — что входит, что нет, и почему. Это новый аспект: не маппинг (это было выше), а проектирование границ.

Нужно показать:
1. Order — агрегат. OrderItem — часть агрегата. Customer — ОТДЕЛЬНЫЙ агрегат (или отдельная сущность). Payment — ОТДЕЛЬНЫЙ агрегат. Inventory — ОТДЕЛЬНЫЙ агрегат.
2. Почему Customer не входит в Order: инварианты Customer не зависят от Order, и наоборот.
3. Почему Payment не входит в Order: Payment имеет свой lifecycle (pending → confirmed → failed), свои инварианты (amount = order.total, но подтверждение Payment не должно блокировать изменение Order).
4. Типовая ошибка: «один большой агрегат» — Order содержит Customer, Payment, Inventory. Последствия: любая операция над Order блокирует всё, транзакции огромные, race conditions, N+1, невозможность параллельной обработки.

Пример ошибки:
- Кто-то решает: «Customer — часть Order, потому что Order содержит customerId». И делает Customer внутренним объектом Order.
- Тогда: изменение email в Customer требует «входа» в Order. Два заказа одного клиента → два агрегата, каждый со своим copy Customer → расхождение.
- Или: «Payment — часть Order, потому что Order должен знать, что он оплачен». Тогда: статус Payment меняется → должен изменять Order → транзакция на Payment блокирует Order.

Эвристики для определения границ:
1. Инварианты: какие правила должны выполнять ВСЕГДА, атомарно? → эти правила внутри агрегата
2. Транзакция: какие операции должны быть атомарными? → внутри агрегата
3. Идентичность: что идентифицирует объект? Если ID — это ID агрегата → внутри. Если ID — свой → отдельно
4. Lifecycle: имеют ли объекты одинаковый lifecycle (рождаются/умирают вместе)? → внутри. Разный → отдельно
5. Конкурентная модификация: могут ли разные пользователи одновременно изменять? → лучше отдельно (меньше блокировок)
6. Агрегат — не «всё, что связано»: Customer связан с Order, но не часть Order

Типовая ошибка — покажу на конкретном коде: «God aggregate» Order + Customer + Payment + Inventory.

Формат: markdown, таблицы, код, структурированно. Как и в предыдущих ответах.

</details>

**Кандидат:**

## Агрегат в DDD: определение и границы

### 1. Что такое агрегат

Агрегат — **граница консистентности**. Это кластер сущностей, которые:

1. **Изменяются атомарно** — инварианты агрегата всегда выполняются после транзакции
2. **Имеют одного корня** — все внешние ссылки указывают на корень, не на внутренности
3. **Защищают инварианты** — снаружи невозможно нарушить правила, только через методы корня

```
┌─────────────────────────────────────────┐
│ Агрегат: Order                          │
│                                         │
│   Order (корень)                        │
│   ├── OrderItem (внутренность)          │
│   ├── OrderItem (внутренность)          │
│   └── OrderItem (внутренность)          │
│                                         │
│   Инварианты:                           │
│   - items не пуст при status=confirmed  │
│   - total = SUM(items.qty * price)      │
│   - status transitions: draft→confirmed │
│                                         │
│   ВНЕШНИЕ ССЫЛКИ:                       │
│   → customerId (ID, не объект)          │
│   → paymentId (ID, не объект)           │
│                                         │
└─────────────────────────────────────────┘
```

**Ключевое:** `customerId` в Order — это **ID**, не ссылка на объект Customer. Customer — другой агрегат. Они связаны через идентификатор, а не через владение.

---

### 2. Критерии: что входит в агрегат, а что нет

| Критерий | Внутри агрегата | Снаружи |
|----------|----------------|---------|
| **Инварианты** | Правила, которые должны выполняться атомарно с этим объектом | Правила, которые зависят от внешних контекстов |
| **Лайфсикл** | Рождаются и умирают вместе | Независимый lifecycle |
| **Конкурентная модификация** | Всегда изменяются в одной транзакции | Могут изменяться независимо |
| **Идентичность** | ID наследуется от корня (или не имеет своего) | Свой ID, своя сущность |
| **Доступ извне** | Только через корень | Прямой доступ через свой репозиторий |

---

### 3. Пример: Order и его соседи

#### Что внутри агрегата Order

```php
// Корень
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,      // ← ID, не Customer!
        public readonly OrderStatus $status,
        private Collection $items,            // ← внутри
    ) {}

    // Инвариант: нельзя подтвердить пустой заказ
    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status->value, 'confirmed');
        }
        if ($this->items->count() === 0) {
            throw new EmptyOrderException($this->id);
        }
        return $this->withStatus(OrderStatus::Confirmed);
    }

    // Инвариант: item должен быть добавлен в draft
    public function addItem(int $productId, int $qty, int $priceCents): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderIsLocked($this->id);
        }
        if ($qty <= 0 || $priceCents <= 0) {
            throw new InvalidItemDataException($qty, $priceCents);
        }
        // ...
    }
}

// Внутренность (child entity)
final class OrderItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,       // ← ID, не Product!
        public readonly string $name,
        public readonly int $qty,
        public readonly int $priceCents,
    ) {}
}
```

**Почему OrderItem внутри:**
- Не существует без Order (orphan item — бессмысленен)
- Изменяется только через Order (`$order->addItem(...)`)
- Инвариант Order зависит от items (`total = SUM(items)`)
- Один lifecycle: Order удаляется → items удаляются

#### Что СНАРУЖИ

| Объект | Почему снаружи | Связь с Order |
|--------|---------------|---------------|
| `Customer` | Свой lifecycle, может быть у 100 заказов, изменяется независимо (email, address) | `customerId: int` |
| `Product` | Каталог, общий для всех заказов, может меняться (price, stock) | `productId: int` |
| `Payment` | Свой lifecycle (pending → confirmed → failed), свой инвариант (amount > 0), может быть retry | `paymentId: int` (на Payment) |
| `Inventory` | Свой инвариант (stock >= 0), изменяется конкурентно (много заказов одновременно) | `productId: int` |

**Почему Payment СНАРУЖИ (ключевой момент):**

```
Сценарий: заказ оплачен, но потом отменён

Order: confirmed → cancelled
Payment: confirmed → refunded

Это ДВА РАЗНЫХ изменения, в РАЗНЫХ транзакциях, в РАЗНОЕ время.
Order может быть отменён через 3 дня после оплаты.
Payment может быть refunded через 7 дней.

Если Payment внутри Order:
- Отмена Order → транзакция на весь агрегат → блокирует Payment
- Refund Payment → должен «войти» в Order → блокирует Order
- Race: два процесса одновременно меняют Order (один отменяет, другой refund'ит)
```

---

### 4. Как я определяю границы: практический процесс

#### Шаг 1: Выписать инварианты

```
Какие правила должны выполнять ВСЕГДА, атомарно?

Order:
  - items не пуст при confirmed
  - total = SUM(items.qty * price)
  - status transitions: draft → confirmed → shipped | cancelled

Customer:
  - email unique
  - email валидный формат
  - name не пуст

Payment:
  - amount > 0
  - status: pending → confirmed | failed
  - confirmed → refunded (однократно)

Inventory:
  - stock >= 0
  - reserved <= stock
```

#### Шаг 2: Сгруппировать инварианты по атомарности

```
Группа A: items не пуст + total = SUM + status transitions
  → Все три правила нарушаются ОДНОЙ операцией: confirm()
  → Один агрегат: Order

Группа B: email unique + email format + name не пуст
  → Правила относятся к Customer, не к Order
  → Отдельный агрегат: Customer

Группа C: amount > 0 + status transitions Payment
  → Правила относятся к Payment, не к Order
  → Отдельный агрегат: Payment

Группа D: stock >= 0 + reserved <= stock
  → Правила относятся к Inventory, не к Order
  → Отдельный агрегат: Inventory
```

#### Шаг 3: Проверить конкурентную модификацию

```
Могут ли РАЗНЫЕ пользователи одновременно изменять?

Order:    один пользователь (владелец) + один админ → низкая конкуренция
Customer: один пользователь (владелец) + один админ → низкая конкуренция
Payment:  один процесс (payment gateway) + один сервис → низкая конкуренция
Inventory: МНОГО заказов одновременно списывают stock → ВЫСОКАЯ конкуренция
  → Inventory ОБЯЗАТЕЛЬНО отдельный агрегат (иначе блокировки на весь заказ)
```

#### Шаг 4: Проверить lifecycle

```
OrderItem:
  - Создаётся при создании Order → да, внутри
  - Удаляется при удалении Order → да, внутри
  - Живёт без Order? → НЕТ → внутри

Customer:
  - Создаётся ДО Order → да, отдельно
  - Живёт ПОСЛЕ удаления Order → да, отдельно
  - У одного Customer 100 Order → да, отдельно

Payment:
  - Создаётся ПОСЛЕ Order (confirm) → да, отдельно
  - Живёт ПОСЛЕ Order (refund) → да, отдельно
  - У одного Order 1 Payment → связь, но не владение
```

#### Шаг 5: Написать «контракт» агрегата

```php
// Публичный API агрегата — ВНЕШНИЙ интерфейс
final class Order
{
    // Фабрики
    public static function create(int $customerId): self;
    public static function reconstitute(int $id, int $customerId, OrderStatus $status, Collection $items): self;

    // Команды (изменяют состояние)
    public function addItem(int $productId, int $qty, int $priceCents): self;
    public function removeItem(int $itemId): self;
    public function confirm(): self;
    public function cancel(string $reason): self;
    public function markAsShipped(): self;

    // Запросы (не изменяют)
    public function totalCents(): int;
    public function items(): Collection;
    public function isConfirmed(): bool;

    // ВНУТРЕННЕЕ:
    // - OrderItem — не имеет публичного API извне
    // - Никто не вызывает $order->items()->first()->setQty(99)
    // - Все изменения через Order
}
```

---

### 5. Типовая ошибка: «God Aggregate»

#### Ошибка

Разработчик решает: «Customer связан с Order, Payment связан с Order, Inventory связан с Order — значит, всё это один агрегат».

```php
// ПЛОХО: один большой агрегат
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly Customer $customer,     // ← объект, не ID
        public readonly Payment $payment,       // ← объект, не ID
        public readonly Collection $items,
        public readonly Collection $reservations, // ← Inventory
    ) {}

    public function confirm(): self
    {
        // 1. Проверяем items
        if ($this->items->count() === 0) throw ...;

        // 2. Меняем Customer (добавляем «подтверждённый заказ»)
        $this->customer->addConfirmedOrder($this);

        // 3. Создаём Payment
        $this->payment = Payment::create($this->totalCents());

        // 4. Резервируем Inventory
        foreach ($this->items as $item) {
            $this->reservations->reserve($item->productId, $item->qty);
        }

        // 5. Меняем статус
        $this->status = OrderStatus::Confirmed;
    }
}
```

#### Почему это ошибка: последствия

| Последствие | Механизм |
|-------------|----------|
| **Блокировки** | `confirm()` захватывает lock на Order, Customer, Payment, Inventory. Другой заказ того же Customer **блокируется** на время `confirm()` |
| **Расхождение данных** | `Customer` внутри каждого `Order` — это **копия**. Если Customer меняет email → нужно обновить 100 копий в 100 заказах |
| **Транзакция огромная** | `confirm()` = UPDATE orders + UPDATE customers + INSERT payments + UPDATE inventory. Если `UPDATE inventory` упадёт → откат ВСЕГО, включая `UPDATE customers` |
| **Race condition** | Два заказа одного Customer: оба делают `confirm()`. Оба пишут `customer.addConfirmedOrder()`. Кто-то потеряет данные |
| **N+1** | `SELECT * FROM orders` → для каждого заказа грузить `Customer`, `Payment`, `Inventory` — 4× больше данных |
| **Невозможность параллельной обработки** | `cancel(Order)` блокирует `confirm(Order)` того же Customer. Через 6 месяцев — deadlock |
| **Тестирование** | Unit-тест `confirm()` требует мока Customer, Payment, Inventory. Тест становится integration-тестом |

---

#### Правильно: раздельные агрегаты

```php
// Order — свой агрегат
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,    // ← ID
        public readonly OrderStatus $status,
        private Collection $items,          // ← внутри
    ) {}

    public function confirm(): self
    {
        // Только инварианты Order
        if ($this->items->count() === 0) throw ...;
        return $this->withStatus(OrderStatus::Confirmed);
    }
}

// Payment — свой агрегат
final class Payment
{
    public function __construct(
        public readonly int $id,
        public readonly int $orderId,       // ← ID
        public readonly int $amountCents,
        public readonly PaymentStatus $status,
    ) {}

    public static function createFor(int $orderId, int $amountCents): self
    {
        if ($amountCents <= 0) throw ...;
        return new self(0, $orderId, $amountCents, PaymentStatus::Pending);
    }

    public function confirm(): self
    {
        if ($this->status !== PaymentStatus::Pending) throw ...;
        return $this->withStatus(PaymentStatus::Confirmed);
    }

    public function refund(string $reason): self
    {
        if ($this->status !== PaymentStatus::Confirmed) throw ...;
        return $this->withStatus(PaymentStatus::Refunded);
    }
}

// Customer — свой агрегат
final class Customer
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
    ) {}

    public function updateEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw ...;
        return new self($this->id, $email, $this->name);
    }
}

// Inventory — свой агрегат
final class Inventory
{
    public function __construct(
        public readonly int $productId,
        public readonly int $stock,
        public readonly int $reserved,
    ) {}

    public function reserve(int $qty): self
    {
        if ($this->reserved + $qty > $this->stock) {
            throw new InsufficientStockException($this->productId, $qty);
        }
        return new self($this->productId, $this->stock, $this->reserved + $qty);
    }

    public function release(int $qty): self
    {
        if ($this->reserved < $qty) {
            throw new InvalidReleaseException($this->productId, $qty);
        }
        return new self($this->productId, $this->stock, $this->reserved - $qty);
    }
}

// Оркестрация: Application Service связывает агрегаты
final class PlaceOrderCommand
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentRepositoryInterface $payments,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(int $customerId, array $items): Order
    {
        // 1. Агрегат Order
        $order = Order::create($customerId);
        foreach ($items as $item) {
            $order = $order->addItem($item['productId'], $item['qty'], $item['price']);
        }
        $order = $order->confirm();
        $this->orders->save($order);  // ← транзакция 1

        // 2. Агрегат Payment
        $payment = Payment::createFor($order->id, $order->totalCents());
        $this->payments->save($payment);  // ← транзакция 2

        // 3. Агрегат Inventory (конкурентная!)
        foreach ($order->items() as $item) {
            $inv = $this->inventory->find($item->productId);
            $inv = $inv->reserve($item->qty);
            $this->inventory->save($inv);  // ← транзакция 3 (FOR UPDATE)
        }

        // 4. Асинхронно: уведомление
        $this->bus->dispatch(new OrderPlaced($order->id));

        return $order;
    }
}
```

**Ключевое отличие:**

| Аспект | God Aggregate | Раздельные агрегаты |
|--------|:------------:|:-------------------:|
| Блокировки | Одна большая | Малые, независимые |
| Транзакции | Одна огромная | Малые, по одному агрегату |
| Расхождение | Копии Customer | Один Customer, все ссылки по ID |
| Race condition | Высокий риск | Минимальный (Inventory — FOR UPDATE) |
| Тестирование | Integration | Unit per агрегат |
| Параллельность | Блокирует | Не блокирует (разные таблицы) |

---

### 6. Эвристики: как определить границу

```
Вопрос 1: «Эти два объекта изменяются в ОДНОЙ транзакции?»
  ├── Да → кандидат на один агрегат
  └── Нет → разные агрегаты

Вопрос 2: «Если я удалю объект A, должен ли удалиться объект B?»
  ├── Да, всегда → B внутри A
  └── Нет, B живёт сам → B отдельный агрегат

Вопрос 3: «Могут ли два разных пользователя одновременно изменять A и B?»
  ├── Да → лучше разные агрегаты (меньше блокировок)
  └── Нет → можно в одном

Вопрос 4: «Есть ли у B свой уникальный ID, который ссылается на A?»
  ├── Да, B ссылается на A через ID → B снаружи
  └── Нет, B существует только внутри A → B внутри

Вопрос 5: «Какие инварианты должны выполняться АТОМАРНО?»
  ├── Инварианты касаются только A → A — агрегат
  ├── Инварианты касаются A и B вместе → A+B — один агрегат
  ├── Инварианты касаются B независимо → B — свой агрегат

Вопрос 6: «Сколько объектов B может быть у одного A?»
  ├── 1..N (корзина: 10 items) → внутри (Collection)
  ├── 1 (Payment на Order) → снаружи (связь по ID)
  ├── N..N (Customer на Order) → снаружи (связь по ID)
```

---

### 7. Частые ошибки в определении границ

| Ошибка | Пример | Почему плохо |
|--------|--------|-------------|
| **Слишком большой** | Order + Customer + Payment + Inventory | Блокировки, расхождение, огромные транзакции |
| **Слишком маленький** | OrderItem как отдельный агрегат с репозиторием | Можно удалить item без Order, нарушить инвариант «total = SUM» |
| **Два агрегата делят инвариант** | «Order знает, что Payment подтверждён» + «Payment знает, что Order подтверждён» | Инвариант раскидан по двум агрегатам → невозможно гарантировать атомарность |
| **Общий ID** | Order и Payment имеют один ID | Связь 1:1, но с разными lifecycle → путаница |
| **Агрегат без корня** | Collection<OrderItem> без Order | Нечему защищать инварианты. Любой может мутировать items напрямую |
| **Ссылка на объект вместо ID** | `Order.customer: Customer` (объект) | Order «владеет» Customer. Удаление Order удалит Customer. Невозможно иметь 100 заказов на одного Customer без копий |

---

### 8. Итог

```
Агрегат = граница атомарности инвариантов.

Внутри: то, что изменяется атомарно и имеет общий lifecycle.
Снаружи: то, что имеет свой lifecycle, свой ID, свою конкуренцию.

Связь между агрегатами: через ID, не через объект.
Оркестрация: через Application Service (или event), не через владение.

Главный вопрос при выборе границы:
«Если я нарушу инвариант X, и это произойдёт через race condition —
могу ли я это поймать в ОДНОЙ транзакции?»

Если да → внутри.
Если нет → снаружи + событийная интеграция.
```

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Конкурентная модификация агрегата

### 1. Что произойдёт без защиты

#### Сценарий: два админа одновременно отменяют и ship'ят один заказ

```php
// Процесс A (админ отменяет)
$order = $repo->find(42);          // status = 'confirmed'
$order = $order->cancel('refund'); // status = 'cancelled' (в памяти)
$repo->save($order);              // UPDATE orders SET status='cancelled' WHERE id=42

// Процесс B (админ отправляет) — ПАРАЛЛЕЛЬНО
$order = $repo->find(42);          // status = 'confirmed'  ← читаем ДО commit A!
$order = $order->markAsShipped();  // status = 'shipped' (в памяти)
$repo->save($order);              // UPDATE orders SET status='shipped' WHERE id=42
```

**Что в БД:**

```
t=0:  status = 'confirmed'
t=1:  A читает → 'confirmed'
t=2:  B читает → 'confirmed'        ← оба видят старое значение
t=3:  A пишет → 'cancelled'         ← COMMIT
t=4:  B пишет → 'shipped'           ← COMMIT (перезаписал A!)
t=5:  status = 'shipped'            ← финальное состояние
```

**Результат:** заказ отменён (A) и одновременно отправлен (B). Финальное состояние — `shipped`. Действие A потеряно.

Это **lost update** — классическая race condition при `READ COMMITTED` (default в PostgreSQL).

---

### 2. Почему это происходит

```
PostgreSQL READ COMMITTED:
┌─────────────────────────────────────────────────────────────┐
│ Каждый SELECT видит только COMMITTED данные.                │
│ Но между SELECT и UPDATE — окно.                            │
│ Другая транзакция может COMMIT'нуть в этом окне.            │
│ UPDATE перезаписывает значение, не зная о чужом COMMIT.     │
└─────────────────────────────────────────────────────────────┘
```

```
Транзакция A                    Транзакция B
    │                              │
    │ SELECT (читает 'confirmed')  │
    │                              │ SELECT (читает 'confirmed')
    │                              │
    │ UPDATE 'cancelled'           │
    │ COMMIT                       │
    │                              │ UPDATE 'shipped'
    │                              │ COMMIT
    │                              │
    └── A не знает, что B переписал ─┘
```

---

### 3. Инструменты защиты

---

#### 3.1. Optimistic Locking (version field) — основной инструмент

**Идея:** каждое изменение инкрементирует `version`. `UPDATE` содержит `WHERE version = :expected`. Если кто-то опередил — `affected rows = 0` → ошибка → ретрайт.

**Доменная модель:**

```php
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,
        public readonly OrderStatus $status,
        private Collection $items,
        public readonly int $version,    // ←版本号
    ) {}

    private function withStatus(OrderStatus $status): self
    {
        // version инкрементируется при каждом изменении
        return new self(
            id: $this->id,
            customerId: $this->customerId,
            status: $status,
            items: $this->items,
            version: $this->version + 1,
        );
    }

    public function cancel(string $reason): self
    {
        if ($this->status !== OrderStatus::Confirmed) {
            throw new InvalidTransitionException($this->status->value, 'cancelled');
        }
        return $this->withStatus(OrderStatus::Cancelled);
    }

    public function markAsShipped(): self
    {
        if ($this->status !== OrderStatus::Confirmed) {
            throw new InvalidTransitionException($this->status->value, 'shipped');
        }
        return $this->withStatus(OrderStatus::Shipped);
    }
}
```

**Репозиторий:**

```php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        /** @var OrderEntity $entity */
        $entity = $this->em->find(OrderEntity::class, $order->id);

        $entity->setStatus($order->status->value);
        $this->syncItems($entity, $order->items());

        // КРИТИЧЕСКИЙ МОМЕНТ: UPDATE с условием на version
        $affected = $this->em->getConnection()->executeStatement(
            'UPDATE orders SET status = :status, version = :new_version
             WHERE id = :id AND version = :expected_version',
            [
                'status'           => $order->status->value,
                'new_version'      => $order->version,
                'id'               => $order->id,
                'expected_version' => $order->version - 1,  // ← то, что мы читали
            ],
        );

        if ($affected === 0) {
            throw new ConcurrentModificationException(
                sprintf(
                    'Order #%d was modified by another process. Expected version %d.',
                    $order->id,
                    $order->version - 1,
                )
            );
        }

        $this->em->flush();
    }
}
```

**Что происходит при конфликте:**

```
Транзакция A (cancel)              Транзакция B (ship)
    │                                  │
    │ SELECT → version=5               │ SELECT → version=5
    │                                  │
    │ UPDATE ... WHERE version=5       │ UPDATE ... WHERE version=5
    │   → affected=1                   │   → affected=0  ← version уже 6!
    │   version → 6                    │
    │ COMMIT                           │
    │                                  │ ConcurrentModificationException
    │                                  │ → ретрайт или ошибка пользователю
```

**Гарантия:** только один из двух `UPDATE` затронет строку. Второй получит `affected=0`.

| Плюс | Минус |
|------|-------|
| Нет блокировок — процессы не ждут друг друга | Ретрайт: нужно пере-читать агрегат и повторить логику |
| Простая реализация (одна колонка) | При высокой конкуренции — много ретраев |
| Не деградирует при 1000 concurrent | Ретрайт = новый SELECT + новая логика → не тривиально |
| Works с READ COMMITTED (default) | |

**Когда:** основной выбор для CRUD-операций над агрегатом (1–50 concurrent на одну строку).

---

#### 3.2. Pessimistic Locking (SELECT FOR UPDATE)

**Идея:** «захватить» строку при чтении. Другой процесс **ждёт**, пока первый откоммитит.

```php
public function find(int $id): ?Order
{
    $sql = 'SELECT * FROM orders WHERE id = :id FOR UPDATE';
    $row = $this->em->getConnection()->fetchAssociative($sql, ['id' => $id]);

    if ($row === false) {
        return null;
    }

    return Order::reconstitute(/* ... */);
}

public function cancel(int $id, string $reason): void
{
    // 1. Захватываем (блокируем строку)
    $order = $this->findForUpdate($id);  // ← FOR UPDATE

    // 2. Модифицируем (блокировка держится до COMMIT)
    $order = $order->cancel($reason);

    // 3. Сохраняем
    $this->save($order);

    // 4. COMMIT → блокировка освобождается
}
```

**SQL:**

```sql
BEGIN;
SELECT * FROM orders WHERE id = 42 FOR UPDATE;  -- блокирует строку
UPDATE orders SET status = 'cancelled' WHERE id = 42;
COMMIT;  -- блокировка освобождена
```

**Что происходит при конфликте:**

```
Транзакция A (cancel)              Транзакция B (ship)
    │                                  │
    │ SELECT FOR UPDATE                │
    │ → блокирует строку               │
    │                                  │ SELECT FOR UPDATE
    │                                  │ → ЖДЁТ (lock wait)
    │ UPDATE                           │
    │ COMMIT                           │
    │                                  │ → получает блокировку
    │                                  │ SELECT → status='cancelled'
    │                                  │ markAsShipped() → InvalidTransitionException!
    │                                  │ (статус уже не 'confirmed')
```

| Плюс | Минус |
|------|-------|
| Второй процесс видит **актуальное** состояние | Блокировка: второй процесс **ждёт** |
| Нет ретраев | Deadlock риск (если A ждёт B, B ждёт A) |
| Простая логика: «прочитал → изменил → сохранил» | При 100 concurrent на одну строку — очередь |
| | `lock_timeout` — если не отпустить, зависнет |

**Deadlock пример:**

```
Транзакция A: SELECT order_42 FOR UPDATE → SELECT inventory_7 FOR UPDATE
Транзакция B: SELECT inventory_7 FOR UPDATE → SELECT order_42 FOR UPDATE

A ждёт B (inventory_7), B ждёт A (order_42) → DEADLOCK
```

**Защита:** PostgreSQL детектирует deadlock за 1 с (`deadlock_timeout = 1s`) и откатывает одну из транзакций.

**Когда:** финансовая операция, где ретрайт недопустим. «Один победитель, остальные ждут».

---

#### 3.3. SERIALIZABLE isolation level

**Идея:** PostgreSQL гарантирует, что последовательность транзакций эквивалентна **последовательному** выполнению. Если конфликт — `SerializationFailure` → ретрайт.

```sql
BEGIN ISOLATION LEVEL SERIALIZABLE;

-- Читаем состояние
SELECT status FROM orders WHERE id = 42;
-- = 'confirmed'

-- Проверяем инвариант
-- status = 'confirmed' → можно ship

-- Записываем
UPDATE orders SET status = 'shipped' WHERE id = 42;

COMMIT;
```

**Что при конфликте:**

```
Транзакция A: BEGIN SERIALIZABLE; SELECT order_42; ...
Транзакция B: BEGIN SERIALIZABLE; SELECT order_42; ...
A: UPDATE; COMMIT
B: UPDATE; COMMIT
  → ERROR: could not serialize access due to concurrent update
  → B откатывается. Ретрайт.
```

| Плюс | Минус |
|------|-------|
| Максимальная изоляция (сильнее S2) | `SerializationFailure` → нужен retry loop |
| Не нужен version field | При высокой конкуренции — много ретраев |
| Работает «из коробки» (настройка БД) | Альтернатива: `FOR UPDATE` проще |
| | Деградация при 100+ concurrent на одну строку |
| | Не переносимый (PostgreSQL-specific, хотя есть и в других) |

**Когда:** финансовые операции, где критична корректность, а не скорость. Банковские системы, инвентарь.

**Настройка:**

```yaml
# connection string
dsn: pgsql://user:pass@host/db?isolationLevel=SERIALIZABLE

# или на уровне session
SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

**Retry loop:**

```php
public function save(Order $order): void
{
    $maxRetries = 3;
    $delay = 100; // ms

    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        try {
            $this->em->transactional(function (EntityManagerInterface $em) {
                // ... persist + flush
            });
            return; // успех
        } catch (SerializationException $e) {
            if ($attempt === $maxRetries - 1) {
                throw new ConcurrentModificationException(
                    "Order #{$order->id}: max retries exceeded",
                    previous: $e,
                );
            }
            usleep($delay * 1000);
            $delay *= 2; // exponential backoff
        }
    }
}
```

---

#### 3.4. Advisory Lock (на уровне «ресурса»)

**Идея:** «только один процесс может одновременно выполнять операцию X для сущности Y». Блокировка на уровне **ключа**, а не строки.

```php
final class LockService
{
    public function __construct(
        private readonly Connection $db,
    ) {}

    public function withLock(string $resource, callable $fn): mixed
    {
        $key = $this->hashKey($resource);  // "order:42" → 847293561

        // pg_try_advisory_lock — non-blocking, session-level
        $locked = $this->db->fetchOne(
            'SELECT pg_try_advisory_lock(:key)',
            ['key' => $key],
        );

        if ($locked !== 't') {
            throw new LockBusyException($resource);
            // или: throw new ConcurrentModificationException(...)
        }

        try {
            return $fn();
        } finally {
            $this->db->executeStatement(
                'SELECT pg_advisory_unlock(:key)',
                ['key' => $key],
            );
        }
    }

    private function hashKey(string $resource): int
    {
        // md5 → bigint (advisory lock принимает 2×int или 1×bigint)
        return (int) substr(hash('md5', $resource), 0, 15);
    }
}
```

```php
// Использование
$this->lock->withLock('order:42', function () {
    $order = $this->repo->find(42);
    $order = $order->cancel('reason');
    $this->repo->save($order);
});
```

| Плюс | Минус |
|------|-------|
| Не блокирует строку — блокирует «концепцию» | Если процесс упадёт до `unlock` — lock живёт до конца session |
| Можно блокировать **несколько** сущностей | Нет `SELECT FOR UPDATE` — нужно само-управлять |
| Гибкий: «только один процесс на order:42 + payment:77» | Не переносимый (PostgreSQL-specific) |
| | Отладка: `pg_locks` → тип `advisory` |

**Когда:** распределённые операции, где нужно атомарно затронуть **несколько** агрегатов без deadlock.

---

### 4. Где в архитектуре

```
┌─────────────────────────────────────────────────────────────┐
│ ДОМЕННАЯ МОДЕЛЬ                                              │
│                                                             │
│  Order (иммутабельный, с version)                           │
│  ├── cancel() → новый Order с version+1                    │
│  ├── markAsShipped() → новый Order с version+1             │
│  └── Инварианты: status transitions                        │
│                                                             │
│  Ничего не знает про блокировки, version, SQL              │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ АПPLICATION SERVICE (оркестрация)                            │
│                                                             │
│  PlaceOrderCommand / CancelOrderCommand                     │
│  ├── Вызывает доменную модель                               │
│  ├── Вызывает репозиторий                                   │
│  ├── Ловит ConcurrentModificationException                  │
│  └── Ретрайт (опционально)                                  │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ ИНФРАСТРУКТУРА (репозиторий)                                 │
│                                                             │
│  DoctrineOrderRepository                                    │
│  ├── save() → UPDATE ... WHERE version = :expected         │
│  ├── find() → SELECT (без FOR UPDATE)                       │
│  ├── findForUpdate() → SELECT FOR UPDATE                   │
│  └── ConcurrentModificationException при affected=0         │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ БД (PostgreSQL)                                              │
│                                                             │
│  orders: id, status, version, ...                           │
│  CHECK (status IN (...))                                    │
│  UNIQUE (id)                                                │
└─────────────────────────────────────────────────────────────┘
```

**Принцип:** доменная модель **не знает** про конкурентность. Она просто создаёт новый объект с `version+1`. Репозиторий **знает**, что `version` — механизм оптимистичной блокировки. Application service **знает**, что может получиться `ConcurrentModificationException` и что делать.

---

### 5. Сводка: какой инструмент выбрать

| Сценарий | Инструмент | Почему |
|----------|-----------|--------|
| 1–50 concurrent, CRUD | **Optimistic (version)** | Нет блокировок, простой, ретрайт редкий |
| Финансовая операция, 1 победитель | **Pessimistic (FOR UPDATE)** | Второй видит актуальное, нет ретраев |
| Банковская система, максимальная изоляция | **SERIALIZABLE + retry** | Гарантия, что конфликт не «проскочит» |
| Несколько агрегатов атомарно | **Advisory Lock** | Блокировка на «концепцию», не на строку |
| Инвентарь (stock decrement) | **Pessimistic** или **SERIALIZABLE** | `UPDATE stock SET qty = qty - :n WHERE qty >= :n` |

**Комбинация (часто в одном проекте):**

```
Order.cancel()        → Optimistic (version)
Payment.charge()      → Pessimistic (FOR UPDATE)
Inventory.reserve()   → Pessimistic (FOR UPDATE)
Idempotency key       → UNIQUE constraint
Cross-aggregate op    → Advisory Lock + event
```

---

### 6. Практический пример: `cancelOrder` с защитой

```php
// src/Application/CancelOrderCommand.php
final class CancelOrderCommand
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentRepositoryInterface $payments,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(int $orderId, string $reason): Order
    {
        $maxRetries = 3;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                return $this->execute($orderId, $reason);
            } catch (ConcurrentModificationException $e) {
                if ($attempt === $maxRetries - 1) {
                    throw $e;
                }
                $this->logger->warning('cancel_order.retry', [
                    'order' => $orderId,
                    'attempt' => $attempt + 1,
                ]);
                usleep(50 * 1000 * ($attempt + 1)); // 50ms, 100ms, 150ms
            }
        }
    }

    private function execute(int $orderId, string $reason): Order
    {
        // 1. Читаем (без блокировки — optimistic)
        $order = $this->orders->find($orderId);
        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Доменная логика (инварианты)
        $order = $order->cancel($reason);  // version +1

        // 3. Сохраняем (UPDATE ... WHERE version = :expected)
        $this->orders->save($order);       // ← здесь ConcurrentModificationException

        // 4. Связанные агрегаты (отдельные транзакции)
        $payment = $this->payments->findByOrder($orderId);
        if ($payment !== null && $payment->status === PaymentStatus::Confirmed) {
            $payment = $payment->refund($reason);
            $this->payments->save($payment);
        }

        $this->inventory->releaseAll($order->items());

        // 5. Асинхронно
        $this->bus->dispatch(new OrderCancelled($orderId, $reason));

        return $order;
    }
}
```

**Почему именно так:**

| Шаг | Механизм | Почему |
|-----|----------|--------|
| Read | Без блокировки | Оптимистичный подход: не блокируем, а проверяем при записи |
| Domain | `cancel()` | Инварианты в доменной модели, не в SQL |
| Save | `UPDATE ... WHERE version` | Атомарная гарантия: кто-то опередил → exception |
| Payment | Отдельная транзакция | Payment — отдельный агрегат, свой lifecycle |
| Inventory | Отдельная транзакция | Inventory — конкурентный, свой FOR UPDATE внутри |
| Event | Асинхронно | Не блокирует ответ пользователю |
| Retry | Exponential backoff | Временный конфликт (1–2 ms) → ретрайт успешен |

**Критично:** если `ConcurrentModificationException` — **не** логировать и **не** проглатывать. Это **ожиданное** событие при конкуренции. Лог на `warning`, ретрайт на `info`, финальный throw на `error`.

> ⏱ 47.5s · prompt 63207 tok · output 5660 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в контексте Symfony/PHP/PostgreSQL. Это продолжение серии вопросов по DDD и архитектуре. Нужно:

1. Объяснить, что такое доменные события и чем они отличаются от application events
2. Когда публиковать (timing)
3. Как обрабатывать (sync vs async)
4. Какие проблемы решают
5. Какие проблемы создают (дубли, порядок, idempotency, testing)
6. Связь с транзакциями (outbox pattern, transactional outbox, event sourcing)
7. Практические примеры в Symfony (Messenger, EventDispatcher)

Структура ответа:
- Определение: доменное событие vs application event
- Когда публиковать: после изменения состояния агрегата, внутри доменной модели или в application service
- Обработка: sync (EventDispatcher) vs async (Messenger + AMQP)
- Связь с транзакциями: outbox pattern — ключевой момент
- Проблемы: дубли, порядок, идемпотентность, тестирование, отладка
- Антипаттерны

Буду использовать пример Order + Payment + Inventory для связности с предыдущими ответами.

</details>

**Кандидат:**

## Доменные события: применение, обработка, связь с транзакциями

### 1. Что такое доменное событие

Доменное событие — **факт**, что произошло в домене. Не «я хочу, чтобы что-то сделали», а «это произошло».

```php
// Факт: заказ подтверждён
final class OrderConfirmed
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $customerId,
        public readonly int $totalCents,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}

// Факт: оплата проведена
final class PaymentSettled
{
    public function __construct(
        public readonly int $paymentId,
        public readonly int $orderId,
        public readonly int $amountCents,
        public readonly string $transactionRef,
    ) {}
}

// Факт: товар зарезервирован
final class StockReserved
{
    public function __construct(
        public readonly int $productId,
        public readonly int $qty,
        public readonly int $orderId,
    ) {}
}
```

**Ключевое отличие от application event:**

| | Доменное событие | Application event |
|---|---|---|
| Что описывает | Факт в домене | Техническое действие |
| Пример | `OrderConfirmed` | `UserCreated` (Symfony form) |
| Живёт в | Доменном слое | Прикладном слое |
| Кто публикует | Агрегат / application service | Контроллер / слушатель формы |
| Смысл | «Заказ подтверждён» | «Форма сохранена» |

**Практически** в Symfony они часто смешиваются. `EventDispatcher` Symfony — общий механизм. Разделение — концептуальное.

---

### 2. Когда публиковать

#### Правило: событие публикуется **после** изменения состояния агрегата и **внутри** той же транзакции (или сразу после неё).

```
┌─────────────────────────────────────────────────────────────────┐
│ Правильно:                                                      │
│                                                                 │
│  1. Агрегат меняет состояние (confirm())                        │
│  2. Агрегат «помнит», что событие произошло                     │
│  3. Application service вызывает repo->save()                   │
│  4. Application service публикует события                       │
│  5. COMMIT                                                      │
│                                                                 │
│  События не опубликованы ДО сохранения.                         │
│  Если save упадёт — события не опубликованы.                    │
└─────────────────────────────────────────────────────────────────┘
```

#### Вариант A: События собирает агрегат

```php
final class Order
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    public function confirm(): self
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status->value, 'confirmed');
        }
        if ($this->items->count() === 0) {
            throw new EmptyOrderException($this->id);
        }

        $new = $this->withStatus(OrderStatus::Confirmed);
        $new->collectEvent(new OrderConfirmed(
            orderId: $this->id,
            customerId: $this->customerId,
            totalCents: $this->totalCents(),
            occurredAt: new \DateTimeImmutable(),
        ));

        return $new;
    }

    private function collectEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return DomainEvent[] */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];  // ← очищаем, чтобы не опубликовать дважды
        return $events;
    }
}
```

```php
// Application Service
final class ConfirmOrderCommand
{
    public function __invoke(int $orderId): Order
    {
        $order = $this->orders->find($orderId);
        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        $order = $order->confirm();  // ← событие собрано внутри

        $this->orders->save($order);  // ← сохранение (транзакция)

        // Публикация ПОСЛЕ сохранения
        foreach ($order->releaseEvents() as $event) {
            $this->bus->dispatch($event);
        }

        return $order;
    }
}
```

**Почему агрегат собирает, а не публикует:**

| Аспект | Агрегат публикует | Агрегат собирает, service публикует |
|--------|:-----------------:|:-----------------------------------:|
| Зависимость | Агрегат знает про `MessageBus` | Агрегат чистый PHP |
| Тестируемость | Need mock bus | Только assert на `releaseEvents()` |
| Транзакция | Сложно связать с save | Save → dispatch — последовательность явная |
| DDD чистота | Нарушает (инфраструктура в домене) | Соблюдает |

---

#### Вариант B: События создаёт application service

```php
final class ConfirmOrderCommand
{
    public function __invoke(int $orderId): Order
    {
        $order = $this->orders->find($orderId);
        $order = $order->confirm();  // ← только смена статуса, без событий

        $this->orders->save($order);

        // Событие создаётся здесь, в прикладном слое
        $event = new OrderConfirmed(
            orderId: $order->id,
            customerId: $order->customerId,
            totalCents: $order->totalCents(),
            occurredAt: new \DateTimeImmutable(),
        );
        $this->bus->dispatch($event);

        return $order;
    }
}
```

**Плюс:** проще, агрегат не знает про события.
**Минус:** событие создаётся **вне** доменной модели. Если доменная модель эволюционирует (добавили поле `discountCents`), нужно помнить обновить и здесь.

**Мой выбор:** Вариант A (агрегат собирает) для событий, которые являются **неотъемлемой частью** изменения состояния. Вариант B для событий, которые являются **следствием** оркестрации (например, `OrderPlaced` после confirm + payment + inventory).

---

### 3. Как обрабатывать: sync vs async

#### Sync (EventDispatcher Symfony)

```php
// src/Listener/OrderConfirmedListener.php
final class OrderConfirmedListener
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function onOrderConfirmed(OrderConfirmed $event): void
    {
        // Выполняется В ТОМ ЖЕ ПРОЦЕССЕ, В ТОЙ ЖЕ ТРАНЗАКЦИИ
        $payment = $this->gateway->charge($event->orderId, $event->totalCents);

        $order = $this->orders->find($event->orderId);
        $order = $order->markAsPaid();
        $this->orders->save($order);
    }
}
```

```yaml
# config/services.yaml
services:
    App\Listener\OrderConfirmedListener:
        tags:
            - { name: kernel.event_listener, event: 'App\Domain\Order\OrderConfirmed', method: onOrderConfirmed }
```

| Аспект | Оценка |
|--------|--------|
| Атомарность | **Высокая**: listener падает → транзакция откатывается |
| Порядок | **Гарантирован**: слушатели выполняются в порядке регистрации |
| Изоляция | **Низкая**: падение одного listener'а убивает весь запрос |
| Масштабируемость | **Ограниченная**: один процесс, одна память |
| Тестируемость | **Простая**: dispatch → assert |

**Когда:** операция **должна** быть атомарной с основной транзакцией. «Оплата обязательна для подтверждения заказа».

---

#### Async (Messenger + AMQP)

```php
// src/Handler/SendOrderConfirmationEmailHandler.php
final class SendOrderConfirmationEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function __invoke(OrderConfirmed $event): void
    {
        // Выполняется В ОТДЕЛЬНОМ ПРОЦЕССЕ, ВНЕ ТРАНЗАКЦИИ
        $user = $this->users->find($event->customerId);
        $this->mailer->send($user->email, 'Order confirmed!', /* ... */);
    }
}
```

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Domain\Order\OrderConfirmed': async  # → RabbitMQ
```

| Аспект | Оценка |
|--------|--------|
| Атомарность | **Нет**: письмо может не дойти, но заказ подтверждён |
| Порядок | **Нет гарантии**: 2 письма могут прийти в любом порядке |
| Изоляция | **Высокая**: падение email-сервиса не влияет на заказ |
| Масштабируемость | **Высокая**: N worker'ов |
| Тестируемость | **Сложнее**: нужно тестировать handler отдельно |

**Когда:** операция **не обязательна** для основной транзакции. «После подтверждения — отправить письмо, обновить индекс, логнуть».

---

#### Гибрид: sync для критичного, async для остального

```
OrderConfirmed
    │
    ├── (sync) PaymentGateway.charge()      ← атомарно
    ├── (sync) Inventory.reserve()          ← атомарно
    ├── (async) SendConfirmationEmail       ← не критично
    ├── (async) UpdateSearchIndex           ← не критично
    └── (async) NotifyAnalytics             ← не критично
```

```php
final class ConfirmOrderCommand
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly PaymentGatewayInterface $gateway,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly EventDispatcherInterface $events,  // sync
        private readonly MessageBusInterface $bus,          // async
    ) {}

    public function __invoke(int $orderId): Order
    {
        $order = $this->orders->find($orderId);
        $order = $order->confirm();

        // 1. Атомарные операции (sync, в транзакции)
        $payment = $this->gateway->charge($order->id, $order->totalCents());
        foreach ($order->items() as $item) {
            $this->inventory->reserve($item->productId, $item->qty);
        }

        $this->orders->save($order);
        // ← COMMIT

        // 2. Неатомарные операции (async, вне транзакции)
        $event = new OrderConfirmed(
            orderId: $order->id,
            customerId: $order->customerId,
            totalCents: $order->totalCents(),
            occurredAt: new \DateTimeImmutable(),
        );

        foreach ($order->releaseEvents() as $e) {
            $this->bus->dispatch($e);  // → AMQP → worker
        }

        return $order;
    }
}
```

---

### 4. Связь с транзакциями: Outbox Pattern

#### Проблема: «событие опубликовано, но транзакция откатилась»

```php
// НАИВНО
public function confirm(int $orderId): void
{
    $order = $this->orders->find($orderId);
    $order = $order->confirm();

    $this->bus->dispatch(new OrderConfirmed(/* ... */));  // → AMQP: SOUGHT!

    $this->orders->save($order);  // ← УПАЛО (DB timeout)
    // → транзакция откатилась
    // → но событие уже в очереди!
    // → worker обработает OrderConfirmed для несуществующего заказа
}
```

**Итого:** событие в очереди, а заказа нет в БД. Невозможно отозвать.

#### Решение: Transactional Outbox

**Идея:** событие пишется в **ту же таблицу БД**, что и данные агрегата. Отдельный процесс (outbox poller) читает таблицу и публикует в очередь.

```sql
CREATE TABLE outbox (
    id BIGSERIAL PRIMARY KEY,
    event_type VARCHAR(255) NOT NULL,
    payload JSONB NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    published_at TIMESTAMPTZ NULL,
    published_by VARCHAR(255) NULL,
    attempts INTEGER NOT NULL DEFAULT 0,
    max_attempts INTEGER NOT NULL DEFAULT 5
);

CREATE INDEX idx_outbox_unpublished ON outbox (created_at) WHERE published_at IS NULL;
```

```php
// Application Service
public function confirm(int $orderId): void
{
    $order = $this->orders->find($orderId);
    $order = $order->confirm();

    // В ОДНОЙ транзакции: save + outbox
    $this->em->transactional(function (EntityManagerInterface $em) {
        $this->persistOrder($order);

        // Событие в outbox (таблица в той же БД)
        $this->em->getConnection()->executeStatement(
            'INSERT INTO outbox (event_type, payload) VALUES (?, ?)',
            [
                'App\Domain\Order\OrderConfirmed',
                json_encode([
                    'orderId' => $order->id,
                    'customerId' => $order->customerId,
                    'totalCents' => $order->totalCents(),
                    'occurredAt' => (new \DateTimeImmutable())->format('c'),
                ]),
            ],
        );
        // ← если save упадёт → откат И save, И outbox
        // ← если outbox insert упадёт → откат ВСЕГО
    });
    // ← COMMIT: и заказ, и событие — атомарно
}
```

```php
// Outbox Poller (отдельный процесс, cron, или long-polling)
final class OutboxPoller
{
    public function __construct(
        private readonly Connection $db,
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger,
    ) {}

    public function run(): void
    {
        while (true) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT id, event_type, payload FROM outbox
                 WHERE published_at IS NULL AND attempts < max_attempts
                 ORDER BY created_at ASC
                 LIMIT 100
                 FOR UPDATE SKIP LOCKED'  // ← несколько poller'ов не конфликтуют
            );

            if ($rows === []) {
                usleep(500 * 1000); // 500ms
                continue;
            }

            foreach ($rows as $row) {
                $eventClass = $row['event_type'];
                $data = json_decode($row['payload'], true);
                $event = $this->rehydrate($eventClass, $data);

                try {
                    $this->bus->dispatch($event);  // → AMQP

                    // Успех: помечаем как опубликованное
                    $this->db->executeStatement(
                        'UPDATE outbox SET published_at = NOW(), published_by = :worker
                         WHERE id = :id',
                        ['worker' => self::WORKER_ID, 'id' => $row['id']],
                    );
                } catch (\Throwable $e) {
                    $this->db->executeStatement(
                        'UPDATE outbox SET attempts = attempts + 1 WHERE id = :id',
                        ['id' => $row['id']],
                    );
                    $this->logger->error('outbox.publish_failed', [
                        'id' => $row['id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
```

**Почему это работает:**

```
┌─────────────────────────────────────────────────────────────┐
│ ТРАНЗАКЦИЯ (атомарная)                                      │
│                                                             │
│  UPDATE orders SET status='confirmed', version=6            │
│  INSERT INTO outbox (event_type, payload) VALUES (...)      │
│  COMMIT                                                     │
│                                                             │
│  Либо ОБА — либо НИ ОДНОГО.                                │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ OUTBOX POLLER (отдельный процесс)                           │
│                                                             │
│  1. SELECT ... FOR UPDATE SKIP LOCKED                       │
│  2. AMQP publish                                            │
│  3. UPDATE outbox SET published_at = NOW()                  │
│                                                             │
│  Если AMQP упало → attempts+1, retry                        │
│  Если poller упал → следующий poller подхватит             │
└─────────────────────────────────────────────────────────────┘
```

| Свойство | Наивный dispatch | Outbox |
|----------|:---------------:|:------:|
| Атомарность с транзакцией | ❌ | ✅ |
| Потеря события при crash | ✅ (потеряно) | ❌ (в таблице) |
| Дубли при retry | ✅ | ✅ (нужна идемпотентность) |
| Сложность | Низкая | Средняя (poller + таблица) |
| Задержка | 0 ms | 0–500 ms (интервал poller) |

---

### 5. Проблемы, которые доменные события решают

| Проблема | Как решают |
|----------|-----------|
| **Жёсткая связь** между сервисами | Order не знает про Email, SearchIndex, Analytics |
| **Нарушение SRP** | `confirm()` не делает 5 вещей. Делает 1. Остальное — слушатели |
| **Тестирование** | Unit-тест `confirm()` не требует моков на email/search |
| **Расширяемость** | Новый слушатель — новый файл. Не трогаем `confirm()` |
| **Асинхронность** | Не блокируем ответ пользователю на email/search |
| **Отказоустойчивость** | Email упал → заказ подтверждён. Retry из outbox |
| **Аудит** | `outbox` — журнал всех событий. Кто, что, когда |

---

### 6. Проблемы, которые доменные события создают

| Проблема | Описание | Как mitigировать |
|----------|----------|-----------------|
| **Дубли** | At-least-once delivery: событие обработано 2 раза | Idempotency key в каждом handler |
| **Порядок** | `OrderConfirmed` → `PaymentSettled`. Если перепутали — баг | Single consumer per topic, или idempotency |
| **Отладка** | «Почему email не отправился?» → нужно искать listener, handler, outbox, AMQP | Корреляция: `eventId` в каждом логе |
| **Тестирование** | Integration-тест: нужно мокнуть AMQP, или использовать `sync` transport | `messenger.routing: sync` в test |
| **Консистентность** | Event издан, но handler ещё не обработал. Состояние «в полёте» | Event = факт, не команда. Consumer работает с актуальными данными |
| **Смерть letter** | После max_retries событие в DLQ. Кто обработает? | Манифест DLQ + алерт + ручной requeue |
| **Версионность** | Изменили payload `OrderConfirmed` (добавили `discountCents`). Старые события в очереди не имеют этого поля | Backward-compatible payload. Не удаляйте поля. |

---

### 7. Идемпотентность: обязательна

```php
final class SendConfirmationEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepositoryInterface $users,
        private readonly Connection $db,
    ) {}

    public function __invoke(OrderConfirmed $event): void
    {
        // Idempotency key: orderId + event type
        $idempotencyKey = hash('sha256', "OrderConfirmed:{$event->orderId}");

        // Атомарная проверка + запись
        $affected = $this->db->executeStatement(
            'INSERT INTO processed_events (idempotency_key, processed_at)
             VALUES (?, NOW())
             ON CONFLICT (idempotency_key) DO NOTHING',
            [$idempotencyKey],
        );

        if ($affected === 0) {
            $this->logger->info('email.skipped_duplicate', [
                'order' => $event->orderId,
            ]);
            return;  // ← уже обработали
        }

        $user = $this->users->find($event->customerId);
        $this->mailer->send(
            $user->email,
            'Order confirmed!',
            sprintf('Order #%d, total: %d ₽', $event->orderId, $event->totalCents),
        );
    }
}
```

```sql
CREATE TABLE processed_events (
    idempotency_key TEXT PRIMARY KEY,
    processed_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

**Почему обязательно:** AMQP / Kafka — at-least-once. Сообщение **может** быть доставлено 2 раза. Без идемпотентности — 2 письма, 2 индекса, 2 логирования.

---

### 8. Антипаттерны

| Антипаттерн | Почему плохо |
|-------------|-------------|
| **Событие как команда** | `OrderConfirmed` → handler делает `charge()`. Если charge упал → событие потеряно. Событие = факт, не инструкция |
| **Слушатель пишет в ту же таблицу** | `OrderConfirmed` → `UPDATE orders SET ...`. Циклическая зависимость, потеря атомарности |
| **50 listener'ов на одно событие** | Невозможно отследить, кто что делает. `OrderConfirmed` → 50 side effects. Отладка — боль |
| **Событие публикуется до COMMIT** | Транзакция откатилась → событие в очереди → inconsistent state |
| **Событие без payload (только ID)** | Handler должен делать `SELECT` по ID. Если запись удалена между publish и handle → баг. Payload — snapshot |
| **Событие как RPC** | `OrderConfirmed` → handler ждёт `PaymentSettled` → ждёт ответ. Событие — fire-and-forget. Если нужно ожидание — это request-response, не событие |
| **Порядок зависит от регистрации listener'ов** | `services.yaml`: A, B, C. Если переставили → баг. Порядок должен быть в доменной модели, не в конфиге |

---

### 9. Практическая структура

```
src/
├── Domain/
│   └── Order/
│       ├── Order.php              ← агрегат, собирает события
│       ├── OrderItem.php
│       ├── OrderStatus.php
│       ├── OrderRepositoryInterface.php
│       └── Event/
│           ├── OrderConfirmed.php     ← доменное событие
│           ├── OrderCancelled.php
│           └── OrderShipped.php
├── Application/
│   ├── Command/
│   │   ├── ConfirmOrderCommand.php    ← оркестрация
│   │   └── CancelOrderCommand.php
│   └── Handler/
│       ├── SendConfirmationEmailHandler.php  ← async
│       ├── UpdateSearchIndexHandler.php      ← async
│       └── NotifyAnalyticsHandler.php        ← async
├── Infrastructure/
│   ├── Persistence/
│   │   └── DoctrineOrderRepository.php
│   ├── Outbox/
│   │   ├── OutboxPoller.php          ← long-polling
│   │   └── OutboxEntity.php
│   └── Notification/
│       └── SendGridMailer.php
└── config/
    └── packages/
        ├── messenger.yaml
        └── doctrine.yaml
```

---

### 10. Итого

```
Доменное событие = ФАКТ, что произошло.

Публикуется:
  - После изменения состояния агрегата
  - В той же транзакции (outbox) или сразу после COMMIT
  - С payload (snapshot), не только ID

Обрабатывается:
  - Sync — если атомарность критична (payment, inventory)
  - Async — если можно отложить (email, search, analytics)

Обязательные механизмы:
  - Outbox pattern (атомарность с транзакцией)
  - Идемпотентность (at-least-once delivery)
  - Retry + DLQ (отказоустойчивость)
  - Корреляция (отладка)

НЕ делает:
  - Не ждёт ответа (это не RPC)
  - Не меняет состояние агрегата (это не команда)
  - Не зависит от порядка (каждый handler самодостаточен)
```

> ⏱ 48.7s · prompt 73193 tok · output 6146 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанный вопроса по DDD:
1. Чем VO отличается от Entity
2. Как решать, что VO, а что Entity
3. Что такое Ubiquitous Language и как влияет на код

Нужно дать структурированный ответ с примерами кода, так как это продолжение интервью на PHP-разработчика. Я уже использовал Money, Order, OrderItem в предыдущих ответах — можно опираться на них для консистентности.

Структура ответа:
- Фундаментальное различие (идентичность vs значение)
- Конкретные примеры кода (Money, Email — VO; Order, User — Entity)
- Практические критерии выбора
- Частые ошибки
- Ubiquitous Language: определение, примеры из кода, как нарушается

Буду честен: в реальном PHP-коде граница между VO и Entity часто размыта, и это нормально. Не нужно делать из VO «священный» объект — иногда «практичный VO» лучше «чистого».

Хочу избегать повторения того, что уже говорил (инварианты, readonly, aggregate). Фокус именно на VO vs Entity и на языке.

</details>

**Кандидат:**

## Value Object vs Entity и Ubiquitous Language

### 1. Фундаментальное различие

| | Entity | Value Object |
|---|---|---|
| **Идентичность** | По `id`. Два объекта с одинаковыми полями — **разные** | По значению. Два объекта с одинаковыми полями — **один и тот же** |
| **Мутабельность** | Может менять состояние со временем | Иммутабилен. «Изменение» = создание нового |
| **Жизненный цикл** | Создаётся → живёт → изменяется → умирает | Создаётся → существует. Не «живёт» |
| **Сравнение** | `===` (reference) | `==` (value) или `equals()` |
| **Репозиторий** | Да (load, save, remove) | Нет |
| **Пример** | `Order #42` | `Money(1500, 'RUB')` |

**Ключевой тест:** если вы замените `Order #42` на `Order #43` с точно такими же полями — это **другой** заказ. Если вы замените `Money(1500, 'RUB')` на другой экземпляр `Money(1500, 'RUB')` — это **то же самое** значение.

```php
// Entity: идентичность по ID
$order1 = Order::load(42);  // status: draft, total: 1500
$order2 = Order::load(42);  // та же строка в БД

$order1 === $order2;  // false (разные объекты в памяти)
$order1->id === $order2->id;  // true (один и тот же заказ)

// Value Object: идентичность по значению
$money1 = Money::of(1500, 'RUB');
$money2 = Money::of(1500, 'RUB');

$money1 === $money2;           // false (разные объекты)
$money1->equals($money2);      // true (одинаковое значение)
$money1->amount === $money2->amount;  // true
```

---

### 2. Почему это важно на практике

#### 2.1. Сравнение и дедупликация

```php
// Entity: нельзя дедуплицировать по значению
$orders = [
    new Order(1, 42, 'draft', 1500),
    new Order(2, 42, 'draft', 1500),  // ← другой ID, другой заказ
    new Order(1, 42, 'draft', 1500),  // ← дубликат (тот же ID)
];

// Дедупликация — по ID
$unique = array_unique($orders, fn($o) => $o->id);

// Value Object: можно дедуплицировать по значению
$amounts = [
    Money::of(1500, 'RUB'),
    Money::of(1500, 'RUB'),  // ← дубликат
    Money::of(2000, 'RUB'),
];

// Дедупликация — по значению
$unique = [];
foreach ($amounts as $m) {
    if (!isset($unique[$m->amount . ':' . $m->currency])) {
        $unique[$m->amount . ':' . $m->currency] = $m;
    }
}
```

#### 2.2. Мутабельность

```php
// Entity: мутирует
$order = Order::load(42);
$order->status = 'confirmed';  // ← та же строка в БД, изменена
$order->totalCents = 2000;    // ← та же строка, изменена
$repo->save($order);

// Value Object: не мутирует
$money = Money::of(1500, 'RUB');
$money = $money->add(Money::of(500, 'RUB'));  // ← НОВЫЙ объект
// $money1 больше не существует (GC)

// Невозможно:
$money->amount = 999999;  // ← Fatal error (readonly)
```

#### 2.3. Передача и использование

```php
// Entity: нужно передавать осторожно (shared reference)
function process(Order $order): void
{
    // Если кто-то мутирует $order — все видят изменение
    $order->status = 'cancelled';
}

$order = Order::load(42);
process($order);
// $order->status теперь 'cancelled'
// Но: кто ещё держит этот объект? Что он с ним сделает?

// Value Object: безопасно передавать
function total(Money $a, Money $b): Money
{
    return $a->add($b);
}

$m = Money::of(100, 'RUB');
$result = total($m, Money::of(200, 'RUB'));
// $m НЕ изменился. Результат — новый объект.
```

---

### 3. Как решать: VO или Entity?

#### Чеклист

```
Вопрос 1: Есть ли у этого объекта стабильный ID, который ссылается на него другие?
  ├── Да → Entity
  └── Нет → вопрос 2

Вопрос 2: Меняется ли он со временем (имеет lifecycle)?
  ├── Да → Entity
  └── Нет → вопрос 3

Вопрос 3: Два экземпляра с одинаковыми полями — это «одинаково» или «разно»?
  ├── Одинаково → Value Object
  └── Разно (у каждого своя история) → Entity
```

#### Примеры

| Концепция | VO / Entity | Почему |
|-----------|:-----------:|--------|
| `Money(1500, 'RUB')` | **VO** | Нет ID. Не живёт. Два `Money(1500)` — одно и то же |
| `Email('a@b.com')` | **VO** | Нет ID. Не живёт. Два `Email('a@b.com')` — одно и то же |
| `Address('Moscow', 'Lenina', '15')` | **VO** | Нет ID. Не живёт. Два одинаковых адреса — одно и то же |
| `Order(42)` | **Entity** | Есть ID. Живёт (draft → confirmed → shipped). Два `Order(42)` — один и тот же |
| `User(7)` | **Entity** | Есть ID. Живёт (reg → active → banned). Два `User(7)` — один и тот же |
| `OrderItem(99, orderId: 42)` | **Entity** (child) | Есть ID. Живёт внутри Order. Но **не** самостоятельная — нет своего репозитория |
| `Discount(10, 'percent')` | **VO** | Нет ID. Не живёт. Два `Discount(10)` — одно и то же |
| `Invoice(55)` | **Entity** | Есть ID. Живёт (created → paid → void). Две `Invoice(55)` — одна |

#### Серые зоны

| Концепция | Спор | Решение |
|-----------|------|---------|
| `PhoneNumber('+7 900 123-45-67')` | Может меняться? Нет. Может быть у 100 пользователей? Да. | **VO** |
| `Subscription('monthly', 990)` | Есть ID? Да (subscription_id). Меняется? Да (upgrade, cancel). | **Entity** |
| `Cart` | Есть ID? Да (session_id). Меняется? Да (add/remove). Живёт? Да (session). | **Entity** (хотя поведение — как VO) |
| `AuditLogEntry` | Есть ID? Да. Меняется? Нет (immutable после создания). | **Entity** (есть ID, есть репозиторий, но immutable) |

**Правило для серых зон:** если нужен **репозиторий** (load by ID, save, remove) — Entity. Если нет — VO.

---

### 4. Частые ошибки

#### Ошибка 1: «Entity с VO-поведением»

```php
// ПЛОХО: Entity, которая ведёт себя как VO
class Email
{
    private ?int $id = null;  // ← зачем?
    private string $address;

    public function equals(self $other): bool
    {
        return $this->address === $other->address;
    }
}

// ПРАВИЛЬНО: чистый VO
final class Email
{
    private function __construct(
        public readonly string $address,
    ) {}

    public static function of(string $address): self
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($address);
        }
        return new self($address);
    }

    public function equals(self $other): bool
    {
        return $this->address === $other->address;
    }
}
```

**Почему плохо:** `id` в `Email` бессмысленен. `Email` не имеет репозитория, не мутирует, не живёт. ID создаёт иллюзию, что `Email` — сущность, и разработчики начинают писать `$repo->find($emailId)`.

#### Ошибка 2: «VO с Entity-поведением»

```php
// ПЛОХО: VO, который мутирует
final class Money
{
    public int $amount;       // ← mutable!
    public string $currency;  // ← mutable!

    public function add(self $other): void  // ← void, не new
    {
        $this->amount += $other->amount;
    }
}

// Использование:
$a = Money::of(100, 'RUB');
$b = Money::of(200, 'RUB');
$a->add($b);
// $a.amount = 300 ← изменился!
// $b.amount = 200 ← не изменился
// Кого удивляет? Всех.
```

**Почему плохо:** `Money` — VO. VO не мутируют. Мутабельный VO — это Entity без ID. Хуже, чем Entity: нет ни идентичности, ни стабильности.

**Правильно:**

```php
final class Money
{
    private function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException();
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

$a = Money::of(100, 'RUB');
$b = Money::of(200, 'RUB');
$c = $a->add($b);
// $a = 100 (не изменился)
// $b = 200 (не изменился)
// $c = 300 (новый объект)
```

#### Ошибка 3: «Слишком много VO»

```php
// ПЛОХО: каждый параметр — отдельный VO
function charge(
    UserId $userId,
    Email $email,
    Money $amount,
    CurrencyCode $currency,
    Phone $phone,
    Address $address,
): void { /* ... */ }
```

**Почему плохо:** 6 объектов вместо 6 значений. Каждое создание — `new UserId(42)`, `new Email('a@b.com')`... Код зашумлён.

**Правило:** VO создавайте, когда:
- Есть **инварианты** (валидация формата)
- Есть **поведение** (методы `add()`, `equals()`, `contains()`)
- Нужно **сравнение по значению** (dedup, cache key)

Если просто «упаковка строки» без инвариантов и поведения — оставьте `string`.

```php
// Оправдано:
Money::of(1500, 'RUB')  // инвариант: amount >= 0, currency валидный. Метод: add().
Email::of('a@b.com')    // инвариант: валидный формат. Метод: equals().

// Неоправдано:
OrderId::of(42)         // просто int. Нет инвариантов. Нет поведения.
UserId::of(7)           // просто int. Нет инвариантов. Нет поведения.
```

---

### 5. Ubiquitous Language

#### Что это

Ubiquitous Language (Универсальный язык) — **общий словарь**, который используют **все** участники: разработчики, аналитики, тестировщики, продуктовые владельцы. Этот словарь **отражается в коде** — в именах классов, методов, полей.

#### Пример из практики

**Без Ubiquitous Language:**

```
Продуктовый владелец: «Когда клиент подтверждает покупку, мы списываем средства и резервируем товар.»

Разработчик: «Ок, я сделаю `processOrder()` с `executePayment()` и `decrementStock()`.»

Тестировщик: «А что значит "подтверждает покупку"? Это `POST /api/orders/confirm`? Или `POST /api/orders/checkout`?»

Аналитик: «А "списываем средства" — это `payment.status = 'charged'`? Или `payment.state = 'settled'`?»
```

**С Ubiquitous Language:**

```
Словарь:
  - Заказ (Order) — намерение купить
  - Подтверждение (Confirmation) — клиент подтвердил заказ
  - Оплата (Settlement) — средства списаны
  - Резервирование (Reservation) — товар зарезервирован
  - Отмена (Cancellation) — заказ отменён
  - Возврат (Refund) — средства возвращены

Код:
  Order::confirm()          ← «подтверждение»
  Payment::settle()         ← «оплата»
  Inventory::reserve()      ← «резервирование»
  Order::cancel()           ← «отмена»
  Payment::refund()         ← «возврат»
```

#### Как Ubiquitous Language влияет на код

**1. Имена методов отражают домен**

```php
// БЫЛО (техническое)
function updateStatus(int $orderId, string $newStatus): void
{
    // $newStatus = 'paid' | 'shipped' | 'cancelled'
    // Непонятно: кто может в какой статус?
}

// СТАЛО (доменное)
public function confirm(): Order       // «подтвердить»
public function markAsShipped(): Order // «отметить отправленным»
public function cancel(string $reason): Order  // «отменить»
```

Разработчик, читая код, **слышит** домен. Тестировщик пишет тест: `test_order_can_be_confirmed`. Не `test_update_status_1_to_2`.

**2. Имена исключений отражают правила**

```php
// БЫЛО
throw new \Exception('Error 402: payment failed');

// СТАЛО
throw new OrderCannotBeConfirmedException(
    orderId: $order->id,
    reason: 'payment declined',
);

throw new InsufficientStockException(
    productId: $item->productId,
    requested: $item->qty,
    available: $inventory->stock,
);
```

**3. Поля Entity отражают домен**

```php
// БЫЛО (техническое)
class OrderEntity
{
    private int $amount;       // ← сколько? чего?
    private int $qty;          // ← количество чего?
    private string $status;    // ← статус чего?
    private int $type;         // ← тип чего?
}

// СТАЛО (доменное)
class OrderEntity
{
    private int $totalCents;      // ← сумма заказа в центов
    private int $itemsCount;      // ← количество позиций
    private string $status;       // ← статус заказа (draft/confirmed/...)
    private string $channel;      // ← канал (web/phone/retail)
}
```

**4. События отражают домен**

```php
// БЫЛО
new Event('order.updated', ['status' => 'paid']);

// СТАЛО
new OrderConfirmed(orderId: 42, totalCents: 1500);
new PaymentSettled(paymentId: 7, orderId: 42, amountCents: 1500);
new StockReserved(productId: 99, qty: 2, orderId: 42);
```

**5. Тесты читаются как спецификация**

```php
// Тест на языке домена
public function test_order_cannot_be_confirmed_when_empty(): void
{
    $order = Order::create(customerId: 42);
    // order без items

    $this->expectException(EmptyOrderException::class);
    $order->confirm();
}

public function test_order_can_be_confirmed_when_has_items(): void
{
    $order = Order::create(customerId: 42);
    $order = $order->addItem(productId: 1, qty: 2, priceCents: 1000);

    $confirmed = $order->confirm();

    self::assertSame(OrderStatus::Confirmed, $confirmed->status);
}

public function test_refund_can_only_be_made_on_settled_payment(): void
{
    $payment = Payment::createFor(orderId: 42, amountCents: 1500);
    // status: pending

    $this->expectException(InvalidTransitionException::class);
    $payment->refund('reason');
}
```

Человек, не знакомый с кодом, но знакомый с доменом, **понимает** эти тесты.

#### Как выстроить Ubiquitous Language

```
1. Собрать глоссарий (1-страничный документ)
   - Термин | Определение | Пример
   - Заказ | Намерение купить товары | «Заказ #42 на сумму 1500 ₽»
   - Подтверждение | Клиент подтвердил заказ | «Заказ #42 подтверждён»
   - Оплата | Средства списаны с клиента | «Оплата по заказу #42 проведена»

2. Согласовать с командой (1-часовой воркшоп)
   - Продуктовый владелец определяет термины
   - Разработчик уточняет: «а что значит "подтверждение" технически?»
   - Тестировщик: «а как я это тестирую?»

3. Зафиксировать в коде
   - Имена классов, методов, полей — из глоссария
   - Code review: «тут "updateStatus" — а в глоссарии "confirm". Переименовать»

4. Обновлять при эволюции домена
   - Новый термин → добавить в глоссарий → переименовать код
   - Устаревший термин → пометить → миграция
```

#### Пример: как Ubiquitous Language меняет код

**Без:**

```php
// services/
order_service.php          // 500 строк
payment_service.php        // 300 строк
stock_service.php          // 200 строк
notification_service.php   // 100 строк

// methods:
processOrder($data)
executePayment($orderId)
decrementStock($productId, $qty)
sendEmail($to, $subject, $body)
```

**С:**

```
src/Domain/
├── Order/
│   ├── Order.php              ← «заказ»
│   ├── OrderItem.php          ← «позиция заказа»
│   ├── OrderStatus.php        ← «статус заказа»
│   ├── OrderConfirmed.php     ← «заказ подтверждён»
│   ├── OrderCancelled.php     ← «заказ отменён»
│   ├── EmptyOrderException    ← «пустой заказ»
│   └── OrderCannotBeConfirmedException
├── Payment/
│   ├── Payment.php            ← «оплата»
│   ├── PaymentStatus.php      ← «статус оплаты»
│   ├── PaymentSettled.php     ← «оплата проведена»
│   ├── PaymentRefunded.php    ← «возврат проведён»
│   └── PaymentDeclinedException
├── Inventory/
│   ├── Inventory.php          ← «инвентарь»
│   ├── StockReserved.php      ← «товар зарезервирован»
│   ├── StockReleased.php      ← «резерв снят»
│   └── InsufficientStockException

src/Application/
├── ConfirmOrderCommand.php    ← «подтвердить заказ»
├── CancelOrderCommand.php     ← «отменить заказ»
└── RefundPaymentCommand.php   ← «вернуть оплату»

src/Handler/
├── SendConfirmationEmailHandler   ← «отправить письмо о подтверждении»
├── UpdateSearchIndexHandler       ← «обновить поисковый индекс»
└── NotifyAnalyticsHandler         ← «уведомить аналитику»
```

**Результат:** новый разработчик открывает код и **слышит** домен. Не «processOrder → executePayment → decrementStock», а «подтвердить заказ → провести оплату → зарезервировать товар».

---

### 6. Связь VO / Entity / Ubiquitous Language

```
Ubiquitous Language
    │
    ├── Определяет: «Order — это сущность, имеет ID, живёт, меняется»
    │   → Entity
    │
    ├── Определяет: «Money — это значение, нет ID, не живёт, не меняется»
    │   → Value Object
    │
    ├── Определяет: «OrderItem — часть Order, нет своего репозитория»
    │   → Child Entity (внутри агрегата)
    │
    └── Определяет: «OrderConfirmed — факт, что произошло»
        → Domain Event
```

**Ubiquitous Language — это источник, из которого рождаются все эти классификации.** Если вы не можете сказать на языке домена, «Order — это...», — вы не можете решить, Entity это или VO.

---

### 7. Итого

```
Entity:   «Кто ты?» → ID. Живёт. Меняется. Есть репозиторий.
VO:       «Что ты?» → Значение. Не живёт. Не меняется. Нет репозитория.
Event:    «Что произошло?» → Факт. Однократный. Immutable. Нет репозитория.

Ubiquitous Language:
  Общий словарь команды, отражённый в именах кода.
  Без него: «processOrder()», «updateStatus()», «executePayment()».
  С ним:    «confirmOrder()», «markAsShipped()», «settlePayment()».

Практическое правило:
  Если в домене говорят «подтвердить заказ» — метод называется confirm().
  Если в домене говорят «провести оплату» — метод называется settle().
  Если в домене говорят «резервировать товар» — метод называется reserve().
  Код = зеркало языка домена.
```

> ⏱ 48.1s · prompt 79129 tok · output 5723 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о чистой архитектуре в PHP-приложении: слои, правило зависимостей, куда попадают Doctrine/Symfony/очередь, и как следить за границами. Это продолжение длинного разговора о DDD, SOLID, Doctrine, Symfony. Нужно дать структурированный ответ с конкретным примером проекта (например, заказ/оплата), показать реальную структуру каталогов, правило зависимостей (внутрь), конкретные классы в каждом слое, и инструменты контроля границ (PHPStan rules, ArchUnit-like проверки, code review). Не нужно перегружать — фокус на практической применимости в PHP/Symfony стеке.

</details>

**Кандидат:**

## Чистая архитектура в PHP/Symfony

### 1. Четыре слоя и правило зависимостей

```
┌──────────────────────────────────────────────────────────────────┐
│  INTERFACES (транспорт)                                          │
│  HTTP-контроллеры, CLI-команды, API-эндпоинты,                  │
│  обработчики очередей, webhook-обработчики                       │
│                                                                 │
│  «Как снаружи попадают данные внутрь»                            │
└──────────────────────────────────────────────────────────────────┘
                         │  зависит от
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│  APPLICATION (оркестрация)                                       │
│  Commands, Use Cases, Handlers, Policies                         │
│                                                                 │
│  «Что делать с данными: порядок вызовов, оркестрация»            │
└──────────────────────────────────────────────────────────────────┘
                         │  зависит от
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│  DOMAIN (ядро)                                                   │
│  Entities, Value Objects, Domain Events,                        │
│  Repository Interfaces, Domain Services                          │
│                                                                 │
│  «Что такое бизнес и каковы его правила»                         │
└──────────────────────────────────────────────────────────────────┘
                         │  НЕ зависит от
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│  INFRASTRUCTURE (услуги)                                         │
│  Doctrine, HTTP-клиенты, Mailer, Queue, Config, Logging         │
│                                                                 │
│  «Как взаимодействовать с внешним миром»                         │
└──────────────────────────────────────────────────────────────────┘
```

**Правило зависимостей (Dependency Rule):**

```
INTERFACES → APPLICATION → DOMAIN
                                    ↑
INFRASTRUCTURE ────────────────────┘
```

- Каждый слой может зависеть **только** от слоя ближе к центру (внутрь).
- **DOMAIN не знает** про ни один внешний слой.
- **INFRASTRUCTURE** зависит от DOMAIN (реализует его интерфейсы).
- **APPLICATION** зависит от DOMAIN (использует его модель).
- **INTERFACES** зависят от APPLICATION (вызывают use cases).

**Направление стрелок = направление зависимостей.** Внутрь — да. Назад (из домена наружу) — нет.

---

### 2. Что лежит в каждом слое

#### 2.1. DOMAIN (центр)

```
src/Domain/
├── Order/
│   ├── Order.php                          ← Entity (агрегат)
│   ├── OrderItem.php                      ← Entity (child)
│   ├── OrderStatus.php                    ← VO (enum)
│   ├── Money.php                          ← VO
│   ├── OrderRepositoryInterface.php       ← INTERFACE (контракт)
│   ├── Event/
│   │   ├── OrderConfirmed.php             ← Domain Event
│   │   ├── OrderCancelled.php
│   │   └── OrderShipped.php
│   ├── Exception/
│   │   ├── OrderCannotBeConfirmedException.php
│   │   ├── EmptyOrderException.php
│   │   └── InvalidTransitionException.php
│   └── Policy/
│       └── OrderTransitionPolicy.php      ← Domain Service (если логика общая)
├── Payment/
│   ├── Payment.php
│   ├── PaymentStatus.php
│   ├── PaymentRepositoryInterface.php
│   ├── PaymentGatewayInterface.php        ← INTERFACE (контракт на внешний мир)
│   ├── Event/
│   │   ├── PaymentSettled.php
│   │   └── PaymentRefunded.php
│   └── Exception/
│       ├── PaymentDeclinedException.php
│       └── InvalidRefundException.php
├── Inventory/
│   ├── Inventory.php
│   ├── InventoryRepositoryInterface.php
│   ├── Event/
│   │   ├── StockReserved.php
│   │   └── StockReleased.php
│   └── Exception/
│       └── InsufficientStockException.php
└── Shared/
    ├── DomainEvent.php                    ← base interface
    ├── AggregateRoot.php                  ← trait (releaseEvents)
    └── Money.php                          ← общий VO
```

**Что НЕ лежит в DOMAIN:**
- ❌ `use Doctrine\ORM\Entity`
- ❌ `use Symfony\Component\HttpFoundation\Request`
- ❌ `use Symfony\Component\Messenger\Handler\MessageHandlerInterface`
- ❌ `use Psr\Log\LoggerInterface`
- ❌ `new GuzzleHttp\Client()`
- ❌ `getenv()`, `$_ENV`, `config()`

**Что лежит:**
- ✅ Чистый PHP-классы
- ✅ Интерфейсы (контракты на инфраструктуру)
- ✅ Исключения (доменные)
- ✅ Инварианты, бизнес-логика

---

#### 2.2. APPLICATION (оркестрация)

```
src/Application/
├── Command/
│   ├── ConfirmOrderCommand.php            ← Use Case
│   ├── CancelOrderCommand.php
│   ├── RefundPaymentCommand.php
│   └── RegisterUserCommand.php
├── Handler/
│   ├── SendConfirmationEmailHandler.php   ← Async handler
│   ├── UpdateSearchIndexHandler.php
│   └── NotifyAnalyticsHandler.php
├── DTO/
│   ├── ConfirmOrderRequest.php            ← входной DTO (из транспорта)
│   └── OrderResponse.php                  ← выходной DTO (в транспорт)
└── Security/
    ├── OrderVoter.php                     ← доменный voter
    └── PaymentVoter.php
```

**ConfirmOrderCommand — типовой Use Case:**

```php
// src/Application/Command/ConfirmOrderCommand.php
final class ConfirmOrderCommand
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,       // ← доменный интерфейс
        private readonly PaymentGatewayInterface $gateway,       // ← доменный интерфейс
        private readonly InventoryRepositoryInterface $inventory,// ← доменный интерфейс
        private readonly MessageBusInterface $bus,              // ← контрак (Psr/Symfony)
    ) {}

    public function __invoke(int $orderId): Order
    {
        // 1. Загрузка (через доменный интерфейс)
        $order = $this->orders->find($orderId);
        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Доменная логика (через модель)
        $order = $order->confirm();

        // 3. Связанные агрегаты
        $payment = $this->gateway->charge($order->id, $order->totalCents());
        foreach ($order->items() as $item) {
            $this->inventory->reserve($item->productId, $item->qty);
        }

        // 4. Сохранение
        $this->orders->save($order);

        // 5. События (async)
        foreach ($order->releaseEvents() as $event) {
            $this->bus->dispatch($event);
        }

        return $order;
    }
}
```

**Правило:** Application зависит от **интерфейсов** DOMAIN, а не от реализаций. Не `DoctrineOrderRepository`, а `OrderRepositoryInterface`.

---

#### 2.3. INFRASTRUCTURE (услуги)

```
src/Infrastructure/
├── Persistence/
│   ├── OrderEntity.php                    ← Doctrine Entity
│   ├── OrderItemEntity.php
│   ├── DoctrineOrderRepository.php        ← реализует OrderRepositoryInterface
│   ├── DoctrinePaymentRepository.php
│   ├── DoctrineInventoryRepository.php
│   └── Mapping/
│       └── OrderEntity.orm.xml            ← mapping-файлы (если не attributes)
├── Payment/
│   ├── StripeGateway.php                  ← реализует PaymentGatewayInterface
│   ├── StripeExceptionMapper.php
│   └── Config/
│       └── stripe.yaml
├── Notification/
│   ├── SendGridMailer.php                 ← реализует NotifierInterface
│   └── Templates/
│       └── confirmation.html.twig
├── Search/
│   ├── MeilisearchIndexer.php             ← реализует SearchIndexerInterface
│   └── Config/
│       └── meilisearch.yaml
├── Message/
│   ├── OutboxPoller.php                   ← long-polling
│   └── OutboxEntity.php
├── Logging/
│   └── StructuredLogger.php
└── Config/
    ├── DatabaseConfig.php
    └── FeatureFlags.php
```

**DoctrineOrderRepository — реализация:**

```php
// src/Infrastructure/Persistence/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function find(int $id): ?Order
    {
        /** @var OrderEntity|null $entity */
        $entity = $this->em->find(OrderEntity::class, $id);
        if ($entity === null) return null;
        return $this->toDomain($entity);
    }

    public function save(Order $order): void
    {
        $entity = $this->toEntity($order);
        $this->em->persist($entity);
        $this->em->flush();
    }

    private function toDomain(OrderEntity $e): Order { /* ... */ }
    private function toEntity(Order $o): OrderEntity { /* ... */ }
}
```

**Ключевое:** Infrastructure **знает** про Doctrine. Domain **не знает**.

---

#### 2.4. INTERFACES (транспорт)

```
src/Interfaces/
├── Http/
│   ├── Controller/
│   │   ├── OrderController.php            ← Symfony Controller
│   │   └── PaymentController.php
│   ├── Serializer/
│   │   ├── OrderRequestDTO.php            ← входной DTO
│   │   └── OrderResponseDTO.php           ← выходной DTO
│   ├── Exception/
│   │   └── ApiExceptionHandler.php        ← перевод доменных исключений в HTTP
│   └── Listener/
│       └── ExceptionListener.php          ← Symfony kernel.exception
├── Cli/
│   └── Command/
│       ├── OutboxPollerCommand.php        ← Symfony Console
│       └── SeedDataCommand.php
├── Queue/
│   └── Handler/
│       ├── SendConfirmationEmailQueueHandler.php  ← Symfony Messenger handler
│       └── UpdateSearchIndexQueueHandler.php
└── Webhook/
    └── StripeWebhookController.php        ← webhooks от внешних сервисов
```

**OrderController — тонкий, только транспорт:**

```php
// src/Interfaces/Http/Controller/OrderController.php
final class OrderController
{
    public function __construct(
        private readonly ConfirmOrderCommand $confirm,  // ← Application
        private readonly CancelOrderCommand $cancel,
    ) {}

    #[Route('/api/orders/{id}/confirm', methods: ['POST'], name: 'order_confirm')]
    public function confirm(
        int $id,
        #[IsGranted(OrderVoter::CONFIRM, 'order')]  // ← доменный voter
    ): JsonResponse {
        /** @var Order $order */
        $order = $this->confirm($id);
        return $this->json(OrderResponseDTO::fromDomain($order), 200);
    }

    #[Route('/api/orders/{id}/cancel', methods: ['POST'], name: 'order_cancel')]
    public function cancel(
        int $id,
        Request $request,
        #[IsGranted(OrderVoter::CANCEL, 'order')]
    ): JsonResponse {
        $reason = $request->request->get('reason', 'no reason');
        $order = $this->cancel->execute($id, $reason);
        return $this->json(OrderResponseDTO::fromDomain($order), 200);
    }
}
```

**Контроллер НЕ знает про:**
- ❌ Doctrine, EntityManager
- ❌ Stripe, PaymentGateway
- ❌ Business-логику (confirm → что происходит)
- ❌ Domain-инварианты (empty order)

**Контроллер ЗНАЕТ про:**
- ✅ HTTP-методы, status codes, JSON
- ✅ DTO (вход/выход)
- ✅ Application Service (вызов use case)
- ✅ Authorization (voter)

---

### 3. Полный граф зависимостей (пример)

```
OrderController (Interfaces)
    │
    ├── зависит от: ConfirmOrderCommand (Application)
    │                 │
    │                 ├── зависит от: OrderRepositoryInterface (Domain)
    │                 ├── зависит от: PaymentGatewayInterface (Domain)
    │                 ├── зависит от: InventoryRepositoryInterface (Domain)
    │                 └── зависит от: MessageBusInterface (Contract)
    │
    └── зависит от: OrderVoter (Application/Security)
                      │
                      └── зависит от: Order (Domain)


DoctrineOrderRepository (Infrastructure)
    │
    ├── реализует: OrderRepositoryInterface (Domain)
    └── зависит от: OrderEntity, EntityManager (Doctrine)


StripeGateway (Infrastructure)
    │
    ├── реализует: PaymentGatewayInterface (Domain)
    └── зависит от: GuzzleHttp\Client, config


Order (Domain)
    │
    ├── зависит от: Money (Domain/Shared)
    ├── зависит от: OrderStatus (Domain)
    └── НЕ зависит от: ничего внешнего
```

**Стрелки идут ТОЛЬКО внутрь.** Обратных нет.

---

### 4. Где конкретные технологии

| Технология | Слой | Почему |
|-----------|------|--------|
| Symfony Controller | **Interfaces** | Транспорт: HTTP → домен |
| Symfony Request/Response | **Interfaces** | Транспорт |
| Symfony Security (Firewall, Authenticator) | **Interfaces** | Транспорт: «кто ты» до «что ты делаешь» |
| Symfony Voter | **Application** | Бизнес-правило «можно ли». Но технически — Symfony. Компромисс |
| Symfony Messenger (MessageBus) | **Contract** | Абстракция над transport. В `config/` — транспорт |
| Symfony Messenger (Worker) | **Interfaces** | Транспорт: очередь → handler |
| Symfony Serializer | **Interfaces** | Транспорт: JSON ↔ DTO |
| Doctrine EntityManager | **Infrastructure** | Персистентность |
| Doctrine Entity | **Infrastructure** | Персистентность |
| Doctrine Repository | **Infrastructure** | Реализация доменного интерфейса |
| Doctrine DBAL Connection | **Infrastructure** | Низкоуровневая БД |
| PostgreSQL | **Infrastructure** | Хранилище |
| Stripe API | **Infrastructure** | Внешний сервис |
| SendGrid API | **Infrastructure** | Внешний сервис |
| RabbitMQ / Redis | **Infrastructure** | Транспорт сообщений |
| Symfony YAML config | **Infrastructure** | Конфигурация |
| PHPUnit / PHPStan | **Все слои** | Качество |

---

### 5. Как следить, что границы не нарушаются

#### 5.1. PHPStan Custom Rule

```php
// tools/StaticAnalysis/NoDoctrineInDomainRule.php
namespace Tools\StaticAnalysis;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\IdentifierRuleError;

/**
 * Запрещает use-инструкции на Doctrine/Symfony в Domain-слое.
 *
 * @implements Rule<InClassNode>
 */
final class NoDoctrineInDomainRule implements Rule
{
    private const FORBIDDEN_PREFIXES = [
        'Doctrine\\',
        'Symfony\\',
        'GuzzleHttp\\',
        'Psr\\Log\\',
        'Psr\\Container\\',
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node instanceof InClassNode)) {
            return [];
        }

        $className = $node->getClassName();

        // Правило: классы в src/Domain/ не могут импортировать запрещённые пространства
        if (!str_starts_with($className, 'App\Domain\\')) {
            return [];
        }

        $errors = [];
        foreach ($node->node->getUses() as $use) {
            $useName = $use->name->toString();
            foreach (self::FORBIDDEN_PREFIXES as $prefix) {
                if (str_starts_with($useName, $prefix)) {
                    $errors[] = IdentifierRuleError::create(
                        sprintf(
                            'Domain class "%s" must not depend on "%s". ' .
                            'Introduce an interface in Domain layer.',
                            $className,
                            $useName,
                        )
                    );
                }
            }
        }

        return $errors;
    }
}
```

```json
// phpstan.neon
{
    "rules": [
        "Tools\\StaticAnalysis\\NoDoctrineInDomainRule",
        "Tools\\StaticAnalysis\\NoSymfonyInDomainRule"
    ]
}
```

**Результат:**

```
Line 12: Domain class "App\Domain\Order\Order" must not depend on
"Doctrine\ORM\EntityManager". Introduce an interface in Domain layer.
```

#### 5.2. Taint Analysis (PHPStan taint / Psalm)

```php
// PHPStan extension: отслеживает «грязные» данные (Request) до домена

// Правило: $request->get(...) не может попасть в метод доменной модели
// без промежуточного DTO

// ПЛОХО:
$order = Order::create($request->get('customer_id')); // ← taint error

// ХОРОШЕ:
$command = new CreateOrderCommand(
    customerId: (int) $request->get('customer_id'),  // ← DTO в Application
);
$order = $this->createOrder->execute($command);
```

#### 5.3. ArchUnit-подобная проверка (кастомный скрипт)

```php
// tools/Architecture/DependencyRuleChecker.php
<?php
// Проверяет, что файлы в src/Domain/ не содержат use-инструкций
// на запрещённые пространства имён.

$domainDir = __DIR__ . '/../../src/Domain';
$forbidden = [
    'Doctrine\\',
    'Symfony\\',
    'GuzzleHttp\\',
    'Psr\\Log\\',
];

$violations = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($domainDir)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;

    $content = file_get_contents($file->getPathname());
    preg_match_all('/^use\s+([^\s;]+);/m', $content, $matches);

    foreach ($matches[1] as $use) {
        foreach ($forbidden as $prefix) {
            if (str_starts_with($use, $prefix)) {
                $violations[] = sprintf(
                    "%s: use %s",
                    $file->getPathname(),
                    $use,
                );
            }
        }
    }
}

if ($violations !== []) {
    echo "ARCHITECTURE VIOLATIONS:\n";
    foreach ($violations as $v) {
        echo "  ✗ $v\n";
    }
    exit(1);
}

echo "✓ No architecture violations found.\n";
```

```bash
# .github/workflows/ci.yml
- name: Architecture check
  run: php tools/Architecture/DependencyRuleChecker.php
```

#### 5.4. Code Review чеклист

| Вопрос | Ожидание |
|--------|----------|
| Есть ли `use Doctrine\` в `src/Domain/`? | Нет |
| Есть ли `use Symfony\` в `src/Domain/`? | Нет |
| Контроллер знает про бизнес-логику? | Нет. Только DTO + вызов Command |
| Application знает про конкретный Gateway (Stripe)? | Нет. Только `PaymentGatewayInterface` |
| Domain знает про `EntityManager`? | Нет |
| Domain знает про `Request`? | Нет |
| Есть ли `new StripeClient()` вне Infrastructure? | Нет |
| Есть ли SQL-запрос вне Infrastructure? | Нет |
| Exception из Domain пойман в Controller? | Нет. Пойман в ExceptionListener (Interfaces) |
| DTO в Domain? | Нет. DTO в Application |

#### 5.5. Конфигурация Symfony: DI как enforcement

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    # Domain: НЕ автовайрим. Ручная сборка.
    App\Domain\*:
        autowire: false
        autoconfigure: false

    # Application: автовайрим, но только на интерфейсы Domain
    App\Application\*:
        autowire: true
        autoconfigure: true

    # Infrastructure: автовайрим на всё
    App\Infrastructure\*:
        autowire: true
        autoconfigure: true

    # Явные bindings
    App\Domain\Order\OrderRepositoryInterface:
        class: App\Infrastructure\Persistence\DoctrineOrderRepository

    App\Domain\Payment\PaymentGatewayInterface:
        class: App\Infrastructure\Payment\StripeGateway

    App\Domain\Inventory\InventoryRepositoryInterface:
        class: App\Infrastructure\Persistence\DoctrineInventoryRepository
```

**Эффект:** если в Domain-классе кто-то напишет `__construct(EntityManagerInterface $em)` — Symfony **не сможет** автовайрить его (autowire: false). Ошибка при сборке контейнера.

#### 5.6. Тестирование границ

```php
// tests/Architecture/DomainIsolationTest.php
final class DomainIsolationTest extends TestCase
{
    public function test_domain_classes_have_no_external_dependencies(): void
    {
        $dir = __DIR__ . '/../../src/Domain';
        $forbidden = ['Doctrine\\', 'Symfony\\', 'GuzzleHttp\\'];

        $violations = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file) {
            if ($file->getExtension() !== 'php') continue;

            $content = file_get_contents($file->getPathname());
            foreach ($forbidden as $prefix) {
                if (str_contains($content, "use {$prefix}")) {
                    $violations[] = $file->getPathname();
                    break;
                }
            }
        }

        self::assertEmpty($violations, 'Domain layer has external dependencies: ' . implode(', ', $violations));
    }

    public function test_domain_classes_are_not_publicly_constructed(): void
    {
        // Все публичные конструкторы в Domain — protected или private
        // (создание только через factories / reconstitute)
    }
}
```

---

### 6. Практический компромисс: Symfony + Чистая архитектура

Symfony — фреймворк, который **предполагает** определённую структуру. Жёсткая чистая архитектура иногда конфликтует с удобством Symfony.

**Компромиссы, которые я применяю:**

| Что | Чистая архитектура | Компромисс |
|-----|-------------------|------------|
| Controller | Тонкий, только DTO | Symfony `#[Route]`, `#[IsGranted]` — ок, это транспорт |
| Voter | В Application | Symfony `Voter` — ок, но логика в Domain-методе |
| `EventDispatcher` | Contract | Symfony реализует PSR — ок |
| `EntityManager` | Только в Infrastructure | Но `EntityManagerInterface` — контракт PSR — допустим в Application |
| `getenv()` / `config()` | Только в Infrastructure | Symfony `ParameterBag` — ok в Infrastructure |
| `#[ORM\Entity]` | Только в Infrastructure | Symfony/Doctrine attribute — ok в Infrastructure |
| `#[AsMessageHandler]` | Только в Interfaces | Symfony attribute — ok в Interfaces (Queue handler) |

**Главное:** **доменная модель** (`Order`, `Money`, `OrderStatus`) — чистый PHP. Всё остальное — допустимо.

---

### 7. Итоговая структура

```
app/
├── src/
│   ├── Domain/              ← ЯДРО. Чистый PHP. Без внешних зависимостей.
│   ├── Application/         ← Оркестрация. Зависит от Domain (интерфейсы).
│   ├── Infrastructure/      ← Услуги. Реализует интерфейсы Domain.
│   ├── Interfaces/          ← Транспорт. Symfony/CLI/Queue.
│   └── Shared/              ← Общие контракты (DomainEvent, AggregateRoot)
├── config/
│   ├── packages/            ← Конфигурация Symfony (security, messenger, doctrine)
│   └── services.yaml        ← DI (явные bindings: interface → implementation)
├── tests/
│   ├── Unit/
│   │   ├── Domain/          ← Unit-тесты доменной модели (без БД, без сети)
│   │   └── Application/     ← Unit-тесты use cases (мок Domain-интерфейсы)
│   ├── Integration/
│   │   ├── Infrastructure/  ← Тесты репозиториев (реальная БД)
│   │   └── Application/     ← Тесты с реальной БД, мок внешние API
│   ├── Feature/
│   │   └── Http/            ← Тесты контроллеров (HTTP → 200/403/500)
│   └── Architecture/        ← Проверка границ (DependencyRule, Taint)
├── tools/
│   └── StaticAnalysis/      ← PHPStan custom rules
└── bin/
    └── console              ← Symfony CLI
```

**Золотое правило:** если вы удалили `vendor/` и `config/`, доменная модель (`src/Domain/`) должна **собраться** и **протестироваться** без ошибок. Она не должна знать, что существует PostgreSQL, Stripe или Symfony.

> ⏱ 50.6s · prompt 84658 tok · output 6181 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о стратегии тестирования Symfony-приложения с PostgreSQL. Это продолжение длинного собеседования, где мы уже обсудили:
- PHP 8 фичи
- Типы PHP
- Исключения
- SOLID (DIP, SRP, OCP)
- DI контейнер Symfony
- Циклические зависимости
- Асинхронные side effects
- Контроль доступа (auth, voters)
- Value Objects в Doctrine
- Агрегаты в Doctrine
- N+1 запросы
- БД-ограничения
- Медленные запросы PostgreSQL
- Агрегаты в DDD
- Конкурентная модификация
- Доменные события
- VO vs Entity + Ubiquitous Language
- Чистая архитектура

Теперь вопрос про тестирование. Нужно ответить в том же стиле: структурно, с примерами кода, trade-offs, антипаттернами. Контекст — PHP-бэкенд, Symfony, PostgreSQL, DDD, чистая архитектура.

Структура ответа:
1. Пирамида тестов — что на каждом уровне
2. Типы тестов: Unit, Integration, Feature/E2E
3. Что мокаем, а что нет — конкретные правила
4. Как держать быстрыми: изоляция, без сети, without real DB где возможно
5. Как держать стабильными: детерминизм, без sleep, без случайности, без внешнего состояния
6. Антипаттерны
7. Практические приёмы: database transactions for test isolation, testcontainers, fixtures

Нужно показать, что я понимаю связь с чистой архитектурой (которую мы только что обсудили) — тестирование доменной модели без внешних зависимостей, integration-тесты репозиториев с реальной БД, feature-тесты через HTTP.

</details>

**Кандидат:**

## Стратегия тестирования Symfony + PostgreSQL

### 1. Пирамида: что и сколько

```
        ╱╲
       ╱  ╲        Feature / E2E (~5-10%)
      ╱────╲       HTTP → полный стек
     ╱      ╲
    ╱        ╲     Integration (~20-30%)
   ╱──────────╲    Домен + реальная БД
  ╱            ╲
 ╱              ╲   Unit (~50-60%)
╱────────────────╲  Чистый PHP, без БД
```

| Уровень | Что тестируем | Сколько | Скорость |
|---------|--------------|:-------:|:--------:|
| Unit | Доменная модель, VO, Application commands | 50–60% | < 1 мс/тест |
| Integration | Репозитории, реальные SQL, транзакции | 20–30% | 10–100 мс/тест |
| Feature | HTTP-запрос → ответ (полный стек) | 5–10% | 100–500 мс/тест |

**Почему не «всё E2E»:** 200 E2E-тестов × 500 мс = 100 секунд. + несостоятельность (flaky). + неясно, **где** сломалось. Unit-тест `test_order_cannot_be_confirmed_when_empty` — 0.3 мс, мгновенно показывает причину.

---

### 2. Unit-тесты: доменная модель

**Принцип:** доменная модель — чистый PHP. Никаких моков БД, нет Symfony-контейнера, нет сети. Только `new` и assert.

```php
// tests/Unit/Domain/Order/OrderTest.php
final class OrderTest extends TestCase
{
    public function test_cannot_confirm_empty_order(): void
    {
        $order = Order::create(customerId: 42);

        $this->expectException(EmptyOrderException::class);
        $order->confirm();
    }

    public function test_confirm_adds_domain_event(): void
    {
        $order = Order::create(customerId: 42);
        $order = $order->addItem(productId: 1, qty: 2, priceCents: 1000);
        $order = $order->confirm();

        $events = $order->releaseEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(OrderConfirmed::class, $events[0]);
        self::assertSame(42, $events[0]->orderId);
        self::assertSame(2000, $events[0]->totalCents);
    }

    public function test_cannot_ship_draft_order(): void
    {
        $order = Order::create(customerId: 42);
        $order = $order->addItem(productId: 1, qty: 1, priceCents: 500);

        $this->expectException(InvalidTransitionException::class);
        $order->markAsShipped();
    }

    public function test_status_transitions(): void
    {
        $order = Order::create(customerId: 42);
        self::assertSame(OrderStatus::Draft, $order->status);

        $order = $order->addItem(productId: 1, qty: 1, priceCents: 500);
        $order = $order->confirm();
        self::assertSame(OrderStatus::Confirmed, $order->status);

        $order = $order->markAsShipped();
        self::assertSame(OrderStatus::Shipped, $order->status);
    }

    public function test_add_item_rejects_negative_qty(): void
    {
        $order = Order::create(customerId: 42);

        $this->expectException(InvalidQuantityException::class);
        $order->addItem(productId: 1, qty: -1, priceCents: 500);
    }
}
```

**Что мокаем:** Ничего. Это чистый PHP. `Order::create()` — статическая фабрика. `Order->confirm()` — метод. Assert — сравнение.

**Скорость:** 0.2–0.5 мс на тест. 200 тестов = 0.1 с.

**Когда писать:**
- Каждый инвариант доменной модели
- Каждый переход состояния
- Каждое исключение
- Каждое доменное событие

---

### 3. Unit-тесты: Application (Use Cases)

Здесь — **моки на интерфейсы Domain**. Не на конкретные реализации.

```php
// tests/Unit/Application/ConfirmOrderCommandTest.php
final class ConfirmOrderCommandTest extends TestCase
{
    public function test_confirm_order_success(): void
    {
        $order = Order::create(customerId: 42)
            ->addItem(productId: 1, qty: 2, priceCents: 1000)
            ->confirm();

        // Моки — интерфейсы Domain, не конкретные классы
        $orders = $this->createMock(OrderRepositoryInterface::class);
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryRepositoryInterface::class);
        $bus = $this->createMock(MessageBusInterface::class);

        $orders->method('find')->with(42)->willReturn(
            Order::create(customerId: 42)->addItem(productId: 1, qty: 2, priceCents: 1000)
        );
        $gateway->method('charge')->willReturn(new PaymentResult('approved', 'tx_1'));
        $inventory->method('reserve')->willReturnSelf();
        $orders->expects(self::once())->method('save');
        $bus->expects(self::once())->method('dispatch');

        $command = new ConfirmOrderCommand($orders, $gateway, $inventory, $bus);
        $result = $command(42);

        self::assertSame(OrderStatus::Confirmed, $result->status);
        self::assertSame(2000, $result->totalCents());
    }

    public function test_confirm_order_fails_on_insufficient_stock(): void
    {
        $orders = $this->createMock(OrderRepositoryInterface::class);
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryRepositoryInterface::class);
        $bus = $this->createMock(MessageBusInterface::class);

        $order = Order::create(customerId: 42)->addItem(productId: 1, qty: 5, priceCents: 1000);
        $orders->method('find')->with(42)->willReturn($order);
        $inventory->method('reserve')
            ->willThrowException(new InsufficientStockException(productId: 1, requested: 5, available: 2));

        $command = new ConfirmOrderCommand($orders, $gateway, $inventory, $bus);

        $this->expectException(InsufficientStockException::class);
        $command(42);

        // Критично: save НЕ должен быть вызван
        $orders->expects(self::never())->method('save');
    }

    public function test_confirm_order_fails_on_not_found(): void
    {
        $orders = $this->createMock(OrderRepositoryInterface::class);
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryRepositoryInterface::class);
        $bus = $this->createMock(MessageBusInterface::class);

        $orders->method('find')->willReturn(null);

        $command = new ConfirmOrderCommand($orders, $gateway, $inventory, $bus);

        $this->expectException(OrderNotFoundException::class);
        $command(999);
    }
}
```

**Что мокаем:**
- ✅ `OrderRepositoryInterface` (доменный интерфейс)
- ✅ `PaymentGatewayInterface` (доменный интерфейс)
- ✅ `InventoryRepositoryInterface` (доменный интерфейс)
- ✅ `MessageBusInterface` (контракт PSR/Symfony)

**Что НЕ мокаем:**
- ❌ `DoctrineOrderRepository` (конкретная реализация)
- ❌ `StripeGateway` (конкретная реализация)
- ❌ `EntityManager` (инфраструктура)

**Скорость:** 1–5 мс на тест. 50 тестов = 0.2 с.

---

### 4. Integration-тесты: репозитории с реальной БД

**Принцип:** реальная PostgreSQL, реальные SQL, реальные индексы, реальные constraints. Мок только **внешние** сервисы (Stripe, SendGrid).

```php
// tests/Integration/Persistence/DoctrineOrderRepositoryTest.php
final class DoctrineOrderRepositoryTest extends IntegrationTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();  // ← запускает контейнер Symfony
        $this->em = self::getContainer()->get('doctrine')->getManager();
    }

    public function test_save_and_find_order(): void
    {
        $order = Order::create(customerId: 42)
            ->addItem(productId: 1, name: 'Widget', qty: 2, priceCents: 1000);

        $this->repo->save($order);

        $loaded = $this->repo->find($order->id);

        self::assertNotNull($loaded);
        self::assertSame(42, $loaded->customerId);
        self::assertSame(2, $loaded->items()->count());
        self::assertSame(2000, $loaded->totalCents());
    }

    public function test_remove_item_triggers_orphan_removal(): void
    {
        $order = Order::create(customerId: 42)
            ->addItem(productId: 1, name: 'Widget', qty: 2, priceCents: 1000)
            ->addItem(productId: 2, name: 'Gadget', qty: 1, priceCents: 500);

        $this->repo->save($order);

        // Проверяем в БД
        $count = $this->em->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM order_items WHERE order_id = ?',
            [$order->id],
        );
        self::assertSame(2, (int) $count);

        // Удаляем один item
        $item = $order->items()->first();
        $order = $order->removeItem($item->id);
        $this->repo->save($order);

        $count = $this->em->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM order_items WHERE order_id = ?',
            [$order->id],
        );
        self::assertSame(1, (int) $count);
    }

    public function test_version_conflict_throws_exception(): void
    {
        $order = Order::create(customerId: 42)
            ->addItem(productId: 1, name: 'Widget', qty: 1, priceCents: 1000);

        $this->repo->save($order);

        // Имитируем: другой процесс изменил version
        $this->em->getConnection()->executeStatement(
            'UPDATE orders SET version = 99 WHERE id = ?',
            [$order->id],
        );

        // Теперь save должен упасть (version mismatch)
        $order = $order->confirm();

        $this->expectException(ConcurrentModificationException::class);
        $this->repo->save($order);
    }

    public function test_db_constraint_rejects_negative_amount(): void
    {
        // PHP-инвариант не сработает (мы обходим доменную модель)
        $this->em->getConnection()->executeStatement(
            'INSERT INTO order_items (order_id, product_id, name, qty, price_cents)
             VALUES (1, 1, test, 1, -100)'
        );
        // → CHECK constraint violation
    }
}
```

**Изоляция данных:**

```php
// tests/IntegrationTestCase.php
abstract class IntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        if ($em->getConnection()->isTransactionActive()) {
            $em->getConnection()->rollBack();
        }

        // Очистка тестовых данных (если транзакция уже откатилась)
        $this->truncateTestTables();

        parent::tearDown();
    }

    private function truncateTestTables(): void
    {
        $conn = static::getContainer()->get('doctrine')->getManager()->getConnection();
        $conn->executeStatement('TRUNCATE orders, order_items, payments, inventory RESTART IDENTITY CASCADE');
    }
}
```

**Почему транзакция:** каждый тест начинает транзакцию, работает, откатывает. БД чистая между тестами. Никаких `DELETE FROM` в `setUp`. Никаких «остатков» от предыдущего теста.

**Скорость:** 10–50 мс на тест. 30 тестов = 1 с.

**Когда писать:**
- Сохранение/загрузка агрегата
- Orphan removal, cascade
- Version conflict
- DB constraints (CHECK, UNIQUE, FK)
- N+1 (assert на количество SQL-запросов)

---

### 5. Feature-тесты: HTTP → ответ

**Принцип:** полный стек (Symfony + Doctrine + БД). Мок только **внешние** API (Stripe, SendGrid).

```php
// tests/Feature/OrderApiTest.php
final class OrderApiTest extends WebTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->client = static::createClient();

        // Фикстура: пользователь с ролью
        $this->createUser('admin@example.com', ['ROLE_ADMIN']);
    }

    public function test_admin_can_confirm_order(): void
    {
        // 1. Создаём заказ через API (или репозиторий)
        $this->client->request('POST', '/api/orders', [
            'customerId' => 42,
            'items' => [
                ['productId' => 1, 'qty' => 2, 'priceCents' => 1000],
            ],
        ]);
        self::assertResponseStatusCodeSame(201);
        $orderId = json_decode($this->client->getResponse()->getContent(), true)['id'];

        // 2. Моким Stripe (внешний сервис)
        $this->mockStripe(['status' => 'approved', 'id' => 'tx_123']);

        // 3. Подтверждаем
        $this->client->request('POST', "/api/orders/{$orderId}/confirm");

        self::assertResponseStatusCodeSame(200);
        $body = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('confirmed', $body['status']);
        self::assertSame(2000, $body['totalCents']);
    }

    public function test_non_owner_cannot_cancel_order(): void
    {
        // Создаём заказ для customer 42
        $orderId = $this->createOrderForCustomer(42);

        // Логинимся как другой пользователь
        $this->loginAs('other@example.com');

        $this->client->request('POST', "/api/orders/{$orderId}/cancel");

        self::assertResponseStatusCodeSame(403);
    }

    public function test_confirm_empty_order_returns_422(): void
    {
        $orderId = $this->createEmptyOrder();

        $this->loginAs('admin@example.com');
        $this->client->request('POST', "/api/orders/{$orderId}/confirm");

        self::assertResponseStatusCodeSame(422);
        $body = json_decode($this->client->getResponse()->getContent(), true);
        self::assertStringContainsString('empty', $body['message']);
    }
}
```

**Скорость:** 100–500 мс на тест. 15 тестов = 5–7 с.

**Когда писать:**
- API-контракт (status code, JSON-структура)
- Authorization (403, 401)
- Валидация входных данных (422, 400)
- Ошибочные сценарии (404, 500)
- Полные потоки (create → confirm → cancel)

---

### 6. Что мокаем, а что нет

| Объект | Мокаем? | Почему |
|--------|:-------:|--------|
| Доменная модель (`Order`, `Money`) | **Нет** | Это то, что тестируем. Чистый PHP. |
| `OrderRepositoryInterface` (в Unit) | **Да** | Unit-тест не должен трогать БД |
| `OrderRepositoryInterface` (в Integration) | **Нет** | Integration — реальная БД |
| `PaymentGatewayInterface` | **Да** | Внешний API (Stripe). Никогда не в unit/feature |
| `NotifierInterface` | **Да** | Внешний API (SendGrid). Никогда не в unit/feature |
| `EntityManager` | **Нет** | Инфраструктура. Тестируется через репозиторий |
| `Request`, `Response` | **Нет** (в Feature) | Feature — реальный HTTP. В Unit — не нужно |
| `EventDispatcher` | **Да** (в Unit) | Не хотим запускать listener'ов в unit-тесте |
| `MessageBus` | **Да** (в Unit) | Не хотим отправлять в RabbitMQ в unit-тесте |
| PostgreSQL | **Нет** (в Integration/Feature) | Тестируем реальное SQL, индексы, constraints |
| Redis | **Да** | Кэш не критичен для логики. Мок или test-Redis |
| Clock / Time | **Да** | Детерминизм. `ClockInterface` → фиксированное время |

**Правило:**

```
Unit:      мок внешние интерфейсы. Никакой БД, нет сети.
Integration: реальная БД. Мок внешние API (Stripe, SendGrid).
Feature:   полный стек. Мок внешние API.
```

**Никогда не мокаем:**
- ❌ Доменную модель
- ❌ PostgreSQL (в Integration/Feature)
- ❌ Symfony-контейнер (в Integration/Feature)
- ❌ Инварианты (тестируем их, а не мокаем)

---

### 7. Быстрота: как держать

#### 7.1. Изоляция тестов (parallel)

```yaml
# phpunit.xml
<phpunit
    cacheResult="false"
    colors="true"
    processIsolation="false"
    beStrictAboutOutputDuringTests="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

```bash
# Paratest: параллельное выполнение
vendor/bin/paratest --processes=4 --testsuite Unit
vendor/bin/paratest --processes=2 --testsuite Integration
# Feature — последовательно (один контейнер Symfony)
vendor/bin/phpunit --testsuite Feature
```

**Результат:**
- Unit: 200 тестов × 0.5 мс = 0.1 с → с paratest (4 proc) = 0.03 с
- Integration: 30 тестов × 30 мс = 0.9 с → с paratest (2 proc) = 0.5 с
- Feature: 15 тестов × 300 мс = 4.5 с (последовательно)

**Итого: ~5 секунд на весь набор.**

#### 7.2. Без сети

```php
// ПЛОХО: тест ходит в реальный Stripe
$gateway = new StripeGateway(getenv('STRIPE_KEY'));
$gateway->charge(42, 1000);

// ХОРОШЕ: мок
$gateway = $this->createMock(PaymentGatewayInterface::class);
$gateway->method('charge')->willReturn(new PaymentResult('approved', 'tx_1'));
```

**Правило:** ни один тест не делает реальный HTTP-зазов наружу. Ни один.

#### 7.3. Без `sleep()`

```php
// ПЛОХО
$command->execute();
sleep(1);  // ждём, пока worker обработает
$result = $repo->find();

// ХОРОШЕ: sync transport в тестах
// config/packages/test/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\*': sync   # ← вызывается синхронно, без очереди
```

#### 7.4. Быстрая БД

```yaml
# docker-compose.test.yml
services:
    postgres:
        image: postgres:16-alpine
        environment:
            POSTGRES_DB: app_test
            POSTGRES_USER: test
            POSTGRES_PASSWORD: test
        command: >
            postgres
            -c shared_buffers=256MB
            -c work_mem=32MB
            -c effective_cache_size=512MB
            -c random_page_cost=1.1
```

**Alpine** — быстрый образ. `random_page_cost=1.1` — тест-БД в памяти (не диск). `shared_buffers` — достаточно для теста.

#### 7.5. Testcontainers (альтернатива docker-compose)

```php
// tests/TestDatabase.php
use Testcontainers\Postgresql\PostgresqlContainer;

final class TestDatabase
{
    public static function create(): PostgresqlContainer
    {
        return (new PostgresqlContainer('postgres:16-alpine'))
            ->withDatabase('app_test')
            ->withUsername('test')
            ->withPassword('test');
    }
}
```

**Плюс:** изолированный контейнер на каждый тест-ран. Нет «загрязнённой» БД.
**Минус:** первый запуск — 5–10 с (pull). Кэшируется.

---

### 8. Стабильность: как держать

#### 8.1. Детерминизм

| Источник нестабильности | Решение |
|------------------------|---------|
| `rand()`, `uniqid()` | Мок `RandomIdGenerator` или фиксированный seed |
| `new \DateTime()` | Мок `ClockInterface` |
| `getenv()` | Фиксированные env в test-конфигурации |
| Параллельные тесты пишут в одну таблицу | Транзакция + rollback per test |
| `sleep()` / таймауты | Sync transport, no sleep |
| Случайный порядок тестов | PHPUnit по умолчанию — порядок файлов. Не менять |
| Внешний сервис (Stripe) | Всегда мок |
| Часовой пояс | `TZ=UTC` в test-окружении |

#### 8.2. Изоляция данных

```php
// Каждый Integration/Feature тест:
protected function setUp(): void
{
    parent::setUp();
    $this->em->getConnection()->beginTransaction();
}

protected function tearDown(): void
{
    $this->em->getConnection()->rollBack();
    $this->truncateTables();  // страховка
    parent::tearDown();
    static::ensureKernelShutdown();
}
```

**Почему работает:** транзакция не видна другим соединениям. Rollback — чистая БД. `TRUNCATE` — страховка на случай, если транзакция уже откатилась (например, `flush()` внутри).

#### 8.3. Без «общего состояния»

```php
// ПЛОХО: статическое состояние между тестами
class PaymentGateway
{
    private static array $calls = [];  // ← накапливается

    public function charge(): void
    {
        self::$calls[] = func_get_args();
    }
}

// ХОРОШЕ: состояние в тесте
public function test_charge(): void
{
    $gateway = $this->createMock(PaymentGatewayInterface::class);
    $gateway->expects(self::once())->method('charge');
    // ...
}
```

#### 8.4. Idempotent-тесты

Тест должен давать **один и тот же** результат при любом количестве запусков:

```php
// ХОРОШЕ: idempotent
public function test_save_and_find(): void
{
    $this->truncate();  // ← чистый старт
    $order = Order::create(42)->addItem(1, 'Widget', 1, 1000);
    $this->repo->save($order);
    $loaded = $this->repo->find($order->id);
    self::assertSame(1000, $loaded->totalCents());
}

// ПЛОХО: зависит от предыдущего теста
public function test_save_and_find(): void
{
    // Предполагает, что "test_create" уже создал заказ
    $loaded = $this->repo->find(1);  // ← что если test_create упал?
}
```

**Правило:** каждый тест создаёт свои данные. Не опирается на другие тесты.

---

### 9. Антипаттерны

| Антипаттерн | Почему плохо |
|-------------|-------------|
| **E2E для каждого сценария** | 200 E2E × 500 мс = 100 с. Flaky. Неясно где сломалось |
| **Мок доменной модели** | Тестируете «как работает мок», а не «как работает бизнес» |
| **Мок БД в Integration** | Тест не проверяет SQL, индексы, constraints. Не Integration |
| **`sleep(1)` в тесте** | Flaky. Зависит от нагрузки. На CI — 3× дольше |
| **Один тест на 500 строк** | 10 сценариев в одном методе. Неясно, что упало |
| **Тест зависит от порядка** | `test_b` опирается на данные из `test_a`. Переставили → упало |
| **Реальные API-вызовы** | Stripe/SendGrid — внешний мир. Нестабильно. Дорого. |
| **Тест без assert** | Прогоняет код, не проверяет результат. «Smoke test» — не тест |
| **Тест на реализацию, не на поведение** | `assertThat($order->status === 'confirmed')` — ок. `assertThat($em->flushWasCalled())` — нет |
| **Один тест = 10 `assert`** | 10 независимых проверок в одном тесте. Один упал — остальные не прогнали. Разделить |

---

### 10. Структура тестов

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── Order/
│   │   │   ├── OrderTest.php              ← инварианты, transitions, events
│   │   │   ├── OrderItemTest.php
│   │   │   └── MoneyTest.php              ← VO: add, equals, invariants
│   │   ├── Payment/
│   │   │   └── PaymentTest.php
│   │   └── Inventory/
│   │       └── InventoryTest.php
│   ├── Application/
│   │   ├── ConfirmOrderCommandTest.php    ← use case с моками
│   │   ├── CancelOrderCommandTest.php
│   │   └── RefundPaymentCommandTest.php
│   └── Shared/
│       └── ClockTest.php
├── Integration/
│   ├── Persistence/
│   │   ├── DoctrineOrderRepositoryTest.php    ← save, find, orphan, version
│   │   ├── DoctrinePaymentRepositoryTest.php
│   │   └── NPlusOneTest.php                   ← assert на количество SQL
│   └── Event/
│       └── OutboxTest.php                     ← outbox + poller
├── Feature/
│   ├── OrderApiTest.php                       ← HTTP: 200, 403, 422, 404
│   ├── PaymentApiTest.php
│   └── AuthorizationTest.php                  ← роли, voters, 401/403
├── Architecture/
│   ├── DomainIsolationTest.php                ← нет Doctrine/Symfony в Domain
│   └── DependencyDirectionTest.php            ← стрелки только внутрь
└── TestCase/
    ├── UnitTestCase.php                       ← базовый, без БД
    ├── IntegrationTestCase.php                ← контейнер + транзакция
    └── FeatureTestCase.php                    ← WebTestCase + fixtures
```

---

### 11. CI-пайплайн

```yaml
# .github/workflows/test.yml
name: Tests
on: [push, pull_request]

jobs:
  unit:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: app_test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports: ['5432:5432']
        options: >
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-progress
      - run: vendor/bin/phpunit --testsuite Unit --coverage-clover=coverage.xml
      - run: vendor/bin/paratest --processes=4 --testsuite Unit

  integration:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: app_test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports: ['5432:5432']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
      - run: composer install
      - run: vendor/bin/phpunit --testsuite Integration

  feature:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: app_test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports: ['5432:5432']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
      - run: composer install
      - run: vendor/bin/phpunit --testsuite Feature

  architecture:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
      - run: composer install
      - run: vendor/bin/phpstan analyse --level 9
      - run: vendor/bin/phpunit --testsuite Architecture
      - run: php tools/Architecture/DependencyRuleChecker.php
```

**Время в CI:**
- Unit: ~10 с
- Integration: ~30 с
- Feature: ~60 с
- Architecture + PHPStan: ~20 с
- **Итого: ~2 минуты**

---

### 12. Итого

```
Быстро:
  - Unit без БД, без сети, без моков доменной модели
  - Paratest для параллельного выполнения
  - Alpine PostgreSQL + work_mem
  - Sync messenger в тестах (нет очередей)
  - Нет sleep()

Стабильно:
  - Транзакция + rollback per test (изоляция данных)
  - M

> ⏱ 63.4s · prompt 90746 tok · output 7973 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

The user is asking about concurrent modification of the same database record by two users. They want to know:
1. What problems can arise
2. What protection mechanisms I know
3. Comparison of approaches
4. When to choose which

This is a natural continuation of the previous conversation about concurrency (optimistic/pessimistic locking). I should structure this clearly, covering the problem space first, then each mechanism with concrete code examples, then a comparison table, and finally decision guidelines.

Let me think about what to cover:
- Lost update (the classic problem)
- Dirty read (less relevant in PG)
- Inconsistent state (partial updates)
- Deadlock
- Double spend / double operation

Protection mechanisms:
1. Optimistic locking (version field)
2. Pessimistic locking (SELECT FOR UPDATE)
3. SERIALIZABLE isolation
4. Advisory locks
5. Conditional UPDATE (WHERE with business condition)
6. UNIQUE constraint (for idempotency)
7. Application-level (mutex, queue)
8. Event sourcing (append-only log)

I should give concrete PHP/PostgreSQL examples, show the race condition visually, and provide a clear decision matrix. Let me make this practical and grounded in the Symfony/PostgreSQL context we've been discussing.

</details>

**Кандидат:**

## Одновременная модификация одной записи

### 1. Какие проблемы возникают

#### 1.1. Lost update (потерянное обновление)

Самая частая проблема. Два процесса читают одно и то же значение, оба пишут — один перезаписывает другого.

```
t=0  balance = 1000

t=1  Процесс A: SELECT balance → 1000
t=2  Процесс B: SELECT balance → 1000

t=3  Процесс A: UPDATE SET balance = 1000 - 300 = 700
t=4  Процесс B: UPDATE SET balance = 1000 - 200 = 800   ← перезаписал A!

t=5  balance = 800
     Ожидалось: 500 (1000 - 300 - 200)
     Потеря: 300
```

**В контексте нашего домена:**

```php
// А: админ отменяет заказ
$order = $repo->find(42);  // status = 'confirmed'
$order = $order->cancel(); // status = 'cancelled' (в памяти)
$repo->save($order);

// B: админ отправляет заказ (ПАРАЛЛЕЛЬНО)
$order = $repo->find(42);  // status = 'confirmed'  ← то же старое значение
$order = $order->markAsShipped(); // status = 'shipped'
$repo->save($order);       // ← перезаписал 'cancelled'
```

#### 1.2. Двойная операция (double operation)

```
Процесс A: списывает 500 с кошелька
Процесс B: списывает 500 с кошелька (одновременно)
→ списано 1000, а должно было быть 500 (одна операция)
```

#### 1.3. Непоследовательное состояние (torn read)

```
Заказ: status='confirmed', total=2000, items=[A, B]

Процесс A: удаляет item B → total должен стать 1500
Процесс B: добавляет item C → total должен стать 2500

Если A и B пересекаются:
  A: UPDATE total = 1500
  B: UPDATE total = 2500 (на основе 2000, не 1500!)
  → total = 2500, а items = [A, C] → 1500 + 1000 = 2500 ✓ (случайно ок)

Но если:
  A: UPDATE total = 1500, UPDATE items = [A]
  B: UPDATE total = 2500, UPDATE items = [A, B, C]  ← B уже удалён A!
  → total = 2500, items = [A, C] → неконсистентно
```

#### 1.4. Deadlock

```
Транзакция A: LOCK order_42 → LOCK inventory_7
Транзакция B: LOCK inventory_7 → LOCK order_42

A ждёт B, B ждёт A → deadlock
PostgreSQL детектирует за 1 с → откатывает одну транзакцию
```

#### 1.5. Ghost row (phantom read)

```
Транзакция A: SELECT * FROM orders WHERE status = 'draft' → [1, 2, 3]
Транзакция B: INSERT order_4 (status = 'draft')
Транзакция A: UPDATE orders SET status = 'confirmed' WHERE status = 'draft'
  → затрагивает [1, 2, 3, 4]  ← 4 появилось после SELECT
```

Менее критично в PostgreSQL (READ COMMITTED), но важно при `SERIALIZABLE`.

---

### 2. Способы защиты

---

#### 2.1. Optimistic Locking (version field)

**Механизм:** каждая запись имеет `version`. `UPDATE` содержит условие `WHERE version = :expected`. Если кто-то опередил → `affected rows = 0` → ошибка.

```sql
ALTER TABLE orders ADD COLUMN version INTEGER NOT NULL DEFAULT 0;
```

```php
// src/Infrastructure/Persistence/DoctrineOrderRepository.php
public function save(Order $order): void
{
    $expectedVersion = $order->version - 1;  // то, что мы читали

    $affected = $this->em->getConnection()->executeStatement(
        'UPDATE orders
         SET status = :status, version = version + 1
         WHERE id = :id AND version = :expected',
        [
            'status'    => $order->status->value,
            'id'        => $order->id,
            'expected'  => $expectedVersion,
        ],
    );

    if ($affected === 0) {
        throw new ConcurrentModificationException(
            sprintf('Order #%d was modified concurrently.', $order->id),
        );
    }

    // items — отдельный UPDATE (или через Doctrine cascade)
    $this->syncItems($order);
    $this->em->flush();
}
```

**Что при конфликте:**

```
Процесс A: SELECT → version=5
Процесс B: SELECT → version=5

Процесс A: UPDATE ... WHERE version=5 → affected=1, version→6, COMMIT
Процесс B: UPDATE ... WHERE version=5 → affected=0  ← version уже 6
           → ConcurrentModificationException
           → ретрайт (пере-читать, повторить логику)
```

**Ретрайт:**

```php
public function __invoke(int $orderId): Order
{
    $maxRetries = 3;

    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        try {
            return $this->execute($orderId);
        } catch (ConcurrentModificationException $e) {
            if ($attempt === $maxRetries - 1) {
                throw $e;  // 409 Conflict
            }
            usleep(50_000 * ($attempt + 1));  // 50ms, 100ms, 150ms
        }
    }
}

private function execute(int $orderId): Order
{
    $order = $this->repo->find($orderId);   // ← пере-читываем актуальное
    $order = $order->cancel($reason);        // ← логика на актуальном состоянии
    $this->repo->save($order);               // ← version +1
    return $order;
}
```

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Блокировка | **Нет.** Процессы не ждут друг друга |
| Latency | Низкая (один UPDATE) |
| Throughput | Высокий (нет очередей) |
| Ретрайт | Да. При высокой конкуренции — много |
| Deadlock | **Нет** (нет блокировок) |
| Сложность реализации | Низкая (одна колонка) |
| Когда ломается | При > 50% конфликтов (очень высокая конкуренция на одну строку) |

---

#### 2.2. Pessimistic Locking (SELECT FOR UPDATE)

**Механизм:** «захватить» строку при чтении. Другой процесс **ждёт** до COMMIT первого.

```php
public function findForUpdate(int $id): ?Order
{
    $row = $this->em->getConnection()->fetchAssociative(
        'SELECT * FROM orders WHERE id = :id FOR UPDATE',
        ['id' => $id],
    );
    return $row === false ? null : $this->toDomain($row);
}

public function cancel(int $id, string $reason): void
{
    $this->em->transactional(function (EntityManagerInterface $em) {
        // 1. Захват (блокирует строку)
        $order = $this->findForUpdate($id);

        // 2. Логика (на актуальном состоянии — другой процесс ещё не написал)
        $order = $order->cancel($reason);

        // 3. Сохранение
        $this->save($order);

        // 4. COMMIT → блокировка освобождена
    });
}
```

**Что при конфликте:**

```
Процесс A: SELECT FOR UPDATE → блокирует строку
Процесс B: SELECT FOR UPDATE → ЖДЁТ (lock wait)
Процесс A: UPDATE, COMMIT
Процесс B: получает блокировку → SELECT → status='cancelled'
          → cancel() → InvalidTransitionException (статус уже не 'confirmed')
```

**Второй процесс видит актуальное состояние** и принимает решение на его основе.

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Блокировка | **Да.** Второй ждёт |
| Latency | Зависит от первого (может ждать 1–5 с) |
| Throughput | Ограничен (очередь на строку) |
| Ретрайт | **Нет.** Второй видит актуальное |
| Deadlock | **Да** (если A ждёт B, B ждёт A) |
| Сложность | Средняя (нужен `lock_timeout`) |
| Когда ломается | При высокой конкуренции → очередь растёт → timeout |

**Защита от deadlock:**

```sql
SET lock_timeout = '5s';  -- если не получили блокировку за 5 с → ошибка
```

```php
try {
    $this->em->transactional(/* ... */);
} catch (LockTimeoutException $e) {
    // 409 Conflict: «попробуйте позже»
}
```

**Порядок блокировок (предотвращение deadlock):**

```php
// ВСЕГДА в одном порядке: order → inventory → payment
// Никогда: inventory → order (обратный порядок)

// Хорошо:
FOR UPDATE order_42
FOR UPDATE inventory_7

// Плохо (deadlock с другим процессом):
FOR UPDATE inventory_7
FOR UPDATE order_42
```

---

#### 2.3. Conditional UPDATE (business condition)

**Механизм:** условие в `WHERE` — не `version`, а **бизнес-состояние**.

```sql
UPDATE orders
SET status = 'shipped'
WHERE id = 42 AND status = 'confirmed';  ← только если ещё confirmed
```

```php
public function markAsShipped(int $orderId): void
{
    $affected = $this->em->getConnection()->executeStatement(
        "UPDATE orders SET status = 'shipped', version = version + 1
         WHERE id = :id AND status = 'confirmed'",
        ['id' => $orderId],
    );

    if ($affected === 0) {
        throw new InvalidTransitionException(
            'confirmed', 'shipped',
            "Order #{$orderId} is no longer in 'confirmed' state."
        );
    }
}
```

**Что при конфликте:**

```
Процесс A: UPDATE ... WHERE status='confirmed' → affected=1, status='cancelled'
Процесс B: UPDATE ... WHERE status='confirmed' → affected=0  ← status уже 'cancelled'
          → InvalidTransitionException
```

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Блокировка | **Нет** |
| Ретрайт | **Нет** (ошибка, не retry) |
| Deadlock | **Нет** |
| Точность | Точная: проверяет **бизнес-условие**, а не version |
| Ограничение | Работает только для **статусных** переходов. Не для «изменить сумму» |
| Сложность | Минимальная (одна строка SQL) |

**Когда:** статусные машины (order status, payment status). Не подходит для «изменить total» (нет дискретного условия).

---

#### 2.4. SERIALIZABLE isolation level

**Механизм:** PostgreSQL гарантирует, что параллельные транзакции эквивалентны последовательным. Конфликт → `SerializationFailure` → ретрайт.

```php
// На уровне подключения
$dsn = 'pgsql://user:pass@host/db?isolationLevel=SERIALIZABLE';

// Или на уровне транзакции
$this->em->getConnection()->executeStatement(
    'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE'
);
```

```php
public function transfer(int $fromId, int $toId, int $amount): void
{
    $this->em->transactional(function (EntityManagerInterface $em) {
        // Читаем
        $from = $this->accounts->find($fromId);
        $to = $this->accounts->find($toId);

        // Проверяем
        if ($from->balance < $amount) {
            throw new InsufficientBalanceException($fromId, $amount);
        }

        // Пишем
        $from->balance -= $amount;
        $to->balance += $amount;
        $em->flush();
    });
    // Если конфликт → SerializationException → retry
}
```

**Что при конфликте:**

```
T1: BEGIN SERIALIZABLE; SELECT account_1; UPDATE account_1; ...
T2: BEGIN SERIALIZABLE; SELECT account_1; UPDATE account_1; ...
T1: COMMIT
T2: COMMIT
  → ERROR: could not serialize access due to concurrent update
  → T2 откатывается → retry
```

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Блокировка | **Нет** (но PostgreSQL внутренне держит SSI-locks) |
| Ретрайт | Да (при конфликте) |
| Deadlock | **Нет** |
| Изоляция | **Максимальная** (сильнее S2) |
| Throughput | **Падает** при высокой конкуренции (много ретраев) |
| Сложность | Средняя (retry loop) |
| Когда ломается | > 100 concurrent на одну строку → ретраев слишком много |

---

#### 2.5. Advisory Lock (session-level mutex)

**Механизм:** «мьютекс» на уровне БД. Блокирует **не строку**, а **ключ** (bigint).

```php
public function withExclusiveLock(string $resource, callable $fn): mixed
{
    $key = $this->hashKey($resource);  // "order:42" → 847293561

    // pg_try_advisory_xact_lock — блокировка до конца транзакции
    $locked = $this->em->getConnection()->fetchOne(
        'SELECT pg_try_advisory_xact_lock(:key)',
        ['key' => $key],
    );

    if ($locked !== 't') {
        throw new LockBusyException($resource);
        // → 409 Conflict, ретрайт на уровне application
    }

    try {
        return $fn();  // ← критическая секция
    } finally {
        // pg_advisory_xact_lock освобождается автоматически при COMMIT/ROLLBACK
    }
}
```

```php
// Использование: «только один процесс может одновременно менять заказ #42»
$this->withExclusiveLock("order:{$orderId}", function () use ($orderId) {
    $order = $this->repo->find($orderId);
    $order = $order->cancel($reason);
    $this->repo->save($order);
});
```

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Блокировка | **Да** (на ключ, не на строку) |
| Гибкость | Блокирует **концепцию** («заказ #42»), а не таблицу |
| Multi-resource | Можно блокировать «order:42 + payment:77» одновременно |
| Deadlock | **Да** (если два процесса блокируют в разном порядке) |
| Когда | Распределённые операции, несколько агрегатов |

---

#### 2.6. UNIQUE constraint (идемпотентность)

**Механизм:** «только одна запись с этим ключом». Двойная операция → `UNIQUE violation`.

```sql
ALTER TABLE payments ADD CONSTRAINT uq_idempotency UNIQUE (idempotency_key);
```

```php
public function charge(int $orderId, int $amount, string $idempotencyKey): Payment
{
    try {
        $payment = Payment::create($orderId, $amount);
        $payment->idempotencyKey = $idempotencyKey;
        $this->em->persist($payment);
        $this->em->flush();  // ← UNIQUE violation при дубле
        return $payment;
    } catch (UniqueConstraintViolationException $e) {
        // Уже списано. Возвращаем существующее
        return $this->payments->findByKey($idempotencyKey);
    }
}
```

**Характеристики:**

| Аспект | Оценка |
|--------|--------|
| Защищает от | Двойной операции (double charge, double refund) |
| Не защищает от | Lost update (два разных значения) |
| Когда | Idempotency: «этот платёж уже проведён» |

---

### 3. Сравнительная таблица

| Критерий | Optimistic | Pessimistic | Conditional UPDATE | SERIALIZABLE | Advisory Lock | UNIQUE |
|----------|:---------:|:-----------:|:-----------------:|:------------:|:-------------:|:------:|
| Блокировка | Нет | Да | Нет | Нет (внутренние) | Да | Нет |
| Ретрайт | Да | Нет | Нет | Да | Да (busy) | Нет (dedup) |
| Deadlock | Нет | Да | Нет | Нет | Да | Нет |
| Throughput | Высокий | Низкий | Высокий | Средний | Низкий | Высокий |
| Latency | Низкая | Зависит от первого | Низкая | Средняя | Зависит | Низкая |
| Защита от lost update | ✅ | ✅ | ✅ (статус) | ✅ | ✅ | ❌ |
| Защита от double op | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Защита от inconsistent state | ✅ | ✅ | ✅ (статус) | ✅ | ✅ | ❌ |
| Сложность | Низкая | Средняя | Минимальная | Средняя | Средняя | Низкая |
| Scalability | Высокая | Низкая | Высокая | Средняя | Низкая | Высокая |

---

### 4. Когда какой выбираю

#### Сценарий 1: CRUD-операция, низкая конкуренция

> Админ редактирует описание заказа. 1–5 concurrent на строку.

**Выбор: Optimistic (version)**

```php
// Простая реализация, нет блокировок, ретрайт редкий
UPDATE orders SET description = :desc WHERE id = 42 AND version = 5;
// affected=0 → 409 Conflict → клиент «попробуйте ещё раз»
```

**Почему не Pessimistic:** блокировка на 1–5 с при низком concurrency — избыточно. Throughput не страдает.

---

#### Сценарий 2: Статусная машина (confirm, cancel, ship)

> Два админа одновременно делают `cancel` и `ship` на один заказ.

**Выбор: Conditional UPDATE + Version**

```sql
-- Оба работают:
UPDATE orders SET status = 'cancelled' WHERE id = 42 AND status = 'confirmed' AND version = 5;
UPDATE orders SET status = 'shipped'   WHERE id = 42 AND status = 'confirmed' AND version = 5;

-- Один получит affected=1, другой affected=0
```

**Почему не Pessimistic:** статусная машина — это **исключение**, а не норма. Быстрее и проще — один UPDATE с условием. Нет блокировки, нет deadlock.

**Почему не SERIALIZABLE:** избыточно для одного UPDATE.

---

#### Сценарий 3: Финансовая операция (balance, transfer)

> Два платежа одновременно списывают с одного кошелька.

**Выбор: Pessimistic (FOR UPDATE) или SERIALIZABLE**

```php
// Вариант A: FOR UPDATE
$this->em->transactional(function ($em) {
    $account = $this->accounts->findForUpdate(42);  // ← блокирует
    if ($account->balance < $amount) throw ...;
    $account->balance -= $amount;
    $em->flush();
});

// Вариант B: SERIALIZABLE + retry
for ($i = 0; $i < 3; $i++) {
    try {
        $this->em->transactional(function ($em) {
            $account = $this->accounts->find(42);
            if ($account->balance < $amount) throw ...;
            $account->balance -= $amount;
            $em->flush();
        });
        return;
    } catch (SerializationException $e) {
        if ($i === 2) throw $e;
        usleep(50_000);
    }
}
```

**Почему не Optimistic:** при 50 concurrent → 25+ ретраев. Каждый ретрайт = полный пере-проход. При Pessimistic — очередь, но каждый проходит один раз.

**Почему не Conditional UPDATE:** баланс — не дискретное состояние. Нет `WHERE balance = :expected` (может быть 999.99).

---

#### Сценарий 4: Двойная операция (double charge)

> Клиент нажал «Оплатить» дважды. Или retry из-за таймаута.

**Выбор: UNIQUE constraint (idempotency key)**

```sql
ALTER TABLE payments ADD CONSTRAINT uq_idem UNIQUE (idempotency_key);
```

```php
$payment = $this->payments->charge($orderId, $amount, $idempotencyKey);
// Внутрь: INSERT → UNIQUE violation → SELECT existing → return
```

**Почему не Pessimistic:** проблема не в конкуренции, а в **повторе**. Блокировка не поможет: второй запрос придёт **после** COMMIT первого.

---

#### Сценарий 5: Несколько агрегатов атомарно

> Confirm Order + Charge Payment + Reserve Inventory — атомарно.

**Выбор: Advisory Lock + Pessimistic внутри**

```php
$this->lock->withExclusiveLock("order:{$orderId}", function () {
    $this->em->transactional(function ($em) {
        $order = $this->orders->findForUpdate($orderId);
        $order = $order->confirm();

        $payment = $this->payments->findForUpdate($paymentId);
        $payment = $payment->charge();

        $inventory = $this->inventory->findForUpdate($productId);
        $inventory = $inventory->reserve($qty);

        $em->flush();
    });
});
```

**Почему не один FOR UPDATE на всё:** разные таблицы, разные строки. Advisory lock — на «концепцию» (заказ), внутри — FOR UPDATE на каждую строку.

**Почему не Optimistic на всё:** 3 агрегата × version conflict = 3× шанс ретраита.

---

#### Сценарий 6: Инвентарь (stock decrement)

> 100 заказов одновременно списывают товар.

**Выбор: Pessimistic + Conditional UPDATE (гибрид)**

```sql
-- Атомарный decrement с условием
UPDATE inventory
SET stock = stock - :qty,
    reserved = reserved + :qty,
    version = version + 1
WHERE product_id = :pid
  AND stock >= :qty;          ← бизнес-условие
  AND version = :expected;   ← optimistic (на случай, что кто-то изменил)

-- affected=0 → InsufficientStockException
```

**Почему:**
- `stock >= :qty` — бизнес-условие (не «version», а «достаточно ли»)
- `version = :expected` — защита от lost update на другие поля
- Один UPDATE — атомарно, нет race
- Throughput: PostgreSQL обрабатывает 10 000+ таких UPDATE/sec

---

### 5. Комбинации (реальные проекты)

В типичном Symfony-проекте все механизмы **используются вместе**:

```
┌─────────────────────────────────────────────────────────────────┐
│ Уровень защиты           │ Механизм                              │
├──────────────────────────┼──────────────────────────────────────┤
│ Доменная модель          │ Инварианты (confirm, cancel, reserve) │
│                          │ → невалидные состояния невозможны     │
├──────────────────────────┼──────────────────────────────────────┤
│ Репозиторий              │ Optimistic (version)                  │
│                          │ → lost update предотвращён            │
├──────────────────────────┼──────────────────────────────────────┤
│ БД (constraint)          │ CHECK, UNIQUE, FK                    │
│                          │ → raw SQL не нарушит инварианты      │
├──────────────────────────┼──────────────────────────────────────┤
│ БД (lock)                │ FOR UPDATE (инвентарь, баланс)        │
│                          │ → конкурентные операции serialized    │
├──────────────────────────┼──────────────────────────────────────┤
│ Application              │ Advisory Lock (multi-aggregate)       │
│                          │ → атомарность между агрегатами        │
├──────────────────────────┼──────────────────────────────────────┤
│ БД (idempotency)         │ UNIQUE (idempotency_key)             │
│                          │ → double operation предотвращена      │
├──────────────────────────┼──────────────────────────────────────┤
│ Application              │ Retry (exponential backoff)           │
│                          │ → временный конфликт → ретрайт        │
└──────────────────────────┴──────────────────────────────────────┘
```

---

### 6. Итого: правило выбора

```
Вопрос 1: Одна строка или несколько?
  ├── Одна → Optimistic / Conditional / Pessimistic
  └── Несколько → Advisory Lock + Pessimistic внутри

Вопрос 2: Статусная машина или число?
  ├── Статус (discrit) → Conditional UPDATE (WHERE status = :expected)
  └── Число (balance, stock) → Pessimistic (FOR UPDATE) или SERIALIZABLE

Вопрос 3: Высокая конкуренция (> 50 concurrent на строку)?
  ├── Да → Pessimistic (очередь) или SERIALIZABLE
  └── Нет → Optimistic (быстрее, меньше блокировок)

Вопрос 4: Возможность повтора (retry, double click)?
  ├── Да → UNIQUE (idempotency key)
  └── Нет → не нужно

Вопрос 5: Допустим ли ретрайт?
  ├── Да → Optimistic / SERIALIZABLE
  └── Нет (финансы) → Pessimistic / FOR UPDATE
```

**Золотое правило:** начните с **Optimistic + Conditional UPDATE**. Это покрывает 80% сценариев без блокировок. Добавляйте **Pessimistic** только там, где конкуренция высокая и ретрайт недопустим. Добавляйте **SERIALIZABLE** только для финансовых операций, где критична максимальная изоляция.

> ⏱ 56.3s · prompt 98356 tok · output 6723 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о git-процессе — от постановки задачи до продакшена. Это вопрос на собеседовании для PHP-бэкенд-разработчика. Нужно ответить структурированно, как опытный разработчик: ветвление, коммиты, PR, ревью, релизы. Важно не просто перечислить правила, а объяснить «почему так, а не иначе» — это ключевая часть вопроса.

Структурирую ответ так:
1. Общий флоу (от задачи до прода)
2. Ветвление — trunk-based, почему не git-flow
3. Формат коммитов — conventional commits, почему
4. Pull request — структура, чеклист
5. Ревью — кто, как, что ищем
6. Релизы — как деплоим, rollback
7. Почему так, а не иначе — обоснование каждого выбора

Надо ответить на русском, так как весь диалог на русском. Ответ должен быть практичным, без воды, с примерами команд.

</details>

**Кандидат:**

## Git-процесс: от задачи до продакшена

### 1. Общий флоу

```
Задача в трекере
    │
    ▼
Ветка feature/xxx из main
    │
    ▼
Разработка: несколько коммитов
    │
    ▼
PR → main
    │
    ├── CI: lint, phpstan, tests, build
    ├── Ревью (1–2 человека)
    └── Merge (squash)
    │
    ▼
main → deploy (auto или manual)
    │
    ▼
Prod. Мониторинг. При баге — hotfix из main
```

Весь цикл от начала задачи до прода — **1–3 дня** на типовую фичу. Не «спринт + релиз-менеджер + окно».

---

### 2. Ветвление: Trunk-Based, а не Git Flow

#### Как устроено

```
main  (всегда deploy-ready)
  │
  ├── feature/order-cancel       (1–3 дня)
  ├── feature/payment-refund     (1–2 дня)
  ├── fix/nplus1-orders          (0.5 дня)
  └── refactor/order-voter       (1 день)

hotfix/*   — только из main, только при P0-баге
```

**Правила:**

| Правило | Зачем |
|---------|-------|
| Все ветки — из `main` | Нет «дрейфа» от старого кода |
| Живут не более **3 дней** | Чем короче ветка, тем меньше merge conflicts |
| Не более **2 открытых PR от одного разработчика** | Фокус, не «параллельные миры» |
| `main` — всегда зелёный (CI + prod) | Никогда не merge'им красный |
| Нет `develop`, `release/*`, `staging` | Убираем 3 ветки, которые не несут ценности |

#### Почему не Git Flow

| Git Flow | Проблема |
|----------|----------|
| `develop` живёт неделями | Merge conflicts растут экспоненциально. Чем дольше ветка — тем сложнее merge |
| `release/*` создаётся из `develop` | Две линии развития: `develop` и `release`. Дублирование fixes |
| Релиз раз в 2–4 недели | Фича, готовая в понедельник, ждёт до пятницы. 100% времени — «ожидание окна» |
| `staging` как отдельная ветка/окружение | Две среды — два набора конфигов, две БД. Баг, найденный только в staging |
| 4+ ветки одновременно | Координируется релиз-менеджер. Если он в отпуске — стоп |

**Trunk-based убирает:** `develop`, `release/*`, `staging`. Остается `main` + короткие фичевые ветки. Деплой — по PR, не по «окну».

#### Единственное исключение: long-running feature

Если фича реально занимает 2–3 недели (новая подсистема):

```
1. Разбить на этапы:
   - feature/order-v1 (ядро, 1 неделя)
   - feature/order-v2 (расширения, 1 неделя)

2. Каждый этап — отдельный PR, merge в main
3. После каждого merge — feature flag отключает новую фичу
4. Финальный PR — включить feature flag
```

Feature flag — это «ветка во времени», а не «ветка в git».

---

### 3. Формат коммитов

#### Стиль: Conventional Commits (упрощённый)

```
<type>(<scope>): <subject>

[optional body]

[optional footer: BREAKING CHANGE, Closes #123]
```

**Типы:**

| Type | Когда |
|------|-------|
| `feat` | Новая функциональность |
| `fix` | Исправление бага |
| `refactor` | Рефакторинг без изменения поведения |
| `perf` | Оптимизация производительности |
| `test` | Добавление/исправление тестов |
| `chore` | Конфигурация, зависимости, CI |
| `docs` | Документация |

**Примеры:**

```
feat(order): add cancel command with reason
fix(payment): handle idempotency key collision on retry
refactor(inventory): extract reserve logic to domain service
perf(order): replace N+1 query with JOIN
test(order): add concurrent modification test
chore(deps): upgrade doctrine to 3.9
```

#### Правила

| Правило | Зачем |
|---------|-------|
| Subject — императив, без точки: `add cancel`, не `added cancel` | Читается как команда: `git log --oneline` → «add cancel», «fix idempotency» |
| Subject ≤ 72 символа | Виден в `git log` без обрезки |
| Scope — обязательный | Видно сразу, какой домен затронут |
| Body — только если «почему» неочевидно | Не дублируем PR-описание |
| `Closes #123` в footer | Авто-закрытие задачи в трекере |

#### Почему не «free-form»

```
# Плохо:
commit -m "fix stuff"
commit -m "wip"
commit -m "asdfasdfasdf"
commit -m "final"
commit -m "final2"
commit -m "final_final"

# Хорошо:
commit -m "fix(order): prevent double charge on idempotency key collision"
```

`git log --oneline` — это **журнал решений команды**. «final2» не даёт информации. «fix(order): prevent double charge...» — даёт.

#### Почему не строго (без scope, без type)

Строгий Conventional Commits с обязательным scope, type, footer — **overhead для 1-строчного фикса**. Мы используем упрощённую версию: type + scope обязательны, body и footer — по ситуации.

---

### 4. Pull Request

#### Структура PR

```markdown
## Что сделано
Краткое описание (2–3 предложения). Не дублируем коммиты.

## Почему
Какая задача/баг motivировал. Ссылка на трекер.

## Как проверено
- [ ] Unit-тесты: `vendor/bin/phpunit --testsuite Unit`
- [ ] Integration: `vendor/bin/phpunit --testsuite Integration`
- [ ] Ручная проверка: описываю шаги
- [ ] PHPStan level 9: `vendor/bin/phpstan analyse`

## Риски / Ограничения
- Изменён API: `/api/orders/{id}/cancel` теперь требует body
- Backward-incompatible: `Order.status` теперь enum, не string
- Миграция: `0042_add_order_version.sql` — запускается автоматически

## Скриншоты / Логи
(если UI или сложные логи)
```

#### Правила

| Правило | Зачем |
|---------|-------|
| **Один PR = одна логическая единица** | Ревьюер не гадает, что здесь за что. «Фича + рефакторинг + фикс» = 3 PR |
| **Максимум 400 строк diff** (включая тесты) | Выше — ревью поверхностное. Разбей |
| **CI зелёный — обязательное условие merge** | Красный CI = не merge. Без исключений |
| **Self-review перед отправкой** | Убираем очевидное. Ревьюер не читает diff построчно |
| **Не push в PR после ревью** (или push + уведомить) | Иначе ревьюер перечитывает. Лучше: squash + новый PR |

#### Почему не «большой PR»

```
# Антипаттерн:
PR: "Рефакторинг Order + фича cancel + фикс N+1 + апгрейд Doctrine"
Diff: 2400 строк
Ревью: 3 часа, 47 комментариев, 2 итерации
Результат: merge conflicts, ревью поверхностное
```

```
# Хорошо:
PR 1: "refactor(order): extract state machine to policy"    (300 строк, 30 мин)
PR 2: "fix(order): replace N+1 with JOIN"                    (80 строк, 10 мин)
PR 3: "feat(order): add cancel command"                      (200 строк, 20 мин)
```

Три PR × 20 мин ревью = 1 час. Один PR × 3 часа = 3 часа.

---

### 5. Ревью

#### Кто ревьюит

| Ситуация | Кто |
|----------|-----|
| Обычная фича | 1 коллега из команды |
| Изменение API-контракта | 1 коллега + 1 из frontend |
| Инфраструктура (CI, миграции) | 1 коллега + тимлид |
| Безопасность (auth, payment) | 2 человека (оба из бэкенда) |
| Hotfix | 1 человек + автор (кросс-ревью) |

**Не автор ревьюит свой код.** Всегда. Без исключений. Даже для 1-строчного фикса.

#### Что ищем в ревью

| Категория | Пример |
|-----------|--------|
| **Правильность** | Инвариант не нарушен? Edge case покрыт? |
| **Читаемость** | Имена отражают домен? Комментарий объясняет «почему»? |
| **Архитектура** | Граница слоёв не нарушена? Domain не знает про Doctrine? |
| **Тесты** | Покрыты happy path + error path? Нет «тестов на мок»? |
| **Производительность** | N+1? Неоптимальный запрос? Память? |
| **Безопасность** | SQL injection? Mass assignment? Secrets в логах? |
| **Совместимость** | Backward-incompatible? Миграция? API-контракт? |

#### Что НЕ ищем (делает CI)

| Автоматизируется | Инструмент |
|------------------|-----------|
| Стилистика | PHP-CS-Fixer, PHPStan |
| Типы | PHPStan level 9 |
| Тесты | PHPUnit |
| Секреты | gitleaks, trufflehog |
| Зависимости | Dependabot, Renovate |
| Конфигурация | yamllint |

**Ревьюер не читает `+ }` / `- }`.** Это делает CI. Ревьюер читает **логику и намерение**.

#### Формат комментариев

```
# Плохо:
"Это неправильно, переделай"

# Хорошо:
"[Вопрос] А что происходит, если $order->items() пустой?
В текущем коде totalCents() вернёт 0, и confirm() пройдёт.
Правильно ли это? Или нужен EmptyOrderException?"

# Плохо:
"А почему не через interface?"

# Хорошо:
"[Предложение] Здесь прямой вызов DoctrineOrderRepository.
Если в будущем появится MongoDB-реализация — этот код придётся менять.
Предлагаю ввести OrderRepositoryInterface в Domain-слое.
Пример: src/Domain/Order/OrderRepositoryInterface.php"
```

**Правило:** комментарий = вопрос или предложение, не приказ. И всегда с «почему» или «примером».

---

### 6. Merge и деплой

#### Merge strategy: Squash

```bash
# 5 коммитов в PR:
#   feat(order): add cancel command
#   feat(order): add tests
#   fix(order): handle empty items
#   feat(order): add event
#   chore: update config

# → 1 коммит в main:
#   feat(order): add cancel command with reason
```

**Почему Squash, а не Merge commit:**

| | Squash | Merge commit |
|---|---|---|
| `git log` | 1 запись = 1 фича | 6 записей = шум |
| `git bisect` | Быстро (1 commit = 1 фича) | Медленно (6 commits, 5 из них WIP) |
| `git revert` | 1 revert = 1 фича | 6 revert = хаос |
| История | Чистая | Заполненная WIP |

**Против Squash:** теряем промежуточные коммиты. **Ответ:** промежуточные коммиты — рабочий процесс, не история. История — «что изменилось», а не «как я к этому пришёл».

#### Деплой

```
main (зелёный CI)
    │
    ▼
Деплой на staging (автоматически, < 5 мин)
    │
    ├── Smoke-тесты (10 HTTP-запросов)
    ├── Health check
    └── Manual check (если API изменилось)
    │
    ▼
Деплой на prod (автоматически или 1 click)
    │
    ├── Канарийка: 1 инстанс → 10 мин мониторинг → 100%
    │   (для критичных изменений)
    │
    └── Обычный: rolling update (контенер за контейнером)
```

**Почему не «release window»:**

```
# Git Flow:
Понедельник: фича готова. Ждём.
Вторник: ещё ждём.
Среда: ещё ждём.
Четверг: «окно релиза в пятницу».
Пятница: деплой.
→ 5 дней на фичу, которая готова в понедельник.

# Trunk-based:
Понедельник: PR merge → деплой на prod.
→ 1 день.
```

#### Rollback

```bash
# Вариант 1: git revert (если баг в коде)
git revert -m 1 <merge-commit>
# → новый коммит-откат, деплой

# Вариант 2: feature flag (если баг в логике)
# → отключаем флаг, старый код работает

# Вариант 3: previous release (если баг в инфраструктуре)
# → helm rollback / kubectl rollout undo
```

**Правило:** rollback **быстрее**, чем hotfix. Если фикс занимает > 15 минут — откатываем, фиксим потом.

---

### 7. Hotfix

```
1. Ветка из main: hotfix/critical-bug
2. Минимальный фикс (не «пока рефакторим...»)
3. PR → main (ускоренный ревью: 1 человек, 15 мин)
4. Merge + деплой (красный канал, не ждём «окно»)
5. Если фикс затронул незавершённую фичу — backport в её ветку
```

**Почему из main, а не из develop:** в trunk-based `main` — единственная «истина». Нет `develop`, из которого можно делать hotfix.

---

### 8. CI-пайплайн

```yaml
# .github/workflows/ci.yml (упрощённо)
name: CI
on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

jobs:
  # 1. Быстрые проверки (параллельно)
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: composer validate
      - run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - run: vendor/bin/phpstan analyse --level 9 --no-progress
      - run: vendor/bin/psalm --no-cache

  # 2. Тесты (параллельно)
  unit:
    needs: lint
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/paratest --processes=4 --testsuite Unit

  integration:
    needs: lint
    services:
      postgres:
        image: postgres:16-alpine
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/phpunit --testsuite Integration

  feature:
    needs: lint
    services:
      postgres:
        image: postgres:16-alpine
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/phpunit --testsuite Feature

  # 3. Архитектура
  architecture:
    needs: lint
    runs-on: ubuntu-latest
    steps:
      - run: vendor/bin/phpunit --testsuite Architecture
      - run: php tools/Architecture/DependencyRuleChecker.php

  # 4. Build
  build:
    needs: [unit, integration, feature, architecture]
    runs-on: ubuntu-latest
    steps:
      - run: composer install --no-dev --optimize-autoloader
      - run: php bin/console cache:clear --env=prod
      - run: php bin/console doctrine:schema:validate

  # 5. Deploy (только при push в main)
  deploy:
    needs: [unit, integration, feature, architecture, build]
    if: github.ref == 'refs/heads/main' && github.event_name == 'push'
    runs-on: ubuntu-latest
    steps:
      - run: ./scripts/deploy.sh --env=staging
      - run: ./scripts/smoke-test.sh
      - run: ./scripts/deploy.sh --env=prod
```

**Время CI:** ~3–5 минут. Не 20 (как при sequential jobs).

---

### 9. Почему так, а не иначе: итоговая таблица

| Решение | Альтернатива | Почему выбрали |
|---------|-------------|---------------|
| Trunk-based | Git Flow | Нет «окон релиза», нет merge conflicts, нет 4 веток |
| Squash merge | Merge commit | Чистый `git log`, простой `revert`, простой `bisect` |
| Conventional Commits (упрощённый) | Free-form | `git log` — журнал решений, не мусорка |
| CI зелёный = обязательное | «Ну тут мелкое изменение» | Красный main = сломаный prod. Нет исключений |
| PR ≤ 400 строк | «Один большой PR» | Ревью < 30 мин, не 3 часа. Merge conflicts меньше |
| 1 PR = 1 фича | «Фича + рефакторинг + фикс» | `revert` одной фичи не ломает другую |
| Ревью ≠ автор | «Ну это мелкое изменение» | Второй взгляд ловит 30–50% багов. Дёшево. |
| Деплой из main | «Релиз из develop» | `main` = prod. Нет «staging-ветки», нет дрифта |
| Rollback < 15 мин | «Починим на месте» | Prod — не лаборатория. Откат дешевле, чем debug в 2 часа ночи |
| Feature flags для long-running | «Держим ветку 3 недели» | Ветка 3 недели = merge conflict 3 недели. Флаг — «ветка во времени» |

---

### 10. Коротко: 10 правил

```
1.  main — всегда зелёный, всегда deploy-ready
2.  Ветки живут ≤ 3 дня
3.  1 PR = 1 логическая единица
4.  Diff ≤ 400 строк (включая тесты)
5.  CI зелёный → merge. CI красный → fix. Нет «ну почти зелёный»
6.  Squash merge
7.  Ревью ≠ автор
8.  Rollback < 15 мин. Дёшево и сердито
9.  Feature flag > долгая ветка
10. git log — журнал решений, не черновик
```

> ⏱ 51.3s · prompt 104865 tok · output 4876 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

Пользователь продолжает серию вопросов в формате собеседования на PHP-бэкенд-разработчика. Вопрос про code review — это уже четвёртый-пятый вопрос в этой сессии. Нужно дать практичный, структурированный ответ, который показывает реальный опыт, а не учебник.

Ключевые аспекты, которые нужно покрыть:
1. Маркировка замечаний — конкретная система тегов (blocking, question, suggestion, nit)
2. Приоритизация — что смотрю в первую очередь, что во вторую
3. Разногласие — как поступаю, когда автор не согласен
4. Практические нюансы: время отклика, тон, формат

Нужно избегать:
- Учения в чистом виде ("ревьюер должен...")
- Абстрактных фраз без примеров
- Повторения того, что уже было сказано в предыдущих ответах (например, про структуру PR)
- Избыточной длины — вопрос сфокусированный, ответ тоже

Структура ответа:
- Система маркировки (конкретные теги с примерами)
- Приоритетная очерёдность проверки (что в первую очередь)
- Как пишу замечания (тон, формат)
- Разногласие (конкретный сценарий)
- Практические правила (время, количество, follow-up)

Примеры должны быть конкретными, с реальным кодом. Тон — спокойный, практичный, без пафоса.

</details>

**Кандидат:**

## Code Review на практике

### 1. Маркировка замечаний

Каждый комментарий в PR начинается с тега. Это убирает главный вопрос ревьюера: «это надо исправить или можно игнорировать?»

| Тег | Смысл | Действие автора |
|-----|-------|----------------|
| **`[blocking]`** | Не merge без исправления | Обязателен |
| **`[question]`** | Не понял намерение, уточни | Ответить, может не менять код |
| **`[suggestion]`** | Есть лучший вариант, но твой рабочий | По желанию |
| **`[nit]`** | Косметика, не трогаю merge | По желанию, можно пропустить |
| **`[praise]`** | Хвалю. Да, я это делаю. | — |

**Примеры в контексте:**

```diff
- $total = 0;
- foreach ($items as $item) {
-     $total += $item->qty * $item->price;
- }
+ $total = $items->reduce(fn($sum, $i) => $sum + $i->lineTotalCents(), 0);
```

```
[nit] Reduce читается компактнее, но оба варианта ок.
```

```php
$payment = $this->gateway->charge($orderId, $amount);
$this->orders->save($order);
// ← нет проверки, что charge прошёл успешно
```

```
[blocking] Если gateway->charge() бросит PaymentDeclinedException,
заказ сохранится в статусе confirmed, а оплата не проведена.
Нужен try/catch с rollback или сохранение после charge.
```

```php
class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}
}
```

```
[question] Почему EntityManager напрямую, а не через репозиторий?
В остальных сервисах мы используем OrderRepositoryInterface.
Это осознанное отклонение или oversight?
```

```php
if ($user->roles contains 'ROLE_ADMIN' or $user->id == $order->customer_id) {
    // allow
}
```

```
[suggestion] Этот pattern повторяется в 4 контроллерах.
Предлагаю OrderVoter::canAccess() — один метод, один тест.
Не блокирую, если есть план на потом.
```

```php
// src/Domain/Order/Money.php
final class Money
{
    private function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {}

    public static function of(int $amount, string $currency): self
    {
        if ($amount < 0) throw new NegativeAmountException($amount);
        if (!in_array($currency, ['RUB','USD','EUR'], true)) {
            throw new InvalidCurrencyException($currency);
        }
        return new self($amount, $currency);
    }
}
```

```
[praise] Immutable + invariants в конструкторе + readonly — ровно то,
что нужно для VO. Чисто.
```

**Почему теги важны:** без них автор не понимает, что можно пропустить. «Сделай `reduce`» звучит как приказ. `[nit]` — «я заметил, но не настаиваю». Это убирает фрустрацию с обеих сторон.

---

### 2. Что смотрю в первую очередь

Очерёдность — не случайна. От наиболее критичного к наименее.

#### Приоритет 1: Правильность (30 секунд на скан)

> «Сломается ли это в продакшене?»

- Инварианты доменной модели нарушены? (`Money(−50)` — можно ли создать?)
- Edge cases: `null`, пустая коллекция, `0`, `1`, `INT_MAX`
- Гонки: два процесса одновременно? (version, FOR UPDATE)
- Транзакции: что откатывается, если упадёт в середине?
- Исключения: кто ловит, что делает, не проглатывается ли тихо?

```php
// Вижу:
$order->items->clear();
$this->em->flush();
// ← где orphan removal? где cascade? если items = 0 → валидный заказ?
```

**Если на этом уровне есть проблема — остальное не читаю.** Блокирую, коммент, жду исправление.

#### Приоритет 2: Архитектура (2 минуты)

> «Правильно ли размещён код?»

- Границы слоёв: Domain не знает про Doctrine? Application не знает про Stripe?
- Зависимости: стрелки внутрь? `new ConcreteClass()` в домене?
- SRP: метод делает 5 вещей? Класс на 400 строк?
- DIP: сервис зависит от конкретного класса, а не интерфейса?

```php
// Вижу в src/Domain/Order/Order.php:
use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\MessageBusInterface;

// → [blocking] Domain-слой не может зависеть от Doctrine и Symfony.
//    Инкапсулируй за интерфейсом в Infrastructure.
```

#### Приоритет 3: Тесты (2–3 минуты)

> «Что покрыто, чего нет?»

- Happy path + каждый error path
- Тесты на инварианты (не на «метод не бросает»)
- Integration: реальная БД, не мок
- Feature: status codes, body, auth

```php
// Вижу:
public function test_confirm(): void
{
    $order = Order::create(42);
    $order = $order->confirm();
    self::assertSame('confirmed', $order->status);
}

// → [question] А если items пустой? А если status уже 'shipped'?
//    Это два разных сценария — два разных теста.
```

#### Приоритет 4: Производительность (1 минута)

> «Упадёт ли это при 1000 req/s?»

- N+1 запросы
- `SELECT *` на 50 колонок
- `sleep()` в запросном пути
- Утечки памяти (не `unset` временных объектов)

#### Приоритет 5: Стиль и читаемость (в конце)

> «Смогу ли я это прочитать через 6 месяцев?»

- Имена: `x`, `tmp`, `data2` → нет
- Комменты: «// делаем запрос» → нет. «// WHY: PostgreSQL не поддерживает ON DUPLICATE KEY» → да
- Длина метода: > 30 строк → разбить

**Эти пункты я помечаю как `[nit]`.** Не блокирую. Не настаиваю. Автор решает.

---

### 3. Как пишу замечания

#### Тон

```
# Жёсткий (избегать):
"Это неправильно. Переделай."
"Ты не понимаешь DDD."
"Это будет работать до первого пользователя."

# Конструктивный:
"[blocking] Здесь нет проверки на пустые items.
confirm() пройдёт, total = 0. Предлагаю EmptyOrderException
в Order::confirm(). Пример: ..."

"[question] Не понимаю намерения: зачем здесь raw SQL,
а не QueryBuilder? Если есть причина (performance,
JSONB) — ок, объясни. Если нет — предлагаю Qb."
```

**Принцип:** я комментирую **код**, не **автора**. «Здесь нет проверки» ≠ «ты не проверил».

#### Формат каждого замечания

```
[тег] Что я вижу.
      Почему это проблема (если неточно).
      Что предлагаю (конкретный код или ссылка).
      (Опционально) «Не блокирую, если [условие]».
```

**Пример:**

```
[blocking] Здесь $order->items() может быть пустым.
confirm() не проверяет это, и total = 0.
В результате: пустой заказ подтверждается, payment=0,
inventory не резервируется, email отправляется.

Предлагаю: в Order::confirm() добавить:
  if ($this->items->count() === 0) {
      throw new EmptyOrderException($this->id);
  }

И тест:
  public function test_cannot_confirm_empty_order(): void { ... }
```

#### Чего не делаю

| Не делаю | Почему |
|----------|--------|
| Не пишу «IMO» / «на мой взгляд» в `[blocking]` | Если блокирую — это объективно. Если субъективно — `[suggestion]` |
| Не комментирую то, что делает CI | PHPStan, CS-Fixer, тесты — их дело |
| Не пишу 20 комментариев за раз | Разбиваю: 3–5 ключевых + `[nit]` в конце |
| Не отвечаю «LGTM» без чтения | Если не прочитал — не LGTM |
| Не ревьюю в телефоне в очереди | Ревью требует концентрации. Не тороплюсь |
| Не пишу «+1» / «ага» на чужой комментарий | Если согласен — молча. Если нет — аргумент |

---

### 4. Разногласие с автором

#### Сценарий: автор не согласен с `[blocking]`

```
Я:   [blocking] Здесь нужен интерфейс, а не concrete class.
Автор: Это один реализатор, интерфейс — overengineering.

Я:   Понимаю. Давай так: если появится второй реализатор —
     введу интерфейс. Напишу follow-up задачу:
     "Ввести PaymentGatewayInterface при добавлении второго провайдера".
     Убираю [blocking]. Спасибо за пояснение.
```

**Почему так:**

1. **Признаю аргумент.** «Понимаю» — не «ок, ты прав». Убедил — ок. Не убедил — говорю.
2. **Компромисс, а не «я уступил».** Follow-up задача = «я согласен отложить, но не забыл».
3. **Не упрямлюсь.** Если аргумент сильнее — отступаю. Ревью — не битва.
4. **Фиксирую решение.** Не «ну ладно, как хочешь», а «записал задачу, снимаю блокировку».

#### Сценарий: автор считает, что `[blocking]` не по делу

```
Я:     [blocking] N+1 запрос в этом цикле.
Автор: Здесь максимум 3 элемента, N+1 — это 3 запроса. Не критично.

Я:     Согласен, что 3 запроса — не катастрофа.
       Но при 100 элементах (крупный заказ) — 100 запросов.
       И при масштабировании — 100 × 50 concurrent = 5000 запросов/сек.

       Предлагаю JOIN. Если ты хочешь оставить — ок,
       но давай зафиксируем: «здесь осознанно N+1,
       ограничение: items ≤ 10».

Автор: Ок, добавлю комментарий и ограничение в доменную модель.
```

**Что здесь важно:**

- Я **не настаиваю**, когда аргумент автора справедлив.
- Я **количественно** обосновываю («100 × 50 = 5000»), а не «это плохо».
- Я **предлагаю компромисс**: комментарий + ограничение.
- Я **фиксирую решение**: «здесь осознанно N+1».

#### Сценарий: я ошибся

```
Я:     [blocking] Здесь нужно FOR UPDATE, иначе race condition.
Автор: Здесь уже есть version field. FOR UPDATE избыточен.

Я:     Ты прав, я не заметил version. Убираю [blocking].
       Извини за ложное замечание.
```

**Почему это важно:** признать ошибку — не слабость. Это сигнал автору: «я читаю внимательно, но могу ошибаться». Если никогда не признаю — автор перестаёт доверять моим `[blocking]`.

---

### 5. Практические правила

| Правило | Деталь |
|---------|--------|
| **Время отклика** | < 4 часов в рабочее время. Если не могу — пишу «вижу сегодня вечером» |
| **Количество** | 3–5 ключевых замечаний + `[nit]`. Не 25 |
| **Первый проход** | Правильность + архитектура. Если ок — второй проход: тесты, стиль |
| **Ревью после merge** | Если нашёл баг после merge — `git revert` + hotfix. Не «ну там мелкое изменение» |
| **Follow-up** | Если `[suggestion]` не выполнен — не напоминаю. Автор решил |
| **Не ревьюю в споре** | Если голос повысился — откладываю на час. Выхожу из чата |
| **Ревью = обучение** | Если объясняю — объясняю с примером. Не «потому что так правильно» |

---

### 6. Итого: мой чеклист перед тем, как отправить замечание

```
1. Я понял намерение автора? (если нет — [question], не [blocking])
2. Это реально сломается в продакшене? (если да — [blocking])
3. Есть ли более простое решение, которое я не вижу?
4. Могу ли я объяснить «почему» за 2 предложения?
5. Это делает CI? (если да — не пишу)
6. Автор сможет согласиться без потери гордости?
7. Я написал теги? (blocking / question / suggestion / nit)

Если на любой вопрос «нет» — переписываю.
```

> ⏱ 40.2s · prompt 109560 tok · output 3526 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит перевести сырую бизнес-проблему в формулировку задачи для разработчика. Это вопрос о том, как я мыслю как техлид/архитектор, переводя бизнес-боли в инженерную задачу.

Мне нужно показать:
1. Как я декомпозирую проблему
2. Что именно входит в формулировку задачи (acceptance criteria, scope, out of scope, контекст, ограничения)
3. Почему каждый элемент нужен
4. Как избежать «уточнений» — т.е. задача должна быть самодостаточной

Структура ответа:
- Сначала покажу саму сформулированную задачу (artifact)
- Потом разберу, что в ней и почему
- Потом покажу, чего НЕ включил и почему (out of scope)
- Потом — типичные ошибки в формулировках задач

Хочу быть конкретным: не абстрактные «напиши acceptance criteria», а реальный текст задачи с критериями, примерами, ограничениями.

Сделаю так:
1. Сырая проблема (кратко)
2. Сформулированная задача (полный текст, как если бы я писал в трекере)
3. Разбор: почему каждый блок нужен
4. Чего НЕ включил и почему
5. Типичные ошибки формулировок

Это продолжение собеседования, тон — разговорный, экспертный, с примерами кода где уместно.

</details>

**Кандидат:**

## Из «поддержка завалена» в задачу

### Сырой вход

> «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями.»

Это **проблема**, не задача. В ней нет: что именно «не могут найти», на чём ищут, что значит «из прошлого», какая доля обращений, какой SLA. Если передать это исполнителю как есть — через 2 часа вернётся вопрос: «а что именно делать?»

---

### Сформулированная задача

```
Задача: Поиск и просмотр истории заказов пользователем
ID: ORD-142
Приоритет: High
Оценка: 5 story points
Спринт: 23

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ПРОБЛЕМА
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
38% обращений в поддержку (127 за неделю) — это
«найдите мой заказ». Средний ответ: 4.2 часа.
Причина: у пользователя нет страницы/экрана, где
он видит свои прошлые заказы. Только последний
заказ виден в личном кабинете.

Цель: пользователь сам находит заказ за < 30 секунд
без обращения в поддержку. Ожидаемый эффект:
−30 обращений/нед, −26 часов работы поддержки/нед.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ДОПУСТИМОЕ ПОЛЕ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Экран «Мои заказы» в личном кабинете:
   - Список заказов текущего пользователя
   - По каждому: дата, статус (draft/confirmed/shipped/cancelled),
     сумма, количество позиций, номер заказа
   - Пагинация: 20 заказов на страницу
   - Сортировка: по дате desc (новые сверху)

2. Фильтры (на клиенте, без доп. запросов к БД):
   - По статусу: все / в процессе / доставленные / отменённые
   - По периоду: последние 30 / 90 / 365 дней / весь период

3. Детализация заказа:
   - Клик по заказу → страница с деталями
   - Позиции: название, qty, цена, line total
   - Итого: сумма, discount, total
   - Статус + дата изменения
   - Если подтверждён: номер платежа, дата оплаты

4. API:
   GET /api/orders?customer_id={me}&status={filter}&page={n}&per_page=20
   GET /api/orders/{id}
   Оба требуют авторизации.
   Customer видит только СВОИ заказы.
   Чужой заказ → 404 (не 403, чтобы не раскрывать существование).

5. Индекс:
   CREATE INDEX CONCURRENTLY idx_orders_customer_date
       ON orders (customer_id, created_at DESC);
   (Сейчас такого индекса нет. SELECT без индекса —
   Seq Scan на 15 млн строк. С индексом — < 1 мс.)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ОГРАНИЧЕНИЯ И КОНТЕКСТ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- Агрегат Order уже существует (см. src/Domain/Order/).
  Поля: id, customer_id, status, created_at, total_cents.
  OrderItem: id, order_id, product_id, name, qty, price_cents.
  Не создавать новую сущность. Использовать существующий агрегат.

- Репозиторий: OrderRepositoryInterface уже есть.
  Добавить метод:
    findByCustomer(int $customerId, int $page, int $perPage,
                   ?string $statusFilter): array

- Авторизация: currentUser уже доступен через
  #[IsGranted('IS_AUTHENTICATED_FULLY')].
  customer_id брать из token, не из query string.

- Пагинация: keyset (WHERE created_at < :cursor),
  не OFFSET (OFFSET на 15 млн строк — O(N)).
  Cursor = created_at + id последнего элемента страницы.

- Статусы: enum OrderStatus (draft, confirmed, shipped, cancelled).
  Фильтр «в процессе» = draft + confirmed.
  «Доставленные» = shipped. «Отменённые» = cancelled.

- Тесты:
  - Unit: OrderRepository findByCustomer (mock EM)
  - Integration: реальная БД, 100 заказов, пагинация, фильтр
  - Feature: GET /api/orders → 200, 404 (чужой), 401 (без auth)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
OUT OF SCOPE (не в этой задаче)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- Поиск по названию товара внутри заказов
- Экспорт в PDF / CSV
- Уведомления о статусе заказа (push, email)
- Отмена / возврат из истории (отдельная задача ORD-155)
- Админ-панель (отдельная задача ADM-78)
- Мобильное приложение (frontend-задача FE-201)
- Кэширование (не нужно: < 1 мс с индексом, QPS < 50)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ДЕФИНИЦИЯ ГОТОВНОСТИ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- [ ] GET /api/orders возвращает 200 + JSON за < 100 мс (p95)
- [ ] GET /api/orders/{id} → 200 (свой), 404 (чужой), 401 (без auth)
- [ ] Фильтр по статусу работает
- [ ] Пагинация: 20 на страницу, cursor-based, last page < 20 элементов
- [ ] Индекс создан (EXPLAIN → Index Scan, не Seq Scan)
- [ ] Unit + Integration + Feature тесты зелёные
- [ ] PHPStan level 9 — 0 ошибок
- [ ] Frontend (FE-201) может начать работу по API-контракту
- [ ] Документация API: Swagger / OpenAPI обновлён

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
КРИТЕРИИ ОТКАЗА (если не укладываемся в спринт)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MVP: только список + пагинация + detail page.
Убрать: фильтры, ключевые метрики.
Оставить: 404 на чужой заказ, индекс, пагинацию.
```

---

### Что я включил и почему

| Блок | Зачем | Что будет, если не включить |
|------|-------|-----------------------------|
| **Проблема + метрика** | Исполнитель знает, **зачем** это. Может предложить лучшее решение, зная цель | Делает «как сказал», не думает. Может предложить кэш вместо индекса — и пройдёт |
| **Цель + ожидаемый эффект** | «−30 обращений/нед» — измеримо. Через месяц проверим: сработало или нет | «Сделали страницу заказов». Сработало? Неизвестно |
| **Допустимое поле (1–5)** | Конкретные endpoints, поля, фильтры. Не «сделай поиск заказов» | 5 интерпретаций → 5 разных реализаций. Ревью — «а я думал иначе» |
| **Ограничения и контекст** | «Агрегат уже существует, не создавай новый». «Пагинация keyset, не OFFSET» | Исполнитель тратит день на «а может, лучше...». Или делает OFFSET на 15 млн строк |
| **Out of scope** | Явно: «не ищи по товару, не делай PDF». Иначе через 3 дня: «а может, покажу товар?» | Scope creep. Оценка 5 SP превращается в 13 |
| **Definicion of Done** | Чеклист. Не «сделал» — а «p95 < 100 мс, EXPLAIN → Index Scan, 401/404». Измеримо | «Готово». А p95 = 2000 мс. А чужой заказ → 200. Никто не заметит до prod |
| **Критерии отказа** | Если не укладываемся — что режем, а что нет. Решение заранее, не в панике за день до дедлайна | «Ну давайте без фильтров». А без 404? А без индекса? Каждый раз — спор |

---

### Почему каждый технический detail включён

#### «Cursor-based, не OFFSET»

```sql
-- OFFSET (плохо на 15 млн строк):
SELECT * FROM orders
WHERE customer_id = 42
ORDER BY created_at DESC
LIMIT 20 OFFSET 400;  -- сканирует 420 строк, возвращает 20

-- Keyset (хорошо):
SELECT * FROM orders
WHERE customer_id = 42
  AND (created_at, id) < (:cursor_date, :cursor_id)
ORDER BY created_at DESC, id DESC
LIMIT 20;  -- всегда сканирует 20 строк
```

Если не указать — исполнитель сделает OFFSET (проще). На 15 млн строк и 100-й странице — 2 секунды. Пользователь жалуется снова.

#### «404, не 403, на чужой заказ»

```
GET /api/orders/999  (чужой заказ)

403 Forbidden  → «у тебя нет доступа к заказу 999»
               → раскрывает, что заказ 999 СУЩЕСТВУЕТ

404 Not Found  → «заказ не найден»
               → не раскрывает существование
```

Это security-решение. Если не указать — исполнитель сделает 403 (интуитивно «у меня нет права»). А это information disclosure.

#### «customer_id из token, не из query string»

```
ПЛОХО:  GET /api/orders?customer_id=42   ← любой может подменить
ХОРОШЕ: GET /api/orders                  ← customer_id из JWT/session
```

Если не указать — mass assignment. Пользователь меняет `customer_id` в query → видит чужие заказы.

#### «Индекс: customer_id + created_at DESC»

```sql
-- Без индекса:
Seq Scan on orders (cost=0.00..450000 rows=15000000)
  Filter: customer_id = 42

-- С индексом:
Index Scan using idx_orders_customer_date (cost=0.56..15.00 rows=42)
  Index Cond: customer_id = 42
  Order By: created_at DESC
```

15 млн строк → 42 строки. 500 мс → 0.5 мс. Если не указать — исполнитель сделает, но без индекса. p95 = 500 мс. «Работает». Через месяц — 50 млн строк → p95 = 2 с.

---

### Чего НЕ включил и почему

| Что не включил | Почему |
|----------------|--------|
| Дизайн UI / мокапы | Это frontend-задача (FE-201). Я задаю API-контракт, не кнопки |
| Конкретную реализацию пагинации (код) | Я задаю **требование** (keyset, < 100 мс p95). Как — решает исполнитель. Может быть Doctrine, может быть raw SQL |
| Выбор технологии (Vue, React) | Frontend-решение. Я даю JSON-контракт |
| SLA «99.9%» | Для внутреннего API с QPS < 50 — избыточно. p95 < 100 мс — достаточно |
| Миграцию БД (если таблица не существует) | Таблица существует. Я указал, что добавить (индекс). Миграцию — исполнитель |
| Нагрузочное тестирование | QPS < 50. Индекс решает. Нагрузка не нужна |
| Логику уведомления | Отдельная задача (уведомления о статусе). Scope creep |

---

### Типичные ошибки в формулировках задач

| Ошибка | Пример | Почему плохо |
|--------|--------|-------------|
| **Без проблемы** | «Сделать страницу заказов» | Исполнитель не знает, зачем. Не может предложить лучшее |
| **Без метрики** | «Улучшить поиск заказов» | «Улучшить» — как? В 2 раза? В 10? |
| **Без out of scope** | «Сделать историю заказов» | Через 3 дня: «а покажу товар? а PDF? а уведомления?» |
| **Без критериев** | «Сделать быстро» | «Быстро» = 500 мс? 50 мс? 5 мс? |
| **Без контекста** | «Сделать API для заказов» | Какой формат? Auth? Пагинация? Ошибки? |
| **Без DoD** | «Сделать и протестировать» | Какие тесты? Unit? Integration? Feature? |
| **Слишком детальный** | «Напиши именно этот код: `SELECT * FROM...`» | Исполнитель не думает. Копирует. Не замечает баг |
| **Слишком абстрактный** | «Сделать UX-приятный поиск» | «Приятный» = что? Кнопки? Анимации? |

---

### Итого: формула задачи

```
Задача = Проблема (зачем)
       + Метрика (как измерить успех)
       + Поле (что делать)
       + Ограничения (контекст, что уже есть)
       + Out of scope (чего НЕ делать)
       + DoD (как проверить, что готово)
       + Критерии отказа (что режем, если не укладываемся)
```

**Принцип:** исполнитель должен открыть задачу и начать писать код, **не открывая чат с автором**. Если он открыл чат с вопросом — задача была неполной.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

## Вопросы заказчику до начала работы

Принцип: каждый вопрос должен **изменить решение**, которое я приму. Если ответ «да/нет» не влияет на архитектуру, код или приоритеты — вопрос лишний. Ниже — только те, которые реально меняют план.

---

### Блок 1. Проверка предположения (самый важный)

Я начал с того, что «причина — нет страницы истории заказов». Это **предположение**. Прежде чем строить, проверю его.

| Вопрос | Почему спрашиваю | Что изменит ответ |
|--------|------------------|-------------------|
| «Из 127 обращений — сколько именно „найдите мой заказ", а сколько „платёж списан, а заказа нет" / „статус не обновился" / „сумма неверная"?» | «Не могут найти» — это симптом. Настоящая причина может быть другой | Если 50% — «сумма неверная», то страница истории **не решит** проблему. Нужно чинить расчёт. Я бы построил лишний экран и не закрыл жалобы |
| «Когда пользователь пишет „не нахожу" — он не видит заказ в ЛК, или видит, но не узнаёт (нет названия товара, нет даты)?» | «Не найти» ≠ «не видеть». Это разные дефекты | Если заказ виден, но не узнаётся — нужна **детализация** (название, дата), а не поиск. Решение другое |
| «Есть ли уже место, где заказы видны (ЛК, письмо, push)? Где именно „пропадает"?» | Чтобы понять разрыв, а не строить с нуля | Если заказы приходят в email, но в ЛК нет — это «синхронизация», не «страница» |

**Почему этот блок первый:** если я построю страницу истории, а реальная проблема — «сумма расхождается» — я потрачу спринт впустую. 30 минут на эти вопросы дешевле, чем день разработки.

---

### Блок 2. Целевое состояние

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Как выглядит идеальный сценарий: что пользователь делает, чтобы найти заказ за 30 секунд?» | Чтобы спроектировать под цель, а не под «страницу» | Если цель — «найти по номеру из письма» — нужен **поиск по номеру**, а не список. Разные архитектуры |
| «Какой заказ ищут чаще всего: самый свежий или старый (через месяц-полгода)?» | Определяет, нужна ли пагинация на годы или достаточно «последние 20» | Если «через полгода» — нужна пагинация + фильтры по периоду. Если «свежий» — достаточно списка |
| «Пользователь ищет **один конкретный** заказ или **просматривает** историю?» | Точечный поиск ≠ ленточный обзор | Точечный — поиск по номеру/товару. Обзорный — лента с пагинацией. UI и API разные |

---

### Блок 3. Данные и объёмы (влияет на архитектуру)

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Сколько заказов на одного пользователя в среднем и максимум?» | Определяет, нужна ли пагинация и её тип | Если максимум 5 — пагинация не нужна. Если 500 — обязателен keyset |
| «Сколько заказов в таблице всего? Растёт как?""" | Определяет, нужен ли индекс, keyset vs OFFSET | 15 млн → keyset + индекс. 150 тыс. → OFFSET пройдёт, но лучше keyset |
| «Как долго храним историю? Есть ли политика хранения (GDPR, архив)?» | Определяет объём и нужна ли архивация | Если «2 года, потом архив» — нужен отдельный store / партиционирование |
| «Старые заказы (отменённые, просроченные) — удаляем или храним?» | Влияет на объём и приватность | Если удаляем — фильтр «все» врёт. Если храним — privacy-риск |

**Почему важно:** в предыдущей задаче я заложил «15 млн строк, keyset, индекс». Эту цифру я **узнаю от заказчика**. Если на самом деле 150 тыс. — я перерешаю архитектуру (OFFSET допустим, кэш можно добавить).

---

### Блок 4. Приватность и права (безопасность)

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Кто видит чужой заказ? Только владелец? А менеджер поддержки? А админ?» | Определяет модель доступа (voter'ы, роли) | Если «поддержка видит все» — нужен admin-срез + voter. Если «только владелец» — 404 на чужой |
| «Можем ли показывать email, телефон, адрес доставки в истории?» | ПII в логах/БД | Если нет — маскация + отдельная политика. Влияет на схему и сериализацию |
| «Можно ли отменить/вернуть заказ из истории? Кем?» | Определяет, нужна ли кнопка действий | Если да — это не «read-only история», а «управление» — другая задача (я её вынес в out of scope) |

---

### Блок 5. Приоритет и объём (MVP)

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Что важнее: список всех, или быстрый переход к конкретному заказу?» | Определяет MVP | Если «к конкретному» — MVP = поиск по номеру. Список — v2 |
| «Готовы ли вы live'нуть без фильтров и экспорта, если это даст эффект уже на следующей неделе?» | Определяет scope sprint-а | Если да — режу фильтры, делаю список+detail. Быстрее к метрике |
| «Есть ли жёсткий дедлайн (кампания, релиз)? Или «когда получится»?» | Определяет trade-off | Жёсткий дедлайн → MVP. Нет → полная версия |

---

### Блок 6. Интеграции и существующее

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Есть ли уже API для заказов, на котором висит мобильное приложение / админка?» | Чтобы не сломать контракт | Если есть — меня **обратно-совместимо**, не переименовываю поля. Влияет на риск |
| «Какие статусы заказа существуют сегодня? Кто их меняет?» | Чтобы использовать реальные enum'ы, не выдумать | Если статусов 8, а не 4 — фильтр «в процессе» = 5 значений. Я бы заложил 4 и сломался |
| «Откуда берётся сумма заказа? Есть ли discount, delivery, tax как отдельные поля?» | Чтобы показать корректный breakdown | Если сумма = net+tax+discount — detail page другая |

---

### Блок 7. Успех и измерение

| Вопрос | Почему | Что изменит ответ |
|--------|--------|-------------------|
| «Как поймём, что задача решена? Какую метрику смотрим?» | Чтобы заложили instrumentation | Если «падение обращений в поддержку» — нужно логировать, что пользователь **нашёл** заказ (аналитика). Без этого не измерю эффект |
| «Какой SLA на время ответа API допустим?» | Определяет, нужен ли кэш, keyset | «< 100 мс p95» → индекс+keyset. «< 1 с» → проще |
| «Ожидаемый рост трафика на этот экран?» | Определяет, нужен ли кэш | QPS < 50 → без кэша. QPS > 500 → кэш |

---

### Чего я НЕ спрашиваю (и почему)

| Не спрашиваю | Почему |
|--------------|--------|
| «На каком фреймворке сделать?» | Это моя зона. Заказчик говорит **что**, не **как** |
| «Какой язык/БД использовать?» | Уже есть стек (PHP/Symfony/PostgreSQL). Не выбираю заново |
| «Какой UI хотите?» | Это FE-задача. Я задаю API-контракт, не кнопки |
| «Какой цвет кнопки?» | Деталь. Не влияет на решение |
| «А давайте ещё и X, Y, Z?» | Scope creep. Я защищаю границы. Если важно — отдельная задача |
| «Сколько это будет стоить?» | Не в моей зоне на этом этапе (оценка = story points, не деньги) |

**Принцип:** заказчик отвечает на **бизнес-вопросы** (что, зачем, для кого, какие правила). Я отвечаю на **технические** (как, на чём, как хранить). Если я спрашиваю «на чём» — я перекладываю на него свою работу.

---

### Как я задаю (тон)

Не «опросник из 20 вопросов». А 5–7 ключевых в живом разговоре, с обоснованием:

```
— Прежде чем я начну: я предположил, что проблема в отсутствии
  страницы истории. Проверьте, пожалуйста: из 127 обращений
  сколько реально „не вижу заказ", а сколько „сумма неверна"?
  От этого зависит, что я строю.

— И ещё: сколько заказов у тяжёлого пользователя? И как долго
  храним историю? Это определяет, нужна ли пагинация и индекс.

— И главное: как мы поймём, что сработало? Метрика?
  Потому что я залогу её в instrumentation, а не буду
  гадать после релиза.
```

Каждый вопрос подкреплён «от этого зависит X». Заказчик видит, что вопрос не «для галочки», а реально меняет решение. Это повышает качество ответов.

---

### Итого

```
Перед задачей — 7 блоков вопросов:
  1. Проверка предположения   ← не строить лишнее
  2. Целевое состояние        ← проектировать под цель
  3. Данные и объёмы          ← архитектура (пагинация, индекс)
  4. Приватность и права      ← безопасность, модель доступа
  5. Приоритет и объём        ← MVP vs полная версия
  6. Интеграции               ← не сломать существующее
  7. Успех и измерение        ← залогу метрику

Критерий вопроса: «изменит ли ответ моё решение?»
  Да → задаю.
  Нет → не задаю (шум).

Принцип: заказчик говорит ЧТО и ЗАЧЕМ.
         Я говорю КАК.
```

> ⏱ 38.4s · prompt 112822 tok · output 3617 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Разбираю два сценария во время выполнения задачи. Нужно ответить как PHP-бэкенд на собеседовании — конкретно, с действиями, не абстрактно.

Сценарий а: нашёл попутный баг, не связанный с задачей.
Ключевой конфликт: желание «попутно починить» против дисциплины scope. Плохое решение — залатать молча (скрытая зависимость, ревью не поймёт, hotfix-риск). Хорошее — зафиксировать, сообщить, предложить, но не чинить в этом PR. Почему: 1 PR = 1 фича (уже говорил про squash/revert), ревьюер проверяет цель PR, «попутка» раздувает diff и риск. Но и проигнорировать — нельзя, баг в проде. Золотая середина: отложить в трекер, сообщить стейкхолдеру, если P0/P1 — эскалировать. Если баг тривиальный и прямо на пути (например, чтобы протестировать свою фичу я обязан его обойти) — отдельный fix-коммит, явно помеченный, с согласием ревьюера.

Сценарий б: критерий приёмки противоречит цели.
Это конфликт спецификации. Плохое — делать как написано в критериях, но молча, зная что это ломает цель (тихий саботаж цели). Плохое второе — игнорировать критерий и делать «по своей голове» без согласования. Хорошее: остановить, задокументировать противоречие (критерий X vs цель Y), показать конкретный пример где они конфликтуют, предложить вариант, эскалировать на уровне, который решает (техлид/продакт). Зафиксировать решение в задаче (поправить DoD), чтобы было на бумаге. Почему: критерии приёмки — это контракт с заказчиком. Если я молча нарушаю — принимающий скажет «не по критериям». Если молча слепо делаю — ломаю цель, которую сам же описывал. Нужно явное решение и фиксация.

Структурирую: для каждого сценария — что делаю (конкретные шаги), почему (принцип), антипаттерн (чего не делаю). Добавлю пример из кода для наглядности, и «как формулирую сообщение». Держу в контексте собеседования: это проверка зрелости, управления границами, коммуникации.

Проверю: не уходить в воду, каждый блок короткий, с конкретикой. Использую маркеры [blocking]/[question] которые вводил ранее — консистентность.

Финал — общий принцип: «не молчи и не решай за других». Это суть обоих случаев.

</details>

**Кандидат:**

## Два случая, одно правило

Оба случая сводятся к одному: **я обнаружил расхождение между тем, что делаю, и тем, что согласовано**. Правильное действие — не «молча сделать по-своему» и не «молча сделать как написано», а **зафиксировать и согласовать до того, как это станет чужим сюрпризом**.

---

### а) Попутный баг, не связанный с задачей

**Ситуация (конкретно):**
Делаю ORD-142 (история заказов). При написании тестов вижу, что `Order::totalCents()` считает `qty * price` без `discount`, хотя в БД поле `discount_cents` есть и в 30% заказов оно > 0. То есть суммы в истории будут неверными. Баг не в моей задаче — у меня в DoD «показать сумму из `total_cents`», и я делаю ровно это.

**Что делаю:**

1. **Не чиню молча в этом PR.** Мой PR — «история заказов». Внезапная правка расчёта суммы — чужая логика, чужие тесты, чужой риск. Ревьюер, глядя на diff, не поймёт, зачем там меняется расчёт. А если в проде что-то сломается — `git revert` откатит и мою фичу, и чужой расчёт.

2. **Фиксирую в трекере.** Создаю задачу `ORD-148: totalCents() не учитывает discount_cents`. Пишу: как нашёл, на каких данных воспроизводится (30% заказов), как влияет (сумма завышена), воспроизводимость.

3. **Сообщаю стейкхолдеру сразу**, не «в конце спринта»:
   ```
   [вопрос/риск] По ходу ORD-142 нашёл баг: totalCents()
   игнорирует discount_cents. В истории заказов суммы будут
   завышены на сумму скидки (~30% заказов).
   Зафичил ORD-148. Не включил в текущий PR, т.к. это
   отдельная логика. Предлагаю: приоритизировать до
   релиза истории, иначе пользователь увидит неверные суммы.
   ```

4. **Если баг P0/P1** (сумма неверная = деньги = деньги пользователей) — эскалирую выше, не жду спринта-планирования. «История заказов» не должна выйти с неверными суммами — это уже не моя фича, а риск релиза.

5. **Если баг стоит прямо на пути** (без фикса не могу протестировать свою фичу, т.к. тесты падают) — спрашиваю ревьюера: «могу ли добавить один fix-коммит, явно помеченный, чтобы протестировать?» Если да — добавляю как отдельный commit `fix(order): include discount_cents in totalCents()`, с тикетом ORD-148, и говорю в PR: «включил попутный фикс, чтобы тесты прошли; можно выделить в отдельный PR».

**Почему так:**

| Принцип | Применение |
|---------|-----------|
| 1 PR = 1 логическая единица | «Попутка» ломает revert и ревью |
| Не молчать, не решать за других | Баг в проде — не моя зона тишины, но и не моя зона молчаливого фикса |
| П0 — эскалация, не «фичу потом» | Деньги пользователей > план спринта |
| Фиксация на бумаге | Через 2 месяца никто не вспомнит. Тикет + комментарий — вспомнят |

**Антипаттерн:** «Пока я тут, поправлю и это — мелочь». Через месяц: `revert` истории заказов тащит с собой чужой расчёт; ревьюер не видел; тесты падают; баг в проде.

---

### б) Критерий приёмки противоречит цели задачи

**Ситуация (конкретно):**
В задаче ORD-142: цель — «пользователь находит заказ за 30 секунд, метрика — падение обращений в поддержку». В DoD пункт: «GET /api/orders возвращает 200 + JSON за < 100 мс (p95)» И «сортировка: по дате asc (старые сверху)».

Противоречие: цель — «найти свежий/конкретный заказ». Если старое сверху — пользователь ищет свежий внизу, пролистывает 20 страниц. Я заложил в код `desc` (новые сверху) — и это **правильно для цели**. Но в DoD написано `asc`. Если я сделаю `asc` — критерий пройден, цель провалена. Если сделаю `desc` — цель достигнута, критерий провален.

**Что делаю:**

1. **Останавливаюсь.** Не пишу «ну сделаю как в DoD», не пишу «ну сделаю как правильно». Стоп.

2. **Формулирую противоречие конкретно**, не «там ошибка». Показываю, где именно конфликт и что он ломает:
   ```
   [вопрос] В ORD-142 противоречие между целью и DoD:

   Цель:    «найти заказ за 30 с», метрика — «нашёл свежий».
   DoD:     «сортировка по дате asc (старые сверху)».

   При asc пользователь ищет свежий заказ — пролистывает
   все старые. 30 секунд не уложиться. Метрика не выполнится.

   Предлагаю: desc (новые сверху). Это и есть «найти за 30 с».
   ```

3. **Предлагаю решение, не просто ставлю вопрос.** Я уже знаю, какой ответ правильный (для цели — `desc`). Я говорю это и прошу подтвердить.

4. **Решение принимает тот, у кого власть над DoD** — продакт или техлид. Не я, не ревьюер. Я показываю расхождение и предлагаю; они решают.

5. **Фиксирую решение в задаче.** Правлю DoD: `asc → desc` с комментарием «изменено по [дата], решение [имя]». Теперь у всех один источник правды. Если через месяц спросят «почему desc?» — в задаче написано, кто и почему решил.

6. **Если решение противоречит моему предложению** — делаю как решили. Не «я же говорил». Зафиксировал, отработал, забыл.

**Почему так:**

| Принцип | Применение |
|---------|-----------|
| DoD — контракт с заказчиком | Я не имею права молча нарушать или молча слепо следовать |
| Цель > буква критерия, но решают стейкхолдеры | Я предлагаю, они решают |
| Фиксация решения | Через 2 месяца «почему desc?» — ответ в задаче |
| Не саботировать цель молча | «Сделаю asc как в DoD» при известном конфликте = тихое проваление цели |

**Антипаттерн A:** Сделаю `desc`, зная что в DoD `asc`. Приёмка: «не по критериям». Я: «ну по цели». Стороны ругаются. Ни один не прав формально, оба — фактически.

**Антипаттерн B:** Делаю `asc` как в DoD, молча. Приёмка: «да, по критериям». Через месяц: 30 обращений в поддержку не упали. Вопрос: «почему?» Я: «ну в DoD было asc». Никому не нравится.

**Хороший путь:** Стоп → показать конфликт → предложить → решить → зафиксировать → делать. 15 минут. Никто не удивлён.

---

### Общий принцип

```
Обнаружил расхождение (scope, спецификация, качество):

  1. Остановись. Не продолжай «по инерции».
  2. Сформулируй конкретно: где конфликт, что ломает.
  3. Предложи решение, не просто ставь вопрос.
  4. Решает тот, у кого власть (продакт, техлид, стейкхолдер).
  5. Зафиксируй решение на бумаге (тикет, DoD, PR-комментарий).
  6. Делай как решили. Даже если не твоё.

Никогда не:
  ❌ «Пока я тут, поправлю и это» (молчаливый scope creep)
  ❌ «В DoD так написано, сделаю так» (молчаливый саботаж цели)
  ❌ «Я решил по-своему» (молчаливое игнорирование контракта)
```

Суть обоих случаев одна: **я не принимаю решений, которые не мои, и не прячу наблюдения, которые не мои**. Я их surfaces — с фактами, с предложением, с фиксацией. Остальное решают люди, у которых это в зоне ответственности.

> ⏱ 33.0s · prompt 118770 tok · output 2764 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

The user is asking about my process for reporting task completion: what goes into the report, and what changes if part of the task couldn't be completed. This is a continuation of a PHP backend developer interview simulation.

Let me think about what makes a good "task completion report" in a real team context:

1. What was done (concretely, not abstractly)
2. How it was verified (tests, metrics, manual checks)
3. What was NOT done (if applicable) and why
4. Risks / follow-ups
5. How to verify it works in prod (instrumentation, rollback plan)

The key principle here is: the report should allow someone else to verify the work WITHOUT talking to me. It should be self-contained, evidence-based, and honest about gaps.

For the "part couldn't be done" case, the key is:
- Don't hide it
- Be specific about what's missing
- Be specific about WHY (not "wasn't time" but "blocked on X, here's the workaround")
- Propose next steps
- Separate "done" from "not done" clearly

Let me structure this concretely with examples that tie back to the ORD-142 task we've been discussing throughout this conversation.

</details>

**Кандидат:**

## Отчёт о выполнении задачи

### 1. Структура: из чего состоит

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ОТЧЁТ: ORD-142 — Поиск и история заказов
Автор: [имя]
Дата: 2025-07-15
Статус: ✅ Готово (90%) / ⚠️ Частично
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. ЧТО СДЕЛАНО
2. КАК ПРОВЕРЕНО
3. ЧТО НЕ СДЕЛАНО (если есть)
4. РИСКИ И ПОБОЧНЫЕ ЭФФЕКТЫ
5. КАК ПРОВЕРИТЬ В ПРОДЕ
6. FOLLOW-UP
```

Каждый блок — не «всё ок», а **конкретика с доказательством**. Ниже — по блокам.

---

### 2. Блок «Что сделано»

Не «сделал API для заказов». А:

```
1. ЧТО СДЕЛАНО
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ GET /api/orders — список заказов текущего пользователя
   - Keyset-пагинация (cursor = created_at + id)
   - 20 на страницу, max 100
   - Фильтр по статусу: all / in_progress / delivered / cancelled
   - customer_id берётся из token, не из query
   - Чужой заказ → 404 (не 403)

✅ GET /api/orders/{id} — детализация заказа
   - Позиции: название, qty, цена, line total
   - Итого: base + discount + total
   - Если подтверждён: payment_id, payment_date

✅ Индекс: idx_orders_customer_date (customer_id, created_at DESC)
   - EXPLAIN: Index Scan, cost=0.56..15.00, rows=42
   - p95: 0.8 мс (раньше — Seq Scan, 480 мс)

✅ Авторизация: IS_AUTHENTICATED_FULLY + 404 на чужой

✅ Тесты:
   - Unit: 12 тестов (OrderRepository::findByCustomer, edge cases)
   - Integration: 7 тестов (реальная БД, 200 заказов, пагинация, фильтр)
   - Feature: 5 тестов (200, 404, 401, фильтры, пагинация)
   - Итого: 24 теста, все зелёные

✅ OpenAPI: обновлён, endpoint'ы задокументированы

Коммит: abc1234 feat(order): add order history API with keyset pagination
Файлы: 14 changed, +680 −45
```

**Почему так, а не «всё сделано»:**

| Формулировка | Проблема |
|-------------|----------|
| «Сделал API» | Что именно? Какие endpoints? Какие параметры? |
| «Добавил пагинацию» | Какую? OFFSET? Keyset? Какой размер? |
| «Тесты проходят» | Сколько? Какие уровни? Что покрывают? |
| «Быстрее стало» | На сколько? p95? До/после? |

Конкретика позволяет:
- Ревьюеру проверить **по пунктам**, не перечитывая весь код
- Через 3 месяца найти, **что именно** было сделано, не копая в коммиты
- При регрессии: «пункт 2 сломался» → сразу понятно, что искать

---

### 3. Блок «Как проверено»

Не «тесты зелёные». А:

```
2. КАК ПРОВЕРЕНО
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Автоматически:
  - vendor/bin/phpunit --testsuite Unit          → 12/12 PASS (0.3 с)
  - vendor/bin/phpunit --testsuite Integration   → 7/7 PASS (2.1 с)
  - vendor/bin/phpunit --testsuite Feature       → 5/5 PASS (1.8 с)
  - vendor/bin/phpstan analyse --level 9         → 0 errors
  - vendor/bin/php-cs-fixer fix --dry-run        → 0 violations

Вручную (staging):
  - 10 заказов у test-пользователя:
    → GET /api/orders?page=1 → 10, page=2 → 0 ✓
    → GET /api/orders?status=delivered → только shipped ✓
    → GET /api/orders/999 (чужой) → 404 ✓
    → GET /api/orders без auth → 401 ✓
  - EXPLAIN ANALYZE:
    → Index Scan using idx_orders_customer_date
    → actual time=0.12..0.85 rows=42 loops=1
    → buffers: shared hit=23 (no disk read)

Нагрузочно (staging, k6):
  - 50 VU × 60 с:
    → p50: 0.6 мс, p95: 0.9 мс, p99: 2.1 мс
    → 0 ошибок, 0 timeout'ов
```

**Почему:** «тесты зелёные» — это **один аргумент**. «24 теста + ручная проверка + EXPLAIN + нагрузочка» — это **четыре независимых подтверждения**. Если один из них врёт — остальные ловят.

---

### 4. Блок «Что не сделано»

**Ключевой блок.** Именно здесь честность отделяет «делал» от «сделал».

```
3. ЧТО НЕ СДЕЛАНО
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️ Фильтр по периоду (30/90/365 дней) — НЕ сделан
   Причина: не уложился в спринт (оценка 5 SP, факт 6.5 SP)
   Влияние: пользователь не может сузить поиск по дате.
            Список работает, пагинация работает.
   Решение: вынесено в ORD-149 (следующий спринт).
            API-контракт уже поддерживает ?period=30d —
            достаточно дописать WHERE-условие.
   Риск: при 500+ заказах у пользователя — прокрутка.
            Для MVP (38% обращений = «не вижу свежий») — ок.

⚠️ Кэширование — не сделано (осознанно)
   Причина: p95 = 0.9 мс, QPS < 50. Кэш не нужен.
   Если QPS вырастет — ORD-150 (кэш по customer_id).
   Сейчас: не блокирующий фактор.

❌ Экспорт в PDF — OUT OF SCOPE (ORD-155, следующий спринт)
❌ Уведомления о статусе — OUT OF SCOPE (NOT-23, бэклог)
```

**Правила этого блока:**

| Правило | Почему |
|---------|--------|
| Не скрывать. Даже «мелочь». | «Я не сделал фильтр по периоду» — это 30 секунд при чтении. А «а фильтр почему нет?» в prod — 3 часа отладки + репутация |
| Конкретная причина, не «не успел» | «Оценка 5, факт 6.5» — можно проверить. «Не успел» — нельзя |
| Влияние на пользователя | «Пользователь не может сузить по дате» — стейкхолдер понимает цену |
| Что уже готово для завершения | «API поддерживает ?period= — дописать WHERE» — следующий разработчик тратит 10 мин, не час |
| Риск если не сделать | «500+ заказов → прокрутка» — количественно, не «может быть медленно» |
| Отдельный тикет | ORD-149 — отслеживается. Не «а, мы это потом» |

**Антипаттерн:** «Всё сделано, тесты зелёные, merge». Через неделю: «а фильтр по периоду? — ну я не сделал, не заметил. — почему в DoD было? — ну я его не читал». Это потому что в отчёте не было блока «что не сделано».

---

### 5. Блок «Риски и побочные эффекты»

```
4. РИСКИ И ПОБОЧНЫЕ ЭФФЕКТЫ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🔶 Риск: keyset-пагинация при изменении created_at
   Если admin изменит дату заказа (backfill), cursor "сдвинется".
   Пользователь увидит дубликаты или пропуски.
   Вероятность: низкая (backfill — редкая операция).
   Митигация: не менять created_at для существующих заказов.
              Если нужно — отдельная миграция с пересчётом.

🔶 Риск: индекс на 15 млн строк — INSERT/UPDATE стал медленнее
   Измерено: INSERT orders +12% (0.3мс → 0.34мс).
   UPDATE orders SET status +8% (0.2мс → 0.22мс).
   Приёмлемо: QPS < 50, суммарный прирост < 5 мс/сек.
   Мониторинг: pg_stat_statements — track INSERT/UPDATE latency.

🔶 Риск: 404 на чужой заказ — information disclosure через timing
   GET /api/orders/1 (свой) → 0.5 мс
   GET /api/orders/999 (чужой) → 0.3 мс (ранний exit, нет JOIN)
   Разница 0.2 мс — незначительная, но теоретически измерима.
   Митигация: не требуется (QPS < 50, нет атакующего трафика).
              Если API станет public — добавить dummy delay.

🟢 Нет breaking changes в API.
🟢 Нет изменений в доменной модели.
🟢 Нет миграций (только индекс, CONCURRENTLY).
```

**Почему этот блок важен:**

| Без него | С ним |
|----------|-------|
| Через месяц: «почему INSERT медленнее?» → 2 часа отладки | «В отчёте ORD-142: +12% на индекс. Ожидалось.» |
| Через месяц: «почему пагинация глючит после backfill?» → баг в проде | «В отчёте: keyset неустойчив к изменению created_at. Не менять дату.» |
| Ревьюер спрашивает: «а timing attack?» → «не думал» | «Рассмотрено, не требуется при QPS < 50. Зафиксировано.» |

---

### 6. Блок «Как проверить в проде»

```
5. КАК ПРОВЕРИТЬ В ПРОДЕ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

После деплоя:
  1. curl -H "Authorization: Bearer <token>" \
       "https://api.example.com/api/orders?per_page=5"
     → 200, JSON, 5 заказов, p50 < 50 мс

  2. curl -H "Authorization: Bearer <token>" \
       "https://api.example.com/api/orders/999999"
     → 404

  3. curl "https://api.example.com/api/orders"
     → 401

  4. EXPLAIN ANALYZE в pgadmin:
     SELECT * FROM orders WHERE customer_id = <id>
     ORDER BY created_at DESC LIMIT 20;
     → Index Scan (не Seq Scan)

  5. Мониторинг (Grafana):
     - p95 GET /api/orders < 100 мс
     - 0 ошибок 5xx
     - pg_stat_statements: mean_exec_time < 5 мс

Rollback (если что-то не так):
  - Индекс: DROP INDEX CONCURRENTLY idx_orders_customer_date;
  - Код: git revert abc1234 → деплой
  - Время rollback: < 5 мин
  - Данные: не теряются (только чтение)
```

**Почему:** «задеплоили» ≠ «работает». Этот блок позволяет:
- DevOps'у проверить деплой по чеклисту
- Через час: «а в проде п95 < 100 мс?» — есть критерий
- При проблеме: rollback план готов, не гадать в 2 часа ночи

---

### 7. Блок «Follow-up»

```
6. FOLLOW-UP
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Созданные задачи:
  - ORD-149: Фильтр по периоду (30/90/365 дней) — 1 SP
  - ORD-150: Кэширование при QPS > 200 — 3 SP (бэклог)
  - ORD-155: Экспорт в PDF — 5 SP (бэклог)

Незакрытые вопросы:
  - [ ] Подтвердить с продактом: 404 (не 403) на чужой заказ — ok?
    (Я заложил 404. Если нужно 403 — 1 строка, но это
    information disclosure. Нужно решение.)

  - [ ] Frontend (FE-201): API-контракт зафиксирован.
    Готов к интеграции. Открыть sync?

Коммит: abc1234
PR: #487
```

---

### 8. Что меняется, если часть НЕ сделана

**Структура та же. Меняется:**

| Аспект | Всё сделано | Часть не сделана |
|--------|:-----------:|:----------------:|
| Статус | ✅ Готово | ⚠️ Частично (90%) |
| Блок «Что не сделано» | Отсутствует или «—» | **Присутствует, детальный** |
| Блок «Риски» | Стандартные | **+ риск от неготовой части** |
| Блок «Follow-up» | Опционально | **Обязателен** |
| Ревью | Стандартное | Ревьюер **фокусируется** на «что не сделано» и «почему» |
| Merge | Да (если DoD покрыт) | **Только если** не сделанное не ломает критерии. Если ломает — не merge |
| Деплой | Да | **Только если** не сделанное не влияет на prod. Если влияет — feature flag |

**Пример: ORD-142 с неготовым фильтром**

```
Статус: ⚠️ Частично (90%)

Сделано:   list, detail, auth, пагинация, индекс, тесты, OpenAPI
Не сделано: фильтр по периоду (ORD-149)

Влияние на цель: минимальное. Цель — «найти свежий заказ».
Фильтр по периоду — удобство, не критичный путь.

Влияние на DoD:
  ✅ GET /api/orders → 200, p95 < 100 мс
  ✅ GET /api/orders/{id} → 200/404/401
  ✅ Индекс (EXPLAIN → Index Scan)
  ✅ Тесты
  ✅ OpenAPI
  ❌ Фильтр по периоду (был в DoD)

Мердж: да, но с условием:
  - ORD-149 в бэклог, 1 SP, следующий спринт
  - В API-контракте: ?period= задокументирован как TODO
  - Frontend не использует ?period= в v1
```

**Ключевое отличие:** я **не прячу** неготовое. Я **квантифицирую** его влияние и **предлагаю решение** (follow-up, флаг, условие merge). Решение принимает стейкхолдер.

---

### 9. Чего НЕ будет в отчёте

| Нет | Почему |
|-----|--------|
| «Сделал быстро» | Субъективно. Не проверимо |
| «Всё хорошо» | Не конкретика. Не позволяет проверить |
| Детали реализации (какой паттерн, какой класс) | Это в коде. Отчёт — **что** и **как проверено**, не **как реализовано** |
| Извинения | «Сorry, не успел фильтр» — не конструктивно. «Фильтр не готов, причина X, влияние Y, follow-up Z» — конструктивно |
| Перекладывание вины | «Frontend не начал» — не моя зона. «API готов к интеграции, FE-201 — open» — факт |
| «Надеюсь, всё ок» | Отчёт — доказательство, не надежда |

---

### 10. Итого: принцип отчёта

```
Отчёт = доказательство, что задача выполнена
       + честность, что часть не выполнена
       + план, как закрыть оставшееся

Не:
  ❌ «Сделал» (без доказательств)
  ❌ «Не успел» (без причины и плана)
  ❌ «Всё ок» (без конкретики)

А:
  ✅ Что сделано (по пунктам, с метриками)
  ✅ Как проверено (тесты, EXPLAIN, нагрузка)
  ✅ Что не сделано (причина, влияние, follow-up)
  ✅ Риски (количественные, с митигацией)
  ✅ Как проверить в проде (чеклист)
  ✅ Как откатить (план)
```

**Критерий качества отчёта:** другой разработчик, прочитав его, может:
1. Убедиться, что задача выполнена (по метрикам)
2. Понять, что НЕ выполнено и почему (по блоку 3)
3. Проверить в проде (по блоку 5)
4. Откатить, если что-то пошло не так (по блоку 5)
5. Продолжить работу с оставшимся (по блоку 6)

— **не спрашивая меня ни одного вопроса.**

> ⏱ 44.5s · prompt 120946 tok · output 4540 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about FORCE INDEX in PostgreSQL. Let me think carefully here.

FORCE INDEX is a **MySQL** syntax, not PostgreSQL. MySQL has `FORCE INDEX`, `USE INDEX`, `IGNORE INDEX` as query hints to influence the query planner's index selection.

PostgreSQL does **not** have `FORCE INDEX` syntax. In PostgreSQL, the way to influence index selection is different:
- `SET enable_indexscan = off` (and similar for seqscan, bitmap, etc.) — these are planner cost-based toggles, not per-query hints
- `pg_hint_plan` extension — adds hint comments like `/*+ IndexScan(table index) */`
- Rewriting the query so the planner naturally picks the desired index
- Creating/dropping indexes
- `SET random_page_cost`, `SET effective_cache_size` — planner cost parameters
- In some cases, `CREATE INDEX ... INCLUDE` (covering index) makes the planner prefer it naturally

So the honest, accurate answer is: **FORCE INDEX does not exist in PostgreSQL**. It's a MySQL feature. I should not invent a PostgreSQL `FORCE INDEX` and pretend it exists. That would be a factual error.

This looks like a continuation of an interview-style thread where I've been answering as a senior PHP backend developer. The right move is to correct the premise politely and clearly, then give the actual PostgreSQL equivalents and when to use each, with trade-offs. This is genuinely the valuable answer — the question contains a false premise, and a good senior dev flags it rather than hallucinating a feature.

Let me make sure about details:
- MySQL: `SELECT ... FROM t FORCE INDEX (idx)` — yes, real.
- PostgreSQL: no `FORCE INDEX` keyword. Confirming. Correct.
- `pg_hint_plan` is a real extension providing SQL hints. Correct.
- Planner GUCs: `enable_indexscan`, `enable_seqscan`, `enable_bitmapscan`, `enable_nestloop`, `enable_hashjoin`, `enable_material`, etc. Correct.
- `random_page_cost`, `seq_page_cost`, `effective_cache_size`, `work_mem` — real. Correct.

I'll structure the answer:
1. Direct correction: FORCE INDEX is MySQL, not PostgreSQL. Don't pretend.
2. What the equivalent goals are (making the planner pick a specific index / plan).
3. The real PostgreSQL tools, ordered from least to most intrusive, with trade-offs.
4. When each is appropriate.
5. Why "forcing an index" is usually a symptom, not a fix — root causes.
6. A worked example.
7. What NOT to do.

I should keep it in Russian to match the conversation. Keep it focused and technical, consistent with the earlier answers' style (tables, concrete SQL, trade-offs, when-to-use). Don't be overly long; be complete but tight.

I want to be accurate and not pad. Let me write it.

</details>

**Кандидат:**

## Сначала важная поправка

**`FORCE INDEX` в PostgreSQL не существует.** Это синтаксис **MySQL** (`SELECT ... FROM t FORCE INDEX (idx)`). В PostgreSQL такого ключевого слова нет, и попытки написать `FORCE INDEX` дадут синтаксическую ошибку.

Если кто-то (в доке, в чужом коде, в вопросе) говорит «FORCE INDEX в PostgreSQL» — это либо путаница с MySQL, либо речь о **`pg_hint_plan`** (extension, который добавляет hint-комментарии) или о **planner GUC** (выключатели `enable_*`). Разберу, что реально есть и когда применять.

---

### 1. Зачем вообще «заставлять» индекс

Задача одна: **сделать так, чтобы планировщик выбрал конкретный план/индекс**. Но в PostgreSQL это почти всегда **симптом**, а не решение. Потому что планировщик выбирает план на основе **стоимости** (cost). Если он «неправильно» выбрал — значит, стоимость считает не так, как вам хочется, и почти всегда причина в одном из:

- Нет нужного индекса (или он устарел)
- Статистика устарела (`ANALYZE` не запускался)
- Блёрт (bloat) — планировщик неверно оценивает объём
- Неправильные cost-параметры (`random_page_cost` для SSD)
- Запрос написан так, что «правильный» план недостижим (функция над колонкой, `OR`, `LIMIT` с сортировкой)

Сначала это. «Заставлять индекс» — последний шаг, когда всё выше исключено.

---

### 2. Реальные инструменты PostgreSQL (от мягких к жёстким)

| Инструмент | Что делает | Область | Временяность |
|------------|-----------|---------|-------------|
| `CREATE INDEX` | Даёт планировщику **вариант** | Вечная | Да (пока не drop) |
| `ANALYZE` | Обновляет статистику | Вечная | До изменений данных |
| Covering index (`INCLUDE`) | Делает индекс привлекательным | Вечная | Да |
| Переписать запрос | Сделать «правильный» план естественным | Вечная | Да |
| Planner GUC (`enable_*`) | Выключает класс планов | Session/DB/global | До RESET |
| Cost GUC (`random_page_cost`) | Меняет расчёт стоимости | Session/DB/global | До RESET |
| `pg_hint_plan` | Коммент-хинт в запросе | Per-query | До удаления коммента |
| Материализованный view / read model | Убирает запрос из «зоны» | Вечная | Да |

---

### 3. По каждому — и trade-offs

#### 3.1. Сначала: индекс + статистика (90% случаев)

```sql
-- Нет индекса → Seq Scan
EXPLAIN ANALYZE SELECT * FROM orders WHERE customer_id = 42 AND created_at > '2025-01-01';
-- Seq Scan on orders (cost=... rows=15000000)

-- Решение
CREATE INDEX CONCURRENTLY idx_orders_cust_date
    ON orders (customer_id, created_at DESC);
ANALYZE orders;

-- После: Index Scan (cost=0.56..25 rows=150)
```

**Почему это «правильно», а не hint:** вы даёте планировщику **лучший вариант**, а не запрещаете ему думать. Если через полгода данных станет больше и Seq Scan снова станет дешевле — планировщик сам переключится. Hint этого не умеет.

**Covering index** (избегает обращения к таблице):

```sql
CREATE INDEX idx_orders_cust_date_cover
    ON orders (customer_id, created_at DESC)
    INCLUDE (status, total_cents);
```

Теперь `SELECT status, total_cents FROM orders WHERE customer_id=...` — **Index Only Scan**, без heap access. Планировщик выбирает его **сам**, потому что он дешевле. Никакого hint.

#### 3.2. Planner GUC — «выключить класс планов»

```sql
-- Запретить Seq Scan (планировщик не будет его даже рассматривать)
SET enable_seqscan = off;

-- Запретить Hash Join
SET enable_hashjoin = off;
```

**Как работает:** это **не** «запрет». Это **огромный штраф** к стоимости плана. Если Seq Scan — единственный вариант — он всё равно будет выбран (штраф конечный). Это «мягкое» указание.

**Где применяется:**
- **Диагностика**: «а что будет, если не Seq Scan?» → `SET enable_seqscan=off; EXPLAIN ...` → видите альтернативу.
- **Сессия-настройка** для конкретного типа запросов (например, в отчёте).

**Почему плохо в production:**
- Глобально: влияет на **все** запросы, включая те, где Seq Scan — оптимально (малые таблицы, full scan по природе).
- Сессийно: нужно помнить, где включено. Лёгкий способ «забыть» и оставить в проде.
- `enable_*` — это **обход**, а не решение. Правильное — индекс + статистика.

```sql
-- Как применять безопасно (только в сессии, с возвратом):
SET LOCAL enable_seqscan = off;  -- в транзакции, авто-reset при COMMIT
EXPLAIN ANALYZE SELECT ...;
RESET enable_seqscan;
```

#### 3.3. `pg_hint_plan` — «FORCE INDEX» в PostgreSQL

Extension, добавляющий hint-комментарии (синтаксис из мира Oracle/MySQL):

```sql
/*+ IndexScan(orders idx_orders_cust_date) NestLoop(o i) */
SELECT o.*
FROM orders o
JOIN order_items i ON i.order_id = o.id
WHERE o.customer_id = 42;
```

**Доступные хинты (примеры):**
```
IndexScan(table index)
IndexOnlyScan(table index)
SeqScan(table)
NestLoop(a b)
HashJoin(a b)
WorkMem(64MB)
Set(cost_param value)
```

**Trade-offs:**

| Аспект | Оценка |
|--------|--------|
| Точность | Высокая: именно этот индекс/план |
| Переносимость | **Низкая**: extension-specific. На MySQL/SQLite/Redshift не работает |
| Обслуживание | Хинт в коде запроса. Если индекс переименуем — хинт ломается **тихо** (игнорируется) |
| Отладка | Сложнее: «почему этот план?» → надо искать комментарий |
| Безопасность | Хинт может быть «протухшим» (индекс удалили → хинт игнорится → план хуже, чем без хинта) |

**Когда уместно:**
- Легаси-запрос, который переписать нельзя (ORM генерирует)
- Критичный отчёт, где «план может измениться» — фиксируем
- Один-два запроса, где hint дешевле, чем переписывать

**Когда нет:**
- В новом коде (сначала индекс + статистика + переписать запрос)
- Если хинтов много — это признак того, что проблема в данных/индексах, а не в планировщике

#### 3.4. Cost GUC — для SSD

```sql
-- По умолчанию random_page_cost = 4.0 (дисковый)
-- Для SSD:
ALTER DATABASE mydb SET random_page_cost = 1.1;
-- Для NVMe / in-memory:
ALTER DATABASE mydb SET random_page_cost = 1.0;
```

**Почему:** по умолчанию PostgreSQL считает, что случайное чтение в 4 раза дороже последовательного (механический диск). На SSD это неверно — разница мала. Из-за этого планировщик **ошибочно** предпочитает Seq Scan индексу. Понижение `random_page_cost` → планировщик чаще выбирает Index Scan.

**Это не hint, а «правильный расчёт».** В отличие от `enable_seqscan=off`, здесь нет запрета — просто честнее стоимость.

**Когда:** SSD/NVMe, много Index Scan вместо ожидаемых. Проверить: `SELECT * FROM pg_settings WHERE name='random_page_cost';`

---

### 4. Когда какой инструмент

| Ситуация | Инструмент |
|----------|-----------|
| Нет индекса | `CREATE INDEX` |
| Есть индекс, но не выбирается, статистика старая | `ANALYZE` |
| SSD, Seq Scan вместо Index Scan | `random_page_cost = 1.1` |
| Нужно избежать heap access | Covering index (`INCLUDE`) |
| Запрос с функцией над колонкой | Переписать / expression index |
| ORM генерирует «неправильный» запрос, переписать нельзя | `pg_hint_plan` |
| Диагностика: «что если без Seq Scan?» | `SET enable_seqscan=off` (сессия) |
| Критичный отчёт, план не должен меняться | `pg_hint_plan` + мониторинг |
| Частый read-heavy с одним запросом | Материализованный view |

---

### 5. Рабочий пример: «FORCE INDEX»-подобная ситуация

**Жалоба:** отчёт «заказы за месяц» тормозит.

```sql
EXPLAIN ANALYZE
SELECT o.*, i.*
FROM orders o
JOIN order_items i ON i.order_id = o.id
WHERE o.created_at > NOW() - INTERVAL '30 days';
```

```
Hash Join (cost=... rows=2000000)
  → Seq Scan on orders (cost=0.00..450000 rows=15000000)   ← Seq Scan
  → Seq Scan on order_items (cost=0.00..95000 rows=5000000)
```

**Шаг 1. Не hint. Индекс.**

```sql
CREATE INDEX CONCURRENTLY idx_orders_created ON orders (created_at DESC);
ANALYZE orders;
```

```
Index Scan Backward using idx_orders_created on orders
  (cost=0.56..8500 rows=500000)   ← Index Scan, стало лучше
```

**Шаг 2. Если всё ещё Seq Scan (статистика врала / bloat):**

```sql
-- Проверяем
SELECT n_live_tup, n_dead_tup, last_analyze FROM pg_stat_user_tables WHERE relname='orders';
-- last_analyze = 2 месяца назад, dead_ratio = 40%

VACUUM (ANALYZE) orders;
```

**Шаг 3. Если SSD и планировщик всё равно Seq Scan:**

```sql
ALTER DATABASE mydb SET random_page_cost = 1.1;
```

**Шаг 4. Если запрос генерирует ORM и переписать нельзя:**

```sql
/*+ IndexScan(o idx_orders_created) */
SELECT o.*, i.*
FROM orders o
JOIN order_items i ON i.order_id = o.id
WHERE o.created_at > NOW() - INTERVAL '30 days';
```

**Шаг 5. Если отчёт hot и данные не меняются часто:**

```sql
CREATE MATERIALIZED VIEW orders_last_month AS
SELECT o.*, i.* FROM orders o JOIN order_items i ...
WHERE o.created_at > NOW() - INTERVAL '30 days';
CREATE UNIQUE INDEX ON orders_last_month (o.id, i.id);
-- REFRESH MATERIALIZED VIEW CONCURRENTLY — в cron
```

**Ключевое:** hint (шаг 4) — это **последний** шаг. Первые три — «правильные» решения. Если пришлось дойти до hint'а — зафиксировать, **почему** (в комментарии) и **когда** его можно убрать.

---

### 6. Чего не делать

| Антипаттерн | Почему |
|-------------|--------|
| `SET enable_seqscan=off` глобально | Ломает все запросы, где Seq Scan оптимален |
| 50 хинтов в 50 запросах | Признак проблемы с индексами/данными, не с планировщиком |
| Hint на несуществующий индекс | Тихо игнорируется. План хуже, чем без hint. Невидимый баг |
| «FORCE INDEX» в доке как PostgreSQL-фича | Это MySQL. Путаница → сломанный код на переносе |
| Убирать «плохой» индекс, потому что «INSERT медленный» | Сравнивать: 100 мс на INSERT vs 2000 мс на SELECT. SELECT важнее |
| Hint'ить «чтобы было быстрее», не проверив `EXPLAIN` | Может быть медленнее. Сначала `EXPLAIN ANALYZE`, потом решение |

---

### 7. Итого

```
FORCE INDEX — это MySQL. В PostgreSQL его нет.

Цель (выбрать конкретный план) достигается:
  1. CREATE INDEX / Covering index     ← основное решение
  2. ANALYZE / VACUUM                  ← статистика, bloat
  3. random_page_cost (для SSD)        ← честный расчёт стоимости
  4. Переписать запрос                 ← естественный план
  5. SET enable_* (сессия)            ← диагностика, не production
  6. pg_hint_plan                     ← когда переписать нельзя
  7. Materialized view                ← hot read-only запрос

Порядок применения: 1 → 2 → 3 → 4 → (5/6 только по необходимости)

Правило: hint — это фиксация, не решение.
Если хинтов больше 3 в проекте — проблема в данных/индексах,
а не в планировщике.
```

Если вопрос был задан с предположением, что `FORCE INDEX` — это PostgreSQL-фича, — я бы на собеседовании именно так и ответил: «в PostgreSQL такого нет, есть `pg_hint_plan` и planner GUC, вот как они работают и когда их применяю». Это показывает, что я не путаю СУБД и знаю, чем заменить «не-existing» фичу.

> ⏱ 42.7s · prompt 125249 tok · output 3929 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Рассмотрим вопрос внимательно. Пользователь спрашивает: «на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?». Это вопрос с ложной посылкой — он предполагает, что strict_types замедляет код на некий измеримый процент. Нужно честно и точно ответить, что это не так, и объяснить почему.

Сначала вспомню, как работает strict_types в PHP. Это декларация в начале файла (declare(strict_types=1)), которая влияет на типизацию при вызове функций/методов из ЭТОГО файла. Ключевой момент: strict_types — это свойство вызывающего файла, а не целевой функции. При включённом strict_types PHP требует точного совпадения типов (без coercion) при передаче аргументов и возврате значений.

Теперь главный технический факт: проверки типов в PHP происходят и в coercive, и в strict режиме. Разница не в наличии/отсутствии проверки, а в том, что в strict режиме PHP НЕ пытается приводить тип — он сразу бросает TypeError, если тип не совпадает. В coercive режиме PHP сначала пробует привести (это и есть потенциальное замедление — попытка приведения), и если не удалось — TypeError.

То есть парадокс: strict_types в отдельных микро-ситуациях может быть ЧУТЬ быстрее, потому что он не тратит время на попытки приведения (coercion) — он сразу проверяет и бросает. Но в успешном случае (когда типы совпадают) оба режима делают одну и ту же работу — проверку совпадения типов. Приведение (coercion) в PHP 8 происходит очень редко в «горячих» путях, когда типы совпадают.

Реальное влияние на производительность: практически нулевое. Проверка типов — это один условный оператор (сравнение типа). Это наносное. Замедление, если и есть, находится в пределах шума бенчмарка (доли процента, и то только на миллионах вызовов с mismatched types, где в strict сразу ошибка, а в coercive — попытка приведения).

Важно: в нормальном коде, где типы совпадают (что и должно быть при хорошем типизированном коде), strict_types не делает НИЧЕГО лишнего. Проверка «тип совпадает?» выполняется в обоих режимах одинаково. Разница проявляется ТОЛЬКО при рассогласовании типов: strict → немедленный TypeError; coercive → попытка приведения (и успех, если возможно, либо ошибка).

Итак, честный ответ: 0% в типичном случае. Вопрос построен на ложной посылке. Нужно это прямо сказать, объяснить механизм, и показать, где возникает иллюзия замедления (и почему это на самом деле не про strict_types, а про попытки приведения, которых в строго типизированном коде и нет).

Дам развёрнутый технический ответ с механизмом, примером, и объясню, почему в «реальной» нагрузке разница не измеряема. Добавлю нюанс про то, что при массе mismatched types strict может быть даже быстрее (не тратит время на futile coercion). И упомяну, что реальные источники замедления — другие (coercion в hot path, autoloading, reflection, т.д.).

Структурирую: прямой ответ (нулевое, вопрос с ложной посылкой), механизм (как проверяются типы в обоих режимах), где возникает иллюзия, что измеримо, и что реально влияет на скорость. Тон — как на собеседовании, технически точно, без воды.

</details>

**Кандидат:**

## Коротко: ноль. И вопрос содержит ложную посылку

В типичном правильно типизированном коде `strict_types=1` **не замедляет код ни на сколько-нибудь измеряемый процент**. Если вы видите измеримую разницу — причина почти наверняка не в `strict_types`, а в том, что в «количественных» бенчмарках сравнивают разные вещи. Ниже — почему.

---

### 1. Что реально делает `strict_types`

Ключевой факт, который убирает большую часть иллюзии: **проверка типа аргумента/возвратного значения выполняется в обоих режимах одинаково.** Разница между `strict` и `coercive` — не в наличии проверки, а в **поведении при рассогласовании типов**:

| Тип совпал | Тип не совпал |
|------------|---------------|
| **strict:** проверка совпадения → успех | **strict:** проверка совпадения → **сразу** `TypeError` |
| **coercive:** проверка совпадения → успех | **coercive:** проверка → **попытка приведения (coercion)** → успех или `TypeError` |

То есть в **успешном** случае — а это 100% вызовов в правильно типизированном коде — оба режима делают **одну и ту же работу**: одно сравнение типа (по сути, сравнение enum'а `zend_type`). Ни одного лишнего байта, ни одного лишнего цикла. `strict_types` не «добавляет» проверку — он **запрещает** следующий шаг (приведение), которого в успешном вызове и не происходит.

---

### 2. Где возникает иллюзия замедления

Обычно так:

```php
// бенчмарк
for ($i = 0; $i < 1_000_000; $i++) {
    add("4", "6");   // строки вместо int!
}
```

В `coercive` режиме PHP **приводит** `"4"` → `4`, `"6"` → `6`. В `strict` режиме — бросает `TypeError` на первом же вызове. Такой «бенчмарк» меряет не strict_types, а **стоимость приведения** (и сравнение вообще нечестное, потому что в strict код не выполняется).

А если в strict-файле типичный hot-path вызов передаёт **правильные** типы (а так и должно быть):

```php
add(4, 6);   // int, int — совпадает
```

— оба режима делают идентичное: сравнили тип, совпало, поехали. Разница ≈ 0.

**Вывод:** измеряемое «замедление» появляется только когда в hot path идёт **массовое рассогласование типов**, и даже тогда в strict часто **быстрее** — он не тратит время на *futile coercion* (приведение, которое всё равно провалилось бы в логике дальше).

---

### 3. Что на самом деле влияет на скорость PHP-кода

В порядке убывания влияния (в реальном коде):

| Источник | Влияние |
|----------|---------|
| Сетевые вызовы, IO, БД | доминирует (мс–сек) |
| `autoload` (первый вызов класса) | заметен при холодном створте |
| Reflection, dynamic properties, `__call` | высокий |
| Coercion в hot path (строки в числовых аргументах) | **вот это** иногда путают с strict_types |
| Копирование больших массивов / `json_encode` больших структур | заметен |
| **Проверка типа аргумента** | **наносное, шум бенчмарка** |

`strict_types` находится в нижней строке таблицы — и только в том смысле, что он **управляет** вторым пунктом из «коercion в hot path»: он делает несовместимый вызов **ошибкой**, а не **тихим приведением**.

---

### 4. Практический вывод

- **0% в нормально типизированном коде.** Проверка типа аргумента — одна условная проверка, она есть в обоих режимах.
- **Иногда даже быстрее в failure-сценариях** (не тратит время на приведение, которое всё равно не помогло бы).
- **Измеримая разница = признак того, что в hot path были mismatched types**, т.е. что коду *нужна* строгая типизация: она превращает «молчаливое приведение → потенциальный логический баг» в «немедленный `TypeError`».

---

### 5. Как я бы ответил на собеседовании, если бы это спросили

> «Замедляет на 0% в правильно типизированном коде. Проверка типа аргумента выполняется одинаково в strict и coercive режиме — `strict_types` не добавляет проверку, а запрещает шаг приведения, который в успешном вызове и не происходит. Если где-то измеряют разницу — это признак mismatched types в hot path, а значит, именно строгая типизация там и нужна: она превращает тихий coercion (и возможный логический баг) в немедленный `TypeError`. Реальные источники замедления — IO, autoload, reflection, а не проверка типов».

Это честно, технически точно, и показывает, что я понимаю *механизм*, а не заученный факт «strict_types тормозит» / «не тормозит».

> ⏱ 27.0s · prompt 128598 tok · output 2061 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос: «Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?»

Сначала проверю посылку вопроса. Утверждение «Doctrine не поддерживает составные первичные ключи» — **частично неверное / устаревшее**. Это классическая «ловушка» вопроса, как в предыдущих (FORCE INDEX в PostgreSQL, strict_types замедление). Нужно не поддакивать, а разобрать честно:

Что реально с составными ключами в Doctrine:
- `@Id` можно ставить на несколько полей — это и есть составной PK. Doctrine поддерживает составные первичные ключи на уровне маппинга (несколько `#[Id]` + `#[Column]`, или association `@Id` + `@OneToOne`/`@ManyToOne` как часть PK).
- НО: есть серьёзные ограничения/неудобства, из-за которых их «не любят» и часто обходят:
  1. Doctrine не генерирует ID для составного ключа (нет `@GeneratedValue` для составного).
  2. Сложности с `find()` — нужен массив.
  3. `merge()`, detached entities с составным PK — хрупко.
  4. Связи (ManyToOne) на составной PK — боль (need to map each column).
  5. Полиморфные ассоциации, inheritance — ограничения.
  6. Performance: UnitOfWork, hydration сложнее.
  7. `@OneToOne` с `@Id` на второй сущности — «mapped by / inversedBy» и owning side.

Так что честный ответ: «не поддерживает» — нет, поддерживает на уровне маппинга. Но «неудобен / имеет ограничения» — да, и это исторически сложилось. Разберу:
- Поддерживается ли (да, синтаксис).
- Где именно «не работает» / неудобно (список ограничений).
- Почему так исторически (дизайн UnitOfWork, сущность = объект с identity, identity в Doctrine = один объект; составной ключ нарушает «простую» модель).
- Как живут в реальных проектах: паттерны.

Паттерны обхода:
1. **Суррогатный ключ (surrogate key / UUID / BIGSERIAL)** — самый частый. Связующая таблица имеет свой `id` + составной уникальный индекс на паре. Это стандарт.
2. **Join table с surrogate key** для many-to-many.
3. **Composite key как value** — если реально нужен составной (например, `order_id + product_id` как естественный ключ позиции), иногда делают surrogate.
4. Когда составной ключ оправдан: join-таблицы, snapshot'ы, матрицы (user_id + role_id), естественные ключи без суррогата (редко).
5. Как маппить в Doctrine, если всё-таки составной: несколько `#[Id]`, `find(['a'=>..,'b'=>..])`.
6. Альтернативы: DQL с условием, native query, read model.

Структурирую ответ:
1. Поправка к посылке — поддерживает, но неудобно.
2. Где именно «не поддерживает» / ограничено.
3. Почему (архитектурная причина — UnitOfWork + identity).
4. Как живут в проде (паттерны, примеры кода).
5. Когда составной всё-таки оправдан и как его маппить.
6. Чего не делать.
7. Итого.

Стиль — как в предыдущих ответах: таблицы, код, честность к посылке вопроса, практические примеры. Язык русский.

Буду писать конкретно с примерами маппинга, чтобы показать и «как работает», и «где боль».

Не перегружать. Сфокусироваться на честности посылки + практики.

</details>

**Кандидат:**

## Сначала — поправка к посылке

«Doctrine **не поддерживает** составные первичные ключи» — **неверно**. Поддерживает: можно пометить несколько полей `#[Id]` и получить составной PK. Это работает.

Но в реальности их **избегают**, и есть для этого причины: не «не поддерживает», а **поддерживает плохо и с ограничениями**, из-за которых в 90% случаев это худший выбор. Разберу и то, и другое.

---

### 1. Что реально работает

```php
// src/Entity/UserRole.php
#[ORM\Entity]
#[ORM\Table(name: 'user_roles')]
class UserRole
{
    // Составной PK из двух полей
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    private string $role;

    public function __construct(int $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
    }
}

> ⏱ 14.7s · prompt 129913 tok · output 1159 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать код PHP-контроллера и переделать его. Это продолжение серии вопросов собеседования на роль PHP-бэкенд-разработчика. Нужно:

1. Найти все проблемы в коде (безопасность, архитектура, надёжность, читаемость)
2. Предложить переделку
3. Обосновать порядок работы (с чего начал и почему)

Давайте систематизирую проблемы по критичности.

**Критичные (безопасность):**
- SQL-инъекция: `$id` подставляется прямо в строку SQL без санитизации. `$req->get('order_id')` — пользовательский ввод. Можно `1 OR 1=1`, `1; DROP TABLE orders`. Это самая грубая и самая опасная проблема.
- Нет авторизации: любой неавторизованный пользователь может оплатить чужой заказ. Не проверяется, что заказ принадлежит текущему пользователю.
- Mass assignment / доверие к input: берётся `order_id` из запроса без валидации типа.

**Надёжность / логика:**
- `SELECT *` — лишний, и при `fetch()` без `FETCH_ASSOC` — объект. Потом `$order['email']` — некорректно.
- Нет транзакции: SELECT, UPDATE, Mailer, Logger — если Mailer упадёт, заказ уже помечен paid. Если UPDATE упадёт — заказ не оплачен, но email может уйти (нет, email после update). Но если между SELECT и UPDATE кто-то изменил — lost update, race condition.
- Race condition: два запроса pay() одновременно → двойной payment, email дважды. Нет идемпотентности.
- Проверка `!$order` после `fetch()` — если `fetch()` вернул `false`/`null` — ок, но если `FETCH_OBJECT` — `!$order` не сработает для объекта.
- Нет проверки статуса: можно «оплатить» уже оплаченный/отменённый заказ. Нет бизнес-валидации.
- `Mailer::send()` синхронно в запросе — блокирует ответ, при падении — 500 после обновления статуса. Отправка email — side effect, не должна быть в транзакции и не должна падать весь запрос.
- Статические вызовы `DB::connect()`, `Mailer::send()` — статический доступ к зависимости, не тестируемо, не инжектируется.
- `new Logger()` внутри метода — создание логгера на лету, не инжектируется.
- Ответ `'ok'` — нет структуры, нет статуса 200 явно (default), нет JSON.
- Нет обработки исключений: если БД упала — 500 с stack trace наружу.
- Кодирование: `'Ваш заказ оплачен'` — хардкод в UI, не локализован.
- `status = 'paid'` — магическая строка, не enum/константа.
- Нет проверки, что `$id` — число.

**Арх

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 9.1s · prompt 130385 tok · output 686 tok · ⚠️ обрыв: length

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

Пользователь — в контексте собеседования на PHP-бэкенд (я уже играл роль кандидата). Задал конкретный вопрос: разобрать доменную сущность `User` с точки зрения DDD и чистой архитектуры, найти проблемы, предложить исправление.

Сначала перечислю все проблемы в коде, чтобы не упустить:

1. `EntityManagerInterface` в конструкторе доменной сущности. Это самое главное нарушение. Доменная сущность (по определению — чистый PHP, без внешних зависимостей) зависит от инфраструктурного компонента (Doctrine). Это прямое нарушение:
   - Dependency Inversion: домен зависит от конкрет

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.5s · prompt 130542 tok · output 146 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос-интервью: как я подхожу к незнакомой технологии/стеку, последовательность действий от первого взгляда до оценки сроков, и когда просить помощь. Это вопрос про Soft Skills / подход к работе, а не про конкретный код.

Нужно дать практичный, структурированный ответ без воды. Я уже отвечал в этом диалоге как PHP-бэкенд разработчик, поэтому сохраню этот контекст.

Сужаю бесконечное пространство ответов:
- Можно написать абстрактно "изучу документацию, погуглю, спрошу" — это плохо, вода.
- Можно дать длинный список "10 шагов" — перебор.
- Нужно: конкретная последовательность действий с чекпоинтами, где я останавливаюсь и оцениваю; явный критерий "когда сам / когда спрашиваю"; привязка к оценке сроков (не "погуглю и сделаю", а "сколько времени я готов потратить на разведку до того, как пойму, что не справлюсь").

Ключевые блоки, которые реально ценны:
1. Сначала разобраться в задаче, а не в технологии (я часто вижу, что человек прыгает в стек, а задача решается вообще без него).
2. Оценка: знакомая ли задача по форме (даже если стек новый) — это сильно снижает риск.
3. Бюджет времени на разведку (time-box) — я не учусь бесконечно, я выделяю N часов на то, чтобы понять, решаемо ли.
4. Три типа неизвестности: (а) незнакомая библиотека, но стандартная — сам; (б) незнакомая архитектура/паттерн — сам, но с чекпоинтом; (в) фундаментально незнакомый домен или инфраструктура (production DB, payment, security) — не сам, сразу команда.
5. Как отличить "мне нужно спросить" от "я спрашиваю потому что

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 6.4s · prompt 130624 tok · output 447 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос в рамках того же собеседования PHP-бэкенда. Вопрос про коммуникацию при срыве сроков — классический вопрос на «софт-скиллы / зрелость инженера». Нужно ответить так, как отвечал бы опытный разработчик: честно, с конкретикой, без общих фраз. 

Что важно донести:
1. Сообщаю сразу, а не в последний момент (ключевой принцип)
2. Сообщаю с анализом: почему срыв, что уже сделано, сколько осталось
3. Предлагаю варианты: (а) срезать scope, (b) сдвинуть срок, (c) помочь команде
4. Не извиняюсь в лоб, не прячусь
5. Фиксирую на бумаге
6. Учитываю на будущее (оценка)

Нужно привести конкретный пример — «задача ORD-142, обещал к завтра, понял что 2 дня». Показываю реальный текст сообщения в чат.

Структурирую:
- Короткий ответ: когда и кому сообщаю
- Пример сообщения (конкретный текст)
- Что включаю в сообщение (и почему)
- Чего НЕ делаю (антипаттерны)
- Как учитываю на будущее

Язык — русский, тон — живой, практичный, без «водилки». Ответ должен читаться как реальный опыт, а не учебник.

Формат: markdown с разделами, немного кода/примеров сообщений, таблицы для антипаттернов. Не слишком длинно, но содержательно — вопрос по сути короткий, ответ должен быть плотным.

</details>

**Кандидат:**

## Сообщаю сразу, в тот момент когда понял

Не в конце дня. Не «вот-в

> ⏱ 5.6s · prompt 130675 tok · output 397 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос про баланс качества и скорости — когда код «достаточно хорош» для отдачи, а когда стоит улучшать. Это вопрос про pragmatism vs perfectionism, cost-benefit analysis качества.

Контекст: собеседование на PHP-бэкенда. Я уже выстроил линию ответов: конкретика, честность про trade-offs, практические примеры, таблицы, «правило выбора». Нужно сохранить этот стиль.

Что здесь важно, чтобы ответ был сильной, а не «воды про balance»:

1. Убрать абстракцию «баланс» и перевести в **решения**: у меня есть критерии, по которым я останавливаюсь. «Достаточно хорош» — это не чувство, а проверяемое состояние.

2. Ключевая идея, которую я хочу донести: качество — это **инвестиция с окупаемостью**. Продолжать улучшать стоит, пока стоимость улучшения < стоимость последствий «недоработанного». Как только стоимость превысила — отдаю. Это cost-benefit, а не «я устал / я хочу идеально».

3. Что конкретно определяет «достаточно»: 
   - Критичность кода (hot path / edge case / один раз)
   - Вероятность, что код изменится (живой / мёртвый)
   - Стоимость ухудшения (баг в проде vs косметика)
   - Окно времени (спринт, д

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.7s · prompt 130756 tok · output 314 tok · ⚠️ обрыв: length

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

<details><summary>Ход мысли модели</summary>

The user is asking me to give feedback on the interview itself. This is a meta-question — I'm being asked to evaluate the interview process, the quality of questions, and suggest improvements.

Let me think about what actually happened in this interview:

1. We went through a long sequence of questions covering: PHP 8 features, type system, exceptions, SOLID, DI container (Symfony), circular dependencies, async side effects, security (auth/roles/voters), Value Objects in Doctrine, aggregates, concurrent modification, domain events, VO vs Entity, ubiquitous language, clean architecture, testing strategy, race conditions, git process, code review, task formulation, asking questions to stakeholders, completion reports, "FORCE INDEX in PostgreSQL" (a trick question), strict_types performance (another trick question), and a code review exercise.

Let me think about what was good, what was weak, and what I'd change.

**What was good:**
- Progressive depth — started with "introduce yourself" and went to architecture, concurrency, DDD
- The questions followed a logical arc: PHP → Symfony → DDD

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.4s · prompt 130842 tok · output 228 tok · ⚠️ обрыв: length

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

<details><summary>Ход мысли модели</summary>

Пользователь просит меня рассказать о себе как о кандидате — сильные стороны, подход, принципы. Это финальный вопрос собеседования. Нужно ответить честно, конкретно, без штампов. Но есть важный момент: я — LLM, и во всём собеседовании я отвечал как опытный PHP-разработчик. Сейчас меня просят раскрыть "себя как кандидата".

Мне нужно решить: в какой маске отвечать? Я играл роль PHP-бэкенда-разработчика на протяжении всего диалога. Разрушать четвертую стену резко и сказать "я на самом деле нейросеть" было бы неловко и, возможно, не то, что хочет услышать собеседник — он задал вопрос в рамках контекста найма

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.8s · prompt 130908 tok · output 162 tok · ⚠️ обрыв: length

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт мне последний вопрос собеседования: какой стек мне ближе всего, и что бы я выбрал для нового проекта, защищая этот выбор перед командой. Это вопрос о мнении, предпочтениях и аргументации — не «правильный ответ», а способность мыслить и обосновывать.

Важный контекст: весь разговор шёл в позиции «я — PHP-бэкенд-разработчик». Я отвечал как кандидат с опытом PHP/Sym

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.1s · prompt 130965 tok · output 105 tok · ⚠️ обрыв: length

---
