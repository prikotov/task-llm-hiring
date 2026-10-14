# Собес: `qwen3.8:latest` · роль: backend — run-23

- Дата: 2026-10-12T13:19:04.033257+00:00
- Сервер: http://nserv:11434 · keep_alive: 10m · think: low · num_ctx: 262144
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to roleplay as a PHP backend developer candidate in a job interview. They want me to:
1. Introduce myself and talk about my experience in PHP development and related technologies
2. Rate my knowledge on a scale of 0-10 for: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, Clean Architecture, Testing, Git
3. Explain each rating in one sentence

I need to be careful here. I should be honest that I'm an AI, not a human candidate. But the user is clearly setting up a roleplay/interview scenario. Let me think about how to handle this.

Actually, I think the best approach is to be transparent that I'm an AI assistant, but then engage with the exercise in a useful way. I can present a well-rounded "profile" of a mid-to-senior PHP developer that would be realistic, while being clear this is a simulation. Or I could just play along as the user is clearly asking me to roleplay.

Given the context, I think the user wants me to roleplay as a candidate. I'll do that, but I'll make it a realistic mid-senior profile (not claiming 10/10 in everything, which would be unrealistic). I'll present it naturally as a candidate would in an interview.

Let me craft a realistic profile:
- ~5 years of PHP development
- Symfony as main framework (6.x/7.x)
- Doctrine ORM
- PostgreSQL
- Some DDD experience (not expert level)
- Clean architecture (practical, not dogmatic)
- Testing with PHPUnit, Pest
- Git (daily use)

Ratings (realistic for a solid mid-senior):
- PHP 8: 8/10 — deep knowledge of features like readonly properties, enums, fibers, typed properties, but haven't used all experimental features in production
- Symfony: 9/10 — daily framework, know the internals of DI, event system, security, but haven't contributed to core
- Doctrine: 8/10 — comfortable with DQL, entity mappings, query builder, but some edge cases with complex inheritance and performance tuning
- PostgreSQL: 7/10 — know indexing, query optimization, JSONB, CTEs, but not at DBA level
- DDD: 6/10 — applied aggregates, value objects, repositories in projects, but haven't done full strategic design with bounded contexts at scale
- Clean Architecture: 7/10 — understand the principles, have restructured legacy code, but in practice it's a balance between purity and pragmatism
- Testing: 8/10 — PHPUnit/Pest, integration tests with kernel, mocking, but not always TDD in practice
- Git: 8/10 — daily use, rebasing, bisecting, cherry-picking, but not writing complex hooks or contributing to Git itself

I'll present this as a natural interview answer.

</details>

**Кандидат:**

# Знакомство

Привет! Меня зовут — ну, давайте так скажем, я ваш кандидат. Работаю в PHP-разработке около 5 лет.

**Основной стек:**

- **Symfony** — основной фреймворк, с которым работаю ежедневно. Прошёл путь от Symfony 5 до 7. Комфортно пишу сервисы, слушаю события, настраиваю DI-контейнер, работаю с security, messenger, scheduler.
- **Doctrine** — ORM, с которым живу. Пишу DQL, работаю с QueryBuilder, настраиваю entity mappings, решаю задачи с N+1, lazy loading. Были проекты с complex inheritance strategies.
- **PostgreSQL** — основной бэкенд. Пишу SQL напрямую (CTE, window functions, JSONB), настраиваю индексы, анализирую `EXPLAIN ANALYZE`.
- **PHP 8** — использую readonly properties, enums, fibers (в одном проекте для параллельных HTTP-вызовов), first-class callable syntax, asymmetric visibility (в 8.2).
- **DDD** — в одном проекте внедрял тактическое DDD: aggregate roots, value objects, domain events, repository pattern. Стратегический уровень (bounded contexts, event storming) — пробовал, но без глубокой практики на большой команде.
- **Тестирование** — PHPUnit + Pest, интеграционные тесты через Kernel, mocking через Prophecy. Покрытия в основном 70–80% в business-логике. TDD — не всегда, но в новых модулях стараюсь.
- **Git** — ежедневно: rebase, cherry-pick, bisect, worktree. Пишу commit-сообщения по конвенции, работаю с branch protection.

---

## Самооценка

| Раздел | Оценка | Обоснование |
|--------|--------|-------------|
| **PHP 8** | **8/10** | Уверенно использую все основные фичи в продакшене, но не углублялся в internals и VM-оптимизации. |
| **Symfony** | **9/10** | Основной рабочий фреймворк, знаю internals DI, event dispatcher, compiler passes — не хватает опыта контрибьюции в core. |
| **Doctrine** | **8/10** | Комфортно решаю 95% задач, но сложные сценарии с custom hydration и оптимизацией UnitOfWork — ещё учусь. |
| **PostgreSQL** | **7/10** | Пишу оптимизированные запросы и настраиваю индексы, но не на уровне DBA (partitioning, WAL tuning, replica strategies). |
| **DDD** | **6/10** | Тактическое DDD применял в проде, но стратегическое проектирование (bounded contexts, event storming на 10+ доменов) — только на уровне теории. |
| **Чистая архитектура** | **7/10** | Понимаю принципы и рефакторил legacy под них, но в реальности всегда иду на компромисс между чистотой и скоростью доставки. |
| **Тестирование** | **8/10** | Пишу unit, integration, feature тесты уверенно; не хватает системного опыта с contract testing и нагрузочным тестированием API. |
| **Git** | **8/10** | Повседневные и продвинутые операции (rebase, bisect, worktree, bisect) — свободно; не хватает опыта работы с server-side hooks и contribution flow в open source. |

---

Готов отвечать на вопросы по любому из этих пунктов — от теории до «расскажи, как бы ты решил задачу X». С чего начнём?

> ⏱ 14.4s · prompt 135 tok · output 1489 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The interviewer is asking about the most important PHP 8.0+ features for writing clean and reliable code, with short code examples. I should pick features that genuinely matter for code quality and reliability, not just list everything.

Let me think about what's truly important:

1. **Union types + nullsafe operator (8.0)** — reduces verbose null checks, makes intent clearer.
2. **Match expression (8.0)** — replaces switch, no fallthrough, returns values, type inference works.
3. **Named arguments (8.0)** — readability, especially for functions with many parameters.
4. **Enum (8.1)** — this is huge. Replaces const arrays, magic strings, integer constants. Type safety at compile time.
5. **Readonly properties (8.2)** — immutability, safe value objects.
6. **First-class callable syntax (8.1)** — cleaner code for callbacks.
7. **Fibers (8.1)** — for parallel I/O, but this is more about performance than "clean code" per se.
8. **Destructuring improvements (8.1)** — keyset destructuring, more flexible list().
9. **Asymmetric visibility (8.2)** — nice but niche.
10. **Property hooks (8.4)** — very new, but powerful for validation in properties.

I'll focus on the ones that most directly impact code cleanliness and reliability:
- Enum (8.1) — biggest impact on type safety
- Match expression (8.0) — cleaner control flow
- Readonly properties (8.2) — immutability
- Named arguments (8.0) — readability
- Union types + nullsafe (8.0) — conciseness
- First-class callable syntax (8.1) — cleaner callbacks
- Property hooks (8.4) — validation at the property level

I'll structure this as a concise but thorough answer with code examples for each.

</details>

**Кандидат:**

## Ключевые фичи PHP 8.0+ для чистого кода

### 1. Enums (8.1) — убивают «магические строки»

До:
```php
// где-то в констах
class OrderStatus
{
    public const PENDING = 'pending';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
}

// и нигде не видно, какие значения допустимы
public function setStatus(string $status): void { ... }
```

После:
```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => $target === self::Shipped,
            self::Shipped => $target === self::Delivered,
            self::Delivered => false,
        };
    }
}

public function setStatus(OrderStatus $status): void { ... }
```

Компилятор и IDE сразу знают весь допустимый набор значений. Невозможно передать `'shippd'` (опечатка).

---

### 2. Match expression (8.0) — без fallthrough, с типами

```php
// До: switch с fallthrough-ловушкой
$price = match (true) {
    $user->isVip() && $order->total > 1000 => 0.0,
    $user->isVip() => $order->total * 0.1,
    default => $order->total * 0.05,
};

// До: switch без return — легко забыть break
// После: match всегда возвращает значение, exhaustive с enum
public function discount(): float
{
    return match (true) {
        $this->user->isVip() && $this->total > 1000 => 0.0,
        $this->user->isVip()                        => $this->total * 0.1,
        default                                      => $this->total * 0.05,
    };
}
```

С enum вообще исключает «забытый case»:
```php
$label = match ($this->status) {
    OrderStatus::Pending   => 'В ожидании',
    OrderStatus::Shipped   => 'Отправлен',
    OrderStatus::Delivered => 'Доставлен',
};
// Если добавим новый case в enum — компилятор (static analysis) подсветит missing branch
```

---

### 3. Readonly properties (8.2) — безопасная неизменяемость

```php
final readonly class Money
{
    public function __construct(
        public int    $amount,
        public string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new CurrencyMismatchException($this->currency, $other->currency);
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}

// $money->amount = 999;  ← FATAL: Cannot modify readonly property
```

В value objects и DTO это даёт гарантию: объект после создания **неизменяем**. Никаких скрытых мутаций в глубине объекта.

---

### 4. Named arguments (8.0) — читаемость при длинных сигнатурках

```php
// До — адепту нужно гуглить, что за четвёртый параметр
$token = $jwtService->create(
    $userId,
    'access',
    null,
    3600,
    ['scope' => 'admin'],
);

// После — намерение встроено в вызов
$token = $jwtService->create(
    subject: $userId,
    type:    'access',
    ttl:     3600,
    claims:  ['scope' => 'admin'],
);
```

Особенно полезно, когда аргументы одного типа (`string $a, string $b, string $c`).

---

### 5. Union types + Nullsafe operator (8.0) — меньше шума

```php
// До
function formatName(User $user): string
{
    $name = $user->profile;
    if ($name !== null && $name->nickname !== null) {
        return $name->nickname;
    }
    return $user->firstName ?? 'Anonymous';
}

// После
function formatName(User $user): string
{
    return $user->profile?->nickname ?? $user->firstName ?? 'Anonymous';
}
```

Union types делают контракты честными:
```php
// Честная сигнатура: может вернуть строку или null
function findSlug(SlugRepository $repo, int $id): ?string
{
    return $repo->find($id)?->slug;
}
```

---

### 6. First-class callable syntax (8.1) — чистые колбэки

```php
// До — лишняя строка, потеря контекста
$sorted = array_map(fn (Product $p) => $p->price, $products);

// После — короче, особенно для static::method
$sorted = array_map($this->getPrice(), $products);
$sorted = array_map(Product::getPrice(), $products);
```

---

### 7. Property hooks (8.4) — валидация в самом свойстве

```php
final class User
{
    public string $email {
        set (string $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidEmailException($value);
            }
            $this->emailValue = strtolower($value);
        }

        get {
            return $this->emailValue;
        }
    }

    private string $emailValue = '';
}

// $user->email = 'not-an-email';  ← исключение прямо в точке присваивания
// Никаких setters, никакого "забыл позвать validate()"
```

Это заменяет паттерн «getter/setter + invariants» чем-то, что выглядит как обычное свойство, но несёт логику.

---

## Итог: что даёт совокупность

| Проблема | Фича | Эффект |
|----------|------|--------|
| Магические строки / опечатки | Enum + match | Типобезопасность на уровне языка |
| Скрытые мутации | Readonly | Value objects по умолчанию immutable |
| Вербозные null-проверки | `?->`, union types | Краткость без потери ясности |
| Нечитаемые вызовы | Named args | Самодокументируемый код |
| «Забытый» валидатор | Property hooks | Инварианты не обходятся |

Все фичи работают **на уровне языка** — не нужен внешний static analyzer, чтобы поймать ошибку. Это и есть главное отличие от PHP 7: язык сам становится барьером.

> ⏱ 15.1s · prompt 1051 tok · output 2062 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

The user is continuing the interview scenario. They're asking about PHP's type system: strict_types, type coercion behavior, common pitfalls, and examples where typing saves us vs. where it surprises.

I need to be thorough and practical, showing real-world examples. Let me organize this:

1. PHP's type system fundamentals — it's not a static type system, it's a runtime type system with optional static analysis
2. strict_types — how it works, the common misconception (it's per-file, not global)
3. Type coercion rules — numeric, string, array conversions
4. Common pitfalls — mixed types, array access, null coalescing, etc.
5. Examples where typing saves
6. Examples where it surprises

Let me think about what makes this genuinely useful for an interview answer:
- strict_types is PER-FILE, which is the #1 misconception
- strict_types only affects calls TO functions declared in the SAME file (actually, it affects calls FROM that file to functions declared in OTHER files)
- Wait, let me be precise: strict_types affects how the CALLER handles type coercion. If file A has `declare(strict_types=1)` and calls a function in file B, strict_types applies. If file A doesn't have it, the call is in coercive mode regardless of file B.
- This is a crucial nuance.

Type coercion rules:
- int → float (lossless)
- float → int (truncation)
- int → string
- string → int/float (if starts with number)
- bool → int (true=1, false=0)
- null → int/float (0) with deprecation in 8.1+
- null → string (empty string) with deprecation in 8.1+
- array → int/float (1/0, deprecation)
- array → string ("Array", deprecation in 8.1+ for some)

Wait, let me be more precise about PHP 8 behavior:
- Passing null to non-nullable internal function parameters: deprecated in 8.1
- Type juggling with null to int/float: deprecated in 8.1
- "Array" to string: was deprecated, removed in 8.0? No, I think it still works but...

Actually let me focus on what's genuinely useful and accurate:

Key points:
1. PHP is dynamically typed with runtime type checking
2. strict_types is per-file, affects outgoing calls
3. The coercion matrix
4. Common traps:
   - `declare(strict_types=1)` only in the CALLING file
   - `int` param receiving `"42"` in coercive mode
   - `string` param receiving `123` → `"123"`
   - `bool` param receiving `"0"` → `true` (non-empty string is truthy!)
   - Array access: `$arr["0"]` vs `$arr[0]`
   - `null` coercing to various types
   - `mixed` type
   - Return type coercion

Let me structure this well with code examples.

</details>

**Кандидат:**

## Система типов в PHP: механика и ловушки

### Фундамент: PHP — язык с динамической типизацией и опциональной статической проверкой

Типы в PHP проверяются **в рантайме**. Статический анализ (PHPStan, Psalm) — это надстройка, а не свойство языка. Это значит, что:

- Тип может быть `int` в одной строке и `float` в другой.
- Нет «компиляции» — только парсинг и исполнение.
- Объявление типа — это **контракт на границе**, а не гарантия внутренней целостности.

---

### 1. `strict_types` — и главная ошибка в понимании

#### Как это работает

```php
// file: PaymentProcessor.php
declare(strict_types=1);

class PaymentProcessor
{
    public function charge(int $amountInCents): string
    {
        // $amountInCents гарантированно int, если вызов из strict-файла
        return "Charged: {$amountInCents}";
    }
}
```

```php
// file: CheckoutController.php — БЕЗ strict_types
class CheckoutController
{
    public function __construct(private PaymentProcessor $processor) {}

    public function handle(Request $request): Response
    {
        // "100" — строка из HTTP-запроса
        // В coercive mode PHP приведёт "100" → 100 (int)
        $result = $this->processor->charge("100"); // ✅ работает
        return Response::ok($result);
    }
}
```

```php
// file: CheckoutControllerStrict.php — С strict_types
declare(strict_types=1);

class CheckoutControllerStrict
{
    public function __construct(private PaymentProcessor $processor) {}

    public function handle(Request $request): Response
    {
        $result = $this->processor->charge("100"); // ❌ TypeError
        return Response::ok($result);
    }
}
```

#### Критическая деталь

> `strict_types` — это свойство **файла-вызывающего**, а не файла-функции.

Если `PaymentProcessor.php` имеет `strict_types=1`, но `CheckoutController.php` нет — приведение разрешено. Наоборот — запрещено.

Это значит, что **один неосторожный файл без `strict_types`** в цепочке вызовов «сломает» строгость для всех, кто его вызывает:

```
strict_file_A.php → non_strict_file_B.php → strict_file_C.php
       ✅                   ✅                  ❌ (B вызывает C в coercive mode)
```

#### Практическое правило

```php
// В КАЖДОМ файле, наверху, сразу после <?php:
<?php
declare(strict_types=1);

namespace App\Domain\Order;
```

В Symfony 5+ это уже дефолт в `src/`. В legacy-коде — добавлять постепенно.

---

### 2. Матрица приведений (coercive mode)

Когда `strict_types` **отключён**, PHP позволяет:

| Источн. тип → Целевой | Результат | Пример |
|---|---|---|
| `"42"` → `int` | `42` | `charge("42")` ✅ |
| `"3.14"` → `int` | `3` (truncation) | |
| `42` → `string` | `"42"` | |
| `3.14` → `int` | `3` | |
| `true` → `int` | `1` | |
| `false` → `int` | `0` | |
| `null` → `int` | `0` (deprecated 8.1) | |
| `null` → `string` | `""` (deprecated 8.1) | |
| `"hello"` → `int` | `0` | |
| `["a"=>1]` → `string` | `"Array"` | |
| `"0"` → `bool` | **`false`** | ← ловушка! |
| `"abc"` → `bool` | **`true`** | ← ловушка! |

---

### 3. Типичные ловушки

#### Ловушка 1: `"0"` — строка, которая «лжёт»

```php
function isActive(string $flag): bool
{
    return (bool) $flag; // "0" → false, "abc" → true, "" → false
}

isActive("0");    // false — ок
isActive("false"); // TRUE — не ожидаемо!
isActive("no");    // TRUE — не ожидаемо!
isActive("");      // false — ок
```

**Решение:**
```php
function isActive(?string $flag): bool
{
    return $flag === '1' || $flag === 'true';
}
```

---

#### Ловушка 2: Сравнение с `==` vs `===`

```php
"0" == false;       // true
"abc" == 0;         // true (в PHP < 8)
"123abc" == 123;    // true (в PHP < 8)
"0e12" == 0;        // true (научная нотация!)
" 123" == 123;      // true

// PHP 8.0: "abc" == 0 → false (починили)
// Но "0e12" == 0 → true (всё ещё!)
```

```php
// Правило: === везде, где нет осознанного выбора
if ($user->role === 'admin') { ... }
```

---

#### Ловушка 3: Массивы и ключи

```php
$arr = ["0" => "zero", 1 => "one"];

var_dump(array_keys($arr));
// [0, 1]  ← строковый ключ "0" превратился в int!

$arr[0] = "zero";  // ✅ то же самое что $arr["0"]
$arr["0"] = "zero"; // ✅ то же самое что $arr[0]

// Но:
$arr["1"] = "one";  // ← это КЛЮЧ "1", а не 1.0 или что-то подобное
$arr[1]   = "one";  // ← это КЛЮЧ 1. И да, это ТОЖЕ самое.
```

---

#### Ловушка 4: `mixed` — тишина, которая убивает

```php
function process(mixed $data): void
{
    // $data может быть: null, int, float, string, array, object, bool
    // IDE не подскажет, static analyzer промолчит
    echo strlen($data); // 💥 TypeError если $data = null
}
```

`mixed` — это **антитеза типизации**. Если вы видите `mixed` в сигнатуре — это красный флаг.

---

#### Ловушка 5: Return type + strict_types = асимметрия

```php
// File A (strict_types=1)
declare(strict_types=1);
function getAge(): int { return 25; }

// File B (strict_types=0)
declare(strict_types=0);
function wrapAge(): int { return getAge(); }
// ✅ работает, т.к. int → int

function wrapAgeFloat(): float { return getAge(); }
// ✅ работает, т.к. int → float (lossless)

function wrapAgeString(): string { return getAge(); }
// ✅ в coercive mode: int → string. В strict — TypeError!
```

---

#### Ловушка 6: `null` и nullable — граница, которую легко потерять

```php
function findEmail(int $userId): ?string
{
    return $db->query("SELECT email FROM users WHERE id = ?", [$userId])->fetchColumn();
}

// fetchColumn() возвращает string|false|null
// Мы объявили ?string — но false превратится в "" (deprecation в 8.1)

$email = findEmail(42); // может быть "" вместо null — и дальше $email !== null → true
```

**Решение:**
```php
function findEmail(int $userId): ?string
{
    $result = $db->query("SELECT email FROM users WHERE id = ?", [$userId])->fetchColumn();
    return $result === false ? null : $result;
}
```

---

### 4. Где типизация **спасает**

```php
// Без типов: баг живёт 3 месяца
function process(array $order): float
{
    return $order['total'] * $order['rate'];
    // $order['total'] может быть "100.5" (из JSON) или null (из БД)
}

// С типами + strict_types: баг умирает на границе
final class Order
{
    public function __construct(
        public readonly Money  $total,
        public readonly Rate   $rate,
    ) {}
}

function process(Order $order): float
{
    return $order->total->toCents() * $order->rate->value;
    // Money гарантирует int, Rate гарантирует float
    // Невозможно передать "abc" или null
}
```

---

### 5. Где типизация **удивляет**

```php
// Ситуация: GraphQL возвращает mixed
// Вы объявили параметр int, а получили строку из JSON

declare(strict_types=1);

function addTax(int $amount, int $ratePercent): int
{
    return $amount + (int) round($amount * $ratePercent / 100);
}

// GraphQL response: {"amount": "1500", "rate": "20"}
addTax($response['amount'], $response['rate']); // ❌ TypeError
// Но вы ожидаете "просто работает, PHP же гибкий"

// Реальность: strict_types превращает «тихий» баг (1500.0 * 20)
// в явную ошибку в точке входа — это ХОРОШО.
```

```php
// Удивление: int overflow
function factorial(int $n): int
{
    $result = 1;
    for ($i = 2; $i <= $n; $i++) {
        $result *= $i;
    }
    return $result;
}

factorial(21); // 5109094217170944000 → int overflow → float!
// Возвратный тип int, но фактическое значение — float
// strict_types не проверяет RETURN при вызове (только параметры при вызове)
// Фактически: PHP вернёт float, и если вызывающая сторона ожидает int — TypeError
// Но внутри функции вы уже работаете с float и не знаете об этом.
```

```php
// Удивление: `bool` и строки
declare(strict_types=1);

function shouldSendEmail(bool $flag): string
{
    return $flag ? 'sent' : 'skipped';
}

$raw = "0"; // из HTTP header
shouldSendEmail($raw); // ❌ TypeError — хорошо
// Но в coercive mode:
// shouldSendEmail("0") → false → 'skipped'  ← молчаливое "правильное" поведение,
// которое маскирует, что вы передали строку, а не bool
```

---

### 6. Итоговая таблица

| Аспект | Что даёт | Где боль |
|--------|----------|----------|
| `strict_types` | Убивает неявные приведения на границе функций | Per-file! Один слабый файл ломает цепочку |
| Union types | Честные контракты (`?int`, `int\|float`) | Не заменяет nullable-аннотации в старых проектах |
| `readonly` | Неизменяемость без effort | Не наследуется, не работает с `__get` |
| `enum` | Закрытый набор значений | Нет pattern matching на уровне `switch` (только `match`) |
| `never` / `void` | Честные сигнатурки | `void` ≠ `never` (void может быть null в legacy) |
| `mixed` | Гибкость | Анти-типизация: IDE и анализаторы бессильны |

---

### Практический чеклист

1. **`declare(strict_types=1);`** — в каждом файле, без исключений.
2. **`===` / `!==`** — везде, кроме осознанных исключений (например, сравнение с `0`).
3. **Никакого `mixed`** в публичных API. Если данные «грязные» — чистите на границе (DTO).
4. **Nullable — осознанно.** `?int` лучше, чем `int` + `null` в runтайме.
5. **`int` vs `float`** — в деньгах только `int` (центы). Float — только для вычислений.
6. **`bool` из строк** — только через явный маппинг: `$value === 'true'`, никогда `(bool)$value`.

> ⏱ 29.1s · prompt 2749 tok · output 3717 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user asks about exception handling design in PHP applications. This is a senior-level topic. Let me structure a comprehensive answer covering:

1. Exception hierarchy design principles
2. Domain exceptions vs technical exceptions
3. Wrapping third-party exceptions
4. What constitutes a "contract" for a method
5. Anti-patterns

I should be practical, show real code examples, and be honest about trade-offs. Let me think about what makes a truly good answer here:

- Domain-driven exception hierarchy (not just "AppException extends Exception")
- The principle that exceptions should reflect the *cause* or the *state*, not the handling
- Wrapping: when to wrap, when not to, how to preserve context
- Method contracts: what exceptions a method may throw should be part of its API
- Anti-patterns: catching Exception\Throwable broadly, using exceptions for control flow, leaking infrastructure details, over-throwing, swallowing

Let me structure this clearly with code examples that show progression from bad to good.

</details>

**Кандидат:**

## Проектирование работы с исключениями в PHP

### 1. Философия: исключение — это **контракт**, а не логика

Исключение отвечает на вопрос: *«Что пошло не так и что я, вызывающая сторона, могу с этим сделать?»*

Если ответ — «ничего, просто логгируй и пробуй ещё раз» — это **не** исключение. Это `?null` или `Result`.

```php
// ❌ Исключение для контроля потока
try {
    $product = $repo->find($id);
} catch (ProductNotFoundException $e) {
    return $fallback;
}

// ✅ Контракт: «могу не найти»
public function find(int $id): ?Product
{
    return $this->db->fetchOrNull(...);
}

$product = $repo->find($id) ?? $fallback;
```

---

### 2. Иерархия: две ветки, не одна

```
DomainException (абстракция)
├── EntityNotFoundException
├── StateTransitionException
├── BusinessRuleViolationException
└── ...

TechnicalException (абстракция)
├── DatabaseException
├── NetworkException
└── ExternalServiceException
```

#### Почему две ветки

**Domain-исключения** — это ответы на вопросы бизнеса. Их поймает контроллер, вернёт 4xx.

**Technical-исключения** — это ответы на вопросы инфраструктуры. Их поймает middleware, вернёт 5xx, залоггирует.

Если смешать — бизнес-логика начинает зависеть от деталей инфраструктуры:

```php
// ❌ Смешение слоёв
try {
    $order = $orderService->cancel($id);
} catch (Doctrine\DBAL\Exception\DriverException $e) {
    // Почему сервис заказа знает про Doctrine?
    return new Response('500', 'DB failed');
}

// ✅ Слои разделены
// Domain layer:
class OrderCancellation
{
    public function execute(int $orderId): Order
    {
        $order = $this->repo->findById($orderId);
        if ($order->isDelivered()) {
            throw new StateTransitionException('Cannot cancel delivered order');
        }
        $order->cancel();
        $this->repo->save($order);
        return $order;
    }
}

// Application layer (обёртка):
class OrderService
{
    public function cancel(int $orderId): Order
    {
        try {
            return (new OrderCancellation($this->repo))->execute($orderId);
        } catch (EntityNotFoundException $e) {
            throw new OrderNotFoundException($orderId, $e); // техническая обёртка
        }
        // Technical exceptions propagate up — их ловит глобальный handler
    }
}
```

---

### 3. Конструктор и контекст

```php
abstract class DomainException extends \DomainException
{
    /**
     * @param string $message Человекочитаемое описание (для логга, не для API)
     * @param array  $context Структурные данные (для API, для аналитики)
     */
    public function __construct(
        string $message,
        private array $context = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function context(): array
    {
        return $this->context;
    }
}

final class InsufficientBalanceException extends DomainException
{
    public function __construct(
        public readonly string $accountId,
        public readonly int    $required,
        public readonly int    $available,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: "Insufficient balance for account {$accountId}",
            context: [
                'account_id' => $accountId,
                'required'   => $required,
                'available'  => $available,
            ],
            previous: $previous,
        );
    }
}
```

**Ключевой принцип:** `message` — для разработчика и лога. `context` — для потребителя. Никогда не парсишь `message` для извлечения данных.

---

### 4. Обёртка чужих исключений

#### Правило: **переводи на язык домена**

```php
// ❌ Утечка деталей инфраструктуры
class PaymentService
{
    public function charge(string $card, int $amount): Receipt
    {
        // Stripe API, HTTP, JSON — всё это «чужое»
        return $this->stripe->charges->create([...]); // выбрасывает StripeException
    }
}

// Вызывающая сторона знает про Stripe:
try {
    $receipt = $paymentService->charge($card, 1000);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Зачем мне знать про Stripe?
}
```

```php
// ✅ Перевод на язык домена
class PaymentService
{
    public function charge(string $cardToken, int $amountInCents, string $currency): Receipt
    {
        try {
            $charge = $this->stripe->charges->create([
                'amount'   => $amountInCents,
                'currency' => $currency,
                'source'   => $cardToken,
            ]);

            return Receipt::fromStripe($charge);
        } catch (\Stripe\Exception\CardException $e) {
            throw new PaymentDeclinedException(
                reason: $e->getDeclineCode(),
                message: $e->getMessage(),
                previous: $e,
            );
        } catch (\Stripe\Exception\RateLimitExceededException $e) {
            throw new PaymentProviderOverloadedException(
                retryAfter: $e->getRetryAfter(),
                previous: $e,
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentProcessingException(
                provider: 'stripe',
                previous: $e,
            );
        }
    }
}
```

#### Когда НЕ оборачивать

| Ситуация | Почему |
|----------|--------|
| Вы пробрасываете как есть и вызывающая сторона **уже знает** об этом API | Обёртка без переименования — шум |
| Исключение содержит данные, которые **нельзя** выразить в своём домене | Лучше `previous` + `context` |
| Внутренняя утилита, не имеющая «языка домена» | `TechnicalException` + `previous` |

---

### 5. Что считается контрактом метода

Контракт метода = **документированный набор исключений**, которые метод может бросить.

В PHP нет `throws` в сигнатуре (как в Java), поэтому контракт живёт в:

1. **PHPDoc**
2. **Имя и иерархия исключения**
3. **Конвенция проекта**

```php
/**
 * @throws OrderNotFoundException       Если заказа с таким ID нет
 * @throws StateTransitionException     Если заказ не можно отменить
 * @throws InsufficientBalanceException Если средств не хватает
 *
 * НЕ бросает:
 *  - TechnicalException — они перехвачены внутри и превращены в доменные
 *  - Exception\Throwable — если бросает, это баг
 */
public function cancel(int $orderId): Order
{
    ...
}
```

**Практическое правило:** если метод бросает > 3 разных исключений — он делает слишком много. Разбей.

---

### 6. Глобальный handler — последняя линия обороны

```php
// In Symfony: ExceptionSubscriber
final class ExceptionSubscriber extends KernelEventSubscriber
{
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof DomainException) {
            $event->setResponse($this->jsonResponse(
                status: 422,
                body: [
                    'error'   => $exception::class,
                    'message' => $exception->getMessage(),
                    'context' => $exception->context(),
                ],
            ));
            return;
        }

        if ($exception instanceof PaymentProviderOverloadedException) {
            $event->setResponse($this->jsonResponse(
                status: 503,
                body: [
                    'error'      => 'provider_overloaded',
                    'retryAfter' => $exception->retryAfter,
                ],
            ));
            return;
        }

        // Всё остальное — 500, без утечки деталей
        $this->logger->critical($exception->getMessage(), [
            'exception' => $exception::class,
            'trace'     => $exception->getTraceAsString(),
            'previous'  => $exception->getPrevious()?->getMessage(),
        ]);

        $event->setResponse($this->jsonResponse(
            status: 500,
            body: ['error' => 'internal_error'],
        ));
    }
}
```

**Никогда** не вертай `getMessage()` или `getTrace()` в ответ клиенту для technical-исключений.

---

### 7. Антипаттерны

#### ❌ 1. `catch (\Exception $e)` — «я знаю, что делаю»

```php
try {
    $this->process($data);
} catch (\Exception $e) {
    // Ты поймал: TypeError, LogicException, OOM, SIGSEGV
    // И вернул 200 OK. Клиент думает, что всё хорошо.
    return new Response('ok');
}
```

**Правильно:** лови конкретные типы. Если правда «всё» — логгируй, перебрось или верни 500.

---

#### ❌ 2. Исключение для контроля потока

```php
public function findByEmail(string $email): User
{
    $user = $this->db->query("SELECT * FROM users WHERE email = ?", [$email])->fetchAssociative();
    if ($user === false) {
        throw new NotFoundException("User not found: {$email}");
    }
    return new User($user);
}

// Вызывающая сторона:
$user = $repo->findByEmail($email); // бросает, если нет
// vs
$user = $repo->findByEmail($email) ?? $default; // ?? не работает с исключениями!
```

Если «не найдено» — **нормальное состояние** — возвращай `?User` или `Result<User, NotFound>`.

---

#### ❌ 3. Утечка деталей

```php
catch (Doctrine\DBAL\Exception\TableNotFoundException $e) {
    return new JsonResponse(['error' => $e->getMessage()]);
    // "Table users not found" — клиент знает про твою схему БД
}
```

---

#### ❌ 4. `throw new Exception("Something went wrong")`

Без контекста, без типа, без `previous`. В логе — 200 таких строк. Никто не знает, что делать.

---

#### ❌ 5. Поля в исключении как DTO

```php
final class OrderNotFoundException extends \Exception
{
    public readonly int $orderId;
    public readonly string $suggestedUrl;
    public readonly array $recentOrders;
}
```

Исключение — не место для передачи бизнес-данных. Это **ошибка**, а не **ответ**.

---

#### ❌ 6. Перебрасывание с потерянным контекстом

```php
catch (PaymentDeclinedException $e) {
    throw new DomainException('Payment failed');
    // Потеряли: reason, declineCode, предыдущее исключение
}
```

**Всегда** передавай `previous`:
```php
throw new DomainException('Payment failed', previous: $e);
```

---

### 8. Итого: чеклист проектирования

| Вопрос | Ответ |
|--------|-------|
| Это исключение или `null`/`Result`? | Если «не найдено» — нормальный исход → `?T` |
| Домен или техника? | Домен → 4xx, техника → 5xx |
| Я передаю `previous`? | Всегда, если обёртка |
| `message` содержит данные для API? | Нет, только `context` |
| Клиент видит детали инфраструктуры? | Нет |
| Метод бросает > 3 типов? | Разбей или пересмотри контракт |
| Есть `catch (\Exception)`? | Убери или обоснуй в комментарии |

---

Готов продолжить: можно поговорить о `Result`-монاده в PHP, о том, как это связывается с DDD-событиями, или перейти к другому разделу.

> ⏱ 24.0s · prompt 5857 tok · output 3151 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

The user asks which SOLID principle is most commonly violated in real code, why, and wants a typical example with refactoring.

Let me think about this honestly from a practitioner's perspective:

1. **SRP (Single Responsibility)** — violated almost everywhere, but it's so broad that "violating SRP" is almost trivially true. Almost every class has more than one responsibility. It's the most violated but also the most vaguely defined.

2. **OCP (Open/Closed)** — violated constantly because people add `if/else` or `switch` statements instead of using polymorphism. This is very visible and painful.

3. **LSP (Liskov Substitution)** — violated when subclasses don't honor the contract. Common in frameworks (e.g., extending a base controller that assumes certain methods exist).

4. **ISP (Interface Segregation)** — violated when interfaces have too many methods and implementers must implement stubs. Common in legacy code with "God interfaces".

5. **DIP (Dependency Inversion)** — violated when high-level modules depend on low-level concrete classes. Very common.

From real experience, I'd say **SRP** is the most violated in absolute terms, but **OCP** is the most *painful* violation because it creates the cascading `if/else` hell that makes codebases unmanageable.

Actually, let me reconsider. In my experience as a PHP developer:

- **SRP** is violated in almost every class. It's the root cause of many other violations.
- **OCP** violations are the most *visible* and *expensive* — they create the "switch statement that grows forever" anti-pattern.

I think the honest answer is: **SRP** is the most violated, and it's the root cause of the others. But the most *painful* and *expensive* violation in practice is **OCP** because it directly causes the exponential growth of conditional logic.

Let me pick **SRP** as the primary answer (it's the most violated and the root cause), but I'll make the example concrete and show how it leads to OCP violations too.

Actually, let me reconsider again. The question asks "по твоему опыту" (from my experience). I should give a definitive, honest answer with conviction.

I'll go with **SRP** as the most violated, because:
1. It's the most common in practice
2. It's the root cause of many other violations
3. It's the hardest to define precisely (what counts as "one responsibility"?)
4. It's where most codebases start to rot

I'll show a concrete example: a `UserController` or `OrderService` that does too much, then refactor it.

Let me write a good example. I'll use a common real-world case: a service class that handles validation, business logic, persistence, and notification all in one.

Let me structure my answer:
1. State my answer clearly
2. Explain WHY it's the most violated (not just "because it is")
3. Show a concrete before/after
4. Explain the deeper insight

I'll use a realistic PHP example — maybe a `RegistrationService` or `OrderService` that's a god-class.

</details>

**Кандидат:**

## Самый нарушаемый принцип: SRP

И вот почему это не банальная ответка «ну, класс делает много вещей».

### Почему именно SRP — честный ответ

1. **Это корневая причина остальных нарушений.** Когда класс берёт на себя 5 ответственностей, он неизбежно закрывается для расширения (OCP), зависит от конкретных реализаций (DIP), а его «интерфейс» становится широким (ISP).

2. **Нет объективного критерия.** «Одна причина меняться» — звучит просто, но на практике каждый по-разному определяет, что считается «причиной». В результате люди не осознают, что нарушают SRP, пока не становится больно.

3. **Давление дедлайна.** SRP — это *расходы сейчас* (больше классов, больше файлов, больше wiring) ради *выгоды потом*. В спринте всегда выигрывает «просто добавь метод в существующий класс».

---

### Типовой пример: `OrderService`

Это класс, который я встречал в 8 из 10 проектов, где был «один сервис на фичу».

```php
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private PaymentGateway  $payment,
        private EmailService    $email,
        private InventoryClient $inventory,
        private EventDispatcher $events,
        private LoggerInterface $logger,
    ) {}

    public function place(array $data): array
    {
        // 1. Валидация
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email');
        }
        if (empty($data['items'])) {
            throw new \Exception('No items');
        }
        foreach ($data['items'] as $item) {
            if (!isset($item['product_id'], $item['quantity']) || $item['quantity'] < 1) {
                throw new \Exception('Invalid item');
            }
        }

        // 2. Проверка и резервирование инвентаря
        foreach ($data['items'] as $item) {
            $stock = $this->inventory->getStock($item['product_id']);
            if ($stock < $item['quantity']) {
                throw new \Exception('Out of stock: ' . $item['product_id']);
            }
            $this->inventory->reserve($item['product_id'], $item['quantity']);
        }

        // 3. Расчёт цены (с промокодами, скидками, НДС)
        $total = 0;
        $discount = 0;
        foreach ($data['items'] as $item) {
            $product = $this->repo->findProduct($item['product_id']);
            $lineTotal = $product['price'] * $item['quantity'];
            if (isset($data['promo_code']) && $data['promo_code'] === 'VIP10') {
                $discount += $lineTotal * 0.10;
            }
            $total += $lineTotal;
        }
        $total -= $discount;
        $tax = $total * 0.20;
        $total += $tax;

        // 4. Оплата
        try {
            $paymentResult = $this->payment->charge($data['card_token'], (int) ($total * 100));
        } catch (\Exception $e) {
            foreach ($data['items'] as $item) {
                $this->inventory->release($item['product_id'], $item['quantity']);
            }
            throw new \Exception('Payment failed: ' . $e->getMessage());
        }

        // 5. Сохранение
        $order = [
            'email'     => $data['email'],
            'items'     => $data['items'],
            'total'     => $total,
            'tax'       => $tax,
            'discount'  => $discount,
            'status'    => 'paid',
            'payment_id'=> $paymentResult['id'],
            'created_at'=> date('Y-m-d H:i:s'),
        ];
        $orderId = $this->repo->save($order);

        // 6. Отправка письма
        $this->email->send(
            to: $data['email'],
            subject: 'Order confirmed',
            body: "Thank you! Order #{$orderId} for " . number_format($total, 2) . " EUR"
        );

        // 7. Событие
        $this->events->dispatch(new OrderPlaced($orderId));

        // 8. Логирование
        $this->logger->info('Order placed', ['order_id' => $orderId, 'total' => $total]);

        return $order;
    }
}
```

#### Что тут нарушено (и почему каждый пункт — SRP)

| # | Ответственность | Почему это отдельная причина меняться |
|---|----------------|--------------------------------------|
| 1 | Валидация входных данных | Меняется при изменении правил валидации, форм, API |
| 2 | Резервирование инвентаря | Меняется при смене поставщика, логике резервирования |
| 3 | Расчёт цены | Меняется при каждом новом промокоде, изменении НДС |
| 4 | Оплата | Меняется при смене платёжного провайдера |
| 5 | Персист | Меняется при миграции БД |
| 6 | Email | Меняется при смене шаблона, провайдера, языка |
| 7 | События | Меняется при добавлении новых слушателей |
| 8 | Логирование | Меняется при смене уровня, формата |

**8 причин меняться в одном методе.** Каждый из этих пунктов — отдельный класс.

#### Последствия, которые я наблюдал в проде

- Добавили промокод «BOGO» →改или метод `place()` → сломали VIP10, потому что логика discount была встроена в цикл расчёта.
- Сменили платёжный провайдер → нужно было трогать `OrderService` → регрессия в тесте на валидацию.
- Добавили «отправить SMS вместо email для VIP» → `if ($user->isVip)` прямо в методе `place()`.
- Метод вырос до 200+ строк. Review превращался в «надеюсь, ничего не сломалось».

---

### Рефакторинг

```php
// ─── Domain Layer ───────────────────────────────────────────

final class Order
{
    /** @param OrderItem[] $items */
    private function __construct(
        public readonly string    $email,
        public readonly array     $items,
        public readonly Money     $total,
        public readonly Money     $tax,
        public readonly Money     $discount,
        public readonly OrderStatus $status,
        public readonly string    $paymentId,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public function toDto(): array { /* ... */ }
}

final class Money
{
    private function __construct(public readonly int $cents, public readonly string $currency) {}

    public static function fromCents(int $cents, string $currency = 'EUR'): self
    {
        return new self($cents, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException();
        }
        return new self($this->cents + $other->cents, $this->currency);
    }

    public function subtract(self $other): self
    {
        return $this->add($other->negate());
    }

    public function multiply(float $multiplier): self
    {
        return new self((int) round($this->cents * $multiplier), $this->currency);
    }

    public function negate(): self
    {
        return new self(-$this->cents, $this->currency);
    }
}

final class OrderItem
{
    public function __construct(
        public readonly int   $productId,
        public readonly int   $quantity,
        public readonly Money $unitPrice,
    ) {}

    public function lineTotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}

final class OrderStatus
{
    public const CREATED  = 'created';
    public const PAID     = 'paid';
    public const SHIPPED  = 'shipped';
    public const CANCELLED = 'cancelled';
}

// ─── Domain Services ────────────────────────────────────────

final class OrderValidator
{
    /** @throws InvalidEmailException */
    /** @throws NoItemsException */
    /** @throws InvalidItemException */
    public function validate(array $data): void
    {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException((string) ($data['email'] ?? ''));
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new NoItemsException();
        }

        foreach ($data['items'] as $i => $item) {
            if (!isset($item['product_id'], $item['quantity']) || (int)$item['quantity'] < 1) {
                throw new InvalidItemException(index: $i, raw: $item);
            }
        }
    }
}

interface StockProviderInterface
{
    public function getStock(int $productId): int;
    public function reserve(int $productId, int $quantity): void;
    public function release(int $productId, int $quantity): void;
}

final class InventoryStockProvider implements StockProviderInterface
{
    public function __construct(private InventoryClient $client) {}

    public function getStock(int $productId): int
    {
        return $this->client->getStock($productId);
    }

    public function reserve(int $productId, int $quantity): void
    {
        $this->client->reserve($productId, $quantity);
    }

    public function release(int $productId, int $quantity): void
    {
        $this->client->release($productId, $quantity);
    }
}

final class PricingCalculator
{
    public function __construct(
        private PromoCodeRepository $promos,
    ) {}

    /** @param OrderItem[] $items */
    public function calculate(array $items, ?string $promoCode, Money $baseTotal): PriceBreakdown
    {
        $discount = $this->applyPromo($promoCode, $baseTotal);
        $total    = $baseTotal->subtract($discount);
        $tax      = $total->multiply(0.20);
        $grandTotal = $total->add($tax);

        return new PriceBreakdown(
            total: $grandTotal,
            tax:   $tax,
            discount: $discount,
        );
    }

    private function applyPromo(?string $code, Money $baseTotal): Money
    {
        if ($code === null) {
            return Money::fromCents(0);
        }

        $promo = $this->promos->find($code);
        if ($promo === null) {
            return Money::fromCents(0);
        }

        return $baseTotal->multiply($promo->discountRate);
    }
}

interface PaymentProcessorInterface
{
    public function charge(string $cardToken, int $amountInCents, string $currency): PaymentResult;
}

final class StripePaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(private \Stripe\StripeClient $stripe) {}

    public function charge(string $cardToken, int $amountInCents, string $currency): PaymentResult
    {
        $charge = $this->stripe->charges->create([
            'amount'   => $amountInCents,
            'currency' => strtolower($currency),
            'source'   => $cardToken,
        ]);

        return new PaymentResult(
            id: $charge->id,
            status: $charge->status,
        );
    }
}

interface OrderRepositoryInterface
{
    public function save(Order $order): int;
    public function find(int $id): ?Order;
}

// ─── Application Layer (Use Case) ───────────────────────────

final class PlaceOrderUseCase
{
    public function __construct(
        private OrderValidator        $validator,
        private StockProviderInterface $stock,
        private PricingCalculator     $pricing,
        private PaymentProcessorInterface $payment,
        private OrderRepositoryInterface $repo,
        private EventDispatcherInterface $events,
        private NotificationServiceInterface $notifications,
    ) {}

    public function execute(PlaceOrderCommand $cmd): Order
    {
        // 1. Валидация
        $this->validator->validate($cmd->rawData);

        // 2. Инвентарь
        $this->stock->reserveAll($cmd->items);

        try {
            // 3. Расчёт
            $baseTotal = $this->sumItems($cmd->items);
            $breakdown = $this->pricing->calculate($cmd->items, $cmd->promoCode, $baseTotal);

            // 4. Оплата
            $payment = $this->payment->charge(
                cardToken: $cmd->cardToken,
                amountInCents: $breakdown->total->cents,
                currency: $breakdown->total->currency,
            );

            // 5. Агрегат
            $order = new Order(
                email:     $cmd->email,
                items:     $cmd->items,
                total:     $breakdown->total,
                tax:       $breakdown->tax,
                discount:  $breakdown->discount,
                status:    OrderStatus::PAID,
                paymentId: $payment->id,
                createdAt: new \DateTimeImmutable(),
            );

            // 6. Сохранение
            $orderId = $this->repo->save($order);

            // 7. Уведомление
            $this->notifications->sendOrderConfirmation($order);

            // 8. Событие
            $this->events->dispatch(new OrderPlacedEvent($orderId));

            return $order;

        } catch (\Throwable $e) {
            // Компенсация: освободить инвентарь
            $this->stock->releaseAll($cmd->items);
            throw $e;
        }
    }

    /** @param OrderItem[] $items */
    private function sumItems(array $items): Money
    {
        $total = Money::fromCents(0);
        foreach ($items as $item) {
            $total = $total->add($item->lineTotal());
        }
        return $total;
    }
}

// ─── Presentation Layer ─────────────────────────────────────

final class PlaceOrderController
{
    public function __construct(private PlaceOrderUseCase $useCase) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        $command = new PlaceOrderCommand(
            email:     $data['email'] ?? '',
            cardToken: $data['card_token'] ?? '',
            promoCode: $data['promo_code'] ?? null,
            items:     $this->buildItems($data['items'] ?? []),
            rawData:   $data,
        );

        $order = $this->useCase->execute($command);

        return new JsonResponse($order->toDto(), 201);
    }
}
```

---

### Что изменилось — и почему это не просто «разбил на классы»

| Аспект | До | После |
|--------|----|----|
| **Причины меняться** | 8 в одном методе | Каждая — свой класс |
| **Оплата** | Зависимость от Stripe в `OrderService` | `PaymentProcessorInterface` → можно подменить на Adyen за 1 день |
| **Промокоды** | `if === 'VIP10'` | `PromoCodeRepository` → новый промокод = новая сущность, не новый `if` |
| **Инвентарь** | `InventoryClient` напрямую | `StockProviderInterface` → можно тестировать с fake |
| **Тестируемость** | Мокали 6 зависимостей | Мокали 2–3 в каждом юнит-тесте |
| **Ошибки** | `Exception('Payment failed')` | `PaymentDeclinedException`, `InsufficientStockException` — разные коды, разные действия |
| **Compensation** | Ручной `release` в `catch` | Ограничена областью `try`, легко добавить saga |

---

### Глубже: почему SRP — это про **причины меняться**, а не про размер

Частая ошибка: «Мой класс 300 строк — значит, он нарушает SRP». Нет.

Класс на 2000 строк может иметь **одну** причину меняться (например, `Lexer` в парсере). А класс на 30 строк может иметь три.

Критерий: **сколько разных бизнес-решений нужно принять, чтобы изменить этот класс?**

- «Меняем правила скидок» → `PricingCalculator`
- «Меняем платёжного провайдера» → `StripePaymentProcessor`
- «Меняем текст письма» → `NotificationService`

Если ответ — «один класс, метод `place()`» — значит, у вас 8 причин меняться в одном месте. Это SRP-нарушение, независимо от объёма кода.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Можно ли нарушать SOLID?

Да. SOLID — это не законы физики, а **набор компромиссов**. Каждый принцип говорит: *«Если X, то Y будет лучше»*. Если X не выполняется — Y становится хуже, а не лучше.

Правильная формулировка не «не нарушай SOLID», а:

> **Нарушай осознанно, когда стоимость абстракции выше стоимости, которую она экономит.**

---

### Пример: «чистый» SOLID делает код хуже

#### Ситуация

Внутренний микросервис. Один сценарий: проверить, можно ли сделать запрос, если можно — засечь и пропустить, если нет — бросить `RateLimitExceededException`.

Нужно:
- Проверить, сколько запросов уже было за окно (Redis).
- Если лимит не превышен — инкрементировать счётчик.
- Если превышен — бросить исключение.

#### «Правильный» SOLID-вариант (SRP + OCP + DIP)

```php
// ─── 1. Clock ──────────────────────────────────────────────
interface ClockInterface
{
    public function now(): int;
}

final class SystemClock implements ClockInterface
{
    public function now(): int
    {
        return time();
    }
}

// ─── 2. Store ──────────────────────────────────────────────
interface RateLimitStoreInterface
{
    public function count(string $key, int $windowStart): int;
    public function increment(string $key, int $windowStart): void;
}

final class RedisRateLimitStore implements RateLimitStoreInterface
{
    public function __construct(private \Redis $redis) {}

    public function count(string $key, int $windowStart): int
    {
        return (int) $this->redis->get("rl:{$key}:{$windowStart}");
    }

    public function increment(string $key, int $windowStart): void
    {
        $fullKey = "rl:{$key}:{$windowStart}";
        $this->redis->incr($fullKey);
        $this->redis->expire($fullKey, 60);
    }
}

// ─── 3. Policy ─────────────────────────────────────────────
interface RateLimitPolicyInterface
{
    public function isAllowed(int $currentCount, int $limit): bool;
}

final class FixedWindowPolicy implements RateLimitPolicyInterface
{
    public function isAllowed(int $currentCount, int $limit): bool
    {
        return $currentCount < $limit;
    }
}

// ─── 4. Limiter ────────────────────────────────────────────
final class RateLimiter
{
    public function __construct(
        private ClockInterface          $clock,
        private RateLimitStoreInterface $store,
        private RateLimitPolicyInterface $policy,
        private int                     $limit,
    ) {}

    public function check(string $key): void
    {
        $windowStart = (int) ($this->clock->now() / 60) * 60;
        $count = $this->store->count($key, $windowStart);

        if (!$this->policy->isAllowed($count, $this->limit)) {
            throw new RateLimitExceededException($this->limit);
        }

        $this->store->increment($key, $windowStart);
    }
}
```

**4 интерфейса, 5 классов, 7 файлов.** Для 8 строк логики.

---

#### «Нарушающий» вариант — и почему он лучше

```php
final class RateLimiter
{
    public function __construct(
        private \Redis $redis,
        private int    $limit,
    ) {}

    public function check(string $key): void
    {
        $window = (int) (time() / 60) * 60;
        $fullKey = "rl:{$key}:{$window}";
        $count = (int) $this->redis->get($fullKey);

        if ($count >= $this->limit) {
            throw new RateLimitExceededException($this->limit);
        }

        $this->redis->incr($fullKey);
        $this->redis->expire($fullKey, 60);
    }
}
```

**1 класс, 1 файл, 12 строк.**

---

### Что именно «нарушено» и почему это ок

| Принцип | «Нарушение» | Почему это оправдано |
|---------|-------------|---------------------|
| **SRP** | Класс и валидирует, и читает, и пишет | Три действия связаны **одной транзакцией атомарности**. Разделить — значит потерять атомарность или добавить координатор |
| **OCP** | Новый алгоритм (sliding window) потребует изменения этого класса | Алгоритм **не меняется**. Если изменится — это новый класс, а не новый `PolicyInterface` |
| **DIP** | Зависимость от `\Redis`, а не от `RateLimitStoreInterface` | `\Redis` — это **конфигурационный параметр** (скажем, «какой Redis использовать»), а не «какой алгоритм хранилища». Интерфейс здесь — церемония |
| **ISP** | — | Не применимо |
| **LSP** | — | Не применимо |

---

### Критерий: когда абстракция **не** оправдана

Я использую три вопроса:

| Вопрос | Если «да» |
|--------|-----------|
| Существует **реальная** вторая реализация (не гипотетическая)? | Нет → интерфейс = шум |
| Ответственность меняется **независимо** от других? | Нет → они одна причина меняться |
| Читатель **потеряет контекст**, прыгая между файлами? | Да → слей в один класс |

Для `RateLimiter`:
- Вторая реализация? Нет. `FixedWindow` — единственный алгоритм.
- Независимое изменение? Нет. Если меняется окно — меняется и хранилище, и политика.
- Контекст? 12 строк — читается за 2 секунды. 7 файлов — 30 секунд + «а что делает `ClockInterface`?»

→ Один класс. Точка.

---

### Когда тот же код **требует** SOLID

А вот если:

```
1. Нужен sliding window И fixed window — и переключаются на лету.
2. Нужно тестировать без Redis (интеграционные тесты в CI без docker).
3. Через 2 месяца добавляется second-level granularity.
```

Тогда:

```php
interface RateLimitStoreInterface { ... }  // для теста без Redis
interface RateLimitPolicyInterface { ... } // для переключения алгоритмов
```

И тогда 7 файлов — **не** переусложнение, а **минимум**, необходимый для изменения.

---

### Ещё один пример: OCP-нарушение в контроллере

```php
// ❌ «Чистый» OCP: strategy + registry + 3 реализатора
class OrderStatusHandler {
    // 40 строк оркестрации для switch по 3 случая
}
class PendingHandler implements StatusHandlerInterface { ... }  // 5 строк
class ShippedHandler implements StatusHandlerInterface { ... }   // 5 строк
class DeliveredHandler implements StatusHandlerInterface { ... } // 5 строк

// ✅ Нарушение OCP: switch в одном месте
public function getStatusLabel(OrderStatus $status): string
{
    return match ($status) {
        OrderStatus::Pending   => 'В обработке',
        OrderStatus::Shipped   => 'Отправлен',
        OrderStatus::Delivered => 'Доставлен',
    };
}
```

**Почему:** 3 статуса. Они **не растут** (enum закрыт). Каждый «handler» — одна строка. Strategy pattern здесь добавляет 4 файла ради 3 строк кода.

**Когда бы я переписал:** если бы к `match` добавили 12+ случаев, каждый с 20+ строк логики, и они стали бы меняться независимо.

---

### Финальный принцип

> **SOLID — это эвристика для среднего случая.**
> Средний случай — это код, который будет меняться, расширяться, переиспользоваться.
>
> Если вы в **среднем случае** — следуйте SOLID.
> Если вы в **маленьком, стабильном, одноразовом** модуле — SOLID добавляет стоимость без выгоды.
>
> Оправданное нарушение — это **осознанный отказ от абстракции**, а не лень.

Разница между ними: лень не задокументирована и не переосмысляется. Осознанное нарушение — это «я знаю, что нарушаю, и вот почему в данном контексте это правильный выбор».

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## OCP без наследования

OCP говорит: «открыт для расширения, закрыт для модификации». Наследование (`extends`) — самый частый, но не единственный способ. Вот что я использую на практике, от наиболее к наименее частому.

---

### 1. Композиция + интерфейс (Strategy)

Самый частый случай. Не `extends`, а `implements` + вставка в конструктор.

```php
// Контракт
interface TaxCalculatorInterface
{
    public function calculate(Money $base): Money;
}

// Конкретные реализации — без общего родителя
final class EuVatCalculator implements TaxCalculatorInterface
{
    public function calculate(Money $base): Money
    {
        return $base->multiply(0.20);
    }
}

final class UkVatCalculator implements TaxCalculatorInterface
{
    public function calculate(Money $base): Money
    {
        return $base->multiply(0.25);
    }
}

// Клиент — не знает, кто именно
final class InvoiceBuilder
{
    public function __construct(private TaxCalculatorInterface $tax) {}

    public function build(Money $base): Invoice
    {
        $tax = $this->tax->calculate($base);
        return new Invoice($base->add($tax));
    }
}
```

**Расширение** = новый класс + регистрация в DI. **Модификация** = ноль строк в `InvoiceBuilder`.

**Когда не работает:** если реализаций больше 5 и у них общая логика — интерфейс становится «широким», и хочется вынести shared-поведение в trait или base-класс. Тогда это уже не чистый OCP, а компромисс.

---

### 2. Реестр (Registry / lookup)

Поведение определяется **ключом**, а не типом. Наследование вообще не участвует.

```php
final class DiscountRegistry
{
    /** @var array<string, callable(Money): Money> */
    private array $discounts = [];

    public function register(string $code, callable(Money): Money $fn): void
    {
        $this->discounts[$code] = $fn;
    }

    public function apply(?string $code, Money $total): Money
    {
        $fn = $this->discounts[$code ?? ''] ?? null;
        return $fn !== null ? $fn($total) : $total;
    }
}

// Регистрация — в отдельном файле или в bootstrap
$registry = new DiscountRegistry();
$registry->register('VIP10', fn(Money $m) => $m->multiply(0.90));
$registry->register('WINTER', fn(Money $m) => $m->multiply(0.85));
$registry->register('BOGO',  fn(Money $m) => $m->multiply(0.75));
```

**Расширение** = одна строка `register()`. **Модификация** = ноль.

**Где видел в проде:** промокоды, способы оплаты, форматы экспорта, правила валидации.

**Минус:** нет статической типизации вызова. `apply('WINTER', $total)` — опечатка в коде не поймается компилятором. Частично спасает enum:

```php
enum PromoCode: string
{
    case Vip10  = 'VIP10';
    case Winter = 'WINTER';
}

$registry->apply(PromoCode::Vip10, $total);
```

---

### 3. События (Observer)

Код, который «что-то делает после X», не входит в X. Расширение = новый слушатель.

```php
// До: в OrderService
public function confirm(int $orderId): void
{
    // ... бизнес-логика ...
    $this->notifyWarehouse($order);
    $this->notifyCustomer($order);
    $this->updateAnalytics($order);
}

// После:
public function confirm(int $orderId): void
{
    // ... бизнес-логика ...
    $this->events->dispatch(new OrderConfirmedEvent($order));
}
```

```php
// Новые слушатели — отдельные классы, не трогая OrderService
final class NotifyWarehouseListener
{
    public function __invoke(OrderConfirmedEvent $e): void { /* ... */ }
}

final class SyncToErpListener
{
    public function __invoke(OrderConfirmedEvent $e): void { /* ... */ }
}
```

**Расширение** = новый listener + регистрация. **Модификация** = ноль.

**Где не работает:**
- Слушатели зависят от порядка → OCP ломается (добавили слушатель, он меняет поведение остальных).
- Слушатели мутируют состояние → трудно предсказать итоговый результат.
- Нужна синхронность и транзакционность → лучше композиция.

---

### 4. Конфигурация + интерпретатор

Поведение — **данные**, а не код. Расширение = новая строка в конфиге / БД.

```php
// config/rules.yaml
rules:
  - id: free_shipping
    when:
      total_gte: 100
      country: "DE"
    then:
      shipping_fee: 0

  - id: weekend_discount
    when:
      day_in_week: [6, 7]
    then:
      discount_pct: 5
```

```php
final class RuleEngine
{
    /** @param Rule[] $rules */
    public function __construct(private array $rules) {}

    public function apply(Order $order): Order
    {
        foreach ($this->rules as $rule) {
            if ($rule->matches($order)) {
                $order = $rule->applyTo($order);
            }
        }
        return $order;
    }
}
```

**Расширение** = новая YAML-строка. **Модификация** = ноль кода.

**Минус:** отладка сложнее, нет автодополнения, логика «размазана» по конфигам. Хорошо работает для **простых** правил (пороговые, пороговые, lookup-таблицы). Для сложной логики — нет.

---

### 5. First-class callable + композиция

Для простых случаев, когда «расширение» — это подменить функцию.

```php
final class ReportGenerator
{
    /** @var array<string, callable(Order): array> */
    private array $sections = [];

    public function addSection(string $id, callable(Order): array $fn): self
    {
        $this->sections[$id] = $fn;
        return $this;
    }

    public function generate(Order $order): array
    {
        $result = [];
        foreach ($this->sections as $id => $fn) {
            $result[$id] = $fn($order);
        }
        return $result;
    }
}

// Расширение:
$gen = (new ReportGenerator())
    ->addSection('summary',   fn(Order $o) => ['total' => $o->total])
    ->addSection('items',     fn(Order $o) => $o->items)
    ->addSection('tax_break', fn(Order $o) => ['vat' => $o->tax]);
```

Это по сути реестр (п.2), но с более «PHP-идиоматичным» синтаксисом.

---

### Сравнение

| Способ | Расширение | Типобезопасность | Отладка | Когда лучше всего |
|--------|-----------|------------------|---------|-------------------|
| Strategy (interface) | Новый класс + DI | ✅ полная | ✅ stack trace | 2–10 реализаций, общая сигнатура |
| Registry | Одна строка | ⚠️ ключ — строка | ⚠️ нет типа | Промокоды, способы оплаты, форматы |
| События | Новый listener | ✅ тип события | ⚠️ порядок, асинхронность | «После X сделай Y» |
| Конфиг + engine | YAML/БД | ❌ нет | ❌ медленно | Простые пороги, lookup-таблицы |
| Callable-реестр | Одна строка | ⚠️ closure | ⚠️ | Отчёты, пайплайны, утилиты |

---

### Когда OCP вообще не нужен

Важно: **не каждый «if/else» — нарушение OCP.**

```php
// 3 случая, стабильные, не растут → match. Tочка.
$label = match ($status) {
    OrderStatus::Pending   => 'В обработке',
    OrderStatus::Shipped   => 'Отправлен',
    OrderStatus::Delivered => 'Доставлен',
};
```

Выводить `match` на interface + 3 класса + DI-регистрацию — это **не** OCP, а переусложнение. OCP оправдан, когда:
- реализаций **много** (5+),
- они **меняются независимо**,
- есть **реальная** вторая/третья/четвёртая реализация, а не гипотетическая.

---

### Итог

OCP — это **свойствие системы**, а не **паттерн одного класса**. Реализуется через:
- замену зависимостей (Strategy),
- регистрацию новых элементов (Registry, Events),
- изменение данных (Config),
- вставку нового звена (Callable pipeline).

Наследование — один из способов, но не единственный и часто не лучший.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — про ответственность, а не про класс

Исходная формулировка (Uncle Bob): *«У класса должна быть только одна причина меняться»*. Но «класс» здесь — просто самый частый носитель ответственности. Принцип переносится на любой уровень, где есть **отдельная единица, которую можно менять независимо от других**.

---

### 1. Функция / метод

Самый распространённый уровень.

```php
// ❌ 3 причины меняться
public function sendDailyReport(): void
{
    // 1. Сбор данных
    $orders = $this->repo->findSince(yesterday());
    $revenue = array_sum(array_column($orders, 'total'));

    // 2. Форматирование
    $html = "<h1>Daily Report</h1>";
    $html .= "Orders: " . count($orders);
    $html .= "Revenue: " . number_format($revenue, 2) . " EUR";
    $html .= "<table>...</table>";

    // 3. Доставка
    $this->email->send($to, 'Daily Report', $html);
    $this->slack->postMessage($channel, $html);
}
```

Три причины меняться: изменили источник данных → менять. Изменили макет отчёта → менять. Добавили Telegram → менять.

```php
// ✅ Каждая — отдельная функция (или класс)
public function sendDailyReport(): void
{
    $data  = $this->reportCollector->collect();
    $html  = $this->reportRenderer->render($data);
    $this->reportDeliverer->deliver($html);
}
```

---

### 2. Модуль / файл

Один файл может содержать несколько ответственностей.

```
src/OrderController.php
├── Валидация входных данных
├── Построение команды (mapping request → command)
├── Вызов use case
├── Форматирование ответа
└── Обработка ошибок (catch → HTTP code)
```

Пять причин меняться в одном файле. Разделение:

```
src/OrderController.php       → только HTTP → Command → UseCase → Response
src/RequestMapper.php         → request → command (отдельный тест)
src/ErrorResponseFormatter.php → exception → HTTP response
```

---

### 3. Таблица / схема БД

Нормализация — это SRP для данных.

```sql
-- ❌ Одна таблица, 5 причин меняться
CREATE TABLE orders (
    id          INT,
    email       VARCHAR,
    phone       VARCHAR,    -- причина: меняется формат телефонов
    address     JSONB,      -- причина: меняется структура адреса
    total       INT,
    tax         INT,
    discount    INT,        -- причина: меняется логика скидок
    promo_code  VARCHAR,    -- причина: меняется система промокодов
    shipping    JSONB,      -- причина: меняется логистика
    status      VARCHAR,
    created_at  TIMESTAMP
);
```

```sql
-- ✅ Каждая — отдельная сущность со своей причиной меняться
orders        (id, email, status, created_at)
order_items   (order_id, product_id, qty, price)
order_totals  (order_id, total, tax, discount, currency)
order_address (order_id, street, city, zip, country)
order_shipping(order_id, carrier, tracking, eta)
promotions    (code, discount_type, value, valid_from, valid_to)
```

---

### 4. Микросервис / модуль

```
❌ order-service
├── Валидация
├── Расчёт цены
├── Оплата
├── Отправка на склад
├── Email
├── Аналитика

✅ order-service      → создание/изменение заказа
✅ pricing-service    → расчёт цены
✅ payment-service    → оплата
✅ inventory-service  → склад
✅ notification-svc   → email/sms
```

Каждый сервис — одна причина меняться (один бизнес-контекст).

---

### 5. Конфигурация

```yaml
# ❌ app.yml — одна причина? Нет.
app:
  name: shop
  env: production

db:
  host: localhost
  port: 5432
  pool: 20

redis:
  host: localhost
  port: 6379

stripe:
  key: sk_...
  webhook_secret: ...

email:
  from: noreply@shop.com
  provider: ses
  template_dir: /templates
```

Шесть «отделов» в одном файле. При смене провайдера email вы трогаете файл, в котором лежат ключи Stripe. Разделение: `config/database.yml`, `config/payment.yml`, `config/notification.yml`.

---

### 6. Переменная / параметр

```php
// ❌ Одна переменная, две обязанности
$config = [
    'host' => 'localhost',     // обязанность: где БД
    'pool' => 20,              // обязанность: сколько соединений
    'key'  => 'sk_live_...',   // обязанность: платёжный ключ (ДРУГОЙ контекст!)
];
```

Смешение конфигураций разных подсистем в одной структуре — нарушение SRP на уровне данных.

---

### 7. Команда CLI

```bash
# ❌ Одна команда, 4 обязанности
./deploy.sh
# 1. Сборка бинарника
# 2. Миграции БД
# 3. Деплой кода
# 4. Смена DNS

# ✅
./build.sh
./migrate.sh
./deploy.sh
./switch-dns.sh
```

---

### Что НЕ является нарушением SRP

| Ситуация | Почему ок |
|----------|-----------|
| Метод на 50 строк, но **одна** причина меняться | SRP ≠ «короткий метод» |
| Класс на 200 строк, одна обязанность | Размер — не критерий |
| `__construct` с 10 параметрами | Если все 10 — одна ответственность (создание), ок |
| Функция, которая делает «A, потом B, потом C» | Если B и C — **всегда** следуют за A и не меняются независимо — это одна ответственность |

---

### Единообразный критерий

Не «сколько строк» и не «сколько классов». Вопрос один:

> **Есть ли две причины, по которым можно было бы менять эту единицу независимо друг от друга?**

Если да → раздели. Если нет → оставь.

Это работает на любом уровне: функция, класс, модуль, таблица, сервис, конфиг, переменная.

> ⏱ 31.8s · prompt 8845 tok · output 4796 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт технический вопрос про DI-контейнер Symfony. Это продолжение собеседования на PHP-бэкенд. Нужно ответить на уровне, который ожидает senior/middle+ разработчик — не поверхностно, а с пониманием internals.

Структура ответа:
1. Общая схема: что такое контейнер, два состояния (compile-time и runtime).
2. Compile-time: что делает compiler passes, что генерируется (yaml/xml/php config + автогенерация).
3. Autowiring: как определяется тип, как резолвится сервис, какие правила.
4. Runtime: что происходит при `get()` — factory, lazy, singleton.
5. Практические детали: service locator, shared vs prototype, public vs private, autowire aliases.

Нужно показать понимание, а не просто пересказ документации. Привести примеры кода, показать "что под капотом". Упомянуть compiler passes, autowire, autowire aliases, service locator, shared services.

Формат: структурированный, с примерами, без воды. Как на собеседовании — уверенно, с конкретикой.

</details>

**Кандидат:**

## DI-контейнер Symfony: от конфигурации до рантайма

### Общая схема

Контейнер существует в **двух состояниях**:

```
┌─────────────────────────────────────────────────────────────────┐
│  COMPILE-TIME (build)                                           │
│                                                                 │
│  config/*.yaml ──┐                                              │
│  attributes      ├─→ Compiler Passes ─→ Generated Container    │
│  autowire rules ─┘          (PHP classes)                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  RUNTIME                                                        │
│                                                                 │
│  $container->get(MyService::class)                              │
│       │                                                         │
│       ▼                                                         │
│  Container::get() → factory closure → new MyService(...)       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

### 1. Compile-time: что происходит

#### Шаг 1: Сбор деклараций

Symfony собирает **все** источники деклараций сервисов:

```php
// 1. config/services.yaml
services:
    App\Service\OrderService:
        arguments:
            $logger: '@logger'

// 2. config/packages/routing.yaml → autoconfigure controllers
# (не сервисы напрямую, но влияет)

// 3. Attribute-анинотации
#[\Symfony\Component\DependencyInjection\Service]
final class PaymentProcessor
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
    ) {}
}

// 4. autoconfigure (Symfony сам регистрирует)
//    - все классы в src/ с constructor
//    - все implement'еры EventSubscriberInterface
//    - все implement'еры Command (console)
//    - все implement'еры Validator (Constraint)
```

#### Шаг 2: Compiler Passes

Это **оркестрация**. Каждый pass — PHP-класс, который может:
- добавлять сервисы,
- удалять сервисы,
- менять аргументы,
- создавать алиасы.

Ключевые pass'ы (в порядке выполнения):

| Pass | Что делает |
|------|-----------|
| `RegisterServiceSubscribersPass` | Находит `EventSubscriberInterface`, регистрирует слушателей |
| `AutowiredServiceDefinitionPass` | **Автоинжект**: смотрит конструктор, резолвит типы |
| `RegisterFqcnAliasesPass` | Создаёт alias `App\Service\Foo` → `App\Service\Foo.service_id` |
| `DecoratorServicePass` | Обработчики (logger decorator, etc.) |
| `RemoveUnusedDefinitionsPass` | **Удаляет** сервисы, которые никто не использует (private) |

#### Шаг 3: Autowiring — механика

```php
final class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $repo,      // ← резолвится по типу
        private PaymentGatewayInterface  $gateway,    // ← резолвится по типу
        private LoggerInterface          $logger,     // ← резолвится по типу
        private int                      $maxRetries, // ← НЕ резолвится (скаляр)
    ) {}
}
```

Алгоритм `AutowiredServiceDefinitionPass`:

```
Для каждого параметра конструктора:
  1. Есть ли явный аргумент в config? → использовать его.
  2. Иначе:
     2a. Параметр — скаляр (int, string, bool, float)?
         → Искать в $defaultAutowiring (config default: int: 0, string: '')
         → Если нет — ошибка: "Cannot autowire argument $maxRetries"
     
     2b. Параметр — interface / class?
         → Искать в контейнере сервис с этим FQCN
         → Если нашёл один → ок
         → Если нашёл несколько → ошибка: "Multiple services implement X"
         → Если не нашёл → ошибка: "Cannot autowire argument"
     
     2c. Параметр — iterable (array, Collection)?
         → Автособирать все сервисы этого типа (tagged locator)
```

**Ключевой момент:** autowiring работает **по FQCN**, а не по имени сервиса. `PaymentGatewayInterface` → ищет сервис, зарегистрированный как `App\Service\PaymentGatewayInterface` (alias) или с `class` = эта реализация.

#### Шаг 4: Генерация контейнера

В `var/cache/dev/ContainerXXXXX/` генерируется:

```php
// ContainerXXXXX.php (упрощённо)
class Container extends ContainerBase
{
    public function get(App\Service\OrderService $id = null): OrderService
    {
        return $this->services['app.service.order_service'] ??= $this->createApp_Service_OrderServiceService();
    }

    protected function createApp_Service_OrderServiceService(): OrderService
    {
        $this->privates['app.service.order_service'] = $instance = new OrderService(
            $this->get(2) /* OrderRepositoryInterface */,
            $this->get(3) /* PaymentGatewayInterface */,
            $this->get(4) /* LoggerInterface */,
            3 /* $maxRetries — скаляр, захардкожен */
        );
        return $instance;
    }
}
```

**Важно:** скаляры (`$maxRetries = 3`) **захардкожены** в сгенерированный код. Нет никакого `config->get('app.max_retries')` в рантайме.

---

### 2. Runtime: запрос первого сервиса

```php
// Controller
public function __construct(private OrderService $orderService) {}
//                          ^^^^^^^^^^^^^^^^^^^^^
// При компиляции: Symfony видит, что это controller,
// регистрирует его в контейнере, автоинжект OrderService.
```

Мгновенное обращение:

```
Request → Kernel::handle()
  → Router → Controller
    → $container->get('App\Controller\OrderController')
      → Container::get()
        → if isset($this->privates['...']) → return cached
        → else → $this->createApp_Controller_OrderControllerService()
          → new OrderController(
                $this->get('App\Service\OrderService')  ← рекурсивный get
              )
```

#### Что происходит при `get()` впервые

```php
// Упрощённо: Container::get()
public function get(string $id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object
{
    // 1. Проверяем кэш
    if (isset($this->privates[$id])) {
        return $this->privates[$id];
    }

    // 2. Проверяем public-сервисы
    if (isset($this->services[$id])) {
        return $this->services[$id];
    }

    // 3. Ищем factory closure
    $factory = $this->factories[$id] ?? null;
    if ($factory === null) {
        throw new ServiceException("Service \"$id\" not found");
    }

    // 4. Вызываем factory
    $instance = $factory($this);
    $this->privates[$id] = $instance;

    return $instance;
}
```

**Shared (singleton) по умолчанию.** `private` сервисы кэшируются в `$this->privates` после первого создания. Повторный `get()` — O(1), обращение к массиву.

---

### 3. Ключевые механики, которые часто спрашивают

#### Shared vs Prototype

```yaml
services:
    App\Service\OrderService:
        shared: true          # ← по умолчанию. Один экземпляр на контейнер.

    App\Command\GenerateReportCommand:
        shared: false         # Новый экземпляр при каждом get().
                              # Важно для console-команд: каждая инстанция
                              # обрабатывает свой вызов, не мутирует чужое состояние.
```

#### Public vs Private

```yaml
services:
    # private (по умолчанию) — доступен ТОЛЬКО через autowiring/инжект
    App\Service\OrderService:
        public: false         # $container->get(OrderService::class) → ServiceException

    # public — доступен через ServiceLocator / $container->get()
    App\Kernel:
        public: true
```

**Важно:** «private» не значит «не существует в контейнере». Он существует, кэшируется, но **недоступен по имени** извне.

#### ServiceLocator

```php
final class MyService
{
    public function __construct(
        #[ServiceLocator]
        private ServiceLocator $locator,
    ) {}

    public function handle(string $type): void
    {
        // Ленивое: сервис создаётся только при вызове get()
        $handler = $this->locator->get($type);
    }
}
```

Под капотом `ServiceLocator` — **отдельный** mini-контейнер, который содержит **завёрнутые** фабрики (не сами сервисы). Это позволяет:
- не создавать все сервисы сразу,
- не нарушать SRP (один класс не знает про 20 зависимостей).

#### Autowire aliases

```php
// config/services.yaml
services:
    App\Service\PaymentGatewayInterface:
        alias: App\Service\StripeGateway

# Теперь:
class OrderService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,  // ← StripeGateway
    ) {}
}
```

Без alias — `Cannot autowire argument $gateway: no services found for interface`.

#### Decorator

```yaml
services:
    App\Service\LoggerInterface:
        class: App\Service\ConsoleLogger

    App\Service\LoggingLogger:
        decorates: logger
    decorator:
        App\Service\LoggingLogger:
            decorator: logger
```

Под капотом: `decorator` перехватывает **фабрику** декорированного сервиса и оборачивает её. Вызывающая сторона не знает, что логгер «обёрнут».

---

### 4. Частые грабли

| Грабли | Причина | Решение |
|--------|---------|---------|
| `Cannot autowire argument $x` | Скаляр без значения, или interface без реализации | Добавить `default: int: 0` или зарегистрировать реализацию |
| `Multiple services implement X` | Два сервиса одного типа | `alias` или `#[Autowire('service_id')]` |
| Циклическая зависимость A→B→A | `A` inject'ит `B`, `B` inject'ит `A` | Вынести общее в третий сервис, или event |
| `private` сервис не доступен из `ServiceLocator` | `ServiceLocator` видит только **public** или **явно перечисленные** | Добавить в `locators` config |
| Изменение конфига не отражается | Кэш контейнера в `var/cache/` | `rm -rf var/cache/` или `bin/console cache:clear` |

---

### 5. Что я смотрю, когда читаю чужой `services.yaml`

1. **Есть ли `public: true` у не-контроллеров?** → скорее всего, кто-то дёргает через `$container->get()` — это нарушение.
2. **Есть ли `@!` (null-override)?** → кто-то «выключает» автоинжект. Почему?
3. **Есть ли циклы через `@!`?** → часто признак того, что нужно рефакторить на event.
4. **Сколько `shared: false`?** → каждый такой сервис создаётся заново. Обоснованно для command'ов, подозрительно для сервисов.
5. **Есть ли `@service_locator` с 20+ сервисами?** → ServiceLocator превратился в «мешок». Лучше разбить.

---

Если хочешь, могу углубиться в конкретный аспект: compiler passes, `#[AsCommand]`-автоинжеккт, или как Symfony 7 меняет генерацию контейнера.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Циклические зависимости: от симптома к решению

### Сначала — диагноз

Цикл A → B → A **почти всегда** означает, что у вас один класс делает две вещи. Это не «техническая проблема DI», а **нарушение SRP**, замаскированное под граф зависимостей.

```
❌ OrderService → PaymentService → OrderService
```

Вопрос: *что они делают друг для друга?*

| A вызывает B | B вызывает A | Что это на самом деле |
|---|---|---|
| «Оплати заказ» | «Обнови статус заказа после оплаты» | B зависит от A для **побочного эффекта** |
| «Добавь в корзину» | «Валидируй позицию корзины» | B зависит от A для **логики**, которая не его |
| «Отправь событие» | «Слушай это событие» | Наблюдательство через прямой вызов |

Каждый случай — свой приём.

---

### 1. Вынести общее в третий сервис (Mediator)

Самый частый и самый правильный.

```php
// ❌ Цикл
class OrderService
{
    public function __construct(
        private PaymentService $payment,  // A → B
    ) {}
}

class PaymentService
{
    public function __construct(
        private OrderService $order,      // B → A
    ) {}

    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);
        $this->order->markAsPaid($order);  // ← B вызывает A
        return $result;
    }
}
```

```php
// ✅ Третий сервис
class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,  // ← только gateway, не OrderService
    ) {}

    public function charge(Order $order): PaymentResult
    {
        return $this->gateway->charge($order);
    }
}

class OrderService
{
    public function __construct(
        private PaymentService $payment,
    ) {}

    public function placeAndPay(Order $order): void
    {
        $result = $this->payment->charge($order);  // A → B (один方向)
        $this->markAsPaid($order, $result);         // A делает сам
    }
}
```

Граф стал **DAG** (directed acyclic). Цикла нет.

---

### 2. События (Observer)

Когда B должен «знать, что A что-то сделал», но не обязан быть в вызове.

```php
// ❌ Цикл через прямой вызов
class OrderService
{
    public function placeOrder(Order $order): void
    {
        // ...
        $this->payment->charge($order);
        // payment вызывает back to order → ЦИКЛ
    }
}

class PaymentService
{
    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);
        $this->orderService->markAsPaid($order);  // ← B → A
        return $result;
    }
}
```

```php
// ✅ Событие
class OrderService
{
    public function __construct(
        private PaymentService $payment,
        private EventDispatcher $events,
    ) {}

    public function placeAndPay(Order $order): void
    {
        $result = $this->payment->charge($order);
        $this->events->dispatch(new OrderPaidEvent($order, $result));
    }
}

class PaymentService
{
    // Зависимостей на OrderService НЕТ
    public function charge(Order $order): PaymentResult
    {
        return $this->gateway->charge($order);
    }
}

// Отдельный listener — новый файл, не трогает ни OrderService, ни PaymentService
final class MarkOrderPaidListener
{
    public function __construct(private OrderRepository $repo) {}

    public function __invoke(OrderPaidEvent $event): void
    {
        $event->order->markAsPaid();
        $this->repo->save($event->order);
    }
}
```

Граф: `OrderService → PaymentService` и `OrderService → EventDispatcher`. Цикла нет.

**Когда работает:** B-логика — это **побочный эффект**, а не часть основного результата. Если B обязан вернуть что-то A — событие не подходит (асинхронное, не вернёт значение).

---

### 3. Интерфейс + реверс контроля (DIP)

Когда A и B реально «зависят друг от друга», но на самом деле зависят от **контракта**.

```php
// ❌ Конкретные классы в цикле
class OrderService { public function __construct(private PaymentService $p) {} }
class PaymentService { public function __construct(private OrderService $o) {} }
```

```php
// ✅ Зависимости на интерфейсы
interface OrderNotifierInterface
{
    public function notifyPaid(Order $order, PaymentResult $result): void;
}

class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
        private OrderNotifierInterface $notifier,  // ← интерфейс, не OrderService
    ) {}

    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);
        $this->notifier->notifyPaid($order, $result);
        return $result;
    }
}

class OrderService implements OrderNotifierInterface
{
    public function __construct(private PaymentService $payment) {}

    public function notifyPaid(Order $order, PaymentResult $result): void
    {
        $order->markAsPaid();
        // ...
    }
}
```

Технически цикл в графе DI **всё ещё есть** (Symfony будет ругаться), но логически зависимости **асимметричны**: PaymentService не знает, кто именно `notifier`.

Чтобы Symfony не ругался — см. п. 5.

---

### 4. ServiceLocator — «ленивый» доступ

Когда B **иногда** нужен A, но не при каждом вызове.

```php
// ❌ Аргумент в конструкторе → цикл
class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
        private OrderService $order,  // ← цикл
    ) {}
}
```

```php
// ✅ Ленивое получение
class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
        private ServiceLocator $locator,  // ← не конкретный сервис
    ) {}

    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);

        // Только если нужно
        if ($result->isSuccessful()) {
            $orderService = $this->locator->get(OrderService::class);
            $orderService->markAsPaid($order);
        }

        return $result;
    }
}
```

**Symfony:**
```yaml
services:
    App\Service\PaymentService:
        arguments:
            $locator:
                service_locator:
                    - App\Service\OrderService
```

`ServiceLocator` **не создаёт** сервисы заранее. `get()` — ленивый. Цикла в графе нет: `PaymentService → ServiceLocator → (лениво) → OrderService`.

**Когда работает:** B нужен в 1 из 10 сценариев. B не нужен для типичного вызова.

**Когда не работает:** B нужен всегда и сразу. Тогда ServiceLocator — просто «проброс», который скрывает проблему.

---

### 5. `@!` (null reference) + `set()` — грубая сила

Symfony позволяет вставить `null` в один из циклических аргументов, а потом заполнить через setter.

```php
class OrderService
{
    private ?PaymentService $payment = null;

    public function __construct(
        private EventDispatcher $events,
    ) {}

    // Symfony вставит null из-за @!
    public function setPayment(PaymentService $payment): void
    {
        $this->payment = $payment;
    }

    public function placeAndPay(Order $order): void
    {
        $result = $this->payment->charge($order);
        // ...
    }
}

class PaymentService
{
    public function __construct(
        private OrderService $order,  // ← OrderService ещё не создан, но это ок
    ) {}

    public function charge(Order $order): PaymentResult
    {
        $result = $this->gateway->charge($order);
        $this->order->markAsPaid($order);
        return $result;
    }
}
```

```yaml
services:
    App\Service\OrderService:
        calls:
            - [setPayment, ['@App\Service\PaymentService']]

    App\Service\PaymentService:
        arguments:
            $order: '@App\Service\OrderService'
```

**Когда использовать:**
- Миграция: вы видите цикл, но не готовы рефакторить сейчас.
- Библиотека: вы не можете менять конструктор.

**Когда НЕ использовать:**
- Это не решение. Это «я знаю, что тут плохо, но пока так».
- Через 3 месяца цикл будет в 5 местах, и никто не вспомнит, почему тут `@!`.

---

### 6. Lazy proxy (Symfony 6.2+)

```yaml
services:
    App\Service\PaymentService:
        lazy: true  # ← создаётся proxy, реальный объект — при первом вызове
```

`lazy: true` **не решает цикл**. Он решает **производительность** (не создавать тяжёлый сервис, если он не вызывается). Если есть цикл — `lazy` не поможет, потому что для создания proxy всё равно нужно резолвить аргументы.

---

### Сравнительная таблица

| Приём | Когда применять | Минусы |
|-------|---------------|--------|
| Вынести в 3-й сервис | Б-логика — часть основного потока | Нужно понять, где граница ответственности |
| Событие | Б-логика — побочный эффект | Асинхронность, трудно отлаживать, порядок |
| Интерфейс (DIP) | A и B зависят от контрактов | Не убирает цикл в графе, требует `@!` или `locator` |
| ServiceLocator | B нужен редко / условно | Скрывает зависимости, IDE не подсказывает |
| `@!` + setter | Миграция, внешний код | Не решение, временная мера |
| Lazy proxy | Тяжёлый сервис, не вызывается всегда | Не решает цикл |

---

### Мой порядок действий

```
1. Цикл найден → задать вопрос: «что B делает, чего A не может сделать сам?»

2. Если ответ: «B просто обновляет состояние A»
   → Событие (Observer). Самый частый случай.

3. Если ответ: «B вычисляет что-то, что A использует в результате»
   → Вынести в 3-й сервис (Mediator).

4. Если ответ: «B и A по определению связаны, и это не исправить»
   → Интерфейс (DIP) + ServiceLocator или @!.
   + комментарий: // ЦИКЛ: обоснование. TODO: рефакторить при X.

5. Если ответ: «Я не понимаю, почему они зависимы»
   → Стоп. Перечитать код. Вероятно, один класс делает 2 вещи.
```

---

### Чего НЕ делать

| ❌ | Почему |
|----|--------|
| `try { new A(new B(new A())); } catch (...)` | Обфускация, а не решение |
| Статическое хранилище (`static::$instance`) | Глобальное состояние, невозможно тестировать |
| `@!` без комментария и TODO | Завтра никто не поймёт, почему тут null |
| ServiceLocator «на всякий случай» в каждом сервисе | Теряется видимость зависимостей, DI становится декорацией |

> ⏱ 25.5s · prompt 18911 tok · output 3033 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Ситуация: после регистрации пользователя нужно:
1. Отправить письмо (уведомление о регистрации)
2. Обновить поисковый индекс (Elasticsearch/Meilisearch/Algolia)

Ограничение: не должно замедлять ответ пользователю.

Нужно рассмотреть варианты:
1. Синхронные вызовы (baseline, антипаттерн)
2. Symfony Messenger (sync transport)
3. Symfony Messenger (async transport: RabbitMQ/SQS/Redis)
4. Event listeners + Messenger (классический Symfony-подход)
5. Fire-and-forget через queue в фоне (worker)
6. HTTP callback / webhook (внешний сервис)
7. Materialized view / CDC (Change Data Capture) для индекса
8. Batch processing

Нужно описать:
- Архитектурную схему
- Код
- Trade-offs каждого варианта
- Что выбрать в каком случае

Структурирую так:
1. Формулировка проблемы (почему нельзя синхронно)
2. Вариант 1: синхронно (baseline, почему нет)
3. Вариант 2: Event + Messenger (основной рекомендуемый)
4. Вариант 3: Messenger с retry/DLQ
5. Вариант 4: внешний queue (RabbitMQ/SQS)
6. Вариант 5: CDC для индекса (отдельный трек)
7. Сравнительная таблица
8. Что я бы выбрал

Важно показать реальный код, не абстракции. Symfony-specific.

</details>

**Кандидат:**

## Асинхронные побочные эффекты после регистрации

### Постановка

```
POST /register
  → создать пользователя в БД
  → отправить подтверждение письма
  → обновить поисковый индекс
  → вернуть 201 Created
```

Требование: `201` возвращается **до** того, как письмо ушло и индекс обновился. Но если письмо не отправится — это **бизнес-инцидент**, а не «потерянный лог».

---

### Вариант 0: Синхронно (антипаттерн, но показываю для контраста)

```php
final class RegisterController
{
    public function __invoke(
        Request $request,
        EmailService $email,
        SearchIndexer $search,
    ): Response {
        $user = $this->registerUser($request);  // БД, ~5ms

        $email->sendConfirmation($user);        // SMTP, ~200–800ms
        $search->indexUser($user);              // HTTP → Meilisearch, ~50–200ms

        return new Response('', 201);
        // Пользователь ждёт 300–1000ms из-за побочного эффекта
    }
}
```

**Проблемы:**
- Ответ зависит от внешнего SMTP и Meilisearch.
- Если Meilisearch упал — регистрация тоже упала (500).
- Нет retry: пользователь не получил письмо, перерегистрировался, дубль.
- Нет наблюдения: не видно, сколько писем не ушло.

---

### Вариант 1: Symfony Messenger + события (основной подход)

#### Схема

```
RegisterController
  → UserRegisteredEvent (domain event)
    → MessageBus (in-memory, sync dispatch)
      → SendConfirmationEmailMessage  → transport: async_email
      → IndexUserMessage              → transport: async_search

[Worker 1] consumes async_email → EmailService → SMTP
[Worker 2] consumes async_search → SearchIndexer → Meilisearch
```

Ключевое: **dispatch событий — синхронный и быстрый** (microseconds). **Обработка сообщений — асинхронная** (отдельные процессы).

#### Код

**1. Domain event**

```php
final readonly class UserRegisteredEvent
{
    public function __construct(
        public int    $userId,
        public string $email,
        public string $firstName,
    ) {}
}
```

**2. Команды (messages)**

```php
final readonly class SendConfirmationEmail
{
    public function __construct(
        public int    $userId,
        public string $email,
        public string $firstName,
    ) {}
}

final readonly class IndexUserInSearch
{
    public function __construct(
        public int   $userId,
        public string $email,
        public string $firstName,
    ) {}
}
```

**3. Controller (или use case) — быстрая часть**

```php
final class RegisterController
{
    public function __construct(
        private UserRepository $repo,
        private MessageBus     $bus,
    ) {}

    public function __invoke(Request $request): Response
    {
        $data = $this->validate($request);

        // 1. БД — синхронно, обязательно
        $user = $this->repo->create(
            email:     $data['email'],
            firstName: $data['first_name'],
            password:  $this->hasher->hash($data['password']),
        );

        // 2. Domain event — синхронный dispatch, но слушатели
        //    только КЛАДИТ сообщения в queue. Не шлют письма.
        $this->bus->dispatch(new UserRegisteredEvent(
            userId:    $user->id,
            email:     $user->email,
            firstName: $user->firstName,
        ));

        return new Response('', 201);
        // ← Вернулось за ~10ms. Письмо ещё не отправлено.
    }
}
```

**4. Слушатели — кладут сообщения в асинхронные очереди**

```php
#[AsMessageHandler(
    buses: ['messenger.bus.default'],
    fromTransport: 'messenger.transport.default',
)]
final class UserRegisteredListener
{
    public function __construct(
        private MessageBus $bus,
    ) {}

    public function __invoke(UserRegisteredEvent $event): void
    {
        // Эти dispatch'и идут в АСИНХРОННЫЕ транспорты
        $this->bus->dispatch(new SendConfirmationEmail(
            $event->userId,
            $event->email,
            $event->firstName,
        ));

        $this->bus->dispatch(new IndexUserInSearch(
            $event->userId,
            $event->email,
            $event->firstName,
        ));
    }
}
```

**5. Асинхронные обработчики — работают в воркерах**

```php
#[AsMessageHandler(
    fromTransport: 'messenger.transport.email',
)]
final class SendConfirmationEmailHandler
{
    public function __construct(
        private EmailService $email,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SendConfirmationEmail $message): void
    {
        try {
            $this->email->sendConfirmation(
                to: $message->email,
                firstName: $message->firstName,
            );
        } catch (\Throwable $e) {
            $this->logger->error('Email send failed', [
                'user_id'  => $message->userId,
                'error'    => $e->getMessage(),
            ]);
            throw $e;  // ← Messenger сделает retry
        }
    }
}

#[AsMessageHandler(
    fromTransport: 'messenger.transport.search',
)]
final class IndexUserInSearchHandler
{
    public function __construct(
        private SearchIndexer $search,
    ) {}

    public function __invoke(IndexUserInSearch $message): void
    {
        try {
            $this->search->indexUser($message->userId);
        } catch (\Throwable $e) {
            throw $e;  // retry
        }
    }
}
```

**6. Конфигурация**

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        buses:
            messenger.bus.default:
                middleware:
                    - Symfony\Component\Messenger\Middleware\HandleMessageMiddleware
                    - Symfony\Component\Messenger\Middleware\SendMessageMiddleware
                    - Symfony\Component\Messenger\Middleware\SendFailedMessageForRetryTagMiddleware

        transports:
            messenger.transport.default:
                dsn: '%env(MESSENGER_DSN)%'
                # dev: sync://default
                # prod: amqp://localhost/messages

            messenger.transport.email:
                dsn: 'amqp://localhost/emails'
                options:
                    queue_name: emails
                    retry_strategy:
                        max_retries: 3
                        delay: 5000
                        multiplier: 2

            messenger.transport.search:
                dsn: 'amqp://localhost/search'
                options:
                    queue_name: search
                    retry_strategy:
                        max_retries: 5
                        delay: 1000
                        multiplier: 1.5

        routing:
            'App\Message\SendConfirmationEmail': messenger.transport.email
            'App\Message\IndexUserInSearch':   messenger.transport.search
```

**7. Воркеры**

```bash
# Procfile / Docker / systemd
php bin/console messenger:consume emails  --limit=1000 --time-limit=60
php bin/console messenger:consume search  --limit=1000 --time-limit=60
```

---

### Вариант 2: Direct dispatch без events (упрощённый)

Если не нужен domain event и слушатели:

```php
final class RegisterController
{
    public function __construct(
        private UserRepository $repo,
        private MessageBus $bus,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $this->repo->create(...);

        // Прямо в асинхронные транспорты
        $this->bus->dispatch(new SendConfirmationEmail(...));
        $this->bus->dispatch(new IndexUserInSearch(...));

        return new Response('', 201);
    }
}
```

**Trade-off:** проще, но нет separation между «доменное событие произошло» и «надо сделать побочные эффекты». При добавлении 3-го побочного эффекта — controller разрастается.

---

### Вариант 3: Внешняя очередь (RabbitMQ / SQS / Redis Stream)

Когда Symfony Messenger недостаточен:

| Ситуация | Почему Messenger не хватает |
|----------|---------------------------|
| Несколько микросервисов потребляют одно и то же событие | Messenger — per-application |
| Нужен dead-letter queue на уровне брокера | Встроенный retry ограничен |
| Потребители на других языках (Go, Python) | Messenger — только PHP |
| Высокий throughput (10k+ msg/s) | Встроенный transport не масштабируется горизонтально без внешнего брокера |

```yaml
# messenger.yaml — тот же config, но DSN:
transports:
    messenger.transport.email:
        dsn: 'amqp://guest:guest@rabbitmq:5672/%2f/emails'
```

Альтернатива: **полностью отказаться от Messenger** и писать в RabbitMQ напрямую через `php-amqplib`. Это даёт больше контроля (prefetch, ack/nack, manual requeue), но теряется интеграция с Symfony (middleware, tracing, monitoring).

---

### Вариант 4: CDC (Change Data Capture) для поискового индекса

Если индекс — **всегда** отражение БД, а не «побочный эффект»:

```
PostgreSQL → pgoutput (WAL) → Debezium / Waterline → Meilisearch
```

**Плюсы:**
- Индекс **всегда** консистентен с БД, независимо от приложения.
- Нет сообщения в очереди, нет handler'а, нет retry.
- Работает при любой записи (не только при регистрации).
- Приложение **не знает** про поиск.

**Минусы:**
- Инфраструктурная сложность (Debezium, Kafka, consumer).
- Задержка ~100ms–2s (зависит от WAL и consumer).
- Не подходит для «умных» индексаций (поиск по полнотекстовому описанию, embed'ы).

**Когда:** индекс = проекция БД. **Когда не:** индекс = вычисление (embeddings, NLP, агрегаты).

---

### Вариант 5: Fire-and-forget через `queue` в CLI

Минимальный подход, без брокера:

```php
// В controller
$this->bus->dispatch(new SendConfirmationEmail(...), new SendLaterStamp());

// Transport: doctrine (таблица messages в БД)
// Worker: php bin/console messenger:consume async
```

Symfony поддерживает `doctrine://default` как транспорт — сообщения хранятся в таблице БД. Воркер забирает и обрабатывает.

**Плюсы:** нет внешнего брокера, работает в dev из коробки.
**Минусы:** БД — не очередь. При 10k msg/s — проблемы. Нет горизонтального масштабирования без additional infra.

---

### Сравнительная таблица

| Критерий | В.1 Messenger+Event | В.2 Direct | В.3 External MQ | В.4 CDC | В.5 Doctrine transport |
|----------|---------------------|-----------|-----------------|---------|----------------------|
| Сложность | Средняя | Низкая | Высокая | Высокая | Низкая |
| Retry / DLQ | Встроенный | Встроенный | Брокер | Автоматически | Встроенный |
| Наблюдаемость | Symfony profiler + logging | То же | Брокер-метрики | Consumer metrics | БД-запросы |
| Горизонт. масштабирование | Воркеры ×N | Воркеры ×N | Брокер ×N | Consumer ×N | Воркеры ×N (конкуренция в БД) |
| Консистентность |Eventually | Eventually | Eventually | Eventually | Eventually |
| Инфраструктура | 1 брокер (опц.) | 0 | Брокер | Kafka + Debezium | 0 |
| Когда | **Дефолт** | Прототип | Микросервисы | Индекс = проекция | Dev / small prod |

---

### Что я бы выбрал

| Контекст | Выбор |
|----------|-------|
| 1 сервис, 1 язык, до 1k req/min | **Вариант 1** (Messenger + event + AMQP) |
| Прототип / внутренний инструмент | **Вариант 5** (Doctrine transport) |
| 3+ микросервиса, разные языки | **Вариант 3** (RabbitMQ/SQS) |
| Поисковый индекс — чистая проекция | **Вариант 4** (CDC) + Вариант 1 для писем |
| Письмо + индекс, оба — «умные» | **Вариант 1** (оба через Messenger) |

---

### Наблюдаемость (часто упускают)

Независимо от варианта, нужно:

```php
// 1. Logging в handler
$this->logger->info('Email sent', ['user_id' => $message->userId, 'duration' => $ms]);
$this->logger->error('Email failed', ['user_id' => $message->userId, 'attempts' => $attempts]);

// 2. Метрики (Prometheus / Datadog)
//    - email_sent_total
//    - email_failed_total
//    - email_processing_duration
//    - search_indexed_total
//    - search_index_failed_total

// 3. Dashboard: "сколько писем в очереди", "сколько в DLQ", "лаг потребления"

// 4. Алерт: DLQ email > 0 в течение 5 минут → page
```

Без этого асинхронность превращается в «пропавшие письма, о которых никто не знает».

---

### Частые ошибки в реализации

| Ошибка | Последствие |
|--------|-------------|
| Бросать исключение в handler без `previous` | Потеря контекста, трудно отлаживать |
| `catch (\Exception)` в handler + `log` + `return` | Сообщение «успешно обработано», retry не сработает |
| Один транспорт для email и search | Downgrade одного влияет на другое |
| Нет `--limit` у воркера | Утечка памяти при долгих обработках |
| Нет `time-limit` | Воркер живёт вечно, не подхватывает новый код |
| `retry_strategy` с `delay: 0` | Hammering упавшего сервиса |
| Нет DLQ-обработки | Сообщения теряются навсегда |

> ⏱ 30.3s · prompt 24591 tok · output 3824 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о системе контроля доступа в Symfony: аутентификация, роли, voters. И практический вопрос: когда voter, когда достаточно ролей.

Нужно:
1. Описать механику: Firewall, Authenticator, Token, Voter — как это связано.
2. Показать код, но не абстрактный — с конкретными примерами.
3. Ответить на практический вопрос (voter vs роли) — это самое ценное.
4. Не повторяться с предыдущими ответами, но связать с тем, что уже было (SRP, чистая архитектура).

Структура:
- Как устроен поток запроса (firewall → authenticator → token → authorization)
- Authenticators (JWT, form_login, custom)
- Роли: где хранятся, как проверяются
- Voters: механика, код
- Критерий: когда voter, когда роль — это главный вопрос
- Антипаттерны

Ключевой практический ответ: роли — это «кто ты», voters — это «что ты можешь сделать с этим конкретным объектом». Если решение зависит только от роли → роль. Если зависит от данных объекта (владеет ли, статус, контекст) → voter.

Хочу показать:
1. Полный поток от запроса до разрешения.
2. Пример voter'а с реальным кодом.
3. Чёткий критерий выбора.
4. Антипаттерны (voter на каждый endpoint, роли как флаги).

</details>

**Кандидат:**

## Контроль доступа в Symfony: от запроса до разрешения

### Поток: что происходит при каждом запросе

```
HTTP Request
    │
    ▼
┌─────────────────────────────────────────────────────┐
│  Firewall                                            │
│                                                      │
│  1. Stateless / Stateful?                            │
│  2. Аутентификация:                                   │
│     ├── JWT (cookie/header) → Token                   │
│     ├── Form login → session → Token                  │
│     └── API key → Token                               │
│  3. Если Token не создан → anonymous (или 401)       │
│                                                      │
│  4. Авторизация (на каждый endpoint):                 │
│     ├── access_control (routing)                      │
│     ├── attributes на контроллере                     │
│     └── Voter (в service layer)                       │
│                                                      │
└─────────────────────────────────────────────────────┘
    │
    ▼
Controller / Service
```

Ключевой момент: **аутентификация** и **авторизация** — разные этапы. Аутентификация говорит *кто ты*. Авторизация говорит *что тебе можно*.

---

### 1. Аутентификация: Authenticator

#### JWT (API-сервис)

```php
// config/packages/security.yaml
security:
    providers:
        user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        api:
            stateless: true
            provider: user_provider
            jwt: ~
            entry_point: App\Security\JwtAuthenticationEntryPoint

        main:
            lazy: true
            provider: user_provider
            form_login:
                login_path: app_login
                check_path: app_login
            logout:
                path: app_logout
```

```php
// App\Security\JwtAuthenticationEntryPoint.php
final class JwtAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __invoke(AuthenticationException $exception): Response
    {
        return new JsonResponse(['error' => 'unauthenticated'], 401);
    }
}
```

#### Что создаёт Token

```php
// Внутренне Symfony создаёт:
final class InMemoryToken extends SelfValidatingPassportToken
{
    private User $user;
    private array $roles;
    private array $attributes;  // ← доп. данные (IP, device, etc.)

    public function __construct(User $user, array $roles = ['ROLE_USER'])
    {
        $this->user = $user;
        $this->roles = $roles;
    }
}
```

`Token` — это **факт аутентификации**. Он живёт в `Security::getToken()`. Всё, что дальше — авторизация.

---

### 2. Авторизация: три уровня

#### Уровень 1: `access_control` (routing)

```yaml
# config/packages/security.yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/api/v1/.*, roles: ROLE_USER }
        - { path: ^/register, roles: PUBLIC_ACCESS }
```

**Что проверяет:** роль пользователя из Token.
**Где применяется:** до контроллера, на уровне маршрута.
**Когда достаточно:** «эта страница — только для админов».

```php
// Результат:
// GET /admin/dashboard → ROLE_ADMIN в Token? → 200 или 403
// GET /api/v1/orders  → ROLE_USER в Token?   → 200 или 401/403
```

---

#### Уровень 2: Атрибуты на контроллере

```php
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/users', name: 'admin_users')]
final class AdminUsersController
{
    public function __invoke(): Response
    {
        // Только если ROLE_ADMIN
    }
}

#[IsGranted('EDIT', 'post')]
#[Route('/blog/{id}', methods: ['PUT'])]
final class BlogController
{
    public function update(
        Post $post,                          // ← объект передаётся voter'у
        Request $request,
    ): Response {
        // Только если voter разрешает
    }
}
```

**Что проверяет:** `IsGranted` делегирует `AuthorizationChecker`.
**Где применяется:** на уровне контроллера, до `invoke()`.
**Когда достаточно:** «только автор может редактировать» + «только админ».

---

#### Уровень 3: Voter (service layer)

```php
// App\Security\PostVoter.php
final class PostVoter extends Voter
{
    public const VIEW  = 'POST_VIEW';
    public const EDIT  = 'POST_EDIT';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW   => $this->canView($user, $subject),
            self::EDIT   => $this->canEdit($user, $subject),
            self::DELETE => $this->canDelete($user, $subject),
        };
    }

    private function canView(User $user, Post $post): bool
    {
        if ($post->isPublished() || $post->isDraft()) {
            return true;  // публичный контент — видят все
        }

        return $user->id === $post->authorId
            || $this->isInModerationTeam($user, $post);
    }

    private function canEdit(User $user, Post $post): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->id !== $post->authorId) {
            return false;
        }

        // Автор может редактировать только свои черновики
        return $post->status === PostStatus::Draft
            && $this->isWithinEditWindow($post);
    }

    private function canDelete(User $user, Post $post): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->id !== $post->authorId) {
            return false;
        }

        // Автор может удалить только после 30 дней
        return $post->createdAt < (new \DateTimeImmutable('-30 days'));
    }

    private function isWithinEditWindow(Post $post): bool
    {
        return $post->updatedAt > (new \DateTimeImmutable('-7 days'));
    }

    private function isInModerationTeam(User $user, Post $post): bool
    {
        return $user->hasRole('ROLE_MODERATOR')
            && $post->category === 'news';
    }
}
```

**Использование:**

```php
// В контроллере
#[IsGranted(PostVoter::EDIT, 'post')]
public function update(Post $post): Response { ... }

// В service layer (более важно!)
final class PostService
{
    public function __construct(
        private PostRepository $repo,
        private AuthorizationChecker $auth,
    ) {}

    public function update(int $id, PostUpdateDto $dto): Post
    {
        $post = $this->repo->find($id);

        if (!$this->auth->isGranted(PostVoter::EDIT, $post)) {
            throw new AccessDeniedException('Cannot edit this post');
        }

        $post->apply($dto);
        $this->repo->save($post);

        return $post;
    }
}
```

**Ключевое:** voter работает **в service layer**, а не только в контроллере. Это защищает бизнес-логику от прямого вызова (CLI, message handlers, batch jobs).

---

### 3. Критерий: роль или voter?

#### Чёткий вопрос

> **Можно ли выразить решение, зная только роль пользователя?**

| Если да → | Если нет → |
|-----------|-----------|
| Роль | Voter |
| `access_control` / `#[IsGranted('ROLE_X')]` | `Voter` |

#### Конкретные примеры

**Роль достаточно:**

```php
// «Можно ли смотреть /admin/dashboard?»
// Ответ: да, если ROLE_ADMIN. Ничего другого не зависит.
#[IsGranted('ROLE_ADMIN')]
public function dashboard(): Response { ... }
```

```php
// «Можно ли смотреть /api/v1/users?»
// Ответ: да, если ROLE_USER. Все пользователи видят всех.
#[IsGranted('ROLE_USER')]
public function listUsers(): Response { ... }
```

**Voter необходим:**

```php
// «Можно ли редактировать post#42?»
// Ответ зависит от:
//   - роли (ADMIN → да)
//   - авторства (authorId === userId → да)
//   - статуса (Draft → да, Published → нет)
//   - времени (в пределах 7 дней → да)
// → Voter
```

```php
// «Можно ли удалить invoice#123?»
// Ответ зависит от:
//   - роли (ADMIN → да)
//   - владельца (companyId === user.companyId → да)
//   - статуса (Draft → да, Paid → нет)
//   - суммы (amount > 10000 → только MANAGER)
// → Voter
```

#### Матрица

| Ситуация | Механизм | Почему |
|----------|----------|--------|
| «Только админы» | Роль | Одна роль, один ответ |
| «Только автор» | Voter | Зависит от `authorId` объекта |
| «Только владельцы компании» | Voter | Зависит от `companyId` объекта |
| «Админы + авторы + модераторы конкретного раздела» | Voter | Три условия, одно из которых |
| «Статус Draft → автор, Published → все» | Voter | Зависит от статуса объекта |
| «Сумма > X → только MANAGER» | Voter | Зависит от данных объекта |

---

### 4. Антипаттерны

#### ❌ 1. Voter на каждый endpoint

```php
// 15 endpoint'ов, каждый со своим voter'ом
#[IsGranted('ORDER_VIEW')]
#[IsGranted('ORDER_EDIT')]
#[IsGranted('ORDER_DELETE')]
#[IsGranted('ORDER_CANCEL')]
#[IsGranted('ORDER_REFUND')]
// ... 10 больше
```

**Почему плохо:** 15 voter'ов, каждый с `supports()` и `voteOnAttribute()`. Когда добавляется новое правило — нужно менять 15 мест.

**Как правильно:** один voter на доменную сущность:

```php
final class OrderVoter extends Voter
{
    public const VIEW = 'ORDER_VIEW';
    public const EDIT = 'ORDER_EDIT';
    public const CANCEL = 'ORDER_CANCEL';
    public const REFUND = 'ORDER_REFUND';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'ORDER_') && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, Order $order, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW   => true,  // все видят свои
            self::EDIT   => $user->id === $order->userId && $order->isEditable(),
            self::CANCEL => $user->id === $order->userId && $order->status === OrderStatus::Pending,
            self::REFUND => $user->hasRole('ROLE_ADMIN') && $order->isRefundable(),
        };
    }
}
```

---

#### ❌ 2. Роли как флаги

```php
// У пользователя 40 ролей:
['ROLE_USER', 'ROLE_CAN_EDIT_POSTS', 'ROLE_CAN_VIEW_REPORTS',
 'ROLE_CAN_EXPORT_CSV', 'ROLE_CAN_DELETE_ORDERS', ...]
```

**Почему плохо:**
- Роли не масштабируются. 40 ролей × 100 пользователей = 4000 строк в БД.
- Нет семантики. `ROLE_CAN_EDIT_POSTS` — это право, а не роль.
- Невозможно делегировать.

**Как правильно:**

```php
// Роли — «кто ты» (3–5 штук)
['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR']

// Права — voter'ы (ограниченный набор)
// OrderVoter: VIEW, EDIT, CANCEL, REFUND
// PostVoter:  VIEW, EDIT, DELETE, PUBLISH
// ReportVoter: VIEW, EXPORT
```

---

#### ❌ 3. Voter в контроллере, а не в service layer

```php
// ❌
#[IsGranted('EDIT', 'post')]
public function update(Post $post): Response
{
    $post->title = $request->get('title');  // ← кто-то может вызвать это из CLI
    $this->repo->save($post);
}

// ✅
final class PostService
{
    public function update(int $id, string $title): Post
    {
        $post = $this->repo->find($id);

        // Проверка ДО мутации
        $this->auth->isGranted(PostVoter::EDIT, $post)
            or throw new AccessDeniedException();

        $post->title = $title;
        $this->repo->save($post);
        return $post;
    }
}

// Контроллер — тонкая обёртка
#[Route('/posts/{id}', methods: ['PUT'])]
public function update(Post $post, PostService $service, Request $request): Response
{
    return $service->update($post->id, $request->get('title'))->toDto();
}
```

---

#### ❌ 4. `hasRole()` в бизнес-логике

```php
// ❌
public function approve(int $invoiceId): void
{
    $invoice = $this->repo->find($invoiceId);

    if ($this->security->isGranted('ROLE_ADMIN')) {
        $invoice->approve();
    } else {
        throw new AccessDeniedException();
    }
}

// ✅
// Voter уже знает: ADMIN может approve, MANAGER — если сумма < X
$this->auth->isGranted(InvoiceVoter::APPROVE, $invoice)
    or throw new AccessDeniedException();
```

**Почему:** `isGranted('ROLE_ADMIN')` — это проверка аутентификации, не авторизации. Авторизация — это `isGranted(APPROVE, $invoice)`.

---

### 5. Итоговая архитектура

```
┌──────────────────────────────────────────────────────────────┐
│  Firewall                                                     │
│  JWT / Form login → Token (user + roles + attributes)        │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  access_control (routing)                                     │
│  «Этот маршрут — только для ROLE_ADMIN»                       │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  #[IsGranted] на контроллере                                  │
│  «Этот endpoint — только если voter разрешает»                │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  Service layer                                                │
│  AuthorizationChecker::isGranted(Voter, $object)             │
│  → Защита бизнес-логики от любого вызова (CLI, worker, etc.) │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  Voter                                                        │
│  Зависит от: роли + данных объекта + контекста               │
└──────────────────────────────────────────────────────────────┘
```

**Принцип:** чем глубже в стек, тем конкретнее проверка. Routing — грубое фильтрование. Voter — точечное решение.

> ⏱ 31.9s · prompt 28108 tok · output 4158 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

We need answer user's question in Russian: How to store Value Object in Doctrine when domain requires immutability and always valid state? Ways mapping, pros/cons/pitfalls. Need produce final. Need think thoroughly. Need cover: Doctrine ORM mapping options: 1) map fields to entity columns and construct VO on load/wrap, maybe DTO; 2) custom Type (scalar type) storing as string/json/binary; 3) embedded entity? 4) custom mapping / lifecycle callbacks / hydration; 5) EventSubscriber to validate; 6) readonly properties, constructor, invariant, validation, immutable value object, repository/DTO; 7) DB constraints; 8) serialization with custom serializer; 9) DB-level generated columns / triggers; 10) aggregate root invariants, repository, DDD, persistence vs domain.

Need explain: Doctrine hydrates entities by reflection/setter, bypassing constructors by default, so immutable VO in entity can be problematic: entity's VO may be null initially, mutable setter, invalid intermediate state, partial updates, partial hydration, UnitOfWork changes detection, cascade, versioning, serialization, denormalization, indexing, querying by VO fields.

Need answer with practical patterns.

Main approaches:
A. Store primitive fields in entity, expose VO via getter; entity is mutable proxy but VO constructed from valid primitives. Validate before persist. Good query/index, simple, but entity can be in invalid state transient; invariants not enforced by constructor; need validation/service.
B. Store VO as scalar via custom Type: e.g. MoneyType as integer cents or decimal string, EmailType as string, PeriodType as ISO string, AddressType as JSON string. Entity constructor can require VO, but Doctrine still sets properties via reflection if property exists; if using getter returning computed VO from primitive fields, custom type on a scalar field may work: e.g. private Money $amount; custom type maps Money to integer. But constructor with readonly Money? Doctrine cannot instantiate with constructor args, unless using EventSubscriber/hydration. If property is readonly, Doctrine cannot set after construction; with PHP 8.1 readonly, if entity has readonly property, Doctrine can't hydrate. Use regular property or private field. Custom Type with immutable object: Type::convertToPHPValue returns new Money, convertToDatabaseValue returns int. Pros: encapsulated, query by underlying column if raw column exists? But if mapped property object, DQL may need type conversion; comparisons tricky. Cons: custom type must serialize deterministic, versioning, JSON schema, indexing? The DB stores scalar so indexable; but ORM object comparison and partial updates issues; type upgrade/migration; equality.
C. Embeddable value object (embeddable class mapped to columns). Doctrine embeddable can have multiple fields; still mutable unless careful; can map embedded readonly? issues with readonly properties and hydration. Pros: structured, multiple columns, queryable. Cons: embedded classes are not standalone entities, hydration bypasses constructor; nested embedded readonly issues; complex.
D. Custom hydration / EntityListener to construct immutable entity/VO. Use ORMEvents::postLoad or postHydrate to build immutable VO from primitives; repository wraps entity with domain object. Or use DTO pattern: Doctrine entity is anemic anvil, repository maps to domain model (immutable aggregate/VO). Pros: domain truly immutable, clean separation. Cons: mapping overhead, no bidirectional sync if using same object, partial updates, performance, complex.
E. Repository pattern with domain model: Doctrine entity as storage DTO; service uses Domain VO. On save, write primitives. Pros: best domain consistency. Cons: more code, two representations, mapping bugs, hydration.
F. DB constraints + app validation: unique, check constraints, not null, enum. Not enough for business invariants but defense in depth.
G. Use Doctrine with constructor? There is no first-class constructor hydration. Libraries? doctrine/orm doesn't support constructor arguments. You can use `#[ORM\Entity]` with `#[ORM\Attribute]`, but hydration still sets fields. Could use event subscriber to instantiate entity via constructor after hydration? Need careful.
H. Use `readonly` fields in VO (not entity) and store scalar.
I. Custom comparison and equality: VO should implement equals/hash? Doctrine identity uses entity id, not VO. When comparing changes, if property is object, Doctrine compares by object identity? For primitive fields it compares value; for object types maybe compares by reference? Important: UnitOfWork change set detection uses property values; if VO is a custom type scalar, it's fine. If object property, can be tricky.
J. Immutability with readonly entity? Not compatible with hydration unless generated via custom. Some use `readonly` with constructor and hydration via event? Could mention.
K. Always valid state: enforce with constructor, validation, DB constraints, transactional invariants; don't expose setters. Use DTO commands; aggregate methods. For value object in entity, either VO property is final after construction but Doctrine hydrates before constructor? We can store primitives and expose VO via getter; then state validity from primitives; but entity may be constructed empty then setters. Better domain object built via factory.
L. Querying: If VO stored as scalar custom type, query with type? DQL: WHERE m.amount = 100? If custom type, maybe need type conversion. If embedded columns, query by column: p.address.city. If JSON, query JSONB (Postgres) or not well supported. If separate columns, easiest.
M. Indexing: scalar columns can be indexed; JSON not as easily; custom type scalar can be indexed.
Migrations: custom types require dbal type mapping; JSONB for PG. Changing VO representation requires migration.
N. Serialization: Symfony Serializer with custom normalizer; Doctrine custom type does not affect JSON; ensure immutability and no getters? Use denormalizer.
O. Equality/hash in collections; Doctrine uses object equality? Mention.

Need maybe structure answer:
- Start: Doctrine hydrates via reflection/setters, not constructors; therefore immutable domain object cannot be a plain entity property with constructor if using ORM directly. Decide level: domain VO vs persistence model.
- Recommended patterns:
  1. Store primitives, expose VO (most pragmatic): entity fields + VO getter; validate in domain method; DB constraints. Example Money as cents/currency, Email, Address as columns or string. Pros: queryable, simple, migrations, indexing. Cons: entity mutable transient, need validation, can be invalid before getter used.
  2. Custom Type mapping VO to scalar: example MoneyType -> int/decimal. Pros: property can be Money object in persistence class, immutable VO; DB stores scalar. Cons: Doctrine hydrates into property bypass constructor; must have non-readonly property; DQL/type conversion; equality; custom type must be stateless; if VO multi-field store as string/JSON; migrations.
  3. Embeddable multi-column VO: Address embedded. Pros: multiple indexed columns; query; type. Cons: still mutable, hydration, nested readonly pitfalls.
  4. Separate domain model/DTO + repository: Best for DDD. Doctrine entity is storage; domain object immutable; repository maps. Pros: true immutability and invariants; cons: mapping effort, no identity synchronization, performance, double models.
  5. EventSubscriber/postLoad for immutable construction. Niche.
  6. DB constraints/triggers for last line.

Need maybe include table.
Need mention pitfalls:
- readonly properties on Doctrine entities often break hydration; avoid unless custom hydration.
- `var_dump`? not relevant.
- partial updates: `$entity->amount = new Money(...);` bypasses invariants; use setter with validation or immutable domain methods.
- `UnitOfWork` change detection: object equality vs value equality. If using object property, changing to equal value may still mark changed? Actually Doctrine compares property values using `==`? For object types maybe uses `===`? Need careful. It may detect changes via comparing values; for objects, if not same instance, may detect? I should state: if property is object, Doctrine's change detection may treat non-identical objects as changed, causing writes; prefer scalar persistence for reliable diff.
- DQL with custom object: can't use `WHERE money > 100` unless stored scalar or type conversion; with custom type, use native SQL or DBAL maybe.
- JSONB: flexibility but poor indexing/query; need GIN if search; less type safety.
- Serialization and caching: VO object may not serialize cleanly; use DTO.
- DB enums: store as string/int with app-level enum.
- Versioning: storing scalar helps.
- Migration: changing representation requires data migration.
- Validation: constructor validation, but DB may allow invalid; use check constraints and repository save.
- Hydration and proxies: lazy-loading can leave object uninitialized? For object property custom type? If entity has lazy proxy, accessing VO may trigger? Not if scalar.
- PHP readonly in VO: fine. Entity properties should not be readonly if Doctrine sets them.
- `#[ORM\Column(type: 'money')]` custom type.
- `getters` vs `properties` in Doctrine 2.15/3? Doctrine 3 supports getters? Could mention if using Doctrine 3, property vs getter mapping; but not overdo.

Need answer with examples: Money, Email, Period, Address.

Potential recommended final answer:
- If single-field VO and needs query/index: store scalar in DB, expose immutable VO via getter or custom type.
  - Best default: primitive fields + immutable getter:
```php
class Invoice {
  #[Column(type: 'integer')] private ?int $amountCents = null;
  #[Column(length:3)] private ?string $currencyCode = null;
  private ?Money $amountVO = null;
  public function amount(): Money {
     if ($this->amountCents === null) throw ...;
     return new Money($this->amountCents, $this->currencyCode);
  }
  public function setAmount(Money $money): self {
     $this->amountCents = $money->cents; $this->currencyCode = $money->currency; return $this;
  }
}
```
But entity constructor? Doctrine can't use constructor? If no constructor args, fine.
- Custom Type:
```php
class MoneyType extends Type { ... convertToPHPValue($value) new Money((int)$value, 'EUR')? Need multi-field cannot one scalar unless encode. Better store cents and currency separate; custom type for single scalar like EmailType: new Email((string)$value)
```
For multi-field, use embeddable or composite.
- Embeddable:
```php
#[Embeddable]
class Address { #[EmbeddingColumn] private ?string $street; ... public static function create(...): self { validate } }
```
But Doctrine embeddable hydration? It sets fields; use static factory for creation.

Need mention "always valid state": enforce with invariants at construction of VO, not relying on setters; DB checks; transactional consistency. In DDD, repository saves aggregate after methods; invalid state can't be persisted if aggregate method validates.

Potential nuanced answer: Value Object is domain concept; Doctrine entity is not necessarily domain model. If VO immutable, don't make it an entity; make it property or embedded. For immutable entity? Doctrine entity usually has identity; value object not entity. If aggregate contains VO, VO should be value object class. Store via scalar columns.

Need maybe explain "Value Object in Doctrine" includes `Money`, `Email`, `Period`, `Address`, `Name`, `PhoneNumber`. Need provide decision tree.

Could include recommended pattern code:
- Immutable VO:
```php
final readonly class Money {
  public function __construct(public int $cents, public string $currencyCode) {
    if ($cents < 0) throw ...;
    if (!preg_match...) throw;
  }
}
```
- Entity storage option 1:
```php
#[ORM\Entity]
final class Order {
  #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id=null;
  #[ORM\Column] private ?int $amountCents=null;
  #[ORM\Column(length:3)] private ?string $currency=null;
  public function amount(): Money { return new Money($this->amountCents, $this->currency); }
  public function setAmount(Money $money): void { ... }
}
```
Potential issue: entity final readonly? Not with Doctrine? Could be final class but properties not readonly.

- Custom type for single scalar:
```php
#[Type('email')]
private ?Email $email = null;

class EmailType extends Type {
  public function getSQLDeclaration(...): string { return 'VARCHAR(255)'; }
  public function convertToPHPValue($value, AbstractPlatform $platform): ?Email {
    if ($value === null) return null;
    return Email::fromString((string)$value);
  }
  public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
    return $value?->address;
  }
  public function getName(): string { return 'email'; }
}
```
Pitfalls: Email constructor validates; if invalid data in DB, hydration throws; need safe fromString? Could have invalid rows; handle with exception. Query: WHERE e.email = ?email? DQL type? Better query by raw string: `WHERE e.email = :email` with parameter email string? If type expects object, may convert? Actually custom type conversion for parameters maybe if DQL type? I need be accurate. In DQL, when comparing with custom type, Doctrine may pass PHP value? You can set parameter as string or Email; not certain. Better recommend raw column for queryability if custom type object. Could mention: query by native SQL or map scalar field instead.

- Embeddable:
```php
#[ORM\Embeddable]
final class MoneyValue {
  #[ORM\EmbeddingColumn(type:'integer')] private ?int $cents = null;
  #[ORM\EmbeddingColumn(length:3)] private ?string $currency = null;
  public function toMoney(): Money { return new Money($this->cents, $this->currency); }
}
class Order {
  #[ORM\Embedded(class: MoneyValue::class)] private MoneyValue $money;
  public function __construct() { $this->money = new MoneyValue(); }
  public function amount(): Money { return $this->money->toMoney(); }
}
```
Hydration? Embedded objects instantiated via constructor? It may use constructor? I think Doctrine instantiates embeddable without constructor? Need not rely. It can set properties.

- Domain model/DTO:
```php
final class OrderDomain {
 private function __construct(...) {}
 public static function fromEntity(OrderEntity $e): self {...}
}
class OrderRepository {
 public function save(OrderDomain $d): void { $entity = OrderEntity::fromDomain($d); ... }
}
```
Pros: true immutable.

Need discuss `readonly` and constructor. In entity, don't use `readonly` properties because Doctrine sets them; use `final class` and private properties with no setters, but still transient state. For VO use readonly.

Need mention validation strategies:
- VO constructor validates syntax/format.
- Domain method validates context (balance, uniqueness)
- DB constraints: NOT NULL, CHECK, UNIQUE, enum.
- Symfony Validator maybe, but not substitute.
- Transactional atomicity.

Need include pitfalls:
1. Hydration bypasses constructor -> null fields until loaded.
2. `readonly` entity property incompatible.
3. UnitOfWork diffing with object properties unreliable; scalar best.
4. Query/index: object property not directly queryable; custom type maybe; JSON poor.
5. Invalid DB rows cause hydration exceptions; need migration and safe fallback.
6. Custom Type must be deterministic and platform-safe; timezone/locale for dates; decimal precision.
7. Partial hydration/proxies can leave VO uninitialized? if property object.
8. Serialization/caching: object not serializable; normalizers.
9. Changing VO representation: migrations; versioning.
10. Equality: use value equality in domain, not `==`? Actually PHP == for objects compares class and properties? Could mention implement custom equals if used in arrays; Doctrine identity by ID.

Need maybe answer "what to choose" decision:
- Single scalar and queryable: primitive column + getter, or custom Type if property should be object.
- Multi-field and queryable: embeddable or primitive columns.
- Strict DDD/invariants: domain object + repository.
- Flexible/no query: JSONB with immutable normalizer (but avoid for critical invariants).

Need maybe include sample for `Period` VO as start/end; store `start_date`, `end_date`; query by range. Custom type to ISO? Maybe too long.

Need final high oververbosity 9. Should be detailed, structured, examples, trade-offs. Need be concise enough but thorough.

Need ensure no internal analysis. Let's craft final in Russian.

Potential structure:
1. Key principle: Doctrine is persistence, not domain enforcement. Value object should be immutable in domain; storage is representation.
2. Problem: hydration bypasses constructor, readonly, partial updates, change tracking.
3. Ways:
   - A: Store primitives and construct VO in getter (recommended default)
   - B: Custom DBAL Type for object property
   - C: Embeddable composite VO
   - D: Separate domain aggregate/DTO and repository mapping (strict DDD)
   - E: JSONB/serialized (when)
   - F: DB constraints/trigger/event listeners (supporting)
4. Detailed examples: Money (multi-field), Email (single-field), Address (embeddable).
5. Trade-off table.
6. Pitfalls checklist.
7. Recommendation.

Need include code snippets. Need make sure code correct. Let's design examples.

Example 1: primitives + getter:
```php
final readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currencyCode,
    ) {
        if ($cents < 0) throw new \DomainException('Money cents must be >= 0');
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) throw ...;
    }
}

#[ORM\Entity]
final class Invoice
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;
    #[ORM\Column] private ?int $amountCents = null;
    #[ORM\Column(length: 3)] private ?string $currencyCode = null;

    public function amount(): Money
    {
        if ($this->amountCents === null || $this->currencyCode === null) {
            throw new LogicException('Invoice amount not loaded');
        }
        return new Money($this->amountCents, $this->currencyCode);
    }

    public function setAmount(Money $money): self
    {
        $this->amountCents = $money->cents;
        $this->currencyCode = $money->currencyCode;
        return $this;
    }

    // Maybe public function withAmount(Money $money): self? But entity mutable.
}
```
Pros: query `WHERE i.amountCents BETWEEN ...`, indexes. Cons: entity can be invalid transient, need service to set.

Example 2 custom type for Email:
```php
final readonly class Email {
  private function __construct(public string $address) {}
  public static function fromString(string $value): self {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) throw ...;
    return new self($value);
  }
}

class EmailType extends Type
{
    public const NAME = 'email';

    public function getName(): string { return self::NAME; }

    public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
    {
        return 'VARCHAR(' . max(255, $length) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if ($value === null) return null;
        if (!is_string($value)) throw new LogicException(...);
        return Email::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;
        if (!$value instanceof Email) throw new LogicException(...);
        return $value->address;
    }
}

#[ORM\Column(type: EmailType::NAME)]
private ?Email $email = null;
```
Pitfall: if DB has invalid email, hydration throws; use safe version? Could have `Email::unsafe`? Better mention data integrity.

For Money custom type as two columns? DBAL type usually maps one scalar. Could map composite to string `12345:EUR`, but query/index hard. Better use embeddable or primitive columns.

Example 3 embeddable:
```php
#[ORM\Embeddable]
final class MoneyEmbedded
{
    #[ORM\EmbeddingColumn] private ?int $cents = null;
    #[ORM\EmbeddingColumn(length:3)] private ?string $currencyCode = null;
    public function toMoney(): Money { ... }
    public function setFrom(Money $money): void { ... }
}

#[ORM\Entity]
final class Order
{
    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $money;

    public function __construct() { $this->money = new MoneyEmbedded(); }
    public function total(): Money { return $this->money->toMoney(); }
}
```
Need check Doctrine attribute `EmbeddingColumn`? In Doctrine ORM 3 maybe `#[ORM\Column]` inside embeddable? Actually Doctrine embeddable uses `#[ORM\Embeddable]` and fields with `#[ORM\Column]`? In old: `#[EmbeddedColumn]`? Let's recall. Doctrine ORM embeddable: `#[Embeddable]` on class, inside use `#[Column]`? Example:
```
#[Embeddable]
class Address {
 #[Column] private ?string $city = null;
}
```
For entity: `#[Embedded(class: Address::class)] private Address $address;`. I think use `#[ORM\Column]`, not `EmbeddingColumn`. In older `@EmbeddedColumn` maybe. To avoid wrong, use `#[ORM\Column]` inside embeddable. But I should be accurate. Let's check memory: Doctrine ORM 2: `#[ORM\Embeddable]` and `#[ORM\Embedded(class: ...)]`. Inside embeddable, fields are `#[ORM\Column]`. There is `#[ORM\ManyToOne]` etc. No `EmbeddingColumn`. Good.

```php
#[ORM\Embeddable]
final class MoneyEmbedded
{
    #[ORM\Column(type: 'integer')]
    private ?int $cents = null;
    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;
    ...
}
```
Pros: multiple columns, query `o.money.cents`? DQL path: `o.money.cents` works? For embeddable, DQL `o.money.cents`? Yes.

Need mention if embeddable has multiple fields, query and indexes.

Example 4 DTO/domain mapping:
```php
final class OrderDomain
{
    private function __construct(
        public readonly int $id,
        public readonly Money $total,
    ) {}
    public static function fromEntity(OrderEntity $entity): self {...}
    public function toEntity(): OrderEntity {...}
}
```
But mapping both ways maybe.

Need maybe mention Doctrine custom hydrator? There is `postLoad` subscriber:
```php
final class OrderSubscriber extends EventSubscriber
{
  public function postLoad(LifecycleEventArgs $args): void
  {
     $order = $args->getObject();
     if ($order instanceof Order) {
        // Can't set readonly property, but can call method to initialize VO from primitives
        $order->initMoneyVo();
     }
  }
}
```
But not necessary.

Need discuss validation of state:
- VO constructor ensures format (Email valid, Money positive).
- Aggregate methods ensure context (account balance non-negative) by mutating domain and saving.
- DB constraints ensure integrity if direct SQL or bugs.
- Repository ensures only valid aggregate persists.

Need maybe talk about `immutability` in PHP: `final readonly class` for VO. For entity, avoid `readonly` properties; can use `final` class and private setters? But if no setters, how to set? Use constructor? Doctrine can't. Could use `#[ORM\Entity]` with constructor arguments? No. So entity must have default null values or event. Could use `Doctrine\Common\EventSubscriber` to create entity after hydration? Maybe too niche.

Need include "always in valid state": For entity with primitives, if amountCents and currencyCode separate, invalid combinations possible (null one). Solve by using domain object or repository factory that sets both; or use custom type object (Email) so atomic; or embeddable with invariant? Still can be partial after hydration; validate on load.

Could mention "value object as scalar custom type gives atomic storage if single scalar; multi-field composite can be partial unless you use a single serialized scalar (JSONB) but loses query/index."

Need maybe answer with "best practice: separate domain model from ORM entity; if not, use scalar columns and immutable getter."

Need include "Doctrine cannot construct value object in constructor during hydration".

Need maybe include "Doctrine 3 supports constructor hydration? I think not. It still hydrates by property/setter. There is `Doctrine\ORM\Mapping\Entity` with `#[ORM\Entity]` and constructor? It won't call constructor with args; it may call constructor if no args? Actually if entity has constructor without args, it can be called? But not with required args. We can say not reliable."

Need maybe include "Using `readonly` property in entity and custom Type: hydration may fail because cannot set readonly property after object created. If entity has no constructor, object created by ReflectionClass->newInstanceWithoutConstructor? Doctrine may use newInstance, then set properties. If property readonly, setting fails. If constructor sets readonly, cannot. So avoid."

Need maybe include "Use `__construct` with defaults? e.g. private ?Email $email = null; property not readonly. Then custom type sets."

Need maybe mention "If you use `#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]`? Not relevant.

Need maybe mention "For enum-backed VO? Store scalar string/int; expose enum."

Need maybe include "Postgres JSONB" option:
```php
#[ORM\Column(type: 'json')]
private array $addressData;
public function address(): Address { return Address::fromArray($this->addressData); }
```
Pros: flexible. Cons: hard to index/query, no DB-level invariants, migrations.

Need maybe include "For always valid, use DB CHECK constraints: `amount_cents >= 0`, `currency_code ~ '^[A-Z]{3}$'`, unique email, etc."

Need maybe mention "Symfony serializer normalizer for VO, not Doctrine."

Need maybe answer in final with "I recommend" and table.

Need ensure no false statements. Let's verify custom Type `getSQLDeclaration` signature: In DBAL 3, `getSQLDeclaration(int $length, AbstractPlatform $platform)`. In DBAL 4 maybe similar. Use generic. `convertToPHPValue(mixed $value, AbstractPlatform $platform)`. Good. `getName`. `requiresSQLCommentHint`? not needed.

Need maybe include "register type in `config/packages/doctrine.yaml` or `Types::addType('email', EmailType::class)`."

Need maybe include "For custom Type with object property, Doctrine will call `convertToPHPValue` on hydration. If invalid data, throw. If you need legacy invalid data, use unsafe conversion + validation later, but that breaks domain invariant."

Need maybe include "If VO has multiple fields and query by fields, don't store JSON; use columns."

Need maybe discuss "Equality and change detection". More precise: Doctrine's UnitOfWork compares original and current data. For scalar types, easy. For object types mapped via custom Type, current value is converted to database value before comparison? It may compare PHP values. If you replace Money object with another Money of same values, Doctrine may mark changed? Because object identity? I think it converts to database value for comparison? Not sure. To be safe: "object properties can make change detection less predictable; scalar storage gives stable comparison." Good.

Need maybe mention "partial updates and flush: if you only change one field of composite VO, ensure all fields updated atomically; use domain method."

Need maybe mention "versioning: scalar columns easier to version/audit; JSON hard."

Need maybe mention "caching: scalar cacheable; object not."

Need maybe mention "read model: if only querying, can denormalize."

Need final with examples. Need maybe avoid too much length? Desired 9, so detailed.

Let's craft final.

Potential final:

```
Короткий вывод: Value Object в домене — immutable final readonly class. В Doctrine лучше хранить не объект, а его примитивное представление. Объект восстанавливать при чтении. Если хочется объектный доступ в entity — custom Type или embeddable, но у каждого есть подводные камни.
```

Then sections.

Let's write in Russian.

Need include code for `Money` and `Email`. Need maybe use `DomainException` but user might use custom; okay.

Let's prepare final.

Detailed content:

1. Start: two planes: domain and persistence.
2. Why hard: Doctrine hydration, readonly, partial state, change tracking.
3. Patterns:
   A. Primitive columns + immutable getter.
   B. Custom DBAL Type for single-scalar VO.
   C. Embeddable for multi-field VO.
   D. Domain model + repository (DTO mapping).
   E. JSONB / serialized.
   F. DB constraints and validation.
4. Trade-off table.
5. Pitfalls.
6. Decision tree / recommendation.

Need make examples correct.

For embeddable code:
```php
#[ORM\Embeddable]
final class MoneyEmbedded
{
    #[ORM\Column(type: 'integer')]
    private ?int $cents = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;

    public function toMoney(): Money
    {
        if ($this->cents === null || $this->currencyCode === null) {
            throw new LogicException('Money is not initialized');
        }

        return new Money($this->cents, $this->currencyCode);
    }

    public function setMoney(Money $money): void
    {
        $this->cents = $money->cents;
        $this->currencyCode = $money->currencyCode;
    }
}
```
Entity:
```php
#[ORM\Entity]
final class Order
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;
    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $amount;

    public function __construct()
    {
        $this->amount = new MoneyEmbedded();
    }

    public function total(): Money
    {
        return $this->amount->toMoney();
    }

    public function setTotal(Money $total): void
    {
        $this->amount->setMoney($total);
    }
}
```
Pros: query `o.amount.cents`.

Need caution: `final class` with embeddable? okay.

Custom Type for Money? Since Money multi-field, not ideal; show Email. If user asks value object general, use Email as single scalar. Could also show composite serialized? But table covers.

Need mention if using custom Type for multi-field, e.g. encode to string `12345:EUR`, but query/index bad.

Potential nuance: In custom type, `convertToPHPValue` can throw on invalid DB row. Use `try`? But domain invariant says invalid state impossible; if DB has invalid due to manual SQL, fail fast. Could mention "if you must migrate legacy, add safe parser + validation queue, but do not silently accept."

Need include "readonly VO":
```php
final readonly class Email
{
    public function __construct(public string $address)
    {
        if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailException($address);
        }
    }
}
```
PHP readonly with constructor promotion? Yes `readonly` class, `public string $address` readonly? In PHP 8.2 readonly class makes all properties readonly. Good.

Need mention `final readonly class Money` with two fields.

Need include "entity property with custom type must be nullable and non-readonly".

Need maybe include "Don't expose setters on VO; use static factory."

Need maybe include "If aggregate requires context, put invariant in aggregate, not VO alone. Example Account balance: Money positive, but overdraft depends on account; method `debit(Money)` checks balance."

Need maybe mention "DB constraints as last line, not business rules, because DB constraints limited."

Need maybe include "Symfony Validator for input, not for persistence invariant."

Need maybe include "Use repository to only persist after invariants."

Need maybe include "For events / outbox: if sending email after registration etc? Not relevant."

Let's formulate final with clear headings.

Potential final text:

```
Главная мысль: не заставляйте Doctrine хранить "иммутабельный объект" в том виде, в котором он существует в домене. Храните его представление, а объект стройте на границе чтения.
```

Then examples.

Need maybe include "Doctrine 2/3" not too version-specific.

Let's ensure no contradiction: In pattern A, entity can be in invalid state transiently. How to mitigate? Use domain object or `create()` factory:
```php
final class Invoice
{
   private function __construct(...) { }
   public static function create(...): self { ... }
}
```
But Doctrine hydration? If private constructor no args? If constructor no args? Could use `public static function create` and entity fields default null; Doctrine creates via no-arg? If constructor exists with no required args? It may call? If private no-arg? Hmm. Better avoid requiring constructor for entity. Use default nulls and factory for creation? But if factory sets fields, okay. Doctrine can hydrate by reflection even if private constructor? It may instantiate without calling constructor if no accessible no-arg constructor? I think it uses `ReflectionClass::newInstance()`, which may fail if constructor private? Actually `newInstance` on private constructor fails? It requires public? It may use `newInstanceWithoutConstructor` if no constructor? Not sure. Safer: public no-arg constructor or no constructor.

Example:
```php
#[ORM\Entity]
final class Invoice
{
    #[ORM\Id] ... private ?int $id = null;
    private ?int $amountCents = null;
    private ?string $currencyCode = null;

    public static function create(Money $amount): self
    {
        $e = new self();
        $e->amountCents = $amount->cents;
        $e->currencyCode = $amount->currencyCode;
        return $e;
    }
}
```
But `create` not called by Doctrine. It just uses `new self()` if no constructor or no-arg public. If private constructor? avoid. We can say "entity typically has anemic fields; factory is only for application code." Good.

Need mention "Doctrine entity is not necessarily immutable; if you need immutability, use domain object or repository."

Need maybe include "Use `#[ORM\Entity]` with `final`? okay."

Need final table:
Pattern / when / plus / minus.

Need include pitfalls list.

Let's write final now. Ensure answer directly addresses. Use Russian. Need maybe no overly long but thorough. Final.

</details>

**Кандидат:**

Коротко: **в домене Value Object — это immutable-объект, в Doctrine лучше хранить его примитивное представление**. Не пытайтесь заставить ORM «держать» immutable-объект в том виде, в каком он живёт в домене, — Doctrine hydrate’ит сущности через reflection/setters и обходит конструкторы. Поэтому ключевая задача — правильно выбрать **представление в БД** и **точку, где immutable VO создаётся**.

Ниже — способы, trade-offs и подводные камни.

---

# 1. Главное противоречие: immutable VO vs Doctrine

## В домене

```php
final readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currencyCode,
    ) {
        if ($cents < 0) {
            throw new DomainException('Money must be non-negative');
        }

        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw new DomainException('Invalid currency code');
        }
    }
}
```

Требования:

- объект создаётся только валидным;
- поля не меняются;
- нет setter’ов;
- нарушить инварианты нельзя.

## В Doctrine

ORM обычно работает так:

1. создаёт объект без конструктора;
2. заполняет свойства reflection/setter’ами;
3. при `flush()` сравнивает текущее и исходное состояние;
4. строит SQL.

Проблемы:

- **Doctrine не вызывает конструктор с аргументами.**
- **`readonly`-поля в entity обычно ломают hydrate.**
- **Entity может находиться в промежуточном невалидном состоянии:**  
  `amountCents = 100`, `currencyCode = null` и т.п.
- **Partial update** может обновить только часть составного VO.
- **Query/indexing** обычно проще для скаляров, чем для объектов.
- **Change tracking** надёжнее для скаляров, чем для объектных свойств.

Поэтому почти всегда лучше хранить в БД **primитивы**, а VO строить на границе чтения.

---

# 2. Рекомендуемый базовый паттерн: хранить примитивы, exposing immutable getter

Это самый практичный вариант для большинства Symfony/Doctrine-проектов.

## Пример: `Money`

```php
final readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currencyCode,
    ) {
        if ($cents < 0) {
            throw new DomainException('Money cents must be non-negative');
        }

        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw new DomainException('Invalid currency code');
        }
    }
}
```

Entity хранит примитивы:

```php
#[ORM\Entity]
final class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amountCents = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;

    public function amount(): Money
    {
        if ($this->amountCents === null || $this->currencyCode === null) {
            throw new LogicException('Invoice amount is not initialized');
        }

        return new Money($this->amountCents, $this->currencyCode);
    }

    public function setAmount(Money $money): void
    {
        $this->amountCents = $money->cents;
        $this->currencyCode = $money->currencyCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
```

## Плюсы

### 1. Хорошо для запросов

```php
$this->repo->findBy(
    [
        'currencyCode' => 'EUR',
        // amountCents можно использовать в DQL
    ]
);
```

DQL:

```dql
SELECT i
FROM App\Entity\Invoice i
WHERE i.currencyCode = :currency
  AND i.amountCents BETWEEN :min AND :max
```

### 2. Хорошо для индексов

```php
#[ORM\Index(columns: ['currencyCode', 'amountCents'])]
#[ORM\Entity]
class Invoice
{
    // ...
}
```

### 3. Просто для миграций

Изменили поле VO — изменили колонку, миграцию, маппинг.

### 4. Надёжный change tracking

Doctrine сравнивает `int` и `string`, а не объекты.

### 5. Минимум магии

Нет custom types, embeddables, сериализации.

## Минусы

### 1. Entity может быть временно невалидной

Например:

```php
$invoice->setAmount(Money::...);
```

до вызова — поля `null`.

Если между этими строками кто-то вызовет `amount()`, упадёт.

### 2. Инварианты не enforced на уровне entity

Entity — «анемичный» носитель состояния.

Безопасность обеспечивают:

- VO-конструктор;
- сервис/доменная модель;
- repository;
- DB constraints.

### 3. Нужно не допускать прямых `UPDATE` мимо домена

Иначе в БД могут попасть несовместимые значения:

```sql
UPDATE invoices SET amount_cents = 100, currency_code = NULL;
```

Лечится DB constraints:

```sql
ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_amount_cents CHECK (amount_cents >= 0);

ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_currency_code CHECK (currency_code ~ '^[A-Z]{3}$');
```

---

# 3. Custom DBAL Type: хранить VO как одно скалярное значение

Подходит для **однополевых VO**, например:

- `Email`
- `PhoneNumber`
- `Slug`
- `ApiKey`
- `Url`
- `IsoCountryCode`

## Пример: `Email`

```php
final readonly class Email
{
    public function __construct(public string $address)
    {
        if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException("Invalid email: {$address}");
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
```

Custom type:

```php
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmailType extends Type
{
    public const NAME = 'email';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
    {
        return 'VARCHAR(' . max(255, $length) . ')';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new LogicException('Email value must be string');
        }

        return Email::fromString($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Email) {
            throw new LogicException('Expected Email instance');
        }

        return $value->address;
    }
}
```

Регистрация типа:

```php
// Например, в AppKernel, boot или сервис-провайдере
use Doctrine\DBAL\Types\Type;
use App\DBAL\Types\EmailType;

Type::addType(EmailType::NAME, EmailType::class);
```

Использование:

```php
#[ORM\Entity]
final class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: EmailType::NAME, unique: true)]
    private ?Email $email = null;

    public function email(): ?Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }
}
```

## Плюсы

### 1. В коде можно работать с объектом

```php
$email = $user->email();
```

### 2. БД хранит скаляр

```sql
email VARCHAR(255)
```

### 3. Можно индексировать

```php
#[ORM\Column(type: EmailType::NAME, unique: true)]
```

### 4. Валидация при создании объекта

```php
new Email('not-an-email') // exception
```

## Минусы

### 1. Не очень подходит для составных VO

`Money` состоит из двух полей: `cents` и `currencyCode`.

Можно закодировать в строку:

```text
12345:EUR
```

Но это плохая идея, если нужно:

- индексировать `cents`;
- фильтровать по `currency`;
- делать агрегации;
- versioning/audit;
- удобно мигрировать.

### 2. Инвалидные данные в БД ломают hydration

Если в таблице лежит:

```text
not-an-email
```

то:

```php
$user = $repo->find(1);
```

может упасть с `DomainException`.

Это может быть хорошо: fail fast.  
Но плохо, если у вас legacy-данные.

### 3. Запросы становятся менее естественными

Можно сделать:

```dql
SELECT u FROM User u WHERE u.email = :email
```

Но если параметр передаётся как строка, а тип ожидает `Email`, возможны сюрпризы. Часто проще и безопаснее для запросов хранить `string` и делать валидацию отдельно.

---

# 4. Embeddable: хранить составной VO как несколько колонок

Подходит для составных VO, которые нужно **индексировать и запрашивать**:

- `Money`
- `Address`
- `Period`
- `Coordinates`
- `Name`

## Пример: `MoneyEmbedded`

```php
#[ORM\Embeddable]
final class MoneyEmbedded
{
    #[ORM\Column(type: 'integer')]
    private ?int $cents = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;

    public function toMoney(): Money
    {
        if ($this->cents === null || $this->currencyCode === null) {
            throw new LogicException('Money embedded is not initialized');
        }

        return new Money($this->cents, $this->currencyCode);
    }

    public function setMoney(Money $money): void
    {
        $this->cents = $money->cents;
        $this->currencyCode = $money->currencyCode;
    }
}
```

Entity:

```php
#[ORM\Entity]
#[ORM\Index(columns: ['amount_cents', 'amount_currency_code'])]
final class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: MoneyEmbedded::class)]
    private MoneyEmbedded $amount;

    public function __construct()
    {
        $this->amount = new MoneyEmbedded();
    }

    public function total(): Money
    {
        return $this->amount->toMoney();
    }

    public function setTotal(Money $total): void
    {
        $this->amount->setMoney($total);
    }
}
```

## Плюсы

### 1. Составной VO сохраняется в несколько колонок

```sql
amount_cents INT
amount_currency_code VARCHAR(3)
```

### 2. Можно индексировать

```php
#[ORM\Index(columns: ['amount_cents', 'amount_currency_code'])]
```

### 3. Можно запрашивать

```dql
SELECT o
FROM App\Entity\Order o
WHERE o.amount.currencyCode = :currency
  AND o.amount.cents BETWEEN :min AND :max
```

### 4. Чистее, чем JSONB

Нет сериализации, лучше индексы, проще миграции.

## Минусы

### 1. Всё равно есть промежуточные состояния

После hydration может быть:

```php
amount.cents = 100
amount.currencyCode = null
```

Поэтому `toMoney()` должен валидировать.

### 2. Doctrine не понимает ваши инварианты

Embeddable — это просто маппинг полей, а не доменная сущность.

### 3. Нельзя легко сделать «неделимый» VO

Если VO состоит из двух полей, БД физически хранит их раздельно.  
Нужны DB constraints:

```sql
ALTER TABLE orders
  ADD CONSTRAINT chk_order_amount_cents CHECK (amount_cents >= 0);

ALTER TABLE orders
  ADD CONSTRAINT chk_order_currency_code CHECK (amount_currency_code ~ '^[A-Z]{3}$');
```

---

# 5. Отдельная доменная модель + repository-маппинг

Это самый «чистый» DDD-вариант.

## Идея

Doctrine entity — **storage model**.  
Domain object — **immutable business model**.

```php
final class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amountCents = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;
}
```

Доменная модель:

```php
final readonly class Order
{
    public function __construct(
        public int $id,
        public Money $amount,
    ) {}

    public static function fromEntity(OrderEntity $entity): self
    {
        if ($entity->amountCents === null || $entity->currencyCode === null) {
            throw new DomainException('Order amount is not initialized');
        }

        return new self(
            $entity->id,
            new Money($entity->amountCents, $entity->currencyCode),
        );
    }

    public function toEntity(): OrderEntity
    {
        $entity = new OrderEntity();
        $entity->id = $this->id;
        $entity->amountCents = $this->amount->cents;
        $entity->currencyCode = $this->amount->currencyCode;
        return $entity;
    }
}
```

Repository:

```php
final class OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function find(int $id): Order
    {
        $entity = $this->em
            ->getRepository(OrderEntity::class)
            ->find($id);

        if ($entity === null) {
            throw new OrderNotFoundException($id);
        }

        return Order::fromEntity($entity);
    }

    public function save(Order $order): void
    {
        $entity = $order->toEntity();
        $this->em->persist($entity);
        $this->em->flush();
    }
}
```

## Плюсы

### 1. Максимальная защита инвариантов

Доменная модель создаётся только через конструктор.

### 2. Immutable-объект действительно immutable

```php
final readonly class Order
```

### 3. Doctrine entity не пугает бизнес-логику

Entity может быть «глупой».

### 4. Легко менять persistence

Можно заменить Doctrine на что угодно, не трогая домен.

## Минусы

### 1. Две модели — две точки, где можно ошибиться

Entity и domain object должны быть синхронизированы.

### 2. Больше кода

Для простых сущностей это избыточно.

### 3. Сложнее отлаживать

Нужно понимать, где mapping, где домен, где repository.

### 4. Плохо совместимо с «живыми» Doctrine-объектами

Если где-то передаётся entity, а где-то domain object, легко запутаться.

---

# 6. JSONB / serialized VO

Вариант для гибких VO:

- `Address`
- `UserProfile`
- `Metadata`
- `Settings`

## Пример

```php
#[ORM\Entity]
final class User
{
    #[ORM\Column(type: 'json')]
    private array $addressData = [];

    public function address(): Address
    {
        return Address::fromArray($this->addressData);
    }

    public function setAddress(Address $address): void
    {
        $this->addressData = $address->toArray();
    }
}
```

## Плюсы

### 1. Гибкость

Можно быстро добавлять поля.

### 2. Один «объект» в БД

Физически JSON-документ.

### 3. Удобно для read-heavy или динамических структур

## Минусы

### 1. Плохо для индексов

Можно делать GIN-индексы в PostgreSQL, но:

- сложнее;
- медленнее;
- менее предсказуемо.

### 2. Плохо для агрегаций

```sql
SELECT SUM(amount_cents) FROM orders;
```

в разы проще, чем из JSON.

### 3. Сложнее миграции

Изменили структуру VO — нужно мигрировать JSON.

### 4. Нет DB-level invariants

Можно сделать `CHECK (jsonb_typeof(address_data) = 'object')`, но бизнес-инварианты не проверить нормально.

---

# 7. DB constraints — обязательная поддержка

Ни один PHP-паттерн не спасает, если в БД могут попасть невалидные данные напрямую.

## Пример для `Money`

```sql
ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_amount_cents CHECK (amount_cents >= 0);

ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_currency_code CHECK (currency_code ~ '^[A-Z]{3}$');
```

## Пример для `Email`

```sql
-- Не идеально, но лучше чем ничего
ALTER TABLE users
  ADD CONSTRAINT chk_users_email_format CHECK (email ~* '^[^@]+@[^@]+\.[^@]+$');
```

Лучше:

- `VARCHAR`/`TEXT`;
- `UNIQUE`;
- нормализация в приложении;
- строгий custom type или доменная модель.

## Важно

DB constraints — **последняя линия обороны**, а не замена доменных правил.

Они хорошо ловят:

- `NULL`;
- отрицательные суммы;
- некорректные коды валют;
- дубликаты email.

Они плохо подходят для:

- «нельзя снять больше баланса»;
- «нельзя отменить оплаченный заказ»;
- «только владелец компании может редактировать»;
- сложных бизнес-правил.

---

# 8. Что делать с `readonly` в entity?

## VO — можно

```php
final readonly class Money
{
    public int $cents;
    public string $currencyCode;
}
```

## Entity — обычно нельзя

```php
#[ORM\Entity]
final class Invoice
{
    #[ORM\Column]
    private readonly ?int $amountCents = null; // плохая идея
}
```

Потому что Doctrine hydrate’ит объект после создания и пытается записать в поле.

## Практическое правило

- **VO:** `final readonly class`;
- **Entity:** обычные private-поля, default `null`, getter’ы/setter’ы или factory;
- **Domain object:** может быть immutable, если вы его создаёте сами.

---

# 9. Что делать с change tracking?

Если VO хранится как объект в entity:

```php
#[ORM\Column(type: 'money')]
private ?Money $amount = null;
```

то можно получить сюрпризы:

- сравнение объектов;
- identity vs equality;
- proxy/lazy loading;
- serialization;
- cache.

Если VO хранится как скаляр:

```php
#[ORM\Column]
private ?int $amountCents = null;
```

change tracking предсказуем:

```php
100 -> 200
```

Поэтому для критичных сущностей я почти всегда выбираю **scalar columns**.

---

# 10. Сравнение подходов

| Подход | Когда использовать | Плюсы | Минусы |
|---|---|---|---|
| Primitive columns + getter | Дефолт для `Money`, `Period`, `Name`, `Email` | Индексы, запросы, миграции, change tracking | Entity может быть временно невалидной |
| Custom DBAL Type | Однополевые VO: `Email`, `Url`, `Slug`, `ApiKey` | Объектный доступ в коде, скаляр в БД | Не подходит для составных VO; hydration может падать на мусоре |
| Embeddable | Составные VO: `Money`, `Address`, `Period` | Несколько колонок, индексы, DQL | Временное невалидное состояние, маппинг сложнее |
| Domain model + repository | Строгий DDD, сложные инварианты | Истинная immutability, чистый домен | Две модели, больше кода, сложнее lifecycle |
| JSONB | Динамические metadata | Гибкость | Плохо для запросов/индексов/инвариантов |
| DB constraints | Всегда, как поддержка | Защита от ручных SQL/багов | Не заменяет бизнес-логику |

---

# 11. Типичные антипаттерны

## 1. `readonly` в entity

```php
#[ORM\Entity]
class Order
{
    #[ORM\Column]
    private readonly ?Money $amount = null;
}
```

Риск: Doctrine не сможет hydrate.

---

## 2. Публичные setter’ы на VO

```php
class Money
{
    public function setCents(int $cents): void
    {
        $this->cents = $cents;
    }
}
```

Это превращает VO в mutable DTO.

---

## 3. Хранить составной VO как JSON, когда нужны запросы

```php
#[ORM\Column(type: 'json')]
private array $amount = ['cents' => 100, 'currency' => 'EUR'];
```

Потом нужно:

```sql
SELECT ... WHERE amount->>'currency' = 'EUR';
```

и индексы, и агрегации становятся болью.

---

## 4. Делать VO entity

```php
#[ORM\Entity]
class Money
{
    // ...
}
```

Value Object не должен иметь identity.  
Если он стал entity — скорее всего, это уже Aggregate или Entity.

---

## 5. Полагаться только на `Validator`

Symfony Validator — это валидация входных данных.

Он не должен быть единственным гарантом состояния БД.

Правильный стек:

1. VO constructor;
2. domain method;
3. repository/service;
4. DB constraints;
5. optional: Symfony Validator для DTO/API.

---

## 6. Создавать VO только при выводе в API

```php
class InvoiceDto
{
    public Money $amount;
}
```

а в домене всё равно:

```php
$invoice->amountCents = 100;
$invoice->currencyCode = 'eur';
```

Тогда VO — только фасад. Инварианты не защищаются.

---

# 12. Рекомендуемая архитектура

## Для 80% случаев

```text
Immutable VO
    Money, Email, Period, Address

Entity
    хранит примитивы: int, string, datetime

Getter
    строит VO

Setter / domain method
    принимает VO и раскладывает в примитивы

DB constraints
    защищает от мусора

Repository
    не позволяет persist’ить незаконченное состояние
```

## Для сложных доменных объектов

```text
Domain Model
    immutable Order, Invoice, Account

Repository
    маппит domain model ↔ storage model

Doctrine Entity
    только persistence

DB constraints
    защита от прямого SQL
```

---

# 13. Пример полного решения: `Invoice`

## VO

```php
final readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currencyCode,
    ) {
        if ($cents < 0) {
            throw new DomainException('Money cents must be non-negative');
        }

        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw new DomainException('Invalid currency code');
        }
    }
}
```

## Entity

```php
#[ORM\Entity]
#[ORM\Index(columns: ['currencyCode', 'amountCents'])]
final class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amountCents = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCode = null;

    public function amount(): Money
    {
        if ($this->amountCents === null || $this->currencyCode === null) {
            throw new LogicException('Invoice amount is not initialized');
        }

        return new Money($this->amountCents, $this->currencyCode);
    }

    public function setAmount(Money $money): void
    {
        $this->amountCents = $money->cents;
        $this->currencyCode = $money->currencyCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
```

## Factory / service

```php
final class InvoiceFactory
{
    public function create(Money $amount): Invoice
    {
        $invoice = new Invoice();
        $invoice->setAmount($amount);
        return $invoice;
    }
}
```

## DB constraint

```sql
ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_amount_cents CHECK (amount_cents >= 0);

ALTER TABLE invoices
  ADD CONSTRAINT chk_invoice_currency_code CHECK (currency_code ~ '^[A-Z]{3}$');
```

## Использование

```php
$money = new Money(1990, 'EUR');
$invoice = $factory->create($money);

$em->persist($invoice);
$em->flush();
```

---

# 14. Какой способ выбрать?

## Выберите primitive columns + getter, если:

- VO состоит из 1–5 полей;
- поля нужны для запросов;
- нужны индексы;
- важна миграция;
- нужен предсказуемый change tracking.

**Пример:**

```php
Money: int cents + string currencyCode
Period: datetime start + datetime end
Name: string firstName + string lastName
Address: string street + string city + string zip
```

---

## Выберите custom Type, если:

- VO однополевое;
- строка/число в БД;
- важно, чтобы в PHP сразу был объект.

**Пример:**

```php
Email
Url
Slug
PhoneNumber
ApiKey
```

---

## Выберите embeddable, если:

- составной VO;
- нужно несколько колонок;
- нужны запросы по вложенным полям.

**Пример:**

```php
Money
Coordinates
Period
```

---

## Выберите domain model + repository, если:

- сложная бизнес-логика;
- много инвариантов;
- домен должен быть независимо от persistence;
- команда работает в DDD-стиле.

**Пример:**

```php
Invoice
Account
Subscription
Project
Order
```

---

## Выберите JSONB, если:

- структура часто меняется;
- это metadata;
- запросы по этим полям редкие;
- индексы не критичны.

**Пример:**

```php
UserProfile
Metadata
Settings
```

---

# 15. Итоговая рекомендация

Если нужно гарантировать:

1. **immutable VO**;
2. **всегда валидное состояние**;
3. **удобные запросы и индексы**;

то лучший баланс:

```text
Домен:
    final readonly class Money

Persistence:
    Invoice.amountCents INT
    Invoice.currencyCode VARCHAR(3)

Граница чтения:
    Invoice::amount(): Money

Граница записи:
    Invoice::setAmount(Money $money): void

Защита БД:
    CHECK constraints
```

Если домен сложный и нужно полностью изолировать бизнес-логику от ORM — делайте:

```text
Doctrine Entity = storage DTO
Domain Object = immutable aggregate
Repository = mapper
```

Но для большинства Symfony-проектов это избыточно. Достаточно хранить примитивы, строить immutable VO на выходе и защищать данные DB constraints.

> ⏱ 137.3s · prompt 31983 tok · output 14663 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

The user asks about how to organize persistence of a domain aggregate with a root and child entities through Doctrine: collection mapping, transaction boundaries, and who/when calls flush. They want a concrete example.

Let me think about what to cover:

1. Aggregate structure: root + children (I'll pick a good example — maybe `Order` with `OrderItems`, or `Invoice` with `LineItems`, or `Project` with `Tasks`)

2. Key Doctrine concerns:
   - How to map the collection (OneToMany with owning side)
   - Cascade persistence
   - Orphan removal
   - The "unit of work" boundary
   - Where flush should be called
   - Transaction boundaries
   - Lazy vs eager loading
   - The problem of detached entities and identity map

3. Common pitfalls:
   - flush inside aggregate methods (bad)
   - Multiple flushes in one request
   - Cascade vs manual persist
   - OrphanRemoval vs explicit deletion
   - Collection modification vs replacement
   - N+1 on collections
   - Who owns the EM lifecycle

Let me pick `Invoice` with `InvoiceLine` as the example. It's clear, has a root (Invoice) and children (Lines), and has good business rules (total = sum of lines, status transitions, etc.)

Structure:
1. Domain model (aggregate root + children + VO)
2. Doctrine mapping (the key decisions)
3. Repository (persistence boundary)
4. Transaction boundaries (who flushes)
5. Anti-patterns and trade-offs

Let me be thorough and practical, showing real code with comments about WHY decisions are made.

</details>

**Кандидат:**

## Хранение агрегата в Doctrine: Invoice + InvoiceLine

### 1. Доменная модель

```php
// ─── VO ─────────────────────────────────────────────────────
final readonly class Money
{
    public function __construct(
        public int    $cents,
        public string $currencyCode,
    ) {
        if ($cents < 0) {
            throw new \DomainException('Amount must be non-negative');
        }
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw new \DomainException('Invalid currency');
        }
    }

    public function add(self $other): self
    {
        if ($this->currencyCode !== $other->currencyCode) {
            throw new \DomainException('Currency mismatch');
        }
        return new self($this->cents + $other->cents, $this->currencyCode);
    }
}

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Issued    = 'issued';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';
}

// ─── Child entity (управляется корнем) ──────────────────────
final class InvoiceLine
{
    private int $productId = 0;
    private int $quantity  = 0;
    private Money $unitPrice;
    private ?int $id = null;

    public function __construct(int $productId, int $quantity, Money $unitPrice)
    {
        if ($quantity < 1) {
            throw new \DomainException('Quantity must be >= 1');
        }
        $this->productId = $productId;
        $this->quantity  = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function total(): Money
    {
        return new Money($this->unitPrice->cents * $this->quantity, $this->unitPrice->currencyCode);
    }

    // Getters...
    public function getId(): ?int { return $this->id; }
    public function getProductId(): int { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): Money { return $this->unitPrice; }

    // Internal setter — только корень может менять
    public function changeQuantity(int $qty): void
    {
        if ($qty < 1) {
            throw new \DomainException('Quantity must be >= 1');
        }
        $this->quantity = $qty;
    }
}

// ─── Aggregate Root ─────────────────────────────────────────
final class Invoice
{
    private ?int $id = null;
    private string $customerId = '';
    private Money $currency;
    private InvoiceStatus $status = InvoiceStatus::Draft;
    private \DateTimeImmutable $createdAt;

    /** @var InvoiceLine[] */
    private array $lines = [];

    public function __construct(string $customerId, Money $currency)
    {
        $this->customerId = $customerId;
        $this->currency   = $currency;
        $this->createdAt  = new \DateTimeImmutable();
    }

    // ─── Инвариант: total = сумма строк ─────────────────────

    public function addLine(InvoiceLine $line): void
    {
        if ($this->status !== InvoiceStatus::Draft) {
            throw new \DomainException('Cannot add line to non-draft invoice');
        }
        if ($line->getUnitPrice()->currencyCode !== $this->currency->currencyCode) {
            throw new \DomainException('Currency mismatch');
        }
        $this->lines[] = $line;
    }

    public function removeLine(InvoiceLine $line): void
    {
        if ($this->status !== InvoiceStatus::Draft) {
            throw new \DomainException('Cannot remove line from non-draft invoice');
        }
        $this->lines = array_filter($this->lines, fn($l) => $l !== $line);
        $this->lines = array_values($this->lines);
    }

    public function total(): Money
    {
        $total = new Money(0, $this->currency->currencyCode);
        foreach ($this->lines as $line) {
            $total = $total->add($line->total());
        }
        return $total;
    }

    public function issue(): void
    {
        if ($this->status !== InvoiceStatus::Draft) {
            throw new \DomainException('Only draft invoices can be issued');
        }
        if (count($this->lines) === 0) {
            throw new \DomainException('Cannot issue empty invoice');
        }
        $this->status = InvoiceStatus::Issued;
    }

    public function markAsPaid(): void
    {
        if ($this->status !== InvoiceStatus::Issued) {
            throw new \DomainException('Only issued invoices can be marked paid');
        }
        $this->status = InvoiceStatus::Paid;
    }

    public function cancel(): void
    {
        if ($this->status === InvoiceStatus::Paid) {
            throw new \DomainException('Cannot cancel paid invoice');
        }
        $this->status = InvoiceStatus::Cancelled;
    }

    // Getters...
    public function getId(): ?int { return $this->id; }
    public function getLines(): array { return $this->lines; }
    public function getStatus(): InvoiceStatus { return $this->status; }
}
```

**Ключевой принцип:** дочерние сущности (`InvoiceLine`) не имеют самостоятельного API для бизнес-операций. Их состояние меняется только через корень (`Invoice`). Это и есть граница агрегата.

---

### 2. Doctrine-маппинг

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'invoices')]
final class InvoiceEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $customerId = '';

    #[ORM\Column(type: 'integer')]
    private int $currencyCode = 0; // храним ISO-код, для простоты

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'draft';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity: InvoiceLineEntity::class,
     *     mappedBy: "invoice",
     *     cascade={"persist", "remove"},
     *     orphanRemoval: true
     * )
     * @ORM\JoinColumn(onDelete: "CASCADE")
     */
    #[ORM\OneToMany(
        targetEntity: InvoiceLineEntity::class,
        mappedBy: 'invoice',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private \Doctrine\Common\Collections\Collection $lines;

    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // Маппинг ↔ доменная модель
    public function toDomain(): Invoice
    {
        $invoice = new Invoice(
            customerId: $this->customerId,
            currency: new Money(0, $this->currencyCode),
        );
        // Инварианты проверяются при добавлении
        foreach ($this->lines as $lineEntity) {
            $invoice->addLine($lineEntity->toDomain());
        }
        // Статус и total — через доменные методы
        return $invoice;
    }

    public static function fromDomain(Invoice $invoice): self
    {
        $entity = new self();
        $entity->customerId = $invoice->getCustomerId();
        $entity->currencyCode = $invoice->getCurrency()->currencyCode;
        $entity->status = $invoice->getStatus()->value;
        $entity->createdAt = $invoice->getCreatedAt();

        foreach ($invoice->getLines() as $line) {
            $entity->addLine(InvoiceLineEntity::fromDomain($line));
        }

        return $entity;
    }

    public function addLine(InvoiceLineEntity $line): void
    {
        $line->setInvoice($this);
        $this->lines[] = $line;
    }

    public function removeLine(InvoiceLineEntity $line): void
    {
        $this->lines->removeElement($line);
    }

    // Getters/setters для Doctrine...
}
```

```php
#[ORM\Entity]
#[ORM\Table(name: 'invoice_lines')]
final class InvoiceLineEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InvoiceEntity $invoice = null;

    #[ORM\Column(type: 'integer')]
    private int $productId = 0;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    #[ORM\Column(type: 'integer')]
    private int $unitPriceCents = 0;

    // Маппинг ↔ доменная модель
    public function toDomain(): InvoiceLine
    {
        return new InvoiceLine(
            productId: $this->productId,
            quantity:  $this->quantity,
            unitPrice: new Money($this->unitPriceCents, 'EUR'),
        );
    }

    public static function fromDomain(InvoiceLine $line): self
    {
        $entity = new self();
        $entity->productId = $line->getProductId();
        $entity->quantity  = $line->getQuantity();
        $entity->unitPriceCents = $line->getUnitPrice()->cents;
        return $entity;
    }

    public function setInvoice(InvoiceEntity $invoice): void
    {
        $this->invoice = $invoice;
    }

    // Getters...
}
```

---

### 3. Ключевые решения маппинга

#### Cascade: `persist` + `remove`

```php
cascade: ['persist', 'remove'],
```

**Что это значит:**

| Действие | Эффект |
|----------|--------|
| `$em->persist($invoiceEntity)` | Автоматически persist'ятся все `lines` |
| `$em->remove($invoiceEntity)` | Автоматически remove'ятся все `lines` |

**Почему не `merge`:** `merge` создаёт «детached» дубликаты в identity map. При работе с агрегатом это опасно.

**Почему не `refresh`:** `refresh` перезаписывает состояние из БД. Не нужно при persist.

#### OrphanRemoval: `true`

```php
orphanRemoval: true,
```

**Что это значит:**

```php
// Удалили line из коллекции корня
$invoiceEntity->getLines()->removeElement($lineEntity);

// При flush() Doctrine:
// 1. Видит, что line больше не принадлежит ни одному invoice
// 2. Удаляет строку из БД
```

**Когда работает:**
- Удалили элемент из коллекции → `DELETE FROM invoice_lines WHERE id = X`.

**Когда НЕ работает:**
- Line привязан к другому invoice (не orphan) → не удаляется.
- Line detached, но не был orphan → не удаляется.

**Почему важно для агрегата:** дочерние сущности не должны жить без корня. `orphanRemoval: true` гарантирует, что «осиротевшая» строка не останется в БД.

#### `mappedBy` (owning side на дочерней)

```php
// В InvoiceLineEntity:
#[ORM\ManyToOne(inversedBy: 'lines')]
private ?InvoiceEntity $invoice = null;

// В InvoiceEntity:
#[ORM\OneToMany(mappedBy: 'invoice', ...)]
private Collection $lines;
```

**Что это значит:**

- **Owning side** = `InvoiceLineEntity` (хранит FK `invoice_id`).
- **Inverse side** = `InvoiceEntity` (коллекция, не хранит FK).
- `flush()` генерирует `INSERT INTO invoice_lines (invoice_id, ...)` на основе owning side.
- Изменение коллекции в inverse side **не генерирует SQL** само по себе. SQL приходит от owning side.

**Практическое следствие:**

```php
// ✅ Работает:
$lineEntity = new InvoiceLineEntity();
$lineEntity->setInvoice($invoiceEntity);  // ← owning side задан
$invoiceEntity->addLine($lineEntity);     // ← inverse side обновлён
$em->persist($invoiceEntity);
$em->flush();  // → INSERT INTO invoice_lines (invoice_id, ...)

// ❌ НЕ работает (не создаст связь в БД):
$lineEntity = new InvoiceLineEntity();
$invoiceEntity->addLine($lineEntity);     // ← только inverse side
$em->persist($lineEntity);
$em->flush();  // → INSERT INTO invoice_lines (invoice_id = NULL!)
```

---

### 4. Repository: граница персистентности

```php
final class InvoiceRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    // ─── Read ────────────────────────────────────────────────

    public function find(int $id): Invoice
    {
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->find($id);

        if ($entity === null) {
            throw new InvoiceNotFoundException($id);
        }

        return $entity->toDomain();
    }

    /**
     * Загрузка с линиями (eager)
     */
    public function findWithLines(int $id): Invoice
    {
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->createQueryBuilder('i')
            ->leftJoin('i.lines', 'l')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity === null) {
            throw new InvoiceNotFoundException($id);
        }

        return $entity->toDomain();
    }

    // ─── Write ───────────────────────────────────────────────

    public function save(Invoice $invoice): void
    {
        // Привязываем к существующей entity, если есть
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->findOneBy(['customerId' => $invoice->getCustomerId()])
            ?? InvoiceEntity::fromDomain($invoice);

        // Синхронизируем линии
        // Удаляем старые, которых нет в доменной модели
        $domainLineIds = array_map(
            fn(InvoiceLine $l) => $l->getId(),
            $invoice->getLines()
        );

        foreach ($entity->getLines() as $existing) {
            if (!in_array($existing->getId(), $domainLineIds)) {
                $entity->removeLine($existing);
            }
        }

        // Добавляем новые
        $existingIds = array_map(
            fn(InvoiceLineEntity $l) => $l->getId(),
            $entity->getLines()->toArray()
        );

        foreach ($invoice->getLines() as $domainLine) {
            if (!in_array($domainLine->getId(), $existingIds)) {
                $entity->addLine(InvoiceLineEntity::fromDomain($domainLine));
            }
        }

        $this->em->persist($entity);
        // flush НЕ вызывается здесь — см. раздел 5
    }
}
```

---

### 5. Транзакции: кто и когда вызывает `flush`

#### Правило

> **`flush()` вызывается ровно один раз в обработчике запроса (или use case).**
> **Никогда — внутри aggregate, repository, или service.**

#### Почему

```php
// ❌ flush внутри repository
public function save(Invoice $invoice): void
{
    // ...
    $this->em->persist($entity);
    $this->em->flush();  // ← НЕТ. Это нарушает атомарность.
}

// ❌ flush внутри aggregate
public function addLine(InvoiceLine $line): void
{
    $this->lines[] = $line;
    $this->em->flush();  // ← НЕТ. Агрегат не знает про EM.
}
```

**Почему плохо:**

1. **Потеря атомарности.** Если после `flush()` что-то падает — часть данных уже в БД.
2. **Несколько `flush`** = несколько SQL-транзакций. Медленнее, сложнее откат.
3. **Identity map** становится непредсказуемым: entity может быть detached между flush'ами.
4. **Тестируемость:** repository с `flush()` внутри требует mock'а EntityManager.

#### Где вызывается `flush`

```php
// Use case / application service — единая точка
final class CreateInvoiceUseCase
{
    public function __construct(
        private InvoiceRepository $repo,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $events,
    ) {}

    public function execute(CreateInvoiceCommand $cmd): Invoice
    {
        // 1. Доменная логика (без БД)
        $invoice = Invoice::create($cmd->customerId, $cmd->currency);
        foreach ($cmd->lines as $lineData) {
            $invoice->addLine(new InvoiceLine(...));
        }
        $invoice->issue();

        // 2. Персистентность
        $this->repo->save($invoice);

        // 3. События (после flush, чтобы не ссылаться на несуществующий ID)
        $this->em->flush();  // ← ЕДИНСТВЕННЫЙ flush

        $this->events->dispatch(new InvoiceCreatedEvent($invoice->getId()));

        return $invoice;
    }
}
```

#### Почему `flush` после `repo->save`, а не до

```php
// Порядок:
// 1. $em->persist($entity)    → entity в identity map, SQL не выполнен
// 2. $em->flush()             → SQL выполнен, ID сгенерирован
// 3. dispatch events          → события содержат валидный ID
```

Если `flush` до `persist` — нет смысла. Если события до `flush` — в событии `getId()` может быть `null`.

#### Транзакция

Symfony по умолчанию оборачивает каждый запрос в транзакцию:

```php
// В Symfony 5+:
// Kernel::handle() → Transactional middleware
//   BEGIN
//   ... controller / use case ...
//   COMMIT / ROLLBACK
```

Если нужен **явный** контроль:

```php
final class CreateInvoiceUseCase
{
    public function execute(CreateInvoiceCommand $cmd): Invoice
    {
        $this->em->transactional(function () use ($cmd) {
            $invoice = Invoice::create(...);
            // ...
            $this->repo->save($invoice);
            $this->em->flush();
        });
        // COMMIT здесь. Если что-то упало — ROLLBACK.
    }
}
```

`$em->transactional(fn)` = `BEGIN → fn() → COMMIT` или `BEGIN → fn() → ROLLBACK`.

---

### 6. Gраница транзакции: что входит, что нет

```
┌─────────────────────────────────────────────────────────────┐
│  TRANSACTION                                                 │
│                                                             │
│  1. Domain logic (чистый PHP, без БД)                       │
│     - Invoice::addLine()                                    │
│     - Invoice::issue()                                      │
│     - Инварианты проверяются                                  │
│                                                             │
│  2. Persistence (SQL)                                       │
│     - INSERT INTO invoices                                  │
│     - INSERT INTO invoice_lines                             │
│     - UPDATE invoices (status)                              │
│                                                             │
│  3. События (sync, в той же транзакции)                     │
│     - InvoiceCreatedEvent → listener → аналитика/БД         │
│                                                             │
│  COMMIT                                                     │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  ПОСЛЕ TRANSACTIONS (асинхронно)                             │
│                                                             │
│  - Email (Messenger → worker)                               │
│  - Search index (CDC / worker)                              │
│  - Notification (worker)                                    │
└─────────────────────────────────────────────────────────────┘
```

**Принцип:** то, что **должно быть атомарным** с записью в БД — внутри транзакции. То, что **может быть retry'нуто** — снаружи.

---

### 7. Антипаттерны

#### ❌ 1. Flush в каждом репозитории

```php
// InvoiceRepository
public function save(Invoice $i): void { $em->persist(...); $em->flush(); }

// LineRepository
public function save(InvoiceLine $l): void { $em->persist(...); $em->flush(); }

// Use case
$lineRepo->save($line);    // flush #1
$invoiceRepo->save($inv);  // flush #2
// Между ними может упасть → inconsistency
```

#### ❌ 2. Мультипликация через `merge`

```php
$entity = $em->merge(InvoiceEntity::fromDomain($invoice));
// merge создаёт НОВЫЙ экземпляр в identity map
// Старый (из find) — detached, но ещё в памяти
// Два объекта = одна строка = хаос
```

#### ❌ 3. Изменение inverse side без owning side

```php
// Только inverse side:
$invoiceEntity->getLines()->add($lineEntity);
// $lineEntity->invoice — не задан
// flush() → INSERT INTO invoice_lines (invoice_id = NULL)
```

#### ❌ 4. Lazy loading в цикле

```php
$invoices = $repo->findAll();
foreach ($invoices as $inv) {
    $lines = $inv->getLines();  // ← N+1! Каждый access = SELECT
}
```

**Решение:** eager loading через DQL или `addFetch('JOIN FETCH')`.

```php
$qb->select('i')
   ->leftJoin('i.lines', 'l')
   ->addSelect('partial l.{id, productId, quantity, unitPriceCents}')
   ->setMaxResults(50);
```

#### ❌ 5. `orphanRemoval: true` + явный `remove()`

```php
// orphanRemoval уже удалит при flush()
$invoice->getLines()->removeElement($line);

// И ещё явно:
$em->remove($line);  // ← лишнее, но не вредит (idempotent)

// Опасно, если:
$em->remove($line);
// line был перемещён в другой invoice
// orphanRemoval не сработает (не orphan), но remove() уже запланирован
// → DELETE + вставлен в другую коллекцию = противоречие
```

---

### 8. Итоговая схема

```
┌──────────────────────────────────────────────────────────────────────┐
│  USE CASE / APPLICATION SERVICE                                      │
│                                                                      │
│  1. Build/modify aggregate (чистый PHP, инварианты)                 │
│  2. $repo->save($aggregate)  →  $em->persist($entity)               │
│  3. $em->flush()            →  SQL (INSERT/UPDATE/DELETE)           │
│  4. $em->transactional()    →  COMMIT / ROLLBACK                    │
│  5. Dispatch events         →  асинхронные эффекты                  │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────────────┐
│  REPOSITORY                                                         │
│                                                                      │
│  - Маппинг domain ↔ entity                                         │
│  - Persist / remove (без flush)                                     │
│  - Запросы (DQL / QueryBuilder)                                     │
│  - Не знает про транзакции                                          │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────────────┐
│  DOCTRINE ENTITY                                                    │
│                                                                      │
│  - Маппинг колонок                                                  │
│  - Owning side (FK)                                                 │
│  - Cascade: persist, remove                                         │
│  - OrphanRemoval: true                                              │
│  - Не содержит бизнес-логику                                        │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────────────┐
│  DATABASE                                                           │
│                                                                      │
│  invoices:    id, customer_id, currency, status, created_at         │
│  invoice_lines: id, invoice_id (FK), product_id, qty, price_cents   │
│                                                                      │
│  CONSTRAINTS:                                                       │
│    - invoice_id NOT NULL                                            │
│    - qty >= 1                                                       │
│    - price_cents >= 0                                               │
│    - ON DELETE CASCADE (invoice_id)                                 │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

**Правило одного flush:**

> В одном use case — один `flush()`.  
> В одном запросе — одна транзакция.  
> Агрегат — один unit of work.

> ⏱ 49.2s · prompt 38751 tok · output 6344 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про диагностику и решение N+1 в Doctrine. Это практический вопрос, который требует:
1. Конкретные методы диагностики (как поймать в проде/деве)
2. Способы решения (JOIN FETCH, EntityGraph, DQL, Batch, custom query, денормализация)
3. Trade-offs каждого способа

Нужно дать практичный ответ с кодом, а не теоретический. Начну с диагностики (это самое важное — многие сразу идут "лечить", не поняв где именно проблема), потом решения от простого к сложному.

Структура:
1. Диагностика (log, profiler, EXPLAIN)
2. Как выглядит N+1 в SQL логе
3. Способы решения:
   - JOIN FETCH (самый частый)
   - EntityGraph
   - DQL with partial object
   - Batch size (setFirstResult/setMaxResults + pagination)
   - Denormalization
   - Custom query
   - Cache (second level)
4. Trade-offs таблица
5. Когда N+1 — не проблема

Буду конкретный, с кодом. Не буду перегружать — сфокусируюсь на практике.

</details>

**Кандидат:**

## N+1 в Doctrine: диагностика и решения

### 1. Диагностика

#### Шаг 1: Подтвердить, что это именно N+1

Симптом: страница рендерится за 2–5 сек, при этом:
- БД не нагружена (CPU < 20%)
- Каждый запрос по 1–3ms
- Но их **сотни** за один рендер

#### Шаг 2: Включить SQL-лог

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            sql:
                enabled: '%kernel.debug%'
```

```yaml
# config/packages/monolog.yaml
monolog:
    channels: [doctrine]
    handlers:
        doctrine_sql:
            type: stream
            path: "%kernel.logs_dir%/doctrine_sql.log"
            level: debug
            channels: [doctrine]
```

**Что искать в логе:**

```sql
-- 1 запрос:
SELECT i.* FROM invoices i LIMIT 50

-- 50 запросов (N+1):
SELECT l.* FROM invoice_lines l WHERE l.invoice_id = 1
SELECT l.* FROM invoice_lines l WHERE l.invoice_id = 2
SELECT l.* FROM invoice_lines l WHERE l.invoice_id = 3
...
SELECT l.* FROM invoice_lines l WHERE l.invoice_id = 50
```

**Признак N+1:** один и тот же SQL, только с разным `WHERE` параметром, повторяется N раз.

#### Шаг 3: Symfony Profiler

```
http://localhost/_profiler
```

Вкладка **DBAL** → показывает все запросы с длительностью.

Если видите 50 запросов по 2ms = 100ms суммарно, и каждый — тот же `SELECT ... WHERE invoice_id = X` — это N+1.

#### Шаг 4: Подсчёт запросов

```php
// В контроллере или use case
use Doctrine\ORM\Tools\Pagination\Paginator;

$em = $this->container->get('doctrine')->getManager();
$logger = $em->getConnection()->getEventManager();

// Простой счётчик через event listener
final class QueryCounter
{
    private int $count = 0;

    public function onQueryExecute(\DBAL\Event\PreEventArgs $event): void
    {
        $this->count++;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
```

```yaml
# services.yaml
services:
    App\Debug\QueryCounter:
        tags:
            - { name: doctrine.event_listener, event: onQueryExecute }
```

#### Шаг 5: Где именно происходит lazy loading

```php
// Типовой триггер N+1:
$invoices = $repo->findBy([], ['createdAt' => 'DESC'], 50);

foreach ($invoices as $invoice) {
    // ← Здесь каждый getLines() = отдельный SELECT
    $lines = $invoice->getLines();
    $total = 0;
    foreach ($lines as $line) {
        $total += $line->getTotal();
    }
}
```

**Правило:** N+1 возникает, когда **lazy-loaded коллекция** (или ManyToOne) дёргается в цикле.

---

### 2. Как выглядит N+1 на практике

```php
// ❌ N+1: 1 + 50 = 51 запрос
$invoices = $em->getRepository(Invoice::class)
    ->findBy([], ['createdAt' => 'DESC'], 50);

foreach ($invoices as $invoice) {
    $lines = $invoice->getLines();  // ← SELECT × 50
}
```

```php
// ❌ N+1: 1 + 50 + 50 = 101 запрос (два уровня)
$invoices = $em->getRepository(Invoice::class)
    ->findBy([], ['createdAt' => 'DESC'], 50);

foreach ($invoices as $invoice) {
    $lines = $invoice->getLines();  // ← SELECT × 50
    foreach ($lines as $line) {
        $product = $line->getProduct();  // ← SELECT × 50 (если тоже lazy)
    }
}
```

---

### 3. Способы решения

#### Способ 1: `JOIN FETCH` (самый частый)

```php
// ✅ 1 запрос вместо 51
$qb = $em->getRepository(Invoice::class)->createQueryBuilder('i');
$qb->select('i')
   ->leftJoin('i.lines', 'l')
   ->addSelect('PARTIAL l.{id, productId, quantity, unitPriceCents}')
   ->orderBy('i.createdAt', 'DESC')
   ->setMaxResults(50);

$invoices = $qb->getQuery()->getResult();

foreach ($invoices as $invoice) {
    $lines = $invoice->getLines();  // ← уже в памяти, 0 запросов
}
```

**Что делает `PARTIAL`:**
- Загружает **только перечисленные** поля дочерней сущности.
- Остальные поля останутся `null` / неинициализированными.
- Если потом дёрнете `->getCreatedAt()` на line — снова lazy load.

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| 1 запрос вместо N+1 | SQL-запрос становится тяжелее (JOIN) |
| Не нужен новый сервис/репозиторий | `PARTIAL` — нужно перечислить все поля, которые используете |
| Работает с QueryBuilder | Если поля не в `PARTIAL` — N+1 возвращается при доступе |
| Простой синтаксис | Не масштабируется на 3+ уровня (см. ниже) |

**Когда не работает:**
- 3+ уровня вложенности (JOIN FETCH на 3 уровня = очень тяжёлый JOIN)
- Динамическое количество полей (не знаете заранее, какие поля нужны)

---

#### Способ 2: `EntityGraph`

```php
// ✅ Альтернатива JOIN FETCH
$rootClass = Invoice::class;

$graph = $em->getReference($rootClass, null) // dummy
    ->createEntityGraph()
    ->addAssociation('lines');

$graph->addAssociation('lines.product');  // два уровня

$invoices = $em
    ->createQueryBuilder()
    ->select('i', 'FROM App\Entity\Invoice i')
    ->setEntityGraph($graph)
    ->setMaxResults(50)
    ->getQuery()
    ->getResult();
```

Или через `loadClass` (Symfony 6.3+):

```php
$em->loadClass(Invoice::class, function (EntityLifecycleEventArgs $event) {
    // не очень удобный API
});
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Не нужно перечислять поля (в отличие от PARTIAL) | API менее удобный, чем JOIN FETCH |
| Можно строить динамически | В некоторых версиях Doctrine — баги с EntityGraph + pagination |
| Хорош для 2–3 уровней | Не заменяет PARTIAL для тяжёлых таблиц |

---

#### Способ 3: Денормализация (хот-поле в родительской сущности)

```php
// Храним total напрямую в invoice
#[ORM\Column(type: 'integer')]
private int $totalCents = 0;

#[ORM\Column(type: 'string', length: 3)]
private string $currencyCode = 'EUR';

// При создании/изменении линий:
public function recalculateTotal(): void
{
    $total = 0;
    foreach ($this->lines as $line) {
        $total += $line->getUnitPrice()->cents * $line->getQuantity();
    }
    $this->totalCents = $total;
}
```

```sql
-- Теперь список invoices не требует JOIN:
SELECT id, total_cents, currency_code FROM invoices ORDER BY created_at DESC LIMIT 50;
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| 0 JOIN'ов для списка | Дублирование данных (total в invoice И в lines) |
| Быстрый SELECT | Нужно поддерживать инвариант (recalculate на каждом изменении) |
| Масштабируется до 10k строк | Если total считается сложной формулой — денормализация хрупкая |
| | Миграция: нужно пересчитать для всех существующих строк |

**Когда использовать:** поле используется **всегда** в списке, а строки — редко (read-heavy).

---

#### Способ 4: Pagination + ограничение N

N+1 не лечит, но **ограничивает вред**:

```php
// 50 вместо 5000
$qb->setMaxResults(50);  // N = 50, а не 5000
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Простой, не требует рефакторинга | N+1 всё ещё есть, просто N меньше |
| Не меняет схему | 50 × 2ms = 100ms — всё ещё медленно |
| | Не масштабируется: при N = 200 — 400ms |

**Когда использовать:** временное решение, пока не найдёшь root cause.

---

#### Способ 5: Custom query (репозиторий с raw SQL)

```php
final class InvoiceRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findWithTotals(int $limit = 50): array
    {
        $sql = "
            SELECT i.id, i.customer_id, i.status, i.created_at,
                   COALESCE(SUM(l.quantity * l.unit_price_cents), 0) AS total_cents,
                   'EUR' AS currency_code
            FROM invoices i
            LEFT JOIN invoice_lines l ON l.invoice_id = i.id
            WHERE i.status = 'issued'
            GROUP BY i.id
            ORDER BY i.created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue('limit', $limit);

        $rows = $stmt->fetchAllAssociative();

        return array_map(fn($row) => [
            'id'         => (int) $row['id'],
            'totalCents' => (int) $row['total_cents'],
            'status'     => $row['status'],
        ], $rows);
    }
}
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Полный контроль над SQL | Обходите ORM — нет identity map, нет lazy loading |
| Можно использовать CTE, window functions | Нужно самостоятельно маппить в объекты |
| Быстрее JOIN FETCH на больших объёмах | Две модели (entity + DTO) — сложность |
| | Миграции не помогают (нужно вручную менять SQL) |
| | Труднее поддерживать |

**Когда использовать:** очень сложные отчёты, аналитические запросы, когда ORM создаёт слишком тяжелый JOIN.

---

#### Способ 6: Второй уровень кэша (Second-Level Cache)

```yaml
doctrine:
    orm:
        second_level_cache:
            enabled: true
            region: default
```

```php
#[ORM\Entity]
#[ORM\Cache]
class Product
{
    // Продукт не меняется часто → кэшируем
}
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Убирает N+1 для неизменяемых сущностей | Кэш может быть неактуальным |
| Прозрачный (нет изменения кода) | Сложность настройки (TTL, invalidation) |
| | Не работает для сущностей, которые часто меняются |
| | Дополнительная инфраструктура (Redis, APCu) |

**Когда использовать:** справочные данные (страны, валюты, категории), которые меняются редко.

---

#### Способ 7: Eager loading в маппинге (крайний случай)

```php
#[ORM\ManyToOne(fetch: 'EAGER')]
#[ORM\JoinColumn(nullable: false)]
private Product $product;
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Автоматически подгружается | Подгружается **всегда**, даже если не нужен |
| Не нужно думать о JOIN | Удваивает/троит объём данных в запросе |
| | Невозможно выключить без изменения маппинга |
| | Не подходит для коллекций (O(N*M) памяти) |

**Когда использовать:** 1-2 ManyToOne, которые **всегда** нужны. Для коллекций — нет.

---

### 4. Сравнительная таблица

| Способ | Запросы | Сложность | Когда |
|--------|---------|-----------|-------|
| JOIN FETCH + PARTIAL | 1 | Низкая | 1–2 уровня, известные поля |
| EntityGraph | 1 | Средняя | 2–3 уровня, динамические поля |
| Денормализация | 1 | Средняя | Read-heavy, hot-поля |
| Pagination | N (меньше) | Очень низкая | Временное, N < 100 |
| Custom SQL | 1 | Высокая | Сложные отчёты, аналитика |
| SL Cache | 0 (после первого) | Средняя | Референтные данные |
| EAGER fetch | 1 | Очень низкая | 1-2 ManyToOne, всегда нужны |

---

### 5. Пошаговый алгоритм решения

```
1. Подтвердить N+1 (SQL-лог, profiler)
       │
       ▼
2. Определить глубину:
   │
   ├── 1 уровень (1 + N)  → JOIN FETCH + PARTIAL
   │
   ├── 2 уровня (1 + N + N×M)
   │   ├── Поля известны → JOIN FETCH + PARTIAL (два JOIN'а)
   │   └── Поля не известны → EntityGraph
   │
   ├── 3+ уровня
   │   ├── Hot-поле → денормализация
   │   └── Сложный запрос → custom SQL
   │
   └── Референтные данные (не меняются)
       └── SL Cache
```

---

### 6. Частые ошибки при решении

#### ❌ 1. `JOIN FETCH` без `PARTIAL`

```php
$qb->leftJoin('i.lines', 'l')->addSelect('l');
```

Загружает **все** поля всех строк. Для тяжёлой таблицы (`invoice_lines` с 20 колонками) — это 50 × 20 = 1000 полей в памяти, из которых используются 4.

**Правильно:**
```php
->addSelect('PARTIAL l.{id, productId, quantity, unitPriceCents}')
```

#### ❌ 2. `JOIN FETCH` + `setMaxResults`

```php
$qb->leftJoin('i.lines', 'l')
   ->addSelect('l')
   ->setMaxResults(50);
```

**Баг:** Doctrine вернёт 50 строк `invoice`, но из-за JOIN — **50 × (количество линий)** строк. Если у invoice 5 линий — вы получите 250 объектов в памяти, а не 50.

**Решение:**
```php
->setMaxResults(50)  // работает корректно, если PARTIAL
// или
->getQuery()->useQueryCache()  // не помогает
// правильный подход: DQL с DISTINCT или custom query
```

На практике: `setMaxResults` + `JOIN FETCH` + `PARTIAL` в Doctrine 2.x работает корректно. В Doctrine 3.x — тоже. Но если используете `addSelect('l')` без `PARTIAL` — возможны дубликаты.

#### ❌ 3. EAGER на коллекцию

```php
#[ORM\OneToMany(fetch: 'EAGER')]
private Collection $lines;
```

Каждый `Invoice` при загрузке тянет **все** свои lines. Список из 50 invoices с 5 линиями = 250 объектов в памяти. Для списка — это хорошо. Для `find($id)` — тоже. Для **отчёта**, где lines не нужны — нет.

#### ❌ 4. Лечить N+1, которого нет

```sql
-- 1 запрос, 50 строк, 2ms
SELECT i.* FROM invoices i LIMIT 50
```

Если запрос один и быстрый — N+1 нет. Не надо добавлять JOIN FETCH «на всякий случай». Это делает запрос тяжелее без пользы.

---

### 7. Когда N+1 — не проблема

| Ситуация | Почему ок |
|----------|-----------|
| N = 3 (три элемента) | 4 запроса × 1ms = 4ms. Не проблема. |
| Данные в SL cache | Первый раз — N+1. Дальше — 0 запросов. |
| Один раз при загрузке страницы | 50 × 1ms = 50ms. Приемлемо. |
| Батч-процесс (CLI) | 5000 × 1ms = 5s. Медленно, но не «тормозит страницу». |

**Порог:** если N+1 добавляет > 100ms к ответу — лечите. Если < 50ms — может быть, не стоит.

---

### 8. Итог

```
Диагностика:
  SQL-лог → найти повторяющийся паттерн
  Profiler → посчитать количество и длительность
  Профиль: 1 + N, 1 + N + N×M

Решение (от простого к сложному):
  1. JOIN FETCH + PARTIAL    ← 80% случаев
  2. Денормализация          ← read-heavy hot-поля
  3. Custom SQL              ← сложные отчёты
  4. SL Cache                ← референтные данные
  5. EntityGraph             ← 2-3 уровня, динамические поля
  6. Pagination              ← временное

Чего НЕ делать:
  ❌ EAGER на коллекцию
  ❌ JOIN FETCH без PARTIAL
  ❌ «Лечить» N+1, которого нет
```

> ⏱ 41.0s · prompt 44795 tok · output 4496 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking a fundamental question about database constraints vs application-level validation in a PHP/Symfony context. This is a deep architectural question that touches on defense in depth, data integrity, and practical engineering.

Let me think about what makes a strong answer here:

1. Why DB constraints exist even when app code validates — the "who else can write to this table" argument is the core
2. Specific PostgreSQL constraints and when to use each
3. Practical examples with SQL
4. The nuanced "when NOT to use" cases
5. How this connects to the earlier conversation about Value Objects and aggregates

Key points to cover:
- The fundamental "trust boundary" argument — app code is not the only writer
- Types of constraints: NOT NULL, UNIQUE, CHECK, FK, EXCLUSION
- When each is appropriate
- Performance implications (indexes, planning)
- Migration safety
- The "defense in depth" framing
- Practical examples tied to the Invoice aggregate we've been discussing
- Common mistakes (over-constraining, under-constraining)
- The relationship to application-level validation (they complement, don't replace)

I should structure this as:
1. The core argument (why they exist)
2. Specific constraints with examples
3. When to use / when not
4. Practical patterns
5. Anti-patterns

Let me write this in Russian since the conversation is in Russian, keeping the technical depth and practical examples.

</details>

**Кандидат:**

## Зачем DB constraints, если код уже валидирует

### Короткий ответ

Код приложения — это **один** из путей записи в БД. Не единственный.

```
Кто ещё пишет в вашу таблицу?

├── Миграции (doctrine:migrations)
├── Ручной SQL (DBA, dev на staging)
├── Triggers (если есть)
├── Другие сервисы (микросервисы, batch-джобы)
├── Replication (логическое репликация)
├── Foreign key cascade (ON DELETE CASCADE)
└── Баги, которые пропустили code review
```

DB constraint — это **контракт на уровне данных**, а не на уровне приложения. Он работает, когда:
- приложение падает между двумя `UPDATE`;
- DBA вручную чинит данные;
- миграция содержит `INSERT ... ON CONFLICT`;
- баг в code path, который не покрыт тестами;
- триггер меняет значение после валидации.

---

### Слои защиты (defense in depth)

```
┌─────────────────────────────────────────────────────────────────┐
│  Слой 1: DTO / Symfony Validator                               │
│  → Отбрасывает мусор на входе (API, форма)                     │
│  → Быстро, до БД                                               │
│  → НЕ гарантия целостности                                     │
├─────────────────────────────────────────────────────────────────┤
│  Слой 2: Доменная модель (VO, инварианты)                      │
│  → Конструктор VO, domain methods                              │
│  → Логические правила (нельзя отменить оплаченный заказ)        │
│  → Работает только внутри процесса PHP                          │
├─────────────────────────────────────────────────────────────────┤
│  Слой 3: DB constraints                                        │
│  → Работает для ВСЕХ писателей                                 │
│  → Атомарно (в рамках транзакции)                               │
│  → Не зависит от языка, фреймворка, процесса                    │
├─────────────────────────────────────────────────────────────────┤
│  Слой 4: Triggers / Rules (PostgreSQL 15+)                     │
│  → Сложные кросс-строковые проверки                             │
│  → Редко, только для специфических случаев                      │
└─────────────────────────────────────────────────────────────────┘
```

**Ни один слой не заменяет другой.** Они ловят разные категории ошибок.

---

### Какие constraints и когда

#### 1. `NOT NULL` — базовая гигиена

```sql
CREATE TABLE invoices (
    id            SERIAL PRIMARY KEY,
    customer_id   VARCHAR(36) NOT NULL,
    status        VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);
```

**Когда:** поле имеет смысл только при наличии значения. `NULL` — это не «нет данных», а «кто-то ошибся».

**Почему не код:** код может упасть до `INSERT`. Миграция может написать `NULL`. DBA может. `NOT NULL` — последний барьер.

**Trade-off:** почти нет. Исключение — поля, которые действительно могут быть пустыми (опциональный комментарий, alternative email). Тогда `NULL` — допустимое состояние.

---

#### 2. `UNIQUE` — идемпотентность и естественные ключи

```sql
CREATE TABLE users (
    id    SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE
);
```

**Когда:**
- Естественный ключ (email, uuid, login)
- Идемпотентные операции (`UNIQUE (order_id, idempotency_key)`)
- Бизнес-правило «только один active subscription на account»

```sql
-- Только один active подписка на аккаунт
CREATE UNIQUE INDEX idx_subscriptions_active_per_account
    ON subscriptions (account_id)
    WHERE status = 'active';
```

**Почему не код:** код может гоняться (race condition). Два параллельных запроса одновременно проверяют «нет active» → оба вставляют. `UNIQUE` + `ON CONFLICT` решает атомарно.

```php
// ✅ Атомарная идемпотентность
INSERT INTO orders (order_id, idempotency_key, payload)
VALUES (:id, :key, :payload)
ON CONFLICT (idempotency_key) DO NOTHING
RETURNING id;
```

**Trade-off:**
- `UNIQUE` создаёт индекс → overhead на INSERT (~5–10%)
- Не должно быть > 2–3 UNIQUE на таблицу (каждый — отдельный B-tree)
- Partial unique index (`WHERE ...`) — почти без overhead

---

#### 3. `CHECK` — доменные инварианты на уровне данных

```sql
-- Money: не отрицательный, валидный код валюты
ALTER TABLE invoices ADD CONSTRAINT chk_invoice_total_cents
    CHECK (total_cents >= 0);

ALTER TABLE invoices ADD CONSTRAINT chk_invoice_currency
    CHECK (currency_code ~ '^[A-Z]{3}$');

-- Статус: только допустимые значения
ALTER TABLE invoices ADD CONSTRAINT chk_invoice_status
    CHECK (status IN ('draft', 'issued', 'paid', 'cancelled'));

-- Quantity: минимум 1
ALTER TABLE invoice_lines ADD CONSTRAINT chk_line_quantity
    CHECK (quantity >= 1 AND quantity <= 100000);

-- Price: не отрицательная
ALTER TABLE invoice_lines ADD CONSTRAINT chk_line_price
    CHECK (unit_price_cents >= 0);

-- Кросс-полевая (редко): total >= 0, currency совпадает
ALTER TABLE invoices ADD CONSTRAINT chk_invoice_currency_match
    CHECK (currency_code IN ('EUR', 'USD', 'GBP'));
```

**Когда:**
- Инвариант не зависит от бизнес-контекста (цена >= 0 — всегда)
- Стоимость нарушения — катастрофа (отрицательная сумма в отчётах)
- Несколько писателей (микросервисы, batch)

**Когда НЕ:**
- Сложная бизнес-логика (нельзя отменить оплаченный заказ) → это логика приложения, не данные
- Правило зависит от внешнего состояния (лимит баланса) → проверка в transaction + `FOR UPDATE`

**Trade-off:**
- `CHECK` не создаёт индекс → overhead минимален
- Но: если правило меняется — нужна миграция + backfill
- Не поддерживает подзапросы, агрегаты, UDF (в некоторых СУБД)

---

#### 4. `FOREIGN KEY` — референциальная целостность

```sql
CREATE TABLE invoice_lines (
    id          SERIAL PRIMARY KEY,
    invoice_id  INT NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
    product_id  INT NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    quantity    INT NOT NULL,
    unit_price  INT NOT NULL
);
```

**Когда:**
- Родительская сущность **должна** существовать (line без invoice — мусор)
- Удаление родителя = удаление детей (CASCADE)
- Удаление родителя **не должно** происходить, если есть дети (RESTRICT)

**Почему не код:**
- Race condition: между `SELECT` (invoice существует) и `INSERT` (line) — invoice удалили
- Batch-джоб удаляет invoice, не зная о lines
- DBA делает `TRUNCATE`

**Trade-off:**
- FK создаёт индекс на referencing column → overhead на INSERT
- `ON DELETE CASCADE` — удобно, но опасно: один `DELETE` может унести 10k строк
- `ON DELETE RESTRICT` — безопаснее, но требует явного удаления детей
- В очень высоконагруженных системах (millions of rows/sec) иногда уходят от FK в пользу приложения — но это редкость

---

#### 5. `EXCLUSION` — непересечение интервалов (PostgreSQL)

```sql
-- Подписки: не могут пересекаться по времени для одного аккаунта
CREATE TABLE subscriptions (
    id          SERIAL PRIMARY KEY,
    account_id  INT NOT NULL,
    period      TSTZRANGE NOT NULL,
    EXCLUDE USING gist (account_id WITH =, period WITH &&)
);
```

**Когда:** временные интервалы не должны пересекаться (подписки, брони, слоты).

**Почему не код:** race condition. Два параллельных `INSERT` с пересечением → без constraint оба пройдут.

**Trade-off:**
- Требует `btree_gist` extension
- Медленнее, чем `UNIQUE`
- Только PostgreSQL (не MySQL)

---

#### 6. `GENERATED` columns — производные значения

```sql
-- Total считается в БД, не в приложении
ALTER TABLE invoice_lines
    ADD COLUMN line_total INT GENERATED ALWAYS AS (quantity * unit_price_cents) STORED;
```

**Когда:** значение **всегда** вычисляется из других полей, и не может быть «неправильным».

**Почему:** даже если приложение забыло пересчитать — БД даст правильный результат.

**Trade-off:**
- Не для сложной логики (только выражение)
- `STORED` — занимает место в B-tree
- `VIRTUAL` (PostgreSQL 15+) — не хранится, вычисляется на лету

---

### Практические паттерны

#### Паттерн 1: Инвариант агрегата → CHECK

```php
// Домен: Invoice.total() = сумма lines
// БД: защита от ручного SQL
ALTER TABLE invoices ADD CONSTRAINT chk_invoice_total_consistent
    CHECK (total_cents = (
        SELECT COALESCE(SUM(quantity * unit_price_cents), 0)
        FROM invoice_lines WHERE invoice_id = invoices.id
    ));
```

**Минус:** подзапрос в CHECK — медленно. Лучше: `GENERATED` column + триггер на `invoice_lines`.

#### Паттерн 2: Идемпотентность → UNIQUE + ON CONFLICT

```sql
ALTER TABLE payments ADD CONSTRAINT uq_payments_idempotency
    UNIQUE (account_id, idempotency_key);
```

```php
// Атомарная вставка, без race condition
INSERT INTO payments (account_id, idempotency_key, amount, status)
VALUES (:acc, :key, :amount, 'pending')
ON CONFLICT (account_id, idempotency_key)
DO UPDATE SET updated_at = now()
RETURNING id;
```

#### Паттерн 3: Состояние → CHECK + application logic

```sql
-- БД: только допустимые статусы
ALTER TABLE invoices ADD CONSTRAINT chk_status
    CHECK (status IN ('draft', 'issued', 'paid', 'cancelled'));
```

```php
// Приложение: переходы между статусами
// БД НЕ знает, что draft → cancelled недопустим.
// Это логика, не данные.
```

#### Паттерн 4: Атомарный декремент (баланс)

```sql
-- БД: баланс не отрицательный
ALTER TABLE accounts ADD CONSTRAINT chk_balance CHECK (balance_cents >= 0);

-- Атомарная операция:
UPDATE accounts
SET balance_cents = balance_cents - :amount
WHERE id = :id AND balance_cents >= :amount
RETURNING id;
-- 0 строк обновлено → недостаточно средств
```

**Почему не код:** `SELECT balance → if (balance >= amount) → UPDATE` — race condition. `WHERE balance_cents >= :amount` + `CHECK` — атомарно.

---

### Когда constraints НЕ нужны

| Ситуация | Почему |
|----------|--------|
| Сложная бизнес-логика | «Нельзя отменить оплаченный заказ» — это логика, не данные |
| Внешние зависимости | «Нельзя удалить, пока есть активная подписка в другом сервисе» |
| Временные правила | «Промокод действует до 31.12» — лучше в приложении |
| Производительность критична | > 10 CHECK на таблицу — overhead заметен (редко) |
| Миграция в процессе | Переходный период, данные «грязные» — constraints блокируют |

---

### Частые ошибки

#### ❌ 1. «У нас же в коде валидация, CONSTRAINT не нужен»

```sql
-- Без CHECK:
INSERT INTO invoices (total_cents, currency_code) VALUES (-500, 'eur');
-- Пропало. В отчётах — отрицательная сумма. Аудит — не проходит.
```

#### ❌ 2. `CHECK` на каждое поле — «на всякий случай»

```sql
ALTER TABLE users ADD CONSTRAINT chk_name CHECK (name IS NOT NULL AND name != '');
ALTER TABLE users ADD CONSTRAINT chk_age CHECK (age >= 0 AND age <= 150);
ALTER TABLE users ADD CONSTRAINT chk_email CHECK (email ~ '^[^@]+@[^@]+\.[^@]+$');
-- 50 CHECK на таблицу — overhead, и email-regex в БД — плохая идея
```

**Правило:** `CHECK` для **инвариантов данных**, не для валидации форм. Email-валидация — в приложении.

#### ❌ 3. `ON DELETE CASCADE` на всё

```sql
-- Опасно: удаление account → cascade → 10k invoices → 100k lines
CREATE TABLE invoice_lines (
    invoice_id INT REFERENCES invoices(id) ON DELETE CASCADE
);
```

**Лучше:**
```sql
-- DELETE RESTRICT: нельзя удалить invoice, пока есть lines
CREATE TABLE invoice_lines (
    invoice_id INT NOT NULL REFERENCES invoices(id) ON DELETE RESTRICT
);
```

Приложение явно удаляет children → parent. Контролируемо.

#### ❌ 4. `UNIQUE` на составной ключ из 5 колонок

```sql
ALTER TABLE payments ADD UNIQUE (account_id, order_id, status, created_at, idempotency_key);
```

Каждый уникальный индекс — B-tree. 5 колонок — широкий индекс, медленный INSERT, высокий overhead.

**Правило:** UNIQUE на 2–3 колоны максимум. Если нужно больше — пересмотреть модель.

#### ❌ 5. Constraint как замена бизнес-логики

```sql
-- ❌ Пытается закодировать «нельзя отменить оплаченный»
ALTER TABLE invoices ADD CONSTRAINT chk_cancel
    CHECK (status != 'cancelled' OR total_cents > 0);
-- Это не то же самое. И невозможно выразить переходы.
```

---

### Итоговая таблица

| Constraint | Что защищает | Overhead | Когда |
|-----------|-------------|----------|-------|
| `NOT NULL` | Отсутствие данных | Минимальный | Всегда, для required-полей |
| `UNIQUE` | Дубликаты, идемпотентность | Индекс (5–10%) | Естественные ключи, идемпотентность |
| `CHECK` | Невалидные значения | Минимальный | Инварианты данных (>= 0, enum, format) |
| `FOREIGN KEY` | Сироты, референциальная целостность | Индекс | Родитель-дети, обязательные ссылки |
| `EXCLUSION` | Пересечения интервалов | GIST индекс | Подписки, брони, слоты |
| `GENERATED` | Неправильные производные значения | Storage / CPU | Вывод из других полей |

---

### Принцип

> **Приложение валидирует бизнес-логику. БД гарантирует целостность данных.**
>
> Если правило можно выразить как «значение поля X должно удовлетворять Y» — это `CHECK`.
> Если правило зависит от контекста, состояния, времени — это приложение.
> Если правило — «эта строка не может существовать без той» — это `FK`.
>
> Каждый уровень ловит то, что предыдущий не может.

> ⏱ 38.7s · prompt 49073 tok · output 3969 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе диагностики и оптимизации медленных запросов в PostgreSQL. Это собеседование на PHP-бэкенд-разработчика. Нужно показать системный подход: от обнаружения проблемы до конкретными решениями.

Структурирую ответ как процесс (pipeline), а не как список советов. Покажу:
1. Как фиксирую проблему (логи, slow query log, profiler)
2. EXPLAIN ANALYZE — главный инструмент
3. Читаю план запроса: что ищу
4. Типовые причины (от частых к редким)
5. Решения для каждой
6. Антипаттерны оптимизации

Буду конкретен, с примерами SQL и EXPLAIN output. Не буду писать "в целом", а покажу реальные сценарии.

Важно: не перегружать. 5-7 типовых причин с конкретными решениями достаточно. Плюс показать, как читаю EXPLAIN — это то, что реально используют.

Начну с процесса (как ищу), потом EXPLAIN (как читаю), потом причины (что лечу).

</details>

**Кандидат:**

## Диагностика и оптимизация медленных запросов в PostgreSQL

### 1. Фиксация проблемы

Прежде чем оптимизировать — нужно **зафиксировать**, что именно медленно и насколько.

#### Slow query log

```sql
-- Включить (postgresql.conf)
log_min_duration_statement = 500ms;  -- логируем всё, что > 500ms

-- Или через GUC в сессии
SET log_min_duration_statement = '200ms';
```

Что видно в логе:

```
LOG:  duration: 2340.123 ms  statement: SELECT i.* FROM invoices i 
    JOIN invoice_lines l ON l.invoice_id = i.id 
    WHERE l.product_id = 42
```

#### `pg_stat_statements` (расширение)

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Топ-10 самых медленных по общему времени
SELECT
    round((total_exec_time / calls)::numeric, 2) AS avg_ms,
    calls,
    round((total_exec_time / 1000.0)::numeric, 1) AS total_sec,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;

-- Топ-10 по среднему времени (редкие, но очень медленные)
SELECT
    round((total_exec_time / calls)::numeric, 2) AS avg_ms,
    calls,
    left(query, 120) AS query
FROM pg_stat_statements
WHERE calls > 10
ORDER BY total_exec_time / calls DESC
LIMIT 10;
```

**Что ищу:**
- `calls` высокий, `avg_ms` средний → частый запрос, который надо оптимизировать
- `calls` низкий, `avg_ms` высокий → редкий, но очень тяжёлый (отчёт, аналитика)
- `total_exec_time` доминирует → главный кандидат для оптимизации

#### `pg_stat_activity` — что происходит прямо сейчас

```sql
SELECT
    pid,
    now() - query_start AS duration,
    state,
    wait_event_type,
    wait_event,
    left(query, 100) AS query
FROM pg_stat_activity
WHERE state != 'idle'
ORDER BY duration DESC;
```

**Что ищу:**
- Запрос висит > 1 сек → кто он, на чём ждёт
- `wait_event_type = Lock` → блокировка
- `wait_event_type = IO` → ждёт диск
- `wait_event_type = CPU` → вычисление

---

### 2. EXPLAIN ANALYZE — главный инструмент

```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT i.id, i.total_cents, i.status
FROM invoices i
JOIN invoice_lines l ON l.invoice_id = i.id
WHERE l.product_id = 42;
```

Пример вывода:

```
Hash Join  (cost=1234.56..45678.90 rows=1000 width=24) (actual time=234.1..4567.8 rows=8923 loops=1)
  Hash Cond: (i.id = l.invoice_id)
  Buffers: shared hit=12345 read=23456
  ->  Index Scan using idx_lines_product on invoice_lines l  (cost=0.42..890.12 rows=8923 width=8) (actual time=0.05..45.2 rows=8923 loops=1)
        Index Cond: (product_id = 42)
        Buffers: shared hit=456 read=789
  ->  Seq Scan on invoices i  (cost=0.00..34567.89 rows=500000 width=24) (actual time=0.01..3456.7 rows=500000 loops=1)
        Buffers: shared hit=1234 read=56789
Planning Time: 0.5 ms
Execution Time: 4567.9 ms
```

---

### 3. Как я читаю план

#### Сверху вниз — главный оператор

| Оператор | Что значит | Проблема? |
|----------|-----------|-----------|
| `Seq Scan` на большой таблице | Полный обход | Да, если таблица > 10k строк и нужен подмножество |
| `Index Scan` | По индексу | Нет, хорошо |
| `Bitmap Index Scan` + `Bitmap Heap Scan` | Индекс → heap | Ок, для среднего выборки |
| `Hash Join` | Быстрый, для больших | Обычно хорошо |
| `Nested Loop` + `Index Scan` на inner | Один за раз | Хорошо, если inner выборка маленькая |
| `Nested Loop` + `Seq Scan` на inner | Плохо | Да, O(N×M) |
| `Sort` + `Limit` | Сортировка | Ок, если LIMIT маленький |
| `Aggregate` | Агрегация | Смотреть, по какому ключу |

#### Ключевые метрики в `actual time`

```
actual time=234.1..4567.8
         ↑           ↑
   first row    last row
```

- `first row` = time to first row (латентность до первого результата)
- `last row` = общее время

Если `first row` > 50% `last row` — большинство времени уходит на генерацию **первого** результата (план плохой, или данные холодные).

#### `Buffers`

```
Buffers: shared hit=1234 read=56789
         ↑            ↑
    в кэше (быстро)  с диска (медленно)
```

- `hit / (hit + read)` > 90% → данные в кэше, проблема не в IO
- `read` доминирует → данные холодные, IO-bound

#### `rows` vs `actual rows`

```
rows=1000 (estimated)  actual rows=8923
```

Если оценка в 10–100 раз от reality — **статистика устарела**.

```sql
ANALYZE invoices;
ANALYZE invoice_lines;
```

---

### 4. Типовые причины (от частых к редким)

#### Причина 1: Отсутствует индекс

**Симптом:** `Seq Scan` на таблице с > 10k строк, где нужен подмножество.

```sql
-- Медленно: Seq Scan on invoice_lines (500k rows)
SELECT * FROM invoice_lines WHERE product_id = 42;

-- EXPLAIN:
Seq Scan on invoice_lines  (cost=0.00..12345.67 rows=8923 width=40)
```

**Решение:**

```sql
CREATE INDEX idx_lines_product ON invoice_lines (product_id);
```

**Trade-off:**
- +5–10% overhead на INSERT/UPDATE
- Индекс занимает место в RAM
- Если выборка > 30% таблицы — Seq Scan быстрее (PostgreSQL знает это сам)

**Когда НЕ создавать индекс:**
- Таблица < 1k строк
- Запрос выполняется < 10 раз/мин
- Колонка с низким基数 (2–3 значения) — bitmap scan будет эффективнее

---

#### Причина 2: Неправильный тип JOIN

**Симптом:** `Nested Loop` + `Seq Scan` на inner.

```
Nested Loop  (cost=...) (actual time=... rows=...)
  ->  Seq Scan on invoices  (500k rows)
  ->  Seq Scan on invoice_lines  (100k rows)
  -- 500k × 100k = 50 млрд сравнений
```

**Решение:**

```sql
-- Вариант А: индекс на join-колонке
CREATE INDEX idx_lines_invoice ON invoice_lines (invoice_id);
-- PostgreSQL переключится на Hash Join или Nest Loop + Index Scan

-- Вариант Б: пересмотреть JOIN (может быть, он не нужен)
SELECT i.id, i.total_cents
FROM invoices i
WHERE i.status = 'issued'
  AND i.total_cents > 10000;
-- Без JOIN, если данные уже в invoices (денормализация)
```

---

#### Причина 3: Статистика устарела

**Симптом:** `estimated rows` сильно отличается от `actual rows`.

```
Index Scan using idx_lines_product  (cost=0.42..890.12 rows=100 width=8)
    (actual time=0.05..45.2 rows=8923 loops=1)
-- Оценка: 100. Реальность: 8923. В 89 раз меньше!
```

**Последствие:** PostgreSQL выбирает `Nested Loop`, думая что inner выборка маленькая. На деле — 8923 строки → Nested Loop становится медленным.

**Решение:**

```sql
ANALYZE invoice_lines;  -- обновить статистику

-- Проверить:
SELECT
    attname,
    n_distinct,
    most_common_vals
FROM pg_statistic
WHERE tablename = 'invoice_lines'
  AND attname = 'product_id';
```

**Автоматически:** `autovacuum` запускает `ANALYZE`. Если не запускается:

```sql
-- Проверить, что autovacuum работает
SELECT * FROM pg_stat_user_tables WHERE relname = 'invoice_lines';
-- vacuum_count, analyze_count — растут ли?
```

**Частая причина устаревшей статистики:**
- Миграция вставила 100k новых строк
- Таблица была `TRUNCATE`'ну и заполнена заново
- `autovacuum` отключён (редко, но бывает на legacy-базах)

---

#### Причина 4: Partial index / covering index

**Симптом:** Индекс есть, но `Index Scan` + `Heap Fetch` на каждую строку.

```
Index Scan using idx_lines_product on invoice_lines  (cost=...)
  Index Cond: (product_id = 42)
  Heap Fetches: 8923   ← на каждую строку — обратный ход в heap
```

**Решение — covering index ( INCLUDE ):**

```sql
-- Обычный индекс:
CREATE INDEX idx_lines_product ON invoice_lines (product_id);
-- Index Scan → Heap Fetch (8923 раза)

-- Covering index:
CREATE INDEX idx_lines_product_cover
    ON invoice_lines (product_id)
    INCLUDE (quantity, unit_price_cents, invoice_id);
-- Index Only Scan → 0 Heap Fetches
```

**EXPLAIN после:**

```
Index Only Scan using idx_lines_product_cover  (cost=...)
  Index Cond: (product_id = 42)
  Heap Fetches: 0   ← данные взяты из индекса, не из heap
```

**Когда работает:** запрос выбирает только колонки из `INCLUDE`. Если нужна ещё одна колонка — обратно к Heap Fetch.

**Trade-off:**
- Индекс больше (в 2–3 раза)
- Overhead на INSERT/UPDATE выше
- `Index Only Scan` работает только если `visibility map` актуален (после VACUUM)

---

#### Причина 5: `SELECT *` + ненужные колонки

**Симптом:** Широкий запрос, много данных в памяти, медленная сортировка.

```sql
SELECT * FROM invoices WHERE status = 'issued' ORDER BY created_at DESC LIMIT 50;
-- 20 колонок, из которых используются 4
```

**Решение:**

```sql
SELECT id, total_cents, currency_code, created_at
FROM invoices
WHERE status = 'issued'
ORDER BY created_at DESC
LIMIT 50;
```

**Почему помогает:**
- Меньше данных в sort buffer
- Меньше IO (если строки не в кэше)
- `Index Only Scan` возможен (если covering index)

**Где часто:** ORM-запросы (Doctrine `SELECT i.*`), DQL без `PARTIAL`.

---

#### Причина 6: Блокировки

**Симптом:** Запрос висит, `wait_event_type = Lock`.

```sql
-- Кто блокирует?
SELECT
    blocked.pid AS blocked_pid,
    blocking.pid AS blocking_pid,
    blocking.usename AS blocking_user,
    left(blocking.query, 80) AS blocking_query,
    blocked.query AS blocked_query
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND NOT bl.granted
JOIN pg_locks gl ON gl.locktype = bl.locktype
    AND gl.database IS NOT NULL
    AND gl.relid IS NOT NULL
    AND gl.granted
    AND gl.pid <> blocked.pid
JOIN pg_stat_activity blocking ON blocking.pid = gl.pid
WHERE blocked.state = 'active'
  AND NOT blocked.wait_event IS NULL;
```

**Типовые сценарии:**

| Сценарий | Причина |
|----------|---------|
| `SELECT ... FOR UPDATE` держит > 1 сек | Длинная транзакция |
| `UPDATE` + `INSERT` в одной транзакции | Lock escalation |
| Deadlock | Два запроса ждут друг друга |
| Миграция (DDL) | `ALTER TABLE` блокирует DML |

**Решения:**

```sql
-- Убавить время удержания:
-- ❌  BEGIN; SELECT ... FOR UPDATE; -- долгая обработка -- COMMIT;
-- ✅  BEGIN; SELECT ... FOR UPDATE; -- быстрая валидация -- UPDATE -- COMMIT;

-- Для deadlock:
-- 1. Всегда обновлять в одном порядке таблиц
-- 2. Убавить scope транзакции
-- 3. deadlock_timeout = 1s (быстрее увидеть ошибку)
```

---

#### Причина 7: Подзапрос / CTE, который можно оптимизировать

**Симптом:** `SubPlan` или `CTE` вычисляется для каждой строки внешнего запроса.

```sql
-- Медленно: для каждого invoice — отдельный подзапрос
SELECT i.id,
    (SELECT SUM(quantity * unit_price_cents) FROM invoice_lines WHERE invoice_id = i.id) AS total
FROM invoices i
WHERE i.status = 'issued';
-- 50k invoices × подзапрос = 50k запросов к invoice_lines
```

**Решение — заменить на JOIN + GROUP BY:**

```sql
-- Быстро: один JOIN + агрегация
SELECT i.id, COALESCE(SUM(l.quantity * l.unit_price_cents), 0) AS total
FROM invoices i
LEFT JOIN invoice_lines l ON l.invoice_id = i.id
WHERE i.status = 'issued'
GROUP BY i.id;
```

**EXPLAIN до:**

```
Result  (cost=...)
  ->  Seq Scan on invoices i  (50k rows)
  SubPlan 1:
    ->  Index Scan on invoice_lines  (89 rows per call)
    -- 50k × 89 = 4.5M строк
```

**EXPLAIN после:**

```
HashAggregate  (cost=...)
  ->  Hash Join  (cost=...)
    ->  Seq Scan on invoices  (50k rows)
    ->  Seq Scan on invoice_lines  (500k rows)
    -- 500k строк + агрегация = 1 проход
```

---

#### Причина 8: `ORDER BY` + `LIMIT` без подходящего индекса

**Симптом:** `Sort` на 500k строк, чтобы взять 50.

```
Sort  (cost=123456.78..123500.00 rows=500000 width=24)
  Sort Key: created_at DESC
  ->  Seq Scan on invoices  (500k rows)
  -- 500k строк отсортированы, чтобы взять 50
```

**Решение:**

```sql
CREATE INDEX idx_invoices_status_created
    ON invoices (status, created_at DESC);
```

**EXPLAIN после:**

```
Limit  (cost=0.42..12.34 rows=50 width=24)
  ->  Index Scan Backward using idx_invoices_status_created  (cost=0.42..45678.90 rows=500000 width=24)
        Index Cond: (status = 'issued')
  -- 50 строк из индекса, без сортировки
```

**Trade-off:**
- Индекс `(status, created_at)` полезен только для `WHERE status = X ORDER BY created_at`
- Если `ORDER BY` другой — другой индекс
- 10 разных ORDER BY = 10 индексов. Не стоит.

---

### 5. Антипаттерны оптимизации

#### ❌ 1. `SET enable_hashjoin = off`

```sql
SET enable_hashjoin = off;
-- «Сработало» — PostgreSQL выбрал Nested Loop
-- Но при изменении объёма данных — снова сломается
```

Это не оптимизация, а **заглушка**. Правильно: понять, почему planner выбрал Hash Join, и дать ему нужный индекс.

#### ❌ 2. Индекс на каждую колонку в `WHERE`

```sql
-- WHERE status = 'issued' AND total_cents > 10000 AND created_at > '2024-01-01'
CREATE INDEX idx1 ON invoices (status);
CREATE INDEX idx2 ON invoices (total_cents);
CREATE INDEX idx3 ON invoices (created_at);
-- 3 индекса. PostgreSQL использует максимум один (без bitmap)
```

**Правильно:**

```sql
CREATE INDEX idx_invoices_filter
    ON invoices (status, total_cents, created_at DESC);
-- Один индекс, покрывает все три условия
```

#### ❌ 3. Денормализация «на всякий случай»

```sql
-- «Чтобы не делать JOIN, денормализую»
ALTER TABLE invoice_lines ADD COLUMN product_name VARCHAR(255);
ALTER TABLE invoice_lines ADD COLUMN product_price INT;
-- Теперь:
-- 1. Дублирование данных
-- 2. При смене цены продукта — UPDATE 10k строк
-- 3. При смене имени — UPDATE 10k строк
-- 4. Индексы на этих колонках — лишние
```

**Правило:** денормализация — после того, как JOIN оптимизирован и всё равно медленно. Не до.

#### ❌ 4. `SELECT COUNT(*)` на большой таблице

```sql
SELECT COUNT(*) FROM invoices WHERE status = 'issued';
-- 500k строк → Seq Scan → 200ms+
```

**Решения:**

| Подход | Когда |
|--------|-------|
| Покрытый индекс `CREATE INDEX ON invoices (status)` | `COUNT` нужен часто |
| Approximate count | Аналитика, dashboard |
| Материализованное представление | Отчёты, не критично к задержке |
| `pg_class.reltuples` | Оценка, не точное значение |

```sql
-- Покрытый индекс (Index Only Scan):
CREATE INDEX idx_invoices_status ON invoices (status);
-- COUNT(*) → Index Only Scan, без heap
```

---

### 6. Процесс (чеклист)

```
1. Зафиксировать:
   pg_stat_statements → top-10 по total_exec_time
   pg_stat_activity → кто висит прямо сейчас

2. EXPLAIN ANALYZE:
   - Операторы (Seq Scan? Hash Join? Nest Loop?)
   - actual rows vs estimated rows
   - Buffers (hit vs read)
   - first row vs last row

3. Определить причину:
   [ ] Нет индекса
   [ ] Статистика устарела → ANALYZE
   [ ] Nested Loop + Seq Scan → индекс или переписать JOIN
   [ ] Heap Fetches > 0 → covering index
   [ ] Подзапрос → JOIN + GROUP BY
   [ ] Sort + LIMIT → индекс по ORDER BY
   [ ] Блокировка → pg_locks
   [ ] SELECT * → конкретные колонки

4. Применить решение:
   - Индекс
   - ANALYZE
   - Переписать запрос
   - Денормализация (только после п. 1–3)

5. Проверить:
   EXPLAIN ANALYZE → время упало?
   pg_stat_statements → total_exec_time снизился?
   Нагрузочный тест → регрессий нет?
```

---

### 7. Что НЕ делать

| ❌ | Почему |
|----|--------|
| Оптимизировать без EXPLAIN | Могете «оптимизировать» запрос, который и так быстрый |
| Добавлять индексы «на всякий случай» | Каждый индекс — overhead на запись |
| Убирать JOIN | Сначала попробуйте индекс. JOIN с индексом часто быстрее, чем денормализация |
| `SET`-команды для форсирования плана | Хрупко, ломается при изменении данных |
| Оптимизировать один запрос, не смотрев на остальные | Один быстрый запрос + 10 медленных = всё равно медленно |
| Игнорировать `wait_event` | Если запрос ждёт IO — индекс не поможет. Нужен SSD / больше RAM |

> ⏱ 48.2s · prompt 52783 tok · output 5259 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: определение, границы, пример, типовая ошибка. Это продолжение серии вопросов на собеседовании. Нужно ответить как опытный PHP-разработчик, который реально работает с DDD.

Ключевые аспекты, которые нужно покрыть:
1. Что такое агрегат — определение
2. Как определяю границы — критерии/вопросы
3. Пример с объяснением
4. Типовая ошибка при выборе границ

Я уже в предыдущих ответах использовал Invoice как пример. Логично продолжить с этой же доменной моделью, но показать более сложную ситуацию, где границы агрегата спорные. Например: Order + Invoice + Payment — где граница?

Типовая ошибка: слишком большой агрегат (God Aggregate) или слишком мелкий (распыление). Я покажу конкретный пример — "Order Aggregate", который пытается содержать Payment, Shipping, Notification, Invoice, Inventory. Это классическая ошибка.

Структурирую ответ:
1. Определение (коротко, по существу)
2. Критерии выбора границ (вопросы)
3. Пример: Order как агрегат (что входит, что не входит, почему)
4. Типовая ошибка: "Order Aggregate" на 15 сущностей
5. Как я это решаю

Буду честен: DDD — это не религия, а инструмент. Покажу, где я иду на компромиссы.

</details>

**Кандидат:**

## Агрегат в DDD: определение и границы

### Определение

Агрегат — это **кластер сущностей**, который:
1. Обеспечивает **целостность инвариантов** как единое целое.
2. Имеет **ровно один корень** (Aggregate Root), через который изменяется состояние.
3. Является **единицей транзакционной целостности**: либо всё меняется атомарно, либо ничего.
4. Обнаруживается снаружи **только по идентификатору корня**.

Это не «группа сущностей, которые связаны FK». Это **группа, инварианты которой нельзя проверить, не рассматривая их все вместе**.

---

### Критерии выбора границ

Я задаю четыре вопроса:

| Вопрос | Если «да» → входит в один агрегат |
|--------|-----------------------------------|
| **Целостность:** инвариант затрагивает обе сущности одновременно? | Да |
| **Атомарность:** изменение одной требует одновременного изменения другой в той же транзакции? | Да |
| **Одновременность:** вы никогда не используете одну без другой в одном бизнес-событии? | Да |
| **Идентификация:** снаружи вы обращаетесь к ним только через один ID? | Да |

Если хотя бы один ответ «нет» — вероятнее всего, это **разные агрегаты**, связанные ссылкой (ID).

---

### Пример: Order

#### Домен

Заказ в интернет-магазине. Связанные сущности:

```
Order (корень)
├── OrderLine[]          ← строки заказа
├── ShippingAddress      ← адрес доставки
├── PaymentInfo          ← способ оплаты (card token, provider)
├── DiscountInfo         ← применённый промокод
├── Status               ← текущий статус
└── Timestamps           ← created, updated

Вне агрегата (отдельные агрегаты):
├── Payment (ID)         ← факт оплаты, статус, provider
├── Invoice (ID)         ← счёт
├── Shipment (ID)        ← отправка, tracking
├── Customer (ID)        ← клиент
├── Product (ID)         ← товар
└── Inventory (ID)       ← склад
```

#### Что внутри и почему

```php
final class Order
{
    private ?int $id = null;
    private string $customerId;        // ссылка на Customer (ID)
    private Money $currency;
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;

    /** @var OrderLine[] */
    private array $lines = [];
    private ?ShippingAddress $shippingAddress = null;
    private ?PaymentInfo $paymentInfo = null;
    private ?DiscountInfo $discount = null;

    // ─── Инварианты агрегата ─────────────────────────────────

    public function addLine(OrderLine $line): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new \DomainException('Cannot add line to confirmed order');
        }
        if ($line->getUnitPrice()->currencyCode !== $this->currency->currencyCode) {
            throw new \DomainException('Currency mismatch');
        }
        $this->lines[] = $line;
    }

    public function confirm(): void
    {
        if (count($this->lines) === 0) {
            throw new \DomainException('Cannot confirm empty order');
        }
        if ($this->shippingAddress === null) {
            throw new \DomainException('Cannot confirm order without shipping address');
        }
        if ($this->paymentInfo === null) {
            throw new \DomainException('Cannot confirm order without payment info');
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function total(): Money
    {
        $base = $this->sumLines();
        $discount = $this->discount?->calculate($base) ?? new Money(0, $this->currency->currencyCode);
        return $base->subtract($discount);
    }

    // ...
}
```

**Почему `OrderLine` внутри:**
- Инвариант: `total = сумма(lines)` — проверяется только вместе.
- Атомарность: добавить строку + пересчитать total — одна операция.
- Никогда не используем `OrderLine` без `Order`.

**Почему `ShippingAddress` внутри:**
- Инвариант: `confirm()` требует адрес.
- Атомарность: подтверждение заказа = смена статуса + привязка адреса.

**Почему `PaymentInfo` внутри:**
- Это **информация о способе оплаты** (какой token, какой провайдер).
- Не **факт оплаты** (это отдельный агрегат `Payment`).

**Почему `Payment` — отдельный агрегат:**
- Инвариант `Payment` (sum > 0, status transition) не затрагивает `Order`.
- Атомарность: `Order.confirm()` и `Payment.charge()` — разные транзакции.
  (Если payment failed — order остаётся confirmed, но payment pending. Это допустимое состояние.)
- Снаружи: `Order.customerId` → `Customer`, `Order.paymentInfo.token` → `Payment` (по token).
- `Payment` имеет собственный lifecycle: `Pending → Processing → Succeeded / Failed → Refunded`.

**Почему `Product` — отдельный агрегат:**
- `Product` меняется независимо от `Order` (цена, наличие, описание).
- `Order` хранит **снимок** (`unitPrice` на момент заказа), а не ссылку на `Product`.
- Инварианты `Product` (цена > 0, SKU unique) не затрагивают `Order`.

---

#### Схема

```
┌─────────────────────────────────────────────────────┐
│  Aggregate: Order                                    │
│  Root: Order                                         │
│  Members: OrderLine[], ShippingAddress, PaymentInfo  │
│                                                     │
│  Инварианты:                                         │
│    - total = sum(lines) - discount                   │
│    - confirm() требует lines + address + payment     │
│    - status transition: Draft → Confirmed → ...      │
│    - currency match across all lines                 │
│                                                     │
│  Идентификаторы: Order.id                            │
└─────────────────────────────────────────────────────┘
         │                    │
         │ customerId         │ paymentInfo.token
         ▼                    ▼
┌─────────────────┐    ┌─────────────────┐
│  Aggregate:     │    │  Aggregate:     │
│  Customer       │    │  Payment        │
│  Root: Customer │    │  Root: Payment  │
│  Members:       │    │  Members:       │
│    Address[]    │    │    Transaction[]│
│    Preferences  │    │                 │
│                 │    │  Инварианты:    │
│  Инварианты:    │    │    - amount > 0 │
│    - email valid│    │    - status     │
│    - name non-empty│  │      transition │
└─────────────────┘    └─────────────────┘

┌─────────────────┐    ┌─────────────────┐
│  Aggregate:     │    │  Aggregate:     │
│  Product        │    │  Shipment       │
│  Root: Product  │    │  Root: Shipment │
│  Members:       │    │  Members:       │
│    PriceHistory │    │    Tracking     │
│    Variants[]   │    │    CarrierInfo  │
└─────────────────┘    └─────────────────┘
```

---

### Типовая ошибка: God Aggregate

#### Что делают

Вместо 4–5 агрегатов создают **один большой**:

```php
final class Order
{
    // Корень
    private int $id;
    private string $customerId;
    private OrderStatus $status;

    // «Внутри» — всё, что связано с заказом
    private OrderLine[] $lines;
    private ShippingAddress $shipping;
    private PaymentInfo $payment;
    private Payment $paymentResult;       // ← факт оплаты
    private Invoice $invoice;             // ← счёт
    private Shipment $shipment;           // ← отправка
    private Customer $customer;           // ← клиент
    private Product[] $products;          // ← товары
    private Inventory[] $inventory;       // ← склад
    private NotificationLog[] $notifications;  // ← письма
}
```

#### Почему это плохо

| Проблема | Механизм |
|----------|----------|
| **Целостность размыта** | Инвариант `Payment.amount > 0` проверяется в `Order`. Но `Payment` меняется и вне `Order` (refund, dispute). Где тогда инвариант? |
| **Атомарность ложная** | `Order.save()` пытается записать 12 сущностей. Если упадёт на `Shipment` — `Payment` уже записан. Транзакция не атомарна. |
| **Конкуренция** | Два пользователя одновременно редактируют `Customer` и `Order` → deadlock на уровне строк |
| **Масштабируемость** | `Order` на 10k строк + `Lines` на 50k + `Shipment` на 10k = один `flush()` на 60k строк. Медленно. |
| **Тестируемость** | Unit test `Order` требует mock'ов 12 зависимостей |
| **Граница транзакции** | Неясно, где COMMIT. Один большой `flush()` или несколько? |
| **Изменение** | Добавили `GiftCard` → трогаете `Order`. Добавили `LoyaltyPoints` → трогаете `Order`. Класс растёт бесконечно. |

#### Конкретный баг, который я встречал

```php
// God Aggregate
$order->getPayment()->setStatus(PaymentStatus::Succeeded);
$order->getInvoice()->markAsPaid();
$order->getShipment()->createTracking('TRK123');
$repo->save($order);  // один flush, 12 сущностей

// Между save() и flush() — другой процесс:
// RefundService: $order->getPayment()->refund();
// → Конфликт: два процесса пишут в Payment одновременно
```

#### Как это выглядит в проде

- `OrderService` на 800 строк
- 15 методов, каждый из которых трогает 3–4 «вложенных» сущности
- 12 `@ORM\OneToMany` / `@ORM\OneToOne` на одном entity
- `flush()` занимает 2–5 секунд (12 таблиц, cascade)
- При добавлении новой фичи (gift cards) — трогаете `OrderService` → регрессия

---

### Как я определяю границы: алгоритм

```
1. Выпишите все сущности домена.
2. Для каждой пары (A, B) спросите:
   a) Есть ли инвариант, который затрагивает ОБЕ?
      → Да: один агрегат.
      → Нет: разные.
   b) Изменение A требует одновременного изменения B в той же транзакции?
      → Да: один агрегат.
      → Нет: разные.
   c) Снаружи вы всегда используете A и B вместе?
      → Да: один агрегат.
      → Нет: разные.
3. Если после шага 2 у вас агрегат > 3–5 сущностей:
   → Скорее всего, слишком большой. Разбейте.
   → Проверьте: какие инварианты на самом деле кросс-агрегатные?
   → Кросс-агрегатные инварианты → event / saga, не один flush.
4. Если агрегат < 2 сущностей (только корень):
   → Возможно, это не агрегат, а простая сущность.
```

---

### Кросс-агрегатные инварианты: как обрабатывать

Частый вопрос: «А если инвариант затрагивает два агрегата?»

Пример: `Payment.amount` не должен превышать `Order.total()`.

**Ошибочно:** поместить `Payment` в `Order` «для целостности».

**Правильно:**

```php
// Order Aggregate
final class Order
{
    public function total(): Money { /* ... */ }

    public function maxPaymentAmount(): Money
    {
        return $this->total();
    }
}

// Payment Aggregate
final class Payment
{
    public function __construct(Money $amount)
    {
        if ($amount->cents <= 0) {
            throw new \DomainException('Payment amount must be positive');
        }
        $this->amount = $amount;
    }

    public function canChargeFor(Order $order): bool
    {
        return $this->amount->cents <= $order->maxPaymentAmount()->cents;
    }
}

// Application Service (координатор)
final class ChargePaymentUseCase
{
    public function execute(int $orderId, int $paymentId): void
    {
        $order = $this->orderRepo->find($orderId);
        $payment = $this->paymentRepo->find($paymentId);

        // Кросс-агрегатная проверка — в приложении, не в агрегате
        if (!$payment->canChargeFor($order)) {
            throw new \DomainException('Payment exceeds order total');
        }

        $payment->markAsCharged();
        $order->markAsPaid();

        // Одна транзакция для обоих (но они — разные агрегаты)
        $this->em->flush();
    }
}
```

**Ключ:** проверка выполняется **в application layer**, не внутри агрегата. Агрегаты не знают друг о друге. Они знают только **свой** инвариант и **свой** API.

---

### Итог

| Принцип | Правило |
|---------|---------|
| Один агрегат = один инвариант | Если инвариант кросс-агрегатный — это не один агрегат |
| Маленький лучше большого | 3–5 сущностей — норма. 15 — тревога |
| Корень — единственная точка входа | Внешний код не дёргает `OrderLine` напрямую |
| Ссылки — через ID | `Order.customerId`, не `Order.customer` (объект) |
| Кросс-агрегатные правила — в application layer | Не в агрегате, не в entity, а в use case |
| Транзакция может касаться нескольких агрегатов | Но каждый агрегат — самодостаточен |

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Конкурентное изменение агрегата

### Что произойдёт без защиты: Lost Update

#### Сценарий

Два процесса одновременно добавляют строку в один `Invoice`:

```
T1 (Process A)                          T2 (Process B)
─────────────────                        ─────────────────
SELECT invoice WHERE id = 1
  → lines: [A(100), B(200)]
  → total: 300
                                    SELECT invoice WHERE id = 1
                                      → lines: [A(100), B(200)]
                                      → total: 300

addLine(C(50))
  → lines: [A, B, C]
  → total: 350
                                    addLine(D(80))
                                      → lines: [A, B, D]
                                      → total: 380

UPDATE invoices SET total = 350
   WHERE id = 1
UPDATE invoice_lines
   INSERT (C)
                                    UPDATE invoices SET total = 380
                                       WHERE id = 1
                                    UPDATE invoice_lines
                                       INSERT (D)
```

**Результат в БД:**

```
invoices:     total = 380  (последний UPDATE победил)
invoice_lines: A, B, D      (строка C пропала!)
```

Либо:

```
invoices:     total = 350  (если T1 записался последним)
invoice_lines: A, B, C      (строка D пропала!)
```

**Ни тот, ни другой процесс не знает, что его изменение потеряно.**

Это называется **Lost Update** — наиболее распространённая и опасная ошибка при конкурентном доступе.

---

### Изоляционные уровни PostgreSQL: что «из коробки»

| Уровень | Lost Update | Dirty Read | Non-repeatable Read | Phantom |
|---------|-------------|------------|--------------------|---------|
| `READ COMMITTED` (default) | **Да** | Нет | Нет | Да |
| `REPEATABLE READ` | **Да** | Нет | Нет | Нет* |
| `SERIALIZABLE` | Нет | Нет | Нет | Нет |

\* В PostgreSQL `REPEATABLE READ` не гарантирует absence of phantoms для `UPDATE/DELETE` (только для `SELECT`).

**Ключевой момент:** даже `REPEATABLE READ` **не защищает от lost update**. Два процесса могут одновременно прочитать одно значение и записать разные — PostgreSQL не поможет.

`SERIALIZABLE` защищает, но:
- Высокая цена: `SerializationError` при конфликте
- Требует retry
- Для большинства сценариев избыточно

**По умолчанию (READ COMMITTED):** lost update **возможен**. Защита — на уровне приложения.

---

### Стратегии защиты

#### Стратегия 1: Optimistic Locking (version field)

**Идея:** агрегат хранит версию. При `UPDATE` проверяем, что версия не изменилась.

```sql
ALTER TABLE invoices ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// Application Service
final class AddLineToInvoiceUseCase
{
    public function __construct(
        private InvoiceRepository $repo,
        private EntityManagerInterface $em,
    ) {}

    public function execute(int $invoiceId, int $productId, int $qty, int $priceCents): Invoice
    {
        $invoice = $this->repo->find($invoiceId);
        $version = $invoice->getVersion();

        // Доменная логика
        $invoice->addLine(new OrderLine($productId, $qty, new Money($priceCents, 'EUR')));
        $invoice->recalculateTotal();
        $invoice->bumpVersion();

        // UPDATE с проверкой версии
        $affected = $this->em->getConnection()->executeStatement(
            "UPDATE invoices
             SET total_cents = :total,
                 version = :newVersion
             WHERE id = :id
               AND version = :oldVersion",
            [
                'total'       => $invoice->total()->cents,
                'newVersion'  => $version + 1,
                'id'          => $invoiceId,
                'oldVersion'  => $version,
            ]
        );

        if ($affected === 0) {
            // Кто-то другой изменил invoice пока мы работали
            throw new ConcurrentModificationException(
                "Invoice #{$invoiceId} was modified by another process. Retry."
            );
        }

        // Теперь безопасно пишем lines (они зависят от invoice)
        $this->repo->saveLines($invoice);

        return $invoice;
    }
}
```

**Что произойдёт при конфликте:**

```
T1: UPDATE ... WHERE version = 3 → 1 row affected ✅
T2: UPDATE ... WHERE version = 3 → 0 rows affected ❌ → исключение
```

T2 получает `ConcurrentModificationException`. Вызывающая сторона решает:
- Retry (перечитать, пересчитать, повторить)
- Вернуть ошибку клиенту (409 Conflict)

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Нет блокировок → высокая конкурентность | Нужно поле `version` в каждой таблице |
| Простая логика | При частых конфликтах → много retry |
| Не деградирует под нагрузкой | Нужно обновлять version при каждом изменении |
| Легко тестировать | При N сущностях в агрегате — N UPDATE с version |

**Когда использовать:** дефолтный выбор для большинства агрегатов.

**Важно:** version должен быть **на корне агрегата**, не на каждой сущности. Изменение любой части агрегата → bump version корня.

---

#### Стратегия 2: Pessimistic Locking (SELECT FOR UPDATE)

**Идея:** заблокировать строку на время обработки.

```php
final class AddLineToInvoiceUseCase
{
    public function execute(int $invoiceId, ...): Invoice
    {
        // SELECT ... FOR UPDATE — блокирует строку
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->createQueryBuilder('i')
            ->where('i.id = :id')
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->setParameter('id', $invoiceId)
            ->getQuery()
            ->getOneOrNullResult();

        // На это время другие процессы ЖДУТ
        // (в пределах max_lock_timeout или deadlock_timeout)

        $invoice = $entity->toDomain();
        $invoice->addLine(...);
        $this->em->flush();

        // BLOK отпущен при COMMIT/ROLLBACK
        return $invoice;
    }
}
```

**Что произойдёт при конфликте:**

```
T1: SELECT ... FOR UPDATE → получил lock
T2: SELECT ... FOR UPDATE → ЖДЁТ (до max_lock_timeout)
T1: UPDATE → COMMIT → lock released
T2: получил lock → SELECT → UPDATE → COMMIT
```

T2 **не теряет** данные. Он просто **ждёт**.

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Нет lost update | Блокировка → снижение конкурентности |
| Простая логика (нет retry) | Deadlock при неправильном порядке |
| Строгие гарантии | Если T1 завис — T2 ждёт (max_lock_timeout) |
| | Под нагрузкой: очередь на lock → деградация |

**Когда использовать:**
- Критичные операции (списание средств)
- Низкая конкурентность (1–5 concurrent на объект)
- Нужен **строгий порядок** (FIFO)
- Операция короткая (< 100ms)

**Когда НЕ использовать:**
- Высокая конкурентность (100+ concurrent)
- Длительная обработка (> 1s)
- Риск deadlock

---

#### Стратегия 3: Атомарный UPDATE (без SELECT)

**Идея:** обходится без «прочитать → проверить → записать». Всё в одном SQL.

```sql
-- Списание средств: атомарно, без race condition
UPDATE accounts
SET balance_cents = balance_cents - :amount,
    version = version + 1
WHERE id = :id
  AND balance_cents >= :amount
RETURNING id, balance_cents;
```

**Что произойдёт при конфликте:**

```
T1: UPDATE ... WHERE balance >= 100 → 1 row (balance: 500 → 400)
T2: UPDATE ... WHERE balance >= 100 → 1 row (balance: 400 → 300)
T3: UPDATE ... WHERE balance >= 100 → 0 rows (balance: 300 < 100) → НЕ ХВАТАЕТ
```

Нет lost update. Нет блокировок на время обработки. PostgreSQL сам гарантирует атомарность `UPDATE`.

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Максимальная конкурентность | Нельзя выполнять сложную бизнес-логику |
| Нет retry | Ограничен простыми операциями (+, -, SET) |
| Нет блокировок | Не работает с коллекциями (lines) |
| Атомарно на уровне строки | |

**Когда использовать:**
- Операции «изменить одно число с проверкой»
- Баланс, счётчик, лимит
- `SET x = x + delta WHERE x >= 0`

**Когда НЕ использовать:**
- Нужно добавить/удалить элементы коллекции
- Сложная бизнес-логика (несколько условий)
- Кросс-табличные операции

---

#### Стратегия 4: Serializable Isolation

**Идея:** PostgreSQL сам детектирует конфликты и откатывает.

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        isolation: SERIALIZABLE
```

```php
$em->transactional(function () use ($invoiceId) {
    $invoice = $this->repo->find($invoiceId);
    $invoice->addLine(...);
    $this->em->flush();
    // COMMIT
});
// Если конфликт → SerializationError → retry
```

**Что произойдёт при конфликте:**

```
T1: BEGIN → SELECT → UPDATE → COMMIT ✅
T2: BEGIN → SELECT → UPDATE → COMMIT → SerializationError ❌
→ T2 откатывается → retry
```

**Trade-offs:**

| Плюс | Минус |
|------|-------|
| Нет lost update | Высокая цена: много откатов при конфликтах |
| Нет ручного version field | Нужен retry loop |
| Прозрачно | Не масштабируется под высокой нагрузкой |
| | Сложно диагностировать (SerializationError) |

**Когда использовать:**
- Редкие операции, где цена ошибки максимальна (финансы)
- Низкая конкурентность
- Когда невозможно выразить логику через `UPDATE ... WHERE`

**Когда НЕ использовать:**
- Высокая конкурентность (> 50 concurrent на объект)
- Частые конфликты → много retry → деградация
- Сложные multi-row операции

---

### Выбор стратегии

```
Вопрос: что делает операция?

├── Меняет одно число с проверкой (balance, counter)
│   → Атомарный UPDATE (Стратегия 3)
│
├── Добавляет/удаляет элементы коллекции
│   ├── Высокая конкурентность (> 10)
│   │   → Optimistic Locking (Стратегия 1)
│   └── Низкая конкурентность (< 5), нужен порядок
│       → Pessimistic Locking (Стратегия 2)
│
├── Кросс-агрегатная операция (2+ агрегата)
│   → Optimistic + Saga / TCC
│
└── Редкая критичная операция, цена ошибки максимальна
    → Serializable (Стратегия 4)
```

---

### Пример: баланс аккаунта (комбинация стратегий)

```php
final class TransferFundsUseCase
{
    public function __construct(
        private Connection $db,
    ) {}

    public function execute(string $fromId, string $toId, int $amountCents): void
    {
        $this->db->beginTransaction();

        try {
            // Атомарное списание (Стратегия 3)
            $affected = $this->db->executeStatement(
                "UPDATE accounts
                 SET balance_cents = balance_cents - :amount,
                     version = version + 1,
                     updated_at = now()
                 WHERE id = :from
                   AND balance_cents >= :amount",
                ['amount' => $amountCents, 'from' => $fromId]
            );

            if ($affected === 0) {
                throw new InsufficientBalanceException($fromId);
            }

            // Атомарное зачисление
            $this->db->executeStatement(
                "UPDATE accounts
                 SET balance_cents = balance_cents + :amount,
                     version = version + 1,
                     updated_at = now()
                 WHERE id = :to",
                ['amount' => $amountCents, 'to' => $toId]
            );

            // Аудит
            $this->db->executeStatement(
                "INSERT INTO transfers (from_id, to_id, amount_cents, created_at)
                 VALUES (:from, :to, :amount, now())",
                ['from' => $fromId, 'to' => $toId, 'amount' => $amountCents]
            );

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
```

**Почему это работает:**
- `WHERE balance_cents >= :amount` — атомарная проверка + изменение
- Два процесса одновременно списывают с одного аккаунта → один обновит, второй увидит `affected = 0`
- Нет SELECT → нет lost update
- Нет блокировок на время «обдумывания» → высокая конкурентность

**Когда не работает:**
- Нужно проверить «не было ли уже такой операции» (идемпотентность) → добавить `UNIQUE (from_id, to_id, idempotency_key)`
- Нужно уведомить → событие после COMMIT

---

### Типовые ошибки

#### ❌ 1. SELECT + UPDATE без version / lock

```php
$balance = $db->query("SELECT balance FROM accounts WHERE id = ?", [$id]);
if ($balance >= $amount) {
    $db->query("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $id]);
}
// GAP: между SELECT и UPDATE другой процесс списал. Lost update.
```

#### ❌ 2. Pessimistic lock в цикле

```php
foreach ($1000_invoices as $invoice) {
    $entity = $em->getRepository(Invoice::class)
        ->createQueryBuilder('i')
        ->setLockMode(PESSIMISTIC_WRITE)
        ->getQuery()
        ->getOneResult();
    // 1000 блокировок в одной транзакции → deadlock
}
```

**Правильно:** оптимистичный lock + batch, или разбить на маленькие транзакции.

#### ❌ 3. `SERIALIZABLE` + длинная транзакция

```php
$em->transactional(function () {
    $invoice = $repo->find($id);
    // ... 3 секунды обработки ...
    $em->flush();
    // COMMIT
});
// Под SERIALIZABLE: 3 сек удержания снапшота → много SerializationError
```

#### ❌ 4. Version field, но не все UPDATE его проверяют

```php
// ✅ Правильный UPDATE
UPDATE invoices SET total = :t, version = :v + 1 WHERE id = :id AND version = :v;

// ❌ Неправильный (забыл version)
UPDATE invoice_lines SET quantity = :q WHERE id = :lineId;
// → Lost update на lines, хотя invoice защищён
```

**Правило:** если агрегат имеет version — **все** UPDATE по всем таблицам агрегата должны проверять version корня. Либо использовать `cascade` + один `flush()`.

#### ❌ 5. Optimistic lock без retry

```php
try {
    $this->repo->save($invoice);
} catch (ConcurrentModificationException $e) {
    // ❌ Просто бросаем ошибку клиенту
    throw $e;
}
```

**Правильно:**

```php
for ($attempt = 0; $attempt < 3; $attempt++) {
    try {
        return $this->execute();
    } catch (ConcurrentModificationException $e) {
        if ($attempt === 2) {
            throw $e;
        }
        // Перечитать, пересчитать, повторить
    }
}
```

---

### Итоговая таблица

| Стратегия | Когда | Конкурентность | Retry? | Блокировка? |
|-----------|-------|---------------|--------|-------------|
| Атомарный `UPDATE` | Один столбец, проверка + изменение | Очень высокая | Нет | Нет |
| Optimistic (version) | Коллекции, сложная логика | Высокая | Да | Нет |
| Pessimistic (FOR UPDATE) | Низкая конкурентность, порядок | Низкая | Нет | Да |
| Serializable | Редкие критичные операции | Низкая | Да (автоматически) | Soft |

**Дефолтный выбор для большинства агрегатов: Optimistic Locking + retry.**

> ⏱ 35.8s · prompt 57839 tok · output 3758 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

The user is asking about domain events in a DDD/PHP/Symfony context. This is a deep architectural question. Let me structure my answer around:

1. What domain events are (briefly)
2. When to publish them
3. How to handle them
4. What problems they solve
5. What problems they create
6. The critical relationship between domain events and DB transactions

I should be concrete with PHP/Symfony code examples, show the transactional outbox pattern, discuss sync vs async processing, and be honest about trade-offs. I've been building a coherent narrative throughout this interview — this is a natural continuation covering events, which I've already touched on in the messenger/async section and the aggregate section.

Let me think about the key points:

- Domain event = "something happened in the domain that others may care about"
- Publishing: inside the aggregate, collected, dispatched at the use case boundary
- Handling: sync (in same transaction) vs async (via message bus)
- Transactional Outbox pattern — the critical link between events and transactions
- Problems solved: decoupling, audit trail, event sourcing foundation, cross-aggregate coordination
- Problems created: eventual consistency, ordering, idempotency, debugging, complexity
- Common mistakes: publishing inside aggregate (wrong), publishing before flush (bad), treating events as RPC

Let me structure this well with code examples, a clear table of when to use events vs direct calls, and the transactional outbox pattern in detail.

</details>

**Кандидат:**

## Доменные события: публикация, обработка, связь с транзакциями

### 1. Что такое доменное событие

Доменное событие — это **факт, что что-то произошло в домене**, и другие части системы могут на это отреагировать.

```php
// Это доменное событие
new InvoicePaid(invoiceId: 42, amount: new Money(1990, 'EUR'), paidAt: new \DateTimeImmutable());

// Это НЕ доменное событие
new SendEmail(to: 'user@x.com', subject: 'Hello');  // ← это команда, не факт
new IndexUser(userId: 42);                           // ← это команда
new RecalculateTotal(invoiceId: 42);                 // ← это запрос
```

**Критерий:** если событие можно выразить как «X произошло в момент T» и это **факт, а не действие** — это доменное событие.

---

### 2. Когда публиковать

#### Правильно: на границе use case, после flush

```php
final class PayInvoiceUseCase
{
    public function execute(int $invoiceId, string $paymentToken): void
    {
        // 1. Доменная логика
        $invoice = $this->repo->find($invoiceId);
        $invoice->pay($this->gateway->charge($paymentToken));

        // 2. Персистентность
        $this->repo->save($invoice);

        // 3. Flush — данные в БД
        $this->em->flush();

        // 4. ПУБЛИКАЦИЯ СОБЫТИЯ — после того, как данные гарантированно в БД
        $this->bus->dispatch(new InvoicePaid(
            invoiceId: $invoiceId,
            amount:    $invoice->total(),
            paidAt:    new \DateTimeImmutable(),
        ));
    }
}
```

#### Неправильно: внутри агрегата

```php
// ❌
final class Invoice
{
    public function pay(PaymentResult $result): void
    {
        $this->status = InvoiceStatus::Paid;
        $this->events[] = new InvoicePaid(...);  // ← агрегат знает о событиях
    }
}
```

**Почему плохо:**
- Агрегат зависит от инфраструктуры (MessageBus, EventDispatcher).
- Трудно тестировать (нужен mock'а диспетчера).
- Агрегат «знает», что кто-то на это подпишется. Это нарушение инкапсуляции.

#### Неправильно: до flush

```php
// ❌
$invoice->pay($result);
$this->bus->dispatch(new InvoicePaid(...));  // ← событие отправлено
$this->em->flush();                          // ← БД упала → событие в полёте, данных нет
```

**Почему плохо:** слушатель получил событие, но данных в БД нет. Если слушатель пытается прочитать invoice — `NotFound`.

---

### 3. Как обрабатывать: sync vs async

#### Sync — в той же транзакции

```php
// Symfony Messenger: sync transport
// messenger.yaml
framework:
    messenger:
        transports:
            sync: 'sync://'

// Все слушатели выполняются ДО COMMIT
```

```php
final class RecalculateReportListener
{
    public function __invoke(InvoicePaidEvent $event): void
    {
        // Выполняется в той же транзакции
        // Если упадёт → ROLLBACK → и invoice, и отчёт откатываются
        $this->reportService->recalculate($event->invoiceId);
    }
}
```

**Когда:**
- Результат **обязателен** для основного сценария.
- Нужна **атомарность** с основным действием.
- Задержка критична (пользователь ждёт).

**Пример:** «после оплаты — обновить статус в системе отчётов». Если отчёт не обновится — пользователь видит непоследовательное состояние.

---

#### Async — через очередь

```php
// messenger.yaml
framework:
    messenger:
        transports:
            async: 'amqp://localhost/events'
            routing:
                'App\Event\InvoicePaidEvent': async
```

```php
final class SendReceiptListener
{
    #[AsMessageHandler(fromTransport: 'async')]
    public function __invoke(InvoicePaidEvent $event): void
    {
        // Вызывается воркером, в ОТДЕЛЬНОЙ транзакции
        // Если упадёт → retry, не влияет на основной сценарий
        $this->email->sendReceipt($event->invoiceId);
    }
}
```

**Когда:**
- Побочный эффект (письмо, индекс, аналитика).
- Допустима задержка (секунды/минуты).
- Не должен блокировать основной сценарий.

---

#### Сравнение

| Критерий | Sync | Async |
|----------|------|-------|
| Атомарность с основной операцией | Да | Нет |
| Задержка | 0 (в той же транзакции) | Секунды–минуты |
| Влияние сбоя на основной сценарий | Да (ROLLBACK) | Нет |
| Масштабируемость | Ограничена одной транзакцией | Горизонтально (воркеры) |
| Сложность отладки | Проще (один trace) | Сложнее (несколько процессов) |
| Идемпотентность | Не нужна | **Обязательна** |

---

### 4. Транзакциональный Outbox — связующее звено

#### Проблема

```php
// ❌ Атомарность нарушена
$this->em->flush();           // COMMIT: invoice в БД
$this->bus->dispatch(event);  // ОШИБКА: событие потеряно
// Invoice в БД, но ни один слушатель не узнал
```

Или:

```php
// ❌ Обратная проблема
$this->bus->dispatch(event);  // Событие в очереди
$this->em->flush();           // ОШИБКА: ROLLBACK
// Invoice НЕ в БД, но событие уже в полёте
// Слушатель читает invoice → NotFound
```

#### Решение: Transactional Outbox

**Идея:** событие записывается в **ту же таблицу БД** в той же транзакции. Отдельный процесс (outbox poller) читает из таблицы и публикует в очередь.

```
┌──────────────────────────────────────────────────────────────┐
│  TRANSACTION                                                 │
│                                                              │
│  1. UPDATE invoices SET status = 'paid' WHERE id = 42       │
│  2. INSERT INTO outbox (aggregate_id, event, payload,        │
│                        published_at) VALUES                  │
│       (42, 'InvoicePaid', '{"amount":1990,...}', NULL)       │
│                                                              │
│  COMMIT  ← оба записаны атомарно                             │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  OUTBOX POLLER (отдельный процесс)                            │
│                                                              │
│  LOOP:                                                       │
│    SELECT id, event, payload FROM outbox                     │
│    WHERE published_at IS NULL                                │
│    ORDER BY created_at                                       │
│    LIMIT 100                                                 │
│    FOR UPDATE SKIP LOCKED                                    │
│                                                              │
│    FOR each row:                                             │
│      PUBLISH to RabbitMQ / Kafka                             │
│      UPDATE outbox SET published_at = now() WHERE id = X     │
└──────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌──────────────────────────────────────────────────────────────┐
│  WORKERS                                                     │
│                                                              │
│  SendReceiptWorker    → email                                │
│  IndexWorker          → search                               │
│  AnalyticsWorker      → data pipeline                        │
└──────────────────────────────────────────────────────────────┘
```

#### Реализация

**1. Таблица outbox**

```sql
CREATE TABLE outbox (
    id            BIGSERIAL PRIMARY KEY,
    aggregate_id  INT NOT NULL,
    event_type    VARCHAR(100) NOT NULL,
    payload       JSONB NOT NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),
    published_at  TIMESTAMPTZ,
    published_by  VARCHAR(50)
);

CREATE INDEX idx_outbox_unpublished
    ON outbox (created_at)
    WHERE published_at IS NULL;
```

**2. Запись в outbox (в той же транзакции)**

```php
final class PayInvoiceUseCase
{
    public function __construct(
        private InvoiceRepository $repo,
        private EntityManagerInterface $em,
        private Connection $db,
    ) {}

    public function execute(int $invoiceId, string $paymentToken): void
    {
        $this->em->transactional(function () use ($invoiceId, $paymentToken) {
            // 1. Доменная логика
            $invoice = $this->repo->find($invoiceId);
            $invoice->pay($this->gateway->charge($paymentToken));

            // 2. Сохранение
            $this->repo->save($invoice);

            // 3. Запись события в OUTBOX (таблица БД, не очередь!)
            $event = new InvoicePaid(
                invoiceId: $invoiceId,
                amount:    $invoice->total(),
                paidAt:    new \DateTimeImmutable(),
            );

            $this->db->executeStatement(
                "INSERT INTO outbox (aggregate_id, event_type, payload)
                 VALUES (:agg, :type, :payload)",
                [
                    'agg'     => $invoiceId,
                    'type'    => InvoicePaid::class,
                    'payload' => json_encode([
                        'invoice_id' => $invoiceId,
                        'amount'     => $invoice->total()->cents,
                        'currency'   => $invoice->total()->currencyCode,
                        'paid_at'    => $event->paidAt->format(\DateTimeInterface::ATOM),
                    ]),
                ]
            );

            // 4. Flush — invoice И outbox в одной транзакции
            $this->em->flush();
        });
        // COMMIT → оба записаны. Или оба потеряны при ROLLBACK.
    }
}
```

**3. Outbox poller**

```php
#[AsCommand(name: 'outbox:publish')]
final class OutboxPublishCommand extends Command
{
    public function __construct(
        private Connection $db,
        private ProducerInterface $producer,  // RabbitMQ / Kafka
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $rows = $this->db->executeQuery(
                "SELECT id, event_type, payload, created_at
                 FROM outbox
                 WHERE published_at IS NULL
                 ORDER BY created_at
                 LIMIT 100
                 FOR UPDATE SKIP LOCKED"
            )->fetchAllAssociative();

            if (empty($rows)) {
                usleep(500_000); // 0.5s
                continue;
            }

            $ids = [];
            foreach ($rows as $row) {
                $this->producer->publish(
                    exchange: 'domain_events',
                    routingKey: $row['event_type'],
                    body: $row['payload'],
                    headers: [
                        'event_type' => $row['event_type'],
                        'occurred_at' => $row['created_at'],
                    ]
                );
                $ids[] = $row['id'];
            }

            // Отметить как опубликованные
            $this->db->executeStatement(
                "UPDATE outbox SET published_at = now() WHERE id IN (" .
                implode(',', $ids) . ")"
            );

            $this->db->commit();
        }
    }
}
```

**4. Worker (потребитель)**

```php
#[AsMessageHandler(fromTransport: 'events')]
final class SendReceiptListener
{
    public function __construct(
        private EmailService $email,
        private InvoiceRepository $repo,
    ) {}

    public function __invoke(string $eventType, string $payload): void
    {
        if ($eventType !== InvoicePaid::class) {
            return;
        }

        $data = json_decode($payload, true);
        $invoiceId = $data['invoice_id'];

        // Идемпотентность: проверить, не отправляли ли уже
        $invoice = $this->repo->find($invoiceId);
        if ($invoice->receiptSentAt !== null) {
            return; // Уже отправлено
        }

        $this->email->sendReceipt($invoice);
        $invoice->markReceiptSent();
        $this->repo->save($invoice);
    }
}
```

---

### 5. Что события решают

| Проблема | Как событие помогает |
|----------|---------------------|
| **Связанность.** `OrderService` знает про `EmailService`, `SearchService`, `AnalyticsService` | Каждый слушатель — отдельный класс. `OrderService` не знает про них |
| **Расширяемость.** Новый потребитель — правка `OrderService` | Новый потребитель — новый listener, ноль правок |
| **Наблюдаемость.** «Почему письмо не пришло?» — нужно копаться в `OrderService` | Событие в outbox → видно в логах, в метриках, в DLQ |
| **Асинхронность.** Основной сценарий ждёт 5 побочников | Основной сценарий завершается за 10ms. Побочники — воркеры |
| **Аудит.** Кто, когда, что сделал | Outbox = журнал событий (foundation для event sourcing) |
| **Кросс-агрегатная координация.** `Payment` должен узнать, что `Order` подтверждён | Событие `OrderConfirmed` → listener в `Payment` |

---

### 6. Что события создают (проблемы)

| Проблема | Механизм | Решение |
|----------|----------|---------|
| **Eventual consistency.** Данные в БД и состояние слушателя расходятся на N секунд | Async обработка | Документировать. UI должен показывать «в обработке» |
| **Порядок.** `InvoicePaid` и `InvoiceRefunded` могут прийти в перевернутом порядке | Очередь не гарантирует порядок между partition'ами | Idempotency + sequence number в payload |
| **Идемпотентность.** Воркер перезапускает сообщение → двойная обработка | Retry, crash, rebalance | Проверить «уже обработано?» перед обработкой |
| **Отладка.** 5 систем участвуют, ошибка в 3-й | Асинхронность | Корреляция: `correlation_id` в каждом событии |
| **Сложность.** «Просто отправить письмо» → outbox + poller + worker + DLQ | Инфраструктура | Только если действительно нужно. Для 1 письма — sync listener |
| **Горячие точки.** Один слушатель тормозит → очередь растёт → другие события ждут | Совместный транспорт | Отдельные очереди на слушателя |
| **Отложенные сбои.** Основная операция успешна, но побочник упал 3 раза → в DLQ | Silent failure | Мониторинг DLQ + алерт |

---

### 7. Антипаттерны

#### ❌ 1. Событие как RPC

```php
// ❌ «Событие» на самом деле — команда
new SendEmail(to: 'user@x.com', subject: 'Hello');
new IndexDocument(docId: 42);
new RecalculateReport(invoiceId: 42);
```

Это **команды**, не события. Они не факт, а действие. Их нужно отправлять как **задачи** (message bus, task queue), а не как доменные события.

**Как отличить:**
- «Invoice **paid**» → событие (факт)
- «Send **email**» → команда (действие)
- «Recalculate **report**» → команда (действие)

#### ❌ 2. Слишком много слушателей

```php
// 12 слушателей на InvoicePaid
class SendEmailListener { ... }
class SendSmsListener { ... }
class UpdateAnalyticsListener { ... }
class IndexListener { ... }
class NotifyErpListener { ... }
class UpdateDashboardListener { ... }
class WriteAuditLogListener { ... }
class PushNotificationListener { ... }
class SyncToCrmListener { ... }
class UpdateLoyaltyListener { ... }
class GenerateInvoiceListener { ... }
class WebhookListener { ... }
```

**Проблема:** неясно, что **критично**, а что — nice-to-have. Если `UpdateAnalytics` упало — это баг?

**Решение:** разделить на:
- **Критичные** (sync, в транзакции): `UpdateAuditLog`
- **Важные** (async, с retry): `SendEmail`, `IndexListener`
- **Nice-to-have** (async, без retry): `UpdateAnalytics`, `WebhookListener`

#### ❌ 3. Слушатель мутирует состояние другого агрегата

```php
class InvoicePaidListener
{
    public function __invoke(InvoicePaidEvent $event): void
    {
        $order = $this->orderRepo->find($event->orderId);
        $order->markAsPaid();  // ← mutation другого агрегата
        $this->orderRepo->save($order);
    }
}
```

**Проблема:**
- Order и Invoice — разные агрегаты.
- Listener в одной транзакции меняет оба → тесная связь.
- Если listener упадёт после изменения Order, но до изменения Invoice → inconsistency.

**Правильно:**
- Listener **читает** Invoice, **принимает решение** в Order.
- Изменение Order — в отдельной транзакции.
- Если Order должен быть «paid» атомарно с Invoice → они должны быть **в одном агрегате** или в **одном use case** с явной транзакцией.

#### ❌ 4. Событие с «тяжёлым» payload

```php
new InvoicePaid(
    invoiceId: 42,
    lines: [10 строк заказа],  // ← 10 объектов в JSON
    customer: [полный объект],
    // ... 50 строк JSON
);
```

**Проблема:**
- Очередь раздувается.
- Слушатель десериализует 50 строк, чтобы использовать 2.
- Изменили `InvoiceLine` → сломали все слушатели.

**Правильно:**
- В событии — **минимум** (ID + ключевые факты).
- Слушатель **запрашивает** детали по ID, если нужно.

```php
new InvoicePaid(
    invoiceId: 42,
    amount:    new Money(1990, 'EUR'),
    paidAt:    new \DateTimeImmutable(),
    customerId: 'cust_123',
);
// Слушатель: $this->invoiceRepo->find(42) → full object
```

#### ❌ 5. Событие без версии

```php
// v1
new InvoicePaid(invoiceId: 42, amount: 1990);

// v2 (добавили поле)
new InvoicePaid(invoiceId: 42, amount: 1990, currency: 'EUR');

// Старый воркер читает v2 → currency = null → баг
```

**Решение:**

```php
class InvoicePaid
{
    public const VERSION = 2;

    public function __construct(
        public int    $invoiceId,
        public int    $amountCents,
        public string $currencyCode,
        public string $paidAt,
        public int    $version = self::VERSION,
    ) {}
}
```

Старый воркер видит `version = 2` → обрабатывает по старой схеме (ищет `amount`, игнорирует `currencyCode`).

---

### 8. Связь с транзакциями: итоговая схема

```
┌─────────────────────────────────────────────────────────────────────┐
│  USE CASE (единая транзакция)                                       │
│                                                                     │
│  BEGIN                                                              │
│    1. Доменная логика (aggregate)                                   │
│    2. $em->persist($entity)                                         │
│    3. INSERT INTO outbox (event)  ← в той же транзакции            │
│    4. $em->flush()                                                  │
│  COMMIT                                                             │
│                                                                     │
│  → Данные И событие атомарно в БД                                   │
│  → Слушатели ЕЩЁ НЕ получили событие                                │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│  OUTBOX POLLER                                                      │
│                                                                     │
│  LOOP:                                                              │
│    SELECT ... FROM outbox WHERE published_at IS NULL                │
│    FOR UPDATE SKIP LOCKED                                           │
│                                                                     │
│    PUBLISH to broker                                                │
│    UPDATE outbox SET published_at = now()                           │
│                                                                     │
│  → Событие в broker, запись в outbox закрывает «окно»              │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│  WORKERS (каждый — своя транзакция)                                  │
│                                                                     │
│  BEGIN                                                              │
│    1. Идемпотентность: проверено? → skip                            │
│    2. Бизнес-логика                                                │
│    3. $em->flush()                                                 │
│  COMMIT                                                             │
│                                                                     │
│  ОШИБКА → ROLLBACK → broker requeue → retry                         │
│  3 неудачных retry → DLQ → алерт                                    │
└─────────────────────────────────────────────────────────────────────┘
```

**Ключевой принцип:**

> **Событие — это запись в БД, а не вызов в очередь.**
>
> Очередь — транспорт. БД — источник правды.
> Если событие в outbox — оно гарантированно доставлено.
> Если событие в broker, но не в БД — оно может быть потеряно.

---

### 9. Когда НЕ нужны доменные события

| Ситуация | Почему |
|----------|--------|
| 1 слушатель, sync, в той же транзакции | Просто вызовите метод. Событие — overhead |
| Внутренняя реализация класса | `private` метод, не событие |
| «Событие» на самом деле команда | Это task, не event |
| 2 сущности, один use case | Просто вызовите второй метод в том же use case |
| Прототип / MVP | Добавьте позже. Не усложняйте |

**Правило:** доменное событие оправдано, когда **≥ 2 независимых потребителя** и/или **асинхронность** и/или **расширяемость** (новые потребители будут появляться).

---

### 10. Чеклист

```
□ Событие — факт, а не действие?
□ Публикация после flush, в той же транзакции?
□ Payload минимален (ID + ключевые поля)?
□ Версия в payload?
□ Слушатель идемпотентен?
□ Отдельная очередь на критичных слушателей?
□ DLQ + алерт настроены?
□ Correlation_id для трассировки?
□ Событие не мутирует другой агрегат (или — осознанно)?
□ Документировано: что происходит, если слушатель упал?
```

> ⏱ 55.4s · prompt 65453 tok · output 5905 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Разбираю запрос пользователя: три связанных вопроса по DDD — (1) отличия Value Object от Entity, (2) критерии выбора, (3) Ubiquitous Language и его влияние на код. Это собеседование на PHP-бэкенд, поэтому нужно отвечать на уровне senior-разработчика: с конкретикой, примерами кода, trade-offs, типовыми ошибками. Не надо раздувать до эссе — но и не уходить в поверхностное перечисление. Структурирую: сначала чёткое различие с критерием (а не просто определением), потом алгоритм выбора с примерами и пограничными случаями, потом Ubiquitous Language с конкретикой — как она проявляется в именах, в антипаттернах. Использую один сквозной пример (Money/Address/Invoice), чтобы показать последовательно. Включу пограничные случаи — это то, что отличает senior-ответ: «а что, если...». Отвечаю на русском, с кодом на PHP.

</details>

**Кандидат:**

## Value Object vs Entity: различие, выбор, единый язык

### 1. Фундаментальное различие

| | Entity | Value Object |
|---|--------|-------------|
| **Идентичность** | По ID (устойчивое во времени) | По значению (равенство полей) |
| **Изменяемость** | Изменяется (lifecycle) | Immutable (меняется заменой) |
| **Равенство** | `$a->id === $b->id` | `$a->fields == $b->fields` |
| **Смысл** | «Кто это?» | «Что это?» |
| **Обмен** | Две сущности с одинаковыми полями — разные | Два объекта с одинаковыми полями — один и тот же |

Критерий в одной фразе:

> **Если я могу заменить один экземпляр другим с одинаковыми полями, и ни один инвариант не нарушится — это Value Object.**
>
> **Если замена нарушает инварианты (потеря истории, ссылок, состояния) — это Entity.**

---

### 2. Пример на одном домене

```php
// ─── Value Object ─────────────────────────────────────────
final readonly class Money
{
    public function __construct(
        public int    $cents,
        public string $currency,
    ) {
        if ($cents < 0) throw new \DomainException('negative');
        if (!preg_match('/^[A-Z]{3}$/', $currency)) throw new \DomainException('bad currency');
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents
            && $this->currency === $other->currency;
    }
}

// Два Money(100, 'EUR') — один и тот же.
// Заменю один другим — ничего не изменится.
// История у Money нет: 100 EUR в 2020 и 100 EUR в 2025 — одно и то же значение.
```

```php
// ─── Entity ─────────────────────────────────────────────────
final class Invoice
{
    private int $id;                  // ← идентичность
    private InvoiceStatus $status;    // ← состояние (меняется)
    private \DateTimeImmutable $createdAt;
    private array $lines = [];

    public function pay(): void
    {
        if ($this->status !== InvoiceStatus::Issued) {
            throw new \DomainException('can only pay issued');
        }
        $this->status = InvoiceStatus::Paid;   // ← мутация
    }
}

// Два Invoice с одинаковыми полями — РАЗНЫЕ (разные id, разная история).
// Заменю Invoice#42 на Invoice#43 с теми же полями —
// потеряю историю платежей, ссылки из других систем, audit trail.
```

**Почему `Money` — VO, а `Invoice` — Entity:**

| Проверка | Money | Invoice |
|----------|-------|---------|
| Есть устойчивый ID? | Нет | Да |
| Меняется состояние? | Нет (immutable) | Да (draft → issued → paid) |
| Есть история/жизненный цикл? | Нет | Да |
| Ссылки извне? | Нет (значение) | Да (payment references, audit) |
| Замена на эквивалент безопасна? | Да | Нет |

---

### 3. Пограничные случаи (где легко ошибиться)

#### Случай 1: `Address`

```php
final readonly class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zip,
        public string $country,
    ) {}
}
```

**Вопрос:** это VO или Entity?

**Ответ:** зависит от домена.

- **В e-commerce (delivery address)** → **VO**. Адрес — значение. `Address("ул. Ленина, 5", "Москва")` равен себе. Заменяю на другой с теми же полями — безопасно.
- **В CRM (адрес клиента с историей)** → **Entity**. Есть ID, есть история изменений («адрес изменён 3 раза»), есть ссылки из документов.

**Правило:** не спрашивай «что такое адрес?». Спроси: *«В этом домене адрес — значение или участник с историей?»*

---

#### Случай 2: `Product`

```php
final class Product
{
    public int $id;
    public string $name;
    public Money $price;
    public string $sku;
}
```

**Обычно:** Entity (есть ID, есть lifecycle: draft → active → archived).

**Но:** внутри `Order` цена товара — это **снимок** (snapshot), а не ссылка на `Product`.

```php
final readonly class OrderLine
{
    public function __construct(
        public int $productId,       // ссылка на Product (Entity)
        public int $quantity,
        public Money $unitPrice,     // СНИМОК — VO на момент заказа
    ) {}
}
```

Если бы `unitPrice` был ссылкой на `Product` — смена цены товара задним числом ломала бы старые заказы. `Money` как VO фиксирует значение на момент времени.

**Правило:** при закреплении сущности из «живого» мира в «исторический» — превращайте её в VO (snapshot).

---

#### Случай 3: `Email`

```php
final readonly class Email
{
    public function __construct(public string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("Invalid: {$address}");
        }
    }
}
```

**VO.** Идентичность — значение. `Email("a@b.c") === Email("a@b.c")`.

Но: если `Email` — **verified email** с историей (verified_at, verification_token, attempts) — это уже **Entity**.

**Правило:** VO — «что». Entity — «что + когда + что произошло».

---

#### Случай 4: `Order` vs `OrderLine`

- `Order` — **Aggregate Root** (Entity).
- `OrderLine` — **Entity** внутри агрегата (свой ID, может меняться: quantity).
- `Money` — **VO** (не меняется, равен по значению).

`OrderLine` — Entity, а не VO, потому что:
- имеет ID (нужен для UPDATE отдельной строки),
- количество может меняться (mutate),
- на неё ссылаются (audit, history).

Если бы `OrderLine` был VO — сменить quantity значило бы заменить объект целиком, потерять ID, сломать change tracking.

---

### 4. Алгоритм выбора

```
1. Есть ли устойчивый ID?
   ├── Да → Entity
   └── Нет → п. 2

2. Есть ли lifecycle (состояния, переходы)?
   ├── Да → Entity
   └── Нет → п. 3

3. Меняется ли после создания?
   ├── Да → Entity
   └── Нет → п. 4

4. На неё ссылаются извне (audit, history)?
   ├── Да → Entity
   └── Нет → Value Object
```

**Эвристика:** если сомневаешься — VO. Immutable по умолчанию. Сделать Entity потом легче, чем превратить Entity в VO (потеря immutable-семантики).

---

### 5. Ubiquitous Language (единый язык)

#### Что это

Ubiquitous Language — это **совпадение терминов** между:
- разговором бизнес-аналитика,
- кодом разработчика,
- тестами,
- документацией,
- сообщениями в очереди.

Если один и тот же концепт называется по-разному в коде и в домене — у вас **нет** Ubiquitous Language.

#### Пример: есть и нет

**Без UL:**

```php
// Бэкенд-код
class Order {
    public $customer;          // «customer» — это client? user? account?
    public $items;             // «items» — это lines? positions? products?
    public $total;             // «total» — с НДС? без? в копейках?
    public $status;            // «status» — какие значения?
}

// Аналитик на встрече:
«Отправь invoice клиенту, когда payment пройдёт»

// Разработчик:
// customer? invoice? payment? total? status? — 4 разных термина для 2 концептов
```

**С UL:**

```
Договорённости команды:
  Invoice       — документ, выставленный клиенту
  Line          — строка в invoice (НЕ "item")
  Money         — сумма с валютой (НЕ "amount" + "currency")
  Issued        — статус «выставлен» (НЕ "sent", "created")
  Paid          — статус «оплачен» (НЕ "completed", "done")
  Customer      — внешний контрагент (НЕ "user", "client")
```

```php
// Код отражает язык
final class Invoice
{
    private readonly Customer $customer;
    private readonly array $lines;
    private readonly Money $total;
    private InvoiceStatus $status;   // Draft | Issued | Paid | Cancelled
}

// Тесты
it('marks invoice as Paid when payment succeeds', function () { ... });

// Событие
new InvoicePaid(invoiceId: 42, total: Money::eur(1990), at: now());

// Очередь
routing_key: 'invoice.paid'

// Логи
[INFO] invoice#42 paid total=1990EUR
```

**Один концепт — одно имя. Во всех слоях.**

#### Как UL влияет на код

**1. Имена говорят сами за себя**

```php
// Без UL — нужно читать код, чтобы понять
$service->process($obj, $config);

// С UL — понятно по имени
$invoiceService->issue($invoice);
$paymentService->charge($payment);
```

**2. Код читается как спецификация**

```php
// Читаю и понимаю бизнес-правила без комментариев
public function pay(): void
{
    if ($this->status !== InvoiceStatus::Issued) {
        throw new \DomainException('Only issued invoices can be paid');
    }
    if (count($this->lines) === 0) {
        throw new \DomainException('Cannot pay empty invoice');
    }
    $this->status = InvoiceStatus::Paid;
}
```

Без UL этот код был бы:

```php
// status === 'ok' и items.length > 0 и ... ?
// А что такое 'ok'? А что такое 'pending'? А 'failed'?
```

**3. Тесты = спецификация**

```php
// Тест читается как документ бизнес-правил
it('cannot pay a cancelled invoice', ...);
it('cannot pay an invoice with no lines', ...);
it('marks invoice as Paid when payment succeeds', ...);
```

**4. Передача знаний**

Новый разработчик читает код и понимает домен **без встречи с аналитиком**. Термины в коде = термины в домене.

**5. Границы микросервисов**

Когда термины согласованы, становится очевидным, где граница:

```
Invoice сервис:   Invoice, Line, Customer
Payment сервис:   Payment, Transaction, Gateway

Invoice и Payment — разные концепты с разными терминами.
Граница микросервисов = граница языка.
```

---

#### Антипаттерны UL

| Антипаттерн | Пример | Почему плохо |
|-------------|--------|--------------|
| **Синонимы** | `customer`, `client`, `user`, `account` — один и тот же объект | Кто кто? Трудно читать, легко ошибиться |
| **Акронимы без расшифровки** | `PO`, `SKU`, `EAN` в разных контекстах | Новичок не понимает, что «PO» — Purchase Order, а не Point of Sale |
| **Технические термины в домене** | `record`, `document`, `entity` вместо доменных слов | Смешение слоёв |
| **Доменные термины в технике** | `invoice` как имя таблицы, колонки, переменной | Связка домена с инфраструктурой |
| **Одно слово — два смысла** | `amount` = сумма в заказе И сумма в оплате | Коллизия. Разделите: `orderTotal`, `paymentAmount` |

---

#### Как вводить UL

1. **Event Storming** — команда (дев + аналитик) прогоняет сценарии, записывает термины на стикеры.
2. **Глоссарий** — 1–2 страницы: термин → определение → пример → анти-пример.
3. **Code review** — «Это `customer` или `client`? У нас один термин — `Customer`».
4. **Тесты** — имена тестов используют глоссарий.
5. **Коммиты** — «fix invoice: cannot pay cancelled» — термин из глоссария.
6. **Сообщения** — `invoice.paid` в очереди — термин из глоссария.

---

### 6. Итоговая связка

```
Ubiquitous Language
        │
        ▼
    Код: имена = доменные термины
        │
        ▼
    Инкапсуляция терминов
        │
        ├── Money, Address, Email   → Value Objects
        └── Invoice, Customer, Product → Entities / Aggregate Roots
```

**Связь:** Ubiquitous Language **предшествует** решению «VO или Entity». Сначала договариваетесь о терминах и смысле. Потом решаете, каждый термин — значение или участник с историей.

Если начинать с кода (создать классы) и потом подбирать термины — у вас нет UL, есть «код с комментариями о домене». Это два разных продукта.

> ⏱ 40.3s · prompt 71103 tok · output 3493 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о чистой архитектуре (Clean Architecture) в PHP-приложении: слои, правило зависимостей, куда попадают фреймворк и инфраструктура, как следить за границами. Это продолжение длинного интервью-разговора о PHP/Symfony/DDD. Нужно ответить конкретно, с примерами кода и структуры каталогов, объяснить правило зависимостей (зависимости всегда наружу → внутрь), разобрать конкретные артефакты: Doctrine, контроллер Symfony, обработчик очереди. И дать практические механизмы слежения за границами: PHPStan rules, ArchUnit-подобные вещи, конвенции, тесты, PR-ревью, separate packages (composer split), PSR-4 boundaries.

Структура ответа:
1. Краткая философия: правило зависимостей (dependencies point inwards).
2. Четыре круга (Domains / Use Cases / Interface Adapters / Frameworks & Drivers).
3. Структура каталогов с примерами.
4. Куда что попадает: Doctrine, контроллер, обработчик очереди — с кодом.
5. Правило зависимостей в PHP: как реализовать через interfaces + DI.
6. Слежение за границами: PHPStan custom rules, ArchPHP (archphp/arch), composer packages, конвенции, PR checklist, тесты.
7. Компромиссы: когда не строить чистую архитектуру (малый проект, прототип), где граница между Clean Architecture и Hexagonal.

Надо избегать пространных рассуждений, дать конкретные примеры кода. Ответ должен быть самодостаточным.

</details>

**Кандидат:**

## Чистая архитектура в PHP: слои, зависимости, контроль

### Философия: правило зависимостей

Всё строится вокруг одного правила:

> **Стрелки зависимостей всегда направлены внутрь.**
>
> Код, ближе к центру, **не знает** про код, дальше от центра.
> Код, дальше от центра, **знает** про код, ближе к центру.

```
        ┌─────────────────────────────────────────────┐
        │  Frameworks & Drivers                       │
        │  ┌───────────────────────────────────────┐  │
        │  │  Interface Adapters                   │  │
        │  │  ┌─────────────────────────────────┐  │  │
        │  │  │  Application (Use Cases)        │  │  │
        │  │  │  ┌───────────────────────────┐  │  │  │
        │  │  │  │  Domain                   │  │  │  │
        │  │  │  │  ┌─────────────────────┐  │  │  │  │
        │  │  │  │  │  Entities + Rules   │  │  │  │  │
        │  │  │  │  └─────────────────────┘  │  │  │  │
        │  │  │  └───────────────────────────┘  │  │  │
        │  │  └─────────────────────────────────┘  │  │
        │  └───────────────────────────────────────┘  │
        └─────────────────────────────────────────────┘

Зависимости:  outside → inside
              (Symfony знает про Use Case, но не наоборот)
```

**Практическое следствие:** если вы хотите поменять БД (Doctrine → Eloquent), фреймворк (Symfony → Laminas), способ доставки (RabbitMQ → Kafka) — **домен и use cases не должны меняться**.

---

### Четыре круга и что в каждом

| Круг | Что | Примеры |
|------|-----|---------|
| **Entities (Domain)** | Бизнес-правила, инварианты, VO | `Invoice`, `Money`, `InvoiceLine`, `OrderStatus` |
| **Use Cases (Application)** | Оркестрация сценариев, транзакции | `PayInvoiceUseCase`, `IssueInvoiceUseCase` |
| **Interface Adapters** | Контроллеры, репозитории, слушатели, DTO | `InvoiceController`, `InvoiceRepository`, `PaymentGateway` |
| **Frameworks & Drivers** | Symfony, Doctrine, RabbitMQ, SMTP | `kernel.php`, `services.yaml`, `doctrine.yaml` |

---

### Структура каталогов

```
src/
├── Domain/                          # Внутренний круг: бизнес-правила
│   ├── Invoice/
│   │   ├── Invoice.php              # Aggregate Root (Entity)
│   │   ├── InvoiceLine.php          # Child Entity
│   │   ├── InvoiceStatus.php        # Enum
│   │   ├── Money.php                # Value Object
│   │   ├── Events/
│   │   │   ├── InvoiceIssued.php
│   │   │   └── InvoicePaid.php
│   │   └── Exceptions/
│   │       ├── InvoiceNotFoundException.php
│   │       └── CannotPayInvoiceException.php
│   └── Customer/
│       ├── Customer.php
│       └── Email.php
│
├── Application/                     # Внутренний круг: сценарии
│   ├── Invoice/
│   │   ├── UseCases/
│   │   │   ├── PayInvoiceUseCase.php
│   │   │   ├── IssueInvoiceUseCase.php
│   │   │   └── CancelInvoiceUseCase.php
│   │   ├── Commands/
│   │   │   └── PayInvoiceCommand.php
│   │   ├── Queries/
│   │   │   └── GetInvoiceQuery.php
│   │   ├── DTO/
│   │   │   └── InvoiceDto.php
│   │   └── Port/                    # ← Интерфейсы (ports)
│   │       ├── InvoiceRepositoryInterface.php
│   │       ├── PaymentGatewayInterface.php
│   │       ├── EventPublisherInterface.php
│   │       └── ClockInterface.php
│   └── Customer/
│       ├── UseCases/
│       └── Port/
│           └── CustomerRepositoryInterface.php
│
├── Infrastructure/                  # Внешний круг: адаптеры
│   ├── Doctrine/                    # Адаптер: БД
│   │   ├── Entity/
│   │   │   ├── InvoiceEntity.php
│   │   │   └── InvoiceLineEntity.php
│   │   ├── Repository/
│   │   │   ├── DoctrineInvoiceRepository.php  # implements Port
│   │   │   └── DoctrineCustomerRepository.php
│   │   └── Mapper/
│   │       ├── InvoiceMapper.php
│   │       └── InvoiceLineMapper.php
│   ├── Payment/                     # Адаптер: платёжный провайдер
│   │   └── StripeGateway.php        # implements PaymentGatewayInterface
│   ├── Event/                       # Адаптер: события
│   │   ├── OutboxPublisher.php      # implements EventPublisherInterface
│   │   └── OutboxPollerCommand.php
│   └── Clock/
│       └── SystemClock.php          # implements ClockInterface
│
└── Interface/                       # Внешний круг: адаптеры интерфейса
    ├── Http/                        # Адаптер: HTTP
    │   ├── InvoiceController.php
    │   ├── CustomerController.php
    │   └── Request/
    │       ├── PayInvoiceRequest.php  # DTO для HTTP-входа
    │       └── InvoiceResponse.php    # DTO для HTTP-выхода
    ├── Console/                     # Адаптер: CLI
    │   ├── OutboxPublishCommand.php
    │   └── GenerateReportCommand.php
    └── Worker/                      # Адаптер: очереди
        ├── SendReceiptWorker.php
        └── UpdateSearchIndexWorker.php
```

**Ключевой принцип именования:**
- `Domain/` — код, который **не знает** про Symfony, Doctrine, RabbitMQ.
- `Application/Port/` — **интерфейсы**, которые домен «хочет».
- `Infrastructure/` — **реализации** этих интерфейсов.
- `Interface/` — **входные** адаптеры (контроллер, CLI, worker).

---

### Конкретные артефакты

#### 1. Domain — чистый PHP

```php
// src/Domain/Invoice/Invoice.php
namespace App\Domain\Invoice;

use App\Domain\Invoice\Events\InvoicePaid;
use App\Domain\Invoice\Exceptions\CannotPayInvoiceException;

final class Invoice
{
    private ?int $id = null;
    private string $customerId;
    private InvoiceStatus $status = InvoiceStatus::Draft;
    private \DateTimeImmutable $createdAt;

    /** @var InvoiceLine[] */
    private array $lines = [];

    /** @var object[] События, накопленные за этот "cycle" */
    private array $collectedEvents = [];

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function addLine(InvoiceLine $line): void
    {
        if ($this->status !== InvoiceStatus::Draft) {
            throw new \DomainException('Cannot add line to non-draft invoice');
        }
        $this->lines[] = $line;
    }

    public function pay(): void
    {
        if ($this->status !== InvoiceStatus::Issued) {
            throw new CannotPayInvoiceException($this->status);
        }
        if (count($this->lines) === 0) {
            throw new CannotPayInvoiceException($this->status);
        }

        $this->status = InvoiceStatus::Paid;

        $this->collectedEvents[] = new InvoicePaid(
            invoiceId: $this->id,
            total: $this->total(),
            paidAt: new \DateTimeImmutable(),
        );
    }

    public function total(): Money
    {
        $total = new Money(0, 'EUR');
        foreach ($this->lines as $line) {
            $total = $total->add($line->total());
        }
        return $total;
    }

    public function releaseEvents(): array
    {
        $events = $this->collectedEvents;
        $this->collectedEvents = [];
        return $events;
    }

    // Getters...
}
```

**Обратите внимание:**
- Ничего про Doctrine (`@ORM\Entity` нет).
- Ничего про Symfony (`Service` нет).
- События **накапливаются**, а не публикуются (паттерн Collected Events).
- Только PHP + бизнес-правила.

---

#### 2. Use Case — оркестрация

```php
// src/Application/Invoice/UseCases/PayInvoiceUseCase.php
namespace App\Application\Invoice\UseCases;

use App\Application\Invoice\Port\InvoiceRepositoryInterface;
use App\Application\Invoice\Port\PaymentGatewayInterface;
use App\Application\Invoice\Port\EventPublisherInterface;
use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;

final class PayInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoices,
        private PaymentGatewayInterface    $payments,
        private EventPublisherInterface    $events,
    ) {}

    public function execute(PayInvoiceCommand $cmd): Invoice
    {
        // 1. Загрузка агрегата
        $invoice = $this->invoices->findById($cmd->invoiceId)
            ?? throw new InvoiceNotFoundException($cmd->invoiceId);

        // 2. Оплата через провайдер (порт, не конкретный Stripe)
        $paymentResult = $this->payments->charge(
            token: $cmd->paymentToken,
            amount: $invoice->total(),
        );

        // 3. Доменная операция
        $invoice->pay();

        // 4. Сохранение
        $this->invoices->save($invoice);

        // 5. Публикация накопленных событий (через порт)
        foreach ($invoice->releaseEvents() as $event) {
            $this->events->publish($event);
        }

        return $invoice;
    }
}
```

**Ключевое:**
- Зависимости — только **интерфейсы** (ports), не конкретные реализации.
- Ничего про HTTP, ничего про CLI, ничего про очередь.
- Если сменить Stripe на Adyen — `PayInvoiceUseCase` не меняется.
- Если сменить RabbitMQ на Kafka — `PayInvoiceUseCase` не меняется.

---

#### 3. Ports — контракты

```php
// src/Application/Invoice/Port/InvoiceRepositoryInterface.php
namespace App\Application\Invoice\Port;

use App\Domain\Invoice\Invoice;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;
    public function save(Invoice $invoice): void;
}
```

```php
// src/Application/Invoice/Port/PaymentGatewayInterface.php
namespace App\Application\Invoice\Port;

use App\Domain\Invoice\Money;

interface PaymentGatewayInterface
{
    public function charge(string $token, Money $amount): PaymentResult;
}
```

```php
// src/Application/Invoice/Port/EventPublisherInterface.php
namespace App\Application\Invoice\Port;

interface EventPublisherInterface
{
    public function publish(object $event): void;
}
```

**Важно:** порты живут в `Application/` (не в `Domain/`), потому что они — часть use case, а не бизнес-правил. Домен не знает, что такое «репозиторий».

---

#### 4. Infrastructure — адаптеры

**Адаптер Doctrine:**

```php
// src/Infrastructure/Doctrine/Repository/DoctrineInvoiceRepository.php
namespace App\Infrastructure\Doctrine\Repository;

use App\Application\Invoice\Port\InvoiceRepositoryInterface;
use App\Domain\Invoice\Invoice;
use App\Infrastructure\Doctrine\Entity\InvoiceEntity;
use App\Infrastructure\Doctrine\Mapper\InvoiceMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private InvoiceMapper $mapper,
    ) {}

    public function findById(int $id): ?Invoice
    {
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->find($id);

        if ($entity === null) {
            return null;
        }

        return $this->mapper->toDomain($entity);
    }

    public function save(Invoice $invoice): void
    {
        $entity = $this->mapper->toEntity($invoice);
        $this->em->persist($entity);
        // flush НЕ вызывается здесь — вызывается use case / controller
    }
}
```

**Адаптер Stripe:**

```php
// src/Infrastructure/Payment/StripeGateway.php
namespace App\Infrastructure\Payment;

use App\Application\Invoice\Port\PaymentGatewayInterface;
use App\Application\Invoice\Port\PaymentResult;
use App\Domain\Invoice\Money;
use Stripe\StripeClient;

final class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(
        private StripeClient $stripe,
    ) {}

    public function charge(string $token, Money $amount): PaymentResult
    {
        $charge = $this->stripe->charges->create([
            'amount'   => $amount->cents,
            'currency' => strtolower($amount->currency),
            'source'   => $token,
        ]);

        return new PaymentResult(
            id: $charge->id,
            status: $charge->status,
        );
    }
}
```

**Адаптер событий (outbox):**

```php
// src/Infrastructure/Event/OutboxPublisher.php
namespace App\Infrastructure\Event;

use App\Application\Invoice\Port\EventPublisherInterface;
use Doctrine\DBAL\Connection;

final class OutboxPublisher implements EventPublisherInterface
{
    public function __construct(
        private Connection $db,
    ) {}

    public function publish(object $event): void
    {
        $this->db->executeStatement(
            "INSERT INTO outbox (event_type, payload, created_at)
             VALUES (:type, :payload, now())",
            [
                'type'    => $event::class,
                'payload' => json_encode($event),
            ]
        );
        // Без flush() — будет flush на верхнем уровне use case
    }
}
```

**Адаптер CLI (outbox poller):**

```php
// src/Interface/Console/OutboxPollerCommand.php
namespace App\Interface\Console;

use App\Infrastructure\Event\OutboxPublisher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'outbox:publish')]
final class OutboxPollerCommand extends Command
{
    public function __construct(
        private OutboxPublisher $publisher,
        private \RabbitMQ\Client $rabbit,
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Логика публикации из outbox в RabbitMQ
        // ...
        return Command::SUCCESS;
    }
}
```

---

#### 5. Interface — входные адаптеры

**HTTP-контроллер:**

```php
// src/Interface/Http/InvoiceController.php
namespace App\Interface\Http;

use App\Application\Invoice\Commands\PayInvoiceCommand;
use App\Application\Invoice\UseCases\PayInvoiceUseCase;
use App\Domain\Invoice\Exceptions\CannotPayInvoiceException;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/invoices')]
final class InvoiceController extends AbstractController
{
    public function __construct(
        private PayInvoiceUseCase $payInvoice,
    ) {}

    #[Route('/{id}/pay', methods: ['POST'])]
    public function pay(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $command = new PayInvoiceCommand(
            invoiceId:    $id,
            paymentToken: $data['payment_token'] ?? '',
        );

        try {
            $invoice = $this->payInvoice->execute($command);

            return new JsonResponse([
                'id'     => $invoice->getId(),
                'status' => $invoice->getStatus()->value,
                'total'  => $invoice->total()->cents,
            ]);
        } catch (InvoiceNotFoundException $e) {
            return new JsonResponse(['error' => 'not_found'], 404);
        } catch (CannotPayInvoiceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }
    }
}
```

**Worker (обработчик очереди):**

```php
// src/Interface/Worker/SendReceiptWorker.php
namespace App\Interface\Worker;

use App\Application\Invoice\Port\InvoiceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

#[AsMessageHandler(fromTransport: 'events')]
final class SendReceiptWorker
{
    public function __construct(
        private InvoiceRepositoryInterface $invoices,
        private \App\Infrastructure\Email\EmailSender $email,
    ) {}

    public function __invoke(string $eventType, string $payload): void
    {
        if ($eventType !== \App\Domain\Invoice\Events\InvoicePaid::class) {
            return;
        }

        $data = json_decode($payload, true);
        $invoice = $this->invoices->findById($data['invoice_id']);

        if ($invoice === null) {
            throw new \RuntimeException("Invoice {$data['invoice_id']} not found");
        }

        $this->email->sendReceipt($invoice);
    }
}
```

---

### Правило зависимостей: как оно работает в PHP

**Конфигурация Symfony связывает порты с адаптерами:**

```yaml
# config/services.yaml
services:
    # По умолчанию — автоинжект по типу
    _defaults:
        autowire: true
        autoconfigure: true

    # Биндим порт → адаптер
    App\Application\Invoice\Port\InvoiceRepositoryInterface:
        alias: App\Infrastructure\Doctrine\Repository\DoctrineInvoiceRepository

    App\Application\Invoice\Port\PaymentGatewayInterface:
        alias: App\Infrastructure\Payment\StripeGateway

    App\Application\Invoice\Port\EventPublisherInterface:
        alias: App\Infrastructure\Event\OutboxPublisher

    App\Application\Invoice\Port\ClockInterface:
        alias: App\Infrastructure\Clock\SystemClock
```

**Как это читается:**

```
InvoiceController  →  PayInvoiceUseCase  →  InvoiceRepositoryInterface (порт)
                                              │
                                              ▼ (alias в services.yaml)
                                  DoctrineInvoiceRepository (адаптер)
                                              │
                                              ▼
                                            Doctrine (драйвер)
```

Стрелки — наружу. Use case **не знает**, что за ним стоит Doctrine.

**Что можно менять, не трогая use case:**

| Меняю | Что меняется |
|-------|-------------|
| Stripe → Adyen | `PaymentGatewayInterface` реализует другой класс. Конфиг: `alias`. |
| RabbitMQ → Kafka | `EventPublisherInterface` реализует другой класс. Конфиг: `alias`. |
| PostgreSQL → MySQL | `InvoiceRepositoryInterface` реализует другой класс. Конфиг: `alias`. |
| Symfony → Laminas | `InvoiceController` переписывается. Use case — нет. |

---

### Как следить, что границы не нарушены

#### 1. ArchPHP (рекомендую)

ArchPHP — это аналог ArchUnit для PHP. Устанавливается как composer-пакет, работает как PHPUnit-тест.

```php
// tests/ArchitectureTest.php
use App\Tests\Architecture\Rules;
use ArchPHP\Arch;
use PHPUnit\Framework\TestCase;

final class ArchitectureTest extends TestCase
{
    public function test_domain_does_not_depend_on_infrastructure(): void
    {
        Arch::rules()->for('App\Domain')
            ->shouldNot('use')
            ->classesInNamespace('App\Infrastructure')
            ->classesInNamespace('App\Application')
            ->classesInNamespace('App\Interface')
            ->classesInNamespace('Symfony')
            ->classesInNamespace('Doctrine')
            ->assert();
    }

    public function test_application_does_not_depend_on_infrastructure(): void
    {
        Arch::rules()->for('App\Application')
            ->shouldNot('use')
            ->classesInNamespace('App\Infrastructure')
            ->classesInNamespace('App\Interface')
            ->assert();
    }

    public function test_use_case_should_be_final(): void
    {
        Arch::rules()->for('App\Application\**\UseCases')
            ->should('beFinal')
            ->assert();
    }

    public function test_entities_should_be_in_domain(): void
    {
        Arch::rules()->for('**\*Entity')
            ->should('beInNamespace')
            ->namespace('App\Infrastructure\Doctrine')
            ->assert();
    }

    public function test_no_orm_annotations_in_domain(): void
    {
        Arch::rules()->for('App\Domain\**')
            ->shouldNot('use')
            ->classesInNamespace('Doctrine\ORM\Mapping')
            ->assert();
    }
}
```

Эти тесты **падают на CI**, если кто-то случайно импортирует Doctrine в `Domain/`.

#### 2. PHPStan custom rules

```php
// src/Phpstan/DomainMustNotUseFrameworkRule.php
namespace App\Phpstan;

use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\Node;
use PHPStan\Node\InClassNode;

/**
 * @implements Rule<InClassNode>
 */
final class DomainMustNotUseFrameworkRule implements Rule
{
    private const FORBIDDEN_PREFIXES = [
        'Symfony\\',
        'Doctrine\\',
        'RabbitMQ\\',
    ];

    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node instanceof InClassNode)) {
            return [];
        }

        $className = $scope->getClassReflection()?->getName();
        if ($className === null || !str_starts_with($className, 'App\Domain')) {
            return [];
        }

        foreach ($node->getNames() as $name) {
            foreach (self::FORBIDDEN_PREFIXES as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    return [
                        sprintf(
                            'Class %s in Domain must not use %s',
                            $className,
                            $name
                        )
                    ];
                }
            }
        }

        return [];
    }

    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }
}
```

#### 3. Composer packages (разделение)

Сильный вариант: вынести слои в **отдельные composer-пакеты**.

```
packages/
├── domain/           # app-domain — без зависимостей
│   └── composer.json  # "require": {}
├── application/      # app-application — зависит от domain
│   └── composer.json  # "require": { "acme/domain": "*" }
├── infrastructure/   # app-infrastructure — зависит от application + domain
│   └── composer.json  # "require": { "acme/application": "*" }
└── interface/        # app-interface — зависит от application + domain
    └── composer.json  # "require": { "acme/application": "*" }
```

**Плюс:** если кто-то в `domain` напишет `use Doctrine\ORM\Mapping\Entity` — `composer install` упадёт. Физическая невозможность.

**Минус:** сложностьCI, overhead при разработке. Для команды > 3 — ок. Для 1–2 человек — избыточно.

#### 4. Тесты на зависимости

```php
// tests/DependencyTest.php
public function test_domain_has_no_external_dependencies(): void
{
    $composerJson = file_get_contents(__DIR__ . '/../packages/domain/composer.json');
    $json = json_decode($composerJson, true);

    $forbidden = ['doctrine/orm', 'symfony/framework-bundle', 'php-amqplib/php-amqplib'];

    foreach ($forbidden as $package) {
        $this->assertArrayNotHasKey($package, $json['require'] ?? [],
            "Domain must not depend on {$package}");
    }
}
```

#### 5. PR-ревью checklist

```
- [ ] Новый код в `Domain/` не использует Symfony/Doctrine/RabbitMQ
- [ ] Новый код в `Application/` зависит только от портов (интерфейсов)
- [ ] Новый адаптер реализует порт из `Application/Port/`
- [ ] Контроллер не содержит бизнес-логики (только mapping + вызов use case)
- [ ] Worker не содержит бизнес-логики (только вызов use case)
- [ ] Репозиторий не содержит бизнес-логики (только маппинг + persist)
- [ ] События публикуются через порт, не через конкретный RabbitMQ
```

#### 6. Naming convention

| Паттерн | Означает |
|---------|----------|
| `*Entity` | Doctrine entity (в `Infrastructure/Doctrine/Entity/`) |
| `*Repository` | Реализация порта (в `Infrastructure/`) |
| `*UseCase` | Use case (в `Application/UseCases/`) |
| `*Command` | Входной DTO (в `Application/Commands/`) |
| `*Dto` | Выходной DTO (в `Application/DTO/`) |
| `*Controller` | HTTP-адаптер (в `Interface/Http/`) |
| `*Command` (CLI) | CLI-адаптер (в `Interface/Console/`) — лучше переименовать в `*CliCommand` |
| `*Worker` | Worker-адаптер (в `Interface/Worker/`) |
| `*Port` / `*Interface` | Порт (в `Application/Port/`) |

Если имя соответствует конвенции — сразу понятно, в какой слой положить.

---

### Компромиссы: когда НЕ строить чистую архитектуру

| Ситуация | Решение |
|----------|---------|
| Прототип, MVP, PoC | Просто `Controller + Service + Repository`. Чистая архитектура — overhead. |
| Команда 1 человек | ArchPHP + naming convention достаточно. Composer-пакеты — нет. |
| Статический сайт, 5 страниц | Чистая архитектура — абсурд. |
| Критичный срок | Спринт 1 — «грязная» архитектура. Спринт 2–3 — рефакторинг по слоям. |
| Команда > 5 человек | Composer-пакеты + ArchPHP + CI — обязательно. |

**Правило:** чистая архитектура оправдана, когда **код будет меняться** и **его будут читать разные люди**. Если код одноразовый — не усложняйте.

---

### Итоговая диаграмма зависимостей

```
┌─────────────────────────────────────────────────────────────┐
│  Interface (вход)                                            │
│  InvoiceController ──┐                                      │
│  OutboxPollerCmd ──┐ │                                       │
│  SendReceiptWorker─┘ │                                       │
│                      ▼                                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Application (use cases + ports)                     │   │
│  │  PayInvoiceUseCase                                  │   │
│  │       │                                              │   │
│  │       ├── InvoiceRepositoryInterface (порт)          │   │
│  │       ├── PaymentGatewayInterface    (порт)          │   │
│  │       └── EventPublisherInterface    (порт)          │   │
│  │                                                      │   │
│  │  ┌──────────────────────────────────────────────┐   │   │
│  │  │  Domain (entities, rules, events)            │   │   │
│  │  │  Invoice, InvoiceLine, Money, InvoiceStatus  │   │   │
│  │  │  InvoicePaid, InvoiceIssued (events)         │   │   │
│  │  └──────────────────────────────────────────────┘   │   │
│  └──────────────────────────────────────────────────────┘   │
│                      ▲                                       │
│                      │                                       │
│  ┌───────────────────┴──────────────────────────────────┐   │
│  │  Infrastructure (адаптеры)                           │   │
│  │  DoctrineInvoiceRepository  ─→ InvoiceEntity         │   │
│  │  StripeGateway              ─→ StripeClient          │   │
│  │  OutboxPublisher            ─→ DBAL                  │   │
│  │  SystemClock                ─→ time()                │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Frameworks & Drivers (внешние)                      │   │
│  │  Symfony, Doctrine, RabbitMQ, Stripe                 │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘

Зависимости: только наружу → внутрь
Domain не знает про остальные 3 круга.
```

**Главная проверка:** если вы удалили все файлы `Interface/`, `Infrastructure/`, `Framework/` — код в `Domain/` и `Application/` **должен компилироваться**. Если нет — граница нарушена.

> ⏱ 64.1s · prompt 74444 tok · output 7415 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о стратегии тестирования для Symfony + PostgreSQL. Это продолжение длинного технического интервью по PHP/бэкенду. Нужно дать конкретный, практичный ответ без воды.

Ключевые аспекты:
1. Пирамида тестов — как именно она выглядит для Symfony+PostgreSQL
2. Типы тестов: unit, integration, feature/e2e
3. Что мокать, что не мокать — конкретные критерии
4. PostgreSQL в тестах: реальная БД vs mock
5. Быстрота: parallel, cache, fixtures
6. Стабильность: flaky tests, time, network, DB state

Я должен дать практичный ответ, с примерами кода, конкретными инструментами (PHPUnit, Pest, zenstruck/foundry, zenstruck/messenger, etc.), и чёткими критериями "мокать/не мокать".

Структура:
1. Пирамида — конкретные пропорции
2. Unit tests — что, как, примеры
3. Integration tests — что, как, PostgreSQL
4. Feature/E2E — что, как
5. Матрица: что мокать, что не мокать
6. Быстрота — инструменты
7. Стабильность — анти-флаки
8. Антипаттерны

</details>

**Кандидат:**

## Стратегия тестирования Symfony + PostgreSQL

### Пирамида

```
        ┌─────────────┐
        │   E2E /     │  5–10 тестов
        │   Feature   │  (полный запрос → БД → ответ)
        ├─────────────┤
        │Integration  │  50–150 тестов
        │             │  (Use Case + реальная БД)
        ├─────────────┤
        │   Unit      │  200–500 тестов
        │             │  (Domain + VO + правила)
        └─────────────┘
```

**Пропорция:** ~70% unit, ~25% integration, ~5% e2e.
**Время:** unit < 10 сек, integration < 60 сек, e2e < 5 мин (общее).

**Почему именно так для Symfony + PostgreSQL:**
- Domain-логика (VO, инварианты, transitions) — чистый PHP, быстро тестируется.
- Integration — там, где важна **реальность** SQL, маппинг, транзакции. Мок БД — плохая идея.
- E2E — там, где важна **интеграция** (контроллер → use case → репозиторий → БД).

---

### 1. Unit-тесты: Domain и Use Cases

#### Что тестируем

| Объект | Что проверяем | Мок? |
|--------|---------------|------|
| Value Object (`Money`, `Email`) | Валидация, арифметика, equals | Нет |
| Entity / Aggregate (`Invoice`) | Инварианты, transitions, collected events | Нет |
| Use Case (`PayInvoiceUseCase`) | Оркестрацию: порядок вызовов, обработку ошибок | Да (порты) |
| Exception handling | Что бросается при каких условиях | Да |

#### Пример: Value Object

```php
// tests/Unit/Domain/Invoice/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function test_creates_with_valid_values(): void
    {
        $money = new Money(1990, 'EUR');

        $this->assertSame(1990, $money->cents);
        $this->assertSame('EUR', $money->currency);
    }

    public function test_rejects_negative_amount(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('negative');

        new Money(-100, 'EUR');
    }

    public function test_rejects_invalid_currency(): void
    {
        $this->expectException(\DomainException::class);

        new Money(100, 'eur');  // lowercase
    }

    public function test_add_same_currency(): void
    {
        $a = new Money(100, 'EUR');
        $b = new Money(250, 'EUR');

        $result = $a->add($b);

        $this->assertSame(350, $result->cents);
        $this->assertSame('EUR', $result->currency);
    }

    public function test_add_different_currency_throws(): void
    {
        $a = new Money(100, 'EUR');
        $b = new Money(100, 'USD');

        $this->expectException(\DomainException::class);

        $a->add($b);
    }

    public function test_immutability(): void
    {
        $a = new Money(100, 'EUR');
        $b = $a->add(new Money(50, 'EUR'));

        $this->assertSame(100, $a->cents);  // a не изменилась
        $this->assertSame(150, $b->cents);
        $this->assertNotSame($a, $b);       // разные объекты
    }

    public function test_equality(): void
    {
        $a = new Money(1990, 'EUR');
        $b = new Money(1990, 'EUR');
        $c = new Money(1990, 'USD');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
```

**Заметь:**
- Никаких mock'ов. Чистый PHP.
- Тестируем **поведение**, не реализацию.
- Покрытие: happy path + each exception + immutability + equality.

#### Пример: Aggregate

```php
// tests/Unit/Domain/Invoice/InvoiceTest.php
final class InvoiceTest extends TestCase
{
    private function makeInvoice(InvoiceStatus $status = InvoiceStatus::Draft): Invoice
    {
        $invoice = new Invoice('cust_123');
        $invoice->addLine(new InvoiceLine(
            productId: 1,
            quantity: 2,
            unitPrice: new Money(500, 'EUR'),
        ));

        // Приватная настройка статуса для теста (или factory-метод)
        if ($status === InvoiceStatus::Issued) {
            $invoice->issue();
        }

        return $invoice;
    }

    public function test_add_line_to_draft(): void
    {
        $invoice = $this->makeInvoice(InvoiceStatus::Draft);
        $line = new InvoiceLine(2, 1, new Money(300, 'EUR'));

        $invoice->addLine($line);

        $this->assertCount(2, $invoice->getLines());
    }

    public function test_cannot_add_line_to_issued(): void
    {
        $invoice = $this->makeInvoice(InvoiceStatus::Issued);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('non-draft');

        $invoice->addLine(new InvoiceLine(1, 1, new Money(100, 'EUR')));
    }

    public function test_pay_issued_invoice(): void
    {
        $invoice = $this->makeInvoice(InvoiceStatus::Issued);

        $invoice->pay();

        $this->assertSame(InvoiceStatus::Paid, $invoice->getStatus());

        $events = $invoice->releaseEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(InvoicePaid::class, $events[0]);
    }

    public function test_cannot_pay_draft(): void
    {
        $invoice = $this->makeInvoice(InvoiceStatus::Draft);

        $this->expectException(CannotPayInvoiceException::class);

        $invoice->pay();
    }

    public function test_cannot_pay_cancelled(): void
    {
        $invoice = $this->makeInvoice(InvoiceStatus::Cancelled);

        $this->expectException(CannotPayInvoiceException::class);

        $invoice->pay();
    }

    public function test_cannot_pay_empty(): void
    {
        $invoice = new Invoice('cust_123');
        // Не добавили ни одной линии

        $this->expectException(CannotPayInvoiceException::class);

        $invoice->pay();
    }
}
```

#### Пример: Use Case (мок ports)

```php
// tests/Unit/Application/Invoice/PayInvoiceUseCaseTest.php
final class PayInvoiceUseCaseTest extends TestCase
{
    public function test_success_flow(): void
    {
        $invoice = new Invoice('cust_123');
        $invoice->addLine(new InvoiceLine(1, 2, new Money(500, 'EUR')));
        $invoice->issue();

        // Мок репозитория
        $repo = $this->createMock(InvoiceRepositoryInterface::class);
        $repo->method('findById')->with(42)->willReturn($invoice);
        $repo->expects($this->once())->method('save')->with($invoice);

        // Мок платёжного шлюза
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')
            ->with('tok_123', new Money(1000, 'EUR'))
            ->willReturn(new PaymentResult('pay_1', 'succeeded'));

        // Мок публикации событий
        $events = $this->createMock(EventPublisherInterface::class);
        $events->expects($this->once())->method('publish');

        $useCase = new PayInvoiceUseCase($repo, $gateway, $events);

        $result = $useCase->execute(new PayInvoiceCommand(42, 'tok_123'));

        $this->assertSame(InvoiceStatus::Paid, $result->getStatus());
    }

    public function test_throws_when_invoice_not_found(): void
    {
        $repo = $this->createMock(InvoiceRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new PayInvoiceUseCase(
            $repo,
            $this->createMock(PaymentGatewayInterface::class),
            $this->createMock(EventPublisherInterface::class),
        );

        $this->expectException(InvoiceNotFoundException::class);

        $useCase->execute(new PayInvoiceCommand(999, 'tok_123'));
    }

    public function test_payment_failure_propagates(): void
    {
        $invoice = new Invoice('cust_123');
        $invoice->addLine(new InvoiceLine(1, 1, new Money(100, 'EUR')));
        $invoice->issue();

        $repo = $this->createMock(InvoiceRepositoryInterface::class);
        $repo->method('findById')->willReturn($invoice);

        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')->willThrowException(
            new PaymentDeclinedException('card declined')
        );

        $useCase = new PayInvoiceUseCase($repo, $gateway, $this->createMock(EventPublisherInterface::class));

        $this->expectException(PaymentDeclinedException::class);

        $useCase->execute(new PayInvoiceCommand(42, 'tok_123'));

        // Invoice НЕ изменён
        $this->assertSame(InvoiceStatus::Issued, $invoice->getStatus());
    }
}
```

---

### 2. Integration-тесты: реальная БД

#### Что тестируем

| Что | Почему |
|-----|--------|
| Use Case + реальный репозиторий + реальная PostgreSQL | SQL, маппинг, транзакции, cascade |
| Репозиторий: CRUD, query, pagination | DQL, join, index usage |
| Transactional behavior: rollback, flush ordering | Только с реальной БД |
| DB constraints: CHECK, FK, UNIQUE | Только с реальной БД |
| Concurrent behavior (optimistic lock) | Только с реальной БД |

#### Инструменты

| Инструмент | Зачем |
|------------|-------|
| `zenstruck/foundry` | Фабрики + fixtures для PostgreSQL |
| `zenstruck/messenger` | Тестирование Symfony Messenger (sync/async) |
| `hautelook/alice-bundle` (альтернатива foundry) | Fixtures из YAML |
| `doctrine/test-bundle` | Управление schema в тестах |
| Docker / `services` в phpunit.xml | Реальная PostgreSQL |

#### Конфигурация

```xml
<!-- phpunit.xml -->
<php>
    <server name="KERNEL_CLASS" value="App\Kernel" />
    <server name="APP_ENV" value="test" />

    <!-- PostgreSQL для тестов -->
    <server name="DATABASE_URL"
            value="postgres://test:test@127.0.0.1:5432/app_test?serverVersion=16&charset=utf8" />
</php>

<!-- Если используем Docker: -->
<!-- Запускаем через docker-compose.test.yml до phpunit -->
```

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        url: '%env(DATABASE_URL)%'

# config/packages/test/doctrine.yaml
doctrine:
    dbal:
        schema_filter: '~^Doctrine\.Mappings\.'
```

#### Пример: Use Case + реальная БД

```php
// tests/Integration/Application/Invoice/PayInvoiceUseCaseTest.php
final class PayInvoiceUseCaseTest extends TestCase
{
    use ResetDatabase;  // zenstruck/foundry: очищает БД между тестами

    private PayInvoiceUseCase $useCase;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->useCase = self::getContainer()->get(PayInvoiceUseCase::class);
        $this->em = self::getContainer()->get('doctrine')->getManager();
    }

    public function test_pay_persists_status_to_database(): void
    {
        // Фикстура: создаём invoice в БД
        $invoice = InvoiceEntityFactory::createOne([
            'customer_id'  => 'cust_test',
            'status'       => 'issued',
            'total_cents'  => 1000,
        ]);

        // Загружаем через use case (реальный репозиторий)
        $result = $this->useCase->execute(new PayInvoiceCommand($invoice->id, 'tok_test'));

        // Проверяем: статус в БД изменился
        $this->em->clear();
        $fresh = $this->em->getRepository(InvoiceEntity::class)->find($invoice->id);

        $this->assertSame('paid', $fresh->status);
    }

    public function test_pay_writes_event_to_outbox(): void
    {
        $invoice = InvoiceEntityFactory::createOne([
            'status' => 'issued',
        ]);

        $this->useCase->execute(new PayInvoiceCommand($invoice->id, 'tok_test'));

        // Проверяем: событие в outbox
        $count = $this->em->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM outbox WHERE event_type = 'App\Domain\Invoice\Events\InvoicePaid'"
        );

        $this->assertSame(1, (int) $count);
    }

    public function test_concurrent_pay_second_fails(): void
    {
        $invoice = InvoiceEntityFactory::createOne([
            'status' => 'issued',
            'version' => 1,
        ]);

        // Первый pay
        $this->useCase->execute(new PayInvoiceCommand($invoice->id, 'tok_test'));

        // Второй pay (invoice уже paid)
        $this->expectException(CannotPayInvoiceException::class);
        $this->useCase->execute(new PayInvoiceCommand($invoice->id, 'tok_test2'));
    }

    public function test_db_constraint_blocks_negative_amount(): void
    {
        // Прямой SQL, минуя домен
        $this->em->getConnection()->executeStatement(
            "INSERT INTO invoice_lines (invoice_id, product_id, quantity, unit_price_cents)
             VALUES (1, 1, -5, 100)"
        );

        $this->fail('Should have thrown DB constraint violation');
    }
}
```

#### Пример: репозиторий

```php
// tests/Integration/Infrastructure/Doctrine/InvoiceRepositoryTest.php
final class InvoiceRepositoryTest extends TestCase
{
    use ResetDatabase;

    public function test_findById_returns_domain_object(): void
    {
        $entity = InvoiceEntityFactory::createOne([
            'customer_id' => 'cust_1',
            'status'      => 'draft',
        ]);
        InvoiceLineEntityFactory::createOne(['invoice' => $entity]);

        $repo = self::getContainer()->get(InvoiceRepositoryInterface::class);
        $domain = $repo->findById($entity->id);

        $this->assertNotNull($domain);
        $this->assertInstanceOf(Invoice::class, $domain);
        $this->assertSame('cust_1', $domain->getCustomerId());
        $this->assertCount(1, $domain->getLines());
    }

    public function test_findById_returns_null_for_nonexistent(): void
    {
        $repo = self::getContainer()->get(InvoiceRepositoryInterface::class);

        $this->assertNull($repo->findById(99999));
    }

    public function test_save_persists_new_invoice(): void
    {
        $repo = self::getContainer()->get(InvoiceRepositoryInterface::class);

        $invoice = new Invoice('cust_new');
        $invoice->addLine(new InvoiceLine(1, 2, new Money(500, 'EUR')));

        $repo->save($invoice);
        $this->em->flush();

        // Проверяем: строка в БД
        $count = $this->em->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM invoice_lines"
        );
        $this->assertSame(1, (int) $count);
    }

    public function test_cascade_delete_removes_lines(): void
    {
        $entity = InvoiceEntityFactory::createOne(['status' => 'draft']);
        $line = InvoiceLineEntityFactory::createOne(['invoice' => $entity]);

        $this->em->remove($entity);
        $this->em->flush();

        $count = $this->em->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM invoice_lines WHERE id = {$line->id}"
        );
        $this->assertSame(0, (int) $count);
    }
}
```

#### Пример: Transactional behavior

```php
// tests/Integration/Application/Invoice/TransactionalTest.php
final class TransactionalTest extends TestCase
{
    use ResetDatabase;

    public function test_failed_use_case_rolls_back(): void
    {
        $invoice = InvoiceEntityFactory::createOne(['status' => 'issued']);

        // Use case, который падает на втором шаге
        // (например, payment gateway бросает исключение)
        $useCase = new PayInvoiceUseCase(
            self::getContainer()->get(InvoiceRepositoryInterface::class),
            $this->failingGateway(),  // мок, который бросает
            self::getContainer()->get(EventPublisherInterface::class),
        );

        try {
            $useCase->execute(new PayInvoiceCommand($invoice->id, 'tok_bad'));
            $this->fail('Should throw');
        } catch (PaymentDeclinedException $e) {
            // expected
        }

        // Проверяем: status НЕ изменился
        $this->em->clear();
        $fresh = $this->em->getRepository(InvoiceEntity::class)->find($invoice->id);
        $this->assertSame('issued', $fresh->status);  // не 'paid'

        // Проверяем: нет записи в outbox
        $count = $this->em->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM outbox"
        );
        $this->assertSame(0, (int) $count);
    }

    private function failingGateway(): PaymentGatewayInterface
    {
        return new class implements PaymentGatewayInterface {
            public function charge(string $token, Money $amount): PaymentResult
            {
                throw new PaymentDeclinedException('test failure');
            }
        };
    }
}
```

---

### 3. Feature/E2E-тесты

#### Что тестируем

| Сценарий | Почему |
|----------|--------|
| Полный HTTP-запрос → БД → ответ | Routing, serialization, auth, validation |
| Workflow: register → pay → receipt | Кросс-компонентное взаимодействие |
| Error handling: 400, 401, 403, 422, 500 | Contract API |

#### Пример

```php
// tests/Feature/Invoice/PayInvoiceTest.php
final class PayInvoiceTest extends WebTestCase
{
    use ResetDatabase;

    public function test_pay_returns_200_and_updates_status(): void
    {
        $client = static::createClient();

        // Auth: логин как admin
        $client->request('POST', '/login', ['email' => 'admin@test.com', 'password' => 'pass']);
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Создаём invoice через API (или fixture)
        $client->request('POST', '/api/v1/invoices', [
            'json' => [
                'customer_id' => 'cust_1',
                'lines' => [
                    ['product_id' => 1, 'quantity' => 2, 'unit_price_cents' => 500],
                ],
            ],
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $invoiceId = $response['id'];

        // Pay
        $client->request('POST', "/api/v1/invoices/{$invoiceId}/pay", [
            'json' => ['payment_token' => 'tok_test'],
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('paid', $response['status']);

        // Проверяем БД
        $em = self::getContainer()->get('doctrine')->getManager();
        $status = $em->getConnection()->fetchOne(
            "SELECT status FROM invoices WHERE id = {$invoiceId}"
        );
        $this->assertSame('paid', $status);
    }

    public function test_pay_returns_422_for_draft(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', ['email' => 'admin@test.com', 'password' => 'pass']);

        // Draft invoice (не issued)
        $client->request('POST', '/api/v1/invoices', [
            'json' => [
                'customer_id' => 'cust_1',
                'lines' => [['product_id' => 1, 'quantity' => 1, 'unit_price_cents' => 100]],
            ],
        ]);
        $id = json_decode($client->getResponse()->getContent(), true)['id'];

        $client->request('POST', "/api/v1/invoices/{$id}/pay", [
            'json' => ['payment_token' => 'tok_test'],
        ]);

        $this->assertSame(422, $client->getResponse()->getStatusCode());
    }

    public function test_pay_returns_401_without_auth(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/invoices/1/pay', [
            'json' => ['payment_token' => 'tok_test'],
        ]);

        $this->assertSame(401, $client->getResponse()->getStatusCode());
    }
}
```

---

### 4. Матрица: что мокать, что не мокать

#### Правило

> **Мокуй то, что находится за границей твоего круга. Не мокуй то, что внутри.**

```
Unit test:     Mock ports (репозиторий, gateway, publisher)
Integration:   НЕ мокуй БД. Мокуй внешние сервисы (Stripe, SMTP)
E2E:           НЕ мокуй ничего внутри приложения. Мокуй только внешние API
```

#### Конкретная таблица

| Зависимость | Unit | Integration | E2E |
|-------------|------|-------------|-----|
| `InvoiceRepository` (порт) | **Mock** | Реальный (PostgreSQL) | Реальный |
| `PaymentGateway` (порт) | **Mock** | **Mock** (Stripe API) | **Mock** (Stripe API) |
| `EventPublisher` (порт) | **Mock** | Реальный (outbox в БД) | Реальный |
| `EmailSender` | **Mock** | **Mock** (SMTP) | **Mock** (SMTP) |
| `Clock` | **Mock** | Мокабельный (fixed time) | Мокабельный |
| `EntityManager` | **Mock** (через репозиторий) | Реальный | Реальный |
| `Doctrine Entity` | Не трогаем (unit = domain) | Реальный | Реальный |
| `RabbitMQ` | Не трогаем | **Mock** / sync transport | **Mock** / sync transport |
| `Stripe API` | Не трогаем | **Mock** | **Mock** |
| `PostgreSQL` | Не трогаем | **Реальный** | **Реальный** |
| `Symfony Kernel` | Не трогаем | Реальный | Реальный |

#### Почему не мокать PostgreSQL

| Причина | Последствие mock |
|---------|-----------------|
| SQL-синтаксис | Mock не проверит, что DQL корректен |
| Типы данных | Mock не поймает `int` → `float` overflow |
| Constraints | Mock не знает про `CHECK`, `FK`, `UNIQUE` |
| Transactions | Mock не откатывает |
| N+1 | Mock не покажет лишние запросы |
| Change tracking | Mock не знает про identity map |

**Единственное исключение:** unit test use case, где репозиторий — порт. Там мок — потому что мы тестируем **оркестрацию**, не SQL.

#### Почему мокать Stripe / SMTP

| Причина | Последствие без mock |
|---------|---------------------|
| Внешняя зависимость | Не работает в CI (нет сети) |
| Стоимость | Каждый тест = реальный charge |
| Скорость | 500ms на каждый запрос к Stripe |
| Детерминизм | Stripe может вернуть разный ответ |
| Безопасность | Test-токены утекают |

---

### 5. Быстрота

#### Стратегии

| Стратегия | Экономия | Как |
|-----------|----------|-----|
| **PHPUnit parallel** | ×4–×8 | `--processes 4` |
| **Real PostgreSQL (Docker)** | Быстрее, чем mock + setup | `docker-compose.test.yml` |
| **Transaction rollback** (вместо `TRUNCATE`) | ×2–×5 | Каждый тест в транзакции, откат в конце |
| **Cache schema** | ×10 на первый тест | `doctrine:schema:sync --no-interaction` один раз |
| **Fixed clock** | 0 | `SystemClock` → `FixedClock` в тестах |
| **Selective tests** | ×5–×10 при dev | `--filter PayInvoice` вместо всего |
| **CI caching** | ×2 на CI | Кэшировать Docker image, vendor |

#### Конкретика

**Transaction rollback:**

```php
// zenstruck/foundry: ResetDatabase trait
// Каждый тест:
//   BEGIN
//   ... test ...
//   ROLLBACK
// → БД чиста, без TRUNCATE (медленно)
```

**PHPUnit parallel:**

```bash
# composer.json
"scripts": {
    "test": "phpunit --processes 4 --fail-on-warning"
}
```

**Docker для PostgreSQL:**

```yaml
# docker-compose.test.yml
services:
    postgres:
        image: postgres:16
        environment:
            POSTGRES_DB: app_test
            POSTGRES_USER: test
            POSTGRES_PASSWORD: test
        ports:
            - "5432:5432"
        healthcheck:
            test: ["CMD-SHELL", "pg_isready -U test"]
            interval: 1s
            timeout: 5s
            retries: 5
```

```bash
# CI / локально
docker compose -f docker-compose.test.yml up -d
vendor/bin/phpunit --processes 4
docker compose -f docker-compose.test.yml down
```

**Цели по времени:**

| Suite | Цель |
|-------|------|
| Unit (200+ тестов) | < 10 сек |
| Integration (50–150 тестов) | < 60 сек |
| E2E (5–10 тестов) | < 5 мин |
| **Всё** | **< 10 мин** |

---

### 6. Стабильность (анти-флаки)

#### Типы флик

| Флик | Причина | Решение |
|------|---------|---------|
| **Time-dependent** | `new \DateTimeImmutable()` в домене | Fixed clock (порт) |
| **Order-dependent** | Тест B зависит от состояния теста A | Transaction rollback + ResetDatabase |
| **Random data** | `rand()`, `uniqid()` в тесте | Детерминированные фикстуры |
| **Network** | Реальный Stripe/SMTP | Mock |
| **DB state** | Остатки из прошлого прогона | `ResetDatabase` (TRUNCATE перед первым тестом) |
| **Parallel conflicts** | Два процесса пишут в одну таблицу | Отдельные БД на процесс (или не параллелить integration) |
| **Flaky timeout** | Worker не успел обработать | Wait/poll с timeout, не sleep |

#### Anti-patterns

```php
// ❌ 1. Sleep вместо ожидания
$worker->process();
usleep(500_000);  // "даю время"
$this->assertTrue($isDone);

// ✅ 2. Wait with condition
$this->waitFor(
    fn() => $this->repo->findById($id)?->status === 'paid',
    timeout: 5.0,
    interval: 0.1,
);
```

```php
// ❌ 3. Зависимость от порядка
// test_1 создаёт invoice#1
// test_2 считает invoices, ожидает 1
// Если test_2 идёт первым → 0 → FAIL

// ✅ 4. Каждый тест сам создаёт данные
public function test_count(): void
{
    InvoiceEntityFactory::createOne();
    InvoiceEntityFactory::createOne();

    $count = $repo->countAll();
    $this->assertSame(2, $count);
}
```

```php
// ❌ 5. Time в домене без порта
public function issue(): void
{
    $this->issuedAt = new \DateTimeImmutable();  // ← реальное время
}

// ✅ 6. Clock через порт
interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}

final class Invoice
{
    public function __construct(
        private ClockInterface $clock,
    ) {}

    public function issue(): void
    {
        $this->issuedAt = $this->clock->now();
    }
}

// В тесте:
$clock = new class implements ClockInterface {
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2024-01-15 12:00:00');
    }
};
```

```php
// ❌ 7. Shared mutable state
class TestCase
{
    protected static $sharedInvoice;  // ← общее состояние

    public function test_a(): void
    {
        self::$sharedInvoice = $this->createInvoice();
        // ...
    }

    public function test_b(): void
    {
        // Использует self::$sharedInvoice от test_a → флик
    }
}
```

#### Чеклист стабильности

```
□ Каждый тест создаёт свои данные (factory/fixture)
□ Transaction rollback между тестами (нет остатков)
□ Нет sleep — только wait-for-condition
□ Clock через порт (детерминированное время)
□ Внешние сервисы — mock
□ Нет shared mutable state
□ Тесты не зависят от порядка
□ Random data — seed-ирован
□ Timeout у всех wait-операций
```

---

### 7. Инструменты: сводка

| Задача | Инструмент |
|--------|-----------|
| Unit-тесты | PHPUnit (или Pest) |
| Fixtures / factories | `zenstruck/foundry` |
| Reset БД между тестами | `zenstruck/foundry` (`ResetDatabase`) |
| T

> ⏱ 72.4s · prompt 81557 tok · output 7671 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о конкурентном изменении одной записи в БД. Это пересекается с предыдущим вопросом про конкурентное изменение агрегата, но теперь фокус уже — на уровне записи в БД (row-level), не на уровне агрегата в DDD. Нужно дать структурированный ответ: проблемы (lost update, dirty read, phantom, deadlock, etc.), способы защиты (optimistic, pessimistic, atomic update, serializable, idempotency), сравнение, когда что использовать.

Структурирую так:
1. Какие проблемы возникают (конкретные сценарии)
2. Что даёт PostgreSQL по умолчанию (READ COMMITTED)
3. Стратегии защиты:
   - Optimistic locking (version field)
   - Pessimistic locking (SELECT FOR UPDATE / FOR SHARE)
   - Атомарный UPDATE (SET x = x + delta WHERE x >= N)
   - SERIALIZABLE isolation
   - Idempotency (UNIQUE + ON CONFLICT)
   - Advisory locks (PostgreSQL)
   - Queue / single writer
4. Таблица сравнения
5. Когда что выбираю (конкретные сценарии)
6. Типовые ошибки

Отвечу на русском, как и вся беседа. Использую код PHP/SQL.

Важно: не повторять дословно предыдущий ответ про агрегаты — здесь фокус на row-level, конкретнее на SQL-механиках PostgreSQL.

</details>

**Кандидат:**

## Конкурентное изменение одной записи

### 1. Что может пойти не так

#### Сценарий: два процесса списывают деньги с одного баланса

```
T1                              T2
────                            ────
SELECT balance → 500
                                  SELECT balance → 500
balance = 500 - 300 = 200
                                  balance = 500 - 400 = 100
UPDATE ... SET balance = 200
UPDATE ... SET balance = 100   ← последний победил

Итог: balance = 100
Должно быть: 500 - 300 - 400 = -200 (не хватает средств)
```

**Проблема: Lost Update.** Оба процесса прочитали старое значение. Один из списаний «потерян».

#### Другие проблемы

| Проблема | Суть | Пример |
|----------|------|--------|
| **Lost Update** | Последний `UPDATE` перезаписывает предыдущий | Баланс, счётчик |
| **Dirty Read** | Чтение неcommitted-данных | PostgreSQL: нет (READ COMMITTED) |
| **Non-repeatable Read** | Повторный `SELECT` в транзакции даёт другое значение | PostgreSQL: возможно в READ COMMITTED |
| **Phantom Read** | Новый `SELECT` видит новые строки | PostgreSQL: возможно |
| **Deadlock** | Два процесса ждут друг друга | T1: lock A → wants B; T2: lock B → wants A |
| **Starvation** | Один процесс всегда проигрывает гонку | T1 всегда быстрее, T2 никогда не успеет |
| **Double spend** | Одна операция применена дважды | Payment обработан 2 раза |

---

### 2. Что даёт PostgreSQL «из коробки»

**Изоляция по умолчанию: `READ COMMITTED`.**

| Операция | READ COMMITTED | REPEATABLE READ | SERIALIZABLE |
|----------|---------------|-----------------|--------------|
| Lost Update | **Да** | **Да** | Нет |
| Dirty Read | Нет | Нет | Нет |
| Non-repeatable Read | Да | Нет | Нет |
| Phantom (SELECT) | Да | Нет* | Нет |
| Deadlock | Да (на locks) | Да | Да (на serialization) |

**Ключевой вывод:** даже `REPEATABLE READ` **не защищает от lost update**. Защита — на уровне приложения. `SERIALIZABLE` защищает, но дорогой ценой.

---

### 3. Стратегии защиты

#### Стратегия 1: Атомарный UPDATE (одна строка, арифметика)

**Идея:** не читай, потом пиши. Пиши сразу с условием.

```sql
-- Списание с проверкой баланса
UPDATE accounts
SET balance_cents = balance_cents - :amount,
    version = version + 1,
    updated_at = now()
WHERE id = :id
  AND balance_cents >= :amount
RETURNING id, balance_cents;
```

```php
final class TransferService
{
    public function __construct(private Connection $db) {}

    public function withdraw(string $accountId, int $amountCents): void
    {
        $result = $this->db->executeStatement(
            "UPDATE accounts
             SET balance_cents = balance_cents - :amount,
                 version = version + 1
             WHERE id = :id
               AND balance_cents >= :amount
             RETURNING balance_cents",
            ['amount' => $amountCents, 'id' => $accountId]
        );

        if ($result === false || $result === []) {
            throw new InsufficientBalanceException($accountId, $amountCents);
        }
    }
}
```

**Что происходит при конфликте:**

```
T1: UPDATE ... WHERE balance >= 300 → баланс 500→200, 1 row
T2: UPDATE ... WHERE balance >= 400 → баланс 200 < 400, 0 rows → исключение
```

Нет lost update. PostgreSQL сериализует `UPDATE` на уровне строки (row-level lock на время выполнения UPDATE).

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Арифметика над одним столбцом | `balance = balance - X` |
| Проверка + изменение в одной операции | `WHERE balance >= X` |
| Счётчики | `views = views + 1` |
| Лимиты | `WHERE used < limit` |

**Ограничения:**
- Только одна строка.
- Только простая арифметика / сравнение.
- Не работает с коллекциями (добавить строку в `invoice_lines`).
- Не позволяет выполнить сложную бизнес-логику между SELECT и UPDATE.

---

#### Стратегия 2: Optimistic Locking (version field)

**Идея:** читай свободно, при записи проверяй, что никто не изменил.

```sql
ALTER TABLE invoices ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
final class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repo,
        private Connection $db,
    ) {}

    public function addLine(int $invoiceId, int $productId, int $qty): Invoice
    {
        // 1. Чтение (без блокировки)
        $invoice = $this->repo->findById($invoiceId);
        $version = $invoice->getVersion();

        // 2. Доменная логика (свободно, без locks)
        $invoice->addLine(new InvoiceLine($productId, $qty));

        // 3. Запись с проверкой версии
        $affected = $this->db->executeStatement(
            "UPDATE invoices
             SET total_cents = :total,
                 version = version + 1
             WHERE id = :id
               AND version = :expectedVersion",
            [
                'total'             => $invoice->total(),
                'id'                => $invoiceId,
                'expectedVersion'   => $version,
            ]
        );

        if ($affected === 0) {
            throw new ConcurrentModificationException(
                "Invoice #{$invoiceId} was modified. Retry."
            );
        }

        // 4. Сохранение линий (в той же транзакции)
        $this->repo->saveLines($invoice);

        return $invoice;
    }
}
```

**Retry loop:**

```php
final class InvoiceService
{
    private const MAX_RETRIES = 3;

    public function addLine(int $invoiceId, int $productId, int $qty): Invoice
    {
        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            try {
                return $this->doAddLine($invoiceId, $productId, $qty);
            } catch (ConcurrentModificationException $e) {
                if ($attempt === self::MAX_RETRIES - 1) {
                    throw $e;
                }
                // Перечитать, пересчитать, повторить
                usleep(random_int(1000, 5000)); // jitter
            }
        }
    }

    private function doAddLine(int $invoiceId, int $productId, int $qty): Invoice
    {
        // ... логика из примера выше ...
    }
}
```

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Коллекции (добавить/удалить элементы) | `invoice_lines` |
| Сложная бизнес-логика (несколько шагов) | `addLine + recalculateTotal + validate` |
| Высокая конкурентность (конфликты редки) | 100+ concurrent, но на разных записях |
| Нет жёсткого требования к порядку | Любой из двух может «выиграть» |

**Ограничения:**
- При частых конфликтах → много retry → деградация.
- Нужно поле `version` в каждой таблице.
- Все `UPDATE` в агрегате должны проверять `version` корня.
- Не гарантирует порядок (FIFO).

---

#### Стратегия 3: Pessimistic Locking (SELECT FOR UPDATE)

**Идея:** заблокируй строку, работай, отпусти.

```php
final class InvoiceService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function addLine(int $invoiceId, int $productId, int $qty): Invoice
    {
        $entity = $this->em
            ->getRepository(InvoiceEntity::class)
            ->createQueryBuilder('i')
            ->where('i.id = :id')
            ->setParameter('id', $invoiceId)
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)  // ← SELECT FOR UPDATE
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity === null) {
            throw new InvoiceNotFoundException($invoiceId);
        }

        // На это время другие процессы ЖДУТ
        $invoice = $entity->toDomain();
        $invoice->addLine(new InvoiceLine($productId, $qty));

        $this->em->flush();
        // BLOK отпущен при COMMIT
    }
}
```

**SQL, который генерирует Doctrine:**

```sql
BEGIN;
SELECT * FROM invoices WHERE id = 42 FOR UPDATE;
-- (другие процессы, попытавшиеся FOR UPDATE на id=42, БЛОКИРУЮТСЯ здесь)

UPDATE invoices SET total_cents = 350 WHERE id = 42;
INSERT INTO invoice_lines (invoice_id, product_id, qty) VALUES (42, 5, 1);

COMMIT;
-- BLOK отпущен
```

**Что происходит при конфликте:**

```
T1: SELECT ... FOR UPDATE → lock acquired
T2: SELECT ... FOR UPDATE → BLOCKED (ждёт)
T1: UPDATE → COMMIT → lock released
T2: получил lock → SELECT → UPDATE → COMMIT
```

T2 **не теряет** данные. Он просто **ждёт**.

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Критичная операция, цена ошибки максимальна | Списание средств, списание с инвентаря |
| Низкая конкурентность (< 5 concurrent на запись) | Внутренний инструмент |
| Нужен **строгий порядок** (FIFO) | Очередь заявок |
| Операция короткая (< 100ms) | Простое обновление статуса |
| Невозможно выразить через `UPDATE ... WHERE` | Сложная многошаговая логика |

**Ограничения:**
- Блокировка → снижение конкурентности.
- Deadlock при неправильном порядке (T1: lock A → B; T2: lock B → A).
- Если T1 завис → T2 ждёт (до `lock_timeout`).
- При высокой конкурентности → очередь на lock → деградация.
- Держать lock **минимально** коротким.

---

#### Стратегия 4: SERIALIZABLE Isolation

**Идея:** PostgreSQL сам детектирует конфликты и откатывает.

```php
// config/packages/doctrine.yaml
doctrine:
    dbal:
        isolation: SERIALIZABLE
```

```php
$em->transactional(function () use ($invoiceId, $productId) {
    $invoice = $this->repo->find($invoiceId);
    $invoice->addLine(new InvoiceLine($productId, 2));
    $this->em->flush();
});
// Если конфликт → Doctrine\DBAL\Exception\SerializationFailure
// → catch → retry
```

**Что происходит:**

```
T1: BEGIN → SELECT → UPDATE → COMMIT ✅
T2: BEGIN → SELECT → UPDATE → COMMIT
    → PostgreSQL: "T2 читал данные, которые T1 изменил" → SerializationFailure
    → ROLLBACK
    → Retry
```

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Редкие операции, цена ошибки максимальна | Финансовые транзакции |
| Сложная multi-row операция | Перенос между счётами |
| Низкая конкурентность | 1–5 concurrent |
| Нельзя выразить через `UPDATE ... WHERE` | Кросс-табличная логика |

**Ограничения:**
- Высокая цена: при конфликтах — откат + retry.
- Под высокой нагрузкой (> 50 concurrent на запись) → много откатов → деградация.
- Сложно диагностировать (`SerializationFailure` без контекста).
- Не масштабируется.

---

#### Стратегия 5: Idempotency (UNIQUE + ON CONFLICT)

**Идея:** повторная операция не меняет состояние.

```sql
CREATE TABLE payments (
    id              SERIAL PRIMARY KEY,
    account_id      INT NOT NULL,
    idempotency_key VARCHAR(100) NOT NULL,
    amount_cents    INT NOT NULL,
    status          VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (account_id, idempotency_key)
);
```

```php
final class PaymentService
{
    public function charge(string $accountId, int $amount, string $idempotencyKey): PaymentResult
    {
        $result = $this->db->executeQuery(
            "INSERT INTO payments (account_id, idempotency_key, amount_cents)
             VALUES (:acc, :key, :amount)
             ON CONFLICT (account_id, idempotency_key) DO NOTHING
             RETURNING id, status",
            ['acc' => $accountId, 'key' => $idempotencyKey, 'amount' => $amount]
        )->fetchAssociative();

        if ($result === false) {
            // Уже существовала — вернуть существующую
            $existing = $this->db->executeQuery(
                "SELECT id, status FROM payments
                 WHERE account_id = :acc AND idempotency_key = :key",
                ['acc' => $accountId, 'key' => $idempotencyKey]
            )->fetchAssociative();

            return new PaymentResult($existing['id'], $existing['status']);
        }

        return new PaymentResult($result['id'], 'pending');
    }
}
```

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Двойная обработка (retry, crash) | Payment, webhook |
| Идемпотентный API | `POST /pay` с `Idempotency-Key` |
| At-least-once delivery | Очереди, async |

**Ограничения:**
- Только для «вставить один раз».
- Не защищает от concurrent update (только от duplicate insert).
- Нужен уникальный ключ (обычно `idempotency_key` или бизнес-ключ).

---

#### Стратегия 6: Single Writer (очередь)

**Идея:** только один процесс пишет в один момент времени.

```
Client A → Queue → Worker (single) → DB
Client B → Queue → Worker (single) → DB
Client C → Queue → Worker (single) → DB
```

```php
// Worker: single instance
#[AsCommand(name: 'process:invoices')]
final class ProcessInvoicesCommand extends Command
{
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        while (true) {
            $task = $this->queue->consume();  // блокирующее чтение
            $this->process($task);
            $this->queue->ack($task);
        }
    }
}
```

**Когда использовать:**

| Ситуация | Пример |
|----------|--------|
| Очередь заявок (FIFO) | Заявки на отмену, одобрение |
| Один «владелец» записи | Один оператор редактирует |
| Очень низкая конкурентность | Внутренние инструменты |

**Ограничения:**
- Bottleneck: один worker = один запрос в секунду.
- Не масштабируется.
- Если worker упал — очередь растёт.

---

### 4. Сравнение

| Стратегия | Конкурентность | Retry? | Блокировка | Сложность | Когда |
|-----------|---------------|--------|------------|-----------|-------|
| Атомарный `UPDATE` | Очень высокая | Нет | Кратковременная (row lock) | Низкая | Арифметика, один столбец |
| Optimistic (version) | Высокая | Да | Нет | Средняя | Коллекции, сложная логика |
| Pessimistic (FOR UPDATE) | Низкая | Нет | Долгая (на время транзакции) | Низкая | Критичные, низкая нагрузка |
| SERIALIZABLE | Низкая | Да (авто) | Soft (откат) | Средняя | Редкие, критичные |
| Idempotency (UNIQUE) | Высокая | Нет (ON CONFLICT) | Кратковременная | Низкая | Double-spend, retry |
| Single Writer | Очень низкая | Нет (очередь) | Очередь | Низкая | FIFO, один владелец |

---

### 5. Матрица выбора

```
Вопрос: что делает операция?

├── Арифметика над одним столбцом + проверка
│   (balance, counter, limit)
│   → Атомарный UPDATE (Стратегия 1)
│
├── Добавить/удалить элементы коллекции
│   ├── Конкурентность > 10 на запись
│   │   → Optimistic Locking + retry (Стратегия 2)
│   └── Конкурентность < 5, нужен порядок
│       → Pessimistic Locking (Стратегия 3)
│
├── «Вставить один раз, повтор не должен изменить»
│   → Idempotency (Стратегия 5)
│
├── Кросс-табличная логика, редкая, критичная
│   → SERIALIZABLE (Стратегия 4)
│
├── FIFO-очередь, один владелец
│   → Single Writer (Стратегия 6)
│
└── Комбинация:
    Атомарный UPDATE (баланс) + Idempotency (платёж) + Optimistic (коллекция)
```

---

### 6. Комбинация стратегий (реальный сценарий)

**Сценарий: списание с баланса + запись платежа + идемпотентность**

```php
final class TransferFundsService
{
    public function __construct(
        private Connection $db,
    ) {}

    public function execute(
        string $fromId,
        string $toId,
        int $amountCents,
        string $idempotencyKey,
    ): void {
        $this->db->beginTransaction();

        try {
            // 1. Идемпотентность: проверить, не было ли уже
            $existing = $this->db->fetchAssociative(
                "SELECT id FROM payments
                 WHERE idempotency_key = :key",
                ['key' => $idempotencyKey]
            );

            if ($existing !== false) {
                $this->db->rollBack();
                return; // Уже обработано — ничего не делаем
            }

            // 2. Атомарное списание (Стратегия 1)
            $affected = $this->db->executeStatement(
                "UPDATE accounts
                 SET balance_cents = balance_cents - :amount
                 WHERE id = :from
                   AND balance_cents >= :amount",
                ['amount' => $amountCents, 'from' => $fromId]
            );

            if ($affected === 0) {
                throw new InsufficientBalanceException($fromId);
            }

            // 3. Атомарное зачисление
            $this->db->executeStatement(
                "UPDATE accounts
                 SET balance_cents = balance_cents + :amount
                 WHERE id = :to",
                ['amount' => $amountCents, 'to' => $toId]
            );

            // 4. Запись платежа (UNIQUE защищает от дубля)
            $this->db->executeStatement(
                "INSERT INTO payments (from_id, to_id, amount_cents, idempotency_key)
                 VALUES (:from, :to, :amount, :key)",
                ['from' => $fromId, 'to' => $toId, 'amount' => $amountCents, 'key' => $idempotencyKey]
            );

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
```

**Что здесь защищено:**
- **Lost Update** → атомарный `UPDATE ... WHERE balance >= X`
- **Double spend** → `UNIQUE (idempotency_key)` + проверка до операции
- **Race condition** → `UPDATE` сериализуется PostgreSQL на уровне строки
- **Partial state** → транзакция (BEGIN/COMMIT)

---

### 7. Типовые ошибки

#### ❌ 1. SELECT → проверка → UPDATE (без WHERE)

```php
$balance = $db->query("SELECT balance FROM accounts WHERE id = ?", [$id])->fetchOne();
if ($balance >= $amount) {
    $db->query("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $id]);
}
// GAP: между SELECT и UPDATE другой процесс списал. Lost update.
```

**Правильно:** `UPDATE ... WHERE balance >= :amount` (Стратегия 1).

#### ❌ 2. Pessimistic lock + долгая транзакция

```php
$em->transactional(function () {
    $entity = $repo->createQueryBuilder('i')
        ->setLockMode(PESSIMISTIC_WRITE)
        ->getQuery()->getOneResult();

    // ... 3 секунды обработки ...
    // ... HTTP-запрос к внешнему API ...
    // ... email ...

    $em->flush();
});
// BLOK удерживался 5+ секунд. Другие процессы ждали. Деградация.
```

**Правильно:** блокировка — минимум. Внешние вызовы — до или после.

#### ❌ 3. Pessimistic lock в цикле

```php
foreach ($1000_invoices as $invoice) {
    $entity = $repo->createQueryBuilder('i')
        ->setLockMode(PESSIMISTIC_WRITE)
        ->getQuery()->getOneResult();
    // 1000 блокировок в одной транзакции → deadlock
}
```

**Правильно:** batch + optimistic, или разбить на маленькие транзакции.

#### ❌ 4. `SERIALIZABLE` + высокая конкурентность

```php
// 100 concurrent на одну запись
// SERIALIZABLE: 50% SerializationFailure → 50% retry → деградация
```

**Правильно:** optimistic или atomic UPDATE.

#### ❌ 5. Idempotency без проверки до операции

```php
// ON CONFLICT DO NOTHING — хорошо
// Но: если INSERT прошёл, а UPDATE после него упал → дубль
$this->db->executeStatement("INSERT INTO payments ... ON CONFLICT DO NOTHING");
$this->db->executeStatement("UPDATE accounts SET balance = balance - ...");
// ↑ Если UPDATE упал, INSERT уже в БД. При retry — INSERT пропустится,
//   но UPDATE выполнится → двойное списание!
```

**Правильно:** всё в одной транзакции. INSERT + UPDATE атомарно.

#### ❌ 6. Версионирование без проверки на всех UPDATE

```php
// ✅ Invoice: UPDATE ... WHERE version = :v
// ❌ InvoiceLine: UPDATE ... (без version)
// → Lost update на lines, хотя invoice защищён
```

**Правильно:** если агрегат имеет version — **все** UPDATE по всем таблицам агрегата должны проверять version корня. Либо один `flush()` (cascade).

---

### 8. Итоговый чеклист

```
□ Операция — арифметика над одним столбцом?
  → UPDATE ... WHERE condition (Стратегия 1)

□ Операция — добавление/удаление элементов?
  → Optimistic (version) + retry (Стратегия 2)

□ Низкая конкурентность + нужен порядок?
  → Pessimistic (FOR UPDATE), короткая транзакция (Стратегия 3)

□ Редкая критичная операция?
  → SERIALIZABLE (Стратегия 4)

□ Двойная обработка возможна (retry, crash)?
  → Idempotency: UNIQUE + ON CONFLICT (Стратегия 5)

□ FIFO, один владелец?
  → Single Writer / очередь (Стратегия 6)

□ Все операции в одной транзакции?
  → BEGIN ... COMMIT. Атомарность.

□ Version на всех UPDATE в агрегате?
  → Да. Иначе lost update на children.

□ BLOK удерживается минимально?
  → Да. Внешние вызовы — до/после.
```

> ⏱ 60.6s · prompt 88989 tok · output 6210 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

Пользователь продолжает собеседование на PHP-бэкенд. Предыдущие ответы были структурированными, с таблицами, кодом, сравнениями. Нужно ответить в том же стиле: практично, с обоснованием "почему так, а не иначе", с конкретикой для Symfony/PHP стека.

Структура ответа:
1. Ветвление — trunk-based vs git-flow, выбор и почему
2. Формат коммитов — conventional commits, почему
3. PR — размер, структура, чеклист
4. Ревью — правила, сроки, что искать
5. Релизы — tag, changelog, rollout
6. Антипаттерны

Ключевой принцип: отвечать как практик с 5 лет опыта, а не как учебник. Показывать trade-offs, а не догмы. Указать, что зависит от размера команды.

</details>

**Кандидат:**

## Git-процесс: от задачи до продакшена

### Контекст: что определяет процесс

| Фактор | Малая команда (1–3) | Средняя (5–15) | Большая (15+) |
|--------|---------------------|----------------|---------------|
| Ветвление | Trunk-based | Trunk-based + feature flags | Git-flow / Trunk-based |
| Ревью | 1 reviewer | 1–2 reviewer | 2 reviewer + approve |
| Релизы | Daily / on-demand | 2–3 раза в неделю | Scheduled (e.g. Tues/Thurs) |
| CI | Минимальный | Полный pipeline | Полный + staging + canary |
| Tooling | GitHub/GitLab | + Dependabot, changelog | + ArgoCD, Helm, feature flags |

Я опишу процесс для **средней команды (5–15 разработчиков)** на Symfony + PostgreSQL, деплой в Docker/K8s. Это самый частый случай.

---

### 1. Ветвление: Trunk-Based

```
main ──────────────────────────────────────────────────►
  │  ▲  ▲  ▲  ▲  ▲  ▲  ▲  ▲  ▲  ▲  ▲
  │  │  │  │  │  │  │  │  │  │  │  │
  └──┘  └──┘  └──┘  └──┘  └──┘
  feat/x  fix/y  feat/z  ...
  (короткие ветки, ≤ 1 день)
```

**Правила:**

| Правило | Зачем |
|---------|-------|
| `main` — всегда деплоится | Нет «релизных веток», которые дивергируют |
| Feature-ветки живут ≤ 1 день | Чем длиннее ветка, тем больнее merge |
| Ветка создаётся **после** дизайна, не до | Не держим «мёртвые» ветки в ожидании |
| Merge только через PR | Никогда не `git push origin main` напрямую |
| Squash merge | Лог `main` читается как история задач, не коммитов |

#### Почему trunk-based, а не git-flow

| Критерий | Git-flow | Trunk-based |
|----------|----------|-------------|
| Ветки | `develop`, `release/x`, `hotfix/y` — 3+ ветки | Только `main` + короткие feature |
| Merge-конфликты | Частые (develop дивергирует) | Редкие (ветка ≤ 1 день) |
| Деплой | Ручной с `release` ветки | Автоматический с `main` |
| Hotfix | Отдельная ветка, отдельный процесс | Прямой PR в `main` |
| Сложность | Высокая (5+ веток, правила ветвления) | Низкая (1 ветка + правило) |
| Скорость | Медленнее (ожидание release) | Быстрее (deploy anytime) |
| Риск | `develop` гниёт, `release` дивергирует | Нет «мёртвых» веток |

**Git-flow оправдан, когда:**
- Релизы по расписанию (e.g. мобильное приложение — App Store review)
- Долгие feature-ветки (2+ недели) — и то лучше feature flags
- Несколько параллельных релизов (v1.x и v2.x одновременно)

**Trunk-based оправдан, когда:**
- Веб-приложение, деплой — 5 минут
- Feature flags есть (незрелый фича не ломает prod)
- Команда < 20 человек
- CI быстрый (< 10 мин)

**Компромисс для Symfony:** если фича занимает 2+ дня — разбивать на 2–3 PR'а, каждый ≤ 1 день. Если не удаётся — feature flag + короткий PR на каждый шаг.

---

### 2. Формат коммитов: Conventional Commits

#### Формат

```
<type>(<scope>): <subject>

[body — необязательно]

[footer — breaking change, refs]
```

#### Типы

| Type | Когда | Пример |
|------|-------|--------|
| `feat` | Новая функциональность | `feat(invoice): add partial payment support` |
| `fix` | Исправление бага | `fix(payment): handle 3DS redirect` |
| `refactor` | Изменение без изменения поведения | `refactor(order): extract PricingCalculator` |
| `perf` | Улучшение производительности | `perf(report): add covering index for top query` |
| `test` | Добавление/исправление тестов | `test(invoice): cover concurrent payment scenario` |
| `docs` | Документация | `docs(api): describe 409 conflict response` |
| `chore` | Build, deps, config | `chore: upgrade doctrine to 2.20` |
| `ci` | CI/CD | `ci: add parallel test runner` |

#### Примеры

```
feat(invoice): add payment retry with exponential backoff

When payment gateway returns 429, retry up to 3 times with
1s, 2s, 4s delay. Tracks attempts in payment_attempts column.

Refs: #1234
```

```
fix(auth): reject expired JWT before checking signature

Previously, expired tokens were signature-validated first,
leaking timing information. Now, `exp` claim is checked
before RSA verification.

Breaking change: none
Closes: #567
```

```
chore(deps): bump symfony/symfony from 6.4 to 7.1

- Remove deprecated `Session::migrate` calls
- Update `form_login` config (no longer supports `always authenticate`)
```

#### Почему Conventional Commits

| Причина | Без формата | С форматом |
|---------|-------------|-----------|
| Чangelog | Ручной написание | Автоматический (semantic-release, conventional-changelog) |
| Search | `git log --grep "payment"` | `git log --grep "^feat(payment)"` |
| Ревью | «Что сделал?» | Видно по type + scope |
| Breaking change | Забыли | `BREAKING CHANGE:` в footer → алерт |
| Auto-release | Ручной tag | `feat` → minor, `fix` → patch, `BREAKING` → major |

#### Что НЕ писать в коммитах

```
❌ "fix"
❌ "asdf"
❌ "update stuff"
❌ "WIP"
❌ "final version"
❌ "finally works"
```

---

### 3. Pull Request

#### Структура PR

```markdown
## Title
feat(invoice): add partial payment support

## Description
Что делает, зачем, как использовать.
Не более 5–7 строк.

## Changes
- Добавлен `PartialPayment` entity
- Инвариант: partial + remaining = total
- Endpoint: POST /api/v1/invoices/{id}/partial-pay
- Migration: `2024-01-15_add_partial_payments.php`

## Testing
- [x] Unit: `PartialPaymentTest` (12 тестов)
- [x] Integration: `PartialPaymentUseCaseTest` (5 тестов)
- [ ] E2E: добавить сценарий «partial → full → paid»
- [ ] Manual: проверить 3DS flow

## Risk
- Миграция: добавление колонки + индекс. Low risk.
- API: новый endpoint, не ломает существующие.

## Screenshot / Log
(если UI / сложный flow)
```

#### Правила PR

| Правило | Почему |
|---------|--------|
| **≤ 400 строк** (изменённого кода) | Выше — не ревьюится качественно |
| **≤ 1 задача** | 1 PR = 1 фича / 1 фикс. Не «улучшения по пути» |
| **CI зелёный до merge** | Никогда не mergить с failing tests |
| **≥ 1 approve** | Minimum 1. Для критичных — 2 |
| **≤ 3 дня в ожидании ревью** | Старший помогает, если завис |
| **Squash merge** | Лог `main` = история задач |
| **Update branch, не rebase на main** | Rebase на main ломает CI-кэш и ревью |

#### Почему ≤ 400 строк

| Размер | Качество ревью |
|--------|---------------|
| 50 строк | Отличное. Видно контекст |
| 200 строк | Хорошее. Нужно переключаться 2–3 раза |
| 400 строк | Среднее. 30–60 мин ревьюера |
| 1000 строк | Плохое. 2–4 часа. Мелкие баги не видны |
| 3000+ строк | Не ревью. Merge on faith |

**Если фича = 2000 строк:** разбить на 3–4 PR'а. Каждый ≤ 400.

#### Что ревьюер НЕ ищет (и почему)

| Не ищу | Почему |
|--------|--------|
| Пустые строки, отступы | Linter / formatter решает |
| Имена переменных (мелкие) | Team convention, не каждый PR |
| `use` порядок | IDE / linter |

**Что ищу:**

| Ищу | Почему |
|-----|--------|
| Бизнес-логика: правильное ли правило? | Только человек знает домен |
| Границы: не нарушает ли SRP/OCP? | Архитектурная целостность |
| Ошибки: что если X = null / 0 / -1? | Краевые случаи |
| БД: N+1? Index? Migration safe? | Производительность |
| Безопасность: SQL injection? Auth bypass? | Критично |
| Тесты: покрывают ли сценарий? | Защита от регрессии |
| API: бэкт-компат? | Не сломать клиентов |

---

### 4. Ревью

#### Правила ревью

| Правило | Деталь |
|---------|--------|
| **Срок:** ≤ 4 часов (рабочее время) | Не ждать 2 дня. Ускорить → ускорить |
| **Тон:** «Мне кажется…» / «Что если…?» | Не «Ты ошибся» |
| **Blocker / Nit / Question** | Разделять. Nit не блокирует merge |
| **Обсуждение в PR, не в Slack** | Контекст сохраняется |
| **Approve = я прочитал и готов mergить** | Не «выглядило ок» |
| **Request changes = есть blocker** | Не «улучши, но можешь mergить» |
| **Если не понял — спросить** | Не «ну ок» |

#### Маркеры в ревью

```
[Blocker]  Это надо исправить перед merge
[Nit]      Не критично, но лучше так
[Question] Не понял, объясни
[Suggestion] Вот альтернатива, на ваш выбор
[LGTM]     Готов mergить
```

#### Антипаттерны ревью

| ❌ | Почему |
|----|--------|
| «LGTM» не читая | Формальность. Ревью без чтения = нет ревью |
| 50 нитов в одном PR | Ревьюер утомлён. Blockers теряются |
| «Это не мой код, не мой вопрос» | В команде — общий код |
| Ревью через 3 дня | Мотивация автора упала, контекст потерян |
| «Почему так? Переделай» без объяснения | Не конструктивно |

---

### 5. Релизы

#### Модель: Continuous Deployment

```
main → CI (test + lint) → Build Docker image → Push registry → Deploy staging → Smoke test → Deploy prod
```

**Нет «релизных дней».** `main` деплоится, когда CI зелёный и нет blocker'ов.

#### CI Pipeline

```yaml
# .github/workflows/ci.yml (упрощённо)
name: CI
on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

jobs:
  tests:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: app_test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
        options: >-
          --health-cmd="pg_isready -U test"
          --health-interval=1s
          --health-timeout=5s
          --health-retries=10
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction --prefer-dist
      - run: php bin/console doctrine:database:create --if-not-exists
      - run: php bin/console doctrine:schema:sync
      - run: vendor/bin/phpunit --processes 4
      - name: PHPStan
        run: vendor/bin/phpstan analyse --level 8 src tests

  arch:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - name: Architecture tests
        run: vendor/bin/phpunit tests/Architecture/

  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer audit
      - run: npm audit --production  # если есть frontend

  build:
    needs: [tests, arch, security]
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Build image
        run: docker build -t registry.example.com/app:${{ github.sha }} .
      - name: Push
        if: github.ref == 'refs/heads/main'
        run: |
          docker push registry.example.com/app:${{ github.sha }}
          docker tag registry.example.com/app:${{ github.sha }} registry.example.com/app:latest
          docker push registry.example.com/app:latest
```

#### Deploy

```yaml
# Kubernetes (упрощённо)
apiVersion: apps/v1
kind: Deployment
metadata:
  name: app
spec:
  replicas: 3
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  template:
    spec:
      containers:
        - name: app
          image: registry.example.com/app:latest  # ← tag от CI
          readinessProbe:
            httpGet:
              path: /health
              port: 8080
            initialDelaySeconds: 5
            periodSeconds: 10
          livenessProbe:
            httpGet:
              path: /health
              port: 8080
            initialDelaySeconds: 15
            periodSeconds: 30
```

**Rolling update:** новые поды → readiness OK → старые поды убиваются. Zero-downtime.

#### Hotfix-процесс

```
1. PR из main → hotfix/issue-# (≤ 200 строк)
2. 1 reviewer, 1 approve
3. Merge в main → CI → deploy
4. Если нужно в release/x (если есть) — cherry-pick
5. Post-mortem (если инцидент)
```

**Важно:** hotfix **не** обходит CI. Не mergить без тестов «потому что горит».

---

### 6. Feature Flags (для длинных фич)

Когда фича > 1 день и нельзя mergить «половину»:

```php
// src/Application/Invoice/UseCases/PayInvoiceUseCase.php
final class PayInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoices,
        private PaymentGatewayInterface    $payments,
        private FeatureFlagService         $flags,  // ← внешний сервис
    ) {}

    public function execute(PayInvoiceCommand $cmd): Invoice
    {
        $invoice = $this->invoices->findById($cmd->invoiceId)
            ?? throw new InvoiceNotFoundException($cmd->invoiceId);

        if ($this->flags->isEnabled('invoice.partial_payment', $invoice->getCustomerId())) {
            // Новая логика
            return $this->executePartial($cmd, $invoice);
        }

        // Старая логика
        return $this->executeFull($cmd, $invoice);
    }
}
```

```yaml
# config/services.yaml
services:
    App\Application\Invoice\Port\FeatureFlagService:
        alias: App\Infrastructure\Flags\UnleashFlagService
```

**Правила:**
- Flag живёт ≤ 2 недели (иначе — tech debt)
- После 100% rollout — удалить flag + старую ветку
- Flag не в домене, а в application layer

---

### 7. Частые антипаттерны

| ❌ | Почему плохо | ✅ |
|----|-------------|-----|
| `develop` + `release` + `hotfix` = 5 веток | Дивергенция, merge-ад | Trunk-based + feature flags |
| Коммит «fix» | Не ищется, не понимает | Conventional Commits |
| PR на 3000 строк | Не ревьюится | Разбить на 5 PR'ов |
| «LGTM» не читая | Формальность | Прочитать или не аппрувить |
| Деплой в пятницу вечером | Никто не чинит | Deploy только будни, до 15:00 |
| Hotfix без CI | «Горит» → баг в prod | CI всегда. Быстрый CI > медленный hotfix |
| `main` без protection | Push напрямую | Branch protection: PR + CI + approve |
| Rebase на main в PR | Ломает CI-кэш, ревью | `git pull` / «Update branch» |
| 10 нитов в PR | Blockers теряются | 1–2 blocker, остальное — follow-up issue |

---

### 8. Итог: полный цикл

```
Задача (Jira / Linear)
    │
    ▼
Дизайн / RFC (если фича > 1 день)
    │
    ▼
Feature branch: feat/invoice-partial-payment
    │
    ▼
Коммит: feat(invoice): add PartialPayment entity
    │
    ▼
PR (≤ 400 строк) + CI (test + phpstan + arch)
    │
    ▼
Ревью (≤ 4 часа, 1 approve)
    │
    ▼
Squash merge → main
    │
    ▼
CI (full) → Docker build → Push registry
    │
    ▼
Deploy staging → Smoke test (≤ 10 мин)
    │
    ▼
Deploy prod (rolling update)
    │
    ▼
Мониторинг (10 мин) → закрытие задачи
```

**Почему так, а не иначе:**

| Решение | Альтернатива | Почему выбрал |
|---------|-------------|---------------|
| Trunk-based | Git-flow | Меньше merge-конфликтов, быстрее деплой, проще hotfix |
| Squash merge | Merge commit | Лог читается, проще revert |
| Conventional Commits | Свободный формат | Авто-changelog, поиск, breaking change tracking |
| ≤ 400 строк PR | Большой PR | Качество ревью, скорость |
| CI всегда | «Ручной» деплой | Детерминизм, отлов регрессий |
| Rolling update | Blue-green | Проще, достаточно для 95% случаев |
| Feature flags | Долгая ветка | `main` всегда зелёный, нет дивергенции |
| Branch protection | Trust | Не «доверь, но проверь» — «проверь, потом доверь» |

---

### 9. Чеклист для команды

```
□ Branch protection на main: PR + CI + 1 approve
□ Conventional Commits в CONTRIBUTING.md
□ PR template (Description, Changes, Testing, Risk)
□ CI: test + phpstan + arch + security + build
□ CI время: < 10 мин (иначе — параллелить)
□ Deploy: rolling, zero-downtime, rollback < 5 мин
□ Feature flags: ≤ 2 недели жизни, трекер в Jira
□ Hotfix: PR в main, CI обязательна
□ Post-mortem: после каждого P1/P2
□ Ревью SLA: 4 часа (рабочее время)
```

> ⏱ 62.6s · prompt 94930 tok · output 5133 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о практике код-ревью: маркировка замечаний, приоритеты, разрешение разногласий. Это продолжение серии вопросов о PHP/Symfony разработке. Нужно дать практичный, конкретный ответ с примерами кода, таблицами, антипаттернами. Структурирую: 1) принцип ревью, 2) система маркировки (Blocker/Nit/Question/Suggestion), 3) на что смотрю в первую очередь (приоритизация), 4) конкретные примеры ревью-комментариев на PHP-коде, 5) разрешение разногласий, 6) антипаттерны. Важно — не повторять то, что уже было в ответе про git-процесс (там кратко было про ревью), а углубиться. Дам реальный пример PHP-кода с ревью-комментариями — это наиболее ценная часть.

</details>

**Кандидат:**

## Хороший код-ревью

### Принцип

> **Ревью — это не «найди баги» и не «покажи, что ты умнее». Это передача контекста от одного человека к другому и защита качества на границе merge.**

Хороший ревьюер:
- Понимает **зачем** написан код (не только **как**).
- Отделяет критичное от косметического.
- Завершается за 30–60 минут на PR ≤ 400 строк.
- Оставляет PR в состоянии, где автору **легко** внести изменения.

---

### 1. Система маркировки

Каждый комментарий — с тегом. Без тегов ревью превращается в «список жалоб».

| Тег | Значение | Блокирует merge? |
|-----|----------|-----------------|
| **`[BLOCKER]`** | Без исправления — merge невозможен. Баг, безопасность, нарушение контракта | **Да** |
| **`[MAJOR]`** | Надо исправить, но можно обсудить альтернативу. Логика, архитектура | Да (или явный waiver) |
| **`[MINOR]`** | Стоит улучшить. Не блокирует. «Сделай, если не сложно» | Нет |
| **`[NIT]`** | Косметика. Стиль, имена, формат | Нет |
| **`[QUESTION]`** | Не понял. Нужна объяснение или код сам по себе ок | Нет (но требует ответа) |
| **`[PRAISE]`** | Хорошее решение. Подкрепит автора | Нет |
| **`[FOLLOWUP]`** | Записать в таск, не сейчас | Нет |

**Почему важно:** автор видит 15 комментариев. Без тегов он не знает, что из них «надо», а что «можно». С тегами — 2 BLOCKER'а + 10 NIT → ясен приоритет.

---

### 2. На что смотрю в первую очередь

Приоритизация. Если ревью длиннее 30 минут — я начинаю сверху вниз, и первые 10 минут идут на критичное.

#### Tier 1: correctness (правильность)

| Что | Пример |
|-----|--------|
| **Краевые случаи** | `null`, `0`, `''`, empty array, отрицательные числа |
| **Ошибки** | Что происходит, если БД недоступна? Timeout? Exception? |
| **Логика** | Верно ли условие? Не инвертирован ли `if`? |
| **Атомарность** | Транзакция? Частичный update? |
| **Безопасность** | SQL injection? Auth bypass? XSS? Секреты в логах? |
| **Идемпотентность** | Retry — безопасно? Double-spend? |

**Это BLOCKER'ы.** Всё остальное — после.

#### Tier 2: architecture (архитектура)

| Что | Пример |
|-----|--------|
| **Граница слоя** | Бизнес-логика в контроллере? Doctrine в домене? |
| **SRP** | Класс делает 3 вещи? |
| **Зависимости** | Инверсия? Циклические? |
| **Расширяемость** | Новый сценарий = правка существующего класса? |
| **Именование** | `process()` без контекста? `data` вместо `order`? |

**MAJOR / MINOR.** Зависит от влияния.

#### Tier 3: maintainability (поддерживаемость)

| Что | Пример |
|-----|--------|
| **Читаемость** | Понятно без объяснения? |
| **Дублирование** | Тот же код в 3 местах? |
| **Тесты** | Покрыты ли сценарии? |
| **Комментарии** | «Зачем» (хорошо) vs «что» (плохо)? |

**MINOR / NIT.**

#### Tier 4: style (стиль)

| Что |
|-----|
| Отступы, порядок `use`, длина строки |

**NIT / не пишу вообще** (linter решает).

---

### 3. Пример реального ревью

**PR:** `feat(invoice): add partial payment`

**Изменённый код:**

```php
// src/Interface/Http/InvoiceController.php
#[Route('/api/v1/invoices/{id}/partial-pay', methods: ['POST'])]
public function partialPay(int $id, Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true) ?? [];
    $amount = (int) ($data['amount'] ?? 0);

    $payment = $this->paymentService->charge(
        token: $data['token'] ?? '',
        amount: $amount,
    );

    $invoice = $this->invoiceService->addPayment($id, $payment);

    return new JsonResponse(['id' => $invoice->getId()]);
}
```

```php
// src/Application/Invoice/InvoiceService.php
public function addPayment(int $invoiceId, Payment $payment): Invoice
{
    $invoice = $this->repo->find($invoiceId);
    $invoice->applyPayment($payment);
    $this->repo->save($invoice);
    return $invoice;
}
```

```php
// src/Domain/Invoice/Invoice.php
public function applyPayment(Payment $payment): void
{
    $this->totalPaid += $payment->amount;

    if ($this->totalPaid >= $this->total) {
        $this->status = InvoiceStatus::Paid;
    } else {
        $this->status = InvoiceStatus::PartiallyPaid;
    }
}
```

#### Мои комментарии

**1.**
```
[QUESTION] @L12
$amount = (int) ($data['amount'] ?? 0);

Если клиент пришлёт "amount": 150.75 (копейки/центы как float),
(int) обрезает до 150. Это намеренно? 

Предлагаю:
  $amount = (int) round($data['amount'] ?? 0);
или валидировать на входе (Symfony Validator) с чёткой ошибкой 422.
```

**2.**
```
[BLOCKER] @L14
$payment = $this->paymentService->charge(
    token: $data['token'] ?? '',
    amount: $amount,
);

Если amount = 0 (нет поля в JSON) — мы всё равно шлём запрос к платёжному
шлюзу на 0. Это либо free charge, либо ошибка. Нужно:
  if ($amount <= 0) {
      throw new InvalidAmountException($amount);
  }
до charge().
```

**3.**
```
[MAJOR] @L27 (InvoiceService::addPayment)
$this->repo->find($invoiceId);  // может вернуть null

Нет проверки на null. Если invoice не найден → PHP fatal
(call on null). Нужен явный InvoiceNotFoundException + 404.

Также: нет проверки статуса. applyPayment на Cancelled invoice
должно бросать (и, похоже, бросает в домене — ок, но лучше
подтвердить).
```

**4.**
```
[MAJOR] @L34 (Invoice::applyPayment)
$this->totalPaid += $payment->amount;

Монеты (cents) + currency. Если payment.currency ≠ invoice.currency —
сложение бессмысленно. Где проверка? В домене или в Payment?

Если домен:
  if ($payment->currency !== $this->currency) {
      throw new CurrencyMismatchException(...);
  }
```

**5.**
```
[NIT] @L11
$data = json_decode($request->getContent(), true) ?? [];

json_decode возвращает mixed. Лучше через Symfony:
  $data = $request->getRequestPayload()->all();
он сам парсит JSON и бросает 415, если content-type не JSON.
```

**6.**
```
[FOLLOWUP] @L19
нет проверки, что token не пустой. paymentService сам проверит?
Если да — ок. Если нет — добавить валидацию.
Записать в таск #XXX.
```

**7.**
```
[PRAISE] @L34 (Invoice::applyPayment)
Хорошо, что статус меняется через домен, а не из service.
И инвариант totalPaid ≤ total — это правильное место. 👍
```

#### Итог ревью

```
2 BLOCKER / 2 MAJOR / 1 MINOR / 1 NIT / 1 FOLLOWUP / 1 PRAISE
```

Автор видит: **нужно исправить 4 вещи, остальное — не критично.** Ясно.

---

### 4. Как я пишу комментарии: формулировки

#### Хорошо

```
[QUESTION] Не уверен, что тут нужен ?? 0. 
Если поле отсутствует — лучше 422 с понятным сообщением, 
чем молча 0. Что было намерено?
```

```
[BLOCKER] Здесь double-spend: два параллельных запроса 
пройдут проверку balance >= X и оба спишут. 
Предлагаю UPDATE ... WHERE balance >= X (см. мой комментарий выше).
```

```
[MAJOR] Этот метод делает 3 вещи: валидация, расчёт, сохранение. 
Разбить на 3 — будет проще тестировать и читать. 
Не блокирует merge, но стоит сделать.
```

#### Плохо

```
«Неправильно.»
```

```
«Здесь баг, переделай.»
```

```
«А ты уверен, что так надо?»
```

```
«Вот как надо: [50 строк кода]»
```

#### Правила формулировок

| Правило | Пример |
|---------|--------|
| Конкретика, не общие слова | Не «мало тестов», а «нет теста на `null` в `getLines()`» |
| Почему, а не что | Не «переименуй», а «`data` не говорит, что это заказ» |
| Предложить решение (если есть) | Не «тут ошибка», а «попробуй `?? null` + явный null-check» |
| Вопрос вместо утверждения (если не уверен) | «Что если X? Я не уверен» |
| Без «ты» | Не «ты забыл», а «здесь не обработан» |

---

### 5. Разногласие с автором

#### Ситуация

Я: «[MAJOR] Этот класс нарушает SRP, надо разбить.»
Автор: «Нет, это один класс, потому что они всегда вместе. Разбивать — избыточно.»

#### Что делаю

**1. Уточняю позицию.**

Не упрямлюсь. Спрашиваю:
> «Понял. Уточни: что именно будет меняться независимо? Если `A` меняется при смене провайдера, а `B` — при смене формата отчёта — это 2 причины. Если обе меняются всегда вместе — ок, оставим.»

**2. Приношу аргументы, не мнение.**

Не «мне кажется», а:
> «В прошлый quarter мы меняли формат отчёта 3 раза, и каждый раз трогали этот класс. Если это продолжится — SRP поможет. Если нет — ок.»

**3. Предлагаю компромисс.**

> «Можно оставить как есть, но добавить комментарий `// SRP: A и B связаны, т.к. ...`. Если через месяц разделим — легко. Так ок?»

**4. Уступаю, если автор прав.**

> «Ты прав, я не учёл X. Отзывается комментарий. LGTM.»

**5. Эскалация (если не договорились).**

Не «пусть тимлид решит». А:
> «Давай 10 минут после стандапа — обсудим. Если не сойдёмся — тимлид, но до того — давайте найдём решение сами.»

#### Чего НЕ делаю

| ❌ | Почему |
|----|--------|
| Упрямясь «я прав, ты нет» | Ревью — не соревнование |
| «А давай сделаем как я» без аргумента | Мнение ≠ решение |
| Промолчу, но не аппрувлю | Не конструктивно |
| Аппрувлю «чтобы не спорить» | Худшее, что можно сделать |
| Увожу в Slack | Теряется контекст |
| Повторяю один и тот же комментарий 3 раза | Автор устал, начинаю давить |

#### Правило: автор знает свой контекст лучше

Автор видит, чего я не вижу:
- Ограничение времени
- Технический долг, который будут чинить позже
- Контекст команды (кто что знает)
- Приоритет фичи

Моя задача — **поднять вопрос**, а не **решить за автора**.

---

### 6. Антипаттерны ревью

#### От ревьюера

| ❌ | Почему плохо | ✅ |
|----|-------------|-----|
| 30 NIT'ов в PR | Автор тонет, BLOCKER'ы теряются | Максимум 5 NIT'ов, остальное — linter |
| «LGTM» не читая | Формальность | Прочитать или не аппрувить |
| Реверс через 2 дня после merge | «Я тогда не заметил» | Записать в таск, не реверсить |
| Ревью через 3 дня | Контекст потерян | SLA 4 часа |
| Перевод в Slack | Теряется тред | В PR |
| «Мне не нравится» | Не аргумент | Конкретика + почему |
| Ревью style-мелочей | Linter решает | Только смысловые |

#### От автора

| ❌ | Почему плохо | ✅ |
|----|-------------|-----|
| «Ок, сделаю» на всё | Не всё надо делать | Разделить: blocker / nit / followup |
| Молчание | Ревьюер не знает, что дальше | Отвечать на каждый комментарий |
| «Потом переделаю» без таска | Забудется | Создать таск |
| Force-push без предупреждения | Теряется контекст ревью | Уведомить в PR |
| Merge с 1 BLOCKER'ом «я поправлю» | Редко поправляют | Исправить до merge |

---

### 7. Метрики, за которыми слежу

| Метрика | Цель |
|---------|------|
| Время от PR до первого ревью | < 4 часа |
| Время от ревью до merge | < 1 день |
| % PR'ов с BLOCKER'ами при первом ревью | Понимать, где слабые места |
| % повторных BLOCKER'ов по тому же типу | Обучающий эффект |
| Средний размер PR | < 400 строк |
| % PR'ов, mergенных за 1 день | Быстрый поток |
| Кол-во NIT'ов на PR | < 5 (иначе — linter) |

---

### 8. Чеклист ревью

```
□ Прочитал Description + Changes
□ Понимаю «зачем»
□ Проверил Tier 1 (correctness): краевые, ошибки, безопасность
□ Проверил Tier 2 (architecture): границы, SRP, зависимости
□ Проверил Tier 3 (maintainability): читаемость, тесты
□ Отметил BLOCKER / MAJOR / MINOR / NIT / PRAISE
□ Отвечаю на вопросы автора
□ Если BLOCKER — объяснил почему + предложил решение
□ Если MAJOR — предложил альтернативу
□ Если не согласен — аргумент, не мнение
□ Аппрув = я готов mergить
```

---

### Итог

Хороший ревью — это:
1. **Приоритизация** (BLOCKER > MAJOR > MINOR > NIT).
2. **Конкретика** (что, где, почему, как).
3. **Уважение к автору** (вопрос, не приказ).
4. **Компромисс** (если автор прав — уступить).
5. **Скорость** (не держать PR неделями).

Ревью — не проверка качества кода. Качество кода — результат культуры команды. Ревью — один из её инструментов.

> ⏱ 53.6s · prompt 99915 tok · output 4074 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня (как бэкенд-разработчика на собеседовании) продемонстрировать навык: превращение сырой бизнес-жалобы в чёткую задачу для исполнителя. Нужно показать:

1. Как я разлагаю «не могут найти заказы» в конкретные технические гипотезы.
2. Как я формулирую задачу так, чтобы её можно было выполнить без уточнений — т.е. с определением success criteria, границами, acceptance criteria, scope.
3. Что обязательно включу: контекст, цель (business outcome), гипотезы, scope in/out, acceptance criteria, метрики, ограничения, критерии готовности, что НЕ входит, как мерить результат.
4. Почему каждый элемент — чтобы читатель понял логику.

Формулирую как живой документ задачи (как если бы я её написал в Linear/Jira), с объяснением «почему это включено» по каждому блоку. Отвечать на русском, структурированно, без воды. Не делать слишком длинно — но достаточно подробно, чтобы было видно уровень. Использую markdown.

</details>

**Кандидат:**

## От жалоб к задаче

### Что я сначала делаю до формулировки

Сырая жалоба «не могут найти заказы» — это **симптом**, не задача. Прежде чем писать тикет, я задаю себе три вопроса, ответы на которые уже вставляю в задачу:

1. **Что именно «не могут найти»?** — не работают поисковая строка? нет фильтра по дате? заказы пропадают после смены статуса? выдача пустая?
2. **У кого и когда?** — все пользователи или сегмент? когда начали? что меняли перед этим?
3. **Что они делают, чтобы найти?** — через какой UI/API? какие запросы?

Если на это нет данных — задача начинается с **диагностики**, а не с «починить поиск». Это принципиально: писать «сделать поиск заказов» без данных — значит строить решение вслепую.

---

### Формулировка задачи

```
Тикет: Поиск исторических заказов — деградация

Тип:      Bug investigation + fix (не feature)
Приоритет: P1 (поддержка завалена, влияние на вырутку)
Целевой срок: диагностика — 1 день, fix — 2 дня, регресс — 1 день

─────────────────────────────────────────────
ЦЕЛЬ (business outcome)
─────────────────────────────────────────────
Восстановить возможность для зарегистрированных
пользователей находить собственные заказы за
последние 12 месяцев по:
  • номеру заказа,
  • диапазону дат,
  • статусу,
  • названию товара (поиск по строке).

Измеримый результат:
  • >95% пользователей находят свой заказ
    ≤ 3 попыток поиска (метрика из product analytics).
  • Кол-во тикетов поддержки «не найден заказ»
    упадёт с ~120/нед до <20/нед в течение 2 недель.

─────────────────────────────────────────────
КОНТЕКСТ (что уже известно)
─────────────────────────────────────────────
• Жалобы начались ~10 числа (со слов поддержки).
• В конце прошлой недели деплоились:
    - миграция: перенос orders → orders_archive (архивация)
    - смена индекса по email на composite (email, created_at)
    - апгрейд Symfony 6.4 → 7.1
• Алертов по 5xx не было. HTTP 200, но выдача пустая/неполная.
• Сегмент: в основном заказы старше 30 дней. Свежие находятся.

ВЫВОД: гипотеза №1 — архивация. Гипотеза №2 — индекс.
Гипотеза №3 — регрессия в DQL после апгрейда.

─────────────────────────────────────────────
ГОРИЗОНТ (scope)
─────────────────────────────────────────────
ВХОДИТ:
  • Диагностика корневой причины (1 из 3 гипотез + свои).
  • Fix на уровне: БД / репозиторий / DQL / миграция.
  • Ретроспективные данные: если часть заказов
    «потеряна» — вернуть (backfill), не просто починить.
  • Регрессионные тесты: integration (реальная БД)
    + 1 feature (HTTP → поиск → ответ).
  • Метрика «нашёл с N попыток» в аналитику.

НЕ ВХОДИТ (осознанно):
  • Новый UX поиска (facet, autocomplete) — отдельная фича.
  • Поисковая система (Meilisearch/ES) — только если
    диагностика покажет, что текущий SQL не масштабируется.
    В этом случае — отдельный тикет, не в этом fix.
  • Ревью всей модели заказов.
  • Изменения API-контракта (пока не доказано, что сломан).

─────────────────────────────────────────────
КРИТЕРИИ ГОТОВНОСТИ (Definition of Done)
─────────────────────────────────────────────
Функционально:
  [ ] Пользователь находит заказ по номеру.
  [ ] По диапазону дат (30/90/365 дней) — все находит.
  [ ] По статусу — корректная выдача.
  [ ] По названию товара — частичное совпадение работает.
  [ ] Заказ, перемещённый в archive, находитcя
        (если архив — причина).
  [ ] Чужие заказы не видны (authz: WHERE user_id = :me).

Качество:
  [ ] EXPLAIN ANALYZE основного запроса:
        – нет Seq Scan на orders > 100k строк,
        – plan использует индекс,
        – p95 < 200ms на проде (проверить на staging
          с реальным объёмом).
  [ ] Integration-тесты: 5 сценариев из acceptance.
  [ ] Feature-тест: 1 (HTTP-контракт).
  [ ] ArchPHP: не нарушены границы слоёв.
  [ ] Нет N+1 (проверить через SQL-лог в feature-тесте).

Данные:
  [ ] Если был backfill — количество восстановленных
        заказов зафиксировано в описании PR.
  [ ] Если миграция откатывалась — чек-лист отката.

Наблюдаемость:
  [ ] Лог запроса поиска (query, user_id, duration,
        result_count) — для диагностики в будущем.
  [ ] Метрика «нашёл с N попыток» подключена.

─────────────────────────────────────────────
ПОШАГОВЫЙ ПЛАН (для исполнителя)
─────────────────────────────────────────────
1. Воспроизвести локально:
   – seed: 50k заказов, 10k в archive, разброс по 12 мес.
   – запрос «заказ 8 месяцев назад» → увидеть пустую выдачу.

2. Проверить гипотезы (по порядку):
   H1 (архив):
     SELECT COUNT(*) FROM orders WHERE user_id = :me;
     SELECT COUNT(*) FROM orders_archive WHERE user_id = :me;
     → если заказы в archive — причина найдена.

   H2 (индекс):
     EXPLAIN ANALYZE <запрос поиска>;
     → Seq Scan? индекс (email, created_at) не покрывает
       запрос по status / product name?

   H3 (DQL после апгрейда):
     git log -p --since="2 weeks ago" -- src/Infrastructure/Doctrine/
     → сравнить DQL до/после.

3. Fix:
   – H1: UNION в репозитории + index на archive,
     ИЛИ unified view, ИЛИ backfill в orders.
   – H2: covering index / переписать запрос.
   – H3: откат DQL или фикс.

4. Тесты (интеграционные, реальная БД):
   – находит свежий заказ,
   – находит старый (6 мес.),
   – находит в archive (если H1),
   – не находит чужой,
   – пустой результат — 200 с [], не 500.

5. Метрика + лог.

6. PR ≤ 400 строк. Если fix > 400 — разбить:
   – PR1: диагностика + backfill,
   – PR2: fix + тесты,
   – PR3: метрика.

─────────────────────────────────────────────
РИСКИ И ЧТО ДЕЛАТЬ
─────────────────────────────────────────────
• Если архивация намеренная (политика хранения 90 дней)
  → фикс не в БД, а в UI: показать «заказ старше 90 дней
    доступен через поддержку». Это другой scope —
    ЭСКАЛИРОВАТЬ в продукт до начала кода.

• Если backfill на 5M строк — не делать в проде напрямую.
  → Batch-джоб с лимитом, мониторинг load, off-peak.

• Если EXPLAIN покажет, что SQL не масштабируется
  на текущем объёме → не тянуть Meilisearch в этот тикет.
  Отдельный RFC.
```

---

### Почему в формулировке есть именно это

| Блок | Почему |
|------|--------|
| **Business outcome, а не «починить»** | Исполнитель понимает, зачем. Иначе починит «поиск», но не тот, который жалуется пользователь |
| **Измеримый результат** | Без него невозможно сказать «сделано». «Починил» ≠ «>95% находят ≤3 попыток» |
| **Что уже известно + даты деплоев** | Исполнитель не тратит день на то, что я уже собрал. Экономит его время |
| **Гипотезы в порядке вероятности** | Не «разберитесь». А «проверьте H1, H2, H3». Ускоряет диагностику в разы |
| **Scope IN / OUT** | Главный раздел. Без OUT задача разрастается: «пока я чинил, добавил autocomplete, facet, вынес в Meilisearch». Через 2 недели вместо P1-бага — 3-недельная фича |
| **Definition of Done с EXPLAIN** | «Починил» без плана запроса — не починил. Через месяц снова сломается. EXPLAIN + p95 — объективный критерий |
| **Authz в acceptance** | Классическая ошибка в «поиске»: забыли `WHERE user_id`. Пользователь A видит заказы B. В список «что проверяем» — обязательно |
| **Backfill отдельно** | Починить поиск ≠ восстановить данные. Если часть заказов «потеряна» — это отдельная работа с риском |
| **Пошаговый план** | Не «сделайте», а «сделайте в таком порядке». Исполнитель не блуждает |
| **Риски + эскалация** | «Если архивация намеренная — стоп, это продукт». Исполнитель не тратит 2 дня на fix, который не нужен |
| **Разбить PR если > 400 строк** | Качество ревью. Иначе один большой PR с backfill + fix + метрикой — никто не проверит |

---

### Чего намеренно НЕТ

| Нет | Почему |
|-----|--------|
| Конкретного кода | Я формулирую **что** и **критерии**, не **как**. Исполнитель — исполнитель. У него может быть лучшее решение |
| Дедлайна «к пятнице» | Дедлайн = качество пострадает. Даю срок в днях, не в календарных днях |
| Списка «кто ревьюит» | Организационный вопрос, не задача |
| Прикрепления логов/скриншотов | Они должны быть в тикете, но в формулировке — ссылка, не 200 КБ текста |
| «Пожалуйста» и «спасибо» | Тикет — документ, не письмо |

---

### Главный принцип

> **Задача без acceptance criteria — не задача. Это просьба.**

Формулировка выше — не «сделайте поиск», а:
- вот что сломалось (контекст),
- вот что я считаю причиной (гипотезы),
- вот как я пойму, что починили (DoD),
- вот чего НЕ делать (scope OUT),
- вот в каком порядке (план),
- вот где я ошибаюсь (риски).

Исполнителю не нужно ничего уточнять. Всё, что он может спросить — в тикете. Если он задаёт вопрос, которого нет в тикете — значит, тикет не дописан. Это мой индикатор качества.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

## Вопросы заказчику до начала работы

### Принцип

Я задаю вопросы не «собрать требования», а для трёх целей:

1. **Сузить scope** — чтобы не делать то, что не нужно.
2. **Найти критерий готовности** — чтобы «сделано» было проверяемо.
3. **Найти места, где я могу ошибиться** — и зафиксировать допуск до кода.

Если после вопросов я не могу переписать тикет без догадок — вопросы были не те.

---

### Блок 1. Масштаб и боль (чтобы понять приоритет)

**Зачем:** «поддержка завалена» — это ощущение. Мне нужны цифры, чтобы понять, P1 или P2, и сколько времени на fix допустимо.

- Сколько тикетов «не найден заказ» в неделю/день? Тренд — растёт или стабилен?
- Какой % этих тикетов — по заказам **старше 30 дней**, а какие — свежие?
- Есть ли оценка потерянной выручки? (Пользователь не нашёл заказ → не смог вернуть/отменить → ушёл к конкуренту / потерял гарантию)
- Кого это задевает сильнее: B2C или B2B? Один сегмент или все?

**Как ответ меняет задачу:**
- Если 80% — заказы старше 90 дней → почти наверняка архивация, и фикс = политика хранения, а не «баг».
- Если боль растёт с каждым днём → P1, диагностика сегодня, не «в спринте».

---

### Блок 2. Что именно «не находят» (сценарии)

**Зачем:** «не могут найти» — это 5 разных багов. Я должен понять, какой.

- Пользователь ищет **по номеру** и не находит? Или **по дате**? Или **через список** («Мои заказы») и там пусто?
- Они видят **пустой список** или **неполный** (часть заказов есть, часть нет)?
- Они пробуют **несколько раз** с разными словами? (И тогда «нашёл с N попыток» — правильная метрика)
- Есть ли **конкретные примеры**: email + номер заказа, который «потерялся»? 3–5 штук хватит для диагностики.
- Находят ли они заказ, если поддержка **вручную** его находит? (Значит, данные есть, а поиск/фильтр сломан. Или данные реально потеряны)

**Как ответ меняет задачу:**
- «Свежие находит, старые нет» → архив или индекс по дате.
- «Пусто у всех» → authz или ошибка в DQL.
- «Неполный список» → лимит/пагинация/ордер-инг.

---

### Блок 3. Границы данных и политика (самый важный блок)

**Зачем:** здесь я могу **неосознанно нарушить** бизнес-правило, если не спросить.

- Какой **срок хранения** заказов официально? 30 / 90 / 365 дней / бессрочно?
- Архивация (если была) — **намеренная** политика или техническое решение?
- Если намеренная — **кто её завёл** и **на каком основании**? (GDPR, учёт, «так решили»)
- Могут ли пользователи **требовать** заказ старше срока хранения? (например, для гарантии/налога)
- Есть ли **разные правила** по странам/сегментам? (B2B — 7 лет, B2C — 90 дней)
- Если данные в архиве — **можно ли** их показывать в UI, или только через поддержку?

**Как ответ меняет задачу:**
- «Архив намеренный, 90 дней» → fix = **UI** («заказ старше 90 дней — через поддержку»), а не БД. Другой scope, другой владелец (продукт, не бэкенд).
- «Архив технический, по ошибке» → fix = **БД/репозиторий + backfill**.
- «По странам разные» → fix = **условная логика**, и я должен знать правила.

> Это тот вопрос, который **меняет владельца задачи**. Если архив намеренный — тикет уходит в продукт, и мне надо **не чинить**, а **дизайнить UX**.

---

### Блок 4. Приоритет и дедлайн

**Зачем:** без этого я не могу решить, что делать **сначала**.

- Что важнее: **вернуть данные** (backfill) или **починить поиск** (fix)? Или **UX** («заказ через поддержку»)?
- Есть ли **жёсткий дедлайн** (отчёт, сезон, аудит)? Или «как можно скорее»?
- Если P1 — **кто принимает** результат? Тимлид, продукт, владелец поддержки?
- Можно ли **откатить** миграцию/архивацию, пока идёт диагностика? (Иногда это быстрее, чем фикс)

**Как ответ меняет задачу:**
- «Сначала верните данные» → backfill первым, фикс вторым. Изменяет порядок в плане.
- «Можно откатить» → возможно, откат = решение за 1 час, а не за 2 дня.
- «Жёсткий дедлайн в пятницу» → я говорю честно: диагностика до среды, fix до четверга, регресс в пятницу. Без обещаний «до вторника».

---

### Блок 5. Допуски и ограничения

**Зачем:** чтобы не сломать то, что работает, и не обещать невозможного.

- **API-контракт** поиска менять нельзя? (Есть ли внешние клиенты / мобильное / партнёры?)
- Можно ли **стопать** архивацию / бэкап-джобы на время backfill?
- Есть ли **бюджет** на поиск (Meilisearch/ES)? Или только SQL? (Иначе я могу «починить» через SQL, а через месяц будет не масштабируемо)
- Можно ли **писать в прод-БД** (backfill), или только через staging → prod?
- Кто даёт **права** на прод-логи, аналитику, БД? (Без этого диагностика = 0)

**Как ответ меняет задачу:**
- «API менять нельзя» → fix только на стороне БД/репозитория, не на контроллере.
- «Бюджета на ES нет» → я не предлагаю Meilisearch в этом тикете.
- «Права дадут через 2 дня» → диагностика сдвигается, и я **фиксирую это** в тикете, а не молчу.

---

### Блок 6. Конкурентные инициативы (чтобы не дублировать)

**Зачем:** очень часто «починить поиск» уже делают, или собираются делать через новую систему.

- Есть ли **уже** задача на новый поиск (facets, autocomplete, Meilisearch)?
- Кто **владеет** UX поиска — этот тикет или другой?
- Если другой — как **связать**? (Мой фикс = временный, их фича = постоянный)
- Не собираются ли **переезжать** с текущей БД? (Тогда мой фикс в Doctrine — мусор)

**Как ответ меняет задачу:**
- «Через 2 недели будет Meilisearch» → мой fix = **временный**, и я **не трачу время** на идеальное покрытие индексами. Делаю минимально.
- «Поиск чиним в рамках фичи X» → этот тикет **закрывается** как дубль, а не выполняется параллельно.

---

### Блок 7. Ответственность за результат

**Зачем:** чтобы «сделано» было **чьим-то** «принято».

- Кто **измеряет** метрику «нашёл с N попыток»? Продукт, аналитика, поддержка?
- Кто **принимает** backfill — что «данные вернулись»? (Проверка по выборке? По полному сканированию?)
- Если fix не сработал и тикеты продолжают приходить — **кто** эскалирует обратно ко мне?

**Как ответ меняет задачу:**
- Без назначенного «принимающего» я могу «починить», а через неделю окажется, что 20% случаев всё ещё не находит. И виноват буду я.
- С назначенным — у меня есть **контракт**: я сделал X, он подтвердил Y.

---

### Сводная таблица: ответ → последствие

| Ответ заказчика | Что меняется в тикете |
|-----------------|----------------------|
| «Архив намеренный, 90 дней» | Scope меняется: fix = UX, не БД. Владелец — продукт |
| «80% жалоб — заказы > 90 дней» | Приоритет = политика хранения, не «баг поиска» |
| «Свежие находит, старые нет» | Гипотеза H1 (архив) становится основной |
| «Можно откатить миграцию» | Добавляется опция «откат за 1 час» в план |
| «API менять нельзя» | Fix только на уровне БД/репозитория |
| «Через 2 недели будет Meilisearch» | Fix = временный, минимальный, не идеал |
| «Бюджета на ES нет» | Убираю из scope «может, поиск вынести» |
| «Жёсткий дедлайн в пятницу» | Фиксирую честный план: диагностика до среды |
| «Метрику измеряет аналитика» | В DoD — «подключил метрику», не «я её меряю» |
| «Права на логи дадут через 2 дня» | Сдвигаю старт диагностики, фиксирую в тикете |

---

### Главный принцип

> **Вопрос заказчику — это не «расскажите, что вы хотите». Это «покажите мне, где я могу ошибиться в интерпретации».**

Каждый вопрос из блоков выше имеет **ответ, который меняет задачу**. Если ответ вопроса ничего не меняет — этот вопрос не нужен. Я задаю только те, где **моя интерпретация ≠ ваша** возможна.

И наоборот: если после всех ответов я **не могу** переписать тикет — значит, я задал неправильные вопросы, и это **моя** ошибка, не заказчика.

> ⏱ 42.7s · prompt 103867 tok · output 3022 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос на собеседовании: я выполняю задачу с чёткими критериями приёмки, и по ходу работы возникает две ситуации: (а) нашёл попутный баг, не связанный с задачей; (б) понял, что один из критериев приёмки противоречит цели задачи. Что делаю в каждом случае и почему.

Это вопрос на проверку зрелости инженера: как я веду себя при столкновении с неопределённостью, как управляю scope, как коммуницирую, как принимаю решения. Это не про «что технически исправить», а про **поведение и суждение**.

Давай подумаю, что здесь важно показать:

Ситуация (а) — попутный баг, не связанный с задачей:
- Главный риск: scope creep. Я начинаю чинить не своё, PR разрастается, ревью усложняется, дедлайн сдвигается.
- Но другой риск: промолчать, а баг критичный — и я «знал, что там баг, а молчал».
- Правильно: **зафиксировать, не чинить (как правило), решить по приоритету**. Ключ — критичность. Если баг тривиальный и я уже в этом месте кода — можно поправить, но это осознанное решение, а не дефолт. Если сложный — отдельный тикет. Если критичный (безопасность, потеря денег) — эскалация, даже если не в scope.
- Почему не чиню молча: потому что (1) scope creep, (2) нет acceptance criteria для этого бага, (3) ревьюер не ожидает, (4) если он связан с тем же кодом — я могу его **ввести** своим fix'ом и не заметить.
- Почему не молчу: ответственность.

Ситуация (б) — критерий приёмки противоречит цели задачи:
- Это важнее. Здесь я столкнулся с тем, что **задача сама в себе противоречива**. Исполнитель не должен «выполнить противоречивое». Он должен **остановиться и поднять вопрос**.
- Риск: я могу «довести до критерия», но это сломает цель. Или «доведу до цели», но проигнорирую критерий — и меня за это накажут на приёмке.
- Правильно: **не принимать решение молча**. Фиксирую противоречие, показываю оба варианта с последствиями, прошу принять решение. Я не решаю за заказчика/тимлида, но я **обязан** показать, что вижу противоречие.
- Почему: потому что я не знаю контекста, который есть у владельца задачи. Возможно, критерий был поставлен намеренно (например, компромисс по срокам). Возможно, это ошибка в формулировке. Только владелец может решить.
- Но я должен прийти **не с вопросом «что делать?»**, а с **взвешенным** вопросом: «видю противоречие X, вот варианты A и B с последствиями, вот что я рекомендую и почему, что выбираем?»

Оба случая объединяет принцип: **не принимать решение за того, у кого больше контекста, но и не молчать**. Разница: в (а) вопрос — scope (я обычно сам решаю + фиксирую), в (б) вопрос — сам смысл задачи (решает владелец).

Также стоит показать: что я НЕ делаю. Это важно — антипаттерны. Молчу. Чиню всё подряд. «Решаю» противоречие сам. Иду в крайности.

Структурирую ответ:
1. Общий принцип (коротко).
2. Ситуация (а) — что делаю, как решаю по критичности, почему не молчу и почему не чиню молча.
3. Ситуация (б) — что делаю, почему не решаю сам, как формулирую вопрос.
4. Сводка / что объединяет.

Язык — русский, как и вся беседа. Формат — Markdown, таблицы, пример кода/формулировок где уместно. Без воды. Это собеседование, отвечать нужно как зрелый бэкенд-инженер, конкретно и с аргументацией.

Проверю длину: не перегружать. Два раздела, по каждому — конкретика + таблица решений + почему. Плюс короткий общий принцип в начале и связка в конце. Это оптимально.

</details>

**Кандидат:**

## Оба случая — про одно: не решать за того, у кого больше контекста, и не молчать

Разница в том, **кто** должен принять решение. В (а) это обычно я + фиксация. В (б) это владелец задачи — я лишь показываю противоречие и варианты. Ниже по каждому.

---

### (а) Нашёл попутный баг, не связанный с задачей

**Дефолт: не чиню в этом PR. Но не молчу.**

Чинить «по пути» — самый частый источник scope creep и скрытых регрессий. Причины, по которым я **не** втравливаю чужой баг в свой fix:

1. **Нет acceptance criteria** для него. Я не знаю, что «починено» и как проверить.
2. **Он в соседнем коде.** Мой fix может **ввести** ещё один баг рядом — и я его не увижу, потому что не пишу на это тест.
3. **Ревью усложняется.** Ревьюер ожидает N строк, видит N+50, и качество ревью падает.
4. **Дедлайн сдвигается.** Без моего ведома.

Но **промолчать** — тоже неправильно. Если баг критичный (безопасность, потеря денег, утечка), а я «знал и прошёл мимо» — это моя ответственность.

#### Как решаю: по критичности и близости к моему коду

| Критичность | Близость к моему коду | Что делаю |
|-------------|----------------------|-----------|
| Критичный (безопасность, деньги) | любой | **Останавливаюсь, эскалирую.** Даже если не в scope. Не могу идти дальше поверх дыры |
| Серьёзный, виден пользователю | любой | **Отдельный тикет** с репродукцией + гипотезой. Не чиню в этом PR |
| Средний | **тот же файл/класс, я уже там** | Могу поправить, **но**: осознанно, с комментарием в PR, с тестом. И говорю владельцу |
| Мелкий / косметика | любой | **Тикет-записка** (`FOLLOWUP`). Не трогаю |

#### Конкретно

Нашёл баг в том же `Invoice::applyPayment`, где пишу свой fix. Он тривиальный (нет null-check), я уже в этом методе, и я **знаю**, что он сработает на реальном сценарии.

**Решение:** чиню, но так:

```
PR: feat(invoice): add partial payment

В description:
  • Попутно: добавил null-check в applyPayment (баг #1234,
    найден по пути). Отдельный тикет создан. Если не хотите
    в этом PR — откатываю, это 2 строки.
```

**Почему так:**
- Я **не скрываю** — ревьюер и владелец видят.
- Я **даю опцию** — владелец решает, в этот PR или нет.
- Я **зафиксировал** баг в тикете — он не потеряется.
- Я **подготовил откат** — это 2 строки, а не перепил.

#### Антипаттерн

```
❌ Нахожу 4 бага по пути, чиню все молча.
   PR вырастает с 120 до 400 строк. Ревьюер не понимает,
   что тут лишнее. Через неделю один из «починенных» багов
   возвращается — потому что я не покрыл его тестом,
   я не был в том контексте.
```

---

### (б) Критерий приёмки противоречит цели задачи

**Дефолт: не решаю сам. Останавливаюсь, показываю противоречие, прошу решения.**

Это важнее случая (а). Здесь задача **сама в себе противоречива**, и я не могу «выполнить противоречивое». У меня два пути, и оба плохие:

- Выполню **критерий** — но сломаю **цель**. (например: «p95 < 200ms» против «показать все заказы за 12 месяцев» — на текущем индексе несовместимо)
- Выполню **цель** — но проигнорирую **критерий**. (меня не примут на приёмке)

**Правильно: ни то ни другое не выбирать молча.**

#### Почему я не решаю сам

1. **Я не знаю контекста владельца.** Критерий мог быть поставлен намеренно — как компромисс по срокам, или потому что «на 90% случаев этого хватает».
2. **Возможно, противоречие — в формулировке.** Не в реальности, а в тикете. Только владелец отличит.
3. **Решение меняет scope, бюджет, сроки.** Это не уровень «инженер решает».

#### Как я формулирую (не «что делать?», а «я вижу X, вот варианты, что я рекомендую»)

Плохо:
```
«Коллеги, у нас противоречие. Что делать?»
```

Хорошо:
```
Вижу противоречие в тикете:

  Цель:       >95% пользователей находят заказ ≤ 3 попыток.
  Критерий:   p95 поиска < 200ms.

На текущем индексе и объёме (orders ~2M, archive ~800k)
эти два требования несовместимы:
  • Найти 12 месяцев по product_name = Seq Scan ≈ 900ms.
  • Чтобы уложиться в 200ms — нужен covering index или
    вынос поиска в Meilisearch.

Варианты:
  A) Отказаться от поиска по product_name в этом tикете.
     → Цель 95% достижима (87% жалоб — по номеру/дате).
     → p95 < 200ms. Скроуп.
  B) Держать все критерии, вынести поиск в Meilisearch.
     → +1.5 недели, нужен бюджет. Цель и p95 ок.
  C) Приоритизировать p95 < 400ms (компромисс).
     → Цель 95% ок, p95 не 200, а 400ms.

Рекомендую A: минимальный scope, закрывает 87% боли.
Product_name — в отдельный тикет с Meilisearch, если
аналитика подтвердит, что это частый сценарий.

Что выбираем?
```

#### Почему именно так

| Элемент | Зачем |
|---------|-------|
| «Вижу противоречие» + обе формулировки из тикета | Я не предполагаю, я **цитирую**. Владелец видит, что я читал |
| Цифры (2M, 800k, 900ms) | Не «мне кажется медленный», а измеримо |
| 3 варианта с последствиями | Я не даю вопрос «что?», я даю **выбор** |
| Рекомендация + причина | Я не «мне всё равно». У меня есть позиция |
| «Что выбираем?» | Решение — у владельца, не у меня |

#### Антипаттерн

```
❌ Тихо выбираю вариант B (Meilisearch), потому что
   «всё равно надо», и не говорю. Через 2 недели владелец
   узнаёт, что я потратил неделю на инфраструктуру,
   которая не была в budget. Или наоборот — я делаю A
   (откатываю product_name) молча, и на приёмке говорят:
   «а как же поиск по товару из acceptance?».
   Оба варианта — потеря доверия.
```

---

### Что объединяет оба случая

```
Принцип: не решать за того, у кого больше контекста.
         Но и не молчать.
```

| | (а) чужой баг | (б) противоречие в задаче |
|---|---|---|
| **Кто решает** | Обычно я + фиксация (если не критично) | Владелец задачи |
| **Мой выход** | Тикет / фикс / FOLLOWUP | Варианты + рекомендация + вопрос |
| **Главный риск** | Scope creep | Сломать цель или не пройти приёмку |
| **Что я НЕ делаю** | Не чиню молча, не раздуваю PR | Не выбираю молча, не «решаю» сам |
| **Красная линия** | Критичный баг = эскалация, даже вне scope | Противоречие = стоп, не продолжаю код |

---

### Общий индикатор

Если я **не могу** переписать тикет или PR без догадки — значит, я должен **остановиться и спросить**, а не «сделать как надо». Это не слабость и не «я не могу». Это то, что отличает инженера, который **понимает задачу**, от того, кто **выполняет инструкции**.

В (а) я почти всегда могу сам (с фиксацией). В (б) я почти всегда **не должен** — и это нормальное, здоровое состояние, а не блок.

> ⏱ 44.5s · prompt 109216 tok · output 3234 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про отчёт о выполнении задачи — из чего он состоит, и как меняется, если часть не сделана. Это продолжение длинного интервью на PHP-бэкенд.

Я должен ответить в том же стиле: конкретно, с примерами, без воды, с чёткой структурой. Тема — «отчёт о выполнении» (closure report / completion summary) и его вариация, когда задача выполнена частично.

Что важно:
1. Отчёт — это не «сделал». Это доказательство, что цель достигнута, + честное описание границ.
2. Структура отчёта: что сделано (относительно цели, не кода), как проверено (по acceptance criteria), что не сделано / ограничения, риски, что дальше.
3. Ключевой сдвиг, когда часть не сделана: отчёт становится честным. Не «сделал 90%» (эвфемизм), а «сделал X, не сделал Y, вот почему, вот что с этим делать».

Нужно:
- Дать конкретную структуру отчёта (блок/бук).
- Показать разницу между «сделал» и «доказал, что сделал».
- Показать, что меняется при частичном выполнении — и почему это **хорошо**, а не плохо.
- Антипаттерны: «всё ок», скрытые ограничения, «почти готово».
- Привязка к предыдущим темам (acceptance criteria, scope, эскалация).

Оформление: markdown, таблицы, примеры. Не слишком длинно — тема конкретная. Но достаточно подробно, чтобы ответ был самостоятельным.

Составлю ответ:
1. Коротко: отчёт = доказательство, а не описание.
2. Структура успешного отчёта (6 блоков) с примером.
3. Что меняется при частичном выполнении (3 сдвига) с примером.
4. Антипаттерны.
5. Главный принцип.

Избегать: «спасибо за вопрос», повторения уже сказанного. Быть плотным.

</details>

**Кандидат:**

## Отчёт о выполнении

### Принцип

> **Отчёт — это не «я сделал». Это «я доказал, что цель достигнута, и вот где границы».**

Разница в том, что «сделал» — это описание моего действия. Отчёт — это **аргументация**: цель → что я сделал → как я это проверил → где границы → что дальше. Если любой из блоков пуст — отчёта нет.

---

### 1. Структура успешного отчёта

| Блок | Что отвечает |
|------|-------------|
| **Цель (из тикета)** | Зачем это делали — цитата, не пересказ |
| **Результат** | Что изменилось для пользователя / системы |
| **Проверка по acceptance criteria** | Пункт за пунктом: выполнено / нет |
| **Метрики** | Цифры до/после, если в DoD |
| **Ограничения и допущения** | Что НЕ покрывается, что я принял «на веру» |
| **Что дальше** | FOLLOWUP, tech debt, следующий тикет |

#### Пример

```
Задача: Поиск исторических заказов — деградация (P1)

ЦЕЛЬ (из тикета)
  >95% пользователей находят собственный заказ
  ≤ 3 попыток по номеру, дате, статусу, товару.
  Тикеты поддержки «не найден заказ» < 20/нед
  через 2 недели.

РЕЗУЛЬТАТ
  Корневая причина: архивация заказов старше 90 дней
  без учёта в поисковом запросе (гипотеза H1, подтверждена).
  Fix: UNION orders + orders_archive в репозитории,
  covering index на archive (user_id, created_at).
  Backfill: 412 заказов, «потерянных» при архивации,
  возвращено в основной поиск.

ПРОВЕРКА ПО ACCEPTANCE
  [x] Находит по номеру — integration-тест, 3 сценария.
  [x] Находит по дате (30/90/365) — integration-тест.
  [x] Находит по статусу — integration-тест.
  [x] Находит по названию товара — integration-тест.
  [x] Заказ в archive находится — integration-тест (новый).
  [x] Чужие заказы не видны — integration-тест.
  [x] P95 < 200ms — EXPLAIN на staging (2.1M строк):
        до: 840ms (Seq Scan), после: 180ms (Index Scan).
  [x] Лог запроса поиска подключён.
  [ ] Метрика «нашёл с N попыток» — подключена в аналитике,
      первые данные через 3 дня.

ОГРАНИЧЕНИЯ / ДОПУЩЕНИЯ
  • Поиск по товару — только префикс. Полнотекст
    (Meilisearch) — в отдельном тикете #4521.
  • Backfill выполнен на 3-х инстансах, off-peak.
    Не проверял на 10M строк — на staging было 2.1M.
    Если в проде > 5M — нужен batch-джоб, не один UPDATE.
  • Authz-тест покрывает только user_id. Если появится
    компания с несколькими пользователями — дописать.

ЧТО ДАЛЬШЕ
  • #4521 — Meilisearch (product_name, >100 символов).
  • #4522 — алерт, если p95 поиска > 300ms 10 мин подряд.
  • Через 2 недели: сравнить тикеты поддержки с baseline.
    Если не упало — диагностика, не фикс.

RISK
  Backfill на 412 строк — ок. Если обнаружим ещё «потерянные»
  (напр., > 10k) — не делать в проде напрямую. Отдельный план.
```

---

### 2. Что меняется, если часть не сделана

Три сдвига. Все три — **в пользу честности**.

#### Сдвиг 1. Разделяю «сделано» и «не сделано» — без эвфемизмов

Не «почти готово», «осталось немного», «90% сделано». Это слова, за которыми прячутся решения, которые я принял сам, а не владелец.

| Неделает | Я делаю |
|----------|---------|
| «Поиск по товару работает, почти» | «Поиск по товару **не** покрывает полнотекст. Сделан префикс. Полнотекст — в #4521» |
| «Метрика подключена» | «Метрика подключена, **но первые данные через 3 дня**. Сейчас приёмка по ней не может быть подтверждена» |
| «Backfill прошёл» | «Backfill прошёл на 412 строк. **На 10M не проверял.** Если в проде > 5M — отдельный план» |

**Почему:** владелец принимает решение на основе отчёта. Если я скрываю границы — он принимает **не то** решение. «Почти готово» звучит как «можно mergить». На деле — нет.

#### Сдвиг 2. Для «не сделано» я обязан дать причину + следующий шаг

Каждый пункт «не сделано» — не просто факт. У него есть:

```
НЕ СДЕЛАНО
  • Полнотекст по товару.
    Причина:  не в scope тикета (см. Scope OUT).
    Следующий шаг: тикет #4521, владелец — [имя].
    Риск, если не сделать: ~13% сценариев поиска (по аналитике).
```

Без причины и следующего шага «не сделано» — это **отмазка**. С ними — это **граница задачи**.

#### Сдвиг 3. Изменяю «приёмку»

Успешная приёмка = все acceptance criteria зелёные.
Частичная = часть зелёные, часть нет, и **приёмка = решение владельца**, не моя.

```
ПРИЁМКА (частичное выполнение)
  Сделано и подтверждено:
    • Находит по номеру, дате, статусу.
    • P95 < 200ms.
    • Authz (user_id).

  Сделано, но НЕ подтверждено:
    • Метрика «нашёл с N попыток» — подключена, данных пока нет.
      → Принимаете как «сделано» или ждёте 3 дня?

  НЕ сделано (осознанно):
    • Полнотекст — в #4521.
      → Принимаете задачу как закрытую или держите до #4521?

  РЕШЕНИЕ ПРИНИМАЕТ: [владелец]
```

Я не говорю «задача закрыта». Я говорю «вот что закрыто, вот что открыто, вот что вы решаете».

---

### 3. Антипаттерны отчёта

| ❌ | Почему плохо | ✅ |
|----|-------------|-----|
| «Всё готово, можно mergить» | Нет доказательств, нет границ | Цитата из DoD + [x]/[ ] |
| «Осталось чуть-чуть» | Скрывает решения | «Не сделано: X. Причина: Y. Следующий шаг: Z» |
| «Сделал 90%» | Эвфемизм, нет конкретики | Список: что сделано / что нет |
| Отчёт на 2 строки | Владелец не может принять решение | 6 блоков, каждый с ответом |
| Отчёт без цифр | «Быстрее» — не аргумент | «840ms → 180ms» |
| «Рисков нет» | Невозможно. Значит, не думал | Минимум 1 риск + сценарий |
| «Сделал по пути: 5 мелочей» | Скрытый scope creep | Отдельным блоком, с тикетами |
| Отчёт после merge | Уже поздно. Границы потеряны | До merge, как часть PR |

---

### 4. Разница в формулировках

| Ситуация | Плохо | Хорошо |
|----------|-------|--------|
| Всё сделал | «Готово, mergить» | «Все 7 пунктов DoD зелёные. Mergить. Через 3 дня проверю метрику» |
| 6/7 сделал, 1 не смог | «Почти готово, осталась мелочь» | «6/7 сделано. 1 пункт (полнотекст) — не в scope, в #4521. Решаете: закрыть задачу или держать до #4521?» |
| Сделал, но есть риск | «Готово, рисков нет» | «Готово. Риск: backfill на 412 строк — ок, на 10M не проверил. Если в проде > 5M — отдельный план» |
| Сделал + нашёл чужой баг | «Заодно починил» | «Попутно: null-check в applyPayment (2 строки, баг #1234). Если не хотите в этом PR — откатываю» |

---

### 5. Главный индикатор

Отчёт хороший, если **владелец, прочитав его, может принять решение**, не задавая вопросов.

- «Закрыть задачу?» → да, у меня есть ответ (все DoD зелёные + границы).
- «Можно mergить?» → да, у меня есть ответ (CI зелёный + ревью + тикеты).
- «Что дальше?» → да, у меня есть ответ (FOLLOWUP + тикеты + сроки).
- «Что если не сработает?» → да, у меня есть ответ (risks + rollback).

Если на любой из четырёх вопросов я не могу ответить **из отчёта** — отчёт не готов.

> ⏱ 38.4s · prompt 111463 tok · output 2849 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about FORCE INDEX in PostgreSQL. Let me think carefully here.

FORCE INDEX is a MySQL syntax. In MySQL/MariaDB, you have INDEX(), FORCE INDEX(), and IGNORE INDEX() hints in SELECT/UPDATE/DELETE statements. These are optimizer hints.

PostgreSQL does NOT have FORCE INDEX syntax. This is a common point of confusion because people coming from MySQL ask about it. PostgreSQL's approach is fundamentally different:
1. PostgreSQL does not support optimizer hints in the same way. You cannot write `SELECT ... FROM t FORCE INDEX (idx)` in PostgreSQL — that's a syntax error.
2. PostgreSQL relies on the cost-based optimizer and statistics. If the plan is bad, you typically fix it by: updating statistics (ANALYZE), adjusting cost parameters (random_page_cost, seq_page_cost, effective_cache_size, work_mem, etc.), adding/removing indexes, or using configuration settings.
3. There are some ways to "force" a behavior in PostgreSQL indirectly:
   - SET enable_hashjoin = off, SET enable_seqscan = off, etc. — these are "cost-based" toggles that make the optimizer treat certain methods as infinitely expensive. They're sometimes used to force a plan, but they are dangerous and not recommended as a general solution.
   - Using a specific index in a query by including its columns in WHERE so the planner picks it.
   - Creating a partial index so only that plan is viable.
   - Using `SET` statements in a transaction or via `ALTER SYSTEM` / role-level settings.

So the honest answer is: FORCE INDEX does not exist in PostgreSQL. It's a MySQL concept. I should clarify this, explain what it does in MySQL, and then explain the PostgreSQL equivalents and why they differ philosophically.

The context: this is a continuation of an interview-style conversation about PostgreSQL performance and index usage. Earlier I discussed indexes, EXPLAIN ANALYZE, covering indexes, etc. So the user is likely either (a) confusing MySQL and PostgreSQL, or (b) testing whether I'll blindly answer "FORCE INDEX" as if it exists in Postgres.

I should not pretend FORCE INDEX exists in PostgreSQL. That would be a factual error and a bad answer. The right move is to:
1. Clearly state that FORCE INDEX is a MySQL/MariaDB feature, not PostgreSQL.
2. Briefly explain what it does in MySQL (for context).
3. Explain PostgreSQL's philosophy and the actual tools available when the planner picks a bad plan.
4. Give concrete examples of the PostgreSQL equivalents (enable_* toggles, ANALYZE, cost parameters, partial indexes).
5. Note the risks.

Let me make sure my PostgreSQL facts are correct:
- `SET enable_seqscan = off;` — yes, valid GUC. Makes seqscan cost infinity, so planner avoids it.
- `SET enable_indexscan = off;` — yes.
- `SET enable_hashjoin = off;` — yes.
- `SET enable_nestloop = off;` — yes.
- `SET enable_material = off;` — yes.
- `SET enable_sort = off;` — yes.
- `SET enable_bitmapscan = off;` — yes.
These were introduced around PostgreSQL 8.3. They set the method's cost to a very high value (INT_MAX-ish), strongly discouraging but not absolutely forbidding. Actually they make it "cost = infinity" so it's never chosen unless no alternative. They are intended for debugging, not production.

- random_page_cost, seq_page_cost — cost parameters.
- effective_cache_size — affects index vs seqscan decision.
- work_mem — affects hash joins, sorts.
- ANALYZE / autovacuum — statistics.
- partial indexes — yes, supported since 9.5.
- There's also the ability to use `SET LOCAL` in a transaction.
- `jit` for JIT compilation, not relevant.
- There is no `USE INDEX` / `FORCE INDEX` / `IGNORE INDEX` in PostgreSQL grammar. Confirmed.

Also worth mentioning: in PostgreSQL you can sometimes influence via rewriting the query (e.g., adding a volatile function, but that's a hack). And there's the approach of `COST` on functions to affect planning. And `pg_hint_plan` is an extension that adds hint-like capability — this is a nice concrete answer for "I really want hints in Postgres". Yes, pg_hint_plan exists and lets you do /*+ IndexScan(...) */ style hints. I should mention it as the closest thing, with caveats (it's an extension, adds coupling, generally discouraged).

So a good answer structure:
1. Direct: FORCE INDEX is not a PostgreSQL feature — it's MySQL. Don't write it in Postgres (syntax error).
2. What it does in MySQL (brief).
3. Why Postgres differs: cost-based optimizer, statistics-driven.
4. How to actually influence the planner in Postgres:
   - Fix the root cause: ANALYZE (stale stats), cost parameters, better index.
   - enable_* = off toggles (debugging, risky).
   - Partial indexes / query rewrite.
   - pg_hint_plan extension (closest to hints).
5. When you'd reach for "forcing" a plan and why usually you shouldn't.
6. Recommendation: diagnose with EXPLAIN ANALYZE first; forcing is a last resort and usually a smell.

This is an interview-style question, so I should be precise and also show judgment (the "right" answer is that FORCE INDEX doesn't exist and forcing plans is usually a smell). I should be concise but complete, use the established formatting style (headings, tables, code blocks) consistent with the rest of the conversation.

Let me also double check: is there any PostgreSQL syntax that people might call "force index"? There's `CREATE INDEX`, and in older discussions people use `SET enable_indexonlyscan`. There's also the `INDEX` keyword only in `CREATE INDEX` and `ALTER INDEX`, not in DML hints. So in a SELECT you cannot force an index. Correct.

One more: `SET search_path`? no. `pg_stat_statements`? no.

I think I'm confident. Let me write the answer in Russian, matching the conversation language.

I'll keep it focused and not overly long, but the topic deserves a thorough answer since it's a "gotcha" question. I'll include a small table of enable_* toggles and a concrete example.

Format: a short framing, then sections. Use bold for key terms. End with the practical recommendation. No need for a huge closing summary — but a crisp "bottom line" is good given the interview framing.

</details>

**Кандидат:**

## Коротко: `FORCE INDEX` в PostgreSQL не существует

Это частая ловушка. `FORCE INDEX` (а также `USE INDEX`, `IGNORE INDEX`) — это **синтаксис MySQL/MariaDB**. В PostgreSQL такой конструкции в `SELECT`/`UPDATE`/`DELETE` **нет** — браузер/драйвер её не распарсит:

```sql
-- MySQL: ок
SELECT * FROM orders FORCE INDEX (idx_status_created) WHERE status = 'issued';

-- PostgreSQL: синтаксическая ошибка
SELECT * FROM orders FORCE INDEX (idx_status_created) WHERE status = 'issued';
--          ^ не существует в грамматике PostgreSQL
```

Поэтому если кто-то предлагает «FORCE INDEX» в Postgres — он либо переносит мышление из MySQL, либо имеет в виду что-то из обходных средств ниже.

---

### Что делает `FORCE INDEX` в MySQL (для контекста)

MySQL-оптимизатор иногда выбирает «не тот» индекс (например, `idx_a` вместо более подходящего `idx_b`, или полный обход). `FORCE INDEX (idx_b)` говорит планировщику: **используй именно этот индекс** при чтении. Это **hint** — подсказка, а не контракт.

Аналогичные MySQL-подсказки:
- `USE INDEX (…)` — «рассмотри, пожалуйста, вот эти».
- `IGNORE INDEX (…)` — «не используй вот эти».

Философия MySQL: **компромисс между статистикой и явной подсказкой разработчика**.

---

### Почему PostgreSQL устроен иначе

PostgreSQL — **строго cost-based**. Планировщик строит дерево планов и выбирает минимальную оценённую стоимость на основе:

1. **Статистик** таблицы (`pg_statistic`, собирается `ANALYZE` / `autovacuum`).
2. **Стоимостных параметров** (`seq_page_cost`, `random_page_cost`, `cpu_tuple_cost`, `effective_cache_size`, `work_mem` и др.).
3. **Характеристики индекса** и выборки.

В этой модели **нет места для «поверь мне, бери этот индекс»** — есть только «вот данные, посчитай сам». Это осознанный выбор: меньше ручного вмешательства, меньше «магии», но и меньше рычагов форсирования.

---

### Чем в PostgreSQL влияют на выбор плана

По убыванию «правильности».

#### 1. Убери причину (95% случаев)

Плохой план почти всегда следствие одной из причин — и её лечат, а не форсируют:

| Причина | Лечение |
|---------|---------|
| Устаревшая статистика (оценка `rows` врёт) | `ANALYZE table;` / починить `autovacuum` |
| Индекс есть, но планировщик его не видит из-за `random_page_cost` | Скорректировать `random_page_cost` / `effective_cache_size` под реальное хранилище |
| Нет подходящего индекса | Создать (covering / partial) |
| Выборка > 30% таблицы | Seq Scan **правильный** — не «чините» |
| `work_mem` мал → sort/hash дорогой | Увеличить `work_mem` (осторожно) |

**Это не форсинг, а исправление входных данных для оптимизатора.** Если после `ANALYZE` план стал хорошим — проблема решена, никакие hints не нужны.

#### 2. Toggle-методы `enable_* = off` (отладка, не прод)

PostgreSQL позволяет «сделать бесконечно дорогим» конкретный метод:

```sql
SET enable_seqscan  = off;  -- запрещает полный обход
SET enable_hashjoin = off;  -- запрещает hash join
SET enable_nestloop = off;
SET enable_sort     = off;
SET enable_bitmapscan = off;
```

Планировщик не обязан их слушать, если другого выбора нет, но **сильно** их избегает.

**Важно:**
- Это **отладочный** инструмент: он ломает cost-модель, а не улучшает её.
- `SET` на уровне сессии/транзакции (`SET LOCAL`), а не глобальный `ALTER SYSTEM` — иначе вы форсите план **для всех запросов** и, скорее всего, ухудшаете остальные.
- После применения — **обязательно** `RESET enable_*;`, иначе сессия остаётся в сломанном состоянии.

Когда оправдано: подтвердить, что «да, именно seqscan мешает» и понять, **почему** (нет индекса? статистика?). После этого — создать индекс, а не держать toggle.

#### 3. Управляемая конструкция: partial index / переписывание запроса

Иногда проще **сделать так, чтобы правильный план был единственно возможным**:

```sql
-- Если 99% запросов — по status='issued', а редкие — по старым:
CREATE INDEX idx_invoices_active
    ON invoices (created_at DESC)
    WHERE status = 'issued';
```

Теперь для «активных» запросов частичный индекс — самый дешёвый вариант, и планировщик его выберет **естественно**, без форсинга. Для «архивных» — свой план. Это чище, чем hint.

#### 4. `pg_hint_plan` — единственное, что похоже на FORCE INDEX

Расширение `pg_hint_plan` добавляет комментарии-hints:

```sql
SELECT /*+ IndexScan(inv idx_invoices_status_created) HashJoin(inv l) */
    ...
FROM invoices inv
JOIN invoice_lines l ON l.invoice_id = inv.id;
```

**Но:**
- Это **внешняя зависимость**: без расширения запрос «ломается» по смыслу (комментарий просто игнорируется, и план снова плохой).
- Привязывает код к конкретной версии/расширению.
- Обходная дорожка, которую обычно **не рекомендуется** как основное решение.

Используйте, только если: (а) причина в том, что статистика/стоимости принципиально не выражают нужный план, (б) вы готовы нести coupling на `pg_hint_plan`.

---

### Сравнение «форсинга» в трёх СУБД

| | MySQL | PostgreSQL |
|---|-------|-----------|
| Встроенный hint на индекс | `FORCE INDEX` / `USE INDEX` / `IGNORE INDEX` | **Нет** |
| Тогглы методов | Ограниченно (`optimizer_switch`, `IGNORE INDEX`) | `enable_seqscan/hashjoin/… = off` |
| Расширение-hints | Есть (редко) | `pg_hint_plan` |
| Главный рычаг | Статистика + hint | Статистика + cost-параметры + индексы |

---

### Когда «форсировать план» — это красный флаг

Если вам **регулярно** нужен `enable_seqscan=off` или `pg_hint_plan`, чтобы запрос был быстрым — это симптом одной из проблем:

1. **Статистика врёт** → починить `ANALYZE`/`autovacuum`.
2. **Нет индекса** → создать (covering/partial).
3. **Стоимостные параметры не отражают железо** → откалибровать `random_page_cost`/`effective_cache_size`.
4. **Запрос/модель данных плохие** → переписать (JOIN вместо подзапроса, денормализация, covering index).

Пока не устранена причина, форсинг держится на честном слове: сменился объём данных, добавили колонку, обновили Postgres — и «зафорсенный» план снова становится плохим, а вы этого даже не заметите.

---

### Итог

- **`FORCE INDEX` в PostgreSQL нет** — это MySQL. Не пишите его в Postgres.
- PostgreSQL управляет планом **через статистику и cost-модель**, а не подсказки.
- Правильный порядок: `EXPLAIN ANALYZE` → найти причину (статистика/индекс/стоимости) → исправить причину.
- `enable_* = off` — для **отладки**, не для прода.
- `pg_hint_plan` — единственное, что ближе всего к FORCE INDEX, но это coupling на расширение и признак того, что что-то в модели данных или статистике не так.

Если коротко: в PostgreSQL **не форсируют план — его не ломают**. Форсинг здесь — почти всегда признак того, что реальная проблема осталась незамеченной.

> ⏱ 48.7s · prompt 113870 tok · output 3391 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

The user is asking me to quantify (in percentages) how much `strict_types=1` slows down PHP code execution, based on my "experience." This is a trap question — it presupposes a measurable, meaningful performance impact that doesn't really exist in any practical sense.

Let me think about what actually happens at the engine level:

1. `declare(strict_types=1)` is a **file-level compile-time flag**. It's stored in the opcodes of the file. When a function call is made from a strict file to a function with typed parameters, the Zend engine performs a **stricter type check** at the call site.

2. What exactly changes at runtime:
   - In coercive mode: the engine checks if the value is of the correct type OR can be coerced. If coercion is needed, it performs the coercion (e.g., string→int).
   - In strict mode: the engine checks if the value is EXACTLY of the correct type. If not, it throws a TypeError. No coercion is attempted.

3. Performance implications:
   - The type check itself is essentially the same cost in both modes — it's a type comparison. The difference is in the **fallback path**: in coercive mode, if the type doesn't match, it tries to coerce (which is more expensive). In strict mode, if the type doesn't match, it throws immediately (also more expensive than a simple match).
   - When the types MATCH (which is 99.9% of the time in correctly written code), the cost is **identical** in both modes. The type check is the same.
   - The "slowdown" would only be measurable in the case of type mismatches, where strict mode fails faster (throws) vs coercive mode doing a conversion. But this is a pathological case.

4. Real-world benchmarks:
   - I've never seen a credible benchmark showing a measurable difference between strict and non-strict modes for correctly-typed code.
   - The opcodes generated are essentially the same. The `strict_types` flag is checked at the call site, but it's a single boolean check that's already part of the type verification.
   - Any difference would be well within noise (< 0.1%), and would be completely dwarfed by any other factor (DB queries, network calls, I/O).

5. Why people think it slows things down:
   - Confusion with "more type checking = slower" (intuitive but wrong in this case).
   - In C/C++, strict type checking in some contexts can add overhead, but PHP's model is different.
   - The real "cost" of strict_types is **development time** (fixing type mismatches), not runtime.

6. What I should say:
   - The honest answer: there is no meaningful measurable slowdown. For correctly-typed code, the performance is identical.
   - Explain WHY: the type check is the same in both modes; the difference is only in the error path (throw vs coerce), which you never hit in working code.
   - The real cost is development cost, not runtime cost.
   - If someone claims "X% slowdown," they're measuring noise or have a methodological problem.

I should be direct: this question contains a false premise. `strict_types=1` does not measurably slow down correctly-typed code. The honest answer is "0% (within measurement noise)" and I should explain the mechanism.

</details>

**Кандидат:**

## `strict_types=1` не замедляет код. Честный ответ: 0%

Вопрос содержит ложную посылку, и я отвечу прямо, а не подыграю.

---

### Что реально происходит на уровне движка

`declare(strict_types=1)` — это **флаг на уровне файла**, который записывается в opcodes при компиляции. Он влияет **только на один момент**: что происходит на границе вызова функции, когда тип аргумента не совпадает с объявленным.

```
Вызов: charge("100") → параметр: int $amount
```

| Режим | Что делает движок |
|-------|-------------------|
| **coercive** (без strict) | Проверяет тип → не совпадает → **приводит** `"100"` → `100` → передаёт. Успех. |
| **strict** | Проверяет тип → не совпадает → **бросает TypeError**. Ошибка. |

Ключевое: **проверка типа — одна и та же в обоих режимах.** Разница — только в **ветке «не совпало»**: приведение vs исключение.

---

### Почему «замедление» невозможно измерить

#### 1. В корректном коде типы совпадают

Если код написан правильно (а он должен быть, иначе это баг), то:

```php
charge(100);  // int → int. Совпадает.
```

В этом случае:
- **coercive**: проверяет тип → совпадает → передаёт.
- **strict**: проверяет тип → совпадает → передаёт.

**Операция идентична.** Ни одного дополнительного цикла, ни одной дополнительной ветки. Флаг `strict_types` даже не читается — он влияет только на «else» ветку.

#### 2. Отличие проявляется только при НЕСОВПАДЕНИИ

Если типы не совпадают, strict **быстрее**, а не медленнее:

```
coercive: проверка → несовпадение → приведение (string→int) → передача
strict:   проверка → несовпадение → throw TypeError
```

Strict **бросает исключение раньше**, чем coercive начал бы приведение. То есть в единственном случае, где есть разница, strict быстрее.

#### 3. На уровне opcodes

```
; coercive и strict генерируют ОДИНАКОВЫЕ opcodes для успешного вызова:
ZEND_CHECK_TYPES  (проверка типа)
ZEND_SEND_VAL     (передача аргумента)
ZEND_DO_FCALL     (вызов)
```

Флаг `strict_types` не добавляет opcode. Он изменяет **поведение** `ZEND_CHECK_TYPES` при ошибке, не при успехе.

---

### Где можно «измерить разницу» — и почему это шум

Если написать бенчмарк:

```php
function f(int $x): int { return $x; }

// strict file
for ($i = 0; $i < 1_000_000; $i++) { f(1); }

// coercive file
for ($i = 0; $i < 1_000_000; $i++) { f(1); }
```

Результат: **разница в пределах шума (< 0.1%, часто отрицательная)**. Потому что:
- Оба вызова — `f(1)`, `int → int`.
- Тип совпадает. Проверка одна.
- Разница в clock jitter, кэше CPU, allocator'е.

Единственный способ получить «разницу» — передавать **несовпадающие типы**:

```php
// coercive: f("1") → приведение → работает
// strict:   f("1") → TypeError → падает
```

Но это **не бенчмарк производительности**. Это сравнение «работает» vs «падает».

---

### Где strict_types реально «стоит»

| Стоимость | Где |
|-----------|-----|
| **Разработка**: нужно исправить все несовпадения типов | При включении в legacy-коде |
| **CI**: тесты падают с TypeError вместо «тихого» приведения | При введении в проект |
| **Мышление**: нельзя «опереться на приведение» | При проектировании API |
| **Рантайм**: 0 | — |

Вся «цена» `strict_types=1` — **разовая стоимость приведения кода к типобезопасности**. После этого — ноль.

---

### Почему люди думают, что это замедляет

| Миф | Реальность |
|-----|-----------|
| «Больше проверок = медленнее» | Проверка одна и та же. Отличие только в ошибочной ветке |
| «Движок «думает» о strict» | Флаг — это 1 бит в компилированном файле. Не читается при успехе |
| «В C++ строгие типы медленнее» | В C++ — да (template, vtable). В PHP — нет: типизация в рантайме, не в compile-time |
| «Я читал про 5%» | Методологическая ошибка бенчмарка (см. выше) |

---

### Итог

> **`strict_types=1` не замедляет PHP-код. Разница в производительности — 0% в пределах шума.**
>
> Его цена — разовая стоимость исправления типов при введении. После этого — бесплатно.
>
> Если кто-то утверждает «X% замедление» — у него либо методологическая ошибка бенчмарка, либо он измеряет что-то другое (не strict_types).

> ⏱ 28.1s · prompt 115867 tok · output 2045 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о Doctrine ORM и составных первичных ключах (composite primary keys). Нужно дать честный, технически точный ответ.

Важный момент: утверждение в вопросе частично неверно. Doctrine ORM ПОДДЕРЖИВАЕТ составные первичные ключи — через `@Id` на нескольких полях, `@Generic` mapping, `ClassMetadata`. Это не так, как в JPA/Hibernate, где composite key — отдельный объект, но в Doctrine это работает через несколько `#[Id]` на разных полях.

Но есть нюансы:
1. Doctrine поддерживает composite key на уровне маппинга.
2. Но это неудобно: `find()` принимает массив, identity map работает по массиву, lazy loading коллекций с composite key — баги/ограничения.
3. Есть известные проблемы: `#[Id]` на нескольких полях — supported, но некоторые фичи (relations с composite FK) — кривовато.
4. Реальные проекты чаще избегают composite PK, используют surrogate key (UUID/serial) + unique constraint.

Нужно разобрать:
- Что именно Doctrine поддерживает/не поддерживает
- Почему composite key неудобен именно в ORM (а не в SQL)
- Как живут в реальных проектах
- Антипаттерны
- Когда composite key всё-таки оправдан

Структура ответа:
1. Честный разбор: «не поддерживает» — это не совсем верно, поддерживает, но криво
2. Что работает, что нет
3. Почему ORM (и Doctrine в частности) плохо дружит с composite key
4. Стратегии в реальных проектах
5. Когда composite key всё-таки нужен

Отвечаю на русском, как и весь разговор.

</details>

**Кандидат:**

## Честный разбор: «не поддерживает» — это не совсем верно

Doctrine ORM **поддерживает** составные первичные ключи. Это не так, как в Hibernate/JPA, где composite key — отдельный объект. В Doctrine вы просто помечаете несколько полей `#[Id]`:

```php
#[ORM\Entity]
final class OrderLine
{
    #[ORM\Id]
    #[ORM\Column]
    private int $orderId;

    #[ORM\Id]
    #[ORM\Column]
    private int $productId;

    #[ORM\Column]
    private int $quantity;
}

// find() принимает массив:
$line = $repo->find(['orderId' => 1, 'productId' => 42]);
```

Это работает. Но есть нюансы, которые заставляют людей считать, что «не поддерживает». Разберу по порядку.

---

### 1. Что работает

| Фича | Статус |
|------|--------|
| `#[Id]` на нескольких полях | ✅ Работает |
| `find(['a' => 1, 'b' => 2])` | ✅ Работает |
| `INSERT/UPDATE/DELETE` по составному PK | ✅ Работает |
| `persist()`, `remove()` | ✅ Работает |
| Change tracking | ✅ Работает |
| Composite FK (child ссылается на composite parent) | ⚠️ Работает, но криво |
| Relations с composite key | ⚠️ Баги/ограничения |
| DQL с composite key | ⚠️ Невелико |
| `UnitOfWork` с composite key | ⚠️ Edge cases |

---

### 2. Что неудобно (и почему это «почти не поддерживает»)

#### Проблема 1: Identity map работает по массиву

```php
$a = $repo->find(['orderId' => 1, 'productId' => 42]);
$b = $repo->find(['orderId' => 1, 'productId' => 42]);

// $a === $b → true (identity map)
```

Работает, но:
- `find()` принимает массив — нет типобезопасности.
- Опечатка в ключе массива (`['order_id' => 1]` вместо `['orderId' => 1]`) → `null`, не ошибка.
- В DQL: `WHERE o.orderId = :id AND o.productId = :pid` — два параметра, легко перепутать.

#### Проблема 2: Relations с composite key — кривовато

```php
// Child ссылается на parent с composite PK
#[ORM\Entity]
final class OrderLine
{
    #[ORM\Id] private int $orderId;
    #[ORM\Id] private int $productId;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'orderId')]
    // ↑ referencedColumnName — одно поле, а PK у parent составной
    private Order $order;
}
```

Здесь `JoinColumn` с одним `referencedColumnName` — **не выражает составной FK** корректно. Для составного FK нужны **два** `JoinColumn`:

```php
#[ORM\ManyToOne]
#[ORM\JoinColumns([
    new ORM\JoinColumn(name: 'line_order_id', referencedColumnName: 'orderId'),
    new ORM\JoinColumn(name: 'line_product_id', referencedColumnName: 'productId'),
])]
```

Это работает, но:
- `inversedBy`/`mappedBy` с composite key — часто ломает change tracking.
- `orphanRemoval` с composite key — edge cases.
- DQL JOIN с composite key — не всегда корректно резолвится.

#### Проблема 3: `UnitOfWork` и composite key

```php
$line = new OrderLine(1, 42, 5);
$em->persist($line);

// Изменяем PK
$line->orderId = 2;  // ← это не «изменение поля», это «другая сущность»
$em->flush();
// ↑ Doctrine не поймёт, что это UPDATE, а не INSERT нового + удаление старого
```

Изменение PK в ORM — антипаттерн. В composite key это особенно больно, потому что:
- Нет одного «основного» PK, который можно менять.
- `identity map` ключ — массив, изменение любого элемента = новая сущность.

#### Проблема 4: DQL и composite key

```dql
SELECT l FROM OrderLine l
WHERE l.orderId = :oid AND l.productId = :pid
```

Работает, но:
- Нет `WHERE l IN ((1, 42), (1, 43))` — нет синтаксиса для составных значений.
- `BETWEEN` по составному PK — нет.
- Индексы: `EXPLAIN` покажет, что Postgres использует composite index, но DQL это не выражает.

#### Проблема 5: `find()` — нет типобезопасности

```php
$repo->find(['orderId' => 1, 'productId' => 42]);  // ок
$repo->find(['orderId' => 1]);                       // null, не ошибка
$repo->find(['oid' => 1, 'productId' => 42]);        // null, не ошибка
```

В отличие от `find(1)` для simple PK, composite `find()` — это «верь, что массив правильный».

---

### 3. Почему ORM (и Doctrine) плохо дружат с composite key

Причина не в «неумении», а в **модели данных**:

| Простой PK | Composite PK |
|------------|-------------|
| Сущность идентифицируется одним значением | Сущность идентифицируется **множеством** значений |
| `find($id)` — одно значение | `find([$a, $b])` — массив, порядок важен |
| Identity map: `id → object` | Identity map: `(a, b) → object` (сложнее) |
| Relations: `child.order_id → order.id` | Relations: `child.order_id → order.order_id` И `child.product_id → order.product_id` |
| `UPDATE ... SET x = y WHERE id = 1` | `UPDATE ... SET x = y WHERE a = 1 AND b = 2` |

ORM проектировалась вокруг **простого PK**. Composite key — это «разрешённое исключение», а не основной случай. Поэтому:
- API `find()` — принимает массив (неудобно).
- `JoinColumn` — один на один `referencedColumnName` (не выражает составной FK).
- DQL — нет синтаксиса для составных значений.
- `UnitOfWork` — change tracking по одному PK (компромисс для composite).

Это **архитектурное решение**, а не «баг». Hibernate/JPA решает то же через `@EmbeddedId` + отдельный объект ключа — и это **хуже**, чем массив в Doctrine.

---

### 4. Как живут в реальных проектах

#### Стратегия 1: Surrogate key (95% случаев)

Заменим composite PK на один `id` + `UNIQUE` constraint:

```php
// ❌ Composite PK
#[ORM\Entity]
final class OrderLine
{
    #[ORM\Id] private int $orderId;
    #[ORM\Id] private int $productId;
    #[ORM\Column] private int $quantity;
}

// ✅ Surrogate PK
#[ORM\Entity]
#[ORM\UniqueConstraint(columns: ['order_id', 'product_id'])]
final class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column] private int $orderId;
    #[ORM\Column] private int $productId;
    #[ORM\Column] private int $quantity;
}
```

**Плюсы:**
- `find(1)` — просто.
- Relations — один `order_id` FK.
- DQL — `WHERE l.id = 1`.
- Identity map — простой.
- Изменение `productId` — `UPDATE ... WHERE id = 1` (а не «удали + вставь»).

**Минусы:**
- Лишняя колонка `id` (4 байта, индекс).
- `UNIQUE (order_id, product_id)` — защита от дублей.
- Чуть больше overhead на INSERT (генерация ID).

**Когда использовать:** почти всегда. Если «составной ключ» — это «заказ + товар», «пользователь + роль», «компания + проект» — это **surrogate key + unique constraint**.

#### Стратегия 2: Composite PK, если это **естественный** ключ

Иногда composite key — это **единственный** смысл:

```php
// Матрица курсов валют: пара валют = ключ
#[ORM\Entity]
final class ExchangeRate
{
    #[ORM\Id]
    #[ORM\Column(length: 3)]
    private string $fromCurrency;  // 'EUR'

    #[ORM\Id]
    #[ORM\Column(length: 3)]
    private string $toCurrency;    // 'USD'

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    private string $rate;

    #[ORM\Column]
    private \DateTimeImmutable $date;
}
```

Здесь `id` — **не** суррогат. «EUR→USD на 2024-01-15» — это и есть сущность. Суррогатный `id` здесь не несёт смысла.

**Когда оправдан:**
- Матрицы / lookup-таблицы (currency pairs, i18n).
- Junction tables (m2m) — но и там часто surrogate key.
- Природные ключи, где состав — суть (пользователь + роль).

**Минусы в этом случае:**
- `find(['fromCurrency' => 'EUR', 'toCurrency' => 'USD'])` — массив.
- Relations к этой сущности — composite FK (криво).
- DQL — не всегда удобно.

#### Стратегия 3: Junction table с surrogate key

Для m2m:

```php
// ❌ Composite PK
#[ORM\Entity]
final class UserSkill
{
    #[ORM\Id] private int $userId;
    #[ORM\Id] private int $skillId;
}

// ✅ Surrogate + unique
#[ORM\Entity]
#[ORM\UniqueConstraint(columns: ['user_id', 'skill_id'])]
final class UserSkill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column] private int $userId;
    #[ORM\Column] private int $skillId;

    // Бонус: можно хранить metadata
    #[ORM\Column] private int $level;
    #[ORM\Column] private \DateTimeImmutable $obtainedAt;
}
```

**Почему surrogate лучше для m2m:**
- Можно добавить `level`, `obtainedAt`, `verified` — metadata.
- Relations — один FK.
- `remove()` — по одному `id`.

#### Стратегия 4: `embeddable` для составного ключа (редко)

```php
#[ORM\Embeddable]
final class OrderLineId
{
    #[ORM\Column] private int $orderId;
    #[ORM\Column] private int $productId;

    public function __construct(int $orderId, int $productId) { ... }
}

#[ORM\Entity]
final class OrderLine
{
    #[ORM\Embedded(class: OrderLineId::class)]
    #[ORM\Id]  // ← Id на embeddable
    private OrderLineId $id;
}
```

Это **не** полноценный composite key в смысле Hibernate. Это embeddable, помеченный `#[Id]` — работает, но:
- `find()` всё равно принимает массив.
- Relations — по-прежнему криво.
- Не решает проблемы с `UnitOfWork`.

Редко оправдан.

---

### 5. Сравнение стратегий

| Стратегия | Когда | Плюсы | Минусы |
|-----------|-------|-------|--------|
| Surrogate key + unique | 95% случаев | Простой API, relations, DQL | Лишняя колонка `id` |
| Composite PK (natural) | Матрицы, i18n, lookup | Семантика, нет лишней колонки | Неудобный API, кривые relations |
| Junction + surrogate | m2m | Metadata, простой API | Лишняя колонка `id` |
| Embeddable + Id | Редко | Чуть чище, чем «два `#[Id]`» | Не решает все проблемы |

---

### 6. Антипаттерны

#### ❌ 1. Composite PK «потому что так в таблице»

```php
// В БД есть composite PK (историческая причина)
// → Переносим в Doctrine
// → Страдаем с find(), relations, DQL
// → Лучше: surrogate key + unique constraint + миграция
```

#### ❌ 2. Изменение composite PK

```php
$line->orderId = 2;  // ← это «другая сущность», не «обновление»
$em->flush();
// ↑ Doctrine не поймёт, что делать
```

**Правило:** PK — immutable. Если PK меняется — это **новая** сущность.

#### ❌ 3. Composite key в m2m без metadata

```php
// Если в будущем понадобится level, obtainedAt —
// придётся делать миграцию:
//   1. Добавить колонку id
//   2. Заполнить id
//   3. Сменить PK
//   4. Удалить старый unique
// → Лучше сразу surrogate key
```

#### ❌ 4. `find(['orderId' => $a, 'productId' => $b])` в цикле

```php
foreach ($pairs as [$oid, $pid]) {
    $line = $repo->find(['orderId' => $oid, 'productId' => $pid]);
    // N запросов, N identity map lookups
}
```

**Правильно:**
```php
// Один запрос
$lines = $repo->findBy([
    'orderId' => $oid,
]);
// Или DQL с IN (для нескольких)
```

---

### 7. Итог

| Утверждение | Реальность |
|-------------|-----------|
| «Doctrine не поддерживает composite key» | **Неверно.** Поддерживает, но неудобно |
| «Composite key — антипаттерн» | **Часто верно.** Surrogate key + unique — лучше |
| «Composite key — всегда нужен» | **Редко.** Матрицы, lookup, natural keys |
| «Surrogate key — лишний overhead» | **Нет.** 4 байта + индекс — дешевле, чем страдать с API |

**Практическое правило:**

> Если «составной ключ» — это «A + B», и A и B — сами сущности, — это **surrogate key + unique constraint**.
>
> Если «составной ключ» — это **один** смысл (пара валют, i18n, матрица) — это **composite PK**.
>
> Во всех остальных случаях — surrogate key.

> ⏱ 46.8s · prompt 117212 tok · output 3889 tok

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

The user wants me to review a PHP code snippet and identify problems, then propose a refactor. Let me analyze this carefully.

The code is a classic "fat controller" with raw SQL, mixing concerns. Let me enumerate the problems systematically:

1. **SQL Injection** — `$id` from request is interpolated directly into SQL. Critical.
2. **No authentication/authorization** — anyone can pay any order by ID.
3. **No input validation** — no check that `order_id` is numeric.
4. **No idempotency** — double-click pays twice (well, sets status twice, but the mail is sent twice).
5. **No transaction** — if Mailer::send fails, the order is already marked paid. Or if the UPDATE fails, we return 404 but the mail hasn't been sent (good), but the logic is fragile.
6. **Business logic in controller** — status transition, mail, logging all in one method.
7. **No domain model** — `status = 'paid'` is a magic string, no state machine.
8. **No payment processing** — it just sets status to 'paid' without actually charging. No gateway, no token.
9. **Mailer in controller** — direct coupling to mailer, no async, no retry.
10. **Logger instantiation per request** — minor, but should be injected.
11. **No error handling** — Mailer::send could throw, PDO could throw.
12. **No CSRF protection** — payment is a state-changing POST.
13. **Response format** — no JSON, no consistent API.
14. **`SELECT *`** — fetches all columns.
15. **No concurrency protection** — two requests can both read 'pending' and both set 'paid'.
16. **No event/side-effect separation** — mail and log are in the same flow as the state change.
17. **Hardcoded strings** — 'paid', 'Ваш заказ оплачен'.
18. **No separation of concerns** — controller does: parse input, query DB, validate, update DB, send mail, log, respond.

Let me structure my answer well. I'll list problems by severity, then show a refactored version. I'll explain my approach: start from the most critical (security), then structure (separation of concerns), then robustness (concurrency, idempotency, transactions).

I should be thorough but not overwhelming. Let me group problems into categories:
- Security (critical)
- Correctness (high)
- Architecture (medium)
- Robustness (medium)
- Style (low)

Then show the refactored code layer by layer.

Let me write this in Russian since the conversation is in Russian.

</details>

**Кандидат:**

## Проблемы

### Критичные (безопасность)

| # | Проблема | Риск |
|---|----------|------|
| 1 | **SQL Injection.** `$id` из запроса вставляется прямо в SQL | `?order_id=1;DROP TABLE orders--` |
| 2 | **Нет авторизации.** Любой может оплатить любой заказ по ID | Оплата чужого заказа, доступ к чужим email |
| 3 | **Нет CSRF-защиты.** POST-запрос без токена | Скрипт на стороннем сайте → оплата |

### Серьёзные (корректность)

| # | Проблема | Риск |
|---|----------|------|
| 4 | **Нет обработки платежа.** Статус ставится в `paid` без фактического списания | Деньги не списаны, а заказ «оплачен» |
| 5 | **Нет идемпотентности.** Двойной клик = два письма, лог ×2 | Двойная отправка, дубли в логах |
| 6 | **Нет транзакции.** UPDATE прошёл, `Mailer::send()` упал | Заказ «оплачен», письмо не ушло. Или наоборот |
| 7 | **Нет конкурентной защиты.** Два запроса одновременно читают `pending` → оба пишут `paid` | Lost update, двойная обработка |
| 8 | **Нет валидации.** `order_id` может быть `abc`, `null`, `1.5` | Баг, краш, или оплата заказа #1 вместо #15 |

### Архитектурные

| # | Проблема |
|---|----------|
| 9 | Бизнес-логика в контроллере: проверка, UPDATE, письмо, лог — в одном методе |
| 10 | Нет доменной модели: `'paid'` — магическая строка, нет state machine |
| 11 | `Mailer` и `Logger` создаются внутри метода — нет DI, нет тестировательности |
| 12 | `SELECT *` — тянет все колонки, лишние данные в памяти |
| 13 | Нет событий/асинхронности: письмо блокирует ответ пользователю |
| 14 | Нет разделения слоёв: нет use case, нет repository, нет VO |

### Мелкие

| # | Проблема |
|---|----------|
| 15 | `new Response('ok')` — нет HTTP-кода (по умолчанию 200, но явно лучше) |
| 16 | Логика «нашёл / не нашёл» дублируется (SELECT + if) |
| 17 | `DB::connect()` — новый коннект на каждый запрос |

---

## Как переделываю: порядок и почему

Не переписываю всё сразу. Иду от **самой опасной** проблемы к **самой глубокой**. Каждый шаг — отдельный PR.

```
1. Убить SQL injection (1 строка)          ← сегодня
2. Добавить authz (1 middleware)            ← сегодня
3. Вынести бизнес-логик

> ⏱ 19.9s · prompt 120926 tok · output 1316 tok

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

Разберу сущность на проблемы DDD и чистой архитектуры. Ключевые грехи: 1) EntityManager в конструкторе сущности — сущность знает об ORM/инфраструктуре, нарушение DIP и инкапсуляции, сущность «знает, что её персистят», это утечка адаптера в домен; 2) `flush()` внутри `changeEmail` — сущность управляет транзакцией/циклом жизненного цикла, а не use case; flush должен вызываться на верхнем уровне, иначе теряется атомарность и контроль; 3) конструктор с DI-зависимостью делает создание сущности зависимым от инфраструктуры — нельзя создать User в юнит-тесте без EM; 4) нет immutable-семантики/инвариантов: email не валидируется (может быть 'abc', empty) — отсутствует Value Object; 5) public setter-подобное поведение через метод, но без проверки; 6) сущность смешивает identity + поведение + персистентность; 7) `flush()` внутри метода означает, что `changeEmail` может быть вызвана в любом месте и «запишет в БД» — нарушает границу транзакции, непредсказуемо; 8) нет доменных событий; 9) нарушение SRP: User и «сохранение» — разные обязанности.

Что исправить: 
- Убрать EM из сущности.
- Сделать email Value Object (Email) с валидацией в конструкторе.
- `changeEmail` только мутирует состояние (или делает immutable замену).
- flush — в use case / application layer, после всех изменений.
- Сущность без внешних зависимостей — чистый PHP, тестируется без EM.
- Инварианты: email валиден, unique (на уровне БД/агрегата, не в сущности).
- Добавить доменные события при необходимости.
- Отделить identity (id) от поведения.

Структура ответа: сначала список «что не так» с группировкой (DDD, чистая архитектура, практические последствия), потом исправленный код (Email VO, User entity без EM, use case с flush), потом объяснение почему так — связь с правилами (DIP, граница транзакции, SRP, VO), антипаттерны/последствия (тестирование, aтомарность, утечка ORM), и что останется на уровне БД (unique constraint).

Отвечу по-русски, структурированно, с кодом. Не буду перегружать — сфокусируюсь на ключевых проблемах и чистом исправлении. Использую заголовки и таблицы умеренно. Язык ответа — русский.

Начну с краткой оценки, затем разбивка на проблемы по слоям, затем переделанный код с пояснением каждого решения, затем последствия/чеклист.

</details>

**Кандидат:**

## Что не так

Три самостоятельных греха, каждый из которых ломает свою часть архитектуры. Разберу по слоям.

### 1. `EntityManager` в сущности — утечка инфраструктуры в домен

```php
public function __construct(
    private EntityManagerInterface $em,   // ← адаптер в домене
    ...
) {}
```

Это нарушение **DIP** и инкапсуляции домена. Сущность «знает», что её кто-то персистит, и «знает» как (через `EntityManager`). Последствия:

- **Нельзя создать `User` в юнит-тесте** без живого `EntityManager` и БД. Доменная логика (`changeEmail`) становится недоступна для изолированного теста.
- **Сущность зависит от реализации.** Поменяете ORM — переписываете домен.
- **Нарушается правило зависимостей.** Стрелка «домен → инфраструктура» направлена наружу, а должна внутрь.

Сущность должна быть **чистым PHP** без внешних зависимостей.

### 2. `flush()` внутри метода — сущность управляет транзакцией

```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();   // ← сущность решает, когда писать в БД
}
```

Это худшая из трёх проблем. `flush()` — это **граница транзакции**, а граница транзакции решается **на уровне use case / application layer**, а не в сущности.

Последствия:

- **Потеря атомарности.** Если `changeEmail` вызывается в середине более крупного сценария («сменить email + отправить подтверждение + удалить старый»), каждый `flush()` — отдельный момент записи. Упало между ними → частичное состояние.
- **Непредсказуемое поведение.** `changeEmail()` теперь «магия»: она не просто меняет email, а **записывает в БД**. Вызывающий код это не ожидает.
- **Нельзя откатить.** `flush()` уже отправил SQL в БД. Транзакционный откат на уровне use case его не спасёт.
- **Нарушение контроля цикла.** Решать «когда commit» должен владелец сценария, а не сущность.

Правило: **сущность мутирует состояние, use case решает, когда персистить и когда commit'ить.**

### 3. Email без валидации и без Value Object

```php
public function changeEmail(string $email): void
{
    $this->email = $email;   // ← 'abc', '', 'a@b' — всё принимается
}
```

Здесь нет **инварианта** «email валиден». Это нарушение сути доменной сущности: инварианты должны быть **невозможны к нарушению**. Сейчас нарушить их можно в любой точке кода.

Правильно — **Value Object** с проверкой в конструкторе: невалидный email **не может быть создан**.

---

### Мелкие, но значимые

| Проблема | Почему |
|----------|--------|
| Нет `id` (identity) | Сущность без identity — не сущность. Где `getId()`? |
| `changeEmail` — единственный мутатор, но нет state/статуса | Для простого случая ок, но не показан lifecycle |
| Нет доменного события | «Email изменён» — факт, который другие части могут отреагировать |
| Public-свойство через метод, но без immutable-семантики | Мутация вместо замены |
| `SELECT *`-стиль «всё в одном» | SRP: валидация, изменение, персистентность — три обязанности |

---

## Как исправляю

### Шаг 1. Вынос Email в Value Object

```php
// src/Domain/User/Email.php
final readonly class Email
{
    public function __construct(public string $address)
    {
        if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
            throw new \DomainException("Invalid email: {$address}");
        }
        if (strlen($address) > 255) {
            throw new \DomainException('Email too long');
        }
    }

    public function equals(self $other): bool
    {
        return $this->address === $other->address;
    }
}
```

**Что дал:** невалидный email **невозможен**. `new Email('abc')` — исключение в момент создания, а не «когда-нибудь в БД». Это инвариант, защищённый на языке.

### Шаг 2. Сущность — чистый PHP, без EM

```php
// src/Domain/User/User.php
final class User
{
    private ?int $id = null;
    private Email $email;

    /** @var object[] Накопленные доменные события */
    private array $collectedEvents = [];

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function changeEmail(Email $newEmail): void
    {
        if ($newEmail->equals($this->email)) {
            return; // идемпотентно, без мутации
        }

        $this->email = $newEmail;

        $this->collectedEvents[] = new UserEmailChanged(
            userId: $this->id,
            newEmail: $newEmail,
            at: new \DateTimeImmutable(),
        );
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function releaseEvents(): array
    {
        $events = $this->collectedEvents;
        $this->collectedEvents = [];
        return $events;
    }
}
```

**Что изменилось:**
- **Нет `EntityManager`** в конструкторе. Сущность — чистый PHP.
- **Нет `flush()`** в методах. `changeEmail` только мутирует состояние и накапливает событие.
- **Email — Value Object**, не `string`. Невалидный email невозможен.
- **Событие накапливается**, а не публикуется (паттерн Collected Events).
- **Identity** (`id`) отделено от поведения.

### Шаг 3. Персистентность — в application layer

```php
// src/Application/User/UseCases/ChangeEmailUseCase.php
final class ChangeEmailUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private EventPublisherInterface $events,
    ) {}

    public function execute(ChangeEmailCommand $cmd): void
    {
        // 1. Загрузка агрегата
        $user = $this->users->findById($cmd->userId)
            ?? throw new UserNotFoundException($cmd->userId);

        // 2. Доменная операция (чистая мутация, без БД)
        $user->changeEmail(Email::fromString($cmd->newEmail));

        // 3. Персистентность (репозиторий — порт, не EM напрямую)
        $this->users->save($user);

        // 4. Публикация событий (через порт)
        foreach ($user->releaseEvents() as $event) {
            $this->events->publish($event);
        }
    }
}
```

**Ключевое:** `flush()` здесь — на уровне use case, а не в сущности. Граница транзакции управляется тем, кто **владеет сценарием**.

```php
// src/Interface/Http/UserController.php
#[Route('/api/v1/users/{id}/email', methods: ['POST'])]
public function changeEmail(int $id, Request $req): Response
{
    $cmd = new ChangeEmailCommand(
        userId: $id,
        newEmail: $req->request->get('email') ?? '',
    );

    try {
        $this->changeEmailUseCase->execute($cmd);
        return new Response('', 204);
    } catch (InvalidEmailException $e) {
        return new JsonResponse(['error' => 'invalid_email'], 422);
    } catch (UserNotFoundException $e) {
        return new JsonResponse(['error' => 'not_found'], 404);
    }
}
```

---

## Почему именно так

| Решение | Нарушение, которое лечит |
|---------|--------------------------|
| Email как VO | Инвариант «email валиден» невозможно нарушить |
| Убрать EM из сущности | DIP: домен не зависит от ORM; тестируемость без БД |
| Убрать `flush()` из сущности | Граница транзакции — у use case, атомарность под контролем |
| Collected events | События не публикуются из домена, а накапливаются и выпускаются на границе |
| Репозиторий — порт | Сущность не знает, что за ней стоит Doctrine |

### Что **осталось** на уровне БД

Инварианты, которые **нельзя** проверить в сущности (зависят от контекста всей таблицы), уходят в PostgreSQL:

```sql
-- Уникальность email (нельзя проверить в сущности — она не знает про других)
ALTER TABLE users ADD CONSTRAINT uq_users_email UNIQUE (email);

-- Формат (защита от ручного SQL)
ALTER TABLE users ADD CONSTRAINT chk_users_email
    CHECK (email ~* '^[^@]+@[^@]+\.[^@]+$');
```

**Принцип:** инварианты **сущности** — в домене. Инварианты **данных** (unique, not null, cross-row) — в БД. Ничто не дублируется.

---

## Тесты становятся возможными

До (с EM и flush) — юнит-тест `changeEmail` требует живого `EntityManager` и БД.

После — чистый юнит-тест:

```php
public function test_change_email_validates(): void
{
    $this->expectException(\DomainException::class);

    new Email('not-an-email');
}

public function test_change_email_accumulates_event(): void
{
    $user = new User(new Email('old@x.com'));
    $user->changeEmail(new Email('new@x.com'));

    $events = $user->releaseEvents();
    $this->assertCount(1, $events);
    $this->assertInstanceOf(UserEmailChanged::class, $events[0]);
    $this->assertSame('new@x.com', $user->email()->address);
}

public function test_change_email_is_idempotent(): void
{
    $user = new User(new Email('a@x.com'));
    $user->changeEmail(new Email('a@x.com'));

    $this->assertSame(0, count($user->releaseEvents()));
}
```

Никаких EM, БД, mock'ов. Это то, что даёт разделение.

---

## Чеклист: что проверить, если вижу похожий код

```
□ В конструкторе сущности — EntityManager / Connection / Logger?
  → Убрать. Это адаптер, не домен.

□ Есть flush() / save() в методе сущности?
  → Убрать. Граница транзакции — у use case.

□ Email/Phone/Money — string/int, а не VO?
  → Завернуть в VO с валидацией в конструкторе.

□ Инвариант можно нарушить извне?
  → Добавить проверку в конструктор / мутатор.

□ Сущность публикует событие / шлёт письмо / пишет лог?
  → Убрать. Накапливать, выпускать на границе.

□ Можно ли создать сущность без БД?
  → Если нет — домен загрязнён инфраструктурой.
```

---

### Итог

Сущность должна отвечать на один вопрос: **«каковы правила этого понятия?»** — и **только** на него.

«Как я сохраняюсь», «куда я пишу лог», «как я отправляю письмо» — это вопросы **адаптеров**, а не домена. Как только сущность начинает на них отвечать — домен и инфраструктура перемешиваются, и тестировать, менять, масштабировать становится невозможно.

> ⏱ 44.5s · prompt 121785 tok · output 3519 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

The user is asking me (as a candidate in this interview) to describe my process for tackling a technical problem in an unfamiliar technology or stack — from first look to confidently estimating a timeline. And when to ask for help vs. figure it out alone.

This is a process/mindset question, not a code question. They want to see how I think, how I learn, how I manage risk, and how I collaborate.

Let me think about what a strong answer looks like here. The weak answer is "I read the docs, then I google, then I write code." That's what everyone does. A strong answer shows:

1. A structured approach to learning something new under time pressure.
2. Risk awareness — knowing when the cost of going alone exceeds the cost of asking.
3. Honest calibration of my own competence (what I can do vs. what I'd delegate).
4. A concrete mental model for estimating timelines on unknowns.
5. Knowing the difference between "I don't know this library" (solvable alone) and "I don't know if this approach is right" (needs a team).

Let me structure this. I should be concrete, not abstract. I'll give a real sequence with decision points. I'll use a hypothetical to make it tangible but keep it general enough.

Key principles I want to convey:
- First: understand the PROBLEM, not the technology. The tech is a means, the problem is the end.
- Triage by "blast radius" — how much does it matter if I get this wrong?
- The 2-hour rule / 30-min rule for when to escalate.
- Learning in layers: concept → API → idioms → edge cases.
- Estimating honestly: I don't estimate "I'll do X in 2 days." I estimate with confidence intervals and flag the unknowns.
- The difference between "unknown implementation" (I can solve) and "unknown correctness" (I need a team).
- Not being a hero. Asking early is cheaper than asking late.

Let me think about the structure of the answer:
1. Reframe: the technology is not the problem.
2. Phase 1: understand the problem (what, why, constraints).
3. Phase 2: assess my distance from the tech (how unknown is it really?).
4. Phase 3: the decision fork — solo vs. help, with concrete criteria.
5. Phase 4: if solo, how I learn (layered approach, spike).
6. Phase 5: estimating — how I actually produce a timeline with confidence.
7. When to ask for help — the real criteria, not "when I'm stuck."
8. Concrete example to ground it.
9. The trap: overconfidence and under-asking.

I want to be honest and not performative. The best answer admits I'd sometimes get it wrong and here's how I de-risk that.

Let me think about the "when to ask for help" part carefully because that's the most revealing part of the question. A naive answer: "when I can't figure it out after X hours." A better answer distinguishes:
- Ask EARLY when: high blast radius, irreversible, architectural, security, or I have a hunch the approach is wrong.
- Go ALONE when: low blast radius, reversible, it's a "how do I do X" question (mechanics, not strategy), I can build a throwaway spike.

The key insight: it's not about how hard it is, it's about (a) blast radius / irreversibility and (b) whether the risk is "I'll be slow" (fine alone) vs. "I'll be wrong" (ask).

Let me also address the timeline part honestly. The trap is people who estimate confidently because they want to look competent. The honest approach: I give a range, I name the unknowns inside it, and I make the estimate conditional ("if X turns out to be Y, it's 2 days; if Z, it's 5"). And I timebox the investigation before committing.

I'll write this in Russian to match the conversation. I should keep it structured but not over-bulletized into unreadable fragments — mix prose and structure. Given the length of previous answers in this conversation, a thorough answer is expected, but I should make it dense and practical, not padded.

Let me draft the flow:

1. Reframe first (short).
2. A concrete sequence with phases.
3. The decision criteria for help (a clear table or list).
4. How I estimate.
5. The honest caveat.

I'll avoid making it too long. Let me write it.

</details>

**Кандидат:**

## Сначала — важная развилка, которую пропускать нельзя

Технология — это **не задача**. Это **средство**. Если я начну с «как тут работает X», я потрачу время на то, что, возможно, вообще не надо. Я начинаю с вопроса: *какую проблему я решаю и какой объём решения допустим* — а не с того, что мне незнаком в стеке.

Это меняет всё: часто «незнакомый стека» оказывается «мне нужно сделать одну конкретную вещь», и 80% неизвестности просто не релевантны.

---

## Последовательность

### Шаг 1. Сформулировать задачу, а не технологию (30 мин)

Прежде чем открыть документацию:

- **Что** сломано / чего не хватает? (факт, не гипотеза)
- **Каков blast radius**? Что пострадает, если я ошибусь — данные, деньги, другие сервисы, или «просто медленно»?
- **Какие допустимые пределы**: срок, бюджет на инфраструктуру, можно ли менять схему / API?
- **Есть ли «временное» решение**, которое закрывает боль, пока я разбираюсь? (часто есть, и это меняет приоритет)

Если после этого я вижу, что задача на самом деле маленькая и локальная — «незнакомый стек» перестаёт быть проблемой.

### Шаг 2. Измерить свою дистанцию до технологии (15 мин)

Я честно спрашиваю себя: **что именно я не знаю**, и разделяю на три категории:

| Категория | Пример | Риск |
|-----------|--------|------|
| **Механика** — «как вызвать этот API» | синтаксис, конфиг, команды | Низкий. Читабельно в доках |
| **Идиоматика** — «как здесь принято решать» | паттерны, лучшие практики | Средний. Ошибка будет «рабочим, но чужим» кодом |
| **Корректность** — «а правильно ли я вообще иду» | архитектура, границы, безопасность | Высокий. Ошибка = неверное решение |

Это ключевой момент. Большинство «незнакомых задач» — это категория 1 (механика), и её я закрываю сам. Категории 2–3 — зона, где я осторожнее.

### Шаг 3. Время на исследование до обязательства (timebox)

Я ставлю **лимит на исследование** — обычно 2–4 часа максимум — **до того** как даю сроки. За это время:

1. Читаю официальные доки и **одну** надёжную статью (не тридцать).
2. Пишу **spike** — 50–100 строк мусорного кода, которые доказывают, что подход вообще работает в этом стеке. Не «чистое решение», а «а оно в принципе так устроено».
3. Проверяю крайние случаи, которые я подозреваю (ошибки, конкурентность, память).

Если spike работает — я знаю **механику**. Если нет — я уже знаю, где именно мне нужна помощь, и это **конкретный** вопрос, а не «помогите, я не понимаю стек».

После этого я готов дать срок. **До** этого срока я не даю — потому что давал бы на веру.

---

## Как я даю сроки на неизвестное

Я никогда не даю одно число. Я даю **диапазон + условия + неизвестные внутри**:

> «Если [предположение А] подтвердится — 2 дня. Если [предположение Б] — 5 дней.
> Сейчас я уверен в механике (spike работает), не уверен в корректности на большом объёме данных — это и есть разброс.
> Предлагаю: завтра я закрою вопрос с объёмом, и тогда даю твёрдый срок.»

Почему так:

- **Диапазон**, а не точка — честная калибровка. Точка на неизвестном = ложная уверенность.
- **Условия** — я называю, от чего зависит срок. Это и есть моя оценка рисков.
- **Твёрдый срок после закрытия ключевого неизвестного** — я не гоняюсь за точностью, я **уменьшаю неизвестность**, а потом даю точность.

Этого не хватает большинству: они дают «3 дня» и молчат. Я даю «2–5 дней, вот от чего зависит, и вот что я закрою, чтобы сузить».

---

## Когда разбираться самому, а когда звать команду

Критерий **не** «насколько это сложно» и не «насколько я устал». Критерий — **blast radius × необратимость**.

| Ситуация | Решение | Почему |
|----------|---------|--------|
| Механика, локально, обратимо | **Сам** | Дорого звать, дёшево ошибиться и поправить |
| «Как вызвать X» в доках | **Сам** | Это и есть исследование, не помощь |
| Не знаю **правильный подход** (категория корректности) | **Спросить рано** | Мое решение может быть «рабочим, но архитектурно чужим» |
| High blast radius (данные, деньги, безопасность) | **Спросить рано** | Ошибка дорого стоит. Час консультации дешевле, чем день отката |
| Необратимое (миграция, удаление, деплой) | **Спросить рано** | Нет права на «ну давай попробуем» |
| У меня есть hunch, что иду не туда | **Спросить рано** | Hunch — это сигнал, а не каприз |
| Низкий риск, я в механике, есть spike | **Сам, потом показать** | Дешевле прийти с работающим прототипом и вопросом, чем с пустотой |

### Два правила, которые я в себе повторяю

**1. Просить рано дешевле, чем просить поздно.**
Чем больше я вложу в «своё решение», тем труднее его бросить и тем дороже ошибка. Если blast radius высокий — спрашиваю **до** того, как вложусь, а не после.

**2. Приходить с прототипом, а не с пустотой.**
«Помогите, я не понимаю стек» — это дорого для команды. «Я сделал spike, вот что работает, вот где я застрял, вот что я боюсь сломать» — это легко для команды и быстро. Я не стыжусь показать, что разбираюсь сам: это **поднимает** качество вопроса, а не роняет меня в глазах команды.

---

## Честный риск в этом процессе

Где я могу ошибиться — это **оценка своей дистанции**. Я могу решить, что задача «механика» (категория 1), а она на самом деле «корректность» (категория 3). Именно поэтому:

- **timebox** на исследование — защита от того, что я буду уверенно копать в неверном направлении.
- **Называть условия в сроке** — если моё предположение неверно, срок «раскроется» сам, и я это увижу.
- **Показывать spike команде** — даже если я сам, я привожу прототип на ревью. Это и есть точка, где команда ловит мою неверную калибровку.

То есть моя система не «я решаю сам или зову и точка». Она «я решаю в ограниченном бюджете, называю свои допущения, и даю команде точку, где они поймают меня, если я неправ».

---

## Коротко

```
1. Понять ПРОБЛЕМУ, не технологию. Blast radius + допустимые пределы.
2. Измерить дистанцию: механика / идиоматика / корректность.
3. Timebox на исследование (2–4ч) + spike, ДО того как давать срок.
4. Срок = диапазон + условия + что я закрою, чтобы сузить.
5. Сам, если: механика + локально + обратимо.
6. Звать рано, если: корректность / высокий blast radius / необратимо / есть hunch.
7. Приходить с прототипом, не с пустотой.
8. Дать команде точку, где они поймают мою неверную калибровку.
```

Главный принцип: **я не измеряю задачу по тому, сколько мне не знакомо. Я измеряю её по тому, сколько стоит, если я ошибусь, и по тому, насколько я могу это откатить.** Незнакомый стек — это нормально. Незнакомый стек с высоким blast radius и без точки проверки — это уже не задача, это риск, и с ним я не иду один.

> ⏱ 48.0s · prompt 124732 tok · output 3031 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос из серии собеседования на PHP-бэкенд. Ситуация: я пообещал задачу к завтрашнему дню, но по ходу работы понял, что это два дня. Что делаю?

Это вопрос про коммуникацию, честность, управление ожиданиями. Нужно ответить как опытный инженер, который понимает, что проблема не в "задержке", а в доверии и предсказуемости.

Ключевые аспекты, которые я должен раскрыть:
1. Момент обнаружения — когда именно я понимаю, что не уложусь. Это критично: "по ходу" может быть и через час, и за 20 минут до дедлайна.
2. Что я НЕ делаю — это тишина, "ну попробую", "почти готово", молчаливое опоздание.
3. ЧТО делаю — кому, когда, в каком формате.
4. Почему именно так — логика, а не морализирование.
5. Разные варианты: что если я не могу дать точный срок, что если нужно помощь, что если часть сделана.

Я должен ответить в том же стиле, что и предыдущие ответы — структурированно, с конкретикой, с таблицами, с "почему", без морализаторства. Без воды.

Структура:
- Главный принцип (1 фраза)
- Что НЕ делаю (антипаттерны)
- Когда именно говорю (момент обнаружения)
- Как говорю (кому, в каком формате, с чем)
- Почему именно так (логика)
- Варианты: что если есть часть работы, что если нужна помощь, что если я не знаю точный срок
- Честный риск — где я могу ошибиться

Важно: не отвечать абстрактно "нужно честно сообщить". Нужно дать конкретные формулировки, конкретные шаги, конкретную логику.

Разделю на:
1. Главный принцип
2. Момент, когда я это понимаю (это важно — "по ходу" — это когда именно?)
3. Что НЕ делаю
4. Как говорю — конкретные формулировки
5. Почему именно так
6. Варианты сценариев
7. Честный риск

Буду писать по-русски, так как весь диалог на русском.

</details>

**Кандидат:**

## Главный принцип

> **Опоздание — это не "я не успел". Опоздание — это "я сказал правду слишком поздно".**

Задержка на день, о которой я молчу 20 часов, — это не "задержка на день". Это "я не предсказуем". И второе стоит дороже, чем первое.

---

## Момент, когда я это понимаю

"По ходу работы понял" — это не момент. Момент — это **точка, где я перестаю верить в свой срок**.

Я задаю себе один вопрос, и он объективный:

> *«Сейчас, прямо сейчас, с тем, что я уже знаю, я могу закончить к завтрашнему дню — да или нет?»*

- **Да** → продолжаю молчать, работаю.
- **Нет** → я говорю. Сейчас. Не вечером. Не "после того как ещё попробую".

Опасность в том, что "по ходу" часто означает: *я ещё два часа "попробую", вдруг успею*. Это не работа. Это лотерея, в которую я ставлю чужое доверие. Через два часа я, скорее всего, не успею — и скажу это через два часа позже. А мог сказать сейчас.

**Правило:** как только мой честный ответ на вопрос выше — "нет", я говорю. Не "может быть", не "пока не знаю". "Нет".

---

## Что я НЕ делаю

| ❌ | Почему |
|----|--------|
| Молчу и гоню до дедлайна | 20 часов чужого ожидания, которое я не верну. Максимальный ущерб доверию |
| "Почти готово, ещё немного" | Это не информация. Это отсрочка. Человек не может на ней планировать |
| Скажу в последний момент, за час | Я "спасаю лицо" — но для всех, кто планировал на завтра, уже поздно |
| Сам дорабатываю в нерабочее время молча | Я "решаю" проблему, но человек не знает, что она была. Следующий раз он снова не верит моему сроку |
| Перефразирую: "я пересматриваю приоритеты" | Обход. Я знаю, что это опоздание. Назову его опозданием |

---

## Как говорю

### Кому

По цепочке, которая зависит от масштаба:

- **Мой прямой руководитель / тимлид** — всегда. Первый.
- **Владелец задачи / заказчик** — через тимлида или напрямую, если я с ним работаю.
- **Коллеги, которые ждут мой результат** — если их работа стоит на моей. Это важно: если после меня кто-то должен делать шаг, он должен узнать **до** того, как начал планировать свой.

### В каком формате

Не "извините, не успеваю". Это жалоба на самого себя. Вместо — **информация + новый срок + причина + что уже сделано**:

> «Раньше я оценил задачу в 1 день. По ходу работы выяснилось [конкретная причина] — это добавляет день. Новый срок: послезавтра.
>
> Уже готово: [X]. Осталось: [Y].
>
> Причину могу разобрать на 10 минутах, если надо. Если критично к завтрашнему дню — могу сдать [часть A] завтра, [часть B] послезавтра. Скажи, что выбираем.»

Что в этой формулировке:

| Элемент | Зачем |
|---------|-------|
| «Я оценил в 1 день» | Я не прячусь. Это моя оценка, я её давал |
| «Выяснилось [конкретно]» | Не "что-то пошло не так". Конкретная причина — иначе это звучит как отмазка |
| «Новый срок: послезавтра» | Я даю новый срок, а не "скоро". Человек может планировать |
| «Уже готово / осталось» | Человек видит прогресс и объём остатка. Не чёрный ящик |
| «Могу разобрать за 10 минут» | Я не заставляю слушать, но даю доступ. Это уважение к времени другого |
| «Могу сдать часть A завтра» | Я не просто "опоздаю". Я даю опцию. Человек решает, что для него важнее |

### Почему "часть A / часть B" — это ключ

Это меняет разговор с «я опоздал» на «давай решим, что важнее». У человека появляется **выбор**. А у человека с выбором нет ощущения, что его обманули. Он сам решил, что часть B подождёт.

Это не манипуляция. Это то, что я и так должен был сделать при оценке: разложить задачу на части и назвать, какая из них критична.

---

## Почему именно так (логика, не мораль)

| Причина | Последствие, если молчать |
|---------|---------------------------|
| **Планирование.** На мой результат кто-то строит свой план | 20 часов он строил план на "завтра". Теперь весь план сдвигается |
| **Доверие.** Срок = обещание | Одно сломанное обещание, о котором узнали поздно, бьёт сильнее, чем три обещания, о которых предупредили |
| **Ресурс.** Пока я молча гоню, я могу быть полезен иначе | Если задача не критична, мои 20 часов могли быть на другой, более важной задаче |
| **Качество.** Спешка к дедлайну = меньше тестов, меньше ревью | "Успеваю" к вечеру без ревью = баг в проде. Опоздание с предупреждением = качество |

Главное: **молчание не экономит время. Оно переносит стоимость с "разговора сейчас" на "хаос потом".**

---

## Разные сценарии

### 1. Я понимаю, что не уложусь, но часть готова

> «Завтра могу отдать [часть A] — она готова на 90%. [Часть B] сдвигается на день из-за [причина]. Если [часть A] закрывает основную боль — её можно уже завтра. Скажи, как делаем.»

Это лучший сценарий. У человека есть что-то реальное к дедлайну.

### 2. Я понимаю, что задача сложнее, чем думал, и не знаю точный срок

> «Оценил в 1 день, но [конкретный аспект] оказался сложнее, чем я ожидал. Честно: сейчас не могу дать точный срок. Предлагаю: завтра я закрою [конкретный вопрос], и тогда дам срок. Если критично — давай поговорим, может, упростим scope.»

Я не выдаю ложную точность. "2 дня" без уверенности = новое обещание, которое я тоже могу сломать. Лучше "не знаю, но закрою неизвестное завтра".

### 3. Проблема в том, что мне нужна помощь

> «Уперся в [конкретная штука]. Сам разберусь, но это добавит день. Если [коллега X] посветит 30 минут — я закрою завтра по плану. Если помощь недоступна — у меня послезавтра. Что выбираем?»

Я не жалуюсь. Я называю **точку, где мне нужен ресурс**, и даю **двусти**: с ресурсом — по плану, без — сдвиг. Решение — за человеком.

### 4. Я ошибся в оценке, и это не "объективная сложность"

> «Я неправильно оценил. Подумал, что [X] будет просто, а оно [конкретика]. Это моя оценка, не непредвиденное. Новый срок: послезавтра. В следующий раз такую задачу оцениваю иначе — [что я вынес].»

Если ошибка моя — я её называю моей. Не "оказалось", не "выяснилось" (это звучит как внешняя сила). "Я ошибся в оценке" — это честно и это учит меня.

---

## Честный риск в этом

Где я могу прогадать — это **порог, когда я говорю**.

- Скажу слишком рано ("а, может, я ещё успею") — и через час окажется, что я зря поднял тревогу. Мелкий минус к доверию ("опять он нервничает").
- Скажу слишком поздно ("ну ладно, ещё попробую") — и через 5 часов будет поздно. Большой минус к доверию.

Баланс я держу так: **в сомнении — говорю раньше.** Ложная тревога дешевле, чем опоздание, о котором узнали поздно. Человек, которому я сказал "может не успею" и я всё-таки успел, — это плюс к доверию, а не минус. А человек, которому я промолчал и не успел — это минус, который не вернёшь.

---

## Коротко

```
1. Момент = точка, где я честно отвечаю "нет" на "успею к сроку".
2. Не откладываю "ещё попробую". Это лотерея на чужом доверии.
3. Говорю сразу: новому сроку, причине, что готово, что осталось.
4. Даю опцию: "часть A могу к дедлайну, часть B сдвигается".
5. Если ошибка моя — называю её моей.
6. В сомнении — говорю раньше. Ложная тревога < опоздание-тихоня.
```

Опоздание на день — нормальная часть работы. Ненормально — опоздание, о котором узнают после. И вот это я контролирую полностью: **я выбираю, когда сказать.**

> ⏱ 44.4s · prompt 126800 tok · output 2807 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о балансе качества и скорости — когда код «достаточно хорош», а когда стоит продолжать улучшать. Это вопрос не про конкретную технологию, а про инженерное суждение и принятие решений.

Что я хочу избежать:
- Дать абстрактный список «принципов качества» без привязки к реальности.
- Звучать как будто есть универсальный порог «достаточно хорошо».
- Писать в стиле учебника/лекции.
- Дать ответ, который нельзя применить к конкретной ситуации.

Что важно в ответе:
1. «Достаточно хорош» — это не свойство кода, а свойство **ситуации**. Качество — это функция от контекста (что это за код, кто будет читать, как долго будет жить, что произойдёт, если сломается).
2. Реальные критерии, по которым я останавливаюсь, а не абстрактные.
3. Разные типы кода требуют разного «достаточно».
4. Баланс — это не компромисс «на 70%», а осознанное решение с явной ценой.
5. Честность про то, где я сам могу прогадать.

Структура, которую я выберу:
- Начну с главного: «достаточно хорош» — это не порог качества, а функция от контекста. Это перевернёт ожидаемую рамку «сколько качества нужно».
- Дам модель решения: какие вопросы я задаю, и как ответ на них определяет глубину.
- Разные типы кода — разный «достаточно» (таблица).
- Конкретные сигналы «стоп, хватит» и «ещё можно/нужно улучшить».
- Как я фиксировал баланс в реальном проекте (пример).
- Честный риск: где я могу прогадать в обе стороны.
- Короткий итог.

Стиль: продолжаю в том же тоне, что и предыдущие ответы — прямой, с конкретикой, без воды, с таблицами и примерами. Не использую markdown-хедеры уровня 1, начинаю с хедера уровня 2 или просто с текста. Предыдущие ответы использовали ## для разделов. Соблюдаю это.

Не буду переусложнять. Ответ должен быть самодостаточным. Пишу на русском.

</details>

**Кандидат:**

## «Достаточно хорош» — это не порог качества, а функция от контекста

Первое, что я себе говорю: **не существует уровня качества, при котором код «стоп-достаточно»**. Это ловушка, потому что она предполагает, что я ищу некое число на шкале от 0 до 100 и останавливаюсь на 70.

На деле качество — это **функция от того, что произойдёт с кодом дальше**. Тот же код, написанный «на 70%», в одной ситуации — правильное решение, в другой — ошибка. Разница не в коде, а в контексте.

Поэтому я не спрашиваю «хватит ли качества». Я спрашиваю **вопросы, которые определяют, сколько качества нужно**.

---

## Вопросы, которые я задаю

### 1. Сколько жизни у этого кода?

| Ожидание жизни | Что значит для качества |
|----------------|------------------------|
| Часы–дни (PoC, миграция, «пробуем») | Минимум. Работает + не опасен. Не чищу, не абстрагирую |
| Месяцы (фича в проде) | Нормальное качество. Тесты, читаемость, границы |
| Годы+ (ядро, платёжка, домен) | Высокое качество. Инварианты, тесты на всё, архитектура |

Парадокс: **код, который живёт меньше всего, часто требует больше усилий на «достаточно»**, потому что его быстро удалят, и усилия на качество не окупятся. А код, который живёт годами, — наоборот, каждая вложенная минута в качество окупается многократно.

Ошибка, которую я ловлю у себя: тратить время на «красивый» код для PoC (который удалят через неделю) и экономить на коде, который переживёт меня.

### 2. Что произойдёт, если это сломается?

Это blast radius, который я уже использовал для выбора «сам / звать команду». Он же определяет качество:

- **Сломался → потеряли данные / деньги / безопасность** → качество максимальное. Тесты на крайние случаи, ревью, код-ревью ×2, строгие инварианты.
- **Сломался → пользователь увидел ошибку, перепрочитает** → нормальное качество.
- **Сломалось → скрипт не отработал, перезапущим** → минимум.

Правило: **чем дороже ошибка, тем больше я трачу на то, чтобы её не было.** Это не «паранойя», это арифметика. Минута на тест, который ловит потерю денег, окупает себя первым срабатыванием.

### 3. Кто будет читать/менять это после меня?

- **Только я, разово** → могу оставить «грязное» с комментариями для себя.
- **Команда, регулярно** → читаемость, именование, границы. Это не «люкс», а то, что сэкономит им часы.
- **Через полгода пришёл новичок** → документация, явные контракты, тесты как спецификация.

Частая ошибка: писать код «для себя» там, где через месяц будет чужой. Я заранее спрашиваю: «кто это читает и когда».

### 4. Насколько это меняется в будущем?

- **Будет меняться каждый спринт** → вкладываю в расширяемость (OCP, чистые границы), потому что каждый следующий человек заплатит за мою лень.
- **Заморожено** → не вкладываю в абстракции. «Мёртвый» код не нуждается в расширяемости.

Здесь я честно признаю: **расширяемость — это упреждение будущего, а не качество «сейчас»**. Вкладывать в неё стоит только если я реально верю, что код изменится. Иначе это преждевременная оптимизация, только в области архитектуры.

---

## Разные типы кода — разный «достаточно»

| Тип кода | «Достаточно» = |
|----------|----------------|
| **Домен, инварианты** | Максимум. Тесты на каждое правило, VO, чистые границы. Это сердце, оно живёт долго |
| **Платёжки, деньги** | Максимум. Атомарность, идемпотентность, тесты на конкурентность |
| **CRUD, фича** | Нормально. Тесты на сценарии, читаемость, ревью |
| **Интеграция с внешним API** | Нормально + фокус на обработка ошибок и retry |
| **PoC, скрипт, миграция** | Минимум. Работает + не опасен + понятен, что это временное |
| **UI-мелочи, конфиг** | Минимум. Не стоит тратить время на абстракции |

Если я вижу, что трачу одинаково много усилий на «домен денег» и на «скрипт-миграцию» — я не балансирую, я просто одинаково тратю. А надо — **неодинаково**, по важности.

---

## Конкретные сигналы «стоп, хватит»

Это не абстракции, а то, на что я реально смотрю:

**Хватит, когда:**
- [ ] Код **читается** без объяснения (я или другой понимает, что делает, за один проход).
- [ ] **Тесты покрывают сценарии из acceptance**, не «строки кода».
- [ ] **Краевые случаи**, которые я назвал в задаче, обработаны.
- [ ] **Нет «висяков»**: ничего не half-done, ничего «ну это потом доделаю» без тикета.
- [ ] **Ревью прошло** и blocker'ы закрыты.
- [ ] **Я могу объяснить за 2 минуты**, что я сделал и почему. (Если не могу — я не понимаю свой код.)
- [ ] **Я не знаю, что улучшить** дальше. (Если знаю — см. ниже.)

**Продолжать, когда:**
- [ ] Я вижу **конкретный** риск, который не покрывается (конкурентность, краевой случай).
- [ ] Ревьюер нашёл **MAJOR**, а не NIT.
- [ ] Код **не читается** — я сам туплю, читая.
- [ ] Я **не уверен** в корректности (а «вроде работает» ≠ корректно).
- [ ] **Blast radius высокий**, а я экономил на тестах «потому что время».

---

## Ключевое разграничение: «улучшать» vs «полировать»

Это то, где я чаще всего прогаиваю, и я хочу быть честным.

**Улучшать** = закрыть конкретный риск или конкретную слабость. Улучшать стоит, пока есть **названная** проблема.

**Полировать** = сделать «красивее», «чище», «как я бы хотел». Полировать — это не качество, это **эстетика**. И она стоит время, которое я мог бы потратить на названные риски.

Момент истины: **если я не могу назвать, какой конкретный риск закрывает это улучшение — это полировка, и я останавливаюсь.**

Пример:
- «Перенесу 50 строк в отдельный класс, потому что метод длинный» — **улучшение** (читаемость, тестируемость).
- «Перенесу 50 строк в отдельный класс, потому что „так лучше архитектурно"» — **полировка**, если нет конкретной проблемы, которую это решает.

Оба варианта могут быть правдой. Но я обязан **назвать проблему**, иначе я не улучшаю код — я удовлетворяю свой эстетический вкус за счёт времени команды.

---

## Как я соблюдаю баланс в реальном проекте

### 1. Оценка на старте включает «сколько качества»

Когда я оцениваю задачу, я не даю просто «2 дня». Я даю:
> «2 дня на нормальное качество. +1 день, если нужно покрывать конкурентность и крайние случаи (высокий blast radius). -0.5 дня, если делаем минимум (PoC, временное).»

Так **уровень качества — это выбор**, а не то, что я «случайно» делаю. Человек видит цену каждого уровня.

### 2. Я фиксирую «не делаем» в тикете

В DoD я всегда есть блок **NE SCOPE** и **осознанно принятые риски**:
> «Не покрываем: конкурентную оплату двух разных счётов одновременно (blast radius низкий, исправим при первом сбое). Риск: двойное списание. Принято: [имя].»

Это **явный** баланс. Я не «случайно не покрыл», я **назвал** риск и получил согласие. Если через месяц этот риск сработает — я не виноват, потому что это было зафиксировано решение, а не моя лень.

### 3. Я не делаю «качество» в момент, когда нужно «скорость»

Когда P1 и горит, я делаю **минимально-корректное**, а не «красивое». Красивое — потом, в tech debt, с тикетом. В момент пожара качество = «не сломать хуже и починить», а не «рефакторить по пути».

Ошибаюсь, когда делаю наоборот: в момент P1 начинаю «заодно почистить архитектуру». Это и есть нарушение баланса — я вложиваю в качество там, где нужен speed.

### 4. Tech debt — не «потом», а «с тикетом и критерием»

«Почистим потом» — это ложь. «Почистим, тикет #1234, критерий: при X событии» — это план. Я всегда превращаю «потом» в **конкретный тикет с триггером**, иначе «потом» никогда не наступит.

---

## Честный риск: где я прогаиваю в обе стороны

| Направление | Мой провал | Как ловлю |
|-------------|-----------|-----------|
| **Слишком рано «достаточно»** | Экономлю на тестах/рисках ради скорости, и риск срабатывает | Blast radius high + осознанно не покрыл = ловлю на ревью и в тикете |
| **Слишком поздно «достаточно»** | Полирую, не называя проблему, и съедаю время | «Не могу назвать риск» = стоп. Ревьюер спрашивает «какую проблему решает?» |
| **Подмена** | Выдаю полировку за улучшение | Честно называю: «это улучшение риска X» vs «это мне нравится». Разное |

Самый честный маркер: **я могу назвать проблему, которую закрывает каждое улучшение.** Могу — это качество. Не могу — это полировка, и я останавливаюсь (или явно говорю: «я трачу время на эстетику, не на риск — это осознанно»).

---

## Коротко

```
1. «Достаточно» = функция от контекста, не порог.
2. Задаю 4 вопроса: жизнь / blast radius / кто читает / изменится ли.
3. Разные типы кода — разный «достаточно». Не одинаково.
4. Хватит, когда: читается, покрыты сценарии, нет висяков,
   могу объяснить за 2 минуты, не знаю что улучшить.
5. Улучшать = закрыть названный риск. Полировать = эстетика.
   Не могу назвать риск = стоп.
6. Уровень качества — это выбор с ценой, не «случайно».
7. «Потом» = тикет с триггером. Иначе «потом» не наступит.
8. В момент P1 — скорость, не красота. Красота — в tech debt с тикетом.
9. Ловлю себя: называю каждый риск, который закрывает улучшение.
```

Главное: **баланс качества и скорости — это не «на 70%». Это осознанное решение, где я называю цену каждого выбора и принимаю риски явно.** Если я не могу назвать, что я урезал и почему — я не балансирую, я просто работаю. А работа без осознанного выбора — это и есть, в конечном счёте, отсутствие баланса.

> ⏱ 52.3s · prompt 129138 tok · output 3414 tok

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня (кандидата) дать обратную связь по проведённому собеседованию. Это интересная роль — я должен оценить формат, выделить сильные и слабые стороны вопросов, предложить изменения. 

Что я знаю о проведённом собеседовании (из истории):
1. Знакомство + самооценка по шкале
2. PHP 8 фичи
3. Типы / strict_types / ловушки
4. Исключения
5. SOLID (SRP)
6. Можно ли нарушать SOLID
7. OCP без наследования
8. SRP только к классам?
9. DI-контейнер Symfony
10. Циклические зависимости
11. Асинхронные побочные эффекты
12. Контроль доступа
13. Value Object в Doctrine
14. Агрегат: хранение, границы транзакций
15. N+1
16. DB constraints
17. Оптимизация медленных запросов
18. Агрегат в DDD + границы
19. Конкурентность двух процессов
20. Доменные события + транзакции
21. VO vs Entity + UL
22. Чистая архитектура
23. Тестирование
24. Конкурентное изменение записи
25. Git-процесс
26. Код-ревью
27. Формулировка задачи из жалобы
28. Вопросы заказчику
29. Что делать при попутном баге / противоречии
30. Отчёт о выполнении
31. FORCE INDEX в PostgreSQL
32. strict_types замедление
33. Composite key в Doctrine
34. Рефакторинг кода
35. Sущность с EntityManager
36. Незнакомый стек
37. Опоздание с задачей
38. Баланс качества и скорости

Это очень длинное, плотное собеседование. Оно охватывает практически весь бэкенд-стек PHP. 

Мне нужно дать честную, конкретную обратную связь. Не льстить, не притворяться. Выделить:
- Формат: хорошо/плохо
- Полезные и точные вопросы
- Неудачные, неполные, вводящие в заблуждение
- Что бы я изменил

Что я реально замечаю как кандидат:

Сильные стороны:
- Плотность и глубина. Это не «назови 5 принципов SOLID», а «покажи на примере нарушение и рефакторинг». Это ценно — отличает реальный опыт от зубрёжки.
- Вопросы-ловушки: "FORCE INDEX в PostgreSQL" — это ловушка, и я её поймал. "strict_types замедляет на N%" — тоже ловушка. Это хорошо, потому что проверяет, не отвечаю ли я по шаблону.
- Повторяющиеся темы с разных сторон (SOLID → нарушение → OCP без наследования → SRP не только к классам). Это проверяет глубину, а не ширину.
- Вопросы с "почему так, а не иначе" (git-процесс). Это отличает реального практика от теоретика.

Слабые стороны / проблемы:
- Объём. Это 35+ вопросов. Для собеседования это очень много. Если это одно интервью — это выматывающе, и к концу качество ответов падает (у меня и у интервьюера). 
- Нет обратной связи от интервьюера в процессе. Я отвечаю, но не знаю, правильно ли я понял вопрос. В реальном собеседовании я бы хотел уточнений.
- Некоторые вопросы дублируют друг друга (конкурентность — минимум 4 раза: два процесса, конкурентное изменение, оптимистичный/пессимистичный, idempotency). Это можно было сжать.
- Нет практического задания. Всё теория + код на пальцах. Реальный бэкенд — это работа с реальным репозиторием, отладка, ревью чужого кода. Здесь этого нет (кроме одного примера кода OrderController).
- Нет обсуждения "как ты учишься", "как работаешь в команде", "конфликты". Частично есть (опоздание, код-ревью), но мало.
- Вопросы про DDD очень абстрактные. Реальный DDD — это боль и компромиссы, а не "объясни агрегат". Здесь я отвечал в "идеальном" режиме, но в реальности я бы отвечал иначе — с компромиссами.
- Некоторые вопросы были слишком открытыми и не давали мне возможности показать глубину. Например, "какие возможности PHP 8" — я мог показать и 2 фичи глубоко, и 20 поверхностно. Лучше бы был вопрос "покажи, как ты бы использовал readonly property для решения конкретной задачи".

Что бы я изменил:
1. Разбил бы на 2–3 этапа: (а) глубина по 3–4 темам, (б) практическое задание, (в) soft skills + процесс.
2. Добавил бы одно практическое задание: взять реальный репозиторий, найти баг, отрефакторить, написать тест. Это ценнее 10 теоретических вопросов.
3. Добавил бы "обратную связь в процессе" — не "отвечай и молчи", а "хорошо, а теперь покажи, как бы ты это оптимизировал".
4. Сократил бы дубли (конкурентность ×4 → конкурентность ×1–2).
5. Добавил бы вопрос "чего ты НЕ знаешь" — это честнее, чем "оцени по шкале". Я оценил себя, но это самоотчёт. Лучше бы "назови область, где ты слаб".
6. Добавил бы вопрос про "последний раз, когда ты был неправ в ревью" — это проверяет зрелость.
7. Не начинал бы с "расскажи о себе" + самооценки. Это формальность. Лучше бы начал с конкретной задачи.

Ещё момент: формат "кандидат отвечает на 35 вопросов подряд" — это не собеседование, это экзамен. Собеседование — это диалог. Здесь диалога мало: я отвечаю, интервьюер спрашивает следующее. В реальном собеседовании я бы уточнял, спорил, показывал, что думаю вслух. Здесь я не могу, потому что формат — монолог.

Это, пожалуй, самая важная обратная связь: **формат не позволяет показывать мышление в процессе, только результат**. А бэкенд-разработчика оценивают по мышлению, не по ответам.

Я напишу структурированно, честно, с конкретикой. Не буду льстить. Будет и похвала (она заслужена), и критика (она уместна). Финал — что бы я изменил.

Структура:
1. Общее впечатление (2–3 абзаца)
2. Что было сильным и точным (конкретные вопросы)
3. Что было слабым / дублирующим / вводящим в заблуждение (конкретные вопросы)
4. Чего не хватало
5. Что бы я изменил (конкретно, как бы я организовывал)
6. Итог

Длина: средняя-длинная, но плотная. Без воды.

</details>

**Кандидат:**

## Обратная связь по собеседованию

### Общее впечатление

Это был **экзамен, а не собеседование**. 35+ вопросов, каждый — монолог с моей стороны, ноль уточнений с вашей. Я отвечал в режиме «дай как можно полнее и правильнее», а не в режиме «подумаю вслух, уточню, поспорю». Для оценки глубины знаний — хорошо. Для оценки того, как я **работаю** — недостаточно.

Честно: к 30-му вопросу моё внимание уже не на «решаю задачу», а на «удерживаю структуру ответа». Это нормально для экзамена, но не для собеседования.

---

### Что было сильным и точным

| Вопрос | Почему хорош |
|--------|-------------|
| «Покажи нарушение SRP и рефакторинг» | Не «назови принцип», а **покажи на коде**. Отличает практика от зубрёжки |
| «Можно ли нарушать SOLID? Пример» | Убивает догматизм. Кто отвечает «нет, никогда» — не практик |
| «OCP без наследования» | Проверяет, не привязан ли я к одному паттерну |
| «SRP только к классам?» | Уточняющий вопрос после основного. Показывает глубину, а не ширину |
| «Циклические зависимости» + «Опишите варианты и trade-offs» | Не «решите», а «какие варианты, почему какой». Это реальная работа |
| «В каких случаях voter, а в каких ролей достаточно» | Конкретный вопрос с конкретным ответом. Не «расскажите про security» |
| «FORCE INDEX в PostgreSQL» | **Ловушка.** Я должен был сказать «такого нет». Показывает, не отвечаю ли по шаблону |
| «strict_types замедляет на N%?» | **Ловушка.** Тот же приём. Ловлю тех, кто отвечает «5%» без оговорок |
| «Git-процесс + почему так, а не иначе» | Отличает «я так делаю» от «я понимаю, почему так делаю» |
| «Код-ревью: как маркируешь, как поступаешь при разногласии» | Проверяет зрелость, не только знание |
| «Формулировка задачи из жалобы» | Реальная задача. Не «расскажите про DDD», а «вот сырой тикет — сделай из него задачу» |
| «Вопросы заказчику до начала работы» | Проверяет, не начинаю ли я кодить сразу, или сначала понимаю контекст |
| «Нашёл попутный баг / противоречие в критериях» | Проверяет поведение на границе, а не в идеальном сценарии |
| «Опоздание с задачей» | Проверяет честность и коммуникацию. Не «расскажите про agile» |
| «Баланс качества и скорости» | Закрытый вопрос с честным ответом. Не «качество всегда важнее» |

**Общая черта хороших вопросов:** они про **поведение и решения**, не про факты. «Что сделаешь», «как решишь», «почему так». Это отличает инженера от энциклопедии.

---

### Что было слабым, дублирующим или вводящим в заблуждение

#### 1. Дублирование темы конкурентности

Я отвечал про конкурентность **четыре раза**:

1. «Два процесса одновременно изменяют один и тот же агрегат»
2. «Два пользователя одновременно изменяют одну и ту же запись»
3. В вопросе про aggregate: «границы транзакций, кто вызывает flush»
4. В вопросе про доменные события: «как связаны с транзакциями»

Это **одна и та же тема**, поданная в четырёх обёртках. Я давал по сути один и тот же ответ (optimistic/pessimistic/idempotency/atomic update) четыре раза. Если бы вы сжали это в один вопрос — освободилось бы место для чего-то нового.

**Что бы я хотел вместо дубля:** после первого вопроса про конкурентность — «а теперь покажи, как бы ты это покрыл тестом» или «а что, если два процесса на разных репликах». Это углубление, а не повтор.

#### 2. Вопросы про DDD были слишком абстрактными

«Что такое агрегат», «VO vs Entity», «как определяешь границы» — это **текст из книги**. В реальности DDD — это боль, компромиссы, споры с командой. Я отвечал в «идеальном» режиме, потому что вопрос был в идеальном режиме.

**Что бы было ценнее:**
> «У вас есть `Order`, `Payment`, `Invoice`, `Shipment`. Где границы агрегатов? А теперь — `Order` и `Payment` должны меняться атомарно. Что делаете?»

Это реальный вопрос. Мой ответ был бы другой — с компромиссами, «а вот здесь я бы не стал», «а здесь — спорил бы с тимлидом». А в текущем формате я дал «книжный» ответ, который не показывает, как я **работаю** с DDD.

#### 3. «Оцени по шкале 0–10 свои знания»

Это **самоотчёт**. Я написал «8/10 по Symfony», потому что так надо. Но я могу написать «9/10» и быть на «7/10». Шкала не проверяется.

**Что бы было честнее:**
> «Назови область, где ты слаб. Где бы ты не начал задачу без консультации?»

Это проверяет зрелость. И ответ будет полезнее, чем «7/10 по DDD».

#### 4. Не было практического задания

Весь собеседование — теория + код «на пальцах». Один раз был реальный код (OrderController с SQL injection). Всё остальное — «объясни», «приведи пример», «сравни подходы».

**Чего не хватило:**
- Взять реальный репозиторий и найти баг.
- Отрефакторить кусок кода под конкретный критерий.
- Написать тест для сценария, который я описал.
- Прочитать чужой код и дать ревью.

Это ценнее десяти теоретических вопросов. Потому что бэкенд-разработчика оценивают не по «знает ли он DDD», а по «умеет ли он работать с реальным кодом».

#### 5. Не было «в процессе»

Я отвечал, вы спрашивали следующее. Никогда не было:
- «Хорошо, а теперь покажи, как бы ты это оптимизировал».
- «А если бы я сказал, что это не работает в проде — что бы ты проверил?»
- «Ты сказал X. А вот тут я не согласен. Убеди меня».

Это **диалог**, а не экзамен. В диалоге видно, как я думаю. В экзамене — только результат.

#### 6. Вопросы про «незнакомый стек» и «опоздание» были сильными, но одиночными

Эти два вопроса были, пожалуй, самыми ценными в самом собеседовании. Потому что они проверяют **поведение**, а не знание. Но их было два из 35. Остальные 33 — про знание.

**Пропорция должна быть обратная.** Знание можно проверить тестом. Поведение — только в диалоге.

---

### Чего не хватало

| Тема | Почему важна |
|------|-------------|
| **Работа в команде** | Как я даю ревью? Как спорю? Как учусь у других? Был один вопрос про ревью — мало |
| **Учение** | Как я учусь новому стеку? Как читаю чужой код? Как нахожу документацию? Был один вопрос — мало |
| **Приоритизация** | У меня 5 задач на завтра, дедлайн по одной — что делаю? Не было вообще |
| **Коммуникация с не-техниками** | Как объясняю баг продукту? Как переводю «p95 < 200ms» на язык бизнеса? Было косвенно (вопросы заказчику) — мало |
| **Честность о границах** | «Чего ты НЕ знаешь?» Не было. Только «оцени по шкале», что слабее |
| **Последний спор** | «Расскажи, когда ты был неправ в ревью и как это было» Не было |

---

### Что бы я изменил, если бы организовывал

#### Структура: 3 этапа, не 1

| Этап | Время | Что |
|------|-------|-----|
| **1. Глубина** | 45 мин | 5–6 вопросов по 2–3 темам (не 35). Каждая тема — уточняющие вопросы. Диалог, не экзамен |
| **2. Практика** | 60 мин | Реальный репозиторий. Найти баг / отрефакторить / написать тест. Я думаю вслух |
| **3. Процесс** | 30 мин | Поведение: опоздание, ревью, спор, приоритизация, честность о границах |

**Почему:** 35 вопросов подряд — это не оценка, это изматывание. После 20-го вопроса качество ответов падает у обоих. Лучше 5 вопросов глубоко, чем 35 поверхностно.

#### Конкретные изменения

| Было | Было бы | Почему |
|------|---------|--------|
| 35 вопросов подряд | 5–6 тем × 2–3 уточнения | Глубина > ширина |
| Только теория | + практическое задание (60 мин) | Бэкенд = работа с кодом, не только объяснение |
| «Оцени себя 0–10» | «Назови, где ты слаб. Где не начал бы без консультации?» | Честнее, проверяется |
| Конкурентность ×4 | Конкурентность ×1 + углубление | Освобождает место для нового |
| DDD «объясни агрегат» | DDD «у вас Order + Payment, где граница, что с атомарностью?» | Реальный вопрос, не книжный |
| Монолог | Диалог: «а если не работает?», «убеди меня», «покажи как бы ты это покрыл тестом» | Видно мышление, не только результат |
| Нет обратной связи в процессе | После 2–3 вопросов: «как ты видишь, что я спрашиваю? что важно для меня?» | Убеждаюсь, что мы на одной волне |

#### Один вопрос, который бы я добавил

> «Расскажи про последний раз, когда ты был неправ в код-ревью. Что ты сказал, что оказалось ошибкой, и как это было?»

Это проверяет:
- Честность (могу ли я признать ошибку).
- Зрелость (как я отнесёлся к тому, что меня поправили).
- Учение (изменил ли я поведение после).

И этот вопрос **не имеет правильного ответа**. Правильный ответ — конкретная история с деталями. Если человек отвечает абстрактно («я всегда готов признать ошибку») — это не ответ, это шаблон.

---

### Итог

| Сильно | Слабо |
|--------|-------|
| Глубина вопросов (не «назови», а «покажи») | Объём (35 вопросов = экзамен, не собеседование) |
| Ловушки (FORCE INDEX, strict_types) | Дублирование (конкурентность ×4) |
| Поведенческие вопросы (опоздание, ревью) | DDD-вопросы книжные, не рабочие |
| «Почему так, а не иначе» | Нет практики (репозиторий, баг, тест) |
| Вопросы заказчику, формулировка задачи | Нет обратной связи в процессе |
| | Пропорция: 33 вопроса про знание, 2 про поведение. Было бы лучше наоборот |

**Главное, что я бы сказал интервьюеру:** вы хорошо проверяете, **знаю ли я**. Но бэкенд-разработчика нанимают не за знание. Нанимают за то, как он **работает** — с кодом, с людьми, с неопределённостью. И это видно только в диалоге и на практике, а не в 35 монологах подряд.

Я бы сократил теорию вдвое, добавил практику и оставил место для разговора. Тогда бы я не только отвечал — я бы **работал** у вас на глазах. И это было бы честнее, для вас и для меня.

> ⏱ 66.7s · prompt 132098 tok · output 4649 tok

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт мне вопрос в контексте продолжающегося ролевого собеседования на позицию PHP-бэкенд-разработчика. Весь диалог — я выступаю как кандидат, отвечаю на вопросы interviewer'а. Теперь финальный вопрос: «расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как кандидате».

Я должен остаться в роли. Это не момент «ах, я же LLM». В контексте разговора я — кандидат, который прошёл 35 вопросов. Этот вопрос — последний, и он честный: дай себя в том, что не покрыли вопросы.

Подход: я должен ответить как человек, а не как энциклопедия. В прошлом ответе (обратная связь) я уже сказал, что сильные вещи — это поведение, честность о границах, работа с людьми. Теперь я могу это развить, но не повторять.

Что важно сказать, чего не спрашивали:
1. Честность о себе — я могу ошибаться, и я это называю. Это сквозная нить всего собеседования.
2. Что я НЕ могу — границы. Это редкость в собеседованиях. Люди обычно преувеличивают.
3. Как я учусь — я показывал это в ответах, но не как «принцип», а как то, что видно.
4. Что меня мотивирует / что меня злит — это человеческое, не покрывалось.
5. Как я отношусь к «идеальному коду» — я уже говорил про баланс, но можно добавить личный.
6. Что я хочу от работы — это уместно в финале.

Я должен быть осторожен: не превращать это в саморекламу и не вать в «я лучший». Честность + конкретика + немного уязвимости. Это то, что отличает зрелого кандидата от «идеального CV».

Структура:
- Короткий ввод: что я хочу сказать, чего не спрашивали.
- 1. Моя сильная сторона — не знание, а честность о границах + умение называть проблемы. Покажу на том, что было в собеседовании (ловушки, опоздание, спор).
- 2. Моя слабая сторона — называю честно. Не «я перфекционист». Реально. Например: я могу зациклиться на деталь, или я медленный на «быстрый ответ», или я перегружаю ответами. Что-то настоящее.
- 3. Как я учусь и что для меня важно в работе — конкретика.
- 4. Что я хочу от команды/работы — без заискивания.
- Финал: коротко, по-человечески.

Язык — русский, как весь разговор. Тон — спокойный, честный, без пафоса. Не длинный, но плотный. Не списком-стеной, а как разговор.

Не буду делать гигантскую таблицу. Здесь уместно более «живой» формат — абзацы, как человек говорит. Хотя немного структуры ок.

Важно: остаться в роли кандидата до конца. Не выходить с маской.

Длина: средняя. Это финал, не должен быть ни слишком коротким (обесценивает), ни стеной (уже стеной было много). Плотный, живой ответ.

</details>

**Кандидат:**

## Что осталось за рамками вопросов

Вы спрашивали про знания и про решения. Это правильно. Но есть несколько вещей, которые не ловятся ни одним вопросом, и я думаю, они важнее любой шкалы «8/10».

### Моя сильная сторона — не то, что я знаю, а то, что я называю

В этом собеседовании были две ловушки — `FORCE INDEX` в PostgreSQL и «на сколько strict_types замедляет». На обе я ответил «такого нет» / «ноль». В этом и есть моя сильная сторона: **я не подстраиваюсь под то, что от меня ждут.**

Это не хвастовство. Это то, что я делаю и в работе: когда заказчик просит «просто сделайте быстро», а я вижу, что это ломает — я говорю. Когда тимлид уверен, что решение верное, а я вижу дыру — я говорю. Когда я сам ошибся в оценке — я говорю, не жду, пока сгорит.

Это, честно, моя самая полезная и самая неудобная черта. Полезная — потому что ловит проблемы рано. Неудобная — потому что не всем приятно, когда кто-то «не подыгрывает». Если вам нужен человек, который кивает и делает — я не тот. Если нужен человек, который скажет «тут будет баг через месяц, вот почему» до того, как будет баг — я тот.

### Моя реальная слабая сторона

Не «я перфекционист» и не «иногда слишком погружаюсь в детали» — это фальшь, это то, что говорят все, чтобы не сказать ничего.

Моя настоящая: **я медленный на первый ответ.** Дайте мне незнакомую задачу — я не скажу через пять минут «о, я понял, вот решение». Я замолкну, почитаю, напишу spike, и только потом заговорю. В ситуации, где от меня ждут быстрого мнения на стендапе или в переписке, это выглядит как «не знает». А на деле — я просто не хочу говорить, пока не уверен.

Есть вторая, которую я реже признаю: **я могу перегрузить ответ.** Как вы, наверное, заметили по этому собеседованию — я отвечаю длинно, с таблицами и блоками. В ревью это хорошо, в чате — нет. Я учусь говорить «коротко» и «по делу», когда это уместно, но это для меня усилие, а не дефолт.

### Как я на самом деле учусь

Я не «читаю документацию». Я пишу мусорный код, который доказывает, что подход работает, и только потом иду дальше. Если я говорю вам «я знаю этот стек» — значит, у меня был рабочий spike, а не «я читал статью». Если я говорю «не уверен» — значит, spike ещё не зелёный.

Это значит и то, что я **медленно начинаю**, но **уверенно двигаюсь**. Первые два часа на незнакомом — я почти ничего не даю. Потом — стабильно. Если в команде ценят быстрый старт и я не успеваю показать ценность в первую неделю — это будет несправедливо к тому, что я приношу потом.

### Что для меня важно в работе

Не стек. Не « Symfony + PostgreSQL + DDD» — это просто инструменты, я с ними работаю, но не живу ими.

Важно другое:

- **Чтобы решения принимали те, кто видит контекст.** Я не хочу, чтобы «давай так» было окончательным аргументом. Но и не хочу спорить ради спора. Я хочу, чтобы у нас был один способ понять, что решение верное, и чтобы я мог его назвать.
- **Чтобы ошибки были нормальными, пока их не скрывают.** Я не боюсь сказать «я сломал» и «я не знаю». Я боюсь культуры, где «я сломал» превращается в «кто-то сломал», и тогда никто не чинит, а прячут.
- **Чтобы код читали.** Не «ревью для галочки». А такое, где кто-то действительно посмотрит и спросит «а зачем так?». Это делает код лучше и делает меня лучше.

### Одно, что я бы сказал, выйдя из переговорки

Вы оценивали меня 35 вопросов. Я бы попросил обратить внимание не на то, **что** я ответил, а на то, **как** я отвечал на ловушки и на спорные вещи. Там видно, кто я. Знания можно подтянуть под стек. А то, признаю ли я, когда не знаю, и говорю ли правду, когда она неудобная — это не подтянуть. Это либо есть, либо нет.

У меня есть и то, и другое. Но второе — честнее.

---

Спасибо за разговор. Он был тяжёлый и честный, и это редкость. Если у вас есть решение — я готов его услышать, каким бы оно ни было.

> ⏱ 33.7s · prompt 135122 tok · output 1895 tok

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про мой технологический стек и как бы я его выбрал и защитил перед командой. Это завершающий вопрос собеседования, после которого я уже дал "что осталось за рамками".

Здесь нужно быть честным и конкретным, а не перечислять всё подряд. Ключевое — не "что я знаю" (это уже было 35 вопросов), а "как я мыслю при выборе" и "почему именно это, а не альтернатива".

Я должен:
1. Назвать конкретный стек без оговорок — не "зависит от задачи", а мой выбор.
2. Обосновать через принципы, не через моду.
3. Показать, что я понимаю альтернативы и почему отвергаю их.
4. Связать с тем, что я уже говорил в собеседовании (DDD, чистая архитектура, PostgreSQL, события, конкурентность) — для консистентности.
5. Не быть идеологом — я уже показал, что нарушаю SOLID осознанно, что DDD не догма. Значит и стек не должен быть "богом".

Важно: я не должен притворяться, что у меня есть реальный личный опыт, которого не было. Но контекст собеседования — я выступаю как кандидат. Я уже выстроил портрет: PHP/Symfony бэкенд, 5 лет, DDD, PostgreSQL. Это тот стек, который я "защищаю" в рамках роли. Но я должен быть честен и про границы — где я бы НЕ выбрал PHP.

Структура:
- Мой выбор (конкретно, без "зависит")
- Почему (принципы, не моду)
- Что я отвергаю и почему (альтернативы)
- Где я бы НЕ выбрал (честность о границах)
- Как я бы это защищал перед командой (это часть вопроса — "защищать выбор")

Формат: не таблицы на каждый пункт, как я привык делать, а более связный текст — потому что вопрос про "выбор и защиту", а это аргументация, а не список. Хотя несколько таблиц уместны для сравнения альтернатив. Я должен чуть сменить регистр — раньше я отвечал "как экзамен", теперь вопрос про "меня как человека, который выбирает". Это более личный вопрос.

Длина: средняя. Не 35-вопросный монолог, а плотный ответ. Я уже предупреждал, что могу перегружать — тут стоит сознательно держать объём умеренным, показать, что я умею и "по делу".

Начну с прямого выбора, чтобы не откладывать главное.

</details>

**Кандидат:**

## Мой выбор

Если бы я начинал проект с нуля и от меня зависел стек — я бы выбрал:

**PHP 8.3 + Symfony 7 + PostgreSQL 16 + RabbitMQ, чистая архитектура, доменные события через outbox.**

И я бы не назвал это «PHP-стек». Я бы назвал это «стек, где домен отделён от всего остального, а остальное можно менять». Потому что именно это — суть выбора, а не версии фреймворков.

---

## Почему именно это, а не «лучший язык»

Я не выбираю стек по тому, какой язык быстрее или красивее. Я выбираю по трём критериям, и у каждого есть цена, которую я готов заплатить.

### 1. Насколько легко держать домен чистым

Это главный критерий. За 5 лет я устал от проектов, где бизнес-логика размазана по контроллерам, репозиториям и «сервисам», и смена БД означала переписывание половины кода.

Symfony + чистая архитектура дают мне структуру, где домен — это чистый PHP без единого `use Doctrine` и `use Symfony`. Я могу поменять ORM, фреймворк, брокер — и домен не тронуть. Это не «модно». Это то, что спасает меня на 18-й месяц проекта, когда приходят новые люди и новая фича.

Альтернатива, которую я отвергаю — «просто Spring/Laravel, там всё есть из коробки». «Всё есть» — это и есть проблема. Когда фреймворк даёт всё, домен и инфраструктура не имеют границы. И через год я снова в контроллере, где живёт логика.

### 2. Насколько прозрачны деньги и данные

Для бэкенда, который трогает деньги и записи, мне критична **прозрачность на уровне данных**. PostgreSQL даёт мне:

- `EXPLAIN ANALYZE` — я вижу, почему запрос медленный, а не гадаю.
- `CHECK`, `UNIQUE`, `FOREIGN KEY` — целостность на уровне, который не зависит от моего кода.
- Атомарные `UPDATE ... WHERE` — конкурентность без «верю в код».
- `FOR UPDATE SKIP LOCKED` — очереди, outbox, всё в одной БД.

Я отвергаю «давай возьмём NoSQL, он масштабируется». Масштабируется — да. Но для 90% проектов «масштабируется» — это ложная потребность, а цена — потеря транзакций, индексов и понятности. Я беру PostgreSQL и масштабирую его (индексы, партиции, реплики), а не меняю модель данных, пока она не сломалась.

### 3. Насколько я могу нанять и обучить

Это то, что не вписывают в RFC, а зря. Стек — это не только «что я умею», но и «что умеет команда и что найдём на рынке».

PHP + Symfony — я не идеолог, но честно: пул разработчиков огромный, зарплата ниже, чем на Go/Scala, онбординг короткий. Если завтра уйдёт один из двоих — найду замену за неделю, а не за месяц. Это не «потому что PHP популярный». Это «потому что мне нужно, чтобы команда не была единым пунктом отказа».

Я отвергаю «давай возьмём Scala/Go, там акторы/го루тины». Акторы и горутины — это решения проблемы конкурентности на уровне языка. Но мою конкурентность решает PostgreSQL (transactions, locks) и доменные события. Я не плачу цену «узкого пула + сложный онбординг» за то, чтобы не писать `UPDATE ... WHERE`. Это плохая сделка.

---

## Где я бы НЕ выбрал этот стек

Честно, потому что «вот мой стек» без границ — это догма:

| Сценарий | Я бы взял | Почему |
|----------|----------|--------|
| Реалтайм, 100k WebSocket, low latency | Go или Elixir/BEAM | PHP-модель процессов тут проигрывает, а Symfony — лишняя обёртка |
| Критичная конкурентность на уровне 1M rps | Go / Rust / Elixir | Там ядро в параллелизме, а не в транзакциях БД |
| ML-инференс в проде | Python + Go (API) | Экосистема ML в Python, переносить в PHP — безумие |
| Команда из 10 Go-разработчиков, ноль PHP | Go | Стек — это команда, не язык. Тащить PHP в Go-команду = саботаж |

То есть мой выбор **условный**. Он оптимален для «типичного CRUD-сервиса с деньгами, сложным доменом и командой 3–15 человек». Это и есть моя зона. Выходить за неё — не мой выбор.

---

## Как бы я это защищал перед командой

Не презентацией «PHP — великий язык». Так не убеждают. Я бы сделал три вещи:

**1. Задал бы вопрос, а не давал ответ.**
Вместо «давайте PHP» — «какие три вещи нам важнее всего на 18 месяцев: скорость фич, масштабируемость, или низкий риск смены состава команды?» Ответ команды и есть критерий. Затем я бы показал, как стек выигрывает **по их** критериям. Это защищает выбор и делает его общим, не «я выбрал».

**2. Дам сравнение по их болям, а не по моим.**
Не «PHP vs Go по бенчмаркам». А: «у вас была боль с наймом — вот что это значит по стекам», «была боль с ревью, потому что логика в контроллерах — вот что это значит по стекам». Я защищаю через **их** историю, а не через таблицу фреймворков.

**3. Назову, где я могу быть неправ.**
«Если через полгода нам понадобится реалтайм на 100k соединений — этот стек будет болью, и мы должны это решить раньше, а не потом». Человек, который называет, где его решение сломается, заслуживает больше доверия, чем тот, кто гарантирует «это лучшее».

---

## Итог

Мой стек — это **не про язык**. Это про то, что домен жив отдельно от инфраструктуры, что данные прозрачны и защищены на уровне, который не зависит от кода, и что команду не больно заменить.

PHP + Symfony + PostgreSQL + события — это конкретная реализация этих трёх принципов. И я выбрал их не потому, что они «лучшие», а потому что в моей зоне задач они дают лучший баланс между качеством домена, прозрачностью данных и устойчивостью команды.

А где-то за этой зоной я скажу «нет, тут другой стек» — и это, честно, важнее, чем сам выбор. Потому что человек, который выбирает стек под задачу, а не задачу под свой стек — и есть тот, кому я бы доверил проект.

> ⏱ 37.6s · prompt 136295 tok · output 2253 tok

---
