# Собес: `qwen3.8:latest` · роль: backend — run-03

- Дата: 2026-10-06T17:52:58.871233+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: auto · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to introduce myself and describe my experience in PHP development and related technologies. They want me to rate my knowledge on a 0-10 scale for: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, Clean Architecture, Testing, Git — with a one-sentence explanation for each rating.

Let me think about this carefully. I'm an AI language model. I don't have personal "experience" in the traditional sense. I was trained on a vast corpus of text that includes PHP documentation, Symfony guides, Doctrine ORM docs, PostgreSQL documentation, DDD literature, Clean Architecture principles, testing frameworks documentation, and Git documentation. I should be honest that I'm an AI and frame my "experience" as training knowledge rather than practical work experience.

Let me think about honest ratings:

- PHP 8: I have extensive knowledge of PHP 8 features (readonly properties, enums, fiber, constructor promotion, new match expression, etc.), OOP, type system, etc. I can explain most features well. I'd say 9/10. I might miss some very niche edge cases or extremely new 8.4 features depending on training cutoff.

- Symfony: I know the framework well - DI container, events, form components, security, routing, serializer, messenger, etc. Very well documented, lots of examples in training data. 9/10.

- Doctrine: ORM, DBAL, QueryBuilder, DQL, migrations, entities, associations, etc. Well-documented. 8-9/10. Maybe 8 because some advanced ORM pitfalls and performance tuning nuances might be less covered.

- PostgreSQL: I know the SQL, indexing, JSONB, CTEs, window functions, connection pooling, replication concepts. 8/10. I don't have practical DBA experience but know the concepts well.

- DDD: I understand the concepts - aggregates, value objects, entities, domain events, repositories, bounded contexts, strategic patterns. Theoretical understanding is strong, but practical application nuances and debates around DDD implementation are less "lived." 7-8/10. Let's say 8.

- Clean Architecture: I understand the layers, dependency rule, ports and adapters (hexagonal), SOLID. Theoretical understanding is solid. 8/10.

- Testing: PHPUnit, Pest, mocking, TDD concepts, integration tests, test doubles, etc. 8-9/10. Let's say 8.

- Git: I know the commands, workflows, rebasing, merging, hooks, etc. 8/10.

Actually, let me reconsider. I should be honest and balanced. I don't "work" in PHP - I process text. But I can demonstrate deep knowledge. Let me frame this as knowledge depth rather than practical experience.

Let me be reasonable with ratings:
- PHP 8: 9 — comprehensive knowledge of language features, internals basics
- Symfony: 9 — framework is extremely well-documented and covered
- Doctrine: 8 — solid ORM knowledge, some edge cases in complex mappings less certain
- PostgreSQL: 8 — strong SQL and concept knowledge, less practical DBA tuning
- DDD: 7 — good theoretical grounding, but practical application is where it gets nuanced and I'm less "battle-tested"
- Clean Architecture: 8 — solid understanding of principles and patterns
- Testing: 8 — PHPUnit, TDD concepts, mocking, but less "gut feel" for when to test what
- Git: 8 — commands and workflows well known, but less "I've resolved a nasty merge conflict at 2am"

Let me write a natural, conversational response in Russian since the user wrote in Russian.

</details>

**Кандидат:**

Привет! Давай познакомимся.

## Обо мне — коротко

Я языковая модель, у меня нет «стажа в компании» и не было ночных дежурств с алертами. Но мой «опыт» — это огромный корпус текста: документация PHP, Symfony и Doctrine, руководства по PostgreSQL, книги Martin Fowlera, Uncle Bоба Марти, Мартина Поулхана, тысячи реальных GitHub-репозиториев, Stack Overflow-треды, RFC-обсуждения. Я «прожил» через текст то, что люди проживают за годы практики. Что это даёт и чего не даёт — честно скажу в оценках ниже.

## Оценки

| Раздел | Балл | Одно предложение |
|---|---|---|
| **PHP 8** | **9 / 10** | Знаю фичи от enum'ов и readonly до Fibers и JIT-интерно, могу разобрать edge-cases с late static binding или сериализацией, но без «живого» отладчика не отличу, например, поведение `WeakMap` в экзотических GC-сценариях. |
| **Symfony** | **9 / 10** | Контейнер DI, события, security, messenger, form, serializer, routing — знаю на уровне «напишу конфиг и middleware с нуля», минус балл за то, что микронюансы производительности конкретного minor-релиза могу перепутать. |
| **Doctrine** | **8 / 10** | ORM-связи, DQL, QueryBuilder, миграции, UnitOfWork — уверенно; стumbles на очень хитрых каскадных удалениях и performance-трюках с `SET`-гидратацией, где «правильный ответ» зависит от конкретного графа сущностей. |
| **PostgreSQL** | **8 / 10** | SQL, JSONB, CTE, window functions, индексы (b-tree, GIN, BRIN), репликация на уровне концепций — всё на месте; минус балл за то, что я не DBA и не чувствую «запах» query plan в продакшене. |
| **DDD** | **7 / 10** | Aggregate, Value Object, Domain Event, Bounded Context, стратегические паттерны — теория крепкая; но где именно провести границу агрегата в *твоём* домене и когда DDD — overkill, я не могу сказать «из опыта», только аргументировать. |
| **Чистая архитектура / hexagonal** | **8 / 10** | Правило зависимостей, Ports & Adapters, SOLID, layering — объясню и помогу переработать монолит в слои; минус балл за то, что в реальных проектах «чистота» часто ломается pragmatic-решениями, и я покажу оба конца спектра, но не «поживу» ни одно. |
| **Тестирование** | **8 / 10** | PHPUnit, Pest, моки, TDD/BDD-цикл, интеграционные с Testcontainers, property-based — всё уверенно; минус балл за интуицию «а тут реально ли нужен mock, а не реальный тест с SQLite в памяти». |
| **Git** | **8 / 10** | rebase, cherry-pick, hooks, workflow-стратегии, разрешение конфликтов, bisect — знаю; минус балл за то, что не «сидел в `git rebase -i` в 3 часа ночи на production-бэче», и тонкости edge-cases с partial merge я могу изложить, но не «почувствовать». |

## Что я умею на практике

- Разобрать кусок кода, указать code smells, предложить refactoring.
- Написать или ревьюнуть микросервис на Symfony + Doctrine + Postgres.
- Помочь спроектировать модуль по DDD и разложить его в hexagonal-слои.
- Написать тесты, объяснить, почему один тест не покрывает инвариант.
- Разобрать `git log`, предложить стратегию merge, написать hook.

## Чего я **не** могу

- Запустить сервер, пощёлкать в pgAdmin, посмотреть реальный `EXPLAIN ANALYZE`.
- Услышать «а у нас на проде это падает, потому что…».
- Взять ответственность за продакшен.

Так что я — очень подробный справочник с голосом, а не коллега, который «был на проекте три года». Но в рамках текста я, надеюсь, полезный собеседник.

С чего начнём — конкретный проект, вопрос по архитектуре, разбор кода?

> ⏱ 18.6s · prompt 87 tok · output 1856 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user is asking about the most important PHP 8.0+ features for writing clean and reliable code, with short code examples. Let me think about what's genuinely important from a "clean architecture / DDD / reliable code" perspective, not just a "cool new syntax" list.

Key features from PHP 8.0-8.4 that matter for clean/reliable code:

1. **Readonly properties (8.1)** — prevents accidental mutation of value objects, makes invariants obvious. Huge for DDD.
2. **Enums (8.1)** — replaces magic strings/constants, type-safe state.
3. **Constructor property promotion (8.0)** — reduces boilerplate, makes constructor explicit.
4. **Named arguments (8.0)** — readability, less fragile to parameter reordering.
5. **First-class callable syntax (8.1)** — cleaner callbacks.
6. **Fiber (8.1)** — async without event loop, relevant for long-running tasks.
7. **readonly classes (8.2)** — whole object is immutable, perfect for DTOs, Value Objects.
8. **Destructuring improvements (8.0)** — list() with keys.
9. **Never return type (8.1)** — for functions that never return (exit, throw).
10. **`new in initializers` (8.1)** — less boilerplate.
11. **WeakMap (8.2)** — avoids circular references, GC-friendly.
12. **Property hooks (8.4)** — validation in property access.
13. **Asymmetric visibility (8.4)** — public getter, private setter pattern.
14. **Typed class constants (8.3)** — type-safe constants.
15. **JIT (8.0)** — performance, less relevant for "clean code" per se.
16. **Match expressions (8.0)** — exhaustive, no fall-through, cleaner than switch.
17. **Nullsafe operator (8.0)** — less null-checking boilerplate.
18. **Stricter typing overall** — strict_types, etc.

Let me pick the ones that genuinely matter for *clean and reliable code* (not just performance), and group them logically. I'll aim for 6-8 features with concise examples. The user wants "short examples" so I shouldn't write 50-line code blocks.

Let me structure this as a focused answer, not an exhaustive list. I'll pick what I genuinely consider most impactful for *reliability* and *clarity*:

1. **Readonly classes + properties (8.1/8.2)** — immutability as default
2. **Enums (8.1)** — type-safe domain states
3. **Match (8.0)** — exhaustive, no accidental fall-through
4. **Constructor promotion + named args (8.0)** — less boilerplate, explicit calls
5. **Never return type (8.1)** — honest contracts
6. **Property hooks + asymmetric visibility (8.4)** — encapsulation without getters/setters boilerplate
7. **First-class callable (8.1)** — cleaner functional composition

That's a good set. Let me write concise examples. I'll keep each example to 5-12 lines max.

Format: I'll use a structure per feature with a one-line "why it matters" and a short code snippet. Keep it tight.

</details>

**Кандидат:**

## Что я считаю действительно важным

Не «что нового», а что из нового напрямую снижает вероятность багов и делает инварианты явными.

---

### 1. `readonly`-классы (8.2) — неизменяемость по умолчанию

В DDD/Value Object это, пожалуй, главная фича. Объект нельзя мутировать после создания — инварианты держит сам язык.

```php
final readonly class Money
{
    public function __construct(
        public int    $amount,
        public string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatch($this->currency, $other->currency);
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

// $money->amount = 999;  → FATAL: Cannot modify readonly property
```

До 8.2 «Value Object» держался на дисциплине. Теперь компилятор за тебя.

---

### 2. Enum (8.1) — конечное множество состояний

Убивает магические строки и `const`-списки, которые никто не перебирает.

```php
enum OrderStatus: string
{
    case Pending  = 'pending';
    case Shipped  = 'shipped';
    case Delivered = 'delivered';

    public function canCancel(): bool
    {
        return match ($this) {
            self::Pending => true,
            self::Shipped, self::Delivered => false,
        };
    }
}

// В DQL / сериализации:
// new OrderStatus('nonexistent') → ValueError, а не тихий null
```

Тип `OrderStatus` в сигнатурах — и IDE, и стат-анализатор, и runtime знают: других значений не будет.

---

### 3. `match` (8.0) — исчерпывающий, без «срыва»

`switch` молча пропускает несуществующий `default`. `match` — нет.

```php
// switch: забыл case → вернёт null, баг уедет в прод
// match:  забыл ветку  → UnhandledMatchError, падает на этапе dev

$shipping = match ($order->region) {
    'EU'  => new EuTax($order),
    'US'  => new UsTax($order),
    default => throw new UnsupportedRegion($order->region),
};
```

В комбинации с `enum` (пример 2) `match` становится исчерпывающим без `default`: компилятор знает все случаи.

---

### 4. Промоуция + именованные аргументы (8.0)

Меньше шума в конструкторах; вызов читается как документация.

```php
final readonly class PaymentGateway
{
    public function __construct(
        public string $merchantId,
        public int    $timeoutMs = 5_000,
        public bool   $idempotent = true,
    ) {}
}

// Было:
// new PaymentGateway('M-42', 3000, true);
// А стало:
$gateway = new PaymentGateway(
    merchantId: 'M-42',
    timeoutMs:  3_000,
);
```

Переупорядочивание параметров в сигнатуре больше не ломает код — именованные аргументы привязаны по имени.

---

### 5. `never` (8.1) — честный контракт «здесь конца нет»

Функция, которая либо бросает исключение, либо завершает процесс — возвращает `never`, а не `void`. Стат-анализаторы и IDE перестают считать что-то «возвращаемым».

```php
function findOrFail(OrderRepository $repo, string $id): Order
{
    return $repo->find($id)
        ?? throw new OrderNotFound($id);   // ветка типизирована как never
}

// Вызывающий код не обязан проверять null:
$order = findOrFail($repo, $id);  // стат-анализатор знает: здесь Order, не ?Order
```

---

### 6. Property hooks + асимметричная видимость (8.4)

Капсуляция без пары `getX()`/`setX()` и без «забыл проверить в setter».

```php
final class Account
{
    public int $balance
    {
        set(int $value)
        {
            if ($value < 0) {
                throw new NegativeBalance($value);
            }
            $this->balance = $value;
        }
    }
}

$account->balance = 500;   // ок
$account->balance = -10;   // NegativeBalance — до того, как значение «закралось»
// $account->balance       // чтение без отдельного getter
```

Инвариант живёт *в* свойстве, а не в случайном сервисе, который «должен был» проверить.

---

### 7. First-class callable (8.1) — чистая композиция

```php
final class OrderProcessor
{
    public function pipeline(array $orders): array
    {
        return array_map(
            $this->applyTaxes(...),   // не fn($o) => $this->applyTaxes($o)
            $orders,
        );
    }
}
```

Меньше стрелочных функций-обёрток, которые скрывают, *кто именно* выполняется. В сочетании с `readonly`-объектами это даёт предсказуемую композицию без замыканий.

---

## Что я намеренно **не** включил

- **Fibers (8.1)** — мощный инструмент, но это про *когда* выполняется код, а не про *надёжность* модели. Для чистого доменного кода нейтрален.
- **JIT (8.0+)** — про скорость, не про корректность.
- **WeakMap (8.2)** — узкая задача, не «каркас чистого кода».

---

Если хочешь, могу на любом из примеров показать, как это ложится в конкретный слой hexagonal-архитектуры (domain / application / infrastructure), или разобрать, какие из этих фич *конфликтуют* с паттернами, которые ещё живут в Symfony-базовом коде (например, `__set` магические свойства vs property hooks).

> ⏱ 17.8s · prompt 1212 tok · output 2264 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о системе типов в PHP: strict_types, приведения типов, ловушки. Нужно дать структурированный ответ с примерами кода.

Ключевые моменты:
1. PHP — динамически типизированный язык со слабыми типами по умолчанию
2. declare(strict_types=1) — что именно делает
3. Приведения типов: коэрция, коэрция строк, числа, bool, массивы
4. Ловушки: "0" == false, "" == 0, "10" == 10, строки как числа, null, массивы как строки
5. Где типизация спасает: strict_types, union types, intersection types, typed properties, readonly
6. Где удивляет: mixed type, null в аргументах, строки-числа, "hello" + 1

Структура:
- Кратко: PHP — динамический язык, типы в рантайме, weak typing по умолчанию
- strict_types: что делает, как работает (per-file, не глобальный)
- Приведения типов: таблица/примеры
- Ловушки с кодом
- Где спасает
- Где удивляет

Буду конкретен, с кодом, без воды.

</details>

**Кандидат:**

## Базовая модель

PHP — динамически типизированный язык: тип значения определяется в рантайме, а не на этапе компиляции. При этом с 7.0+ у PHP есть *декларативные* типы (свойства, аргументы, возвращаемые значения), но по умолчанию они работают в режиме **coercive typing**: язык сам пытается привести аргумент к ожидаемому типу, вместо того чтобы упасть.

Это главный источник большинства сюрпризов.

---

## `strict_types=1` — что он реально делает

```php
// src/Calculator.php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

function format(string $s): string {
    return strtoupper($s);
}
```

```php
// caller.php  (БЕЗ strict_types)
add(42, "10");       // OK → 52. Строка приведена к int.
add(42, 10.5);       // OK → 52. Float приведён к int (обрезка).
format(123);         // OK → "123". Int приведён к строке.
format(null);        // OK → "".  Null приведён к строке.
format([1,2,3]);     // TypeError! Массив нельзя привести к строке.
```

```php
// caller.php  (С strict_types)
declare(strict_types=1);

add(42, "10");       // TypeError: Argument #2 must be of type int, string given
add(42, 10.5);       // TypeError: Argument #2 must be of type int, float given
format(123);         // TypeError: Argument #1 must be of type string, int given
format(null);        // TypeError: Argument #1 must be of type string, null given
```

### Ключевые нюансы

| Факт | Пояснение |
|---|---|
| **Per-file** | `declare(strict_types=1)` валидна только в файле, где она написана, и действует на *вызовы*, сделанные из этого файла. Приёмник функции не имеет значения. |
| **Не глобальный** | Нельзя включить «на всё приложение». Каждый файл — свой. |
| **Только аргументы** | Возвращаемое значение, `return` и присвоение в свойства **не** проверяются strict-режимом (см. ниже). |
| **`null` → `mixed`** | `null` всё равно принимается как `mixed`, если параметр `mixed` или nullable. Но `null` → `string`/`int`/`bool`/`float` — TypeError. |
| **`true`/`false` → `int`** | Даже в strict-режиме `bool` приводится к `int` (1/0), потому что это «встроенная» коэрция PHP. |

```php
declare(strict_types=1);

function take(int $x): int { return $x; }

take(true);   // OK! → 1.  Bool→int допускается даже в strict.
take(false);  // OK! → 0.

take("5");    // TypeError.
take(5.0);    // TypeError (float ≠ int).
```

Это единственное «пробитие» strict_types, и оно часто удивляет.

---

## Что strict_types **не** проверяет

```php
declare(strict_types=1);

function returnsInt(): int {
    return "42";        // OK! Coercive return. Возвращаемый тип не strict.
    // return 42;       // тоже OK.
}

class Box {
    public int $val;
}

$b = new Box();
$b->val = "7";         // OK! Присвоение в свойство — всегда coercive.
$b->val = null;        // TypeError (null не приводится к int).
$b->val = 3.99;        // TypeError (float ≠ int, даже при коэрции).
```

Строка → свойство `int` работает. Float → свойство `int` **нет**. Это не последовательно, но так устроено.

---

## Приведения типов: карта коэрций

| Ожидаемый тип | Что примется (coercive) | Что упадёт |
|---|---|---|
| `int` | строка с числом `"42"`, `true`→1, `false`→0, `42.0`→42 (только в strict) | `"hello"`, `null`, массив, `42.5` |
| `float` | `int`, строка `"3.14"`, `true`→1.0 | `"hello"`, `null`, массив |
| `string` | `int`, `float`, `bool`, `null`→`""` | массив, объект без `__toString` |
| `bool` | **всё**, кроме `null` и пустых структур | — (см. ловушки ниже) |
| `mixed` | буквально всё | — |
| `array` | — | что угодно, кроме массива |

---

## Ловушки на практике

### 1. Сравнение с `==`

```php
var_dump(0 == "hello");     // true   — "hello" приведён к 0
var_dump("0" == false);     // true
var_dump(1 == "1abc");      // false  (8.0+, до 7.x было true!)
var_dump(0 == null);        // true
var_dump("abc" == 0);       // false  (8.0+)
var_dump("" == "0");        // false  (8.0+, до 7.x было true)
var_dump([] == false);      // false  (8.0+, до 7.x было true!)
var_dump([] == 0);          // false  (8.0+)
var_dump(null == 0);        // true
```

PHP 8.0 перестал приводить строки-не-числа к `0` при сравнении с числом, но `null == 0` и `false == "0"` живут.

**Практика:** в доменном коде использовать только `===` и `!==`. Если нужен сравнение «пусто или нет» — явные проверки.

```php
// Плохо:
if ($input == "") { ... }

// Хорошо:
if ($input === null || $input === "") { ... }
// Или:
if (!is_string($input) || $input === "") { ... }
```

### 2. Арифметика со строками

```php
$a = "10";
$b = "20";

echo $a + $b;      // 30  (строки → int)
echo $a . " px";   // "10 px"
echo $a * $b;      // 200

// Ловушка:
$c = "10px";
echo $c + 5;       // 5!  "10px" → 0 (не число), 0 + 5 = 5
echo $c * 10;      // 0

// PHP 8.1+:
echo $c + 5;       // Warning: Unsupported operand types... но всё равно 5
```

### 3. `bool` как «всё»

```php
var_dump((bool) "0");       // false  — единственная строка, ведущая к false
var_dump((bool) "hello");   // true
var_dump((bool) "0.0");     // true!
var_dump((bool) [1,2,3]);   // true
var_dump((bool) null);      // false
var_dump((bool) 0);         // false
var_dump((bool) -1);        // true
```

```php
function process(?string $flag): void {
    if ($flag) {            // "0" → false! "0.0" → true!
        echo "enabled\n";
    }
}

process("0");     // ничего не печатает
process("0.0");   // "enabled"
process("");      // ничего
process(null);    // ничего
```

### 4. `mixed` как «тихий» `any`

```php
function handle(mixed $data): string {
    return $data . "!";
}

handle(42);       // "42!"
handle(true);     // "1!"
handle([1,2]);    // Warning: Array to string conversion → "Array!"
handle(null);     // "!"  (null → "")
```

В доменном коде `mixed` — красный флаг. Если функция принимает `mixed`, у вас нет контракта.

### 5. Nullable vs `null`

```php
function find(?string $id): string {
    // $id может быть null. Стат-анализатор это видит.
    // Но в runtime:
    return strtoupper($id);  // Warning: strtoupper(null) → deprecated в 8.1
}

// А здесь:
function find(string $id): string {
    return strtoupper($id);
}

find(null);  // TypeError (strict) / "" (coercive, deprecated)
```

### 6. Внутренние функции vs userland

```php
function myUpper(string $s): string { return strtoupper($s); }

declare(strict_types=1);
myUpper(42);      // TypeError — PHP проверяет аргумент *вашей* функции.

strtoupper(42);   // OK → "42". Внутренние функции не подчиняются strict_types.
```

Это означает: ваш strict-обёрточный слой защищает *вас*, но не защищает вас от внутреннего кода.

---

## Где типизация реально спасает

### Typed properties + readonly

```php
final readonly class OrderId
{
    public function __construct(
        public string $value,
    ) {
        if ($value === '' || !ctype_alnum($value)) {
            throw new InvalidOrderId($value);
        }
    }
}

// $id = new OrderId(42);       // TypeError: string expected
// $id = new OrderId(null);     // TypeError
// $id->value = "hacked";       // Fatal: readonly
```

До 7.4 «Value Object» был конструктор + private + getter. После 8.2 — `final readonly class` и готово. Инвариант живёт в языке.

### Union types для исчерпывающих состояний

```php
type Result<T> = Success<T>|Failure;

function loadOrder(string $id): Result<Order>
{
    $order = $repo->find($id);
    return $order !== null
        ? new Success($order)
        : new Failure(OrderNotFound::from($id));
}

// Вызывающий код *обязан* разобрать оба варианта:
$result = loadOrder("ORD-1");
match (true) {
    $result instanceof Success => render($result->value),
    $result instanceof Failure => redirect($result->error),
    // Нет «забыл ветку» — стат-анализатор подскажет.
};
```

### Intersection types для интерфейсных контрактов

```php
interface Loggable   { public function log(string $msg): void; }
interface Idempotent { public function isIdempotent(): bool; }

function dispatch(Loggable & Idempotent $cmd): void
{
    // Гарантировано: и log() есть, и isIdempotent() есть.
    // Перед 7.2 приходилось делать instanceof-проверки в runtime.
}
```

### Never (8.1) — «функция не вернёт значение»

```php
function mustBePositive(int $x): int
{
    return $x > 0
        ? $x
        : throw new InvalidArgumentException("must be > 0");
}

// Стат-анализатор знает: здесь int, не ?int.
// Не нужно `?? 0` на выходе.
$val = mustBePositive($input);
```

---

## Где удивляет (подборка)

```php
// 1. Строка с числом + строка без числа
echo "12" + "34";      // 46  (оба → int)
echo "12" + "abc";     // 12  ("abc" → 0)
echo "12px" + 5;       // 5   ("12px" → 0)

// 2. Массив как ключ
$arr = ["a" => 1];
echo $arr["A"];        // 1 — ключи строк без чувствительности к регистру (для не-числовых)
echo $arr["a"];        // 1
$arr["b"] = 2;
echo $arr["B"];        // 2

// 3. Но числовые ключи — без коэрции
$arr[1] = "one";
$arr["1"] = "TWO";     // тот же ключ! Перезаписал.
echo $arr[1];          // "TWO"

// 4. null + что-то
echo null + 5;         // 5  (null → 0)
echo null . "x";       // "x" (null → "")
echo null === 0;       // false
echo null == 0;        // true

// 5. Сравнение массивов
var_dump([1,2] == [2,1]);  // false — порядок важен
var_dump([1,2] === [1,2]); // true
var_dump([1=>2] == [2=>1]); // false
```

---

## Практические рекомендации

1. **`declare(strict_types=1)`** — в каждом пользовательском файле. Без исключений.
2. **Только `===` / `!==`** в бизнес-логике. `==` — только в сравнениях с `null`, и то лучше `$x === null`.
3. **Избегать `mixed`** в публичных сигнатурах. Если данные извне — валидировать на границе, дальше — конкретный тип.
4. **Nullable явно**: `?string`, а не `string|null` (то же самое, но `?` короче) или `string` + `null` в union, если есть ещё варианты.
5. **`readonly`** — для всех Value Object, ID, конфигурации.
6. **Enum** — для любого конечного множества (статусы, категории, направления).
7. **Стат-анализатор** (PHPStan level 8+/9, Psalm) — строгие типы в рантайме не заменяют стат-анализ. Они дополняют.
8. **PHP 8.4 property hooks** — вместо ручных `setX()` с проверками, если инвариант простой.

---

Если хочешь, могу показать, как эти правила ложатся на конкретный слой: например, доменную сущность + application-команду + infra-репозиторий, с full-типизацией и strict_types на каждом уровне.

> ⏱ 28.7s · prompt 2793 tok · output 3781 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user asks about exception handling design in PHP applications. This is a deep architectural question. Let me structure my answer around:

1. My philosophy/approach to exception hierarchies
2. Wrapping external/foreign exceptions
3. What constitutes a method's contract regarding exceptions
4. Anti-patterns

I should be concrete with code examples, reference PHP 8+ features (readonly, enums, etc.), and tie it to DDD/clean architecture since we've been discussing that. Let me think about what's genuinely useful vs. generic advice.

Key principles I want to convey:
- Exceptions are part of the API contract, not just "error handling"
- Domain exceptions vs. infrastructure exceptions vs. application exceptions
- The "throw domain, catch infrastructure" principle
- PHP-specific issues: Exception vs Throwable, Error vs Exception hierarchy
- Anti-patterns: catching Throwable, generic catch, exception-as-control-flow, swallowing, over-catching

Let me structure this clearly with code examples that build on each other.

</details>

**Кандидат:**

## Принципы, на которых строю

1. **Исключение — часть контракта.** Если метод может не вернуть значение, он должен *сказать почему* на уровне типа, а не только в runtime.
2. **Домен не знает о инфраструктуре.** `OrderRepository` бросает `OrderNotFound`, а не `Doctrine\ORM\NoResultException`.
3. **Каждый слой перехватывает только то, что *понимает*.** Не «всё подряд».
4. **`Error` ≠ `Exception`.** `TypeError`, `DivisionByZeroError`, `ArgumentCountError` — это баги кода, а не «предполагаемая ситуация». Их не ловят в бизнес-логике.

---

## Иерархия: три круга

```
DomainException                      // корень, не inherits от \Exception — свой
├── OrderNotFound
├── InsufficientStock
├── InvalidOrderTransition
└── ...

ApplicationException                 // корень application-слоя
├── ValidationFailed
├── UnauthorizedAction
└── ...

InfrastructureException              // корень infra-слоя (внутренний)
├── DatabaseConnectionLost
├── HttpGatewayTimeout
└── ...
```

Важно: доменные исключения **не наследуются** от `\Exception`. Они наследуются от собственного `DomainException`, который наследуется от `\Exception` (чтобы `catch (\Exception $e)` на границе всё ещё работал), но *доменный код никогда не ловит `\Exception`*.

```php
// domain/DomainException.php
namespace App\Domain;

/**
 * Базовое исключение домена.
 * Не содержит message-холод. Конструктор принимает контекст.
 */
abstract class DomainException extends \Exception
{
    /** @param class-string<self> $class */
    protected static function make(string $class, string $context = ''): static
    {
        return new static($context !== '' ? "$class: $context" : $class);
    }
}

// domain/Order/OrderNotFound.php
namespace App\Domain\Order;

final class OrderNotFound extends \App\Domain\DomainException
{
    private readonly string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
        parent::__construct("Order not found: $orderId");
    }

    public function orderId(): string
    {
        return $this->orderId;
    }
}
```

Зачем `orderId()` как геттер, а не парсинг `getMessage()`? Потому что **message — для людей, данные — для кода**. Локализация, переформатирование, логирование — не трогают структуру.

---

## Контракт метода: что «документирует» сигнатура

PHP не имеет `throws`-клаузуры. Контракт живёт в:

| Канал | Что несёт |
|---|---|
| Docblock `@throws` | Статический анализ (PHPStan/Psalm) видит, что *может* упасть |
| Тип возвращаемого значения | Если `Order` (не `?Order`) — значит либо возвращаем, либо бросаем |
| `never` (8.1) | Метод, который *всегда* бросает (например, `assert`) |
| Именование | `findOrThrow` vs `find` — семантический сигнал |

```php
final class OrderRepository
{
    /**
     * Возвращает Order или бросает OrderNotFound.
     * Никогда не возвращает null.
     *
     * @throws OrderNotFound
     */
    public function findById(string $id): Order
    {
        $result = $this->em->getRepository(OrderEntity::class)->find($id);

        return match (true) {
            $result instanceof OrderEntity => Order::fromEntity($result),
            default => throw new OrderNotFound($id),
        };
    }

    /**
     * Возвращает Order или null. Не бросает.
     */
    public function findOrNull(string $id): ?Order
    {
        $result = $this->em->getRepository(OrderEntity::class)->find($id);
        return $result instanceof OrderEntity ? Order::fromEntity($result) : null;
    }
}
```

**Принцип:** один метод — одна семантика. Либо `findById` (бросает), либо `findOrNull` (возвращает `null`). Не `findById` с `?Order` и catch внутри — это «я и сам не решил, что делать».

---

## Оборачивание чужих исключений

### Граница — единственный место, где «чужое» становится «нашим»

```php
// infrastructure/Doctrine/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findById(string $id): Order
    {
        try {
            $entity = $this->em->getRepository(OrderEntity::class)
                               ->findOneBy(['uuid' => $id]);
        } catch (NoResultFound $e) {
            throw new OrderNotFound($id) from $e;   // chain, не swallow
        }

        if ($entity === null) {
            throw new OrderNotFound($id);
        }

        return Order::fromEntity($entity);
    }
}
```

### HTTP-гейтвей

```php
// infrastructure/Http/StripeGateway.php
final class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(private HttpClientInterface $http, private string $apiKey) {}

    public function charge(ChargeRequest $request): ChargeResult
    {
        try {
            $response = $this->http->post(
                'https://api.stripe.com/v1/charges',
                headers: ['Authorization' => "Bearer {$this->apiKey}"],
                body:    $request->toQuery(),
            );
        } catch (ConnectionException $e) {
            throw new PaymentGatewayUnavailable('Stripe: ' . $e->getMessage()) from $e;
        }

        return match ($response->status()) {
            200..299 => ChargeResult::fromJson($response->body()),
            402      => throw new PaymentDeclined($request->orderId()),
            429      => throw new PaymentGatewayRateLimited(),
            default   => throw new PaymentGatewayError(
                             "Unexpected status {$response->status()}",
                             $response->body(),
                         ),
        };
    }
}
```

### Что здесь происходит

| Шаг | Зачем |
|---|---|
| `catch (NoResultFound)` — **конкретный** класс | Не `catch (\Exception)`. Не ловим то, что не планировали. |
| `throw new OrderNotFound(...) from $e` | Сохраняем причинную цепочку. В логге видно *что* было внутри. |
| `match ($response->status())` | Каждый HTTP-статус — осмысленное доменное/прикладное исключение. |
| Граница (инфра) — единственное место, где ловим `NoResultFound`, `ConnectionException`, `TimeoutException` | Домен знает только `OrderNotFound`, `PaymentDeclined`. |

---

## Слои: кто что ловит

```
┌─────────────────────────────────────────────────────────────────┐
│  Controller / CommandHandler                                     │
│  ─ ловит DomainException, ApplicationException                  │
│  ─ транслирует в HTTP 4xx / 5xx                                  │
│  ─ НЕ ловит \Exception (кроме финального fallback)               │
├─────────────────────────────────────────────────────────────────┤
│  Application Service                                             │
│  ─ не ловит ничего (пропускает наверх)                          │
│  ─ или ловит ApplicationException, переупаковывает в Domain     │
├─────────────────────────────────────────────────────────────────┤
│  Domain (aggregate, service)                                     │
│  ─ бросает DomainException                                       │
│  ─ НИКОГДА не ловит \Exception, \Error, infra-исключения        │
├─────────────────────────────────────────────────────────────────┤
│  Infrastructure (repo, gateway, queue)                           │
│  ─ ловит «чужие» (Doctrine, cURL, Swoole)                       │
│  ─ бросает своё (доменное или infra-обёртка)                     │
└─────────────────────────────────────────────────────────────────┘
```

```php
// application/PlaceOrderHandler.php
final class PlaceOrderHandler
{
    public function __construct(
        private OrderRepository $orders,
        private StockService    $stock,
        private PaymentGateway  $payments,
    ) {}

    public function handle(PlaceOrderCommand $cmd): OrderId
    {
        $order = Order::create($cmd->items, $cmd->customer);

        $this->stock->reserve($order->items);       // бросает InsufficientStock
        $this->orders->save($order);                 // бросает PersistenceError (infra)

        $charge = $this->payments->charge(           // бросает PaymentDeclined / Unavailable
            ChargeRequest::forOrder($order),
        );
        $order->markPaid($charge->transactionId());
        $this->orders->save($order);

        return $order->id();
    }
    // НЕТ try/catch. Исключения всплывают наверх.
}
```

```php
// controller/OrderController.php
final class OrderController
{
    public function place(PlaceOrderCommand $cmd): JsonResponse
    {
        try {
            $id = $this->handler->handle($cmd);
            return new JsonResponse(['id' => $id->value()], 201);
        } catch (InsufficientStock $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        } catch (PaymentDeclined $e) {
            return new JsonResponse(['error' => $e->getMessage()], 402);
        } catch (PaymentGatewayUnavailable $e) {
            return new JsonResponse(['error' => 'Service temporarily unavailable'], 503);
        } catch (DomainException $e) {
            // Неопознанное доменное — это баг, 500.
            $this->logger->error('Unhandled domain exception', ['exception' => $e]);
            return new JsonResponse(['error' => 'Internal error'], 500);
        } catch (\Throwable $e) {
            // Альтернативная точка: Error, баг, что-то непредвиденное.
            $this->logger->critical('Uncaught throwable', [
                'class'     => $e::class,
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return new JsonResponse(['error' => 'Internal error'], 500);
        }
    }
}
```

**Почему `catch (\Throwable)` — в последнюю очередь и только на границе.** `Error` — это `TypeError`, `ArgumentCountError`, `ParseError`. Если он дошёл до контроллера — код сломан. Логируем, возвращаем 500, не пытаемся «обработать бизнес-смысл».

---

## Антипаттерны

### 1. `catch (\Exception $e) {}` / пустой блок

```php
try {
    $this->repo->save($order);
} catch (\Exception $e) {
    // «пока просто проглотим, потом разберёмся»
}
```

Исключение исчезло. Нет лога. Нет алерта. Данные не сохранены. Клиент получил 200. **Худший из всех.**

### 2. `catch (\Exception $e)` как «универсальный» в домене

```php
// domain/OrderService.php
public function ship(Order $order): void
{
    try {
        $this->notifyCourier($order);
    } catch (\Exception $e) {
        // «а вдруг courier не важен, пропущу»
        // Но мы проглотили и OutOfMemoryError, и LogicError.
    }
    $order->markShipped();
}
```

Домен не должен решать, «важно ли» исключение. Это решение application/controller.

### 3. Исключения как flow-control

```php
function getStrategy(string $type) {
    try {
        return new TaxStrategy($type);
    } catch (InvalidType $e) {
        return new DefaultStrategy();
    }
}
```

Вместо:

```php
function getStrategy(string $type): Strategy {
    return match ($type) {
        'eu' => new EuTaxStrategy(),
        'us' => new UsTaxStrategy(),
        default => new DefaultStrategy(),
    };
}
```

`match` + явные ветки — детерминированно. `try/catch` для «может, такого не будет» — нет.

### 4. `getMessage()` как единственный канал информации

```php
catch (OrderNotFound $e) {
    $orderId = substr($e->getMessage(), 20);  // парсинг строки!
    // Локализовали message в en → "Order not found: ORD-1"
    // А в ru → "Заказ не найден: ORD-1"
    // substr(20) теперь режет "ORD-1" пополам.
}
```

Решение: данные — в свойстве/геттере (см. `OrderNotFound::orderId()`), message — для человека.

### 5. Наследование `\RuntimeException` для «всего»

```php
class OrderNotFound extends \RuntimeException {}
class InsufficientStock extends \RuntimeException {}
class PaymentDeclined extends \RuntimeException {}
```

Это «технические» исключения, которые кто-то поймает заодно с `DatabaseException`. Семантика потеряна. Нужен собственный корень.

### 6. `finally` для бизнес-логики

```php
try {
    $this->process();
} finally {
    $this->sendAuditLog();   // если process() бросил — аудит всё равно уйдёт.
}
```

Само по себе не баг, но если `sendAuditLog()` *тоже* может упасть, он перезаписывает первоначальное исключение. Правильно: `finally` — только для освобождения ресурсов (соединение, файл). Аудит — отдельный шаг в `try` или `catch`.

### 7. Catch-перекати-поле по уровню

```php
catch (\Exception $e) {
    if ($e instanceof OrderNotFound) { ... }
    elseif ($e instanceof PaymentDeclined) { ... }
    else { ... }
}
```

Вместо:

```php
catch (OrderNotFound $e) { ... }
catch (PaymentDeclined $e) { ... }
catch (DomainException $e) { ... }
```

Перечисление *конкретных* типов сверху вниз — читается как контракт. `instanceof`-цепь — «я не знаю, что здесь будет».

### 8. Создание нового исключения без `from $e`

```php
catch (NoResultFound $e) {
    throw new OrderNotFound($id);   // потеряна причина
}
```

Вместо:

```php
catch (NoResultFound $e) {
    throw new OrderNotFound($id) from $e;   // getPrevious() → NoResultFound
}
```

В логгере видно: `OrderNotFound: Order not found: ORD-1` → `NoResultFound: No OrderEntity found for identifier`.

---

## Чек-лист при ревью

- [ ] Каждое `catch` — **конкретный** тип, не `\Exception` / `\Throwable` (кроме финального fallback на границе).
- [ ] Пустых `catch`-блоков нет. Ни одного.
- [ ] Доменный код не содержит `catch`. Только `throw`.
- [ ] Каждое доменное исключение несёт **данные** (геттер), не только `message`.
- [ ] `throw ... from $e` — везде, где оборачиваем.
- [ ] `@throws` в docblock — для каждого исключения, которое *может* быть брошено.
- [ ] `\Error` не ловится в бизнес-логике.
- [ ] `finally` — только освобождение ресурсов, не бизнес-действия.

---

Если хочешь, могу показать, как это ложится на конкретный сценарий: «клиент платит картой → stripe → 402 → откат инвентаря → аудит-лог», с кодом на каждом слое и всеми `from $e`.

> ⏱ 30.8s · prompt 6342 tok · output 4095 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о том, какой принцип SOLID чаще всего нарушается в реальном коде, почему, и просит показать типовой пример нарушения и рефакторинг.

Давайте подумаем, что реально встречается чаще всего в PHP-коде (и не только PHP):

1. **SRP (Single Responsibility Principle)** — нарушается постоянно, но это скорее "плохая организация кода" чем конкретный баг. Крупные классы, которые делают 5 вещей.

2. **OCP (Open/Closed Principle)** — нарушается, когда приходится менять существующий код при добавлении нового поведения. Типичный пример: if/elseif/switch для разных типов.

3. **LSP (Liskov Substitution Principle)** — нарушается, когда наследование используется не как "is-a", а как "mix-in" или "convenience". В PHP это часто через интерфейсы, которые не все реализуют честно.

4. **ISP (Interface Segregation Principle)** — крупные интерфейсы, в которых половина методов не нужна конкретному классу.

5. **DIP (Dependency Inversion Principle)** — зависит от высокоскоростного модуля от низкоуровневого.

По моему "опыту" (анализу огромного количества кода), я бы сказал, что **OCP** — самый часто нарушаемый и самый "бесшумный". Потому что:
- SRP нарушается явно (огромный класс — видно сразу)
- DIP нарушается, когда нет DI-контейнера (тогда это архитектурная проблема, не "принцип")
- LSP нарушается, но это требует наследования, а в современном PHP с интерфейсами это реже
- ISP — часто, но менее "болезненно"
- **OCP** — это то, что накапливается как техдолг. Каждый новый тип продукта/способ оплаты/статус — это `if/elseif` или `switch`, который разрастается. Код "работает", тесты проходят, но каждый раз приходится трогать старый код. Это классический пример "it works, but it hurts".

Типовой пример в PHP: обработка разных типов скидок/промокодов, или разных способов оплаты, или разных форматов экспорта.

Давайте возьму конкретный, узнаваемый пример: **расчёт итоговой суммы заказа с учётом разных скидок/промокодов**. Это то, что встречается в 80% e-commerce проектов.

Нарушение:
```php
function calculateTotal(array $items, string $promoCode): float {
    $total = 0;
    foreach ($items as $item) {
        $total += $item->price * $item->qty;
    }
    
    if (str_starts_with($promoCode, 'SALE_')) {
        $total *= 0.8; // -20%
    } elseif (str_starts_with($promoCode, 'BIRTHDAY')) {
        $total *= 0.7; // -30%
    } elseif (str_starts_with($promoCode, 'VIP')) {
        $total -= 500; // фиксированная скидка
    } elseif ($total > 10000 && str_starts_with($promoCode, 'LARGE')) {
        $total -= 5%;
    }
    
    return max(0, $total);
}
```

Каждый новый промокод — новая ветка. Это прямое нарушение OCP: закрытый для изменения, открытый для расширения.

Рефакторинг: Strategy pattern + реестр + DI.

Также можно показать, как это ломает LSP (если делать через наследование Discount, и какой-то подкласс не может честно реализовать метод).

Подумаю, стоит ли показать и LSP-нарушение как "второй по частоте"? Нет, пользователь просит один принцип. Сосредоточусь на OCP, но упомяну, что он часто идёт в паре с SRP и LSP.

Структура ответа:
1. Короткий ответ: OCP, почему
2. Типовой пример нарушения (реалистичный PHP-код)
3. Почему это "тихое" нарушение
4. Рефакторинг по шагам
5. Что изменилось и как проверить, что OCP соблюдён

Дам конкретный, реалистичный пример с e-commerce логикой. Код должен быть PHP 8.x с strict_types, readonly, enum.

</details>

**Кандидат:**

## Ответ: OCP (Open/Closed Principle)

Не SRP — его хотя бы *видят* (огромный класс, 500 строк, «а что тут вообще происходит?»). Не DIP — его нарушают, когда архитектуры нет вообще, и это уже не «принцип», а отсутствие проектирования.

OCP нарушается **тихо**. Код работает. Тесты зелёные. Ревью проходит. А через шесть месяцев в `calculateTotal()` уже 14 веток `if/elseif`, и каждый новый промокод — это правка *старой* функции, с которой уже завязаны 20 вызовов. Никто не бросает алерт. Никто не говорит «нарушен принцип». Говорят: «добавь ещё один `elseif`, тесты поправь, пуш».

И через полгода это 40 веток, 3 разработчика, которые не помнят, в какой ветке что, и один `return max(0, $total)`, который кто-то поставил наугад.

---

## Типовое нарушение: расчёт скидки

Это то, что я вижу в 8 из 10 e-commerce проектов на Symfony/PHP.

```php
// src/Service/OrderPricing.php
declare(strict_types=1);

final class OrderPricing
{
    /**
     * @param array<int, {product_id: string, price: int, qty: int}> $items
     */
    public function calculateTotal(array $items, string $promoCode, int $userId): int
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $total = $subtotal;

        // ── Промокоды ──────────────────────────────────────────────
        if (str_starts_with($promoCode, 'SALE_')) {
            $total = (int) ($total * 0.80);          // сезонная -20%
        } elseif (str_starts_with($promoCode, 'BIRTHDAY')) {
            $total = (int) ($total * 0.70);          // день рождения -30%
        } elseif (str_starts_with($promoCode, 'VIP_')) {
            $total = $total - 50000;                  // VIP: -500 ₽
        } elseif (str_starts_with($promoCode, 'LARGE')) {
            if ($subtotal >= 1000000) {
                $total = (int) ($total * 0.95);      // от 10 000 ₽: -5%
            }
        } elseif ($promoCode === 'FREEDELIV') {
            // скидка на доставку обрабатывается отдельно,
            // но тут всё равно трогали $total
            $total = $total - 0; // заглушка, "доставку посчитают потом"
        } elseif ($promoCode === 'WELCOME') {
            if ($this->isFirstOrder($userId)) {
                $total = (int) ($total * 0.90);
            }
        } elseif (str_starts_with($promoCode, 'APP_')) {
            $total = (int) ($total * 0.92);
            // + бонусные баллы, но их считают в другом месте
        }
        // ... через 3 месяца ещё 6 веток

        return max(0, $total);
    }

    private function isFirstOrder(int $userId): bool
    {
        // ... запрос к БД внутри pricing-сервиса
    }
}
```

### Что здесь нарушено (не только OCP)

| Нарушение | Где |
|---|---|
| **OCP** | Каждый новый промокод = правка *этой* функции. Не закрыта для изменения. |
| **SRP** | Сервис знает про промокоды, БД (`isFirstOrder`), доставку, бонусы, VIP-статус. |
| **ISP** | Вызывающий код передаёт `promoCode` как строку — нет контракта, *какой* это промокод. |
| **LSP** | Если попробовать вынести каждый `elseif` в подкласс `Discount`, получится класс, у которого половина методов «не про меня». |
| **Данные через строку** | `str_starts_with($promoCode, 'SALE_')` — парсинг *формата* промокода вместо *типа*. |

Но главный симптом — **OCP**. Функция открыта для изменения, а не для расширения.

---

## Рефакторинг

### Шаг 1: Извлечь доменную сущность «скидка»

```php
// src/Domain/Order/Discount.php
declare(strict_types=1);

namespace App\Domain\Order;

interface Discount
{
    /**
     * Возвращает новую итоговую сумму после применения скидки.
     * Не мутирует состояние. Не знает о БД, HTTP, промокодах-строках.
     */
    public function apply(int $subtotal): int;

    /**
     * Может ли эта скидка быть применена к данному контексту?
     */
    public function isApplicable(OrderContext $ctx): bool;
}
```

```php
// src/Domain/Order/OrderContext.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class OrderContext
{
    public function __construct(
        public int $subtotal,
        public int $userId,
        public bool $isFirstOrder,
        public bool $isVip,
    ) {}
}
```

Обратите внимание: `isFirstOrder` и `isVip` — **данные**, а не вызов БД. Их вычисляет application-слой *до* вызова pricing. Домен не знает, откуда они.

### Шаг 2: Конкретные скидки — отдельные классы

```php
// src/Domain/Order/PercentageDiscount.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class PercentageDiscount implements Discount
{
    public function __construct(
        private readonly string $name,
        private readonly int    $percentOff,   // 20 = -20%
    ) {}

    public function apply(int $subtotal): int
    {
        return $subtotal - (int) ($subtotal * $this->percentOff / 100);
    }

    public function isApplicable(OrderContext $ctx): bool
    {
        return true; // процентная скидка всегда применима, если выбрана
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

```php
// src/Domain/Order/FixedDiscount.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class FixedDiscount implements Discount
{
    public function __construct(
        private readonly string $name,
        private readonly int    $amountOff,
    ) {}

    public function apply(int $subtotal): int
    {
        return max(0, $subtotal - $this->amountOff);
    }

    public function isApplicable(OrderContext $ctx): bool
    {
        return true;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

```php
// src/Domain/Order/ConditionalDiscount.php
declare(strict_types=1);

namespace App\Domain\Order;

use Closure;

final readonly class ConditionalDiscount implements Discount
{
    public function __construct(
        private readonly string    $name,
        private readonly Discount  $inner,
        private readonly Closure(OrderContext): bool $predicate,
    ) {}

    public function apply(int $subtotal): int
    {
        return $this->inner->apply($subtotal);
    }

    public function isApplicable(OrderContext $ctx): bool
    {
        return ($this->predicate)($ctx);
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

### Шаг 3: Реестр — расширение без изменения

```php
// src/Application/Order/DiscountRegistry.php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\Discount;
use App\Domain\Order\PercentageDiscount;
use App\Domain\Order\FixedDiscount;
use App\Domain\Order\ConditionalDiscount;

final class DiscountRegistry
{
    /** @var list<Discount> */
    private array $discounts = [];

    public function register(Discount $discount): void
    {
        $this->discounts[] = $discount;
    }

    /** @return list<Discount> */
    public function all(): array
    {
        return $this->discounts;
    }
}
```

### Шаг 4: Компоновка — DI-конфигурация, а не `if/elseif`

```php
// config/services_pricing.php  (Symfony DI)
use App\Domain\Order\PercentageDiscount;
use App\Domain\Order\FixedDiscount;
use App\Domain\Order\ConditionalDiscount;

return static function (ContainerConfigurator $c) {
    $services = $c->services();

    $services->set('discount.seasonal')
        ->class(PercentageDiscount::class)
        ->args(['SALE', 20]);

    $services->set('discount.birthday')
        ->class(PercentageDiscount::class)
        ->args(['BIRTHDAY', 30]);

    $services->set('discount.vip')
        ->class(ConditionalDiscount::class)
        ->args([
            'VIP',
            new FixedDiscount('VIP', 50000),
            static fn (OrderContext $ctx): bool => $ctx->isVip,
        ]);

    $services->set('discount.large_order')
        ->class(ConditionalDiscount::class)
        ->args([
            'LARGE',
            new PercentageDiscount('LARGE', 5),
            static fn (OrderContext $ctx): bool => $ctx->subtotal >= 1000000,
        ]);

    $services->set('discount.welcome')
        ->class(ConditionalDiscount::class)
        ->args([
            'WELCOME',
            new PercentageDiscount('WELCOME', 10),
            static fn (OrderContext $ctx): bool => $ctx->isFirstOrder,
        ]);
};
```

### Шаг 5: Pricing-сервис — тонкий, без бизнес-логики

```php
// src/Application/Order/OrderPricing.php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\Discount;
use App\Domain\Order\OrderContext;

final class OrderPricing
{
    /**
     * @param iterable<Discount> $discounts
     */
    public function __construct(
        private readonly iterable $discounts,
    ) {}

    public function calculateTotal(OrderContext $ctx): int
    {
        $total = $ctx->subtotal;

        foreach ($this->discounts as $discount) {
            if ($discount->isApplicable($ctx)) {
                $total = $discount->apply($total);
            }
        }

        return max(0, $total);
    }
}
```

### Шаг 6: Application-слой собирает контекст

```php
// src/Application/Order/PlaceOrderHandler.php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\OrderContext;

final class PlaceOrderHandler
{
    public function __construct(
        private readonly OrderPricing      $pricing,
        private readonly UserRepository    $users,
        private readonly PromoCodeResolver $promoResolver,  // строка → Discount
    ) {}

    public function handle(PlaceOrderCommand $cmd): PricedOrder
    {
        $subtotal = $this->sumItems($cmd->items);

        // Вычисляем данные ДО вызова домена. Домен не ходит в БД.
        $ctx = new OrderContext(
            subtotal:      $subtotal,
            userId:        $cmd->userId,
            isFirstOrder:  $this->users->isFirstOrder($cmd->userId),
            isVip:         $this->users->isVip($cmd->userId),
        );

        $total = $this->pricing->calculateTotal($ctx);

        return PricedOrder::withTotals($subtotal, $total);
    }

    /** @param list<Item> $items */
    private function sumItems(array $items): int
    {
        return array_sum(array_map(
            static fn (Item $i): int => $i->price * $i->qty,
            $items,
        ));
    }
}
```

---

## Что изменилось

| Было | Стало |
|---|---|
| 14 веток `if/elseif` в одной функции | N отдельных `Discount`-классов + реестр |
| Новый промокод = правка `calculateTotal()` | Новый промокод = новый `services_*` конфиг + 0 строк в `OrderPricing` |
| `isFirstOrder()` ходит в БД внутри pricing-сервиса | `PlaceOrderHandler` вычисляет данные, передаёт в `OrderContext` |
| `str_starts_with($promoCode, 'SALE_')` — парсинг строки | `PromoCodeResolver` мапит строку → конкретный `Discount`-объект |
| Тест на новый промокод = правка `OrderPricingTest` | Тест на новый промокод = новый `XxxDiscountTest` + 1 строка в реестр-тесте |

### Проверка OCP

**Закрывает ли изменение?** `OrderPricing::calculateTotal()` — не трогали. `Discount::apply()` — не трогали. `OrderContext` — не трогали.

**Открывает ли расширение?** Новый промокод «STUDENT_15%» — добавляем:

```php
$services->set('discount.student')
    ->class(PercentageDiscount::class)
    ->args(['STUDENT', 15]);
```

Ноль строк изменённого кода. Только новый файл конфига.

---

## Когда OCP — overkill

Честно: если промокодов **3–4** и они **не меняются** в течение всего жизненного цикла проекта — `if/elseif` допустим. OCP — не религия. Экстракция в интерфейс + реестр + DI оправдывает себя, когда:

- количество вариантов **растёт** (новый промокод каждый маркетинговый цикл);
- есть **несколько команд**, которые одновременно добавляют варианты;
- варианты имеют **разную логику** (не просто константа, а условие + расчёт).

Если это «всего 3 строки и не изменятся» — DRY не обязывает выносить. Но в e-commerce промокоды *всегда* меняются, и это то, что превращает 3 строки в 40 за полгода.

---

## Короткий чек-лист: «а не нарушен ли у меня OCP?»

1. Есть ли функция/класс, в который я **в прошлый раз** заходил, чтобы *добавить ветку*? → Вероятно, OCP нарушен.
2. Меняю ли я **существующий** код, чтобы поддержать **новый** сценарий? → Да = OCP нарушен.
3. Могу ли я добавить новый сценарий, **не трогая** ни одну существующую строку (кроме конфига/реестра)? → Нет = OCP нарушен.
4. Сколько человек одновременно правят одну и ту же функцию? > 1 = сигнал.

---

Если хочешь, следующим шагом могу показать, как этот же паттерн раскладывается на **LSP** (что происходит, если попытаться сделать `Discount` через наследование `class AbstractDiscount` с `final`-методами, и почему это ломает замещение) — это второй по частоте «брат» OCP-нарушений в PHP-коде.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Короткий ответ

Да. SOLID — это эвристики для *предсказания* трения, а не конвейерная нормативка. Принцип, который в конкретной ситуации увеличивает трение вместо того, чтобы его снижать, — нарушенный принцип, и это не слабость, а зрелость.

Ниже — два примера, где нарушение *лучше* соблюдения.

---

## Пример 1: LSP + `final` — «нарушение» как защита

LSP гласит: объекты подкласса должны быть заменяемы объектами суперкласса без изменения корректности программы. То есть если у меня `class Cat extends Animal`, то любой метод, принимающий `Animal`, должен корректно работать с `Cat`.

А теперь — реальная ситуация в e-commerce.

### «Правильный» LSP-код

```php
// domain/ShippingMethod.php
abstract class ShippingMethod
{
    abstract public function calculateCost(int $weightGrams): int;
    abstract public function estimateDays(): int;
    abstract public function label(): string;
}

// domain/StandardShipping.php
class StandardShipping extends ShippingMethod
{
    public function __construct(
        private readonly int $baseCost = 350,
        private readonly int $perKg = 40,
        private readonly int $days = 5,
    ) {}

    public function calculateCost(int $weightGrams): int
    {
        $kg = max(1, (int) ceil($weightGrams / 1000));
        return $this->baseCost + $kg * $this->perKg;
    }

    public function estimateDays(): int { return $this->days; }
    public function label(): string    { return 'Standard'; }
}

// domain/ExpressShipping.php
class ExpressShipping extends ShippingMethod
{
    public function calculateCost(int $weightGrams): int
    {
        $kg = max(1, (int) ceil($weightGrams / 1000));
        return 900 + $kg * 80;
    }

    public function estimateDays(): int { return 1; }
    public function label(): string    { return 'Express'; }
}
```

Кажется красиво. Абстракция, полиморфизм, LSP. А теперь — через 4 месяца кто-то:

```php
// domain/FreeShipping.php
class FreeShipping extends ShippingMethod
{
    public function calculateCost(int $weightGrams): int
    {
        return 0;
    }

    public function estimateDays(): int
    {
        // «А, ну тут дней нет, доставка бесплатная, пусть будет 0»
        return 0;
    }

    public function label(): string { return 'Free'; }
}
```

А теперь вызывающий код:

```php
function renderShipping(ShippingMethod $m): string
{
    return sprintf('%s — %d дн., %d ₽', $m->label(), $m->estimateDays(), $m->calculateCost(1500));
}

renderShipping(new FreeShipping());
// "Free — 0 дн., 0 ₽"   ← «0 дней» — это баг, но LSP его не видит
```

LSP *гарантирует*, что `FreeShipping` подставится в любое место, где ожидается `ShippingMethod`. И это **проблема**: `estimateDays()` для бесплатной доставки не имеет смысла, но LSP *заставляет* его существовать и возвращать что-то. Подкласс «подменяет» семантику, а не расширяет.

### Нарушение LSP: `final` + конкретный тип

```php
// domain/ShippingMethod.php
final readonly class ShippingMethod
{
    public function __construct(
        public string $label,
        public int    $baseCost,
        public int    $perKg,
        public int    $days,
    ) {}

    public function calculateCost(int $weightGrams): int
    {
        $kg = max(1, (int) ceil($weightGrams / 1000));
        return $this->baseCost + $kg * $this->perKg;
    }
}
```

```php
// domain/FreeShipping.php
final readonly class FreeShipping
{
    public function calculateCost(int $weightGrams): int
    {
        return 0;
    }

    // НЕТ estimateDays(). НЕТ label(). Нет абстракции, которую нужно «подменить».
}
```

```php
// application/ShippingCalculator.php
final class ShippingCalculator
{
    /** @param list<ShippingMethod|FreeShipping> $options */
    public function __construct(private array $options) {}

    public function cheapest(int $weightGrams): int
    {
        return min(array_map(
            static fn (object $m): int => $m->calculateCost($weightGrams),
            $this->options,
        ));
    }

    public function render(ShippingMethod|FreeShipping $m, int $weightGrams): string
    {
        return match (true) {
            $m instanceof FreeShipping => sprintf('Бесплатная доставка'),
            $m instanceof ShippingMethod => sprintf(
                '%s — %d дн., %d ₽',
                $m->label, $m->days, $m->calculateCost($weightGrams),
            ),
        };
    }
}
```

**Что нарушено?** LSP. `FreeShipping` не является `ShippingMethod`. Наследования нет. `render()` принимает union-тип, а не абстракцию.

**Почему это лучше?**

| | LSP-вариант | `final`-вариант |
|---|---|---|
| `FreeShipping::estimateDays()` | Должен существовать, возвращает бред (0) | Не существует. Не может быть вызван. |
| Новый тип доставки, где нет `days` | Добавляем `return 0;` в подкласс, LSP-контракт *заставляет* | Просто не добавляем поле. |
| `render()` | Должен работать с *любым* `ShippingMethod`, включая те, где `label()` = `null` | Явно перечисляет: либо `FreeShipping`, либо `ShippingMethod`. Стат-анализатор проверяет исчерпываемость. |
| Кто-то `new class extends FreeShipping` | LSP позволяет, и вдруг `calculateCost()` возвращает `string` | `final` — `extends` невозможен. |

`final` — это *утверждение*: «у этой сущности нет подклассов, и я не хочу, чтобы кто-то пытался их создать». LSP говорит «подкласс должен быть заменяем». `final` говорит «подкласса нет, и не будет». Это не противоречие LSP как концепции — это *осознанный отказ* от полиморфизма там, где он не несёт пользы.

### Когда это оправдано

- Набор вариантов **закрыт** и мал (2–5 штук).
- Варианты **семантически разные**, а не «вариации одного».
- Нет ожидания, что *внешние* пакеты/команды будут добавлять новые подклассы.
- Стат-анализатор (PHPStan 9+) умеет проверять исчерпываемость union-типов.

### Когда это *не* оправдано

- Вариантов 10+, и новые появляются каждый спринт → нужен полиморфизм.
- Разные команды расширяют набор → нужна общая точка расширения (интерфейс/абстракция).
- `render()` растёт до `match` на 12 веток → это уже `if/elseif` в маске union-типа, и OCP снова просит вынести в стратегию.

---

## Пример 2: SRP — микро-выделение, которое *ухудшает*

SRP: «у класса должна быть одна причина для изменения». Звучит безупречно. А теперь — реальный PR.

### «Чистое» SRP-выделение

Задача: при создании заказа проверить, что email валиден, и что телефон не дублируется.

```php
// src/Domain/Customer/Email.php
final readonly class Email
{
    public function __construct(public string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail($value);
        }
    }
}

// src/Domain/Customer/Phone.php
final readonly class Phone
{
    public function __construct(public string $value)
    {
        if (strlen($value) < 10 || strlen($value) > 15) {
            throw new InvalidPhone($value);
        }
    }
}

// src/Domain/Customer/EmailValidator.php
final class EmailValidator
{
    public function validate(Email $email): void
    {
        // Email уже валиден по конструктору. Что тут делать?
        // ... пустой метод, оставлен «на будущее».
    }
}

// src/Domain/Customer/PhoneUniquenessChecker.php
final class PhoneUniquenessChecker
{
    public function __construct(private CustomerRepository $repo) {}

    public function check(Phone $phone): void
    {
        if ($this->repo->findByPhone($phone)) {
            throw new DuplicatePhone($phone->value);
        }
    }
}

// src/Application/Customer/RegisterHandler.php
final class RegisterHandler
{
    public function __construct(
        private EmailValidator          $emailValidator,
        private PhoneUniquenessChecker  $phoneChecker,
        private CustomerRepository      $repo,
    ) {}

    public function handle(RegisterCommand $cmd): CustomerId
    {
        $email = new Email($cmd->email);       // валидация #1
        $phone = new Phone($cmd->phone);       // валидация #2
        $this->emailValidator->validate($email); // валидация #3 (пустая)
        $this->phoneChecker->check($phone);      // проверка уникальности
        $customer = Customer::create($email, $phone, $cmd->name);
        $this->repo->save($customer);
        return $customer->id;
    }
}
```

Четыре файла для **двух** валидаций. `EmailValidator` — пустой. `Email` и `Phone` — по сути конструкторы с одной строкой проверки. `PhoneUniquenessChecker` — это метод репозитория с лишней обёрткой.

### Нарушение SRP: один класс, одна ответственность — «создать клиента»

```php
// src/Domain/Customer/Customer.php
final class Customer
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
    ) {}

    public static function create(
        string $name,
        string $email,
        string $phone,
        CustomerRepository $repo,   // нужен для проверки уникальности
    ): self {
        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail($email);
        }
        // Валидация телефона
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            throw new InvalidPhone($phone);
        }
        // Уникальность телефона
        if ($repo->findByPhone($phone) !== null) {
            throw new DuplicatePhone($phone);
        }

        return new self(
            id:    (string) \Symfony\Component\Uid\Uuid::v4(),
            name:  $name,
            email: $email,
            phone: $phone,
        );
    }
}
```

```php
// src/Application/Customer/RegisterHandler.php
final class RegisterHandler
{
    public function __construct(private CustomerRepository $repo) {}

    public function handle(RegisterCommand $cmd): CustomerId
    {
        $customer = Customer::create($cmd->name, $cmd->email, $cmd->phone, $this->repo);
        $this->repo->save($customer);
        return $customer->id;
    }
}
```

**Что нарушено?** SRP. `Customer::create()` делает валидацию email, телефона, уникальность, генерацию ID. «Много причин для изменения».

**Почему это лучше?**

- **Читаемость.** Один метод, 20 строк, читается сверху вниз. Чтобы понять, что происходит при регистрации, нужно открыть *один* файл, а не четыре.
- **Отсутствие «пустых» сущностей.** `EmailValidator` с пустым методом — это не SRP, это *артефакт*. Класс, который ничего не делает, — это не «отдельная ответственность», это *отсутствие* ответственности.
- **Тестирование.** Тест на «регистрация отклоняет дубликат телефона» — один тест на `Customer::create()`. В SRP-варианте — три теста на три класса, и при этом *интеграционный* сценарий (все три вместе) не покрыт ни одним из них.
- **Меньше имён.** `Email`, `Phone`, `EmailValidator`, `PhoneUniquenessChecker`, `Customer`, `RegisterHandler` — 6 имён. Во втором варианте — `Customer`, `RegisterHandler` — 2.

### Когда SRP-выделение оправдано

- `Email` используется в 12 местах (email-уведомления, поиск, аудит) — тогда `Email` как Value Object имеет смысл.
- Валидация телефона зависит от страны, и есть 5 реализаций — тогда `PhoneValidator` с DI оправдан.
- Уникальность проверяется по *нескольким* правилам (телефон + email + логин) — тогда `UniquenessChecker` с коллекцией правил имеет смысл.

Но в ситуации «два поля, две проверки, один сценарий» — выделение в 4 класса ради формального SRP *увеличивает* трение, а не снижает.

---

## Критерий: нарушать или нет

Вопрос не «нарушен ли принцип?», а:

> **Если я нарушу принцип, станет ли код *проще читать, тестировать и менять*?**

| Признак, что нарушение оправдано | Признак, что принцип надо соблюдать |
|---|---|
| Вариантов ≤ 4 и они семантически разные | Варианты растут, новые появляются часто |
| Один сценарий, один вызывающий код | Несколько сценариев, разные команды |
| «Чистый» принцип создаёт пустые/тривиальные классы | «Чистый» принцип устраняет дублирование логики |
| `final` защищает от неправильного наследования | Абстракция даёт реальную точку расширения |
| 20 строк в одном методе читаются за 10 секунд | 20 строк в 4 файлах читаются за 2 минуты |

---

## Главное

SOLID — это не «законы природы». Это 50 лет коллективного опыта, упакованного в мнемонику. Принцип, который в вашей ситуации заставляет писать *больше* кода, *больше* имён, *больше* файлов для того же результата — это не «ваша вина». Это признак того, что эвристика не применима к конкретной задаче.

Нарушать можно. Но **осознанно**: «я вижу, что SRP тут создаёт 4 пустых класса, и я намеренно оставляю валидацию в `create()`, потому что …». Это уже не нарушение — это **решение**.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## OCP = «расширить без изменения»

Наследование — самый известный способ, но не единственный. Каждый из нижеперечисленных даёт ту же гарантию: новый сценарий появляется как *добавление*, а не *правка* существующего кода. Разница — в механике, гибкости и цене.

---

## 1. Composition + Strategy (без `extends`)

Уже показывал с `Discount`. Ключевой момент: **интерфейс + состав**, а не иерархия.

```php
interface TaxStrategy
{
    public function calc(int $amount): int;
}

final class Vat20 implements TaxStrategy
{
    public function calc(int $amount): int {
        return (int) ($amount * 0.20);
    }
}

// Новый налог — новый файл, ноль строк изменённых
final class CarbonTax implements TaxStrategy
{
    public function __construct(private readonly int $ratePerTon) {}

    public function calc(int $amount): int {
        return (int) round($amount * 0.005 * $this->ratePerTon / 1000);
    }
}
```

Потребитель принимает `iterable<TaxStrategy>` или `TaxStrategy` — не знает и не должен знать конкретных классов.

**Чем отличается от наследования:** нет `abstract class` → нет проблемы «подкласс обязан реализовать все методы». Нет `final`-защиты от «случайного» наследования. Состав — это *выбор*, а не *обязательство*.

---

## 2. Registry / DI-driven

Поведение определяется **конфигурацией**, а не кодом вызывающего модуля.

```php
// domain/Discount.php
interface Discount {
    public function apply(int $total): int;
}

// application/DiscountRegistry.php
final class DiscountRegistry
{
    /** @var array<string, Discount> */
    private array $byName = [];

    public function register(string $name, Discount $d): void
    {
        $this->byName[$name] = $d;
    }

    public function get(string $name): Discount
    {
        return $this->byName[$name]
            ?? throw new UnknownDiscount($name);
    }

    /** @return list<Discount> */
    public function all(): array
    {
        return array_values($this->byName);
    }
}
```

```php
// config/services.php — Symfony DI
return static function (ContainerConfigurator $c) {
    $services = $c->services();

    $services->set('discount.seasonal')
        ->class(PercentageDiscount::class)->args([20]);

    $services->set('discount.vip')
        ->class(FixedDiscount::class)->args([50000]);

    // Новый промокод = 3 строки конфига. Ноль строк PHP-кода изменено.
    $services->set('discount.student')
        ->class(PercentageDiscount::class)->args([15]);
};
```

```php
// application/OrderPricing.php
final class OrderPricing
{
    public function __construct(
        /** @param iterable<Discount> $discounts */
        private readonly iterable $discounts,
    ) {}

    public function total(int $subtotal): int
    {
        foreach ($this->discounts as $d) {
            $subtotal = $d->apply($subtotal);
        }
        return max(0, $subtotal);
    }
}
```

**OCP:** `OrderPricing` не изменён. `Discount`-интерфейс не изменён. Новый сценарий = новый класс + 3 строки конфига.

**Чем отличается от наследования:** нет `abstract`-базы. Нет `super::`. Расширение определяется *вне* класса — в DI-контейнере. Это делает набор вариантов **видимым** в одном месте (конфиг), а не размазанным по `extends`.

---

## 3. Pipeline / middleware chain

Поведение = последовательность шагов. Новый шаг = новый элемент в цепочке.

```php
// application/Shipping/Pipeline.php
final class ShippingPipeline
{
    /** @param list\callable(int): int $steps */
    private array $steps = [];

    public function add(callable $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    public function run(int $baseCost): int
    {
        foreach ($this->steps as $step) {
            $baseCost = $step($baseCost);
        }
        return $baseCost;
    }
}
```

```php
// config/shipping.php
use App\Application\Shipping\ShippingPipeline;

return static function (ContainerConfigurator $c) {
    $c->services()
      ->set('shipping.pipeline', ShippingPipeline::class)
      ->call('add', [static fn (int $cost): int => $cost + 100])        // базовая
      ->call('add', [static fn (int $cost): int => (int)($cost * 1.2)]) // НДС
      ->call('add', [static fn (int $cost): int => min($cost, 5000)]);  // cap

    // Новый шаг:
    // ->call('add', [static fn (int $cost): int => $cost + 50]);
};
```

**OCP:** `ShippingPipeline::run()` не изменён. Новый шаг = одна `->call()`. Порядок — явный, в конфиге.

**Когда лучше наследования:** шаги **не связаны** между собой, каждый работает с «прошлым результатом». Нет состояния, нет «this». Тривиально тестируется: каждый `fn` — чистая функция.

**Ограничение:** если шаг зависит от *контекста* (не только от `int`), closure становится громоздкой. Тогда — вариант 1 (Strategy с контекстом).

---

## 4. Event-driven (hooks)

Поведение расширяется **после** основного события, а не внутри.

```php
// domain/Order/OrderPlaced.php
final readonly class OrderPlaced
{
    public function __construct(
        public readonly string $orderId,
        public readonly int    $total,
    ) {}
}
```

```php
// application/OrderDispatcher.php
final class OrderDispatcher
{
    /** @var list\callable(OrderPlaced): void */
    private array $listeners = [];

    public function subscribe(callable(OrderPlaced): void $listener): void
    {
        $this->listeners[] = $listener;
    }

    public function dispatch(OrderPlaced $event): void
    {
        foreach ($this->listeners as $listener) {
            $listener($event);
        }
    }
}
```

```php
// config/events.php
return static function (ContainerConfigurator $c) {
    $dispatcher = $c->services()->get('order.dispatcher');

    // Существующие подписчики
    $dispatcher->call('subscribe', [
        static fn (OrderPlaced $e) => \app\logging($e->orderId),
    ]);
    $dispatcher->call('subscribe', [
        static fn (OrderPlaced $e) => \app\sendEmail($e),
    ]);

    // Новый сценарий: уведомление в Slack. Ноль строк в OrderDispatcher.
    $dispatcher->call('subscribe', [
        static fn (OrderPlaced $e) => \app\slackNotify("Order {$e->orderId} — {$e->total} ₽"),
    ]);
};
```

**OCP:** `OrderDispatcher::dispatch()` не изменён. Новое поведение = новый `subscribe`.

**Чем отличается от наследования:**
- Нет общего базового класса. Слушатели не знают друг о друге.
- Порядок — по `subscribe`, не по MRO.
- Лёгкое отключение (не вызывать `subscribe`), а не «переопределить метод в подклассе».
- Идеально для *side effects* (лог, уведомления, аналитика), плохо для *расчёта* (каждый слушатель не видит результат предыдущего).

---

## 5. Data-driven (правила как данные)

Поведение определяется **таблицей**, а не ветками кода.

```php
// domain/TaxRule.php
final readonly class TaxRule
{
    public function __construct(
        public readonly string $country,
        public readonly int    $rateBps,     // базисные пункты: 2000 = 20%
        public readonly int    $threshold,   // порог, от которого применяется
    ) {}
}

// data/tax_rules.php — чистые данные
return [
    new TaxRule('DE', 1900, 0),
    new TaxRule('FR', 2000, 0),
    new TaxRule('US', 800,  50000),
    // Новая страна = одна строка. Ноль PHP-логики.
    new TaxRule('JP', 1000, 0),
];
```

```php
// domain/TaxCalculator.php
final class TaxCalculator
{
    /** @param list<TaxRule> $rules */
    public function __construct(private readonly array $rules) {}

    public function tax(string $country, int $amount): int
    {
        $rule = $this->rulesFor($country)
            ?? throw new UnsupportedCountry($country);

        if ($amount < $rule->threshold) {
            return 0;
        }

        return (int) round($amount * $rule->rateBps / 10000);
    }

    /** @return TaxRule|null */
    private function rulesFor(string $country): ?TaxRule
    {
        foreach ($this->rules as $r) {
            if ($r->country === $country) {
                return $r;
            }
        }
        return null;
    }
}
```

**OCP:** `TaxCalculator` не изменён. Новая страна = одна строка в данных.

**Когда лучше наследования:** набор правил **закрыт и стабилен**, различия — в **константах**, а не в **логике**. Если для `JP` нужна особая логика (не просто ставка, а «если сумма > X, то Y») — данные не выражают, и нужен Strategy (вариант 1).

---

## 6. Plugin discovery (autoload-based)

Расширение определяется **наличием файла**. Контейнер находит реализации сам.

```php
// interface: PaymentGateway.php
interface PaymentGateway {
    public function name(): string;
    public function charge(ChargeRequest $r): ChargeResult;
}
```

```php
// infrastructure/StripeGateway.php  — существует, значит подключён
final class StripeGateway implements PaymentGateway { /* ... */ }

// infrastructure/PayPalGateway.php  — существует, значит подключён
final class PayPalGateway implements PaymentGateway { /* ... */ }
```

```php
// bootstrap/plugins.php — автообнаружение
use App\PaymentGateway;

function discoverGateways(string $dir): array
{
    $gateways = [];
    foreach (glob("$dir/*Gateway.php") as $file) {
        $class = 'App\\' . basename($file, '.php');
        if (class_exists($class) && is_a($class, PaymentGateway::class, true)) {
            $gateways[] = new $class();
        }
    }
    return $gateways;
}
```

```php
// application/PaymentRouter.php
final class PaymentRouter
{
    /** @param list<PaymentGateway> $gateways */
    public function __construct(private readonly array $gateways) {}

    public function charge(string $provider, ChargeRequest $r): ChargeResult
    {
        $gw = $this->gateways[$provider]
            ?? throw new UnknownProvider($provider);

        return $gw->charge($r);
    }
}
```

**OCP:** Новый гейтвей = новый файл `XxxGateway.php`. `PaymentRouter` не трогали. Обнаружение — по автозагрузке, не по хардкоду.

**Чем отличается от DI-конфига:** нет явного «списка» в конфиге. Набор определяется *файловой системой*. Плюсы: «положил файл — заработало». Минусы: неочевидно, *какие* именно гейтвеи активны; трудно отключить один, не удаляя файл.

**Когда лучше:** микросервисы, где каждый сервис «вешает» свой гейтвей, и центральный сервис не знает набора заранее.

---

## 7. Trait-based composition (PHP-специфично)

Не наследование, а **миксинг**. Класс собирает поведение из независимых черт.

```php
trait Auditable
{
    protected array $auditLog = [];

    protected function audit(string $action): void
    {
        $this->auditLog[] = ['at' => time(), 'action' => $action];
    }
}

trait Retryable
{
    private int $maxRetries = 3;

    public function withRetries(int $n): self
    {
        $this->maxRetries = $n;
        return $this;
    }

    protected function withRetry(callable $fn): mixed
    {
        for ($i = 0; $i < $this->maxRetries; $i++) {
            try {
                return $fn();
            } catch (\RuntimeException $e) {
                if ($i === $this->maxRetries - 1) throw $e;
                usleep(100_000 * ($i + 1));
            }
        }
        throw new Unreachable();
    }
}

// Композиция: Order получает оба поведения без иерархии
final class Order
{
    use Auditable, Retryable;

    public function place(): void
    {
        $this->withRetry(function () {
            $this->audit('place');
            // ...
        });
    }
}
```

**OCP:** Новое поведение (например, `Cacheable`) = новый trait + `use Cacheable` в классе. Нет `extends`. Нет MRO. Нет «подкласс обязан переопределить».

**Ограничение:** traits в PHP не имеют полей-инициализации, не могут иметь конструкторов. Конфликты методов разрешаются `insteadof`/`as`. Это *внутренняя* композиция, а не *внешнее* расширение — новый trait требует правки класса, который его использует. То есть OCP здесь **частичный**: поведение расширяется, но *применение* этого поведения — нет.

---

## Сравнение

| Способ | Расширение = | Тестируемость | Видимость набора | Когда лучше всего |
|---|---|---|---|---|
| **Strategy + DI** | Новый класс + конфиг | Высокая (изолированный класс) | В конфиге DI | 3–20 вариантов, разная логика |
| **Pipeline** | `->call(fn)` | Высокая (чистая fn) | В конфиге | Независимые шаги, конвейер |
| **Event / hooks** | `subscribe(fn)` | Высокая (изолированный listener) | В конфиге | Side effects, не расчёт |
| **Data-driven** | Строка в массиве | Высокая (таблица данных) | В данных | Стабильный набор, различия в константах |
| **Plugin discovery** | Новый файл | Средняя (нужен интеграционный тест) | В файловой системе | Микросервисы, «положил — заработало» |
| **Traits** | Новый trait + `use` | Средняя | В коде класса | Внутренняя композиция поведения |
| **Наследование** | Новый подкласс | Средняя (MRO-сложности) | В иерархии классов | Глубокая иерархия, 5+ уровней |

---

## Главное

OCP — это **контракт**: «я не буду менять *этот* код, чтобы добавить *то* поведение». Как именно реализуется «добавить» — не важно. Наследование, состав, данные, события, файлы — все работают. Выбор зависит от:

- **Сколько** вариантов и **как часто** растут.
- **Что** различается: константы (данные), логика (Strategy), порядок (Pipeline), side effects (Events).
- **Кто** расширяет: та же команда (DI-конфиг) или внешние сервисы (plugin discovery).
- **Нужна ли** видимость набора в одном месте.

Наследование — один из инструментов, и часто не самый подходящий.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — про *единицу абстракции*, а не про класс

«Класс» — просто то, как PHP оформляет единицу. Принцип живёт ниже: на уровне **функции, модуля, таблицы, компонента, сервиса**. Критерий один и тот же:

> **Есть ли у этого *две независимые причины для изменения*?**

«Ответственность» ≠ «функция в бытовом смысле». Это *причина, по которой код поменяют*. Если причина A и причина B независимы (меняются в разное время, по разным требованиям, разными командами) — это две ответственности, даже если они лежат в одном методе.

---

## На уровне функции

```php
// Одна функция, две причины для изменения
function saveOrder(Order $order): void
{
    // Причина 1: правила валидации меняются (маркетинг, комплаенс, аудит)
    if ($order->total() < 1000) throw new MinOrder();
    if (!in_array($order->country, ALLOWED)) throw new UnsupportedCountry();
    if ($order->items === []) throw new EmptyOrder();

    // Причина 2: способ хранения меняется (Postgres → CockroachDB, добавление idempotency key)
    $db->insert('orders', [
        'uuid'    => $order->id(),
        'total'   => $order->total(),
        'country' => $order->country,
        'items'   => json_encode($order->items),
        'created' => time(),
    ]);
}
```

Две независимые причины:
- Маркетинг говорит: «минимальный заказ — 500 ₽» → правим валидацию.
- Архитектор говорит: «переезжаем на CockroachDB» → правим persist.

Они не связаны. Если валидация меняется, persist не трогается. Если persist меняется, валидация не трогается. Значит — **две** ответственности в одной функции.

```php
// Разделено
function validate(Order $order): void
{
    if ($order->total() < 1000) throw new MinOrder();
    if (!in_array($order->country, ALLOWED)) throw new UnsupportedCountry();
    if ($order->items === []) throw new EmptyOrder();
}

function persist(Order $order): void
{
    $db->insert('orders', [
        'uuid'    => $order->id(),
        'total'   => $order->total(),
        'country' => $order->country,
        'items'   => json_encode($order->items),
        'created' => time(),
    ]);
}

// Вызывающий код
function saveOrder(Order $order): void
{
    validate($order);
    persist($order);
}
```

Теперь «минимальный заказ → 500» = правка *одной* функции. «Переезд на CockroachDB» = правка *другой*. Они не пересекаются.

---

## На уровне файла / модуля

В PHP «файл = класс», но это не закон. Иногда один файл несёт две ответственности:

```php
// src/Service/EmailService.php — один файл, две причины
final class EmailService
{
    // Причина 1: шаблон письма меняется (дизайн, локализация, A/B)
    public function renderTemplate(string $type, array $vars): string
    {
        return match ($type) {
            'welcome'  => "Здравствуйте, {$vars['name']}! Добро пожаловать...",
            'invoice'  => "Ваш счёт #{$vars['id']} на сумму {$vars['sum']}...",
            'remind'   => "Напоминание: ваш заказ #{$vars['id']} не оплачен...",
            // ... 8 шаблонов, каждый — свой
        };
    }

    // Причина 2: транспорт меняется (SMTP → SES → Mailgun)
    public function send(string $to, string $subject, string $body): void
    {
        $this->smtp->send($to, $subject, $body);
    }
}
```

Шаблон меняется дизайнерами. Транспорт меняется инфраструктурой. Независимые причины.

```php
// src/Service/EmailRenderer.php — только шаблоны
final class EmailRenderer
{
    public function render(string $type, array $vars): RenderedEmail { /* ... */ }
}

// src/Service/EmailSender.php — только транспорт
final class EmailSender
{
    public function __construct(private MailTransport $transport) {}
    public function send(RenderedEmail $email): void { /* ... */ }
}
```

Теперь «поменяли шаблон welcome» → правка *только* `EmailRenderer`. «Миграция на Mailgun» → правка *только* `EmailSender`.

---

## На уровне таблицы БД

```sql
-- Одна таблица, две ответственности
CREATE TABLE users (
    id           UUID PRIMARY KEY,
    -- Причина 1: профиль пользователя (менится с UI, локализацией)
    name         VARCHAR(100),
    avatar_url   TEXT,
    bio          TEXT,
    locale       CHAR(5),
    -- Причина 2: аутентификация (менится с изменением security-политики)
    password_hash VARCHAR(255),
    mfa_secret   TEXT,
    mfa_enabled  BOOLEAN DEFAULT false,
    last_login   TIMESTAMPTZ
);
```

«Добавили поле `bio`» → миграция на `users`.
«Переходим на Argon2id + TOTP» → миграция на `users`.

Две независимые причины менять *одну* таблицу. Блокировки, permissions, backup-политика — всё связано.

```sql
-- Разделение
CREATE TABLE user_profiles (
    user_id      UUID PRIMARY KEY REFERENCES users(id),
    name         VARCHAR(100),
    avatar_url   TEXT,
    bio          TEXT,
    locale       CHAR(5)
);

CREATE TABLE user_credentials (
    user_id         UUID PRIMARY KEY REFERENCES users(id),
    password_hash   VARCHAR(255),
    mfa_secret      TEXT,
    mfa_enabled     BOOLEAN DEFAULT false,
    last_login      TIMESTAMPTZ
);
```

Теперь «добавили `bio`» → миграция `user_profiles`. «Argon2id» → миграция `user_credentials`. Разные таблицы, разные бэкапы, разные права.

---

## На уровне UI-компонента

```tsx
// Один компонент, две ответственности
function CheckoutForm() {
    // Причина 1: валидация полей (меняет фронтенд-команда)
    const errors = validateForm(values);

    // Причина 2: логика расчёта итога (меняет бэкенд / бизнес)
    const total = calculateTotal(items, promoCode, taxRate);

    return (
        <form onSubmit={handleSubmit}>
            {/* рендер полей */}
            <TotalDisplay total={total} />
        </form>
    );
}
```

Валидация формы меняется, когда дизайнер поправляет placeholder. Расчёт итога меняется, когда бухгалтер правит формулу НДС. Независимые команды, независимые причины.

---

## На уровне микросервиса

```
order-service:
  - принимает заказы          ← причина 1 (бизнес-логика заказов)
  - считает налоги            ← причина 2 (налоговая политика, меняет финотдел)
  - шлёт уведомления          ← причина 3 (маркетинг, каналы, A/B)
```

«Новая страна, новый налог» → правим `order-service`.
«Новый канал: push вместо email» → правим `order-service`.

Две независимые причины менять один сервис. Разные SLA, разные команды, разные депли.

```
order-service:   принимает заказы
tax-service:     считает налоги
notify-service:  шлёт уведомления
```

---

## Критерий: «что изменится, если …?»

Задайте вопрос к любой единице (функция, класс, файл, таблица, сервис, компонент):

> **Если поменяется *X*, я буду править *этот* код?**
> **Если поменяется *Y*, я буду править *этот* код?**

Если ответ «да» на два независимых *X* и *Y* — это **две** ответственности.

| Единица | Изменится X | Изменится Y | Вердикт |
|---|---|---|---|
| `saveOrder()` | правила валидации | способ persist | 2 ответственности |
| `EmailService` | шаблоны | транспорт | 2 ответственности |
| `users` (таблица) | профиль | аутентификация | 2 ответственности |
| `CheckoutForm` | валидация | расчёт итога | 2 ответственности |
| `order-service` | бизнес-логика | налоговый расчёт | 2+ ответственности |

---

## Честные границы

**SRP не означает «один метод = одна строка».** Метод `calculateTotal()` может быть на 30 строк и иметь *одну* причину для изменения (формула расчёта). Это SRP-совместимо.

**SRP не означает «один класс = одна функция».** Класс `Order` может иметь `place()`, `cancel()`, `ship()` — три действия, но **одну** причину: «жизненный цикл заказа». Это одна ответственность.

**Granularity — это вопрос баланса.** Слишком мелкое разделение («каждая операция — свой класс») — это *fragmentation*, не SRP. Слишком грубое («один класс на сервис») — нарушение SRP. Правильная гранулярность — там, где *независимые причины изменения* разделены.

---

## Итого

SRP — не про классы. Не про методы. Не про таблицы. Он про **причину для изменения**, и применяется к любой единице, которая может *меняться*: функция, класс, файл, модуль, таблица, UI-компонент, сервис, микросервис. Критерий один: **две независимые причины — две единицы.**

> ⏱ 33.7s · prompt 10269 tok · output 4660 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про DI-контейнер Symfony: компиляция, autowiring, lazy loading. Нужно объяснить архитектурно, но конкретно — с механикой, а не маркетингом. Структура:

1. Общая картина: два этапа — build-time (компиляция) и runtime (запрос).
2. Build-time: как Symfony собирает граф сервисов из PHP-конфига + annotations + autowiring. Что делает ContainerBuilder. Пасы, компиляция в PHP-класс.
3. Autowiring: как тип-хинт → сервис. Пасы, которые это обеспечивают. Ограничения.
4. Runtime: что происходит при get(). Лазийные прокси. Scope (singleton, request, prototype). Утилизация.
5. Ловушки и нюансы, которые на практике выстреливают.

Нужно быть конкретным: имена классов, пумы, что реально происходит в байткоде. Не абстрактно.

Ключевые моменты:
- ContainerBuilder → ContainerPassManager → пумы (RegisterServiceSubscribersPass, AutowirePass, RegisterFQCNAliasesPass и т.д.)
- Компилится в PHP-класс (App_KernelDevDebugContainer), где каждый сервис — метод getFooService()
- Autowiring: тип-хинт в конструкторе → alias на FQCN → поиск по алиасам. Если несколько кандидатов — needTag / $autowiringTypes.
- Лазийность: ProxyManager / native (8.2+) — createLazyService возвращает прокси, который лениво вызывает getFooService()
- Scope: singleton (по умолчанию), request, prototype. Утилизация — reverse-порядок создания.
- Публичные vs private сервисы. Private — не доступны через $container->get(), только через инъект.
- Autowiring не работает для: интерфейсов без одного implementations, циклических зависимостей, services, зарегистрированных через find() / tag-подхода.
- what about `!tagged_iterator` — итерация по тегам.
- what about autowiring_types в yaml.

Структурирую по этапам: 1) декларация, 2) компиляция, 3) runtime. Плюс ловушки.

Язык — русский, как и весь диалог.

</details>

**Кандидат:**

## Общая картина

DI-контейнер Symfony — это **два отдельных мира**, которые связывает сгенерированный PHP-класс:

```
┌─────────────────────────────────────────────────────────────────────────┐
│  BUILD TIME  (composer run / cache:clear / dev-перезагрузка)            │
│                                                                         │
│  YAML / XML / PHP-конфиг  +  attribute/annotation  +  AutowirePass      │
│         │                                                               │
│         ▼                                                               │
│  ContainerBuilder  (граф сервисов, пумы)                               │
│         │                                                               │
│         ▼                                                               │
│  Сгенерированный PHP-класс:  App_KernelDevDebugContainer               │
│         │                                                               │
│         ▼                                                               │
│  Кэшируется в var/cache/dev/                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│  RUNTIME  (каждый request)                                             │
│                                                                         │
│  $container->get('app.my_service')                                     │
│         │                                                               │
│         ▼                                                               │
│  App_KernelDevDebugContainer::getMyService()                           │
│         │                                                               │
│         ├── lazy?  → Proxy, который лениво вызывает getMyService()     │
│         ├── singleton? → кэш в $this->services['my_service']           │
│         └── prototype? → new каждый раз                                │
└─────────────────────────────────────────────────────────────────────────┘
```

Ничего «магического» в рантайме нет. Контейнер — это **объект с предсказуемыми методами**, сгенерированный на этапе сборки.

---

## Этап 1: декларация

Вы пишете конфиг. Три эквивалентных способа:

```yaml
# config/services.yaml
services:
  App\Service\OrderService:
    autowire: true
    autoconfigure: true

  App\Payment\PaymentGateway:
    class: App\Payment\StripeGateway   # реализация
    autowire: true

  App\Payment\PaymentGateway:    # алиас (FQCN → конкретный класс)
    public: true
```

```php
// config/services.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c) {
    $s = $c->services();

    $s->set(OrderService::class)
      ->autowire()
      ->autoconfigure();

    $s->set(PaymentGateway::class)   // интерфейс
      ->class(StripeGateway::class)  // конкретная реализация
      ->autowire();
};
```

```php
// Альтернатива: attribute на классе (Symfony 6.1+)
#[AsService]
final class OrderService
{
    public function __construct(
        private readonly OrderRepository $repo,   // ← autowire по типу
        private readonly EventDispatcherInterface $dispatcher,
    ) {}
}
```

На этом этапе Symfony ещё **не знает**, какие конкретно сервисы нужны. Он видит: «есть `OrderService`, есть `PaymentGateway` → `StripeGateway`».

---

## Этап 2: компиляция (ContainerBuilder + пумы)

Когда вы запускаете `bin/console cache:clear` или dev-режим подхватывает изменения конфига, происходит:

### 2.1. Сборка графа

`ContainerBuilder` создаёт **граф**: узлы = сервисы, рёбра = зависимости. Каждый сервис описывается как:

```php
// Внутренне в ContainerBuilder (упрощённо):
[
    'id'        => 'App\Service\OrderService',
    'class'     => 'App\Service\OrderService',
    'arguments' => [/* пока пусто, заполнит AutowirePass */],
    'public'    => false,
    'shared'    => true,   // singleton
    'tags'      => [],
    'autowired' => true,
    'autoconfigured' => true,
]
```

### 2.2. Пасы (CompilerPass)

`ContainerPassManager` применяет пумы **в строгом порядке**. Каждая пума может добавлять, удалять, менять сервисы. Ключевые:

| Пасс | Что делает |
|---|---|
| `RegisterFQCNAliasesPass` | Для каждого класса создаёт алиас `FQCN → FQCN` (чтобы autowiring работал «из коробки») |
| `AutowirePass` | **Главный**. Смотрит на `__construct` каждого сервиса, берёт тип-хинты, находит соответствующие сервисы по FQCN или алиасу, подставляет их в `arguments` |
| `AutoconfigurePass` | Регистрирует «стандартные» теги: `Command` → `console.command`, `EventSubscriber` → `kernel.event_subscriber`, `FormType` → `form.type`, и т.д. |
| `RegisterServiceSubscribersPass` | Обрабатывает `!tagged_iterator` / `!tagged_locator` — «все сервисы с тегом X» |
| `ResolveChildDefinitionsPass` | Инлайнит `parent` в определение |
| `CheckDefinitionValidityPass` | Валидирует: есть ли класс, доступны ли аргументы |
| `InlineServiceDefinitionsPass` | **Инлайнит** private-сервисы: если сервис используется один раз и не public — его определение встраивается прямо в метод, который его создаёт. Меньше индирекций. |
| `ResolveBindingsPass` | Обрабатывает `!bind` (глобальные привязки аргументов) |
| `RemoveUnusedDefinitionsPass` | **Удаляет** сервисы, которые никто не использует (если private). В prod-контейнере их может быть 2–5 раз меньше, чем в dev. |
| `AnalyzeServiceReferencesPass` | Строит финальный граф зависимостей, определяет порядок создания и утилизации |

### 2.3. Генерация PHP-класса

После всех пум `ContainerBuilder` генерирует **один PHP-класс** (название зависит от kernel + env):

```php
// var/cache/dev/App_KernelDevDebugContainer.php  (упрощённый фрагмент)
class App_KernelDevDebugContainer extends Container
{
    // --- Служебные ---
    private $privates = [];
    private $services = [];
    private $parameters = [];
    private $factories = [];

    // --- Сервисы (singleton) ---
    protected function getOrderService(): \App\Service\OrderService
    {
        return $this->services['app.service.orderservice'] ??= new \App\Service\OrderService(
            $this->getOrderRepository(),                          // ← зависимость
            $this->getEventDispatcher(),                          // ← зависимость
        );
    }

    // --- Лазийный (если прокси-провайдер) ---
    protected function getLazyPaymentGateway(): \App\Payment\PaymentGateway
    {
        return $this->services['app.payment.gateway'] ??= (static function (): \App\Payment\PaymentGateway {
            // Лазийное создание: не создаём, пока не вызовут метод
            $container = $this;
            return new \App\Payment\StripeGateway(
                $container->getHttpClient(),
                $container->getParameter('stripe_api_key'),
            );
        })();
    }

    // --- Prototype (новый на каждый get) ---
    protected function getPrototypeLogger(): \App\Log\Logger
    {
        return new \App\Log\Logger();   // без ??= — каждый раз новый
    }

    // --- Утилизация (обратный порядок создания) ---
    public function reset(): void
    {
        $this->privates = [];
        // ... освобождение ресурсов в обратном порядке
    }
}
```

**Ключевые наблюдения:**

- **Каждый сервис = один `protected` метод.** Нет `switch`, нет отражения, нет `new $class(...)`. Статический вызов.
- **Singleton** = `??=` (null coalescing assignment). Первый `get()` создаёт, последующие — из кэша.
- **Lazy** = closure, которая вызывается лениво. Прокси (через `ProxyManager` или нативные `readonly`/`__call` в PHP 8.4) перенаправляет первые вызовы на `getLazy...()`.
- **Prototype** = без `??=`. Каждый `get()` — новый объект.
- **Инлайн** (после `InlineServiceDefinitionsPass`): private-сервис, используемый один раз, встраивается прямо в тело метода, который его создаёт. Метод `getSomePrivateService()` исчезает.

---

## Autowiring: что именно происходит

`AutowirePass` делает следующее для каждого сервиса:

### 1. Читает сигнатуру конструктора

```php
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,            // ← тип-хинт
        private EventDispatcherInterface $event,  // ← тип-хинт (интерфейс)
        private string $defaultCurrency = 'EUR',  // ← скаляр с default
    ) {}
}
```

`AutowirePass` парсит через `ReflectionClass` (на этапе сборки, не в рантайме).

### 2. Для каждого параметра ищет сервис

Порядок поиска:

| Приоритет | Источник | Пример |
|---|---|---|
| 1 | Явный аргумент в конфиге (`$s->set(...)->args([...])`) | `->args(['repo' => 'custom.repo'])` |
| 2 | `!bind` (глобальная привязка) | `bind: { string $defaultCurrency: 'USD' }` |
| 3 | Алиас FQCN → класс | `App\Payment\PaymentGateway` → `App\Payment\StripeGateway` |
| 4 | Сервис, зарегистрированный под FQCN | `App\Service\OrderService` (само-ссылка) |
| 5 | **Один** сервис, реализующий интерфейс | `EventDispatcherInterface` → `Symfony\Component\EventDispatcher\EventDispatcher` |
| 6 | **Несколько** реализаций → **ошибка** | Два сервиса реализуют `PaymentGateway` → `AutowiringFailedException` |

### 3. Для скаляров — из параметров

```yaml
parameters:
  app.default_currency: EUR
```

```php
// Если нет default в сигнатуре и нет bind:
// AutowirePass ищет parameter 'app.defaultCurrency' (camelCase → snake_case)
```

### 4. Если ничего не найдено — **ошибка на этапе сборки**

```
[ServiceDefinitionError]
Cannot autowire service "App\Service\OrderService": 
argument "$repo" of method "__construct()" references 
interface "App\Repository\OrderRepository" but no such service exists.
```

Это **не** runtime-ошибка. Контейнер не скомпилируется. Приложение не запустится. Это и есть главная ценность autowiring: **ошибка смещается в build-time**.

### 5. `!tagged_iterator` — «все сервисы с тегом»

```yaml
services:
  App\Notification\Notifier:
    autowire: true

  App\Notification\NotifierInterface:
    autowire: true
    tags: ['app.notifier']

  App\Notification\EmailNotifier:
    tags: ['app.notifier']

  App\Notification\SmsNotifier:
    tags: ['app.notifier']
```

```php
final class NotificationService
{
    /**
     * @param iterable<NotifierInterface> $notifiers
     */
    public function __construct(
        private iterable $notifiers,  // ← Symfony подставит IteratorAggregate
    ) {}
}
```

В конфиге:

```yaml
services:
  App\Notification\NotificationService:
    autowire: true
    calls:
      - ['setNotifiers', ['!tagged_iterator app.notifier']]
```

Или в PHP-конфиге:

```php
$s->set(NotificationService::class)
  ->autowire()
  ->call('setNotifiers', [
      tagged_iterator('app.notifier'),
  ]);
```

На этапе компиляции `RegisterServiceSubscribersPass` собирает **все** сервисы с тегом `app.notifier` и создаёт `ServiceLocator` / `ServiceIterator` — ленивую коллекцию. Новый нотификатор = один `tags: ['app.notifier']`.

---

## Этап 3: runtime — первый `get()`

### Сценарий: контроллер запрашивает сервис

```php
// src/Controller/OrderController.php
final class OrderController
{
    public function __construct(
        private readonly OrderService $orderService,  // ← autowired при компиляции
    ) {}

    #[Route('/order/{id}', name: 'order_show')]
    public function show(string $id): Response
    {
        $order = $this->orderService->get($id);
        // ...
    }
}
```

### Что происходит при `new OrderController(...)`

1. **Symfony уже сгенерировал** в контейнере:

```php
// var/cache/dev/App_KernelDevDebugContainer.php
protected function getController_...(): \App\Controller\OrderController
{
    return $this->services['controller.order'] ??= new \App\Controller\OrderController(
        $this->getOrderService(),   // ← вызов другого метода контейнера
    );
}

protected function getOrderService(): \App\Service\OrderService
{
    return $this->services['app.service.orderservice'] ??= new \App\Service\OrderService(
        $this->getOrderRepository(),
        $this->getEventDispatcher(),
    );
}

protected function getOrderRepository(): \App\Repository\DoctrineOrderRepository
{
    return $this->services['app.repository.order'] ??= new \App\Repository\DoctrineOrderRepository(
        $this->getManagerRegistry(),   // ← дальше по цепочке
    );
}
```

2. **Вызов `getController_...()`** → `??=` создаёт `OrderController`, передавая результат `getOrderService()`.

3. **`getOrderService()`** → `??=` создаёт `OrderService`, передавая `getOrderRepository()` и `getEventDispatcher()`.

4. **`getOrderRepository()`** → `??=` создаёт репозиторий.

5. **Каждый `??=`** — один раз на запрос (singleton scope). Повторный `get()` вернёт из `$this->services`.

### Лазийный сервис

Если `PaymentGateway` помечен как lazy:

```php
protected function getPaymentGateway(): \App\Payment\PaymentGateway
{
    if (isset($this->services['app.payment.gateway'])) {
        return $this->services['app.payment.gateway'];
    }

    // Создаём ПРОКСИ, а не реальный объект
    $proxy = new \App\Payment\StripeGateway\Proxy(
        $this,   // ссылка на контейнер
    );

    return $this->services['app.payment.gateway'] = $proxy;
}
```

Прокси наследуется от реального класса и переопределяет конструктор. Пока никто не вызывает методы, **реальный `StripeGateway` не создан**. При первом вызове метода — прокси вызывает `getPaymentGateway()` заново, но теперь контейнер видит, что сервис уже в кэше, и возвращает реальную реализацию.

**Зачем:** если `PaymentGateway` тяжёлый (создаёт HTTP-клиент, читает config), а в 90% запросов он не нужен — не создаём вообще.

### Scope: `request`

Сервис с `scope: request` (например, `RequestStack`, `Session`) живёт **один на запрос**, а не на процесс. В dev-режиме (один запрос = один процесс) разница не видна. В long-running процессе (Swoole, FrankenPHP, RoadRunner) критична:

```php
protected function getRequest(): \Symfony\Component\HttpFoundation\Request
{
    // Singleton в рамках текущего request
    return $this->services['request_stack'] ??= /* из RequestStack */;
}

// При новом request в Swoole:
// $this->services = [];  ← сброс, новый request
```

### Утилизация

При завершении запроса Symfony вызывает `reset()` в **обратном** порядке создания:

```php
public function reset(): void
{
    // Если OrderService был создан 1-м, а Repository 2-м:
    // Сначала reset Repository (закрывает connection), потом OrderService.
    $this->privates = [];
    $this->services = [];
}
```

Это гарантирует, что зависимости освобождаются **до** того, как освободится зависимый.

---

## Публичные vs private сервисы

| | `public: true` | `public: false` (default) |
|---|---|---|
| Доступ через `$container->get()` | Да | Нет (TypeError) |
| Инъект через DI | Да | Да |
| Влияние на компиляцию | Остаётся как отдельный метод | Может быть **инлайнован** (`InlineServiceDefinitionsPass`) |
| Влияние на `RemoveUnusedDefinitionsPass` | Не удаляется | Удаляется, если не используется |

**Практика:** по умолчанию `public: false`. Только сервисы, которые нужно получить «вручную» (CLI-команды, тесты, debug) — `public: true`.

```yaml
services:
  App\Service\AuditLogger:
    public: true   # нужно в bin/console audit:export
  App\Service\Internal\TokenGenerator:
    # private по умолчанию — не доступен извне
```

---

## Ловушки, которые выстреливают на практике

### 1. Autowiring не видит «динамические» зависимости

```php
final class PluginManager
{
    public function __construct(
        private ContainerInterface $container,  // ← сам контейнер
    ) {}

    public function get(string $name): Plugin
    {
        return $this->container->get("app.plugin.{$name}");  // ← в рантайме
    }
}
```

Autowiring не может «угадать» `app.plugin.stripe` / `app.plugin.paypal` на этапе сборки. Решение: `!tagged_locator` или явные аргументы.

### 2. Циклические зависимости

```php
final class A { public function __construct(B $b) {} }
final class B { public function __construct(A $a) {} }
```

Компилятор **упадёт** с ошибкой. Решения: `!service_locator` (ленивое), event-driven, рефакторинг.

### 3. `@Inject` vs тип-хинт

```php
final class Service
{
    // Способ 1: конструктор (autowiring)
    public function __construct(private OrderRepository $repo) {}

    // Способ 2: property (Symfony 5.3+)
    #[Inject]
    private OrderRepository $repo;
}
```

`#[Inject]` на свойстве — это **отложенный** autowiring. Сервис создаётся при первом обращении к `$this->repo`, а не в конструкторе. Полезно для тяжёлых зависимостей, которые используются не всегда.

### 4. `autowire` ≠ `autoconfigure`

```yaml
services:
  App\Some\Command:
    autowire: true        # подставит зависимости
    # autoconfigure НЕ включает автоматически!
```

`autoconfigure` — это «добавь стандартные теги» (`console.command`, `form.type`, `kernel.event_subscriber`). Без него `Command` не зарегистрируется в CLI, `FormType` не будет в форме.

**Практика:** для `App\` — всегда оба:

```yaml
services:
  App\:
    resource: '../src/'
    autowire: true
    autoconfigure: true
```

### 5. Prod vs Dev: разные контейнеры

| | Dev | Prod |
|---|---|---|
| Публичные сервисы | Все | Только `public: true` |
| Private, неиспользуемые | Остаются | Удаляются (`RemoveUnusedDefinitionsPass`) |
| Inline | Нет (отладка) | Да (производительность) |
| Lazy прокси | Есть | Есть, но оптимизированы |
| Размер контейнера | ~2000 сервисов | ~400–800 сервисов |

В prod-контейнере `OrderService` может быть **инлайнован** прямо в метод контроллера. Отладка через `debug:container` работает иначе, чем в dev.

### 6. `!tagged_iterator` + `autowire` — не всегда работает

```yaml
services:
  App\Notifier\Notifier:
    calls:
      - ['setNotifiers', ['!tagged_iterator app.notifier']]
```

Это работает **только** через `calls`, не через `autowire` на параметре. Для `autowire` нужен `ServiceLocator` или явный `->call()`.

---

## Краткая схема: что происходит при `$container->get('app.my_service')`

```
1. PHP вызывает метод App_KernelDevDebugContainer::getMyService()
       │
2. Проверяет $this->services['app.my_service']
       ├── Есть → возвращает (singleton, кэш)
       │
       ├── Нет + lazy → создаёт Proxy, кладёт в кэш, возвращает Proxy
       │
       └── Нет + обычный → 
             │
3. new App\Service\MyService(
       $this->getDependency1(),   ← рекурсивный вызов
       $this->getDependency2(),
   )
       │
4. Результат кладёт в $this->services['app.my_service']
       │
5. Возвращает объект
```

Никакого отражения. Никакого `new $class(...)`. Статический, предсказуемый, инлайнованный код. Это и есть «компиляция»: **граф зависимостей превращается в плоский PHP-код**, который исполняется как обычные вызовы методов.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Сначала — почему это происходит

Циклическая зависимость в 90% случаев означает, что **два сервиса решают одну задачу с разных сторон**. Не «у DI-контейнера проблема», а у вас проблема с разграничением ответственности. Контейнер просто честно говорит: «я не могу построить объект, который создаёт себя».

```
OrderService ──→ InvoiceService
      ↑                │
      └────────────────┘
```

Типовой сценарий: «создать заказ» → «сгенерировать инвойс» → «инвойс ссылается на заказ» → «нужен заказ» → цикл.

---

## Стратегии разруливания

От самой частой к самой редкой.

---

### 1. Event-driven — «я не знаю, что ты сделаешь с результатом»

Самая частая и правильная стратегия. Один сервис завершает свою работу, бросает событие. Другой слушает и делает своё. Никакой прямой зависимости.

**Было (цикл):**

```php
final class OrderService
{
    public function __construct(
        private OrderRepository  $repo,
        private InvoiceService   $invoices,  // ← зависимость
    ) {}

    public function place(PlaceOrderCommand $cmd): OrderId
    {
        $order = Order::create($cmd->items);
        $this->repo->save($order);

        $invoice = $this->invoices->generateForOrder($order);  // ← прямой вызов
        $this->repo->save($order);   // сохранить уже с инвойсом

        return $order->id();
    }
}

final class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repo,
        private OrderService      $orders,   // ← ЦИКЛ
    ) {}

    public function generateForOrder(Order $order): Invoice
    {
        // Нужен статус заказа, чтобы определить тип инвойса
        $status = $this->orders->getStatus($order->id());

        $invoice = Invoice::create($order, $status);
        $this->repo->save($invoice);
        return $invoice;
    }
}
```

**Стало (событие):**

```php
// domain/Order/OrderPlaced.php
final readonly class OrderPlaced
{
    public function __construct(
        public readonly OrderId $orderId,
        public readonly int     $total,
        public readonly string  $status,
    ) {}
}
```

```php
final class OrderService
{
    public function __construct(
        private OrderRepository           $repo,
        private EventDispatcherInterface  $dispatcher,
    ) {}

    public function place(PlaceOrderCommand $cmd): OrderId
    {
        $order = Order::create($cmd->items);
        $this->repo->save($order);

        // Не знаю, кто слушает. Не знаю, будет ли инвойс.
        // Это не моя ответственность.
        $this->dispatcher->dispatch(
            new OrderPlaced($order->id(), $order->total(), $order->status())
        );

        return $order->id();
    }
}
```

```php
final class InvoiceListener
{
    public function __construct(
        private InvoiceRepository $repo,
    ) {}

    #[AsEventListener]
    public function onOrderPlaced(OrderPlaced $event): void
    {
        $invoice = Invoice::createFromEvent($event);
        $this->repo->save($invoice);
    }
}
```

```yaml
# config/services.yaml
services:
  App\Listener\InvoiceListener:
    autoconfigure: true   # Symfony сам поставит тег kernel.event_listener
```

**Что изменилось:**

| | Было | Стало |
|---|---|---|
| `OrderService` знает `InvoiceService`? | Да | Нет |
| `InvoiceService` знает `OrderService`? | Да | Нет |
| Цикл | Есть | Нет |
| Новый слушатель (например, аналитика) | Правка `OrderService` | Новый файл + `#[AsEventListener]` |
| Тест `OrderService` | Нужен mock `InvoiceService` | Нужен mock `EventDispatcher` (или реальный `EventDispatcher` с 0 слушателей) |

**Когда это правильно:** один сервис завершает свою работу, результат *может* потребоваться кому угодно, но сам сервис не знает *кому*. Инвойс, аналитика, уведомления, склад — все слушают одно событие.

**Когда это не подходит:** если инвойс *обязателен* для завершения заказа (без него заказ не считается размещённым) — событие асинхронное по смыслу, и вы теряете транзакционную атомарность. Тогда — вариант 2.

---

### 2. Извлечение общей зависимости — «вам обоим нужен не друг, а третья вещь»

Очень частый случай: два сервиса «зависят» друг от друга, потому что оба ходят за одними и теми же данными.

**Было (цикл):**

```php
final class OrderService
{
    public function __construct(
        private OrderRepository  $repo,
        private CustomerService  $customers,  // ← нужен email клиента
    ) {}

    public function place(PlaceOrderCommand $cmd): OrderId
    {
        $customer = $this->customers->get($cmd->customerId);
        $order = Order::create($cmd->items, $customer->email());
        $this->repo->save($order);
        return $order->id();
    }
}

final class CustomerService
{
    public function __construct(
        private CustomerRepository $repo,
        private OrderService       $orders,   // ← нужен список заказов клиента
    ) {}

    public function get(int $id): Customer
    {
        $customer = $this->repo->find($id);
        $customer->setOrderCount($this->orders->countForCustomer($id));  // ← ЦИКЛ
        return $customer;
    }
}
```

Оба сервисам нужен **доступ к данным** (заказы клиента, email клиента), а не *поведение* друг друга. Значит, им не нужен друг друг — им нужен **репозиторий / query-сервис**.

**Стало:**

```php
final class OrderService
{
    public function __construct(
        private OrderRepository    $repo,
        private CustomerRepository $customers,  // ← репозиторий, а не сервис
    ) {}

    public function place(PlaceOrderCommand $cmd): OrderId
    {
        $customer = $this->customers->find($cmd->customerId);
        $order = Order::create($cmd->items, $customer->email());
        $this->repo->save($order);
        return $order->id();
    }
}

final class CustomerService
{
    public function __construct(
        private CustomerRepository $repo,
        private OrderRepository    $orders,    // ← репозиторий, а не сервис
    ) {}

    public function get(int $id): Customer
    {
        $customer = $this->repo->find($id);
        $customer->setOrderCount($this->orders->countForCustomer($id));
        return $customer;
    }
}
```

Цикла нет. Оба ходят к `OrderRepository` и `CustomerRepository` — это **данные**, а не «поведение чужого сервиса». Репозитории не знают друг о друге.

**Критерий:** если зависимость сводится к «мне нужны данные X» — это репозиторий. Если «мне нужно *поведение* Y (расчёт, валидация, оркестрация)» — это уже сложнее, и нужен вариант 1 или 3.

---

### 3. ServiceLocator — «мне нужен доступ, но не в конструкторе»

Когда зависимость **редкая**, **ленивая**, и вы не хотите тащить её в сигнатуру конструктора.

**Было (цикл, потому что зависимость в конструкторе):**

```php
final class ReportService
{
    public function __construct(
        private ReportRepository $repo,
        private OrderService     $orders,   // ← нужен для вкладки «заказы»
    ) {}

    public function buildMonthlyReport(string $month): Report
    {
        $report = new Report($month);

        $orders = $this->orders->forMonth($month);  // ← редкий вызов
        $report->setOrders($orders);

        $this->repo->save($report);
        return $report;
    }
}

final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private ReportService   $reports,  // ← ЦИКЛ
    ) {}
    // ...
}
```

**Стало (ServiceLocator):**

```yaml
services:
  App\Service\ReportService:
    autowire: true
    calls:
      - ['setOrderProvider',
          ['!service_container']]   # ← контейнер, а не конкретный сервис
```

```php
final class ReportService
{
    private ContainerInterface $locator;

    public function setOrderProvider(ContainerInterface $locator): void
    {
        $this->locator = $locator;
    }

    public function buildMonthlyReport(string $month): Report
    {
        $report = new Report($month);

        // Ленивое: создаём OrderService только в этот момент
        /** @var OrderService $orders */
        $orders = $this->locator->get(OrderService::class);
        $report->setOrders($orders->forMonth($month));

        $this->repo->save($report);
        return $report;
    }
}
```

**Но подождите.** `OrderService` всё ещё зависит от `ReportService`? Если да — цикл остаётся, просто спрятан за ленивость. ServiceLocator **не решает** цикл. Он **откладывает** его: если в конкретном запросе `ReportService` не вызывается, `OrderService` не создаётся, и цикл не проявляется.

Это **костыль**, а не решение. Оправдан только если:
- зависимость действительно редкая (1 из 10 запросов);
- вы **уверены**, что в одном запросе оба сервиса не вызовутся одновременно;
- рефакторинг (варианты 1–2) невозможен в текущем спринте.

**Когда ServiceLocator — правильное решение, а не костыль:**

```php
// Фабрика плагинов: набор не известен на этапе сборки
final class PluginManager
{
    public function __construct(private ContainerInterface $locator) {}

    public function get(string $name): Plugin
    {
        return $this->locator->get("app.plugin.{$name}");
    }
}
```

Здесь нет цикла. Есть **неизвестный набор** зависимостей. ServiceLocator — единственный способ выразить «получу сервис по имени в рантайме».

---

### 4. Refactoring: один из сервисов не должен существовать

Самый честный ответ. «InvoiceService» часто — это **не сервис**, а **метод на aggregate** или **шаг в application-команде**.

**Было (цикл, потому что лишний сервис):**

```php
// OrderService, InvoiceService, NotificationService,
// AuditService, AnalyticsService — 5 сервисов, все зависят друг от друга
```

**Стало:**

```php
// domain/Order/Order.php — aggregate сам знает, что делать
final class Order
{
    public function markPaid(string $transactionId): void
    {
        $this->status = OrderStatus::Paid;
        $this->paidAt = new \DateTimeImmutable();
        $this->transactionId = $transactionId;

        // Доменное событие. Не вызов сервиса.
        $this->events[] = new OrderPaid($this->id(), $transactionId);
    }

    /** @return list<DomainEvent> */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
```

```php
// application/PlaceOrderHandler.php — оркестрация, не «сервис»
final class PlaceOrderHandler
{
    public function __construct(
        private OrderRepository          $repo,
        private StockService             $stock,
        private PaymentGateway           $payments,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function handle(PlaceOrderCommand $cmd): OrderId
    {
        $order = Order::create($cmd->items, $cmd->customer);

        $this->stock->reserve($order->items());

        $charge = $this->payments->charge(ChargeRequest::forOrder($order));
        $order->markPaid($charge->transactionId());

        $this->repo->save($order);

        // Инвойс, уведомления, аудит — слушатели. Не вызовы из handler'а.
        foreach ($order->pullEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

        return $order->id();
    }
}
```

**Что исчезло:** `InvoiceService` как отдельный сервис. Инвойс — это слушатель события `OrderPaid`. Аудит — слушатель. Уведомление — слушатель. `PlaceOrderHandler` знает только то, что *обязательно* для завершения команды.

**Когда это правильно:** «сервис» существует только для того, чтобы связать два других сервиса. Он не имеет собственного доменного смысла. Тогда он — не сервис, а **команда** или **listener**.

---

### 5. Interface Segregation — «мне не нужен весь сервис, только один метод»

Иногда цикл возникает потому, что один сервис **слишком большой**.

**Было (цикл):**

```php
interface PaymentGateway   // ← 20 методов
{
    public function charge(ChargeRequest $r): ChargeResult;
    public function refund(string $id): void;
    public function getBalance(): int;
    public function createCustomer(CustomerData $d): string;
    public function webhooks(WebhookPayload $p): void;
    // ... ещё 15
}

final class OrderService
{
    public function __construct(
        private PaymentGateway $gateway,  // ← нужен только charge()
    ) {}
}

final class CustomerService
{
    public function __construct(
        private PaymentGateway $gateway,  // ← нужен только createCustomer()
        private OrderService   $orders,   // ← ЦИКЛ (потому что PaymentGateway «тянет» OrderService через webhooks)
    ) {}
}
```

**Стало:**

```php
interface Chargeable
{
    public function charge(ChargeRequest $r): ChargeResult;
}

interface CustomerRegistrar
{
    public function createCustomer(CustomerData $d): string;
}

final class StripeGateway implements Chargeable, CustomerRegistrar
{
    // ...
}

final class OrderService
{
    public function __construct(
        private Chargeable $gateway,   // ← только то, что нужно
    ) {}
}

final class CustomerService
{
    public function __construct(
        private CustomerRegistrar $gateway,  // ← только то, что нужно
    ) {}
}
```

Цикл мог быть побочным эффектом «слишком широкого интерфейса», который тащил за собой несвязанные зависимости. Сужение интерфейса убирает их.

---

## Таблица решений

| Симптом цикла | Стратегия | Пример |
|---|---|---|
| «Я завершаю работу, кто-то должен отреагировать» | **Event-driven** | Order → Invoice, Order → Notification |
| «Мне нужны данные, которые есть у другого» | **Извлечение репозитория / query** | OrderService и CustomerService → OrderRepository |
| «Зависимость редкая и ленивая» | **ServiceLocator** (костыль) | PluginManager, ReportService |
| «Сервис существует только чтобы связать два других» | **Убрать сервис, сделать handler/listener** | InvoiceService → InvoiceListener |
| «Интерфейс слишком широкий, тащит лишнее» | **Interface Segregation** | PaymentGateway → Chargeable + CustomerRegistrar |
| «Два сервиса делают одну задачу с разных сторон» | **Объединить в один** или **разделить домен** | OrderService + InvoiceService → Order aggregate + application command |

---

## Чек-лист: «у меня цикл, что делать?»

1. **Нарисовать граф.** Не в коде, а на салфетке. Кто кого вызывает. Где именно точка цикла.

2. **Запросить у каждой стрелки:** «мне нужно *данные* или *поведение*?»
   - Данные → репозиторий. Цикл исчезает.
   - Поведение → переходить к пункту 3.

3. **Запросить:** «если я бросил событие, а не вызвал метод, кто-то сломается?»
   - Нет → Event-driven. Цикл исчезает.
   - Да → переходить к пункту 4.

4. **Запросить:** «один из этих сервисов имеет самостоятельный доменный смысл?»
   - Нет → это не сервис. Это handler, listener, или метод на aggregate.
   - Да → переходить к пункту 5.

5. **Запросить:** «могу ли я сузить интерфейс так, чтобы каждый сервис видел только свой срез?»
   - Да → Interface Segregation.
   - Нет → рефакторинг домена. Возможно, два «сервиса» — это один Bounded Context, и их нужно объединить.

6. **Если ничего не помогает** — ServiceLocator как временный мост. С комментарием `// TODO: рефакторинг, см. issue #123`. Не навсегда.

---

## Что я **не** делаю

- **Не «ломать» цикл через `public` + `$container->get()` в рантайме**, если есть шанс рефакторинга. Это переносит проблему из build-time в runtime, и она станет тише, но не исчезнет.
- **Не добавлять `try/catch`** вокруг зависимостей. Цикл — это архитектурная проблема, не runtime-исключение.
- **Не вводить «общего предка»** (абстрактный `BaseService`), чтобы «разделить» два сервиса. Это не решает цикл, а усложняет иерархию.
- **Не делать `private` → `public`** на сервисе, чтобы «обойти» ограничение контейнера. Это маскирует проблему, а не решает её.

---

Если хочешь, могу разобрать конкретный цикл, который у тебя есть: покажи два класса и их зависимости — предложу 2–3 варианта с кодом.

> ⏱ 44.6s · prompt 23967 tok · output 5673 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о том, как организовать асинхронные side effects (email + поисковый индекс) после успешной регистрации в Symfony. Это классическая задача: ответ должен быть быстрым, но side effects обязательны.

Нужно описать варианты:
1. Event-driven + Messenger (встроенный в Symfony) — async transport
2. Event-driven + внешний брокер (RabbitMQ, Kafka, Redis Streams)
3. Fire-and-forget в request (set_time_limit, curl в фоне) — антипаттерн
4. Swoole/FrankenPHP coroutines
5. Cron / batch-обработка

Trade-offs:
- Атомарность: если регистрация успешна, а email не ушёл — что делать?
- Идемпотентность: если message будет доставлен дважды?
- Наблюдаемость: как отследить, что email ушёл?
- Retry: если email-сервер упал?
- Ordering: важно ли, чтобы email ушёл до индекса?
- Backpressure: если индекс упал, не должен ли это блокировать регистрацию?
- Тестируемость

Структурирую ответ:
1. Краткое: почему не делаем синхронно
2. Архитектура: event → messenger → async transport → workers
3. Варианты транспорта и их trade-offs
4. Идемпотентность и retry
5. Наблюдаемость
6. Антипаттерны

Буду писать на русском, как и весь разговор. Покажу код.

</details>

**Кандидат:**

## Почему не синхронно

Регистрация = доменная операция. Email и индекс — **side effects**, которые не влияют на корректность регистрации. Пользователь уже зарегистрирован. Если email-сервер упал — это не отменяет регистрацию. Если индекс не обновился — пользователь просто не найдётся в поиске через 30 секунд.

Синхронный вызов:

```php
// Плохо: ответ зависит от скорости email-сервера и поискового индекса
public function register(RegisterCommand $cmd): Response
{
    $user = $this->userRepo->save(User::create($cmd));

    $this->mailer->sendWelcome($user);       // +200–2000 мс, может упасть
    $this->searchIndex->addUser($user);       // +50–500 мс, может упасть

    return new JsonResponse(['id' => $user->id()], 201);
}
```

Проблемы:
- Ответ зависит от *самых медленных* внешних систем.
- Падение email-сервера → 500 для пользователя, хотя регистрация успешна.
- Нет retry: упало один раз — не уйдёт никогда.
- Нет наблюдаемости: «почему пользователь не получил письмо?» — лог в одном месте, индекс в другом.

---

## Архитектура: Event → Messenger → Async → Workers

```
┌────────────────────────────────────────────────────────────────────────────┐
│  REQUEST                                                                  │
│                                                                          │
│  RegisterHandler::handle()                                                │
│    1. Валидация                                                           │
│    2. Создание User (домен)                                               │
│    3. Сохранение User (репозиторий)                                       │
│    4. $dispatcher->dispatch(new UserRegistered($user))  ← один вызов     │
│    5. return 201 OK                                              ← ответ │
│                                                                          │
│  (всё в одной транзакции БД + один event)                                 │
└────────────────────────────────────────────────────────────────────────────┘
         │
         ▼  (event перехвачен Messenger-листенером)
┌────────────────────────────────────────────────────────────────────────────┐
│  MESSAGE BUS (Symfony Messenger)                                          │
│                                                                          │
│  UserRegistered (доменное событие)                                        │
│       │                                                                  │
│       ├──→ SendWelcomeEmail (message)    → транспорт → Worker A          │
│       └──→ IndexUserSearch (message)     → транспорт → Worker B          │
│                                                                          │
│  Transport: sync / amqp / redis / doctrine / psqtcp                      │
└────────────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌────────────────────────────────────────────────────────────────────────────┐
│  WORKERS (отдельные процессы)                                             │
│                                                                          │
│  Worker A: SendWelcomeEmailHandler                                        │
│    - конструирует письмо                                                  │
│    - вызывает SMTP / SES                                                  │
│    - при ошибке → retry (exponential backoff)                             │
│    - после N попыток → dead-letter queue + алерт                          │
│                                                                          │
│  Worker B: IndexUserSearchHandler                                         │
│    - отправляет документ в Meilisearch / Elasticsearch                    │
│    - при ошибке → retry                                                   │
│    - после N попыток → dead-letter queue + алерт                          │
│                                                                          │
│  (Worker A и B параллельны, независимы)                                   │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## Код

### Доменное событие

```php
// src/Domain/User/UserRegistered.php
declare(strict_types=1);

namespace App\Domain\User;

final readonly class UserRegistered
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
```

### Application: dispatch

```php
// src/Application/User/RegisterHandler.php
declare(strict_types=1);

namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRegistered;
use Symfony\Component\Messenger\MessageEnvelope;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RegisterHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private MessageBusInterface     $bus,
    ) {}

    public function handle(RegisterCommand $cmd): UserId
    {
        // Доменная операция
        $user = User::create($cmd->email, $cmd->password, $cmd->name);
        $this->users->save($user);

        // Side effects — не вызываю напрямую.
        // Отправляю событие на шину. Что с ним сделают — не моё дело.
        $this->bus->dispatch(new UserRegistered(
            userId: $user->id(),
            email:  $user->email(),
            name:   $user->name(),
        ));

        return $user->id();
    }
}
```

**Важно:** `dispatch()` здесь **не** означает «отправил email». Он означает «положил сообщение в транспорт». Если транспорт — `sync` (dev), обработчик вызовется синхронно. Если `amqp`/`redis` (prod) — сообщение уйдёт в очередь, и ответ вернётся немедленно.

### Конфигурация шин и транспортов

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        # Шина 1: доменные события → конкретные сообщения
        buses:
            event.bus:
                middleware:
                    - 'messenger.handle_message'

        # Шина 2: конкретные сообщения → обработчики
        buses:
            command.bus:
                middleware:
                    - 'messenger.handle_message'

        transports:
            # Dev: в памяти (синхронно, для тестов)
            dev:
                dsn: 'sync://'

            # Prod: Redis (лёгкий, достаточный для email + search)
            redis:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                # dsn: redis://localhost:6379/0

            # Prod (альтернатива): RabbitMQ
            # amqp:
            #     dsn: 'amqp://guest:guest@localhost:5672/%2f'

        # Роутинг: какое сообщение → какой транспорт → какой bus
        routing:
            'App\Message\SendWelcomeEmail':
                transport: '%env(MESSENGER_TRANSPORT)%'
                bus: command.bus
            'App\Message\IndexUserSearch':
                transport: '%env(MESSENGER_TRANSPORT)%'
                bus: command.bus

        # Retry-политика
        failure_transport: failed

        # Фреймворк-специфичные транспорты
        transports:
            amqp:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    connection_timeout: 2
                    wait_for_connection: true
            doctrine:
                dsn: 'doctrine://default?auto_setup=0'
            redis:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
```

### Слушатель события → конкретные сообщения

```php
// src/Message/UserRegisteredListener.php
declare(strict_types=1);

namespace App\Message;

use App\Domain\User\UserRegistered;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class UserRegisteredListener
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {}

    #[AsEventListener]
    public function onUserRegistered(UserRegistered $event): void
    {
        // Одно доменное событие → несколько прикладных сообщений.
        // Каждое — независимое, с своим retry, своим транспортом.

        $this->bus->dispatch(new SendWelcomeEmail($event->userId, $event->email));
        $this->bus->dispatch(new IndexUserSearch($event->userId, $event->name, $event->email));
    }
}
```

### Конкретные сообщения

```php
// src/Message/SendWelcomeEmail.php
declare(strict_types=1);

namespace App\Message;

final readonly class SendWelcomeEmail
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
    ) {}
}

// src/Message/IndexUserSearch.php
declare(strict_types=1);

namespace App\Message;

final readonly class IndexUserSearch
{
    public function __construct(
        public readonly string $userId,
        public readonly string $name,
        public readonly string $email,
    ) {}
}
```

### Обработчики (Workers)

```php
// src/MessageHandler/SendWelcomeEmailHandler.php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendWelcomeEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface   $mailer,
        private LoggerInterface  $logger,
    ) {}

    public function __invoke(SendWelcomeEmail $message): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($message->email)
            ->subject('Welcome!')
            ->text("Hi {$message->userId}, welcome aboard!");

        $this->mailer->send($email);

        $this->logger->info('Welcome email sent', [
            'user_id' => $message->userId,
            'to'      => $message->email,
        ]);
    }
    // Если $this->mailer->send() бросает исключение —
    // Messenger автоматически повторит (retry) согласно конфигу.
}
```

```php
// src/MessageHandler/IndexUserSearchHandler.php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\IndexUserSearch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Meilisearch\Client as MeiliClient;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
final class IndexUserSearchHandler
{
    public function __construct(
        private MeiliClient     $meili,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(IndexUserSearch $message): void
    {
        $this->meili
            ->index('users')
            ->updateDocument([
                'id'    => $message->userId,
                'name'  => $message->name,
                'email' => $message->email,
            ]);

        $this->logger->info('User indexed for search', [
            'user_id' => $message->userId,
        ]);
    }
}
```

### Worker-процесс

```bash
# Procfile / Docker / systemd
php bin/console messenger:consume redis --time-limit=3600 --memory-limit=256M
```

Каждый worker — отдельный процесс. Может масштабироваться горизонтально. Если email-обработчик упал — worker B (search) продолжает работать.

---

## Варианты транспорта и trade-offs

| Транспорт | Задержка | Устойчивость | Масштабирование | Сложность | Когда |
|---|---|---|---|---|---|
| `sync://` | 0 (в request) | ❌ (процесс умер — сообщение умерло) | Нет | Минимальная | Dev, тесты |
| `doctrine://` | ~0 | ⚠️ (в БД, но в той же транзакции) | Нет | Низкая | Прототип, маленький проект |
| `redis://` | 1–5 мс | ✅ (survives crash) | ✅ (multi-consumer) | Низкая | 1–10k msg/s, 1–5 нод |
| `amqp://` (RabbitMQ) | 1–10 мс | ✅ (publisher confirm, prefetch) | ✅ (queues, exchanges) | Средняя | 10k–100k msg/s, сложных роутинг |
| `psqtcp://` (Postgres LISTEN/NOTIFY) | 1–5 мс | ⚠️ (single consumer, connection drop) | ⚠️ | Низкая | Простые сценарии, уже есть Postgres |
| Kafka / Redpanda | 5–50 мс | ✅✅ (repartition, offset) | ✅✅ (partitions) | Высокая | 100k+ msg/s, строгий ordering, replay |
| `beanstalkd://` | 1–5 мс | ⚠️ (не так robust как Rabbit) | ✅ | Низкая | Простые job queues |

### Мой выбор для «email + search»

**Redis** (или `psqtcp`, если уже есть Postgres и нет Redis). Почему:
- Объём: 2 сообщения на регистрацию. Даже при 100 регистраций/сек — 200 msg/s. Redis справляется с миллионом.
- Ordering: не нужен. Email и search независимы.
- Replay: не нужен. Если email не ушёл — переслать вручную.
- Сложность: минимум. Нет broker, нет топик-схем, нет consumer groups.
- Устойчивость: если процесс worker'а упал — сообщение в Redis, подхватит другой worker.

### Когда Kafka

- Есть строгий ordering (email → SMS → push, и именно в этом порядке).
- Нужно replay (переслать все email за неделю).
- Объём: >100k msg/s.
- Разные команды consumer'ят одну и ту же очередь.

Для «email + search» — overkill.

---

## Retry и идемпотентность

### Retry-политика

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\SendWelcomeEmail':
                transport: redis
                options:
                    # 5 попыток, exponential backoff
                    max_retries: 5
                    delay: 5000        # 5 секунд
                    multiplier: 2      # 5s, 10s, 20s, 40s, 80s
                    max_delay: 300000  # максимум 5 минут

            'App\Message\IndexUserSearch':
                transport: redis
                options:
                    max_retries: 3
                    delay: 2000
```

### Dead-letter queue

После исчерпания retry — сообщение уходит в `failed` transport:

```yaml
framework:
    messenger:
        failure_transport: failed
        transports:
            failed:
                dsn: 'doctrine://default'   # или redis://failed
```

```bash
# Просмотр упавших сообщений
php bin/console messenger:failed --limit=20

# Повторная отправка (после починки)
php bin/console messenger:failed --retry
php bin/console messenger:failed --delete 42
```

### Идемпотентность

**Проблема:** message доставлен дважды (crash между `ack` и обработкой). Handler запускается повторно.

```php
// НЕ идемпотентно:
public function __invoke(SendWelcomeEmail $msg): void
{
    $this->mailer->send($msg);  // пользователь получит 2 письма
}
```

**Решение:**

```php
// src/MessageHandler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface    $mailer,
        private Connection         $pdo,   // для tracking
    ) {}

    public function __invoke(SendWelcomeEmail $msg): void
    {
        // Идемпотентность: не слать, если уже ушло
        $sent = $this->pdo->fetchOne(
            'SELECT 1 FROM email_log WHERE user_id = ? AND type = ?',
            [$msg->userId, 'welcome'],
        );
        if ($sent !== null) {
            return;  // уже отправлено, пропускаем
        }

        $this->mailer->send($msg);

        // Отмечаем ДО возврата. Если crash между send и insert —
        // resend допустим (дубликат письма лучше, чем отсутствие).
        $this->pdo->executeStatement(
            'INSERT INTO email_log (user_id, type, sent_at) VALUES (?, ?, NOW())',
            [$msg->userId, 'welcome'],
        );
    }
}
```

Для search-индекса идемпотентность «бесплатна»: `updateDocument` с тем же `id` — overwrite. Дубликат = нет-op.

### Ordering

Для email + search ordering **не важен**. Они независимы. Если бы было email → SMS → push, и порядок критичен — нужен `priority` в messenger или separate queue per step.

---

## Наблюдаемость

```php
// src/MessageHandler/SendWelcomeEmailHandler.php
public function __invoke(SendWelcomeEmail $msg): void
{
    $this->logger->info('welcome_email.start', [
        'user_id' => $msg->userId,
        'attempt' => $this->getAttempt(),  // из Stamps
    ]);

    try {
        $this->mailer->send($msg);
        $this->logger->info('welcome_email.success', ['user_id' => $msg->userId]);
    } catch (\Throwable $e) {
        $this->logger->error('welcome_email.failed', [
            'user_id' => $msg->userId,
            'error'   => $e->getMessage(),
        ]);
        throw $e;  // даём Messenger повторить
    }
}
```

### Stamps (попытка, timestamp, transport)

```php
// В handler'е можно посмотреть метадату:
use Symfony\Component\Messenger\Stamp\TransportStamp;
use Symfony\Component\Messenger\Stamp\RetryStamp;

public function __invoke(
    SendWelcomeEmail $msg,
    Envelope $envelope,
): void {
    $retry = $envelope->last(RetryStamp::class);
    if ($retry) {
        $this->logger->warning('Retrying welcome email', [
            'attempt' => $retry->getRetryCount(),
            'user_id' => $msg->userId,
        ]);
    }
}
```

### Метрики (Prometheus / DataDog)

```php
// src/Messenger/MetricsMiddleware.php
final class MetricsMiddleware implements MiddlewareInterface
{
    public function __construct(private Prometheus\Counter $counter) {}

    public function handle(Envelope $envelope, SenderInterface $sender): Envelope
    {
        $class = $envelope->getMessage()::class;

        try {
            $result = $sender->send($envelope);
            $this->counter->label(['type' => $class, 'status' => 'ok'])->inc();
            return $result;
        } catch (\Throwable $e) {
            $this->counter->label(['type' => $class, 'status' => 'fail'])->inc();
            throw $e;
        }
    }

    public function supports(object $message, string $senderAlias): bool
    {
        return true;
    }
}
```

---

## Антипаттерны

### 1. `set_time_limit` + curl в фоне

```php
// АНТИПАТТЕРН: "fire and forget"
$ch = curl_init('http://internal-worker/send-email');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_exec($ch);  // не ждём ответа
curl_close($ch);
```

- Нет retry.
- Нет подтверждения доставки.
- Нет наблюдаемости.
- Если worker не поднял соединение за 5 секунд — письмо потеряно.
- Убийство процесса (OOM, segfault) между `curl_exec` и `curl_close` — состояние неопределённо.

### 2. Синхронный `dispatch` в dev + асинхронный в prod

```yaml
# config/packages/dev/messenger.yaml
framework:
    messenger:
        transports:
            default:
                dsn: 'sync://'   # dev: в памяти
```

```yaml
# config/packages/messenger.yaml (prod)
framework:
    messenger:
        transports:
            default:
                dsn: 'redis://...'
```

Это **нормально** (dev-удобство), но:
- Тесты на dev не проверяют retry, dead-letter, ordering.
- Нужно отдельный набор интеграционных тестов для prod-поведения.

### 3. Один worker на все сообщения

```bash
# АНТИПАТТЕРН для production:
php bin/console messenger:consume redis
```

Один процесс обрабатывает и email, и search, и report generation. Если email-сервис завис на 30 секунд — search-индексация тоже ждёт.

**Решение:** отдельные worker-процессы с фильтрацией:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\SendWelcomeEmail':
                transport: redis
                bus: email.bus
            'App\Message\IndexUserSearch':
                transport: redis
                bus: search.bus
```

```bash
# Procfile
email_worker:  php bin/console messenger:consume redis --message='App\Message\SendWelcomeEmail'
search_worker: php bin/console messenger:consume redis --message='App\Message\IndexUserSearch'
```

### 4. Отсутствие dead-letter queue

```yaml
# АНТИПАТТЕРН:
framework:
    messenger:
        # нет failure_transport
```

Сообщение упало 5 раз — исчезло. Никто не знает.

**Решение:** всегда:

```yaml
framework:
    messenger:
        failure_transport: failed
```

### 5. Доменный код знает о транспорте

```php
// АНТИПАТТЕРН:
final class User
{
    public function register(): void
    {
        // ...
        $this->bus->dispatch(new SendWelcomeEmail(...));  // ← домен знает о Messenger!
    }
}
```

Домен не должен знать о `MessageBusInterface`, `Transport`, `Envelope`. Это application-слой.

---

## Итоговая схема

```
Регистрация (request)
  │
  │  1. Валидация
  │  2. User::create()         ← домен
  │  3. repo->save()           ← инфраструктура (транзакция)
  │  4. bus->dispatch(         ← application (1 строка)
  │       UserRegistered       ← доменное событие
  │     )
  │  5. return 201             ← ответ пользователю (~50 мс)
  │
  ▼ (асинхронно, в отдельном процессе)
  │
  ├──→ SendWelcomeEmail → Redis → Worker A → SMTP → ACK / RETRY / DLQ
  │
  └──→ IndexUserSearch  → Redis → Worker B → Meili → ACK / RETRY / DLQ
```

**Ключевые свойства:**
- Ответ пользователю не зависит от скорости email/search.
- Падение email-сервера не влияет на search.
- Retry автоматический, с backoff.
- Dead-letter queue — ничего не теряется.
- Масштабирование: 2 worker'а → 10 worker'ов, без изменения кода.
- Добавление нового side effect (SMS, аналитика) = 1 новый message + 1 новый handler + 1 строка в routing.

> ⏱ 44.6s · prompt 33574 tok · output 5869 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему контроля доступа в Symfony: аутентификация, роли, voters. И практический вопрос — когда voter, а когда достаточно ролей.

Мне нужно:
1. Разобрать архитектуру Symfony Security: firewall, auth manager, token, provider, voter.
2. Показать, как это работает изнутри (что происходит при request).
3. Конкретные примеры кода.
4. Практический критерий: voter vs роль. Это ключевой вопрос — он про принятие решений.

Структура:
- Аутентификация: кто ты (firewall, provider, token, session)
- Авторизация: что тебе можно (roles, attributes, voters)
- Как это связано
- Voter: когда и как
- Роль: когда достаточно
- Критерий выбора
- Антипаттерны

Нужно показать реальный код, не абстракции. И дать чёткий практический критерий, а не "зависит от ситуации".

</details>

**Кандидат:**

## Три разных вопроса, которые часто смешивают

| Вопрос | Что отвечает | Механизм Symfony |
|---|---|---|
| **Кто ты?** | Аутентификация | Firewall → Provider → Token |
| **Какая у тебя роль?** | Грубый фильтр | `hasRole('ROLE_ADMIN')` |
| **Можно ли *конкретное* действие над *конкретным* объектом?** | Тонкий фильтр | Voter, attributes |

Роль отвечает «какой ты». Voter отвечает «можно ли *тебе* это *тут*». Это разные уровни, и их путают в 80% проектов.

---

## Аутентификация: что происходит при request

```
HTTP Request
    │
    ▼
┌──────────────────────────────────────────────────────────────────┐
│  Firewall (security.yaml)                                        │
│                                                                  │
│  1. Match: URL попадает в firewall "main"?                        │
│     (path: ^/, pattern: ...)                                     │
│                                                                  │
│  2. Аутентификация:                                               │
│     ┌─ session-based (form_login)                                │
│     │   Cookie PHPSESSID → Session → UserIdentifier              │
│     │   → UserProvider::loadUserByIdentifier()                  │
│     │   → User object                                            │
│     │                                                            │
│     ├─ token-based (jwt)                                         │
│     │   Header Authorization: Bearer <token>                     │
│     │   → JwtTokenAuthenticator::supports() → true              │
│     │   → JwtTokenAuthenticator::authenticate()                 │
│     │   → decode JWT → UserIdentifier → UserProvider            │
│     │                                                            │
│     └─ http basic, ldap, oauth2, ...                             │
│                                                                  │
│  3. Результат:                                                    │
│     ┌─ Успех → TokenInterface (RememberMeToken, PostAuthenticationToken)
│     │         содержит: User object, roles[], credentials       │
│     │         → хранится в Security::getTokenStorage()          │
│     │                                                            │
│     └─ Невалидно → 401 + WWW-Authenticate header               │
│                                                                  │
│  4. Token доступен в дальнейшем:                                  │
│     $this->user  (в контроллере / сервисе)                       │
│     Security::getUser()                                          │
│     #[CurrentUser] User $user                                    │
└──────────────────────────────────────────────────────────────────┘
    │
    ▼
┌──────────────────────────────────────────────────────────────────┐
│  Авторизация: можно ли выполнить действие?                        │
│                                                                  │
│  @IsGranted('EDIT', post)                                        │
│  $this->isGranted('ROLE_ADMIN')                                  │
│  $this->denyAccessUnlessGranted('DELETE', $order)                │
│                                                                  │
│  → AuthorizationChecker::isGranted()                             │
│     → Voter chain:                                                 │
│        RoleVoter → AccessDecisionManager → Result                │
│                                                                  │
│  → 200 / 403                                                    │
└──────────────────────────────────────────────────────────────────┘
```

### Конфигурация

```yaml
# config/packages/security.yaml
security:
    # Откуда берётся User
    providers:
        app_user:
            entity:
                class: App\Entity\User
                property: email   # loadUserByEmail()

    # Как аутентифицируем
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|assets|bundles)/
            security: false

        main:
            lazy: true
            provider: app_user

            # Форм-логин (session-based)
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_home
                enable_csrf: true

            # JWT (token-based, для API)
            # jwt:
            #     auth_header_name: Authorization
            #     auth_header_prefix: Bearer
            #     user_id_claim: uid

            logout:
                path: app_logout
                target: app_login

            # Remember me
            remember_me:
                secret: '%env(APP_SECRET)%'
                lifetime: 31536000
                path: /

    # Access control: URL → роль
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: ROLE_USER }
        - { path: ^/,       roles: PUBLIC_ACCESS }

    # Роли по умолчанию
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER, ROLE_EDITOR]
        ROLE_EDITOR: ROLE_USER
```

### User entity

```php
// src/Entity/User.php
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?string $passwordHash = null;

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        // ROLE_USER — базовая, всегда есть
        // ROLE_ADMIN / ROLE_EDITOR — из БД или hardcode
        $roles = ['ROLE_USER'];
        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }
        if ($this->isEditor) {
            $roles[] = 'ROLE_EDITOR';
        }
        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->passwordHash ?? '';
    }

    public function isPasswordAuthenticated(): bool
    {
        return true;
    }
}
```

---

## Авторизация: три уровня

### Уровень 1: URL-доступ (access_control)

```yaml
access_control:
    - { path: ^/admin,   roles: ROLE_ADMIN }
    - { path: ^/profile, roles: ROLE_USER }
    - { path: ^/,        roles: PUBLIC_ACCESS }
```

Отвечает: «может ли этот URL вообще открыть?» Не знает, *какой* объект. Подходит для «админка только админам», «личка только авторизованным».

### Уровень 2: Роль (RoleVoter)

```php
// src/Controller/PostController.php
class PostController
{
    #[Route('/posts/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        // Просто: роль есть — доступ есть
        $this->denyAccessUnlessGranted('ROLE_USER');
        // ...
    }

    #[Route('/posts/{id}/edit', name: 'post_edit')]
    public function edit(Post $post): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $post);
        // Роль Admin — доступ. Роль User — 403.
        // Но: Admin может редактировать ЧУЖОЙ пост? Да. Потому что роль.
        // А User — свой? Нет. Потому что нет роли.
        // Это НЕ то, что мы хотим.
    }
}
```

Роль — **глобальная**. `ROLE_ADMIN` — «я админ, мне всё можно». Не привязана к объекту.

### Уровень 3: Voter (object-level)

```php
// src/Security/PostVoter.php
final class PostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const DELETE = 'POST_DELETE';
    public const COMMENT = 'POST_COMMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Этот voter работает только с Post
        if (!$subject instanceof Post) {
            return false;
        }

        // И только с нашими атрибутами
        return in_array($attribute, [self::EDIT, self::DELETE, self::COMMENT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
    {
        /** @var Post $post */
        return match ($attribute) {
            self::EDIT    => $this->canEdit($post, $user),
            self::DELETE  => $this->canDelete($post, $user),
            self::COMMENT => $this->canComment($post, $user),
        };
    }

    private function canEdit(Post $post, UserInterface $user): bool
    {
        // Владелец — может
        if ($post->getAuthor() === $user) {
            return true;
        }
        // Админ — может
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        // Редактор — может, если пост в его рубрике
        if ($user->hasRole('ROLE_EDITOR')
            && in_array($post->getCategory(), $user->getAssignedCategories(), true)) {
            return true;
        }
        // Публичный пост — любой авторизованный может (режим «draft review»)
        if ($post->isPublic() && $user->hasRole('ROLE_USER')) {
            return true;
        }

        return false;
    }

    private function canDelete(Post $post, UserInterface $user): bool
    {
        // Только владелец или админ
        return $post->getAuthor() === $user
            || $user->hasRole('ROLE_ADMIN');
    }

    private function canComment(Post $post, UserInterface $user): bool
    {
        // Закрытый пост — только автор и админ
        if (!$post->isPublic()) {
            return $post->getAuthor() === $user || $user->hasRole('ROLE_ADMIN');
        }
        // Публичный — любой авторизованный
        return $user->hasRole('ROLE_USER');
    }
}
```

```php
// src/Controller/PostController.php
class PostController
{
    #[Route('/posts/{id}/edit', name: 'post_edit')]
    public function edit(Post $post): Response
    {
        // Конкретный объект. Конкретное правило.
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

        // ...
    }

    #[Route('/posts/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::COMMENT, $post);
        // Если нельзя комментировать — можно ли смотреть? Отдельный вопрос.
        // Это можно вынести в отдельный attribute: PostVoter::VIEW
        // ...
    }
}
```

```twig
{# templates/post/show.html.twig #}
{% if is_granted('POST_EDIT', post) %}
    <a href="{{ path('post_edit', { id: post.id }) }}">Редактировать</a>
{% endif %}

{% if is_granted('POST_DELETE', post) %}
    <button onclick="deletePost({{ post.id }})">Удалить</button>
{% endif %}
```

```php
// src/Service/PostService.php
class PostService
{
    public function __construct(private AuthorizationChecker $authz) {}

    public function edit(Post $post, EditPostCommand $cmd): Post
    {
        $this->authz->isGranted(PostVoter::EDIT, $post)
            or throw new AccessDeniedException('Cannot edit this post');

        // ... бизнес-логика
    }
}
```

---

## Когда voter, а когда достаточно роли

### Критерий: зависит ли решение от **конкретного объекта**?

```
Вопрос: «Может ли User редактировать ЧУЖОЙ пост?»

  Роль:    ROLE_ADMIN → да, ROLE_USER → нет.
           Не знает, ЧЬЙ пост. Не может отличить свой от чужого.

  Voter:  $post->getAuthor() === $user → да.
          $post->getAuthor() !== $user → нет (если не admin/editor).
          Знает объект. Знает владельца. Знает рубрику.
```

### Таблица

| Ситуация | Роль или Voter? | Почему |
|---|---|---|
| `/admin/dashboard` — только админы | **Роль** | Нет объекта. Нет контекста. URL-уровня достаточно. |
| `/profile` — только авторизованные | **Роль** (`ROLE_USER`) | Нет объекта. «Авторизован или нет». |
| Редактировать **свой** пост | **Voter** | Зависит от `$post->getAuthor()` |
| Удалить **любой** пост (админ) | **Роль** или **Voter** | Если правило одно («admin — да») → роль. Если «admin + пост не закреплён» → voter. |
| Просмотр **своего** заказа | **Voter** | Зависит от `$order->getCustomer()` |
| Просмотр **чужого** заказа (менеджер) | **Voter** | Зависит от `$user->getTeam()` vs `$order->getTeam()` |
| Выгрузка отчёта (роль FINANCE) | **Роль** | Нет объекта. «Ты финотчёт или нет». |
| Удалить **свой** аккаунт | **Voter** | Зависит от `$account->getOwner()` |
| Доступ к `/api/internal/*` | **Роль** + IP-allowlist | Нет объекта. Инфраструктурный уровень. |

### Правило

> **Роль** — когда решение зависит только от *кто ты* (титул, команда, уровень).
> **Voter** — когда решение зависит от *кто ты + что это за объект + в каком состоянии объект*.

Если в правиле есть `if ($post->getAuthor() === $user)` — это voter. Роль не выразит.

### Гибрид: роль как входной барьер + voter как деталь

```php
// access_control: грубый фильтр
- { path: ^/posts/.*edit, roles: ROLE_USER }

// Voter: точный фильтр
$this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
```

Роль отсекает «гостей». Voter решает «свой или чужой». Это не дублирование, а два уровня.

---

## Voter: структура и лучшие практики

### Один voter — одна сущность

```php
// ХОРОШО
final class PostVoter extends Voter { /* правила для Post */ }
final class OrderVoter extends Voter { /* правила для Order */ }
final class UserVoter extends Voter { /* правила для User */ }

// ПЛОХО
final class EverythingVoter extends Voter { /* Post, Order, User, Comment, ... */ }
```

### Атрибуты — константы, не строки

```php
// ХОРОШО
final class PostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';

    public function edit(Post $post): void
    {
        $this->denyAccessUnlessGranted(self::EDIT, $post);
    }
}

// ПЛОХО (опечатка не найдётся)
$this->denyAccessUnlessGranted('POST_EDIT', $post);  // в одном месте
$this->isGranted('POST_EDI', $post);                  // в другом — typo
```

### `supports()` — узкий

```php
protected function supports(string $attribute, mixed $subject): bool
{
    // Только Post. Только наши атрибуты.
    return $subject instanceof Post
        && in_array($attribute, [self::EDIT, self::DELETE], true);
}
```

Не `return true;`. Иначе voter участвует в решении по `User`, `Order` — и может случайно проголосовать.

### `voteOnAttribute()` — чистая функция

```php
protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
{
    /** @var Post $post */
    return match ($attribute) {
        self::EDIT   => $post->getAuthor() === $user
                    || $user->hasRole('ROLE_ADMIN'),
        self::DELETE => $post->getAuthor() === $user
                    && !$post->isLocked(),
    };
}
```

Нет side effects. Нет вызовов БД. Нет HTTP. Чистое решение по данным.

### Если правило сложное — вынесите в домен

```php
// domain/Post/Post.php
final class Post
{
    public function canBeEditedBy(User $user): bool
    {
        if ($this->author === $user) return true;
        if ($user->hasRole('ROLE_ADMIN')) return true;
        if ($user->hasRole('ROLE_EDITOR')
            && $user->isAssignedTo($this->category)) return true;
        return false;
    }
}
```

```php
// src/Security/PostVoter.php
protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
{
    /** @var Post $post */
    return match ($attribute) {
        self::EDIT   => $post->canBeEditedBy($user),
        self::DELETE => $post->canBeDeletedBy($user),
    };
}
```

Правила живут в домене. Voter — тонкий адаптер. Тесты на `Post::canBeEditedBy()` не нужны Symfony.

---

## Антипаттерны

### 1. Voter на всё

```php
// АНТИПАТТЕРН: voter для «авторизован или нет»
final class AuthVoter extends Voter
{
    public const IS_AUTHENTICATED = 'IS_AUTHENTICATED';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::IS_AUTHENTICATED;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
    {
        return true; // всегда true, если дошли до voter
    }
}
```

Это то, что делает `ROLE_USER` в `access_control`. Voter не нужен.

### 2. Роль для object-level

```php
// АНТИПАТТЕРН: «я admin, значит я могу удалить ЧУЖОЙ пост»
// Но: пост закреплён. Даже admin не должен удалять.
$this->denyAccessUnlessGranted('ROLE_ADMIN');
$this->postRepo->delete($post);  // удалён закреплённый пост!
```

Роль не знает, что пост закреплён. Voter знает.

### 3. `if-else` в контроллере вместо voter

```php
// АНТИПАТТЕРН:
public function edit(Post $post): Response
{
    if ($post->getAuthor() !== $this->user
        && !$this->user->hasRole('ROLE_ADMIN')) {
        throw new AccessDeniedException();
    }
    // ...
}
```

Правило размазано по контроллерам. В Twig нет `is_granted`. В сервисе нет проверки. Каждое место — потенциальный пропуск.

### 4. Voter ходит в БД

```php
// АНТИПАТТЕРН:
protected function voteOnAttribute(string $attribute, mixed $subject, UserInterface $user): bool
{
    $permissions = $this->permRepo->findForUser($user, $subject);  // ← БД в voter
    return $permissions->canEdit();
}
```

Voter — не место для I/O. Данные должны быть в объекте. Если их нет — загрузите их в application-слое перед вызовом `isGranted()`.

### 5. `denyAccessUnlessGranted` после мутации

```php
// АНТИПАТТЕРН:
public function update(Post $post, EditForm $form): Response
{
    $post->setTitle($form->title);    // ← мутация ДО проверки
    $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);  // ← проверка ПОСЛЕ

    // Если 403 — пост уже изменён в памяти.
    // Если транзакция не откатилась — данные испорчены.
}
```

Проверка **до** мутации. Всегда.

---

## Итого

```
┌─────────────────────────────────────────────────────────────────────┐
│  REQUEST                                                            │
│                                                                     │
│  1. Firewall: кто ты?                                               │
│     form_login / jwt / basic → User object + roles[]                │
│                                                                     │
│  2. access_control: можно ли этот URL?                              │
│     /admin → ROLE_ADMIN → 403 или continue                         │
│                                                                     │
│  3. Voter: можно ли ЭТО действие над ЭТИМ объектом?                 │
│     PostVoter::EDIT + $post → canEdit($post, $user) → true/false   │
│                                                                     │
│  4. 200 / 403                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

**Роль** — «какой ты». Глобально. Без объекта.
**Voter** — «можно ли *тебе* *это* *тут*». Локально. С объектом.

Если в правиле есть `if ($obj->getX() === $user)` — это voter. Без исключений.

> ⏱ 40.1s · prompt 39149 tok · output 5140 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о маппинге Value Objects в Doctrine, когда домен требует неизменяемости и валидности. Это очень практичный вопрос, который встречается в любом DDD-проекте на Symfony/Doctrine.

Что нужно покрыть:
1. Почему это вообще проблема (DO vs VO mismatch)
2. Основные способы маппинга:
   - CUSTOM TYPE (DbType)
   - Атрибуты/аннотации с custom type
   - Embedded (embeddable)
   - Ручной mapping в repository (anti-pattern, но встречается)
   - Composite key / multiple columns
3. Для каждого: плюсы, минусы, подводные камни
4. Конкретные примеры кода
5. Типичные ловушки:
   - Equality (==) vs identity
   - NULL handling
   - Dirty checking (UnitOfWork не видит изменение VO)
   - Immutable VO + Doctrine change tracking
   - Serialization/denormalization
   - Validation in constructor vs after hydration
   - Миграции (change of type)
   - Query: можно ли фильтровать по VO
6. Рекомендации

Структура:
- Проблема: DO mutable, VO immutable. Doctrine строит объекты через конструктор + setters. VO не имеет setters.
- Способ 1: CUSTOM TYPE (самый частый)
- Способ 2: Embeddable
- Способ 3: Несколько колонок (composite)
- Подводные камни (dirty checking, equality, null, query)
- Практические рекомендации

Буду честен: нет идеального решения. Каждое имеет trade-offs. Покажу все варианты с кодом и буду конкретен в подводных камнях.

</details>

**Кандидат:**

## Суть проблемы

Doctrine строит объекты так: конструктор без аргументов → заполнение свойств через reflection → `UnitOfWork` следит за изменениями через snapshot-сравнение.

Value Object — это:
- **Неизменяемость**: нет setters. Поля `readonly` или `private` без мутаторов.
- **Валидность**: инвариант проверяется в конструкторе. Не существует «невалидного» VO.
- **Идентичность через значение**: `new Email("a@b.com") === new Email("a@b.com")` → `true`.

Это два разных мира. Doctrine не знает, что `Email` — это «один объект», а не «три свойства». Он видит `object` и не понимает, как его сериализовать, сравнивать, сравнивать на изменение.

---

## Способ 1: Custom Type (DbType)

Самый частый подход. Одна колонка = один VO.

### Реализация

```php
// src/Domain/User/Email.php
declare(strict_types=1);

namespace App\Domain\User;

final readonly class Email
{
    public function __construct(
        public string $value,
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \App\Domain\User\InvalidEmail($value);
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

```php
// src/Infrastructure/Persistence/Doctrine/Types/EmailType.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Types;

use App\Domain\User\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class EmailType extends Type
{
    public const NAME = 'email';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(int $columnLength, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL(['length' => 255]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        if ($value === null) {
            return null;
        }
        return new Email((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        /** @var Email $value */
        return $value->value;
    }

    public function getDatabaseType(string $typeName, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([]);
    }
}
```

```php
// src/Infrastructure/Persistence/Doctrine/Entity/UserEntity.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\User\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class UserEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private \Symfony\Component\Uid\Uuid $id;

    #[ORM\Column(type: EmailType::NAME, nullable: true)]
    private ?Email $email;

    #[ORM\Column(type: EmailType::NAME, nullable: true)]
    private ?Email $secondaryEmail;
}
```

### Регистрация типа

```php
// config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            email: App\Infrastructure\Persistence\Doctrine\Types\EmailType
```

Или программно (в `bootstrap`):

```php
use Doctrine\DBAL\Types\Type;
use App\Infrastructure\Persistence\Doctrine\Types\EmailType;

Type::addType(EmailType::NAME, EmailType::class);
```

### Использование в repository

```php
// src/Infrastructure/Persistence/Doctrine/DoctrineUserRepository.php
final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(User $user): void
    {
        $entity = UserEntity::fromDomain($user);
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function findById(UserId $id): ?User
    {
        $entity = $this->em->find(UserEntity::class, $id->value);
        return $entity !== null ? User::fromEntity($entity) : null;
    }
}
```

### Плюсы

- Одна колонка. Миграции — одна колонка `VARCHAR(255)`.
- Валидность гарантирована: `convertToPHPValue` вызывает конструктор → `InvalidEmail` при повреждённых данных.
- Читаемость: `type: email` в mapping — сразу понятно, что это.
- Не зависит от реализации VO: можно менять внутренность (добавить `domain`, `local`), маппинг не тронем.

### Минусы и подводные камни

**1. Dirty checking сломан.**

```php
// UnitOfWork делает snapshot:
//   BEFORE: [email => Email("a@b.com")]
//   AFTER:  [email => Email("a@b.com")]
//
// Сравнение: Email("a@b.com") == Email("a@b.com")
//
// PHP: == на объектах → сравнение свойств (если нет __equals)
// Но: readonly-свойства, аутбоксы, сложные VO — == может не сработать как ожидается.
//
// Хуже: если VO содержит массив или другой VO:
//   Address(street: "Main", city: "Berlin")
//   ==
//   Address(street: "Main", city: "Berlin")
//
// PHP: == на объектах — сравнение класса + свойств рекурсивно.
// Но: если в VO есть WeakMap, Closure, или lazy-поле — == может упасть или дать false.
```

**Практическое следствие:** если вы мутируете агрегат и `flush()` не генерирует `UPDATE` — это потому, что UnitOfWork не увидел «изменение». Решение:

```php
// Вариант A: принудительный refresh
$this->em->refresh($entity);
$this->em->flush();

// Вариант B: replace entity (удалить + persist заново)
$this->em->remove($entity);
$this->em->persist($entity);
$this->em->flush();

// Вариант C: переопределить __equals в VO (не рекомендуется для readonly)
// Вариант D: принудительное изменение через replace-паттерн
$entity->email = new Email("b@b.com");  // новое значение → == false → UPDATE
```

**2. NULL handling.**

```php
// convertToPHPValue(null) → null
// Но: домен требует Email (не ?Email)
//
// В домене:  User::email() → Email (не nullable)
// В сущности: UserEntity::$email → ?Email (nullable: true)
//
// Несоответствие: домен говорит «email обязателен»,
// БД говорит «может быть null».
//
// Решение: валидация на границе.
//   UserEntity::fromDomain() → если $user->email() === null → throw
//   User::fromEntity() → если $entity->email === null → throw (или fallback)
```

**3. Query: нельзя фильтровать по «внутренности» VO.**

```php
// ХОРОШО:
$qb->andWhere('u.email = :email')
   ->setParameter('email', new Email("a@b.com"));
// Doctrine: new Email("a@b.com") → convertToDatabaseValue → "a@b.com" → WHERE email = 'a@b.com'

// ПЛОХО (не работает):
$qb->andWhere('u.email.domain = :domain');  // ← нет такого. Email — атом.
// Если нужно фильтровать по домену — нужна отдельная колонка.
```

**4. Миграции при изменении VO.**

```php
// Было: Email(value: string)
// Стало: Email(local: string, domain: string)
//
// БД: одна колонка VARCHAR.
// Домен: два поля.
//
// Миграция:
//   1. Добавить колонки local, domain
//   2. Data migration: SPLIT(email) → local, domain
//   3. Сменить type в mapping
//   4. Удалить колонку email (на следующей итерации)
//
// Риск: во время data migration есть окно, где данные в двух форматах.
```

**5. `convertToPHPValue` — единственная точка валидации.**

```php
public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
{
    return new Email((string) $value);  // ← валидация здесь
}
```

Если в БД лежит `"not-an-email"` (ручной insert, старый код, race condition) — `convertToPHPValue` бросит `InvalidEmail`. Приложение упадёт при чтении. Это **feature** (better to fail early) или **bug** (прерывает весь request), в зависимости от контекста.

---

## Способ 2: Embeddable (несколько колонок = один VO)

Когда VO — это не одна строка, а структура.

### Пример: Money

```php
// src/Domain/Order/Money.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class Money
{
    public function __construct(
        public int    $amount,     // в центов: 1050 = 10.50
        public string $currency,   // "EUR"
    ) {
        if ($amount < 0) throw new \App\Domain\Order\NegativeAmount($amount);
        if (!preg_match('/^[A-Z]{3}$/', $currency)) throw new \App\Domain\Order\InvalidCurrency($currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \App\Domain\Order\CurrencyMismatch($this->currency, $other->currency);
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
```

```php
// src/Infrastructure/Persistence/Doctrine/Mapping/MoneyEmbeddable.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapping;

use App\Domain\Order\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class MoneyEmbeddable
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

    public function toDomain(): Money
    {
        return new Money($this->amount, $this->currency);
    }

    public static function fromDomain(Money $money): self
    {
        return new self($money->amount, $money->currency);
    }
}
```

```php
// src/Infrastructure/Persistence/Doctrine/Entity/OrderEntity.php
#[ORM\Entity]
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private \Symfony\Component\Uid\Uuid $id;

    #[ORM\Embedded(class: MoneyEmbeddable::class)]
    private MoneyEmbeddable $total;

    #[ORM\Embedded(class: MoneyEmbeddable::class)]
    private MoneyEmbeddable $subtotal;
}
```

### Плюсы

- VO остаётся единым. `Money::add()`, `Money::equals()` — без изменений.
- Миграция: две колонки `amount INT, currency VARCHAR(3)`. Явно.
- Query: `WHERE o.total_amount = 1050 AND o.total_currency = 'EUR'` — работает.
- Нет custom type. Нет `convertToPHPValue`-магии.

### Минусы и подводные камни

**1. Embeddable — мутабельный по своей природе.**

```php
#[ORM\Embeddable]
final class MoneyEmbeddable
{
    // Нет readonly. Нет валидации в конструкторе.
    // Doctrine заполняет свойства через reflection.
    // Невозможно проверить инвариант при hydrate.
}
```

Решение: валидация в `toDomain()` (конструктор `Money` проверит). Но между hydrate и `toDomain()` объект «невалидный» существует в памяти.

**2. Dirty checking — частично работает.**

```php
// UnitOfWork видит:
//   BEFORE: [total => MoneyEmbeddable(amount: 1050, currency: "EUR")]
//   AFTER:  [total => MoneyEmbeddable(amount: 2000, currency: "EUR")]
//
// Сравнение: amount 1050 ≠ 2000 → UPDATE. Работает.
//
// Но:
//   BEFORE: [total => MoneyEmbeddable(amount: 1050, currency: "EUR")]
//   AFTER:  [total => MoneyEmbeddable(amount: 1050, currency: "EUR")]
//
// Сравнение: всё равно → no UPDATE. OK.
//
// Ловушка:
//   Если вы делаете:
//   $entity->total = MoneyEmbeddable::fromDomain(new Money(1050, "EUR"));
//   (то же значение, но новый объект)
//
// UnitOfWork: == сравнение → true → no UPDATE. OK, не баг.
// Но: если amount=1050, currency="EUR" → amount=1050, currency="USD"
// UnitOfWork: currency EUR ≠ USD → UPDATE. OK.
```

**3. NULL handling.**

```php
#[ORM\Embedded(class: MoneyEmbeddable::class, nullable: true)]
private ?MoneyEmbeddable $total;
```

`nullable: true` на `@Embedded` — это `NULL` в *обе* колонки (`amount NULL, currency NULL`). Нельзя иметь `amount = 0, currency = NULL`. Либо оба null, либо оба set.

**4. Query по «части» VO.**

```php
// Работает:
$qb->andWhere('o.total.currency = :ccy')
   ->setParameter('ccy', 'EUR');

// Не работает (нет такого):
$qb->andWhere('o.total = :money')
   ->setParameter('money', new Money(1050, 'EUR'));
// ← Embeddable не поддерживает = comparison в DQL
```

**5. Миграция при изменении структуры VO.**

```php
// Было: Money(amount: int, currency: string)
// Стало: Money(amount: int, currency: string, precision: int)
//
// Миграция: добавить колонку precision.
// Old rows: precision = NULL → need data migration.
// Mapping: добавить поле в Embeddable.
// toDomain(): new Money($this->amount, $this->currency, $this->precision ?? 2)
```

---

## Способ 3: Несколько отдельных колонок (без Embeddable)

Когда не хочется вводить Embeddable-класс.

```php
#[ORM\Entity]
final class OrderEntity
{
    #[ORM\Column(type: 'integer')]
    private int $totalAmount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;
}
```

```php
// В repository:
$entity->totalAmount   = $order->total()->amount;
$entity->totalCurrency = $order->total()->currency;

// Извлечение:
$total = new Money($entity->totalAmount, $entity->totalCurrency);
```

### Плюсы

- Минимум магии. Нет Embeddable, нет Type.
- Миграции — просто колонки.
- Query — обычный `WHERE total_currency = 'EUR'`.

### Минусы

- Разбросанность: `totalAmount` и `totalCurrency` — две независимые переменные. Можно случайно установить только одну.
- Нет инкапсуляции: `new Money(1050, 'EUR')` проверяется, но `$entity->totalCurrency = 'XXX'` — нет.
- При 5 VO в сущности — 10+ колонок, маппинг размазан.

**Когда оправдан:** 2–3 поля, VO простой, не планируется изменение структуры.

---

## Сравнение

| Критерий | Custom Type | Embeddable | Отдельные колонки |
|---|---|---|---|
| **Одна колонка?** | Да | Нет (2+) | Нет (2+) |
| **Валидация при hydrate** | Да (конструктор) | Частично (toDomain) | Нет (ручная) |
| **Dirty checking** | Сломан для сложных VO | Работает (скалярные поля) | Работает |
| **Query по VO** | `= :vo` (через type) | Частично (по подполям) | Частично (по подполям) |
| **Query по «внутренности»** | Нет | Да | Да |
| **NULL handling** | `?VO` | `?Embeddable` (оба null) | Каждая колонка отдельно |
| **Миграция при изменении VO** | Сложная (data migration) | Средняя (добавить поле) | Средняя (добавить колонку) |
| **Читаемость mapping** | Высокая (`type: email`) | Средняя | Низкая (2+ поля) |
| **Количество PHP-файлов** | 2 (Type + VO) | 3 (Embeddable + VO + Entity) | 1 (Entity) |
| **Подходит для** | Simple VO (Email, Phone, UUID) | Structured VO (Money, Address, DateRange) | Простые пары (amount+currency) |

---

## Подводные камни, которые встречаются во всех способах

### 1. `==` vs `equals()`

```php
// UnitOfWork использует == (PHP object comparison)
// Email("a@b.com") == Email("a@b.com") → true (сравнение свойств)
// Email("a@b.com") === Email("a@b.com") → false (разные объекты)

// Но: если VO содержит Closure, WeakMap, или lazy-поле:
// == может дать false или упасть.

// Решение: не полагаться на ==. Явно сравнивать.
// В домене: equals()
// В persistence: replace-паттерн (new object → flush)
```

### 2. VO в домене ≠ VO в сущности

```php
// domain/Email.php  — final readonly class, equals(), __toString()
// entity/UserEntity.php  — ?Email $email (nullable)

// Несоответствие:
//   Домен: email обязателен
//   БД: email nullable

// Решение: на границе (repository):
//   fromDomain: if ($user->email() === null) throw
//   toDomain:   if ($entity->email === null) throw (или fallback)
```

### 3. `convertToPHPValue` — единственная защита

```php
// В БД: "not-an-email"
// convertToPHPValue → new Email("not-an-email") → InvalidEmail
//
// Это хорошо (fail fast).
// Но: если это в loop (find() на 1000 записей) — одна битая запись
//      убивает весь request.
//
// Решение: try/catch в repository. Логировать. Вернуть null.
// Или: data migration, чтобы починить данные.
```

### 4. Composite VO + Query

```php
// Money(amount: 1050, currency: "EUR")
//
// Query: WHERE total = new Money(1050, "EUR")
//
// Custom Type: работает (convertToDatabaseValue → "1050 EUR" → WHERE total = '1050 EUR')
// Embeddable: НЕ работает (нет = comparison для embedded)
//             → WHERE total_amount = 1050 AND total_currency = 'EUR'
//
// Если VO — это «единая сущность», но для query нужна «распаковка» —
// Embeddable + явные условия в DQL.
```

### 5. Миграции: изменение структуры VO

```php
// Было: Address(street: string, city: string)
// Стало: Address(street: string, city: string, zipCode: string)
//
// Custom Type: одна колонка VARCHAR. Data migration: SPLIT.
// Embeddable: две колонки → три. Data migration: NULL → default.
//
// Риск: во время миграции есть окно, где данные в двух форматах.
// Решение: backward-compatible VO (zipCode nullable → default "00000").
```

### 6. VO в Association (ManyToMany)

```php
// Post hasMany Tag
// Tag — VO? No, Tag — сущность (has identity).
//
// Но: если Tag = (name: string, color: string) — это VO?
//
// В Doctrine: ManyToMany требует identity. VO не имеет identity.
//
// Решение: Tag — сущность (id, name, color).
//          Или: Post.tags = JSON array of string.
```

---

## Практические рекомендации

1. **Простой VO (Email, Phone, Uuid, Slug)** → Custom Type. Одна колонка. Чисто.

2. **Структурированный VO (Money, Address, DateRange)** → Embeddable. Несколько колонок. Dirty checking работает.

3. **VO с 2–3 скалярными полями, где query по подполям — редкость** → Отдельные колонки. Минимум магии.

4. **Валидация — в конструкторе VO.** Нигде больше. `convertToPHPValue` → конструктор. `toDomain()` → конструктор. Одна точка.

5. **NULL: явное решение.** Либо VO nullable в сущности (`?Email`), либо нет. Не «и то, и другое».

6. **Dirty checking: не полагаться.** Для сложных VO — replace-паттерн:

```php
// Вместо мутации:
$entity->total = MoneyEmbeddable::fromDomain($order->total());
$this->em->flush();

// Или: replace entity целиком
$this->em->remove($entity);
$this->em->persist($entity);
$this->em->flush();
```

7. **Query: знайте, что можно.** Custom Type — `= :vo`. Embeddable — по подполям. Не пытайтесь `WHERE vo = complex_expression`.

8. **Миграция: backward-compatible VO.** Новое поле — nullable + default. Data migration — отдельный шаг. Не «сменил VO → сломалось».

---

## Итого

Нет «правильного» способа. Есть trade-off:

| Хотите | Выберите |
|---|---|
| Минимум кода, одна колонка | Custom Type |
| Чистый dirty checking, query по подполям | Embeddable |
| Минимум магии, максимум контроля | Отдельные колонки |
| VO с 10+ полями, сложная логика | Embeddable + явный mapping в repository |

Главное правило: **VO в домене — неизменяемый и валидный. Маппинг — адаптер. Не подгоняйте VO под Doctrine. Подгоняйте Doctrine под VO.**

> ⏱ 47.0s · prompt 44115 tok · output 5916 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата с корнем и коллекцией дочерних сущностей через Doctrine. Нужно разобрать:
1. Маппинг коллекций (OneToMany, ManyToOne, cascades, orphanRemoval)
2. Границы транзакции
3. Кто и когда вызывает flush

Выберу пример: Order (корень) + OrderItem (дочерние сущности). Это классический и понятный пример.

Ключевые аспекты:
- Order как aggregate root
- OrderItem как child entity (не aggregate root, не имеет внешней identity в контексте этого aggregate)
- Mapping: OneToMany с mappedBy, cascade: persist, remove; orphanRemoval: true
- Транзакция: кто открывает, кто коммитит
- Flush: кто вызывает, где граница
- Полиморфизм vs concrete classes
- UnitOfWork и dirty checking
- Проблема: OrderItem не должен быть доступен напрямую извне aggregate

Структурирую ответ:
1. Доменная модель (Order, OrderItem)
2. Mapping (Entity, relationships, cascades)
3. Транзакции и flush (Application Service → Repository)
4. Подводные камни (cascades, orphanRemoval, lazy loading, UnitOfWork)
5. Код целиком

Давайте сделаю пример Order → OrderItem → OrderItemDiscount (вложенный VO-like). Нет, лучше Order → OrderItem, и OrderItem имеет Money (embedded VO). Это покажет и коллекцию, и VO внутри.

На самом деле, давай проще: Order (root) → items (collection of OrderItem). OrderItem содержит product_id, quantity, unit_price. Это чистый пример агрегата.

Ключевые решения, которые нужно обосновать:
1. OrderItem — child entity, не aggregate root. Его identity имеет смысл только в контексте Order.
2. cascade: persist + remove, orphanRemoval: true — потому что items живут и умирают вместе с Order.
3. Flush вызывает Application Service (или Command Handler), НЕ Repository. Repository — dumb adapter.
4. Транзакция — на уровне Application Service (Command Handler). Одна транзакция = один use case.
5. Lazy loading — опасен в aggregate. Лучше eager или явная загрузка.
6. UnitOfWork — следит за Order, но items — часть того же UnitOfWork через cascade.

Подводные камни:
- orphanRemoval + cascade: remove = удалить item из коллекции → DELETE из БД. Это правильно для aggregate.
- Но: если item является частью другого aggregate (например, StockItem) — cascade remove сломает.
- Lazy loading: $order->items — Proxy. Если сериализовать или JSON_encode — fatal error.
- UnitOfWork: если мутировать $order->items, UnitOfWork должен видеть изменение. Но если items — lazy proxy и не loaded — изменение может потеряться.
- Flush в Repository vs в Service: если в Repository — Repository не знает о транзакции. Если в Service — Service управляет жизненным циклом.

Давайте напишу развёрнутый ответ с кодом.

</details>

**Кандидат:**

## Агрегат: Order → OrderItem

Классический пример: заказ (root) содержит позиции (children). Позиция не имеет смысла вне заказа. Её identity — `(orderId, productId)`, а не просто `id`.

---

## Доменная модель

```php
// src/Domain/Order/OrderId.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class OrderId
{
    public function __construct(
        public string $value,
    ) {}

    public static function generate(): self
    {
        return new self(\Symfony\Component\Uid\Uuid::v7()->toRfc4122());
    }
}
```

```php
// src/Domain/Order/OrderItem.php
declare(strict_types=1);

namespace App\Domain\Order;

final readonly class OrderItem
{
    public function __construct(
        public string $productId,
        public int    $quantity,
        public int    $unitPriceCents,  // в центов
    ) {
        if ($quantity < 1) {
            throw new \App\Domain\Order\InvalidQuantity($quantity);
        }
        if ($unitPriceCents < 0) {
            throw new \App\Domain\Order\NegativePrice($unitPriceCents);
        }
    }

    public function totalCents(): int
    {
        return $this->quantity * $this->unitPriceCents;
    }
}
```

```php
// src/Domain/Order/Order.php  — AGGREGATE ROOT
declare(strict_types=1);

namespace App\Domain\Order;

final class Order
{
    private OrderId $id;
    private string  $customerId;
    /** @var list<OrderItem> */
    private array   $items = [];
    private string  $status;   // pending → confirmed → shipped → delivered
    private \DateTimeImmutable $createdAt;

    // Корень создаётся только через factory. Состояние всегда валидное.
    public static function create(string $customerId, array $items): self
    {
        if ($items === []) {
            throw new \App\Domain\Order\EmptyOrder();
        }

        $order = new self();
        $order->id        = OrderId::generate();
        $order->customerId = $customerId;
        $order->status    = 'pending';
        $order->createdAt = new \DateTimeImmutable();

        foreach ($items as $item) {
            $order->items[] = $item;
        }

        return $order;
    }

    // Mutations — только через методы. Внешний код не трогает $items напрямую.
    public function addItem(OrderItem $item): void
    {
        if ($this->status !== 'pending') {
            throw new \App\Domain\Order\OrderNotPending($this->status);
        }
        $this->items[] = $item;
    }

    public function removeItem(string $productId): void
    {
        if ($this->status !== 'pending') {
            throw new \App\Domain\Order\OrderNotPending($this->status);
        }
        $this->items = array_values(array_filter(
            $this->items,
            static fn (OrderItem $i): bool => $i->productId !== $productId,
        ));

        if ($this->items === []) {
            throw new \App\Domain\Order\CannotRemoveLastItem();
        }
    }

    public function confirm(): void
    {
        if ($this->status !== 'pending') {
            throw new \App\Domain\Order\InvalidTransition($this->status, 'confirmed');
        }
        $this->status = 'confirmed';
    }

    // Getters
    public function id(): OrderId { return $this->id; }
    public function customerId(): string { return $this->customerId; }
    public function status(): string { return $this->status; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return list<OrderItem> */
    public function items(): array
    {
        return $this->items;  // возвращаем копию? Нет — возвращаем reference.
                              // Но внешний код не может мутировать, потому что
                              // OrderItem — readonly. Можно только replace весь массив.
    }

    public function totalCents(): int
    {
        return array_sum(array_map(
            static fn (OrderItem $i): int => $i->totalCents(),
            $this->items,
        ));
    }

    // Приватный конструктор: нельзя создать «пустой» Order.
    private function __construct() {}
}
```

**Ключевые решения домена:**
- `OrderItem` — **не** aggregate root. У него нет `id`. Он живёт внутри `Order`.
- `Order` — единственный вход в aggregate. Внешний код не может сделать `$order->items[] = new OrderItem(...)` — только через `addItem()`.
- `OrderItem` — `readonly`. Изменить количество — удалить и добавить заново.

---

## Mapping: сущности Doctrine

Здесь начинается «перевод» домена на язык БД.

### OrderEntity (root)

```php
// src/Infrastructure/Persistence/Doctrine/Entity/OrderEntity.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Infrastructure\Persistence\Doctrine\Types\OrderIdType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\Index(columns: ['customer_id'])]
#[ORM\Index(columns: ['status'])]
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: OrderIdType::NAME)]
    private OrderId $id;

    #[ORM\Column(type: 'string', length: 64)]
    private string $customerId;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity: OrderItemEntity::class,
     *     mappedBy: "order",
     *     cascade: {"persist", "remove"},
     *     orphanRemoval: true,
     *     fetch: EAGER,
     * )
     * @ORM\JoinColumn onDelete: "CASCADE"
     * @var Collection<int, OrderItemEntity>
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    // --- Mapping-доступ (только для Doctrine, не для домена) ---

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    /** @return Collection<int, OrderItemEntity> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItemEntity(OrderItemEntity $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    public function removeItemEntity(OrderItemEntity $item): void
    {
        $this->items->removeElement($item);
    }

    public function id(): OrderId { return $this->id; }
    public function setOrderId(OrderId $id): void { $this->id = $id; }

    public function customerId(): string { return $this->customerId; }
    public function setCustomerId(string $id): void { $this->customerId = $id; }

    public function status(): string { return $this->status; }
    public function setStatus(string $s): void { $this->status = $s; }

    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): void { $this->createdAt = $dt; }

    // --- Конвертация ---

    public static function fromDomain(Order $order): self
    {
        $entity = new self();
        $entity->id         = $order->id();
        $entity->customerId = $order->customerId();
        $entity->status     = $order->status();
        $entity->createdAt  = $order->createdAt();

        foreach ($order->items() as $item) {
            $itemEntity = new OrderItemEntity();
            $itemEntity->setOrderId($order->id());
            $itemEntity->setProductId($item->productId);
            $itemEntity->setQuantity($item->quantity);
            $itemEntity->setUnitPriceCents($item->unitPriceCents);
            $entity->addItemEntity($itemEntity);
        }

        return $entity;
    }

    public function toDomain(): Order
    {
        // Не вызываем Order::create() — там generate ID, validation.
        // Используем reflection или dedicated reconstitution method.
        $order = (new \ReflectionClass(Order::class))->newInstanceWithoutConstructor();

        $refId   = new \ReflectionProperty(Order::class, 'id');
        $refId->setAccessible(true);
        $refId->setValue($order, $this->id);

        $refCust = new \ReflectionProperty(Order::class, 'customerId');
        $refCust->setAccessible(true);
        $refCust->setValue($order, $this->customerId);

        $refStatus = new \ReflectionProperty(Order::class, 'status');
        $refStatus->setAccessible(true);
        $refStatus->setValue($order, $this->status);

        $refCreated = new \ReflectionProperty(Order::class, 'createdAt');
        $refCreated->setAccessible(true);
        $refCreated->setValue($order, $this->createdAt);

        $refItems = new \ReflectionProperty(Order::class, 'items');
        $refItems->setAccessible(true);

        $items = [];
        foreach ($this->items as $itemEntity) {
            $items[] = new \App\Domain\Order\OrderItem(
                productId:    $itemEntity->productId(),
                quantity:     $itemEntity->quantity(),
                unitPriceCents: $itemEntity->unitPriceCents(),
            );
        }
        $refItems->setValue($order, $items);

        return $order;
    }
}
```

### OrderItemEntity (child)

```php
// src/Infrastructure/Persistence/Doctrine/Entity/OrderItemEntity.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Order\OrderId;
use App\Infrastructure\Persistence\Doctrine\Types\OrderIdType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
#[ORM\Index(columns: ['order_id', 'product_id'], unique: true)]
final class OrderItemEntity
{
    // Composite PK: order_id + product_id
    // Позиция уникальна в рамках заказа.

    #[ORM\Id]
    #[ORM\Column(type: OrderIdType::NAME)]
    private OrderId $orderId;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    private string $productId;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'integer')]
    private int $unitPriceCents;

    // --- Mapping ---

    public function setOrderId(OrderId $id): void { $this->orderId = $id; }
    public function orderId(): OrderId { return $this->orderId; }

    public function setProductId(string $id): void { $this->productId = $id; }
    public function productId(): string { return $this->productId; }

    public function setQuantity(int $q): void { $this->quantity = $q; }
    public function quantity(): int { return $this->quantity; }

    public function setUnitPriceCents(int $c): void { $this->unitPriceCents = $c; }
    public function unitPriceCents(): int { return $this->unitPriceCents; }
}
```

### Custom Type для OrderId

```php
// src/Infrastructure/Persistence/Doctrine/Types/OrderIdType.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Types;

use App\Domain\Order\OrderId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class OrderIdType extends Type
{
    public const NAME = 'order_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(int $columnLength, AbstractPlatform $platform): string
    {
        return 'UUID';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?OrderId
    {
        if ($value === null) return null;
        return new OrderId((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;
        /** @var OrderId $value */
        return $value->value;
    }
}
```

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            order_id: App\Infrastructure\Persistence\Doctrine\Types\OrderIdType
```

---

## Ключевые решения в mapping — и почему

### 1. `cascade: {"persist", "remove"}`

```yaml
cascade: {"persist", "remove"}
```

| Cascade | Что делает |
|---|---|
| `persist` | `em->persist($order)` → Doctrine автоматически persist'ит все items из коллекции |
| `remove` | `em->remove($order)` → Doctrine автоматически удалит все items из БД |
| `merge` | **Не используем**. `merge` — legacy, создаёт duplicate objects |
| `refresh` | **Не используем**. `refresh($order)` без cascade refresh не тронет items |
| `detach` | **Не используем**. Опасен в aggregate |

**Почему не `cascade: all`:** `all` включает `merge` и `refresh`, которые мы не хотим. Явное перечисление — явный контракт.

### 2. `orphanRemoval: true`

```yaml
orphanRemoval: true
```

**Что значит:** если вы удаляете `OrderItemEntity` из коллекции `$order->items` — Doctrine автоматически выполнит `DELETE FROM order_items WHERE ...`.

```php
// Без orphanRemoval:
$order->items->removeElement($item);
$this->em->flush();
// Результат: item остаётся в БД (orphan). Нет DELETE.

// С orphanRemoval:
$order->items->removeElement($item);
$this->em->flush();
// Результат: DELETE FROM order_items WHERE order_id = ? AND product_id = ?
```

**Для aggregate это правильно:** item без order не существует. Удалили из коллекции — удалили из БД.

**Опасность:** если item одновременно принадлежит другому aggregate (например, `StockItem` с `cascade: persist`), `orphanRemoval` удалит его из-под ног. В нашем случае `OrderItem` принадлежит только `Order` — безопасно.

### 3. `fetch: EAGER`

```yaml
fetch: EAGER
```

**Почему:** `Order` — aggregate. Items — часть aggregate. Если `fetch: LAZY` (default):

```php
$order = $this->repo->find($id);
// $order->items — Proxy (lazy)

json_encode($order);  // FATAL: Cannot serialize lazy proxy
// Или:
$order->items()->count();  // N+1: SELECT * FROM order_items WHERE order_id = ?
```

Для aggregate **всегда** загружайте детей. Это не N+1, это **один запрос** на aggregate. `EAGER` гарантирует, что `$order->items` — реальный `ArrayCollection`, а не `Proxy`.

**Альтернатива:** `DQL` с `JOIN FETCH`:

```php
$qb->select('o', 'oi')
   ->from(OrderEntity::class, 'o')
   ->join('o.items', 'oi')
   ->where('o.id = :id')
   ->setParameter('id', $orderId);
```

Работает, но `EAGER` в mapping проще и не забывает.

### 4. Composite PK для OrderItemEntity

```php
#[ORM\Id]
#[ORM\Column(type: OrderIdType::NAME)]
private OrderId $orderId;

#[ORM\Id]
#[ORM\Column(type: 'string', length: 128)]
private string $productId;
```

**Почему не отдельный `id UUID`:**
- `OrderItem` не имеет identity вне контекста `Order`.
- Composite PK `(order_id, product_id)` — естественная ключевая пара.
- Единственный `id` — избыточность: `order_id` уже есть, `product_id` уже есть.

**Минус:** `em->find(OrderItemEntity::class, $id)` не работает (нет единого `id`). Загрузка — только через `Order`.

**Плюс:** невозможно случайно загрузить `OrderItem` без `Order`. Enforced на уровне схемы.

### 5. `UNIQUE (order_id, product_id)`

```yaml
#[ORM\Index(columns: ['order_id', 'product_id'], unique: true)]
```

Защита от дублей на уровне БД. Даже если доменный код ошибся — БД не даст вставить два одинаковых item в один заказ.

---

## Repository

```php
// src/Domain/Order/OrderRepositoryInterface.php
declare(strict_types=1);

namespace App\Domain\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(OrderId $id): ?Order;

    public function findByCustomerId(string $customerId): array;
}
```

```php
// src/Infrastructure/Persistence/Doctrine/DoctrineOrderRepository.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Order $order): void
    {
        $existing = $this->em->find(OrderEntity::class, $order->id()->value);

        if ($existing === null) {
            // Новый агрегат
            $entity = OrderEntity::fromDomain($order);
            $this->em->persist($entity);
            // НЕ вызываем flush() здесь.
        } else {
            // Существующий: обновляем поля
            $existing->setCustomerId($order->customerId());
            $existing->setStatus($order->status());

            // Синхронизируем items
            $existingIds = [];
            foreach ($existing->getItems() as $item) {
                $existingIds[] = $item->productId();
            }

            $newIds = [];
            foreach ($order->items() as $item) {
                $newIds[] = $item->productId();
            }

            // Удаляем orphan'ы (те, что были раньше, но больше нет)
            foreach ($existing->getItems() as $item) {
                if (!in_array($item->productId(), $newIds, true)) {
                    $existing->removeItemEntity($item);
                    // orphanRemoval: true → Doctrine сгенерирует DELETE
                }
            }

            // Добавляем новые / обновляем существующие
            foreach ($order->items() as $item) {
                $found = false;
                foreach ($existing->getItems() as $itemEntity) {
                    if ($itemEntity->productId() === $item->productId) {
                        $itemEntity->setQuantity($item->quantity);
                        $itemEntity->setUnitPriceCents($item->unitPriceCents);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $newItem = new OrderItemEntity();
                    $newItem->setOrderId($order->id());
                    $newItem->setProductId($item->productId);
                    $newItem->setQuantity($item->quantity);
                    $newItem->setUnitPriceCents($item->unitPriceCents);
                    $existing->addItemEntity($newItem);
                    // cascade: persist → Doctrine persist'ит новый item
                }
            }
        }
        // НЕ вызываем flush() здесь.
    }

    public function findById(OrderId $id): ?Order
    {
        $entity = $this->em->find(OrderEntity::class, $id->value);
        return $entity !== null ? $entity->toDomain() : null;
    }

    public function findByCustomerId(string $customerId): array
    {
        $entities = $this->em->getRepository(OrderEntity::class)
            ->findBy(['customerId' => $customerId]);

        return array_map(
            static fn (OrderEntity $e): Order => $e->toDomain(),
            $entities,
        );
    }
}
```

**Критично: `flush()` не вызывается в Repository.** Repository — «глупый» адаптер. Он знает, *как* сохранить, но не *когда* коммитить.

---

## Транзакция и flush: кто управляет

### Принцип

> **Один use case = одна транзакция = один `flush()` = один `commit()`.**

`flush()` — это «примени все изменения к БД». `commit()` — «закрепи транзакцию». В Doctrine с `autoCommit = false` (default) `flush()` выполняет `COMMIT` после `INSERT/UPDATE/DELETE`. Но логически это одна единица.

### Application Service (Command Handler)

```php
// src/Application/Order/PlaceOrderHandler.php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Stock\StockService;
use Doctrine\ORM\EntityManagerInterface;

final class PlaceOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private StockService             $stock,
        private EntityManagerInterface   $em,
    ) {}

    public function handle(PlaceOrderCommand $cmd): OrderId
    {
        // 1. Домен: создаём агрегат. Все инварианты проверены.
        $items = array_map(
            static fn (array $i): OrderItem => new OrderItem(
                productId:    $i['product_id'],
                quantity:     $i['quantity'],
                unitPriceCents: $i['unit_price'],
            ),
            $cmd->items,
        );

        $order = Order::create($cmd->customerId, $items);

        // 2. Side effect: резервируем库存. Может бросить InsufficientStock.
        //    Если бросит — ничего не сохранено (транзакция не начата).
        $this->stock->reserve($order->items());

        // 3. Сохраняем агрегат.
        $this->orders->save($order);

        // 4. FLUSH + COMMIT. Одна точка. Одна транзакция.
        $this->em->flush();

        return $order->id();
    }
}
```

```php
// src/Application/Order/UpdateOrderHandler.php
final class UpdateOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private EntityManagerInterface   $em,
    ) {}

    public function handle(OrderId $id, UpdateOrderCommand $cmd): OrderId
    {
        // 1. Загрузка агрегата (EAGER: items уже в памяти)
        $order = $this->orders->findById($id)
            ?? throw new \App\Domain\Order\OrderNotFound($id->value);

        // 2. Мутация через доменные методы. Инварианты держит Order.
        if ($cmd->items !== null) {
            foreach ($cmd->items as $item) {
                $order->addItem(new OrderItem(
                    productId:    $item['product_id'],
                    quantity:     $item['quantity'],
                    unitPriceCents: $item['unit_price'],
                ));
            }
        }

        if ($cmd->shouldConfirm) {
            $order->confirm();
        }

        // 3. Сохранение
        $this->orders->save($order);

        // 4. Одна точка flush
        $this->em->flush();

        return $order->id();
    }
}
```

### Почему `flush()` в Handler, а не в Repository?

| Вариант | Проблема |
|---|---|
| `flush()` в Repository | Repository не знает о транзакции. Если Handler вызывает `repo->save()` дважды (например, `save` + `save` после side effect) — два `flush()`, две транзакции. Атомарность сломана. |
| `flush()` в Handler | Одна точка. Handler решает, *когда* коммитить. Если что-то упало между `save()` и `flush()` — транзакция откатывается. |
| `flush()` в Controller | Слишком высоко. Controller не должен знать о UnitOfWork. |

**Правило:** `flush()` — в том слое, который управляет **use case**. Для Symfony-приложения это Application Service / Command Handler.

### Транзакция явно (для сложных сценариев)

```php
public function handle(PlaceOrderCommand $cmd): OrderId
{
    $order = Order::create($cmd->customerId, $cmd->items);

    // Явная транзакция: всё или ничего
    $this->em->transactional(function () use ($order, $cmd) {
        $this->stock->reserve($order->items());  // может бросить

        $this->orders->save($order);

        // Если reserve бросил — save не выполнился.
        // Если save бросил — reserve откатился (та же транзакция).
    });

    return $order->id();
}
```

`$em->transactional()` обёртка:
- `BEGIN`
- closure
- `COMMIT` (если нет исключения)
- `ROLLBACK` (если исключение)

Это то же самое, что `flush()` + `commit()`, но явно. Для простых сценариев `flush()` достаточно (Doctrine с `autoCommit` делает `COMMIT` после `flush()`).

---

## Подводные камни

### 1. `orphanRemoval` + `cascade: remove` = двойное удаление

```php
// Если orphanRemoval: true И cascade: remove:
$order->items->removeElement($item);
$this->em->remove($order);  // remove cascade → remove items
// orphanRemoval тоже сработает → DELETE × 2?
```

Нет, Doctrine умён: он не генерирует двойной `DELETE`. Но если `cascade: remove` **без** `orphanRemoval` — orphan'ы останутся. Поэтому для aggregate: **оба**.

### 2. Lazy proxy в JSON

```php
// Если fetch: LAZY (не наш случай, но если кто-то поменял):
$order = $repo->find($id);
json_encode($order);  // FATAL: Cannot serialize lazy proxy
```

`EAGER` в aggregate — не опция, а **обязательность**.

### 3. Н+1 при `findByCustomerId`

```php
// EAGER на OneToMany:
// SELECT * FROM orders WHERE customer_id = ?
// SELECT * FROM order_items WHERE order_id IN (?, ?, ?)  ← один запрос, IN
//
// Это НЕ N+1. Это 2 запроса на N заказов.
//
// Если бы было LAZY:
// SELECT * FROM orders WHERE customer_id = ?     ← 1
// SELECT * FROM order_items WHERE order_id = ?   ← N (по одному на каждый заказ)
// → N+1
```

`EAGER` на `OneToMany` = `JOIN` или `IN`. Для aggregate — правильно.

### 4. `save()` не идемпотентен для новых entities

```php
// Если $order ещё не persist'ит:
$this->em->persist($entity);  // → INSERT

// Если вызовем save() ещё раз:
$this->em->persist($entity);  // → Doctrine: уже managed, no-op
// Но: если мы создали НОВЫЙ OrderEntity для того же OrderId → INSERT → UNIQUE violation
```

Решение: в `save()` проверяем `find()` (как в нашем репозитории). Если есть — update. Если нет — insert.

### 5. `flush()` после `remove` + `persist`

```php
$this->em->remove($oldEntity);
$this->em->persist($newEntity);
$this->em->flush();
//
// Порядок в БД: DELETE, INSERT (или INSERT, DELETE — зависит от UnitOfWork)
// Если UNIQUE constraint на (order_id, product_id) — может быть conflict
//
// Решение: не «replace», а «update in place»:
$entity->setQuantity($newQty);  // → UPDATE
$this->em->flush();
```

### 6. UnitOfWork и dirty checking для collections

```php
// Doctrine следит за коллекцией через ArrayCollection.
// Если вы делаете:
$order->items[] = new OrderItem(...);  // ← НЕ РАБОТАЕТ. items — private.

// Правильно:
$order->addItem(new OrderItem(...));  // метод Order.
// В репозитории:
$existing->addItemEntity($newItemEntity);  // → ArrayCollection::add()
// UnitOfWork видит: коллекция изменилась → INSERT нового item.
```

**Критично:** мутация коллекции должна идти через `ArrayCollection::add/remove`, а не через прямое присвоение `$this->items = [...]`. Иначе UnitOfWork не увидит изменение.

---

## Схема: полный request

```
PlaceOrderHandler::handle()
    │
    ├── 1. Order::create()              ← домен, валидация, items в памяти
    │
    ├── 2. StockService::reserve()      ← side effect, может бросить
    │   │
    │   └── (если бросило → exception → нет flush → нет commit → откат)
    │
    ├── 3. DoctrineOrderRepository::save()
    │   │
    │   ├── find() → нет → persist(OrderEntity)
    │   │   ├── cascade: persist → persist(每项 OrderItemEntity)
    │   │   └── items → ArrayCollection
    │   │
    │   └── (без flush)
    │
    └── 4. $em->flush()
        │
        ├── INSERT INTO orders (...)
        ├── INSERT INTO order_items (...) × N
        ├── COMMIT
        │
        └── return OrderId
```

**Одна транзакция. Один commit. Атомарность.**

Если `reserve()` бросает на шаге 2 — шага 3 и 4 нет. Ничего не сохранено.

Если `flush()` бросает (UNIQUE violation, constraint) — `ROLLBACK`. Ничего не сохранено.

---

## Итого: правила

| Что | Где | Почему |
|---|---|---|
| `cascade: persist, remove` | Mapping `OneToMany` | Items живут и умирают с Order |
| `orphanRemoval: true` | Mapping `OneToMany` | Удалили из коллекции → DELETE из БД |
| `fetch: EAGER` | Mapping `OneToMany` | Aggregate — всегда с детьми |
| Composite PK | `OrderItemEntity` | Item не имеет identity вне Order |
| `UNIQUE (order_id, product_id)` | Индекс | Защита от дублей на уровне БД |
| `flush()` | Handler / Service | Одна точка коммита на use case |
| `flush()` НЕ в Repository | Repository | Repository — адаптер, не владелец транзакции |
| Мутация через методы | `Order::addItem()` | Инварианты в домене, не в mapping |
| `ArrayCollection::add/remove` | Repository | UnitOfWork видит изменение |

Aggregate — это **логическая граница**. Doctrine не знает про агрегаты. Мы заставляем его уважать границу через `cascade`, `orphanRemoval`, composite PK, `EAGER` и запрет внешнего доступа к items.

> ⏱ 59.9s · prompt 49726 tok · output 8216 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о диагностике и решении N+1 в Doctrine. Это продолжение длинного технического диалога о PHP/Symfony/Doctrine. Нужно:

1. Диагностика — как найти N+1 (инструменты, методы)
2. Способы решения — с trade-offs каждого
3. Конкретные примеры кода
4. Быть честным о подводных камнях

Структура:
- Как диагностировать (пошагово)
- Способы решения (каждый с кодом, плюсами, минусами, когда подходит)
- Сравнительная таблица
- Антипаттерны при "лечении"

Способы решения N+1:
1. JOIN FETCH (DQL / QueryBuilder)
2. EAGER loading в mapping
3. Doctrine Collections + getReference
4. Subselect / Batch fetching (Doctrine 2.14+)
5. Custom hydration (array)
6. DataLoader / DataLoader
7. Denormalization (избыточность в БД)
8. Pagination (ограничение набора)
9. DataLoader pattern (для batch)
10. Materialized views / read models (CQRS)

Давайте структурирую логично: сначала диагностика, потом решения от "простых" к "сложным", с trade-offs.

Нужно быть конкретным, с кодом, и честным о том, что "EAGER везде" — антипаттерн.

</details>

**Кандидат:**

## Диагностика

### Шаг 1: Убедиться, что это N+1, а не другое

N+1 — это **N+1 SELECT-запросов**, где первый загрузил N записей, а каждый из N сделал свой `SELECT`. Симптом: 1 + 100 запросов при 100 записях на странице.

Но «страница тормозит» может быть:
- Медленный запрос (не N+1, а один тяжёлый)
- N+1 (100 запросов по 1 мс = 100 мс суммарно)
- Медленный renderer (Twig, JS)
- Медленный внешний сервис (HTTP к API)
- Блокировки (lock wait)

**Первое, что делаю:** смотрю **количество** запросов, а не их длительность. 100 × 1 мс = N+1. 1 × 500 мс = медленный запрос. Лечение разное.

### Шаг 2: Логи SQL-запросов

```yaml
# config/packages/dev/doctrine.yaml
doctrine:
    orm:
        auto_generate_proxy_classes: true
        auto_generate_hydrators: false
        # Логируем каждый запрос:
        dumper_class: Doctrine\DebugStack\DebugStack
```

```yaml
# config/packages/dev/framework.yaml
framework:
    profiler:
        enabled: true
```

**Web Profiler → Doctrine** — показывает каждый запрос, время, план. Ищу паттерн:

```
SELECT ... FROM orders WHERE id = ?          ← 1 запрос (N)
SELECT ... FROM order_items WHERE order_id = 1   ← 1
SELECT ... FROM order_items WHERE order_id = 2   ← 2
SELECT ... FROM order_items WHERE order_id = 3   ← 3
...
SELECT ... FROM order_items WHERE order_id = 100 ← 100
```

101 запрос. N+1.

### Шаг 3: `Doctrine\ORM\QueryBuilder` + `PDO`-логирование

Если Profiler недоступен (prod):

```php
// src/EventListener/QueryLogger.php
use Doctrine\DBAL\Logging\SQLLogger;

final class QueryLogger implements SQLLogger
{
    private int $count = 0;
    private float $totalMs = 0.0;
    /** @var array<int, array{sql: string, ms: float, params: mixed[]}> */
    private array $queries = [];

    public function startQuery(string $sql, ?array $params = null, ?array $types = null): void
    {
        $this->count++;
        $this->start = microtime(true);
    }

    public function stopQuery(): void
    {
        $ms = (microtime(true) - $this->start) * 1000;
        $this->totalMs += $ms;
        $this->queries[] = [
            'sql'    => $this->lastSql,
            'ms'     => $ms,
        ];
    }

    public function summary(): array
    {
        return [
            'total_queries' => $this->count,
            'total_ms'      => round($this->totalMs, 2),
            'queries'       => $this->queries,
        ];
    }
}
```

```php
// В контроллере (только dev):
$logger = new QueryLogger();
$this->em->getConnection()->setSQLLogger($logger);

// ... бизнес-логика ...

$summary = $logger->summary();
if ($summary['total_queries'] > 20) {
    $this->logger->warning('N+1 suspected', $summary);
}
```

### Шаг 4: `EXPLAIN` на подозрительном запросе

```sql
EXPLAIN ANALYZE
SELECT oi.* FROM order_items oi WHERE oi.order_id = 42;
```

Если каждый из N+1 запросов быстрый (1 мс) — проблема в **количестве**, не в **стоимости**. Если каждый медленный (50 мс) — проблема и в количестве, и в стоимости (индекс).

### Шаг 5: Где именно происходит N+1

Профилирую конкретный метод:

```php
// src/Controller/OrderController.php
public function index(): Response
{
    // Таймстэмп перед
    $t0 = microtime(true);

    $orders = $this->orderRepo->findRecent(20);  // ← подозреваемый

    $t1 = microtime(true);

    // Рендер — тоже подозреваемый
    $html = $this->twig->render('orders/index.html.twig', ['orders' => $orders]);

    $t2 = microtime(true);

    $this->logger->info('timing', [
        'repo_ms'  => round(($t1 - $t0) * 1000),
        'render_ms' => round(($t2 - $t1) * 1000),
    ]);
}
```

Если `repo_ms` = 150 мс, а `render_ms` = 5 мс — проблема в репозитории. Смотрю SQL-логи: 21 запрос → N+1.

---

## Способы решения

### 1. `JOIN FETCH` — явное подключение в запросе

**Самый частый и самый правильный способ.**

```php
// Было (N+1):
public function findRecent(int $limit): array
{
    $orders = $this->em->getRepository(OrderEntity::class)
        ->findBy([], ['createdAt' => 'DESC'], $limit);
    // 1 SELECT orders
    // N SELECT order_items (при первом обращении к $order->items)
}
```

```php
// Стало (2 запроса):
public function findRecent(int $limit): array
{
    $qb = $this->em->createQueryBuilder()
        ->select('o', 'oi')
        ->from(OrderEntity::class, 'o')
        ->leftJoin('o.items', 'oi')
        ->where('o.createdAt > :since')
        ->setParameter('since', (new \DateTimeImmutable('-30 days'))->format('Y-m-d H:i:s'))
        ->orderBy('o.createdAt', 'DESC')
        ->setMaxResults($limit);

    return $qb->getQuery()->getResult();
    // 1 SELECT orders LEFT JOIN order_items
    // 0 SELECT order_items (уже в памяти)
}
```

**Trade-offs:**

| | |
|---|---|
| ✅ Один запрос. Нет N+1. | |
| ✅ Явно: видно в коде, что items загружаются. | |
| ✅ Можно добавить `WHERE` / `HAVING` на joined-таблицу. | |
| ⚠️ `LEFT JOIN` + `setMaxResults` — **опасно**. `setMaxResults(20)` после JOIN может вернуть 20 *строк JOIN*, а не 20 *заказов*. Если у заказа 5 items → 5 строк → `LIMIT 20` вернёт 4 заказа, а не 20. |
| ⚠️ `JOIN FETCH` на `ManyToMany` — Doctrine не поддерживает (ограничение). |
| ⚠️ Результат — плоский массив entities, не `Paginator`. Пагинация сложнее. |
| ⚠️ Если `items` — 1000 записей на заказ → 1 запрос на 20 000 строк. Память. |

**Обход для `setMaxResults`:**

```php
// Сначала получаем ID (1 запрос), потом JOIN (1 запрос):
$ids = $this->em->createQueryBuilder()
    ->select('o.id')
    ->from(OrderEntity::class, 'o')
    ->orderBy('o.createdAt', 'DESC')
    ->setMaxResults(20)
    ->getQuery()
    ->getSingleColumnResult();  // [id1, id2, ... id20]

$orders = $this->em->createQueryBuilder()
    ->select('o', 'oi')
    ->from(OrderEntity::class, 'o')
    ->join('o.items', 'oi')
    ->where('o.id IN (:ids)')
    ->setParameter('ids', $ids)
    ->getQuery()
    ->getResult();
// 2 запроса. Точная пагинация.
```

---

### 2. `EAGER` в mapping

```php
#[ORM\OneToMany(
    targetEntity: OrderItemEntity::class,
    mappedBy: "order",
    cascade: {"persist", "remove"},
    orphanRemoval: true,
    fetch: EAGER,   // ← было LAZY
)]
private Collection $items;
```

**Что происходит:** каждый `SELECT * FROM orders` автоматически включает `JOIN order_items`. Никогда не бывает N+1 для этой связи.

**Trade-offs:**

| | |
|---|---|
| ✅ Не нужно помнить про `JOIN FETCH` в каждом репозитории. | |
| ✅ Автоматически для всех запросов. | |
| ⚠️ **Каждый** запрос на `Order` загружает `items`, даже если они не нужны. `SELECT * FROM orders WHERE status = 'pending'` — 10 000 заказов × 5 items = 50 000 строк. |
| ⚠️ Память: 10 000 заказов с items = десятки МБ. |
| ⚠️ Если `items` — 10 000 записей (агрегат-«свиноколбаса») — каждый запрос грузит 10 000 строк. |
| ⚠️ `EAGER` на `ManyToMany` — та же проблема: Cartesian product. |
| ❌ **Нельзя выключить для конкретного запроса.** Если один сценарий не needs items — всё равно загружает. |

**Когда оправдан:** aggregate с малым числом детей (≤ 20), и items всегда нужны. `Order → OrderItem` (5–20 позиций) — да. `User → Comment` (10 000 комментариев) — нет.

---

### 3. Batch fetching (Doctrine 2.14+ / 3.x)

Механизм, который **автоматически** группирует N+1 в один `WHERE IN (...)`.

```php
// config/packages/doctrine.yaml
doctrine:
    orm:
        # Doctrine 2.14+
        query_cache_driver:
            type: apcu
        # Batch fetching включается автоматически для EAGER-связей
        # и для JOIN FETCH, если результат > 1
```

**Как работает:**

```php
// 100 заказов, каждый с 5 items.
// Без batch: 100 × SELECT * FROM order_items WHERE order_id = ?
// С batch:   1 × SELECT * FROM order_items WHERE order_id IN (?, ?, ..., ?)  ← 100 значений
```

Doctrine 2.14+ делает это **автоматически** для `EAGER`-связей. Для `LAZY` — при первом обращении к коллекции.

**Trade-offs:**

| | |
|---|---|
| ✅ Автоматически. Не нужно писать `JOIN FETCH`. | |
| ✅ Один запрос вместо N. | |
| ⚠️ `WHERE IN (100 значений)` — если N = 10 000 → запрос на 10 000 значений. БД может не справиться. |
| ⚠️ Память: 100 заказов × 5 items = 500 строк в одном запросе. OK. 10 000 × 100 = 1 000 000 строк — нет. |
| ⚠️ Не работает для `ManyToMany` (ограничение Doctrine). |
| ⚠️ В Doctrine 2.x — только для `EAGER`. Для `LAZY` — нужно вручную `JOIN FETCH`. |

**Когда оправдан:** средний набор (10–500 сущностей), связь `OneToMany`, `EAGER`.

---

### 4. `getReference()` — загрузка без SELECT

```php
// Вместо:
$customer = $this->em->find(CustomerEntity::class, $order->customerId);
// 1 SELECT customers WHERE id = ?

// Делаем:
$customer = $this->em->getReference(CustomerEntity::class, $order->customerId);
// 0 SELECT. Возвращает Proxy.

// Proxy не загружает данные, пока не обратишься к методу:
$customer->getId();      // OK, id уже есть (из PK)
$customer->getName();    // ← 1 SELECT (lazy load)
$customer->getEmail();   // ← тот же SELECT (proxy уже loaded)
```

**Trade-offs:**

| | |
|---|---|
| ✅ 0 запросов, если данные не нужны. | |
| ✅ Идеально для «мне нужен только id / только наличие». | |
| ⚠️ Первый доступ к методу — всё равно SELECT. N+1 не исчез, а **откладывается**. | |
| ⚠️ Если в цикле 100 заказов × `$customer->getName()` → 100 SELECT. N+1 возвращается. |
| ⚠️ `json_encode($customer)` → FATAL (proxy не сериализуется). |
| ⚠️ `===` на proxy — работает (сравнивает id), но `==` может спровоцировать load. |

**Когда оправдан:** «мне нужен объект для связи, но данные не нужны». Например, `Order → Customer` (нужен только `id` для ссылки, а имя — только в детальной странице).

---

### 5. Array hydration — обход entity-модели

```php
// Вместо entities:
$orders = $this->em->createQuery(
    'SELECT o.id, o.status, o.createdAt,
            oi.productId, oi.quantity, oi.unitPriceCents
     FROM OrderEntity o
     JOIN o.items oi'
)->getResult();
// 1 запрос. Результат: array of arrays.
//
// [
//   ['id' => 'uuid-1', 'status' => 'pending', 'createdAt' => '2024-01-01',
//    'productId' => 'p1', 'quantity' => 2, 'unitPriceCents' => 1050],
//   ['id' => 'uuid-1', 'status' => 'pending', 'createdAt' => '2024-01-01',
//    'productId' => 'p2', 'quantity' => 1, 'unitPriceCents' => 500],
//   ['id' => 'uuid-2', 'status' => 'confirmed', 'createdAt' => '2024-01-02',
//    'productId' => 'p3', 'quantity' => 5, 'unitPriceCents' => 200],
//   // ...
// ]

// Группируем вручную:
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['id']][] = $row;
}
// Теперь: $grouped['uuid-1'] → 2 строки (items), $grouped['uuid-2'] → 1 строка
```

**Trade-offs:**

| | |
|---|---|
| ✅ Максимальная производительность. 1 запрос. Нет entity-overhead. | |
| ✅ Нет проблем с `setMaxResults` + JOIN (нет entity identity map). | |
| ✅ Можно выбрать ровно те поля, что нужны. | |
| ❌ Нет entity-модели. Нет `Order::confirm()`. Нет инвариантов. | |
| ❌ Нет cascade, dirty checking, UnitOfWork. | |
| ❌ Дублирование логики: если добавили поле в Order — нужно поменять 2 места (entity + query). | |
| ❌ Сложнее тестировать (нет mock'а репозитория). |

**Когда оправдан:** Read-модель, list-view, where performance критична и entity-модель не нужна. CQRS: write через aggregate, read через raw query.

---

### 6. Denormalization — избыточность в БД

```sql
-- Было:
CREATE TABLE orders (id, status, customer_id, ...);
CREATE TABLE order_items (order_id, product_id, quantity, ...);

-- Стало (для list-view):
CREATE TABLE orders (
    id,
    status,
    customer_id,
    total_cents INT,        ← denormalized
    items_count INT,        ← denormalized
    ...
);

-- List-view:
SELECT id, status, customer_id, total_cents, items_count
FROM orders
ORDER BY created_at DESC
LIMIT 20;
-- 1 запрос. Нет JOIN. Нет N+1.
```

**Обновление:** триггеры, application-код, или materialized view.

```sql
-- Postgres: materialized view (если не нужен realtime)
CREATE MATERIALIZED VIEW orders_summary AS
SELECT
    o.id,
    o.status,
    o.customer_id,
    o.created_at,
    SUM(oi.quantity * oi.unit_price_cents) AS total_cents,
    COUNT(oi.id) AS items_count
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
GROUP BY o.id;

REFRESH MATERIALIZED VIEW CONCURRENTLY orders_summary;
```

**Trade-offs:**

| | |
|---|---|
| ✅ Максимальная скорость чтения. 1 запрос. Без JOIN. | |
| ✅ Не зависит от Doctrine / ORM. | |
| ⚠️ Дублирование данных. `total_cents` в `orders` и `SUM(...)` в `order_items` — два источника истины. |
| ⚠️ Сложность обновления: каждый `INSERT/UPDATE/DELETE` в `order_items` → обновление `orders.total_cents`. |
| ⚠️ Race condition: если обновление异步 → window inconsistency. |
| ⚠️ Миграции: если добавили поле в `order_items` → нужно обновить materialized view. |
| ❌ Нарушает 3NF. Но для read-модели — осознанный trade-off. |

**Когда оправдан:** list-view, отчёты, дашборды. Write-path — через aggregate. Read-path — через denormalized view.

---

### 7. CQRS: разделение read и write

```
WRITE:
  PlaceOrderHandler
    → Order aggregate
    → Doctrine (aggregate, EAGER, cascade)
    → 1 INSERT orders + N INSERT order_items
    → COMMIT

READ:
  OrderListController
    → SELECT FROM orders_summary  ← denormalized / materialized view
    → 1 запрос. Без Doctrine entity.
    → Рендер
```

**Trade-offs:**

| | |
|---|---|
| ✅ Read-модель оптимизирована под конкретный запрос. | |
| ✅ Write-модель — чистый DDD aggregate. | |
| ⚠️ Два кодовых пути. Две схемы. Два набора миграций. | |
| ⚠️ Сложность: согласование write → read (event → refresh view). | |
| ⚠️ Overkill для малого проекта. |

**Когда оправдан:** > 1000 QPS на read-path. Разные команды пишут и читают. Разные требования к performance.

---

### 8. DataLoader (batch loading)

Паттерн из N+1-библии: один «загрузчик» собирает все запросы в один batch.

```php
// src/Loader/CustomerLoader.php
final class CustomerLoader
{
    /** @var array<string, ?Customer> */
    private array $cache = [];
    /** @var list<string> */
    private array $pending = [];

    public function load(string $customerId): ?Customer
    {
        if (array_key_exists($customerId, $this->cache)) {
            return $this->cache[$customerId];
        }

        $this->pending[] = $customerId;
        return null;  // «ещё не загружено, но в очереди»
    }

    public function flush(EntityManagerInterface $em): void
    {
        if ($this->pending === []) return;

        // Один запрос: WHERE id IN (...)
        $customers = $em->getRepository(CustomerEntity::class)
            ->findBy(['id' => $this->pending]);

        foreach ($customers as $c) {
            $this->cache[$c->id()] = $c;
        }

        // Отмечаем «не найдены»
        foreach ($this->pending as $id) {
            if (!isset($this->cache[$id])) {
                $this->cache[$id] = null;
            }
        }

        $this->pending = [];
    }
}
```

```php
// В цикле:
$loader = new CustomerLoader();

foreach ($orders as $order) {
    // load() не делает запрос. Только добавляет в pending.
    $customerId = $loader->load($order->customerId());
}

// Один запрос на всех:
$loader->flush($em);

// Теперь все данные в кэше:
foreach ($orders as $order) {
    $customer = $loader->load($order->customerId());  // из кэша, 0 запросов
    // ...
}
```

**Trade-offs:**

| | |
|---|---|
| ✅ N+1 → 1 запрос. `WHERE IN (...)`. | |
| ✅ Работает для любого типа связи. | |
| ⚠️ Нужно знать, **когда** все `load()` вызваны (иначе `flush()` слишком рано). | |
| ⚠️ Усложняет код: вместо `em->find()` → `loader->load()` + `loader->flush()`. | |
| ⚠️ Если `pending` = 10 000 → `WHERE IN (10 000)` → БД может не справиться. |
| ⚠️ Не интегрируется с Doctrine UnitOfWork. |

**Когда оправдан:** N = 50–500, связь `ManyToOne`, нет `JOIN FETCH` в DQL (например, через `ManyToMany`, или динамический набор).

---

## Сравнительная таблица

| Способ | Запросы | Память | Сложность | Когда |
|---|---|---|---|---|
| **JOIN FETCH** | 1 | Средняя | Низкая | Один-в-много, ≤ 50 children, нужно в DQL |
| **EAGER** | 1 (auto) | Высокая (всегда) | Минимальная | Aggregate, всегда нужны дети |
| **Batch fetching** | 1 (auto) | Средняя | Низкая | EAGER + N = 10–500 |
| **getReference** | 0 (до access) | Низкая | Низкая | «Мне нужен только id» |
| **Array hydration** | 1 | Низкая | Высокая | Read-модель, list-view |
| **Denormalization** | 1 | Низкая | Высокая (write-path) | Read-модель, дашборд |
| **CQRS** | 1 | Низкая | Очень высокая | > 1000 QPS, разные команды |
| **DataLoader** | 1 | Средняя | Средняя | N = 50–500, нет DQL JOIN |

---

## Антипаттерны при «лечении» N+1

### 1. `EAGER` на всё

```php
#[ORM\OneToMany(fetch: EAGER)]  // User → Comments (10 000)
#[ORM\ManyToMany(fetch: EAGER)] // User → Roles (OK)
#[ORM\OneToMany(fetch: EAGER)]  // Order → Items (OK)
#[ORM\OneToMany(fetch: EAGER)]  // Product → Reviews (50 000)
```

Каждый `SELECT * FROM users` грузит 10 000 комментариев. Каждый `SELECT * FROM products` грузит 50 000 отзывов. **Worse than N+1.**

**Правило:** `EAGER` — только для aggregate-связей с ≤ 50 children. Остальное — `LAZY` + `JOIN FETCH` по необходимости.

### 2. `JOIN FETCH` + `setMaxResults` без защиты

```php
// Опасно:
$qb->select('o', 'oi')
   ->from(OrderEntity::class, 'o')
   ->join('o.items', 'oi')
   ->setMaxResults(20);
//
// Если заказ 1 → 5 items, заказ 2 → 3 items:
// JOIN = 8 строк. LIMIT 20 → 8 строк → 2 заказа (не 20!)
//
// Если заказ 1 → 100 items:
// JOIN = 100 строк. LIMIT 20 → 20 строк → 1 заказ (не 20!)
```

**Решение:** двухшаговый запрос (IDs → JOIN) или `Paginator` (knpu).

### 3. `EntityManager::clear()` в цикле

```php
foreach ($ids as $id) {
    $order = $this->em->find(OrderEntity::class, $id);
    // ...
    $this->em->clear();  // ← убивает identity map
}
```

`clear()` освобождает UnitOfWork. Следующий `find()` — новый SELECT, даже для того же `id`. Если в цикле 100 заказов с одинаковым `customer_id` → 100 SELECT customers. **N+1 возвращается.**

### 4. Ручное `JOIN` в SQL вместо DQL

```php
// «Быстро и просто»:
$sql = "SELECT o.*, oi.* FROM orders o JOIN order_items oi ON oi.order_id = o.id";
$rows = $this->em->getConnection()->fetchAllAssociative($sql);
//
// Проблема: 20 заказов × 5 items = 100 строк.
// Но: если 20 заказов × 50 items = 1000 строк.
// И: нет entity-модели. Нет identity map. Дубликаты.
//
// Если нужно — array hydration (способ 5), а не «raw SQL».
```

### 5. «Оптимизация» на уровне Twig

```twig
{# templates/orders/index.html.twig #}
{% for order in orders %}
    {{ order.customer.name }}  {# ← lazy load! N+1 в template #}
    {% for item in order.items %}
        {{ item.product.name }}  {# ← lazy load! N+1 в template #}
    {% endfor %}
{% endfor %}
```

Twig не знает про Doctrine. `order.customer` — Proxy. Первый доступ → SELECT. 100 заказов → 100 SELECT customers. **N+1 в шаблоне.**

**Решение:** загрузить в controller (`JOIN FETCH`), а не в Twig.

---

## Мой алгоритм: «страница тормозит»

```
1. Web Profiler → количество SQL-запросов
   ├── > 20 → N+1. Переход к 2.
   └── ≤ 5 → медленный запрос. Переход к 3.

2. N+1:
   ├── Выявить связь: orders → order_items? users → comments?
   ├── N (число родителей) ≤ 20 → JOIN FETCH
   ├── N = 50–500 → Batch fetching / DataLoader
   ├── Sвязь ManyToMany → Array hydration / denormalization
   └── Aggregate (Order → Items) → EAGER (если ≤ 50 children)

3. Медленный запрос:
   ├── EXPLAIN ANALYZE → индекс?
   ├── Нет индекса → добавить
   ├── Индекс есть, но sequential scan → partial index / BRIN
   ├── Subquery → materialized view / CTE
   └── Внешний сервис → async / cache / timeout

4. После fixes:
   ├── Profiler: ≤ 5 запросов, ≤ 50 мс суммарно
   ├── Load test: 50 concurrent → без деградации
   └── Код-ревью: нет EAGER на больших связях, нет raw SQL без причины
```

---

## Главное

N+1 — не «баг Doctrine». Это **архитектурное решение**: ORM отдаёт вам entity-модель (удобство), а вы платите запросами (производительность). `JOIN FETCH`, `EAGER`, `DataLoader`, `Array hydration` — это способы **выбрать, что вам важнее в конкретном запросе**: удобство entity-модели или скорость.

Нет «правильного» способа. Есть **контекст**: сколько сущностей, сколько children, нужен ли entity-модель, один запрос или список.

> ⏱ 59.0s · prompt 57302 tok · output 7001 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking about why we need database-level constraints in PostgreSQL when business validation is already done in application code. They want to know what tools PostgreSQL provides and when to use them. This is a follow-up in a conversation about PHP/Symfony/Doctrine/DDD.

Let me think about this carefully:

1. The premise: "All business validation is done in application code" — this is true for domain invariants (e.g., "order can't be empty", "price can't be negative"). But DB constraints serve a DIFFERENT purpose.

2. Why DB constraints are still needed:
   - Defense in depth: app code has bugs, race conditions, direct DB access (DBAs, migrations, other services)
   - Atomicity: CHECK constraints and unique constraints are enforced atomically at the storage level
   - Concurrent access: two requests can both pass app-level validation before either commits
   - Data integrity for external consumers: other services, analytics, DBAs querying directly
   - Migration safety: during data migration, constraints prevent bad data
   - Performance: unique constraints create indexes, CHECK constraints can help the planner
   - Different entry points: CLI commands, cron jobs, other microservices, raw SQL

3. What PostgreSQL provides:
   - NOT NULL
   - UNIQUE / UNIQUE constraints (composite)
   - PRIMARY KEY
   - FOREIGN KEY
   - CHECK constraints (row-level)
   - EXCLUSION constraints (Postgres-specific)
   - DEFAULT values
   - Generated columns (identity, computed)
   - PARTITION constraints
   - Triggers (before/after)
   - Table-level CHECK constraints
   - Domain types (custom types with CHECK)

4. What should NOT be in DB constraints:
   - Complex business rules that span multiple tables (e.g., "order total = sum of items") — too complex for CHECK
   - Rules that depend on application context (e.g., "user is VIP" — that's in app state)
   - Rules that need external data (e.g., "stock available" — that's in another service)

5. The relationship between app validation and DB constraints:
   - App validation: rich, contextual, user-friendly error messages
   - DB constraints: last line of defense, atomic, no false positives
   - They're complementary, not redundant

6. Race condition example: two requests both read stock=1, both pass "stock > 0" check, both try to decrement. App-level check passes for both. DB constraint (CHECK stock >= 0) catches the second one.

7. Doctrine's role: it generates DDL from mapping, but you can also have constraints that Doctrine doesn't know about (via migrations).

Let me structure this clearly with examples, trade-offs, and practical guidance. I'll keep the same style as previous answers — concrete code, tables, trade-offs.

</details>

**Кандидат:**

## Короткий ответ

Бизнес-валидация в коде и ограничения в БД решают **разные задачи**. Код отвечает «допустимо ли это в *контексте* сценария». БД отвечает «допустимо ли это в *любом* сценарии, в *любой* момент времени, независимо от того, *кто* и *как* пишет данные».

Они не дублируют друг друга. Они **комплементарны**.

---

## Почему «код проверил» ≠ «БД защищена»

### 1. Race condition

```php
// Два параллельных запроса. Оба проходят app-валидацию.
// Stock: 1 шт.

// Request A (t=0ms):
$stock = $repo->find('SKU-1');   // stock = 1
if ($stock->quantity > 0) { ... }  // ✅ прошло
$stock->quantity = 0;
$em->persist($stock);
// ... ещё 50 мс на flush ...

// Request B (t=1ms):
$stock = $repo->find('SKU-1');   // stock = 1 (A ещё не commit'ил)
if ($stock->quantity > 0) { ... }  // ✅ прошло
$stock->quantity = 0;
$em->persist($stock);

// Result: два заказа на 1 шт.
// App-валидация: прошла в обоих.
// БД-валидация: CHECK (quantity >= 0) — оба 0, OK.
// Но: CHECK (quantity = 0 AND version = ?) — второй UPDATE затронет 0 строк.
```

Без DB-защиты — перепродажа. App-код не может гарантировать атомарность «прочитал → проверил → записал» без явной блокировки или optimistic lock.

### 2. Прямой доступ к БД

- DBA чинит данные: `UPDATE orders SET status = 'refunded' WHERE id = 42;`
- Миграция: `INSERT INTO audit_log (...) SELECT ... FROM old_table;`
- Другой микросервис (Python, Go) пишет в ту же БД.
- Cron-джоба: `UPDATE subscriptions SET expired = true WHERE end_date < NOW();`
- Аналитик: `DELETE FROM sessions WHERE created_at < NOW() - INTERVAL '30 days';`

Никто из них не ходит через ваш PHP-код. Никто не вызывает ваш `Order::confirm()`. CHECK constraint — единственный барьер.

### 3. Баг в коде

```php
// Опечатка:
$order->total = $items->sum() * 0.8;  // забыли * 100 (центы)
// total = 840 (доллары) вместо 84000 (центы)
// App-валидация: total > 0 → ✅
// DB: CHECK (total_cents >= 0) → ✅
// DB: CHECK (total_cents % 100 = 0) → ✅ (840 % 100 = 40 → ❌)
```

### 4. Partial failure

```php
try {
    $this->orders->save($order);
    $this->notifications->send($order);
    $this->em->flush();
} catch (\Exception $e) {
    // flush не выполнен. Но: если flush был вызван раньше (в save)?
    // Если между save и flush процесс упал (OOM, segfault)?
    // Данные в БД без notify. Без commit (если autoCommit = false).
    // Но: если кто-то другой commit'нул параллельно — partial state.
}
```

DB constraints — последний рубеж. Если app упал *после* INSERT, но *до* UPDATE status — `CHECK (status IN (...))` не даст «зависшего» статуса.

### 5. Performance-поддержка

`UNIQUE` constraint автоматически создаёт индекс. `CHECK` может быть использован planner'ом для отбраковки строк до scan. Это не «валидация» — это **оптимизация**.

---

## Что PostgreSQL даёт

### 1. `NOT NULL` + `DEFAULT`

```sql
CREATE TABLE orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    customer_id UUID NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    total_cents INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

**Что защищают:**
- `NOT NULL` — никто не вставит `NULL` в обязательное поле. Даже raw SQL.
- `DEFAULT` — если забыли передать поле (migration, другой сервис) — значение не `NULL`, а осмысленное.
- `gen_random_uuid()` — id генерируется на стороне БД. Приложение не может «забыть» его присвоить.

**Когда:** всегда. Базовый уровень.

### 2. `UNIQUE` (включая composite)

```sql
-- Однозначная пара: заказ + товар
CREATE UNIQUE INDEX idx_order_items_unique
    ON order_items (order_id, product_id);

-- Email уникален среди активных пользователей
CREATE UNIQUE INDEX idx_users_email_unique
    ON users (lower(email))
    WHERE deleted_at IS NULL;   -- partial index

-- Один token на одного пользователя (активный)
CREATE UNIQUE INDEX idx_sessions_token_unique
    ON sessions (token_hash)
    WHERE expires_at > NOW();
```

**Что защищают:**
- Дубли в `order_items` — даже при race condition.
- Дубль email — даже при параллельной регистрации.
- Дубль token — даже при bug в коде генерации.

**Trade-off:** каждый `UNIQUE` — это индекс. 10+ уникальных индексов на таблице = overhead на INSERT/UPDATE. Но дубль в БД — хуже.

**Partial unique index** (`WHERE ...`) — мощное средство: уникальность только для подмножества. Активный email уникален, удалённый — нет.

### 3. `CHECK` — row-level валидация

```sql
CREATE TABLE orders (
    ...
    total_cents INTEGER NOT NULL
        CHECK (total_cents >= 0),
    status VARCHAR(32) NOT NULL
        CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled')),
    CHECK (total_cents = (SELECT COALESCE(SUM(quantity * unit_price_cents), 0)
                          FROM order_items WHERE order_items.order_id = orders.id)),
    -- ↑ table-level CHECK: total = сумма позиций
);

CREATE TABLE order_items (
    ...
    quantity INTEGER NOT NULL
        CHECK (quantity >= 1 AND quantity <= 9999),
    unit_price_cents INTEGER NOT NULL
        CHECK (unit_price_cents >= 0 AND unit_price_cents <= 999999999),
    CHECK (quantity * unit_price_cents <= 999999999)
);

CREATE TABLE users (
    ...
    password_hash VARCHAR(255) NOT NULL
        CHECK (password_hash LIKE '$argon2id$%' OR password_hash LIKE '$2y$%'),
    -- только bcrypt / argon2. Не plain text. Не md5.
);
```

**Что защищают:**
- `status IN (...)` — никто не вставит `'pending2'` или `''`.
- `total_cents >= 0` — нет отрицательных сумм.
- `total = SUM(items)` — согласованность внутри БД, без приложения.
- `password_hash LIKE '$argon2id$%'` — только валидный формат hash.

**Ограничения `CHECK`:**
- Только row-level (одна строка). Не может обращаться к *другой* строке (кроме subquery, как в примере `total = SUM`).
- Не может вызывать функции (кроме `immutable`).
- Ошибка: `check constraint violation` — без контекста, без «какое правило».

**Когда:** простые инварианты, которые не зависят от контекста. «Цена не отрицательная», «статус из перечисления», «email содержит @».

**Когда НЕ нужно:** «если статус = shipped, то必须有 tracking_number». Это бизнес-правило, которое зависит от *состояния* другого поля. В Postgres — можно через `CHECK (status <> 'shipped' OR tracking_number IS NOT NULL)`. Но если правило 5 строк — в код.

### 4. `FOREIGN KEY`

```sql
CREATE TABLE order_items (
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id UUID NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    ...
);

CREATE TABLE orders (
    customer_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    ...
);
```

**Что защищают:**
- Ссылка на несуществующий `product_id` — невозможна. Даже raw SQL.
- `ON DELETE CASCADE` — удаление заказа → автоматическое удаление items. Не «забыли удалить».
- `ON DELETE RESTRICT` — удаление пользователя с заказами → BLOCKED. Не «сиротские заказы».

**Trade-off:**
- `FK` — это индекс на `child`-таблице + проверка на `parent`. Overhead на INSERT.
- `ON DELETE CASCADE` — «тихое» удаление. Если удалили parent — children исчезли без следа. Для aggregate (`Order → OrderItem`) — правильно. Для `User → Order` — опасно (лучше `RESTRICT`).
- Cross-database FK — невозможны. Если `orders` и `products` в разных БД — FK не работает.

**Когда:** всегда для `parent → child` внутри одной БД. Исключение: denormalized read-модели, где FK не нужны (и не могут быть).

### 5. `EXCLUSION` (только Postgres)

```sql
CREATE TABLE reservations (
    id UUID PRIMARY KEY,
    room_id UUID NOT NULL,
    start_at TIMESTAMPTZ NOT NULL,
    end_at TIMESTAMPTZ NOT NULL,
    CHECK (end_at > start_at),
    EXCLUDE USING gist (
        room_id WITH =,
        daterange(start_at, end_at) WITH &&
    )
);
```

**Что защищает:** пересечение бронирований в одной комнате. `room_id =` (равенство) + `daterange &&` (пересечение). Невозможно вставить пересекающийся интервал. Ни через app, ни через raw SQL.

**Когда:** расписания, бронирования, слоты. Там, где «диапазоны не пересекаются» — инвариант, а не бизнес-правило.

### 6. `GENERATED` columns (Postgres 12+)

```sql
CREATE TABLE orders (
    ...
    total_cents INTEGER NOT NULL,
    total_with_tax_cents INTEGER GENERATED ALWAYS AS (
        total_cents * 1.2
    ) STORED
    CHECK (total_with_tax_cents >= 0)
);
```

**Что защищает:** `total_with_tax_cents` невозможно «забыть обновить». БД считает сама. App-код не может вставить `total_cents = 100, total_with_tax_cents = 50` — `GENERATED` не позволяет записать.

**Когда:** производные поля, которые всегда вычисляются из других полей этой же строки.

### 7. Triggers (последний инструмент)

```sql
CREATE OR REPLACE FUNCTION trigger_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_updated_at
    BEFORE UPDATE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION trigger_updated_at();
```

**Что защищает:** `updated_at` всегда актуален. Даже при raw SQL `UPDATE orders SET status = 'shipped' WHERE id = ...;`.

**Trade-off:**
- Trigger — это «код в БД». Тестируется сложнее, отлаживается сложнее.
- `BEFORE UPDATE` на каждой строке — overhead.
- Не прозрачен: новый разработчик не знает, что `updated_at` обновляется «магией».

**Когда:** `updated_at`, audit-логи, счётчики. Вещи, которые *всегда* должны происходить при записи, независимо от источника.

**Когда НЕ нужно:** бизнес-логика («если status = shipped, создать tracking»). Это app-код. Trigger — для механических действий.

### 8. Custom `DOMAIN` types

```sql
CREATE DOMAIN email AS TEXT
    CONSTRAINT email_format CHECK (VALUE ~ '^[^@]+@[^@]+\.[^@]+$');

CREATE DOMAIN cents AS INTEGER
    CONSTRAINT cents_range CHECK (VALUE >= 0 AND VALUE <= 999999999);

CREATE DOMAIN order_status AS TEXT
    CONSTRAINT status_valid CHECK (VALUE IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled'));

CREATE TABLE orders (
    ...
    customer_email email NOT NULL,
    total_cents cents NOT NULL,
    status order_status NOT NULL DEFAULT 'pending'
);
```

**Что защищает:** один тип → одна валидация → все таблицы используют её. `email` во всех таблицах — один `CHECK`. Изменили правило — изменили `DOMAIN`.

**Trade-off:**
- Не все ORM'ы (включая Doctrine) корректно поддерживают `DOMAIN`. Migration-генераторы могут не учесть.
- Ошибка: `email_format constraint violation` — без контекста «какое поле».

**Когда:** 3+ таблицы используют один тип (`email`, `cents`, `uuid`). Единая точка валидации.

---

## Что **не** выносим в БД

| Правило | Где | Почему |
|---|---|---|
| «Заказ не может быть пустым» | Домен (`Order::create`) | Зависит от контекста (draft может быть пустым) |
| «Stock > 0 при оформлении» | Домен + DB (`CHECK`) | Race condition → DB. Контекст (VIP-заказ без stock) → код |
| «Только admin может удалить» | Voter / App | Зависит от авторизации. БД не знает про роли |
| «Email подтверждён, если token valid» | App (token = hash(email + secret)) | Зависит от секрета. БД не хранит secret |
| «Скидка не больше 50%» | Домен | Зависит от промокода, даты, VIP-статуса. Сложно для `CHECK` |
| «Только один активный подписочный план на аккаунт» | DB (`UNIQUE`) | Простой, атомарный, не зависит от контекста |
| «Sum(items) = total» | DB (`CHECK` c subquery) | Механический инвариант. Не зависит от сценария |

**Критерий:** если правило можно выразить как `CHECK (выражение_над_одной_строкой = true)` или `UNIQUE (колонки)` — в БД. Если нужно «запросить другой сервис», «знать текущее время», «знать роль пользователя» — в код.

---

## Соотношение: код vs БД

```
┌─────────────────────────────────────────────────────────────────────────┐
│  LAYER 1: Домен (PHP)                                                  │
│  ─ Order::create() — items не пустые                                   │
│  ─ OrderItem — quantity ≥ 1, price ≥ 0                                 │
│  ─ Order::confirm() — status transition valid                          │
│  ─ Money — currency valid, amount ≥ 0                                  │
│                                                                         │
│  Ошибка: InvalidEmail, InsufficientStock, EmptyOrder                   │
│  (rich message, контекст, user-friendly)                                │
├─────────────────────────────────────────────────────────────────────────┤
│  LAYER 2: Application (PHP)                                            │
│  ─ Voter: canEdit(post, user)                                          │
│  ─ StockService::reserve() — atomarное уменьшение                      │
│  ─ Transaction: save + reserve = атомарно                               │
│                                                                         │
│  Ошибка: AccessDeniedException, InsufficientStock                      │
├─────────────────────────────────────────────────────────────────────────┤
│  LAYER 3: База данных (PostgreSQL)                                      │
│  ─ NOT NULL — нет NULL в обязательных полей                             │
│  ─ UNIQUE — нет дублей (race condition)                                │
│  ─ CHECK — status IN (...), amount ≥ 0                                  │
│  ─ FK — нет сиротских записей                                          │
│  ─ EXCLUDE — нет пересечений (расписания)                              │
│  ─ TRIGGER — updated_at, audit                                         │
│                                                                         │
│  Ошибка: check constraint violation, unique violation, FK violation    │
│  (кратко, без контекста, но АТОМАРНО)                                   │
└─────────────────────────────────────────────────────────────────────────┘
```

**Ключевое различие:**

| | Код | БД |
|---|---|---|
| **Контекст** | Да (роль, время, сценарий) | Нет (только данные в строке) |
| **Атомарность** | Зависит от транзакции | Встроена (один statement = один check) |
| **Покрывает raw SQL?** | Нет | Да |
| **Покрывает другие сервисы?** | Нет | Да (если общая БД) |
| **Message** | Rich, user-friendly | Кратко, technical |
| **Перформанс** | N/A | Overhead на INSERT/UPDATE (но малый) |
| **Тестируемость** | Unit test | Integration test / migration test |

---

## Практика: какой набор ставлю

### Минимальный набор (для любой таблицы)

```sql
CREATE TABLE orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    customer_id UUID NOT NULL REFERENCES users(id),
    status VARCHAR(32) NOT NULL DEFAULT 'pending'
        CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled')),
    total_cents INTEGER NOT NULL DEFAULT 0
        CHECK (total_cents >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

### Для aggregate (Order → OrderItem)

```sql
CREATE TABLE order_items (
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id UUID NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    quantity INTEGER NOT NULL
        CHECK (quantity >= 1 AND quantity <= 9999),
    unit_price_cents INTEGER NOT NULL
        CHECK (unit_price_cents >= 0 AND unit_price_cents <= 999999999),
    PRIMARY KEY (order_id, product_id)   -- composite PK + UNIQUE
);

-- Согласованность total:
ALTER TABLE orders
    ADD CONSTRAINT chk_total_consistent
    CHECK (total_cents = (
        SELECT COALESCE(SUM(quantity * unit_price_cents), 0)
        FROM order_items WHERE order_items.order_id = orders.id
    ));
```

### Для расписаний / слотов

```sql
CREATE TABLE time_slots (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    resource_id UUID NOT NULL,
    start_at TIMESTAMPTZ NOT NULL,
    end_at TIMESTAMPTZ NOT NULL,
    CHECK (end_at > start_at),
    EXCLUDE USING gist (
        resource_id WITH =,
        daterange(start_at, end_at) WITH &&
    )
);
```

### Для audit

```sql
CREATE TRIGGER trg_audit
    AFTER INSERT OR UPDATE OR DELETE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION audit_log();
```

---

## Антипаттерны

### 1. Вся бизнес-логика в triggers

```sql
-- АНТИПАТТЕРН:
CREATE TRIGGER on_order_update
    BEFORE UPDATE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION (
        -- 50 строк plpgsql: расчёт скидки, валидация промокода,
        -- уведомление складу, обновление кэша...
    );
```

Trigger — для `updated_at` и `audit`. Не для бизнес-логики. Тестировать, отлаживать, переносить между БД — кошмар.

### 2. `CHECK` на всё

```sql
-- АНТИПАТТЕРН:
CHECK (status = 'pending' OR (status = 'shipped' AND tracking_number IS NOT NULL))
CHECK (status <> 'cancelled' OR cancelled_reason IS NOT NULL)
CHECK (status = 'confirmed' AND total_cents > 0 AND customer_id IS NOT NULL)
-- 15 CHECK-ов на таблице. Читаемость = 0.
```

**Правило:** `CHECK` — для **простых, независимых** инвариантов. «Цена ≥ 0», «статус из перечисления», «количество 1–9999». Если правило 3+ строки или зависит от 3+ полей — в код.

### 3. FK на denormalized read-модель

```sql
-- АНТИПАТТЕРН:
CREATE TABLE orders_summary (
    order_id UUID NOT NULL REFERENCES orders(id),  -- FK к write-модели
    customer_name VARCHAR(100),  -- denormalized
    total_cents INTEGER
);
```

Read-модель — это **копия**. FK к source-таблице создаёт зависимость: нельзя удалить source, пока есть summary. Нельзя реорганизовать source, пока есть FK. Denormalized = independent.

### 4. Удаление `NOT NULL` ради «гибкости»

```sql
-- АНТИПАТТЕРН:
customer_id UUID,  -- nullable. "А вдруг понадобится."
status VARCHAR(32),  -- nullable. "А вдруг будет draft без статуса."
```

`NULL` в БД — это «мы не знаем» или «мы забыли». Не «опциональное поле». Если поле опциональное — `DEFAULT ''` или `DEFAULT 'draft'`. Не `NULL`.

### 5. `CHECK` вместо `ENUM`

```sql
-- ХОРОШО:
status VARCHAR(32) NOT NULL CHECK (status IN ('a', 'b', 'c', 'd', 'e'));

-- АНТИПАТТЕРН (Postgres-specific, но):
status VARCHAR(32) NOT NULL,
-- и в приложении: const STATUSES = ['a', 'b', 'c', 'd', 'e'];
// → два источника истины. BД не знает о перечислении.
```

`CHECK` — единственный способ «enum» в Postgres, который не требует `ALTER TABLE ADD/DROP VALUE` (а это breaking change для reader'ов).

---

## Итого

| Вопрос | Ответ |
|---|---|
| «Зачем CHECK, если в PHP проверил?» | PHP не покрывает raw SQL, другие сервисы, race conditions, баги. |
| «Зачем UNIQUE, если в коде `findByEmail`?» | Параллельная регистрация → оба проходят `findByEmail` → оба INSERT. UNIQUE — атомарный барьер. |
| «Зачем FK, если в коде проверяю наличие?» | `DELETE FROM users WHERE id = 42;` из psql. FK → BLOCKED. Без FK → сироты. |
| «Зачем NOT NULL, если в конструкторе проверяю?» | `INSERT INTO orders (id, status) VALUES ('uuid', 'pending');` — `customer_id` = NULL. NOT NULL → ERROR. |
| «Зачем trigger, если в коде ставлю `updated_at`?» | `UPDATE orders SET status = 'shipped';` из migration. Без trigger → `updated_at` не обновился. |

**БД-ограничения — не дубль кода. Это последний рубеж.** Код — первый рубеж, с контекстом и красивыми сообщениями. БД — без контекста, но **без исключений**. И это не «защита от дурака». Это **гарантия целостности данных** в системе, где пишут не только ваши PHP-сервисы.

> ⏱ 56.9s · prompt 64027 tok · output 6272 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь просит описать процесс диагностики и оптимизации медленных запросов в PostgreSQL. Это продолжение серии вопросов о PHP/Symfony/Doctrine/PostgreSQL. Нужно дать структурированный, практичный ответ с конкретными инструментами, командами и примерами.

Структура ответа:
1. Диагностика: какие инструменты, в каком порядке
2. Чтение плана выполнения (EXPLAIN ANALYZE)
3. Типовые причины с примерами
4. Оптимизация: что делать для каждого типа
5. Антипаттерны оптимизации

Нужно быть конкретным: реальные команды, реальные планы EXPLAIN, реальные индексы. Не абстрактно, а как будто я сижу за терминалом и чиню.

Типовые причины:
- Missing index / bad index
- Sequential scan на большой таблице
- N+1 (уже обсуждали, но на уровне PG — много маленьких запросов)
- Lock contention (row-level, table-level)
- Bloat (dead tuples, autovacuum)
- Bad JOIN order
- Subquery не материализуется
- Statistics устарели
- Connection pool exhaustion
- Memory: work_mem, shared_buffers
- Plan cache (prepared statements)
- JSONB без GIN-индекса
- Partial index не используется
- Covering index (INCLUDE)

Инструменты:
- EXPLAIN (ANALYZE, BUFFERS, SETTINGS, FORMAT JSON)
- pg_stat_statements
- pg_stat_activity
- pg_locks
- pg_stat_user_tables (n_dead_tup, last_autovacuum)
- autovacuum
- pg_bigm / pg_trgm
- pg_stat_wal
- pg_stat_replication
- pgBadger (логи)
- pg_stat_io
- HypoPG (test indexes)
- pg_stat_progress_*
- pg_buffercache

Буду писать на русском, как и вся беседа. Структурированно, с кодом.

</details>

**Кандидат:**

## Принцип

«Запрос медленный» — это не диагноз. Это симптом. Может быть один запрос на 5 секунд. Может быть 200 запросов по 25 мс. Может быть запрос быстрый, но за ним стоит lock на 30 секунд. Диагностика начинается с вопроса «что именно и сколько времени».

---

## Шаг 1: Измерить, а не гадать

### 1.1. Найти медленный запрос

```sql
-- Включить pg_stat_statements (в postgresql.conf):
-- shared_preload_libraries = 'pg_stat_statements'
-- pg_stat_statements.track = all

CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Топ-20 по времени (с учётом частоты):
SELECT
    calls,
    round(total_exec_time::numeric, 2) AS total_ms,
    round(mean_exec_time::numeric, 2) AS mean_ms,
    round(max_exec_time::numeric, 2) AS max_ms,
    rows,
    shared_blks_hit + shared_blks_read AS blks,
    round((shared_blks_hit::numeric / greatest(shared_blks_hit + shared_blks_read, 1)) * 100, 1) AS hit_pct,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

Что ищу:

| Признак | Диагноз |
|---|---|
| `calls` высокий, `mean_ms` средний | Систематическая проблема. N+1 или горячий путь. |
| `calls` низкий, `max_ms` огромный | Однократный тяжёлый запрос. Отчёт, миграция, админка. |
| `rows` маленький (0–10), `mean_ms` большой | План плохой. Индекс не найден. |
| `rows` большой (100 000+), `mean_ms` большой | Нормально для bulk. Может быть OK. |
| `hit_pct` < 90% | Диск-IO. Данные не в buffer cache. |

### 1.2. Живые запросы

```sql
-- Что сейчас выполняется:
SELECT
    pid,
    now() - query_start AS duration,
    wait_event_type,
    wait_event,
    state,
    left(query, 100) AS query
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY query_start;
```

| `wait_event_type` | Что происходит |
|---|---|
| `CPU` | Запрос считает. Нормально, если кратко. |
| `IO` → `Read` / `Write` | Диск. Данные не в памяти. |
| `Lock` → `transactionid` / `tuple` / `relation` | Ожидание блокировки. |
| `LWLock` → `buffer_content` | Конкуренция за buffer pages. |
| `Activity` → `bgwriter` | Фоновая запись. Редко блокирует. |
| `Client` → `ClientRead` / `ClientWrite` | Сеть. Кlien не читает результат. |

### 1.3. Блокировки

```sql
-- Кто кого блокирует:
SELECT
    blocked.pid AS blocked_pid,
    blocked.usename AS blocked_user,
    left(blocked.query, 80) AS blocked_query,
    blocking.pid AS blocking_pid,
    blocking.usename AS blocking_user,
    left(blocking.query, 80) AS blocking_query,
    now() - blocked.query_start AS blocked_for
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND NOT bl.granted
JOIN pg_locks gl ON gl.locktype = bl.locktype
    AND gl.database IS NOT DISTINCT FROM bl.database
    AND gl.relation IS NOT DISTINCT FROM bl.relation
    AND gl.page IS NOT DISTINCT FROM bl.page
    AND gl.tuple IS NOT DISTINCT FROM bl.tuple
    AND gl.virtualxid IS NOT DISTINCT FROM bl.virtualxid
    AND gl.transactionid IS NOT DISTINCT FROM bl.transactionid
    AND gl.classid IS NOT DISTINCT FROM bl.classid
    AND gl.objid IS NOT DISTINCT FROM bl.objid
    AND gl.objsubid IS NOT DISTINCT FROM bl.objsubid
    AND gl.pid != bl.pid AND gl.granted
JOIN pg_stat_activity blocking ON blocking.pid = gl.pid
ORDER BY blocked_for DESC;
```

Если `blocked_for` > 5 секунд — это не «медленный запрос», а «запрос ждёт».

---

## Шаг 2: EXPLAIN ANALYZE

### 2.1. Правильный вызов

```sql
EXPLAIN (ANALYZE, BUFFERS, SETTINGS, FORMAT TEXT)
SELECT o.id, o.status, o.total_cents, c.email,
       oi.product_id, oi.quantity
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN customers c ON c.id = o.customer_id
WHERE o.status = 'pending'
  AND o.created_at > NOW() - INTERVAL '7 days'
  AND c.email ILIKE '%@example.com'
ORDER BY o.created_at DESC
LIMIT 50;
```

Параметры:

| Флаг | Зачем |
|---|---|
| `ANALYZE` | Реальное время, не оценка. Выполняет запрос. |
| `BUFFERS` | Shared buffers, temp buffers, hit/miss. |
| `SETTINGS` | Значения `work_mem`, `statement_timeout` и др. |
| `FORMAT TEXT` | Читаемо. `FORMAT JSON` — для парсинга. |

### 2.2. Чтение плана — что я ищу

Пример медленного плана:

```
Limit  (cost=1234567.89..1234568.01 rows=1 width=120) (actual time=4523.112..4523.115 rows=1 loops=1)
  Buffers: shared hit=456123 read=89452
  ->  Sort  (cost=1234567.89..1234567.90 rows=1 width=120) (actual time=4523.100..4523.101 rows=50 loops=1)
        Sort Key: o.created_at DESC
        Sort Method: top-N heapsort  Memory: 38kB
        Buffers: shared hit=456123 read=89452
        ->  Nested Loop  (cost=0.70..1234567.80 rows=1 width=120) (actual time=0.210..4520.445 rows=52834 loops=1)
              Buffers: shared hit=456123 read=89452
              ->  Seq Scan on customers c  (cost=0.00..894.52 rows=42 width=28) (actual time=0.012..18.445 rows=42 loops=1)
                    Filter: (lower(email) ~~ '%@example.com')
                    Rows Removed by Filter: 118758
                    Buffers: shared hit=456123 read=89452
              ->  Index Scan using idx_orders_status_created on orders o  (cost=0.70..29154.20 rows=1258 width=96) (actual time=0.018..106.234 rows=1258 loops=42)
                    Index Cond: (status = 'pending' AND created_at > ...)
                    Buffers: shared hit=89012
              ->  Index Scan using order_items_order_id_product_id on order_items oi  (cost=0.43..1.43 rows=1 width=40) (actual time=0.005..0.005 rows=1 loops=52834)
                    Index Cond: (order_id = o.id)
                    Buffers: shared hit=367111
Planning Time: 0.312 ms
Execution Time: 4523.567 ms
```

### 2.3. Красные флаги в плане

| Симптом | Что значит |
|---|---|
| `Seq Scan on <big_table>` | Нет индекса. Или индекс есть, но planner считает, что seq scan дешевле. |
| `Rows Removed by Filter: 99%` | Индекс не покрывает фильтр. Фильтр применяется после scan. |
| `Nested Loop` с `loops=52834` | Один из инпутов большой, внутренний scan повторяется. Hash Join был бы дешевле. |
| `Sort Method: external merge` | `work_mem` не хватила. Сортировка на диск. |
| `Buffers: shared hit=0 read=1000000` | Всё с диска. Данные не в buffer cache. |
| `actual time=0.001 rows=0 loops=1` | Вложенный scan ничего не нашёл, но вызывался N раз. |
| `Heap Fetches: 50000` (в index scan) | Много dead tuples. Index points to heap rows, которые уже удалены. Bloat. |
| `ctid` в index scan, но `Heap Fetches` высокий | Autovacuum не успел. |
| `Index Cond` ≠ `Filter` | Индекс используется для части условий. Остальное — post-filter. |
| `Parallel Seq Scan` | Планировщик решил параллелить seq scan. Таблица огромная, индекс не подходит. |

### 2.4.BUFFERS — ключ к IO

```
Buffers: shared hit=456123 read=89452
```

- `hit` — из shared_buffers (RAM). Быстро.
- `read` — с диска. Медленно.
- `hit_pct = hit / (hit + read) * 100`

| hit_pct | Диагноз |
|---|---|
| > 99% | Данные в RAM. Проблема в CPU / плане. |
| 90–99% | Частично в RAM. Часть с диска. |
| < 90% | Данные не в RAM. Диск-IO. Увеличить `shared_buffers` / `work_mem`. Или данные слишком большие для RAM. |

---

## Шаг 3: Типовые причины и лечение

### 3.1. Missing index

**Симптом:** `Seq Scan` на таблице > 10 000 строк. `Rows Removed by Filter` > 90%.

```sql
-- Запрос фильтрует по status + created_at:
WHERE status = 'pending' AND created_at > NOW() - INTERVAL '7 days'

-- Нет индекса:
-- Seq Scan on orders (cost=0.00..4523.00 rows=1258 width=96)
--   Filter: (status = 'pending' AND created_at > ...)
--   Rows Removed by Filter: 498742

-- Лечение:
CREATE INDEX CONCURRENTLY idx_orders_status_created
    ON orders (status, created_at DESC);
--
-- CONCURRENTLY: не блокирует DML. Долго, но безопасно.
-- (status, created_at DESC): композитный. status = equality, created_at = range.
-- Порядок: equality сначала, range потом.
-- DESC: ORDER BY created_at DESC → index scan в правильном направлении, без Sort.
```

**Почему `CONCURRENTLY`:** `CREATE INDEX` (без `CONCURRENTLY`) берёт `SHARE` lock на таблицу. Блокирует все `INSERT/UPDATE/DELETE`. В проде — недопустимо. `CONCURRENTLY` — два прохода, не блокирует, но если упадёт — оставит `INVALID` index, который нужно `DROP`.

### 3.2. Индекс есть, но не используется

**Симптом:** `Index Scan` в плане отсутствует. `Seq Scan`. Индекс существует.

Причины:

| Причина | Как проверить | Лечение |
|---|---|---|
| SELECTivity < 30% | `COUNT(*) FILTER (WHERE cond) / COUNT(*)` | Planner прав: seq scan дешевле. Не «чинить», а оптимизировать запрос. |
| Статистики устарели | `n_live_tup` в `pg_stat_user_tables` vs реальное `COUNT(*)` | `ANALYZE orders;` |
| `work_mem` мал | `SET work_mem = '256MB'; EXPLAIN ANALYZE ...` | Увеличить `work_mem` (session или global). |
| Индекс не покрывает ORDER BY | `Sort` после `Index Scan` | Переопределить индекс: `(status, created_at DESC)` вместо `(created_at DESC)`. |
| `ILIKE '%pattern%'` | `Seq Scan` | `pg_trgm` GIN-индекс. |
| `COUNT(DISTINCT ...)` на большой таблице | HashAggregate + Seq Scan | Materialized view / partial index. |

**Статистики:**

```sql
-- Посмотреть:
SELECT
    relname,
    n_live_tup,
    n_dead_tup,
    round(n_dead_tup::numeric / greatest(n_live_tup + n_dead_tup, 1) * 100, 1) AS dead_pct,
    last_autovacuum,
    last_autoanalyze,
    autovacuum_count,
    autoanalyze_count
FROM pg_stat_user_tables
ORDER BY n_dead_tup DESC
LIMIT 20;

-- Обновить:
ANALYZE orders;
ANALYZE order_items;

-- Если n_dead_tup > 20% и last_autovacuum > 1 час:
-- Autovacuum не успевает. Увеличить:
-- autovacuum_vacuum_scale_factor = 0.05  (default 0.2)
-- autovacuum_vacuum_cost_delay = 2      (default 20ms)
```

### 3.3. N+1 на уровне БД

**Симптом:** `pg_stat_statements` показывает 500 вызовов одного и того же запроса:

```
calls=500, mean_ms=3.2, max_ms=12.1
query: SELECT * FROM order_items WHERE order_id = $1
```

500 × 3.2 мс = 1.6 секунды. Каждый запрос быстрый. Суммарно — нет.

**Лечение:** уже обсуждали (JOIN FETCH, batch, DataLoader). На уровне PG — `pg_stat_statements` помогает **увидеть** паттерн:

```sql
SELECT
    queryid,
    calls,
    round(total_exec_time / calls, 2) AS mean_ms,
    round(total_exec_time, 0) AS total_ms,
    left(regexp_replace(query, '\s+', ' ', 'g'), 100) AS query
FROM pg_stat_statements
GROUP BY queryid, query
HAVING calls > 10
ORDER BY total_exec_time DESC
LIMIT 10;
```

Если `calls > 50` и `query` содержит `WHERE id = $1` — это N+1.

### 3.4. Lock contention

**Симптом:** `wait_event_type = 'Lock'`, `wait_event = 'tuple'`. Запрос ждёт > 5 секунд.

```sql
-- Кто держит lock:
SELECT
    pid,
    usename,
    now() - xact_start AS xact_age,
    wait_event_type,
    wait_event,
    state,
    left(query, 100) AS query
FROM pg_stat_activity
WHERE state = 'active'
ORDER BY xact_start;
```

Типовой сценарий:

```
t=0s   Transaction A: BEGIN; UPDATE orders SET status='confirmed' WHERE id=42;  -- держит row lock
t=0s   Transaction A: ... делает HTTP-запрос к внешнему API (30 секунд) ...
t=1s   Transaction B: BEGIN; UPDATE orders SET total_cents=... WHERE id=42;   -- ждёт lock
t=30s  Transaction A: COMMIT;
t=30s  Transaction B: ... наконец UPDATE ...
```

**Причина:** долгая транзакция с внешними вызовами.

**Лечение:**

```php
// Плохо:
$this->em->transactional(function () {
    $this->orders->confirm($order);
    $this->http->post('https://external-api/notify', ...);  // 30 секунд с lock!
    $this->em->flush();
});

// Хорошо:
$this->em->transactional(function () {
    $this->orders->confirm($order);
    $this->em->flush();   // COMMIT. Lock освобождён.
});
$this->http->post('https://external-api/notify', ...);  // вне транзакции
```

**Правило:** транзакция — минимальная. Внешние вызовы (HTTP, queue publish, file write) — **после** `COMMIT`, не внутри.

### 3.5. Bloat (dead tuples)

**Симптом:** `Heap Fetches` высокий в `Index Scan`. `n_dead_tup` > 20%. Запросы стали медленнее без изменения данных.

```
Index Scan using idx_orders_status on orders  (cost=0.42..1234.56 rows=1258 width=96)
  Index Cond: (status = 'pending')
  Heap Fetches: 45000
  Buffers: shared hit=89012
```

`Heap Fetches: 45000` — index указал на 45 000 heap-строк, большинство из которых уже `dead` (удалены, но не vacuum'нуты). Planner считал 1258 строк, а физически прошёл 45 000.

**Лечение:**

```sql
-- 1. Vacuum:
VACUUM (ANALYZE, VERBOSE) orders;

-- 2. Проверить autovacuum:
SELECT relname, last_autovacuum, autovacuum_count
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- 3. Если autovacuum не успевает:
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.05,   -- default 0.2
    autovacuum_vacuum_threshold = 100,       -- default 50
    autovacuum_analyze_scale_factor = 0.02   -- default 0.1
);
```

**Предотвращение:** batch-обновления вместо `DELETE + INSERT`:

```sql
-- Плохо: 10 000 DELETE + 10 000 INSERT = 20 000 dead tuples
DELETE FROM sessions WHERE expires_at < NOW();
INSERT INTO sessions (...) SELECT ...;

-- Хорошо: один UPDATE
UPDATE sessions SET expires_at = NULL WHERE expires_at < NOW();
```

### 3.6. ILIKE / полнотекстовый поиск

**Симптом:** `Seq Scan` + `Filter: (lower(email) ~~ '%@example.com')`. 200 000 строк, 50 мс.

**Варианты лечения:**

| Метод | Когда | Ограничения |
|---|---|---|
| `pg_trgm` + GIN | `ILIKE '%substr%'` | Только `LIKE`/`ILIKE`. Не `REGEXP`. |
| Full-text search (`tsvector`) | Семантический поиск по тексту | Не для email, phone, ID. |
| `BRIN` index | Поля отсортированы по insert (timestamps) | Не для случайных значений. |
| Разбиение на поля | `email → local, domain` | Миграция. Изменение схемы. |
| Redis / Meilisearch | Поисковый сервис | Дополнительная инфраструктура. |

```sql
-- pg_trgm:
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX CONCURRENTLY idx_users_email_trgm
    ON users USING gin (email gin_trgm_ops);

-- Теперь:
-- WHERE email ILIKE '%@example.com'
-- → GIN Index Scan. 50 мс → 2 мс.
```

### 3.7. JSONB без индекса

**Симптом:**

```sql
SELECT * FROM events
WHERE payload->>'type' = 'order_placed'
  AND payload->>'order_id' = 'uuid-42';
-- Seq Scan. 10 000 000 строк. 2.3 секунды.
```

**Лечение:**

```sql
-- GIN для операторов @>, ?,
CREATE INDEX CONCURRENTLY idx_events_payload
    ON events USING gin (payload jsonb_path_ops);
-- jsonb_path_ops: быстрее, но поддерживает только @>, ?, ?|
-- gin (payload): поддерживает также @> c. Медленнее, но гибче.

-- Для конкретного пути (быстрее):
CREATE INDEX CONCURRENTLY idx_events_payload_type
    ON events ((payload->>'type'));

CREATE INDEX CONCURRENTLY idx_events_payload_order
    ON events ((payload->>'order_id'));
```

### 3.8. `work_mem` и сортировка

**Симптом:**

```
Sort  (cost=... rows=500000 width=96)
  Sort Method: external merge  Disk: 24576kB
```

`external merge` — сортировка не влезла в `work_mem`, пошла на диск.

```sql
-- Проверить текущее значение:
SHOW work_mem;
-- work_mem = 4MB  (default)

-- Для конкретного запроса:
SET work_mem = '256MB';
EXPLAIN ANALYZE
SELECT ... ORDER BY created_at DESC;
-- Sort Method: top-N heapsort  Memory: 38kB  ← в RAM

-- Глобально (postgresql.conf):
work_mem = 64MB
--
-- ВНИМАНИЕ: work_mem — на каждую сортировку/джойн.
-- 10 параллельных запросов × 64MB = 640MB.
-- Не ставить 1GB. Считать по нагрузке.
```

### 3.9. Subquery и CTE

**Симптом:**

```sql
SELECT o.*
FROM orders o
WHERE o.id IN (
    SELECT order_id FROM order_items WHERE quantity > 10
);
```

Если внутренний subquery возвращает 500 000 строк — `Hash Semi Join`. Если 10 строк — `Nested Loop`.

**Проверка:**

```sql
-- EXPLAIN покажет, как planner распарсил:
-- Hash Semi Join  (cost=...)
--   ->  Seq Scan on orders
--   ->  Hash
--         ->  Seq Scan on order_items
--               Filter: (quantity > 10)
```

Если `IN (subquery)` → `Hash Semi Join` — нормально. Если → `SubPlan` в `Filter` — плохо (коррелированный, выполняется N раз).

**Лечение:** переписать в `EXISTS` (коррелированный, но останавливается на первом совпадении):

```sql
SELECT o.*
FROM orders o
WHERE EXISTS (
    SELECT 1 FROM order_items oi
    WHERE oi.order_id = o.id AND oi.quantity > 10
);
```

### 3.10. `SELECT *` + широкие строки

**Симптом:** запрос быстрый, но передача 200 МБ данных.

```sql
-- Плохо:
SELECT * FROM events;  -- 50 колонок, включая JSONB payload (10KB)
-- 1 000 000 строк × 10KB = 10 ГБ.

-- Хорошо:
SELECT id, type, created_at FROM events;  -- 50 байт/строку
```

**На уровне application:** не `SELECT *`. Только нужные поля. `JOIN FETCH` — только нужные связи.

---

## Шаг 4: Индексы — проектирование

### 4.1. Порядок колонок

```sql
-- WHERE status = 'pending' AND created_at > '2024-01-01' ORDER BY created_at DESC
--
-- ПРАВИЛЬНО:
CREATE INDEX ON orders (status, created_at DESC);
--
-- status = equality → первая колонка.
-- created_at = range + ORDER BY → вторая колонка. DESC — без Sort.
--
-- НЕПРАВИЛЬНО:
CREATE INDEX ON orders (created_at DESC, status);
--
-- created_at = range. Planner использует index для range,
-- но status = post-filter. И ORDER BY — не из индекса (status мешает).
-- → Sort + Filter. Медленнее.
```

**Правило:** `equality → range → ORDER BY`. В этом порядке.

### 4.2. Covering index (INCLUDE)

```sql
-- Запрос:
SELECT id, status, total_cents FROM orders WHERE customer_id = ?;
--
-- Index on (customer_id) → Index Scan + Heap Fetch (каждая строка).
--
-- Covering:
CREATE INDEX idx_orders_customer_covering
    ON orders (customer_id)
    INCLUDE (id, status, total_cents);
--
-- Index Only Scan. Нет heap fetch. Быстрее.
--
-- INCLUDE: поля не участвуют в B-tree. Только храним в leaf-узлах.
-- Не влияет на selectivity. Не участвует в ORDER BY.
```

**Когда:** `Index Scan` + высокий `Heap Fetches`. SELECT-поля известны и стабильны.

**Ограничение:** `INCLUDE` не уменьшает индекс по ключу. Только добавляет данные. Индекс становится шире → медленнее на INSERT/UPDATE.

### 4.3. Partial index

```sql
-- Активные заказы: 5% от всех. Неактивные: 95%.
-- Запрос: WHERE status = 'pending' AND created_at > ...

-- Полноценный индекс: 10 000 000 строк.
-- Partial: 500 000 строк. В 20 раз меньше.

CREATE INDEX idx_orders_active
    ON orders (created_at DESC)
    WHERE status = 'pending';
--
-- Planner использует ТОЛЬКО для WHERE status = 'pending'.
-- Для status = 'delivered' → не использует. Seq Scan.
--
-- Плюс: меньше в RAM. Быстрее vacuum. Меньше overhead на INSERT.
```

**Когда:** фильтр по статусу / флагу, который отбирает < 30% строк.

### 4.4. Индекс на выражение

```sql
-- WHERE lower(email) = 'a@b.com'
-- Seq Scan. lower() — функция. Обычный index на email не поможет.

CREATE INDEX idx_users_email_lower
    ON users (lower(email));
--
-- Теперь: Index Scan. lower(email) вычисляется один раз при INSERT,
-- а не на каждый запрос.
```

### 4.5. Когда индекс **не** нужен

| Ситуация | Почему |
|---|---|
| Таблица < 1 000 строк | Seq scan быстрее, чем index + heap. |
| SELECTivity > 50% | Брать половину строк через index + heap = медленнее, чем seq scan. |
| Таблица читается редко | Index overhead на INSERT/UPDATE не окупается. |
| `ORDER BY random()` | Нет индекса на random. |
| `COUNT(*)` без WHERE | Seq scan + count. Index не поможет. |

---

## Шаг 5: Настройки PostgreSQL

### 5.1. Критические параметры

```ini
# postgresql.conf

# RAM: 64GB сервер
shared_buffers = 16GB          # 25% RAM. Buffer pool.
work_mem = 64MB               # На каждую сортировку/джойн. НЕ 1GB!
effective_cache_size = 48GB   # Подсказка planner'у. «У меня есть 48GB в RAM».
                              # Не выделяет память. Влияет на cost-оценки.
maintenance_work_mem = 1GB    # VACUUM, CREATE INDEX. Временная.

# Autovacuum
autovacuum = on
autovacuum_vacuum_scale_factor = 0.05   # default 0.2. Частее vacuum.
autovacuum_analyze_scale_factor = 0.02  # default 0.1. Частее analyze.
autovacuum_vacuum_cost_delay = 2        # default 20ms. Быстрее vacuum.

# WAL
wal_buffers = 64MB            # default -1 (автоматически). 16MB–64MB.
max_wal_size = 2GB            # Checkpoint frequency.
min_wal_size = 512MB

# Planner
random_page_cost = 1.1        # SSD: random ≈ sequential. Default 4.0 — для HDD.
                              # На SSD/NVMe: 1.0–1.5.
effective_io_concurrency = 256  # SSD. Default 200.
```

**`random_page_cost`** — одна из самых недооценённых настроек. Default `4.0` говорит planner'у: «random IO в 4 раза дороже sequential». На NVMe-SSD — это неправда. Random ≈ sequential. Если не поменять — planner **систематически** избегает index scan, предпочитая seq scan.

```sql
-- Проверить:
SHOW random_page_cost;
-- 4.0  → на SSD сменить на 1.1
```

### 5.2. Connection pool

```
300 concurrent connections × 64MB work_mem = 19.2 GB
+ shared_buffers 16GB = 35.2 GB
→ OOM.

Решение:
  - PgBouncer / Supavisor / pgbouncer
  - max_connections = 100 (на PG)
  - PgBouncer: 500 client connections → 100 PG connections
  - transaction pooling (не session) для коротких запросов
```

---

## Шаг 6: Антипаттерны «оптимизации»

### 1. `SELECT *` + «а потом в PHP отфильтрую»

```php
$orders = $repo->findAll();  // 100 000 строк × 50 колонок
$orders = array_filter($orders, fn($o) => $o->status === 'pending');
```

100 000 строк передано по сети. 50 колонок десериализованы. 95% выброшено.

```php
// Вместо:
$orders = $repo->findBy(['status' => 'pending']);
// → WHERE status = 'pending'. 5 000 строк.
```

### 2. `COUNT(*)` на большой таблице

```sql
SELECT COUNT(*) FROM orders WHERE status = 'pending';
-- 500 000 строк. 2 секунды.
```

```sql
-- Альтернативы:
-- 1. Approximate (если точность не критична):
SELECT n_live_tup FROM pg_stat_user_tables WHERE relname = 'orders';
-- 0 мс. Но нет WHERE.

-- 2. Materialized counter:
CREATE TABLE order_counters (status TEXT, count INT);
-- UPDATE в trigger. SELECT = 0 мс.

-- 3. По страницам:
SELECT COUNT(*) FROM orders WHERE status = 'pending' AND created_at > NOW() - INTERVAL '1 day';
-- Меньше строк.
```

### 3. «Оптимизация» через `SET` в каждом запросе

```sql
SET work_mem = '1GB';
SET statement_timeout = '0';  -- отключить timeout
SET synchronous_commit = off; -- для speed
-- ... query ...
```

- `work_mem = 1GB` × 100 соединений = 100GB. OOM.
- `statement_timeout = 0` — «быстрый» запрос на 10 минут. Lock на всю таблицу.
- `synchronous_commit = off` — данные потеряны при crash.

### 4. Индекс на каждую колонку

```sql
CREATE INDEX idx_orders_id ON orders (id);
CREATE INDEX idx_orders_status ON orders (status);
CREATE INDEX idx_orders_created ON orders (created_at);
CREATE INDEX idx_orders_customer ON orders (customer_id);
CREATE INDEX idx_orders_total ON orders (total_cents);
CREATE INDEX idx_orders_updated ON orders (updated_at);
-- 6 индексов. INSERT = 6 B-tree обновлений.
```

**Правило:** индекс = стоимость на запись. Если таблица write-heavy (events, logs) — каждый лишний индекс = 10–30% overhead на INSERT.

### 5. `VACUUM FULL` в проде

```sql
VACUUM FULL orders;  -- переписывает всю таблицу. EXCLUSIVE lock.
```

`VACUUM FULL` — `EXCLUSIVE` lock. Блокирует все DML. На 10 ГБ таблице — 30 минут блокировки. **Не делать в проде.**

**Вместо:** `VACUUM (ANALYZE)` (без `FULL`) + правильный autovacuum.

### 6. Отключение autovacuum «для скорости»

```ini
autovacuum = off
```

Через неделю: 50% dead tuples. `Heap Fetches` × 10. Все запросы × 3 медленнее.

---

## Чек-лист: «запрос медленный»

```
1. EXPLAIN (ANALYZE, BUFFERS) запроса
   ├── Seq Scan на большой таблице? → индекс
   ├── Sort: external merge? → work_mem
   ├── Nested Loop, loops > 1000? → Hash Join
   ├── Heap Fetches > rows × 10? → bloat, VACUUM
   └── hit_pct < 90%? → disk IO, shared_buffers

2. pg_stat_statements: это один запрос или N?
   ├── N > 50 → N+1. Оптимизировать на уровне application.
   └── N = 1 → оптимизировать сам запрос.

3. pg_stat_activity: кто ждёт lock?
   ├── wait_event = Lock → найти blocking query.
   └── Blocking query > 5 секунд → короткая транзакция.

4. pg_stat_user_tables: dead tuples?
   ├── n_dead_tup > 20% → VACUUM.
   └── last_autovacuum > 1 час → tune autovacuum.

5. Индекс:
   ├── Есть, но не используется? → selectivity, statistics, random_page_cost.
   ├── Нет? → CREATE INDEX CONCURRENTLY.
   └── Есть, но не покрывает? → INCLUDE, partial, expression.

6. Настройки:
   ├── random_page_cost = 4.0 на SSD? → 1.1.
   ├── work_mem = 4MB? → 64MB.
   └── shared_buffers = 128MB на 64GB сервере? → 16GB.

7. Сеть:
   ├── hit_pct OK, но запрос медленный? → latency.
   ├── Application → DB: > 1ms? → colocation.
   └── SELECT * на 50 колонок? → только нужные поля.
```

---

## Главное

Медленный запрос — это не «PostgreSQL медленный». Это **несоответствие между запросом и данными**:

- Запрос читает 10 строк, но проходит 10 000 000. → Индекс.
- Запрос читает 1 000 000 строк, и это нормально. → Ограничить (`LIMIT`, `WHERE`).
- Запрос ждёт lock. → Короткая транзакция.
- Запрос сортирует на диск. → `work_mem`.
- Запрос выполняется 500 раз. → N+1.

Диагностика — это не «найти магическую команду». Это **прочитать план**, **понять, что planner делает и почему**, и **сменить форму запроса или структуру данных**, чтобы planner выбрал другой путь.

> ⏱ 76.4s · prompt 69737 tok · output 8909 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: определение, границы, пример с объяснением, типовая ошибка при выборе границ. Это уже четвёртый вопрос в серии про DDD/архитектуру. Я уже использовал Order → OrderItem как пример агрегата. Теперь нужно:

1. Чётко определить, что такое агрегат (не путать с entity, не путать с таблицей).
2. Объяснить критерии определения границ — это самое важное.
3. Привести пример с объяснением — можно продолжить с Order или взять новый.
4. Типовая ошибка при выборе границ — это то, что чаще всего встречается на практике.

Давай подумаю о структуре ответа:

**Определение:** Агрегат — это кластер сущностей, которые рассматриваются как единое целое для целей сохранения целостности. Корень агрегата — единственный вход. Внешний код ссылается на сущности внутри агрегата только через корень (по ID). Инварианты агрегата гарантируются внутри транзакции.

**Ключевые свойства:**
- Инварианты (invariants) — условия, которые всегда должны быть истинными
- Корень (root) — единственный вход
- Identity — корень имеет ID, дети могут не иметь
- Транзакционная целостность — все изменения внутри агрегата атомарны

**Границы:** Это самое сложное. Критерии:
1. Какие инварианты должны держаться атомарно?
2. Какие сущности не имеют смысла вне контекста друг друга?
3. Что нужно загружать/сохранять вместе?
4. Какие операции должны быть транзакционно целостными?

Типовые ошибки:
1. Слишком большой агрегат ("Big Ball of Mud" в DDD) — Order + Payment + Shipping + Notification + Inventory + Customer — всё в одном. Проблемы: contention, сложно менять, транзакция длинная.
2. Слишком маленький агрегат — каждую сущность отдельно. Проблемы: нарушение инвариантов, race conditions, сложность координации.
3. Путаница агрегата с таблицей/модулем/сервисом.
4. Ссылки на entities внутри других агрегатов по объекту (а не по ID).

Лучший пример ошибки: когда делают Order → Customer ссылку как объект (не ID), и Customer становится частью агрегата Order. Или когда Payment + Order + Inventory — один агрегат.

Давай возьму конкретный пример. У меня уже был Order → OrderItem. Теперь возьму более сложный сценарий, где границы неочевидны. Например:

**Хороший пример границ:** Order (root) + OrderItem (child). Инвариант: total = sum(items). Order не может быть пустым. Order не может быть cancelled, если уже shipped.

**Плохой пример границ (типовая ошибка):** Order + Customer + Product + Payment + Inventory — все в одном агрегате. Почему плохо:
- Customer живёт свою жизнь независимо от Order
- Product живёт независимо
- Inventory — отдельный Bounded Context
- Payment — отдельный Bounded Context
- Contention: 1000 заказов в секунду на один Product → lock contention
- Транзакция: чтобы обновить Order, нужно lock'нуть Customer, Product, Inventory, Payment → 4 lock'а → deadlock risk

**Ещё одна типовая ошибка:** Order → Customer как объектная ссылка (не ID). Тогда Customer становится частью агрегата Order. Изменение email клиента требует обновления ВСЕХ его заказов. Или: удаление клиента → каскад на 10 000 заказов.

**Третья ошибка:** слишком мелкий агрегат. Order и OrderItem — отдельные агрегаты. Тогда инвариант "total = sum(items)" нельзя гарантировать атомарно. Между save(order) и save(items) — окно, где данные несогласованны.

Структурирую ответ:
1. Определение (чётко, коротко)
2. Свойства агрегата
3. Как определяю границы (алгоритм)
4. Пример: Order — правильный агрегат
5. Типовая ошибка: "крупный агрегат" (Order + Customer + Product + Inventory)
6. Вторая типовая ошибка: слишком мелкий (Order и Item разнесены)
7. Критерии: что делает агрегат правильным

Пишу на русском, как и весь диалог. Использую код где уместно. Не перегружаю.

</details>

**Кандидат:**

## Что такое агрегат

Агрегат — это **кластер сущностей, для которых инварианты гарантируются атомарно**. Не «группа связанных таблиц». Не «модуль». Не «сервис». Агрегат — это **граница транзакционной целостности**: всё, что внутри, должно быть согласовано в один момент времени. Внутренние сущности не имеют самостоятельного существования вне агрегата.

Это даёт четыре свойства:

| Свойство | Смысл |
|---|---|
| **Корень** | Единственный вход. Внешний код оперирует только корнем. Дети — по ссылке от корня, не напрямую. |
| **Инварианты** | Условия, которые всегда истинны для любого агрегата. Нарушение — исключение, не состояние. |
| **Identity** | Корень имеет внешний ID. Дети могут не иметь (или иметь внутренний ключ). |
| **Транзакционная целостность** | Все изменения внутри агрегата атомарны. Нельзя быть «наполовину обновлённым». |

Важно: агрегат — **не** единица деплоя. Не микросервис. Не модуль. Не Bounded Context. Он меньше, чем Bounded Context, и больше, чем сущность.

---

## Как я определяю границы

Это не теоретический вопрос. Это набор практических вопросов, которые я задаю по очереди:

### Вопрос 1: Какие инварианты должны держаться атомарно?

> «Если X изменилось, Y **обязан** измениться в тот же момент, иначе система в невалидном состоянии.»

Это главный критерий. Если инвариант требует атомарности — сущности в одном агрегате. Если инвариант допускает задержку (eventual consistency) — в разных.

Пример:
- `Order.total = SUM(OrderItem.price × qty)` — **атомарный**. Изменили позицию — total пересчитывается сразу. Одна транзакция. → Один агрегат.
- `Order` → `Invoice` создан — **не атомарный**. Инвойс может появиться через 5 секунд (async event). → Разные агрегаты.

### Вопрос 2: Существует ли сущность без другого?

> «Можно ли удалить Order, оставив OrderItem?»

Нет. OrderItem без Order не имеет смысла. Нет `order_id` — нет связи, нет контекста. → `OrderItem` внутри агрегата `Order`.

> «Можно ли удалить Customer, оставив Order?»

Технически да ( FK `ON DELETE RESTRICT` заблокирует). Но семантически — Order ссылается на Customer по **ID**, не по объекту. Customer живёт свою жизнь: меняет email, телефоны, адрес. Order не должен «знать» об этом. → Customer — **отдельный** агрегат.

### Вопрос 3: Кто оперирует этим в одном use case?

> «Какие сущности я трогаю в рамках одной команды?»

`PlaceOrder` → создаю `Order`, добавляю `OrderItem[]`, сохраняю. Один use case, один агрегат.

`PlaceOrder` → также резервирую `Inventory`. Но `Inventory` живёт свою жизнь: пополнение, списание, перемещение между складами. Я не «создаю» инвентарь в рамках заказа. Я **спрашиваю** его: «хватит ли?» и **получаю ответ**. → `Inventory` — отдельный агрегат в другом Bounded Context.

### Вопрос 4: Что происходит при конфликте (contention)?

> «Сколько параллельных операций могут затрагивать один экземпляр?»

`OrderItem` — только один `Order` пишет. Нет contention.

`Inventory` (SKU-1, склад-A) — 100 заказов в секунду могут резервировать. Если `Inventory` внутри агрегата `Order` — каждый заказ lock'ит `Inventory` → contention на 100 параллельных транзакций. Если `Inventory` — отдельный агрегат с собственным механизмом (optimistic lock, queue) — contention управляемая.

### Вопрос 5: Какую гранулярность загрузки нужно?

> «Когда я загружаю Order, мне нужны Customer, Product, Invoice, Shipping?»

Нет. `Order` — `OrderItem[]` — `productId` (ID, не объект). Customer — по ID, когда нужно. Product — по ID, когда нужно. Инвариант `total = sum(items)` требует только `OrderItem`. → `OrderItem` внутри. `Customer`, `Product` — вне.

---

## Пример: Order — правильный агрегат

```
┌─────────────────────────────────────────────────────────┐
│  AGGREGATE: Order                                       │
│                                                         │
│  Order (root)                                           │
│  ├── id: OrderId                                        │
│  ├── customerId: string        ← ID, не объект         │
│  ├── status: OrderStatus      ← enum                    │
│  ├── totalCents: int          ← инвариант: = sum(items) │
│  ├── createdAt: datetime                                     │
│  └── items: list<OrderItem>    ← дети агрегата          │
│       ├── productId: string    ← ID, не объект          │
│       ├── quantity: int        ← инвариант: ≥ 1         │
│       └── unitPriceCents: int  ← инвариант: ≥ 0         │
│                                                         │
│  Инварианты:                                            │
│  • items не пустые                                      │
│  • totalCents = Σ(item.quantity × item.unitPriceCents)  │
│  • status transition: pending → confirmed → shipped     │
│  • quantity ≥ 1 для каждого item                        │
│                                                         │
│  Внешний доступ: только через Order                     │
│  • order.addItem(item)                                   │
│  • order.removeItem(productId)                           │
│  • order.confirm()                                       │
│  • order.totalCents()                                    │
│  • НЕ: order.items[0].quantity = 5  ← запрещено         │
└─────────────────────────────────────────────────────────┘

        ↕ ссылки по ID (не по объекту)

┌────────────────────┐    ┌────────────────────┐
│  Customer (отдельный│    │  Inventory (отдель-│
│  агрегат)          │    │  ный агрегат)      │
│  • id              │    │  • sku             │
│  • email           │    │  • warehouseId     │
│  • name            │    │  • quantity        │
│  • address         │    │  • reservedQty     │
│                    │    │                    │
│  Инварианты:       │    │  Инварианты:       │
│  • email valid     │    │  • quantity ≥ 0    │
│  • name non-empty  │    │  • reserved ≤ qty  │
└────────────────────┘    └────────────────────┘
```

### Почему Customer — вне агрегата

- Customer меняет email. Это не должно трогать 10 000 заказов.
- Order хранит `customerId: string`. Не `customer: Customer`. Ссылка по ID.
- Если Customer удалён — `FK RESTRICT` заблокирует. Но это **инфраструктурная** защита, не доменная. Доменный код проверяет: «Customer существует?» → `customerRepo.findById(id)` → `null` → `CustomerNotFound`.
- Customer и Order живут в **разных Bounded Context**'ах (Identity vs. Ordering).

### Почему Inventory — вне агрегата

- `PlaceOrder` → `inventory.reserve(sku, qty)` → ответ: `OK` / `InsufficientStock`.
- Inventory живёт свою жизнь: пополнение, перемещение, пересчёт.
- 100 параллельных заказов на один SKU — contention управляется **внутри** Inventory (optimistic lock), а не в Order.
- Если Inventory внутри Order: каждый `order.save()` lock'ит `Inventory` → 100 параллельных lock'ов → deadlock.

### Почему OrderItem — внутри

- `totalCents = SUM(items)` — атомарный инвариант. Изменили item → total пересчитывается.
- OrderItem без Order не существует. Нет `order_id` → нет контекста.
- `orphanRemoval` в Doctrine: убрали из коллекции → DELETE. Семантика «детей нет без родителя».

---

## Типовая ошибка: «крупный агрегат»

Это то, что я вижу чаще всего.

### Как выглядит

```
AGGREGATE: Order (по мнению команды)
├── Order (root)
├── OrderItem[]
├── Customer        ← объект, не ID
├── Product         ← объект, не ID (на каждый item)
├── Payment         ← объект
├── Invoice         ← объект
├── ShippingLabel   ← объект
├── InventoryReservation  ← объект
└── Notification[]  ← объекты
```

Одна транзакция: `save(order)` → persist'ит **всё**. Одна БД. Один lock. Одна точка отказа.

### Почему это плохо

**1. Contention.**

100 заказов в секунду на `Product(SKU-1)`. Каждый заказ → `save(order)` → UPDATE `products SET stock = stock - 1`. 100 параллельных UPDATE на одну строку. PostgreSQL: 1 commit'ит, 99 ждут lock. Latency растёт линейно с нагрузкой.

Если `Inventory` — отдельный агрегат:

```
Order:   INSERT orders + INSERT order_items         → 1 транзакция
Inventory: UPDATE inventory SET reserved = reserved + 1 WHERE sku = ? → 1 транзакция
```

Два lock'а на разных таблицах. Contention управляется **внутри** Inventory (optimistic lock: `WHERE version = ?`). 100 заказов → 100 попыток → 1 успех → 99 retry. Но retry быстрый (1 мс), а не «ждём lock 30 секунд».

**2. Транзакция длинная.**

```php
$this->em->transactional(function () {
    $order = Order::create(...);
    $order->customer = $this->customerRepo->find($id);  // SELECT
    $order->product  = $this->productRepo->find($id);   // SELECT
    $order->payment  = $this->stripe->charge(...);      // HTTP: 500–3000 мс
    $order->invoice  = $this->invoiceService->create(...); // SELECT + INSERT
    $order->shipping = $this->shippingService->label(...); // HTTP: 200–1000 мс
    $this->orderRepo->save($order);                      // INSERT × 7
    $this->em->flush();                                  // COMMIT
});
```

Транзакция: 3 000–6 000 мс. Lock на `Customer`, `Product`, `Payment`, `Invoice`, `Shipping` — **6 000 мс**. В это время:
- Другой запрос не может обновить `Customer`.
- Другой запрос не может изменить `Product.stock`.
- WAL log: 6 000 мс × 100 TPS = 600 секунд WAL в pending state.

Если в этом окне процесс падает (OOM, segfault) → ROLLBACK → но HTTP-запрос к Stripe **уже ушёл**. Деньги списаны. Order откатился. **Несогласованность.**

**3. Изменение Customer трогает Order.**

```php
// Customer меняет email:
$customer->email = new Email("new@example.com");
$this->customerRepo->save($customer);
```

Если Customer **внутри** агрегата Order — это UPDATE на 10 000 заказов. Или: `Customer` не обновляется в 9 999 заказах → несогласованность.

Если Customer **вне** агрегата — `UPDATE customers SET email = ? WHERE id = ?`. Один запрос. 10 000 заказов ссылаются по ID. Email видят через `customerRepo.find()` или через denormalized read-модель.

**4. Сложность изменения.**

Хотите добавить `Discount` в Order. Но `Discount` зависит от `PromoCode`, который живёт в `Marketing` Bounded Context. Если всё в одном агрегате — `Marketing` становится частью `Order`. Изменение правил промокода → правка в агрегате Order → regression risk на весь Order.

### Правильные границы для того же сценария

```
Bounded Context: Ordering
  Агрегат: Order (root)
    • OrderItem[]
    • Инварианты: total, status, items non-empty

Bounded Context: Identity
  Агрегат: Customer
    • email, name, address
    • Order ссылается по customerId (string)

Bounded Context: Catalog
  Агрегат: Product
    • sku, name, price
    • OrderItem ссылается по productId (string)

Bounded Context: Inventory
  Агрегат: StockLevel
    • sku, warehouseId, quantity, reservedQty, version
    • Order → StockLevel: по ID. reserve() / release()

Bounded Context: Billing
  Агрегат: Payment
    • orderId (string), amount, status, gatewayRef
    • Order → Payment: по ID. Event-driven.

Bounded Context: Invoicing
  Агрегат: Invoice
    • orderId (string), lines, total
    • Order → Invoice: по ID. Event-driven (OrderConfirmed → create Invoice)
```

Связи: **по ID + event**. Не по объекту. Не в одной транзакции.

```
PlaceOrder (use case):
  1. inventory.reserve(sku, qty)        ← синхронно, нужен ответ
  2. orderRepo.save(order)              ← синхронно, атомарно
  3. COMMIT
  4. dispatch(OrderPlaced)              ← async
     ├── PaymentListener → stripe.charge()
     ├── InvoiceListener → create Invoice
     └── ShippingListener → create label

Если stripe упал → Order подтверждён, Payment не создан.
→ Retriable. Event в очереди. Retry через 5 секунд.
→ Order не откатился. Деньги не списаны. Состояние согласованное.
```

---

## Вторая типовая ошибка: слишком мелкий агрегат

### Как выглядит

```
Агрегат 1: Order (только поля: id, status, total)
Агрегат 2: OrderItem (отдельно: orderId, productId, qty, price)
Агрегат 3: OrderDiscount (отдельно: orderId, code, amount)
```

Каждый — отдельный `em->persist()`. Отдельная транзакция.

### Почему это плохо

Инвариант `total = sum(items) + discount` **не может быть гарантирован атомарно**:

```php
// t=0ms: save(order)          → INSERT orders (total = 1000)
// t=0ms: save(item_1)        → INSERT order_items (qty=2, price=500)
// t=1ms: save(item_2)        → INSERT order_items (qty=1, price=300)
// t=1ms: save(discount)      → INSERT order_discount (amount=50)
// t=2ms: COMMIT

// Если crash между t=0ms и t=1ms:
// orders.total = 1000
// order_items: только item_1 (total = 1000)
// item_2 нет, discount нет
// → total ≠ sum(items). Несогласованность.
```

Ни один `CHECK constraint` не спасёт, потому что между транзакциями данные **могут** быть несогласованными.

### Правильно

`OrderItem` и `OrderDiscount` — **внутри** агрегата `Order`. Одна транзакция. Один `save()`. Инвариант держится атомарно.

---

## Критерии: как проверить, что границы правильные

| Вопрос | Да → один агрегат | Нет → разные агрегаты |
|---|---|---|
| Инвариант требует атомарности? | Да | Нет (eventual consistency допустимо) |
| Сущность не существует без другой? | Да | Нет (живёт свою жизнь) |
| Изменение одной требует мгновенного изменения другой? | Да | Нет (можно через event) |
| Конкурентные операции на одну сущность? | Мало (1 агрегат пишет) | Много (отдельный агрегат + свой lock) |
| Сущность используется в 5+ use cases независимо? | Нет (всегда через корень) | Да (отдельный агрегат) |
| Изменение сущности не должно трогать другие? | Нет (внутри агрегата) | Да (отдельный агрегат) |

**Золотое правило:** агрегат — это **минимальная** единица, которая гарантирует свои инварианты атомарно. Не больше. Не меньше.

Если вы сомневаетесь — начните с **большего** агрегата и **разделяйте**, когда contention или сложность изменения станут болью. Разделять проще, чем объединять (объединение = migration + backfill + dual-write).

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Что происходит без защиты

Два процесса одновременно пишут в один агрегат. Без явной стратегии защиты — **lost update**. Один из двух результатов теряется молча. Без ошибки. Без лога. Данные просто несогласованные.

### Пример: Inventory (StockLevel)

```
SKU-1, warehouse-A: quantity = 10, reserved = 0

Process A (заказ #101):  Process B (заказ #102):
t=0ms  READ: qty=10, reserved=0   t=0ms  READ: qty=10, reserved=0
t=1ms  WRITE: reserved=1         t=1ms  WRITE: reserved=2
       (10 - 1 = 9 available)       (10 - 2 = 8 available)
t=2ms  COMMIT                   t=2ms  COMMIT
```

Если оба пишут `UPDATE ... SET reserved = reserved + 1` — OK, PostgreSQL атомарно инкрементирует. `reserved = 2`.

Но если оба **читают состояние, вычисляют, пишут результат**:

```php
// Process A:
$stock = $repo->find('SKU-1');       // qty=10, reserved=0
$newReserved = $stock->reserved + 1;  // 1
$stock->reserved = $newReserved;     // 1
$em->flush();                        // UPDATE SET reserved = 1

// Process B (параллельно):
$stock = $repo->find('SKU-1');       // qty=10, reserved=0  ← СТАЛОЕ!
$newReserved = $stock->reserved + 1;  // 1  (не 2!)
$stock->reserved = $newReserved;     // 1
$em->flush();                        // UPDATE SET reserved = 1  ← перезаписал!
```

Результат: `reserved = 1`. Но два заказа резервировали. Должно быть `reserved = 2`. **Lost update.**

Хуже: если `quantity = 10`, а оба процесса резервируют по 10:

```
Process A: 10 - 10 = 0 → OK
Process B: 10 - 10 = 0 → OK
Итого: reserved = 10, но фактически 20.
```

**Перепродажа.**

---

## Что PostgreSQL делает «под капотом»

### MVCC и read committed

По умолчанию PostgreSQL работает в режиме `READ COMMITTED`. Каждая транзакция видит **снимок** данных на момент первого read. Но:

- `UPDATE` всегда читает **последнюю коммитнутую** версию строки (не снимок).
- Два параллельных `UPDATE` на одну строку — **второй ждёт lock первого**.
- После `COMMIT` первого — второй продолжает.

```sql
-- Process A:
BEGIN;
SELECT reserved FROM stock WHERE sku = 'SKU-1';  -- 0
UPDATE stock SET reserved = 1 WHERE sku = 'SKU-1';  -- берёт row lock
-- ... ждёт 100 мс (HTTP к Stripe) ...
COMMIT;  -- lock освобождён

-- Process B (начался в t=0):
BEGIN;
SELECT reserved FROM stock WHERE sku = 'SKU-1';  -- 0 (read committed: видит до A)
UPDATE stock SET reserved = 2 WHERE sku = 'SKU-1';
-- ЖДЁТ lock A. 100 мс.
-- A commit'ил. B продолжает.
-- B пишет reserved = 2.
COMMIT;
```

Здесь `UPDATE ... SET reserved = 2` — **не** lost update. B записал 2. Но B **читал** 0 и вычислил 2 как `0 + 2`. Если бы B резервировал 10, а A тоже 10 — B написал бы `reserved = 10`, а не `reserved = 20`. **Lost update при read-then-write паттерне.**

### Почему `UPDATE SET col = col + 1` безопасно

```sql
UPDATE stock SET reserved = reserved + 1 WHERE sku = 'SKU-1';
```

PostgreSQL:
1. Берёт row lock.
2. Читает **текущую** (последнюю коммитнутую) строку.
3. Вычисляет `reserved + 1` на текущем значении.
4. Пишет.
5. Освобождает lock.

Нет read-then-write. Нет lost update. **Но только если вы не читали строку ранее и не вычисляли значение в PHP.**

---

## Стратегии защиты

### 1. Optimistic Locking — основной подход для агрегатов

**Идея:** не блокируем. Просто проверяем, что данные не изменились, пока мы их редактировали. Если изменились — ретраим.

#### Как работает

Добавляем колонку `version` (integer). Каждый `UPDATE` инкрементирует её. `WHERE version = ?` — если версия изменилась — `0 rows affected`.

```sql
CREATE TABLE stock (
    sku         TEXT PRIMARY KEY,
    warehouse   TEXT NOT NULL,
    quantity    INT NOT NULL DEFAULT 0,
    reserved    INT NOT NULL DEFAULT 0,
    version     INT NOT NULL DEFAULT 0,
    CHECK (reserved >= 0 AND reserved <= quantity),
    CHECK (version >= 0)
);
```

```sql
-- Reserve 5 units:
UPDATE stock
SET reserved = reserved + 5,
    version = version + 1
WHERE sku = 'SKU-1'
  AND warehouse = 'A'
  AND reserved + 5 <= quantity   -- инвариант в WHERE!
  AND version = 3;               -- ожидаемая версия

-- Если 0 rows affected → кто-то изменил строку. RETRY.
-- Если 1 row affected → успех.
```

#### В PHP / Doctrine

```php
// src/Domain/Inventory/StockLevel.php
final class StockLevel
{
    private string $sku;
    private string $warehouse;
    private int    $quantity;
    private int    $reserved;
    private int    $version;

    public function reserve(int $units): void
    {
        if ($units < 1) throw new \App\Domain\Inventory\InvalidUnits($units);
        if ($this->reserved + $units > $this->quantity) {
            throw new \App\Domain\Inventory\InsufficientStock(
                $this->sku, $units, $this->quantity - $this->reserved
            );
        }
        $this->reserved += $units;
    }

    public function release(int $units): void
    {
        if ($this->reserved - $units < 0) {
            throw new \App\Domain\Inventory\OverRelease($this->sku, $units);
        }
        $this->reserved -= $units;
    }

    // Getters
    public function sku(): string       { return $this->sku; }
    public function warehouse(): string { return $this->warehouse; }
    public function quantity(): int     { return $this->quantity; }
    public function reserved(): int     { return $this->reserved; }
    public function available(): int    { return $this->quantity - $this->reserved; }
    public function version(): int      { return $this->version; }
}
```

```php
// src/Infrastructure/Persistence/Doctrine/Entity/StockEntity.php
#[ORM\Entity]
#[ORM\Table(name: 'stock', uniqueColumns: ['sku', 'warehouse'])]
final class StockEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    private string $sku;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $warehouse;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'integer')]
    private int $reserved;

    #[ORM\Column(type: 'integer')]
    private int $version;

    #[ORM\Version]
    private int $version;   // ← Doctrine ORM: автоматический optimistic lock
}
```

**`#[ORM\Version]`** — ключевая аннотация. Что она делает:

1. При `INSERT` — `version = 0`.
2. При `UPDATE` — Doctrine генерирует:
   ```sql
   UPDATE stock
   SET reserved = ?, version = version + 1
   WHERE sku = ? AND warehouse = ? AND version = 3
   ```
3. Если `0 rows affected` — Doctrine бросает `OptimisticLockException`.

#### Retry loop

```php
// src/Infrastructure/Persistence/Doctrine/DoctrineStockRepository.php
final class DoctrineStockRepository implements StockRepositoryInterface
{
    private const MAX_RETRIES = 5;
    private const BASE_DELAY_MS = 10;

    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function reserve(string $sku, string $warehouse, int $units): void
    {
        $lastException = null;

        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            try {
                $this->em->transactional(function () use ($sku, $warehouse, $units) {
                    /** @var StockEntity $stock */
                    $stock = $this->em->getRepository(StockEntity::class)
                        ->findOneBy(['sku' => $sku, 'warehouse' => $warehouse])
                        ?? throw new StockNotFound($sku, $warehouse);

                    $stock->reserved += $units;

                    // #[ORM\Version] автоматически добавит AND version = ?
                    // к UPDATE. Если версия изменилась — 0 rows → OptimisticLockException.
                });

                return; // успех
            } catch (OptimisticLockException $e) {
                $lastException = $e;
                $delay = self::BASE_DELAY_MS * (2 ** $attempt); // exponential backoff
                usleep($delay * 1000);
                // повторная попытка: fresh read, fresh compute, fresh write
            }
        }

        throw new ConcurrencyConflict(
            "Failed to reserve stock after {$this->MAX_RETRIES} attempts: {$sku}",
        ) from $lastException;
    }
}
```

**Почему это работает:**

| Шаг | Что происходит |
|---|---|
| 1. `SELECT` | Читаем текущее состояние. `version = 3`. |
| 2. `reserve(5)` | `reserved += 5`. В памяти. |
| 3. `UPDATE ... WHERE version = 3` | Если никто не изменил — `1 row`. Успех. |
| 4. Если кто-то изменил — `0 rows` → `OptimisticLockException`. |
| 5. Retry: fresh `SELECT`. `version = 4`. `reserved` уже includes чужое изменение. |
| 6. `reserve(5)` на **новом** состоянии. `UPDATE ... WHERE version = 4`. |

**Нет lost update.** Каждый retry работает на актуальных данных.

#### Trade-offs

| | |
|---|---|
| ✅ Нет блокировок. Высокий throughput. | |
| ✅ Простая схема. Одна колонка `version`. | |
| ✅ Doctrine поддерживает из коробки (`#[ORM\Version]`). | |
| ⚠️ При высокой конкуренции — много retry'ов. 100 процессов на одну строку → 50+ retry'ов. |
| ⚠️ Exponential backoff: 10, 20, 40, 80, 160 мс. Если не удалось за 5 попыток — 310 мс потеряно. |
| ⚠️ Не защищает от **логической** гонки: «прочитал quantity=10, решил что хватает, а к моменту UPDATE quantity стало 3». → `CHECK (reserved + units <= quantity)` в `WHERE` — защита. |

**Ограничение:** optimistic locking защищает от **конкурентных записей на одну строку**. Не защищает от **логических** гонек (read-then-decide-then-write с внешним состоянием). Для этого — `CHECK` в `WHERE` или serializable isolation.

---

### 2. Pessimistic Locking — `SELECT FOR UPDATE`

**Идея:** блокируем строку при чтении. Другие процессы **ждут**, пока мы не `COMMIT`.

```sql
BEGIN;
SELECT quantity, reserved, version
FROM stock
WHERE sku = 'SKU-1' AND warehouse = 'A'
FOR UPDATE;          -- ← берёт exclusive row lock

-- Здесь: вычисление, внешние вызовы, что угодно.
-- Другие процессы на этой строке ЖДУТ.

UPDATE stock SET reserved = reserved + 5 WHERE sku = 'SKU-1';
COMMIT;              -- ← lock освобождён. Ожидающие продолжают.
```

#### В PHP

```php
public function reserve(string $sku, string $warehouse, int $units): void
{
    $this->em->transactional(function () use ($sku, $warehouse, $units) {
        $qb = $this->em->createQueryBuilder()
            ->select('s')
            ->from(StockEntity::class, 's')
            ->where('s.sku = :sku')
            ->andWhere('s.warehouse = :wh')
            ->setParameter('sku', $sku)
            ->setParameter('wh', $warehouse)
            ->setLockMode(LockMode::OPTIMISTIC);  // ← pessimistic
        // setLockMode(PessimisticWrite::class) → FOR UPDATE

        /** @var StockEntity|null $stock */
        $stock = $qb->getQuery()->getOneOrNullResult()
            ?? throw new StockNotFound($sku, $warehouse);

        if ($stock->reserved + $units > $stock->quantity) {
            throw new InsufficientStock($sku, $units);
        }

        $stock->reserved += $units;
        // flush → UPDATE. Lock держится до COMMIT.
    });
}
```

#### Trade-offs

| | |
|---|---|
| ✅ Простая семантика: «я держу, другие ждут». | |
| ✅ Нет retry loop. Одна попытка. | |
| ❌ **Blocker.** 100 процессов × 30 сек (HTTP к Stripe) = 3000 сек ожидания. |
| ❌ **Deadlock risk.** Process A lock'ит SKU-1, ждёт SKU-2. Process B lock'ит SKU-2, ждёт SKU-1. Цикл. |
| ❌ **Connection leak.** Lock держится, пока открыта транзакция. Если процесс завис — lock держится до `lock_timeout` или `statement_timeout`. |
| ❌ Throughput падает линейно с числом конкурентов. |

**Когда оправдан:**
- Операция **короткая** (< 100 мс). Нет внешних вызовов.
- Конкурентность **низкая** (1–5 параллельных).
- Семантика «только один может» критична (например, «забрать последний билет»).

**Когда не оправдан:**
- Внешние HTTP-вызовы внутри транзакции.
- Высокая конкуренция (100+ параллельных).
- Есть возможность optimistic + retry.

---

### 3. `WHERE` guard — защита от логической гонки

Даже с optimistic locking возможна логическая гонка:

```
t=0: Process A читает quantity=10, reserved=0. Доступно: 10.
t=0: Process B читает quantity=10, reserved=0. Доступно: 10.
t=1: A резервирует 8. reserved=8. available=2. COMMIT.
t=1: B резервирует 8. reserved=8 (на основе СТАЛОГО чтения). available=2.
     Но фактически reserved должно быть 16. available = -6. ПЕРЕПРОДАЖА.
```

**Защита:** инвариант в `WHERE`:

```sql
UPDATE stock
SET reserved = reserved + 8,
    version = version + 1
WHERE sku = 'SKU-1'
  AND reserved + 8 <= quantity   -- ← инвариант в WHERE
  AND version = 3;

-- Если reserved + 8 > quantity → 0 rows → retry/exception.
-- Даже если version совпала (что маловероятно), инвариант держит.
```

Или в PHP (через Doctrine, если `#[ORM\Version]` не покрывает):

```php
// Вместо:
$stock->reserved += $units;
$em->flush();

// Делаем:
$stmt = $this->em->getConnection()->executeStatement(
    'UPDATE stock
     SET reserved = reserved + :units, version = version + 1
     WHERE sku = :sku AND warehouse = :wh
       AND reserved + :units <= quantity
       AND version = :version',
    ['units' => $units, 'sku' => $sku, 'wh' => $warehouse, 'version' => $version],
);

if ($stmt === 0) {
    throw new ConcurrencyConflict("Stock insufficient or modified: {$sku}");
}
```

**Это hybrid:** optimistic lock (`version`) + invariant guard (`reserved + units <= quantity` в `WHERE`). Оба условия в одном `UPDATE`. Атомарно. Без lost update. Без перепродажи.

---

### 4. Serializable Isolation

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

PostgreSQL **детектирует** write skew и бросает `40001 serialization_failure`. Транзакция откатывается. Вы ретраите.

**Write skew (проблема, которую serializable решает):**

```
Process A:  SELECT reserved FROM stock WHERE sku='A';  -- 0
Process B:  SELECT reserved FROM stock WHERE sku='B';  -- 0
Process A:  UPDATE stock SET reserved=reserved+5 WHERE sku='A';
Process B:  UPDATE stock SET reserved=reserved+5 WHERE sku='B';
Process A:  CHECK: total_reserved < MAX → OK (0+5=5 < 10)
Process B:  CHECK: total_reserved < MAX → OK (0+5=5 < 10)
-- Но: total_reserved = 10. MAX = 8. Нарушение!
-- READ COMMITTED: не детектирует. Оба COMMIT.
-- SERIALIZABLE: детектирует. 40001. ROLLBACK. RETRY.
```

#### Trade-offs

| | |
|---|---|
| ✅ Гарантирует отсутствие write skew. | |
| ❌ **Много aborts.** 100 конкурентных транзакций → 50+ aborts. Throughput падает. |
| ❌ **Deadlock** (cycle detection) → `40P01 deadlock_detected`. |
| ❌ PostgreSQL serializable — **SSI** (Serializable Snapshot Isolation), не true serializable. Есть edge cases. |
| ❌ Slow: SSI добавляет overhead на каждый write (dependency tracking). |

**Когда оправдан:** финансовый учёт, где write skew = потеря денег. Низкая конкуренция (< 10 TPS). Готовность к retry.

**Когда не оправдан:** inventory, где optimistic + `WHERE` guard достаточно. Высокая конкуренция.

---

### 5. Idempotency — защита от дублей

Отдельная проблема: **один и тот же** запрос приходит дважды (retry клиента, duplicate webhook).

```
Клиент: POST /order  (заказ #101, SKU-1 × 5)
Сервер: reserve(5) → OK. order saved. 200.
Клиент: (не получил ответ, retry) POST /order (заказ #101, SKU-1 × 5)
Сервер: reserve(5) → OK (ещё 5!). order saved. 200.
Итого: reserved = 10. Но заказ один.
```

**Защита:**

```sql
CREATE TABLE idempotency_keys (
    key         TEXT PRIMARY KEY,
    order_id    UUID NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- В транзакции:
BEGIN;
INSERT INTO idempotency_keys (key, order_id)
VALUES ('client-req-id-123', 'order-uuid-42')
ON CONFLICT (key) DO NOTHING
RETURNING order_id;

-- Если 0 rows → уже обработан. Вернуть сохранённый order_id.
-- Если 1 row → первый раз. Продолжить.
COMMIT;
```

В PHP:

```php
public function handle(PlaceOrderCommand $cmd): OrderId
{
    $idemKey = $cmd->idempotencyKey ?? bin2hex(random_bytes(16));

    // 1. Проверить идемпотентность (вне основной транзакции)
    $existing = $this->idemRepo->find($idemKey);
    if ($existing !== null) {
        return $existing->orderId;  // уже обработан
    }

    // 2. Основной use case (с optimistic lock на stock)
    $orderId = $this->placeOrder($cmd);

    // 3. Зафиксировать идемпотентность (в той же транзакции)
    $this->idemRepo->save(new IdempotencyRecord($idemKey, $orderId));

    return $orderId;
}
```

**Принцип:** `idempotency_key` + `UNIQUE` = атомарная защита от дублей. Даже при race condition двух одинаковых запросов — `UNIQUE` не даст вставить дважды.

---

## Deadlock — как возникает и как избежать

### Классический deadlock

```
Process A:                    Process B:
BEGIN;                        BEGIN;
UPDATE stock sku='A'           UPDATE stock sku='B'
  (lock sku='A')                (lock sku='B')
UPDATE stock sku='B'           UPDATE stock sku='A'
  (ждёт lock sku='B')           (ждёт lock sku='A')
  ← DEADLOCK →                ← DEADLOCK →
PostgreSQL: 40P01 deadlock_detected
Process A: ROLLBACK. Process B продолжает.
```

**Условие:** два процесса lock'ят строки в **разном порядке**.

### Как избежать

**1. Единый порядок lock'ов.**

```php
// Всегда: сначала sku, потом warehouse. Никогда наоборот.
$keys = [[$sku, $warehouse], [$sku2, $warehouse2]];
sort($keys);  // сортировка → единый порядок

foreach ($keys as [$s, $w]) {
    $this->em->getConnection()->executeStatement(
        'SELECT 1 FROM stock WHERE sku = ? AND warehouse = ? FOR UPDATE',
        [$s, $w]
    );
}
```

**2. `lock_timeout`.**

```sql
SET lock_timeout = '5s';
-- Если не удалось получить lock за 5 секунд → error, не deadlock.
```

**3. Минимальная транзакция.**

```php
// Плохо: lock держится 30 секунд (HTTP внутри транзакции)
$this->em->transactional(function () {
    $stock = $this->lockStock('SKU-1');
    $stripe->charge(...);  // 5–30 секунд
    $this->em->flush();
});

// Хорошо: lock держится < 100 мс
$this->em->transactional(function () {
    $stock = $this->lockStock('SKU-1');
    $this->em->flush();  // COMMIT. Lock освобождён.
});
$stripe->charge(...);  // вне транзакции
```

**4. Optimistic вместо pessimistic.**

Нет lock'ов → нет deadlock. Есть retry. При умеренной конкуренции (10–50 параллельных) retry быстрее, чем deadlock detection + rollback + retry.

---

## Проектирование: где что применять

| Сценарий | Механизм | Почему |
|---|---|---|
| Inventory reserve (высокая конкуренция) | Optimistic + `WHERE` guard | Нет блокировок. Инвариант в `WHERE`. Retry при конфликте. |
| Payment capture (низкая конкуренция, критичность) | Pessimistic (`FOR UPDATE`) | Простая семантика. Одна попытка. Нет retry. |
| Financial ledger (write skew критичен) | Serializable + idempotency | SSI детектирует write skew. Idempotency защищает от дублей. |
| Order creation (средняя конкуренция) | Optimistic на Order + idempotency key | Version на Order. Idempotency на команду. |
| Counter increment (очень высокая конкуренция) | `UPDATE SET count = count + 1` (атомарный) | Нет read-then-write. PostgreSQL атомарно инкрементирует. |
| «Забрать последний билет» | Pessimistic + `WHERE` guard | Одна попытка. Нет retry. Критично «только один». |

### Универсальный паттерн для агрегатов

```php
// 1. Домен: мутация + инвариант
$stock->reserve($units);  // бросает InsufficientStock

// 2. Persistence: optimistic lock + WHERE guard
$em->transactional(function () {
    $stock = $repo->find($sku, $warehouse);  // SELECT (без FOR UPDATE)
    $stock->reserve($units);                  // доменная валидация
    $em->flush();                             // UPDATE ... WHERE version = ?
    // + WHERE reserved + units <= quantity (в SQL)
});

// 3. Retry loop (вне домена)
catch (OptimisticLockException) → retry (max 5, exponential backoff)

// 4. Idempotency (для idempotent операций)
UNIQUE (idempotency_key) → ON CONFLICT DO NOTHING → вернуть existing
```

---

## Антипаттерны

### 1. `sleep(100)` между read и write

```php
$stock = $repo->find($sku);
usleep(100_000);  // «ну, чтобы конкуренты не мешали»
$stock->reserve($units);
$em->flush();
```

Не работает. `sleep` не lock'ит. Конкуренты читают то же самое. Lost update.

### 2. `SELECT FOR UPDATE` + HTTP внутри транзакции

```php
$this->em->transactional(function () {
    $stock = $repo->lock($sku);       // FOR UPDATE
    $stripe->charge(...);             // 5–30 секунд с lock!
    $this->em->flush();
});
```

Lock держится 30 секунд. 100 конкурентов × 30 сек = очередь. Throughput → 0.

### 3. Optimistic lock без retry

```php
$stock->reserve($units);
$em->flush();
// OptimisticLockException → 500. Пользователь видит ошибку.
// Но: данные не потеряны. Просто не удалось.
// Без retry — «ошибка» на каждый второй конфликт.
```

### 4. `version` в доменном коде

```php
// АНТИПАТТЕРН: домен знает о version
final class StockLevel
{
    public function reserve(int $units): void
    {
        $this->reserved += $units;
        $this->version++;  // ← домен не должен знать о version!
    }
}
```

`version` — **инфраструктурная** деталь. Домен не знает, как хранятся данные. `#[ORM\Version]` — на сущности, не в доменном VO.

### 5. Serializable isolation «на всякий случай»

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
-- Для простого UPDATE SET reserved = reserved + 1
```

Overhead на SSI (dependency tracking) не нужен. `UPDATE SET col = col + 1` атомарен в любом режиме. Serializable — для write skew, не для simple increment.

---

## Итого

```
Конкуренция на агрегат
    │
    ├── Низкая (< 5 параллельных), операция короткая
    │   → Pessimistic (FOR UPDATE) + короткая транзакция
    │
    ├── Средняя (5–100), операция короткая
    │   → Optimistic (version) + retry + WHERE guard
    │
    ├── Высокая (100+), операция короткая
    │   → Атомарный UPDATE (SET col = col + N) + WHERE guard
    │     (нет read-then-write, нет version, нет retry)
    │
    ├── Write skew критичен (финансы)
    │   → Serializable + idempotency + retry
    │
    └── Дубли запросов (retry клиента)
        → Idempotency key + UNIQUE
```

**Главный принцип:** агрегат — граница атомарности. Конкурентный доступ — это **один** агрегат, который **два** процесса пытаются изменить. Защита — на уровне **записи** (optimistic/pessimistic), не на уровне **чтения**. Чтение может быть устаревшим. Запись должна быть атомарной.

> ⏱ 50.6s · prompt 78246 tok · output 5048 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в DDD/Symfony: когда публиковать, как обрабатывать, проблемы, связь с транзакциями. Это продолжение серии вопросов о DDD, PHP, Symfony, Doctrine.

Нужно дать структурированный, практичный ответ. Я уже обсуждал:
- Ошибки, исключения
- SOLID (OCP, SRP)
- DI-контейнер Symfony
- Access control
- Value Object в Doctrine
- Агрегаты
- Конкурентность (optimistic/pessimistic locking)
- N+1
- PostgreSQL performance

Теперь — доменные события. Это естественное продолжение: события — механизм связи между агрегатами, который мы уже упоминали (OrderPlaced → InvoiceListener, UserRegistered → SendWelcomeEmail).

Структура ответа:
1. Что такое доменное событие (и что это НЕ)
2. Когда публикуем (правила)
3. Как обрабатываем (паттерны)
4. Связь с транзакциями (критичный вопрос)
5. Проблемы, которые решает
6. Проблемы, которые создаёт
7. Антипаттерны
8. Практические рекомендации

Связь с транзакциями — это самый сложный и важный аспект. Нужно чётко разделить:
- Outbox pattern (событие в БД в той же транзакции)
- In-memory events (symfony event dispatcher)
- Message bus (symfony messenger)
- Transactional outbox vs "publish then commit" race condition

Давайте структурирую:

## 1. Что такое доменное событие
- Факт, который произошёл. Не команда, не запрос.
- Прошедшее время: OrderPlaced, Not OrderPlacing.
- Неизменяемое (readonly).
- Не содержит инструкций ("отправь письмо"). Только факт ("заказ размещён").
- Не знает, кто слушает.

## 2. Когда публикуем
- Изменение состояния агрегата, которое имеет значение для других.
- Не для внутренних деталей (создание OrderItem — не событие, часть Order).
- Не для каждой мутации (addItem — нет, confirm — да).
- Правило: "если это случилось, и кто-то другой может об этом хотеть знать" → событие.

## 3. Как обрабатываем
- In-process: Symfony EventDispatcher (sync, в той же транзакции)
- Out-of-process: Symfony Messenger (async, через транспорт)
- Outbox pattern: событие в БД + polling/publisher

## 4. Транзакции — критичный вопрос
Проблема: "publish then commit" race condition:
1. BEGIN
2. INSERT order
3. PUBLISH event (ушло в очередь/Redis)
4. CRASH (до COMMIT)
5. Order не сохранён, но событие ушло
6. Listener обработал событие → несуществующий заказ

Решения:
- Outbox pattern: событие в той же транзакции в БД. Отдельный процесс публикует.
- In-memory event: событие в памяти, публикуется после COMMIT.
- Transactional outbox + relay.

## 5. Проблемы, которые решает
- Связь агрегатов без прямых зависимостей
- Async processing
- Расширяемость (новый listener без изменения кода)
- Аудит
- CQRS (read model update)

## 6. Проблемы, которые создаёт
- Ordering (если важен)
- Idempotency (duplicates)
- Debugging (причина → следствие не в одном месте)
- "Тайные" зависимости (listener может быть скрытым потребителем)
- Complexity (инфраструктура: очереди, workers, DLQ)

## 7. Антипаттерны
- Событие как команда ("отправь письмо" в имени)
- Слишком много событий (на каждую мутацию)
- Событие с инструкциями (payload содержит "что делать")
- In-memory event для async (потеря при crash)
- Event-driven всё подряд (overkill)

## 8. Практика
- Доменное событие (fact) → Application event (конкретное действие)
- Outbox для production
- Idempotency в handlers
- Ordering только когда нужен

Давайте напишу развёрнутый ответ с кодом.

</details>

**Кандидат:**

## Что такое доменное событие

Доменное событие — **неизменяемый факт**, что произошло. Не инструкция. Не команда. Не запрос.

```php
// Доменное событие: факт
final readonly class OrderPlaced
{
    public function __construct(
        public readonly OrderId  $orderId,
        public readonly int      $totalCents,
        public readonly string   $customerId,
        public readonly \DateTimeImmutable $placedAt,
    ) {}
}

// НЕ доменное событие: команда / инструкция
// final class SendWelcomeEmail { ... }     ← это command
// final class CreateInvoice { ... }        ← это command
// final class NotifyWarehouse { ... }      ← это command
```

Пять свойств:

| Свойство | Смысл |
|---|---|
| **Прошедшее время** | `OrderPlaced`, не `OrderPlacing`. Событие — уже случилось. |
| **Неизменяемое** | `readonly`. Не можно «отменить» событие. Можно опубликовать `OrderCancelled`. |
| **Факт, не инструкция** | Не «отправь письмо». А «заказ размещён». Что делать — дело слушателя. |
| **Не знает слушателей** | `OrderPlaced` не содержит `email`, `template`, `channel`. Только данные факта. |
| **Принадлежит агрегату** | Событие порождено конкретным экземпляром агрегата. `Order#42 placed`. Не «какой-то заказ». |

---

## Когда публикуем

### Правило

> Публикуем событие, если **состояние агрегата изменилось** и **это изменение имеет значение для других участников**, которые **не обязаны** знать об этом в момент изменения.

### Три вопроса

**1. Изменилось ли состояние агрегата?**

```php
$order->addItem($item);     // ← внутренняя мутация. Не событие.
$order->addItem($item2);    // ← внутренняя мутация. Не событие.
$order->confirm();          // ← СМЕНА СОСТОЯНИЯ. pending → confirmed. Событие.
$order->cancel();           // ← СМЕНА СОСТОЯНИЯ. Событие.
```

Не на каждый `addItem`. На каждый **переход состояния**, который имеет значение для других.

**2. Есть ли другие участники, которым это нужно знать?**

```php
$order->setInternalNote("позвонить клиенту");
// ← нет. Это внутренняя заметка. Никому не нужно. Не событие.

$order->confirm();
// ← да. Инвойс, склад, аналитика, уведомления. Событие.
```

**3. Можно ли отложить реакцию?**

```php
// Если ответ должен быть синхронным (в том же request) → не событие.
// Если ответ может быть асинхронным → событие.

// Событие:
OrderPlaced → InvoiceListener (через 5 сек)
OrderPlaced → StockListener (через 1 сек)
OrderPlaced → AnalyticsListener (через 1 мин)

// НЕ событие (синхронная зависимость):
PlaceOrder → StockService::reserve() → если нет → отказ.
// Здесь нужно решение ДО сохранения заказа. Не «после».
```

### Что **не** публикуем как событие

| Ситуация | Почему |
|---|---|
| Внутренняя мутация агрегата (`addItem`) | Не меняет состояние. Не имеет значения для других. |
| Результат проверки (`isEligible`) | Это query, не факт. |
| Ошибка валидации | Это исключение, не событие. |
| «Заказ создан» (draft) | Draft — не факт. Факт — `OrderConfirmed`. |
| Каждая смена поля | `setCustomer()`, `setTotal()` — внутреннее. |

---

## Как обрабатываем: три уровня

### Уровень 1: In-memory (Symfony EventDispatcher)

Событие публикуется **в процессе**, **в той же транзакции**, **синхронно**.

```php
// Domain: Order порождает событие
final class Order
{
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    public function confirm(): void
    {
        $this->status = 'confirmed';
        $this->confirmedAt = new \DateTimeImmutable();

        // Событие внутри агрегата. Не публикуем. Только фиксируем.
        $this->domainEvents[] = new OrderConfirmed(
            orderId:     $this->id,
            totalCents:  $this->totalCents,
            confirmedAt: $this->confirmedAt,
        );
    }

    /** @return list<DomainEvent> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

```php
// Application: публикуем после сохранения
final class ConfirmOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface   $em,
    ) {}

    public function handle(ConfirmOrderCommand $cmd): void
    {
        $order = $this->orders->findById($cmd->orderId)
            ?? throw new OrderNotFound($cmd->orderId->value);

        $order->confirm();  // доменная мутация

        $this->orders->save($order);

        $this->em->flush();  // COMMIT

        // Публикуем ПОСЛЕ commit.
        // Если crash между flush и dispatch → событие потеряно.
        // Но: заказ сохранён. Слушатели не обработали.
        // Это допустимо? Зависит от сценария.
        // Для Invoice — да (можно пересоздать).
        // Для Stock release — нет (нужен outbox).

        foreach ($order->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
```

```php
// Listener (in-process, sync)
final class InvoiceListener
{
    public function __construct(
        private InvoiceService $invoices,
    ) {}

    #[AsEventListener]
    public function onOrderConfirmed(OrderConfirmed $event): void
    {
        $this->invoices->createForOrder($event->orderId);
        // Синхронно. В той же транзакции? НЕТ.
        // EventDispatcher не обёрнут в транзакцию.
        // Если InvoiceService бросит → Order уже сохранён.
    }
}
```

**Характеристики:**

| | |
|---|---|
| Синхронно? | Да. Listener выполняется до возврата из `dispatch()`. |
| В той же транзакции? | **Нет.** `flush()` уже вызван. `dispatch()` — после. |
| Если listener упадёт? | Order сохранён. Listener не обработан. **Несоответствие.** |
| Масштабируется? | Нет. Всё в одном процессе. |
| Когда оправдан? | Низкая нагрузка. Listener'ы быстрые. Потеря допустима. |

### Уровень 2: Message Bus (Symfony Messenger + транспорт)

Событие → сообщение → транспорт (Redis/AMQP/Postgres) → worker.

```php
// Application: публикуем в message bus
final class ConfirmOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private MessageBusInterface      $bus,   // ← Messenger, не EventDispatcher
        private EntityManagerInterface   $em,
    ) {}

    public function handle(ConfirmOrderCommand $cmd): void
    {
        $order = $this->orders->findById($cmd->orderId)
            ?? throw new OrderNotFound($cmd->orderId->value);

        $order->confirm();
        $this->orders->save($order);

        $this->em->flush();  // COMMIT

        foreach ($order->pullDomainEvents() as $event) {
            if ($event instanceof OrderConfirmed) {
                // Конкретное прикладное сообщение, не доменное событие
                $this->bus->dispatch(new CreateInvoice($event->orderId));
                $this->bus->dispatch(new ReleaseStock($event->orderId));
                $this->bus->dispatch(new TrackOrder($event->orderId));
            }
        }
    }
}
```

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\CreateInvoice':
                transport: 'amqp://invoice'
            'App\Message\ReleaseStock':
                transport: 'amqp://stock'
            'App\Message\TrackOrder':
                transport: 'amqp://analytics'
```

```php
// Worker (отдельный процесс)
#[AsMessageHandler]
final class CreateInvoiceHandler
{
    public function __construct(
        private InvoiceService $invoices,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(CreateInvoice $msg): void
    {
        $this->invoices->createForOrder($msg->orderId);
        // При ошибке → Messenger retry (exponential backoff)
        // После max_retries → dead-letter queue
    }
}
```

**Характеристики:**

| | |
|---|---|
| Синхронно? | Нет. `dispatch()` кладёт в транспорт. Worker обработает позже. |
| В той же транзакции? | **Нет.** Транзакция уже commit'нута. |
| Если worker упадёт? | Retry. DLQ. Ничего не потеряно. |
| Масштабируется? | Да. Горизонтально. |
| Ordering? | Нет гарантии (если разные очереди). |
| Когда оправдан? | Production. Async. Разные скорости. |

### Уровень 3: Transactional Outbox (наиболее надёжный)

**Проблема уровней 1 и 2:**

```
t=0ms  BEGIN
t=1ms  INSERT INTO orders ...
t=2ms  PUBLISH event → Redis/AMQP (ушло!)
t=3ms  CRASH (до COMMIT)
t=3ms  ROLLBACK (автоматический)

Результат:
  - Order НЕ сохранён (rollback).
  - Событие УШЛО (в Redis/AMQP).
  - Worker обработал событие → создал Invoice для несуществующего заказа.
  - Несогласованность.
```

**Решение:** событие хранится **в БД в той же транзакции**. Отдельный процесс (outbox relay) публикует.

```sql
-- Таблица outbox
CREATE TABLE outbox (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    aggregate_id UUID NOT NULL,
    event_type  TEXT NOT NULL,       -- 'OrderConfirmed'
    payload     JSONB NOT NULL,      -- { "orderId": "...", "totalCents": 5000 }
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    published_at TIMESTAMPTZ        -- NULL = ещё не опубликовано
);
```

```php
// Application: сохраняем событие в outbox В ТОЙ ЖЕ транзакции
final class ConfirmOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private EntityManagerInterface   $em,
    ) {}

    public function handle(ConfirmOrderCommand $cmd): void
    {
        $order = $this->orders->findById($cmd->orderId)
            ?? throw new OrderNotFound($cmd->orderId->value);

        $order->confirm();

        $this->em->transactional(function () use ($order, $cmd) {
            $this->orders->save($order);

            // Событие в outbox. В той же транзакции.
            foreach ($order->pullDomainEvents() as $event) {
                $this->em->persist(new OutboxEntity(
                    aggregateId: (string) $order->id->value,
                    eventType:   $event::class,
                    payload:     json_encode($this->toPayload($event)),
                ));
            }

            $this->em->flush();  // COMMIT. Order + Outbox атомарно.
        });
        // Если crash между INSERT и COMMIT → оба откатились. Событие НЕ ушло.
    }

    private function toPayload(object $event): array
    {
        return match (true) {
            $event instanceof OrderConfirmed => [
                'orderId'     => $event->orderId->value,
                'totalCents'  => $event->totalCents,
                'confirmedAt' => $event->confirmedAt->format('c'),
            ],
            default => get_object_vars($event),
        };
    }
}
```

```php
// Outbox Relay (отдельный процесс, cron или long-running)
final class OutboxRelay
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus,
        private LoggerInterface       $logger,
    ) {}

    public function run(): void
    {
        while (true) {
            $this->processBatch();
            usleep(500_000);  // 500ms polling interval
        }
    }

    private function processBatch(): void
    {
        $batch = $this->em->createQueryBuilder()
            ->select('o')
            ->from(OutboxEntity::class, 'o')
            ->where('o.publishedAt IS NULL')
            ->orderBy('o.createdAt', 'ASC')   // ordering!
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();

        foreach ($batch as $entry) {
            try {
                $message = $this->toMessage($entry);
                $this->bus->dispatch($message);  // в транспорт (AMQP/Redis)

                // Только после успешного dispatch:
                $entry->publishedAt = new \DateTimeImmutable();
                $this->em->persist($entry);
                $this->em->flush();
            } catch (\Throwable $e) {
                $this->logger->error('Outbox publish failed', [
                    'id'    => $entry->id,
                    'error' => $e->getMessage(),
                ]);
                // Не publishedAt. Следующий pick-up повторит.
                // Но: если transport недоступен — все batch'и встанут.
                // Решение: backoff, circuit breaker.
                break;
            }
        }
    }

    private function toMessage(OutboxEntity $entry): object
    {
        $payload = json_decode($entry->payload, true);

        return match ($entry->eventType) {
            'App\Domain\Order\OrderConfirmed' => new CreateInvoice(
                orderId: OrderId::from($payload['orderId']),
            ),
            default => throw new UnknownEventType($entry->eventType),
        };
    }
}
```

**Характеристики:**

| | |
|---|---|
| Атомарность? | **Да.** Order + Outbox в одной транзакции. Crash → оба откатились. |
| Потеря событий? | Нет. Пока `published_at IS NULL` — в БД. |
| Duplicates? | Возможны (relay crash между dispatch и mark published). → Idempotency в handler. |
| Ordering? | Да. `ORDER BY created_at ASC` + `MAX 100` batch. |
| Сложность? | Высокая. Отдельный процесс. Monitoring. DLQ. |
| Когда оправдан? | Production. Критичные события. Financial. |

---

## Связь с транзакциями: детальная картина

### Три модели

```
МОДЕЛЬ 1: In-memory (EventDispatcher)
─────────────────────────────────────
BEGIN
  INSERT order
  dispatch(OrderPlaced)
    → InvoiceListener → INSERT invoice
    → StockListener   → UPDATE stock
COMMIT
─────────────────────────────────────
Всё в одной транзакции. Атомарно.
Но: синхронно. Медленный listener → медленный request.
Но: не масштабируется. Один процесс.

МОДЕЛЬ 2: Message Bus (Messenger + AMQP)
─────────────────────────────────────────
BEGIN
  INSERT order
COMMIT
dispatch(OrderPlaced) → AMQP → Worker A (invoice)
                              → Worker B (stock)
─────────────────────────────────────────
Атомарность: order + commit. Событие — после.
Риск: crash между commit и dispatch → потеря.
Worker: retry, DLQ. Асинхронно. Масштабируется.

МОДЕЛЬ 3: Outbox
─────────────────
BEGIN
  INSERT order
  INSERT outbox (event)
COMMIT
─────────────────────────────────────────
Relay (отдельный процесс):
  SELECT outbox WHERE published_at IS NULL
  → dispatch → AMQP → Worker
  → UPDATE outbox SET published_at = NOW()
─────────────────────────────────────────
Атомарность: order + outbox. Crash → оба откатились.
Safest. Но: отдельный процесс. Polling. Complexity.
```

### Race condition: «publish then commit»

```
t=0ms  BEGIN
t=1ms  INSERT INTO orders VALUES (42, 'confirmed', 5000)
t=2ms  PUBLISH → AMQP (message в очереди)
t=3ms  CRASH (OOM, segfault, kill -9)
t=3ms  PostgreSQL: transaction не commit'нута → ROLLBACK

Состояние:
  orders:    нет строки 42 (rollback)
  AMQP:      message "OrderConfirmed #42" В ОЧЕРЕДИ
  Worker:    обработал → INSERT invoice для заказа 42 → несуществующий!
```

**Это критический баг.** Инвойс для несуществующего заказа. Склад зарезервирован. Уведомление отправлено. Всё для призрака.

**Решение:** Outbox. Событие в БД. Не в AMQP. Crash → ROLLBACK → событие тоже откатилось. AMQP не знает.

### Race condition: «commit then publish»

```
t=0ms  BEGIN
t=1ms  INSERT INTO orders VALUES (42, 'confirmed', 5000)
t=2ms  COMMIT
t=3ms  PUBLISH → AMQP
t=4ms  CRASH

Состояние:
  orders:    строка 42 СУЩЕСТВУЕТ (commit)
  AMQP:      нет message
  Worker:    не обработал

Результат: заказ сохранён, но invoice не создан. Stock не зарезервирован.
```

**Менее критично**, но тоже несогласованность. Инвойс можно пересоздать (idempotent). Stock — retry.

**Решение:** Outbox + relay. Событие в БД. Relay публикует. Если relay упал — событие в БД. Подхватит.

### Transactional outbox: полный цикл

```
┌─────────────────────────────────────────────────────────────────┐
│  REQUEST                                                        │
│                                                                │
│  BEGIN                                                         │
│    INSERT orders (id=42, status='confirmed', total=5000)       │
│    INSERT outbox (event_type='OrderConfirmed', payload={...})  │
│  COMMIT                                                        │
│                                                                │
│  ← 200 OK                                                     │
└─────────────────────────────────────────────────────────────────┘
        │
        ▼  (асинхронно, отдельный процесс)
┌─────────────────────────────────────────────────────────────────┐
│  OUTBOX RELAY (polling every 500ms)                             │
│                                                                │
│  SELECT * FROM outbox WHERE published_at IS NULL               │
│  ORDER BY created_at ASC                                       │
│  LIMIT 100                                                     │
│                                                                │
│  For each entry:                                               │
│    1. Dispatch to AMQP (message bus)                           │
│    2. UPDATE outbox SET published_at = NOW() WHERE id = ?      │
│    3. COMMIT                                                   │
│                                                                │
│  Если crash между 1 и 3:                                       │
│    - AMQP: message в очереди                                   │
│    - Outbox: published_at IS NULL                              │
│    - Следующий poll: dispatch ещё раз → DUPLICATE              │
│    - Worker: idempotency → skip                                │
└─────────────────────────────────────────────────────────────────┘
        │
        ▼
┌─────────────────────────────────────────────────────────────────┐
│  WORKERS (AMQP consumers)                                       │
│                                                                │
│  Worker A: CreateInvoiceHandler                                 │
│    - Идempotency: SELECT 1 FROM invoices WHERE order_id = ?    │
│    - Если есть → skip                                           │
│    - Если нет → INSERT invoice                                  │
│                                                                │
│  Worker B: ReleaseStockHandler                                  │
│    - Idempotency: UPDATE stock SET reserved = reserved - N      │
│      WHERE sku = ? AND version = ?                              │
│    - Если 0 rows → skip (уже released)                          │
│                                                                │
│  Worker C: NotifyCustomerHandler                                │
│    - Idempotency: INSERT INTO email_log (UNIQUE order_id, type) │
│    - ON CONFLICT → skip                                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Проблемы, которые решает

| Проблема | Без событий | С событиями |
|---|---|---|
| Связь агрегатов | Прямой вызов: `orderService.createInvoice()`. Циклические зависимости. | Event → listener. Нет прямой зависимости. |
| Async processing | Синхронный вызов в request. Медленный listener → медленный ответ. | Worker в фоне. Ответ мгновенный. |
| Масштабируемость | Один процесс. | Горизонтальное масштабирование workers. |
| Расширяемость | Новый consumer = правка существующего кода. | Новый listener = новый файл. NОль строк изменено. |
| Аудит | Нет. | Outbox — журнал всех событий. |
| CQRS | Write-модель знает о read-модели. | Event → read model update. Разделение. |
| Fault isolation | Падение invoice-service → падает order-service. | Invoice-service упал → order сохранён. Retry. |

---

## Проблемы, которые создаёт

### 1. Потеря атомарности между агрегатами

```
Без событий:
  Order + Invoice в одной транзакции. Атомарно.
  Crash → оба откатились. Согласованно.

С событиями:
  Order committed. Event → Worker → Invoice.
  Worker упал. Invoice не создан.
  Order существует. Invoice — нет.
  → НЕСОГЛАСОВАННО. Но: retry создаст.
```

**Trade-off:** теряем strong consistency между агрегатами. Получаем eventual consistency. Для большинства сценариев — приемлемо. Для финансовых операций — нет.

### 2. Ordering

```
OrderConfirmed → CreateInvoice
OrderConfirmed → ReleaseStock

Если Worker A (invoice) медленный, Worker B (stock) быстрый:
  Stock released. Invoice не создан.
  Клиент видит: «заказ подтверждён, инвентарь зарезервирован».
  Но: invoice не создан.

Если ordering критичен:
  → Одна очередь. Один consumer. Sequential processing.
  → Или: dependency chain (invoice → THEN stock).
```

Большинство событий **не требуют ordering**. Но если требуют — это ограничение.

### 3. Idempotency

Duplicate delivery — неизбежна в distributed systems. AMQP, Redis, network retry — все могут доставить дважды.

**Решение:** каждый handler идемпотентен.

```php
// Инвайдент: не создать второй инвойс
public function __invoke(CreateInvoice $msg): void
{
    $exists = $this->em->createQueryBuilder()
        ->select('COUNT(i.id)')
        ->from(InvoiceEntity::class, 'i')
        ->where('i.orderId = :id')
        ->setParameter('id', $msg->orderId->value)
        ->getQuery()
        ->getSingleScalarResult();

    if ($exists > 0) {
        return;  // уже создан
    }

    // создать...
}
```

### 4. Debugging

```
Без событий:
  Request → OrderService → InvoiceService → Done.
  Stack trace: 3 фрейма. Видно всё.

С событиями:
  Request → OrderService → dispatch → [AMQP] → Worker → InvoiceService
  Stack trace: 3 фрейма. Но: где AMQP? Где Worker? Где retry?
  Log: в 4 разных местах. 4 разных процесса. 4 разных временных метки.
```

**Решение:** Correlation ID. `X-Request-Id` в заголовке → в payload события → в log worker'а. Один ID на весь цепочку.

### 5. Скрытые зависимости

```php
// OrderController не знает, что OrderConfirmed → CreateInvoice.
// InvoiceListener — отдельный файл. Отдельный сервис.
// Если удалить OrderListener → invoice не создаётся. Тихо. Без ошибки.
```

**Решение:** integration tests. «OrderConfirmed → Invoice существует». Если listener удалён → тест падает.

### 6. Overhead инфраструктуры

| Компонент | Зачем |
|---|---|
| AMQP broker (RabbitMQ) | Транспорт |
| Redis (если transport) | Транспорт |
| Outbox table | Надёжность |
| Relay process | Публикация |
| Worker processes | Обработка |
| DLQ table/queue | Ошибки |
| Monitoring (Grafana, Prometheus) | Наблюдаемость |
| Alerting (Alertmanager) | Алерты |

Для «отправить email после регистрации» — это 8 компонентов. Overkill? Зависит от нагрузки и требований.

---

## Антипаттерны

### 1. Событие как команда

```php
// АНТИПАТТЕРН:
final class SendWelcomeEmail
{
    public function __construct(
        public string $to,
        public string $template,
        public string $channel,  // ← инструкция, не факт
    ) {}
}

// ХОРОШО:
final class UserRegistered
{
    public function __construct(
        public string $userId,
        public string $email,
        public \DateTimeImmutable $registeredAt,
    ) {}
}
// Listener сам решает: отправить email, SMS, push.
```

### 2. Событие на каждую мутацию

```php
// АНТИПАТТЕРН:
$order->setTotal(5000);      → TotalChanged
$order->setCustomer('id');   → CustomerChanged
$order->addItem($item);      → ItemAdded
$order->removeItem($item);   → ItemRemoved
$order->setStatus('pending');→ StatusChanged
// 5 событий на одну операцию. Шум.

// ХОРОШО:
$order->confirm();           → OrderConfirmed (1 событие)
```

### 3. In-memory event для critical path

```php
// АНТИПАТТЕРН:
// "Отправим email после регистрации" через EventDispatcher (in-memory)
// Если процесс crash'ит после commit, но до dispatch → email не уйдёт. Никогда.
// Нет retry. Нет DLQ. Нет monitoring.

// ХОРОШО:
// Outbox + Messenger + Worker + retry + DLQ.
```

### 4. Event-driven для синхронной зависимости

```php
// АНТИПАТТЕРН:
// "Заказ нельзя подтвердить, если нет инвентаря"
// → Публикуем OrderConfirmed → StockListener проверяет → откатывает
// → Но: OrderConfirmed уже опубликован. Invoice уже создан.
// → Несоответствие.

// ХОРОШО:
// StockService::reserve() — синхронный вызов ДО сохранения.
// Если нет → exception. Order не сохранён. Чисто.
```

### 5. Listener ходит в домен другого агрегата

```php
// АНТИПАТТЕРН:
#[AsEventListener]
function onOrderPlaced(OrderPlaced $event) {
    $order = $this->orderRepo->find($event->orderId);
    $order->items[0].quantity = 10;  // ← мутация чужого агрегата!
    $this->orderRepo->save($order);
}

// ХОРОШО:
// Listener создаёт СОБСТВЕННУЮ сущность (Invoice, StockReservation).
// Не трогает чужой агрегат.
```

---

## Практические рекомендации

### 1. Доменное событие ≠ Прикладное сообщение

```
Доменное событие:    OrderConfirmed (факт, readonly, в домене)
Прикладное сообщение: CreateInvoice (инструкция, в application)

OrderConfirmed ──→ CreateInvoice
                ──→ ReleaseStock
                ──→ TrackOrder

Одно доменное событие → N прикладных сообщений.
Домен не знает о прикладных.
```

### 2. Payload — только данные, не инструкции

```php
// ПЛОХО:
final class OrderConfirmed {
    public string $orderId;
    public string $action;  // ← "create_invoice" — инструкция!
}

// ХОРОШО:
final class OrderConfirmed {
    public string $orderId;
    public int $totalCents;
    public \DateTimeImmutable $confirmedAt;
}
// Что делать с этим фактом — решает listener.
```

### 3. Idempotency в каждом handler

```php
// Паттерн: UNIQUE key + ON CONFLICT
INSERT INTO invoices (order_id, ...) VALUES (...)
ON CONFLICT (order_id) DO NOTHING;
// → duplicate delivery = no-op.
```

### 4. Outbox для production. In-memory для dev.

```yaml
# config/packages/dev/messenger.yaml
framework:
    messenger:
        routing:
            'App\Message\*':
                transport: 'sync://'   # dev: синхронно, для тестов

# config/packages/messenger.yaml (prod)
framework:
    messenger:
        routing:
            'App\Message\*':
                transport: 'amqp://%'  # prod: асинхронно
```

### 5. Correlation ID

```php
// В controller:
$requestId = $request->headers->get('X-Request-Id') ?? bin2hex(random_bytes(8));
$request->attributes->set('request_id', $requestId);

// В Outbox payload:
'payload': json_encode([...payload, 'requestId' => $requestId])

// В Worker log:
$this->logger->info('Invoice created', ['requestId' => $msg->requestId, ...]);

// Гrep по requestId в 4 лог-файлах → полная цепочка.
```

### 6. Monitoring

```
Метрики:
  - outbox.pending_count (积压)
  - outbox.processing_time (p50, p95, p99)
  - worker.success_count / worker.fail_count
  - dlq.count (критичная!)
  - worker.retry_count

Алерты:
  - dlq.count > 0 → P1
  - outbox.pending_count > 1000 → P2
  - worker.fail_rate > 5% → P2
```

---

## Итого

```
Когда:
  • Состояние агрегата изменилось и это имеет значение для других.
  • Реакция может быть асинхронной.
  • Есть 2+ consumers.

Как:
  • Dev: EventDispatcher (in-memory). Простой.
  • Prod: Outbox + Messenger + Workers. Надёжный.
  • Критичные (финансы): Outbox + Serializable + Idempotency.

Транзакции:
  • Событие в БД (outbox) → атомарно с агрегатом.
  • Событие в памяти → потеряно при crash.
  • Событие в AMQP (без outbox) → race condition.

Решает:
  • Связь агрегатов без зависимостей.
  • Async. Масштабируемость. Расширяемость.

Создаёт:
  • Eventual consistency (теряем strong).
  • Idempotency (дубли).
  • Ordering (если нужен).
  • Complexity (инфраструктура).
```

Доменное событие — это **контракт**: «это случилось, и ты можешь об этом знать». Не «сделай это». Не «в этот момент». Не «в этом порядке». Это факт. Что с ним делать — твоя проблема.

> ⏱ 75.6s · prompt 89186 tok · output 8643 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает три связанных вопроса:
1. Чем VO отличается от Entity в DDD?
2. Как решать, к чему отнести концепцию?
3. Что такое Ubiquitous Language и как она влияет на код?

Это теоретический вопрос, но с практической составляющей. Нужно:
- Чётко разделить VO и Entity (identity, mutability, equality)
- Дать практические критерии для классификации
- Объяснить Ubiquitous Language и показать, как она проявляется в коде
- Привести примеры из PHP/Symfony контекста (как в предыдущих ответах)

Структура ответа:
1. Ключевое различие (identity vs value)
2. Практические критерии (таблица вопросов)
3. Примеры: что VO, что Entity, что спорно
4. Ubiquitous Language — что это, как влияет на код, примеры хороших/плохих имён
5. Антипаттерны

Важно: не повторять то, что уже было сказано (Order/OrderItem как aggregate), а дать новые примеры и углубить.

Для VO vs Entity ключевое:
- Identity: у Entity есть ID, у VO нет
- Equality: Entity сравниваются по ID, VO по значению
- Mutability: Entity мутируются, VO неизменяемы (readonly)
- Lifecycle: Entity живут во времени (создана, изменена, удалена), VO — "момент"

Критерии для классификации:
- Есть ли уникальная идентичность?
- Меняется ли со временем (статус, количество)?
- Интересен ли сам объект или только его значение?
- Можно ли заменить равным?

Ubiquitous Language:
- Общий словарь между разработчиками, аналитиками, тестировщиками
- Имена в коде = имена в бизнесе
- "Order" а не "Record", "Customer" а не "User" (или наоборот, осознанно)
- Методы говорят на языке бизнеса: order.confirm() а не order.setStatus("confirmed")

Плюсы:
- Снижает когнитивную нагрузку
- Код читается как бизнес-требование
- Устраняет "перевод" между бизнесом и кодом
- Новичок понимает код, зная бизнес

Минусы/грабли:
- Не всегда совпадает (технические концепции: proxy, cache, middleware)
- Слишком буквальный перевод → плохие имена
- Споры о терминах тратят время
- Риск: бизнес-термин меняется → переименование всего кода

Примеры:
- Good: Money, OrderId, ShippingAddress, InvoiceLine
- Bad: Data, Info, Details, Manager, Handler, Processor (без контекста)

Дам структурированный ответ с кодом.

</details>

**Кандидат:**

## VO vs Entity: ключевое различие

Одно слово: **identity**.

| | Entity | Value Object |
|---|---|---|
| **Идентичность** | По `id`. Два объекта с одинаковыми полями — разные. | По значению. Два объекта с одинаковыми полями — **один и тот же**. |
| **Сравнение** | `a === b` (то же самое в памяти). `a.id == b.id` (та же сущность). | `a == b` (равные значения). `a === b` не важно. |
| **Время** | Живёт. Создаётся, изменяется, удаляется. Имеет историю. | Не живёт. Это **момент**. «500 ₽ в EUR». Нет вчерашних 500 ₽. |
| **Мутабельность** | Мутируется. `order.confirm()`, `stock.reserve(5)`. | Не мутируется. `money.add(other)` → **новый** `Money`. |
| **Ссылка** | Другой объект ссылается на неё по `id`. | Другой объект **включает** его. Не ссылается. |
| **Lifecycle** | Persist → mutate → flush. UnitOfWork отслеживает. | Создаётся на лету. Не persistится как отдельная строка (или — как колонка). |

### Наглядный пример

```php
// ENTITY: Order
final class Order
{
    public OrderId $id;          // ← identity
    public string  $customerId;
    public int     $totalCents;
    public string  $status;      // меняется со временем
    // ...

    // Два Order с одинаковыми полями — РАЗНЫЕ заказы.
    // Order#1 (status: pending) ≠ Order#2 (status: confirmed)
    // Даже если id, customer, total одинаковые.
}

// VALUE OBJECT: Money
final readonly class Money
{
    public int    $amount;
    public string $currency;
    // НЕТ id.

    // Money(500, "EUR") === Money(500, "EUR")?
    // В PHP: нет (=== — identity). Но СЕМАНТИЧЕСКИ: это одно и то же.
    // 500 евро = 500 евро. Не «вчерашние 500» и не «сегодняшние 500».

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
        // Новый объект. Старый не мутирован.
    }
}
```

### Почему это важно

```php
// Entity: можно мутировать. UnitOfWork видит изменение.
$order->status = 'confirmed';
$em->flush();  // UPDATE orders SET status = 'confirmed' WHERE id = ?

// VO: нельзя мутировать. Создаём новый.
$money = new Money(500, "EUR");
$money->add(new Money(200, "EUR"));  // ← ОШИБКА. $money не изменился.
$money = $money->add(new Money(200, "EUR"));  // ← новый объект. 700 EUR.
// $em не отслеживает Money. Это не entity.
```

Если случайно мутируешь VO — UnitOfWork не видит. Данные не обновляются. Тихий баг.

---

## Как решать: VO или Entity?

### Три вопроса

**1. Есть ли у него уникальная идентичность, которая важна для бизнеса?**

```
Order: да. «Заказ #42». Никто не скажет «заказ на 5000 ₽» — скажет «заказ #42».
Money: нет. «500 евро». Не «деньги #7». Не «евро-экземпляр 42».
Email: нет. «a@b.com». Не «почта #3».
```

**2. Меняется ли он со временем (без потери идентичности)?**

```
Order: да. pending → confirmed → shipped. Но это ВЕСЬ ЕЩЁ заказ #42.
Money: нет. 500 EUR — всегда 500 EUR. «Стал» 700 EUR — это ДРУГИЕ деньги.
Customer: да. Меняет email, адрес. Но это ВЕСЬ ЕЩЁ клиент #7.
```

**3. Можно ли заменить равным без потери смысла?**

```
Order: нет. Нельзя заменить заказ #42 на «другой заказ с теми же полями».
       Это другой клиент, другой продукт, другая история.
Money: да. 500 EUR = 500 EUR. Неважно, кто их «создал».
Email: да. a@b.com = a@b.com. Неважно, из какой формы.
```

Если на все три — «нет» → **VO**. Если хотя бы один «да» → **Entity**.

### Таблица

| Концепция | ID? | Меняется? | Заменим равным? | Класс |
|---|---|---|---|---|
| `Order` | Да (#42) | Да (status) | Нет | **Entity** |
| `OrderItem` | Да (composite) | Да (qty) | Нет (в контексте order) | **Entity** (child) |
| `Money` | Нет | Нет | Да | **VO** |
| `Email` | Нет | Нет | Да | **VO** |
| `Phone` | Нет | Нет | Да | **VO** |
| `ShippingAddress` | Нет | Нет | Да | **VO** |
| `Customer` | Да (#7) | Да (email, addr) | Нет | **Entity** |
| `Product` | Да (SKU-1) | Да (price, name) | Нет | **Entity** |
| `DateRange` | Нет | Нет | Да | **VO** |
| `Percentage` | Нет | Нет | Да | **VO** |
| `Invoice` | Да (#INV-99) | Да (status, lines) | Нет | **Entity** |
| `InvoiceLine` | Да (composite) | Да (qty, price) | Нет | **Entity** (child) |
| `Discount` | Нет | Нет | Да | **VO** |
| `OrderId` | Нет (это ID) | Нет | Да | **VO** (ID wrapper) |

### Спорные случаи

**`Address`:**

```
«Адрес доставки для заказа #42»
  → 123 Main St, Berlin, 10115

«Адрес клиента #7»
  → 123 Main St, Berlin, 10115
```

Один и тот же адрес. Разные контексты. Но **значение** — одно. Нет identity. Не меняется. Заменим равным.

→ **VO.** Хранится как строка/JSON в таблице `orders` и `customers`. Не отдельная сущность.

**`StockLevel`:**

```
«SKU-1 на складе A: quantity=100, reserved=20»
```

Есть ли identity? `SKU-1 + warehouse-A` — composite key. Меняется? Да (reserve, release). Заменим равным? Нет — это конкретный уровень на конкретном складе.

→ **Entity.** Но простой: одна строка в БД. Не aggregate root (если нет children).

**`Payment`:**

```
«Оплата заказа #42: $50.00, Stripe, tx_id=ch_abc123, status=succeeded»
```

Identity: `tx_id` или `id`. Меняется: `pending → succeeded`. Заменим: нет.

→ **Entity.** Отдельный агрегат (Billing context).

**`Session` / `Token`:**

```
«Сессия пользователя #7: token=xyz, expires=2024-12-31»
```

Identity: `token` или `id`. Меняется: `expires` обновляется. Заменим: нет.

→ **Entity.** Но «плоская»: одна строка, нет children.

---

## Ubiquitous Language

### Что это

Единый словарь, на котором говорят **все** участники: разработчик, аналитик, тестировщик, продуктовый менеджер, техподдержка. Один термин — одно понятие. Одно понятие — один термин.

Не «один человек понимает свои термины». Не «у каждого свой словарь». **Один** словарь для **всей** команды и **всего** кода.

### Как влияет на код

**Имена классов, методов, переменных, полей БД — на языке бизнеса.** Не на языке технологий.

```php
// БЕЗ Ubiquitous Language:
class Record {
    public $data;       // какая запись? какие данные?
    public $flag;       // что за флаг?
    public $value;      // какое значение?
    public function process() {}  // что обработывает?
}

// С Ubiquitous Language:
final class Order {
    public Money   $total;        // не $value, не $amount, не $sum
    public OrderStatus $status;   // не $flag, не $state
    public list<OrderItem> $items; // не $data, не $rows

    public function confirm(): void {}  // не process(), не activate(), не handle()
    public function cancel(): void {}
    public function total(): Money {}   // не getValue(), не getSum()
}
```

### Почему это работает

**1. Код читается как бизнес-требование.**

```
Требование: «Заказ можно подтвердить только если инвентарь зарезервирован.»

Код:
$order->confirm();  // ← «подтвердить заказ» — из требования
$stock->reserve();  // ← «зарезервировать инвентарь» — из требования
```

Не `processOrder(2)` и `decrementStock(5)`.

**2. Устраняет «перевод».**

```
Без UL:
  БД:  customers.email
  Код: $user->emailAddress
  UI:  «Почта»
  Аналитик: «e-mail»
  Тестировщик: «mail»
  → 5 терминов для одного понятия. Конфусия.

С UL:
  БД:  customers.email
  Код: $customer->email()
  UI:  «Email»
  Аналитик: «email»
  Тестировщик: «email»
  → 1 термин. Нет перевода.
```

**3. Снижает когнитивную нагрузку.**

Новый разработчик видит:

```php
final readonly class ShippingAddress
{
    public string $street;
    public string $city;
    public string $postalCode;
    public string $country;
}
```

И понимает, что это, не читая документацию. `ShippingAddress` — не `AddressData`, не `Location`, не `GeoInfo`. **Адрес доставки.** Точка.

### Как внедрять

**1. Словарь до кода.**

До написания кода — список терминов:

| Термин | Определение | Не путать с |
|---|---|---|
| **Order** | Запрос клиента на покупку товаров. Имеет ID, статус, позиции. | Не «заказ» в бухгалтерском смысле (Invoice). |
| **OrderItem** | Одна позиция в заказе. Ссылается на Product по ID. | Не «товар». Не «позиция инвентаря». |
| **Product** | Торговое предложение: SKU, название, цена. | Не «товар на складе» (StockLevel). |
| **StockLevel** | Количество товара на складе. Резерв. | Не «запас». Не «инвентарь» (Inventory — весь контекст). |
| **Customer** | Клиент, который совершает покупки. | Не «пользователь» (User — аутентификация). |
| **Invoice** | Счёт на оплату. Создётся после подтверждения заказа. | Не «заказ». Не «оплата» (Payment). |
| **Payment** | Факт оплаты. Gateway, сумма, статус. | Не «оплата» как процесс (PaymentProcess). |

**2. Одна команда — один словарь.**

В разных Bounded Context'ах один термин может значить разное:

```
Context: Ordering
  Order = заказ на покупку

Context: Billing
  Order = документ в 1С (другая сущность!)

Context: Logistics
  Order = отправление (ещё другая сущность!)
```

Решение: **разные имена** или **разные пространства имён**:

```php
namespace App\Domains\Ordering;
final class Order { ... }

namespace App\Domains\Billing;
final class BillingOrder { ... }  // не Order. Не путаем.

namespace App\Domains\Logistics;
final class Shipment { ... }  // не Order. Не путаем.
```

**3. Переименование — дорого.**

Если бизнес-термин меняется («клиент» → «партнёр»), переименование затрагивает:

- Class names
- Method names
- Table names
- Column names
- API endpoints
- Документацию
- Тесты

Поэтому: **фиксировать словарь ДО начала разработки**. Изменение термина — PR, не «ну переименуем потом».

### Где UL ломается

**1. Технические концепции без бизнес-имени.**

```php
class Proxy { ... }         // технический термин. OK.
class Middleware { ... }    // технический. OK.
class Repository { ... }    // технический. OK (но лучше: OrderRepository).
class Cache { ... }         // технический. OK.

// Но:
class Handler { ... }       // какой handler? PlaceOrderHandler. Не просто Handler.
class Service { ... }       // какой service? StockService. Не просто Service.
class Manager { ... }       // какой manager? PaymentManager? OrderManager?
class Processor { ... }     // что обрабатывает?
```

**Правило:** техническое слово допустимо, если **квалификатор** несёт бизнес-смысл. `OrderRepository` — хорошо. `Repository` — нет.

**2. «А у нас по-другому называют».**

```
Команда A: «клиент» = Customer
Команда B: «клиент» = Client (в CRM)
Команда C: «клиент» = Subscriber (в биллинге)

→ Три сущности. Три имени. Не один термин.
→ UL нарушена. Конфусия.
```

**Решение:** на встрече определить: «клиент» = `Customer`. `Client` — другое. `Subscriber` — третье. Записать в словарь.

**3. Буквальный перевод из другого языка.**

```
Английский:  "Order confirmation"
Русский:    "Подтверждение заказа"
Код:        OrderConfirmation  ← хорошо

Английский:  "Fulfillment"
Русский:    "Исполнение"
Код:        Fulfillment        ← OK, если команда понимает

Но:
Английский:  "Fulfillment"
Русский:    "Отгрузка"
Код:        Shipping           ← ХОРОШО. Бизнес-термин, не калька.
```

**Правило:** имя в коде = то, что говорит **бизнес**. Не перевод с английского. Если бизнес говорит «отгрузка» — `Shipping`, `Shipment`, `Отгрузка` (если код на русском). Не `Fulfillment`.

### Как UL проявляется в разных слоях

```
Бизнес-требование:
  «Заказ можно отменить, только если он ещё не подтверждён.»

Домен (PHP):
  $order->cancel();
  // Инвариант: status == pending. Иначе OrderNotCancellable.

API (JSON):
  POST /orders/{id}/cancel
  200: { "id": "42", "status": "cancelled" }
  409: { "error": "Order already confirmed" }

БД:
  UPDATE orders SET status = 'cancelled', cancelled_at = NOW()
  WHERE id = 42 AND status = 'pending'
  -- 0 rows → conflict

UI:
  [Кнопка «Отменить»]  ← видна только если status == 'pending'

Тест:
  it('cancels a pending order') { ... }
  it('rejects cancel of confirmed order') { ... }

Везде: «отмена», «заказ», «подтверждён», «отклонён».
Не «delete», не «deactivate», не «void».
```

---

## Антипаттерны

### 1. VO как Entity

```php
// АНТИПАТТЕРН: у VO есть ID и репозиторий
class Email {
    public int $id;          // ← зачем?
    public string $value;
}

class EmailRepository {
    public function findById(int $id): ?Email { ... }
    // ← кто будет искать email по id?
}
```

Email не имеет identity. Не живёт. Не мутируется. `Email` — VO. Хранится как `VARCHAR` в колонке `customers.email`. Не как отдельная строка.

**Когда VO становится Entity:** когда у него появляется **жизненный цикл**.

```
Email (VO): «a@b.com». Значение.
EmailVerification (Entity): «a@b.com, token=xyz, created=2024-01-01, expires=2024-01-02».
  → Живёт. Имеет id. Меняется (token refresh, expire).
  → Entity.
```

### 2. Entity как VO

```php
// АНТИПАТТЕРН: сущность без identity
class Money {
    public int $id;          // ← Money#42?
    public int $amount;
    public string $currency;
}

// Два Money(500, "EUR") с разными id → "разные" деньги?
// Нет. 500 EUR = 500 EUR. ID не нужен.
```

### 3. Имена без контекста

```php
class Data { ... }       // чьи данные?
class Info { ... }       // какая информация?
class Details { ... }    // детали чего?
class Manager { ... }    // что управляет?
class Handler { ... }    // что обрабатывает?
class Processor { ... }  // что обрабатывает?
class Service { ... }    // какой сервис?
```

Каждое из этих имён **не несёт бизнес-смысла**. `OrderData` — лучше, но всё ещё размыто. `Order` — точно.

### 4. Репозиторий для VO

```php
// АНТИПАТТЕРН:
class MoneyRepository {
    public function save(Money $m): void { ... }
    public function findById(int $id): ?Money { ... }
}

// Money не имеет id. Не persistится как сущность.
// Хранится как колонка в другой сущности.
```

### 5. Мутация VO

```php
// АНТИПАТТЕРН:
final class Money {
    public int $amount;
    public string $currency;

    public function add(self $other): void {   // ← void! Мутация!
        $this->amount += $other->amount;
    }
}

// Должно быть:
public function add(self $other): self {       // ← новый объект
    return new self($this->amount + $other->amount, $this->currency);
}
```

### 6. UL, которая не UL

```
Код:  $customer->email
БД:   users.mail
UI:   «Почта»
Аналитик: «e-mail address»
Тест:  «mail»

→ 5 именов. Не UL.
```

---

## Итого

### VO vs Entity — один вопрос

> **«Если я заменю этот объект на равный, система сломается?»**

- Да → **Entity**. (Заказ #42 ≠ Заказ #43, даже если поля одинаковые.)
- Нет → **VO**. (500 EUR = 500 EUR. Неважно, откуда.)

### Ubiquitous Language — один принцип

> **«Имя в коде = слово, которое говорит бизнес. Не перевод. Не синоним. Это слово.»**

Это не «красивые имена». Это **устранение перевода** между бизнесом и кодом. Когда перевод исчезает — ошибки исчезают. Когда аналитик говорит «заказ подтверждён», а в коде `order.status = 'confirmed'` — нет разрыва. Нет «а что значит confirmed? А может, оно means 'activated'?»

### Практика

1. **Перед кодом** — словарь. 10–20 терминов. Определения. Анти-примеры.
2. **В коде** — имена на языке словаря. Классы, методы, поля, колонки.
3. **В тесте** — имена на языке словаря. `it('cancels a pending order')`.
4. **В API** — имена на языке словаря. `POST /orders/{id}/cancel`.
5. **В БД** — имена на языке словаря. `orders.status = 'cancelled'`.
6. **Когда термин меняется** — PR. Не «ну переименуем потом».

> ⏱ 56.7s · prompt 96906 tok · output 5606 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о чистой архитектуре в PHP-приложении. Это продолжение длинного разговора о PHP, Symfony, DDD, Doctrine. Нужно дать структурированный ответ с примерами кода, показать:

1. Слои и что в каждом
2. Правило зависимостей (стрелки показывают "depends on")
3. Куда попадают конкретные технологии (Doctrine, Symfony controller, queue handler)
4. Как следить за границами (статический анализ, архитектура-тесты, folder structure)

Нужно быть конкретным, с кодом, с trade-offs. Не абстрактно — "вот как выглядит реальный проект". Показать, что чистая архитектура — не религия, а инструмент. Показать где она оправдана, где — overkill.

Структура ответа:
1. Краткая идея: слои и правило зависимостей
2. Конкретная структура папок с кодом
3. Куда попадает каждый компонент (Doctrine, Symfony controller, queue handler, EventDispatcher)
4. Правило зависимостей — стрелки
5. Как следить: PHPStan rules, folder structure, архитектура-тесты, code review
6. Trade-offs: когда чистая архитектура оправдана, когда — overkill
7. Антипаттерны

Давайте сделаю это максимально конкретно, с реальным примером (Order/Inventory), с кодом каждого слоя, с PHPStan-конфигурацией, с архитектурными тестами.

</details>

**Кандидат:**

## Идея в одном абзаце

Чистая архитектура — это **правило направленных зависимостей**: внутренние слои (домен, application) не знают о внешних (HTTP, БД, очередь, внешние API). Зависимости всегда идут **наружу → внутрь**, никогда наоборот. Внутренние слои определяют **интерфейсы**, внешние — **реализуют**. Это не «много папок». Это **направление стрелок**.

```
        НАРУЖНОЕ                        ВНУТРЕННЕЕ
        (может меняться)                (стабильно)

  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
  │  Frameworks  │───→│  Application │───→│    Domain    │
  │  (Symfony,   │    │  (use cases, │    │  (модель,    │
  │  Doctrine,   │    │   команды,   │    │   правила,   │
  │  AMQP, ...)  │    │   DTO)       │    │   VO)        │
  └──────────────┘    └──────────────┘    └──────────────┘
        ↑                    ↑                    ↑
   может меняться       может меняться         не должен
   (смена framework)   (смена сценариев)      меняться
```

Стрелки: `Domain ← Application ← Frameworks`. Домен **не знает** о Symfony, Doctrine, AMQP. Application **не знает** о контроллерах, репозиториях, транспортах.

---

## Конкретная структура

```
src/
├── Domain/                        ← ВНУТРЕННИЙ ЯДРО. Чистый PHP.
│   ├── Order/
│   │   ├── Order.php              ← Aggregate root
│   │   ├── OrderItem.php          ← Child entity (VO-like внутри aggregate)
│   │   ├── OrderId.php            ← VO (ID wrapper)
│   │   ├── OrderStatus.php        ← Enum
│   │   ├── OrderPlaced.php        ← Domain event
│   │   ├── OrderConfirmed.php     ← Domain event
│   │   ├── EmptyOrder.php         ← Domain exception
│   │   ├── OrderNotPending.php    ← Domain exception
│   │   └── OrderRepositoryInterface.php  ← Интерфейс (port)
│   ├── Inventory/
│   │   ├── StockLevel.php         ← Entity
│   │   ├── StockRepositoryInterface.php
│   │   ├── InsufficientStock.php
│   │   └── ...
│   ├── Money.php                  ← VO
│   └── DomainEvent.php            ← Интерфейс для событий
│
├── Application/                   ← USE CASES. Оркестрация.
│   ├── Order/
│   │   ├── PlaceOrderCommand.php  ← Input DTO
│   │   ├── PlaceOrderHandler.php  ← Use case (оркестрация)
│   │   ├── ConfirmOrderCommand.php
│   │   ├── ConfirmOrderHandler.php
│   │   ├── OrderNotFound.php      ← Application exception
│   │   └── PricedOrder.php        ← Output DTO
│   ├── Inventory/
│   │   ├── ReserveStockCommand.php
│   │   └── ReserveStockHandler.php
│   └── Shared/
│       ├── Command.php            ← Маркерный интерфейс
│       ├── Handler.php            ← Маркерный интерфейс
│       └── Result.php             ← Обёртка результата
│
├── Infrastructure/                ← ВНЕШНИЙ МИР. Адаптеры.
│   ├── Persistence/
│   │   ├── Doctrine/
│   │   │   ├── Entity/
│   │   │   │   ├── OrderEntity.php
│   │   │   │   └── StockEntity.php
│   │   │   ├── Repository/
│   │   │   │   ├── DoctrineOrderRepository.php
│   │   │   │   └── DoctrineStockRepository.php
│   │   │   └── Types/
│   │   │       ├── OrderIdType.php
│   │   │       └── MoneyType.php
│   │   └── Outbox/
│   │       ├── OutboxEntity.php
│   │       └── OutboxRepository.php
│   ├── Http/
│   │   ├── Client/
│   │   │   └── StripeGateway.php  ← Адаптер внешнего API
│   │   └── Server/                ← (см. ниже)
│   ├── Messaging/
│   │   ├── Transport/
│   │   │   └── AmqpConfig.php
│   │   └── Handler/
│   │       ├── CreateInvoiceHandler.php
│   │       └── ReleaseStockHandler.php
│   └── Search/
│       └── MeilisearchAdapter.php
│
└── Interfaces/                    ← ВХОДНЫЕ/ВЫХОДНЫЕ ТОЧКИ (framework-specific)
    ├── Http/                      ← Symfony
    │   ├── Controller/
    │   │   ├── OrderController.php
    │   │   └── InventoryController.php
    │   ├── Form/
    │   │   └── PlaceOrderFormType.php
    │   ├── Serializer/
    │   │   └── OrderDto.php       ← API DTO (не domain!)
    │   └── Security/
    │       └── OrderVoter.php
    ├── Cli/                       ← Symfony Console
    │   └── Command/
    │       ├── ImportOrdersCommand.php
    │       └── ReindexSearchCommand.php
    └── Message/                   ← Входящие сообщения (от других сервисов)
        └── Webhook/
            └── StripeWebhookHandler.php
```

---

## Что в каждом слое

### Domain — ядро. Чистый PHP.

```php
// src/Domain/Order/Order.php
declare(strict_types=1);

namespace App\Domain\Order;

final class Order
{
    private OrderId $id;
    private string  $customerId;
    /** @var list<OrderItem> */
    private array   $items = [];
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    private function __construct() {}

    public static function create(string $customerId, array $items): self
    {
        if ($items === []) throw new EmptyOrder();
        $o = new self();
        $o->id = OrderId::generate();
        $o->customerId = $customerId;
        $o->items = $items;
        $o->status = OrderStatus::Pending;
        $o->createdAt = new \DateTimeImmutable();
        return $o;
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new InvalidTransition($this->status, OrderStatus::Confirmed);
        }
        $this->status = OrderStatus::Confirmed;
        $this->domainEvents[] = new OrderConfirmed($this->id, $this->totalCents());
    }

    public function cancel(): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new CannotCancelDelivered();
        }
        $this->status = OrderStatus::Cancelled;
        $this->domainEvents[] = new OrderCancelled($this->id);
    }

    // ... getters, totalCents(), addItem(), removeItem() ...

    /** @return list<DomainEvent> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    private function __destruct() {}  // запрет сериализации? Нет, это не нужно.
}
```

```php
// src/Domain/Order/OrderRepositoryInterface.php
declare(strict_types=1);

namespace App\Domain\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(OrderId $id): ?Order;
    public function findByCustomerId(string $customerId): array;
}
```

**Что домен НЕ знает:**
- Symfony. Ни `Controller`, ни `Request`, ни `Response`, ни `FormType`.
- Doctrine. Ни `EntityManager`, ни `Entity`, ни `Repository` (в смысле Doctrine).
- AMQP / Redis. Ни `Transport`, ни `MessageBus`.
- HTTP. Ни `curl`, ни `HttpClient`.
- БД. Ни `PDO`, ни `SQL`, ни `migration`.

**Что домен знает:**
- Свою модель: `Order`, `OrderItem`, `OrderId`, `OrderStatus`.
- Свои инварианты: `EmptyOrder`, `InvalidTransition`.
- Свой порт: `OrderRepositoryInterface`.
- Свои события: `OrderConfirmed`, `OrderCancelled`.

**Зависимости:** только PHP stdlib + свои классы. Ноль внешних пакетов.

### Application — use cases. Оркестрация.

```php
// src/Application/Order/PlaceOrderHandler.php
declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Inventory\StockRepositoryInterface;
use App\Domain\Inventory\InsufficientStock;
use App\Domain\Order\EmptyOrder;

final class PlaceOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private StockRepositoryInterface $stock,
        private EventDispatcherInterface $dispatcher,  // ← interface, не concrete
    ) {}

    public function handle(PlaceOrderCommand $cmd): OrderId
    {
        $items = array_map(
            static fn (array $i): OrderItem => new OrderItem(
                productId:    $i['product_id'],
                quantity:     $i['quantity'],
                unitPriceCents: $i['unit_price'],
            ),
            $cmd->items,
        );

        $order = Order::create($cmd->customerId, $items);

        foreach ($order->items() as $item) {
            $this->stock->reserve($item->productId, $item->quantity);
        }

        $this->orders->save($order);

        foreach ($order->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

        return $order->id();
    }
}
```

**Что application знает:**
- Domain: `Order`, `OrderItem`, `OrderRepositoryInterface`, `StockRepositoryInterface`.
- Свои DTO: `PlaceOrderCommand`, `PricedOrder`.
- Интерфейс диспетчера: `EventDispatcherInterface` (свой, не Symfony's).

**Что application НЕ знает:**
- Symfony Controller, Request, Response, Form.
- Doctrine EntityManager, Entity.
- Конкретный транспорт (AMQP, Redis).
- Как хранятся данные (БД, файл, Redis).

**Зависимости:** Domain + свои DTO + интерфейсы (ports).

### Infrastructure — адаптеры. Внешний мир.

```php
// src/Infrastructure/Persistence/Doctrine/Repository/DoctrineOrderRepository.php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,   // ← Doctrine здесь, не в Domain
    ) {}

    public function save(Order $order): void
    {
        $entity = OrderEntity::fromDomain($order);
        $this->em->persist($entity);
        // НЕ flush. Flush — в application layer или в транзакции.
    }

    public function findById(OrderId $id): ?Order
    {
        $entity = $this->em->find(OrderEntity::class, $id->value);
        return $entity !== null ? $entity->toDomain() : null;
    }

    public function findByCustomerId(string $customerId): array
    {
        $entities = $this->em->getRepository(OrderEntity::class)
            ->findBy(['customerId' => $customerId]);
        return array_map(
            static fn (OrderEntity $e): Order => $e->toDomain(),
            $entities,
        );
    }
}
```

```php
// src/Infrastructure/Http/Client/StripeGateway.php
declare(strict_types=1);

namespace App\Infrastructure\Http\Client;

use App\Application\Payment\PaymentGatewayInterface;  ← port из Application
use Symfony\Contracts\HttpClient\HttpClientInterface;  ← Symfony здесь

final class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(
        private HttpClientInterface $http,
        private string $apiKey,
    ) {}

    public function charge(ChargeRequest $r): ChargeResult
    {
        // Symfony HttpClient здесь. Domain и Application не знают о нём.
    }
}
```

**Что infrastructure знает:**
- Всё. Doctrine, Symfony, AMQP, Redis, HTTP, Meilisearch.
- Domain (интерфейсы, VO).
- Application (интерфейсы-порты).

**Зависимости:** Domain + Application + внешние пакеты.

### Interfaces (адаптеры ввода) — framework-specific

```php
// src/Interfaces/Http/Controller/OrderController.php
declare(strict_types=1);

namespace App\Interfaces\Http\Controller;

use App\Application\Order\PlaceOrderCommand;
use App\Application\Order\PlaceOrderHandler;
use App\Domain\Order\OrderId;
use App\Domain\Order\EmptyOrder;
use App\Domain\Inventory\InsufficientStock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private PlaceOrderHandler $placeOrder,   // ← application, не domain
    ) {}

    #[Route('/orders', name: 'order_place', methods: ['POST'])]
    public function place(Request $request): JsonResponse
    {
        // Symfony-specific: Request, JsonResponse, Route, AbstractController
        $data = json_decode($request->getContent(), true) ?? [];

        $cmd = new PlaceOrderCommand(
            customerId: (string) ($data['customer_id'] ?? ''),
            items:      $data['items'] ?? [],
        );

        try {
            $id = $this->placeOrder->handle($cmd);
        } catch (EmptyOrder $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        } catch (InsufficientStock $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }

        return new JsonResponse(['id' => $id->value], 201);
    }
}
```

```php
// src/Infrastructure/Messaging/Handler/CreateInvoiceHandler.php
declare(strict_types=1);

namespace App\Infrastructure\Messaging\Handler;

use App\Application\Invoice\CreateInvoiceCommand;
use App\Application\Invoice\CreateInvoiceHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateInvoiceMessageHandler
{
    public function __construct(
        private CreateInvoiceHandler $handler,  // ← application
    ) {}

    public function __invoke(CreateInvoiceCommand $cmd): void
    {
        $this->handler->handle($cmd);
    }
}
```

**Что Interfaces знает:**
- Symfony (Route, Request, Response, FormType, AbstractController).
- Application (handlers, commands).
- Domain (exceptions, для catch).

**Зависимости:** Application + Domain + Symfony.

---

## Правило зависимостей: стрелки

```
                    НЕ ЗНАЕТ
                    ↓
┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│   Interfaces  ←  Application  ←  Domain                             │
│   (Http, CLI, │  (Handlers,   │  (Order,     │                     │
│    Message)    │   Commands,   │   OrderItem, │                     │
│                │   DTO)        │   Money,     │                     │
│                │               │   Repos Intf)│                     │
│                                                                     │
│   Зависит от:  Зависит от:  Зависит от:                             │
│   - Application - Domain       - Ничего (чистый PHP)                │
│   - Domain        - Ports      - Ports (интерфейсы)                 │
│   - Symfony       (интерф.)                                     │
│   - Application                                                             │
│     (handlers)                                                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘

                    НЕ ЗНАЕТ
                    ↑
Infrastructure  →  реализует порты Application/Domain
(HTTP client, Doctrine, AMQP, Meilisearch)
Зависит от: Domain + Application + внешние пакеты
```

**Правило:**

| Кто | Может зависеть от | Не может зависеть от |
|---|---|---|
| **Domain** | PHP stdlib, свои классы | Всё остальное |
| **Application** | Domain, свои DTO, порты (интерфейсы) | Infrastructure, Interfaces, Symfony, Doctrine |
| **Infrastructure** | Domain, Application, внешние пакеты | Interfaces |
| **Interfaces** | Application, Domain, Symfony | Infrastructure (прямые) |

**Исключение:** `Infrastructure` и `Interfaces` могут общаться **косвенно** через DI-контейнер. `OrderController` не `new`'ит `DoctrineOrderRepository`. Он получает `PlaceOrderHandler`, который получает `OrderRepositoryInterface`. Конкретную реализацию подставляет Symfony DI.

---

## Куда попадают конкретные компоненты

### Symfony Controller

```
src/Interfaces/Http/Controller/OrderController.php
```

**Почему здесь:** Controller — адаптер HTTP-протокола. Превращает `Request` → `PlaceOrderCommand`, `OrderId` → `Response`. Это **входная точка**. Зависит от Symfony. Не зависит от Doctrine.

**Альтернатива:** некоторые кладут контроллеры в `src/Controller/` (стандарт Symfony). Тогда правило зависит от **импорта**, не от папки. `OrderController` не импортирует `Doctrine\*`. Это проверяемо PHPStan.

### Doctrine (Entity, Repository, Type)

```
src/Infrastructure/Persistence/Doctrine/
├── Entity/OrderEntity.php
├── Repository/DoctrineOrderRepository.php
├── Types/OrderIdType.php
└── Migrations/Version20240101000000.php
```

**Почему здесь:** Doctrine — технологический выбор. Можно заменить на Eloquent, на raw SQL, на Redis. Domain не должен знать.

**Важно:** `OrderEntity` ≠ `Order`. `OrderEntity` — мутабельный, с setters, без инвариантов. `Order` — неизменяемый (в смысле мутаций через методы), с инвариантами. Они **переводят** друг друга:

```php
// OrderEntity → Order (hydrate)
public function toDomain(): Order { ... }

// Order → OrderEntity (persist)
public static function fromDomain(Order $order): self { ... }
```

### Обработчик очереди

```
src/Infrastructure/Messaging/Handler/CreateInvoiceMessageHandler.php
```

**Почему здесь:** Обработчик знает о Symfony Messenger (`#[AsMessageHandler]`, `Message` class). Это инфраструктурный адаптер. Бизнес-логика — в `CreateInvoiceHandler` (Application).

```
Message (AMQP)
    → CreateInvoiceMessageHandler  (Infrastructure: десериализация, retry)
        → CreateInvoiceHandler     (Application: бизнес-оркестрация)
            → Order                (Domain: правила)
```

### EventDispatcher

```
// Domain:  OrderConfirmed (событие)
// Application: dispatch через EventDispatcherInterface (порт)
// Infrastructure: Symfony EventDispatcher, или Messenger, или Outbox+AMQP
```

`EventDispatcherInterface` — в **Application** (или в **Domain**, если события — часть доменной модели). Конкретная реализация (Symfony `EventDispatcher`, `Messenger`, Outbox relay) — в **Infrastructure**.

### Voter (Security)

```
src/Interfaces/Http/Security/OrderVoter.php
```

**Почему здесь:** Voter зависит от Symfony Security (`Voter`, `UserInterface`). Это framework-specific. Доменная логика (`canBeEditedBy`) — в Domain.

```php
// Domain:
final class Order
{
    public function canBeEditedBy(User $user): bool { ... }
}

// Interfaces/Http/Security:
final class OrderVoter extends Voter
{
    protected function voteOnAttribute(string $attr, mixed $subject, UserInterface $user): bool
    {
        /** @var Order $order */
        return match ($attr) {
            self::EDIT => $order->canBeEditedBy(User::fromSymfony($user)),
        };
    }
}
```

---

## Как следить, что границы не нарушаются

### 1. PHPStan / Psalm — правила импорта

```neon
# phpstan.neon
parameters:
    level: 8
    paths:
        - src/
        - tests/

    # Domain: не должен импортировать Symfony, Doctrine, anything
    checkExplicitMixed: true
    reportUnmatchedIgnoredErrors: true

# Кастомные правила:
rules:
    - App\PHPStan\DomainImportRule

# Или через forbidden-классы:
forbiddenCalls:
    methods:
        - eval
        - shell_exec
```

Кастомное правило:

```php
// src/PHPStan/DomainImportRule.php
namespace App\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNodeAnalyzer;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Namespace_>
 */
final class DomainImportRule implements Rule
{
    private const FORBIDDEN_PREFIXES = [
        'Symfony\\',
        'Doctrine\\',
        'Psr\\Http',
        'GuzzleHttp\\',
        'OpenTelemetry\\',
    ];

    public function getNodeType(): string
    {
        return Namespace_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $node->name?->toString() ?? '';

        // Правило: только файлы в App\Domain\*
        if (!str_starts_with($namespace, 'App\Domain')) {
            return [];
        }

        $errors = [];

        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof \PhpParser\Node\Stmt\Use_) {
                continue;
            }
            foreach ($stmt->uses as $use) {
                $imported = $use->name->toString();
                foreach (self::FORBIDDEN_PREFIXES as $prefix) {
                    if (str_starts_with($imported, $prefix)) {
                        $errors[] = sprintf(
                            'Domain layer must not import %s (file: %s)',
                            $imported,
                            $scope->getFile(),
                        );
                    }
                }
            }
        }

        return $errors;
    }
}
```

```php
// Аналогично для Application:
// Не может импортировать Symfony\*, Doctrine\* (кроме интерфейсов)
// Может импортировать App\Domain\*
```

### 2. Архитектурные тесты

```php
// tests/Architecture/LayerBoundaryTest.php
final class LayerBoundaryTest extends TestCase
{
    public function test_domain_does_not_import_symfony(): void
    {
        $this->assertNoForbiddenImports(
            directory:  'src/Domain',
            forbidden:  ['Symfony\\', 'Doctrine\\', 'Psr\\Http'],
        );
    }

    public function test_domain_does_not_import_infrastructure(): void
    {
        $this->assertNoForbiddenImports(
            directory:  'src/Domain',
            forbidden:  ['App\\Infrastructure\\', 'App\\Interfaces\\'],
        );
    }

    public function test_application_does_not_import_symfony(): void
    {
        $this->assertNoForbiddenImports(
            directory:  'src/Application',
            forbidden:  [
                'Symfony\\Bundle\\',
                'Symfony\\Component\\HttpKernel',
                'Symfony\\Component\\Routing',
                'Doctrine\\ORM\\EntityManager',
            ],
            // Разрешено: Symfony\Contracts (HttpClient, Messenger interfaces)
        );
    }

    public function test_application_does_not_import_interfaces(): void
    {
        $this->assertNoForbiddenImports(
            directory:  'src/Application',
            forbidden:  ['App\\Interfaces\\'],
        );
    }

    public function test_domain_files_are_pure_php(): void
    {
        // Нет use-выводов, кроме App\Domain\* и PHP stdlib
        $files = $this->findPhpFiles('src/Domain');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $imports = $this->extractImports($content);

            foreach ($imports as $import) {
                $allowed = str_starts_with($import, 'App\\Domain\\')
                    || str_starts_with($import, 'App\\Shared\\')
                    || $this->isStdlib($import);
                self::assertTrue($allowed, "Domain file $file imports $import");
            }
        }
    }

    private function assertNoForbiddenImports(
        string $directory,
        array $forbidden,
        array $allowed = [],
    ): void {
        $files = $this->findPhpFiles($directory);
        $violations = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $imports = $this->extractImports($content);

            foreach ($imports as $import) {
                // Проверить forbidden
                foreach ($forbidden as $prefix) {
                    if (str_starts_with($import, $prefix)) {
                        // Проверить allowed
                        $isAllowed = false;
                        foreach ($allowed as $allowPrefix) {
                            if (str_starts_with($import, $allowPrefix)) {
                                $isAllowed = true;
                                break;
                            }
                        }
                        if (!$isAllowed) {
                            $violations[] = "$file imports $import";
                        }
                    }
                }
            }
        }

        self::assertEmpty($violations, implode("\n", $violations));
    }

    private function findPhpFiles(string $dir): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        $files = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    private function extractImports(string $content): array
    {
        $imports = [];
        if (preg_match_all('/^\s*use\s+([\w\\\\]+)/m', $content, $matches)) {
            $imports = $matches[1];
        }
        return $imports;
    }

    private function isStdlib(string $import): bool
    {
        $stdlib = ['DateTimeImmutable', 'ArrayIterator', 'Iterator', 'Countable'];
        return in_array($import, $stdlib, true)
            || !str_contains($import, '\\');
    }
}
```

Запуск: `vendor/bin/phpunit tests/Architecture/` в CI. Красный тест = нарушена граница.

### 3. Folder structure + composer autoload

```json
{
    "autoload": {
        "psr-4": {
            "App\\Domain\\":           "src/Domain/",
            "App\\Application\\":      "src/Application/",
            "App\\Infrastructure\\":   "src/Infrastructure/",
            "App\\Interfaces\\":       "src/Interfaces/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\Tests\\Architecture\\": "tests/Architecture/"
        }
    }
}
```

**Почему это помогает:** namespace = слой. `App\Domain\Order\Order` — сразу видно, что это domain. `App\Infrastructure\Persistence\Doctrine\Entity\OrderEntity` — сразу видно, что это Doctrine. Не `src/Entity/Order.php` (смешение). Не `src/Model/Order.php` (размыто).

### 4. DI-конфигурация как «карта зависимостей»

```yaml
# config/services.yaml
services:
    # Domain: нет сервисов. Только классы.
    # Application: handlers
    App\Application\Order\PlaceOrderHandler:
        autowire: true
        autoconfigure: true

    # Infrastructure: конкретные реализации
    App\Infrastructure\Persistence\Doctrine\Repository\DoctrineOrderRepository:
        autowire: true

    # Interfaces: контроллеры, commands
    App\Interfaces\Http\Controller\OrderController:
        autowire: true
        public: true

    # Binding: порт → реализация
    App\Domain\Order\OrderRepositoryInterface:
        alias: App\Infrastructure\Persistence\Doctrine\Repository\DoctrineOrderRepository
```

**Правило:** в `services.yaml` видно **какой слой** реализует **какой порт**. Если `Domain` появляется в `services.yaml` как concrete class — что-то не так. Domain — не сервисы. Domain — классы.

### 5. Code review чек-лист

```
[ ] Domain-файл: нет use Symfony\*, Doctrine\*, Guzzle\*
[ ] Application-файл: нет use Doctrine\ORM\EntityManager, Symfony\Bundle
[ ] Infrastructure: implements интерфейс из Domain/Application
[ ] Interfaces: нет прямого доступа к Doctrine (через handler)
[ ] Новый класс: в правильной папке? Правильный namespace?
[ ] Новый import: нарушает ли правило?
[ ] DI-конфиг: новый сервис — в правильном слое?
```

### 6. CI pipeline

```yaml
# .github/workflows/architecture.yml
jobs:
  architecture-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - run: composer install
      - run: vendor/bin/phpstan analyse --level 8
      - run: vendor/bin/phpunit tests/Architecture/
      - run: grep -r "use Doctrine" src/Domain/ && exit 1 || true
      - run: grep -r "use Symfony" src/Domain/ && exit 1 || true
      - run: grep -r "use Doctrine" src/Application/ && exit 1 || true
```

---

## Trade-offs: когда это оправдано, а когда — overkill

### Оправдано

| Условие | Почему |
|---|---|
| 3+ разработчика | Общее понимание структуры. Меньше «а где это лежит?» |
| 6+ месяцев разработки | Границы накапливают смысл. Новый разработчик понимает, куда что класть. |
| 2+ технологии доступа (БД + API + очередь) | Адаптеры изолированы. Замена технологии не трогает домен. |
| Смена фреймворка / ORM в планах | Домен чистый. Замена = новый слой Infrastructure. |
| Критичная бизнес-логика | Инварианты в домене, не в контроллере. Тестируются без БД. |

### Overkill

| Условие | Почему |
|---|---|
| 1 разработчик, 2 недели | Папки > польза. Лучше один `src/Service/`. |
| CRUD-админка, 10 сущностей | Домен = Entity + Controller. Чистая архитектура = 4× папок. |
| Прототип / spike | Скорость > структура. |
| 2 технологии, 1 БД, 1 API | Адаптеры не нужны. `OrderRepository` → `DoctrineOrderRepository` — один файл. |

**Практическое правило:** если проект > 3 месяцев и > 2 разработчика — начинайте с Domain + Application + Infrastructure. Если < 3 месяца и 1 разработчик — `src/Entity/` + `src/Service/` + `src/Controller/` достаточно.

---

## Антипаттерны

### 1. «Чистая архитектура» = 20 папок

```
src/
├── Domain/
│   ├── Order/
│   │   ├── Entity/
│   │   ├── ValueObject/
│   │   ├── Event/
│   │   ├── Exception/
│   │   ├── Repository/
│   │   ├── Service/
│   │   ├── Factory/
│   │   ├── Query/
│   │   ├── Specification/
│   │   └── Policy/
```

10 папок для **одного** агрегата. Каждая папка — 1–3 файла. Файлы перепрыгивают между папками. Новый разработчик не понимает, куда класть `OrderStatus`. В `Entity/`? В `ValueObject/`? В `Policy/`?

**Решение:** плоская структура. `src/Domain/Order/` — все файлы агрегата. Без подпапок.

### 2. Domain зависит от Application

```php
// src/Domain/Order/Order.php
use App\Application\Order\PlaceOrderCommand;  ← НАРУШЕНИЕ!

class Order
{
    public function fromCommand(PlaceOrderCommand $cmd): self { ... }
}
```

Domain не знает о `PlaceOrderCommand`. Это Application DTO. `Order::create(string $customerId, array $items)` — чистый домен.

### 3. Controller ходит в Domain

```php
// src/Interfaces/Http/Controller/OrderController.php
use App\Domain\Order\Order;

public function confirm(Order $order): Response
{
    $order->confirm();  ← Controller знает о доменной логике
    // ...
}
```

Controller должен вызывать **Application handler**:

```php
public function confirm(string $id): Response
{
    $this->confirmHandler->handle(new ConfirmOrderCommand($id));
    // Controller не знает, что происходит внутри.
}
```

### 4. Infrastructure знает о Interfaces

```php
// src/Infrastructure/Http/Client/StripeGateway.php
use App\Interfaces\Http\Serializer\StripeDto;  ← НАРУШЕНИЕ!
```

Infrastructure не знает о `Interfaces/`. DTO для API — в `Interfaces/`. `StripeGateway` принимает `ChargeRequest` (Application DTO), а не `StripeDto`.

### 5. «Domain» с `EntityManager`

```php
// src/Domain/Order/Order.php
use Doctrine\ORM\EntityManagerInterface;  ← НАРУШЕНИЕ!

class Order
{
    public function __construct(private EntityManagerInterface $em) { ... }
}
```

`EntityManager` — Infrastructure. Domain не знает о нём. `OrderRepositoryInterface` — порт. `DoctrineOrderRepository` — реализация.

### 6. Все в `src/Service/`

```php
// src/Service/OrderService.php  (500 строк)
// src/Service/OrderService.php  (ещё 500 строк)
// src/Service/InvoiceService.php
// src/Service/StockService.php
// src/Service/NotificationService.php
// ... 20 сервисов, все знают друг о друге
```

Это не «чистая архитектура». Это «грязная архитектура с папкой `Service/`». Слои нужны не для красоты. Для **направления зависимостей**.

---

## Итого

```
┌─────────────────────────────────────────────────────────────────────┐
│  ПРАВИЛО: зависимости идут НАРУЖУ → ВНУТРЬ                           │
│                                                                     │
│  Domain ← Application ← Infrastructure                             │
│     ↑         ↑                 ↑                                    │
│  Чистый    Use cases        Адаптеры                               │
│  PHP       + DTO            (Doctrine, Symfony, AMQP)              │
│                                                                     │
│  Domain не знает о Symfony, Doctrine, AMQP                         │
│  Application не знает о Controller, EntityManager, Transport        │
│  Infrastructure знает всё и реализует порты                         │
│                                                                     │
│  КОНТРОЛЬ:                                                          │
│  1. PHPStan: forbidden imports per layer                           │
│  2. Архитектурные тесты: assertNoForbiddenImports                  │
│  3. Folder structure = namespace = layer                           │
│  4. DI-конфиг: порт → реализация (видна карта)                      │
│  5. Code review: чек-лист                                          │
│  6. CI: grep + phpstan + arch tests                                │
│                                                                     │
│  КОГДА: > 3 месяца, > 2 разработчика, критичная бизнес-логика       │
│  КОГДА НЕ: прототип, CRUD, 1 разработчик, < 3 месяца               │
└─────────────────────────────────────────────────────────────────────┘
```

> ⏱ 76.4s · prompt 101943 tok · output 8986 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о стратегии тестирования для Symfony-приложения с PostgreSQL. Нужно покрыть: пирамиду тестов, типы тестов, что мокаем/не мокаем, как держать тесты быстрыми и стабильными.

Учитывая весь контекст разговора (DDD, чистая архитектура, Doctrine, PostgreSQL, доменные события, агрегаты), ответ должен быть последовательным с предыдущими ответами. Я уже обсуждал:
- Доменные события и outbox
- Агрегаты и конкурентность
- Value Object vs Entity
- Чистую архитектуру со слоями
- DI-контейнер Symfony
- PostgreSQL и его особенности

Стратегия тестирования должна быть привязана к этой архитектуре. Не абстрактная "пирамида тестов", а конкретная: что тестируем на каждом слое, как.

Ключевые решения:
1. Пирамида: unit (домен) → integration (application + infrastructure) → e2e (interfaces)
2. Домен: чистые unit-тесты без БД, без Symfony
3. Application: integration с реальным репозиторием (in-memory или Postgres)
4. Infrastructure: интеграционные тесты с реальным Postgres (Testcontainers)
5. Interfaces: функциональные тесты через HTTP kernel
6. Что мокаем: внешние сервисы (Stripe, email). Что не мокаем: БД, Doctrine, DI.
7. Скорость: Testcontainers vs in-memory, параллельный запуск, без network calls
8. Стабильность: determinism, фиксированные даты, изоляция данных

Структурирую ответ:
- Пирамида с конкретными числами
- Каждый слой: что тестируем, как, пример кода
- Моки: чёткое правило
- Скорость: конкретные техники
- Стабильность: конкретные техники
- Антипаттерны
- CI pipeline

Напишу на русском, как весь разговор.

</details>

**Кандидат:**

## Принципиальная позиция

Тестируем **поведение**, не **реализацию**. Тест, который ломается при смене реализации (Doctrine → Eloquent, AMQP → Redis), без изменения поведения — **плохой тест**. Тест, который ловит баг в бизнес-логике, не зависит от технологии — **хороший**.

Это определяет всё остальное.

---

## Пирамида для нашего приложения

```
                    ┌──────────────┐
                    │  E2E / API   │  5–10 тестов
                    │  (Interfaces)│  Полный стек: HTTP → Handler → Repo → PG
                    │              │  Через Kernel, через реальную БД
                    ├──────────────┤
                    │ Integration  │  50–100 тестов
                    │  (App + Infra)│ Handler + реальная БД (Testcontainers)
                    │              │  Домен + Persistence + Outbox
                    ├──────────────┤
                    │  Unit /      │  200–500 тестов
                    │  Domain      │  Чистый PHP. Без БД. Без Symfony.
                    │              │  Order, Money, OrderItem, инварианты
                    └──────────────┘

Соотношение: ~80% unit, ~15% integration, ~5% e2e
Время:       ~10 сек,      ~60 сек,         ~30 сек
```

### Почему именно так

| Слой | Что проверяем | Что НЕ проверяем |
|---|---|---|
| **Unit (Domain)** | Инварианты. Переходы состояний. Расчёты. | Хранение. Сеть. DI. |
| **Integration (App + Infra)** | Сценарий целиком. Транзакции. Outbox. Конкурентность. | HTTP. UI. Внешние API. |
| **E2E (Interfaces)** | HTTP-контракт. Сериализация. Auth. Рутинг. | Бизнес-логику (уже покрыта выше). |

---

## Уровень 1: Unit-тесты домена

### Что тестируем

- Инварианты агрегата
- Переходы состояний
- Расчёты (Money, total)
- VO (Email, OrderId, Percentage)
- Доменные исключения
- **Ничего другого**

### Как

Чистый PHP. Без Symfony. Без Doctrine. Без БД. Можно запускать в любом окружении (даже на Raspberry Pi).

```php
// tests/Unit/Domain/Order/OrderTest.php
declare(strict_types=1);

namespace App\Tests\Unit\Domain\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\EmptyOrder;
use App\Domain\Order\OrderNotPending;
use App\Domain\Order\InvalidTransition;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test_create_with_items(): void
    {
        $order = Order::create(
            customerId: 'cust-1',
            items: [
                new OrderItem('prod-1', 2, 1500),
                new OrderItem('prod-2', 1, 3000),
            ],
        );

        self::assertSame('pending', $order->status());
        self::assertSame(6000, $order->totalCents());
        self::assertSame('cust-1', $order->customerId());
        self::assertCount(2, $order->items());
    }

    public function test_create_empty_throws(): void
    {
        $this->expectException(EmptyOrder::class);
        Order::create(customerId: 'cust-1', items: []);
    }

    public function test_confirm_from_pending(): void
    {
        $order = Order::create('cust-1', [new OrderItem('p1', 1, 100)]);

        $order->confirm();

        self::assertSame('confirmed', $order->status());
        self::assertNotEmpty($order->pullDomainEvents());
    }

    public function test_confirm_from_confirmed_throws(): void
    {
        $order = Order::create('cust-1', [new OrderItem('p1', 1, 100)]);
        $order->confirm();
        $order->pullDomainEvents();  // очистить

        $this->expectException(InvalidTransition::class);
        $order->confirm();
    }

    public function test_add_item_to_confirmed_throws(): void
    {
        $order = Order::create('cust-1', [new OrderItem('p1', 1, 100)]);
        $order->confirm();

        $this->expectException(OrderNotPending::class);
        $order->addItem(new OrderItem('p2', 1, 200));
    }

    public function test_remove_last_item_throws(): void
    {
        $order = Order::create('cust-1', [new OrderItem('p1', 1, 100)]);

        $this->expectException(\App\Domain\Order\CannotRemoveLastItem::class);
        $order->removeItem('p1');
    }

    public function test_total_calculates_correctly(): void
    {
        $order = Order::create('cust-1', [
            new OrderItem('p1', 3, 100),   // 300
            new OrderItem('p2', 2, 250),   // 500
            new OrderItem('p3', 1, 50),    // 50
        ]);

        self::assertSame(850, $order->totalCents());
    }
}
```

```php
// tests/Unit/Domain/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function test_add_same_currency(): void
    {
        $result = Money::of(100, 'EUR')->add(Money::of(50, 'EUR'));
        self::assertSame(150, $result->amount());
        self::assertSame('EUR', $result->currency());
    }

    public function test_add_different_currency_throws(): void
    {
        $this->expectException(CurrencyMismatch::class);
        Money::of(100, 'EUR')->add(Money::of(50, 'USD'));
    }

    public function test_zero_is_valid(): void
    {
        $money = Money::of(0, 'EUR');
        self::assertSame(0, $money->amount());
    }

    public function test_negative_throws(): void
    {
        $this->expectException(NegativeAmount::class);
        Money::of(-1, 'EUR');
    }

    public function test_immutability(): void
    {
        $a = Money::of(100, 'EUR');
        $b = $a->add(Money::of(50, 'EUR'));

        self::assertSame(100, $a->amount());  // a не изменился
        self::assertSame(150, $b->amount());  // b — новый
        self::assertNotSame($a, $b);           // разные объекты
    }
}
```

### Ключевые принципы

| Правило | Почему |
|---|---|
| Нет `extends \KernelTestCase` | Unit-тест домена не нужен Symfony. |
| Нет `EntityManager` | Домен не знает о Doctrine. |
| Нет `new` с аргументами из конфига | Всё, что нужно — в конструкторе. |
| Один сценарий = один тест | `test_confirm_from_pending`, не `test_confirm_and_cancel_and_ship`. |
| `expectException` для негативных | Не `try/catch` в тесте. |
| Нет сетевых вызовов | Домен не ходит в сеть. Если ходит — архитектура сломана. |
| Без `sleep()` | Детерминированность. |

### Скорость

```bash
$ vendor/bin/phpunit tests/Unit/ --filter Domain
Time: 0.12 seconds, Memory: 8.50 MB
OK (342 tests, 1204 assertions)
```

342 теста за 0.12 секунды. Без БД, без Symfony. Можно запускать на каждом commit.

---

## Уровень 2: Интеграционные тесты (Application + Infrastructure)

### Что тестируем

- Полный use case: `PlaceOrderHandler::handle()` → домен → репозиторий → БД
- Транзакции: commit / rollback
- Outbox: событие сохраняется в БД атомарно с агрегатом
- Конкурентность: optimistic lock, retry
- Связь домена с persistence: `OrderEntity::fromDomain()` / `toDomain()` — без потери данных
- Валидация на уровне БД: `CHECK`, `UNIQUE`, `FK`

### Как: Testcontainers + реальная PostgreSQL

```php
// tests/Integration/TestCase/DatabaseTestCase.php
declare(strict_types=1);

namespace App\Tests\Integration\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;

abstract class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Очистка данных (не схема!)
        $this->truncateAll();
    }

    protected function tearDown(): void
    {
        $this->em->close();
        self::ensureKernelShutdown();
    }

    protected function truncateAll(): void
    {
        $conn = $this->em->getConnection();
        $conn->executeStatement('TRUNCATE outbox, order_items, orders, stock, users CASCADE');
    }

    /**
     * Вспомогательный: создать фикстуру напрямую в БД.
     */
    protected function createUser(string $id, string $email): void
    {
        $this->em->getConnection()->executeStatement(
            'INSERT INTO users (id, email, created_at) VALUES (?, ?, NOW())',
            [$id, $email],
        );
    }
}
```

**Важно:** не `setUp: create schema`. Не `tearDown: drop schema`. **`TRUNCATE`** между тестами. Схема создаётся один раз (миграции) и не меняется.

### Testcontainers в CI

```yaml
# .github/workflows/test.yml
name: Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_pgsql, redis
          coverage: none
      - run: composer install --no-interaction
      - run: bin/console doctrine:migrations:migrate --env=test
        env:
          DATABASE_URL: 'postgresql://test:test@localhost:5432/test'
      - run: vendor/bin/phpunit tests/Unit/ tests/Integration/ --testdox
        env:
          APP_ENV: test
          DATABASE_URL: 'postgresql://test:test@localhost:5432/test'
          MESSENGER_TRANSPORT_DSN: 'in-memory://'
```

### Пример: PlaceOrderHandler

```php
// tests/Integration/Order/PlaceOrderHandlerTest.php
declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Application\Order\PlaceOrderCommand;
use App\Application\Order\PlaceOrderHandler;
use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderRepositoryInterface;
use App\Tests\Integration\TestCase\DatabaseTestCase;

final class PlaceOrderHandlerTest extends DatabaseTestCase
{
    private PlaceOrderHandler $handler;
    private OrderRepositoryInterface $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = static::getContainer()->get(PlaceOrderHandler::class);
        $this->repo    = static::getContainer()->get(OrderRepositoryInterface::class);
    }

    public function test_place_order_persists_order_and_items(): void
    {
        $this->createUser('cust-1', 'a@b.com');

        $cmd = new PlaceOrderCommand(
            customerId: 'cust-1',
            items: [
                ['product_id' => 'prod-1', 'quantity' => 2, 'unit_price' => 1500],
                ['product_id' => 'prod-2', 'quantity' => 1, 'unit_price' => 3000],
            ],
        );

        $orderId = $this->handler->handle($cmd);

        // Загружаем из БД (не из памяти!)
        $loaded = $this->repo->findById($orderId);

        self::assertNotNull($loaded);
        self::assertSame('cust-1', $loaded->customerId());
        self::assertSame(6000, $loaded->totalCents());
        self::assertCount(2, $loaded->items());
        self::assertSame('pending', $loaded->status());
    }

    public function test_place_order_writes_to_outbox(): void
    {
        $this->createUser('cust-1', 'a@b.com');

        $cmd = new PlaceOrderCommand(
            customerId: 'cust-1',
            items: [['product_id' => 'prod-1', 'quantity' => 1, 'unit_price' => 100]],
        );

        $this->handler->handle($cmd);

        // Проверяем outbox в БД
        $count = $this->em->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM outbox WHERE event_type = ?',
            ['App\Domain\Order\OrderPlaced'],
        );

        self::assertSame(1, (int) $count);
    }

    public function test_place_order_rolls_back_on_stock_failure(): void
    {
        $this->createUser('cust-1', 'a@b.com');

        // В БД нет stock для prod-1 → InsufficientStock
        $cmd = new PlaceOrderCommand(
            customerId: 'cust-1',
            items: [['product_id' => 'prod-1', 'quantity' => 5, 'unit_price' => 100]],
        );

        $this->expectException(\App\Domain\Inventory\InsufficientStock::class);
        $this->handler->handle($cmd);

        // После исключения: в БД ничего нет
        $count = $this->em->getConnection()->fetchOne('SELECT COUNT(*) FROM orders');
        self::assertSame(0, (int) $count);
    }

    public function test_order_id_is_persisted(): void
    {
        $this->createUser('cust-1', 'a@b.com');

        $cmd = new PlaceOrderCommand(
            customerId: 'cust-1',
            items: [['product_id' => 'prod-1', 'quantity' => 1, 'unit_price' => 100]],
        );

        $orderId = $this->handler->handle($cmd);

        // ID валидный UUID
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $orderId->value,
        );
    }
}
```

### Что мы **не** мокаем на этом уровне

| Компонент | Мокаем? | Почему |
|---|---|---|
| `OrderRepositoryInterface` → `DoctrineOrderRepository` | **Нет** | Это то, что мы тестируем. |
| PostgreSQL | **Нет** | Реальная БД. CHECK, UNIQUE, FK — реально. |
| `EntityManager` | **Нет** | UnitOfWork, flush, транзакции — реально. |
| `Order` (домен) | **Нет** | Это ядро. Тестируется на unit-уровне. |
| `StockRepositoryInterface` → реальная | **Нет** | Конкурентность, CHECK — реально. |
| `EventDispatcher` (outbox) | **Нет** | Outbox в БД — проверяем. |
| `StripeGateway` | **Да** | Внешний сервис. Нет Stripe в CI. |
| `MailerInterface` | **Да** | Не отправляем email в тестах. |
| `Meilisearch` | **Да** | Нет Meilisearch в CI. Или Testcontainers. |

### Пример: concurrency (optimistic lock)

```php
// tests/Integration/Inventory/ConcurrencyTest.php
final class ConcurrencyTest extends DatabaseTestCase
{
    public function test_concurrent_reserves_do_not_oversell(): void
    {
        // 10 единиц на складе
        $this->em->getConnection()->executeStatement(
            'INSERT INTO stock (sku, warehouse, quantity, reserved, version)
             VALUES (\'SKU-1\', \'A\', 10, 0, 0)'
        );

        $repo = static::getContainer()->get(StockRepositoryInterface::class);

        // 10 параллельных попыток зарезервировать по 2
        $exceptions = 0;
        $successes = 0;

        for ($i = 0; $i < 10; $i++) {
            try {
                $this->em->transactional(function () use ($repo) {
                    $repo->reserve('SKU-1', 'A', 2);
                });
                $successes++;
            } catch (\App\Domain\Inventory\InsufficientStock) {
                $exceptions++;
            } catch (OptimisticLockException) {
                $exceptions++;
            }
        }

        $stock = $repo->find('SKU-1', 'A');
        self::assertSame(5, $successes);           // 10 / 2 = 5
        self::assertSame(5, $exceptions);
        self::assertSame(10, $stock->reserved());  // 5 × 2 = 10
        self::assertSame(0, $stock->available());  // 10 - 10 = 0
    }
}
```

**Этот тест невозможно написать с mock'ами.** Mock не даст `OptimisticLockException`. Mock не знает про `version`. Только реальная БД.

---

## Уровень 3: E2E / API-тесты (Interfaces)

### Что тестируем

- HTTP-контракт: method, path, status, headers, body
- Сериализация: JSON schema, nullable, форматы дат
- Auth: 401 / 403 / voter
- Routing: параметризованные пути, query params
- Error handling: 422, 409, 500
- **Не** тестируем: бизнес-логику (уже покрыта)

### Как

```php
// tests/Api/OrderApiTest.php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Integration\TestCase\DatabaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final class OrderApiTest extends DatabaseTestCase
{
    private function get(string $uri, array $headers = []): Response
    {
        $client = static::createClient();
        return $client->request('GET', $uri, server: $headers);
    }

    private function post(string $uri, array $body, array $headers = []): Response
    {
        $client = static::createClient();
        return $client->request('POST', $uri, server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $headers['auth'] ?? '',
        ], body: json_encode($body));
    }

    public function test_place_order_returns_201_with_id(): void
    {
        $this->createUser('cust-1', 'a@b.com');
        $token = $this->createAuthToken('cust-1', ['ROLE_USER']);

        $response = $this->post('/orders', [
            'customer_id' => 'cust-1',
            'items' => [
                ['product_id' => 'prod-1', 'quantity' => 1, 'unit_price' => 100],
            ],
        ], ['auth' => "Bearer $token"]);

        self::assertSame(201, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        self::assertArrayHasKey('id', $body);
        self::assertNotEmpty($body['id']);
    }

    public function test_place_order_empty_items_returns_422(): void
    {
        $this->createUser('cust-1', 'a@b.com');
        $token = $this->createAuthToken('cust-1', ['ROLE_USER']);

        $response = $this->post('/orders', [
            'customer_id' => 'cust-1',
            'items' => [],
        ], ['auth' => "Bearer $token"]);

        self::assertSame(422, $response->getStatusCode());
    }

    public function test_place_order_unauthenticated_returns_401(): void
    {
        $response = $this->post('/orders', [
            'customer_id' => 'cust-1',
            'items' => [['product_id' => 'p1', 'quantity' => 1, 'unit_price' => 100]],
        ]);

        self::assertSame(401, $response->getStatusCode());
    }

    public function test_place_order_insufficient_stock_returns_409(): void
    {
        $this->createUser('cust-1', 'a@b.com');
        $token = $this->createAuthToken('cust-1', ['ROLE_USER']);

        // Нет stock для prod-1
        $response = $this->post('/orders', [
            'customer_id' => 'cust-1',
            'items' => [['product_id' => 'prod-1', 'quantity' => 5, 'unit_price' => 100]],
        ], ['auth' => "Bearer $token"]);

        self::assertSame(409, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        self::assertArrayHasKey('error', $body);
    }

    public function test_order_detail_includes_items(): void
    {
        $this->createUser('cust-1', 'a@b.com');
        $token = $this->createAuthToken('cust-1', ['ROLE_USER']);

        // Создаём заказ через API
        $create = $this->post('/orders', [
            'customer_id' => 'cust-1',
            'items' => [
                ['product_id' => 'p1', 'quantity' => 2, 'unit_price' => 500],
                ['product_id' => 'p2', 'quantity' => 1, 'unit_price' => 300],
            ],
        ], ['auth' => "Bearer $token"]);

        $orderId = json_decode($create->getContent(), true)['id'];

        // Читаем
        $detail = $this->get("/orders/$orderId", ['auth' => "Bearer $token"]);

        self::assertSame(200, $detail->getStatusCode());

        $body = json_decode($detail->getContent(), true);
        self::assertSame(1300, $body['total_cents']);
        self::assertCount(2, $body['items']);
        self::assertSame('pending', $body['status']);
    }
}
```

### Что мокаем на этом уровне

Только **внешние** сервисы. Всё остальное — реально.

```yaml
# config/packages/test/framework.yaml
framework:
    test: true
    secret: '%env(APP_SECRET)%'
    session:
        storage_factory_id: session.storage.factory.mock_file

# config/packages/test/messenger.yaml
framework:
    messenger:
        buses:
            default:
                middleware:
                    - 'messenger.noop'   # или in-memory
        transports:
            async:
                dsn: 'in-memory://'

# config/services_test.php (Symfony DI)
use App\Application\Payment\PaymentGatewayInterface;
use App\Tests\Mock\FakeStripeGateway;

return static function (ContainerConfigurator $c) {
    $c->services()
      ->set(PaymentGatewayInterface::class, FakeStripeGateway::class)
      ->autowire()
      ->autoconfigure();
};
```

```php
// tests/Mock/FakeStripeGateway.php
final class FakeStripeGateway implements PaymentGatewayInterface
{
    /** @var list<ChargeRequest> */
    public array $charges = [];

    public function charge(ChargeRequest $r): ChargeResult
    {
        $this->charges[] = $r;

        if ($r->amountCents === -1) {
            throw new PaymentDeclined($r->orderId);
        }

        return new ChargeResult(
            orderId:      $r->orderId,
            transactionId: 'ch_test_' . bin2hex(random_bytes(8)),
            status:       'succeeded',
        );
    }
}
```

**Принцип:** мок = **двойник внешнего мира**. Stripe, Mail, Meilisearch. Всё, что живёт **за пределами** вашего процесса. Не мок = **внутри** вашего процесса: БД, домен, application, Doctrine.

---

## Что мокаем, что нет: сводная таблица

| Компонент | Мокаем? | Почему |
|---|---|---|
| `Order` (домен) | **Нет** | Ядро. Тестируется на unit. |
| `OrderRepositoryInterface` → Doctrine | **Нет** | Persistence — часть того, что тестируем. |
| PostgreSQL | **Нет** | CHECK, UNIQUE, FK, MVCC — только реально. |
| `EntityManager`, UnitOfWork | **Нет** | flush, transaction, dirty checking — реально. |
| `StockRepository` → Doctrine | **Нет** | Concurrency, optimistic lock — реально. |
| Outbox (таблица + relay) | **Нет** | Атомарность — только с реальной БД. |
| `EventDispatcher` (Symfony) | **Нет** | Если in-memory — быстро. Если AMQP — Testcontainers. |
| `StripeGateway` → HTTP | **Да** | Нет Stripe в CI. Внешний сервис. |
| `MailerInterface` | **Da** | Не отправляем email. |
| `Meilisearch` | **Да** (или Testcontainers) | Внешний сервис. |
| `Time` (current time) | **Да** (clock) | Детерминированность. |
| `Random` (UUID, random) | **Нет** (или seed) | UUID v4 — непредсказуем. Если нужно — inject. |
| `Filesystem` | **Да** (temp dir) | Изоляция. |

### Правило одного слова

> **Мокаем границу процесса. Не мокаем границу слоя.**

Граница процесса = внешний мир (Stripe, Mail, Search). Граница слоя = БД, домен, application. Всё, что внутри процесса — реально.

---

## Скорость: как держать тесты быстрыми

### 1. Unit-тесты: 0 мс на тест

```bash
$ vendor/bin/phpunit tests/Unit/
Time: 0.12 seconds
OK (342 tests, 1204 assertions)
```

Нет БД. Нет Symfony. Нет сети. Можно запускать на каждом `git commit` (pre-commit hook).

### 2. Integration: `TRUNCATE`, не `DROP/CREATE`

```php
// ПЛОХО: 5 сек на тест (drop + create schema)
protected function setUp(): void
{
    $this->em->getConnection()->executeStatement('DROP SCHEMA public CASCADE');
    $this->em->getConnection()->executeStatement('CREATE SCHEMA public');
    $this->runMigrations();  // 3 сек
}

// ХОРОШО: 50 мс на тест (TRUNCATE)
protected function setUp(): void
{
    $this->em->getConnection()->executeStatement(
        'TRUNCATE outbox, order_items, orders, stock, users CASCADE'
    );
}
```

Схема создаётся **один раз** (миграции). Тесты только очищают данные.

### 3. In-memory messenger в тестах

```yaml
# config/packages/test/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'in-memory://'   # нет Redis, нет AMQP
```

События обрабатываются синхронно. Нет ожидания. Нет брокера.

### 4. Нет сетевых вызовов

| Что | Вместо |
|---|---|
| Stripe API | `FakeStripeGateway` |
| Mail (SMTP) | `InMemoryMailer` (Symfony) |
| Meilisearch | Mock или Testcontainers |
| Внешний HTTP | `Symfony\Contracts\HttpClient` mock |

### 5. Параллельный запуск

```bash
# PHPUnit 10+: параллельные процессы
vendor/bin/phpunit tests/Integration/ --processes 4

# Каждый процесс — отдельная БД (или schema)
# DATABASE_URL: postgresql://test:test@localhost:5432/test_0
# DATABASE_URL: postgresql://test:test@localhost:5432/test_1
# ...
```

Или `paratest`:

```bash
vendor/bin/paratest --processes 4 tests/
```

### 6. Без `sleep()`

```php
// ПЛОХО:
$repo->save($order);
sleep(1);  // «подождём, пока flush завершится»
$result = $repo->findById($id);

// ХОРОШО:
$repo->save($order);
$em->flush();  // синхронно. Завершилось.
$result = $repo->findById($id);
```

Если нужен `sleep` — архитектура неправильная. Асинхронность — в отдельном процессе (worker), не в тесте.

### 7. Тестовый набор: целевое время

| Набор | Целевое время | Команда |
|---|---|---|
| Unit (Domain) | < 1 сек | `vendor/bin/phpunit tests/Unit/` |
| Integration | < 2 мин | `vendor/bin/phpunit tests/Integration/` |
| E2E / API | < 1 мин | `vendor/bin/phpunit tests/Api/` |
| **Всё** | **< 5 мин** | `vendor/bin/phpunit` |

Если Integration > 5 мин — что-то не так. Сеть? `sleep()`? `DROP SCHEMA`? Внешний сервис?

---

## Стабильность: как убрать flaky tests

### 1. Детерминированные даты

```php
// ПЛОХО:
$order->createdAt  // current time. Завтрашний тест — другая дата.

// ХОРОШО:
final class Order
{
    public static function create(string $customerId, array $items, ?\DateTimeImmutable $now = null): self
    {
        // ...
        $o->createdAt = $now ?? new \DateTimeImmutable();
        return $o;
    }
}

// В тесте:
$order = Order::create('cust-1', $items, new \DateTimeImmutable('2024-01-15 10:00:00'));
```

Или через DI:

```php
// config/services_test.php
$c->services()
  ->set('clock', class: class_exists(FakeClock::class) ? FakeClock::class : SystemClock::class)
  ->autowire();
```

```php
final class FakeClock
{
    private \DateTimeImmutable $now;

    public function __construct(string $datetime = '2024-01-15 10:00:00')
    {
        $this->now = new \DateTimeImmutable($datetime);
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    public function advance(int $seconds): void
    {
        $this->now = $this->now->modify("+$seconds seconds");
    }
}
```

### 2. Детерминированные ID

```php
// ПЛОХО:
$orderId = OrderId::generate();  // UUID v4 — непредсказуем
// Тест не может знать, какой ID будет.

// ХОРОШО:
// Вариант A: Order::create принимает OrderId
$order = Order::createWithId(OrderId::from('test-fixed-id'), 'cust-1', $items);

// Вариант B: после создания читаем ID
$orderId = $order->id();
self::assertNotNull($orderId);
// Не assertSame('expected-uuid', $orderId->value) — а проверять свойства.
```

### 3. Изоляция данных

```php
// ПЛОХО: тест A создаёт данные, тест B их видит
// (если TRUNCATE не вызван, или тесты идут в одном процессе)

// ХОРОШО: каждый тест — чистое начало
protected function setUp(): void
{
    parent::setUp();
    $this->truncateAll();  // ВСЕГДА. Каждый тест.
}
```

### 4. Нет зависимости от порядка

```php
// ПЛОХО:
public function test_1_create_order(): void { ... }
public function test_2_confirm_order(): void { ... }  // зависит от test_1!

// ХОРОШО: каждый тест самодостаточен
public function test_confirm_order(): void
{
    $this->createUser('cust-1', 'a@b.com');
    $orderId = $this->createOrder('cust-1', ...);
    $this->confirmOrder($orderId);
    // ...
}
```

### 5. Нет зависимости от внешнего мира

```php
// ПЛОХО:
public function test_stripe_charge(): void
{
    $gateway = new StripeGateway($this->http, 'sk_live_...');
    $result = $gateway->charge($request);
    // Реальный Stripe. Реальные деньги. Flaky.
}

// ХОРОШО:
public function test_stripe_charge(): void
{
    $fake = new FakeStripeGateway();
    $result = $fake->charge($request);
    self::assertSame('succeeded', $result->status());
}
```

### 6. Конкурентность — детерминированная

```php
// ПЛОХО: race condition. Идёт 10 раз, 9 раз падает, 1 раз проходит.
public function test_concurrent(): void
{
    // 10 параллельных запросов. Результат зависит от тайминга.
}

// ХОРОШО: контролируемая конкуренция
public function test_concurrent_reserves(): void
{
    // 10 последовательных транзакций.
    // Каждая пытается зарезервировать.
    // Оптимистичный lock детерминированно срабатывает.
    // Результат: 5 успех, 5 fail. Всегда.
}
```

Для **настоящей** параллельности — отдельный набор тестов, которые запускаются редко (nightly):

```yaml
# .github/workflows/nightly.yml
name: Concurrency Tests
on:
  schedule:
    - cron: '0 3 * * *'  # 3 AM
  workflow_dispatch: {}

jobs:
  concurrency:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        # ...
    steps:
      - run: vendor/bin/phpunit tests/Concurrency/ --processes 10
```

---

## Структура тестов

```
tests/
├── Unit/
│   └── Domain/
│       ├── Order/
│       │   ├── OrderTest.php
│       │   └── OrderItemTest.php
│       ├── MoneyTest.php
│       ├── EmailTest.php
│       └── OrderIdTest.php
│
├── Integration/
│   ├── Order/
│   │   ├── PlaceOrderHandlerTest.php
│   │   ├── ConfirmOrderHandlerTest.php
│   │   └── CancelOrderHandlerTest.php
│   ├── Inventory/
│   │   ├── ReserveStockTest.php
│   │   └── ConcurrencyTest.php
│   ├── Outbox/
│   │   └── OutboxAtomicityTest.php
│   └── Shared/
│       └── DatabaseTestCase.php
│
├── Api/
│   ├── OrderApiTest.php
│   ├── InventoryApiTest.php
│   └── AuthApiTest.php
│
├── Mock/
│   ├── FakeStripeGateway.php
│   ├── InMemoryMailer.php
│   └── FakeClock.php
│
└── Architecture/
    └── LayerBoundaryTest.php
```

**Правило имён:**

| Тест | Имя |
|---|---|
| `test_create_order_with_items` | Что проверяем |
| `test_create_order_empty_throws` | Негативный сценарий |
| `test_confirm_from_confirmed_throws` | Граничное состояние |
| `test_concurrent_reserves_do_not_oversell` | Конкурентность |

Не `test_1`, `test_2`. Не `test_something`. Не `test_check`.

---

## CI pipeline

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]

jobs:
  # 1. Unit (быстро, каждый commit)
  unit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - run: composer install
      - run: vendor/bin/phpstan analyse --level 8 --memory-limit 512M
      - run: vendor/bin/phpunit tests/Unit/ --testdox
      - run: vendor/bin/phpunit tests/Architecture/

  # 2. Integration + E2E (медленнее, каждый PR)
  integration:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_pgsql
      - run: composer install
      - run: bin/console doctrine:migrations:migrate --env=test
        env:
          DATABASE_URL: 'postgresql://test:test@localhost:5432/test'
      - run: vendor/bin/phpunit tests/Integration/ tests/Api/ --testdox
        env:
          APP_ENV: test
          DATABASE_URL: 'postgresql://test:test@localhost:5432/test'
          MESSENGER_TRANSPORT_DSN: 'in-memory://'

  # 3. Concurrency (ночь)
  concurrency:
    if: github.event_name == 'schedule' || github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        # ...
    steps:
      - run: vendor/bin/phpunit tests/Concurrency/ --processes 5
```

---

## Антипаттерны

### 1. Mock репозитория

```php
// АНТИПАТТЕРН:
$repo = $this->createMock(OrderRepositoryInterface::class);
$repo->method('save')->willReturn(null);
$repo->method('findById')->willReturn($order);

$handler->handle($cmd);

// Тест проходит. Но:
// - Не проверена SQL-инъекция.
// - Не проверен CHECK constraint.
// - Не проверен UNIQUE.
// - Не проверен rollback.
// - Не проверен outbox.
// Тест говорит «код не упал». Не говорит «данные правильные».
```

**Правило:** репозиторий — **не мокаем**. Он часть того, что тестируем. Мокаем только **внешний мир**.

### 2. Тестирование реализации

```php
// АНТИПАТТЕРН:
public function test_save_calls_em_persist(): void
{
    $em = $this->createMock(EntityManagerInterface::class);
    $em->expects($this->once())->method('persist');
    // ...
}

// Тест ломается, если мы поменяем persist на merge.
// Но поведение не изменилось.
// Это тест реализации, не поведения.
```

### 3. `sleep()` в тестах

```php
// АНТИПАТТЕРН:
$handler->handle($cmd);
sleep(2);  // «подождём, пока async обработается»
$result = $repo->findById($id);

// Flaky. На медленном CI — не успеет. На быстром — лишние 2 сек × 50 тестов.
```

### 4. Тест, который зависит от времени

```php
// АНТИПАТТЕРН:
public function test_order_expires_in_24h(): void
{
    $order = Order::create(...);
    $order->createdAt = new \DateTimeImmutable();  // NOW

    // Через 24 часа (в реальном времени) — expired.
    // Тест сегодня проходит. Завтра — тоже.
    // Но: если тест запускается в 23:59 — «24 часа» = завтра.
    // Флуктуация на границе суток.
}

// ХОРОШО:
$order = Order::create(..., new \DateTimeImmutable('2024-01-15 10:00:00'));
self::assertFalse($order->isExpired('2024-01-15 23:00:00'));
self::assertTrue($order->isExpired('2024-01-16 11:00:00'));
```

### 5. Один тест на 10 сценариев

```php
// АНТИПАТТЕРН:
public function test_order(): void
{
    $this->testCreate();
    $this->testConfirm();
    $this->testCancel();
    $this->testAddItem();
    $this->testRemoveItem();
    $this->testTotal();
    $this->testEvents();
    $this->testInvalidTransition();
    $this->testEmptyOrder();
    $this->testConcurrency();
    // 10 сценариев. Если третий падает — четвёртый не запускается.
    // Не видно, какой именно сломался.
}
```

### 6. Тест «через» контроллер

```php
// АНТИПАТТЕРН: для unit-теста домена
$client = static::createClient();
$client->request('POST', '/orders', ...);
// 200 OK

// Это E2E. Не unit. Не unit домена.
// Unit домена: Order::create(...). assert. Конец.
```

---

## Итого

```
┌─────────────────────────────────────────────────────────────────────┐
│  СТРАТЕГИЯ                                                          │
│                                                                     │
│  Unit (80%)          Integration (15%)      E2E (5%)               │
│  Домен               App + Infra            Interfaces             │
│  Чистый PHP          Реальная БД            HTTP + Symfony         │
│  < 1 сек             < 2 мин                < 1 мин                │
│  Инварианты          Сценарии, конкурент.   Контракт, auth         │
│  Переходы            Транзакции, outbox     Сериализация           │
│  Расчёты             CHECK, UNIQUE, FK      401/403/422/409        │
│                                                                     │
│  МОКАЕМ:                                                        │
│  • Stripe, Mail, Meilisearch (внешний мир)                       │
│  • Clock (детерминированность)                                   │
│  • Filesystem (изоляция)                                         │
│                                                                     │
│  НЕ МОКАЕМ:                                                     │
│  • PostgreSQL (CHECK, UNIQUE, FK, MVCC)                          │
│  • Doctrine (UnitOfWork, flush, transaction)                      │
│  • Домен (инварианты)                                            │
│  • Application (оркестрация)                                     │
│  • Outbox (атомарность)                                          │
│                                                                     │
│  СКОРОСТЬ:                                                        │
│  • TRUNCATE, не DROP/CREATE                                       │
│  • In-memory messenger                                            │
│  • Нет сети (mocks внешних сервисов)                              │
│  • Нет sleep()                                                    │
│  • Параллельный запуск (--processes 4)                            │
│                                                                     │
│  СТАБИЛЬНОСТЬ:                                                    │
│  • Детерминированные даты (FakeClock)                             │
│  • Изоляция данных (TRUNCATE per test)                            │
│  • Самодостаточные тесты (не зависят от порядка)                  │
│  • Нет внешнего мира (mocks)                                      │
│  • Конкурентность — детерминированная (nightly)                   │
│                                                                     │
│  ЦЕЛЬ: 5 минут на весь набор. 0 flaky. 100% покрытие домена.      │
└─────────────────────────────────────────────────────────────────────┘
```

> ⏱ 93.8s · prompt 110659 tok · output 11263 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о concurrent writes на одну запись в БД. Это продолжение длинной беседы о DDD, Doctrine, PostgreSQL. Нужно:

1. Описать проблемы (lost update, write skew, dirty read, phantom read, deadlock)
2. Перечислить способы защиты (pessimistic lock, optimistic lock, serializable isolation, WHERE guard, idempotency, application-level patterns)
3. Сравнить подходы
4. Дать практические рекомендации когда что использовать

Я уже частично затрагивал эту тему в ответе про агрегаты и конкурентность (optimistic lock, pessimistic lock, serializable). Но там фокус был на агрегатах DDD. Здесь вопрос шире — "два пользователя одновременно изменяют одну запись". Нужно дать более общий, практичный ответ, не дублируя предыдущий, а расширяя.

Структура:
1. Какие проблемы (конкретные сценарии с кодом)
2. Способы защиты (каждый с кодом, trade-offs)
3. Сравнительная таблица
4. Когда что выбираю (практические сценарии)

Не буду дублировать подробно то, что уже сказал про optimistic/pessimistic/serializable. Дам более компактное сравнение и добавлю то, чего не было: application-level patterns (versioned updates, if-then conditions, application-level queues), и конкретные сценарии из реального PHP/Symfony кода.

Отвечу на русском, как вся беседа.

</details>

**Кандидат:**

## Какие проблемы возникают

### 1. Lost update (самая частая)

Два процесса читают одно значение, оба пишут. Одно изменение теряется.

```
t=0ms  User A:  SELECT balance FROM accounts WHERE id = 1;  → 1000
t=0ms  User B:  SELECT balance FROM accounts WHERE id = 1;  → 1000
t=1ms  User A:  UPDATE accounts SET balance = 1500 WHERE id = 1;  (1000 + 500)
t=1ms  User B:  UPDATE accounts SET balance = 1200 WHERE id = 1;  (1000 + 200)
t=2ms  Оба COMMIT.

Результат: balance = 1200. Ожидание: 1700.
500 из User A потеряны. Без ошибки. Без лога.
```

### 2. Write skew (скрытая потеря согласованности)

Оба пишут в **разные** строки, но нарушают **общий** инвариант.

```
Правило: в отделе не более 10 активных сотрудников.
Сейчас: 9.

t=0  User A: SELECT COUNT(*) FROM employees WHERE dept = 'X' AND active = true;  → 9
t=0  User B: SELECT COUNT(*) FROM employees WHERE dept = 'X' AND active = true;  → 9
t=1  User A: INSERT employee (dept='X', active=true);  → 10. OK.
t=1  User B: INSERT employee (dept='X', active=true);  → 11. НОРМ! (по его чтению)
t=2  Оба COMMIT.

Результат: 11 сотрудников. Лимит 10. Нарушение.
READ COMMITTED не детектирует. Сериализация — да.
```

### 3. Dirty read

Один читает несохранённые данные другого. В PostgreSQL по умолчанию (`READ COMMITTED`) **не происходит** — каждый видит только committed данные. Но в `READ UNCOMMITTED` (не поддерживается PG) или при некорректном использовании `REPEATABLE READ` + `FOR UPDATE SKIP LOCKED` — может.

### 4. Deadlock

```
t=0  User A: UPDATE orders SET ... WHERE id = 1;  (lock на строку 1)
t=0  User B: UPDATE orders SET ... WHERE id = 2;  (lock на строку 2)
t=1  User A: UPDATE orders SET ... WHERE id = 2;  (ждёт lock B)
t=1  User B: UPDATE orders SET ... WHERE id = 1;  (ждёт lock A)
→ DEADLOCK. PostgreSQL детектирует, откатывает одного. 40P01.
```

### 5. Application-level race (read-then-decide-then-write)

```php
// "Можно ли удалить?"
$count = $repo->countReferences($id);
if ($count === 0) {
    // Между SELECT и DELETE кто-то создал ссылку!
    $repo->delete($id);
}
```

Нет `SELECT FOR UPDATE` → между проверкой и удалением — окно. Кто-то успевает создать ссылку. `FK` заблокирует delete — но только если `ON DELETE RESTRICT`. Если `ON DELETE CASCADE` — **тихое удаление** связанных данных.

---

## Способы защиты

### 1. Атомарный `UPDATE ... WHERE` (самый простой и надёжный)

Вместо read-then-write — **один** SQL-вызов с условием.

```sql
-- Вместо:
SELECT balance FROM accounts WHERE id = 1;
-- если balance >= 500:
UPDATE accounts SET balance = balance - 500 WHERE id = 1;

-- Делаем:
UPDATE accounts
SET balance = balance - 500
WHERE id = 1
  AND balance >= 500;
-- 0 rows → недостаточно средств. 1 row → успех.
```

```php
// PHP
$stmt = $conn->executeStatement(
    'UPDATE accounts SET balance = balance - :amount
     WHERE id = :id AND balance >= :amount',
    ['amount' => 500, 'id' => 1],
);

if ($stmt === 0) {
    throw new InsufficientFunds();
}
```

**Почему работает:** PostgreSQL атомарно читает текущее значение, вычисляет, пишет, проверяет условие — в одной операции. Нет read-then-write. Нет lost update.

**Когда:** counter, balance, stock decrement, increment. Всё, где операция — `col = col ± N` + условие.

**Ограничение:** не выражает сложную бизнес-логику (расчёт скидки, валидация по 5 полям). Для этого — другие способы.

---

### 2. Optimistic Locking (version column)

```sql
CREATE TABLE orders (
    id UUID PRIMARY KEY,
    status TEXT NOT NULL,
    total INT NOT NULL,
    version INT NOT NULL DEFAULT 0
);
```

```sql
UPDATE orders
SET status = 'confirmed', total = 5000, version = version + 1
WHERE id = '42'
  AND version = 3;
-- 0 rows → кто-то изменил. RETRY или ошибка.
```

```php
// В PHP
try {
    $em->transactional(function () use ($order) {
        $order->confirm();
        $em->flush();  // UPDATE ... WHERE version = ?
    });
} catch (OptimisticLockException) {
    // Перечитать, пересчитать, повторить
    $order = $repo->find($id);  // fresh
    $order->confirm();
    $em->flush();
}
```

**Когда:** агрегаты с сложной бизнес-логикой. Несколько полей. Условие не выражается в одном `WHERE`.

**Trade-off:** retry loop. При высокой конкуренции — много retry'ов.

---

### 3. Pessimistic Locking (`SELECT FOR UPDATE`)

```sql
BEGIN;
SELECT status, total, version
FROM orders WHERE id = '42'
FOR UPDATE;

-- Здесь: бизнес-логика, вычисления.
-- Другие процессы на этой строке ЖДУТ.

UPDATE orders SET status = 'confirmed', total = 5000 WHERE id = '42';
COMMIT;  -- lock освобождён
```

```php
// Doctrine
$qb = $em->createQueryBuilder()
    ->select('o')
    ->from(OrderEntity::class, 'o')
    ->where('o.id = :id')
    ->setParameter('id', $id)
    ->setLockMode(LockMode::OPTIMISTIC);
// или PessimisticWrite::class для FOR UPDATE
```

**Когда:**
- Короткая операция (< 100 мс).
- Нет внешних HTTP-вызовов внутри транзакции.
- Конкурентность умеренная (1–10 параллельных).
- Критично «только один может».

**Против:** блокирует. 100 параллельных × 30 сек (HTTP) = очередь. Deadlock risk.

---

### 4. Serializable Isolation

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

PostgreSQL (SSI) детектирует **write skew** и бросает `40001 serialization_failure`. Вы откатываете и ретраите.

**Когда:**
- Финансовый учёт.
- Лимиты, квоты, аллокации.
- Низкая конкуренция (< 10 TPS).
- Готовность к retry.

**Против:** overhead на каждый write. Много aborts при высокой конкуренции.

---

### 5. Application-level: очередь / single-writer

Один процесс пишет. Остальные кладут в очередь.

```
User A → Queue → Worker (единственный писатель) → UPDATE
User B → Queue → Worker → UPDATE
User C → Queue → Worker → UPDATE

Нет параллельных записей. Нет конфликтов.
```

**Когда:**
- Высокая конкуренция на **одну** запись (1000 TPS на один SKU).
- Сложная бизнес-логика.
- Можно допустить задержку (async).

**Против:** задержка. Очередь — дополнительная инфраструктура. Ordering гарантирован (FIFO), но latency растёт.

---

### 6. `WHERE` guard + `CHECK` constraint (защита на уровне БД)

```sql
-- Инвариант в BД:
CHECK (reserved >= 0 AND reserved <= quantity)

-- Атомарный update с guard:
UPDATE stock
SET reserved = reserved + :units
WHERE sku = :sku
  AND reserved + :units <= quantity
  AND version = :version;

-- 0 rows → не хватило / версия изменилась.
```

Даже если app-код багнул, БД не даст нарушить инвариант. Это **последний рубеж**.

---

## Сравнение

| Критерий | Атомарный UPDATE | Optimistic | Pessimistic | Serializable | Queue |
|---|---|---|---|---|---|
| **Lost update** | Нет | Нет (retry) | Нет | Нет | Нет |
| **Write skew** | Частично (WHERE) | Частично | Нет | **Да** | Нет |
| **Deadlock** | Нет | Нет | **Да** | Да (40P01) | Нет |
| **Блокировки** | Кратковременные | Нет | Длительные | Кратковременные | Нет |
| **Throughput** | Высокий | Средний | Низкий | Средний | Высокий (async) |
| **Latency** | Низкая | Низкая + retry | Высокая (ждёт) | Низкая + abort | Высокая (очередь) |
| **Сложность кода** | Минимальная | Средняя (retry) | Низкая | Низкая + retry | Высокая (инфра) |
| **Выражает бизнес-логику** | Ограниченно | Да | Да | Да | Да |
| **Нужна ли БД** | Да | Да | Да | Да | Да (+ broker) |
| **Идемпотентность** | Встроена | Нужна | Нужна | Нужна | Встроена (FIFO) |

---

## Когда что выбираю

### Сценарий 1: «Пользователь вносит 500 ₽ на счёт»

```php
// Атомарный UPDATE. Окончательно.
$stmt = $conn->executeStatement(
    'UPDATE accounts SET balance = balance + :amount
     WHERE id = :id',
    ['amount' => 500, 'id' => $accountId],
);
```

Почему: операция — `col = col + N`. Нет сложной логики. Атомарно. Без version, без lock, без retry.

### Сценарий 2: «Пользователь подтверждает заказ»

```php
// Optimistic lock. Агрегат с инвариантами.
$em->transactional(function () {
    $order = $repo->find($id);          // SELECT (без FOR UPDATE)
    $order->confirm();                   // доменная валидация
    $em->flush();                        // UPDATE ... WHERE version = ?
});
// catch OptimisticLockException → retry
```

Почему: несколько полей. Инварианты в домене. Не выражается в одном `WHERE`. Retry допустим (конфликт редок: два пользователя подтверждают один заказ — маловероятно).

### Сценарий 3: «Забронировать последнюю комнату на 15:00–17:00»

```sql
-- EXCLUDE constraint (Postgres-specific)
EXCLUDE USING gist (
    room_id WITH =,
    daterange(check_in, check_out) WITH &&
)
```

Или:

```php
// Pessimistic lock + WHERE guard
$em->transactional(function () {
    $room = $repo->lock($roomId);  // SELECT FOR UPDATE

    // Проверить пересечение
    $conflict = $repo->findConflicting($roomId, $start, $end);
    if ($conflict !== null) {
        throw new RoomNotAvailable();
    }

    $booking = new Booking($roomId, $start, $end);
    $repo->save($booking);
    // UPDATE + INSERT в одной транзакции. Lock на room.
});
```

Почему: «последняя комната» = критичное «только один». Короткая операция. Pessimistic lock оправдан. `EXCLUDE` — ещё лучше, если Postgres.

### Сценарий 4: «В отделе не более 10 сотрудников»

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;

BEGIN;
SELECT COUNT(*) FROM employees WHERE dept = 'X' AND active = true;
-- если < 10:
INSERT INTO employees (dept, active) VALUES ('X', true);
COMMIT;

-- Если write skew → 40001 → ROLLBACK → RETRY
```

Почему: write skew. READ COMMITTED не детектирует. Только Serializable. Конкуренция низкая (не 1000 TPS на один отдел). Retry допустим.

### Сценарий 5: «1000 пользователей покупают один товар (SKU-1, 100 шт.)»

```php
// Queue / single-writer
queue->push(new ReserveStock('SKU-1', 1));

// Worker (один процесс):
while ($msg = queue->pop()) {
    $stmt = $conn->executeStatement(
        'UPDATE stock SET reserved = reserved + 1
         WHERE sku = :sku AND reserved < quantity',
        ['sku' => $msg->sku],
    );
    if ($stmt === 0) {
        queue->push(new NotifySoldOut($msg->sku));
    }
}
```

Почему: 1000 конкурентов. Pessimistic lock = очередь на 1000 × lock time. Optimistic = 500+ retry'ов. Queue = последовательная обработка, zero конфликтов. Latency растёт, но система не падает.

### Сценарий 6: «Два админа редактируют один пост»

```php
// Optimistic lock (version)
$em->transactional(function () {
    $post = $repo->find($id);
    $post->setTitle($newTitle);
    $post->setBody($newBody);
    $em->flush();  // UPDATE ... WHERE version = ?
});

// Если конфликт:
catch (OptimisticLockException) {
    // 409 Conflict. UI: «Пост был изменён другим пользователем. Перезагрузите.»
    throw new ConflictException('Post modified by another user');
}
```

Почему: конфликты редкие (два админа в один момент). Retry неуместен (данные пользователя потеряны). 409 — честный ответ.

---

## Комбинации

В реальном коде это не «один способ». Это **набор**:

```
1. Атомарный UPDATE (для простых операций)
   + WHERE guard (инвариант в SQL)
   + CHECK constraint (последний рубеж в БД)

2. Optimistic lock (для агрегатов с бизнес-логикой)
   + Retry loop (exponential backoff, max 3–5)
   + 409 Conflict (если retry исчерпан)

3. Pessimistic lock (для «только один может»)
   + Короткая транзакция (нет HTTP внутри)
   + lock_timeout (защита от вечного ожидания)

4. Serializable (для write skew)
   + Retry (40001)
   + Idempotency (дубли при retry)

5. Queue (для высокой конкуренции)
   + Idempotency (дубли delivery)
   + DLQ (ошибки)
   + Monitoring (задержки в очереди)
```

---

## Принципы, которые я соблюдаю

**1. Минимальная транзакция.**

```php
// ПЛОХО:
$em->transactional(function () {
    $stock = $repo->lock($sku);       // lock
    $stripe->charge($amount);          // HTTP: 5–30 сек. LOCK ДЕРЖИТСЯ!
    $em->flush();
});

// ХОРОШО:
$em->transactional(function () {
    $stock = $repo->lock($sku);
    $em->flush();                      // COMMIT. Lock освобождён.
});
$stripe->charge($amount);              // вне транзакции.
```

**2. Инвариант в `WHERE`, не только в PHP.**

```php
// PHP:
if ($stock->reserved + $units > $stock->quantity) { throw ... }
$stock->reserved += $units;
$em->flush();

// БД (страховка):
UPDATE stock SET reserved = reserved + :u
WHERE sku = :s AND reserved + :u <= quantity;
// 0 rows → throw
```

**3. Idempotency при retry.**

```php
// Каждый retry может дублировать. Защита:
INSERT INTO payments (id, order_id, amount)
VALUES ('pay-123', 'order-42', 5000)
ON CONFLICT (id) DO NOTHING;
```

**4. `lock_timeout` на всё, что блокирует.**

```sql
SET lock_timeout = '5s';
-- Если не удалось получить lock за 5 сек → error. Не deadlock.
```

**5. Мониторинг конфликтов.**

```
Метрики:
  - optimistic_lock_retries (count, p99)
  - deadlock_count (из pg_stat_database)
  - serialization_aborts (40001)
  - lock_wait_time (p99)

Алерты:
  - optimistic_lock_retries > 10/min → P2
  - deadlock_count > 0 → P1
  - lock_wait_time > 5s → P2
```

---

## Итого

```
Простая операция (col = col ± N):
  → Атомарный UPDATE + WHERE guard. Конец.

Агрегат с бизнес-логикой:
  → Optimistic lock + retry + 409.

«Только один может» (короткая операция):
  → Pessimistic lock + lock_timeout.

Write skew (лимиты, квоты):
  → Serializable + retry + idempotency.

Высокая конкуренция на одну запись:
  → Queue / single-writer + idempotency.

ВСЕГДА:
  + CHECK constraint в БД (последний рубеж)
  + lock_timeout
  + Idempotency (при retry)
  + Мониторинг (retries, deadlocks, lock wait)
  + Минимальная транзакция (нет HTTP внутри lock)
```

Нет «правильного» способа. Есть **характер операции**: простая/сложная, конкурентная/неконкурентная, синхронная/асинхронная, критичная/допустимая задержка. От этого — выбор.

> ⏱ 50.2s · prompt 121519 tok · output 4860 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

The user is asking me to describe my git workflow from task assignment to production: branching, commit format, pull requests, code review, releases. And explain why I choose what I choose.

This is a continuation of a long conversation about PHP/Symfony development, DDD, clean architecture, etc. The user has been asking deep technical questions and I've been giving structured, detailed answers with code examples, tables, and practical guidance.

Let me think about what makes a good answer here:

1. The user wants a COMPLETE process - from task to production
2. They want the "why" - not just "what" but the reasoning
3. They want it to be practical, not theoretical
4. Given the conversation context, this person is a senior PHP developer working with Symfony, PostgreSQL, DDD
5. They've been asking about real-world trade-offs, not textbook answers

Let me structure this as:
- Branching strategy (and why)
- Commit format (and why)
- PR process (and why)
- Code review (and why)
- Release process (and why)

I should be opinionated where appropriate, acknowledge trade-offs, and give concrete examples. I should avoid being a generic "git flow" answer and instead give a practical, opinionated process that reflects real senior-level practice.

Key opinions I want to express:
- Trunk-based development over git-flow (and why)
- Feature branches are short-lived (not long-lived)
- Conventional commits but not religious about it
- PRs are for humans, not for CI
- Code review is about the "why", not the "what"
- Releases are about deployment, not about code
- Hotfixes are a smell if they happen often
- The process should minimize WIP, maximize feedback speed

Let me write this in Russian since the conversation is in Russian.

I'll structure it as a narrative flow: task → branch → develop → commit → PR → review → merge → CI → deploy → production. With tables, code examples, and clear "why" explanations throughout.

</details>

**Кандидат:**

## Принцип, на котором строится процесс

> **Минимизировать время от «я изменил код» до «это в проде». Минимизировать время от «я хочу понять, что изменилось» до «я понял».**

Всё остальное — следствия.

---

## 1. От задачи к ветке

### Что происходит

```
Jira/Linear: "Order: добавить отмену заказа после подтверждения"
    │
    ▼
1. Создать ветку:    feature/order-cancel-after-confirm
2. Запушить:         git push -u origin feature/order-cancel-after-confirm
3. Открыть PR:       сразу, до первой строки кода (draft)
4. В PR описать:     что, зачем, какой сценарий, какой edge case
5. Написать тест:    RED (тест падает)
6. Написать код:     GREEN (тест проходит)
7. Рефакторинг:      зелёный, чистый
8. Squash + merge
```

### Имя ветки

```
feature/order-cancel-after-confirm
fix/stripe-webhook-idempotency
chore/upgrade-doctrine-3
docs/update-api-spec
```

**Правило:** `<тип>/<короткое-описание-в-кебаках>`.

| Тип | Когда |
|---|---|
| `feature/` | Новое поведение, которого раньше не было |
| `fix/` | Исправление некорректного поведения |
| `chore/` | Зависимости, конфиг, CI, инфраструктура |
| `docs/` | Документация |
| `refactor/` | Изменение структуры без изменения поведения |
| `test/` | Добавление/изменение тестов |

**Почему не `JIRA-1234`:** Имя ветки должно быть **человеческим**. `JIRA-1234` не говорит, что внутри. `feature/order-cancel` — говорит. Номер задачи — в PR.

**Почему не `master`-ветки / `release/1.0` / `hotfix/urgent`:** Об этом ниже.

---

## 2. Ветвление: trunk-based, не git-flow

### Что я использую

```
main ──────────────────────────────────────────────────→ PROD
  │
  ├── feature/order-cancel ──┐
  ├── fix/stripe-idem ───────┤  → PR → squash merge → main
  ├── chore/upgrade-php ─────┤
  └── feature/stock-limit ───┘
```

Одна долгоживущая ветка: `main`. Все фичи — короткие ветки (1–3 дня), которые мержатся в `main`.

### Чего я **не** использую

| Стратегия | Почему нет |
|---|---|
| **Git Flow** (`develop`, `release/*`, `hotfix/*`) | 4 ветки, которые нужно синхронизировать. `develop` отстаёт от `main` на 2 недели → merge conflict на 200 файлов. Release-ветки живут 3 недели → дублирование фиксов. |
| **GitHub Flow** (только `main` + `feature/*`) | Почти то же, что и trunk-based. Разница: `main` = prod. Я согласен, но добавляю staging-деплой через tag. |
| **Long-lived feature branches** (2+ недели) | Чем дольше ветка живёт, тем больше drift. Merge conflict. Устаревший контекст. Тесты ломаются на `main`. |
| **`release/1.0` на 3 недели** | В это время 5 фиксов в `main` → их нужно backport в `release` → дублирование → баги. |

### Почему trunk-based

**1. Short feedback loop.**

```
Git Flow:
  Пишу в feature/ → merge в develop → CI на develop → deploy в staging → тестирование → merge в main → CI → deploy в prod
  5 этапов. 2–3 дня от кода до прода.

Trunk-based:
  Пишу в feature/ → PR → CI → merge в main → CI → deploy в staging → deploy в prod
  3 этапа. 3–6 часов от кода до прода.
```

**2. Merge conflict — в момент, а не через 3 недели.**

```
Git Flow:
  Неделя 1: feature/A и feature/B не пересекаются.
  Неделя 2: feature/A меняет Order.php.
  Неделя 3: feature/B мержится. Conflict в Order.php.
           Разрешать conflict в контексте 3-недельной ветки.
           «А почему тут так? Кто это изменил?» → git log → 15 коммитов.

Trunk-based:
  День 1: feature/A мержится в main.
  День 2: feature/B видит изменения в main. Адаптируется.
  Нет 3-недельного drift.
```

**3. `main` всегда deployable.**

Каждый merge в `main` — это потенциальный релиз. Не «копилим фичи до пятницы, а потом деплоим». Каждая фича — отдельный деплой.

### Что с `release` и `hotfix`

**Release** = tag, не ветка.

```bash
# Фича смёржена в main. CI зелёный. Staging OK.
git tag -a v2.14.0 -m "Order cancel, stock limit, PHP 8.3"
# CI: build → deploy staging → smoke test → deploy prod
```

**Hotfix** = короткая ветка от `main`.

```bash
git checkout -b fix/critical-stock-oversell main
# fix → PR → merge в main → tag v2.14.1 → deploy
```

Если hotfix случается **чаще 1 раза в 2 недели** — это не процесс, а проблема качества. Нужно не «быстрее hotfix», а «почему это попало в прод».

---

## 3. Коммиты

### Формат

```
<тип>: <короткое описание в настоящем времени>

[необязательное тело: объяснение ПОЧЕМУ]

[необязательные футер: Breaking Change, Closes #123]
```

### Примеры

```
feat(order): allow cancellation after confirmation

Previously cancel() threw InvalidTransition when status was
confirmed. Now it's allowed if no payment has been captured.

Closes #482
```

```
fix(stock): prevent oversell on concurrent reserve

Added WHERE guard (reserved + units <= quantity) to the
atomic UPDATE. Without it, two concurrent requests could
both pass the app-level check and oversell.

BREAKING CHANGE: StockLevel::reserve() now throws
ConcurrencyConflict instead of silently succeeding.
```

```
chore(deps): upgrade doctrine/orm to 3.3

- Remove deprecated EntityManagerInterface::create()
- Replace findBy() with getRepository()->findBy()
```

### Правила

| Правило | Почему |
|---|---|
| Тип: `feat`, `fix`, `chore`, `docs`, `refactor`, `test`, `perf` | `git log --oneline` читается. `git log --grep` работает. |
| Краткое описание ≤ 72 символа | `git log --oneline` не обрезает. |
| Настоящее время: `add`, не `added` | `git log` читается как инструкция: `feat: add cancel` = «это добавляет отмену». |
| Бюль: **почему**, не **что** | `что` видно в diff. `почему` — нет. |
| `Closes #123` в футере | Автозакрытие задачи. Связь код ↔ задача. |
| `BREAKING CHANGE:` в футере | Автоматический highlight в changelog. |

### Чего я **не** делаю

```
# ПЛОХО:
commit "fix stuff"
commit "WIP"
commit "finally fixed the bug"
commit "update"
commit "1"
commit "s"
commit "merge branch 'feature/x' into main"   ← это делает squash-merge

# ПЛОХО:
commit "fix: fixed the issue where the order was not cancelable
         because the status check was wrong and also the event
         was not being dispatched properly"
         ← 3 изменения в одном коммите. 3 коммита.
```

### Squash или не squash?

**Я squash'ю.** Ветка `feature/order-cancel` содержит 7 коммитов:

```
a1: feat: add cancel() method to Order
a2: feat: add OrderCancelled event
a3: feat: add CancelOrderHandler
a4: test: add cancel tests
a5: fix: edge case with delivered orders
a6: chore: update docs
a7: fix: typo in exception message
```

Squash → **один** коммит в `main`:

```
feat(order): allow cancellation after confirmation

Closes #482
```

**Почему squash:**

| | Без squash | С squash |
|---|---|---|
| `git log main` | 2000+ коммитов. `WIP`, `fix typo`, `update` | 200 осмысленных коммитов |
| `git bisect` | 7 шагов на фичу | 1 шаг на фичу |
| `git revert` | Revert 7 коммитов (risk) | Revert 1 коммит |
| `git log --oneline` | Шум | Читаемо |
| История ветки | Потеряна | Сохранена в PR |

**Почему не «сохранить всю историю»:** История **внутри** ветки — рабочая. `a1` → `a7` — это «как я думал». Это не история проекта. История проекта — **что изменилось и почему**. Это в squashed-коммите и в PR.

**Исключение:** `refactor/` и `chore/` иногда мержу без squash (3–5 осмысленных коммитов). Если каждый коммит самодостаточен и ревьюер их просматривал — OK.

---

## 4. Pull Request

### Структура PR

```markdown
## Что
Позволяет отменить заказ после подтверждения, если оплата ещё не была
заряжена (status: confirmed, payment: pending).

## Почему
Клиенты сообщают, что не могут отменить заказ, если передумали
сразу после подтверждения, но до оплаты. Сейчас — только до
подтверждения. Это создаёт 15–20 обращений в неделю.

## Как
- Order::cancel() теперь допускает transition confirmed → cancelled
  (ранее: pending → cancelled только)
- Новый domain event: OrderCancelled
- Новый use case: CancelOrderHandler
- Инвариант: нельзя отменить, если payment.status = succeeded

## Тесты
- Unit: 4 новых теста (transition, event, edge cases)
- Integration: 2 новых (DB, outbox)
- API: 2 новых (200, 409)

## Что НЕ изменилось
- Отмена pending → cancelled работает как раньше
- Cancel delivered → всё ещё InvalidTransition
- Payment flow не тронут

## Скриншоты / видео
(если UI)

## Риски
- Если Stripe webhook придёт между cancel и charge → деньги списаны,
  заказ отменён. Митигация: webhook проверяет status. Если cancelled
  → refund.
```

### Правила

| Правило | Почему |
|---|---|
| **Маленький PR** (≤ 400 строк diff) | Ревьюер тратит 15–30 мин, а не 3 часа. Мелкие баги не прячутся. |
| **Одна логическая единица** | «Отмена заказа» — один PR. Не «отмена + скидка + новый цвет кнопки». |
| **Draft → Ready** | Draft = «я работаю, смотрите направление». Ready = «ревьюите». Не спамит ревьюеру. |
| **Тесты ДО кода** (RED → GREEN) | Ревьюер видит тесты. Понимает ожидаемое поведение. Не гадает. |
| **Ссылка на задачу** | `Closes #482`. Связь. |
| **CI зелёный до запроса ревью** | Не трясём ревьюера. Он не ждёт, пока `composer install` пройдёт. |

### Чего я **не** делаю

```
# ПЛОХО:
PR: "Update" (2000 строк, 15 файлов, 3 фичи, 2 фикса, 1 refactor)
PR: "Fix all TODOs" (47 файлов)
PR: "Refactor everything in Order" (без теста)
PR: "Merge develop into feature" (merge commit, не squash)
```

### Если PR большой — разбить

```
# Вместо одного PR на 2000 строк:
PR 1: feat(order): add OrderCancelled event + domain logic (300 строк)
PR 2: feat(order): add CancelOrderHandler + tests (200 строк)
PR 3: feat(order): add API endpoint + voter (150 строк)

Каждый мержится отдельно. Каждый deployable.
```

---

## 5. Code Review

### Что я ищу (по приоритету)

| Приоритет | Что | Пример |
|---|---|---|
| **1. Правильность** | Логика неверна? Инвариант нарушен? Edge case? | `$order->cancel()` — а если `status = delivered`? |
| **2. Безопасность** | SQL injection? XSS? Auth bypass? | `WHERE id = '.$_GET['id']` — нет, но `ORDER BY '.$col` — да |
| **3. Архитектура** | Нарушено правило зависимостей? SRP? OCP? | Controller ходит в Doctrine напрямую. |
| **4. Тесты** | Покрывают ли сценарий? Edge cases? | `test_cancel` — а `test_cancel_delivered`? |
| **5. Имена** | Читаемо? Ubiquitous language? | `processData()` → `confirmOrder()` |
| **6. Стиль** | Форматирование, import'ы | `use` в неправильном порядке |

### Чего я **не** ищу

- «Почему `int` а не `float`?» — если `int` правильный, `int`. Обсуждение типов — в отдельном PR.
- «Почему 4 пробела, а не 2?» — `.editorconfig` решает.
- «Почему `foreach` а не `array_map`?» — если оба читаемы — OK.
- «Почему `final` а не `abstract`?» — если `final` правильный, `final`.

### Как я ревьюю

```
1. Читать PR description. Понять «что» и «почему».
2. Прочитать тесты. Понять ожидаемое поведение.
3. Прочитать код. Сверить с тестами.
4. Проверить edge cases. «А что если...?»
5. Проверить architecture. «Где это лежит правильно?»
6. Комментарий:
   - [blocker] — нужно исправить до merge
   - [question] — не понял, объясни
   - [suggestion] — можно лучше, но не критично
   - [nit] — стиль, не блокирует
```

### Пример ревью-комментария

```
🔴 [blocker] src/Domain/Order/Order.php:47

public function cancel(): void
{
    if ($this->status === OrderStatus::Delivered) {
        throw new CannotCancelDelivered();
    }
    // Но: что если status = Confirmed И payment = Succeeded?
    // Деньги списаны. Заказ отменён. Кто возвращает?
    //
    // Предложение:
    // 1. В CancelOrderHandler: проверить payment.status.
    //    Если succeeded → throw PaymentAlreadyCaptured.
    // 2. Или: разрешить отмену, но publish OrderCancelled.
    //    PaymentListener делает refund.
    //
    // Какой сценарий правильный?
```

### Почему ревью — не «галочка»

| Подход | Результат |
|---|---|
| «ЛГТМ» за 2 минуты | Баг в проде. «Я не успел посмотреть». |
| 30 минут, 5 вопросов, 2 blocker'а | Баг найден до прода. 2 часа работы не потеряны. |
| Ревью на следующий день | Ревьюер забыл контекст. 50% вопросов — «а это что?» |

**Правило:** ревью в тот же день. Максимум 24 часа. Иначе контекст потерян.

---

## 6. CI/CD pipeline

```
PR открыт
    │
    ├── 1. Lint + Style (10 сек)
    │      php-cs-fixer --dry-run
    │      phpstan analyse --level 8
    │
    ├── 2. Unit tests (15 сек)
    │      vendor/bin/phpunit tests/Unit/
    │
    ├── 3. Architecture tests (10 сек)
    │      vendor/bin/phpunit tests/Architecture/
    │
    ├── 4. Integration + E2E (2 мин)
    │      (Postgres service)
    │      vendor/bin/phpunit tests/Integration/ tests/Api/
    │
    ├── 5. Build (30 сек)
    │      composer install --no-dev
    │      symfony cache:clear --env=prod
    │
    └── 6. Security audit (1 мин)
           composer audit
           npm audit (если фронт)
           trivy image scan (docker)

    Все зелёные → PR можно mergать

Merge в main
    │
    ├── 7. Deploy staging (2 мин)
    │      docker build → push → k8s rollout
    │
    ├── 8. Smoke test (1 мин)
    │      curl /health
    │      curl /api/orders (200)
    │
    └── 9. Deploy prod (2 мин)
           k8s rollout (canary 10% → 50% → 100%)

    Если smoke test падает → rollback (30 сек)
```

### Почему каждый шаг — отдельный job

| Если один job | Если отдельные jobs |
|---|---|
| Lint падает → не знаем, тесты зелёные? | Lint красный, тесты зелёные — видно |
| Повторный запуск — всё с нуля | Повторить только упавший шаг |
| 5 минут на всё | Параллельные: lint + unit + arch одновременно |

---

## 7. Деплой

### Стратегия: canary

```
1. Build image: app:v2.14.0
2. Deploy 10% traffic → new pods
   - 5 мин: мониторинг (error rate, latency, CPU)
   - Если error rate > 1% → ROLLBACK (30 сек)
3. Deploy 50% traffic
   - 5 мин
4. Deploy 100%
5. Old pods: graceful shutdown (drain requests, 30 сек)

> ⏱ 55.3s · prompt 126099 tok · output 4973 tok · ⚠️ обрыв: length

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о моём подходе к код-ревью: маркировка замечаний, приоритеты, разрешение разногласий. Это продолжение длинной серии о PHP/Symfony/DDD/архитектуре. Нужно ответить конкретно, с примерами, без воды.

Что важно:
1. Система маркировки — конкретная, с примерами
2. Приоритеты — что смотрю первым, что не смотрю
3. Разногласие — конкретный протокол, а не «обсудим»
4. Это продолжение контекста: чистая архитектура, SOLID, доменные события, concurrency — я уже описывал. Не повторять, а углубить именно в процесс ревью.

Структура:
- Система маркирований (с конкретными примерами комментариев)
- Приоритеты (что первое, что последнее, что не смотрю)
- Как пишу комментарии (тон, формат)
- Разногласие (конкретный протокол, а не «обсудим»)
- Антипаттерны ревью
- Пример полного ревью (несколько комментариев на один дифф)

Должно быть практично, с примерами. Не абстрактно.

</details>

**Кандидат:**

## Маркировка: три уровня

Не пять. Не семь. Три. Потому что автор PR должен сразу понять: **что блокирует merge, а что нет**.

### 🛑 `blocker` — merge невозможен без исправления

```
🛑 src/Domain/Order/Order.php:52

    public function cancel(): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new CannotCancelDelivered();
        }
        $this->status =

> ⏱ 8.8s · prompt 130678 tok · output 390 tok · ⚠️ обрыв: length

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит взять сырую бизнес-жалобу и превратить её в формулировку задачи для исполнителя (разработчика), которая может быть выполнена без уточняющих вопросов. Это задача на product management / technical writing.

Давайте разберу, что здесь нужно сделать:

1. Сырая жалоба: "Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями"

Что в ней НЕ определено:
- Что именно "не могут найти" — нет раздела "Мои заказы"? Есть, но медленный? Ищут по чему? По дате? По сумме? По товару?
- "Из прошлого" — сколько? Месяц? Год? 5 лет?
- "Поддержка завалена" — сколько обращений? Какой тип?
- Кто исполнитель? Фронтендер? Бэкендер? Полстек?

</details>

**Кандидат:**

(пустой ответ модели)

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.0s · prompt 130871 tok · output 200 tok · ⚠️ обрыв: length

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

The user is asking about how I handle two specific situations during task execution:
a) Finding an unrelated bug along the way
b) Discovering that an acceptance criterion contradicts the task's goal

This is a practical software engineering question about professional conduct, communication, and decision-making. Let me think through both scenarios carefully.

For (a) - finding an unrelated bug:

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.5s · prompt 130995 tok · output 77 tok · ⚠️ обрыв: length

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о структуре отчёта о выполненной задаче. Это вопрос о профессиональной практике — как оформить

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.0s · prompt 131046 tok · output 26 tok · ⚠️ обрыв: length

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про FORCE INDEX в PostgreSQL. Это вопрос-ловушка: FORCE INDEX — это синтаксис MySQL, а не PostgreSQL. В PostgreSQL такого механизма нет. Нужно честно сказать об этом, объяснить, как в MySQL работает FORCE INDEX, а затем дать эквиваленты для PostgreSQL — потому что скорее всего пользователь хочет решить реальную

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 74.9s · prompt 130998 tok · output 73 tok · ⚠️ обрыв: length

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает: «По твоему опыту, на сколько процентов strict_types=1 замедляет

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.1s · prompt 131043 tok · output 25 tok · ⚠️ обрыв: length

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, почему Doctrine ORM не поддерживает составные (composite) первичные ключи. Это фактический вопрос, но в нём есть предпосылка, которую нужно проверить: действительно ли Doctrine «не поддерживает» composite PK?

Факты:
1. Doctrine ORM **поддерживает** composite primary keys через `@Id` на нескольких полях или `#[ORM\Id]` на нескольких свойствах. Это работает.
2. Но есть ограничения и неудобства:
   - `EntityManager::find()` работает только с одним ID или с массивом для composite.
   - `EntityManager::getReference()` с composite — работает через массив.
   - Association mapping: `@ManyToOne` на composite key — есть, но awkward.
   - `@OwningSide` с composite FK — работает, но через `@JoinColumns`.
   - Identity map работает, но ключ — это composite (массив или строка).
   - Dirty checking на composite entities — работает, но сложнее.
   - `@MappedSuperclass` с composite ID — проблемы.
   - Serialization/denormalization — сложнее.
   - `EntityManager::detach()` — работает.
   - Cascade и orphanRemoval — работают.
   - НО: многие фичи Doctrine (like `@NaturalId`, `@HasLifecycleCallbacks`) — могут быть ограничены.
   - **Главное неудобство**: `find()` с composite ID — нужно передавать массив. `getReference()` — тоже. Это не «не работает», а «неудобно».
   - **Реальное ограничение**: Doctrine **не позволяет** использовать composite ID в `@Id` + `@GeneratedValue`. То есть auto-increment только на одно поле.
   - **Настоящая проблема**: association mapping. Если у тебя есть `Order` (composite ID: orderId + itemId) и ты делаешь `@ManyToOne` от `OrderItem` к `Order` — это работает. Но если `Order` сам имеет composite ID и ты делаешь `@OneToMany` на `OrderItem` — `mappedBy` работает, но `@JoinColumn` на owning side — нужно перечислить все колонки.

Подожди, я должен быть честным: **Doctrine ПОДДЕРЖИВАЕТ composite primary keys**. Это не миф. В документации прямо сказано: "You can map an entity with a composite primary key by annotating multiple fields with @Id". 

Но есть реальные проблемы:
1. `EntityManager::find(OrderItem::class, ['orderId' => $id, 'productId' => $pid])` — работает, но неудобно.
2. `EntityManager::getReference(OrderItem::class, ['orderId' => $id, 'productId' => $pid])` — работает.
3. Но: **некоторые фичи не работают или работают некорректно**:
   - `@NaturalId` — не поддерживает composite ID (или поддерживает с ограничениями).
   - `@HasLifecycleCallbacks` — работает.
   - `@UniqueConstraint` — работает.
   - **Identity map**: ключ в identity map — это composite. Работает, но если ты делаешь `find()` с неправильным типом (int vs string) — не найдётся.
   - **`EntityManager::merge()`** — работает, но сложнее.
   - **`@Version`** — работает на composite entities.
   - **`@PreFlush`** — работает.

4. **Реальная боль**: 
   - Сериализация: если ты сериализуешь entity (JSON, Redis cache) — composite ID становится объектом/массивом. Десериализация сложнее.
   - `EntityManager::clear()` — работает.
   - **Query Builder**: `WHERE o.id = :id` — не работает для composite. Нужно `WHERE o.orderId = :oid AND o.productId = :pid`.
   - **DQL**: `WHERE e.id IN (:ids)` — не работает для composite. Нужно `WHERE (e.orderId, e.productId) IN (...)`.
   - **`@OneToOne` с composite FK** — работает, но awkward.
   - **`@ManyToMany` с composite ID** — работает, но join table будет с composite FK.

5. **Настоящая проблема, которую я знаю из практики**:
   - Doctrine **не поддерживает** composite ID в сочетании с `@GeneratedValue`.
   - Doctrine **не поддерживает** composite ID в `@NaturalId` (по крайней мере, в старых версиях).
   - **`EntityManager::getReference()`** с composite ID — работает, но если ты передашь одно поле вместо двух — ошибка.
   - **Identity map и `find()`** — работает, но если типы не совпадают (int vs string) — не найдётся. Это тихий баг.
   - **`@UniqueConstraint`** на composite — работает.

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 84.0s · prompt 130014 tok · output 1055 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать плохой PHP-код контроллера оплаты заказа. Это типичный "спагетти" код, где всё в одной функции. Нужно:

1. Идентифицировать ВСЕ проблемы (безопасность, архитектура, надёжность, качество)
2. Показать переделанный код
3. Объяснить, с чего начать и почему — это важно, потому что "переписать всё" — не ответ, а "приоритизация" — да

Проблемы, которые я вижу:

**Безопасность:**
- SQL injection: `$id` вставляется прямо в SQL без параметризации. `WHERE id = $id` → `WHERE id = 1; DROP TABLE orders;--`
- Нет аутентификации/авторизации: любой может оплатить любой заказ
- XSS в email (Mailer)
- Нет CSRF-защиты

**Надёжность:**
- Нет транзакции: SELECT и UPDATE не атомарны. Между ними кто-то может изменить статус.
- Нет проверки текущего статуса: можно "оплатить" уже оплаченный, отменённый, несуществующий
- Race condition: два параллельных запроса → двойная оплата
- Нет проверки idempotency: повторный запрос = повторная отправка email
- Mailer::send может упасть после UPDATE → заказ оплачен, но письмо не ушло. Или наоборот: письмо ушло, но UPDATE откатился (нет транзакции, но даже если бы была — email не должен быть в транзакции).
- Нет проверки, что деньги реально списаны (Stripe, банк). `pay` — это "установить статус paid", а не "списать деньги". Где оплата?
- `$order['email']` — если email null/пусто? Mailer упадёт?

**Архитектура:**
- Controller делает всё: валидацию, БД, бизнес-логику, отправку email, логирование
- Нет разделения на слои
- `DB::connect()` — global, нет DI
- `Mailer::send()` — static, не инъектируется, не тестируется
- `(new Logger())` — создаётся каждый раз
- Нет доменной модели: `$order` — associative array из БД
- Нет обработки ошибок: PDOException, MailerException — всё падает в 500
- Нет контракта: что возвращает `pay`? `Response('ok')` — а что если email не отправился?

**Качество кода:**
- `$req->get('order_id')` — откуда? body? query? params? Не ясно.
- `SELECT *` — не знаем, какие поля нужны
- Нет типизации
- Нет валидации $id (string? int? uuid?)
- Логирование после email — если email упадёт, лог не напишется
- Нет try/catch

**S чего начать:**
1. SQL injection — это P0, безопасность. Фиксится в одну строку (prepared statements). Но это не "решает" проблему.
2. Аутентификация — без неё код не должен работать в проде.
3. Транзакция + проверка статуса — без этого данные будут несогласованными.
4. Архитектура — разделение на слои.
5. Остальное — качество.

Приоритизация:
- Сначала: безопасность (SQL injection, auth)
- Потом: корректность (транзакция, статус, idempotency)
- Потом: архитектура (слои, DI)
- Потом: качество (типы, ошибки, логирование)

С чего начать конкретно:
1. SQL injection

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 9.4s · prompt 130234 tok · output 838 tok · ⚠️ обрыв: length

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

Пользователь просит разобрать конкретный код доменной сущности с точки зрения DDD и чистой архитектуры. Это не вопрос на знание фактов — это просьба применить принципы, которые мы обсуждали на протяжении всего разговора.

Давайте посмотрим на код:

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

1. **EntityManager в доменной сущности** — это самое грубое нарушение. Домен не должен знать о Doctrine. `EntityManagerInterface` — это инфраструктурная зависимость. Сущность не может сама вызывать `flush()` — это нарушение правила направленных зависимостей (Domain ← Application ← Infrastructure).

2. **Смешение домена и персистентности** — `#[ORM\Entity]` + `EntityManagerInterface` в одном классе. Это не "доменная сущность" — это ORM-сущность (anemic + fat hybrid). Настоящая доменная сущность (Aggregate Root) не знает о том, как хранится.

3. **`flush()` в сущности** — сущность сама решает, когда коммитить. Это нарушает принцип: flush вызывается на уровне application/use-case, а не в домене. Сущность мутирует состояние; кто и когда его персистит — решение application-слоя.

4. **Нет валидации email** — `changeEmail("not-an-email")` просто присваивает. Доменная сущность должна гарантировать инварианты. Email должен быть валидным.

5. **Нет Value Object для Email** — email хранится как `string`. Нет инкапсуляции. Нет валидации. Нет `equals()`. Нет `__toString()`.

6. **Нет identity** — у User нет `id`. Как доменная сущность она должна иметь identity.

7. **Нет доменных событий** — смена email — это значимое событие. Другим частям системы (аудит, уведомления, реиндексация) нужно об этом знать.

8. **Нет separation** — один класс делает всё: хранение данных, бизнес-логику, персистентность. Нарушение SRP.

9. **Публичный конструктор с EntityManager** — любой код может создать User, передав EM. Это создаёт цикл: домен зависит от инфраструктуры.

10. **Нет readonly** — email можно менять через `changeEmail`, но нет контроля.

11. **Нет доменных исключений** — если email невалиден, должно быть доменное исключение, а не null или молчаливое присвоение.

Теперь, как бы я это переделал:

1. Доменная сущность `User` — чистый PHP, без Doctrine, без

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 8.0s · prompt 130391 tok · output 681 tok · ⚠️ обрыв: length

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о моём подходе к решению задач на незнакомом стеке. Это вопрос о процессе мышления и работе, не о конкретной технологии. Нужно описать последовательность действий, критерии для просьбы о помощи.

Отвечу на русском, как и весь контекст диалога. Структурирую: последовательность шагов, конкретные критерии для "спросить/не спросить", с примерами. Не буду писать абстрактно — буду показывать на конкретных ситуациях, чтобы ответ был живым и применимым.

Важно: не давать "универсальный совет" типа "читай документацию". Это знают все. Покажу реальную последовательность мышления, включая моменты, где я останавливаюсь и переоцениваю, а не бегу вперёд.

</details>

**Кандидат:**

## Первые 10 минут: не писать код

### Что делаю

Открываю задачу и задаю себе три вопроса по порядку:

**1. Что именно я не знаю?**

Не "мне незнаком стек", а конкретно:

```
Знаю:        PHP, HTTP, SQL, как устроена БД, как работает auth
Не знаю:     Rust, wasm, как этот конкретный фреймворк
Не знаю:     почему именно этот фреймворк, а не другой (контекст проекта)
Не знаю:     какие уже были попытки решения, что пробовали до меня
```

Это критически важно. "Незнакомый стек" — это не одна неизвестность, а несколько слоёв. Я разделяю **фундамент** (который я знаю: HTTP, SQL, память, процессы) от **обёртки** (которую не знаю: конкретный фреймворк, его API, его идиомы). Фундамент не исчезает. Фреймворк — это словарь, который можно выучить за часы.

**2. Какой результат ожидается?**

Не "сделать на Rust", а:
- Что именно должно работать?
- Какие критерии приёмки?
- Какой срок? (3 дня или 3 недели — разный подход)
- Есть ли уже работающий код, который нужно поддерживать, или с нуля?

**3. Кто будет читать мой код через 6 месяцев?**

Если я пишу на незнакомом стеке — через 6 месяцев меня может не быть. Кто подхватит? Значит:
- Код должен быть в **идиоматике этого стека**, а не "PHP, но на Rust"
- Имена, структура, комментарии — на языке этого стека
- Если я делаю что-то нестандартно — объясняю почему в PR, а не в коде

---

> ⏱ 8.4s · prompt 130473 tok · output 598 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о профессиональной этике и коммуникации: пообещал срок, понял что не успеть. Это классическая ситуация в разработке.

Мне нужно ответить конкретно, без воды. Что я делаю на практике. Ключевые аспекты:
1. Когда я понимаю, что не успеть — не в момент дедлайна, а раньше
2. Как именно я это доношу — кому, когда, в какой формулировке
3. Что я предлагаю вместо "я не успеваю" — варианты
4. Чего я НЕ делаю

Важно: вопрос про "ты" — то есть

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.0s · prompt 130932 tok · output 140 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансе качества и скорости — это классический вопрос инженерного суждения. Мне нужно дать практичный, честный ответ, а не абстрактную теорию. 

Что я действительно делаю:

1. У меня есть конкретные критерии, по которым код "достаточно хорош":
   - Тест

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.8s · prompt 130996 tok · output 75 tok · ⚠️ обрыв: length

---
