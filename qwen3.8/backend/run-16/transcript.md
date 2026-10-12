# Собес: `qwen3.8:27b` · роль: backend — run-01

- Дата: 2026-10-12T02:30:12.604993+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 10m · think: low · num_ctx: 131072
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to role-play as a PHP backend developer in a job interview. They want me to introduce myself, talk about my experience with PHP and related technologies, and then rate my knowledge on a scale of 0-10 for each listed area with a one-sentence justification.

I need to be honest here. I'm an AI, not a human developer. I should not pretend to be a human with a fake resume. That would be dishonest. However, I can approach this in a useful way: I can acknowledge I'm an AI, but then provide a genuinely useful answer about what I know in these areas — essentially presenting my "knowledge profile" as if I were a candidate, while being transparent about what I am.

Actually, let me reconsider. The user is setting up a role-play scenario. They said "ты проходишь собеседование" — you're going through an interview. This is a common way people use LLMs — to test their knowledge, get mock interview answers, or practice. 

The most helpful approach: I'll play the role as requested (since this is clearly a practice/mock interview exercise), present a realistic developer profile with ratings, and be substantive in my answers. I won't add a disclaimer that breaks the immersion unless asked. The user clearly wants a mock interview exercise.

Let me construct a believable profile of a mid-to-senior PHP developer (5-7 years of experience) and give honest ratings that reflect what a strong candidate in these areas would have:

- PHP 8: 9 — strong knowledge of modern PHP, fibers, readonly, enums, match, etc.
- Symfony: 9 — daily use, DI, events, bundles, configuration
- Doctrine: 8 — ORM, DBAL, query builder, DQL, some edge cases with proxies/lazy loading
- PostgreSQL: 7 — good working knowledge, indexes, CTEs, window functions, but not DBA level
- DDD: 7 — understands bounded contexts, aggregates, value objects, applies in medium complexity domains, not deep strategic design
- Clean Architecture: 7 — understands layers, dependency inversion, has applied in practice but acknowledges real-world tradeoffs
- Testing: 8 — PHPUnit, integration tests, some mocking, test-driven mindset
- Git: 9 — daily tool, rebasing, bisect, hooks, worktrees

Let me write this as a natural interview answer in Russian.

</details>

**Кандидат:**

Привет! Спасибо за возможность.

**Коротко о себе:**

Рассматриваю ~6 лет коммерческой разработки на PHP. Начинал с Laravel, но последние 4 года основная площадка — Symfony. Работаю в командах от 3 до 15 человек, обычно как senior, иногда веду архитектуру.

**Что делаю руками:**

- Проектирую и пишу доменные модели, сервисы, API (REST и GraphQL через API Platform или overblog).
- Работаю с Doctrine ORM на ежедневной основе: DQL, QueryBuilder, hydration, транзакции, иногда спускаюсь до DBAL и native SQL.
- Пишу интеграционные и unit-тесты (PHPUnit, иногда Pest). Есть опыт TDD, но в продакшене чаще BDD-стиль — сначала сценарий, потом тест, потом реализация.
- Инфраструктура: Docker, CI (GitLab CI / GitHub Actions), деплой на Kubernetes.
- Сообщения и асинхронность: RabbitMQ, Symfony Messenger, иногда Kafka через kafka-php.

**Что важно для меня в команде:**

Яркий код, понятные границы контекстов, минимизация «магии» фреймворка. Не против микросервисов, но только когда домен реально это требует — иначе modular monolith с чёткой структурой.

---

**Оценки по шкале 0–10:**

| Раздел | Оценка | Обоснование |
|--------|--------|-------------|
| **PHP 8** | **9** | Ежедневно использую enums, readonly, fibers, named arguments, attributes, match — понимаю internals и ограничения каждой фичи. |
| **Symfony** | **9** | DI, события, messenger, security, form, validator — знаю внутреннее устройство, пишу кастомные bundle-компоненты и перехватываю ядро. |
| **Doctrine** | **8** | Уверенно работаю с ORM, понимаю lifecycle, unit of work, proxy/lazy loading, ограничения — но edge-кейсы с custom DQL и сложными joins иногда требуют экспериментов. |
| **PostgreSQL** | **7** | Пишу CTE, window functions, понимаю индексацию и explain — но не углублялся в тюнинг planner и partitioning на уровне DBA. |
| **DDD** | **7** | Корректно проектирую aggregates, value objects, события; разбиваю bounded contexts в проектах средней сложности, но strategic design (ubiquitous language с доменной командой) — пока не всегда в чистом виде. |
| **Чистая архитектура** | **7** | Применяю dependency inversion, разделение слоёв, hexagonal в проектах; понимаю tradeoffs и не догматик, но в больших системах иногда приходится идти на упрощения. |
| **Тестирование** | **8** | PHPUnit, интеграционные тесты с реальным БД, мок-сервисы, coverage; есть опыт TDD-подхода в greenfield-проектах, в легаси — догоняю тестами. |
| **Git** | **9** | Rebase, bisect, worktrees, hooks, интерактивный rebase, разрешение конфликтов в больших merge — ежедневный инструмент, никаких сюрпризов. |

---

Готов двигаться дальше — спрашивайте, что хотите углубить или проверить вживую.

> ⏱ 13.7s · prompt 135 tok · output 1271 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user is asking me (in the role of a PHP backend developer) about the most important features of modern PHP (8.0+) for writing clean and reliable code, with short code examples.

Let me think about what's genuinely impactful for "clean and reliable code":

1. **Readonly properties** (8.1) — immutability, prevents accidental mutation
2. **Enums** (8.1) — replaces scattered const strings/ints, type-safe
3. **Named arguments** (8.0) — clarity at call sites
4. **Union types & intersection types** (8.0, 8.2) — more precise contracts
5. **Match expression** (8.0) — replaces verbose switch, exhaustive with exhaustiveness checking
6. **Fibers** (8.1) — async patterns without coroutines library
7. **Constructor property promotion** (8.0) — less boilerplate
8. **First-class callable syntax** (8.1) — cleaner function references
9. **Ditto operator** (8.0) — less type annotation duplication
10. **Never return type** (8.1) — expresses "this function never returns"
11. **Property hooks** (8.4) — encapsulation with controlled access
12. **Asymmetric visibility** (8.4) — readonly with public getter, private setter

For "clean and reliable code" specifically, I should focus on:
- **Enums** — type safety, exhaustiveness
- **Readonly properties / objects** — immutability, less bugs
- **Never return type** — expresses intent (throws/exits)
- **Match** — exhaustive patterns vs switch
- **Constructor promotion** — less boilerplate, clearer contracts
- **First-class callables** — cleaner function passing
- **Property hooks** (8.4) — encapsulation without getters/setters boilerplate

Let me pick the most impactful ones and show short, practical examples. I'll aim for 5-6 features with concise examples that demonstrate the "before/after" or the value proposition.

I'll structure it as a list with feature name, why it matters for clean/reliable code, and a short code snippet.

</details>

**Кандидат:**

## Самые значимые фичи PHP 8.0+ для чистого кода

### 1. Enums (8.1)

Убивают «магические» строки и инты, дают исчерпывающий перебор и статическую проверку.

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Delivered, self::Cancelled => true,
            self::Pending, self::Shipped => false,
        };
    }
}

// Вместо:
// $order->status = 'shipped';  // опечатка = баг в продакшене
// Теперь:
$order->status = OrderStatus::Shipped; // IDE подсказывает, компилятор проверяет
```

---

### 2. Readonly properties + readonly classes (8.1–8.2)

Иммутабельность — базовый инструмент против «сюрпризов» при передаче объектов между слоями.

```php
readonly class Money
{
    public function __construct(
        public int    $amountCents,
        public string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException();
        }

        return new self($this->amountCents + $other->amountCents, $this->currency);
    }
}

// $money->amountCents = 999; → Fatal Error: Cannot modify readonly property
```

---

### 3. `never` return type (8.1)

Чётко выражает «эта функция не вернёт управление» — компилятор и IDE знают, что код после вызова недостижим.

```php
function requireActiveOrder(Order $order): never
{
    if (!$order->isActive()) {
        throw new InactiveOrderException($order->getId());
    }

    throw new LogicException('Unreachable — guard above should have thrown');
}

// Вызов:
requireActiveOrder($order);
// IDE понимает, что следующая строка — dead code, если guard сработал
```

---

### 4. `match` (8.0)

Исчерпывающий паттерн: если ветка не покрыта — исключение в рантайме, а не тихое «пропускание» как в `switch`.

```php
public function calculateFee(): Money
{
    return match (true) {
        $this->customer->isPremium() => new Money(0, 'EUR'),
        $this->subtotalCents > 50_000 => new Money(0, 'EUR'),
        default => new Money(599, 'EUR'),
    };
    // Если убрать default и не покрыть все случаи — UnexpectedValueException
}
```

---

### 5. Constructor property promotion (8.0)

Меньше boilerplate, контракт ясен из сигнатуры — сразу видно, какие поля обязательны и их тип.

```php
// До:
class SendEmailHandler
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private int $retryLimit;

    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger,
        int $retryLimit = 3
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->retryLimit = $retryLimit;
    }
}

// После:
class SendEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private int $retryLimit = 3,
    ) {}
}
```

---

### 6. First-class callable syntax (8.1)

Передача методов без `fn`-обёртки — чище, особенно в DI-контейнерах и колбэках.

```php
// До:
$this->eventBus->subscribe('order.created', function (OrderCreated $event) {
    return $this->sender->send($event);
});

// После:
$this->eventBus->subscribe('order.created', $this->sender->send(...));
```

---

### Итого

| Фича | Что даёт «надёжности» |
|------|----------------------|
| Enums | Типобезопасность + исчерпывающий перебор |
| Readonly | Иммутабельность, нет «мутабельных сюрпризов» |
| `never` | Явный контракт «не вернёт управление» |
| `match` | Отсутствие тихих пропусков веток |
| Promotion | Контракт виден в сигнатуре, меньше boilerplate |
| First-class callables | Меньше обёрток, чище код |

Если хотите — могу показать, как эти фичи сочетаются в одном реальном примере (например, доменная сущность + обработчик события + валидация).

> ⏱ 11.6s · prompt 956 tok · output 1660 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему типов в PHP: strict_types, приведения типов, ловушки. Нужно дать структурированный ответ с примерами кода. Я уже выступил в роли senior PHP-разработчика, продолжаю в этой роли.

Структура ответа:
1. Что такое strict_types и как он работает (важно: влияет на файл, где объявлен, а не на вызывающий)
2. Типизация в PHP — коварная система: scalar types, union types, intersection types
3. Приведения типов — коварные правила (string vs int, float vs int, bool vs string, "truthy" строки)
4. Ловушки на практике:
   - strict_types = 1 не защищает от "внешнего" кода
   - "0" vs "00" vs false
   - int overflow в float
   - string|int union type
   - null в параметрах (nullable)
   - массивы как "свободные" структуры
   - "empty string" vs "0"
   - float comparison
   - string "1e5" как int
   - "99999999999999999999" → float

Сделаю чётко, с примерами, где типизация спасает, а где удивляет.

</details>

**Кандидат:**

## Система типов в PHP: механика, strict_types и ловушки

### 1. Что делает `strict_types`

```php
// strict_types.php
<?php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

add("1", 2); // TypeError: Argument #1 must be of type int, string given
```

**Ключевое правило:** `strict_types` действует **в файле, где он объявлен**, на **вызовы функций из этого файла**.

```php
// app.php (strict_types=1)
<?php
declare(strict_types=1);

require 'vendor.php';

// vendor.php НЕ имеет strict_types
// Вызов функции из vendor.php — в режиме coerced, даже если мы в strict-файле
```

А вот вызов **нашей** функции `add()` из `app.php` — строго, потому что `app.php` объявил `strict_types=1`.

```php
// vendor.php
<?php
// нет strict_types

function risky(int $x): int {
    return $x;
}

// app.php (strict_types=1)
risky("42"); // OK! Потому что risky() определена в файле БЕЗ strict_types
// Но:
risky(4.5);  // TypeError, потому что вызов идёт из strict-файла... 
// НЕТ! Правило другое: strict_types влияет на файл-ОБЪЯВЛЕНИЕ, 
// а вызов из strict-файла — на strict-режим ВЫЗОВА.
```

Уточню точнее:

> `strict_types=1` в файле **A** означает: все **вызовы функций из файла A** выполняются в strict-режиме. Типы параметров проверяются строго.

```php
// A.php
<?php
declare(strict_types=1);
require 'B.php';

B::take(3.14); // TypeError — вызов из strict-файла A
```

```php
// B.php
<?php
// нет strict_types
class B {
    public static function take(int $x): int { return $x; }
}
```

---

### 2. Режим coerced (по умолчанию) — коварные правила

В режиме coerced PHP **приводит** аргументы к ожидаемому типу. Но правила — неочевидные:

#### a) String → int

```php
<?php
// coerced
function f(int $x): int { return $x; }

f("42");          // 42 — OK
f(" 42 ");        // 42 — OK (trim)
f("42abc");       // 42 — OK (берёт префикс)
f("1e5");         // 1 — OK (только до "e")
f("0x1A");        // 0 — OK (не парсит hex)
f("");            // 0 — OK
f("12345678901234567890"); // 1.2345678901235E+19 — float! int переполнился
```

**Ловушка:** `"12345678901234567890"` → `float`. В strict-режиме — `TypeError`, в coerced — тихое переполнение.

#### b) Float → int

```php
f(4.0);   // 4 — OK, точное целое
f(4.7);   // 4 — округление вниз, молча
f(4.9);   // 4 — тоже вниз (не round!)
f(-4.7);  // -4 — к нулю (не вниз!)
```

#### c) Bool → string, string → bool

```php
// string → bool (когда ожидаем bool)
function g(bool $b): bool { return $b; }

g("0");    // false — ЕДИНСТВЕННАЯ строка, которая = false
g("");     // false
g("false");// TRUE  ← ловушка!
g(" ");    // TRUE
g("abc");  // TRUE
```

#### d) Int/float → string

```php
function h(string $s): string { return $s; }

h(42);     // "42"
h(4.5);    // "4.5"
h(1.234567890123456789); // "1.23456789" — потеря точности float!
h(1E20);   // "1E+20" — экспоненциальная запись
```

#### e) Null → что угодно

```php
function f(int $x): int { return $x; }
f(null); // TypeError — null НЕ приводится к int (даже в coerced!)

// Но:
function g(string $s): string { return $s; }
g(null); // "" — null приводится к пустой строке!
```

**Это, пожалуй, самая коварная асимметрия.**

---

### 3. Где типизация **спасает**

```php
<?php
declare(strict_types=1);

// 1. Иммутабельные value objects
readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currency,
    ) {}
}

// money->cents = "abc"; → TypeError
// money->cents = 3.14;  → TypeError
// money->cents = null;  → TypeError

// 2. Enums — исчерпывающий перебор
enum PaymentMethod: string
{
    case Card = 'card';
    case Bank = 'bank';
    case Crypto = 'crypto';
}

function fee(PaymentMethod $method): int
{
    return match ($method) {
        PaymentMethod::Card => 299,
        PaymentMethod::Bank => 0,
        PaymentMethod::Crypto => 149,
        // Если добавим новый case — IDE подсветит, что match не полон
    };
}

// 3. Union types — явный контракт
function id(int|string $id): string
{
    return (string) $id;
}

id(42);        // "42"
id("abc-123"); // "abc-123"
id(4.5);       // TypeError — float не в union
```

---

### 4. Где типизация **удивляет**

#### a) `strict_types` не защищает «внешние» данные

```php
<?php
declare(strict_types=1);

function process(string $userId): void { /* ... */ }

// Данные из БД:
$row = $pdo->query('SELECT user_id FROM orders')->fetch();
process($row['user_id']); // OK — string из БД

// Данные из JSON:
$data = json_decode($body, true);
process($data['user_id']); // TypeError, если user_id = 42 (int из JSON)
// strict_types не «знает», что JSON может дать int вместо string
```

#### b) Intersection types и object types — не «глубже»

```php
function log(LoggerInterface&Stringable $logger): void {}

// $logger->toString() — OK
// $logger->log() — OK
// Но: нет проверки, что объект НЕ имеет метода, которого мы ожидаем НЕ быть
// (нет structural typing, только nominal)
```

#### c) Array types — «дыра» в системе типов

```php
function items(array $items): void {}

items([]);           // OK
items([1, "a", null, 3.14, new stdClass()]); // OK — mixed array
items([1, 2, 3]);   // OK
// PHP не может гарантировать, что каждый элемент — int
// (list<int> — только в static analysis, не в рантайме)
```

**Это главная причина, почему Psalm/PHPStan добавляют ценность сверх встроенных типов.**

#### d) `mixed` — «анти-тип»

```php
function anything(mixed $x): mixed { return $x; }

anything("hello");  // OK
anything(42);       // OK
anything(null);     // OK
anything([1,2]);    // OK
anything(fn() => 1);// OK

// mixed = «я сдаюсь, проверяй сам»
```

#### e) Numeric string vs int в `match`

```php
<?php
declare(strict_types=1);

$id = "42"; // из HTTP-заголовка, например

match ($id) {
    42 => "found",     // НЕ сработает! "42" !== 42 в match (strict comparison)
    "42" => "found",   // сработает
};

// В if/switch (coerced) было бы:
if ($id == 42) { /* сработало бы */ }
```

---

### 5. Практические рекомендации

| Ситуация | Решение |
|----------|---------|
| Весь проект | `declare(strict_types=1);` в каждом файле |
| Границы (HTTP, БД, JSON) | Явное приведение + валидация **до** передачи в домен |
| Коллекции | `array<int, Order>` + static analysis (Psalm/PHPStan) |
| Value objects | `readonly` + constructor promotion + enum |
| «Возвратит или нет» | `never` вместо `void` + throw |
| Опасные строки | Не полагаться на coerced-приведение — парсить явно |

---

**Резюме:** встроенная типизация PHP — это «тонкий слой безопасности» поверх динамической системы. Она ловит 80% опечаток и несовпадений, но на границах (входные данные, массивы, float-арифметика) нужна либо дисциплина, либо static analysis. `strict_types` — не «полная типизация», а «уберни самые тихие баги».

> ⏱ 19.9s · prompt 2199 tok · output 2634 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о проектировании работы с исключениями в PHP. Это продолжение собеседования. Нужно ответить как senior PHP-разработчик — практично, с примерами кода, без воды.

Структурирую ответ:
1. Принципы проектирования иерархии
2. Свой доменный слой исключений (пример)
3. Оборачивание чужих исключений (инфраструктурный слой)
4. Контракт метода — что должно быть в сигнатуре
5. Антипаттерны с конкретным кодом

Важно:
- Не быть догматиком, показывать tradeoffs
- Показывать разницу между domain exception и infrastructure exception
- Показать, как исключения работают в связке с типами (union types, never)
- Антипаттерны: Exception (база), catch all, throw из деструкторов, исключения для flow control, "exception factory" без смысла, catch и rethrow без контекста

Примеры кода должны быть реалистичными, не учебными.

</details>

**Кандидат:**

## Проектирование исключений в PHP

### 1. Базовый принцип: разделение слоёв

Исключения — это **контракт между слоями**. Домен не знает, что у нас PostgreSQL или что мы в Docker. Инфраструктура не знает, что у нас `Order` или `Invoice`.

```
┌─────────────────────────────────────────────────┐
│  Domain Layer                                   │
│  - DomainException (абстракция)                 │
│  - OrderNotFound, InsufficientBalance, ...     │
│  - НЕ зависит от Symfony, Doctrine, PDO         │
└──────────────────────┬──────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────┐
│  Application Layer                              │
│  - ApplicationException                         │
│  - Обёртка: "что произошло на уровне use-case"  │
└──────────────────────┬──────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────┐
│  Infrastructure Layer                           │
│  - Catch чужих исключений (Doctrine, PDO, HTTP) │
│  - Перевод в доменные или application           │
└─────────────────────────────────────────────────┘
```

---

### 2. Пример иерархии

```php
// Domain/Exception.php
namespace App\Domain\Common;

abstract class DomainException extends \RuntimeException
{
    // Наследует от RuntimeException — это НЕ логическая ошибка.
    // Если метод нарушил инвариант домена — это runtime-ситуация.
}
```

```php
// Domain/Order/Exception/OrderNotFound.php
namespace App\Domain\Order\Exception;

final class OrderNotFound extends DomainException
{
    public function __construct(
        private readonly string $orderId,
    ) {
        parent::__construct(
            sprintf("Order [%s] not found", $orderId),
        );
    }

    public function orderId(): string
    {
        return $this->orderId;
    }
}
```

```php
// Domain/Order/Exception/InsufficientBalance.php
namespace App\Domain\Order\Exception;

final class InsufficientBalance extends DomainException
{
    public function __construct(
        private readonly int   $requiredCents,
        private readonly int   $availableCents,
    ) {
        $diff = $requiredCents - $availableCents;
        parent::__construct(
            sprintf(
                "Insufficient balance: need %d, have %d (short %d)",
                $requiredCents,
                $availableCents,
                $diff,
            ),
        );
    }
}
```

```php
// Application/Exception.php
namespace App\Application;

class ApplicationException extends \RuntimeException
{
    // Маркер: "что-то пошло не так в use-case".
    // Не конкретнее — конкретика живёт в domain.
}
```

---

### 3. Оборачивание чужих исключений

Ключевое правило: **инфраструктура не бросает «сырые» исключения наружу**. Она переводит их.

```php
// Infrastructure/Persistence/OrderRepository.php
namespace App\Infrastructure\Persistence\Order;

use App\Domain\Order\Exception\OrderNotFound;
use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

final class DoctrineOrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(OrderId $id): Order
    {
        try {
            return $this->em
                ->getRepository(Order::class)
                ->findOneBy(['id' => $id->value()]);
        } catch (NoResultException) {
            // Не кидаем NoResultException наружу —
            // домен этого не знает и не должен
            throw new OrderNotFound($id->value());
        }
    }

    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
        // Здесь НЕ ловим — если flush падает,
        // это уже проблема на уровне Application/HTTP
    }
}
```

```php
// Application/OrderService.php
namespace App\Application;

use App\Domain\Order\Exception\OrderNotFound;
use App\Domain\Order\Exception\InsufficientBalance;
use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Infrastructure\Persistence\Order\DoctrineOrderRepository;

final class OrderService
{
    public function __construct(
        private readonly DoctrineOrderRepository $orders,
    ) {}

    /**
     * @throws OrderNotFound
     * @throws InsufficientBalance
     */
    public function pay(OrderId $id): Order
    {
        $order = $this->orders->findById($id);

        if (!$order->canBePaid()) {
            throw new OrderStatusInvalid($id->value());
        }

        $order->pay();
        $this->orders->save($order);

        return $order;
    }
}
```

---

### 4. Что считается контрактом метода

Контракт — это **документированный набор исключений**, которые метод **гарантированно** может бросить.

```php
/**
 * Подтверждает оплату заказа.
 *
 * @throws OrderNotFound        — если заказ не существует
 * @throws InsufficientBalance  — если на балансе не хватает средств
 * @throws OrderStatusInvalid   — если заказ уже в финальном статусе
 *
 * @return Order  подтверждённый заказ
 */
public function pay(OrderId $id): Order
```

**Что НЕ входит в контракт:**
- `\RuntimeException` из `EntityManager::flush()` — это «всё сломалось», не доменная ситуация.
- `\TypeError` — это баг.
- `NoResultException` из Doctrine — это инфраструктурный артефакт.

**Практика:**
- В доменных методах — только доменные исключения.
- В application-методах — доменные + application.
- В инфраструктурных методах — «всё, что угодно», но **на границе** (repository, client) — перевод.

---

### 5. Обработка на границе (HTTP)

```php
// Controller — тонкий, не содержит логики
namespace App\Controller;

use App\Application\OrderService;
use App\Domain\Order\Exception\OrderNotFound;
use App\Domain\Order\Exception\InsufficientBalance;
use Symfony\Component\HttpFoundation\JsonResponse;

final class OrderController
{
    public function __construct(
        private readonly OrderService $orders,
    ) {}

    public function pay(string $id): JsonResponse
    {
        try {
            $order = $this->orders->pay(new OrderId($id));

            return new JsonResponse([
                'id'      => $order->id()->value(),
                'status'  => $order->status()->value,
            ]);
        } catch (OrderNotFound) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        } catch (InsufficientBalance $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        } catch (OrderStatusInvalid $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }
    }
}
```

Альтернатива (чистее для сложных проектов) — **Listener/Subscriber**:

```php
// Infrastructure/Http/ExceptionListener.php
namespace App\Infrastructure\Http;

use App\Domain\Common\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class DomainExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();

        if (!$exception instanceof DomainException) {
            return; // не наше
        }

        $event->setResponse(new JsonResponse(
            ['error' => $exception->getMessage()],
            $this->statusCode($exception),
        ));
    }

    private function statusCode(DomainException $e): int
    {
        return match (true) {
            $e instanceof OrderNotFound       => 404,
            $e instanceof InsufficientBalance => 422,
            $e instanceof OrderStatusInvalid  => 409,
            default                           => 422,
        };
    }
}
```

---

### 6. Антипаттерны

#### a) Бросать базовый `\Exception`

```php
// ПЛОХО
function processOrder(Order $order): void
{
    if ($order->isExpired()) {
        throw new \Exception("Order is expired");
        // — не знаю, что делать
        // — не могу catch-ить точечно
        // — не могу отличить от любого другого \Exception
    }
}

// ХОРОШО
throw new OrderExpired($order->id()->value());
```

#### b) Catch all + rethrow без контекста

```php
// ПЛОХО
try {
    $this->apiClient->charge($amount);
} catch (\Exception $e) {
    throw $e; // просто перекладываем, без обогащения
}

// ХОРОШО
try {
    $this->apiClient->charge($amount);
} catch (PaymentGatewayException $e) {
    throw new PaymentDeclined(
        reason: $e->getGatewayCode(),
        original: $e,  // сохраняем причину
    ) from $e;
}
```

#### c) Исключения для flow control

```php
// ПЛОХО — исключение как замена if/else
function findActiveOrder(OrderId $id): Order
{
    try {
        return $this->repository->findActive($id);
    } catch (OrderNotFound) {
        return $this->repository->findArchived($id);
    }

    // Исключение — это НЕЧТО ИСКЛЮЧИТЕЛЬНОЕ.
    // "Заказа нет" — это нормальная бизнес-ситуация, не exception.
}

// ХОРОШО
function findActiveOrArchived(OrderId $id): Order
{
    return $this->repository->findActive($id)
        ?? $this->repository->findArchived($id)
        ?? throw new OrderNotFound($id->value());
}
```

#### d) «Exception factory» — лишняя абстракция

```php
// ПЛОХО — зачем?
class ExceptionFactory
{
    public static function orderNotFound(string $id): OrderNotFound
    {
        return new OrderNotFound($id);
    }

    public static function insufficientBalance(int $req, int $have): InsufficientBalance
    {
        return new InsufficientBalance($req, $have);
    }
}

// ХОРОШО — просто new
throw new OrderNotFound($id);
```

#### e) Глотать исключения молча

```php
// ПЛОХО
try {
    $this->eventBus->publish($event);
} catch (\Throwable $e) {
    // "не критично" — нет.
    // Если событие не ушло — состояние системы рассинхронизировано.
}

// ХОРОШО — лог + явное решение
try {
    $this->eventBus->publish($event);
} catch (\Throwable $e) {
    $this->logger->critical('Event publish failed', [
        'event' => get_class($event),
        'error' => $e->getMessage(),
    ]);
    // Решаем: retry? dead letter queue? throw?
    throw new EventPublishFailed($e) from $e;
}
```

#### f) Наследование от `\Exception`, а не от `RuntimeException`

```php
// ПЛОХО (мелочь, но важно)
class OrderNotFound extends \Exception {}
// \Exception — логическая ошибка (программист ошибся)
// \RuntimeException — ситуация в рантайме (данные, окружение)

// ХОРОШО
class OrderNotFound extends \RuntimeException {}
```

---

### 7. Итого — чеклист

| Вопрос | Ответ |
|--------|-------|
| Где бросать? | В слое, где **обнаружена** проблема |
| Куда переводить? | Инфраструктура → домен/application на границе |
| Что в контракте? | Только те исключения, которые **смысловые** для вызывающего |
| Catch или не catch? | Catch только если **можешь** обработать осмысленно |
| `throw ... from`? | Да, если переводишь — сохраняем причину |
| `\Exception` в сигнатуре? | Нет — только конкретные типы |
| Flow control через исключения? | Нет — это `null` / `Result` / `Option` |

> ⏱ 22.0s · prompt 4586 tok · output 3178 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

The user is asking me to continue the interview roleplay as a PHP backend developer. They want me to discuss SOLID principles — which one is most commonly violated in real code, why, with a before/after refactoring example.

Let me think about which principle is most commonly violated:

- S (Single Responsibility) — very commonly violated, but it's also the most ambiguous to define precisely
- O (Open/Closed) — often violated via if/else chains, but can be over-engineered
- L (Liskov Substitution) — commonly violated in inheritance hierarchies
- I (Interface Segregation) — commonly violated in fat interfaces
- D (Dependency Inversion) — commonly violated but often "solved" by just injecting concrete classes

In my experience as a PHP developer, I'd say the most commonly violated principle is **Single Responsibility**, but not in the obvious "god class" way. The more subtle and damaging violation is when a class has **multiple reasons to change** that are conflated — e.g., a service that both calculates business logic AND handles persistence AND sends notifications. This leads to classes that are hard to test, hard to understand, and change for the wrong reasons.

Actually, let me reconsider. In real PHP codebases I've seen, the most painful violation is actually **Liskov Substitution** combined with **Dependency Inversion** — specifically, the pattern where you have a base class/interface that's not truly polymorphic, and subclasses break the contract. But that's more Java-ish.

For PHP specifically, I think the most common and damaging violation is **Single Responsibility** manifested as "service classes that do too much" — the classic `OrderService` that creates the order, validates the customer, sends the email, records the audit log, publishes the event, and handles the payment. Each of these is a different "axis of change."

Let me go with SRP as the most commonly violated, but I'll frame it in a nuanced way — not "class has 20 methods" but "class has multiple *reasons to change*." I'll show a realistic PHP example with Symfony-style code.

Let me structure my answer:
1. Which principle and why (with nuance)
2. A realistic "before" example
3. The refactoring
4. Why this matters in practice

I'll keep it concrete and PHP-specific.

</details>

**Кандидат:**

## Самый частый нарушенный принцип: Single Responsibility

### Почему именно он

Остальные принципы SOLID — в основном про **структуру** (иерархии, интерфейсы, зависимости). Их нарушение видно в IDE: «а, тут `extends` не туда» или «интерфейс на 12 методов».

SRP — про **причины меняться**. И нарушение его не видно на уровне сигнатур: метод выглядит нормально, класс «не большой», а при этом каждая новая фича требует правку одного и того же файла. Это приводит к:

- конфликтам merge в git,
- тестам, которые падают из-за «не связанной» причины,
- коду, где бизнес-логика сплетена со способами доставки.

В PHP это особенно коварно, потому что DI-контейнер **поощряет** создание «сервисов» — и в них всё удобно засовывать: и валидацию, и отправку email, и запись в лог, и вызов внешнего API.

---

### Типовой пример нарушения

Классический `OrderService` в среднем Symfony-проекте:

```php
// Application/OrderService.php
final class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private PaymentGatewayInterface $payment,
        private AuditLogger $audit,
        private EventDispatcherInterface $dispatcher,
        private TokenGenerator $tokenGenerator,
    ) {}

    public function place(PlaceOrderRequest $request): Order
    {
        // 1. Валидация клиента
        $customer = $this->em->getRepository(Customer::class)
            ->findOneBy(['email' => $request->customerEmail]);

        if ($customer === null) {
            throw new CustomerNotFoundException($request->customerEmail);
        }

        if (!$customer->isVerified()) {
            throw new CustomerNotVerifiedException($customer->id());
        }

        // 2. Расчёт стоимости
        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = $this->em->getRepository(Product::class)
                ->findOneBy(['sku' => $item->sku]);

            if ($product === null) {
                throw new ProductNotFoundException($item->sku);
            }

            $subtotal += $product->price() * $item->quantity;
        }

        $shipping = $subtotal > 5000 ? 0 : 499;
        $discount = $customer->hasCoupon($request->coupon)
            ? (int) ($subtotal * 0.10)
            : 0;

        $total = $subtotal + $shipping - $discount;

        // 3. Создание заказа
        $order = new Order(
            customer: $customer,
            items: $request->items,
            subtotal: $subtotal,
            shipping: $shipping,
            discount: $discount,
            total: $total,
        );
        $this->em->persist($order);
        $this->em->flush();

        // 4. Оплата
        $paymentResult = $this->payment->charge(
            $customer->cardToken(),
            $total,
            $order->currency(),
        );

        if (!$paymentResult->success()) {
            $order->markAsFailed();
            $this->em->flush();
            throw new PaymentDeclinedException($paymentResult->reason());
        }

        $order->markAsPaid();

        // 5. Генерация токена для трекинга
        $trackingToken = $this->tokenGenerator->generate('TRK-', 12);
        $order->setTrackingToken($trackingToken);

        // 6. Отправка email
        $this->mailer->send(
            to: $customer->email(),
            subject: 'Order confirmed',
            template: 'orders/confirmation.html.twig',
            context: ['order' => $order],
        );

        // 7. Аудит
        $this->audit->log(
            action: 'order.placed',
            subject: "Order:{$order->id()}",
            actor: $customer->id(),
        );

        // 8. Событие
        $this->dispatcher->dispatch(new OrderPlacedEvent($order->id()));

        // 9. Лог
        $this->logger->info('Order placed', [
            'order_id' => $order->id(),
            'total'    => $total,
        ]);

        $this->em->flush();
        return $order;
    }
}
```

**Что здесь нарушено:**

| Причинa меняться | Строчки |
|-------------------|---------|
| Правила валидации клиента | 12–19 |
| Расчёт стоимости (скидки, доставка) | 23–40 |
| Состояние заказа | 43–75 |
| Логика оплаты | 77–88 |
| Формат tracking-токена | 91–93 |
| Шаблоны email | 96–101 |
| Формат аудита | 104–108 |
| Канал событий | 111 |
| Формат лога | 114–118 |

Каждая из этих причин — **разная команда, другой тимлид, другая скорость изменений**. Но всё в одном методе.

**Практический боль:**
- Меняем формат tracking-токена → правим `OrderService` → переписываем тесты на `place()`.
- Добавляем новый канал уведомлений (SMS) → опять `OrderService`.
- Меняем правила скидок → опять `OrderService`.
- Конфликт merge с коллегой, который в тот же день менял email-шаблон.

---

### Рефакторинг

Разбиваем по **осям изменений**, а не по «этапам»:

```php
// Domain/Order/PriceCalculator.php — ось: правила расчёта
final class PriceCalculator
{
    public function calculate(
        array $items,
        array $products,
        ?string $couponCode,
        bool $customerHasCoupon,
    ): OrderPricing
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $product = $products[$item->sku] ?? throw new ProductNotFoundException($item->sku);
            $subtotal += $product->price() * $item->quantity;
        }

        $shipping = $subtotal >= 5000 ? 0 : 499;
        $discount = $customerHasCoupon && $couponCode !== null
            ? (int) ($subtotal * 0.10)
            : 0;

        return new OrderPricing($subtotal, $shipping, $discount, $subtotal + $shipping - $discount);
    }
}
```

```php
// Domain/Order/Order.php — ось: состояние и инварианты (уже есть, добавляем)
final class Order
{
    // ...existing fields...

    public function pay(PaymentResult $result): void
    {
        if ($result->success()) {
            $this->status = OrderStatus::Paid;
        } else {
            $this->status = OrderStatus::PaymentFailed;
        }
    }

    public function assignTrackingToken(string $token): void
    {
        $this->trackingToken = $token;
    }
}
```

```php
// Application/Port/ — ось: «что нужно сделать» без «как»
interface PaymentProcessor
{
    public function charge(CardToken $token, int $amountCents, string $currency): PaymentResult;
}

interface OrderNotifier
{
    public function notifyPlaced(Order $order): void;
}

interface OrderAuditor
{
    public function logPlaced(Order $order, Customer $customer): void;
}

interface TrackingTokenGenerator
{
    public function generate(): string;
}
```

```php
// Application/PlaceOrder.php — use-case: тонкая оркестрация
final class PlaceOrder
{
    public function __construct(
        private CustomerRepository    $customers,
        private ProductRepository     $products,
        private OrderRepository       $orders,
        private PriceCalculator       $calculator,
        private PaymentProcessor      $payment,
        private OrderNotifier         $notifier,
        private OrderAuditor          $auditor,
        private TrackingTokenGenerator $tokens,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function __invoke(PlaceOrderRequest $request): Order
    {
        $customer = $this->customers->byEmail($request->customerEmail)
            ?? throw new CustomerNotFoundException($request->customerEmail);

        $this->assertVerified($customer);

        $products = $this->products->bySkus(array_map(fn($i) => $i->sku, $request->items));
        $pricing  = $this->calculator->calculate(
            $request->items,
            $products,
            $request->coupon,
            $customer->hasCoupon($request->coupon),
        );

        $order = Order::create($customer, $request->items, $pricing);
        $this->orders->save($order);

        $result = $this->payment->charge($customer->cardToken(), $pricing->total(), 'EUR');
        $order->pay($result);
        $this->orders->save($order);

        if ($result->success()) {
            $order->assignTrackingToken($this->tokens->generate());
            $this->orders->save($order);
            $this->notifier->notifyPlaced($order);
            $this->dispatcher->dispatch(new OrderPlacedEvent($order->id()));
        }

        $this->auditor->logPlaced($order, $customer);

        return $order;
    }

    private function assertVerified(Customer $customer): void
    {
        if (!$customer->isVerified()) {
            throw new CustomerNotVerifiedException($customer->id());
        }
    }
}
```

---

### Что изменилось

| Критерий | До | После |
|----------|----|----|
| Кол-во зависимостей в одном классе | 7 | 9 (но каждая — 1 роль) |
| Длина `place()` / `__invoke()` | ~70 строк | ~30 строк |
| Тест расчёта скидки | Тестировал `place()`, мокал 6 зависимостей | `new PriceCalculator()` — 0 зависимостей |
| Добавление SMS-канала | Правим `PlaceOrder` | Реализуем `OrderNotifier` заново, `PlaceOrder` не трогаем |
| Смена формата токена | Правим `PlaceOrder` | Меняем `TrackingTokenGenerator` |
| Merge-конфликты | 1 файл | Разные файлы |

---

### Почему это не «просто разбил класс на классы»

SRP — не «о класс = одна ответственность». Формулировка (Robert C. Martin):

> «У класса должна быть **одна причина** для изменения.»

Проверка простая: **если две команды (бизнес-аналитик по скидкам и разработчик email-инфраструктуры) не должны одновременно править один и тот же файл — у вас нарушение SRP.**

В рефакторинге выше:
- Аналитик скидок → правит `PriceCalculator`.
- Разработчик email → правит `EmailOrderNotifier`.
- Разработчик платежей → правит `PaymentGatewayProcessor`.

Ни один из них не трогает `PlaceOrder` и не конфликтует с остальными.

---

### Когда SRP **не** нужно применять

- Метод на 15 строк, который просто «складывает три вещи и передаёт дальше» — это не нарушение.
- В инфраструктурных адаптерах (Doctrine repository, HTTP client) — «несколько обязанностей» нормально, потому что они **тонкие**.
- В контроллерах Symfony — небольшая оркестрация допустима.

Правило: **если при изменении X вы не должны открывать файл Y — SRP соблюдён.**

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Да. SOLID — эвристики, а не законы физики

Каждый принцип — это **trade-off**: он снижает один класс издержек и повышает другой. Оправданное нарушение — когда издержки от «соблюдения» превышают издержки от нарушения.

---

### Пример 1: DIP — «DI для всего»

**Принцип:** зависьте от абстракций, а не от реализаций.

**Нарушение:** прямой `new` для value objects и простых утилит.

```php
// "По SOLID" — DI для создания Money
class OrderCalculator
{
    public function __construct(
        private MoneyFactory $moneyFactory, // интерфейс
    ) {}

    public function calculate(array $items): Money
    {
        $total = $this->moneyFactory->create(0, 'EUR');
        foreach ($items as $item) {
            $total = $total->add($this->moneyFactory->create($item->price, 'EUR'));
        }
        return $total;
    }
}

// Интерфейс, который никто никогда не имплементирует по-другому:
interface MoneyFactory
{
    public function create(int $cents, string $currency): Money;
}
```

```php
// Реальность:
class OrderCalculator
{
    public function calculate(array $items): Money
    {
        $total = new Money(0, 'EUR');
        foreach ($items as $item) {
            $total = $total->add(new Money($item->price, 'EUR'));
        }
        return $total;
    }
}
```

**Почему нарушение оправдано:**

| | "По SOLID" | Прямой `new` |
|--|-----------|-------------|
| Зависимостей для теста | 1 (мок фактории) | 0 |
| Код на создание Money | 3 строки | 1 строка |
| Реальных альтернативных реализаций | 0 | 0 |
| Понятность | «Зачем здесь фактория?» | Очевидно |

DIP полезен, когда **реально есть** несколько реализаций (Doctrine vs Eloquent, Stripe vs Adyen). Для `Money`, `OrderId`, `DateTime` — альтернатив нет. Введение абстракции здесь создаёт **ложную расширяемость**: интерфейс, который никогда не будет реализован иначе.

---

### Пример 2: OCP — «закрытая» иерархия вместо открытой

**Принцип:** сущности расширяемы без модификации.

**Нарушение:** закрытый enum + `match` вместо открытой иерархии классов.

```php
// "По SOLID" — открытая иерархия, готовая к расширению
abstract class DiscountStrategy
{
    abstract public function apply(Money $amount): Money;
}

class PercentageDiscount extends DiscountStrategy { /* ... */ }
class FixedAmountDiscount extends DiscountStrategy { /* ... */ }
// Завтра кто-то добавит LoyaltyPointsDiscount, SeasonalDiscount, ...

// Контрибьютор: "А можно добавить мой вид скидки?"
// → Да, просто наследуйтесь. Не трогаем ни одну существующую строку.
```

```php
// Реальность для конкретного проекта:
enum DiscountType: string
{
    case Percentage = 'percentage';
    case Fixed      = 'fixed';
    case None       = 'none';
}

// В доменной модели:
final class Order
{
    public function applyDiscount(DiscountType $type, int $value): void
    {
        match ($type) {
            DiscountType::Percentage => $this->discount = (int)($this->subtotal * $value / 100),
            DiscountType::Fixed      => $this->discount = min($value, $this->subtotal),
            DiscountType::None       => $this->discount = 0,
        };
        // Если добавим 4-й case — компилятор/IDE подсветит, что match неполон.
        // Если забудем обработать — runtime-исключение, а не тихий bug.
    }
}
```

**Почему нарушение оправдано:**

В этом проекте скидки **определяет продукт** — 3 типа, и они не меняются чаще раза в год. Расширение — это **осознанное решение команды**, а не «приходит плагиновый разработчик».

| | Открытая иерархия | Закрытый enum |
|--|-------------------|---------------|
| Безопасность | Новый класс может нарушить инварианты `Order` | `match` — исчерпывающий, IDE проверяет |
| Расширение | «Просто унаследуйтесь» — без ревью | Явное изменение enum + match — видно в PR |
| Тестирование | 3+ класса + полиморфные тесты | 1 класс, 3 ветки |
| Когнитивная нагрузка | Нужно знать, какие подклассы существуют | Видно в одном месте |

OCP имеет смысл, когда **расширение делает третья сторона**, которой вы не контролируете (плагинная система, ядро фреймворка). Если расширение делает **ваша команда** через PR — закрытая иерархия безопаснее.

---

### Критерий: когда нарушение оправдано

```
Нарушение оправдано, если:

1. Альтернативная реализация НЕ существует и НЕ предвидится.
2. Расширение делает ВАША команда, а не внешний потребитель.
3. Стоимость абстракции (кода, когнитивной нагрузки, тестов)
   превышает стоимость её отсутствия.
4. Вы можете точно назвать момент, когда нарушение придётся
   пересмотреть.
```

Если хотя бы один пункт не выполняется — вероятно, стоит вернуться к «каноническому» SOLID.

---

### Чего я **не** оправдываю

- Нарушение **ISP** «потому что лень вырывать метод» — если 3 из 8 методов интерфейса не используются, это реальный запах.
- Нарушение **LSP** «потому что так быстрее» — подмена поведения в наследнике ломает вызывающий код молча. Это не trade-off, а баг.
- Нарушение **SRP** «потому что класс на 800 строк» — это не оправдание, а признак того, что причина меняться одна, но она огромная. Разбить — не «нарушить», а «выполнить SRP на другом уровне».

---

**Итого:** SOLID — это навигационные карты, а не GPS. Они указывают направление, но не запрещают срезать через поле, если вы точно знаете, где выход.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## OCP без наследования

OCP — это не «используй наследование». Это **«новое поведение появляется без правки существующего кода»**. Механизмов много, и большинство сильнее наследования.

---

### 1. Composition + Strategy (самый частый)

Вместо иерархии классов — **объект, который делает** + **контейнер, который вызывает**.

```php
// Оркестратор НЕ знает, какие стратегии существуют
interface PriceModifier
{
    public function apply(OrderPricing $pricing, OrderContext $ctx): OrderPricing;
}

final class OrderPricingEngine
{
    public function __construct(
        private readonly iterable<PriceModifier> $modifiers, // итерация, не наследование
    ) {}

    public function calculate(OrderContext $ctx): OrderPricing
    {
        $pricing = OrderPricing::zero();

        foreach ($this->modifiers as $modifier) {
            $pricing = $modifier->apply($pricing, $ctx);
        }

        return $pricing;
    }
}

// Конкретные модификаторы — простые классы, без общего base
final class LoyaltyDiscount implements PriceModifier
{
    public function apply(OrderPricing $p, OrderContext $ctx): OrderPricing
    {
        if ($ctx->customer->loyaltyPoints() > 100) {
            return $p->withDiscount($p->subtotal * 10);
        }
        return $p;
    }
}

final class ShippingFee implements PriceModifier
{
    public function apply(OrderPricing $p, OrderContext $ctx): OrderPricing
    {
        $fee = $p->subtotal >= 5000 ? 0 : 499;
        return $p->withShipping($fee);
    }
}
```

```php
// Регистрация — в конфиге или через tag
// Symfony: tagged_iterator
public function __construct(
    #[Autowire(tags: ['app.price_modifier'])]
    private iterable<PriceModifier> $modifiers,
) {}

// Новый скидочный механизм:
// 1. Создать класс
// 2. Поставить tag
// 3. Не трогать OrderPricingEngine ни одной строкой
```

**Почему сильнее наследования:** нет общего базового класса, нет `super::`, нет иерархии. Каждый модификатор — самодостаточный.

---

### 2. Data-driven rules (поведение = данные)

Вместо кода — **таблица правил**, которые интерпретирует движок.

```php
// rules.json (или БД)
[
  { "id": "loyalty_10", "when": "customer.loyalty > 100", "action": "discount_percent:10" },
  { "id": "free_ship",  "when": "subtotal >= 5000",        "action": "shipping:0" },
  { "id": "bday",       "when": "customer.birthday_week",  "action": "discount_percent:15" }
]

// Движок — один на всё, не меняется
final class RuleEngine
{
    public function __construct(
        private readonly iterable<Rule> $rules,
    ) {}

    public function apply(OrderContext $ctx): OrderPricing
    {
        $pricing = OrderPricing::zero();

        foreach ($this->rules as $rule) {
            if ($rule->matches($ctx)) {
                $pricing = $rule->execute($pricing);
            }
        }

        return $pricing;
    }
}
```

**Новое правило = новая строка в JSON/БД. Ноль строк кода.**

**Почему сильнее:** нет компиляции, нет деплоя для добавления правила. Ограничение: логика ограничена выражениями, которые умеет парсить движок.

---

### 3. Event-driven (pub/sub)

Поведение появляется **асинхронно**, через подписку на событие.

```php
// Оркестратор:
final class OrderPlacedHandler
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function handle(Order $order): void
    {
        $this->dispatcher->dispatch(new OrderPlacedEvent($order));
        // Ничего не знает, кто прослушивает
    }
}

// Новые подписчики — независимые классы:
final class SendReceiptEmail implements MessageHandlerInterface
{
    public function __invoke(OrderPlacedEvent $event): void { /* ... */ }
}

final class UpdateAnalytics implements MessageHandlerInterface
{
    public function __invoke(OrderPlacedEvent $event): void { /* ... */ }
}

// Новый подписчик = новый класс + регистрация.
// OrderPlacedHandler не меняется никогда.
```

**Почему сильнее:** стороны даже **не знают о существовании друг друга**. Это максимальная степень «open for extension».

**Цена:** теряете синхронность и простоту отладки.

---

### 4. Registry + ключ (plugin pattern)

Механизм выбора реализации через **ключ**, а не тип.

```php
final class PaymentGatewayRegistry
{
    /** @var array<string, PaymentGateway> */
    private array $gateways = [];

    public function register(string $id, PaymentGateway $gateway): void
    {
        $this->gateways[$id] = $gateway;
    }

    public function get(string $id): PaymentGateway
    {
        return $this->gateways[$id]
            ?? throw new GatewayNotFoundException($id);
    }
}

// Использование:
$gateway = $this->registry->get($order->preferredGateway); // 'stripe', 'adyen', 'paypal'
$gateway->charge($amount);
```

```php
// Подключение нового шлюза:
// 1. Имплементировать PaymentGateway
// 2. register('new_gateway', new NewGateway())
// Ни одна существующая строка кода не меняется
```

**Почему сильнее:** нет иерархии, нет `instanceof`, нет `match` по классу. Выбор — по **бизнес-значению** (id), а не по типу.

---

### 5. Higher-order functions (PHP 8.1+)

Самый лёгкий вариант: поведение = замыкание.

```php
final class Pipeline
{
    /** @var array<callable(mixed): mixed> */
    private array $steps = [];

    public function pipe(callable $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    public function run(mixed $input): mixed
    {
        foreach ($this->steps as $step) {
            $input = $step($input);
        }
        return $input;
    }
}

// Использование:
$result = (new Pipeline())
    ->pipe(fn($order) => $order->validate())
    ->pipe(fn($order) => $order->calculatePrice())
    ->pipe(fn($order) => $order->charge())
    ->run($order);

// Новый шаг = одна строчка, новый класс не нужен
```

**Почему сильнее:** нет классов, нет DI, нет интерфейсов. Максимально лёгкая «расширяемость».

**Цена:** теряете типобезопасность (кроме union types), сложность отладки, DI-контейнер не помогает.

---

### Сравнение механизмов

| Механизм | Сила OCP | Типобезопасность | Сложность | Когда |
|----------|----------|-----------------|-----------|-------|
| Наследование | Слабая (нужен base class) | Высокая | Средняя | Редко оправдан |
| Strategy + composition | Средняя | Высокая | Низкая | Несколько вариантов, один контракт |
| Data-driven | Высокая | Низкая | Средняя | Правила часто меняются, логика простая |
| Events | Максимальная | Низкая (тип события) | Высокая | Стороны независимы, асинхронность OK |
| Registry | Высокая | Средняя | Низкая | Выбор по ключу, не по типу |
| Functions | Средняя | Низкая | Минимальная | Простые трансформации |

---

### Правило выбора

```
Есть 2–5 вариантов, один контракт
    → Strategy + composition

Поведение = данные, часто меняется, логика простая
    → Data-driven

Стороны не должны знать друг о друге
    → Events

Выбор по ключу, не по типу
    → Registry

Одно-два преобразования, нет DI
    → Functions

Ничего из этого не подходит, нужно 20+ уровней, shared state
    → Наследование (да, иногда)
```

Наследование — **последний** инструмент, а не первый.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — про «причину меняться» на любом уровне абстракции

Формулировка не привязана к классу:

> «У **модуля** должна быть одна причина для изменения.»

«Модуль» — это любая единица кода, которую вы можете изолировать и менять независимо.

---

### 1. Функция / метод

```php
// ПЛОХО — 3 причины меняться в одном методе:
public function sendWelcomeEmail(Customer $c): void
{
    // Причина 1: шаблон письма меняется
    $subject = 'Welcome, ' . $c->name();
    $body    = 'Thanks for joining! Your code: ' . $c->promoCode();

    // Причина 2: канал доставки меняется (email → SMS → push)
    $this->mailer->send($c->email(), $subject, $body);

    // Причина 3: бизнес-правило «кому присылать» меняется
    if ($c->isB2B() && $c->country() === 'RU') {
        return; // не присылаем B2B из РФ
    }
}
```

```php
// ХОРОШО — каждая причина в своём месте:
// Domain: кому присылать
final class WelcomeEligibility
{
    public function isEligible(Customer $c): bool
    {
        return !($c->isB2B() && $c->country() === 'RU');
    }
}

// Application: оркестрация
final class SendWelcome
{
    public function __invoke(Customer $c): void
    {
        if (!$this->eligibility->isEligible($c)) {
            return;
        }

        $message = $this->composer->build($c);
        $this->delivery->send($message);
    }
}
```

---

### 2. Файл / модуль

```
// ПЛОХО — один файл на всё:
app/Services/OrderService.php
  - создаёт заказ
  - считает стоимость
  - отправляет email
  - пишет в БД
  - шлёт в analytics
  - проверяет лимит частоты
```

```
// ХОРОШО — файлы по осям изменения:
app/Domain/Order/Order.php              — инварианты
app/Domain/Order/PriceCalculator.php    — правила расчёта
app/Application/PlaceOrder.php          — оркестрация
app/Infrastructure/Mail/OrderMailer.php — доставка
app/Infrastructure/Analytics/Tracker.php— аналитика
```

Критерий: **если команда A и команда B не должны одновременно править один файл — SRP на уровне модуля.**

---

### 3. Конфигурация / схема БД

```sql
-- ПЛОХО — одна таблица на всё:
CREATE TABLE events (
    id BIGSERIAL,
    type VARCHAR,           -- причина 1: типы событий
    payload JSONB,          -- причина 2: структура payload
    channel VARCHAR,        -- причина 3: канал доставки
    delivery_rule JSONB,    -- причина 4: правила маршрутизации
    ...
);

-- ХОРОШО — разделение по осям:
CREATE TABLE event_types (...);       -- каталог
CREATE TABLE event_payloads (...);    -- данные
CREATE TABLE delivery_channels (...); -- маршрутизация
```

---

### 4. Микросервис / бина

```
# ПЛОХО — один сервис:
payment-service:
  - валидация платежа
  - зарядка (Stripe/Adyen)
  - отправка receipts
  - рассылка по email
  - логирование для compliance
```

```
# ХОРОШО — по осям изменения (если команды разные):
payment-core/       — валидация + зарядка
receipt-service/    — формирование и доставка receipts
compliance-logger/  — аудит
```

Опять же: **не разбиваем «по функциям», а по «кому и как часто менять»**.

---

### 5. Даже в UI / шаблонах

```twig
{# ПЛОХО — один шаблон: #}
{# emails/order_confirmation.html.twig #}
  - логотип и branding (меняет дизайн-команда)
  - расчёт итоговой суммы (меняет бизнес-аналитик)
  - ссылка на трекинг (меняет backend)
  - кнопка «оцените нас» (меняет growth-команда)
```

```twig
{# ХОРОШО — компоненты: #}
emails/partials/branding.html.twig
emails/partials/pricing.html.twig
emails/partials/tracking.html.twig
emails/partials/upsell.html.twig
```

---

### Универсальный критерий

```
SRP нарушен, если:
  причина A меняется чаще, чем причина B
  И обе причины живёт в одной единице
  И для изменения A нужно открывать B (или тестировать B).

SRP соблюдён, если:
  для изменения X вы открываете файл/класс/модуль X
  и НИ ОДИН другой файл не трогаете.
```

---

### Частая ошибка

Применять SRP **только** к классам и делать «маленькие классы по 20 строк», но при этом один файл на 400 строк, один метод на 80 строк, одна БД-таблица на 15 сущностей. Это SRP на уровне класса соблюдён, а на уровне модуля — нет.

И наоборот: разбить на микросервисы, но внутри каждого — один `GodService` на 2000 строк. SRP на уровне бина соблюдён, на уровне класса — нет.

**SRP — это lens, а не checklist. Пропускайте его на уровне, где реально «боль» от совместных изменений.**

> ⏱ 23.0s · prompt 7577 tok · output 3291 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о внутреннем устройстве DI-контейнера Symfony. Это технический вопрос, ожидаемый ответ — детальное объяснение механики: компиляция, autowiring, lazy-инстанцирование. Нужно объяснить на уровне "знаю, что происходит под капотом", а не поверхностно.

Структура:
1. Общее: что такое контейнер, какие типы сервисов (public/private, shared/synthetic, shared per-request/per-service)
2. Компиляция: что делает CompilerPass, какие шаги (ResolveParameterPlaceHolders, AutowirePass, RegisterServiceSubscribersPass, InlineServiceDefinitionsPass, RemoveUnusedDefinitionsPass, CheckCircularReferencesPass и т.д.)
3. Autowiring: как работает — тип-гид, alias, autowiring aliases, как определяется тип (hint), что с nullable, что с union types, что с generics (не поддерживается), как работает #[Autowire], #[Target]
4. Момент запроса: что делает get(), как работает proxy для lazy services, как работает shared instance, что с synthetic services, что с per-request services
5. Ловушки и нюансы: circular refs, container as service, container-locus, когда proxy создаётся, что делает "inline"

Нужно дать ответ с примерами кода и схемой. Не перегружать, но показать, что понимаю внутренности.

Формат — как в предыдущих ответах: заголовки, примеры, таблицы, итог.

</details>

**Кандидат:**

## Внутреннее устройство DI-контейнера Symfony

### 1. Общая модель

Контейнер — это **фактически скомпилированный PHP-класс**, который наследует от `Container`. В runtime он — просто `new Container()` с набором методов, где каждый сервис = один метод `get_*()`.

```
src/ContainerXX/ContainerXX.php  (генерируемый файл)
├── public services  → публичные методы
├── private services → private-методы
└── shared instances → $this->privates[...] / $this->services[...]
```

Ключевые атрибуты определения сервиса:

| Атрибут | Значение |
|---------|----------|
| `public` | Доступен через `$container->get()` |
| `private` (по умолчанию) | Внутренний, может быть инлайн-нут |
| `shared` (по умолчанию) | Один экземпляр на всё приложение |
| `prototype` | Новый экземпляр на каждый `get()` |
| `synthetic` | Экземпляр подставляется извне (например, из request) |
| `lazy` | Прокси, реальный объект создаётся при первом обращении к методу |
| `abstract` | Не инстанцируется сам, но наследуется |

---

### 2. Что происходит при компиляции

Компиляция — это **трансформация дерева определений** в код. Запускается при `bin/console cache:clear` или `cache:warmup`.

#### 2.1 Входные данные

Контейнер собирает `Definition` из:
- `services.yaml` / `services.php`
- Аттрибутов на классах (`#[Service]`, `#[Autowired]`)
- Bundle-конфигураций
- `services_<env>.yaml`

Каждое определение — это `Definition` с:
- классом (или factory)
- аргументами конструктора
- method calls
- file/after-инстанцирование
- tags
- alias
- `shared`, `public`, `lazy`, `synthetic`

#### 2.2 CompilerPasses — конвейер трансформаций

Каждый pass — класс, реализующий `CompilerPassInterface`. Запускаются по очереди. Ключевые:

```
┌─────────────────────────────────────────────────────────────┐
│ 1. RegisterServiceSubscribersPass                           │
│    Распознать сервисы с #[MessageHandler], #[EventSubscriber]│
│    и зарегистрировать их как subscribers                    │
├─────────────────────────────────────────────────────────────┤
│ 2. ResolveParameterPlaceHoldersPass                         │
│    Заменить %env(...)%, %kernel.debug% и т.п. на реальные   │
│    значения или на ExpressionLanguage-выражения             │
├─────────────────────────────────────────────────────────────┤
│ 3. ResolveInstanceofConditionalsPass                        │
│    Развернуть instanceof-условия (например, LoggerInterface  │
│    → конкретный логгер)                                     │
├─────────────────────────────────────────────────────────────┤
│ 4. ResolveChildDefinitionsPass                              │
│    Развернуть child service (наследование определений)       │
├─────────────────────────────────────────────────────────────┤
│ 5. AutowirePass                                             │
│    ← КЛЮЧЕВОЙ. Автоинжект зависимостей по типам             │
├─────────────────────────────────────────────────────────────┤
│ 6. RegisterAutoconfigureAttributesPass                      │
│    Обработать атрибуты типа #[AsCommand], #[AsMessageHandler]│
├─────────────────────────────────────────────────────────────┤
│ 7. CheckCircularReferencesPass                              │
│    Найти циклические зависимости → либо бросить,            │
│    либо разрешить через proxy                               │
├─────────────────────────────────────────────────────────────┤
│ 8. RemoveUnusedDefinitionsPass                              │
│    Удалить сервисы, которые никто не использует              │
│    (только private + shared + не tag'нутые)                 │
├─────────────────────────────────────────────────────────────┤
│ 9. InlineServiceDefinitionsPass                             │
│    Инлайн-нуть одноразовые private-сервисы прямо            │
│    в место использования → меньше объектов в runtime         │
├─────────────────────────────────────────────────────────────┤
│ 10. CheckExceptionOnInvalidReferenceBehaviorPass            │
│     Проверить, что все ссылки разрешены                     │
└─────────────────────────────────────────────────────────────┘
```

Порядок важен: `AutowirePass` должен пройти до `InlineServiceDefinitionsPass`, иначе автоинжект сломается.

#### 2.3 Результат компиляции

Генерируется PHP-класс:

```php
// var/cache/dev/ContainerXXXX/ContainerXXXX.php
class ContainerXXXX extends Container
{
    protected array $privates = [];

    public function get_App_Service_OrderService(): OrderService
    {
        return $this->privates['app.service.order_service'] ??= new OrderService(
            $this->get_App_Repository_CustomerRepository(),
            $this->get_App_Repository_ProductRepository(),
            $this->get_App_Service_PaymentGateway(),
            // ...
        );
    }

    private function get_App_Repository_CustomerRepository(): CustomerRepository
    {
        return $this->privates['app.repository.customer_repository'] ??= new CustomerRepository(
            $this->getDoctrine()->getManager(),
        );
    }
}
```

Обратите на `??=` — это **memoization shared-инстанса**: первый вызов создаёт, остальные возвращают кэш.

---

### 3. Autowiring — как работает

#### 3.1 Механика

`AutowirePass` берёт `Definition`, смотрит конструктор класса и для каждого параметра:

1. Определяет **типовую подсказку** (type hint) из рефлексии.
2. Ищет в контейнере сервис с **точным совпадением типа**.
3. Если не нашёл — ищет по **alias** (например, `logger` → `LoggerInterface`).
4. Если не нашёл — бросает `ServiceCircularReferenceException` или `AutowiredServiceNotFoundException`.

```php
// services.yaml
services:
    App\Service\EmailService:
        public: true
        # Autowiring enabled by default in framework bundle

# EmailService.php
class EmailService
{
    public function __construct(
        private MailerInterface $mailer,        // ← autowire
        private LoggerInterface $logger,         // ← autowire
        private CustomerRepository $customers,   // ← autowire
        private int $maxRetries = 3,             // ← default value
    ) {}
}
```

#### 3.2 Типовые подсказки (type hints)

| Что в сигнатуре | Что ищет autowire |
|----------------|-------------------|
| `MailerInterface $m` | Сервис типа `MailerInterface` |
| `string $s` | `null` (если нет `default` или `#[Autowire]`) |
| `int $i` | `null` (аналогично) |
| `?LoggerInterface $l` | Если нет — `null` |
| `array $a` | `null` |
| `iterable<PriceModifier> $mods` | `iterable` → ищет tagged_iterator |
| `float $f` | `null` |
| `array{a: int, b: string} $cfg` | Не поддерживает (только static analysis) |

#### 3.3 Как autowire находит сервис

```
Алгоритм поиска (упрощённо):
1. Точное совпадение типа:
   - Тип = FQCN → ищем сервис с классом = FQCN
   - Тип = interface → ищем сервис с alias = interface

2. Alias:
   - Если в контейнере есть alias "LoggerInterface" → "App\Logger\MyLogger"
   - Autowire следит по alias

3. Fallback (для scalars):
   - Если нет default value → ошибка
   - Если есть default value → используется default
   - Если есть #[Autowire(env: 'APP_KEY')] → берётся из env
```

#### 3.4 `#[Autowire]` — переопределение

```php
class EmailService
{
    public function __construct(
        #[Autowire(service: 'transactional_mailer')]
        private MailerInterface $mailer,

        #[Autowire(env: 'SMTP_PORT')]
        private int $smtpPort,

        #[Autowire('%app.email.from%')]
        private string $fromEmail,
    ) {}
}
```

#### 3.5 `#[Target]` — разрешение ambiguity

```yaml
services:
    App\Payment\StripeGateway:
        tags: ['app.payment_gateway']
    App\Payment\AdyenGateway:
        tags: ['app.payment_gateway']
```

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $primary,      // ← ambiguous!
        #[Target('primary')]
        private PaymentGateway $fallback,     // ← тоже ambiguous!
    ) {}
}
```

```yaml
services:
    App\Payment\StripeGateway:
        tags:
            - name: app.payment_gateway
              alias: primary
    App\Payment\AdyenGateway:
        tags:
            - name: app.payment_gateway
              alias: fallback
```

#### 3.6 Границы autowiring

| Ситуация | Что происходит |
|----------|---------------|
| Два сервиса одного интерфейса, без `#[Target]` | `ServiceCircularReferenceException` (или `AmbiguousServiceException`) |
| `union type` (`int\|string`) | Не поддерживает — ищет точное совпадение |
| `intersection type` (`Countable&Iterator`) | Поддерживает с PHP 8.1 |
| Generics (`Collection<Order>`) | Не поддерживает — видит только `Collection` |
| `self` в параметре | Не разрешается (инfinite loop) |
| `static` / `callable` | Не поддерживает |
| `readonly`-классы | Работает, autowire не знает о readonly |

---

### 4. Момент запроса: `get()`

Когда код делает `$container->get(OrderService::class)`:

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Container::get() → делегирует в ContainerXXXX::get()     │
├──────────────────────────────────────────────────────────────┤
│ 2. Проверяет $this->privates['app.service.order_service']    │
│    - Если есть → возвращает (shared)                          │
│    - Если нет → создаёт                                       │
├──────────────────────────────────────────────────────────────┤
│ 3. Создаёт: new OrderService(...)                             │
│    - Для каждого аргумента:                                    │
│      a. Если это shared service → get_*() (рекурсивно)        │
│      b. Если это prototype → new на каждый вызов              │
│      c. Если это lazy proxy → возвращает proxy, НЕ реальный   │
│      d. Если это scalar → значение из параметра               │
│      e. Если это synthetic → $this->getService() (из request) │
├──────────────────────────────────────────────────────────────┤
│ 4. Сохраняет в $this->privates[...]= (memoization)            │
├──────────────────────────────────────────────────────────────┤
│ 5. Возвращает                                                  │
└──────────────────────────────────────────────────────────────┘
```

#### 4.1 Lazy services — прокси

```yaml
services:
    App\Gateway\StripeGateway:
        class: App\Gateway\StripeGateway
        lazy: true   # ← прокси
```

Компиляция генерирует **прокси-класс**:

```php
// Генерируемый прокси
class StripeGatewayProxy extends StripeGateway
{
    private bool $initialized = false;
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function charge(int $amount): ChargeResult
    {
        if (!$this->initialized) {
            $real = $this->container->get_StripeGateway();
            $this->initialize($real);  // копирует свойства
            $this->initialized = true;
        }
        return parent::charge($amount);
    }
}
```

**Ключевой момент:** реальный `StripeGateway` с его конструктором и зависимостями создаётся **только при первом вызове метода**, а не при `new`.

#### 4.2 Shared vs Prototype

```yaml
services:
    # shared (по умолчанию)
    App\Service\OrderService: ~
    # Каждый $container->get(OrderService::class) возвращает ОДИН и тот же объект

    # prototype
    App\Worker\BatchProcessor:
        shared: false
    # Каждый get() — НОВЫЙ экземпляр
```

**Когда prototype:**
- Сервис содержит состояние, которое должно сбрасываться
- Используется в параллельных контекстах
- Worker в пуле

#### 4.3 Synthetic services

```yaml
services:
    App\Service\CurrentUserProvider:
        synthetic: true   # ← не инстанцируется контейнером
```

```php
// В Listener:
$container->set(CurrentUserProvider::class, new CurrentUserProvider($request->getUser()));
```

Типичное использование: `Request`, `Session`, `CurrentUser` — объекты, которые **приходят извне** (HTTP-запрос), а не создаются контейнером.

---

### 5. Ловушки и нюансы

#### 5.1 Circular references

```php
// ПЛОХО — цикл
class A {
    public function __construct(private B $b) {}
}
class B {
    public function __construct(private A $a) {}
}
```

Компилятор бросит `ServiceCircularReferenceException`.

**Решения:**
- Один из сервисов сделать `lazy: true` → прокси разрывает цикл.
- Вынести общую логику в третий сервис.
- Использовать `setter injection` (method call после конструктора).
- Использовать **события** вместо прямой зависимости.

```yaml
services:
    App\Service\A:
        lazy: true   # ← прокси, цикл разрешён
```

#### 5.2 `Container` как зависимость

```php
// ПЛОХО
class MyService
{
    public function __construct(
        private ContainerInterface $container,  // ← антипаттерн
    ) {}

    public function doSomething(): void
    {
        $other = $this->container->get(OtherService::class);
        // ← потеряна статическая проверка, IDE не помогает
    }
}
```

**Почему плохо:**
- Теряется типобезопасность.
- Теряется autowiring для `$other`.
- Служит «якорем» — делает `MyService` зависимым от всего контейнера.
- Сложно тестировать (мок контейнера = мок всего мира).

**Когда допустимо:**
- Динамическая загрузка по ключу (plugin system).
- `Container` как `service_locator` (Symfony сам делает это под капотом).

#### 5.3 `public: true` — не «для доступа», а «для автоинжекта»

```yaml
services:
    App\Service\OrderService:
        public: true   # ← доступно через $container->get()

# Но даже private-сервисы можно автоинжектнуть!
class AnotherService
{
    public function __construct(
        private OrderService $orders,  // ← работает, даже если OrderService private
    ) {}
}
```

`public` нужен **только** для доступа через `$container->get()` — например, из контроллера, если вы не используете autoconfigure, или из console-команды.

#### 5.4 Inlining — «невидимые» сервисы

```yaml
services:
    App\Repository\CustomerRepository:
        shared: true       # по умолчанию
        # private (по умолчанию)

    App\Service\OrderService:
        public: true
```

Если `CustomerRepository` используется **только** в `OrderService` и нигде больше, `InlineServiceDefinitionsPass` **впишет его конструктор прямо в** `OrderService`:

```php
// До inline:
public function get_App_Service_OrderService(): OrderService
{
    return new OrderService(
        $this->get_App_Repository_CustomerRepository(),
        $this->get_App_Repository_ProductRepository(),
    );
}

// После inline (CustomerRepository вписан):
public function get_App_Service_OrderService(): OrderService
{
    return new OrderService(
        new CustomerRepository($this->get_Doctrine_Manager()),  // ← инлайн!
        $this->get_App_Repository_ProductRepository(),
    );
}
```

**Эффект:** меньше объектов в runtime, меньше памяти, быстрее запуск. Но **только** для `private + shared + не tag'нутых + не lazy`.

#### 5.5 Environment-specific контейнеры

```
var/cache/dev/ContainerDevXXX/ContainerDevXXX.php   ← dev
var/cache/prod/ContainerProdXXX/ContainerProdXXX.php ← prod
```

Это **разные скомпилированные классы**. В prod `debug`-сервисы удалены, `lazy` может быть выключен (для скорости), параметры подставлены из `env`.

---

### 6. Итого

| Этап | Что происходит |
|------|---------------|
| Сборка определений | YAML + атрибуты + bundle-конфиги → `Definition` |
| CompilerPasses | Трансформация: autowire, inline, cleanup |
| Генерация | `Definition` → PHP-класс `ContainerXXXX` |
| Runtime `get()` | Memoization + ленивое создание зависимостей |
| Lazy proxy | Прокси создаётся сразу, реальный объект — при первом методе |
| Inline | Одноразовые private-сервисы вписываются в место использования |

**Главная мысль:** DI-контейнер Symfony — это не «магия в runtime». Это **компилятор**, который превращает декларативные определения в оптимизированный PHP-код. Runtime — это просто `new` + `??=` memoization.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Циклические зависимости: диагностика и решения

### 1. Что это

```
A → B → A
```

Symfony бросает:

```
Service "App\Service\OrderService" has dependency on "App\Service\InventoryService",
which has dependency on "App\Service\OrderService".
This should never happen.
```

### 2. Первый вопрос: а нужен ли этот цикл?

В 80% случаев цикл — **симптом того, что два сервиса делят одну ответственность**. Прежде чем «разрешать» цикл, стоит спросить: а не один ли это предмет?

```php
// A: OrderService → зависит от B
class OrderService {
    public function __construct(private InventoryService $inventory) {}
    public function place(Order $o): void {
        $this->inventory->reserve($o->items());
        // ...
    }
}

// B: InventoryService → зависит от A
class InventoryService {
    public function __construct(private OrderService $orders) {}
    public function reserve(array $items): void {
        // ...
        $this->orders->notifyReserved($items); // ← зачем?
    }
}
```

Здесь `InventoryService` «уведомляет» `OrderService` — но на самом деле **уведомление — не ответственность инвентаря**. Это симптом: два класса спорят о том, кто владеет потоком.

---

### 3. Способы разрулить (от правильного к костылю)

---

#### a) Вынесение общей логики в третий сервис

Самый частый и самый правильный вариант.

```php
// Было:
// OrderService → InventoryService → OrderService

// Стало:
// OrderService → ReservationService
// InventoryService → ReservationService

// ReservationService не зависит ни от кого из них
final class ReservationService
{
    public function __construct(
        private readonly ItemRepository $items,
        private readonly EventDispatcherInterface $events,
    ) {}

    public function reserve(array $items): ReservationResult
    {
        // логика резервирования
        // ...

        $this->events->dispatch(new ItemsReservedEvent($items));
        // ↑ не знает, кто слушает

        return new ReservationResult(...);
    }
}
```

```php
final class OrderService
{
    public function __construct(
        private readonly ReservationService $reservation,
    ) {}

    public function place(Order $o): void
    {
        $this->reservation->reserve($o->items());
        // ...
    }
}

final class InventoryService
{
    public function __construct(
        private readonly ReservationService $reservation,
    ) {}

    public function release(array $items): void
    {
        $this->reservation->cancel($items);
    }
}
```

**Критерий:** если вы можете назвать **одну сущность**, которой принадлежит общая логика — вынесите её в отдельный сервис.

---

#### b) Event-driven (pub/sub)

Один сервис **узнаёт** о состоянии другого через событие, а не через прямую зависимость.

```php
// OrderService бросает событие, не зная слушателей
final class OrderService
{
    public function __construct(
        private readonly ReservationService $reservation,
        private readonly EventDispatcherInterface $events,
    ) {}

    public function place(Order $o): void
    {
        $this->reservation->reserve($o->items());
        $this->events->dispatch(new OrderPlacedEvent($o->id()));
    }
}

// InventoryService подписывается — не знает, кто бросил
#[AsMessageHandler]
final class OnOrderPlaced
{
    public function __invoke(OrderPlacedEvent $event): void
    {
        // реакция на заказ: логирование, аналитика, инвентарь
    }
}
```

**Плюс:** стороны не знают друг о друге. Максимальная степень развязки.

**Минус:** теряется синхронность. Если `InventoryService` **обязан** отработать до того, как `OrderService` вернёт ответ — события не подходят.

**Когда использовать:** реакция не критична по времени, есть несколько слушателей, стороны реально независимы.

---

#### c) `lazy: true` (Symfony-специфичный)

Symfony генерирует **прокси-класс**, который создаётся сразу, а реальный объект — при первом вызове метода. Это **разрывает** цикл на уровне конструктора.

```yaml
services:
    App\Service\OrderService: ~
    App\Service\InventoryService:
        lazy: true   # ← прокси
```

```php
// Что видит OrderService:
class OrderService
{
    public function __construct(
        private InventoryService $inventory, // ← это InventoryServiceProxy
    ) {}

    public function place(Order $o): void
    {
        $this->inventory->reserve($o->items()); // ← здесь создаётся реальный объект
    }
}

// Что видит InventoryService:
class InventoryService
{
    public function __construct(
        private OrderService $orders, // ← это OrderService (уже создан, потому что мы В его методе)
    ) {}
}
```

**Механика:**

```
1. $container->get(OrderService)
   → new OrderService(InventoryServiceProxy)   ← прокси, дёшево
   → сохраняет в cache

2. orderService->place($o)
   → $this->inventory->reserve()
   → прокси вызывает $container->get(InventoryService)
   → new InventoryService(OrderService)         ← уже в cache!
   → сохраняет в cache
   → вызывает реальный reserve()
```

**Когда допустимо:**
- Цикл — **неизбежный** (например, `EntityManager` ↔ `Repository` в Doctrine).
- Оба сервиса **истинно** зависят друг от друга и не могут быть разведены.
- Вы понимаете, что это **технический** цикл, а не концептуальный.

**Когда НЕ допустимо:**
- Если цикл маскирует то, что A и B — на самом деле одна сущность.
- Если порядок инициализации имеет значение (A должен быть полностью готов, когда B его вызывает).

---

#### d) Setter injection (method call)

Зависимость не в конструкторе, а в методе.

```php
// Без цикла в конструкторе:
final class OrderService
{
    private ?InventoryService $inventory = null;

    // Конструктор — без InventoryService
    public function __construct(
        private readonly EventDispatcherInterface $events,
    ) {}

    // Зависимость подставляется после
    public function setInventory(InventoryService $inventory): void
    {
        $this->inventory = $inventory;
    }

    public function place(Order $o): void
    {
        $this->inventory?->reserve($o->items());
        // ...
    }
}

// services.yaml
services:
    App\Service\InventoryService:
        calls:
            - [setInventory, ['@App\Service\OrderService']]
```

**Плюс:** нет цикла в конструкторе, IDE видит.

**Минус:**
- Состояние объекта **не гарантировано** до вызова setter.
- `@param` в конструкторе — это контракт. Setter — «хрупкий контракт».
- Symfony **не поддерживает** setter injection через autowiring (нужно явно писать `calls`).

**Когда использовать:** редко. В основном — в наследуемых классах, где конструктор уже занят.

---

#### e) Service Locator (осторожно!)

```php
final class OrderService
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {}

    public function place(Order $o): void
    {
        /** @var InventoryService $inventory */
        $inventory = $this->container->get(InventoryService::class);
        $inventory->reserve($o->items());
    }
}
```

**Почему это плохо:**
- Теряется типобезопасность.
- IDE не подскажет.
- `OrderService` теперь зависит от **всего** контейнера.
- Тестирование = мок контейнера.

**Когда допустимо:**
- **Плагинная система**: сервис получает неизвестный набор обработчиков по ключу.
- **Консоль-команды**: где сервисы подтягиваются динамически.

Для «разрыва цикла» — **не использовать**.

---

### 4. Таблица решений

| Способ | Когда | Сила развязки | Стоимость |
|--------|-------|---------------|-----------|
| Вынесение общего в 3-й сервис | A и B спорят об одной сущности | Максимальная | Нужно понять домен |
| Events | Стороны независимы, асинхронность OK | Максимальная | Потеря синхронности |
| `lazy: true` | Технический цикл, оба сервиса реальны | Средняя (маскирует) | Нулевая |
| Setter injection | Конструктор занят, наследование | Низкая | Хрупкость |
| Service Locator | Плагинная система, динамическая загрузка | Низкая | Потеря типобезопасности |

---

### 5. Чеклист: что делать, когда видите цикл

```
1. Откройте оба класса. Спросите:
   «Если бы A не существовал, что делал бы B?»
   «Если бы B не существовал, что делал бы A?»

2. Если ответ: «ничего не делал бы» —
   они делят одну ответственность → вынесите в 3-й сервис.

3. Если ответ: «делал бы X, но без Y» —
   они независимы, но синхронны → events.

4. Если ответ: «делал бы X, и Y не имеет значения,
   пока не вызовется метод» → lazy: true.

5. Если ничего из этого не подходит —
   пересмотрите доменную модель.
   Цикл — это почти всегда «я не понял, где граница контекста».
```

---

### 6. Реальный пример: Symfony + Doctrine

Частый «цикл»: `EntityManager` ↔ `Repository`.

```php
// EntityManager зависит от Repository (чтобы persist/flush)
// Repository зависит от EntityManager (чтобы работать с БД)
```

Symfony **не** использует `lazy: true` здесь. Вместо этого — `synthetic` сервис:

```yaml
services:
    doctrine.orm.entity_manager:
        synthetic: true   # ← подставляется из Request/Kernel

    App\Repository\OrderRepository:
        arguments:
            $em: '@doctrine.orm.entity_manager'
```

`EntityManager` создаётся **до** контейнера (в `Kernel::boot()`), и подставляется в контейнер как готовый объект. Цикла нет, потому что одна сторона **входит извне**.

Это — ещё один паттерн: **разорвать цикл, вынеся один из сервисов за границы контейнера.**

> ⏱ 35.5s · prompt 15449 tok · output 4622 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

The user is asking about how to organize post-registration side effects (sending email + updating search index) in a Symfony application without blocking the response. This is a classic "fan-out" / async side-effects problem.

Let me think about the options:

1. **Symfony Messenger + async transport (RabbitMQ/SQS/Redis)** — the canonical Symfony way
2. **Database outbox pattern** — for guaranteed delivery
3. **Event listeners with `sync` flag** — synchronous (not suitable here)
4. **`enqueue` / external queue** — similar to messenger
5. **Fire-and-forget with `register_shutdown_function`** — anti-pattern, but exists
6. **Cron/worker that polls DB** — outbox pattern
7. **Kafka** — for high-throughput
8. **Redis Streams** — lighter than Kafka
9. **Symfony's `async` transport** — built-in

I should structure this as:
- Problem framing (why not sync)
- Options with trade-offs table
- Deep dive into the Messenger + outbox approach (most robust)
- Code examples
- Edge cases and failure handling

Let me make this practical and show real code, not just theory. I'll focus on the production-grade approach (Messenger + outbox) while showing lighter alternatives for simpler cases.

Key considerations:
- At-least-once vs exactly-once delivery
- Order guarantee
- Retry/backoff
- Idempotency
- Observability (dead letter, metrics)
- Testing
- When NOT to use async (consistency requirements)

I'll structure it as a progression from simple to robust, with clear trade-offs at each level.

</details>

**Кандидат:**

## Пост-регистрация: email + поиск без блокировки

### Постановка

```
POST /register
  → 201 Created  (пользователь получил ответ)
  → [async] email
  → [async] search index
```

Ключевое требование: **ответ пользователю не зависит от успешности side-effects**.

---

### Варианты от простого к robust

| # | Подход | Delivery | Сложность | Когда |
|---|--------|----------|-----------|-------|
| 1 | `sync` listener | в-том-же-request | Низкая | Не наш кейс |
| 2 | Messenger + `async` (RabbitMQ) | at-least-once | Средняя | 90% проектов |
| 3 | Outbox + worker | at-least-once, гарантировано | Высокая | Финансы, критичность |
| 4 | Kafka / Redis Streams | at-least-once, ordered | Высокая | High throughput |
| 5 | `fire-and-forget` (curl, exec) | best-effort | Минимальная | Тесты, скрипты |

Разберу 2, 3 и 4.

---

### Вариант 2: Symfony Messenger + RabbitMQ (основной)

#### Архитектура

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────┐
│  Controller  │       │   Message Bus    │       │   Workers    │
│              │──────▶│                  │──────▶│              │
│  register()  │       │  RabbitMQ       │       │  SendEmail   │
│              │       │  (persistent)   │       │  UpdateIndex │
└──────────────┘       └──────────────────┘       └──────────────┘
       │                         │
       ▼                         ▼
  HTTP 201              dead_letter_queue
```

#### 1. Событие (домен)

```php
// Domain/Event/UserRegistered.php
namespace App\Domain\Event;

final readonly class UserRegistered
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $name,
    ) {}
}
```

#### 2. Публикация в контроллере

```php
// Controller/UserController.php
final class UserController
{
    public function __construct(
        private readonly RegisterUser $registerUser,  // use-case
        private readonly MessageBusInterface $bus,
    ) {}

    public function register(RegisterRequest $request): Response
    {
        $user = $this->registerUser($request);

        // Публикуем событие — НЕ ожидаем обработки
        $this->bus->dispatch(new UserRegistered(
            userId: $user->id,
            email: $user->email,
            name: $user->name,
        ));

        return new Response(null, 201, ['Location' => "/users/{$user->id}"]);
        // ← Пользователь получил ответ. Workers работают в фоне.
    }
}
```

> **Важно:** `dispatch()` здесь — **синхронная публикация в очередь**. Она не ждёт обработки. Месадж уходит в RabbitMQ, и функция возвращает. Обработка — в отдельном процессе.

#### 3. Handlers (consumers)

```php
// Application/Handler/SendWelcomeEmail.php
namespace App\Application\Handler;

#[AsMessageHandler]
final class SendWelcomeEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(UserRegistered $event): void
    {
        $this->mailer->send(
            to: $event->email,
            subject: 'Welcome, ' . $event->name,
            template: 'emails/welcome.html.twig',
        );
    }
}
```

```php
// Application/Handler/UpdateSearchIndex.php
#[AsMessageHandler]
final class UpdateSearchIndex
{
    public function __construct(
        private readonly SearchIndexClient $search,
    ) {}

    public function __invoke(UserRegistered $event): void
    {
        $this->search->index('users', [
            'id'    => $event->userId,
            'name'  => $event->name,
            'email' => $event->email,
        ]);
    }
}
```

#### 4. Конфигурация транспортов

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: '%env(RABBITMQ_DSN)%'
                # amqp://user:pass@host:5672/%2f?queue_name=app_messages
                max_retries: 3
                retry_delay: 1000        # 1с
                multiplier: 2           # 2с, 4с
                dead_letter_transport: failed
            failed:
                dsn: '%env(RABBITMQ_DSN_FAILED)%'
                # amqp://user:pass@host:5672/%2f?queue_name=app_failed
        routing:
            'App\Domain\Event\UserRegistered': async
            'App\Application\Message\SendEmail': async
            # ...
```

#### 5. Worker (отдельный процесс)

```bash
# Procfile / Docker
worker: bin/console messenger:consume async -vv --time-limit=3600
```

```yaml
# docker-compose.yml
services:
    worker:
        build: .
        command: bin/console messenger:consume async -vv
        depends_on:
            - rabbitmq
        restart: always
```

#### 6. Наблюдаемость

```bash
# Просмотр очереди
bin/console messenger:stats

# Просмотр failed
bin/console messenger:failed:show

# Retry конкретного сообщения
bin/console messenger:failed:retry 42

# Ре-публикация
bin/console messenger:failed:publish 42
```

---

### Вариант 3: Outbox Pattern (когда «ушло в очередь» ≠ «доставлено»)

#### Проблема с Вариантом 2

```
1. Транзакция: INSERT user → COMMIT
2. dispatch(UserRegistered) → RabbitMQ
3. CRASH между 1 и 2 → письмо потеряно
```

Или наоборот:

```
1. dispatch(UserRegistered) → RabbitMQ (OK)
2. INSERT user → ROLLBACK
3. Worker обрабатывает событие для несуществующего пользователя
```

#### Решение: Outbox

```
┌─────────────────────────────────────────────────────────────┐
│  Transaction:                                               │
│    INSERT INTO users (...)                                  │
│    INSERT INTO outbox (aggregate_id, event, payload)        │
│  COMMIT                                                     │
└─────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Poller (отдельный процесс, cron или loop)                   │
│    SELECT * FROM outbox WHERE status = 'pending' LIMIT 100  │
│    → publish to RabbitMQ                                    │
│    → UPDATE outbox SET status = 'published'                 │
└─────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Worker (как в Варианте 2)                                  │
└─────────────────────────────────────────────────────────────┘
```

#### Код

```php
// Domain/Outbox/OutboxEntry.php
final readonly class OutboxEntry
{
    public function __construct(
        public string $id,
        public string $aggregateId,
        public string $eventType,
        public string $payloadJson,
        public int $createdAt,
        public int $publishedAt = 0,
        public int $attempts = 0,
    ) {}
}
```

```php
// Application/RegisterUser.php
final class RegisterUser
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OutboxRepository $outbox,
    ) {}

    public function __invoke(RegisterRequest $request): User
    {
        $user = User::create($request);
        $this->em->persist($user);

        // В ТОЙ ЖЕ транзакции — запись в outbox
        $event = new UserRegistered(
            userId: $user->id,
            email: $user->email,
            name: $user->name,
        );

        $this->outbox->append(
            aggregateId: $user->id,
            eventType: UserRegistered::class,
            payload: serialize($event),
        );

        $this->em->flush();  // ← ATOMIC: user + outbox вместе

        return $user;
    }
}
```

```php
// Infrastructure/OutboxPoller.php (отдельный процесс)
#[AsCommand(name: 'outbox:publish')]
final class OutboxPublishCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
    ) {}

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $entries = $this->em->createQuery(
            'SELECT o FROM OutboxEntry o WHERE o.publishedAt = 0 ORDER BY o.createdAt ASC'
        )->setMaxResults(100)->getResult();

        foreach ($entries as $entry) {
            $event = unserialize($entry->payloadJson);

            try {
                $this->bus->dispatch($event, [
                    'bus' => 'outbox_bus',  // отдельный bus с persistent queue
                ]);

                $entry->publishedAt = time();
                $this->em->flush();

                $out->writeln("Published: {$entry->id}");
            } catch (\Throwable $e) {
                $entry->attempts++;
                $this->em->flush();

                if ($entry->attempts >= 5) {
                    // → dead letter / alert
                    $out->writeln("FAILED: {$entry->id}", OutputInterface::VERBOSITY_QUIET);
                }
            }
        }

        return Command::SUCCESS;
    }
}
```

```bash
# Запуск: непрерывный цикл
while true; do
    bin/console outbox:publish
    sleep 2
done
```

#### Trade-offs Outbox

| | Плюс | Минус |
|--|------|-------|
| Гарантия | Пользователь + событие — атомарно | Дополнительная таблица |
| Delivery | at-least-once | Задержка = poll interval (1–5с) |
| Ordering | По `createdAt` | Нет strict global order |
| Наблюдаемость | `SELECT * FROM outbox` — всё видно | Extra infra |
| Crash | Не потеряет события | Если poller упал — события ждут |

---

### Вариант 4: Kafka / Redis Streams (high throughput)

Когда:
- >10k событий/сек
- Нужен **global ordering** per partition
- Несколько консьюмеров с независимыми группами
- Ретреен-поток (replay)

```php
// Publish
use RdKafka\KafkaProducer;

class KafkaEventPublisher
{
    public function __construct(
        private KafkaProducer $producer,
    ) {}

    public function publish(string $topic, UserRegistered $event): void
    {
        $message = new \RdKafka\Message();
        $message->payload = json_encode([
            'event'  => UserRegistered::class,
            'data'   => $event,
            'ts'     => time(),
        ]);
        $message->key = $event->userId;  // partitioning по пользователю

        $producer->produce(RD_KAFKA_PRODUCER_PARTITION_ANY, 0, $message->payload);
        $producer->flush(RD_KAFKA_FLUSH_NON_BLOCKING, 1000);
    }
}
```

| | Kafka | RabbitMQ |
|--|-------|----------|
| Ordering | Per-partition, global replay | Per-queue, нет replay |
| Throughput | Миллионы msg/s | Тысячи msg/s |
| Replay | Да (offset) | Нет |
| Complexity | ZK/KRaft, partitions, consumer groups | Broker, queues |
| Когда | Event sourcing, CQRS, audit trail | Обычные async задачи |

---

### Вариант 5: Fire-and-forget (только для нес критичного)

```php
// АНТИПАТТЕРН для прода
// Но для email «спам-рассылки» или «не критичного уведомления»:

class FireAndForgetMailer
{
    public function send(string $to, string $subject, string $body): void
    {
        // 1. Записываем в БД (fallback)
        $this->em->persist(new PendingEmail($to, $subject, $body));
        $this->em->flush();

        // 2. Пытаемся отправить синхронно, с коротким таймаутом
        try {
            $this->mailer->send($to, $subject, $body);
            $this->em->remove($pendingEmail);
            $this->em->flush();
        } catch (\Throwable $e) {
            // Не бросаем — пользователь не должен ждать
            $this->logger->warning('Email deferred', ['to' => $to]);
            // Cron подхватит из БД
        }
    }
}
```

**Когда OK:** email «спасибо, что подписались», где потеря = не критична.
**Когда НЕ OK:** подтверждение заказа, платёж, reset password.

---

### Сравнение для вашего кейса

| Критерий | Messenger+RabbitMQ | Outbox | Kafka |
|----------|-------------------|--------|-------|
| Задержка до email | ~50мс | ~2–5с | ~50мс |
| Потеря при crash | Возможна (gap) | **Нет** | Нет |
| Сложность infra | RabbitMQ | БД + poller | Kafka cluster |
| Ordering | Нет гарантии | Per-aggregate | Per-partition |
| Replay | Нет | Да (из БД) | Да |
| Наблюдаемость | `messenger:stats` | `SELECT * FROM outbox` | Kafka UI |
| Для email + search | **Оптимален** | Если email критичен | Overkill |

---

### Рекомендация

Для 90% Symfony-проектов (email + search index после регистрации):

```
Messenger + RabbitMQ + 2 workers
```

Добавить **Outbox**, только если:
- Email — **единственное подтверждение** (нет retry-UI у пользователя).
- Есть регуляторные требования («доказательство отправки»).
- Crash-резилиентность критична (финансы, healthcare).

Кafka — только если:
- Уже есть Kafka в infra.
- >10k msg/s.
- Нужен replay.
- Event sourcing / CQRS.

---

### Чего НЕ делать

```php
// 1. Синхронно в контроллере
public function register(): Response
{
    $user = $this->registerUser($request);
    $this->mailer->send(...);        // ← блокирует ответ на 200мс+
    $this->search->index(...);       // ← блокирует ответ на 500мс+
    return new Response(null, 201);
}

// 2. register_shutdown_function
register_shutdown_function(function() use ($event) {
    $this->mailer->send(...);  // ← не гарантируется, не ретраится, не логится
});

// 3. В background-процессе через exec()
exec('php bin/console app:send-email ' . escapeshellarg($user->id) . ' &');
// ← zombie-процессы, нет retry, нет observability

// 4. В отдельном микросервисе, который «просто слушает»
// ← overkill для email + search
```

> ⏱ 29.9s · prompt 22367 tok · output 4078 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о системе контроля доступа в Symfony: аутентификация, роли, voters, и когда что использовать. Это продолжение собеседования на PHP-бэкенд.

Нужно:
1. Разобрать архитектуру: firewall → authenticator → token → authorization
2. Аутентификация: как устроены authenticators (custom, form login, JWT, etc.)
3. Авторизация: roles vs voters
4. Конкретные примеры кода
5. Практические критерии выбора
6. Ловушки и антипаттерны

Структура ответа:
- Общая модель (firewall → auth → token → access control)
- Аутентификация: Authenticator, Token, как работает
- Авторизация: roles, attributes, voters
- Примеры кода
- Когда voter, когда роли
- Антипаттерны
- Итого

Пишу на русском, как и весь диалог.

</details>

**Кандидат:**

## Контроль доступа в Symfony: полная модель

### 1. Общая архитектура

```
HTTP Request
    │
    ▼
┌─────────────────────────────────────────────────────┐
│  FIREWALL                                           │
│                                                     │
│  ┌─────────────┐    ┌──────────────┐    ┌────────┐ │
│  │ Authenticator│───▶│  Token      │───▶│ Access │ │
│  │ (кто ты?)   │    │  (что ты?)  │    │ Control│ │
│  └─────────────┘    └──────────────┘    │ (что   │ │
│                                         │  можешь)│ │
│                                         └────────┘ │
└─────────────────────────────────────────────────────┘
    │
    ▼
Controller / Service
```

Три вопроса:
1. **Кто ты?** → Аутентификация (Authenticator)
2. **Что ты?** → Токен (роли, атрибуты)
3. **Что тебе можно?** → Авторизация (Access Control / Voters)

---

### 2. Аутентификация

#### 2.1 Firewall — входные ворота

```yaml
# config/packages/security.yaml
security:
    firewalls:
        api:
            pattern: ^/api
            stateless: true
            provider: app_user_provider
            jwt: ~          # ← Authenticator

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
            logout:
                path: app_logout
```

Каждый firewall — **отдельная цепочка authenticators**. Запрос попадает в один и только один firewall по `pattern`.

#### 2.2 Authenticator — «кто ты?»

```php
// Security/EmailPasswordAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Security;

class EmailPasswordAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private Security $security,
    ) {}

    public function supports?($request: Request): ?bool
    {
        return $request->getPathInfo() === '/login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // ← Здесь: проверка пароля, throttling, 2FA, whatever
        $user = $this->security->getUser(); // из UserProvider

        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        $badge = new SelfValidatingPassportBadge($user);

        return new SelfValidatingPassport($badge);
    }

    public function createToken(Passport $passport): TokenInterface
    {
        $user = $passport->getUser();

        return new UsernamePasswordToken(
            user: $user,
            firewallsKey: 'main',
            roles: $user->getRoles(),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse('/dashboard');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse('/login');
    }
}
```

#### 2.3 Passport — «доказательство»

`Passport` — это пакет бейджей, который описывает, **как** пользователь аутентифицировался:

```php
// Простой: email + пароль
new SelfValidatingPassport(new SelfValidatingPassportBadge($user))

// С 2FA:
new SelfValidatingPassport(
    new SelfValidatingPassportBadge($user),
    new TwoFactorBadge($otpCode),
)

// С OAuth:
new SelfValidatingPassport(
    new SelfValidatingPassportBadge($user),
    new OAuthBadge($token),
)
```

#### 2.4 Token — «что ты сейчас»

Токен хранится в request и передаётся в сервисы:

```php
// В любом сервисе:
class OrderService
{
    public function __construct(
        private readonly Security $security,  // не Container!
    ) {}

    public function place(PlaceOrderRequest $req): Order
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new UnauthorizedException('Must be logged in');
        }

        // ...
    }
}
```

---

### 3. Авторизация

#### 3.1 Роли

```php
interface UserInterface
{
    public function getRoles(): array;
    // → ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR']
}
```

```yaml
# security.yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/api/internal, roles: ROLE_API_KEY }
        - { path: ^/dashboard, roles: ROLE_USER }
        - { path: ^/public, roles: PUBLIC_ACCESS }
```

**Роли — это статичные, глобальные, иерархические метки.**

```yaml
# Иерархия
security:
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER, ROLE_EDITOR]
        ROLE_EDITOR: [ROLE_USER]
```

`ROLE_ADMIN` → автоматически имеет `ROLE_USER` и `ROLE_EDITOR`.

#### 3.2 Attributes (атрибуты объекта)

```php
// Security/User.php
class User
{
    public function getAttributes(): array
    {
        return [
            'department' => 'engineering',
            'region'     => 'eu',
            'plan'       => 'enterprise',
        ];
    }
}
```

```yaml
# security.yaml
security:
    access_control:
        - { path: ^/api/eu, attributes: { region: eu } }
        - { path: ^/premium, attributes: { plan: enterprise } }
```

#### 3.3 Voters — «контекстная» авторизация

Voter — это **объектно-ориентированная** проверка доступа. Не «у тебя есть роль X», а «у тебя есть доступ к **этому конкретному объекту** в **данном контексте**».

```php
// Security/OrderVoter.php
namespace App\Security;

use App\Domain\Order\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrderVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Я голосую только по Order
        if (!$subject instanceof Order) {
            return false;
        }

        return in_array($attribute, ['edit', 'delete', 'view', 'export'], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /** @var Order $order */
        $order = $subject;

        return match ($attribute) {
            'view'   => $this->canView($order, $user),
            'edit'   => $this->canEdit($order, $user),
            'delete' => $this->canDelete($order, $user),
            'export' => $this->canExport($order, $user),
            default  => false,
        };
    }

    private function canView(Order $order, User $user): bool
    {
        return $order->ownerId === $user->id
            || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canEdit(Order $order, User $user): bool
    {
        return $this->canView($order, $user)
            && !$order->isLocked()
            && $order->status !== OrderStatus::Closed;
    }

    private function canDelete(Order $order, User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles(), true)
            || ($order->ownerId === $user->id && !$order->isLocked());
    }

    private function canExport(Order $order, User $user): bool
    {
        return $this->canView($order, $user)
            && in_array('ROLE_EXPORTER', $user->getRoles(), true);
    }
}
```

#### 3.4 Использование

```php
// В контроллере:
class OrderController
{
    public function edit(
        Order $order,                          // ← entity resolver
        #[IsGranted('edit')]                    // ← voter проверяет
        void $granted,
    ): Response
    {
        // Если voter вернул false → 403 (автоматически)
        // Если true → метод выполняется
        $order->update($request->request->all());
        $this->em->flush();
        return new Response('Updated');
    }
}
```

```php
// В twig:
{% if is_granted('edit', order) %}
    <a href="{{ path('order_edit', { id: order.id }) }}">Edit</a>
{% endif %}
```

```php
// В сервисе:
class OrderService
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authz,
    ) {}

    public function export(Order $order): string
    {
        $this->authz->isGranted('export', $order);
        // → бросает AccessDeniedException, если нет

        return $this->generateCsv($order);
    }
}
```

---

### 4. Когда voter, когда роли

| Ситуация | Механизм | Почему |
|----------|----------|--------|
| «Только админы» | `ROLE_ADMIN` | Статичная, глобальная роль |
| «Только свои заказы» | **Voter** | Зависит от объекта (owner) |
| «Только если статус ≠ closed» | **Voter** | Зависит от состояния объекта |
| «Только в регионе EU» | `attributes` или **Voter** | Зависит от контекста пользователя |
| «Только enterprise-план» | `attributes` | Статичный атрибут пользователя |
| «Только если есть роль X И объект в статусе Y» | **Voter** | Комбинированное условие |
| «Только на определённых роутах» | `access_control` | Глобальный маршрут, не объект |

**Правило:**

```
Если ответ на «можно ли?» зависит ТОЛЬКО от роли пользователя
    → ROLE_XXX / access_control

Если ответ зависит от ОБЪЕКТА (чей он, в каком статусе, что в данных)
    → Voter
```

---

### 5. Антипаттерны

#### a) Роли вместо voters

```php
// ПЛОХО — 50 ролей на «кто что может»
interface UserInterface
{
    public function getRoles(): array;
    // → ['ROLE_USER', 'ROLE_ORDER_OWNER', 'ROLE_ORDER_EDITOR',
    //    'ROLE_INVOICE_VIEWER', 'ROLE_INVOICE_EXPORTER', ...]
}
```

```php
// ХОРОШО — 3–5 ролей + voters для контекста
// → ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ACCOUNTANT']
// Остальное — OrderVoter, InvoiceVoter
```

#### b) Voter, который не проверяет объект

```php
// ПЛОХО — voter без контекста
class RoleVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array($attribute, $token->getUser()->getRoles(), true);
        // ← Это дублирует access_control. Зачем voter?
    }
}
```

Voter без контекста объекта — это просто роль в другом месте.

#### c) `isGranted` в цикле

```php
// ПЛОХО — N+1 на авторизацию
foreach ($orders as $order) {
    if ($this->authz->isGranted('edit', $order)) {
        $editable[] = $order;
    }
}
// Каждый вызов — новый voter → N вызовов

// ХОРОШО — batch или предфильтрация
$editable = $this->orderRepo->findEditableBy($this->security->getUser());
```

#### d) Аутентификация и авторизация в одном месте

```php
// ПЛОХО
public function handle(Order $order): void
{
    if (!$this->security->getUser()) {
        throw new UnauthorizedException();
    }
    if (!$this->security->isGranted('ROLE_ADMIN')) {
        throw new AccessDeniedException();
    }
    if ($order->ownerId !== $this->security->getUser()->id) {
        throw new AccessDeniedException();
    }
    // ← Три проверки, которые должны быть в разных местах
}
```

```php
// ХОРОШО
#[IsGranted('edit')]  // ← voter (включает проверку «свой ли объект»)
public function handle(Order $order): void
{
    // Только бизнес-логика
}
```

#### e) `PUBLIC_ACCESS` для всего

```yaml
# ПЛОХО
access_control:
    - { path: ^/api, roles: PUBLIC_ACCESS }
    # ← Весь API публичный. Аутентификация «опциональная».

# ХОРОШО
access_control:
    - { path: ^/api/public, roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: ROLE_USER }
```

---

### 6. Итоговая модель

```
┌──────────────────────────────────────────────────────────────────────┐
│  LAYER 1: Access Control (global, per-route)                         │
│  - Роли: ROLE_ADMIN, ROLE_USER                                       │
│  - Атрибуты: region, plan                                            │
│  - Когда: «этот URL доступен только X»                               │
├──────────────────────────────────────────────────────────────────────┤
│  LAYER 2: Voters (per-object, contextual)                            │
│  - OrderVoter: canView, canEdit, canDelete                           │
│  - InvoiceVoter: canExport                                           │
│  - Когда: «этот объект доступен, если ...»                           │
├──────────────────────────────────────────────────────────────────────┤
│  LAYER 3: Business rules (in domain)                                 │
│  - Order::canBePaid()                                                │
│  - Invoice::isOverdue()                                              │
│  - Когда: «состояние объекта не позволяет действие»                  │
└──────────────────────────────────────────────────────────────────────┘
```

**Layer 1** — «ты вообще можешь сюда зайти?»
**Layer 2** — «ты можешь это конкретное действие с этим конкретным объектом?»
**Layer 3** — «состояние объекта позволяет это действие?» (инварианты домена)

Каждый слой отвечает на свой вопрос. Не подменяйте один другим.

> ⏱ 27.9s · prompt 26129 tok · output 3919 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении Value Objects в Doctrine при требованиях неизменяемости и валидного состояния. Это глубокий вопрос на стыке DDD и ORM.

Ключевые проблемы:
1. Doctrine работает с mutable-объектами (setter'ы, hydration)
2. VO по определению immutable (readonly, финальные)
3. Doctrine не знает о VO — для него это просто данные
4. Гидратация: Doctrine создаёт объект через reflection, может нарушить инварианты
5. Dirty checking: Doctrine сравнивает состояния, а VO — это "всё или ничего"

Способы маппинга:
1. Composite types (кастомные типы) — маппинг на отдельные колонки
2. Single column (JSONB / serialized) — один столбец
3. Embeddable (embedded objects) — составной тип из нескольких полей
4. Custom Type (Doctrine Type) — кастомный тип
5. Встроенные enum (PHP 8.1 + Doctrine enum type)
6. Composite key как VO

Подводные камни:
- Hydration bypass'ит конструктор VO
- Doctrine сравнивает по ссылке, а не по значению (reference equality vs value equality)
- Dirty checking может не сработать, если VO заменён на новый экземпляр с тем же значением
- readonly properties — Doctrine не умеет
- Валидация при гидратации — как заставить?
- Performance: JSONB vs отдельные колонки
- Индексация: можно ли индексировать VO?
- Миграции: как менять структуру VO?

Структурирую ответ:
1. Кратко: почему это проблема
2. Каждый способ с кодом, плюсами/минусами
3. Подводные камни (общие и специфичные)
4. Рекомендации

</details>

**Кандидат:**

## Value Object в Doctrine: маппинг

### Почему это проблема

Doctrine — mutable-фреймворк. Она:
- Создаёт объекты через **reflection**, минуя конструктор
- Хранит **ссылки**, а не значения
- Сравнивает по **идентичности** (`===`), а не по значению
- Hydrate'ит **напрямую в свойства**

Value Object — обратное:
- Создаётся только через конструктор
- Неизменяем (readonly)
- Равен по **значению**
- Всегда валиден (инварианты в конструкторе)

Эти две модели **столкновятся** без явного соглашения.

---

### Способ 1: Composite Type (кастомный Doctrine Type)

Самый частый. Один VO = несколько колонок.

```php
// Domain/Money.php
namespace App\Domain;

final readonly class Money
{
    public function __construct(
        public int    $amountCents,
        public string $currency,
    ) {
        if ($amountCents < 0) {
            throw new \InvalidArgumentException('Amount must be >= 0');
        }
    }

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }
}
```

```php
// Infrastructure/Persistence/MoneyType.php
namespace App\Infrastructure\Persistence;

use App\Domain\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MoneyType extends Type
{
    public function getName(): string
    {
        return 'money';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Возвращаем SQL для ОДНОЙ колонки (INT)
        // А в маппинге будем использовать ДВЕ колонки через composite
        return $platform->getIntTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        // $value — это array, потому что composite
        return new Money(
            amountCents: (int) $value['amount'],
            currency:    (string) $value['currency'],
        );
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new \TypeError('Expected Money, got ' . get_debug_type($value));
        }

        return [
            'amount'   => $value->amountCents,
            'currency' => $value->currency,
        ];
    }

    public function getMappedSQLTypes(array $column): array
    {
        return ['amount' => Types::INTEGER, 'currency' => Types::STRING];
    }
}
```

```php
// Entity
#[ORM\Entity]
class Invoice
{
    #[ORM\Id]
    #[ORM\Column(type: Types::UUID)]
    public readonly string $id;

    #[ORM\Column(type: MoneyType::NAME, nullable: false)]
    public readonly Money $amount;

    #[ORM\Column(type: MoneyType::NAME, nullable: false)]
    public readonly Money $total;
}
```

**Результат в БД:**

```sql
CREATE TABLE invoices (
    id UUID PRIMARY KEY,
    amount_amount INT NOT NULL,
    amount_currency VARCHAR(3) NOT NULL,
    total_amount INT NOT NULL,
    total_currency VARCHAR(3) NOT NULL
);
```

| Плюсы | Минусы |
|-------|--------|
| Индексация по компонентам | Гидратация идёт через `convertToPHPValue` — **ок** |
| SQL-запросы на компоненты (`WHERE amount_amount > 1000`) | `convertToDatabaseValue` вызывается на **каждый** flush |
| Миграции — обычные | Composite types не поддерживаются во всех DBAL-драйверах |
| | Методы `getMappedSQLTypes` — нестандартный API |

---

### Способ 2: Embedded (встроенный объект)

Doctrine имеет встроенную поддержку embedded objects.

```php
// Domain/Money.php (тот же)

// Infrastructure/Persistence/Embedded/MoneyEmbedded.php
namespace App\Infrastructure\Persistence\Embedded;

use App\Domain\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class MoneyEmbedded
{
    #[ORM\Embedded(class: Money::class)]
    private Money $money;

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public function get(): Money
    {
        return $this->money;
    }
}
```

**Проблема:** Doctrine embedded работает с **mutable** объектами. Она hydrate'ит свойства напрямую, минуя конструктор.

```php
// Doctrine hydrate'ит так (упрощённо):
$money = new Money();  // ← конструктор с invariants НЕ вызывается!
$money->amountCents = 100;  // ← прямой доступ к property
$money->currency = 'EUR';
```

**Для immutable VO это ломает инварианты.** Работает только если VO — простой data-класс без валидации в конструкторе.

| Плюсы | Минусы |
|-------|--------|
| Встроенная поддержка Doctrine | **Нарушает инварианты** VO |
| Миграции — автоматические | Нет контроля над гидратацией |
| | Dirty checking работает по свойствам (может быть некорректно для VO) |

---

### Способ 3: JSONB / serialized (один столбец)

```php
// Domain/Money.php (тот же)

// Infrastructure/Persistence/MoneyJsonType.php
namespace App\Infrastructure\Persistence;

use App\Domain\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MoneyJsonType extends Type
{
    public function getName(): string
    {
        return 'money_json';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
        // или: return 'JSONB'; // для PostgreSQL
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        return new Money(
            amountCents: (int) $data['amount'],
            currency:    (string) $data['currency'],
        );
        // ↑ Конструктор VO ВЫЗЫВАЕТСЯ — инварианты проверяются
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode([
            'amount'   => $value->amountCents,
            'currency' => $value->currency,
        ]);
    }
}
```

```php
#[ORM\Entity]
class Invoice
{
    #[ORM\Column(type: MoneyJsonType::NAME)]
    public readonly Money $amount;
}
```

**Результат в БД:**

```sql
CREATE TABLE invoices (
    id UUID PRIMARY KEY,
    amount JSONB NOT NULL  -- {"amount": 100, "currency": "EUR"}
);
```

| Плюсы | Минусы |
|-------|--------|
| **Конструктор VO всегда вызывается** | Нет индексации по компонентам (без GIN) |
| Гибкость: VO может иметь сложную структуру | SQL-запросы на компоненты — `amount->>'amount'` |
| Лёгко менять структуру VO (добавить поле) | Размер строки больше |
| Валидация при чтении из БД | Migrations — сложнее |

**Для PostgreSQL** можно использовать GIN-индекс:

```sql
CREATE INDEX idx_invoice_amount ON invoices USING GIN (amount jsonb_path_ops);
-- Теперь можно: WHERE amount @> '{"amount": 100}'
```

---

### Способ 4: PHP 8.1 Enum + Doctrine EnumType

Для простых VO (статус, тип, категория):

```php
// Domain/OrderStatus.php
namespace App\Domain;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Delivered, self::Cancelled => true,
            _ => false,
        };
    }
}
```

```php
// Entity
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: 'enum', enumType: OrderStatus::class)]
    public OrderStatus $status;
}
```

**Доctrine 2.14+** поддерживает нативный маппинг PHP 8.1 enums.

| Плюсы | Минусы |
|-------|--------|
| Нативная поддержка | Только для простых enum-VO |
| Валидация: `OrderStatus::from('invalid')` → `ValueError` | Нет composite-логики |
| Идемпотентность | Нет сложной структуры |

---

### Способ 5: Ручной маппинг (repository + DTO)

Без кастомного Type. VO хранится как **состав из колонок**, маппинг — в репозитории.

```php
// Entity (без VO!)
#[ORM\Entity]
class InvoiceEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    public int $amountCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    public string $currency;

    // ← Entity — «плоский» DTO для persistence
}
```

```php
// Repository: маппинг Entity ↔ Domain
namespace App\Infrastructure\Persistence;

use App\Domain\Money;
use App\Domain\Invoice;

final class DoctrineInvoiceRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(Invoice $invoice): void
    {
        $entity = new InvoiceEntity();
        $entity->id = $invoice->id();
        $entity->amountCents = $invoice->amount()->amountCents;
        $entity->currency = $invoice->amount()->currency;
        // ...

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function findById(string $id): Invoice
    {
        /** @var InvoiceEntity $entity */
        $entity = $this->em->getRepository(InvoiceEntity::class)->find($id);

        if ($entity === null) {
            throw new InvoiceNotFound($id);
        }

        return Invoice::reconstitute(
            id: $entity->id,
            amount: new Money($entity->amountCents, $entity->currency),
            // ...
        );
    }
}
```

| Плюсы | Минусы |
|-------|--------|
| **Полный контроль** над маппингом | Больше кода (маппинг в репо) |
| Инварианты VO **гарантированы** | Entity ≠ Domain (два класса) |
| Нет magic — видно в IDE | DRY: дублирование структуры |
| Миграции — обычные | Невозможно lazy-load VO |

---

### Подводные камни (общие)

#### 1. Hydration bypass'ит конструктор

```php
// Doctrine делает так (упрощённо):
$money = new \ReflectionClass(Money::class)->newInstanceWithoutConstructor();
$money->amountCents = 100;  // ← прямой доступ
$money->currency = 'EUR';
// Инварианты НЕ проверяются!
```

**Решение:** Способ 3 (JSONB) или Способ 5 (ручной маппинг) — конструктор всегда вызывается.

#### 2. Dirty checking по ссылке

```php
// Doctrine сравнивает:
if ($oldMoney !== $newMoney) {  // ← reference comparison
    // dirty!
}

// Если вы заменили VO на новый экземпляр С ТЕМ ЖЕ значением:
$invoice->amount = new Money(100, 'EUR');  // новый объект
// Doctrine: "о, ссылка изменилась → dirty → UPDATE"
// Но значение то же! Ложный dirty.

// Если вы НЕ заменили (readonly), dirty checking не сработает.
```

**Решение:** Способ 3 (JSONB) — Doctrine сравнивает строки, а не ссылки.

#### 3. `readonly` properties

```php
// Doctrine hydrate'ит через reflection:
$money->amountCents = 100;  // ← Fatal Error: readonly property
```

**Решение:** Не использовать `readonly` в VO, которые хранит Doctrine. Или — Способ 5 (ручной маппинг).

#### 4. Миграции при изменении VO

```php
// VO Money: { amount, currency }
// Новый VO Money: { amount, currency, taxRate }

// Способ 3 (JSONB): просто добавьте поле, старые строки не сломаются
// Способ 1 (composite): нужно добавить колонку, backfill
// Способ 5 (ручной): нужно изменить entity + репо + миграцию
```

#### 5. Индексация

```sql
-- Способ 1 (composite): можно
CREATE INDEX idx_invoice_amount ON invoices (amount_amount, amount_currency);

-- Способ 3 (JSONB): можно (GIN)
CREATE INDEX idx_invoice_amount ON invoices USING GIN (amount);

-- Способ 5 (ручной): можно
CREATE INDEX idx_invoice_amount ON invoice_entity (amount_cents, currency);
```

#### 6. Наследование

```php
// Если Invoice наследует от BaseInvoice:
// Composite types и JSONB — работают
// Embedded — может сломаться (Doctrine embedded + inheritance = боль)
```

---

### Сравнение для реального выбора

| Критерий | Composite | Embedded | JSONB | Enum | Ручной |
|----------|-----------|----------|-------|------|--------|
| Инварианты VO | ⚠️ | ❌ | ✅ | ✅ | ✅ |
| Индексация | ✅ | ✅ | ⚠️ (GIN) | ✅ | ✅ |
| Миграции | ✅ | ✅ | ⚠️ | ✅ | ✅ |
| Производительность | ✅ | ✅ | ⚠️ | ✅ | ✅ |
| Контроль маппинга | ⚠️ | ❌ | ⚠️ | ✅ | ✅ |
| Сложность кода | Средняя | Низкая | Низкая | Минимальная | Высокая |
| readonly-совместимость | ❌ | ❌ | ✅ | ✅ | ✅ |

---

### Рекомендация

```
Простой VO (статус, тип, код)
    → PHP 8.1 enum + Doctrine enum type

Составной VO (Money, Address, Period)
    → JSONB (Способ 3) — если инварианты важны
    → Composite (Способ 1) — если нужна индексация по компонентам

Сложный VO (Invoice, Contract)
    → Ручной маппинг (Способ 5) — полный контроль, два класса

Встроенный (Embedded) — только если:
    - VO простой, без валидации в конструкторе
    - Не используете readonly
    - Не нужен полный контроль
```

**Главный принцип:** если VO имеет инварианты в конструкторе — **не давайте Doctrine hydrate'ить его напрямую**. Либо JSONB (конструктор вызывается), либо ручной маппинг (вы управляете процессом).

> ⏱ 31.4s · prompt 29884 tok · output 4123 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата в Doctrine. Это конкретный, глубокий вопрос на стыке DDD и persistence. Нужно разобрать:

1. Структуру агрегата (корень + дочерние)
2. Маппинг коллекций в Doctrine (One-ToMany, cascade, orphanRemoval)
3. Границы транзакций — кто управляет
4. Кто вызывает flush — агрегат, repository, application service, controller?
5. Подводные камни: lazy loading, identity map, detached entities

Выберу конкретный пример: Order (агрегат) → OrderLine (дочерние). Это классика, понятная всем, и позволяет показать все нюансы.

Структура ответа:
- Модель агрегата (домен)
- Маппинг в Doctrine (entity + collection)
- Кто владеет транзакцией (Unit of Work)
- Кто вызывает flush
- Границы: application service vs controller
- Подводные камни (identity map, lazy loading, detached)
- Антипаттерны

Важно показать, что flush — это НЕ «сохрани агрегат», а «зафиксируй всё, что изменилось в UoW». Это частая путаница.

Также важно: агрегат — это граница транзакции. Нельзя менять два агрегата в одной транзакции, если они из разных bounded contexts (хотя технически можно).

Разберу на примере Order → OrderLine, покажу код, маппинг, и объясню почему flush вызывается на уровне application service, а не в контроллере и не в агрегате.

</details>

**Кандидат:**

## Агрегат в Doctrine: Order → OrderLine

### 1. Доменная модель

```php
// Domain/Order/Order.php
namespace App\Domain\Order;

final class Order
{
    /** @var OrderLine[] */
    private array $lines = [];

    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
    ) {}

    public function addLine(Sku $sku, int $quantity, Money $unitPrice): void
    {
        $this->assertOpen();

        if ($this->findLine($sku) !== null) {
            throw new DuplicateLineException($sku->value());
        }

        $this->lines[] = new OrderLine(
            sku: $sku,
            quantity: $quantity,
            unitPrice: $unitPrice,
        );
    }

    public function removeLine(Sku $sku): void
    {
        $this->assertOpen();
        $this->lines = array_filter(
            $this->lines,
            fn(OrderLine $l) => $l->sku()->value() !== $sku->value(),
        );
    }

    public function total(): Money
    {
        $total = new Money(0, 'EUR');
        foreach ($this->lines as $line) {
            $total = $total->add($line->lineTotal());
        }
        return $total;
    }

    /** @return OrderLine[] */
    public function lines(): array
    {
        return $this->lines;
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    private function findLine(Sku $sku): ?OrderLine
    {
        foreach ($this->lines as $line) {
            if ($line->sku()->equals($sku)) {
                return $line;
            }
        }
        return null;
    }

    private function assertOpen(): void
    {
        if ($this->isFinal()) {
            throw new OrderClosedException($this->id->value());
        }
    }
}
```

```php
// Domain/Order/OrderLine.php
namespace App\Domain\Order;

final class OrderLine
{
    public function __construct(
        private readonly Sku $sku,
        private int $quantity,
        private readonly Money $unitPrice,
    ) {
        if ($quantity <= 0) {
            throw new InvalidQuantityException($quantity);
        }
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function lineTotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
```

**Ключевые свойства агрегата:**
- `Order` — корень. Единственная точка входа для изменений.
- `OrderLine` — дочерняя. Не существует без `Order`. Её не загружают отдельно.
- `OrderLine` — **идентифицируемая** (есть `id`), но **не самостоятельная** (не агрегат).

---

### 2. Маппинг в Doctrine

#### 2.1 Entity

```php
// Infrastructure/Persistence/OrderEntity.php
namespace App\Infrastructure\Persistence;

use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderId;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    private string $customerId;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\OneToMany(
        targetEntity: OrderLineEntity::class,
        mappedBy: 'order',
        cascade: ['persist'],
        orphanRemoval: true,       // ← удаление дочерних через removal из коллекции
        fetch: FetchMode::EAGER,   // ← см. ниже почему EAGER
    )]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    // Getters / setters для Doctrine
    public function getId(): string { return $this->id; }
    public function setId(string $id): void { $this->id = $id; }
    public function getCustomerId(): string { return $this->customerId; }
    public function setCustomerId(string $id): void { $this->customerId = $id; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): void { $this->status = $s; }

    /** @return Collection<int, OrderLineEntity> */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(OrderLineEntity $line): void
    {
        $this->lines->add($line);
        $line->setOrder($this);
    }

    public function removeLine(OrderLineEntity $line): void
    {
        $this->lines->removeElement($line);
        $line->setOrder(null);  // ← orphanRemoval требует обнуления owning-стороны
    }
}
```

```php
// Infrastructure/Persistence/OrderLineEntity.php
namespace App\Infrastructure\Persistence;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLineEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    private ?OrderEntity $order = null;

    #[ORM\Column(type: Types::STRING)]
    private string $sku;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    #[ORM\Column(type: Types::INTEGER)]
    private int $unitPriceCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    // Getters / setters
    public function getId(): ?int { return $this->id; }
    public function getOrder(): ?OrderEntity { return $this->order; }
    public function setOrder(?OrderEntity $order): void { $this->order = $order; }
    public function getSku(): string { return $this->sku; }
    public function setSku(string $sku): void { $this->sku = $sku; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $q): void { $this->quantity = $q; }
    public function getUnitPriceCents(): int { return $this->unitPriceCents; }
    public function setUnitPriceCents(int $c): void { $this->unitPriceCents = $c; }
    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $c): void { $this->currency = $c; }
}
```

#### 2.2 SQL

```sql
CREATE TABLE orders (
    id VARCHAR(36) PRIMARY KEY,
    customer_id VARCHAR(36) NOT NULL,
    status VARCHAR(20) NOT NULL
);

CREATE TABLE order_lines (
    id SERIAL PRIMARY KEY,
    order_id VARCHAR(36) NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price_cents INT NOT NULL,
    currency VARCHAR(3) NOT NULL
);

CREATE INDEX idx_lines_order ON order_lines(order_id);
```

#### 2.3 Почему EAGER для дочерних

```php
fetch: FetchMode::EAGER
```

**Правило DDD:** дочерние сущности агрегата **должны быть загружены вместе с корнем**. Если `OrderLine` загружается лениво, то:

```php
$order = $repo->findById($id);
$order->lines();  // ← может быть пустым, если lazy
$order->total();  // ← 0, а не реальная сумма
```

Это **нарушает инвариант агрегата**: корень должен знать своё полное состояние.

**Компромисс:** EAGER — только для дочерних **в пределах агрегата**. Для связей между агрегатами — по ID (не по entity).

---

### 3. Repository: маппинг домен ↔ entity

```php
// Infrastructure/Persistence/DoctrineOrderRepository.php
namespace App\Infrastructure\Persistence;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\CustomerId;
use App\Domain\Common\Sku;
use App\Domain\Common\Money;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

final class DoctrineOrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(OrderId $id): Order
    {
        /** @var OrderEntity $entity */
        try {
            $entity = $this->em
                ->getRepository(OrderEntity::class)
                ->getReference(OrderEntity::class, $id->value());

            $this->em->refresh($entity);  // ← гарантирует свежее состояние

        } catch (NoResultException) {
            throw new OrderNotFound($id->value());
        }

        return $this->toDomain($entity);
    }

    public function save(Order $order): void
    {
        $entity = $this->findOrCreate($order->id());
        $this->syncLines($entity, $order);
        $this->em->flush();
    }

    private function toDomain(OrderEntity $entity): Order
    {
        $order = new Order(
            id: new OrderId($entity->getId()),
            customerId: new CustomerId($entity->getCustomerId()),
        );

        // ← Восстановление дочерних
        // Для этого нужен метод на Order:
        // $order = Order::reconstitute(...)
        // или через factory:
        $order = Order::fromEntity(
            id: new OrderId($entity->getId()),
            customerId: new CustomerId($entity->getCustomerId()),
            lines: array_map(fn(OrderLineEntity $l) => [
                'sku' => new Sku($l->getSku()),
                'quantity' => $l->getQuantity(),
                'unitPrice' => new Money($l->getUnitPriceCents(), $l->getCurrency()),
            ], $entity->getLines()->toArray()),
        );

        return $order;
    }

    private function syncLines(OrderEntity $entity, Order $order): void
    {
        $existing = $entity->getLines()->toArray();
        $domainLines = $order->lines();

        // Удаляем orphan-строки
        foreach ($existing as $lineEntity) {
            $shouldKeep = false;
            foreach ($domainLines as $domainLine) {
                if ($lineEntity->getSku() === $domainLine->sku()->value()) {
                    $shouldKeep = true;
                    break;
                }
            }

            if (!$shouldKeep) {
                $entity->removeLine($lineEntity);
                $this->em->remove($lineEntity);
            }
        }

        // Добавляем / обновляем
        foreach ($domainLines as $domainLine) {
            $found = null;
            foreach ($entity->getLines() as $lineEntity) {
                if ($lineEntity->getSku() === $domainLine->sku()->value()) {
                    $found = $lineEntity;
                    break;
                }
            }

            if ($found === null) {
                $new = new OrderLineEntity();
                $new->setSku($domainLine->sku()->value());
                $new->setQuantity($domainLine->quantity());
                $new->setUnitPriceCents($domainLine->lineTotal()->amountCents());
                $new->setCurrency('EUR');
                $entity->addLine($new);
            } else {
                $found->setQuantity($domainLine->quantity());
            }
        }
    }

    private function findOrCreate(OrderId $id): OrderEntity
    {
        $entity = $this->em->find(OrderEntity::class, $id->value());

        if ($entity === null) {
            $entity = new OrderEntity();
            $entity->setId($id->value());
            $this->em->persist($entity);
        }

        return $entity;
    }
}
```

---

### 4. Граница транзакции: кто и когда вызывает `flush`

#### 4.1 Кто вызывает `flush`

```
Controller
    │
    │  (не вызывает flush)
    ▼
Application Service (use-case)
    │
    │  ← FLUSH ЗДЕСЬ
    ▼
Repository
    │
    │  (persist, sync — но НЕ flush)
    ▼
Doctrine EntityManager
```

**Правило:** `flush` вызывается на **уровне application service**, а не в контроллере и не в репозитории.

#### 4.2 Почему не в контроллере

```php
// ПЛОХО
class OrderController
{
    public function create(CreateOrderRequest $req): Response
    {
        $order = $this->orderService->create($req);
        $this->em->flush();  // ← контроллер знает про Doctrine!
        return new Response(null, 201);
    }
}
```

Проблема: контроллер зависит от `EntityManager`. Если use-case делает 2 flush'а (например, после создания и после отправки события), контроллер не должен это знать.

#### 4.3 Почему не в репозитории

```php
// ПЛОХО
class DoctrineOrderRepository
{
    public function save(Order $order): void
    {
        // ...
        $this->em->flush();  // ← репозиторий фиксирует ВСЁ, что в UoW
    }
}
```

Проблема: `flush` фиксирует **весь** Unit of Work. Если use-case до `save()` ещё сделал `persist` другого агрегата, `flush` внутри репозитория зафиксирует **оба** — раньше, чем use-case закончил. Это нарушает атомарность use-case.

#### 4.4 Правильно: application service

```php
// Application/CreateOrder.php
namespace App\Application;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\CustomerId;
use App\Domain\Common\Sku;
use App\Domain\Common\Money;
use App\Infrastructure\Persistence\DoctrineOrderRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateOrder
{
    public function __construct(
        private readonly DoctrineOrderRepository $orders,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(CreateOrderRequest $request): Order
    {
        $order = new Order(
            id: new OrderId(uniqid('ord_', true)),
            customerId: new CustomerId($request->customerId),
        );

        foreach ($request->items as $item) {
            $order->addLine(
                sku: new Sku($item->sku),
                quantity: $item->quantity,
                unitPrice: new Money($item->priceCents, 'EUR'),
            );
        }

        // ← Все изменения в UoW, но ещё не зафиксированы

        $this->orders->save($order);
        // ↑ Внутри: persist + syncLines. flush НЕ вызывается.

        // Атомарность: всё или ничего
        // Если save() бросит исключение — ничего не закоммитится.

        // Событие (после успешного save)
        $this->bus->dispatch(new OrderCreatedEvent($order->id()->value()));

        return $order;
    }
}
```

```php
// Controller — тонкий
class OrderController
{
    public function __construct(
        private readonly CreateOrder $createOrder,
    ) {}

    public function create(CreateOrderRequest $request): Response
    {
        $order = ($this->createOrder)($request);
        return new Response(null, 201, ['Location' => "/orders/{$order->id()->value()}"]);
    }
}
```

**Где `flush`?** В `DoctrineOrderRepository::save()`:

```php
public function save(Order $order): void
{
    $entity = $this->findOrCreate($order->id());
    $this->syncLines($entity, $order);
    $this->em->flush();  // ← ЗДЕСЬ
}
```

Подождите — я только что сказал, что flush не в репозитории. Уточню:

**Есть два стиля:**

| Стиль | flush в | Когда |
|-------|---------|-------|
| A | Application service | Use-case делает несколько `save` и один `flush` в конце |
| B | Repository | Каждый `save` = один `flush`. Use-case = одна транзакция |

Для **одного агрегата** (наш пример) — Стиль B проще и достаточно:

```php
// Application
$order->addLine(...);
$order->removeLine(...);
$this->orders->save($order);  // ← flush внутри

// Всё или ничего: один flush = одна транзакция
```

Для **нескольких агрегатов** в одном use-case — Стиль A:

```php
// Application
$order = $this->orders->findById($id);
$inventory = $this->inventory->findById($id);

$order->addLine(...);
$inventory->reserve(...);

$this->orders->saveWithoutFlush($order);   // persist только
$this->inventory->saveWithoutFlush($inventory);  // persist только
$this->em->flush();  // ← ОДИН flush, АТОМАН
```

---

### 5. Подводные камни

#### 5.1 Identity Map: «мёртвые» ссылки

```php
$order1 = $repo->findById($id);  // загружен в UoW
$order2 = $repo->findById($id);  // ТОТ ЖЕ объект (identity map)

$order1->addLine(...);
$order2->lines();  // ← видит изменение!

// Это не баг — это identity map.
// Но может удивить: два «разных» вызова дают один объект.
```

#### 5.2 Detached entities

```php
// Запрос 1:
$order = $repo->findById($id);  // managed
$this->em->clear();            // → detached!

$order->addLine(...);          // ← работает в памяти
$repo->save($order);           // ← persist() на detached = новый INSERT!
// Результат: два ряда в БД с одинаковым id
```

**Решение:** не `clear()` между операциями с одним агрегатом. Или `merge()`:

```php
$managed = $this->em->merge($order);  // ← привязывает к UoW
```

#### 5.3 Lazy loading вне request

```php
// В worker-процессе (messenger:consume):
$order = $repo->findById($id);
$order->lines();  // ← LAZY! Сделает SELECT

// Это нормально, но:
// 1. Удлинняется время обработки
// 2. Если BД недоступна — падение
// 3. Невидимый SELECT в профайлере

// Решение: EAGER для дочерних (см. выше)
// Или: eager-load в репозитории:
$this->em->createQuery(
    'SELECT o FROM OrderEntity o JOIN FETCH o.lines WHERE o.id = :id'
)->setParameter('id', $id)->getOneOrNullResult();
```

#### 5.4 Cascade + orphanRemoval = «тихое» удаление

```php
// orphanRemoval: true означает:
// Если OrderLine удалён из коллекции $order->lines —
// Doctrine автоматически сделает DELETE FROM order_lines

// Опасность:
$order = $repo->findById($id);
$order->lines = [];  // ← ВСЕ строки удалены!
$repo->save($order);
// DELETE FROM order_lines WHERE order_id = ...  ← без подтверждения

// Защита: инварианты в домене
// Order::removeLine() — один по одному, с проверками
```

#### 5.5 `persist` vs `merge`

```php
// persist — для НОВЫХ объектов
$this->em->persist($newOrder);

// merge — для DETACHED (отключённых)
$managed = $this->em->merge($detachedOrder);

// NEVER: persist на уже managed-объекте
$this->em->persist($existingOrder);  // ← ошибка или дубликат
```

---

### 6. Итоговая схема

```
┌─────────────────────────────────────────────────────────────────┐
│  AGGREGATE BOUNDARY (Order)                                     │
│                                                                 │
│  ┌──────────┐    ┌─────────────┐                                │
│  │  Order   │───▶│ OrderLine[] │  ← EAGER, cascade, orphan      │
│  │  (root)  │    │ (children)  │                                │
│  └──────────┘    └─────────────┘                                │
│       │                                                        │
│       │  Единственная точка изменения                           │
│       │  Инварианты проверяются в методах корня                 │
│       ▼                                                        │
│  ┌──────────────────────────────────────────────────────┐      │
│  │  Unit of Work (EntityManager)                         │      │
│  │  - identity map                                       │      │
│  │  - dirty checking                                     │      │
│  │  - flush = COMMIT                                     │      │
│  └──────────────────────────────────────────────────────┘      │
│       │                                                        │
│       │  flush вызывается ОДИН раз                             │
│       │  на границе use-case                                   │
│       ▼                                                        │
│  ┌──────────────────────────────────────────────────────┐      │
│  │  Transaction (BEGIN ... COMMIT / ROLLBACK)            │      │
│  └──────────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────────┘
```

**Правила:**

1. **Агрегат = граница транзакции.** Один use-case меняет один агрегат.
2. **Корень — единственный вход.** Дочерние не изменяются «напрямую».
3. **EAGER для дочерних.** Корень всегда знает своё полное состояние.
4. **Flush на границе use-case.** Не в контроллере, не в домене.
5. **Один flush = одна транзакция.** Атомарность на уровне use-case.
6. **Не `clear()` внутри use-case.** Detached = потеря состояния.

> ⏱ 38.8s · prompt 33664 tok · output 5719 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о диагностике и лечении N+1 запросов в Doctrine. Это практический вопрос — нужно дать конкретные инструменты диагностики и решения с trade-offs.

Структура ответа:
1. Диагностика — как обнаружить (логгер, profiler, slow query log, Doctrine logger)
2. Решения — EAGER, JOIN FETCH, DQL, QueryBuilder, batch loading, denormalization
3. Trade-offs каждого

Нужно быть конкретным: реальный код, реальные инструменты (Xdebug profiler, Doctrine logger, Symfony Profiler, pg_stat_statements), а не абстрактные рассуждения.

Начну с диагностики — это логично: сначала найдём, потом лечим.

</details>

**Кандидат:**

## N+1 в Doctrine: диагностика и лечение

### 1. Что такое N+1

```
SELECT * FROM orders WHERE status = 'open';           ← 1
SELECT * FROM order_lines WHERE order_id = 'a';       ← +1
SELECT * FROM order_lines WHERE order_id = 'b';       ← +1
SELECT * FROM order_lines WHERE order_id = 'c';       ← +1
...
SELECT * FROM customers WHERE id = 'x';               ← +1
SELECT * FROM customers WHERE id = 'y';               ← +1
```

100 заказов → 201+ запросов. Каждый — round-trip к БД, парсинг, аллокация.

---

### 2. Диагностика

#### 2.1 Symfony Profiler

```
http://localhost/profiler/{id}
→ DB Query Tab
→ Сортировка по COUNT и DURATION
```

Видно: 200 запросов `SELECT * FROM order_lines WHERE order_id = ?` с почти одинаковой длительностью. Паттерн очевиден.

**Ограничение:** показывает только текущий request. Для batch-обработки — нет.

#### 2.2 Doctrine Logger (production-grade)

```php
// config/services.php
use Doctrine\DBAL\Logging\Middleware;
use Monolog\Logger;

->set(Middleware::class)
    ->args([
        new Logger('doctrine'),
        fn(\Throwable $e) => false,
    ])
```

```php
// Или свой логгер для production:
// src/Infrastructure/DoctrineLogger.php
namespace App\Infrastructure;

use Doctrine\DBAL\Logging\SQLLogger;

class BatchQueryLogger implements SQLLogger
{
    private array $queries = [];
    private int $count = 0;
    private float $totalTime = 0.0;

    public function startQuery(string $sql, ?array $params = null): void
    {
        $this->count++;
    }

    public function stopQuery(): void
    {
        $this->totalTime += 0.001;
    }

    public function getSummary(): array
    {
        return [
            'count' => $this->count,
            'total_ms' => $this->totalTime * 1000,
        ];
    }
}
```

#### 2.3 pg_stat_statements (PostgreSQL)

```sql
SELECT
    calls,
    round(mean_exec_time::numeric, 2) AS mean_ms,
    round(total_exec_time::numeric, 2) AS total_ms,
    left(query, 120) AS query
FROM pg_stat_statements
WHERE query ILIKE '%order_lines%'
ORDER BY calls DESC
LIMIT 20;
```

**Полезно для:**
- Аудита после деплоя
- Обнаружения N+1, который не видно в profiler'е (batch-процессы, cron)

#### 2.4 Xdebug / Blackfire (call stack)

```
Xdebug Trace:
  OrderController::index()
    → OrderRepository::findAllOpen()         ← 1 SELECT
    → Order::lines()                         ← N × SELECT
      → EntityManager::refresh()
        → SQL: SELECT * FROM order_lines ...
```

Показывает **откуда** идёт вызов — критично, когда N+1 «спрятан» в гидратации.

#### 2.5 Алгоритм диагностики

```
1. Воспроизвести на local (Xdebug / Profiler)
2. Найти паттерн: одинаковый SQL с разными params
3. Определить: это lazy-load? Гидратация? Явный вызов?
4. Измерить: сколько запросов, средняя длительность
5. Оценить: N+1 на 10 строк? На 1000? (влияние масштаба)
6. Выбрать решение по trade-off ниже
```

---

### 3. Решения

#### 3.1 JOIN FETCH (самое частое)

Заменяет N+1 одним запросом с JOIN.

```php
// Было (N+1):
$orders = $em->getRepository(Order::class)->findBy(['status' => 'open']);
foreach ($orders as $order) {
    $lines = $order->getLines();  // ← lazy → N SELECT
}

// Стало (1 запрос):
$orders = $em->createQuery(
    'SELECT o, l FROM Order o JOIN o.lines l WHERE o.status = :status'
)->setParameter('status', 'open')
 ->getResult();

// Теперь $order->getLines() — уже загружено, нет доп. запросов
```

**SQL:**

```sql
SELECT o.id, l.id, l.sku, l.quantity
FROM orders o
JOIN order_lines l ON l.order_id = o.id
WHERE o.status = 'open';
```

| Плюс | Минус |
|------|-------|
| 1 запрос вместо N+1 | Картезианово произведение: 100 заказов × 5 строк = 500 строк в ответе |
| Простота | Память: все строки в RAM одновременно |
| Не требует изменения модели | `getResult()` — весь объём в память сразу |
| | Не работает для глубоких вложенностей (JOIN JOIN JOIN → экспонента строк) |

**Когда идеален:** 1 уровень вложенности, <1000 строк в результате.

---

#### 3.2 EAGER fetching (конфигурация)

```php
#[ORM\OneToMany(
    targetEntity: OrderLine::class,
    mappedBy: 'order',
    fetch: FetchMode::EAGER,  // ← всегда JOIN FETCH
)]
private Collection $lines;
```

| Плюс | Минус |
|------|-------|
| Нет N+1 никогда | **Всегда** JOIN, даже когда линии не нужны |
| Не нужно помнить в каждом запросе | Удлинняет любой SELECT по Order |
| | Труднее отключить (нужно переопределять в каждом DQL) |

**Когда идеален:** дочерние **всегда** нужны (наши OrderLine — да).

**Когда НЕ идеален:** связь, которая иногда нужна, иногда нет (Order → AuditLog — нет).

---

#### 3.3 Partial SELECT (DQL)

Загружаем только нужные поля, избегаем hydrate.

```php
// N+1 через entity:
$orders = $em->getRepository(Order::class)->findAll();
foreach ($orders as $o) { $o->getLines(); }  // N SELECT

// Без entity — только данные:
$rows = $em->createQuery(
    'SELECT o.id, l.sku, l.quantity
     FROM Order o JOIN o.lines l'
)->getResult();

// → array of arrays, нет lazy-loading, нет N+1
// → но и нет domain-методов
```

| Плюс | Минус |
|------|-------|
| Минимум памяти | Нет доменных методов |
| Быстрее (меньше hydration) | Дублирование SQL-логики |
| Нет N+1 | Сложнее поддерживать |

**Когда идеален:** списки/таблицы, где нужны только поля. Детальные страницы — entity.

---

#### 3.4 Batch / Chunked loading

Загрузка порциями, чтобы не держать всё в RAM.

```php
// Doctrine 2.12+
$query = $em->createQuery(
    'SELECT o, l FROM Order o JOIN o.lines l WHERE o.status = :s'
)->setParameter('s', 'open');

$query->setMaxResults(100);
$query->setFirstResult(0);

// Chunked:
$offset = 0;
$chunkSize = 100;

while (true) {
    $orders = $query->setFirstResult($offset)->setMaxResults($chunkSize)->getResult();
    if (count($orders) < $chunkSize) break;

    foreach ($orders as $order) {
        process($order);
    }

    $em->clear(Order::class);  // ← освобождаем identity map
    $offset += $chunkSize;
}
```

| Плюс | Минус |
|------|-------|
| Ограниченная память | `$em->clear()` — lose identity map (detached!) |
| Работает для больших объёмов | Порядок не гарантирован без ORDER BY |
| | Медленнее (N запросов по chunk-у) |

**Когда идеален:** обработка >10k строк в worker/cron.

---

#### 3.5 Denormalization / Pre-computation

Убираем JOIN на уровне схемы.

```sql
-- Было:
CREATE TABLE orders (id, status);
CREATE TABLE order_lines (id, order_id, sku, qty, price);

-- Стало:
CREATE TABLE orders (
    id, status,
    total_cents INT,          -- ← precomputed
    line_count INT,           -- ← precomputed
    first_sku VARCHAR(100)    -- ← для списков
);
```

```php
// Теперь список заказов — 1 SELECT без JOIN:
$orders = $em->getRepository(Order::class)->findBy(['status' => 'open']);
// $order->totalCents — уже в строке
```

| Плюс | Минус |
|------|-------|
| 1 запрос, без JOIN | Денормализация → синхронизация |
| Быстрее всего | Где храним: триггер? Service? Outbox? |
| Меньше памяти | Сложнее миграции |

**Когда идеален:** списки/таблицы, где 90% времени показывают сводку.

---

#### 3.6 Data Transfer Object + Repository

Обходим entity совсем на «горячих» путях.

```php
// Repository возвращает DTO, не entity
interface OrderListRepository
{
    /** @return OrderListItem[] */
    public function findOpen(int $limit, int $offset): array;
}

// Реализация:
class DoctrineOrderListRepository implements OrderListRepository
{
    public function findOpen(int $limit, int $offset): array
    {
        return $this->em->createQuery(
            'SELECT new App\DTO\OrderListItem(
                o.id, o.status, o.totalCents,
                l.sku AS firstSku
            )
            FROM Order o LEFT JOIN o.lines l
            WHERE o.status = "open"
            GROUP BY o.id
            ORDER BY o.createdAt DESC'
        )
        ->setMaxResults($limit)
        ->setFirstResult($offset)
        ->getResult();
    }
}
```

| Плюс | Минус |
|------|-------|
| Полный контроль над SQL | DTO ≠ Entity (два класса) |
| Нет hydration-оверхеда | Дублирование полей |
| Быстрее всего | Не доменные методы |

**Когда идеален:** публичные API-списки, dashboard-виджеты.

---

#### 3.7 Caching (second-level / application)

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        second_level_cache:
            enabled: true
            metadata_cache_driver:
                type: pool
                pool: doctrine.system_cache_pool
            query_cache_driver:
                type: pool
                pool: doctrine.result_cache_pool
            result_cache_driver:
                type: pool
                pool: doctrine.result_cache_pool
```

| Плюс | Минус |
|------|-------|
| Повторные запросы — из cache | Инвалидация: когда данные меняются? |
| Не требует изменения кода | Память: cache может быть огромным |
| | Race condition: stale data |

**Когда идеален:** референсные данные (продукты, категории). Для mutable данных (заказы) — **нет**.

---

### 4. Сравнение для выбора

| Ситуация | Решение | Почему |
|----------|---------|--------|
| 1 уровень, <1k строк | JOIN FETCH | Простой, быстрый |
| 1 уровень, всегда нужен | EAGER | Не нужно помнить |
| Список/таблица | Partial SELECT / DTO | Минимум данных |
| >10k строк, worker | Chunked + clear() | Память |
| Сводные данные | Denormalization | Скорость |
| Референсные данные | Cache | Повторные чтения |
| 2+ уровня вложенности | NESTED SET / CTE / разбивка | JOIN JOIN JOIN — экспонента |

---

### 5. Чего НЕ делать

```php
// 1. $em->clear() + повторный find — «обход» N+1
$orders = $repo->findAll();
$em->clear();
foreach ($orders as $o) {  // ← detached!
    $fresh = $repo->find($o->id);  // ← N+1 возвращается, только скрытый
}

// 2. EAGER на ВСЁ
#[ORM\ManyToMany(fetch: FetchMode::EAGER)]  // на AuditLog, на Tags, на Comments
// ← Каждый SELECT по Order тащит за собой 500 лишних строк

// 3. subselect как панацея
// Doctrine subselect (DQL):
// 'SELECT o FROM Order o WHERE o IN (SELECT l.order FROM OrderLine l)'
// ← Работает, но: 2 запроса, пагинация сломана, не масштабируется

// 4. «Оптимизация» без замеров
// Увидел N+1 → сразу EAGER → не замерил → память выросла в 3 раза
// Замеряй ДО и ПОСЛЕ: pg_stat_statements + memory_get_peak_usage()
```

---

### 6. Чеклист: что делать при обнаружении N+1

```
1. Подтверди: pg_stat_statements / profiler — 1 паттерн, N повторений
2. Оцени: N = ? (10? 100? 10 000?)
3. Оцени: объём строк в результате? (память)
4. Выбери:
   - N < 100, 1 уровень → JOIN FETCH
   - N < 1000, всегда нужен → EAGER
   - N > 1000 → Chunked / DTO / Denormalization
   - Референсы → Cache
5. Замерь: time + memory ДО и ПОСЛЕ
6. Проверь: не сломали ли пагинацию, order, где-else
```

> ⏱ 29.7s · prompt 39069 tok · output 3488 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking about database-level integrity constraints in PostgreSQL vs. application-level business validation. This is a classic "why do we need both?" question. Let me think about what a senior PHP developer would actually say.

Key points to cover:
1. Why application-level validation isn't enough (multiple code paths, race conditions, direct DB access, bugs, scripts, migrations, admin panels)
2. What DB constraints exist in PostgreSQL: NOT NULL, UNIQUE, CHECK, FOREIGN KEY, EXCLUSION, TRIGGER
3. When each is used
4. Trade-offs: performance, error handling, developer experience
5. The principle: DB constraints are the LAST line of defense, not the primary validation
6. Practical examples in the context of an Order aggregate

I should be practical and show real SQL + how it interacts with Doctrine. I should also be honest about the downsides (ugly exceptions, performance of CHECK on high-write tables, etc.).

Let me structure this as:
- Why app validation alone is insufficient (concrete scenarios)
- PostgreSQL constraint toolbox
- When to use which
- How they interact with Doctrine (exception handling)
- Anti-patterns
- Summary principle

</details>

**Кандидат:**

## Зачем ограничения на уровне БД, если есть валидация в коде

### 1. Почему «кода достаточно» — ложная уверенность

```
Код приложения — НЕ единственный путь к данным.
```

Конкретные сценарии, где валидация в коде **не сработает**:

| Сценарий | Почему код не спасает |
|----------|----------------------|
| Два запроса одновременно (race condition) | Код проверяет «баланс >= X», но между SELECT и UPDATE — другой запрос списал средства |
| Админ-панель / cron-скрипт | Другой код, другая логика, другой разработчик |
| Миграция / backfill | SQL-скрипт, который «просто вставит строку» |
| `psql` / pgAdmin | DBA или аналитик правит данные напрямую |
| Баг в коде | Опечатка, гонка, ошибка в логике |
| Replication lag | Читаем из replica, пишем в primary — состояние устарело |
| Разные версии приложения | Старый контейнер ещё работает, новый уже задеплоен |

**Принцип:** приложение — первый рубеж. БД — **последний**. Если что-то прошло сквозь все слои, БД должна отклонить запись, а не принять мусор.

---

### 2. Что даёт PostgreSQL

#### 2.1 NOT NULL

```sql
CREATE TABLE orders (
    id UUID NOT NULL,
    customer_id UUID NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
```

**Что защищает:** отсутствие обязательных полей.

**Когда:** всегда. Если поле «должно быть» — ставьте `NOT NULL`. Не «ну, в коде проверяем» — ставьте.

---

#### 2.2 UNIQUE

```sql
CREATE UNIQUE INDEX idx_orders_customer_sku 
    ON order_lines (order_id, sku);

CREATE UNIQUE INDEX idx_users_email 
    ON users (lower(email));
```

**Что защищает:** дубликаты.

**Когда:** бизнес-уникальность (email, sku, account number).

**Нюанс:** `UNIQUE` — это **индекс**. Каждый INSERT делает проверку по индексу. Для high-write таблиц (100k+ inserts/s) — дорого. Альтернатива: `UNIQUE` + partial index:

```sql
-- Уникальность только для активных
CREATE UNIQUE INDEX idx_users_email_active 
    ON users (lower(email)) 
    WHERE deleted_at IS NULL;
```

---

#### 2.3 CHECK

```sql
CREATE TABLE order_lines (
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price_cents INT NOT NULL CHECK (unit_price_cents >= 0),
    status VARCHAR(20) NOT NULL 
        CHECK (status IN ('pending', 'shipped', 'delivered', 'cancelled'))
);

CREATE TABLE accounts (
    balance_cents BIGINT NOT NULL CHECK (balance_cents >= 0),
    interest_rate NUMERIC(5,4) CHECK (interest_rate >= 0 AND interest_rate <= 1)
);
```

**Что защищает:** диапазон, допустимые значения.

**Когда:**
- Статусы (перечисление в CHECK)
- Диапазоны (количество, цена, процент)
- Логические связки:

```sql
CHECK (
    (status = 'shipped' AND shipped_at IS NOT NULL)
    OR
    (status = 'pending' AND shipped_at IS NULL)
)
```

**Нюанс:** CHECK **не может** обращаться к другим строкам. Для этого — `EXCLUSION` или `TRIGGER`.

---

#### 2.4 FOREIGN KEY

```sql
CREATE TABLE order_lines (
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    ...
);

CREATE TABLE payments (
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE RESTRICT,
    ...
);
```

**Что защищает:** ссылочную целостность.

**`ON DELETE` стратегии:**

| Стратегия | Когда |
|-----------|-------|
| `CASCADE` | Дочерние умирают с родителем (order_lines) |
| `RESTRICT` | Родитель не может быть удалён, пока есть ссылки (payments) |
| `SET NULL` | Ссылка обнуляется (audit_log.actor_id) |

**Нюанс:** FK — это **индекс**. Каждый INSERT/UPDATE проверяет существование. Для high-write — дорого, но **необходимо** для ссылочной целостности.

**Альтернатива для超高-нагрузок:** убрать FK, полагаться на код. **Не рекомендую** — теряете последнюю защиту.

---

#### 2.5 EXCLUSION (PostgreSQL-specific)

Защищает от **пересечения** интервалов.

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;

CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    room_id INT NOT NULL,
    starts_at TIMESTAMPTZ NOT NULL,
    ends_at TIMESTAMPTZ NOT NULL,
    EXCLUDE USING gist (
        room_id WITH =,
        daterange(starts_at, ends_at) WITH &&
    )
);
```

```sql
-- Это отклонит:
INSERT INTO bookings (room_id, starts_at, ends_at) VALUES (1, '2025-01-01', '2025-01-03');
INSERT INTO bookings (room_id, starts_at, ends_at) VALUES (1, '2025-01-02', '2025-01-04');
-- → ERROR: conflicting key value violates exclusion constraint
```

**Когда:** бронирование, временные слоты, диапазоны без перекрытия.

---

#### 2.6 TRIGGER (последний рубеж)

Для логики, которую нельзя выразить в CHECK/EXCLUSION:

```sql
-- Баланс не может уйти в минус
CREATE OR REPLACE FUNCTION check_balance()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.balance_cents < 0 THEN
        RAISE EXCEPTION 'Balance cannot be negative: %', NEW.balance_cents;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_check_balance
    BEFORE INSERT OR UPDATE ON accounts
    FOR EACH ROW
    EXECUTE FUNCTION check_balance();

-- Атомарная операция «списать» с проверкой
CREATE OR REPLACE FUNCTION debit_account(account_id UUID, amount INT)
RETURNS VOID AS $$
BEGIN
    UPDATE accounts
    SET balance_cents = balance_cents - amount
    WHERE id = account_id
      AND balance_cents >= amount;  ← атомарная проверка + списание

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Insufficient balance for account %', account_id;
    END IF;
END;
$$ LANGUAGE plpgsql;
```

**Когда:**
- Атомарные операции (списать с баланса)
- Сложные кросс-табличные инварианты
- Аудит (who, when, what)

**Нюанс:** триггеры — **чёрный ящик** для приложения. Ошибка триггера приходит как `SQLSTATE: 23514` (check_violation) или `P0001` (raise_exception). Нужно маппить в доменные исключения.

---

### 3. Как это взаимодействует с Doctrine

#### 3.1 Исключения из БД

```php
// Когда БД отклоняет:
try {
    $em->flush();
} catch (UniqueConstraintViolationException $e) {
    // PostgreSQL: "duplicate key value violates unique constraint"
    // → переводим в домен
    throw new DuplicateEmailException($email) from $e;
} catch (ForeignKeyConstraintViolationException $e) {
    throw new OrderHasPaymentsException($orderId) from $e;
} catch (ConstraintViolationException $e) {
    // CHECK violation
    throw new InvalidDomainStateException($e->getMessage()) from $e;
}
```

#### 3.2 В репозитории

```php
public function save(Order $order): void
{
    $entity = $this->findOrCreate($order->id());
    $this->syncLines($entity, $order);

    try {
        $this->em->flush();
    } catch (UniqueConstraintViolationException $e) {
        if (str_contains($e->getMessage(), 'idx_orders_customer_sku')) {
            throw new DuplicateLineException($e->getConstraint()->getColumnName());
        }
        throw $e;
    } catch (ConstraintViolationException $e) {
        if (str_contains($e->getMessage(), 'quantity_check')) {
            throw new InvalidQuantityException();
        }
        throw $e;
    }
}
```

#### 3.3 Валидация ДО и ПОСЛЕ

```
До flush (в коде):
  → UX: понятное сообщение, валидация формы, доменное сообщение
  → Быстро: не ходит в БД

После flush (в БД):
  → Гарантия: даже если код пропустил — БД отклонит
  → Race condition: только БД может гарантировать атомарность
  → Мусор: даже если 5 разных кодов пишут — БД единый судья
```

---

### 4. Что **НЕ** ставить в БД

| Что | Почему не в БД |
|-----|---------------|
| «Пароль должен быть 8+ символов» | Валидация UX, не целостность данных |
| «Email должен быть в формате a@b.c» | Формат ≠ целостность. `CHECK (email ~ '@')` — слабое, не нужно |
| «Заказ нельзя удалить, если у него 0 строк» | Логика приложения, не инвариант данных |
| «Два заказа не могут быть созданы в 1мс» | Race condition → UNIQUE + retry, не CHECK |
| Сложная бизнес-логика (скидки, логистика) | БД — не место для бизнес-правил |

**Критерий:** если нарушение = **мусор в данных** (дубликат, ссылка на несуществующее, отрицательное количество) → в БД. Если нарушение = **плохая UX** или **бизнес-правило** → в коде.

---

### 5. Практический набор для проекта

```sql
-- Orders
CREATE TABLE orders (
    id UUID PRIMARY KEY,
    customer_id UUID NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL 
        CHECK (status IN ('pending', 'paid', 'shipped', 'delivered', 'cancelled')),
    total_cents INT NOT NULL CHECK (total_cents >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Order lines
CREATE TABLE order_lines (
    id SERIAL PRIMARY KEY,
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price_cents INT NOT NULL CHECK (unit_price_cents >= 0),
    UNIQUE (order_id, sku)  -- ← нет дубликатов SKU в одном заказе
);

-- Accounts (деньги)
CREATE TABLE accounts (
    id UUID PRIMARY KEY,
    owner_id UUID NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
    balance_cents BIGINT NOT NULL CHECK (balance_cents >= 0),
    currency VARCHAR(3) NOT NULL CHECK (currency IN ('EUR', 'USD', 'RUB'))
);

-- Idempotency (повторные платежи)
CREATE TABLE idempotency_keys (
    key VARCHAR(255) PRIMARY KEY,
    request_id UUID NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    EXPIRES AT TIMESTAMPTZ NOT NULL DEFAULT now() + interval '24 hours'
);
```

---

### 6. Итог

```
┌─────────────────────────────────────────────────────────────────┐
│  SLAB 1: Код (UX, бизнес-правила, понятные сообщения)           │
│  → Валидация формы, доменные инварианты, user-friendly errors   │
├─────────────────────────────────────────────────────────────────┤
│  SLAB 2: БД (целостность данных, гарантия, последний рубеж)     │
│  → NOT NULL, UNIQUE, CHECK, FK, EXCLUSION, TRIGGER              │
└─────────────────────────────────────────────────────────────────┘

SLAB 1 ловит 99% проблем ДО того, как данные дойдут до БД.
SLAB 2 ловит оставшийся 1%: race, bugs, scripts, прямые доступы.

Без SLAB 1: плохой UX, лишние round-trips, дорогие ошибки.
Без SLAB 2: мусор в данных, который никто не заметит до прода.
```

**Правило:** если вы можете сформулировать инвариант как «запись в таблице должна/не должна быть такой» — это ограничение БД. Если инвариант = «действие X допустимо при условии Y» — это код.

> ⏱ 27.0s · prompt 42447 tok · output 3334 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь просит описать процесс диагностики и оптимизации медленного SQL-запроса в PostgreSQL. Это практический вопрос на собеседовании — нужно показать системный подход: от репликации проблемы до применения конкретных оптимизаций.

Структурирую ответ:
1. Диагностика — инструменты (EXPLAIN, pg_stat_statements, pg_stat_activity, pg_stat_user_indexes, pg_buffercache)
2. Типовые причины — их порядок по частоте
3. Оптимизация — конкретные действия для каждой причины
4. Anti-patterns — что не делать

Дам конкретные примеры SQL, EXPLAIN-выводы, индексы. Сделаю ответ практическим, с конкретными командами и интерпретацией.

Язык — русский, как и весь диалог.

</details>

**Кандидат:**

## Диагностика и оптимизация медленного SQL в PostgreSQL

### 1. Первый шаг: воспроизвести и замерить

```sql
-- 1. Сколько реально занимает?
EXPLAIN (ANALYZE, BUFFERS) 
SELECT * FROM orders WHERE customer_id = 'abc' AND status = 'pending';
```

`EXPLAIN (ANALYZE, BUFFERS)` — основной инструмент. Разбираю вывод построчно.

---

### 2. Читаю EXPLAIN

```
Seq Scan on orders  (cost=0.00..45230.00 rows=1000 width=320) (actual time=0.012..182.345 rows=87 loops=1)
  Filter: ((customer_id = 'abc') AND (status = 'pending'))
  Rows Removed by Filter: 449213
  Buffers: shared hit=2145 read=3421
```

| Поле | Что значит |
|------|-----------|
| `Seq Scan` | Полное сканирование таблицы — **красный флаг** |
| `rows=1000` (planner) vs `rows=87` (actual) | Планер ошибся в 10× — statistics устарели |
| `Rows Removed by Filter: 449213` | Прочитали 450k строк, чтобы найти 87 |
| `Buffers: read=3421` | Чтение с диска, не из shared_buffers |

---

### 3. Системная диагностика

#### 3.1 Статистика планировщика

```sql
-- Актуальность статистики
SELECT relname, reltuples, n_live_tup, last_analyze, last_autoanalyze
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- Если reltuples сильно отличается от реального:
ANALYZE orders;
```

**Частая причина:** статистика устарела → планер выбирает Seq Scan вместо Index Scan.

#### 3.2 Индексы: есть ли, используются ли

```sql
-- Какие индексы есть
SELECT indexname, indexdef
FROM pg_indexes WHERE tablename = 'orders';

-- Используются ли
SELECT
    indexrelname,
    idx_scan,           -- сколько раз использовался
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE relname = 'orders'
ORDER BY idx_scan DESC;
```

| idx_scan | Вывод |
|----------|-------|
| 0 | Индекс не используется — или не нужен, или планер его игнорирует |
| > 0, но мало | Индекс есть, но запрос всё равно медленный — проверьте selectivity |

#### 3.3 Текущие запросы и блокировки

```sql
-- Что прямо сейчас висит
SELECT pid, state, wait_event_type, wait_event, 
       now() - query_start AS duration, query
FROM pg_stat_activity
WHERE state != 'idle'
ORDER BY duration DESC;

-- Блокировки
SELECT blocked.pid AS blocked_pid,
       blocking.pid AS blocking_pid,
       blocked.query AS blocked_query,
       blocking.query AS blocking_query
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND NOT bl.granted
JOIN pg_locks kl ON kl.transactionid = bl.transactionid AND kl.granted
JOIN pg_stat_activity blocking ON blocking.pid = kl.pid;
```

#### 3.4 pg_stat_statements — топ медленных

```sql
SELECT
    round(total_exec_time::numeric, 2) AS total_ms,
    calls,
    round(mean_exec_time::numeric, 2) AS mean_ms,
    round(stddev_exec_time::numeric, 2) AS stddev_ms,
    rows,
    shared_blks_hit,
    shared_blks_read,
    left(query, 150) AS query
FROM pg_stat_statements
WHERE query NOT ILIKE '%pg_stat%'
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Ключевые метрики:**

| Метрика | Что смотреть |
|---------|-------------|
| `total_exec_time / calls` | Среднее время |
| `stddev_exec_time` | Высокий → нестабильный запрос (race, lock, cold cache) |
| `shared_blks_read / shared_blks_hit` | > 0.1 → много чтения с диска |
| `rows` | Мало → запрос «дорогой», но мало отдаёт |

---

### 4. Типовые причины (по частоте)

#### a) Отсутствие подходящего индекса (50% случаев)

```sql
-- Запрос:
SELECT * FROM orders WHERE customer_id = 'abc' AND status = 'pending' ORDER BY created_at DESC LIMIT 20;

-- Нет индекса по (customer_id, status, created_at)
-- → Seq Scan + Sort

-- Решение:
CREATE INDEX idx_orders_customer_status_created
    ON orders (customer_id, status, created_at DESC)
    WHERE status = 'pending';  -- partial index: меньше размер, выше selectivity
```

**Порядок колонок в индексе:**
1. Equality (`=`) — сначала
2. Range (`>`, `<`, `BETWEEN`) — потом
3. `ORDER BY` — в конце

```sql
-- Правильно:
WHERE customer_id = 'abc' AND status = 'pending' AND created_at > '2025-01-01'
ORDER BY created_at DESC
→ INDEX (customer_id, status, created_at DESC)

-- Неправильно:
INDEX (created_at, customer_id, status)
→ Планер может использовать только created_at, остальное — filter
```

#### b) Stale statistics (20% случаев)

```sql
-- Симптомы:
-- - Seq Scan вместо Index Scan
-- - Planner estimate в 10–100× от actual
-- - Запрос был быстрым, стал медленным после大批 вставки

-- Решение:
ANALYZE orders;
-- или:
ALTER TABLE orders ALTER COLUMN status SET STATISTICS 1000;  -- больше выборки
```

#### c) Lock contention (10% случаев)

```sql
-- Симптом:
-- - wait_event_type = 'Lock'
-- - Запрос висит 30–300с
-- - Другой сессия держит FOR UPDATE

-- Диагностика:
SELECT * FROM pg_locks WHERE NOT granted;

-- Решение:
-- 1. Уменьшить длительность транзакции
-- 2. Снизить уровень блокировки (FOR UPDATE → FOR SHARE)
-- 3. Разбить на мелкие транзакции
-- 4. Advisory locks для идемпотентности
```

#### d) N+1 из приложения (10% случаев)

```sql
-- Симптом:
-- - Один и тот же запрос 500 раз в секунду
-- - Каждый быстрый (2мс), но суммарно 1с

-- Диагностика:
SELECT query, calls, mean_exec_time
FROM pg_stat_statements
GROUP BY query
HAVING calls > 100
ORDER BY calls DESC;

-- Решение: JOIN FETCH / batch / DTO (см. предыдущий вопрос)
```

#### e) Suboptimal query plan (5% случаев)

```sql
-- Симптом:
-- - Index Scan есть, но медленный
-- - Nested Loop вместо Hash Join
-- - Planner выбирает Seq Scan, хотя индекс есть

-- Диагностика:
EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON) ...

-- Решение:
-- 1. SET enable_nestloop = off;  -- временно, для диагностики
-- 2. CREATE INDEX с INCLUDE (covering index)
CREATE INDEX idx_orders_covering
    ON orders (customer_id, status)
    INCLUDE (total_cents, created_at);  -- ← не нужно читать heap

-- 3. Материализованные представления для сложных сводок
CREATE MATERIALIZED VIEW order_summary AS
    SELECT customer_id, count(*) AS cnt, sum(total_cents) AS total
    FROM orders WHERE status = 'paid'
    GROUP BY customer_id;
CREATE UNIQUE INDEX ON order_summary(customer_id);

REFRESH MATERIALIZED VIEW CONCURRENTLY order_summary;
```

#### f) Table bloat (5% случаев)

```sql
-- Диагностика:
SELECT relname, 
       n_live_tup,
       n_dead_tup,
       round(100.0 * n_dead_tup / nullif(n_live_tup + n_dead_tup, 0), 1) AS dead_pct
FROM pg_stat_user_tables
ORDER BY n_dead_tup DESC;

-- Если dead_pct > 30%:
VACUUM (ANALYZE) orders;
-- или агрессивнее:
VACUUM (ANALYZE, VERBOSE) orders;
```

---

### 5. Инструменты — сводка

| Инструмент | Когда использовать |
|------------|-------------------|
| `EXPLAIN (ANALYZE, BUFFERS)` | Всегда. Первый шаг. |
| `pg_stat_statements` | Топ медленных, частых, нестабильных |
| `pg_stat_activity` | Что сейчас висит, кто ждёт |
| `pg_stat_user_tables` | Статистика, bloat, last_analyze |
| `pg_stat_user_indexes` | Используются ли индексы |
| `pg_locks` | Блокировки, deadlocks |
| `pg_buffercache` | Что в shared_buffers, hit ratio |
| `pg_stat_wal` | WAL throughput (для write-heavy) |
| `autovacuum` logs | VACUUM-задержки |
| `pg_stat_replication` | Lag репликации (если есть) |

---

### 6. Процесс — алгоритм

```
1. EXPLAIN (ANALYZE, BUFFERS) на проблемном запросе
   │
   ├─ Seq Scan?
   │   ├─ Есть подходящий индекс? → почему не используется?
   │   │   ├─ Stats устарели → ANALYZE
   │   │   ├─ Selectivity низкая → partial index / covering index
   │   │   └─ Planner ошибся → SET enable_* / HINT
   │   └─ Нет индекса → CREATE INDEX
   │
   ├─ Nested Loop дорогой?
   │   ├─ Inner scan — Seq Scan → индекс
   │   └─ Loop count высокий → Hash Join (SET enable_nestloop = off)
   │
   ├─ wait_event = Lock?
   │   → pg_locks → кто блокирует → уменьшить транзакцию
   │
   ├─ Buffers: read >> hit?
   │   → shared_buffers мал? table bloat? → VACUUM
   │
   ├─ calls > 100 в pg_stat_statements?
   │   → N+1 из приложения → JOIN FETCH / batch
   │
   └─ Всё выше не помогло?
       → Ревью самого запроса: можно ли упростить?
         → Материализованное представление?
         → Денормализация?
```

---

### 7. Антипаттерны оптимизации

| Что делают | Почему плохо |
|-----------|-------------|
| `SELECT *` | Читают все колонки → covering index бесполезен |
| `OR (a = 1 OR b = 2)` в WHERE | Планер не может использовать два индекса (без bitmap scan) |
| `COUNT(*)` на большой таблице | Полный scan. Решения: материализованное представление, approximate count |
| `ORDER BY` без индекса | Sort = O(n log n) + memory. Решение: индекс по ORDER BY |
| `ILIKE '%foo%'` | Полный scan. Решение: pg_trgm GIN index или Elasticsearch |
| `LIMIT 1` на无序 таблице | Всё равно полный scan. Решение: индекс по WHERE + ORDER BY |
| Добавить индекс «на всякий» | Каждый индекс замедляет INSERT/UPDATE/DELETE. Не нужны = не ставить |

---

### 8. Чеклист: быстрый ответ на «запрос тормозит»

```
1. EXPLAIN ANALYZE — что делает?
2. Seq Scan? → индекс / stats / partial index
3. Lock? → pg_locks / уменьшить транзакцию
4. N+1? → pg_stat_statements calls / JOIN FETCH
5. Bloat? → n_dead_tup / VACUUM
6. Статистика? → ANALYZE
7. Сам запрос? → упростить / covering index / материализованное представление
```

> ⏱ 25.4s · prompt 45576 tok · output 3080 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: определение, границы, пример, типовая ошибка. Это продолжение серии вопросов на собеседовании по PHP-бэкенду. Нужно ответить как senior-разработчик, практик, без воды.

Структура ответа:
1. Определение агрегата — не «куча сущностей», а граница консистентности
2. Ключевой принцип: инварианты должны гарантироваться внутри одной транзакции
3. Как определять границы — практический алгоритм
4. Пример — конкретный, с кодом
5. Типовая ошибка — с объяснением почему она типова
6. Чеклист

Пример: я возьму «Заказ» + «Каталог» + «Доставка» — типичная ошибка, когда делают один большой агрегат «Заказ + Товар + Доставка + Оплата», хотя это 3-4 разных агрегата.

Типовая ошибка: «агрегат = сущность с коллекцией» — то есть путают агрегат с entity-иерархией. Или наоборот: делают слишком большой агрегат, чтобы «было удобно».

Лучше взять конкретный, реалистичный пример. Например: E-commerce. Сущности: Order, OrderLine, Product, Inventory, Shipment, Payment.

Ошибка: сделать Order + Product + Inventory + Shipment + Payment одним агрегатом. Почему это плохо:
- Конкуренция: два заказа на один товар
- Инварианты Payment не должны зависеть от Shipment
- Транзакция слишком длинная
- Невозможно масштабировать

Правильно: Order (агрегат), Inventory (агрегат), Shipment (агрегат), Payment (агрегат). Они взаимодействуют через события/команды.

Начну с определения, потом алгоритм, потом пример, потом ошибка.

</details>

**Кандидат:**

## Агрегат в DDD

### Определение

Агрегат — это **граница консистентности**. Внутри неё все инварианты гарантируются **в одной транзакции**. Снаружи — через события, команды, финальную консистентность.

Не «куча сущностей с коллекцией». Не «таблица с дочерними таблицами». А: **минимальный набор сущностей, который нужно изменить атомарно, чтобы домен остался корректным.**

---

### Как определяю границы

Три вопроса, задаваемых по порядку:

```
1. Какие инварианты должны выполняться ВСЕГДА (даже при сбое)?
   → Они определяют ядро агрегата

2. Какие сущности нельзя изменить, не нарушив эти инварианты?
   → Они внутри агрегата

3. Какие сущности можно изменить асинхронно, не ломая инварианты?
   → Они вне агрегата, связываются событиями
```

---

### Пример: E-commerce

Доменные объекты:

```
Order, OrderLine, Product, Inventory, Shipment, Payment, Refund
```

#### Шаг 1: Инварианты

| Инвариант | Где должен гарантироваться |
|-----------|---------------------------|
| `Order.total = sum(lines)` | **Всегда**, атомарно |
| `OrderLine.quantity > 0` | **Всегда**, атомарно |
| `Order не имеет дубликатов SKU` | **Всегда**, атомарно |
| `Inventory.stock >= 0` | **Всегда**, атомарно |
| `Payment.amount = Order.total` | **Всегда**, атомарно |
| `Shipment.tracked после создания` | Не сразу — можно асинхронно |
| `Product.price` актуален на момент заказа | Момент времени, не транзакция |

#### Шаг 2: Группировка

```
Агрегат 1: Order
├── OrderLine (дочерние, изменяются только через Order)
└── Инварианты: total, quantity, unique SKU, status

Агрегат 2: Inventory
├── StockItem (дочерние)
└── Инварианты: stock >= 0, не двойное списание

Агрегат 3: Payment
└── Инварианты: amount, status, idempotency

Агрегат 4: Shipment
└── Инварианты: tracking, status, carrier

Агрегат 5: Catalog (Product)
└── Инварианты: price, availability
```

#### Шаг 3: Взаимодействие

```
Order ──создаётся──▶ Payment (команда: "оплати")
Order ──оплачен───▶ Inventory (команда: "списывай")
Inventory ──списано──▶ Shipment (событие: "готовь к отправке")
Shipment ──отправлен──▶ Order (событие: "обнови статус")
```

Ключевое: **агрегаты не знают друг о друге напрямую**. Они общаются через команды (синхронно) и события (асинхронно).

---

### Код

```php
// Агрегат: Order
final class Order
{
    /** @var OrderLine[] */
    private array $lines = [];

    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
    ) {}

    // Единственный способ изменить состояние:
    public function addLine(Sku $sku, int $qty, Money $price): void
    {
        $this->assertOpen();
        $this->assertNoDuplicate($sku);

        $this->lines[] = new OrderLine($sku, $qty, $price);
    }

    public function confirmPayment(): void
    {
        $this->assertOpen();
        $this->status = OrderStatus::Paid;
        // Инвариант: статус не может быть Paid без total
        // Гарант: мы внутри одной транзакции
    }

    public function total(): Money
    {
        $t = new Money(0, 'EUR');
        foreach ($this->lines as $l) {
            $t = $t->add($l->lineTotal());
        }
        return $t;
    }
}
```

```php
// Агрегат: Inventory — НЕЗНАЕТ про Order
final class Inventory
{
    /** @var StockItem[] */
    private array $stock = [];

    public function reserve(Sku $sku, int $qty): void
    {
        $item = $this->find($sku);
        if ($item->available() < $qty) {
            throw new InsufficientStockException($sku, $qty);
        }
        $item->reserve($qty);
        // Инвариант: stock >= 0 гарантирован В ЭТОЙ транзакции
    }
}
```

```php
// Use-case: создаёт Order, потом резервирует Inventory
// ДВА агрегата, ДВЕ транзакции, связь через команду
final class PlaceOrder
{
    public function __construct(
        private readonly DoctrineOrderRepository $orders,
        private readonly DoctrineInventoryRepository $inventory,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(CreateOrderRequest $req): Order
    {
        // Транзакция 1: Order
        $order = Order::create(...);
        $this->orders->save($order);  // flush #1

        // Транзакция 2: Inventory
        foreach ($req->items as $item) {
            $this->inventory->reserve(
                new Sku($item->sku),
                $item->qty,
            );
        }
        $this->inventory->save();  // flush #2

        // Асинхронно: Payment
        $this->bus->dispatch(new PaymentRequestedEvent($order->id()));

        return $order;
    }
}
```

---

### Типовая ошибка: «один большой агрегат»

#### Что делают

```php
// Гигантский агрегат:
final class Order
{
    private array $lines;
    private Product $product;      // ← Product ВНУТРИ Order
    private Inventory $inventory;  // ← Inventory ВНУТРИ Order
    private Shipment $shipment;    // ← Shipment ВНУТРИ Order
    private Payment $payment;      // ← Payment ВНУТРИ Order

    public function placeAndPayAndShip(): void
    {
        // Одна транзакция на всё
    }
}
```

#### Почему это ломается

| Проблема | Механизм |
|----------|----------|
| **Concurrency** | Два пользователя заказывают последний товар. Оба проходят `if (stock > 0)`. Оба пишут. Stock = -1. |
| **Длинные транзакции** | Payment API тормозит 3с → транзакция держит lock на Order + Inventory + Shipment → deadlock |
| **Тестирование** | Чтобы тестировать `Order`, нужно мокать Payment, Shipment, Inventory — 4 mock-объекта |
| **Масштабирование** | Невозможно развести Order-сервис и Inventory-сервис — они «одна сущность» |
| **Изменения** | Меняем логику Shipment → правим Order → переписываем тесты на Order |

#### Правильная граница

```
Order          → инварианты: total, lines, status
Inventory      → инварианты: stock >= 0, no double-reserve
Payment        → инварианты: amount, idempotency, status
Shipment       → инварианты: tracking, carrier, status

Каждый — отдельная транзакция.
Взаимодействие — команды + события.
```

---

### Как распознать ошибку на ревью

Красные флаги:

```
1. Агрегат имеет 5+ сущностей с внешними ID
   → Скорее всего, это 2-3 агрегата, слипшихся

2. Метод агрегата вызывает HTTP-запрос
   → Выход за границу транзакции. Разделите.

3. Агрегат знает о статусе другого агрегата
   → Payment.status проверяется в Order. Событие, а не прямая зависимость.

4. Транзакция > 100мс (замерили)
   → Слишком много агрегатов в одной.

5. «Удобно» — единственная причина
   → «Удобно» держать Payment внутри Order = не причина.
```

---

### Чеклист: правильно ли определена граница

```
✅ Внутри агрегата:
   - Инварианты атомарны (одна транзакция)
   - Нет внешних HTTP-вызовов
   - Нет «а, и ещё Payment»
   - Корень — единственная точка изменения

✅ Снаружи агрегата:
   - Взаимодействие через события/команды
   - Финальная консистентность допустима
   - Другой сервис/контекст может не знать про этот агрегат

✅ Размер:
   - 1–3 сущности (корень + 1–2 дочерних)
   - Транзакция < 50мс
   - Тестируется без 10 mock-объектов
```

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Конкурентное изменение агрегата

### 1. Что произойдёт по умолчанию

Без защиты — **last-writer-wins**. Второй процесс затирает изменение первого. Тихо, без ошибок.

```
T0:  Order.total = 1000 (в БД)

T1:  Process A: read Order → total = 1000
T2:  Process B: read Order → total = 1000

T3:  Process A: total += 500 → 1500 → WRITE
T4:  Process B: total += 200 → 1200 → WRITE  ← затерло A

T5:  В БД: total = 1200
     Ожидание: 1700 (1000 + 500 + 200)
     Реальность: 1200 (1000 + 200)
     Потеря: 500. Без ошибки. Без лога. Без предупреждения.
```

Это называется **lost update**.

---

### 2. Стратегии (от лёгкой к тяжёлой)

| # | Стратегия | Механизм | Когда |
|---|-----------|----------|-------|
| 1 | Optimistic Locking | `version` column | 90% случаев |
| 2 | Pessimistic Locking | `SELECT FOR UPDATE` | Короткие транзакции, высокий конфликт |
| 3 | Single-Writer (очередь) | Message bus / queue | Один агрегат = один обработчик |
| 4 | Event Sourcing | Append-only log | Аудит, воспроизведение, временные машины |
| 5 | Idempotency Key | Уникальный ключ запроса | Повторные вызовы (retry, webhook) |

---

### 3. Optimistic Locking (основная)

#### Механика

```sql
ALTER TABLE orders ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// Запись:
UPDATE orders
SET total_cents = 1500, version = version + 1
WHERE id = 'abc' AND version = 1;  ← проверям, что никто не менял

-- Если 0 строк обновлено → конфликт → бросаем исключение
```

#### Реализация через Doctrine

**Способ A: `@ORM\Version` (встроенный)**

```php
#[ORM\Entity]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Version]  ← Doctrine автоматически:
    #[ORM\Column(type: Types::INTEGER)]  // 1) добавляет WHERE version = X
    private int $version = 0;            // 2) инкрементит при UPDATE

    #[ORM\Column(type: Types::INTEGER)]
    private int $totalCents;
}
```

```php
// Repository
public function save(Order $order): void
{
    $entity = $this->em->find(OrderEntity::class, $order->id()->value());

    if ($entity === null) {
        throw new OrderNotFound($order->id()->value());
    }

    $entity->setTotalCents($order->total()->amountCents());
    // $entity->version — НЕ трогаем. Doctrine сам инкрементит.

    try {
        $this->em->flush();
    } catch (OptimisticLockException $e) {
        // ← Doctrine бросает, если 0 строк обновлено
        throw new ConcurrentModificationException(
            sprintf('Order %s was modified by another process', $order->id()->value()),
        ) from $e;
    }
}
```

**Способ B: Ручной (контроль)**

```php
public function save(Order $order): void
{
    /** @var OrderEntity $entity */
    $entity = $this->em->find(OrderEntity::class, $order->id()->value());

    $expectedVersion = $entity->getVersion();
    $newTotal = $order->total()->amountCents();

    $query = $this->em->createQuery(
        'UPDATE OrderEntity o
         SET o.totalCents = :total, o.version = o.version + 1
         WHERE o.id = :id AND o.version = :version'
    )
    ->setParameter('total', $newTotal)
    ->setParameter('id', $order->id()->value())
    ->setParameter('version', $expectedVersion);

    $affectedRows = $query->execute();

    if ($affectedRows === 0) {
        throw new ConcurrentModificationException($order->id()->value());
    }
}
```

#### Что видит приложение

```
Process A:
  read  → version = 1
  write → WHERE version = 1 → OK, version → 2

Process B (одновременно):
  read  → version = 1
  write → WHERE version = 1 → 0 rows → CONCURRENT_MODIFICATION
  → retry / abort / show user "конфликт"
```

#### Trade-offs

| Плюс | Минус |
|------|-------|
| Нет блокировки в БД | При высоком конфликте — много retries |
| Простота | Retry loop может быть бесконечным |
| Не «замораживает» строку | Нужно обрабатывать `OptimisticLockException` везде |
| Работает через реплику (write → primary) | `version` — лишний столбец |

#### Retry с exponential backoff

```php
final class SaveWithRetry
{
    public function __construct(
        private readonly DoctrineOrderRepository $repo,
        private readonly int $maxAttempts = 3,
    ) {}

    public function __invoke(Order $order): void
    {
        $attempt = 0;
        $delay = 50; // ms

        while (true) {
            try {
                $this->repo->save($order);
                return; // успех
            } catch (ConcurrentModificationException $e) {
                $attempt++;
                if ($attempt >= $this->maxAttempts) {
                    throw $e; // исчерпали — пробрасываем
                }
                usleep($delay * 1000);
                $delay *= 2; // 50, 100, 200...

                // ← Важно: пере-загрузить агрегат из БД
                $fresh = $this->repo->findById($order->id());
                $this->reapplyChanges($fresh, $order);
            }
        }
    }
}
```

---

### 4. Pessimistic Locking

#### Механика

```sql
BEGIN;
SELECT * FROM orders WHERE id = 'abc' FOR UPDATE;  ← блокирует строку
-- ... изменения ...
UPDATE orders SET ... WHERE id = 'abc';
COMMIT;  ← блокировка снята
```

#### В Doctrine

```php
public function findByIdForUpdate(OrderId $id): Order
{
    $entity = $this->em->createQueryBuilder()
        ->select('o')
        ->from(OrderEntity::class, 'o')
        ->where('o.id = :id')
        ->setParameter('id', $id->value())
        ->setLockMode(LockMode::OPTIMISTIC)  // или PESSIMISTIC_WRITE
        ->getQuery()
        ->getOneOrNullResult();

    if ($entity === null) {
        throw new OrderNotFound($id->value());
    }

    return $this->toDomain($entity);
}
```

Или через DQL:

```php
$query = $em->createQuery(
    'SELECT o FROM OrderEntity o WHERE o.id = :id'
)->setLockMode(LockMode::PESSIMISTIC_WRITE)
 ->setParameter('id', $id);
```

#### Trade-offs

| Плюс | Минус |
|------|-------|
| Конфликт определяется ДО записи | Блокировка = конкуренты ждут |
| Нет retry loop | Deadlock если порядок блокировок разный |
| Простота | Транзакция должна быть КОРоткой |
| | Если процесс упадёт — lock живёт до `lock_timeout` |

#### Когда использовать

```
✅ Высокий конфликт (10+ concurrent writes на одну строку)
✅ Короткие транзакции (< 50мс)
✅ Критичность: потеря обновления = катастрофа (финансы)

❌ Длинные транзакции (вызов внешнего API внутри lock)
❌ Низкий конфликт (optimistic дешевле)
❌ Worker-процессы с переменной длительностью
```

---

### 5. Single-Writer (очередь)

Идея: **один агрегат = один обработчик в любой момент времени.**

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Command 1   │────▶│              │     │  Command 2   │
│  (HTTP API)  │     │  Queue       │     │  (Webhook)   │
└──────────────┘     │  (per-agg)   │     └──────────────┘
                     │              │
┌──────────────┐     │  Partition   │     ┌──────────────┐
│  Command 3   │────▶│  by orderId │     │  Worker       │
│  (Cron)      │     │              │     │  (1 на agg)   │
└──────────────┘     └──────────────┘     └──────────────┘
```

#### Реализация: RabbitMQ с routing key = orderId

```yaml
# messenger.yaml
framework:
    messenger:
        transports:
            order_commands:
                dsn: 'amqp://host/commands?queue_name=order_commands'
                # routing_key = order_id → partitioning
        routing:
            'App\Domain\Command\AddLineToOrder': order_commands
            'App\Domain\Command\CancelOrder': order_commands
            'App\Domain\Command\ShipOrder': order_commands
```

```php
// Publisher
$this->bus->dispatch(
    new AddLineToOrder($orderId, $sku, $qty),
    ['routing_key' => $orderId->value()],  ← partitioning
);
```

```php
// Consumer (RabbitMQ: 1 consumer per queue instance, или prefetch=1)
// → гарантируется: на один orderId — один обработчик в момент времени
#[AsMessageHandler]
final class AddLineHandler
{
    public function __invoke(AddLineToOrder $cmd): void
    {
        $order = $this->repo->findById($cmd->orderId);
        $order->addLine($cmd->sku, $cmd->qty);
        $this->repo->save($order);
        // ← Конкурентный AddLineToOrder ЖДЁТ в очереди
    }
}
```

#### Trade-offs

| Плюс | Минус |
|------|-------|
| **Никаких конфликтов** (по построению) | Задержка: очередь → 5–50мс |
| Нет retry loop | Throughput ограничен шириной очереди |
| Естественная idempotency (dedup по orderId) | Нужно гарантировать: 1 consumer per partition |
| Аудит: вся история команд в очереди | Сложность инфраструктуры |

#### Когда использовать

```
✅ Финансы, заказы, инвентарь — потеря обновления = деньги
✅ Высокий конфликт (10+ concurrent writes)
✅ Нужен аудит (вся история команд)
✅ Уже есть message bus

❌ Простые CRUD (< 5 concurrent writes)
✅ Реальное время (очередь = задержка)
```

---

### 6. Event Sourcing

Агрегат = **последовательность событий**. Состояние = fold событий.

```
Order:
  + OrderCreated(id: 1, customer: X)
  + LineAdded(sku: A, qty: 2, price: 100)
  + LineAdded(sku: B, qty: 1, price: 200)
  + PaymentReceived(amount: 400)
  + Shipped(carrier: DHL)

Состояние: status=Shipped, total=400, lines=[A×2, B×1]
```

```php
// Append-only: никогда не UPDATE, только INSERT
CREATE TABLE order_events (
    order_id UUID NOT NULL,
    seq INT NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    payload JSONB NOT NULL,
    occurred_at TIMESTAMPTZ NOT NULL,
    PRIMARY KEY (order_id, seq)
);
```

```php
// Запись:
INSERT INTO order_events (order_id, seq, event_type, payload)
VALUES ('abc', 5, 'PaymentReceived', '{"amount": 400}')
ON CONFLICT (order_id, seq) DO NOTHING;  ← идемпотентно

// Если seq = 5 уже существует → конфликт → повторить/отклонить
```

#### Trade-offs

| Плюс | Минус |
|------|-------|
| Полная история (аудит) | Сложность: fold, projection, snapshot |
| Конфликт = конфликт seq (простой) | Read-модель ≠ write-модель |
| Время-машина (откат) | Обучение команды |
| Idempotency по seq | Миграции сложны |

**Когда:** финансы, compliance, «кто что когда сделал», откаты.
**Когда НЕ:** простой CRUD, < 5 лет проекта, небольшая команда.

---

### 7. Idempotency Key

Защищает от **повторного** выполнения (retry, webhook, double-click).

```php
// Клиент шлёт:
POST /orders/123/lines
Idempotency-Key: req-abc-456

// Сервер:
// 1. SELECT FROM idempotency WHERE key = 'req-abc-456'
// 2. Если есть → вернуть сохранённый ответ
// 3. Если нет → обработать, сохранить ответ под key
```

```sql
CREATE TABLE idempotency_keys (
    key VARCHAR(255) PRIMARY KEY,
    response_body JSONB,
    response_code INT,
    created_at TIMESTAMPTZ DEFAULT now(),
    expires_at TIMESTAMPTZ NOT NULL
);
```

```php
public function addLine(AddLineRequest $req, string $idempotencyKey): Response
{
    $existing = $this->idempotency->find($idempotencyKey);
    if ($existing !== null) {
        return $existing; // ← повтор → тот же ответ
    }

    $order = $this->repo->findById($req->orderId);
    $order->addLine($req->sku, $req->qty, $req->price);
    $this->repo->save($order);

    $response = new Response(null, 201);
    $this->idempotency->save($idempotencyKey, $response);

    return $response;
}
```

**Не заменяет** optimistic locking — дополняет. Optimistic защищает от **разных** одновременных изменений. Idempotency — от **одного и того же** повторного.

---

### 8. Сравнение стратегий

| Критерий | Optimistic | Pessimistic | Single-Writer | Event Sourcing |
|----------|-----------|-------------|---------------|----------------|
| Конфликт | Detect after write | Prevent (lock) | Prevent (serialize) | Detect (seq) |
| Latency | +1 row (version) | Lock wait | Queue delay | Fold |
| Throughput | Высокий | Ограничен lock | Ограничен width | Высокий (append) |
| Аудит | Нет | Нет | Очередь | Полная |
| Сложность | Низкая | Низкая | Средняя | Высокая |
| Retry | Да (loop) | Нет | Нет | Да |
| Deadlock | Нет | Да | Нет | Нет |

---

### 9. Антипаттерны

#### a) Optimistic locking без retry

```php
try {
    $this->repo->save($order);
} catch (OptimisticLockException) {
    // ...и что? Пустой catch? throw без контекста?
    // Пользователь видит 500. Без retry.
}
```

**Решение:** retry loop с backoff + upper bound.

#### b) Pessimistic lock + внешний HTTP-вызов

```php
BEGIN;
SELECT * FROM orders WHERE id = 'x' FOR UPDATE;
$paymentResult = $stripeClient->charge(...);  ← 3–10с!
UPDATE orders SET ...;
COMMIT;
```

**Проблема:** строка заблокирована 3–10 секунд. Все конкуренты ждут. При 10 конкурентах — очередь на 30–100с.

**Решение:** сначала HTTP, потом короткая транзакция. Или single-writer.

#### c) Optimistic locking на агрегате с 50 дочерними

```php
// Order с 50 OrderLine
// Каждое изменение = UPDATE orders SET version = version + 1
// + 50 UPDATE order_lines
// → 51 statements в транзакции
// → lock на 51 строки
```

**Проблема:** при конфликте — откат 51 statements. Дорого.

**Решение:** version на уровне агрегата (1 строка), дочерние — cascade. Или single-writer.

#### d) «Мы используем Redis lock»

```php
$redis->set('lock:order:123', 1, ['NX', 'EX' => 30]);
try {
    // ... бизнес-логика ...
} finally {
    $redis->del('lock:order:123');
}
```

**Проблемы:**
- Если процесс упадёт между `set` и `del` — lock живёт 30с (TTL).
- Если Redis down — нет lock → race.
- Нет механизма «кто владеет lock» (Eviction).
- Не работает через реплику (Redis ≠ source of truth).

**Когда OK:** как дополнение, не как единственная защита.

---

### 10. Практический выбор

```
Конфликт < 1/час на строку
    → Optimistic Locking (version column) + retry

Конфликт 1–10/час на строку
    → Optimistic + idempotency (если есть retry/webhook)

Конфликт > 10/час на строку
    → Single-Writer (очередь per aggregate)
    + Optimistic как safety net

Финансы / compliance
    → Event Sourcing
    + Single-Writer
    + Idempotency

Короткая транзакция, критичность
    → Pessimistic (FOR UPDATE)
    ТОЛЬКО если транзакция < 100мс
```

---

### 11. Итого

```
Потеря обновления — молчаливый, самый опасный класс ошибок.

Защита = 3 слоя:

1. МЕХАНИЗМ (version / lock / queue / seq)
   → Определяет «когда» конфликт обнаружен

2. ПОЛИТИКА (retry / abort / last-writer / merge)
   → Определяет «что делать» при конфликте

3. НАБЛЮДАЕМОСТЬ (лог, метрики, алерт)
   → Определяет «знаем ли мы, что конфликт был»

Без всех трёх — вы «защищаетесь» частично.
```

> ⏱ 21.7s · prompt 48518 tok · output 2603 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в DDD контексте: публикация, обработка, trade-offs, связь с транзакциями. Это продолжение серии вопросов о DDD, агрегатах, конкурентности.

Нужно структурировать ответ:
1. Что такое доменное событие (контраст с application events)
2. Когда публикуем (после изменения агрегата, внутри транзакции)
3. Как обрабатываем (synchronous vs asynchronous)
4. Связь с транзакциями — ключевой момент (outbox pattern)
5. Trade-offs
6. Антипаттерны

Ключевой момент: события должны быть опубликованы АТОМАРНО с изменением агрегата. Если агрегат сохранён, а событие потеряно — рассинхрон. Если событие отправлено, а агрегат не сохранён — "зомби" событие.

Подход:
- Domain events собираются на агрегате
- Публикуются после flush (чтобы гарантировать, что изменения в БД)
- Outbox pattern для гарантии

Разберу детально с кодом.

</details>

**Кандидат:**

## Доменные события в DDD

### 1. Что это

Доменное событие — факт, который **уже произошёл** в домене. Не «сделай X», а «X произошло».

```
Domain Event:      OrderPlaced, PaymentReceived, StockReserved
Application Event: SendEmail, UpdateAnalytics, NotifyFulfillment
```

Ключевое отличие:

| | Domain Event | Application Event |
|--|-------------|-------------------|
| Кто публикует | Агрегат | Use-case / сервис |
| Что означает | «Факт в домене» | «Что-то нужно сделать» |
| Зависит от домена | Да | Нет |
| Пример | `OrderPlaced` | `SendConfirmationEmail` |

---

### 2. Жизненный цикл события

```
1. Агрегат фиксирует факт:
   $order->lines[] = new OrderLine(...);
   $order->status = OrderStatus::Placed;
   $order->recordEvent(new OrderPlaced(...));  ← событие на агрегате

2. Use-case сохраняет агрегат:
   $this->repo->save($order);  ← flush (commit)

3. Событие публикуется:
   $events = $order->releaseEvents();
   foreach ($events as $e) {
       $this->bus->dispatch($e);
   }

4. Обработчики реагируют:
   → SendEmail
   → ReserveInventory
   → UpdateAnalytics
```

**Критично:** событие публикуется **после** успешного flush. Не до.

---

### 3. Публикация: код

#### 3.1 Событие на агрегате

```php
// Domain/Order/Order.php
namespace App\Domain\Order;

final class Order
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
        private OrderStatus $status = OrderStatus::Draft,
    ) {}

    public function addLine(Sku $sku, int $qty, Money $price): void
    {
        $this->assertDraft();
        $this->lines[] = new OrderLine($sku, $qty, $price);
        // Событие НЕ здесь. Событие — на уровне «факта завершённого действия».
    }

    public function confirm(): void
    {
        $this->assertDraft();
        $this->assertHasLines();

        $this->status = OrderStatus::Placed;

        $this->recordEvent(
            new OrderPlaced(
                orderId: $this->id,
                customerId: $this->customerId,
                total: $this->total(),
                at: \DateTimeImmutable::now(),
            ),
        );
    }

    public function markAsShipped(TrackingNumber $tracking): void
    {
        $this->assertPlaced();
        $this->status = OrderStatus::Shipped;
        $this->tracking = $tracking;

        $this->recordEvent(
            new OrderShipped(
                orderId: $this->id,
                tracking: $tracking,
            ),
        );
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
}
```

```php
// Domain/Common/DomainEvent.php
interface DomainEvent
{
    public function occurredAt(): \DateTimeImmutable;
}

// Domain/Order/Event/OrderPlaced.php
final readonly class OrderPlaced implements DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        public CustomerId $customerId,
        public Money $total,
        private \DateTimeImmutable $at,
    ) {}

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->at;
    }
}
```

#### 3.2 Use-case: публикация после flush

```php
// Application/ConfirmOrder.php
namespace App\Application;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use Symfony\Component\Messenger\MessageBusInterface;

final class ConfirmOrder
{
    public function __construct(
        private readonly DoctrineOrderRepository $orders,
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(OrderId $id): Order
    {
        $order = $this->orders->findById($id);

        // 1. Изменяем агрегат (событие зафиксировано внутри)
        $order->confirm();

        // 2. Сохраняем (flush = commit)
        $this->orders->save($order);

        // 3. Публикуем события ПОСЛЕ успешного сохранения
        foreach ($order->releaseEvents() as $event) {
            $this->bus->dispatch($event);
        }

        return $order;
    }
}
```

**Почему именно этот порядок:**

```
❌ dispatch → save:
   Событие отправлено, но save() упало (DB down).
   Worker обработал событие для несуществующего заказа.
   → «Зомби» событие.

✅ save → dispatch:
   Save() упало → событие не отправлено. Корректно.
   Save() OK, dispatch упало → событие потеряно.
   → НО данные в БД корректны. Можно ре-публиковать.

✅✅ Outbox (см. ниже):
   Save + запись события в outbox — АТОМАРНО.
   Dispatch из outbox — отдельный процесс.
```

---

### 4. Обработка событий

#### 4.1 Синхронная (в том же процессе)

```yaml
# messenger.yaml
framework:
    messenger:
        routing:
            'App\Domain\Order\Event\OrderPlaced': sync
```

```php
#[AsMessageHandler]
final class ReserveStockHandler
{
    public function __construct(
        private readonly DoctrineInventoryRepository $inventory,
    ) {}

    public function __invoke(OrderPlaced $event): void
    {
        $order = $this->orders->findById($event->orderId);
        foreach ($order->lines() as $line) {
            $this->inventory->reserve($line->sku(), $line->quantity());
        }
    }
}
```

**Плюсы:**
- Простота: нет очереди, нет worker'ов
- Атомарность: если handler упал — use-case тоже упал
- Отладка: один stack trace

**Минусы:**
- Handler упал → use-case упал → пользователь видит ошибку
- Нельзя добавить handler, не трогая use-case (well, можно, но он выполнится синхронно)
- Один медленный handler замедляет всё

**Когда:** критичные, синхронные, «обязательные» действия (инвентарь, платёж).

---

#### 4.2 Асинхронная (очередь)

```yaml
framework:
    messenger:
        routing:
            'App\Domain\Order\Event\OrderPlaced': async
```

```php
// Worker (отдельный процесс)
#[AsMessageHandler]
final class SendConfirmationEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(OrderPlaced $event): void
    {
        $order = $this->orders->findById($event->orderId);
        $customer = $this->customers->findById($event->customerId);

        $this->mailer->send(
            to: $customer->email(),
            subject: 'Order confirmed',
            body: "...",
        );
    }
}
```

**Плюсы:**
- Не замедляет ответ пользователю
- Изоляция: email упал → заказ не сломан
- Масштабируемость: 10 worker'ов на 1 очередь
- Retry: messenger ретраит автоматически

**Минусы:**
- Задержка (5–50мс)
- Наблюдаемость: «а дошло ли событие?»
- Ordering: нет гарантии последовательности
- Сложность инфраструктуры

**Когда:** не-критичные действия (email, аналитика, уведомления).

---

#### 4.3 Гибрид: critical sync + rest async

```yaml
framework:
    messenger:
        routing:
            # Критичные — синхронно
            'App\Domain\Order\Event\OrderPlaced': sync
            'App\Domain\Inventory\Event\StockReserved': sync

            # Не-критичные — асинхронно
            'App\Application\Event\SendEmail': async
            'App\Application\Event\UpdateAnalytics': async
            'App\Application\Event\NotifyFulfillment': async
```

```php
// Use-case публикует ОБА типа:
public function __invoke(OrderId $id): Order
{
    $order = $this->orders->findById($id);
    $order->confirm();
    $this->orders->save($order);

    foreach ($order->releaseEvents() as $event) {
        $this->bus->dispatch($event);  // sync handler выполнится здесь
    }

    // Асинхронные — через отдельный dispatch
    $this->bus->dispatch(new SendEmailNotification($id));
    $this->bus->dispatch(new UpdateAnalyticsEvent($id));

    return $order;
}
```

---

### 5. Связь с транзакцией: Outbox Pattern

#### 5.1 Проблема

```
T1: INSERT INTO orders ... (BEGIN)
T2: COMMIT
T3: dispatch(OrderPlaced) → RabbitMQ
T4: CRASH

→ Заказ в БД, событие потеряно.
```

Или:

```
T1: dispatch(OrderPlaced) → RabbitMQ (OK)
T2: INSERT INTO orders ... → ROLLBACK (constraint violation)

→ Событие отправлено, заказа нет.
→ Worker обрабатывает «зомби».
```

#### 5.2 Решение: Outbox

```
┌─────────────────────────────────────────────────────────────┐
│  АТОМАРНАЯ ТРАНЗАКЦИЯ (одна COMMIT)                          │
│                                                             │
│  BEGIN                                                      │
│    INSERT INTO orders (...)                                 │
│    INSERT INTO outbox (aggregate_id, event, payload, status)│
│  COMMIT                                                     │
└─────────────────────────────────────────────────────────────┘
         │
         ▼ (отдельный процесс, poll loop)
┌─────────────────────────────────────────────────────────────┐
│  Outbox Poller                                              │
│    SELECT * FROM outbox WHERE status = 'pending' LIMIT 100  │
│    → publish to RabbitMQ                                    │
│    → UPDATE outbox SET status = 'published'                 │
└─────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│  Workers                                                    │
└─────────────────────────────────────────────────────────────┘
```

**Ключевое:** `INSERT INTO orders` и `INSERT INTO outbox` — в **одной транзакции**. Либо оба, либо ни одного.

#### 5.3 Реализация

```php
// Domain/Common/OutboxEntry.php
final readonly class OutboxEntry
{
    public function __construct(
        public string $id,
        public string $aggregateType,
        public string $aggregateId,
        public string $eventType,
        public string $payloadJson,
        public int $createdAt,
        public int $publishedAt = 0,
        public int $attempts = 0,
        public string $status = 'pending',  // pending | published | failed
    ) {}
}
```

```php
// Infrastructure/Persistence/OutboxRepository.php
final class OutboxRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Вызывается ВНУТРИ транзакции use-case.
     * НЕ flush — это делает use-case.
     */
    public function append(string $aggType, string $aggId, DomainEvent $event): void
    {
        $entry = new OutboxEntity();
        $entry->setAggregateType($aggType);
        $entry->setAggregateId($aggId);
        $entry->setEventType($event::class);
        $entry->setPayloadJson(json_encode($event));
        $entry->setCreatedAt(time());

        $this->em->persist($entry);
        // flush — НЕ здесь. use-case решает, когда фиксировать.
    }
}
```

```php
// Application/ConfirmOrder.php
final class ConfirmOrder
{
    public function __construct(
        private readonly DoctrineOrderRepository $orders,
        private readonly OutboxRepository $outbox,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(OrderId $id): Order
    {
        $order = $this->orders->findById($id);
        $order->confirm();

        // Сохраняем агрегат
        $this->orders->saveWithoutFlush($order);

        // Записываем события в outbox (В ТОЙ ЖЕ транзакции)
        foreach ($order->releaseEvents() as $event) {
            $this->outbox->append(
                aggType: 'Order',
                aggId: $order->id()->value(),
                event: $event,
            );
        }

        // ОДИН flush = АТОМАРНО
        $this->em->flush();  // ← orders + outbox вместе

        return $order;
    }
}
```

```php
// Infrastructure/OutboxPoller.php (отдельный процесс)
#[AsCommand(name: 'outbox:publish')]
final class OutboxPublishCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
        private readonly int $batchSize = 100,
        private readonly int $maxAttempts = 5,
    ) {}

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $entries = $this->em->createQuery(
            'SELECT o FROM OutboxEntity o
             WHERE o.status = "pending" AND o.attempts < :max
             ORDER BY o.createdAt ASC'
        )
        ->setParameter('max', $this->maxAttempts)
        ->setMaxResults($this->batchSize)
        ->getResult();

        foreach ($entries as $entry) {
            $event = $this->deserialize($entry);

            try {
                $this->bus->dispatch($event);
                $entry->setStatus('published');
                $entry->setPublishedAt(time());
            } catch (\Throwable $e) {
                $entry->setAttempts($entry->getAttempts() + 1);

                if ($entry->getAttempts() >= $this->maxAttempts) {
                    $entry->setStatus('failed');
                    $out->writeln(sprintf(
                        'FAILED after %d attempts: %s (%s)',
                        $entry->getAttempts(),
                        $entry->getEventType(),
                        $e->getMessage(),
                    ));
                }
            }
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function deserialize(OutboxEntity $entry): object
    {
        $data = json_decode($entry->getPayloadJson(), true);
        $class = $entry->getEventType();

        // Реализация зависит от структуры события
        return new $class(...array_values($data));
    }
}
```

```bash
# Запуск (Procfile / Docker)
outbox-poller: while true; do bin/console outbox:publish; sleep 1; done
```

#### 5.4 Что гарантирует Outbox

```
✅ Заказ сохранён → событие ОБЯЗАТЕЛЬНО будет опубликовано
✅ Заказ не сохранён → событие НЕ было опубликовано
✅ Crash между INSERT и COMMIT → оба откатываются
✅ Crash после COMMIT, до publish → poller подхватит при следующем цикле
✅ Worker упал при обработке → retry (messenger) или manual replay
```

---

### 6. Trade-offs

| Подход | Гарантия | Задержка | Сложность | Когда |
|--------|----------|----------|-----------|-------|
| Sync (в use-case) | Атомарность с use-case | 0 | Низкая | Критичные, «или всё или ничего» |
| Async (очередь) | At-least-once | 5–50мс | Средняя | Не-критичные |
| Outbox | **Гарантированная доставка** | 1–5с | Высокая | Финансы, compliance |
| Kafka | At-least-once, ordered, replay | 5–50мс | Очень высокая | High throughput, audit |

---

### 7. Что доменные события РЕШАЮТ

| Проблема | Решение |
|----------|---------|
| A и B не знают друг о друге | События = «общий язык» |
| Изменение в A должно отразиться в B, но не блокировать A | Async обработка |
| Нужен аудит «что произошло» | События = append-only log |
| Новые подписчики без изменения издателя | Добавление handler'а |
| Разные скорости обработки | Очередь буферизует |
| Повторная обработка (replay) | Outbox / Kafka |

---

### 8. Какие проблемы СОЗДАЮТ

| Проблема | Механизм | Митигация |
|----------|----------|-----------|
| **Рассинхрон** | Событие обработано, но данные изменились | Idempotency + eventual consistency |
| **Потеря события** | Crash между dispatch и обработкой | Outbox / persistent queue |
| **Дублирование** | Retry → событие обработано 2× | Idempotency key на handler'е |
| **Порядок** | `OrderPlaced` → `OrderShipped` пришло вперемешку | Per-aggregate ordering (Kafka partition) |
| **Наблюдаемость** | «А дошло ли событие?» | Dead letter queue + алерты |
| **Отладка** | Stack trace разбит на 3 процесса | Correlation ID через события |
| **Семантика** | Событие = «факт», но handler интерпретирует по-своему | Явные контракты (schema) |
| **Миграции** | Событие изменило структуру | Versioning (event v1, v2) |

---

### 9. Антипаттерны

#### a) Событие как команда

```php
// ПЛОХО:
class SendEmailToUser implements DomainEvent
{
    // ← Это не факт, это действие.
    // Доменное событие: "OrderPlaced" (факт)
    // Application event: "SendEmail" (действие)
}
```

**Правильно:**
```php
// Domain: OrderPlaced (факт)
// Handler: SendEmail (реакция)
// Связь: OrderPlaced → SendEmail (в handler'е)
```

#### b) Событие с бизнес-логикой в handler'е

```php
// ПЛОХО:
#[AsMessageHandler]
final class OnOrderPlaced
{
    public function __invoke(OrderPlaced $event): void
    {
        // Здесь: расчёт скидки, проверка инвентаря, обновление заказа
        $this->orders->updateDiscount($event->orderId, 10);
        $this->inventory->reserve(...);
        $this->orders->updateStatus(...);
    }
}
```

**Проблема:** Handler знает слишком много. Должен быть тонким: «реакция на факт».

**Правильно:**
```php
#[AsMessageHandler]
final class OnOrderPlaced_ReserveStock
{
    public function __invoke(OrderPlaced $event): void
    {
        $this->inventory->reserveForOrder($event->orderId);
        // Только одно действие
    }
}
```

#### c) Событие без idempotency

```php
// ПЛОХО:
#[AsMessageHandler]
final class OnPaymentReceived
{
    public function __invoke(PaymentReceived $event): void
    {
        $this->accounts->credit($event->accountId, $event->amount);
        // ← Если событие придёт 2 раза (retry) — двойное зачисление!
    }
}
```

**Правильно:**
```php
#[AsMessageHandler]
final class OnPaymentReceived
{
    public function __invoke(PaymentReceived $event): void
    {
        // Idempotency: проверить, что уже не обработано
        if ($this->processed->exists($event->paymentId)) {
            return; // ← идемпотентно
        }

        $this->accounts->credit($event->accountId, $event->amount);
        $this->processed->mark($event->paymentId);
    }
}
```

#### d) Событие «для всего»

```php
// ПЛОХО:
class OrderEvent {
    public Order $order;  // ← весь агрегат в событии
    public array $lines;
    public Customer $customer;
    public Payment $payment;
    // ← 10 полей, 3 агрегата в одном событии
}
```

**Проблема:**
- Событие зависит от структуры 3 агрегатов
- Миграция одного = изменение события
- Handler получает больше, чем нужно

**Правильно:**
```php
class OrderPlaced {
    public OrderId $orderId;       // ← только ID
    public CustomerId $customerId;
    public Money $total;
    public \DateTimeImmutable $at;
}

// Handler сам загружает, что нужно
```

#### e) Sync события для не-критичного

```php
// ПЛОХО:
$this->bus->dispatch(new SendNewsletter($userId));  // sync
// ← Email API упало → весь use-case упал
// ← Пользователь видит 500 из-за newsletter
```

**Правильно:** async + retry + dead letter.

---

### 10. Итоговая схема

```
┌──────────────────────────────────────────────────────────────────┐
│  USE-CASE                                                        │
│                                                                  │
│  1. Load aggregate                                               │
│  2. Mutate (events recorded on aggregate)                        │
│  3. Save aggregate + outbox entries (ONE flush)                  │
│  4. Return response                                              │
└──────────────────────────────────────────────────────────────────┘
         │
         ▼ (атомарно)
┌──────────────────────────────────────────────────────────────────┐
│  DATABASE (transaction)                                          │
│  orders: INSERT/UPDATE                                           │
│  outbox: INSERT (event, payload, status=pending)                 │
└──────────────────────────────────────────────────────────────────┘
         │
         ▼ (poller, 1s interval)
┌──────────────────────────────────────────────────────────────────┐
│  OUTBOX POLLER                                                   │
│  SELECT pending → dispatch to bus → mark published               │
└──────────────────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────────┐
│  WORKERS (async, parallel)                                       │
│  - ReserveStock          (critical, retry 5×)                    │
│  - SendConfirmationEmail (non-critical, retry 3×)                │
│  - UpdateAnalytics       (best-effort, no retry)                 │
└──────────────────────────────────────────────────────────────────┘
```

**Принцип:** данные и событие — атомарно. Обработка — независимо.

> ⏱ 43.4s · prompt 55349 tok · output 5794 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанных вопроса о DDD:
1. Чем VO отличается от Entity
2. Как решать, к чему отнести концепцию
3. Ubiquitous language и его влияние на код

Нужно дать структурированный ответ с конкретными примерами кода на PHP, как в предыдущих ответах. Связать с уже обсуждёнными темами (Money, Order, OrderLine). Показал бы разницу на практике: когда VO, когда Entity, и как язык домена проявляется в именах.

Структура:
1. Формальная разница (таблица)
2. Критерии определения
3. Примеры: Money (VO), Order (Entity), OrderLine (Entity-дочерняя)
4. Сложные случаи: Customer, Address, Product
5. Ubiquitous language: что это, как влияет на код, примеры
6. Антипаттерны

</details>

**Кандидат:**

## Value Object vs Entity

### 1. Формальная разница

| | Entity | Value Object |
|--|--------|-------------|
| Идентичность | По **id** | По **значению** |
| Равенство | `a->id === b->id` | `a->field1 === b->field1 && ...` |
| Изменяемость | Мutable (жизненный цикл) | Immutable (создан = неизменён) |
| Временная ось | Прошлая, текущая, будущая | Только «состояние в момент X» |
| Пример | `Order(id: 1)` и `Order(id: 2)` — разные, даже если поля одинаковые | `Money(100, 'EUR')` и `Money(100, 'EUR')` — **одинаковые** |

### 2. Критерий выбора

Один вопрос:

> **Если я создам два экземпляра с одинаковыми полями — это одна и та же сущность или две разные?**

```
Одна и та же → Value Object
Две разные   → Entity
```

| Концепция | Ответ | Тип |
|-----------|-------|-----|
| `Money(100, 'EUR')` × 2 | Одна и та же (100 центов — это 100 центов) | **VO** |
| `Sku('ABC-123')` × 2 | Одна и та же | **VO** |
| `Address('ул. Ленина, 1')` × 2 | Одна и та же | **VO** |
| `Order(id: 1)` × 2 | **Две разные** (два заказа, даже с одинаковыми строками) | **Entity** |
| `Customer(id: 'x')` × 2 | **Две разные** (два человека, даже с одинаковым email) | **Entity** |
| `OrderLine(id: 5)` × 2 | **Две разные** (две позиции в разных заказах) | **Entity** |
| `Period(from, to)` × 2 | Одна и та же (интервал — это значение) | **VO** |
| `Email('a@b.c')` × 2 | Одна и та же | **VO** |

### 3. Почему это важно для кода

```php
// VO: равенство по значению
final readonly class Money
{
    public function __construct(
        public int $amountCents,
        public string $currency,
    ) {}

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }

    // НЕТ $id. НЕТ mutable-состояния.
    // Money(100, 'EUR') === Money(100, 'EUR') по смыслу
}
```

```php
// Entity: равенство по id
final class Order
{
    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
        private OrderStatus $status,
        /** @var OrderLine[] */
        private array $lines = [],
    ) {}

    public function id(): OrderId
    {
        return $this->id;
    }

    // ДВА Order с одинаковыми линиями — РАЗНЫЕ заказы
    // Равенство только по id
    public function sameAs(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
```

**Практическое следствие:**

```php
// VO: можно свободно передавать, сравнивать, использовать в collections
$cart->add(new Money(100, 'EUR'));
$cart->add(new Money(100, 'EUR'));  // ← две позиции по 100 центов
// Это две РАЗНЫЕ позиции в корзине (разные sku),
// но Money-объекты равны

// Entity: нельзя «потерять» идентичность
$order->addLine(new OrderLine($sku, 2, new Money(100, 'EUR')));
// OrderLine имеет id в БД. Если мы потеряем id — потеряем связь с Order
```

### 4. Сложные случаи

#### Customer

```
Customer(id: 'x', name: 'Иван', email: 'ivan@mail.ru')
```

**Entity.** Даже если два клиента имеют одинаковое имя и email — это два разных человека.

Но внутри Customer — VO:

```php
final class Customer
{
    public function __construct(
        private readonly CustomerId $id,
        private Name $name,        // ← VO
        private Email $email,      // ← VO
        private readonly string $birthYear,
    ) {}

    public function name(): Name { return $this->name; }
    public function email(): Email { return $this->email; }
}

// VO внутри:
final readonly class Name
{
    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {}

    public function full(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}

final readonly class Email
{
    public function __construct(
        public string $value,
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail($value);
        }
    }

    public function equals(self $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }
}
```

#### Product

```
Product(id: 'p1', sku: 'ABC', name: 'Винт', price: Money(100, 'EUR'))
```

**Entity.** `Product(id: 'p1')` и `Product(id: 'p2')` с одинаковым SKU — два разных каталожных номера (например, от разных поставщиков).

Но:
- `Sku('ABC')` — **VO** (идентификатор, не сущность)
- `Money(100, 'EUR')` — **VO**
- `ProductImage(url: '...')` — **VO**

#### Address

```
Address(country: 'RU', city: 'Москва', street: 'Ленина', house: 1)
```

**VO.** Два заказа с одинаковым адресом — адрес один и тот же. У адреса нет id, нет жизненного цикла.

```php
final readonly class Address
{
    public function __construct(
        public Country $country,
        public string $city,
        public string $street,
        public int $house,
    ) {}

    public function equals(self $other): bool
    {
        return $this->country->code() === $other->country->code()
            && $this->city === $other->city
            && $this->street === $other->street
            && $this->house === $other->house;
    }
}
```

---

## Ubiquitous Language (Единый язык)

### 1. Что это

Единый язык — **совпадение терминов** между:
- Разговорами с бизнес-аналитиком
- Документацией
- Именами в коде
- Именами в БД

```
Бизнес говорит:      «Оплатить заказ»
Код говорит:         OrderService::pay()
БД говорит:          orders.status = 'paid'
Документация:        «Статус заказа: оплачен»

Это ОДНО и то же.
```

### 2. Как влияет на код

#### a) Имена

```php
// БЕЗ единого языка:
class OrderProcessor
{
    public function doPayment(Action $action): Result
    {
        if ($action->getType() === 2) {
            $this->db->update('tbl_orders', ['st' => 3], ['id' => $action->getId()]);
        }
    }
}

// С единым языком:
final class Order
{
    public function confirmPayment(): void
    {
        $this->status = OrderStatus::Paid;
    }
}
```

Разница: в первом случае нужно **знать**, что `type=2` — это «оплата», что `st=3` — это «оплачен». Во втором — имя говорит само за себя.

#### b) Границы

```php
// Бизнес: «заказ» и «посылка» — разные вещи
// Код:
class Order { ... }       // ← «заказ» — домен заказа
class Shipment { ... }    // ← «посылка» — домен логистики

// НЕ:
class OrderWithShipment { ... }  // ← смешение двух концепций
```

Единый язык помогает **видеть границы**: если в коде одно имя описывает две разные бизнес-концепции — это сигнал, что агрегаты должны быть разделены.

#### c) События

```php
// Бизнес: «заказ подтверждён» — это факт
// Код:
class OrderConfirmed { ... }  // ← доменное событие

// НЕ:
class ProcessOrder { ... }    // ← это команда, не факт
class OrderDone { ... }       // ← «done» — не бизнес-термин
```

#### d) БД

```sql
-- С единым языком:
CREATE TABLE orders (
    id UUID,
    status VARCHAR(20),     -- 'draft', 'confirmed', 'paid', 'shipped', 'delivered', 'cancelled'
    total_cents INT,
    ...
);

-- БЕЗ:
CREATE TABLE tbl_ord (
    id UUID,
    st INT,                 -- 0=draft, 1=confirmed, 2=paid, 3=shipped, ...
    sum INT,
    ...
);
```

### 3. Как строить единый язык

```
1. Глоссарий
   ┌─────────────────────────────────────────────────────────────┐
   │ Термин         │ Определение                  │ В коде      │
   │────────────────┼──────────────────────────────┼─────────────│
   │ Заказ          │ Набор позиций, которые      │ Order       │
   │                │ клиент хочет приобрести      │             │
   │────────────────┼──────────────────────────────┼─────────────│
   │ Позиция        │ Одна строка заказа: SKU +    │ OrderLine   │
   │                │ количество + цена            │             │
   │────────────────┼──────────────────────────────┼─────────────│
   │ Подтверждение  │ Факт того, что заказ принят  │ confirm()   │
   │                │ к обработке                   │ OrderStatus │
   │                │                              │ ::Confirmed │
   │────────────────┼──────────────────────────────┼─────────────│
   │ Оплата         │ Перевод денег от клиента     │ Payment     │
   │                │ в пользу магазина            │             │
   │────────────────┼──────────────────────────────┼─────────────│
   │ Посылка        │ Физическая отправка заказа   │ Shipment    │
   │                │ курьером                     │             │
   └─────────────────────────────────────────────────────────────┘

2. Правила:
   - Одно термин = одна сущность в коде
   - Имя класса = имя термина (без префиксов/суффиксов)
   - Метод = глагол из глоссария
   - Нет «общих» имён (Manager, Handler, Service) без контекста
```

### 4. Пример: как язык «находит» нарушения

```php
// Бизнес: «заказ» не может быть «отменён» после «отправки»
// Код:
class OrderService
{
    public function cancel(Order $order): void
    {
        if ($order->status === 'shipped') {
            throw new \Exception('Cannot cancel');
        }
        $order->status = 'cancelled';
    }
}

// Вопрос к бизнесу: «А если посылка уже в пути — это отмена?»
// Ответ: «Нет, это возврат. Отмена — до отправки.»

// ← Язык дал нам ДВА термина: «отмена» и «возврат»
// ← В коде это ДВА разных метода:
class Order
{
    public function cancel(): void { /* только из draft/confirmed */ }
    public function initiateReturn(): void { /* из shipped/delivered */ }
}
```

### 5. Антипаттерны единого языка

| Антипаттерн | Пример | Почему плохо |
|-------------|--------|-------------|
| «Технические» имена | `OrderManager`, `OrderHandler` | Не говорит, что делает |
| «Универсальные» имена | `Service`, `Controller` | Нет контекста |
| Числовые коды | `type = 2`, `st = 3` | Нужно знать легенду |
| Смешение языков | `Order.process()` (код) + «обработать» (бизнес) | «Process» ≠ «обработать» (в бизнесе это может быть «подтвердить») |
| Английский в русском домене | `cancelOrder()` в проекте, где говорят «отмена заказа» | Разрыв с бизнес-языком |

### 6. Итого

```
Entity vs VO:
  «Два одинаковых полей = одна сущность?»
  Да → VO. Нет → Entity.

Ubiquitous Language:
  Термин в бизнесе = имя в коде = имя в БД.
  Если нет совпадения — где-то потеряна граница или смысл.
```

Единый язык — это не «переименовать классы». Это **процесс**, при котором код становится **переводом** бизнес-правил, а не «реализацией требований». И тогда ревью кода = ревью бизнес-логики.

> ⏱ 29.5s · prompt 60951 tok · output 3404 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о структуре PHP-приложения по принципам чистой архитектуры. Нужно описать:
1. Слои и что в каждом лежит
2. Правило зависимостей
3. Куда попадают конкретные технологии (Doctrine, Symfony-контроллер, обработчик очереди)
4. Как следить за границами

Я уже в контексте собеседования, отвечаю как PHP-бэкенд-разработчик. Нужно дать практичную, конкретную структуру с примерами кода, а не абстрактные рассуждения.

Структура ответа:
1. Слои (Domain, Application, Infrastructure) + правило зависимостей
2. Конкретная файловая структура
3. Что в каждом слое — с примерами
4. Куда попадают Doctrine, Symfony, Messenger
5. Как enforcing'ить границы (static analysis, tests, структура)

Сделаю компактно, но полно. Использую ту же тональность, что и в предыдущих ответах.

</details>

**Кандидат:**

## Чистая архитектура в PHP-приложении

### 1. Слои и правило зависимостей

```
┌─────────────────────────────────────────────────────────────────────┐
│  INFRASTRUCTURE (внешний мир)                                       │
│  Doctrine, Symfony, RabbitMQ, Mailgun, Stripe, Elasticsearch        │
│                                                                     │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  APPLICATION (use-cases, оркестрация)                         │  │
│  │                                                               │  │
│  │  ┌─────────────────────────────────────────────────────────┐  │  │
│  │  │  DOMAIN (правила, инварианты, события)                  │  │  │
│  │  │                                                         │  │  │
│  │  │  НОЛЬ зависимостей от мира                              │  │  │
│  │  └─────────────────────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘

Правило: зависимости направлены ВНЕ → ВНУТРЬ.
Domain не знает, что такое Symfony. Application не знает, что такое Doctrine.
```

**Стрелки зависимостей:**

```
Infrastructure → Application → Domain
     ↓                ↓            ↓
  (реализует)   (оркестрирует)  (ничего не знает)
```

---

### 2. Файловая структура

```
src/
├── Domain/                          ← ЧИСТЫЙ PHP. Ноль vendor-зависимостей.
│   ├── Order/
│   │   ├── Order.php               ← Агрегат
│   │   ├── OrderLine.php           ← Дочерняя сущность
│   │   ├── OrderId.php             ← VO
│   │   ├── OrderStatus.php         ← Enum
│   │   ├── Event/
│   │   │   ├── OrderPlaced.php
│   │   │   ├── OrderShipped.php
│   │   │   └── OrderCancelled.php
│   │   ├── Exception/
│   │   │   ├── OrderNotFound.php
│   │   │   ├── DuplicateLine.php
│   │   │   └── OrderClosed.php
│   │   └── Port/                   ← Интерфейсы, которые Domain требует от мира
│   │       ├── OrderRepository.php
│   │       ├── Clock.php
│   │       └── EventPublisher.php
│   │
│   ├── Inventory/
│   │   ├── Inventory.php
│   │   ├── StockItem.php
│   │   ├── Event/
│   │   └── Port/
│   │       └── InventoryRepository.php
│   │
│   └── Common/
│       ├── Money.php               ← VO
│       ├── Sku.php                 ← VO
│       ├── DomainEvent.php         ← Интерфейс
│       └── Exception/
│           └── DomainException.php
│
├── Application/                     ← Use-cases. Зависит от Domain. НЕ от Infrastructure.
│   ├── Order/
│   │   ├── PlaceOrder.php          ← Use-case (command handler)
│   │   ├── ConfirmOrder.php
│   │   ├── ShipOrder.php
│   │   └── Command/
│   │       ├── PlaceOrderCommand.php
│   │       └── ConfirmOrderCommand.php
│   │
│   ├── Inventory/
│   │   ├── ReserveStock.php
│   │   └── ReleaseStock.php
│   │
│   └── Shared/
│       ├── UseCase.php             ← Маркерный интерфейс
│       └── IdempotencyKey.php
│
├── Infrastructure/                  ← Зависит от Application + Domain.
│   ├── Persistence/                ← Doctrine
│   │   ├── Order/
│   │   │   ├── OrderEntity.php     ← Entity (mapping)
│   │   │   ├── OrderLineEntity.php
│   │   │   └── DoctrineOrderRepository.php  ← Реализация Domain\Order\Port\OrderRepository
│   │   ├── Inventory/
│   │   │   └── DoctrineInventoryRepository.php
│   │   └── Outbox/
│   │       ├── OutboxEntity.php
│   │       └── DoctrineOutboxRepository.php
│   │
│   ├── Http/                       ← Symfony (входные адаптеры)
│   │   ├── Controller/
│   │   │   ├── OrderController.php
│   │   │   └── InventoryController.php
│   │   ├── Request/
│   │   │   ├── PlaceOrderRequest.php   ← DTO (не Domain!)
│   │   │   └── ConfirmOrderRequest.php
│   │   └── ExceptionListener.php   ← Domain\Exception → HTTP 4xx
│   │
│   ├── Message/                    ← Symfony Messenger (входные + выходные адаптеры)
│   │   ├── Handler/
│   │   │   ├── PlaceOrderHandler.php   ← Подписчик команды
│   │   │   └── ReserveStockHandler.php
│   │   └── Subscriber/
│   │       ├── SendOrderEmail.php      ← Реакция на доменное событие
│   │       └── UpdateSearchIndex.php
│   │
│   ├── Notification/               ← Выходные адаптеры (внешние сервисы)
│   │   ├── MailgunMailer.php
│   │   └── SmsGateway.php
│   │
│   └── Search/
│       └── ElasticsearchClient.php
│
├── Config/                          ← Композиция (DI wiring)
│   ├── services.yaml
│   ├── messenger.yaml
│   └── doctrine.yaml
│
└── Kernel.php                       ← Entry point

tests/
├── Domain/                          ← Unit tests. Ноль mock'ов.
│   ├── OrderTest.php
│   └── MoneyTest.php
│
├── Application/                     ← Integration tests. Fake-репозитории.
│   ├── PlaceOrderTest.php
│   └── ConfirmOrderTest.php
│
└── Infrastructure/                  ← Adapter tests. Реальная БД (testcontainer).
    └── DoctrineOrderRepositoryTest.php
```

---

### 3. Что в каждом слое

#### Domain — «что» и «почему»

```php
// src/Domain/Order/Order.php
// ЗАВИСИМОСТИ: только PHP stdlib. НОЛЬ use Symfony\*, Doctrine\*, ...

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Common\Sku;
use App\Domain\Order\Event\OrderPlaced;
use App\Domain\Order\Exception\DuplicateLine;
use App\Domain\Order\Exception\OrderClosed;

final class Order
{
    private array $lines = [];
    private array $domainEvents = [];

    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
        private OrderStatus $status = OrderStatus::Draft,
    ) {}

    public function addLine(Sku $sku, int $qty, Money $unitPrice): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderClosed($this->id->value());
        }

        foreach ($this->lines as $existing) {
            if ($existing->sku()->equals($sku)) {
                throw new DuplicateLine($sku->value());
            }
        }

        $this->lines[] = new OrderLine($sku, $qty, $unitPrice);
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderClosed($this->id->value());
        }

        if (count($this->lines) === 0) {
            throw new \DomainException('Cannot confirm empty order');
        }

        $this->status = OrderStatus::Confirmed;
        $this->record(new OrderPlaced($this->id, $this->total(), \DateTimeImmutable::now()));
    }

    public function total(): Money
    {
        $t = new Money(0, 'EUR');
        foreach ($this->lines as $l) {
            $t = $t->add($l->lineTotal());
        }
        return $t;
    }

    public function id(): OrderId { return $this->id; }
    public function status(): OrderStatus { return $this->status; }
    /** @return OrderLine[] */
    public function lines(): array { return $this->lines; }

    public function releaseEvents(): array
    {
        $e = $this->domainEvents;
        $this->domainEvents = [];
        return $e;
    }

    private function record(\App\Domain\Common\DomainEvent $e): void
    {
        $this->domainEvents[] = $e;
    }
}
```

**Что в Domain НЕ может быть:**
- `use Symfony\...`
- `use Doctrine\...`
- `use App\Infrastructure\...`
- `use App\Application\...`
- `EntityManagerInterface`
- `Request`, `Response`
- `MessageBusInterface`
- `LoggerInterface`

**Что в Domain МОЖЕТ быть:**
- PHP stdlib (`DateTimeImmutable`, `ArrayAccess`, `Stringable`)
- Свой `DomainEvent` interface
- Свой `DomainException`
- Enum, readonly, match

---

#### Application — «как» (оркестрация)

```php
// src/Application/Order/PlaceOrder.php
// ЗАВИСИМОСТИ: Domain (через интерфейсы-порты). НЕ Infrastructure.

namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\CustomerId;
use App\Domain\Common\Money;
use App\Domain\Common\Sku;
use App\Domain\Order\Event\OrderPlaced;
use App\Domain\Order\Port\OrderRepository;
use App\Domain\Order\Port\EventPublisher;
use App\Domain\Order\Port\Clock;

final class PlaceOrder
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly EventPublisher $events,
        private readonly Clock $clock,
    ) {}

    public function __invoke(PlaceOrderCommand $cmd): Order
    {
        $order = new Order(
            id: new OrderId($cmd->orderId),
            customerId: new CustomerId($cmd->customerId),
        );

        foreach ($cmd->items as $item) {
            $order->addLine(
                sku: new Sku($item->sku),
                qty: $item->quantity,
                unitPrice: new Money($item->priceCents, $item->currency),
            );
        }

        $order->confirm();

        // ← Сохраняем через порт (интерфейс из Domain)
        $this->orders->save($order);

        // ← Публикуем события через порт
        foreach ($order->releaseEvents() as $event) {
            $this->events->publish($event);
        }

        return $order;
    }
}
```

**Ключевое:** Application знает **интерфейсы** из Domain (`OrderRepository`, `EventPublisher`), но **не знает** их реализации (Doctrine, RabbitMQ). Реализации подставляются через DI.

**Что в Application МОЖЕТ быть:**
- Доменные объекты и их методы
- Команды/DTO (входные данные use-case)
- Интерфейсы-порты из Domain

**Что в Application НЕ может быть:**
- `use Symfony\Component\HttpFoundation\...`
- `use Doctrine\ORM\...`
- `use App\Infrastructure\...`
- `EntityManagerInterface`
- Конкретные адаптеры (`MailgunMailer`, `ElasticsearchClient`)

---

#### Infrastructure — «с помощью чего»

```php
// src/Infrastructure/Persistence/Order/DoctrineOrderRepository.php
// РЕАЛИЗАЦИЯ порта из Domain

namespace App\Infrastructure\Persistence\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\CustomerId;
use App\Domain\Common\Money;
use App\Domain\Common\Sku;
use App\Domain\Order\Port\OrderRepository;  ← реализуем ЭТО
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

final class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(OrderId $id): Order
    {
        /** @var OrderEntity $entity */
        $entity = $this->em->find(OrderEntity::class, $id->value());

        if ($entity === null) {
            throw new \App\Domain\Order\Exception\OrderNotFound($id->value());
        }

        return $this->toDomain($entity);
    }

    public function save(Order $order): void
    {
        $entity = $this->findOrCreate($order->id());
        $this->sync($entity, $order);
        $this->em->flush();
    }

    // ... маппинг entity ↔ domain
}
```

```php
// src/Infrastructure/Http/Controller/OrderController.php
// ТОНКИЙ адаптер: DTO → use-case → Response

namespace App\Infrastructure\Http\Controller;

use App\Application\Order\PlaceOrder;
use App\Application\Order\PlaceOrderCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders', name: 'order_')]
final class OrderController
{
    public function __construct(
        private readonly PlaceOrder $placeOrder,
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $cmd = new PlaceOrderCommand(
            orderId: $data['id'],
            customerId: $data['customer_id'],
            items: array_map(fn($i) => [
                'sku' => $i['sku'],
                'quantity' => $i['qty'],
                'priceCents' => $i['price_cents'],
                'currency' => $i['currency'],
            ], $data['items']),
        );

        $order = ($this->placeOrder)($cmd);

        return new JsonResponse([
            'id' => $order->id()->value(),
            'status' => $order->status()->value,
            'total' => $order->total()->amountCents(),
        ], 201);
    }
}
```

```php
// src/Infrastructure/Message/Handler/ReserveStockHandler.php
// Адаптер: доменное событие → действие в мире

namespace App\Infrastructure\Message\Handler;

use App\Domain\Order\Event\OrderPlaced;
use App\Application\Inventory\ReserveStock;
use App\Application\Inventory\ReserveStockCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ReserveStockHandler
{
    public function __construct(
        private readonly ReserveStock $reserveStock,
    ) {}

    public function __invoke(OrderPlaced $event): void
    {
        $cmd = new ReserveStockCommand(
            orderId: $event->orderId,
            at: $event->at,
        );

        ($this->reserveStock)($cmd);
    }
}
```

---

### 4. Правило зависимостей — формально

```
Domain:
  зависит от: PHP stdlib
  НЕ зависит от: Symfony, Doctrine, Application, Infrastructure

Application:
  зависит от: Domain (интерфейсы, объекты)
  НЕ зависит от: Symfony, Doctrine, Infrastructure

Infrastructure:
  зависит от: Domain, Application, Symfony, Doctrine
  (реализует порты, адаптирует внешний мир)
```

**Стрелки `use`:**

```
Infrastructure → Application → Domain → (stdlib)
     ↘              ↘          ↗
      → (Symfony)   → (Doctrine)
```

Никогда не наоборот.

---

### 5. Конкретные примеры: куда что попадает

| Компонент | Слой | Почему |
|-----------|------|--------|
| `OrderController` (Symfony) | Infrastructure/Http | Входной адаптер: HTTP → use-case |
| `OrderEntity` (Doctrine) | Infrastructure/Persistence | Маппинг БД ↔ Domain |
| `DoctrineOrderRepository` | Infrastructure/Persistence | Реализация порта |
| `PlaceOrder` (use-case) | Application | Оркестрация, не бизнес-логика |
| `Order` (агрегат) | Domain | Инварианты, правила |
| `OrderPlaced` (событие) | Domain | Факт в домене |
| `ReserveStockHandler` (Messenger) | Infrastructure/Message | Адаптер: событие → use-case |
| `MailgunMailer` | Infrastructure/Notification | Выходной адаптер |
| `PlaceOrderCommand` (DTO) | Application | Входные данные use-case |
| `OrderRepository` (интерфейс) | Domain/Port | Контракт, который Domain требует |
| `EventPublisher` (интерфейс) | Domain/Port | Контракт |
| `services.yaml` (DI) | Config | Композиция (вне слоёв) |

---

### 6. Как следить, что границы не нарушаются

#### a) PHPStan / Psalm (static analysis)

```yaml
# phpstan.neon
parameters:
    level: 8
    paths:
        - src

    ignoreErrors:
        # ...
```

```php
// src/Domain/Order/Order.php
// Если здесь появится:
use Doctrine\ORM\EntityManagerInterface;
// → PHPStan НЕ покажет ошибку (это валидный PHP).
// Но:
// 1. Code review: «зачем здесь Doctrine?»
// 2. Архитектурный тест (ниже)
```

#### b) Архитектурные тесты (ключевой инструмент)

```php
// tests/Architecture/DomainLayerTest.php
namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class DomainLayerTest extends TestCase
{
    public function test_domain_does_not_use_symfony(): void
    {
        $this->assertNoImports(
            directory: __DIR__ . '/../../src/Domain',
            prefixes: [
                'Symfony\\',
                'Doctrine\\',
                'App\\Infrastructure\\',
                'App\\Application\\',
            ],
        );
    }

    public function test_domain_does_not_use_infrastructure(): void
    {
        $this->assertNoImports(
            directory: __DIR__ . '/../../src/Domain',
            prefixes: [
                'App\\Infrastructure\\',
                'App\\Application\\',
            ],
        );
    }

    public function test_application_does_not_use_infrastructure(): void
    {
        $this->assertNoImports(
            directory: __DIR__ . '/../../src/Application',
            prefixes: [
                'App\\Infrastructure\\',
                'Symfony\\',
                'Doctrine\\',
            ],
        );
    }

    private function assertNoImports(string $directory, array $prefixes): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            $tokens = token_get_all($content);

            foreach ($tokens as $token) {
                if (is_array($token) && $token[0] === T_USE) {
                    // Пропускаем use до следующего ;
                    $stmt = '';
                    for ($i = array_search($token, $tokens); $i < count($tokens); $i++) {
                        $t = $tokens[$i];
                        $stmt .= is_array($t) ? $t[1] : $t;
                        if ($t === ';') break;
                    }

                    foreach ($prefixes as $prefix) {
                        $this->assertStringNotContainsString(
                            $prefix,
                            $stmt,
                            sprintf(
                                'File %s imports "%s" — violates layer boundary',
                                $file->getPathname(),
                                $prefix,
                            )
                        );
                    }
                }
            }
        }
    }
}
```

**Запуск:** `phpunit tests/Architecture/` — в CI. Если кто-то добавит `use Doctrine\...` в Domain — тест упадёт.

#### c) Infection / Mutation Testing (дополнительно)

Проверяет, что тесты действительно покрывают логику. Не про границы, но про качество.

#### d) Code Review Checklist

```
□ Нет ли в Domain: use Symfony\*, use Doctrine\*, use App\Infrastructure\*
□ Нет ли в Application: use Symfony\Component\HttpFoundation\*, use Doctrine\ORM\*
□ Controller тонкий: < 20 строк, нет бизнес-логики
□ Use-case не знает про HTTP (Request/Response)
□ Repository реализует интерфейс из Domain, а не наоборот
□ События — из Domain, обработчики — из Infrastructure
```

#### e) Figma / C4-диаграмма

```
┌─────────────────────────────────────────────────────────────────┐
│  L3: Infrastructure                                             │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────────────┐  │
│  │  Http       │  │  Persistence │  │  Notification         │  │
│  │  Controller │  │  Doctrine    │  │  Mailgun, SMS         │  │
│  └──────┬──────┘  └──────┬───────┘  └───────────┬───────────┘  │
│         │                │                       │              │
│  ┌──────▼────────────────▼───────────────────────▼───────────┐  │
│  │  L2: Application                                           │  │
│  │  PlaceOrder, ConfirmOrder, ReserveStock                    │  │
│  └──────────────────────────┬────────────────────────────────┘  │
│                             │                                    │
│  ┌──────────────────────────▼────────────────────────────────┐  │
│  │  L1: Domain                                                │  │
│  │  Order, OrderLine, Money, OrderPlaced, OrderRepository (I)│  │
│  └────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

### 7. Частые нарушения и как их ловить

| Нарушение | Как ловить |
|-----------|-----------|
| `use Symfony\...` в Domain | Архитектурный тест |
| `EntityManagerInterface` в Application | Архитектурный тест |
| Business-логика в Controller | Code review + тест: Controller < 20 строк |
| `use App\Infrastructure\...` в Application | Архитектурный тест |
| Доменное событие в Infrastructure (не в Domain) | Code review + структура каталогов |
| Use-case зависит от конкретного адаптера | PHPStan: проверьте, что use-case зависит от интерфейсов |
| «God Service» в Application | Code review: < 3 портов, < 50 строк |

---

### 8. Итого

```
Domain      = правила + инварианты + события. Ноль зависимостей.
Application = оркестрация use-case'ов. Зависит от Domain-портов.
Infrastructure = адаптеры (HTTP, БД, очередь, email). Реализуют порты.

Границы защищаются:
  1. Архитектурными тестами (CI)
  2. Code review
  3. Структурой каталогов (физическое разделение)
  4. DI-конфигурацией (реализации подставляются из Config, не из кода)
```

**Главный принцип:** если вы можете удалить `composer require symfony/framework-bundle` и `composer require doctrine/orm`, и Domain + Application **компилируются** — границы соблюдены.

> ⏱ 42.1s · prompt 64225 tok · output 5803 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о стратегии тестирования для Symfony + PostgreSQL. Нужно дать конкретный, практичный ответ: пирамида, типы тестов, что мокаем/не мокаем, как держать быстрыми и стабильными.

Структура ответа:
1. Пирамида — конкретная для Symfony
2. Типы тестов (unit, integration, e2e) — что в каждом
3. Что мокаем, что нет — конкретные критерии
4. Скорость и стабильность — практические приёмы
5. Антипаттерны

Буду конкретен, с кодом, с числами (время, покрытие). Без воды.

</details>

**Кандидат:**

## Стратегия тестирования Symfony + PostgreSQL

### 1. Пирамида (конкретная)

```
            ┌─────────────┐
            │   E2E (API) │  ~5%  (10–30 тестов)
            │   5–20с/тест│
            └──────┬──────┘
           ┌───────┴───────┐
           │  Integration  │  ~25%  (100–300 тестов)
           │   0.5–2с/тест │
           └───────┬───────┘
         ┌─────────┴─────────┐
         │    Unit (Domain)  │  ~70%  (500–2000 тестов)
         │    < 50мс/тест    │
         └───────────────────┘
```

**Целевое время всего набора:** < 60 секунд (unit) + < 3 минут (integration) + < 5 минут (e2e).

---

### 2. Что тестируем на каждом уровне

#### Unit (Domain) — 70%

```php
// tests/Domain/OrderTest.php
namespace Tests\Domain;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\CustomerId;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\Exception\OrderClosed;
use App\Domain\Common\Money;
use App\Common\Sku;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    private function makeOrder(): Order
    {
        return new Order(
            id: new OrderId('test-order'),
            customerId: new CustomerId('cust-1'),
        );
    }

    public function test_add_line_increments_total(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new Sku('ABC'), 2, new Money(100, 'EUR'));

        self::assertSame(200, $order->total()->amountCents());
    }

    public function test_cannot_add_line_to_confirmed_order(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new Sku('ABC'), 1, new Money(100, 'EUR'));
        $order->confirm();

        $this->expectException(OrderClosed::class);
        $order->addLine(new Sku('DEF'), 1, new Money(50, 'EUR'));
    }

    public function test_cannot_confirm_empty_order(): void
    {
        $order = $this->makeOrder();

        $this->expectException(\DomainException::class);
        $order->confirm();
    }

    public function test_confirm_releases_event(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new Sku('ABC'), 1, new Money(100, 'EUR'));
        $order->confirm();

        $events = $order->releaseEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(OrderPlaced::class, $events[0]);
    }

    public function test_duplicate_sku_rejected(): void
    {
        $order = $this->makeOrder();
        $order->addLine(new Sku('ABC'), 1, new Money(100, 'EUR'));

        $this->expectException(DuplicateLine::class);
        $order->addLine(new Sku('ABC'), 2, new Money(50, 'EUR'));
    }
}
```

**Характеристики:**
- **Ноль mock'ов.** Только Domain-объекты.
- **Ноль БД.** Ниль Symfony. Ноль DI.
- **Скорость:** < 10мс на тест. 2000 тестов = < 20с.
- **Стабильность:** 100%. Нет внешних зависимостей → нет flaky.

**Что НЕ тестируем на этом уровне:**
- Doctrine (mapping, flush)
- Symfony (routing, DI, validation)
- HTTP (request/response)
- Очередь (delivery)

---

#### Integration (Application + Infrastructure) — 25%

```php
// tests/Application/PlaceOrderTest.php
namespace Tests\Application;

use App\Application\Order\PlaceOrder;
use App\Application\Order\PlaceOrderCommand;
use App\Domain\Order\Port\OrderRepository;
use App\Domain\Order\Port\EventPublisher;
use App\Domain\Order\Port\Clock;
use PHPUnit\Framework\TestCase;

final class PlaceOrderTest extends TestCase
{
    public function test_place_order_persists_and_publishes(): void
    {
        // Fake (не mock) — реализация интерфейса из Domain
        $repo = new InMemoryOrderRepository();
        $events = new InMemoryEventPublisher();
        $clock = new SystemClock();

        $useCase = new PlaceOrder($repo, $events, $clock);

        $cmd = new PlaceOrderCommand(
            orderId: 'order-1',
            customerId: 'cust-1',
            items: [
                ['sku' => 'ABC', 'quantity' => 2, 'priceCents' => 100, 'currency' => 'EUR'],
            ],
        );

        $order = $useCase($cmd);

        // Проверка: сохранено
        $saved = $repo->findById(new OrderId('order-1'));
        self::assertSame(OrderStatus::Confirmed, $saved->status());
        self::assertSame(200, $saved->total()->amountCents());

        // Проверка: событие опубликовано
        self::assertCount(1, $events->published());
        self::assertInstanceOf(OrderPlaced::class, $events->published()[0]);
    }

    public function test_place_order_fails_if_duplicate_sku(): void
    {
        $repo = new InMemoryOrderRepository();
        $events = new InMemoryEventPublisher();
        $clock = new SystemClock();

        $useCase = new PlaceOrder($repo, $events, $clock);

        $cmd = new PlaceOrderCommand(
            orderId: 'order-1',
            customerId: 'cust-1',
            items: [
                ['sku' => 'ABC', 'quantity' => 1, 'priceCents' => 100, 'currency' => 'EUR'],
                ['sku' => 'ABC', 'quantity' => 2, 'priceCents' => 50, 'currency' => 'EUR'],
            ],
        );

        $this->expectException(DuplicateLine::class);
        $useCase($cmd);
    }
}
```

**Fake-репозиторий** (не mock):

```php
// tests/Fakes/InMemoryOrderRepository.php
namespace Tests\Fakes;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\Port\OrderRepository;

final class InMemoryOrderRepository implements OrderRepository
{
    /** @var array<string, Order> */
    private array $orders = [];

    public function findById(OrderId $id): Order
    {
        return $this->orders[$id->value()]
            ?? throw new \App\Domain\Order\Exception\OrderNotFound($id->value());
    }

    public function save(Order $order): void
    {
        $this->orders[$order->id()->value()] = $order;
    }

    public function reset(): void
    {
        $this->orders = [];
    }
}
```

**Характеристики:**
- **Fake'и вместо mock'ов.** Fake — рабочая реализация интерфейса (InMemory). Mock — записанный ответ.
- **Ноль БД** (в идеале). Если БД нужна — см. ниже.
- **Скорость:** < 100мс на тест.
- **Стабильность:** 99%.

**Когда нужна реальная БД:**
- Тест маппинга Entity ↔ Domain (repository)
- Тест ограничений БД (UNIQUE, CHECK, FK)
- Тест транзакций (rollback, concurrent)

```php
// tests/Infrastructure/DoctrineOrderRepositoryTest.php
namespace Tests\Infrastructure;

use App\Infrastructure\Persistence\Order\DoctrineOrderRepository;
use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Common\Money;
use App\Domain\Common\Sku;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class DoctrineOrderRepositoryTest extends TestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        // Testcontainers: реальная PostgreSQL
        $this->em = createTestEntityManager(); // см. ниже
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->executeStatement('TRUNCATE orders, order_lines CASCADE');
    }

    public function test_save_and_retrieve_order(): void
    {
        $repo = new DoctrineOrderRepository($this->em);

        $order = new Order(new OrderId('test-1'), new CustomerId('cust-1'));
        $order->addLine(new Sku('ABC'), 2, new Money(100, 'EUR'));
        $order->confirm();

        $repo->save($order);
        $this->em->clear();  // ← сбрасываем identity map

        $loaded = $repo->findById(new OrderId('test-1'));

        self::assertSame(2, count($loaded->lines()));
        self::assertSame(200, $loaded->total()->amountCents());
        self::assertSame(OrderStatus::Confirmed, $loaded->status());
    }

    public function test_save_persists_lines_atomically(): void
    {
        $repo = new DoctrineOrderRepository($this->em);

        $order = new Order(new OrderId('test-2'), new CustomerId('cust-1'));
        $order->addLine(new Sku('ABC'), 1, new Money(100, 'EUR'));
        $order->addLine(new Sku('DEF'), 2, new Money(50, 'EUR'));

        $repo->save($order);
        $this->em->clear();

        $loaded = $repo->findById(new OrderId('test-2'));
        self::assertCount(2, $loaded->lines());
    }
}
```

---

#### E2E (API) — 5%

```php
// tests/Api/OrderApiTest.php
namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderApiTest extends KernelTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->client = static::createClient();

        // Очистка БД
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->getConnection()->executeStatement('TRUNCATE orders, order_lines, customers CASCADE');
    }

    public function test_create_order_returns_201(): void
    {
        $payload = [
            'id' => 'order-e2e-1',
            'customer_id' => 'cust-1',
            'items' => [
                ['sku' => 'ABC', 'qty' => 1, 'price_cents' => 100, 'currency' => 'EUR'],
            ],
        ];

        $this->client->request('POST', '/api/orders', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        self::assertResponseStatusCodeSame(201);
        $body = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('order-e2e-1', $body['id']);
        self::assertSame('confirmed', $body['status']);
    }

    public function test_create_order_with_invalid_sku_returns_422(): void
    {
        $payload = [
            'id' => 'order-e2e-2',
            'customer_id' => 'cust-1',
            'items' => [
                ['sku' => '', 'qty' => 1, 'price_cents' => 100, 'currency' => 'EUR'],
            ],
        ];

        $this->client->request('POST', '/api/orders', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        self::assertResponseStatusCodeSame(422);
    }
}
```

**Характеристики:**
- **Реальная БД** (PostgreSQL через Testcontainers или Docker).
- **Ноль mock'ов.**
- **Скорость:** 1–3с на тест (boot kernel + HTTP + БД).
- **Количество:** 10–30. Только критические сценарии.

---

### 3. Что мокаем, что нет

| Компонент | Мокаем? | Почему |
|-----------|---------|--------|
| Domain-объекты | **НЕТ** | Тестируем их сами |
| `OrderRepository` (в unit) | **ФЕЙК** (InMemory) | Реализация интерфейса из Domain |
| `EventPublisher` (в unit) | **ФЕЙК** (InMemory) | Реализация интерфейса |
| `Clock` | **ФЕЙК** (FixedClock) | Детерминизм |
| `EntityManagerInterface` | **НЕТ** (в integration) | Используем реальную БД |
| `MailerInterface` | **НЕТ** (в E2E) — **ФЕЙК** (в unit) | В E2E — проверяем, что email был (Outbox). В unit — fake |
| `PaymentGateway` (Stripe) | **Всегда фейк/mock** | Внешний сервис, никогда в тестах |
| `Elasticsearch` | **НЕТ** (в E2E) — **ФЕЙК** (в unit) | В E2E — реальный (если критично). В unit — fake |
| `MessageBusInterface` | **ФЕЙК** (в unit) — **реальный** (в E2E) | В unit — fake (проверяем, что dispatch'нули). В E2E — реальный (если тестим доставку) |
| `Request` / `Response` | **НЕТ** (в E2E) — **не используем** (в unit) | В unit — нет HTTP. В E2E — реальный |

**Правило:**

```
Мокаем: внешние сервисы (Stripe, Mailgun, Elasticsearch) — НИКОГДА в реальных тестах.
Фейки: интерфейсы из Domain (Repository, Publisher, Clock) — в unit/integration.
Реальные: БД, Symfony kernel, Messenger — в integration/E2E.
Никогда: Domain-объекты.
```

**Fake vs Mock:**

| | Fake | Mock |
|--|------|------|
| Что | Рабочая реализация | Записанный ответ |
| Пример | `InMemoryOrderRepository` | `$repo->expects()->save()` |
| Когда | Unit/Integration | Редко. Только для проверки «был ли вызов» |
| Стабильность | Высокая | Средняя (хрупкий) |

**Я尽量避免 mock'ов.** Fake даёт то же, что mock (изоляция), но без хрупкости.

---

### 4. Как держать тесты быстрыми

#### a) Параллелизм

```bash
# PHPUnit (v10+) — параллельные процессы
vendor/bin/phpunit --processes=4

# Для integration (БД) — отдельные схемы:
vendor/bin/phpunit --processes=4 tests/Infrastructure/
# Каждый процесс → отдельная БД-схема (pg_create_role)
```

```php
// config/packages/test/doctrine.yaml
doctrine:
    dbal:
        # Каждый параллельный процесс получает свою схему
        schema: '%env(test_SCHEMA)%'
```

**Эффект:** 300 integration-тестов × 1с = 300с serial → **75с** с 4 процессами.

#### b) Testcontainers (реальная БД без Docker-overhead)

```php
// tests/Infrastructure/createTestEntityManager.php
namespace Tests\Infrastructure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManagerDecorator;
use Doctrine\DBAL\DriverManager;
use Testcontainers\Postgres\PostgresContainer;

function createTestEntityManager(): EntityManager
{
    static $container = null;
    static $em = null;

    if ($em === null) {
        $container = PostgresContainer::newInstance()
            ->withDatabase('test')
            ->withUsername('test')
            ->withPassword('test')
            ->start();

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => '127.0.0.1',
            'port' => (int) $container->getMappedPort(5432),
            'database' => 'test',
            'user' => 'test',
            'password' => 'test',
        ]);

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../../src/Infrastructure/Persistence'],
            isDevMode: true,
        );

        $em = EntityManager::create($conn, $config);

        // Миграции
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);
    }

    return $em;
}
```

**Альтернатива:** Docker Compose с фиксированным портом.

```yaml
# docker-compose.test.yml
services:
    postgres:
        image: postgres:16
        environment:
            POSTGRES_DB: test
            POSTGRES_USER: test
            POSTGRES_PASSWORD: test
        ports:
            - "5432:5432"
```

```bash
# CI:
docker compose -f docker-compose.test.yml up -d postgres
vendor/bin/phpunit
docker compose -f docker-compose.test.yml down
```

#### c) TRUNCATE vs DELETE

```php
// tearDown — TRUNCATE (быстрее, сбрасывает sequences)
$em->getConnection()->executeStatement(
    'TRUNCATE orders, order_lines, customers CASCADE'
);

// НЕ:
$em->createQuery('DELETE FROM OrderEntity o')->execute();  // ← медленнее
```

#### d) Не boot'им kernel в unit-тестах

```php
// tests/Domain/OrderTest.php
// НЕТ: self::bootKernel();
// НЕТ: static::getContainer();
// Только: new Order(...), new Money(...)
```

**Эффект:** 2000 unit-тестов × 5мс = 10с. Без kernel = 0 overhead.

#### e) Бюджет времени в CI

```yaml
# .github/workflows/test.yml
- name: Run tests
  run: |
    # Unit: < 30с
    timeout 60 vendor/bin/phpunit tests/Domain/

    # Integration: < 3мин
    timeout 180 vendor/bin/phpunit tests/Application/ tests/Infrastructure/

    # E2E: < 5мин
    timeout 300 vendor/bin/phpunit tests/Api/
```

Если тест превышает бюджет — алерт.

---

### 5. Как держать тесты стабильными (anti-flaky)

#### a) Детерминизм времени

```php
// tests/Fakes/FixedClock.php
namespace Tests\Fakes;

use App\Domain\Order\Port\Clock;

final class FixedClock implements Clock
{
    private \DateTimeImmutable $now;

    public function __construct(?string $datetime = null)
    {
        $this->now = new \DateTimeImmutable($datetime ?? '2025-01-01 12:00:00');
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}

// В тесте:
$clock = new FixedClock('2025-06-15 10:30:00');
$useCase = new PlaceOrder($repo, $events, $clock);
// → детерминированное время, не зависящее от wall clock
```

#### b) Детерминизм id

```php
// НЕ: uniqid(), uuid4()
// А: фиксированный id в тесте
$order = new Order(new OrderId('test-order-1'), ...);
```

#### c) Нет зависимости от порядка

```php
// ПЛОХО:
public function test_b() { $this->assertDatabaseCount('orders', 2); }
// ← зависит от того, что test_a() уже вставил 1 строку

// ХОРОШО:
public function test_b() {
    $this->setupOrder();  // ← сам создаёт, что нужно
    $this->assertDatabaseCount('orders', 2);
}
```

#### d) Нет зависимости от окружения

```php
// ПЛОХО:
public function test_x() {
    $this->client->request('POST', '/api/orders', [...]);
    // ← зависит от того, что в БД до этого
}

// ХОРОШО:
protected function setUp(): void
{
    parent::setUp();
    $this->truncateAll();  // ← каждый тест стартует с чистого состояния
}
```

#### e) Retry для интеграционных (осторожно!)

```php
// phpunit.xml
<phpunit>
    <!-- Только для integration, НЕ для unit -->
    <testsuites>
        <testsuite name="integration">
            <directory>tests/Infrastructure</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

```bash
# CI: retry 1 раз для integration (если fallback)
vendor/bin/phpunit tests/Infrastructure/ || vendor/bin/phpunit tests/Infrastructure/
```

**Никогда** не retry'им unit-тесты. Если unit-тест flaky — он **сломан**, а не «неудачный».

#### f) Timeout на HTTP

```php
// tests/Api/OrderApiTest.php
$this->client->setServerParameters([
    'HTTP_X_REQUEST_TIMEOUT' => '5000',  // 5с
]);
```

---

### 6. Покрытие и метрики

```bash
# Покрытие (unit + integration):
vendor/bin/phpunit --coverage-text

# Цель:
#   Domain:    > 95% (почти всё)
#   Application: > 85%
#   Infrastructure: > 60% (mapping, repository)
#   Controller: > 70% (тонкие, но есть логика)
```

**Не гонитесь за 100%.** Гонитесь за тем, что **каждый инвариант** имеет тест.

```php
// Чеклист: каждый публичный метод Domain-класса имеет ≥ 1 тест
// Каждый thrown exception имеет ≥ 1 тест
// Каждый переход состояния имеет ≥ 1 тест
```

---

### 7. Антипаттерны

| Антипаттерн | Почему плохо |
|-------------|-------------|
| Mock'ить Domain-объекты | Теряете смысл: Domain — то, что тестируете |
| Тестировать через HTTP, когда можно через use-case | Медленно, хрупко, не показывает, **что** сломалось |
| `sleep(2)` в integration | Нondetерминизм, медленно |
| Тест, который проверяет 10 сценариев в одном методе | Один падает — непонятно, какой |
| Mock'ить `EntityManager` | Не тестируете mapping. Фейк репозитория + реальная БД |
| Тест E2E для каждой граничной валидации | Дорого. Валидация — unit/integration |
| Покрытие 100% как цель | Метрика, а не цель. Цель — инварианты покрыты |
| Тесты, которые зависят от порядка | Перестановка = падение. Flaky |
| `skip` на flaky-тесте | Хороним проблему. Фиксим или удаляем |

---

### 8. Итого — стратегия

```
Unit (Domain)
  ├── Ноль mock'ов, ноль БД
  ├── Фейки для портов (InMemory, FixedClock)
  ├── < 50мс/тест
  ├── 70% всех тестов
  └── Покрытие инвариантов > 95%

Integration (Application + Infrastructure)
  ├── Фейки для внешних (Stripe, Mailgun)
  ├── Реальная БД для repository-тестов
  ├── 0.5–2с/тест
  ├── 25% всех тестов
  └── Покрытие use-case'ов > 85%

E2E (API)
  ├── Реальный Symfony kernel + БД
  ├── Ноль mock'ов
  ├── 1–3с/тест
  ├── 5% всех тестов
  └── Только критические сценарии (happy path + 3–5 error paths)

Скорость: < 10 минут весь набор (CI)
Стабильность: > 99.5% (retry только для integration, 1 раз)
```

> ⏱ 46.4s · prompt 69875 tok · output 6059 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о concurrent modification одной записи БД. Это пересекается с предыдущим вопросом про optimistic/pessimistic locking, но теперь фокус шире — «проблемы» и «все способы защиты», а не только DDD-агрегаты.

Нужно:
1. Перечислить конкретные проблемы (lost update, dirty read, phantom read, deadlock, starvation).
2. Перечислить все способы защиты (optimistic, pessimistic, single-writer, idempotency, advisory locks, MVCC-уровни изоляции, application-level).
3. Сравнить по критериям.
4. Дать практический выбор по сценариям.

Структурирую ответ так, чтобы он был самодостаточным и не повторял дословно предыдущий ответ, а расширял его — здесь фокус на уровне БД/транзакций, а не только DDD.

</details>

**Кандидат:**

## Конкурентное изменение одной записи

### 1. Проблемы

#### a) Lost Update (самая частая)

```
T0:  balance = 1000 (в БД)

T1:  User A: SELECT balance → 1000
T2:  User B: SELECT balance → 1000

T3:  User A: UPDATE balance = 1000 - 200 = 800  → COMMIT
T4:  User B: UPDATE balance = 1000 - 300 = 700  → COMMIT

Итог: balance = 700
Ожидание: 1000 - 200 - 300 = 500
Потеря: 200. Без ошибки.
```

#### b) Dirty Read

```
T1:  User A: BEGIN
T2:  User A: UPDATE orders SET status = 'cancelled' WHERE id = 1
T3:  User B: SELECT status FROM orders WHERE id = 1  → 'cancelled' (незакоммиченный!)
T4:  User A: ROLLBACK

Итог: User B видел данные, которых «не было».
```

#### c) Non-repeatable Read

```
T1:  User A: BEGIN
T2:  User A: SELECT count(*) FROM orders WHERE customer_id = 'X' → 5
T3:  User B: INSERT INTO orders (customer_id = 'X') → COMMIT
T4:  User A: SELECT count(*) FROM orders WHERE customer_id = 'X' → 6
T5:  User A: COMMIT (решение основано на «5», а не на «6»)
```

#### d) Phantom Read

```
T1:  User A: BEGIN
T2:  User A: SELECT * FROM orders WHERE status = 'pending' → 10 строк
T3:  User B: INSERT INTO orders (status = 'pending') → COMMIT
T4:  User A: UPDATE orders SET status = 'paid' WHERE status = 'pending'
     → затронуло 11 строк (включая «призрака»)
```

#### e) Deadlock

```
T1:  User A: SELECT FOR UPDATE order 1  → OK
T2:  User B: SELECT FOR UPDATE order 2  → OK

T3:  User A: SELECT FOR UPDATE order 2  → ЖДЁТ (User B держит)
T4:  User B: SELECT FOR UPDATE order 1  → ЖДЁТ (User A держит)

→ DEADLOCK. PostgreSQL убивает одну из транзакций (40P01).
```

#### f) Starvation

```
100 пользователей одновременно обновляют одну запись.
User 1: lock → write → unlock (быстро)
User 2: lock → write → unlock (быстро)
...
User 100: lock → ЖДЁТ → lock → write → unlock
→ User 100 может ждать очень долго.
```

---

### 2. Способы защиты

#### 2.1 Уровни изоляции PostgreSQL

| Уровень | Dirty Read | Non-repeatable | Phantom | Cost |
|---------|-----------|----------------|---------|------|
| READ UNCOMMITTED | ✅ | ✅ | ✅ | Минимум |
| READ COMMITTED (default) | ❌ | ✅ | ✅ | Низкий |
| REPEATABLE READ | ❌ | ❌ | ✅ | Средний |
| SERIALIZABLE | ❌ | ❌ | ❌ | Высокий |

```sql
-- PostgreSQL: READ COMMITTED (default)
-- Dirty read невозможен.
-- Non-repeatable read возможен (сделали SELECT → кто-то COMMIT → повторный SELECT другой).
-- Phantom: частично защищён (snapshot isolation), но не полностью.

-- REPEATABLE READ:
BEGIN ISOLATION LEVEL REPEATABLE READ;
SELECT ...;  -- snapshot зафиксирован
SELECT ...;  -- тот же snapshot, даже если другие COMMIT'нули
COMMIT;

-- SERIALIZABLE:
BEGIN ISOLATION LEVEL SERIALIZABLE;
SELECT ...;
UPDATE ...;
COMMIT;  -- если конфликт → 40001 (serialization_failure) → retry
```

**Когда какой:**

```
READ COMMITTED (default)
  → 95% сценариев. Достаточно, если есть optimistic locking.

REPEATABLE READ
  → Финансовые отчёты: «цифры в отчёте не должны меняться во время расчёта».
  → Билетирование: «осталось 5 мест» → snapshot.

SERIALIZABLE
  → Критичные операции: «два перевода не могут создать/списать несуществующие деньги».
  → Rare: дорого, retry loop.
```

---

#### 2.2 Optimistic Locking (version column)

```sql
ALTER TABLE accounts ADD COLUMN version INT NOT NULL DEFAULT 0;

UPDATE accounts
SET balance_cents = 800, version = 1
WHERE id = 'acc-1' AND version = 0;

-- 0 rows affected → конфликт → retry/abort
```

```sql
-- Атомарная операция «списать» без version (ещё лучше):
UPDATE accounts
SET balance_cents = balance_cents - 200
WHERE id = 'acc-1' AND balance_cents >= 200;

-- 0 rows → недостаточно средств → нет lost update
```

| Плюс | Минус |
|------|-------|
| Нет блокировки | Retry loop при высоком конфликте |
| Простота | Не защищает от dirty read (но PostgreSQL READ COMMITTED и так защищает) |
| Масштабируется | Нужно обрабатывать 0 rows |

---

#### 2.3 Pessimistic Locking (SELECT FOR UPDATE)

```sql
BEGIN;
SELECT * FROM accounts WHERE id = 'acc-1' FOR UPDATE;
-- ← строка заблокирована до COMMIT/ROLLBACK

UPDATE accounts SET balance_cents = 800 WHERE id = 'acc-1';
COMMIT;  -- ← lock снят
```

| Плюс | Минус |
|------|-------|
| Конфликт = ожидание (не потеря) | Deadlock при разном порядке |
| Простота | Транзакция должна быть КОРОТКОЙ |
| Детерминирован | Starvation при высоком конфликте |

---

#### 2.4 Advisory Locks (PostgreSQL-specific)

```sql
-- Sesson-level:
SELECT pg_advisory_lock(hashtext('order:123'));
-- ... работа ...
SELECT pg_advisory_unlock(hashtext('order:123'));

-- Transaction-level (автоматически снимается при COMMIT):
SELECT pg_advisory_xact_lock(hashtext('order:123'));
```

| Плюс | Минус |
|------|-------|
| Не блокирует строку (внешний lock) | Нужно помнить, кто lock'нул и кто unlock'нул |
| Можно использовать «между» транзакциями | Если процесс упал — session lock живёт до disconnect |
| Лёгче, чем FOR UPDATE (не держит строку) | Нет автоматического timeout (session level) |

**Когда:** координирование между процессами, где не подходит FOR UPDATE (например, lock на «задачу», а не на строку).

---

#### 2.5 Single-Writer (очередь)

```
Command → Queue (partition by orderId) → Worker (1 на partition)
```

| Плюс | Минус |
|------|-------|
| Конфликт невозможен по построению | Задержка (очередь) |
| Нет deadlock | Throughput ограничен |
| Аудит (история команд) | Сложность инфраструктуры |

---

#### 2.6 Idempotency Key

```sql
CREATE TABLE idempotency_keys (
    key VARCHAR(255) PRIMARY KEY,
    response JSONB,
    created_at TIMESTAMPTZ DEFAULT now(),
    expires_at TIMESTAMPTZ NOT NULL
);
```

```php
// Перед обработкой:
$existing = $this->idempotency->find($key);
if ($existing !== null) {
    return $existing; // ← повтор → тот же ответ
}

// После обработки:
$this->idempotency->save($key, $response);
```

| Плюс | Минус |
|------|-------|
| Защита от повторов (retry, double-click) | Не защищает от «разных» concurrent writes |
| Идемпотентность API | Нужно генерировать key |

---

#### 2.7 Atomic Operations (SQL-уровень)

Вместо «read → modify → write» — один атомарный SQL:

```sql
-- ПЛОХО (3 шага, race condition):
SELECT balance FROM accounts WHERE id = 'x';  -- 1000
-- ... calculation ...
UPDATE accounts SET balance = 800 WHERE id = 'x';  -- lost update!

-- ХОРОШО (1 атомарная операция):
UPDATE accounts
SET balance_cents = balance_cents - 200
WHERE id = 'x' AND balance_cents >= 200;
-- → атомарно. Если balance < 200 → 0 rows → ошибка.
```

```sql
-- INCREMENT с атомарной проверкой:
UPDATE accounts
SET balance_cents = balance_cents + 500
WHERE id = 'x';
-- → нет lost update. Даже 1000 concurrent UPDATE → все применятся.
```

| Плюс | Минус |
|------|-------|
| Нет lost update (по построению) | Не подходит для сложных вычислений |
| Нет retry | Не подходит для «read → validate → write» |
| Быстро | |

---

#### 2.8 MVCC Snapshot (PostgreSQL)

PostgreSQL использует MVCC: каждый reader видит **snapshot** на момент BEGIN.

```sql
-- User A:
BEGIN;
SELECT balance FROM accounts WHERE id = 'x';  -- 1000
-- ... (5 секунд работы) ...
UPDATE accounts SET balance = 800 WHERE id = 'x';
COMMIT;

-- User B (начал транзакцию ДО User A COMMIT):
SELECT balance FROM accounts WHERE id = 'x';  -- 1000 (snapshot!)
-- → User B НЕ видит изменение User A (READ COMMITTED)
-- → Но если User B UPDATE → конфлик (READ COMMITTED: UPDATE видит latest)
```

**Важно:** в PostgreSQL `READ COMMITTED` + `UPDATE` = **серийный** (каждый UPDATE видит latest). Это не SERIALIZABLE, но и не «старые данные».

```sql
-- READ COMMITTED:
-- SELECT видит snapshot
-- UPDATE видит LATEST (не snapshot!)
-- → Если баланс изменился между SELECT и UPDATE → UPDATE может затереть!

-- Решение: UPDATE WHERE balance >= 200 (атомарная проверка)
-- Или: FOR UPDATE (блокирует между SELECT и UPDATE)
```

---

### 3. Сравнение

| Критерий | Optimistic | Pessimistic | Single-Writer | Atomic SQL | Idempotency | Advisory Lock |
|----------|-----------|-------------|---------------|------------|-------------|---------------|
| Lost update | ❌ (retry) | ❌ (lock) | ❌ (serialize) | ❌ (atomic) | ❌ (dedup) | ❌ (lock) |
| Deadlock | ❌ | ⚠️ | ❌ | ❌ | ❌ | ⚠️ |
| Starvation | ⚠️ (retry) | ⚠️ (wait) | ⚠️ (queue) | ❌ | ❌ | ⚠️ |
| Latency | +1 row | Lock wait | Queue delay | 0 | +1 lookup | Lock wait |
| Throughput | Высокий | Ограничен | Ограничен | Высокий | Высокий | Ограничен |
| Сложность | Низкая | Низкая | Средняя | Минимальная | Низкая | Средняя |
| Аудит | Нет | Нет | Очередь | Нет | Да (key) | Нет |
| Повторы | Нет защиты | Нет защиты | Нет защиты | Нет защиты | ✅ | Нет защиты |

---

### 4. Практический выбор

```
Сценарий 1: «Списать с баланса»
  → Atomic SQL: UPDATE ... SET balance = balance - X WHERE balance >= X
  → Если 0 rows → InsufficientBalance
  → Нет retry, нет lock, нет lost update.

Сценарий 2: «Обновить профиль пользователя»
  → Optimistic (version column)
  → Конфликт редкий (1 раз в неделю)
  → Retry 1–2 раза.

Сценарий 3: «Бронирование места»
  → Atomic SQL: UPDATE seats SET status = 'booked' WHERE id = X AND status = 'free'
  → Если 0 rows → занято
  → 1000 concurrent → все атомарны, нет lost update.

Сценарий 4: «Сложная трансакция: списать + создать заказ + обновить инвентарь»
  → Pessimistic (FOR UPDATE на строках)
  → Короткая транзакция (< 100мс)
  → Или: Single-Writer (очередь)

Сценарий 5: «Повторный webhook от платёжного шлюза»
  → Idempotency Key
  → + Optimistic как safety net

Сценарий 6: «Финансовый отчёт: цифры не должны меняться»
  → REPEATABLE READ
  → + Optimistic для записи

Сценарий 7: «Два перевода между счетами»
  → SERIALIZABLE или Single-Writer
  → Атомарность: либо оба, либо ни одного
```

---

### 5. Комбинирование

На практике часто комбинируют:

```
┌─────────────────────────────────────────────────────────────────┐
│  SLAB 1: Atomic SQL (защита от lost update)                     │
│  UPDATE ... SET balance = balance - X WHERE balance >= X        │
├─────────────────────────────────────────────────────────────────┤
│  SLAB 2: Optimistic Locking (защита от conflict на уровне agg)  │
│  version column + retry                                         │
├─────────────────────────────────────────────────────────────────┤
│  SLAB 3: Idempotency Key (защита от повторов)                   │
│  UNIQUE key на запрос                                           │
├─────────────────────────────────────────────────────────────────┤
│  SLAB 4: SERIALIZABLE / REPEATABLE READ (защита на уровне tx)   │
│  Для критичных операций                                         │
└─────────────────────────────────────────────────────────────────┘
```

**Пример: платёж**

```sql
BEGIN;

-- SLAB 3: Idempotency
INSERT INTO idempotency_keys (key, response) VALUES ('req-123', NULL)
ON CONFLICT (key) DO NOTHING;
-- Если CONFLICT → повтор → вернуть сохранённый ответ

-- SLAB 1: Atomic списание
UPDATE accounts SET balance_cents = balance_cents - 200
WHERE id = 'acc-1' AND balance_cents >= 200;
-- 0 rows → ROLLBACK → InsufficientBalance

-- SLAB 2: Optimistic (заказ)
UPDATE orders SET status = 'paid', version = version + 1
WHERE id = 'ord-1' AND version = 3;
-- 0 rows → ROLLBACK → ConcurrentModification

COMMIT;
```

---

### 6. Чего НЕ делать

| ❌ | Почему |
|----|--------|
| `SELECT ... FOR UPDATE` + внешний HTTP-вызов | Lock держится 3–10с → starvation |
| `sleep(1)` между SELECT и UPDATE | Race condition + slowly |
| Optimistic без retry loop | 0 rows → пользователь видит 500 |
| `pg_advisory_lock` без timeout | Если процесс упал → lock вечный |
| SERIALIZABLE «на всё» | Retry loop, дорого, не нужно для 95% случаев |
| `IF NOT EXISTS` вместо `ON CONFLICT` | Race: два процесса одновременно видят «не существует» |
| Lock на уровне application (Redis) без DB-защиты | Redis down → нет lock → race |

---

### 7. Итого

```
Принцип:

1. Атомарность на уровне SQL — первый выбор.
   Если операцию можно выразить как один UPDATE — делайте это.

2. Если операция многошаговая — optimistic + retry.

3. Если конфликт частый и критичный — pessimistic (короткая транзакция).

4. Если нужна гарантия «одна запись = один обработчик» — single-writer.

5. Всегда: idempotency key на API-границе.

6. Никогда: полагаться только на «а в коде мы проверяем».
   БД — последний рубеж.
```

> ⏱ 36.9s · prompt 75829 tok · output 4178 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

The user is asking about my git process from task to production: branching, commit format, PRs, reviews, releases. And why this approach over alternatives.

I'm continuing the role of a senior PHP backend developer in an interview. I should give a concrete, opinionated process with justification for each choice. Let me structure it as a pipeline: task → branch → commits → PR → review → merge → release → production.

Key decisions to explain:
1. Branching strategy: I'll pick trunk-based with feature flags over gitflow. Why? Because in a team of 5-15 people with CI/CD, gitflow creates merge pain, stale branches, and long-lived branches. Trunk-based + feature flags is more modern. But I'll acknowledge when gitflow is still appropriate.

2. Commit format: Conventional Commits. Why? Automated changelog, traceability, but I'll be honest about the tradeoffs (overhead vs benefit).

3. PR format: What goes in a good PR, why small PRs.

4. Review: What I look for, timebox, why not "rubber stamp".

5. Release: Trunk-based → every merge to main is a release candidate. Feature flags for risky changes. Canary/rolling for production.

Let me be concrete and opinionated, with code examples where useful. I'll avoid being too long - focus on the "why" as requested.

</details>

**Кандидат:**

## Git-процесс: от задачи до продакшена

### 1. Ветвление: trunk-based + feature flags

```
main (always deployable)
 │
 ├── feat/order-refund        ← короткая ветка (1–3 дня)
 ├── fix/price-rounding       ← hotfix (2 часа)
 └── chore/upgrade-doctrine   ← инфраструктура (1 день)
```

**Почему не gitflow:**

| | Gitflow | Trunk-based |
|--|---------|-------------|
| Длительность ветки | Неделли–месяцы | 1–3 дня |
| Merge pain | Высокий (rebase на develop каждый день) | Низкий (main всегда актуален) |
| Deploy | Только с release-ветки | Каждый merge → deploy |
| Feature flags | Нет | Да |
| Team size | > 20 | 3–15 |
| CI/CD | Сложнее (ветки = разные окружения) | Проще (одно окружение) |

**Почему не "каждый merge = deploy" без flags:**

```
Без flag:
  Merge → Deploy → Feature видна всем → если баг → rollback 5 минут

С flag:
  Merge → Deploy → Feature видна только 0% → включить на 10% → 100%
  Если баг → выключить flag (10 секунд, без rollback)
```

**Правила:**

```
✅ Ветка живёт ≤ 3 дня
✅ Ветка ≤ 500 строк (без generated code)
✅ Ветка мержится в main (не в develop)
✅ main всегда зелёный (CI зелёный = deployable)
✅ Риск = flag, а не ветка
```

**Когда gitflow всё-таки уместен:**
- Команда > 20 человек
- Релизный цикл фиксированный (месяц)
- Нет CI/CD
- Клиент требует "release 2.0" и "release 2.1" как артефакты

---

### 2. Формат коммитов: Conventional Commits

```
feat(order): add refund workflow
fix(payment): handle duplicate webhook
refactor(inventory): extract reserve logic
chore: upgrade doctrine to 2.19
docs: update API changelog
test(order): cover edge case with zero-quantity line
```

**Структура:**

```
<type>(<scope>): <description>
   │        │        │
   │        │        └── что сделано (imperative, ≤ 72 символа)
   │        └──────────── какой модуль (необязательно)
   └───────────────────── что изменилось
```

**Типы:**

| Type | Значение | Пример |
|------|----------|--------|
| `feat` | Новая функциональность | `feat(order): add partial refund` |
| `fix` | Исправление бага | `fix(payment): handle 3D-Secure redirect` |
| `refactor` | Перестройка без изменения поведения | `refactor: extract PriceCalculator` |
| `chore` | Инфраструктура, deps, конфиг | `chore: upgrade PHP to 8.3` |
| `docs` | Документация | `docs: add API examples` |
| `test` | Тесты | `test(order): cover empty cart` |
| `perf` | Производительность | `perf: add index on orders(customer_id)` |

**Почему Conventional Commits, а не "fix bug", "update", "wip":**

```
Польза 1: Автоматический changelog
  feat(order): add refund       → "Added refund workflow"
  fix(payment): handle dup     → "Fixed duplicate webhook handling"
  → git-cliff / conventional-changelog → CHANGELOG.md

Польза 2: Traceability
  "Почему изменилось поведение?" → git log --grep="feat(order)"

Польза 3: Semver
  feat → minor bump
  fix  → patch bump
  BREAKING CHANGE → major bump
  → Автоматический bump версии

Польза 4: Ревью
  "refactor: ..." → ревьюер знает, что поведение не меняется → быстрее
  "feat: ..."     → ревьюер ищет новые инварианты → внимательнее
```

**Чего НЕ делаю:**

```
❌ Коммит на каждый save (10 коммитов на 50 строк)
❌ "Fix" → "Fix fix" → "Fix fix 2"
❌ Мегатикет "Update all" на 2000 строк
❌ Коммиты без описания (".", "wip", "tmp")
```

**Правило:** один коммит = одна логическая единица. Если можно откатить один коммит без побочных эффектов — это правильный размер.

---

### 3. Pull Request

#### 3.1 Формат

```markdown
## What
Добавляю частичный возврат по заказу.

## Why
Бизнес-требование: клиент может вернуть часть позиций.
Сейчас можно только полный возврат.

## How
- Новый use-case: RefundOrder (Application)
- Новый метод: Order::refundPartial() (Domain)
- Новое событие: OrderPartiallyRefunded (Domain)
- Новый endpoint: POST /api/orders/{id}/refunds

## Testing
- Unit: 4 новых теста на Order::refundPartial()
- Integration: 2 теста на RefundOrder use-case
- E2E: 1 тест на endpoint
- Manual: проверил сцenario "возврат 1 из 3 позиций"

## Risks
- Если возврат > total → 422 (покрыто тестом)
- Concurrent refund → optimistic lock (покрыт тестом)

## Screenshots / Logs
[по ссылке]
```

#### 3.2 Размер

```
Цель: 300–500 строк (без generated).
Максимум: 1000 строк.
Выше — разбить.

Почему:
- Ревьюер тратит 10 мин на 200 строк → 2 часа на 2000 строк
- Внимание падает после 500 строк (исследования: defect detection drops)
- Меньше PR = быстрее merge = быстрее feedback
```

#### 3.3 CI на PR

```yaml
# .github/workflows/pr.yml
name: PR

on: [pull_request]

jobs:
    static-analysis:
        runs-on: ubuntu-latest
        steps:
            - checkout
            - composer install
            - vendor/bin/phpstan analyse --level 8
            - vendor/bin/psalm --output-format=compact

    unit-tests:
        runs-on: ubuntu-latest
        steps:
            - checkout
            - composer install
            - vendor/bin/phpunit tests/Domain/ tests/Application/

    integration-tests:
        services:
            postgres:
                image: postgres:16
                ports: ["5432:5432"]
        runs-on: ubuntu-latest
        steps:
            - checkout
            - composer install
            - vendor/bin/phpunit tests/Infrastructure/

    architecture-tests:
        runs-on: ubuntu-latest
        steps:
            - checkout
            - composer install
            - vendor/bin/phpunit tests/Architecture/
            # ← проверит, что Domain не импортирует Symfony/Doctrine
```

**Правило:** merge запрещён, если CI красный. Без исключений.

---

### 4. Ревью

#### 4.1 Что смотрю (чеклист)

```
Корректность:
  □ Логика соответствует What/Why в PR?
  □ Edge cases покрыты? (null, 0, empty, overflow)
  □ Исключения обработаны?
  □ Транзакции корректны? (flush в правильном месте)

Безопасность:
  □ SQL-инъекции? (параметризованные запросы)
  □ CSRF / XSS? (в Symfony — обычно ок)
  □ Secrets в коде? (env vars)
  ✓ Авторизация: #IsGranted / voter?

Производительность:
  □ N+1? (EXPLAIN в голове)
  □ Мемоизация? (не пересчёт в цикле)
  □ Индексы? (новые WHERE → нужен индекс?)

Читаемость:
  □ Имена говорят что делают?
  □ Метод < 30 строк?
  □ Нет дублирования?
  □ Комментарии объясняют WHY, не WHAT?

Тесты:
  □ Новые инварианты → новые тесты?
  □ Тесты детерминированные? (нет sleep, нет random)
  □ Покрытие error paths?
```

#### 4.2 Время и правила

```
Срок: ≤ 24 часа (рабочее время).
Не "когда-нибудь", не "завтра".

Правила:
  - Ревьюер не обязан искать баги. Он ищет НЕСОГЛАСОВАННОСТИ.
  - "Я бы написал иначе" ≠ блок. Предложение ≠ требование.
  - Nit / Suggestion / Blocking — помечать явно.
  - Если PR > 3 дня без ответа — эскалация (lead).
```

#### 4.3 Чего НЕ делаю

```
❌ Ревью в 23:40 (усталый = поверхностный)
❌ "ЛГТМ" на 500 строк (не прочитал)
❌ Ревью по диффу без контекста (открыть класс целиком)
❌ Ревью "на лету" в чате (нет истории, нет traceability)
❌ Ревью своего кода (нужен второй взгляд)
```

---

### 5. Мерж

```
Стратегия: Squash Merge

Почему:
  - В main — один коммит на PR (чистый log)
  - Откат: git revert <sha> (один коммит = одна операция)
  - История: feat/fix/refactor — читаемо
  - Без merge-коммитов (чистый linear history)

Пример git log main:
  a1b2c3d feat(order): add partial refund
  e4f5g6h fix(payment): handle 3D-Secure redirect
  i7j8k9l chore: upgrade doctrine to 2.19
  m0n1o2p perf: add index on orders(customer_id, status)
```

```
НЕ: Merge Commit (merge --no-ff)
  → "Merge branch 'feat/x' into main" — мусор в истории
  → Труднее bisect

НЕ: Rebase (на main)
  → Переписывает историю → проблемы при shared branches
  → Ревьюеры теряют ссылку
```

---

### 6. Релизы и деплой

#### 6.1 Pipeline

```
┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│  PR      │────▶│  CI      │────▶│  Deploy  │────▶│  Verify  │
│  merge   │     │  (green) │     │  to prod │     │  (checks)│
└──────────┘     └──────────┘     └──────────┘     └──────────┘
     │                │                │                │
     │                │                │                │
  Squash merge    Tests + SA      Docker build     Health check
  to main         + Arch tests    + Push + Deploy  + Error rate
                                     │
                                     ▼
                              Feature flag
                              (off by default)
                                     │
                                     ▼
                              Gradual rollout
                              10% → 50% → 100%
```

#### 6.2 Автоматизация

```yaml
# .github/workflows/deploy.yml
name: Deploy

on:
    push:
        branches: [main]

jobs:
    deploy:
        runs-on: ubuntu-latest
        steps:
            - name: Build image
              run: docker build -t registry/app:sha-${{ github.sha }} .

            - name: Push
              run: docker push registry/app:sha-${{ github.sha }}

            - name: Deploy
              run: |
                kubectl set image deployment/app app=registry/app:sha-${{ github.sha }}
                kubectl rollout status deployment/app --timeout=120s

            - name: Verify
              run: |
                # Health check
                curl -sf https://api.example.com/health
                # Error rate (после 2 мин)
                sleep 120
                curl -sf https://api.example.com/metrics | grep -q 'error_rate<0.01'

            - name: Rollback (on failure)
              if: failure()
              run: kubectl rollout undo deployment/app
```

#### 6.3 Фича-флаги

```php
// src/Infrastructure/Feature/FeatureFlagService.php
final class FeatureFlagService
{
    public function __construct(
        private readonly RedisClient $redis,
    ) {}

    public function isEnabled(string $flag, string $userId = ''): bool
    {
        $value = $this->redis->get("flag:{$flag}");

        if ($value === null || $value === '0') {
            return false;
        }

        if ($value === '100') {
            return true;
        }

        // Percent-based rollout
        $bucket = crc32($userId) % 100;
        return $bucket < (int) $value;
    }
}
```

```php
// В use-case:
final class RefundOrder
{
    public function __invoke(RefundCommand $cmd): Refund
    {
        if (!$this->flags->isEnabled('partial_refund', $cmd->userId)) {
            throw new FeatureNotEnabledException('partial_refund');
        }

        // ... бизнес-логика
    }
}
```

```
Rollout:
  T0:  flag = 0    → никто не видит
  T1:  flag = 10   → 10% пользователей
  T2:  flag = 50   → 50%
  T3:  flag = 100  → все
  T4:  убрать flag из кода (следующий PR)
```

#### 6.4 Роллбек

```
Уровень 1: Feature flag (10 секунд)
  → Выключить flag. Код в проде, но не активен.
  → Без деплоя, без restart.

Уровень 2: K8s rollout undo (30 секунд)
  → Предыдущий image. Старый код.
  → kubectl rollout undo deployment/app

Уровень 3: Git revert (5 минут)
  → git revert <sha> → PR → CI → Deploy
  → Медленно, но безопасно.
  → Использовать, если bug в данных (не только в коде).

Уровень 4: Миграция backward
  → ALTER TABLE ... DROP COLUMN
  → Только если данные несовместимы.
```

**Правило:** всегда иметь план роллбека ДО деплоя. Не «подумаем, если что».

---

### 7. Почему именно так (сводка)

| Решение | Почему |
|---------|--------|
| Trunk-based | Короткие циклы, нет merge pain, CI/CD прост |
| Conventional Commits | Traceability, changelog, semver |
| Squash merge | Чистый log, простой revert |
| PR ≤ 500 строк | Ревью качество, скорость merge |
| CI обязателен | Красный = не мержим. Без исключений. |
| Feature flags | Риск = настройка, не деплой |
| Автоматический деплой | Человек не нужен для «поставить на прод» |
| Роллбек план ДО | Не после. Всегда. |

---

### 8. Что НЕ делаю

| ❌ | Почему |
|----|--------|
| Долгие feature-ветки (недели) | Merge pain, stale code, "a bit of work" |
| Deploy только по пятницам | Friday = 3 дня без hotfix |
| "Работает на моей машине" | CI = источник правды |
| Ревью > 48 часов | Заблокированный коллега = потерянный день |
| Force push на main | Переписывает историю, ломает bisect |
| Deploy без health check | 500 на 20% пользователей → «ой» |
| Роллбек "когда-нибудь" | Если нет плана — нет роллбека |

---

### 9. Метрики процесса

```
Скорость:
  - Lead time: commit → prod (цель: < 1 час)
  - PR cycle time: создание → merge (цель: < 1 день)
  - Deploy frequency: раз в день (минимум)

Надёжность:
  - Change failure rate: % деплоев с багом (цель: < 5%)
  - MTTR: mean time to recover (цель: < 15 минут)
  - CI flaky rate: % упавших "бесплатно" (цель: < 2%)

Качество:
  - Code review turnaround: < 24 часов
  - PR size: медиана < 300 строк
  - Rework rate: % PR с > 3 итераций (цель: < 20%)
```

Эти метрики — не KPI. Это **диагностика**: если lead time вырос — где bottleneck? Если flaky rate вырос — какие тесты сломаны?

> ⏱ 42.3s · prompt 79855 tok · output 4435 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о моём подходе к код-ревью: маркировка замечаний, приоритеты, разрешение разногласий. Это продолжение серии вопросов на собеседовании PHP-бэкенда.

Нужно:
1. Описать систему маркировки (blocking / suggestion / nit / question)
2. Расставить приоритеты того, на что смотрю в первую очередь
3. Описать процесс разрешения разногласий
4. Дать конкретные примеры, а не абстракции
5. Сохранить стиль предыдущих ответов: таблицы, примеры кода, чеклисты

Структура:
- Система маркировки (с примерами из реальных PR)
- Приоритеты (что смотрю первым, что — последним)
- Чего НЕ делаю
- Разногласия: алгоритм
- Примеры реальных диалогов
- Итого

Не нужно повторять то, что уже было в предыдущем ответе о git-процессе (чеклист ревью уже там). Здесь глубже: именно механика ревью, не процесс.

</details>

**Кандидат:**

## Код-ревью: механика

### 1. Маркировка замечаний

Каждый комментарий в PR получает **ярлык**. Это не бюрократия — это способ сказать автору: «что тут нужно сделать, а что — на твоё усмотрение».

| Ярлык | Значение | Что от автора ожидается |
|-------|----------|------------------------|
| 🔴 **Blocking** | Без этого не мержим | Исправить или объяснить, почему не нужно |
| 🟡 **Suggestion** | Предложение, не требование | Взять в работу или отклонить с аргументом |
| 🔵 **Nit** | Мелочь, не блокирует | Опционально. Автор решает |
| ❓ **Question** | Не понимаю, хочу понять | Ответить. Может оказаться, что я не прав |
| 📝 **Note** | Информация для будущего | Прочитать. Ничего не делать |

#### Пример реального PR

```diff
+ public function calculateTotal(array $lines): Money
+ {
+     $total = 0;
+     foreach ($lines as $line) {
+         $total += $line->price * $line->quantity;
+     }
+     return new Money($total, 'EUR');
+ }
```

**Комментарии:**

```
🔴 Blocking (строка 4)
   $total = 0 — это int. $line->price * $line->quantity — float.
   В Money(int) будет TypeError при flush.
   Замените на: $total = new Money(0, 'EUR');
   Или: return array_sum(array_map(fn($l) => $l->price, $lines)) * qty...

🟡 Suggestion (строка 2)
   Метод зависит от конкретного типа array.
   Предлагаю: /** @param OrderLine[] $lines */ — явная подпись.
   Не блокирует, но IDE и PHPStan будут благодарны.

🔵 Nit (строка 6)
   'EUR' — магическая строка. Если валюта приходит из $line —
   используйте $lines[0]->currency. Или вынесите в константу.

❓ Question (строка 3)
   А если $lines пустой? new Money(0, 'EUR') — это корректно?
   Или бросаем EmptyCartException?

📝 Note
   В будущем, если добавим скидки — этот метод нужно будет
   разделить на subtotal + discount + total. Не сейчас, но имейте в виду.
```

**Почему так:** автор видит сразу — что **нужно** исправить (1), что **можно** (1), что **опционально** (1), что **нужно ответить** (1). Не нужно читать 5 комментариев и гадать, что из них «обязательное».

---

### 2. Приоритеты: что смотрю первым

Порядок — не случайный. Я иду от **«сломает продакшен»** к **«некрасиво»**.

#### Первый проход: корректность (5 минут)

```
□ Логика делает то, что заявлено в PR?
□ Edge cases: null, 0, empty, overflow, negative?
□ Race condition: concurrent access к данным?
□ Транзакции: flush в правильном месте? Rollback при ошибке?
□ Исключения: не глотаются? Не бросаются базовые?
□ SQL: инъекции? N+1? Отсутствие индекса на новом WHERE?
```

**Пример:**

```php
// Автор написал:
public function transfer(string $from, string $to, int $amount): void
{
    $fromAccount = $this->repo->find($from);
    $toAccount = $this->repo->find($to);

    $fromAccount->balance -= $amount;
    $toAccount->balance += $amount;

    $this->em->flush();
}
```

```
🔴 Blocking:
   1. Нет проверки balance >= 0. Отрицательный баланс в БД.
   2. Нет атомарности: если flush упадёт на 2-м UPDATE —
      деньги «исчезли» (списаны, но не зачислены).
   3. Concurrent: два перевода одновременно → lost update.
   
   Предложение:
   UPDATE accounts SET balance = balance - :amt WHERE id = :from AND balance >= :amt
   UPDATE accounts SET balance = balance + :amt WHERE id = :to
   В одной транзакции. Проверить rowCount на каждом UPDATE.
```

#### Второй проход: архитектура (3 минуты)

```
□ Границы слоёв не нарушены? (Domain не знает про Symfony)
□ SRP: метод/класс не делает 3 вещей?
□ Зависимости направлены внутрь, а не наружу?
□ Нет дублирования логики (DRY)?
□ Новый код в правильном слое?
```

#### Третий проход: читаемость (2 минуты)

```
□ Имена говорят что делают?
□ Метод < 30 строк? (если больше — можно разбить?)
□ Нет «магии» (числа, строки без контекста)?
□ Комментарии объясняют WHY, не WHAT?
□ Индентификация ≤ 4 уровня?
```

#### Четвёртый проход: тесты (2 минуты)

```
□ Новые инварианты → новые тесты?
□ Error paths покрыты?
□ Тесты детерминированные? (нет sleep, random, time())
□ Тесты читаемы? (arrange / act / assert)
```

**Итого: ~12 минут на PR до 500 строк.** Если больше — PR слишком большой или я не понимаю контекст.

---

### 3. Чего НЕ делаю

| ❌ | Почему |
|----|--------|
| Ревью без прочтения PR целиком | Вижу diff, но не контекст. Ложные замечания. |
| «Сделай как я» | Мое решение ≠ единственное. Есть аргументация — принимаю. |
| Ревью в 23:00 | Уставший = поверхностный. Откладываю. |
| «ЛГТМ» на 500 строк | Не прочитал = не ревью. |
| Blocking на стиль (отступы, имена) | Это Nit. Не блокирует. |
| Ревью по диффу, не по классу | Не вижу, как метод встраивается. |
| Комментарий «здесь можно оптимизировать» без конкретного предложения | Не помогает автору. |

---

### 4. Разногласие с автором

Это **самая частая** ситуация. Алгоритм:

#### Шаг 1: Убедиться, что я прав

```
Я пишу: "🔴 Используйте enum вместо строки"
Автор: "Мы обсуждали это, решили на строках. Строка в БД,
        а enum — доменная вещь. Не хотим耦合."

→ Я перечитываю PR, контекст, историю.
→ Возможно, автор прав. Я просто не знал контекста.
→ Отвечаю: "Понял, спасибо за контекст. Снимаю замечание."
```

**Правило:** если автор дал контекст, которого у меня не было — я **первый** снимаю замечание. Не торгуюсь.

#### Шаг 2: Если я уверен, что автор не прав

```
Я: "🔴 Здесь нужен FOR UPDATE, иначе lost update"
Автор: "У нас READ COMMITTED + optimistic locking. FOR UPDATE не нужен."

→ Я проверяю: а действительно ли optimistic locking здесь применён?
→ Открываю код. Да, version column есть.
→ Отвечаю: "Проверил, да, version column есть. Снимаю.
            Единственное: убедитесь, что retry loop есть
            в caller'е. Сейчас вижу, что OptimisticLockException
            не обработан."
```

**Ключевое:** я **проверяю**, а не спорю. «Смотри, я открыл код, и вот что вижу...»

#### Шаг 3: Если оба уверены, что правы

```
Я: "Это блокирующее замечание. Не могу мержить без исправления."
Автор: "Я понимаю, но по бизнес-причине X это должно работать так."

→ Эскалация. Но не «кто главный».
→ Вопрос: «Кто принимает решение?»
  - Если это доменное решение → Product Owner / Tech Lead
  - Если это техническое → Senior / Architect
  - Если оба уверены → 30-минутный звонок, не переписка

→ На звонке:
  - Я объясняю: «Вот что случится, если не исправить. Конкретный сценарий.»
  - Автор объясняет: «Вот почему нужно так. Конкретный сценарий.»
  - Решает третий (не мы двое).
  
→ Результат фиксируется в PR:
  "Согласовано с @lead: оставляем как есть. Причине: ..."
```

**Почему не «кто главный»:**
- Иерархия ≠ правильность. Junior может быть прав, senior — нет.
- Эскалация — не «победа». Это **решение по контексту**, которого у нас обоих не было.
- Если каждый раз «я главный» — через 3 месяца команда перестанет спорить. Это хуже, чем разногласие.

#### Шаг 4: Если автор не отвечает 24 часа

```
T0:  Оставил замечание.
T+24h: Напоминание в PR: "Привет, есть ответ по 🔴?"
T+48h: Личное сообщение: "Блокирует merge. Нужен ответ сегодня."
T+72h: Эскалация на lead: "PR заблокирован, автор не отвечает."
```

**Правило:** блокирующее замечание не может висеть > 48 часов. Либо ответили, либо эскалировали.

---

### 5. Примеры реальных диалогов

#### a) Автор прав, я нет

```
Я: "🔴 Зачем здесь $em->clear()? Это сломает identity map."
Автор: "Мы обрабатываем 50k строк в worker. Без clear() — OOM.
       Это осознанное решение, задокументировано в README."
Я: "Понял. Снимаю. Дам 🔵 nit: добавьте комментарий
    // clear() intentional: prevents OOM on large batches (see README#memory)"
```

#### b) Я прав, автор не видит

```
Я: "🔴 Здесь нет проверки на отрицательный баланс."
Автор: "А, да. Забыл. Фикшу."
(Через 2 часа)
Автор: "Исправил. Добавил проверку + тест."
Я: "LGTM. 🔵 Nit: тест называется test_negative_balance,
    но проверяет zero_balance. Переименуйте."
```

#### c) Оба уверены

```
Я: "🔴 Нужен retry loop на OptimisticLockException."
Автор: "Нет. У нас single-writer через очередь. Конфликт невозможен."
Я: "Ок, но я вижу, что AddLineHandler и RemoveLineHandler
    идут в разные очереди (routing_key по sku, не по order_id).
    Это не single-writer."
Автор: "..."
Автор: "Ты прав. Я думал, что routing по order_id. Это баг."
Автор: "Фикшу routing + добавлю retry как safety net."
```

---

### 6. Что я пишу в PR как автор (чтобы ревью было проще)

```markdown
## Что изменилось
2 абзаца. Без «улучшил код».

## Почему
Ссылка на задачу. Контекст, которого нет в тикете.

## Как проверить
1. curl -X POST ...
2. Ожидаемый ответ: ...
3. Как проверить edge case: ...

## Что НЕ изменилось
(Иногда полезно: «Не менял API, не менял БД, не менял поведение»)

## Риски
(«Если не сработает → роллбек через flag partial_refund»)
```

**Почему:** ревьюер тратит 3 минуты на чтение PR-описания вместо 15 минут на догадки.

---

### 7. Итого

```
Маркировка: Blocking / Suggestion / Nit / Question / Note
           → Автор сразу знает, что делать

Приоритеты: Корректность → Архитектура → Читаемость → Тесты
           → От «сломает» к «некрасиво»

Разногласие:
  1. Проверить, что я прав (открыть код)
  2. Если автор дал контекст — снять замечание
  3. Если оба уверены — эскалация на третьего
  4. Блокирующее замечание ≤ 48 часов
  5. Результат фиксируется в PR

Антипаттерны:
  ❌ «Сделай как я»
  ❌ Ревью без контекста
  ❌ Blocking на стиль
  ❌ «ЛГТМ» без прочтения
  ❌ Торговля 10 сообщений вместо звонка
```

> ⏱ 34.5s · prompt 84039 tok · output 3350 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит показать, как я превращаю «сырой» бизнес-запрос в конкретную задачу для исполнителя (разработчика). Это вопрос на собеседовании — проверяют:
1. Умение формулировать задачи (не «сделай поиск», а конкретный, исполнимый тикет)
2. Понимание, что между жалобой и задачей есть разрыв, который должен закрыть аналитик/тимлид
3. Что именно я включаю в формулировку и почему

Подумаю, что нужно в хорошей задаче:
- Контекст: почему это важно (метрики, частота)
- Цель: что именно должно стать возможным (не «поиск», а конкретный сценарий)
- Границы: что в scope, что не в scope
- Приёмные критерии: как понять, что задача выполнена
- Ограничения: технические, бизнес-
- Риски / что НЕ делать
- Связь с другими задачами / зависимостями

Что обязательно НЕ должно быть в задаче:
- Решение (как делать) — это выбор исполнителя
- Технологии, если не критично
- Детали реализации

Что ДОЛЖНО быть:
- Проблема (что сейчас больно)
- Цель (что станет иначе)
- Scope (что делаем, что нет)
- Acceptance criteria (конкретные, проверяемые)
- Метрики успеха (как поймём, что решили проблему)
- Ограничения (что нельзя/не нужно)
- Контекст для решений (что уже знаем, чего не знаем)

Сформулирую задачу для исполнителя (разработчика), показав каждый блок и объясняя, зачем он.

Структурирую ответ:
1. Сначала — почему сырая формулировка не подходит
2. Затем — готовая задача (тикет)
3. После — разбор каждого блока: почему именно это включено
4. Антипаттерны, которых избегаю

</details>

**Кандидат:**

## От «сырой» жалобы до исполнимой задачи

### Почему сырая формулировка не подходит

> «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями.»

| Проблема | Почему |
|----------|--------|
| Нет цели | «Найти» — как? По дате? По сумме? По товару? |
| Нет scope | Все заказы за все время? Только за 12 месяцев? |
| Нет критерия успеха | Как поймём, что «поддержка разгрузилась»? |
| Нет ограничений | Что можно, а что нельзя (бюджет, срок, зависимости) |
| Нет контекста | Почему именно сейчас? Что уже пробовали? |

Исполнитель получит тикет и сделает **что-то** — не то, что нужно бизнесу.

---

### Готовая задача

```markdown
## Задача: Поиск по историческим заказам пользователя

### Контекст
За последние 30 дней: 47 обращений в поддержку с вопросом
«где мой заказ / хочу вернуться к прошлому заказу».
Среднее время обработки обращения: 4 минуты.
Пиковый период: понедельник 10:00–14:00.

Сейчас: пользователь видит только 5 последних заказов
на странице /account/orders. Всё старше — не видно.
Нет поиска, нет фильтра, нет пагинации.

### Цель
Пользователь находит нужный прошлый заказ **без обращения
в поддержку** за ≤ 30 секунд.

### Scope (в задачи входит)
1. Пагинация на /account/orders (20 заказов на страницу)
2. Фильтр по дате (от — до)
3. Фильтр по статусу (все / delivered / cancelled)
4. Текстовый поиск по: SKU, названию товара, номеру заказа
5. Сортировка по дате (новые сверху) — по умолчанию

### Scope (НЕ входит)
- Поиск по содержимому письма / чек
- Экспорт в CSV
- Поиск по промокоду
- Уведомления о новых заказах (отдельная задача)
- Редизайн карточки заказа (отдельная задача)

### Acceptance Criteria

**PA-1: Пагинация**
- [ ] /account/orders показывает 20 заказов на страницу
- [ ] Кнопки «назад / вперёд» + номер страницы
- [ ] URL содержит ?page=N (можно шарить ссылку)
- [ ] Пустой результат: «Заказов не найдено»

**PA-2: Фильтр по дате**
- [ ] Две date-picker'ы (от, до)
- [ ] Если дата «от» > дата «до» → показать 0 заказов (не ошибка)
- [ ] Без фильтра — все заказы (без ограничения по дате)

**PA-3: Фильтр по статусу**
- [ ] Dropdown: Все / Delivered / Cancelled / In Progress
- [ ] «Все» = без фильтра по статусу

**PA-4: Текстовый поиск**
- [ ] Поле ввода, поиск по: order number, SKU, product name
- [ ] Case-insensitive
- [ ] Минимальная длина: 2 символа (иначе не ищем)
- [ ] Результат: ≤ 50 заказов (LIMIT)
- [ ] Пустой результат: «Заказов не найдено. Попробуйте изменить запрос.»

**PA-5: Сортировка**
- [ ] По умолчанию: created_at DESC
- [ ] Можно переключить на created_at ASC

**PA-6: Производительность**
- [ ] П95 запроса ≤ 500мс при 100k заказов у пользователя
- [ ] Нет N+1 (проверить через pg_stat_statements)

### Метрики успеха (замер через 2 недели после релиза)
- Обращения в поддержку «где мой заказ»: снижение на ≥ 50%
- Конверсия /account/orders → возврат товара: рост ≥ 10%
  (пользователь находит старый заказ → «купить снова»)
- П95 /account/orders: ≤ 500мс

### Ограничения
- Срок: 2 спринта (10 рабочих дней)
- Не трогать API /api/orders (использовать существующий)
- Не вводить Elasticsearch (PostgreSQL полнотекстовый поиск
  или ILIKE — на выбор исполнителя)
- Не менять схему БД без согласования с @architect
  (индекс на orders(customer_id, created_at) — ок без согласования)

### Что уже известно
- Таблица orders: ~2M строк. customer_id — индекс есть.
- Таблица order_lines: ~8M строк. sku — индекс есть.
- Сейчас: SELECT * FROM orders WHERE customer_id = X ORDER BY created_at DESC LIMIT 5
- PostgreSQL 15, без full-text-индексов

### Открытые вопросы (ответить до начала работы)
1. Ищем по product_name или только по SKU?
   → Продукт: и по обоим.
2. Если заказов > 10 000 — пагинация или infinite scroll?
   → Продукт: пагинация (проще, меньше трафик).
3. Поиск по номеру заказа — точное совпадение или префикс?
   → Продукт: префикс (user помнит «ORD-2025-...»)

### Зависимости
- Блокер: нет (можно начинать сразу)
- После: «Возврат товара» (нужен поиск, чтобы найти заказ)
- Параллельно: «Редактирование адреса доставки» (не пересекается)

### Роллбек
- Feature flag: account_order_search
- Если баг → выключить flag → старая страница (5 заказов)
```

---

### Разбор: что и почему включено

| Блок | Почему |
|------|--------|
| **Контекст с цифрами** | Исполнитель понимает **масштаб**. Не «поддержка завалена», а 47 обращений/30 дней. Это определяет приоритет и глубину решения. |
| **Цель в поведении пользователя** | Не «сделать поиск» (решение), а «находит за ≤ 30 сек без поддержки» (результат). Исполнитель может предложить не поиск, а умную группировку — и цель будет достигнута. |
| **Scope: что входит** | Убирает «а можно ещё...» на середине спринта. Чёткая граница. |
| **Scope: что НЕ входит** | Ещё важнее, чем «что входит». Без этого блокера исполнитель потянёт «пока я тут, добавлю и CSV экспорт». |
| **Acceptance Criteria** | Прверяемо. Не «работает поиск», а «минимальная длина 2 символа, LIMIT 50, case-insensitive». Ревьюер и QA работают по чеклисту, не по догадкам. |
| **Метрики успеха** | Через 2 недели мы **измеряем**, решена ли проблема. Не «ревьюер сказал ок», а 50% снижение обращений. |
| **Ограничения** | Срок, технологии, чего не делать. Без этого исполнитель «оптимизирует» с Elasticsearch, а бюджет — на PostgreSQL. |
| **Что уже известно** | Экономит 1 день разведки. Исполнитель не лезет в БД, а сразу видит: 2M строк, индексы есть. |
| **Открытые вопросы + ответы** | Убирает «а что делать, если...» на середине. Решено ДО, не в момент. |
| **Зависимости** | Исполнитель знает: «после меня будет «возврат товара»» → не ломает контракт. |
| **Роллбек** | Исполнитель знает: «если баг — не паника, есть flag». Меньше страха при деплое. |

---

### Чего я НЕ включаю и почему

| ❌ Не включаю | Почему |
|--------------|--------|
| «Используйте ILIKE» | Это **решение**. Исполнитель может предложить GIN-индекс + trigram — лучше. Я даю ограничение («без Elasticsearch»), а не реализацию. |
| «Сделайте на React» | Технология frontend — не моя зона. Если есть команда frontend — они решают. |
| «Дедлайн: пятница» | Дедлайн без причины = давление. «2 спринта» = причина (бюджет, приоритет). |
| Детальный дизайн UI | Я даю поведение (PA-1..PA-6), а не макет. Дизайнер делает макет, я — контракт. |
| «Не спорьте с продуктом» | Это не задача. Это токсичность. Если продукт неправ — эскалация. |

---

### Антипаттерны, которых избегаю

| ❌ | Почему плохо |
|----|-------------|
| «Сделай поиск по заказам» | Исполнитель сделает то, что **он** понимает. Не то, что нужно бизнесу. |
| 20 acceptance criteria | Исполнитель теряется. 5–8 — максимум. Остальное — в документе. |
| «Как лучше, так и сделай» | Без ограничений исполнитель «оптимизирует» то, что не нужно. |
| Метрика «довольные пользователи» | Неизмеримо. «50% снижение обращений» — измеримо. |
| Задание без контекста | Исполнитель не знает, **почему** это важно → не может приоритизировать решения. |
| «Срочно!» | Не факт. Если срочно — почему? Что будет, если не сделаем в этот спринт? |

---

### Итого

```
Хорошая задача =
  Контекст (почему) +
  Цель (что станет иначе) +
  Scope (что / что не) +
  Acceptance Criteria (как проверить) +
  Метрики (как измерить успех) +
  Ограничения (что нельзя) +
  Известное (что уже знаем) +
  Открытые вопросы (что решено) +
  Зависимости (что до / после) +
  Роллбек (что если)

И не включает:
  Решение (как) — это выбор исполнителя
  Макет (как выглядит) — это выбор дизайнера
  Давление (срочно!) — это отсутствие причины
```

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

## Вопросы заказчику до начала работы

### Как я их задаю

Не «перечислить 20 вопросов и уйти». А: 30 минут, 8–10 вопросов, по блокам. Записываю ответы. В конце повторяю: «Я правильно понял, что...»

---

### Блок 1: Масштаб и приоритет

**В1. Сколько обращений в поддержку сейчас и как это растёт?**

| Спросил | Зачем |
|---------|-------|
| 47/мес? 470/мес? Тренд вверх или вниз? | 47/мес = задача на 2 спринта. 470/мес = задача на 2 недели. Приоритет другой. |

**В2. Что произойдёт, если мы не сделаем в этот квартал?**

| Спросил | Зачем |
|---------|-------|
| Поддержка уйдёт в красный? Потерим клиентов? Ни один из вариантов? | Если «ничего страшного» — задача не P0. Не тратим 2 спринта. |

**В3. Есть ли другие способы решить проблему, кроме поиска?**

| Спросил | Зачем |
|---------|-------|
| Может, просто показать «последний заказ» крупнее? Или push-уведомление «ваш заказ доставлен, посмотреть»? | Я иду к **решению**, а не к **проблеме**. Заказчик просит «поиск», но может решить и «кнопка посмотреть последний заказ». |

---

### Блок 2: Поведение пользователей

**В4. Что именно пользователи ищут? По дате? По товару? По сумме?**

| Спросил | Зачем |
|---------|-------|
| «Хочу вернуться к тому заказу, где я брал drill machine» → поиск по товару | «Где мой заказ за март» → фильтр по дате |
| | Это определяет, **что** строить: текстовый поиск, фильтр по дате, или и то и другое. |

**В5. Сколько заказов у «типичного» пользователя? 5? 50? 500?**

| Спросил | Зачем |
|---------|-------|
| 5 → пагинация не нужна. 50 → пагинация нужна. 500 → нужен поиск. | Решает architecture. Не перерешаю после. |

**В6. Есть ли конкретные примеры обращений, которые я могу почитать?**

| Спросил | Зачем |
|---------|-------|
| «Где мой заказ?» — это не конкретика. | «Хочу заказать ещё раз ту же drill machine, что брал в ноябре» — конкретика. Из ответа я узнаю, что нужно «купить снова», а не просто «найти». |

---

### Блок 3: Ограничения

**В7. Бюджет: сколько спринтов/человеко-недель мы можем потратить?**

| Спросил | Зачем |
|---------|-------|
| 2 спринта → PostgreSQL, без Elasticsearch. 6 спринтов → можно и FTS, и Redis-кэш. | Без бюджета я спроектирую «идеальное», а получат «дорогое». |

**В8. Есть ли ограничения по инфраструктуре? (Elasticsearch? Redis? Ограничение по БД?)**

| Спросил | Зачем |
|---------|-------|
| Если нет Elasticsearch в infra → не предлагаю. Если есть → предлагаю. | Не трачу время на обсуждение того, что невозможно. |

**В9. Могут ли пользователи быть в разных регионах/языках? А значит — разные форматы дат?**

| Спросил | Зачем |
|---------|-------|
| Да → date-picker с локальным форматом. Нет → фиксированный формат. | Мелочь, но без ответа — баг на проде. |

---

### Блок 4: Приоритеты внутри задачи

**В10. Если мы делаем только 50% задачи — что важнее: поиск по товару или фильтр по дате?**

| Спросил | Зачем |
|---------|-------|
| Это заставляет заказчика **выбрать**. Не «всё и сразу», а «что сначала». | Если не спрошу — сделаю всё и не успею. Если спрошу — сделаю главное вовремя. |

**В11. «Купите снова» — это часть этой задачи или отдельная?**

| Спросил | Зачем |
|---------|-------|
| Если часть → добавляю кнопку «repeat order». Если отдельная → не трогаю. | Без ответа я либо добавлю лишнее, либо уберу нужное. |

---

### Блок 5: Риски

**В12. Есть ли заказы, которые пользователь НЕ должен видеть? (возврат, отмена, внутренний)**

| Спросил | Зачем |
|---------|-------|
| Да → фильтрую. Нет → показываю всё. | Без ответа — пользователь видит «внутренний тестовый заказ». |

**В13. Что делать с заказами старше 2 лет? Показывать? Архивировать?**

| Спросил | Зачем |
|---------|-------|
| Показывать → БД-запрос по 2M строк. Архивировать → отдельная задача (архив). | Определяет scope и производительность. |

---

### Блок 6: Организация

**В14. Кто принимает решение, если мы спорим с продуктовом?**

| Спросил | Зачем |
|---------|-------|
| Кто-то конкретный (имя). | Чтобы при разногласии я знал, к кому идти. Не «к руководству». |

**В15. Когда и как вы будете принимать результат?**

| Спросил | Зачем |
|---------|-------|
| Демонстрация в конце спринта? Устное «ок»? Письменное подтверждение? | Чтобы не сделать «идеально» по моему пониманию и услышать «нет, я имел в виду другое». |

---

### Что я НЕ спрашиваю

| ❌ Не спрашиваю | Почему |
|----------------|---------|
| «Какой UI?» | Это дизайнер. Я спрашиваю поведение, не пиксели. |
| «На чём фронтенд?» | Это фронтенд-команда. |
| «Какой хостинг?» | Это инфраструктура. |
| «Сколько стоит Elasticsearch?» | Я не заказчик. Я спрашиваю ограничения, не цены. |
| «А можно ещё и...?» | Это не вопрос. Это scope-creep. |

---

### Итого: 15 вопросов, 5 блоков

```
Масштаб      → В1, В2, В3      (нужно ли это? как срочно?)
Поведение    → В4, В5, В6      (что именно делают пользователи?)
Ограничения  → В7, В8, В9      (что можно, что нельзя?)
Приоритеты   → В10, В11        (что главное, если времени мало?)
Риски        → В12, В13        (что может пойти не так?)
Организация  → В14, В15        (кто решает, как принимаем?)

Время: 30 минут.
Результат: формулировка задачи (см. предыдущий ответ).
```

**Главный принцип:** каждый вопрос должен иметь **ответ, который меняет решение**. Если ответ не меняет то, что я буду делать — вопрос не нужен.

> ⏱ 30.3s · prompt 87220 tok · output 2859 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос из категории «профессиональная этика и коммуникация»: как вести себя, когда по ходу работы возникают (а) побочный баг и (б) противоречие в критериях приёмки. Это вопрос на собеседовании/интервью, поэтому нужен не абстрактный ответ, а конкретный, структурированный, с примерами и обоснованием. 

Я буду отвечать как тот же PHP-бэкенд-разработчик, что и в предыдущих сообщениях — с примерами кода, конкретикой, без воды. Нужно разделить два кейса, дать алгоритм действий и объяснить «почему». 

Кейс (а) — попутный баг:
- Не фикшу молча (scope creep, незаметный change)
- Не игнорию (если критичный — плохо)
- Документирую, оцениваю, предлагаю варианты
- Критерий: блокирует ли задачу? критичность?
- Формат: тикет + упоминание в PR/статусе

Кейс (б) — противоречие в критериях:
- Не молча выбираю одно из двух
- Не «делаю как в критериях, раз написано»
- Останавливаюсь, фиксирую противоречие, эскалирую
- Формат: конкретный пример, почему противоречит, 2–3 варианта решения
- Кто решает: product/tech lead, не я

Структурирую так: общий принцип → кейс (а) с примером → кейс (б) с примером → таблица/сводка → антипаттерны.

Примеры возьму из контекста предыдущей задачи (поиск по заказам) — так будет связно.

Язык — русский, как и весь диалог. Форматирование — markdown, таблицы, код.

</details>

**Кандидат:**

## Два разных кейса, два разных алгоритма

Общий принцип: **в обоих случаях я не молчу и не решаю за других**. Но действия разные — в (а) я управляю scope, в (б) я эскалирую решение.

---

### Кейс (а): Нашёл попутный баг

#### Что делаю

**1. Останавливаюсь на 2 минуты и классифицирую:**

```
Критичность:
  - P0: data loss, деньги, безопасность → эскалирую СЕЙЧАС
  - P1: сломанный основной сценарий → сегодня
  - P2: edge case, редкий → в бэклог
  - P3: косметика, опечатка → заметка

Связь с моей задачей:
  - Блокирует ли мою задачу? (если да — это не «попутный»)
  - В том ли модуле? (если да — риск затронуть при ревью)
  - Является ли побочным эффектом моих изменений? (если да — это МОЙ баг)
```

**2. Если не блокирует и не мой:**

```
❌ Не фикшу молча в своём PR.
   → PR растёт, ревьюер видит «а зачем это здесь?»,
     scope-creep, сложнее откат.

❌ Не игнорирую и не молчу.
   → Баг остаётся, я его знаю, никто другой — нет.

✅ Документирую:
   - Создаю тикет (с репро, severity, приоритет)
   - В своём PR/статусе пишу: «Заметил баг X,
     завёл тикет #1234, не блокирует текущую задачу»
   - Если P0/P1 — отдельно пишу product/lead, не жду спринт-плана
```

**3. Если блокирует или в том же модуле:**

```
✅ Если блокирует → это не «попутный», это моя задача.
   Фикшу в своём PR или разбиваю: баг отдельно, фича отдельно.

✅ Если в том же модуле, но не блокирует:
   - Предложить lead: «Можно зафиксировать вместе?
     В том же файле, ревью будет одним махом.»
   - Если да → фикшу, но ОТСРЕЛЯЮ коммитом:
     fix(order): handle null quantity in price calculation
     (отдельный коммит, легко откатить)
   - Если нет → тикет в бэклог.
```

#### Пример (из задачи поиска по заказам)

```
Работаю над пагинацией /account/orders.
Натыкаюсь: при ?page=999999 возвращается 500,
а должно 404 или пустой список.

Классификация:
  - P2 (edge case, пользователь так не введёт, но API-консьюмеры могут)
  - Не блокирует мою задачу (я делаю пагинацию, а не 500-х)
  - В том же контроллере → риск затронуть при ревью

Решение:
  1. Завожу тикет: «GET /account/orders?page=999999 → 500»
     с репро и ожидаемым поведением (404 или 200 + empty)
  2. В статусе задачи пишу:
     «Заметил баг с out-of-range page, тикет #1234.
      Не блокирует, но в том же контроллере.
      Предложение: зафиксировать в этом PR отдельным коммитом.»
  3. Lead решает. Если да → отдельный коммит:
     fix(order): return empty list for out-of-range page
  4. Если нет → тикет живёт в бэклоге, я не трогаю.
```

#### Почему так

```
1. Scope-дисциплина.
   PR = одна задача. Ревьюер тратит внимание на одну вещь.
   Мешанина = хуже ревью, сложнее откат, сложнее бисект.

2. Видимость.
   Баг в тикете = у него есть владелец, приоритет, статус.
   Баг «в голове у разработчика» = нет ничего из этого.

3. Ответственность.
   Я не скрываю, что знаю о баге. Но и не беру на себя
   решение, которое не моё (приоритет, срок).

4. Ревью-гигиена.
   Отдельный коммит fix(...) → если баг вернётся —
   откатываю один коммит, а не копаю в PR-е.
```

---

### Кейс (б): Критерий приёмки противоречит цели

#### Что делаю

**1. Сначала убеждаюсь, что противоречие реальное, а не моё непонимание.**

```
Перевожу оба в конкретные сценарии:

  Цель:        «Пользователь находит заказ за ≤ 30 сек без поддержки»
  Критерий:    «Поиск только по точному номеру заказа»

  Сценарий A: пользователь помнит «drill machine, ноябрь»
              → Цель: находит. Критерий: не находит (нет товара в поиске).
              → ПРОТИВОРЕЧИЕ.

  Сценарий B: пользователь помнит «ORD-2025-12345»
              → Цель: находит. Критерий: находит.
              → Совпадает.

  → Значит, противоречие есть, но частичное:
    критерий покрывает сценарий B, но не A.
```

**2. Фиксирую противоречие в письменном виде.**

```
❌ В устной беседе: «А тут вот, мне кажется, это не то.»
   → Никто не помнит, что решено. Через неделю спорим заново.

✅ В PR / тикете / чате (текстом):

  «Обнаружил противоречие:

  Цель задачи:    пользователь находит заказ по товару/дате/номеру.
  Критерий PA-4:  поиск только по точному номеру заказа.

  Сценарий:       пользователь помнит товар, не номер.
  При текущем    критерии:      не найдёт → обращается в поддержку.
                                (цель не достигнута)

  Варианты:
  A) Расширить PA-4: поиск по номеру + SKU + товар
     (≈ +3 дня, нужен GIN-индекс на product_name)
  B) Оставить PA-4, добавить фильтр по дате (PA-2 уже есть)
     (покрывает «ноябрь», не покрывает «drill machine»)
  C) Оставить PA-4 как есть, перенести «поиск по товару»
     в отдельную задачу следующего квартала

  Предлагаю A. Нужно ваше решение до начала работы.»
```

**3. Эскалирую на того, кто принимает решение.**

```
Кто решает?
  - Продуктовое решение (что важнее) → Product Owner
  - Техническое (что возможно) → Tech Lead / Architect
  - Приоритет (сколько дней) → Lead

Не решаю сам. Но приношу варианты, а не вопрос.
«Что делать?» — плохо. «A/B/C, предлагаю A» — хорошо.
```

**4. Жду решение. Не начинаю работу по «своему» варианту.**

```
❌ «Ладно, сделаю по-своему, раз они молчат.»
   → 3 дня работы → «нет, мы хотели B» → переделка.

✅ Стою. Если > 24 часа без ответа → напоминание.
   Если > 48 часов → эскалация выше.
```

#### Пример (из той же задачи)

```
Цель:      «Пользователь находит заказ за ≤ 30 сек»
Критерий:  «Текстовый поиск по SKU, названию товара, номеру»
Ограничение из ТЗ: «Без Elasticsearch»

Конфликт:
  Поиск по «названию товара» на 8M строк order_lines
  через ILIKE '%drill%' → full table scan → 5–15 сек.
  П95 ≤ 500мс (из PA-6) не выполним.

Фиксирую:

  «Противоречие между PA-4 (поиск по товару)
   и PA-6 (П95 ≤ 500мс) при ограничении «без Elasticsearch».

  Варианты:
  A) Убрать «поиск по товару» из PA-4.
     Остаётся: номер + SKU + фильтр по дате.
     П95 ок. (−1 сценарий: «помню товар»)
  B) PostgreSQL trigram (pg_trgm) + GIN-индекс.
     П95 ~1–2с. Частично нарушает PA-6.
     (+1 сценарий, но медленно)
  C) Redis-кэш популярных запросов + ILIKE fallback.
     П95 ок для топ-100 запросов, медленно для хвоста.
     (+сложность инфраструктуры)
  D) Перенести «поиск по товару» в следующую задачу,
     сейчас — номер + SKU + дата.

  Предлагаю D: не блокирует текущий релиз,
  цель покрывается на 80% (номера и даты — частый кейс
  по обращениям в поддержку).

  Жду решение до начала работы над поиском.»
```

#### Почему так

```
1. Не решаю за product.
   «Что важнее: скорость или покрытие сценариев?» —
   это бизнес-решение, не моё.

2. Приношу варианты, а не проблему.
   «У меня противоречие» — бесполезно.
   «Вот 4 варианта, предлагаю D» — можно решить за 5 минут.

3. Фиксирую письменно.
   Устные решения теряются. Письменные — в истории.
   Если через месяц спросят «почему так» — есть ответ.

4. Не начинаю работу до решения.
   Работа по «своему» варианту = риск переделки.
   Остановка на 1 день < переделка 3 дней.

5. Не молчу «раз так написано».
   Критерий противоречит цели → цель не будет достигнута.
   Если я молчу — я соучастник.
```

---

### Сводка: два кейса, два алгоритма

| | (а) Попутный баг | (б) Противоречие в критериях |
|--|-------------------|------------------------------|
| Первое действие | Классифицировать (P0–P3, блокирует?) | Фиксировать в письменном виде |
| Кто решает | Я (scope) / Lead (приоритет) | Product / Tech Lead |
| Формат | Тикет + упоминание в статусе | Варианты A/B/C + рекомендация |
| Работа продолжается? | Да (если не блокирует) | **Нет**, жду решения |
| Риск, если молчать | Баг остаётся без владельца | Цель не достигнута, переделка |
| Риск, если «решу сам» | Scope creep, плохое ревью | Переделка, конфликт с product |

---

### Антипаттерны

| ❌ | Почему плохо |
|----|-------------|
| (а) Починить молча в PR | Scope creep, ревьюер не понимает, сложно откатить |
| (а) Заметить и промолчать | «Я знал, но не сказал» — при инциденте это проблема |
| (а) Починить и не завести тикет | Нет истории, нет владельца, через месяц вернётся |
| (б) Сделать «как в критериях» и не сказать | Цель не достигнута, я соучастник |
| (б) Сделать «как я думаю» и не сказать | Переделка, конфликт |
| (б) Устно обсудить и не зафиксировать | Через неделю спорим заново |
| (б) Начать работу до решения | Риск переделки. Остановка < переделка |

---

### Главный принцип

```
(а) Попутный баг → я управляю scope, но не приоритет.
    Документирую, предлагаю, lead решает срок.

(б) Противоречие → я не решаю.
    Приношу варианты + рекомендацию.
    Решает тот, у кого есть контекст, которого у меня нет.

Общее: не молчу. Не решаю за других.
Фиксирую письменно. Приношу варианты, а не проблемы.
```

> ⏱ 33.9s · prompt 91492 tok · output 3443 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь продолжает интервью на роль PHP-бэкендера. Вопрос про отчёт о выполнении задачи: что в него входит, и как меняется при частичном выполнении.

Это вопрос про коммуникацию и профессионализм. Нужно:
1. Структура полного отчёта (когда всё сделано)
2. Что меняется при частичном выполнении
3. Почему именно так — связь с предыдущими ответами (задача, критерии, антипаттерны)

Важно не повторяться с предыдущими ответами, а дать конкретику формата отчёта. Это то, что реально пишут в Jira/Slack/PR при закрытии задачи.

Структура:
- Отчёт = не «готово», а доказательство, что критерии выполнены
- Что входит: результат, доказательства, остаток, риски, follow-up
- При частичном выполнении: чёткое разделение «что сделано / что нет / почему / что дальше»
- Ключевое: отчёт — это контракт с заказчиком, а не «отчёт для галочки»

Свяжу с предыдущими ответами: acceptance criteria из задачи, метрики, роллбек, feature flags — всё это отражается в отчёте.

</details>

**Кандидат:**

## Отчёт о выполнении задачи

### Главный принцип

Отчёт — это **доказательство**, что цель достигнута. Не «я написал код». Не «тесты зелёные». А: вот что изменилось для пользователя, вот как я это проверил, вот что осталось, вот что может пойти не так.

Если после прочтения отчёта человек **не должен ничего переспрашивать** — отчёт хороший.

---

### Структура отчёта (задача выполнена полностью)

```markdown
## Отчёт: Поиск по историческим заказам (задача #4521)

### Статус: ✅ Готово к приёмке

### 1. Что сделано
Все 6 критериев приёмки выполнены:

| Критерий | Статус | Доказательство |
|----------|--------|----------------|
| PA-1: Пагинация (20/стр) | ✅ | Скрин + URL с ?page=2 |
| PA-2: Фильтр по дате | ✅ | Скрин + тест |
| PA-3: Фильтр по статусу | ✅ | Скрин + тест |
| PA-4: Текстовый поиск | ✅ | Скрин + тест |
| PA-5: Сортировка | ✅ | Скрин |
| PA-6: П95 ≤ 500мс | ✅ | Замер на staging (ниже) |

### 2. Как проверить
1. Зайти на staging: https://staging.example.com/account/orders
2. Логин: test@example.com / test123
3. Создать 25 тестовых заказов (скрипт: scripts/create_test_orders.php)
4. Проверить: пагинация, фильтры, поиск
5. Замерить: DevTools → Network → time для /account/orders

### 3. Результаты замеров
- П95 /account/orders: 320мс (цель ≤ 500мс) ✅
- П99: 480мс ✅
- Запросов к БД: 3 (было: 27, N+1 устранён)
- Размер ответа: 12KB (20 заказов)

### 4. Что изменилось в коде
- src/Domain/Order/OrderSearchCriteria.php (новый)
- src/Application/Order/SearchOrders.php (новый use-case)
- src/Infrastructure/Persistence/Order/DoctrineOrderRepository.php (изменён)
- src/Infrastructure/Http/Controller/OrderController.php (изменён)
- src/Infrastructure/Http/Request/SearchOrdersRequest.php (новый)
- migration: 2025_01_15_add_trgm_index.sql
- тесты: 12 новых (5 unit, 5 integration, 2 E2E)
- Feature flag: account_order_search (off by default)

### 5. Риски
- Поиск по product_name использует pg_trgm.
  При >50k заказов на пользователя скорость может деградировать.
  Мониторинг: алерт при П95 > 800мс.
- Если pg_trgm не установлен в прод → fallback на ILIKE
  (медленнее, но работает). Проверено на staging.

### 6. Роллбек
- Feature flag: account_order_search
- Если баг → выключить flag (10 сек, без деплоя)
- Индекс можно оставить (не мешает старому коду)

### 7. Follow-up (не блокирует приёмку)
- [ ] Тикет #4522: «Купите снова» (кнопка repeat order)
- [ ] Тикет #4523: «Поиск по промокоду» (из scope-out)
- [ ] Тикет #4524: Метрики — настроить алерт на П95

### 8. Метрики успеха (замер через 2 недели)
- Обращения «где мой заказ»: baseline 47/мес → цель ≤ 23/мес
- Конверсия «нашёл заказ → повторил покупку»: baseline 3% → цель ≥ 5%
- Замер: 15 февраля, отчёт в Jira
```

---

### Что обязательно и почему

| Блок | Почему без него не ок |
|------|----------------------|
| **Статус одной строкой** | Заказчик читает 1 строку и понимает: можно принимать или нет |
| **Таблица по критериям** | Не «всё сделано», а **каждый** критерий + доказательство. Заказчик не должен перепроверять сам. |
| **Как проверить** | Конкретные шаги, а не «зайти и посмотреть». Если я смогу повторить — значит, это работает. |
| **Замеры** | «Быстро» ≠ 320мс. Цифры = доказательство PA-6. |
| **Что изменилось в коде** | Ревьюер знает, что читать. DBA знает, что миграция есть. |
| **Риски** | Не «всё идеально», а «вот где может сломаться и что я сделал». |
| **Роллбек** | «Если что — выключить flag». Не «подумаем». |
| **Follow-up** | Чётко: что сделано, что нет, что дальше. Не «а можно ещё...». |
| **Метрики + дата** | «Через 2 недели замерируем». Не «потом посмотрим». |

---

### Что НЕ входит в отчёт

| ❌ Не включаю | Почему |
|--------------|--------|
| Детальный diff | Это в PR, не в отчёте |
| «Было тяжело, но справился» | Не информация для заказчика |
| Обоснование всех решений | Обоснование — в PR-описании и в ревью |
| Скриншоты каждого экрана | 2–3 ключевых, не 15 |
| «Сделал лучше, чем просили» | Если не в scope — это тикет #4522, не часть отчёта |
| Список всех файлов | 5–7 ключевых, не 40 |

---

## Если часть задачи не выполнена

### Что меняется

Структура та же, но появляются **три новых блока**:

```
1. Статус: ⚠️ Частично выполнено
2. Что НЕ сделано + почему
3. Что нужно для завершения
```

И **исчезает** блок «Все критерии выполнены». Вместо него — честная таблица с ✅ и ❌.

---

### Пример: не удалось сделать поиск по товару

```markdown
## Отчёт: Поиск по историческим заказам (задача #4521)

### Статус: ⚠️ Частично выполнено (4 из 6 критериев)

### 1. Что сделано

| Критерий | Статус | Доказательство |
|----------|--------|----------------|
| PA-1: Пагинация (20/стр) | ✅ | Скрин + тест |
| PA-2: Фильтр по дате | ✅ | Скрин + тест |
| PA-3: Фильтр по статусу | ✅ | Скрин + тест |
| PA-4: Текстовый поиск | ⚠️ Частично | См. ниже |
| PA-5: Сортировка | ✅ | Скрин |
| PA-6: П95 ≤ 500мс | ✅ (для того, что сделано) | 280мс |

### 2. Что НЕ сделано и почему

**PA-4: Поиск по названию товара — НЕ ГОТОВО**

Причина:
  pg_trgm + GIN-индекс на order_lines.product_name
  создаёт P99 = 3.2с при 50k заказов (цель ≤ 500мс).
  Не укладывается в PA-6.

Что работает:
  ✅ Поиск по номеру заказа (ORD-2025-XXXXX)
  ✅ Поиск по SKU
  ✅ Фильтр по дате
  ✅ Фильтр по статусу

Что НЕ работает:
  ❌ Поиск по названию товара («drill machine»)

### 3. Варианты завершения (нужно решение)

| Вариант | Что даёт | Стоимость | П95 |
|---------|----------|-----------|-----|
| A: Оставить как есть | Номер + SKU + дата | 0 (готово) | 280мс |
| B: + pg_trgm | + поиск по товару | 1 день | 3.2с (нарушает PA-6) |
| C: + Redis-кэш | + поиск по товару (топ-100) | 3 дня | 400мс (топ), 2с (хвост) |
| D: Отложить в Q2 | + поиск по товару | 5 дней (Elasticsearch) | 300мс |

**Моя рекомендация: A.**
По данным поддержки (47 обращений/мес):
  - 32 из 47 — «помню номер или дату» → покрывается A
  - 15 из 47 — «помню товар» → не покрывается

  A закрывает 70% обращений. B нарушает SLA.
  D — полное решение, но в Q2.

Жду решение до 15 января.

### 4. Как проверить (то, что работает)
1. Staging: https://staging.example.com/account/orders
2. Поиск: «ORD-2025-» → находит
3. Фильтр: январь 2025 → находит
4. П95: DevTools → 280мс

### 5. Риски
- Если выбор = B: П99 3.2с. Пользователь будет ждать.
  Алерт: П95 > 800мс → уведомление в канал.
- Если выбор = A: 15 обращений/мес останутся без покрытия.
  Mitigation: в поддержке добавить подсказку
  «Покажите номер заказа» (скрипт).

### 6. Роллбек
- Feature flag: account_order_search (off by default)
- Индексы: можно оставить, не мешают

### 7. Follow-up
- [ ] Решение по PA-4: до 15 января
- [ ] Если A → тикет «Поиск по товару» в Q2
- [ ] Если B → нагрузка-тест на staging ДО деплоя
- [ ] Метрики: 15 февраля

### 8. Что нужно от заказчика
- [ ] Выбор варианта (A/B/C/D) — до 15 января
- [ ] Если B/C/D: подтверждение, что П99 > 500мс допустим
- [ ] Приёмка того, что уже работает (PA-1, 2, 3, 5)
```

---

### Что принципиально меняется

| | Всё готово | Частично |
|--|-----------|----------|
| Статус | ✅ | ⚠️ + что именно не готово |
| Таблица | Все ✅ | Честные ✅ и ❌ |
| «Почему» | Отсутствует | **Обязателен**: причина, не оправдание |
| Варианты | Отсутствуют | A/B/C + рекомендация |
| Решение | «Принимайте» | «Выберите вариант, я продолжу» |
| Риск | Низкий | Явный: «если B → П99 3.2с» |
| Приёмка | Вся задача | «Примите то, что готово + решите остальное» |

---

### Чего НЕ делаю при частичном выполнении

| ❌ | Почему |
|----|--------|
| «Почти всё готово, осталось мелочи» | «Мелочи» = 50% работы. Не «почти». |
| Спрятать ❌ в общий ✅ | Заказчик примет «всё» и обнаружит дыру на проде |
| Не писать «почему» | «Не успел» — не причина. «П99 3.2с, не укладывается в SLA» — причина |
| Решить сам «а, оставлю как есть» | Это решение product, не моё |
| Не предлагать варианты | «Не могу» — бесполезно. «Вот 4 варианта, предлагаю A» — полезно |
| Ждать, пока спросят | Если через 2 недели спросят «а почему нет поиска по товару?» — поздно |

---

### Итого

```
Полный отчёт =
  Статус (1 строка)
  + Таблица по критериям (✅/❌ + доказательство)
  + Как проверить (шаги)
  + Замеры (цифры)
  + Что изменилось (файлы, миграции)
  + Риски (где сломается)
  + Роллбек (что если)
  + Follow-up (что дальше)
  + Метрики (как измерим успех)

Частичный отчёт =
  Всё выше +
  Честные ❌ с причиной
  + Варианты A/B/C + рекомендация
  + Что нужно от заказчика (решение, срок)
  + «Примите готовое, решите остальное»

Общее:
  Не «готово» vs «не готово».
  А: вот что работает, вот что нет, вот что нужно, вот когда.
```

> ⏱ 31.9s · prompt 94551 tok · output 3407 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about FORCE INDEX in PostgreSQL. This is a trick question / knowledge check. FORCE INDEX is a MySQL/MariaDB feature, not a PostgreSQL one. PostgreSQL does not have FORCE INDEX syntax.

Let me think about what the user might be testing:
1. Do I know that FORCE INDEX is MySQL, not PostgreSQL?
2. Do I understand the analogous mechanisms in PostgreSQL?
3. Can I explain when you'd want to force index usage and how to do it in PostgreSQL?

The correct answer is: FORCE INDEX doesn't exist in PostgreSQL. It's a MySQL/MariaDB construct. In PostgreSQL, the planner is (generally) trusted to pick the right index. But there are situations where you might want to influence the planner:

- `SET enable_hashjoin = off;` etc. to disable specific plan types
- `SET enable_indexscan = off;` to force seq scan
- Creating/dropping indexes
- `SET random_page_cost` to influence cost estimates
- Rewriting the query (e.g., adding a WHERE clause that makes the index more attractive)
- `ANALYZE` to update statistics
- `pg_hint_plan` extension (similar to MySQL's force index)
- Rewriting with explicit JOIN order
- `SET plan_cache_mode` for prepared statements

I should:
1. Clearly state that FORCE INDEX is not a PostgreSQL feature
2. Explain what it is in MySQL
3. Explain the PostgreSQL equivalents and when you'd want to influence the planner
4. Give practical examples

This is a good opportunity to show that I don't just regurgitate — I catch the premise error and provide the correct information.

Let me structure:
- Direct answer: FORCE INDEX не существует в PostgreSQL
- Что это в MySQL
- Когда хочется «заставить» планировщик
- Как это сделать в PostgreSQL (аналоги)
- Когда это оправдано, а когда нет
- Антипаттерны

</details>

**Кандидат:**

## FORCE INDEX не существует в PostgreSQL

`FORCE INDEX` — это синтаксис **MySQL/MariaDB**:

```sql
-- MySQL
SELECT * FROM orders FORCE INDEX (idx_customer) WHERE customer_id = 'abc';
```

В PostgreSQL такой конструкции **нет**. Это частая ловушка при переходе с MySQL или при поиске «как сделать в PostgreSQL то, что я делал в MySQL».

---

### Почему в PostgreSQL это не нужно (в 95% случаев)

Планировщик PostgreSQL — **cost-based** и, как правило, выбирает правильный план. Если он выбирает Seq Scan вместо Index Scan — причина почти всегда не в «глупости планировщика», а в:

- устаревших статистиках (`ANALYZE` не выполнялся),
- низкой selectivity (индекс не окупается),
- некорректной стоимости (`random_page_cost` завышен для SSD),
- маленьком объёме данных (Seq Scan на 1000 строк быстрее Index Scan).

---

### Когда всё же хочется повлиять на планировщик

#### 1. Устаревшая статистика (самая частая причина)

```sql
-- Диагностика:
SELECT relname, reltuples, n_live_tup, last_analyze, last_autoanalyze
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- Если reltuples сильно отличается от n_live_tup:
ANALYZE orders;

-- После:
EXPLAIN (ANALYZE) SELECT * FROM orders WHERE customer_id = 'abc';
-- → скорее всего, Seq Scan уйдёт сам
```

**Это не «force index». Это «дай планировщику правильные данные».**

---

#### 2. Запрет конкретного типа плана (временный, для диагностики)

```sql
-- Отключить Hash Join (если планировщик выбирает его, а нужно Nested Loop):
SET enable_hashjoin = off;
EXPLAIN (ANALYZE) SELECT ...;

-- Отключить Index Scan (если планировщик «зациклился» на индексе):
SET enable_indexscan = off;
EXPLAIN (ANALYZE) SELECT ...;

-- Отключить Merge Join:
SET enable_mergejoin = off;

-- Сбросить:
RESET enable_hashjoin;
RESET enable_indexscan;
RESET enable_mergejoin;
```

**Важно:** это **не** «force index». Это **запрет** определённых планов. Планировщик всё равно выбирает из оставшихся. Использовать только для **диагностики**, не для прода.

---

#### 3. Корректировка стоимостей

```sql
-- Если SSD, а планировщик думает, что диск медленный:
-- random_page_cost по умолчанию = 4.0 (для HDD)
-- Для SSD: 1.1–1.5

SET random_page_cost = 1.1;
EXPLAIN (ANALYZE) SELECT ...;

-- Постоянно (в postgresql.conf):
random_page_cost = 1.1
```

Планировщик пересчитает стоимость и, скорее всего, выберет Index Scan.

---

#### 4. Покрытие (covering index) — «force» через структуру

```sql
-- Проблема:
SELECT customer_id, status, total_cents FROM orders WHERE customer_id = 'abc';
-- Index Scan на idx_customer → затем heap fetch на 3 колонки.

-- Решение: covering index
CREATE INDEX idx_orders_covering
    ON orders (customer_id)
    INCLUDE (status, total_cents);

-- Теперь: Index Only Scan (без heap fetch)
-- Планировщик сам выберет его — дешевле.
```

Это не «force», а «сделай так, чтобы правильный план был самым дешёвым».

---

#### 5. `pg_hint_plan` — расширение (ближе всего к FORCE INDEX)

```sql
CREATE EXTENSION pg_hint_plan;

SELECT * FROM orders
/*+ IndexScan(o idx_orders_customer) */
WHERE customer_id = 'abc';
```

**Когда использовать:**
- Временный workaround, пока не найдена корневая причина.
- Тестирование альтернативных планов.

**Когда НЕ использовать:**
- В прода-коде. Это костыль. Если планировщик «упрямится» — ищите причину (статистика, стоимость, структура данных).

---

#### 6. Переписывание запроса

Часто «force index» — симптом того, что запрос можно переписать:

```sql
-- До (Seq Scan):
SELECT * FROM orders
WHERE customer_id = 'abc' OR status = 'pending'
ORDER BY created_at DESC LIMIT 20;

-- Проблема: OR ломает использование индекса.

-- После (UNION ALL — каждый вариант по индексу):
SELECT * FROM (
    SELECT * FROM orders WHERE customer_id = 'abc' ORDER BY created_at DESC LIMIT 20
    UNION ALL
    SELECT * FROM orders WHERE status = 'pending' AND customer_id IS DISTINCT FROM 'abc' ORDER BY created_at DESC LIMIT 20
) combined ORDER BY created_at DESC LIMIT 20;
```

---

### Сравнение: MySQL FORCE INDEX vs PostgreSQL

| | MySQL | PostgreSQL |
|--|-------|------------|
| Синтаксис | `FORCE INDEX (idx)` | Нет прямого аналога |
| Когда применяют | Часто (планер MySQL хуже) | Редко (планер PG лучше) |
| Правильное решение | `ANALYZE TABLE` + пересмотр запроса | `ANALYZE` + `random_page_cost` + covering index |
| Костыль | `FORCE INDEX` | `SET enable_* = off` / `pg_hint_plan` |

---

### Итого

```
FORCE INDEX — MySQL, не PostgreSQL.

В PostgreSQL «заставить» планировщик:
  1. ANALYZE (статистика)         → 90% случаев
  2. random_page_cost (стоимость) → SSD
  3. Covering index (структура)   → Index Only Scan
  4. SET enable_* = off (диагностика) → временно
  5. pg_hint_plan (костыль)       → экстренно, с планом удаления
  6. Переписать запрос            → часто лучшее решение

Никогда: pg_hint_plan в прода-коде «навсегда».
Всегда: сначала найти причину, потом лечить симптом.
```

> ⏱ 19.3s · prompt 97710 tok · output 1879 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о влиянии `strict_types=1` на производительность PHP. Это вопрос с подвохом — он предполагает, что есть измеримое "процентов замедления", но реальность сложнее. Нужно ответить честно: нет единой цифры, влияние минимально и зависит от контекста, и объяснить механику.

Ключевые факты:
1. strict_types влияет только на режим вызова функций (strict vs coercive), не на сам код внутри функции.
2. Проверка типов в strict-режиме — это сравнение типов аргументов перед вызовом. В coercive-режиме — попытка приведения.
3. На практике разница в микросекундах на вызов.
4. Есть бенчмарки, но они показывают: 0–5% в синтетических микробенчмарках, в реальных приложениях — статистически незначимо.
5. Главное: overhead есть, но он на уровне шума. Не "5%", а "миллисекунды на 10k вызовов".

Нужно:
- Честно сказать, что единой цифры нет
- Объяснить механику (что именно проверяется)
- Привести реалистичные порядки величин
- Сказать, когда это может быть измеримо (hot loops, миллионы вызовов)
- Не придумывать точные проценты, если их нет
- Развеять миф о "заметном замедлении"

Структура:
1. Короткий ответ: нет единой цифры, порядок — доли процента в реальных приложениях
2. Механика: что именно происходит при вызове
3. Когда измеримо / когда нет
4. Что реально влияет на производительность (не strict_types)
5. Итог: не стоит выбирать между производительностью и типобезопасностью

Формат: таблица + примеры кода + бенчмарк-пример. Без воды.

</details>

**Кандидат:**

## short answer

Единой цифры нет. В реальных приложениях — **статистически незначимо** (< 0.5% общего времени). В синтетических микробенчмарках с миллионами вызовов «чистых» функций — до 5–15%. Но такие нагрузки не типичны.

---

### Что именно происходит при вызове

```php
// strict_types=1
function add(int $a, int $b): int { return $a + $b; }
add(1, 2);

// В VM (Zend Engine):
// 1. Проверить, что 1 — int (не float, не string)
// 2. Проверить, что 2 — int
// 3. Вызвать функцию
// 4. Проверить, что результат — int

// strict_types=0
function add(int $a, int $b): int { return $a + $b; }
add("1", "2");

// В VM:
// 1. "1" — string, ожидаем int → приведение (string_to_int)
// 2. "2" — string, ожидаем int → приведение
// 3. Вызвать функцию
// 4. Результат — int, ок
```

**Ключевое:** `strict_types` **не добавляет** проверку. В coercive-режиме проверок **больше** (приведение + проверка). В strict — **меньше** (только проверка типа, без приведения).

```
coercive:  check_type → coerce → check_result
strict:    check_type → check_result
```

Значит, **strict быстрее coercive**, а не медленнее. Разница — в микросекундах.

---

### Где overhead реально есть

| Ситуация | Overhead | Почему |
|----------|----------|--------|
| Миллионы вызовов `add(1,2)` в цикле | 1–5 мкс на 10k вызовов | Проверка типа аргументов |
| Вложенные вызовы (100 уровней) | Кумулятивно заметен | Каждый уровень — проверка |
| Union types (`int\|string`) | Чуть больше | Больше вариантов для проверки |
| Intersection types | Чуть больше | instanceof на каждом |
| `mixed` как параметр | 0 | Не проверяется |
| Объявление типов в сигнатуре | 0 | Компилируется в opcode, не в runtime |

**Порядок:** 1 проверка типа ≈ 1–10 нс (наносекунд) на modern CPU. 10 000 вызовов ≈ 10–100 мкс. Для запроса, который занимает 50 мс — это 0.02–0.2%.

---

### Что реально влияет на производительность (не strict_types)

| Фактор | Влияние |
|--------|---------|
| N+1 запросы к БД | 10–100× |
| Серилизация/десерилизация JSON | 5–50× |
| Reflection (DI-контейнер, ORM hydration) | 10–100× |
| Строковые операции в цикле | 2–10× |
| `trim()`, `preg_replace` в hot path | 1–5× |
| **strict_types** | **< 1%** |

Если ваше приложение тормозит — проблема **не** в `strict_types`. Она в N+1, в reflection, в строках, в БД.

---

### Бенчмарк (схема)

```php
// benchmark.php
<?php
declare(strict_types=1);

function add(int $a, int $b): int { return $a + $b; }

const ITERATIONS = 10_000_000;

$start = hrtime(true);
for ($i = 0; $i < ITERATIONS; $i++) {
    add(1, 2);
}
$strict = hrtime(true) - $start;

echo "strict: {$strict}ns ({$strict / ITERATIONS}ns/call)\n";
```

```bash
# strict_types=1 (в файле)
# → ~2.5ns/call

# strict_types=0 (убрать declare)
# add("1", "2") — string → int coercion
# → ~3.5ns/call

# Разница: ~40% на ОДИН вызов
# Но: 10M вызовов × 1ns = 10ms на весь бенчмарк
# Для реального запроса (100 вызовов): 100ns = 0.0001% запроса
```

**Вывод:** на один вызов — разница видна. На запрос — нет.

---

### Когда strict_types «заметен»

```
✅ Заметен:
  - Микробенчмарк (10M+ вызовов одной функции)
  - Hot loop в CLI-инструменте (процессор 100%)
  - Сравнение двух реализаций с одинаковой нагрузкой

❌ Не заметен:
  - Веб-запрос (БД dominates)
  - API-запрос (сериализация dominates)
  - Worker-процесс (БД + очередь dominate)
  - Любое реальное приложение
```

---

### Итого

```
Вопрос:  "На сколько strict_types замедляет?"
Ответ:   "Ни на сколько заметное. < 0.5% в реальных приложениях.
         В синтетике — до 5–15%, но это не реальные нагрузки."

Почему:  strict не добавляет проверок. Он убирает приведения.
         Overhead — 1–10 нс на вызов. 100 вызовов = 1 мкс.
         Запрос = 50 мс. Значит: 0.002%.

Вывод:   Не стоит выбирать между производительностью и типобезопасностью.
         strict_types=1 — бесплатная типобезопасность.
         Если приложение тормозит — ищите N+1, reflection, БД.
```

> ⏱ 18.7s · prompt 99209 tok · output 1875 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о том, почему Doctrine ORM не поддерживает составные (composite) первичные ключи и как с этим работают в реальных проектах. Это вопрос на стыке DDD, ORM и практик проектирования БД.

Важно уточнить: на самом деле Doctrine **поддерживает** составные ключи — есть `@ORM\Id` на нескольких полях, `@ORM\Embedded`, ассоциации со composite key. Но поддержка **хромая**: ассоциации (ManyToOne, ManyToMany) с сущностями, у которых composite key — работают плохо, lazy loading сломан, identity map некорректен.

Нужно:
1. Честно сказать, что формально поддержка есть, но она хромая
2. Объяснить, почему (интервал: identity map, lazy loading, equality по id)
3. Показать, как живут в реальных проектах (surrogate keys, join-таблицы с surrogate, денормализация)
4. Привести конкретные примеры

Структура ответа:
- Честный разбор: что поддерживается, что нет
- Почему (внутренняя механика Doctrine: identity map, lazy loading, proxy)
- Типовые сценарии с composite key
- Как живут: 4 подхода
- Сравнение и рекомендация

Пишу на русском, в том же стиле, что и предыдущие ответы — с таблицами, кодом, trade-offs. Не раздуваю, но покрываю вопрос полностью.

</details>

**Кандидат:**

## Composite Key в Doctrine: что реально работает, что нет

### Сначала честный разбор

Doctrine **формально поддерживает** composite key. Но поддержка **хромая**. Вот что работает, а что нет:

| Возможность | Работает? | Нюанс |
|-------------|-----------|-------|
| `@ORM\Id` на 2+ полях | ✅ | Сам composite key создаётся |
| SELECT по composite key | ✅ | `em->find()` с array |
| UPDATE/DELETE по composite key | ✅ | Работает |
| **ManyToOne на сущность с composite key** | ⚠️ | Lazy loading сломан |
| **ManyToMany с composite key** | ⚠️ | Proxy не работает корректно |
| **Identity map** | ⚠️ | Равенство по id — через array, не по ссылке |
| **Lazy loading (proxy)** | ❌ | Proxy для composite key — боль |
| **Cascade + orphanRemoval** | ⚠️ | Работает, но хрупко |
| **DQL JOIN** | ⚠️ | Ограничения в синтаксисе |

---

### Почему хромая: внутренняя механика

#### 1. Identity Map

Doctrine хранит загруженные сущности в identity map по ключу:

```php
// Для simple key:
$identityMap[Order::class]['ord-123'] = $orderObject;

// Для composite key:
$identityMap[JoinTable::class]['[customer_id: "c1", sku: "ABC"]'] = $joinObject;
```

Проблема: **равенство по array-ключу**. PHP array-ключи хешируются. Если ключ — `[1, 'ABC']` vs `[1, 'ABC']` — совпадает. Но если `1` (int) vs `'1'` (string) — **не совпадает**. И вы получаете два объекта в identity map, которые «один и тот же».

```php
// Баг-сценарий:
$row1 = ['customer_id' => 1, 'sku' => 'ABC'];   // int
$row2 = ['customer_id' => '1', 'sku' => 'ABC']; // string

// Identity map хранит ОБА.
// $row1 !== $row2 по ссылке, но это «одна и та же» строка.
// → Дубль в коллекции, потеря dirty-checking.
```

#### 2. Lazy Loading / Proxy

Doctrine создаёт proxy (заглушку) для lazy-ассоциаций:

```php
// Для simple key:
class OrderProxy extends Order {
    private ?string $id = null;  // ← один скаляр, легко сравнить
    public function __load() { /* fetch by id */ }
}

// Для composite key:
class JoinProxy extends Join {
    private ?int $customerId = null;
    private ?string $sku = null;  // ← два поля, сравнение сложнее
    public function __load() { /* fetch by (id, sku) */ }
}
```

Проблема: proxy сравнивает **все** поля ключа. Если один не совпал — это «другая» сущность. Но в коллекциях `Collection` сравнение идёт по `===` (ссылка), и proxy ≠ real object до `__load()`.

Результат:

```php
$join = $order->getJoin();  // proxy, не загружен
$join->getCustomer();       // триггерит __load()
$join === $order->getJoin(); // FALSE! (proxy ≠ real, different reference)
// → «дубли» в коллекции
```

#### 3. Equality

```php
// Simple key:
$a = new Order('id-1');
$b = new Order('id-1');
$a === $b;  // FALSE (разные объекты)
$a->id === $b->id;  // TRUE (один id)

// Composite key:
$a = new Join('c1', 'ABC');
$b = new Join('c1', 'ABC');
$a === $b;  // FALSE
$a->customerId === $b->customerId && $a->sku === $b->sku;  // TRUE
// Но Doctrine сравнивает по array, и int vs string ломает это.
```

---

### Типовые сценарии с composite key

#### a) Связка «заказ — позиция» (Order → OrderLine)

```
orders (id)
order_lines (order_id, sku)  ← composite key
```

Здесь composite key **естественный**: позиция определяется парой (заказ, sku).

#### b) Join-таблица ManyToMany

```
users (id)
roles (id)
user_roles (user_id, role_id)  ← composite key
```

#### c) Связка «контрагент — позиция в договоре»

```
contracts (id)
contract_items (contract_id, item_code)  ← composite key
```

---

### Как живут в реальных проектах

#### Подход 1: Surrogate Key (самый частый)

Добавляем `id SERIAL` и забываем про composite key.

```sql
CREATE TABLE order_lines (
    id BIGSERIAL PRIMARY KEY,        ← surrogate
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    UNIQUE (order_id, sku)           ← бизнес-уникальность
);
```

```php
#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    private Order $order;

    #[ORM\Column(type: Types::STRING)]
    private string $sku;
}
```

| Плюс | Минус |
|------|-------|
| Doctrine работает идеально | Лишний столбец (8 байт) |
| Lazy loading, identity map — ок | `id` не несёт смысла |
| Простые ассоциации | Нужно поддерживать UNIQUE (order_id, sku) |
| Быстрее (INT PK vs UUID+VARCHAR) | |

**Когда:** 90% случаев. Order → OrderLine, User → Role, Contract → Item.

---

#### Подход 2: Embedded (компоненты ключа)

Компоненты composite key — отдельные сущности.

```sql
CREATE TABLE order_lines (
    order_id UUID NOT NULL REFERENCES orders(id),
    sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    PRIMARY KEY (order_id, sku)
);
```

```php
// OrderLine — без своего id. Компоненты — Order (ManyToOne) + Sku (embedded).
#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLine
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(name: 'order_id', onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $sku;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;
}
```

| Плюс | Минус |
|------|-------|
| Нет лишнего столбца | Lazy loading на Order — боль |
| Semantic: sku — часть ключа | Proxy на OrderLine — хрупкий |
| | DQL: `WHERE o.lines.sku = :sku` — работает, но не всегда |

**Когда:** когда sku — **сам по себе** сущность (кatalog), и OrderLine — «связка». Не подходит, если OrderLine — полноценная сущность со своим жизненным циклом.

---

#### Подход 3: Денормализация

Убираем join-таблицу, встраиваем данные.

```sql
-- Было:
users (id)
roles (id)
user_roles (user_id, role_id)

-- Стало:
users (id, roles JSONB)  ← ['admin', 'editor']
```

```php
#[ORM\Entity]
class User
{
    #[ORM\Id]
    private string $id;

    /** @var Role[] */
    #[ORM\Column(type: 'json')]
    private array $roles = [];
}
```

| Плюс | Минус |
|------|-------|
| Нет composite key | Нет FK на roles (целостность в коде) |
| Быстрее (1 SELECT) | Миграции сложнее |
| Проще Doctrine | Репликация: JSONB — не индексируется по ролям |

**Когда:** роли, теги, категории — «множество», а не «связь».

---

#### Подход 4: Отдельная сущность + repository

Если composite key — **доменная необходимость** (например, (customer_id, sku) определяет цену), но сущность должна быть самостоятельной:

```php
// Domain: PricePoint — VO, не entity
final readonly class PricePoint
{
    public function __construct(
        public CustomerId $customerId,
        public Sku $sku,
        public Money $price,
    ) {}
}

// Persistence: отдельная таблица с surrogate key
CREATE TABLE price_points (
    id BIGSERIAL PRIMARY KEY,
    customer_id UUID NOT NULL,
    sku VARCHAR(100) NOT NULL,
    price_cents INT NOT NULL,
    UNIQUE (customer_id, sku)
);

// Repository: работает по (customer_id, sku), но хранит id
final class PriceRepository
{
    public function find(CustomerId $cid, Sku $sku): PricePoint
    {
        $row = $this->em->createQuery(
            'SELECT p FROM PricePoint p WHERE p.customerId = :c AND p.sku = :s'
        )->setParameter('c', $cid->value())
         ->setParameter('s', $sku->value())
         ->getOneOrNullResult();

        if ($row === null) {
            throw new PriceNotFound($cid, $sku);
        }

        return new PricePoint($row->customerId, $row->sku, $row->price);
    }
}
```

| Плюс | Минус |
|------|-------|
| Domain-модель чистая (VO) | Два класса (entity + domain) |
| Composite key в БД (semantic) | Маппинг в репозитории |
| Surrogate key для Doctrine | |

**Когда:** цена, ставка, тариф — «зависимость» от пары ключей.

---

### Сравнение подходов

| Критерий | Surrogate | Embedded | Денормализация | VO + Repo |
|----------|-----------|----------|----------------|-----------|
| Doctrine happy | ✅ | ⚠️ | ✅ | ✅ |
| Lazy loading | ✅ | ❌ | ✅ | ✅ |
| Semantic PK | ❌ | ✅ | ❌ | ⚠️ |
| Миграции | Простые | Простые | Сложные | Простые |
| Целостность БД | FK + UNIQUE | FK | Нет FK | FK + UNIQUE |
| Производительность | ✅ | ⚠️ | ✅ | ✅ |
| Сложность кода | Низкая | Средняя | Низкая | Средняя |

---

### Рекомендация

```
Order → OrderLine
    → Surrogate key (id BIGSERIAL)
    → UNIQUE (order_id, sku)
    → Doctrine happy, lazy loading ok

User → Roles
    → Денормализация (roles JSONB)
    → Или surrogate key (user_roles.id)
    → Не composite key

Price (customer, sku) → Money
    → Surrogate key + UNIQUE (customer_id, sku)
    → Domain: PricePoint (VO)
    → Repo: find by (customer, sku)

Contract → Items
    → Surrogate key (id)
    → UNIQUE (contract_id, item_code)
    → Если Item — самостоятельная сущность → Embedded
```

**Правило:** если сущность **существует сама по себе** (свои поля, свой lifecycle) — surrogate key. Если сущность — **связка** (только ключи) — embedded или денормализация. Composite PK — только если **БД требует** (например, sharding по composite key).

---

### Итого

```
Doctrine composite key:
  ✅ Формально поддерживается
  ❌ Lazy loading — хрупкий
  ❌ Identity map — int vs string bug
  ⚠️  DQL — ограниченный

В реальных проектах:
  → Surrogate key (90% случаев)
  → Embedded (когда компонент — сущность)
  → Денормализация (когда «связь» — на самом деле множество)
  → VO + Repository (когда composite key — доменное понятие)

Композитный PK в БД — это норм.
Композитный PK в Doctrine — это боль.
Решение: surrogate key + UNIQUE constraint.
```

> ⏱ 30.5s · prompt 100683 tok · output 3338 tok

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

Пользователь просит разобрать плохой код контроллера и переделать. Это классический "антипаттерн-контроллер" в одном методе. Нужно:
1. Перечислить проблемы (безопасность, архитектура, читаемость, надёжность).
2. Показать, как переделал бы (по слоям: Domain / Application / Infrastructure).
3. Объяснить, с чего начать и почему (приоритизация: безопасность в первую очередь — SQL-инъекция — это P0).

Приоритеты проблем:
- P0: SQL-инъекция (безопасность, деньги/данные).
- P1: Отсутствие транзакции, потеря idempotency, mail в request cycle.
- P1: Отсутствие аутентификации (кто оплачивает? чей заказ?).
- P2: Жёсткая связка с PDO, статический Mailer/Logger.
- P2: Нет валидации id, нет статуса, нет доменных инвариантов (можно оплатить уже оплаченный).
- P3: Стиль, типизация, строгие типы, readonly.

С чего начать: безопасность (SQL-инъекция) — это критично. Потом аутентификация (без неё любой может оплатить чужой заказ). Потом транзакция и домен. Потом слои.

Структурирую ответ: список проблем по приоритету, потом рефакторинг слоями, потом "с чего начать" — обоснование. Не делать слишком длинно, но полностью. Использую таблицу проблем и код рефакторинга.

Ответ должен быть на русском, самодостаточным.

</details>

**Кандидат:**

## Разбор: от «что сломано» к «как переделать»

### Проблемы — по приоритету

| # | Проблема | Уровень | Почему |
|---|----------|---------|--------|
| 1 | **SQL-инъекция** | P0 | `$id` идёт прямо в SQL. `id=1; DROP TABLE orders` — и всё. |
| 2 | **Нет аутентификации** | P0 | Любой может оплатить **чужой** заказ. |
| 3 | **Нет доменного инварианта** | P1 | Можно оплатить уже оплаченный/отменённый заказ. |
| 4 | **Нет транзакции** | P1 | UPDATE прошёл, mail упал → статус «paid», но пользователь не уведомлён. Или mail ушёл, а UPDATE откатился. |
| 5 | **Mail в request-цикле** | P1 | Если Mailer 3с — пользователь ждёт 3с. Если упал — 500. |
| 6 | **Нет idempotency** | P1 | Двойной клик / retry webhooks → двойной «paid». |
| 7 | **Статические `Mailer`, `Logger`** | P2 | Невозможно протестировать, невозможна замена. |
| 8 | **`SELECT *` + ручной UPDATE** | P2 | Две операции, которые должны быть одной. Риск рассинхрона. |
| 9 | **Нет типизации** | P3 | `$req->get()` → `mixed`. Опечатка = тихий баг. |
| 10 | **Один метод на всё** | P3 | 20 строк, 4 ответственности. SRP нарушен. |

---

### С чего начать и почему

**Не с «красивого кода».** Не с «разбиения на слои».

**С P0.**

```
1. SQL-инъекция → параметризованный запрос. 10 минут. Убирает критическую дыру.
2. Аутентификация → только владелец может оплатить свой заказ. 30 минут.
3. Доменный инвариант → можно оплатить только из статуса 'pending'.
4. Транзакция → UPDATE + mail-событие атомарно (outbox).
5. Mail → асинхронно (очередь).
6. Слои → только после того, как логика корректна.
```

**Почему такой порядок:** если начать с «разбить на слои» и не починить SQL-инъекцию — я получу **красивый** код, который всё ещё позволяет слить БД. Безопасность > архитектура > стиль.

---

### Рефакторинг

#### Домен (правила)

```php
// Domain/Order/Order.php
final class Order
{
    public function __construct(
        private readonly OrderId $id,
        private CustomerId $customerId,
        private Email $customerEmail,
        private OrderStatus $status,
    ) {}

    public function pay(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new InvalidPaymentState($this->id, $this->status);
        }

        $this->status = OrderStatus::Paid;

        $this->record(new OrderPaid($this->id, \DateTimeImmutable::now()));
    }

    public function id(): OrderId { return $this->id; }
    public function status(): OrderStatus { return $this->status; }

    /** @return DomainEvent[] */
    public function releaseEvents(): array
    {
        $e = $this->domainEvents;
        $this->domainEvents = [];
        return $e;
    }

    private function record(DomainEvent $e): void
    {
        $this->domainEvents[] = $e;
    }
}
```

```php
// Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
```

```php
// Domain/Order/Exception/InvalidPaymentState.php
final class InvalidPaymentState extends DomainException
{
    public function __construct(OrderId $id, OrderStatus $status)
    {
        parent::__construct(sprintf(
            'Cannot pay order %s in status %s',
            $id->value(), $status->value,
        ));
    }
}
```

#### Application (оркестрация)

```php
// Application/Order/PayOrder.php
final class PayOrder
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly Outbox $outbox,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(OrderId $id, CustomerId $currentCustomer): Order
    {
        $order = $this->orders->findById($id);

        // ← Доменный инвариант: только владелец
        if (!$order->customerId()->equals($currentCustomer)) {
            throw new OrderNotFound($id);  // не раскрываем, что чужой
        }

        // ← Доменный инвариант: только из pending
        $order->pay();

        // ← Атомарно: агрегат + событие в outbox
        $this->orders->saveWithoutFlush($order);
        foreach ($order->releaseEvents() as $event) {
            $this->outbox->append(Order::class, $order->id()->value(), $event);
        }
        $this->em->flush();  // ← ОДИН commit: order + outbox вместе

        return $order;
    }
}
```

#### Infrastructure — репозиторий (без SQL-инъекции)

```php
// Infrastructure/Persistence/Order/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(OrderId $id): Order
    {
        /** @var OrderEntity $entity */
        $entity = $this->em->find(OrderEntity::class, $id->value());

        if ($entity === null) {
            throw new OrderNotFound($id);
        }

        return Order::fromEntity($entity);
    }

    public function saveWithoutFlush(Order $order): void
    {
        /** @var OrderEntity $entity */
        $entity = $this->em->find(OrderEntity::class, $order->id()->value());
        $entity->setStatus($order->status());
        // flush — делает use-case (атомарно с outbox)
    }
}
```

#### Infrastructure — HTTP (тонкий)

```php
// Infrastructure/Http/Controller/OrderController.php
#[Route('/api/orders/{id}/pay', methods: ['POST'], name: 'order_pay')]
final class OrderController
{
    public function __construct(
        private readonly PayOrder $payOrder,
        private readonly Security $security,
    ) {}

    public function pay(
        string $id,
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser()
            ?? throw new AccessDeniedException('Auth required');

        $order = ($this->payOrder)(
            id: new OrderId($id),
            currentCustomer: new CustomerId($user->getCustomerId()),
        );

        return new JsonResponse([
            'id' => $order->id()->value(),
            'status' => $order->status()->value,
        ]);
    }
}
```

#### Mail — асинхронно, отдельный обработчик

```php
// Infrastructure/Message/Handler/SendPaymentEmail.php
#[AsMessageHandler]
final class SendPaymentEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly OrderRepository $orders,
    ) {}

    public function __invoke(OrderPaid $event): void
    {
        $order = $this->orders->findById($event->orderId);
        $this->mailer->send(
            to: $order->customerEmail()->value(),
            subject: 'Order paid',
            template: 'emails/order_paid.html.twig',
        );
        // Если упало — messenger ретраит. Запрос уже ответил пользователю.
    }
}
```

#### Обработка исключений → HTTP

```php
// Infrastructure/Http/ExceptionListener.php
final class DomainExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getException();

        if (!$e instanceof DomainException) {
            return;
        }

        $event->setResponse(new JsonResponse(
            ['error' => $e->getMessage()],
            match (true) {
                $e instanceof OrderNotFound         => 404,
                $e instanceof InvalidPaymentState   => 409,
                $e instanceof AccessDeniedException => 403,
                default                             => 422,
            },
        ));
    }
}
```

---

### Что изменилось — сводка

| Проблема (было) | Решение (стало) |
|-----------------|-----------------|
| SQL-инъекция | Параметризованный запрос через Doctrine |
| Нет аутентификации | `Security->getUser()` + проверка владельца |
| Можно оплатить чужой/не-pending | Доменные инварианты в `Order::pay()` |
| Mail блокирует ответ | Асинхронный handler через очередь |
| Нет атомарности | Outbox: order + событие в одном commit |
| Статические `Mailer`, `Logger` | DI, интерфейсы из домена |
| `SELECT *` + `UPDATE` | Одна операция через репозиторий |
| Нет типов | `OrderId`, `OrderStatus`, `Email` — VO |
| 1 метод на всё | Домен + use-case + контроллер + handler |

---

### Почему именно так, а не «просто починить инъекцию»

```
Если починить только SQL-инъекцию:
  ✅ Безопаснее
  ❌ Но: любой может оплатить чужой заказ
  ❌ Но: можно оплатить отменённый
  ❌ Но: mail блокирует ответ
  ❌ Но: нет тестов (Mailer статический)
  ❌ Но: через неделю кто-то добавит 3 строки и всё сломается

Если починить всё по слоям:
  ✅ Безопасно (инъекция + авторизация + инварианты)
  ✅ Надёжно (outbox, асинхронный mail)
  ✅ Тестируемо (0 mock'ов в домене)
  ✅ Поддерживаемо (каждый слой < 30 строк)
  ✅ Расширяемо (новый статус = enum + ветка в pay())
```

**Принцип:** рефакторинг начинается с **самой опасной** проблемы, а не с самой «некрасивой». Безопасность и корректность — до архитектуры. Архитектура — до стиля.

> ⏱ 26.5s · prompt 103895 tok · output 2945 tok

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

We need answer in Russian. User asks: Look at domain entity code with EntityManager in constructor and flush inside method. What's wrong DDD/clean architecture, fix. Need analyze deeply. Points: dependency inversion, domain depends on infrastructure (Doctrine), side effects in domain method, hidden persistence, transaction boundaries, testability, encapsulation, mutable email without validation, no domain event, entity has ORM attributes (persistence anemic? Entity has no id? no readonly? no invariants), constructor side? EM in constructor makes entity hard to construct, hydration bypasses constructor? Doctrine entities typically need no-args? Here custom constructor with EM impossible for ORM hydration? Actually if entity has custom constructor with non-default params, Doctrine reflection instantiation? With attribute mapping, if constructor has args without default, can fail. It also violates segregation, single responsibility: domain entity responsible for persistence. Clean architecture: Domain layer cannot depend on Infrastructure. Application/Infrastructure handles persistence. Also method flush leaks transaction management; caller cannot batch changes; premature flush; concurrency/lost updates; no atomicity across multiple changes; hides failure; side effect not declared; no domain events; no validation of email (format, uniqueness), no status; email as primitive string instead of VO; mutable with no state machine; equality/identity missing (no id); ORM attribute on domain class couples domain to Doctrine. Also if Doctrine Entity and domain aggregate same? We can separate: domain User with Email VO, id, domain event, invariants; persistence UserEntity and DoctrineUserRepository. Or if pragmatic, at least move flush to repository/use-case, inject repository not EM. Need discuss hydration: constructor with EM incompatible with ORM? Need mention readonly? DDD aggregate root? User maybe aggregate root, email change emits EmailChanged. Also side effect in changeEmail makes method impure; unit tests require EM mock; cannot test domain logic in isolation. Clean arch: dependencies inward. Use case: load, change, save, publish. DB constraints/unique email handled at repository/DB. Need provide refactored code. Maybe include PHP domain User, Email VO, UserChangedEmail event, ports, repository, use-case, entity mapping. Need mention if using Symfony: DI can inject domain object? Better not. For entity with Doctrine, either use domain object + repository or separate persistence model. Also constructor issue: private EM not typed readonly? It is private, but constructor requires EM. Doctrine cannot instantiate unless EM available? It can via factory? But not good. Also flush inside method: if multiple changes, multiple flushes; if exception after, state unclear. If user changes email and then something fails, already flushed. If repository saves many, flush in each method may persist partially. Need discuss DDD: entity identity, invariants. The class lacks id, no equality, no lifecycle. Email as string: invalid email not caught. changeEmail should validate and maybe emit event; but domain shouldn't know DB uniqueness? It can throw if duplicate if repository checks? Better use case checks or DB unique and repository translates. Need maybe mention constructor with EM also violates Law of Demeter? Entity knows about unit of work. Need maybe answer structure: problems by layer, then corrected code. We need be concise but thorough. Also user asks "Как бы ты её исправил?" So present ideal and pragmatic options. Let's craft final in Russian. Ensure not claim Doctrine no composite? irrelevant. Need include code. Need discuss "domain entity" vs "ORM entity". Maybe state: shown code is ORM entity with side effect. DDD: Domain model should be independent from ORM. If want one model, at least no EM and no flush. But better separate.

Potential final outline:
1. Коротко: это не доменная сущность, а ORM-обёртка с побочным эффектом.
2. Проблемы:
- Domain depends on Doctrine (DI violation, architecture, testability, hydration)
- side effect flush in domain method (transaction boundary hidden, partial commits, no atomicity)
- constructor with EM (Doctrine can't hydrate? entity instantiation problems; EM should be application/infra concern)
- no identity (id), no aggregate root; email mutable without invariants
- no validation (Email VO)
- no domain event / notification
- primitive obsession (string email)
- ORM attributes on domain class (coupling)
- method changeEmail returns void but changes and persists; no result, no event.
- no concurrency control.
3. Refactor ideal:
Domain User:
```
final class User {
  private Email $email;
  private array $events = [];
  public function __construct(public readonly UserId $id, Email $email) {}
  public function changeEmail(Email $new): void {
    if ($this->email->equals($new)) return;
    $this->email = $new;
    $this->events[] = new UserEmailChanged(...);
  }
  public function email(): Email { return $this->email; }
  public function releaseEvents(): array {...}
}
```
Email VO validation.
Port: UserRepository.
Use case:
```
final class ChangeUserEmail {
  __construct(UserRepository, Outbox, EM)
  __invoke(UserId, Email) { $user=repo.find; if not exists?; $user->changeEmail(email); repo.saveWithoutFlush; outbox append events; em.flush; }
}
```
Infra entity:
```
#[ORM\Entity] class UserEntity { id, email; constructor no args; setters; }
```
Repository maps.
4. Pragmatic minimal fix if keep one model:
```
final class User { public string $email; public function changeEmail(Email $email): void { $this->email=$email; } }
```
And use-case/repository flush. But still no id.
Need mention Doctrine constructor: if one model, Doctrine typically requires no-arg or default args for hydration. Better factory/repo. Maybe show with Doctrine's `#[ORM\Entity]` separate.
Need maybe include note about email uniqueness: DB unique + repository catch UniqueConstraintViolationException -> DuplicateEmail.
Need final answer with code and why.

</details>

**Кандидат:**

## Что не так

Этот класс — не «доменная сущность с небольшой проблемой». Это **ORM-объект со скрытым побочным эффектом**, который одновременно:

1. нарушает чистую архитектуру,
2. нарушает DDD,
3. ломает тестирование,
4. ломает управление транзакциями.

Разберём по слоям.

---

### 1. Домен зависит от инфраструктуры

```php
private EntityManagerInterface $em
```

Это нарушение **Dependency Inversion Principle** и **Clean Architecture**.

Доменное правило «пользователь сменил email» не должно знать:

- где хранятся данные (PostgreSQL, MySQL, Redis, файл),
- через ORM или raw SQL,
- как работает `EntityManager`,
- когда происходит `flush`.

Сейчас у вас:

```
Domain → Doctrine
```

А должно быть:

```
Application/Infrastructure → Domain
```

Домен может знать про `Email`, `UserId`, `UserEmailChanged`.  
Он не должен знать про `EntityManagerInterface`.

---

### 2. Доменная сущность делает побочный эффект

```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();
}
```

Метод доменной сущности:

- меняет состояние,
- **и одновременно сохраняет его в БД**.

Это плохо.

Доменная сущность должна описывать **бизнес-состояние**, а не инфраструктурные действия.

Сохранение — это решение уровня use-case / application / infrastructure.

---

### 3. `flush()` внутри сущности ломает атомарность

Представим, что есть use-case:

```php
$user->changeEmail('new@example.com');
$user->updateName('Иван');
$account->block();
```

Если каждый метод внутри сущности сам вызывает `flush()`, вы получаете несколько промежуточных коммитов.

А может быть нужно:

```php
$user->changeEmail(...);
$account->block();
$repository->save($user);
$em->flush();
```

Тогда либо всё коммитится, либо ничего.

Сейчас у вас транзакционная граница спрятана внутрь домена. Это плохо управляется.

---

### 4. Сущность становится не тестируемой без инфраструктуры

Чтобы протестировать:

```php
$user->changeEmail('test@example.com');
```

вам нужен:

- `EntityManagerInterface`,
- БД или мок,
- правильная конфигурация Doctrine,
- возможно, схема БД.

А тест должен быть таким:

```php
$user = new User(new UserId('1'), new Email('old@example.com'));
$user->changeEmail(new Email('new@example.com'));

assertThat($user->email()->value(), 'new@example.com');
```

Без Doctrine, без БД, без Symfony.

---

### 5. Конструктор с `EntityManagerInterface` ломает работу с ORM

```php
public function __construct(
    private EntityManagerInterface $em,
    private string $email,
) {}
```

Doctrine часто создаёт объекты через reflection. Если у сущности сложный обязательный конструктор, это создаёт проблемы:

- `EntityManager` должен быть доступен в момент гидратации,
- объект сложно создать вручную,
- сущность превращается в объект, привязанный к окружению.

Доменная сущность должна создаваться легко:

```php
new User(
    id: new UserId('1'),
    email: new Email('a@b.c'),
);
```

---

### 6. `email` — примитив, а не value object

```php
private string $email
```

Этого недостаточно.

Email — это доменное понятие со своими инвариантами:

- должен быть валидным,
- может нормализоваться,
- может сравниваться по значению,
- может иметь уникальность.

Лучше:

```php
new Email('user@example.com')
```

а не:

```php
'user@example.com'
```

---

### 7. Нет identity

Сущность без идентификатора — это уже почти value object, а не entity.

В DDD:

- **Entity** определяется идентичностью: `UserId`, `OrderId`, `CustomerId`.
- **Value Object** определяется значением: `Email`, `Money`, `Address`.

У вас:

```php
class User
```

но нет:

```php
private UserId $id;
```

Поэтому невозможно корректно отвечать на вопрос:

> это тот же пользователь или другой?

---

### 8. Нет событий и явных инвариантов

Когда пользователь меняет email, это важный доменный факт:

```php
UserEmailChanged
```

Но сейчас это событие «спрятано» внутри `flush()`.

Внешний код не знает:

- что именно произошло,
- нужно ли отправить письмо,
- нужно ли пересоздать индекс,
- нужно ли обновить SSO,
- нужно ли записать в аудит.

---

## Как исправить

Идеальный вариант: разделить **доменную модель** и **persistence-модель**.

---

# 1. Доменная сущность

```php
// src/Domain/User/User.php
namespace App\Domain\User;

final class User
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    public function __construct(
        private readonly UserId $id,
        private Email $email,
    ) {}

    public function changeEmail(Email $email): void
    {
        if ($this->email->equals($email)) {
            return;
        }

        $this->email = $email;

        $this->record(
            new UserEmailChanged(
                userId: $this->id,
                newEmail: $email,
                at: \DateTimeImmutable::now(),
            ),
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    /** @return DomainEvent[] */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function record(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
}
```

---

# 2. Value Object

```php
// src/Domain/User/Email.php
namespace App\Domain\User;

final readonly class Email
{
    public function __construct(
        public string $value,
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \App\Domain\Common\Exception\InvalidEmail($value);
        }

        $this->value = strtolower($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

```php
// src/Domain/User/UserId.php
namespace App\Domain\User;

final readonly class UserId
{
    public function __construct(
        public string $value,
    ) {}

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

---

# 3. Доменное событие

```php
// src/Domain/User/Event/UserEmailChanged.php
namespace App\Domain\User\Event;

final readonly class UserEmailChanged
{
    public function __construct(
        public UserId $userId,
        public Email $newEmail,
        public \DateTimeImmutable $at,
    ) {}
}
```

---

# 4. Порт: репозиторий

Важно: интерфейс репозитория знает про доменную сущность, но **не знает про Doctrine**.

```php
// src/Domain/User/Port/UserRepository.php
namespace App\Domain\User\Port;

interface UserRepository
{
    public function findById(UserId $id): User;

    public function save(User $user): void;
}
```

---

# 5. Use-case

Транзакция и сохранение — здесь.

```php
// src/Application/User/ChangeUserEmail.php
namespace App\Application\User;

use App\Domain\User\Email;
use App\Domain\User\Event\UserEmailChanged;
use App\Domain\User\Port\UserRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

final class ChangeUserEmail
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(UserId $id, Email $newEmail): User
    {
        $user = $this->users->findById($id);

        $user->changeEmail($newEmail);

        foreach ($user->releaseEvents() as $event) {
            // Можно:
            // - отправить в outbox
            // - сохранить в event store
            // - закинуть в message bus
            // здесь показана упрощённая схема
        }

        $this->users->save($user);

        return $user;
    }
}
```

Если вы хотите строгую атомарность «сменили email + записали событие», используйте outbox:

```php
// Pseudocode
BEGIN;
UPDATE users SET email = ... WHERE id = ...;
INSERT INTO outbox (user_id, event, payload) VALUES (...);
COMMIT;
```

---

# 6. Persistence-модель

Doctrine-сущность отделена от доменной.

```php
// src/Infrastructure/Persistence/User/UserEntity.php
namespace App\Infrastructure\Persistence\User;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class UserEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    private string $email;

    public function __construct()
    {
        // пустой конструктор для Doctrine
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
```

---

# 7. Репозиторий

Здесь происходит маппинг и сохранение.

```php
// src/Infrastructure/Persistence/User/DoctrineUserRepository.php
namespace App\Infrastructure\Persistence\User;

use App\Domain\User\Email;
use App\Domain\User\Port\UserRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(UserId $id): User
    {
        /** @var UserEntity|null $entity */
        $entity = $this->em->find(UserEntity::class, $id->value());

        if ($entity === null) {
            throw new \App\Domain\User\Exception\UserNotFound($id);
        }

        return new User(
            id: new UserId($entity->getId()),
            email: new Email($entity->getEmail()),
        );
    }

    public function save(User $user): void
    {
        /** @var UserEntity|null $entity */
        $entity = $this->em->find(UserEntity::class, $user->id()->value());

        if ($entity === null) {
            $entity = new UserEntity();
            $entity->setId($user->id()->value());
        }

        $entity->setEmail($user->email()->value());

        $this->em->persist($entity);
        $this->em->flush();
    }
}
```

---

# 8. Контроллер / адаптер

Тонкий адаптер. Без бизнес-логики.

```php
// src/Infrastructure/Http/Controller/UserController.php
namespace App\Infrastructure\Http\Controller;

use App\Application\User\ChangeUserEmail;
use App\Domain\User\Email;
use App\Domain\User\UserId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserController
{
    public function __construct(
        private readonly ChangeUserEmail $changeUserEmail,
    ) {}

    #[Route('/api/users/{id}/email', methods: ['PATCH'])]
    public function changeEmail(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = ($this->changeUserEmail)(
            id: new UserId($id),
            newEmail: new Email($data['email'] ?? ''),
        );

        return new JsonResponse([
            'id' => $user->id()->value(),
            'email' => $user->email()->value(),
        ]);
    }
}
```

---

## Минимальный ремонт, если не хочется разводить две модели

Если по какой-то причине вы хотите оставить одну сущность, хотя бы уберите из неё `EntityManager` и `flush()`.

Было:

```php
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
}
```

Станет:

```php
final class User
{
    public function __construct(
        private readonly UserId $id,
        private Email $email,
    ) {}

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
```

А сохранение переносится наружу:

```php
$user = $repository->findById($id);
$user->changeEmail(new Email($requestEmail));
$repository->save($user);
```

Или в use-case:

```php
final class ChangeUserEmail
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function __invoke(UserId $id, Email $email): User
    {
        $user = $this->users->findById($id);
        $user->changeEmail($email);
        $this->users->save($user);

        return $user;
    }
}
```

Но это всё ещё упрощённый вариант. Лучше — доменная модель отдельно и persistence-модель отдельно.

---

## Итог: что именно плохо

| Проблема | Почему плохо |
|---|---|
| `EntityManagerInterface` в доменной сущности | Домен зависит от инфраструктуры |
| `flush()` внутри метода | Скрытая транзакция, нет атомарности |
| Конструктор с `EntityManager` | Сущность трудно создать и тестировать |
| `string $email` | Нет инвариантов email |
| Нет `id` | Нарушена природа entity |
| Нет доменных событий | Внешняя система не знает об изменении |
| ORM-атрибуты в доменной сущности | Связывает домен с Doctrine |
| Нет separation of concerns | Одна сущность отвечает за домен, хранение и побочные эффекты |

---

## Главное правило

Доменная сущность отвечает на вопрос:

> **Что происходит с объектом и какие правила должны соблюдаться?**

Репозиторий / use-case отвечает на вопрос:

> **Как и когда сохранить это изменение?**

`EntityManager` не должен быть внутри доменной сущности. Он — инструмент инфраструктуры.

> ⏱ 47.3s · prompt 106614 tok · output 4995 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о моём подходе к решению технической задачи на незнакомом стеке/технологии. Это вопрос на собеседовании о soft skills и инженерных привычках. Нужно ответить так, чтобы показать:
1. Системный подход (не "паника", а алгоритм)
2. Честность о границах (когда просить помощь)
3. Практичность (не абстракции, а конкретные действия)
4. Умение работать в команде (не "я всё сам" и не "сразу бегу к старшему")

Структура ответа:
- Фаза 1: Разведка (первый взгляд)
- Фаза 2: Узкое место (что именно не знаю)
- Фаза 3: Решение (сам или с командой)
- Фаза 4: Оценка сроков
- Когда просить помощь, а когда нет

Важно: не делать из этого "универсальную философию". Дать конкретный, практичный алгоритм с примерами.

Ключевые моменты:
1. Не путать "не знаком с технологией" и "не могу решить задачу". Часто задача решается на уровне абстракции, а технология — деталь.
2. Уметь отличать "нужно 2 часа гуглить" от "нужно 2 дня гуглить".
3. Просить помощь — не слабость. Но просить "спасите" без попытки — да.
4. Оценка сроков: пессимистичная + буфер.
5. Документировать, чтобы в следующий раз было быстрее.

Формат: фазы с конкретными действиями, не абстракции. Плюс чеклист "когда просить/когда нет".

</details>

**Кандидат:**

## Как я решаю задачу на незнакомом стеке

### Общий принцип

Незнакомая технология — это не «задача». Это **деталь**, которую я могу выучить за ограниченное время. Сама задача обычно решается на уровне абстракции, который мне знаком.

```
Незнакомое:  K8s, Terraform, Kafka, gRPC, ClickHouse
Знакомое:    транзакции, очереди, атомарность, HTTP, тесты, git
```

Я решаю знакомое. Незнакомое — выучиваю ровно столько, чтобы не сломать.

---

## Фаза 1: Разведка (0.5–2 часа)

Цель: понять, **что именно** мне нужно, а не учить технологию целиком.

### 1.1 Читаю задачу и формулирую вопрос

```
Задача: «Пользователь не видит данные в дашборде после обновления»

Незнакомое: ClickHouse, Grafana, gRPC-сервис

Вопрос к себе:
  - Это баг или фича?
  - Где «пользователь не видит» — в браузере, в API, в БД?
  - Что изменилось до поломки?
  - Кто может воспроизвести?
```

### 1.2 Смотрю окружение, не документацию

```
❌ Сажусь читать «ClickHouse: Complete Guide»
✅ Открываю:
   - README проекта
   - docker-compose / k8s manifests (как поднимать)
   - config'и (что подключено к чему)
   - последние 5 коммитов (что меняли до поломки)
   - логи (что именно упало)
   - CI/CD pipeline (как деплоится)
```

**Правило:** 80% информации для развёртывания — в репозитории. Не в документации.

### 1.3 Поднимаю локально (или на staging)

```
Цель: не «понять», а «увидеть».
- Команда запускается → я вижу, как это работает.
- Команда не запускается → я вижу, где сломано (это уже диагноз).
```

### 1.4 Формулирую узкое место

```
После 1–2 часов я должен ответить на вопрос:

«Что именно мне НЕ понятно, и без чего я не могу двигаться дальше?»

Примеры:
  ✅ «Не понимаю, как в Kafka работает consumer group.
      Мне нужно это, чтобы найти, почему события теряются.»
  ✅ «Не знаю, как читать trace'и в Jaeger.
      Мне нужно это, чтобы найти, какой микросервис падает.»
  ❌ «Не знаю, как работает Kafka.» (слишком широко)
  ❌ «Не понимаю gRPC.» (слишком широко)
```

**Если не могу сформулировать узкое место за 2 часа — значит, задача больше, чем я думал. Эскалирую.**

---

## Фаза 2: Учение по узкому месту (2–8 часов)

### 2.1 Учу только то, что нужно

```
Не: «Выучить ClickHouse»
А:  «Написать 3 запроса: SELECT, UPDATE, смотреть EXPLAIN»

Не: «Понять K8s»
А:  «Научиться: kubectl logs, kubectl exec, kubectl port-forward»

Не: «Выучить gRPC»
А:  «Написать один клиент, один сервер, поймать один error»
```

### 2.2 Порядок источника информации

```
1. Официальная документация (только нужная глава)
2. Репозиторий проекта (примеры, config'и, тесты)
3. GitHub Issues (кто-то уже задавал этот вопрос)
4. Stack Overflow / Reddit / Discord
5. Спросить в команде (если 1–4 не помогли за 1 час)
```

### 2.3 Письмо, не чтение

```
❌ Читаю 3 часа про Kafka consumer groups
✅ Пишу 20 строк кода: producer → consumer → смотрю, что происходит
   Ломается → читаю error → иду в docs по этому error

Письмо — это обратная связь. Чтение — нет.
```

### 2.4 Чекпоинт каждый час

Каждый час спрашиваю себя:

```
- Я стал ближе к решению?
- Я понимаю, что именно мне нужно дальше?
- Я не трачу время на то, что не нужно?

Если 2 часа подряд без прогресса → меняю подход или прошу помощь.
```

---

## Фаза 3: Решение

### 3.1 Если задача на стыке знакомого и незнакомого

```
Пример: «Добавить идемпотентность в gRPC-сервис на Go»

Знакомое:  идемпотентность (ключ, БД, retry)
Незнакомое: gRPC, Go

Решение:
  1. Понять протокол gRPC (1 час): metadata, status codes, interceptor
  2. Реализовать идемпотентность (4 часа): ключ в metadata,
     проверка в БД, response caching
  3. Написать тесты (2 часа): interceptor test, e2e test

Время: ~7 часов, не 2 дня.
```

### 3.2 Если задача полностью на незнакомой технологии

```
Пример: «Перепишите модуль на Rust»

Честно: это не «задача на незнакомом стеке».
Это «новая технология + новая команда + новый процесс».

Время: не 1 день, а 2–4 недели.
Решение: не «разобраться самому», а:
  1. 1 день: разобраться, что вообще нужно (задача, API, тесты)
  2. 1 день: написать hello world + тест + CI
  3. 3 дня: рефакторинг модуля
  4. 2 дня: ревью, интеграция, деплой

Итого: ~1.5 недели. Не 1 день. Не 1 месяц.
```

---

## Фаза 4: Оценка сроков

### Формула

```
Срок = время_на_разведку + время_на_учение + время_на_реализацию + буфер

где:
  разведка:     0.5–2ч (если задача чёткая)
                1–2д (если задача размытая)
  учение:       2–8ч (узкое место)
                1–3д (если широко)
  реализация:   зависит от задачи
  буфер:        50% от (разведка + учение)
                + 100% если «никогда не видел»
```

### Пример оценки

```
Задача: «Исправить потеря событий в Kafka-пайплайне»
Мой опыт: Kafka — видел, consumer groups — нет.

Разведка:     2ч (логи, config, воспроизвести)
Учение:       4ч (consumer groups, offset management)
Реализация:   4ч (найти баг, починить, тест)
Буфер:        3ч (что-то сломается)

Итого:        ~13ч ≈ 2 рабочих дня

Ответ заказчику:
  «2 рабочих дня. Пессимистично: 3, если будет unexpected.»
```

### Что НЕ включаю в срок

```
❌ Время на ревью (отдельно)
❌ Время на деплой (отдельно)
❌ Время на «а, надо ещё и документацию»
✅ Но включаю: «чтобы я был уверен, что работает»
```

---

## Когда просить помощь, а когда разбираться самому

### Критерий

```
Я прошу помощь, когда:
  - Потратил > 2 часов на одно узкое место без прогресса
  - Узкое место не в моей зоне ответственности (инфра, безопасность)
  - Решение повлияет на других (API, схема БД, инфраструктура)
  - Я не могу сформулировать, что именно не понимаю

Я разбираюсь сам, когда:
  - Потратил < 2 часа (ещё рано просить)
  - Узкое место узкое и конкретное («как работает X»)
  - Решение локальное (только мой модуль)
  - Я могу сформулировать вопрос: «Я сделал A, ожидаю B, получаю C»
```

### Как просить правильно

```
❌ «Я не понимаю, как это работает. Помогите.»
❌ «У меня не работает. Что делать?»
❌ (Молчу 3 дня, а потом: «Я не справился»)

✅ «Я разбираюсь с X. Вот что я понял: [3 пункта].
    Вот где застрял: [конкретное место].
    Вот что я уже пробовал: [2 варианта].
    Вопрос: [один конкретный вопрос].
    Нужна помощь в: [что именно]»
```

**Правило:** если я могу сформулировать «я сделал A, ожидаю B, получаю C» — я почти готов. Если нет — я ещё не доработал вопрос.

### Когда просить «на уровне»

```
Ситуация: задача требует решения на уровне, которого у меня нет.

Пример:
  - Нужно переписать архитектуру (у меня нет опыта)
  - Нужно решить вопрос безопасности (не моя зона)
  - Нужно принять решение по инфраструктуре (не моя зона)

В этом случае: не «разбираюсь сам 2 недели», а
«Я понимаю задачу, но решение требует [X].
 Предлагаю обсудить с [Y]. Могу подготовить 3 варианта.»
```

---

## Антипаттерны

| ❌ | Почему плохо |
|----|-------------|
| 2 дня гуглить, не попробовав | Нет обратной связи. Гугление ≠ понимание. |
| «Я всё сам» 5 дней | Команда не знает, что я застрял. Потеряно 5 дней. |
| Просить помощь сразу | Не дал себе шанс. Команда думает: «А он вообще пытается?» |
| Просить помощь «спасите» | Нет контекста, нет попытки. Помогают, но бездоверчиво. |
| Оценивать срок пессимистично «на всякий» | 3 недели вместо 2. Потеряно доверие. |
| Оценивать срок оптимистично «я быстро» | 1 день вместо 3. Провал. |
| Не документировать, что выучил | В следующий раз — опять 2 дня. |

---

## Итого

```
1. Разведка: 0.5–2ч. Что именно мне нужно?
2. Учение: 2–8ч. Только узкое место. Письмо, не чтение.
3. Решение: зависит от задачи.
4. Оценка: разведка + учение + реализация + буфер 50–100%.

Просить помощь:
  ✅ > 2ч без прогресса на одном узком месте
  ✅ Решение влияет на других
  ✅ Не могу сформулировать вопрос
  ❌ < 2ч (ещё рано)
  ❌ «Я не могу» (не сформулировал что именно)

Формулировка помощи:
  «Я сделал A. Ожидаю B. Получаю C.
   Пробовал X и Y.
   Вопрос: Z.»
```

> ⏱ 41.3s · prompt 110382 tok · output 3073 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь продолжает серию вопросов в формате собеседования на PHP-бэкенд. Вопрос про управление ожиданиями: пообещал срок, понял что не успеваю. Нужно ответить как практик: алгоритм действий, тайминг, формулировка, чего не делать. Без воды, по структуре предыдущих ответов: краткий принцип, конкретные шаги, примеры формулировок, антипаттерны, итог. Ответ на русском.

</details>

**Кандидат:**

## Что делаю, когда понял, что не успею

### Главный принцип

**Рассказать сразу, а не в момент дедлайна.**

Разница между «я не успею, нужен ещё день» и «я не успел, простите» — это не один день работы. Это доверие команды.

---

### Когда именно понимаю, что не успеваю

Не «в конце дня». А в момент, когда могу это **замерить**.

```
❌ «Кажется, будет сложно»
   → Это не факт. Это сомнение. Жду ещё 3 часа.

✅ «Я сделал 40% за 4 часа. Осталось 60%.
    По темпу — ещё 6 часов. Итого 10 часов, а не 6.
    Это факт.»
```

**Критерий:** я могу назвать цифру. «Осталось ~6 часов работы» — это факт. «Кажется, не успею» — это не факт.

---

### Алгоритм

#### Шаг 1: Остановиться и посчитать

```
1. Что уже сделано? (конкретно, не «продвинулся»)
2. Что осталось? (конкретно)
3. Сколько времени займёт каждая часть?
4. Итого: X часов, а не Y часов.
5. Почему? (что я не учёл)
```

#### Шаг 2: Выбрать вариант

```
Вариант A: Сделать всё, но позже (дедлайн +1 день)
Вариант B: Сделать часть к дедлайну, остальное позже
Вариант C: Сделать упрощённую версию к дедлайну

Выбираю тот, который:
  - сохраняет ценность для бизнеса
  - не ломает другие задачи
  - даёт команде возможность перестроиться
```

#### Шаг 3: Рассказать

**Сколько времени до дедлайна?**

```
> 24 часа до дедлайна:
   → Рассказать сегодня. Команда может перестроиться.

8–24 часа до дедлайна:
   → Рассказать сегодня. Ещё можно подстраховаться.

< 8 часов до дедлайна:
   → Рассказать СЕЙЧАС. Даже если «уже поздно».
     Лучше поздно, чем никогда.
```

**Всегда: как только могу назвать цифру.** Не «в конце дня». Не «завтра скажу».

---

### Как формулирую

#### Хорошо

```
Привет,

По задаче #4521 (поиск по заказам):

Сделано:
  ✅ Пагинация
  ✅ Фильтр по дате
  ✅ Тесты (unit + integration)

Осталось:
  ⏳ Поиск по товару (pg_trgm)
  ⏳ Нагрузка-тест

Проблема: pg_trgm на staging показывает П99 = 3.2с.
Цель: ≤ 500мс. Не укладывается.

Оценка: ещё 1 день (оптимизация + альтернативный подход).

Варианты:
  A) Дедлайн +1 день. Всё готово.
  B) Сегодня: пагинация + фильтры (70% ценности).
     Завтра: поиск по товару.
  C) Сегодня: упрощённый поиск (номер + SKU).
     Завтра: расширенный.

Предлагаю B: команда получает работающее ядро сегодня,
поиск по товару — завтра.

Что скажешь?
```

#### Плохо

```
❌ «Кажется, не успею.»
   (Что именно? Почему? Что сделать?)

❌ «Будет сложно.»
   (Сложно — это сколько?)

❌ «Сделаю, как получится.»
   (Это не план. Это безнадёжность.)

❌ Молчать до дедлайна.
   (Команда узнаёт, что всё сломано, в момент, когда
    уже нельзя ничего сделать.)
```

---

### Чего НЕ делаю

| ❌ | Почему плохо |
|----|-------------|
| Молчать до дедлайна | Команда не может перестроиться. Доверие → 0 |
| «Попытаюсь уложиться» | Не решение. Обещание без плана. |
| Делать «всё и сразу» в ночь | Качество падает. Баги на проде. |
| Просить «дайте ещё время» без вариантов | Команда не может принять решение. |
| Обвинять («задачу оценили плохо») | Не помогает. Лучше: «я не учёл X» |
| Делать молча и показать в 23:00 | Ревью в 23:00 = поверхностное. |
| Скрывать, что что-то не работает | «Работает, но медленно» ≠ «работает». |

---

### Почему именно так

```
1. Команда строит план на основе моих обещаний.
   Если я не успеваю — их план ломается.
   Чем раньше я скажу — тем дешевле для них перестроиться.

2. «Не успеваю» — это не провал.
   Это новая информация. Она позволяет принять решение.
   «Молчу» — это провал. Потому что решение принимается на неверных данных.

3. Варианты — это не слабость.
   «У меня есть 3 варианта, предлагаю B» — это профессионализм.
   «Я не знаю, что делать» — это не профессионализм.

4. Честность о времени = доверие.
   Если я 10 раз говорю «не успеваю» и 9 раз прав —
   команде можно верить.
   Если я 1 раз говорю «успею» и не успеваю —
   команде нельзя верить.
```

---

### Итог

```
1. Посчитать: что сделано, что осталось, сколько часов.
2. Выбрать: A / B / C (полный срок / часть / упрощение).
3. Рассказать: как только могу назвать цифру.
4. Предложить варианты, а не проблему.
5. Не молчать. Не оправдываться. Не делать в ночь.

Правило:
  «Не успеваю» + «вот варианты» = профессионализм.
  «Не успеваю» без вариантов = проблема.
  Молчать до дедлайна = потеря доверия.
```

> ⏱ 18.1s · prompt 113109 tok · output 1583 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансе качества и скорости — это классический вопрос для senior/middle уровня на собеседовании. Нужно ответить конкретно, с примерами, без абстрактных «зависит от ситуации». Структура: критерии «достаточно хорошо», конкретные правила, примеры из практики, антипаттерны. Важно показать, что это не «я чувствую», а есть конкретные правила и чеклисты.

</details>

**Кандидат:**

## Когда код «достаточно хорош»

### Главный критерий

Не «я перестал видеть улучшения». Не «мне нравится». А:

> **Код решает задачу так, что через 3 месяца я или коллега сможем его прочитать, понять и изменить без гуглинга.**

Если да — достаточно. Если нет — не достаточно.

---

### Конкретные критерии «достаточно хорошо»

```
✅ Достаточно, если:

1. Задача выполнена
   - Все acceptance criteria покрыты
   - Тесты зелёные
   - Нет P0/P1 багов

2. Код читается за 5 минут
   - Имена говорят что делают
   - Методы < 30 строк
   - Нет «магии» (числа, строки без контекста)
   - Индентификация ≤ 3 уровня

3. Изменения не сломают
   - Новые инварианты → новые тесты
   - Error paths покрыты
   - Нет скрытых побочных эффектов

4. Ревьюер поймёт без объяснений
   - PR-описание: что, почему, как проверить
   - Комментарии объясняют WHY, не WHAT
   - Нет «очевидных» комментов (`i++; // increase i`)

5. Можно откатить
   - Отдельный коммит на каждую логическую единицу
   - Feature flag если риск
   - Роллбек план существует
```

---

### Когда НЕ достаточно

```
❌ Не достаточно, если:

1. Я не могу объяснить в 2 предложениях, что делает функция
2. Тест проходит, но я не знаю, ПОЧЕМУ он проходит
3. Чтобы добавить фичу, нужно менять 5 файлов
4. Код работает, но я не уверен, что он правильный
5. Ревьюер спрашивает «а что если...» и я не знаю ответа
6. Есть дублирование, которое я «потом вынесу»
7. Я оставил TODO, зная, что не вернусь
```

---

## Баланс качество/скорость: конкретные правила

### Правило 1: Качество до merge, не после

```
❌ «Сделаю быстро, потом рефакторю»
   → «Потом» = никогда. Или через 3 месяца, когда забыл контекст.

✅ «Сделаю достаточно хорошо сразу»
   → 30 минут на рефактор до merge = 3 часа через месяц.

Формула:
  1 час рефактора ДО merge = 5 часов ПОСЛЕ merge
  (потому что после — тесты, документация, другие люди знают «старое»)
```

### Правило 2: Не оптимизируй то, что не измерял

```
❌ «Вот тут можно использовать HashMap вместо Array»
   → Я не замерил. Может, разница 0.1мс.

✅ «Замерил: 50мс. Цель 10мс. Разбираю почему.»
   → Профильер показал: 80% времени в JSON decode.
   → Оптимизирую decode, не HashMap.

Правило:
  Оптимизация = замер → гипотеза → изменение → замер.
  Без замера — это «красиво», не «быстро».
```

### Правило 3: Абстракция — только при второй встрече

```
❌ Первая функция с 3 параметрами → делаю класс с интерфейсом
   → Через неделю понимаю, что параметров было 2, а 3-й — случайность.

✅ Первая функция → просто функция.
   Вторая функция с похожей логикой → вот тут выношу общую часть.

Правило:
  Абстракция = повторение, а не предсказание.
  «Может, пригодится» — не причина.
```

### Правило 4: Тесты до того, как код станет сложным

```
❌ Написал 200 строк → «а, надо бы протестировать» → 2 часа
   → Тесты хрупкие, потому что тестирую «всё сразу».

✅ Написал 30 строк → тест → ещё 30 строк → тест
   → Каждый тест маленький, понятный, быстрый.

Правило:
  Если код > 50 строк и нет теста — это не «код», это «долг».
```

### Правило 5: «Достаточно хорошо» ≠ «идеально»

```
Что я НЕ делаю в спринте:
  ❌ Выношу «на всякий случай» новый сервис
  ❌ Добавляю конфигурацию «на будущее»
  ❌ Переписываю чужой код, который работает
  ❌ Добавляю логгирование «на всякий случай»

Что я делаю в спринте:
  ✅ Читаемость: имена, методы < 30 строк
  ✅ Тесты на новые инварианты
  ✅ Error handling на новых путях
  ✅ Один коммит = одна логическая единица

Граница:
  Если улучшение требует > 30 минут и не покрыто тестом
  или не влияет на корректность — это «на потом».
```

---

## Конкретные ситуации

### Ситуация 1: Спринт заканчивается, осталось «мелочь»

```
Осталось:
  ✅ Основная логика
  ✅ Тесты
  ⏳ «Вынести дублирование в helper»

Решение:
  Если дублирование 2 строки — оставляю. Не стоит 30 минут.
  Если дублирование 20 строк — выношу. Иначе через месяц будет 3 копии.

Критерий:
  Дублирование < 5 строк → оставляю.
  Дублирование > 10 строк → выношу.
  Дублирование с бизнес-логикой → всегда выношу (риск рассинхрона).
```

### Ситуация 2: Нашёл баг в чужом коде по дороге

```
Задача: добавить новый метод.
Попутно: нашёл, что существующий метод не проверяет null.

Решение:
  Если null-кейс не блокирует мою задачу:
    → Тикет + упоминание в PR. Не чиню молча.
  Если null-кейс блокирует мою задачу:
    → Чиню. Отдельный коммит. Объясняю в PR.

Критерий:
  Блокирует → чиню.
  Не блокирует → тикет.
```

### Ситуация 3: Ревьюер сказал «можно улучшить»

```
Ревьюер: «Может, лучше сделать через Strategy pattern?»

Ситуация A: моя реализация 20 строк, Strategy = 80 строк + 3 класса
  → «Оставляю как есть. Strategy нужен будет, если появится 3+ варианта.
     Сейчас 2. Через месяц пересмотрим.»
  → Аргумент: YAGNI + 20 строк vs 80.

Ситуация B: моя реализация уже 50 строк, если добавить 3-й вариант будет 150
  → «Согласен. Перепишу через Strategy.»
  → Аргумент: уже растёт, дальше будет больнее.

Критерий:
  Не «а можно лучше?», а «а это нужно сейчас?»
  Если не нужно — не делаю.
  Если нужно — делаю.
```

### Ситуация 4: Дедлайн завтра, код «работает, но я не уверен»

```
❌ «Отдам как есть, раз дедлайн.»
   → На проде будет баг. Исправлять — 2 дня.
   → Лучше потратить 2 часа сейчас.

✅ «Потрачу 2 часа на:
   1. Прочитать код ещё раз (30 мин)
   2. Написать тест на edge case, который я не уверен (30 мин)
   3. Спросить у коллеги, который знает этот модуль (30 мин)
   4. Если всё ок → отдам. Если нет → скажу, что не успеваю.»

Критерий:
  «Не уверен» = не отдам.
  Даже если дедлайн завтра.
  Лучше «не успеваю» сегодня, чем «баг на проде» завтра.
```

### Ситуация 5: Код «красивый», но не покрыт тестами

```
❌ «Оно же работает, зачем тесты?»
   → Завтра кто-то изменит логику и сломает. Никто не знает, что сломалось.

✅ «30 минут на тесты = 2 часа на отладку через месяц.»

Критерий:
  Если код содержит бизнес-логику → тесты обязательны.
  Если код — тонкий адаптер (controller, DTO) → 1 тест на happy path.
  Если код — тривиальный getter/setter → не тестирую.
```

---

## Метрики, которые использую

Не «процент покрытия». А:

```
✅ Сколько времени на ревью?
   < 1 час на 300 строк = код читаемый.
   > 3 часа = код непонятный.

✅ Сколько раз переделал за неделю?
   0–1 = ок.
   3+ = я торопился. Или задача не была ясна.

✅ Сколько багов на проде за месяц?
   0–1 = ок.
   3+ = я пропускаю тесты или edge cases.

✅ Сколько времени на «а, надо бы...»?
   Если > 30 минут на «мелочь» — это не мелочь.
   Либо делаю, либо не делаю.
```

---

## Антипаттерны

| ❌ | Почему плохо |
|----|-------------|
| «Сделаю идеально» | Перфекционизм. Дедлайн сдвинется. Качество не вырастет. |
| «Сделаю быстро» | Долг. Через месяц — 2× дольше. |
| «Оптимизирую на будущее» | YAGNI. Через месяц — не нужно. |
| «Потом рефакторю» | Потом = никогда. |
| «Оно же работает» | Не покрыто тестами = не работает. Просто ещё не сломалось. |
| «Ревьюер сказал, значит, делаю» | Не всегда. Если аргумент слабый — обсуждаю, не делаю. |
| «Все так делают» | Все ≠ правильно. Смотрю на конкретный случай. |

---

## Итого

```
«Достаточно хорошо» =
  Задача выполнена + тесты зелёные
  + Код читается за 5 минут
  + Ревьюер поймёт без объяснений
  + Можно откатить
  + Я уверен, что это работает

«Продолжать улучшать» =
  Я не уверен, что это работает
  + Код не читается
  + Тесты не покрывают edge cases
  + Дублирование с бизнес-логикой

Баланс:
  Качество до merge, не после.
  Абстракция при повторении, не при предсказании.
  Оптимизация по замерам, не по «красивости».
  «Достаточно хорошо» ≠ «идеально».
  «Не уверен» = не отдам. Даже если дедлайн.
```

> ⏱ 29.2s · prompt 114658 tok · output 2768 tok

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

<details><summary>Ход мысли модели</summary>

Пользователь просит дать обратную связь по самому собеседованию, которое мы с ним только что прошли. Это мета-вопрос, и он честный, потому что на реальных технических собеседованиях кандидатам почти никогда не предлагают такую возможность. Мне нужно ответить так, как ответил бы зрелый senior: без хвалы «спасибо, всё было отлично», без претенциозности, конкретно.

Что я реально могу оценить по ходу нашего разговора:

1. Формат — это был не классический round-based интерью, а длинная разговорная сессия «один на один», где интервьюер последовательно углублялся в темы. Это больше похоже на technical design interview + code review + soft skills, слитые в один поток. Плюс — у меня была возможность развернуться и показать глубину. Минус — нет чётких таймингов, нет «бейзинга» через мелкие вопросы перед глубокими.

2. Какие вопросы были точными и полезными:
- «Объясни, как устроена система типов в PHP» — хороший, потому что проверяет не заученные фичи, а понимание механики и ловушек.
- «Чем VO отличается от Entity и как решаешь, что есть что» — проверяет реальное понимание DDD, не заученное.
- «Два процесса одновременно изменяют агрегат» — отличный вопрос, потому что здесь видно, кандидат говорит абстрактно или понимает механику.
- «Посмотри на этот код (OrderController с SQL-инъекцией)» — самый сильный вопрос в серии, потому что он не даёт ответа заранее и проверяет приоритизацию.
- «Задача: пользователи не могут найти заказы» — проверка перевода бизнес-задачи в техническую.
- «Ты не успеваешь — что делаешь» — soft skill, честный.

3. Какие были неудачными или вводящими в заблуждение:
- «FORCE INDEX в PostgreSQL» — это ловушка/костыль. Вопрос не был плохим, но он был чужеродным в серии про PHP. Кандидат может быть силен в PHP, но не знать MySQL — и вопрос создаёт впечатление, что проверяют «всё подряд».
- «На сколько процентов strict_types замедляет» — тоже ловушка. Кандидат может дать честный ответ «статистически незначимо», но это вопрос с подвохом, где «правильного» ответа нет.
- «Составные первичные ключи в Doctrine» — вопрос честный, но он проверяет знание ограничений инструмента, а не понимание. Это скорее «знаешь ли ты факт», чем «умеешь ли ты».
- Вопрос про «какой SOLID чаще нарушается» — слишком открытый для начала, требует от кандидата сразу давать позицию, а не показывать процесс.

4. Что я бы изменил:
- Чёткие тайминги на блок вопросов.
- Один «бейзинг» вопрос в начале, чтобы настроить тон (лёгкий).
- Разделение на «покажи процесс» и «покажи знание» — сейчас они перемешаны.
- Один вопрос с кодом, где я мог бы реально написать код, а не описать его. В нашем разговоре все ответы — описательные, и это не даёт честной картины.
- Один вопрос с trade-off без «правильного ответа» — например, «выберите подход из двух и защитите».
- Возможность задать вопрос в середине («а что если...») — это показывает, что интервьюер слышит.
- Финальный вопрос «что ты бы хотел уточнить по задаче/команде» — этого не было.
- Обратная связь о том, на каком этапе собеседования я нахожусь.

5. Честная часть:
Я отвечал «как идеальный кандидат», а не как реальный человек, который иногда забывает детали. В реальном собеседовании я бы:
- споткнулся бы на FORCE INDEX (если бы не знал контекста)
- на «сколько процентов strict_types» — мог бы дать размытый ответ
- на вопрос о SOLID — мог бы начать с общего и потерять фокус

Мне нужно ответить в том же тоне, что и весь разговор: конкретно, без хвалы, с примерами, с честным «вот что было бы иначе». И важно — не делать вид, что я «кандидат», а реально оценить формат как человек, который участвовал в этом разговоре.

Формат ответа: структура «что понравилось / что нет / что бы изменил / честная часть про себя». Без воды. Примеры конкретных вопросов из нашего разговора.

Длина: средняя, как и в предыдущих ответах. Не слишком длинно — это финальный ответ, и он должен быть собранным.

Язык: русский, как весь разговор.

</details>

**Кандидат:**

## Обратная связь по собеседованию

### Что понравилось

**Формат «разговор, а не опросник».** Вы не шли по чеклисту «PHP → Symfony → DDD → SQL». Каждый следующий вопрос вырос из предыдущего ответа. Это редкость и плюс: кандидат не может заучить ответы — он видит, что собеседник слушает, а не сверяется со списком.

**Вопрос с OrderController** — лучший в серии. Не было подсказки, не было «что тут не так?» в лоб. Просто «посмотри и скажи». Это проверяет приоритизацию (инъекция раньше стиля), честность (найдёшь ли ты P0 или будешь говорить про naming) и глубину (почувствуешь ли ты, что mail в request-цикле — не «мелочь», а P1).

**Вопрос про «не успеваешь»** — честный. Не «а как бы ты справился с давлением?», а конкретная ситуация с конкретным дедлайном. Это отсекает кандидатов, которые умеют говорить про time management в теории, но не в жизни.

**Вопрос про «почему так, а не иначе»** в каждом блоке — сильный приём. Не «опиши, как работает X», а «почему ты выбрал X, а не Y». Это проверяет не знание, а суждение.

---

### Что было бы точнее

**«Нарушение SOLID» как отдельный вопрос.** Он был открытым и требовал сразу дать позицию («SRP чаще всего»). В реальном собеседовании кандидат может замяться, потому что у него нет одной «правильной» позиции — есть контекст. Я бы сделал его иначе: дал бы короткий код (10–15 строк) и спросил «какой принцип нарушен и почему». Тогда кандидат показывает суждение на конкретике, а не в абстракции.

**Вопрос про FORCE INDEX.** Он честный и полезный (проверяет, не подменил ли кандидат PostgreSQL на MySQL в голове). Но в серии, где весь разговор про PHP и DDD, он выглядит чужеродно. Кандидат, который силён в доменной модели, но не работал с MySQL, может провалить этот вопрос — и это скажет мало о его способности писать PHP-код. Я бы перенёс его в отдельный блок «SQL и БД» и сформулировал как «вот запрос тормозит, какие инструменты используешь?» — и FORCE INDEX всплыл бы сам, как одна из опций.

**Вопрос про strict_types «на сколько процентов».** Я отвечал честно: «статистически незначимо, <0.5% в реальных приложениях». Но вопрос сформулирован так, будто есть конкретная цифра. Кандидат, который реально замерял, даст «1–5% в микробенчмарке, <0.5% в проде». Кандидат, который не замерял, может начать гадать «а, процентов десять, наверное?». Разница между этими двумя — не в знании, а в честности. Я бы спросил иначе: «strict_types замедляет код? Если да — в каких сценариях это измеримо?» Это проверяет понимание, а не знание цифры.

**Вопрос про composite key в Doctrine.** Он проверяет знание ограничений инструмента. Но у кандидата, который никогда не работал с composite key, ответ будет «не знаю». А у кандидата, который работал — будет «я использовал surrogate key, потому что...». И в обоих случаях вы понимаете, что человек не умеет проектировать BDD-модель с composite key. Вопрос не отличает «знаю» от «не знаю». Я бы спросил: «У вас есть таблица с composite key. Как бы вы её маппили? Почему?» Это проверяет суждение, а не факт.

---

### Чего не хватило

**Один вопрос «напиши код».** Весь разговор был описательным: я объяснял, как бы сделал, но не писал. Это нормально для senior-level разговора, но не полная картина. Я бы добавил один вопрос вида: «Вот доменная сущность Order. Напишите метод `addLine`, который проверяет: (1) заказ в статусе draft, (2) SKU не дублируется, (3) quantity > 0. Бросайте доменные исключения.» 15 строк кода. Это отсекает кандидатов, которые умеют говорить про DDD, но не умеют писать DDD-код.

**Один вопрос с trade-off без правильного ответа.** Например: «У вас есть два варианта: optimistic locking с retry или pessimistic locking. Конфликт на запись — 1 раз в час. Какой выбираете и почему?» Здесь нет «правильного» ответа. Есть аргументация. Это проверяет зрелость, а не знание.

**Вопрос про команду, а не про технологию.** Весь разговор был про код. Я бы добавил один вопрос: «Расскажи про разрабатываемую систему, которая тебе не понравилась архитектурно. Что ты бы сделал иначе и почему?» Это показывает, есть ли у человека опыт реальных проектов, а не только теория.

**Чёткие тайминги.** В нашем разговоре не было «у вас 20 минут на этот блок». В реальном собеседовании это важно: кандидат не может «зависнуть» на одном вопросе на 40 минут. Тайминг создаёт дисциплину и честность: если не знаешь — скажи и иди дальше.

**Вопрос «что ты хочешь уточнить».** Я бы в конце спросил: «Что ты хочешь уточнить про команду, стек, процесс?» Не потому что это важно для найма, а потому что это показывает, готов ли кандидат работать, а не просто проходить собеседование.

---

### Что я бы изменил в структуре

```
Текущая структура (наш разговор):
  Знакомство → PHP → SOLID → Symfony → DDD → ... → Soft skills

Было бы лучше:
  1. Знакомство (5 мин) — кто, где, что
  2. Конкретный код (15 мин) — написание, не описание
  3. Архитектура (20 мин) — проект, агрегаты, события
  4. SQL и БД (15 мин) — запросы, индексы, транзакции
  5. Soft skills (10 мин) — ревью, сроки, конфликты
  6. Вопросы кандидата (5 мин)

Итого: 70 минут, не 3 часа.
```

**Почему 70 минут, а не 3 часа:** через 2 часа кандидат устаёт, ответы становятся короче и менее честными. Я сам чувствовал, как к финальным вопросам начал отвечать короче. Это не «плохо» — это усталость. Если я устал, то и кандидат устанет раньше и не покажет свой реальный уровень.

---

### Честная часть про себя

Я отвечал как «идеальный кандидат». В реальном собеседовании:

- На «FORCE INDEX» я бы мог сказать «это MySQL, в PostgreSQL нет», но не сразу вспомнил бы `pg_hint_plan`.
- На «на сколько процентов strict_types» я бы мог начать гадать, а не дать сразу честный ответ.
- На «какой SOLID чаще нарушен» я бы начал с общего и потерял фокус на 30 секунд.
- На вопрос «расскажи про проект, который тебе не понравился» я бы мог ответить общими словами, а не конкретикой.

Это не «я слабый». Это «у каждого кандидата есть 2–3 вопроса, где ответ будет короче и честнее, чем в идеальном разговоре». И это нормально. Плохо, если все 20 вопросов кандидат отвечает идеально. Значит, либо он заучил, либо собеседник не давил.

---

### Итого

```
Сильные стороны интервью:
  ✅ Разговорный формат, не опросник
  ✅ Вопросы выросли из предыдущих ответов
  ✅ Проверка суждения, а не знания
  ✅ Soft skills вперемешку с technical

Слабые стороны:
  ⚠️  Нет «напиши код»
  ⚠️  Тайминги не ясны
  ⚠️  Некоторые вопросы проверяют факт, а не суждение
  ⚠️  Частота «ловушек» (FORCE INDEX, strict_types)
      может отпугнуть кандидатов, которые честны, но не знают контекст

Что бы я изменил:
  1. Добавил бы 15 мин на написание кода
  2. Сократил бы до 70 минут
  3. Заменил бы «знаешь ли ты факт» на «выбери из двух и защити»
  4. Добавил бы вопрос про реальный проект
  5. Давал бы тайминги на блоки
```

> ⏱ 38.4s · prompt 117409 tok · output 3112 tok

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

<details><summary>Ход мысли модели</summary>

The user is asking me to share something about myself as a candidate that wasn't covered in the interview questions. This is the final, open-ended question. Let me think about what would be genuinely valuable here.

I've been playing the role of a senior PHP backend developer throughout this interview. The user is now asking me to step back and share something that wouldn't come up in structured technical questions.

What's important that structured questions miss?

1. **How I handle uncertainty** — I've shown I can answer confidently. But what's more revealing is when I DON'T know something, and how I handle that. In this interview I was always confident. In real life, I'm not always confident. That's honest.

2. **What I optimize for** — I've been showing "correct" answers. But in a real team, I optimize for the team's velocity, not just code quality. I choose the 70% solution when 100% costs 3x. That's not "good enough" — that's engineering judgment.

3. **My blind spots** — I've been showing a very well-rounded candidate. In reality, I have gaps. I'm not equally strong in everything. I should name where I'm weaker.

4. **What I need from a team** — I've been showing "I can do anything." In reality, I need context, I need to be able to say no, I need a team that doesn't treat me as a code factory.

5. **What I'd flag as a red flag** — If the team's process is "deploy Friday, rollback Monday," I'd flag that. If the architecture is "one service does everything," I'd flag that. If the team says "we don't need tests," I'd flag that.

Let me think about what's genuinely important to say here that I haven't said:

- I don't know everything. I know PHP/Symfony/Doctrine deeply. I know DDD well. But I don't know everything. I haven't built a high-throughput system from scratch. I haven't managed a team. I haven't done a large migration. I should be honest about what I haven't done.

- I choose boring technology. I've been showing "advanced" solutions (event sourcing, outbox, single-writer). But in practice, I'd choose the simplest thing that works. I'd use a cron job before a message queue. I'd use a database table before a cache. I'd use a single service before microservices. The "advanced" answer is not the "right" answer. The "right" answer is the one that the team can maintain in 2 years.

- I'm a team player, not a solo developer. I've been showing "here's how I'd do it." In reality, I'd ask the team "how do we do it?" first. I'd adapt to the team's conventions before imposing my own. I'd write code that the team can understand, not code that I find elegant.

- I have opinions, but I'm not rigid. I've been showing strong positions ("SRP is most violated," "optimistic locking is the default"). But in a team, I'd listen to why they do it differently. Maybe their context is different. Maybe they have a reason I don't know.

- I want to grow, not just deliver. I want to understand the business. I want to be close to the product owner. I want to understand why we're building this, not just what to build.

- I need to say no sometimes. If the deadline is unrealistic, I say no. If the architecture is wrong, I say no. If the team is about to deploy a bad change, I say no. I've been showing "I adapt." But sometimes adaptation is the wrong move.

Let me structure this as a genuine, honest closing statement. Not a sales pitch. Not a list of achievements. A real person speaking.

I should be:
- Honest about gaps
- Specific about what I need
- Clear about what I won't do
- Warm but not sycophantic
- Concise (this is the end, not a new essay)

Format: I'll write it as a direct speech, not a structured document. Less tables, more voice. Because this is the moment where the candidate stops performing and starts talking.

Length: This should be shorter than most of my previous answers. It's a closing statement, not a new topic. Maybe 4-6 paragraphs of direct speech, then a short list of "what I'd flag."

</details>

**Кандидат:**

## Что осталось за рамками вопросов

### Я не знаю всё

В этом разговоре я отвечал уверенно. Это не значит, что я всегда уверен.

Я глубоко знаю PHP, Symfony, Doctrine, PostgreSQL — потому что работаю с ними каждый день. Я хорошо знаю DDD — потому что проектирую на нём. Но я **не** знаю всё:

- Я не строил систему с >100k RPS. Я работал на 1–10k. Разница не в «больше того же», а в другой физической модели. Если вы идёте в high-throughput — я буду учиться, а не «закрывать вопрос».
- Я не управлял командой. Я работал senior-ом в командах 5–15 человек. Я не нанимал, не увольнял, не строил process. Если вам нужен tech lead — я не тот.
- Я не делал миграцию с нуля на новую БД (MySQL → PostgreSQL, или наоборот). Я работал внутри одной. Миграция — это другая проблема: совместимость, dual-write, cutover. Я бы не сказал «я это умею».

**Почему говорю:** если вы наймёте меня под задачу, которую я не решал — я не буду притворяться, что решал. Я скажу: «Я не делал это. Вот как я понимаю, как это работает. Вот что мне нужно узнать. Вот сколько это займёт. Давайте обсудим, подходит ли это вам.»

---

### Я выбираю скучное

В этом разговоре я показывал «продвинутые» решения: event sourcing, outbox, single-writer, pg_hint_plan. Но в реальной работе я **не** начинаю с них.

Я начинаю с:

- Таблицы в БД, а не из Kafka.
- Cron-джоба, а не из очереди.
- Одного сервиса, а не из микросервисов.
- `SELECT * FROM orders WHERE customer_id = X`, а не из материализованного представления.

«Продвинутый» ответ — это не «правильный» ответ. Правильный ответ — тот, который команда сможет поддерживать через 2 года. Если через 2 года у вас 3 разработчика и нет Kafka-эксперта — Kafka будет проблемой. А таблица в БД — нет.

Я показываю «продвинутые» решения, потому что **знаю** их. Но применяю их, когда есть конкретная причина. Не «на всякий случай».

---

### Я адаптируюсь под команду, а не наоборот

У меня есть мнения. Я их выражаю. Но я не прихожу в команду и не переписываю всё под «свой» стиль.

Первые 2 недели я делаю:

1. Читаю код. Понимаю, как команда пишет.
2. Читаю CI. Понимаю, как они тестируют.
3. Спрашиваю: «Как у вас принято делать X?» — прежде чем предложить свой вариант.
4. Пишу код в стиле команды. Даже если я бы написал иначе.

Потому что код, который я пишу «красиво», а команда не понимает — это не «красиво». Это «чужой». Через 3 месяца кто-то будет его читать без меня. И он должен понимать.

---

### Что я бы флагнул на этапе найма

Если бы я шёл на интервью и увидел:

```
🚩 «У нас не тестируем, потому что деплоим часто»
   → Я бы спросил: «А что, когда сломали? Сколько времени на фикс?»
   → Если «30 минут» — ок. Если «2 дня» — это не скорость, это хаос.

🚩 «Архитектура: один сервис на всё»
   → Я бы спросил: «А что, когда один модуль тормозит?»
   → Если «ничего, он маленький» — ок. Если «тормозит весь API» — это не масштабируемость.

🚩 «Деплой по пятницам»
   → Я бы спросил: «А если сломали в пятницу?»
   → Если «откатываем в понедельник» — это не надёжность. Это лотерея.

🚩 «У нас нет ревью, потому что мы доверяем»
   → Я бы спросил: «А если баг на проде? Кто находит?»
   → Если «пользователь» — это не доверие. Это отсутствие контроля.

🚩 «Ты будешь делать всё: фронт, бэк, DevOps, поддержка»
   → Я бы спросил: «А кто тогда ревьюит мой код?»
   → Если «никто, ты сам» — это не senior. Это solo developer с дедлайнами.
```

Я не говорю, что это «плохая компания». Может, это стартап на 3 человека, где всё в одном человеке. Это нормально. Но я хочу **знать**, а не обнаруживать через месяц.

---

### Что мне важно в команде

Не «офис с кофемашиной». Не «гибкий график». А:

```
1. Я могу сказать «нет» и меня не уволят.
   Если дедлайн нереалистичный — я говорю.
   Если архитектура неправильная — я говорю.
   Если команда собирается задеплоить баг — я говорю.
   И это не «конфликт», а работа.

2. Я могу спросить «почему?» без наказания.
   Не «потому что так принято», а «потому что X».
   Если ответ «потому что так принято» — я не научусь.
   Если ответ «потому что X, но вот Y тоже работает» — я вырасту.

3. Я близок к продукту.
   Не «бэкенд делает, что сказал фронт».
   А «я понимаю, что делает пользователь, и почему».
   Это не обязательно еженедельная встреча с PM.
   Это доступ к тикетам, к логам, к обратной связи.

4. Я не один.
   Если я застрял — я могу спросить.
   Если я ошибся — кто-то увидит.
   Если я устал — кто-то подхватит.
   Это не «командный дух». Это базовая гигиена.
```

---

### И последнее

В этом разговоре я отвечал как «идеальный кандидат». Это не значит, что я идеален. Это значит, что я **знаю**, как отвечаю на эти вопросы. В реальной работе я буду:

- Ошибаться. И признавать.
- Не знать. И говорить «не знаю».
- Торопиться. И потом исправлять.
- Спорить. И проигрывать.
- Быть усталым. И писать код хуже, чем хотелось бы.

Я не продаю себя. Я говорю: вот что я умею, вот где я слаб, вот что мне нужно, вот что я не сделаю. Дальше — ваше решение.

> ⏱ 30.3s · prompt 119507 tok · output 2574 tok

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

<details><summary>Ход мысли модели</summary>

The user is asking me to reflect on my "closest" tech stack and defend a choice for a new project. This is a continuation of the interview scenario I've been in. I should stay in character as a PHP backend developer who has been honest throughout the interview — including admitting what I don't know, choosing boring over fancy, and being concrete rather than generic.

Key things to keep in mind from the conversation:
- I've been a PHP/Symfony/Doctrine/PostgreSQL developer
- I've emphasized boring over fancy, pragmatic over theoretical
- I've been honest about limits (no >10k RPS, no team lead)
- I've shown I can reason about trade-offs, not just list technologies
- I've been against "because everyone does it" and for "because here's the context"
- I've shown I value maintainability over cleverness
- I've been concrete with examples, not vague with principles

I should NOT:
- Give a generic "I'm polyglot" answer
- List 10 technologies with pros/cons
- Be a yes-man ("any stack works if you're good")
- Be a dogmatist ("PHP is best and here's why")
- Give a 3-page essay with tables when a focused answer would be better

I SHOULD:
- Be concrete about what "close" means to me
- Defend a choice with reasoning, not features
- Show I know when NOT to use my preferred stack
- Be honest about what "close" actually means (muscle memory, ecosystem, hiring, debugging)
- Keep it grounded in the persona I've built

Let me think about what a real senior PHP dev would say here. They'd probably:
1. Say PHP/Symfony/PostgreSQL is their home turf — honestly
2. But also say "it depends on the project" — honestly
3. Show they can reason about WHY, not just WHAT
4. Admit they'd choose differently for different contexts
5. Not be religious about it

The format should be conversational, not a textbook. The user has been asking me to be honest and concrete throughout. I'll keep it that way. No giant tables. A focused, opinionated answer with real reasoning.

</details>

**Кандидат:**

## Мой стек

### Честный ответ

PHP 8.3, Symfony 7, Doctrine ORM, PostgreSQL 16, RabbitMQ, Docker.

Не потому что «лучший». Потому что я знаю его **внутренне**. Не «знаю как пользоваться» — а «знаю что будет, если я сделаю X».

Это не про фичи. Это про то, что когда у меня баг в 2 часа ночи, я не гуглю «как работает X». Я открываю код, смотрю, и понимаю. Это экономит не часы, а дни.

### Но «мне близок» ≠ «лучший для проекта»

Если бы мне сказали «выбери стек для нового проекта», я бы сначала спросил **три вопроса**:

**1. Какой у вас состав команды?**

```
5 человек, все backend, все с опытом в PHP → PHP.
5 человек, все с опытом в Go/Java → не PHP.
10 человек, половина junior → PHP (экосистема дружелюбнее,
   наём проще, меньше «почему он упал»).
```

Это не «я не уважаю Go». Это «мне проще нанять junior на PHP, чем на Go». И «мне проще наставить junior на PHP, чем на Go». Не потому что PHP проще. А потому что у меня есть опыт наставничества именно на нём.

**2. Какой у вас масштаб?**

```
100 запросов/сек, 5 сервисов → PHP + PostgreSQL.
10k запросов/сек, 20 сервисов → PHP + PostgreSQL + Redis + очередь.
100k запросов/сек, real-time → PHP может быть не тем местом.
```

Я не утверждаю, что PHP «не масштабируется». Я утверждаю, что **мне** проще масштабировать то, что я знаю, чем то, что я нет. Через 3 месяца я знаю Go так же хорошо. Но через 3 месяца у вас уже будет продукт.

**3. Что будет через 2 года?**

```
Если проект — SaaS на 10 клиентов → PHP.
Если проект — инфраструктура на 10k клиентов → может быть Go/Java.
Если проект — внутренний инструмент на 50 пользователей → PHP, и точка.
```

### Почему именно Symfony, а не Laravel

Не из преданности. Из конкретики:

```
DI-контейнер Symfony — я знаю, как он компилируется.
Когда у меня проблема с зависимостями — я открою ContainerXXX.php
и посмотрю. У Laravel я бы гуглил.

Event system Symfony — я знаю, как работает kernel request cycle.
Я могу перехватить на любом этапе. У Laravel я бы читал source.

Валидация Symfony — я знаю, как работают constraint-классы.
Я пишу свои. У Laravel я бы использовал built-in.
```

Это не «Symfony лучше». Это «я знаю Symfony так, что могу его чинить изнутри».

### Что я бы НЕ выбрал, даже если бы хотел

```
❌ Microservices «на старте»
   → 3 человека, 5 сервисов = 15 точек отказа.
   → Начну с modular monolith. Разделю, когда будет боль.

❌ MongoDB «потому что flexible»
   → У меня PostgreSQL. У меня JOIN'ы, транзакции, индексы.
   → «Flexible» = «не знаю схему». А я знаю схему.

❌ GraphQL «потому что современный»
   → У меня REST. У меня кэширование, пагинация, авторизация.
   → GraphQL добавляет сложность, которую я не вижу как ценность
     для текущего продукта.

❌ K8s на 5 контейнерах
   → Docker Compose на 2 машинах. Конец.
   → K8s нужен, когда у вас 50 сервисов и 10 node'ов.
   → Не раньше.

❌ Event-driven «на старте»
   → Если мне не нужны асинхронность, replay, audit —
     мне не нужна очередь.
   → Просто function call. Конец.
```

### Как бы я защищал выбор перед командой

Не «PHP лучше, потому что...». А:

```
«Вот что я знаю:
  - 5 человек в команде.
  - 2 из них junior.
  - Дедлайн MVP: 6 недель.
  - Масштаб: до 500 запросов/сек.
  - Нет требований к real-time.

Вот что я предлагаю:
  PHP 8.3 + Symfony 7 + PostgreSQL + RabbitMQ + Docker.

Вот почему:
  1. Все 5 человек работали на PHP. Ноль обучения.
  2. Symfony: DI, validation, routing — всё встроено.
     Не тратим 2 недели на «каркас».
  3. PostgreSQL: у нас relational data. JOIN'ы, транзакции.
     Не гоняемся за «NoSQL-гибкостью», которой не нужно.
  4. RabbitMQ: у нас 3 асинхронных процесса (email, индекс, аналитика).
     Не больше. Очередь — ровно то, что нужно.
  5. Docker: один файл, `docker compose up`, всё работает.
     Не K8s. Не Helm. Не Terraform. 5 человек.

Вот что я НЕ предлагаю и почему:
  - Не Go: обучение 2–3 недели, а мы должны быть в коде на этой неделе.
  - Не microservices: 5 человек, 1 продукт. Monolith.
  - Не K8s: 3 контейнера. Compose.
  - Не Kafka: 3 события/сек. RabbitMQ.

Вот когда я бы пересмотрел:
  - > 5k запросов/сек → горизонтальное масштабирование, Redis.
  - > 15 человек в команде → microservices по доменным границам.
  - > 10k событий/сек → Kafka.
  - > 5 node'ов в prod → K8s.

Вопросы?»
```

### И последнее

Я не буду защищать PHP как «лучший язык». Я защищу **конкретный выбор для конкретного контекста**. Потому что «лучший стек» не существует. Существует «стек, который команда может написать, запустить, отладить и поддерживать через 2 года».

И для 80% команд, которые я встречал, это PHP + PostgreSQL. Не потому что PHP лучший. А потому что команда его знает, нанимает, понимает и чинит.

Если вы ищете человека, который скажет «всё зависит от контекста» и дальше ничего не скажет — я не тот. Я скажу: «Вот мой контекст, вот мой выбор, вот почему, вот когда я бы передумал.»

> ⏱ 24.8s · prompt 121160 tok · output 2041 tok

---
