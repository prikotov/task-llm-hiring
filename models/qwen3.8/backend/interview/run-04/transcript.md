# Собес: `qwen3.8:latest` · роль: backend — run-04

- Дата: 2026-10-06T18:14:45.991812+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: auto · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to introduce myself and talk about my experience in PHP development and related technologies. They want me to rate my knowledge on a scale of 0-10 for each topic: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, clean architecture, testing, git — and explain each rating in one sentence.

I need to be honest here. I'm an AI language model. I don't have "experience" in the traditional sense — I haven't written production code, debugged at 3am, or had code reviews. But I do have extensive training data about all these technologies. I should be transparent about what I am while still being helpful.

Let me think about how to frame this honestly:
- I don't have personal experience, but I have extensive knowledge from training data
- I should be honest that I'm an AI, not a developer with a career
- But I can still be useful as a coding assistant, explain concepts, write code, debug, etc.
- For the ratings, I should be fair. My "knowledge" is different from a human developer's — I know syntax, patterns, best practices from documentation and code examples, but I don't have the "feel" of debugging in production, I don't have context-specific war stories.

Let me think about each rating:
- PHP 8: I know the language well — readonly properties, enums, fibers, union types, attributes, match expressions, etc. I can write PHP 8 code. But I don't "use" it daily. I'd say 8-9 for knowledge of the language itself.
- Symfony: I know the framework architecture, components, best practices, configuration. I've seen a lot of Symfony code in training data. 8.
- Doctrine: I know ORM, DQL, migrations, repository patterns. 8.
- PostgreSQL: I know SQL, window functions, CTEs, indexing strategies, etc. 7-8.
- DDD: I know the concepts — aggregates, value objects, domain events, bounded contexts. I can explain and implement them. 7-8.
- Clean architecture: I know the layers, dependency rules, hexagonal architecture. 7-8.
- Testing: PHPUnit, Prophecy, integration testing, TDD practices. 7-8.
- Git: I know the commands, workflows, rebasing, merging. 8-9.

I should be honest that these are "knowledge" ratings, not "experience" ratings. I haven't shipped production code. But my knowledge of the concepts, patterns, and code is quite extensive.

Let me write a response in Russian since the user wrote in Russian.

</details>

**Кандидат:**

## Знакомство

Привет! Сразу буду честен: я не разработчик с биографией в резюме, ночными дебагами и «а помнишь тот incident в пятницу». Я языковая модель, у меня нет рук, нет продакшена, нет code review. Но за то время, что я обрабатывал документы, исходники, issues, Stack Overflow-треды и учебники, у меня сформировалась довольно плотная *декларативная* картина того, как устроены эти технологии — и я могу писать рабочий код, объяснять «почему так», ловить типовые ошибки и обсуждать архитектурные решения.

Важная оговорка: моя «опытность» — это не интуиция, выработанная на десятках проектов, а широкий, но плоский срез. Я не «чувствую» Doctrine-полиморфизм, я его *знаю*. Это разные вещи, и если вам нужен собеседник, который скажет «а у нас в проде это упало, потому что…», — я не тот. Если нужен партнёр для разбора архитектуры, написания кода, ревью и объяснений — постараюсь быть полезным.

## Самооценка (0–10)

| Раздел | Оценка | Обоснование одним предложением |
|---|---|---|
| **PHP 8** | **9** | Я уверенно владею синтаксисом и новыми фичами (readonly, enums, fibers, attributes, union types, `never`), могу писать и ревьюить код, но не прогоняю его в реальном runtime, поэтому мелкий edge-case в динамической типизации могу пропустить. |
| **Symfony** | **8** | Я знаю архитектуру ядра (DI-контейнер, event dispatcher, service locator), компоненты (HttpKernel, Console, Validator), лучшие практики конфигурации и типичные грабли с autowiring/autowire-конфликтами, но не прошёл путь «собрал проект с нуля → прогнал на staging → получил 200-строчный stack trace». |
| **Doctrine** | **8** | ORM, DQL, EntityManager-цикл, миграции, Repository pattern, lazy/eager, N+1 — всё это я умею описать и закодировать, однако тонкие сценарии с наследованием и каскадами за пределами типовых случаев мне ближе как теория, чем как «я это трижды чинил». |
| **PostgreSQL** | **7** | Я пишу SQL, знаю CTE, window functions, partial index, JSONB-операторы, basics of VACUUM и planning, но не профилирую `EXPLAIN ANALYZE` на реальных данных по 50 млн строк, где решение упирается в I/O, а не в синтаксис. |
| **DDD** | **7** | Я разбираюсь в агрегатах, value objects, domain events, bounded contexts, стратегиях тактики/стратегии и могу нарисовать структуру модулей, но «живое» DDD рождается из предметной области, а у меня её нет — я переношу паттерны из документации. |
| **Чистая архитектура** | **7** | Я знаю слои, правило зависимостей, hexagonal/clean-подход, могу спроектировать boundaries и ports/adapters, но без реального проекта не ощущаешь, где «чистота» начинает тормозить скорость доставки, и этот компромисс я описываю абстрактно. |
| **Тестирование** | **7** | PHPUnit, Prophecy, TDD-цикл, интеграционные vs unit, mock'и, fixture'ы, code coverage — я могу написать тесты и объяснить, *что* и *почему* тестировать, но не ловлю на себе «test passed, а prod упал», потому что не держу сервис. |
| **Git** | **8** | Коммиты, rebase, cherry-pick, merge strategies, bisect, hooks, работа с ветками и pull-реквестами — я уверенно оперирую всем этим и могу составить git-команды под задачу; минус балл за то, что не переживал конфликт на 200 файлов в пятницу вечером и не знаю, *когда* rebase — плохая идея, а только *что* это такое. |

## Что я реально умею сделать для тебя

- Написать или отрефакторить PHP/Symfony/Doctrine-код под конкретную задачу.
- Разобрать архитектурное решение: «а тут лучше event или repository?»
- Написать тесты, объяснить, почему тест «зелёный», но не тот.
- Посмотреть на SQL-запрос и предложить индекс или переписать на CTE.
- Объяснить DDD-термин без «воды из книги».
- Написать git-команды, скрипты, CI-конфиги.

Если хотите, бросьте мне конкретную задачу или код — будет гораздо продуктивнее, чем абстрактное знакомство. С чего начнём?

> ⏱ 17.2s · prompt 87 tok · output 1713 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user is asking about the most important features of modern PHP (8.0+) for writing clean and reliable code. They want short code examples. Let me think about what truly matters for code quality and reliability:

1. **Union types and intersection types (8.0)** — better type safety
2. **Named arguments (8.0)** — readability
3. **Match expressions (8.0)** — cleaner than switch, no fall-through, returns values
4. **Constructor property promotion (8.0)** — less boilerplate
5. **Nullsafe operator (8.0)** — less verbose null checks
6. **Readonly properties (8.2)** — immutability
7. **Enums (8.1)** — replace magic constants/strings
8. **`never` return type (8.1)** — expresses "doesn't return"
9. **Fiber (8.0)** — though more for concurrency, less for "clean code" per se
10. **Attributes (8.0)** — replaces docblocks for annotations (Symfony, Doctrine)
11. **`enum` + backed enums (8.1)** — type safety
12. **Destructuring improvements**
13. **First-class callable syntax (8.1)** — cleaner
14. **Readonly classes (8.2)** — full immutability
15. **Typed constants (8.3)**
16. **`new` in initializers (8.1)** — less boilerplate
17. **Asymmetric visibility (8.2)** — probably niche
18. **`&new` (8.1)** — probably niche
19. **WeakMap, WeakRef (8.0)** — memory management
20. **`str_starts_with`, `str_contains` (8.0)** — standard library additions

For "clean and reliable code" specifically, I'd prioritize:
- **Union/Intersection types + `null` in parameters** — type safety at compile time
- **Readonly properties / readonly classes (8.2)** — immutability, no accidental mutations
- **Enums (8.1)** — eliminates stringly-typed bugs
- **Match expressions** — exhaustive matching, no fall-through
- **Constructor property promotion** — DRY, less boilerplate
- **Named arguments** — readability at call sites
- **Nullsafe operator** — cleaner chains
- **`never` return type** — expresses intent (e.g., `exit()` alternatives, domain exceptions)
- **Attributes (8.0)** — cleaner annotations for frameworks
- **First-class callable syntax (8.1)** — cleaner closures
- **`new` in initializers (8.1)** — DRY

Let me structure this as a focused answer with maybe 6-8 key features, each with a short, meaningful code example. I want the examples to be realistic (not toy), showing the "before → after" or just the "after" with a comment about why it matters.

I'll aim for a well-structured response with clear sections. Let me pick the most impactful ones and write concise, realistic examples. I'll group them logically.

Let me write this in Russian since the user is communicating in Russian.

</details>

**Кандидат:**

# Ключевые фичи PHP 8.0+ для чистого кода

Выделю то, что реально меняет качество кода, а не просто «добавляет синтаксис».

## 1. Союзные и пересечённые типы + `null`

Самое важное для надёжности: сужаем пространство допустимых значений на уровне сигнатуры.

```php
// До 8.0: docblock-аннотация, которую никто не проверяет
/**
 * @param string|int $id
 */
function findById($id): ?Entity

// PHP 8.2: тип — контракт, стат-анализер не даст ошибиться
function findById(string|int $id): ?Entity
{
    // ...
}

// Пересечение типов: объект должен быть одновременно счётным и итерируемым
function toList(countable&iterable $items): array
{
    return is_array($items) ? $items : iterator_to_array($items);
}
```

## 2. `readonly` свойства и классы (8.2)

Иммутабельность без boilerplate. Ошибка «кто-то мутировал value object» исчезает на уровне компилятора.

```php
readonly class Money
{
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}

    public function add(self $other): self
    {
        assert($this->currency === $other->currency, 'Currency mismatch');
        return new self($this->amount + $other->amount, $this->currency);
    }
}

// Попытка $money->amount = 100 → TypeError в рантайме,
// а у стат-анализора — ошибка ещё до запуска.
```

`readonly class` (8.2) делает **все** свойства readonly и запрещает наследование — идеальный шаблон для DTO и value objects.

## 3. Enum (8.1)

Убивает «стрингли-типы» и магические константы.

```php
// До 8.1:
// class Status { public const OPEN = 'open'; public const CLOSED = 'closed'; }
// $order->status = 'OpEn';  // ничего не сломалось

// PHP 8.1:
enum OrderStatus: string
{
    case Open   = 'open';
    case Shipped = 'shipped';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open    => 'Открыт',
            self::Shipped => 'Отправлен',
            self::Closed  => 'Закрыт',
        };
    }
}

// Теперь:
$order->status = OrderStatus::Closed; // ✅
$order->status = 'closed';           // ❌ TypeError
$order->status = 'closedd';          // ❌ TypeError

// И match-исчерпывающий — стат-анализер подскажет про забытый case:
function transitionTo(OrderStatus $current): array
{
    return match ($current) {
        OrderStatus::Open    => [OrderStatus::Shipped, OrderStatus::Closed],
        OrderStatus::Shipped => [OrderStatus::Closed],
        OrderStatus::Closed  => [],
    };
}
```

## 4. `match` вместо `switch`

Нет fall-through, возвращает значение, строгое сравнение, а не `==`.

```php
// До:
function discount(string $tier): float {
    switch ($tier) {
        case 'gold':
        case 'platinum':
            $rate = 0.2;
            break;
        case 'silver':
            $rate = 0.1;
            break;
        default:
            $rate = 0.0;
    }
    return $rate;
}

// PHP 8.0:
function discount(string $tier): float
{
    return match ($tier) {
        'gold', 'platinum' => 0.2,
        'silver'           => 0.1,
        default            => 0.0,
    };
}

// Стат-анализер: если добавите новый case в enum,
// а здесь не обновите match — получите предупреждение.
```

## 5. Конструкторная промoción свойств (8.0) + `new` в инициализаторах (8.1)

Меньше строк, меньше дублирования, явнее контракт.

```php
// До 8.0: 12 строк boilerplate
class Repository
{
    private EntityManager $em;
    private ClassMetadata $meta;

    public function __construct(EntityManager $em)
    {
        $this->em   = $em;
        $this->meta = new ClassMetadata($em);
    }
}

// PHP 8.0 + 8.1:
class Repository
{
    public function __construct(
        private EntityManager $em,
        private ClassMetadata $meta = null,   // 8.1: `new` в initializer
    ) {
        $this->meta ??= new ClassMetadata($em);
    }
}

// Ещё компактнее с readonly (8.2):
readonly class Repository
{
    public function __construct(
        public EntityManager  $em,
        public ClassMetadata $meta,
    ) {}
}
```

## 6. Именованные аргументы (8.0)

Читаемость на стороне вызова, особенно при 3+ параметрах с одинаковым типом.

```php
// До:
$query->setParameter(42, 'order_id');
$query->setParameter('open', 'status');
$query->setParameter(new \DateTimeImmutable(), 'created_at');

// PHP 8.0:
$query
    ->setParameter(name: 'order_id', value: 42)
    ->setParameter(name: 'status',   value: 'open')
    ->setParameter(name: 'created_at', value: new \DateTimeImmutable());

// Не боитесь переставить аргументы местами:
// ->setParameter(value: 42, name: 'order_id')  — тоже работает
```

## 7. Nullsafe оператор `?->` (8.0)

Убирает каскад `if (!is_null(...))`.

```php
// До:
$city = null;
if ($order !== null) {
    $address = $order->getAddress();
    if ($address !== null) {
        $city = $address->getCity();
    }
}

// PHP 8.0:
$city = $order?->getAddress()?->getCity();  // null, если любой шаг null
```

## 8. `never` как тип возврата (8.1)

Говорит стат-анализеру: «эта функция **никогда** не вернёт управление».

```php
// В DDD-домене:
function fail(string $reason): never
{
    throw new DomainException($reason);
}

// Стат-анализер знает, что кода ниже нет:
function process(Order $order): Money
{
    if ($order->isClosed()) {
        fail('Cannot modify a closed order');
    }

    // Анализирующий инструмент не считает $order->isClosed() === true
    // в этом блоке — нет ложных срабатываний.
    return $order->total();
}
```

## 9. First-class callable syntax (8.1)

Меньше «висячих» стрелочных функций, явнее передача callback'ов.

```php
// До:
$names = array_map(fn (User $u) => $u->fullName(), $users);
usort($orders, fn (Order $a, Order $b) => $a->createdAt() <=> $b->createdAt());

// PHP 8.1:
$names = array_map(User::fullName(...), $users);
usort($orders, static fn (Order $a, Order $b) =>
    $a->createdAt() <=> $b->createdAt()
);

// Можно сохранить и передать дальше без closures:
$comparator = Order::compareByTotal(...);
array_map($comparator, $a, $b);
```

## 10. Attributes (8.0) — для интеграции с фреймворками

Документация через `#[...]`, а не через docblocks, которые легко «слить».

```php
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderRequest
{
    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 255)]
    public string $customerName;

    #[Assert\GreaterThan(0)]
    public int $quantity;

    // До 8.0:
    // /**
    //  * @Assert\NotNull()
    //  * @Assert\Length(min=1, max=255)
    //  */
    // public string $customerName;
    //
    // Опечатка в @Assert\NotNul → молчаливое игнорирование.
    // #[Assert\NotNull] → ParseError, ошибка видна сразу.
}
```

---

## Что я бы выделил как «максимум за минимум усилий»

Если в проекте только PHP 8.0 и выше, три вещи дают наибольший прирост надёжности за наименьший рефакторинг:

1. **Enum** — заменяют магические строки/константы, match становится исчерпывающим.
2. **`readonly` + constructor promotion** — value objects и DTO перестают мутировать.
3. **Союзные/пересечённые типы + `never`** — стат-анализер (Psalm / PHPStan) начинает реально ловить ошибки, а не гадать по docblock'ам.

Остальное — вопрос вкуса и читаемости, а эти три — вопрос «упадёт ли в проде».

Хотите, возьму какой-то конкретный срез (например, доменный слой с DDD + Symfony) и покажу, как эти фичи выглядят в связке?

> ⏱ 22.2s · prompt 1290 tok · output 3027 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

The user is asking about PHP's type system: how it works, what strict_types does, how type coercions behave, typical pitfalls in practice, examples where typing helps and where it surprises.

Let me structure a thorough answer:

1. PHP's type system fundamentals - it's a dynamic, weakly-typed system with optional type declarations
2. strict_types - what it does, how it works (per-file, not per-function)
3. Type coercion rules - the big one: string↔number, bool conversions, array handling
4. Typical pitfalls
5. Examples where typing saves you
6. Examples where it surprises

Let me think about the key points:

**PHP type system characteristics:**
- Dynamic typing (types determined at runtime)
- Weak typing (implicit coercion)
- Optional type declarations (parameter types, return types)
- 8 internal types: int, float, string, bool, array, object, null, mixed
- Union types (8.0+), intersection types (8.2+), readonly (8.2+)
- Type coercion follows specific rules

**strict_types:**
- Must be declared at the top of the file (after `<?php`, before anything else except declare)
- Affects the file where it's declared, NOT the file where the function is defined
- When strict_types is on: no coercion for scalars (int, float, string, bool), arrays, null
- When off: coercive typing - PHP will try to convert
- Important: strict_types is a per-file setting, checked at the CALL SITE, not at the definition site

**Coercion rules (when strict_types is off):**
- "123" → 123 (string to int)
- "12.5" → 12.5 (string to float)
- "12.5" → 12 (string to int - truncated!)
- 1 → true (int to bool)
- 0 → false (int to bool)
- "false" → true (non-empty string to bool - classic gotcha!)
- "0" → false (special case!)
- "" → false (empty string to bool)
- "abc" → 0 (non-numeric string to int)
- [1,2,3] → "Array" (array to string)
- object → string only if __toString is defined
- null → anything (null coerces to many types)

**Pitfalls:**
1. "false" == true (string "false" is truthy)
2. "0" == false (but "0" is falsy - special case)
3. "" == 0 (empty string equals 0)
4. "abc" == 0 (in PHP 8, this is false! In PHP 7, "abc" == 0 was true)
5. null == "" (true)
6. null == 0 (true)
7. null == false (true)
8. The "==" comparison vs "==="
9. Array keys: string keys that look like integers are cast to int
10. isset() and null coalescing
11. strict_types is per-file, at call site

**PHP 8 changes to comparison:**
- Before PHP 8: "abc" == 0 → true (string was cast to 0)
- PHP 8: "abc" == 0 → false (non-numeric string compared as string, "abc" != "0")
- "123abc" == 123 → true (numeric prefix is extracted)
- "123abc" == 123 → true (still true in PHP 8)

Let me write concrete examples showing:
1. strict_types behavior
2. Coercion surprises
3. Where typing saves
4. Where it surprises

I'll structure this clearly with code examples.

</details>

**Кандидат:**

# Система типов PHP: как устроено, где спасает, где подставляет

## Базовая модель

PHP — динамически типизированный язык со **слабым** приведением. Тип переменной определяется в рантайме, а не на этапе объявления. Декларации типов (параметры, return) — это **assertions на границе**, а не статическая типизация:

```php
function add(int $a, int $b): int
{
    return $a + $b;
}

// В рантайме PHP проверяет: «а ты точно int?»
// Если нет — зависит от strict_types в месте вызова.
```

Внутри тела функции переменные по-прежнему не имеют фиксированного типа — тип «живёт» в значении, а не в слоте памяти.

---

## `strict_types`: что реально происходит

### Ключевое правило

`strict_types` — **свойство файла вызывающего**, а не файла, где функция определена.

```php
// lib.php — БЕЗ strict_types
function multiply(int $x, int $y): int {
    return $x * $y;
}
```

```php
// app.php — С strict_types
<?php
declare(strict_types=1);
require 'lib.php';

multiply("3", "4");   // ❌ TypeError: Argument #1 must be of type int, string given
```

```php
// app2.php — БЕЗ strict_types
<?php
require 'lib.php';

multiply("3", "4");   // ✅ 12 — PHP превращает "3" → 3, "4" → 4
```

Это ловушка №1: вы поставили `strict_types` в `app.php`, а функция из вендор-пакета без него — и типизация работает «полуслабо».

### Что именно блокирует strict_types

| Приведение | strict_types = 1 | strict_types = 0 |
|---|---|---|
| `"123"` → `int` | ❌ TypeError | ✅ 123 |
| `"12.5"` → `int` | ❌ TypeError | ✅ 12 (обрезание!) |
| `"12.5"` → `float` | ❌ TypeError | ✅ 12.5 |
| `1.0` → `int` | ❌ TypeError | ✅ 1 |
| `1` → `bool` | ❌ TypeError | ✅ true |
| `"abc"` → `int` | ❌ TypeError | ✅ 0 |
| `null` → `int` | ❌ TypeError (если не `?int`) | ✅ 0 |
| `[1]` → `string` | ❌ TypeError | ❌ **Всегда** TypeError |

Обратите внимание: массивы и объекты **никогда** не приводятся к скалярам, независимо от strict_types.

---

## Таблица приведений (когда strict_types = 0)

Это то, что на практике вызывает 90% «PHP-удивлений»:

```php
// --- Int → Bool ---
var_dump((bool) 0);          // bool(false)
var_dump((bool) 1);          // bool(true)
var_dump((bool) -5);         // bool(true)

// --- String → Bool (КЛАССИКА) ---
var_dump((bool) "");         // bool(false)   — пустая строка
var_dump((bool) "0");        // bool(false)   — ТОЛЬКО "0"!
var_dump((bool) "false");    // bool(TRUE)    — не пустая строка → true
var_dump((bool) "null");     // bool(TRUE)
var_dump((bool) "abc");      // bool(TRUE)

// --- String → Int ---
var_dump((int) "123");       // 123
var_dump((int) "12.5");      // 12   ← обрезает, не округляет
var_dump((int) "0x1A");      // 0    ← не разбирает hex
var_dump((int) "100px");     // 100  ← берёт числовой префикс
var_dump((int) "abc");       // 0

// --- Int/Float → String ---
var_dump((string) 42);       // "42"
var_dump((string) 1.5);      // "1.5"
var_dump((string) 1.0E+30);  // "1.0E+30"  ← экспоненция!
var_dump((string) -0);       // "0"        ← не "-0"
```

---

## Сравнение `==`: где PHP удивляет

### До PHP 8 (частые баги)

```php
"abc" == 0;      // true  ← строка приводится к 0, 0 == 0
"" == 0;         // true
null == 0;       // true
null == "";      // true
" " == 0;        // true  ← пробел → 0
" " == "";       // true  ← пробел → ""
"0" == false;    // true
"0" == 0;        // true
"0" == null;     // true
```

### PHP 8: правила сравнения строк и чисел ужесточены

```php
// PHP 8.0+:
"abc" == 0;      // false ← строка НЕ приводится к числу, сравнивается "abc" == "0"
"abc" == "0";    // false
"123" == 123;    // true  ← обе стороны числовые → сравниваются как числа
"123abc" == 123; // true  ← числовой префикс извлекается
"123abc" == 1234; // false

// Но:
"12.5" == 12.5;  // true
"12.5" == 12;    // false  ← 12.5 ≠ 12
```

### Ловушка, которая выживает даже в PHP 8

```php
// null всё ещё «равнивается» почти всему:
null == 0;        // true
null == "";       // true
null == false;    // true
null == [];       // true
null == "abc";    // false  ← единственное исключение

// А вот это — боль:
if ($value == null) {
    // true для: null, false, 0, "", "0", []
    // Если $value — строка из API, это ТИХИЙ баг
}
```

---

## Где типизация спасает

### 1. Граница входа (API, CLI, DB)

```php
declare(strict_types=1);

// Без типов:
function processOrder($id, $qty) {
    // $id может быть "abc", null, [1,2], "0x1F"
    // $qty может быть "2.5", "2,5", true
    $total = $id * $qty;  // что здесь?
}

// С типами:
function processOrder(int $orderId, int $quantity): float
{
    if ($quantity <= 0) {
        throw new DomainException('Quantity must be positive');
    }
    // $orderId — гарантированно целое.
    // $quantity — гарантированно целое, > 0.
    // Сложнее «потерять» null или "abc".
    return $quantity * 100.0;
}
```

### 2. `match` + enum: исчерпывающее ветвление

```php
declare(strict_types=1);

enum PaymentMethod: string {
    case Card   = 'card';
    case Bank   = 'bank';
    case Crypto = 'crypto';
}

function fee(PaymentMethod $method, float $amount): float
{
    // match с enum: если добавите case в enum,
    // стат-анализер (PHPStan level 8, Psalm) — ошибка.
    // Если не добавите — молчаливый default → баг.
    return match ($method) {
        PaymentMethod::Card   => $amount * 0.029 + 0.30,
        PaymentMethod::Bank   => $amount * 0.005,
        PaymentMethod::Crypto => $amount * 0.01,
        // Нет default. Нет «а что если прилетит 'CARD'?»
        // Прилетит — TypeError на границе, не на 3-й неделе в проде.
    };
}

// fee("card", 100.0) → TypeError. Не 0. Не false. Ошибка.
```

### 3. `readonly` + конструкторная промоторация: невозможность «забыть» инициализацию

```php
declare(strict_types=1);

readonly class Invoice
{
    public function __construct(
        public readonly string $number,
        public readonly int    $amountCents,
        public readonly \DateTimeImmutable $issuedAt,
    ) {}

    public function total(): float
    {
        return $this->amountCents / 100;
    }
}

// Без readonly:
// $inv = new Invoice("INV-1", 5000, new \DateTimeImmutable());
// $inv->amountCents = 0;  // никто не заметит, пока не придёт платёж
// $inv->number = null;    // и это тоже

// С readonly:
// $inv->amountCents = 0;  // ❌ Error: Cannot modify readonly property
```

---

## Где типизация удивляет

### 1. `strict_types` не защищает тело функции

```php
<?php
declare(strict_types=1);

function safe(int $x): int
{
    $x = "3.7";  // ✅ Никакой ошибки. Типизация — только на границе.
    // Внутри — обычный weakly-typed PHP.
    return (int) $x;  // 3, не 4. Обрезание, а не округление.
}

echo safe(3);  // 3
```

Типы на параметрах и return — это **контракт между вызывающим и определяющим**, а не аннотация для всего тела.

### 2. `null` и `isset`

```php
declare(strict_types=1);

function find(string $key): ?int
{
    $data = ['a' => 1, 'b' => null, 'c' => 0];
    return $data[$key] ?? null;
}

// $result = find('b');  // null
// $result = find('c');  // 0
// $result = find('x');  // null
//
// if ($result) { ... }  ← ТИХО пропустит и 0, и null!
if ($result !== null) { ... }  // вот так — иначе
```

### 3. Массивы: ключи-строки и `int`

```php
$arr = [1 => 'a', "2" => 'b', "3" => 'c'];
// Рантайм: [1 => 'a', 2 => 'b', 3 => 'c']
// Ключ "2" стал int 2. Бесшумно.

// Но:
$arr2 = ["01" => 'a', "02" => 'b'];
// Ключи "01" и "02" ОСТАЮТСЯ строками! (не валидные int)
// array_keys($arr2) → ["01", "02"]

// И в foreach:
foreach ($arr2 as $key => $val) {
    // $key === "01" (string), а не 1 (int)
}
```

### 4. `bool` из внешних источников

```php
// JSON из API: {"active": true}
// JSON из API: {"active": "yes"}
// JSON из API: {"active": 1}
// JSON из API: {"active": "false"}   ← строка!

function isActive(mixed $raw): bool
{
    // ❌ Опасно:
    // return (bool) $raw;
    // "false" → true. "0" → false. 0 → false. null → false.
    // Все они — «неактивно»? Нет, "false" — это да, но "1" тоже true.

    // ✅ Явно:
    return match (true) {
        $raw === true, $raw === 1, $raw === "true", $raw === "1" => true,
        default => false,
    };
}
```

### 5. `float` и «равенство»

```php
declare(strict_types=1);

function isDiscountApplied(float $total, float $discount): bool
{
    // ❌
    // return $total - $discount === 0.0;
    // 0.1 + 0.2 !== 0.3 → всегда

    // ✅
    return abs($total - $discount) < 0.000001;
}

// Или лучше: работаем в центах (int).
function isDiscountApplied(int $totalCents, int $discountCents): bool
{
    return $totalCents - $discountCents === 0;
}
```

### 6. `string` как «любой объект с `__toString`»

```php
class OrderId {
    public function __toString(): string {
        return $this->id;
    }
    public function __construct(private string $id) {}
}

function log(string $ref): void {
    echo $ref;
}

$orderId = new OrderId('ORD-99');

// strict_types = 1:
log($orderId);  // ❌ TypeError. String, а не object.

// strict_types = 0:
log($orderId);  // ✅ "ORD-99" — вызывается __toString

// А вот это работает всегда:
log((string) $orderId);  // ✅ явное приведение
log("Order: " . $orderId);  // ✅ concat вызывает __toString
```

---

## Практические правила, которые я рекомендую

```php
<?php
// 1. В КАЖДОМ файле:
declare(strict_types=1);

// 2. На границах (контроллеры, репозитории, DTO) — явные типы:
function fromArray(array $data): Order
{
    return new Order(
        id:    (int) $data['id'],          // явное приведение — видно, что мы знаем
        qty:   (int) ($data['qty'] ?? 1),
        total: (float) $data['total'],
    );
}

// 3. В домене — enum, readonly, match без default:
enum Role: string { case Admin = 'admin'; case User = 'user'; }

readonly class Permission
{
    public function __construct(public readonly Role $role) {}
}

// 4. Нигде:
//    if ($x == null)        → if ($x === null)
//    if ($x) для строк       → if ($x !== '' && $x !== null)
//    (bool) $apiResponse    → явный match / валидация
//    $int = "12.5"          → round() или intdiv(), если это осознанно

// 5. Стат-анализ (PHPStan level 8+ / Psalm 8) — не опция, а часть CI.
//    strict_types ловит 80% проблем на границе.
//    Стат-анализер ловит оставшиеся 20% внутри.
```

---

## Итого: ментальная модель

```
PHP-типизация — это НЕ статическая типизация.
Это — runtime-assertions на границах функции/метода,
плюс weakly-typed тело,
плюс таблица приведений, которую нужно знать наизусть,
плюс строчка declare(strict_types=1), которая
  в 90% случаев убирает «тихие» баги,
  а в 10% случаев ломает вендор-код, который
  на неё не рассчитан.
```

Типы в PHP — **страховка на границе**, а не **система гарантии на всём пути**. Стат-анализер (PHPStan/Psalm) — то, что превращает эту страховку в нечто близкое к статической типизации. Без него `int $x` — это просто «кикни, если не int», а не «все вычисления с $x — целочисленные».

> ⏱ 35.4s · prompt 3680 tok · output 4709 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user is asking about exception handling design in PHP applications. They want to know:
1. My approach to custom exception hierarchies
2. Wrapping third-party/external exceptions
3. What constitutes a method's contract
4. Common anti-patterns

This is a deep architectural question. I should give a thorough, practical answer with code examples. Let me structure this well:

- Start with the core principle: exceptions are a contract
- Build the hierarchy (Domain, Infrastructure, Application)
- Show wrapping patterns
- Define what a method's contract means in terms of exceptions
- List anti-patterns with concrete bad examples

I'll write in Russian since the conversation is in Russian. Let me be thorough but not verbose. Code examples should be realistic and show the "why" behind each pattern.

</details>

**Кандидат:**

# Работа с исключениями в PHP

## Главный принцип

Исключение — часть контракта метода. Если метод может бросить `X`, вызывающий обязан знать об этом. В PHP нет `throws` в сигнатуре (как в Java/C#), поэтому контракт живёт в **документации, иерархии имён и стат-анализаторе**. Это делает иерархию не «косметикой», а единственным способом сообщить «что здесь может пойти не так».

---

## Иерархия: три слоя, три вопроса

Я строю иерархию вокруг вопроса **«кто и где это может вызвать»**, а не вокруг «что произошло».

```
Exception (SPL)
├── DomainException                      ← ядро: бизнес-правило нарушено
│   ├── InvalidOrderException
│   ├── InsufficientFundsException
│   └── DuplicateResourceException
│
├── ApplicationException                 ← оркестрация: сценарий не выполнен
│   ├── ValidationException
│   ├── UnauthorizedException
│   └── NotFoundException
│
├── InfrastructureException              ← внешний мир: DB, HTTP, queue
│   ├── DatabaseException
│   ├── HttpException
│   └── QueueException
│
└── UnhandledException                   ← «не должно было случиться»
    └── (бросается только в catch-all)
```

### Зачем три слоя, а не один `MyAppException`

```php
// Плохо: один класс, все в нём.
catch (MyAppException $e) { /* а что делать? */ }

// Хорошо: вызывающий знает, ЧТО поймать.
try {
    $orderService->process($orderId);
} catch (InvalidOrderException $e) {
    // Доменное правило. Показываем пользователю. 422.
    return $this->unprocessable($e->getMessage());
} catch (DatabaseException $e) {
    // Инфраструктура. Логируем, не показываем. 503.
    return $this->serviceUnavailable('Please try again');
} catch (Throwable $e) {
    // Не должно было случиться. 500. Логируем со stack trace.
    return $this->internalError();
}
```

Ключ: **вызывающий выбирает глубину** `catch`-а. Доменное — бизнес-логика. Инфраструктурное — retry/fallback. Нечего ловить — прокинуть выше.

---

## Доменные исключения: что несёт в себе

```php
declare(strict_types=1);

abstract class DomainException extends \RuntimeException
{
    // Код, который фронтенд может использовать для i18n / специфичной UI-логики.
    // НЕ HTTP-статус. НЕ SQL-код. Бизнес-код.
    public function __construct(
        string              $message,
        private readonly string $code   = 'DOMAIN_ERROR',
        private readonly array  $context = [],
        ?\Throwable         $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function code(): string   { return $this->code; }
    public function context(): array { return $this->context; }
}

final class InsufficientFundsException extends DomainException
{
    public function __construct(
        private readonly int $requiredCents,
        private readonly int $availableCents,
    ) {
        parent::__construct(
            message: 'Insufficient funds',
            code:    'INSUFFICIENT_FUNDS',
            context: [
                'required'  => $requiredCents,
                'available' => $availableCents,
                'shortfall' => $requiredCents - $availableCents,
            ],
        );
    }

    public function shortfallCents(): int
    {
        return $this->requiredCents - $this->availableCents;
    }
}
```

### Почему `context` массивом, а не отдельными свойствами

- Универсальный: `array $ctx = $e->context();` работает для всех `DomainException`.
- Не нужен полиморфный `catch` для каждого подкласса, чтобы вытащить данные.
- При сериализации в JSON для API — просто `json_encode($e->context())`.

### Почему НЕ наследую `Exception` напрямую

`RuntimeException` — более семантически точный. `LogicError` — для багов в коде. `Error` (не `Exception`) — для «программист, ты ошибся». Я использую:

| Базовый класс | Когда |
|---|---|
| `DomainException extends \RuntimeException` | Бизнес-правило нарушено |
| `ApplicationException extends \RuntimeException` | Оркестрация не может выполнить |
| `InfrastructureException extends \RuntimeException` | Внешний сервис/ресурс недоступен |
| `ProgrammingError extends \LogicException` | Невозможное состояние, баг |

---

## Оборачивание чужих исключений

### Правило: **никогда не пропускай инфраструктурное через доменную границу**

```php
// ❌ АНТИПАТТЕРН: Doctrine-исключение лезет в домен
class OrderRepository
{
    public function findById(int $id): Order
    {
        // Бросит ORMInvalidArgumentException, DBAL\Exception\ConnectionException,
        // PDOException, … Вызывающий ДОМЕНА не обязан знать про Doctrine.
        return $this->em->getRepository(Order::class)->find($id);
    }
}

// ❌ Ещё хуже: catch и «переупаковка» в неконкретный класс
class OrderRepository
{
    public function findById(int $id): Order
    {
        try {
            return $this->em->getRepository(Order::class)->find($id);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Something went wrong', 0, $e);
            // Вызывающий теряет типизацию. Нечего ловить.
        }
    }
```

```php
// ✅ Правильно: репозиторий — граница. Оборачиваем в InfrastructureException.
class OrderRepository
{
    public function __construct(
        private EntityManager $em,
    ) {}

    public function findById(int $id): Order
    {
        try {
            $entity = $this->em->getRepository(Order::class)->find($id);
            if ($entity === null) {
                throw new OrderNotFoundException($id);  // доменное!
            }
            return $entity;
        } catch (ORMException|DBALException $e) {
            // Оборачиваем: вызывающий знает, что «БД недоступна»,
            // а не что «Doctrine-метод `find` бросил что-то».
            throw new DatabaseException(
                message: 'Failed to load order #{id}',
                context: ['order_id' => $id],
                previous: $e,
            ) from $e;
        }
    }
}
```

### Правило `from`

```php
throw new DatabaseException('...', $e) from $e;
// vs
throw new DatabaseException('...', 0, $e);

// `from` — синтаксический сахар: устанавливает $previous и
// делает его «корнем» в chain. Читаемее.
```

### Глубина оборачивания

```
HTTP layer
  └── catch DomainException       → 4xx
  └── catch DatabaseException     → 503
  └── catch Throwable             → 500

Application service
  └── бросает DomainException
  └── бросает ApplicationException
  └── НЕ бросает DatabaseException (это ниже)

Repository / infrastructure
  └── бросает DomainException (наш доменный)
  └── бросает InfrastructureException (обёртку)
  └── НЕ пропускает PDOException / ORMException наверх
```

Каждый слой знает **свой** набор исключений. Выше — уже абстракция.

---

## Контракт метода: что значит «может бросить»

PHP не имеет `throws`, поэтому контракт = **имена + иерархия + докблок**.

```php
/**
 * Запрашивает и создаёт платёж.
 *
 * @throws InsufficientFundsException  если на счёте не хватает средств.
 * @throws DuplicatePaymentException   если платёж с таким ID уже существует.
 * @throws PaymentGatewayException     если внешний шлюз недоступен или отклонил.
 * @throws ProgrammingError            если передан невалидный статус (баг).
 *
 * НЕ бросает:
 *  - null / false как результат ошибки
 *  - \Exception напрямую
 *  - \Error (TypeError и т.п.) — если это случилось, это баг, а не контракт.
 *
 * @return Payment  гарантированно в статусе `Succeeded` или `Pending`
 */
function createPayment(
    int         $orderId,
    int         $amountCents,
    string      $cardToken,
): Payment {
    // ...
}
```

### Что я считаю контрактом

| Входит | Не входит |
|---|---|
| Определённый набор `DomainException` / `ApplicationException` | `\Exception` как «на случай чего» |
| `InfrastructureException` на границе (репозиторий, HTTP-клиент) | `PDOException`, `GuzzleException` из-за слоя |
| `ProgrammingError` / `\LogicException` для «невозможно» | `TypeError` как часть контракта (это баг) |
| Документация: что, когда, с каким `code` | Молчаливое возвращение `null` при ошибке |

### `ProgrammingError` для «невозможного»

```php
final class ProgrammingError extends \LogicException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

// Использование:
final class Order
{
    public function applyDiscount(Discount $discount): void
    {
        $status = $this->status;

        if ($status === OrderStatus::Open) {
            $this->discount = $discount;
            return;
        }

        if ($status === OrderStatus::Shipped) {
            // Теоретически невозможно, если service-слой проверяет.
            // Но если дошло сюда — баг. Не DomainException. Не 500. БУГ.
            throw new ProgrammingError(
                "Cannot apply discount to {$status->value} order",
            );
        }

        throw new ProgrammingError(
            "Unexpected order status: {$status->value}"
        );
    }
}
```

Почему не `DomainException`: `DomainException` — «пользователь/система сделала что-то допустимое, но неправильное». `ProgrammingError` — «код вёл себя не так, как я его писал». Обработка разная: первая — 4xx, вторая — 500 + алерт.

---

## Антипаттерны

### 1. `catch (\Exception $e) { return null; }`

```php
// ❌ Вызывающий не знает, случилось ли.
function findOrder(int $id): ?Order
{
    try {
        return $repo->findById($id);
    } catch (\Exception $e) {
        return null;  // «не нашёлся» и «БД упала» — одно и то же
    }
}

// ✅ Либо NotFound, либо DatabaseException. Вызывающий различает.
```

### 2. Переброс с потерей контекста

```php
// ❌
try {
    $gateway->charge($amount);
} catch (GatewayException $e) {
    throw new \RuntimeException('Payment failed');
    // Нет $previous. Нет code. Нет amount.
    // В логе — «Payment failed». Стек — пустой. Что делать?
}

// ✅
try {
    $gateway->charge($amount);
} catch (GatewayException $e) {
    throw new PaymentGatewayException(
        message: 'Charge declined by gateway',
        context: ['amount' => $amount, 'gateway_code' => $e->code()],
    ) from $e;
}
```

### 3. Исключение как control flow

```php
// ❌
function getStatus(string $id): string
{
    try {
        return $repo->find($id)->status();
    } catch (EntityNotFoundException $e) {
        return 'not_found';  // «исключение» = «нет»
    }
}

// Это не исключение. Это опциональное значение.
// Используем null / Option / Result.
function getStatus(string $id): ?string
{
    $entity = $repo->find($id);
    return $entity?->status();
}

// Исключение — для «не должно было случиться», а не «обычный путь».
```

### 4. `empty()` / фалсифицируемый результат вместо исключения

```php
// ❌
function createOrder(array $data): Order
{
    $order = new Order($data);
    if (!$order->isValid()) {
        return null;  // или false, или [] — вызывающий гадает
    }
    return $order;
}

// ✅
function createOrder(array $data): Order
{
    $order = new Order($data);
    $errors = $order->validate();
    if ($errors !== []) {
        throw new ValidationException(
            message: 'Validation failed',
            context: ['errors' => $errors],
        );
    }
    return $order;
}
```

### 5. Полиморфный `catch` по базе

```php
// ❌
catch (\Exception $e) {
    if ($e instanceof DomainException) { /* ... */ }
    elseif ($e instanceof DatabaseException) { /* ... */ }
    else { /* 500 */ }
    // Это if/else в одежде catch. Не читается.
}

// ✅
catch (DomainException $e)       { /* 4xx */ }
catch (DatabaseException $e)     { /* 503 */ }
catch (Throwable $e)             { /* 500 */ }
```

### 6. `finally` как «сброс состояния»

```php
// ❌
function process() {
    $conn = $this->getConnection();
    try {
        $conn->beginTransaction();
        // ...
        $conn->commit();
    } catch (\Exception $e) {
        $conn->rollBack();
        throw $e;
    } finally {
        $conn->close();  // ← закрывает даже при успехе,
                         //   но если commit() бросил —
                         //   rollBack уже вызван, а close ещё нет.
    }
    // Состояние: что если getConnection() сам бросил?
    // $conn не определён. finally не сработает. ОК.
    // Но что если бросил ПОСЛЕ getConnection, НО до try?
}

// ✅
function process() {
    $conn = $this->getConnection();
    $conn->beginTransaction();

    try {
        // ...
        $conn->commit();
    } catch (\Throwable $e) {
        try { $conn->rollBack(); } catch (\Throwable) {}
        throw $e;
    } finally {
        $conn->close();
    }
}
```

### 7. «Свободный» `catch` в доменном слое

```php
// ❌ Домен не знает про HTTP, не знает про логгер.
class Order
{
    public function cancel(): void {
        try {
            // ...
        } catch (\Exception $e) {
            $this->logger->error($e);   // ← логгер в домене
            $this->http->send(500);     // ← HTTP в домене
        }
    }

// ✅ Домен бросает. Слой выше решает.
class Order
{
    public function cancel(): void {
        if ($this->status === OrderStatus::Shipped) {
            throw new DomainException(
                'Cannot cancel shipped order',
                code: 'CANNOT_CANCEL_SHIPPED',
            );
        }
        // ...
    }
}
```

---

## Итоговая схема

```
┌─────────────────────────────────────────────────────────────┐
│  HTTP / CLI layer                                           │
│  catch DomainException        → 4xx + $e->context()        │
│  catch InfrastructureException→ 503 + retry logic           │
│  catch Throwable              → 500 + log full trace        │
├─────────────────────────────────────────────────────────────┤
│  Application service                                          │
│  бросает DomainException (из домена)                         │
│  бросает ApplicationException                                │
│  НЕ бросает InfrastructureException                          │
│  НЕ ловит — прокидывает вверх                                │
├─────────────────────────────────────────────────────────────┤
│  Domain (entity, value object, policy)                       │
│  бросает DomainException                                     │
│  бросает ProgrammingError (баг)                               │
│  НЕ знает про DB, HTTP, логгер                                │
├─────────────────────────────────────────────────────────────┤
│  Infrastructure (repo, HTTP client, queue)                    │
│  бросает InfrastructureException (обёртку)                   │
│  бросает DomainException (наш, если бизнес-правило)          │
│  НЕ пропускает PDOException / GuzzleException наружу         │
└─────────────────────────────────────────────────────────────┘
```

Каждый слой ловит **только то, что понимает**, и бросает **только то, что его вызывающий понимает**. Всё остальное — `from $previous`, и выше.

> ⏱ 32.7s · prompt 7600 tok · output 4351 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о SOLID — какой принцип чаще всего нарушается в реальном коде, почему, и просит типовой пример нарушения + рефакторинг. Это продолжение нашего разговора о PHP-архитектуре, DDD, чистом коде.

Давай подумаю, какой принцип реально чаще всего нарушается:

1. **S — Single Responsibility Principle** — чаще всего нарушается. "Класс делает одну вещь" звучит банально, но на практике:
   - Контроллеры Symfony, которые валидируют, формируют запрос, мапят результат, отправляют email
   - Сервисы-боги (OrderService на 2000 строк)
   - Repository, который ищет, мапит, кэширует, логирует

2. **O — Open/Closed** — нарушается через if/else/switch на типах. "Если добавить новый способ оплаты — правим 5 файлов."

3. **L — Liskov Substitution** — нарушается через наследование, где подкласс нарушает предпосылки. В PHP — через наследование Entity, где конкретная сущность переопределяет поведение.

4. **I — Interface Segregation** — нарушается через "жирные" интерфейсы (Repository c 20 методами, которые 80% реализаций не используют).

5. **D — Dependency Inversion** — нарушается через зависимость от конкретных классов в контроллерах, через service locator.

Я считаю, что **S (SRP)** — самое частое и самое болезненное нарушение. Потому что:
- Остальные (O, L, I, D) — это про структуру интерфейсов и наследование. Их нарушение видно и чинится точечно.
- SRP — про семантику. "Что делает этот класс?" — и ответ "всё" — это не баг, а культурная норма.
- В PHP/Symfony это особенно проявляется: контроллер → сервис → репозиторий, где сервис — это "бог".
- Причины: легкость добавления метода в существующий класс vs. создание нового. Лень. Ненависть к "избыточной абстракции".
- Последствия: тестирование становится кошмаром, изменение одного поведения ломает другое, код невозможно понимать.

Но я хочу быть честным: OCP тоже очень часто нарушается. И D тоже. Но SRP — корневое нарушение, из которого вырастают другие.

Давай покажу типовой пример. Самый реалистичный для PHP/Symfony: **контроллер/сервис, который делает всё сразу**. Или лучше — сервис, который и валидирует, и считает, и отправляет email, и пишет в лог, и управляет транзакцией.

Пример: `OrderService::process()` на 150 строк, который:
1. Загружает заказ
2. Валидирует
3. Считает скидку
4. Проверяет баланс
5. Создает платёж
6. Обновляет статус
7. Отправляет email
8. Логгирует
9. Пушит в очередь

Это классический "бог". Нарушение SRP, а заодно D (зависит от EmailSender, PaymentGateway, Logger, Queue, Repository — всё сразу) и O (добавить новый способ оплаты — правим этот метод).

Рефакторинг:
- Выделить Domain Policy (OrderPolicy / PricingPolicy)
- Выделить Application Service (оркестрация)
- Выделить Infrastructure (PaymentGateway, EmailNotifier)
- Каждый класс — одна ответственность

Покажу до/после на конкретном коде. Сделаю компактно, но по-настоящему.

Структура ответа:
1. Короткий вердикт: SRP, и почему именно он (2-3 абзаца)
2. Типовой пример нарушения — код "до"
3. Почему это больно — 3-4 пункта
4. Рефакторинг — код "после"
5. Что именно изменилось и почему — таблица или список
6. Честная оговорка: SRP не означает "один метод на класс"

Буду писать на русском, как и весь разговор. Код на PHP.

</details>

**Кандидат:**

# Single Responsibility — самое частое и самое дорогое нарушение

## Почему именно SRP

Из пяти принципов SOLID именно SRP нарушается чаще всего — и не потому, что он самый сложный для понимания (он, наоборот, звучит как «каждый класс делает одну вещь»), а потому что **он противоречит естественному способу, которым мы пишем код**.

Когда у тебя есть `OrderService` и нужно добавить отправку email — самый короткий путь: добавить метод в существующий класс. Создать `OrderNotifier`, внедрить в сервис, прогнать через DI-контейнер — это три лишних действия ради одной строчки. И через полгода `OrderService` на 1 400 строк делает валидацию, расчёт скидки, вызов платёжного шлюза, отправку email, запись в аналитику и управление транзакцией.

Остальные принципы (OCP, LSP, ISP, DIP) нарушаются точечно — у конкретного интерфейса, конкретной иерархии. SRP нарушается **системно**: вырастает класс-бог, и в нём одновременно нарушаются и OCP (чтобы добавить сценарий — правим его), и DIP (он зависит от 7 конкретных классов), и ISP (интерфейс на 15 методов, из которых каждая реализация использует 4).

Главная причина — **семантическая лень**. SRP требует ответить на вопрос: «что делает этот класс и *только* это?» — и честно признать, что «создание заказа» и «отправка уведомления о заказе» — это разные вещи, даже если они происходят в одном сценарии.

---

## Типовое нарушение: `OrderService::process()`

```php
declare(strict_types=1);

// src/Service/OrderService.php
// 140 строк. Один публичный метод. Зависимости: 7 штук.

final class OrderService
{
    public function __construct(
        private OrderRepository      $orderRepo,
        private PaymentGateway       $payment,
        private EmailSender          $email,
        private LoggerInterface      $logger,
        private EventDispatcher      $events,
        private AnalyticsClient      $analytics,
        private EntityManager        $em,
    ) {}

    public function process(int $orderId): Order
    {
        $em->beginTransaction();
        try {
            // 1. Загрузка
            $order = $this->orderRepo->find($orderId);
            if ($order === null) {
                throw new NotFoundException("Order {$orderId} not found");
            }

            // 2. Валидация бизнес-правил
            if ($order->isClosed()) {
                throw new DomainException('Order is already closed');
            }
            if ($order->items() === []) {
                throw new DomainException('Order has no items');
            }

            // 3. Расчёт скидки
            $base = $order->subtotal();
            $discount = 0;
            if ($order->hasTag('vip')) {
                $discount = (int) ($base * 0.10);
            } elseif ($order->items()->count() >= 5) {
                $discount = (int) ($base * 0.05);
            }
            $order->applyDiscount($discount);

            // 4. Проверка баланса
            $amount = $order->total();
            if ($amount <= 0) {
                throw new DomainException('Order total is zero or negative');
            }

            // 5. Платёж
            $paymentResult = $this->payment->charge(
                email:   $order->customer()->email(),
                amount:  $amount,
                cardToken: $order->customer()->cardToken(),
                metadata: ['order_id' => $orderId],
            );

            if ($paymentResult->status === 'declined') {
                $this->logger->warning('Payment declined', [
                    'order' => $orderId,
                    'reason' => $paymentResult->reason,
                ]);
                $this->email->send(
                    to:      $order->customer()->email(),
                    subject: 'Payment failed',
                    body:    'Your payment was declined: ' . $paymentResult->reason,
                );
                $order->markAsFailed($paymentResult->reason);
                $em->flush();
                $em->commit();
                return $order;
            }

            // 6. Обновление статуса
            $order->markAsPaid($paymentResult->transactionId);
            $em->flush();

            // 7. Email об успехе
            $this->email->send(
                to:      $order->customer()->email(),
                subject: 'Order confirmed',
                body:    "Order #{$orderId} confirmed. Total: {$amount}",
            );

            // 8. Аналитика
            $this->analytics->track('order_completed', [
                'order_id' => $orderId,
                'total'    => $amount,
                'discount' => $discount,
                'items'    => $order->items()->count(),
            ]);

            // 9. Событие
            $this->events->dispatch(new OrderCompletedEvent($order));

            // 10. Лог
            $this->logger->info('Order processed', ['order' => $orderId]);

            $em->commit();
            return $order;
        } catch (\Throwable $e) {
            $em->rollBack();
            $this->logger->error('Order processing failed', [
                'order'  => $orderId,
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
```

### Почему это больно

| Проблема | Проявление |
|---|---|
| **Не тестируется** | Чтобы юнит-тестнуть «расчёт скидки», нужно мокнуть 7 зависимостей. Из них 5 не имеют отношения к скидке. |
| **OCP нарушен** | Добавить «промокод на 20%» → правим метод `process()`. Добавить «платёж через СБП» → опять `process()`. Каждый новый сценарий — патч в одном файле. |
| **DIP нарушен** | Доменная логика (валидация, расчёт) зависит от `PaymentGateway`, `EmailSender`, `AnalyticsClient`. Домен знает про аналитику. |
| **Транзакция шире, чем нужно** | Email отправляется внутри `$em->commit()`. Если email-сервер недоступен — откатывается транзакция. Платёж списан, а заказ «не оплачен». |
| **Один `catch` на всё** | Не distinguished: упал платёжный шлюз или сломался расчёт скидки. В логе — одно и то же. |
| **Имя лжёт** | `process()` — не процесс. Это 10 разных вещей, склеенных в одну. |

---

## Рефакторинг

Разбиваю на **три слоя по трём вопросам**: *что* считать (домен), *в каком порядке* делать (аппликация), *как* доставить (инфраструктура).

### 1. Домен: расчёт и правила — без внешних зависимостей

```php
// src/Domain/Order/Order.php
final class Order
{
    public function isProcessable(): bool
    {
        return !$this->isClosed()
            && $this->items()->count() > 0
            && $this->subtotal() > 0;
    }

    public function assertProcessable(): void
    {
        if ($this->isClosed()) {
            throw new DomainException('Order is already closed', code: 'ORDER_CLOSED');
        }
        if ($this->items()->count() === 0) {
            throw new DomainException('Order has no items', code: 'ORDER_EMPTY');
        }
        if ($this->subtotal() <= 0) {
            throw new DomainException('Order total is non-positive', code: 'ORDER_INVALID_TOTAL');
        }
    }

    public function applyDiscount(int $discountCents): void
    {
        if ($discountCents < 0 || $discountCents > $this->subtotal()) {
            throw new ProgrammingError('Discount out of bounds');
        }
        $this->discountCents = $discountCents;
    }

    public function markAsPaid(string $transactionId): void
    {
        $this->status = OrderStatus::Paid;
        $this->transactionId = $transactionId;
    }

    public function markAsFailed(string $reason): void
    {
        $this->status = OrderStatus::Failed;
        $this->failureReason = $reason;
    }
}

// src/Domain/Order/PricingPolicy.php
// Чистый домен. Ни DI, ни DB, ни email. Только вход → выход.
final class PricingPolicy
{
    public function calculateDiscount(int $subtotalCents, Order $order): int
    {
        if ($order->hasTag('vip')) {
            return (int) ($subtotalCents * 0.10);
        }
        if ($order->items()->count() >= 5) {
            return (int) ($subtotalCents * 0.05);
        }
        return 0;
    }
}

// src/Domain/Order/OrderRules.php
final class OrderRules
{
    public function assertProcessable(Order $order): void
    {
        $order->assertProcessable();  // делегируем — или инкапсулируем здесь
    }

    public function assertChargeable(Order $order): void
    {
        if ($order->total() <= 0) {
            throw new DomainException('Cannot charge zero', code: 'CHARGE_ZERO');
        }
    }
}
```

Тестирование: **ноль моков**. `PricingPolicy` и `OrderRules` — чистые функции.

```php
final class PricingPolicyTest extends TestCase
{
    public function testVipGetsTenPercent(): void
    {
        $policy = new PricingPolicy();
        $order  = OrderFactory::create(subtotal: 10_000, tags: ['vip']);

        $this->assertSame(1_000, $policy->calculateDiscount(10_000, $order));
    }

    public function testBulkGetsFivePercent(): void
    {
        $policy = new PricingPolicy();
        $order  = OrderFactory::create(subtotal: 5_000, itemCount: 5);

        $this->assertSame(250, $policy->calculateDiscount(5_000, $order));
    }
}
```

### 2. Аппликация: оркестрация. Знает порядок, не знает «как»

```php
// src/Application/Order/ProcessOrderService.php
final class ProcessOrderService
{
    public function __construct(
        private OrderRepository    $orders,
        private PaymentGateway     $payment,   // интерфейс, а не GuzzleClient
        private OrderNotifier      $notifier,  // интерфейс
        private AnalyticsReporter  $analytics, // интерфейс
        private EventDispatcher    $events,
        private EntityManager      $em,
    ) {}

    public function process(int $orderId): Order
    {
        $order = $this->orders->getById($orderId);  // бросает OrderNotFoundException

        // Доменные правила — чистый код, без моков в тесте
        $order->assertProcessable();

        $discount = $this->pricing->calculateDiscount($order->subtotal(), $order);
        $order->applyDiscount($discount);
        $this->rules->assertChargeable($order);

        // Инфраструктура: платёж
        $result = $this->payment->charge(
            ChargeRequest::fromOrder($order),
        );

        if ($result->isDeclined()) {
            $order->markAsFailed($result->reason());

            $this->em->flush();
            $this->notifier->paymentDeclined($order);
            $this->em->commit();

            return $order;
        }

        $order->markAsPaid($result->transactionId());

        $this->em->flush();
        $this->em->commit();

        // Post-commit: не в транзакции.
        // Если email упадёт — заказ уже оплачен. Это правильно.
        $this->notifier->orderConfirmed($order);
        $this->analytics->track(OrderCompleted::from($order, $discount));
        $this->events->dispatch(new OrderCompletedEvent($order));

        return $order;
    }
}
```

Что изменилось:
- **Email и аналитика — post-commit**, не внутри транзакции.
- **`OrderRepository::getById`** бросает `OrderNotFoundException` вместо `null`. Вызывающий не проверяет `if ($order === null)`.
- **Расчёт скидки** — через `PricingPolicy`, а не inline.
- **Валидация** — через `OrderRules`, а не if/else в процессе.
- **`$this->pricing`** — ещё одна зависимость, но она чистая, тестируемая без моков.

### 3. Инфраструктура: «как», а не «что»

```php
// src/Infrastructure/Payment/PaymentGatewayInterface.php
interface PaymentGateway
{
    public function charge(ChargeRequest $request): ChargeResult;
}

// src/Infrastructure/Payment/StripeGateway.php
final class StripeGateway implements PaymentGateway
{
    public function __construct(
        private \Stripe\StripeClient $client,
        private LoggerInterface      $logger,
    ) {}

    public function charge(ChargeRequest $request): ChargeResult
    {
        try {
            $charge = $this->client->charges->create([
                'amount'   => $request->amountCents(),
                'currency' => $request->currency(),
                'source'   => $request->cardToken(),
                'metadata' => ['order_id' => $request->orderId()],
            ]);
            return ChargeResult::succeeded($charge->id);
        } catch (\Stripe\Exception\CardException $e) {
            $this->logger->warning('Stripe decline', ['code' => $e->getStripeCode()]);
            return ChargeResult::declined($e->getDeclineCode());
        } catch (\Stripe\Exception\StripeException $e) {
            throw new PaymentGatewayException(
                'Stripe API error',
                context: ['message' => $e->getMessage()],
            ) from $e;
        }
    }
}

// src/Infrastructure/Notification/EmailNotifier.php
final class EmailNotifier implements OrderNotifier
{
    public function __construct(private MailerInterface $mailer) {}

    public function orderConfirmed(Order $order): void
    {
        $this->mailer->send(
            to:      $order->customer()->email(),
            subject: 'Order confirmed',
            body:    "Order #{$order->id()} confirmed. Total: {$order->total()}",
        );
    }

    public function paymentDeclined(Order $order): void
    {
        $this->mailer->send(
            to:      $order->customer()->email(),
            subject: 'Payment failed',
            body:    'Your payment was declined: ' . $order->failureReason(),
        );
    }
}
```

---

## Что именно изменилось — сводка

| Было | Стало | Почему |
|---|---|---|
| 1 класс, 140 строк, 7 deps | 6 классов, 40–80 строк каждый, 1–3 deps | Каждый класс отвечает на один вопрос |
| Email внутри `$em->commit()` | Email post-commit | Падение email не откатывает платёж |
| `if ($order === null)` | `getById()` бросает `NotFoundException` | Контракт явный, нет `null`-проверок |
| Расчёт скидки inline | `PricingPolicy::calculateDiscount()` | Тестируется без моков. Легко добавить промокод |
| `catch (\Throwable)` на весь метод | Каждый слой ловит своё | Шлюз упал ≠ баг в расчёте |
| Домен знает про `Logger`, `AnalyticsClient` | Домен не знает. Аппликация знает. | DIP: домен не зависит от инфраструктуры |
| `process()` — имя-обещание | `process()` + `PricingPolicy` + `OrderRules` + `OrderNotifier` | Имена совпадают с ответственностью |

---

## Честная оговорка

SRP **не означает** «один метод на класс» и «один класс на концепцию». Если `Order` и `OrderItem` — два класса, а не один, — это не SRP, это просто нормальный код. Если `PricingPolicy` — 12 строк, — это не «избыточная абстракция», это **тестируемость без моков**.

Критерий: **есть ли у класса две независимые причины измениться?**

- «Добавить новый способ оплаты» и «изменить расчёт скидки» — две причины. Значит, `PaymentGateway` и `PricingPolicy` — разные классы.
- «Изменить текст email» и «изменить расчёт скидки» — две причины. Значит, `OrderNotifier` и `PricingPolicy` — разные классы.
- «Изменить текст email» и «добавить новый канал (Telegram)» — две причины. Значит, `OrderNotifier` — интерфейс, а `EmailNotifier` и `TelegramNotifier` — реализации.

Если ответ «одна причина» — один класс. Если «две» — два класса. Не «один метод на класс», а **одна причина измениться**.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

# Можно ли нарушать SOLID?

Можно. И вопрос «можно ли» — не тот, который стоит задавать. Правильный вопрос: **«что я покупаю, нарушая, и что теряю, соблюдая?»** SOLID — не заповеди. Это набор эвристик, каждая оптимизирует одну ось: стоимость изменения, переиспользование, развязку. Но есть и другие оси — когнитивная нагрузка, скорость доставки, вероятность изменения, срок жизни проекта. Если нарушение удешевляет другие оси больше, чем дорожает ось SOLID, — нарушение оправдано.

Ниже — два примера, где «текстово правильный» вариант хуже «нарушенного» в реальном коде.

---

## 1. OCP: switch вместо Strategy

### «Текстово правильный» вариант (Strategy + Factory + DI)

Требование: «добавить способ оплаты, не трогая существующий код».

```php
// interface PaymentStrategy.php
interface PaymentStrategy
{
    public function charge(ChargeRequest $req): ChargeResult;
    public function supports(string $method): bool;
}

// StripeStrategy.php
final class StripeStrategy implements PaymentStrategy
{
    public function __construct(private \Stripe\StripeClient $client) {}

    public function charge(ChargeRequest $req): ChargeResult { /* ... */ }
    public function supports(string $method): bool { return $method === 'card'; }
}

// BankTransferStrategy.php
final class BankTransferStrategy implements PaymentStrategy
{
    public function charge(ChargeRequest $req): ChargeResult { /* ... */ }
    public function supports(string $method): bool { return $method === 'bank'; }
}

// CryptoStrategy.php
final class CryptoStrategy implements PaymentStrategy
{
    public function charge(ChargeRequest $req): ChargeResult { /* ... */ }
    public function supports(string $method): bool { return $method === 'crypto'; }
}

// PaymentStrategyFactory.php
final class PaymentStrategyFactory
{
    /** @param iterable<PaymentStrategy> $strategies */
    public function __construct(
        private array $strategies,
    ) {}

    public function resolve(string $method): PaymentStrategy
    {
        foreach ($this->strategies as $s) {
            if ($s->supports($method)) {
                return $s;
            }
        }
        throw new DomainException("Unknown payment method: {$method}", code: 'BAD_METHOD');
    }
}

// PaymentService.php
final class PaymentService
{
    public function __construct(private PaymentStrategyFactory $factory) {}

    public function charge(string $method, ChargeRequest $req): ChargeResult
    {
        return $this->factory->resolve($method)->charge($req);
    }
}

// config/services.php
// + 4 записи DI-контейнера, + 4 файла, + 2 интерфейса, + 1 фабрика
```

**Итого: 7 файлов, 2 интерфейса, 1 фабрика, 4 записи DI.** Чтобы добавить 4-й способ оплаты — 1 новый файл + 1 запись DI. Чтобы найти, почему платёж не прошёл — пройти через фабрику, стратегию, клиент.

### «Нарушенный» вариант (switch)

```php
// src/Infrastructure/Payment/PaymentGateway.php
final class PaymentGateway
{
    public function __construct(
        private \Stripe\StripeClient $stripe,
        private BankApiClient        $bank,
        private LoggerInterface      $logger,
    ) {}

    public function charge(string $method, ChargeRequest $req): ChargeResult
    {
        return match ($method) {
            'card'   => $this->chargeViaStripe($req),
            'bank'   => $this->chargeViaBank($req),
            'crypto' => $this->chargeViaCrypto($req),
            default  => throw new DomainException(
                "Unknown payment method: {$method}",
                code: 'BAD_METHOD',
            ),
        };
    }

    private function chargeViaStripe(ChargeRequest $req): ChargeResult
    {
        try {
            $c = $this->stripe->charges->create(/* ... */);
            return ChargeResult::succeeded($c->id);
        } catch (\Stripe\Exception\CardException $e) {
            return ChargeResult::declined($e->getDeclineCode());
        }
    }

    private function chargeViaBank(ChargeRequest $req): ChargeResult
    { /* ... */ }

    private function chargeViaCrypto(ChargeRequest $req): ChargeResult
    { /* ... */ }
}
```

**Итого: 1 файл, 1 класс, 3 приватных метода.** Добавление 4-го способа — 1 case в `match` + 1 приватный метод. Навигация: один файл, Ctrl+F.

### Когда switch оправдан

| Условие | Почему |
|---|---|
| 3–5 способов оплаты, не больше | Вероятность 6-го — мала. Strategy окупается при 8+ |
| Каждый способ имеет **разную логику** (Stripe ≠ bank ≠ crypto) | Нет общего кода, который Strategy «факторит». `charge()` у каждого — на 20+ строк и не похож |
| 1–2 разработчика, проект живёт 1–2 года | Когнитивный налог Strategy (интерфейс → фабрика → реализация) не окупается |
| `match` + `default` даёт **исчерпывающий** ветвление | Стат-анализер подскажет, если добавите case в enum, а здесь не обновите. OCP нарушен, но **безопасно**: не «забудете», а «пройдёте» |

Когда switch **не** оправдан: 8+ способов, общий код между ними (retry, idempotency key, logging), микросервисы с разными командами, «способы оплаты» — это плагинная система. Тогда Strategy + DI — не «SOLID ради SOLID», а экономия.

**Литmus-тест:** если добавить новый способ оплаты — я правлю 1 файл или 4? Если 1 — switch. Если 4+ и каждый из них тянет за собой DI-конфигурацию и тест-матрицу — Strategy.

---

## 2. DIP: слой-прокидчик

### «Текстово правильный» вариант (Controller → Service → Repository)

```php
// Controller
final class OrderController
{
    public function __construct(private OrderService $service) {}  // ← интерфейс

    public function show(int $id): JsonResponse
    {
        $order = $this->service->getById($id);
        return new JsonResponse(OrdersDTO::fromEntity($order)->toArray());
    }
}

// Service — СЛОЙ-ПРОКИДЧИК
final class OrderService
{
    public function __construct(private OrderRepository $repo) {}  // ← интерфейс

    public function getById(int $id): Order
    {
        return $this->repo->getById($id);   // ← единственная строка
    }
}

// Repository
interface OrderRepository {
    public function getById(int $id): Order;
    // ... 12 других методов
}
```

**Что даёт этот слой?** `OrderService::getById()` — одна строка, которая делегирует в `OrderRepository::getById()`.

- Тестировать `OrderService`? Мокнуть `OrderRepository`, убедиться, что `getById` вызывается с тем же аргументом и возвращает то же. **Тестируем, что делегирование работает.**
- DIP: контроллер зависит от `OrderService`, а не от `OrderRepository`. **Но** `OrderService` не добавляет поведения. Она не знает о бизнес-правилах, не валидирует, не оркестрирует.

### «Нарушенный» вариант (Controller → Repository напрямую)

```php
final class OrderController
{
    public function __construct(private OrderRepository $repo) {}

    public function show(int $id): JsonResponse
    {
        $order = $this->repo->getById($id);
        return new JsonResponse(OrdersDTO::fromEntity($order)->toArray());
    }
}
```

### Когда прокидчик — шум

| Условие | Почему |
|---|---|
| Метод сервиса = `return $repo->getById($id)` | Нет поведения. DIP защищает от **конкретной** зависимости, а не от самого слова «Repository». Если `OrderRepository` — интерфейс, контроллер уже зависит от абстракции |
| Service-слой есть только в `getById`, а `create()` и `process()` — в контроллере | Непоследовательно. Либо все методы через сервис, либо ни один. «Service для чтения, контроллер для записи» — это не архитектура, это случайность |
| 1 разработчик, 3 контроллера, 2 репозитория | 6 прокидчиков-пустышек. Каждый — файл, тест, DI-запись, когнитивный шаг «куда смотреть» |
| Нет поведенческой логики в «сервисе» | `OrderService` не проверяет права, не логирует, не транзакционирует. Он **не сервис**. Это адаптер с одним методом |

### Когда прокидчик **нужен**

```php
final class OrderService
{
    public function getById(int $id, int $currentUserId): Order
    {
        $order = $this->repo->getById($id);

        if ($order->ownerId() !== $currentUserId
            && !User::isStaff($currentUserId)
        ) {
            throw new ForbiddenException('Not your order');
        }

        $this->logger->debug('Order viewed', ['order' => $id, 'user' => $currentUserId]);

        return $order;
    }
}
```

Теперь слой **добавляет поведение**: авторизация, логирование. Контроллер не знает о правах. Тестируется без HTTP. DIP окупился.

**Литmus-тест:** если удалить service-класс и перенести его единственный метод в контроллер — что-то ломается? Если нет — слой был декорацией. Если да (теряется авторизация, логирование, транзакция) — слой несёт ответственность.

---

## 3. Коротко про ISP

`OrderRepository` на 15 методов. «Разбей на 4 интерфейса» — да, если есть **несколько** потребителей, каждый из которых использует свой подмножество. Если потребителей два (service и controller) и оба используют 12 из 15 — разбивка на `OrderReader`, `OrderWriter`, `OrderSearcher`, `OrderAuditor` создаёт 4 интерфейса, 4 реализации, 4 DI-записи, и ни один потребитель не выигрывает. ISP защищает **реализатора**, который вынужден реализовывать 15 методов, чтобы использовать 2. Если реализатор один (ваш `DoctrineOrderRepository`) и он и так реализует все 15 — ISP не защищает ни кого.

---

## Обобщённый критерий

Не «нарушает ли это SOLID?», а:

```
1. Сколько раз я буду менять этот код?
   → 1–2 раза за жизнь проекта: проще switch / прямой вызов.
   → 5+ раз, разными людьми: Strategy / DI / interface окупается.

2. Что я теряю, нарушая?
   → Если теряю «возможность добавить X не трогая Y»,
     а X и Y не планируются — теряю воздух.
   → Если теряю «тестируемость без моков» — теряю реальное.

3. Что я получаю, нарушая?
   → Меньше файлов, меньше индирекции, меньше DI-конфига.
   → Одно место для поиска, один стек для отладки.

4. Кто читает код через 6 месяцев?
   → Если это я и я помню структуру — switch норм.
   → Если это новый человек в команде из 8 — Strategy норм.
```

SOLID — это **инструмент для снижения стоимости изменения**. Если изменение маловероятно, дорогое только при 10-м применении, а код живёт 8 месяцев — инструмент не нужен. Это не «нарушение SOLID». Это **правильная оценка того, что SOLID не нужен в этой точке**.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

# OCP без наследования

OCP — про **точку расширения**: добавить новое поведение, не трогая существующий код. Наследование (interface → класс A, B, C) — самый частый способ, но далеко не единственный. Ниже — альтернативы, от наиболее «PHP-идиоматичных» к более общим.

---

## 1. Registry / Plugin pattern

Расширение = регистрация нового обработчика в реестр. Существующий код не знает и не знает о нём.

```php
final class TaxCalculator
{
    /** @var array<string, callable(array): float> */
    private array $rules = [];

    public function register(string $country, callable $calc): void
    {
        $this->rules[$country] = $calc;
    }

    public function calculate(array $order, string $country): float
    {
        if (!isset($this->rules[$country])) {
            throw new DomainException("No tax rule for: {$country}");
        }
        return ($this->rules[$country])($order);
    }
}

// Регистрация — в отдельном файле, в сервис-провайдере, в CLI-команде.
// Кто-то через полгода добавит:
$tax->register('UA', fn (array $o) => $o['total'] * 0.20);
$tax->register('KZ', fn (array $o) => $o['total'] * 0.12 + 500);

// calculate() — не тронут. Ни одна строка.
```

**Что даёт:** точка расширения — один вызов `register()`. Новый код — один файл, одна строка. Старый код не читается, не перекомпилируется, не передеплоится.

**Ограничение:** нет статической типизации ключа. `'UA '` (с пробелом) — тихий баг. Решение: enum.

```php
enum Country: string { case UA = 'UA'; case KZ = 'KZ'; }

public function register(Country $c, callable $calc): void { ... }
```

---

## 2. Higher-order functions (callback как параметр)

Расширение = передать другую функцию. Никаких подклассов, никаких интерфейсов.

```php
final class ReportRenderer
{
    public function __construct(
        private \Closure $formatter,   // как отформатировать строку
        private \Closure $aggregator,  // как агрегировать
    ) {}

    public function render(array $rows): string
    {
        $aggregated = ($this->aggregator)($rows);
        return array_map($this->formatter, $aggregated);
    }
}

// Вариант A: CSV
$csv = new ReportRenderer(
    formatter:  fn (array $r) => implode(',', [$r['id'], $r['name'], $r['total']]),
    aggregator: fn (array $rows) => array_slice($rows, 0, 100),
);

// Вариант B: JSON
$json = new ReportRenderer(
    formatter:  fn (array $r) => json_encode($r),
    aggregator: fn (array $rows) => array_filter($rows, fn ($r) => $r['total'] > 0),
);

// ReportRenderer — не тронут. Ни одной строки.
```

**Что даёт:** два «алгоритма» без единого интерфейса, без `implements`, без фабрики. Расширение — новый `new ReportRenderer(...)`.

**Когда лучше, чем Strategy:** когда «стратегия» — одна функция, а не объект с состоянием. Strategy-класс на 5 строк, где `charge()` = одна строка, — это наследование ради наследования.

---

## 3. Event-driven (Observer)

Расширение = подписать обработчик на событие. Публикующий код не знает и не знает о подписчиках.

```php
final class EventBus
{
    /** @var array<string, list<callable>> */
    private array $subscribers = [];

    public function on(string $event, callable $handler): void
    {
        $this->subscribers[$event][] = $handler;
    }

    public function dispatch(string $event, object $payload): void
    {
        foreach ($this->subscribers[$event] ?? [] as $h) {
            $h($payload);
        }
    }
}

// Ядро процесса:
$order->markAsPaid($txId);
$bus->dispatch('order.paid', new OrderPaidEvent($order));

// Кто-то потом добавит:
$bus->on('order.paid', fn (OrderPaidEvent $e) => $logger->info('Paid', ['id' => $e->id]));
$bus->on('order.paid', fn (OrderPaidEvent $e) => $analytics->track('order_paid', $e->toArray()));
$bus->on('order.paid', fn (OrderPaidEvent $e) => $email->sendConfirmation($e->order));

// markAsPaid / dispatch — не тронуты.
```

**Что даёт:** N подписчиков, 0 знаний у публикующего. Добавление — одна строка `on()`.

**Когда лучше, чем наследование:** когда «расширение» — это **побочное действие**, а не **альтернатива поведения**. Наследование — «вместо A делай B». Observer — «после A сделай ещё C, D, E». Для альтернатив поведения лучше Strategy/Registry.

---

## 4. Data-driven (конфиг / правила)

Расширение = запись в массив/БД. Код не меняется вообще.

```php
// config/routing.php
return [
    'GET /orders'      => [OrderController::class, 'index'],
    'GET /orders/{id}' => [OrderController::class, 'show'],
    'POST /orders'     => [OrderController::class, 'store'],
    'PUT /orders/{id}' => [OrderController::class, 'update'],
];

// Через месяц:
return [
    'GET /orders'      => [OrderController::class, 'index'],
    'GET /orders/{id}' => [OrderController::class, 'show'],
    'POST /orders'     => [OrderController::class, 'store'],
    'PUT /orders/{id}' => [OrderController::class, 'update'],
    'DELETE /orders/{id}' => [OrderController::class, 'destroy'],  // ← новое
];

// Router — не тронут. Ни одной строки кода.
```

Это **максимальный OCP**: расширение — данные, а не код. Применяется там, где «новые варианты» — это **варианты конфигурации**, а не **варианты поведения**.

**Когда лучше:** маршруты, права доступа, валидационные правила, тарифы. Когда «новое» — это ещё одна строка в таблице, а не ещё один класс.

---

## 5. Decorator / Middleware (композиция, не наследование)

Расширение = обернуть. Не наследовать, а **обернуть** существующий объект.

```php
interface Logger { public function write(string $msg): void; }

final class FileLogger implements Logger
{
    public function write(string $msg): void { file_put_contents('/var/log/app.log', $msg . "\n", FILE_APPEND); }
}

// Расширение: добавить timestamp. Не наследуем FileLogger. Оборачиваем.
final class TimestampedLogger implements Logger
{
    public function __construct(private Logger $inner) {}

    public function write(string $msg): void
    {
        $this->inner->write('[' . date('c') . "] {$msg}");
    }
}

// Расширение: добавить ротацию. Оборачиваем TimestampedLogger.
final class RotatingLogger implements Logger
{
    public function __construct(private Logger $inner, private int $maxFiles = 5) {}

    public function write(string $msg): void
    {
        // логика ротации
        $this->inner->write($msg);
    }
}

// Составление:
$logger = new RotatingLogger(new TimestampedLogger(new FileLogger()));

// FileLogger — не тронут. TimestampedLogger — не тронут.
// Добавление нового поведения = новый обёртка + одна строка в составе.
```

**Что даёт:** каждый «слой» — один класс, одна ответственность. Расширение — новый обёртка, не модификация.

**В Symfony/Laravel** это middleware-пайплайн: `addMiddleware()` — и существующий пайплайн не тронут.

---

## 6. Table-driven dispatch (match / массив)

Расширение = запись в таблицу. Код-диспетчер не меняется.

```php
final class NotificationChannel
{
    /** @var array<string, callable(string, string): void> */
    private array $handlers = [];

    public function __construct()
    {
        $this->handlers = [
            'email'    => fn (string $to, string $msg) => $this->sendEmail($to, $msg),
            'sms'      => fn (string $to, string $msg) => $this->sendSms($to, $msg),
            'telegram' => fn (string $to, string $msg) => $this->sendTelegram($to, $msg),
        ];
    }

    public function send(string $channel, string $to, string $msg): void
    {
        if (!isset($this->handlers[$channel])) {
            throw new DomainException("Unknown channel: {$channel}");
        }
        ($this->handlers[$channel])($to, $msg);
    }
}

// Добавить 'push':
// → одна строка в конструктор + один приватный метод sendPush().
// → send() — не тронут.
```

Это **гибрид** Registry и data-driven: таблица жёстко задана в коде (статическая типизация каналов), но расширение — добавление записи, а не создание подкласса.

---

## Сводка: когда какой способ

| Способ | Расширение = | Лучшее применение |
|---|---|---|
| **Наследование** (interface → class) | Новый класс + DI-запись | Когда «стратегия» — объект с состоянием, логикой, зависимостями |
| **Registry** | `register()` / запись в массив | Когда обработчиков много, они независимы, ключ — string/enum |
| **Higher-order fn** | Новый `new` с callback | Когда «стратегия» — одна функция без состояния |
| **Event** | `on()` / `listen()` | Побочные действия, а не альтернатива поведения |
| **Data-driven** | Запись в конфиг / БД | Маршруты, правила, тарифы, права |
| **Decorator** | Новый обёртка | Добавление поведения к существующему объекту |
| **Table dispatch** | Запись в match / массив | 3–8 вариантов, каждый — 5–20 строк |

**Общий принцип:** OCP не требует наследования. Он требует, чтобы **точка расширения была отделена от точки использования**. Наследование — один способ сделать это. Массив функций, конфиг, событие, обёртка — другие. Выбирай тот, который минимизирует код, который **не** меняется при расширении.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

# SRP не про классы. SRP про «причину измениться»

Определение Грэма Мартина: *«Каждый модуль должен иметь только одну причину измениться»*. Слово — **модуль**, а не «класс». Класс — просто самая удобная единица в ООП. Но принцип работает на любом уровне абстракции.

---

## 1. Функция

```php
// ❌ Две причины измениться:
//   1) поменять формат отчёта
//   2) поменять, отчёты для кого генерировать
function generateReport(User $user, string $period, string $format): string
{
    $data = $user->orders()->whereBetween('created_at', [$period . '-start', $period . '-end']);

    if ($format === 'csv') {
        return $data->map(fn ($o) => implode(',', [$o->id, $o->total]))->implode("\n");
    }
    if ($format === 'pdf') {
        return $this->renderPdf($data);
    }
    throw new DomainException("Unknown format: {$format}");
}

// ✅ Одна причина: «как отформатировать массив строк»
function toCsv(array $rows): string
{
    return implode("\n", array_map(fn (array $r) => implode(',', $r), $rows));
}

function toPdf(array $rows): string
{
    // ...
}

function generateReport(User $user, string $period, string $format): string
{
    $data = $user->orders()->whereBetween('created_at', [$period . '-start', $period . '-end'])->toArray();
    return match ($format) {
        'csv' => toCsv($data),
        'pdf' => toPdf($data),
        default => throw new DomainException("Unknown format: {$format}"),
    };
}
```

`generateReport` — одна причина: *какие данные извлечь*. Формат — другая причина, вынесен в отдельные функции. Изменение формата не трогает выборку данных.

---

## 2. Метод

```php
// ❌ save() делает три вещи:
//   1) валидирует
//   2) сохраняет в БД
//   3) отправляет email
public function save(Order $order): void
{
    $errors = $order->validate();
    if ($errors !== []) {
        throw new ValidationException($errors);
    }

    $this->em->persist($order);
    $this->em->flush();

    $this->mailer->send($order->customer()->email(), 'Order saved', $order->id());
}

// ✅ Три причины → три метода (или три класса, если масштабируется)
public function save(Order $order): void
{
    $this->validator->assertValid($order);   // причина 1
    $this->em->persist($order);               // причина 2
    $this->em->flush();
    $this->notifier->orderSaved($order);      // причина 3
}
```

Если «причины» разные по частоте изменений (валидация меняется редко, email-шаблон — каждую неделю) — выносите в отдельные классы. Если меняются вместе — можно оставить в одном методе, но **названиями** покажите границы.

---

## 3. Файл / модуль

```
❌ src/Domain/Order.php
   └─ class Order            (сущность)
   └─ class OrderStatus      (enum)
   └─ class OrderException   (исключение)
   └─ class OrderFactory     (фабрика)
   └─ class OrderValidator   (валидатор)

✅ src/Domain/Order/
   ├─ Order.php
   ├─ OrderStatus.php
   ├─ OrderException.php
   ├─ OrderFactory.php
   └─ OrderValidator.php
```

Один файл — не один модуль. Если в файле 5 классов, у него **5 причин измениться**. SRP на уровне файла: один файл = одна единица = одна причина.

---

## 4. Сервис / оркестратор

```php
// ❌ CheckoutService: 200 строк, 9 зависимостей
//    Причины измениться:
//    1) поменять расчёт скидки
//    2) добавить новый способ оплаты
//    3) изменить отправку email
//    4) поменять правила валидации
//    5) добавить логирование
//    6) изменить формат ответа API

// ✅ Четыре слоя, каждый со своей причиной
CheckoutService      // оркестрация: порядок вызовов. Причина: сценарий.
PricingPolicy        // расчёт. Причина: бизнес-правила.
PaymentGateway       // платёж. Причина: интеграция со шлюзом.
OrderNotifier        // уведомления. Причина: каналы/шаблоны.
```

Каждый слой — свой «модуль» в смысле SRP. Изменился способ оплаты → трогаете только `PaymentGateway`. Изменился шаблон email → трогаете только `OrderNotifier`. `CheckoutService` не тронут.

---

## 5. Even ниже: переменная / параметр

SRP иногда проецируется даже на **имена и границы данных**:

```php
// ❌ Один массив несёт три разные роли:
function process(array $config): void
{
    // $config['db_host']     ← инфраструктура
    // $config['max_retries'] ← политика
    // $config['debug']       ← окружение
    // Изменение любой из трёх → правка одного «модуля»
}

// ✅ Три объекта, три причины:
function process(
    DatabaseConfig  $db,
    RetryPolicy     $policy,
    RuntimeEnv      $env,
): void { ... }
```

Каждый объект — «модуль» со своей причиной измениться. Изменилась политика ретраев → правите `RetryPolicy`, а не ищете `max_retries` в общем массиве.

---

## Как применять на практике

Перед тем как написать/рефакторить, спросите:

| Вопрос | Что ищите |
|---|---|
| Сколько **независимых** причин измениться у этой функции/класса/файла? | Если 2+ — кандидат на разделение |
| Меняются ли эти причины **вместе** или **отдельно**? | Если отдельно — разделяйте. Если вместе — можно не разделять |
| Кто **разные** люди/команды, которые будут их менять? | Разные люди → разные модули, даже если логически «одно» |
| Изменение одной причины **не должно** требовать перекомпиляции/передеплоя другой? | Если да → жёсткая граница (интерфейс, событие, конфигурация) |

---

## Частый контраргумент: «Разделишь — станет 10 файлов на одну задачу»

Верно. SRP не говорит «разделяй всегда». Он говорит: **если** есть две независимые причины измениться, **и** они меняются с разной частотой, **и** их изменение не должно затрагивать друг друга — тогда разделите.

Если у вас 3 строки кода, которые меняются вместе, раз в год, одним человеком — это **один модуль** по SRP, даже если визуально «делает две вещи». Избыточное разделение — тоже нарушение: оно увеличивает когнитивную стоимость без выгоды.

**Критерий не «сколько вещей делает», а «сколько независимых причин измениться» и «меняются ли они независимо».**

> ⏱ 36.3s · prompt 11830 tok · output 5063 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про DI-контейнер Symfony: компиляция, autowiring, и что происходит при первом запросе сервиса. Это технический вопрос, требующий точности. Нужно объяснить:

1. Архитектуру контейнера (ContainerBuilder → CompiledContainer)
2. Этапы компиляции (definition, autowiring, autologging, autoconfiguration, pass-ы)
3. Autowiring — как работает (типы аргументов → сервисы по типу)
4. Runtime: что происходит при `get()` или инъекции в контроллер

Важно быть точным про Symfony 5/6/7. Структура:
- ServiceDefinition (id, class, args, tags, public/private, shared)
- ContainerBuilder собирает definitions из PHP-атрибутов, XML, YAML, PHP-конфигов
- Pass-ы: ResolveInstanceofConditionalsPass, RegisterServiceSubscribersPass, ResolveChildDefinitionsPass, ResolveParameterPlaceHoldersPass, AutowirePass, RegisterAutoconfigureAttributesPass, RemoveUnusedDefinitionsPass, ResolveHotpathPass, CheckDefinitionValidityPass, ResolveInstanceofConditionalsPass, etc.
- Компиляция: генерация PHP-кода (Container.php с методами getServiceXxx())
- Autowiring: в AutowirePass, для каждого аргумента `#[Autowire]` или тип-гитинга — ищем сервис по классу/интерфейсу
- Runtime: CompiledContainer::get('id') → если shared → статическое поле, если нет → new. Если lazy → прокси. Если public → доступен.

Структурирую как:
1. Ментальная модель (что такое контейнер)
2. Этап 1: Сборка (boot)
3. Этап 2: Компиляция (pass-ы)
4. Autowiring — детально
5. Этап 3: Runtime (первый get)
6. Что происходит при инъекции в контроллер
7. Ловушки / нюансы

Поехали.

</details>

**Кандидат:**

# DI-контейнер Symfony: от `php bin/console` до инстанса в контроллере

## Ментальная модель

Контейнер — это **фабрика объектов**. Вы не создаёте `new OrderService(...)` сами. Вы говорите контейнеру: «дай мне `OrderService`» — а он знает, какие зависимости нужны, как их создать, в каком порядке, и возвращает готовый объект.

Два состояния:

```
┌─────────────────────────────┐       ┌─────────────────────────────┐
│  ContainerBuilder           │──────▶│  CompiledContainer          │
│  (в памяти, в dev)          │       │  (PHP-файл, в prod)         │
│  definitions, params, tags  │       │  public function get('id')  │
│  pass-ы, autowiring         │       │  { return $this->privOrder  │
│  гибкий, медленный          │       │     ?: $this->privOrder =   │
└─────────────────────────────┘       │        $this->createPrivO…  │
                                      │  }                          │
                                      └─────────────────────────────┘
```

В **dev** — `ContainerBuilder`, всё в памяти, можно менять. В **prod** — скомпилированный PHP-файл (`var/cache/prod/ContainerXXX/ContainerXXX.php`), без `ContainerBuilder`, без pass-ов, только методы. Это и есть «компиляция».

---

## Этап 1: Сборка (Boot)

При каждом запросе Symfony загружает:

```php
// config/bundles.php
// src/Kernel.php
// config/packages/*.yaml (framework.yaml, doctrine.yaml, …)
// config/services.yaml
// config/services_*.yaml
// Атрибуты #[Autoconfigure], #[Autowire], #[AsCommand], …
```

Каждый сервис описывается как **ServiceDefinition**:

```php
// config/services.yaml (упрощённо)
services:
    App\Service\OrderService:
        arguments:
            $orders:    '@App\Repository\OrderRepository'
            $payment:   '@App\Gateway\StripeGateway'
            $notifier:  '@App\Notifier\EmailNotifier'
            $em:        '@doctrine.orm.entity_manager'
        tags: ['event_listener']
        public: false
        shared: true
```

`ContainerBuilder` превращает это в:

```php
$def = new Definition(OrderService::class, [
    new Reference(OrderRepository::class),
    new Reference(StripeGateway::class),
    new Reference(EmailNotifier::class),
    new Reference('doctrine.orm.entity_manager'),
]);
$def->addTag('event_listener');
$def->setPublic(false);
$def->setShared(true);
$builder->setDefinition('App\Service\OrderService', $def);
```

На этом этапе в контейнере — **описания**, а не объекты. Никаких `new` ещё не было.

---

## Этап 2: Компиляция (Pass-ы)

`$builder->compile()` прогоняет **список pass-ов** (компиляций), каждый из которых мутирует collection of definitions. Порядок важен. Ключевые:

| # | Pass | Что делает |
|---|---|---|
| 1 | `ResolveInstanceofConditionalsPass` | Раскрывает `instanceof` блоки из `framework.yaml` |
| 2 | `RegisterServiceSubscribersPass` | Находит `getSubscribedServices()`, создаёт `ServiceSubscriber`-прокси |
| 3 | `ResolveChildDefinitionsPass` | Унаследованные definitions (parent/child) |
| 4 | `ResolveParameterPlaceHoldersPass` | `%env(APP_ENV)%` → реальные значения |
| 5 | **`AutowirePass`** | **Внедряет зависимости по типу** (см. ниже) |
| 6 | `RegisterAutoconfigureAttributesPass` | `#[Autoconfigure]`, `#[Autowire]` → tags, args |
| 7 | `RemoveUnusedDefinitionsPass` | Убирает приватные сервисы, которые никто не использует |
| 8 | `CheckDefinitionValidityPass` | Валидация: нет ли циклов, пропущенных аргументов |
| 9 | `ResolveHotpathPass` | Определяет, какие сервисы можно лениво создать |
| 10 | `RegisterEnvVarProcessorsPass` | `env()` → обработчики |
| … | … | ~30 pass-ов суммарно |

После всех pass-ов каждая definition **полная**: все аргументы разрешены, все ссылки — на конкретные id, все теги на месте.

### Компиляция в файл (prod)

```php
// var/cache/prod/ContainerABC123/ContainerABC123.php
class ContainerABC123 extends Container
{
    private $privOrderService;

    public function get('App\Service\OrderService'): OrderService
    {
        return $this->privOrderService
            ?? $this->privOrderService = $this->createPrivOrderServiceService();
    }

    private function createPrivOrderServiceService(): OrderService
    {
        return new \App\Service\OrderService(
            $this->get('App\Repository\OrderRepository'),
            $this->get('App\Gateway\StripeGateway'),
            $this->get('App\Notifier\EmailNotifier'),
            $this->get('doctrine.orm.entity_manager'),
        );
    }
}
```

Обратите внимание: **нет `ContainerBuilder`**, нет pass-ов, нет YAML-парсера. Только PHP-методы. Это и есть «компиляция»: описание → исполняемый код.

---

## Autowiring: как работает детально

### Что такое autowiring

Вместо:
```yaml
arguments:
    $orders: '@App\Repository\OrderRepository'
```

Вы пишете:
```php
final class OrderService
{
    public function __construct(
        private OrderRepository  $orders,
        private PaymentGateway   $payment,
        private EntityManager    $em,
    ) {}
}
```

И в `services.yaml`:
```yaml
_App\Service\OrderService: ~   # без arguments
```

ContainerBuilder «дочитывает» типы из сигнатуры конструктора и сам подставляет сервисы.

### Механизм (`AutowirePass`)

```
Для каждого сервиса, у которого нет явных arguments:
  1. Отражаем класс: ReflectionMethod('__construct')
  2. Для каждого параметра:
     a. Если тип = класс/интерфейс → ищем в контейнере сервис с этим id
     b. Если тип = скаляр (string, int) → ищем параметр %app.order_prefix% или оставляем null
     c. Если есть #[Autowire(service: '...')] → явная ссылка
     d. Если есть #[Autowire(name: '...')] → ищем по имени
     e. Если параметр nullable и сервис не найден → null
     f. Если сервис не найден и не nullable → ошибка компиляции
  3. Заполняем definition->setArguments()
```

### Ключевые нюансы

**1. Autowiring по типу, не по имени.**

```php
public function __construct(
    private OrderRepository $orders,   // ← имя $orders НЕ имеет значения
) {}
```

Symfony ищет сервис с id `App\Repository\OrderRepository` (FQCN). Переменная `$orders` — косметика.

**2. Интерфейс → реализация.**

```php
interface PaymentGateway { ... }
final class StripeGateway implements PaymentGateway { ... }

// config/services.yaml
_App\Gateway\StripeGateway:
    alias: App\Gateway\PaymentGateway   // ← alias

// Тогда autowiring:
// PaymentGateway → alias → StripeGateway ✅
```

Без alias — ошибка: «Cannot autowire service "App\Service\OrderService": argument "$payment" of method "__construct()" references interface "App\Gateway\PaymentGateway" but this type has been declared as abstract».

**3. Несколько реализаций одного интерфейса → tagged iterator.**

```yaml
services:
    _instanceof:
        App\Gateway\PaymentGateway:
            tags: ['app.payment_gateway']
```

```php
public function __construct(
    /** @param iterable<PaymentGateway> $gateways */
    private iterable $gateways,
) {}
```

Symfony подставит `ServiceLocator` с всеми сервисами, имеющими тег `app.payment_gateway`.

**4. `[Autowire]` и `[Autoconfigure]`.**

```php
#[Autowire]
final class OrderService
{
    public function __construct(
        #[Autowire(service: 'my_custom_payment')]
        private PaymentGateway $payment,   // ← конкретный сервис, не alias
    ) {}
}
```

`#[Autowire]` на классе = «включи autowiring для этого сервиса» (если глобально выключен).
`#[Autoconfigure]` = «добавь стандартные теги» (например, `event_listener`).

**5. Autowiring работает только для публичных/приватных методов, а не для «всем подряд».**

Каждый сервис, у которого есть `arguments`, — autowire. Каждый, у кого нет — тоже. Глобально выключить:

```yaml
framework:
    autowiring: false   # тогда каждый аргумент — явный
```

---

## Этап 3: Runtime — первый `get()`

### Сценарий: контроллер запрашивает `OrderService`

```php
final class OrderController
{
    public function __construct(
        private OrderService $service,   // ← контейнер внедряет
    ) {}

    public function show(int $id): JsonResponse
    {
        $order = $this->service->get($id);
        return new JsonResponse($order->toArray());
    }
}
```

### Что происходит по шагам

```
1. Router парсит запрос → OrderController::show()
2. HttpKernel::handle() → controller resolver
3. Container::get('App\Controller\OrderController')
   │
   ├── shared: true? → да
   │   → isset($this->privOrderController)?
   │       → НЕТ (первый раз)
   │       → $this->privOrderController = $this->createPrivOrderControllerService()
   │
   └── createPrivOrderControllerService():
         return new \App\Controller\OrderController(
             $this->get('App\Service\OrderService'),   // ← рекурсия
         );
         │
         └── Container::get('App\Service\OrderService')
               │
               ├── shared: true → isset($this->privOrderService)?
               │   → НЕТ
               │   → $this->privOrderService = $this->createPrivOrderServiceService()
               │
               └── createPrivOrderServiceService():
                     return new \App\Service\OrderService(
                         $this->get('App\Repository\OrderRepository'),  // ← рекурсия
                         $this->get('App\Gateway\StripeGateway'),      // ← рекурсия
                         $this->get('App\Notifier\EmailNotifier'),     // ← рекурсия
                         $this->get('doctrine.orm.entity_manager'),    // ← singleton
                     );
```

**Ключевое:** каждый `get()` — либо возвращает кэш (shared), либо создаёт. Цепочка создания — **рекурсивная**, «сверху вниз». Зависимости создаются раньше зависимости.

### `shared` vs `non-shared`

```php
// shared: true (по умолчанию)
// Первый get() → создаёт. Второй get() → возвращает тот же инстанс.
// Это singleton.

// shared: false
// Каждый get() → новый инстанс.
// Используется для: request-зависимых сервисов,
//                   сервисов с изменяемым состоянием,
//                   #[Autowired] в request-контроллере.
```

### Lazy services

```yaml
services:
    App\Gateway\StripeGateway:
        lazy: true
```

При компиляции Symfony генерирует **прокси**:

```php
class StripeGatewayLazyProxy
{
    private ?StripeGateway $real = null;

    public function charge(ChargeRequest $req): ChargeResult
    {
        $this->initialize();
        return $this->real->charge($req);
    }

    private function initialize(): void
    {
        $this->real ??= $this->container->get('App\Gateway\StripeGateway');
    }
}
```

До первого вызова метода — объект не создан. Если метод никогда не вызывается — объект не создан. Это экономит время и память.

### ServiceLocator (теги)

```php
/** @param iterable<PaymentGateway> $gateways */
public function __construct(
    private iterable $gateways,   // ← ServiceLocator, не массив
) {}
```

`ServiceLocator` — «ленивая коллекция». При итерации `foreach ($this->gateways as $g)` — каждый элемент создаётся **по требованию**, а не все сразу. Если вы используете только один gateway из пяти — четыре не создадутся.

---

## Ловушки и нюансы

### 1. Циклическая зависимость

```
A зависит от B
B зависит от A
```

Компиляция: `CheckDefinitionValidityPass` → **ошибка**.

Решения:
- Event dispatcher (расцепление через событие)
- `ServiceLocator` (ленивая зависимость)
- Refactor: вынести общее в C

### 2. `private` vs `public`

```yaml
services:
    App\Service\OrderService:
        public: false   # ← нельзя get() извне
```

В prod-контейнере `private` сервисы **не имеют публичного метода `get()`**. Если вы попытаетесь `$container->get('App\Service\OrderService')` — `InvalidArgumentException`.

Нужно для `console:`, `webdebug:`, или прямого `get()` — `public: true`.

### 3. `bind` vs `arguments`

```yaml
services:
    _defaults:
        bind:
            string $environment: '%env(APP_ENV)%'
            int    $maxRetries: 3

    App\Service\OrderService: ~
```

`bind` — глобальный для всех сервисов. `arguments` — конкретный. При конфликте — `arguments` выигрывает.

### 4. `public: false` + autowiring — это нормально

```php
// services.yaml
_App\Service\OrderService:
    public: false   # нельзя get() напрямую
```

```php
// Но autowiring работает!
final class CheckoutService
{
    public function __construct(
        private OrderService $order,   // ← inject, не get()
    ) {}
}
```

Wiring ≠ `get()`. Wiring — «дай мне при создании». `get()` — «дай мне по id». Это разные механизмы.

### 5. Dev vs Prod: что меняется

| | Dev | Prod |
|---|---|---|
| Контейнер | `ContainerBuilder` в памяти | PHP-файл |
| Autowiring | Да, при компиляции | Да, при компиляции (один раз) |
| `public: false` | Можно `get()` через `debug:container` | Нельзя |
| Перезагрузка | Каждый request (cache-dir) | Один раз (deploy) |
| Ошибка autowiring | В runtime, stack trace | В компиляции, **до** деплоя |

---

## Итого: полная цепочка

```
YAML / PHP / Атрибуты
        │
        ▼
  ServiceDefinition (id, class, args, tags)
        │
        ▼
  Pass-ы (AutowirePass, RemoveUnused, …)
        │
        ▼
  Скомпилированный PHP-файл (prod)
        │
        ▼
  Container::get('id')
        │
        ├── shared? → кэш → return
        │
        └── нет → createPrivXxxService()
                    │
                    ├── new Class(...)
                    │     ├── $this->get('dep1')  ← рекурсия
                    │     ├── $this->get('dep2')  ← рекурсия
                    │     └── ...
                    │
                    └── return инстанс
```

Контейнер — не «магия». Это **описанный граф зависимостей**, который на этапе компиляции превращается в **дерево вызовов `new`**, а на этапе runtime — в **рекурсивное создание с кэшированием**. Autowiring — это **автоматическое заполнение графа** по типам, на этапе компиляции, до того как кто-то нажмёт «deploy».

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

# Циклические зависимости: как разруливать

## Сначала — диагноз, потом лекарство

Цикл `A → B → A` почти никогда не означает «у меня два сервиса, которые нужны друг другу». Чаще он означает одну из трёх вещей:

1. **Один из сервисов делает две работы**, и вторая работа на самом деле принадлежит другому.
2. **Порядок инвертирован**: тот, кто «знает», должен вызывать того, кто «не знает», а не наоборот.
3. **Связность через событие**, а не через вызов: A не «нуждается» в B, а «реагирует на факт, который B порождает».

Каждый из трёх случаев лечится по-разному. Разберу на конкретном примере.

---

## Пример цикла

```php
// OrderService.php
final class OrderService
{
    public function __construct(
        private PaymentService $payment,   // ← OrderService нужен PaymentService
        private NotifierService $notifier, // ← NotifierService нужен OrderService
    ) {}

    public function place(int $customerId, Cart $cart): Order
    {
        $order = Order::create($customerId, $cart);

        // Нужен платёж
        $result = $this->payment->charge($order);

        $order->applyPayment($result);

        // Нужна нотификация
        $this->notifier->notify($order);

        return $order;
    }
}

// NotifierService.php
final class NotifierService
{
    public function __construct(
        private OrderService $orders,      // ← цикл!
    ) {}

    public function notify(Order $order): void
    {
        $customer = $this->orders->getCustomer($order->customerId());
        $this->mailer->send($customer->email(), "Order {$order->id()} placed");
    }
}
```

`OrderService` → `NotifierService` → `OrderService`. Symfony на этапе компиляции:

```
Circular reference found between services ("App\Service\OrderService" -> "App\Service\NotifierService" -> "App\Service\OrderService")
```

Вопрос не «как обмануть контейнер», а «что здесь на самом деле происходит?».

---

## Способ 1: Инверсия — «тот, кто знает», не спрашивает «того, кто не знает»

Разберу, что на самом деле делает `NotifierService`: ему нужен **клиент** заказа. А где лежит клиент? В `OrderRepository`, не в `OrderService`. `OrderService` — оркестратор, а не источник данных.

```php
// До: NotifierService тянет OrderService, чтобы узнать клиента
final class NotifierService
{
    public function __construct(
        private OrderService $orders,   // ← цикл
    ) {}
}

// После: NotifierService тянет репозиторий или сам Order
final class NotifierService
{
    public function __construct(
        private CustomerRepository $customers,  // ← нет цикла
    ) {}

    public function notify(Order $order): void
    {
        $customer = $this->customers->findById($order->customerId());
        $this->mailer->send($customer->email(), "Order {$order->id()} placed");
    }
}
```

**Почему это работает:** `NotifierService` не «нуждается в OrderService». Ему нужен `Customer`. `Customer` живёт в `CustomerRepository`. Цикл исчезает, потому что одна из двух стрелок была ложной — сервис «тянул» не то, что ему нужно, а то, что оказалось ближе по коду.

**Когда применять:** когда один из сервисов в цикле на самом деле является **источником данных**, а не **поведением**. Репозиторий, сущность, value object — это данные. Поведение — сервис. Данные не должны зависеть от поведения.

---

## Способ 2: Event dispatcher — «я не вызываю тебя, я объявляю факт»

Это самый частый и самый правильный ответ на «у меня цикл, потому что A после себя делает B».

Суть: `OrderService` не знает, кто реагирует на факт «заказ создан». Он **объявляет** факт. `NotifierService` **подписан** на этот факт. Ни один не знает о другом.

```php
// OrderPlacedEvent.php
final readonly class OrderPlacedEvent
{
    public function __construct(
        public readonly Order $order,
    ) {}
}
```

```php
// OrderService.php — цикл исчез
final class OrderService
{
    public function __construct(
        private PaymentService  $payment,
        private EventDispatcher $events,   // ← не NotifierService
    ) {}

    public function place(int $customerId, Cart $cart): Order
    {
        $order = Order::create($customerId, $cart);
        $result = $this->payment->charge($order);
        $order->applyPayment($result);

        $this->events->dispatch(new OrderPlacedEvent($order));

        return $order;
    }
}
```

```php
// NotifierService.php — подписчик
final class OrderPlacedListener
{
    public function __construct(
        private CustomerRepository $customers,
        private MailerInterface    $mailer,
    ) {}

    #[AsEventListener(event: OrderPlacedEvent::class)]
    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        $customer = $this->customers->findById($event->order->customerId());
        $this->mailer->send($customer->email(), "Order {$event->order->id()} placed");
    }
}
```

```yaml
# config/services.yaml
services:
    App\EventListener\OrderPlacedListener:
        tags: ['kernel.event_listener']
```

**Что получилось:**

- `OrderService` не знает о `NotifierService`.
- `NotifierService` не знает о `OrderService`.
- Оба знают только про `OrderPlacedEvent`.
- Добавление третьего подписчика (аналитика, лог, кэш-инвалидация) — один новый класс, ноль изменений в существующем.
- Цикл невозможен: события — односторонние.

**Когда применять:** когда связь A → B — это «после X сделаю Y», а не «чтобы сделать X, мне нужен Y». Событие — это «после». Вызов — это «чтобы».

**Ограничение:** события асинхронны по смыслу. Если B нужен **до** того, как A вернёт результат — событие не подойдёт. Тогда — способ 1 или 3.

---

## Способ 3: Вынесение общего в третий сервис

Часто цикл возникает потому, что **два сервиса оба делают одну вещь**, и каждая из них «тянет» другую, чтобы дотелеть.

```php
// До: цикл OrderService ↔ InventoryService
// OrderService: place() → резервирует инвентарь → нужен InventoryService
// InventoryService: release() → проверяет статус заказа → нужен OrderService

final class OrderService
{
    public function __construct(private InventoryService $inventory) {}

    public function place(Cart $cart): Order
    {
        $this->inventory->reserve($cart);   // ← тянет InventoryService
        // ...
    }
}

final class InventoryService
{
    public function __construct(private OrderService $orders) {}

    public function release(string $orderId): void
    {
        $order = $this->orders->get($orderId);  // ← тянет OrderService
        if ($order->isCancelled()) { /* не резервируем */ }
    }
}
```

Оба тянут «заказ». Обобщение: **состояние заказа** нужно обоим. Выносим:

```php
// OrderStatusService.php — единственный, кто знает о статусе
final class OrderStatusService
{
    public function __construct(
        private OrderRepository $repo,
    ) {}

    public function isActive(string $orderId): bool
    {
        return $this->repo->find($orderId)?->isActive() ?? false;
    }

    public function markPlaced(string $orderId): void
    {
        $this->repo->getById($orderId)->markPlaced();
        $this->repo->flush();
    }
}
```

```php
// OrderService.php — цикл исчез
final class OrderService
{
    public function __construct(
        private InventoryService   $inventory,
        private OrderStatusService $status,   // ← не InventoryService для статуса
    ) {}

    public function place(Cart $cart): Order
    {
        $order = Order::create($cart);
        $this->inventory->reserve($cart);
        $this->status->markPlaced($order->id());
        return $order;
    }
}

// InventoryService.php — цикл исчез
final class InventoryService
{
    public function __construct(
        private InventoryRepository $inventory,
        private OrderStatusService  $status,   // ← не OrderService
    ) {}

    public function release(string $orderId): void
    {
        if (!$this->status->isActive($orderId)) {
            return;
        }
        $this->inventory->releaseAll($orderId);
    }
}
```

**Почему это работает:** «общее» — состояние заказа. Оба сервиса не должны знать друг о друге, они оба знают о состоянии. Третий сервис — **общий зависимый**, не «связующий».

**Когда применять:** когда два сервиса в цикле оба обращаются к **одному и тому же источнику данных** или **одной и той же сущности**. Этот источник и есть третий сервис.

---

## Способ 4: `ServiceLocator` — ленивая односторонняя связь

Если цикл нельзя разорвать архитектурно (легаси, внешняя библиотека), можно **обрубить** одну из стрелок через `ServiceLocator`.

```php
// OrderService.php
final class OrderService
{
    public function __construct(
        /** @param ServiceLocator<NotifierService> $notifier */
        private ServiceLocator $notifier,   // ← ленивая ссылка
    ) {}

    public function place(Cart $cart): Order
    {
        $order = Order::create($cart);
        // ...
        $notifier = $this->notifier->get(NotifierService::class);
        $notifier->notify($order);
        return $order;
    }
}

// NotifierService.php — как был, так и есть
final class NotifierService
{
    public function __construct(private OrderService $orders) {}
}
```

```yaml
services:
    App\Service\OrderService:
        arguments:
            $notifier: '@service_locator.notifier'

    service_locator.notifier:
        class: 'Symfony\Component\DependencyInjection\ServiceLocator'
        factory: ['@service_container', 'locating']
        calls:
            - [set, ['App\Service\NotifierService', '@App\Service\NotifierService']]
```

**Что происходит:** `OrderService` получает не `NotifierService`, а **ленивую ссылку** на него. При создании `OrderService` `NotifierService` **не создаётся**. Он создаётся только в момент `$this->notifier->get(...)`. К этому моменту `OrderService` уже полностью сконструирован. Контейнер не видит цикл, потому что граф на этапе компиляции — однонаправленный.

**Когда применять:**
- Легаси-код, который нельзя рефакторить.
- Временная мера, пока вы не перешли на события (способ 2).
- Когда одна из зависимостей — «тяжёлый» сервис (создаётся дорого), и вы хотите создать его только при реальном использовании.

**Когда НЕ применять:**
- Как «решение» для нового кода. Это маскировка архитектурной проблемы.
- Если обе стороны в цикле — «тяжёлые». Тогда ленивость не спасает: всё равно обе создаются в одном request.

**Риск:** `ServiceLocator` — это «я не знаю, кто я, но кто-то знает». Стат-анализер теряет трассировку. Тестирование сложнее (нужен мок locator'а, а не мок сервиса). Это **экзотик**, не норма.

---

## Способ 5: Рефакторинг — один из сервисов не нужен

Иногда цикл — это **ошибка дизайна**, и один из сервисов вообще не должен существовать как отдельная сущность.

```php
// До: цикл
// OrderService → ShippingService → OrderService

// Анализ:
// OrderService.place() вызывает ShippingService.schedule()
// ShippingService.schedule() проверяет order->status()
//
// Но order->status() — это просто свойство.
// ShippingService не «нуждается в OrderService».
// Ему нужен Order. А Order уже есть в аргументе schedule().

// После:
final class ShippingService
{
    public function __construct(
        private CarrierClient $carrier,   // ← не OrderService
    ) {}

    public function schedule(Order $order): Shipment   // ← Order приходит как аргумент
    {
        if (!$order->isPlaced()) {
            throw new DomainException('Cannot ship an unplaced order');
        }
        return $this->carrier->book($order);
    }
}
```

`ShippingService` не тянет `OrderService`. Ему передают `Order`. Цикл исчезает. **Сервис, которому нужен объект, должен получать его, а не тянуть сервис, который его создаёт.**

---

## Антипаттерны

### 1. `static`-реестр

```php
// ❌
class ServiceRegistry
{
    private static array $services = [];

    public static function set(string $id, object $svc): void { self::$services[$id] = $svc; }
    public static function get(string $id): object { return self::$services[$id]; }
}

// OrderService:
self::get('notifier')->notify($order);

// Почему плохо:
// - Глобальное состояние. Тесты влияют друг на друга.
// - Стат-анализер не видит. IDE не подсказывает.
// - «Цикл» ушёл в невидимую область. Он никуда не делся.
```

### 2. `#[Autowire]` с циклом

```php
// ❌
#[Autowire]
final class OrderService
{
    public function __construct(
        #[Autowire] private NotifierService $notifier,  // ← автовайринг не поможет
    ) {}
}
```

Autowiring — это **заполнение аргументов по типу**. Он не знает и не знает о цикле. Цикл — проблема графа, а не типа. Autowiring здесь не помогает и не вредит: ошибка будет та же, на этапе компиляции.

### 3. «Я же могу просто `new` внутри метода»

```php
// ❌
final class OrderService
{
    public function place(Cart $cart): Order
    {
        $order = Order::create($cart);
        $notifier = new NotifierService($this);  // ← новый цикл, только в рантайме
        $notifier->notify($order);
    }
}
```

Цикл не исчез. Он переехал из compile-time в runtime. Теперь Symfony не ругается, но:
- `NotifierService` создаётся каждый вызов.
- Тестирование — нужно мокнуть `new`, что невозможно без DI.
- Стат-анализер не видит.
- Это **худшее из двух миров**: нет compile-time проверки и нет DI.

### 4. Взаимные `public`-модификаторы

```php
// ❌
final class OrderService
{
    public NotifierService $notifier;  // ← публичное свойство

    public function setNotifier(NotifierService $n): void { $this->notifier = $n; }
}
```

Это «инжект через сеттер». Работает, но:
- Объект **неполный** до вызова `setNotifier()`.
- Любой код может вызвать `setNotifier()` и сломать вайринг.
- Стат-анализер не может гарантировать, что свойство инициализировано.
- В `readonly` мире — вообще невозможно.

---

## Эвристика выбора

```
Есть цикл A ↔ B.

1. B нужен A для ПОЛУЧЕНИЯ ДАННЫХ (сущность, репозиторий)?
   → B тянет репозиторий, а не A. (Способ 1)

2. B нужен A для РЕАКЦИИ на факт, который A порождает?
   → Event dispatcher. (Способ 2)

3. A и B оба обращаются к ОДНОМУ И ТОМУ ЖЕ источнику?
   → Вынести источник в C. A → C ← B. (Способ 3)

4. B не «нужен» A, а A передаёт B объект, которым B оперирует?
   → B получает объект как аргумент. (Способ 5)

5. Легаси, время не позволяет рефакторить?
   → ServiceLocator. (Способ 4)
   → Пометить TODO. Вернуться к способам 1–5.
```

**Главный вопрос не «как обмануть контейнер», а «почему A и B оба знают о друг друге?»** Ответ на этот вопрос и определяет, какой из пяти способов применить. В 80% случаев это способ 1 или 2: кто-то тянет не то, что ему нужно, или связь — односторонняя по смыслу, а двусторонняя по реализации.

> ⏱ 33.5s · prompt 23322 tok · output 4345 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь просит описать, как организовать асинхронные post-commit действия (отправка письма + обновление поискового индекса) после успешной регистрации пользователя в Symfony, чтобы не замедлять ответ. Нужно описать варианты и trade-offs.

Варианты:
1. Event listeners (синхронные, в том же процессе) — самый простой, но НЕ решает задачу "не замедлять".
2. Symfony Messenger — queued transport (RabbitMQ/Redis/SQS) — самый "правильный" Symfony-подход.
3. Symfony Messenger — doctrine transport (БД как очередь) — компромисс.
4. Symfony Messenger — synchronous transport (in-memory) — для dev/тестов.
5. Своя очередь (RabbitMQ напрямую, без Messenger).
6. Cron-задача / batch.
7. Событие + асинхронный обработчик (worker-процесс).

Нужно структурировать:
- Ситуация: после регистрации → 2 действия (email + search index)
- Требование: не замедлять ответ
- Варианты от простого к сложному, с trade-offs

Структурирую ответ:
1. Коротко: что именно нужно (2 независимых действия, post-commit)
2. Варианты:
   a. Event listeners (синхронные) — почему НЕ подходит для этой задачи
   b. Messenger + queued transport (RabbitMQ/Redis) — основной
   c. Messenger + Doctrine transport — компромисс
   d. Messenger + вайронный worker (separate process)
   e. Своя интеграция с брокером (без Messenger)
   f. Cron/batch
3. Trade-offs таблица
4. Рекомендация для "обычного" проекта
5. Что я бы сделал конкретно — код

Дам конкретные примеры кода для каждого варианта, чтобы было понятно.

</details>

**Кандидат:**

# Асинхронные post-commit действия в Symfony

## Задача

После успешной регистрации (запись в БД, коммит) нужно:
- отправить email,
- обновить поисковый индекс (Elasticsearch/Meilisearch).

Оба действия **не нужны** для ответа клиенту. Клиент получил `201 Created` — всё. Письмо дойдёт через 2 секунды, индекс через 5 — не критично.

---

## Вариант 1: Event listeners (синхронные) — НЕ решает задачу

```php
// src/EventListener/OnUserRegistered.php
final class OnUserRegistered
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexer   $search,
    ) {}

    #[AsEventListener(event: UserRegisteredEvent::class)]
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->send(/* ... */);          // 200–800 мс
        $this->search->indexUser($event->user);  // 50–300 мс
    }
}
```

**Что происходит:** listener вызывается в том же PHP-процессе, в том же request, **до** того как клиент получит ответ.

| Плюс | Минус |
|---|---|
| Одна строка регистрации, ноль инфраструктуры | Ответ замедляется на сумму всех listener'ов |
| Тестируется как обычный метод | Если email-сервер упадёт — регистрация «успешна», но клиент ждал 30 секунд |
| Работает в dev из коробки | Нет retry, нет idempotency, нет наблюдимости |

**Вердикт:** подходит, если действия < 50 мс и их падение допустимо. Для email + search — **не подходит**, потому что требование именно «не замедлять ответ».

---

## Вариант 2: Symfony Messenger + queued transport (RabbitMQ / Redis / SQS)

Это основной «Symfony-нативный» путь.

### Архитектура

```
Request
  → RegistrationController
    → UserRegistrationService::register()
      → БД: INSERT + COMMIT          ← клиент получает 201
      → $bus->dispatch(new SendWelcomeEmail($userId))
      → $bus->dispatch(new IndexUser($userId))
                                           │
                                           ▼
                                     Transport (RabbitMQ / Redis)
                                           │
                                           ▼
                                     Worker-процесс (separate PHP process)
                                           │
                                           ├── SendWelcomeEmailHandler
                                           └── IndexUserHandler
```

### Код

**Сообщения (immutable, serializable):**

```php
// src/Messenger/SendWelcomeEmail.php
final readonly class SendWelcomeEmail
{
    public function __construct(
        public readonly int $userId,
    ) {}
}

// src/Messenger/IndexUser.php
final readonly class IndexUser
{
    public function __construct(
        public readonly int $userId,
    ) {}
}
```

**Диспатч (в сервисе, после commit):**

```php
// src/Service/UserRegistrationService.php
final class UserRegistrationService
{
    public function __construct(
        private UserRepository  $users,
        private EntityManager   $em,
        private MessageBus      $bus,
    ) {}

    public function register(RegistrationCommand $cmd): User
    {
        $user = User::create($cmd->email, $cmd->password);

        $this->users->add($user);
        $this->em->flush();

        // Post-commit: асинхронные действия
        $this->bus->dispatch(new SendWelcomeEmail($user->id()));
        $this->bus->dispatch(new IndexUser($user->id()));

        return $user;
    }
}
```

**Хендлеры:**

```php
// src/Messenger/Handler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private UserInterface    $users,
        private MailerInterface  $mailer,
    ) {}

    public function __invoke(SendWelcomeEmail $msg): void
    {
        $user = $this->users->find($msg->userId);

        $this->mailer->send(
            to:      $user->getEmail(),
            subject: 'Welcome!',
            body:    'Your account is ready.',
        );
    }
}

// src/Messenger/Handler/IndexUserHandler.php
final class IndexUserHandler
{
    public function __construct(
        private UserInterface  $users,
        private SearchIndexer  $search,
    ) {}

    public function __invoke(IndexUser $msg): void
    {
        $user = $this->users->find($msg->userId);
        $this->search->upsert($user);
    }
}
```

**Конфигурация:**

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        default_bus: messenger.bus.default

        buses:
            messenger.bus.default:
                middleware:
                    - 'messenger.middleware.validation'
                    - 'messenger.middleware.do_not_send_messages_for_tests'
                    - 'messenger.middleware.failure_listener'
                    - 'messenger.middleware.send_failed_message_for_retry_listener'

        transports:
            default: '%env(MESSENGER_TRANSPORT_DSN)%'
            # prod: amqp://guest:guest@localhost:5672/%2f/messages
            # dev:  sync://

        routing:
            'App\Messenger\SendWelcomeEmail': default
            'App\Messenger\IndexUser':        default
```

**Worker (separate process):**

```bash
# Docker / systemd / Supervisor
php bin/console messenger:consume default --time-limit=3600 --memory-limit=256M
```

### Trade-offs

| Плюс | Минус |
|---|---|
| Ответ клиенту — до dispatch. ~0 мс задержки на действия | Нужно разворачивать RabbitMQ / Redis |
| Retry: `max_retries: 3`, `delay: 5000` — встроенно | Один ещё один компонент в инфраструктуре |
| Idempotency: хендлер может проверить «уже обработано» | Серьёзнее тестирование (нужен fake-транспорт или `sync://`) |
| Наблюдаемость: `messenger:stats`, dead-letter | Сложнее debug'ить «почему письмо не ушло» (открыть RabbitMQ management UI) |
| Горизонтальное масштабирование: N workers | Overhead: сериализация → транспорт → десериализация |
| Разделение: email-worker и search-worker — разные процессы, разные лимиты памяти | |
| `Bus::dispatch()` — одна строка в сервисе | |

---

## Вариант 3: Messenger + Doctrine transport (БД как очередь)

```yaml
transports:
    default: doctrine://default
```

Вместо RabbitMQ — таблица `messenger_messages`. Worker читает из БД.

### Trade-offs vs RabbitMQ

| | RabbitMQ | Doctrine |
|---|---|---|
| Задержка | ~1 мс | ~5–20 мс (SELECT + UPDATE) |
| Throughput | 10k+ msg/s | 500–2000 msg/s (зависит от индексов) |
| Infra | +1 сервис | 0 (уже есть БД) |
| Reliability | Durable queues, ACK | ACID, но race на SELECT |
| Подходит для | Высокая нагрузка, микросервисы | 1–5k сообщений/день, один сервер |
| Операционная стоимость | RabbitMQ management, monitoring | Только БД |

**Когда Doctrine transport — достаточно:** малый проект, < 1000 регистраций/день, один сервер, нет второго сервиса.

**Когда уже мало:** > 5k msg/s, несколько инстансов приложения, need priority queues, DLQ с переработкой.

---

## Вариант 4: Messenger + `sync://` transport (in-memory)

```yaml
transports:
    default: sync://
```

Сообщение обрабатывается **немедленно, в том же процессе**, через хендлер. Формально — через Bus. Фактически — синхронно.

**Зачем:**
- Unit/integration тесты: `sync://` + `do_not_send_messages_for_tests` middleware → хендлеры не вызываются, или вызываются предсказуемо.
- Dev-режим: не нужен RabbitMQ.
- Формальное соответствие: код пишется через Bus, транспорт меняется в `.env`.

**Не решает** задачу «не замедлять ответ». Это sugar для тестируемости.

---

## Вариант 5: Прямая интеграция с брокером (без Messenger)

```php
// Не Messenger, а прямой publish в RabbitMQ / SQS / Kafka
final class EventPublisher
{
    public function __construct(
        private \Psr\EventDispatcher\EventDispatcherInterface $events,
        private \RabbitMQ\Channel $channel,
    ) {}

    public function publish(UserRegisteredEvent $event): void
    {
        $this->channel->basic_publish(
            json_encode($event->toArray()),
            exchange: 'app.events',
            routing_key: 'user.registered',
        );
    }
}
```

**Почему не стоит:**
- Теряете retry, DLQ, stats из Messenger.
- Теряете `#[AsMessageHandler]` и автообнаружение.
- Теряете `Bus::dispatch()` как единый контракт.
- Получаете «два слоя»: свой publisher + свой consumer.
- В Symfony это **антипаттерн**: Messenger создан именно для того, чтобы не писать свой.

**Когда оправдан:** микросервисная архитектура, где события — контракт между сервисами, а не внутренняя очередь. Или Kafka, который Messenger не поддерживает (напрямую).

---

## Вариант 6: Cron / batch

```bash
# crontab:每分钟
* * * * * php bin/console app:process-pending-actions
```

```php
// src/Command/ProcessPendingActions.php
#[AsCommand('app:process-pending-actions')]
final class ProcessPendingActions extends Command
{
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $pending = $this->actionsRepo->findPending();
        foreach ($pending as $action) {
            try {
                $this->executeAction($action);
                $action->markDone();
            } catch (\Throwable $e) {
                $action->incrementRetry();
            }
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Ноль дополнительной инфраструктуры | Задержка до 60 секунд (cron-интервал) |
| Простое масштабирование (lock) | Нет real-time |
| Идемпотентность — естественная (таблица) | «Почему письмо не ушло?» → проверить таблицу |
| | Не подходит, если действие нужно < 10 с |

**Когда:** batch-процессы (отчёт, индексация 10k записей), где задержка в минуту — норма.

---

## Сравнительная таблица

| Критерий | Event listener | Messenger + AMQP | Messenger + Doctrine | Cron |
|---|---|---|---|---|
| Задержка ответа | +300–1000 мс | +0 мс | +0 мс | +0 мс |
| Задержка действия | 0 (sync) | 1–5 с | 1–10 с | до 60 с |
| Retry | Нет | Встроенно | Встроенно | Ручной |
| DLQ | Нет | Да | Да | Нет |
| Наблюдаемость | Log | `messenger:stats` | `messenger:stats` | Таблица |
| Infra | 0 | RabbitMQ/Redis | 0 | 0 |
| Тестируемость | Простая | Средняя (fake transport) | Простая | Простая |
| Горизонтальное маштабирование | Нет | Да (N workers) | Ограничено (БД) | Да (lock) |
| Подходит для | < 50 мс, нет retry | Высокая нагрузка, несколько сервисов | Малый проект, 1 сервер | Batch |

---

## Что я бы сделал для «обычного» проекта

### Если 1–5k регистраций/день, один сервер, нет микросервисов

```
Messenger + Doctrine transport
```

Почему:
- Zero additional infra.
- Retry + DLQ из коробки.
- `messenger:stats` в console.
- Тестируется с `sync://` в `.env.test`.
- Если через год нагрузка вырастет — меняю DSN на `amqp://`, **ни строчки кода** не трогаю.

```yaml
# .env
MESSENGER_TRANSPORT_DSN=doctrine://default

# .env.test
MESSENGER_TRANSPORT_DSN=sync://
```

### Если > 10k/день, несколько сервисов, need < 1 с доставка

```
Messenger + RabbitMQ
+ 2 worker-процесса (email, search)
+ Sentry / Datadog для failed messages
```

### Если критично «письма не теряются»

```
RabbitMQ + persistent queue + ACK
+ idempotency key в хендлере (message ID)
+ dead-letter queue + алерт
```

---

## Конкретная реализация (Doctrine transport, полный цикл)

```php
// src/Service/UserRegistrationService.php
final class UserRegistrationService
{
    public function __construct(
        private UserInterface $users,
        private EntityManager $em,
        private MessageBus    $bus,
    ) {}

    public function register(RegistrationCommand $cmd): User
    {
        // 1. Валидация
        $this->assertValid($cmd);

        // 2. Создание + persist
        $user = User::create($cmd->email, $cmd->password);
        $this->users->add($user);
        $this->em->flush();  // ← COMMIT. Клиент получит 201 после этого.

        // 3. Асинхронные действия (не блокируют ответ)
        $this->bus->dispatch(new SendWelcomeEmail($user->id()));
        $this->bus->dispatch(new IndexUserSearch($user->id()));

        return $user;
    }
}
```

```php
// src/Messenger/Handler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private UserInterface  $users,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    #[AsMessageHandler]
    public function __invoke(SendWelcomeEmail $msg): void
    {
        $user = $this->users->find($msg->userId);

        if ($user === null || $user->isDeleted()) {
            $this->logger->info('Skip email: user deleted or gone', ['user' => $msg->userId]);
            return;  // idempotent: повторная обработка — no-op
        }

        $this->mailer->send(
            to:      $user->getEmail(),
            subject: 'Welcome to the platform',
            body:    (new EmailTemplate('welcome', ['name' => $user->getFirstName()]))->render(),
        );
    }
}
```

```php
// src/Messenger/Handler/IndexUserSearchHandler.php
final class IndexUserSearchHandler
{
    public function __construct(
        private UserInterface $users,
        private SearchIndexer $search,
    ) {}

    #[AsMessageHandler]
    public function __invoke(IndexUserSearch $msg): void
    {
        $user = $this->users->find($msg->userId);

        if ($user === null || $user->isDeleted()) {
            $this->search->remove('user', $msg->userId);
            return;
        }

        $this->search->upsert('user', $msg->userId, [
            'id'    => $msg->userId,
            'name'  => $user->getFullName(),
            'email' => $user->getEmail(),
            'tags'  => $user->getTags()->toArray(),
        ]);
    }
}
```

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            default: '%env(MESSENGER_TRANSPORT_DSN)%'
            async:   '%env(MESSENGER_TRANSPORT_DSN)%'

        routing:
            'App\Messenger\SendWelcomeEmail': default
            'App\Messenger\IndexUserSearch':  default

        default_bus: messenger.bus.default

        buses:
            messenger.bus.default:
                middleware:
                    - 'messenger.middleware.validation'
                    - 'messenger.middleware.handle_message'
                    - 'messenger.middleware.send_failed_message_for_retry_listener'
                    - 'messenger.middleware.failure_listener'
```

```bash
# Docker compose / systemd: worker
php bin/console messenger:consume default \
    --time-limit=3600 \
    --memory-limit=256M \
    --sleep=5000 \
    --limit=100
```

### Что происходит при регистрации

```
t=0ms    POST /register
t=5ms    Validation
t=8ms    INSERT INTO users ...
t=10ms   COMMIT
t=11ms   INSERT INTO messenger_messages (2 rows)
t=12ms   ← 201 Created → клиент
         (ответ ушёл. Email и search ещё не сделаны.)

t=15ms   [worker] SELECT FROM messenger_messages WHERE visible = false
t=18ms   [worker] DESERIALIZE SendWelcomeEmail
t=200ms  [worker] SMTP send → 250 OK
t=205ms  [worker] DELETE FROM messenger_messages WHERE id = ?
t=210ms  [worker] SELECT ... IndexUserSearch
t=350ms  [worker] ES upsert → 201
t=355ms  [worker] DELETE ...
```

Клиент получил ответ на 12-й миллисекунде. Email и search — до 350-й. Разрыв — 338 мс. Это и есть «не замедлять ответ».

---

## Что я бы НЕ делал

- **Не ставил бы RabbitMQ на 200 регистраций в день.** Это overhead. Doctrine transport закрывает задачу.
- **Не писал бы «свою очередь».** Messenger уже даёт retry, DLQ, stats, testing. Переиспользование > изобретение.
- **Не делал бы email и search в одном хендлере.** Если search упадёт — email не должен переисполняться. Два сообщения, два хендлера, независимые retry.
- **Не dispatch'ил бы ДО commit.** Если commit откатится — у вас email «Welcome» для несуществующего пользователя. Post-commit — единственно правильный момент.
- **Не использовал бы `kernel.response` event.** Это «после ответа», но в том же процессе. Для async — только Messenger.

> ⏱ 38.6s · prompt 31389 tok · output 4909 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о системе контроля доступа в Symfony: аутентификация, роли, voters. И хочет понять, когда voter, а когда достаточно ролей. Это продолжение серии технических вопросов о Symfony/PHP. Нужно дать структурированный ответ с кодом, показать как всё связывается, и дать чёткий критерий выбора между ролями и voters.

Структура:
1. Общая схема: Authentication → Authorization → (roles | voter | attribute)
2. Аутентификация — как работает (firewall, authenticator, token)
3. Роли — как назначаются, как проверяются
4. Voters — как работают, пример
5. Attribute-based (#[IsGranted])
6. Когда voter, когда роли — критерий
7. Антипаттерны

Буду честен: я языковая модель, у меня нет "опыта", но я знаю архитектуру Symfony Security хорошо. Буду отвечать в том же стиле — конкретно, с кодом, с trade-offs.

</details>

**Кандидат:**

# Контроль доступа в Symfony: от логина до «может ли этот пользователь удалить эту запись»

## Общая схема

```
Request
  │
  ▼
┌──────────────────────────────────────────────────────────────────┐
│  Firewall (security.yaml)                                       │
│                                                                 │
│  1. Authentication: КТО этот пользователь?                       │
│     → Token с User + ролями                                     │
│                                                                 │
│  2. Authorization: ЕМУ ПОЗВОЛЕНО?                                │
│     → Ролевые проверки:  ROLE_ADMIN, ROLE_EDITOR                │
│     → Voter:              может ли этот user удалить ЭТУ order?  │
│     → Expression:         user.id === object.ownerId            │
│     → #[IsGranted]:       любой из трёх, на уровне метода       │
└──────────────────────────────────────────────────────────────────┘
  │
  ▼
Controller / Service
```

Ключевое разделение: **аутентификация** — «кто ты», **авторизация** — «что тебе можно». Роли и voters — оба инструмента авторизации, но на разных уровнях детализации.

---

## Аутентификация: как Symfony определяет «кто ты»

### Firewalls (security.yaml)

```yaml
security:
    providers:
        app_user:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|js|img|fonts)/
            security: false

        api:
            pattern: ^/api
            stateless: true
            json_login:
                check_path: /api/login
                username_path: email
                password_path: password
                success_handler: App\Security\ApiAuthSuccessHandler
                failure_handler: App\Security\ApiAuthFailureHandler

        main:
            pattern: ^/
            lazy: true              # токен создаётся только при первом обращении
            provider: app_user
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
                enable_csrf: true
            logout:
                path: app_logout
                target: app_login

            remember_me:
                secret: '%env(APP_SECRET)%'
                lifetime: 604800
                name: REMEMBER_ME
                path: /
                secure: true
```

### Что происходит при `POST /api/login`

```
1. Firewall перехватывает запрос на /api/login
2. JsonLoginAuthenticator::authenticate():
   a. Читает JSON body: { "email": "a@b.c", "password": "..." }
   b. UserChecker::checkPreAuth()   ← password, locked, expired
   c. PasswordHasher::verify()      ← bcrypt/argon2
   d. UserChecker::checkPostAuth()  ← (редко)
   e. Создаёт Token:
      new SelfValidatingToken($user, $roles)
      // $user->getRoles() → ['ROLE_USER', 'ROLE_EDITOR']
3. TokenStorage::setToken($token)
4. SuccessHandler → 200 + { "user_id": 42, "token": "..." }
```

### Token — центральная сущность

```php
// Symfony\Component\Security\Core\Authentication\Token\TokenInterface
interface TokenInterface
{
    public function getUser(): ?UserInterface;
    public function getRoleNames(): array;       // ['ROLE_USER', 'ROLE_EDITOR']
    public function getUserIdentifier(): string;
}
```

После аутентификации **каждый** запрос в этом firewall'е несёт Token. `Security::getUser()` — это просто `TokenStorage->getToken()->getUser()`.

### Custom Authenticator (Symfony 6+, рекомендуемый путь)

Когда `form_login` / `json_login` не хватает — свой authenticator:

```php
// src/Security/ApiKeyAuthenticator.php
final class ApiKeyAuthenticator extends AbstractAuthenticationForm
{
    public const CREDENTIALS_KEY = 'X-API-Key';

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::CREDENTIALS_KEY);
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get(self::CREDENTIALS_KEY);
        $user = $this->apiKeyRepository->findByKey($apiKey);

        if ($user === null || !$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(
            UserBadge($user),
            new SelfValidatingBadge(),
        );
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        $user = $passport->getUser();
        $token = new SelfValidatingToken($user, $user->getRoles(), $firewallName);
        $token->setRequestAttributes(['api_key_source' => true]);
        return $token;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $e): ?Response
    {
        return new Response('Forbidden', Response::HTTP_FORBIDDEN);
    }
}
```

```yaml
# security.yaml
firewalls:
    api_keys:
        pattern: ^/api/v2
        stateless: true
        custom_authenticator: App\Security\ApiKeyAuthenticator
```

**Почему authenticator, а не `guard` (устарело с 5.3):** каждый authenticator — один класс, одна логика. `guard` — 3 метода, 3 конфигурации. Authenticator — 1.

---

## Роли: что это и как они работают

### Откуда роли берутся

```php
// src/Entity/User.php
#[ORM\Entity]
class User implements UserInterface
{
    /** @var string[] */
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    public function getRoles(): array
    {
        // ROLE_USER — минимальная роль, добавляется автоматически
        // если не указана. Не путать с custom ролями.
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }
}
```

Или из БД, или из `security.yaml`:

```yaml
# Статические роли (редко)
security:
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER, ROLE_EDITOR, ROLE_AUDITOR]
        ROLE_SUPER_ADMIN: [ROLE_ADMIN]
```

### Как проверяются

```php
// 1. В шаблоне Twig
{% if is_granted('ROLE_ADMIN') %}
    <button>Delete</button>
{% endif %}

// 2. В контроллере (attribute)
#[Route('/admin/users', name: 'admin_users')]
#[IsGranted('ROLE_ADMIN')]
public function users(): Response { ... }

// 3. В коде
$security->isGranted('ROLE_EDITOR');  // true/false
// или
$security->isGranted('ROLE_EDITOR', $post);  // делегируется voter'у
```

### Что роль **не** делает

Роль — это **метка на пользователе**. Она не знает об объектах. `ROLE_EDITOR` говорит «этот пользователь — редактор». Она не отвечает на вопрос «может ли он удалить **этот конкретный** пост, написанный **другим** автором?»

Это вопрос для voter'а или expression.

---

## Voters: когда роли не хватает

### Как работает

```php
// src/Security/Voter/OrderVoter.php
final class OrderVoter extends Voter
{
    public const VIEW   = 'ORDER_VIEW';
    public const EDIT   = 'ORDER_EDIT';
    public const DELETE = 'ORDER_DELETE';

    // 1. Какие атрибуты этот voter понимает?
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Order;
    }

    // 2. Решение: может ли $token (пользователь) выполнить $attribute на $subject?
    protected function voteOnAttribute(
        string $attribute,
        mixed   $subject,
        TokenInterface $token,
    ): bool {
        /** @var User $user */
        $user  = $token->getUser();
        $order = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($user, $order),
            self::EDIT => $this->canEdit($user, $order),
            self::DELETE => $this->canDelete($user, $order),
            default => false,
        };
    }

    private function canView(User $user, Order $order): bool
    {
        // Владелец — может
        if ($order->ownerId === $user->getId()) return true;

        // Админ — может
        if (in_array('ROLE_ADMIN', $user->getRoles())) return true;

        // Менеджер — видит все заказы в своём отделе
        if (in_array('ROLE_MANAGER', $user->getRoles())) {
            return $order->departmentId === $user->departmentId;
        }

        return false;
    }

    private function canEdit(User $user, Order $order): bool
    {
        if ($order->ownerId !== $user->getId()) return false;
        if ($order->status !== OrderStatus::Open) return false;  // закрытый не редактируется
        if ($order->createdAt < (new \DateTimeImmutable('-48h'))) return false;

        return true;
    }

    private function canDelete(User $user, Order $order): bool
    {
        // Только админ, и только закрытые заказы
        if (!in_array('ROLE_ADMIN', $user->getRoles())) return false;
        return $order->status === OrderStatus::Closed;
    }
}
```

### Как вызывается

```php
// В контроллере:
#[Route('/orders/{id}', name: 'order_show')]
#[IsGranted(OrderVoter::VIEW, 'order')]   // ← subject = параметр $order
public function show(Order $order, Security $security): Response
{
    return $this->render('order/show.html.twig', ['order' => $order]);
}

// В сервисе (вне контроллера):
$security->isGranted(OrderVoter::DELETE, $order);

// В шаблоне Twig:
{% if is_granted('ORDER_DELETE', order) %}
    <a href="{{ path('order_delete', {id: order.id}) }}">Delete</a>
{% endif %}
```

### Механика внутри Security

```
isGranted('ORDER_DELETE', $order)
  │
  ▼
AuthorizationChecker::isGranted()
  │
  ├── 1. Для каждого Voter (в порядке регистрации):
  │     voter->supports('ORDER_DELETE', $order)
  │       → true? → voter->voteOnAttribute('ORDER_DELETE', $order, $token)
  │         → true  → ACCESS_GRANTED  ← стоп
  │         → false → ACCESS_ABSTAIN  ← продолжаем
  │       → false? → пропускаем этого voter
  │
  ├── 2. Если ни один voter не вернул GRANTED:
  │     ACCESS_DENIED → 403
  │
  └── 3. Ролевые атрибуты (ROLE_*) обрабатываются
         встроенным RoleVoter (напрямую, не через кастомный)
```

**ACCESS_ABSTAIN** — ключевое. Voter говорит «я не знаю, решайте дальше». Это позволяет нескольким voter'ам сотрудничать: `OrderVoter` — «может ли редактировать?», `TimeWindowVoter` — «в рабочее ли время?». Оба должны сказать GRANTED.

---

## Expression: когда voter — overkill

Для простых условий не нужен отдельный класс:

```php
// В контроллере:
#[Route('/users/{id}/profile')]
#[IsGranted("user.id == id or hasRole('ROLE_ADMIN')")]
public function profile(User $user): Response { ... }

// В шаблоне:
{% if is_granted("post.authorId == app.user.id or hasRole('ROLE_ADMIN')") %}
```

Expression-язык:

| Функция | Пример |
|---|---|
| `user` | текущий `UserInterface` |
| `object` | subject |
| `hasRole('ROLE_X')` | проверка роли |
| `property` | доступ к property объекта |
| `constant` | `App\Enum\Status::Open` |
| арифметика, сравнение | `object.createdAt > constant("now")` |

**Когда expression хватает:** условие на 1–2 строки, не меняется часто, не тестируется отдельно.
**Когда нужен voter:** условие на 10+ строк, с бизнес-логикой, используется в 5+ местах, нужно покрыть тестами.

---

## Когда voter, а когда достаточно ролей

### Критерий

```
Вопрос: «может ли пользователь сделать X?»

Если ответ зависит ТОЛЬКО от роли:
  → isGranted('ROLE_ADMIN')
  Пример: «только админы видят страницу /admin/settings»

Если ответ зависит от РОЛИ + ДАННЫХ:
  → Voter / Expression
  Пример: «пользователь видит свой профиль,
           но НЕ видит чужой, если он не менеджер того же отдела»
```

### Конкретные ситуации

| Ситуация | Инструмент | Почему |
|---|---|---|
| Страница доступна только админам | `ROLE_ADMIN` | Не зависит от объекта |
| CRUD на сущностях своего отдела | `ROLE_MANAGER` + voter | Роль говорит «ты менеджер», voter говорит «на ЭТОТ отдел» |
| Только автор может редактировать пост | Voter / Expression | Зависит от `post.authorId` |
| Админ может всё, пользователь — только свои | Voter с `ROLE_ADMIN` внутри | Роль — одна из веток, не весь ответ |
| Числовой лимит: «не более 5 заказов в день» | Voter (или доменное правило) | Роль не хранит счётчики |
| Временное окно: «редактирование до 48ч» | Voter | Зависит от `createdAt` |
| Гостевой доступ: «видит 3 статьи, дальше — нет» | Voter | Зависит от `article.isPremium` |
| API: «этот API-key имеет scope `orders:read`» | Custom voter / expression | Scope ≠ роль, но по структуре похож |

### Антипаттерн: роли для всего

```yaml
# ❌ 47 ролей
ROLE_USER
ROLE_USER_CAN_VIEW_ORDERS
ROLE_USER_CAN_EDIT_OWN_ORDERS
ROLE_USER_CAN_EDIT_DEPT_ORDERS
ROLE_USER_CAN_DELETE_CLOSED_ORDERS
ROLE_USER_CAN_EXPORT_REPORTS
ROLE_USER_CAN_IMPORT_REPORTS
# ...
```

Каждая новая бизнес-правило → новая роль. Роль превращается в «флаг». Управление: «дайте Ивану право удалять закрытые заказы» → добавляем роль в БД → пересобираем токен.

**Правильно:** `ROLE_USER` (базовая) + `ROLE_MANAGER` (отдел) + `ROLE_ADMIN` (всё). Всё остальное — voter, который знает `user.deptId`, `order.status`, `order.deptId`.

### Антипаттерн: voter для всего

```php
// ❌ Voter на 200 строк, 8 веток if/else,
//    где 7 из них — «if role === ROLE_X return true»
protected function voteOnAttribute(...): bool
{
    if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) return true;
    if (in_array('ROLE_ADMIN', $user->getRoles())) return true;
    if (in_array('ROLE_MANAGER', $user->getRoles()) && $order->dept === $user->dept) return true;
    if (in_array('ROLE_EDITOR', $user->getRoles()) && $order->status === 'open') return true;
    if ($order->owner === $user->id) return true;
    // ...
    return false;
}
```

Это работает, но:
- Тестирование: 20+ тестов на один метод.
- Изменение: «а если теперь менеджер может редактировать и закрытые?» → правка в 200-строчном методе.
- Читаемость: нужно читать все 8 веток, чтобы понять, что «менеджер может».

**Правильно:** разбить на 2–3 voter'а. `DepartmentVoter` — «в пределах отдела?». `StatusVoter` — «статус позволяет?». `OwnershipVoter` — «владелец?». Каждый — 10–20 строк. Каждый тестируется отдельно.

---

## Полный пример: контроллер + voter + expression

```php
// src/Controller/OrderController.php
final class OrderController
{
    public function __construct(
        private OrderService $orders,
        private Security     $security,
    ) {}

    // Список: все свои + (менеджер) все в отделе
    #[Route('/orders', name: 'order_index')]
    public function index(): Response
    {
        $user  = $this->security->getUser();
        $query = $this->orders->search();

        if (!$this->security->isGranted('ROLE_MANAGER')) {
            $query = $query->whereOwner($user->getId());
        } else {
            $query = $query->whereDepartment($user->departmentId);
        }

        return $this->render('order/index.html.twig', [
            'orders' => $query->getResult(),
        ]);
    }

    // Показ: только свои или менеджер отдела или админ
    #[Route('/orders/{id}', name: 'order_show', requirements: {'id' => '\d+'})]
    #[IsGranted(OrderVoter::VIEW, 'order')]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', ['order' => $order]);
    }

    // Редактирование: только владелец, только open, только < 48ч
    #[Route('/orders/{id}/edit', name: 'order_edit')]
    #[IsGranted(OrderVoter::EDIT, 'order')]
    public function edit(Order $order, Request $request): Response
    {
        // ...
    }

    // Удаление: только админ, только closed
    #[Route('/orders/{id}', name: 'order_delete', methods: ['DELETE'])]
    #[IsGranted(OrderVoter::DELETE, 'order')]
    public function delete(Order $order): Response
    {
        $this->orders->delete($order);
        return $this->redirectToRoute('order_index');
    }
}
```

```twig
{# order/show.html.twig #}
<h1>Order #{{ order.id }}</h1>
<p>Status: {{ order.status.value }}</p>

{% if is_granted('ORDER_EDIT', order) %}
    <a href="{{ path('order_edit', {id: order.id}) }}">Edit</a>
{% endif %}

{% if is_granted('ORDER_DELETE', order) %}
    <form method="post" action="{{ path('order_delete', {id: order.id}) }}"
          onsubmit="return confirm('Delete?')">
        <button>Delete</button>
    </form>
{% endif %}
```

```php
// src/Security/Voter/OrderVoter.php (как выше)
```

---

## Тестирование voters

```php
// tests/Security/OrderVoterTest.php
final class OrderVoterTest extends TestCase
{
    private OrderVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new OrderVoter();
    }

    private function tokenWith(array $roles, int $id, int $dept = 1): TokenInterface
    {
        $user = new User();
        $user->setRoles($roles);
        $user->setId($id);
        $user->setDepartmentId($dept);
        return new SelfValidatingToken($user, $roles, 'test');
    }

    public function testOwnerCanViewOwnOrder(): void
    {
        $order = Order::create(ownerId: 42, deptId: 1, status: OrderStatus::Open);
        $token = $this->tokenWith(['ROLE_USER'], 42, dept: 1);

        $this->assertTrue(
            $this->voter->vote($token, $order, OrderVoter::VIEW)
        );
    }

    public function testOwnerCannotViewOthersOrder(): void
    {
        $order = Order::create(ownerId: 99, deptId: 1, status: OrderStatus::Open);
        $token = $this->tokenWith(['ROLE_USER'], 42, dept: 1);

        $this->assertFalse(
            $this->voter->vote($token, $order, OrderVoter::VIEW)
        );
    }

    public function testManagerCanViewOrdersInOwnDepartment(): void
    {
        $order = Order::create(ownerId: 99, deptId: 2, status: OrderStatus::Open);
        $token = $this->tokenWith(['ROLE_MANAGER'], 42, dept: 2);

        $this->assertTrue(
            $this->voter->vote($token, $order, OrderVoter::VIEW)
        );
    }

    public function testManagerCannotViewOrdersInOtherDepartment(): void
    {
        $order = Order::create(ownerId: 99, deptId: 3, status: OrderStatus::Open);
        $token = $this->tokenWith(['ROLE_MANAGER'], 42, dept: 2);

        $this->assertFalse(
            $this->voter->vote($token, $order, OrderVoter::VIEW)
        );
    }

    public function testAdminCanViewAnyOrder(): void
    {
        $order = Order::create(ownerId: 99, deptId: 3, status: OrderStatus::Closed);
        $token = $this->tokenWith(['ROLE_ADMIN'], 1, dept: 99);

        $this->assertTrue(
            $this->voter->vote($token, $order, OrderVoter::VIEW)
        );
    }

    public function testCannotEditClosedOrder(): void
    {
        $order = Order::create(ownerId: 42, deptId: 1, status: OrderStatus::Closed);
        $token = $this->tokenWith(['ROLE_USER'], 42, dept: 1);

        $this->assertFalse(
            $this->voter->vote($token, $order, OrderVoter::EDIT)
        );
    }

    public function testCannotDeleteNonClosedOrder(): void
    {
        $order = Order::create(ownerId: 42, deptId: 1, status: OrderStatus::Open);
        $token = $this->tokenWith(['ROLE_ADMIN'], 1, dept: 99);

        $this->assertFalse(
            $this->voter->vote($token, $order, OrderVoter::DELETE)
        );
    }
}
```

---

## Итого: дерево решений

```
Нужна проверка доступа.

1. «Кто ты?» → Аутентификация (firewall, authenticator, token)
   Роль в этом этапе: просто данные на user.

2. «Ты вообще можешь зайти на эту страницу/endpoint?»
   → Роли: isGranted('ROLE_ADMIN')
   → Expression: is_granted("user.status == 'active' and hasRole('ROLE_USER')")

3. «Можешь ли ты сделать это с ЭТИМ объектом?»
   → Voter: isGranted('ORDER_EDIT', $order)
   → Expression (если условие ≤ 2 строки): is_granted("order.ownerId == user.id")

4. «Можешь ли ты сделать это с ЭТИМ объектом в ЭТОМ контексте?»
   → Voter (несколько, каждый за свой аспект)
   → Доменное правило (Order::assertEditable()) + voter обёртывает
```

Роли — **кто ты**. Voters — **что тебе можно с этим**. Не подменяйте одно другим.

> ⏱ 44.3s · prompt 35911 tok · output 5836 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении Value Objects (VO) в Doctrine, при ограничениях: неизменяемость + всегда валидное состояние. Это глубокий вопрос на стыке DDD и ORM. Нужно разобрать:

1. Что такое VO в домене: readonly, конструктор валидирует, нет setters.
2. Проблема: Doctrine работает через reflection, setters/getters, mutable state.
3. Способы маппинга:
   - Приватный constructor + public factory (static create/validate) — классика
   - #[ORM\Embedded] с EmbeddedObject — встроенные объекты
   - #[ORM\Column] с custom Type (serialize/deserialize) — для простых скалярных VO
   - #[ORM\ManyToOne] + custom mapping — для VO, которые ссылаются на другие сущности
   - #[ORM\Embedded] + readonly class (PHP 8.2) — но Doctrine не поддерживает readonly class напрямую
   - DTO-подход: VO только в домене, в persistence-слое — плоские поля
   - Custom Type для compound keys
   - #[ORM\CustomIdGenerator] — не про VO, но смежно

Подводные камни:
- Doctrine не поддерживает readonly properties напрямую (ReflectionProperty::setValue на readonly → Error)
- #[ORM\Embedded] требует no-arg constructor или constructor injection — но Doctrine не умеет DI в embedded
- Lazy loading + VO = проблема: lazy proxy не может быть immutable
- equals() / value equality: Doctrine сравнивает по identity, а VO — по value. Двойная запись.
- Cascade persist на embedded: если VO содержит ссылку на сущность — cascade + cycle detection
- Serialization: serialize() на VO с readonly — работает, но deserialization обходит конструктор (unserialize не вызывает __construct). Это обход валидации!
- Cloning: __clone на readonly class — copy-on-write, но Doctrine clone для detached → managed
- PHP 8.2 readonly class: все свойства readonly, класс нельзя наследовать. Doctrine Embedded требует наследуемости? Нет, но требует no-arg или constructor.

Структурирую ответ:
1. Проблема: почему "просто" не работает
2. Способ 1: Приватный конструктор + static factory (классика, работает всегда)
3. Способ 2: #[ORM\Embedded] (встроенный объект)
4. Способ 3: Custom Type (скалярный VO в одном столбце)
5. Способ 4: Плоское хранилище (VO только в домене, в БД — поля)
6. Подводные камни: unserialize, lazy loading, equality, cascade
7. Рекомендация по выбору

Напишу на русском, с кодом, как в предыдущих ответах.

</details>

**Кандидат:**

# Value Object в Doctrine: неизменяемость vs рефлексия

## Почему «просто» не работает

Домен требует:

```php
final readonly class Money
{
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {
        if ($amountCents < 0) {
            throw new DomainException('Amount must be non-negative');
        }
        if ($currency === '') {
            throw new DomainException('Currency must not be empty');
        }
    }
}

// В домене:
$order->total = new Money(5000, 'EUR');       // ✅ валидный
// $order->total->amountCents = 0;            // ❌ Error: readonly
// new Money(-1, 'USD');                      // ❌ Exception в конструкторе
```

Doctrine работает иначе:

- Создаёт объект через `newInstance()` (без конструктора) или с аргументами.
- Устанавливает свойства через `ReflectionProperty::setValue()` — **обходит конструктор**.
- Сравнивает по identity (`$a === $b`), а не по значению.
- Делает `clone` для detached → managed.
- Серилизует/десериализует через `unserialize()`, который **не вызывает `__construct`**.

Каждый из этих пунктов конфликтует с «валидация в конструкторе» и «immutable».

---

## Способ 1: Приватный конструктор + static factory

Самый распространённый и самый надёжный.

```php
final class Money
{
    private function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {
        // Ничего. Конструктор приватный — извне не вызывается.
    }

    public static function fromCents(int $cents, string $currency): self
    {
        if ($cents < 0) {
            throw new DomainException('Amount must be non-negative', code: 'MONEY_NEGATIVE');
        }
        if ($currency === '') {
            throw new DomainException('Currency must not be empty', code: 'MONEY_NO_CURRENCY');
        }

        return new self($cents, $currency);
    }

    public static function zero(string $currency): self
    {
        return self::fromCents(0, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch', code: 'MONEY_CURRENCY_MISMATCH');
        }
        return self::fromCents($this->amountCents + $other->amountCents, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return number_format($this->amountCents / 100, 2) . ' ' . $this->currency;
    }
}
```

### Как маппить в Entity

**Вариант А: два отдельных столбца**

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    public function total(): Money
    {
        return Money::fromCents($this->totalCents, $this->totalCurrency);
    }

    public function setTotal(Money $money): void
    {
        // fromCents уже валидирует. Здесь — только запись.
        $this->totalCents    = $money->amountCents;
        $this->totalCurrency = $money->currency;
    }
}
```

**Вариант Б: Embedded object**

```php
#[ORM\Embedded(class: Money::class)]
private Money $total;

#[ORM\Embeddable]
final class Money
{
    private function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {}

    public static function fromCents(int $cents, string $currency): self
    {
        if ($cents < 0) throw new DomainException('...');
        if ($currency === '') throw new DomainException('...');
        return new self($cents, $currency);
    }

    // Скорее всего нужны геттеры/сеттеры для Doctrine
    public function getAmountCents(): int    { return $this->amountCents; }
    public function getCurrency(): string    { return $this->currency; }
    public function setAmountCents(int $v): void { $this->amountCents = $v; }
    public function setCurrency(string $v): void { $this->currency = $v; }
}
```

### Плюсы

| | |
|---|---|
| Валидация **гарантирована** | Единственный путь создания — `fromCents()`. Никакого `new Money(-1, '')` извне. |
| Неизменяемость | `readonly` + приватный конструктор. Никто не может «забыть» обновить одно поле и не другое. |
| Тестируется без Doctrine | `Money::fromCents(-1, 'USD')` → Exception. Без БД, без моков. |
| Домен не знает про Doctrine | `Money` не содержит `#[ORM\Embeddable]`. Может жить без ORM. |

### Минусы

| | |
|---|---|
| `readonly` ломает Embedded | Doctrine устанавливает свойства через Reflection. `ReflectionProperty::setValue` на readonly → `Error: Cannot modify readonly property`. |
| Нужно снимать `readonly` | Либо убираете `readonly` и доверяете «никто не поменяет», либо держите `readonly` и не используете Embedded (Вариант А). |
| Дублирование валидации | `fromCents()` валидирует. Но Doctrine **обходит** его при hydrate. В БД может лежать мусор, если кто-то вставил SQL-запрос напрямую. |
| `equals()` vs `===` | Doctrine сравнивает по identity. VO — по value. `$order1->total !== $order2->total` даже если оба `Money(100, 'USD')`. Unit of Work может сделать лишний UPDATE. |

---

## Способ 2: `#[ORM\Embedded]` — встроенный объект

Работает, когда VO — «простая структура данных», а не «сущность с поведением».

```php
#[ORM\Embeddable]
class Address
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $street;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $zipCode = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $city;

    // Для Doctrine:
    public function __construct(
        string $street,
        string $city,
        ?string $zipCode = null,
    ) {
        $this->street  = $street;
        $this->city    = $city;
        $this->zipCode = $zipCode;
    }

    // Для Doctrine:
    public function getStreet(): string  { return $this->street; }
    public function getZipCode(): ?string { return $this->zipCode; }
    public function getCity(): string    { return $this->city; }

    public function setStreet(string $v): void  { $this->street = $v; }
    public function setZipCode(?string $v): void { $this->zipCode = $v; }
    public function setCity(string $v): void    { $this->city = $v; }

    // Доменное поведение:
    public function isValid(): bool
    {
        return $this->street !== ''
            && $this->city !== ''
            && ($this->zipCode === null || preg_match('/^\d{4,10}$/', $this->zipCode));
    }
}
```

```php
#[ORM\Entity]
class Customer
{
    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    public function setAddress(string $street, string $city, ?string $zip): void
    {
        $addr = new Address($street, $city, $zip);
        if (!$addr->isValid()) {
            throw new DomainException('Invalid address');
        }
        $this->address = $addr;
    }
}
```

### Подводные камни Embedded

**1. Doctrine обходит конструктор при hydrate.**

```sql
-- В БД: street = '', city = 'X'
-- Hydrate: Doctrine создаёт Address через newInstance(),
--          затем setValue('street', '').
-- isValid() вернёт false, но объект УЖЕ существует.
-- Валидация — только при set(), не при hydrate.
```

**Решение:** валидировать в сервисе перед `persist()`. Или в `prePersist`/`preUpdate` listener'е.

**2. Embedded не может ссылаться на другую сущность.**

```php
// ❌ Не работает:
#[ORM\Embeddable]
class Money
{
    #[ORM\ManyToOne(targetEntity: Currency::class)]  // ← ошибка
    private Currency $currency;
}
```

Embedded — только скаляры. Если VO содержит ссылку на сущность — это уже не VO, а Aggregate Root или Composite.

**3. `cascade` и `cascade-persist` не работают для Embedded.**

Embedded — часть владельца. Если владелец `persist()` — и embedded с ним. Отдельно — нельзя.

**4. `unserialize` обходит конструктор.**

```php
$serialized = serialize($order);
$restored   = unserialize($serialized);
// $restored->address — создан БЕЗ вызова конструктора.
// readonly свойства — не установлены (unserialize не вызывает __construct).
// На PHP 8.2: Error: "Typed property Address::$street must not be accessed before initialization"
```

**5. `clone` + Embedded.**

```php
$order2 = clone $order;
// $order2->address === $order->address  ← тот же объект!
// Для VO это критично: изменишь $order2->address — изменишь и у $order.
// Нужно: $order2->address = clone $order->address;
```

---

## Способ 3: Custom Type — скалярный VO в одном столбце

Когда VO — «одна логическая величина», но хранится как `string` / `json`.

```php
// src/Doctrine/Types/JsonType.php (уже в Doctrine, можно использовать)
// Или свой:

use Doctrine\DBAL\Types\Types;

#[ORM\CustomType]
final class JsonType extends Type
{
    public function getName(): string
    {
        return 'json';
    }

    public function getSQLDeclaration(int $columnCount, Mapping\FieldMapping $mapping, Platform\AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL([]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) return null;
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
```

Применение:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'json')]
    private array $total;  // {"amount_cents": 5000, "currency": "EUR"}

    public function total(): Money
    {
        return Money::fromCents(
            $this->total['amount_cents'],
            $this->total['currency'],
        );
    }

    public function setTotal(Money $money): void
    {
        $this->total = [
            'amount_cents' => $money->amountCents,
            'currency'     => $money->currency,
        ];
    }
}
```

### Плюсы

- Один столбец. Индексируется (в PG — GIN).
- VO остаётся в домене. Entity — «плоская».
- Нет Embedded-проблем с readonly.

### Минусы

- **Нет типов на уровне БД.** `SELECT total FROM orders` → `{"amount_cents": -5, "currency": "XX"}`. Валидация — только в PHP.
- **Индексы и ограничения** — только через GIN + expression index, не через `CHECK (amount_cents >= 0)`.
- **Сравнение в SQL** — `WHERE total->>'currency' = 'EUR'` работает, но медленно без GIN.
- **Миграции** — сложно изменить структуру (добавить поле `exchange_rate`). Нужно `ALTER TYPE` или новая колонка.
- **Стат-анализер** — `array` вместо `Money`. Типизация теряется на границе.

### Когда подходит

- VO — «набор метаданных», а не «бизнес-правило». Например: `Address` в логах, `IPRange` в firewall-правил.
- Редко меняется.
- Не нужна сортировка/фильтрация по полям VO в SQL.

---

## Способ 4: Плоское хранилище (VO только в домене)

Самый строгий DDD-подход.

```php
// Домен: VO живёт сам по себе
final readonly class Money
{
    public function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) { /* валидация */ }
}

// Persistence: плоские поля, никакого VO
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalAmountCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    // Геттер — «мост» в домен
    public function total(): Money
    {
        return new Money($this->totalAmountCents, $this->totalCurrency);
    }

    public function setTotal(Money $money): void
    {
        $this->totalAmountCents = $money->amountCents;
        $this->totalCurrency    = $money->currency;
    }
}

// Доменная сущность (отдельная от Entity!)
final class Order
{
    public function __construct(
        public readonly int   $id,
        public readonly Money $total,
        public readonly \DateTimeImmutable $placedAt,
    ) {}
}
```

### Плюсы

- **Полная неизменяемость.** `Money` — `readonly class`, приватный конструктор.
- **Валидация — только в домене.** БД не знает про доменные правила.
- **Домен не зависит от Doctrine.** `Order` (домен) не содержит `#[ORM\Entity]`.
- **Тестируется без ORM.**

### Минусы

- **Два класса с одинаковым именем.** `App\Domain\Order\Order` и `App\Infrastructure\Persistence\Order`. Разное пространство имён, но путаница в стеках, логах, IDE.
- **Маппинг вручную.** `OrderMapper::toDomain($entity)`, `OrderMapper::toEntity($domain)`. Для каждого VO — свой маппер.
- **Нет Unit of Work.** `setTotal()` не помечает entity как modified. Нужно `$em->detach()` + `$em->merge()`, или явно `$em->persist()`.
- **Сложнее для команды.** «Зачем два класса?» — вопрос, который придётся объяснять каждому новому разработчику.

### Когда подходит

- Строгий DDD, bounded contexts, 5+ VO в домене.
- Команда знает DDD, готова к overhead'у.
- Домен сложный, VO имеют поведение (расчёт, сравнение, агрегация).

---

## Подводные камни (сводка)

### 1. `unserialize` обходит конструктор

```php
// PHP не вызывает __construct при unserialize.
// Если в конструкторе — валидация:
$bad = unserialize(serialize($good));  // валидация ПРОПУЩЕНА
```

**Решение:**
- Валидировать в `__wakeup()` (если используется) или в `__unserialize()`.
- Или: не сериализовать VO. Использовать `json_encode` / `json_decode` + фабрику.
- Или: доверять, что в БД нет мусора (слабый аргумент).

```php
public function __wakeup(): void
{
    if ($this->amountCents < 0) {
        throw new DomainException('Money corrupted: negative amount');
    }
}
```

### 2. Lazy loading + VO

```php
// Если VO содержит lazy-ссылку:
#[ORM\Embeddable]
class Money
{
    #[ORM\ManyToOne]  // ← НЕ РАБОТАЕТ в embedded
    private Currency $currency;
}
```

Embedded не поддерживает associations. Если VO — «простой» — ОК. Если «сложный» с сущностями — это Aggregate, а не VO.

### 3. Unit of Work и value equality

```php
$order->setTotal(Money::fromCents(5000, 'EUR'));
$order->setTotal(Money::fromCents(5000, 'EUR'));  // «новое» значение, то же самое

// Doctrine: $order->total !== $oldTotal (reference comparison)
// → помечает как modified → UPDATE
// Хотя значение не изменилось.
```

**Решение:**
- В setter'е сравнивать: `if ($this->total?->equals($money)) return;`
- Или: принести к тому, что Doctrine сравнивает по identity. VO — по value. Конфликт на уровне UoW.

### 4. `clone` и shared state

```php
$order2 = clone $order;
// $order2->total === $order->total  ← тот же объект (shallow copy)
// Для VO это критично: $order2->total->add(...) — нет, это immutable,
// но если VO содержит mutable коллекцию:
// $order2->items[] = ... → изменит и $order->items!
```

**Решение:** `__clone()` в VO:

```php
public function __clone()
{
    $this->items = new \SplObjectStorage();  // или array_copy
    foreach ($this->items as $item) {
        $this->items[] = clone $item;
    }
}
```

### 5. `readonly` + Doctrine Reflection

```php
// PHP 8.2:
final readonly class Money
{
    public function __construct(
        public readonly int $amountCents,
    ) {}
}

// Doctrine hydrate:
$prop = new \ReflectionProperty(Money::class, 'amountCents');
$prop->setAccessible(true);
$prop->setValue($instance, 100);
// → Error: Cannot modify readonly property Money::$amountCents
```

**Решение:**
- Не использовать `readonly` для Embedded/Entity.
- Или: не использовать Embedded. Плоские поля (Способ 4).
- Или: PHP 8.3 `__unserialize` + `ReflectionProperty::setValue` — не поможет, readonly на уровне языка.

### 6. Cascade + cycle detection

```php
#[ORM\Entity]
class Order
{
    #[ORM\Embedded]
    private Money $total;

    #[ORM\ManyToOne]
    private Customer $customer;
}

// Money содержит ссылку на Customer (через embedded — НЕЛЬЗЯ).
// Если VO — отдельная сущность:
// Order → Money → Customer
// Customer → Order (back-reference)
// → Цикл. Doctrine: "A cycle was detected..."
```

**Решение:** VO не содержит ссылок на сущности. Если содержит — это не VO.

---

## Таблица выбора

| Критерий | Приватный конст. + плоские поля | Embedded | Custom Type (json) | Плоское хранилище (два класса) |
|---|---|---|---|---|
| Неизменяемость | ✅ (readonly) | ⚠️ (нет readonly) | ✅ (в домене) | ✅ (полный) |
| Валидация | ✅ (фабрика) | ⚠️ (обход при hydrate) | ✅ (в домене) | ✅ (полный) |
| Тестируемость | ✅ | ⚠️ (нужна БД) | ✅ | ✅ |
| Индексы в БД | ✅ (отдельные столбцы) | ✅ (столбцы embedded) | ⚠️ (GIN) | ✅ |
| SQL-запросы | ✅ (WHERE total_currency = ?) | ✅ | ⚠️ (JSONB) | ✅ |
| Код | Средний | Средний | Минимум | Много |
| DDD-чистота | Высокая | Средняя | Низкая | Максимальная |
| readonly class | ✅ | ❌ | ✅ | ✅ |

---

## Что я бы выбрал

### Для 80% проектов (1–3 VO в домене)

```php
// Приватный конструктор + flat columns
final class Money
{
    private function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {}

    public static function fromCents(int $c, string $cur): self { /* validate */ }
    public function add(self $o): self { /* ... */ }
    public function equals(self $o): bool { /* ... */ }
}

#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    public function total(): Money
    {
        return Money::fromCents($this->totalCents, $this->totalCurrency);
    }

    public function setTotal(Money $m): void
    {
        $this->totalCents    = $m->amountCents;
        $this->totalCurrency = $m->currency;
    }
}
```

Почему:
- `Money` — чистый, `readonly`, тестируется без БД.
- `Order` — плоская, индексируется, SQL-запросы работают.
- Нет Embedded-проблем с readonly.
- Один класс на концепцию. Не два.
- `fromCents()` — единственная точка валидации.

### Для сложного домена (5+ VO, поведенческие)

```php
// Плоское хранилище: Domain + Persistence
App\Domain\Order\Money        // VO, readonly, фабрика
App\Domain\Order\Order       // Aggregate Root
App\Infrastructure\Persistence\Order  // Entity
App\Infrastructure\Persistence\OrderMapper  // маппинг
```

Почему:
- Домен полностью отделён от ORM.
- VO может иметь сложное поведение (расчёт, агрегация, сравнение).
- Unit of Work не нужен. Явный `persist()` / `flush()`.
- Bounded context: каждый контекст — свой домен, своя БД.

### НЕ выбрал бы

- **Embedded для сложных VO** — readonly не работает, associations не работают, hydrate обходит валидацию.
- **Custom Type (json) для бизнес-критичных VO** — нет индексов, нет CHECK-ограничений, стат-анализер теряет тип.
- **Публичный конструктор + публичные свойства** — «ну, мы же доверяем разработчикам». Через месяц кто-то напишет `$money->amount = -1` и не вызовет валидацию.

> ⏱ 50.1s · prompt 41581 tok · output 6223 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про хранение агрегата в Doctrine: маппинг коллекций, границы транзакций, кто и когда вызывает flush. Нужно разобрать на конкретном примере.

Давайте выберу хороший пример — агрегат "Invoice" (счёт):
- Invoice (Aggregate Root)
- InvoiceLineItem (дочерняя сущность, только через Invoice)
- Money (VO)

Это хороший пример, потому что:
1. Есть чёткая граница агрегата (LineItem не существует без Invoice)
2. Есть коллекция (0..N line items)
3. Есть бизнес-правила на уровне агрегата (сумма, валидация)
4. Есть вопросы транзакции (создание Invoice + LineItems атомарно)
5. Есть вопросы flush (кто вызывает — сервис, репозиторий, контроллер?)

Структура ответа:
1. Пример домена (Aggregate Root + Child Entity + VO)
2. Маппинг в Doctrine (OneToOne/OneToMany, cascade, orphanRemoval, lifecycle)
3. Репозиторий (граница)
4. Кто вызывает flush (и почему)
5. Границы транзакции
6. Подводные камни (N+1, lazy loading, Unit of Work, detached)
7. Антипаттерны

Язык — русский, как и весь разговор.

</details>

**Кандидат:**

# Агрегат в Doctrine: Invoice → LineItems

## Домен: что у нас есть

```php
// src/Domain/Invoice/Money.php — Value Object
final readonly class Money
{
    private function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {}

    public static function fromCents(int $c, string $cur): self
    {
        if ($c < 0) throw new DomainException('Negative amount');
        if ($cur === '') throw new DomainException('Empty currency');
        return new self($c, $cur);
    }

    public function add(self $o): self
    {
        if ($this->currency !== $o->currency) {
            throw new DomainException('Currency mismatch');
        }
        return self::fromCents($this->amountCents + $o->amountCents, $this->currency);
    }

    public function equals(self $o): bool
    {
        return $this->amountCents === $o->amountCents
            && $this->currency === $o->currency;
    }
}

// src/Domain/Invoice/InvoiceLineItem.php — дочерняя сущность
final class InvoiceLineItem
{
    private function __construct(
        public readonly int    $id,
        public readonly string $description,
        public readonly Money  $amount,
        public readonly int    $quantity,
    ) {}

    public static function create(string $description, Money $amount, int $quantity): self
    {
        if ($description === '') throw new DomainException('Empty description');
        if ($quantity < 1) throw new DomainException('Quantity must be ≥ 1');
        return new self(0, $description, $amount, $quantity);  // id = 0 до persist
    }

    public function total(): Money
    {
        return Money::fromCents(
            $this->amount->amountCents * $this->quantity,
            $this->amount->currency,
        );
    }
}

// src/Domain/Invoice/Invoice.php — Aggregate Root
final class Invoice
{
    /** @var list<InvoiceLineItem> */
    private array $lineItems = [];

    public function __construct(
        public readonly int            $id,
        public readonly string         $number,
        public readonly string         $customerEmail,
        public readonly \DateTimeImmutable $issuedAt,
        private readonly Money         $currency,  // «базовая» валюта агрегата
    ) {}

    public static function new(string $number, string $customerEmail): self
    {
        if ($number === '') throw new DomainException('Empty number');
        if ($customerEmail === '') throw new DomainException('Empty email');

        return new self(
            id: 0,
            number: $number,
            customerEmail: $customerEmail,
            issuedAt: new \DateTimeImmutable(),
            currency: Money::fromCents(0, 'EUR'),  // временная, будет определена первым item
        );
    }

    public function addLineItem(InvoiceLineItem $item): void
    {
        // Правило 1: валюта item должна совпадать с валютой агрегата
        if ($item->amount->currency !== $this->currency->currency) {
            throw new DomainException(
                "Currency mismatch: invoice is {$this->currency->currency}, item is {$item->amount->currency}"
            );
        }

        // Правило 2: не более 100 позиций
        if (count($this->lineItems) >= 100) {
            throw new DomainException('Max 100 line items');
        }

        // Правило 3: нет дубликатов по описанию
        foreach ($this->lineItems as $existing) {
            if ($existing->description === $item->description) {
                throw new DomainException("Duplicate: {$item->description}");
            }
        }

        $this->lineItems[] = $item;
    }

    public function removeLineItem(int $itemId): void
    {
        foreach ($this->lineItems as $i => $existing) {
            if ($existing->id === $itemId) {
                array_splice($this->lineItems, $i, 1);
                return;
            }
        }
        throw new DomainException("LineItem {$itemId} not found in this invoice");
    }

    /** @return list<InvoiceLineItem> */
    public function lineItems(): array
    {
        return $this->lineItems;
    }

    public function total(): Money
    {
        $total = Money::fromCents(0, $this->currency->currency);
        foreach ($this->lineItems as $item) {
            $total = $total->add($item->total());
        }
        return $total;
    }

    public function isEmpty(): bool
    {
        return $this->lineItems === [];
    }

    public function assertNotEmpty(): void
    {
        if ($this->isEmpty()) {
            throw new DomainException('Cannot finalize an empty invoice', code: 'INVOICE_EMPTY');
        }
    }
}
```

Ключевые свойства:
- `LineItem` **не существует без Invoice**. Нет `LineItemRepository`. Нет `lineItem->invoice` back-reference в домене.
- Все бизнес-правила — на уровне `Invoice`. `addLineItem()` проверяет валюту, лимит, дубликаты.
- `Money` — `readonly class`, приватный конструктор.

---

## Маппинг в Doctrine

### Решение: OneToOne + `owning side` на LineItem

```php
// src/Persistence/Invoice.php — Entity (не Domain\Invoice\Invoice!)
#[ORM\Entity]
#[ORM\Table(name: 'invoice')]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    private string $number;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $customerEmail;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $issuedAt;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    /**
     * @ORM\OneToMany(
     *     targetEntity: LineItem::class,
     *     mappedBy: "invoice",
     *     cascade: {"persist", "remove"},
     *     orphanRemoval: true,
     *     fetch: EAGER,
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @var Collection<int, LineItem>
     */
    private Collection $lineItems;

    public function __construct()
    {
        $this->lineItems = new ArrayCollection();
    }

    // ── Геттеры/сеттеры для Doctrine ──────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getNumber(): string { return $this->number; }
    public function setNumber(string $v): void { $this->number = $v; }

    public function getCustomerEmail(): string { return $this->customerEmail; }
    public function setCustomerEmail(string $v): void { $this->customerEmail = $v; }

    public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
    public function setIssuedAt(\DateTimeImmutable $v): void { $this->issuedAt = $v; }

    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $v): void { $this->currency = $v; }

    public function getLineItems(): Collection { return $this->lineItems; }

    public function addLineItem(LineItem $item): void
    {
        if (!$this->lineItems->contains($item)) {
            $this->lineItems->add($item);
            $item->setInvoice($this);
        }
    }

    public function removeLineItem(LineItem $item): void
    {
        if ($this->lineItems->removeElement($item)) {
            $item->setInvoice(null);
        }
    }
}
```

```php
// src/Persistence/LineItem.php — Entity (дочерняя)
#[ORM\Entity]
#[ORM\Table(name: 'invoice_line_item')]
class LineItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // OWNING SIDE: FK лежит здесь
    #[ORM\ManyToOne(inversedBy: 'lineItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Invoice $invoice = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $description;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amountCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    #[ORM\Column(type: Types::INTEGER)]
    private int $position = 0;

    // ── Геттеры/сеттеры ───────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getInvoice(): ?Invoice { return $this->invoice; }
    public function setInvoice(?Invoice $inv): void { $this->invoice = $inv; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $v): void { $this->description = $v; }

    public function getAmountCents(): int { return $this->amountCents; }
    public function setAmountCents(int $v): void { $this->amountCents = $v; }

    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $v): void { $this->currency = $v; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $v): void { $this->quantity = $v; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): void { $this->position = $v; }
}
```

### Схема БД

```
invoice                          invoice_line_item
─────────                        ─────────────────
id (PK)                          id (PK)
number (UNIQUE)                  invoice_id (FK → invoice.id, CASCADE)
customer_email                   description
issued_at                        amount_cents
currency                         currency
                                 quantity
                                 position
```

### Ключевые решения маппинга

| Решение | Почему |
|---|---|
| `cascade: {"persist", "remove"}` | `persist($invoice)` → автоматически `persist` каждого LineItem. Не нужно перебирать коллекцию. |
| `orphanRemoval: true` | Удалили LineItem из коллекции → `DELETE FROM invoice_line_item WHERE id = ?`. Без orphanRemoval — запись осталась бы в БД. |
| `fetch: EAGER` | Агрегат всегда используется целиком. Lazy-загрузка LineItems → N+1 при итерации. EAGER — один JOIN. |
| `onDelete: 'CASCADE'` | На уровне БД. Если кто-то удалит Invoice через SQL — LineItems тоже удалятся. Страховка от «забыл orphanRemoval». |
| `position` | Порядок в массиве домена → порядковое поле в БД. Без него `ORDER BY` — по `id`, что не всегда совпадает с логическим порядком. |
| `currency` в LineItem | Дублирование (тоже в Invoice). Да, денормализация. Но: LineItem может быть «вынут» из Invoice (export, report) и должен быть самодостаточным. Альтернатива — только в Invoice, но тогда LineItem без Invoice «не знает» свою валюту. |
| **Нет** back-reference в домене | `Domain\Invoice\InvoiceLineItem` не содержит `invoice`. Граница агрегата — только через `Invoice`. |

---

## Репозиторий: граница между доменом и persistence

```php
// src/Domain/Invoice/InvoiceRepository.php — интерфейс (в домене)
interface InvoiceRepository
{
    public function save(Invoice $invoice): void;
    public function findById(int $id): Invoice;
    public function findByNumber(string $number): Invoice;
    public function delete(Invoice $invoice): void;
}
```

```php
// src/Persistence/InvoiceRepository.php — реализация (в infrastructure)
final class InvoiceRepository implements InvoiceRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Invoice $invoice): void
    {
        $entity = $this->toEntity($invoice);
        $this->em->persist($entity);
        // НЕ flush() здесь! (подробнее ниже)
    }

    public function findById(int $id): Invoice
    {
        $entity = $this->em->getRepository(InvoiceEntity::class)->find($id);
        if ($entity === null) {
            throw new InvoiceNotFoundException($id);
        }
        return $this->toDomain($entity);
    }

    public function findByNumber(string $number): Invoice
    {
        $entity = $this->em->getRepository(InvoiceEntity::class)
            ->findOneBy(['number' => $number]);
        if ($entity === null) {
            throw new InvoiceNotFoundException(number: $number);
        }
        return $this->toDomain($entity);
    }

    public function delete(Invoice $invoice): void
    {
        $entity = $this->em->getRepository(InvoiceEntity::class)
            ->findOneBy(['id' => $invoice->id]);
        if ($entity === null) return;

        $this->em->remove($entity);
        // НЕ flush() здесь!
    }

    // ── Маппинг Domain ↔ Entity ──────────────────────────────────

    private function toEntity(Invoice $invoice): InvoiceEntity
    {
        $entity = new InvoiceEntity();
        $entity->setNumber($invoice->number);
        $entity->setCustomerEmail($invoice->customerEmail);
        $entity->setIssuedAt($invoice->issuedAt);
        $entity->setCurrency($invoice->currency);

        foreach (array_values($invoice->lineItems()) as $pos => $item) {
            $lineEntity = new LineItemEntity();
            $lineEntity->setDescription($item->description);
            $lineEntity->setAmountCents($item->amount->amountCents);
            $lineEntity->setCurrency($item->amount->currency);
            $lineEntity->setQuantity($item->quantity);
            $lineEntity->setPosition($pos);
            $entity->addLineItem($lineEntity);
        }

        return $entity;
    }

    private function toDomain(InvoiceEntity $entity): Invoice
    {
        $invoice = new Invoice(
            id: $entity->getId(),
            number: $entity->getNumber(),
            customerEmail: $entity->getCustomerEmail(),
            issuedAt: $entity->getIssuedAt(),
            currency: Money::fromCents(0, $entity->getCurrency()),
        );

        foreach ($entity->getLineItems() as $lineEntity) {
            $invoice->addLineItem(
                InvoiceLineItem::create(
                    description: $lineEntity->getDescription(),
                    amount: Money::fromCents(
                        $lineEntity->getAmountCents(),
                        $lineEntity->getCurrency(),
                    ),
                    quantity: $lineEntity->getQuantity(),
                )
            );
        }

        return $invoice;
    }
}
```

### Почему репозиторий НЕ вызывает `flush()`

Это **самый частый вопрос** и **самый частый баг**.

```php
// ❌ АНТИПАТТЕРН
public function save(Invoice $invoice): void
{
    $entity = $this->toEntity($invoice);
    $this->em->persist($entity);
    $this->em->flush();  // ← flush внутри репозитория
}

// Почему плохо:
// 1. Сервис может сохранить Invoice И Customer.
//    Если flush() в save() — Customer не сохранён (flush до persist).
//    Если Customer.save() после Invoice.save() — два flush'а, две транзакции.
// 2. Репозиторий «знает» о транзакциях. Это не его дело.
// 3. Если сервис сохраняет 5 инвойсов — 5 flush'ов. Медленнее.
// 4. Тестирование: мок EntityManager::flush() — лишний.
```

```php
// ✅ Правильно: репозиторий persist/remove. Flush — на уровне сервиса.
public function save(Invoice $invoice): void
{
    $entity = $this->toEntity($invoice);
    $this->em->persist($entity);
    // flush() — НЕ здесь.
}
```

---

## Сервис: кто вызывает `flush()` и где граница транзакции

```php
// src/Service/InvoiceService.php
final class InvoiceService
{
    public function __construct(
        private InvoiceRepository $invoices,
        private EntityManagerInterface $em,
        private EventDispatcher $events,
    ) {}

    public function createWithLineItems(
        string $number,
        string $customerEmail,
        array  $items,  // [['description' => ..., 'amount' => ..., 'qty' => ...], ...]
    ): Invoice {

        $invoice = Invoice::new($number, $customerEmail);

        foreach ($items as $itemData) {
            $item = InvoiceLineItem::create(
                description: $itemData['description'],
                amount:      Money::fromCents($itemData['amount'], $itemData['currency']),
                quantity:    (int) $itemData['qty'],
            );
            $invoice->addLineItem($item);  // ← доменные правила: валюта, лимит, дубликаты
        }

        $invoice->assertNotEmpty();

        // ── ГРАНИЦА ТРАНЗАКЦИИ ──────────────────────────────────
        $this->em->beginTransaction();
        try {
            $this->invoices->save($invoice);   // persist, без flush
            $this->em->flush();                // ← ОДИН flush на всю операцию
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollBack();
            throw $e;
        }
        // ── КОНЕЦ ГРАНИЦЫ ───────────────────────────────────────

        // Post-commit: асинхронные действия
        $this->events->dispatch(new InvoiceCreatedEvent($invoice));

        return $invoice;
    }

    public function addLineItemToInvoice(int $invoiceId, string $description, int $amountCents, string $currency, int $qty): Invoice
    {
        $invoice = $this->invoices->findById($invoiceId);

        $item = InvoiceLineItem::create($description, Money::fromCents($amountCents, $currency), $qty);
        $invoice->addLineItem($item);  // ← доменные правила

        $this->em->beginTransaction();
        try {
            $this->invoices->save($invoice);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollBack();
            throw $e;
        }

        return $invoice;
    }

    public function removeLineItemFromInvoice(int $invoiceId, int $lineItemId): Invoice
    {
        $invoice = $this->invoices->findById($invoiceId);
        $invoice->removeLineItem($lineItemId);

        $this->em->beginTransaction();
        try {
            $this->invoices->save($invoice);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollBack();
            throw $e;
        }

        return $invoice;
    }
}
```

### Кто вызывает что — сводка

| Кто | Что | Почему |
|---|---|---|
| **Репозиторий** | `persist()`, `remove()` | Говорит EntityManager'у «запомни этот объект». Не записывает в БД. |
| **Сервис** | `beginTransaction()`, `flush()`, `commit()`, `rollBack()` | Граница транзакции. Сервис знает, что входит в «одну операцию». |
| **Контроллер** | Вызывает сервис. Не знает про БД. | Отделение представления от логики. |
| **Репозиторий (read)** | `find()` → `findOneBy()` | SELECT. Не требует транзакции. |

### Почему `flush()` в сервисе, а не в контроллере

```php
// ❌ Контроллер
public function create(InvoiceCreateRequest $req): JsonResponse
{
    $this->invoiceService->createWithLineItems(...);
    $this->em->flush();  // ← контроллер знает про EntityManager
    return new JsonResponse(201);
}

// Почему плохо:
// - Контроллер зависит от EntityManager.
// - Если сервис вызывает flush() внутри — контроллер делает второй.
// - Если сервис не вызывает flush() — контроллер «забывает».
// - TDD: нужно мокнуть EntityManager в контроллере. Бред.
```

### Почему `flush()` в сервисе, а не в репозитории

```php
// ❌ Репозиторий
public function save(Invoice $inv): void
{
    $this->em->persist($this->toEntity($inv));
    $this->em->flush();  // ← репозиторий решает о транзакции
}

// Почему плохо:
// - Сервис сохраняет Invoice + Customer.
//   save($invoice) → flush() → Invoice в БД.
//   save($customer) → flush() → Customer в БД.
//   Две транзакции. Если вторая упадёт — первая уже закоммичена.
//
// ✅ Сервис
$this->em->beginTransaction();
$this->invoices->save($invoice);
$this->customers->save($customer);
$this->em->flush();      // ← ОДИН flush, обе сущности
$this->em->commit();
```

---

## Что происходит при `flush()` с агрегатом

```
$invoice (InvoiceEntity)
├── lineItems: [LineItemEntity#1, LineItemEntity#2, LineItemEntity#3]
│
├── persist() → UoW: INSERT invoice (id=NULL)
├── persist() → UoW: INSERT line_item (invoice_id=NULL)  ← FK ещё нет!
│
flush():
│
├── 1. INSERT INTO invoice (number, email, issued_at, currency)
│      VALUES ('INV-001', 'a@b.c', '2024-01-15', 'EUR')
│      → LAST_INSERT_ID = 42
│
├── 2. UPDATE line_item SET invoice_id = 42 WHERE id IN (101, 102, 103)
│      (или INSERT ... VALUES (42, 'desc1', 1000, 'EUR', 1, 0), ...)
│
└── 3. commit() → все 4 записи атомарны
```

Doctrine решает порядок: сначала `invoice` (owning side), затем `line_item` (inversed side). Если `line_item` был бы owning — наоборот. **Owning side всегда записывается первым.**

При удалении:

```
$invoice->removeLineItem($item)
→ orphanRemoval: true
→ UoW: DELETE FROM invoice_line_item WHERE id = ?
→ flush() → DELETE выполнено
```

---

## Транзакции: границы и правила

### Правило 1: Одна транзакция = одна «единица работы»

```php
// ✅ Одна единица: «создать счёт с позициями»
beginTransaction();
persist(invoice);
flush();
commit();

// ✅ Одна единица: «оплатить счёт + списать с баланса»
beginTransaction();
persist(invoice);
persist(balance);
flush();
commit();

// ❌ Две единицы: «создать счёт» и «отправить email»
beginTransaction();
persist(invoice);
flush();
commit();
// email — отдельная транзакция (или вообще не транзакция)
```

### Правило 2: Транзакция не должна содержать «дорогие» внешние вызовы

```php
// ❌
beginTransaction();
persist(invoice);
$this->paymentGateway->charge($amount);  // ← HTTP-вызов, 200–2000 мс
flush();
commit();
// Если charge() упадёт — транзакция открыта 2+ секунды.
// Connection pool истощается. Deadlock'и.

// ✅
$result = $this->paymentGateway->charge($amount);  // вне транзакции
if ($result->isDeclined()) {
    throw new DomainException('Payment declined');
}

beginTransaction();
persist(invoice);
persist(paymentRecord);
flush();
commit();
// Транзакция открыта 10–50 мс.
```

### Правило 3: Post-commit действия — вне транзакции

```php
beginTransaction();
persist(invoice);
flush();
commit();          // ← БД уже записана

// Ниже — вне транзакции. Если email упадёт — invoice уже в БД.
$this->events->dispatch(new InvoiceCreatedEvent($invoice));
// → EmailNotifier (async)
// → SearchIndexer (async)
// → AnalyticsReporter (async)
```

### Правило 4: `SELECT` не требует транзакции

```php
// Чтение — без beginTransaction()
$invoice = $this->invoices->findById(42);
// SELECT — read-only, autocommit. Не нужно commit'ить.
```

Исключение: `SELECT ... FOR UPDATE` (pessimistic lock) — требует транзакции, иначе lock снимется сразу после SELECT.

---

## Подводные камни

### 1. N+1 при `fetch: LAZY`

```php
// Если fetch: LAZY (default):
$invoices = $repo->findAll();
foreach ($invoices as $inv) {
    foreach ($inv->getLineItems() as $item) {  // ← SELECT на каждый!
        // ...
    }
}
// N+1: 1 SELECT invoices + N SELECT line_items
```

**Решение:**
- `fetch: EAGER` (для агрегатов — нормально, коллекция всегда нужна).
- Или: `DQL: SELECT i, li FROM Invoice i JOIN i.lineItems li`.
- Или: `Doctrine\Common\Collections\LazyCollection` + `->initialize()`.

Для агрегата `EAGER` — оправданный выбор. Агрегат — «атом»: читаем целиком или не читаем.

### 2. `orphanRemoval` + `cascade: remove` — двойное удаление

```php
#[ORM\OneToMany(
    cascade: {"persist", "remove"},
    orphanRemoval: true,
)]
```

`orphanRemoval: true` **уже** включает `remove`. Дублирование в `cascade` — не ошибка, но шум.

```php
// ✅ Достаточно:
#[ORM\OneToMany(
    cascade: {"persist"},
    orphanRemoval: true,  // ← сам по себе включает remove
)]
```

### 3. `flush()` после `remove()` — не сразу

```php
$invoice->removeLineItem($item);
$repo->save($invoice);

// Если тут НЕТ flush() — DELETE не выполнен.
// Если тут flush() — DELETE выполнен.
// Но если между ними — exception — rollback.
// Поэтому: flush в сервисе, не в репозитории.
```

### 4. Detached сущность + flush

```php
$invoice = $repo->findById(42);  // managed
$em->clear();                    // detached!
$invoice->addLineItem($item);
$repo->save($invoice);           // persist(detached) → Error!
```

`persist()` на detached → `Exception: A new entity was found through the relationship`.

**Решение:**
- `$em->merge($invoice)` — «возвращает» в managed state.
- Или: не `clear()` между операциями.
- Или: один request = один `EntityManager` (Symfony по умолчанию).

### 5. `position` не обновляется

```php
$invoice->addLineItem($item1);  // position = 0
$invoice->addLineItem($item2);  // position = 1
$invoice->addLineItem($item3);  // position = 2

$invoice->removeLineItem($item2);
// lineItems: [item1, item3]
// position в БД: 0, 2  ← item2 (position=1) удалён
// ORDER BY position → item1, item3. OK.

// Но если:
$invoice->removeLineItem($item1);
// lineItems: [item3]
// position в БД: 2
// ORDER BY position → item3. OK.
// Но «логический» порядок: item3 — первая позиция.
// position = 2 — косметически некрасиво, но работает.
```

**Решение:** при `removeLineItem()` — перенумеровать:

```php
public function removeLineItem(int $itemId): void
{
    foreach ($this->lineItems as $i => $existing) {
        if ($existing->id === $itemId) {
            array_splice($this->lineItems, $i, 1);
            // Перенумеровать:
            foreach ($this->lineItems as $pos => $item) {
                $item->setPosition($pos);
            }
            return;
        }
    }
    throw new DomainException("LineItem {$itemId} not found");
}
```

### 6. `unserialize` обходит конструктор `Money`

```php
$serialized = serialize($invoice);
$restored   = unserialize($serialized);
// Money::amountCents — установлен через Reflection, НЕ через fromCents().
// Валидация пропущена.
// Если в БД был мусор (amountCents = -5) — Money «создан» с -5.
```

**Решение:**
- Валидировать в `__wakeup()` Money.
- Или: не сериализовать. Использовать JSON + фабрику.
- Или: доверять БД (слабо).

---

## Антипаттерны

### 1. LineItem с собственным репозиторием

```php
// ❌
interface LineItemRepository
{
    public function findById(int $id): LineItem;
    public function save(LineItem $item): void;
}

// Почему: LineItem не существует без Invoice.
// «Найти LineItem по id» → нужно сначала найти Invoice,
// в который он входит. Это не «LineItem-запрос»,
// это «Invoice-запрос с фильтром».
```

### 2. `em->flush()` в контроллере

```php
// ❌
public function create(Request $req): Response
{
    $this->service->create(...);
    $this->em->flush();  // ← контроллер знает про БД
    return new Response(201);
}
```

### 3. `em->flush()` в репозитории

```php
// ❌
public function save(Invoice $inv): void
{
    $this->em->persist($this->toEntity($inv));
    $this->em->flush();  // ← репозиторий решает о транзакции
}
```

### 4. `new Invoice()` в домене

```php
// ❌
$invoice = new Invoice();
$invoice->setNumber('INV-001');
$invoice->setCustomerEmail('a@b.c');
// → Invoice в невалидном состоянии (нет email, нет number)
// → Если кто-то вызовет invoice->total() → Error

// ✅
$invoice = Invoice::new('INV-001', 'a@b.c');
// → Валидный с рождения
```

### 5. `cascade: {"persist"}` без `orphanRemoval`

```php
// ❌
#[ORM\OneToMany(cascade: {"persist"})]  // без orphanRemoval

// Удалили LineItem из коллекции → записью в БД осталась.
// «Сирота» в invoice_line_item.
```

---

## Итого: кто делает что

```
┌─────────────────────────────────────────────────────────────────┐
│  Controller                                                     │
│  → валидирует вход, вызывает сервис, формирует ответ            │
│  → НЕ знает про EntityManager, Repository, Transaction          │
├─────────────────────────────────────────────────────────────────┤
│  Service                                                        │
│  → бизнес-логика: create, addLineItem, removeLineItem          │
│  → beginTransaction / commit / rollBack                         │
│  → flush() — ОДИН на операцию                                   │
│  → post-commit: event dispatch                                  │
│  → НЕ знает про Entity, ORM, SQL                                │
├─────────────────────────────────────────────────────────────────┤
│  Domain (Invoice, InvoiceLineItem, Money)                       │
│  → бизнес-правила: addLineItem, removeLineItem, total          │
│  → валидация: fromCents, assertNotEmpty                         │
│  → НЕ знает про Doctrine, EntityManager, Repository             │
│  → НЕ содержит #[ORM\Entity]                                    │
├─────────────────────────────────────────────────────────────────┤
│  Persistence (InvoiceEntity, LineItemEntity)                    │
│  → маппинг: #[ORM\Entity], #[ORM\Column]                        │
│  → cascade, orphanRemoval, fetch                                │
│  → геттеры/сеттеры для Doctrine                                  │
│  → НЕ содержит бизнес-логики                                    │
├─────────────────────────────────────────────────────────────────┤
│  Repository (InvoiceRepository)                                 │
│  → persist() / remove()                                         │
│  → find() → findOneBy()                                         │
│  → toDomain() / toEntity()                                      │
│  → НЕ вызывает flush()                                          │
│  → НЕ вызывает beginTransaction()                               │
└─────────────────────────────────────────────────────────────────┘
```

**Один `flush()` на операцию. Одна транзакция на операцию. Репозиторий — «руки». Сервис — «мозг». Домен — «правила». Контроллер — «лицо».**

> ⏱ 60.4s · prompt 47262 tok · output 8473 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о диагностике и решении N+1 запросов в Doctrine. Это практический вопрос, и мне нужно дать конкретные, применимые ответы.

Структура ответа:
1. Диагностика — как именно найти N+1 (профилировщики, логирование, инструменты)
2. Решения — каждое с trade-offs

Диагностика:
- Doctrine Logger / QueryLogger
- Symfony Profiler
- pg_stat_statements
- Логи БД (log_min_duration_statement)
- Xdebug / Blackfire
- Визуализация запросов

Решения:
1. EAGER fetching (JOIN, JOIN FETCH)
2. DQL с JOIN FETCH
3. Doctrine Collections + initialize()
4. Batch loading
5. Denormalization
6. Материализованные представления
7. GraphQL / batching
8. Ленивая инициализация (lazy collections) — антипаттерн для N+1, но нужно упомянуть
9. Caching (second-level cache)
10. Переписывание на raw SQL с JOIN

Для каждого: как работает, когда подходит, минусы.

Примеры кода нужны — конкретные, на PHP/Symfony/Doctrine.

Структурирую ответ:
- Диагностика (инструменты + конкретные шаги)
- Пример N+1 (чтобы было на что ссылаться)
- Решения с trade-offs (таблица + развёрнутый разбор каждого)
- Итоговая эвристика выбора

Нужно быть конкретным, не абстрактным. Показывать код. Указывать на подводные камни.

</details>

**Кандидат:**

# N+1 в Doctrine: диагностика и лечение

## Пример, на который будем ссылаться

```php
// src/Controller/OrderController.php
public function index(): Response
{
    $orders = $this->em
        ->getRepository(OrderEntity::class)
        ->findBy([], ['createdAt' => 'DESC'], 50);

    return $this->render('order/index.html.twig', ['orders' => $orders]);
}
```

```twig
{# order/index.html.twig #}
{% for order in orders %}
    <tr>
        <td>{{ order.number }}</td>
        <td>{{ order.customer.name }}</td>   {# ← N+1: SELECT customer WHERE id = ? (на каждый order) #}
        <td>
            {% for item in order.lineItems %} {# ← N+1: SELECT line_item WHERE invoice_id = ? (на каждый order) #}
                <div>{{ item.description }}</div>
            {% endfor %}
        </td>
    </tr>
{% endfor %}
```

**Результат:** 1 запрос (orders) + 50 (customers) + 50 (lineItems) = **101 запрос**. При 50 заказах. При 500 — 1001.

---

## Диагностика

### 1. Doctrine QueryLogger — базовый инструмент

```php
// src/Doctrine/Debug/QueryLogger.php
use Doctrine\DBAL\Logging\SQLLogger;

final class QueryLogger implements SQLLogger
{
    private array $queries = [];
    private float $totalTime = 0;

    public function startQuery(string $sql, ?array $params, ?array $types): void
    {
        $this->queries[] = [
            'sql'    => $sql,
            'params' => $params,
            'start'  => microtime(true),
        ];
    }

    public function stopQuery(): void
    {
        end($this->queries);
        $last = key($this->queries);
        $duration = microtime(true) - $this->queries[$last]['start'];
        $this->queries[$last]['duration'] = $duration;
        $this->totalTime += $duration;
    }

    public function report(): string
    {
        $lines = [];
        $count = 0;

        foreach ($this->queries as $q) {
            $count++;
            $lines[] = sprintf(
                "[%2d] %7.2f ms  %s",
                $count,
                $q['duration'] * 1000,
                $q['sql'],
            );
        }

        $summary = sprintf(
            "Total: %d queries, %.2f ms\n",
            $count,
            $this->totalTime * 1000,
        );

        return $summary . implode("\n", $lines);
    }
}
```

```php
// src/Kernel.php или boot script (только в dev)
$queryLogger = new QueryLogger();
$em->getConnection()->setSQLLogger($queryLogger);

// ... после выполнения запросов:
echo $queryLogger->report();
```

**Вывод:**

```
Total: 101 queries, 483.20 ms
[ 1]    2.34 ms  SELECT o.id, o.number, ... FROM invoice o ORDER BY o.created_at DESC LIMIT 50
[ 2]    0.81 ms  SELECT c.* FROM customer c WHERE c.id = 42
[ 3]    0.79 ms  SELECT c.* FROM customer c WHERE c.id = 43
[ 4]    0.82 ms  SELECT c.* FROM customer c WHERE c.id = 44
...
[52]    0.80 ms  SELECT li.* FROM invoice_line_item li WHERE li.invoice_id = 42
[53]    0.78 ms  SELECT li.* FROM invoice_line_item li WHERE li.invoice_id = 43
...
```

**Как читать:**
- 101 запрос вместо ожидаемых 1–3.
- Запросы 2–51 — одинаковый SQL с разным `id`. Это **подпись N+1**.
- Суммарное время: 50 × 0.8 мс = 40 мс на customers. Не страшно. Но при 5000 заказов — 4000 мс.

### 2. Symfony Profiler

`php bin/console debug:router` — нет. Нужно:

```yaml
# config/packages/dev/web_profiler.yaml
web_profiler:
    toolbar: true
```

Открыть страницу → вкладка **Database** → список всех SQL-запросов. Фильтр: показать только уникальные SQL. Если видите 50 одинаковых `SELECT c.* FROM customer WHERE c.id = ?` — N+1.

### 3. `pg_stat_statements` (PostgreSQL)

```sql
-- Разово включить:
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
-- pg_hba.conf: shared_preload_libraries = 'pg_stat_statements'

-- Запросы с наибольшим суммарным временем:
SELECT
    calls,
    total_exec_time,
    mean_exec_time,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Что ищем:** `calls` в сотни/тысячи, `mean_exec_time` < 1 мс, но `total_exec_time` большой. Это N+1.

### 4. Лог БД

```ini
# postgresql.conf
log_min_duration_statement = 1   # логировать все запросы ≥ 1 мс
log_statement = all
```

```bash
tail -f /var/log/postgresql/postgresql.log
```

Видите 101 запрос за один request — N+1.

### 5. Xdebug / Blackfire

Визуализация stack trace: кто вызывает `SELECT`. Полезно, когда N+1 спрятан глубоко в фреймворке или библиотеке.

### 6. `doctrine:query:explain` (нет, не существует)

Ближайшее — DQL в консоли + `EXPLAIN ANALYZE`:

```bash
php bin/console doctrine:query:schema --show-dql
```

Полезно для понимания, **какой** SQL генерируется, но не для диагностики N+1.

### Чек-лист диагностики

```
1. Сколько всего запросов за request?
   → > 10 при 1 сущности — подозрительно.

2. Есть ли повторяющийся SQL с разным параметром?
   → Да — N+1.

3. Где вызывается?
   → Stack trace через QueryLogger / Profiler.

4. Это в контроллере, в шаблоне, в service?
   → Определяет, какое решение применять.

5. Сколько записей в списке?
   → 50 — терпимо. 5000 — критично.

6. Это каждый request или только при кэше?
   → При кэше — N+1 скрыт. При cache miss — взрывается.
```

---

## Решения

### 1. `JOIN` в DQL — один запрос вместо N+1

**Было (N+1):**

```php
$orders = $repo->findBy([], ['createdAt' => 'DESC'], 50);
// foreach: $order->getCustomer() → SELECT customer WHERE id = ?
```

**Стало (JOIN):**

```php
$orders = $this->em
    ->createQuery(
        'SELECT o, c
         FROM App\Persistence\OrderEntity o
         JOIN o.customer c
         ORDER BY o.createdAt DESC'
    )
    ->setMaxResults(50)
    ->getResult();

// foreach: $order->getCustomer() → уже в памяти, SELECT нет
```

**Что произошло:**

```sql
-- Было (101 запрос):
SELECT o.* FROM invoice o LIMIT 50;
SELECT c.* FROM customer c WHERE c.id = 42;
SELECT c.* FROM customer c WHERE c.id = 43;
...

-- Стало (1 запрос):
SELECT o.*, c.*
FROM invoice o
JOIN customer c ON c.id = o.customer_id
ORDER BY o.created_at DESC
LIMIT 50;
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 1 запрос вместо 101 | `LIMIT 50` — на 50 строк `o`. Если JOIN возвращает 50×N строк `o,c`, PHP создаёт 50×N объектов `OrderEntity` (дубликаты) |
| Быстро на 50–500 записей | `getResult()` + `array_unique` — Doctrine уже это делает, но память растёт |
| Нет изменений в сущности | Не работает для `OneToMany` без `JOIN FETCH` (см. ниже) |
| | Если customer большой (10 колонок) — 50×10 = 500 колонок в ответе |

**Подводный камень: дубликаты при JOIN.**

```sql
-- Если order имеет 3 lineItems:
SELECT o.id, o.number, c.id, c.name
FROM invoice o JOIN customer c ON ...
-- Результат: 3 строки для одного order (по одному на lineItem)
-- Doctrine: 3 объекта OrderEntity, все с id=42.
// → getResult() вернёт 3 объекта с id=42.
// → В шаблоне: 3 строки в таблице.
```

**Решение:** `getResult()` + `array_unique` по id, или `toIterable()` + `foreach` с проверкой.

---

### 2. `JOIN FETCH` — для OneToMany

**Было (N+1):**

```php
$orders = $repo->findBy([], ['createdAt' => 'DESC'], 50);
// foreach: $order->getLineItems() → SELECT line_item WHERE invoice_id = ?
```

**Стало (JOIN FETCH):**

```php
$orders = $this->em
    ->createQuery(
        'SELECT o
         FROM App\Persistence\OrderEntity o
         JOIN FETCH o.lineItems li
         ORDER BY o.createdAt DESC'
    )
    ->setMaxResults(50)
    ->getResult();

// foreach: $order->getLineItems() → уже загружено, SELECT нет
```

**SQL:**

```sql
SELECT o.*, li.*
FROM invoice o
LEFT JOIN invoice_line_item li ON li.invoice_id = o.id
ORDER BY o.created_at DESC;
-- LIMIT 50 не работает на SQL-уровне при JOIN FETCH (см. ниже)
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 1 запрос вместо 51 | `LIMIT` не работает корректно: `LIMIT 50` режет строки JOIN, а не заказы. 50 строк = может быть 20 заказов × 3 lineItems |
| Коллекция загружена целиком | Память: 50 заказов × 10 lineItems × 8 колонок = 4000 значений |
| Нет N+1 | Если lineItems = 1000 на заказ → 50 000 строк. Невозможно. |
| | `getResult()` — всё в RAM. Для 50 заказов × 10 items — ~10 МБ. Терпимо. Для 500 × 100 — 100 МБ. Нет. |
| | Не работает с `setMaxResults` + пагинация (Doctrine не знает, где «граница» заказа) |

**Решение для пагинации:**

```php
// Шаг 1: получить ID заказов (1 запрос)
$orderIds = $this->em
    ->createQuery(
        'SELECT o.id
         FROM App\Persistence\OrderEntity o
         ORDER BY o.createdAt DESC'
    )
    ->setMaxResults(50)
    ->setFirstResult($page * 50)
    ->getSingleColumnResult();

// Шаг 2: загрузить заказы + lineItems (2 запроса)
$orders = $this->em
    ->createQuery(
        'SELECT o
         FROM App\Persistence\OrderEntity o
         JOIN FETCH o.lineItems li
         WHERE o.id IN (' . implode(',', $orderIds) . ')
         ORDER BY o.createdAt DESC'
    )
    ->getResult();
```

**Итого: 3 запроса вместо 101.**

**Минус:** 2 дополнительных запроса. Но 3 << 101.

---

### 3. `fetch: EAGER` в маппинге

```php
#[ORM\Entity]
class OrderEntity
{
    #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)]
    private CustomerEntity $customer;

    #[ORM\OneToMany(
        mappedBy: 'order',
        fetch: EAGER,  // ← было LAZY (default)
    )]
    private Collection $lineItems;
}
```

**Что происходит:** при загрузке `OrderEntity` — автоматически `JOIN customer` и `JOIN lineItems`.

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Не нужно писать DQL. `findBy()` — и всё загружено | **Каждый** запрос к `OrderEntity` теперь делает 2 JOIN. Даже если вам нужен только `order.number`. |
| «Работает из коробки» | `SELECT o.* FROM invoice o JOIN customer c JOIN line_item li` — 3 таблицы, 3×N строк |
| | Не контролируется: не можете отключить JOIN для конкретного запроса |
| | Память: каждый `OrderEntity` несёт в себе `Customer` + `Collection<LineItem>` |
| | Если `lineItems` = 100 — каждый заказ несёт 100 объектов. 50 заказов = 5000 LineItem-объектов в RAM |

**Когда подходит:**
- `ManyToOne` (customer) — да, `EAGER` норм. Одна запись, не растёт.
- `OneToMany` (lineItems) — **нет**. Растёт. N+1 превращается в «один тяжёлый запрос».

**Правило:** `EAGER` для `ManyToOne` (одна запись). `LAZY` + `JOIN FETCH` для `OneToMany` (много записей).

---

### 4. Batch loading

```php
// В сервисе, до передачи в шаблон:
$orders = $repo->findBy([], ['createdAt' => 'DESC'], 50);

// Загрузить всех customers одним запросом:
$customerIds = $orders->map(fn ($o) => $o->getCustomer()->getId())->toArray();

$customers = $this->em
    ->getRepository(CustomerEntity::class)
    ->createQueryBuilder('c')
    ->where('c.id IN (:ids)')
    ->setParameter('ids', $customerIds)
    ->getQuery()
    ->getResult();

// Привязать:
foreach ($orders as $order) {
    $cid = $order->getCustomer()->getId();
    // Customer уже в identity map (загружен),
    // Doctrine вернёт тот же объект.
}
```

**Но:** это работает только если `customer` — `ManyToOne` и Doctrine уже «знает» про него. Для `OneToMany` — не работает, потому что коллекция `lineItems` — `LazyCollection`, и доступ к ней триггерит запрос.

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Не нужно менять маппинг | Работает только для `ManyToOne` |
| 2 запроса вместо N+1 | Нужно вручную управлять identity map |
| | Не масштабируется: 3 сущности → 3 batch запроса |
| | Код в сервисе, не в репозитории. Нарушение инкапсуляции |

---

### 5. Денормализация

```php
#[ORM\Entity]
class OrderEntity
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $customerName;  // ← денормализация

    #[ORM\Column(type: 'integer')]
    private int $totalCents;       // ← денормализация (sum lineItems)

    #[ORM\Column(type: 'integer')]
    private int $lineItemCount;    // ← денормализация (count lineItems)
}
```

**SQL:**

```sql
-- Было:
SELECT o.* FROM invoice o;
SELECT c.name FROM customer c WHERE c.id = ?;  × 50
SELECT COUNT(*) FROM line_item WHERE invoice_id = ?;  × 50

-- Стало:
SELECT o.*, o.customer_name, o.total_cents, o.line_item_count
FROM invoice o
ORDER BY o.created_at DESC
LIMIT 50;
-- 1 запрос. Нет JOIN. Нет N+1.
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 1 запрос. Всегда. | Денормализация: имя клиента в 50 местах. Изменили имя → UPDATE 50 строк. |
| Быстро. Не зависит от размера коллекций. | Нужно синхронизировать: при изменении Customer → UPDATE всех Order'ов. |
| Нет JOIN → нет дубликатов в SQL. | Trigger / event listener для синхронизации. Ещё один слой сложности. |
| Идеально для списков/таблиц. | Если клиент переименован — нужно «донести» до всех order'ов. Race condition при параллельных запросах. |
| | Стат-анализер: `order.customerName` — string. А `order.customer.name` — string + валидация. |

**Когда подходит:**
- Список/таблица: «показать 50 заказов с именем клиента и суммой».
- Данные редко меняются: имя клиента — не каждый день.
- Чтение >> записи.

**Когда не подходит:**
- Данные часто меняются.
- Нужно «живое» значение: `order.customer.name` — актуальное.
- Меньше 3–5 денормализированных полей.

---

### 6. Материализованное представление (materialized view)

```sql
CREATE MATERIALIZED VIEW mv_order_summary AS
SELECT
    o.id,
    o.number,
    c.name AS customer_name,
    o.created_at,
    SUM(li.amount_cents * li.quantity) AS total_cents,
    COUNT(li.id) AS line_item_count
FROM invoice o
JOIN customer c ON c.id = o.customer_id
LEFT JOIN invoice_line_item li ON li.invoice_id = o.id
GROUP BY o.id, o.number, c.name, o.created_at;

CREATE INDEX idx_mv_order_summary_created ON mv_order_summary(created_at DESC);

-- Обновление:
REFRESH MATERIALIZED VIEW CONCURRENTLY mv_order_summary;
```

```php
// Запрос:
$rows = $this->em->getConnection()->fetchAssociative(
    'SELECT * FROM mv_order_summary ORDER BY created_at DESC LIMIT 50'
);
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 1 запрос. Всегда. Быстро. | Отдельный объект в БД. Миграция + refresh. |
| Нет JOIN в runtime. | Refresh: `CONCURRENTLY` — блокирует чтение? Нет, но требует额外 индекс. |
| Индексы под запрос. | Данные «заморожены» на момент refresh. Задержка. |
| | Если customer.name изменён → mv устарела. До refresh — старые данные. |
| | Сложнее в операционном плане: мониторинг refresh, логирование ошибок. |
| | Не в Doctrine. Raw SQL. Стат-анализер не помогает. |

**Когда подходит:**
- Отчёты, дашборды.
- Данные меняются редко (1–2 раза в день).
- Запрос сложный: 5+ таблиц, агрегаты, оконные функции.
- Частота запросов > 100/мин.

**Когда не подходит:**
- Реальное время: «счёт только что создан, покажите».
- Меньше 2–3 таблиц в JOIN.
- Команда не готова поддерживать MV.

---

### 7. Caching (second-level cache Doctrine)

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        second_level_cache:
            enabled: true
            region:
                orders:
                    metadata: doctrine_cache.apc
                    lifetime: 300
                    lock_lifetime: 60
                customers:
                    metadata: doctrine_cache.apc
                    lifetime: 600
                    lock_lifetime: 60
```

```php
// При загрузке:
$customer = $this->em->find(CustomerEntity::class, $id);
// 1-й раз: SELECT из БД. Записывает в APC.
// 2-й раз (300 сек): APC → БД нет.
// N+1: 50 × SELECT customer → 50 × APC read. Быстрее, но 50 запросов к кэшу.
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| N+1 не исчезает, но ускоряется: 50 × 0.1 мс (APC) вместо 50 × 0.8 мс (БД) | 50 запросов к кэшу — всё ещё 50 операций. |
| Нет изменений в коде | Кэш — ещё один слой. APC / Redis. Мониторинг, инвалидация. |
| | Инвалидация: customer.name изменён → UPDATE БД + DELETE из кэша. Race condition. |
| | Lifetime: 300 сек. Если customer переименован — 5 мин «старых» имён. |
| | Память: 50 × 2 КБ = 100 КБ в APC. Не критично. Но 5000 × 2 КБ = 10 МБ. |
| | Не решает проблему «много запросов». Решает «много медленных запросов». |

**Когда подходит:**
- Данные читаемые, редко меняются.
- N+1 «медленный» (БД на другом хосте, сетевой latency).
- Как **дополнение** к JOIN FETCH, не вместо.

**Когда не подходит:**
- Данные меняются часто.
- N+1 — не «медленный», а «много». 50 × 0.1 мс = 5 мс. Не страшно. Проблема — не latency, а количество.
- Нет инфраструктуры для кэша.

---

### 8. Переписывание на raw SQL

```php
$rows = $this->em->getConnection()->fetchAllAssociative(
    'SELECT
        o.id, o.number, o.created_at,
        c.name AS customer_name,
        o.currency,
        COALESCE(SUM(li.amount_cents * li.quantity), 0) AS total_cents,
        COUNT(li.id) AS line_item_count
     FROM invoice o
     JOIN customer c ON c.id = o.customer_id
     LEFT JOIN invoice_line_item li ON li.invoice_id = o.id
     GROUP BY o.id
     ORDER BY o.created_at DESC
     LIMIT 50'
);
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Полный контроль над SQL. | Вне Doctrine. Нет entity, нет hydration. |
| 1 запрос. | Стат-анализер не помогает. |
| Индексы под запрос. | Миграция: нужно вручную синхронизировать SQL с маппингом. |
| | Нет каскадного обновления, нет identity map. |
| | Тестирование: мок Connection, не EntityManager. |
| | Переписывать каждое изменение схемы — 2 места (YAML + SQL). |

**Когда подходит:**
- Сложный отчёт, 5+ таблиц, оконные функции.
- Doctrine генерирует «не тот» SQL (лишние JOIN, неверный ORDER BY).
- Разовый запрос, не в домене.

**Когда не подходит:**
- CRUD на сущностях.
- Доменная логика: `order.total()` — не `SUM(...)` в SQL.
- Команда не готова поддерживать 2 источника правды (YAML + SQL).

---

## Сводная таблица

| Решение | Запросы | Сложность | Когда |
|---|---|---|---|
| `JOIN` (DQL) | 1 | Низкая | `ManyToOne`, список до 500 |
| `JOIN FETCH` | 1–3 | Низкая | `OneToMany`, пагинация |
| `fetch: EAGER` | 1 | Нулевая | `ManyToOne`, «не знаю DQL» |
| Batch loading | 2 | Средняя | `ManyToOne`, legacy |
| Денормализация | 1 | Высокая | Список, чтение >> запись |
| Материализованное представление | 1 | Высокая | Отчёт, 5+ таблиц |
| Caching | N (быстрых) | Средняя | Latency > количество |
| Raw SQL | 1 | Высокая | Сложный запрос, домен не нужен |

---

## Эвристика выбора

```
N+1 найден.

1. Это ManyToOne (один customer на order)?
   → JOIN в DQL. 1 запрос. Готово.

2. Это OneToMany (lineItems)?
   → JOIN FETCH + пагинация через ID. 3 запроса. Готово.

3. Нужно в списке 50 записей с агрегатами (sum, count)?
   → Денормализация или JOIN + GROUP BY.

4. Запрос сложный (5+ таблиц, оконные функции)?
   → Материализованное представление или raw SQL.

5. БД на другом хосте, latency 50 мс?
   → Caching (APC/Redis) как дополнение.

6. Данные меняются редко, чтение 1000+ раз/мин?
   → Денормализация + индекс.

7. Разовый отчёт, не в домене?
   → Raw SQL. Не в репозитории, а в команде/сервисе.
```

**Главный принцип:** N+1 — это **N отдельных round-trip'ов** к БД. Решение — **сократить round-trip'ы до 1–3**. Как — зависит от того, что именно вы читаете и как часто.

> ⏱ 55.9s · prompt 55472 tok · output 6490 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking about why database-level constraints (CHECK, UNIQUE, FOREIGN KEY, etc.) in PostgreSQL are still needed when business validation is already done in application code. This is a classic "why do we need defense in depth" question in software architecture.

Let me think about what to cover:

1. Why app-level validation isn't enough (the core argument)
2. Specific PostgreSQL tools: CHECK constraints, UNIQUE, NOT NULL, FOREIGN KEY, EXCLUDE, triggers, partial indexes, generated columns
3. Concrete examples showing where app validation fails but DB constraint catches it
4. Trade-offs: performance, complexity, when it's overkill
5. Practical patterns: how to organize constraints in migrations

Let me structure this well with concrete examples, since the user has been asking for practical code examples throughout the conversation.

Key arguments for why DB constraints matter:
- Race conditions (TOCTOU)
- Direct SQL access (admins, scripts, other services)
- Application bugs (validation skipped, wrong branch)
- Migration inconsistencies
- Different entry points (CLI, API, batch jobs)
- Data integrity as a system property, not a component property

Let me give concrete PostgreSQL examples and show how they complement (not replace) app validation.

I should be honest about when DB constraints are overkill and when they're essential.

</details>

**Кандидат:**

# Ограничения целостности в PostgreSQL: не «вместо» валидации, а «помимо»

## Главный аргумент: валидация в коде — это доверие к одному пути

Валидация в PHP гарантирует: «если запрос пришёл через `InvoiceService::create()`, то данные валидны». Это всё. Она не гарантирует:

```
1. TOCTOU (time-of-check, time-of-use)
   Thread A: SELECT count(*) FROM invoice WHERE number = 'INV-001' → 0
   Thread B: SELECT count(*) FROM invoice WHERE number = 'INV-001' → 0
   Thread A: INSERT INTO invoice (number) VALUES ('INV-001')
   Thread B: INSERT INTO invoice (number) VALUES ('INV-001')  ← дубликат

   CHECK в коде: «свободно». UNIQUE в БД: «нет, уже занят».
```

```
2. Обход валидации
   Admin: psql → INSERT INTO invoice (number, amount_cents) VALUES ('X', -5);
   Script: php artisan migrate:seed → bypass-ит InvoiceService
   Другой сервис: gRPC → не знает про ваш InvoiceService
   Отладка: $em->persist($order); $em->flush();  ← без validate()
```

```
3. Баг в коде
   if ($amount < 0 && $amount > 1_000_000) { throw ... }
   // Валидно: -5 < 0 И -5 > 1_000_000 → false
   // Ошибка логики. Данные записаны.
```

```
4. Разные entry points
   API → InvoiceService → validate() → OK
   CLI → InvoiceCommand → validate() → OK
   Batch job → $em->persist() напрямую → validate() ПРопущена
   Migration → INSERT INTO ... → validate() НЕ СУЩЕСТВУЕТ
```

Ограничение в БД — **системное свойство данных**, а не свойство одного кода. Оно работает при любом способе записи: SQL, ORM, триггер, другой сервис, админ.

---

## Что и когда

### 1. `NOT NULL` — «поле обязательно существует»

```sql
ALTER TABLE invoice
    ADD CONSTRAINT invoice_number_not_null
    CHECK (number IS NOT NULL);
```

**В коде:** `if ($number === '') throw ...`

**Почему в БД:**
- `INSERT INTO invoice (number) VALUES (NULL)` — из psql, из миграции, из другого сервиса.
- `UPDATE invoice SET number = NULL` — «случайно» в скрипте.
- Без `NOT NULL` — `SELECT number FROM invoice WHERE number IS NULL` → молча пустые строки в отчётах.

**Когда:** всегда. `NOT NULL` — не «валидация бизнес-правила», а «это поле существует в схеме». Без него схема неполная.

---

### 2. `UNIQUE` — «значения не повторяются»

```sql
ALTER TABLE invoice
    ADD CONSTRAINT invoice_number_unique
    UNIQUE (number);
```

**В коде:** `if ($repo->findByNumber($number) !== null) throw ...`

**Почему в БД:**

```php
// Race condition:
$exists = $repo->findByNumber('INV-001');  // NULL
// ... 1 мс ...
// Другой процесс:
$exists = $repo->findByNumber('INV-001');  // NULL
// Оба:
$em->persist($invoice);  // Оба INSERT'ят 'INV-001'
$em->flush();
// В коде: «свободно». В БД: UNIQUE violation.
```

Без `UNIQUE` — дубликат в продакшене. С `UNIQUE` — `IntegrityConstraintViolationException`, которую можно поймать и показать пользователю.

**Правило:** любое поле, которое является «идентификатором для человека» (number, email, external_id) — `UNIQUE` в БД. Всегда.

```sql
ALTER TABLE user
    ADD CONSTRAINT user_email_unique
    UNIQUE (email);

ALTER TABLE payment
    ADD CONSTRAINT payment_external_id_unique
    UNIQUE (gateway, external_id);  -- составной: уникальный в рамках шлюза
```

---

### 3. `CHECK` — «значение в допустимом диапазоне»

```sql
-- Сумма не может быть отрицательной
ALTER TABLE invoice_line_item
    ADD CONSTRAINT li_amount_non_negative
    CHECK (amount_cents >= 0);

-- Количество >= 1
ALTER TABLE invoice_line_item
    ADD CONSTRAINT li_quantity_positive
    CHECK (quantity >= 1);

-- Статус — из известного набора
ALTER TABLE invoice
    ADD CONSTRAINT invoice_status_check
    CHECK (status IN ('draft', 'open', 'paid', 'void'));

-- Email — грубая валидация (не полноценный RFC 5322, но «@» должен быть)
ALTER TABLE user
    ADD CONSTRAINT user_email_format
    CHECK (email LIKE '%@%.%');

-- Дата эмиссии не в будущем
ALTER TABLE invoice
    ADD CONSTRAINT invoice_issued_at_check
    CHECK (issued_at <= NOW());
```

**В коде:** `if ($cents < 0) throw ...`

**Почему в БД:**
- Код проверяет один раз. БД проверяет **каждый** `INSERT` и `UPDATE`.
- `CHECK` — это **инвариант данных**, а не «правило одного сервиса».
- Если завтра появится второй сервис, который пишет в эту таблицу — `CHECK` уже там.

**Тонкость: `CHECK` не проверяет при `SELECT`.** Только при `INSERT` и `UPDATE`. Это нормально: вы не «нарушаете» целостность, читая данные.

**Тонкость: `CHECK` не проверяет `SET DEFAULT`.** Если у столбца `DEFAULT 0` и вы делаете `INSERT INTO t (name) VALUES ('x')` — `DEFAULT` применяется, и `CHECK` проверяет. Но если `DEFAULT -1` — `CHECK` сработает. Убедитесь, что `DEFAULT` сам валиден.

---

### 4. `FOREIGN KEY` — «ссылка на существующую запись»

```sql
ALTER TABLE invoice_line_item
    ADD CONSTRAINT li_invoice_fk
    FOREIGN KEY (invoice_id)
    REFERENCES invoice(id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;
```

**В коде:** обычно нет отдельной проверки. `cascade: persist` в Doctrine делает это «автоматически» — но только через ORM.

**Почему в БД:**
- `INSERT INTO invoice_line_item (invoice_id, ...) VALUES (999999, ...)` — из psql. Invoice 999999 не существует.
- `DELETE FROM invoice WHERE id = 42` — без FK и `ON DELETE CASCADE` → сироты в `invoice_line_item`.
- `UPDATE invoice SET id = 43 WHERE id = 42` — без `ON UPDATE RESTRICT` → lineItems «прилипли» к 43.

**`ON DELETE`:**

| Действие | Когда |
|---|---|
| `CASCADE` | Дочерние записи — «часть» родителя. Удалили Invoice → строки в lineItem тоже удаляются. |
| `RESTRICT` (default) | Не разрешить удаление, если есть ссылки. «Удалите сначала lineItems». |
| `SET NULL` | Дочерняя запись ссылается на «ничего». `invoice_id = NULL`. |
| `SET DEFAULT` | Ссылка → значение по умолчанию. |

Для агрегата Invoice → LineItem: `CASCADE`. LineItem без Invoice не существует.

**Для ManyToOne (Order → Customer):**

```sql
ALTER TABLE invoice
    ADD CONSTRAINT inv_customer_fk
    FOREIGN KEY (customer_id)
    REFERENCES customer(id)
    ON DELETE RESTRICT;  -- не удалить customer, пока есть инвойсы
```

---

### 5. `EXCLUDE` — «геометрическое/диапазонное пересечение»

Редкий, но мощный инструмент PostgreSQL.

```sql
-- Бронирование гостиницы: два бронирования не могут пересекаться
ALTER TABLE booking
    ADD CONSTRAINT booking_no_overlap
    EXCLUDE USING gist (
        guest_id    WITH =,
        room_id     WITH =,
        date_range  WITH &&
    );
```

```sql
ALTER TABLE booking
    ADD COLUMN date_range tsrange
    GENERATED ALWAYS AS (tsrange(check_in, check_out)) STORED;
```

**В коде:** `SELECT * FROM booking WHERE guest_id = ? AND room_id = ? AND date_range && '[start, end)'` → `if (count > 0) throw ...`

**Почему в БД:** тот же TOCTOU. Два одновременных запроса «проверили пересечение» → оба «свободно» → оба `INSERT`'нули. `EXCLUDE` — атомарная проверка + запись.

**Когда:** бронирования, слоты, временные окна, геозоны.

---

### 6. Partial index + `UNIQUE` — «уникальность в подмножестве»

```sql
-- Email уникален только среди активных пользователей.
-- Удалённый пользователь может иметь «тот же» email (перерегистрация).
CREATE UNIQUE INDEX idx_user_email_active
    ON user (email)
    WHERE deleted_at IS NULL;
```

**В коде:** `if ($repo->findActiveByEmail($email) !== null) throw ...`

**Почему в БД:**
- Тот же TOCTOU, но в «мягком» delete-сценарии.
- Индекс на 10% таблицы вместо 100% → быстрее, меньше память.

**Когда:** soft delete, «один активный подписочный план на аккаунт», «один действующий контракт на сотрудника».

```sql
-- Один действующий контракт на сотрудника
CREATE UNIQUE INDEX idx_contract_one_active
    ON employment_contract (employee_id)
    WHERE end_date IS NULL;  -- «действующий» = end_date IS NULL
```

---

### 7. `GENERATED COLUMN` — «производное значение, которое нельзя испортить»

```sql
ALTER TABLE invoice_line_item
    ADD COLUMN line_total_cents integer
    GENERATED ALWAYS AS (amount_cents * quantity) STORED;
```

**В коде:** `$item->total() = $item->amount * $item->quantity`

**Почему в БД:**
- `UPDATE invoice_line_item SET line_total_cents = 0 WHERE ...` — из psql. Не работает: `GENERATED ALWAYS` — только read-only.
- Не нужно «не забыть» пересчитать `line_total_cents` при изменении `amount_cents`.
- Индекс по `line_total_cents` — всегда актуален.

**Когда:** суммы, хеши, нормализованные значения. «Значение, которое является функцией других столбцов и не должно расходиться с ними».

```sql
-- Хеш email для поиска (без хранения самого email в индексе)
ALTER TABLE user
    ADD COLUMN email_hash text
    GENERATED ALWAYS AS (md5(email)) STORED;

CREATE INDEX idx_user_email_hash ON user (email_hash);
```

---

### 8. Триггеры — «действие при изменении данных»

```sql
-- Обновить updated_at при любом UPDATE
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER invoice_updated_at
    BEFORE UPDATE ON invoice
    FOR EACH ROW
    EXECUTE FUNCTION set_updated_at();
```

**В коде:** `preUpdate`-listener в Doctrine.

**Почему в БД:**
- Listener в Doctrine срабатывает **только** через ORM.
- `UPDATE invoice SET status = 'paid' WHERE id = 42` — из psql → триггер сработал, listener — нет.
- `updated_at` — системное свойство. Не «бизнес-правило», а «метаданные записи».

**Когда:** `created_at`, `updated_at`, audit log, пересчёт агрегатов.

**Внимание:** триггеры не видны в migrations Doctrine 2. Нужен raw SQL в миграции:

```php
// src/Migration/Version20240115000001.php
public function up(Schema $schema): void
{
    $this->addSql('CREATE TRIGGER invoice_updated_at ...');
}
```

---

## Что я ставлю, а что нет

### Всегда ставлю

| Ограничение | Почему |
|---|---|
| `NOT NULL` | Поле существует в схеме. Без него — неполная запись. |
| `UNIQUE` на идентификаторах | `number`, `email`, `external_id`. TOCTOU. |
| `FOREIGN KEY` | Ссылка на существующую запись. `CASCADE` для агрегатов. |
| `CHECK` на диапазонах | `amount >= 0`, `status IN (...)`. Инвариант данных. |
| `ON DELETE` явный | Не `RESTRICT` по умолчанию. Решено: `CASCADE` или `SET NULL`. |

### Ставлю, если осмысленно

| Ограничение | Когда |
|---|---|
| Partial `UNIQUE` | Soft delete, «один активный» |
| `GENERATED COLUMN` | Производные значения, которые «расходятся» |
| `EXCLUDE` | Бронирования, слоты |
| Триггер `updated_at` | Системные метаданные |

### НЕ ставлю

| Ограничение | Почему нет |
|---|---|
| `CHECK (email ~ '^[a-z]+@[a-z]+\.[a-z]+$')` | Слишком строгий regex в БД. Валидация email — в коде. БД: только `LIKE '%@%.%'`. |
| `CHECK` на бизнес-правилах с 5 условиями | «Если status = paid, то paid_at IS NOT NULL, AND paid_at >= issued_at, AND amount > 0». Это бизнес-логика. В коде. |
| Триггеры на бизнес-логике | «При создании invoice → создать 3 записи в audit». Это не «целостность данных», а «бизнес-процесс». В коде. |
| `CHECK` на `text` полях > 10 символов | «CHECK (description != '')» — да. «CHECK (description NOT LIKE '%bad word%')» — нет. |

---

## Граница: что в коде, что в БД

```
┌─────────────────────────────────────────────────────────────────────┐
│  Код приложения (InvoiceService, Domain\Invoice)                    │
│                                                                     │
│  • Бизнес-правила: «invoice не может быть пустым»                   │
│  • Контекстные проверки: «менеджер может видеть только свой отдел»  │
│  • Multi-entity инварианты: «сумма lineItems = total»              │
│  • Условная логика: «если VIP → скидка 10%»                        │
│  • Пользовательские сообщения: «Недостаточно средств, не хватает 500 центов»│
│  • TOCTOU-безопасные операции: SELECT FOR UPDATE + UPDATE           │
├─────────────────────────────────────────────────────────────────────┤
│  PostgreSQL (constraints, indexes)                                   │
│                                                                     │
│  • Нотация: NOT NULL, UNIQUE, FK                                     │
│  • Диапазон: CHECK (amount >= 0)                                    │
│  • Перечень: CHECK (status IN (...))                                │
│  • Пересечение: EXCLUDE (date_range)                                 │
│  • Атомарность: UNIQUE (email) WHERE deleted_at IS NULL             │
│  • Производные: GENERATED (line_total = amount * qty)              │
│  • Системные: TRIGGER (updated_at)                                  │
│                                                                     │
│  Принципиальное отличие:                                             │
│  Код — «можно ли это сделать В ЭТОТ МОМЕНТ?»                        │
│  БД  — «существует ли запись с ТАКИМИ ДАННЫМИ?»                    │
└─────────────────────────────────────────────────────────────────────┘
```

**Код** знает контекст: кто пользователь, какой отдел, какая транзакция.
**БД** знает данные: это значение, это ссылка, это диапазон.

Они не дублируют друг друга. Они **на разных уровнях**.

---

## Практика: миграция с комментариями

```php
// src/Migration/Version20240115000001.php
final class Version20240115000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // ── Таблица ──────────────────────────────────────────────
        $this->addSql('
            CREATE TABLE invoice (
                id            serial PRIMARY KEY,
                number        varchar(32)  NOT NULL,
                customer_id   integer      NOT NULL,
                status        varchar(16)  NOT NULL DEFAULT '\''draft'\'',
                currency      char(3)      NOT NULL,
                total_cents   integer      NOT NULL DEFAULT 0,
                issued_at     timestamptz  NOT NULL DEFAULT NOW(),
                created_at    timestamptz  NOT NULL DEFAULT NOW(),
                updated_at    timestamptz  NOT NULL DEFAULT NOW(),

                -- Инварианты данных. Работают при любом INSERT/UPDATE,
                -- включая psql, другой сервис, batch-скрипт.
                CONSTRAINT inv_number_not_null   CHECK (number IS NOT NULL),
                CONSTRAINT inv_number_len        CHECK (char_length(number) BETWEEN 3 AND 32),
                CONSTRAINT inv_status_check      CHECK (status IN ('\''draft'\'', '\''open'\'', '\''paid'\'', '\''void'\'')),
                CONSTRAINT inv_currency_check    CHECK (currency IN ('\''USD'\'', '\''EUR'\'', '\''GBP'\'', '\''KZT'\'')),
                CONSTRAINT inv_total_nonneg      CHECK (total_cents >= 0),
                CONSTRAINT inv_issued_not_future CHECK (issued_at <= NOW())
            )
        ');

        // Уникальность: TOCTOU-безопасность
        $this->addSql('
            CREATE UNIQUE INDEX idx_invoice_number
                ON invoice (number)
        ');

        // FK: агрегат. LineItem без Invoice не существует.
        $this->addSql('
            ALTER TABLE invoice
                ADD CONSTRAINT inv_customer_fk
                FOREIGN KEY (customer_id)
                REFERENCES customer(id)
                ON DELETE RESTRICT
                ON UPDATE RESTRICT
        ');

        // ── Дочерняя таблица ─────────────────────────────────────
        $this->addSql('
            CREATE TABLE invoice_line_item (
                id            serial PRIMARY KEY,
                invoice_id    integer  NOT NULL,
                description   varchar(255) NOT NULL,
                amount_cents  integer  NOT NULL,
                currency      char(3)  NOT NULL,
                quantity      integer  NOT NULL,
                position      integer  NOT NULL DEFAULT 0,

                -- Инварианты:
                CONSTRAINT li_desc_not_empty   CHECK (description IS NOT NULL AND description <> '\''\''),
                CONSTRAINT li_amount_nonneg    CHECK (amount_cents >= 0),
                CONSTRAINT li_qty_positive     CHECK (quantity >= 1),
                CONSTRAINT li_currency_match   CHECK (currency IN ('\''USD'\'', '\''EUR'\'', '\''GBP'\'', '\''KZT'\''))

                -- line_total = amount * qty:
                , line_total_cents integer GENERATED ALWAYS AS (amount_cents * quantity) STORED
            )
        ');

        // FK: CASCADE — lineItem не существует без invoice
        $this->addSql('
            ALTER TABLE invoice_line_item
                ADD CONSTRAINT li_invoice_fk
                FOREIGN KEY (invoice_id)
                REFERENCES invoice(id)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
        ');

        // Индексы под частые запросы:
        $this->addSql('
            CREATE INDEX idx_li_invoice ON invoice_line_item (invoice_id)
        ');

        // ── Триггер: updated_at ──────────────────────────────────
        // Системное свойство, не бизнес-правило.
        $this->addSql('
            CREATE OR REPLACE FUNCTION trg_set_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
        ');

        $this->addSql('
            CREATE TRIGGER invoice_updated_at
                BEFORE UPDATE ON invoice
                FOR EACH ROW
                EXECUTE FUNCTION trg_set_updated_at()
        ');

        $this->addSql('
            CREATE TRIGGER li_updated_at
                BEFORE UPDATE ON invoice_line_item
                FOR EACH ROW
                EXECUTE FUNCTION trg_set_updated_at()
        ');
    }

    public function down(Schema $schema): void
    {
        // Обратный порядок: FK → indexes → tables
        $this->addSql('DROP TRIGGER IF EXISTS li_updated_at ON invoice_line_item');
        $this->addSql('DROP TRIGGER IF EXISTS invoice_updated_at ON invoice');
        $this->addSql('DROP FUNCTION IF EXISTS trg_set_updated_at()');
        $this->addSql('DROP TABLE IF EXISTS invoice_line_item CASCADE');
        $this->addSql('DROP TABLE IF EXISTS invoice CASCADE');
    }
}
```

---

## Честная оговорка

Ограничения в БД **не заменяют** валидацию в коде. Они делают другую работу:

| Код говорит | БД говорит |
|---|---|
| «Ты не можешь сделать это, потому что...» (контекст, роль, состояние) | «Эта запись не может существовать в этой таблице» (инвариант) |
| «Недостаточно средств, не хватает 500 центов» | `CHECK (amount_cents >= 0)` → `IntegrityConstraintViolationException` |
| «Ты не менеджер этого отдела» | Не знает про «отдел». Знает: `customer_id` существует в `customer`. |
| «Сначала оплати, потом отмени» | Не знает про «сначала/потом». Знает: `status IN (...)`. |

**Код — политика. БД — закон.** Закон не объясняет, почему нельзя. Закон просто говорит: «нет». И это правильно: закон применяется ко всем, кто попадает в его юрисдикцию. Независимо от того, пришли вы через ORM, psql или gRPC.

> ⏱ 48.1s · prompt 61650 tok · output 5540 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь просит описать процесс диагностики и оптимизации медленного запроса в PostgreSQL. Это практический вопрос, требующий структурированного ответа с конкретными инструментами, командами и типовыми причинами.

Я должен:
1. Описать процесс поэтапно (от воспроизведения до финальной оптимизации)
2. Конкретные инструменты: EXPLAIN ANALYZE, pg_stat_statements, pg_stat_activity, pg_stat_user_indexes, pg_stats, autovacuum, pg_buffercache
3. Типовые причины: missing index, bad plan, lock, I/O, bloat, N+1 (на уровне приложения), данные, память
4. Показывать реальные SQL-команды и примеры вывода EXPLAIN
5. Trade-offs решений
6. Не быть слишком академичным — дать практический workflow

Структура:
1. Воспроизведение и замер
2. EXPLAIN ANALYZE — чтение плана
3. Типовые паттерны в плане и их решения
4. Инструменты мониторинга
5. Типовые причины (checklist)
6. Оптимизация: индексы, переписывание, данные
7. Что НЕ делать

Буду конкретным, с примерами SQL и вывода.

</details>

**Кандидат:**

# Медленный запрос в PostgreSQL: диагностика и оптимизация

## Принцип

Не «ускорить запрос». А **понять, где именно он теряет время**, и устранить именно это. 80% медленных запросов — это 3–4 конкретных паттерна. Остальные 20% — уже интереснее.

---

## Этап 1: Воспроизвести и замерить

### Что нужно знать до начала

```
1. Какой запрос? (точный SQL, не DQL, не «медленная страница»)
2. На каких данных? (1000 строк или 100 млн?)
3. Как часто? (1 раз/день или 100 раз/сек?)
4. Сколько времени? (2 сек или 30 сек?)
5. На какой машине? (prod, staging, локальная?)
6. Какой workload параллельно? (другие запросы, VACUUM, backup?)
```

### Замер

```sql
-- Точный замер одного запроса:
\timing on
SELECT * FROM invoice WHERE customer_id = 42 AND status = 'open';
-- Time: 1847.332 ms

-- Несколько раз, чтобы отсечь кэш:
SELECT * FROM invoice WHERE customer_id = 42 AND status = 'open';  -- 1847 ms
SELECT * FROM invoice WHERE customer_id = 43 AND status = 'open';  -- 1841 ms
SELECT * FROM invoice WHERE customer_id = 44 AND status = 'open';  -- 1850 ms
-- Стабильно ~1.8 сек → не кэш, не случайность.
```

### `pg_stat_statements` — что реально происходит

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
-- postgresql.conf:
-- shared_preload_libraries = 'pg_stat_statements'
-- pg_stat_statements.track = all

-- Топ-20 по суммарному времени:
SELECT
    round(total_exec_time / 1000, 1)    AS total_sec,
    round(mean_exec_time, 1)            AS mean_ms,
    calls,
    round(100.0 * total_exec_time /
        SUM(total_exec_time) OVER (), 1) AS pct,
    left(regexp_replace(query, '\s+', ' ', 'g'), 100) AS query
FROM pg_stat_statements
WHERE query NOT ILIKE '%pg_stat%'
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Что искать:**

```
 total_sec | mean_ms | calls | pct  | query
-----------+---------+-------+------+------
   4821.3  |  2115.2 |  2279 | 34.1| SELECT o.*, c.name FROM invoice o JOIN customer c ON...
   1203.5  |   451.2 |  2667 |  8.5| SELECT * FROM invoice_line_item WHERE invoice_id IN...
    891.2  |   212.1 |  4201 |  6.3| UPDATE invoice SET status = 'paid' WHERE id IN...
```

`mean_ms` > 100 при `calls` > 1000 → системная проблема, не разовый выброс.

---

## Этап 2: `EXPLAIN (ANALYZE, BUFFERS, TIMING, FORMAT TEXT)`

Это **главный инструмент**. Не `EXPLAIN` (план без данных), а `EXPLAIN ANALYZE` (фактическое выполнение).

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING, FORMAT TEXT)
SELECT o.id, o.number, c.name, SUM(li.amount_cents * li.quantity) AS total
FROM invoice o
JOIN customer c ON c.id = o.customer_id
JOIN invoice_line_item li ON li.invoice_id = o.id
WHERE o.status = 'open'
  AND o.created_at > NOW() - INTERVAL '30 days'
GROUP BY o.id, o.number, c.name
ORDER BY total DESC
LIMIT 50;
```

### Пример вывода и как его читать

```
Limit  (cost=1284532.11..1284532.61 rows=50 width=128) (actual time=1847.231..1847.412 rows=50 loops=1)
  Buffers: shared hit=234567 read=45678
  ->  Sort  (cost=1284532.11..1284535.00 rows=1156 width=128) (actual time=1847.220..1847.289 rows=50 loops=1)
        Sort Key: (sum(((li.amount_cents * li.quantity))) DESC)
        Sort Method: top-N heapsort  Memory: 40kB
        Buffers: shared hit=234567 read=45678
        ->  GroupAggregate  (cost=4523.11..1284232.00 rows=1156 width=128) (actual time=0.312..1845.112 rows=1247 loops=1)
              Group Key: o.id
              Filter: (o.status = 'open' AND o.created_at > (now() - '30 days'::interval))
              Rows Removed by Filter: 847523
              Buffers: shared hit=234567 read=45678
              ->  Nested Loop  (cost=0.71..4213.00 rows=50000 width=96) (actual time=0.098..342.112 rows=524711 loops=1)
                    Buffers: shared hit=123456 read=45678
                    ->  Seq Scan on invoice o  (cost=0.00..8453.00 rows=105 width=72) (actual time=0.012..12.334 rows=105 loops=1)
                          Filter: (status = 'open' AND created_at > (now() - '30 days'::interval))
                          Rows Removed by Filter: 847523
                          Buffers: shared hit=12345 read=3456
                    ->  Index Scan using li_invoice_id_idx on invoice_line_item li  (cost=0.71..39.80 rows=5 width=48) (actual time=0.003..2.891 rows=5 loops=105)
                          Index Cond: (invoice_id = o.id)
                          Buffers: shared hit=111111 read=42222
```

### Читаем по ключевым маркерам

| Что вижу в плане | Что это значит |
|---|---|
| `Seq Scan on invoice` + `Rows Removed by Filter: 847523` | **Нет индекса** по `status` + `created_at`. Сканируем 847k строк, оставляем 105. |
| `Nested Loop` + `actual time=...342 ms` + `loops=105` | Для каждой из 105 строк `invoice` — по 5 строк `line_item`. 105 × 5 = 525 операций. Само по себе ОК. |
| `Buffers: shared hit=234567 read=45678` | 45678 страниц **прочитаны с диска**. Это I/O. Остальные — из кэша. |
| `Sort Method: top-N heapsort Memory: 40kB` | Sort в памяти. Не в temp file. ОК. |
| `actual time=1847.231` на `Limit` | Общий запрос — 1.85 сек. Совпадает с замером. |
| `GroupAggregate` занимает `1845 ms` из 1847 | **99.9% времени** — в агрегации, не в чтении данных. |

### Главный инсайт из этого плана

Проблема **не** в `JOIN` и **не** в `line_item`. Проблема:

```
Seq Scan on invoice: 847523 строк отфильтрованы → 105 осталось.
```

Нет индекса по `(status, created_at)`. PostgreSQL читает всю таблицу.

---

## Этап 3: Типовые паттерны и их лечение

### Паттерн 1: `Seq Scan` при фильтрации

**Симптом:**
```
Seq Scan on invoice (cost=0.00..8453.00)
  Filter: (status = 'open' AND created_at > ...)
  Rows Removed by Filter: 847523
```

**Диагностика:**
```sql
-- Сколько строк в таблице?
SELECT count(*) FROM invoice;  -- 847628

-- Сколько проходит фильтр?
SELECT count(*) FROM invoice WHERE status = 'open' AND created_at > NOW() - INTERVAL '30 days';  -- 105

-- Selectivity: 105 / 847628 = 0.012%
-- PostgreSQL скажет: «читать 0.012% через Seq Scan?» — да, если нет индекса.
```

**Лечение:**
```sql
CREATE INDEX idx_invoice_status_created
    ON invoice (status, created_at DESC)
    INCLUDE (id, number);  -- covering index, не читать heap
```

```sql
-- Проверить:
EXPLAIN (ANALYZE, BUFFERS)
SELECT * FROM invoice
WHERE status = 'open'
  AND created_at > NOW() - INTERVAL '30 days';
-- Index Scan using idx_invoice_status_created (cost=0.42..5.21 rows=105)
-- Buffers: shared hit=12
-- actual time: 0.034 ms
```

**Было:** 12.3 мс (Seq Scan) → **Стало:** 0.034 мс (Index Scan). **360× быстрее.**

**Когда Seq Scan — норм:**
- Таблица 100 строк.
- Фильтр проходит > 30% строк.
- Таблица только для чтения, без индексов (аналитика).
- `WHERE 1=1` — нет фильтра.

---

### Паттерн 2: `Nested Loop` с большим `loops`

**Симптом:**
```
Nested Loop (cost=0.71..4213.00) (actual time=0.098..342.112 rows=524711)
  ->  Seq Scan on orders (rows=105)
  ->  Index Scan on order_items (rows=5000) (loops=105)
```

105 × 5000 = 525 000 строк. Если это нужно — ОК. Если нет — проблема.

**Диагностика:**
```sql
-- Сколько реально нужно?
SELECT count(*) FROM order_items WHERE order_id IN (
    SELECT id FROM orders WHERE status = 'open' AND created_at > NOW() - INTERVAL '30 days'
);
-- 524 711. Да, столько нужно.

-- Но мы берём LIMIT 50. Значит, 524 711 строк — только для агрегации.
-- GroupAggregate должен обходиться без сортировки всех 524 711.
```

**Лечение:**
```sql
-- Вариант А: Hash Join вместо Nested Loop (если PostgreSQL не выбрал сам)
SET enable_nestloop = off;
EXPLAIN (ANALYZE, BUFFERS)
SELECT ...;
-- Hash Join (cost=...) — быстрее при 5000 строк во внутреннем

-- Вариант Б: переписать запрос, чтобы агрегация была раньше
SELECT o.id, o.number, c.name,
       (SELECT SUM(li.amount_cents * li.quantity)
        FROM invoice_line_item li
        WHERE li.invoice_id = o.id
       ) AS total
FROM invoice o
JOIN customer c ON c.id = o.customer_id
WHERE o.status = 'open'
  AND o.created_at > NOW() - INTERVAL '30 days'
ORDER BY total DESC
LIMIT 50;
-- Subquery: для каждого из 105 заказов — одна сумма.
-- Не 524 711 строк в сортировку, а 105 сумм.
```

---

### Паттерн 3: `Sort` на огромном наборе

**Симптом:**
```
Sort (cost=1284532.11..1284535.00 rows=1156) (actual time=1847.220..1847.289)
  Sort Key: (sum(...) DESC)
  Sort Method: external merge  Disk: 523416kB
```

`Disk: 523416kB` — **сортировка не влезла в память**, пошла на диск. Это **главный убийца**.

**Диагностика:**
```sql
-- Сколько памяти доступно для сортировки?
SHOW work_mem;  -- 4MB (default)

-- Сколько строк сортируем?
-- 524 711 строк × 128 байт = ~64 МБ
-- work_mem = 4 МБ → не влезает → внешняя сортировка
```

**Лечение:**

```sql
-- Вариант А: увеличить work_mem (на session, не на сервер)
SET work_mem = '64MB';
EXPLAIN (ANALYZE, BUFFERS) SELECT ...;
-- Sort Method: top-N heapsort  Memory: 40kB
-- Disk: (нет)

-- Вариант Б: сократить набор ДО сортировки
-- (индекс по status + created_at → 105 строк, а не 524 711)

-- Вариант Ц: не сортировать. Использовать индекс.
-- Если есть индекс по total_cents DESC — сортировка бесплатна.
```

**Важно:** `work_mem` — на **оператор**, не на запрос. Если в плане 3 сортировки — `work_mem` × 3. 64 МБ × 3 = 192 МБ на один запрос. × 100 параллельных = 19 ГБ. Убейте сервер.

```sql
-- Безопасно:
SET work_mem = '32MB';  -- на session
-- В production: pgBouncer + pool + лимит
```

---

### Паттерн 4: `Bitmap Heap Scan` с ре-сканом

**Симптом:**
```
Bitmap Heap Scan on invoice (recheck Cond: ...)
  Recheck Cond: (status = 'open')
  Filter: (created_at > ...)
  Rows Removed by Filter: 45000
  ->  Bitmap Index Scan on idx_invoice_status
        Index Cond: (status = 'open')
```

`Rows Removed by Filter: 45000` — индекс по `status` нашёл 45 105 строк, но `created_at` отсекает 45 000. Индекс **не covering**.

**Лечение:**
```sql
-- Было:
CREATE INDEX idx_invoice_status ON invoice (status);

-- Стало:
CREATE INDEX idx_invoice_status_created ON invoice (status, created_at DESC);

-- Или partial:
CREATE INDEX idx_invoice_open_recent
    ON invoice (created_at DESC)
    WHERE status = 'open';
```

---

### Паттерн 5: `CTE` как оптимизационный барьер

**Симптом:**
```sql
WITH active AS (
    SELECT id FROM invoice WHERE status = 'open'
)
SELECT * FROM active a JOIN customer c ON ...;
```

```
CTE Scan on active (actual time=... rows=105)
  ->  Seq Scan on invoice (rows=105, filter: status = 'open')
```

В PostgreSQL < 12 `CTE` — оптимизационный барьер: данные **материализуются** в temp table, даже если `WHERE`-условие можно «пропустить» внутрь JOIN.

PostgreSQL 12+: `WITH` = «опциональная» материализация. `WITH ... AS MATERIALIZED` — принудительно. `WITH ... AS NOT MATERIALIZED` — нет материализации.

```sql
-- PostgreSQL 12+:
WITH active AS (
    SELECT id, number, customer_id
    FROM invoice
    WHERE status = 'open'
    AND created_at > NOW() - INTERVAL '30 days'
) NOT MATERIALIZED  -- ← не материализовать, пусть оптимизирует
SELECT ...;
```

---

### Паттерн 6: `NestLoop` с `SubPlan` в `WHERE`

**Симптом:**
```sql
SELECT * FROM invoice o
WHERE EXISTS (
    SELECT 1 FROM invoice_line_item li
    WHERE li.invoice_id = o.id AND li.amount_cents > 10000
);
```

```
Seq Scan on invoice o (rows=847628)
  Filter: (SubPlan 1)
    ->  SubPlan 1
          ->  Index Scan on li_invoice_id (rows=5) (loops=847628)
```

847 628 × 5 = 4 238 140 операций. Даже по индексу — медленно.

**Лечение:**
```sql
-- Вариант А: JOIN вместо EXISTS
SELECT DISTINCT o.*
FROM invoice o
JOIN invoice_line_item li ON li.invoice_id = o.id
WHERE li.amount_cents > 10000
  AND o.status = 'open';

-- Вариант Б: полу-join
SELECT o.* FROM invoice o
WHERE o.id IN (
    SELECT li.invoice_id
    FROM invoice_line_item li
    WHERE li.amount_cents > 10000
);
-- PostgreSQL оптимизирует IN → Hash Semi Join
```

---

### Паттерн 7: Блокировки

**Симптом:** запрос висит, `EXPLAIN ANALYZE` не завершается.

**Диагностика:**
```sql
-- Кто блокирует?
SELECT
    blocked.pid     AS blocked_pid,
    blocked.query   AS blocked_query,
    blocking.pid    AS blocking_pid,
    blocking.query  AS blocking_query,
    blocking.state
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND NOT bl.granted
JOIN pg_locks kl ON kl.locktype = bl.locktype
    AND kl.database IS NOT DISTINCT FROM bl.database
    AND kl.relation IS NOT DISTINCT FROM bl.relation
    AND kl.transactionid IS NOT DISTINCT FROM bl.transactionid
    AND kl.pid <> bl.pid
JOIN pg_stat_activity blocking ON blocking.pid = kl.pid
WHERE blocked.state = 'active'
  AND blocking.state IS NOT NULL;

-- Кто сколько ждёт?
SELECT pid, now() - query_start AS waiting, state, left(query, 80)
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY waiting DESC;
```

**Лечение:**
```sql
-- Найти блокирующего:
SELECT pg_cancel_backend(<blocking_pid>);  -- мягко
SELECT pg_terminate_backend(<blocking_pid>); -- жёстко

-- Предотвратить:
-- 1. SET lock_timeout = '5s';  -- на session
-- 2. NOWAIT: SELECT ... FOR UPDATE NOWAIT;  -- сразу ошибка, не ждать
-- 3. DEADLOCK DETECTION: deadlock_timeout = '1s'
```

---

### Паттерн 8: Table bloat

**Симптом:**
```
Seq Scan on invoice (cost=0.00..8453.00)
  Buffers: shared hit=12345 read=45678  ← много read, мало hit
```

Таблица «раздута»: 847 628 живых строк, но на диске 15 000 000 страниц (из-за UPDATE'ов без VACUUM).

**Диагностика:**
```sql
-- Насколько «раздута»?
SELECT
    relname,
    n_live_tup AS live,
    n_dead_tup AS dead,
    round(100.0 * n_dead_tup / NULLIF(n_live_tup + n_dead_tup, 0), 1) AS dead_pct,
    round(pg_size_pretty(pg_total_relation_size(relid))) AS total_size,
    last_vacuum,
    last_autovacuum
FROM pg_stat_user_tables
WHERE relname = 'invoice'
ORDER BY dead_pct DESC;

-- live=847628, dead=4500000, dead_pct=84.1%
-- total_size=2.3 GB
-- last_autovacuum=2024-01-10  (4 дня назад)
```

**Лечение:**
```sql
-- Немедленно:
VACUUM (ANALYZE, VERBOSE) invoice;

-- Проверить autovacuum:
SELECT relname, autovacuum_enabled,
       relfrozenxid, relminmxid
FROM pg_class c
JOIN pg_am a ON c.relam = a.oid
WHERE relname = 'invoice';

-- Настройки autovacuum:
-- autovacuum = on
-- autovacuum_max_workers = 3
-- autovacuum_vacuum_scale_factor = 0.05  (5% dead → vacuum)
-- autovacuum_vacuum_cost_limit = 200

-- Для горячих таблиц:
ALTER TABLE invoice SET (
    autovacuum_vacuum_scale_factor = 0.01,  -- 1% вместо 5%
    autovacuum_vacuum_cost_limit = 1000
);
```

---

## Инструменты мониторинга (не разовые, а постоянные)

### `pg_stat_activity` — кто что делает сейчас

```sql
SELECT
    pid,
    usename,
    state,
    wait_event_type,
    wait_event,
    now() - query_start AS duration,
    left(query, 100) AS query,
    client_addr
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY duration DESC;
```

### `pg_stat_user_indexes` — неиспользуемые индексы

```sql
SELECT
    schemaname,
    relname AS table,
    indexrelname AS index,
    idx_scan AS scans,
    round(pg_size_pretty(pg_relation_size(indexrelid))) AS size
FROM pg_stat_user_indexes
WHERE idx_scan = 0
  AND indexrelname NOT LIKE '%pkey%'
ORDER BY pg_relation_size(indexrelid) DESC;
```

> 0 сканов → кандидат на удаление. Но: может использоваться только при peak load. Проверить за 30 дней.

### `pg_stat_user_tables` — sequential scan

```sql
SELECT
    relname,
    seq_scan,
    seq_tup_read,
    idx_scan,
    n_live_tup,
    n_dead_tup
FROM pg_stat_user_tables
WHERE seq_scan > 1000
ORDER BY seq_tup_read DESC;
```

Много `seq_scan` + много `seq_tup_read` → нет подходящего индекса.

### `pg_buffercache` — что в shared buffers

```sql
SELECT
    relname,
    count(*) AS pages,
    round(100.0 * count(*) FILTER (WHERE is_pinned) / count(*), 1) AS pinned_pct
FROM pg_buffercache b
JOIN pg_class c ON b.relfilenode = c.relfilenode
WHERE c.relname = 'invoice'
GROUP BY relname;
```

### `pg_locks` — блокировки

```sql
SELECT
    l.pid,
    l.mode,
    l.granted,
    l.relation::regclass AS table,
    a.query
FROM pg_locks l
JOIN pg_stat_activity a ON a.pid = l.pid
WHERE l.relation IS NOT NULL
ORDER BY l.pid;
```

---

## Checklist: что проверить, когда запрос медленный

```
□ 1. EXPLAIN ANALYZE — какой оператор ест время?
    → Seq Scan? Sort? Nested Loop? Join?

□ 2. Buffers: hit vs read?
    → read > 10% → I/O bound. Кэш не покрывает.
    → hit > 99% → CPU bound. Алгоритм/план.

□ 3. Rows Removed by Filter?
    → Много → индекс не покрывает условия.
    → 0 → фильтр не нужен.

□ 4. Sort: Memory или Disk?
    → Disk → work_mem мал. Или набор огромный.

□ 5. Table bloat?
    → n_dead_tup > n_live_tup → VACUUM.

□ 6. Блокировки?
    → pg_stat_activity: wait_event = Lock?

□ 7. Параллельный workload?
    → pg_stat_activity: другие запросы едят I/O?
    → autovacuum идёт?

□ 8. Индекс существует, но не используется?
    → EXPLAIN: Seq Scan, хотя индекс есть.
    → UPDATE statistics? ANALYZE.
    → Cost-based: PostgreSQL считает, что Seq Scan дешевле.
    → enable_indexscan = on (для отладки).

□ 9. Данные?
    → 1000 строк vs 100 млн. План может быть другим.
    → UPDATE statistics: ANALYZE table;

□ 10. Приложение?
    → N+1 на уровне ORM?
    → 100 отдельных запросов по 10 мс = 1 сек.
    → Не «медленный запрос», а 100 запросов.
```

---

## Оптимизация: что делать

### Порядок действий

```
1. Индекс. (дешёвее всего, даёт 10–100×)
2. Переписать запрос. (JOIN вместо subquery, CTE, LIMIT)
3. work_mem / maintenance_work_mem. (сортировка, hash join)
4. VACUUM / ANALYZE. (bloat, stale statistics)
5. Денормализация. (precomputed column, materialized view)
6. Партиционирование. (если таблица > 100 млн строк)
7. Read replica. (если чтение давит на запись)
8. Архивирование. (hot data vs cold data)
```

### Индекс: как выбрать

```sql
-- 1. Какой запрос?
SELECT * FROM invoice
WHERE status = 'open'
  AND customer_id = 42
  AND created_at > NOW() - INTERVAL '30 days'
ORDER BY total_cents DESC;

-- 2. Условия: equality (status, customer_id) + range (created_at)
--    Порядок: equality FIRST, range LAST.
CREATE INDEX idx_invoice_lookup
    ON invoice (customer_id, status, created_at DESC)
    INCLUDE (total_cents, id, number);

-- 3. Проверить:
EXPLAIN (ANALYZE, BUFFERS)
SELECT * FROM invoice
WHERE customer_id = 42 AND status = 'open'
  AND created_at > NOW() - INTERVAL '30 days'
ORDER BY total_cents DESC;
```

### `ANALYZE` — свежие statistics

```sql
-- PostgreSQL опирается на statistics для выбора плана.
-- Если данные изменились (массовый UPDATE, INSERT) — statistics устарели.
ANALYZE invoice;
ANALYZE invoice_line_item;

-- Проверить:
SELECT attname, n_distinct, most_freq_vals, most_freq_range
FROM pg_stats
WHERE tablename = 'invoice'
  AND attname = 'status';
-- status='open' → 5% строк. status='paid' → 80%.
-- Если statistics говорят «open = 50%» — план будет неправильный.
```

---

## Чего НЕ делать

### ❌ Удалить «неиспользуемый» индекс без проверки

```
idx_scan = 0 за 7 дней.
→ Удалили.
→ В пятницу вечером peak load → запрос, который использовал этот индекс → Seq Scan → prod упал.
```

**Правильно:** удалить только если `idx_scan = 0` за 30+ дней И нет сезонности.

### ❌ Увеличить `work_mem` глобально

```ini
# postgresql.conf
work_mem = 256MB  # ← глобально
```

100 параллельных запросов × 256 МБ = 25 ГБ. OOM.

**Правильно:** `work_mem = 4MB` глобально. `SET work_mem = '64MB'` на session для тяжёлых запросов.

### ❌ `DISABLE`-флаги для «ускорения»

```sql
SET enable_seqscan = off;
SET enable_nestloop = off;
```

Это **отладочные** флаги. В production — нет. PostgreSQL знает лучше. Если план «неправильный» — проблема в statistics или данных, не в «запрете» оператора.

### ❌ Переполнить таблицу индексами

```
10 индексов на одной таблице.
→ Каждый INSERT: 10 UPDATE индексов.
→ Каждый UPDATE: 10 UPDATE индексов.
→ Каждый DELETE: 10 DELETE из индексов.
→ Нагрузка на запись × 10.
```

**Правильно:** 3–5 индексов на горячую таблицу. Каждый — под конкретный запрос.

### ❌ `VACUUM FULL` в production

```sql
VACUUM FULL invoice;  -- exclusive lock, 30 мин для 2 ГБ таблицы
```

**Правильно:** `VACUUM (ANALYZE)` — без lock. `VACUUM FULL` — только в maintenance window, только если bloat > 50%.

---

## Итого: 10-минутный workflow

```
1. Замерить: \timing on + запрос.  (1 мин)
2. EXPLAIN (ANALYZE, BUFFERS).     (1 мин)
3. Найти «дорогой» оператор.       (2 мин)
4. Проанализировать:
   - Seq Scan? → индекс.
   - Sort on Disk? → work_mem / LIMIT.
   - Nested Loop × N? → Hash Join / переписать.
   - Buffers read? → bloat / VACUUM.
   - Lock? → pg_stat_activity.
5. Применить: CREATE INDEX / SET work_mem / REWRITE.
6. EXPLAIN ANALYZE ещё раз.        (1 мин)
7. Замерить: \timing on + запрос.  (1 мин)
8. Документировать:
   - Почему медленный был.
   - Что сделано.
   - Какой индекс / настройка.
   (2 мин)
```

**80% медленных запросов лечатся на шагах 3–5.** Остальные 20% — уже «интересные»: bloat, параллельные workload, архитектурные (N+1, denormalization). Но это другие разговоры.

> ⏱ 63.8s · prompt 66962 tok · output 7634 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: что это, как определять границы, пример, типовая ошибка. Это продолжение длинной беседы о PHP/Symfony/Doctrine/DDD. Я уже давал пример с Invoice → LineItem в предыдущем ответе про Doctrine. Нужно не повторять тот же пример, а дать новый или углубить.

Ключевые аспекты, которые нужно раскрыть:
1. Что такое агрегат — не «сущность с коллекцией», а «граница согласованности»
2. Как определять границы — это не техническое решение, а доменное
3. Пример — лучше взять новый, не Invoice. Может быть Order с Payment, или Account с Transactions, или Project с Tasks
4. Типовая ошибка — самая частая: «агрегат на всё» (God Aggregate) или наоборот «мелкие агрегаты» которые не защищают инварианты

Давайте подумаю о хорошем примере. 

Варианты:
- Bank Account + Transactions + Transfers. Агрегат = Account. Транзакция — внутри. Transfer — вне (отдельный агрегат).
- Order + Payment + Shipment. Агрегат = Order. Payment — отдельный агрегат (разная команда, разные инварианты). Shipment — отдельный.
- Project + Task + Comment. Агрегат = Project? Task? Comment — отдельный?
- Employee + Contract + Department. 

Я думаю, лучший пример — это **Bank Account** с **Transfer**, потому что:
- Есть явные инварианты (баланс не может быть отрицательным при withdraw)
- Есть соблазн сделать «один большой агрегат» на все счета + транзакции + переводы
- Есть классическая ошибка: Transfer пытается изменить два Aggregate (sender + receiver) атомарно
- Граница явная: Account — агрегат, Transfer — отдельный агрегат/сущность

Или ещё лучше: **Order** + **Payment** + **Inventory**. Потому что:
- Order — агрегат (items внутри)
- Payment — отдельный агрегат (свои инварианты: idempotency, gateway state)
- Inventory — отдельный агрегат (sвои инварианты: stock count)
- Типовая ошибка: «давайте сделаем один агрегат Order, который содержит Payment и Inventory, чтобы всё было атомарно»

Но я уже использовал Invoice/LineItem в прошлом ответе. Лучше взять что-то другое.

Возьму **Bank Account** как основной пример, потому что:
1. Инварианты явные и простые для понимания
2. Граница агрегата неочевидна (а что с Transfer? а что с Statement?)
3. Типовая ошибка наглядна

Структура ответа:
1. Определение агрегата — не «сущность + коллекция», а «граница согласованности»
2. Три вопроса для определения границ
3. Пример: Bank Account
4. Что ВНУТРИ агрегата, что СНАРУЖИ, почему
5. Типовая ошибка: God Aggregate / «один агрегат на всё»
6. Как это выглядит в коде (кратко, не повторять Invoice)

Важно: не повторять Invoice-пример из прошлого ответа. Дать новый.

</details>

**Кандидат:**

# Агрегат в DDD: граница согласованности

## Определение

Агрегат — это **граница, внутри которой инварианты домена всегда истинны**. Не «сущность с коллекцией». Не «таблица с дочерними». А ответ на вопрос: **«какие правила должны выполняться одновременно, и никто не может нарушить их в середине операции?»**

```
Агрегат = корень + вложенные сущности + инварианты,
          которые должны держатьсь атомарно.

Корень — единственная точка входа.
Внешний мир обращается ТОЛЬКО к корню.
Вложенные сущности не имеют собственного идентификатора
для внешнего мира (или имеют, но не доступны напрямую).
```

Ключевое слово — **атомарно**. Если два правила должны держатьсь одновременно, и нарушение одного без другого — невалидное состояние — они принадлежат к **одному** агрегату. Если могут нарушаться независимо — **разные** агрегаты.

---

## Три вопроса для определения границ

Перед тем как нарисовать границу, отвечаю на три вопроса. Не «как устроена БД», не «какие таблицы». А:

### 1. Какие инварианты не могут нарушиться в середине операции?

```
«Баланс счёта не может стать отрицательным при withdraw.»
→ Баланс и операция withdraw — в ОДНОМ агрегате.
  Потому что между SELECT balance и UPDATE balance
  кто-то может сделать другой withdraw.
  Инвариант: balance - amount >= 0.
  Должен держаться атомарно.
```

```
«В заказе не может быть двух позиций с одинаковым SKU.»
→ Position и Order — в ОДНОМ агрегате.
  Потому что добавление позиции проверяет дубликат.
  Между проверкой и записью кто-то мог добавить тот же SKU.
  Инвариант: SKU уникальны внутри заказа.
```

```
«Платёж не может быть списан дважды.»
→ Idempotency key и Payment — в ОДНОМ агрегате.
  Но: Payment НЕ в агрегате Order.
  Потому что Order может быть создан, а Payment — ещё нет.
  Они живут в разное время, с разными командами.
```

### 2. Кто вызывает изменение?

```
«Менеджер добавляет позицию в заказ.»
→ Команда: AddLineItem(orderId, item)
→ Агрегат: Order. Менеджер знает orderId. Не знает paymentId.

«Платёжный шлюз подтверждает списание.»
→ Команда: ConfirmPayment(paymentId, gatewayRef)
→ Агрегат: Payment. Шлюз знает paymentId. Не знает orderId.
```

Разные команды → разные точки входа → разные агрегаты.

### 3. Можно ли изменить один без другого, и это будет валидно?

```
Order создан, Payment ещё нет.
→ Валидно. Order в статусе «awaiting_payment».
→ Значит, Payment — НЕ часть Order-агрегата.

Order оплачен, Shipment ещё нет.
→ Валидно. Order в статусе «paid», Shipment появится позже.
→ Значит, Shipment — НЕ часть Order-агрегата.

Две позиции в Order.
→ Нельзя удалить одну, не трогая другую.
→ Валидно. (Или не валидно — зависит от правил.)
→ Если можно — позиции независимы.
→ Если нельзя (например, «минимум 2 позиции») —
   правило на уровне Order, а не позиции.
```

---

## Пример: Bank Account

### Домен

```
Сущности:
  Account      — счёт. Баланс, владелец, статус.
  Transaction  — операция: deposit, withdraw, transfer_in, transfer_out.
  Transfer     — перевод между двумя счетами.

Инварианты:
  I1: balance >= 0 (после любой операции)
  I2: transaction.amount > 0
  I3: transaction.type ∈ {deposit, withdraw, transfer_in, transfer_out}
  I4: Transfer ссылается на ровно 2 разных Account
  I5: Sender.balance >= Transfer.amount (на момент списания)
  I6: Receiver.balance += Transfer.amount (на момент зачисления)
  I7: Transfer.idempotencyKey уникален (однократность)
```

### Где граница

**Агрегат 1: Account**

```
Корень: Account
Внутри: Transaction (вложенная, не имеет собственного ID для внешнего мира)

Инварианты внутри:
  I1: balance >= 0
  I2: amount > 0
  I3: type ∈ {...}

Команды (через корень Account):
  account.deposit(amount)
  account.withdraw(amount)
  account.transferOut(toAccount, amount)   ← см. ниже

Запросы:
  account.balance()
  account.transactions(since, until)
```

**Агрегат 2: Transfer**

```
Корень: Transfer
Внутри: (ничего вложенного. Только ссылки на 2 Account по ID)

Инварианты внутри:
  I4: senderId ≠ receiverId
  I7: idempotencyKey уникален

Команды:
  transfer.execute()   ← списывает с sender, зачисляет на receiver
  transfer.cancel()

Запросы:
  transfer.status()
  transfer.senderId()
  transfer.receiverId()
```

### Почему Transfer — отдельный агрегат, а не «часть» Account

```
Если Transfer ВНУТРИ Account:
  → account.sender.transfer(receiverAccount, amount)
  → Но receiverAccount — другой агрегат.
  → Чтобы изменить его баланс — нужно «войти» в чужой агрегат.
  → Нарушение инкапсуляции.
  → Два агрегата меняются одновременно.
  → Если упадёт между списанием и зачислением — деньги «испарятся».

Если Transfer — отдельный агрегат:
  → Transfer.execute() — одна команда.
  → Внутри: sender.withdraw(amount) + receiver.deposit(amount).
  → Оба — в ОДНОЙ транзакции (на уровне приложения, не домена).
  → Доменный инвариант I5+I6 держится на уровне приложения.
  → А I1 (balance >= 0) держится на уровне каждого Account.
```

### Почему Transaction — ВНУТРИ Account, а не отдельный агрегат

```
Если Transaction — отдельный агрегат:
  → transaction.id доступен извне.
  → transaction.amount можно изменить.
  → transaction.type можно изменить.
  → «Записать transaction, но не обновить balance» → нарушение I1.
  → Нет атомарности.

Если Transaction ВНУТРИ Account:
  → account.deposit(100) — одна операция.
  → Внутри: new Transaction(...) + balance += 100.
  → Оба — в одном объекте, одна транзакция.
  → I1 и I2 держатся атомарно.
  → Transaction не имеет публичного ID. Внешний мир знает transactionId
    только для чтения: account.transactions().findBy(id).
```

### Код

```php
// src/Domain/Banking/Account.php — Aggregate Root
final class Account
{
    /** @var list<Transaction> */
    private array $transactions = [];

    private function __construct(
        public readonly string $id,
        public readonly string $ownerId,
        private int            $balanceCents,
        public readonly string $currency,
        public readonly Status $status,
    ) {}

    public static function open(string $ownerId, string $currency): self
    {
        if ($ownerId === '') throw new DomainException('Empty owner');
        return new self(
            id: Uuid::v4(),
            ownerId: $ownerId,
            balanceCents: 0,
            currency: $currency,
            status: Status::Active,
        );
    }

    // ── Команды (через корень) ────────────────────────────────────

    public function deposit(int $amountCents, string $reference = ''): Transaction
    {
        $this->assertActive();
        $tx = Transaction::deposit($amountCents, $reference, $this->currency);
        $this->balanceCents += $amountCents;
        $this->transactions[] = $tx;
        return $tx;
    }

    public function withdraw(int $amountCents, string $reference = ''): Transaction
    {
        $this->assertActive();
        if ($amountCents > $this->balanceCents) {
            throw new InsufficientFundsException(
                required: $amountCents,
                available: $this->balanceCents,
            );
        }
        $tx = Transaction::withdraw($amountCents, $reference, $this->currency);
        $this->balanceCents -= $amountCents;
        $this->transactions[] = $tx;
        return $tx;
    }

    public function close(): void
    {
        if ($this->balanceCents !== 0) {
            throw new DomainException(
                'Cannot close account with non-zero balance',
                code: 'ACCOUNT_NON_ZERO_BALANCE',
            );
        }
        $this->status = Status::Closed;
    }

    // ── Запросы ───────────────────────────────────────────────────

    public function balanceCents(): int { return $this->balanceCents; }

    /** @return list<Transaction> */
    public function transactions(): array { return $this->transactions; }

    // ── Private ───────────────────────────────────────────────────

    private function assertActive(): void
    {
        if ($this->status !== Status::Active) {
            throw new DomainException(
                "Account is {$this->status->value}",
                code: 'ACCOUNT_INACTIVE',
            );
        }
    }
}
```

```php
// src/Domain/Banking/Transaction.php — вложенная сущность
final readonly class Transaction
{
    public function __construct(
        public readonly string $id,
        public readonly Type   $type,
        public readonly int    $amountCents,
        public readonly string $currency,
        public readonly string $reference,
        public readonly \DateTimeImmutable $at,
    ) {}

    public static function deposit(int $amount, string $ref, string $cur): self
    {
        return self::make(Type::Deposit, $amount, $ref, $cur);
    }

    public static function withdraw(int $amount, string $ref, string $cur): self
    {
        return self::make(Type::Withdraw, $amount, $ref, $cur);
    }

    private static function make(Type $t, int $amount, string $ref, string $cur): self
    {
        if ($amount <= 0) {
            throw new DomainException('Amount must be positive', code: 'TX_NON_POSITIVE');
        }
        return new self(
            id: Uuid::v4(),
            type: $t,
            amountCents: $amount,
            currency: $cur,
            reference: $ref,
            at: new \DateTimeImmutable(),
        );
    }
}
```

```php
// src/Domain/Banking/Transfer.php — отдельный агрегат
final class Transfer
{
    private function __construct(
        public readonly string $id,
        public readonly string $senderAccountId,
        public readonly string $receiverAccountId,
        public readonly int    $amountCents,
        public readonly string $currency,
        public readonly string $idempotencyKey,
        public Status          $status,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        string $senderId,
        string $receiverId,
        int    $amountCents,
        string $currency,
        string $idempotencyKey,
    ): self {
        if ($senderId === $receiverId) {
            throw new DomainException(
                'Cannot transfer to self',
                code: 'TRANSFER_SELF',
            );
        }
        if ($amountCents <= 0) {
            throw new DomainException('Amount must be positive', code: 'TRANSFER_NON_POSITIVE');
        }
        if ($idempotencyKey === '') {
            throw new DomainException('Idempotency key required', code: 'TRANSFER_NO_IDEM');
        }

        return new self(
            id: Uuid::v4(),
            senderAccountId: $senderId,
            receiverAccountId: $receiverId,
            amountCents: $amountCents,
            currency: $currency,
            idempotencyKey: $idempotencyKey,
            status: Status::Pending,
            createdAt: new \DateTimeImmutable(),
        );
    }

    // ── Команда: выполнить перевод ────────────────────────────────
    // Вызывается TransferService (application layer),
    // который загружает ОБА Account и вызывает withdraw/deposit.
    // Transfer сам НЕ знает про Account.

    public function markExecuted(): void
    {
        if ($this->status !== Status::Pending) {
            throw new DomainException('Already executed', code: 'TRANSFER_DONE');
        }
        $this->status = Status::Executed;
    }

    public function markFailed(string $reason): void
    {
        $this->status = Status::Failed;
        $this->failureReason = $reason;
    }
}
```

```php
// src/Application/Banking/TransferService.php
final class TransferService
{
    public function __construct(
        private AccountRepository  $accounts,
        private TransferRepository $transfers,
        private EntityManagerInterface $em,
    ) {}

    public function execute(string $transferId): void
    {
        $transfer = $this->transfers->findById($transferId);

        $this->em->beginTransaction();
        try {
            // Загружаем ОБА агрегата в ОДИН EntityManager.
            // Это допустимо: оба — разные агрегаты,
            // но операция — одна транзакция.
            $sender   = $this->accounts->findById($transfer->senderAccountId);
            $receiver = $this->accounts->findById($transfer->receiverAccountId);

            // Инвариант I5: sender.balance >= amount
            // Проверка ВНУТРИ Account::withdraw()
            $sender->withdraw($transfer->amountCents, "transfer:{$transfer->id}");

            // Инвариант I6: receiver.balance += amount
            $receiver->deposit($transfer->amountCents, "transfer:{$transfer->id}");

            $transfer->markExecuted();

            $this->accounts->save($sender);
            $this->accounts->save($receiver);
            $this->transfers->save($transfer);

            $this->em->flush();
            $this->em->commit();
        } catch (InsufficientFundsException $e) {
            $this->em->rollBack();
            $transfer->markFailed('Insufficient funds');
            $this->transfers->save($transfer);
            $this->em->flush();
            $this->em->commit();
            throw $e;
        } catch (\Throwable $e) {
            $this->em->rollBack();
            $transfer->markFailed($e->getMessage());
            $this->transfers->save($transfer);
            $this->em->flush();
            $this->em->commit();
            throw $e;
        }
    }
}
```

### Схема

```
┌─────────────────────────────────────────────────────────┐
│  Агрегат: Account                                       │
│                                                        │
│  Корень: Account                                        │
│  Вложенные: Transaction (list)                         │
│  Инварианты: balance >= 0, amount > 0, type ∈ {...}     │
│  Команды: deposit(), withdraw(), close()               │
│  Запросы: balanceCents(), transactions()               │
│                                                        │
│  ВНЕШНИЙ МИР обращается ТОЛЬКО к Account.              │
│  Transaction — только через account.transactions().     │
│  Никогда не: $this->em->find(Transaction::class, $id)  │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Агрегат: Transfer                                      │
│                                                        │
│  Корень: Transfer                                       │
│  Вложенные: (нет)                                       │
│  Инварианты: sender ≠ receiver, idempotencyKey unique  │
│  Команды: markExecuted(), markFailed()                 │
│  Запросы: status(), senderAccountId(), ...             │
│                                                        │
│  Ссылки: senderAccountId (string), receiverAccountId   │
│  Это НЕ навигация. Это ID. Transfer не «знает»         │
│  Account. Только его ID.                               │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Application: TransferService                           │
│                                                        │
│  Загружает 2 Account + 1 Transfer.                     │
│  Вызывает команды на каждом.                            │
│  Одна транзакция.                                       │
│  Единственное место, где агрегаты «встречаются».        │
└─────────────────────────────────────────────────────────┘
```

---

## Типовая ошибка: God Aggregate

### Как это выглядит

```php
// ❌ «Давайте сделаем один большой агрегат, чтобы всё было атомарно»
final class BankingSystem  // ← «агрегат» на весь домен
{
    /** @var list<Account> */
    private array $accounts;

    /** @var list<Transfer> */
    private array $transfers;

    /** @var list<Statement> */
    private array $statements;

    public function __construct(
        EntityManager $em,
    ) {
        // Загружаем ВСЁ
        $this->accounts  = $em->getRepository(Account::class)->findAll();
        $this->transfers = $em->getRepository(Transfer::class)->findAll();
    }

    public function transfer(string $fromId, string $toId, int $amount): void
    {
        $from = $this->findById($this->accounts, $fromId);
        $to   = $this->findById($this->accounts, $toId);

        if ($from->balance < $amount) throw new \Exception('No funds');

        $from->balance -= $amount;
        $to->balance   += $amount;

        // И ещё 40 строк «while we're at it»:
        $this->updateStatement($from, $amount);
        $this->updateStatement($to, $amount);
        $this->recalculateMonthlyReport($from);
        $this->recalculateMonthlyReport($to);
        $this->notifyRiskEngine($from, $to, $amount);
        $this->updateAnalytics(...);
        $this->updateDashboardCache(...);
    }
}
```

### Почему это плохо

| Проблема | Объяснение |
|---|---|
| **Память** | `findAll()` на accounts — 100 000 счетов в RAM. Каждый request. |
| **Атомарность ложная** | `recalculateMonthlyReport()` может занять 2 сек. Транзакция открыта 2 сек. Deadlock. |
| **Параллельность** | Два `transfer()` одновременно → оба загружают `findAll()` → оба пишут → потеря обновления. |
| **Тестируемость** | Чтобы тестнуть `transfer()` — нужен `EntityManager` с 100k записей. |
| **Расширяемость** | Добавить `FX conversion` → править `BankingSystem`. Добавить `compliance check` → править `BankingSystem`. Один файл, 2000 строк. |
| **Инкапсуляция** | `$this->accounts` — публично. Любой код может `$system->accounts[0]->balance = 999999999`. |
| **Нарушение I1** | `withdraw()` не проверяет баланс, потому что «проверяет `transfer()`». Но `deposit()` не проходит через `transfer()`. Инвариант держится в 5 местах. |

### Правильно

```php
// ✅ Три агрегата, один сервис, одна транзакция

Account::withdraw(amount)    // проверяет balance >= 0. Самостоятельно.
Account::deposit(amount)     // добавляет. Самостоятельно.
Transfer::markExecuted()     // меняет статус. Самостоятельно.

TransferService::execute()   // загружает 2 Account + 1 Transfer.
                             // Вызывает 3 команды.
                             // Одна транзакция.
                             // Не знает про statements, reports, risk.
```

Критерий: **каждый агрегат защищает СВОИ инварианты сам, без помощи других**. `Account` не спрашивает `Transfer`, сколько списать. `Account` знает: «мне сказали withdraw 500 — я проверяю свой баланс».

### Когда God Aggregate оправдан

Практически никогда. Но есть один случай: **сценарий, где инвариант по определению spans 2+ сущности, и нет способа выразить его иначе**.

Пример: «в департаменте не может быть двух сотрудников с одинаковым employee_id». Это инвариант на уровне Department, а не Employee. Тогда Department — агрегат, Employee — вложенный. Но это не «все сотрудники компании», а «один департамент».

---

## Вторая типовая ошибка: «мелкие агрегаты»

### Как это выглядит

```php
// ❌ Каждая сущность — свой агрегат
final class Account {
    public function __construct(
        public int $balanceCents,  // ← публичное свойство!
    ) {}
    // Нет withdraw(). Нет deposit().
    // Внешний код: $account->balanceCents -= 500;
}

final class Transaction {
    public function __construct(
        public int $amountCents,  // ← публичное!
        public int $accountId,
    ) {}
    // Нет валидации amount > 0.
}

// Сервис:
function withdraw(Account $acc, int $amount): Transaction {
    $acc->balanceCents -= $amount;  // ← может стать отрицательным
    $tx = new Transaction($amount, $acc->id);
    // amount может быть -500. Никто не проверяет.
    return $tx;
}
```

### Почему это плохо

- Инвариант I1 (`balance >= 0`) не защищён никем. Любой код может `$account->balanceCents = -999`.
- Инвариант I2 (`amount > 0`) не защищён.
- Нет атомарности: между «списали» и «записали transaction» — кто-то может изменить баланс.
- Агрегата нет. Есть «таблицы».

### Правило

**Если инвариант может быть нарушен через прямое изменение свойства — свойства должны быть приватными, а изменение — только через метод на корне агрегата.**

```php
// ✅
final class Account {
    private int $balanceCents;  // приватное

    public function withdraw(int $amount): Transaction {
        if ($amount > $this->balanceCents) throw new InsufficientFundsException(...);
        $this->balanceCents -= $amount;  // валидировано
        $tx = Transaction::withdraw($amount, ...);
        $this->transactions[] = $tx;
        return $tx;
    }

    public function balanceCents(): int { return $this->balanceCents; }  // read-only
}
```

---

## Как я определяю границы на практике

```
1. Выписать все инварианты домена.
   «Баланс не может быть отрицательным.»
   «Платёж не может быть списан дважды.»
   «В заказе минимум 1 позиция.»

2. Для каждого инварианта: «какие сущности должны
   измениться ОДНОВРЕМЕННО, чтобы он держался?»

   I1 (balance >= 0):
     → Account.balance + Account.transactions
     → Одна сущность. Один агрегат.

   I7 (idempotency):
     → Transfer.idempotencyKey
     → Одна сущность. Один агрегат.

   I5+I6 (sender списан, receiver зачислен):
     → Account A + Account B
     → ДВЕ сущности. ДВА агрегата.
     → Атомарность обеспечивается на уровне приложения (транзакция).
     → НЕ на уровне домена.

3. Если инвариант «span'ит» 2+ сущности —
   это НЕ один агрегат. Это команда (service),
   которая загружает оба агрегата и вызывает
   команды на каждом в одной транзакции.

4. Проверить: «есть ли сущность, которая
   существует ТОЛЬКО внутри другого?»
   → Transaction без Account не имеет смысла.
   → Вложенная сущность. Не агрегат.

5. Проверить: «есть ли сущность, которую
   внешний мир создаёт/изменяет независимо?»
   → Transfer создаётся независимо от Account.
   → Отдельный агрегат.
```

**Главный тест:** если я могу нарушить инвариант, изменив одно свойство без вызова метода на корне — граница агрегата неправильная.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

# Конкурентные изменения агрегата: что происходит и как проектировать

## Что реально произойдёт без защиты

Сценарий: два процесса одновременно выводят средства с одного счёта.

```
Bаланс: 1000 центов.
Процесс A: withdraw(600)
Процесс B: withdraw(600)
Ожидаемый результат: один проходит, второй — InsufficientFundsException.
```

### Без защиты: lost update

```
t=0  A: SELECT balance FROM account WHERE id = 1  → 1000
t=1  B: SELECT balance FROM account WHERE id = 1  → 1000
t=2  A: валидация: 1000 >= 600 ✅
t=3  B: валидация: 1000 >= 600 ✅
t=4  A: UPDATE account SET balance = 400 WHERE id = 1
t=5  B: UPDATE account SET balance = 400 WHERE id = 1
t=6  A: commit
t=7  B: commit

Результат: balance = 400.
Списано: 1200. Осталось: 400.
Фактически: 1600 «испарилось». Инвариант I1 (balance >= 0) — формально держится,
но деньги потеряны.
```

### Хуже: нарушение инварианта

```
t=0  A: SELECT balance → 500
t=1  B: SELECT balance → 500
t=2  A: withdraw(400) → 500 >= 400 ✅
t=3  B: withdraw(400) → 500 >= 400 ✅
t=4  A: UPDATE balance = 100
t=5  B: UPDATE balance = 100
t=6  commit A, commit B

Результат: balance = 100.
Списано: 800. Но 500 - 800 = -300.
Баланс ДОЛЖЕН был стать -300. Стал 100.
Инвариант «баланс отражает все операции» нарушен.
```

### В Doctrine: конкретный механизм

```php
// Account.php
final class Account
{
    private int $balanceCents;

    public function withdraw(int $amount): Transaction
    {
        if ($amount > $this->balanceCents) {
            throw new InsufficientFundsException(...);
        }
        $this->balanceCents -= $amount;
        // ...
    }
}
```

```php
// TransferService.php
$account = $this->accounts->findById(1);  // SELECT, hydrate
$account->withdraw(600);                   // mutate в памяти
$this->accounts->save($account);           // persist (не flush)
$this->em->flush();                        // UPDATE balance = 400
$this->em->commit();
```

Doctrine делает `UPDATE account SET balance = ? WHERE id = ?`. **Без** `WHERE version = ?`. Два процесса:

```sql
-- A:
UPDATE account SET balance = 400 WHERE id = 1;  -- affected rows: 1

-- B:
UPDATE account SET balance = 400 WHERE id = 1;  -- affected rows: 1
```

Оба «успешны». Ни один не знает, что другой уже записал.

---

## Три механизма защиты

### 1. Optimistic Locking (version column)

**Идея:** каждый агрегат несёт «версию». UPDATE содержит `WHERE version = N`. Если кто-то успел раньше — `affected rows = 0` → конфликт.

```sql
ALTER TABLE account
    ADD COLUMN version integer NOT NULL DEFAULT 0;

ALTER TABLE account
    ADD CONSTRAINT account_version_nonneg
    CHECK (version >= 0);
```

```php
// Entity:
#[ORM\Entity]
class AccountEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $balanceCents;

    #[ORM\Version]  // ← Doctrine сам генерирует WHERE version = ? и UPDATE version = version + 1
    private int $version;

    public function getVersion(): int { return $this->version; }
}
```

```php
// При flush():
// UPDATE account SET balance = 400, version = 2 WHERE id = 1 AND version = 1;
//
// Если B успел: version уже = 2.
// UPDATE ... WHERE version = 1 → 0 rows affected.
// Doctrine: OptimisticLockException.
```

### Что происходит при конфликте

```
t=0  A: SELECT → balance=1000, version=1
t=1  B: SELECT → balance=1000, version=1
t=2  A: UPDATE SET balance=400, version=2 WHERE id=1 AND version=1 → 1 row ✅
t=3  A: commit
t=4  B: UPDATE SET balance=400, version=2 WHERE id=1 AND version=1 → 0 rows ❌
t=5  B: OptimisticLockException
```

**Критично:** `version` в `UPDATE` — это **то, что было на момент SELECT**. Не «текущее в БД». Поэтому если B сделал SELECT до A commit'а — его `version = 1`, а в БД уже `version = 2`. Конфликт обнаружен.

### Проектирование вокруг optimistic locking

```php
// TransferService.php
final class TransferService
{
    public function execute(string $transferId): void
    {
        $transfer = $this->transfers->findById($transferId);

        $this->em->beginTransaction();
        try {
            $sender   = $this->accounts->findById($transfer->senderAccountId);
            $receiver = $this->accounts->findById($transfer->receiverAccountId);

            $sender->withdraw($transfer->amountCents);
            $receiver->deposit($transfer->amountCents);
            $transfer->markExecuted();

            $this->accounts->save($sender);
            $this->accounts->save($receiver);
            $this->transfers->save($transfer);

            $this->em->flush();   // ← может бросить OptimisticLockException
            $this->em->commit();

        } catch (OptimisticLockException $e) {
            $this->em->rollBack();
            // ← Что делать? (обсуждаю ниже)
            throw new ConcurrencyConflictException(
                'Account was modified concurrently. Please retry.',
                previous: $e,
            );
        } catch (InsufficientFundsException $e) {
            $this->em->rollBack();
            $transfer->markFailed('Insufficient funds');
            $this->transfers->save($transfer);
            $this->em->flush();
            $this->em->commit();
            throw $e;
        }
    }
}
```

### Retry-логика

```php
// В контроллере / CLI / worker:
final class TransferController
{
    public function execute(Request $req): JsonResponse
    {
        $maxRetries = 3;
        $delay = 50;  // ms

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $this->transferService->execute($req->get('transfer_id'));
                return $this->json(['status' => 'executed']);
            } catch (ConcurrencyConflictException $e) {
                if ($attempt === $maxRetries - 1) {
                    // Истощили retries. 409 Conflict.
                    return $this->json(
                        ['error' => 'Conflict', 'message' => 'Please retry.'],
                        409,
                    );
                }
                // Экспоненциальный backoff + jitter
                usleep($delay * (2 ** $attempt) * 1000 + random_int(0, 50000));
            } catch (InsufficientFundsException $e) {
                // Это НЕ конфликт. Это бизнес-ошибка. Не ретраим.
                return $this->json(['error' => 'Insufficient funds'], 422);
            }
        }
    }
}
```

**Почему 3 ретрая достаточно:** конфликт — это «кто-то другой изменил тот же агрегат за последние 1–5 секунд». Вероятность тройного конфликта — мала. После 3 попыток — уже не «конкурентное изменение», а «hot spot». Нужен другой подход (очередь, блокировка).

### Trade-offs optimistic locking

| Плюс | Минус |
|---|---|
| Нет блокировок. Не «зависает». | Конфликт обнаружен ПОСЛЕ выполнения. Работа потрачена впустую. |
| Высокий throughput. | При high contention (100 req/s на один счёт) — много ретраев. |
| Простота: одна колонка, одна строка в UPDATE. | Клиент должен обрабатывать `409 Conflict`. |
| Нет deadlocks. | Если бизнес-операция дорогая (вызов внешнего API) — повтор = повторный вызов. |
| Работает с read-replicas (SELECT на реплике, UPDATE на primary). | |

**Когда подходит:** low–medium contention (1–10 одновременных операций на один агрегат). API, веб-приложения, большинство CRUD.

---

### 2. Pessimistic Locking (SELECT FOR UPDATE)

**Идея:** заблокировать строку на время транзакции. Второй процесс **ждёт** (или получает ошибку).

```sql
SELECT * FROM account WHERE id = 1 FOR UPDATE;
-- Блокировка: exclusive row lock.
-- Второй процесс: SELECT ... FOR UPDATE → блокируется до commit/rollback первого.
```

```php
// Репозиторий:
public function findByIdLocked(int $id): AccountEntity
{
    return $this->em->createQueryBuilder()
        ->select('a')
        ->from(AccountEntity::class, 'a')
        ->where('a.id = :id')
        ->setParameter('id', $id)
        ->setLockMode(LockMode::OPTIMISTIC)  // ← нет, это optimistic
        // Правильно:
        ->setLockMode(LockMode::PESSIMISTIC_WRITE)  // ← SELECT FOR UPDATE
        ->getQuery()
        ->getOneOrNullResult();
}
```

```sql
-- Что генерирует Doctrine:
SELECT a.* FROM account a WHERE a.id = 1 FOR UPDATE;
```

### Сценарий

```
t=0  A: SELECT ... FOR UPDATE → блокирует строку. balance=1000.
t=1  B: SELECT ... FOR UPDATE → БЛОКИРОВАН. Ждёт.
t=2  A: UPDATE balance = 400.
t=3  A: commit. → блокировка снята.
t=4  B: получает строку. balance=400.
t=5  B: withdraw(600) → 400 < 600 → InsufficientFundsException. ✅
```

**B не делает лишней работы.** Он узнаёт «баланс 400» и сразу бросает. Не «выполнил, а потом понял, что конфликт».

### Trade-offs pessimistic locking

| Плюс | Минус |
|---|---|
| Конфликт предотвращён, а не обнаружен. | B **ждёт**. Если A транзакция 2 сек — B простаивает 2 сек. |
| Нет ретраев. | Deadlock: A ждёт B, B ждёт A. (При 2+ агрегатах.) |
| Высокая корректность при high contention. | Connection pool: N заблокированных = N заняты connections. |
| | Если A упадёт (crash, network) → блокировка до `lock_timeout`. |
| | Не масштабируется: 1000 concurrent на один счёт → 1000 в очереди. |

**Когда подходит:** high contention (100+ req/s на один агрегат). Банковские операции с «горячими» счетами. Сценарии, где retry дорог (внешний API, платёж).

### Deadlock при 2+ агрегатах

```
Transfer: sender → receiver

A: transfer(from=1, to=2)
B: transfer(from=2, to=1)

t=0  A: SELECT account 1 FOR UPDATE  ← блокирует 1
t=1  B: SELECT account 2 FOR UPDATE  ← блокирует 2
t=2  A: SELECT account 2 FOR UPDATE  ← ждёт B (B держит 2)
t=3  B: SELECT account 1 FOR UPDATE  ← ждёт A (A держит 1)
t=4  DEADLOCK.
```

PostgreSQL:
```
ERROR:  deadlock detected
DETAIL:  Process 12345 waits for ShareLock on transaction 67890; blocked by process 54321.
         Process 54321 waits for ShareLock on transaction 12345; blocked by process 12345.
```

Один из процессов — `ERROR`. Другой — продолжает.

**Предотвращение:**

```php
// Всегда блокировать в ОДНОМ порядке: по ID.
$first  = min($senderId, $receiverId);
$second = max($senderId, $receiverId);

$account1 = $this->accounts->findByIdLocked($first);
$account2 = $this->accounts->findByIdLocked($second);

// Теперь:
if ($first === $senderId) {
    $sender = $account1; $receiver = $account2;
} else {
    $sender = $account2; $receiver = $account1;
}
```

Оба процесса блокируют `min` первым, `max` вторым. Deadlock невозможен.

**Альтернатива:** `lock_timeout`:

```sql
SET lock_timeout = '5s';  -- на session
SELECT ... FOR UPDATE;   -- если ждём > 5с → ERROR, не deadlock
```

```php
// В репозитории:
$conn->executeStatement('SET LOCAL lock_timeout = 5000');
// (LOCAL — только для текущей транзакции)
```

---

### 3. Идempotency как часть дизайна

**Идея:** даже если операция выполнится дважды — результат тот же. Конфликт не «ошибка», а «повтор».

```php
// TransferService.php
public function execute(string $idempotencyKey): TransferResult
{
    // 1. Проверить: уже выполнен?
    $existing = $this->transfers->findByKey($idempotencyKey);
    if ($existing !== null) {
        if ($existing->status === Status::Executed) {
            return TransferResult::alreadyExecuted($existing);  // идемпотентный ответ
        }
        if ($existing->status === Status::Pending) {
            // Ещё выполняется. Ждём или ретраим.
            throw new TransferInProgressException($existing->id);
        }
    }

    // 2. Создать/загрузить.
    $transfer = $this->transfers->createOrFind($idempotencyKey);

    // 3. Выполнить (с optimistic/pessimistic locking).
    // ...
}
```

```sql
-- UNIQUE на idempotency_key:
ALTER TABLE transfer
    ADD CONSTRAINT transfer_idem_key_unique
    UNIQUE (idempotency_key);
```

**Что это даёт:**

```
Клиент: POST /transfer {key: "abc-123", from: 1, to: 2, amount: 500}
Сервер: execute("abc-123")
Клиент: POST /transfer {key: "abc-123", from: 1, to: 2, amount: 500}  ← retry по сети
Сервер: findByKey("abc-123") → уже Executed → 200 + результат
```

Без идемпотентности: второй POST → двойное списание. С идемпотентностью: второй POST → «уже сделано».

**Это не замена locking'у.** Это дополнение. Locking защищает «одновременное изменение». Idempotency защищает «повторное выполнение». Оба нужны.

---

## Как проектирую: слои ответственности

```
┌─────────────────────────────────────────────────────────────────┐
│  Domain (Account)                                               │
│                                                                │
│  • Инвариант: balance >= 0                                      │
│  • Команда: withdraw(amount) — проверяет balance ДО изменения   │
│  • НЕ знает о конкурентности. Не знает о version.               │
│  • «Я проверяю свой баланс и меняю его. Это моя зона.»          │
├─────────────────────────────────────────────────────────────────┤
│  Persistence (AccountEntity)                                    │
│                                                                │
│  • #[ORM\Version] private int $version;                        │
│  • SELECT FOR UPDATE (при PESSIMISTIC_WRITE)                    │
│  • UPDATE ... WHERE version = ? (при optimistic)               │
│  • Переводит OptimisticLockException → доменное?               │
│    Нет. Бросает как есть. Application решает.                   │
├─────────────────────────────────────────────────────────────────┤
│  Application (TransferService)                                  │
│                                                                │
│  • Загружает агрегаты (locked или unlocked)                    │
│  • Вызывает команды на каждом                                   │
│  • ОДНА транзакция                                               │
│  • Поймал OptimisticLockException → rollBack + throw           │
│  • НЕ ретраит сам. Ретраит — responsibility вызывающего.       │
│  • При pessimistic: порядок блокировок (min → max)             │
│  • lock_timeout = 5s                                            │
├─────────────────────────────────────────────────────────────────┤
│  Presentation (Controller / Worker)                             │
│                                                                │
│  • Retry-логика: 3 попытки, exponential backoff                │
│  • 409 Conflict при исчерпании retries                         │
│  • Idempotency key из заголовка / тела запроса                  │
│  • НЕ знает о version, FOR UPDATE, locking                     │
│  • Знает: «операция может конфликтовать. Ретраю.»              │
└─────────────────────────────────────────────────────────────────┘
```

### Кто что знает

| Уровень | Знает | Не знает |
|---|---|---|
| Domain | Инварианты. Команды. | Version, FOR UPDATE, retry. |
| Persistence | `#[ORM\Version]`, LockMode. | Бизнес-логику. Retry. |
| Application | Транзакции, порядок блокировок, lock_timeout. | HTTP, retry, backoff. |
| Presentation | Retry, idempotency, 409. | SQL, version, FOR UPDATE. |

---

## Конкретный код: полный сценарий

### Entity

```php
#[ORM\Entity]
#[ORM\Table(name: 'account')]
class AccountEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $balanceCents = 0;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'KZT';

    #[ORM\Version]  // ← optimistic lock
    private int $version = 0;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = 'active';

    // геттеры/сеттеры...

    public function getVersion(): int { return $this->version; }
}
```

### Репозиторий

```php
final class AccountRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    // Без блокировки (optimistic):
    public function findById(int $id): AccountEntity
    {
        $entity = $this->em->getRepository(AccountEntity::class)->find($id);
        if ($entity === null) throw new AccountNotFoundException($id);
        return $entity;
    }

    // С блокировкой (pessimistic):
    public function findByIdLocked(int $id): AccountEntity
    {
        $entity = $this->em->createQueryBuilder()
            ->select('a')
            ->from(AccountEntity::class, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity === null) throw new AccountNotFoundException($id);
        return $entity;
    }

    public function save(AccountEntity $entity): void
    {
        $this->em->persist($entity);
        // НЕ flush(). Flush — в Application.
    }
}
```

### Application: optimistic (low contention)

```php
final class TransferService
{
    public function __construct(
        private AccountRepository $accounts,
        private TransferRepository $transfers,
        private EntityManagerInterface $em,
    ) {}

    public function execute(string $idempotencyKey, int $senderId, int $receiverId, int $amountCents): TransferEntity
    {
        // Idempotency check (до транзакции):
        $existing = $this->transfers->findByIdempotencyKey($idempotencyKey);
        if ($existing !== null && $existing->getStatus() === 'executed') {
            return $existing;  // уже сделано
        }

        $transfer = $this->transfers->create($idempotencyKey, $senderId, $receiverId, $amountCents);

        $this->em->beginTransaction();
        try {
            $sender   = $this->accounts->findById($senderId);   // без FOR UPDATE
            $receiver = $this->accounts->findById($receiverId);

            // Доменные команды:
            $this->applyWithdraw($sender, $amountCents);
            $this->applyDeposit($receiver, $amountCents);
            $transfer->markExecuted();

            $this->accounts->save($sender);
            $this->accounts->save($receiver);
            $this->transfers->save($transfer);

            $this->em->flush();  // ← UPDATE ... WHERE version = ?
            // Если конфликт → OptimisticLockException → catch ниже
            $this->em->commit();

        } catch (OptimisticLockException $e) {
            $this->em->rollBack();
            $transfer->markFailed('Conflict');
            $this->transfers->save($transfer);
            $this->em->flush();
            $this->em->commit();
            throw $e;  // ← Application бросает. Presentation ретраит.
        } catch (InsufficientFundsException $e) {
            $this->em->rollBack();
            $transfer->markFailed('Insufficient funds');
            $this->transfers->save($transfer);
            $this->em->flush();
            $this->em->commit();
            throw $e;  // ← НЕ конфликт. Не ретраим.
        }

        return $transfer;
    }

    private function applyWithdraw(AccountEntity $account, int $amount): void
    {
        if ($amount > $account->getBalanceCents()) {
            throw new InsufficientFundsException($amount, $account->getBalanceCents());
        }
        $account->setBalanceCents($account->getBalanceCents() - $amount);
    }

    private function applyDeposit(AccountEntity $account, int $amount): void
    {
        $account->setBalanceCents($account->getBalanceCents() + $amount);
    }
}
```

### Application: pessimistic (high contention)

```php
public function executePessimistic(string $idempotencyKey, int $senderId, int $receiverId, int $amountCents): TransferEntity
{
    $existing = $this->transfers->findByIdempotencyKey($idempotencyKey);
    if ($existing !== null && $existing->getStatus() === 'executed') {
        return $existing;
    }

    $transfer = $this->transfers->create($idempotencyKey, $senderId, $receiverId, $amountCents);

    // Порядок блокировок: min → max. Предотвращает deadlock.
    $firstId  = min($senderId, $receiverId);
    $secondId = max($senderId, $receiverId);

    $this->em->beginTransaction();

    try {
        // lock_timeout: если ждём > 5с — ERROR, не deadlock.
        $this->em->getConnection()->executeStatement('SET LOCAL lock_timeout = 5000');

        $account1 = $this->accounts->findByIdLocked($firstId);
        $account2 = $this->accounts->findByIdLocked($secondId);

        [$sender, $receiver] = ($firstId === $senderId)
            ? [$account1, $account2]
            : [$account2, $account1];

        $this->applyWithdraw($sender, $amountCents);
        $this->applyDeposit($receiver, $amountCents);
        $transfer->markExecuted();

        $this->accounts->save($sender);
        $this->accounts->save($receiver);
        $this->transfers->save($transfer);

        $this->em->flush();
        $this->em->commit();

    } catch (LockException | DeadlockDetectedException $e) {
        $this->em->rollBack();
        $transfer->markFailed('Lock timeout');
        $this->transfers->save($transfer);
        $this->em->flush();
        $this->em->commit();
        throw new ConcurrencyConflictException('Lock timeout', previous: $e);
    } catch (InsufficientFundsException $e) {
        $this->em->rollBack();
        $transfer->markFailed('Insufficient funds');
        $this->transfers->save($transfer);
        $this->em->flush();
        $this->em->commit();
        throw $e;
    }

    return $transfer;
}
```

### Presentation: retry

```php
final class TransferController
{
    public function __construct(
        private TransferService $service,
        private LoggerInterface $logger,
    ) {}

    public function execute(Request $req): JsonResponse
    {
        $key    = $req->headers->get('Idempotency-Key') ?? $req->get('key');
        $sender = (int) $req->get('from');
        $receiver = (int) $req->get('to');
        $amount = (int) $req->get('amount');

        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $result = $this->service->execute($key, $sender, $receiver, $amount);
                return $this->json([
                    'id'     => $result->getId(),
                    'status' => $result->getStatus(),
                ]);

            } catch (ConcurrencyConflictException | OptimisticLockException $e) {
                $delay = 50 * (2 ** ($attempt - 1)) + random_int(0, 100);
                $this->logger->info('Transfer conflict, retrying', [
                    'attempt' => $attempt,
                    'key'     => $key,
                    'delay_ms' => $delay,
                ]);
                if ($attempt === $maxRetries) {
                    break;
                }
                usleep($delay * 1000);

            } catch (InsufficientFundsException $e) {
                // НЕ конфликт. Бизнес-ошибка. 422.
                return $this->json([
                    'error'   => 'Insufficient funds',
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        // Истощили retries:
        return $this->json([
            'error'   => 'Conflict',
            'message' => 'Account is busy. Please retry in a few seconds.',
        ], 409);
    }
}
```

---

## Как выбрать: optimistic vs pessimistic

| Критерий | Optimistic | Pessimistic |
|---|---|---|
| Contention (операций/сек на один агрегат) | < 10 | > 10 |
| Стоимость операции | Дешёвая (DB only) | Дорогая (внешний API, платёж) |
| Допустимый latency | 50 мс (retry) | 2 с (ожидание) |
| Connection pool | Не исчерпывается | Исчерпывается (ожидание) |
| Deadlock | Нет | Да (при 2+ агрегатах) |
| Сложность | version column + retry | FOR UPDATE + lock_timeout + порядок |
| Read replicas | SELECT на реплике, UPDATE на primary | Только primary |

**Практическое правило:**

```
Если агрегат «холодный» (1–5 операций/сек):  → optimistic.
Если агрегат «горячий» (50+ операций/сек):   → pessimistic + очередь.
Если агрегат «очень горячий» (1000+/сек):     → очередь + batch.
                                                 Не locking'и, а serialization.
```

---

## Антипаттерны

### 1. `sleep()` «чтобы не конфликтовать»

```php
// ❌
time_sleep(2);  // «ну, чтобы другие успели»
$account->withdraw($amount);
$em->flush();
```

Не решает ничего. Спим 2 сек, а конфликт был бы и на 1 мс. Плюс: 2 сек latency для каждого запроса.

### 2. Валидация «до» и «после» flush

```php
// ❌
if ($account->balance >= $amount) {
    $account->balance -= $amount;
    $em->flush();
    // «А теперь проверим ещё раз?»
    $em->refresh($account);
    if ($account->balance < 0) {
        $em->rollback();
        throw new \Exception('Negative balance!');
    }
}
```

Это не «защита». Это «надежда». Между первым и вторым SELECT кто-то мог изменить баланс. `#[ORM\Version]` — детерминированный механизм. «Проверим после» — нет.

### 3. Публичное свойство + внешний `if`

```php
// ❌
$account->balanceCents;  // публичное
$service:
if ($account->balanceCents >= $amount) {
    $account->balanceCents -= $amount;
}
```

Нарушает инкапсуляцию. Любой код может `$account->balanceCents = -999`. Нет единой точки контроля. `withdraw()` — единственная точка.

### 4. Locking «на всякий случай»

```php
// ❌
$account = $repo->findByIdLocked($id);  // FOR UPDATE
$account->getBalanceCents();            // только чтение!
```

Блокировка на чтение. Другой процесс не может изменить счёт, пока я «смотрю». Для read-only операция — `findById()` без `FOR UPDATE`.

### 5. Retry в Application вместо Presentation

```php
// ❌ В TransferService:
for ($i = 0; $i < 3; $i++) {
    try {
        // ... execute ...
        return $result;
    } catch (OptimisticLockException $e) {
        continue;
    }
}
throw $e;
```

Application не знает «кто вызвал». CLI? Web? Worker? Retry-политика разная. Application бросает. Presentation решает.

---

## Итого

```
Конкурентное изменение агрегата — это НЕ «баг», а свойство системы.
Два процесса. Один ресурс. Физика.

Защита — на 3 уровнях:

1. Domain:   инвариант + команда. «Я проверяю и меняю атомарно.»
2. Persistence: version column / FOR UPDATE. «Я не записываю,
   если данные изменились после моего SELECT.»
3. Application: одна транзакция + порядок блокировок + lock_timeout.
   «Я координирую несколько агрегатов.»

Retry — на 4-м уровне: Presentation. «Я пробую 3 раза.
   Не получилось — 409. Не моя проблема.»

Idempotency — на 0-м уровне: дизайн. «Даже если я вызову дважды —
   результат тот же.»

Никогда: sleep, публичные свойства, retry в сервисе,
        «проверим после flush», locking на чтение.
```

> ⏱ 57.4s · prompt 74355 tok · output 6701 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про доменные события (domain events) в контексте DDD и Symfony/PHP. Это продолжение нашего длинного разговора про DDD, агрегаты, транзакции, Doctrine, Symfony.

Что нужно покрыть:
1. Когда публикуешь доменные события (triggers)
2. Как обрабатываешь (handlers, синхронно/асинхронно)
3. Какие проблемы решает
4. Какие проблемы создаёт
5. Связь с транзакциями БД (самый критичный момент)

Я уже в разговоре использовал события (InvoiceCreatedEvent, OrderPlacedEvent, UserRegisteredEvent), так что я должен быть последовательен.

Ключевые аспекты:

**Когда публикуешь:**
- После изменения состояния агрегата (команда выполнена, инварианты держатся)
- НЕ до изменения (событие — факт, а не намерение)
- НЕ во вложенных сущностях (только корень)
- Post-commit vs in-transaction — критический вопрос

**Как обрабатываешь:**
- Синхронные listener'ы (в той же транзакции)
- Асинхронные (через Messenger / очередь)
- In-process vs out-of-process
- Idempotency обработчиков

**Проблемы, которые решает:**
- Развязка (decoupling)
- Расширяемость (OCP)
- Аудит
- Re-аггрегация

**Проблемы, которые создаёт:**
- "Невидимые" зависимости
- Трудность отладки
- Порядок обработки
- Тестирование
- "Event storm" — 47 событий на одну операцию

**Связь с транзакциями — САМОЕ ВАЖНОЕ:**

Здесь есть фундаментальное различие:
1. Доменное событие как факт в домене (в памяти)
2. Публикация события (dispatch)
3. Обработка события (handler)
4. Транзакция БД

Ключевые проблемы:
- Если обработчик делает HTTP-вызов — он внутри транзакции? Нет.
- Если обработчик пишет в БД — он в той же транзакции? Да, если синхронный.
- Если обработчик упадёт — откат? Да, если в той же транзакции.
- Если событие опубликовано, но commit не случился — событие "потеряно"?
- Outbox pattern: запись события в БД + запись агрегата в ОДНОЙ транзакции. Публикация — асинхронно.

Давайте структурирую ответ:

1. Что такое доменное событие (определение)
2. Когда публикуешь (правила)
3. Как обрабатываешь (синхронно/асинхронно)
4. Связь с транзакциями (самый сложный и важный раздел)
5. Outbox pattern
6. Проблемы, которые решает
7. Проблемы, которые создаёт
8. Антипаттерны
9. Практический пример

Буду использовать примеры из нашего разговора (Invoice, Account, Order).

Язык — русский, как и весь разговор.

</details>

**Кандидат:**

# Доменные события: когда, как, и связь с транзакциями

## Что такое доменное событие

Доменное событие — это **факт, который произошёл в домене**. Не команда, не запрос, не «намерение сделать». Факт.

```
Команда:  «Создай счёт на 5000 тенге»        → намерение
Событие:  СчётСоздан (id, баланс=0, валюта)   → факт
Команда:  «Оплати заказ #42»                  → намерение
Событие:  ЗаказОплачен (id, сумма, дата)       → факт
```

Критическое свойство: **событие уже произошло**. Никто не может его «отменить». Если `OrderPlaced` опубликован — заказ создан. Точка.

Это отличает события от команд:

```
Команду можно отклонить. Событие — нельзя.
Команду можно переисполнить. Событие — переисполнение = нарушение идемпотентности.
Команда — вопрос. Событие — утверждение.
```

---

## Когда публикуешь

### Правило 1: После изменения состояния, до commit'а

```php
// ✅ Правильно: событие — часть изменения
public function place(Cart $cart): Invoice
{
    $invoice = Invoice::create($cart);
    $invoice->assertNotEmpty();

    // Событие — ФАКТ, что invoice создан.
    // Публикуем ДО commit'а, а не после.
    $this->registerDomainEvent(new InvoiceCreatedEvent($invoice));

    $this->em->persist($invoice);
    $this->em->flush();
    $this->em->commit();   // ← commit фиксирует и aggregate, и событие

    return $invoice;
}
```

Почему до commit'а, а не после:

```
// ❌ После commit'а:
$this->em->commit();
$this->events->dispatch(new InvoiceCreatedEvent($invoice));
// Если dispatch упадёт (listener бросил exception) —
// invoice УЖЕ в БД, но событие «потеряно».
// Аналитика не записала. Email не ушёл. Индекс не обновился.
// Причём: событие НЕ В БД. Его не «перепубликовать».

// ✅ До commit'а (outbox):
// Событие записано в БД в той же транзакции, что и aggregate.
// Commit → и aggregate, и событие.
// Rollback → и aggregate, и событие.
// Атомарность.
```

### Правило 2: Только на уровне агрегата

```php
// ✅ Корень агрегата регистрирует событие
final class Invoice
{
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    public function addLineItem(InvoiceLineItem $item): void
    {
        // ... валидация ...
        $this->lineItems[] = $item;
        // НЕ регистрируем событие «позиция добавлена».
        // Это внутренняя деталь. Внешний мир видит InvoiceCreated
        // или InvoiceUpdated — на уровне агрегата.
    }

    public function markAsPaid(string $txId): void
    {
        $this->status = Status::Paid;
        $this->paymentTxId = $txId;
        $this->registerDomainEvent(
            new InvoicePaidEvent($this->id, $this->total(), $txId)
        );
    }
}

// ❌ Вложенная сущность публикует событие
final class InvoiceLineItem
{
    public function setAmount(Money $m): void
    {
        $this->amount = $m;
        // $this->events->dispatch(new LineItemAmountChanged(...));
        // ← Нет. Внешний мир не знает про lineItem.
        // ← Событие — на уровне Invoice.
    }
}
```

### Правило 3: Событие несёт ID, а не объект

```php
// ✅
final readonly class InvoicePaidEvent
{
    public function __construct(
        public readonly int   $invoiceId,   // ← ID, не объект
        public readonly Money $total,       // ← snapshot на момент события
        public readonly string $txId,
        public readonly \DateTimeImmutable $at,
    ) {}
}

// ❌
final readonly class InvoicePaidEvent
{
    public function __construct(
        public readonly Invoice $invoice,  // ← объект. Живая ссылка.
    ) {}
    // Если обработчик изменит $invoice — «испортит» факт.
    // Событие — immutable snapshot.
}
```

### Правило 4: Не публикуй «внутренние» события

```php
// ❌ 47 событий на одну операцию:
class Invoice
{
    public function addLineItem(...) {
        $this->registerEvent(new LineItemAddedEvent(...));
    }
    public function removeLineItem(...) {
        $this->registerEvent(new LineItemRemovedEvent(...));
    }
    public function setCurrency(...) {
        $this->registerEvent(new CurrencyChangedEvent(...));
    }
    // ...
}

// Это не «события». Это «журнал изменений».
// Если нужно — audit log. Но это не доменное событие.
// Доменное событие — то, на что РЕАГИРУЕТ внешний мир.
```

### Правило 5: Не публикуй событие, если нет обработчика

```php
// ❌
$this->registerEvent(new InvoiceDraftedEvent($invoice));
// Никто не подписан. Событие записано в outbox.
// Worker прочитает. Увидит: «InvoiceDrafted».
// Найдёт 0 обработчиков. Пропустит.
// Мусор в очереди.

// ✅ Публикуй только то, на что есть реакция.
// Или: публикуй, но обработчик — «no-op» с комментарием.
// Это сигнал: «это событие существует, но пока никто не реагирует».
```

---

## Как обрабатываешь

### Вариант А: Синхронный in-process handler (в той же транзакции)

```php
// src/Handler/OnInvoicePaid.php
final class OnInvoicePaid
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexer   $search,
    ) {}

    #[AsEventListener(event: InvoicePaidEvent::class)]
    public function __invoke(InvoicePaidEvent $event): void
    {
        $this->mailer->send(
            to:      'billing@company.com',
            subject: "Invoice #{$event->invoiceId} paid",
            body:    "Total: {$event->total}",
        );

        $this->search->upsert('invoice', $event->invoiceId, [
            'status' => 'paid',
            'total'  => $event->total->amountCents,
        ]);
    }
}
```

**Что происходит:**

```
beginTransaction();
  persist(invoice);
  persist(event → outbox table);
  dispatch(InvoicePaidEvent)
    → OnInvoicePaid::invoke()
      → mailer->send()        ← SMTP, 200 мс
      → search->upsert()      ← HTTP, 100 мс
  flush();
commit();
```

**Проблема:** SMTP и HTTP — **внутри транзакции**. Если SMTP упадёт — откат. Invoice не создан. Но SMTP уже «ушёл» (или нет). Нечистое состояние.

**Когда подходит:**
- Обработчик быстрый (< 50 мс).
- Обработчик не делает внешних вызовов.
- Обработчик пишет в ту же БД.
- «Если событие не обработано — операция не состоялась». (Например: «создать invoice + записать audit log» — атомарно.)

### Вариант Б: Асинхронный (через Messenger / очередь)

```php
// src/Handler/OnInvoicePaid.php
final class OnInvoicePaid
{
    public function __construct(
        private MessageBus $bus,
    ) {}

    #[AsEventListener(event: InvoicePaidEvent::class)]
    public function __invoke(InvoicePaidEvent $event): void
    {
        // Не выполняем. Публикуем в очередь.
        $this->bus->dispatch(new ProcessInvoicePaidMessage($event));
    }
}

// src/Messenger/Handler/ProcessInvoicePaidHandler.php
final class ProcessInvoicePaidHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexer   $search,
        private UserRepository  $users,
    ) {}

    #[AsMessageHandler]
    public function __invoke(ProcessInvoicePaidMessage $msg): void
    {
        $event = $msg->event;
        $user  = $this->users->findById($event->invoiceId);

        $this->mailer->send(/* ... */);
        $this->search->upsert(/* ... */);
    }
}
```

**Что происходит:**

```
beginTransaction();
  persist(invoice);
  persist(event → outbox table);
  dispatch(InvoicePaidEvent)
    → OnInvoicePaid::invoke()
      → bus->dispatch(...)   ← записывает в messenger_messages
  flush();
commit();                    // ← invoice + событие + очередь message — в ОДНОЙ транзакции

// ...позже, в worker-процессе:
SELECT FROM messenger_messages WHERE visible = false;
→ ProcessInvoicePaidHandler::invoke()
  → mailer->send()           ← SMTP. 200 мс. Вне транзакции.
  → search->upsert()         ← HTTP. 100 мс. Вне транзакции.
```

**Проблема:** если worker упадёт после `mailer->send()`, но до `search->upsert()` — email ушёл, индекс не обновлён. Нужна идемпотентность.

**Когда подходит:**
- Обработчик делает внешние вызовы (SMTP, HTTP, очередь).
- Обработчик медленный (> 100 мс).
- Несколько обработчиков на одно событие (email, analytics, cache invalidation).
- «Событие должно быть обработано, но не обязательно в этот request».

### Вариант В: Event Sourcing (полный)

```php
// Событие — ЕДИНСТВЕННЫЙ источник правды.
// Состояние агрегата — реконструировано из событий.

class Invoice
{
    private function __construct(
        public readonly int $id,
        private array $events,  // ← события, не состояние
    ) {}

    public function apply(InvoiceCreatedEvent $e): void { /* ... */ }
    public function apply(InvoicePaidEvent $e): void { /* ... */ }
    public function apply(LineItemAddedEvent $e): void { /* ... */ }

    public static function fromEvents(array $events): self
    {
        $inv = new self(0, []);
        foreach ($events as $e) {
            $inv->apply($e);
        }
        return $inv;
    }
}
```

**Когда:** финансовый учёт, аудит, «почему баланс такой?». **Не для 95% проектов.** Сложность: temporal queries, snapshot'ы, migration существующих данных, отладка.

---

## Связь с транзакциями: самый критичный момент

### Проблема: «событие опубликовано, но commit не случился»

```
t=0  BEGIN
t=1  UPDATE invoice SET status = 'paid' WHERE id = 42
t=2  dispatch(InvoicePaidEvent)
     → handler: mailer->send()   ← EMAIL УШЁЛ
     → handler: search->upsert() ← ИНДЕКС ОБНОВЛЁН
t=3  UPDATE analytics SET ...    ← ОШИБКА. БД недоступна.
t=4  ROLLBACK

Результат:
  • invoice в БД: status = 'draft' (откатили)
  • email: отправлен (не откатится)
  • search: обновлён (не откатится)
  • analytics: не записан

Несогласованность. Событие «случилось», но операция «не случилась».
```

### Решение: Outbox Pattern

```
┌─────────────────────────────────────────────────────────────┐
│  Transaction (ОДНА, атомарная)                              │
│                                                            │
│  1. UPDATE invoice SET status = 'paid' WHERE id = 42       │
│  2. INSERT INTO outbox (event_type, payload, status)       │
│     VALUES ('InvoicePaid', '{"id":42,"total":5000}', 'new')│
│                                                            │
│  commit()                                                  │
│  ← ЛИБО оба, ЛИБО ни одного. Атомарно.                     │
└─────────────────────────────────────────────────────────────┘
         │
         ▼  (асинхронно, позже)
┌─────────────────────────────────────────────────────────────┐
│  Outbox Relay (отдельный процесс)                           │
│                                                            │
│  1. SELECT * FROM outbox WHERE status = 'new' FOR UPDATE   │
│  2. UPDATE outbox SET status = 'published' WHERE id = ?    │
│  3. PUBLISH to RabbitMQ / Kafka / Redis                    │
│  4. commit()                                               │
│                                                            │
│  Если шаг 3 упал:                                           │
│    → status остаётся 'new'                                  │
│    → Следующий цикл: повтор                                │
│    → НЕ потеряно                                            │
└─────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│  Consumers (email, search, analytics)                       │
│                                                            │
│  • Идемпотентность: проверяют event_id перед обработкой     │
│  • Retry: 3 попытки + DLQ                                   │
│  • Наблюдаемость: lag, failed, DLQ count                    │
└─────────────────────────────────────────────────────────────┘
```

### Реализация outbox в Symfony/Doctrine

```php
// src/Entity/OutboxEvent.php
#[ORM\Entity]
#[ORM\Table(name: 'outbox')]
class OutboxEvent
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $eventType;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = 'new';  // new → published → failed

    #[ORM\Column(type: 'integer')]
    private int $attempts = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    public function __construct(string $eventType, array $payload)
    {
        $this->eventType = $eventType;
        $this->payload   = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function markPublished(): void
    {
        $this->status = 'published';
        $this->publishedAt = new \DateTimeImmutable();
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
        if ($this->attempts >= 5) {
            $this->status = 'failed';
        }
    }

    // геттеры...
}
```

```php
// src/Service/DomainEventPublisher.php
final class DomainEventPublisher
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBus             $bus,
    ) {}

    /**
     * Вызывается ДО commit'а.
     * Записывает событие в outbox в той же транзакции, что и aggregate.
     */
    public function publish(DomainEvent $event): void
    {
        $outbox = new OutboxEvent(
            eventType: $event::class,
            payload:   $event->toArray(),
        );
        $this->em->persist($outbox);
        // НЕ flush(). Flush — в вызывающем (сервис), в той же транзакции.
    }
}
```

```php
// src/Service/InvoiceService.php
final class InvoiceService
{
    public function __construct(
        private InvoiceRepository   $invoices,
        private DomainEventPublisher $events,
        private EntityManagerInterface $em,
    ) {}

    public function markAsPaid(int $invoiceId, string $txId): void
    {
        $invoice = $this->invoices->findById($invoiceId);
        $invoice->markAsPaid($txId);

        // Событие — часть транзакции:
        $this->events->publish(new InvoicePaidEvent(
            invoiceId: $invoiceId,
            total:     $invoice->total(),
            txId:      $txId,
            at:        new \DateTimeImmutable(),
        ));

        $this->invoices->save($invoice);

        $this->em->flush();   // ← invoice + outbox event
        $this->em->commit();  // ← атомарно
    }
}
```

```php
// src/Command/PublishOutboxCommand.php
// Отдельный процесс (cron / supervisor)
#[AsCommand('outbox:publish')]
final class PublishOutboxCommand extends Command
{
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $em = $this->em;
        $bus = $this->bus;

        while (true) {
            $events = $em->createQueryBuilder()
                ->select('e')
                ->from(OutboxEvent::class, 'e')
                ->where('e.status = :status')
                ->setParameter('status', 'new')
                ->orderBy('e.createdAt', 'ASC')
                ->setMaxResults(100)
                ->getQuery()
                ->getResult();

            if ($events === []) break;

            foreach ($events as $event) {
                $em->beginTransaction();
                try {
                    // «Забираем» событие:
                    $event->markPublished();
                    $em->flush();
                    $em->commit();

                    // Публикуем (внешний вызов, вне транзакции):
                    $bus->dispatch(new OutboxMessage($event));

                    $out->writeln("Published: {$event->getEventType()}");

                } catch (\Throwable $e) {
                    $em->rollBack();
                    $event->incrementAttempts();
                    $em->flush();
                    $out->writeln("Failed: {$event->getEventType()} — {$e->getMessage()}");
                }
            }

            usleep(100000);  // 100 мс пауза
        }

        return Command::SUCCESS;
    }
}
```

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            default: '%env(MESSENGER_TRANSPORT_DSN)%'

        routing:
            'App\Messenger\OutboxMessage': default
```

```php
// src/Messenger/Handler/OutboxMessageHandler.php
// Фактическая маршрутизация: по eventType → конкретный обработчик
final class OutboxMessageHandler
{
    /** @var array<string, callable> */
    private array $handlers;

    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $h) {
            $this->handlers[$h::getEventType()] = $h;
        }
    }

    public function __invoke(OutboxMessage $msg): void
    {
        $eventType = $msg->eventType;

        if (!isset($this->handlers[$eventType])) {
            // Неизвестный тип. Пропускаем.
            return;
        }

        ($this->handlers[$eventType])(new \DomainEvent\GenericEvent(
            $eventType,
            $msg->payload,
        ));
    }
}
```

---

## Проблемы, которые решает

| Проблема | Как решает |
|---|---|
| **Развязка** | `InvoiceService` не знает про email, search, analytics. Публикует факт. Обработчики подписываются. |
| **OCP** | Новый обработчик = новый класс + тег. Ни строчки в существующем. |
| **Аудит** | Outbox — журнал. «Что произошло, когда, кто обработал». |
| **Атомарность** | Aggregate + событие — одна транзакция. Либо оба, либо ни одно. |
| **Надёжность** | Событие в БД. Worker может упасть — событие не потеряно. |
| **Асинхронность** | Ответ клиенту — до обработки. Email, search — позже. |
| **Повторная обработка** | Outbox с `status = 'new'` — идемпотентный retry. |
| **Расширяемость** | 1 событие → 5 обработчиков → 10. Обработчики независимы. |

---

## Проблемы, которые создаёт

| Проблема | Обострение |
|---|---|
| **Невидимые зависимости** | `InvoiceService` не «тянет» `EmailNotifier`. Но `EmailNotifier` зависит от `InvoicePaidEvent`. Если изменишь payload события — сломает 5 обработчиков. Стат-анализер не видит. |
| **Порядок** | События `A` и `B` на один агрегат. Обработчик `A` должен быть раньше `B`. В асинхронном режиме — нет гарантии. |
| **Тестирование** | Unit test: `InvoiceService` публикует событие. Integration test: обработчик реагирует. E2E: всё вместе. Три уровня. |
| **Отладка** | «Почему email не ушёл?» → `InvoiceService` работает. `InvoicePaidEvent` в outbox? → Да. `status = 'published'`? → Да. В RabbitMQ? → Да. В consumer? → Да. Consumer бросил exception. В DLQ. 5 уровней. |
| **Event storm** | 47 событий на одну операцию. Каждый с 3 полями. Обработчики не знают, какие поля использовать. |
| **Дублирование данных** | Событие несёт `total = 5000`. Aggregate несёт `total = 5000`. Если aggregate изменён после публикации — расхождение. |
| **Семантическая нагрузка** | «Событие» звучит как «вещь, которая происходит». На деле — это контракт между 10 модулями. Изменение контракта = breaking change. |

---

## Антипаттерны

### 1. Событие как «мусорная корзина»

```php
// ❌ «Событие» для всего
class Invoice
{
    public function addLineItem(...) {
        $this->registerEvent(new LineItemAddedEvent(...));
    }
    public function removeLineItem(...) {
        $this->registerEvent(new LineItemRemovedEvent(...));
    }
    public function changeCustomer(...) {
        $this->registerEvent(new CustomerChangedEvent(...));
    }
    public function changeStatus(...) {
        $this->registerEvent(new StatusChangedEvent(...));
    }
    // ... 15 событий
}

// Это не «события». Это «журнал».
// Правильно: 2–4 события на агрегат.
// InvoiceCreated, InvoicePaid, InvoiceVoided.
```

### 2. Событие несёт объект

```php
// ❌
final readonly class InvoicePaidEvent
{
    public function __construct(
        public readonly Invoice $invoice,  // ← живой объект
    ) {}
}

// Обработчик:
$event->invoice->total();  // ← если invoice изменён — «испорчен» факт
$event->invoice->addLineItem(...);  // ← можно изменить!

// ✅
final readonly class InvoicePaidEvent
{
    public function __construct(
        public readonly int   $invoiceId,
        public readonly Money $total,       // ← snapshot
        public readonly string $txId,
    ) {}
}
```

### 3. Обработчик знает «кто опубликовал»

```php
// ❌
final class OnInvoicePaid
{
    public function __invoke(InvoicePaidEvent $event): void
    {
        if ($event->source === 'web_controller') {
            // ... разная логика
        }
        if ($event->source === 'api_gateway') {
            // ... другая логика
        }
    }
}

// Событие — факт. Не «откуда».
// Если нужно «откуда» — это часть доменного правила,
// а не событие.
```

### 4. Обработчик изменяет aggregate

```php
// ❌
final class OnInvoicePaid
{
    public function __invoke(InvoicePaidEvent $event): void
    {
        $invoice = $this->repo->findById($event->invoiceId);
        $invoice->addDiscount(500);  // ← меняет состояние!
        $this->repo->save($invoice);
        // Событие «InvoicePaid» → изменили invoice.
        // Невидимая зависимость. Невидимое изменение.
    }
}

// Событие — ФАКТ. Обработчик — РЕАКЦИЯ.
// Реакция не должна изменять источник.
// Если нужно — это отдельная команда.
```

### 5. Синхронный обработчик делает HTTP-вызов

```php
// ❌
#[AsEventListener]
public function __invoke(InvoicePaidEvent $event): void
{
    $this->http->post('https://analytics.example.com/track', [...]);  // ← 500 мс
    $this->http->post('https://search.example.com/index', [...]);    // ← 300 мс
    $this->mailer->send(...);                                         // ← 200 мс
    // Итого: 1 сек внутри транзакции.
    // Connection pool занят. Другие запросы ждут.
}

// ✅ Асинхронный. Через outbox + worker.
```

### 6. «Событие» как замена возвращаемому значению

```php
// ❌
public function createInvoice(...): void  // ← нет return
{
    // ...
    $this->events->dispatch(new InvoiceCreatedEvent($invoice));
    // Вызывающий: а где id? А где total?
    // → Слушает событие. В асинхронном режиме — позже.
    // → Непредсказуемо.
}

// ✅
public function createInvoice(...): Invoice  // ← возвращает результат
{
    // ...
    $this->events->dispatch(new InvoiceCreatedEvent($invoice));
    return $invoice;
}

// Событие — для внешних систем.
// Возвращаемое значение — для вызывающего.
// Это разные контракты.
```

---

## Практический пример: полный цикл

### 1. Домен

```php
final class Invoice
{
    /** @var list<DomainEvent> */
    private array $pendingEvents = [];

    public function markAsPaid(string $txId, int $amountCents): void
    {
        if ($this->status === Status::Paid) {
            throw new DomainException('Already paid', code: 'INVOICE_ALREADY_PAID');
        }
        if ($amountCents !== $this->totalCents) {
            throw new DomainException('Amount mismatch', code: 'INVOICE_AMOUNT_MISMATCH');
        }

        $this->status = Status::Paid;
        $this->paymentTxId = $txId;
        $this->paidAt = new \DateTimeImmutable();

        $this->pendingEvents[] = new InvoicePaidEvent(
            invoiceId: $this->id,
            totalCents: $this->totalCents,
            currency: $this->currency,
            txId: $txId,
            at: $this->paidAt,
        );
    }

    /** @return list<DomainEvent> */
    public function pullEvents(): array
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];  // ← очищаем. Не публикуем повторно.
        return $events;
    }
}
```

### 2. Сервис

```php
final class InvoiceService
{
    public function __construct(
        private InvoiceRepository $invoices,
        private DomainEventPublisher $events,
        private EntityManagerInterface $em,
    ) {}

    public function markAsPaid(int $invoiceId, string $txId): Invoice
    {
        $invoice = $this->invoices->findById($invoiceId);
        $invoice->markAsPaid($txId, $invoice->totalCents);

        // События агрегата → outbox.
        foreach ($invoice->pullEvents() as $event) {
            $this->events->publish($event);
        }

        $this->invoices->save($invoice);

        $this->em->flush();   // ← invoice + outbox events
        $this->em->commit();

        return $invoice;
    }
}
```

### 3. Outbox

```php
// Таблица:
// outbox (
//   id UUID PK,
//   event_type VARCHAR(128),
//   payload JSON,
//   status VARCHAR(16) DEFAULT 'new',
//   attempts INT DEFAULT 0,
//   created_at TIMESTAMPTZ,
//   published_at TIMESTAMPTZ NULL
// )
//
// Индекс: idx_outbox_status ON outbox(status) WHERE status = 'new'
```

### 4. Relay (отдельный процесс)

```bash
# supervisord / systemd / docker
php bin/console outbox:publish --time-limit=3600
```

### 5. Обработчики (асинхронные)

```php
// Email
final class OnInvoicePaidEmail
{
    public function __construct(
        private InvoiceRepository $invoices,
        private UserRepository    $users,
        private MailerInterface   $mailer,
    ) {}

    public function __invoke(InvoicePaidEvent $event): void
    {
        // Идемпотентность: проверить, что уже не отправляли.
        // (через outbox id или payment_tx_id)

        $invoice = $this->invoices->findById($event->invoiceId);
        $user    = $this->users->findById($invoice->customerId);

        $this->mailer->send(
            to:      $user->email,
            subject: "Invoice #{$event->invoiceId} paid",
            body:    sprintf('Total: %d %s', $event->totalCents, $event->currency),
        );
    }
}

// Search
final class OnInvoicePaidSearch
{
    public function __construct(private SearchIndexer $search) {}

    public function __invoke(InvoicePaidEvent $event): void
    {
        $this->search->upsert('invoice', $event->invoiceId, [
            'status'     => 'paid',
            'total_cents'=> $event->totalCents,
            'currency'   => $event->currency,
            'paid_at'    => $event->at->format('c'),
        ]);
    }
}

// Analytics
final class OnInvoicePaidAnalytics
{
    public function __construct(private AnalyticsClient $analytics) {}

    public function __invoke(InvoicePaidEvent $event): void
    {
        $this->analytics->track('invoice_paid', [
            'invoice_id'  => $event->invoiceId,
            'total_cents' => $event->totalCents,
            'tx_id'       => $event->txId,
        ]);
    }
}
```

### 6. Что происходит при оплате

```
t=0ms    POST /invoices/42/pay
t=2ms    InvoiceService::markAsPaid(42, 'tx_abc')
t=4ms    Invoice::markAsPaid() → status = 'paid', event в pendingEvents
t=5ms    DomainEventPublisher::publish(InvoicePaidEvent)
         → INSERT INTO outbox (event_type, payload, status) VALUES (...)
t=6ms    em->flush()
         → UPDATE invoice SET status='paid', version=5 WHERE id=42 AND version=4
         → INSERT INTO outbox ...
t=7ms    em->commit()
         ← 201 Created → клиент
         (ответ ушёл. Обработка ещё не началась.)

t=8ms    [outbox:publish]
         SELECT * FROM outbox WHERE status='new' ORDER BY created_at
         → 1 событие
t=10ms   [outbox:publish]
         UPDATE outbox SET status='published' WHERE id = ?
         commit()
t=12ms   [outbox:publish]
         bus->dispatch(OutboxMessage)
         → INSERT INTO messenger_messages ...
t=15ms   [worker]
         SELECT FROM messenger_messages WHERE visible = false
         → 1 сообщение
t=20ms   [worker]
         OnInvoicePaidEmail::invoke()
         → SELECT invoice, user
         → SMTP send → 250 OK
t=250ms  [worker]
         OnInvoicePaidSearch::invoke()
         → ES upsert → 201
t=350ms  [worker]
         OnInvoicePaidAnalytics::invoke()
         → HTTP POST → 204
t=355ms  [worker]
         DELETE FROM messenger_messages WHERE id = ?
         → Готово.
```

**Клиент получил ответ на 7-й миллисекунде.** Email, search, analytics — к 355-й. Разрыв — 348 мс. «Не замедляет ответ».

**Если SMTP упал:**
```
t=20ms   [worker]
         OnInvoicePaidEmail::invoke()
         → SMTP send → CONNECTION REFUSED
         → Exception
         → Messenger: retry (1/3)
         → 5000 мс позже:
         OnInvoicePaidEmail::invoke()
         → SMTP send → 250 OK
```

**Если worker упал:**
```
t=20ms   [worker] — crash
         → messenger_messages: status = 'failed'
         → DLQ
         → Алерт
         → Ручная переработка / автоматический retry
```

**Событие не потеряно.** Оно в `outbox` со статусом `published`. В `messenger_messages` со статусом `failed`. DLQ. Можно переработать.

---

## Итого: правила

```
1. Событие = ФАКТ. Не команда. Не намерение.
2. Публикуешь ДО commit'а. В той же транзакции.
3. Событие несёт ID + snapshot. Не объект.
4. Событие — на уровне агрегата. Не вложенной сущности.
5. 2–4 события на агрегат. Не 47.
6. Обработчик — асинхронный (внешние вызовы) или синхронный (внутренний).
7. Outbox: aggregate + событие — одна транзакция.
8. Relay: «забирает» событие, публикует, помечает.
9. Обработчик идемпотентен. Может быть вызван дважды.
10. Обработчик НЕ изменяет агрегат. Только реагирует.
11. Вызывающий получает результат через return. Не через событие.
12. Если нет обработчика — не публикуй.
```

> ⏱ 75.5s · prompt 88151 tok · output 9086 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанных вопроса о DDD:
1. Чем VO отличается от Entity
2. Как решить, к чему отнести концепцию
3. Ubiquitous language и его влияние на код

Это продолжение разговора о DDD. Я уже обсуждал VO (Money, InvoiceLineItem), Entity (Order, Invoice), агрегаты, события. Нужно дать чёткий, практичный ответ без воды.

Структура:
1. VO vs Entity — табличное сравнение + примеры
2. Критерии выбора — практические вопросы
3. Ubiquitous language — что это, как влияет на имена в коде, примеры

Важно: я должен быть честен, что у меня нет "опыта", но могу дать структурированный ответ. В предыдущих ответах я уже показал Money как VO, Invoice/Account как Entity, Transfer как отдельный агрегат. Могу ссылаться на эти примеры для связности.

Ключевое отличие VO vs Entity:
- Entity: идентичность (id), изменяемость, жизненный цикл
- VO: значение, неизменяемость, нет id, сравнение по содержимому

Как решать:
- Есть ли у концепции "жизнь"? (создан → изменён → удалён)
- Меняется ли она?
- Интересует ли "какая именно" или "какая по значению"?
- Кто её создаёт? Кто изменяет?

Ubiquitous language:
- Общая терминология между бизнесом и кодом
- Имена классов, методов, полей = термины домена
- "customer" vs "user" vs "buyer" — разные термины = разные понятия
- Влияние: имена методов, имена событий, имена колонок

Давайте структурирую.

</details>

**Кандидат:**

# Value Object, Entity и Ubiquitous Language

## Value Object vs Entity

### Корневое различие

**Entity** — это «кто». Уникальность определяется **идентичностью** (id). Две сущности с одинаковыми полями — это **разные** сущности.

**Value Object** — это «что». Уникальность определяется **значением**. Два VO с одинаковыми полями — это **одинаковые** объекты.

```php
// Entity: «какой именно заказ?»
$order1 = new Order(id: 42, total: Money(5000, 'EUR'));
$order2 = new Order(id: 43, total: Money(5000, 'EUR'));

$order1 === $order2;  // false — разные id, разные заказы
$order1 == $order2;   // false — Doctrine сравнивает по identity

// Даже если поля идентичны — это разные сущности.
// «Заказ №42» ≠ «Заказ №43», даже если оба на 5000 EUR.
```

```php
// Value Object: «какое значение?»
$m1 = Money::fromCents(5000, 'EUR');
$m2 = Money::fromCents(5000, 'EUR');

$m1 === $m2;  // false — разные объекты в памяти (reference)
$m1->equals($m2);  // true — одинаковое значение
// «5000 центов в EUR» = «5000 центов в EUR». Неважно, кто создал.
```

### Сравнение по осям

| Ось | Entity | Value Object |
|---|---|---|
| **Идентичность** | `id`. «Это *тот самый* заказ». | Значение. «Это *та же сумма*». |
| **Изменяемость** | Меняется: `status: draft → paid → void`. | Не меняется. `add()` возвращает **новый** VO. |
| **Жизненный цикл** | Created → Active → Archived → Deleted. | Нет цикла. Появляется, используется, исчезает. |
| **Сравнение** | По `id` (identity). | По полям (value). `equals()`. |
| **Создание** | Фабрика + id из БД. | Конструктор / static factory. |
| **Хранение** | Таблица, PK, FK. | Столбцы, JSON, embedded. |
| **Ссылки** | «Заказ ссылается на клиента». | «Заказ содержит сумму». |
| **Дубликаты** | Невозможны (PK). | Возможны. Два `Money(5000, 'EUR')` в разных заказах. |
| **Копирование** | `clone` — новая сущность с тем же id. | `clone` — тот же объект (значения равны). |

### Конкретно: почему `Money` — VO, а `Invoice` — Entity

```php
// Money — VO:
// • Нет id. «5000 центов в EUR» — не «деньги с id=77».
// • Неизменяемо. new Money(5000, 'EUR')->add(new Money(100, 'EUR')) → new Money(5100, 'EUR').
// • Дублируемо. В 10 заказах — 10 объектов Money(5000, 'EUR'). Они «одинаковые».
// • Нет жизненного цикла. Не «создан → активен → закрыт».
// • Создаётся конструктором. Не из БД.

// Invoice — Entity:
// • Есть id. «Инвойс №42» — это *тот самый* инвойс, не «любой инвойс на 5000».
// • Изменяется. draft → paid → void.
// • Дублируемы? Нет. PK. «Инвойс №42» и «Инвойс №43» — разные, даже если тот же total.
// • Жизненный цикл. Создан 15.01. Оплачен 20.01. Закрыт 01.03.
// • Создаётся фабрикой + persist + id из БД.
```

### Граница не всегда очевидна

**`Address`** — VO или Entity?

```
Аргументы за VO:
  • «Адрес на Ленина 10» — значение. Не «адрес с id=55».
  • Два клиента могут жить по одному адресу. Это один и тот же адрес.
  • Неизменяем (в большинстве доменов).
  • Нет жизненного цикла.

Аргументы за Entity:
  • У адреса может быть id. «Адрес №55».
  • Адрес может измениться: переезд.
  • Адрес может быть «удалён» (клиент сменил адрес).

Решение: в 90% случаев — VO.
Адрес — это «где находится». Значение. Не «кто».
Если адрес — самостоятельная сущность (реестр объектов недвижимости) — Entity.
Критерий: «адрес» или «объект недвижимости»?
```

**`Email`** — VO или Entity?

```
VO: «a@b.c» — значение. Два пользователя с одинаковым email —
     это нарушение uniqueness, но сам email — значение.
Entity: нет. Email не «живёт». Не «создан → активен → закрыт».
```

**`User`** — VO или Entity?

```
Entity: безусловно.
• Есть id. «Пользователь №42».
• Изменяется: имя, email, роль.
• Жизненный цикл: создан → активен → заблокирован → удалён.
• «Пользователь №42» ≠ «пользователь №43», даже если оба «Иван Иванов».
```

**`Permission`** — VO или Entity?

```
Зависит от домена.

Если permission = «строка-код»: 'orders:read', 'orders:write'
  → VO. Значение. Не имеет id. Не изменяется.

Если permission = «запись в БД с id, scope, expires_at»
  → Entity. Идентичность. Жизненный цикл.
```

---

## Как решать: практические вопросы

Когда встречаю концепцию домена и не могу сразу сказать «это Entity» или «это VO» — спрашиваю пять вопросов. Порядок важен.

### Вопрос 1: «Это *какой-то* или *тот самый*?»

```
«Какой-то» → VO.
  • «Сумма 5000 EUR» — не «та самая сумма». Любая сумма 5000 EUR — одинаковая.
  • «Адрес Ленина 10» — не «тот самый адрес». Одинаковый адрес — один адрес.

«Тот самый» → Entity.
  • «Заказ №42» — конкретный. Не «любой заказ».
  • «Пользователь Иван Иванов (id=42)» — конкретный. Не «любой Иван».
```

### Вопрос 2: «Есть ли у него id, который *важен*?»

```
«id используется для ссылок, для аудита, для "найди мне именно его"»
  → Entity.

«id — техническая деталь (PK). По значению можно найти»
  → VO.

Пример:
  Invoice.id — важен. «Оплати инвойс №42».
  Money — нет id. «Сумма 5000 EUR». Не «сумма с id=77».
```

### Вопрос 3: «Изменяется ли он?»

```
Да, и изменение — значимое:
  • Order: draft → paid. Значение.
  • User: active → blocked. Значение.
  → Entity.

Нет, или «изменение» = создание нового:
  • Money(5000) + Money(100) → Money(5100). Старый Money(5000) не изменился.
  • Address: переезд = новый Address. Старый — «был».
  → VO.
```

### Вопрос 4: «Дублируется ли он?»

```
Да, и дубликаты — это нормально:
  • 10 заказов с Money(5000, 'EUR'). 10 объектов. Все «одинаковые».
  → VO.

Нет, дубликаты — это ошибка:
  • Два Invoice с id=42 — невозможно (PK).
  • Два User с email='a@b.c' — нарушение uniqueness.
  → Entity.
```

### Вопрос 5: «Кто создаёт и кто изменяет?»

```
Создаётся фабрикой, изменяется командами:
  • Invoice::create() → invoice.addLineItem() → invoice.markAsPaid()
  • Множество методов изменения.
  → Entity.

Создаётся конструктором, не изменяется:
  • Money::fromCents(5000, 'EUR') → готово. Нет addLineItem.
  → VO.
```

### Сводная таблица

```
                    VO               Entity
─────────────────────────────────────────────────────
«Какой-то / Тот самый»    Какой-то      Тот самый
id важен?                Нет            Да
Изменяется?              Нет (новый)    Да
Дубликаты?               Нормально      Ошибка
Создание                 Конструктор    Фабрика + id
Примеры                  Money, Address, Email, Color, Period
                         Order, User, Invoice, Transfer
```

### Сложные случаи

**`Address` в e-commerce:**

```
VO. Почему:
  • «Адрес доставки» — значение. Не «адрес с id».
  • Клиент может иметь 5 разных адресов. Каждый — значение.
  • «Переехал» = новый Address. Старый — история.
  • Нет id. Нет жизненного цикла.

Но: если в домене «реестр объектов недвижимости» —
  Address = Entity. Есть id. Изменяется (реконструкция).
  «Объект №55» ≠ «объект №56».
```

**`Period` (период подписки):**

```
VO. Почему:
  • «Подписка на январь-март 2024» — значение.
  • Два пользователя с подпиской «янв-март 2024» —
    это один и тот же период. Не «периоды с id=1 и id=2».
  • Неизменяем. «Продлить» = новый Period.
```

**`Subscription` (подписка):**

```
Entity. Почему:
  • «Подписка пользователя №42 на план Premium» — конкретная.
  • Есть id. «Отменить подписку №77».
  • Изменяется: active → paused → cancelled.
  • Дубликаты: два активных плана — ошибка.
```

**`Money` vs `Payment`:**

```
Money(5000, 'EUR') — VO. Значение. «Сколько».
Payment(id=77, amount=Money(5000,'EUR'), status='succeeded') — Entity.
  • «Платёж №77» — конкретный.
  • Изменяется: pending → succeeded → refunded.
  • Дубликаты: два платежа на одну сумму — нормально (повторная оплата).
  • Но: «платёж №77» ≠ «платёж №78», даже если оба на 5000 EUR.
```

---

## Ubiquitous Language

### Что это

Ubiquitous Language (UL) — **единый набор терминов**, который используют:

- Бизнес-аналитик в требованиях.
- Разработчик в именах классов, методов, полей.
- Тестировщик в названиях тестов.
- Product-менеджер в Jira.
- QA в баг-репортах.

Один термин = одно понятие. Одно понятие = один термин.

### Почему это важно

```
Без UL:
  Бизнес:  «клиент»
  Код:     $user
  БД:      customers
  API:     /buyers
  Тест:    testUserCanLogin
  Баг:     «buyer не может купить»

Вопрос: «Клиент», «user», «customer», «buyer» — это одно и то же?
Если да — почему 4 слова?
Если нет — где граница?
```

```
С UL:
  Бизнес:  «Покупатель»
  Код:     Buyer
  БД:      buyers
  API:     /buyers
  Тест:    testBuyerCanPlaceOrder
  Баг:     «Покупатель не может создать заказ»

Один термин. Все знают, о чём речь.
```

### Как UL влияет на код

#### 1. Имена классов = термины домена

```php
// Без UL:
class Customer { }       // а почему не User?
class Order { }          // а почему не Transaction?
class Money { }          // а почему не Amount?
class Address { }        // а почему не Location?

// С UL (термины из домена):
class Buyer { }          // «Покупатель» — тот, кто покупает
class Seller { }         // «Продавец» — тот, кто продаёт
class Invoice { }        // «Счёт» — документ, а не «заказ»
class LineItem { }       // «Позиция» — строка в счёте
class Amount { }         // «Сумма» — не «деньги»
class ShippingAddress { }// «Адрес доставки» — не «адрес»
class BillingAddress { } // «Адрес facturации» — не «адрес»
```

**Принцип:** имя класса должно быть термином, который бизнес-аналитик скажет вслух. Не «DataHolder», не «Manager», не «Handler».

#### 2. Имена методов = действия домена

```php
// Без UL:
$order->setStatus('PAID');
$order->setDate(new \DateTime());
$order->add(5000);
$order->remove();

// С UL:
$invoice->markAsPaid($payment);
$invoice->recordIssueDate($date);
$invoice->addLineItem($item);
$invoice->void($reason);
```

Метод — это **глагол домена**. «markAsPaid», а не `setStatus('PAID')`. «void», а не `delete()`.

#### 3. Имена полей = атрибуты домена

```php
// Без UL:
class Order {
    public int $amount;        // сумма в каких единицах?
    public string $type;       // какой тип?
    public bool $active;       // активен = ?
    public array $items;       // позиции?
    public string $status;     // статус = ?
}

// С UL:
class Invoice {
    public Amount $total;      // «итоговая сумма» — с валютой
    public InvoiceStatus $status;  // enum: Draft, Open, Paid, Void
    public list<LineItem> $lineItems;  // «позиции счёта»
    public \DateTimeImmutable $issuedAt;  // «дата выставления»
    public Buyer $buyer;      // «покупатель»
    public ?Payment $payment; // «платёж» (ссылка на отдельный агрегат)
}
```

#### 4. Имена событий = факты домена

```php
// Без UL:
new OrderStatusChangedEvent('PAID');
new UserActionEvent('create_order');
new SystemEvent('payment_processed');

// С UL:
new InvoiceCreatedEvent($invoiceId, $total, $buyerId);
new InvoicePaidEvent($invoiceId, $total, $paymentTxId, $at);
new InvoiceVoidedEvent($invoiceId, $reason, $at);
new BuyerRegisteredEvent($buyerId, $email);
```

Событие — **утверждение**. «Счёт оплачен». Не «статус изменён на PAID». Не «действие пользователя: create_order».

#### 5. Имена ошибок = нарушения домена

```php
// Без UL:
throw new \Exception('Error: 0 items');
throw new \Exception('Invalid amount');
throw new \RuntimeException('DB error');

// С UL:
throw new EmptyInvoiceException();          // «счёт пуст»
throw new InsufficientFundsException(       // «недостаточно средств»
    required: 5000,
    available: 3000,
);
throw new InvoiceAlreadyPaidException($invoiceId);  // «уже оплачен»
throw new CurrencyMismatchException('EUR', 'KZT');  // «валюты не совпадают»
```

Имя ошибки — **нарушение доменного правила**. Не «ошибка 500». Не «DB error». А «что именно было нарушено».

#### 6. Имена колонок в БД = атрибуты домена

```sql
-- Без UL:
CREATE TABLE orders (
    id INT,
    type VARCHAR,
    amount INT,
    status VARCHAR,
    date DATETIME
);

-- С UL:
CREATE TABLE invoice (
    id INT,
    buyer_id INT,
    total_cents INT,          -- «итого в центах»
    currency CHAR(3),
    status VARCHAR,           -- draft, open, paid, void
    issued_at TIMESTAMPTZ,    -- «дата выставления»
    created_at TIMESTAMPTZ,
    updated_at TIMESTAMPTZ
);
```

### Как UL рождается

UL **не придумывается**. Она **вырастает** из разговора.

```
1. До начала кода:
   Сидим с бизнес-аналитиком. Он говорит:
   «Покупатель создаёт заказ. В заказе позиции.
    Позиция имеет наименование, количество, сумму.
    Заказ может быть оплачен или отменён.»

   Мы записываем термины:
   Покупатель, Заказ, Позиция, Наименование, Количество, Сумма,
   Оплачен, Отменён.

2. Прототип:
   Показываем бизнесу:
   «У нас Invoice, LineItem, Quantity, UnitPrice, Total.
    Statuses: Draft, Paid, Void.»

   Бизнес: «Нет, это не "Invoice". Это "Счёт".
    И "LineItem" — это "Позиция".
    "Void" — это "Аннулирован". Не "Отменён".»

   Мы меняем имена. В коде. В БД. В API. В тестах.

3. Документация:
   Глоссарий:
   • Счёт (Invoice) — документ, содержащий позиции, сумму, статус.
   • Позиция (LineItem) — строка в счёте: наименование, количество, цена.
   • Покупатель (Buyer) — пользователь, создающий счёт.
   • Аннулирован (Void) — счёт отозван. Не отменён. Аннулирован.

4. Код:
   class Invoice { }
   class LineItem { }
   class Buyer { }
   enum InvoiceStatus { case Draft; case Paid; case Void; }
```

### UL и границы агрегатов

UL помогает определить границы:

```
Бизнес: «Счёт и Платёж — это разные вещи.
Счёт создаётся. Платёж — отдельная операция.
Счёт может быть создан, но не оплачен.
Платёж может быть выполнен, но счёт — ещё нет.»

→ Invoice — один агрегат.
→ Payment — другой агрегат.
→ Связь: Invoice.paymentId (ссылка на Payment по id).
→ Не «Payment внутри Invoice». Отдельные агрегаты.
```

```
Бизнес: «Позиция не существует без Счёта.
Нельзя "найти позицию по id". Можно "найти счёт по id и посмотреть его позиции".»

→ LineItem — вложенная сущность в Invoice.
→ Не отдельный агрегат.
→ Не имеет репозитория.
```

```
Бизнес: «Адрес — это данные. Не "сущность".
У покупателя может быть 5 адресов.
Каждый адрес — "значение", не "объект".»

→ Address — Value Object.
→ Не Entity.
→ Вложен в Buyer как VO.
```

### UL и события

```
Бизнес: «Когда счёт оплачен — мы должны:
1. Отправить письмо покупателю.
2. Обновить поиск.
3. Записать в аналитику.
Это три разные реакции на один факт: "Счёт оплачен"».

→ Событие: InvoicePaid.
→ Три обработчика.
→ Не «отправить письмо» (команда).
→ Не «обновить поиск» (команда).
→ Факт: «Счёт оплачен». Реакции — кто во что горазд.
```

### UL и ошибки

```
Бизнес: «Не "ошибка 400". А "недостаточно средств".
Не "invalid amount". А "сумма не может быть отрицательной".
Не "DB error". А "связь с базой данных потеряна".»

→ InsufficientFundsException
→ NegativeAmountException
→ DatabaseConnectionException

Каждая ошибка — нарушение конкретного доменного правила.
Имя ошибки = название правила.
```

---

## Антипаттерны UL

### 1. Четыре слова для одного понятия

```
// В одном проекте:
class Customer { }
class User { }
class Buyer { }
class Account { }

// Все четыре — «тот, кто покупает».
// Или: нет. Customer ≠ User ≠ Buyer ≠ Account.
// Если нет — UL сломана.
// Если да — выберите ОДНО слово. Остальные — alias или удаляйте.
```

### 2. Технический термин в домене

```php
// ❌
class Entity { }
class Repository { }
class Handler { }
class Processor { }
class Manager { }
class Service { }

// Это не UL. Это «что делает в коде».
// UL — «что это в домене».

// ✅
class Invoice { }
class LineItem { }
class Buyer { }
```

`Service`, `Handler`, `Manager` — паттерны. Не термины. `Invoice` — термин.

### 3. Английский в русском домене (или наоборот)

```
// ❌ Смешение:
$invoice->setStatus('PAID');         // английский enum
$invoice->markAsOpлачен();           // русский метод
$buyer->getEmail();                   // «email» — латиница
$buyer->getEmailAdress();            // «email» + «адрес» — два языка

// ✅ Единый язык:
$invoice->markAsPaid();
$buyer->email();  // или $buyer->contactEmail()
```

Язык можно выбрать. Но один. Не «на полпути».

### 4. UL в одном месте, не в другом

```php
// ❌
// В домене: Buyer, Invoice, LineItem
// В БД:     customers, orders, order_items
// В API:    /users, /transactions, /products
// В тестах: testUserCanLogin, testOrderCreation

// Четыре языка. Один проект.
```

### 5. UL как «переименование ради переименования»

```php
// ❌ «Давайте назовём всё "доменно"»:
class Invoice { }
class LineItem { }
class Buyer { }
class Amount { }
class Currency { }
class PaymentMethod { }
class TaxRate { }
class ShippingMethod { }
class Discount { }
class Promotion { }
// 15 классов, каждый «доменный».
// А на деле: 12 из них — просто данные.

// UL — не «много классов с красивыми именами».
// UL — «каждое имя = термин, который бизнес скажет вслух».
// Если бизнес не скажет «PaymentMethod» вслух — это не UL.
```

---

## Итого: три вопроса перед созданием класса

```
1. Как бизнес-аналитик скажет это вслух?
   → Это имя класса. (Invoice, не InvoiceEntity.)

2. Это «тот самый» (id, изменяется, жизненный цикл)?
   → Entity.

   Это «какой-то» (значение, неизменяем, дублируется)?
   → Value Object.

3. Есть ли для этого события?
   → Если да — имя события = термин. (InvoicePaid, не StatusChanged.)
   → Если нет — не публикуй.
```

**Entity** — «кто». **Value Object** — «что». **Ubiquitous Language** — «как мы об этом говорим». Три вещи, которые вместе дают код, который читается как домен, а не как реализация.

> ⏱ 61.6s · prompt 96566 tok · output 6217 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

The user asks about clean architecture structure for a PHP application: layers, what goes in each, dependency rule, where Doctrine/Symfony controllers/queue handlers land, and how to enforce boundaries.

Let me think about this carefully. I've been giving increasingly detailed architectural advice throughout this conversation. Now they want the "big picture" — how all the pieces fit together in a clean architecture structure.

Key things to cover:
1. The layers and their responsibilities
2. Dependency rule (arrows point inward)
3. Where Doctrine goes (infrastructure)
4. Where Symfony controllers go (presentation/framework)
5. Where queue handlers go (application or infrastructure)
6. How to enforce boundaries (tools, conventions, testing)

I should be concrete with directory structure, code examples, and enforcement mechanisms. The user has been getting very detailed answers, so I should match that level.

Let me think about the layers:
- Domain (innermost): entities, value objects, domain events, domain services, repository interfaces
- Application: use cases / application services, DTOs, ports (interfaces for infrastructure)
- Infrastructure: Doctrine, Symfony HTTP adapter, message queue transport, email
- Framework/Adapters: Symfony controllers, listeners, command handlers

The dependency rule: everything depends on domain. Application depends on domain. Infrastructure depends on application (implements its interfaces). Framework depends on application.

In PHP/Symfony, this is tricky because Symfony is a framework with its own structure (app/, config/, src/Controller, src/Repository, etc.). Clean architecture means fighting against the framework's default structure.

Let me structure this clearly with a directory tree, code examples, and enforcement mechanisms (PHPStan, ArchUnit-like tools, testing strategies).

I should also address the "where does X go" questions explicitly since the user asked.

Let me write a comprehensive but well-organized answer.

</details>

**Кандидат:**

# Чистая архитектура в PHP: структура, зависимости, контроль границ

## Правило зависимостей (Dependency Rule)

Одна стрелка, которая определяет всё:

```
Внешние слои → Внутренние слои. Никогда наоборот.

Infrastructure → Application → Domain
Framework      → Application → Domain

Domain НЕ знает о Symfony, Doctrine, Redis, RabbitMQ.
Application НЕ знает о Symfony, Doctrine, Redis, RabbitMQ.
Infrastructure ЗНАЕТ о Domain и Application (реализует их интерфейсы).
```

Если `Domain` содержит `use Symfony\Component\...` — граница нарушена. Точка.

---

## Четыре слоя

```
┌─────────────────────────────────────────────────────────────────────┐
│  FRAMEWORK / ADAPTERS (внешний мир)                                 │
│                                                                     │
│  Symfony Controller, Twig, Request, Response                        │
│  Console Command, Queue Consumer, Webhook Handler                   │
│  «Переводит» внешний запрос в вызов Application                     │
│  Зависит от: Application, Domain                                    │
├─────────────────────────────────────────────────────────────────────┤
│  APPLICATION (use cases, оркестрация)                               │
│                                                                     │
│  Application Services (Use Cases)                                   │
│  DTO (Command, Query, Result)                                       │
│  Ports (интерфейсы для Infrastructure)                              │
│  «Что нужно сделать» — порядок, транзакции, координация             │
│  Зависит от: Domain                                                 │
├─────────────────────────────────────────────────────────────────────┤
│  DOMAIN (ядро)                                                      │
│                                                                     │
│  Entities, Value Objects, Domain Services                           │
│  Domain Events                                                      │
│  Repository INTERFACES (порты)                                      │
│  «Бизнес-правила, инварианты, термины»                              │
│  Зависит от: Ничего. Чистый PHP.                                    │
├─────────────────────────────────────────────────────────────────────┤
│  INFRASTRUCTURE (реализация портов)                                 │
│                                                                     │
│  Doctrine (Entity, Repository, EntityManager)                       │
│  Mailer, SearchIndexer, AnalyticsClient                             │
│  MessageBus transport, Cache, FileStorage                           │
│  «Как» — технологии, протоколы, форматы                              │
│  Зависит от: Domain, Application (реализует их интерфейсы)          │
└─────────────────────────────────────────────────────────────────────┘
```

**Ключевой момент:** Repository — **интерфейс** в Domain, **реализация** в Infrastructure.

```php
// Domain: интерфейс
interface InvoiceRepository {
    public function save(Invoice $invoice): void;
    public function findById(int $id): Invoice;
}

// Infrastructure: реализация
final class DoctrineInvoiceRepository implements InvoiceRepository {
    public function __construct(private EntityManagerInterface $em) {}
    // ... Doctrine-код ...
}
```

---

## Структура каталогов

```
project/
├── bin/
│   └── console                    # Symfony entry point
│
├── config/
│   ├── bundles.php
│   ├── packages/
│   │   ├── framework.yaml
│   │   ├── doctrine.yaml
│   │   ├── messenger.yaml
│   │   └── security.yaml
│   └── services.yaml             # DI: связывает слои
│
├── src/
│   │
│   ├── Domain/                   # ─── ЯДРО. Чистый PHP. ───
│   │   ├── Invoice/
│   │   │   ├── Invoice.php          # Entity / Aggregate Root
│   │   │   ├── LineItem.php         # Вложенная сущность
│   │   │   ├── Money.php            # Value Object
│   │   │   ├── InvoiceStatus.php    # Enum
│   │   │   ├── InvoiceRepository.php  # ИНТЕРФЕЙС (порт)
│   │   │   ├── InvoiceService.php     # Domain Service (чистая логика)
│   │   │   ├── Event/
│   │   │   │   ├── InvoiceCreated.php
│   │   │   │   ├── InvoicePaid.php
│   │   │   │   └── InvoiceVoided.php
│   │   │   └── Exception/
│   │   │       ├── InsufficientFunds.php
│   │   │       ├── InvoiceAlreadyPaid.php
│   │   │       └── EmptyInvoice.php
│   │   │
│   │   ├── Buyer/
│   │   │   ├── Buyer.php
│   │   │   ├── BuyerRepository.php  # ИНТЕРФЕЙС
│   │   │   ├── Money.php            # (общий VO, или shared/)
│   │   │   └── Event/
│   │   │       └── BuyerRegistered.php
│   │   │
│   │   ├── Shared/                 # Общие VO, используемые в нескольких BC
│   │   │   ├── Money.php
│   │   │   ├── Email.php
│   │   │   └── Uuid.php
│   │   │
│   │   └── Exception/              # Базовые доменные исключения
│   │       ├── DomainException.php
│   │       └── ProgrammingError.php
│   │
│   ├── Application/              # ─── USE CASES. Оркестрация. ───
│   │   ├── Invoice/
│   │   │   ├── CreateInvoiceUseCase.php
│   │   │   ├── MarkInvoicePaidUseCase.php
│   │   │   ├── VoidInvoiceUseCase.php
│   │   │   ├── ListInvoicesUseCase.php
│   │   │   ├── Dto/
│   │   │   │   ├── CreateInvoiceCommand.php
│   │   │   │   ├── MarkPaidCommand.php
│   │   │   │   ├── InvoiceResult.php
│   │   │   │   └── InvoiceListItem.php
│   │   │   └── Port/
│   │   │       ├── Clock.php              # interface
│   │   │       ├── UuidGenerator.php      # interface
│   │   │       └── EventPublisher.php     # interface
│   │   │
│   │   ├── Buyer/
│   │   │   ├── RegisterBuyerUseCase.php
│   │   │   ├── Dto/
│   │   │   │   ├── RegisterCommand.php
│   │   │   │   └── BuyerResult.php
│   │   │   └── Port/
│   │   │       └── PasswordHasher.php     # interface
│   │   │
│   │   └── Shared/
│   │       ├── Money.php            # (если не в Domain\Shared)
│   │       └── Clock.php
│   │
│   ├── Infrastructure/           # ─── ТЕХНОЛОГИИ. Реализация портов. ───
│   │   ├── Persistence/          # Doctrine
│   │   │   ├── Entity/
│   │   │   │   ├── InvoiceEntity.php      # #[ORM\Entity]
│   │   │   │   ├── LineItemEntity.php
│   │   │   │   └── BuyerEntity.php
│   │   │   ├── Repository/
│   │   │   │   ├── DoctrineInvoiceRepository.php  # implements InvoiceRepository
│   │   │   │   └── DoctrineBuyerRepository.php
│   │   │   ├── Mapper/
│   │   │   │   ├── InvoiceMapper.php      # Domain ↔ Entity
│   │   │   │   └── BuyerMapper.php
│   │   │   └── EventStore/
│   │   │       └── OutboxEntity.php       # #[ORM\Entity]
│   │   │
│   │   ├── Notification/
│   │   │   ├── SymfonyMailerNotifier.php  # implements Notifier (port)
│   │   │   └── EmailTemplate.php
│   │   │
│   │   ├── Search/
│   │   │   └── MeilisearchIndexer.php     # implements SearchIndexer (port)
│   │   │
│   │   ├── Messaging/
│   │   │   ├── Handler/
│   │   │   │   ├── SendWelcomeEmailHandler.php  # #[AsMessageHandler]
│   │   │   │   ├── IndexBuyerHandler.php
│   │   │   │   └── TrackInvoiceHandler.php
│   │   │   ├── Message/
│   │   │   │   ├── SendWelcomeEmail.php       # сериализуемое сообщение
│   │   │   │   └── IndexBuyer.php
│   │   │   └── OutboxPublisher.php            # relay: outbox → bus
│   │   │
│   │   ├── Crypto/
│   │   │   └── SodiumPasswordHasher.php       # implements PasswordHasher
│   │   │
│   │   ├── Time/
│   │   │   └── SystemClock.php                # implements Clock
│   │   │
│   │   └── Id/
│   │       └── UuidGenerator.php              # implements UuidGenerator
│   │
│   ├── Framework/                # ─── АДАПТЕРЫ. Внешний мир. ───
│   │   ├── Http/
│   │   │   ├── Controller/
│   │   │   │   ├── InvoiceController.php      # Symfony Controller
│   │   │   │   └── BuyerController.php
│   │   │   ├── Request/
│   │   │   │   ├── CreateInvoiceRequest.php   # валидация, маппинг
│   │   │   │   └── RegisterBuyerRequest.php
│   │   │   ├── Response/
│   │   │   │   ├── InvoiceResponse.php        # DTO для JSON
│   │   │   │   └── ErrorResponse.php
│   │   │   ├── Security/
│   │   │   │   ├── ApiKeyAuthenticator.php    # Symfony Authenticator
│   │   │   │   └── Voter/
│   │   │   │       └── InvoiceVoter.php
│   │   │   └── Exception/
│   │   │       └── GlobalExceptionHandler.php # #[AsExceptionListener]
│   │   │
│   │   ├── Console/
│   │   │   └── Command/
│   │   │       ├── OutboxPublishCommand.php   # #[AsCommand]
│   │   │       └── SeedCommand.php
│   │   │
│   │   └── Event/
│   │       └── Listener/
│   │           ├── OnBuyerRegisteredEmail.php # #[AsEventListener]
│   │           └── OnInvoicePaidSearch.php
│   │
│   └── Kernel.php
│
├── tests/
│   ├── Unit/
│   │   ├── Domain/               # Тесты БЕЗ моков. Чистый PHP.
│   │   │   ├── InvoiceTest.php
│   │   │   ├── MoneyTest.php
│   │   │   └── InvoiceServiceTest.php
│   │   ├── Application/          # Тесты с моками портов.
│   │   │   ├── CreateInvoiceUseCaseTest.php
│   │   │   └── MarkPaidUseCaseTest.php
│   │   └── Infrastructure/       # Тесты реализации портов.
│   │       ├── DoctrineInvoiceRepositoryTest.php
│   │       └── MeilisearchIndexerTest.php
│   │
│   ├── Integration/
│   │   ├── DoctrineInvoiceRepositoryTest.php  # реальная БД (testcontainer)
│   │   └── MessengerFlowTest.php
│   │
│   └── E2E/
│       └── InvoiceApiTest.php    # HTTP request → response
│
├── composer.json
├── phpstan.neon
├── .php-cs-fixer.dist.php
└── .env
```

---

## Что лежит в каждом слое

### Domain — ядро

**Входит:**

| Что | Пример |
|---|---|
| Entities / Aggregate Roots | `Invoice`, `Buyer` |
| Value Objects | `Money`, `Email`, `Address` |
| Domain Services | `PricingPolicy`, `TaxCalculator` |
| Domain Events (классы) | `InvoicePaid`, `BuyerRegistered` |
| Repository **интерфейсы** | `InvoiceRepository` |
| Domain Exceptions | `InsufficientFunds`, `InvoiceAlreadyPaid` |
| Enums | `InvoiceStatus`, `BuyerTier` |

**НЕ входит:**

| Что | Почему |
|---|---|
| `#[ORM\Entity]` | Это Doctrine. Инфраструктура. |
| `use Symfony\...` | Фреймворк. Внешний мир. |
| `EntityManager` | Инфраструктура. |
| HTTP-статусы, JSON, Twig | Framework. |
| `MessageBus`, `RabbitMQ`, `Redis` | Инфраструктура. |
| Конфигурация, DI-контейнер | Фреймворк. |

**Зависимости:** только `std` PHP + `Domain\Exception`. Никаких vendor-пакетов.

```php
// src/Domain/Invoice/Invoice.php
// ← НИКАКИХ use Symfony, Doctrine, Doctrine\Common, ...
namespace App\Domain\Invoice;

use App\Domain\Exception\DomainException;
use App\Domain\Shared\Money;
use App\Domain\Invoice\Event\InvoicePaid;
use App\Domain\Invoice\Exception\InvoiceAlreadyPaid;

final class Invoice
{
    // ... чистый PHP ...
}
```

### Application — use cases

**Входит:**

| Что | Пример |
|---|---|
| Use Cases / Application Services | `CreateInvoiceUseCase`, `MarkInvoicePaidUseCase` |
| Command / Query DTO | `CreateInvoiceCommand`, `MarkPaidCommand` |
| Result DTO | `InvoiceResult`, `InvoiceListItem` |
| Port interfaces (для Infrastructure) | `Clock`, `UuidGenerator`, `EventPublisher`, `PasswordHasher` |

**НЕ входит:**

| Что | Почему |
|---|---|
| `#[ORM\Entity]` | Инфраструктура. |
| HTTP: Request, Response, Controller | Framework. |
| `EntityManager`, `flush()`, `commit()` | Инфраструктура. |
| `MessageBus::dispatch()` | Инфраструктура. |
| Twig, JSON, HTTP-статусы | Framework. |

**Зависимости:** `Domain` + свои Port interfaces.

```php
// src/Application/Invoice/MarkInvoicePaidUseCase.php
namespace App\Application\Invoice;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceRepository;
use App\Application\Invoice\Dto\MarkPaidCommand;
use App\Application\Invoice\Dto\InvoiceResult;
use App\Application\Shared\Clock;

final class MarkInvoicePaidUseCase
{
    public function __construct(
        private InvoiceRepository $invoices,
        private Clock             $clock,
    ) {}

    public function execute(MarkPaidCommand $cmd): InvoiceResult
    {
        $invoice = $this->invoices->findById($cmd->invoiceId);
        $invoice->markAsPaid($cmd->txId, $this->clock->now());
        $this->invoices->save($invoice);
        // НЕ flush(). Не commit(). Это — Infrastructure.
        return InvoiceResult::fromInvoice($invoice);
    }
}
```

**Вопрос:** «Кто вызывает `flush()` и `commit()`?»

Ответ: **Unit of Work** — это порт в Application, реализация — в Infrastructure.

```php
// Application/Port:
interface UnitOfWork {
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
    public function flush(): void;
}

// Infrastructure:
final class DoctrineUnitOfWork implements UnitOfWork {
    public function __construct(private EntityManagerInterface $em) {}
    public function beginTransaction(): void { $this->em->beginTransaction(); }
    public function commit(): void { $this->em->commit(); }
    public function rollBack(): void { $this->em->rollBack(); }
    public function flush(): void { $this->em->flush(); }
}
```

Теперь `UseCase` вызывает `$this->uow->beginTransaction()` — без `EntityManager`.

### Infrastructure — технологии

**Входит:**

| Что | Пример |
|---|---|
| Doctrine Entity | `InvoiceEntity` (`#[ORM\Entity]`) |
| Doctrine Repository (реализация) | `DoctrineInvoiceRepository implements InvoiceRepository` |
| Mapper (Domain ↔ Entity) | `InvoiceMapper` |
| Mailer, Search, Analytics | `SymfonyMailerNotifier`, `MeilisearchIndexer` |
| Message handlers | `SendWelcomeEmailHandler` (`#[AsMessageHandler]`) |
| Outbox relay | `OutboxPublishCommand` |
| Password hasher, Clock, Uuid | `SodiumPasswordHasher`, `SystemClock` |
| Outbox Entity | `OutboxEntity` (`#[ORM\Entity]`) |

**НЕ входит:**

| Что | Почему |
|---|---|
| Business logic | Domain. |
| Оркестрация use case'ов | Application. |
| HTTP-контроллеры | Framework. |

**Зависимости:** `Domain` (интерфейсы) + `Application` (ports) + vendor (Doctrine, Symfony Mailer, Meilisearch, ...).

### Framework — внешний мир

**Входит:**

| Что | Пример |
|---|---|
| Symfony Controller | `InvoiceController` |
| Request / Response DTO | `CreateInvoiceRequest`, `InvoiceResponse` |
| Security (Authenticator, Voter) | `ApiKeyAuthenticator`, `InvoiceVoter` |
| Exception listener (HTTP) | `GlobalExceptionHandler` |
| Console Command (вход) | `SeedCommand` |
| Event Listener (вход) | `OnBuyerRegisteredEmail` |
| Twig templates | `invoice/index.html.twig` |

**НЕ входит:**

| Что | Почему |
|---|---|
| Business logic | Domain. |
| Оркестрация | Application. |
| Doctrine-запросы | Infrastructure. |

**Зависимости:** `Application` (UseCase) + `Domain` (для Security Voter, типизации).

---

## Куда попадает каждый компонент

| Компонент | Слой | Почему |
|---|---|---|
| **Doctrine Entity** | Infrastructure | `#[ORM\Entity]` — технология. Не домен. |
| **Doctrine Repository** | Infrastructure | Реализация `InvoiceRepository` (интерфейс в Domain). |
| **EntityManager** | Infrastructure | «Как сохранять». Не «что сохранять». |
| **Symfony Controller** | Framework | «Переводит» HTTP → UseCase. |
| **Symfony Request/Response** | Framework | HTTP-формат. Не домен. |
| **Twig** | Framework | Представление. |
| **`#[AsMessageHandler]`** | Infrastructure | Работает с MessageBus — технология. |
| **`#[AsEventListener]`** | Framework (если синхронный) / Infrastructure (если асинхронный) | Зависит от того, что делает. |
| **`#[AsCommand]`** | Framework (вход) / Infrastructure (outbox relay) | Вход — Framework. Обработка — Infrastructure. |
| **Voter** | Framework | Зависит от `Token`, `Request`. |
| **Authenticator** | Framework | Зависит от `Request`, `Passport`. |
| **Security (roles)** | Framework + Domain | Роли — в Domain (на Entity). Проверка — в Framework. |
| **Outbox Entity** | Infrastructure | `#[ORM\Entity]`. Хранение событий. |
| **Outbox Relay** | Infrastructure | Публикация в MessageBus. |
| **`UnitOfWork`** | Port в Application / Реализация в Infrastructure | «Транзакция» — понятие Application. «`$em->flush()`» — Infrastructure. |
| **`MessageBus`** | Infrastructure | Symfony Messenger. Технология. |
| **`EventDispatcher`** | Port в Application / Реализация в Infrastructure | «Публиковать событие» — Application. «Как» — Infrastructure. |
| **`PasswordHasher`** | Port в Application / Реализация в Infrastructure | «Хэшировать» — Application. `sodium_*` — Infrastructure. |
| **`Clock`** | Port в Application / Реализация в Infrastructure | «Текущее время» — Application. `time()` — Infrastructure. |

---

## Правило зависимостей на практике

```
Разрешено:
  Framework    → Application  → Domain
  Infrastructure → Application → Domain

НЕ разрешено:
  Domain       → Application
  Domain       → Infrastructure
  Domain       → Framework
  Application  → Infrastructure  (только через Port interfaces)
  Application  → Framework

Конкретно:
  ✅ Domain\Invoice\Invoice.php        → use App\Domain\Shared\Money
  ✅ Application\...UseCase.php        → use App\Domain\Invoice\InvoiceRepository
  ✅ Infrastructure\...Repository.php  → use App\Domain\Invoice\Invoice
  ✅ Infrastructure\...Repository.php  → use App\Application\...UnitOfWork
  ✅ Framework\...Controller.php       → use App\Application\...UseCase

  ❌ Domain\Invoice\Invoice.php        → use Doctrine\ORM\Entity
  ❌ Domain\Invoice\Invoice.php        → use Symfony\Component\Security\...
  ❌ Application\...UseCase.php        → use Doctrine\ORM\EntityManagerInterface
  ❌ Application\...UseCase.php        → use App\Infrastructure\Persistence\...
  ❌ Application\...UseCase.php        → use Symfony\Component\Messenger\...
```

### Проверка: `composer.json`

```json
{
    "require": {
        "php": ">=8.2",
        "symfony/framework-bundle": "^7.0",
        "doctrine/orm": "^3.0",
        "doctrine/doctrine-bundle": "^2.10"
    }
}
```

Это **глобальный** `composer.json`. Domain **не может** изолироваться через composer (один пакет). Изоляция — через **пространство имён** + **стат-анализ** + **конвенцию**.

---

## Как следить, что границы не нарушаются

### 1. PHPStan с кастомными правилами

```neon
# phpstan.neon
parameters:
    level: 8
    paths:
        - src/
        - tests/

    ignoreErrors:
        # (пусто. Ноль игноров.)

    # Кастомное правило: Domain не может использовать Symfony/Doctrine
    # (через extension в composer.json)
```

```json
// composer.json
{
    "extra": {
        "symfony": {
            "require": "7.0-dev"
        }
    }
}
```

**Проще и надёжнее:** отдельный скрипт.

```php
#!/usr/bin/env php
<?php
// tools/check-architecture.php

$domainFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src/Domain')
);

$forbidden = [
    'Doctrine\\',
    'Symfony\\',
    'App\\Infrastructure\\',
    'App\\Framework\\',
    'App\\Application\\',
    'EntityManager',
    'ORM\\Entity',
    'ORM\\Column',
];

$errors = [];

foreach ($domainFiles as $file) {
    if ($file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    foreach ($forbidden as $pattern) {
        if (str_contains($content, $pattern)) {
            $errors[] = "{$file->getPathname()}: содержит '{$pattern}'";
        }
    }
}

$appFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src/Application')
);

$forbiddenApp = [
    'Doctrine\\',
    'App\\Infrastructure\\',
    'App\\Framework\\',
    'EntityManagerInterface',
    'MessageBus',
    'Symfony\\Component\\Messenger',
];

foreach ($appFiles as $file) {
    if ($file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    foreach ($forbiddenApp as $pattern) {
        if (str_contains($content, $pattern)) {
            $errors[] = "{$file->getPathname()}: содержит '{$pattern}'";
        }
    }
}

if ($errors !== []) {
    echo "ARCHITECTURE VIOLATIONS:\n";
    foreach ($errors as $e) echo "  ❌ {$e}\n";
    exit(1);
}

echo "✅ Architecture boundaries intact.\n";
```

```yaml
# GitHub Actions / GitLab CI
- name: Check architecture boundaries
  run: php tools/check-architecture.php
```

### 2. PHPStan — уровень 8 + `checkMissingIterableValueType`

```neon
parameters:
    level: 8
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
```

Это ловит:
- `@param array` без `array<key, value>`.
- `mixed` там, где должен быть тип.
- Нарушение контрактов.

### 3. PHPUnit: структура тестов отражает структуру кода

```
tests/
├── Unit/
│   ├── Domain/              ← БЕЗ моков. БЕЗ EntityManager.
│   │   ├── InvoiceTest.php  ← new Invoice(...), assert, throw
│   │   └── MoneyTest.php
│   ├── Application/         ← Моки портов. Не моки Doctrine.
│   │   └── MarkPaidUseCaseTest.php
│   └── Infrastructure/      ← Моки vendor. Реальные DTO.
│       └── DoctrineInvoiceRepositoryTest.php
│
├── Integration/
│   ├── DoctrineInvoiceRepositoryTest.php  ← реальная БД
│   └── MessengerFlowTest.php
│
└── E2E/
    └── InvoiceApiTest.php   ← HTTP
```

**Правило:** если тест Domain требует `EntityManager` — граница нарушена.

```php
// ✅ Domain тест — 0 зависимостей
final class InvoiceTest extends TestCase
{
    public function testCannotPayTwice(): void
    {
        $invoice = Invoice::new('INV-1', 'a@b.c');
        $invoice->addLineItem(LineItem::create('Item', Money::fromCents(1000, 'EUR'), 1));
        $invoice->markAsPaid('tx-1');

        $this->expectException(InvoiceAlreadyPaid::class);
        $invoice->markAsPaid('tx-2');
    }
}

// ✅ Application тест — моки портов
final class MarkPaidUseCaseTest extends TestCase
{
    public function testMarksInvoiceAsPaid(): void
    {
        $invoice  = Invoice::new('INV-1', 'a@b.c');
        $invoice->addLineItem(/* ... */);

        $repo = $this->createMock(InvoiceRepository::class);
        $repo->method('findById')->willReturn($invoice);

        $clock = $this->createMock(Clock::class);
        $clock->method('now')->willReturn(new \DateTimeImmutable('2024-01-15'));

        $uow = $this->createMock(UnitOfWork::class);

        $useCase = new MarkInvoicePaidUseCase($repo, $clock, $uow);
        $result  = $useCase->execute(new MarkPaidCommand(1, 'tx-1'));

        $this->assertSame(InvoiceStatus::Paid, $result->status);
    }
}
```

### 4. `arch` пакет (PHP)

```bash
composer require --dev dmtube/arch
```

```php
// arch.php
<?php

use dmtube\Arch\Rules;

Rules::for('App\Domain\*')
    ->shouldNotUse('Doctrine\*')
    ->shouldNotUse('Symfony\*')
    ->shouldNotUse('App\Infrastructure\*')
    ->shouldNotUse('App\Framework\*')
    ->shouldNotUse('App\Application\*')
    ->shouldBeVerified();

Rules::for('App\Application\*')
    ->shouldNotUse('Doctrine\*')
    ->shouldNotUse('App\Infrastructure\*')
    ->shouldNotUse('App\Framework\*')
    ->shouldBeVerified();

Rules::for('App\Infrastructure\*')
    ->shouldNotUse('App\Framework\*')
    ->shouldBeVerified();

Rules::for('App\Framework\*')
    ->shouldNotUse('App\Infrastructure\*')
    ->shouldBeVerified();
```

```bash
vendor/bin/arch check
```

Это **автоматизированная** проверка. В CI. Красный крестик — мердж запрещён.

### 5. Конвенция имён

| Слои | Имена |
|---|---|
| Domain | `Invoice`, `Money`, `InvoiceRepository` (interface), `InsufficientFunds` |
| Application | `MarkInvoicePaidUseCase`, `MarkPaidCommand`, `InvoiceResult`, `UnitOfWork` (interface) |
| Infrastructure | `DoctrineInvoiceRepository`, `InvoiceEntity`, `DoctrineUnitOfWork`, `MeilisearchIndexer` |
| Framework | `InvoiceController`, `CreateInvoiceRequest`, `InvoiceResponse`, `InvoiceVoter` |

Если имя не содержит «Entity», «UseCase», «Controller», «Repository» (в Infrastructure) — это подозрительно. Граница размывается.

### 6. `services.yaml` — DI как документация границ

```yaml
# config/services.yaml
services:
    # ── Domain: ноль DI-конфигурации ──
    # (Domain не зарегистрирована в DI. Чистые классы.)

    # ── Application: UseCase'ы, порты ──
    App\Application\Invoice\CreateInvoiceUseCase:
        arguments:
            $invoices: '@App\Domain\Invoice\InvoiceRepository'  # интерфейс!
            $clock:    '@App\Application\Shared\Clock'          # интерфейс!
            $uow:      '@App\Application\Shared\UnitOfWork'     # интерфейс!

    App\Application\Invoice\MarkInvoicePaidUseCase:
        arguments:
            $invoices: '@App\Domain\Invoice\InvoiceRepository'
            $clock:    '@App\Application\Shared\Clock'
            $uow:      '@App\Application\Shared\UnitOfWork'

    # ── Infrastructure: реализация портов ──
    App\Domain\Invoice\InvoiceRepository:  # alias
        alias: 'App\Infrastructure\Persistence\Repository\DoctrineInvoiceRepository'

    App\Application\Shared\Clock:
        alias: 'App\Infrastructure\Time\SystemClock'

    App\Application\Shared\UnitOfWork:
        alias: 'App\Infrastructure\Persistence\DoctrineUnitOfWork'

    # ── Framework: адаптеры ──
    App\Framework\Http\Controller\InvoiceController:
        arguments:
            $createInvoice: '@App\Application\Invoice\CreateInvoiceUseCase'
            $markPaid:      '@App\Application\Invoice\MarkInvoicePaidUseCase'
```

**Визуально видно:** Controller → UseCase → Domain interface → Infrastructure implementation. Стрелки наружу. Никогда внутрь.

### 7. Code review checklist

```
При merge request:

□ Новый класс в Domain/ — есть `use Symfony` или `use Doctrine`?
  → Да → отклонить.

□ Новый класс в Application/ — есть `EntityManager` или `MessageBus`?
  → Да → отклонить. Заменить на Port.

□ Новый класс в Infrastructure/ — содержит бизнес-логику?
  → Да → перенести в Domain или Application.

□ Новый UseCase — вызывает `flush()` / `commit()` напрямую?
  → Да → использовать `UnitOfWork` порт.

□ Controller содержит бизнес-логику?
  → Да → перенести в UseCase.

□ Test в tests/Unit/Domain/ — требует `EntityManager`?
  → Да → тест неправильный. Domain не зависит от BDD.

□ `services.yaml` — UseCase зависит от конкретной реализации?
  → Да → заменить на интерфейс.
```

---

## Полный поток запроса

```
HTTP Request: POST /api/invoices/42/pay
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ Framework\InvoiceController                                 │
│                                                             │
│ 1. Валидация Request (Symfony Validator)                    │
│ 2. Security: isGranted('INVOICE_PAY', $invoice)            │
│ 3. Create MarkPaidCommand(invoiceId: 42, txId: 'tx-abc')   │
│ 4. $this->markPaid->execute($cmd)  ← вызов Application     │
│ 5. InvoiceResult → InvoiceResponse → JSON                   │
│                                                             │
│ Зависимости: Application (UseCase), Domain (Voter)         │
│ НЕ: Infrastructure, Doctrine                                 │
└─────────────────────────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ Application\MarkInvoicePaidUseCase                          │
│                                                             │
│ 1. $invoice = $this->invoices->findById(42)                │
│    ← InvoiceRepository (интерфейс из Domain)               │
│ 2. $invoice->markAsPaid('tx-abc', $this->clock->now())    │
│    ← Domain: валидация, статус, событие                     │
│ 3. $this->invoices->save($invoice)                          │
│ 4. foreach ($invoice->pullEvents() as $e)                   │
│      $this->events->publish($e)                             │
│      ← EventPublisher (порт)                                │
│ 5. $this->uow->beginTransaction()                           │
│    ← UnitOfWork (порт)                                      │
│ 6. return InvoiceResult::fromInvoice($invoice)              │
│                                                             │
│ Зависимости: Domain (Invoice, InvoiceRepository)           │
│            Application (ports: Clock, UoW, EventPublisher)  │
│ НЕ: Infrastructure, Doctrine, Symfony                       │
└─────────────────────────────────────────────────────────────┘
  │
  ▼  (через DI-контейнер: alias → реализация)
┌─────────────────────────────────────────────────────────────┐
│ Infrastructure\DoctrineInvoiceRepository                    │
│                                                             │
│ 1. findById: $this->em->getRepository(InvoiceEntity::class) │
│ 2. toDomain(InvoiceEntity) → Invoice (Domain)               │
│ 3. save: toDomain(Invoice) → InvoiceEntity                  │
│    $this->em->persist($entity)                               │
│                                                             │
│ Зависимости: Domain (Invoice), Infrastructure (Entity)      │
│ Реализует: InvoiceRepository (интерфейс из Domain)         │
└─────────────────────────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ Infrastructure\DoctrineUnitOfWork                           │
│                                                             │
│ beginTransaction: $this->em->beginTransaction()             │
│ commit:           $this->em->commit()                       │
│ rollBack:         $this->em->rollBack()                     │
│ flush:            $this->em->flush()                        │
│                                                             │
│ Реализует: UnitOfWork (порт из Application)                 │
└─────────────────────────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ Infrastructure\OutboxPublisher                              │
│                                                             │
│ 1. INSERT INTO outbox (event_type, payload, status)         │
│ 2. (в той же транзакции, что и invoice)                      │
│                                                             │
│ Реализует: EventPublisher (порт из Application)             │
└─────────────────────────────────────────────────────────────┘
  │
  ▼  (асинхронно, позже)
┌─────────────────────────────────────────────────────────────┐
│ Infrastructure\OutboxPublishCommand (worker)                │
│                                                             │
│ 1. SELECT FROM outbox WHERE status = 'new'                  │
│ 2. UPDATE outbox SET status = 'published'                   │
│ 3. $this->bus->dispatch(new OutboxMessage($event))          │
│                                                             │
│ Зависимости: MessageBus (Symfony), OutboxEntity             │
└─────────────────────────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ Infrastructure\Handler\SendInvoicePaidEmailHandler          │
│                                                             │
│ #[AsMessageHandler]                                         │
│ 1. Загружает данные                                         │
│ 2. $this->mailer->send(...)                                 │
│                                                             │
│ Зависимости: Mailer, Repository                              │
└─────────────────────────────────────────────────────────────┘
```

---

## Честная оговорка

Чистая архитектура — это **не** «4 каталога и всё». Это **правило**: «внутреннее не знает о внешнем». Структура каталогов — следствие, а не причина.

Для проекта на 3 разработчика и 2 bounded context — 4 слоя с `UseCase`, `Port`, `UnitOfWork`, `Mapper` — это **overhead**. Время на написание и понимание > времени на изменение.

Для проекта на 10+ разработчиков, 5+ BC, 3 года жизни — **must have**. Без этого: «кто вызывает `flush()`?», «где бизнес-логика?», «почему Controller знает про Redis?» — каждый день.

**Критерий:** если вы можете нарисовать Dependency Rule на салфетке и каждый разработчик знает, куда положить новый класс — структура работает. Если каждый раз спрашиваете «а где это лежит?» — структура не работает.

Начинайте с Domain. Он не зависит от ничего. Напишите его. Тестируйте. Только потом — Application. Только потом — Infrastructure. Только потом — Framework. В этом порядке. Не наоборот.

> ⏱ 76.2s · prompt 102463 tok · output 9255 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about a testing strategy for a Symfony application with PostgreSQL. They want to know:
1. Test pyramid approach
2. Types of tests
3. What to mock vs what not to mock
4. How to keep tests fast and stable

Let me structure this comprehensively, building on everything we've discussed (clean architecture, domain events, Doctrine, DDD). I should give concrete code examples, practical trade-offs, and specific tools. I'll follow the established format of the conversation - detailed, code-heavy, with tables and clear structure.

Key areas to cover:
- Pyramid shape for Symfony+PostgreSQL specifically
- Unit tests (Domain, Application) - pure PHP, no mocks for Domain
- Integration tests (Infrastructure) - real DB with Testcontainers
- E2E/API tests - full Symfony kernel
- What to mock (external services, time, randomness) vs what NOT to mock (Doctrine, DB for integration)
- Keeping tests fast: parallel execution, Testcontainers reuse, avoiding full kernel where possible
- Keeping tests stable: avoiding flaky tests, deterministic data, proper isolation
- Specific tools: PHPUnit, Prophecy, Testcontainers, Symfony Panther, DataProviders
- CI integration
- Common anti-patterns in Symfony testing

Let me write this in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Стратегия тестирования Symfony + PostgreSQL

## Пирамида: не абстракция, а конкретные числа

```
         ┌──────────────┐
         │  E2E / API   │  5–15 тестов
         │  (ядро)      │  Полный kernel, реальная БД
         └──────┬───────┘
                │
        ┌───────┴────────┐
        │ Integration    │  30–80 тестов
        │ (пояс)         │  Реальная БД, моки внешних сервисов
        └───────┬────────┘
                │
  ┌─────────────┴─────────────────┐
  │  Unit                         │  300–2000 тестов
  │  (база)                       │  Чистый PHP. Без БД. Без kernel.
  └───────────────────────────────┘
```

**Пропорция:** 80% unit, 15% integration, 5% E2E.

**Время:** 80% < 50 мс, 15% < 500 мс, 5% < 3 с.

**Почему не «всё E2E»:** 50 E2E-тестов × 2 с = 100 с на каждый push. Команда перестанет запускать тесты. Пирамида — не догма, а компромисс: много дешёвых + мало дорогих.

---

## Уровень 1: Unit (база)

### 1a. Domain — чистый PHP, ноль зависимостей

```php
// tests/Unit/Domain/Invoice/InvoiceTest.php
final class InvoiceTest extends TestCase
{
    public function testCannotPayAlreadyPaidInvoice(): void
    {
        $invoice = Invoice::new('INV-1', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(
            LineItem::create('Widget', Money::fromCents(5000, 'EUR'), 1)
        );
        $invoice->markAsPaid('tx-001');

        $this->expectException(InvoiceAlreadyPaid::class);
        $this->expectExceptionMessage('Invoice INV-1 is already paid');

        $invoice->markAsPaid('tx-002');
    }

    public function testCannotAddLineItemAfterVoid(): void
    {
        $invoice = Invoice::new('INV-2', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('Widget', Money::fromCents(1000, 'EUR'), 1));
        $invoice->void('Duplicate');

        $this->expectException(InvoiceVoided::class);
        $invoice->addLineItem(LineItem::create('Gadget', Money::fromCents(2000, 'EUR'), 2));
    }

    public function testTotalIsSumOfAllLineItems(): void
    {
        $invoice = Invoice::new('INV-3', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('A', Money::fromCents(1000, 'EUR'), 1));
        $invoice->addLineItem(LineItem::create('B', Money::fromCents(2500, 'EUR'), 2));

        $this->assertTrue($invoice->total()->equals(Money::fromCents(6000, 'EUR')));
    }

    public function testCannotCreateWithNegativeAmount(): void
    {
        $this->expectException(NegativeAmount::class);
        LineItem::create('X', Money::fromCents(-1, 'EUR'), 1);
    }

    /**
     * @dataProvider provideCurrencyMismatchCases
     */
    public function testCurrencyMismatchThrows(string $invoiceCur, string $itemCur): void
    {
        $invoice = Invoice::new('INV-4', 'a@b.c', Money::fromCents(0, $invoiceCur));

        $this->expectException(CurrencyMismatch::class);
        $invoice->addLineItem(LineItem::create('X', Money::fromCents(100, $itemCur), 1));
    }

    public static function provideCurrencyMismatchCases(): iterable
    {
        yield 'EUR vs KZT' => ['EUR', 'KZT'];
        yield 'KZT vs USD' => ['KZT', 'USD'];
        yield 'USD vs EUR' => ['USD', 'EUR'];
    }
}
```

**Что здесь нет:**
- `EntityManager`.
- `createMock()`.
- `Kernel`.
- `@testWith` / `@dataProvider` — только чистые PHP-объекты.
- `sleep()`, `mt_rand()`, `time()`.

**Время:** 1 тест < 1 мс. 500 тестов < 2 с.

### 1b. Domain Services — чистая логика

```php
// tests/Unit/Domain/Invoice/PricingPolicyTest.php
final class PricingPolicyTest extends TestCase
{
    private PricingPolicy $policy;

    protected function setUp(): void
    {
        $this->policy = new PricingPolicy();
    }

    public function testVipGetsTenPercent(): void
    {
        $order = $this->makeOrder(subtotal: 10_000, tags: ['vip'], itemCount: 1);
        $this->assertSame(1_000, $this->policy->discount($order));
    }

    public function testBulkFiveItemsGetsFivePercent(): void
    {
        $order = $this->makeOrder(subtotal: 5_000, tags: [], itemCount: 5);
        $this->assertSame(250, $this->policy->discount($order));
    }

    public function testVipTakesPriorityOverBulk(): void
    {
        $order = $this->makeOrder(subtotal: 5_000, tags: ['vip'], itemCount: 10);
        $this->assertSame(500, $this->policy->discount($order));  // 10%, не 5%
    }

    public function testNoTagsNoBulkGetsZero(): void
    {
        $order = $this->makeOrder(subtotal: 5_000, tags: [], itemCount: 2);
        $this->assertSame(0, $this->policy->discount($order));
    }

    private function makeOrder(int $subtotal, array $tags, int $count): Order
    {
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = new OrderItem("Item {$i}", Money::fromCents(intdiv($subtotal, $count), 'EUR'), 1);
        }
        return new Order(0, 'ORD-1', $items, $tags);
    }
}
```

### 1c. Application — моки портов (не Doctrine)

```php
// tests/Unit/Application/Invoice/MarkInvoicePaidUseCaseTest.php
final class MarkInvoicePaidUseCaseTest extends TestCase
{
    public function testMarksInvoiceAsPaidAndPublishesEvent(): void
    {
        // Arrange
        $invoice = Invoice::new('INV-1', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('X', Money::fromCents(5000, 'EUR'), 1));

        $repo = $this->createMock(InvoiceRepository::class);
        $repo->method('findById')->with(1)->willReturn($invoice);
        $repo->expects(self::once())->method('save')->with($invoice);

        $clock = $this->createMock(Clock::class);
        $clock->method('now')->willReturn(new \DateTimeImmutable('2024-06-15T12:00:00Z'));

        $events = $this->createMock(EventPublisher::class);
        $events->expects(self::once())
            ->method('publish')
            ->with(self::callback(
                fn ($e) => $e instanceof InvoicePaidEvent && $e->invoiceId === 0
            ));

        $uow = $this->createMock(UnitOfWork::class);
        $uow->expects(self::once())->method('beginTransaction');
        $uow->expects(self::once())->method('commit');

        // Act
        $useCase = new MarkInvoicePaidUseCase($repo, $clock, $events, $uow);
        $result = $useCase->execute(new MarkPaidCommand(invoiceId: 1, txId: 'tx-42'));

        // Assert
        $this->assertSame(InvoiceStatus::Paid, $result->status);
        $this->assertSame('tx-42', $result->txId);
    }

    public function testRollsBackOnDomainException(): void
    {
        $invoice = Invoice::new('INV-2', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->markAsPaid('tx-1');  // уже оплачен

        $repo = $this->createMock(InvoiceRepository::class);
        $repo->method('findById')->willReturn($invoice);

        $uow = $this->createMock(UnitOfWork::class);
        $uow->expects(self::once())->method('rollBack');
        $uow->expects(self::never())->method('commit');

        $useCase = new MarkInvoicePaidUseCase($repo, /* ... */, $uow);

        $this->expectException(InvoiceAlreadyPaid::class);
        $useCase->execute(new MarkPaidCommand(invoiceId: 2, txId: 'tx-2'));
    }

    public function testInsufficientFundsIsNotRolledBackTwice(): void
    {
        // ...
    }
}
```

**Ключевой принцип:** мокируем **порты** (`InvoiceRepository`, `Clock`, `UnitOfWork`), а **не** `EntityManager`, **не** `Doctrine\ORM\EntityRepository`.

---

## Уровень 2: Integration (пояс)

### Что тестируем

| Компонент | Тестируем | Как |
|---|---|---|
| `DoctrineInvoiceRepository` | save, findById, delete | Реальная PostgreSQL. Мок только внешние сервисы. |
| `DoctrineUnitOfWork` | beginTransaction, commit, rollBack | Реальная PostgreSQL. |
| `DoctrineInvoiceMapper` | Domain → Entity → Domain (roundtrip) | Без БД. Чистый маппинг. |
| `OutboxPublisher` | INSERT into outbox в транзакции | Реальная PostgreSQL. |
| `MessageBus` (sync) | dispatch → handler вызов | `sync://` transport. |
| `PasswordHasher` | hash → verify | Реальная `sodium`. |

### Testcontainers: реальная PostgreSQL

```php
// tests/Integration/TestCase/PostgresTestCase.php
namespace Tests\Integration\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class PostgresTestCase extends KernelTestCase
{
    protected static ?\PDO $pdo = null;

    protected static function setUpPostgres(): void
    {
        if (self::$pdo !== null) return;

        // Testcontainers запускает PostgreSQL (или поднимает из Docker Compose)
        // DSN из .env.test:
        // DATABASE_URL=postgresql://test:test@localhost:5432/test_db
        $dsn = self::getContainer()->getParameter('database_url');

        self::$pdo = new \PDO($dsn, 'test', 'test', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
    }

    protected function truncateAllTables(): void
    {
        self::setUpPostgres();

        $tables = self::$pdo->query(
            "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        self::$pdo->exec('SET CONSTRAINTS ALL DEFERRED');
        foreach ($tables as $table) {
            self::$pdo->exec("TRUNCATE TABLE {$table} CASCADE");
        }
    }
}
```

```php
// tests/Integration/Persistence/DoctrineInvoiceRepositoryTest.php
final class DoctrineInvoiceRepositoryTest extends PostgresTestCase
{
    private DoctrineInvoiceRepository $repo;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->truncateAllTables();

        $this->em   = self::getContainer()->get('doctrine')->getManager();
        $this->repo = new DoctrineInvoiceRepository($this->em);
    }

    public function testSaveAndFindById(): void
    {
        // Arrange
        $invoice = Invoice::new('INV-100', 'buyer@test.com', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('Widget', Money::fromCents(5000, 'EUR'), 2));

        // Act
        $this->repo->save($invoice);
        $this->em->flush();

        $found = $this->repo->findById($invoice->id());

        // Assert
        $this->assertSame('INV-100', $found->number());
        $this->assertTrue($found->total()->equals(Money::fromCents(10000, 'EUR')));
        $this->assertCount(1, $found->lineItems());
    }

    public function testSaveWithLineItemsPersistsAll(): void
    {
        $invoice = Invoice::new('INV-101', 'buyer@test.com', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('A', Money::fromCents(1000, 'EUR'), 1));
        $invoice->addLineItem(LineItem::create('B', Money::fromCents(2000, 'EUR'), 3));
        $invoice->addLineItem(LineItem::create('C', Money::fromCents(500, 'EUR'), 5));

        $this->repo->save($invoice);
        $this->em->flush();

        $found = $this->repo->findById($invoice->id());

        $this->assertCount(3, $found->lineItems());
        $this->assertTrue($found->total()->equals(Money::fromCents(1000 + 6000 + 2500, 'EUR')));
    }

    public function testOrphanRemovalDeletesLineItems(): void
    {
        $invoice = Invoice::new('INV-102', 'buyer@test.com', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('A', Money::fromCents(1000, 'EUR'), 1));
        $invoice->addLineItem(LineItem::create('B', Money::fromCents(2000, 'EUR'), 1));

        $this->repo->save($invoice);
        $this->em->flush();

        $found = $this->repo->findById($invoice->id());
        $found->removeLineItem(/* id первого lineItem */);
        $this->repo->save($found);
        $this->em->flush();

        $refound = $this->repo->findById($invoice->id());
        $this->assertCount(1, $refound->lineItems());
    }

    public function testUniqueConstraintOnNumber(): void
    {
        $inv1 = Invoice::new('INV-DUP', 'a@b.c', Money::fromCents(0, 'EUR'));
        $this->repo->save($inv1);
        $this->em->flush();

        $inv2 = Invoice::new('INV-DUP', 'x@y.z', Money::fromCents(0, 'EUR'));
        $this->repo->save($inv2);

        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->em->flush();
    }

    public function testCheckConstraintPreventsNegativeAmount(): void
    {
        $entity = new InvoiceEntity();
        // ... set fields ...
        $entity->setTotalCents(-1);  // нарушает CHECK (total_cents >= 0)

        $this->em->persist($entity);

        $this->expectException(\Doctrine\DBAL\Exception\InvalidArgumentException::class);
        $this->em->flush();
    }
}
```

### Outbox: атомарность aggregate + событие

```php
// tests/Integration/Persistence/OutboxAtomicityTest.php
final class OutboxAtomicityTest extends PostgresTestCase
{
    public function testEventAndAggregateCommitTogether(): void
    {
        $invoice = Invoice::new('INV-200', 'a@b.c', Money::fromCents(0, 'EUR'));
        $invoice->addLineItem(LineItem::create('X', Money::fromCents(1000, 'EUR'), 1));
        $invoice->markAsPaid('tx-1');

        $this->repo->save($invoice);
        $this->eventPublisher->publish($invoice->pullEvents()[0]);
        $this->em->flush();
        $this->em->commit();

        // Оба в БД:
        $this->assertNotNull($this->pdo->query(
            "SELECT 1 FROM invoice WHERE number = 'INV-200'"
        )->fetchColumn());

        $this->assertNotNull($this->pdo->query(
            "SELECT 1 FROM outbox WHERE event_type = 'InvoicePaidEvent'"
        )->fetchColumn());
    }

    public function testEventAndAggregateRollBackTogether(): void
    {
        $invoice = Invoice::new('INV-201', 'a@b.c', Money::fromCents(0, 'EUR'));
        // Намеренно нарушаем constraint:
        $invoice->markAsPaid('tx-1');
        $invoice->markAsPaid('tx-2');  // ← бросит InvoiceAlreadyPaid

        $this->expectException(InvoiceAlreadyPaid::class);

        try {
            $this->repo->save($invoice);
            $this->eventPublisher->publish(new InvoicePaidEvent(/* ... */));
            $this->em->flush();
        } catch (InvoiceAlreadyPaid $e) {
            $this->em->rollBack();
            throw $e;
        }

        // Ничего в БД:
        $this->assertNull($this->pdo->query(
            "SELECT 1 FROM invoice WHERE number = 'INV-201'"
        )->fetchColumn());
    }
}
```

---

## Уровень 3: E2E / API (ядро)

### Что тестируем

**Только критические сценарии.** Не «каждый endpoint». А «путь, который сломает бизнес».

```
1. Регистрация → подтверждение email → вход → первый заказ
2. Создание счёта → добавление позиций → оплата → подтверждение
3. Отмена счёта до оплаты
4. Двойная оплата → идемпотентность
5. Access denied: пользователь A не видит счёт пользователя B
```

5–10 тестов. Не 50.

### Symfony Panther (или HTTP Client)

```php
// tests/E2E/InvoiceApiTest.php
final class InvoiceApiTest extends KernelTestCase
{
    private \Http\Client\Common\HttpClient $client;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->client = self::getContainer()->get('http_client');

        // Очистка БД (через Testcontainers / Docker)
        $this->truncateTables();
    }

    public function testFullInvoiceLifecycle(): void
    {
        // 1. Регистрация
        $res = $this->client->request('POST', '/api/buyers', [
            'json' => ['email' => 'buyer@test.com', 'password' => 'secret123'],
        ]);
        $this->assertSame(201, $res->getStatusCode());
        $buyerId = json_decode($res->getContent(), true)['id'];

        // 2. Создание счёта
        $res = $this->client->request('POST', '/api/invoices', [
            'json' => [
                'number' => 'INV-E2E-1',
                'buyer_id' => $buyerId,
                'currency' => 'EUR',
                'line_items' => [
                    ['description' => 'Widget', 'amount_cents' => 5000, 'quantity' => 2],
                ],
            ],
            'headers' => ['Authorization' => "Bearer {$this->tokenFor($buyerId)}"],
        ]);
        $this->assertSame(201, $res->getStatusCode());
        $data = json_decode($res->getContent(), true);
        $invoiceId = $data['id'];
        $this->assertSame(10000, $data['total_cents']);

        // 3. Оплатить
        $res = $this->client->request('POST', "/api/invoices/{$invoiceId}/pay", [
            'json' => ['tx_id' => 'tx-e2e-001'],
            'headers' => ['Authorization' => "Bearer {$this->tokenFor($buyerId)}"],
        ]);
        $this->assertSame(200, $res->getStatusCode());
        $data = json_decode($res->getContent(), true);
        $this->assertSame('paid', $data['status']);

        // 4. Повторная оплата → 409
        $res = $this->client->request('POST', "/api/invoices/{$invoiceId}/pay", [
            'json' => ['tx_id' => 'tx-e2e-002'],
            'headers' => ['Authorization' => "Bearer {$this->tokenFor($buyerId)}"],
        ]);
        $this->assertSame(409, $res->getStatusCode());
    }

    public function testCannotAccessOthersInvoice(): void
    {
        // 1. Два покупателя
        $buyer1 = $this->createBuyer('a@test.com');
        $buyer2 = $this->createBuyer('b@test.com');

        // 2. Покупатель 1 создаёт счёт
        $invoiceId = $this->createInvoice($buyer1, 'INV-OTHER');

        // 3. Покупатель 2 пытается посмотреть
        $res = $this->client->request('GET', "/api/invoices/{$invoiceId}", [
            'headers' => ['Authorization' => "Bearer {$this->tokenFor($buyer2)}"],
        ]);
        $this->assertSame(403, $res->getStatusCode());
    }

    public function testIdempotencyKeyPreventsDoublePayment(): void
    {
        $buyer = $this->createBuyer('idem@test.com');
        $invoiceId = $this->createInvoice($buyer, 'INV-IDEM');

        $headers = [
            'Authorization' => "Bearer {$this->tokenFor($buyer)}",
            'Idempotency-Key' => 'unique-key-123',
        ];

        // Первый запрос:
        $res1 = $this->client->request('POST', "/api/invoices/{$invoiceId}/pay", [
            'json' => ['tx_id' => 'tx-1'],
            'headers' => $headers,
        ]);
        $this->assertSame(200, $res1->getStatusCode());

        // Второй запрос (retry):
        $res2 = $this->client->request('POST', "/api/invoices/{$invoiceId}/pay", [
            'json' => ['tx_id' => 'tx-1'],
            'headers' => $headers,
        ]);
        $this->assertSame(200, $res2->getStatusCode());

        // Одна запись в outbox:
        $count = $this->pdo->query(
            "SELECT count(*) FROM outbox WHERE payload->>'invoice_id' = '{$invoiceId}'"
        )->fetchColumn();
        $this->assertSame('1', $count);
    }
}
```

---

## Что мокаем, а что нет

### Мокаем (внешние, несущественные для тестируемого)

| Мокаем | Почему |
|---|---|
| `Clock` / `time()` | Детерминизм. Тест не зависит от текущего времени. |
| `UuidGenerator` / `random_bytes()` | Детерминизм. Тест воспроизводим. |
| `MailerInterface` | SMTP — внешний сервис. Медленный. Нестабильный. |
| `SearchIndexer` | Meilisearch — внешний сервис. |
| `AnalyticsClient` | Внешний HTTP. |
| `PaymentGateway` | Внешний HTTP. Невозможно вызвать в тесте. |
| `EventPublisher` (в Unit) | Мы тестируем UseCase, не публикацию. |
| `UnitOfWork` (в Unit) | Мы тестируем логику, не транзакцию. |

### НЕ мокаем

| НЕ мокаем | Почему |
|---|---|
| `InvoiceRepository` (в Integration) | Реальная БД. Тестируем маппинг, cascade, constraints. |
| `EntityManager` (в Integration) | Реальный flush, commit, rollback. |
| `Doctrine` Entity | Реальный маппинг. Реальные constraints. |
| `PasswordHasher` (sodium) | Реальная хэшировка. Быстро. Детерминировано. |
| `Money`, `Invoice`, `LineItem` (Domain) | Чистый PHP. Нет зависимости. Мокить = тестировать мок. |
| `PricingPolicy` (Domain Service) | Чистая логика. Мок = «а вдруг логика неверна?» |

### Правило

```
Мок — это «внешний мир, который не контролируем».
НЕ мок — это «наш код, который тестируем».

Граница: где заканчивается тестируемый компонент и начинается внешний мир.

Unit:       тестируем Domain / Application → моки портов
Integration: тестируем Infrastructure → реальная БД, моки внешних HTTP
E2E:        тестируем весь стек → реальная БД, моки только Payment Gateway
```

### Антипаттерн: «сломанный мок»

```php
// ❌ Мокаем InvoiceRepository и проверяем, что он «работает»
$repo = $this->createMock(InvoiceRepository::class);
$repo->method('save')->willReturn(null);  // «ага, сохранил»
$repo->method('findById')->willReturn($invoice);  // «ага, нашёл»

// Этот тест проверяет, что МОК работает. А не код.
// Интеграционный тест с реальной БД — проверяет код.
```

```php
// ✅ Unit: моки портов, тестируем UseCase
$repo = $this->createMock(InvoiceRepository::class);
$repo->method('findById')->willReturn($invoice);
// UseCase вызывает $invoice->markAsPaid() → доменную логику
// UseCase вызывает $repo->save() → мок
// UseCase вызывает $uow->commit() → мок
// Тестируем: ОРКЕСТРАЦИЮ. Не БД. Не Doctrine.

// ✅ Integration: реальная БД, тестируем Repository
$repo = new DoctrineInvoiceRepository($this->em);
$repo->save($invoice);
$this->em->flush();
$found = $repo->findById($id);
// Тестируем: МАППИНГ, CASCADE, CONSTRAINTS.
```

---

## Быстрые тесты: конкретные приёмы

### 1. Unit < 1 мс

```php
// ✅ 0 зависимостей, 0 I/O, 0 сеть
$money = Money::fromCents(5000, 'EUR');
$this->assertTrue($money->equals(Money::fromCents(5000, 'EUR')));
// Время: 0.003 мс

// ✅ Моки портов — в памяти
$clock = $this->createMock(Clock::class);
$clock->method('now')->willReturn(new \DateTimeImmutable('2024-01-01'));
// Время: 0.005 мс

// ❌ В Unit: $this->em->flush() → 50 мс
// ❌ В Unit: $this->client->request('GET', '/') → 200 мс
```

### 2. Parallel execution (PHPUnit 10+)

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="E2E">
            <directory>tests/E2E</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

```yaml
# GitHub Actions: параллельный запуск
jobs:
  test:
    strategy:
      matrix:
        suite: [unit, integration, e2e]
    steps:
      - name: Run tests
        run: |
          case ${{ matrix.suite }} in
            unit)        vendor/bin/phpunit --testsuite Unit ;;
            integration) vendor/bin/phpunit --testsuite Integration ;;
            e2e)         vendor/bin/phpunit --testsuite E2E ;;
          esac
```

```
Unit:        500 тестов × 1 мс = 0.5 с (1 процесс)
Integration:  50 тестов × 200 мс = 10 с (1 процесс, 1 БД)
E2E:          10 тестов × 2 с = 20 с (1 процесс, 1 БД)

Всего: 30 с (последовательно) или 20 с (параллельно, Unit + Integration + E2E)
```

### 3. Testcontainers: одна PostgreSQL на все Integration тесты

```yaml
# docker-compose.test.yml
services:
  postgres:
    image: postgres:16
    environment:
      POSTGRES_USER: test
      POSTGRES_PASSWORD: test
      POSTGRES_DB: test_db
    ports:
      - "5433:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U test"]
      interval: 1s
      timeout: 2s
      retries: 10
```

```yaml
# GitHub Actions
- name: Start PostgreSQL
  uses: docker://postgres:16
  env:
    POSTGRES_USER: test
    POSTGRES_PASSWORD: test
    POSTGRES_DB: test_db
  ports:
    - 5433:5432
  options: >
    --health-cmd="pg_isready -U test"
    --health-interval=1s
    --health-timeout=2s
    --health-retries=10
```

**Один контейнер** на все Integration + E2E тесты. Не 50 контейнеров.

### 4. Truncate, не migrate

```php
// ✅ Быстро: TRUNCATE (1–5 мс)
protected function setUp(): void
{
    $this->truncateTables();  // TRUNCATE ... CASCADE
}

// ❌ Медленно: drop + recreate (500 мс)
// ❌ Медленно: migrate up + down (2 с)
```

```sql
-- TRUNCATE: 2 мс
TRUNCATE TABLE invoice, invoice_line_item, buyer, outbox, messenger_messages CASCADE;

-- Migrate: 2000 мс
DROP TABLE invoice;
DROP TABLE invoice_line_item;
-- ... 15 таблиц ...
CREATE TABLE invoice (...);
-- ... 15 таблиц ...
```

### 5. `sync://` transport для Unit/Application тестов

```yaml
# .env.test
MESSENGER_TRANSPORT_DSN=sync://
```

```php
// В Unit: сообщения обрабатываются немедленно, в памяти.
// В Integration: реальная очередь (Redis / RabbitMQ) или sync://.
// В E2E: реальная очередь.
```

### 6. Не запускаем Kernel, если не нужно

```php
// ✅ Unit: без Kernel
final class MoneyTest extends TestCase { ... }

// ✅ Application: без Kernel
final class MarkPaidUseCaseTest extends TestCase { ... }

// ❌ НЕ нужно:
// final class MoneyTest extends KernelTestCase { ... }
// Зачем Kernel для Money?
```

```php
// ✅ Integration: Kernel нужен (DI, Doctrine)
final class DoctrineInvoiceRepositoryTest extends PostgresTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();  // нужен для EntityManager
        // ...
    }
}

// ✅ E2E: Kernel нужен (HTTP, Security, Router)
final class InvoiceApiTest extends KernelTestCase { ... }
```

---

## Стабильные тесты: убить флейк

### 1. Детерминизм

```php
// ❌ Флейк: зависит от текущего времени
public function testInvoiceCreatedToday(): void
{
    $invoice = Invoice::new('INV-1', 'a@b.c', Money::fromCents(0, 'EUR'));
    // $invoice->issuedAt — new \DateTimeImmutable()
    // Если тест запустится в 23:59:59.5 — дата может быть «завтра»
    $this->assertSame(date('Y-m-d'), $invoice->issuedAt()->format('Y-m-d'));
}

// ✅ Детерминизм: фиксированное время
public function testInvoiceCreatedAtGivenTime(): void
{
    $clock = new FixedClock(new \DateTimeImmutable('2024-06-15T12:00:00Z'));
    $invoice = Invoice::new('INV-1', 'a@b.c', Money::fromCents(0, 'EUR'), $clock);
    $this->assertSame('2024-06-15', $invoice->issuedAt()->format('Y-m-d'));
}
```

```php
// ❌ Флейк: случайный UUID
$invoice->id  // Uuid::v4() → каждый раз другой

// ✅ Детерминизм: фиксированный UUID
$generator = new FixedUuidGenerator('a1b2c3d4-...');
```

### 2. Нет `sleep()`

```php
// ❌
usleep(100000);  // «ну, чтобы worker успел»

// ✅
// Worker — в отдельном процессе. Тест ждёт результат через polling:
$deadline = microtime(true) + 5.0;
while (microtime(true) < $deadline) {
    $result = $this->repo->findById($invoiceId);
    if ($result->status() === 'paid') break;
    usleep(50000);  // 50 мс polling
}
$this->assertSame('paid', $result->status());
```

### 3. Нет зависимостей от порядка

```php
// ❌ Тест B зависит от данных, созданных тестом A
public function testA(): void { $this->createBuyer('a@test.com'); }
public function testB(): void { $this->findBuyer('a@test.com'); }  // ломается если A не прошёл

// ✅ Каждый тест — самодостаточный
protected function setUp(): void
{
    $this->truncateTables();  // чистая БД
}
```

### 4. Нет сетевых вызовов (кроме E2E)

```php
// ❌ Integration тест вызывает Meilisearch
$this->search->upsert('invoice', 1, [...]);  // HTTP → 200 мс, может упасть

// ✅ Integration: мок SearchIndexer
$search = $this->createMock(SearchIndexer::class);
$search->expects(self::once())->method('upsert');

// ✅ E2E: реальный Meilisearch (Testcontainers)
// Только 1–2 E2E-теста с реальным Meilisearch.
```

### 5. Транзакции в Integration

```php
protected function setUp(): void
{
    self::bootKernel();
    $this->em = self::getContainer()->get('doctrine')->getManager();
    $this->em->getConnection()->beginTransaction();
}

protected function tearDown(): void
{
    $this->em->getConnection()->rollBack();
    // БД в исходном состоянии. Каждый тест — изолирован.
}
```

**Минус:** `COMMIT`-зависимые тесты (например, `TRIGGER` на `AFTER COMMIT`) не работают внутри транзакции.

**Компромисс:** 90% Integration — в транзакции (быстро, изолированно). 10% — с `TRUNCATE` (медленнее, но тестирует commit-логик).

---

## CI pipeline

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  static:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          coverage: none
      - run: composer install --no-interaction
      - name: Architecture check
        run: vendor/bin/arch check
      - name: PHPStan
        run: vendor/bin/phpstan analyse
      - name: CS Fixer (dry-run)
        run: vendor/bin/php-cs-fixer fix --dry-run --diff

  unit:
    runs-on: ubuntu-latest
    needs: static
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - name: Run Unit tests
        run: vendor/bin/phpunit --testsuite Unit --coverage-clover=coverage.xml
        env:
          APP_ENV: test
      - name: Upload coverage
        uses: codecov/codecov-action@v3

  integration:
    runs-on: ubuntu-latest
    needs: static
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: test_db
        ports:
          - 5433:5432
        options: >
          --health-cmd="pg_isready -U test"
          --health-interval=1s
          --health-timeout=2s
          --health-retries=10
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - name: Run Integration tests
        run: vendor/bin/phpunit --testsuite Integration
        env:
          APP_ENV: test
          DATABASE_URL: postgresql://test:test@localhost:5433/test_db

  e2e:
    runs-on: ubuntu-latest
    needs: [unit, integration]
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: test_db
        ports:
          - 5433:5432
        options: >
          --health-cmd="pg_isready -U test"
          --health-interval=1s
          --health-timeout=2s
          --health-retries=10
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - name: Run E2E tests
        run: vendor/bin/phpunit --testsuite E2E
        env:
          APP_ENV: test
          DATABASE_URL: postgresql://test:test@localhost:5433/test_db
          MESSENGER_TRANSPORT_DSN: sync://
```

**Время CI:**
```
static:      30 с
unit:        10 с
integration: 40 с
e2e:         30 с
─────────────────
Итого:      ~2 мин (последовательно) или ~1.5 мин (unit + integration параллельно)
```

---

## Покрытие: где нужно, где не нужно

```
Domain:        90–100%. Каждый инвариант — тест. Каждая ветка — тест.
Application:   80–90%. Каждый UseCase. Каждый путь (успех, ошибка, rollback).
Infrastructure: 60–80%. Критические пути (save, find, flush). Не каждый getter.
Framework:     30–50%. Критические сценарии (auth, validation). Не каждый парамет.
```

**Не гонитесь за 100%.** 85% Domain + 80% Application > 100% «всё».

---

## Антипаттерны

| Антипаттерн | Почему плохо | Правильно |
|---|---|---|
| Unit-тест с `EntityManager` | 50 мс на тест. Зависимость от БД. | Unit: моки портов. Integration: реальная БД. |
| Мок доменной сущности | `createMock(Invoice::class)` — тестируем мок, не код. | Тестируем реальную `Invoice`. Моки — только порты. |
| 200 E2E-тестов | 200 × 2 с = 400 с. Команда перестанет запускать. | 10 E2E. 300 Unit. |
| `sleep(1000)` в тесте | Флейк. Зависит от нагрузки CI. | Polling с deadline. |
| Тест зависит от порядка | `testB` использует данные из `testA`. | `setUp: truncate`. Каждый тест самодостаточен. |
| Integration без `TRUNCATE` | Остатки от предыдущего теста. Флейк. | `TRUNCATE ... CASCADE` в `setUp`. |
| `assertDatabaseHas()` без `assertDatabaseCount()` | «Есть запись» ≠ «ровно одна». | Точное количество. |
| Тест «без имени» | `test1`, `test2`, `it_works`. | `testCannotPayAlreadyPaidInvoice`. |
| Тест на 50 строк | 5 действий, 10 assert. | 1 тест = 1 сценарий = 3–10 строк. |
| Мокаем `Mail` и проверяем `Mail` | `createMock(Mailer::class)` + `method('send')->willReturn(true)`. Тестируем мок. | Unit: мок Mailer. Integration: MailPit (реальный SMTP-сервер в контейнере). |

---

## Итого: чек-лист

```
Перед написанием теста:

□ Что я тестирую? (Domain / Application / Infrastructure / Framework)
□ Какой уровень? (Unit / Integration / E2E)
□ Что мокаю? (только порты / только внешние HTTP)
□ Что НЕ мокаю? (доменные сущности, БД в Integration)
□ Тест детерминирован? (нет time(), random, sleep)
□ Тест изолирован? (setUp: truncate / clean state)
□ Тест быстрый? (Unit < 1 мс, Integration < 500 мс, E2E < 3 с)
□ Имя описывает сценарий? (testCannotPayAlreadyPaidInvoice, не test1)
□ 1 тест = 1 сценарий? (не 5 действий в одном)
□ Если тест падает — я знаю ПОЧЕМУ? (не «random failure»)
```

> ⏱ 87.4s · prompt 111381 tok · output 10554 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

The user is asking about concurrent modification of the same database record by two users. This is a classic concurrency problem. Let me think about what problems arise and what protection mechanisms exist.

Problems:
1. Lost update (classic race condition)
2. Dirty read / non-repeatable read
3. Deadlock
4. Inconsistent state (partial writes)
5. TOCTOU (time-of-check, time-of-use)

Protection mechanisms:
1. Optimistic locking (version column)
2. Pessimistic locking (SELECT FOR UPDATE)
3. Idempotency
4. Application-level serialization (queues)
5. Database-level isolation levels
6. Check constraints / triggers
7. Application-level retry with backoff

Let me structure this well, with concrete PHP/Doctrine examples, trade-offs, and clear decision criteria. I'll write in Russian as the conversation is in Russian.

Let me think about the structure:
1. What problems arise (concrete scenarios with timing diagrams)
2. Protection mechanisms (each with code, trade-offs)
3. Comparison table
4. When to choose what (decision tree)
5. Anti-patterns

I should be concrete with PHP/Doctrine code since that's the context of this conversation. Let me make sure the examples are realistic and the trade-offs are honest.

</details>

**Кандидат:**

# Конкурентная запись: проблемы и защита

## Что произойдёт без защиты

### Сценарий: два редактора правят один документ

```
Документ: { title: "Отчёт Q1", body: "Текст...", version: 1 }
Пользователь A: правит title → "Отчёт Q1 (итоговый)"
Пользователь B: правит body  → "Текст (обновлённый)"
Оба начинают в t=0, сохраняют в t=1.
```

### Проблема 1: Lost update

```
t=0  A: SELECT * FROM document WHERE id = 42  → {title: "Отчёт Q1", body: "Текст", version: 1}
t=0  B: SELECT * FROM document WHERE id = 42  → {title: "Отчёт Q1", body: "Текст", version: 1}
t=1  A: в памяти: title = "Отчёт Q1 (итоговый)"
t=1  B: в памяти: body = "Текст (обновлённый)"
t=2  A: UPDATE document SET title = 'Отчёт Q1 (итоговый)', body = 'Текст' WHERE id = 42
t=3  B: UPDATE document SET title = 'Отчёт Q1', body = 'Текст (обновлённый)' WHERE id = 42

Результат: title = 'Отчёт Q1', body = 'Текст (обновлённый)'
→ Изменение A УТЕРЯНО. Пользователь A видит «я сохранил», но данные не те.
```

**Это не «ошибка». Это нормальное поведение SQL без явной семантики.** Оба UPDATE «успешны». БД не знает, что A и B правили *разные* поля *одного* документа.

### Проблема 2: TOCTOU (Time-of-check, time-of-use)

```php
// Пользователь A: «Оставить комментарий»
$comments = $repo->countByDocument(42);       // ← CHECK: 5 комментариев
if ($comments < 10) {                          // ← 5 < 10, OK
    $repo->save(new Comment(42, 'Отлично!'));  // ← USE
}
```

```
t=0  A: count = 5
t=0  B: count = 5
t=1  A: INSERT (6-й комментарий)
t=1  B: INSERT (7-й комментарий)
→ Оба «прошли» проверку. 7 комментариев вместо лимита 10 — не критично.
Но при лимите 5: оба прошли. 6-й и 7-й — нарушение бизнес-правила.
```

### Проблема 3: Deadlock

```
A: UPDATE document SET title = ... WHERE id = 1
A: UPDATE audit_log SET ... WHERE doc_id = 1

B: UPDATE document SET body = ... WHERE id = 1
B: UPDATE audit_log SET ... WHERE doc_id = 1

A ждёт: B держит audit_log.
B ждёт: A держит document.
→ DEADLOCK. PostgreSQL убивает одного.
```

### Проблема 4: Несогласованное состояние

```
t=0  A: UPDATE invoice SET total_cents = 5000 WHERE id = 1
t=1  B: UPDATE invoice SET status = 'void' WHERE id = 1
t=2  A: UPDATE invoice_line_item SET amount = ... WHERE invoice_id = 1
t=3  B: UPDATE invoice_line_item SET amount = 0 WHERE invoice_id = 1  (аннулирование)

Результат: invoice.status = 'void', но line_item.amount = 5000.
→ Сумма позиций ≠ total. Инвариант нарушен.
```

---

## Механизмы защиты

### 1. Optimistic Locking — `version` column

**Идея:** каждый UPDATE несёт условие «данные не изменились с момента моего SELECT».

```sql
ALTER TABLE document ADD COLUMN version integer NOT NULL DEFAULT 0;
```

```php
// Entity:
#[ORM\Entity]
class DocumentEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $body;

    #[ORM\Version]  // ← Doctrine генерирует WHERE version = ? и SET version = version + 1
    private int $version;
}
```

```sql
-- A:
SELECT * FROM document WHERE id = 42;  -- version = 1
UPDATE document SET title = '...', version = 2 WHERE id = 42 AND version = 1;
-- → 1 row affected. ✅

-- B:
SELECT * FROM document WHERE id = 42;  -- version = 1 (A ещё не commit'ил)
UPDATE document SET body = '...', version = 2 WHERE id = 42 AND version = 1;
-- → 0 rows affected. ❌ Конфликт.
```

```php
// Doctrine бросает:
// Doctrine\OptimisticLockException

// Сервис:
try {
    $doc->setTitle('Новый заголовок');
    $em->flush();
    $em->commit();
} catch (OptimisticLockException $e) {
    $em->rollBack();
    throw new ConcurrencyConflictException(
        'Документ был изменён другим пользователем. Обновите страницу.',
        previous: $e,
    );
}
```

**Клиент получает `409 Conflict`.** UI показывает: «Другой пользователь изменил документ. Перезагрузить?»

#### Когда подходит

```
✅ Низкая конкуренция: 1–5 одновременных правок одной записи.
✅ Редактирование документов, настроек, профилей.
✅ CRUD-приложения, админки.
✅ High throughput: нет блокировок, нет ожидания.
```

```
❌ Высокая конкуренция: 50+ одновременных операций на одну запись.
   → 80% ретраев. Каждый retry — полная перезагрузка + пересчёт.
❌ Дорогая операция: вызов внешнего API → повтор = повторный вызов.
❌ Пользователь не может «просто перезагрузить»: сложная форма, 20 полей.
```

#### Что происходит при конфликте

```
Пользователь A: сохранил. Видит «Успешно».
Пользователь B: получает 409. Видит «Конфликт. Перезагрузить?»

Вопрос: а что делать с изменением B?
  Вариант 1: потерять. B перезагружает, видит изменения A, правит снова.
  Variant 2: merge. B видит «A изменил title. Ваше изменение body сохранено?»
  Variant 3: B перезагружает, правит body поверх title A. Сохраняет.

В 90% случаев — Вариант 1. Достаточно.
```

---

### 2. Pessimistic Locking — `SELECT FOR UPDATE`

**Идея:** заблокировать строку. Второй пользователь **ждёт** или получает ошибку.

```php
// Репозиторий:
public function findByIdLocked(int $id): DocumentEntity
{
    return $this->em->createQueryBuilder()
        ->select('d')
        ->from(DocumentEntity::class, 'd')
        ->where('d.id = :id')
        ->setParameter('id', $id)
        ->setLockMode(LockMode::PESSIMISTIC_WRITE)  // ← SELECT FOR UPDATE
        ->getQuery()
        ->getOneOrNullResult();
}
```

```sql
-- A:
BEGIN;
SELECT * FROM document WHERE id = 42 FOR UPDATE;  -- блокирует строку
UPDATE document SET title = '...' WHERE id = 42;
COMMIT;  -- блокировка снята

-- B (параллельно):
BEGIN;
SELECT * FROM document WHERE id = 42 FOR UPDATE;  -- БЛОКИРОВАН. Ждёт.
-- ... A commit'ит ...
-- B получает строку. Видит обновлённый title.
UPDATE document SET body = '...' WHERE id = 42;
COMMIT;
```

**B не терпит lost update.** Он видит актуальные данные A и правит поверх.

#### `lock_timeout` — чтобы не ждать вечно

```php
$conn = $em->getConnection();
$conn->executeStatement('SET LOCAL lock_timeout = 5000');  // 5 секунд

$doc = $repo->findByIdLocked(42);
// Если A держит блокировку > 5 с → ERROR: canceling statement due to lock timeout
```

```php
try {
    $doc = $repo->findByIdLocked(42);
    // ...
    $em->commit();
} catch (LockTimeoutException $e) {
    $em->rollBack();
    throw new ConcurrencyConflictException('Сервер занят. Попробуйте через минуту.');
}
```

#### Deadlock при 2+ строках

```
A: lock document#1 → lock audit_log#1
B: lock document#2 → lock document#1  (ждёт A)
A: → lock audit_log#2  (ждёт B)
→ DEADLOCK
```

**Предотвращение: порядок блокировок.**

```php
// Всегда: min(id) → max(id)
$first  = min($docId, $relatedId);
$second = max($docId, $relatedId);

$entity1 = $repo->findByIdLocked($first);
$entity2 = $repo->findByIdLocked($second);
```

Оба процесса блокируют `min` первым. Deadlock невозможен.

#### Когда подходит

```
✅ Высокая конкуренция: 10+ одновременных операций на одну запись.
✅ Банковские операции: «баланс не может стать отрицательным».
✅ Бронирования: «место не может быть продано дважды».
✅ Дорогая операция: платёжный шлюз, внешний API.
✅ Пользователь не может «просто перезагрузить».
```

```
❌ Низкая конкуренция: 1–2 операции/мин. Блокировка — overhead.
❌ Connection pool: N заблокированных = N занятых соединений.
❌ Latency: B ждёт A. 5 сек → 5 сек ожидания.
❌ Масштабируемость: 100 concurrent → 100 в очереди.
```

---

### 3. Idempotency — «повтор = тот же результат»

**Идея:** даже если операция выполнится дважды — результат один.

```sql
ALTER TABLE transfer
    ADD CONSTRAINT transfer_idem_key_unique
    UNIQUE (idempotency_key);
```

```php
// Клиент (браузер, мобильное приложение):
// POST /transfers
// Headers:
//   Idempotency-Key: "abc-123-def-456"
// Body:
//   { "from": 1, "to": 2, "amount": 500 }

// Сервер:
function execute(string $key, array $payload): Response
{
    // 1. Уже было?
    $existing = $repo->findByIdempotencyKey($key);
    if ($existing !== null) {
        return $this->json($existing->result());  // 200 + тот же результат
    }

    // 2. Нет. Выполняем.
    $result = $this->transferService->execute($payload);

    // 3. Записываем ключ + результат.
    $this->repo->saveKey($key, $result);
    $em->flush();
    $em->commit();

    return $this->json($result);
}
```

**Сценарий:**

```
t=0  Клиент: POST /transfers {key: "abc-123"}
t=1  Сервер: начал выполнять.
t=2  Сеть упала. Клиент не получил ответ.
t=3  Клиент: retry. POST /transfers {key: "abc-123"}
t=4  Сервер: findByIdempotencyKey("abc-123") → уже есть.
t=5  Сервер: 200 + результат.
→ Двойное списание НЕ произошло.
```

#### Когда подходит

```
✅ Любой HTTP endpoint, который мутирует состояние.
✅ Платежи, переводы, заказы.
✅ Retry-логика на клиенте (браузер, мобильное).
✅ Асинхронные задачи (worker может обработать дважды).
```

**Idempotency не заменяет locking.** Это дополнение. Locking защищает «одновременное изменение». Idempotency защищает «повторное выполнение».

---

### 4. Application-level serialization — очередь

**Идея:** операции над одной записью проходят через **одну** очередь. Параллельно не могут.

```php
// Вместо:
//   POST /documents/42/edit → сразу UPDATE

// Делаем:
//   POST /documents/42/edit → enqueue(document_id: 42, changes: {...})
//   Worker (1 на документ): dequeue → apply → ack

// Очередь: document_42
// Worker: 1 процесс на документ. Последовательная обработка.
```

```php
// Worker:
#[AsMessageHandler]
final class DocumentEditWorker
{
    public function __invoke(DocumentEditMessage $msg): void
    {
        $doc = $this->repo->findById($msg->documentId);
        foreach ($msg->changes as $field => $value) {
            $doc->set($field, $value);
        }
        $this->repo->save($doc);
        $this->em->flush();
        $this->em->commit();
    }
}
```

**Почему это решает проблему:** только один worker обрабатывает документ#42 в любой момент. Нет параллельных UPDATE.

#### Когда подходит

```
✅ Очень высокая конкуренция: 100+ операций/сек на одну запись.
✅ Операции дешёвые: «поменить статус», «добавить комментарий».
✅ Задержка 100 мс – 2 сек допустима.
❌ Операции дорогие: платёж, внешний API.
❌ Нужен мгновенный ответ: «я сохранил, покажи результат».
❌ 10 000 документов × 1 очередь на каждый = 10 000 очередей.
```

---

### 5. Database isolation levels

| Уровень | Что защищает | Что не защищает |
|---|---|---|
| `READ COMMITTED` (default PG) | Dirty read | Non-repeatable read, Phantom |
| `REPEATABLE READ` | Dirty read, Non-repeatable read | Phantom (без SSI) |
| `SERIALIZABLE` (SSI) | Всё | Ничего. Но: deadlock, retry |

```sql
-- Для конкретного запроса:
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;

-- Или на уровне сессии:
-- postgresql.conf: default_transaction_isolation = 'serializable'
```

**SERIALIZABLE + SSI (PostgreSQL):**

```
A: BEGIN ISOLATION LEVEL SERIALIZABLE;
A: SELECT * FROM document WHERE id = 42;
A: UPDATE document SET title = '...' WHERE id = 42;
A: COMMIT;

B: BEGIN ISOLATION LEVEL SERIALIZABLE;
B: SELECT * FROM document WHERE id = 42;
B: UPDATE document SET body = '...' WHERE id = 42;
B: COMMIT;  -- ← SSI детектирует конфликт. ABORT.
```

PostgreSQL бросает:
```
ERROR:  could not serialize access due to read/write dependencies among transactions
HINT:  The transaction might succeed if retrying.
```

**Это не «ошибка». Это «повторите».** Приложение ловит `SerializationFailure` и ретраит.

#### Когда подходит

```
✅ Финансовые операции: «баланс должен быть консистентным при любом порядке».
✅ Аудит: «каждый запрос видит снимок БД».
❌ Высокий throughput: SSI → много abort → много retry.
❌ Длинные транзакции: SSI держит блокировки дольше.
```

---

### 6. Check constraints + application validation

**Не защита от конкуренции. Защита от невалидного состояния.**

```sql
ALTER TABLE account
    ADD CONSTRAINT account_balance_nonneg
    CHECK (balance_cents >= 0);
```

Даже если lost update произошёл и `balance = -500` — **INSERT/UPDATE отклоняется**. Не потеряно, а **отклонено**. Приложение ловит `IntegrityConstraintViolationException`.

```php
try {
    $account->setBalance(-500);
    $em->flush();
} catch (IntegrityConstraintViolationException $e) {
    $em->rollBack();
    throw new InsufficientFundsException('Баланс не может быть отрицательным');
}
```

Это **последняя линия обороны**. Не замена locking'у, а страховка от бага в коде.

---

## Сравнение

| Механизм | Latency | Throughput | Сложность | Когда |
|---|---|---|---|---|
| **Optimistic (version)** | Низкая (нет ожидания) | Высокая | Низкая (1 колонка) | Low–medium contention. CRUD. |
| **Pessimistic (FOR UPDATE)** | Высокая (ожидание) | Средняя (pool) | Средняя (lock_timeout, порядок) | High contention. Банки. Бронирования. |
| **Idempotency** | 0 (проверка key) | Высокая | Низкая (1 unique constraint) | Любой mutating endpoint. Retry. |
| **Очередь** | 100 мс – 2 с | Очень высокая | Высокая (инфраструктура) | Very high contention. Дешёвые операции. |
| **SERIALIZABLE** | Низкая–средняя | Средняя (abort+retry) | Средняя (retry-логика) | Финансы. Аудит. |
| **CHECK constraint** | 0 | Высокая | Низкая (1 DDL) | Всегда. Страховка. |

---

## Решение: слои защиты

Ни один механизм не заменяет остальные. Они **дополняют**.

```
┌─────────────────────────────────────────────────────────────────────┐
│  Слой 0: CHECK constraint в БД                                      │
│  «Запись не может существовать в невалидном состоянии.»             │
│  Всегда. Невидимо. Последняя линия.                                  │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 1: Idempotency key                                            │
│  «Повторный вызов = тот же результат.»                              │
│  Любой mutating endpoint. Клиент + сервер.                          │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 2: Optimistic или Pessimistic locking                         │
│  «Одновременное изменение = детектировано.»                         │
│  Выбор по contention (см. ниже).                                     │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 3: Retry-логика (Presentation)                                │
│  «Конфликт = попробуй снова. 3 раза. Не вышло = 409.»              │
│  Только для optimistic. Pessimistic — нет retry (уже заблокирован). │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 4: Очередь (если нужно)                                       │
│  «Операции над одной записью — последовательны.»                    │
│  Только для very high contention.                                   │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Когда что выбираю

### Admin-панель, CRUD, 5 разработчиков, 50 req/s

```
Optimistic locking + Idempotency + CHECK constraint.

Почему:
  • Contention: 2–3 редактора одновременно. Мала.
  • Latency: 50 мс. OK.
  • Retry: 1–2. Редко.
  • Сложность: 1 колонка version. 1 unique key. 1 CHECK.
  • Connection pool: не исчерпывается.
```

```php
// 3 строки в entity:
#[ORM\Version]
private int $version;

// 1 строка в БД:
ALTER TABLE document ADD COLUMN version integer NOT NULL DEFAULT 0;

// 1 строка в БД:
ALTER TABLE document ADD CONSTRAINT doc_title_not_empty CHECK (char_length(title) > 0);
```

### Банковский перевод, 500 req/s, «горячий» счёт

```
Pessimistic locking + Idempotency + CHECK constraint + lock_timeout.

Почему:
  • Contention: 50+ одновременных операций на один счёт.
  • Lost update: недопустим. (Деньги.)
  • Retry: дорогой (внешний API). Pessimistic = один раз.
  • Latency: 500 мс ожидания. Допустимо (перевод).
  • Connection pool: 50 заблокированных. OK (pool = 200).
```

```php
// 5 строк в сервисе:
$conn->executeStatement('SET LOCAL lock_timeout = 5000');
$sender   = $this->accounts->findByIdLocked($senderId);   // FOR UPDATE
$receiver = $this->accounts->findByIdLocked($receiverId);  // FOR UPDATE
$sender->withdraw($amount);
$receiver->deposit($amount);
$em->commit();
```

### Бронирование гостиницы, 200 req/s, «место не продаётся дважды»

```
Pessimistic locking + Idempotency + CHECK constraint.

Или: очередь, если 1000+ req/s.

Почему:
  • Contention: 20+ одновременных бронирований одного номера.
  • Lost update: недопустим. (Двойное бронирование.)
  • Pessimistic: B ждёт A. 500 мс. OK.
  • Если > 500 concurrent: очередь.
```

### Отчёт, дашборд, 10 req/s, «данные должны быть консистентны»

```
Optimistic locking + SERIALIZABLE (для отчёта).

Почему:
  • Contention: мала (10 req/s).
  • Но: «сумма за месяц» должна быть консистентной.
  • SERIALIZABLE: snapshot. Каждый запрос видит «снимок».
  • Abort + retry: 1–2 раза. Редко.
```

### 10 000 документов, 5000 req/s, «поменить статус»

```
Очередь (1 на документ) + Idempotency + CHECK constraint.

Почему:
  • Contention: 50+ операций/сек на один документ.
  • Pessimistic: 50 в очереди × 500 мс = 25 с. Нет.
  • Optimistic: 80% retry. Нет.
  • Очередь: последовательно. 10 мс на операцию. 5000/с. OK.
  • Latency: 200 мс. Допустимо («статус обновится»).
```

---

## Антипаттерны

### 1. `sleep(1000)` «чтобы не конфликтовать»

```php
// ❌
time_sleep(1000);  // «ну, другие успели»
$em->flush();
```

Не решает ничего. Конфликт был бы и на 1 мс. Плюс: 1 с latency для каждого.

### 2. Публичное свойство + внешний `if`

```php
// ❌
$account->balanceCents;  // публичное
$service:
if ($account->balanceCents >= $amount) {
    $account->balanceCents -= $amount;
}
// Между if и -= кто-то мог изменить.
// withdraw() — единая точка. Валидация + изменение — атомарно (в памяти).
```

### 3. «Проверим после flush»

```php
// ❌
$account->balanceCents -= $amount;
$em->flush();
$em->refresh($account);
if ($account->balanceCents < 0) {
    $em->rollback();
    throw new \Exception('Negative!');
}
// Между flush и refresh кто-то мог изменить.
// #[ORM\Version] — детерминированный. «Проверим после» — нет.
```

### 4. Optimistic locking + retry в сервисе

```php
// ❌ В UseCase:
for ($i = 0; $i < 5; $i++) {
    try {
        $this->execute();
        return;
    } catch (OptimisticLockException $e) {
        $em->rollBack();
        continue;
    }
}
throw $e;
```

UseCase не знает «кто вызвал». CLI? Web? Worker? Retry-политика разная. UseCase бросает. Presentation решает.

### 5. Pessimistic locking на чтение

```php
// ❌
$doc = $repo->findByIdLocked(42);  // FOR UPDATE
echo $doc->getTitle();            // только чтение!
// Другой пользователь не может изменить, пока я «смотрю».
// Для чтения: findById() без FOR UPDATE.
```

### 6. Locking «на всякий случай»

```php
// ❌
$account = $repo->findByIdLocked($id);  // блокировка
$report  = $this->generateReport($account);  // 2 сек.
echo $report;
// Блокировка 2 сек. 50 concurrent = 100 с очереди.
// Если не mutate — не блокировать.
```

---

## Итого: решение за 3 вопроса

```
1. Сколько одновременных операций на одну запись?
   1–5    → Optimistic
   5–50   → Pessimistic
   50+    → Очередь

2. Можно ли «просто попробовать ещё раз»?
   Да (дешёвая операция, нет внешних вызовов) → Optimistic + retry
   Нет (дорогая, внешний API)                  → Pessimistic

3. Повторный вызов безопасен?
   Да (idempotency key) → любой механизм
   Нет (двойное списание) → Idempotency + любой механизм

Всегда:
   CHECK constraint в БД.
   Idempotency key на mutating endpoint.
   lock_timeout (pessimistic).
   Порядок блокировок (2+ строки).
```

> ⏱ 64.1s · prompt 121707 tok · output 6551 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

The user is asking me to describe my git process from task definition to production: branching, commit format, pull requests, code review, releases. And explain why each choice was made.

Let me think about this carefully. I've been consistently honest in this conversation that I'm an AI language model without personal experience. But I can describe a well-reasoned git workflow that I would recommend and defend based on the principles I've been discussing throughout this conversation (clean architecture, DDD, testing, concurrency, etc.).

Let me structure this:

1. Branching strategy - why not git-flow (too heavy), why trunk-based with feature branches
2. Commit format - why conventional commits, why not "fix stuff"
3. Pull Request - structure, what goes in, size limits
4. Code review - what to look for, what not to, time limits
5. Releases - how, when, what automation
6. The "why" for each choice

I should be specific, practical, and tie it back to the architecture we've been discussing. Let me write in Russian as the user has been communicating in Russian throughout.

Let me think about the branching strategy more carefully:

- Git flow (develop, release, feature, hotfix, master) - too many branches, too much merge overhead, designed for projects with multiple release tracks. For most PHP/Symfony apps, it's overkill.
- GitHub flow (main + feature branches) - simpler, but no "staging" concept built in
- Trunk-based (main + short-lived feature branches) - best for fast-moving teams, but requires good CI, feature flags
- Trunk-based with a "release" branch for hotfixes - pragmatic middle ground

I'll go with: main is always deployable. Feature branches are short-lived (1-3 days). For hotfixes, branch from main. No "develop" branch. Releases are tags on main.

Commit format: Conventional Commits (feat:, fix:, refactor:, test:, docs:, chore:). Why: automatable changelogs, meaningful history, easy to revert.

PR size: 400 lines max (including tests). Why: review quality degrades above this.

Review: what to look for (architecture, correctness, tests), what not to (formatting - CS Fixer, variable names - team agreement).

Releases: semantic versioning, automated changelog, tag-based deploy.

Let me write this out in a structured way with concrete examples and clear reasoning for each choice.

</details>

**Кандидат:**

# Git-процесс: от задачи до продакшена

## Философия

Git-процесс — не ритуал. Это **инструмент для трёх вещей**:

1. **Безопасность.** Любой коммит можно откатить. Любой merge — отменить.
2. **Скорость.** От идеи до продакшена — часы, не дни.
3. **Читаемость истории.** Через 6 месяцев разработчик (не я, а другой) понимает, *почему* так, а не иначе.

Всё, что не служит этим трём целям — overhead.

---

## 1. Ветвление

### Структура

```
main (всегда деплоится)
 │
 ├── feature/invoice-payment        (1–3 дня)
 ├── feature/buyer-registration    (1–3 дня)
 ├── fix/rounding-error            (1–2 дня)
 │
 └── hotfix/critical-amount-overflow  (от main, 2–4 часа)
```

**Одна постоянная ветка: `main`.** Остальные — временные. Живут 1–3 дня. Мёртвы после merge.

### Почему не Git Flow (`develop` + `release` + `feature` + `hotfix`)

```
Git Flow:
  feature → develop → release → main

Проблемы:
  1. `develop` — вторая «главная» ветка. Merge develop → release → main.
     Три merge'а на один фича. Каждый — шанс конфликта.

  2. `release` живёт 2 недели. За 2 недели — 5 фич, 3 фикса,
     2 рефакторинга. Merge release → main = 10 изменений одновременно.
     Если один из них сломал — ищем среди 10.

  3. Развилка: develop и main расходятся на 2 недели.
     Rebase develop на main = 2 недели конфликтов.

  4. Для команды из 3–8 человек — overhead.
     Git Flow создан для 20+ разработчиков и 3+ release tracks.
```

### Почему не trunk-based (merge в main каждый час)

```
Trunk-based:
  feature → main (merge 5–10 раз в день)

Плюсы:
  • Ноль конфликтов (короткие ветки).
  • Fast feedback.

Минусы:
  1. Требует feature flags для каждой неполной фичи.
     Без flags: сломанный main = сломанный прод.
  2. Требует CI < 5 мин. Иначе каждый merge ждёт CI.
  3. Требует, что все 8 разработчиков мержат в main 5–10 раз в день.
     Не все готовы. Не все фичи «достаточно малы».
```

### Почему trunk-based + короткоживущие feature-ветки

```
Компромисс:
  • main — всегда деплоится. (Безопасность.)
  • Feature-ветки — 1–3 дня. (Читаемость.)
  • Merge в main — через PR + CI. (Качество.)
  • Hotfix — от main, мерж в main. (Скорость.)
  • Нет develop, нет release. (Простота.)
```

### Правила веток

```
1. Имя: {type}/{short-description}
   feature/invoice-payment
   fix/rounding-error
   refactor/order-mapper
   hotfix/amount-overflow

2. Живёт ≤ 3 дня.
   4-й день — ветка «закисла». Пересматриваем:
   • Разбить на 2 PR?
   • Начать заново от main?
   • Принять как есть (если всё зелёное)?

3. Ровно один тип изменения.
   feature/invoice-payment — только оплата.
   Не feature/invoice-payment-and-email-and-analytics.

4. Не мержим main в feature. Rebase на main.
   git fetch origin
   git rebase origin/main
   → Линейная история. Без merge-коммитов в feature.

5. Feature-ветка — private. Не публична в репозитории.
   (GitHub: branch protection на main. Feature — не защищена.)
```

---

## 2. Коммиты

### Формат: Conventional Commits

```
<type>(<scope>): <imperative summary>

[optional body]

[optional footer]
```

### Примеры

```
feat(invoice): add payment endpoint

POST /api/invoices/{id}/pay
Accepts: { tx_id: string }
Returns: 200 { status: "paid" } or 409 { error: "already_paid" }

Closes #142

fix(money): round to nearest cent instead of truncating

Money::fromCents(1999.7) previously truncated to 1999.
Now rounds to 2000. Affects invoice totals and line items.

refactor(order): extract PricingPolicy from OrderService

OrderService::process() was 140 lines, 7 dependencies.
PricingPolicy is a pure function: (subtotal, order) → discount.
OrderService now: orchestration + persistence.

test(invoice): add tests for concurrent payment

Covers OptimisticLockException on double pay.
Adds 3 test cases: success, conflict, rollback.

docs: update API reference for /invoices endpoint

chore(deps): upgrade symfony to 7.1

BREAKING CHANGE: Symfony 7 requires PHP 8.2+
```

### Типы

| Тип | Когда | Пример |
|---|---|---|
| `feat` | Новая возможность для пользователя | `feat(invoice): add payment` |
| `fix` | Исправление бага | `fix(money): round to nearest cent` |
| `refactor` | Изменение без изменения поведения | `refactor(order): extract PricingPolicy` |
| `test` | Добавление/изменение тестов | `test(invoice): concurrent payment` |
| `docs` | Документация | `docs: update API reference` |
| `chore` | Инструменты, CI, deps | `chore(deps): upgrade symfony` |
| `perf` | Улучшение производительности | `perf(query): add composite index` |
| `style` | Форматирование (не логика) | `style: fix CS violations` |

### Scope

```
scope = bounded context или модуль.

invoice, buyer, order, money, payment
persistence, messaging, security, infra
deps, ci, docs
```

### Почему Conventional Commits, а не «fix stuff» / «WIP» / «final2»

```
1. Автоматический changelog.
   git-cliff / conventional-changelog → CHANGELOG.md из коммитов.
   feat → "Added ...". fix → "Fixed ...". chore → пропущен.
   Без форматирования коммитов — changelog пишется вручную.
   Ручной changelog = устаревший changelog.

2. Автоматический семантический version.
   feat → MINOR bump. fix → PATCH. BREAKING CHANGE → MAJOR.
   Без формата — версии «на глаз».

3. Читаемость git log.
   git log --oneline --since="2 weeks ago"
   → 30 строк. Каждую можно понять без контекста.

4. Revert.
   git revert <commit> → точный откат.
   Без типа: «какой коммит откатить? 30 коммитов, 5 из них fix».
```

### Правила

```
1. Один коммит = одно логическое изменение.
   Не «feat + fix + test» в одном коммите.
   Не «refactor + feat» в одном.

2. Summary ≤ 72 символа. Imperative mood.
   ✅ "add payment endpoint"
   ❌ "added the payment endpoint"
   ❌ "adding payment endpoint"

3. Body — ПОЧЕМУ. Не ЧТО.
   ЧТО видно в diff. ПОЧЕМУ — нет.
   ✅ "Money::fromCents(1999.7) truncated to 1999. Now rounds to 2000."
   ❌ "fixed rounding"

4. Footer — ссылки на issues, BREAKING CHANGE.
   Closes #142
   BREAKING CHANGE: ...

5. Не коммитим WIP в feature-ветку как «final».
   Если коммит — он закончен. Если нет — squash перед merge.
```

### Squash перед merge

```
Feature-ветка:
  a1: feat(invoice): add MarkInvoicePaidUseCase
  a2: test(invoice): add unit tests for use case
  a3: fix(invoice): handle OptimisticLockException
  a4: refactor(invoice): extract UnitOfWork port
  a5: fix(invoice): correct import path
  a6: chore: fix CS violations

Merge в main: SQUASH → 1 коммит
  feat(invoice): add payment endpoint with optimistic locking

Почему:
  • History main — 1 коммит = 1 фича. Не 6 коммитов.
  • git log main --oneline → 50 строк за год. Не 300.
  • Revert: откатить фичу = git revert <1 commit>. Не 6.
  • a5 «fix import» и a6 «fix CS» — мусор в истории.
```

**Исключение:** hotfix. Не squash. Каждый hotfix-коммит — отдельный.
Причина: точный revert. Hotfix = 2 коммита (fix + test). Revert = 2.

---

## 3. Pull Request

### Структура

```markdown
## What

One paragraph. What does this PR do?
Not "refactoring". "Extracts PricingPolicy from OrderService
so that discount calculation is a pure function, testable
without mocking 7 dependencies."

## Why

One paragraph. Why now? What problem does it solve?
Not "it's cleaner". "OrderService::process() was 140 lines.
Adding a new discount rule required modifying the method,
which also handles payment and email. Separating pricing
makes it possible to add rules without touching payment code."

## How to test

1. Create an invoice with 3 line items.
2. Apply VIP tag.
3. Expected: 10% discount.
4. Apply VIP + bulk (5+ items).
5. Expected: 10% (VIP wins), not 15% (sum).

## Screenshots / Logs

(pasted if relevant)

## Checklist

- [x] Unit tests added
- [x] Integration tests updated
- [x] PHPStan green
-

> ⏱ 37.9s · prompt 128038 tok · output 3033 tok · ⚠️ обрыв: length

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

The user is asking about my approach to code review: how I mark/label comments, what I look at first, and how I handle disagreements with the author. This is a follow-up in a long conversation about PHP, Symfony, DDD, architecture, testing, git processes. The user has been asking detailed, practical questions and I've been giving thorough, structured answers with code examples and trade-offs.

Let me think about what makes a good code review answer:

1. **Marking/labeling comments** — I should have a clear system for severity levels, not just "this is bad" but structured feedback
2. **What to look at first** — priority order matters. Not everything is equal in a review
3. **Handling disagreements** — this is the human/soft skill part, very important

Let me structure this well:

**Marking system:**
- Blocking (must fix before merge)
- Important (should fix, discuss if not)
- Suggestion (nice to have, optional)
- Question (I don't understand, please clarify)
- Praise (yes, I should acknowledge good code)

**Priority order when reviewing:**
1. Does it do what it's supposed to? (correctness)
2. Can it cause data loss/security issue? (risk)
3. Does it break existing behavior? (regression)
4. Is it testable? (maintainability)
5. Readability and naming (clarity)
6. Performance (only if relevant)
7. Style/formatting (lowest priority, usually auto-fixed)

**Disagreements:**
- Distinguish between "I think" and "the code will break"
- Propose alternatives, don't just reject
- Agree to disagree on style, insist on correctness
- Time-box the discussion
- Escalation path

Let me write this in Russian, maintaining the same detailed, structured style as previous answers. I'll use tables, code examples, and concrete scenarios. I should be practical and opinionated where appropriate, acknowledging trade-offs.

I'll structure as:
1. Marking system (severity levels)
2. Priority order (what I look at first)
3. The review itself (concrete example)
4. Disagreement handling
5. Anti-pattern

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 8.0s · prompt 130587 tok · output 485 tok · ⚠️ обрыв: length

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

The user asks me to take a raw business complaint ("Users complain they can't find their past orders, support is flooded with requests") and formulate it as a well-structured task for a developer/executor, such that it can be completed without clarifications. They want to know what I'd include and why.

This is a product/tech lead question about translating vague business problems into actionable engineering tasks. Let me think about what makes a task specification good enough to execute without back-and-forth.

Key elements I need to cover:
1. Problem statement (what's wrong, from user's perspective)
2. Hypothesis (why it's happening) — because "can't find" could mean: no search, bad search, broken search, data not stored, UI hides it, pagination limit, no date filter
3. Scope (what we're doing, what we're NOT doing)
4. Acceptance criteria (how we know it's done)
5. Constraints (performance, UX, compatibility)
6. What we already know (data model, existing endpoints)
7. Definition of done (tests, docs, monitoring)
8. Out of scope (explicitly)
9. Success metrics (how we measure if support tickets drop)

The "why" for each element is crucial — the user explicitly asks "что обязательно включишь и почему".

Let me structure this as a concrete task spec, then explain the reasoning for each section. I'll make it practical, not theoretical. I'll write it as if I'm the tech lead writing a ticket in Linear/Jira for a developer to pick up.

I should be specific: show the actual text of the task, not just describe it. The user wants to see the artifact, not a meta-discussion about artifacts.

Let me think about what "can't find past orders" actually decomposes into:
- Is there even a

</details>

**Кандидат:**

(пустой ответ модели)

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

(пустой ответ модели)

> ⏱ 5.2s · prompt 130675 tok · output 397 tok · ⚠️ обрыв: length

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о двух конкретных рабочих ситуациях:
а) Поупутный баг, не связанный с задачей
б) Критерий приёмки противоречит цели задачи

Нужно ответить практично, с конкретными действиями, а не абстрактно. Показать мышление разработчика, который работает в команде с заказчиком.

Ситуация а) — классика. Баг "попутный". Соблазн: починить заодно. Проблема: scope creep, сломанные тесты, непредвиденные последствия, смещение сроков. Решение: зафиксировать, но не чинить в текущей задаче. Отдельный ticket.

Ситуация б) — сложнее. Критерий приёмки — это договорённость. Если она противоречит цели — значит, кто-то ошибся при формулировке. Нужно: не молча исправить, не молча проигнорировать. Эскалировать. Обосновать. Предложить альтернативу.

Нужно ответить структурированно, с конкретными шагами, с примерами, с обоснованием "почему". Не абстрактно, а как рабочий процесс.

Отвечаю на русском, как

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.8s · prompt 130799 tok · output 272 tok · ⚠️ обрыв: length

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о структуре отчёта о выполнении задачи: что в него входит, и что меняется, если часть задачи не выполнена. Это вопрос о коммуникации результата работы.

Ключевые аспекты:
1. Что именно включать в отчёт — конкретные пункты
2. Почему каждый пункт нужен (обоснование)
3. Что меняется при частичном выполнении — это принципиально другой тип отчёта
4. Честность: не "я сделал 90%", а "вот что сделано, вот что нет, вот почему, вот что нужно"

Я должен ответить как разработчик, который реально пишет отчёты. Не абстрактно, а конкретно: какие разделы, что в них, какой тон, что НЕ писать.

Структура ответа:
1. Полный отчёт (задача выполнена)
2. Частичный отчёт (часть не сделана)
3. Принципы, которые стоят за структурой

Не буду делать это слишком длинно. Пользователь уже получил много больших ответов

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.4s · prompt 130850 tok · output 222 tok · ⚠️ обрыв: length

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about "FORCE INDEX" in PostgreSQL. Let me think carefully about this.

FORCE INDEX is a MySQL syntax:
```sql
SELECT * FROM table FORCE INDEX (index_name) WHERE ...
```

PostgreSQL does NOT have FORCE INDEX syntax. PostgreSQL does not have USE INDEX, FORCE INDEX, IGNORE INDEX, or SET INDEX. These are all MySQL-specific features.

In PostgreSQL, the query planner makes its own decisions about which index to use based on:
- Statistics (pg_stats, updated by ANALYZE)
- Cost estimates
- Available indexes
- Query structure

If a developer wants to influence index selection in PostgreSQL, they have different mechanisms:
1. `SET enable_indexscan = off` — disables index scans entirely (debugging only)
2. `SET enable_seqscan = off` — disables sequential scans (debugging only)
3. `SET enable_bitmap

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.5s · prompt 130884 tok · output 188 tok · ⚠️ обрыв: length

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, на сколько процентов strict_types=1 замедляет выполнение PHP-кода. Это вопрос с подвохом — он предполагает, что strict_types=1 имеет заметное влияние на производительность. Нужно честно ответить: влияние есть, но оно минимальное и измеряемое только в микробенчмарках. Важно не выдумать конкретный процент, которого я не знаю точно, а объяснить механизм и дать честную оценку.

Что я реально знаю:
- strict_types добавляет проверку типов на границе функции (каждый вызов аргумента проверяется).
- Без strict_types PHP может делать type coercion — и эта проверка тоже есть, просто л

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.5s · prompt 130929 tok · output 143 tok · ⚠️ обрыв: length

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает: "Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?"

Это ложная предпосылка. Doctrine ORM ПОДДЕРЖИВАЕТ составные первичные ключи (composite/compound primary keys). Это не миф — есть конкретные механизмы:

1. `#[ORM\Id]` на нескольких полях — несколько полей с атрибутом

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.8s · prompt 130974 tok · output 98 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать кусок кода и переделать его. Это классический "bad code review" вопрос. Нужно:

1. Найти все проблемы — от критических до косметических
2. Приоритизировать: что убивает в проде, что просто плохо
3. Показать переделанный код
4. Обосновать порядок работы ("с чего начал бы и почему")

Проблемы в коде:
- SQL injection: $id вставляется прямо в запрос
- Нет транзакции: UPDATE + email — если email упадёт, статус уже paid
- Email внутри запроса: замедляет ответ
- $pdo->query с параметром в строке — инъекция
- Нет валидации $id (может быть null, строка "1; DROP TABLE")
- Нет проверки статуса: можно "оплатить" уже оплаченный заказ
- Mailer::send синхронный — блокирует ответ
- Logger::log — синхронный, тоже блокирует
- Нет обработки ошибок (что если UPDATE затронул 0 строк? race condition?)
- Нет CSRF-защиты (если это веб)
- Нет авторизации: любой пользователь может оплатить любой заказ
- Response('ok') — нет статуса 200 явно, нет JSON
- No type hints на параметрах
- $req->get('order_id') — из query string? Из тела? Неясно
- Нет idempotency: двойной вызов = двойная "оплата"
- Mailer::send — статический метод, не инжектится, не тестируется
- Logger — статический, не инжектится
- DB::connect() — статический, не инжектится
- Нет Doctrine / нет ORM — raw PDO. Не обязательно плохо, но в контексте вопроса — да
- SELECT * — неэффективно
- Нет проверки, что заказ принадлежит текущему пользователю
- "not found" — не JSON, не структурированный ответ
- Нет try/catch
- Статическая логика "paid" — хардкод строки

Приоритизация:
1. CRITICAL: SQL injection, отсутствие авторизации, отсутствие проверки статуса
2. HIGH: нет транзакции, синхронный email, нет idempotency
3. MEDIUM: нет обработки ошибок, нет типов, статические зависимости
4. LOW: стиль, формат ответа

Порядок переделки:
1. Сначала убрать SQL injection (parameterized queries)
2. Добавить авторизацию и проверку статуса
3. Разделить транзакцию и side effects
4. Инжектить зависимости
5. Типизировать
6. Структурировать ответ

Нужно показать конкретный код "до" и "после", объяснить каждый шаг. Не перегружать — показать суть, а не 20 классов. Но показать архитектурные решения.

Отвечу на русском, структурированно, с кодом.

</details>

**Кандидат:**

## Что не так: по приоритету

### Критическое (упадёт в проде)

| # | Проблема | Последствие |
|---|---|---|
| 1 | **SQL-инъекция** | `$id` вставляется в SQL-строку напрямую. `?order_id=1; DROP TABLE orders` — и таблица удалена |
| 2 | **Нет авторизации** | Любой запрос с `order_id=999` оплатит чужой заказ. Никто не проверяет, что текущий пользователь — владелец |
| 3 | **Нет проверки статуса** | Можно «оплатить» уже оплаченный заказ. Или отменённый. Или несуществующий (частично покрыто `if (!$order)`) |
| 4 | **Нет idempotency** | Двойной клик / retry по сети → два `UPDATE` + два email. Заказ «оплачен дважды» |
| 5 | **Email внутри запроса** | Если SMTP недоступен → `Exception` → ответ не ушёл, но `UPDATE` уже выполнен. Статус `paid`, а пользователь не знает. Или: SMTP отвечает 3 сек → ответ клиенту — 3 сек |

### Высокое (сломается при нагрузке или рефакторинге)

| # | Проблема |
|---|---|
| 6 | Статические вызовы: `DB::connect()`, `Mailer::send()`, `new Logger()`. Немышо мокать, нереально тестируемо |
| 7 | Нет обработки `UPDATE ... WHERE id = $id` → 0 строк (заказ удалили между SELECT и UPDATE). Тихо «успешно» |
| 8 | `SELECT *` — берёт все колонки, включая `card_token`, `password_hash`, если они в таблице |
| 9 | Нет типов на параметрах, нет `strict_types` |
| 10 | Ответ `'ok'` — не JSON, не структури

> ⏱ 85.1s · prompt 129960 tok · output 1112 tok · ⚠️ обрыв: length

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

Пользователь просит разобрать конкретный код доменной сущности с точки зрения DDD и чистой архитектуры. Давай проанализирую код внимательно.

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

Проблемы, которые я вижу:

1. **Зависимость от Infrastructure (EntityManagerInterface) в доменной сущности** — это грубейшее нарушение Dependency Rule. Domain не должен знать о Doctrine, Symfony, любой ORM. EntityManager — это инфраструктурный компонент.

2. **`flush()` внутри доменного метода** — доменная сущность не должна знать о транзакциях, о персистентности. `flush()` — это решение уровня Application/Infrastructure.

3. **`#[ORM\Entity]` на доменной сущности** — это привязывает домен к конкретной ORM. Если завтра уйдем с Doctrine — домен сломан.

4. **Отсутствие валидации** — `changeEmail` принимает любую строку. Пустую, "abc", "a@b.c" без TLD. Нет проверки.

5. **Отсутствие валидации при создании** — конструктор принимает email без проверки.

6. **Отсутствие имутабельности / инкапсуляции** — хотя свойство `private`, нет readonly. Но главная проблема — не это.

7. **Отсутствие domain events** — смена email — значимое событие. Никто не уведомляется.

8. **Отсутствие бизнес-правил** — можно ли менять email? Только один раз? Нужно ли подтверждение? Эти правила не выражены.

9. **Конструктор с EntityManager** — это "антимон" для DDD. Сущность создаётся зависимостями, а не значением.

10. **Отсутствие `readonly`** — свойство email может быть изменено извне через reflection, хотя private. Но главная проблема — отсутствие валидации.

11. **Нет `

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 6.5s · prompt 130559 tok · output 512 tok · ⚠️ обрыв: length

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о процессе решения технической проблемы на незнакомой технологии/стеке. Это вопрос о методологии, процессах, принятии решений. Нужно ответить как опытный PHP-разработчик/архитектор, который сталкивался с незнакомыми стеками (Node, Go, Python, новые версии Symfony, новые БД, микросервисы и т.д.).

Структура ответа:
1. Первые 15 минут — диагностика (что вообще за задача, какой scope)
2. Первые часы — изучение (документация, примеры, окружение)
3. Прототип / spike — минимальный рабочий фрагмент
4. Оценка — сколько осталось, где риски
5. Критерии «сам vs. попросить помощь»

Важно: не абстрактный список советов, а конкретная последовательность с примерами. Показать, где граница между «разбираюсь сам» и «уже неэффективно». Дать чёткие критерии.

Сделаю ответ структурированным, с конкретными временными рамками и критериями. Не буду перегружать — лучше 5-6 чётких шагов с объяснением «почему так», чем 20 пунктов.

Формат: markdown, без excesса таблиц (уже было много таблиц в предыдущих ответах). Скорее текстовое повествование с выделенными этапами. Конкретные примеры из практики (или реалистичные).

</details>

**Кандидат:**

## Первые 15 минут: не начинаю писать код

Самая частая ошибка — открыть IDE и начать гуглить синтаксис. Вместо этого я отвечаю на четыре вопроса:

**1. Что именно я не знаю?**
Не «не знаком со стеком», а конкретно. «Не знаю, как в этой версии Laravel работает Eloquent scope» — это решается за 20 минут. «Не понимаю, как устроен их event-driven мик

> ⏱ 6.2s · prompt 130641 tok · output 431 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

The user asks a practical professional question: what to do when you realize a task you promised to complete "by tomorrow" will actually take two days. This is a classic communication/expectation management question in software development.

Let me think about what a senior engineer would actually do here, not just generic advice. The key dimensions are:

1. Timing: when exactly do you tell people?
2. To whom do you tell?
3. What exactly do you say?
4. What do you NOT do?
5. What do you do with the work in the meantime?
6. How do you prevent this from happening again?

Let me think about what's actually hard about this situation:
- The psychological cost of "breaking a promise"
- The temptation to silently work overtime
- The question of whether to deliver a partial result vs. a complete one
- The difference between "I underestimated" vs "scope changed" vs "I hit an unknown unknown"
- The manager's perspective: they have dependencies, deadlines, other people waiting

Let me structure this as a concrete sequence of actions, not generic advice. I should be specific about:
- What I do in the moment I realize
- What I say (with actual phrasing examples)
- What I do NOT do (common anti-patterns)
- How I handle the "partial delivery"

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.2s · prompt 130785 tok · output 286 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансе между качеством и скоростью — когда код "достаточно хорош". Это вопрос инженерного суждения, а не формального правила. Нужно дать практический, честный ответ без корпоративных лозунгов.

Ключевые аспекты:
1. Нет универсального порога "95% готовности"
2. Контекст решает: критичность кода, срочность, команда
3. Конкретные критерии "достаточно хорошо" для разных типов кода
4. Ловушки: перфекционизм vs "спринт, потом поправим"
5. Практические эвристики, а не абстракции
6. Честность о том, что я иногда ошибаюсь в оценке

Структура:
- Главный принцип (не "баланс", а "риск")
- Критерии "достаточно" по категориям кода
- Что я реально делаю (чек-лист перед отправ

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.4s · prompt 130849 tok · output 220 tok · ⚠️ обрыв: length

---
