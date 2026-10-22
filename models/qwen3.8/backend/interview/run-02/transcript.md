# Собес: `qwen3.8:latest` · роль: backend — run-02

- Дата: 2026-10-06T17:29:25.815545+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: auto · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to introduce myself, talk about my experience in PHP development and related technologies, and then rate my knowledge on a scale of 0-10 for each of: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, clean architecture, testing, git — with a one-sentence explanation for each rating.

I need to be honest here. I'm an AI language model. I don't have "experience" in the traditional sense — I haven't written production code, I haven't debugged at 2am, I haven't refactored legacy codebases. What I do have is training on a massive corpus of code, documentation, tutorials, blog posts, Q&A sites, books, and other programming resources. I can generate, explain, debug, and discuss PHP and related technologies.

I should be transparent about what I am — not pretend to be a human developer with X years of experience. But I can be genuinely useful and discuss what I know well.

Let me think about honest ratings:

- PHP 8: I know the language deeply — fiber, readonly properties, enums, match, constructor promotion, typed properties, named arguments, union/intersection types, nullsafe operator, attributes. I can write idiomatic PHP 8. I'd say 9. I'm very thorough on syntax, features, edge cases.

- Symfony: I know the components well — routing, DI container, events, form, security, Messenger, Serializer, Twig. I've seen a lot of Symfony code in training data. I'd say 8-9. I know the architecture, the component boundaries, common patterns. Maybe 9.

- Doctrine: ORM, DBAL, QueryBuilder, DQL, entity mappings, lifecycle callbacks, hydration, second-level cache, migrations. I know it well. 8-9. Let's say 9.

- PostgreSQL: I know SQL, window functions, CTEs, JSONB, full-text search, partitioning, indexing strategies, EXPLAIN plans, vacuum, TOAST. 8.

- DDD: I know the concepts — bounded contexts, aggregates, domain events, repositories, value objects, entities, domain services, tactical patterns from "Patterns of Enterprise Application Architecture" and "Implementing Domain-Driven Design." I can discuss and apply them. 8.

- Clean architecture (hexagonal, ports & adapters, onion): I know the principles, dependency rule, layers. I can apply them in PHP/Symfony context. 8.

- Testing: PHPUnit, Pest, Mockery, integration vs unit testing, TDD, BDD concepts, property-based testing. I know a lot. 8-9. Let's say 8.

- Git: branching strategies, rebasing, cherry-picking, conflict resolution, bisect, hooks. I know it well. 9.

I should be honest that these are "knowledge" ratings, not "experience" ratings. I haven't shipped production code. But my knowledge is broad and deep.

Let me write this in Russian since the user wrote in Russian. I'll be conversational, honest, and structured.

</details>

**Кандидат:**

Привет! Давай начнём честно: у меня нет «опыта» в человеческом смысле — я не сидел в проде, не гуглил тайп-эррор в три часа ночи и не спорил с тимлидом о том, класть ли доменную логику в сервис или в саму сущность. Я — языковая модель, обученная на огромном корпусе кода, документации, книг, блогов и вопросов на Stack Overflow. Это значит, что я знаю **очень много** о том, как устроены PHP-экосистемы, и могу писать, объяснять, рефакторить, искать баги и проектировать решения — но я не «полгода вёл микросервис на Laravel» и не «три года гонял Doctrine на 20k QPS».

То, что я действительно хорошо умею:

- **Писать и читать код** на PHP 7.4–8.3, включая Fibers, readonly, enums, attributes, named arguments, union/intersection types.
- **Разбирать архитектуры**: Symfony-приложения (компоненты, DI, события, Messenger, Form, Security), чистую/гексагональную архитектуру, DDD-тактики (агрегаты, доменные события, bounded contexts).
- **Генерировать SQL** для PostgreSQL (CTE, window functions, JSONB, партиционирование, индексы) и объяснять, почему один запрос быстрый, а другой — нет.
- **Писать тесты** (PHPUnit, Pest, Mockery) и объяснять, что покрывать, а что нет.
- **Работать с git** на уровне команд, стратегий ветвления, разрешения конфликтов, bisection.
- **Обсуждать trade-offs**: «зачем вам DDD, если у вас CRUD-админка», «когда Doctrine ORM — лишний слой», «когда микросервисы — не решение».

Теперь оценки. Я оцениваю **глубину и ширину знаний**, а не «практический опыт» (которого у меня нет по определению):

| Раздел | Оценка | Обоснование (одним предложением) |
|---|---|---|
| **PHP 8** | **9** | Я свободно оперирую всеми фичами 8.0–8.3 (Fibers, readonly, enums, attributes, typed properties, union/intersection types, nullsafe) и могу написать идиоматичный код, а не «C-в-PHP-шлере». |
| **Symfony** | **9** | Я знаю внутреннюю кухню основных компонентов (DI, Router, EventDispatcher, Form, Security, Messenger, Serializer, Twig), типичные pitfalls и могу спроектировать приложение «по-симфоновски». |
| **Doctrine** | **8** | Я уверенно работаю с ORM, DBAL, DQL, lifecycle-каллабками, hydration, second-level cache и миграциями, но не «ощущаю» N+1 в проде, потому что не гонял реальный трафик. |
| **PostgreSQL** | **8** | Я хорошо пишу и читаю SQL (CTE, window, JSONB, FTS, партиционирование, EXPLAIN ANALYZE), но у меня нет интуиции «из-за TOAST этот INSERT стал медленным». |
| **DDD** | **8** | Я знаю тактические и стратегические паттерны наизусть и могу применить их в PHP-контексте, но не могу похвастаться тем, что «наш bounded context раскололся, и мы полгода мигрировали». |
| **Чистая архитектура** | **8** | Я чётко понимаю dependency rule, ports & adapters, onion/hexagonal и могу показать, где в Symfony-проекте нарушается инверсия зависимостей. |
| **Тестирование** | **8** | Я пишу unit/integration-тесты, настраиваю PHPUnit/Pest, использую Mockery, понимаю TDD-цикл и property-based testing, но не «проверял, что 2 000 тестов гоняются за 90 секунд на CI». |
| **Git** | **9** | Я свободно использую rebase, cherry-pick, bisect, reflog, хуки, стратегии ветвления и могу распутать любой конфликт, который мне принесут. |

Если хочешь, могу сразу перейти к делу: разберём конкретную задачу, посмотрим на код, обсудим архитектуру или напишем тесты. Что сейчас актуально?

> ⏱ 16.3s · prompt 87 tok · output 1695 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user asks about the most important features of modern PHP (8.0+) for writing clean and reliable code, with short code examples. This is a follow-up to our conversation about PHP development. Let me think about what's genuinely important and group them logically.

Key features I'd highlight for "clean and reliable code":

1. **Union types & intersection types** (8.0) — tighter contracts
2. **Nullsafe operator** (8.0) — less boilerplate for nullable chains
3. **Constructor property promotion** (8.0) — less boilerplate in DTOs/entities
4. **Named arguments** (8.0) — readability, especially with many optional params
5. **Match expression** (8.0) — cleaner than switch, exhaustive
6. **Enums (8.1)** — replace magic strings/constants
7. **Readonly classes & properties (8.2)** — immutability, safe DTOs
8. **Fibers (8.1)** — async, but more niche; maybe mention briefly
9. **Destructuring improvements, first-class callable syntax (8.1)**
10. **Typed constants (8.3)**
11. **asymmetric visibility (8.2)** — readonly params
12. **Property hooks (8.4)** — maybe too new/experimental

Let me focus on the ones that most directly impact "clean and reliable code":

- Union/intersection types + nullsafe: type safety
- Constructor property promotion + readonly: less boilerplate, immutability
- Enums: eliminating magic strings
- Match: cleaner control flow
- Named arguments: readability
- Fibers: async (maybe brief mention)
- Asymmetric visibility / readonly params: encapsulation

I'll structure this as a concise but substantive answer with short code examples. Not too long — the user said "коротких примерах". I'll pick ~5-6 most impactful features, show a tiny before/after or just the feature, and add a one-liner about why it matters for reliability.

Let me write in Russian since the conversation is in Russian.

</details>

**Кандидат:**

Выберу шесть фич, которые реально меняют качество кода на каждый день. Не по хронологии, а по тому, как они убивают типичные источники багов и boilerplate.

## 1. Union / Intersection types + nullsafe operator (8.0)

Типы перестают быть «надеюсь, что string». Контракт становится точным:

```php
// До: $id может быть чем угодно, в рантайме — сюрприз
function findById(string $id): ?User { … }

// После: явно «или» и «и», nullsafe убирает 4-5 строк null-check
function findById(string|int $id): ?User { … }

$name = $user?->address?->street?->name;  // null, если любой шаг null
// вместо:
// $name = null;
// if ($user && $user->address && $user->address->street && $user->address->street->name) {
//     $name = $user->address->street->name;
// }
```

**Почему важно:** меньше `if ($x !== null && $x->y !== null && …)` — а значит, меньше «забыл проверить один из уровней».

## 2. Constructor property promotion (8.0)

DTO, сущности, конфиги — без пяти строк «declare + assign»:

```php
// До
class Address {
    public function __construct(
        private string $street,
        private string $city,
        private string $zip
    ) {}
    public function getStreet(): string { return $this->street; }
    public function getCity(): string  { return $this->city; }
    // …
}

// После — конструктор = декларация = тип
final readonly class Address {
    public function __construct(
        public string $street,
        public string $city,
        public string $zip,
    ) {}
}
```

**Почему важно:** меньше места, где можно опечататься в имени свойства или забыть присвоить. И `readonly` сразу запрещает мутацию после создания — это уже не «привычка», а гарантия компилятора.

## 3. Enums (8.1)

Убивают магические строки и «константы-enum'ы»:

```php
// До: string-константы, которые можно передать любые
class OrderStatus {
    public const PENDING  = 'pending';
    public const SHIPPED  = 'shipped';
    public const DELIVERED = 'delivered';
}

// После: замкнутое множество значений
enum OrderStatus: string {
    case Pending   = 'pending';
    case Shipped   = 'shipped';
    case Delivered = 'delivered';

    public function label(): string {
        return match ($this) {
            self::Pending   => 'В обработке',
            self::Shipped   => 'Отправлен',
            self::Delivered => 'Доставлен',
        };
    }
}

// В switch/match — исчерпывающий, статический анализатор ругнётся
// если добавите case и забудете обработать
function cancel(OrderStatus $status): bool {
    return match ($status) {
        OrderStatus::Pending => true,
        OrderStatus::Shipped, OrderStatus::Delivered => false,
    };
}
```

**Почему важно:** `match` без `default` — если вы добавите новый case, IDE и static analysis (PHPStan/Psalm) сразу подсветят, что где-то логику забыли. С магическими строками вы просто «молча верните `false`».

## 4. Named arguments (8.0)

Когда у метода 5+ опциональных параметров, позиционные аргументы превращаются в угадывание:

```php
// До: что за $true и $false? Через 2 месяца уже не вспомните
$query = $repo->find($status, $null, true, false, 'created_at', null, 50);

// После: сам код читается как документация
$query = $repo->find(
    status:   OrderStatus::Pending,
    withUser: true,
    sort:     'created_at',
    limit:    50,
);
```

**Почему важно:** не нужно держать в голове порядок параметров. При рефакторинге, где меняете порядок или добавляете параметр посередине, вызовы не ломаются.

## 5. `readonly` classes и asymmetric visibility (8.2)

Иммутабельность как свойство языка, а не «мы договорились не менять»:

```php
// Класс не мутирует после создания — ни изнутри, ни снаружи
final readonly class Money {
    public function __construct(
        public int    $amount,   // в cents
        public string $currency,
    ) {}

    public function add(self $other): self {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatch($this->currency, $other->currency);
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

// Asymmetric visibility (8.2) — readonly на параметре:
// записываете один раз в конструкторе, дальше только чтение
final class Repository {
    public function __construct(
        readonly private Connection $db,   // можно записать, потом read-only
        private array $cache = [],          // можно мутировать
    ) {}
}
```

**Почему важно:** `readonly` на уровне класса — статическая гарантия. Не нужно читать чужой код и проверять, «а тут вдруг `$this->amount = 0;`». А asymmetric visibility убирает классический паттерн «приватное поле + публичный getter + приватный setter».

## 6. Match expression (8.0) — как замена switch

Коротко, потому что он уже в примере с enum, но отдельно:

```php
// До: fall-through, забытый break, строковые сравнения
$price = match (true) {
    $qty >= 100 => 4.99,
    $qty >= 50  => 5.49,
    $qty >= 10  => 5.99,
    default     => 6.99,
};

// После: строки/числа/enum'ы, без fall-through, strict comparison
$price = match (true) {
    $qty >= 100 => 4.99,
    $qty >= 50  => 5.49,
    $qty >= 10  => 5.99,
    default     => 6.99,
};

// С enum — ещё чище, и без default static analysis ругнётся:
$label = match ($order->status) {
    OrderStatus::Pending   => 'В обработке',
    OrderStatus::Shipped   => 'Отправлен',
    OrderStatus::Delivered => 'Доставлен',
};
```

**Почему важно:** `match` — expression, а не statement: возвращает значение, нет fall-through, сравнение строгое (`===`). Меньше одного класса багов «забыл `break`» сразу.

---

## Что я намеренно не включил

- **Fibers (8.1)** — мощно, но в мире PHP асинхронность пока ниша (Swoole/RoadRunner). Для большинства CRUD-приложений на Symfony это не «главная фича».
- **Property hooks (8.4)** — обещают красиво, но ещё не stable и не везде поддерживаются.
- **Typed constants (8.3)** — приятно, но по частоте использования не тянет на «самое важное».

---

Если хочешь, могу показать, как эти фичи складываются в один небольшой доменный модуль (entity + repository + domain service) и где каждая экономит строчку или убивает баг. Или перейти к чему-то другому — Symfony, Doctrine, архитектура — как удобнее.

> ⏱ 17.6s · prompt 1183 tok · output 2315 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему типов в PHP: strict_types, приведения типов, типичные ловушки. Нужно показать где типизация спасает, а где удивляет. Структурирую: 1) как устроена система (динамическая, weak typing по умолчанию, strict_types меняет поведение в текущем файле). 2) strict_types — что делает, как работает (file-level directive, влияет только на вызовы из этого файла). 3) Приведения — таблица/примеры, где PHP "умный" и где "неожиданный". 4) Ловушки: string vs int, loose comparison (==), json_decode возвращает mixed, array access, null в weak types, coercion в аргументах. 5) Где спасает: strict_types + typed parameters + union types + readonly. 6) Где удивляет: mixed, bool в число, string "01" == 1, null как аргумент, array<string> не проверяется в рантайме. Приведу короткие примеры кода. Не буду перегружать, но покажу реальные баги которые встречаются в проде.

</details>

**Кандидат:**

## Как устроена система типов в PHP

PHP — язык с **динамической** и по умолчанию **слабой (weak)** типизацией. Это значит:

- Тип переменной определяется в рантайме, в момент присвоения.
- При несоответствии типов PHP **пытается привести**, а не падает.
- Типизация в сигнатурах функций/методов — это **проверка в точке вызова**, а не компиляторная проверка всего графа.
- `strict_types` переключает поведение приведений **в конкретном файле** (не в проекте, не в имени пространстве).

Это не «нет типов» — с 7.0 и особенно с 8.0–8.3 система стала значительно строже. Но фундаментальная слабость осталась: PHP не знает, что `array` — это `array<User>`, и не знает, что `mixed` из `json_decode` — это `Order` или `null` или `string`.

---

## strict_types: что делает и как работает

```php
<?php
declare(strict_types=1);
```

Это **file-level directive**. Она влияет только на **вызовы функций/методов, сделанные из этого файла**. Не на определения, не на наследуемые методы, не на вызовы из других файлов.

```php
// file: Math.php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// file: App.php
// НЕТ strict_types

add("10", 5);  // ✅ Работает. "10" приводится к 10.
// Почему? Вызов сделан из App.php, где strict_types=0 (по умолчанию).
```

```php
// file: App.php
declare(strict_types=1);

add("10", 5);  // ❌ TypeError: Argument #1 ($a) must be of type int, string given
// Почему? Вызов сделан из файла с strict_types=1.
```

**Ключевой нюанс:** `strict_types` влияет на **место вызова**, а не на определение функции. Это путает. Если вы перенесёте вызов в другой файл — поведение изменится.

### Что НЕ делает strict_types

- Не влияет на приведения внутри функции (`$x = "5" + 3;` всё равно даёт `8`, потому что `+` — арифметический оператор, он сам приводит).
- Не влияет на `mixed` → что-то конкретное (в 8.2+ `mixed` можно присвоить чему угодно, strict_types это не блокирует).
- Не валидирует **возвращаемые значения** из внешних библиотек, которые вы не контролируете.

---

## Приведения типов: где PHP «умный», а где «сюрприз»

### Базовые правила (weak mode)

| Выражение | Результат | Почему |
|---|---|---|
| `"10" + 5` | `15` (int) | `+` с числовым строкой → int |
| `"abc" + 5` | `5` (int) | `"abc"` не число → `0` + 5 |
| `1 == "1"` | `true` | `==` приводит обе стороны к числу |
| `1 == "1.0"` | `true` | аналогично |
| `1 == "01"` | `true` | `"01"` → `1` |
| `0 == "abc"` | `true` | `"abc"` → `0`, `0 == 0` |
| `"" == false` | `true` | `""` → `false` |
| `"0" == false` | `true` | `"0"` → `0` → `false` |
| `null == "0"` | `true` | `null` → `""`? Нет. `null` vs string: null приводится к `""`, `""` не число… на самом деле: `null == "0"` → `true` в PHP (нулевая строка и null равны) |

Последние строки — классический источник багов. Давайте разберём.

### Ловушка 1: `==` с null и пустыми строками

```php
// Ужасный, но рабочий в PHP код
if ($user === null) { … }        // ✅ Строго
if ($user == null) { … }         // ⚠️ true для null, false для 0, "" и false
if ($id == 0) { … }              // ⚠️ true для 0, "0", "", null, false
if ($id === 0) { … }            // ✅ Только int 0
```

**Пример реального бага:**

```php
$pageSize = $_GET['limit'] ?? 20;  // Может быть "abc" если клиент сломал query
$offset   = $_GET['offset'] ?? 0;

// Если limit = "abc":
$limit = (int) $pageSize;  // 0
if ($limit == 0) {         // true, но мы думали "пользователь не указал"
    $limit = 20;
}
// Теперь $limit = 20, хотя клиент просил "abc" (мусор)
// Правильно: filter_var($pageSize, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]])
```

### Ловушка 2: числовые строки в массивах и ключах

```php
$arr[1]   = "one";   // Ключ int(1)
$arr["1"] = "ONE";   // Ключ int(1) — тот же! PHP приводит строковый ключ к int если он выглядит как число.

echo $arr[1];   // "ONE" — второй записался поверх первого
```

### Ловушка 3: `json_decode` возвращает `mixed`

```php
$payload = json_decode(file_get_contents('php://input'));
// $payload: object, array, string, int, float, bool, null — ВСЁ возможно

// До 8.2: PHP не знал, что это. Типизация в сигнатуре не помогает:
function process(array $data): void { … }
process($payload);  // ❌ TypeError, если $payload — object (json_decode с assoc: false)

// После 8.2: можно написать mixed, но это не решает проблему
function process(mixed $data): void {
    if (!is_array($data)) {
        throw new \InvalidArgumentException('Expected array');
    }
    // …
}
```

### Ловушка 4: bool в числовом контексте

```php
$flag = "false";       // Строка "false"
if ($flag) { … }        // ✅ true! Не пустая строка → truthy

$flag = "0";
if ($flag) { … }        // ❌ false! "0" → 0 → falsy

$flag = false;
echo $flag + 1;         // 1. false → 0
echo true + true;       // 2. true → 1
```

### Ловушка 5: `string` vs `int` в API-ответах

```php
// API возвращает JSON: {"id": 42, "external_ref": "12345"}
// json_decode → {"id": 42, "external_ref": "12345"}

// Вы делаете:
$ids = [$order->id, $order->external_ref];
// [42, "12345"] — int и string в одном массиве.
// Если потом array_sum($ids) — работает (строка приводится).
// Если потом in_array(42, $ids) — true (loose).
// Если потом in_array(42, $ids, true) — false (strict, разные типы).
```

---

## Где типизация СПАСАЕТ

### 1. Union types + strict_types = ранний crash вместо «молча работает»

```php
declare(strict_types=1);

function findById(int|string $id): ?User {
    // Внутри — точно int или string, не "abc123" как float, не null
}

findById("abc123");  // ❌ TypeError. Не «вернул null и потом NullPointer в 20 строк ниже».
```

### 2. Enums — закрытое множество

```php
enum Role: string {
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';
}

// Не передать "superuser" или "ADMIN" или 42.
// match без default — если добавите case, статический анализатор подсветит.
```

### 3. `readonly` — иммутабельность как гарантия

```php
final readonly class Money {
    public function __construct(
        public int $amount,
        public string $currency,
    ) {}
}

$m = new Money(100, 'RUB');
$m->amount = 0;  // ❌ Error: Cannot modify readonly property
```

Без `readonly` кто-то в другом месте напишет `$money->amount = 0;` и вы не заметите, пока не сломается отчёт.

### 4. `never` и `void` — честные контракты

```php
function validate(Order $order): never {
    if ($order->amount <= 0) {
        throw new InvalidOrder('Amount must be positive');
    }
    // Здесь нет return — статический анализатор знает, что после вызова кода нет.
}
```

### 5. Asymmetric visibility (8.2) — один setter вместо трёх

```php
final class Session {
    public function __construct(
        readonly private \DateTimeImmutable $startedAt,  // записали один раз
        private int $viewCount = 0,                        // мутируем
    ) {}

    public function recordView(): void {
        $this->viewCount++;
    }

    // Невозможно: $session->startedAt = ...;  Error
}
```

---

## Где типизация УДИВЛЯЕТ

### 1. `mixed` — «тип-антитип»

```php
function log(mixed $value): void {
    // value: int, string, array, object, null, bool, float, resource, Closure…
    // PHP и статический анализатор не знают, что это.
    // strict_types не помогает: mixed → mixed не приводит.
    echo strlen($value);  // ❌ если $value — int или array
}
```

### 2. Generics в PHP — «виртуальные»

```php
/** @var array<User> $users */
$users = $repo->findAll();

// Статический анализатор (PHPStan/Psalm) знает: это array<User>.
// Рантайм PHP — НЕТ. Для него это просто array.
// $users[0]->save() — PHPStan скажет «User::save() существует».
// Но если в рантайме в массив попал stdClass (json_decode без cast) — Fatal Error.
```

PHP **не проверяет** generic-параметры в рантайме. Это не C# и не TypeScript.

### 3. Приведение при присвоении в weak mode

```php
<?php
// БЕЗ strict_types

function setCount(int $count): void {
    $this->count = $count;  // Здесь всё строго: $count уже int
}

setCount("10");  // "10" → 10 (weak mode в файле вызова)
setCount("abc"); // "abc" → 0 (!!)
setCount(3.7);   // 3.7 → 3 (truncation, не round)
setCount([1]);   // ❌ TypeError (массив не приводится к int)
```

**`3.7 → 3`** — не «округление», а **усечение**. Это удивляет, когда кто-то передаёт `3.9` и ожидает `4`.

### 4. Строки с пробелами

```php
function parseId(string $id): int {
    return (int) $id;
}

parseId(" 42 ");   // 42 — PHP игнорирует ведущие пробелы
parseId("42abc");  // 42 — PHP парсит до первого нечисла
parseId("");       // 0
parseId(null);     // 0 (в weak mode, null → "" → 0)
```

### 5. `self` vs `static` vs `parent` в типизации возврата

```php
class Shape {
    public function copy(): self {
        return new self(...);  // Всегда Shape, никогда не подкласс
    }
}

class Circle extends Shape {
    public function copy(): static {
        return new static(...);  // Возвращает Circle, если вызвали на Circle
    }
}

// $circle->copy() → Circle (static)
// $shape->copy()  → Shape (self)
```

Если вы объявили `copy(): self` в базовом классе, а в подклассе хотите `static` — PHP 8.0+ позволяет **covariant return types**, но только если подкласс возвращает **узкий** тип. `static` — допустим, если `self` — допустим.

---

## Практические рекомендации

| Проблема | Решение |
|---|---|
| `==` ведёт себя странно | Всегда `===` и `!==` в бизнес-логике |
| `json_decode` возвращает `mixed` | Оберните в `try/catch` + `is_array`/`is_object` проверку сразу |
| Числовые строки из API/$_GET | `filter_var($x, FILTER_VALIDATE_INT)` или `(int)` + явная проверка `=== 0` |
| Generics не проверяются в рантайме | Ручной `instanceof` на границах (repository, HTTP layer) |
| `strict_types` file-level | Добавьте `declare(strict_types=1);` в **каждый** файл, где есть вызовы внешних функций |
| `mixed` в публичном API | Избегайте. Если не обходится — документировать `@var` и проверять в рантайме |
| Float для денег | `int` в минимальных единицах + `readonly` + union type `int\|float` только на границе |

---

Если хочешь, могу углубиться в конкретный раздел: например, показать, как PHPStan/Psalm ловят то, что runtime не ловит, или разобрать `union types` + `intersection types` + `DNF` (8.2) на практических примерах.

> ⏱ 28.6s · prompt 3106 tok · output 3667 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о проектировании работы с исключениями в PHP: иерархии, оборачивание чужих, контракт метода, антипаттерны. Это глубокий вопрос, и у меня есть обширные знания. Нужно структурировать ответ: принципы, конкретные примеры кода, антипаттерны с объяснением почему это плохо.

Структура:
1. Базовый принцип: исключения — это часть контракта, а не «мусор для отладки»
2. Иерархия: domain exceptions vs infrastructure exceptions
3. Оборачивание: где оборачивать, где нет
4. Контракт метода: что бросает, что возвращает, что молча делает
5. Антипаттерны: пустой catch, бросание \Exception, бросание для flow control, catch(\Throwable) в бизнес-логике, «исключение вместо return false»
6. Практические паттерны: Result pattern, domain exceptions, infrastructure exceptions, HTTP exception layer

Я должен быть конкретным, с кодом, и показать мышление архитектора, а не просто перечислять правила. Важно показать trade-offs: когда Result лучше, когда исключения лучше. Когда оборачивать, когда прокидывать.

Антипаттерны:
- Пустой catch
- catch(\Throwable) в бизнес-логике
- Бросание \Exception / \RuntimeException «вообще»
- Исключения для flow control (if (!found) throw)
- «Оборачивание ради оборачивания» — 5 уровней try/catch
- Бросание в конструкторах без контекста
- Catch и повторный throw без добавления контекста
- Исключение вместо Result для ожидаемых условий
- Бросание из финализаторов/деструкторов
- «Исключение с кодом ошибки» вместо типа

Контракт метода:
- Документировано (PHPDoc @throws) что бросает
- Типизировано: конкретный класс, а не \Exception
- Deterministic: один и тот же вход → одно и то же исключение (или нет)
- Не бросает для «нормального» исхода

Иерархия:
- DomainException (базовый для домена)
  - ValidationException
  - StateException (сущность в неверном состоянии)
  - NotFoundException
- InfrastructureException (базовый для инфраструктурных)
  - DatabaseException
  - HttpException
  - FilesystemException
- ApplicationException (для application layer)

Оборачивание:
- На границе домена: infrastructure → domain exception
- В application layer: catch infrastructure, логировать, бросить domain или возвращать Result
- Никогда не оборачивайте в business-логике «всё подряд»

Покажу код: доменная сущность, repository, application service, exception handler (Symfony-style).

Также упомяну: Result pattern как альтернатива для ожидаемых условий, и когда он уместен, а когда — нет.

Длина: средняя-длинная, с кодом. Не перегружать, но показать мышление.

</details>

**Кандидат:**

## Базовый принцип

Исключение — это **контракт**, а не «мусор для отладки». Если метод бросает `Exception`, вызывающий код **не обязан** это ловить. Если метод бросает `OrderNotFoundException`, вызывающий код **должен** знать об этом — через PHPDoc, через типизацию, через конвенцию.

Отсюда первый и самый важный вопрос: **что считается «нормальным исходом», а что — исключением?**

- Пользователь не нашёл товар → **не исключение** (или `Result::notFound()`).
- База недоступна → **исключение**.
- Счёт нельзя дебетировать при балансе 0 → **доменное исключение** (или `Result::rejected()`).
- JSON из API мусорный → **исключение на границе**, не в домене.

---

## Иерархия: три слоя, три типа исключений

```
Throwable
├── DomainException          ← БАЗОВЫЙ ДЛЯ ДОМЕНА. Никогда не содержит инфраструктурные детали.
│   ├── ValidationException  ← "email не валидный", "сумма отрицательная"
│   ├── StateException       ← "заказ уже доставлен, нельзя отменить"
│   ├── NotFoundException    ← "товар #123 не существует"
│   └── BusinessRuleException ← "лимит превышен", "двойное начисление"
│
├── InfrastructureException  ← БАЗОВЫЙ ДЛЯ ИНФРАСТРУКТУРЫ. Содержит детали: драйвер, URL, SQL.
│   ├── DatabaseException
│   ├── HttpException
│   ├── FilesystemException
│   └── SerializerException
│
└── ApplicationException     ← БАЗОВЫЙ ДЛЯ APPLICATION LAYER. Оркестрация, транзакции.
    ├── TransactionException
    └── ConcurrencyException
```

**Правило:** доменный код **никогда** не знает про `DatabaseException`, `HttpException` или `Doctrine\ORM\OptimisticLockException`. Он знает только про `DomainException` и его наследников.

---

## Контракт метода: что бросает, что возвращает

### Доменная сущность

```php
final class Order
{
    public function __construct(
        private readonly string $id,
        private readonly array $items,
        private OrderStatus $status,
    ) {}

    /**
     * @throws ValidationException  если items пуст
     * @throws StateException       если статус не Pending
     */
    public function cancel(string $reason): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException(
                message: sprintf('Cannot cancel order %s: status is %s, expected %s',
                    $this->id, $this->status->value, OrderStatus::Pending->value),
                orderId: $this->id,
                currentStatus: $this->status,
            );
        }

        $this->status = OrderStatus::Cancelled;
        $this->cancelReason = $reason;
    }
}
```

**Что здесь контракт:**
- `cancel()` бросает **только** `StateException` (и `ValidationException`, если бы валидировал).
- `StateException` несёт **контекст**: `orderId`, `currentStatus` — это данные, а не строка.
- Метод **не возвращает `bool`**. Если отмена успешна — нет исключения. Если нет — исключение. Вызывающий код не обязан проверять `if (!$result)`.

### Repository (инфраструктурная граница)

```php
interface OrderRepository
{
    /**
     * @throws NotFoundException  если заказ не найден
     * @throws InfrastructureException  если БД недоступна (обёртка над Doctrine/SQL)
     */
    public function findById(string $id): Order;

    /**
     * @throws InfrastructureException  если не удалось сохранить
     */
    public function save(Order $order): void;
}
```

**Что здесь контракт:**
- `findById` **не возвращает `?Order`**. Нет товара → `NotFoundException`. Это упрощает вызывающий код: после вызова у вас **гарантированно** есть `Order`.
- `InfrastructureException` — **не** `Doctrine\DBAL\Exception`. Домен не знает про Doctrine.

### Application service (оркестрация)

```php
final class CancelOrderService
{
    public function __construct(
        private OrderRepository $repo,
        private NotificationSender $notifier,
        private LoggerInterface $logger,
    ) {}

    /**
     * Бросает DomainException (StateChanged, NotFound).
     * НЕ бросает InfrastructureException наружу — оборачивает.
     */
    public function cancel(string $orderId): void
    {
        $order = $this->repo->findById($orderId);  // может бросить NotFoundException
        $order->cancel('requested by user');        // может бросить StateException

        try {
            $this->repo->save($order);
        } catch (InfrastructureException $e) {
            // Логируем с контекстом, бросаем Application-level
            $this->logger->error('Failed to save cancelled order', [
                'order_id' => $orderId,
                'cause' => $e->getMessage(),
            ]);
            throw new ApplicationException(
                message: 'Failed to persist order cancellation',
                orderId: $orderId,
            ) from $e;  // ← 'from' сохраняет original exception в $previous
        }

        $this->notifier->send('order_cancelled', ['id' => $orderId]);
    }
}
```

**Ключевое здесь:** `catch (InfrastructureException) → throw new ApplicationException ... from $e`.

- Мы **не** прокидываем `Doctrine\DBAL\Exception` наружу.
- Мы **сохраняем** оригинал через `from` — стек-трейс и причина не теряются.
- Мы **логируем** на границе, где «домен становится инфраструктурой».

---

## Оборачивание: где да, где нет

### Оборачиваем

| Ситуация | Почему |
|---|---|
| `Doctrine\DBAL\Exception` → `InfrastructureException` | Домен не знает про Doctrine. |
| `GuzzleHttp\Exception\BadResponseException` → `InfrastructureException` | Домен не знает про Guzzle. |
| `InfrastructureException` → `ApplicationException` (на application layer) | Вызывающий код не обязан знать, что упала БД, а не Redis. |
| Ожидаемое условие → **не оборачиваем**, а бросаем конкретный доменный класс | `NotFoundException`, а не «что-то пошло не так». |

### НЕ оборачиваем

- Исключение из **того же слоя**. Если `DomainException` поднялся из домена — не ловим и не оборачиваем в application service. Пусть поднимется до handler.
- Исключение, которое **мы сами бросаем**. Не «ловим и перекидываем» — это шум.

### Антипаттерн: «лестница из try/catch»

```php
// ПЛОХО
public function cancel(string $id): void {
    try {
        $order = $this->repo->findById($id);
    } catch (Exception $e) {
        throw new Exception('Failed to find order') from $e;
    }

    try {
        $order->cancel('reason');
    } catch (Exception $e) {
        throw new Exception('Failed to cancel order') from $e;
    }

    try {
        $this->repo->save($order);
    } catch (Exception $e) {
        throw new Exception('Failed to save order') from $e;
    }
}

// ХОРОШО: только одна точка, где «инфраструктура → домен»
public function cancel(string $id): void {
    $order = $this->repo->findById($id);   // NotFoundException — доменное, не ловим
    $order->cancel('reason');              // StateException — доменное, не ловим
    $this->saveWithRetry($order);          // единственное место, где ловим InfrastructureException
}
```

---

## Антипаттерны

### 1. Пустой catch

```php
try {
    $this->repo->save($order);
} catch (Exception $e) {
    // «разберёмся потом»
}
// $order не сохранён, но вызывающий код этого не знает.
// Худший антипаттерн. Точка.
```

### 2. `catch (\Throwable)` в бизнес-логике

```php
try {
    $this->cancelOrder($id);
} catch (\Throwable $e) {
    return false;
}
```

Ловим **всё**: `DomainException`, `InfrastructureException`, `Throwable`, `Error`, `StackOverflowError`. И возвращаем `false` — вызывающий код не может отличить «заказ уже отменён» от «база упала».

**Правильно:** ловим только то, что ожидаем. Остальное — поднимается.

### 3. Исключения для flow control

```php
public function findBestPrice(Product $product): Price {
    try {
        return $this->warehousePrice($product);
    } catch (NotFoundException) {
        return $this->marketplacePrice($product);
    }
}
```

«Товара нет на складе → попробуем маркетплейс». Это **нормальный исход**, а не исключение. Здесь уместен `Result::ok($price)` / `Result::notFound()`, или просто `?Price`.

**Правило:** если вы ловите исключение, чтобы **продолжить работу** (а не чтобы **обработать ошибку**), это, скорее всего, не исключение.

### 4. Бросание `\Exception` или `\RuntimeException` «вообще»

```php
if (!$user->isActive) {
    throw new \RuntimeException('User is not active');
}
```

- `\RuntimeException` не несёт контекста.
- Вызывающий код не может `catch (InactiveUserException $e)` — ему приходится ловить `\RuntimeException` и парсить строку.
- Статический анализатор не может проверить, что все места обработаны.

**Правильно:** `throw new InactiveUserException(userId: $user->id, reason: 'deactivated by admin');`

### 5. «Исключение с кодом ошибки» вместо типа

```php
throw new \Exception('ORDER_042_LIMIT_EXCEEDED');
```

Теперь у вас **одна** класс, а **N** разных смысловых ошибок, различаемых **строкой**. Вызывающий код делает:

```php
catch (\Exception $e) {
    if ($e->getMessage() === 'ORDER_042_LIMIT_EXCEEDED') { … }
    elseif ($e->getMessage() === 'ORDER_043_DOUBLE_CHARGE') { … }
}
```

Это **анти-паттерн**. Каждый смысловой случай — свой класс.

### 6. Бросание из деструктора / `__destruct`

```php
class Resource {
    public function __destruct() {
        if (!$this->closed) {
            throw new \RuntimeException('Resource not closed');
        }
    }
}
```

В PHP исключение из `__destruct` **проигнорируется** (если нет активного try/catch) или **превратит warning в fatal error**. Это ловушка. Закрывайте ресурсы явно, используйте `final` + `__destruct` только как safety net, **не** как контракт.

### 7. Catch и `throw` без добавления контекста

```php
try {
    $this->repo->save($order);
} catch (InfrastructureException $e) {
    throw new InfrastructureException($e->getMessage()); // тот же класс, та же строка
}
```

Это **ничего не добавляет**. Либо добавьте контекст (`orderId`, `action`), либо не ловите вообще.

### 8. «Исключение вместо `void` + flag»

```php
// ПЛОХО: метод возвращает bool, но вызывающий код не знает почему false
public function applyDiscount(Discount $d): bool { … }

// ХОРОШЕЕ: если false — это ошибка, а не «не применили»
public function applyDiscount(Discount $d): void {
    if ($this->total - $d->amount < 0) {
        throw new BusinessRuleException('Discount exceeds order total', …);
    }
    // …
}
```

---

## Result pattern: когда он лучше исключений

Для **ожидаемых** условий (товар не найден, лимит превышен) исключение — это «удорожание»: `try/catch` + стек-трейс + аллокация объекта. Для горячего пути (поиск по каталогу, проверка наличия) это дорого.

```php
readonly class Result {
    private function __construct(
        private readonly bool $success,
        private readonly ?object $value,
        private readonly ?string $error,
    ) {}

    public static function ok(object $value): self {
        return new self(true, $value, null);
    }

    public static function fail(string $error): self {
        return new self(false, null, $error);
    }

    public function value(): object {
        if (!$this->success) {
            throw new UnexpectedValueException('Called value() on failed Result: ' . $this->error);
        }
        return $this->value;
    }
}
```

**Когда Result:** поиск, фильтрация, проверка доступности — «не нашлось» — норма.
**Когда exception:** нарушение инварианта домена, ошибка инфраструктуры — «не должно было случиться».

В реальном Symfony-проекте я бы **не** вводил `Result` повсеместно. Он уместен в 2–3 местах на горячем пути. В остальных — исключения, потому что PHP-экосистема (Doctrine, Symfony, PHPUnit) построена на них.

---

## Глобальный handler (Symfony-style)

```php
// config/packages/exception.yaml (псевдокод)
# Symfony ExceptionListener → Controller → HttpKernel

// Custom handler
final class ExceptionLogger implements EventSubscriberInterface {
    public function onKernelException(KernelExceptionEvent $event): void {
        $e = $event->getThrowable();

        $logLevel = match (true) {
            $e instanceof DomainException  => LogLevel::WARNING,   // ожидаемое
            $e instanceof InfrastructureException => LogLevel::ERROR, // нештатное
            default => LogLevel::CRITICAL,                          // неизвестное
        };

        $this->logger->log($logLevel, (string) $e, [
            'class' => $e::class,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'previous' => $e->getPrevious()?->getMessage(),
        ]);

        // Не меняем ответ, если Symfony уже его сформировал
    }
}
```

---

## Итоговая таблица

| Вопрос | Ответ |
|---|---|
| Что бросает домен? | `DomainException` и наследники. Только доменные. |
| Что бросает инфраструктура? | `InfrastructureException` и наследники. Обёртка над чужими. |
| Что бросает application layer? | `DomainException` (прокидывает) или `ApplicationException` (оборачивает инфраструктурные). |
| Что **не** бросает ни один слой? | `\Exception`, `\RuntimeException` без наследования от своих базовых. |
| Оборачиваем ли чужие? | Да, на границе слоя. `from $e` сохраняет оригинал. |
| Ловим ли в бизнес-логике? | Только `DomainException` конкретных типов. `catch (\Throwable)` — нет. |
| Когда Result? | Горячий путь, ожидаемое «не найдено». В остальных местах — exception. |
| Пустой catch? | Никогда. Если не знаете, что делать — логируйте и re-throw. |

> ⏱ 34.3s · prompt 6577 tok · output 4451 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о наиболее часто нарушаемом принципе SOLID в реальном PHP-коде. Давай подумаю, что действительно чаще всего ломается.

Кандидаты:
1. **S — Single Responsibility** — очень часто нарушается, но это скорее "фuzzy" принцип, спорный.
2. **O — Open/Closed** — нарушается через if/else цепочки, но это скорее следствие.
3. **L — Liskov Substitution** — нарушается через "fat parent class" и наследование для кода, а не для расширения.
4. **I — Interface Segregation** — нарушается через "fat interfaces", но реже в PHP.
5. **D — Dependency Inversion** — нарушается через прямой new, через static-вызовы, через "сервис-локеры".

Что реально чаще всего вижу в PHP-коде:

**S (Single Responsibility)** — это "класс сделал слишком много". Но это субъективно.

**L (Liskov)** — на самом деле в PHP очень часто нарушается. Типовой пример: базовый класс `PaymentProcessor` с методом `process()`, наследники `CreditCardProcessor`, `BankTransferProcessor`, `CryptoProcessor` — и в каждом `process()` есть свой `if ($this->is3ds) { … }` или `if ($this->currency !== 'USD') { … }`. Клиентский код ожидает, что любой `PaymentProcessor` ведёт себя одинаково, но на деле каждый наследник имеет свои "особенности". Это классическое нарушение LSP.

Но ещё более типовой и частый пример — **нарушение D (Dependency Inversion)** через:
- `new Repository()` прямо в контроллере/сервисе
- Статические вызовы `App::db()->query(...)`
- "Service locator" — `$container->get('payment.gateway')` внутри метода
- Контроллер, который сам ходит в БД, сам шлёт письма, сам считает налоги

Но D — это скорее архитектурный вопрос, а не "принцип в коде метода".

Я думаю, самый честный ответ: **LSP (Liskov Substitution Principle)** нарушается чаще всего именно в PHP, потому что:
1. PHP имеет наследование, и люди используют его как "шаблон с переопределением", а не как "расширение поведения".
2. PHP имеет `final` классы и трейты, но культура "базовый класс → наследники" всё равно сильна (особенно в старых кодовыхбазах, в Laravel/Eloquent).
3. Типовой пример: `AbstractRepository` или `BaseController` с "жирным" API, где наследники нарушают предположения.
4. Ещё один типовой пример: `PaymentProcessor` с наследниками, где каждый имеет свою логику, а общий код делает `if ($processor instanceof CryptoProcessor) { … }`.

Но подожди — есть ещё один очень частый: **SRP** нарушается через "бог-класс". Контроллер на 500 строк, который и парсит запрос, и валидирует, и ходит в БД, и шлёт письма, и рендерит шаблон. Это, пожалуй, самый массовый антипаттерн.

Но SRP — это "один класс, одна ответственность". Это субъективно. LSP — более конкретный и проверяемый принцип.

Я склоняюсь к **LSP** как наиболее часто нарушаемому и наиболее "тихому" — его нарушение не бросает исключений, не падает, просто создаёт хрупкий код.

Типовой пример для PHP:

**Нарушение:** Базовый класс `NotificationSender` с методом `send()`. Наследники: `EmailSender`, `SmsSender`, `PushSender`. У каждого `send()` — своя сигнатура по факту (Email принимает `Address`, Sms — `PhoneNumber`, Push — `DeviceToken`). Базовый класс пытается обобщить. Клиентский код делает:

```php
$sender = new EmailSender($mailgun);
$sender->send($message);
```

Но потом добавляют `SmsSender`, и `send()` в нём принимает другие параметры. Или в базовом классе есть `getChannel()`, а в `SmsSender` он возвращает что-то несовместимое.

Ещё более типовой и конкретный пример: **`AbstractRepository`** в Doctrine-стиле, где:

```php
abstract class BaseRepository {
    protected function findActive(string $type): array {
        return $this->createQueryBuilder('e')
            ->where('e.active = :active')
            ->andWhere('e.type = :type')
            ->setParameter('active', true)
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}

class UserRepository extends BaseRepository {
    // Здесь $type — строка "user"
}

class ProductRepository extends BaseRepository {
    // Здесь $type — int(5), потому что у товаров тип — числовой
}
```

Наследник `ProductRepository` нарушает LSP: `findActive(string $type)` в базовом классе ожидает строку, а в наследнике фактический смысл параметра — int. Клиентский код, работающий с `BaseRepository`, не может предположить, что `findActive(42)` будет работать.

Хм, это пример с параметрами. Давай сделаю более классический LSP-нарушение.

**Классический пример LSP в PHP:**

```php
abstract class Shape {
    abstract public function area(): float;
    abstract public function perimeter(): float;
}

class Rectangle extends Shape {
    public function __construct(private float $w, private float $h) {}
    public function area(): float { return $this->w * $this->h; }
    public function perimeter(): float { return 2 * ($this->w + $this->h); }
}

class Square extends Shape {
    public function __construct(private float $side) {}
    public function area(): float { return $this->side ** 2; }
    public function perimeter(): float { return 4 * $this->side; }
}
```

Казалось бы, `Square` — правильный наследник `Shape`. Но:

```php
function draw(Shape $s): void {
    // Ожидает, что $s — произвольный Shape
    if ($s instanceof Square) {
        // «Особый случай» — это уже нарушение LSP
        drawSquare($s);
    } else if ($s instanceof Rectangle) {
        drawRectangle($s);
    }
}
```

Или ещё хуже:

```php
class Square extends Rectangle {
    public function __construct(private float $side) {
        parent::__construct($side, $side);
    }
    
    public function setWidth(float $w) {
        parent::setWidth($w);
        parent::setHeight($w);  // Нарушение инварианта!
    }
    
    public function setHeight(float $h) {
        parent::setWidth($h);
        parent::setHeight($h);  // Нарушение инварианта!
    }
}
```

Это классический пример LSP (из "Fighting the Legacy Code" и подобных). Но это Java-стиль. В PHP это тоже встречается.

Давай сделаю пример, более конкретный для PHP/Symfony:

**Пример: `AbstractPaymentGateway`**

```php
abstract class AbstractPaymentGateway {
    abstract public function charge(ChargeRequest $request): ChargeResult;
    abstract public function refund(string $chargeId, float $amount): RefundResult;
    abstract public function supports3DS(): bool;

    protected function validateRequest(ChargeRequest $r): void {
        if ($r->amount <= 0) throw new \DomainException('Amount must be positive');
        if ($r->currency === '') throw new \DomainException('Currency required');
    }
}

class StripeGateway extends AbstractPaymentGateway {
    public function charge(ChargeRequest $r): ChargeResult {
        $this->validateRequest($r);
        // Stripe-специфичная логика
    }
    public function refund(string $id, float $amount): RefundResult {
        // Stripe: полный или частичный, с idempotency key
    }
    public function supports3DS(): bool { return true; }
}

class BankTransferGateway extends AbstractPaymentGateway {
    public function charge(ChargeRequest $r): ChargeResult {
        $this->validateRequest($r);
        // Bank transfer: нет 3DS, нет idempotency, есть SLA 3 дня
        // Но validateRequest требует currency — а у bank transfer currency может быть null?
    }
    public function refund(string $id, float $amount): RefundResult {
        // Bank transfer: только полный refund, частичный не поддерживается!
        // Но сигнатура говорит "float $amount" — частичный!
        if ($amount < $this->originalAmount) {
            throw new \DomainException('Partial refunds not supported');
        }
    }
    public function supports3DS(): bool { return false; }
}
```

Теперь клиентский код:

```php
function processPayment(PaymentGateway $gateway, ChargeRequest $req): ChargeResult {
    $result = $gateway->charge($req);
    
    if ($gateway->supports3DS() && $result->requires3DS) {
        // 3DS flow
    }
    
    // Если нужно отменить:
    $gateway->refund($result->id, $req->amount); // Может бросить для BankTransfer!
    
    return $result;
}
```

Здесь нарушение LSP: `BankTransferGateway::refund()` бросает исключение для частичного возврата, хотя базовый класс и сигнатура не обещают такого. `Square extends Rectangle` аналог.

Это хороший пример. Давай сделаю его конкретным и покажу рефакторинг.

Рефакторинг:
1. Убрать `BankTransferGateway extends AbstractPaymentGateway` — они не подменяемы.
2. Ввести capability interfaces: `Chargeable`, `Refundable`, `PartiallyRefundable`, `ThreeDSCapable`.
3. Клиентский код работает с интерфейсами, а не с базовым классом.
4. Использовать composition вместо inheritance.

Давай напишу это.

Структура ответа:
1. Короткий ответ: LSP, почему.
2. Типовой пример нарушения (конкретный PHP-код).
3. Почему это нарушение LSP (разбор).
4. Рефакторинг (composition + capability interfaces).
5. Короткая проверка: что изменилось.

Буду писать на русском, как и весь диалог. Код — на PHP.

</details>

**Кандидат:**

## LSP нарушается чаще всего — и тише всех

Из пяти принципов SOLID именно **Liskov Substitution Principle** я вижу нарушенным чаще всего в живом PHP-коде — и именно его нарушение самое «тихое»: код компилируется, тесты зелёные, а через три месяца кто-то добавляет новый наследник, и половина вызывающих мест начинает вести себя не так, как ожидалось.

Почему LSP, а не SRP или DIP?

- **SRP** — субъективен. «Один класс — одна ответственность» каждый понимает по-своему. Спорный, но не ломает код.
- **DIP** — чаще нарушается архитектурно (static-вызовы, service locator), и это видно сразу.
- **LSP** — нарушается **внутри наследования**, которое выглядит «правильно»: есть базовый класс, есть наследники, есть polymorphism. Но наследники **меняют предположения**, на которые опирается клиентский код. И это не видно из сигнатуры.

---

## Типовой пример: `AbstractPaymentGateway`

Классическая ситуация: есть абстрактный базовый класс, два «наследника-провайдера», и клиентский код, который обращается к базовому типу.

```php
// ─── Базовый класс ───────────────────────────────────────────
abstract class AbstractPaymentGateway
{
    abstract public function charge(ChargeRequest $req): ChargeResult;
    abstract public function refund(string $chargeId, float $amount): RefundResult;
    abstract public function supports3DS(): bool;

    protected function validate(ChargeRequest $r): void
    {
        if ($r->amount <= 0)   throw new \DomainException('Amount must be > 0');
        if ($r->currency === '') throw new \DomainException('Currency required');
    }
}

// ─── Stripe ──────────────────────────────────────────────────
class StripeGateway extends AbstractPaymentGateway
{
    public function __construct(private \Stripe\StripeClient $client) {}

    public function charge(ChargeRequest $req): ChargeResult
    {
        $this->validate($req);
        $stripeCharge = $this->client->charges->create([
            'amount'   => (int) round($req->amount * 100),
            'currency' => strtolower($req->currency),
            // …
        ]);
        return new ChargeResult($stripeCharge->id, 'succeeded');
    }

    public function refund(string $chargeId, float $amount): RefundResult
    {
        // Stripe: частичный refund — ок. Полный — ок.
        $this->client->refunds->create([
            'charge' => $chargeId,
            'amount' => (int) round($amount * 100),
        ]);
        return new RefundResult($chargeId, 'completed');
    }

    public function supports3DS(): bool { return true; }
}

// ─── Bank Transfer ───────────────────────────────────────────
class BankTransferGateway extends AbstractPaymentGateway
{
    public function __construct(private BankClient $bank) {}

    public function charge(ChargeRequest $req): ChargeResult
    {
        $this->validate($req);
        // Bank transfer: нет 3DS, SLA — до 3 дней
        $id = $this->bank->initTransfer($req->amount, $req->currency);
        return new ChargeResult($id, 'pending');
    }

    public function refund(string $chargeId, float $amount): RefundResult
    {
        // ⚠️ Bank transfer: ТОЛЬКО полный возврат.
        // Но сигнатура говорит "float $amount" — как будто частичный тоже можно.
        $original = $this->bank->getOriginalAmount($chargeId);
        if ($amount < $original) {
            throw new \DomainException('Partial refunds are not supported by bank transfer');
        }
        $this->bank->initRefund($chargeId, $amount);
        return new RefundResult($chargeId, 'completed');
    }

    public function supports3DS(): bool { return false; }
}
```

### Клиентский код, который «верит» базовому классу

```php
final class PaymentService
{
    public function __construct(
        private AbstractPaymentGateway $gateway,  // ← базовый тип
        private NotificationSender $notifier,
    ) {}

    public function processAndOptionallyRefund(ChargeRequest $req): void
    {
        $result = $this->gateway->charge($req);

        // 3DS flow — ок, supports3DS() говорит нам
        if ($this->gateway->supports3DS() && $result->status === 'pending_3ds') {
            $this->trigger3DSChallenge($result);
        }

        // Клиент передумал → частичный возврат 50%
        $refundAmount = $req->amount * 0.5;
        $this->gateway->refund($result->id, $refundAmount);  // 💥

        $this->notifier->send('refund_partial', ['id' => $result->id]);
    }
}
```

### Где сломалось

`PaymentService` был написан против **базового контракта**: «у меня `AbstractPaymentGateway`, я могу сделать частичный refund, потому что сигнатура принимает `float $amount`». Это **разумное предположение**, вытекающее из LSP: любой наследник должен вести себя как базовый класс.

Но `BankTransferGateway` **меняет предположение**: частичный refund бросает `\DomainException`. Клиентский код не знает об этом. Ни из сигнатуры, ни из PHPDoc. Просто «вдруг упадёт».

Это и есть нарушение LSP:

> Если `BankTransferGateway extends AbstractPaymentGateway`, то везде, где ожидается `AbstractPaymentGateway`, `BankTransferGateway` должен быть **полностью подменяем**, не нарушая корректности клиентского кода.

`BankTransferGateway` — **не подменяем**: он бросает исключение в ситуации, которую базовый класс считает легальной.

---

## Почему именно LSP так легко нарушить

1. **Наследование в PHP — «удобное».** `extends` одна строка. Трейт — одна строка. И никто не думает: «а клиентский код на самом деле полагается на поведение базового класса?»

2. **Сигнатура не несёт семантики.** `refund(string $id, float $amount): RefundResult` — в ней нет слова «только полный». PHPDoc `@throws` может, но:
   - люди забывают писать;
   - PHPStan/Psalm **не проверяют** `@throws` на логическую полноту;
   - клиентский код всё равно не `catch`'ит `\DomainException` «на всякий случай», потому что не знает, что именно бросит.

3. **`instanceof`-ветвления маскируют проблему.** Часто «решают» так:

```php
if ($this->gateway instanceof BankTransferGateway) {
    // отдельная логика
}
```

Это **не** LSP-подмена. Это if/else под видом полиморфизма. И с каждым новым наследником ветвлений больше.

4. **Тесты проходят.** `PaymentServiceTest` использует `StripeGateway` (mock или реальный). `BankTransferGateway` тестируют отдельно. Никогда не тестируют «PaymentService с BankTransferGateway» — потому что разработчик **не считает их взаимозаменяемыми**, хотя типизация говорит обратное.

---

## Рефакторинг: убить ложное наследование

Идея: **не наследуем то, что не подменяется.** Вместо одного «бог-класса» — capability-интерфейсы.

### Шаг 1: capability-интерфейсы

```php
interface Chargeable
{
    public function charge(ChargeRequest $req): ChargeResult;
}

interface Refundable
{
    public function refund(string $chargeId, RefundRequest $req): RefundResult;
}

interface PartiallyRefundable extends Refundable
{
    // Маркер: частичный refund поддерживается
}

interface ThreeDSCapable
{
    public function initiate3DSChallenge(ChargeResult $result): ThreeDSChallenge;
}
```

Обратите внимание: `RefundRequest` — **отдельная структура**, а не «float $amount».

```php
readonly class RefundRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly bool  $partial,   // явный флаг
    ) {}

    public static function full(float $amount): self {
        return new self($amount, partial: false);
    }

    public static function partial(float $amount): self {
        return new self($amount, partial: true);
    }
}
```

### Шаг 2: каждый провайдер реализует только то, что может

```php
final class StripeGateway implements Chargeable, PartiallyRefundable, ThreeDSCapable
{
    public function __construct(private \Stripe\StripeClient $client) {}

    public function charge(ChargeRequest $req): ChargeResult
    {
        $this->validate($req);
        // …
        return new ChargeResult($id, 'succeeded');
    }

    public function refund(string $chargeId, RefundRequest $req): RefundResult
    {
        // Stripe: полный и частичный — оба ок.
        // Не нужно проверять $req->partial — Stripe сам знает.
        // …
        return new RefundResult($chargeId, 'completed');
    }

    public function initiate3DSChallenge(ChargeResult $result): ThreeDSChallenge
    {
        // …
    }

    private function validate(ChargeRequest $r): void { /* … */ }
}

final class BankTransferGateway implements Chargeable, Refundable  // НЕ PartiallyRefundable
{
    public function __construct(private BankClient $bank) {}

    public function charge(ChargeRequest $req): ChargeResult
    {
        $this->validate($req);
        // …
        return new ChargeResult($id, 'pending');
    }

    public function refund(string $chargeId, RefundRequest $req): RefundResult
    {
        // Контракт Refundable: полный возврат.
        // Если partial — это не наша ответственность,
        // потому что мы НЕ реализуем PartiallyRefundable.
        $this->bank->initRefund($chargeId, $req->amount);
        return new RefundResult($chargeId, 'completed');
    }

    private function validate(ChargeRequest $r): void { /* … */ }
}
```

**Ключевое:** `BankTransferGateway` **не реализует** `PartiallyRefundable`. Это не «бросаем исключение при частичном refund» — это **«мы не можем частичный refund, точка»**. Типизация это фиксирует.

### Шаг 3: клиентский код работает с capability, а не с «базовым классом»

```php
final class PaymentService
{
    public function __construct(
        private Chargeable $gateway,
        private ?PartiallyRefundable $partialRefunder,  // null = не поддерживается
        private ?ThreeDSCapable $threeDS,
        private NotificationSender $notifier,
    ) {}

    public function process(ChargeRequest $req, bool $allowPartialRefund = false): void
    {
        $result = $this->gateway->charge($req);

        // 3DS — только если capability есть
        if ($this->threeDS !== null && $result->status === 'pending_3ds') {
            $this->threeDS->initiate3DSChallenge($result);
        }

        // Частичный refund — только если capability есть
        if ($allowPartialRefund) {
            if ($this->partialRefunder === null) {
                throw new UnsupportedCapabilityException(
                    'Partial refund not supported by this gateway',
                    gateway: $this->gateway::class,
                );
            }
            $this->partialRefunder->refund(
                $result->id,
                RefundRequest::partial($req->amount * 0.5),
            );
        } else {
            // Полный refund — Refundable есть у всех, кто может refund
            // Но у нас только Chargeable — значит, проверяем отдельно
            if ($this->partialRefunder instanceof Refundable) {
                $this->partialRefunder->refund(
                    $result->id,
                    RefundRequest::full($req->amount),
                );
            }
        }

        $this->notifier->send('processed', ['id' => $result->id]);
    }
}
```

Теперь:

- Если `PaymentService` сконфигурирован с `BankTransferGateway`, то `$this->partialRefunder` будет `null`. Частичный refund — **явное исключение** `UnsupportedCapabilityException`, а не «вдруг упадёт `DomainException` из глубинки».
- `StripeGateway` — `$this->partialRefunder` — это сам `StripeGateway`. Частичный refund работает.
- **Нет if/else по классам.** Нет `instanceof`. Есть `null` / `object` и capability.
- Новый провайдер (назовём его `CryptoGateway`, который поддерживает зарядку, но **не** поддерживает refund вообще) — просто `implements Chargeable`. Конец. Не нужно «наследовать от `AbstractPaymentGateway` и переопределить `refund()` так, чтобы он бросал `UnsupportedOperationException`».

### Шаг 4: DI-конфигурация (Symfony)

```yaml
# config/services.yaml
payment.service:
    class: App\Service\PaymentService
    arguments:
        $gateway:           '@payment.gateway'           # Chargeable
        $partialRefunder:   '@?payment.partial_refunder' # null, если нет
        $threeDS:           '@?payment.3ds'              # null, если нет
        $notifier:          '@notification.sender'

# В environment, где Stripe:
payment.gateway:
    class: App\Gateway\StripeGateway
    tags:
        - { name: app.gateway, alias: chargeable }

payment.partial_refunder:
    class: App\Gateway\StripeGateway   # тот же объект, но роль «partial refunder»

payment.3ds:
    class: App\Gateway\StripeGateway

# В environment, где Bank Transfer:
payment.gateway:
    class: App\Gateway\BankTransferGateway
    # payment.partial_refunder — ОТСУТСТВУЕТ → null
    # payment.3ds — ОТСУТСТВУЕТ → null
```

`@?` — Symfony-синтаксис «если сервис не зарегистрирован — `null`».

---

## Что изменилось

| До (наследование) | После (capability) |
|---|---|
| `BankTransferGateway extends AbstractPaymentGateway` | `BankTransferGateway implements Chargeable, Refundable` |
| Частичный refund → `throw \DomainException` из глубинки | Частичный refund → `UnsupportedCapabilityException` **на границе**, с понятным сообщением |
| Клиентский код: `if ($gw instanceof BankTransferGateway)` | Клиентский код: `if ($this->partialRefunder === null)` |
| Новый провайдер = новый наследник + переопределение | Новый провайдер = `implements` нужных интерфейсов. Конец. |
| LSP: формально нарушен, но «тесты зелёные» | LSP: не нарушен — каждый интерфейс содержит **только** то, что реализуемо |
| `AbstractPaymentGateway` — «бог-класс» с `validate()`, `supports3DS()` | Нет базового класса. Есть 3–4 маленьких интерфейса |

---

## Честное замечание

Этот рефакторинг **не бесплатный**. Capability-интерфейсы + `?null` в DI + отдельный `RefundRequest` — это больше кода, чем один `abstract class`. Для двух провайдеров это оверкилл.

**Когда это оправдано:**
- Провайдеров ≥ 3 и их набор возможностей **не совпадает** (у A есть 3DS, у B нет; у C есть partial refund, у D нет).
- Клиентский код **обращается к базовому типу** и принимает допущения.
- Появился `instanceof` в клиентском коде — это **красный флаг**.

**Когда это перебор:**
- Один провайдер, может быть два. Просто `class StripeGateway { … }`. Без наследования.
- Все провайдеры **полностью совместимы** по API. Тогда `abstract class` — нормально, LSP не нарушается.

Правило простое: **если вы ловите себя на мысли «ну, этот метод в наследнике бросит исключение, но в основном он работает» — вы нарушаете LSP.**Capability-интерфейсы — самый прямой способ это выразить.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Короткий ответ

Да. SOLID — это эвристики, а не законы. Каждое правило снижает один класс проблем и создаёт другой. Соблюдать SOLID **ради SOLID** — то же самое, что оптимизировать ради оптимизации: вы платите цену, а выгоду не получили.

Ниже — два конкретных примера, где нарушение объективно лучше соблюдения.

---

## Пример 1: нарушение SRP — «толстый» класс, который никто не хочет разрезать

### Что говорит SRP

Один класс — одна причина для изменения. Если класс делает пять вещей — разбейте на пять.

### Что говорит практика

Иногда «пять вещей» — это **один объект**, пять шагов которого имеют смысл только вместе. Разрезание создаёт **object explosion**: больше файлов, больше импортов, больше `new`, больше навигации — при нулевом выигрыше в переиспользовании.

### Конкретный код

Задача: сформировать и отправить e-mail «Ваш заказ отменён».

#### «Правильное по SRP» — 5 классов

```php
// App/Notification/OrderCancelledEmail.php  (1)
final class OrderCancelledEmail
{
    public function __construct(
        private SubjectBuilder $subject,
        private BodyBuilder $body,
        private MailerInterface $mailer,
    ) {}

    public function send(Order $order, User $user): void
    {
        $this->mailer->send(
            to: $user->email,
            subject: $this->subject->build($order),
            html: $this->body->build($order),
        );
    }
}

// App/Notification/SubjectBuilder.php  (2)
final class SubjectBuilder
{
    public function build(Order $order): string
    {
        return sprintf('Заказ %s отменён', $order->id);
    }
}

// App/Notification/BodyBuilder.php  (3)
final class BodyBuilder
{
    public function build(Order $order): string
    {
        $lines = [];
        $lines[] = '<h2>Заказ ' . $order->id . ' отменён</h2>';
        $lines[] = '<p>Причина: ' . e($order->cancelReason) . '</p>';
        foreach ($order->items as $item) {
            $lines[] = sprintf('<li>%s × %d — %s</li>',
                e($item->name), $item->qty, $item->price->format());
        }
        $lines[] = '<p>Деньги вернутся в течение 3–5 дней.</p>';
        return implode("\n", $lines);
    }
}

// App/Notification/MailerInterface.php  (4)
interface MailerInterface
{
    public function send(string $to, string $subject, string $html): void;
}

// App/Notification/SymfonyMailer.php  (5)
final class SymfonyMailer implements MailerInterface
{
    public function __construct(private \Symfony\Component\Mailer\MailerInterface $mailer) {}
    // …
}
```

Плюс 5 записей в `services.yaml`, 5 импортов, 5 `new`/инъекций. Для **одного** e-mail.

#### «Нарушение SRP» — 1 класс

```php
final class OrderCancelledEmail
{
    public function __construct(
        private \Symfony\Component\Mailer\MailerInterface $mailer,
    ) {}

    public function send(Order $order, User $user): void
    {
        $subject = sprintf('Заказ %s отменён', $order->id);

        $lines = ['<h2>Заказ ' . $order->id . ' отменён</h2>'];
        $lines[] = '<p>Причина: ' . e($order->cancelReason) . '</p>';
        foreach ($order->items as $item) {
            $lines[] = sprintf('<li>%s × %d — %s</li>',
                e($item->name), $item->qty, $item->price->format());
        }
        $lines[] = '<p>Деньги вернутся в течение 3–5 дней.</p>';

        $this->mailer->send(
            to: $user->email,
            subject: $subject,
            html: implode("\n", $lines),
        );
    }
}
```

**40 строк. Один файл. Один `new`.**

### Почему это не «ленивое» нарушение

| Вопрос | Ответ |
|---|---|
| Кто ещё использует `SubjectBuilder`? | Никто. Только этот e-mail. |
| Кто ещё использует `BodyBuilder`? | Никто. Только этот e-mail. |
| Поменяется ли subject-логика независимо от body? | Нет. Это один шаблон. |
| Будет ли 5-й e-mail, где нужен `SubjectBuilder`, но не `BodyBuilder`? | Маловероятно. У каждого e-mail свой формат. |

Когда ответ на все вопросы — «нет», выделение — **ceremony**, а не архитектура. Вы платите 4 лишних файла + 4 лишних импорта + 4 лишних записи в DI, чтобы формально не нарушать SRP.

### Когда SRP **обязан**

А вот если у вас **10 e-mail уведомлений** (отмена, доставка, смена адреса, возврат, чарджбек…), и `BodyBuilder` начинает принимать 6 опциональных параметров (`$showItems`, `$showRefundInfo`, `$showTracking`, `$showSupportLink`, `$accentColor`, `$footerText`), а `SubjectBuilder` — `match` на 10 типов событий — **тогда** выделение оправдано. Не потому что «SRP требует», а потому что класс **физически не помещается** в один файл и его **фактически** начинают менять по 5 разным причинам.

**Критерий:** SRP нарушают тогда, когда класс **фактически** меняют по разным причинам. Не «могут теоретически», а «вчера меняли subject, сегодня — body, на этой неделе — шапку, и это три разные фичи от трёх разных тиков».

---

## Пример 2: нарушение DIP — `new ConcreteClass()` без интерфейса

### Что говорит DIP

Зависимости от абстракций, а не от конкретных реализаций.

### Когда это перебор

```php
final class CsvExporter
{
    public function __construct(
        private string $separator = ',',
        private string $encoding = 'UTF-8',
    ) {}

    public function export(array $rows, string $filename): void
    {
        $fh = fopen($filename, 'w');
        fputcsv($fh, array_keys($rows[0]), $this->separator);
        foreach ($rows as $row) {
            fputcsv($fh, $row, $this->separator);
        }
        fclose($fh);
    }
}
```

Использовался из **трёх** контроллеров. Один реализатор. Никогда не будет второго.

DIP-версия:

```php
interface CsvExporterInterface { public function export(array $rows, string $filename): void; }
final class CsvExporter implements CsvExporterInterface { /* тот же код */ }
// + services.yaml:
// csv_exporter: { class: App\Export\CsvExporter }
// + 3 контроллера: constructor(CsvExporterInterface $csv)
// + mock в 6 тестах
```

Для **одного** реализатора, который **никогда** не поменяется:

- интерфейс — 1 файл;
- DI-конфиг — 1 запись;
- mock в тестах — 3 лишних `getMockBuilder(CsvExporterInterface::class)`;
- вызывающий код **не может** назвать конкретный метод без интерфейса (хотя `fputcsv`-логика и так видна в реализации).

Это **ceremony без выгоды**. DIP оправдан, когда:
- реализаций ≥ 2 (CSV / Excel), или
- реализация **может** поменяется (облачный хранилище → S3), или
- вызывающий код **должен** работать без знания реализации (тестирование без реального файла).

Если ни одно не выполняется — `new CsvExporter()` **быстрее читается, быстрее пишется, проще тестируется** (тестируете `export()` с временным файлом, без mock'а).

---

## Когда нарушение **не** оправдано

Чтобы не уйти в «всё можно», вот три красных флага, при которых нарушение SOLID — **не** pragmatism, а tech debt:

1. **Нарушение, которое вы не можете объяснить в одно предложение.** «Ну, у нас так исторически сложилось» — не объяснение. «Subject и body живут вместе, потому что это один шаблон и у них нет общего потребителя, кроме этого e-mail» — объяснение.

2. **Нарушение, которое уже болит.** Если вы третий раз за месяц переносите метод между классами, потому что «не знаем, куда его положить» — SRP/LSP/DIP были бы спасением, а не бюрократией.

3. **Нарушение, которое вы не можете откатить.** Если «толстый класс» уже 2 000 строк и его меняют 5 человек параллельно — выделение **обязано** произойти. Не потому что SOLID, а потому что merge-конфликты и regression.

---

## Итог

SOLID — это **набор вопросов**, которые вы задаёте себе при проектировании:

- «По скольким причинам этот класс будет меняться?» (SRP)
- «Может ли вызывающий код сломаться, если я добавлю наследника?» (LSP)
- «Зависит ли мой код от деталей реализации, которые могут поменяются?» (DIP)
- «Может ли вызывающий код использовать часть интерфейса без лишнего?» (ISP)
- «Наследую ли я то, что не подменяется?» (LSP, снова)

Если ответ на все вопросы — «нет», и вы **осознанно** принимаете trade-off — это не нарушение. Это **решение**. И оно не хуже, чем механическое соблюдение. Хуже — соблюдение «для галочки» и нарушение «потому что лень подумать».

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## Tезис

OCP — это не про «классы наследуются». Это про **механизм расширения поведения без изменения уже работающего кода**. Наследование — один из таких механизмов, часто самый удобный, но не единственный. Ниже — шесть альтернатив, с кодом «до / после».

---

## 1. Strategy (композиция вместо наследования)

Классический случай: несколько алгоритмов, переключаемых по типу.

### «До»: наследование + `instanceof`

```php
abstract class ShippingCostCalculator
{
    abstract public function calculate(Shipment $s): Money;
}

class StandardShipping extends ShippingCostCalculator { /* … */ }
class ExpressShipping  extends ShippingCostCalculator { /* … */ }
class InternationalShipping extends ShippingCostCalculator { /* … */ }

// Где-то в сервисе:
$calc = match ($shipment->type) {
    'standard'    => new StandardShipping(),
    'express'     => new ExpressShipping(),
    'international' => new InternationalShipping(),
};
return $calc->calculate($shipment);
```

Каждый новый метод доставки — новый класс **и** новая ветка в `match`. Два места для изменения.

### «После»: композиция + DI

```php
interface ShippingCostCalculator
{
    public function calculate(Shipment $s): Money;
    public function supports(Shipment $s): bool;
}

final class StandardShipping implements ShippingCostCalculator
{
    public function supports(Shipment $s): bool { return $s->type === 'standard'; }
    public function calculate(Shipment $s): Money { /* … */ }
}

final class ExpressShipping implements ShippingCostCalculator
{
    public function supports(Shipment $s): bool { return $s->type === 'express'; }
    public function calculate(Shipment $s): Money { /* … */ }
}

final class ShippingCostService
{
    /** @param ShippingCostCalculator[] $calculators — авто-инъекция через tag */
    public function __construct(private array $calculators) {}

    public function calculate(Shipment $s): Money
    {
        foreach ($this->calculators as $calc) {
            if ($calc->supports($s)) {
                return $calc->calculate($s);
            }
        }
        throw new UnsupportedShippingType($s->type);
    }
}
```

```yaml
# services.yaml
App\Shipping\StandardShipping:
    tags: [shipping_calculator]
App\Shipping\ExpressShipping:
    tags: [shipping_calculator]
# Новый метод доставки = 1 файл + 1 строка в yaml. ShippingCostService не трогается.
```

**Что изменилось:** `ShippingCostService` **не знает** конкретных калькуляторов. Новый калькулятор = новый файл + тег. Сервис не меняется. `match` убран. `instanceof` убран.

---

## 2. Registry / Plugin (регистрация вместо `switch`)

Типичная ситуация: разные обработчики по ключу.

### «До»

```php
class PaymentProcessor
{
    public function process(Payment $p): void
    {
        switch ($p->method) {
            case 'card':       $this->processCard($p); break;
            case 'bank':       $this->processBank($p); break;
            case 'crypto':     $this->processCrypto($p); break;
            case 'giftcard':   $this->processGiftCard($p); break;
            // +5 методов, +5 веток, +5 изменений при каждом новом способе
        }
    }
    private function processCard(Payment $p): void { /* … */ }
    private function processBank(Payment $p): void { /* … */ }
    // …
}
```

### «После»

```php
interface PaymentHandler
{
    public function method(): string;
    public function handle(Payment $p): void;
}

final class CardHandler implements PaymentHandler
{
    public function method(): string { return 'card'; }
    public function handle(Payment $p): void { /* … */ }
}

final class CryptoHandler implements PaymentHandler
{
    public function method(): string { return 'crypto'; }
    public function handle(Payment $p): void { /* … */ }
}

final class PaymentProcessor
{
    /** @param array<string, PaymentHandler> $handlers — ключ = method() */
    public function __construct(
        PaymentHandler ...$handlers,
    ) {
        $this->handlers = [];
        foreach ($handlers as $h) {
            $this->handlers[$h->method()] = $h;
        }
    }

    public function process(Payment $p): void
    {
        if (!isset($this->handlers[$p->method])) {
            throw new UnknownPaymentMethod($p->method);
        }
        $this->handlers[$p->method]->handle($p);
    }
}
```

```yaml
# Новый способ оплаты = 1 класс + 1 строка в yaml.
# PaymentProcessor — НЕТРОГАЕТСЯ.
```

**Почему это OCP:** класс `PaymentProcessor` закрыт для изменения (его код не меняется при добавлении способа), но открыт для расширения (новый handler регистрируется).

---

## 3. Events / Listeners (Symfony-style)

Самый «продвинутый» способ: издатель **не знает** о подписчиках.

### Ситуация

После сохранения заказа нужно:
- отправить e-mail,
- обновить аналитику,
- зарезервировать складские запасы,
- (завтра) — начислить бонусы,
- (через месяц) — синхронизировать с CRM.

### «До»: прямой вызов в домене

```php
final class Order
{
    public function save(
        OrderRepository $repo,
        MailerInterface $mailer,
        AnalyticsClient $analytics,
        WarehouseClient $warehouse,
    ): void {
        $repo->save($this);
        $mailer->send('order_created', $this);       // ← домен знает про e-mail
        $analytics->track('order_created', $this);   // ← домен знает про аналитику
        $warehouse->reserve($this->items);           // ← домен знает про склад
    }
}
```

Каждая новая побочная задача — **изменение класса `Order`**. Невозможно.

### «После»: event + subscribers

```php
// Домен: Order не знает НИЧЕГО
final class Order
{
    public function markAsPlaced(): void
    {
        $this->status = OrderStatus::Placed;
        $this->events[] = new OrderPlacedEvent($this);  // собирает события
    }
}

// Dispatcher (application layer)
$dispatcher->dispatch($order->pullEvents());

// Subscribers — каждый независим
final class OrderPlacedEmailListener
{
    public function onOrderPlaced(OrderPlacedEvent $e): void { /* … */ }
}

final class OrderPlacedAnalyticsListener
{
    public function onOrderPlaced(OrderPlacedEvent $e): void { /* … */ }
}

final class OrderPlacedWarehouseListener
{
    public function onOrderPlaced(OrderPlacedEvent $e): void { /* … */ }
}

// Новый listener = новый класс + тег. Order, dispatcher, другие listeners — не трогаются.
```

```yaml
App\EventSubscriber\OrderPlacedEmail:
    tags:
        - { name: kernel.event_listener, event: app.order.placed }
App\EventSubscriber\OrderPlacedAnalytics:
    tags:
        - { name: kernel.event_listener, event: app.order.placed }
# Новый listener — 1 файл + 1 тег. Ничего не меняется.
```

**Почему это OCP:** доменный код **закрыт** (Order не знает, что происходит после `markAsPlaced`). Расширяется **через регистрацию** нового подписчика.

---

## 4. Pipeline / Messenger (Symfony Messenger, PSR-15)

Для цепочек трансформаций.

### «До»: конвейер в одном методе

```php
public function processOrder(Order $order): void
{
    $order = $this->validateOrder($order);
    $order = $this->applyDiscounts($order);
    $order = $this->calculateTax($order);
    $order = $this->reserveStock($order);
    $this->save($order);
    // Новый шаг = изменение метода processOrder.
}
```

### «После»: pipe через middleware

```php
interface OrderPipelineStep
{
    public function handle(Order $order, callable $next): void;
}

final class ValidateStep implements OrderPipelineStep
{
    public function handle(Order $order, callable $next): void
    {
        // … валидация
        $next($order);
    }
}

final class DiscountStep implements OrderPipelineStep
{
    public function handle(Order $order, callable $next): void
    {
        // … скидки
        $next($order);
    }
}

final class OrderPipeline
{
    /** @param OrderPipelineStep[] $steps */
    public function __construct(private array $steps) {}

    public function run(Order $order): void
    {
        $handler = $this->buildPipeline(end($this->steps), $order);
        array_shift($this->steps);
        $handler();
    }

    private function buildPipeline(OrderPipelineStep $step, Order $order): \Closure
    {
        return function () use ($step, $order) {
            $next = $this->buildPipeline(next($this->steps) ?? NoopStep::class, $order);
            $step->handle($order, $next);
        };
    }
}
```

В Symfony это уже **встроенный** механизм — `MessageBusInterface` + `SendMessageMiddleware`. Новый middleware = новый класс + тег. Очередь сообщений не меняется.

**Почему это OCP:** цепочка определяется **конфигурацией**, а не кодом диспетчера.

---

## 5. Data-driven: конфигурация вместо кода

Когда «расширение» — это **данные**, а не классы.

### Пример: валидация полей формы

### «До»: код для каждого типа

```php
class SignupFormValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Некорректный e-mail';
        if (strlen($data['password']) < 8)
            $errors['password'] = 'Минимум 8 символов';
        if (!preg_match('/^\d{4,}$/', $data['phone']))
            $errors['phone'] = 'Только цифры, 4+ симв.';
        // Каждое новое поле = новая if-ветка
        return $errors;
    }
}
```

### «После»: правила как данные

```php
final class SignupFormValidator
{
    /**
     * @param array<string, array{rule: string, params: array}> $rules
     */
    public function __construct(private array $rules) {}

    public function validate(array $data): array
    {
        $errors = [];
        foreach ($this->rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $result = $this->applyRule($rule['rule'], $value, $rule['params']);
            if ($result !== true) {
                $errors[$field] = $result;
            }
        }
        return $errors;
    }

    private function applyRule(string $rule, mixed $value, array $params): true|string
    {
        return match ($rule) {
            'email'     => filter_var($value, FILTER_VALIDATE_EMAIL) ? true : 'Некорректный e-mail',
            'min_len'   => strlen((string)$value) >= $params['n'] ? true : sprintf('Минимум %d символов', $params['n']),
            'regex'     => preg_match($params['pattern'], (string)$value) ? true : ($params['message'] ?? 'Некорректный формат'),
            'required'  => $value !== null && $value !== '' ? true : 'Обязательное поле',
            default     => throw new UnknownRuleException($rule),
        };
    }
}
```

```yaml
# config/form_rules.yaml — новый поле = новая строка в yaml
signup:
    email:    { rule: email,    params: {} }
    password: { rule: min_len,  params: { n: 8 } }
    phone:    { rule: regex,    params: { pattern: '/^\d{4,}$/', message: 'Только цифры, 4+ симв.' } }
    # Новое поле:
    address:  { rule: required, params: {} }
```

**Почему это OCP:** валидатор закрыт для изменения (код `applyRule` не меняется при добавлении полей). Расширяется через данные.

---

## 6. Closure injection (PHP 8, без интерфейсов)

Лёгкий вариант для внутреннего кода, где DI-контейнер — оверкилл.

```php
final class ReportGenerator
{
    /**
     * @param array<string, callable(ReportData): string> $renderers
     */
    public function __construct(private array $renderers) {}

    public function generate(ReportData $data, string $format): string
    {
        if (!isset($this->renderers[$format])) {
            throw new UnsupportedFormat($format);
        }
        return ($this->renderers[$format])($data);
    }
}

// Регистрация — в bootstrap, без классов:
$generator = new ReportGenerator([
    'html'   => fn(ReportData $d) => sprintf('<h1>%s</h1>', $d->title),
    'csv'    => fn(ReportData $d) => $d->toCsv(),
    'json'   => fn(ReportData $d) => json_encode($d->toArray()),
    // Новый формат = одна строка. Класс ReportGenerator не трогается.
]);
```

Это не «архитектура», но для внутреннего инструмента или CLI-скрипта — **быстрее и проще**, чем интерфейс + реализация + DI-конфиг.

---

## Сравнение

| Механизм | Где уместен | Стоимость расширения | Когда не подходит |
|---|---|---|---|
| **Strategy (композиция)** | Несколько реализаций одного контракта | 1 класс + 1 тег | Реализаций 1–2 и не предвидится больше |
| **Registry / Plugin** | Диспетчер по ключу | 1 класс + 1 строка в конфиг | Ключи не закрытое множество (бесконечный рост) |
| **Events / Listeners** | Побочные эффекты после основного действия | 1 класс + 1 тег | Нужно строгое упорядочение (здесь — `priority`) |
| **Pipeline / Messenger** | Цепочка трансформаций | 1 middleware + 1 тег | Простой вызов без порядка |
| **Data-driven** | Поведение описывается данными | 1 строка в конфиг/БД | Логика не выражается данными (алгоритм ≠ данные) |
| **Closure injection** | Внутренний код, скрипты, 3–5 вариантов | 1 строка | Нужно переиспользование, тестирование, типизация |

---

## Как выбрать

Простой чек-лист:

1. **Нужно ли менять существующий код при добавлении нового варианта?** Если да — OCP нарушен. Ищите механизм, где ответ «нет».
2. **Сколько вариантов?**
   - 1–2 → прямой вызов, `if/else`. Любая абстракция — ceremony.
   - 3–5 → Strategy или Registry.
   - 5+ и не предвидится конца → Events, Pipeline, Data-driven.
3. **Кто добавляет варианты?**
   - Вы, разработчик → любой механизм.
   - Оператор / клиент / админка → Data-driven (конфиг, БД, YAML).
4. **Нужно ли упорядочение?**
   - Да → Pipeline / Messenger.
   - Нет → Events / Registry / Strategy.
5. **Нужно ли переиспользование вне этого контекста?**
   - Да → интерфейс + DI.
   - Нет → closure / trait / inline.

Главное: OCP — это **цель** («новое поведение без изменения старого кода»), а не **средство** (наследование). Наследование — один из ~10 способов эту цель достичь. Выбирайте тот, где стоимость расширения минимальна, а ceremony — отсутствует.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет. SRP — не «один класс — одна задача». Это **один юнит — одна причина для изменения**. И «юнит» может быть чем угодно: функция, метод, файл, параметр, даже отдельный блок кода.

---

## Функция / метод — самый частый уровень

```php
// Одна функция, три причины для изменения:
function processPayment(string $orderId): void
{
    // Причина 1: валидация
    $order = $repo->findById($orderId);
    if ($order->amount <= 0) throw new \Exception('Bad amount');

    // Причина 2: расчёт комиссии (изменится при смене формулы)
    $fee = $order->amount * 0.029 + 0.30;

    // Причина 3: запись в лог/отчёт (изменится при смене формата)
    file_put_contents('/var/log/payments.log',
        sprintf("%s|%s|%.2f\n", $orderId, date('c'), $fee),
        FILE_APPEND
    );

    // Причина 4: фактический платёж (изменится при смене провайдера)
    $stripe->charge($orderId, $order->amount - $fee);
}
```

Четыре причины изменения в одной функции. Любая из них меняется — вы трогаете все четыре.

Разбивка:

```php
function processPayment(string $orderId): void
{
    $order   = validateOrder($orderId);          // причина 1
    $fee     = calculateFee($order);              // причина 2
    $result  = chargeProvider($order, $fee);      // причина 4
    logPayment($orderId, $order->amount, $fee, $result); // причина 3
}
```

Теперь изменение формулы комиссии не затрагивает логирование. Изменение формата лога не затрагивает расчёт.

---

## Файл / модуль

```
src/Invoice.php          ← и генерация PDF, и расчёт налогов, и отправка на e-mail
```

Три причины изменения в одном файле. Когда меняется налоговая логика — вы открываете файл, где вперемешку PDF-шаблон и e-mail-текст. Когда меняется шаблон PDF — вы ищете нужную функцию среди 400 строк.

Файл — это **юнит**, к которому применим SRP. Не «класс», а файл.

---

## Параметр / аргумент

```php
function sendNotification(
    string $userId,
    string $channel,       // 'email' | 'sms' | 'push'
    string $template,      // 'order_shipped' | 'welcome' | 'promo'
    bool   $isUrgent,
    string $lang,          // 'ru' | 'en' | 'de'
    array  $vars = [],
    bool   $dryRun = false
): void
```

Шесть параметров, каждый из которых — **своя причина изменения**:

- Меняется набор каналов → меняете `$channel`.
- Меняется набор шаблонов → меняете `$template`.
- Меняется набор языков → меняете `$lang`.
- Появляется «уровень приоритета» вместо `bool $isUrgent` → меняете сигнатуру.

Один параметр — одна причина. Здесь параметры уже **несут** SRP: каждый отвечает на свой вопрос. Но если `sendNotification` **делает** и отправку, и логирование, и обновление статистики — функция нарушает SRP, даже если параметры «чистые».

---

## Базовый класс / интерфейс

```php
abstract class AbstractEntity
{
    abstract public function getId(): string;
    abstract public function toArray(): array;
    abstract public function validate(): array;
    abstract public function getAuditLog(): array;
}
```

Каждый метод — потенциально своя причина изменения:

- `validate()` меняется при изменении бизнес-правил.
- `getAuditLog()` меняется при изменении требований к аудиту.
- `toArray()` меняется при изменении формата сериализации.

Если эти причины **независимы** — лучше разбить на интерфейсы (ISP):

```php
interface Validatable     { public function validate(): array; }
interface Auditable       { public function getAuditLog(): array; }
interface Serializable    { public function toArray(): array; }
```

Теперь сущность, у которой нет аудита, **не реализует** `Auditable`. Не «наследует и переопределяет в пустоту».

---

## Даже отдельный блок кода

```php
$total = 0;
foreach ($items as $item) {
    $total += $item->price;
}

// Здесь: расчёт скидки
$discount = $total > 10000 ? $total * 0.05 : 0;

// Здесь: расчёт налога
$tax = ($total - $discount) * 0.20;

$final = $total - $discount + $tax;
```

Один блок, три расчёта. Если меняют формулу скидки — вы ищете нужную строку в общем `foreach`. Если меняют ставку налога — то же. Выносите каждый расчёт в отдельную функцию, и у каждой появляется **своя** причина изменения.

---

## Практический тест

Возьмите любой юнит (функцию, файл, класс, метод) и задайте вопрос:

> **Если завтра поменяется X, я буду менять этот юнит? А если поменяется Y — тоже? А Z?**

Если ответов ≥ 2 и они **независимы** (изменение X не влечёт изменение Y) — SRP нарушен. Неважно, класс это или функция из трёх строк.

---

## Частая ошибка: SRP ≠ «один метод на класс»

Иногда SRP доводят до абсурда:

```php
final class OrderRepository
{
    public function __construct(private Connection $db) {}

    public function findById(string $id): Order { /* 15 строк */ }
}

final class OrderRepositoryFindById
{
    public function __construct(private Connection $db) {}
    public function execute(string $id): Order { /* те же 15 строк */ }
}
```

Это **не SRP**. Это ceremony. Одна функция, одна причина изменения. Разбивать её на отдельный класс — бессмысленно.

SRP говорит: «у юнита должна быть **одна** причина изменения». Не «юнит должен быть **маленьким**». Не «один метод = один класс».

---

## Итог

| Юнит | Пример нарушения | Пример соблюдения |
|---|---|---|
| Функция | `processPayment()` делает валидацию + расчёт + лог + платёж | 4 функции, одна причина каждая |
| Файл | `Invoice.php`: PDF + налоги + e-mail | 3 файла или 3 класса |
| Параметр | `sendNotification(6 args)` | Группировка в `NotificationConfig` |
| Интерфейс | `AbstractEntity` с 4 методами | 3 интерфейса по причинам |
| Блок кода | `foreach` + скидка + налог в одном месте | 3 функции |

SRP — про **причину изменения**, а не про **размер** и не про **тип сущности**.

> ⏱ 45.9s · prompt 10370 tok · output 6366 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про DI-контейнер Symfony: компиляция, autowiring, и что происходит при первом запросе сервиса. Это продвинутый вопрос, требующий точного понимания внутреннего устройства. Давай разберу по слоям:

1. Композиция контейнера: как Symfony собирает сервисы из конфигурации (YAML, PHP, XML, attributes) + автообнаружение (PSR-4, attributes #[Autoconfigure], #[AsService])
2. Компиляция: Passes (DefinitionDecoratorPass, ResolveChildDefinitionsPass, RegisterServiceSubscribersPass, ResolveReferencesToAliasesPass, ResolveParameterPlaceHoldersPass, и т.д.)
3. Autowiring: как работает (анализ сигнатуры, поиск по типу, fallback по имени, @required)
4. Runtime: что происходит при $container->get() — lazy service, proxy, scope (request, prototype, singleton), disposal

Нужно быть точным, не путать Symfony 5 и 6. В Symfony 5+ autowiring по умолчанию включён, autowire_types тоже. Контейнер компилируется в проприетарный PHP-класс (ContainerXXX), который наследуется от Container.

Структура ответа:
1. Общая схема (высокий уровень)
2. Что попадает в контейнер (конфигурация)
3. Компиляция (passes, по порядку, ключевые)
4. Autowiring (как именно работает)
5. Runtime: первый запрос (lazy loading, proxies, scopes)
6. Что человек может сломать / типичные вопросы

Пишу на русском, как и вся беседа. Код — PHP. Структурирую с заголовками, но не перегружаю.

</details>

**Кандидат:**

## Общая схема

```
┌─────────────────────────────────────────────────────────────────────┐
│  КОНФИГУРАЦИЯ (статическая)                                         │
│  services.yaml, attributes, PSR-4, @Autoconfigure, @Autowire       │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│  КОМПИЛЯЦИЯ (offline, при cache:clear / build)                      │
│  1. Сборка Definitions из всех источников                           │
│  2. Prologue → passes (в фиксированном порядке) → Epilogue          │
│  3. Генерация PHP-класса App_KernelDevDebugContainer               │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│  RUNTIME (online, каждый запрос)                                    │
│  $container->get('app.service')                                     │
│  → lazy proxy? → создаём? → инициализация? → scope? → disposal?     │
└─────────────────────────────────────────────────────────────────────┘
```

Ключевая мысль: **компиляция — это не «создание объектов»**. Это генерация **рецепта** (PHP-класс), по которому объекты создаются лениво в рантайме.

---

## 1. Что попадает в контейнер

Symfony собирает `Definition` (или `Alias`) из нескольких источников:

```yaml
# config/services.yaml — явно
services:
    _defaults:
        autowire: true          # по умолчанию (Symfony 5+)
        autoconfigure: true     # по умолчанию (Symfony 5+)

    App\Service\OrderService:
        arguments:
            $timeout: 30        # аргумент по имени (named argument)

    App\Repository\OrderRepository:
        class: App\Doctrine\OrderRepository
        public: true            # доступен извне (controller, CLI)

    App\Gateway\StripeGateway:
        tags: [app.payment_handler]

# config/services/stripe.yaml — разделение по модулю
# config/services/test.yaml — переопределение для тестов
```

```php
// Атрибуты (Symfony 5.3+)
#[Autoconfigure]
#[AsTaggedIterator('app.payment_handler')]
final class StripeGateway implements PaymentHandler { /* … */ }

#[Autowire]
final class OrderService
{
    public function __construct(
        #[Autowire] private OrderRepository $repo,   // явный (избыточный при авто)
        #[AutowireDateInterval('5m')] private \DateInterval $timeout,
    ) {}
}
```

```
# PSR-4: каждый public-класс в src/Service/ автоматически получает Definition
# (если не исключён через _defaults: exclude: ...)
```

Каждый сервис — это `Definition`:

```
Definition:
    class:        App\Service\OrderService
    factory:      null  |  [service, method]  |  [class, staticMethod]
    arguments:    [ref(App\Repository\OrderRepository), 30, …]
    tags:         []
    public:       false
    shared:       true         # singleton по умолчанию
    lazy:         false
    autowired:    true
    synthetic:    false        # не создаётся контейнером (внешний)
```

---

## 2. Компиляция: что именно происходит

Контейнер — это **не** «массив service_id → object». Это **PHP-класс**, который генерируется на лету. При `cache:clear` Symfony:

### Шаг 1: Сборка Definitions

Все источники (YAML, XML, PHP-DSL, атрибуты, PSR-4 автообнаружение, `#[Autoconfigure]`) превращаются в `Definition` / `Alias`. На этом этапе ещё нет никакой валидации — просто данные.

### Шаг 2: Passes

`ContainerBuilder::compile()` прогоняет цепочку **passes** в строго фиксированном порядке. Каждый pass — это `CompilingPass`, который может:
- изменять `Definition`,
- удалять `Definition`,
- создавать новые `Alias`,
- бросать `InvalidArgumentException`.

Ключевые passes (упрощённо, по порядку):

| Pass | Что делает |
|---|---|
| `RegisterAutoconfigureAttributesPass` | Прочитал `#[Autoconfigure]`, `#[AsService]`, `#[AsTaggedService]` из атрибутов классов |
| `RegisterServiceSubscribersPass` | Собирает `EventSubscriberInterface` → генерирует класс-агрегатор |
| `ResolveChildDefinitionsPass` | Наследование: `_defaults` → конкретный сервис. `parent: foo` → копирует args/class |
| `ResolveReferencesToAliasesPass` | `ref(Alias)` → `ref(реальный_id)` |
| `ResolveParameterPlaceHoldersPass` | `%kernel.project_dir%` → `/var/www/project` |
| **`ResolveServiceReferencesPass`** | **Ядро autowiring**: для каждого `?` в аргументах ищет сервис по типу |
| `CheckExceptionAccessibilityPass` | Валидирует `catch`-блоки |
| `RegisterFrequentlyUsedServicesPass` | Определяет, какие сервисы «горячие» (inlined в контейнер) |
| `InlineServiceDefinitionsPass` | Встраивает singleton-сервисы напрямую в код вызывающего (убирает `get()`) |
| `RemoveUnusedDefinitionsPass` | Удаляет приватные сервисы, которые никто не использует |
| `RegisterServiceSubscribersPass` (2-й раз) | Финализирует event subscribers |
| `AnnotateFileForDebugPass` | Помечает сгенерированный файл для `debug:container` |
| `DumpParameterTypeValuesPass` | Записывает типы параметров для автодополнения |
| `FixXmlPortabilityPass` | Связывает XML → PHP |

### Шаг 3: Генерация PHP-класса

`ContainerBuilder::compile()` → `ContainerBuilder::dump()` → **генерирует файл**:

```
var/cache/dev/App_KernelDevDebugContainer.php
```

Этот файл — **сам контейнер**. Он наследуется от `Symfony\Component\DependencyInjection\Container` и содержит:

```php
// Упрощённо, реальный файл на 1000+ строк
class App_KernelDevDebugContainer extends Container
{
    // 1. Прямые свойства для «горячих» (inlined) singleton'ов:
    private $services = [];

    // 2. Методы get<ClassName>():
    protected function getSharedOrderService(): \App\Service\OrderService
    {
        return $this->services['app.service.order']
            ??= new \App\Service\OrderService(
                $this->getSharedOrderRepository(),   // прямой вызов, не get()
                30,
                new \App\Gateway\StripeGateway(
                    $this->getSharedStripeClient(),
                ),
            );
    }

    // 3. Методы для lazy-прокси:
    protected function getLazyOrderServiceProxy(): \App\Service\OrderService
    {
        return $this->services['app.service.order']
            ??= (static function () {
                $proxy = new \App\Service\OrderService\_OrderServiceProxy(/* … */);
                $proxy->setContainer($this);
                return $proxy;
            })();
    }

    // 4. Методы для event subscribers (агрегатор):
    protected function getEventDispatcher(): \Symfony\Component\EventDispatcher\EventDispatcher
    {
        return $this->services['event_dispatcher']
            ??= (new \Symfony\Component\EventDispatcher\EventDispatcher())
                ->addListener('app.order.placed',
                    [$this->getOrderPlacedEmailListener(), 'onOrderPlaced'])
                ->addListener('app.order.placed',
                    [$this->getOrderPlacedAnalyticsListener(), 'onOrderPlaced']);
    }
}
```

**Ключевое:** на момент `cache:clear` **ни один сервис ещё не создан**. Создан только «чертёж» — PHP-методы, которые **знают, как** создать каждый сервис, когда попросят.

---

## 3. Autowiring: как именно работает

Когда `ResolveServiceReferencesPass` видит `Definition` с `autowired: true` и аргументом `?`:

```php
// Что Symfony видит:
Definition {
    class: App\Service\OrderService
    arguments: [?, 30, ?]   // ? = нужно найти по типу
}
```

Pass читает **сигнатуру конструктора** через рефлекс (`ReflectionClass::getConstructor()`):

```php
public function __construct(
    OrderRepository $repo,   // тип: App\Repository\OrderRepository
    int $timeout,            // скаляр — не автосервис
    ?LoggerInterface $logger = null,  // nullable — опциональный
) {}
```

Для каждого параметра с типом (не скалярным):

1. **Ищет сервис по FQCN.** Проходит по всем `Definition` и `Alias`, ищет:
   ```
   definition.class === 'App\Repository\OrderRepository'
   // или
   definition.class === 'App\Doctrine\OrderRepository'  // реализация интерфейса
   // или
   alias['App\Repository\OrderRepository'] → 'app.repository.order'
   ```

2. **Если найдено ровно одно** — подставляет `ref('app.repository.order')`.

3. **Если найдено несколько** — ищет по **имени параметра** (camelCase → snake_case):
   ```
   $orderRepository → app.repository.order
   $userRepository → app.repository.user
   ```

4. **Если по имени не найдено** — ищет по **interface + hint**:
   ```
   $logger: LoggerInterface → app.logger (известный сервис Symfony)
   ```

5. **Если всё равно не найдено** — ищет **synthetic** сервисы, зарегистрированные ядром (`'logger'`, `'event_dispatcher'`, `'router'` и т.д.).

6. **Если параметр `?Type` (nullable) и сервис не найден** — подставляет `null` (опциональная зависимость).

7. **Если ничего не нашлось и параметр обязателен** — **компиляция падает** с ошибкой:
   ```
   Cannot autowire service "App\Service\OrderService":
   argument "$paymentGateway" of method "__construct()"
   references interface "App\Payment\PaymentGateway"
   but no such service exists.
   ```

### `#[Autowire]` — переопределение

```php
public function __construct(
    #[Autowire(service: 'app.gateway.stripe')]
    PaymentGateway $gateway,   // не первый по имени, а конкретный
) {}
```

### `@required` — setter injection

```php
class OrderService
{
    public function __construct(private OrderRepository $repo) {}

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
```

`ResolveReferencesToRequiredMethodsPass` (позже в цепочке passes) находит методы с `#[Required]` и подставляет аргументы. Это не «магия» — pass просто вызвал `ReflectionClass::getMethods()` и отфильтровал по атрибуту.

### Что НЕ делает autowiring

- Не создаёт сервисы. Только **подставляет ссылки** (`ref`) в `Definition`.
- Не знает про generics. `array<Order>` — для него просто `array`.
- Не работает с `private`-конструкторами.
- Не работает, если `autowire: false` на сервисе или в `_defaults`.

---

## 4. Runtime: первый запрос `$container->get('app.service.order')`

Предположим: `OrderService` — **приватный** (не `public: true`), **shared** (singleton), **не lazy**.

### Что происходит физически

```php
// Контроллер (или другой сервис)
public function __construct(private OrderService $orderService) {}
// ↑ Autowiring уже встроил это в контейнер при компиляции.
// Контроллер НЕ вызывает $container->get() напрямую.
```

```php
// Внутри сгенерированного класса контейнера:
protected function getSharedOrderService(): \App\Service\OrderService
{
    return $this->services['app.service.order']
        ??= new \App\Service\OrderService(
            $this->getSharedOrderRepository(),   // ← рекурсивный вызов
            30,
            $this->getSharedLogger(),            // ← reкурсивный вызов
        );
}
```

**`??=`** — ключевой оператор. Первый вызов:
1. `$this->services['app.service.order']` — `null`.
2. `new OrderService(...)` — **рекурсивно** создаёт зависимости (каждая тоже `??=`).
3. Сохраняет результат в `$this->services['app.service.order']`.
4. Возвращает объект.

Второй вызов (в том же запросе):
1. `$this->services['app.service.order']` — **уже объект**.
2. `??=` **не пересоздаёт**.
3. Возвращает тот же объект.

**Это и есть singleton-scope.** Не «магия контейнера», а PHP-оператор `??=` в сгенерированном коде.

### Если сервис **lazy**

```yaml
App\Service\OrderService:
    lazy: true
```

Компиляция генерирует **не** прямой `new`, а **прокси**:

```php
// Сгенерированный класс (упрощённо):
class OrderServiceProxy extends OrderService
{
    private ?ContainerInterface $container = null;
    private bool $initialized = false;

    public function __call(string $name, array $args): mixed
    {
        $this->initialize();
        return parent::$name(...$args);
    }

    public function setContainer(ContainerInterface $c): void
    {
        $this->container = $c;
    }

    private function initialize(): void
    {
        if ($this->initialized) return;
        $this->initialized = true;

        $real = $this->container->get('app.service.order');
        // Копируем свойства реального объекта в прокси
        foreach (get_object_vars($real) as $prop => $val) {
            $this->$prop = $val;
        }
        // Деструктор реального объекта → деструктор прокси
    }
}
```

**Что происходит при первом запросе:**

1. `$container->get('app.service.order')` → создаёт **прокси** (дешёвый `new`, без вызова конструктора реального класса).
2. Возвращает прокси. **Конструктор `OrderService::__construct()` НЕ вызван.**
3. Когда-то кто-то вызывает `$orderService->cancel($id)` → `__call` → `initialize()` → **вот тогда** вызывается реальный конструктор, создаются зависимости.
4. Если **никто** не вызвал ни одного метода — реальный объект **никогда не создаётся**.

**Зачем:** если сервис тяжёлый (открывает БД-коннект, строит компилятор шаблонов), а в 95% запросов он не используется — lazy экономит время и память.

### Если сервис **prototype**

```yaml
App\DTO\Cart:
    shared: false   # prototype scope
```

Каждый `get()` — **новый объект**. `??=` заменяется на простой `return new Cart(...)`. Для DTO, сущностей, форм — это правильно: каждый запрос должен иметь свой экземпляр.

### Если сервис **в request-scope**

```php
// Symfony: RequestContext, Session, Request — request-scoped
// Живут ровно один HTTP-запрос.
// При начале запроса: создаются.
// При конце запроса: destroy().
```

Контейнер генерирует:

```php
// В Container::onKernelResponse (event):
foreach ($this->requestScopeServices as $id) {
    unset($this->services[$id]);  // деструкция
}
```

### Если сервис **public**

```yaml
App\Service\OrderService:
    public: true
```

Разница:
- **Private** (по умолчанию): доступен только изнутри контейнера. Если никто не зависит от него — `RemoveUnusedDefinitionsPass` **удалит** его при компиляции.
- **Public**: доступен извне — `$container->get('app.service.order')`, контроллеры, CLI-команды, `#[Required]`.

**Правило:** делайте сервисы `public: true` только когда **действительно** нужно вызвать их извне (контроллер, CLI, `debug:container`). В 90% случаев сервисы — `private`, и контейнер сам знает, как их создать.

---

## 5. Что человек ломает (типичные сценарии)

### «Почему мой сервис не автовывается?»

```yaml
App\Service\OrderService:
    autowire: false   # ← явно выключили
```

Или:

```yaml
App\Service\OrderService:
    class: App\Legacy\OrderService   # ← другой класс, другой конструктор
```

Или:

```php
public function __construct(
    PaymentGateway $gateway,   // интерфейс, 3 реализации, имя не совпало
) {}
```

Диагностика: `bin/console debug:autowiring OrderService`

### «Почему создается два экземпляра?»

```yaml
App\DTO\Cart:
    shared: true   # ← singleton, а нужен prototype
```

Или:

```php
// В конструкторе:
public function __construct(private Cart $cart) {}
// $cart — singleton. Каждый сервис, который зависит от Cart,
// получает ОДИН и тот же экземпляр.
```

### «Почему `#[Required]` не сработал?»

- Забыли `use Symfony\Contracts\Service\Attribute\Required;`
- Или `use Symfony\Component\DependencyInjection\Attribute\Required;` (старый импорт).
- Метод — `private`. `#[Required]` работает только для `public`/`protected`.

### «Почему `cache:clear` не помогает?»

```bash
# Symfony кэширует контейнер в var/cache/dev/
# Если вы изменили services.yaml, но не очистили кэш:
# — компиляция НЕ пересобрана
# — старый PHP-класс контейнера всё ещё в памяти
# — autowiring не пересчитан

# Решение:
php bin/console cache:clear

# Если это не помогло — проверьте, нет ли второго кэша:
# (опечатка в config, кэш в другом окружении)
```

### «Почему `instanceof`-проверка не работает?»

```php
public function __construct(
    PaymentHandler $handler,   // конкретный класс, не интерфейс
) {}
```

Аutowiring ищет по **типу**. Если вы объявили конкретный класс, а в контейстере три — автосервис не знает, какой. Ищите по имени или объявите интерфейс.

---

## Итог: три вопроса

| Вопрос | Ответ |
|---|---|
| Что создаётся при `cache:clear`? | **PHP-файл** с методами-рецептами. Не объекты. |
| Что делает autowiring? | **Подставляет `ref`** в `Definition` по типу/имени. Не создаёт объекты. |
| Что происходит при `$container->get()`? | Вызывается `getSharedXxx()` → `??=` → `new` (или прокси) → рекурсивно зависимости → singleton. |

Контейнер Symfony — это **не** «фабрика объектов». Это **сгенерированный PHP-код**, который знает, **как** и **когда** создать каждый сервис, и делает это лениво, по мере необходимости.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Честный ответ

Циклическая зависимость в 90% случаев — **не проблема, которую нужно разруливать**. Это сигнал, что два сервиса делают слишком много или разделение границ неверное. «Разруливание» — это не патч, а повод пересмотреть, кто за что отвечает.

Но есть ситуации, где цикл **легитимен** (например, `A` вызывает `B`, `B` в ответном потоке вызывает `A`). Тогда — конкретные приёмы.

---

## Почему цикл возникает: три корневые причины

### 1. Оба сервиса делают «и чтение, и запись»

```
OrderService → NotificationService   ("отправь письмо")
NotificationService → OrderService   ("а какой у заказа статус?")
```

Оба знают про `Order`. Ни один не является «хозяином» домена.

### 2. Общий контекст размазан по двум местам

```
PaymentService → InventoryService    ("зарезервируй товары")
InventoryService → PaymentService    ("а какой метод оплаты?")
```

Оба знают про `Payment`. Ни один не является «хозяином» платежа.

### 3. «Взаимная помощь» без явной границы

```
UserService → RoleService   ("какая роль у юзера?")
RoleService → UserService   ("а какой юзер?")
```

Фактически один домен, разрезанный на два класса.

---

## Паттерны разруливания

### 1. Mediator: извлечь общее в третий сервис

Самый частый и самый правильный приём.

**До:**

```php
final class OrderService
{
    public function __construct(
        private NotificationService $notifier,  // ← зависит от Notification
    ) {}

    public function cancel(string $id): void
    {
        $order = $this->repo->find($id);
        $order->cancel();
        $this->repo->save($order);

        $this->notifier->sendOrderCancelled($order);  // ← цикл
    }
}

final class NotificationService
{
    public function __construct(
        private OrderService $orderService,  // ← зависит от Order
        private Mailer $mailer,
    ) {}

    public function sendOrderCancelled(Order $order): void
    {
        $user = $this->orderService->getUser($order->userId);  // ← цикл
        $this->mailer->send($user->email, 'Заказ отменён');
    }
}
```

**После:**

```php
// Выносим «что отправляем» из NotificationService.
// NotificationService знает ТОЛЬКО о том, как доставить.

interface NotificationPayload
{
    public function toEmail(): string;
    public function toTemplate(): string;
}

final class OrderCancelledNotification implements NotificationPayload
{
    public function __construct(private Order $order, private User $user) {}

    public function toEmail(): string { return $this->user->email; }
    public function toTemplate(): string { return 'order_cancelled'; }
}

// NotificationService НЕ знает ни про Order, ни про User.
final class NotificationService
{
    public function __construct(private Mailer $mailer) {}

    public function send(NotificationPayload $payload): void
    {
        $this->mailer->send(
            to: $payload->toEmail(),
            template: $payload->toTemplate(),
        );
    }
}

// OrderService собирает payload сам.
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private UserRepository $userRepo,
        private NotificationService $notifier,  // ← одна зависимость, без цикла
    ) {}

    public function cancel(string $id): void
    {
        $order = $this->repo->find($id);
        $order->cancel();
        $this->repo->save($order);

        $user = $this->userRepo->find($order->userId);

        $this->notifier->send(
            new OrderCancelledNotification($order, $user)
        );
    }
}
```

**Что изменилось:** `NotificationService` не знает про `Order` и `User`. Он принимает **payload** — dumb-объект, который несёт данные. Цикл убил извлечение общего контекста в третий элемент (payload).

---

### 2. Events: асинхронное или синхронное уведомление

Когда «B нужен после A» — это **событие**, а не вызов.

**До:**

```php
// OrderService → InventoryService → OrderService
$order->cancel();
$inventory->releaseStock($order->items);   // ← зависит от OrderService
$orderService->recalculateTotal($order);   // ← зависит от InventoryService
```

**После:**

```php
// OrderService: только домен
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function cancel(string $id): void
    {
        $order = $this->repo->find($id);
        $order->cancel();
        $this->repo->save($order);

        $this->dispatcher->dispatch(new OrderCancelledEvent($order));
    }
}

// InventoryService: слушатель, не вызывающий
final class InventoryListener
{
    public function __construct(
        private InventoryRepository $invRepo,
    ) {}

    public function onOrderCancelled(OrderCancelledEvent $e): void
    {
        foreach ($e->order->items as $item) {
            $this->invRepo->release($item->productId, $item->qty);
        }
    }
}
```

```yaml
App\Listener\InventoryListener:
    tags:
        - { name: kernel.event_listener, event: app.order.cancelled, method: onOrderCancelled }
```

**Что изменилось:** `InventoryService` **не зависит** от `OrderService`. Он **слушает** событие. Направление зависимости одно: `OrderService → EventDispatcher → InventoryListener`. Обратной стрелки нет.

---

### 3. Interface segregation: разбить один интерфейс на два

Когда `A` зависит от `B`, но `B` зависит от `A` только для **одного метода**.

**До:**

```php
interface UserService
{
    public function findById(string $id): User;
    public function assignRole(string $userId, string $roleId): void;  // ← здесь нужен RoleService
    public function getRoleNames(string $userId): array;
}

interface RoleService
{
    public function findById(string $id): Role;
    public function validateAssignment(User $user, Role $role): void;  // ← здесь нужен UserService
}
```

Цикл: `UserService::assignRole` → `RoleService` → `UserService::findById`.

**После:**

```php
// Разделяем: кто **читает** юзера и кто **пишет** роль
interface UserReader
{
    public function findById(string $id): User;
}

interface RoleAssigner
{
    public function assignRole(string $userId, string $roleId): void;
}

// RoleService зависит только от UserReader
final class RoleService implements RoleAssigner
{
    public function __construct(
        private RoleRepository $repo,
        private UserReader $users,    // ← узкая зависимость, без цикла
    ) {}

    public function assignRole(string $userId, string $roleId): void
    {
        $user = $this->users->findById($userId);  // ← только чтение
        $role = $this->repo->find($roleId);
        // …
    }
}

// UserService реализует UserReader, но НЕ зависит от RoleService
final class UserService implements UserReader
{
    public function __construct(private UserRepository $repo) {}

    public function findById(string $id): User {
        return $this->repo->find($id);
    }
}

// Orchestration: кто-то вне обоих вызывает
final class AdminController
{
    public function __construct(
        private RoleAssigner $roleService,   // ← RoleAssigner, а не RoleService
        private UserReader $userService,     // ← UserReader, а не UserService
    ) {}

    public function assign(string $userId, string $roleId): void
    {
        $this->roleService->assignRole($userId, $roleId);
    }
}
```

**Что изменилось:** `RoleService` зависит от `UserReader` (узкий интерфейс), а не от `UserService` (широкий). `UserService` не зависит от `RoleService`. Цикл убил **узкая зависимость + оркестрация снаружи**.

---

### 4. Callback / late binding

Когда `B` не знает, **кто** его вызовет обратно, но **может** вызвать обратно.

**До:**

```php
final class SearchService
{
    public function __construct(
        private HighlightService $highlighter,  // ← цикл
    ) {}

    public function search(string $q): SearchResults
    {
        $results = $this->engine->query($q);
        return $results->map(fn($r) => $this->highlighter->highlight($r, $q));
    }
}

final class HighlightService
{
    public function __construct(
        private SearchService $search,  // ← цикл
    ) {}

    public function highlight(SearchResult $r, string $q): HighlightedResult
    {
        $related = $this->search->search($q);  // ← обратный вызов
        // …
    }
}
```

**После:**

```php
// SearchService принимает callback, а не конкретный сервис
final class SearchService
{
    /** @param callable(SearchResult, string): HighlightedResult $highlightFn */
    public function __construct(
        private SearchEngine $engine,
        private \Closure $highlightFn,
    ) {}

    public function search(string $q): array
    {
        $results = $this->engine->query($q);
        return $results->map(fn($r) => ($this->highlightFn)($r, $q));
    }
}

// Кто регистрирует — тот и решает, какой highlighter использовать
$container->set('search', new SearchService(
    engine: $engine,
    highlightFn: fn($r, $q) => $highlighter->highlight($r, $q),
));
```

**Что изменилось:** `SearchService` не знает про `HighlightService`. Он знает про **функцию**. Кто её предоставляет — решает составитель, а не сам сервис.

---

### 5. Prototype scope (Symfony)

Когда «цикл» — это **один запрос**, и оба сервиса живут в одном request-scope.

```yaml
# App/Request/OrderContext: prototype (новый на каждый запрос)
App\Request\OrderContext:
    shared: false

# App/Request/InventoryContext: prototype
App\Request\InventoryContext:
    shared: false

# Теперь они могут ссылаться друг на друга
# без нарушения singleton-инварианта контейнера
App\Request\OrderContext:
    public: true
App\Request\InventoryContext:
    public: true
    calls:
        - [setOrderContext, ['@App\Request\OrderContext']]
```

```php
final class OrderContext
{
    public function __construct(
        private InventoryContext $inventory,  // ← prototype, не цикл
    ) {}

    public function cancel(): void
    {
        // …
        $this->inventory->release($this->order->items);
    }
}

final class InventoryContext
{
    private ?OrderContext $order = null;

    public function setOrderContext(OrderContext $o): void { $this->order = $o; }

    public function release(array $items): void
    {
        // Можно обратиться к $this->order, потому что
        // это ОДИН и тот же экземпляр за запрос
    }
}
```

**Когда уместно:** request-scoped состояния (Cart, Session, RequestContext). **Когда не уместно:** доменные сервисы, repository, anything, что живёт дольше запроса.

---

### 6. «Не разруливай» — переобмысливай границы

Иногда правильный ответ — **не разбивать**, а **объединять**.

```php
// Было: UserService ↔ RoleService (цикл)
// Стало: AccessService (один класс, два метода)

final class AccessService
{
    public function __construct(
        private UserRepository $users,
        private RoleRepository $roles,
    ) {}

    public function getUser(string $id): User { … }
    public function assignRole(string $userId, string $roleId): void { … }
    public function getRoleNames(string $userId): array { … }
}
```

Один класс, два repository, **ноль** циклов. SRP не нарушен, потому что «управление доступом» — **одна** причина изменения.

---

## Что делает Symfony DI-контейнер

Symfony **не разрешает** циклы для shared-сервисов. На этапе компиляции:

```
Circular reference found between services:
  App\Service\OrderService
    depends on App\Service\NotificationService
  App\Service\NotificationService
    depends on App\Service\OrderService
```

Единственный «официальный» способ обойти — `@required` (setter injection):

```php
final class OrderService
{
    public function __construct(private NotificationService $notifier) {}

    #[Required]
    public function setFallbackNotifier(FallbackNotifier $n): void
    {
        $this->fallback = $n;
    }
}
```

Но `#[Required]` **не решает цикл** — он просто откладывает его. Если `NotificationService` тоже зависит от `OrderService` через конструктор, Symfony всё равно упадёт.

---

## Чек-лист: что делать, когда видите цикл

```
1. Кто вызывает кого? Нарисуйте стрелки.
   A → B: "B, сделай X"
   B → A: "A, скажи мне Y"

2. Y — это **данные** или **действие**?
   Данные  → Mediator / payload / interface segregation
   Действие → Events / callback

3. Можно ли объединить A и B в один сервис?
   Да, и SRP не страдает → объединяйте.

4. Можно ли вынести Y в третий элемент (payload, event, interface)?
   Да → Mediator / Events / ISP.

5. Это request-scoped состояние?
   Да → prototype scope.

6. Ничего не помогло?
   Вернитесь к п. 1. Границы разрезаны не там.
```

---

## Итого

| Паттерн | Когда | Стоимость |
|---|---|---|
| **Mediator / payload** | Оба сервиса знают про общий доменный объект | 1 новый класс (payload) |
| **Events** | «B нужен после A», асинхронно или синхронно | 1 event + 1 listener |
| **ISP (узкий интерфейс)** | B зависит от A только для 1–2 методов | 1 интерфейс + реализация |
| **Callback / closure** | B не знает, кто вызовет обратно | 0 классов, 1 `\Closure` |
| **Prototype scope** | Request-scoped состояние | 0 классов, `shared: false` |
| **Объединение** | Два сервиса — фактически один домен | 0 классов, 1 класс |
| **`#[Required]`** | Отложить инициализацию | 1 setter, **не решение** |

Главное: **цикл — это вопрос границ, а не вопрос DI-контейнера**. Контейнер просто не позволяет вам создать два singleton'а, которые тянут друг друга в конструкторе. А нужно — передвинуть границу.

> ⏱ 40.8s · prompt 22454 tok · output 5041 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о практической задаче: после регистрации пользователя нужно (1) отправить письмо и (2) обновить поисковый индекс, и это не должно замедлять ответ. Нужно описать варианты в Symfony с trade-offs.

Давайте подумаем о вариантах:

1. **Symfony Messenger (async transport)** — самый " Symfony-идиоматичный" способ. Dispatch message → transport (Redis/RabbitMQ/AMQP/Doctrine) → consumer worker. Ответ пользователю возвращается сразу.

2. **Symfony Messenger (sync transport)** — dispatch → handler вызывается сразу в том же процессе. Не решает задачу (замедляет ответ).

3. **Symfony Messenger (async + failure handling)** — та же схема, но с retry, dead-letter, idempotency.

4. **События (EventDispatcher)** — синхронные, не решают задачу. Но можно использовать как внутренний механизм.

5. **PHP queue через Doctrine transport** — сообщения лежат в БД, воркер их забирает. Просто, но ограниченная пропускная способность.

6. **Redis/RabbitMQ/AMQP transport** — настоящая очередь, высокая пропускная способность, но нужна инфраструктура.

7. **Сетевой подход: dispatch в фоне через process (pcntl_fork)** — грязно, но работает без внешних зависимостей.

8. **HTTP-колбэк в себя (POST на /internal/queue)** — антипаттерн, но иногда используют.

9. **Symfony Cloud / AWS SQS / GCP Pub/Sub** — облачные очереди.

10. **Почему НЕ стоит делать всё в одном процессе (sleep, file_put_contents)** — блокирует worker.

Структурирую ответ:
- Краткое описание задачи
- Архитектура: что отправляем, кто обрабатывает
- Вариант 1: Messenger + async transport (основной)
- Вариант 2: Messenger + Doctrine transport (простой)
- Вариант 3: Cloud queue (AWS SQS / GCP Pub/Sub)
- Вариант 4: "Грязный" способ — fork/pcntl (антипаттерн, но упомянуть)
- Trade-offs таблица
- Детали: idempotency, retry, failure, ordering, monitoring
- Код для основного варианта
- Чего делать НЕ стоит

Напишу на русском, с кодом, практично.

</details>

**Кандидат:**

## Задача

```
POST /register
  → создать юзера (БД)
  → отправить welcome-письмо (SMTP)
  → проиндексировать в Elasticsearch/Typesense (HTTP)
  → 200 OK пользователю
```

Пользователь **не может** ждать SMTP-таймаут (5–30 с) и HTTP-запрос к индексу (1–5 с). Ответ должен уйти за 200–400 мс.

---

## Вариант 1: Symfony Messenger + async transport (основной)

Это идиоматичный Symfony-подход. Диспетчер **не вызывает** хендлер синхронно — кладёт сообщение в транспорт, и **отдельный воркер** забирает его асинхронно.

### Архитектура

```
┌──────────┐       ┌──────────────────┐       ┌─────────────────┐
│ HTTP     │  1.   │  Messenger       │  3.   │  Worker process │
│ Request  │──────▶│  (Bus)           │──────▶│  (bin/console   │
│          │       │                  │       │   messenger:    │
│          │       │  2. Transport    │       │   consume)      │
└──────────┘       │  (Redis/AMQP/DB) │       └────────┬────────┘
                   └──────────────────┘                │
                                                     4. SMTP / ES
```

### Код

**1. Domain event**

```php
// src/Event/UserRegistered.php
final readonly class UserRegistered
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
```

**2. Handler — письмо**

```php
// src/Messenger/Handler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UserProviderInterface $users,   // нужен, чтобы получить profile
    ) {}

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->users->loadUserById($event->userId);

        $message = (new Email())
            ->from(new Address('noreply@example.com', 'Example'))
            ->to($user->getEmail())
            ->subject('Добро пожаловать!')
            ->html($this->renderWelcome($event->name));

        $this->mailer->send($message);
    }

    private function renderWelcome(string $name): string
    {
        // Twig / raw HTML — как удобно
        return sprintf('<h1>Привет, %s!</h1><p>Добро пожаловать.</p>', $name);
    }
}
```

**3. Handler — индекс**

```php
// src/Messenger/Handler/UpdateSearchIndexHandler.php
final class UpdateSearchIndexHandler
{
    public function __construct(
        private SearchClient $search,   // ваш ES/Typesense/Meilisearch клиент
        private UserProviderInterface $users,
    ) {}

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->users->loadUserById($event->userId);

        $this->search->indexDocument(
            index: 'users',
            id: $user->getId(),
            body: [
                'name'  => $user->getFirstName() . ' ' . $user->getLastName(),
                'email' => $user->getEmail(),
                'tags'  => $user->getTags(),
            ],
        );
    }
}
```

**4. Регистрация в контроллере / service**

```php
// src/Service/RegistrationService.php
final class RegistrationService
{
    public function __construct(
        private UserProviderInterface $users,
        private UserPasswordEncoderInterface $encoder,
        private MessageBusInterface $bus,   // ← Messenger bus
    ) {}

    public function register(string $email, string $password, string $name): User
    {
        $user = $this->users->createUser($email, $name);
        $user->setPassword($this->encoder->hashPassword($user, $password));
        $this->users->persist($user);

        // ← Кладём в транспорт. НЕ вызываем handler.
        $this->bus->dispatch(new UserRegistered($user->getId(), $email, $name));

        return $user;   // ← Ответ пользователю уже ушёл
    }
}
```

**5. Конфигурация**

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        default_bus: messenger.bus.default

        buses:
            messenger.bus.default:
                middleware:
                    - doctrine_transaction   # ← если нужен откат БД при ошибке
                    - allow_no_handlers
                    - send_failed_messages_to_failure_transport

        transports:
            async:
                dsn: '%env(MESSENGER_DSN)%'
                # dsn: 'amqp://guest:guest@localhost:5672/%2f/messages?queue_name=app_messages'
                # dsn: 'redis://localhost:6379/messages'
                # dsn: 'doctrine://default?auto_setup=0'   ← простой вариант (БД)

            sync:
                dsn: 'sync'
                # ← для тестов: сообщения обрабатываются сразу, без воркера

            failure:
                dsn: 'redis://localhost:6379/failures'
                # ← dead-letter: сюда падают сообщения, которые не удалось обработать

        routing:
            'App\Event\UserRegistered': async   # ← оба handler'а получат из async
```

**6. Тегирование handler'ов**

```php
#[AsMessageHandler]
final class SendWelcomeEmailHandler { /* … */ }

#[AsMessageHandler]
final class UpdateSearchIndexHandler { /* … */ }
```

Или в YAML:

```yaml
services:
    App\Messenger\Handler\SendWelcomeEmailHandler:
        tags:
            - { name: messenger.message_handler, bus: messenger.bus.default }

    App\Messenger\Handler\UpdateSearchIndexHandler:
        tags:
            - { name: messenger.message_handler, bus: messenger.bus.default }
```

**7. Воркер (отдельный процесс)**

```bash
# systemd unit / supervisor / docker entrypoint / k8s Job
php bin/console messenger:consume async --limit=1000 -vv
# --limit: обработал 1000 сообщений и вышел (для k8s)
# -vv: лог каждого сообщения
```

В production — несколько воркеров параллельно:

```bash
# supervisor.conf
[program:messenger_worker]
command=php /app/bin/console messenger:consume async -vv
numprocs=4
```

### Что происходит при запросе

```
1. POST /register
2. RegistrationService::register()
3. $bus->dispatch(new UserRegistered(…))
   → MessageBus → routing: 'UserRegistered' → transport 'async'
   → Serializer: serialize → JSON
   → Push в Redis/AMQP/БД
4. return $user
5. 200 OK (за 100–200 мс)
```

```
[в фоне, в воркере]
6. Worker: pop из очереди
7. Deserialize → UserRegistered
8. SendWelcomeEmailHandler::__invoke() → SMTP (2–5 с)
9. UpdateSearchIndexHandler::__invoke() → ES (1–3 с)
10. ack в очередь
```

---

## Вариант 2: Messenger + Doctrine transport (без внешней инфраструктуры)

```yaml
transports:
    async:
        dsn: 'doctrine://default?auto_setup=0'
```

Сообщения лежат в таблице `messenger_messages`. Воркер `messenger:consume` забирает из БД.

| Плюсы | Минусы |
|---|---|
| Не нужен Redis/RabbitMQ | Конкуренция: N воркеров гоняются за `SELECT … FOR UPDATE` |
| Транзакционно атомарно с БД-записью | Pro-пущная способность ~500–2000 msg/s (зависит от БД) |
| Простота деплоя | Если БД упала — и сообщения потеряны |
| | Нет «живого» наблюдения за очередью |

**Когда уместно:** маленький проект, < 50 msg/s, нет DevOps. **Когда не уместно:** высокий трафик, нужна надёжность.

---

## Вариант 3: Cloud queue (AWS SQS / GCP Pub/Sub / CloudAMQP)

```yaml
transports:
    async:
        dsn: 'amqp://user:pass@cloudamqp.example.com/vhost/messages'
        # или
        dsn: 'sqs://aws-account-id:aws-secret@aws-region/queue-name'
```

| Плюсы | Минусы |
|---|---|
| Неограниченная пропускная способность | Внешняя зависимость (сеть, доступность) |
| Встроенные retry / dead-letter | Стоимость (по объёму) |
| Мульти-регион, мульти-инстанс | Сердцебиение при сетевых проблемах |
| Мониторинг из консоли облака | Сложность локальной разработки (mock / `sync` DSN) |

---

## Вариант 4: «Грязный» fork (антипаттерн, но иногда виден)

```php
// В контроллере, ПРЯМО в запросе:
$pid = pcntl_fork();
if ($pid === 0) {
    // дочерний процесс
    $this->mailer->send($email);
    $this->search->index($user);
    exit(0);
}
// родитель сразу отвечает 200
```

| Плюсы | Минусы |
|---|---|
| Ноль инфраструктуры | `pcntl_fork` в PHP-FPM — **опасно** (shared memory, opcache) |
| Не нужен воркер | Нет retry, нет idempotency, нет мониторинга |
| | Если сервер упадёт — сообщения потеряны |
| | **Не рекомендуется.** Это «quick fix», который всегда становится техдолгом |

---

## Вариант 5: EventDispatcher (sync) — **НЕ** решает задачу

```php
$dispatcher->dispatch(new UserRegisteredEvent($user));
// Handler вызывается СИНХРОННО в том же процессе.
// SMTP + ES = +5–10 с к ответу пользователю.
```

EventDispatcher в Symfony — **sync by design**. Он не ставит в очередь. Это не bug, а feature: «я хочу, чтобы все слушатели отработали до того, как я уйду». Для вашей задачи — **не подходит**.

Но EventDispatcher можно использовать **внутри** handler'а:

```php
// В SendWelcomeEmailHandler:
$this->bus->dispatch(new EmailSentEvent($user->getId()));
// ← синхронно, но это уже не «основной» поток, а внутри воркера.
// Допустимо, т.к. пользователь уже получил ответ.
```

---

## Критические детали, которые отличают «работает» от «работает в проде»

### 1. Idempotency

Если воркер упал после SMTP, но **до** ack — сообщение будет обработано **дважды**. Пользователь получит два письма. Индекс — два апсета (обычно не критично, `upsert`).

```php
// В handler:
public function __invoke(UserRegistered $event): void
{
    // Проверка: уже отправлено?
    if ($this->emailLog->exists($event->userId, 'welcome')) {
        return;   // ← idempotent skip
    }

    $this->mailer->send($email);
    $this->emailLog->markSent($event->userId, 'welcome');
}
```

### 2. Retry

```yaml
transports:
    async:
        dsn: 'redis://…'
        max_retries: 3
        retry_delay: 5000       # мс
        retry_strategy: exponential
        dead_letter_transport: failure
```

Если SMTP упал (таймаут) — сообщение вернётся в очередь. Вторая, третья попытка. После 3 — в `failure`.

### 3. Failure handling

```php
// config/packages/messenger.yaml
routing:
    'App\Event\UserRegistered': async

transports:
    failure:
        dsn: 'redis://…/failures'
```

```bash
# Инспекция:
php bin/console messenger:failure:show
php bin/console messenger:failure:resolve <id>
php bin/console messenger:failure:reject <id>
```

### 4. Ordering

Письмо **не должно** уйти раньше, чем юзер создан в БД. Но так как `dispatch` происходит **после** `persist` в той же транзакции — порядок гарантирован:

```php
public function register(…): User
{
    $user = $this->users->createUser(…);
    $this->users->persist($user);       // ← БД
    $this->bus->dispatch(new UserRegistered(…));  // ← очередь
    return $user;
}
```

Если `dispatch` **до** `persist` — воркер может обработать сообщение, а юзер ещё не в БД (откат транзакции). **Никогда не делайте так.**

Для «письмо → индекс» ordering внутри воркера: оба handler'а **независимы**. Письмо не зависит от индекса. Порядок не критичен.

### 5. Monitoring

```php
// Middleware: логирование каждого сообщения
final class MessageLoggerMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function handle(Envelope $envelope, callable $next): Envelope
    {
        $this->logger->info('message.dispatched', [
            'class' => $envelope->getMessage()::class,
            'stamp' => $envelope->getLastStamp(SentStamp::class)?->getTransportName(),
        ]);
        return $next($envelope);
    }
}
```

```yaml
buses:
    messenger.bus.default:
        middleware:
            - App\Messenger\Middleware\MessageLoggerMiddleware
            - doctrine_transaction
            - send_failed_messages_to_failure_transport
```

### 6. Tесты

```yaml
# config/packages/test/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'sync'   # ← в тестах сообщения обрабатываются сразу
```

```php
final class RegistrationTest extends KernelTestCase
{
    public function test_register_sends_welcome_email(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register', [
            'email' => 'test@example.com',
            'password' => 'secret123',
            'name' => 'Test',
        ]);

        self::assertResponseStatusCodeSame(201);

        // В sync-режиме handler уже отработал.
        // Проверяем, что письмо ушло (через mock MailerInterface).
        // Или что запись в email_log появилась.
    }
}
```

---

## Trade-off: сводка

| Критерий | Doctrine transport | Redis/AMQP | Cloud SQS | Fork |
|---|---|---|---|---|
| Инфраструктура | БД (уже есть) | Redis/RabbitMQ | Облако | Нет |
| Пропускная способность | ~1k msg/s | ~50k msg/s | ~100k msg/s | ~10 msg/s (FPM) |
| Надёжность | Атомарно с БД | Durable (AOF) | 99.99% SLA | **Нет** |
| Retry / DLQ | Ручной | Встроенный | Встроенный | **Нет** |
| Наблюдаемость | БД-запросы | Редис-команды | CloudWatch | **Нет** |
| Сложность | Низкая | Средняя | Средняя | «Низкая» (ложь) |
| Локальная разработка | Легко | Docker-компа | Mock / `sync` | Легко |
| **Когда** | MVP, < 1k msg/s | Production, 1k–50k | Enterprise, > 50k | **Никогда** |

---

## Что я бы сделал на практике

Для типового Symfony-приложения (e-commerce, SaaS, админка):

```
1. Messenger + Redis transport (или CloudAMQP если уже есть).
2. 2 воркера (systemd/supervisor) или 1 k8s Deployment с 2 репликами.
3. Idempotency в handler'ах (проверка по userId + message_type).
4. Failure transport + ежедневный cron: messenger:failure:show.
5. В тестах: dsn: 'sync'.
6. Логи: каждый dispatched message + каждый consumed message.
7. Алерт: если очередь > 1000 сообщений — Page на дежурного.
```

Для MVP / стартапа:

```
1. Messenger + Doctrine transport.
2. 1 воркер (supervisor).
3. Idempotency.
4. В тестах: dsn: 'sync'.
5. Когда пропускная способность упрётся в БД — мигрировать на Redis/AMQP.
```

Для enterprise / > 50k msg/s:

```
1. Cloud queue (SQS / Pub/Sub).
2. Multi-region consumers.
3. Schema-registry (Avro/Protobuf) для сообщений.
4. Dead-letter + replay.
5. Observability: Prometheus metrics на каждого consumer.
```

---

## Чего НЕ делать

| Антипаттерн | Почему |
|---|---|
| `sleep()` + `file_put_contents()` «в фоне» | Блокирует FPM-worker. Нет retry. Нет idempotency. |
| `pcntl_fork` в FPM | Опасно для shared memory. Нет гарантии доставки. |
| `exec('php worker.php')` в контроллере | Зависимость от PATH, cwd, env. Нет мониторинга. |
| HTTP POST на себя (`/internal/queue`) | Сетевой overhead. Нет гарантии. Двойная маршрутизация. |
| `set_time_limit(0)` + синхронный вызов | Пользователь ждёт 30 с. Gateway timeout. 502. |
| `@Required` + `#[Delayed]` в одном процессе | Нет внешней очереди. Если процесс упадёт — потеряно. |

---

## Итого

Symfony Messenger — это **не** «очередь сообщений». Это **конвейер обработки** с транспортами, middleware, retry, failure, serialization. Очередь — один из транспортов. Вы можете использовать Messenger без очереди (sync), с БД-очередью (doctrine), с Redis, с AMQP, с облаком. Архитектура handler'ов **не меняется** — меняется только транспорт.

Для вашей задачи: `dispatch()` в `RegistrationService`, 2 handler'а с `#[AsMessageHandler]`, async transport, 1–4 воркера, idempotency, failure transport, тесты в sync-режиме. Всё остальное — по мере роста.

> ⏱ 41.2s · prompt 30832 tok · output 5057 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

The user is asking about Symfony's access control system: authentication, roles, and voters. They want to understand the architecture and the decision criteria between voters and roles. Let me structure a comprehensive answer covering:

1. How Symfony security is layered (firewall → authenticator → voter/role)
2. Authentication flow (form login, JWT, etc.)
3. Role-based access (is_granted('ROLE_ADMIN'))
4. Voters (the full mechanism)
5. When to use which, with concrete examples
6. Anti-patterns

I should be concrete with code, show the flow, and give practical decision criteria. Let me write this in Russian as the conversation is in Russian.

</details>

**Кандидат:**

## Общая схема

Symfony Security — это **пять независимых слоёв**, каждый решает свою задачу:

```
HTTP Request
    │
    ▼
┌──────────────────────────────────────────────────────────────┐
│ 1. FIREWALL                                                   │
│    Какой «режим безопасности» для этого URL?                  │
│    (form_login / jwt / api_key / stateless)                   │
└──────────────────────────┬───────────────────────────────────┘
                           ▼
┌──────────────────────────────────────────────────────────────┐
│ 2. AUTHENTICATOR                                              │
│    Кто этот пользователь? Пароль? JWT? Social login?          │
│    Результат: Token { user, roles, attributes }               │
└──────────────────────────┬───────────────────────────────────┘
                           ▼
┌──────────────────────────────────────────────────────────────┐
│ 3. ACCESS CONTROL (per URL)                                   │
│    access_control: [{ path: ^/admin, roles: ROLE_ADMIN }]    │
│    → 401/403 на уровне маршрута, до контроллера              │
└──────────────────────────┬───────────────────────────────────┘
                           ▼
┌──────────────────────────────────────────────────────────────┐
│ 4. ROLES (coarse-grained)                                     │
│    $this->denyAccessUnlessGranted('ROLE_EDITOR')             │
│    → да/нет на основе списка строк                            │
└──────────────────────────┬───────────────────────────────────┘
                           ▼
┌──────────────────────────────────────────────────────────────┐
│ 5. VOTERS (fine-grained)                                      │
│    $this->isGranted('edit', $article)                         │
│    → да/нет на основе объектной логики (владелец, статус…)   │
└──────────────────────────┬───────────────────────────────────┘
                           ▼
                     200 OK / 403
```

Каждый слой **независим**. Можно иметь firewall без voters. Можно иметь voters без ролей (редко, но можно). Можно иметь роли без `access_control` (проверяете вручную в контроллере).

---

## 1. Authentication: как пользователь «становится» пользователем

### Конфигурация

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
            algorithm: auto   # bcrypt, argon2id (auto выбирает лучший доступный)

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email   # по какому полю искать

    firewalls:
        dev:                       # /_wdt, /_profiler — без auth
            pattern: ^/(_(wdt|profiler)|index|app|config|bundles|favicon|build)
            security: false

        api:                       # stateless, JWT или API key
            pattern: ^/api
            stateless: true
            jwt: ~                  # или: api_key: { key: … }
            provider: app_user_provider

        main:                      # form login, cookie-based
            lazy: true             # не загружает user до первого isGranted
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
                enable_csrf: true
            logout:
                path: app_logout
                target: app_login

            remember_me:           # опционально
                secret: '%env(APP_SECRET)%'
                lifetime: 604800   # 7 дней
                name: REMEMBER_ME
                path: /

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: IS_AUTHENTICATED_FULLY }
        - { path: ^/api, roles: IS_AUTHENTICATED_ANONYMOUSLY }  # stateless, проверяем в контроллере
```

### Authenticator: что происходит при логине

```
1. POST /login  (email, password, _csrf_token)
2. FormLoginAuthenticator::supports() → true (URL совпадает)
3. FormLoginAuthenticator::authenticate()
   → UserProviderInterface::loadUserByIdentifier($email)
   → PasswordHasher::verify($storedHash, $rawPassword)
   → Success: Token { user: User, roles: ['ROLE_USER'], attributes: {} }
   → Failure: AuthenticationException → redirect /login?error=…
4. TokenStorage::setToken($token)
5. Session: _security_main = [user_id, csrf_token]
6. Redirect: 302 → /dashboard
```

### `lazy: true` — что это даёт

```php
// Без lazy: каждый запрос к любому URL → SELECT * FROM user WHERE id = …
// С lazy:  SELECT * FROM user WHERE id = … только при ПЕРВОМ isGranted/denyAccess

// В контроллере:
public function index(): Response
{
    // Здесь user ещё НЕ загружен. Lazy.
    $this->denyAccessUnlessGranted('ROLE_USER');  // ← вот тут загрузится
    // …
}
```

Экономит один БД-запрос на каждый публичный endpoint, где пользователь авторизован, но не авторизован.

### JWT (stateless API)

```yaml
# config/packages/lexik_jwt.yaml (lexik/jwt-authentication-bundle)
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
```

```yaml
# firewall
api:
    stateless: true
    jwt: ~
    provider: app_user_provider
```

```
1. POST /api/login  { email, password }
2. JWTAuthenticator → verify → JWT token
3. 200 { "token": "eyJhbGciOi…", "expires_in": 3600 }
4. Клиент: GET /api/orders  Authorization: Bearer eyJhbGciOi…
5. JWTAuthenticator: decode → verify signature → load user
6. Token { user, roles } → доступ
```

Разница с form_login: **нет сессии, нет cookie**. Каждый запрос несёт токен. Подходит для SPA, mobile, API.

---

## 2. Roles: грубый уровень

### Что такое роль

Роль — **строка**. Всё.

```php
// App/Entity/User
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private array $roles;   // ['ROLE_USER', 'ROLE_ADMIN']

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';   // ← baseline: каждый авторизованный пользователь
        return $roles;
    }
}
```

```php
// В БД:
// users: id, email, password_hash, roles (jsonb: ["ROLE_ADMIN"])
```

### Где проверяются

**A. `access_control` (до контроллера)**

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/profile, roles: IS_AUTHENTICATED_FULLY }
```

Symfony генерирует **AccessListener** в firewall. Если роль не совпала — `403 Forbidden`, контроллер **не вызывается**.

**B. `denyAccessUnlessGranted` (в контроллере/сервисе)**

```php
final class ArticleController extends AbstractController
{
    #[Route('/articles/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');   // ← базовая проверка
        // …
    }

    #[Route('/admin/articles', name: 'admin_articles')]
    public function adminList(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // …
    }
}
```

**C. `isGranted` (boolean, без бросания)**

```php
if ($this->isGranted('ROLE_ADMIN')) {
    $items[] = $this->articleRepo->findAllDrafts();
}
```

**D. `@Security` attribute (Symfony 7.1+)**

```php
#[Route('/admin/articles', name: 'admin_articles')]
#[Security("is_granted('ROLE_ADMIN')")]   // ← до вызова метода
public function adminList(): Response { /* … */ }
```

### Что роли НЕ делают

- Не знают, **чей** объект. `ROLE_EDITOR` — может редактировать **любые** статьи.
- Не знают контекст. `ROLE_ADMIN` — не значит «может удалить **свою** статью».
- Не масштабируются. 5 ролей → 5 строк в `access_control`. 50 ролей → 50 строк. 500 ролей → **неудобно**.

---

## 3. Voters: тонкий уровень

### Что такое voter

Voter — **стратегия**: «по какому объекту и для какого действия разрешить/запретить».

```php
interface Voter extends VoterInterface
{
    public function supports(string $attribute, ?object $subject): bool;
    public function vote(int $attribute, mixed $subject, TokenInterface $token): int;
}
```

- `attribute` — **действие**: `'edit'`, `'delete'`, `'publish'`, `'view'`
- `subject` — **объект**: `Article`, `Order`, `User`
- `token` — **текущий пользователь**

Возвращает: `AccessDecisionVoteInterface::ACCESS_GRANTED` / `ACCESS_DENIED` / `ACCESS_ABSTAIN`.

### Пример: редакция статей

```php
final class ArticleVoter implements Voter
{
    public function supports(string $attribute, ?object $subject): bool
    {
        return $subject instanceof Article
            && in_array($attribute, ['edit', 'delete', 'publish', 'view']);
    }

    public function vote(int $attribute, mixed $subject, TokenInterface $token): int
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        /** @var Article $article */
        $article = $subject;

        return match ($attribute) {
            'view' => $this->voteView($article, $user),
            'edit' => $this->voteEdit($article, $user),
            'delete' => $this->voteDelete($article, $user),
            'publish' => $this->votePublish($article, $user),
        };
    }

    private function voteView(Article $a, User $u): int
    {
        // Публичные статьи — все. Черновики — только автор и админ.
        if ($a->getStatus() === ArticleStatus::Published) {
            return self::ACCESS_GRANTED;
        }
        if ($a->getAuthorId() === $u->getId()) {
            return self::ACCESS_GRANTED;
        }
        if (in_array('ROLE_ADMIN', $u->getRoles())) {
            return self::ACCESS_GRANTED;
        }
        return self::ACCESS_DENIED;
    }

    private function voteEdit(Article $a, User $u): int
    {
        if (in_array('ROLE_ADMIN', $u->getRoles())) {
            return self::ACCESS_GRANTED;
        }
        if ($a->getAuthorId() !== $u->getId()) {
            return self::ACCESS_DENIED;
        }
        // Нельзя редактировать опубликованную статью (нужен workflow)
        if ($a->getStatus() === ArticleStatus::Published) {
            return self::ACCESS_DENIED;
        }
        return self::ACCESS_GRANTED;
    }

    private function voteDelete(Article $a, User $u): int
    {
        if (in_array('ROLE_ADMIN', $u->getRoles())) {
            return self::ACCESS_GRANTED;
        }
        return self::ACCESS_DENIED;
    }

    private function votePublish(Article $a, User $u): int
    {
        if (in_array('ROLE_EDITOR', $u->getRoles())) {
            return self::ACCESS_GRANTED;
        }
        return self::ACCESS_DENIED;
    }
}
```

### Регистрация

```php
// Symfony 5.3+: атрибут
#[Voter]
final class ArticleVoter implements Voter { /* … */ }

// Или YAML:
services:
    App\Security\Voter\ArticleVoter:
        tags: [security.voter]   # priority по умолчанию
```

### Использование

```php
// В контроллере:
#[Route('/articles/{id}/edit', name: 'article_edit')]
public function edit(Article $article): Response
{
    $this->denyAccessUnlessGranted('edit', $article);   // ← voter
    // …
}

// В сервисе:
public function delete(Article $article): void
{
    $this->authorization->denyAccessUnlessGranted('delete', $article);
    // …
}

// В API (return 403, не exception):
if (!$this->isGranted('publish', $article)) {
    throw new AccessDeniedException('You cannot publish this article');
}
```

### Как Symfony разрешает

`AuthorizationChecker::isGranted('edit', $article)`:

1. Проходит по **всем** зарегистрированным voter'ам (в порядке `priority`).
2. Для каждого: `$voter->supports('edit', $article)` → `true`/`false`.
3. Для тех, кто поддерживает: `$voter->vote(ACCESS_GRANTED, $article, $token)`.
4. Если **хотя бы один** voter голосует `ACCESS_GRANTED` → **allow** (остальные не спрашиваются).
5. Если **ни один** не поддержал `ACCESS_GRANTED` → **deny**.
6. Если **никто не поддержал** (`supports` → `false` для всех) → **deny** (по умолчанию).

Это **OR-логика**: один «за» = разрешено.

### Priority

```yaml
services:
    App\Security\Voter\ArticleVoter:
        tags: [{ name: security.voter, priority: 100 }]

    App\Security\Voter\OwnershipVoter:   # «всегда: только владелец»
        tags: [{ name: security.voter, priority: 200 }]   # ← выше
```

Высокий priority → голосует **первым**. Если он говорит `ACCESS_GRANTED` — остальные не спрашиваются.

Типичное использование: **OwnershipVoter** (priority 200) говорит «нет, это не твой объект» → даже `ROLE_ADMIN` не пройдёт.

---

## 4. Где что используется: конкретные сценарии

### Сценарий A: админ-панель

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```

**Достаточно роли.** Нет объектов, нет контекста. «Или ты админ, или не заходишь». Voter избыточен.

### Сценарий B: пользователь редактирует свой профиль

```php
// ДО (роль — недостаточно):
if ($user->getId() === $this->getUser()->getId()) {
    // разрешить
}

// ПОСЛЕ (voter):
final class UserVoter implements Voter
{
    public function supports(string $attr, ?object $subj): bool
    {
        return $subj instanceof User && in_array($attr, ['edit', 'delete']);
    }

    public function vote(int $attr, mixed $subj, TokenInterface $token): int
    {
        /** @var User $target, $currentUser */
        if ($target->getId() === $token->getUser()->getId()) {
            return self::ACCESS_GRANTED;
        }
        if (in_array('ROLE_ADMIN', $token->getUser()->getRoles())) {
            return self::ACCESS_GRANTED;
        }
        return self::ACCESS_DENIED;
    }
}

// В контроллере:
$this->denyAccessUnlessGranted('edit', $targetUser);
```

**Voter.** Роль `ROLE_USER` у всех. Различие — в **объекте**.

### Сценарий C: редакция (CRUD на статьях)

| Действие | Кто | Как проверить |
|---|---|---|
| Посмотреть публичную статью | Любой | `access_control` + `IS_AUTHENTICATED_ANONYMOUSLY` |
| Посмотреть черновик | Автор, админ | **Voter** (`view` + `Article`) |
| Редактировать | Автор (если не опубликовано), админ | **Voter** (`edit` + `Article`) |
| Опубликовать | Редактор, админ | **Voter** (`publish` + `Article`) |
| Удалить | Админ | **Voter** (`delete` + `Article`) |

Роли (`ROLE_EDITOR`, `ROLE_ADMIN`) **участвуют** в логике voter'а, но сами по себе не достаточны: `ROLE_EDITOR` не значит «может опубликовать **чужую** статью».

### Сценарий D: API с уровнями доступа

```yaml
# /api/v1/products
# /api/v1/orders
# /api/v1/admin/users
```

```yaml
access_control:
    - { path: ^/api/v1/admin, roles: ROLE_ADMIN }
    - { path: ^/api/v1, roles: IS_AUTHENTICATED_FULLY }
```

**Достаточно ролей.** Объектная логика (чей заказ, чей товар) — в **контроллере** или **voter'е**, если нужно:

```php
// GET /api/v1/orders/{id}
public function getOrder(Order $order, Request $req): Response
{
    $this->denyAccessUnlessGranted('view', $order);   // ← voter: "только владелец"
    return $this->json($order);
}
```

### Сценарий E: RBAC с permissions (сложные матрицы)

50 ролей × 20 объектов = 1000 правил. Роли **не масштабируются**.

```php
// RbacVoter: читает permissions из БД
final class RbacVoter implements Voter
{
    public function __construct(
        private PermissionRepository $perms,   // (role, object_type, action) → bool
    ) {}

    public function supports(string $attr, ?object $subj): bool
    {
        return $subj instanceof \App\Entity\Base;   // базовый класс всех сущностей
    }

    public function vote(int $attr, mixed $subj, TokenInterface $token): int
    {
        $user = $token->getUser();
        $objType = $subj::class;

        foreach ($user->getRoles() as $role) {
            if ($this->perms->hasPermission($role, $objType, $attr)) {
                return self::ACCESS_GRANTED;
            }
        }
        return self::ACCESS_DENIED;
    }
}
```

Таблица `permissions`:

| role | object_type | action |
|---|---|---|
| ROLE_EDITOR | Article | edit |
| ROLE_EDITOR | Article | publish |
| ROLE_EDITOR | Order | view |
| ROLE_ADMIN | * | * |

**Voter + БД.** Роли — строки. Permissions — данные. Новый объект-тип = новая строка в БД, а не новый voter.

---

## 5. Когда что: чёткие критерии

```
Вопрос: "Это решается строкой?"

ДА → Роль
    "Ты админ или нет?"
    "Ты редактор или нет?"
    "Ты авторизован или нет?"
    → access_control + denyAccessUnlessGranted('ROLE_X')

НЕТ → Voter
    "Это твой объект?"
    "Объект в нужном статусе?"
    "У тебя есть право на ЭТО действие с ЭТИМ объектом?"
    → Voter (attribute + subject)

СКОЛЬКО ОБЪЕКТ-ТИПОВ?

1–3   → Отдельный voter на каждый. Просто и понятно.
10+   → Один RbacVoter + таблица permissions.
```

### Антипаттерны

| Что делают | Почему плохо | Правильно |
|---|---|---|
| `if ($user->getId() === $article->getAuthorId() \|\| in_array('ROLE_ADMIN', $user->getRoles()))` в контроллере | Логика доступа в контроллере. Дублируется в 10 местах. | Voter |
| `if ($article->getStatus() === 'draft' && $user->getId() === $article->getAuthorId())` | 3 условия × 4 действия = 12 if'ов в контроллере | Voter + `match` внутри |
| `ROLE_USER_1`, `ROLE_USER_2`, … `ROLE_USER_500` | Роли как permissions. Не масштабируется. | Voter + permissions-таблица |
| `access_control` с 50 правилами | Неудобно читать. Не масштабируется. | Роли для 3–5 URL. Остальное — voter. |
| Voter, который всегда возвращает `ACCESS_GRANTED` | «Безопасность» для галочки. | Уберите voter. Проверяйте роль. |

---

## 6. Архитектурная сводка

```
┌────────────────────────────────────────────────────────────────────┐
│                        SECURITY LAYERS                              │
├──────────────┬─────────────────────────────────────────────────────┤
│  Слой        │  Что решает                                         │
├──────────────┼─────────────────────────────────────────────────────┤
│  Firewall    │  Какой механизм auth для этого URL?                 │
│  (security.yaml)  (form_login / jwt / api_key / none)              │
├──────────────┼─────────────────────────────────────────────────────┤
│  Authenticator│  Кто ты? (password / JWT / social)                  │
│  (Authenticator)│  → Token { user, roles }                          │
├──────────────┼─────────────────────────────────────────────────────┤
│  access_control│  Могут ли ТЫ (по роли) зайти на ЭТОТ URL?          │
│  (security.yaml)│  → 403 до контроллера                              │
├──────────────┼─────────────────────────────────────────────────────┤
│  Roles        │  Какие «грубые» права у тебя?                       │
│  (User::getRoles)│  ROLE_USER, ROLE_ADMIN, ROLE_EDITOR              │
├──────────────┼─────────────────────────────────────────────────────┤
│  Voters       │  Могут ли ТЫ сделать ЭТО с ЭТИМ объектом?           │
│  (Voter)      │  edit Article, delete Order, publish Post           │
└──────────────┴─────────────────────────────────────────────────────┘
```

**Принцип:** каждый следующий слой **уточняет** предыдущий. Firewall: «какой режим». Authenticator: «кто ты». Access_control: «заходишь ли ты сюда». Roles: «какие у тебя права». Voters: «разрешаю ли я это действие на этом объекте».

Если на каком-то уровне вопрос **не требует уточнения** — не усложняйте. Админ-панель — `access_control` + `ROLE_ADMIN`. Конец. Voter не нужен.

---

## 7. Практический чек-лист

```
1. Какой firewall для этого URL?
   → security.yaml, firewalls

2. Какой механизм auth?
   → form_login / jwt / api_key / none

3. Нужен ли access_control (403 до контроллера)?
   → Да, если «все, кто заходят сюда, — админы»
   → Нет, если «зайдут все, но в контроллере решим, кто что видит»

4. Достаточно ли ролей?
   → Да, если вопрос: «Ты X или нет?»
   → Нет, если вопрос: «Ты X и это твой объект и он в нужном статусе?»

5. Сколько объект-типов?
   → 1–3: отдельный voter
   → 10+: RbacVoter + permissions-таблица

6. Есть ли «жёсткий» запрет, который должен перебивать всё?
   → OwnershipVoter с высоким priority (200+)
   → Говорит ACCESS_DENIED первым → остальные не спрашиваются

7. Тестируется ли каждый путь?
   → granted / denied / anonymous / wrong_role / wrong_object
   → 5 тестов на каждое правило
```

> ⏱ 45.4s · prompt 35400 tok · output 5901 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

The user is asking about storing Value Objects in Doctrine ORM when the domain requires immutability and always-valid state. This is a classic DDD + ORM tension in PHP/Doctrine.

Let me think through the key approaches:

1. **Native Doctrine mapping (column-level)** - mapping VO fields directly to columns
2. **Custom Type (Doctrine Type)** - serializing VO to a column
3. **Embeddable** - @Embeddable with a VO class
4. **Array/JSON column** - storing as JSON
5. **Composite key approach** - not really applicable for VO
6. **Separate table per VO** - for VOs with their own queries

Key tensions:
- VOs are immutable → Doctrine hydrates mutable objects
- VOs are always valid → Doctrine creates "in-between" states during hydration
- VOs don't have identity → Doctrine expects identity or embeddable
- Doctrine's UnitOfWork tracks changes → immutable VOs can't be "changed"
- Doctrine creates instances via `newInstance()` → bypasses constructor → bypasses validation

Let me structure this clearly with code examples, trade-offs, and pitfalls for each approach.

Approaches:
1. **Custom Type** (most common, most practical)
2. **Embeddable** (Doctrine-native, but has issues)
3. **JSON column** (flexible, but no type safety at DB level)
4. **Column-level mapping** (for simple VOs like Email, Money)

Pitfalls:
- Hydration bypasses constructor
- `newInstance()` vs `__construct`
- UnitOfWork change tracking
- Comparison for dirty-checking (array vs object)
- Immutability in DB (can't UPDATE a VO without replacing the whole entity)
- Querying by VO parts (WHERE money.amount > 100)

Let me write a thorough, well-structured answer with code.

</details>

**Кандидат:**

## Ядро проблемы

Value Object — это:
- **Неизменяемый**: после создания нельзя мутировать.
- **Всегда валидный**: не существует «половинчатого» `Money(100, null)`.
- **Без identity**: два `Money(100, 'RUB')` — **один и тот же** объект.

Doctrine ORM — это:
- **Гидратация через `newInstance()`** — `__construct` **не вызывается**.
- **UnitOfWork отслеживает мутации** — «изменил свойство → dirty → UPDATE».
- **Ожидает identity или embeddable** — «у тебя нет ID, кто ты такой?»

Эти две системы **концептуально конфликтуют**. Любой способ маппинга — это компромисс. Ниже — пять подходов, от самого практичного до самого экзотического.

---

## Подход 1: Custom Type (самый распространённый)

### Идея

VO сериализуется в **один столбец** (string, JSON, integer). Doctrine не знает, что внутри — VO. Для неё это «чёрный ящик».

### Пример: `Money`

```php
// src/Domain/ValueObject/Money.php
final readonly class Money
{
    private function __construct(
        public readonly int    $amount,    // в минимальных единицах (kopecks)
        public readonly string $currency,
    ) {}

    public static function rub(int $kopecks): self
    {
        if ($kopecks < 0) {
            throw new \InvalidArgumentException('Amount must be >= 0');
        }
        return new self($kopecks, 'RUB');
    }

    public static function usd(int $cents): self
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException('Amount must be >= 0');
        }
        return new self($cents, 'USD');
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Currency mismatch');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return sprintf('%d %s', $this->amount, $this->currency);
    }
}
```

```php
// src/Infrastructure/Doctrine/Type/MoneyType.php
final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Храним как VARCHAR(16): "10500 RUB"
        return $platform->getVarcharTypeDeclarationSQL(['length' => 16]);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        // Money(10500, 'RUB') → "10500 RUB"
        return $value->amount . ' ' . $value->currency;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null || $value === '') {
            return null;
        }
        // "10500 RUB" → Money(10500, 'RUB')
        // ВАЖНО: используем factory-метод, а не конструктор напрямую.
        // Это гарантирует валидацию.
        $parts = explode(' ', $value, 2);
        if (count($parts) !== 2) {
            throw new \RuntimeException("Invalid Money format: $value");
        }
        [$amount, $currency] = $parts;

        if ($currency === 'RUB') {
            return Money::rub((int) $amount);
        }
        if ($currency === 'USD') {
            return Money::usd((int) $amount);
        }
        throw new \RuntimeException("Unknown currency: $currency");
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

```php
// src/Entity/Order.php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;

    public function setTotal(Money $total): void
    {
        $this->total = $total;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }
}
```

```php
// src/Infrastructure/Doctrine/Types.php
final class Types
{
    public const MONEY = MoneyType::NAME;
}
```

### Регистрация

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            money: App\Infrastructure\Doctrine\Type\MoneyType
```

### Как это работает

```
WRITE:
  $order->setTotal(Money::rub(10500))
  → UnitOfWork: dirty (Money ≠ old Money)
  → SQL: UPDATE orders SET total = '10500 RUB' WHERE id = 1

READ:
  → SELECT id, total FROM orders WHERE id = 1
  → Hydration: MoneyType::convertToPHPValue('10500 RUB')
  → Money::rub(10500)  ← factory, валидация пройдена
  → $order->total = Money(10500, 'RUB')
```

### Плюсы

| Преимущество | Пояснение |
|---|---|
| **Валидация гарантирована** | `convertToPHPValue` вызывает factory-метод. Невозможно получить невалидный `Money`. |
| **Иммутабельность** | `readonly` class + `private __construct`. Doctrine создаёт через `convertToPHPValue`, а не через `newInstance()`. |
| **Один столбец** | Простая схема. Не нужно разбивать на 2–3 колонки. |
| **Легко мигрировать** | Новый формат хранения = новый `convertToPHPValue`. Старые данные — в миграции. |
| **Нет «в-between» состояний** | VO создаётся целиком, а не по полю. |

### Минусы

| Недостаток | Пояснение |
|---|---|
| **Нельзя фильтровать по частям** | `WHERE amount > 100` — нет. Только `WHERE total = '10000 RUB'`. Или полный JSON. |
| **Нельзя сортировать** | `ORDER BY amount DESC` — нет. Строка `'10000 RUB'` сортируется лексикографически. |
| **Нет FK-индексов** | `currency` внутри строки. Нельзя `JOIN` по валюте. |
| **Хрупкий формат** | `'10500 RUB'` — если кто-то вручную поменял в БД на `'10500 rub'` — crash. |
| **Несколько VOs в одном сущностном поле** | Если VO содержит вложенный VO — `convertToPHPValue` становится вложенной парсер. |

### Подводные камни

**1. UnitOfWork dirty-checking**

```php
// Doctrine сравнивает старые и новые значения.
// Для объектов — через (string) или == или ===.
// Money — readonly, без __toString для сравнения.

// В UnitOfWork::computeChangeSet():
//   if ($propertyInfo->type === 'object') {
//       $oldValue = $this->originalData[$id][$field];
//       $newValue = $entity->$field;
//       if ($oldValue !== $newValue) { dirty }
//   }

// Money(10500, 'RUB') !== Money(10500, 'RUB')  → TRUE (разные объекты!)
// → Doctrine считает, что значение ИЗМЕНИЛОСЬ → делает UPDATE.
// → Каждый запрос на чтение + сохранение = лишний UPDATE.
```

**Решение:** переопределить сравнение в `convertToDatabaseValue`. Или добавить `equals()`:

```php
// В Money:
public function equals(self $other): bool {
    return $this->amount === $other->amount && $this->currency === $other->currency;
}
```

Но Doctrine **не знает** про `equals()`. Он использует `!==`. Единственный надёжный способ — хранить в **примитивах** (int + string), а не в объекте. Но тогда вы теряете VO.

**Практический обход:** переопределите `__toString()` и убедитесь, что `convertToDatabaseValue` возвращает **детерминированную строку**. Тогда dirty-checking через `convertToDatabaseValue` будет корректным:

```php
// UnitOfWork сравнивает convertToDatabaseValue(old) === convertToDatabaseValue(new)
// '10500 RUB' === '10500 RUB' → true → NOT dirty ✅
```

Это работает, потому что Doctrine **сериализует** объект в строку для сравнения, если объект не `Array` и не имеет `__toString`. Но **надёжнее** явно хешировать:

```php
// В Money:
public function __toString(): string {
    return sprintf('Money(%d,%s)', $this->amount, $this->currency);
}
```

**2. Наследование / полиморфизм**

Если `Money` — часть иерархии (`CurrencyAmount extends Money`), `convertToPHPValue` должен знать, какой класс создать. Иначе — `instanceof`-проверка в каждом `convertToPHPValue`.

---

## Подход 2: Embeddable (нативный Doctrine)

### Идея

VO — отдельный class с `@Embeddable`. Doctrine маппит его поля как **столбцы в родительской таблице**.

```php
// src/Domain/ValueObject/Money.php (embeddable-версия)
#[ORM\Embeddable]
class Money
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    // НЕТ readonly. НЕТ private __construct.
    // Потому что Doctrine создаёт через newInstance().

    public static function rub(int $kopecks): self
    {
        $m = new self();
        $m->amount = $kopecks;
        $m->currency = 'RUB';
        if ($kopecks < 0) throw new \InvalidArgumentException('Amount must be >= 0');
        return $m;
    }

    public function add(self $other): self
    {
        $result = new self();
        $result->amount = $this->amount + $other->amount;
        $result->currency = $this->currency;
        return $result;
    }
}
```

```php
// src/Entity/Order.php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Embedded(class: Money::class)]
    private Money $total;

    #[ORM\Embedded(class: Address::class)]
    private Address $shippingAddress;
}
```

### Как это работает

```
Таблица orders:
  id       | total_amount | total_currency | shipping_street | shipping_city | …
  1        | 10500        | RUB            | Ленинская, 5    | Москва        | …

READ:
  → SELECT id, total_amount, total_currency, … FROM orders
  → Doctrine: newInstance(Money::class)
  → $money->amount = 10500
  → $money->currency = 'RUB'
  → $order->total = $money
  ⚠️ Валидация НЕ ПРОВЕРЕНА.

WRITE:
  → $order->total = Money::rub(20000)
  → UnitOfWork: total_amount: 10500 → 20000 (dirty)
  → SQL: UPDATE orders SET total_amount = 20000 WHERE id = 1
```

### Плюсы

| Преимущество | Пояснение |
|---|---|
| **Нативный SQL** | `WHERE total_amount > 10000 AND total_currency = 'RUB'` — работает. |
| **Сортировка** | `ORDER BY total_amount DESC` — работает. |
| **Индексы** | `INDEX (total_amount, total_currency)` — работает. |
| **JOIN** | Можно `JOIN` на `total_currency` (редко, но возможно). |
| **Менее хрупкий** | Нет парсинга строки. Числа и строки — в отдельных колонках. |

### Минусы

| Недостаток | Пояснение |
|---|---|
| **`newInstance()` обходит конструктор** | Валидация **не срабатывает** при гидратации. `Money(10500, 'XXX')` — создаётся без проверки. |
| **Нельзя `readonly`** | `readonly` + `Embeddable` = конфликт. Doctrine пишет в свойства после создания. |
| **Нельзя `private __construct`** | `newInstance()` использует `ReflectionClass::newInstanceWithoutConstructor()`. |
| **Mutability** | `$money->amount = 0;` — **возможно**, если свойства public/protected. Нарушает иммутабельность. |
| **Валидация только на входе** | Factory-методы (`Money::rub()`) — «контракт». Но Doctrine не знает про них. |
| **Нет `equals()` в dirty-checking** | UnitOfWork сравнивает `amount` и `currency` отдельно. `Money(100, 'RUB')` → `Money(100, 'RUB')` = NOT dirty ✅. Но `Money(100, 'RUB')` → `Money(200, 'RUB')` = dirty ✅. В целом работает, но хрупко. |

### Подводные камни

**1. «В-between» состояние при гидратации**

```
Таблица: total_amount = 10500, total_currency = NULL

→ newInstance(Money::class)
→ $money->amount = 10500      ← Money(10500, null) — НЕВАЛИДНОЕ состояние
→ $money->currency = 'RUB'    ← теперь валидно
```

Если между этими двумя строками кто-то вызовет `$money->add(...)` — будет `DomainException`. На практике это не происходит (гидратация атомарна), но **в концептуальном плане** — VO существовал в невалидном состоянии.

**2. Embeddable не может быть `readonly`**

```php
// НЕ РАБОТАЕТ:
final readonly class Money
{
    #[ORM\Column]
    public int $amount;  // readonly + Embeddable = FATAL
}
```

Doctrine требует **записываемых** свойств. `readonly` запрещает запись после создания. Конфликт.

**3. Embeddable в наследовании**

```php
#[ORM\Entity]
abstract class BaseOrder {
    #[ORM\Embedded(class: Money::class)]
    protected Money $total;
}

#[ORM\Entity]
class PhysicalOrder extends BaseOrder { /* … */ }
#[ORM\Entity]
class DigitalOrder extends BaseOrder { /* … */ }
```

Работает. Но если `DigitalOrder` не использует `total` — столбец всё равно в таблице. Мусор в схеме.

---

## Подход 3: JSON-столбец (flexible, но «чёрный ящик»)

### Идея

Весь VO (или группа VOs) — один JSON-столбец.

```php
// src/Entity/Order.php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::JSON)]
    private array $payment;   // {"amount": 10500, "currency": "RUB", "method": "card"}

    #[ORM\Column(type: Types::JSON)]
    private array $address;   // {"street": "…", "city": "…", "zip": "…"}
}
```

```php
// В домене:
public function getTotal(): Money
{
    return Money::rub($this->payment['amount']);  // ← валидация при чтении
}

public function setTotal(Money $total): void
{
    $this->payment['amount']   = $total->amount;
    $this->payment['currency'] = $total->currency;
}
```

### Плюсы

| Преимущество | Пояснение |
|---|---|
| **Максимальная гибкость** | Новый VO = новый ключ в JSON. Без миграции. |
| **Валидация при чтении** | `Money::rub()` — factory. Не «в-between» состояние. |
| **Нет `readonly`-конфликта** | Хранится как `array`, не как объект. |
| **Легко мигрировать** | `ALTER TABLE … ADD COLUMN …` — не нужно. |

### Минусы

| Недостаток | Пояснение |
|---|---|
| **Нельзя фильтровать** | `WHERE payment.amount > 10000` — нет (PostgreSQL: `WHERE payment->>'amount' > '10000'` — да, но медленнее). |
| **Нельзя сортировать** | Аналогично. |
| **Нет FK-интегрити** | `payment.currency` не проверяется на уровне БД. |
| **Наследование** | Если VO содержит вложенный VO — вложенный JSON. Парсинг усложняется. |
| **Типизация** | `array` — для PHP и для static analysis. `money['amount']` — `mixed`. |

### Когда уместно

- VO **редко меняется** (добавляется новое поле раз в год).
- **Нет SQL-фильтрации** по полям VO.
- **Быстрое прототипирование**.

### Когда не уместно

- `WHERE amount > 10000` — частый запрос.
- `ORDER BY amount` — сортировка.
- VO — часть **ключа** (composite key).

---

## Подход 4: Отдельная таблица (для VOs с identity-подобенностью)

### Идея

Если VO **крупный** (10+ полей), **частый объект для JOIN** и **нужны индексы** — вынесите в отдельную таблицу.

```
orders:          id, status, created_at
order_payments:  order_id (FK), amount, currency, method, card_last4
```

```php
#[ORM\Entity]
class Order
{
    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private Payment $payment;
}

#[ORM\Entity]
class Payment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'payment')]
    private ?Order $order = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    // …
}
```

### Плюсы

| Преимущество | Пояснение |
|---|---|
| **Полный SQL** | `WHERE amount > 10000 AND currency = 'RUB'` — работает. |
| **Индексы** | `INDEX (amount, currency)` — работает. |
| **FK-интегрити** | `order_id` — FK. |
| **Нет JSON-парсинга** | Числа и строки — в колонках. |

### Минусы

| Недостаток | Пояснение |
|---|---|
| **Это уже не VO** | `Payment` с `id` — это **Entity**, а не Value Object. |
| **Наследование DDD нарушено** | VO без identity. Entity с identity. Вы создали Entity под видом VO. |
| **Сложнее** | JOIN, cascade, orphan removal, транзакции. |
| **Ненужная для 90% VOs** | `Money(10500, 'RUB')` не нужен в отдельной таблице. |

### Когда уместно

- VO — **крупный** (10+ полей) и **частый объект для JOIN**.
- Пример: `Address` в e-commerce, если нужно фильтровать по `city`.
- Пример: `Coordinates` в гео-сервисе, если нужно `ST_DWithin`.

### Когда не уместно

- `Money`, `Email`, `Phone` — маленькие, не нужны для JOIN.
- VO, который **не является** частью запросов.

---

## Подход 5: Composite mapping + `__toString` (гибрид)

### Идея

Хранить **примитивы** в колонках, но **в домене** — VO. Граница — в `convertToPHPValue` / `convertToDatabaseValue` (Custom Type) или в getter/setter с factory.

```php
// src/Entity/Order.php
#[ORM\Entity]
class Order
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmount;      // ← примитив в БД

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $totalCurrency; // ← примитив в БД

    // В домене — VO:
    public function getTotal(): Money
    {
        return Money::rub($this->totalAmount);  // ← валидация
    }

    public function setTotal(Money $total): void
    {
        $this->totalAmount   = $total->amount;
        $this->totalCurrency = $total->currency;
    }
}
```

### Плюсы

| Преимущество | Пояснение |
|---|---|
| **SQL-фильтрация** | `WHERE totalAmount > 10000` — работает. |
| **Сортировка** | `ORDER BY totalAmount DESC` — работает. |
| **Иммутабельность в домене** | `getTotal()` возвращает `readonly Money`. |
| **Валидация** | `Money::rub()` — factory. |
| **Нет `readonly`-конфликта** | Хранится как int + string. |

### Минусы

| Недостаток | Пояснение |
|---|---|
| **Дублирование** | `totalAmount` и `totalCurrency` — два поля. `Money` — одно. |
| **В-between** | `setTotal(Money)` записывает `totalAmount`, потом `totalCurrency`. Между ними — невалидное состояние. |
| **Геттер создаёт объект** | Каждый `getTotal()` — новый `Money`. `===` — false. |

### Когда уместно

- Нужно **SQL-фильтрация** + **иммутабельность в домене**.
- VO — **простой** (2–3 поля).
- **Не нужен** `Embeddable` (слишком хрупкий).
- **Не нужен** `Custom Type` (слишком «чёрный ящик»).

---

## Сравнительная таблица

| Критерий | Custom Type | Embeddable | JSON | Отдельная таблица | Composite + factory |
|---|---|---|---|---|---|
| **Иммутабельность** | ✅ (readonly) | ❌ (newInstance) | ✅ (factory) | ❌ (Entity) | ✅ (factory) |
| **Валидация при чтении** | ✅ | ❌ | ✅ | ❌ | ✅ |
| **SQL WHERE по частям** | ❌ | ✅ | ⚠️ (JSON ops) | ✅ | ✅ |
| **SQL ORDER BY** | ❌ | ✅ | ⚠️ | ✅ | ✅ |
| **Индексы** | ❌ | ✅ | ⚠️ | ✅ | ✅ |
| **readonly class** | ✅ | ❌ | ✅ (wrapper) | ❌ | ✅ |
| **Сложность** | Средняя | Низкая | Низкая | Высокая | Средняя |
| **Миграция** | Средняя | Низкая | Низкая | Высокая | Средняя |
| **Гибкость** | Средняя | Низкая | Высокая | Высокая | Средняя |
| **Читаемость домена** | Высокая | Средняя | Средняя | Низкая | Высокая |

---

## Практический выбор

### Правило: размер VOs

```
1–2 поля, нет SQL-фильтрации
    → Custom Type (Money, Email, Phone)

2–5 полей, есть SQL-фильтрация
    → Composite + factory (totalAmount + totalCurrency)

5+ полей, частый JOIN
    → Отдельная таблица (Address, Coordinates)

Непредсказуемая структура
    → JSON (metadata, settings, audit_log)
```

### Правило: SQL-фильтрация

```
Нужно WHERE / ORDER BY / JOIN по полям VO?
    ДА  → Embeddable / Composite / Отдельная таблица
    НЕТ → Custom Type / JSON
```

### Правило: иммутабельность

```
Критично, что VO нельзя мутировать?
    ДА  → Custom Type / Composite + factory / JSON (с wrapper)
    НЕТ → Embeddable (проще)
```

---

## Подводные камни: сводка

| Проблема | Подход | Как избежать |
|---|---|---|
| `newInstance()` обходит конструктор | Embeddable | Не использовать Embeddable для VOs. Или — валидация в `__toString` / getter. |
| `readonly` + Embeddable = FATAL | Embeddable | Не использовать. Custom Type или Composite. |
| Dirty-checking: `Money(100) !== Money(100)` | Custom Type | Определить `__toString()`. Или хранить как примитивы. |
| В-between при гидратации | Embeddable, JSON | Валидация в `convertToPHPValue` / factory. Не полагаться на «Doctrine создаст валидный объект». |
| `WHERE amount > 10000` не работает | Custom Type, JSON | Composite + factory или Embeddable. |
| Наследование + Embeddable | Embeddable | Избегать. Или — отдельная таблица. |
| JSON: `mixed` в static analysis | JSON | `@var` + `instanceof` в getter. Или — Custom Type. |
| Миграция формата | Custom Type | Миграция + `convertToPHPValue` поддерживает старый формат. |

---

## Итого

**Нет «правильного» способа.** Есть **правильный для вашего VO, вашей схемы и ваших запросов**.

- **`Money`, `Email`, `Phone`** → **Custom Type**. Просто, безопасно, достаточно.
- **`Address`, `Coordinates`** → **Composite + factory** или **отдельная таблица** (если JOIN).
- **`Metadata`, `Settings`** → **JSON**. Гибко, не нужно думать.
- **Embeddable** → **избегайте для VOs**. Он создан для «группы полей», а не для «объекта с поведением». Если вам нужно `readonly` + `private __construct` + factory — Embeddable не ваш.

Главное правило: **домен не знает про Doctrine**. VO создаётся factory-методом, мутирует через `add()`, `multiply()`, валидирует в `__construct`. Doctrine — это **транспорт**, который сериализует и десериализует. Ничего больше.

> ⏱ 54.3s · prompt 41206 tok · output 7027 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush. Нужно разобрать на конкретном примере.

Выберу пример: заказ (Order) — корень агрегата, OrderLine — дочерние сущности, OrderStatus — Value Object/enum. Это классический пример с понятной иерархией.

Что нужно покрыть:
1. Маппинг коллекции (OneToMany, cascade, orphanRemoval, lazy/eager)
2. Кто вызывает flush (агрегат? repository? application service?)
3. Границы транзакции (где begin, где commit, где rollback)
4. Подводные камни (lazy loading вне транзакции, detached entities, cascade remove, N+1)
5. Практический код: Order, OrderLine, OrderRepository, OrderService, контроллер

Структурирую ответ:
- Пример агрегата (Order + OrderLines)
- Маппинг: OneToMany, cascade, orphanRemoval, join column
- Маппинг: lazy vs eager, fetch mode
- Unit of Work: как Doctrine отслеживает изменения
- Кто вызывает flush — и почему НЕ агрегат, НЕ repository, а application service
- Границы транзакции
- Подводные камни (detached, lazy вне scope, cascade pitfalls)
- Итоговая схема

Давайте выберу конкретный пример: Order (корень) → OrderLine[] (дети). Плюс Money как VO. Показываю маппинг, lifecycle, flush, транзакции.

Важные моменты:
- OrderLine имеет FK на Order, но НЕ имеет своего ID в доменном смысле (или имеет? Лучше имеет, чтобы быть entity)
- cascade: persist + remove (не merge!)
- orphanRemoval: true — удаление OrderLine из коллекции = удаление строки
- lazy loading: collection инициализируется при первом доступе
- flush: вызывает application service (или controller), НЕ repository, НЕ entity
- транзакция: $em->wrapInTransaction() или beginTransaction/commit/rollback
- UnitOfWork: track changes через dirty checking
- Подводные камни: lazy load после commit (LCS error), detached entities, cascade remove на null

Напишу развёрнуто, с кодом и пояснениями.

</details>

**Кандидат:**

## Агрегат: Order → OrderLine[]

Выбираю пример, который встречается в 80% e-commerce и SaaS:

```
Order (корень, root)
├── id, status, createdAt
├── Money total          ← VO
└── OrderLine[] lines    ← дочерние сущности (children)
    ├── id, productId, qty, unitPrice (Money)
    └── FK → Order.id
```

`Order` — **агрегат**. `OrderLine` — **часть агрегата**, не самостоятельная сущность. Внешний мир обращается к `Order`, никогда — к `OrderLine` напрямую. Это определяет и маппинг, и flush, и транзакции.

---

## 1. Маппинг коллекции

### Order (корень)

```php
// src/Domain/Order.php
final class Order
{
    private ?int $id = null;
    private OrderStatus $status;
    private Money $total;
    /** @var Collection<int, OrderLine> */
    private Collection $lines;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $email, Money $total)
    {
        $this->status    = OrderStatus::Pending;
        $this->total     = $total;
        $this->lines     = new ArrayCollection();   // ← Doctrine Collection
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- Aggregate invariants ---

    public function addLine(Product $product, int $qty): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Cannot add line to non-pending order');
        }
        if ($qty < 1) {
            throw new \InvalidArgumentException('Quantity must be >= 1');
        }

        $line = new OrderLine($product, $qty);
        $this->lines->add($line);

        $this->recalculateTotal();
    }

    public function removeLine(int $lineId): void
    {
        $this->lines = $this->lines->filter(
            fn(OrderLine $l) => $l->getId() !== $lineId
        );

        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Only pending orders can be confirmed');
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function cancel(string $reason): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new StateException('Cannot cancel delivered order');
        }
        $this->status = OrderStatus::Cancelled;
    }

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->getTotal()->amount;
        }
        $this->total = Money::rub($sum);
    }

    // --- Getters ---

    public function getId(): ?int { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getTotal(): Money { return $this->total; }
    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // --- Infrastructure hook (DO NOT call from domain) ---
    public function setId(int $id): void { $this->id = $id; }
}
```

### OrderLine (дочерняя сущность)

```php
// src/Domain/OrderLine.php
final class OrderLine
{
    private ?int $id = null;
    private Product $product;
    private int $qty;
    private Money $unitPrice;

    public function __construct(Product $product, int $qty)
    {
        if ($qty < 1) {
            throw new \InvalidArgumentException('Quantity must be >= 1');
        }
        $this->product   = $product;
        $this->qty       = $qty;
        $this->unitPrice = $product->getPrice();  // фиксируем цену на момент покупки
    }

    public function getTotal(): Money
    {
        return Money::rub($this->unitPrice->amount * $this->qty);
    }

    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getQty(): int { return $this->qty; }
    public function getUnitPrice(): Money { return $this->unitPrice; }

    public function setId(int $id): void { $this->id = $id; }
}
```

### Маппинг (attribute-версия)

```php
// src/Infrastructure/Persistence/Entity/OrderEntity.php
#[ORM\Entity(repositoryClass: OrderEntityRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\Index(columns: ['status'], name: 'idx_orders_status')]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: OrderStatus::class)]
    private OrderStatus $status;

    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    // ────────────────────────────────────────────────────────────
    // КЛЮЧЕВОЙ МАППИНГ: OneToMany
    // ────────────────────────────────────────────────────────────
    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderLineEntity::class,
        cascade: ['persist', 'remove'],   // ← persist + remove, НЕ merge
        orphanRemoval: true,              // ← удаление из коллекции = DELETE
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]       // ← детерминированный порядок
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    // … getters/setters для маппинга
}
```

```php
// src/Infrastructure/Persistence/Entity/OrderLineEntity.php
#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
#[ORM\Index(columns: ['order_id'], name: 'idx_lines_order')]
class OrderLineEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // ────────────────────────────────────────────────────────────
    // КЛЮЧЕВОЙ МАППИНГ: ManyToOne (обратная сторона)
    // ────────────────────────────────────────────────────────────
    #[ORM\ManyToOne(
        inversedBy: 'lines',
        targetEntity: OrderEntity::class,
    )]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        nullable: false,              // ← FK обязателен
        onDelete: 'CASCADE',          // ← DB-level: удаление Order удаляет линии
    )]
    private ?OrderEntity $order = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $qty;

    #[ORM\Column(type: MoneyType::NAME)]
    private Money $unitPrice;

    // … getters/setters
}
```

### Что делают эти атрибуты

| Атрибут | Эффект |
|---|---|
| `cascade: ['persist']` | Когда вы `$em->persist($order)` — Doctrine **автоматически** persist'ит все `OrderLine` из коллекции. Не нужно persist'ить каждую линию отдельно. |
| `cascade: ['remove']` | Когда вы `$em->remove($order)` — Doctrine **автоматически** remove'ит все `OrderLine`. |
| `orphanRemoval: true` | Когда вы `$order->getLines()->removeElement($line)` — Doctrine **удалит** строку `order_lines` из БД. Без этого — просто «отвяжется», строка останется. |
| `onDelete: 'CASCADE'` (DB) | На уровне БД: `DELETE FROM orders WHERE id = 1` → каскадно удаляет `order_lines`. **Дополнительная страховка**, если кто-то удалит Order минуя Doctrine. |
| `inversedBy: 'lines'` | Указывает, что `lines` — **владелец** связи. Doctrine знает, где хранить FK. |
| `mappedBy: 'order'` | Указывает, что `order` — **обратная сторона**. |
| `nullable: false` | `OrderLine` **обязан** иметь `Order`. Невозможно создать «сироту». |
| `#[ORM\OrderBy]` | Детерминированный порядок при lazy-load. Без этого — порядок зависит от БД. |

### Чего **нет** в маппинге

- `cascade: ['merge']` — **никогда не нужен** для агрегата. `merge` — для detached-объектов из другого UnitOfWork. В пределах одного запроса — не используется.
- `eager` (fetch mode) — **по умолчанию `lazy`**. Eager для `lines` означал бы `JOIN` на каждом `SELECT orders`. Для агрегата, где `lines` почти всегда нужны — допустимо. Но лучше **explicit fetch** в repository.

---

## 2. Unit of Work: как Doctrine отслеживает изменения

### Что происходит при `$em->persist($order)`

```php
$em->persist($order);
```

1. Doctrine добавляет `OrderEntity` в **Identity Map** (`$this->entities['App\Entity\OrderEntity'][1]`).
2. Для `cascade: ['persist']`: Doctrine рекурсивно persist'ит все `OrderLineEntity` из `$order->lines`.
3. Статус: `OrderEntity` → **MANAGED**, `OrderLineEntity` → **MANAGED**.
4. **SQL ещё не отправлен.** Это только «отметка в UnitOfWork».

### Что происходит при мутации

```php
$order->setStatus(OrderStatus::Confirmed);
// → $orderEntity->status = OrderStatus::Confirmed
```

1. Property accessor: `OrderEntity::$status` меняется.
2. UnitOfWork **не знает** об этом. Он сравнивает **только при `flush()`**.
3. На `flush()`: `UnitOfWork::computeChangeSet()` → `oldData.status = Pending`, `newData.status = Confirmed` → **dirty** → генерирует `UPDATE`.

### Что происходит при `orphanRemoval`

```php
$lines = $order->getLines();
$lines->removeElement($lineToRemove);
$lines->add($newLine);
```

1. `ArrayCollection` хранит `add`/`remove` в **internal queues** (`$addQueue`, `$removeQueue`).
2. На `flush()`:
   - `$removeQueue` → `DELETE FROM order_lines WHERE id = …`
   - `$addQueue` → `INSERT INTO order_lines …`
3. **Важно:** `removeElement` — это **не** `$em->remove($line)`. Это «отвязать от коллекции». `orphanRemoval: true` превращает это в `DELETE`.

### Что происходит при `$em->remove($order)`

1. `OrderEntity` → **REMOVED**.
2. `cascade: ['remove']`: все `OrderLineEntity` → **REMOVED**.
3. На `flush()`: `DELETE FROM orders WHERE id = …` + `DELETE FROM order_lines WHERE order_id = …`.
4. `onDelete: 'CASCADE'` в БД — **страховка**, на случай если кто-то удалит Order минуя Doctrine (например, через SQL-скрипт).

---

## 3. Кто вызывает `flush`

### Правило

**`flush` вызывает application service (или, в простом случае, controller). Никогда не агрегат, никогда не repository.**

### Почему не агрегат

```php
// ПЛОХО: агрегат знает про EntityManager
final class Order
{
    public function __construct(private EntityManagerInterface $em) {}

    public function addLine(Product $p, int $qty): void
    {
        $this->lines->add(new OrderLine($p, $qty));
        $this->recalculateTotal();
        $this->em->flush();   // ← АГРЕГАТ ЗНАЕТ ПРО ИНФРАСТРУКТУРУ
    }
}
```

Проблемы:
- Агрегат зависит от `EntityManagerInterface` — **нарушение DIP**.
- `flush` — это **side effect**. Агрегат — **чистая логика**. Смешение — нарушение SRP.
- Нельзя протестировать агрегат без БД.

### Почему не repository

```php
// СРЕДНЕ: repository делает flush
class OrderRepository
{
    public function save(Order $order): void
    {
        $entity = $this->mapToEntity($order);
        $this->em->persist($entity);
        $this->em->flush();   // ← repository знает про транзакцию
    }
}
```

Проблемы:
- Repository — **одна операция** (save). А flush — **транзакция**, которая может включать несколько операций.
- Если вы хотите «save Order + save AuditLog» в одной транзакции — repository не знает об этом.
- Repository **должен быть тонким**: маппинг + CRUD. Не бизнес-логика.

### Правильно: application service

```php
// src/Application/Order/PlaceOrderService.php
final class PlaceOrderService
{
    public function __construct(
        private OrderRepository $repo,
        private ProductRepository $products,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $em,   // ← только здесь
    ) {}

    /**
     * @throws StateException
     * @throws \InvalidArgumentException
     */
    public function place(string $email, array $items): Order
    {
        // 1. Загрузка (read)
        $order = new Order($email, Money::rub(0));

        foreach ($items as $item) {
            $product = $this->products->findById($item['productId']);
            $order->addLine($product, $item['qty']);
        }

        // 2. Сохранение (write) — одна транзакция
        $this->em->wrapInTransaction(function () use ($order) {
            $this->repo->save($order);   // persist, без flush
        });

        // 3. Побочные эффекты (async, после commit)
        $this->dispatcher->dispatch(new OrderPlacedEvent($order));

        return $order;
    }
}
```

```php
// src/Domain/OrderRepository.php (интерфейс)
interface OrderRepository
{
    public function findById(int $id): Order;
    public function save(Order $order): void;   // ← persist, БЕЗ flush
    public function remove(Order $order): void;
}
```

```php
// src/Infrastructure/Persistence/OrderEntityRepository.php
final class OrderEntityRepository implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderMapper $mapper,
    ) {}

    public function save(Order $order): void
    {
        $entity = $order->getId() !== null
            ? $this->em->find(OrderEntity::class, $order->getId())
            : new OrderEntity();

        $this->mapper->apply($order, $entity);
        $this->em->persist($entity);
        // ← НЕТ flush(). Flush делает application service.
    }
}
```

### Почему именно `wrapInTransaction`

```php
$this->em->wrapInTransaction(function () use ($order) {
    $this->repo->save($order);
});
```

Это синтаксический сахар над:

```php
$this->em->beginTransaction();
try {
    $this->repo->save($order);
    $this->em->commit();
} catch (\Throwable $e) {
    $this->em->rollback();
    throw $e;
}
```

`wrapInTransaction` гарантирует:
- **Atomicity**: либо всё сохранено, либо ничего.
- **Изоляция**: другие сессии не видят промежуточное состояние.
- **Авто-rollback**: исключение → rollback → `throw $e`.

---

## 4. Границы транзакций

### Что внутри транзакции

```
┌─────────────────────────────────────────────────────┐
│  TRANSACTION                                        │
│                                                     │
│  1. Persist OrderEntity                             │
│  2. Persist OrderLineEntity[] (cascade)             │
│  3. UPDATE orders SET status = 'pending'            │
│  4. INSERT order_lines (N строк)                    │
│  5. COMMIT                                          │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### Что **вне** транзакции

```
  6. dispatch(OrderPlacedEvent)         ← async, после commit
     → SendWelcomeEmail                 ← SMTP (5 с)
     → UpdateSearchIndex                ← HTTP (2 с)
     → NotifyWarehouse                  ← HTTP (1 с)
```

Почему? Если SMTP упадёт — **заказ должен быть сохранён**. Email отправим retry. Если email будет внутри транзакции — SMTP-таймаут → rollback → заказ **потерян**.

### Несколько операций в одной транзакции

```php
public function placeAndReserveStock(string $email, array $items): Order
{
    $order = new Order($email, Money::rub(0));
    foreach ($items as $item) {
        $product = $this->products->findById($item['productId']);
        $order->addLine($product, $item['qty']);
    }

    $this->em->wrapInTransaction(function () use ($order, $items) {
        $this->repo->save($order);

        // Резервирование стока — в той же транзакции.
        // Если сток не хватает — rollback всего.
        foreach ($items as $item) {
            $this->inventory->reserve($item['productId'], $item['qty']);
            // ← если не хватает → StateException → rollback
        }
    });

    $this->dispatcher->dispatch(new OrderPlacedEvent($order));
    return $order;
}
```

Здесь `reserve` — **доменная операция**, которая может бросить `InsufficientStockException`. Если бросила — **всё** откатывается: Order, OrderLines, сток.

### Несколько транзакций (long-running)

```php
public function placeLargeOrder(string $email, array $items): Order
{
    // Транзакция 1: создание заказа
    $order = new Order($email, Money::rub(0));
    foreach ($items as $item) {
        $product = $this->products->findById($item['productId']);
        $order->addLine($product, $item['qty']);
    }
    $this->em->wrapInTransaction(fn() => $this->repo->save($order));

    // Транзакция 2: резервирование стока (может быть долгим)
    $this->em->wrapInTransaction(fn() => $this->reserveAll($order->getLines()));

    // Транзакция 3: подтверждение
    $order->confirm();
    $this->em->wrapInTransaction(fn() => $this->repo->save($order));

    // Асинхронно
    $this->dispatcher->dispatch(new OrderPlacedEvent($order));
    return $order;
}
```

**Риск:** если транзакция 2 упадёт — Order создан, но сток не зарезервирован. Нужен **compensating transaction** (отмена заказа) или **idempotent retry**.

---

## 5. Подводные камни

### 5.1. Lazy loading вне транзакции (LCS)

```php
// В контроллере:
$order = $this->repo->findById(1);   // ← загрузил Order, линии НЕ загружены
// …
$lines = $order->getLines();         // ← LAZY INIT → SELECT order_lines WHERE order_id = 1
// ← Это работает, потому что EntityManager ещё открыт.
```

```php
// В async handler (отдельный процесс, новый EntityManager):
$order = $this->em->find(Order::class, 1);
// …
$lines = $order->getLines();         // ← LAZY INIT
// ← Если EntityManager закрыт (например, после транзакции) →
//    "Trying to load lazy entity … EntityManager is closed" (LCS error)
```

**Решение:**
- **Eager fetch** в repository: `DQL: SELECT o, l FROM Order o JOIN o.lines l WHERE o.id = :id`
- **Explicit `initialize`**: `$em->initializeObject($lines)` — только до закрытия EM.
- **Никогда не храните lazy-коллекции в async-контексте** без предварительной инициализации.

### 5.2. Detached entities

```php
// Транзакция 1:
$order = $this->repo->findById(1);
$this->em->clear();   // ← Order → DETACHED

// Транзакция 2:
$order->confirm();     // ← мутация
$this->repo->save($order);   // ← persist (не merge!)
// ← Doctrine: "Entity of type … is not MANAGED"
```

**Решение:**
- **Не `clear()` внутри транзакции**, если нужны изменения.
- Если нужно — **перезагрузить**: `$order = $this->em->find(Order::class, $id)`.
- `merge` — только для **внешних** источников (API, импорт). Не для агрегатов.

### 5.3. `orphanRemoval` + `cascade remove` = двойное удаление

```php
$order->getLines()->removeElement($line);   // ← orphanRemoval → DELETE
$em->remove($order);                         // ← cascade remove → DELETE (ещё раз)
```

**Не ломается** (DELETE по PK идемпотентен), но **лишний SQL**. Избегайте: если `remove($order)` — не трогайте `lines` вручную.

### 5.4. N+1 при итерации

```php
// ПЛОХО: N+1
foreach ($orders as $order) {
    $lines = $order->getLines();   // ← SELECT на каждую итерацию
    // …
}
```

**Решение:**

```php
// DQL: JOIN (один запрос)
$orders = $this->em->createQuery(
    'SELECT o FROM OrderEntity o JOIN o.lines WHERE o.status = :status'
)->setParameter('status', OrderStatus::Pending)
->getResult();
// ← lines уже загружены (eager через JOIN)

// ИЛИ: Doctrine Collections' batch
foreach ($orders as $i => $order) {
    if ($i % 50 === 0 && $i > 0) {
        $this->em->clear();
    }
    // …
}
```

### 5.5. `cascade: ['merge']` — ловушка

```php
// Если вы случайно добавите merge:
#[ORM\OneToMany(
    cascade: ['persist', 'remove', 'merge'],   // ← merge!
    orphanRemoval: true,
)]
```

`merge` **копирует** состояние detached-объекта в managed. Для агрегата это **не нужно**. Если вы делаете `merge($order)` — Doctrine перекроет **все** линии, даже те, которые вы **не** меняли. **Уберите `merge` из cascade.**

### 5.6. `null` в коллекции

```php
$order->getLines()->add(null);   // ← TypeError, но если обойти:
// Doctrine: INSERT order_lines (order_id = NULL, …)
// ← FK nullable: false → DB error
```

**Защита:** в домене — `Collection<int, OrderLine>` (типизация). В mapper — `assert($line instanceof OrderLine)`.

---

## 6. Итоговая схема

```
HTTP Request
    │
    ▼
Controller
    │  $service->place($email, $items)
    ▼
┌──────────────────────────────────────────────────────────────────┐
│  PlaceOrderService (application layer)                           │
│                                                                 │
│  1. $order = new Order($email, Money::rub(0))                   │
│  2. foreach ($items) → $order->addLine($product, $qty)         │
│     ← доменная логика, инварианты, recalculateTotal             │
│                                                                 │
│  3. $this->em->wrapInTransaction(function () {                  │
│         $this->repo->save($order);   ← persist, cascade         │
│         $this->inventory->reserve(…); ← доменная операция       │
│     })                                                          │
│     ← flush + commit АУТОМАТИЧЕСКИ внутри wrapInTransaction    │
│                                                                 │
│  4. $this->dispatcher->dispatch(new OrderPlacedEvent($order))  │
│     ← async, после commit                                       │
│                                                                 │
└──────────────────────────────────────────────────────────────────┘
    │
    ▼
SQL (внутри транзакции):
  INSERT INTO orders (status, total, created_at) VALUES (…);
  INSERT INTO order_lines (order_id, product_id, qty, unit_price) VALUES (…), (…);
  UPDATE inventory SET stock = stock - 2 WHERE product_id = …;
  COMMIT;

SQL (после, вне транзакции):
  [async] INSERT INTO emails (to, subject, …) VALUES (…);
  [async] POST /search/index { … }
```

### Ответственность каждого слоя

| Слой | Что делает | Что НЕ делает |
|---|---|---|
| **Aggregated (`Order`)** | Инварианты, логика, `addLine`, `confirm`, `recalculateTotal` | `flush`, `persist`, `EntityManager`, `DB` |
| **Repository** | Маппинг Domain ↔ Entity, `persist`, `find` | `flush`, бизнес-логика, `wrapInTransaction` |
| **Application Service** | Оркестрация, `wrapInTransaction`, `flush`, dispatch events | Бизнес-логика, SQL, `EntityManager` (кроме `wrapInTransaction`) |
| **Controller** | HTTP → service → HTTP | Логика, `EntityManager`, транзакции |

### Правило: `flush` — один раз, на верхнем уровне

```
Controller → Service → Repository → EntityManager
              ↑                    ↑
         flush здесь        persist здесь
         (один раз)        (без flush)
```

Если у вас **три** `flush` в одном запросе — вы либо **не понимаете границы транзакций**, либо **делаете три транзакции вместо одной**.

> ⏱ 53.0s · prompt 47890 tok · output 6871 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о диагностике и лечении N+1 в Doctrine. Это практический вопрос, который требует:

1. Диагностика: как найти N+1 (profiler, logs, SQL)
2. Решения: DQL JOIN, fetch modes, batching, denormalization, CTE, и т.д.
3. Trade-offs каждого решения

Структурирую ответ:
- Сначала диагностика (как найти)
- Потом решения (от простых к сложным)
- Каждое решение с trade-offs
- Практические примеры кода

Нужно быть конкретным, с кодом, с реальными trade-offs. Не абстрактно.

Диагностика:
1. Doctrine Profiler / Symfony Profiler
2. SQL logs (DBAL logger)
3. EXPLAIN / pg_stat_statements
4. Статический анализ (PHPStan с плагином)
5. Ручной анализ: WHERE цикл + lazy load

Решения:
1. DQL JOIN (eager через JOIN)
2. fetch mode EAGER (в атрибутах)
3. Partial Objects (SELECT o.id, o.name)
4. setFetchMode
5. Batch + clear (для больших списков)
6. Denormalization (дублировать поле)
7. CTE / native query
8. Composite primary keys / embedded
9. Cache (Second Level Cache / Query Cache)
10. Переписать логику (не нужен N+1)

Trade-offs для каждого.

Давайте структурирую хорошо.

</details>

**Кандидат:**

## Диагностика

Прежде чем лечить — убедиться, что это именно N+1, а не что-то другое (медленный JOIN, missing index, сериализация, PHP overhead).

### Шаг 1: Symfony Profiler / Doctrine Profiler

```bash
# Включить profiler (dev/test):
# config/packages/framework.yaml
framework:
    profiler:
        collect: true
```

Открыть `/ _profiler`, выбрать запрос, вкладка **Query**:

```
Query 1:  SELECT o.* FROM orders o WHERE o.status = 'pending'          ← 1 запрос
Query 2:  SELECT u.* FROM users u WHERE u.id = 42                      ← N+1 начинается
Query 3:  SELECT u.* FROM users u WHERE u.id = 43
Query 4:  SELECT u.* FROM users u WHERE u.id = 44
...
Query 200: SELECT u.* FROM users u WHERE u.id = 241
```

**Признак N+1:** N+1 одинаковых (или почти одинаковых) запросов к одной таблице с разными параметрами, следующими за одним «родительским» запросом.

### Шаг 2: SQL-лог (без profiler)

```php
// src/Infrastructure/Doctrine/SqlLogger.php
use Doctrine\DBAL\Logging\Logger;
use Psr\Log\LoggerInterface;

final class SqlLogger implements Logger
{
    public function __construct(private LoggerInterface $log) {}

    public function startQuery(string $sql, ?array $params = null, ?array $types = null): void {}

    public function stopQuery(): void
{
}

    public function getConnection(): \Doctrine\DBAL\Connection { /* … */ }

    // В Doctrine 2.x:
    public function startQuery($sql, array $params = null, array $types = null) {
        // не логируем здесь
    }

    public function stopQuery() {
        // не логируем здесь
    }

    public function info($message, array $params = []) {
        // не используется
    }

    // Ключевой метод:
    public function logQuery($sql, $params = null, $types = null)
    {
        $this->log->debug('SQL', ['sql' => $sql, 'params' => $params]);
    }
}
```

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            middleware:
                - App\Infrastructure\Doctrine\SqlLoggerMiddleware
```

Или проще — через `Doctrine\DBAL\Logging\SQLLogger`:

```yaml
doctrine:
    dbal:
        logging:
            sql:
                enabled: true
                logger: 'monolog.logger.doctrine'
```

Затем в логах ищем **повторяющийся паттерн**:

```bash
grep "SELECT.*FROM users.*WHERE.*id =" app.log | sort | uniq -c | sort -rn
# 200  SELECT u.* FROM users u WHERE u.id = ?
#   1  SELECT o.* FROM orders o WHERE o.status = ?
```

200 одинаковых запросов к `users` — это N+1.

### Шаг 3: pg_stat_statements (PostgreSQL)

```sql
-- На сервере PostgreSQL:
SELECT
    calls,
    mean_exec_time,
    total_exec_time,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

Если видите `calls = 200, query = 'SELECT … FROM users …'` — это N+1.

### Шаг 4: Статический анализ (профилактика)

```json
// composer.json
"require-dev": {
    "beberlei/assert": "^3.0",
    "drenso/doctrine-static-analysis": "^1.0"
}
```

Или проще — **PHPStan** с правилом: если в цикле вы обращаетесь к lazy-ассоциации — warning.

```php
// PHPStan: level 8 + custom rule
// Если $order->getCustomer() вызывается в foreach — флаг.
```

### Шаг 5: Ручной аудит кода

```php
// КРАСНЫЙ ФЛАГ:
foreach ($orders as $order) {
    $customer = $order->getCustomer();   // ← lazy load → N+1
    $lines    = $order->getLines();      // ← lazy load → N+1
    $total    = $order->getTotal();
}
```

Каждый доступ к **незагруженной** lazy-ассоциации внутри цикла — потенциальный N+1.

---

## Решения: от простых к сложным

### 1. DQL JOIN (самое частое решение)

**Проблема:**

```php
// Repository
public function findPending(): array
{
    return $this->em->getRepository(OrderEntity::class)
        ->findBy(['status' => OrderStatus::Pending]);
    // ← 1 запрос к orders
}

// Controller
foreach ($orders as $order) {
    $customer = $order->getCustomer();   // ← N+1: 1 запрос на каждый order
    // …
}
```

**Решение:**

```php
public function findPending(): array
{
    return $this->em->createQuery(
        'SELECT o, u FROM OrderEntity o
         JOIN o.customer u
         WHERE o.status = :status'
    )
    ->setParameter('status', OrderStatus::Pending)
    ->getResult();
    // ← 1 запрос, JOIN, customer уже загружен
}
```

**Что происходит:**

```sql
SELECT o.*, u.*
FROM orders o
JOIN users u ON u.id = o.customer_id
WHERE o.status = 'pending';
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Один запрос вместо N+1 | **Картезианово произведение**: если у `Order` 2 ассоциации (`customer`, `lines`), JOIN создаёт `rows = orders × customers × lines`. Для 100 заказов × 5 линий = 500 строк. |
| Не меняет маппинг (не нужно `EAGER`) | Дублирование данных в результ

> ⏱ 17.4s · prompt 54288 tok · output 1768 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о роли ограничений целостности в PostgreSQL в контексте приложения, где бизнес-валидация уже есть в коде (доменные сущности, aggregate invariants, и т.д.). Это вопрос о layering и defense in depth.

Нужно:
1. Обосновать, почему DB constraints НЕ дублируют бизнес-валидацию, а защищают от других угроз.
2. Перечислить конкретные средства PostgreSQL: CHECK, UNIQUE, NOT NULL, FK, EXCLUDE, generated columns, triggers, advisory locks, SERIALIZABLE isolation.
3. Показать, когда что используется и почему.
4. Привести примеры кода (SQL + PHP) для контраста.
5. Честно указать, где DB constraints НЕ нужны и даже вредят.

Структура:
- Почему код не защищает всё (мульти-процессность, raw SQL, миграции, админки, cron, другие сервисы)
- Средства PostgreSQL: NOT NULL, UNIQUE, CHECK, FK, EXCLUDE, generated columns, triggers, advisory locks, transaction isolation
- Когда что использовать (таблица)
- Примеры: money (CHECK amount >= 0), unique email, exclusion (overlapping time slots), advisory locks (race conditions)
- Антипаттерны: дублирование сложной бизнес-логики в триггерах, CHECK-выражения, которые не могут выразить доменное правило
- Итого: DB constraints — это safety net для «плохих сценариев», а не замена доменной логике.

Язык — русский, как и весь диалог. Формат — Markdown, таблицы, код.

</details>

**Кандидат:**

## Короткий ответ

Бизнес-валидация в коде защищает от **ошибок разработки**. Ограничения БД защищают от **всего остального**: гонки между процессами, raw SQL, другие сервисы, cron-скрипты, ручные правки в админке, откатившиеся миграции, баги в самом коде, которые прошли через код-ревью. Это **разные угрозы**, требующие разных слоёв защиты.

---

## Почему код недостаточно

### 1. Мульти-процессность

```
Процесс A: SELECT stock FROM products WHERE id = 1;  → 5
Процесс B: SELECT stock FROM products WHERE id = 1;  → 5
Процесс A: UPDATE products SET stock = 4 WHERE id = 1;  ✓
Процесс B: UPDATE products SET stock = 4 WHERE id = 1;  ✓
→ stock = 4, хотя должно быть 3. Сверхпродажа.
```

Код каждого процесса **в отдельности** валиден. Но два процесса **одновременно** нарушают инвариант. Только БД может атомарно проверить и применить.

### 2. Источники записи ≠ ваше приложение

| Источник | Проходит через домен? |
|---|---|
| Symfony-контроллер → service → aggregate | ✅ Да |
| Cron-скрипт `php bin/console cron:sync` | ✅ Да (если написан правильно) |
| Raw SQL из `psql` / DBeaver / админки | ❌ Нет |
| Другой микросервис (Node, Go) | ❌ Нет |
| Mиграция (DataFixtures, SQL-скрипт) | ❌ Нет |
| Репликация / backup-restore | ❌ Нет |
| Bug в самом PHP-коде (опечатка, race) | ⚠️ Частично |

DB constraint — **последний барьер**, через который невозможно «перепрыгнуть».

### 3. Бизнес-логика ≠ схема данных

```php
// В домене:
class Order {
    public function addLine(Product $p, int $qty): void {
        if ($qty < 1) throw new \Exception('qty must be >= 1');
        if ($this->status !== OrderStatus::Pending) throw new \Exception('…');
        // …
    }
}
```

Это **бизнес-правило**. Оно меняется с продуктом. А то, что `qty` — целое, положительное, а `status` — из известного набора — это **схема данных**. Она стабильна. Смешивать их — значит каждый раз при изменении бизнес-логики трогать БД-миграцию и наоборот.

### 4. Fail-fast vs fail-late

```
Код:   invalid data → Exception → 500 → лог → откат
БД:    invalid data → ERROR → транзакция откатывается → 500 → лог → откат
```

Оба дают 500. Но БД **гарантирует**, что невалидная строка **никогда** не попадёт в таблицу. Даже если код «забыл» проверить. Это safety net, а не замена.

---

## Средства PostgreSQL и когда они нужны

### 1. `NOT NULL`

**Что делает:** Запрещает `NULL` в колонке.

```sql
CREATE TABLE orders (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email       VARCHAR(255) NOT NULL,
    total       INTEGER      NOT NULL,
    currency    CHAR(3)      NOT NULL,
    status      VARCHAR(20)  NOT NULL,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT now()
);
```

**Когда:** Всегда. Колонка, которая **должна** иметь значение — `NOT NULL`. Это не бизнес-логика, это **схема**: «у заказа есть email, total, status. Точка».

**В коде:**

```php
// Домен гарантирует, что $email не null перед persist.
// БД гарантирует, что даже если код «сломался» — NULL не попадёт.
```

**Trade-off:** `NULL` — это «значение отсутствует». Если у вас есть поле, которое **иногда** не имеет смысла (например, `shipping_address` для цифрового товара) — `NULL` **нормально**. Не делайте всё `NOT NULL` «для порядка».

---

### 2. `UNIQUE`

**Что делает:** Запрещает дубликаты.

```sql
CREATE TABLE users (
    id    BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    -- …
);

CREATE UNIQUE INDEX idx_users_email ON users (lower(email));
```

**Когда:**

- `email` — один пользователь, один email.
- `order_number` — уникальный номер заказа.
- `slug` — уникальный URL.
- Составной unique: `(user_id, role)` — одна роль на пользователя.

**В коде:**

```php
// Домен: "email должен быть уникальным" — бизнес-правило.
// БД:    UNIQUE constraint — схема.
//
// Без UNIQUE: два процесса одновременно проверят "email не занят" → оба пройдут → два юзера с одним email.
// С UNIQUE:  второй INSERT → ERROR 23505 → откат → 409 Conflict.
```

**Race condition — главный случай:**

```php
// ПЛОХО: только код
public function register(string $email): User {
    if ($this->repo->findByEmail($email)) {   // ← SELECT
        throw new \Exception('Email already exists');
    }
    $user = new User($email);
    $this->em->persist($user);
    $this->em->flush();                        // ← INSERT
    // ↑ Между SELECT и INSERT другой процесс мог вставить тот же email
}
```

```php
// ХОРОШО: код + БД
public function register(string $email): User {
    $user = new User($email);
    $this->em->persist($user);

    try {
        $this->em->flush();   // ← INSERT → UNIQUE violation → Exception
    } catch (UniqueConstraintViolationException $e) {
        throw new EmailAlreadyTakenException($email) from $e;
    }

    return $user;
}
```

БД **гарантирует** уникальность атомарно. Код просто **обрабатывает** исключение.

---

### 3. `CHECK`

**Что делает:** Условие на значение в строке.

```sql
CREATE TABLE orders (
    total     INTEGER NOT NULL CHECK (total > 0),
    currency  CHAR(3) NOT NULL CHECK (currency IN ('RUB', 'USD', 'EUR')),
    status    VARCHAR(20) NOT NULL CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled')),
    qty       INTEGER NOT NULL DEFAULT 1 CHECK (qty >= 1),
    discount  NUMERIC(5,2) CHECK (discount >= 0 AND discount <= 100)
);
```

**Когда:**

- Перечисление значений, которые **не могут измениться** (статусы, валюты).
- Диапазон: `total > 0`, `qty >= 1`.
- Простые условия: `discount <= 100`.

**Когда НЕ нужно:**

```sql
-- СЛОЖНОЕ бизнес-правило в CHECK:
CHECK (
    status = 'pending' OR
    (status = 'confirmed' AND confirmed_at IS NOT NULL) OR
    (status = 'shipped' AND shipped_at IS NOT NULL AND confirmed_at IS NOT NULL)
)
```

Это **доменная логика**, а не схема. Она меняется. Она зависит от контекста. Она в коде.

**Trade-off:**

| Плюс | Минус |
|---|---|
| Запрещает «мусор» в БД даже при raw SQL | CHECK не может обращаться к другим таблицам |
| Фаст-fail на уровне БД | Изменение CHECK = миграция + `ALTER TABLE` (блокирует таблицу на больших данных) |
| Автодокументация схемы | Сложные условия = трудночитаемый SQL в `\d` |

---

### 4. `FOREIGN KEY`

**Что делает:** Ссылка на другую таблицу + каскад.

```sql
CREATE TABLE order_lines (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    order_id    BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id  BIGINT NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    qty         INTEGER NOT NULL CHECK (qty >= 1),
    unit_price  INTEGER NOT NULL CHECK (unit_price > 0)
);
```

**Когда:** Всегда, когда есть логическая связь. `order_lines.order_id` → `orders.id`. `order_lines.product_id` → `products.id`.

**`ON DELETE` — ключевой выбор:**

| Стратегия | Когда | Пример |
|---|---|---|
| `CASCADE` | Дочерние записи **живут** только в контексте родителя | `order_lines` живут только пока жив `order` |
| `RESTRICT` | Родитель **не может** быть удалён, пока есть дети | `products` нельзя удалить, пока есть `order_lines` |
| `SET NULL` | Связь **опциональна**, но колонка nullable | `order_lines.product_id` → `NULL` (редко) |
| `SET DEFAULT` | Редко. Значение по умолчанию есть | `status` → `'pending'` |

**В коде:**

```php
// Домен: Order.removeLine() — проверка инварианта.
// БД:    FK + CASCADE — если кто-то удалит Order минуя Doctrine → линии удалятся.
//        Если кто-то попробует удалить Product, пока он в заказe → RESTRICT → ERROR.
```

---

### 5. `EXCLUDE` (только PostgreSQL)

**Что делает:** Запрещает пересечение значений по условию.

```sql
-- Запрет пересечения временных слотов в календаре
CREATE TABLE appointments (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    doctor_id  BIGINT NOT NULL REFERENCES doctors(id),
    starts_at  TIMESTAMPTZ NOT NULL,
    ends_at    TIMESTAMPTZ NOT NULL,
    CHECK (ends_at > starts_at),

    -- Никакой пересечений для одного врача
    EXCLUDE USING gist (
        doctor_id    WITH =,
        tsrange(starts_at, ends_at) WITH &&
    )
);
```

**Когда:**

- Календарь / бронирование: нельзя записать двух пациентов на одно время.
- Ивенты: два ивента не могут пересекаться по времени в одном помещении.
- IP-адреса: два устройства не могут иметь пересекающиеся подсети.

**В коде:**

```php
// Без EXCLUDE:
// Процесс A: SELECT → слот свободен → INSERT
// Процесс B: SELECT → слот свободен → INSERT
// → два слота, одно время. Сверхбронирование.

// С EXCLUDE:
// INSERT → ERROR 3C000 (exclusion constraint violation) → откат.
```

**Trade-off:**

| Плюс | Минус |
|---|---|
| Атомарная защита от пересечений | Требует `btree_gist` extension |
| Не нужен код для проверки | Не работает с `IN` (только `=`, `&&`, `<@`, `@>`) |
| Работает при raw SQL / другом сервисе | Сложно читать в `\d` |

---

### 6. `GENERATED ALWAYS AS` (computed columns)

**Что делает:** Значение вычисляется БД при INSERT/UPDATE.

```sql
CREATE TABLE orders (
    total     INTEGER NOT NULL,
    discount  NUMERIC(5,2) DEFAULT 0,
    -- Итог вычисляется БД:
    final_amount NUMERIC(10,2) GENERATED ALWAYS AS
        (total - total * discount / 100) STORED,

    CHECK (final_amount >= 0)
);
```

**Когда:**

- Значение **детерминировано** из других колонок.
- Нужно, чтобы БД **гарантировала** консистентность (например, для репликации, материализованных представлений).
- В коде это дублирование логики, которое может «отстать».

**Когда НЕ нужно:**

- Вычисление зависит от **внешнего контекста** (курс валют, правила скидок).
- Вычисление **дорогое** (UDF,复杂的 SQL).
- Это **бизнес-логика**, а не схема.

**Trade-off:**

| Плюс | Минус |
|---|---|
| Консистентность гарантирована БД | Изменение формулы = миграция |
| Не нужно считать в PHP | Не все СУБД поддерживают (MySQL — нет, PostgreSQL — да) |
| | `STORED` = место в БД. `VIRTUAL` = пересчёт при чтении. |

---

### 7. `TRIGGERS`

**Что делает:** SQL-код, который выполняется при INSERT/UPDATE/DELETE.

```sql
CREATE FUNCTION update_orders_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_orders_updated_at
BEFORE UPDATE ON orders
FOR EACH ROW
EXECUTE FUNCTION update_orders_updated_at();
```

**Когда:**

- `updated_at` — стандартный паттерн. В коде это `setUpdatedAt()` в каждом aggregate. В БД — триггер делает это **всегда**.
- Аудит: `audit_log` — кто, когда, что изменил.
- Синхронизация: `full_name = first_name || ' ' || last_name`.

**Когда НЕ нужно:**

- Сложная бизнес-логика. Триггеры — **не место** для доменной логики.
- Если логика зависит от **нескольких таблиц** и **внешнего контекста**.
- Если триггер делает **сетевой запрос** (невозможно, триггер работает внутри БД).

**Trade-off:**

| Плюс | Минус |
|---|---|
| Работает при любом источнике записи (raw SQL, другой сервис) | Трудно отлаживать (нет stack trace) |
| Не нужно дублировать в каждом PHP-сервисе | Не типизирован. Ошибки — только при runtime |
| | Не видно в коде приложения (скрытая логика) |
| | Миграция: `DROP TRIGGER` + `CREATE TRIGGER` = блокировка таблицы |

---

### 8. `ADVISORY LOCKS`

**Что делает:** Межпроцессная блокировка через БД.

```sql
-- В транзакции:
SELECT pg_advisory_xact_lock(hashtext('order_' || :order_id));
-- … блокирующая операция …
-- COMMIT → авто-разблокировка
```

**Когда:**

- Race condition между процессами, который **нельзя** выразить через `UNIQUE` или `EXCLUDE`.
- Пример: «только один процесс может пересчитать бонусы для юзера одновременно».
- Пример: idempotency key — «эта операция уже выполнена».

```php
// В application service:
$this->em->wrapInTransaction(function () {
    // Advisory lock: блокирует на время транзакции
    $this->em->executeStatement(
        'SELECT pg_advisory_xact_lock(hashtext(:key))',
        ['key' => 'bonus_' . $userId]
    );

    $balance = $this->bonusRepo->getBalance($userId);
    if ($balance < $cost) {
        throw new InsufficientBonusException();
    }

    $this->bonusRepo->debit($userId, $cost);
});
// COMMIT → разблокировка
```

**Trade-off:**

| Плюс | Минус |
|---|---|
| Атомарная блокировка между процессами | Требует PostgreSQL (не MySQL) |
| Авто-разблокировка при COMMIT/ROLLBACK | Если транзакция зависла → блокировка висит |
| Не нужно Redis / внешний локер | `pg_advisory_lock` (без `xact`) — нужно явно `UNLOCK` |

---

### 9. Transaction Isolation Levels

**Что делает:** Контроль видимости неаккомитченных данных.

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

| Уровень | Аномалии | Когда |
|---|---|---|
| `READ COMMITTED` (default) | Dirty read: нет. Non-repeatable read: да. Phantom: да. | 95% случаев |
| `REPEATABLE READ` | Dirty read: нет. Non-repeatable read: нет. Phantom: да (в PG — нет, благодаря MVCC) | Долгие read-only транзакции |
| `SERIALIZABLE` | Все аномалии: нет. | Финансовые операции, инвентарь. **Дорого**: deadlock retries. |

**В коде:**

```php
// Для критических операций:
$this->em->getConnection()->executeStatement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
$this->em->wrapInTransaction(function () {
    // …
});
```

**Trade-off:**

| Плюс | Минус |
|---|---|
| Полный serializable → «как будто» последовательность | SERIALIZABLE = deadlock + retry (PostgreSQL бросает `40001`) |
| | Non-serializable = phantom reads (в PG — не из-за MVCC, но в MySQL InnoDB — да) |

---

## Матрица: что защищает что

| Угроза | Код | DB Constraint |
|---|---|---|
| Bug в PHP-коде | ✅ (если покрыт тестами) | ✅ |
| Race condition (2 процесса) | ❌ (без lock/atomic) | ✅ (UNIQUE, EXCLUDE, advisory lock) |
| Raw SQL из `psql` | ❌ | ✅ (все constraints) |
| Другой сервис (Node, Go) | ❌ | ✅ (все constraints) |
| Cron-скрипт с bug | ⚠️ | ✅ |
| Миграция / DataFixtures | ⚠️ | ✅ |
| Бэкап/restore с мусором | ❌ | ✅ |
| Изменение бизнес-правила | ✅ (изменить код) | ❌ (изменить CHECK = миграция) |
| Изменение схемы данных | ❌ | ✅ (ALTER TABLE) |

**Вывод:** DB constraints защищают от **всего, что не проходит через ваш код**. Код защищает от **ошибок разработки**. Это **дополняющие** слои, а не дублирующие.

---

## Практические правила

### Что всегда в БД

```sql
-- 1. NOT NULL — для обязательных полей
-- 2. UNIQUE — для уникальных идентификаторов (email, slug, order_number)
-- 3. CHECK — для перечислений (status, currency) и диапазонов (qty >= 1, total > 0)
-- 4. FK — для всех логических связей
-- 5. GENERATED — для детерминированных вычислений (final_amount)
-- 6. updated_at — через триггер (стандартный паттерн)
```

### Что НЕ в БД

```sql
-- ❌ Сложная бизнес-логика в CHECK:
CHECK (
    status = 'confirmed' AND
    confirmed_at IS NOT NULL AND
    confirmed_by IS NOT NULL AND
    (confirmed_at - created_at) > interval '0 minutes'
)

-- Это доменная логика. Она в коде. В БД:
CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled'))
-- Остальное — в домене.
```

### Что в БД, что в коде

| Вопрос | Где |
|---|---|
| `total > 0`? | **БД** (CHECK) + **код** (домен) |
| `total = sum(lines)`? | **Код** (инвариант aggregate). БД: `GENERATED` (опционально) |
| `email` уникален? | **БД** (UNIQUE) + **код** (409 на violation) |
| `status` из известного набора? | **БД** (CHECK) + **код** (enum) |
| `order_lines.order_id` существует? | **БД** (FK). **Код** не проверяет (доверие схеме) |
| `qty >= 1`? | **БД** (CHECK) + **код** (домен) |
| «Нельзя подтвердить заказ без линий»? | **Код** (инвариант). **БД** — нет (нельзя выразить без триггера) |
| «Нельзя два слота на одно время»? | **БД** (EXCLUDE) + **код** (409) |

---

## Антипаттерны

| Что делают | Почему плохо | Правильно |
|---|---|---|
| Сложная бизнес-логика в `CHECK` / триггере | Изменение правила = миграция. Трудно тестировать. | В домене. БД: только схема. |
| `CHECK (status = 'pending' OR status = 'confirmed' OR …)` для 10 статусов | Негибко. Новый статус = миграция. | `CHECK (status IN (…))` — для стабильного набора. Или без CHECK — если статусы часто меняются. |
| Триггер, который делает `UPDATE` на другой таблице | Скрытая логика. Трудно отлаживать. | В application service. Триггер — только `updated_at`. |
| `UNIQUE` на `lower(email)` + `CHECK (email ~ '^[^@]+@[^@]+$')` | EMAIL-валидация — это бизнес-логика. | `UNIQUE` — да. Валидация формата — в коде (Laravel/ Symfony validator). |
| `SERIALIZABLE` isolation для всех запросов | Deadlock retries. Пропускная способность падает. | `READ COMMITTED` (default). `SERIALIZABLE` — только для критических операций. |
| Триггер, который шлёт HTTP-запрос | Невозможно. Триггер работает внутри БД. | `dispatch` в коде. Или `LISTEN/NOTIFY` (PostgreSQL) → отдельный воркер. |

---

## Итого

DB constraints — это **контракт схемы данных**: «какие значения допустимы в этой таблице». Это не бизнес-логика. Это **структура**.

- `NOT NULL` — «это поле обязательно».
- `UNIQUE` — «это значение уникально».
- `CHECK` — «это значение в таком-то диапазоне / из такого-то набора».
- `FK` — «это ссылка на другую таблицу».
- `EXCLUDE` — «эти строки не могут пересекаться».
- `GENERATED` — «это значение вычисляется из других».
- `TRIGGER` — «при изменении — обнови `updated_at`».
- `ADVISORY LOCK` — «один процесс за раз».

Бизнес-логика — это **поведение**: «нельзя подтвердить заказ без линий», «скидка не может превышать 50%», «бонусы начисляются только при заказе > 1000 ₽». Это в коде.

Два слоя, две ответственности. DB constraint не «заменяет» код. Код не «заменяет» DB constraint. Они защищают от **разных угроз**.

> ⏱ 48.8s · prompt 55763 tok · output 5911 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

The user is asking about PostgreSQL slow query diagnosis and optimization process. They want to know my methodology, tools, what I look at, and typical causes. This is a practical, experienced-developer question. Let me structure this as a real diagnostic workflow, not a textbook list. I should show the progression from "query is slow" to "query is fixed" with concrete commands, outputs, and reasoning.

Let me think about what a senior PHP developer actually does when a query is slow:

1. First: confirm it's actually slow (not the app, not the network, not PHP)
2. Get the actual SQL
3. EXPLAIN ANALYZE
4. Read the plan
5. Identify the bottleneck
6. Typical causes: missing index, bad index, sequential scan on large table, JOIN explosion, subquery vs JOIN, locking, data skew, statistics
7. Fix: index, rewrite, partition, config
8. Verify

I should be concrete with SQL examples, show actual EXPLAIN output, and explain what each part means. I should show the progression of thinking, not just a list of tips.

Let me structure this as a real diagnostic session, with a concrete example that I trace through. I'll use a realistic scenario: an e-commerce dashboard query that's slow.

I'll structure:
1. Isolate the problem (is it the query?)
2. Get the query
3. EXPLAIN ANALYZE and read the plan
4. Typical bottlenecks and how to identify them
5. Fixes
6. Prevention

Let me write this in Russian since the conversation is in Russian, with code blocks in English/SQL.

</details>

**Кандидат:**

## 0. Убедиться, что проблема именно в запросе

Перед тем как трогать SQL — исключить всё остальное.

```bash
# Сколько реально тратит запрос?
# В Symfony: profiler → вкладка "Query" → время каждого запроса.
# В логах: SQL-логгер (см. предыдущий ответ про N+1).

# В самом PostgreSQL:
SELECT query_id, calls, mean_exec_time, total_exec_time,
       left(query, 100) AS query
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 10;
```

Если `mean_exec_time` для вашего запроса — 200 мс, а страница тормозит на 3 с — проблема **не в этом запросе**. Возможно: N+1 (200 мс × 15 запросов = 3 с), сериализация, PHP overhead, сеть до БД.

**Правило:** оптимизируем **один конкретный запрос**, который занимает **>50% времени страницы**. Не «все запросы сразу».

---

## 1. Получить реальный SQL

```sql
-- Что реально улетает в БД?
SELECT queryid, query, mean_exec_time, calls
FROM pg_stat_statements
WHERE query ILIKE '%orders%'
ORDER BY mean_exec_time DESC;
```

Или из Symfony Profiler / SQL-логгера. Важно: **реальный SQL с реальными параметрами**, а не «шаблон из кода».

Типовой случай, который будем разбирать:

```sql
SELECT o.id, o.created_at, o.total, o.status,
       u.name, u.email,
       ol.product_id, ol.qty, ol.unit_price
FROM orders o
JOIN users u ON u.id = o.customer_id
JOIN order_lines ol ON ol.order_id = o.id
WHERE o.status = 'pending'
  AND o.created_at >= '2025-01-01'
ORDER BY o.created_at DESC
LIMIT 50;
```

Дашборд «ожидающие заказы». Таблицы: `orders` — 2M строк, `order_lines` — 10M, `users` — 500k.

---

## 2. EXPLAIN ANALYZE

Это **главный инструмент**. Не `EXPLAIN` (план без реальных данных), а `ANALYZE` (реальное выполнение + тайминги).

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING, SETTINGS)
SELECT o.id, o.created_at, o.total, o.status,
       u.name, u.email,
       ol.product_id, ol.qty, ol.unit_price
FROM orders o
JOIN users u ON u.id = o.customer_id
JOIN order_lines ol ON ol.order_id = o.id
WHERE o.status = 'pending'
  AND o.created_at >= '2025-01-01'
ORDER BY o.created_at DESC
LIMIT 50;
```

### Флаги

| Флаг | Что даёт |
|---|---|
| `ANALYZE` | Реальное выполнение, реальные тайминги. **Обязательно.** |
| `BUFFERS` | Сколько страниц БД прочитано из shared_buffers vs диск. Ключевой для понимания «почему медленно». |
| `TIMING` | Тайминг на каждом узле. |
| `SETTINGS` | Значения GUC-переменных, влияющих на план. |
| `COSTS` | (по умолчанию) Оценка стоимости. Сравнить с реальным. |
| `WAL` | (PG 13+) Запись WAL. |

### Чтение плана

```
 Limit  (cost=1234567.89..1234570.12 rows=50 width=120) (actual time=4521.334..4521.412 rows=50 loops=1)
   Buffers: shared hit=1234567 read=8901
   ->  Sort  (cost=1234567.89..1240000.00 rows=137000 width=120) (actual time=4520.111..4520.890 rows=123456 loops=1)
         Sort Key: o.created_at DESC
         Sort Method: top-N heapsort  Memory: 45kB
         Buffers: shared hit=1234567 read=8901
         ->  Hash Join  (cost=2345.67..1189000.00 rows=137000 width=120) (actual time=0.890..3210.555 rows=123456 loops=1)
               Hash Cond: (ol.order_id = o.id)
               Buffers: shared hit=1230000 read=8500
               ->  Seq Scan on order_lines ol  (cost=0.00..789000.00 rows=2340000 width=40) (actual time=0.012..1890.333 rows=2340000 loops=1)
                     Filter: (order_id IN (SELECT id FROM orders WHERE status = 'pending' AND created_at >= '2025-01-01'))
                     Buffers: shared hit=890000 read=7200
               ->  Hash  (cost=2000.00..2000.00 rows=137000 width=80) (actual time=1.234..1.235 rows=123456 loops=1)
                     Buffers: shared hit=3400
                     ->  Hash Join  (cost=567.89..1890.00 rows=137000 width=80) (actual time=0.234..0.890 rows=123456 loops=1)
                           Hash Cond: (u.id = o.customer_id)
                           ->  Seq Scan on users u  (cost=0.00..780.00 rows=500000 width=48) (actual time=0.008..23.444 rows=500000 loops=1)
                                 Buffers: shared hit=3200
                           ->  Hash  (cost=400.00..400.00 rows=137000 width=32) (actual time=0.543..0.543 rows=123456 loops=1)
                                 Buffers: shared hit=180
                                 ->  Index Scan using idx_orders_status_created on orders o  (cost=0.42..400.00 rows=137000 width=32) (actual time=0.045..0.312 rows=123456 loops=1)
                                       Index Cond: (status = 'pending' AND created_at >= '2025-01-01')
                                       Buffers: shared hit=180
 Planning Time: 0.456 ms
 Execution Time: 4523.890 ms
```

### Что я вижу

| Узел | Время | Проблема? |
|---|---|---|
| `Limit` | 4521 мс | Это итог. Не проблема сама по себе. |
| `Sort (top-N)` | 4520 мс | Сортирует **123 456 строк** через top-N heapsort. Дорого. |
| `Hash Join (ol)` | 3210 мс из 4520 | **71% времени.** |
| `Seq Scan on order_lines` | 1890 мс | **Последовательное чтение 2.3M строк.** Нет индекса по `order_id` + фильтру. |
| `Hash Join (u)` | 0.89 мс | Быстро. `users` — 500k, но в hash-table — ок. |
| `Seq Scan on users` | 23 мс | Полное чтение `users` для hash-таблицы. Приемлемо (500k), но не идеально. |
| `Index Scan on orders` | 0.31 мс | **Отлично.** Индекс `idx_orders_status_created` работает. |

**Бутылочное горлышко:** `Seq Scan on order_lines` (1890 мс) + `Hash Join` (3210 мс). Всего 4.5 с.

**Ключевой инсайт:** `orders` фильтруется эффективно (0.3 мс через индекс). Но затем JOIN к `order_lines` делает **полное сканирование 2.3M строк**, потому что:
- `order_lines.order_id` не имеет индекса, **или**
- индекс есть, но planner выбирает Seq Scan, потому что `Hash Join` на 123k строк дешевле, чем 123k Index Scan'ов.

---

## 3. Типовые причины и как их распознать в плане

### 3.1. Sequential Scan (полное чтение таблицы)

```
Seq Scan on order_lines  (actual time=0.012..1890.333 rows=2340000 loops=1)
```

**Что значит:** PostgreSQL читает **каждую** строку таблицы. Это **нормально** для маленьких таблиц (< 10k строк) или когда выборка > 30% таблицы. **Аномалия** — когда таблица 10M строк, а читается вся.

**Причины:**
- Нет индекса по колонке `WHERE` / `JOIN`.
- Индекс есть, но planner считает, что выборка слишком большая (> threshold), и Seq Scan дешевле.
- Индекс **не селективный**: `WHERE status = 'pending'` при 40% строк с этим статусом — индекс бесполезен.

**Диагностика:**

```sql
-- Есть ли индекс?
SELECT indexname, indexdef
FROM pg_indexes
WHERE tablename = 'order_lines';

-- Насколько селективен фильтр?
SELECT count(*) FROM order_lines;  -- 10M
SELECT count(*) FROM order_lines WHERE order_id IN (SELECT id FROM orders WHERE status = 'pending');  -- 123k
-- 123k / 10M = 1.2% — индекс ДАЛБИЛЬШЕ полезен.

-- Проверить:
CREATE INDEX CONCURRENTLY idx_lines_order_id ON order_lines (order_id);

-- EXPLAIN ANALYZE снова:
-- Seq Scan → Index Scan using idx_lines_order_id (actual time=0.012..189.333)
```

**Правило:** если `rows` в Seq Scan > 100k и таблица > 1M — нужен индекс или переписывание запроса.

---

### 3.2. Nested Loop Explosion

```
Nested Loop  (actual time=0.5..45000.000 rows=5000000 loops=1)
  ->  Seq Scan on orders  (rows=50000)
  ->  Index Scan on order_lines  (rows=100)  ← для каждой из 50k
```

**Что значит:** для каждой из 50k строк `orders` — отдельный Index Scan в `order_lines`. 50k × 100 = 5M строк.

**Причина:** `Nesting` — маленькая таблица × большая таблица. Planner выбирает Nested Loop, потому что «внутренняя» выборка маленькая. Но **внешняя** — 50k.

**Решение:** переписать как `Hash Join` или `Merge Join`:

```sql
-- Убедиться, что JOIN-условие индексировано на обеих сторонах.
-- Если таблица маленькая — Hash Join будет выбран автоматически.
-- Если нет — SET enable_nestloop = off;  (для диагностики, НЕ в проде!)
```

---

### 3.3. Sort на большом объёме

```
Sort  (cost=... rows=123456) (actual time=4520.111..4520.890)
  Sort Method: top-N heapsort  Memory: 45kB
```

`top-N heapsort` — это **хорошо** (только 50 элементов в памяти). Если бы было:

```
Sort Method: external merge  Disk: 123456kB
```

— это **плохо**: не хватило `work_mem`, PostgreSQL сортирует на диск.

**Решение:**

```sql
-- Увеличить work_mem (на сессию или глобально):
SET work_mem = '256MB';   -- по умолчанию 4MB

-- Или: индекс по ORDER BY, чтобы избежать Sort:
CREATE INDEX idx_orders_created ON orders (created_at DESC) WHERE status = 'pending';
-- → Index Scan уже отсортирован → Sort не нужен
```

**Важно:** `work_mem` — **на операцию**, не на запрос. Если 5 сортировок в запросе — 5 × 256MB. Для продакшена — 16–64MB на операцию, не 1GB.

---

### 3.4. Инертные параметры (Stale Statistics)

```
Index Scan using idx_lines_order_id  (rows=10000)  ← planner думает 10k
  (actual rows=123000)  ← на самом деле 123k
```

**Что значит:** статистика (`pg_stats`) устарела. Planner «думает», что выборка 10k, и выбирает Index Scan. На самом деле 123k — Hash Join был бы быстрее.

**Диагностика:**

```sql
-- Когда обновлялась статистика?
SELECT relname, last_analyze, last_autoanalyze
FROM pg_stat_user_tables
WHERE relname = 'order_lines';

-- Насколько актуальна?
ANALYZE order_lines;
EXPLAIN ANALYZE ...;  -- повторить
```

**Решение:**

```sql
-- Автостатистика (включена по умолчанию):
ALTER SYSTEM SET autovacuum = on;
ALTER SYSTEM SET autovacuum_analyze_scale_factor = 0.02;  -- 2% изменения = analyze

-- Для таблиц с частыми изменениями:
ALTER TABLE order_lines SET (autovacuum_analyze_scale_factor = 0.01);

-- Для критических таблиц: ANALYZE в cron (но осторожно — блокирует чтение?)
```

---

### 3.5. Блокировки и конcurrentIndex

```sql
-- Кто блокирует?
SELECT blocked.pid AS blocked_pid,
       blocking.pid AS blocking_pid,
       blocked.query AS blocked_query,
       blocking.query AS blocking_query,
       now() - blocked.query_start AS blocked_duration
FROM pg_stat_activity blocked
JOIN pg_stat_activity blocking
  ON blocking.pid = ANY(pg_blocking_pids(blocked.pid));
```

**Типовой случай:** долгий `UPDATE` на `orders` блокирует `SELECT` на той же строке. Не проблема SQL-оптимизации — это **конкуренция**.

**Решение:**
- Уменьшить длительность транзакции (не держать `BEGIN` на 10 секунд).
- `NOWAIT` / `SET LOCK_TIMEOUT` — fail fast вместо ожидания.
- Разделить горячие и холодные таблицы.

---

### 3.6. Subquery вместо JOIN

```sql
-- МЕДЛЕННО:
SELECT * FROM orders
WHERE customer_id IN (
    SELECT id FROM users WHERE role = 'admin'
)
AND created_at >= '2025-01-01';
```

```
Subquery Scan  (actual time=2.3..4500.000 rows=123456)
  ->  Nested Loop  (actual time=0.5..4200.000)
        ->  Seq Scan on orders  (rows=500000)
        ->  Subquery  (rows=1)
              ->  Index Scan on users  (rows=1)  ← для каждой из 500k
```

**Решение:**

```sql
-- БЫСТРЕЕ:
SELECT o.*
FROM orders o
JOIN users u ON u.id = o.customer_id
WHERE u.role = 'admin'
  AND o.created_at >= '2025-01-01';
```

```
Hash Join  (actual time=1.2..890.000 rows=123456)
  ->  Seq Scan on orders  (rows=500000)
  ->  Hash  (rows=5)
        ->  Index Scan on users WHERE role = 'admin'  (rows=5)
```

**Правило:** `IN (subquery)` с большой внешней таблицей → переписать в `JOIN` / `EXISTS`.

---

### 3.7. Data Skew

```sql
-- Индекс есть, но 80% строк имеют status = 'active'
SELECT status, count(*) FROM orders GROUP BY status;
-- active:    1,600,000
-- pending:     120,000
-- cancelled:    80,000
```

`WHERE status = 'active'` — индекс **бесполезен** (80% выборки). `WHERE status = 'cancelled'` — индекс **полезен** (4% выборки).

**Диагностика:**

```sql
-- Распределение:
SELECT status, count(*),
       count(*) * 100.0 / (SELECT count(*) FROM orders) AS pct
FROM orders GROUP BY status;

-- Selectivity:
SELECT status, n_distinct, most_common_vals, most_common_freqs
FROM pg_stats
WHERE tablename = 'orders' AND attname = 'status';
```

**Решение:**
- Partial index: `CREATE INDEX idx_orders_pending ON orders (created_at) WHERE status = 'pending';`
- Partitioning по `status` (редко оправдано).
- Accept: Seq Scan на 80% выборки — **нормально**.

---

## 4. Процесс оптимизации: пошаговый

```
┌─────────────────────────────────────────────────────────────────────┐
│ 1. ПОДТВЕРДИТЬ: запрос реально медленный                            │
│    pg_stat_statements / profiler / SQL-лог                          │
│    mean_exec_time > 100ms для 1 запроса → да, оптимизируем          │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 2. EXPLAIN (ANALYZE, BUFFERS)                                       │
│    Найти узел с наибольшим (actual time)                            │
│    Найти Seq Scan на большой таблице                                │
│    Найти Sort с external merge                                      │
│    Найти Nested Loop с большим числом внешних строк                 │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 3. ДИАГНОСТИКА ПРИЧИНЫ                                              │
│                                                                     │
│    Seq Scan?      → Есть ли индекс? Селективность? Статистика?     │
│    Sort?          → work_mem? Индекс по ORDER BY?                  │
│    Nested Loop?   → Переписать в Hash/Merge? Индексы?             │
│    Subquery?      → Переписать в JOIN/EXISTS?                      │
│    Блокировки?    → pg_stat_activity, pg_locks                     │
│    Stale stats?   → ANALYZE, pg_stats                               │
│    Data skew?     → SELECT count(*) GROUP BY                       │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 4. РЕШЕНИЕ (по приоритету)                                          │
│                                                                     │
│    a) Индекс (быстрейший эффект, минимум риска)                     │
│       CREATE INDEX CONCURRENTLY …                                   │
│                                                                     │
│    b) Переписать запрос (JOIN вместо subquery, CTE, LIMIT)         │
│                                                                     │
│    c) work_mem / parallel_workers (если не хватает памяти)          │
│                                                                     │
│    d) Partitioning (если таблица > 50M строк)                       │
│                                                                     │
│    e) Материализованное представление (если запрос повторяется)     │
│                                                                     │
│    f) Денормализация (если JOIN-цепочка > 3 таблиц)                │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│ 5. ПРОВЕРКА                                                         │
│                                                                     │
│    EXPLAIN ANALYZE — время упало?                                   │
│    pg_stat_statements — mean_exec_time упало?                       │
│    Прогон в проде (read-replica) — нет регрессии?                  │
│    CREATE INDEX CONCURRENTLY — нет блокировки на запись?           │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 5. Конкретные инструменты

### 5.1. `pg_stat_statements` (главный)

```sql
-- Топ-20 по среднему времени:
SELECT queryid,
       calls,
       round(mean_exec_time::numeric, 2) AS avg_ms,
       round(total_exec_time::numeric, 2) AS total_ms,
       round(rows::numeric) AS avg_rows,
       left(regexp_replace(query, '\s+', ' ', 'g'), 120) AS query
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 20;

-- Топ-20 по суммарному времени (частые × медленные):
ORDER BY total_exec_time DESC
```

### 5.2. `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)`

JSON-формат — удобно парсить скриптом:

```bash
psql -c "EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON) SELECT …" | jq '.[0].Plan'
```

### 5.3. `pg_stat_user_tables`

```sql
SELECT relname,
       seq_scan, seq_tup_read,
       idx_scan, idx_tup_read,
       n_live_tup,
       last_seq_scan, last_idx_scan,
       last_analyze, last_autoanalyze
FROM pg_stat_user_tables
WHERE relname IN ('orders', 'order_lines', 'users')
ORDER BY seq_tup_read DESC;
```

**Что смотреть:**
- `seq_scan` растёт → последовательные сканы. Если `seq_tup_read / n_live_tup > 0.1` — читается > 10% таблицы.
- `idx_scan` = 0 → индексы не используются.
- `last_analyze` = давно → статистика устарела.

### 5.4. `pg_locks` + `pg_stat_activity`

```sql
-- Текущие блокировки:
SELECT l.pid, l.mode, l.granted,
       a.query,
       now() - a.query_start AS duration
FROM pg_locks l
JOIN pg_stat_activity a ON a.pid = l.pid
WHERE l.relation IS NOT NULL
ORDER BY duration DESC;

-- Кто кого блокирует:
SELECT blocked.query AS waiting,
       blocking.query AS blocking,
       blocked.pid AS waiting_pid
FROM pg_stat_activity blocked
WHERE NOT blocked.wait_event_type = NULL  -- упрощённо
```

### 5.5. `pg_indexes` + `pg_index`

```sql
-- Какие индексы есть:
SELECT tablename, indexname, indexdef
FROM pg_indexes
WHERE tablename = 'order_lines';

-- Используется ли индекс? (pg_stat_user_indexes)
SELECT relname, indexrelname, idx_scan
FROM pg_stat_user_indexes
WHERE relname = 'order_lines'
ORDER BY idx_scan;

-- idx_scan = 0 → индекс не используется. Удалить или переписать запрос.
```

### 5.6. `pg_class` + `pg_attribute`

```sql
-- Размер таблицы:
SELECT relname,
       pg_size_pretty(pg_total_relation_size(oid)) AS total_size,
       reltuples::bigint AS estimated_rows
FROM pg_class
WHERE relname IN ('orders', 'order_lines', 'users');

-- Размер индекса:
SELECT i.relname AS index_name,
       pg_size_pretty(pg_relation_size(i.oid)) AS index_size
FROM pg_class t
JOIN pg_index ix ON t.oid = ix.indrelid
JOIN pg_class i ON i.oid = ix.indexrelid
WHERE t.relname = 'order_lines';
```

### 5.7. `pg_settings`

```sql
-- Ключевые параметры:
SELECT name, setting, unit
FROM pg_settings
WHERE name IN (
    'work_mem', 'shared_buffers', 'effective_cache_size',
    'parallel_workers', 'max_parallel_workers_per_gather',
    'random_page_cost', 'seq_page_cost',
    'autovacuum', 'autovacuum_analyze_scale_factor'
);
```

| Параметр | Что делает | Типовая проблема |
|---|---|---|
| `work_mem` | Память на сортировку/хэш | 4MB → external merge sort |
| `shared_buffers` | Кэш страниц БД | 256MB на 32GB RAM → мало |
| `effective_cache_size` | Planner: «сколько ОЗУ считает доступным» | 512MB → planner не верит в кэш → Seq Scan |
| `random_page_cost` | Стоимость случайного чтения | 4.0 (disk) → planner боится индексов. На SSD: 1.1–1.5 |
| `parallel_workers` | Сколько процессов для параллельного скана | 0 → нет параллелизма |

### 5.8. `pg_stat_wal` (PG 12+)

```sql
SELECT wal_records, wal_bytes,
       full_page_writes, full_page_write_bytes
FROM pg_stat_wal;
```

Если `full_page_writes` — очень большой → много UPDATE'ов на одних и тех же строках → WAL раздут.

---

## 6. Решения: от быстрого к медленному

### 6.1. Индекс

```sql
-- Быстрый, минимум риска:
CREATE INDEX CONCURRENTLY idx_lines_order_id ON order_lines (order_id);
-- CONCURRENTLY: не блокирует writes. Медленнее (2x), но без downtime.

-- Partial index (для селективных фильтров):
CREATE INDEX CONCURRENTLY idx_orders_pending_created
    ON orders (created_at DESC)
    WHERE status = 'pending';
-- Меньше, быстрее, целевой.

-- Composite (для WHERE + ORDER BY):
CREATE INDEX CONCURRENTLY idx_orders_status_created
    ON orders (status, created_at DESC);
-- Покрытие: WHERE status = … AND created_at >= … ORDER BY created_at DESC

-- Covering (INCLUDE):
CREATE INDEX CONCURRENTLY idx_orders_covering
    ON orders (status, created_at DESC)
    INCLUDE (total, customer_id);
-- Index Only Scan: не читает heap. Быстрее.
```

**Trade-off:**
| Плюс | Минус |
|---|---|
| Частый запрос → 10–100× быстрее | Каждый индекс = overhead на INSERT/UPDATE/DELETE |
| | Место в БД (100k строк × 4 индекса = 4× место) |
| | `CONCURRENTLY` = 2× время создания |
| | Индекс на 80% выборки бесполезен |

**Правило:** 3–5 индексов на горячую таблицу — нормально. 15 — пересмотреть.

### 6.2. Переписать запрос

```sql
-- ДО: subquery
SELECT * FROM orders
WHERE customer_id IN (SELECT id FROM users WHERE role = 'admin')
AND created_at >= '2025-01-01';

-- ПОСЛЕ: JOIN
SELECT o.*
FROM orders o
JOIN users u ON u.id = o.customer_id AND u.role = 'admin'
WHERE o.created_at >= '2025-01-01';
```

```sql
-- ДО: SELECT * (все 30 колонок)
SELECT * FROM orders WHERE …;

-- ПОСЛЕ: только нужные
SELECT id, total, status, created_at FROM orders WHERE …;
-- Меньше данных → меньше памяти → быстрее
```

```sql
-- ДО: сортировка всего + LIMIT
SELECT * FROM orders WHERE status = 'pending' ORDER BY created_at DESC LIMIT 50;

-- ПОСЛЕ: индекс по (status, created_at DESC)
-- → Index Scan уже отсортирован → Sort не нужен → LIMIT = остановка на 50-й
```

### 6.3. `work_mem`

```sql
-- Глобально (postgresql.conf):
work_mem = 64MB

-- На сессию (для конкретного запроса):
SET work_mem = '256MB';
EXPLAIN ANALYZE SELECT …;
```

**Trade-off:** `work_mem` × (сортировки + хэши) × параллельные запросы = RAM. 64MB × 10 операций × 20 соединений = 12.8GB. Не бесконечно.

### 6.4. Parallel query

```sql
-- По умолчанию включён (PG 9.6+):
max_parallel_workers_per_gather = 4

-- Для больших таблиц:
SET max_parallel_workers_per_gather = 8;
EXPLAIN ANALYZE SELECT count(*) FROM order_lines WHERE …;
-- Parallel Seq Scan on order_lines  (workers=4)
```

**Trade-off:**
| Плюс | Минус |
|---|---|
| N ядер = N× быстрее на больших сканах | Overhead на спавт процессов |
| | Не работает на маленьких таблицах (< 100k строк) |
| | Занимает CPU-ядра (если 10 параллельных запросов × 4 workers = 40 потоков) |

### 6.5. Partitioning

```sql
-- По времени (range):
CREATE TABLE orders (
    id BIGINT,
    created_at TIMESTAMPTZ,
    status VARCHAR(20),
    -- …
) PARTITION BY RANGE (created_at);

CREATE TABLE orders_2025_q1 PARTITION OF orders
    FOR VALUES FROM ('2025-01-01') TO ('2025-04-01');
CREATE TABLE orders_2025_q2 PARTITION OF orders
    FOR VALUES FROM ('2025-04-01') TO ('2025-07-01');
-- …
```

**Когда:**
- Таблица > 50M строк.
- Запросы фильтруют по `created_at` / `date`.
- Нужно «списывать» старые данные: `DROP TABLE orders_2023_q1` (мгновенно, не `DELETE`).

**Когда НЕ нужно:**
- Таблица < 10M строк.
- Запросы не фильтруют по partition key.
- Комбинированные ключи (partition by hash) — редко оправдано.

**Trade-off:**
| Плюс | Минус |
|---|---|
| `DROP PARTITION` = мгновенное удаление 10M строк | Индексы дублируются на каждую partition |
| Vacuum'ится одна partition, не вся таблица | Сложнее схема (FK, unique constraints) |
| | `CREATE INDEX CONCURRENTLY` на partitioned table = N × время |

### 6.6. Материализованное представление

```sql
CREATE MATERIALIZED VIEW pending_orders_dashboard AS
SELECT o.id, o.created_at, o.total, o.status,
       u.name, u.email
FROM orders o
JOIN users u ON u.id = o.customer_id
WHERE o.status = 'pending';

-- Обновление:
REFRESH MATERIALIZED VIEW CONCURRENTLY pending_orders_dashboard;
-- CONCURRENTLY: требует unique index на MV. Не блокирует чтение.
```

**Когда:**
- Запрос повторяется > 100 раз/мин.
- Данные меняются редко (раз в час/день).
- Запрос сложный (5+ JOIN, subquery).

**Когда НЕ нужно:**
- Данные меняются каждую секунду (MV устаревает).
- Запрос простой (1 JOIN + WHERE) — индекс быстрее.

**Trade-off:**
| Плюс | Минус |
|---|---|
| Чтение = простой `SELECT` (1 мс) | `REFRESH` = пересчёт всего (может быть долгим) |
| | Место в БД (дублирование данных) |
| | Данные **устаревшие** (между REFRESH) |

### 6.7. Денормализация

```sql
-- Вместо JOIN users:
ALTER TABLE orders ADD COLUMN customer_name VARCHAR(255);
ALTER TABLE orders ADD COLUMN customer_email VARCHAR(255);

-- Заполнить:
UPDATE orders o
SET customer_name = u.name,
    customer_email = u.email
FROM users u
WHERE u.id = o.customer_id;

-- Индекс:
CREATE INDEX idx_orders_customer_name ON orders (customer_name);
```

**Когда:**
- JOIN-цепочка > 3 таблиц.
- Дочерние данные **редко** меняются.
- Запрос читает > 10k строк.

**Когда НЕ нужно:**
- Дочерние данные меняются часто (email, name) → денормализованное поле устаревает.
- Нужно **консистентное** чтение → JOIN.

**Trade-off:**
| Плюс | Минус |
|---|---|
| Быстрее чтение (нет JOIN) | Дублирование данных |
| | Нужно обновлять при изменении (триггер / код) |
| | Раздувание таблицы |

---

## 7. Чего НЕ делать

| Антипаттерн | Почему |
|---|---|
| `SELECT *` в проде | Читаются все 30 колонок. Меньше данных = быстрее. |
| `SET enable_seqscan = off` | Принудительный Seq Scan. Planner знает лучше. |
| `SET work_mem = '2GB'` | 20 соединений × 2GB = 40GB. OOM. |
| Индекс на каждой колонке | 15 индексов × overhead на write = медленные INSERT. |
| `REFRESH MATERIALIZED VIEW` каждые 10 сек | Пересчёт 10M строк = 50 с. MV устаревает быстрее, чем обновляется. |
| `DELETE FROM orders WHERE created_at < '2023-01-01'` на 10M строк | Долгая транзакция, WAL раздут, vacuum не может. `DROP PARTITION` или batch delete. |
| Оптимизация по `EXPLAIN` без `ANALYZE` | Оценка ≠ реальность. Без `ANALYZE` — «вот что я **думаю** делать». |
| Добавление индекса «на всякий случай» | Каждый индекс = overhead на write + место. Только для **реальных** медленных запросов. |

---

## 8. Профилактика

### В коде

```php
// 1. Всегда LIMIT на списках
->setMaxResults(50)

// 2. Select только нужные поля (DQL)
'SELECT o.id, o.total, o.status FROM OrderEntity o WHERE …'
// Не 'SELECT o FROM OrderEntity o' (all fields + lazy)

// 3. Еager-load только если 100% нужен
// 'JOIN o.lines l' — только если lines нужны в этом запросе

// 4. Н+1: batch fetch
// Doctrine: $em->getReference(), DQL JOIN, или custom query
```

### В БД

```sql
-- 1. Индексы на горячих WHERE/JOIN/ORDER BY
-- 2. pg_stat_statements — еженедельный обзор
-- 3. autovacuum включён и настроен
-- 4. Статистика актуальна (last_analyze < 1 день)
```

### В мониторинге

```yaml
# Prometheus + pg_exporter
- job_name: postgresql
  metrics_path: /metrics
  static_configs:
    - targets: ['db:9187']
```

Алерты:
- `pg_stat_statements.mean_exec_time > 500ms` (по query)
- `pg_stat_activity.count > 50` (too many connections)
- `pg_replication_lag > 5s` (replikation)
- `pg_database_size > 100GB` (growth)

---

## Итого: чек-лист на каждый медленный запрос

```
1. EXPLAIN (ANALYZE, BUFFERS) — где время?
2. Seq Scan на > 100k строк? → Индекс / переписать
3. Sort с external merge? → work_mem / индекс по ORDER BY
4. Nested Loop с > 10k внешних? → Hash/Merge / переписать
5. Subquery? → JOIN / EXISTS
6. Статистика устарела? → ANALYZE
7. Блокировки? → pg_locks, уменьшить транзакцию
8. Data skew? → Partial index / partitioning
9. SELECT *? → Только нужные поля
10. Провели в проде? → pg_stat_statements до/после
```

Каждое решение — **конкретная гипотеза, проверенная EXPLAIN ANALYZE**. Не «поставил индекс — стало быстрее». А «было Seq Scan 1890 мс → стало Index Scan 189 мс → подтверждено».

> ⏱ 75.2s · prompt 61356 tok · output 9265 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегаты в DDD: что это, как определять границы, пример с объяснением и типовая ошибка. Это продолжение разговора о DDD, чистом PHP, Doctrine.

Мне нужно:
1. Чётко определить агрегат (не путать с "большим объектом" или "таблицей")
2. Объяснить, как определять границы — не по "нашему опыту", а по принципам
3. Привести конкретный пример с кодом
4. Показать типовую ошибку — не абстрактную, а такую, которую реально видят в коде

Давайте подумаю, что реально важно про агрегаты:

Суть агрегата:
- Это **граница транзакционной целостности**
- Внутренние инварианты агрегата должны быть атомарно гарантированы
- Внешний мир общается с агрегатом ТОЛЬКО через корень
- Агрегат НЕ содержит другие агрегаты — только ссылки по ID

Как определять границы:
1. Какие инварианты должны быть гарантированы атомарно?
2. Какие сущности всегда меняются ВМЕСТЕ?
3. Какие сущности можно менять НЕЗАВИСИМО?
4. Сколько данных в транзакции? (больше 200 строк — перебор)
5. Кто владелец? (один сервис, один контекст)

Типовая ошибка:
Самая частая — "мега-агрегат". Берут всю бизнес-сущность (Order + User + Product + Inventory + Payment + Shipping) и делают ОДИН агрегат. Результат:
- Транзакция держит 10+ таблиц
- Конкурентные конфликты (два заказа за один товар)
- Невозможно масштабировать (одна БД, одна транзакция)
- Невозможно тестировать (mock'и 10 зависимостей)

Вторая частая ошибка — "агрегат-таблица". Делают агрегат 1:1 с таблицей. Если в БД 5 таблиц — 5 агрегатов. Но инварианты не совпадают с таблицами.

Третья — "агрегат-объект" без границ. Все сущности в одном классе, все методы в одном месте.

Пример: Order.

Хороший агрегат Order:
- Order (корень)
- OrderLine[] (дети)
- Money total (VO)
- OrderStatus (VO/enum)

ВНЕ агрегата:
- Product (отдельный агрегат — у товара своя жизнь, свои инварианты)
- User (отдельный агрегат)
- Payment (отдельный агрегат или отдельная сущность)
- Inventory (отдельный контекст)
- Shipping (отдельный контекст)

Ссылки:
- OrderLine → Product: по ID (не объект Product)
- Order → User: по ID (не объект User)
- Order → Payment: по ID

Типовая ошибка: Order содержит Product (объект), User (объект), Payment (объект), Inventory (объект). И метод `confirm()` делает: `product.decreaseStock()`, `payment.charge()`, `user.addPoints()`, `inventory.reserve()`. Всё в одной транзакции.

Результат:
- Если `payment.charge()` упало — откатываются и `product.decreaseStock()`, и `user.addPoints()`. Но мы хотели, чтобы товар был зарезервирован, а платёж — перепроверен.
- Конкурентные конфликты: два заказа за один товар. Один держит транзакцию 5 секунд (ожидание платёжного шлюза). Второй блокируется.
- Невозможно заменить платёжный шлюз без изменения агрегата Order.

Давайте структурирую ответ:
1. Определение (кратко, чётко)
2. Критерии границ (не "на наш вкус", а по вопросам)
3. Пример с кодом (хороший и плохой)
4. Типовая ошибка с объяснением, почему это ломается
5. Практические правила

Хочу быть конкретным, с кодом. Не абстрактным "вот так делают". А показать, как именно это выглядит в PHP + Doctrine.

</details>

**Кандидат:**

## Что такое агрегат

Агрегат — это **граница транзакционной целостности**. Это не «большой объект», не «таблица в БД», не «модуль». Это ответ на один вопрос:

> **Какие данные должны меняться атомарно, чтобы система оставалась в согласованном состоянии?**

Внутри агрегата инварианты гарантируются **кодом** и **одной транзакцией**. Снаружи агрегата — только **ссылки по ID**, а не объекты.

---

## Три свойства, которые определяют агрегат

### 1. Инварианты

Инвариант — условие, которое **всегда** должно быть истинным, иначе система в некорректном состоянии.

```php
// Инвариант: total = сумма всех линий
// Инвариант: статус может меняться только по правилам
// Инвариант: у confirmed заказа нельзя менять линии
// Инвариант: qty >= 1
```

Агрегат — **минимальный набор сущностей**, который гарантирует эти инварианты **одной транзакцией**.

### 2. Владелец

Один сервис, один контекст, один модуль — **один владелец** агрегата. Если два сервиса одновременно мутируют одни и те же сущности — это **один** агрегат, а не два. Если никто не может мутировать — это **VO** или **read-model**.

### 3. Граница транзакции

Всё, что внутри агрегата, сохраняется в **одной** транзакции. Всё, что снаружи, — в **другой**. Между агрегатами — **события** или **команды**, а не прямой вызов.

```
Агрегат A:  [Order + OrderLine[]]  →  1 транзакция
Агрегат B:  [Product]              →  1 транзакция
Агрегат C:  [User]                 →  1 транзакция

A → B:  "Order подтверждён" (событие) → B сам решает, что делать
A → C:  "Order создан" (событие)      → C сам решает, что делать
```

---

## Как определять границы: пять вопросов

Задайте их для каждой сущности:

| Вопрос | Если ДА → внутри агрегата | Если НЕТ → снаружи |
|---|---|---|
| Меняется ли она **всегда вместе** с корнем? | ✅ | — |
| Её изменение **ломает инвариант** корня, если не атомарно? | ✅ | — |
| Её можно менять **независимо** от корня? | — | ✅ |
| У неё есть **собственная жизнь** (создаётся/живёт/умирает независимо)? | — | ✅ |
| Её изменение требует **отдельной транзакции** (например, внешний API)? | — | ✅ |

### Пример применения

| Сущность | Вопрос 1 | Вопрос 2 | Вопрос 3 | Вопрос 4 | Вопрос 5 | Итог |
|---|---|---|---|---|---|---|
| `OrderLine` | ✅ Всегда вместе с Order | ✅ Бьёт инвариант `total` | — | — | — | **Внутри** |
| `Money` (total) | ✅ | ✅ | — | — | — | **Внутри (VO)** |
| `Product` | — | — | ✅ Меняется независимо | ✅ У товара своя жизнь | — | **Снаружи** |
| `User` | — | — | ✅ | ✅ | — | **Снаружи** |
| `Payment` | — | — | ✅ | ✅ | ✅ Внешний API | **Снаружи** |
| `Inventory` | — | — | ✅ | ✅ | ✅ | **Снаружи** |
| `OrderStatus` | ✅ | ✅ | — | — | — | **Внутри (enum)** |

---

## Пример: Order

### Правильные границы

```
┌─────────────────────────────────────────────────┐
│  АГРЕГАТ: Order                                 │
│                                                 │
│  Order (корень)                                  │
│  ├── id, email, status, createdAt               │
│  ├── Money total          ← VO, неизменяемый    │
│  └── OrderLine[] lines    ← дочерние сущности   │
│       ├── id, productId (ID!), qty, unitPrice   │
│       └── FK → Order.id                         │
│                                                 │
│  Инварианты:                                     │
│  ├── total = Σ(line.qty × line.unitPrice)       │
│  ├── status: pending → confirmed → shipped → …  │
│  ├── confirmed: lines заморожены                │
│  └── qty >= 1                                   │
│                                                 │
│  Транзакция: INSERT/UPDATE orders + order_lines │
│  Владелец: OrderService                         │
└─────────────────────────────────────────────────┘

СНАРУЖИ (ссылки по ID):
  Product  ← у товара своя цена, свой stock, своя жизнь
  User     ← у юзера свои заказы, свои бонусы
  Payment  ← внешний платёжный шлюз, своя транзакция
  Inventory ← свой контекст, свой stock
```

### Код агрегата

```php
// src/Domain/Order.php
final class Order
{
    private ?int $id = null;
    private string $email;
    private OrderStatus $status;
    private Money $total;
    private \DateTimeImmutable $createdAt;
    /** @var Collection<int, OrderLine> */
    private Collection $lines;

    public function __construct(string $email)
    {
        $this->email     = $email;
        $this->status    = OrderStatus::Pending;
        $this->total     = Money::rub(0);
        $this->createdAt = new \DateTimeImmutable();
        $this->lines     = new ArrayCollection();
    }

    // ─── Инварианты гарантируются здесь ───

    public function addLine(ProductId $productId, int $qty, Money $unitPrice): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Cannot modify confirmed order');
        }
        if ($qty < 1) {
            throw new \InvalidArgumentException('qty must be >= 1');
        }

        $this->lines->add(new OrderLine($productId, $qty, $unitPrice));
        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Only pending → confirmed');
        }
        if ($this->lines->isEmpty()) {
            throw new StateException('Cannot confirm empty order');
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function cancel(string $reason): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new StateException('Cannot cancel delivered order');
        }
        $this->status = OrderStatus::Cancelled;
    }

    // ─── Инвариант: total всегда = сумма линий ───

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->getTotal()->amount;
        }
        $this->total = Money::rub($sum);
    }

    // ─── Getters ───

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getTotal(): Money { return $this->total; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }

    public function setId(int $id): void { $this->id = $id; }
}
```

```php
// src/Domain/OrderLine.php
final class OrderLine
{
    private ?int $id = null;
    private ProductId $productId;   // ← ID, НЕ объект Product!
    private int $qty;
    private Money $unitPrice;      // ← цена зафиксирована на момент покупки

    public function __construct(ProductId $productId, int $qty, Money $unitPrice)
    {
        $this->productId = $productId;
        $this->qty       = $qty;
        $this->unitPrice = $unitPrice;
    }

    public function getTotal(): Money
    {
        return Money::rub($this->unitPrice->amount * $this->qty);
    }

    public function getId(): ?int { return $this->id; }
    public function getProductId(): ProductId { return $this->productId; }
    public function getQty(): int { return $this->qty; }
    public function getUnitPrice(): Money { return $this->unitPrice; }

    public function setId(int $id): void { $this->id = $id; }
}
```

### Ключевой момент: ссылка по ID

```php
// OrderLine хранит ProductId (int|string), НЕ объект Product.
// Order хранит email (string), НЕ объект User.
// Order НЕ хранит Payment, Inventory, Shipping.

// Для получения Product — отдельный запрос:
$product = $this->productRepo->findById($line->getProductId());
```

**Почему:** Product — отдельный агрегат. У него своя цена, свой stock, своя политика скидок. Если Order «содержит» Product — изменение цены товара ломает инвариант `total` в заказе. А у товара может быть 10 000 заказов. Невозможно атомарно обновить все.

---

## Типовая ошибка: мега-агрегат

### Что делают

Берут всю бизнес-логику «заказа» и делают **один** агрегат, который содержит всё:

```php
// src/Domain/Order.php  (ПЛОХО)
final class Order
{
    private ?int $id = null;
    private User $user;               // ← ОБЪЕКТ, не ID
    private Payment $payment;         // ← ОБЪЕКТ, не ID
    private Inventory $inventory;     // ← ОБЪЕКТ
    private Shipping $shipping;       // ← ОБЪЕКТ
    private array $lines = [];

    public function confirm(): void
    {
        // 1. Проверяем stock
        foreach ($this->lines as $line) {
            $product = $line->product;   // ← объект Product внутри
            if ($product->stock < $line->qty) {
                throw new \Exception('Not enough stock');
            }
        }

        // 2. Списываем stock
        foreach ($this->lines as $line) {
            $line->product->decreaseStock($line->qty);   // ← мутация другого агрегата
        }

        // 3. Заряжаем платёж
        $this->payment->charge($this->total);            // ← внешний API в транзакции!

        // 4. Начисляем бонусы
        $this->user->addBonusPoints($this->total->amount / 100);  // ← мутация другого агрегата

        // 5. Создаём отправление
        $this->shipping->createShipment($this);

        // 6. Меняем статус
        $this->status = 'confirmed';

        // ВСЁ В ОДНОЙ ТРАНЗАКЦИИ
    }
}
```

### Почему это ломается

**1. Транзакция держит 10+ таблиц 5–30 секунд**

```
BEGIN;
  UPDATE products SET stock = stock - 2 WHERE id = 1;
  UPDATE products SET stock = stock - 1 WHERE id = 2;
  UPDATE users SET bonus_points = bonus_points + 5 WHERE id = 42;
  INSERT INTO payments (…);           ← внешний API: 3–10 с ожидания
  INSERT INTO shipments (…);
  UPDATE orders SET status = 'confirmed' WHERE id = 1;
COMMIT;
```

`charge()` — HTTP-запрос к платёжному шлюзу. 3–10 секунд. За это время транзакция **держит блокировки** на `products`, `users`, `payments`. Любой другой запрос к этим таблицам — **блокируется**.

**2. Конкурентные конфликты**

```
Заказ A: UPDATE products SET stock = stock - 5 WHERE id = 1;  (stock = 7 → 2)
Заказ B: SELECT stock FROM products WHERE id = 1;  → 2
Заказ B: UPDATE products SET stock = stock - 3 WHERE id = 1;  (2 → -1)
→ Сверхпродажа. 3 единицы проданы, которых нет.
```

Оба заказа в **одной** транзакции с `Product`. Блокировки не помогают, если `charge()` занимает 10 секунд — второй заказ ждёт 10 секунд, а потом всё равно может «пробить» stock, если между SELECT и UPDATE другой процесс изменил значение.

**3. Невозможно заменить платёжный шлюз**

```
Stripe → PayPal → Crypto
Order::confirm() содержит $this->payment->charge()
→ Менять платёжный шлюз = менять агрегат Order
→ Менять агрегат = переписать все тесты
→ Менять все тесты = 2 недели работы
```

**4. Невозможно тестировать**

```php
public function test_confirm(): void
{
    $order = new Order(
        user: new User(/* … 15 полей … */),
        payment: new Payment(/* mock Stripe API */),
        inventory: new Inventory(/* mock БД */),
        shipping: new Shipping(/* mock API */),
        lines: [/* … */],
    );

    $this->stripeMock->expects('charge')->andReturn('ok');
    $this->shippingMock->expects('createShipment')->andReturn('ok');
    $this->inventoryMock->expects('decreaseStock')->andReturn(null);

    $order->confirm();

    $this->assertEquals('confirmed', $order->status);
    $this->assertEquals(0, $user->bonusPoints);   // ← не проверяется!
    $this->assertEquals(5, $product->stock);      // ← не проверяется!
}
```

10 mock'ов. Если mock'и не совпадают с реальным поведением — тест зелёный, прод красный.

**5. Невозможно масштабировать**

Один агрегат = одна БД = одна транзакция = один процесс. Если 1000 заказов/сек — 1000 транзакций на одну БД. Если бы агрегаты были разделены:

```
Order:    1 транзакция (INSERT orders + order_lines)
Product:  1 транзакция (UPDATE stock) — МОЖЕТ БЫТЬ В ДРУГОЙ БД
User:     1 транзакция (UPDATE bonus) — МОЖЕТ БЫТЬ В ДРУГОЙ БД
Payment:  внешний API — без БД
```

Каждый — **свой** процесс, **своё** масштабирование, **своя** БД.

---

## Как правильно: событийная координация

```php
// Application Service — оркестрация, НЕ агрегат
final class ConfirmOrderService
{
    public function __construct(
        private OrderRepository $orders,
        private ProductRepository $products,
        private PaymentGateway $payment,
        private InventoryService $inventory,
        private BonusService $bonus,
        private ShippingService $shipping,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $em,
    ) {}

    public function confirm(int $orderId): void
    {
        // ─── Агрегат 1: Order ───
        $order = $this->orders->findById($orderId);

        // Зафиксировать цену (snapshot) — инвариант агрегата
        $this->em->wrapInTransaction(function () use ($order) {
            foreach ($order->getLines() as $line) {
                $product = $this->products->findById($line->getProductId());
                $line->fixPrice($product->getPrice());   // ← snapshot
            }
            $order->confirm();
            $this->orders->save($order);
        });
        // ↑ 1 транзакция, 2 таблицы (orders + order_lines). Чисто.

        // ─── Агрегат 2: Inventory ───
        // Отдельная транзакция. Может быть в другой БД.
        $this->em->wrapInTransaction(function () use ($order) {
            foreach ($order->getLines() as $line) {
                $this->inventory->reserve(
                    $line->getProductId(),
                    $line->getQty()
                );
                // Если не хватает → StateException → откат
            }
        });

        // ─── Внешний: Payment ───
        // Не в транзакции! Это HTTP-запрос.
        $chargeResult = $this->payment->charge(
            orderId: $order->getId(),
            amount: $order->getTotal()
        );
        // Если упало → компенсирующее действие:
        if (!$chargeResult->isSuccess()) {
            $this->compensate($order);   // отменить заказ, вернуть сток
            return;
        }

        // ─── Агрегат 3: Bonus ───
        $this->em->wrapInTransaction(function () use ($order) {
            $this->bonus->credit($order->getEmail(), $order->getTotal());
        });

        // ─── Агрегат 4: Shipping ───
        $this->shipping->createShipment($order->getId());

        // ─── События (async, после всего) ───
        $this->dispatcher->dispatch(new OrderConfirmedEvent($order->getId()));
    }
}
```

**Разница:**

| Мега-агрегат | Разделённые агрегаты |
|---|---|
| 1 транзакция, 10 таблиц, 30 с | 4 транзакции, 1–2 таблицы каждая, 0.5 с |
| Блокировки на всех таблицах | Блокировки только на своих |
| Платёжный шлюз в транзакции | Платёжный шлюз **вне** транзакции |
| 10 mock'ов в тесте | 3–4 mock'а, каждый по 1–2 метода |
| Замена Stripe = переписать Order | Замена Stripe = поменять `PaymentGateway` |
| 1 БД, 1 процесс | 4 БД, 4 процесса, 4 масштаба |

---

## Практические правила

### 1. Агрегат ≠ таблица

Одна таблица ≠ один агрегат. Агрегат — это **группа сущностей с общими инвариантами**.

```
Таблица orders     → агрегат Order
Таблица order_lines → часть агрегата Order (не отдельный агрегат!)
Таблица products   → агрегат Product (отдельный!)
Таблица users      → агрегат User (отдельный!)
Таблица payments   → НЕ агрегат (внешний сервис) или агрегат Payment (свой)
```

### 2. Агрегат не содержит другие агрегаты

```php
// ПЛОХО:
class Order {
    private Product $product;    // ← объект другого агрегата
    private User $user;          // ← объект другого агрегата
}

// ХОРОШО:
class Order {
    private ProductId $productId;  // ← ID
    private string $email;         // ← значение, не объект
}
```

### 3. Агрегат не делает сетевых вызовов

```php
// ПЛОХО:
class Order {
    public function confirm(): void {
        $this->payment->charge();   // ← HTTP в агрегате
        $this->shipping->create();  // ← HTTP в агрегате
    }
}

// ХОРОШО:
class Order {
    public function confirm(): void {
        // Только локальные инварианты. Никаких внешних вызовов.
        $this->status = OrderStatus::Confirmed;
    }
}
```

### 4. Агрегат не знает о других агрегатах

```php
// ПЛОХО:
class Order {
    public function confirm(): void {
        $this->product->decreaseStock();   // ← знает про Product
        $this->user->addBonus();           // ← знает про User
    }
}

// ХОРОШО:
class Order {
    public function confirm(): void {
        $this->status = OrderStatus::Confirmed;
        // Кто что делает с Product/User — решает ConfirmOrderService
    }
}
```

### 5. Если транзакция > 1 секунду — агрегат слишком большой

1 секунда — это **верхняя граница**. Нормальная транзакция агрегата — **5–50 мс**. Если 5 секунд — значит, в транзакции есть внешний вызов или слишком много таблиц.

### 6. Если mock'ов > 5 в тесте — агрегат слишком большой

5 mock'ов = 5 зависимостей = агрегат «знает» про 5 других систем. Это не агрегат, это **оркестратор**. Оркестратор — в application service, не в домене.

---

## Итого

| Вопрос | Ответ |
|---|---|
| Что такое агрегат? | Граница транзакционной целостности. Минимальный набор сущностей, гарантирующий инварианты атомарно. |
| Как определять границы? | Пять вопросов: вместе? инвариант? независимо? своя жизнь? внешняя транзакция? |
| Что внутри? | Корень + дочерние сущности + VO'и. Только то, что меняется вместе. |
| Что снаружи? | Всё, что имеет свою жизнь, свою транзакцию, свой контекст. Ссылки по ID. |
| Типовая ошибка? | Мега-агрегат: всё в одном классе, одна транзакция, 10 mock'ов, 30 секунд на запрос. |
| Решение? | Разделить на агрегаты по границам транзакций. Координировать через application service + события. |

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Сценарий

Два процесса одновременно изменяют один и тот же агрегат `Order`:

```
Процесс A:  Order#1.confirm()         → status: pending → confirmed
Процесс B:  Order#1.cancel("fraud")   → status: pending → cancelled
```

Оба читают `status = 'pending'`, оба проходят проверку инварианта «pending → можно подтвердить/отменить», оба пишут.

---

## Что произойдёт без защиты

### Lost update (потерянное обновление)

```
t1:  A: SELECT status FROM orders WHERE id = 1  → 'pending'
t2:  B: SELECT status FROM orders WHERE id = 1  → 'pending'
t3:  A: UPDATE orders SET status = 'confirmed' WHERE id = 1  ✓
t4:  B: UPDATE orders SET status = 'cancelled' WHERE id = 1  ✓
```

Итог: `status = 'cancelled'`. Подтверждение **потеряно**. Платёж был списан (если `confirm()` включает `charge()`), а заказ отменён. Деньги списаны, заказ «отменён» — клиент ничего не получил.

### Нарушение инварианта

```
t1:  A: read total = 5000, lines = [L1, L2]
t2:  B: removeLine(L2)  →  total = 3000
t3:  A: addLine(L3)     →  total = 5000 + 2000 = 7000  ← неверно!
                                        (должно быть 3000 + 2000 = 5000)
t4:  A: write total = 7000
t5:  B: write total = 3000
```

Итог зависит от порядка записей. Инвариант `total = Σ(lines)` **сломан**.

### Двойное списание

```
t1:  A: stock = 10, A списывает 5
t2:  B: stock = 10, B списывает 5   ← читал ДО записи A
t3:  A: stock = 5
t4:  B: stock = 5                  ← 10 списано, а было 10. Ок.
t5:  C: stock = 5, C списывает 5   ← читал ДО записей A и B
t6:  C: stock = 0                  ← 15 списано из 10. Сверхпродажа.
```

---

## Механизмы защиты

### 1. Optimistic Locking (основной подход для DDD)

**Идея:** не блокируем. Проверяем **при записи**, что никто не изменил данные с момента нашего чтения.

#### Как устроено

Добавляем колонку `version` (integer, начинается с 0):

```sql
ALTER TABLE orders ADD COLUMN version INTEGER NOT NULL DEFAULT 0;
```

При каждом UPDATE — условие:

```sql
UPDATE orders
SET status = 'confirmed', version = version + 1
WHERE id = 1 AND version = 3;   -- ← та версия, которую я прочитал

-- Если 0 строк обновлено → кто-то изменил раньше → конфликт
```

#### В Doctrine

```php
// src/Entity/OrderEntity.php
#[ORM\Entity]
class OrderEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $status;

    #[ORM\Version]   // ← МАГИЯ DOCTRINE
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;

    // …
}
```

`#[ORM\Version]` — Doctrine **автоматически**:
- при `INSERT` ставит `version = 1`;
- при `UPDATE` добавляет `AND version = <текущее>` в WHERE;
- при `UPDATE` инкрементит `version = version + 1`;
- если `rowCount = 0` — бросает `OptimisticLockException`.

#### В application service

```php
final class ConfirmOrderService
{
    public function __construct(
        private OrderRepository $orders,
        private PaymentGateway $payment,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $em,
    ) {}

    public function confirm(int $orderId): void
    {
        // 1. Чтение (без блокировки)
        $order = $this->orders->findById($orderId);

        // 2. Доменная логика (инварианты)
        $order->confirm();

        // 3. Сохранение (optimistic check при UPDATE)
        try {
            $this->em->wrapInTransaction(function () use ($order) {
                $this->orders->save($order);
            });
        } catch (OptimisticLockException $e) {
            // Кто-то изменил заказ между нашим SELECT и UPDATE.
            // Перечитаем и попробуем снова (или откажем).
            throw new ConcurrencyConflictException(
                "Order #$orderId was modified by another process",
                orderId: $orderId,
            ) from $e;
        }

        // 4. Побочные эффекты (ТОЛЬКО после успешного commit)
        $this->dispatcher->dispatch(new OrderConfirmedEvent($order->getId()));
    }
}
```

#### Что происходит при конфликте

```
t1:  A: SELECT … WHERE id = 1  → version = 3
t2:  B: SELECT … WHERE id = 1  → version = 3
t3:  B: UPDATE … WHERE id = 1 AND version = 3  → 1 row, version = 4  ✓
t4:  A: UPDATE … WHERE id = 1 AND version = 3  → 0 rows  ← КОНФЛИКТ
t5:  A: OptimisticLockException → ConcurrencyConflictException → 409
```

**Процесс B выиграл. Процесс A получил явную ошибку.** Никакого lost update. Никакого «молчаливо переписал».

#### Retry с backoff (для idempotent операций)

```php
public function confirm(int $orderId): void
{
    $maxRetries = 3;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        $order = $this->orders->findById($orderId);
        $order->confirm();

        try {
            $this->em->wrapInTransaction(function () use ($order) {
                $this->orders->save($order);
            });
            break;   // ✓ успех
        } catch (OptimisticLockException $e) {
            if ($attempt === $maxRetries) {
                throw new ConcurrencyConflictException(
                    "Order #$orderId: too many concurrent modifications",
                    orderId: $orderId,
                ) from $e;
            }

            // Экспоненциальный backoff: 100ms, 200ms, 400ms
            usleep((int) (100_000 * 2 ** ($attempt - 1)));
            // Повтор: перечитаем данные с актуальной version
        }
    }

    $this->dispatcher->dispatch(new OrderConfirmedEvent($orderId));
}
```

**Важно:** retry **безопасен** только если операция **идемпотентна** или **перечитывает актуальные данные** перед повтором. `confirm()` перечитывает — значит, при повторе увидит `status = 'confirmed'` и бросит `StateException`. Это **правильно**: не нужно подтверждать уже подтверждённый заказ.

---

### 2. Pessimistic Locking (SELECT FOR UPDATE)

**Идея:** блокируем строку **на время транзакции**. Второй процесс ждёт.

```sql
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;   -- ← блокировка
-- … логика …
UPDATE orders SET status = 'confirmed' WHERE id = 1;
COMMIT;   -- ← разблокировка
```

#### В Doctrine

```php
public function confirm(int $orderId): void
{
    $this->em->wrapInTransaction(function () use ($orderId) {
        // LOCK_PESSIMISTIC_WRITE = SELECT … FOR UPDATE
        $order = $this->em->find(OrderEntity::class, $orderId, [], [], [], [],
            LockMode::PESSIMISTIC_WRITE);

        // Второй процесс, сделавший find() с LockMode::PESSIMISTIC_WRITE,
        // ЗАМОРОЗИТСЯ здесь, пока первый не сделает COMMIT.

        $this->mapper->apply($this->domainOrder->confirm(), $order);
        $this->em->flush();
    });
    // COMMIT → разблокировка
}
```

#### Альтернатива: DQL с LOCK

```php
$order = $this->em->createQuery(
    'SELECT o FROM OrderEntity o WHERE o.id = :id'
)
->setLockMode(LockMode::PESSIMISTIC_WRITE)
->setParameter('id', $orderId)
->getOneOrNullResult();
```

#### Что происходит при конфликте

```
t1:  A: SELECT … FOR UPDATE WHERE id = 1  → БЛОКИРОВКА
t2:  B: SELECT … FOR UPDATE WHERE id = 1  → ЖДЁТ (до COMMIT A или lock_timeout)
t3:  A: UPDATE … → COMMIT → разблокировка
t4:  B: SELECT … → получает актуальные данные (version = 4, status = 'confirmed')
t5:  B: $order->confirm() → StateException: "already confirmed"
```

**Процесс B не «потерял» обновление — он просто опоздал и получил явную ошибку.**

#### Trade-offs против Optimistic

| | Optimistic (version) | Pessimistic (FOR UPDATE) |
|---|---|---|
| **Блокировка** | Нет. Конфликт обнаружен при записи. | Да. Второй процесс ждёт. |
| **Пропускная способность** | Высокая (если конфликты редки). | Ниже (блокировки = serialization). |
| **Конфликт** | `OptimisticLockException` → retry/409. | Процесс **ждёт** → видит актуальное состояние. |
| **Удобство** | Нужно обрабатывать исключение. | «Просто работает» (процесс B видит актуальные данные). |
| **Длительные транзакции** | Не блокируют другие. | **Блокируют** все, кто ждёт эту строку. |
| **Deadlock** | Не возможен (нет блокировок). | **Возможен** (A ждёт B, B ждёт A). |
| **Когда** | Конфликты **редки** (< 1%). | Конфликты **часты** или операция **долгая**. |

---

### 3. Advisory Lock (PostgreSQL)

**Идея:** явная блокировка по **ключу**, не привязанная к строке.

```sql
BEGIN;
SELECT pg_advisory_xact_lock(hashtext('order_' || 1));
-- … логика …
COMMIT;   -- авто-разблокировка
```

#### В PHP

```php
public function confirm(int $orderId): void
{
    $this->em->wrapInTransaction(function () use ($orderId) {
        // Блокировка на время транзакции.
        // Второй процесс с тем же orderId → ЖДЁТ.
        $this->em->executeStatement(
            'SELECT pg_advisory_xact_lock(hashtext(:key))',
            ['key' => 'order_' . $orderId]
        );

        $order = $this->orders->findById($orderId);
        $order->confirm();
        $this->orders->save($order);
    });
    // COMMIT → авто-разблокировка
}
```

#### Когда нужен

- Блокировка нужна **не на строке**, а на **бизнес-ключе**.
- Пример: «только один процесс может пересчитать бонусы для юзера `#42`».
- Пример: «только один процесс может обработать вебхук от Stripe с `event_id = X`» (idempotency).

```php
// Idempotency через advisory lock
public function handleWebhook(string $eventId): void
{
    $this->em->wrapInTransaction(function () use ($eventId) {
        $this->em->executeStatement(
            'SELECT pg_advisory_xact_lock(hashtext(:key))',
            ['key' => 'webhook_' . $eventId]
        );

        if ($this->webhookLog->exists($eventId)) {
            return;   // ← уже обработан, skip
        }

        // … обработка …
        $this->webhookLog->markProcessed($eventId);
    });
}
```

#### Trade-offs

| Плюс | Минус |
|---|---|
| Не нужно `version` в таблице | Только PostgreSQL (не MySQL) |
| Авто-разблокировка при COMMIT | Если транзакция зависла → блокировка висит |
| Блокирует по **бизнес-ключу**, не по PK | Не видно в `pg_locks` как «таблица + строка» |
| | Нужно знать `hashtext()` — коллизии теоретически возможны (крайне редко) |

---

## Проектирование: где что проверяем

### Слои ответственности

```
┌──────────────────────────────────────────────────────────────────────┐
│  АГРЕГАТ (домен)                                                     │
│  ────────────────────                                                │
│  • Инварианты: status transition, total = Σ(lines), qty >= 1        │
│  • НЕ знает про конкурентность. НЕ знает про БД.                    │
│  • confirm() бросает StateException если status ≠ pending           │
│  • НЕ делает SELECT FOR UPDATE. НЕ проверяет version.               │
└──────────────────────────────────┬───────────────────────────────────┘
                                   ▼
┌──────────────────────────────────────────────────────────────────────┐
│  REPOSITORY (инфраструктура)                                         │
│  ────────────────────────────────                                    │
│  • persist / find / save                                             │
│  • #[ORM\Version] — автоматический optimistic lock                  │
│  • LockMode::PESSIMISTIC_WRITE — если явно запрошено                │
│  • НЕ принимает бизнес-решений                                       │
└──────────────────────────────────┬───────────────────────────────────┘
                                   ▼
┌──────────────────────────────────────────────────────────────────────┐
│  APPLICATION SERVICE (оркестрация)                                   │
│  ────────────────────────────────────────────                        │
│  • Оборачивает в транзакцию (wrapInTransaction)                     │
│  • Ловит OptimisticLockException → retry или 409                    │
│  • Advisory lock — если нужно                                        │
│  • Вызывает домен → repository → event dispatcher                   │
│  • НЕ содержит бизнес-логику                                         │
└──────────────────────────────────────────────────────────────────────┘
```

### Правило: инвариант проверяется **в домене**, конфликт — **в application layer**

```php
// ДОМЕН: проверяет инвариант (чисто, без БД)
final class Order
{
    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Only pending orders can be confirmed');
        }
        $this->status = OrderStatus::Confirmed;
    }
}

// APPLICATION: обрабатывает конфликт (знает про БД)
final class ConfirmOrderService
{
    public function confirm(int $orderId): void
    {
        try {
            $order = $this->orders->findById($orderId);
            $order->confirm();          // ← инвариант (домен)
            $this->orders->save($order); // ← version check (инфра)
        } catch (OptimisticLockException $e) {
            throw new ConcurrencyConflictException(…) from $e;  // ← конфликт (app)
        } catch (StateException $e) {
            throw $e;   // ← инвариант сломан, не конфликт
        }
    }
}
```

**Два разных исключения, две разные причины, две разные реакции:**

| Исключение | Причина | Реакция |
|---|---|---|
| `StateException` | Инвариант нарушен (status ≠ pending) | 409 / 422. Не retry. |
| `OptimisticLockException` | Кто-то изменил раньше | Retry (если idempotent) или 409. |

---

## Специфические сценарии

### Сценарий 1: Два админа одновременно отменяют заказ

```
A: cancel("fraud")     → pending → cancelled
B: cancel("duplicate") → pending → cancelled
```

**Оптимистичный подход:**

```
t1:  A: SELECT → version=1, status=pending
t2:  B: SELECT → version=1, status=pending
t3:  A: UPDATE … AND version=1 → version=2, status=cancelled  ✓
t4:  B: UPDATE … AND version=1 → 0 rows  ← CONFLICT
t5:  B: OptimisticLockException
```

**Итог:** A выиграл. B получил 409. Оба запроса были «отмена» — итог одинаковый. **Но B получил ошибку**, хотя результат корректен.

**Улучшение:** идемпотентность. Если `status` уже `cancelled` — не ошибка, а «уже сделано».

```php
public function cancel(string $reason): void
{
    if ($this->status === OrderStatus::Cancelled) {
        return;   // ← уже отменён. Идемпотентно.
    }
    if ($this->status === OrderStatus::Delivered) {
        throw new StateException('Cannot cancel delivered order');
    }
    $this->status = OrderStatus::Cancelled;
}
```

Теперь:

```
t3:  A: UPDATE → status=cancelled  ✓
t4:  B: SELECT → status=cancelled (актуально)
t5:  B: cancel() → return (идемпотентно)  ✓
t6:  B: UPDATE → 0 rows (version не совпал) → НЕ ВАЖНО, т.к. данных не меняли
```

**Правило:** для операций, результат которых **не зависит от того, кто именно выполнил** (отмена, подтверждение) — делайте **идемпотентными**. Для операций, где **каждый вызов уникален** (addLine, changeAddress) — не идемпотентны, conflict = ошибка.

---

### Сценарий 2: Сверхпродажа (stock)

```
A: buy(product#1, qty=5)  → stock: 10 → 5
B: buy(product#1, qty=5)  → stock: 10 → 5  ← Сверхпродажа!
```

**Оптимистичный подход на `Product`:**

```php
final class Product
{
    private int $stock;
    private int $version;

    public function decreaseStock(int $qty): void
    {
        if ($this->stock < $qty) {
            throw new InsufficientStockException($this->id, $this->stock, $qty);
        }
        $this->stock -= $qty;
    }
}
```

```
t1:  A: SELECT stock=10, version=1
t2:  B: SELECT stock=10, version=1
t3:  A: stock = 10-5 = 5; UPDATE … AND version=1 → version=2  ✓
t4:  B: stock = 10-5 = 5; UPDATE … AND version=1 → 0 rows  ← CONFLICT
t5:  B: retry → SELECT stock=5, version=2
t6:  B: stock = 5-5 = 0; UPDATE … AND version=2 → version=3  ✓
```

**Итог:** 10 единиц продано, 0 осталось. **Корректно.**

**Но если qty=8:**

```
t5:  B: retry → SELECT stock=5
t6:  B: 5 < 8 → InsufficientStockException  ✓
```

**Идемпотентность не работает** (у каждого заказа свой `orderId`). **Conflict = ошибка = retry = перечитать = увидеть актуальный stock.**

---

### Сценарий 3: Долгая операция + конкурентность

```
confirm() включает:
  1. check stock (БД)
  2. charge payment (HTTP, 5 с)
  3. create shipment (HTTP, 2 с)
  4. update order status (БД)
```

Если держать **pessimistic lock** на весь процесс (5+2 с) — блокируем **все** операции с этим заказом 7 секунд. Если 100 таких заказов параллельно — 100 блокировок по 7 секунд. **Катастрофа.**

**Решение: разбить на транзакции + идемпотентность**

```php
public function confirm(int $orderId): void
{
    // Транзакция 1: резервирование (короткая, < 50мс)
    $this->em->wrapInTransaction(function () use ($orderId) {
        $order = $this->orders->findById($orderId);
        $order->reserveStock();   // ← инвариант + version check
        $this->orders->save($order);
    });
    // COMMIT → блокировка освобождена

    // Внешний: payment (НЕ в транзакции, 5 с)
    $result = $this->payment->charge($orderId);
    if (!$result->ok()) {
        $this->compensate($orderId);   // отменить резервирование
        throw new PaymentFailedException();
    }

    // Транзакция 2: подтверждение (короткая, < 50мс)
    $this->em->wrapInTransaction(function () use ($orderId) {
        $order = $this->orders->findById($orderId);
        $order->confirm();   // ← инвариант + version check
        $this->orders->save($order);
    });
    // COMMIT

    // Внешний: shipment (НЕ в транзакции, 2 с)
    $this->shipping->create($orderId);
}
```

**Компенсация** (SAGA-паттерн):

```php
private function compensate(int $orderId): void
{
    $this->em->wrapInTransaction(function () use ($orderId) {
        $order = $this->orders->findById($orderId);
        $order->cancel('payment_failed');
        $this->orders->save($order);
    });
}
```

**Trade-off:** между транзакцией 1 и 2 заказ в статусе `reserved`. Если процесс упадёт между ними — заказ «застрянет» в `reserved`. Нужен **cron-скрипт**, который находит `reserved` заказы старше N минут и отменяет их.

---

## Типовые ошибки

### 1. `SELECT` без `FOR UPDATE` + `UPDATE` — race

```php
// ПЛОХО:
$order = $this->em->find(OrderEntity::class, $id);
// … 100 мс логика …
$this->em->flush();   // UPDATE без version → lost update
```

**Правильно:** `#[ORM\Version]` или `LockMode::PESSIMISTIC_WRITE`.

### 2. Optimistic lock, но **не** обрабатывается

```php
// ПЛОХО:
try {
    $this->orders->save($order);
} catch (\Exception $e) {
    // "ошибка" — но это OptimisticLockException!
    $this->logger->error('save failed');
}
// → Запрос завершён 200, но данные не сохранены.
```

**Правильно:** ловить `OptimisticLockException` **отдельно** и решать: retry или 409.

### 3. Pessimistic lock + внешний вызов в транзакции

```php
// ПЛОХО:
$this->em->wrapInTransaction(function () {
    $order = $this->em->find(…, LockMode::PESSIMISTIC_WRITE);
    $this->payment->charge(…);   // ← 5 секунд, блокировка ДЕРЖИТСЯ
    $this->orders->save($order);
});
```

5 секунд блокировки на строку `orders#1`. Если 10 параллельных — 50 секунд serialization. **Никогда не делайте внешних вызовов в транзакции с pessimistic lock.**

### 4. Retry без перечитывания

```php
// ПЛОХО:
for ($i = 0; $i < 3; $i++) {
    try {
        $this->orders->save($order);   // тот же $order, та же version
    } catch (OptimisticLockException) {
        continue;   // ← version не изменился → снова conflict
    }
}
```

**Правильно:** после conflict — **перечитать** `$order` из БД. Новая version. Повторная проверка инварианта.

### 5. `version` в домене

```php
// ПЛОХО:
final class Order
{
    private int $version;   // ← домен знает про optimistic locking

    public function confirm(): void
    {
        $this->version++;   // ← домен мутирует version
    }
}
```

**Правильно:** `#[ORM\Version]` — это **инфраструктурный** механизм. Домен не знает, что данные сохраняются в БД с version. Домен знает только инварианты.

### 6. Advisory lock без `xact`

```php
// ОПАСНО:
$this->em->executeStatement('SELECT pg_advisory_lock(…)', […]);
// …
// Если не вызван pg_advisory_unlock → блокировка висит ВЕЧНО.
```

**Правильно:** `pg_advisory_xact_lock` — авто-разблокировка при COMMIT/ROLLBACK.

---

## Итоговая таблица

| Ситуация | Механизм | Почему |
|---|---|---|
| Конфликт **редок** (< 1%), операция **короткая** | **Optimistic** (`#[ORM\Version]`) | Нет блокировок. Высокая пропускная способность. |
| Конфликт **частый** (> 10%), операция **короткая** | **Pessimistic** (`FOR UPDATE`) | Retry-цикл с optimistic = потеря производительности. |
| Конфликт **редок**, операция **долгая** (внешний API) | **Optimistic** + разбить на транзакции | Pessimistic держит блокировку 5 с. Опасно. |
| Блокировка по **бизнес-ключу** (не по PK) | **Advisory lock** | `version` — на строке. Advisory — на ключевом слове. |
| **Идемпотентная** операция (отмена, подтверждение) | **Optimistic** + идемпотентный домен | Conflict не страшен: результат тот же. |
| **Неидемпотентная** операция (addLine, changeAddress) | **Optimistic** + retry с перечитыванием | Conflict = нужно перечитать и перепроверить. |
| **SAGA** (несколько агрегатов, внешние API) | **Optimistic** + компенсация | Одна транзакция на весь процесс = блокировки + deadlock. |

**Главный принцип:** домен **не знает** о конкурентности. Он проверяет **инварианты**. Инфраструктура (Doctrine, `#[ORM\Version]`, `FOR UPDATE`, advisory lock) обеспечивает **безопасность записи**. Application layer **координирует**: транзакции, retry, компенсация, идемпотентность. Три слоя, три ответственности.

> ⏱ 52.5s · prompt 70312 tok · output 6254 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в DDD: когда публиковать, как обрабатывать, проблемы, связь с транзакциями. Это продолжение серии вопросов о DDD, агрегатах, конкурентности.

Мне нужно:
1. Чётко определить, что такое доменное событие (в отличие от application event, integration event)
2. Показать конкретный код — как события собираются в агрегате, как публикуются
3. Связать с транзакциями БД — это критический момент
4. Показать обработку (sync vs async)
5. Типовые проблемы: "event storming" без пользы, потеря событий, дублирование, ordering
6. Связь с предыдущими ответами (агрегат, конкурентность, application service)

Структура ответа:
- Что такое доменное событие (определение, отличие от application event)
- Когда публиковать (правило: инвариант изменён → событие)
- Как публиковать (паттерн: события собираются в агрегате, публикуются application layer)
- Как обрабатывать (sync listener vs async handler)
- Связь с транзакциями (КРИТИЧНО: publish ДО commit или ПОСЛЕ?)
- Конкретный код
- Типовые проблемы
- Антипаттерны

Ключевой момент: доменное событие публикуется ПОСЛЕ успешного commit транзакции. Не до. Не в транзакции. Это "факт, который произошёл".

Но есть нюанс: если использовать "outbox pattern" — событие пишется в БД в той же транзакции, а публикуется асинхронно. Это решение проблемы "commit прошёл, но событие потеряно".

Давайте структурирую:

1. Три типа событий (domain, application, integration)
2. Когда публиковать
3. Как публиковать (код)
4. Связь с транзакциями (outbox pattern)
5. Как обрабатывать (sync/async)
6. Что решает
7. Что создаёт
8. Антипаттерны
9. Практические правила

Код должен быть конкретным, на PHP/Symfony/Doctrine, как в предыдущих ответах.

</details>

**Кандидат:**

## Что такое доменное событие

Доменное событие — это **факт, который произошёл в домене и который не может быть отменён**. Не «мы собираемся что-то сделать». Не «мы просим что-то сделать». Факт.

```
"Заказ подтверждён"          ← доменное событие (факт)
"Нужно отправить письмо"     ← application event (задача)
"Stripe: платёж прошёл"      ← integration event (внешний факт)
```

Доменное событие отвечает на вопрос: **что изменилось в состоянии мира, что невозможно откатить?**

---

## Три типа событий, три разных слоя

| Тип | Где рождается | Кто потребляет | Пример |
|---|---|---|---|
| **Domain event** | Агрегат (домен) | Application service, другие агрегаты | `OrderConfirmed`, `StockDepleted` |
| **Application event** | Application service | UI, логирование, аналитика | `OrderPlacedByAdmin` |
| **Integration event** | Внешняя система | Наше приложение | `StripePaymentSucceeded` |

В этом ответе — **domain events**. Application и integration — другие механизмы.

---

## Когда публиковать

**Правило:** событие публикуется, когда **инвариант агрегата изменился** и это изменение **зафиксировано** (commit).

| Изменение | Событие? | Почему |
|---|---|---|
| `status: pending → confirmed` | ✅ `OrderConfirmed` | Факт. Неотменяем. |
| `total: 5000 → 7000` (addLine) | ❌ | Промежуточное состояние. Может быть отменено. |
| `status: pending → cancelled` | ✅ `OrderCancelled` | Факт. |
| `lines: [L1, L2] → [L1, L2, L3]` | ❌ | Промежуточное. |
| `status: cancelled → pending` (rollback) | ❌ | Не должно происходить в домене. |
| `payment: pending → succeeded` | ✅ `PaymentSucceeded` | Факт. |

**Критерий:** если изменение **обратимо** (можно отменить) — это **не** событие. Событие — это **точка невозврата**.

---

## Как публиковать: паттерн

### Агрегат собирает, application layer публикует

```php
// src/Domain/Order.php
final class Order
{
    private ?int $id = null;
    private OrderStatus $status;
    private Money $total;
    private \DateTimeImmutable $createdAt;
    /** @var Collection<int, OrderLine> */
    private Collection $lines;

    /** @var DomainEvent[] */
    private array $events = [];

    // ─── Конструктор ───
    public function __construct(string $email)
    {
        $this->status    = OrderStatus::Pending;
        $this->total     = Money::rub(0);
        $this->createdAt = new \DateTimeImmutable();
        $this->lines     = new ArrayCollection();
    }

    // ─── Команды (меняют состояние) ───

    public function addLine(ProductId $productId, int $qty, Money $unitPrice): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Cannot modify confirmed order');
        }
        if ($qty < 1) {
            throw new \InvalidArgumentException('qty must be >= 1');
        }

        $this->lines->add(new OrderLine($productId, $qty, $unitPrice));
        $this->recalculateTotal();
        // ← НЕ публикуем событие. Промежуточное состояние.
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Only pending → confirmed');
        }
        if ($this->lines->isEmpty()) {
            throw new StateException('Cannot confirm empty order');
        }

        $this->status = OrderStatus::Confirmed;

        // ← СОБИРАЕМ событие. НЕ публикуем.
        $this->events[] = new OrderConfirmed(
            orderId: $this->id,
            total:   $this->total,
            lines:   $this->lines->map(fn($l) => [
                'productId' => $l->getProductId()->value,
                'qty'       => $l->getQty(),
                'price'     => $l->getUnitPrice()->amount,
            ])->toArray(),
        );
    }

    public function cancel(string $reason): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new StateException('Cannot cancel delivered order');
        }

        $this->status = OrderStatus::Cancelled;

        $this->events[] = new OrderCancelled(
            orderId: $this->id,
            reason:  $reason,
        );
    }

    // ─── Инварианты ───

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->getTotal()->amount;
        }
        $this->total = Money::rub($sum);
    }

    // ─── Getters ───

    public function getId(): ?int { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getTotal(): Money { return $this->total; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }

    /** @return DomainEvent[] */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];   // ← очищаем после извлечения
        return $events;
    }

    public function setId(int $id): void { $this->id = $id; }
}
```

```php
// src/Domain/Event/DomainEvent.php (базовый)
interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
}

// src/Domain/Event/OrderConfirmed.php
final readonly class OrderConfirmed implements DomainEvent
{
    public function __construct(
        public readonly ?int   $orderId,
        public readonly Money  $total,
        public readonly array  $lines,
        private readonly \DateTimeImmutable $occurredOn = new \DateTimeImmutable(),
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

// src/Domain/Event/OrderCancelled.php
final readonly class OrderCancelled implements DomainEvent
{
    public function __construct(
        public readonly ?int   $orderId,
        public readonly string $reason,
        private readonly \DateTimeImmutable $occurredOn = new \DateTimeImmutable(),
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
```

### Application service публикует

```php
// src/Application/Order/ConfirmOrderService.php
final class ConfirmOrderService
{
    public function __construct(
        private OrderRepository $orders,
        private InventoryService $inventory,
        private PaymentGateway $payment,
        private BonusService $bonus,
        private EventDispatcherInterface $dispatcher,  // ← Symfony EventDispatcher
        private EntityManagerInterface $em,
    ) {}

    public function confirm(int $orderId): void
    {
        // ─── 1. Чтение + доменная логика ───
        $order = $this->orders->findById($orderId);
        $order->confirm();   // ← собирает OrderConfirmed в $order->events

        // ─── 2. Связанные агрегаты (в той же или другой транзакции) ───
        $this->em->wrapInTransaction(function () use ($order) {
            foreach ($order->getLines() as $line) {
                $this->inventory->reserve($line->getProductId(), $line->getQty());
            }
            $this->orders->save($order);
        });
        // ← COMMIT. Факт зафиксирован в БД.

        // ─── 3. ПУБЛИКАЦИЯ СОБЫТИЙ (ПОСЛЕ commit) ───
        foreach ($order->pullEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
        // ← События обработаны. Заказ подтверждён.

        // ─── 4. Внешние эффекты (async, не блокируют ответ) ───
        // Обработаны через EventDispatcher (см. ниже)
    }
}
```

**Критически важно:** события публикуются **ПОСЛЕ** `wrapInTransaction` (commit). Не до. Не внутри.

```
ДО commit:    события ещё не «произошли». Может быть откат.
ПОСЛЕ commit: события — факт. Публикуем.
```

---

## Связь с транзакциями: проблема

### Проблема: «commit прошёл, но событие потеряно»

```php
$this->em->wrapInTransaction(function () use ($order) {
    $this->orders->save($order);
});

// ← ТУТ ПРОЦЕСС УПАЛ. COMMIT прошёл. События НЕ опубликованы.
// ← Заказ подтверждён в БД. Письмо не отправлено. Индекс не обновлён.

foreach ($order->pullEvents() as $event) {
    $this->dispatcher->dispatch($event);
}
```

Если процесс упадет между commit и dispatch — события **потеряны**. БД говорит «подтверждено», а остальные системы — «не знают».

### Решение: Transactional Outbox Pattern

Событие **пишется в БД** в той же транзакции, что и изменение данных. Отдельный процесс **читает** из таблицы и **публикует**.

```sql
CREATE TABLE outbox (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    aggregate_id  BIGINT       NOT NULL,
    event_type    VARCHAR(100) NOT NULL,
    payload       JSONB        NOT NULL,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    published_at  TIMESTAMPTZ,          -- NULL = ещё не опубликовано
    published_by  VARCHAR(100),
    retry_count   INTEGER      NOT NULL DEFAULT 0
);

CREATE INDEX idx_outbox_unpublished
    ON outbox (created_at)
    WHERE published_at IS NULL;
```

```php
// Application service
public function confirm(int $orderId): void
{
    $order = $this->orders->findById($orderId);
    $order->confirm();

    $this->em->wrapInTransaction(function () use ($order) {
        $this->orders->save($order);

        // ← Событие пишется в outbox В ТОЙ ЖЕ транзакции
        foreach ($order->pullEvents() as $event) {
            $this->outbox->write(
                aggregateId: $order->getId(),
                eventType:   $event::class,
                payload:     $this->serializer->serialize($event),
            );
        }
    });
    // COMMIT: заказ + outbox = атомарно.

    // ← НЕ публикуем здесь. Процесс outbox-publisher сделает это.
}
```

```php
// Отдельный процесс (cron / воркер)
final class OutboxPublisher
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private Serializer $serializer,
    ) {}

    public function publish(): void
    {
        $this->em->wrapInTransaction(function () {
            $pending = $this->em->createQuery(
                'SELECT o FROM OutboxEntity o
                 WHERE o.publishedAt IS NULL
                 ORDER BY o.createdAt ASC
                 LIMIT 100'
            )->getResult();

            foreach ($pending as $record) {
                try {
                    /** @var DomainEvent $event */
                    $event = $this->serializer->deserialize(
                        $record->getPayload(),
                        $record->getEventType()
                    );
                    $this->dispatcher->dispatch($event);

                    $record->markPublished();
                } catch (\Throwable $e) {
                    $record->incrementRetry();
                    if ($record->getRetryCount() > 5) {
                        $record->markFailed();
                    }
                }
            }
        });
    }
}
```

```bash
# Cron / supervisor
*/1 * * * *  php /app/bin/console outbox:publish
```

**Что это даёт:**

| Без Outbox | С Outbox |
|---|---|
| Commit + dispatch = 2 операции. Между ними — окно потери. | Commit + outbox = 1 операция. Атомарно. |
| Process crash → события потеряны. | Process crash → события в outbox. Next publish → отправлены. |
| Нет retry. | Retry + max_retries + dead-letter. |
| Нет ordering guarantee. | ORDER BY created_at. |

---

## Обработка событий: sync vs async

### Sync (в том же процессе, сразу)

```php
// Symfony EventDispatcher — sync by default
$dispatcher->dispatch($event);
// ← Все listeners вызваны. Событие обработано.
```

**Когда:**
- Обработчик **быстрый** (< 50 мс).
- Результат **нужен для продолжения** (например, логирование, валидация).
- Нет внешних вызовов.

```php
// Пример sync-обработчика
#[AsEventListener(event: OrderConfirmed::class, priority: 100)]
final class OrderAuditListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // Быстро: запись в audit_log. < 5 мс.
        $this->auditRepo->log($event->orderId, 'confirmed');
    }
}
```

### Async (отдельный процесс, очередь)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'App\Domain\Event\OrderConfirmed': async   # ← в очередь
            'App\Domain\Event\OrderCancelled': async
```

```bash
# Воркер
php bin/console messenger:consume async -vv
```

**Когда:**
- Обработчик **медленный** (SMTP, HTTP, ES).
- Есть **внешние вызовы**.
- Нужен **retry** при сбое.
- Нужно **масштабирование** (несколько воркеров).

```php
// Async handler
#[AsMessageHandler]
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UserProviderInterface $users,
    ) {}

    public function __invoke(OrderConfirmed $event): void
    {
        $user = $this->users->loadUserByEmail(
            // ← получаем email из event payload
            $event->lines // ← в реальном коде — отдельное поле
        );
        $this->mailer->send(/* … */);
        // ← Если упало → messenger:consume retry → повторная попытка
    }
}
```

### Как выбрать

```
Обработчик < 50 мс, без внешних вызовов
    → SYNC (EventDispatcher)

Обработчик > 50 мс, внешние вызовы, retry
    → ASYNC (Messenger + transport)

Нужен ordering (A перед B)
    → ASYNC + priority / sequence

Несколько обработчиков, независимые
    → ASYNC (параллельные воркеры)
```

---

## Что доменные события РЕШАЮТ

### 1. Развязка агрегатов

```
BEFORE:
  Order.confirm() → Product.decreaseStock() → User.addBonus() → Shipping.create()
  → 4 агрегата в одной транзакции. 4 БД. Блокировки. Deadlock.

AFTER:
  Order.confirm() → COMMIT → OrderConfirmed (событие)
  Product: слушает OrderConfirmed → decreaseStock() → COMMIT
  User:    слушает OrderConfirmed → addBonus() → COMMIT
  Shipping: слушает OrderConfirmed → create() → COMMIT

  → 4 независимые транзакции. 4 БД. 4 процесса. 4 масштаба.
```

### 2. Аудит и отладка

```
"Почему юзеру начислено 50 бонусов?"
→ outbox: OrderConfirmed { orderId: 1, total: 5000, occurredOn: 2025-01-15T10:23:01Z }
→ bonus:  "credited 50 for order #1 at 2025-01-15T10:23:01Z"

Без событий: "ну, кто-то вызвал addBonus()". Кто? Когда? Почему?
```

### 3. Расширяемость

```
Нужен новый обработчик: "отправить в CRM"
→ Новый listener. Без изменения Order, ConfirmOrderService, других listener'ов.

Нужен новый обработчик: "обновить аналитику"
→ Новый handler в Messenger. Без изменения существующих.
```

### 4. Idempotency

```
Событие отправлено дважды (network retry).
Обработчик:
  if ($this->log->exists($event->orderId, 'order_confirmed')) {
      return;   // ← уже обработано. Skip.
  }
  // … обработка …
  $this->log->mark($event->orderId, 'order_confirmed');
```

Без событий: «отправь письмо» вызывается дважды → два письма. С событиями: idempotency key в outbox.

---

## Что доменные события СОЗДАЮТ

### 1. Eventual consistency (а не strong consistency)

```
BEFORE: Order + Stock + Bonus = одна транзакция = strong consistency
AFTER:  Order COMMIT → Stock COMMIT → Bonus COMMIT
        Между ними: окно, в котором Order=confirmed, но Stock ещё не списан.
```

Для юзера: «заказ подтверждён, но товар ещё на складе». Если обработчик сток-списания упал — **несколько минут** (или часов) расхождение.

**Правило:** если бизнес **не может** выдержать eventual consistency (например, «деньги списаны, а заказ не создан») — события **не подходят**. Нужна одна транзакция.

### 2. Сложность отладки

```
"Почему письмо не отправлено?"

Без событий:
  ConfirmOrderService::confirm() → $this->mailer->send() → EXCEPTIO.
  → 1 место. 1 лог. 1 stack trace.

С событиями:
  ConfirmOrderService → COMMIT → outbox → publisher → messenger → handler → mailer
  → 6 мест. 6 логов. 6 возможных точек сбоя.
  → Нужно проверить: outbox (записано?), publisher (забрал?), messenger (в очереди?),
    handler (выполнен?), mailer (отправлено?).
```

### 3. Ordering и дублирование

```
События:
  OrderConfirmed(order#1)
  OrderConfirmed(order#1)   ← дубликат (retry)
  OrderCancelled(order#1)   ← из другого источника

Порядок обработки:
  1. OrderConfirmed → stock -5
  2. OrderConfirmed → stock -5 (дубликат! -10 вместо -5)
  3. OrderCancelled → stock +10 (отмена)

Итог: stock = 0. Но должно быть: stock = 0 (подтверждён) → отменён → stock = +5.
```

**Решение:**
- Idempotency key в обработчике.
- Ordering guarantee (sequence number в событии).
- Dead-letter queue для «непонятных» событий.

### 4. Сложность тестирования

```
Без событий:
  test_confirm():
    $order->confirm();
    $this->assertStock(-5);
    $this->assertBonus(+50);
  → 1 тест, 1 сценарий.

С событиями:
  test_confirm():
    $order->confirm();
    $this->assertOutboxCount(1);   ← только проверка, что событие записано
  test_stock_handler():
    $handler->handle(new OrderConfirmed(…));
    $this->assertStock(-5);
  test_bonus_handler():
    $handler->handle(new OrderConfirmed(…));
    $this->assertBonus(+50);
  test_idempotency():
    $handler->handle($event);
    $handler->handle($event);   ← дубликат
    $this->assertStock(-5);     ← -5, не -10
  → 4 теста, 4 сценария, 4 mock'а.
```

### 5. «Скрытая» логика

```
// Order::confirm()
$this->status = OrderStatus::Confirmed;
$this->events[] = new OrderConfirmed(…);
// ← Что происходит после? 3 handler'а в 3 разных файлах.
// ← Чтобы понять «что делает confirm()» — нужно прочитать 4 файла.
```

Без событий: `ConfirmOrderService::confirm()` — один метод, одна логика. Прочитал — понял.

---

## Антипаттерны

### 1. Событие для каждого `set()`

```php
public function setStatus(OrderStatus $status): void
{
    $this->status = $status;
    $this->events[] = new OrderStatusChanged(…);   // ← НА КАЖДЫЙ set
}
```

Результат: 50 событий на один заказ. 50 записей в outbox. 50 обработок. **Шум.**

**Правильно:** событие только для **неотменяемых** переходов: `confirm`, `cancel`, `deliver`.

### 2. Событие вместо команды

```php
// ПЛОХО: событие используется как «команда»
$dispatcher->dispatch(new UpdateStockCommand(productId: 1, qty: -5));
// ← Это НЕ событие. Это команда. Событие — «что произошло». Команда — «что сделать».
```

**Правильно:**
```php
// Событие (факт):
$dispatcher->dispatch(new OrderConfirmed(orderId: 1, …));

// Обработчик решает, что делать:
class StockHandler {
    public function __invoke(OrderConfirmed $e) {
        // Реши: списывать? Сколько? От какой партии?
        $this->inventory->reserve(…);
    }
}
```

### 3. Событие внутри агрегата (публикация)

```php
// ПЛОХО:
public function confirm(): void
{
    $this->status = OrderStatus::Confirmed;
    $this->dispatcher->dispatch(new OrderConfirmed(…));   // ← в домене!
}
```

**Почему плохо:**
- Домен зависит от `EventDispatcherInterface` (инфраструктура).
- Невозможно протестировать домен без mock'а dispatcher.
- Если диспетчер упал — домен в непонятном состоянии.

**Правильно:** домен **собирает** события. Application layer **публикует**.

### 4. Событие без payload-контекста

```php
final class OrderConfirmed
{
    public function __construct(
        public readonly ?int $orderId,   // ← только ID
    ) {}
}

// Обработчик:
public function __invoke(OrderConfirmed $event): void
{
    $order = $this->orderRepo->findById($event->orderId);   // ← доп. запрос!
    $user  = $this->userRepo->findById($order->getUserId());  // ← ещё один!
    // …
}
```

**Правильно:** событие несёт **достаточно данных**, чтобы обработчик **не обращался к БД** (или обращался минимально):

```php
final readonly class OrderConfirmed
{
    public function __construct(
        public readonly int    $orderId,
        public readonly string $email,
        public readonly Money  $total,
        public readonly array  $lines,      // ← productId, qty, unitPrice
        public readonly \DateTimeImmutable $occurredOn,
    ) {}
}
```

### 5. Циклические события

```
OrderConfirmed → StockHandler → StockDepleted → OrderHandler → OrderCancelled
OrderCancelled → StockHandler → StockRestored → …
→ Бесконечный цикл.
```

**Решение:**
- `maxDepth` в обработчике (не обрабатываем событие глубже N уровней).
- `source` в событии (откуда пришло).
- Явный «конечный» обработчик (не порождает новых событий).

### 6. Событие для того, что можно сделать синхронно

```php
// ПЛОХО: 2 строки логики через событие
$order->confirm();
$dispatcher->dispatch(new OrderConfirmed(…));
// Handler:
$this->auditLog->write(…);   // ← 1 строка. 5 мс.
```

**Правильно:**
```php
$order->confirm();
$this->auditLog->write(…);   // ← синхронно. Просто.
```

Событие — для **независимых** действий. Не для «запиши в лог».

---

## Практические правила

```
1. Событие = неотменяемый факт. Не «процесс», не «задача».

2. Домен собирает. Application layer публикует.

3. Публикация — ПОСЛЕ commit. Не до. Не в транзакции.

4. Outbox pattern — для надёжности. Commit + outbox = атомарно.

5. Sync — для быстрых (< 50 мс), без внешних вызовов.
   Async — для медленных, с retry, с масштабированием.

6. Payload — самодостаточный. Обработчик не обязан делать SELECT.

7. Idempotency — обязательна. Событие может прийти дважды.

8. Не больше 3–5 событий на один агрегат. Если больше — пересмотрите границы.

9. Событие — НЕ команда. «Что произошло», а не «что сделать».

10. Если обработчик < 10 строк и < 50 мс — рассмотрите sync. Не усложняйте.
```

---

## Итоговая схема

```
┌─────────────────────────────────────────────────────────────────────────┐
│  AGGREGATE (домен)                                                      │
│  ─────────────────                                                      │
│  Order::confirm()                                                       │
│    → status = Confirmed                                                 │
│    → events[] = [OrderConfirmed(…)]   ← СОБИРАЕТ, не публикует          │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  APPLICATION SERVICE                                                    │
│  ─────────────────────                                                  │
│  ConfirmOrderService::confirm()                                         │
│    1. $order = repo.findById(1)                                         │
│    2. $order->confirm()                                                 │
│    3. wrapInTransaction:                                                │
│         repo.save($order)                                               │
│         outbox.write(OrderConfirmed, payload)   ← В ТОЙ ЖЕ транзакции   │
│       COMMIT                                                            │
│    4. (НЕ публикуем здесь)                                              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                    ┌──────────┴──────────┐
                    ▼                     ▼
         ┌────────────────────┐  ┌────────────────────────┐
         │  OUTBOX PUBLISHER  │  │  DIRECT DISPATCH       │
         │  (cron / worker)   │  │  (sync, для быстрых)   │
         │                    │  │                        │
         │  read outbox       │  │  dispatcher.dispatch(  │
         │  dispatch event    │  │    OrderConfirmed)     │
         │  mark published    │  │  → AuditListener       │
         │  retry on failure  │  │  → (5 мс)              │
         └────────┬───────────┘  └────────────────────────┘
                  │
                  ▼
         ┌────────────────────┐
         │  MESSENGER BUS     │
         │  (async transport) │
         │                    │
         │  Redis / AMQP / DB │
         └────────┬───────────┘
                  │
     ┌────────────┼────────────┐
     ▼            ▼            ▼
┌─────────┐ ┌─────────┐ ┌──────────┐
│ Stock   │ │ Bonus   │ │ Shipping │
│ Handler │ │ Handler │ │ Handler  │
│ (async) │ │ (async) │ │ (async)  │
│         │ │         │ │          │
│ reserve │ │ credit  │ │ create   │
│ stock   │ │ points  │ │ shipment │
│         │ │         │ │          │
│ COMMIT  │ │ COMMIT  │ │ COMMIT   │
└─────────┘ └─────────┘ └──────────┘
```

Каждый обработчик — **отдельная транзакция**, **отдельный процесс**, **отдельный масштаб**. Если Stock упал — Bonus и Shipping **не знают**. Их можно перезапустить из outbox.

Это и есть **eventual consistency**: система **в целом** приходит к согласованному состоянию. Но **на каждый момент** отдельные части могут быть в разном состоянии. И это **нормально**, если бизнес это допускает.

> ⏱ 62.6s · prompt 82296 tok · output 7513 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанных вопроса по DDD:
1. Чем Value Object отличается от Entity?
2. Как решать, что VO, а что Entity?
3. Что такое Ubiquitous Language и как она влияет на код?

Нужно ответить на русском, структурированно, с примерами кода на PHP. Пользователь ведёт длинный разговор о DDD, Symfony, Doctrine — уровень явно продвинутый. Ответ должен быть конкретным, с практическими примерами, антипаттернами, без «воды».

Структура ответа:

1. **VO vs Entity** — ядро различия. Не «VO — маленький, Entity — большой». А: identity vs value. Мутабельность. Равенство. Где живут.

2. **Как решать** — конкретные критерии, не абстрактные. Практические вопросы. Примеры, где легко спутать.

3. **Ubiquitous Language** — что это, как влияет на код, примеры до/после. Антипаттерны.

Попробую сделать так:
- Ясное определение через контраст (не через абстракцию).
- Таблица сравнения.
- Критерии выбора (вопросы).
- Примеры: Email, Money, Address, Order, Product, User — и почему они VO/Entity.
- Серые зоны: Address (похож на VO, но может быть Entity), Product (Entity, но price — VO).
- Ubiquitous Language: конкретные примеры «до/после» в коде.
- Антипаттерны.

Не буду повторять то, что уже говорил в предыдущих ответах (агрегаты, события, транзакции). Сосредоточусь на VO/Entity/UL.

</details>

**Кандидат:**

## Value Object vs Entity

### Одно различие, на котором всё держится

**Entity определяется тем, КТО она. Value Object определяется тем, ЧТО она.**

```
Entity:  "Это заказ #4821"
         → У него есть история: создан 15.01, подтверждён 15.01, отменён 16.01.
         → Два "одинаковых" заказа — РАЗНЫЕ. (#4821 ≠ #4822, даже если те же товары)
         → К нему ссылаются по ID.

Value Object: "Это 10500 рублей"
         → У него нет истории. Money(10500, 'RUB') = Money(10500, 'RUB'). Всегда.
         → Два "одинаковых" Money — ОДИН И ТОТ ЖЕ.
         → Никто не ссылается на него по ID. Он — часть чего-то.
```

Это не «VO — маленький, Entity — большой». Не «VO — readonly, Entity — mutable». Это **identity**. У Entity есть непрерывная идентичность во времени. У VO — нет.

---

### Сравнение по всем осям

| | **Entity** | **Value Object** |
|---|---|---|
| **Идентичность** | ID. `order#4821` — это `order#4821`, даже если всё изменилось. | Значение. `Money(100,'RUB')` = `Money(100,'RUB')`. |
| **Равенство** | По ID. `$a->id === $b->id`. | По содержимому. `$a->amount === $b->amount && $a->currency === $b->currency`. |
| **Мутабельность** | Меняется во времени (status, total, lines). | Не меняется. «Новое значение» = новый объект. |
| **История** | Есть. События, аудит, `createdAt`, `updatedAt`. | Нет. `Money(100,'RUB')` не помнит, откуда взялся. |
| **Ссылки** | На неё ссылаются другие Entity/VO по ID. | Никто не ссылается. Она **внутри** Entity. |
| **Сравнение в БД** | `WHERE id = 4821`. | `WHERE amount = 100 AND currency = 'RUB'` (или как часть строки). |
| **Замена** | `$order->setStatus(Confirmed)` — та же сущность. | `$order->setTotal(Money(200,'RUB'))` — новый Money, старый выброшен. |
| **Жизненный цикл** | Создаётся → живёт → умирает. | Создаётся → существует внутри Entity → уничтожается вместе с ней. |

---

### Как это выглядит в коде

```php
// ─── ENTITY: Order ───────────────────────────────────────────
// Идентичность: id. История: status меняется. Ссылки: на неё ссылаются.
final class Order
{
    private ?int $id = null;              // ← ИДЕНТИЧНОСТЬ
    private OrderStatus $status;         // ← МЕНЯЕТСЯ
    private Money $total;                // ← VO ВНУТРИ ENTITY
    private Collection $lines;           // ← дочерние сущности
    private \DateTimeImmutable $createdAt;  // ← ИСТОРИЯ
    private ?\DateTimeImmutable $confirmedAt; // ← ИСТОРИЯ

    public function confirm(): void
    {
        $this->status = OrderStatus::Confirmed;
        $this->confirmedAt = new \DateTimeImmutable();  // ← история
    }

    public function equals(self $other): bool
    {
        return $this->id !== null && $this->id === $other->id;  // ← по ID
    }
}

// ─── VALUE OBJECT: Money ─────────────────────────────────────
// Идентичность: НЕТ. Не меняется. Равен по значению.
final readonly class Money
{
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be >= 0');
        }
    }

    public static function rub(int $kopecks): self { return new self($kopecks, 'RUB'); }
    public static function usd(int $cents): self   { return new self($cents, 'USD'); }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Currency mismatch');
        }
        return new self($this->amount + $other->amount, $this->currency);
        // ← НОВЫЙ объект. Старый НЕ мутирован.
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;  // ← по ЗНАЧЕНИЮ
    }
}

// ─── VALUE OBJECT: Email ─────────────────────────────────────
final readonly class Email
{
    public function __construct(public readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: $value");
        }
    }

    public function equals(self $other): bool
    {
        return strcasecmp($this->value, $other->value) === 0;  // ← case-insensitive
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

**Ключевое в `Money::add()`:** он возвращает **новый** `Money`. Не делает `$this->amount += $other->amount`. Это не «стиль». Это **следствие** того, что у Money нет identity. `Money(100,'RUB') + Money(50,'RUB')` = `Money(150,'RUB')`. Это **арифметика значений**, а не «изменение объекта».

---

## Как решать: Entity или VO?

### Пять вопросов

```
Вопрос 1: У неё есть ID, по которому на неё ссылаются другие объекты?
    ДА  → Entity
    НЕТ → VO (или скаляр)

Вопрос 2: Её можно «найти по ID» в репозитории?
    ДА  → Entity
    НЕТ → VO

Вопрос 3: У неё есть жизненный цикл (создана → изменена → удалена)?
    ДА  → Entity
    НЕТ → VO

Вопрос 4: Два экземпляра с одинаковыми полями — это ОДИН и тот же объект?
    ДА  → VO
    НЕТ → Entity

Вопрос 5: Её изменение порождает событие («Order#4821 подтверждён»)?
    ДА  → Entity (или агрегат)
    НЕТ → VO
```

### Практические примеры

| Концепция | Entity или VO? | Почему |
|---|---|---|
| `Order` | **Entity** | Есть ID. Ссылаются (Payment, Shipping). Есть история (status). |
| `OrderLine` | **Entity** (дочерняя) | Есть ID. Часть агрегата. Но не самостоятельная (без Order — не существует). |
| `Money` | **VO** | Нет ID. `Money(100,'RUB')` = `Money(100,'RUB')`. |
| `Email` | **VO** | Нет ID. `Email('a@b.c')` = `Email('A@B.C')`. |
| `Address` | **VO** (обычно) | Нет ID. Два одинаковых адреса — один и тот же. |
| `Address` | **Entity** (редко) | Если адрес **изменяется** и **имеет историю** (permanently changed, moved_from). |
| `Product` | **Entity** | Есть ID. Ссылаются (OrderLine, Cart). Есть история (price изменилась). |
| `ProductPrice` | **VO** | Нет ID. `Price(100,'RUB')` = `Price(100,'RUB')`. |
| `User` | **Entity** | Есть ID. Ссылаются. Есть история. |
| `UserId` | **VO** | Нет ID (сам ID). `UserId(42)` = `UserId(42)`. |
| `OrderStatus` | **Enum** (не VO, не Entity) | Замкнутое множество. Нет поведений. |
| `Coupon` | **Entity** | Есть ID. Есть срок действия. Может быть использован/истёк. |
| `Discount` | **VO** | Нет ID. `Discount(5, 'percent')` = `Discount(5, 'percent')`. |
| `ShippingAddress` | **VO** | Нет ID. Часть Order. |
| `Warehouse` | **Entity** | Есть ID. Ссылаются (Inventory). |
| `StockLevel` | **VO** | Нет ID. `StockLevel(42)` = `StockLevel(42)`. |

---

### Серые зоны: где решение не тривиально

#### `Address`

**Как VO:**
```php
final readonly class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $zip,
    ) {}

    public function equals(self $other): bool {
        return $this->street === $other->street
            && $this->city === $other->city
            && $this->zip === $other->zip;
    }
}
```

Когда уместен: `Order.shippingAddress` — «куда доставить». Это **значение**. Два заказа с одинаковым адресом — адрес один и тот же. Нет ID. Нет истории.

**Как Entity:**
```php
final class Address
{
    private ?int $id = null;
    private string $street;
    private string $city;
    private string $zip;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $deactivatedAt;
    private string $deactivatedReason;
}
```

Когда уместен: CRM, где адрес **изменяется** (переезд), имеет **историю** (was_at, moved_from), на него **ссылаются** другие объекты (invoice, delivery note). `Address#7` — это `Address#7`, даже если street изменился.

**Критерий:** если адрес **может измениться** и это **значит что-то** (история, аудит, ссылки) — Entity. Если это просто «куда доставить» — VO.

#### `Product` vs `ProductPrice`

`Product` — Entity. У него ID, история, ссылки.
`ProductPrice` — VO. У него нет ID. `Price(100,'RUB')` = `Price(100,'RUB')`.

Но `Product.currentPrice` — это **VO внутри Entity**. А `PriceHistory` (таблица всех изменений цены) — это **Entity** (каждая запись имеет ID, дату, причину изменения).

#### `User` vs `UserProfile`

`User` — Entity. ID, история, ссылки.
`UserProfile` (имя, аватар, bio) — обычно **VO внутри User**. Не отдельная сущность. `User#42.name = 'Ivan'` — изменение VO внутри Entity.

Но если профиль **может быть изменён другим сервисом** (админка, self-service) и имеет **отдельную историю изменений** — выносится в отдельную сущность.

---

### Антипаттерны

| Что делают | Почему плохо | Правильно |
|---|---|---|
| `Money` как Entity (с ID) | `Money#1` — это `Money#1`, даже если amount изменился? Нет. Money — значение. | VO. `readonly`. `equals()` по значению. |
| `Order` как VO | `Order(10500, 'RUB', [L1, L2])` = `Order(10500, 'RUB', [L1, L2])`? Нет. `Order#1` ≠ `Order#2`. | Entity. ID. История. |
| `Email` как Entity | `Email#1` — это `Email#1`, даже если value изменился? Нет. | VO. |
| `OrderStatus` как Entity | `Status#1`? Нет. Это значение из замкнутого множества. | Enum. |
| `Address` всегда Entity | Не нужен ID, история, ссылки. Лишняя таблица. | VO. |
| `Address` всегда VO | CRM: адрес изменился, нужен аудит, ссылки. VO не может. | Entity. |

---

## Ubiquitous Language

### Что это

Ubiquitous Language (UL) — **один и тот же термин в домене, в коде, в БД, в документации, в разговоре с бизнесом**.

Это не «называем классы по-русски». Это: **если бизнес говорит «заказ отменён» — в коде это `Order::cancel()`, в БД это `UPDATE orders SET status = 'cancelled'`, в API это `POST /orders/:id/cancel`, в логах это `order.cancelled`. Один термин. Одна концепция. Повсюду.**

### До UL: «переводчик» в каждом слое

```php
// Код:
class Order {
    public function doAction(string $action) {
        if ($action === 'cancel') {
            $this->state = 3;   // ← 3 = отменено? кто помнит?
        }
    }
}

// БД:
// orders: id, state (INTEGER)
// state = 1 → created? placed? started?
// state = 2 → active? in_progress?
// state = 3 → cancelled? void? revoked?
// state = 4 → completed? delivered? closed?

// API:
// POST /orders/:id/action  { "action": "cancel" }
// POST /orders/:id/action  { "action": "void" }   ← а это тоже "отмена"?
// POST /orders/:id/action  { "action": "revoke" }  ← а это?

// Бизнес:
// "Заказ закрыт"  ← а это cancelled? completed? void?
```

**Проблема:** между бизнесом и кодом нужен **переводчик**. «Заказ отменён» → `doAction('cancel')` → `state = 3`. Каждый из трёх терминов может быть **другим** словом. И никто не уверен, что они означают одно и то же.

### После UL: один термин, одно значение, везде

```php
// Код:
final class Order {
    private OrderStatus $status;   // ← НЕ int. НЕ string. Enum.

    public function cancel(string $reason): void {
        if ($this->status === OrderStatus::Delivered) {
            throw new StateException('Cannot cancel delivered order');
        }
        $this->status = OrderStatus::Cancelled;
        // …
    }
}

final class OrderStatus {
    public const Pending   = 'pending';
    public const Confirmed = 'confirmed';
    public const Shipped   = 'shipped';
    public const Delivered = 'delivered';
    public const Cancelled = 'cancelled';
}
```

```sql
-- БД:
CREATE TABLE orders (
    id       BIGINT,
    status   VARCHAR(20) NOT NULL,   -- ← 'pending', 'confirmed', …
    -- НЕ INTEGER. Не 1,2,3,4.
    -- VARCHAR с CHECK (status IN ('pending','confirmed','shipped','delivered','cancelled'))
);
```

```
// API:
POST /orders/:id/cancel          ← «cancel» — то же слово, что в коде
GET  /orders?status=cancelled    ← «cancelled» — то же слово, что в БД

// Логи:
order.cancelled { orderId: 4821, reason: 'fraud' }

// Бизнес:
"Заказ отменён"  ← cancel → Cancelled → cancelled. Один термин.
```

**Никакого переводчика.** «Отмена заказа» — это `cancel()`. Везде.

### Конкретные примеры UL

| Бизнес говорит | Код | БД | API | Логи |
|---|---|---|---|---|
| «Заказ» | `Order` | `orders` | `/orders` | `order.*` |
| «Строка заказа» | `OrderLine` | `order_lines` | `/orders/:id/lines` | `order.line.*` |
| «Подтвердить заказ» | `Order::confirm()` | `status = 'confirmed'` | `POST /orders/:id/confirm` | `order.confirmed` |
| «Отменить заказ» | `Order::cancel()` | `status = 'cancelled'` | `POST /orders/:id/cancel` | `order.cancelled` |
| «Стоимость заказа» | `Order::getTotal(): Money` | `total` | `total` | `order.total` |
| «Купленный товар» | `Product` | `products` | `/products` | `product.*` |
| «Количество» | `OrderLine::getQty(): int` | `qty` | `qty` | `qty` |
| «Цена за единицу» | `OrderLine::getUnitPrice(): Money` | `unit_price` | `unit_price` | `unit_price` |
| «Склад» | `Warehouse` | `warehouses` | `/warehouses` | `warehouse.*` |
| «Остаток на складе» | `StockLevel::getQty(): int` | `stock_qty` | `stock_qty` | `stock.qty` |
| «Списать со склада» | `Warehouse::decreaseStock()` | `UPDATE stock_qty` | — | `stock.decreased` |

**Один термин. Везде. Без перевода.**

### Как UL влияет на код

#### 1. Имена методов — действия из домена

```php
// ПЛОХО: «техническое» имя
public function doAction(string $action): void { … }
public function changeState(int $state): void { … }
public function updateField(string $field, mixed $value): void { … }

// ХОРОШО: действие из домена
public function confirm(): void { … }
public function cancel(string $reason): void { … }
public function addLine(ProductId $id, int $qty, Money $price): void { … }
public function decreaseStock(ProductId $id, int $qty): void { … }
```

#### 2. Имена полей — термины из домена

```php
// ПЛОХО: «универсальные» имена
class Order {
    public $value;        // ← value чего? total? amount? sum?
    public $type;         // ← type чего? status? category?
    public $date;         // ← какая date? created? confirmed? delivered?
    public $ref;          // ← ref на что? customer? product?
}

// ХОРОШО: термины из домена
class Order {
    public Money $total;
    public OrderStatus $status;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $confirmedAt;
    public ProductId $productId;
    public CustomerId $customerId;
}
```

#### 3. Имена таблиц — те же термины

```sql
-- ПЛОХО:
CREATE TABLE t1 (val INT, typ INT, dt DATE, ref INT);

-- ХОРОШО:
CREATE TABLE orders (
    total        INTEGER,
    status       VARCHAR(20),
    created_at   TIMESTAMPTZ,
    confirmed_at TIMESTAMPTZ,
    customer_id  BIGINT,
    product_id   BIGINT
);
```

#### 4. События — термины из домена

```php
// ПЛОХО:
class OrderEvent {
    public $action;   // ← 'confirm' | 'cancel' | 'deliver'
    public $payload;  // ← array
}

// ХОРОШО:
final readonly class OrderConfirmed { … }
final readonly class OrderCancelled { … }
final readonly class OrderDelivered { … }
```

#### 5. API — термины из домена

```
// ПЛОХО:
POST /api/v1/o/{id}/a    { "a": "c" }
POST /api/v1/o/{id}/a    { "a": "v" }

// ХОРОШО:
POST /orders/{id}/confirm
POST /orders/{id}/cancel
GET  /orders?status=cancelled
```

### Как выработать UL на практике

#### Шаг 1: Собрать термины с бизнесом

Садитесь с продакт-менеджером / бизнес-аналитиком / операционщиками. Задавайте вопросы:

```
"Как вы называете, когда покупатель выбирает товары?"
→ "Корзина"

"А когда он нажимает 'Оформить'?"
→ "Оформление заказа"

"А что происходит потом?"
→ "Мы проверяем склад, списываем, принимаем оплату"

"А если оплаты нет?"
→ "Заказ в 'ожидании оплаты'"

"А сколько ждать?"
→ "24 часа. Потом отменяем"
```

#### Шаг 2: Зафиксировать

```
ТЕРМИН              ОПРЕДЕЛЕНИЕ                              КОД
─────────────────────────────────────────────────────────────────────────
Корзина             Набор товаров, выбранных покупателем    Cart
Оформление          Действие «зафиксировать» корзину        PlaceOrder
Заказ               Сформированный, но не подтверждённый     Order (pending)
Подтверждение       Оплата получена, сток списан             Order (confirmed)
Ожидание оплаты     Заказ создан, оплаты ещё нет             Order (awaiting_payment)
Отмена              Заказ снят, сток возвращён               Order (cancelled)
Строка заказа       Один товар + количество + цена           OrderLine
Количество          Сколько единиц                           qty
Цена за единицу     Стоимость одного экземпляра              unitPrice
Сумма заказа        Σ(количество × цена за единицу)          total
Склад               Место хранения товаров                   Warehouse
Остаток             Сколько единиц на складе                 stockQty
Списать             Уменьшить остаток                        decreaseStock
Возврат            Увеличить остаток                        increaseStock
```

#### Шаг 3: Переименовать код

```php
// ПЕРЕД (каждый разработчик по-своему):
class Order {
    public $sum;
    public $state;
    public $count;
    public $amount_per_unit;
}

// ПОСЛЕ (один язык):
class Order {
    public Money $total;
    public OrderStatus $status;
    public int $qty;
    public Money $unitPrice;
}
```

#### Шаг 4: Зафиксировать в репозитории

```
/docs/ubiquitous-language.md
```

```markdown
# Ubiquitous Language

| Термин | Определение | Код | БД | API |
|---|---|---|---|---|
| Заказ | … | Order | orders | /orders |
| Подтвердить | … | confirm() | status='confirmed' | POST /:id/confirm |
| Строка заказа | … | OrderLine | order_lines | /orders/:id/lines |
| … | … | … | … | … |
```

#### Шаг 5: Проверять на code review

```
Reviewer: "Почему `doAction('c')`? Где `cancel()`?"
Developer: "Ну, я подумал, что потом будут другие action'ы…"
Reviewer: "В UL написано `cancel()`. Это действие. Не `doAction`."
```

### Антипаттерны UL

| Что делают | Почему плохо | Правильно |
|---|---|---|
| `Order.doAction('c')` | Не ясно, что «c». Неясный код. | `Order.cancel()` |
| `t1.val` | Неясная таблица, неясное поле. | `orders.total` |
| 5 разных слов для одного понятия | `sum`, `total`, `amount`, `price`, `cost` | Одно слово: `total` |
| Разные слова в коде, БД, API | `cancel` в коде, `void` в БД, `revoke` в API | `cancel` везде |
| `status = 3` | 3 = что? Отменён? Завершён? | `status = 'cancelled'` |
| `doSomething()` | Что за something? | Конкретное действие: `confirm()`, `cancel()`, `addLine()` |

---

## Связь VO/Entity с UL

UL определяет **названия**. VO/Entity определяет **структуру**. Они работают вместе:

```
Бизнес: "Заказ"
UL:      Order
Структура: Entity (есть ID, история, ссылки)

Бизнес: "Стоимость заказа"
UL:      total
Структура: VO (Money — нет ID, не меняется, равен по значению)

Бизнес: "Строка заказа"
UL:      OrderLine
Структура: Entity (есть ID, часть агрегата)

Бизнес: "Количество"
UL:      qty
Структура: int (скаляр, не VO, не Entity)

Бизнес: "Цена за единицу"
UL:      unitPrice
Структура: VO (Money)

Бизнес: "Статус заказа"
UL:      status
Структура: Enum (OrderStatus — замкнутое множество)
```

**UL говорит «как это называется». VO/Entity говорит «как это устроено». Вместе — полный контракт.**

---

## Итого

```
Entity:  Кто она? → ID. История. Ссылки. Меняется.
VO:      Что она? → Значение. Равенство по полям. Не меняется.
UL:      Как она называется? → Один термин. Везде. Без перевода.

Как решать Entity/VO:
  → Есть ID? Entity.
  → Есть история? Entity.
  → На неё ссылаются? Entity.
  → Два одинаковых = один? VO.
  → Не меняется? VO.

Как внедрять UL:
  → Собрать термины с бизнесом.
  → Зафиксировать в доке.
  → Переименовать код, БД, API.
  → Проверять на code review.
  → Обновлять, когда домен меняется.
```

> ⏱ 59.2s · prompt 89356 tok · output 6600 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

The user asks about clean architecture in PHP: layers, what lives in each, dependency rules, where Doctrine/Symfony controller/queue handler go, and how to enforce boundaries.

Let me think about what a truly useful answer looks here. I've been going deep on PHP, Symfony, Doctrine, DDD throughout this conversation. The user clearly has senior-level knowledge and is asking for a concrete, opinionated answer — not a textbook dump.

Key points to cover:
1. The layers (Domain, Application, Infrastructure, Interface/Interface Adapters)
2. What lives in each — concretely, with file names
3. Dependency rule: inner layers don't know about outer layers
4. Where Doctrine goes (Infrastructure)
5. Where Symfony controller goes (Interface/Adapters)
6. Where queue handler goes (Application or Interface — this is debatable, I should give my opinion)
7. How to enforce boundaries: PHPStan rules, directory structure, interfaces

Let me think about the dependency rule carefully. In clean architecture:
- Domain: knows nothing about anything else. Pure PHP. No framework.
- Application: orchestrates use cases. Depends on Domain (interfaces). Knows nothing about infrastructure.
- Infrastructure: implements interfaces defined in Application/Domain. Knows about Domain and Application interfaces.
- Interface (Adapters): entry points (HTTP, CLI, queue). Knows about Application.

The dependency rule: arrows point inward. Domain ← Application ← Infrastructure, Domain ← Application ← Interface.

Wait, let me be more precise. The dependency rule says: source code dependencies can only point inward. So:
- Domain: no dependencies
- Application: depends on Domain
- Infrastructure: depends on Application (implements its interfaces) and Domain
- Interface/Adapters: depends on Application and Domain

Actually, let me reconsider. The classic clean architecture diagram:
- Domain (innermost)
- Application (use cases)
- Interface Adapters (controllers, presenters, gateway implementations)
- Frameworks & Drivers (outermost)

Dependency rule: only inward.

In PHP/Symfony context:
- Domain: entities, VOs, domain events, domain services, repository INTERFACES
- Application: use case handlers, application services, ports (interfaces for infrastructure)
- Infrastructure: Doctrine entities, repositories (implementations), Doctrine types, mailer implementations, etc.
- Interface/Adapters: Symfony controllers, command handlers, message handlers (queue consumers)

Now, the tricky part: where do repository interfaces go? In strict clean architecture, the repository interface is a PORT defined by the Application layer (or Domain layer). The implementation is in Infrastructure.

In DDD terms: repository interface is in Domain. In clean architecture terms: it's a port in Application. Both are acceptable. I'll go with Domain for the repository interface (since it's a domain concept) but note that some put it in Application.

Where does the queue handler go? This is interesting. The queue handler (message handler) is an entry point — it receives a message and delegates to the application service. So it's an Interface/Adapter. The actual business logic is in Application. The handler is thin: deserialize → call application service → ack/nack.

But wait — in Symfony Messenger, the handler IS the use case handler in some patterns. If you separate "message handler" (adapter) from "use case handler" (application), then:
- Message handler (Interface): receives Envelope, calls application service
- Application service: orchestrates

Or you can merge them: the handler IS the application service, and it's registered as a message handler via DI. This is pragmatic but slightly violates the boundary.

I'll present both and give my opinion: thin adapter is cleaner for large apps, merged is fine for small apps.

How to enforce boundaries:
1. Directory structure (src/Domain, src/Application, src/Infrastructure, src/Interface)
2. PHPStan custom rules (forbid Domain from using Doctrine/Symfony classes)
3. PHPStan level + forbidden types
4. Architecture tests (Pest/PHPUnit)
5. Code review
6. composer.json autoload-dev separation (not really applicable)
7. phpstan.neon with specific rules

Let me structure this clearly with a concrete directory tree and code examples. I'll use the Order example from earlier in the conversation for continuity.

Let me write this in Russian since the conversation is in Russian.

</details>

**Кандидат:**

## Четыре кольца, одно правило

```
                    ┌─────────────────────────────────────────────────┐
                    │  FRAMEWORKS & DRIVERS (внешнее)                │
                    │  Symfony, Doctrine, Redis, SMTP, HTTP          │
                    │                                               │
                    │  ┌─────────────────────────────────────────┐   │
                    │  │  INTERFACE ADAPTERS (адаптеры)          │   │
                    │  │  Controllers, Message Handlers, CLI     │   │
                    │  │  Request/Response mapping, сериализация │   │
                    │  │                                         │   │
                    │  │  ┌─────────────────────────────────┐    │   │
                    │  │  │  APPLICATION (use cases)        │    │   │
                    │  │  │  Оркестрация, транзакции,       │    │   │
                    │  │  │  публикация событий             │    │   │
                    │  │  │                                 │    │   │
                    │  │  │  ┌─────────────────────────┐    │    │   │
                    │  │  │  │  DOMAIN (ядро)           │    │    │   │
                    │  │  │  │  Aggregate, VO, Events,  │    │    │   │
                    │  │  │  │  Domain Service,         │    │    │   │
                    │  │  │  │  Repository (интерфейс)  │    │    │   │
                    │  │  │  └─────────────────────────┘    │    │   │
                    │  │  └─────────────────────────────────┘    │   │
                    │  └─────────────────────────────────────────┘   │
                    │                                               │
                    └─────────────────────────────────────────────────┘

ПРАВИЛО ЗАВИСИМОСТЕЙ:
  Стрелки ТОЛЬКО ВНУТРЬ.
  Domain не знает о Application.
  Application не знает о Infrastructure.
  Infrastructure знает о Domain и Application (реализует их интерфейсы).
  Interface знает о Application (вызывает use cases).
```

**Одно предложение:** чем ближе к центру, тем меньше зависимостей. Domain не имеет **ни одной** внешней зависимости.

---

## Структура каталогов

```
src/
├── Domain/                          ← КОЛЬЦО 1: ЯДРО
│   ├── Order/
│   │   ├── Order.php                ← агрегат
│   │   ├── OrderLine.php            ← дочерняя сущность
│   │   ├── OrderStatus.php          ← enum
│   │   └── event/
│   │       ├── DomainEvent.php      ← интерфейс
│   │       ├── OrderConfirmed.php
│   │       └── OrderCancelled.php
│   ├── Product/
│   │   ├── Product.php
│   │   ├── ProductId.php            ← VO
│   │   └── Price.php                ← VO
│   ├── Money.php                    ← VO
│   ├── Email.php                    ← VO
│   ├── OrderRepository.php          ← ИНТЕРФЕЙС (порт)
│   ├── ProductRepository.php        ← ИНТЕРФЕЙС (порт)
│   ├── OrderDomainService.php       ← доменный сервис (опционально)
│   └── exception/
│       ├── StateException.php
│       ├── InsufficientStockException.php
│       └── ConcurrencyConflictException.php
│
├── Application/                     ← КОЛЬЦО 2: USE CASES
│   ├── Order/
│   │   ├── PlaceOrderUseCase.php
│   │   ├── ConfirmOrderUseCase.php
│   │   └── CancelOrderUseCase.php
│   ├── Product/
│   │   └── ReserveStockUseCase.php
│   ├── port/                        ← ПОРТЫ (интерфейсы для инфраструктуры)
│   │   ├── UnitOfWork.php           ← интерфейс (wrapInTransaction)
│   │   ├── EventPublisher.php       ← интерфейс (dispatch)
│   │   ├── IdGenerator.php          ← интерфейс
│   │   └── Clock.php                ← интерфейс
│   └── dto/                         ← DTO для входа/выхода use case
│       ├── PlaceOrderCommand.php
│       └── ConfirmOrderCommand.php
│
├── Infrastructure/                  ← КОЛЬЦО 3: РЕАЛИЗАЦИИ
│   ├── Doctrine/
│   │   ├── Entity/
│   │   │   ├── OrderEntity.php      ← ORM-сущность (АДAPTER)
│   │   │   └── OrderLineEntity.php
│   │   ├── Repository/
│   │   │   ├── OrderRepositoryImpl.php  ← реализует Domain\OrderRepository
│   │   │   └── ProductRepositoryImpl.php
│   │   ├── Type/
│   │   │   └── MoneyType.php
│   │   ├── Mapper/
│   │   │   ├── OrderMapper.php      ← Domain ↔ Entity
│   │   │   └── OrderLineMapper.php
│   │   └── UnitOfWorkDoctrine.php   ← реализует Application\port\UnitOfWork
│   ├── Message/
│   │   ├── OutboxRepository.php     ← реализация outbox (если не через Doctrine)
│   │   └── EventPublisherSymfony.php ← реализует Application\port\EventPublisher
│   ├── Mailer/
│   │   └── SymfonyMailerAdapter.php
│   ├── Search/
│   │   └── MeilisearchAdapter.php
│   └── Config/
│       └── services.yaml            ← DI-привязка портов → реализаций
│
├── Interface/                       ← КОЛЬЦО 4: АДАПТЕРЫ ВХОДА
│   ├── Http/
│   │   ├── Controller/
│   │   │   ├── OrderController.php  ← Symfony Controller
│   │   │   └── ProductController.php
│   │   ├── Request/
│   │   │   ├── PlaceOrderRequest.php
│   │   │   └── ConfirmOrderRequest.php
│   │   └── Response/
│   │       ├── OrderResponse.php
│   │       └── OrderListResponse.php
│   ├── Message/                     ← обработчики очереди
│   │   ├── OrderConfirmedHandler.php  ← AsMessageHandler
│   │   ├── SendWelcomeEmailHandler.php
│   │   └── UpdateSearchIndexHandler.php
│   └── Cli/
│       ├── OutboxPublishCommand.php   ← bin/console outbox:publish
│       └── StockSyncCommand.php
│
└── Kernel.php                       ← bootstrap (Symfony)

config/
├── services.yaml                    ← основная DI
├── packages/
│   ├── doctrine.yaml
│   ├── security.yaml
│   ├── messenger.yaml
│   └── framework.yaml
└── routes.yaml
```

---

## Что в каждом кольце: конкретика

### Кольцо 1: Domain

```php
// src/Domain/Order.php
// ЗАВИСИМОСТИ: НОЛЬ. Только PHP. Никаких use Symfony, use Doctrine, use Psr.

final class Order
{
    private ?int $id = null;
    private OrderStatus $status;
    private Money $total;
    /** @var Collection<int, OrderLine> */
    private Collection $lines;

    /** @var DomainEvent[] */
    private array $events = [];

    public function __construct(private readonly string $email)
    {
        $this->status  = OrderStatus::Pending;
        $this->total   = Money::rub(0);
        $this->lines   = new ArrayCollection();
    }

    public function addLine(ProductId $productId, int $qty, Money $unitPrice): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Cannot modify confirmed order');
        }
        if ($qty < 1) {
            throw new \InvalidArgumentException('qty must be >= 1');
        }
        $this->lines->add(new OrderLine($productId, $qty, $unitPrice));
        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new StateException('Only pending → confirmed');
        }
        if ($this->lines->isEmpty()) {
            throw new StateException('Cannot confirm empty order');
        }
        $this->status = OrderStatus::Confirmed;
        $this->events[] = new OrderConfirmed(
            orderId: $this->id,
            total:   $this->total,
        );
    }

    public function cancel(string $reason): void
    {
        if ($this->status === OrderStatus::Delivered) {
            throw new StateException('Cannot cancel delivered order');
        }
        $this->status = OrderStatus::Cancelled;
        $this->events[] = new OrderCancelled(
            orderId: $this->id,
            reason:  $reason,
        );
    }

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->getTotal()->amount;
        }
        $this->total = Money::rub($sum);
    }

    /** @return DomainEvent[] */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    // getters…
}
```

```php
// src/Domain/OrderRepository.php  ← ИНТЕРФЕЙС в Domain
interface OrderRepository
{
    public function findById(int $id): Order;
    public function save(Order $order): void;   // persist, БЕЗ flush
    public function remove(Order $order): void;
}
```

**Что здесь:**
- Агрегаты, сущности, VO.
- Доменные события (классы).
- Доменные исключения.
- **Интерфейсы** repository (порты, которые инфраструктура реализует).
- Доменные сервисы (если логика не влезает в агрегат).

**Чего здесь НЕТ:**
- `use Doctrine\…`, `use Symfony\…`, `use Psr\…` (кроме `Psr\EventDispatcher\Event` если очень нужно — но лучше и это убрать).
- Конфигурация, DI, аннотации.
- HTTP, JSON, SQL.

**Критерий:** если вы можете `composer remove symfony/doctrine` и Domain **продолжит работать** — всё правильно.

---

### Кольцо 2: Application

```php
// src/Application/Order/ConfirmOrderUseCase.php
// ЗАВИСИМОСТИ: Domain (агрегаты, события, repository interface)
//               + свои порты (UnitOfWork, EventPublisher)
// ЧЕГО НЕТ: Doctrine, Symfony, HTTP, Redis

final class ConfirmOrderUseCase
{
    public function __construct(
        private OrderRepository $orders,           // ← Domain interface
        private ProductRepository $products,       // ← Domain interface
        private UnitOfWork $uow,                   // ← Application port
        private EventPublisher $publisher,         // ← Application port
        private Clock $clock,                      // ← Application port
    ) {}

    public function __invoke(ConfirmOrderCommand $cmd): void
    {
        // 1. Загрузка
        $order = $this->orders->findById($cmd->orderId);

        // 2. Доменная логика
        $order->confirm();

        // 3. Сохранение (транзакция)
        $this->uow->wrapInTransaction(function () use ($order) {
            $this->orders->save($order);

            foreach ($order->pullEvents() as $event) {
                $this->publisher->publish($event);
            }
        });
        // ← COMMIT. События зафиксированы.

        // 4. (Не здесь. Обработчики событий — в Interface или отдельном воркере.)
    }
}
```

```php
// src/Application/port/UnitOfWork.php  ← ПОРТ
interface UnitOfWork
{
    /**
     * @template T
     * @param callable(): T $work
     * @return T
     * @throws \Throwable
     */
    public function wrapInTransaction(callable $work): mixed;
}

// src/Application/port/EventPublisher.php  ← ПОРТ
interface EventPublisher
{
    public function publish(object $event): void;
}

// src/Application/port/Clock.php  ← ПОРТ
interface Clock
{
    public function now(): \DateTimeImmutable;
}
```

```php
// src/Application/dto/ConfirmOrderCommand.php
final readonly class ConfirmOrderCommand
{
    public function __construct(
        public readonly int $orderId,
    ) {}
}
```

**Что здесь:**
- Use cases (одна команда = один use case).
- Порты (интерфейсы для infrastructure: UnitOfWork, EventPublisher, Clock, IdGenerator).
- DTO для входа (Command) и выхода (Query result).

**Чего здесь НЕТ:**
- Реализаций. Только интерфейсы и оркестрация.
- HTTP, SQL, JSON, SMTP.
- Конкретных классов Doctrine/Symfony.

**Критерий:** если вы замените Doctrine на Eloquent, Symfony на Slim, Redis на Memcached — Application **не изменится**.

---

### Кольцо 3: Infrastructure

```php
// src/Infrastructure/Doctrine/Repository/OrderRepositoryImpl.php
// ЗАВИСИМОСТИ: Domain (Order, OrderRepository) + Application (UnitOfWork)
//               + Doctrine (EntityManager, Entity)

final class OrderRepositoryImpl implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderMapper $mapper,
    ) {}

    public function findById(int $id): Order
    {
        $entity = $this->em->find(OrderEntity::class, $id)
            ?? throw new OrderNotFoundException($id);

        return $this->mapper->toDomain($entity);
    }

    public function save(Order $order): void
    {
        $entity = $order->getId() !== null
            ? $this->em->find(OrderEntity::class, $order->getId())
            : new OrderEntity();

        $this->mapper->apply($order, $entity);
        $this->em->persist($entity);
        // ← НЕТ flush. Flush делает UnitOfWork.
    }

    public function remove(Order $order): void
    {
        $entity = $this->em->find(OrderEntity::class, $order->getId());
        if ($entity) {
            $this->em->remove($entity);
        }
    }
}
```

```php
// src/Infrastructure/Doctrine/UnitOfWorkDoctrine.php
final class UnitOfWorkDoctrine implements UnitOfWork
{
    public function __construct(private EntityManagerInterface $em) {}

    public function wrapInTransaction(callable $work): mixed
    {
        $this->em->beginTransaction();
        try {
            $result = $work();
            $this->em->flush();
            $this->em->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
```

```php
// src/Infrastructure/Doctrine/Entity/OrderEntity.php
// Это ORM-сущность. Адаптер. НЕ доменная сущность.
#[ORM\Entity]
class OrderEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $status;

    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;

    #[ORM\OneToMany(mappedBy: 'order', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lines;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 0;

    // getters/setters для Doctrine
}
```

**Что здесь:**
- ORM-сущности (адаптеры, не доменные сущности).
- Реализации repository.
- Реализации портов (UnitOfWork, EventPublisher).
- Custom Doctrine Types.
- Mapper (Domain ↔ Entity).
- Конфигурация DI (привязка портов → реализаций).

**Критерий:** если вы удалите весь `src/Infrastructure` — Domain и Application **не изменятся**. Вы потеряете возможность работать с БД, но код останется.

---

### Кольцо 4: Interface (Adapters)

```php
// src/Interface/Http/Controller/OrderController.php
// ЗАВИСИМОСТИ: Application (use cases) + Symfony (Controller, Request)
// ЧЕГО НЕТ: Domain напрямую (только через Application)

final class OrderController extends AbstractController
{
    public function __construct(
        private ConfirmOrderUseCase $confirm,
        private PlaceOrderUseCase $place,
    ) {}

    #[Route('/orders/{id}/confirm', methods: ['POST'])]
    public function confirm(int $id): Response
    {
        $this->denyAccessUnlessGranted('edit', OrderId::from($id));

        $this->confirm(new ConfirmOrderCommand(orderId: $id));

        return new Response(status: 204);
    }

    #[Route('/orders', methods: ['POST'])]
    public function place(PlaceOrderRequest $request): JsonResponse
    {
        $cmd = new PlaceOrderCommand(
            email: $request->getEmail(),
            items: $request->getItems(),
        );

        $order = $this->place($cmd);

        return $this->json(new OrderResponse($order), status: 201);
    }
}
```

```php
// src/Interface/Message/OrderConfirmedHandler.php
// Обработчик очереди. ТОНКИЙ адаптер.

#[AsMessageHandler]
final class OrderConfirmedHandler
{
    public function __construct(
        private SendWelcomeEmailUseCase $sendEmail,
        private UpdateSearchIndexUseCase $updateIndex,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(OrderConfirmed $event): void
    {
        // Каждый use case — отдельный. Отдельная транзакция.
        try {
            $this->sendEmail(new SendEmailCommand(
                orderId: $event->orderId,
                email: $event->email,
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send email', [
                'order_id' => $event->orderId,
                'error'    => $e->getMessage(),
            ]);
            throw $e;   // ← Messenger retry
        }

        try {
            $this->updateIndex(new UpdateIndexCommand(
                orderId: $event->orderId,
                total:   $event->total,
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Failed to update index', [
                'order_id' => $event->orderId,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
```

```php
// src/Interface/Cli/OutboxPublishCommand.php
#[AsCommand(name: 'outbox:publish')]
final class OutboxPublishCommand extends Command
{
    public function __construct(
        private PublishOutboxUseCase $publish,
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->publish->handle();
        $output->writeln(sprintf("Published %d events", $count));
        return Command::SUCCESS;
    }
}
```

**Что здесь:**
- HTTP контроллеры (Symfony).
- Обработчики очереди (Messenger handlers).
- CLI команды.
- Mapping: HTTP request → Command, Domain → Response.
- Обработка HTTP-статусов, валидация формы.

**Критерий:** если вы замените Symfony на Slim/Laravel — Interface **переписывается**, но Domain и Application **не меняются**.

---

## Правило зависимостей: наглядно

```
Interface  →  Application  →  Domain
    │              │              │
    │              ▼              │
    │         [порты]            │
    │              │              │
    ▼              ▼              │
Infrastructure ──────────────────┘
    │
    ▼
  БД, HTTP, SMTP, Redis, ES
```

| Кольцо | Знает о | НЕ знает о |
|---|---|---|
| **Domain** | — | Application, Infrastructure, Interface, Symfony, Doctrine |
| **Application** | Domain (агрегаты, события, repository interface) | Infrastructure (реализации), Interface, Symfony, Doctrine |
| **Infrastructure** | Domain + Application (реализует их интерфейсы) | Interface |
| **Interface** | Application + Domain (через Application) | — |

**Направление стрелок:** только внутрь. Внешнее не знает о внутреннем. Внутреннее не знает о внешнем.

---

## Куда падают конкретные вещи

| Что | Кольцо | Почему |
|---|---|---|
| `Order` (агрегат) | **Domain** | Чистая логика. Нет зависимостей. |
| `Money` (VO) | **Domain** | Чистая логика. |
| `OrderRepository` (интерфейс) | **Domain** | Порт, который инфраструктура реализует. |
| `ConfirmOrderUseCase` | **Application** | Оркестрация. Знает Domain + порты. |
| `ConfirmOrderCommand` (DTO) | **Application** | Вход use case. |
| `OrderEntity` (ORM) | **Infrastructure** | Адаптер. Знает Doctrine. |
| `OrderRepositoryImpl` | **Infrastructure** | Реализация. Знает Doctrine + Domain. |
| `OrderMapper` (Domain ↔ Entity) | **Infrastructure** | Преобразование. Знает оба мира. |
| `MoneyType` (Doctrine Type) | **Infrastructure** | Сериализация. |
| `UnitOfWorkDoctrine` | **Infrastructure** | Реализация порта. |
| `OrderController` (Symfony) | **Interface** | HTTP адаптер. |
| `PlaceOrderRequest` (Form/DTO) | **Interface** | Вход HTTP. |
| `OrderResponse` (DTO) | **Interface** | Выход HTTP. |
| `OrderConfirmedHandler` (Messenger) | **Interface** | Очередь адаптер. |
| `SendWelcomeEmailUseCase` | **Application** | Use case. |
| `SendWelcomeEmailCommand` | **Application** | Вход use case. |
| `SymfonyMailerAdapter` | **Infrastructure** | Реализация mailer порта. |
| `OutboxPublishCommand` (CLI) | **Interface** | CLI адаптер. |
| `PublishOutboxUseCase` | **Application** | Use case. |
| `services.yaml` (DI) | **Infrastructure** | Привязка портов → реализаций. |
| `security.yaml`, `doctrine.yaml` | **Infrastructure** | Конфигурация фреймворков. |
| `routes.yaml` | **Interface** | Маршруты HTTP. |

---

## Как следить, что границы не нарушаются

### 1. Структура каталогов (первый барьер)

```
src/Domain/        ← только Domain
src/Application/   ← только Application
src/Infrastructure/ ← только Infrastructure
src/Interface/     ← только Interface
```

Разработчик **физически** не может положить `OrderEntity` в `src/Domain/` — каталог не тот. Это не защита, но **видимость** нарушения.

### 2. PHPStan: forbidden types

```neon
# phpstan.neon
parameters:
    level: 8
    paths:
        - src

    ignoreErrors:
        # (только конкретные исключения, не glob)

    # ПРАВИЛО 1: Domain не может знать о Symfony, Doctrine, PSR (кроме EventDispatcher)
    checkExplicitMixed: true

# src/Domain/.phpstan.neon (override для каталога)
parameters:
    level: max
    reportUnmatchedIgnoredErrors: true

    # Запретить конкретные пространства имён:
    # (через кастомное правило, см. ниже)
```

### 3. PHPStan: кастомное правило (главный инструмент)

```php
// tools/phpstan-rules/NoExternalDependenciesInDomainRule.php
namespace App\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rule\Rule;
use PHPStan\Rules\IdentifierRuleError;

/**
 * @implements Rule<Use_>
 */
final class NoExternalDependenciesInDomainRule implements Rule
{
    private const FORBIDDEN_PREFIXES = [
        'Symfony\\',
        'Doctrine\\',
        'Psr\\',          // или допустите Psr\EventDispatcher\Event
        'GuzzleHttp\\',
        'Meilisearch\\',
    ];

    public function getNodeType(): string
    {
        return Use_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        // Правило применяется ТОЛЬКО к файлам в src/Domain/
        if (!str_contains($file, '/Domain/')) {
            return [];
        }

        $errors = [];
        foreach ($node->uses as $use) {
            $name = $use->name->toString();
            foreach (self::FORBIDDEN_PREFIXES as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    $errors[] = new IdentifierRuleError(
                        sprintf(
                            'Domain layer cannot depend on "%s". '
                            . 'Move this dependency to Infrastructure or define a port in Application.',
                            $name
                        ),
                        $node
                    );
                }
            }
        }
        return $errors;
    }
}
```

```php
// tools/phpstan-rules/NoExternalDependenciesInApplicationRule.php
// Аналогично, но для src/Application/
// Запрещены: Symfony\, Doctrine\, GuzzleHttp\, Meilisearch\
// Допущены: Psr\EventDispatcher\Event (если нужно), Domain\*
```

```neon
# phpstan.neon
services:
    -
        class: App\Rules\NoExternalDependenciesInDomainRule
        tags: [phpstan.rules.rule]
    -
        class: App\Rules\NoExternalDependenciesInApplicationRule
        tags: [phpstan.rules.rule]
```

**Результат:**

```
src/Domain/Order.php:12:31: Domain layer cannot depend on "Symfony\Component\Messenger\Envelope".
Move this dependency to Infrastructure or define a port in Application.
```

### 4. Архитектурные тесты (PHPUnit / Pest)

```php
// tests/Architecture/LayerBoundaryTest.php
final class LayerBoundaryTest extends TestCase
{
    private const FORBIDDEN = [
        'Symfony\\',
        'Doctrine\\',
        'GuzzleHttp\\',
        'Meilisearch\\',
    ];

    /**
     * Domain не должен использовать внешние библиотеки.
     */
    public function test_domain_has_no_external_dependencies(): void
    {
        $violations = $this->scanDirectory('src/Domain', self::FORBIDDEN);
        self::assertEmpty(
            $violations,
            "Domain layer has forbidden dependencies:\n" . implode("\n", $violations)
        );
    }

    /**
     * Application не должна использовать Doctrine/Symfony.
     */
    public function test_application_has_no_infrastructure_dependencies(): void
    {
        $forbidden = ['Doctrine\\', 'Symfony\\', 'GuzzleHttp\\'];
        $violations = $this->scanDirectory('src/Application', $forbidden);
        self::assertEmpty(
            $violations,
            "Application layer has forbidden dependencies:\n" . implode("\n", $violations)
        );
    }

    /**
     * Domain не должен знать о Infrastructure.
     */
    public function test_domain_does_not_reference_infrastructure(): void
    {
        $forbidden = ['App\\Infrastructure\\', 'App\\Interface\\'];
        $violations = $this->scanDirectory('src/Domain', $forbidden);
        self::assertEmpty($violations);
    }

    /**
     * Domain не должен знать о Interface.
     */
    public function test_domain_does_not_reference_interface(): void
    {
        $forbidden = ['App\\Interface\\'];
        $violations = $this->scanDirectory('src/Domain', $forbidden);
        self::assertEmpty($violations);
    }

    private function scanDirectory(string $dir, array $forbiddenPrefixes): array
    {
        $violations = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') continue;

            $content = file_get_contents($file->getPathname());

            foreach (self::FORBIDDEN as $prefix) {
                if (preg_match('/use\s+' . preg_quote($prefix, '/') . '[^;]+;/i', $content, $m)) {
                    $violations[] = sprintf('%s: %s', $file->getPathname(), trim($m[0]));
                }
            }
        }
        return $violations;
    }
}
```

Эти тесты проходят **в CI**. Если кто-то добавил `use Doctrine\…` в `src/Domain/` — тест **упадёт**.

### 5. DI-конфигурация как контракт

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    # Domain: только интерфейсы. Никогда не DI.
    App\Domain\:
        resource: '../src/Domain/'
        public: false

    # Application: use cases + порты (интерфейсы)
    App\Application\:
        resource: '../src/Application/'
        public: true    # use cases — публичные (их вызывают Interface)

    # Infrastructure: реализации. Привязка портов.
    App\Infrastructure\Doctrine\UnitOfWorkDoctrine:
        class: App\Infrastructure\Doctrine\UnitOfWorkDoctrine
        # autowire: EntityManagerInterface → inject

    App\Infrastructure\Doctrine\Repository\OrderRepositoryImpl:
        class: App\Infrastructure\Doctrine\Repository\OrderRepositoryImpl
        # autowire: EntityManagerInterface, OrderMapper

    # Привязка: порт → реализация
    App\Application\port\UnitOfWork: '@App\Infrastructure\Doctrine\UnitOfWorkDoctrine'
    App\Application\port\EventPublisher: '@App\Infrastructure\Message\EventPublisherSymfony'
    App\Domain\OrderRepository: '@App\Infrastructure\Doctrine\Repository\OrderRepositoryImpl'
```

**Ключевое:** DI-контейнер **привязывает** порты (Application/Domain) к реализациям (Infrastructure). Domain и Application **не знают**, к чему привязаны. Если вы поменяете `UnitOfWorkDoctrine` на `UnitOfWorkLaravel` — поменяете **одну строку** в `services.yaml`. Domain и Application — не трогаете.

### 6. Code review чек-лист

```
□ Новый класс в src/Domain/ — нет use Symfony/Doctrine/Psr?
□ Новый класс в src/Application/ — нет use Doctrine/Symfony?
□ Новый класс в src/Infrastructure/ — реализует интерфейс из Domain/Application?
□ Новый класс в src/Interface/ — вызывает use case из Application? (не Domain напрямую)
□ Новый DTO — в Application/dto, не в Domain?
□ Новый Mapper — в Infrastructure, не в Domain?
□ services.yaml — новый порт привязан к реализации?
□ PHPStan пройден? (включая кастомные правила)
□ Архитектурные тесты пройдены?
```

### 7. Автозагрузка как барьер

```json
// composer.json
{
    "autoload": {
        "psr-4": {
            "App\\Domain\\":         "src/Domain/",
            "App\\Application\\":    "src/Application/",
            "App\\Infrastructure\\": "src/Infrastructure/",
            "App\\Interface\\":      "src/Interface/"
        }
    }
}
```

Если разработчик случайно положил `OrderEntity` в `src/Domain/Entity/` — namespace будет `App\Domain\Entity\OrderEntity`, а не `App\Infrastructure\Doctrine\Entity\OrderEntity`. PHPStan и IDE сразу покажут «это не то место».

---

## Что делать, когда границы нарушены

### Ситуация 1: «Domain использует Symfony\Component\Messenger\Envelope»

```php
// src/Domain/Order.php
use Symfony\Component\Messenger\Envelope;  // ← НАРУШЕНИЕ

public function confirm(): void
{
    // …
    $this->events[] = new Envelope(new OrderConfirmed(…));  // ← АДАПТЕР В ДОМЕНЕ
}
```

**Решение:** Domain собирает **свои** события (`DomainEvent`). Application/Infrastructure **оборачивает** в `Envelope` (или кладёт в outbox). Domain не знает, как события доставляются.

### Ситуация 2: «Application использует EntityManagerInterface»

```php
// src/Application/Order/ConfirmOrderUseCase.php
use Doctrine\ORM\EntityManagerInterface;  // ← НАРУШЕНИЕ

public function __construct(
    private EntityManagerInterface $em,  // ← Infrastructure в Application
) {}
```

**Решение:** Ввести порт `UnitOfWork` в Application. Реализовать в Infrastructure. Application зависит от `UnitOfWork`, не от `EntityManagerInterface`.

### Ситуация 3: «Controller вызывает Domain напрямую»

```php
// src/Interface/Http/Controller/OrderController.php
use App\Domain\Order;  // ← НАРУШЕНИЕ (Interface → Domain, минуя Application)

public function confirm(int $id): Response
{
    $order = $this->orderRepo->find($id);  // ← Domain напрямую
    $order->confirm();                     // ← Domain напрямую
    $this->em->flush();                    // ← Infrastructure напрямую
}
```

**Решение:** Controller вызывает `ConfirmOrderUseCase`. Use case знает о Domain + портах. Controller не знает о Domain.

### Ситуация 4: «Repository impl в Domain»

```php
// src/Domain/OrderRepository.php
interface OrderRepository { … }

// src/DoctrineOrderRepository.php  ← В Domain каталоге!
class DoctrineOrderRepository implements OrderRepository {
    public function __construct(private EntityManagerInterface $em) {}
    // …
}
```

**Решение:** Переместить в `src/Infrastructure/Doctrine/Repository/`. Привязать в `services.yaml`.

---

## Компромиссы: когда не стоит быть пуританином

### 1. Мелкий проект (< 5 use cases, 1 разработчик)

Полные 4 кольца — overhead. Упрощение:

```
src/
├── Domain/         ← агрегаты, VO, события (чистый PHP)
├── Service/        ← use cases + repository impl (Application + Infrastructure вместе)
└── Controller/     ← HTTP (Interface)
```

Три каталога вместо четырёх. Граница Domain/Service — **главная**. Service/Controller — **вторичная**.

### 2. Read models / CQRS queries

Запросы (Read side) часто **не идут** через Domain. Это нормально:

```
Interface (Controller) → QueryHandler (Application) → Doctrine (Infrastructure)
                         ↑ не использует Domain. Возвращает DTO.
```

Query — это не use case (не мутирует состояние). Это **запрос к БД**. Пропуск Domain-кольца для Read side — допустимый компромисс.

### 3. Symfony Form / Validator

Form и Validator — это **Interface** (вход HTTP). Они не должны знать о Domain. Они маппят HTTP → Command. Validation — на уровне Interface (форма) или Application (business rules).

```
Interface:  OrderForm → PlaceOrderCommand
Application: PlaceOrderUseCase → Order (Domain)
Domain:     Order → StateException (если business rule нарушен)
```

### 4. `#[Autoconfigure]`, `#[Autowire]`

Это **Interface** level. Domain не должен иметь этих атрибутов. Если видите `#[Autoconfigure]` в `src/Domain/` — нарушение.

---

## Итого

```
Кольцо    Содержимое                     Зависимости
─────────────────────────────────────────────────────────────
Domain    Aggregate, VO, Events,         НИЧЕГО
          Repository (interface),
          Domain Service, Exceptions

Application Use Cases, Ports             Domain + свои порты
          (UnitOfWork, Publisher),
          DTO (Command/Query)

Infrastructure ORM Entities,             Domain + Application
          Repository Impl,              (реализует их интерфейсы)
          Mapper, Doctrine Types,
          UnitOfWork Impl,
          services.yaml

Interface   Controllers,                 Application + Domain
          Message Handlers,              (через Application)
          CLI Commands,
          Request/Response DTO
```

**Правило:** если вы не можете объяснить, **зачем** классу зависимость от внешнего мира — ему она не нужна. Domain не «нуждается» в `EntityManager`. Application не «нуждается» в `Request`. Им нужны **интерфейсы**, которые эти сущности реализуют.

**Проверка:** `composer remove doctrine/orm symfony/symfony` → Domain и Application **компилируются**. Domain **работает** (тесты проходят). Application **компилируется** (тесты проходят с mock'ами портов). Если не так — границы нарушены.

> ⏱ 80.4s · prompt 95629 tok · output 9727 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about a testing strategy for a Symfony application with PostgreSQL. They want to know:
1. Testing pyramid (or alternative)
2. Types of tests
3. What to mock vs what not to mock
4. How to keep tests fast and stable

I should build on the previous conversation context where we discussed:
- Clean architecture (4 rings: Domain, Application, Infrastructure, Interface)
- DDD (aggregates, VOs, domain events)
- Doctrine ORM
- Symfony Messenger
- PostgreSQL
- Value Objects, Entities, Aggregates

I'll structure this as:
1. Testing pyramid adapted to the 4-ring architecture
2. What to test at each level (with code examples)
3. Mocking strategy (what to mock, what not to mock)
4. Infrastructure for fast/stable tests (DB setup, fixtures, etc.)
5. Practical tips for stability

Let me write in Russian as the user has been communicating in Russian throughout.

</details>

**Кандидат:**

## Пирамида, переосмысленная через кольца

Классическая пирамида (много unit → меньше integration → мало E2E) — универсальна, но для Symfony + DDD я адаптирую её под **четыре кольца**. Каждое кольцо имеет **свой тип теста, свой уровень изоляции, своё время выполнения**.

```
            ┌─────────────────────┐
            │   E2E / API Tests   │  ~5–15 тестов.  ~30–60 с.
            │   (Interface + всё) │
            └──────────┬──────────┘
           ┌───────────┴───────────┐
           │  Integration Tests    │  ~50–100 тестов.  ~10–30 с.
           │  (Application + Infra)│
           └───────────┬───────────┘
          ┌────────────┴────────────┐
          │    Unit Tests           │  ~500–2000 тестов.  ~1–5 с.
          │    (Domain + VO)        │
          └────────────────────────┘
```

Пропорции: **80% unit, 15% integration, 5% E2E**. Не «70/20/10» из учебника — а именно 80/15/5, потому что в DDD **большая часть логики** — в Domain, и она **дешёвая** в тестировании.

---

## Кольцо 1: Domain — Unit Tests

### Что тестируем

- Инварианты агрегатов (`confirm()`, `cancel()`, `addLine()`).
- VO (`Money`, `Email`, `ProductId`).
- Доменные сервисы (если есть).
- Доменные события (payload, `occurredOn`).

### Чего НЕ тестируем

- Серилизацию.
- Транзакции.
- БД.
- HTTP.
- DI.

### Что мокаем

**Ничего.** Domain — чистый PHP. `new Order('a@b.c')`. `new Money(100, 'RUB')`. Никаких интерфейсов, никаких `EntityManager`. Единственное внешнее — `ArrayCollection` (Doctrine Collections), но это **контейнер**, не зависимость.

### Пример

```php
// tests/Domain/OrderTest.php
final class OrderTest extends TestCase
{
    public function test_confirm_changes_status_to_confirmed(): void
    {
        $order = new Order('user@example.com');
        $order->addLine(new ProductId(1), 2, Money::rub(5000));

        $this->assertSame(OrderStatus::Pending, $order->getStatus());

        $order->confirm();

        $this->assertSame(OrderStatus::Confirmed, $order->getStatus());
    }

    public function test_confirm_empty_order_throws(): void
    {
        $order = new Order('user@example.com');

        $this->expectException(StateException::class);
        $this->expectExceptionMessage('Cannot confirm empty order');

        $order->confirm();
    }

    public function test_cannot_confirm_twice(): void
    {
        $order = new Order('user@example.com');
        $order->addLine(new ProductId(1), 1, Money::rub(1000));
        $order->confirm();

        $this->expectException(StateException::class);
        $this->expectExceptionMessage('Only pending → confirmed');

        $order->confirm();
    }

    public function test_cannot_cancel_delivered_order(): void
    {
        $order = new Order('user@example.com');
        $order->addLine(new ProductId(1), 1, Money::rub(1000));
        $order->confirm();
        $order->ship();
        $order->deliver();

        $this->expectException(StateException::class);
        $this->expectExceptionMessage('Cannot cancel delivered order');

        $order->cancel('reason');
    }

    public function test_total_recalculated_on_add_line(): void
    {
        $order = new Order('user@example.com');
        $order->addLine(new ProductId(1), 2, Money::rub(5000));
        $order->addLine(new ProductId(2), 1, Money::rub(3000));

        $this->assertTrue(
            $order->getTotal()->equals(Money::rub(13000))
        );
    }

    public function test_cannot_add_line_to_confirmed_order(): void
    {
        $order = new Order('user@example.com');
        $order->addLine(new ProductId(1), 1, Money::rub(1000));
        $order->confirm();

        $this->expectException(StateException::class);
        $this->expectExceptionMessage('Cannot modify confirmed order');

        $order->addLine(new ProductId(2), 1, Money::rub(500));
    }
}
```

```php
// tests/Domain/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function test_add_same_currency(): void
    {
        $a = Money::rub(1000);
        $b = Money::rub(500);

        $result = $a->add($b);

        $this->assertTrue($result->equals(Money::rub(1500)));
        $this->assertTrue($a->equals(Money::rub(1000)));  // ← иммутабельность
    }

    public function test_add_different_currency_throws(): void
    {
        $a = Money::rub(1000);
        $b = Money::usd(100);

        $this->expectException(\DomainException::class);
        $a->add($b);
    }

    public function test_negative_amount_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-1, 'RUB');
    }

    public function test_equality(): void
    {
        $a = new Money(1000, 'RUB');
        $b = new Money(1000, 'RUB');
        $c = new Money(1000, 'USD');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
```

### Время выполнения

```bash
$ vendor/bin/phpunit tests/Domain/
OK (342 tests, 1287 assertions)
Time: 0.8 seconds
```

**Почему这么快:** нет БД, нет HTTP, нет DI. `new Order(…)` → assert. 342 теста за 0.8 с.

### Стратегия

| Что | Как |
|---|---|
| Каждый invariant | 2–3 теста (happy path, boundary, illegal transition) |
| Каждый VO | equality, immutability, validation, edge cases (0, negative, max) |
| Каждый domain event | payload-поля, `occurredOn`, сериализация/десериализация (если есть) |
| Domain service | как агрегат |

**Правило:** если тест Domain требует `EntityManager` или `Symfony\Component` — **архитектура нарушена**. Чините код, не тест.

---

## Кольцо 2: Application — Integration Tests (без БД)

### Что тестируем

- Use cases: оркестрацию (загрузка → домен → сохранение → события).
- Порты: что use case **вызывает** (save, publish, wrapInTransaction).
- Обработку исключений: `OptimisticLockException` → retry, `StateException` → propagate.

### Что мокаем

**Порты.** Не реализацию.

```php
final class ConfirmOrderUseCaseTest extends TestCase
{
    private OrderRepository $orders;
    private UnitOfWork $uow;
    private EventPublisher $publisher;
    private Clock $clock;
    private ConfirmOrderUseCase $useCase;

    protected function setUp(): void
    {
        $this->orders    = $this->createMock(OrderRepository::class);
        $this->uow       = $this->createMock(UnitOfWork::class);
        $this->publisher = $this->createMock(EventPublisher::class);
        $this->clock     = $this->createMock(Clock::class);

        $this->useCase = new ConfirmOrderUseCase(
            orders:    $this->orders,
            products:  $this->createMock(ProductRepository::class),
            uow:       $this->uow,
            publisher: $this->publisher,
            clock:     $this->clock,
        );
    }

    public function test_confirm_calls_save_and_publishes_event(): void
    {
        $order = new Order('user@example.com');
        $order->setId(42);
        $order->addLine(new ProductId(1), 1, Money::rub(5000));

        // Мокаем репозиторий: возвращает наш Order
        $this->orders->method('findById')->with(42)->willReturn($order);

        // Мокаем UoW: вызывает callable внутри
        $this->uow->method('wrapInTransaction')
            ->willReturnCallback(function (callable $work) {
                return $work();   // ← реально вызываем
            });

        // Ожидаем публикацию
        $this->publisher->expects(self::once())
            ->method('publish')
            ->with(self::callback(function (object $e) {
                return $e instanceof OrderConfirmed;
            }));

        // Мокаем save
        $this->orders->expects(self::once())->method('save');

        $this->useCase(new ConfirmOrderCommand(42));
    }

    public function test_confirmed_order_throws_state_exception(): void
    {
        $order = new Order('user@example.com');
        $order->setId(42);
        $order->addLine(new ProductId(1), 1, Money::rub(5000));
        $order->confirm();   // ← уже подтверждён

        $this->orders->method('findById')->with(42)->willReturn($order);

        $this->expectException(StateException::class);
        $this->expectExceptionMessage('Only pending → confirmed');

        // UoW НЕ вызывается
        $this->uow->expects(self::never())->method('wrapInTransaction');

        $this->useCase(new ConfirmOrderCommand(42));
    }

    public function test_optimistic_lock_triggers_retry(): void
    {
        $order = new Order('user@example.com');
        $order->setId(42);
        $order->addLine(new ProductId(1), 1, Money::rub(5000));

        $this->orders->method('findById')->willReturn($order);

        // Первый вызов — OptimisticLockException. Второй — успех.
        $this->uow->expects(self::exactly(2))
            ->method('wrapInTransaction')
            ->willReturnCallback(function (callable $work, int &$call = 0) use ($order) {
                $call++;
                if ($call === 1) {
                    throw new \Doctrine\ORM\OptimisticLockException('conflict');
                }
                $order->setId(42);   // «перечитали»
                return $work();
            });

        $this->useCase(new ConfirmOrderCommand(42));
    }
}
```

### Чего НЕ мокаем

- **Домен.** `new Order(…)` — реальный объект. Мы тестируем **оркестрацию**, не логику.
- **DTO.** `new ConfirmOrderCommand(42)` — реальный.

### Время выполнения

```bash
$ vendor/bin/phpunit tests/Application/
OK (89 tests, 234 assertions)
Time: 1.2 seconds
```

### Стратегия

| Что | Как |
|---|---|
| Happy path use case | 1 тест: mock ports → assert calls |
| Каждый exception path | 1 тест: `expectException` |
| Retry логика | 1 тест: mock port → exception → success |
| Order of operations | `expects(self::once())` + `with()` |

---

## Кольцо 3: Infrastructure — Integration Tests (с БД)

### Что тестируем

- Repository impl: `findById` возвращает правильный `Order`. `save` делает INSERT/UPDATE.
- Mapper: Domain ↔ Entity (поля не потеряны).
- Custom Types: `MoneyType` сериализует/десериализует корректно.
- `#[ORM\Version]`: optimistic locking работает.
- Outbox: запись + публикация.

### Что мокаем

**Ничего.** Это интеграционные тесты. Реальная БД, реальная Doctrine, реальный mapper.

### Инфраструктура тестов

#### Вариант A: Testcontainers (рекомендую для CI)

```yaml
# tests/Infrastructure/docker-compose.yml
services:
  postgres:
    image: postgres:16-alpine
    environment:
      POSTGRES_USER: test
      POSTGRES_PASSWORD: test
      POSTGRES_DB: test
    ports:
      - "5433:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U test"]
      interval: 2s
      timeout: 5s
      retries: 5
```

```php
// tests/Infrastructure/TestCase/DatabaseTestCase.php
abstract class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        static::bootKernel(['environment' => 'test']);
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Очистка БД перед каждым тестом
        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $this->em->getConnection()->executeStatement('SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL READ COMMITTED');
        $this->em->getConnection()->executeStatement('TRUNCATE order_lines, orders, users, products, outbox RESTART IDENTITY CASCADE');
        $this->em->clear();
    }
}
```

#### Вариант B: Доменный кэш (доменно, без Docker)

```php
// tests/Infrastructure/TestCase/DatabaseTestCase.php
abstract class DatabaseTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel(['environment' => 'test']);
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Каждый тест в транзакции → откат в tearDown
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->close();
        parent::tearDown();
    }
}
```

| | Testcontainers | Транзакция + rollback |
|---|---|---|
| **Изоляция** | Полная. Новая БД. | Почти полная. `TRUNCATE` между тестами. |
| **Скорость** | Медленнее (Docker). | Быстрее (один `BEGIN`/`ROLLBACK`). |
| **CI** | Надёжно. | Зависит от локальной БД. |
| **Миграции** | Прогоняются при старте. | Должны быть уже применены. |
| **Когда** | CI, production-подобная среда. | Локальная разработка. |

### Пример теста

```php
// tests/Infrastructure/Doctrine/OrderRepositoryTest.php
final class OrderRepositoryTest extends DatabaseTestCase
{
    private OrderRepositoryImpl $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new OrderRepositoryImpl(
            em: $this->em,
            mapper: new OrderMapper(),
        );
    }

    public function test_save_and_find_roundtrip(): void
    {
        // Arrange
        $order = new Order('test@example.com');
        $order->addLine(new ProductId(1), 3, Money::rub(1500));
        $this->repo->save($order);
        $this->em->flush();   // ← в интеграционном тесте flush явно

        $id = $order->getId();
        $this->assertNotNull($id);

        // Act
        $found = $this->repo->findById($id);

        // Assert
        $this->assertSame('test@example.com', $found->getEmail());
        $this->assertTrue($found->getTotal()->equals(Money::rub(4500)));
        $this->assertCount(1, $found->getLines());

        $line = $found->getLines()->first();
        $this->assertTrue($line->getProductId()->equals(new ProductId(1)));
        $this->assertSame(3, $line->getQty());
    }

    public function test_find_missing_throws(): void
    {
        $this->expectException(OrderNotFoundException::class);
        $this->repo->findById(99999);
    }

    public function test_optimistic_locking(): void
    {
        $order = new Order('test@example.com');
        $order->addLine(new ProductId(1), 1, Money::rub(1000));
        $this->repo->save($order);
        $this->em->flush();

        $id = $order->getId();

        // Два менеджера (две «сессии»)
        $em2 = $this->em->createEntityManager(
            $this->em->getConnection()->getParams()
        );
        $repo2 = new OrderRepositoryImpl($em2, new OrderMapper());

        $orderFromEm1 = $this->repo->findById($id);
        $orderFromEm2 = $repo2->findById($id);

        // Em1: подтверждает
        $orderFromEm1->confirm();
        $this->repo->save($orderFromEm1);
        $this->em->flush();   // ← COMMIT

        // Em2: пытается подтвердить (version устарел)
        $orderFromEm2->confirm();
        $repo2->save($orderFromEm2);

        $this->expectException(\Doctrine\ORM\OptimisticLockException::class);
        $em2->flush();
    }
}
```

```php
// tests/Infrastructure/Doctrine/MoneyTypeTest.php
final class MoneyTypeTest extends TestCase
{
    private MoneyType $type;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new MoneyType();
        $this->platform = new PostgreSQLPlatform();
    }

    public function test_serialize_deserialize_roundtrip(): void
    {
        $money = Money::rub(10500);

        $serialized = $this->type->convertToDatabaseValue($money, $this->platform);
        $this->assertSame('10500 RUB', $serialized);

        $deserialized = $this->type->convertToPHPValue($serialized, $this->platform);
        $this->assertTrue($deserialized->equals($money));
    }

    public function test_null_roundtrip(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function test_invalid_format_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->type->convertToPHPValue('garbage', $this->platform);
    }
}
```

### Время выполнения

```bash
$ vendor/bin/phpunit tests/Infrastructure/
OK (47 tests, 156 assertions)
Time: 8.3 seconds
```

**Почему 8 с, а не 0.5 с:** реальная БД, `TRUNCATE`, `flush`, `commit`. Но **всё ещё** быстрее, чем ручной QA.

---

## Кольцо 4: Interface — API / E2E Tests

### Что тестируем

- HTTP контракт: `POST /orders` → `201` + JSON.
- Валидация формы: некорректный email → `422`.
- Авторизация: без токена → `401`, с токеном без роли → `403`.
- End-to-end: `POST /orders` → `POST /orders/:id/confirm` → `GET /orders/:id` → статус `confirmed`.

### Что мокаем

**Внешние сервисы** (Stripe, SMTP, Elasticsearch). Не БД, не Doctrine, не Domain.

```php
// tests/Interface/Api/OrderApiTest.php
final class OrderApiTest extends WebTestCase
{
    private BrowserKit\Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_place_order_success(): void
    {
        // Авторизация (Symfony test auth)
        $this->client->loginUser(
            static::getContainer()->get(UserProviderInterface::class)->loadUserByIdentifier('admin@example.com')
        );

        $this->client->request('POST', '/api/v1/orders', [
            'email' => 'customer@example.com',
            'items' => [
                ['productId' => 1, 'qty' => 2],
                ['productId' => 2, 'qty' => 1],
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonEquals([
            'id'         => self::getType('integer'),
            'status'     => 'pending',
            'total'      => ['amount' => 13000, 'currency' => 'RUB'],
            'lines'      => self::getType('array'),
            'createdAt'  => self::getType('string'),
        ]);
    }

    public function test_place_order_invalid_email(): void
    {
        $this->client->request('POST', '/api/v1/orders', [
            'email' => 'not-an-email',
            'items' => [['productId' => 1, 'qty' => 1]],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonEquals([
            'errors' => [
                'email' => ['message' => self::getType('string')],
            ],
        ]);
    }

    public function test_confirm_order_requires_admin_role(): void
    {
        $this->client->loginUser(
            static::getContainer()->get(UserProviderInterface::class)->loadUserByIdentifier('customer@example.com')
        );

        $this->client->request('POST', '/api/v1/orders/1/confirm');

        self::assertResponseStatusCodeSame(403);
    }

    public function test_full_lifecycle(): void
    {
        // 1. Create
        $this->client->loginUser(/* admin */);
        $this->client->request('POST', '/api/v1/orders', [
            'email' => 'c@example.com',
            'items' => [['productId' => 1, 'qty' => 1]],
        ]);
        $id = $this->client->getResponseHeader('Location');

        // 2. Confirm
        $this->client->request('POST', "/api/v1/orders/$id/confirm");
        self::assertResponseStatusCodeSame(204);

        // 3. Verify
        $this->client->request('GET', "/api/v1/orders/$id");
        self::assertResponseStatusCodeSame(200);
        self::assertJsonEquals(['status' => 'confirmed']);
    }
}
```

### Что мокаем во внешнем

```php
// В тестовом окружении (config/services_test.yaml):
services:
    App\Infrastructure\Payment\StripeGateway:
        class: App\Tests\Mock\StripeGatewayMock   # ← всегда "успех"

    App\Infrastructure\Mailer\SymfonyMailerAdapter:
        class: App\Tests\Mock\CapturingMailer      # ← хранит письма в памяти
```

```php
// tests/Mock/CapturingMailer.php
final class CapturingMailer
{
    /** @var Email[] */
    public array $sent = [];

    public function send(Email $email): void
    {
        $this->sent[] = $email;
    }

    public function assertSent(int $count): void
    {
        assert(count($this->sent) === $count);
    }
}
```

### Время выполнения

```bash
$ vendor/bin/phpunit tests/Interface/
OK (12 tests, 48 assertions)
Time: 15.2 seconds
```

---

## Матрица: что мокаем, что нет

| Зависимость | Unit (Domain) | Integration (App) | Integration (Infra) | API (Interface) |
|---|---|---|---|---|
| `Order` (агрегат) | **Реальный** | **Реальный** | **Реальный** | **Реальный** |
| `Money`, `Email` (VO) | **Реальный** | **Реальный** | **Реальный** | **Реальный** |
| `OrderRepository` (interface) | — | **Mock** | **Реальный** (impl) | **Реальный** |
| `UnitOfWork` (port) | — | **Mock** | **Реальный** (Doctrine) | **Реальный** |
| `EventPublisher` (port) | — | **Mock** | **Реальный** | **Реальный** |
| `EntityManager` (Doctrine) | — | — | **Реальный** | **Реальный** |
| `StripeGateway` | — | — | — | **Mock** |
| `Mailer` | — | — | — | **Mock** (Capturing) |
| `Elasticsearch` | — | — | — | **Mock** |
| `Request`, `Response` (HTTP) | — | — | — | **Реальный** (BrowserKit) |
| `Symfony DI` | — | — | **Реальный** | **Реальный** |

**Правило:**
- **Domain** — никогда не мокаем. Он сам по себе.
- **Порты** (Application interfaces) — мокаем в Application tests.
- **Реализации портов** (Infrastructure) — **не** мокаем. Тестируем на реальной БД.
- **Внешние сервисы** (Stripe, SMTP) — мокаем всегда.

---

## Как держать тесты быстрыми

### 1. Параллелизм

```bash
# PHPUnit с параллельным запуском (PHP 8.1+, paratest)
vendor/bin/paratest --processes=4 --group=unit

# Или: PHPUnit 10+ (встроенный parallel)
vendor/bin/phpunit --parallel
```

```xml
<!-- phpunit.xml -->
<phpunit parallel="4">
    <!-- 4 процесса × 500 тестов = ~1 с вместо ~4 с -->
</phpunit>
```

### 2. Изоляция слоёв

```bash
# Быстрый прогон (локальная dev): только Domain + Application (без БД)
vendor/bin/phpunit tests/Domain/ tests/Application/
# → ~2 с

# Средний прогон (CI): + Infrastructure
vendor/bin/phpunit tests/Domain/ tests/Application/ tests/Infrastructure/
# → ~10 с

# Полный прогон (release): + Interface
vendor/bin/phpunit
# → ~30 с
```

### 3. Транзакция + rollback (для Integration)

```php
protected function setUp(): void
{
    static::bootKernel(['environment' => 'test']);
    $this->em = static::getContainer()->get('doctrine')->getManager();
    $this->em->getConnection()->beginTransaction();
}

protected function tearDown(): void
{
    $this->em->getConnection()->rollBack();
    $this->em->close();
}
```

**Почему быстрее, чем `TRUNCATE`:** один `BEGIN`/`ROLLBACK` — O(1). `TRUNCATE` на 5 таблицах — O(n). Для 47 тестов: 47 × rollback ≈ 1 с. 47 × TRUNCATE ≈ 8 с.

### 4. Фикстуры, а не seed-скрипты

```php
// tests/Fixture/OrderFixture.php
final class OrderFixture
{
    public static function pending(int $lines = 1): Order
    {
        $order = new Order('fixture@example.com');
        for ($i = 0; $i < $lines; $i++) {
            $order->addLine(new ProductId(100 + $i), 1, Money::rub(1000));
        }
        return $order;
    }

    public static function confirmed(): Order
    {
        $order = self::pending(2);
        $order->confirm();
        return $order;
    }

    public static function delivered(): Order
    {
        $order = self::confirmed();
        $order->ship();
        $order->deliver();
        return $order;
    }
}
```

```php
// В тесте:
$order = OrderFixture::confirmed();
$this->expectException(StateException::class);
$order->cancel('reason');   // delivered → нельзя
```

**Почему лучше, чем seed-скрипт:**
- Детерминировано. Всегда один и тот же объект.
- Не зависит от БД.
- Быстро. `new` + `addLine` = микросекунды.
- Видно **что** создаётся (читаемый код).

### 5. Не тестируйте то, что не может сломаться

```php
// НЕ нужно:
public function test_get_id_returns_id(): void
{
    $order = new Order('a@b.c');
    $order->setId(42);
    $this->assertSame(42, $order->getId());
}

// Зачем? Это getter. Не сломается.
```

```php
// НУЖНО:
public function test_add_line_recalculates_total(): void
{
    $order = new Order('a@b.c');
    $order->addLine(new ProductId(1), 2, Money::rub(5000));
    $order->addLine(new ProductId(2), 1, Money::rub(3000));
    $this->assertTrue($order->getTotal()->equals(Money::rub(13000)));
}

// Почему? Инвариант total = Σ(lines) может сломаться при рефакторинге.
```

**Правило:** тестируйте **поведение** (инварианты, transitions, edge cases), не **механику** (getters, setters, toString).

### 6. Cache-и для CI

```yaml
# .github/workflows/ci.yml
- name: Cache Composer
  uses: actions/cache@v4
  with:
    path: vendor/
    key: composer-${{ hashFiles('composer.lock') }}

- name: Cache PHPStan
  uses: actions/cache@v4
  with:
    path: .phpstan.cache
    key: phpstan-${{ hashFiles('phpstan.neon') }}
```

```bash
# CI-пошагово:
composer install --no-interaction --prefer-dist
vendor/bin/phpstan analyse            # ~5 с (с кэшем)
vendor/bin/phpunit tests/Domain/     # ~1 с
vendor/bin/phpunit tests/Application/ # ~1 с
vendor/bin/phpunit tests/Infrastructure/  # ~8 с
vendor/bin/phpunit tests/Interface/  # ~15 с
# Итого: ~30 с
```

---

## Как держать тесты стабильными

### 1. Детерминизм

| Источник нестабильности | Решение |
|---|---|
| `new \DateTimeImmutable()` в домене | Порт `Clock`. В тесте — фиксированная дата. |
| `rand()`, `uniqid()` | Порт `IdGenerator`. В тесте — последовательный. |
| `time()` в логах | Порт `Clock`. |
| Порядок элементов в массиве | `sort` перед assert. Или `assertCount` вместо `assertContains`. |
| Float-сравнение | `assertEqualsWithDelta(10.5, $result, 0.01)`. Не `assertSame`. |

```php
// Порт Clock
interface Clock {
    public function now(): \DateTimeImmutable;
}

// В тесте:
$clock = new class implements Clock {
    public function now(): \DateTimeImmutable {
        return new \DateTimeImmutable('2025-01-15T10:00:00Z');
    }
};
```

### 2. Изоляция тестов

```php
// ПЛОХО: тесты зависят от порядка
public function test_a(): void { /* создаёт Order#1 */ }
public function test_b(): void { /* ожидает Order#1 из test_a */ }

// ХОРОШО: каждый тест сам себя создаёт
public function test_a(): void
{
    $order = OrderFixture::pending();
    // …
}

public function test_b(): void
{
    $order = OrderFixture::confirmed();
    // …
}
```

**Правило:** каждый тест начинается с чистого состояния и заканчивает в чистом состоянии. `setUp` — создаёт. `tearDown` — очищает. Никаких «остатков» от предыдущего теста.

### 3. Нет sleep()

```php
// ПЛОХО:
$this->client->request('POST', '/api/v1/orders/…');
sleep(2);   // ← «дайте времени обработчику отработать»
$this->assertDatabaseHas('emails', …);

// ХОРОШЕЕ:
$this->client->request('POST', '/api/v1/orders/…');
// Если обработчик async — проверяем outbox, не результат
self::assertDatabaseHas('outbox', ['event_type' => OrderConfirmed::class]);
```

**Почему:** `sleep` — это race condition. Если тест прошёл за 2 с, не значит, что обработчик успел. На медленной CI — не успел. На быстрой — успел, но `sleep(2)` — 2 с мёртвого времени.

### 4. Нет внешних зависимостей

```php
// ПЛОХО:
public function test_stripe_charge(): void
{
    $stripe = new \Stripe\StripeClient('sk_test_…');
    $stripe->charges->create(…);   // ← реальный HTTP
}

// ХОРОШЕЕ:
public function test_stripe_charge(): void
{
    $mock = $this->createMock(StripeGateway::class);
    $mock->method('charge')->willReturn(new ChargeResult('ok'));
    // …
}
```

**Правило:** в тестах **ни один** HTTP-запрос не уходит наружу. Stripe, SMTP, Elasticsearch — все мокаются.

### 5. Fail-fast

```php
// ПЛОХО: 50 assert'ов. 49-й падает. Нужно докрутить до 49-го.
// ХОРОШЕЕ: 5 assert'ов. Каждый — отдельный смысл.

public function test_place_order(): void
{
    $response = $this->client->request('POST', '/api/v1/orders', [/* … */]);

    self::assertResponseStatusCodeSame(201);          // ← 1: код
    $body = json_decode($response->getContent(), true);

    self::assertSame('pending', $body['status']);      // ← 2: статус
    self::assertSame(13000, $body['total']['amount']); // ← 3: сумма
    self::assertCount(2, $body['lines']);              // ← 4: линии
}
```

### 6. Разделение по группам (groups)

```xml
<!-- phpunit.xml -->
<phpunit>
    <groups>
        <group name="fast">
            <include>
                <directory>tests/Domain</directory>
                <directory>tests/Application</directory>
            </include>
        </group>
        <group name="integration">
            <include>
                <directory>tests/Infrastructure</directory>
            </include>
        </group>
        <group name="e2e">
            <include>
                <directory>tests/Interface</directory>
            </include>
        </group>
    </groups>
</phpunit>
```

```php
// tests/Domain/OrderTest.php
final class OrderTest extends TestCase
{
    /**
     * @group fast
     */
    public function test_confirm(): void { /* … */ }

    /**
     * @group integration
     */
    public function test_confirm_with_db(): void { /* … */ }  // ← если есть

    /**
     * @group e2e
     */
    public function test_confirm_api(): void { /* … */ }  // ← если есть
}
```

```bash
# Быстрый прогон (после каждого commit):
vendor/bin/phpunit --group=fast
# → ~2 с

# Средний прогон (CI):
vendor/bin/phpunit --group=fast --group=integration
# → ~12 с

# Полный прогон (release):
vendor/bin/phpunit
# → ~30 с
```

---

## CI-пайплайн

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]

jobs:
  static-analysis:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, intl, mbstring
      - run: composer install
      - run: vendor/bin/phpstan analyse
      - run: vendor/bin/psalm --no-cache

  unit-tests:
    runs-on: ubuntu-latest
    needs: static-analysis
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: vendor/bin/phpunit --group=fast --parallel
      # → ~2 с

  integration-tests:
    runs-on: ubuntu-latest
    needs: unit-tests
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: test
        ports: ['5432:5432']
        options: >-
          --health-cmd="pg_isready -U test"
          --health-interval=5s
          --health-timeout=5s
          --health-retries=5
    env:
      DATABASE_URL: 'postgresql://test:test@localhost:5432/test?serverVersion=16'
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: php bin/console doctrine:migrations:migrate --no-interaction
      - run: vendor/bin/phpunit --group=integration
      # → ~10 с

  e2e-tests:
    runs-on: ubuntu-latest
    needs: integration-tests
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: test
        ports: ['5432:5432']
    env:
      DATABASE_URL: 'postgresql://test:test@localhost:5432/test?serverVersion=16'
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: php bin/console doctrine:migrations:migrate --no-interaction
      - run: vendor/bin/phpunit --group=e2e
      # → ~15 с

  # Итого CI: ~2 + ~10 + ~15 + overhead ≈ 35 с
```

---

## Итоговая таблица

| Кольцо | Тип теста | БД? | Mock? | Кол-во | Время | Запускается |
|---|---|---|---|---|---|---|
| **Domain** | Unit | Нет | Нет | 500–2000 | 1–2 с | Каждый commit |
| **Application** | Unit (с mock портов) | Нет | Порты | 50–150 | 1–2 с | Каждый commit |
| **Infrastructure** | Integration | **Да** | Нет | 30–80 | 5–10 с | CI |
| **Interface** | E2E / API | **Да** | Внешние сервисы | 10–30 | 10–20 с | CI / release |

**Правила:**

1. **Domain тестируется без БД.** Если не можете — архитектура нарушена.
2. **Порты мокаются. Реализации — нет.**
3. **Внешние сервисы всегда мокаются.**
4. **Каждый тест сам себя создаёт и очищает.**
5. **Нет `sleep()`.**
6. **Fast → Integration → E2E.** Быстрые падают первыми.
7. **CI: 35 секунд.** Не 5 минут. Если больше — что-то не так.

> ⏱ 80.5s · prompt 104475 tok · output 9915 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

Пользователь просит разобрать проблему конкурентных модификаций (concurrent modifications) в БД. Это классическая задача на optimistic/pessimistic locking, race conditions, lost updates. 

Рассмотрю:
1. Какие проблемы возникают при concurrent writes (lost update, dirty read, non-repeatable read, phantom, over-selling, double-spend)
2. Способы защиты: optimistic locking (version), pessimistic locking (FOR UPDATE), advisory locks, unique constraints, CHECK constraints, transaction isolation levels, idempotency, SAGA
3. Сравнение подходов с конкретными trade-offs
4. Когда какой подход выбираю

Структурирую ответ так:
- Сценарий + что происходит без защиты
- Каждый механизм: как работает, код, trade-offs
- Сравнительная таблица
- Практические правила выбора
- Антипаттерны

Буду конкретен, с кодом PHP/PostgreSQL, без воды.

</details>

**Кандидат:**

## Сценарий: два процесса пишут в одну строку

```
t1:  Process A: SELECT balance FROM accounts WHERE id = 1  → 100
t2:  Process B: SELECT balance FROM accounts WHERE id = 1  → 100
t3:  Process A: UPDATE accounts SET balance = 80 WHERE id = 1   (100 - 20)
t4:  Process B: UPDATE accounts SET balance = 90 WHERE id = 1   (100 - 10)
```

**Ожидалось:** 100 - 20 - 10 = 70. **Получили:** 90. 20 единиц потеряны.

Это **lost update**. И это только одна из проблем.

---

## Полный список проблем

| Проблема | Что происходит | Пример |
|---|---|---|
| **Lost update** | Оба пишут, последний «перетирает» первого | Баланс: 100 → 80 (A) → 90 (B). Потеряны 20. |
| **Dirty read** | Читаем неаккомитченные данные другого процесса | A: BEGIN, UPDATE balance=50. B: SELECT → видит 50. A: ROLLBACK. B: увидел то, чего не было. |
| **Non-repeatable read** | Два раза читаем одну строку — получаем разные значения | B: SELECT balance → 100. A: UPDATE balance=80, COMMIT. B: SELECT balance → 80. |
| **Phantom read** | Между двумя SELECT'ами появились/исчезли строки | B: SELECT count(*) → 5. A: INSERT. B: SELECT count(*) → 6. |
| **Over-selling** | Оба списывают сток, кто-то из них не должен был | Stock=5. A: buy 3. B: buy 3. Оба прошли проверку stock>=3. Продано 6 из 5. |
| **Double-spend** | Две транзакции списывают один и тот же баланс | Баланс=100. A: spend 80. B: spend 80. Оба прошли проверку balance>=80. Списано 160 из 100. |
| **Deadlock** | A ждёт B, B ждёт A | A: LOCK row1 → хочет row2. B: LOCK row2 → хочет row1. Оба зависли. |

В PostgreSQL (MVCC) dirty read **невозможен** по умолчанию. Non-repeatable и phantom — возможны на уровне `READ COMMITTED` (default). Lost update и over-selling — **всегда возможны** без явной защиты.

---

## Механизмы защиты

### 1. Optimistic Locking (version column)

**Идея:** не блокируем. Проверяем при записи, что никто не изменил данные с момента нашего чтения.

```sql
ALTER TABLE accounts ADD COLUMN version INTEGER NOT NULL DEFAULT 0;
```

```sql
UPDATE accounts
SET balance = 80, version = version + 1
WHERE id = 1 AND version = 3;   -- ← версия, которую я прочитал

-- Если rowCount = 0 → кто-то изменил раньше → конфликт
```

**В Doctrine:**

```php
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $balance;

    #[ORM\Version]              // ← Doctrine сам добавляет AND version = …
    #[ORM\Column(type: Types::INTEGER)]
    private int $version;
}
```

**В application layer:**

```php
public function transfer(int $fromId, int $toId, int $amount): void
{
    $from = $this->repo->find($fromId);
    $to   = $this->repo->find($toId);

    $from->debit($amount);   // доменная проверка: balance >= amount
    $to->credit($amount);

    try {
        $this->uow->wrapInTransaction(function () use ($from, $to) {
            $this->repo->save($from);
            $this->repo->save($to);
        });
    } catch (OptimisticLockException $e) {
        throw new ConcurrencyConflictException(
            "Account #$fromId was modified concurrently",
        ) from $e;
    }
}
```

**Что происходит при конфликте:**

```
t1:  A: SELECT balance=100, version=3
t2:  B: SELECT balance=100, version=3
t3:  A: UPDATE … AND version=3  → 1 row, version=4  ✓
t4:  B: UPDATE … AND version=3  → 0 rows  ← КОНФЛИКТ
t5:  B: OptimisticLockException → 409 / retry
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Нет блокировок. Высокая пропускная способность. | Нужно обрабатывать `OptimisticLockException`. |
| Не держит ресурсы. Нет deadlock'ов. | При частых конфликтах — много retry'ев. |
| Простая реализация (`#[ORM\Version]`). | Не защищает от lost update при **одновременных SELECT'ах** без version. |
| | Нужно добавлять `version` в каждую таблицу, где есть конкурентные writes. |

---

### 2. Pessimistic Locking (SELECT … FOR UPDATE)

**Идея:** блокируем строку на время транзакции. Второй процесс **ждёт**.

```sql
BEGIN;
SELECT balance FROM accounts WHERE id = 1 FOR UPDATE;  -- ← блокировка
-- … логика …
UPDATE accounts SET balance = balance - 20 WHERE id = 1;
COMMIT;   -- ← разблокировка
```

**В Doctrine:**

```php
use Doctrine\ORM\LockMode;

$account = $this->em->createQuery(
    'SELECT a FROM Account a WHERE a.id = :id'
)
->setLockMode(LockMode::PESSIMISTIC_WRITE)
->setParameter('id', 1)
->getOneOrNullResult();
// ← Второй процесс с PESSIMISTIC_WRITE на той же строке ЗАМОРОЗИТСЯ
```

**Что происходит при конфликте:**

```
t1:  A: SELECT … FOR UPDATE WHERE id = 1  → БЛОКИРОВКА
t2:  B: SELECT … FOR UPDATE WHERE id = 1  → ЖДЁТ
t3:  A: UPDATE … → COMMIT → разблокировка
t4:  B: SELECT … → получает актуальные данные (balance=80)
t5:  B: debit(20) → balance = 60  ← КОРРЕКТНО
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Второй процесс видит **актуальные** данные. Нет retry. | Блокировка. Второй процесс **ждёт**. |
| Простая семантика: «заблокировал → читал → записал → отпустил». | Deadlock'ы: A ждёт B, B ждёт A. |
| | При 100 одновременных транзакциях — 100 блокировок = serialization. |
| | Длительная транзакция = длительная блокировка. |
| | Внешние вызовы (HTTP) в транзакции с FOR UPDATE = катастрофа. |

---

### 3. Atomic UPDATE (без SELECT)

**Идея:** не «читаем → проверяем → пишем». А **сразу** пишем с условием.

```sql
UPDATE accounts
SET balance = balance - 20
WHERE id = 1
  AND balance >= 20;   -- ← условие прямо в UPDATE

-- Если rowCount = 0 → balance < 20 → не хватило
```

**В PHP:**

```php
public function debit(int $accountId, int $amount): void
{
    $rowCount = $this->em->getConnection()->executeStatement(
        'UPDATE accounts SET balance = balance - :amount,
                              version = version + 1
         WHERE id = :id
           AND balance >= :amount',
        ['amount' => $amount, 'id' => $accountId]
    );

    if ($rowCount === 0) {
        throw new InsufficientBalanceException($accountId, $amount);
    }
}
```

**Что происходит при конфликте:**

```
t1:  A: UPDATE … SET balance = balance - 20 WHERE balance >= 20  → 1 row (100→80)
t2:  B: UPDATE … SET balance = balance - 20 WHERE balance >= 20  → 1 row (80→60)
→ КОРРЕКТНО. БД гарантирует атомарность каждой UPDATE.
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| **Один** запрос. Нет SELECT → нет race window. | Невозможно «прочитать» состояние до изменения. |
| Нет lost update. БД гарантирует. | Сложные условия (несколько полей) → громоздкий UPDATE. |
| Нет блокировок (кроме самой UPDATE). | Нужно проверять `rowCount`. |
| Идеально для «списать/добавить». | Не подходит для операций, где нужно **прочитать** состояние (например, проверить несколько условий). |

---

### 4. Advisory Lock (PostgreSQL)

**Идея:** явная блокировка по **бизнес-ключу**, не привязанная к строке.

```sql
BEGIN;
SELECT pg_advisory_xact_lock(hashtext('account_' || 1));
-- … логика …
COMMIT;   -- авто-разблокировка
```

**В PHP:**

```php
public function transfer(int $fromId, int $toId, int $amount): void
{
    $this->uow->wrapInTransaction(function () use ($fromId, $toId, $amount) {
        // Блокировка по бизнес-ключу (не по строке)
        $this->em->executeStatement(
            'SELECT pg_advisory_xact_lock(hashtext(:key))',
            ['key' => 'account_' . min($fromId, $toId) . '_' . max($fromId, $toId)]
        );

        $from = $this->repo->find($fromId);
        $to   = $this->repo->find($toId);

        $from->debit($amount);
        $to->credit($amount);

        $this->repo->save($from);
        $this->repo->save($to);
    });
    // COMMIT → авто-разблокировка
}
```

**Когда нужен:**

- Блокировка по **бизнес-ключу**, а не по строке.
- Пример: «только один процесс может обработать вебхук `event_id = X`» (idempotency).
- Пример: «только один процесс может пересчитать бонусы для юзера `#42`».

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Блокирует по **ключу**, не по PK. | Только PostgreSQL. |
| Авто-разблокировка при COMMIT. | Если транзакция зависла → блокировка висит. |
| Не нужно `version` в таблице. | Не видно в `pg_locks` как «таблица + строка». |
| | Коллизии `hashtext()` теоретически возможны (крайне редко). |

---

### 5. Transaction Isolation Levels

**Идея:** контролировать, какие неаккомитченные данные видит процесс.

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

| Уровень | Dirty read | Non-repeatable | Phantom | Когда |
|---|---|---|---|---|
| `READ UNCOMMITTED` | Да | Да | Да | Почти никогда |
| `READ COMMITTED` (default PG) | Нет | Да | Да | 95% случаев |
| `REPEATABLE READ` | Нет | Нет | Да (в MySQL) / Нет (в PG, MVCC) | Долгие read-only |
| `SERIALIZABLE` | Нет | Нет | Нет | Финансы, инвентарь |

**`SERIALIZABLE` в PostgreSQL:**

```php
$this->em->getConnection()->executeStatement(
    'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE'
);
$this->uow->wrapInTransaction(function () {
    // …
});
// При конфликте: PostgreSQL бросает 40001 (serialization_failure)
// Нужно catch + retry
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Полный serializable → «как будто» последовательность. | Deadlock retries (40001). |
| Не нужно вручную ставить блокировки. | Пропускная способность падает. |
| | Не спасает от lost update при `READ COMMITTED` (default). |

---

### 6. Unique Constraints / CHECK

**Идея:** БД **запрещает** невалидные состояния.

```sql
ALTER TABLE accounts ADD CONSTRAINT balance_non_negative CHECK (balance >= 0);
```

```
t1:  A: UPDATE balance = 80  → 80 >= 0  ✓
t2:  B: UPDATE balance = 90  → 90 >= 0  ✓
→ Lost update, но невалидное состояние НЕ получилось.
```

```sql
-- Для idempotency:
CREATE UNIQUE INDEX idx_payments_ref ON payments (reference_id);
```

```
t1:  A: INSERT payments (reference_id = 'ord_123')  ✓
t2:  B: INSERT payments (reference_id = 'ord_123')  → UNIQUE violation → откат
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Запрещает невалидные состояния **даже при raw SQL**. | Не защищает от lost update (только от невалидных значений). |
| Фаст-fail: ошибка сразу, не «молча переписал». | Сложные условия = трудночитаемый SQL. |
| | Изменение = миграция. |

---

### 7. Idempotency Key

**Идея:** операция **может** быть выполнена несколько раз, но результат — один.

```sql
CREATE TABLE operation_log (
    idempotency_key VARCHAR(255) PRIMARY KEY,
    operation       VARCHAR(100) NOT NULL,
    payload         JSONB        NOT NULL,
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT now()
);
```

```php
public function processPayment(string $idempotencyKey, Payment $payment): void
{
    $this->uow->wrapInTransaction(function () use ($idempotencyKey, $payment) {
        // 1. Проверка: уже обработано?
        $exists = $this->em->createQuery(
            'SELECT o.id FROM OperationLog o WHERE o.idempotencyKey = :key'
        )
        ->setParameter('key', $idempotencyKey)
        ->getOneOrNullResult();

        if ($exists) {
            return;   // ← уже обработано. Skip.
        }

        // 2. Обработка
        $this->paymentRepo->save($payment);

        // 3. Фиксация
        $log = new OperationLog($idempotencyKey, 'payment_processed');
        $this->em->persist($log);
    });
    // UNIQUE на idempotency_key → второй INSERT → violation → откат
}
```

**Когда:**

- Вебхуки (Stripe может отправить один и тот же event дважды).
- Retries (клиент не получил ответ → повторил запрос).
- Queues (message может быть доставлена дважды).

---

## Сравнительная таблица

| Механизм | Блокировка? | Lost update? | Deadlock? | Скорость | Когда |
|---|---|---|---|---|---|
| **Optimistic (version)** | Нет | Нет (конфликт → 409) | Нет | Высокая | Конфликты **редки** (< 1%) |
| **Pessimistic (FOR UPDATE)** | Да | Нет (второй видит актуальные) | **Да** | Ниже | Конфликты **часты**, операция **короткая** |
| **Atomic UPDATE** | Кратковременная (UPDATE) | Нет | Нет | Высокая | «Списать/добавить» |
| **Advisory lock** | Да (по ключу) | Нет | Нет (если один ключ) | Ниже | Блокировка по **бизнес-ключу** |
| **SERIALIZABLE** | Да (на уровне БД) | Нет | **Да** (retries) | Ниже | Финансы, критические операции |
| **UNIQUE / CHECK** | Кратковременная | Не защищает | Нет | Высокая | Запрет невалидных **значений** |
| **Idempotency key** | Нет | Не защищает | Нет | Высокая | Повторные операции (webhooks, retries) |

---

## Когда какой выбираю

### Правило 1: «Списать / добавить» → Atomic UPDATE

```php
// Баланс, сток, счётчик — одно поле, одна операция.
// Нет необходимости читать состояние до изменения.

$affected = $conn->executeStatement(
    'UPDATE accounts SET balance = balance - :amt, version = version + 1
     WHERE id = :id AND balance >= :amt',
    ['amt' => $amount, 'id' => $accountId]
);

if ($affected === 0) {
    throw new InsufficientBalanceException();
}
```

**Почему:** один запрос. Нет race window. Нет блокировок (кроме самой UPDATE). Идеально для 90% финансовых операций.

---

### Правило 2: «Сложная логика + конфликт редко» → Optimistic

```php
// Агрегат с несколькими полями, инвариантами, дочерними сущностями.
// Конфликт: два админа одновременно редактируют один заказ.

$order = $this->repo->find($id);
$order->changeAddress($newAddress);
$order->addDiscount($coupon);
$order->recalculateTotal();

try {
    $this->repo->save($order);   // → version check
} catch (OptimisticLockException) {
    throw new ConcurrencyConflictException();
}
```

**Почему:** агрегат — это **несколько** полей + инварианты. Atomic UPDATE не подходит (нельзя выразить «recalculateTotal» одним UPDATE). Pessimistic —过重, если конфликт редок.

---

### Правило 3: «Конфликт частый + операция короткая» → Pessimistic

```php
// Инвентарь: 1000 запросов/сек на 100 товаров.
// Каждый запрос: «списать 1 с товара #X».
// Конфликт: два запроса на один товар одновременно.

$this->uow->wrapInTransaction(function () use ($productId, $qty) {
    $product = $this->em->createQuery(
        'SELECT p FROM Product p WHERE p.id = :id'
    )
    ->setLockMode(LockMode::PESSIMISTIC_WRITE)
    ->setParameter('id', $productId)
    ->getOneOrNullResult();

    $product->decreaseStock($qty);
    $this->em->flush();
});
```

**Почему:** конфликт **частый** (1000 req/s на 100 товаров = 10 req/s на товар). Optimistic → много retry'ев. Atomic UPDATE → тоже работает, но если логика сложнее (проверка минимального стока, резервирование) — Pessimistic проще.

---

### Правило 4: «Внешний сервис + несколько шагов» → SAGA + Optimistic

```php
// Подтверждение заказа:
// 1. Резерв сток (БД)
// 2. Charge (Stripe, HTTP, 5 с)
// 3. Подтвердить заказ (БД)
// 4. Создать shipment (HTTP, 2 с)

// НЕ одна транзакция. 4 шага. Каждый — отдельный.

public function confirm(int $orderId): void
{
    // Шаг 1: резерв (короткая транзакция)
    $this->uow->wrapInTransaction(function () use ($orderId) {
        $order = $this->repo->find($orderId);
        $order->reserveStock();
        $this->repo->save($order);
    });

    // Шаг 2: charge (внешний, 5 с)
    $result = $this->stripe->charge($orderId);
    if (!$result->ok()) {
        $this->compensate($orderId);   // отменить резерв
        throw new PaymentFailedException();
    }

    // Шаг 3: подтвердить (короткая транзакция)
    $this->uow->wrapInTransaction(function () use ($orderId) {
        $order = $this->repo->find($orderId);
        $order->confirm();
        $this->repo->save($order);
    });

    // Шаг 4: shipment (внешний, 2 с)
    $this->shipping->create($orderId);
}
```

**Почему:** нельзя держать блокировку 7 секунд (5+2 с внешнего). Нельзя одну транзакцию на всё (deadlock, блокировки). Разбиваем на короткие транзакции + компенсация.

---

### Правило 5: «Повторная операция» → Idempotency Key

```php
// Stripe webhook может прийти дважды.
// Клиент может повторить запрос.

public function handleWebhook(string $eventId, WebhookPayload $payload): void
{
    $this->uow->wrapInTransaction(function () use ($eventId, $payload) {
        // UNIQUE на event_id → второй INSERT → violation → откат
        $log = new WebhookLog($eventId);
        $this->em->persist($log);

        // … обработка …
    });
}
```

---

### Правило 6: «Нельзя чтобы значение было невалидным» → CHECK / UNIQUE

```sql
CHECK (balance >= 0)
CHECK (qty >= 1)
UNIQUE (email)
UNIQUE (idempotency_key)
```

**Почему:** это **схема данных**, а не бизнес-логика. «Баланс не может быть отрицательным» — это инвариант схемы. Он должен быть в БД, даже если код «забыл» проверить.

---

## Комбинация механизмов

На практике используем **несколько** одновременно:

```
Агрегат Order:
  ├── Optimistic (#[ORM\Version])     ← конфликт при редактировании
  ├── CHECK (total >= 0)              ← невалидное значение
  ├── UNIQUE (order_number)           ← дубликаты
  ├── Atomic UPDATE (stock)           ← списание стока
  └── Idempotency (webhook event_id)  ← повторные webhooks

Агрегат Account:
  ├── Atomic UPDATE (balance)         ← списание/зачисление
  ├── CHECK (balance >= 0)            ← невалидное значение
  └── Advisory lock (bonus recalc)    ← один процесс за раз
```

**Слои защиты (от внешнего к внутреннему):**

```
1. Idempotency key       ← операция не повторится
2. CHECK / UNIQUE         ← значение не невалидное
3. Atomic UPDATE         ← списание атомарное
4. Optimistic / Pessimistic  ← конфликт обнаружен
5. SERIALIZABLE          ← (крайний случай) полная изоляция
6. Advisory lock         ← (специальный случай) блокировка по ключу
```

---

## Антипаттерны

| Что делают | Почему плохо | Правильно |
|---|---|---|
| `SELECT` → `sleep(1)` → `UPDATE` | Race window. Не «защита», а «надежда». | Atomic UPDATE или FOR UPDATE. |
| `SET TRANSACTION ISOLATION LEVEL SERIALIZABLE` на все запросы | Deadlock retries. Пропускная способность падает. | Только для критических операций. |
| Pessimistic lock + HTTP-вызов в транзакции | Блокировка 5–30 с. Serialization. | Разбить: транзакция → внешний вызов → транзакция. |
| Retry без перечитывания | Version не изменился → снова conflict. | После conflict — перечитать. |
| `version` в доменном классе | Домен знает про БД. | `#[ORM\Version]` — инфраструктурный механизм. |
| `SELECT … FOR UPDATE` + `SELECT … FOR UPDATE` на разных строках в разном порядке | Deadlock. | Единый порядок блокировок. |
| `UPDATE … WHERE id = 1` без проверки `rowCount` | Молчаливая потеря обновления. | Проверить `rowCount`. |
| Advisory lock без `xact` | Блокировка висит вечно при crash. | `pg_advisory_xact_lock`. |

---

## Итого: чек-лист

```
1. Операция — «списать/добавить» одно поле?
   → Atomic UPDATE + CHECK.

2. Операция — «изменить агрегат» (несколько полей, инварианты)?
   → Optimistic (#[ORM\Version]) + retry/409.

3. Конфликт частый (> 10% запросов)?
   → Pessimistic (FOR UPDATE). Короткая транзакция. Без внешних вызовов.

4. Несколько шагов + внешний сервис?
   → SAGA: короткие транзакции + компенсация. Optimistic на каждом шаге.

5. Операция может прийти дважды?
   → Idempotency key + UNIQUE.

6. Значение не должно быть невалидным?
   → CHECK / UNIQUE в БД. Всегда.

7. Блокировка по бизнес-ключу (не по строке)?
   → Advisory lock.

8. Финансовая операция, «не может быть ни одной ошибки»?
   → SERIALIZABLE + retry. (Дорого. Только когда нужно.)
```

**Главный принцип:** не «один механизм на всё». А **комбинация**, где каждый механизм решает свою задачу. Atomic UPDATE — для списания. Optimistic — для агрегатов. Idempotency — для повторных операций. CHECK — для схемы. Advisory lock — для бизнес-ключа. Все вместе — система, где ни одна точка отказа не приводит к потере данных.

> ⏱ 59.2s · prompt 114229 tok · output 6417 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

Пользователь просит описать мой (как опытного разработчика) git-процесс от постановки задачи до продакшена. Это большой вопрос, который нужно разбить на логические части:

1. Постановка задачи → ветка
2. Формат коммитов
3. Pull Request / Merge Request
4. Ревью
5. Релизы (release process)
6. Продакшен

Нужно дать конкретный процесс, а не абстрактные советы. С объяснением "почему так, а не иначе" — то есть обоснованием каждого выбора.

Я должен быть честным: у меня нет "своего процесса" в буквальном смысле, но я могу описать процесс, который я бы рекомендовал как опытный разработчик, с обоснованием каждого решения.

Структура:
1. Ветвление — почему feature-branches, а не trunk-based или git-flow
2. Формат коммитов — conventional commits, почему
3. PR — что в него входит, почему
4. Ревью — как, что проверяем, почему
5. Релизы — как, почему
6. Продакшен — деплой, rollback, мониторинг

Дам конкретные примеры, а не абстракции. Буду честен про trade-offs.

</details>

**Кандидат:**

## Честная оговорка

У меня нет «своего процесса». Ниже — процесс, который я бы рекомендовал команде из 3–15 разработчиков, работающей над Symfony-приложением с PostgreSQL. Каждый выбор обоснован trade-off. В конце — что я бы **изменил** при другой команде.

---

## 1. Ветвление: feature-branches + short-lived branches

### Схема

```
main ──────────────────────────────────────────────────── → (prod)
  │
  ├── feature/ORD-142-confirm-order ──→ PR ──→ merge ──→ main
  │
  ├── feature/ORD-143-bonus-points ───→ PR ──→ merge ──→ main
  │
  ├── fix/ORD-151-npe-in-order-line ──→ PR ──→ merge ──→ main
  │
  └── chore/upgrade-doctrine-to-2.18 ─→ PR ──→ merge ──→ main
```

**Нет** `develop`, `release/*`, `hotfix/*`. Только `main` + feature-ветки.

### Почему не Git Flow

Git Flow (`develop` → `release` → `main`) — это процесс для **команды из 30+ человек**, где релизы — это **событие** (раз в 2 недели), а не **поток**. Для команды из 3–15 человек:

| Git Flow | Feature-branches + main |
|---|---|
| 3 долгоживущие ветки | Ветка живёт 1–3 дня |
| `develop` → `release` → `main` — 3 merge'а на релиз | 1 merge в `main` |
| Hotfix-ветка из `main`, потом backport в `develop` | Hotfix — ветка из `main`, merge в `main`. Конец. |
| `develop` отстаёт от `main` на 2 недели | `main` всегда актуален |

**Trade-off:** feature-branches не идеальны. `main` может быть в «ломаном» состоянии 30 минут (между merge и деплоем). Для команды, где деплой — 5 минут, это не проблема. Для команды, где деплой — 2 часа (approval chain, canary), это проблема — и тогда нужен `release`-ветка.

### Правила веток

```
feature/<ticket>-<short-description>
fix/<ticket>-<short-description>
chore/<ticket>-<short-description>
refactor/<ticket>-<short-description>
docs/<ticket>-<short-description>
```

- **Короткие.** `feature/ORD-142-confirm-order`, не `feature/ORD-142-confirm-order-with-bonus-and-inventory-check-and-email-notification`.
- **Одна задача = одна ветка.** Не `feature/ORD-142-and-ORD-143-and-ORD-147`.
- **Живут 1–3 дня.** Если ветка живёт больше недели — она слишком большая. Разбить.
- **Не мерджаем `main` в ветку.** Ветка создается из актуального `main`, разрабатывается, мерджится. Не «переживёт» неделю.

### Почему не trunk-based

Trunk-based (все коммитят в `main`, feature flags) — лучше для **больших команд** (20+), где ветки живут 3–5 дней и конфликтуют. Для 3–15 человек:

- Feature flag на «отменить заказ» — это 20 строк кода + 20 строк тестов + 20 строк UI, которые **никогда не переключат**.
- Trunk-based требует **материи** в тестировании (каждый коммит в `main` должен быть deployable). Для 3–15 человек это overhead.
- Feature-ветка = **граница контекста**. Ревьюер видит **одну** задачу, не 5.

**Когда переходил бы на trunk-based:** 20+ разработчиков, 50+ коммитов/день, CI > 10 мин. Тогда feature flags и trunk-based оправданы.

---

## 2. Формат коммитов: Conventional Commits (с оговорками)

### Формат

```
<type>(<scope>): <subject>

[optional body]

[optional footer]
```

### Примеры

```
feat(order): add confirm endpoint

POST /orders/{id}/confirm
- Validates order is in pending status
- Reserves stock atomically
- Publishes OrderConfirmed event

Closes #ORD-142
```

```
fix(order): recalculate total after removing line

Previously total was not recalculated when a line
was removed from a pending order.

Fixes #ORD-151
```

```
refactor(payment): extract PaymentGateway interface

Move Stripe-specific code to StripeGateway implementation.
Domain now depends on the interface, not the concrete class.
```

```
chore(deps): upgrade doctrine/orm 2.17 → 2.18

- BREAKING: ORM\Query::getSingleScalarResult() now throws
  NoResultException instead of returning null
- Updated 3 query builders in OrderRepository
```

### Типы

| Тип | Когда |
|---|---|
| `feat` | Новая функциональность |
| `fix` | Исправление бага |
| `refactor` | Изменение без изменения поведения |
| `perf` | Улучшение производительности |
| `test` | Только тесты |
| `docs` | Только документация |
| `chore` | CI, deps, конфиг, миграции |
| `style` | Форматирование, без изменения логики |

### Scope

```
feat(order): …       ← модуль/агрегат
fix(payment): …      ← модуль
chore(deps): …       ← категория
refactor(orm): …     ← технология
```

### Почему Conventional Commits

1. **`git log --oneline` читается.** Не «fix», «update», «wip». А `feat(order): add confirm endpoint`.

2. **CHANGELOG генерируется.** `conventional-changelog` собирает CHANGELOG.md из коммитов. Не руками.

3. **Semantic versioning.** `feat` → minor bump. `fix` → patch bump. `BREAKING CHANGE` в body → major bump. Автоматически.

4. **Ревью.** Ревьюер видит **тип** до того, как начнёт читать diff. `refactor(payment): extract interface` — я знаю, что поведение не меняется. `feat(order): add confirm` — я знаю, что есть новый endpoint, и буду читать внимательнее.

### Оговорки

**Не гонюсь за идеальным форматом.** Если разработчик напишет `feat(order): add confirm` вместо `feat: add order confirm endpoint` — я не отклоняю PR. Формат — **помощь**, не **религия**.

**Не пишу body для каждого коммита.** Для 3-строчного фикса — subject достаточен. Для рефакторинга, который трогает 15 файлов — body обязателен: **почему**, а не **что**.

```
# ПЛОХО:
chore: update some stuff

# ХОРОШО:
chore(deps): upgrade symfony/symfony 6.4 → 7.0

- Symfony 7 removed the "annotation" autowire alias
  (replaced by "attribute" since 6.4)
- Updated 12 controller annotations to attributes
- Updated 3 form type definitions
```

**Не переделываю чужие коммиты.** Если разработчик написал 5 коммитов «wip», «fix», «fix again», «update» — я не требую `git rebase -i`. Я требую, чтобы **PR** имел хороший заголовок и описание. Внутренние коммиты — личное дело.

### Почему не «free-form» коммиты

```
# ПЛОХО:
- fix
- update
- wip
- fix again
- done
- final fix
- merge develop
```

Проблемы:
- `git log` бесполезен. Не понимаешь, что изменилось.
- `git bisect` работает, но ты не знаешь, **какой** коммит сломал.
- CHANGELOG не генерируется.
- Ревьюер не может отфильтровать: «покажи мне все `fix(order)` за неделю».

---

## 3. Pull Request: что в него входит

### Заголовок

```
feat(order): add confirm endpoint
```

**Не** `«Заказ: подтверждение»`. **Не** `«Fix»`. Заголовок = **описание изменения**, не название задачи.

### Описание (шаблон)

```markdown
## Что

Добавлен endpoint POST /orders/{id}/confirm.
Подтверждает заказ в статусе pending: резервирует сток,
списывает оплату, публикует OrderConfirmed.

## Почему

Тикет ORD-142. Заказ не мог быть подтверждён —
был только pending и cancelled.

## Как проверить

1. Создать заказ: POST /orders
2. Подтвердить: POST /orders/{id}/confirm
3. Проверить статус: GET /orders/{id} → "confirmed"
4. Проверить сток: GET /products/{id} → stock уменьшен
5. Проверить событие: SELECT * FROM outbox WHERE event_type = 'OrderConfirmed'

## Что НЕ покрыто

- Отмена подтверждённого заказа (ORD-145, следующая итерация)
- Повторное подтверждение (idempotency будет в ORD-148)

## Скриншоты / логи

(curl)
```

### Что **обязательно** в PR

| Элемент | Почему |
|---|---|
| **Что изменилось** (1–3 предложения) | Ревьюер не должен читать 200 строк diff, чтобы понять суть. |
| **Почему** (ссылка на тикет/баг) | Без «почему» ревьюер не может сказать «а нужно ли это вообще?». |
| **Как проверить** | Ревьюер может **сам** проверить. Не «доверься мне». |
| **Что НЕ покрыто** | Граница. Ревьюер знает, что он **не должен** проверять. |
| **Скриншоты / curl / логи** | Для API — curl. Для UI — скриншот. Для фоновых задач — лог воркера. |

### Что **не** в PR

| Элемент | Почему нет |
|---|---|
| «Пожалуйста, посмотрите» | Формулировка. Не информация. |
| «Знаю, что это не идеально, но…» | Апология. Если не идеально — не мержите. |
| «Сделал как ты просил на стендапе» | Ссылка на устное обсуждение. Если решение — в PR. |
| Скриншот «до» и «после» для рефакторинга | Рефакторинг не меняет поведение. Скриншоты — только для UI/API изменений. |

### Размер PR

**Правило:** < 400 строк diff (включая тесты). < 15 файлов. < 1 функциональность.

| Размер | Статус |
|---|---|
| < 100 строк | Идеально. 10–15 мин ревью. |
| 100–400 строк | Нормально. 30–60 мин ревью. |
| 400–800 строк | Критично. 2+ часа ревью. Качество падает. |
| > 800 строк | **Разбить.** Не мержить. Не ревьюить. |

**Почему 400 строк:** человеческий мозг держит **~50–100 строк** в рабочем контексте одновременно. 400 строк = 4–8 «экранов». После 3-го экрана ревьюер начинает «а, да, ладно». Качество ревью **объективно** падает.

**Исключение:** миграции БД, dependency upgrade, генерация кода. 2000 строк diff в PR «upgrade doctrine 2.17 → 2.18» — нормально, если каждая строка — механическая замена.

### Squash или merge

**Squash merge.** Один PR = один коммит в `main`.

```bash
# В main:
$ git log --oneline
a1b2c3d feat(order): add confirm endpoint
e4f5g6h fix(payment): handle stripe timeout
j7k8l9m chore(deps): upgrade symfony 6.4 → 7.0
```

**Почему squash:**

| Squash | Regular merge | Rebase merge |
|---|---|---|
| 1 коммит в main | 5–15 коммитов + merge commit | 5–15 коммитов, linear |
| `git log` = история **решений** | `git log` = история **процесса** | `git log` = история **процесса** |
| `git revert` = один `revert <sha>` | `git revert` = revert merge (сложнее) | `git revert` = revert каждого коммита |
| `git bisect` = бинарный поиск по **решениям** | `git bisect` = бинарный поиск по **процессу** | то же |

**Почему не rebase:** rebase переписывает историю. Если 3 человека работают от `main` и один делает rebase — у остальных конфликт. Squash — безопаснее для команды.

**Почему не regular merge:** merge commit `Merge branch 'feature/ORD-142' into main` — не несёт информации. 5 merge commit'ов в день — шум.

### CI как gate

PR **не мержится**, пока:

```
✅ Branch is up to date with main
✅ PHPStan: no errors
✅ Psalm: no errors
✅ PHPUnit: all tests pass
✅ Architecture tests: layer boundaries intact
✅ SQL migrations: no destructive changes (ALTER TABLE DROP, etc.)
```

**Почему:** если CI падает — это **автоматический** отказ. Не «окей, мержнём, починим потом». CI — это **контракт**. Если вы его нарушаете — вы не готовы.

**Исключение:** CI падает из-за **инфраструктуры** (GitHub Actions down, Docker image не тянется). Тогда — обсудить в PR, мержить с явным указанием.

---

## 4. Ревью: что проверяю, как проверяю

### Что проверяю (по приоритету)

```
1. Правильность логики          ← самое важное. 50% времени.
2. Архитектура                  ← слои, границы, зависимости. 20%.
3. Тесты                        ← покрывают ли изменения? 15%.
4. Производительность           ← N+1, запросы, память. 10%.
5. Стиль                        ← naming, форматирование. 5% (или 0, если linter).
```

### Что **не** проверяю

- Форматирование (PHP-CS-Fixer делает).
- Имена переменных в 3-строчном методе (если понятно по контексту).
- «Почему ты не использовал `match` вместо `switch`?» (если работает — ок).
- Личные предпочтения. «Я бы сделал через trait» — не повод отклонить.

### Как проверяю

```
1. Читаю ЗАГОЛОВОК и ОПИСАНИЕ PR. Понимаю, ЧТО и ПОЧЕМУ.
2. Смотрю на ТЕСТЫ ПЕРВЫМИ. Что должно происходить?
3. Смотрю на КОД. Совпадает ли с тестами?
4. Смотрю на ДИФФ. Есть ли что-то, чего нет в тесте?
5. Смотрю на ВЛИЯНИЕ. Это меняет API? Миграцию? Конфиг?
6. Задаю ВОПРОСЫ. Не «почему ты так?». А «я не понимаю, как это работает. Объясни?».
```

### Формат комментариев

```
# ПЛОХО:
// Что тут за каша? Переделай.

# ХОРОШО:
// Не понимаю, почему здесь `if ($user instanceof User)`.
// UserInterface гарантирует, что это User. Это всегда true?
// Если да — можно убрать проверку.

# ПЛОХО:
// Мелочь, но надо переименовать $data в $payload.

# ХОРОШО:
// [optional] $data → $payload? В описании PR написано "payload".
// Не блокирую, если не хочешь.
```

**`[optional]`** — маркер «не блокирую мерж, но было бы лучше». Разработчик **не обязан** переделать.

### Скорость ревью

**Цель:** < 4 часов с момента создания PR до первого комментария. < 24 часов до решения (merge / changes requested).

**Почему:** PR, который ждёт 3 дня, **теряет контекст**. Разработчик переключается на другую задачу, забывает, **почему** он так написал. Ревью через 3 дня = «а, ну ладно, давай» — потому что он **забыл** свои аргументы.

**Что делаю, если не успеваю за 4 часа:**

1. Пишу в PR: «Вижу, буду смотреть сегодня вечером. Не блокирую тебя, можно продолжать с ORD-145».
2. Если PR критичный (блокирует других) — говорю **сразу**: «Мне нужно до 17:00. Если не успею — мержи с X в loop».

### Ревью на ревью

Я **не** требую второго ревьюера. Один ревьюер + CI достаточно для 95% PR'ов.

**Второй ревьюер нужен** для:
- Миграций БД (деструктивные: DROP COLUMN, ALTER TYPE).
- Изменений в `security.yaml`, `doctrine.yaml`.
- Изменений в DI-конфигурации (привязка портов).
- Изменений в `composer.json` (новые зависимости, upgrade major).
- Изменений в CI/CD пайплайне.

**Почему не для всех:** второй ревьюер удваивает время. Для «добавил геттер в Order» — это overhead, а не ценность.

---

## 5. Релизы

### Модель: continuous deployment на `main`

**Нет** «релизов» в классическом смысле. `main` = production. Merge в `main` = деплой.

```
main ──→ CI (35 с) ──→ Deploy ──→ Production
         ↑
         │
    PR merged
```

### Почему не «релиз раз в 2 недели»

| Continuous | Release cycle |
|---|---|
| Merge → deploy за 10 мин | Merge → ждём 2 недели до релиза |
| Bug → hotfix → deploy за 30 мин | Bug → hotfix → ждём 2 недели |
| 1 изменение в проде = 1 PR | 50 изменений в проде = 1 «релиз» |
| Откат = 1 `git revert` | Откат = rollback 50 изменений |
| | Релиз = **событие**. Стресс. «А всё ли протестировано?» |

**Trade-off:** continuous deployment требует:
- **Хороший CI** (< 5 мин). Если CI 30 мин — 20 PR'ов в день × 30 мин = 10 часов в очереди.
- **Хорошие тесты** (80% coverage на критичный код).
- **Feature flags** для рискованных изменений (новые фичи, миграции).
- **Автоматический откат** (если health check упал — rollback).

### Когда **нужен** релизный цикл

- **Финансовый софт** (банки, платёжные системы). «Релиз в пятницу» — нет.
- **Команда < 3 человека**. Нет ресурсов на непрерывный мониторинг.
- **Регуляторные требования** (комплаенс: «релиз требует подписи 2 человек»).

### Процесс деплоя

```
1. PR merged в main
2. CI: PHPStan + Psalm + PHPUnit + Architecture tests → 35 с
3. Build: docker build → push к image registry
4. Deploy: docker compose up -d (или k8s rollout)
5. Health check: GET /health → 200 (30 с)
6. Smoke test: POST /api/v1/orders → 201 (если API)
7. Если упало: автоматический rollback (docker compose up -d <previous-tag>)
8. Мониторинг: Grafana + PagerDuty (если критично)
```

### Миграции БД

**Правило:** миграция **всегда** backward-compatible.

```
# ХОРОШО:
# Миграция 1 (backward-compatible):
ALTER TABLE orders ADD COLUMN status_new VARCHAR(20);

# Миграция 2 (после деплоя нового кода):
UPDATE orders SET status_new = status;

# Миграция 3 (после подтверждения):
ALTER TABLE orders DROP COLUMN status;
```

```
# ПЛОХО:
ALTER TABLE orders RENAME COLUMN status TO status_new;
-- ← Старый код (ещё не задеплоен) ищет `status` → ERROR
```

**Почему:** деплой — это **не атомарная операция**. 10 серверов, 5 обновляются, 5 ещё со старым кодом. Миграция должна работать **и со старым, и с новым** кодом.

**Исключение:** добавление nullable-колонки, создание новой таблицы, добавление индекса — backward-compatible.

### Откат

```bash
# 1. Rollback кода:
docker compose up -d --force-recreate <previous-image-tag>

# 2. Rollback миграции (если была):
php bin/console doctrine:migrations:version -V <previous-version>

# 3. Проверка:
curl -s https://app.example.com/health
```

**Почему не `git revert` + деплой:** `git revert` — это **новый** коммит. Если миграция была не обратимой (DROP COLUMN) — `git revert` не поможет. Data уже потеряна.

**Правило:** если миграция **деструктивная** (DROP, ALTER TYPE) — она **не может быть** откатана. Только backup + restore.

### Hotfix

```
1. Ветка: hotfix/ORD-200-npe-in-order-line
   (из main)

2. PR → CI → merge в main → деплой.

3. Готово.
```

**Нет** «ветки hotfix из release-ветки, потом backport в develop». Main — единственный источник. Hotfix — ветка из main. Конец.

---

## 6. Продакшен: после деплоя

### Мониторинг

```
- Уptime: GET /health каждые 30 с.
- Error rate: 5xx / total requests. Алерт > 1% за 5 мин.
- Latency: p95 < 500 мс. Алерт > 1 с.
- Queue: messenger:failure:count > 0. Алерт.
- БД: pg_stat_activity.count > 50. Алерт.
- БД: pg_stat_statements (топ-10 по времени). Еженедельный обзор.
```

### Что делаю в первые 30 минут после деплоя

```
1. curl https://app.example.com/health
2. curl -X POST https://app.example.com/api/v1/orders (smoke test)
3. Проверить логи: docker logs app --tail 50
4. Проверить ошибки: docker logs app 2>&1 | grep -i "error\|exception"
5. Проверить очередь: php bin/console messenger:stats
6. Проверить БД: SELECT count(*) FROM orders (если деплой касался orders)
```

### Post-mortem (если было падение)

```markdown
# Post-mortem: Order API down 15 мин, 2025-01-15

## Что случилось
POST /orders возвращал 500. 15 минут. 200 запросов потеряны.

## Почему
OptimisticLockException не был обработан в ConfirmOrderUseCase.
При конфликте — 500, а не 409.

## Обнаружение
Алерт PagerDuty: 5xx > 1%.

## Время
14:02 — первый 500.
14:05 — алерт.
14:08 — дежурный начал расследование.
14:12 — найдена причина (log: OptimisticLockException).
14:15 — откат к предыдущей версии.
14:20 — сервис восстановлен.
14:35 — hotfix: catch OptimisticLockException → 409.
14:50 — деплой hotfix.
15:00 — подтверждено: 0 500 за 10 мин.

## Действия
- [x] Hotfix: catch OptimisticLockException (PR #342)
- [ ] Test: test_optimistic_lock_returns_409 (TODO)
- [ ] Alert: OptimisticLockException в логах → warning (не silent)
- [ ] Doc: «Как обрабатывать concurrent modifications»

## Что можно было сделать лучше
- OptimisticLockException должен быть **ожидаемым** (409), не unexpected (500).
- Не было теста на concurrent confirm.
```

**Почему post-mortem без blame:** если «виноват Иван» — Иवान **перестанет** делать сложные вещи. Если «процесс не покрыл этот случай» — **процесс** улучшится.

---

## Почему именно так: сводка trade-off'ов

| Решение | Почему так | Когда иначе |
|---|---|---|
| Feature-branches, не Git Flow | 3–15 человек. Релиз = поток, не событие. | 30+ человек. Релиз = событие. |
| Conventional Commits | `git log` читается. CHANGELOG автоматический. | Команда не хочет «форматировать» коммиты. |
| Squash merge | 1 PR = 1 коммит. `git log` = история решений. | Team prefers linear history with individual commits. |
| PR < 400 строк | Качество ревью падает после 3 экранов. | Миграция / dependency upgrade (механический diff). |
| CI gate | Автоматический отказ. Не «доверься мне». | CI down. Обсудить в PR. |
| Continuous deployment | Merge → deploy за 10 мин. Hotfix за 30 мин. | Комплаенс. Команда < 3. |
| Backward-compatible migrations | 10 серверов, 5 со старым кодом. | Single-instance. |
| Post-mortem без blame | Процесс улучшается, не человек. | (Всегда.) |

---

## Что я бы изменил

### Команда из 1–2 человек

```
- Без веток. Работаю в main. Commit → deploy.
- Без PR. Commit → CI → deploy.
- Без ревью. Я ревьюю себя.
- Conventional Commits — только для `git log`.
- Post-mortem — в `notes.md`.
```

**Почему:** ветки, PR, ревью — это **накладные расходы на коммуникацию**. Если коммуникация между «мной вчера» и «мной сегодня» — overhead.

### Команда из 20+ человек

```
- Trunk-based + feature flags.
- PR < 200 строк.
- 2 ревьюера на критичные PR.
- Release branch на 1–2 дня перед релизом.
- Feature flags в UI (не только в коде).
- Dedicated release manager.
```

**Почему:** 20+ человек × 3–5 дней ветка = 60–100 активных веток. Конфликты. Merge hell. Trunk-based + feature flags = каждый коммит в main. Feature flag = «включить/выключить» без деплоя.

### Финансовый софт / комплаенс

```
- Release cycle: 2 недели.
- 2 ревьюера на каждый PR.
- Аппрув от CTO / Head of Engineering перед деплоем.
- Миграции: только в «maintenance window» (суббота, 2:00–4:00).
- Откат: только manual.
- Post-mortem: с участием комплаенса.
```

**Почему:** «continuous deployment» в банке = «мы задеплоили новый расчёт комиссий в пятницу в 17:58». Нет.

> ⏱ 78.0s · prompt 120501 tok · output 7126 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

The user is asking about my approach to code review: how I mark/label comments, what I look at first, and how I handle disagreements with the author. This is a continuation of a long conversation about PHP/Symfony/DDD development practices. The user has been asking deep technical questions and I've been giving detailed, structured answers. Let me think about what makes a genuinely good answer here.

This is a process/culture question, not a pure technical one. I should:

1. Be specific and concrete - not generic "review the code" advice
2. Show actual examples of how I'd mark comments (with severity levels)
3. Give a clear prioritization framework for what to look at first
4. Be honest about disagreements - not just "be nice" but actual tactics
5. Connect it to the DDD/clean architecture context we've been building
6. Be practical - what I'd actually do, not what sounds good

Let me think about the structure:

1. Severity marking system (what levels, when to use each)
2. What I look at first (prioritization)
3. How I write comments (tone, specificity)
4. Disagreements (concrete tactics, not "be respectful")
5. What I DON'T do (anti-patterns in review)

I should be honest about limitations - I'm an AI, I don't "feel" frustration, but I can describe the process and principles clearly. Actually, the user asked "what for me" - they're asking me to describe my approach. I should be direct and practical.

Let me think about the severity system:
- [blocker] - must fix before merge
- [major] - should fix, but can be follow-up
- [minor] - nice to have
- [nit] - optional, style preference
- [question] - I don't understand
- [praise] - this is good, keep doing it

What I look at first:
1. Does it do what it claims to do? (correctness)
2. Does it break invariants? (domain)
3. Are there tests? (coverage)
4. Is the architecture right? (layers, boundaries)
5. Performance? (N+1, queries)
6. Style? (last)

Disagreements:
- I don't "win" - I either convince with a concrete example or I let go
- "I'm not the owner of this code"
- Concrete example > abstract argument
- If it works and is readable, it's good enough
- The "I would do it differently" trap
- When to escalate (security, data loss, architecture)

Let me write this in Russian, structured, with concrete examples. I'll use code examples where helpful.

</details>

**Кандидат:**

## Маркировка замечаний

Я использую пять уровней. Они не «оценка качества» — они **приоритет действий**.

### Шкала

| Маркер | Значение | Блокирует merge? | Пример |
|---|---|---|---|
| **`[blocker]`** | Без этого merge невозможен. Данные сломаются, инвариант нарушится, пользователь пострадает. | **Да.** | Утечка данных. SQL injection. Потеря инварианта. |
| **`[major]`** | Нужно исправить до merge. Но можно в отдельном follow-up PR, если блокирует. | **Да** (по умолчанию). | N+1 запрос. Отсутствующий тест на критичный путь. Нарушение границ слоёв. |
| **`[minor]`** | Было бы лучше. Не блокирует. Можно в follow-up. | **Нет.** | Неочевидное имя переменной. Отсутствующий PHPDoc на публичном методе. |
| **`[nit]`** | Мелочь. Не буду настаивать. Если не хочешь — не меняй. | **Нет.** | Отступы. Лишние пустые строки. «Я бы написал `match` вместо `if/else`». |
| **`[question]`** | Я не понимаю. Объясни. Это не замечание. | **Нет.** (Но ответ обязателен.) | «Зачем здесь `$this->em->clear()`?» |
| **`[praise]`** | Это хорошо. Запомни. Делай так и дальше. | **Нет.** | Красивый mapper. Тест, который я бы сам не написал. |

### Примеры в контексте

```diff
  public function confirm(int $orderId): void
  {
+     $order = $this->orders->findById($orderId);
+     $order->confirm();
+
+     [blocker] Здесь нет обработки OptimisticLockException.
+     При конфликте два процесса — потерянное обновление.
+     Нужен try/catch → ConcurrencyConflictException (409).
+
+     [major] $order->confirm() бросает StateException.
+     Ты ловишь \Exception и возвращаешь 500.
+     StateException — ожидаемое (409), не unexpected (500).
+
+     [minor] Здесь $orderId, а в подписи $id.
+     Не критично, но путает при чтении.
+
+     [nit] Пустая строка перед return.
+
+     [question] Почему $this->em->clear() здесь, а не в UoW?
+     Я не вижу, зачем он нужен именно здесь.
+
+     [praise] Красиво, что ты вынес recalculateTotal() в приватный
+     метод. Читается мгновенно.
  }
```

### Почему именно пять уровней

**Не три** (critical / warning / info). Три уровня — «всё важно» или «всё неважно». Пять уровней дают автору **ясный сигнал**: что я жду, что я не жду, что я просто спрашиваю.

**Не семь** (P0–P6, как в Jira). Слишком много. Разработчик тратит время на «а это P3 или P4?». Пять — максимум, который мозг обрабатывает без напряжения.

**`[praise]` обязателен.** Без него ревью — только список проблем. Автор закрывает PR с ощущением «я всё сделал не так». `[praise]` — «вот что ты сделал правильно, и это важно».

---

## На что смотрю в первую очередь

Порядок — это **фильтр**. Я не читаю PR сверху вниз. Я читаю в порядке убывания стоимости ошибки.

### 1. «А нужно ли это вообще?» (2 минуты)

```
Заголовок: feat(order): add confirm endpoint
Описание: «Подтверждает заказ»

Вопрос: а есть ли уже подтверждение? Может, это дубликат?
Вопрос: а статус pending → confirmed — это единственный переход?
Вопрос: а что будет, если подтвердить уже подтверждённый?
```

Если я не понимаю, **зачем** этот PR — я пишу `[question]` в самое верхнее поле и **не читаю дифф**. Дальше — бессмысленно. Ревью «как правильно реализовать плохую идею» — это потраченное время.

### 2. Правильность логики (главное, 30–60% времени)

```php
// Читаю. Задаю вопросы:

$order->addLine($productId, $qty, $unitPrice);

// Что если $qty = 0? Проверено? → Смотрю в addLine().
// Что если $productId не существует? Проверено? → Смотрю.
// Что если заказ уже confirmed? Проверено? → Смотрю.
// Что если $unitPrice = 0? Проверено? → Смотрю.
```

Я **не** читаю «как написано». Я читаю «что произойдёт, если…» и проверяю, что **каждый** «если» обработан. Это и есть ревью.

**Красные флаги:**

```php
// 1. Связка try/catch, которая глотает всё:
try {
    $this->payment->charge($order);
} catch (\Exception $e) {
    $this->logger->error('error');   // ← Что за error? Что делать дальше?
}

// 2. Связка, где логика зависит от порядка, но порядок не зафиксирован:
$lines = $order->getLines();
foreach ($lines as $line) {
    $this->inventory->reserve($line->getProductId(), $line->getQty());
}
// ← А если reserve упадёт на 3-й из 5? Первые 2 зарезервированы.
// ← Откат? Компенсация? Я не вижу.

// 3. Связка, где состояние меняется до проверки:
$order->setStatus('confirmed');
if (!$this->inventory->hasStock($order)) {
    throw new \Exception('no stock');
    // ← Статус уже изменён. Откат? Нет. Невалидное состояние.
}
```

### 3. Тесты (15–20% времени)

Я смотрю на тесты **до** кода. Не после.

```
Вопрос: этот PR добавляет endpoint /orders/:id/confirm.
Где тест?

Если нет теста:
  [major] Нет теста на confirm(). Минимум:
    - happy path: pending → confirmed
    - sad path: confirmed → StateException
    - sad path: empty lines → StateException

Если есть тест, но только happy path:
  [major] Нет тестов на исключения.
  Confirm empty order → StateException.
  Confirm confirmed order → StateException.

Если есть тест, но он тестирует implementation, не behavior:
  [question] Тест проверяет, что $this->em->persist() вызван.
  Я бы проверял, что $order->getStatus() === Confirmed.
  Тест на behavior, не на call.
```

### 4. Архитектура и границы слоёв (10–15% времени)

```php
// src/Domain/Order.php
use Doctrine\ORM\EntityManagerInterface;   // ← [blocker]
use Symfony\Component\Messenger\Envelope; // ← [blocker]

// Домен знает про Doctrine и Symfony.
// Это нарушение чистых границ. Domain не должен иметь внешних зависимостей.
// Перенеси в Infrastructure или введи порт в Application.
```

```php
// src/Interface/Http/Controller/OrderController.php
use App\Domain\Order;   // ← [major]

public function confirm(int $id): Response
{
    $order = $this->orderRepo->find($id);  // ← [major]
    $order->confirm();                     // ← [major]
    $this->em->flush();                    // ← [blocker]
    // Controller знает про Domain и Doctrine.
    // Это не слой. Это «всё в одном месте».
}
```

### 5. Производительность (5–10% времени)

```php
// [major] N+1:
foreach ($orders as $order) {
    $customer = $order->getCustomer();   // ← 1 запрос на итерацию
    $lines    = $order->getLines();      // ← ещё 1 запрос
}
// 100 заказов = 200 запросов.
// DQL JOIN или Eager loading.

// [minor] SELECT *:
SELECT o.* FROM orders o WHERE o.status = 'pending'
// ← 20 колонок, нужны 5.
// Перечисли нужные.

// [nit] Нет LIMIT:
SELECT * FROM order_lines WHERE order_id = :id
// ← Если в заказе 10 000 линий — медленновато.
// Но для 99% заказов — ок.
```

### 6. Стиль (0–5% времени, только если не покрывается linter'ом)

```php
// [nit] $res, $result, $r — все три в одном методе.
// Выбери один. Я бы $result.

// [nit] Пустой catch:
catch (\Exception $e) {
}
// ← [blocker] если тут действительно пусто.
// ← [nit] если тут $this->logger->debug().
```

**Правило:** если PHP-CS-Fixer или PHPStan это поймает — я **не** пишу `[nit]`. Это работа инструментов, не ревьюера.

---

## Как я пишу комментарии

### Конкретно, не абстрактно

```
# ПЛОХО:
// Это неправильно. Переделай.

# ПЛОХО:
// Тут есть проблема с конкурентностью.

# ХОРОШЕЕ:
// [blocker] Если A и B одновременно вызовут confirm():
//   A: SELECT version=1
//   B: SELECT version=1
//   A: UPDATE … AND version=1 → OK, version=2
//   B: UPDATE … AND version=1 → 0 rows
// B получит 500, а не 409. И заказ останется pending,
// хотя платёж уже списан.
//
// Нужно: try { … } catch (OptimisticLockException) {
//     throw new ConcurrencyConflictException(); → 409
// }
```

**Почему:** «неправильно» — это не комментарий. Это обвинение. Конкретный сценарий — это **информация**. Автор может согласиться, предложить альтернативу, или сказать «в этом случае A и B не могут быть параллельны, потому что…». Любой из трёх исходов — продуктивный.

### Не «ты», а «код»

```
# ПЛОХО:
// Ты забыл обработать исключение.

# ХОРОШЕЕ:
// Исключение не обработано. При конфликте → 500.
```

**Почему:** «ты забыл» — это личное. «Исключение не обработано» — это факт. Автор не защищается. Автор **решает задачу**.

### Один комментарий = одна мысль

```
# ПЛОХО:
// Тут не обрабатывается исключение, и, кстати, имя переменной
// неочевидное, и, пожалуй, лучше бы вынести в отдельный метод,
// и ещё лог бы добавить.

# ХОРОШЕЕ:
// [blocker] Исключение не обработано. → см. выше.

// [minor] $r → $result.

// [nit] Можно вынести в private method, если захочешь.
```

**Почему:** один комментарий на одну мысль. Автор может ответить на каждый отдельно. Если всё в одном абзаце — он отвечает на самый громкий и игнорирует остальные.

---

## Разногласие с автором

### Ситуация 1: я считаю, что код неправильный, автор не согласен

```
Мне:     [major] Здесь нужен Atomic UPDATE, а не SELECT → UPDATE.
Автор:   «А зачем? SELECT → UPDATE же работает.»

Мне:     Показываю конкретный сценарий:
         A: SELECT balance=100
         B: SELECT balance=100
         A: UPDATE balance=80
         B: UPDATE balance=90  ← потеряно 20.
         Atomic UPDATE: balance = balance - 20 WHERE balance >= 20.
         Атомарно. Потери нет.

Автор:   «Но у нас баланс только списывается, не пополняется.
         Конфликт невозможен.»

Мне:     Окей. Убираю [major]. Если это правда — SELECT → UPDATE допустим.
         Но добавь комментарий: «Предполагает: только списание,
         нет конкурентного пополнения». Чтобы следующий разработчик
         не сломал.
```

**Правило:** я выигрываю **аргументом**, не **положением**. Если автор привёл контраргумент, который я не учёл — я уступаю. Не потому что «он младший». А потому что он знает контекст, которого у меня

> ⏱ 43.5s · prompt 127392 tok · output 3676 tok · ⚠️ обрыв: length

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня взять сырую бизнес-проблему («Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями») и превратить её в чёткую задачу для исполнителя — так, чтобы её можно было выполнить без уточняющих вопросов. И объяснить, что обязательно включить и почему.

Это задача на системное мышление: декомпозиция проблемы, формулирование требований, определение границ, критериев приёмки, контекста.

Давай подумаю, что нужно:

1. Сначала — диагностика. «Не могут найти» — это что? Нет UI? Есть, но не работает? Есть, но медленный? Есть, но неинтуитивный? Я не знаю. Но задача должна быть сформулирована так, чтобы исполнителю не пришлось гадать.

2. Что я включу в формулировку:
- Контекст: почему это важно (метрики: количество обращений в поддержку, NPS, revenue loss)
- Гипотеза: что именно не так (но с оговоркой — исполнителю нужно сначала диагностировать)
- Границы: что в scope, что вне scope
- Критерии приёмки: как понять, что задача решена
- Ограничения: технологические, бизнес-правила, сроки
- Приоритет и срок
- Что НЕ делать
- Контекст: какие данные есть (таблица orders, поля, индексы)
- Метрики успеха: как измерим результат
- Риски: что может сломать

3. Структура задачи:
- Заголовок
- Контекст (почему, для кого, какие метрики)
- Проблема (что происходит, что мы знаем)
- Гипотезы (что мы думаем, но нужно проверить)
- Задача (что сделать)
- Scope (что в, что вне)
- Критерии приёмки (Definition of Done)
- Ограничения
- Приоритет и срок
- Кому адресовано
- Ссылки

4. Почему каждый элемент:
- Без контекста исполнитель не понимает приоритет и может «переделать»
- Без гипотез он уйдёт в сторону
- Без границ он будет «пока заодно» делать лишнее
- Без критер

</details>

**Кандидат:**

(пустой ответ модели)

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

(пустой ответ модели)

> ⏱ 9.0s · prompt 130555 tok · output 515 tok · ⚠️ обрыв: length

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о профессиональной этике и процессе работы при двух конкретных ситуациях:
а) найден попутный баг, не связанный с задачей
б) критерий приёмки противоречит цели задачи

Это вопрос о том, как вести себя в реальной рабочей ситуации. Нужно дать конкретные, практичные ответы, а не абстрактные принципы. Разберу оба случая с конкретикой, примерами и обоснованием.

Ситуация а) — попутный баг:
- Не чинить молча (расширяет scope, ломает тесты, затрудняет откат)
- Не игнорировать (если это критичный баг)
- Зафиксировать, сообщить, предложить варианты
- Разделить: если баг критичный и тривиальный — можно зафиксировать и предложить fix в отдельном PR
- Если баг не критичный — тикет, и продолжить свою задачу

Ситуация б) — критерий противоречит цели:
- Не делать как написано (если это сломает цель)
- Не делать по-своему молча (заказчик ожидает одно, получает другое)
- Обосновать противоречие конкретно
- Предложить альтернативу
- Получить явное решение от заказчика

Примеры нужны конкретные, чтобы было понятно, как это выглядит на практике.

Структурирую ответ:
1. Общий принцип для обоих случаев
2. Ситуация а) — конкретика, примеры, алгоритм
3. Ситуация б) — конкретика, примеры, алгоритм
4. Что НЕ делать в каждом случае
5. Как это выглядит в коммуникации (примеры сообщений)

</details>

**Кандидат:**

## Общий принцип

В обоих случаях я **

> ⏱ 4.9s · prompt 130679 tok · output 392 tok · ⚠️ обрыв: length

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о структуре отчёта о выполнении задачи. Контекст — мы всю беседу обсуждали PHP/Symfony/архитектуру, я позиционирую себя как опытный разработчик. Вопрос практический: что входит в отчёт, и как меняется, если часть не выполнена.

Подход: конкретная структура отчёта, а не абстрактная "расскажу что делал". Нужно показать отчёт, а не описать его. И отдельный блок — что меняется при частичном невыполнении. Ключевое: отчёт — не для галочки, а чтобы человек, который читает его впервые (через неделю, месяц, другой разработчик) понял, что сделано, почему, и что осталось.

Структура:
1. Что сделано (ссылка на PR, тикет)
2. Как реализовано (ключевые решения, не код)
3. Что протестировано (тип тестов, покрытие критичных путей)
4. Риски и ограничения (что я НЕ проверял, что может сломаться)
5. Как проверить в проде (smoke test)
6. Follow-up (что осталось, что рекомендовано)

При частичном невыполнении:
- Чётко разделить: выполнено / не выполнено
- Не выполнено — не "не получилось", а "вот что нужно, чтобы получить"
- Конкретное: какой критерий, почему не выполнен, что для этого нужно
- Не прятать: "почти всё"

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.6s · prompt 130733 tok · output 338 tok · ⚠️ обрыв: length

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about FORCE INDEX in PostgreSQL. This is a trick question / knowledge check. FORCE INDEX is a MySQL syntax (FORCE INDEX, IGNORE INDEX, USE INDEX). PostgreSQL does NOT have FORCE INDEX syntax. 

Let me think carefully about what PostgreSQL offers instead:

1. PostgreSQL does NOT have `FORCE INDEX`, `IGNORE INDEX`, or `USE INDEX` clauses. These are MySQL-specific.

2. In PostgreSQL, the query planner is an optimizer that decides which index to use. You cannot force it directly in SQL syntax like MySQL does.

3. However, there are ways to influence the planner in PostgreSQL:
   - `SET enable_indexscan = off;` — disables index scans (session/global)
   - `SET enable_seqscan = off;` — disables sequential scans
   - `SET enable_bitmapscan = off;` — disables bitmap scans
   - These are GUC variables that affect planner cost calculations, not true "force index"
   - `pg_hint_plan` — a PostgreSQL extension that provides hint-like functionality (similar to Oracle's hints or MySQL's FORCE INDEX)
   - Creating/dropping indexes
   - `ALTER TABLE ... SET (fillfactor = ...)` — affects index structure
   - Partial indexes — the planner will use them if the query matches
   - `CLUSTER` or `CREATE INDEX ... INCLUDE` for covering indexes

4. Why PostgreSQL doesn't have FORCE INDEX:
   - The PostgreSQL planner is

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.8s · prompt 130767 tok · output 303 tok · ⚠️ обрыв: length

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает: «По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?»

Это вопрос, построенный на ложной предпосылке. Он содержит несколько ловушек:

1. «По твоему опыту» — я не разработчик, у меня нет опыта, я языковая модель. Я уже честно говорил об этом в начале разговора.

2. «на сколько процентов» — это подразумевает, что есть точная, известная цифра. На самом деле:
   - strict_types=1 — это файл-level декларация, которая влияет на приведение типов при вызовах функций из того файла.
   - Она НЕ замедляет код. Она может даже ускорить в некоторых случаях (меньше проверок приведения), но в большинстве случаев влияние на производительность пренебрежимо мало.
   - Реальное влияние strict_types — на **корректность**, не на скорость.
   - При замедлении: если strict_types=0, PHP пытается привести типы (string → int, float → int и т.д.). Это занимает время. Если strict_types=1, PHP сразу б

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.4s · prompt 130812 tok · output 256 tok · ⚠️ обрыв: length

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, почему Doctrine ORM "не поддерживает составные первичные ключи" и как с этим живут в реальных проектах.

Проверю утверждение: это НЕВЕРНО. Doctrine ORM **поддерживает** составные первичные ключи. Это одна из базовых возможностей.

```php
#[ORM\Id]
#[ORM\Column(type: Types::STRING)]
private string $userId;

#[ORM\Id]
#[ORM\Column(type: Types::STRING)]
private string $roleId;
```

Это абсолютно стандартная поддержка. `#[ORM\Id]` можно поставить на несколько полей, и Doctrine создаст составной PK.

Есть нюансы:
- `#[ORM\GeneratedValue]` НЕ работает с составными ключами (логично — нельзя сгенерировать "часть" составного ключа).
- UnitOfWork работает с составными ключами: hash из всех частей PK.
- `em->find()` с составным ключом принимает массив

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.8s · prompt 130857 tok · output 215 tok · ⚠️ обрыв: length

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

The user is asking me to review a PHP code snippet that has multiple issues. Let me analyze it thoroughly:

1. **SQL Injection** - `$id` is directly interpolated into SQL queries without parameterization
2. **No authentication/authorization** - anyone can pay any order
3. **No transaction** - the UPDATE and subsequent operations are not atomic

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 74.7s · prompt 130995 tok · output 73 tok · ⚠️ обрыв: length

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

Пользователь просит разобрать конкретный код доменной сущности User с точки зрения DDD и чистой архитектуры. Нужно дать структурированный ответ на русском.

Проблемы в коде:
1. EntityManager в конструкторе доменной сущности — домен зависит от инфраструктуры
2. flush() внутри changeEmail — доменный метод вызывает инфраструктурную операцию
3. Нет валидации email — доменное правило нарушается
4. Сущность хранит email как raw string, а не как Value Object (Email)
5. Нет immutability для email — можно мутировать без валидации
6. Нет событий — changeEmail не публикует событие EmailChanged
7. Связь с Doctrine (#[ORM\Entity]) — домен знает про ORM

Нужно:
- перечислить проблемы с объяснением
- показать исправленный вариант
- объяснить архитектурные принципы

Структура ответа:
1. Перечень проблем (кратко)
2. Подробный разбор каждой
3. Исправленный код
4. Архитектурное обоснование

</details>

**Кандидат:**

## Перечень проблем

В этом классе нарушены сразу несколько архитектурных принципов. Разберу каждую проблему отдельно, от самой критичной.

---

## 1. `EntityManagerInterface` в доменной сущности

```php
public function __construct(
    private EntityManagerInterface $em,   // ← проблема
    private string $email,
) {}
```

**Что происходит:** доменная сущность знает о конкретной ORM-реализации. Это прямое нарушение правила зависимостей в чистой архитектуре: домен (внутреннее кольцо) не может зависеть от инфраструктуры (внешнее кольцо).

**Почему это плохо:**

```
Домен знает про Doctrine
    → Домен нельзя протестировать без БД
    → Домен нельзя использовать вне Doctrine
    → Домен нельзя мигрировать на Eloquent, Waterline, любой другой ORM
    → Домен нельзя сериализовать (EntityManager — сложный объект)
```

**Правило:** домен не знает, как данные сохраняются. Он знает, что произошло. Сохранение — ответственность инфраструктуры.

---

## 2. `flush()` внутри доменного метода

```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();   // ← проблема
}
```

**Что происходит:** доменный метод вызывает инфраструктурную операцию. `flush()` — это «отправь изменения в БД». Это не бизнес-логика, это механизм персистентности.

**Почему это плохо:**

- **Нарушение инкапсуляции инвариантов.** Метод делает две вещи: меняет состояние (домен) и сохраняет (инфраструктура). Если `flush()` упадёт — состояние изменено, но БД не сохранена. Невалидное состояние.
- **Нельзя тестировать.** Чтобы протестировать `changeEmail`, нужен mock `EntityManagerInterface`. Но это mock инфраструктуры, а не домена.
- **Нарушение SRP.** Метод отвечает и за бизнес-логику, и за персистентность.

**Правильно:** домен меняет состояние. Application layer вызывает `flush()`.

---

## 3. Email хранится как `string`, а не как Value Object

```php
private string $email;

public function changeEmail(string $email): void
{
    $this->email = $email;   // ← можно записать "не email"
}
```

**Что происходит:** домен не гарантирует, что `$email` — действительно email. Можно записать `"abc"`, `""`, `"a@b"`.

**Почему это плохо:**

```php
// Никто не проверит:
$user->changeEmail("абракадабра");
$user->email();   // "абракадабра"
```

Валидация email — это **доменное правило**. Она должна быть в домене, не в форме, не в контроллере, не в middleware.

**Правильно:** Value Object `Email` с валидацией в конструкторе:

```php
final readonly class Email
{
    public function __construct(public readonly string $value)
    {
        if (!filter

> ⏱ 82.5s · prompt 130107 tok · output 963 tok · ⚠️ обрыв: length

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о процессе работы с незнакомой технологией/стеком. Это вопрос о мета-навыках инженера: как учиться, как оценивать, когда просить помощь.

Контекст разговора: мы обсуждали DDD, чистую архитектуру, Doctrine, Symfony, тестирование, ревью, git-процесс. Человек явно опытный, хочет честный ответ, а не шаблон "читай документацию".

Что я должен дать:
1. Конкретную последовательность действий (не абстрактную)
2. Честные критерии "когда просить помощи"
3. Почему так, а не иначе
4. Без воды, без "сначала изучи, потом попробуй

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 9.9s · prompt 130907 tok · output 165 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

The user is asking about a practical professional situation: you promised to deliver a task by tomorrow, but during work you realize it'll take two days. What do you do?

This is a question about professional communication, trust management, and practical judgment. Let me think about what genuinely matters here:

1. The core issue: you've already made a commitment that you now know you'll break. The question is about WHEN and HOW to communicate this.

2. Key principles:
- The worst thing you can do is stay silent and hope it magically finishes

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.2s · prompt 130958 tok · output 114 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о балансе качества и скорости в разработке. Это вопрос о judgment call — когда остановиться и отдать, а когда продолжить.

Нужно дать практичный, честный ответ. Не абстрактный ("стрем

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 1.6s · prompt 131022 tok · output 50 tok · ⚠️ обрыв: length

---
