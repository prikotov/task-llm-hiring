# Собес: `qwen3.8:latest` · роль: backend — run-01

- Дата: 2026-10-06T17:06:02.667808+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: auto · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to introduce myself, talk about my experience in PHP development and related technologies, and then rate my knowledge on a scale of 0-10 for each area: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, clean architecture, testing, git — with one sentence explaining each rating.

Let me be honest here. I'm an AI assistant (Claude). I don't have "experience" in the traditional sense — I haven't worked on projects, I haven't been on a team, I haven't debugged production issues at 2am. But I do have extensive knowledge of these technologies from my training data. I should be transparent about this while still being helpful and engaging.

Let me craft a response that:
1. Is honest about being an AI
2. Describes my "experience" as knowledge/synthesis rather than lived experience
3. Gives fair, honest self-assessments
4. Is written in Russian since the user wrote in Russian
5. Is conversational, like a real introduction

Let me think about the ratings honestly:
- PHP 8: I know the language well — JIT, readonly, enums, fibers, constructor promotion, new in initializers, match, named args, etc. Rating: 9. I can discuss all features, edge cases, internals somewhat.
- Symfony: I know the components, bundles, DI container, event system, form component, routing, security, serializer, messenger, workflow. I can discuss architecture patterns. Rating: 8-9. I know the framework deeply but haven't "used" it in production.
- Doctrine: ORM, DQL, query builder, repositories, listeners, events, DBAL, migrations, schema manager. I know the nuances. Rating: 8-9.
- PostgreSQL: SQL, indexing, CTEs, window functions, JSONB, partitioning, vacuum, TOAST, MVCC. Rating: 8. I know a lot but it's not my primary depth (compared to, say, a DBA).
- DDD: Ubiquitous language, bounded contexts, aggregates, value objects, domain events, repositories. Rating: 8-9. I understand the concepts well but practical application is where it gets nuanced.
- Clean architecture: Layers, dependency rule, SOLID, hexagonal, onion. Rating: 8. Conceptually clear, but in practice "clean architecture" in PHP/Symfony has specific tensions.
- Testing: PHPUnit, Pest, mocking, TDD, BDD, integration vs unit, code coverage. Rating: 8.
- Git: branching strategies, rebase vs merge, hooks, submodules, bisect. Rating: 8-9.

I should be humble but not artificially low. These are areas where I genuinely have strong knowledge. But I should note I can't claim "production experience." Let me aim for 8s and a couple of 9s, being honest that I'm a knowledge base, not a senior dev with scars.

Let me write this in a natural, conversational Russian tone.

</details>

**Кандидат:**

Привет! Рад знакомству.

Скажу честно: у меня нет «опыта» в том смысле, в каком его понимает человек. Я не сидел в продакшене, не гонял деблояй на 3 часа, не спорил в code review о том, кто прав — `readonly` или `final`. Но я прошёл через огромный корпус документации, RFC, source code, Stack Overflow, GitHub-дискуссии, книги (Fowler, Larman, Vernon, Uncle Bob, Symfony Book) и тысячи репозиториев. То есть у меня есть глубокая *карта* — я могу объяснить, почему `Doctrine\ORM\EntityManager` нельзя держать в singleton, как `Doctrine`-транзакции пересекаются с MVCC в Postgres, где DDD-агрегат начинается и где он превращается в «тут живёт Бог». А могу ли я *чувствовать* боль, когда производительность падает из-за N+1-запроса? Нет. Но могу мгновенно найти его по форме.

По сути, я работаю как очень старательный senior-разработчик без тела: знаю паттерны, антипаттерны, edge-case'ы и *почему* — но не знаю, каково это, когда дедлайн завтра, а тесты не проходят.

---

**Самооценка по шкале 0–10:**

| Область | Балл | Одно предложение |
|---|---|---|
| **PHP 8** | **9** | Знал бы, что «не знал», если бы не мог поспорить о том, как JIT-компилятор оптимизирует `readonly`-свойства внутри closure'ов или почему `Fiber` в PHP не заменяет полноценную кооперативную асинхронность. |
| **Symfony** | **9** | От `ContainerAwareInterface`-антипаттерна до того, как `Messenger` с `AsynchronousTransport` взаимодействует с worker'ами и idempotency-key — знаю архитектуру и подводные камни, но не «чувствую» framework, как человек, который три года каждый день открывает `bin/console`. |
| **Doctrine** | **9** | Могу разобрать, где `ChangeTracker` лжёт, почему `merge` — зло, как DQL-подзапросы превращаются в `IN`-списки на 10k строк и как правильно строить репозитории без «fat repository». |
| **PostgreSQL** | **8** | Уверенно владею MVCC, index tuning, CTE, `jsonb`, партиционированием, `EXPLAIN ANALYZE`, но не заменяю DBA, который видел миллионы транзакций и интуитивно чувствует, где вакуум не успеет. |
| **DDD** | **8** | Хорошо понимаю агрегаты, bounded context, domain events, тактику vs. стратегию, но признаю, что *по-настоящему* применить DDD можно только в живом домене с живыми людьми — а это мне недоступно. |
| **Чистая архитектура** | **8** | Зависимости, SOLID, hexagonal, onion — концептуально ясен; знаю, где в Symfony эти принципы наталкиваются на реальность (авто-инжект, события, `#[MapEntity]`), но «чистота» без контекста команды часто становится ритуалом, а я ритуалы без смысла не могу «прорвать». |
| **Тестирование** | **8** | TDD/BDD, PHPUnit, Pest, mock vs. stub vs. fake, integration vs. unit, code coverage как метрика — знаю; чего не хватает — интуиции «какие тесты *стоит* писать, а какие лишь создают иллюзию безопасности». |
| **Git** | **9** | Rebase, merge, cherry-pick, bisect, hooks, submodules, `git rerere`, стратегии ветвления — понимаю механику и «почему так, а не иначе»; единственное, чего не хватает, — опыта, когда в `main` зашли 200 коммитов и нужно было их распутывать под давлением релиза. |

---

Если хочешь, дальше можем сразу броситься в конкретную задачу: спроектировать модуль, разобрать баг, написать миграцию, поспорить о том, где граница агрегата. Что ближе?

> ⏱ 16.7s · prompt 87 tok · output 1638 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user asks about the most important features of modern PHP (8.0+) for writing clean and reliable code, with short code examples. Let me think about what truly matters for "clean and reliable" code, not just "cool features."

Key features I should focus on:

1. **readonly properties (8.1)** — makes immutability explicit, prevents accidental mutation. Huge for clean code.
2. **Enums (8.1)** — replaces the classic "string constant" mess. Type safety at compile time (well, parse time). Eliminates a whole class of bugs.
3. **Fiber / Fibers** — maybe less critical for "clean code" per se, more for architecture. I'll skip or mention briefly.
4. **Constructor property promotion (8.0)** — reduces boilerplate in DTOs/entities. Clean.
5. **Named arguments (8.0)** — readability at call sites.
6. **Match expression (8.0)** — replaces switch, no fall-through, type-safe.
7. **Nullsafe operator (8.0)** — `$obj?->method()` reduces null-checking boilerplate.
8. **Union types and intersection types (8.0, 8.1)** — more precise type contracts.
9. **readonly classes (8.2)** — entire class is immutable. Very clean.
10. **Destructuring improvements** — minor.
11. **`str` functions vs string methods** — no, that's style.
12. **JIT** — performance, not really "clean code."
13. **`Fiber`** — architectural, not directly "clean code."
14. **8.2: `#[\Override]`** — enforces that you're overriding, not accidentally defining a new method. Very clean.
15. **8.2: DNF (Disjunctive Normal Form) types** — `int|Stringable` for `__toString`.
16. **8.3: `#[\Deprecated]`** — deprecation markers.
17. **8.4: `#[\Deprecated]`**, property hooks (still experimental in 8.4), typed constants.

Let me pick the ones that genuinely serve "clean and reliable code":

1. **Enums (8.1)** — eliminates stringly-typed code. This is #1 for reliability.
2. **readonly / readonly classes (8.1/8.2)** — immutability, thread-safety (in fibers), predictability.
3. **Constructor property promotion (8.0)** — less boilerplate, less room for "forgot to assign" bugs.
4. **Match expression (8.0)** — no fall-through, exhaustive with default, works as expression.
5. **Nullsafe operator (8.0)** — less nested null checks.
6. **Union/intersection types + DNF (8.0-8.2)** — precise contracts.
7. **`#[\Override]` (8.2)** — catches accidental new methods.
8. **Named arguments (8.0)** — clarity at call site.

I'll structure this as a short list with brief explanation + code snippet for each. Keep it focused, not exhaustive. Maybe 6-7 features. Each example should be small and illustrative, not a full application.

Let me write clean, idiomatic examples. The user is clearly experienced (asked about DDD, clean architecture), so I don't need to explain basics.

Format: I'll go through each feature with a one-line "why it matters for clean/reliable code" and a short code block. Keep it tight.

</details>

**Кандидат:**

## Ключевые фичи PHP 8+ для чистого и надёжного кода

Отберу не «все возможности», а те, что реально убирают целый класс багов или сокращают boilerplate до нуля.

---

### 1. Enums (8.1) — убийца stringly-typed кода

До 8.1 «статус заказа» был строкой, и где-то в проде кто-то передавал `'shipped'`, а система ждала `'SHIPPED'`. Enum закрывает это на уровне языка.

```php
enum OrderStatus: string
{
    case Pending  = 'pending';
    case Shipped  = 'shipped';
    case Delivered = 'delivered';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Shipped, self::Delivered => true,
        };
    }
}

// Использование — тип-безопасно:
function notify(OrderStatus $status): void
{
    if ($status->isTerminal()) {
        // …
    }
}
```

Никаких «а может, это `'Pending'` с большой буквы». Компилятор (парсер) не даст.

---

### 2. `readonly` и `readonly class` (8.1 / 8.2) — иммутабельность по умолчанию

Опечатка `$order->total = 0;` в середине сервиса больше не случится. Для DTO и value objects это становится нормой, а не исключением.

```php
// 8.2: весь класс иммутабилен — даже $this->x = … внутри конструктора запрещено
readonly class Money
{
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new CurrencyMismatch();
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

Плюс: в мире `Fiber` и асинхронных задач `readonly`-сущности безопасны для «передачи по ссылке» без скрытых мутаций.

---

### 3. Constructor property promotion (8.0) — меньше строк, меньше «забыл присвоить»

```php
// Было: 8 строк, из них 4 — просто $this->x = $x
// Стало:
final class Payment
{
    public function __construct(
        public readonly string  $id,
        public readonly Money   $amount,
        public readonly OrderStatus $status,
        private   string        $reference,
    ) {}
}
```

Меньше кода — меньше мест для рассинхрона «конструктор присвоил, а геттер читает другое поле».

---

### 4. `match` (8.0) — исчерпывающий, без fall-through, работает как выражение

```php
$label = match ($order->status) {
    OrderStatus::Pending   => 'Ждёт отправки',
    OrderStatus::Shipped   => 'В пути',
    OrderStatus::Delivered => 'Доставлен',
};
```

- Нет случайного «проваливания» как в `switch`.
- Если добавите новый `case` в enum — компилятор (или статический анализатор) подскажет, что `match` не исчерпан, если не будет `default`.
- Это *выражение*, а не оператор: можно писать `$result = match (…) { … };` без `$x; break;`.

---

### 5. Nullsafe operator `?->` (8.0) — конец пирамиде null-проверок

```php
// Было:
$city = null;
if ($order !== null && $order->address !== null) {
    $city = $order->address->city;
}

// Стало:
$city = $order?->address?->city;   // null на любом уровне — и всё, дальше null
```

Читаемо, и не нужно оборачивать каждое звено в `if`.

---

### 6. Union / DNF-типы (8.0 – 8.2) — точный контракт

```php
// 8.0: union
function format(string|int $value): string { … }

// 8.2: DNF — «int ИЛИ (Stringable)», а не «int ИЛИ Stringable ИЛИ …»
function render(int|Stringable $payload): string
{
    return $payload instanceof Stringable
        ? $payload->__toString()
        : (string) $payload;
}
```

Контракт становится *точным*: вы передаёте то, что метод реально обрабатывает, а не «ну, может, и object сработает».

---

### 7. `#[\Override]` (8.2) — ловит опечатки в сигнатуре

```php
interface Repository
{
    public function find(string $id): ?Entity;
}

final class OrderRepository implements Repository
{
    #[\Override]
    public function find(string $id): ?Order
    {
        // …
    }
}
```

Без `#[\Override]` опечатка `public function findd(string $id)` создаёт *новый* метод, а не переопределяет старый — и `find()` молча возвращает `null` от родительского. Аннотация превращает это в compile-time error.

---

### 8. Named arguments (8.0) — читаемость на стороне вызова

```php
$builder->addConstraint(
    column: 'email',
    unique: true,
    nullable: false,
    onError: OnViolation::Log,   // 4-й параметр — без гугла «а что там 4-й?»
);
```

Особенно ценно для конструкторов и фабрик с 4+ параметрами, где порядок — источник багов.

---

## Что я бы *не* ставил в топ для «чистоты»

- **JIT** — это про скорость, не про чистоту.
- **Fibers** — архитектурно важно, но это инструмент для I/O-интенсивных сценариев, а не для «чистого кода» как такового.
- **Property hooks (8.4, experimental)** — обещают заменить геттеры/сеттеры, но пока нестабильны и не вошли в mainstream-практику.

Если резюмировать одним принципом: PHP 8+ дала язык *статических гарантий* (типы, enum, readonly, `#[\Override]`), которые раньше приходилось обеспечивать конвенциями, линтерами или ревью. Это сдвигает баги от рантайма к времени написания кода — и именно это, на мой взгляд, и есть «надёжность».

Хотите, разберём одну из этих фич глубже на контексте Symfony + Doctrine?

> ⏱ 18.3s · prompt 1133 tok · output 2411 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о системе типов в PHP: strict_types, приведения типов, ловушки, примеры где типизация спасает и удивляет. Это продолжение разговора о PHP 8+. Нужно дать глубокий, практичный ответ с кодом.

Структурирую так:
1. Как устроена система типов — ковариантность, контравариантность, union types, mixed, void, never
2. strict_types — что именно она делает, как работает на уровне файла
3. Приведения типов — коварные места (bool, int, float, string, null)
4. Ловушки на практике — конкретные примеры
5. Где типизация спасает — реальные сценарии
6. Где удивляет — коварные моменты

Ключевые моменты:
- strict_types=1 влияет только на вызовы ФУНКЦИЙ ИЗ ТЕКУЩЕГО ФАЙЛА, не на сам файл где функция определена. Это коварно.
- 1 == "1" → true, 0 == "" → true (до 8.0), 0 == "abc" → false (до 8.0), с 8.0: 0 == "abc" → false, но 0 == "" → false (изменили!)
- На самом деле: PHP 8.0 изменило сравнение 0 == "abc" на false (раньше было true). А 0 == "" было true до 8.0, в 8.0 стало false.
- Float: 0.1 + 0.2 !== 0.3
- int overflow: на 32-битных системах int — 32 бита
- "123" + "456" = 579 (string to int)
- "123" + "abc" = 123 (в PHP 8 это warning, но всё равно работает как 123)
- null: null == "" → false (в 8.0), null == 0 → false (в 8.0)
- bool: "0" == false → false (в 8.0), но "0" == "" → false
- empty() поведение
- typeof vs instanceof
- mixed type в 8.0
- never type в 8.1
- void — нельзя return значение

strict_types коварство:
- declare(strict_types=1); в файле A — влияет на вызовы функций из файла A. Если файл A вызывает функцию из файла B, то строгость определяется файлом A.
- Но если файл B вызывает функцию из файла C, то строгость определяется файлом B.
- Это означает: один и тот же вызов $f(1.0) может быть ok в одном файле и TypeError в другом.

Приведения коварные:
- is_int("123") → false, но "123" + 1 → 124
- filter_var("hello", FILTER_VALIDATE_INT) → false
- (bool)"0" → false, (bool)"hello" → true
- "1e5" — это "100000" или "1e5"? intval("1e5") → 1
- 0.1 + 0.2 = 0.30000000000000004
- JSON: json_decode('{"a": 1}', true)['a'] + 0.5 — int vs float
- Array keys: $arr["0"] и $arr[0] — один и тот же ключ
- String to number: "100" + 50 = 150, но "100abc" + 50 — в PHP 8 это TypeError? Нет, это 150 с warning. В 8.0: "100abc" + 50 → 150 (warning). А "abc" + 1 → 1 (warning).
- На самом деле в PHP 8: "abc" + 1 даёт warning "A non well formed numeric string encountered" и результат 1. А "100abc" + 1 → 100.

Ловушки:
- $x = null; $x ?? "default" — ок, но $x = 0; $x ?? "default" → 0 (нулевые значения НЕ null)
- $x = ""; if ($x) { } — пустая строка falsy
- match vs switch: switch("1") case 1: — match("1") case 1: — match использует ===, switch использует ==. Это ОГРОМНАЯ разница!
- $arr = ["a" => 1, 1 => "b"]; echo $arr[1]; → "b" (string key "1" и int key 1 — разные!)
- На самом деле: $arr = [1 => "a", "1" => "b"]; — в PHP это ОДНИ И ТОТ ЖЕ ключ, потому что int и numeric string эквивалентны для array keys. echo $arr[1] → "b" (последний перезаписал).
- Но $arr = [1.0 => "a", 1 => "b"]; — тоже один ключ, 1.0 превращается в 1.
- A non-well-formed numeric string: "  123" + 1 — лидирующие пробелы ок, но "123 " (хвостовые) — warning в PHP 8.

Строгие типы:
- int, float, string, bool, array, callable, object, null, mixed
- Union types: int|string
- Intersection types (8.1): Countable&Iterator
- DNF types (8.2): (int|Stringable)
- readonly properties (8.1)
- readonly classes (8.2)
- never type (8.1)
- void type
- Enums (8.1)

Ковариантность: return type можно сужать
Контравариантность: parameter type можно расширять

Примеры где спасает:
- strict_types + type hints: нельзя передать 1.0 вместо int
- readonly: нельзя случайно изменить
- #[\Override]: ловит опечатки
- Union types: точный контракт
- Enums: нет magic strings
- never: функция не возвращает контроль

Примеры где удивляет:
- strict_types — на уровне файла, не функции
- 0 == "abc" изменилось в 8.0
- "1e5" как string
- float precision
- array keys: 1, "1", 1.0 — один ключ
- bool: "0" is falsy, но "hello" is truthy
- null coalescing vs zero

Давай структурирую ответ хорошо, с кодом, и сделаю его практичным.

</details>

**Кандидат:**

# Система типов PHP: как она устроена и где кусается

## 1. Лексика типов

PHP — это *weakly typed* язык с *type coercion*. В 8.x добавили:

| Тип | Появился | Суть |
|---|---|---|
| `int`, `float`, `string`, `bool`, `null`, `mixed` | всегда / 8.0 | Базовые. `mixed` = «любой, включая `null`» |
| `void` | 7.1 | «вернуть нельзя ничего» |
| `callable`, `object`, `array`, `iterable` | 7.2 | Структурные |
| Union `int\|string` | 8.0 | «или то, или то» |
| Intersection `Countable&Iterator` | 8.1 | «и то, и то одновременно» |
| DNF `(int\|Stringable)` | 8.2 | Точные формулы, не плоские union |
| `never` | 8.1 | «функция не возвращает контроль» |
| `readonly` | 8.1/8.2 | Не тип, но меняет семантику свойства/класса |
| `enum` | 8.1 | Закрытый набор значений с payload |

Ковариантность: `return type` можно **сужать** в переопределении.
Контравариантность: `parameter type` можно **расширять**.

```php
interface Logger { public function log(string $msg): void; }

final class DbLogger implements Logger
{
    // return: void → void (нельзя расширить)
    // param:  string → string (нельзя сузить)
    public function log(string $msg): void { /* … */ }
}
```

---

## 2. `strict_types`: что реально делает и где коварно

```php
// utils.php
declare(strict_types=1);   // ← влияет ТОЛЬКО на вызовы, сделанные ИЗ ЭТОГО файла

function add(int $a, int $b): int { return $a + $b; }
```

```php
// app.php  — НЕТ strict_types
require 'utils.php';
add(1, 2);     // OK
add(1.0, 2);   // OK — coercion 1.0 → 1, потому что вызов сделан из app.php без strict
```

```php
// app2.php
declare(strict_types=1);   // ← ТЕПЕРЬ строгость включена для этого файла
require 'utils.php';
add(1, 2);     // OK
add(1.0, 2);   // TypeError: Argument #1 ($a) must be of type int, float given
```

**Ключевая коварность:** строгость определяется **файлом, который *вызывает***, а не файлом, где функция *объявлена*. Одна и та же функция `add()` ведёт себя по-разному в зависимости от того, из какого файла её вызывают. Это не баг, но первый источник «а почему у одного разработчика работает, а у другого — нет».

### Практический вывод

`declare(strict_types=1);` — **первая строка каждого файла** (после `<?php`). Без этого вы живёте в режиме «всё можно подлить».

---

## 3. Приведения типов: карта минных полей

### 3.1 Числа и строки

```php
// PHP 8.0+
"123" + 1;          // 124 — строка → int, без предупреждения
"100abc" + 1;       // 101 — warning "non well formed numeric string", но работает
"abc" + 1;          // 1    — то же самое, warning
"1e5" + 0;          // 1    — НЕ 100000! intval("1e5") = 1
"0x1A" + 0;         // 0    — hex не распознаётся

// Float — классика
0.1 + 0.2 === 0.3;  // false
0.1 + 0.2;          // 0.30000000000000004
```

### 3.2 Булевы и строки (с 8.0 — изменено)

```php
// До 8.0:  "abc" == 0  → true   (какая бы строка ни была)
// С 8.0:
"abc" == 0;   // false — строка больше не превращается в 0
"0"   == 0;   // true  — но "0" → 0
""      == 0; // true  (до 8.0) → false (с 8.0!)
null    == 0; // false (до 8.0: false, но null == "" → true до 8.0, с 8.0 → false)

// bool-контекст
(bool) "0";     // false — пустая строка и "0" — falsy
(bool) "";      // false
(bool) "hello"; // true
(bool) 0;       // false
(bool) "1e5";   // true
```

### 3.3 Array keys: `1`, `"1"`, `1.0` — один и тот же ключ

```php
$arr = [1 => 'int', 1.0 => 'float', '1' => 'string'];
echo count($arr);  // 1
echo $arr[1];      // 'string' — последний перезаписал
```

Но:

```php
$arr = [0 => 'zero', '0' => 'zero_str'];
echo count($arr);  // 1 — 0 и "0" тоже эквивалентны
```

А вот:

```php
$arr = ["abc" => 1, 123 => 2];
echo $arr["abc"];  // 1 — строковый ключ
echo $arr[123];    // 2 — int-ключ
// Это РАЗНЫЕ ключи. Но "123" и 123 — ОДИН ключ.
```

### 3.4 `null` vs «пустое значение»

```php
$value = 0;
echo $value ?? 'default';  // 0 — НЕ default, ?? проверяет null, а не falsy

$value = "";
echo $value ?: 'default';  // 'default' — : проверяет falsy
```

`??` и `:` — разные вещи, и путаница между ними — один из самых частых багов.

---

## 4. `match` vs `switch`: разница, которая убивает

```php
$status = "shipped";

// switch — loose comparison (==)
switch ($status) {
    case "shipped": echo "A"; break;
    case 1:         echo "B"; break;   // Никогда не сработает
}

// match — strict comparison (===)
match ($status) {
    "shipped" => 'A',
    1         => 'B',   // Никогда не сработает
};

// А вот тут разница:
$x = "1";
match ($x) {
    1 => 'int one',     // НЕ сработает, "1" !== 1
    "1" => 'string one',// Сработает
    default => 'other'
};

switch ($x) {
    case 1:   echo 'int one'; break;   // Сработает! "1" == 1
    default:  echo 'other';
}
```

Это, пожалуй, **самая частая ловушка** при миграции с `switch` на `match`.

---

## 5. Где типизация реально спасает

### 5.1 Строгая сигнатура + `strict_types`

```php
declare(strict_types=1);

final class Money
{
    public function __construct(
        public readonly int $amount,    // centavos
        public readonly string $currency,
    ) {}

    public function add(Money $other): Money
    {
        if ($other->currency !== $this->currency) {
            throw new DomainException('Mismatch');
        }
        return new Money($this->amount + $other->amount, $this->currency);
    }
}

$eur = new Money(100, 'EUR');
$eur->add(1.5);  // TypeError: Argument #1 ($other) must be of type Money, float given
```

Без `strict_types` и type hint: `$eur->add(1.5)` → PHP молча создаёт `Money(1, '1.5')`. С типами — мгновенный `TypeError`.

### 5.2 `#[\Override]` ловит опечатку в сигнатуре

```php
interface OrderRepository
{
    public function findById(string $id): ?Order;
}

final class PgOrderRepository implements OrderRepository
{
    // Опечатка: findId вместо findById
    // БЕЗ #[\Override]: PHP создаёт НОВЫЙ метод, findById() не переопределён,
    //   вызов $repo->findById("x") → Fatal error: undefined method (если в родителе нет)
    //   или возвращает null (если в родителе есть дефолт)
    // С #[\Override]: compile-time error — сразу видно, что сигнатура не совпала.
    #[\Override]
    public function findById(string $id): ?Order
    {
        // …
    }
}
```

### 5.3 `never` закрывает «молчаливый return»

```php
function assertActive(User $user): void
{
    if (!$user->active) {
        throw new InactiveException();
    }
}

// В PHP 8.1+ можно:
function dieWith(string $msg): never
{
    error_log($msg);
    exit(1);
}

$result = getUser() ?? dieWith('no user');  // Стatically: getUser() | never → getUser()
```

Анализатор понимает, что после `??` функция не вернётся — и `null` в `$result` невозможен.

### 5.4 Union + `match` — исчерпывающий контракт

```php
enum Role: string {
    case Admin    = 'admin';
    case Editor   = 'editor';
    case Viewer   = 'viewer';

    public function canEdit(): bool
    {
        return match ($this) {
            self::Admin, self::Editor => true,
            self::Viewer              => false,
            // Если добавите case Role::Auditor — анализатор (PHPStan level 8+)
            // скажет: match не исчерпан enum. Нет молчаливого default.
        };
    }
}
```

---

## 6. Где типизация удивляет (и как это использовать)

### 6.1 `mixed` — это не `any` из TypeScript

```php
declare(strict_types=1);

function process(mixed $data): void
{
    // $data может быть: int, float, string, bool, array, object, null — ЛЮБОЕ
    // Но: process(42) — OK, process("x") — OK
    // Это НЕ значит «я не знаю тип». Это значит «контракт не ограничивает».
    // PHPStan level 8: $data->foo — error "cannot access property on mixed"
}
```

`mixed` — это *отказ от контракта*, а не «универсальный ключ». Используйте, только если реально не знаете (JSON-декодер, generic-фабрика).

### 6.2 `int` vs `float` в JSON

```php
$json = '{"amount": 100}';
$decoded = json_decode($json, true);

var_dump($decoded['amount']);  // int(100)

$json = '{"amount": 100.0}';
$decoded = json_decode($json, true);

var_dump($decoded['amount']);  // float(100)

// Если в коде:
function pay(int $cents): void { /* … */ }
pay($decoded['amount']);  // OK в strict? Нет: float → int — TypeError!
```

JSON `100` → `int`, `100.0` → `float`. Если у вас `int $cents` и приходит `100.0` — **TypeError**. Ловушка при работе с внешними API.

### 6.3 `bool` из внешнего мира

```php
// Из HTTP-заголовка, из DB, из JSON
$headers = ['X-Active' => 'false'];

$isActive = (bool) $headers['X-Active'];  // true! "false" — непустая строка
$isActive = filter_var($headers['X-Active'], FILTER_VALIDATE_BOOLEAN); // false

// С 8.0:
"false" == false;  // false — строка не превращается в false
"0"     == false;  // false — тоже! (в 7.x было true)
```

**Правило:** никогда не делайте `(bool)$string` для внешнего ввода. Используйте `filter_var` или явный маппинг.

### 6.4 `readonly` и наследование

```php
readonly class BaseEntity
{
    public function __construct(
        public readonly int $id,
    ) {}
}

final class Order extends BaseEntity  // ← PHP 8.2: readonly class НЕЛЬЗЯ наследовать
{
    public function __construct(
        public readonly string $status,
    ) {
        parent::__construct(1);
    }
}
// Fatal: Class Order cannot extend readonly class BaseEntity
```

`readonly class` — это *лист*. Если хотите наследование — только `readonly` на свойствах.

---

## 7. Чек-лист «безопасного» файла

```php
<?php

declare(strict_types=1);              // 1. Стригость — первая строка

namespace App\Domain\Order;

final class Money                      // 2. final, если нет причин наследовать
{
    // 3. readonly на свойствах — иммутабельность по умолчанию
    public function __construct(
        public readonly int    $amount,
        public readonly string $currency,
    ) {}                               // 4. Promotion — меньше строк, меньше багов

    // 5. Union / DNF — точный контракт
    public function format(bool $withSymbol = true): string
    {
        // 6. match, а не switch — строгое сравнение
        $code = match ($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            'RUB' => '₽',
            default => "{$this->currency} ",
        };

        return $code . number_format($this->amount / 100, 2);
    }

    // 7. #[\Override] при реализации интерфейса
    // 8. never / void — явный контракт «не возвращаю»
    public function validate(): void
    {
        if ($this->amount < 0) {
            throw new \DomainException('Amount must be non-negative');
        }
    }
}
```

---

## Резюме

| Аспект | Что даёт | Где коварно |
|---|---|---|
| `strict_types` | Запрещает implicit coercion при вызове | Включается **на файл-вызывающий**, не на файл-объявляющий |
| Union / DNF types | Точный контракт «что принимаю / что верну» | `mixed` — не «любой», а «отказ от контракта» |
| `readonly` | Убирает мутации, thread-safety для Fibers | `readonly class` нельзя наследовать |
| `match` | Строгое сравнение, исчерпывающий | Миграция с `switch`: `"1" === 1` vs `"1" == 1` |
| `never` / `void` | Статический анализ понимает «тут не вернутся» | `void` ≠ `null` — в `void` нельзя return ничего |
| Coercion | Удобство на входе (CLI, JSON) | `int` vs `float` в JSON, `"false"` → `bool`, `"0"` → `bool` |

Если хотите, следующим шагом можем разобрать, как эти принципы встраиваются в **Symfony-сервис + Doctrine-репозиторий** — где типизация сталкивается с реальностью `EntityManager` и `Query\Expr`.

> ⏱ 39.9s · prompt 2811 tok · output 5392 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

Here's a thinking process that leads to the suggested response about exception handling in PHP:

1.  **Deconstruct the Prompt:**
    *   **Topic:** Exception handling in PHP applications.
    *   **Key questions:**
        *   How to design custom exception hierarchies?
        *   How to wrap third-party/vendor exceptions?
        *   What constitutes a method's contract regarding exceptions?
        *   What are common anti-patterns?
    *   **Style/Persona:** Senior/Architect PHP developer, pragmatic, values Clean Architecture/Hexagonal principles, direct, uses code examples, no fluff. (Based on previous turns: confident, structured, honest about limitations).

2.  **Core Philosophy (The "Why" and "What"):**
    *   Exceptions are for *exceptional* circumstances, not control flow.
    *   Domain logic shouldn't know about infrastructure (DB, HTTP, third-party APIs).
    *   Separation of concerns: Domain Exceptions vs. Infrastructure Exceptions vs. Application/Transport Exceptions.

3.  **Structuring the Response:**
    *   **Introduction:** Brief statement of the core principle (Separation of Concerns / DDD).
    *   **1. Exception Hierarchies (Designing the structure):**
        *   Base exception.
        *   Domain exceptions.
        *   Infrastructure exceptions.
        *   Application/Handling exceptions.
        *   *Code example showing the tree.*
    *   **2. Wrapping Third-Party Exceptions (The "Adapter" approach):**
        *   Catch vendor exceptions at the boundary.
        *   Translate to Domain/Application exceptions.
        *   *Code example (e.g., wrapping a payment gateway or DB exception).*
    *   **3. Method Contracts (What should a method promise?):**
        *   Document exceptions (PHPDoc `@throws`).
        *   Fail fast.
        *   Don't swallow exceptions silently.
        *   *Code example of a good contract.*
    *   **4. Anti-patterns (What to avoid):**
        *   Catching `Throwable` everywhere (sloppy).
        *   Using exceptions for control flow (e.g., `NotFound` in a loop).
        *   Swallowing exceptions (empty `catch`).
        *   Leaking infrastructure exceptions to the UI/Controller.
        *   *Code examples for each.*
    *   **Conclusion:** Summary thought.

4.  **Drafting the Content & Refining Code Examples:**

    *   *Hierarchy:*
        ```php
        // src/Exceptions/
        class DomainException extends \Exception {} // Base for domain rules
        class InfrastructureException extends \Exception {} // Base for DB, APIs

        // src/Domain/Order/Exceptions/
        class InsufficientFundsException extends DomainException {}
        class OrderAlreadyShippedException extends DomainException {}
        ```
        *Self-Correction during drafting:* Keep it simple. Don't over-engineer a 10-level tree. A 2-3 level tree (Base -> Domain/Infra -> Specific) is standard.

    *   *Wrapping (The crucial part):*
        Imagine an `OrderRepository` or `PaymentGateway`.
        ```php
        // App/Infrastructure/Adapters/StripeGateway.php
        public function charge(string $orderId): void {
            try {
                $this->stripeClient->charges->create([...]);
            } catch (\Stripe\StripeException $e) {
                // Translate to Domain
                if ($e instanceof \Stripe\Exception\CardException) {
                    throw new PaymentDeclinedException('Card declined', $e);
                }
                // Otherwise, wrap in generic Infra exception
                throw new GatewayCommunicationException('Stripe unreachable', $e);
            }
        }
        ```
        *Key point:* Always pass `$previousException` to the constructor. Stack traces must not be lost.

    *   *Method Contracts:*
        What is a contract? "I will do X, and if Y happens, I will throw Z."
        Use PHP 8 features where appropriate.
        ```php
        /**
         * @throws InsufficientFundsException if balance is less than required
         * @throws OrderNotFoundException if order does not exist
         */
        public function process(OrderId $id): void { ... }
        ```
        *Rule:* Catch what you can *handle* (retry, log, transform). Let it bubble up if you can't.

    *   *Anti-patterns (The meat of the prompt):*
        1.  **`catch (\Throwable $e) { /* do something */ }`** -> Hides bugs, breaks error reporting.
        2.  **Control Flow:** `try { find(); } catch (NotFound) { return null; }`. *Counter-argument:* Actually, in repositories, returning `null` is often better than throwing `NotFound` for simple lookups, but for business rules (e.g., `activateUser()`), throwing is correct. Let's use a better anti-pattern: `while (true) { try { ... } catch (Exception) { sleep(1); } }`. Or catching exceptions just to check `instanceof`. Let's stick to: *Catching exceptions just to check state* (e.g., `catch (FileNotFound) { if ($e->getMessage() === 'not found') ... }`).
        3.  **Swallowing:** `catch (Exception $e) {}`. The worst sin.
        4.  **Leaking `PDOException` to the Controller.** The Controller shouldn't know about Postgres.
        5.  **Catching in the wrong layer.** Catching DB exceptions in the Domain layer.

5.  **Review against Persona and Constraints:**
    *   *Tone:* Professional, direct, slightly opinionated (as a senior dev should be).
    *   *Formatting:* Markdown, clear headings, code blocks.
    *   *PHP 8+ context:* Use typed exceptions, constructor promotion in exceptions (optional but good practice).
    *   *Self-Correction on Exceptions:* In PHP, exceptions implement `Throwable`. `Error` and `Exception` are separate. Catching `Throwable` is necessary *at the very top level* (Framework kernel), but never in the middle.

6.  **Final Polish (Russian Language Nuances):**
    *   Ensure terms are correctly translated (e.g., "control flow" -> "поток управления", "wrapping" -> "оборачивание/транслирование").
    *   Make the code examples idiomatic PHP.

7.  **Generate Output.** (Proceeding to generate the Russian response based on these structured thoughts).

</details>

**Кандидат:**

Работа с исключениями в PHP — это один из лучших индикаторов того, насколько архитектура приложения «чистая». Плохо написанные `try-catch` блоки убивают DDD (Domain-Driven Design) и превращают код в хаос, где доменная логика знает о деталях работы с базой данных, а контроллеры парсят сообщения об ошибках Stripe или Postgres.

Вот как я проектирую систему исключений и на что опираюсь.

---

### 1. Иерархия исключений: Разделение по слоям

Главное правило: **Домен не должен знать об Инфраструктуре.**
Если в домене (Domain) мы ловим `PDOException` или `\Stripe\Exception\ApiErrorException`, мы уже проиграли.

Моя стандартная иерархия выглядит так (3 уровня):

```php
// src/Shared/Exceptions/
abstract class AppException extends \Exception {}

// Исключения бизнеса (домена). Их знают все слои.
class DomainException extends AppException {}

// Исключения инфраструктуры (внешнего мира). Их знают только адаптеры.
class InfrastructureException extends AppException {}
```

**Пример доменных исключений (Domain):**
```php
// src/Domain/Order/Exceptions/
class OrderAlreadyShippedException extends DomainException {}
class InsufficientStockException extends DomainException {}

// Использование конструкторов PHP 8+ для чистоты:
final class InsufficientStockException extends DomainException
{
    public function __construct(
        public readonly string $productId,
        public readonly int    $requestedQty,
        public readonly int    $availableQty
    ) {
        parent::__construct(
            sprintf(
                'Not enough stock for product %s. Requested: %d, Available: %d',
                $productId, $requestedQty, $availableQty
            )
        );
    }
}
```

**Пример инфраструктурных (Infra):**
```php
// src/Infrastructure/Exceptions/
class GatewayCommunicationException extends InfrastructureException {}
class DatabaseConstraintViolation extends InfrastructureException {}
```

---

### 2. Оборачивание чужих исключений (Translation)

Точки входа во внешний мир (API, БД, файловая система) — это **границы (Boundaries)**. На этих границах чужие исключения перехватываются и **транслируются** в наши.

**Правило:** Всегда передавайте исходное исключение в `$previous` (второй параметр конструктора `Exception`), чтобы не потерять Stack Trace.

```php
// src/Infrastructure/Adapters/StripePaymentGateway.php
final class StripePaymentGateway implements PaymentGatewayInterface
{
    public function charge(string $orderId, int $amountCents): void
    {
        try {
            $this->stripeClient->charges->create([
                'amount' => $amountCents,
                'source' => $orderId,
            ]);
        } 
        // 1. Ловим конкретное исключение вендора
        catch (\Stripe\Exception\CardException $e) {
            // 2. Транслируем в доменное (бизнес знает, что карта отклонена)
            throw new PaymentDeclinedException('Card declined by issuer', previous: $e);
        }
        
        // 3. Ловим общее исключение API
        catch (\Stripe\StripeException $e) {
            // 4. Транслируем в инфраструктурное (бизнес знает, что gateway упал)
            throw new GatewayCommunicationException('Failed to reach Stripe', previous: $e);
        }
        
        // НИКАКОГО catch (\Throwable $e) или catch (\Exception $e) здесь!
    }
}
```

**Почему это важно?**
Сервис `OrderService` (домен) может сказать:
```php
try {
    $this->paymentGateway->charge($id, $amount);
} catch (PaymentDeclinedException $e) {
    // Бизнес-логика: отменить заказ, написать пользователю
} catch (GatewayCommunicationException $e) {
    // Бизнес-логика: НЕ отменять заказ! Попробовать позже (retry) или выставить статус "payment_pending"
}
```
Если бы мы бросили `StripeException` наружу, доменному сервису пришлось бы знать про классы Stripe.

---

### 3. Контракт метода

Контракт метода — это его сигнатура + документация + поведение в граничных случаях.

**Что должен гарантировать метод:**
1. **Fail Fast.** Если входные данные невалидны для бизнес-правил — бросаем исключение сразу, без создания БД-записей.
2. **Явные ожидания.** Метод либо успешно выполняет действие, либо бросает *конкретное* исключение из своей иерархии.
3. **Запрет на `null` в качестве сигнала ошибки.** `null` означает «сущность не существует» (например, в `findById`). Исключение означает «нарушено правило».

**Пример правильного контракта (PHPDoc + Код):**

```php
interface OrderRepository
{
    /**
     * @throws OrderNotFoundException if order with given ID does not exist.
     * Никогда не бросает DatabaseException наружу.
     */
    public function findById(string $id): Order;
    
    /**
     * @throws OrderAlreadyShippedException if order status is already 'SHIPPED'.
     * @throws InsufficientStockException if warehouse doesn't have enough items.
     * @throws GatewayCommunicationException if payment provider is unreachable.
     */
    public function process(Order $order): void;
}
```

---

### 4. Антипаттерны (Что я вырезаю на ревью)

#### Антипаттерн 1: Ловля `\Throwable` или `\Exception` в середине приложения
```php
// ПЛОХО
public function processOrder(Order $order): void
{
    try {
        $this->paymentGateway->charge();
        $this->inventory->reserve();
        $this->db->save($order);
    } catch (\Exception $e) {
        $this->logger->error($e->getMessage());
        // Что теперь делать? Откатить транзакцию? Отправить email? 
        // Это "ловля вслепую", которая скрывает баги.
    }
}
```
*Решение:* Ловите только те исключения, которые можете **обработать** (retry, fallback, логирование с алертом). Остальные пусть всплывут до контроллера/обработчика HTTP-запроса.

#### Антипаттерн 2: Использование исключений для потока управления (Control Flow)
```php
// ПЛОХО
public function findUserByEmail(string $email): ?User
{
    try {
        return $this->repo->findById($email); // Допустим, он бросает NotFound
    } catch (UserNotFoundException $e) {
        return null;
    }
}
```
*Почему плохо?* Это работает, но если `findById` иногда бросает `UserNotFoundException`, а иногда возвращает `null`, вы нарушаете принцип минимального удивления. 
*Правило:* Если сущность *может* отсутствовать — возвращайте `null` (или `Result`). Если сущность *обязана* быть, а её нет — бросайте исключение.

#### Антипаттерн 3: Проглатывание исключений (Empty Catch)
```php
// ПЛОХО
try {
    $this->cache->set($key, $value);
} catch (\Exception $e) {
    // "Ничего страшного, кэш упал, пойдём в БД"
}
```
*Почему плохо?* Если кэш постоянно падает, вы не узнаете об этом, пока БД не утонет в запросах.
*Решение:*
```php
try {
    $this->cache->set($key, $value);
} catch (\Throwable $e) {
    $this->logger->warning('Cache set failed', ['exception' => $e, 'key' => $key]);
    // Логгируем и идём дальше, если это действительно необязательная операция
}
```

#### Антипаттерн 4: Передача исключений в `catch` без `$previous`
```php
// ПЛОХО
catch (\Stripe\StripeException $e) {
    throw new PaymentException('Error: ' . $e->getMessage());
}
```
*Почему плохо?* Вы теряете Stack Trace оригинального исключения. Отладка превращается в квест "где ругнулся Stripe?".
*Решение:* `throw new PaymentException('Error: ' . $e->getMessage(), previous: $e);`

#### Антипаттерн 5: Контроллеры, которые знают об Инфраструктуре
```php
// ПЛОХО
try {
    $this->orderService->process();
} catch (PDOException $e) {
    return $this->json(['error' => 'DB Error', 'code' => 500]);
} catch (StripeException $e) {
    return $this->json(['error' => 'Payment Error', 'code' => 402]);
}
```
*Решение:* Контроллер ловит только `DomainException` (и, возможно, `InfrastructureException` для 500-ок), либо вообще ничего не ловит, а делегирует обработку глобальному Exception Handler фреймворка (Symfony, Laravel).

---

### Итоговая формула

1. **Домен** знает только `DomainException`.
2. **Инфраструктура** (репозитории, API-клиенты) перехватывает чужие ошибки и бросает `InfrastructureException` или `DomainException`.
3. **Приложение (Сервисы)** комбинирует логику и может переводить `InfrastructureException` в `DomainException`, если это нужно бизнесу.
4. **Контроллеры** (или глобальный Exception Handler) переводят наши исключения в HTTP-ответы (400, 402, 500).
5. **Всегда** передаём `$previousException` и **никогда** не ловим `\Throwable` в бизнес-логике.

> ⏱ 29.2s · prompt 6719 tok · output 3797 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает, какой принцип SOLID чаще всего нарушается в реальном PHP-коде. Нужно дать честный, практический ответ с примером из реальной жизни (Symfony/Doctrine контекст), а не абстрактный учебный пример.

Давайте подумаю, что реально чаще всего нарушается:

1. **SRP** (Single Responsibility) — очень частое нарушение, но оно размытое и его трудно "поймать" как конкретный баг.
2. **OCP** (Open/Closed) — частое нарушение через if/else цепочки и switch по типу.
3. **LSP** (Liskov Substitution) — нарушение через наследование, где дочерний класс меняет поведение. В PHP с Doctrine это очень частое — Entity с наследованием, где дочерняя сущность меняет семантику.
4. **ISP** (Interface Segregation) — "fat interfaces" — репозитории с 20 методами.
5. **DIP** (Dependency Inversion) — зависимость от конкретных классов, а не абстракций.

Что реально чаще всего? В PHP-коде (Symfony, Laravel) я бы сказал:

**OCP** — через if/else/switch по типу сущности/статуса/типа платежа. Это самое видимое и самое частое нарушение. Каждый раз, когда появляется новый тип — идёшь и добавляешь ветку. Классический пример: `if ($type === 'visa') ... elseif ($type === 'mastercard') ... elseif ($type === 'crypto') ...`

Но также **LSP** — через Doctrine inheritance (Single Table, Joined Table), где дочерняя entity меняет поведение методов родителя. Это очень специфично для PHP/Doctrine и очень больно бьёт.

Я думаю, стоит выбрать **OCP** как самый частый и универсальный, потому что:
- Его видно в каждом проекте
- Он порождает другие нарушения (SRP, иногда LSP)
- Рефакторинг с ним самый наглядный
- Он напрямую связан с DDD (Strategic pattern: Command, Strategy, State)

Но я могу также упомянуть LSP как "второго по частоте" в контексте Doctrine.

Давайте сделаю так: основной фокус на OCP, с конкретным Symfony/Doctrine примером. Покажу "до" и "после".

Типовой сценарий: обработка платежей или обработка статусов заказов. Ветвление по типу.

Пример "до":
- Service с методом `processPayment()` где if/else по типу платежа (card, bank_transfer, crypto, gift_card)
- Каждый новый тип — новая ветка в этом же методе
- Метод разрастается до 200+ строк

Пример "после":
- Strategy pattern / полиморфизм
- Interface `PaymentProcessor`
- Конкретные реализации: `CardPaymentProcessor`, `BankTransferProcessor`
- Registry / Factory для выбора
- Новый тип = новый класс, ни одну строку в существующем не трогаешь

Также покажу более "субтильный" пример — нарушение OCP через switch по enum-статусу в домене.

Структурирую ответ:
1. Короткий ответ: OCP
2. Почему именно он (а не SRP или LSP)
3. Типовой "до" (реалистичный PHP/Symfony код)
4. Рефакторинг "после"
5. Когда OCP — НЕ решение (антинаттерн "стратегий ради стратегий")
6. Краткое упоминание LSP как второго по частоте

Пишу на русском, как и запрос.

</details>

**Кандидат:**

## OCP — самый частый нарушитель в реальном PHP-коде

Если SRP — это «размытое» нарушение, которое видно, когда читаешь код, то **Open/Closed Principle** нарушается *каждую неделю* в каждом проекте, и видно это сразу: прилетает тикет «добавить ещё один тип X», и разработчик идёт в один и тот же метод добавлять `elseif`.

Почему именно OCP, а не другие:

- **SRP** нарушают осознанно («ну лень тянуть в отдельный класс») — это вопрос дисциплины.
- **LSP** нарушается реже, потому что требует наследования, а в PHP/Doctrine его используют аккуратно.
- **DIP** в Symfony решается из коробки (DI-контейнер).
- **OCP** нарушается *структурно*: язык и фреймворк не мешают, фичу добавляют быстро, а расплата наступает через 6 месяцев, когда в `processPayment()` уже 14 веток и два разработатора одновременно правят этот файл.

---

## Типовое «до»: платёжный сервис

Ситуация: есть 4 способа оплаты. Каждый новый — это новая ветка. Метод разрастается.

```php
// src/Application/Service/OrderPaymentService.php

final class OrderPaymentService
{
    public function __construct(
        private StripeClient $stripe,
        private BankGateway  $bank,
        private CryptoClient $crypto,
        private GiftCardRepo $giftCards,
        private LoggerInterface $logger,
    ) {}

    public function charge(Order $order, PaymentMethod $method): void
    {
        // 1) Валидация — общая
        if (!$order->isAwaitingPayment()) {
            throw new OrderNotPayableException();
        }

        // 2) Ветвление по типу — ПЛОХО
        switch ($method->type) {

            case 'card':
                $result = $this->stripe->charge(
                    amount: $order->totalCents,
                    source: $method->reference,
                    currency: $order->currency,
                );
                if ($result['status'] !== 'succeeded') {
                    throw new PaymentDeclinedException(
                        reason: $result['error']['message'] ?? 'unknown'
                    );
                }
                $this->logger->info('Card charge OK', ['order' => $order->id]);
                break;

            case 'bank_transfer':
                $ref = $this->bank->createTransfer(
                    amount: $order->totalCents,
                    iban:   $method->reference,
                );
                // банк асинхронный — просто сохраняем ref
                $order->markBankTransferPending($ref);
                break;

            case 'crypto':
                $wallet = $this->crypto->createInvoice(
                    amount: $order->totalCents,
                    token:  'USDT',
                );
                $order->markCryptoPending($wallet['address']);
                $this->logger->info('Crypto invoice', ['addr' => $wallet['address']]);
                break;

            case 'gift_card':
                $card = $this->giftCards->findOneBy(['code' => $method->reference]);
                if ($card === null || $card->balanceCents < $order->totalCents) {
                    throw new InsufficientBalanceException();
                }
                $card->deduct($order->totalCents);
                $this->giftCards->save($card);
                $order->markFullyPaid('gift_card');
                break;

            default:
                throw new UnsupportedPaymentTypeException($method->type);
        }

        // 3) Сохранение — общая
        $this->saveOrder($order);
    }
}
```

### Что здесь нарушено

| Нарушение | Где |
|---|---|
| **OCP** | Чтобы добавить `paypal` — правим `switch`, метод, конструктор (новый клиент). Каждый новый тип — правка существующего класса. |
| **SRP** (вследствие) | Метод знает детали Stripe, банка, крипто-кошелька, подарочных карт. |
| **Testability** | Чтобы юнит-тестнуть `bank_transfer`, нужно мокать Stripe, Crypto, GiftCards. |
| **Распространение ошибок** | `InsufficientBalanceException` бросается внутри `case`, а не в домене подарочной карты. |

---

## Рефакторинг «после»

Идея: каждый тип платежа знает, как себя начислять. Метод `charge()` становится **тонким оркестратором**.

### Шаг 1 — Доменный контракт

```php
// src/Domain/Payment/ProcessorInterface.php

interface PaymentProcessor
{
    /**
     * Начисляет оплату.
     *
     * @throws PaymentDeclinedException  если платёж отклонён.
     * @throws GatewayCommunicationException если внешний сервис недоступен.
     *
     * Возвращает результат, который сервис сохранит в Order.
     */
    public function process(Order $order, PaymentMethod $method): PaymentResult;
}
```

```php
// src/Domain/Payment/PaymentResult.php

readonly class PaymentResult
{
    public function __construct(
        public readonly bool   $succeeded,
        public readonly ?string $externalRef = null,
        public readonly string  $settlementStatus = 'immediate', // immediate | pending
    ) {}
}
```

### Шаг 2 — Реализации (по одной на тип)

```php
// src/Infrastructure/Adapters/Stripe/StripePaymentProcessor.php

final class StripePaymentProcessor implements PaymentProcessor
{
    public function __construct(
        private StripeClient $stripe,
        private LoggerInterface $logger,
    ) {}

    public function process(Order $order, PaymentMethod $method): PaymentResult
    {
        try {
            $result = $this->stripe->charge(
                amount:   $order->totalCents,
                source:   $method->reference,
                currency: $order->currency,
            );
        } catch (\Stripe\Exception\CardException $e) {
            throw new PaymentDeclinedException(
                $e->getMessage(),
                previous: $e
            );
        } catch (\Stripe\StripeException $e) {
            throw new GatewayCommunicationException(
                'Stripe unreachable',
                previous: $e
            );
        }

        if ($result['status'] !== 'succeeded') {
            throw new PaymentDeclinedException(
                $result['error']['message'] ?? 'unknown'
            );
        }

        $this->logger->info('Stripe charge OK', ['order' => $order->id]);

        return new PaymentResult(
            succeeded:       true,
            externalRef:     $result['id'],
            settlementStatus: 'immediate',
        );
    }
}
```

```php
// src/Infrastructure/Adapters/Bank/BankTransferProcessor.php

final class BankTransferProcessor implements PaymentProcessor
{
    public function __construct(private BankGateway $bank) {}

    public function process(Order $order, PaymentMethod $method): PaymentResult
    {
        $ref = $this->bank->createTransfer(
            amount: $order->totalCents,
            iban:   $method->reference,
        );

        return new PaymentResult(
            succeeded:       true,
            externalRef:     $ref,
            settlementStatus: 'pending',   // ← асинхронный
        );
    }
}
```

```php
// src/Domain/GiftCard/GiftCardProcessor.php
// Доменный — не знает про Stripe, банк, крипто

final class GiftCardProcessor implements PaymentProcessor
{
    public function __construct(
        private GiftCardRepository $giftCards,
    ) {}

    public function process(Order $order, PaymentMethod $method): PaymentResult
    {
        $card = $this->giftCards->findOneByCode($method->reference)
            ?? throw new GiftCardNotFoundException($method->reference);

        if ($card->balanceCents < $order->totalCents) {
            throw new InsufficientBalanceException(
                balance: $card->balanceCents,
                required: $order->totalCents,
            );
        }

        $card->deduct($order->totalCents);
        $this->giftCards->save($card);

        return new PaymentResult(succeeded: true, settlementStatus: 'immediate');
    }
}
```

### Шаг 3 — Реестр + тонкий сервис

```php
// src/Application/Service/PaymentProcessorRegistry.php

final class PaymentProcessorRegistry
{
    /** @var array<string, PaymentProcessor> */
    private array $processors;

    /**
     * @param iterable<PaymentProcessor> $processors
     */
    public function __construct(iterable $processors)
    {
        foreach ($processors as $p) {
            $this->processors[$p->supportedType()] = $p;
        }
    }

    public function get(string $type): PaymentProcessor
    {
        return $this->processors[$type]
            ?? throw new UnsupportedPaymentTypeException($type);
    }
}
```

В Symfony это собирается автоматически через `iterable $processors` (DI-контейнер инжектит все реализацию `PaymentProcessor`):

```yaml
# config/services.yaml
App\Application\Service\PaymentProcessorRegistry:
    arguments:
        $processors:
            - '@App\Infrastructure\Adapters\Stripe\StripePaymentProcessor'
            - '@App\Infrastructure\Adapters\Bank\BankTransferProcessor'
            - '@App\Domain\GiftCard\GiftCardProcessor'
            - '@App\Infrastructure\Adapters\Crypto\CryptoProcessor'
```

Или ещё чище — через тег:

```yaml
# в services.yaml каждого адаптера
App\...\StripePaymentProcessor:
    tags: ['app.payment_processor']

# в реестре:
public function __construct(
    /** @param iterable<PaymentProcessor> */
    iterable $processors,
)
```
```yaml
App\Application\Service\PaymentProcessorRegistry:
    arguments:
        $processors: !tagged_iterator { tag: app.payment_processor }
```

Теперь **добавить `paypal`** = создать `PayPalProcessor implements PaymentProcessor` + одна строка в конфиге. Ни одну существующую строку не трогаем.

### Шаг 4 — Оркестратор (стало тонким)

```php
final class OrderPaymentService
{
    public function __construct(
        private PaymentProcessorRegistry $registry,
        private OrderRepository          $orders,
    ) {}

    public function charge(Order $order, PaymentMethod $method): void
    {
        if (!$order->isAwaitingPayment()) {
            throw new OrderNotPayableException();
        }

        $processor = $this->registry->get($method->type);  // OCP: выбор — данные, не код
        $result    = $processor->process($order, $method);

        match ($result->settlementStatus) {
            'immediate' => $order->markFullyPaid($method->type, $result->externalRef),
            'pending'   => $order->markAwaitingSettlement($result->externalRef),
        };

        $this->orders->save($order);
    }
}
```

### Что изменилось

| Аспект | До | После |
|---|---|---|
| Новый тип платежа | Правка `switch` + конструктора + импорты | Новый класс + тег в конфиге |
| Touched files | 1 файл (но 200 строк) | 1 новый файл, 1 строка конфига |
| Тестирование `bank_transfer` | Мокать Stripe, Crypto, GiftCards | Мокать только `BankGateway` |
| Исключения | Бросаются в `switch`, домен не знает | Бросаются в процессоре, доменный контракт явный |
| Параллельная разработка | Конфликты в одном файле | Разные файлы |

---

## Когда OCP — НЕ решение

Однажды на ревью я видел «стратегий» на 12 штук, где каждая — это 3 строки:

```php
final class VisaProcessor   implements PaymentProcessor { public function process(…){ return $this->generic->process(…); } }
final class MasterProcessor implements PaymentProcessor { public function process(…){ return $this->generic->process(…); } }
```

Это **антипаттерн "стратегий ради стратегий"**: вы разнесли то, что не имеет различий, и создали 12 классов, 12 тегов, 12 тестов — для одного и того же поведения.

**Критерий:** если две реализации `PaymentProcessor` делают одинаковую работу с разными параметрами — это **не** две стратегии, это **один** процессор с конфигурацией:

```php
final class CardProcessor implements PaymentProcessor
{
    public function __construct(
        private StripeClient $client,
        private string $network,   // 'visa' | 'mastercard' | 'amex'
    ) {}

    public function process(Order $order, PaymentMethod $method): PaymentResult
    {
        // одна логика, $this->network — параметр
    }
}
```

Правило: **полиморфизм там, где поведение различается по логике; параметризация там, где различается только конфигурация.**

---

## Bônus: LSP как «второй по частоте» в Doctrine

Если OCP — самое частое, то **Liskov** бьёт через наследование сущностей:

```php
#[ORM\Entity]
class Product
{
    public function priceWithDiscount(): float
    {
        return $this->price * (1 - $this->discount);
    }
}

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
class BundleProduct extends Product
{
    // «Бандл — сумма цен компонентов»
    public function priceWithDiscount(): float
    {
        // Игнорирует $this->discount, считает по-своему
        return $this->components->reduce(0, fn($s, $c) => $s + $c->price);
    }
}
```

Клиент вызывал `product->priceWithDiscount()` ожидая «цену со скидкой», а для `BundleProduct` получал «сумму без скидки». Это **нарушение LSP**: дочерний класс меняет *семантику* метода, а не расширяет.

Решение: не наследовать. Вынести `Priceable`-интерфейс, а `Bundle` — отдельная сущность со своим контрактом. `Product` и `Bundle` — не родитель/дочь, а **соседи** через композицию.

---

## Итог

OCP нарушается чаще всего, потому что он **дешёвый в нарушении и дорогой в исправлении**:

- Нарушить — добавить `elseif` за 2 минуты.
- Исправить — через 6 месяцев, когда в `switch` 14 веток, три разработатора правят один файл, и тесты бьются друг о друга.

Поэтому я считаю, что **один `elseif` — нормально, три — уже сигнал, пять — рефакторинг в спринте.** Не дожидаясь шестого.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

# Можно ли нарушать SOLID?

Да. И иногда это *правильное* решение.

SOLID — это не заповеди, а **направления поиска**. «S» означает «найди причину, по которой класс меняется, и убеди себя, что она одна». Если причина одна, но класс маленький, а «разделение» создаёт 4 файла вместо 1 — вы не улучшили код, вы его *размазали*.

Нарушение SOLID становится оправданным, когда **цена соблюдения выше ценности, которую оно даёт**. SOLID снижает *кост растущего* кода. Если код не растёт — плата не окупается.

---

## Пример: Webhook-обработчик в микросервисе

### Контекст

Микросервис `billing` на Symfony. Один эндпоинт: `/webhooks/stripe`. Два разработчика. В проде — 3 события: `invoice.paid`, `charge.failed`, `customer.deleted`. В планах роста — нет. Сервис живёт 2 года, потом его закрывают.

### «Чистый» вариант (SOLID-фанатик)

```
src/
  Controller/
    WebhookController.php          # 15 строк, делегирует
  Service/
    WebhookHandler.php             # парсит, валидирует sig, делегирует
  Event/
    PaymentEventProcessor.php      # match по типу, делегирует
  Event/Handler/
    InvoicePaidHandler.php         # обновляет Order, шлёт email
    ChargeFailedHandler.php        # логирует, алерт
    CustomerDeletedHandler.php     # шлёт email
  Infrastructure/
    EmailSender.php                # шлёт через Mailer
    OrderRepository.php            # сохраняет Order
```

**8 файлов, 6 классов.** Чтобы ответить на вопрос «что происходит при `invoice.paid`», нужно открыть 4 файла и проследить цепочку.

Каждый класс по 10–20 строк. `EmailSender` — это:

```php
final class EmailSender
{
    public function __construct(private MailerInterface $mailer) {}

    public function send(string $to, string $subject, string $body): void
    {
        $this->mailer->send($to, $subject, $body);
    }
}
```

20 строк, чтобы вызвать `send()`.

### «Нарушающий» вариант

```php
final class WebhookController extends AbstractController
{
    public function __construct(
        private OrderRepository $orders,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $stripeSecret,
    ) {}

    #[Route('/webhooks/stripe', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        // ── Валидация подписи ──────────────────────────────────
        $sig = $request->headers->get('Stripe-Signature', '');
        if (!StripeSignature::verify($request->getContent(), $sig, $this->stripeSecret)) {
            return new Response('bad signature', 400);
        }

        $event = json_decode($request->getContent(), true);
        $type  = $event['type'] ?? '';

        // ── invoice.paid ───────────────────────────────────────
        if ($type === 'invoice.paid') {
            $orderId = $event['data']['object']['metadata']['order_id'] ?? null;
            if ($orderId === null) {
                $this->logger->warning('invoice.paid without order_id', ['event' => $event]);
                return new Response('ok', 200);
            }

            $order = $this->orders->findById($orderId)
                ?? throw new NoResultException("Order {$orderId} not found");

            $order->markPaid($event['data']['object']['id']);
            $this->orders->save($order);

            // Email — прямо здесь, без EmailSender-обёртки
            $this->mailer->send(
                to:      $order->customerEmail,
                subject: 'Your payment was received',
                body:    "Order {$order->id} paid via Stripe ({$order->totalCents}¢).",
            );

            return new Response('ok', 200);
        }

        // ── charge.failed ──────────────────────────────────────
        if ($type === 'charge.failed') {
            $chargeId = $event['data']['object']['id'];
            $reason   = $event['data']['object']['failure_reason'] ?? 'unknown';
            $this->logger->error('Charge failed', [
                'charge' => $chargeId,
                'reason' => $reason,
            ]);
            // Алерт в Slack (вынес в trait или service, если нужно)
            return new Response('ok', 200);
        }

        // ── customer.deleted ───────────────────────────────────
        if ($type === 'customer.deleted') {
            $custId = $event['data']['object']['id'];
            $this->logger->info('Customer deleted', ['customer' => $custId]);
            // По требованию GDPR — purge связанных данных
            $this->orders->deleteByCustomerId($custId);
            return new Response('ok', 200);
        }

        // ── Неизвестный тип ────────────────────────────────────
        $this->logger->info('Unhandled webhook type', ['type' => $type]);
        return new Response('ok', 200);  // Stripe ретраит, если 5xx
    }
}
```

**1 файл, ~60 строк.** Всё на одном экране. Чтобы ответить «что происходит при `invoice.paid`» — прокручиваете 10 строк вниз.

### Что тут «нарушено» и почему это ок

| Принцип | Нарушение | Почему оправдано |
|---|---|---|
| **SRP** | Класс знает про парсинг, бизнес-логику, email, логирование | «Одна причина меняться» — *один webhook-эндпоинт*. Если меняется логика `invoice.paid` — правите этот файл. Это *одна* причина. |
| **OCP** | Новый тип события = правка этого файла | В планах — нет новых типов. Даже если появится — это 1 `if` блок на 10 строк. Полиморфизм из 3 классов-обработчиков не окупает себя. |
| **DIP** | Зависит от `OrderRepository` и `MailerInterface` напрямую | Оба — интерфейсы Symfony/Doctrine, они *уже* абстракции. Дальше — ложная абстракция. |
| **SRP** (вновь) | `EmailSender`-обёртка убрана | 20 строк, чтобы вызвать `send()`. В микросервисе на 12 файлов это шум. |

### Когда этот вариант *плох*

Если завтра:
- Появляется `invoice.refunded`, `charge.succeeded`, `payment_intent.requires_action` — 7 событий.
- Приходят два разработчика, которые одновременно правят этот файл.
- Нужно покрыть тестами *каждое* событие отдельно.
- Email-логика усложняется (шаблоны, A/B, rate-limit).

Тогда «один файл на 60 строк» становится 250 строками, где `if` блоки переплетаются, и **вот тогда** OCP и SRP начинают *спасать* — потому что код начал расти, и стоимость роста стала выше стоимости полиморфизма.

---

## Критерий: нарушать или нет

Перед тем как «почистить» код по SOLID, спросите:

1. **Будет ли это место меняться чаще, чем раз в квартал?** Если нет — полиморфизм не окупится.
2. **Сколько человек будут править этот файл параллельно?** Если 1–2 — «один файл» удобнее.
3. **Есть ли реальное *различие в поведении*, а не только в параметрах?** Если `InvoicePaidHandler` и `ChargeFailedHandler` делают `log()` + `send()` — это *не* две стратегии, это одна функция с разными аргументами.
4. **Сколько файлов добавится?** Если 6 файлов по 15 строк вместо 1 файла на 60 — вы создали *бюрократию*, а не архитектуру.

Если на все четыре ответа «нет, один-два, одно, один» — **не чистите**. Ваш «нарушенный SOLID» код — это *правильный* код для этой задачи.

---

## Что я *не* оправдываю

- Нарушение **LSP** (дочерний класс ломает контракт родителя) — почти никогда не оправдано. Если `BundleProduct` не является `Product` в смысле «можно подставить» — он не наследует.
- Проглатывание исключений (`catch (\Exception) {}`) — никогда.
- Отсутствие `strict_types` — никогда.
- Глобальные состояния и «магия» — никогда.

---

## Одна фраза напоследок

SOLID отвечает на вопрос **«как не сломать код, когда он вырастает?»**. Если он не вырастает — вопрос не возникает, и ответ не нужен. Архитектор, который применяет SOLID как чек-лист, а не как инструмент размышления, создаёт *формально чистый* код, который *фактически сложнее* понять.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

# OCP без наследования

Классический ответ «OCP = Strategy + `implements`» — это, если смотреть строго, всё ещё статический полиморфизм: вы создаёте новый класс, регистрируете его, DI-контейнер подбирает. Работает, но не единственный путь. Вот что ещё есть.

---

## 1. Реестр (Map) — поведение как данные

Самый радикальный уход от полиморфизма. Вместо «новый класс на новый случай» — **новая запись в массив**.

```php
// src/Application/Service/NotificationService.php

final class NotificationService
{
    /** @var array<string, callable(array $context): void> */
    private array $handlers = [];

    public function register(string $event, callable $handler): void
    {
        $this->handlers[$event][] = $handler;
    }

    public function dispatch(string $event, array $context = []): void
    {
        foreach ($this->handlers[$event] ?? [] as $handler) {
            $handler($context);
        }
    }
}
```

```php
// config/services.yaml (Symfony)
App\Application\Service\NotificationService:
    calls:
        - [register, ['order.paid',      '@app.notif.email_order_paid']]
        - [register, ['order.paid',      '@app.notif.slack_order_paid']]
        - [register, ['order.cancelled','@app.notif.email_order_cancelled']]
```

Каждый «handler» — маленький сервис:

```php
final class EmailOrderPaid
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(array $context): void
    {
        $this->mailer->send(
            to:      $context['email'],
            subject: 'Order paid',
            body:    "Order {$context['order_id']} confirmed.",
        );
    }
}
```

**Что открыто:** новый `callable` + одна строка в конфиге.
**Что закрыто:** `NotificationService::dispatch()` — ни строчки не трогаем.

### Когда выигрывает
- Событий много, и они **не образуют иерархию** («email» — не родитель «slack»).
- Один и тот же обработчик нужен для 3 событий — просто 3 строки в конфиге, а не 3 класса с копипастой.
- Команда из 5+ человек, и каждый добавляет свои обработчики, не читая чужие.

### Когда проигрывает
- Логика обработчика больше 30 строк — `callable` в конфиге теряет читаемость.
- Нужно переопределить поведение в тесте — проще подменить объект, чем функцию из массива.

---

## 2. Замыкания как citizens (PHP 8.1 first-class callable)

Тот же реестр, но без отдельного класса на каждое действие. Полезна, когда поведение — **один-два вызова**.

```php
// src/Domain/Order/OrderStateTransition.php

enum OrderState
{
    case Draft;
    case Confirmed;
    case Shipped;
    case Delivered;

    /** @var array<string, callable(self, self): void> */
    private static array $guards = [];

    public static function onTransition(string $from, string $to, callable $guard): void
    {
        self::$guards[$from . '->' . $to] = $guard;
    }

    public function transitionTo(self $target): void
    {
        $guard = self::$guards[$this->name . '->' . $target->name]
            ?? throw new InvalidTransitionException($this->name, $target->name);

        $guard($this, $target);
    }
}

// Регистрация — один файл, не 10 классов:
OrderState::onTransition('Draft', 'Confirmed', fn($f, $t) => true);
OrderState::onTransition('Confirmed', 'Shipped', function ($f, $t) {
    if (!Warehouse::hasStock()) {
        throw new OutOfStockException();
    }
});
OrderState::onTransition('Shipped', 'Delivered', fn($f, $t) => true);
// "Draft -> Delivered" не зарегистрирован — InvalidTransitionException
```

**Что открыто:** новая строка `onTransition(...)`.
**Что закрыто:** `transitionTo()` — не трогается.

### Когда выигрывает
- Таблица состояний, матрица разрешений, простые guards.
- Понедельная «а давайте ещё и X->Y разрешим» — строка, не класс.

### Когда проигрывает
- Guard-логика растёт до 50 строк — замыкание в конфиге превращается в спагетти.
- Нужен доступ к DI-контейнеру внутри guard — замыкание не умеет `autowire`.

---

## 3. Pipeline / Middleware-цепочка

Очень близко к реестру, но с **порядком** и **возможностью прервать**.

```php
// src/Domain/Payment/ChargePipeline.php

final class ChargePipeline
{
    /** @var list<callable(ChargeContext): ChargeContext|false> */
    private array $steps = [];

    public function add(callable $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    public function run(ChargeContext $ctx): ChargeContext
    {
        foreach ($this->steps as $step) {
            $result = $step($ctx);
            if ($result === false) {
                throw new ChargeAbortedException(
                    $ctx->abortedAt ?? 'unknown step'
                );
            }
            $ctx = $result;
        }
        return $ctx;
    }
}
```

```php
// Сборка:
$pipe = (new ChargePipeline())
    ->add(fn($c) => $c->withValidated())          // валидация
    ->add(fn($c) => $c->withRiskCheck())           // антифрод
    ->add(fn($c) => $c->withFundsReserved())       // резерв средств
    ->add(fn($c) => $c->withLedgerEntry());        // запись в ledger

$ctx = $pipe->run($ctx);
```

Каждый шаг — `callable`, который получает контекст и возвращает обновлённый (или `false` для прерывания). Новый шаг — новая строка `->add(...)`.

### Когда выигрывает
- Порядок шагов **важен** и **стабилен** (валидация → риск → резерв).
- Шаги **не знают друг о друге** — только о `ChargeContext`.
- Нужно тестировать каждый шаг изолированно: `fn($c) => ...` — это чистая функция, моки не нужны.

### Когда проигрывает
- Шаги **взаимозависимы** (шаг 4 нужно знать результат шага 2 в деталях) — контекст становится «богом».
- Нужно асинхронное поведение (шаг 2 ждёт ответ банка 3 сек) — `callable` синхронный, а `Fiber`/`Promise` в пайплайне усложняют.

---

## 4. `implements` без `extends` (композиция, а не иерархия)

Технически это всё ещё «стратегия», но **без наследования класса**. Важно: в PHP `implements` и `extends` — разные механизмы.

```php
interface RateLimiter
{
    public function allow(string $key): bool;
}

// Не "class SlidingWindow extends RateLimiter"
// А "class SlidingWindow implements RateLimiter"
final class SlidingWindowLimiter implements RateLimiter
{
    public function __construct(private RedisClient $redis, private int $window) {}
    public function allow(string $key): bool { /* … */ }
}

final class TokenBucketLimiter implements RateLimiter
{
    public function __construct(private RedisClient $redis, private int $capacity) {}
    public function allow(string $key): bool { /* … */ }
}
```

Вызывающий код **закрыт**:

```php
final class ApiController
{
    public function __construct(private RateLimiter $limiter) {}  // ← не знает реализацию

    public function handle(): Response
    {
        if (!$this->limiter->allow('api:rate')) {
            return new Response('429', 429);
        }
        // …
    }
}
```

Новая реализация = новый файл `implements RateLimiter` + одна строка в конфиге. Никто не наследует, никто не extends.

### Чем отличается от «классического» Strategy
- Нет `abstract class`-базы с «общей» логикой. Каждая реализация автономна.
- Нет «забыл вызвать `parent::`» — потому что `parent` нет.
- Проще тестировать: мокается только `RateLimiter`, не иерархия.

---

## 5. Конфиг-драйвен: поведение как YAML/JSON

Экстремальный случай: логика описана **данными**, а не кодом.

```yaml
# config/pricing.yaml
rules:
  - match: { currency: "USD", customer_tier: "enterprise" }
    discount_pct: 15
  - match: { currency: "USD", order_items_gte: 10 }
    discount_pct: 5
  - match: { currency: "EUR" }
    discount_pct: 0
  default:
    discount_pct: 0
```

```php
final class PricingEngine
{
    public function __construct(private array $rules) {}

    public function calculateDiscount(Order $order): int
    {
        foreach ($this->rules as $rule) {
            if ($this->matches($rule['match'], $order)) {
                return (int) round($order->totalCents * $rule['discount_pct'] / 100);
            }
        }
        return (int) round($order->totalCents * ($this->rules['default']['discount_pct'] ?? 0) / 100);
    }

    private function matches(array $cond, Order $order): bool
    {
        foreach ($cond as $field => $expected) {
            $actual = match ($field) {
                'currency'        => $order->currency,
                'customer_tier'   => $order->customer->tier,
                'order_items_gte' => $order->items->count(),
            };
            if ($actual !== $expected) return false;
        }
        return true;
    }
}
```

Новое правило = **строка в YAML**. Ни одной строки PHP не трогаем.

### Когда выигрывает
- Правила меняет **не разработчик**, а менеджер / продакт / поддержка.
- Правил 10–50, и они **однотипны** (условие → действие).

### Когда проигрывает
- Правило содержит **ветвление внутри** (`if amount > 1000 then X else Y`) — YAML превращается в DSL, и вы написали язык программирования на 200 строк.
- Нужны side effects (отправить email, вызвать API) — «действие» перестает быть данными.

---

## Сравнение

| Механизм | «Открытость» | Когда лучше | Когда хуже |
|---|---|---|---|
| **Реестр (Map)** | Новая строка в конфиге | Много однородных событий, 5+ человек | Логика обработчика > 30 строк |
| **Замыкания** | Новая строка в коде | Простые guards, таблица состояний | Нужен DI внутри, асинхронность |
| **Pipeline** | Новый `->add(...)` | Порядок важен, шаги независимы | Шаги взаимозависимы, асинхронно |
| **`implements`** | Новый класс + строка конфига | Логика 50+ строк, нужен DI | Реализация 5 строк — шум |
| **Конфиг-драйвен** | Строка в YAML | Меняет не-разработчик, правил 10+ | Ветвление внутри правила, side effects |

---

## Практическое правило

Перед тем как создать `XxxHandler implements YyyProcessor` на 12 строк, спросите:

> **Могу ли я выразить это как запись в массив / строку в YAML / замыкание на 3 строки?**

Если да — сделайте это. Класс — это **цена**, которую вы платите за именование, наследование и DI. Если имя и DI вам не нужны — не платите.

Если логика вырастает до 40 строк, и вы хотите моки в тестах, и два разработчика правят параллельно — **тогда** класс с интерфейсом начинает окупаться. Переход от замыкания к классу в этот момент — не «нарушение SOLID», а **нормальный рост**.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

# SRP — не только про классы

Нет. SRP формулируется так:

> **Один модуль должен иметь одну и только одну причину меняться.**

«Модуль» — это не класс. Это любая единица кода, которую вы можете выделить и изменить независимо от остальных. Принцип работает на каждом уровне абстракции.

---

## Уровни, на которых SRP реально применяется

### 1. Метод / функция

Самый близкий к коду уровень. «Одна причина меняться» = один аспект поведения.

```php
// НАРУШЕНИЕ: 3 причины менять
public function submitOrder(Order $order): void
{
    // Причина 1: валидация бизнес-правил
    if ($order->totalCents <= 0) {
        throw new InvalidOrderException();
    }
    if (in_array($order->customer->id, $this->blacklist)) {
        throw new BlacklistedCustomerException();
    }

    // Причина 2: расчёт итоговой суммы (скидки, налоги, доставка)
    $total = $this->calculateTax($order);
    $total += $this->calculateShipping($order);
    $order->setTotal($total);

    // Причина 3: побочное действие — уведомление
    $this->mailer->send(
        to:      $order->customerEmail,
        subject: 'Order received',
        body:    "Order {$order->id} confirmed.",
    );

    // Причина 4: персист
    $this->repo->save($order);
}
```

Четыре причины меняться: правила валидации меняются, формула скидок меняется, текст email меняется, схема БД меняется. Все — в одном методе.

Раздельно:

```php
// Причина 1: валидация — меняется, когда меняются правила
public function validate(Order $order): void { /* … */ }

// Причина 2: расчёт — меняется, когда меняются тарифы/налоги
public function calculateTotal(Order $order): int { /* … */ }

// Причина 3: уведомление — меняется, когда меняется канал/шаблон
public function notify(Order $order): void { /* … */ }

// Причина 4: персист — меняется, когда меняется схема
public function persist(Order $order): void { /* … */ }

// Оркестратор: тонкий, не меняется без изменения *состава* шагов
public function submitOrder(Order $order): void
{
    $this->validate($order);
    $order->setTotal($this->calculateTotal($order));
    $this->notify($order);
    $this->persist($order);
}
```

### 2. Таблица БД

Очень распространённое нарушение, которое редко называют «SRP», но это оно.

```sql
-- НАРУШЕНИЕ: 3 причины менять
CREATE TABLE orders (
    id            BIGSERIAL PRIMARY KEY,
    customer_id   BIGINT NOT NULL,
    total_cents   INT NOT NULL,
    status        VARCHAR(20),

    -- Причина 2: аудит
    created_at    TIMESTAMPTZ,
    updated_at    TIMESTAMPTZ,
    created_by    BIGINT,
    updated_by    BIGINT,

    -- Причина 3: настройки/разное
    internal_notes TEXT,
    is_test       BOOLEAN DEFAULT FALSE,
    metadata      JSONB
);
```

Причины меняться:
- Схема домена меняется (новые поля заказа).
- Политика аудита меняется (нужен `ip_address`, `user_agent`, `device_id`).
- Операционные флаги меняются (новый флаг `is_archived`, `priority_level`).

Раздельно:

```sql
CREATE TABLE orders (
    id            BIGSERIAL PRIMARY KEY,
    customer_id   BIGINT NOT NULL,
    total_cents   INT NOT NULL,
    status        VARCHAR(20),
    created_at    TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE order_audit (
    order_id      BIGINT REFERENCES orders(id),
    action        VARCHAR(30),
    actor_id      BIGINT,
    ip_address    INET,
    metadata      JSONB,
    created_at    TIMESTAMPTZ
);

CREATE TABLE order_meta (
    order_id      BIGINT PRIMARY KEY REFERENCES orders(id),
    internal_notes TEXT,
    is_test       BOOLEAN DEFAULT FALSE,
    priority      INT DEFAULT 0
);
```

Теперь изменение аудита не трогает `orders`. Изменение флагов не трогает доменную сущность.

### 3. Микросервис / модуль

Макроскопический уровень. «Один сервис = одна причина меняться» = «один сервис = один bounded context».

```
ПЛОХО: сервис «billing» отвечает за:
  - расчёт цены (причина: меняются тарифы)
  - обработку платежей (причина: меняются платёжные провайдеры)
  - отправку чеков (причина: меняются требования к чекам)
  - хранение истории транзакций (причина: меняются требования хранения)

ХОРОШО:
  pricing-service    → причины: тарифы, скидки, налоги
  payment-service    → причины: провайдеры, retry, idempotency
  invoice-service    → причины: формат, канал отправки, локализация
  ledger-service     → причины: схема, retention, compliance
```

Если вы меняете Stripe на Adyen — трогаете только `payment-service`. Если меняете формат PDF-чека — только `invoice-service`. `pricing-service` не пострадал.

### 4. Файл

Парадоксальный уровень: один файл может содержать несколько *независимых* причин меняться.

```php
// config/services.yaml — НАРУШЕНИЕ
services:
    App\Service\OrderService:
        arguments:
            $timeout: 30        # ← причина 1: производительность
            $maxRetries: 5     # ← причина 2: надёжность
            $logLevel: info    # ← причина 3: observability
            $featureFlags:     # ← причина 4: product decisions
                new_pricing: true
                beta_checkout: false
```

Один файл, четыре независимые причины менять. Раздельно:

```yaml
# config/timeout.yaml
# config/retry.yaml
# config/logging.yaml
# config/feature-flags.yaml
```

В Symfony это делается через `env()` и отдельные файлы, или через `Bag`-инжект в сервис.

### 5. Тест

Каждый тест-кейс — одна причина меняться: «что-то в системе изменилось, и этот тест сломался».

```python
# ПЛОХО: тест проверяет 3 вещи
def test_order_submitted():
    resp = client.post('/orders', json=order)
    assert resp.status_code == 201           # причина 1: API contract
    assert db.query('SELECT total FROM orders').one() == 150  # причина 2: расчёт
    assert email_sent['subject'] == 'Order received'  # причина 3: уведомление
```

Если меняется текст email — сломается и тест API contract. Это SRP-нарушение в тесте.

```python
def test_api_returns_201():          # причина 1
    resp = client.post('/orders', json=order)
    assert resp.status_code == 201

def test_total_calculated_correctly():  # причина 2
    client.post('/orders', json=order)
    assert db.query(...).one() == 150

def test_notification_sent():        # причина 3
    client.post('/orders', json=order)
    assert email_sent['subject'] == 'Order received'
```

---

## Частая путаница: «маленький класс = SRP»

Это **не одно и то же**.

| Ситуация | Размер | SRP? |
|---|---|---|
| Класс `JsonSerializer` на 80 строк, одна причина меняться (формат JSON) | Большой | **Да** |
| Класс `Dto` на 10 строк, но содержит и валидацию, и маппинг, и сериализацию | Маленький | **Нет** |

SRP — про **количество независимых причин меняться**, а не про количество строк. Маленький класс с тремя «ответственностями» нарушает SRP. Большой класс с одной причиной — не нарушает.

---

## Практический тест

Когда читаете код (класс, метод, таблицу, сервис) и задумываетесь «почему это здесь?», — это **причина меняться**. Считайте их:

- **0–1 причина** → SRP соблюдён.
- **2 причины** → граница. Можно оставить, если причины связаны (один домен).
- **3+ причины** → почти наверняка нужно выделить.

Самый быстрый способ проверить: «Если завтра изменится X, я поправлю этот класс? Если изменится Y — тоже? Если изменится Z — тоже?» Три «да» на независимые X, Y, Z — это три причины, и это три разных класса/таблицы/сервиса.

---

## Коротко

SRP — это про **причины изменения**, а не про размер. Он применяется к методу, классу, таблице, файлу, сервису, тесту — к любой единице, которую вы можете выделить и изменить *независимо от остальных*. Классы — самый видимый уровень, но не единственный, и часто не самый важный.

> ⏱ 31.5s · prompt 9102 tok · output 4357 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь просит объяснить внутреннее устройство DI-контейнера Symfony: компиляцию, autowiring, и что происходит при первом запросе сервиса. Это технический вопрос, требующий точности.

Давайте пройдусь по этапам:

1. **Компиляция контейнера** (build time):
   - Сбор деклараций сервисов из PHP-атрибутов, YAML, XML, PHP-конфига.
   - Регистрация в `ContainerBuilder`.
   - Компоненты: Passes (CompilerPass) — это последовательность преобразований графа зависимостей.
   - Основные passes: ResolveInstanceofConditionalsPass, ResolveChildDefinitionsPass, AutowirePass, CheckCircularReferencesPass, CheckDefinitionValidityPass, InlineServiceDefinitionsPass, RemoveUnusedDefinitionsPass, RegisterEnvVarProcessorsPass, и др.
   - На выходе — сгенерированный PHP-файл `ContainerXX.php` (или `App_KernelDevDebugContainer.php`), где каждый сервис описан как метод `create0()`, `create1()` и т.д.
   - `AutowirePass` — ключевой: для каждого необязательного аргумента в конструкторе/методе, Symfony ищет тип и пробует сопоставить с зарегистрированными сервисами. Если один кандидат — подставляет. Если несколько — ошибка (нужен `autowire: ['!service_id']` или `named` argument). Если ноль — ошибка.
   - `InlineServiceDefinitionsPass` — инлайнит сервисы, которые используются только в одном месте и не публикуются.
   - `RemoveUnusedDefinitionsPass` — убирает сервисы, которые ни на кого не ссылаются и не публичные.

2. **Autowiring** подробнее:
   - Работает на этапе `AutowirePass` (компиляция).
   - Для каждого параметра конструктора/метода, который не имеет явного bindings, Symfony смотрит на тип-хинт.
   - Если тип — класс/интерфейс, ищется в реестре. Один кандидат → подстановка. Несколько → `AutowireException`. Ноль → `AutowireException`.
   - `iterable $foo` → `tagged_iterator`.
   - `Closure $foo` → lazy factory.
   - `string $env` → env var (через `env()`).
   - PHP 8 named arguments: если имя аргумента совпадает с id сервиса — можно подставить без типа.
   - `#[Autowire(service: '...')]` — override.
   - `#[Required]` — inject через метод.

3. **Время выполнения (runtime) — первый запрос**:
   - Приложение стартует: `Kernel::boot()` → `$kernel->getContainer()` → загрузить сгенерированный PHP-файл.
   - Файл содержит класс, наследующий от `Container` (или `ContainerBuilder` в dev).
   - Каждый сервис — приватный метод: `protected function create0(): Foo { return new Foo($this->getService('bar'), $this->getService('baz')); }`
   - При первом обращении `$container->get(Foo::class)` → вызов `create0()` → конструктор → резкурсивно `getService()` для зависимостей.
   - Singleton: после первого создания результат кэшируется в `$this->services['id']`. Повторный `get()` — возврат из кэша.
   - Prototype (`shared: false`): каждый `get()` — новый экземпляр.
   - Lazy: если сервис объявлен `public function &create0(): Foo` или через `#[Lazy]` — при первом обращении создаётся proxy (PHP `__clone`, `__destruct`, `__call`), и реальный объект создаётся при первом вызове метода.
   - Synthesis: `get()` возвращает объект или `null` (если private и не зарегистрирован).

4. **Dev vs Prod**:
   - Dev: `ContainerBuilder` + debug-информация, `dump()` доступен, `removeUnusedDefinitionsPass` не так агрессивен (или не применяется), `inlineServiceDefinitionsPass` не применяется (чтобы можно было подменить в `debug:container`).
   - Prod: оптимизированный контейнер, инлайнинг, removal, нет debug.

5. **Compiler Passes — порядок**:
   Symfony применяет passes в определённом порядке. Основные группы:
   - Pre-compile: `RegisterAutoconfigureAttributesPass`, `RegisterAttributeAutoregistrationPass` (для `#[Autoconfigure]`).
   - Resolve: `ResolveInstanceofConditionalsPass`, `ResolveChildDefinitionsPass`, `ResolveParameterPlaceHoldersPass`, `ResolveHotPathPass`.
   - Autowire: `AutowirePass`.
   - Validate: `CheckCircularReferencesPass`, `CheckDefinitionValidityPass`, `CheckExceptionAvailabilityPass`.
   - Optimize: `InlineServiceDefinitionsPass`, `RemoveUnusedDefinitionsPass`, `RegisterEnvVarProcessorsPass`.
   - Post: `ResetInterfacePass`, `ServiceLocatorTagPass`.

6. **Что происходит при первом `get()`**:
   - PHP include'ит сгенерированный файл (если ещё не загружен).
   - Вызывается метод `get(string $id)` → `$this->services[$id] ?? $this->createXxx()`.
   - `createXxx()` — конструктор с уже подставленными зависимостями.
   - Зависимости тоже `createYyy()` — рекурсия по графу.
   - Результат сохраняется в `$this->services[$id]`.
   - Если сервис `shared: false` — не сохраняется.
   - Если `#[Lazy]` — создаётся proxy-объект (через `ProxyHelper`), который при первом `__call`/`__get` реально инициализирует.

Структурирую ответ по трём блокам: компиляция, autowiring, runtime. Добавлю конкретный пример сгенерированного кода, чтобы было наглядно.

</details>

**Кандидат:**

# DI-контейнер Symfony: изнутри

## Три временные зоны

Контейнер живёт в три эпохи:

1. **Компиляция** (build time) — из деклараций строится граф и генерируется PHP-файл.
2. **Загрузка** (boot time) — файл `require_once`-ится, создаётся экземпляр `Container`.
3. **Запрос** (runtime) — `get()` по id, ленивое создание, кэширование.

Разберём каждую.

---

## 1. Компиляция: из деклараций в PHP-файл

### Вход

Symfony собирает «сырые» описания сервисов из всех источников:

- Атрибуты `#[Controller]`, `#[Service]`, `#[Autoconfigure]`.
- YAML / XML / PHP-конфиг в `config/services*.yaml`.
- Встроенные сервисы фреймворка и бандлов.

Результат — объект `ContainerBuilder`, внутри которого `array<string, Definition>`:

```php
// Упрощённо:
'App\Service\OrderService' => new Definition(
    OrderService::class,
    [
        '$repo'    => new Reference('App\Repository\OrderRepository'),
        '$mailer'  => new Reference('mailer'),
        '$timeout' => new Parameter('app.order.timeout'),  // 30
    ],
    null,
    null,
    null,
    true,  // shared
    false, // public
    [],    // tags
);
```

### Compiler Passes — последовательность преобразований

`ContainerBuilder::compile()` запускает цепочку **CompilerPass'ов**. Каждый получает граф зависимостей, мутирует его, отдаёт дальше. Порядок фиксирован, основные группы:

```
┌─────────────────────────────────────────────────────────────────┐
│  1. RegisterAttributeAutoregistrationPass                      │
│     → атрибуты #[Autoconfigure] превращаются в autoconfigure   │
├─────────────────────────────────────────────────────────────────┤
│  2. ResolveInstanceofConditionalsPass                          │
│     → instanceof: {...} раскрывается для каждого класса        │
├─────────────────────────────────────────────────────────────────┤
│  3. ResolveChildDefinitionsPass                                │
│     → parent: 'base' → merge-merge определений                 │
├─────────────────────────────────────────────────────────────────┤
│  4. AutowirePass   ← КЛЮЧЕВОЙ (разберём ниже)                  │
├─────────────────────────────────────────────────────────────────┤
│  5. CheckCircularReferencesPass                                │
│     → A → B → A ? Фатальная ошибка                             │
├─────────────────────────────────────────────────────────────────┤
│  6. CheckDefinitionValidityPass                                │
│     → класс существует? конструктор соответствует аргументам?  │
├─────────────────────────────────────────────────────────────────┤
│  7. InlineServiceDefinitionsPass   (prod only)                 │
│     → сервис, используемый в 1 месте, не публичный →           │
│       его create-метод встраивается в родительский             │
├─────────────────────────────────────────────────────────────────┤
│  8. RemoveUnusedDefinitionsPass    (prod only)                 │
│     → ни один сервис не ссылается, не public → удаляется       │
├─────────────────────────────────────────────────────────────────┤
│  9. RegisterEnvVarProcessorsPass                               │
│     → %env(int:APP_PORT)% → обработчик, валидирующий int       │
├─────────────────────────────────────────────────────────────────┤
│  10. ResetInterfacePass, ServiceLocatorTagPass, …             │
└─────────────────────────────────────────────────────────────────┘
```

### На выходе

Генерируется **один PHP-файл**:

```
var/cache/prod/App_KernelProdContainer.php
var/cache/dev/App_KernelDevDebugContainer.php
```

Структура (упрощённо, prod):

```php
// var/cache/prod/App_KernelProdContainer.php
final class App_KernelProdContainer extends Container
{
    protected array $services = [];
    protected array $aliases = ['logger' => 'monolog.logger'];

    public function get(string $id): ?object
    {
        return match ($id) {
            'App\Service\OrderService'  => $this->services['0'] ??= $this->create0(),
            'App\Repository\OrderRepo'  => $this->services['1'] ??= $this->create1(),
            'mailer'                    => $this->services['2'] ??= $this->create2(),
            default                     => throw new ServiceNotFoundException($id),
        };
    }

    // ── Листовые сервисы (без зависимостей) ──────────────────
    private function create5(): Logger
    {
        $a = new \Monolog\Logger('app');
        $a->pushHandler(new \Monolog\Handler\StreamHandler(
            '/var/log/app.log', \Monolog\Logger::INFO
        ));
        return $this->services['5'] = $a;
    }

    // ── Сервис с зависимостями ───────────────────────────────
    private function create2(): Mailer
    {
        return $this->services['2'] = new \Symfony\Component\Mailer\Mailer(
            $this->create3(),   // Transport
        );
    }

    private function create3(): Transport
    {
        return $this->services['3'] = new \Symfony\Component\Mailer\Transport\SmtpTransport(
            'smtp.example.com', 587,
            'user', 'pass', true,
        );
    }

    // ── Репозиторий ──────────────────────────────────────────
    private function create1(): OrderRepository
    {
        return $this->services['1'] = new \App\Repository\OrderRepository(
            $this->create0_em(),  // EntityManager
        );
    }

    // ── Топ-сервис ───────────────────────────────────────────
    private function create0(): OrderService
    {
        return $this->services['0'] = new \App\Service\OrderService(
            $this->create1(),    // OrderRepository
            $this->create2(),    // Mailer
            30,                  // $timeout — из Parameter
        );
    }

    private function create0_em(): EntityManager
    {
        // … Doctrine EntityManager, тяжёлый граф …
    }
}
```

**Ключевые наблюдения:**

- Каждый сервис — **приватный метод** `createN()`. Числовой индекс вместо строки — быстрее.
- `$this->services['N'] ??= $this->createN()` — **singleton**: создаётся один раз, кэшируется в массиве. Повторный `get()` — возврат из массива.
- Зависимости **инлайнены** в вызовы. Нет `array_map`, нет `ReflectionClass`, нет строчек `new Service([...])` в рантайме.
- `inlineServiceDefinitionsPass` убрал все сервисы, которые использовались в одном месте и не были публичны. Их `createN()` встроены прямо в родительский метод.

---

## 2. Autowiring: что делает `AutowirePass`

Это **единственный** pass, который «думает». Остальные мутируют структуру. `AutowirePass` — **заполняет пропуски**.

### Алгоритм (упрощённо)

Для каждого `Definition`, у которого `autowire: true` (по умолчанию для всех):

```
Для каждого аргумента конструктора (или метода с #[Required]):
  │
  ├─ Аргумент уже задан явно (Reference / Parameter / Value)?
  │   → Пропускаем. Явное значение побеждает.
  │
  ├─ Аргумент имеет #[Autowire(service: 'x')]?
  │   → Подставляем Reference('x').
  │
  ├─ Тип аргумента — iterable?
  │   → Подставляем tagged_iterator (все сервисы с соответствующим тегом).
  │
  ├─ Тип аргумента — Closure?
  │   → Подставляем lazy factory.
  │
  ├─ Тип аргумента — конкретный класс или интерфейс?
  │   │
  │   ├─ В реестре ровно 1 сервис этого типа?
  │   │   → Подставляем Reference(этого_сервиса).
  │   │
  │   ├─ В реестре 0 сервисов?
  │   │   → AUTOWIRE EXCEPTION: "Cannot autowire service ... argument $foo:
  │   │     it needs a reference to ... but no such service exists."
  │   │
  │   └─ В реестре 2+ сервиса?
  │       ├─ Указан named binding (App\Repository\Foo::class => '@specific')?
  │       │   → Подставляем по binding.
  │       │
  │       ├─ Имя аргумента совпадает с id сервиса ($orderRepo → App\...\OrderRepo)?
  │       │   → Подставляем (PHP 8 named args).
  │       │
  │       └─ Иначе?
  │           → AUTOWIRE EXCEPTION: "Cannot autowire service ... argument $foo:
  │             it relies on an automatic dependency injection, but multiple
  │             candidates exist: a, b."
  │
  └─ Тип — string / int / float / bool (без type hint или mixed)?
      → Ищем env var (APP_*) или parameter.
         Не нашли → пропуск (или ошибка, если strict).
```

### Примеры

```php
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,       // 1 кандидат → авто
        private MailerInterface $mailer,     // 1 кандидат → авто
        private LoggerInterface $logger,     // 1 кандидат → авто
        private string $timeout,             // string → ищем param / env
        private iterable $processors,        // iterable → tagged_iterator
        private PaymentGateway $gateway,     // 2 кандидата → ОШИБКА
    ) {}
}
```

Для `$gateway` Symfony скажет:

```
Cannot autowire service "App\Service\OrderService":
argument "$gateway" of method "__construct()" references
interface "App\Gateway\PaymentGateway" but 2 services are tagged
with it: "stripe.gateway", "paypal.gateway".
```

Решение — любой из:

```yaml
# Вариант 1: named binding
services:
    App\Gateway\PaymentGateway: '@stripe.gateway'   # по умолчанию

# Вариант 2: явный аргумент
    App\Service\OrderService:
        arguments:
            $gateway: '@paypal.gateway'

# Вариант 3: атрибут
    public function __construct(
        #[Autowire(service: 'paypal.gateway')]
        private PaymentGateway $gateway,
    ) {}
```

### `iterable $foo` — магия тегов

```php
public function __construct(
    private iterable $paymentProcessors,  // ←
) {}
```

Symfony собирает **все** сервисы, помеченные `tag: 'app.payment_processor'`, в `ServiceLocator`. Это не массив на момент создания, а **ленивый** итератор:

```php
// Сгенерировано:
private function create0(): OrderService
{
    return $this->services['0'] = new OrderService(
        $this->create1(),
        $this->getServiceLocator(['stripe', 'bank', 'crypto']),  // ←
        30,
    );
}

private function getServiceLocator(array $ids): ServiceLocator
{
    return $this->services['loc0'] ??= new ServiceLocator(
        $ids,
        fn($id) => $this->get($id)  // лениво, по требованию
    );
}
```

Если один из процессоров тяжёлый — он **не создастся**, пока по нему не итерировали.

---

## 3. Runtime: что происходит при первом `get()`

### Стек вызовов

```
HTTP-запрос
  → Symfony Kernel::handle()
    → Controller::index()
      → $this->orderService->charge(...)     // ← первый доступ
```

`$this->orderService` — это свойство, которое Symfony встроил в контроллер через **injection** (не через `get()` в рантайме). На этапе компиляции контроллер уже получил референс:

```php
// Сгенерированный контроллер:
final class OrderController
{
    public function __construct(
        private OrderService $orderService,  // ← create0() вызван при создании контроллера
    ) {}
}
```

### Первый вызов `create0()`

```php
private function create0(): OrderService
{
    return $this->services['0'] ??= new \App\Service\OrderService(
        $this->create1(),   // ← рекурсивно: создаёт OrderRepository
        $this->create2(),   // ← рекурсивно: создаёт Mailer
        30,
    );
}
```

**Что происходит внутри `create1()`:**

```php
private function create1(): OrderRepository
{
    return $this->services['1'] ??= new \App\Repository\OrderRepository(
        $this->create0_em(),  // ← создаёт EntityManager (тяжёлый)
    );
}
```

**`create0_em()`** — это самый тяжёлый узел: Doctrine `EntityManager` с `Connection`, `SchemaManager`, `UnitOfWork`. Но он создаётся **один раз**, кэшируется в `$this->services['em']`.

**Ключевой момент:** `$this->services['1'] ??= …` — это **singleton guarantee**. Если два разных сервиса зависят от `OrderRepository`, оба получат **один и тот же экземпляр**. Второй `create1()` даже не вызовется — `??=` увидит, что key уже есть.

### `shared: false` (prototype)

```yaml
services:
    App\Service\EventCollector:
        shared: false   # каждый get() — новый экземпляр
```

Сгенерировано:

```php
private function create7(): EventCollector
{
    return new EventCollector();   // без $this->services['7'] = …
}
```

Каждый вызов `create7()` — новый объект. Используется для сервисов с состоянием на запрос (например, `Request`-скроуп-объекты).

### `#[Lazy]`

```php
public function __construct(
    #[Lazy] private EntityManager $em,  // ←
) {}
```

Сгенерировано:

```php
private function create0(): OrderService
{
    return $this->services['0'] = new OrderService(
        $this->create1(),
        $this->create2(),
        new \Symfony\Component\Proxy\ErroredProxy(  // ← PROXY, не EntityManager
            'App\Repository\OrderRepository',
            fn() => $this->create0_em(),  // ← ленивая инициализация
        ),
        30,
    );
}
```

Proxy — это **объект-обёртка** с `__call`, `__get`, `__set`. Пока вы не вызовали `$em->find(...)` — реальный `EntityManager` **не создан**. Первый доступ к методу → proxy вызывает closure → `create0_em()` → реальный объект → дальше всё через `__call`.

Это критично, если сервис тяжёлый, а используется в 5% запросов.

### Циклические зависимости

```
A → B → A
```

`CheckCircularReferencesPass` на этапе компиляции **запрещает** это для `shared: true`. Если нужно — `shared: false` или переработка. В рантайме цикл невозможен: граф — дерево, не граф.

---

## 4. Dev vs Prod: что иначе

| Аспект | Dev (`DebugContainer`) | Prod (`Container`) |
|---|---|---|
| `inlineServiceDefinitionsPass` | **Не применяется** — можно подменить | Применяется |
| `removeUnusedDefinitionsPass` | **Не применяется** — все сервисы доступны | Применяется |
| `dump()` | `debug:container` показывает граф | Недоступен |
| `getServiceLocator` | Возвращает все тегированные | Возвращает только тегированные |
| Исключения | Полное trace + «а какие кандидаты были?» | Короткое сообщение |
| `ContainerBuilder` | Доступен (`$kernel->getContainer()->getDefinition(...)`) | Нет |
| Размер файла | 50–200 KB | 5–50 KB |
| Скорость `get()` | Чуть медленнее (доп. проверки) | Максимальная |

---

## 5. Частые «вот почему это не работает»

| Симптом | Причина |
|---|---|
| `AutowireException: multiple candidates` | 2+ сервиса одного интерфейса, нет named binding |
| `AutowireException: no such service` | Сервис не зарегистрирован, или `public: false` и не инжектится |
| Сервис создаётся 2 раза | `shared: false` или `ServiceLocator` (лениво) |
| `ServiceNotFoundException` в рантайме | Сервис удалён `RemoveUnusedDefinitionsPass` в prod, но вы получаете через `$container->get()` по строке |
| `#[Required]` не работает | Метод не public, или класс не помечен `#[Autoconfigure]` |
| `iterable` приходит пустым | Тег не указан на сервисах, или `tagged_iterator` настроен с `index_by`, а индекс не уникален |
| Цикл `A → B → A` | `CheckCircularReferencesPass` на compile. Нужно `shared: false` или рефакторинг |

---

## Итого: три фразы

1. **Компиляция** превращает граф деклараций в плоский PHP-файл с `createN()`-методами. `AutowirePass` — единственный, кто «думает»: сопоставляет типы с реестром и подставляет референсы.

2. **Первый `get()`** — рекурсивный вызов `createN()` по дереву зависимостей. `??=` гарантирует singleton. `#[Lazy]` откладывает создание до первого метода.

3. **Prod-контейнер** — это не «большой YAML в рантайме». Это оптимизированный, инлайновый, лишённый лишних сервисов PHP-код, который запускается за миллисекунды и не требует `ReflectionClass`.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

# Циклические зависимости в Symfony

## Что происходит

```
OrderService  ──→  PaymentService
     ↑                    │
     └────────────────────┘
```

`ContainerBuilder::compile()` доходит до `CheckCircularReferencesPass`, находит цикл и бросает:

```
Circular reference on service "App\Service\OrderService"
(path: "App\Service\OrderService" -> "App\Service\PaymentService" -> "App\Service\OrderService").
```

Сборка падает. Приложению не быть.

---

## Почему это плохо — до технических решений

Цикл `A → B → A` почти всегда означает, что **один из них делает работу другого**. Это не «две услуги, которые сотрудничают» — это **две половины одного модуля**, которые кто-то разрезал по неосторожности.

Типовые сценарии:

| Цикл | Что на самом деле происходит |
|---|---|
| `OrderService ↔ PaymentService` | Оба знают про «состояние заказа». Один должен быть **агрегатом**, другой — **коллаборатором** |
| `Repository ↔ Service` | Репозиторий лезет в бизнес-логику, или сервис дублирует логику репозитория |
| `Validator ↔ Service` | Валидатор знает про service-методы, а service вызывает валидатор |
| `Logger ↔ Service` | Логгер получает service для форматирования, service логирует через логгер |

---

## Решения — от правильного к хакерскому

### 1. Вынести общий код в третий сервис (золотой стандарт)

```
Было:
OrderService → PaymentService → OrderService

Стало:
OrderService → OrderState → PaymentService
                  ↑
              (общая логика здесь)
```

```php
// ПЛОХО: два сервиса знают друг про друга
final class OrderService
{
    public function __construct(private PaymentService $payment) {}
    public function pay(Order $o): void
    {
        $o->markPendingPayment();
        $this->payment->charge($o);   // → PaymentService вызывает OrderService->save()
    }
}

final class PaymentService
{
    public function __construct(private OrderService $orders) {}
    public function charge(Order $o): void
    {
        // …
        $this->orders->save($o);     // ← цикл
    }
}
```

```php
// ХОРОШО: общая логика — в доменном объекте или отдельном сервисе
final class Order
{
    public function markPendingPayment(): void { /* … */ }
    public function markPaid(string $ref): void { /* … */ }
    // Мутации — здесь, не в сервисах
}

final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private PaymentGateway  $gateway,   // ← не PaymentService, а порт
    ) {}

    public function pay(string $id): void
    {
        $order = $this->repo->findById($id);
        $order->markPendingPayment();
        $ref = $this->gateway->charge($order);  // ← порт, не сервис
        $order->markPaid($ref);
        $this->repo->save($order);              // ← один владелец persist
    }
}

final class PaymentGateway
{
    public function __construct(private StripeClient $stripe) {}
    public function charge(Order $order): string { /* … */ }
    // Не знает про OrderService. Не знает про OrderRepository.
}
```

**Правило:** если `A` нужен `B`, а `B` нужен `A` — ищите, что общего. Это и есть третий сервис (или доменный объект).

---

### 2. `#[Lazy]` — прокси откладывает создание

Когда цикл **реальный и не устраняется** (например, `EventSubscriber` должен знать о сервисе, а сервис publishes event через `EventDispatcher`, который знает про subscriber):

```php
final class OrderService
{
    // Без Lazy:
    // public function __construct(private PaymentService $payment) {}
    // → цикл

    // С Lazy:
    public function __construct(
        #[Lazy]
        private PaymentService $payment,
    ) {}

    public function pay(Order $order): void
    {
        $this->payment->charge($order);  // ← здесь PaymentService реально создаётся
    }
}
```

Что генерирует Symfony:

```php
private function create0(): OrderService
{
    return $this->services['0'] = new OrderService(
        new \Symfony\Component\DependencyInjection\LazyProxy\Proxy\Instantiator\RealServiceProxy(
            'App\Service\PaymentService',
            fn() => $this->create1(),   // ← лениво: создастся только при первом вызове метода
        ),
    );
}
```

`RealServiceProxy` — обёртка с `__call`, `__get`, `__set`. Пока вы не вызвали `$this->payment->charge(...)`, реальный `PaymentService` **не существует в памяти**.

**Когда работает:**
- `A` использует `B` не в каждом запросе, а в 5%.
- Цикл — через `EventDispatcher`, `Messenger`, `Cache`.
- Вы **осознанно** принимаете, что архитектура неидеальна.

**Когда не работает:**
- `A` использует `B` в конструкторе: `public function __construct(#[Lazy] B $b) { $this->x = $b->getValue(); }` — proxy создаётся, метод вызывается, реальный `B` создаётся, который ищет `A` → цикл **всё равно**.
- `A` и `B` оба `shared: true`, и оба вызывают друг друга в одном request-цикле — proxy не спасёт, логика всё равно зациклена.

---

### 3. `ServiceLocator` / `service_subscriber` — ленивое получение по имени

Вместо инъекции в конструктор — получить по имени в момент необходимости:

```php
use Symfony\Component\DependencyInjection\ServiceLocator;

final class OrderService
{
    public function __construct(
        private ServiceLocator $locator,  // ← не PaymentService, а locator
    ) {}

    public function pay(Order $order): void
    {
        /** @var PaymentService $payment */
        $payment = $this->locator->get('App\Service\PaymentService');
        $payment->charge($order);
    }
}
```

Конфиг:

```yaml
services:
    App\Service\OrderService:
        arguments:
            $locator:
                - !service_locator
                    App\Service\PaymentService: '@App\Service\PaymentService'
```

Или через `ServiceSubscriberInterface` (Symfony 4.3+):

```php
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;

final class OrderService implements ServiceSubscriberInterface
{
    public static function getSubscribedServices(): array
    {
        return [
            PaymentService::class => PaymentService::class,
        ];
    }

    public function __construct(
        private ServiceLocator $locator,
    ) {}

    public function pay(Order $order): void
    {
        $this->locator->get(PaymentService::class)->charge($order);
    }
}
```

**Плюс:** граф DI больше не видит зависимости `OrderService → PaymentService`. Цикл **не существует на уровне контейнера**.

**Минус:**
- Теряется типобезопасность в конструкторе (IDE не подскажет).
- Теряется возможность легко замокать `PaymentService` в тесте (мокать придётся `ServiceLocator`).
- Это **скрытая зависимость** — код читается как «у OrderService нет зависимостей», а на самом деле они есть.

**Когда уместно:** `EventSubscriber`, `MessageHandler`, `Command` — всё, что живёт за диспетчером и не должно тянуть весь граф.

---

### 4. `shared: false` (prototype)

```yaml
services:
    App\Service\PaymentService:
        shared: false   # каждый get() — новый экземпляр
```

Теоретически разбивает «один и тот же объект в цикле». На практике — **костыль**:

- `PaymentService` создаётся N раз за запрос → память, скорость.
- Состояние (если есть) не разделяется → логические баги.
- Маскирует архитектурную проблему.

**Когда допустимо:** тесты, где `PaymentService` накапливает `array $calls` и вы проверяете, что вызвали 2 раза. В проде — почти никогда.

---

### 5. События / Messenger — асинхронное разделение

```
Было:  OrderService → PaymentService → OrderService

Стало:
OrderService  ──publish──→  PaymentEvent
                              ↓
                     PaymentHandler  (отдельный процесс / worker)
                              ↓
                     OrderService (через репозиторий, без цикла)
```

```php
// OrderService
public function pay(Order $order): void
{
    $order->markPendingPayment();
    $this->repo->save($order);
    $this->bus->dispatch(new PaymentRequested($order->id));  // ← событие
}

// PaymentHandler (Symfony Messenger, отдельный worker)
final class PaymentHandler
{
    public function __construct(
        private OrderRepository $repo,
        private StripeClient    $stripe,
    ) {}

    public function __invoke(PaymentRequested $event): void
    {
        $order = $this->repo->findById($event->orderId);
        $ref   = $this->stripe->charge(...);
        $order->markPaid($ref);
        $this->repo->save($order);
    }
}
```

Цикл **исчезает**, потому что `OrderService` не знает про `PaymentHandler`. Связь — через событие.

**Когда уместно:**
- `PaymentService` — тяжёлый (зовёт внешний API, 2–5 сек).
- Нужен retry, idempotency, dead-letter queue.
- Пользователь не должен ждать.

**Когда не уместно:**
- Результат нужен **сразу** (синхронный checkout).
- Система из 2 сервисов, и вы вводите инфраструктуру очереди ради одного цикла.

---

### 6. `ContainerAwareInterface` — антипаттерн (упоминание для полноты)

```php
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class OrderService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function pay(Order $order): void
    {
        $payment = $this->container->get(PaymentService::class);
        $payment->charge($order);
    }
}
```

**Почему плохо:**
- Полная потеря типобезопасности.
- Невозможно замокать.
- Невозможно увидеть зависимости, читая конструктор.
- `RemoveUnusedDefinitionsPass` не понимает, что `PaymentService` нужен → может удалить.

**Единственное оправдание:** legacy-код, где вы не можете переписать. Новых случаев — нет.

---

## Сравнение

| Метод | Типобезопасность | Тестируемость | Сложность | Когда |
|---|---|---|---|---|
| Вынести в третий сервис | ✅ | ✅ | Средняя | **Всегда сначала** |
| `#[Lazy]` | ✅ | ✅ (мок через прокси) | Низкая | Реальный цикл, 5% использования |
| `ServiceLocator` | ⚠️ (потеря в констр.) | ⚠️ (мокать locator) | Низкая | Subscriber, Handler, Command |
| `shared: false` | ✅ | ✅ | Минимальная | Только тесты |
| События / Messenger | ✅ | ✅ | Высокая (инфраструктура) | Асинхронность нужна |
| `ContainerAware` | ❌ | ❌ | Минимальная | Только legacy |

---

## Практический чек-лист

Когда видите `Circular reference`:

1. **Откройте оба класса. Найдите, что общего.** Если один метод можно вынести в третий сервис — вынесите. Это 80% случаев.

2. **Если один из них — `EventSubscriber` или `MessageHandler`** — используйте `ServiceSubscriberInterface`. Цикл через диспетчер — нормальный паттерн.

3. **Если один из них используется в 5% запросов** — `#[Lazy]`. Одно слово, работает, не ломает типобезопасность.

4. **Если результат нужен асинхронно** — вынесите в `MessageHandler`. Бонус: retry, idempotency, мониторинг.

5. **Если ничего не помогает и вы не можете переписать** — `ServiceLocator`. Не `ContainerAware`.

6. **`shared: false`** — только если вы точно понимаете, что делаете, и это тест.

---

## Одна фраза

Циклическая зависимость — это не «DI-контейнер не справился». Это **архитектурный запах**: два модуля, которые должны быть одним или которыми должен управлять третий. `#[Lazy]` и `ServiceLocator` — это **обезболивающие**, а не **лечение**. Снимите боль, потом найдите, что болит.

> ⏱ 44.7s · prompt 20189 tok · output 5722 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о конкретном сценарии: после регистрации пользователя нужно (1) отправить email и (2) обновить поисковый индекс, и это не должно замедлять ответ. Нужно описать варианты и trade-offs в Symfony.

Варианты:
1. Symfony Messenger (async transport) — основной вариант
2. Symfony Events (EventDispatcher) + Messenger
3. Queue (RabbitMQ / Redis / Sqs) напрямую
4. `async` через `Fiber` (PHP 8.1+) — но это не настоящая асинхронность для I/O
5. `register_shutdown_function` — антипаттерн
6. Fire-and-forget через `curl` / `pcntl` — хаки

Наиболее правильный ответ в Symfony — Messenger + async transport. Но нужно показать спектр и trade-offs.

Структурирую:
1. Контекст задачи (что именно нужно сделать)
2. Вариант 1: Messenger + async transport (основной)
3. Вариант 2: EventDispatcher + Messenger (событийная модель)
4. Вариант 3: Внешняя очередь (RabbitMQ/SQS) напрямую
5. Вариант 4: `Fiber` / async (почему это не решение)
6. Вариант 5: `register_shutdown_function` (почему плохо)
7. Сравнительная таблица
8. Рекомендация для данного сценария

Напишу на русском, с кодом, как в предыдущих ответах.

</details>

**Кандидат:**

# Асинхронные побочные эффекты после регистрации

## Задача

```
POST /register
  → создать User в БД
  → отправить welcome email        ← не может ждать
  → обновить поисковый индекс      ← не может ждать
  → 201 Created                   ← пользователь ждёт
```

Ключевое ограничение: **ответ пользователю не должен зависеть от скорости email-провайдера и Elasticsearch**. Но при этом:

- Email **должен** прийти (не «потерялся»).
- Индекс **должен** обновиться (иначе поиск врёт).
- Если пользователь обновит профиль через 2 секунды — индекс не должен быть «старым».
- Сервер не должен хранить очередь в памяти (перезагрузка = потеря).

---

## Вариант 1: Symfony Messenger + async transport

Это «родной» способ. Symfony уже обёрнул очередь в фреймворк.

### Схема

```
Controller
  → RegistrationService
    → $bus->dispatch(new SendWelcomeEmail($userId))     ← в очередь
    → $bus->dispatch(new UpdateSearchIndex($userId))     ← в очередь
    → 201 Created                                        ← пользователь свободен

[Worker-процесс (separate from web)]
  → consume() из DoctrineTransport / Redis / RabbitMQ
    → SendWelcomeEmailHandler::handle()
    → UpdateSearchIndexHandler::handle()
```

### Код

**1. Message-объекты (immutable DTO):**

```php
// src/Message/SendWelcomeEmail.php
final readonly class SendWelcomeEmail
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $name,
    ) {}
}

// src/Message/UpdateSearchIndex.php
final readonly class UpdateSearchIndex
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public string $registeredAt,
    ) {}
}
```

**2. Диспетчеризация в сервисе (синхронно, быстро):**

```php
// src/Service/RegistrationService.php
final class RegistrationService
{
    public function __construct(
        private UserRepository $users,
        private BusInterface   $bus,
    ) {}

    public function register(string $name, string $email, string $password): User
    {
        $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT));

        // Транзакция: только БД-операция
        $this->users->save($user);

        // Асинхронные побочные эффекты — В ОЧЕРЕДЬ, не в код
        $this->bus->dispatch(new SendWelcomeEmail($user->id, $user->email, $user->name));
        $this->bus->dispatch(new UpdateSearchIndex($user->id, $user->name, $user->email, $user->createdAt));

        return $user;
    }
}
```

**3. Обработчики (выполняются в worker):**

```php
// src/MessageHandler/SendWelcomeEmailHandler.php
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SendWelcomeEmail $msg): void
    {
        try {
            $this->mailer->send(
                to:      $msg->email,
                subject: "Welcome, {$msg->name}!",
                body:    $this->renderTemplate($msg),
            );
        } catch (\Throwable $e) {
            $this->logger->error('Welcome email failed', [
                'user' => $msg->userId,
                'error' => $e->getMessage(),
            ]);
            // Не бросаем — Worker перепостит (см. ниже)
        }
    }

    private function renderTemplate(SendWelcomeEmail $msg): string
    {
        return "Hi {$msg->name}, thank you for registering!";
    }
}

// src/MessageHandler/UpdateSearchIndexHandler.php
final class UpdateSearchIndexHandler
{
    public function __construct(
        private SearchIndexClient $search,
        private LoggerInterface   $logger,
    ) {}

    public function __invoke(UpdateSearchIndex $msg): void
    {
        $this->search->upsert([
            'id'             => $msg->userId,
            'name'           => $msg->name,
            'email'          => $msg->email,
            'registered_at'  => $msg->registeredAt,
        ]);
    }
}
```

**4. Конфигурация транспорта:**

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            # Разные очереди для разных типов сообщений
            async_email:    '%env(MESSENGER_EMAIL_DSN)%'    # amqp://mail@localhost:5672/%2f
            async_search:   '%env(MESSENGER_SEARCH_DSN)%'   # redis://localhost:6379/0
            sync:           'sync://'

        routing:
            # Каждый тип сообщения → свой транспорт
            App\Message\SendWelcomeEmail:    async_email
            App\Message\UpdateSearchIndex:   async_search

            # Валидация — синхронная (быстрая, без очереди)
            App\Message\ValidateEmail:       sync

        # Retry-политика
        failure_transport: failed
        recovery:
            retries: 3
            delay: 1000        # ms
            multiplier: 2
            max_delay: 30000
```

**5. Worker (отдельный процесс, systemd / supervisor / Docker):**

```bash
# systemd unit: app-worker.service
ExecStart=/usr/local/bin/php /var/www/app/bin/console messenger:consume async_email async_search --limit=10000 --time-limit=3600
```

Или Docker Compose:

```yaml
services:
    worker:
        build: .
        command: php bin/console messenger:consume async_email async_search
        deploy:
            replicas: 2
        environment:
            MESSENGER_EMAIL_DSN: amqp://mail@rabbit:5672/%2f
            MESSENGER_SEARCH_DSN: redis://redis:6379/0
```

### Что даёт этот вариант

| Плюс | Минус |
|---|---|
| Ответ пользователю — миллисекунды | Нужен worker-процесс (инфраструктура) |
| Email упал — сообщение в очереди, retry автоматически | Середина: если worker упал на 5-й из 10 сообщений — 1-е 4 потеряны (без ACK) |
| Разные очереди = разные SLA (email — 5 сек, индекс — 50 сек) | Нужно следить за `messenger:consume` (модель) |
| `messenger:failed` — таблица упавших сообщений | В dev — `sync://`, в prod — amqp/redis. Разное поведение. |
| `messenger:stop-workers` при деплое | |
| Тесты: `$bus->dispatch()` — мок, handler — отдельный тест | |

---

## Вариант 2: EventDispatcher + Messenger (событийная модель)

Если в будущем будет 5, 10, 20 слушателей на `UserRegistered`, а не 2 — выносите событие.

```php
// src/Event/UserRegistered.php
final class UserRegistered
{
    public function __construct(public User $user) {}
}

// src/Service/RegistrationService.php
public function register(string $name, string $email, string $password): User
{
    $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT));
    $this->users->save($user);

    // Одно событие, N слушателей
    $this->eventDispatcher->dispatch(new UserRegistered($user));

    return $user;
}
```

```php
// src/EventListener/SendWelcomeEmailListener.php
final class SendWelcomeEmailListener
{
    public function __construct(private BusInterface $bus) {}

    #[AsEventListener(event: UserRegistered::class, method: 'onUserRegistered')]
    public function onUserRegistered(UserRegistered $event): void
    {
        $this->bus->dispatch(new SendWelcomeEmail(
            $event->user->id,
            $event->user->email,
            $event->user->name,
        ));
    }
}

// src/EventListener/UpdateSearchIndexListener.php
final class UpdateSearchIndexListener
{
    public function __construct(private BusInterface $bus) {}

    #[AsEventListener(event: UserRegistered::class, method: 'onUserRegistered')]
    public function onUserRegistered(UserRegistered $event): void
    {
        $this->bus->dispatch(new UpdateSearchIndex(
            $event->user->id,
            $event->user->name,
            $event->user->email,
            $event->user->createdAt,
        ));
    }
}
```

### Почему это лучше, чем 2 прямых `dispatch`

| Сценарий | Прямые `dispatch` | Event + `dispatch` |
|---|---|---|
| Добавить «отправить SMS» | Править `RegistrationService` | Новый `EventListener`, не трогать сервис |
| Добавить «записать в CRM» | Править `RegistrationService` | Новый `EventListener` |
| Тестирование | Мокать 2 bus-вызова | Мокать 1 `eventDispatcher` |
| OCP | Нарушается (новый тип = правка сервиса) | Соблюдается (новый listener = новый файл) |

**Но:** если слушателей 2 и они не вырастут — EventDispatcher избыточен. Два прямых `dispatch` проще и читаемее.

---

## Вариант 3: Внешняя очередь напрямую (RabbitMQ / SQS / Redis)

Обойти Messenger, говорить с брокером напрямую:

```php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

final class RegistrationService
{
    public function __construct(
        private UserRepository $users,
        private AMQPChannel   $channel,   // ← «голый» канал
    ) {}

    public function register(...): User
    {
        $user = new User(...);
        $this->users->save($user);

        $this->channel->basic_publish(
            json_encode(['type' => 'welcome_email', 'user_id' => $user->id]),
            exchange: 'events',
            routing_key: 'email.welcome',
        );
        $this->channel->basic_publish(
            json_encode(['type' => 'search_index', 'user_id' => $user->id]),
            exchange: 'events',
            routing_key: 'search.user',
        );

        return $user;
    }
}
```

### Когда это оправдано

- У вас **уже есть** RabbitMQ / SQS как инфраструктурный стандарт.
- Messenger не тянет нужный протокол (например, AWS SQS с DLQ + Lambda).
- Микро-сервисы на разных языках (Go-сервис потребляет ту же очередь).
- Нужен **контроль над ACK/NACK** на уровне брокера (Messenger абстрагирует это).

### Когда это плохо

- Вы пишете свой consumer, свой retry, свой dead-letter. Messenger это уже сделал.
- `AMQPChannel` — небезопасный ресурс (socket). Управление жизненным циклом на вас.
- Тесты: мокать `AMQPChannel` — боль. Мокать `BusInterface` — тривиально.

**Вывод:** для Symfony-приложения в одном сервисе — Messenger покрывает 95% случаев. Прямой AMQP — только если инфраструктура уже требует.

---

## Вариант 4: `Fiber` (PHP 8.1) — почему это НЕ решение

```php
// ТЕМПТЕНИЕ:
public function register(...): User
{
    $user = new User(...);
    $this->users->save($user);

    // «Асинхронно» отправим email
    \Fiber::start(function () use ($user) {
        $this->mailer->send(...);  // ← блокирующий I/O внутри Fiber
    })->start();

    // «Асинхронно» обновим индекс
    \Fiber::start(function () use ($user) {
        $this->search->upsert(...);
    })->start();

    return $user;  // ← пользователь получил ответ
}
```

**Почему не работает:**

1. `Fiber` — **кооперативная** асинхронность. Пока Fiber выполняет `$this->mailer->send()` (блокирующий TCP-вызов), **весь PHP-процесс заморожен**. Другой Fiber не запустится, пока первый не завершится или не вызовёт `Fiber::suspend()`.

2. У `MailerInterface` и `SearchIndexClient` нет Fiber-aware API. Они делают `fsockopen()` / `curl_exec()` — блокирующие вызовы. Fiber не поможет.

3. Даже если переписать клиент на `stream_socket` + `Fiber::suspend()` — это **один процесс**. Если сервер перезагрузится на 2-й миллисекунде — оба Fibers умирают, сообщения теряны.

4. `Fiber` в PHP 8.1 — это **синтаксис**, а не runtime. Нет event loop, нет `await`, нет интеграции с фреймворком.

**Fiber пригодятся**, когда:
- У вас **весь стек** Fiber-aware (Swoole / FrankenPHP / custom event loop).
- И вы управляете процессом сами, а не через PHP-FPM + Apache.

Для стандартного Symfony + PHP-FPM — нет.

---

## Вариант 5: `register_shutdown_function` — антипаттерн

```php
public function register(...): User
{
    $user = new User(...);
    $this->users->save($user);

    register_shutdown_function(function () use ($user) {
        $this->mailer->send(...);
        $this->search->upsert(...);
    });

    return $user;
}
```

**Почему плохо:**

| Проблема | Деталь |
|---|---|
| Нет гарантии | PHP-FPM может убить процесс по `request_terminate_timeout` |
| Нет retry | Отправился ли email? Не знаете. |
| Нет мониторинга | Нет очереди, нет метрик, нет dead-letter |
| Блокировка worker | PHP-FPM worker занят на время `curl` к email-серверу. Следующий запрос ждёт. |
| Потеря при деплое | `kill -9` на деплой → shutdown-функция не выполнится |
| Тестирование | Невозможно перехватить `register_shutdown_function` в юнит-тесте |

**Когда допустим:** одноразовый скрипт, который вы запускаете вручную и не переиспользуете. В продакшене — нет.

---

## Вариант 6: `curl` fire-and-forget из CLI

```php
// В worker-скрипте:
exec("curl -X POST http://email-service/api/send -d '{".$payload."}' &");
```

**Почему плохо:** процесс-«детище» не контролируется. Нет ACK. Нет retry. Нет логирования. `exec()` — дыра безопасности. Не масштабируется.

---

## Сравнительная таблица

| Критерий | Messenger | Event + Messenger | Прямой AMQP | Fiber | Shutdown |
|---|---|---|---|---|---|
| Ответ пользователю быстрый | ✅ | ✅ | ✅ | ✅ (но блокирует процесс) | ✅ |
| Надёжность доставки | ✅ (retry + DLQ) | ✅ | ✅ (брокер) | ❌ | ❌ |
| Тестируемость | ✅ (мок `BusInterface`) | ✅ | ⚠️ (мок `AMQPChannel`) | ❌ | ❌ |
| Инфраструктура | Worker + транспорт | Worker + транспорт | Брокер + consumer | Нет | Нет |
| OCP (новый listener) | Правка сервиса | Новый файл | Новый consumer | Правка | Правка |
| Наблюдаемость | `messenger:stats` | `messenger:stats` | Брокер-метрики | Нет | Нет |
| Сложность внедрения | Низкая | Низкая | Средняя | Высокая (и не работает) | Низкая (и не работает) |

---

## Рекомендация для этого сценария

```
RegistrationService
    ├── БД-транзакция (синхронная, < 5ms)
    │
    ├── dispatch(SendWelcomeEmail)    → очередь async_email
    ├── dispatch(UpdateSearchIndex)   → очередь async_search
    │
    └── return User                   → 201 Created
```

**Почему именно так:**

1. **Messenger + 2 транспорта** — один email, один индекс. Разные SLA, разные retry-политики. Email упал 3 раза → в `failed`, оператор увидит. Индекс упал → retry с задержкой, Elasticsearch мог быть на перезагрузке.

2. **Worker — отдельный процесс.** Не блокирует PHP-FPM. Может масштабироваться (2 реплики). При деплое — `messenger:stop-workers` корректно завершает текущие сообщения.

3. **Транзакция в БД — синхронная.** Если `save()` упала — ни email, ни индекс не ушли. Если `save()` прошла, а `dispatch()` упала (очередь недоступна) — это **отдельный инцидент**, который ловится и логируется. (Ниже — как это обработать.)

4. **Если слушателей вырастет > 3** — мигрировать на `EventDispatcher` + `Messenger` без изменения обработчиков.

### Обработка «очередь недоступна»

```php
public function register(string $name, string $email, string $password): User
{
    $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT));
    $this->users->save($user);

    try {
        $this->bus->dispatch(new SendWelcomeEmail(...));
        $this->bus->dispatch(new UpdateSearchIndex(...));
    } catch (TransportException $e) {
        // Очередь недоступна. Пользователь Зарегистрирован.
        // Логгируем КРИТИЧНО + алерт. НЕ бросаем исключение наружу
        // (иначе 500 пользователю, а он уже в системе).
        $this->logger->critical('Async dispatch failed, user registered', [
            'user'  => $user->id,
            'error' => $e->getMessage(),
        ]);
        // Опционально: записать в БД-таблицу pending_events для повторной отправки
    }

    return $user;
}
```

### Идентичность и порядок

Если пользователь регистрируется и сразу обновляет email (реалистичный сценарий):

```
t=0s:  dispatch(SendWelcomeEmail, email: a@old.com)
t=2s:  dispatch(UpdateSearchIndex, email: b@new.com)
```

Если email-очередь обрабатывает медленно, email уйдёт на `a@old.com`, а индекс будет на `b@new.com`. **Это нормально для welcome email** (он один раз, и если email устарел — это мелочь). Для индекса — `upsert` с `registered_at` как version stamp защищает от «старое покрыло новое».

Если порядок критичен (например, 10 последовательных событий) — это уже **не** «fire-and-forget», а **ordered processing**, и нужен другой подход (каждая очередь на пользователя, `consumer` с `prefetch=1`). Для welcome email + индекс — не нужно.

---

## Что я бы **не** делал

| Плохо | Почему |
|---|---|
| `curl -X POST … &` из PHP | Нет ACK, нет retry, нет логирования, потеря при деплое |
| `register_shutdown_function` | Нет гарантии, блокирует worker, нет мониторинга |
| `Fiber` в PHP-FPM | Блокирует весь процесс, нет runtime, нет гарантии доставки |
| `ContainerAware` в handler | Потеря типобезопасности, невозможно мокать |
| Один общий `queue` для email и индекс | Один медленный consumer тормозит оба. Разные SLA = разные очереди |
| `dispatch_sync` в проде | Это синхронный вызов. Пользователь ждёт email. |

> ⏱ 39.5s · prompt 27727 tok · output 5137 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему контроля доступа в Symfony: аутентификация, роли, voters. И ключевой вопрос — когда voter, а когда достаточно ролей.

Мне нужно:
1. Объяснить архитектуру Symfony Security: firewalls, authenticators, token, roles.
2. Показать, как устроена аутентификация (Authenticator-based, modern approach).
3. Роли — что это, как назначаются, где проверяются.
4. Voters — механизм, когда роли не хватает.
5. Конкретные примеры кода.
6. Ключевой вопрос: когда voter, а когда роли? Это должен быть не абстрактный ответ, а с конкретными критериями.

Структура:
1. Общая архитектура (firewall → authenticator → token → roles → voter)
2. Аутентификация (LoginFormAuthenticator как пример)
3. Роли (назначение, проверка)
4. Voters (AccessVoter, примеры)
5. Когда voter, а когда роли — критерии
6. Антипаттерны

Важно показать реальный код, а не абстракции. И ответить на "когда" конкретно.

</details>

**Кандидат:**

# Контроль доступа в Symfony

## Архитектура: цепочка проверки

Каждый запрос проходит через конвейер:

```
HTTP Request
    │
    ▼
┌─────────────────────────────────────────────────────────┐
│  Firewall (config/packages/security.yaml)              │
│                                                         │
│  1. Какой authenticator? (login_form, jwt, api_key…)   │
│  2. Кто этот пользователь? (Token)                      │
│  3. Какие у него роли? (Token->getRoles())             │
│  4. Разрешён ли доступ?                                 │
│     ├─ access_control (URI-based)                      │
│     ├─ #[IsGranted] (контроллер/метод)                 │
│     └─ $this->isGranted() (в коде)                     │
│        └─ Voter(s) → true / false / abstain            │
│                                                         │
│  Если разрешён → Controller                            │
│  Если нет → 401 / 403 / AccessDeniedException          │
└─────────────────────────────────────────────────────────┘
```

Ключевое: **роли — это данные**, а **voter — это логика**. Роли отвечают «кто ты», voter отвечает «разрешено ли *тебе* сделать *это*».

---

## 1. Аутентификация: кто этот пользователь

### Modern approach (Symfony 5.3+): Authenticator

```php
// src/Security/LoginFormAuthenticator.php
final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private UserLoaderInterface $userLoader,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    // 1. Из чего достать креденшелы
    public function getCredentials(Request $request): array
    {
        $request->getSession(); // инициализация сессии

        return [
            'username' => $request->getPayload()->getString('username') ?: '',
            'password' => $request->getPayload()->getString('password') ?: '',
            '_csrf_token' => $request->getPayload()->getString('_csrf_token') ?: '',
        ];
    }

    // 2. Валидация креденшелов
    public function checkCredentials(array $credentials): bool
    {
        // CSRF
        $this->csrfTokenManager->isTokenValid(
            'authenticate',
            $credentials['_csrf_token'],
        ) or throw new \RuntimeException('Invalid CSRF token');

        // Пароль
        $user = $this->userLoader->loadUserByIdentifier($credentials['username']);
        return $this->passwordHasher->isPasswordValid($user, $credentials['password']);
    }

    // 3. Куда редиректнуть
    public function onAuthenticationSuccess(Request $request, string $firewallName): ?Response
    {
        if ($target = $this->getTargetUrl($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }
        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && 'POST' === $request->getMethod();
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        $this->checkCredentials($credentials);

        return new SelfValidatingPassport(
            UserBadge($credentials['username']),
            new CsrfTokenBadge('authenticate', $credentials['_csrf_token']),
        );
    }
}
```

### Конфиг

```yaml
# config/packages/security.yaml
security:
    enable_authenticator_manager: true

    password_hashers:
        App\Entity\User:
            algorithm: auto
            cost: 12

    providers:
        app_user:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            lazy: true
            provider: app_user
            form_login:
                authenticator: App\Security\LoginFormAuthenticator
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            logout:
                path: app_logout
                target: app_login

    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER]
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: ROLE_USER }
        - { path: ^/login, roles: PUBLIC_ACCESS }
```

### Что происходит под капотом

1. Запрос приходит на `/dashboard`.
2. Firewall `main` срабатывает.
3. Если сессия/токен есть — `TokenStorage` содержит `TokenInterface` с `User` и `roles`.
4. `AccessDecisionManager` (или современный `AccessChecker`) проверяет `access_control`.
5. Если `roles: ROLE_USER` — и токен содержит `ROLE_USER` (или `ROLE_ADMIN`, который наследует) — доступ разрешён.
6. Если нет — `AccessDeniedException` → 403 (или 401, если не аутентифицирован).

**Важно:** `access_control` проверяет **только роли**. Никакой бизнес-логики. «/admin» — для `ROLE_ADMIN`. Точка.

---

## 2. Роли: что они дают и чего не дают

### Назначение ролей

```php
// src/Entity/User.php
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // гарантированная базовая роль
        return array_unique($roles);
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }
}
```

### Проверка ролей

```php
// В контроллере:
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/users', name: 'admin_users')]
public function list(): Response
{
    // Только ROLE_ADMIN или выше
}

// В шаблоне Twig:
{% if is_granted('ROLE_ADMIN') %}
    <button>Удалить</button>
{% endif %}

// В коде сервиса:
$this->denyUnlessAdmin();  // → throw AccessDeniedException
```

### Что роли дают

- **Статичное** разграничение: «админ / пользователь / гость».
- **URI-based** контроль: `access_control`.
- **Простые** проверки: `#[IsGranted('ROLE_X')]`.
- **Наследование**: `ROLE_SUPER_ADMIN → ROLE_ADMIN → ROLE_USER`.

### Чего роли НЕ дают

- «Может ли **этот** пользователь удалить **этот** заказ?»
- «Может ли редактор публиковать **статьи своего раздела**?»
- «Может ли менеджер одобрить заказ, если он **не превысил лимит**?»
- «Может ли пользователь увидеть **свой** профиль, но не чужой?»

Всё это — **вотер**.

---

## 3. Voters: когда роли не хватает

### Механизм

```
$security->isGranted('order:delete', $order)
    │
    ▼
AccessDecisionManager
    │
    ├─ Voter 1 (OrderVoter) → ACCESS_GRANTED  ← стоп
    ├─ Voter 2 (OrderVoter) → ACCESS_DENIED
    ├─ Voter 3 (DenyVoter)  → ACCESS_ABSTAIN
    │
    ▼
Итог: GRANTED (первый GRANTED побеждает)
       Если все ABSTAIN → DENIED
       Если есть DENIED и нет GRANTED → DENIED
```

Три результата:

| Результат | Значение |
|---|---|
| `ACCESS_GRANTED` | «Да, разрешаю» — **останавливает** цепочку |
| `ACCESS_DENIED` | «Нет, запрещаю» — **останавливает** цепочку (если нет GRANTED) |
| `ACCESS_ABSTAIN` | «Я не знаю, спросите следующих» — **продолжает** |

### Пример: `OrderVoter`

```php
// src/Security/Voter/OrderVoter.php
final class OrderVoter extends Voter
{
    private const OWNER   = 'order:owner';
    private const DELETE  = 'order:delete';
    private const APPROVE = 'order:approve';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Этот voter занимается только Order
        return in_array($attribute, [self::OWNER, self::DELETE, self::APPROVE], true)
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user   = $token->getUser();
        $order  = $subject;

        return match ($attribute) {
            self::OWNER   => $this->voteOwner($user, $order),
            self::DELETE  => $this->voteDelete($user, $order),
            self::APPROVE => $this->voteApprove($user, $order),
            default       => false,
        };
    }

    // ── Логика ────────────────────────────────────────────────

    private function voteOwner(User $user, Order $order): bool
    {
        return $user->getId() === $order->getCustomerId();
    }

    private function voteDelete(User $user, Order $order): bool
    {
        // Владелец может удалить только свои заказы в статусе DRAFT
        if ($user->getId() !== $order->getCustomerId()) {
            return false;
        }
        return $order->getStatus() === OrderStatus::Draft;
    }

    private function voteApprove(User $user, Order $order): bool
    {
        // Админ может одобрить любой
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Менеджер своего отдела — только если лимит не превышен
        if (in_array('ROLE_MANAGER', $user->getRoles(), true)) {
            return $order->getDepartment() === $user->getDepartment()
                && $order->getTotalCents() <= $user->getApprovalLimitCents();
        }

        return false;
    }
}
```

### Использование

```php
// В контроллере:
#[Route('/orders/{id}', name: 'order_show', requirements: {'id' => '\d+'})]
public function show(Order $order): Response
{
    // Пользователь видит только свои заказы
    $this->denyUnlessGranted('order:owner', $order);
    // ↓ AccessDeniedException → 403

    return $this->render('order/show.html.twig', ['order' => $order]);
}

// В шаблоне:
{% if is_granted('order:delete', order) %}
    <a href="{{ path('order_delete', {id: order.id}) }}" class="btn-danger">Удалить</a>
{% endif %}

{% if is_granted('order:approve', order) %}
    <button data-action="approve">Одобрить</button>
{% endif %}
```

```php
// В сервисе (не в контроллере!):
final class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private AccessChecker   $checker,
    ) {}

    public function delete(int $orderId, User $currentUser): void
    {
        $order = $this->repo->findById($orderId);

        // Бросает AccessDeniedException, если нет
        $this->checker->isGranted('order:delete', $order);

        $order->markDeleted();
        $this->repo->save($order);
    }
}
```

### `DenyVoter` — последняя защита

Symfony включает по умолчанию `ExpressionLanguageVoter` и `AuthenticatedVoter`. Но если **ни один** voter не выражает мнения (`ABSTAIN`), доступ **запрещён** по умолчанию. Это «deny by default».

```php
// Если ни OrderVoter, ни кто-то другой не вернул GRANTED/DENIED:
// → AccessDeniedException
// → 403
```

Это важно: **отсутствие voter'а ≠ доступ разрешён**. Отсутствие voter'а = «никто не подтвердил» = запрет.

---

## 4. Когда voter, а когда роли

### Критерий

| Вопрос | Ответ | Механизм |
|---|---|---|
| «Кто ты?» (админ, пользователь, гость) | Роль | `ROLE_ADMIN`, `ROLE_USER` |
| «Разрешён ли *этот* URI?» | Роль | `access_control` |
| «Может ли *этот* пользователь сделать *это* с *этим* объектом?» | **Voter** | `isGranted('action', $subject)` |
| «Удовлетворяет ли состояние объекта условию?» | **Voter** | `$order->status === Draft` |
| «Может ли пользователь A видеть объект B?» | **Voter** | `$user->id === $order->customerId` |

### Конкретные примеры

**Достаточно ролей:**

```yaml
# Кто: админ. Что: любой URI под /admin.
- { path: ^/admin, roles: ROLE_ADMIN }

# Кто: авторизованный. Что: /profile.
- { path: ^/profile, roles: ROLE_USER }
```

Здесь нет «объекта», нет «состояния», нет «отношения между пользователем и сущностью». Только «какая роль даёт доступ к этой зоне».

**Нужен voter:**

```php
// «Может ли пользователь X удалить заказ Y?»
isGranted('order:delete', $order)

// «Может ли пользователь X публиковать статью Z?»
isGranted('article:publish', $article)

// «Может ли пользователь X видеть метрику W своего отдела?»
isGranted('metric:view', $metric)
```

Здесь есть **три компонента**: пользователь, действие, объект. Роль может сказать «ты админ», но не может сказать «этот заказ — твой» или «статья — в твоём разделе».

### Практическое правило

> Если проверка сводится к `in_array('ROLE_X', $user->getRoles())` — это **роль**.
>
> Если в проверке участвует **состояние объекта**, **владение**, **отдел**, **лимит** — это **voter**.

---

## 5. Expression Language — промежуточный уровень

Для простых условий, которые не заслуживают voter'а, но сложнее «роль = X»:

```twig
{# Шаблон #}
{% if is_granted('ROLE_USER') and order.customer == app.user %}
    <button>Отменить</button>
{% endif %}
```

```php
// Атрибут
#[IsGranted("object.id == user.id and object.status == 'draft'")]
public function cancel(Order $order): void { /* … */ }
```

```yaml
# access_control (редко, но бывает)
- { path: ^/invoices/\d+, roles: IS_AUTHENTICATED_FULLY,
    if: "is_granted('invoice:own', request.get('id'))" }
```

**Когда использовать:**
- Условие на 1–2 строки.
- Нет «отдела», «лимита», «иерархии».
- Вы не хотите создавать `Voter`-класс ради `object.owner == user`.

**Когда НЕ использовать:**
- Условие на 5+ строк с `and`/`or`.
- Нужно повторно использовать в 3+ местах.
- В условии есть вызов метода (`$order->isEditableBy($user)`).

В этих случаях — **voter**. Expression Language — это «inline-voter», удобный, но не масштабируемый.

---

## 6. Архитектурные правила

### Правило 1: Voter знает только домен, не инфраструктуру

```php
// ПЛОХО:
private function voteApprove(User $user, Order $order): bool
{
    return $this->redis->get("limit:{$user->id}") > $order->getTotal();
    // Voter знает про Redis. Если поменяете Redis на Memcached — правите voter.
}

// ХОРОШО:
private function voteApprove(User $user, Order $order): bool
{
    return $order->getTotalCents() <= $user->getApprovalLimitCents();
    // Voter знает только доменные сущности. Лимит — свойство User.
}
```

### Правило 2: `supports()` — узкий

```php
// ПЛОХО:
protected function supports(string $attribute, mixed $subject): bool
{
    return true;  // «я знаю всё»
}

// ХОРОШО:
protected function supports(string $attribute, mixed $subject): bool
{
    return in_array($attribute, [self::DELETE, self::APPROVE], true)
        && $subject instanceof Order;
}
```

Узкий `supports()` → `voteOnAttribute()` вызывается только когда нужно. Не зацикливаетесь.

### Правило 3: Один voter на доменную сущность

```
OrderVoter      → Order
ArticleVoter    → Article
InvoiceVoter    → Invoice
ProjectVoter    → Project
```

Не `GenericVoter` с 40 `match`-ветками. Если `OrderVoter` на 200 строк — разбейте на `OrderDeleteVoter` и `OrderApproveVoter`. Но в большинстве случаев один voter на сущность — нормально.

### Правило 4: Не дублируйте логику из контроллера в voter

```php
// ПЛОХО:
public function delete(Order $order): Response
{
    if ($order->getStatus() !== OrderStatus::Draft) {
        return $this->json(['error' => 'Cannot delete'], 403);
    }
    if ($order->getCustomer()->getId() !== $this->getUser()->getId()) {
        return $this->json(['error' => 'Not your order'], 403);
    }
    // … удаление
}

// Логика в контроллере. Шаблон проверяет отдельно. API-клиент проверяет в третьем месте.
// Три места, три разных формулировки, один баг.

// ХОРОШО:
public function delete(Order $order): Response
{
    $this->denyUnlessGranted('order:delete', $order);  // ← всё в voter
    $order->markDeleted();
    return $this->json(['ok' => true]);
}
```

### Правило 5: `denyUnlessGranted` в домене, не только в контроллере

Контроллер — **один** из входных точек. Есть ещё:
- API-эндпоинт.
- Console-команда.
- MessageHandler.
- Cron-задача.

Если проверка «только в контроллере» — `bin/console order:approve --id=42` обойдёт её. **Voter + `denyUnlessGranted` в сервисе** — единственный надёжный способ.

```php
// src/Service/OrderService.php
public function approve(Order $order, User $approver): void
{
    $this->accessChecker->isGranted('order:approve', $order);
    // ↑ Бросит AccessDeniedException, если нет.
    //   Не зависит от того, кто вызвал: контроллер, console, handler.

    $order->markApproved($approver);
    $this->repo->save($order);
}
```

---

## 7. Антипаттерны

| Антипаттерн | Почему плохо | Решение |
|---|---|---|
| `if ($user->getRole() === 'admin')` в 15 местах | Роль размазана, нет единой точки | `isGranted('ROLE_ADMIN')` или `access_control` |
| `if ($user->id === $order->customer->id)` в шаблоне | Логика в презентации, нет для API | Voter |
| Voter с 50 `match`-ветками | Нарушение SRP, невозможно тестировать | Разделить на voter'ы по сущности |
| `$container->get('security')->isGranted(...)` | Обход DI, невозможно мокать | Инжект `AccessChecker` |
| Проверка прав **после** мутации БД | «Удалили, а потом поняли, что не имели права» | `denyUnlessGranted` **до** мутации |
| Voter знает про `EntityManager` | Связь домена с инфраструктурой | Voter работает с доменными объектами |
| `ROLE_USER` как «авторизован» | Роль — это привилегия, не статус | `IS_AUTHENTICATED_FULLY` |

---

## 8. Итоговая схема

```
┌─────────────────────────────────────────────────────────────────┐
│                        ЗАПРОС                                    │
│                                                                 │
│  1. Firewall: кто ты? → Token (User + Roles)                    │
│                                                                 │
│  2. access_control: URI + Role                                  │
│     /admin → ROLE_ADMIN                                         │
│     /profile → ROLE_USER                                        │
│     → 403, если нет                                             │
│                                                                 │
│  3. Controller / Service                                        │
│     denyUnlessGranted('order:delete', $order)                   │
│         │                                                       │
│         ▼                                                       │
│  4. Voter (OrderVoter)                                          │
│     supports? → voteOnAttribute()                               │
│         │                                                       │
│         ├─ GRANTED → доступ                                     │
│         ├─ DENIED  → 403                                        │
│         └─ ABSTAIN → следующий voter                            │
│                                                                 │
│  5. Все ABSTAIN → DENIED (deny by default)                      │
└─────────────────────────────────────────────────────────────────┘
```

**Роли** — «кто ты». **Voter** — «разрешено ли тебе». **Expression Language** — «разрешено ли тебе, и вот тут одно условие». **`access_control`** — «какие роли дают доступ к этой зоне».

Роли — для **зоны** (URI, API-эндпоинт). Voter — для **объекта и действия**. Если вы ловите себя на том, что пишете `if ($user->getDepartment() === $order->getDepartment())` — это voter, не роль.

> ⏱ 42.7s · prompt 32570 tok · output 5621 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

The user is asking about how to store Value Objects in Doctrine ORM when the domain requires immutability and always-valid state. This is a deep architectural question that touches on:

1. The fundamental tension: Value Objects are immutable domain concepts, but Doctrine works with mutable entity state and SQL tables
2. Mapping strategies: single column with custom Type, multiple columns, JSON/JSONB
3. Immutability enforcement: constructor validation, readonly, `__clone`, `__wakeup`
4. Always-valid state: invariants in constructor, factory methods
5. Doctrine-specific pitfalls: ChangeTracker, lazy loading, serialization, DQL, comparisons

Let me structure this as:
- The fundamental problem (why it's hard)
- Mapping strategies (with code)
  - Custom Doctrine Type (single column)
  - Multiple columns (embedded object)
  - JSONB column
- Immutability enforcement in the VO itself
- Always-valid state (invariants)
- Doctrine-specific pitfalls (ChangeTracker, DQL, hydration, serialization)
- Practical recommendations

I'll use concrete PHP examples with Symfony/Doctrine. Let me think about what makes each approach good/bad:

**Custom Type:**
- Pros: Clean, type-safe, single column, DQL-friendly
- Cons: One per VO type, need to handle comparison, can't easily query sub-fields
- Example: Money (amount + currency), Email, Money

**Multiple columns / Embedded:**
- Pros: Can query individual fields, indexes, constraints
- Cons: Verbose, need to keep VO and columns in sync, Doctrine manages mutability
- Example: Address (street, city, zip, country)

**JSONB:**
- Pros: Flexible, no schema changes for new fields, works well with Postgres
- Cons: No indexes on sub-fields (without GIN), no type safety at DB level, DQL is awkward
- Example: Complex settings, metadata

**The immutability problem with Doctrine:**
- Doctrine hydrates entities/VOs by setting properties, bypassing constructor
- ChangeTracker detects changes by comparing property values
- If VO is truly immutable (no setters), Doctrine still needs a way to hydrate
- Solution: `__wakeup`, reflection-based hydration, or custom Type that constructs via constructor

**Always-valid state:**
- Constructor throws if invalid
- No public setters
- `readonly` properties
- `final` class
- Factory methods for "create with modification" (returns new instance)

Let me write a comprehensive answer with code examples.

</details>

**Кандидат:**

# Value Object в Doctrine: неизменяемость vs. гидратация

## Фундаментальное противоречие

Value Object говорит: «создай меня валидным, и я никогда не изменюсь».
Doctrine говорит: «я создам объект через `new`, а потом поставлю свойства через `ReflectionProperty::setValue()`, потому что так удобнее».

Это столкновение двух миров. Каждый способ маппинга — это компромисс в этой точке. Разберём все три основных подхода и то, где каждый ломается.

---

## 1. Сами Value Object: неизменяемость и валидность

Независимо от способа хранения, VO должен быть «железным»:

```php
// src/Domain/Common/Money.php

final class Money
{
    private function __construct(
        public readonly int    $amountCents,
        public readonly string $currency,
    ) {}

    // Фабрика: единственный способ создать валидный объект
    public static function fromCents(int $cents, string $currency): self
    {
        if ($cents < 0) {
            throw new \DomainException("Amount must be non-negative, got {$cents}");
        }
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new \DomainException("Invalid currency code: {$currency}");
        }
        return new self($cents, $currency);
    }

    // «Мутация» = новый объект
    public function plus(self $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new \DomainException('Currency mismatch');
        }
        return self::fromCents($this->amountCents + $other->amountCents, $this->currency);
    }

    public function multiply(int $factor): self
    {
        if ($factor <= 0) {
            throw new \DomainException('Factor must be positive');
        }
        return self::fromCents($this->amountCents * $factor, $this->currency);
    }

    // Защита от клонирования/сериализации (опционально, но в prod полезно)
    public function __clone()
    {
        // Небольшой трюк: clone создаёт копию, но у нас всё readonly,
        // так что копия идентична. Запрещаем явное сообщение.
        throw new \BadMethodCallException('Money is immutable and cannot be cloned');
    }

    // Value equality
    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }
}
```

Ключевые решения:

| Решение | Зачем |
|---|---|
| `final class` | Нельзя наследовать и сломать инварианты |
| `private __construct` | Единственный вход — `fromCents()`, где проверяются инварианты |
| `readonly` | Даже внутри класса `$this->amountCents = 0` — ошибка |
| Нет `__set`, `__unset` | `__magic` не может объехать |
| `plus()` возвращает `new` | Не мутация, а создание нового |

---

## 2. Способ A: Кастомный Doctrine Type (одна колонка)

### Как работает

Регистрируете свой тип, который знает, как `VO ↔ строка/число/структура` в колонке.

```php
// src/Infrastructure/Persistence/Type/MoneyType.php

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * DB → PHP (hydration)
     * @return Money
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        // Храним как "1500:USD" или как JSON "1500|USD"
        // Формат: "{amount}:{currency}"
        $parts = explode(':', (string) $value);
        if (count($parts) !== 2) {
            throw new ConversionException(
                "Invalid Money format: {$value}",
                'value', $this->getName()
            );
        }

        return Money::fromCents((int) $parts[0], $parts[1]);
        // ^^^ Если amount < 0 или currency невалиден — Exception из конструктора.
        //     Гидратация ПАДАЕТ, а не создаёт «мёртвый» объект.
    }

    /**
     * PHP → DB (persistence)
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new ConversionException(
                'Expected Money instance',
                'value', $this->getName()
            );
        }

        return "{$value->amountCents}:{$value->currency}";
    }

    public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 20]);
    }

    public function getDatabaseTypeDeclaration(int $length, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 20});
    }
}
```

Регистрация:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            money: App\Infrastructure\Persistence\Type\MoneyType
```

Использование в сущности:

```php
// src/Domain/Order/Order.php

#[ORM\Entity]
final class Order
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'money', nullable: false)]
    private Money $total;

    #[ORM\Column(type: 'money', nullable: true)]
    private ?Money $discount = null;

    // …
}
```

### Плюсы

| Плюс | Деталь |
|---|---|
| Типобезопасность на уровне сущности | `$order->total` — это `Money`, не `string` |
| Инварианты при гидратации | `fromCents()` проверяет. Мусор в БД = явная ошибка, не «тихий 0» |
| Одна колонка | Миграции простые, DQL: `WHERE o.total IS NOT NULL` |
| Не зависит от JSON | Работает на любом SQL-движке |
| `readonly` в сущности | `#[ORM\Column(type: 'money')] private Money $total;` — readonly работает |

### Минусы и подводные камни

| Проблема | Деталь |
|---|---|
| **Нельзя искать по подполю** | `WHERE o.total.amountCents > 100` — нет. Нужно парсить строку или добавить отдельную колонку-индекс |
| **ChangeTracker не понимает VO** | Doctrine сравнивает `getTotal()` до/после. Если вы создали новый `Money` с теми же значениями — ChangeTracker **не увидит изменения** (value equality). Это фича, но надо знать |
| **`convertToPHPValue` — единственный вход** | Если в будущем `Money` получит `decimalPlaces` — формат строки ломается. Миграция данных + изменение Type |
| **Нет JSON-совместимости** | Если вы хотите логировать `$order->total` в JSON — нужен свой `JsonSerializable` |
| **DQL-сравнение** | `WHERE o.total = :money` — Doctrine передаст `Money` объект в Type, который конвертирует в строку. Работает, но неочевидно |

### Подводный камень #1: ChangeTracker и value equality

```php
// Сценарий:
$order->setTotal(Money::fromCents(1500, 'USD'));  // 15.00 USD
$order->setTotal(Money::fromCents(1500, 'USD'));  // «новый» объект, те же значения

// ChangeTracker сравнивает:
//   old = Money{1500, 'USD'}
//   new = Money{1500, 'USD'}
//   → идентичны по ===? НЕТ (разные объекты)
//   → идентичны по value? ДА
//
// Doctrine использует === для сравнения. Значит:
//   old !== new → считает, что ИЗМЕНИЛОСЬ → UPDATE в БД.
//
// Результат: лишний UPDATE. Не баг, но шум.
```

Решение: в `setTotal()` (или фабрике) сравнивать через `equals()`:

```php
public function setTotal(Money $total): void
{
    if ($this->total?->equals($total) === true) {
        return; // значение не изменилось — не трогаем
    }
    $this->total = $total;
}
```

Но `readonly` на свойстве сущности это запретит. Значит, для сущностей с VO-свойствами **не используйте `readonly`** — только для VO-классов.

### Подводный камень #2: `nullable` и `null`

```php
#[ORM\Column(type: 'money', nullable: true)]
private ?Money $discount = null;
```

При гидратации: `convertToPHPValue(null, $platform)` → `null`. ОК.
При записи: `convertToDatabaseValue(null, $platform)` → `null`. ОК.

Но если в БД стоит `''` (пустая строка) вместо `NULL` — `explode(':', '')` → `['']` → `count !== 2` → `ConversionException`. **Валидируйте данные при миграции.**

---

## 3. Способ B: Несколько колонок (embedded)

### Как работает

Разбиваете VO на колонки, как обычную таблицу.

```php
// src/Domain/Common/Money.php — тот же VO

// src/Domain/Order/Order.php

#[ORM\Entity]
final class Order
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;

    // Money → 2 колонки
    #[ORM\Column(type: 'integer', name: 'total_amount_cents')]
    private int $totalAmountCents;

    #[ORM\Column(type: 'string', length: 3, name: 'total_currency')]
    private string $totalCurrency;

    // Геттер, возвращающий VO
    public function getTotal(): Money
    {
        return Money::fromCents($this->totalAmountCents, $this->totalCurrency);
        // ^^^ Инварианты проверяются каждый раз при обращении.
        //     Если в БД мусор — исключение, не «тихий 0».
    }

    public function setTotal(Money $total): void
    {
        $this->totalAmountCents = $total->amountCents;
        $this->totalCurrency    = $total->currency;
    }
}
```

### Плюсы

| Плюс | Деталь |
|---|---|
| **Поиск по подполю** | `WHERE total_amount_cents > 1000 AND total_currency = 'USD'` — работает, индексируется |
| **DB-контрейнты** | `CHECK (total_amount_cents >= 0)`, `CHECK (total_currency ~ '^[A-Z]{3}$')` |
| **Индексы** | `INDEX (total_currency, total_amount_cents)` — compound index |
| **Прозрачность** | SQL-админ видит данные без парсинга |
| **Нет кастомного Type** | Меньше кода, меньше мест для багов |

### Минусы

| Минус | Деталь |
|---|---|
| **Дублирование логики** | `Money::fromCents()` в VO + ручной маппинг в сущности. Два места, где надо синхронизировать |
| **Гидратация создаёт «сырой» объект** | Doctrine ставит `int` и `string` в свойства. `Money` не существует до вызова `getTotal()`. Если кто-то поправит `$order->totalAmountCents = -5` напрямую — инварианты не сработают (пока не вызовут `getTotal()`) |
| **Нет `readonly`** | Свойства сущности не могут быть `readonly`, потому что Doctrine их ставит. А `Money`-геттер — не свойство, а вычисление |
| **Версии** | Если `Money` получит `decimalPlaces` — новая колонка, миграция, переписывание сущности. Кастомный Type — просто строка меняется |
| **Составные VO** | `Address` (5 полей) → 5 колонок + 5 маппингов. `Money` (2 поля) → ещё терпимо |

### Подводный камень: «мутация через обход»

```php
// Кто-то в сервисе:
$order->totalAmountCents = -999;  // PHP не ругается (нет readonly)
$order->totalCurrency    = 'XXX';

// Пока не вызвали getTotal() — «невалидный Money» существует в памяти.
// Если этот объект попадёт в JSON-ответ — будет -999 и XXX.

// Защита:
// 1. Не делать свойства public (private, только через геттер/сеттер).
// 2. В __toString / JsonSerializable — вызывать getTotal(), который валидирует.
// 3. В DQL — не читать raw-колонки, а читать через сущность.
```

Это **главная слабость** embedded-подхода: «сырые» поля существуют в памяти сущности и могут быть невалидными до того, как кто-то вызовет геттер.

---

## 4. Способ C: JSONB (PostgreSQL)

### Как работает

```php
#[ORM\Entity]
final class Order
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'json', nullable: false)]
    private array $total;  // ["amountCents": 1500, "currency": "USD"]

    // Геттер-фабрика
    public function getTotal(): Money
    {
        return Money::fromCents(
            (int) $this->total['amountCents'],
            (string) $this->total['currency'],
        );
    }

    public function setTotal(Money $money): void
    {
        $this->total = [
            'amountCents' => $money->amountCents,
            'currency'    => $money->currency,
        ];
    }
}
```

### Плюсы

| Плюс | Деталь |
|---|---|
| **Гибкость** | Добавили `decimalPlaces` в Money — просто новое поле в JSON. Без миграции |
| **Сложные VO** | `Address` (street, city, zip, country, geo: {lat, lng}) — одна колонка |
| **Postgres-функции** | `WHERE total->>'currency' = 'USD'` — работает |
| **GIN-индекс** | `CREATE INDEX ON orders USING GIN (total jsonb_path_ops)` — поиск по подполю |
| **Не надо писать Type** | `json` — встроенный тип Doctrine |

### Минусы

| Минус | Деталь |
|---|---|
| **Нет типа в БД** | `total` — это `jsonb`. Никто не проверит, что там именно `{amountCents: int, currency: string}`. Мусор не упадёт при вставке |
| **Нет CHECK-контрейнтов** | `CHECK (total->>'amountCents' >= 0)` — не работает (jsonb-выражения не в CHECK) |
| **Индексы** | GIN — медленнее, чем B-tree на колонке. Для точного совпадения — OK. Для range (`> 1000`) — GIN не помогает, нужен `expression index` |
| **DQL** | `WHERE o.total['currency'] = 'USD'` — работает в Doctrine 2.x, но в DQL это «магия», которая зависит от версии |
| **ChangeTracker** | Сравнивает массивы. `["a" => 1, "b" => 2]` и `["b" => 2, "a" => 1]` — **разные** для `===`. Лишний UPDATE |
| **Сериализация** | Если сущность попадёт в кэш/очередь — JSON → `["amountCents": …]`. При десериализации `int` может стать `string` (если JSON-парсер не строгий) |

### Подводный камень: ChangeTracker и порядок ключей

```php
// Сценарий:
$order->setTotal(Money::fromCents(1500, 'USD'));
// $this->total = ["amountCents" => 1500, "currency" => "USD"]

// Гидратация из БД:
// $this->total = ["currency" => "USD", "amountCents" => 1500]
// (Postgres jsonb не гарантирует порядок ключей)

// ChangeTracker:
//   old: ["currency" => "USD", "amountCents" => 1500]
//   new: ["amountCents" => 1500, "currency" => "USD"]
//   === ?  НЕТ (разный порядок)
//   → UPDATE. Лишний.
```

Решение: `ksort()` перед записью в `$this->total`:

```php
public function setTotal(Money $money): void
{
    $this->total = [
        'amountCents' => $money->amountCents,
        'currency'    => $money->currency,
    ];
    ksort($this->total);  // детерминированный порядок
}
```

И в `convertToPHPValue` (если используете кастомный Type поверх jsonb):

```php
$data = json_decode($value, true);
ksort($data);
return $data;
```

---

## 5. Сравнение

| Критерий | Кастомный Type | Embedded (колонки) | JSONB |
|---|---|---|---|
| Инварианты при гидратации | ✅ `fromCents()` бросит | ⚠️ Только при вызове геттера | ⚠️ Только при вызове геттера |
| Поиск по подполю | ❌ Парсить строку | ✅ Индексы, `WHERE` | ⚠️ GIN, expression index |
| Добавление поля в VO | Миграция + Type | Миграция + сущность | Просто новое поле |
| `readonly` в сущности | ⚠️ Не всегда (см. выше) | ❌ Нет (Doctrine ставит) | ⚠️ Нет (массив) |
| Сложные VO (5+ полей) | ⚠️ Формат строки усложняется | ❌ 5 колонок | ✅ Одна колонка |
| DB-контрейнты | ❌ | ✅ CHECK, FK | ❌ |
| ChangeTracker-шум | ⚠️ `===` на объектах | ✅ Простые скаляры | ⚠️ Порядок ключей |
| Тестируемость | ✅ Мок Type | ✅ Простые свойства | ✅ Простой массив |
| Производительность | ✅ Одна колонка | ✅ Индексы | ⚠️ GIN медленнее |
| Сложность внедрения | Средняя (Type + миграция) | Низкая (колонки + маппинг) | Минимальная (одна колонка) |

---

## 6. Подводные камни, общие для всех способов

### 6.1 Doctrine и `__clone` / `__wakeup`

```php
// Если VO имеет __clone() { throw … }
// А сущность — clone-able (Doctrine делает $this->clone() при merge)
// → Фатальная ошибка.

// Решение: не запрещать __clone в VO.
// Лучше: __clone() {} — пустой. Копия идентична, и это OK для immutable.
```

### 6.2 `EntityManager::getReference()` и lazy loading

```php
$ref = $em->getReference(Order::class, 'order-123');
$total = $ref->getTotal();  // ← ТРИГГЕРИРУЕТ lazy loading!
// → SELECT ... FROM orders WHERE id = 'order-123'
// → Гидратация → Money::fromCents() → инварианты проверяются
// → Если в БД мусор → Exception в момент обращения, не в момент запроса
```

Это **хорошо** (fail-fast), но **неочевидно**: ошибка возникает не в репозитории, а в бизнес-коде при чтении свойства.

### 6.3 DQL и кастомные типы

```php
// Работает:
$qb->andWhere('o.total IS NOT NULL')

// Работает (Doctrine конвертирует):
$qb->setParameter('money', Money::fromCents(1500, 'USD'))
$qb->andWhere('o.total = :money')

// НЕ работает:
$qb->andWhere('o.total.amountCents > 1000')  // ← DQL не знает про поля VO
```

Для поиска по подполю — embedded или JSONB.

### 6.4 Сериализация в очередях / кэше

```php
// Message в Redis:
$payload = serialize($order);  // или json_encode
// → Money: {"amountCents":1500,"currency":"USD"}
// → При десериализации: int может стать "1500" (string), если json не strict

// Защита: в Money — явный каст в fromCents()
// (int) $parts[0] — работает для "1500" и 1500
```

### 6.5 `readonly` на свойстве сущности vs. VO

```php
// НЕЛЬЗЯ:
#[ORM\Column(type: 'money')]
private readonly Money $total;  // ❌ Doctrine не может поставить значение через readonly

// МОЖНО:
#[ORM\Column(type: 'money')]
private Money $total;           // ✅

// Но в VO:
final class Money
{
    public readonly int $amountCents;  // ✅ readonly на уровне VO
}
```

`readonly` — на VO. Не на свойстве сущности. Иначе Doctrine не сможет гидратировать.

---

## 7. Практическая рекомендация

### Для простого VO (2–3 скаляра): `Money`, `Email`, `PhoneNumber`

**Кастомный Type.** Одна колонка. Инварианты при гидратации. Чисто.

```php
#[ORM\Column(type: 'money')]
private Money $total;
```

### Для составного VO (4+ полей, часть ищется): `Address`, `Period`

**Embedded.** Индексы, CHECK-контрейнты, поиск по `city`.

```php
#[ORM\Column(name: 'addr_street')]
private string $street;

#[ORM\Column(name: 'addr_city')]
private string $city;

public function getAddress(): Address
{
    return Address::create($this->street, $this->city, …);
}
```

### Для «гибкого» VO (меняется часто, не ищется): `Settings`, `Metadata`

**JSONB.** Без миграций. GIN-индекс если нужно.

```php
#[ORM\Column(type: 'json')]
private array $settings;

public function getSettings(): Settings
{
    return Settings::fromArray($this->settings);
}
```

### Универсальное правило

> **Геттер сущности всегда возвращает VO через фабрику, которая проверяет инварианты.**
>
> Неважно, откуда данные — одна колонка, пять колонок, JSONB.
> `getTotal()` → `Money::fromCents(...)` → если мусор → Exception.
> Мусор в БД не создаёт «мёртвый» объект в памяти.

Это единственная гарантия, что **валидный статус** VO сохраняется от БД до домена, независимо от способа хранения.

---

## 8. Что я бы НЕ делал

| Антипаттерн | Почему |
|---|---|
| `public Money $total` без валидации в геттере | Мусор в БД → мусор в памяти → мусор в ответе |
| `convertToPHPValue` возвращает `new Money($cents, $currency)` без проверки | Обходите фабрику. Инварианты не проверяются |
| JSONB + `array` + доступ к `['amountCents']` в 15 местах | VO не защищён. Каждый читает «сырой массив» |
| Embedded + `public` свойства + прямой доступ `$order->totalAmountCents` | Обходите VO. Инварианты не проверяются |
| Кастомный Type с форматом `"1500:USD:2"` (3 части) без версионирования | Добавите 4-ю часть — все старые данные сломаны |

> ⏱ 52.2s · prompt 37975 tok · output 6493 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о хранении доменного агрегата (корень + дочерние сущности) через Doctrine. Нужно разобрать: маппинг коллекций, границы транзакций, кто и когда вызывает flush. Разобрать на конкретном примере.

Это продолжение серии вопросов о DDD + Symfony + Doctrine. Пользователь явно работает в этом стеке и хочет глубокое понимание.

Выберу пример: **Order** (корень агрегата) + **OrderLine** (дочерние сущности) + **Payment** (может быть частью агрегата или отдельным). Это классический пример из DDD.

Структура ответа:
1. Пример доменного агрегата (Order + OrderLine)
2. Маппинг коллекций через Doctrine (OneToOne vs ManyToOne, Owned vs Shared)
3. Границы транзакций — кто открывает, кто коммитит
4. Кто вызывает flush — репозиторий, сервис, контроллер?
5. Подводные камни: каскад, orphanRemoval, change tracking, N+1

Буду конкретным, с кодом. Показать правильно и неправильно.

</details>

**Кандидат:**

# Агрегат Order + OrderLine: хранение через Doctrine

## 1. Доменная модель

Агрегат `Order` — корень. `OrderLine` — дочерние сущности, которые **не имеют самостоятельной жизни вне агрегата**. У `OrderLine` нет своего ID в домене (есть технический PK в БД, но бизнес оперирует агрегатом целиком).

```php
// src/Domain/Order/Order.php

final class Order
{
    private string $id;
    private string $customerId;
    private OrderStatus $status;
    private Money $total;
    private Money $discount;
    private string $createdAt;
    /** @var OrderLine[] */
    private array $lines = [];

    public function __construct(string $customerId)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->customerId = $customerId;
        $this->status = OrderStatus::Draft;
        $this->total = Money::fromCents(0, 'USD');
        $this->discount = Money::fromCents(0, 'USD');
        $this->createdAt = \date('c');
    }

    // ── Дочерние: управление через корень ─────────────────────

    public function addLine(ProductId $productId, Money $unitPrice, int $quantity): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotEditableException('Cannot add lines to a non-draft order');
        }
        if ($quantity <= 0) {
            throw new InvalidQuantityException($quantity);
        }

        $line = new OrderLine($productId, $unitPrice, $quantity);
        $this->lines[] = $line;
        $this->recalculateTotal();
    }

    public function removeLine(ProductId $productId): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotEditableException('Cannot remove lines from a non-draft order');
        }

        $this->lines = array_filter(
            $this->lines,
            fn(OrderLine $l) => !$l->productId->equals($productId),
        );
        $this->lines = array_values($this->lines);  // перенумеровываем
        $this->recalculateTotal();
    }

    public function updateLineQuantity(ProductId $productId, int $quantity): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotEditableException('Cannot modify lines on a non-draft order');
        }

        $found = false;
        foreach ($this->lines as $i => $line) {
            if ($line->productId->equals($productId)) {
                $this->lines[$i] = $line->withQuantity($quantity);  // новый объект
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new LineNotFoundForProductException($productId);
        }
        $this->recalculateTotal();
    }

    // ── Состояние ──────────────────────────────────────────────

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidStateTransitionException($this->status, OrderStatus::Confirmed);
        }
        if (count($this->lines) === 0) {
            throw new EmptyOrderException($this->id);
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function markShipped(string $trackingNumber): void
    {
        if ($this->status !== OrderStatus::Confirmed) {
            throw new InvalidStateTransitionException($this->status, OrderStatus::Shipped);
        }
        $this->status = OrderStatus::Shipped;
    }

    // ── Вычисления ─────────────────────────────────────────────

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->lineTotalCents();
        }
        $this->total = Money::fromCents($sum, 'USD');
    }

    public function totalCents(): int { return $this->total->amountCents; }
    public function id(): string { return $this->id; }
    public function customerId(): string { return $this->customerId; }
    public function status(): OrderStatus { return $this->status; }
    public function createdAt(): string { return $this->createdAt; }
    public function discountCents(): int { return $this->discount->amountCents; }

    /** @return OrderLine[] */
    public function lines(): array { return $this->lines; }
}
```

```php
// src/Domain/Order/OrderLine.php

final class OrderLine
{
    public function __construct(
        private ProductId $productId,
        private Money     $unitPrice,
        private int       $quantity,
    ) {}

    public function withQuantity(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new InvalidQuantityException($quantity);
        }
        return new self($this->productId, $this->unitPrice, $quantity);
    }

    public function productId(): ProductId { return $this->productId; }
    public function unitPrice(): Money { return $this->unitPrice; }
    public function quantity(): int { return $this->quantity; }
    public function lineTotalCents(): int { return $this->unitPrice->amountCents * $this->quantity; }
}
```

```php
// src/Domain/Order/OrderStatus.php

enum OrderStatus
{
    case Draft;
    case Confirmed;
    case Shipped;
    case Delivered;
    case Cancelled;
}
```

```php
// src/Domain/Order/ProductId.php

final class ProductId
{
    private function __construct(public readonly string $value) {}

    public static function of(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

**Ключевые доменные решения:**

| Решение | Зачем |
|---|---|
| `OrderLine` — не entity с `@Id` в домене | У неё нет самостоятельной жизни. Удаление — через `$order->removeLine()` |
| Мутации — только через корень | `addLine`, `removeLine`, `updateLineQuantity`. Никто не может «подправить» `OrderLine` напрямую |
| `recalculateTotal()` — приватный, в корне | Инвариант «total = sum(lines)» гарантируется агрегатом |
| Состояние — enum, переходы — методы корня | `confirm()`, `markShipped()` проверяют переход |

---

## 2. Маппинг через Doctrine

### Таблицы

```sql
CREATE TABLE orders (
    id          VARCHAR(36) PRIMARY KEY,
    customer_id VARCHAR(36) NOT NULL,
    status      VARCHAR(20) NOT NULL,
    total_cents INT NOT NULL,
    total_currency CHAR(3) NOT NULL,
    discount_cents INT NOT NULL DEFAULT 0,
    discount_currency CHAR(3) NOT NULL DEFAULT 'USD',
    created_at  TIMESTAMPTZ NOT NULL
);

CREATE TABLE order_lines (
    id          BIGSERIAL PRIMARY KEY,       -- технический PK, не доменный
    order_id    VARCHAR(36) NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id  VARCHAR(36) NOT NULL,
    unit_cents  INT NOT NULL,
    unit_currency CHAR(3) NOT NULL,
    quantity    INT NOT NULL,
    UNIQUE (order_id, product_id)           -- один продукт на заказ — одна строка
);

CREATE INDEX idx_order_lines_order ON order_lines (order_id);
```

**Заметка:** `order_lines.id` — технический, для Doctrine. В домене `OrderLine` не имеет ID. Это «двойное представление»: домен видит `OrderLine[]`, БД видит `order_lines` с PK.

### Сущность-запись (ORM-слой)

Разделяем **доменную сущность** и **запись в БД**. Домен — `Order` и `OrderLine` (чистый PHP, без `#[ORM]`). ORM — `OrderRecord` и `OrderLineRecord` (аннотации, маппинг).

```php
// src/Infrastructure/Persistence/ORM/OrderRecord.php

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\Index(columns: ['customer_id'])]
final class OrderRecord
{
    #[ORM\Id, ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $customerId;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'integer')]
    private int $totalCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $totalCurrency;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $discountCents;

    #[ORM\Column(type: 'string', length: 3, options: ['default' => 'USD'])]
    private string $discountCurrency;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity: OrderLineRecord::class,
     *     mappedBy: "order",
     *     cascade: {"persist", "remove"},
     *     orphanRemoval: true,
     *     fetch: "EAGER"
     * )
     */
    #[ORM\OneToMany(
        targetEntity: OrderLineRecord::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
        fetch: 'EAGER',
    )]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    // ── Геттеры / сеттеры (только для репозитория) ────────────
    public function getId(): string { return $this->id; }
    public function setId(string $id): void { $this->id = $id; }
    public function getCustomerId(): string { return $this->customerId; }
    public function setCustomerId(string $v): void { $this->customerId = $v; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): void { $this->status = $v; }
    public function getTotalCents(): int { return $this->totalCents; }
    public function setTotalCents(int $v): void { $this->totalCents = $v; }
    public function getTotalCurrency(): string { return $this->totalCurrency; }
    public function setTotalCurrency(string $v): void { $this->totalCurrency = $v; }
    public function getDiscountCents(): int { return $this->discountCents; }
    public function setDiscountCents(int $v): void { $this->discountCents = $v; }
    public function getDiscountCurrency(): string { return $this->discountCurrency; }
    public function setDiscountCurrency(string $v): void { $this->discountCurrency = $v; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $v): void { $this->createdAt = $v; }
    public function getLines(): Collection { return $this->lines; }
    public function addLine(OrderLineRecord $line): void { $this->lines->add($line); }
    public function removeLine(OrderLineRecord $line): void { $this->lines->removeElement($line); }
    public function clearLines(): void { $this->lines->clear(); }
}
```

```php
// src/Infrastructure/Persistence/ORM/OrderLineRecord.php

#[ORM\Entity]
#[ORM\Table(name: 'order_lines', uniqueConstraints: [
    new ORM\UniqueConstraint(columns: ['order_id', 'product_id']),
])]
final class OrderLineRecord
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: OrderRecord::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private OrderRecord $order;

    #[ORM\Column(type: 'string', length: 36)]
    private string $productId;

    #[ORM\Column(type: 'integer')]
    private int $unitCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $unitCurrency;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function getOrder(): OrderRecord { return $this->order; }
    public function setOrder(OrderRecord $order): void { $this->order = $order; }
    public function getProductId(): string { return $this->productId; }
    public function setProductId(string $v): void { $this->productId = $v; }
    public function getUnitCents(): int { return $this->unitCents; }
    public function setUnitCents(int $v): void { $this->unitCents = $v; }
    public function getUnitCurrency(): string { return $this->unitCurrency; }
    public function setUnitCurrency(string $v): void { $this->unitCurrency = $v; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $v): void { $this->quantity = $v; }
}
```

### Маппер: домен ↔ запись

```php
// src/Infrastructure/Persistence/ORM/OrderMapper.php

final class OrderMapper
{
    public function toRecord(Order $order): OrderRecord
    {
        $record = new OrderRecord();
        $record->setId($order->id());
        $record->setCustomerId($order->customerId());
        $record->setStatus($order->status()->name);
        $record->setTotalCents($order->totalCents());
        $record->setTotalCurrency('USD');
        $record->setDiscountCents($order->discountCents());
        $record->setDiscountCurrency('USD');
        $record->setCreatedAt(new \DateTimeImmutable($order->createdAt()));

        foreach ($order->lines() as $line) {
            $lineRecord = new OrderLineRecord();
            $lineRecord->setOrder($record);
            $lineRecord->setProductId($line->productId()->value);
            $lineRecord->setUnitCents($line->unitPrice()->amountCents);
            $lineRecord->setUnitCurrency($line->unitPrice()->currency);
            $lineRecord->setQuantity($line->quantity());
            $record->addLine($lineRecord);
        }

        return $record;
    }

    public function fromRecord(OrderRecord $record): Order
    {
        // Восстановляем доменный агрегат
        $order = new Order($record->getCustomerId());
        // ... восстанавливаем id, status, total, lines через private setter'ы
        // или через статическую фабрику Order::reconstitute(...)

        // Альтернатива: Order::reconstitute()
        return Order::reconstitute(
            $record->getId(),
            $record->getCustomerId(),
            OrderStatus::from($record->getStatus()),
            Money::fromCents($record->getTotalCents(), $record->getTotalCurrency()),
            Money::fromCents($record->getDiscountCents(), $record->getDiscountCurrency()),
            $record->getCreatedAt()->format('c'),
            \array_map(fn(OrderLineRecord $lr) =>
                new OrderLine(
                    ProductId::of($lr->getProductId()),
                    Money::fromCents($lr->getUnitCents(), $lr->getUnitCurrency()),
                    $lr->getQuantity(),
                ),
            $record->getLines()->toArray(),
        );
    }
}
```

```php
// Добавляем в Order:
public static function reconstitute(
    string $id,
    string $customerId,
    OrderStatus $status,
    Money $total,
    Money $discount,
    string $createdAt,
    array $lines,
): self {
    $order = new self($customerId);
    $order->id = $id;
    $order->status = $status;
    $order->total = $total;
    $order->discount = $discount;
    $order->createdAt = $createdAt;
    $order->lines = $lines;
    return $order;
}
```

### Репозиторий

```php
// src/Domain/Order/OrderRepository.php (доменный интерфейс)

interface OrderRepository
{
    public function save(Order $order): void;
    public function findById(string $id): ?Order;
    public function findByCustomer(string $customerId): array;
}
```

```php
// src/Infrastructure/Persistence/ORM/OrderRepository.php

final class OrderRepository implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderMapper            $mapper,
    ) {}

    public function save(Order $order): void
    {
        $record = $this->mapper->toRecord($order);
        $this->em->persist($record);
        // ФЛUSH НЕ ТУТ (разберём ниже)
    }

    public function findById(string $id): ?Order
    {
        $record = $this->em->getRepository(OrderRecord::class)->find($id);
        return $record !== null ? $this->mapper->fromRecord($record) : null;
    }

    public function findByCustomer(string $customerId): array
    {
        $records = $this->em->getRepository(OrderRecord::class)
            ->createQueryBuilder('o')
            ->andWhere('o.customerId = :cid')
            ->setParameter('cid', $customerId)
            ->getQuery()
            ->getResult();

        return \array_map(fn(OrderRecord $r) => $this->mapper->fromRecord($r), $records);
    }
}
```

**Важно:** репозиторий **не вызывает `flush()`**. Он только `persist()`. Это разберём в разделе про транзакции.

---

## 3. Границы транзакций: кто открывает, кто коммитит

### Принцип

> **Транзакцию открывает и коммитит сервис приложения (application service), а не репозиторий и не контроллер.**

Почему:

| Кто | Почему не он |
|---|---|
| Контроллер | Знает про HTTP, не про БД. Должен быть тонким. |
| Репозиторий | Один репозиторий = одна операция. Агрегат может писать в 2 таблицы (`orders` + `order_lines`). Транзакция должна обхватить **обе**. Репозиторий видит одну. |
| Домен | Не знает про БД. Не знает про `EntityManager`. |
| **Сервис** | Знает «что происходит»: загрузка агрегата, мутация, сохранение. Может объединить в одну транзакцию. |

### Сервис

```php
// src/Application/Service/OrderService.php

final class OrderService
{
    public function __construct(
        private OrderRepository      $orders,
        private EntityManagerInterface $em,
    ) {}

    // ── Создать заказ ──────────────────────────────────────────
    public function create(string $customerId, array $items): Order
    {
        $order = new Order($customerId);
        foreach ($items as $item) {
            $order->addLine(
                ProductId::of($item['productId']),
                Money::fromCents($item['priceCents'], 'USD'),
                $item['quantity'],
            );
        }

        // Одна транзакция: orders + order_lines
        $this->em->wrapInTransaction(function () use ($order): void {
            $this->orders->save($order);
            $this->em->flush();
        });

        return $order;
    }

    // ── Отправить (confirm + ship) ─────────────────────────────
    public function confirm(string $orderId, User $user): Order
    {
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        // Проверка прав (voter)
        // $this->denyUnlessGranted('order:confirm', $order);

        $order->confirm();

        $this->em->wrapInTransaction(function () use ($order): void {
            $this->orders->save($order);
            $this->em->flush();
        });

        return $order;
    }

    // ── Добавить позицию ───────────────────────────────────────
    public function addLine(string $orderId, string $productId, int $priceCents, int $qty): Order
    {
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        $order->addLine(ProductId::of($productId), Money::fromCents($priceCents, 'USD'), $qty);

        $this->em->wrapInTransaction(function () use ($order): void {
            $this->orders->save($order);
            $this->em->flush();
        });

        return $order;
    }
}
```

### Почему `wrapInTransaction`

```php
$this->em->wrapInTransaction(function () use ($order): void {
    $this->orders->save($order);  // persist: orders + order_lines
    $this->em->flush();           // SQL: INSERT/UPDATE
});
```

`wrapInTransaction` делает:
1. `BEGIN`
2. Выполняет closure.
3. Если closure бросил исключение → `ROLLBACK`.
4. Если closure завершил успешно → `COMMIT`.
5. Возвращает результат closure (если нужно).

**Без него:**

```php
$this->em->beginTransaction();
try {
    $this->orders->save($order);
    $this->em->flush();
    $this->em->commit();
} catch (\Throwable $e) {
    $this->em->rollBack();
    throw $e;
}
```

То же самое, но `wrapInTransaction` короче и не пропускает `commit`/`rollback` при неочевидных путях выхода.

### Граница транзакции = граница агрегата

```
┌──────────────────────────────────────────────────┐
│  Транзакция                                      │
│                                                  │
│  1. SELECT orders, order_lines WHERE id = ?      │
│  2. Мутация: $order->addLine(...)                │
│     → инварианты проверяются (status, quantity)  │
│  3. UPDATE orders SET total_cents = ?            │
│  4. INSERT INTO order_lines VALUES (?)           │
│  5. COMMIT                                       │
│                                                  │
│  Если шаг 3 успешен, а шаг 4 упал → ROLLBACK     │
│  → orders не изменён, order_lines не вставлен    │
│  → Агрегат цел.                                  │
└──────────────────────────────────────────────────┘
```

Агрегат — **минимальная единица целостности**. Транзакция — **минимальная единица атомарности**. Они совпадают: одна транзакция = один агрегат.

Если нужно изменить **два агрегата** (Order + Inventory) — это уже **distributed transaction** (Saga, outbox pattern), а не одна БД-транзакция.

---

## 4. Кто и когда вызывает `flush()`

### Правило

| Кто | Flush? | Почему |
|---|---|---|
| Домен (`Order`) | ❌ | Не знает про `EntityManager` |
| Репозиторий | ❌ | `persist()` ≠ `flush()`. Репозиторий регистрирует, не применяет |
| **Сервис** | ✅ | Сервис знает «когда всё готово». Вызывает `flush()` после всех `persist()` |
| Контроллер | ❌ | Тонкий, делегирует сервису |
| Worker (Messenger) | ✅ | В `__invoke` — то же, что сервис |

### Почему не в репозитории

```php
// ПЛОХО:
public function save(Order $order): void
{
    $record = $this->mapper->toRecord($order);
    $this->em->persist($record);
    $this->em->flush();  // ← ФЛЮШ В РЕПОЗИТОРИИ
}
```

Проблема:

```php
// Сервис:
$order = $this->orders->findById($id);
$order->addLine(...);
$this->orders->save($order);   // flush уже вызван здесь

$inventory = $this->inventory->find(...);
$inventory->reserve(...);
$this->inventory->save($inventory);  // flush здесь — НОВАЯ транзакция

// Если reserve() упало → addLine() уже в БД. Несохранность.
```

Репозиторий **не знает**, что после него будет ещё одна операция. Только сервис знает полную картину.

### Почему не в контроллере

```php
// ПЛОХО:
public function addLine(Order $order, …): Response
{
    $this->orderService->addLine(...);
    $this->em->flush();  // ← Контроллер знает про EntityManager
    return $this->json([...]);
}
```

Контроллер не должен знать, что для сохранения нужен `flush()`. Это деталь персистентности.

### Правильный поток

```
Контроллер
  → $this->orderService->addLine($id, $productId, $price, $qty)
      → $order = $this->orders->findById($id)
      → $order->addLine(...)
      → $this->em->wrapInTransaction(fn() => {
            $this->orders->save($order);   // persist (регистрация)
            $this->em->flush();            // SQL (применение)
        });
  → Response(200)
```

---

## 5. Подводные камни

### 5.1 `cascade: persist` и `orphanRemoval`

```php
#[ORM\OneToMany(
    cascade: ['persist', 'remove'],
    orphanRemoval: true,
)]
```

| Флаг | Что делает |
|---|---|
| `cascade: persist` | `$em->persist($orderRecord)` → автоматически `persist()` для всех `OrderLineRecord` |
| `cascade: remove` | `$em->remove($orderRecord)` → автоматически `remove()` для всех `OrderLineRecord` |
| `orphanRemoval: true` | Если `OrderLineRecord` убран из `$orderRecord->getLines()` → автоматически `remove()` |

**Подводный камень:** `orphanRemoval` + `cascade: persist` вместе — если вы **замените** коллекцию:

```php
$record->clearLines();  // orphanRemoval → DELETE FROM order_lines WHERE order_id = ?
$record->addLine($new); // cascade:persist → INSERT INTO order_lines ...
```

Это «удали всё и вставь заново». Работает, но **дорого** для больших коллекций. Для `OrderLine` (обычно 1–20 строк) — нормально. Для `Document` с 1000 параграфов — нет.

### 5.2 ChangeTracker и замена коллекций

```php
// Сценарий: removeLine()
// Домен: $this->lines = array_filter(...)  → новый массив

// ORM-маппер:
$record->clearLines();   // orphanRemoval: DELETE
foreach ($lines as $l) {
    $record->addLine($lineRecord);  // cascade:persist: INSERT
}
```

Каждый `removeLine()` → `DELETE` + `INSERT`. Не `UPDATE`. Для 3 позиций — ок. Для 500 — нет.

Оптимизация: вместо «clear + re-add» — точечное удаление:

```php
// В маппере:
foreach ($record->getLines() as $existing) {
    if (!in_array($existing->getProductId(), $currentProductIds, true)) {
        $record->removeLine($existing);  // orphanRemoval: DELETE одной строки
    }
}
foreach ($currentLines as $line) {
    if (!isset($existingByProduct[$line->productId()])) {
        $record->addLine($this->toLineRecord($line));  // INSERT одной строки
    }
}
// Обновить quantity для оставшихся:
foreach ($currentLines as $line) {
    $existing = $existingByProduct[$line->productId()] ?? null;
    if ($existing && $existing->getQuantity() !== $line->quantity()) {
        $existing->setQuantity($line->quantity());  // UPDATE
    }
}
```

Это diff-подход. Для 3 позиций — избыточен. Для 500 — экономит.

### 5.3 `fetch: EAGER` vs `EAGER` + N+1

```php
fetch: 'EAGER'
```

При `findById()`:
```sql
SELECT o.* FROM orders o WHERE o.id = ?
SELECT l.* FROM order_lines l WHERE l.order_id = ?
```

Два запроса, но **один раз**. Не N+1, потому что `OrderLine` — дочерний, не отдельный корень.

N+1 возникнет при:
```php
$orders = $repo->findByCustomer($customerId);  // N заказов
foreach ($orders as $order) {
    $order->lines();  // EAGER → 1 запрос на заказ → N+1
}
```

Решение: `DQL` с `JOIN FETCH`:

```php
$records = $em->getRepository(OrderRecord::class)
    ->createQueryBuilder('o')
    ->join('o.lines', 'l', 'WITH', '1=1')   // FETCH JOIN
    ->andWhere('o.customerId = :cid')
    ->setParameter('cid', $customerId)
    ->getQuery()
    ->getResult();
```

```sql
SELECT o.*, l.*
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
WHERE o.customer_id = ?
```

Один запрос. Все линии загружены.

### 5.4 `flush()` и `clear()`

```php
// После flush() сущности остаются в EntityManager (managed state)
// Если следующий запрос в том же request — Doctrine вернёт cached-экземпляр.

// Опасность:
$order = $repo->findById('123');  // managed
$order->addLine(...);
$repo->save($order);             // persist (уже managed)
$em->flush();                    // SQL

// Другой сервис в том же request:
$order2 = $repo->findById('123');  // ТОТ ЖЕ ОБЪЕКТ (identity map)
// $order2 !== $order, но $order2->lines() — те же данные, что и $order
```

Если вы хотите «чистое» состояние — `$em->clear()`. Но это **дорого** и ломает identity map. Используйте только между независимыми операциями в одном request (редко).

### 5.5 `Product` как отдельная сущность

В примере `OrderLine` хранит `productId` (string) и `unitPrice`. `Product` — **отдельный агрегат**, не часть `Order`.

```
Order (агрегат)
├── OrderLine (дочерний, без ID)
└── customerId (reference, не aggregate composition)

Product (отдельный агрегат)
├── productId (ID)
├── name, description, ...
```

`OrderLine` хранит **копию** цены на момент заказа (`unitPrice`). Если цена `Product` изменится через 5 минут — `OrderLine` не изменится. Это **осознанное решение**: заказ фиксирует цену на момент покупки.

Не путать с:
- `OrderLine → Product` (ManyToOne, `lazy: true`) — ссылка, не копия. Цена «живая».
- `OrderLine` хранит `unitPrice` — копия. Цена «замороженная».

Для заказа — копия. Для «корзины» (Cart) — ссылка.

---

## 6. Итоговая схема

```
┌─────────────────────────────────────────────────────────────────────┐
│  КОНТРОЛЛЕР (тонкий)                                               │
│  POST /orders/{id}/lines                                           │
│    → $this->orderService->addLine($id, $pid, $price, $qty)         │
│    → Response(200)                                                 │
├─────────────────────────────────────────────────────────────────────┤
│  СЕРВИС (граница транзакции)                                       │
│                                                                    │
│  1. $order = $this->orders->findById($id)   ← SELECT (2 запроса)   │
│  2. $order->addLine(...)                   ← домен, инварианты     │
│  3. $this->em->wrapInTransaction(function() {                     │
│       $this->orders->save($order);        ← persist (регистрация)  │
│       $this->em->flush();               ← SQL: UPDATE + INSERT     │
│     })                                                            │
│  4. return $order                                                 │
├─────────────────────────────────────────────────────────────────────┤
│  РЕПОЗИТОРИЙ (без flush)                                           │
│  save(): $em->persist($record)                                     │
│  findById(): $em->getRepository(...)->find($id)                    │
├─────────────────────────────────────────────────────────────────────┤
│  ДОМЕН (без EntityManager)                                         │
│  Order::addLine() → проверяет status, quantity, пересчитывает total│
│  OrderLine — не имеет ID, управляется корнем                       │
├─────────────────────────────────────────────────────────────────────┤
│  БД                                                                │
│  orders: 1 строка                                                  │
│  order_lines: N строк (ON DELETE CASCADE)                          │
└─────────────────────────────────────────────────────────────────────┘
```

**Три правила:**

1. **Транзакция = агрегат.** Одна транзакция обхватывает все таблицы, которые принадлежит один агрегат.
2. **`flush()` в сервисе, не в репозитории.** Репозиторий регистрирует (`persist`), сервис применяет (`flush`).
3. **Домен не знает про БД.** `Order` не знает про `OrderRecord`. `OrderLine` не знает про `order_lines`. Маппер — единственный мост.

> ⏱ 58.5s · prompt 44003 tok · output 8419 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про диагностику и решение N+1 в Doctrine в Symfony. Это продолжение длинного технического диалога о PHP/Symfony/Doctrine/DDD. Нужно:

1. Диагностика — конкретные инструменты и методы
2. Способы решения — с trade-offs
3. Примеры кода
4. Практическая структура

Давай структурирую:

**Диагностика:**
- Doctrine Debug Bundle (dev)
- XHProf / Blackfire
- Logger (Doctrine Logger)
- EXPLAIN ANALYZE
- Chrome DevTools / Waterfall
- `doctrine:query:schema` — нет, это другое
- `doctrine:fix-schema` — нет
- SQL-логирование
- `EntityManager::getConfiguration()->setSQLLogger(...)`
- Symfony Profiler → Doctrine tab
- `var_dump($em->getStatistics())` — нет такого
- `doctrine:cache:clear-query` — нет
- `bin/console doctrine:query:schema` — нет
- `bin/console doctrine:mapping:info` — показывает маппинг, не запросы
- `bin/console doctrine:schema:validate` — валидация схемы
- `bin/console doctrine:query:sql` — нет такого
- `bin/console doctrine:fixtures:load` — нет
- `bin/console doctrine:query:sql` — нет
- `bin/console doctrine:query:sql` — нет

Правильные инструменты:
1. Symfony Profiler (Doctrine tab) — показывает все запросы, их время, количество
2. Doctrine Logger — `Doctrine\DBAL\Logging\Middleware` (4.x) или `SQLLogger` (2.x)
3. Blackfire — production profiling
4. XHProf — PHP-level
5. `EXPLAIN (ANALYZE, BUFFERS)` — Postgres
6. `doctrine:query:schema` — нет
7. `EntityManager::wrapInTransaction` + count
8. `bin/console debug:router` — нет
9. `bin/console debug:container` — нет
10. `bin/console doctrine:mapping:info` — показывает маппинг, не запросы

Для Symfony:
- `app_dev` → `/_profiler/{token}/?panel=doctrine` — показывает все SQL-запросы
- `app_dev` → `/_profiler/{token}/?panel=doctrine` — количество запросов, время каждого
- `app_dev` → `/_profiler/{token}/?panel=doctrine` — видно N+1 по повторяющимся паттернам SQL

Для production:
- Blackfire
- `Doctrine\DBAL\Logging\Middleware` с PSR-3 logger
- `Sentry` — логирование медленных запросов

**Способы решения:**

1. **JOIN FETCH** (DQL)
   - `SELECT o, l FROM Order o JOIN FETCH o.lines WHERE o.id = :id`
   - Один запрос вместо N+1
   - Trade-off: память (все линии в памяти), Cartesian product при нескольких JOIN'ах

2. **Partial Object** (DQL)
   - `SELECT o.id, o.customerId FROM Order o WHERE ...`
   - Загружаем только нужные поля
   - Trade-off: при обращении к незагруженному полю — additional query (lazy loading)

3. **QuerySet / setMaxResults + setFirstResult** (пагинация)
   - Не решает N+1 напрямую, но уменьшает N
   - Trade-off: пагинация — UX-решение, не техническое

4. **ChangeSet / UnitOfWork** — нет, это другое

5. **`@ORM\ManyToMany` vs `@ORM\OneToMany` + junction table**
   - Архитектурное решение
   - Trade-off: нормализация vs деноминализация

6. **`@ORM\CustomIdGenerator`** — нет

7. **`EntityManager::getReference()`** (proxy)
   - Не создаёт запрос, создаёт lazy proxy
   - Trade-off: при обращении к свойству — запрос. Не решает N+1, но откладывает

8. **`EntityManager::clear()`**
   - Очищает identity map
   - Trade-off: ломает identity map, повторные запросы

9. **`EntityManager::detach()`**
   - Открепляет сущность
   - Trade-off: изменения не отслеживаются

10. **`@ORM\OrderBy`** — нет

11. **`@ORM\Where`** — нет

12. **`@ORM\BasicEntityValueMapping`** — нет

13. **`@ORM\Embedded`** (embedded objects)
    - Встраивает VO в сущность
    - Не решает N+1

14. **`@ORM\JoinColumns` + `@ORM\ManyToOne`** — нет

15. **`@ORM\JoinColumn` + `onDelete`** — нет

16. **`@ORM\Index`** — помогает, но не решает N+1

17. **`@ORM\Index` + `@ORM\UniqueConstraint`** — нет

18. **`@ORM\ChangeTrackingPolicy`** — нет

19. **`@ORM\OptimisticLocking`** — нет

20. **`@ORM\Version`** — нет

21. **`@ORM\DiscriminatorColumn`** — нет

22. **`@ORM\InheritanceType`** — нет

23. **`@ORM\Table` + `@ORM\Index`** — нет

24. **`@ORM\Index` + `@ORM\UniqueConstraint`** — нет

25. **`@ORM\Index` + `@ORM\UniqueConstraint`** — нет

Ок, я зациклился на аннотациях. Вернёмся к реальным способам решения N+1:

**Реальные способы:**

1. **JOIN FETCH** (DQL)
   - `SELECT o, l FROM Order o JOIN FETCH o.lines WHERE o.id = :id`
   - Один запрос вместо N+1
   - Trade-off: память (все линии в памяти), Cartesian product при нескольких JOIN'ах

2. **Partial Object** (DQL)
   - `SELECT o.id, o.customerId FROM Order o WHERE ...`
   - Загружаем только нужные поля
   - Trade-off: при обращении к незагруженному полю — additional query (lazy loading)

3. **`@ORM\ManyToMany` vs `@ORM\OneToMany` + junction table**
   - Архитектурное решение
   - Trade-off: нормализация vs деноминализация

4. **`EntityManager::getReference()`** (proxy)
   - Не создаёт запрос, создаёт lazy proxy
   - Trade-off: при обращении к свойству — запрос. Не решает N+1, но откладывает

5. **`EntityManager::clear()`**
   - Очищает identity map
   - Trade-off: ломает identity map, повторные запросы

6. **`EntityManager::detach()`**
   - Открепляет сущность
   - Trade-off: изменения не отслеживаются

7. **`@ORM\Index`**
   - Ускоряет запрос, но не устраняет N+1
   - Trade-off: замедляет writes

8. **`@ORM\Index` + `@ORM\UniqueConstraint`**
   - Нет

9. **`@ORM\Index` + `@ORM\UniqueConstraint`**
   - Нет

10. **`@ORM\Index` + `@ORM\UniqueConstraint`**
    - Нет

Ок, я опять зациклился. Давай структурирую ответ нормально:

**Диагностика:**
1. Symfony Profiler (Doctrine tab)
2. Doctrine Logger (PSR-3)
3. Blackfire (production)
4. XHProf
5. EXPLAIN ANALYZE
6. SQL-паттерны в логах

**Способы решения:**
1. JOIN FETCH (DQL)
2. Partial Object (DQL)
3. `@ORM\Index` (ускорение, не устранение)
4. `EntityManager::getReference()` (proxy, откладывание)
5. Архитектурные: нормализация, денормализация, CQRS
6. Пагинация (уменьшение N)
7. Кэширование (Redis, Doctrine Query Cache)
8. `EntityManager::clear()` (очистка identity map)
9. `EntityManager::detach()` (открепление)
10. `@ORM\Index` + `@ORM\UniqueConstraint` (индексы)

Давай напишу структурированный ответ с кодом и trade-offs.

</details>

**Кандидат:**

# N+1 в Doctrine: диагностика и решение

## 1. Диагностика

### 1.1 Symfony Profiler (dev-среда) — первый шаг

```
GET /_profiler/{token}/?panel=doctrine
```

Что видно:

| Что | Как читать |
|---|---|
| **Число запросов** | 47 запросов на странице — уже подозрительно. 5 запросов — норма. |
| **Повторяющиеся паттерны** | `SELECT l.* FROM order_lines l WHERE l.order_id = ?` × 12 — это N+1. |
| **Время каждого** | 1 запрос × 200мс = 200мс. 12 запросов × 5мс = 60мс. N+1 не всегда медленный — но масштабируется линейно. |
| **Порядок** | Если 12 одинаковых запросов идут последовательно — это lazy loading в цикле. |

**Быстрый тест:** откройте profiler, найдите запрос, который повторяется N раз. Первый — загрузка коллекции. Остальные N — обращение к lazy-связанной сущности в цикле.

### 1.2 Doctrine Logger (production, без profiler)

```php
// config/services.yaml (prod)
services:
    _instanceof:
        Doctrine\DBAL\Connection:
            calls:
                - ['setNestingLevel', [1]]   # не логгируем nested

    # PSR-3 logger для Doctrine
    App\Infrastructure\Persistence\DoctrineSqlLogger:
        autowire: true
```

```php
// src/Infrastructure/Doctrine/DoctrineSqlLogger.php
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Logging\Middleware;

final class DoctrineSqlLogger
{
    public function __construct(
        private LoggerInterface $logger,
        private ?string         $env,
    ) {}

    public function log(string $sql, array $params, float $durationMs): void
    {
        // В prod: логгируем только медленные запросы
        if ($durationMs > 100) {
            $this->logger->warning('Slow SQL query', [
                'sql'      => $sql,
                'params'   => $params,
                'duration' => $durationMs,
            ]);
        }

        // Считаем запросы с одинаковым паттерном (без параметров)
        // Это позволяет увидеть N+1: 12 раз один и тот же sql-шаблон
        $pattern = $this->stripParams($sql);
        $this->logger->debug('SQL pattern', [
            'pattern'  => $pattern,
            'duration' => $durationMs,
        ]);
    }

    private function stripParams(string $sql): string
    {
        // Заменяем ? на :param_N для группировки
        return preg_replace('/\?/', ':p', $sql);
    }
}
```

```php
// src/Infrastructure/Doctrine/DoctrineMiddleware.php
use Doctrine\DBAL\Logging\Middleware;

final class DoctrineMiddleware extends Middleware
{
    public function __construct(
        private \Psr\Log\LoggerInterface $logger,
    ) {}

    public function startQuery(string $sql, ?array $params, ?array $types): void
    {
        $this->startTime = microtime(true);
        $this->lastSql   = $sql;
        $this->lastParams = $params;
    }

    public function stopQuery(): void
    {
        $duration = (microtime(true) - $this->startTime) * 1000;
        $this->logger->debug('SQL', [
            'sql'      => $this->lastSql,
            'params'   => $this->lastParams,
            'duration' => round($duration, 2),
        ]);
    }
}
```

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        middleware:
            - App\Infrastructure\Doctrine\DoctrineMiddleware
```

### 1.3 Blackfire (production, real users)

```bash
blackfire profile --server php-fpm
```

Отчёт показывает:
- **SQL-запросы по частоте и времени** — топ-10 медленных.
- **Повторяющиеся паттерны** — если `SELECT ... WHERE order_id = ?` входит в топ-3 по частоте — это N+1.
- **Waterfall** — видно, что 80% времени уходит на 12 одинаковых запросов.

### 1.4 XHProf (PHP-level)

```php
// src/Controller/OrderController.php
use XHProf\XHProf;

public function list(): Response
{
    XHProf::start();

    $orders = $this->repo->findActive();
    // ... рендер

    $prof = XHProf::stop();
    // Загрузить в Blackfire или xhprof.net
}
```

Полезно, если проблема не в SQL, а в **PHP-цикле** (сериализация, маппинг, валидация).

### 1.5 EXPLAIN ANALYZE (Postgres)

```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)
SELECT o.id, o.customer_id, o.status
FROM orders o
WHERE o.status = 'active'
ORDER BY o.created_at DESC
LIMIT 20;
```

Что ищем:
- `Seq Scan` на большой таблице → нужен индекс.
- `Sort Method: external merge` → не хватает памяти для сортировки.
- `Rows Removed by Filter` → индекс не помогает, filter после scan.

### 1.6 Быстрый чек-лист диагностики

| Вопрос | Где смотреть |
|---|---|
| Сколько всего SQL-запросов на странице? | Profiler / Logger |
| Есть ли повторяющиеся паттерны? | Profiler / Logger (group by pattern) |
| Какой запрос самый медленный? | Profiler / EXPLAIN |
| Это lazy loading в цикле или N+1 по JOIN? | Stack trace в profiler |
| Проблема в SQL или в PHP? | XHProf / Blackfire |
| Нужно ли вообще загружать эти данные? | Бизнес-требование |

---

## 2. Способы решения

### 2.1 JOIN FETCH (DQL) — основной инструмент

**Проблема:**

```php
// Репозиторий:
$orders = $em->getRepository(Order::class)
    ->createQueryBuilder('o')
    ->andWhere('o.status = :status')
    ->setParameter('status', 'active')
    ->setMaxResults(20)
    ->getQuery()
    ->getResult();

// Контроллер:
foreach ($orders as $order) {
    $lines = $order->getLines();  // ← LAZY LOADING → 1 запрос на каждый
    // ...
}
```

**SQL-поток (N+1):**
```sql
SELECT o.* FROM orders o WHERE o.status = 'active' LIMIT 20;   -- 1 запрос
SELECT l.* FROM order_lines l WHERE l.order_id = 'order-1';    -- запрос 1
SELECT l.* FROM order_lines l WHERE l.order_id = 'order-2';    -- запрос 2
...
SELECT l.* FROM order_lines l WHERE l.order_id = 'order-20';   -- запрос 20
-- Итого: 21 запрос
```

**Решение:**

```php
$orders = $em->getRepository(Order::class)
    ->createQueryBuilder('o')
    ->addSelect('l')
    ->join('o.lines', 'l')
    ->addSelect('l')
    ->andWhere('o.status = :status')
    ->setParameter('status', 'active')
    ->setMaxResults(20)
    ->getQuery()
    ->getResult();
```

Лучше через DQL:

```php
$orders = $em->createQuery(
    'SELECT o, l
     FROM App\Domain\Order\Order o
     JOIN o.lines l
     WHERE o.status = :status'
)
->setParameter('status', 'active')
->setMaxResults(20)
->getResult();
```

**SQL-поток (после):**
```sql
SELECT o.*, l.*
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
WHERE o.status = 'active'
LIMIT 20;
-- Итого: 1 запрос
```

#### Trade-offs

| Плюс | Минус |
|---|---|
| 1 запрос вместо N+1 | **Cartesian product**: если `Order` имеет 2 коллекции (`lines`, `coupons`), JOIN FETCH обоих → N × M строк |
| Простота — один `addSelect` | **Память**: все `OrderLine` загружены в PHP. Если 20 заказов × 50 линий = 1000 объектов |
| Не требует изменения схемы | **`setMaxResults` + JOIN FETCH**: Postgres применит `LIMIT` до JOIN → обрезка линий. Работает не всегда предсказуемо |
| | **Identity map**: если `Order` уже загружен, Doctrine вернёт cached-экземпляр. JOIN FETCH не обновит коллекцию |

**Опасность `setMaxResults` + JOIN FETCH:**

```php
// ОПАСНО:
$qb->addSelect('l')
   ->join('o.lines', 'l')
   ->setMaxResults(20)  // ← LIMIT 20 на результирующий набор после JOIN
```

Если `Order-1` имеет 5 линий, а `Order-2` — 3 линии:
- `LIMIT 20` вернёт 20 **строк** (order + line), не 20 **заказов**.
- Возможно, 20-я строка — 3-я линия 5-го заказа. 6-й заказ не загружен.

**Решение:** `DQL` + `DISTINCT` ( Doctrine обходится, но Postgres всё равно обрезает). Надёжнее — подзапрос:

```php
$ids = $em->createQuery(
    'SELECT o.id FROM Order o WHERE o.status = :status ORDER BY o.createdAt DESC'
)->setParameter('status', 'active')
 ->setMaxResults(20)
 ->getResult();

$ids = array_map(fn($r) => $r['id'], $ids);

$orders = $em->createQuery(
    'SELECT o, l FROM Order o JOIN o.lines l WHERE o.id IN (:ids)'
)->setParameter('ids', $ids)
 ->getResult();
```

Два запроса, но корректная пагинация.

---

### 2.2 Partial Object — загрузка только нужных полей

**Проблема:** список заказов на главной. Нужно: `id`, `status`, `totalCents`. Не нужно: `lines`, `customer.email`, `createdAt`.

**SQL (полная загрузка):**
```sql
SELECT o.id, o.customer_id, o.status, o.total_cents, o.total_currency,
       o.discount_cents, o.discount_currency, o.created_at
FROM orders o
WHERE o.status = 'active'
LIMIT 20;
```

**SQL (partial object):**
```sql
SELECT o.id, o.status, o.total_cents
FROM orders o
WHERE o.status = 'active'
LIMIT 20;
```

**Код:**

```php
$orders = $em->createQuery(
    'SELECT o.id, o.status, o.totalCents
     FROM Order o
     WHERE o.status = :status'
)
->setParameter('status', 'active')
->setMaxResults(20)
->getResult();
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Меньше данных с БД → быстрее сеть, быстрее парсинг | При обращении к `$order->getCustomer()` → **дополнительный запрос** (lazy) |
| Меньше памяти в PHP | **Порядок свойств**: если `totalCents` — computed property (не колонка), Doctrine не может его вернуть |
| Идеально для «карточек» в списке | **Identity map**: частичный объект и полный объект с тем же ID — **один и тот же**. Если сначала загрузили partial, потом полный — Doctrine вернёт partial (без `lines`), а не полный |

**Identity map ловушка:**

```php
// Загрузка partial:
$orders = $em->createQuery(
    'SELECT o.id, o.status FROM Order o'
)->setMaxResults(20)->getResult();

// Загрузка полного (в том же request):
$order = $em->find(Order::class, $orders[0]->getId());

// $order === $orders[0]  (identity map!)
// $order->getLines() → LAZY LOADING → доп. запрос
// $order->getCustomer() → LAZY LOADING → доп. запрос
// Partial «загрязнил» identity map
```

Решение: `$em->clear()` между нагрузками. Или не смешивать partial и полный в одном request.

---

### 2.3 `EntityManager::getReference()` — lazy proxy

**Проблема:** в списке заказов нужен `customer.name`. Не нужен весь `Customer` (email, phone, address).

**Без proxy:**
```php
// N+1: 1 запрос на каждый Order.customer
foreach ($orders as $order) {
    $name = $order->getCustomer()->getName();  // → SELECT * FROM customers WHERE id = ?
}
```

**С proxy:**
```php
$ref = $em->getReference(Customer::class, $order->getCustomerId());
$name = $ref->getName();  // → SELECT name FROM customers WHERE id = ?
// Только одно поле, один запрос
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Не создаёт запрос при создании proxy | При первом обращении к свойству — запрос |
| Можно использовать в `DQL` без JOIN | **Proxy не работает с `readonly`** (PHP 8.2) |
| Меньше данных | **Proxy + `serialize()`** — сломает proxy (станет обычным объектом) |
| | **Proxy + `__toString()`** — если `Customer::__toString()` вызывает `getEmail()` → доп. запрос |

**Ограничение:** `getReference()` работает только по **ID**. Не по `email`, не по `name`. Если `customerId` — string UUID — ок. Если связь через `@ManyToOne` с `@JoinColumn` — Doctrine знает FK, работает.

---

### 2.4 `@ORM\Index` — ускорение, не устранение

**Проблема:** N+1-запрос выполняется за 50мс. С индексом — 2мс. N+1 не исчез, но стал в 25 раз быстрее.

```php
#[ORM\Index(columns: ['order_id'])]
class OrderLine { /* … */ }
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 50мс → 2мс на каждый запрос | Замедляет `INSERT` / `UPDATE` (поддержание индекса) |
| Не меняет код | **Не масштабируется**: 20 заказов × 2мс = 40мс. 200 заказов × 2мс = 400мс |
| Простота | Индекс на `order_id` — обычно уже есть (FK). Проверьте: `SELECT indexname FROM pg_indexes WHERE tablename = 'order_lines'` |

**Когда помогает:** когда N+1-запрос идёт по **неиндексированному** полю. Например: `WHERE created_at > ?` без индекса → Seq Scan 100мс. С индексом → 2мс.

---

### 2.5 Архитектурные решения

#### 2.5.1 Денормализация (materialized column)

**Проблема:** `Order.lines` — N+1. Но на карточке заказа нужен только `totalCents`.

**Решение:** `totalCents` уже в таблице `orders`. `OrderLine` не нужен для карточки.

```php
// Карточка: SELECT id, status, total_cents FROM orders — 1 запрос, 0 N+1
// Детали: SELECT l.* FROM order_lines l WHERE l.order_id = ? — 1 запрос, 0 N+1
```

Это **уже есть** в схеме из предыдущего разбора. `totalCents` — денормализованное поле. N+1 на `lines` нужен только для детального просмотра.

**Trade-off:** денормализация → `totalCents` и `sum(lines)` могут расхождение. Решение: `recalculateTotal()` в корне агрегата + `CHECK`-контрент в БД.

#### 2.5.2 CQRS (Command Query Responsibility Segregation)

**Проблема:** запись — сложный агрегат с коллекциями. Чтение — плоская структура для UI.

**Решение:** разделение на **write model** (агрегат `Order` + `OrderLine`) и **read model** (плоская `OrderReadModel` в отдельной таблице или в Elasticsearch).

```
Write path:
  Command → OrderService → Order (агрегат) → orders + order_lines (БД)

Read path:
  Query → OrderReadRepository → order_read_model (БД / ES / Redis)
```

```sql
-- Read model (плоская, без JOIN):
CREATE TABLE order_read_model (
    id              VARCHAR(36) PRIMARY KEY,
    customer_id     VARCHAR(36) NOT NULL,
    status          VARCHAR(20) NOT NULL,
    total_cents     INT NOT NULL,
    total_currency  CHAR(3) NOT NULL,
    line_count      INT NOT NULL DEFAULT 0,
    first_product   VARCHAR(36),
    created_at      TIMESTAMPTZ NOT NULL,
    updated_at      TIMESTAMPTZ NOT NULL
);
```

Обновление: `OrderService` после `flush()` → `OrderReadModelUpdater` (event handler) → `UPDATE order_read_model`.

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Чтение — 1 запрос, 0 N+1, 0 JOIN | **Две модели** → сложнее поддерживать |
| Read model оптимизирована под UI | **Ложная согласованность**: read model может быть на 100ms «старой» |
| Индексы под конкретные запросы | **Дополнительная инфраструктура**: event handler, idempotency |
| Write model остаётся чистой (DDD) | **Не масштабируется** на 10+ read model'ей |

**Когда оправдано:** страница с 50+ сущностями, сложная фильтрация, сортировка по составным полям. Для 20 заказов с 5 линиями — избыточно.

#### 2.5.3 Материализованные представления (PostgreSQL)

```sql
CREATE MATERIALIZED VIEW order_summary AS
SELECT
    o.id,
    o.customer_id,
    o.status,
    o.total_cents,
    o.total_currency,
    COUNT(l.id) AS line_count,
    MIN(l.product_id) AS first_product,
    o.created_at
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
GROUP BY o.id;

CREATE INDEX ON order_summary (status, created_at DESC);

-- Обновление (asynchronous):
REFRESH MATERIALIZED VIEW CONCURRENTLY order_summary;
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 1 запрос, 0 N+1, 0 JOIN | **Задержка**: данные «старые» между refresh'ами |
| Индексы на материализованном представлении | `REFRESH CONCURRENTLY` блокирует чтение (Postgres 11+) |
| Простота — SQL, не PHP | **Не для OLTP**: если `orders` обновляется 100 раз/сек — refresh не успевает |

---

### 2.6 Кэширование

#### 2.6.1 Doctrine Query Cache

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        query_cache_driver:
            type: apcu
```

Кэширует **DQL → SQL** и **metadata** (маппинг сущностей). Не кэширует **результаты запросов**.

| Помогает с N+1? | Почему |
|---|---|
| Нет | N+1 — это N+1 **запросов к БД**. Query Cache кэширует DQL-компиляцию (микросекунды). БД-запросы идут как были. |

#### 2.6.2 Result Cache (Redis / APCu)

```php
// Кэшируем результат запроса:
$key = 'orders:active:20';
$orders = $this->cache->get($key);

if ($orders === null) {
    $orders = $em->createQuery(
        'SELECT o, l FROM Order o JOIN o.lines l WHERE o.status = :s'
    )->setParameter('s', 'active')->setMaxResults(20)->getResult();

    $this->cache->set($key, $orders, 300);  // 5 минут
}
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| 0 запросов к БД при кэше | **Сериализация**: `Order` → `serialize()` → Redis. `OrderLine` с `readonly` — ок. С `EntityManager`-ссылками — сломает |
| Мгновенный ответ | **Консистентность**: заказ изменён → кэш «старый» на 5 минут |
| Простота | **Invalidation**: как понять, что кэш устарел? Event после `flush()` → `$cache->delete()` |
| | **Память Redis**: 20 заказов × 50 линий × 200 байт = 200KB на запись. 1000 записей = 200MB |

**Когда оправдано:** данные редко меняются (каталог товаров, справочники). Не оправдано: заказы, платежи, баланс.

---

### 2.7 Пагинация — уменьшение N

**Проблема:** 20 заказов × 50 линий = 1000 объектов в памяти. N+1: 21 запрос.

**Решение:** 5 заказов × 50 линий = 250 объектов. N+1: 6 запросов.

```php
$page = $request->query->getInt('page', 1);
$per  = 5;  // вместо 20

$orders = $em->createQuery(
    'SELECT o, l FROM Order o JOIN o.lines l WHERE o.status = :s'
)
->setParameter('s', 'active')
->setMaxResults($per)
->setFirstResult(($page - 1) * $per)
->getResult();
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Меньше памяти, меньше запросов | **UX**: 5 заказов на страницу — мало |
| Простота | **`setFirstResult` + JOIN FETCH**: см. 2.1, `LIMIT` + `OFFSET` на JOIN-результате — неточно |
| | **Сортировка**: `ORDER BY o.created_at DESC` + `OFFSET` — Postgres должен отсортировать **все** строки, чтобы пропустить N. Медленно при больших N |

**Оптимизация:** keyset pagination (cursor-based):

```php
// Вместо OFFSET 100:
// WHERE created_at < :cursor ORDER BY created_at DESC LIMIT 5
```

Нет `OFFSET`. Быстро. Но требует уникальный `created_at` + `id` как составной курсор.

---

### 2.8 `EntityManager::clear()` и `detach()`

#### `clear()`

```php
// После загрузки списка:
$orders = $repo->findActive();  // N+1: 21 запрос
$em->clear();  // ← очищает identity map

// Теперь можно безопасно загрузить полный агрегат:
$order = $em->find(Order::class, $orders[0]->id);  // новый запрос, полная загрузка
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Чистый identity map | **Все managed-сущности становятся detached** — `flush()` не применит изменения |
| Нет «загрязнения» partial → full | **Дополнительные запросы**: если `Customer` уже загружен — повторный SELECT |
| Простота | **Не решает N+1**: устраняет только identity map confusion |

#### `detach()`

```php
$order = $em->find(Order::class, $id);
$em->detach($order);  // ← открепляет одну сущность

// Теперь $order — обычный PHP-объект. Изменения не отслеживаются.
// При последующей загрузке того же ID — новый запрос.
```

**Когда:** после `flush()`, если сущность больше не нужна, но занимает память в identity map.

---

## 3. Сравнительная таблица

| Способ | Устраняет N+1? | Сложность | Когда |
|---|---|---|---|
| **JOIN FETCH** | ✅ | Низкая | 1–2 коллекции, 10–50 элементов |
| **Partial Object** | ✅ (для нужных полей) | Низкая | Список, «карточки» |
| **`getReference()`** | ✅ (lazy) | Низкая | 1 поле из связанной сущности |
| **`@ORM\Index`** | ⚠️ Ускоряет | Минимальная | Неиндексированное поле в WHERE |
| **Денормализация** | ✅ | Средняя | Поле, которое всегда нужно |
| **CQRS / Read Model** | ✅ | Высокая | Сложные read-запросы, 50+ сущностей |
| **Матер. представление** | ✅ | Средняя | Пост-агрегаты, аналитика |
| **Кэширование** | ✅ (кэш) | Низкая | Редко меняющиеся данные |
| **Пагинация** | ⚠️ Уменьшает N | Низкая | Большие списки |
| **`clear()` / `detach()`** | ⚠️ Identity map | Низкая | Partial → full transition |

---

## 4. Алгоритм: что делать при обнаружении N+1

```
1. Определить: какой запрос повторяется N раз?
   → Profiler / Logger

2. Определить: lazy loading в цикле или JOIN?
   → Stack trace / DQL

3. Определить: нужен ли вообще этот объект в этом контексте?
   → Бизнес-требование
   → Если нет → Partial Object / getReference()

4. Если нужен:
   a. 1–2 коллекции, 10–50 элементов → JOIN FETCH
   b. 1 поле из связанной сущности → getReference()
   c. Поле всегда нужно → Денормализация
   d. Сложный read-запрос, 50+ сущностей → CQRS / Read Model
   e. Неиндексированное поле → @ORM\Index
   f. Большие данные, редко меняются → Кэш
   g. Большой список → Пагинация + keyset

5. Проверить:
   - Identity map: partial → full?
   - setMaxResults + JOIN FETCH?
   - readonly + proxy?
   - Сериализация в кэш?
```

---

## 5. Чего НЕ делать

| Антипаттерн | Почему |
|---|---|
| `JOIN FETCH` на 5 коллекций одновременно | Cartesian product: N × M × K × L × P строк |
| `EntityManager::clear()` в цикле | Каждый `clear()` → все последующие `find()` → доп. запросы |
| Кэш с TTL 5 минут для платежей | Пользователь видит «старый» баланс |
| `@ORM\Index` на 10 полей | Замедляет writes, индексы не используют |
| CQRS для 1 таблицы с 200 строк | Инфраструктура дороже проблемы |
| `EntityManager::detach()` на managed-сущность с pending changes | `flush()` не применит изменения |
| `JOIN FETCH` + `setMaxResults` без подзапроса | Непредсказуемая обрезка линий |

---

## 6. Итог

N+1 — это **симптом**, а не болезнь. Болезнь — одна из:

1. **Ленивая загрузка в цикле** → JOIN FETCH / Partial Object / getReference.
2. **Отсутствие денормализации** → материализованное поле.
3. **Смешение read и write моделей** → CQRS.
4. **Отсутствие индекса** → `@ORM\Index`.
5. **Слишком много данных** → пагинация, partial object.

Диагностика — 5 минут (profiler). Лечение — от 5 минут (JOIN FETCH) до 2 недель (CQRS). Начните с profiler, найдите повторяющийся паттерн, и 90% случаев лечатся одним `addSelect('l')`.

> ⏱ 69.8s · prompt 52209 tok · output 8700 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

The user is asking a deep architectural question: if all business validation is done in application code (PHP/Symfony), why bother with database-level integrity constraints in PostgreSQL? What tools do we use and in what cases?

This is a great question that touches on:
1. Defense in depth
2. The difference between business rules and data integrity
3. Practical PostgreSQL tools: CHECK, UNIQUE, NOT NULL, FK, EXCLUSION, GENERATED, PARTITION
4. When app-level validation is insufficient
5. Trade-offs

Let me think about the structure:

1. Why app-level validation is NOT enough (the core argument)
2. What DB constraints actually protect against (different threat model)
3. Specific PostgreSQL tools with examples
4. When to use which
5. Anti-patterns (over-constraining, constraining business logic in DB)
6. The boundary: what belongs where

Key insight: DB constraints protect against:
- Race conditions (two transactions simultaneously)
- Bugs in application code
- Direct DB access (DBA, scripts, other services)
- Corrupted data from migrations
- Multiple services writing to the same DB

App code protects against:
- Business rules (complex, multi-step)
- UX (friendly error messages)
- Workflow enforcement

They are NOT redundant. They are DIFFERENT LAYERS protecting against DIFFERENT THREATS.

Let me structure this clearly with examples, trade-offs, and practical guidance. I'll use PostgreSQL-specific features since the user asked about it.

I should be concrete: show actual DDL, show the scenarios where each constraint saves you, and show where DB constraints are the WRONG place to put logic.

</details>

**Кандидат:**

# Целостность БД: не дублирование, а другая линия обороны

## Почему валидация в коде не заменяет ограничения БД

Валидация в PHP отвечает на вопрос: *«Правильно ли это действие для *этого* пользователя, в *этом* контексте, в *этой* транзакции?»*

Ограничения БД отвечают на вопрос: *«Может ли *эта строка* существовать в *этой таблице* в принципе?»*

Это разные угрозы. Вот конкретные сценарии, где код не спасает, а `CHECK` или `UNIQUE` — спасает.

---

## 1. Гонка (race condition)

Два запроса приходят одновременно. PHP-валидация не успевает:

```
Т1: SELECT balance FROM accounts WHERE id = 'acc-1';  → 500
Т2: SELECT balance FROM accounts WHERE id = 'acc-1';  → 500

Т1: balance = 500 - 400 = 100;  → PASS (100 > 0)
Т2: balance = 500 - 400 = 100;  → PASS (100 > 0)

Т1: UPDATE accounts SET balance = 100 WHERE id = 'acc-1';
Т2: UPDATE accounts SET balance = 100 WHERE id = 'acc-1';

Результат: баланс 100. Должен быть -300. Деньги из воздуха.
```

**В PHP:**
```php
$account = $this->repo->find('acc-1');
if ($account->balanceCents < 400) {
    throw new InsufficientBalanceException();  // ← Т1 и Т2 обе PASS
}
$account->setBalance($account->balanceCents - 400);
$this->repo->save($account);
```

**В БД (спасает):**
```sql
ALTER TABLE accounts
    ADD CONSTRAINT chk_balance_non_negative
        CHECK (balance_cents >= 0);
```

Т2 упадёт на `CHECK`: `100 >= 0` → PASS? Нет, подождите. Оба пишут `100`. Оба PASS. `CHECK` тут **не спасает**, потому что оба записывают *допустимое* значение.

Настоящая защита — **оптимистическая блокировка**:

```sql
ALTER TABLE accounts
    ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// PHP:
$account = $this->repo->find('acc-1');  // version = 3
$account->setBalance($account->balanceCents - 400);

$em->flush();
// UPDATE accounts SET balance_cents = 100, version = 4
// WHERE id = 'acc-1' AND version = 3;
// ← Если Т2 успел: version уже 4. UPDATE затронул 0 строк.
// ← Exception: StaleObjectStateException
```

А теперь `CHECK` **подстраховывает** случай, если `version`-проверку пропустили (баг, прямой SQL, скрипт миграции):

```sql
CHECK (balance_cents >= 0)
```

Даже если кто-то запишет `-300` — БД откажет.

**Вывод:** `CHECK` не заменяет правильную транзакцию. Он — **последний барьер**, если что-то пошло не так выше.

---

## 2. Много писателей

В реальном приложении к одной БД пишут:

| Писатель | Пример |
|---|---|
| Веб-приложение (PHP) | Основной сервис |
| Worker (PHP) | Messenger handler, cron |
| Консоль (PHP) | `bin/console order:import` |
| Другой сервис (Go / Python) | Microservice с общим Postgres |
| DBA / скрипты | Миграции, бэкапы, ручные правки |
| ETL / аналитика | Выгрузка в DWH |

PHP-валидация покрывает **только первый**. Остальные — нет.

```sql
-- Worker (PHP) пишет:
UPDATE orders SET status = 'shipped' WHERE id = 'o-1';

-- Консоль (PHP) пишет:
UPDATE orders SET status = 'cancelled' WHERE id = 'o-1';

-- DBA вручную:
UPDATE orders SET status = 'test' WHERE id = 'o-1';
-- ← status = 'test' не существует в enum. PHP не проверяет.
```

**Защита:**
```sql
CREATE TYPE order_status AS ENUM ('draft', 'confirmed', 'shipped', 'delivered', 'cancelled');

ALTER TABLE orders
    ALTER COLUMN status TYPE order_status
    USING status::order_status;
```

Теперь `status = 'test'` → `invalid input value for enum`. **Независимо от того, кто пишет.**

---

## 3. Миграции и бэкапы

```bash
# pg_dump → restore на стейдже
# Миграция, которая «почти» работает:
ALTER TABLE orders ADD COLUMN total_cents INT DEFAULT 0;

# Батч-скрипт заполнения:
UPDATE orders SET total_cents = 0 WHERE total_cents IS NULL;

# Кто-то забыл:
# В БД остаются строки с total_cents = 0, хотя должно быть > 0
```

**Защита:**
```sql
ALTER TABLE orders
    ADD CONSTRAINT chk_total_positive
        CHECK (total_cents > 0);
```

Миграция упадёт на старых строках → вы **сразу** видите, что данные не заполнены. Без `CHECK` — баг всплывёт через 3 недели в проде.

---

## 4. Что именно использовать и когда

### 4.1 `NOT NULL`

**Когда:** поле, которое **всегда** должно существовать.

```sql
CREATE TABLE orders (
    id          VARCHAR(36) PRIMARY KEY,
    customer_id VARCHAR(36) NOT NULL,
    status      order_status NOT NULL DEFAULT 'draft',
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);
```

**Почему не в коде:** `NOT NULL` — это контракт схемы. Если `customer_id` nullable — это **другая бизнес-модель** (анонимный заказ). PHP не должен «забывать» поставить значение. БД не даст.

**Антипаттерн:** `NOT NULL DEFAULT ''` для текстовых полей. Лучше `NOT NULL` без default и обязательный ввод, либо `NULL` (если поле действительно опциональное). Пустая строка — это «состояние», не «отсутствие».

---

### 4.2 `UNIQUE`

**Когда:** поле (или набор полей), которое **идентифицирует** запись.

```sql
CREATE TABLE users (
    id       BIGSERIAL PRIMARY KEY,
    email    CITEXT NOT NULL UNIQUE,
    -- ...
);

CREATE TABLE order_lines (
    order_id  VARCHAR(36) NOT NULL,
    product_id VARCHAR(36) NOT NULL,
    -- ...
    CONSTRAINT uq_order_product UNIQUE (order_id, product_id)
);
```

**Почему не в коде:**

```php
// PHP:
if ($this->repo->findOneByEmail($email)) {
    throw new EmailTakenException();
}
$this->repo->save($user);
```

Два запроса → две вставки → одна строка дублируется. `UNIQUE` в БД — **атомарная** защита. `SELECT` + `INSERT` в PHP — нет.

**`CITEXT` vs `lower()`:**

```sql
-- CITEXT: "User@Example.com" = "user@example.com"
CREATE EXTENSION IF NOT EXISTS citext;
email CITEXT NOT NULL UNIQUE

-- Альтернатива: generated column
email VARCHAR(255) NOT NULL,
email_normalized VARCHAR(255) GENERATED ALWAYS AS (lower(email)) STORED,
UNIQUE (email_normalized)
```

`CITEXT` проще. `GENERATED` работает на всех СУБД.

---

### 4.3 `CHECK`

**Когда:** доменное ограничение **одного поля** или **одной строки**, которое **не зависит от других таблиц**.

```sql
ALTER TABLE accounts
    ADD CONSTRAINT chk_balance
        CHECK (balance_cents >= 0);

ALTER TABLE order_lines
    ADD CONSTRAINT chk_quantity
        CHECK (quantity > 0 AND quantity <= 9999);

ALTER TABLE orders
    ADD CONSTRAINT chk_discount_not_exceed_total
        CHECK (discount_cents <= total_cents);

ALTER TABLE users
    ADD CONSTRAINT chk_password_hash
        CHECK (password_hash LIKE '$2b$%' OR password_hash LIKE '$argon2id$%');
```

**Что `CHECK` НЕ делает:**

```sql
-- ПЛОХО: бизнес-правило в БД
CHECK (status = 'shipped' AND shipping_method IS NOT NULL)

-- ПЛОХО: сложная логика
CHECK (
    (tier = 'enterprise' AND discount_pct <= 30)
    OR
    (tier = 'pro' AND discount_pct <= 10)
    OR
    (tier = 'basic' AND discount_pct = 0)
)

-- ПЛОХО: зависимость от другой таблицы
CHECK (customer_id IN (SELECT id FROM customers WHERE active = true))
-- ← Это FK, не CHECK
```

**Критерий:** если правило можно выразить как `column_op_value` или `col1_op_col2` — это `CHECK`. Если нужно `SELECT` из другой таблицы или `CASE` с 5 ветками — это **домен**, не БД.

---

### 4.4 `FOREIGN KEY`

**Когда:** ссылка на другую таблицу **должна** существовать.

```sql
ALTER TABLE orders
    ADD CONSTRAINT fk_order_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE RESTRICT;  -- нельзя удалить customer с заказами
```

**`ON DELETE`:**

| Стратегия | Когда |
|---|---|
| `RESTRICT` | Связь «обязательная». Не могу удалить, пока есть ссылки. **По умолчанию.** |
| `CASCADE` | Дочерние записи «принадлежат» родительской. Удаление `Order` → удаление `OrderLine`. |
| `SET NULL` | Ссылка опциональна. Удалили `Product` → `order_lines.product_id = NULL`. |
| `SET DEFAULT` | Редко. Удалили `Department` → `orders.department_id = 'default'`. |

**Почему не в коде:**

```php
// PHP:
$order->setCustomerId('non-existent-id');
$this->repo->save($order);
// ← PHP не проверяет существование. БД — да.
```

**Производительность:** `FK` — это `INSERT`/`UPDATE`/`DELETE` на **каждую** операцию. Для `orders` (10M строк) с 3 FK — 3 дополнительных `SELECT` на вставку. На Postgres 14+ с `MERGE` — меньше. Но это **миллисекунды**, а не «проблема».

**Антипаттерн:** `FK` на `jsonb` поле. Не работает. `FK` — только на скалярные колонки.

---

### 4.5 `EXCLUSION` (PostgreSQL-специфично)

**Когда:** диапазоны не должны пересекаться.

```sql
CREATE TABLE booking_slots (
    id         BIGSERIAL PRIMARY KEY,
    room_id    INT NOT NULL REFERENCES rooms(id),
    slot_start TIMESTAMPTZ NOT NULL,
    slot_end   TIMESTAMPTZ NOT NULL,
    CHECK (slot_end > slot_start),

    EXCLUDE USING gist (
        room_id WITH =,
        tstzrange(slot_start, slot_end) WITH &&
    )
);
```

`&&` — пересечение интервалов. Попытка вставить пересекающийся слот → `conflicting key value violates exclusion constraint`.

**Почему не в коде:**

```php
// PHP:
$existing = $repo->findOverlapping($roomId, $start, $end);
if ($existing) throw new ConflictException();
$repo->save($newSlot);
// ← Гонка: два запроса одновременно → оба PASS → оба вставились
```

`EXCLUDE` — **атомарный**, на уровне индекса. Гонка невозможна.

**Ограничение:** только PostgreSQL. Только `gist`/`btree`/`hash`/`spgist`/`brin` индексы.

---

### 4.6 `GENERATED` columns

**Когда:** поле вычисляется **детерминированно** и **не может** быть неконсистентным.

```sql
ALTER TABLE order_lines
    ADD COLUMN line_total_cents INT
        GENERATED ALWAYS AS (unit_cents * quantity) STORED;

ALTER TABLE users
    ADD COLUMN email_normalized VARCHAR(255)
        GENERATED ALWAYS AS (lower(email)) STORED;
```

**Почему не в коде:**

```php
// PHP:
$line->setUnitCents(100);
$line->setQuantity(5);
// Забыл: $line->setLineTotalCents(500);
// → В БД: unit_cents=100, quantity=5, line_total_cents=NULL
// → Агрегат: total = sum(line_total_cents) → NULL → баг
```

`GENERATED` — **нельзя** записать вручную. `INSERT INTO order_lines (line_total_cents) VALUES (999)` → ошибка.

**Ограничение:** только `STORED` (вычисляется при записи) или `VIRTUAL` (Postgres 18+, вычисляется при чтении). Нет сложных функций. Нет `SELECT`.

---

### 4.7 `TRIGGER` + `FUNCTION`

**Когда:** логика, которую **нельзя** выразить через `CHECK`/`GENERATED`, но которая **должна** выполняться **в атомарно** с записью.

```sql
-- Пример: ledger. Баланс всегда = SUM(transactions).
-- Не CHECK (нужен SUM из другой таблицы).
-- Не PHP (гонка, много писателей).

CREATE FUNCTION fn_update_balance() RETURNS TRIGGER AS $$
BEGIN
    UPDATE accounts
       SET balance_cents = balance_cents + (
           CASE
               WHEN NEW.type = 'credit' THEN  NEW.amount_cents
               WHEN NEW.type = 'debit'  THEN -NEW.amount_cents
               ELSE 0
           END
       )
     WHERE id = NEW.account_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_balance
    AFTER INSERT ON transactions
    FOR EACH ROW
    EXECUTE FUNCTION fn_update_balance();
```

**Trade-offs:**

| Плюс | Минус |
|---|---|
| Атомарно с INSERT | **Скрытая логика**: не видно из кода приложения |
| Работает для всех писателей | **Трудно отлаживать**: ошибка в `plpgsql` — не в PHP-стектрейсе |
| Нет гонки | **Не тестируется PHPUnit-ом**: нужен тест на уровне БД |
| | **Зависимость от Postgres**: перенос на MySQL → переписать |

**Правило:** `TRIGGER` — **последний инструмент**. Если можно сделать через `CHECK`, `GENERATED`, `EXCLUDE` — сделайте так. `TRIGGER` — когда логика **межтабличная** и **атомарность критична**.

---

### 4.8 `PARTITION BY` + ограничения на партицию

```sql
CREATE TABLE events (
    id         BIGSERIAL,
    type       VARCHAR(50) NOT NULL,
    payload    JSONB NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    PRIMARY KEY (id, created_at)
) PARTITION BY RANGE (created_at);

CREATE TABLE events_2025_01 PARTITION OF events
    FOR VALUES FROM ('2025-01-01') TO ('2025-02-01');

CREATE TABLE events_2025_02 PARTITION OF events
    FOR VALUES FROM ('2025-02-01') TO ('2025-03-01');
```

Не «ограничение целостности», но **гарантия**, что данные лежат в правильной партиции. Попытка вставить `created_at = '2025-03-15'` без партиции → ошибка (если `DEFAULT` партиция не создана).

---

## 5. Где граница: домен vs. БД

```
┌─────────────────────────────────────────────────────────────────────┐
│  ДОМЕН (PHP)                                                        │
│                                                                     │
│  • Сложные бизнес-правила (множественные условия, контекст)         │
│  • Workflow (Order: draft → confirmed → shipped)                   │
│  • Правила, зависящие от пользователя / роли / времени              │
│  • Друзья: OrderVoter, OrderService, Order (агрегат)               │
│  • Ошибка: DomainException → 400/422 пользователю                  │
├─────────────────────────────────────────────────────────────────────┤
│  БД (PostgreSQL)                                                    │
│                                                                     │
│  • Схемные инварианты (NOT NULL, UNIQUE, FK)                       │
│  • Атомарность при гонке (CHECK, EXCLUDE, TRIGGER)                 │
│  • Защита от «чужих» писателей (ENUM, CHECK)                       │
│  • Детерминированные вычисления (GENERATED)                        │
│  • Ошибка: IntegrityConstraintViolation → 500 (баг системы)        │
└─────────────────────────────────────────────────────────────────────┘
```

**Ключевое различие в семантике ошибки:**

| Где упало | Что это значит | Код ответа |
|---|---|---|
| PHP (`DomainException`) | Пользователь сделал что-то **неправильное** | 400 / 422 |
| БД (`IntegrityConstraintViolation`) | Система **сломалась** (гонка, баг, чужой писатель) | 500 |

Если пользователь получает `500` из-за `CHECK (balance_cents >= 0)` — это **не** его вина. Это значит, что PHP-валидация не сработала. Это баг. `CHECK` — не замена валидации, а **детектор** того, что валидация не сработала.

---

## 6. Практический набор для типового проекта

```sql
-- ═══ users ═══════════════════════════════════════════════════════
CREATE TABLE users (
    id            BIGSERIAL PRIMARY KEY,
    email         CITEXT NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(255) NOT NULL,
    role          VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),

    CHECK (password_hash LIKE '$2b$%' OR password_hash LIKE '$argon2id$%'),
    CHECK (full_name <> '')
);

-- ═══ orders ══════════════════════════════════════════════════════
CREATE TYPE order_status AS ENUM ('draft','confirmed','shipped','delivered','cancelled');

CREATE TABLE orders (
    id              VARCHAR(36) PRIMARY KEY,
    customer_id     BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    status          order_status NOT NULL DEFAULT 'draft',
    total_cents     INT NOT NULL,
    total_currency  CHAR(3) NOT NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT now(),

    CHECK (total_cents > 0),
    CHECK (total_currency ~ '^[A-Z]{3}$')
);

-- ═══ order_lines ═════════════════════════════════════════════════
CREATE TABLE order_lines (
    id           BIGSERIAL PRIMARY KEY,
    order_id     VARCHAR(36) NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id   BIGINT NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    unit_cents   INT NOT NULL,
    quantity     INT NOT NULL,
    line_total   INT GENERATED ALWAYS AS (unit_cents * quantity) STORED,

    CHECK (unit_cents > 0),
    CHECK (quantity > 0 AND quantity <= 9999),
    CONSTRAINT uq_order_product UNIQUE (order_id, product_id)
);

-- ═══ accounts (ledger) ═══════════════════════════════════════════
CREATE TABLE accounts (
    id            BIGSERIAL PRIMARY KEY,
    user_id       BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    balance_cents INT NOT NULL DEFAULT 0,
    version       INT NOT NULL DEFAULT 0,

    CHECK (balance_cents >= 0),
    CHECK (version >= 0)
);

CREATE TABLE transactions (
    id            BIGSERIAL PRIMARY KEY,
    account_id    BIGINT NOT NULL REFERENCES accounts(id) ON DELETE RESTRICT,
    type          VARCHAR(10) NOT NULL,
    amount_cents  INT NOT NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),

    CHECK (type IN ('credit', 'debit')),
    CHECK (amount_cents > 0)
);
```

**Что намеренно НЕ в БД:**

| Правило | Почему в PHP |
|---|---|
| «Нельзя отменить заказ в статусе `shipped`» | Workflow. Зависит от контекста. В PHP: `Order::cancel()` проверяет |
| «Админ может удалить любой заказ» | Роль. В PHP: `OrderVoter` |
| «Скидка не может превышать 50% для tier=pro» | Бизнес-правило. Зависит от `tier`. В PHP: `PricingService` |
| «Нельзя изменить заказ, если он уже оплачен» | Зависит от `PaymentService`. В PHP: `OrderService` |

---

## 7. Антипаттерны

| Антипаттерн | Почему плохо |
|---|---|
| `CHECK` с `CASE` на 10 веток | Это бизнес-логика. Не читаема. Не тестируется PHPUnit-ом. |
| `TRIGGER` для «просто логируем» | Скрытая логика. Не видно из кода. Отладка в `plpgsql`. |
| `FK` на `jsonb` | Не работает. `FK` — только скалярные колонки. |
| `CHECK (email LIKE '%@%')` | Валидация email — домен. `CHECK` — только «не пусто» / формат схемы. |
| `DEFAULT ''` для `VARCHAR` | Пустая строка ≠ NULL. Лучше `NOT NULL` или `NULL`. |
| `UNIQUE` на 5 колонок | «Набор полей» — это почти PK. Если нужен — сделайте `PK` или составной `UNIQUE` с именем. |
| `TRIGGER` + `SELECT` из 3 таблиц | Это PHP. `TRIGGER` — только для атомарных межтабличных обновлений. |
| `CHECK` как замена `ENUM` | `ENUM` даёт тип, автодополнение, `WHERE status = 'shipped'` без опечатки. `CHECK (status IN ('a','b','c'))` — то же самое, но без типа. |

---

## 8. Итог

Ограничения БД — это **не дублирование** валидации. Это:

1. **Атомарность** при гонке (`CHECK`, `EXCLUDE`, `UNIQUE`).
2. **Защита от чужих писателей** (`ENUM`, `NOT NULL`, `FK`).
3. **Детерминированность** (`GENERATED` — нельзя забыть пересчитать).
4. **Детектор багов** (если `CHECK` упал в проде — PHP-валидация не сработала, и это **500**, а не **400**).

PHP говорит: *«Этот пользователь не может сделать это действие»*.
БД говорит: *«Эта строка не может существовать в этой таблице»*.

Два разных вопроса. Два разных уровня. Оба нужны.

> ⏱ 51.1s · prompt 59010 tok · output 6065 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе диагностики и оптимизации медленных запросов в PostgreSQL. Это продолжение серии вопросов о PHP/Symfony/Doctrine/PostgreSQL. Нужно дать структурированный, практический ответ.

Что важно покрыть:
1. Пошаговый процесс диагностики (от "медленно" до "почему")
2. Инструменты (EXPLAIN, pg_stat_statements, pg_stat_activity, pg_stat_user_indexes, autovacuum, lock monitoring)
3. Типовые причины (отсутствие индекса, seq scan, lock contention, bloat, неправильный план, параметризованные запросы, memory issues)
4. Оптимизация (индексы, переписывание запроса, партиционирование, материализованные представления, настройки)
5. Trade-offs

Я уже обсуждал N+1 в предыдущем ответе, так что не буду дублировать. Фокус именно на PostgreSQL как СУБД, а не на PHP-уровне.

Структура:
1. Пошаговый процесс (от наблюдения к решению)
2. Инструменты (с примерами вывода)
3. Типовые причины (с конкретными примерами)
4. Оптимизация (с примерами)
5. Чего не делать

Дам конкретные SQL-запросы, примеры EXPLAIN, конкретные настройки. Это должно быть практично, не теоретически.

</details>

**Кандидат:**

# Диагностика медленных запросов в PostgreSQL

## 0. Первое: что именно "медленно"?

Прежде чем лезть в `EXPLAIN`, уточните:

| Вопрос | Зачем |
|---|---|
| Медленно **один** запрос или **все**? | Один → план/индекс. Все → ресурсы (CPU, IO, memory, locks) |
| Медленно **всегда** или **периодически**? | Всегда → план. Периодически → lock contention, autovacuum, bloat |
| Медленно **на чтении** или **на записи**? | Чтение → seq scan, отсутствие индекса. Запись → WAL, lock, trigger |
| Сколько **строк** возвращает? | 10 строк за 5с — проблема в плане. 1M строк за 5с — проблема в объёме |
| `SELECT` или `UPDATE`/`DELETE`? | UPDATE/DELETE медленнее из-за MVCC, WAL, locks |
| Был **быстрее** раньше? | Регрессия: новый индекс, изменение данных, план-кэш |

Это занимает 2 минуты и сразу сужает пространство.

---

## 1. Инструменты: что запускать и в каком порядке

### 1.1 `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)`

**Основной инструмент.** Запускайте **всегда**, прежде чем что-либо менять.

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING, FORMAT JSON)
SELECT o.id, o.status, o.total_cents
FROM orders o
JOIN customers c ON c.id = o.customer_id
WHERE o.status = 'active'
  AND o.created_at >= now() - interval '30 days'
ORDER BY o.created_at DESC
LIMIT 20;
```

Ключевые флаги:

| Флаг | Что даёт |
|---|---|
| `ANALYZE` | **Выполняет** запрос. Без него — только предсказание плана (может не совпадать с реальностью) |
| `BUFFERS` | Сколько блоков (8KB) прочитано из shared_buffers vs. диска. `shared hits` vs. `disk reads` |
| `TIMING` | Время на каждом узле дерева. Без него — только `actual time` на корневом узле |
| `WAL` | Сколько WAL-записей. Важно для UPDATE/INSERT |
| `FORMAT JSON` | Структурированный вывод. Удобно парсить скриптом |

### 1.2 Чтение `EXPLAIN`: на что смотреть

Пример вывода (упрощённо):

```
Limit (cost=0.56..8.82 rows=20) (actual time=2.1..215.3 rows=20)
  Buffers: shared hit=152 read=8470
  ->  Sort (cost=4520.5..4520.7 rows=120) (actual time=214.8..215.1 rows=20)
        Sort Key: o.created_at DESC
        Sort Method: top-N heapsort  Memory: 34kB
        Buffers: shared hit=152 read=8470
        ->  Nested Loop (cost=0.56..4518.2 rows=120) (actual time=0.04..210.5 rows=118)
              Buffers: shared hit=152 read=8470
              ->  Seq Scan on orders o (cost=0.00..4210.5 rows=120) (actual time=0.02..198.3 rows=118)
                    Filter: ((status = 'active') AND (created_at >= ...))
                    Rows Removed by Filter: 4820
                    Buffers: shared hit=145 read=8470
              ->  Index Scan using pk_customers on c (cost=0.56..2.50 rows=1)
                    Index Cond: (id = o.customer_id)
                    Buffers: shared hit=7
```

**Красные флаги:**

| Что вижу | Что значит |
|---|---|
| `Seq Scan` на таблице > 100K строк | Нет индекса под WHERE. Или селекторность низкая (см. ниже) |
| `Rows Removed by Filter: 4820` из 4938 | Скан 100% таблицы, фильтрует 97%. Индекс нужен |
| `shared read=8470` (8470 × 8KB ≈ 66MB с диска) | Данные не в shared_buffers. Холодный кэш. Если запрос повторяется — должно быть `hit` |
| `Sort Method: external merge` | Не хватило `work_mem`. Сортировка на диске |
| `Nested Loop` с `actual rows` >> `estimated rows` | Статистика устарела. `ANALYZE` |
| `Hash Join` с `Hash Batches > 1` | `hash_mem` мал. Увеличить `work_mem` |
| `CTE` с материализацией | CTE в PG < 12 — `MATERIALIZED` по умолчанию. `NOT MATERIALIZED` если не нужно |
| `Gather` (parallel) с `Workers: 0` | Параллелизм не включился. `min_parallel_table_scan_size` |

### 1.3 `pg_stat_statements` — топ медленных за 30 дней

```sql
-- Расширение (одноразово):
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- В postgresql.conf:
-- shared_preload_libraries = 'pg_stat_statements'
-- pg_stat_statements.track = all

-- Топ-20 по среднему времени:
SELECT
    calls,
    round(mean_exec_time::numeric, 2) AS avg_ms,
    round(total_exec_time::numeric, 2) AS total_ms,
    rows,
    round((shared_blks_hit * 1.0 / NULLIF(shared_blks_hit + shared_blks_reads, 0))::numeric, 4) AS cache_hit_ratio,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 20;

-- Топ-20 по суммарному времени (какие запросы "съедают" ресурсы):
SELECT
    calls,
    round(mean_exec_time::numeric, 2) AS avg_ms,
    round(total_exec_time::numeric, 2) AS total_ms,
    round(total_exec_time::numeric / 1000, 1) AS total_s,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;

-- Запросы с низким cache hit ratio (чтение с диска):
SELECT
    left(query, 120) AS query,
    calls,
    round((shared_blks_hit * 1.0 / NULLIF(shared_blks_hit + shared_blks_reads, 0))::numeric, 4) AS hit_ratio,
    shared_blks_reads AS disk_reads
FROM pg_stat_statements
WHERE shared_blks_reads > 1000
  AND shared_blks_hit * 1.0 / NULLIF(shared_blks_hit + shared_blks_reads, 0) < 0.9
ORDER BY shared_blks_reads DESC
LIMIT 20;
```

**Что искать:**

| Паттерн | Диагноз |
|---|---|
| `calls = 10000`, `avg_ms = 50` | N+1 или hot loop. 10K × 50мс = 500с суммарно |
| `cache_hit_ratio < 0.8` | Данные не в RAM. Увеличить `shared_buffers` или оптимизировать запрос |
| `rows = 1000000`, `avg_ms = 5000` | Запрос возвращает 1M строк. Нужна пагинация или агрегация |
| Один и тот же SQL, но `avg_ms` растёт со временем | Bloat, устаревшая статистика, growing table |

### 1.4 `pg_stat_activity` — что происходит прямо сейчас

```sql
-- Кто подключён и что делает:
SELECT
    pid,
    usename,
    application_name,
    state,
    wait_event_type,
    wait_event,
    round((now() - query_start)::interval, 3) AS duration,
    left(query, 100) AS query
FROM pg_stat_activity
WHERE state <> 'idle'
ORDER BY query_start;

-- Заблокированные запросы:
SELECT
    blocked.pid AS blocked_pid,
    blocked.usename AS blocked_user,
    left(blocked.query, 80) AS blocked_query,
    blocking.pid AS blocking_pid,
    blocking.usename AS blocking_user,
    left(blocking.query, 80) AS blocking_query,
    blocking.state AS blocking_state
FROM pg_stat_activity blocked
JOIN pg_locks bl ON bl.pid = blocked.pid AND bl.granted = false
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
    AND kl.pid <> bl.pid
    AND kl.granted = true
JOIN pg_stat_activity blocking ON blocking.pid = kl.pid
WHERE bl.granted = false;
```

**Типовые находки:**

| `wait_event` | Значение |
|---|---|
| `Lock / relation` | Ждёт lock на таблицу. Кто-то держит длинную транзакцию |
| `IO / DataFileRead` | Читает с диска. Холодный кэш или seq scan |
| `CPU` | ЦП-ограничено. Сложный JOIN, сортировка, агрегация |
| `LWLock / buffer_content` | Конкуренция за блоки буферов. Высокая параллельность |
| `Client / ClientRead` | Клиент (PHP) не читает ответ. Не проблема БД |

### 1.5 `pg_stat_user_indexes` — неиспользуемые и перегруженные индексы

```sql
-- Индексы, которые никто не использует (кандидаты на удаление):
SELECT
    schemaname,
    relname AS table,
    indexrelname AS index,
    idx_scan AS scans,
    pg_size_pretty(pg_relation_size(indexrelid)) AS size
FROM pg_stat_user_indexes
WHERE idx_scan = 0
  AND schemaname = 'public'
ORDER BY pg_relation_size(indexrelid) DESC;

-- Индексы, которые читают много (хорошие, но проверьте, не дублируют ли):
SELECT
    relname,
    indexrelname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE idx_tup_fetch > 0
ORDER BY idx_tup_read DESC
LIMIT 20;
```

### 1.6 `pg_stat_user_tables` — bloat и dead tuples

```sql
-- Таблицы с большим количеством dead tuples (кандидаты на VACUUM):
SELECT
    relname,
    n_live_tup,
    n_dead_tup,
    round(n_dead_tup * 100.0 / NULLIF(n_live_tup + n_dead_tup, 0), 1) AS dead_pct,
    last_vacuum,
    last_autovacuum,
    pg_size_pretty(pg_total_relation_size(relid)) AS total_size
FROM pg_stat_user_tables
WHERE n_dead_tup > 1000
ORDER BY n_dead_tup DESC
LIMIT 20;

-- Если dead_pct > 20% и last_autovacuum давно → autovacuum не успевает
```

### 1.7 `pg_locks` — анализ блокировок

```sql
-- Текущие locks:
SELECT
    l.pid,
    a.usename,
    l.mode,
    l.granted,
    l.locktype,
    c.relname
FROM pg_locks l
JOIN pg_stat_activity a ON a.pid = l.pid
LEFT JOIN pg_class c ON c.oid = l.relation
WHERE l.granted = false
ORDER BY l.pid;
```

---

## 2. Типовые причины и решения

### 2.1 Отсутствие индекса / неподходящий индекс

**Симптом:** `Seq Scan`, `Rows Removed by Filter` > 50%.

```sql
-- EXPLAIN показывает:
-- Seq Scan on orders (rows=120, actual rows=118)
--   Filter: status = 'active' AND created_at >= ...
--   Rows Removed by Filter: 4820
```

**Решение:**

```sql
CREATE INDEX CONCURRENTLY idx_orders_status_created
    ON orders (status, created_at DESC)
    WHERE status = 'active';   -- partial index: только 'active'
```

**Почему partial:** если 95% строк — `active`, обычный индекс мало помогает. Partial индексирует только нужные строки → меньше размер, быстрее поиск.

**Важно:** `CONCURRENTLY` — не блокирует таблицу на время создания. Нельзя в транзакции.

**Когда индекс НЕ помогает:**

| Ситуация | Почему |
|---|---|
| `WHERE email LIKE '%pattern%'` | Prefix search не работает. Нужен `pg_trgm` + GIN |
| `WHERE created_at BETWEEN ? AND ?` на 80% таблицы | Селекторность низкая. Seq scan быстрее (Postgres знает) |
| `WHERE json_col->>'key' = 'val'` | Нет expression index. Создать: `CREATE INDEX ON t ((json_col->>'key'))` |

### 2.2 Устаревшая статистика

**Симптом:** `estimated rows` сильно отличается от `actual rows`. План неправильный.

```
-- EXPLAIN:
-- Nested Loop (cost=... rows=120) (actual rows=15000)
--         ↑ estimate            ↑ реальность
--         ×125 разне!
```

**Решение:**

```sql
ANALYZE orders;
ANALYZE order_lines;
```

**Почему происходит:**
- Таблица выросла (миллион → 10M), статистика от старой версии.
- `autovacuum` отключён или не успевает.
- Данные изменились неравномерно (99% строк — `active`, 1% — `archived`).

**Профилактика:**

```sql
-- postgresql.conf
autovacuum = on
autovacuum_max_workers = 4
autovacuum_naptime = '60s'
autovacuum_vacuum_scale_factor = 0.05   -- 5% dead tuples → vacuum
autovacuum_analyze_scale_factor = 0.05  -- 5% изменений → analyze

-- Для горячих таблиц — агрессивнее:
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.02,
    autovacuum_analyze_scale_factor = 0.02
);
```

### 2.3 `work_mem` слишком мал → сортировка/хэш на диске

**Симптом:** `Sort Method: external merge disk`, `Hash Batches: 12`.

```sql
-- EXPLAIN:
-- Sort (actual time=... rows=...)
--   Sort Method: external merge  Disk: 24512kB
--   ↑ данные не помещаются в RAM, пишутся на диск
```

**Решение:**

```sql
-- Глобально (осторожно):
-- work_mem = '256MB'   -- на сессию, на сортировку/хэш

-- На запрос (точечно):
SET work_mem = '512MB';
SELECT ...;

-- Или на уровне сессии в PHP:
-- $connection->executeStatement('SET work_mem = \'512MB\'');
```

**Trade-off:** `work_mem` — на **операцию** (sort/hash), не на запрос. Если запрос содержит 3 сортировки — 3 × 512MB. 100 параллельных сессий × 3 × 512MB = 150GB RAM. **Считайте.**

### 2.4 Bloat (накопление dead tuples)

**Симптом:** `n_dead_tup / (n_live_tup + n_dead_tup) > 20%`. Таблица физически большая. Seq scan медленный, потому что читает мёртвые строки.

```sql
-- Диагностика:
SELECT
    relname,
    pg_size_pretty(pg_relation_size(relid)) AS table_size,
    n_live_tup,
    n_dead_tup,
    round(n_dead_tup * 100.0 / GREATEST(n_live_tup + n_dead_tup, 1), 1) AS dead_pct
FROM pg_stat_user_tables
WHERE n_dead_tup > 10000;
```

**Решение:**

```sql
-- Немедленный VACUUM (в проде — осторожно):
VACUUM (ANALYZE, VERBOSE) orders;

-- VACUUM FULL — пересоздаёт таблицу, блокирует запись. ТОЛЬКО в окне обслуживания:
VACUUM (FULL, ANALYZE) orders;
```

**Почему bloat:**
- Много UPDATE/DELETE без VACUUM.
- Long-running transaction держит old xmin → dead tuples не удаляются.
- `autovacuum` отключён или параметры слишком пассивные.

**Профилактика:**

```sql
-- postgresql.conf
autovacuum = on
autovacuum_max_workers = 4
autovacuum_vacuum_cost_limit = 1000
autovacuum_analyze_scale_factor = 0.05

-- Для горячих таблиц:
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.02,
    autovacuum_vacuum_cost_limit = 2000
);
```

### 2.5 Lock contention

**Симптом:** Запросы висят. `pg_stat_activity` показывает `state = active`, `wait_event = Lock/relation`.

```sql
-- Кто держит lock:
SELECT
    a.pid,
    a.usename,
    a.state,
    a.query_start,
    left(a.query, 100) AS query
FROM pg_stat_activity a
JOIN pg_locks l ON l.pid = a.pid
WHERE l.relation = 'orders'::regclass
  AND a.state <> 'idle'
ORDER BY a.query_start;
```

**Типовые причины:**

| Причина | Диагностика |
|---|---|
| Длинная транзакция в PHP (не `COMMIT`-нул) | `pg_stat_activity`: `state = idle in transaction`, `query_start` час назад |
| `SELECT ... FOR UPDATE` в цикле | `pg_locks`: много `FOR UPDATE` на одной таблице |
| `VACUUM FULL` на большой таблице | `pg_stat_activity`: `query` содержит `VACUUM` |
| Deadlock | `pg_stat_activity`: 2+ пидов с `wait_event = Lock` |

**Решение:**

```sql
-- Найти и убить длинную транзакцию (осторожно):
SELECT pg_terminate_backend(pid) FROM pg_stat_activity
WHERE state = 'idle in transaction'
  AND now() - query_start > interval '5 minutes';

-- Предотвратить:
-- 1. В PHP: всегда $em->close() или $em->clear() после операции
-- 2. В postgresql.conf:
--    statement_timeout = '30s'
--    idle_in_transaction_session_timeout = '60s'
```

### 2.6 Неверный выбор плана (plan regression)

**Симптом:** Запрос был быстрый, стал медленным. Данные изменились.

**Причины:**

| Причина | Пример |
|---|---|
| Данные изменили селективность | 90% строк `active` → индекс перестал помогать. Postgres переключился на seq scan |
| Статистика устарела | `ANALYZE` |
| `SET search_path` изменился | Виден другой объект |
| Обновление Postgres | План-кэш пересчитан |
| Новый индекс "перепутал" оптимизатор | Удалить новый индекс |

**Решение:**

```sql
-- 1. Пересчитать статистику:
ANALYZE;

-- 2. Посмотреть, какой план Postgres выбирает сейчас:
EXPLAIN (ANALYZE, BUFFERS) <ваш запрос>;

-- 3. Если план плохой из-за нового индекса:
DROP INDEX CONCURRENTLY idx_suspect;

-- 4. Если план плохой из-за данных (селекторность):
--    Ничего не сделать. Seq scan — правильный выбор.
--    Оптимизировать запрос (меньше данных, пагинация)

-- 5. Зафиксировать план (крайний случай):
--    Не рекомендуется. Postgres не имеет FORCE INDEX.
--    Альтернатива: pg_hint_plan (расширение), но это костыль.
```

### 2.7 `SELECT *` + широкие таблицы

**Симптом:** Запрос быстрый на `EXPLAIN`, но медленный "в жизни". `Buffers: shared read=50000`.

**Причина:** `SELECT *` читает все колонки, включая `TEXT`, `JSONB`. Каждая строка — 10KB. 1000 строк = 10MB с диска.

**Решение:**

```sql
-- Было:
SELECT * FROM orders WHERE status = 'active' LIMIT 20;

-- Стало:
SELECT id, status, total_cents, created_at
FROM orders
WHERE status = 'active'
ORDER BY created_at DESC
LIMIT 20;
```

**Бонус:** coverинг индекс (index-only scan):

```sql
CREATE INDEX idx_orders_covering
    ON orders (created_at DESC)
    INCLUDE (id, status, total_cents)
    WHERE status = 'active';
```

Postgres может ответить **только из индекса**, не читая таблицу. `Seq Scan` → `Index Only Scan`.

### 2.8 `JSONB` без индекса

**Симптом:** `WHERE data->>'type' = 'payment'` → Seq Scan.

**Решение:**

```sql
-- Expression index:
CREATE INDEX idx_events_type
    ON events ((data->>'type'));

-- GIN для поиска внутри:
CREATE INDEX idx_events_data
    ON events USING GIN (data jsonb_path_ops);

-- Для часто используемых ключей — generated column:
ALTER TABLE events ADD COLUMN event_type VARCHAR(50)
    GENERATED ALWAYS AS (data->>'type') STORED;
CREATE INDEX idx_events_event_type ON events (event_type);
```

### 2.9 Слишком много данных возвращают

**Симптом:** `rows = 500000`, `actual time = 12000ms`. Запрос корректный, но возвращает полмиллиона строк.

**Решение:**

| Подход | Когда |
|---|---|
| `LIMIT` + пагинация | Список |
| Агрегация в БД (`COUNT`, `SUM`) | Статистика |
| Материализованное представление | Сложная агрегация, редкие обновления |
| Партиционирование | Таблица > 100M строк |

```sql
-- Вместо:
SELECT * FROM events WHERE created_at >= now() - interval '90 days';
-- 500K строк

-- Стало:
SELECT type, count(*), sum(amount)
FROM events
WHERE created_at >= now() - interval '90 days'
GROUP BY type;
-- 5 строк
```

### 2.10 `CTE` как барьер оптимизации (PG < 12)

**Симптом:** CTE "замораживает" подзапрос, оптимизатор не может push-down фильтры.

```sql
-- PG 11 и ниже:
WITH recent AS (
    SELECT * FROM orders WHERE created_at >= now() - interval '30 days'
)
SELECT r.*, c.name
FROM recent r
JOIN customers c ON c.id = r.customer_id
WHERE r.status = 'active';

-- CTE материализуется ВСЁ (без фильтра по status)
-- → 50K строк в CTE, потом JOIN, потом FILTER
```

**Решение (PG 12+):** CTE — `NOT MATERIALIZED` по умолчанию, если не с `UNION`. Оптимизатор пушит фильтры.

**Решение (PG < 12):**

```sql
-- Убрать CTE:
SELECT o.*, c.name
FROM orders o
JOIN customers c ON c.id = o.customer_id
WHERE o.created_at >= now() - interval '30 days'
  AND o.status = 'active';
```

---

## 3. Процесс: чек-лист диагностики

```
1. Воспроизвести. Зафиксировать точный SQL + параметры.
   → pg_stat_activity (live) или log (после)

2. EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)
   → На каком узле теряется время?
   → Seq Scan? Sort on disk? Nested Loop с большим inner?

3. Сравнить estimated vs. actual rows
   → Расхождение > 10× → ANALYZE
   → Расхождение < 2× → план верный, проблема в другом (IO, locks)

4. pg_stat_statements: сколько раз вызывается?
   → calls > 1000 → N+1 / hot loop → оптимизировать вызов
   → calls = 1 → разовый запрос → оптимизировать план

5. pg_stat_user_tables: bloat?
   → dead_pct > 20% → VACUUM
   → last_autovacuum > 1 день → параметры autovacuum

6. pg_stat_user_indexes: индекс используется?
   → idx_scan = 0 → удалить (если не нужен)
   → idx_tup_fetch >> idx_tup_read → индекс читает, но мало находит

7. pg_stat_activity: locks?
   → wait_event = Lock → кто держит?
   → idle in transaction > 5min → убить

8. pg_stat_user_indexes: есть ли подходящий индекс?
   → Нет → создать
   → Есть, но не используется → проверить план, статистку, селективность

9. Buffers: shared hit vs. disk reads?
   → hit_ratio < 0.8 → данные не в RAM → shared_buffers / оптимизация запроса

10. Проверить настройки:
    work_mem, shared_buffers, effective_cache_size
    → work_mem = 4MB (default) → external sort → увеличить
    → effective_cache_size = 128MB (default) → оптимизатор не знает о RAM → увеличить
```

---

## 4. Оптимизация: приоритет действий

| Приоритет | Действие | Влияние |
|---|---|---|
| **1** | Убрать `SELECT *`, сузить WHERE | Часто 10× без изменений схемы |
| **2** | `ANALYZE` (если статистика устарела) | 0.1с, мгновенный эффект |
| **3** | Индекс под WHERE/JOIN/ORDER BY | 10–100× на выборке |
| **4** | `work_mem` на запрос | 2–10× на сортировке/хэше |
| **5** | VACUUM (если bloat) | 2–5× на seq scan |
| **6** | Переписать запрос (JOIN → subquery, CTE → inline) | 2–5× |
| **7** | `partial index` / `covering index` | 2× + экономия RAM |
| **8** | Партиционирование | Только при > 50M строк |
| **9** | `pg_prewarm`, `effective_cache_size` | Холодный старт |
| **10** | Аппаратное (SSD → NVMe, +RAM) | 2–5× на IO-bound |

**Порядок важнее инструмента.** Индекс без `ANALYZE` может не сработать. `VACUUM` без устранения причины bloat (long transaction) — временно.

---

## 5. Чего НЕ делать

| Антипаттерн | Почему |
|---|---|
| `SELECT *` "чтобы потом не дорабатывать" | Читает все колонки, включая `JSONB` на 100KB |
| `VACUUM FULL` в проде на большой таблице | Блокирует запись. Пересоздаёт файл. Часы простоя |
| `SET work_mem = '2GB'` глобально | 100 сессий × 2GB × 3 операции = 600GB RAM |
| Индекс на каждый столбец | Замедляет INSERT/UPDATE. 50% ресурсов — на поддержку индексов |
| `pg_terminate_backend` на "медленный" запрос | Если запрос правильный (ETL, отчёт) — убили легитимную нагрузку |
| Удаление индекса "на всякий случай" | `EXPLAIN` мог не использовать его из-за устаревшей статистики. `ANALYZE` сначала |
| `SET statement_timeout = '5s'` глобально | Legit-запросы (ETL, backup) умирают. Точечно, на сессию |
| Перенос на Redis "чтобы быстрее" | Если запрос на 50мс — Redis не нужен. Если на 5с — Redis не поможет (данные не в Redis) |
| `pg_hint_plan` | Костыль. Сначала `ANALYZE`, потом индекс, потом переписать запрос. `pg_hint_plan` — если ничего не помогло и вы понимаете, почему оптимизатор "глупеет" |

---

## 6. Одна фраза

Медленный запрос — это **симптом**. Лечить симптом (увеличить `work_mem`, добавить индекс) без понимания причины (bloat, гонка, 1M строк вместо 20, устаревшая статистика) — значит получить быстрый запрос, который через неделю станет медленным снова. `EXPLAIN (ANALYZE, BUFFERS)` + `pg_stat_statements` + `pg_stat_activity` — три инструмента, которые за 10 минут показывают, **где** проблема. Остальное — лечение.

> ⏱ 63.7s · prompt 64768 tok · output 7568 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегат в DDD: определение, границы, пример, типовая ошибка. Это продолжение длинного разговора о PHP/Symfony/Doctrine/DDD. Нужно ответить глубоко, но без воды. Структура:

1. Что такое агрегат — чёткое определение, не размытое
2. Как определяю границы — конкретный процесс, не "зависит от контекста"
3. Пример — разберу на конкретной сущности, покажу как границы влияют на код
4. Типовая ошибка — не абстрактная, а с кодом "до" и "после"

Важно: я уже в предыдущих ответах использовал пример Order + OrderLine. Сейчас нужно либо углубиться в тот же пример с акцентом на ГРАНИЦЫ (а не на хранение), либо взять новый пример. Лучше углубиться, но под другим углом: не "как хранить", а "почему именно эти границы, а не другие".

Типовые ошибки при выборе границ агрегата:
1. Слишком большой агрегат ("God aggregate") — Order включает Payment, Shipment, Invoice, Notification. Всё в одном. Результат: один flush() = 5 таблиц, deadlock, параллельные запросы блокируют друг друга.
2. Слишком маленький агрегат — каждая сущность отдельно. Order, OrderLine, Payment, Shipment — все независимые. Результат: нет инвариантов. Можно создать Order без Payment. Можно удалить OrderLine из Order. Несохранность.
3. Агрегат с двумя корнями — Order + Shipment оба "корни", но связаны. Кто владелец? Кто валидирует?
4. Граница по техническим соображениям (одна таблица = один агрегат)
5. Граница по UI (одна страница = один агрегат)

Я возьму пример с Order, Payment, Shipment, Invoice. Покажу, как неправильно определить границы (всё в один агрегат), и как правильно (Order — агрегат, Payment — отдельный, Shipment — отдельный, Invoice — отдельный). И покажу конкретные инварианты, которые теряются при слишком малой границе.

Типовая ошибка: "Order включает Payment, потому что без Payment Order не имеет смысла". Это частая логическая ошибка: "смысл" ≠ "целостность". Payment — отдельный процесс, с другим lifecycle, другим team owner, другой частотой изменений.

Процесс определения границ — покажу конкретные вопросы:
- Какие инварианты должны быть атомарными?
- Кто валидирует?
- Какой lifecycle?
- Кто изменяет? (одна команда? разные команды?)
- Частота изменений?
- Объём данных?

Напишу структурированно, с кодом, с конкретными инвариантами. Без воды.

</details>

**Кандидат:**

# Агрегат в DDD: определение и границы

## Что такое агрегат

Агрегат — это **минимальная группа сущностей**, для которой инварианты (непреложные правила домена) гарантируются **атомарно** в рамках одной транзакции.

Ключевое слово — **атомарно**. Не "логически связаны", не "используются вместе", не "имеют смысл как единое целое". Атомарно: либо все инварианты соблюдаются, либо ни один.

```
Агрегат = {
    корень (root)         — единственный вход, через него валидация
    сущности (entities)   — имеют ID, живут внутри агрегата
    value objects         — неизменяемые, не имеют ID
    инварианты            — правила, которые гарантируются вместе
}
```

Корень — **единственная точка входа**. Внешний мир не может обратиться к `OrderLine` напрямую. Только через `Order`. Это не ограничение ради ограничения — это гарантия, что инварианты не будут обойдены.

---

## Как определяю границы

Я не начинаю с "что логически связано". Я начинаю с **инвариантов**.

### Шаг 1: Выписать инварианты

Для домена "Заказ" я записываю правила, которые **должны** выполняться всегда:

| # | Инвариант | Что ломается, если нарушен |
|---|---|---|
| I1 | `Order.total = sum(OrderLine.lineTotal)` | Бухгалтерия врёт |
| I2 | `Order.status` не может "прыгнуть" (draft → shipped без confirmed) | Пользователь платит, но заказ не подтверждён |
| I3 | `Order` в статусе `confirmed` не может изменять `lines` | Пользователь меняет товар после оплаты |
| I4 | `Payment.amount = Order.total` на момент оплаты | Переплата / недоплата |
| I5 | `Payment` существует только для `confirmed` `Order` | Оплата "воздушного" заказа |
| I6 | `Shipment.trackingNumber` уникален | Двойная доставка |
| I7 | `Invoice.total = Order.total` (или `= Order.total − refund`) | Счёт не совпадает с заказом |
| I8 | `Order` в статусе `shipped` не может быть `cancelled` | Вернули заказ, который уже везут |

### Шаг 2: Группировать инварианты по атомарности

Вопрос: **какие инварианты должны быть гарантированы в ОДНОЙ транзакции?**

| Группа | Инварианты | Почему вместе |
|---|---|---|
| **Order + OrderLine** | I1, I2, I3, I8 | Изменение `lines` → пересчёт `total` → проверка `status`. Одна операция. |
| **Payment** | I4, I5 | `Payment` создаётся при `confirm`. Но `Payment` имеет **свой lifecycle** (pending → paid → refunded). |
| **Shipment** | I6 | `Shipment` создаётся при `ship`. Свой lifecycle (pending → in_transit → delivered). |
| **Invoice** | I7 | `Invoice` создаётся после `paid`. Свой lifecycle (draft → sent → paid → overdue). |

### Шаг 3: Проверить по критериям

| Критерий | Order | Payment | Shipment | Invoice |
|---|---|---|---|---|
| Инварианты атомарны? | ✅ I1–I3, I8 | ✅ I4–I5 | ✅ I6 | ✅ I7 |
| Один owner (команда)? | Order team | Payments team | Logistics team | Billing team |
| Частота изменений? | Высокая (добавление линий) | Низкая (1 раз) | Низкая (1 раз) | Низкая (1 раз) |
| Lifecycle независим? | ✅ | ✅ (может быть refunded без изменения Order) | ✅ | ✅ |
| Объём данных? | Средний | Малый | Малый | Малый |
| Можно ли изменить Order без изменения Payment? | ✅ (добавить линию до confirm) | ❌ (после confirm — нет) | ✅ | ✅ |

**Результат: 4 отдельных агрегата.**

```
┌─────────────────────┐     ┌──────────────┐
│  Order (агрегат)     │     │  Payment      │
│  ┌─────────────────┐ │     │  (агрегат)    │
│  │ OrderLine × N   │ │     │  amount       │
│  │ total           │ │     │  status       │
│  │ status          │ │     │  paidAt       │
│  └─────────────────┘ │     └──────┬───────┘
└──────────┬───────────┘            │
           │ orderId (ref)          │
           ▼                        │
┌─────────────────────┐     ┌──────┴───────┐
│  Shipment            │     │  Invoice      │
│  (агрегат)           │     │  (агрегат)    │
│  trackingNumber      │     │  total        │
│  carrier             │     │  status       │
└─────────────────────┘     │  dueDate      │
                            └──────────────┘
```

Связь — через **ID** (`orderId`), не через object reference. Это принципиально: каждый агрегат **не знает** о внутренностях другого.

---

## Пример: код

### Order (агрегат)

```php
final class Order
{
    private string $id;
    private string $customerId;
    private OrderStatus $status;
    private Money $total;
    /** @var OrderLine[] */
    private array $lines = [];

    // I1: total = sum(lines) — гарантируется
    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->unitPrice()->amountCents() * $line->quantity();
        }
        $this->total = Money::fromCents($sum, 'USD');
    }

    // I3: нельзя менять lines после confirm
    public function addLine(ProductId $pid, Money $price, int $qty): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderLockedException(
                "Cannot add lines to order in status {$this->status->name}"
            );
        }
        // ...
        $this->lines[] = new OrderLine($pid, $price, $qty);
        $this->recalculateTotal();
    }

    // I2: переходы только по правилу
    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status, OrderStatus::Confirmed);
        }
        if (count($this->lines) === 0) {
            throw new EmptyOrderException();
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function cancel(): void
    {
        // I8: нельзя отменить shipped
        if (in_array($this->status, [OrderStatus::Shipped, OrderStatus::Delivered])) {
            throw new InvalidTransitionException(
                $this->status, OrderStatus::Cancelled,
                "Cannot cancel a {$this->status->name} order"
            );
        }
        $this->status = OrderStatus::Cancelled;
    }

    // Корень — единственный вход
    public function id(): string { return $this->id; }
    public function total(): Money { return $this->total; }
    public function status(): OrderStatus { return $this->status; }
    /** @return OrderLine[] */
    public function lines(): array { return $this->lines; }
}
```

### Payment (отдельный агрегат)

```php
final class Payment
{
    private string $id;
    private string $orderId;   // ← ссылка, не объект
    private Money $amount;
    private PaymentStatus $status;

    // I4: amount фиксируется при создании
    // I5: создаётся только для confirmed Order (проверяет сервис, не Payment)

    public static function createForOrder(string $orderId, Money $amount): self
    {
        $p = new self();
        $p->id = Uuid::uuid4()->toString();
        $p->orderId = $orderId;
        $p->amount = $amount;   // заморожено
        $p->status = PaymentStatus::Pending;
        return $p;
    }

    public function markPaid(string $externalRef): void
    {
        if ($this->status !== PaymentStatus::Pending) {
            throw new PaymentAlreadySettledException($this->id);
        }
        $this->status = PaymentStatus::Paid;
        $this->externalRef = $externalRef;
    }

    public function refund(Money $amount, string $reason): void
    {
        if ($this->status !== PaymentStatus::Paid) {
            throw new PaymentNotPaidException($this->id);
        }
        if ($amount->amountCents() > $this->amount->amountCents()) {
            throw new RefundExceedsPaymentException();
        }
        $this->status = PaymentStatus::Refunded;
    }

    // Payment НЕ знает про Order. У него только $orderId.
}
```

### Критическое различие

```php
// ПЛОХО: Payment знает про Order
class Payment
{
    private Order $order;   // ← объект

    public function __construct(Order $order) {
        $this->order = $order;
        $this->amount = $order->total();   // ← читает внутренний агрегат
    }

    public function markPaid(): void {
        $this->order->markPaid();   // ← мутирует чужой агрегат!
    }
}

// ХОРОШО: Payment знает только ID
class Payment
{
    private string $orderId;   // ← ссылка

    public static function createForOrder(string $orderId, Money $amount): self
    {
        // amount передан извне. Payment не читает Order.
    }
}
```

---

## Сервис: оркестрация между агрегатами

```php
final class CheckoutService
{
    public function __construct(
        private OrderRepository   $orders,
        private PaymentRepository $payments,
        private OrderMapper       $orderMapper,     // домен → БД
        private PaymentMapper     $paymentMapper,   // домен → БД
        private EntityManagerInterface $em,
    ) {}

    public function checkout(string $orderId): Payment
    {
        // 1. Загрузить агрегат
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        // 2. Мутация агрегата (инварианты I2, I3, I8 — внутри Order)
        $order->confirm();

        // 3. Создать Payment (инварианты I4, I5 — внутри Payment)
        $payment = Payment::createForOrder(
            $order->id(),
            $order->total(),   // ← значение, не объект
        );

        // 4. Атомарно сохранить ОБА агрегата
        $this->em->wrapInTransaction(function () use ($order, $payment): void {
            $this->orders->save($order);
            $this->payments->save($payment);
            $this->em->flush();
        });

        return $payment;
    }
}
```

**Важно:** `wrapInTransaction` обхватывает **оба** агрегата. Если `save($payment)` упадёт — `save($order)` откатится. Инвариант "Payment существует только для confirmed Order" (I5) гарантирован на уровне БД.

Но `Order` и `Payment` **не знают друг о друге**. Связь — через `$orderId` (string). Это и есть граница.

---

## Типовая ошибка: God Aggregate

### Как это выглядит

```php
// ПЛОХО: один агрегат на всё
final class Order
{
    private string $id;
    private OrderStatus $status;
    private Money $total;
    private array $lines;

    // ── А теперь Payment внутри Order ──
    private Payment $payment;

    // ── А теперь Shipment ──
    private Shipment $shipment;

    // ── А теперь Invoice ──
    private Invoice $invoice;

    // ── А теперь Notification ──
    private array $notifications;

    public function processPayment(StripeResponse $resp): void
    {
        $this->payment->markPaid($resp);
        $this->status = OrderStatus::Paid;       // мутация Order
        $this->invoice->recalculate();            // мутация Invoice
        $this->notifications[] = new Notification(...);  // мутация Notification
    }

    public function ship(string $tracking): void
    {
        $this->shipment = new Shipment($tracking);
        $this->status = OrderStatus::Shipped;
    }

    public function cancel(): void
    {
        $this->status = OrderStatus::Cancelled;
        $this->payment->refund(...);              // мутация Payment
        $this->invoice->void();                   // мутация Invoice
        $this->shipment->cancel();                // мутация Shipment
    }
}
```

### Что ломается

| Проблема | Деталь |
|---|---|
| **Один `flush()` = 5 таблиц** | `UPDATE orders`, `UPDATE payments`, `UPDATE shipments`, `UPDATE invoices`, `INSERT notifications`. Если `UPDATE invoices` упал (constraint) — **все** откатываются. Включая `orders.status = 'cancelled'`. |
| **Deadlock** | Worker A: `checkout` → lock `orders` → lock `payments`. Worker B: `refund` → lock `payments` → lock `orders`. Deadlock. |
| **Параллельные запросы** | `ship()` и `refund()` одновременно → оба мутируют `Order` → один перезаписывает другого. |
| **Тестирование** | Чтобы тестировать `Payment::markPaid()` — нужно создать `Order` + `OrderLine[]` + `Shipment` + `Invoice` + `Notification[]`. 500 строк тестовой настройки. |
| **Изменение логики** | Добавили `PartialRefund` → правите `Order::cancel()`, `Order::processPayment()`, `Order::ship()`. 3 места. |
| **Команды** | Payments team правит `Order.php`. Logistics team правит `Order.php`. Конфликты. |

### Корень ошибки

Разработчик думает: *«Order без Payment не имеет смысла, значит Payment — часть Order»*.

Это **ошибка логической связи ≠ граница целостности**. Payment имеет:

- Свой lifecycle (pending → paid → refunded → disputed).
- Своя команда (Payments team).
- Своя частота изменений (refund может прийти через 30 дней).
- Свой внешний провайдер (Stripe, Adyen).
- Свой SLA (payment должен быть идемпотентным, idempotency key).

Это **4 разных агрегата**, которые **связаны**, но **не вложены**.

### Правило

> Если две сущности имеют **разный lifecycle**, **разных owner'ов**, **разную частоту изменений** — это **два агрегата**, даже если логически они "вместе".

---

## Вторая типовая ошибка: слишком маленькие агрегаты

### Как это выглядит

```php
// ПЛОХО: каждая сущность — отдельный "агрегат"
final class OrderLine
{
    public function __construct(
        private ProductId $productId,
        private Money $unitPrice,
        private int $quantity,
    ) {}

    // OrderLine НЕ знает про Order.
    // Никто не гарантирует: total = sum(lines)
    // Никто не гарантирует: status = draft при addLine
}

final class Order
{
    public function __construct(
        private string $customerId,
        private array $lines,   // ← массив, не коллекция с валидацией
    ) {}

    // Никто не гарантирует: status переходы
    // Никто не гарантирует: lines не пусты при confirm
}
```

### Что ломается

```php
// Сервис:
$order = new Order($customerId, []);
$order->setStatus(OrderStatus::Confirmed);  // ← пустой заказ подтверждён!
$this->repo->save($order);

$order->lines[] = new OrderLine($pid, $price, $qty);  // ← добавили после confirm
$this->repo->save($order);

// total = 0, но status = confirmed. I1 и I2 нарушены.
// Никто не проверил, потому что инварианты не "живут" в ни одной сущности.
```

### Правило

> Если инвариант **касается нескольких сущностей** — эти сущности **должны** быть в одном агрегате. Иначе инвариант не гарантирован.

---

## Как проверить, что границы правильные

### Чек-лист

| Вопрос | Если "да" → одна группа. Если "нет" → разные агрегаты |
|---|---|
| Должны ли инварианты быть атомарными? | I1: total = sum(lines) → Order + OrderLine вместе |
| Есть ли один owner? | Order team ≠ Payments team → разные агрегаты |
| Разный lifecycle? | Order: draft→confirmed→shipped. Payment: pending→paid→refunded → разные |
| Разная частота изменений? | Order: 10×/день. Payment: 1× → разные |
| Можно ли изменить одну без другой? | Add line к Order (да) vs. Refund Payment (да) → разные |
| Объём данных? | Order: 50 строк. Payment: 1 строка → не обязательно вместе |

### Тест: "могу ли я удалить?"

> Могу ли я удалить `Payment`, не удаляя `Order`?

- **Да** (Order можно отменить, Payment уже refunded) → разные агрегаты. ✅
- **Нет** (без Payment Order не валиден) → один агрегат. ❌ (в 99% случаев — ошибка)

### Тест: "могу ли я изменить?"

> Могу ли я изменить `OrderLine.quantity`, не затрагивая `Order.status`?

- **Да** (когда status = draft) → OrderLine внутри Order, но мутация через корень.
- **Нет** (когда status = confirmed) → инвариант I3. Гарантируется через корень `Order`.

---

## Итог

Агрегат — это **граница атомарности инвариантов**, не "логическая группа связанных сущностей".

| Правильно | Неправильно |
|---|---|
| Граница по **инвариантам** | Граница по "логической связи" |
| Граница по **lifecycle** | Граница по "одна таблица" |
| Граница по **owner / команде** | Граница по "одна страница UI" |
| Связь через **ID** (string) | Связь через **object reference** |
| Мутация через **корень** | Мутация через **любую сущность** |
| 4 агрегата: Order, Payment, Shipment, Invoice | 1 агрегат: Order (с Payment, Shipment, Invoice внутри) |

Граница агрегата — это **архитектурное решение**, а не техническое. Её определяют **доменными инвариантами**, а не схемой БД. И она **не совпадает** с "что логически связано" в 70% случаев.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

# Конкурентная модификация агрегата

## Что произойдёт (конкретно)

### Сценарий

Два веб-воркера (два PHP-процесса, две сессии MySQL/Postgres) одновременно обрабатывают запросы на один и тот же `Order`:

```
Т1 (worker A)                          Т2 (worker B)
─────────────────────────────────────   ─────────────────────────────────────
                                        │
SELECT * FROM orders WHERE id='o-1'     │
→ status='draft', total=500             │
                                        │
SELECT * FROM orders WHERE id='o-1'     │
→ status='draft', total=500             │
                                        │
$order->addLine(...)                    │
→ total = 800                           │
                                        │
                                        │ $order->cancel()
                                        │ → status = 'cancelled'
UPDATE orders
SET status='draft', total=800
WHERE id='o-1'
→ OK (1 row)                            │
                                        │
                                        │ UPDATE orders
                                        │ SET status='cancelled', total=500
                                        │ WHERE id='o-1'
                                        │ → OK (1 row)
COMMIT                                  │
                                        │ COMMIT
```

**Результат:** `status='cancelled'`, `total=500`. Добавленная линия (total 500→800) **тихо потеряна**. Никто не получил ошибку. Никто не знает, что произошло.

Это называется **lost update**. И это **дефолтное поведение** PostgreSQL и MySQL в изоляции `READ COMMITTED` (дефолт в обоих).

### Почему Doctrine не спасает

Каждый PHP-процесс имеет:

| Что | Процесс A | Процесс B |
|---|---|---|
| `EntityManager` | Свой экземпляр | Свой экземпляр |
| Identity map | Свой | Свой |
| `Connection` (socket) | Свой | Свой |
| `UnitOfWork` | Свой | Свой |

Каждый процесс читает строку из БД, создаёт **свой** PHP-объект `Order`, мутирует его в памяти, затем генерирует `UPDATE`. Doctrine **не знает**, что другой процесс тоже изменил ту же строку. `StaleObjectStateException` не бросится, потому что **версионирование не настроено**.

`Identity map` защищает только **в пределах одного процесса** (внутренняя консистентность). Между процессами — ничего.

---

## Решение 1: Оптимистичная блокировка (Optimistic Locking)

### Механизм

Добавляем колонку `version` (или `updated_at`). Каждый `UPDATE` содержит условие `WHERE version = <то, что я прочитал>`. Если другой процесс успел увеличить `version` — `UPDATE` затронул **0 строк**.

### В Doctrine

```php
// src/Domain/Order/Order.php (доменная сущность)

final class Order
{
    private string $id;
    private OrderStatus $status;
    private Money $total;
    private int $version;   // ← доменное поле, не "техническое"

    // ...

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status, OrderStatus::Confirmed);
        }
        $this->status = OrderStatus::Confirmed;
    }

    public function version(): int { return $this->version; }
    public function bumpVersion(): void { $this->version++; }
}
```

```php
// src/Infrastructure/Persistence/ORM/OrderRecord.php (ORM-сущность)

#[ORM\Entity]
final class OrderRecord
{
    #[ORM\Id, ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'integer')]
    private int $totalCents;

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version = 0;   // ← Doctrine управляет

    // get/set ...
}
```

`#[ORM\Version]` делает три вещи автоматически:

1. При `INSERT` → `version = 0` (или `1`).
2. При `UPDATE` → Doctrine генерирует:
   ```sql
   UPDATE orders
   SET status = 'confirmed', total_cents = 800, version = 2
   WHERE id = 'o-1' AND version = 1;
   ```
3. Если `affected rows = 0` → Doctrine бросает `StaleObjectStateException`.

### Что происходит в конкурентном сценарии

```
Т1                                    Т2
─────────────────────────────────────   ─────────────────────────────────────
SELECT → version=1                      │
ORDER BY:                               │
  status='draft', total=500, v=1        │
                                        │ SELECT → version=1
$order->addLine()                       │   status='draft', total=500, v=1
→ total=800                             │
                                        │ $order->cancel()
UPDATE orders                           │   → status='cancelled'
SET total=800, version=2
WHERE id='o-1' AND version=1            │
→ 1 row ✓                               │
COMMIT                                  │
                                        │ UPDATE orders
                                        │ SET status='cancelled', version=2
                                        │ WHERE id='o-1' AND version=1
                                        │ → 0 rows ✗
                                        │
                                        │ StaleObjectStateException
                                        │ → 409 Conflict / retry
```

**Т2 получил явную ошибку**, а не тихую потерю данных. Клиент может:
- Перегрузить агрегат (`findById` снова) и повторить операцию.
- Показать пользователю «Заказ был изменён другим пользователем. Обновите страницу».

### Тест

```php
public function test_concurrent_confirm_and_addLine(): void
{
    $order = Order::reconstitute('o-1', 'c-1', OrderStatus::Draft,
        Money::fromCents(500, 'USD'), 'c-1', 1);

    // Т1: addLine
    $order->addLine(ProductId::of('p-2'), Money::fromCents(300, 'USD'), 1);
    $this->assertSame(800, $order->total()->amountCents());

    // Т2: cancel (с тем же version=1)
    // В реальном коде: два разных EntityManager, два процесса.
    // В тесте: имитируем через version check.

    $this->expectException(StaleObjectStateException::class);

    // Флеш Т2 с version=1, но в БД уже version=2
    // → UPDATE ... WHERE version=1 → 0 rows → Exception
}
```

### Trade-offs

| Плюс | Минус |
|---|---|
| Нет блокировки. Оба процесса читают параллельно | При высоком конфликте — много retries (thundering herd) |
| Просто: одна колонка, одна аннотация | Не защищает от **логических** конфликтов (оба сделали "правильно", но конфликтует бизнес-логика) |
| Работает через репликацию (read replica) | Retry-логика на клиенте/сервисе |
| Нет deadlocks | `version` — integer. При 4B инкрементов переполнение (не реально) |

### Когда подходит

- Конфликты **редкие** (< 1% операций).
- Операции **короткие** (< 100мс).
- Можно **повторить** операцию (idempotent).
- Разные пользователи редактируют **разные** заказы.

---

## Решение 2: Пессимистичная блокировка (Pessimistic Locking)

### Механизм

Перед чтением агрегата — `SELECT ... FOR UPDATE`. БД ставит **эксклюзивную блокировку** на строку. Второй процесс **ждёт**, пока первый `COMMIT`/`ROLLBACK`.

### В Doctrine

```php
// Репозиторий:
public function findByIdForUpdate(string $id): ?Order
{
    $em = $this->em;

    $order = $em->createQuery(
        'SELECT o FROM Order o WHERE o.id = :id'
    )
    ->setParameter('id', $id)
    ->setLockMode(LockMode::PESSIMISTIC_WRITE)   // ← FOR UPDATE
    ->getOneOrNullResult();

    return $order !== null ? $this->mapper->fromRecord($order) : null;
}
```

Генерируемый SQL:

```sql
SELECT o.* FROM orders o WHERE o.id = 'o-1' FOR UPDATE;
```

### Что происходит

```
Т1                                    Т2
─────────────────────────────────────   ─────────────────────────────────────
SELECT ... FOR UPDATE                   │
→ строка заблокирована                  │
                                        │ SELECT ... FOR UPDATE
$order->addLine()                       │ → ЖДЁТ (lock wait)
→ total=800                             │
UPDATE → 1 row ✓                        │
COMMIT → lock освобождён                │
                                        │ → строка прочитана (version=2)
                                        │ $order->cancel()
                                        │ → InvalidTransitionException
                                        │    (status='confirmed', а не 'draft')
                                        │    → 409
```

Т2 **не потерял данные**. Он получил **актуальное** состояние и **доменную ошибку** (не `StaleObjectStateException`, а `InvalidTransitionException`).

### Trade-offs

| Плюс | Минус |
|---|---|
| Всегда актуальное состояние. Нет lost update | **Блокировка**. Второй процесс ждёт (до `lock_timeout`) |
| Нет retry-цикла | **Deadlock**: Т1 ждёт lock Т2, Т2 ждёт lock Т1 |
| Простота — нет `version`-колонки | **Скажируемость**: N запросов к одной строке = N в очереди |
| | **Long transaction**: если PHP-процесс завис после `FOR UPDATE` → lock держится до `statement_timeout` |

### Защита от deadlock

```sql
-- postgresql.conf
lock_timeout = '5s'          -- если не получил lock за 5с → ошибка
statement_timeout = '30s'    -- если запрос идёт > 30с → kill
```

```php
// В сервисе: всегда COMMIT/ROLLBACK в finally
try {
    $order = $this->repo->findByIdForUpdate($id);
    $order->confirm();
    $this->repo->save($order);
    $em->flush();
} catch (\Throwable $e) {
    $em->rollback();
    throw $e;
}
```

### Когда подходит

- Конфликты **частые** (> 5% операций).
- Операция **не идемпотентна** (нельзя просто повторить).
- Критично получить **актуальное** состояние (остаток на складе, баланс).
- Ожидаемое время блокировки < 100мс.

---

## Решение 3: Архитектурное — один writer на агрегат

### Принцип

> **Один и только один процесс имеет право мутировать конкретный агрегат в конкретный момент.**

Это не "один сервер" и не "один PHP-процесс". Это **логическая гарантия**:

| Механизм | Пример |
|---|---|
| Partitioning по `orderId` | Все операции над `order-1` идут на **один** worker (consistent hashing) |
| Message queue с `consumer = 1` | `OrderEvents` в RabbitMQ с `exclusive consumer` |
| `SELECT FOR UPDATE` на корневой сущности | БД-блокировка (см. выше) |
| Feature flag / circuit breaker | В момент миграции — только один сервис пишет |

### Пример: consistent hashing в микросервисах

```
Request: POST /orders/o-1/lines
         ↓
API Gateway
         ↓
Consistent Hash (key = 'o-1')
         ↓
Worker #3  ← единственный, кто может мутировать order-1
```

Worker #3 обрабатывает **все** операции над `order-1` последовательно. Конфликта нет, потому что нет параллельности.

**Trade-off:** если Worker #3 упал — `order-1` недоступен до rebalance. Но конфликты **исключены** на уровне архитектуры.

### Когда подходит

- Высокий throughput (1000+ ops/sec на агрегат).
- Оптимистичная блокировка даёт > 10% retries.
- Пессимистичная блокировка даёт очереди > 1с.
- Микро-сервисная архитектура, где "один PHP-процесс" не гарантирован.

---

## Сравнение

| Критерий | Оптимистичная | Пессимистичная | Один writer |
|---|---|---|---|
| Блокировка | Нет | Да (row lock) | Нет (последовательно) |
| Lost update | Нет (409) | Нет (актуальное состояние) | Нет (нет параллельности) |
| Retry | Да (клиент/сервис) | Нет | Нет |
| Deadlock | Нет | Да | Нет |
| Скажируемость | Высокая | Низкая (очередь) | Высокая (N workers) |
| Сложность | Низкая | Средняя | Высокая |
| Инфраструктура | 0 | 0 | Hash ring / queue |
| Частота конфликтов | < 5% | 5–50% | Любая |
| Идемпотентность операции | Должна быть | Не требуется | Не требуется |

---

## Практическая рекомендация

Для **90% Symfony-приложений** (monolith, 2–4 web-воркера, 100–500 RPS):

```
1. #[ORM\Version] на корневой сущности агрегата.
2. В сервисе: catch StaleObjectStateException → 409.
3. На клиенте: показ "Обновите страницу и повторите".
4. lock_timeout = '5s' как страховка.
```

Для **высоконагруженных** (1000+ RPS, микро-сервисы, очереди):

```
1. Consistent hashing по aggregate ID.
2. Один consumer на aggregate.
3. Оптимистичная блокировка как fallback.
```

---

## Типовые ошибки

### Ошибка 1: `#[ORM\Version]` на дочерней сущности

```php
#[ORM\Entity]
class OrderLine
{
    #[ORM\Version]
    private int $version;   // ← БЕСПОЛЕЗНО

    // OrderLine не сохраняется независимо.
    // Она сохраняется через Order.
    // UPDATE order_lines SET ... WHERE id=? AND version=? — не сработает,
    // потому что OrderLine сохраняется как часть cascade от Order.
}
```

`#[ORM\Version]` — **только на корневой сущности** (`OrderRecord`). Дочерние (`OrderLineRecord`) управляются через cascade.

### Ошибка 2: `flush()` до завершения бизнес-логики

```php
// ПЛОХО:
$order = $repo->findById($id);
$order->addLine(...);
$em->flush();          // ← SQL выполнен, но транзакция ещё не COMIT-нута
$order->confirm();     // ← доменная логика ещё выполняется
$em->flush();
$em->commit();         // ← COMIT здесь

// Между первым flush() и commit() другой процесс может
// прочитать "промежуточное" состояние (READ COMMITTED)
// и принять решение на его основе.
```

**Правило:** `flush()` — внутри транзакции. `commit()` — после **всей** бизнес-логики. `flush()` ≠ `commit()`.

### Ошибка 3: Повторная загрузка после `StaleObjectStateException`

```php
// ПЛОХО:
try {
    $order->confirm();
    $this->repo->save($order);
    $em->flush();
} catch (StaleObjectStateException $e) {
    // Повторяем с тем же объектом!
    $order->confirm();       // ← статус уже 'confirmed' в памяти
    $this->repo->save($order);
    $em->flush();
}
```

Правильно: **откатить**, **перезагрузить**, **повторить**:

```php
// ХОРОШО:
try {
    $this->em->wrapInTransaction(function () use ($id): void {
        $order = $this->repo->findById($id);
        $order->confirm();
        $this->repo->save($order);
        $this->em->flush();
    });
} catch (StaleObjectStateException $e) {
    // Транзакция откатилась (wrapInTransaction)
    // Перезагружаем:
    $order = $this->repo->findById($id);  // ← свежие данные
    try {
        $order->confirm();  // ← может бросить InvalidTransitionException
    } catch (InvalidTransitionException $e2) {
        throw new ConflictException('Заказ уже изменён', previous: $e);
    }
    $this->em->wrapInTransaction(function () use ($order): void {
        $this->repo->save($order);
        $this->em->flush();
    });
}
```

### Ошибка 4: `SELECT FOR UPDATE` вне транзакции

```php
// ПЛОХО:
$order = $em->createQuery('SELECT o FROM Order o WHERE o.id = :id')
    ->setLockMode(LockMode::PESSIMISTIC_WRITE)
    ->getOneOrNullResult();

// ... 5 секунд бизнес-логики ...

$em->flush();
// Lock держался 5 секунд! Все другие запросы к этой строке ждали.
```

**Правило:** `FOR UPDATE` — внутри `wrapInTransaction`. Транзакция — как можно короче.

```php
// ХОРОШО:
$this->em->wrapInTransaction(function (): void {
    $order = $this->repo->findByIdForUpdate($id);
    $order->confirm();
    $this->repo->save($order);
    $this->em->flush();
});
// Lock освобождён. Делайте медленные операции (email, HTTP-вызов) ВНЕ транзакции.
```

### Ошибка 5: "А у нас один сервер, конфликтов не бывает"

Даже на **одном** PHP-сервере:

- 2 FPM-воркера = 2 процесса = 2 `EntityManager`.
- 1 FPM-воркер + 1 worker (Messenger) = 2 процесса.
- 1 процесс + `bin/console` (cron) = 2 процесса.
- 1 процесс + DBA (`psql`) = 2 процесса.

"Один сервер" ≠ "один процесс" ≠ "один writer". Конкурентность — свойство **системы**, не количество железа.

---

## Итог

```
┌─────────────────────────────────────────────────────────────────────┐
│  Вопрос: "Что произойдёт, если два процесса изменят один агрегат?"  │
│                                                                     │
│  Без защиты: lost update. Тихая потеря данных. Никто не знает.     │
│                                                                     │
│  Защита (выберите по частоте конфликтов):                           │
│                                                                     │
│  < 5%   →  #[ORM\Version] + StaleObjectStateException + 409        │
│  5–50%  →  FOR UPDATE + lock_timeout + короткая транзакция         │
│  > 50%  →  One-writer architecture (hashing / queue)               │
│                                                                     │
│  Всегда:                                                            │
│  • #[ORM\Version] на корневой сущности (не на дочерних)            │
│  • flush() внутри транзакции, commit() после всей логики           │
│  • FOR UPDATE — только внутри wrapInTransaction                    │
│  • lock_timeout = '5s' как страховка                                │
│  • Не доверяйте "одному серверу"                                    │
└─────────────────────────────────────────────────────────────────────┘
```

> ⏱ 45.7s · prompt 72067 tok · output 5314 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о доменных событиях в DDD-контексте: когда публиковать, как обрабатывать, какие проблемы решают и создают, и связь с транзакциями БД. Это продолжение длинной технической беседы на русском о PHP/Symfony/Doctrine/DDD.

Нужно структурировать ответ:
1. Что такое доменное событие (отличие от application event, от notification)
2. Когда публиковать (внутри агрегата, не в сервисе)
3. Как обрабатывать (synchronous vs asynchronous)
4. Какие проблемы решает
5. Какие проблемы создаёт
6. Связь с транзакциями БД — это КРИТИЧЕСКИЙ вопрос. Outbox pattern. "Dual write problem".

Давайте подумаю о структуре:

- Доменное событие — это факт, который произошёл в домене. Не команда, не запрос. "OrderWasConfirmed", не "ConfirmOrder".
- Публикация: внутри агрегата (метод `confirm()` вызывает `$this->recordEvent(new OrderConfirmed(...))`). Не в сервисе.
- Сборка: сервис собирает события после мутации.
- Публикация: сервис публикует ВНЕ транзакции БД (иначе — dual write problem).
- Обработка: синхронно (EventDispatcher Symfony) или асинхронно (Messenger).
- Outbox pattern: запись события в ту же транзакцию, что и мутация, в таблицу `outbox`. Worker читает из `outbox` и публикует в брокер. Убирает dual write.
- Проблемы: "событие упало, а мутация применилась" (или наоборот). Order of events. Idempotency.

Ключевой момент: связать с предыдущими ответами. У нас уже есть Order + Payment + Shipment + Invoice как отдельные агрегаты. Доменные события — это способ связать их без прямой зависимости.

Структура ответа:
1. Что такое доменное событие (отличить от команды, от application event)
2. Где генерируется (внутри агрегата)
3. Где публикуется (в сервисе, НЕ в агрегате)
4. Как обрабатывается (sync/async)
5. Связь с транзакциями — ДВОЙНАЯ ПРОБЛЕМА
6. Outbox pattern — решение
7. Проблемы, которые решает
8. Проблемы, которые создаёт
9. Практический пример на Order + Payment
10. Антипаттерны

Длина: средняя-длинная. Пользователь явно хочет глубину. Но не перегружать.

Начну с определения, потом практика, потом транзакции (самое важное), потом trade-offs.

Важно: пользователь спросил "как доменные события связаны с транзакциями базы данных?" — это ключевой вопрос, на который нужно ответить подробно. Dual write problem и Outbox pattern.

Формат: заголовки, таблицы, код. Как в предыдущих ответах.

</details>

**Кандидат:**

# Доменные события: публикация, обработка, транзакции

## 1. Что это и что это НЕ

| Термин | Значение | Пример |
|---|---|---|
| **Доменное событие** | Факт, который **произошёл** в домене. Прошедшее время. | `OrderWasConfirmed`, `PaymentWasRefunded` |
| **Команда** | Запрос на действие. Будущее время. | `ConfirmOrder`, `RefundPayment` |
| **Application event** | Техническое уведомление о завершении операции | `KernelEvents::RESPONSE` |

```php
// НЕ событие:
class ConfirmOrder { }          // ← команда

// Событие:
final readonly class OrderWasConfirmed
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public int    $totalCents,
        public \DateTimeImmutable $occurredAt,
    ) {}
}
```

**Ключевое свойство:** событие **неизменяемо** и **неотменяемо**. Заказ подтверждён — факт. Его нельзя «отменить подтверждение». Можно издать `OrderWasCancelled` — отдельное событие.

---

## 2. Где генерируется

Событие рождаётся **внутри агрегата**, в тот момент, когда инвариант соблюдён и состояние изменилось:

```php
final class Order
{
    /** @var list<object> */
    private array $domainEvents = [];

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status, OrderStatus::Confirmed);
        }
        if (count($this->lines) === 0) {
            throw new EmptyOrderException();
        }

        $this->status = OrderStatus::Confirmed;
        $this->confirmedAt = new \DateTimeImmutable();

        // Событие — часть мутации. Не "побочный эффект", а следствие.
        $this->recordEvent(new OrderWasConfirmed(
            orderId:     $this->id,
            customerId:  $this->customerId,
            totalCents:  $this->total->amountCents,
            occurredAt:  $this->confirmedAt,
        ));
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return list<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];   // очищаем: событие извлечено
        return $events;
    }
}
```

**Почему не в сервисе:**

```php
// ПЛОХО: сервис решает, что "подтверждение" = событие
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $order->confirm();
    $this->repo->save($order);

    // Событие здесь — значит, кто-то другой может
    // вызвать $order->confirm() и ЗАБЫТЬ опубликовать.
    $this->bus->dispatch(new OrderWasConfirmed(...));
}

// ХОРОШО: событие — следствие мутации. Не забудешь.
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $order->confirm();
    $this->repo->save($order);

    $events = $order->pullDomainEvents();  // гарантированно заполнено
    $this->bus->dispatchAll($events);
}
```

Агрегат **знает**, какое событие произошло, потому что **он сам** изменил состояние. Сервис **не знает** — он только вызывает метод.

---

## 3. Где публикуется

Публикация — **за сервисом**, потому что сервис знает, когда мутация **закончена**:

```
┌──────────────────────────────────────────────────────────────────┐
│  Transaction                                                     │
│                                                                  │
│  1. $order->confirm()          ← мутация (событие записано)     │
│  2. $this->repo->save($order)  ← persist                        │
│  3. $this->em->flush()         ← SQL: UPDATE orders             │
│  4. $this->outbox->append($order->pullDomainEvents())           │
│  5. $this->em->flush()         ← SQL: INSERT INTO outbox        │
│  6. COMMIT                                                            │
│                                                                  │
│  События ВНЕ транзакции:                                         │
│                                                                  │
│  7. Worker: SELECT FROM outbox WHERE published = false           │
│  8. Worker: publish to RabbitMQ / Redis Stream                   │
│  9. Worker: UPDATE outbox SET published = true                   │
│                                                                  │
│  10. Handler A: PaymentCreated → creates Payment                │
│  11. Handler B: SearchIndexUpdated → upserts in Elasticsearch   │
│  12. Handler C: NotifyCustomer → sends email                    │
└──────────────────────────────────────────────────────────────────┘
```

**Шаг 4** — ключевой. Разберём ниже.

---

## 4. Как обрабатывается

### 4.1 Синхронно (в том же процессе)

```php
// Symfony EventDispatcher
final class PaymentCreatedHandler
{
    #[AsEventListener(event: OrderWasConfirmed::class, method: 'handle')]
    public function handle(OrderWasConfirmed $event): void
    {
        $payment = Payment::createForOrder($event->orderId, $event->totalCents);
        $this->payments->save($payment);
        $this->em->flush();
    }
}
```

**Когда:** обработка должна быть **в той же транзакции**. Если Payment не создан — Order не должен быть подтверждён.

**Проблема:** это уже **не доменное событие**, а **внутренняя координация**. Вы создаёте скрытую зависимость: `OrderService` → `EventDispatcher` → `PaymentHandler` → `PaymentRepository`. Цепочка не видна из кода `OrderService`.

### 4.2 Асинхронно (отдельный процесс)

```php
// Symfony Messenger
final class PaymentCreatedHandler
{
    public function __construct(
        private PaymentRepository $payments,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(OrderWasConfirmed $event): void
    {
        $payment = Payment::createForOrder($event->orderId, $event->totalCents);
        $this->em->wrapInTransaction(function () use ($payment): void {
            $this->payments->save($payment);
            $this->em->flush();
        });
    }
}
```

```yaml
# messenger.yaml
routing:
    App\Domain\Order\Event\OrderWasConfirmed: async
```

**Когда:** обработка **не входит** в транзакцию мутации. Payment может быть создан через 2 секунды. Если создание упало — retry. Order уже подтверждён.

**Проблема:** между подтверждением и созданием Payment — **окно不一致ности**. Система в промежуточном состоянии: Order подтверждён, Payment ещё нет.

### 4.3 Выбор

| Критерий | Синхронно | Асинхронно |
|---|---|---|
| Атомарность с мутацией | ✅ | ❌ |
| Отказоустойчивость (retry) | ❌ | ✅ |
| Скорость ответа пользователю | Медленнее | Быстрее |
| Скрытая зависимость | Выше (EventDispatcher) | Ниже (явный handler) |
| Tестирование | Проще (один процесс) | Сложнее (мок bus) |
| Масштабируемость | Ограничена одним процессом | Горизонтально |

**Практическое правило:**

> Если обработка **обязана** произойти **атомарно** с мутацией — это **не событие**, а **часть операции**. Делайте синхронно или просто вызывайте метод.
>
> Если обработка **может** произойти позже, **может** повториться, **может** быть обработана разными системами — это **событие**. Асинхронно.

---

## 5. Связь с транзакциями: Dual Write Problem

Это **главная** проблема доменных событий. Разберём подробно.

### 5.1 Проблема

```php
// НАИВНЫЙ подхвд:
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $order->confirm();

    // ТРАНЗАКЦИЯ БД
    $this->em->wrapInTransaction(function () use ($order): void {
        $this->repo->save($order);
        $this->em->flush();
    });
    // ← COMIT. Order подтверждён в БД.

    // ПУБЛИКАЦИЯ СОБЫТИЯ
    $this->bus->dispatch(new OrderWasConfirmed(...));
    // → RabbitMQ / Redis
}
```

**Что может пойти не так:**

| Момент | Что упало | Состояние |
|---|---|---|
| После `COMMIT`, до `dispatch` | PHP-процесс (kill -9, OOM) | Order подтверждён. Событие **никому не отправлено**. Payment не создан. |
| После `dispatch`, до обработки | Worker упал | Order подтверждён. Событие в очереди. Payment не создан. **Retry спасает.** |
| До `COMMIT` | `flush()` (constraint) | Order НЕ подтверждён. Событие **не отправлено** (ещё не dispatch). **OK.** |

**Критический случай:** `COMMIT` прошёл, `dispatch` не произошёл. Событие **потеряно навсегда**. У нас нет механизма повторной отправки, потому что мы не знаем, что оно должно было быть отправлено.

Это **dual write problem**: две системы (БД + очередь), одна транзакция не обхватывает обе.

### 5.2 Решение: Transactional Outbox

**Идея:** записываем событие **в ту же транзакцию**, что и мутацию. В **ту же базу данных**. В таблицу `outbox`.

```sql
CREATE TABLE outbox (
    id          BIGSERIAL PRIMARY KEY,
    aggregate_id  VARCHAR(36) NOT NULL,
    aggregate_type VARCHAR(50) NOT NULL,   -- 'Order', 'Payment'
    event_type    VARCHAR(100) NOT NULL,   -- 'OrderWasConfirmed'
    payload       JSONB NOT NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),
    published_at  TIMESTAMPTZ,             -- NULL = ещё не опубликовано
    published_by  VARCHAR(100),            -- worker ID
    UNIQUE (aggregate_id, event_type, created_at)  -- идемпотентность
);

CREATE INDEX idx_outbox_unpublished
    ON outbox (created_at)
    WHERE published_at IS NULL;
```

**Код:**

```php
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $order->confirm();
    $events = $order->pullDomainEvents();

    // ОДНА транзакция: мутация + событие
    $this->em->wrapInTransaction(function () use ($order, $events): void {
        $this->repo->save($order);           // UPDATE orders
        $this->em->flush();

        foreach ($events as $event) {
            $this->outbox->append(
                aggregateId:   $order->id(),
                aggregateType: 'Order',
                eventType:     get_class($event),
                payload:       $this->serializer->normalize($event),
            );
        }
        $this->em->flush();                   // INSERT INTO outbox
    });
    // ← COMIT. И Order, и событие — в одной транзакции.
    //    Либо оба, либо ни одно.

    // ПУБЛИКАЦИЯ — асинхронная, отдельным worker'ом.
    // Здесь мы НИЧЕГО не делаем. Worker сам подберёт.
}
```

**Worker:**

```php
final class OutboxPublisher
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventPublisherInterface $publisher,   // RabbitMQ / Redis
        private LoggerInterface $logger,
    ) {}

    public function publish(int $batchSize = 100): int
    {
        $published = 0;

        while (true) {
            $rows = $this->em->createQuery(
                'SELECT o.id, o.aggregateType, o.eventType, o.payload
                 FROM OutboxRecord o
                 WHERE o.publishedAt IS NULL
                 ORDER BY o.createdAt ASC
                 LIMIT :batch'
            )
            ->setParameter('batch', $batchSize)
            ->getResult();

            if (count($rows) === 0) break;

            foreach ($rows as $row) {
                try {
                    $this->publisher->publish(
                        eventType: $row['eventType'],
                        payload:   json_decode($row['payload'], true),
                        headers: [
                            'aggregate-id'   => $row['aggregateId'],
                            'aggregate-type' => $row['aggregateType'],
                        ],
                    );

                    // Подтверждаем публикацию
                    $this->em->wrapInTransaction(function () use ($row): void {
                        $record = $this->em->find(OutboxRecord::class, $row['id']);
                        $record->markPublished();
                        $this->em->flush();
                    });

                    $published++;
                } catch (\Throwable $e) {
                    $this->logger->error('Outbox publish failed', [
                        'id'      => $row['id'],
                        'event'   => $row['eventType'],
                        'error'   => $e->getMessage(),
                    ]);
                    // НЕ подтверждаем. Worker повторит при следующем запуске.
                    // Останавливаемся, чтобы не «проскочить» событие.
                    break 2;
                }
            }
        }

        return $published;
    }
}
```

**Запуск:**

```bash
# Supervisor / systemd / Docker
php bin/console outbox:publish --loop --batch=100 --sleep=100
```

### 5.3 Почему это работает

| Сценарий | Что происходит |
|---|---|
| `COMMIT` прошёл, PHP упал | Событие **в БД**. Worker подберёт. **Не потеряно.** |
| Worker упал после `publish`, до `markPublished` | Событие **дублировано** в RabbitMQ. Handler **идемпотентен** → OK. |
| `flush()` (constraint) упало | `ROLLBACK`. И Order, и событие — отменены. **Консистентно.** |
| Worker запустился дважды (2 реплики) | Оба читают `published_at IS NULL`. Один успел первым → `markPublished`. Второй — `0 rows`. **Idempotent.** |

### 5.4 Trade-offs Outbox

| Плюс | Минус |
|---|---|
| Атомарность: событие не потеряно | Дополнительная таблица |
| Не требует 2PC | Worker — дополнительная инфраструктура |
| Работает с любой БД (MySQL, Postgres) | Задержка: событие обработано через 100мс–1с |
| Аудит: в `outbox` — полная история событий | `published_at IS NULL` — нужно периодически чистить |
| Реплицируется с БД (replication) | |

**Чего НЕ делает Outbox:**
- Не гарантирует **порядок** событий из разных агрегатов.
- Не гарантирует, что handler **успешно** обработал. Для этого — retry в RabbitMQ + dead-letter queue.
- Не заменяет **idempotency** handler'а.

---

## 6. Проблемы, которые решает

| Проблема | Как решает |
|---|---|
| **Жёсткая связность агрегатов** | Order не знает про Payment. Событие `OrderWasConfirmed` → Handler создаёт Payment. |
| **Побочные эффекты в мутации** | `$order->confirm()` не отправляет email, не обновляет индекс. Чистая мутация. |
| **Новые потребители** | Добавили `AuditLogHandler` на `OrderWasConfirmed` — не трогаем `OrderService`. OCP. |
| **Разные скорости** | Email — 2с. Elasticsearch — 50мс. Order — 10мс. Не блокируют друг друга. |
| **Наблюдаемость** | `outbox` — полный лог событий. Можно переиграть (replay). |
| **Тестирование** | `Order::confirm()` — чистый unit test. Handler — отдельный test. |

---

## 7. Проблемы, которые создаёт

| Проблема | Деталь |
|---|---|
| **Неконсистентное промежуточное состояние** | Order подтверждён, Payment не создан (окно 100мс–1с). UI может показать «подтверждён» без Payment. |
| **Порядок событий** | `OrderWasConfirmed` → `PaymentCreated` → `ShipmentCreated`. Если worker'ы параллельны — `ShipmentCreated` может прийти до `PaymentCreated`. |
| **Idempotency** | Дубль события в RabbitMQ → Handler создаёт **два** Payment. Нужен `UNIQUE (orderId)` в `payments`. |
| **Сложность отладки** | «Почему Payment не создан?» → Check outbox → Check worker log → Check RabbitMQ → Check handler log. 4 места. |
| **Replay** | `outbox` хранит события. Можно «переиграть» — но handler должен быть идемпотентным. |
| **Schema evolution** | `payload` — JSONB. Добавили поле в `OrderWasConfirmed` → старые события в outbox не имеют этого поля. Handler должен обрабатывать `null`. |
| **Граница ответственности** | Кто отвечает за доставку: сервис, worker, или handler? Из-за Outbox — worker. Но бизнес думает, что «я опубликовал, значит доставлено». |

---

## 8. Антипаттерны

### 8.1 Событие вместо метода

```php
// ПЛОХО:
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $this->bus->dispatch(new ConfirmOrderCommand($id));
    // ← "Команда" как "событие". Асинхронно.
    //    Пользователь получил 200, но Order может не быть подтверждён ещё 5 секунд.
}

// ХОРОШО:
public function confirm(string $id): void
{
    $order = $this->repo->findById($id);
    $order->confirm();
    $this->repo->save($order);
    $this->em->flush();
    // ← Пользователь получил 200, Order ТОЧНО подтверждён.
}
```

**Правило:** **Команды** — синхронные, с подтверждением. **События** — асинхронные, факт.

### 8.2 Событие в конструкторе

```php
// ПЛОХО:
public function __construct(...)
{
    // ...
    $this->bus->dispatch(new UserCreated($this->id));
}
```

Конструктор — не момент для побочных эффектов. Событие должно быть результатом **метода-мутации**.

### 8.3 Handler мутирует агрегат, который его породил

```php
// ПЛОХО:
#[AsEventListener(event: OrderWasConfirmed::class)]
public function handle(OrderWasConfirmed $event): void
{
    $order = $this->repo->findById($event->orderId);
    $order->setTotal($order->total() + 50);  // ← мутирует Source-агрегат!
    $this->repo->save($order);
}
```

Событие — **факт**. Handler **не может** отменить или изменить факт. Если нужно изменить Order — это **новая команда**, не реакция на событие.

### 8.4 Без Outbox

```php
// ПЛОХО:
$order->confirm();
$em->flush();
$em->commit();
$this->bus->dispatch(new OrderWasConfirmed(...));  // ← dual write
```

Без Outbox — потеря события при `kill -9` между `commit` и `dispatch`.

### 8.5 Событие на каждое изменение поля

```php
// ПЛОХО:
class Order
{
    public function setTotal(Money $total): void
    {
        $this->total = $total;
        $this->recordEvent(new OrderTotalChanged($this->id, $total));
    }

    public function setStatus(OrderStatus $s): void
    {
        $this->status = $s;
        $this->recordEvent(new OrderStatusChanged($this->id, $s));
    }
}
```

Событие — **бизнес-факт**, не «изменение поля». `OrderTotalChanged` — не факт. Факт: `OrderWasConfirmed`, `OrderWasShipped`, `PaymentWasRefunded`.

---

## 9. Практический пример: Order → Payment → Shipment

```
Пользователь: POST /orders/o-1/confirm
    │
    ▼
OrderService::confirm('o-1')
    │
    ├── 1. $order = repo->findById('o-1')
    ├── 2. $order->confirm()          ← мутация + event
    ├── 3. $events = $order->pullDomainEvents()
    │      → [OrderWasConfirmed]
    │
    ├── 4. wrapInTransaction:
    │      ├── repo->save($order)     ← UPDATE orders
    │      ├── outbox->append(...)    ← INSERT INTO outbox
    │      └── em->flush()            ← COMMIT
    │
    └── 5. return 200 OK

[Worker: outbox:publish]
    │
    ├── SELECT FROM outbox WHERE published_at IS NULL
    │   → (OrderWasConfirmed, {orderId: 'o-1', totalCents: 5000, ...})
    │
    ├── publisher->publish('OrderWasConfirmed', payload)
    │   → RabbitMQ / Redis Stream
    │
    └── UPDATE outbox SET published_at = now()

[Worker: messenger:consume]
    │
    ├── PaymentHandler::handle(OrderWasConfirmed)
    │   ├── Payment::createForOrder('o-1', Money(5000, 'USD'))
    │   ├── payments->save($payment)
    │   ├── em->flush()
    │   └── (опционально) outbox->append(PaymentWasCreated)
    │
    ├── SearchHandler::handle(OrderWasConfirmed)
    │   └── elasticsearch->upsert('orders', 'o-1', {...})
    │
    └── EmailHandler::handle(OrderWasConfirmed)
        └── mailer->send('Order confirmed', ...)
```

**Ключевое:** `OrderService` **не знает** про Payment, Elasticsearch, Email. Три разных handler'а, три разных процесса, три разных SLA. `OrderService` — одна транзакция, одна таблица, одна запись в outbox.

---

## 10. Итог

```
┌─────────────────────────────────────────────────────────────────────┐
│  Доменное событие — это ФАКТ. Прошедшее время. Неизменяемый.       │
│                                                                     │
│  Генерация:  внутри агрегата, в методе-мутации                     │
│  Публикация: в сервисе, ВНЕ мутации (Outbox или async)             │
│  Обработка:  асинхронный handler, идемпотентный                    │
│                                                                     │
│  Транзакции:                                                        │
│  • Мутация + запись в outbox — ОДНА транзакция                     │
│  • Публикация в брокер — ОТДЕЛЬНЫЙ процесс (worker)                │
│  • Обработка handler'ом — ОТДЕЛЬНАЯ транзакция                     │
│                                                                     │
│  Без Outbox: dual write → потеря события при crash                 │
│  С Outbox:   атомарность + асинхронность + наблюдаемость           │
│                                                                     │
│  Команды — синхронные. События — асинхронные.                      │
│  Событие не мутирует source-агрегат.                                │
│  Handler идемпотентен.                                              │
└─────────────────────────────────────────────────────────────────────┘
```

> ⏱ 55.1s · prompt 81599 tok · output 6500 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт три связанных вопроса по DDD:
1. Чем VO отличается от Entity
2. Как решать, к чему отнести концепцию
3. Что такое Ubiquitous Language и как влияет на код

Это концептуальные вопросы. Нужно дать чёткие, не размытые ответы. Пользователь уже глубоко погрузился в DDD (агрегаты, события, транзакции), так что можно говорить на уровне senior-разработчика.

Структурирую так:
1. VO vs Entity — чёткое различие через критерии, не через "описание"
2. Алгоритм принятия решения — конкретные вопросы, а не абстрактные принципы
3. Ubiquitous Language — что это на практике, как влияет на имя методов, классов, таблиц, событий

Важно: не повторяться с предыдущими ответами про VO в Doctrine (там был технический вопрос хранения). Здесь — концептуальный вопрос "что это такое в домене".

Примеры — конкретные, из домена заказа/оплаты, который уже используется в диалоге.

Формат: таблицы для сравнений, код для примеров, чек-листы для принятия решений. Без воды.

</details>

**Кандидат:**

# Value Object vs. Entity vs. Ubiquitous Language

## 1. Value Object и Entity: в чём разница

### Ошибка, которую совершают чаще всего

Говорят: «Entity — это сущность с ID, Value Object — без ID». И на этом останавливаются.

Это **недостаточно**. ID — это техническая деталь хранения. Разница — в **семантике** и **поведении**.

### Настоящее различие

| Критерий | Entity | Value Object |
|---|---|---|
| **Тождество** | Определено **внутренним** ID. `User(id: 1)` ≠ `User(id: 2)`, даже если все поля одинаковые | Определено **значением**. `Money(100, 'USD')` == `Money(100, 'USD')`. Всегда. |
| **Жизненный цикл** | Создаётся → изменяется → удаляется. Имеет историю | Создаётся → существует → заменяется. Не изменяется. Не имеет истории |
| **Изменения** | Мутация: `$user->changeEmail('new@x.com')` | Замена: `$order->setTotal(Money::fromCents(200, 'USD'))` — **новый объект** |
| **Ссылки** | Другая сущность **ссылается** на неё по ID | Другой объект **содержит** её как значение (композиция) |
| **Уникальность** | Уникальна в системе. Никто другой не имеет этот ID | Не уникальна. 1000 заказов могут иметь `Money(100, 'USD')` |
| **Вопрос «кто?»** | «**Который**?» (какой конкретный) | «**Какой**?» (какого значения) |
| **Удаляемость** | Да: `DELETE FROM users WHERE id = 1` | Нет: значение не «удаляется». Оно перестаёт использоваться |

### Конкретно: два `Money(100, 'USD')`

```php
$a = Money::fromCents(100, 'USD');
$b = Money::fromCents(100, 'USD');

$a === $b;   // false (разные объекты в памяти)
$a->equals($b);  // true (разное значение — нет, одно и то же значение)

// В БД:
// orders  total_cents=100  total_currency='USD'
// payments amount_cents=100 amount_currency='USD'
//
// Это ОДНО И ТО ЖЕ значение. Не две сущности.
// Никакой ID не нужен. Никакой «истории».
```

```php
// Entity:
$userA = $repo->find(1);
$userB = $repo->find(1);
$userA === $userB;  // true (identity map) или false (разные запросы)
// Но это ОДИН и тот же пользователь. Не «два одинаковых».
```

### Ключевой тест

> **Если я заменю один объект на другой с идентичными полями — система заметит?**

- Entity: **да**. `User(id: 1, email: 'a@x.com')` заменён на `User(id: 1, email: 'b@x.com')` — это **изменение**. Система ведёт учёт.
- Value Object: **нет**. `Money(100, 'USD')` заменён на `Money(100, 'USD')` — система **не заметит**. Значение не изменилось.

---

## 2. Как решить, к чему отнести концепцию

### Не спрашивайте «Entity или VO?»

Спрашивайте:

```
1. Есть ли у него тождество, которое важно для бизнеса?
2. Есть ли у него жизненный цикл, который бизнес отслеживает?
3. Могут ли два объекта с одинаковыми полями быть РАЗНЫМИ для бизнеса?
4. Ссылается ли на него другая сущность по ID?
5. Изменяется ли он, или заменяется?
```

### Разбор на примерах

#### Пример 1: `Address`

```php
final class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $zipCode,
        public readonly string $country,
    ) {}

    public function equals(self $other): bool
    {
        return $this->street   === $other->street
            && $this->city     === $other->city
            && $this->zipCode  === $other->zipCode
            && $this->country  === $other->country;
    }
}
```

**ВО.** Почему:

- 10 000 пользователей могут жить по адресу «ул. Ленина, 1, Москва, 101000, RU». Это **одно и то же значение**, не 10 000 сущностей.
- У адреса нет ID. Никто не говорит «адрес №4521».
- Нет жизненного цикла: адрес не «создан», не «изменён», не «удалён». Он **присвоен** пользователю.
- Заменяется целиком: `$user->changeAddress(new Address(...))`. Не `$user->address->changeCity(...)`.

#### Пример 2: `Product`

```php
final class Product
{
    private ProductId $id;
    private string $name;
    private Money $price;
    private string $sku;
    private bool $active;

    // ...
}
```

**Entity.** Почему:

- `Product(id: 'p-1', name: 'Coffee', price: Money(500, 'USD'))` и `Product(id: 'p-2', name: 'Coffee', price: Money(500, 'USD'))` — **разные** продукты. Один на складе A, другой на складе B. Даже если все поля одинаковые.
- У товара есть жизненный цикл: создан → активирован → снят с продажи → архивирован.
- Заказ **ссылается** на товар по ID. Не копирует значение.
- Товар **изменяется**: `$product->changePrice(Money(600, 'USD'))`.

**Но!** `Money` внутри `Product` — **VO**. `Product` — Entity. `Money` — VO. Разные уровни.

#### Пример 3: `OrderLine`

```php
final class OrderLine
{
    public function __construct(
        private ProductId $productId,   // ← ссылка на Entity
        private Money     $unitPrice,   // ← VO
        private int       $quantity,    // ← скаляр
    ) {}
}
```

**Спорный случай.** Разберём.

**Аргументы за Entity:**
- У `OrderLine` есть технический PK в БД (`BIGSERIAL`).
- Может быть изменена: `updateLineQuantity()`.
- У неё есть «жизнь»: создана → изменена → удалена.

**Аргументы за VO:**
- `OrderLine` **не существует** вне `Order`. Нет такого, как «заказ №5, а линия №3». Линия — часть заказа.
- У `OrderLine` нет **бизнес-ID**. `productId + quantity + unitPrice` полностью определяют её.
- Две `OrderLine` с одинаковыми `productId`, `unitPrice`, `quantity` в **разных** заказах — это **разные** линии (разный контекст). Но в **одном** заказе — это **одно и то же** (уникальный constraint).
- Изменяется через **корень**: `$order->updateLineQuantity($pid, 5)`. Не `$line->setQuantity(5)`.

**Вердикт:** в рамках агрегата `Order` — `OrderLine` ведёт себя как **VO** (управляется корнем, не имеет самостоятельного ID в домене). В БД — имеет технический PK. Это **компромисс**, и он нормальный.

**Практическое правило:** если сущность не может существовать вне агрегата и не имеет бизнес-ID — она **ведёт себя как VO внутри агрегата**, даже если в БД имеет PK.

#### Пример 4: `Payment`

```php
final class Payment
{
    private string $id;         // ← бизнес-ID, сгенерирован при создании
    private string $orderId;    // ← ссылка на Order
    private Money $amount;      // ← VO
    private PaymentStatus $status;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $paidAt;
    private ?string $externalRef;
}
```

**Entity.** Безоговорочно.

- У платежа есть бизнес-ID. `payment_123` — это конкретный платёж.
- У платежа есть lifecycle: `pending → paid → refunded`.
- Другая сущность (`Refund`) ссылается на `Payment` по ID.
- Платёж **не заменяется**. Он **изменяется** (статус меняется).
- Два платежа с одинаковым `amount` и `orderId` — **разные** платежи.

#### Пример 5: `Email`

```php
final class Email
{
    private function __construct(public readonly string $value) {}

    public static function of(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($value);
        }
        return new self(strtolower(trim($value)));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

**VO.** Почему:

- `Email('John@Example.com')` и `Email('john@example.com')` — **одно и то же значение** (после нормализации).
- У email нет ID. Нет lifecycle.
- `User` содержит `Email` как значение. Не ссылается на `Email` по ID.
- 10 000 пользователей с email `john@example.com` — **одно и то же значение**, не 10 000 сущностей.

### Алгоритм

```
Концепция X из домена
    │
    ├─ Есть ли у X бизнес-ID, который отслеживает бизнес?
    │   ├── ДА → Entity
    │   └── НЕТ → ↓
    │
    ├─ Есть ли у X жизненный цикл (создан → изменён → удалён)?
    │   ├── ДА → Entity
    │   └── НЕТ → ↓
    │
    ├─ Могут ли два X с одинаковыми полями быть РАЗНЫМИ для бизнеса?
    │   ├── ДА → Entity
    │   └── НЕТ → ↓
    │
    ├─ Ссылается ли другая сущность на X по ID?
    │   ├── ДА → Entity
    │   └── НЕТ → ↓
    │
    └─ VO
```

### Частые ошибки

| Ошибка | Пример | Почему плохо |
|---|---|---|
| Делать Entity из того, что VO | `EmailEntity` с ID, lifecycle, repository | Избыточная сложность. Email не имеет истории. |
| Делать VO из того, что Entity | `ProductVO` без ID, «просто данные» | Теряется тождество. Два одинаковых продукта — один или два? |
| Делать Entity из того, что не сущность | `OrderStatusEntity` с ID | Статус — enum или VO. Не сущность. |
| Смешивать уровни | `Product` — Entity, но `Money` внутри — Entity | `Money` — VO. Всегда. |

---

## 3. Ubiquitous Language: что это на практике

### Определение (не из книги)

Ubiquitous Language — это **совокупность имён, которые одинаково используются**:
- в разговоре команды,
- в коде (имена классов, методов, полей),
- в именах таблиц и колонок БД,
- в именах событий,
- в API-контрактах,
- в документации.

**Не** «терминологический словарь в Confluence». Это **имена в коде**, которые совпадают с тем, как бизнес говорит.

### Конкретный пример

#### Без Ubiquitous Language

```php
// Код:
final class OrderController
{
    public function doPayment($orderId, $amount, $method)
    {
        $order = $this->repo->find($orderId);
        $this->payment->process($order, $amount, $method);
        $this->notify->sendMail($order->getCustomer()->getEmail(), 'You paid!');
        $this->search->updateIndex($order);
    }
}
```

```sql
CREATE TABLE tbl_orders (
    id INT,
    cust_id INT,
    amt INT,
    cur VARCHAR(3),
    st VARCHAR(20),
    ts TIMESTAMP
);
```

```json
// API
{
    "order_id": 123,
    "pay_status": "ok",
    "cust_email": "john@x.com"
}
```

**Проблема:** разработчик смотрит на код и думает: «`doPayment` — это то же самое, что «подтвердить заказ» в домене?» Не уверен. `pay_status: "ok"` — это «оплачено», «подтверждено», «успешно»? Неясно. `amt` — это «итоговая сумма» или «сумма без скидок»? Неясно.

#### С Ubiquitous Language

Бизнес-команда говорит:

> «Когда клиент **подтверждает** заказ, система **фиксирует** оплату. Если оплата **отклонена**, заказ остаётся в статусе **черновик**. Если оплата **успешна**, заказ переходит в статус **подтверждён** и **отправляется** на **резервирование** склада.»

Код:

```php
final class OrderService
{
    public function confirmOrder(string $orderId): ConfirmedOrder
    {
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        // Доменное действие: подтверждение
        $order->confirm();

        // Сохранение
        $this->em->wrapInTransaction(function () use ($order): void {
            $this->orders->save($order);
            $this->em->flush();
        });

        // Побочные эффекты (асинхронно, через события)
        $events = $order->pullDomainEvents();
        // → [OrderWasConfirmed]

        return new ConfirmedOrder(
            orderId: $order->id(),
            total: $order->total(),
            confirmedAt: $order->confirmedAt(),
        );
    }
}
```

```php
final class Order
{
    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotInDraftException($this->status);
        }
        if (count($this->lines) === 0) {
            throw new EmptyOrderException($this->id);
        }

        $this->status = OrderStatus::Confirmed;
        $this->confirmedAt = new \DateTimeImmutable();

        $this->recordEvent(new OrderWasConfirmed(
            orderId: $this->id,
            total: $this->total,
            confirmedAt: $this->confirmedAt,
        ));
    }
}
```

```sql
-- Имена совпадают с доменным языком:
CREATE TABLE orders (
    id             VARCHAR(36) PRIMARY KEY,
    customer_id    VARCHAR(36) NOT NULL,
    status         order_status NOT NULL,
    total_cents    INT NOT NULL,
    total_currency CHAR(3) NOT NULL,
    confirmed_at   TIMESTAMPTZ,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TYPE order_status AS ENUM
    ('draft', 'confirmed', 'shipped', 'delivered', 'cancelled');
```

```json
// API
{
    "orderId": "o-123",
    "status": "confirmed",
    "total": { "amountCents": 5000, "currency": "USD" },
    "confirmedAt": "2025-01-15T10:30:00Z"
}
```

```php
// Событие
final readonly class OrderWasConfirmed
{
    public function __construct(
        public string $orderId,
        public Money  $total,
        public \DateTimeImmutable $confirmedAt,
    ) {}
}
```

### Что совпадает

| Доменное слово | Код | БД | API | Событие |
|---|---|---|---|---|
| Заказ | `Order` | `orders` | `orderId` | `OrderWasConfirmed` |
| Статус | `OrderStatus` | `order_status` | `status` | — |
| Подтверждён | `OrderStatus::Confirmed` | `'confirmed'` | `"confirmed"` | `OrderWasConfirmed` |
| Итоговая сумма | `Money` | `total_cents` | `total.amountCents` | `total` |
| Черновик | `OrderStatus::Draft` | `'draft'` | `"draft"` | — |
| Пустой заказ | `EmptyOrderException` | — | 422 | — |

### Как это влияет на код

#### 1. Имена методов = доменные действия

```php
// ПЛОХО:
$order->do();
$order->update();
$order->process();

// ХОРОШО:
$order->confirm();
$order->cancel();
$order->addLine();
$order->updateLineQuantity();
```

Каждый метод — **доменное действие**, которое можно произнести вслух: «Подтвердить заказ», «Отменить заказ», «Добавить позицию».

#### 2. Исключения = доменные нарушения

```php
// ПЛОХО:
throw new RuntimeException("Cannot confirm: status is not draft");
throw new Exception("Empty order");

// ХОРОШО:
throw new OrderNotInDraftException($this->status);
throw new EmptyOrderException($this->id);
```

Имя исключения — **доменное правило**: «Заказ не в статусе черновик», «Заказ пустой».

#### 3. События = доменные факты

```php
// ПЛОХО:
new OrderUpdated()
new PaymentProcessed()
new StatusChanged()

// ХОРОШО:
new OrderWasConfirmed()
new PaymentWasRefunded()
new OrderWasCancelled()
```

Прошедшее время. Факт. Не «обновлён» (что?), а «подтверждён» (конкретный факт).

#### 4. Имена в БД = доменные слова

```sql
-- ПЛОХО:
amt, cur, st, cust_id, ts

-- ХОРОШО:
total_cents, total_currency, status, customer_id, confirmed_at
```

DBA, который читает схему, **понимает**, что такое `total_cents`. Не `amt`.

#### 5. API = доменный контракт

```json
// ПЛОХО:
{ "id": 1, "st": "ok", "amt": 500 }

// ХОРОШО:
{
    "orderId": "o-123",
    "status": "confirmed",
    "total": { "amountCents": 5000, "currency": "USD" }
}
```

Frontend-разработчик читает `status: "confirmed"` и **понимает**, что заказ подтверждён. Не `st: "ok"`.

### Как внедрять Ubiquitous Language

#### Шаг 1: Собрать события (Event Storming)

Команда (разработчики + продакт + QA) берёт стикеры. Жёлтые — **события** (прошедшее время):

```
[Заказ создан]  [Заказ подтверждён]  [Оплата получена]
[Заказ отменён] [Заказ отгружен]    [Оплата возвращена]
```

Красные — **команды** (кто вызывает):

```
[Клиент] → [Подтвердить заказ]
[Админ] → [Отменить заказ]
[Система] → [Получить оплату]
```

Синие — **акторы** (кто/что):

```
[Клиент] [Админ] [Stripe] [Склад]
```

Серые — **правила** (инварианты):

```
[Заказ не может быть подтверждён, если пуст]
[Оплата не может быть возвращена больше, чем получена]
```

#### Шаг 2: Из событий — имена

```
Событие:        OrderWasConfirmed
Команда:        ConfirmOrder
Команда:        CancelOrder
Команда:        RefundPayment
Правило:        OrderMustNotBeEmpty
Правило:        RefundCannotExceedPayment
```

#### Шаг 3: Из имён — код

```php
final class Order          // ← «Заказ»
{
    public function confirm(): void { ... }  // ← «Подтвердить»
    public function cancel(): void { ... }   // ← «Отменить»
}

final class Payment        // ← «Оплата»
{
    public function refund(Money $amount): void { ... }  // ← «Возвратить»
}

// Исключения:
class OrderMustNotBeEmptyException extends DomainException
class RefundCannotExceedPaymentException extends DomainException

// События:
class OrderWasConfirmed
class PaymentWasRefunded
```

#### Шаг 4: Проверка

Откройте код. Прочитайте вслух. Звучит ли это как **домен**?

```php
// Прочитать вслух:
$order->confirm();
// «Заказ. Подтвердить.» ✓

$payment->refund($amount);
// «Оплата. Возвратить сумму.» ✓

throw new RefundCannotExceedPaymentException();
// «Возврат не может превышать оплату.» ✓

throw new RuntimeException("error: amt > paid");
// «Ошибка: amt больше paid.» ✗ (что такое amt? что такое paid?)
```

### Что ломается без Ubiquitous Language

| Проблема | Пример |
|---|---|
| Два разработчика называют одно и то же по-разному | `Order.totalCents` vs `Order.amount` vs `Order.sum`. Три имени, одно понятие. |
| Код нечитаемый для бизнес-аналитика | Аналитик смотрит на `$this->process($o, $a, $m)` и не понимает, что происходит. |
| API непоследователен | `/api/orders` возвращает `total`, а `/api/payments` — `amount`. Один и тот же концепт — два имени. |
| Миграция между сервисами | Сервис A пишет `OrderWasConfirmed`, сервис B ожидает `PaymentProcessed`. Не совпадает. |
| Тесты непрозрачные | `test_processOrderWithAmount500AndMethodStripe` — что такое «process»? Подтвердить? Оплатить? |

### Границы Ubiquitous Language

| Где вводится | Где не вводится |
|---|---|
| Доменные сущности: `Order`, `Payment` | Технические детали: `EntityManager`, `Connection` |
| Доменные действия: `confirm()`, `refund()` | Технические методы: `flush()`, `persist()` |
| Доменные исключения: `EmptyOrderException` | Технические: `StaleObjectStateException`, `PDOException` |
| Доменные события: `OrderWasConfirmed` | Технические: `KernelEvents::TERMINATE` |
| БД-колонки: `total_cents` | БД-колонки: `id`, `created_at` (универсальные) |
| API: `orderId`, `status` | API: `requestId`, `traceId` (технические) |

**Правило:** Ubiquitous Language — для **домена**. Не для инфраструктуры. `EntityManager` — не доменное слово. `OrderRepository` — доменное.

---

## 4. Связь трёх концепций

```
┌─────────────────────────────────────────────────────────────────────┐
│  UBICOUITOUS LANGUAGE                                               │
│  "Заказ" = Order = orders = orderId = OrderWasConfirmed            │
│                                                                     │
│  ┌───────────────────────────────────────────────────────────────┐ │
│  │  ENTITY: Order (заказ)                                        │ │
│  │  • ID: o-123                                                 │ │
│  │  • Lifecycle: draft → confirmed → shipped → delivered        │ │
│  │  • Содержит:                                                 │ │
│  │    ┌─────────────────────────────────────────────────────┐   │ │
│  │    │  VALUE OBJECT: Money (сумма)                        │   │ │
│  │    │  • Значение: 5000, USD                              │   │ │
│  │    │  • Нет ID, нет lifecycle                            │   │ │
│  │    │  • 1000 заказов могут иметь Money(5000, 'USD')     │   │ │
│  │    └─────────────────────────────────────────────────────┘   │ │
│  │    ┌─────────────────────────────────────────────────────┐   │ │
│  │    │  VALUE OBJECT: OrderStatus (статус)                 │   │ │
│  │    │  • Значение: 'confirmed'                            │   │ │
│  │    │  • Нет ID, нет lifecycle                            │   │ │
│  │    └─────────────────────────────────────────────────────┘   │ │
│  │    ┌─────────────────────────────────────────────────────┐   │ │
│  │    │  ENTITY (дочерняя): OrderLine (позиция)             │   │ │
│  │    │  • Бизнес-ID: нет (в рамках Order)                  │   │ │
│  │    │  • Ведёт себя как VO внутри агрегата                │   │ │
│  │    │  • Содержит: Money (VO), ProductId (ссылка)         │   │ │
│  │    └─────────────────────────────────────────────────────┘   │ │
│  └───────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Связь: Order → Payment (через orderId, не через объект)           │
│  Payment — отдельная Entity с собственным ID и lifecycle            │
└─────────────────────────────────────────────────────────────────────┘
```

**Ubiquitous Language связывает всё воедино:** имя `Order` в коде, `orders` в БД, `orderId` в API, `OrderWasConfirmed` в событии — это **одна и та же концепция**, названная одинаково на всех уровнях. Entity и VO — это **типы** этой концепции, а не сами концепции.

---

## 5. Коротко

| Вопрос | Ответ |
|---|---|
| Entity или VO? | Есть ли бизнес-ID и lifecycle? → Entity. Нет? → VO. |
| Как назвать? | Так, как говорит бизнес. `confirm()`, не `process()`. `OrderWasConfirmed`, не `OrderUpdated`. |
| Что такое Ubiquitous Language? | Одинаковые имена в коде, БД, API, событиях — для одной и той же доменной концепции. |
| Где заканчивается Ubiquitous Language? | На границе домена и инфраструктуры. `EntityManager` — не доменное слово. |
| Что ломается без UL? | Двое разработчиков называют `totalCents` по-разному. Аналитик не читает код. API непоследователен. |

> ⏱ 57.9s · prompt 87481 tok · output 6718 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

The user asks about the structure of a typical PHP application following Clean Architecture principles. They want to know:
1. What layers exist and what goes in each
2. The dependency rule between layers
3. Where Doctrine, Symfony controllers, and queue handlers land
4. How to enforce that layer boundaries aren't violated

This is a continuation of a long conversation about PHP, Symfony, DDD, Doctrine, and architecture. I should be consistent with everything I've said before (the Order/OrderLine/Payment examples, the domain events discussion, the aggregate boundaries, etc.).

Let me structure this clearly:

1. The layers (from inside out): Domain → Application → Infrastructure → Interface (presentation)
2. What goes in each layer
3. The dependency rule (dependencies point inward)
4. Where specific technologies land
5. How to enforce boundaries (tools, practices)

I should use concrete PHP/Symfony examples consistent with the rest of the conversation. Let me think about the practical structure:

- Domain layer: Pure PHP, no framework dependencies. Entities, Value Objects, Domain Services, Domain Events, Repository interfaces, Gateways/Ports interfaces.
- Application layer: Use cases / Application Services. Orchestrates domain objects. Knows about ports (interfaces) but not adapters (implementations).
- Infrastructure layer: Doctrine entities, repositories (implementations), message queue adapters, email adapters, external API clients.
- Interface/Presentation layer: Symfony controllers, request handlers, form handlers, API controllers, console commands.

The dependency rule: Inner layers don't depend on outer layers. Outer layers depend on inner layers. Domain knows nothing. Application knows about Domain but not Infrastructure. Infrastructure implements interfaces defined in Domain/Application.

For Symfony specifically:
- Controllers → Application services → Domain
- Doctrine adapters → implement Domain repository interfaces
- Messenger handlers → Application services
- Symfony services.yaml wires it all together (composition root)

Enforcement:
- Architecture testing (e.g., using PHPStan custom rules, or a simple test that checks `use` statements)
- PHPStan with custom rules
- Physical directory structure (but not sufficient alone)
- Code review
- CI checks
- The "ports and adapters" pattern means interfaces are in inner layers, implementations in outer

Let me write a thorough, practical answer with code examples.

</details>

**Кандидат:**

# Чистая архитектура в PHP/Symfony: слои, зависимости, enforcement

## 1. Четыре слоя и правило зависимостей

```
┌─────────────────────────────────────────────────────────────────────────┐
│  INTERFACE (Presentation)                                              │
│  Controllers, Commands, Form Handlers, API                            │
│                                                                        │
│  ┌───────────────────────────────────────────────────────────────────┐ │
│  │  APPLICATION (Use Cases)                                          │ │
│  │  OrderService, PaymentService, RegistrationService               │ │
│  │                                                                   │ │
│  │  ┌─────────────────────────────────────────────────────────────┐ │ │
│  │  │  DOMAIN                                                    │ │ │
│  │  │  Order, OrderLine, Money, OrderStatus                      │ │ │
│  │  │  OrderRepository (interface), PaymentGateway (interface)   │ │ │
│  │  │  OrderWasConfirmed, PaymentWasRefunded                     │ │ │
│  │  │  DomainServices, Exceptions                                │ │ │
│  │  └─────────────────────────────────────────────────────────────┘ │ │
│  └───────────────────────────────────────────────────────────────────┘ │
│                                                                        │
│  ┌───────────────────────────────────────────────────────────────────┐ │
│  │  INFRASTRUCTURE (Adapters)                                       │ │
│  │  Doctrine entities, OrderRepository (impl), StripeClient         │ │
│  │  RabbitMQPublisher, ElasticsearchIndex, MailerAdapter            │ │
│  └───────────────────────────────────────────────────────────────────┘ │
│                                                                        │
└─────────────────────────────────────────────────────────────────────────┘

Правило: стрелки указывают ВНИЗ (внутрь).
Никогда не изнутри наружу.
```

**Правило зависимостей:**

> Внутренний слой **не знает** о внешнем. Внешний слой **знает** о внутреннем через интерфейсы, определённые во внутреннем слое.

| Внутренний слой | Знает о | НЕ знает о |
|---|---|---|
| Domain | Ничего. Чистый PHP | Symfony, Doctrine, RabbitMQ, Twig |
| Application | Domain (сущности, интерфейсы) | Doctrine, Symfony HTTP, конкретные адаптеры |
| Infrastructure | Domain (интерфейсы, которые реализует) | Application, Interface |
| Interface (Presentation) | Application (сервисы) | Domain напрямую (опционально, для типов) |

---

## 2. Что лежит в каждом слое

### 2.1 Domain (`src/Domain/`)

**Чистый PHP. Ноль зависимостей от фреймворков.**

```
src/Domain/
├── Order/
│   ├── Order.php                  ← Агрегат (корень)
│   ├── OrderLine.php              ← Дочерняя сущность
│   ├── OrderStatus.php            ← Enum
│   ├── OrderRepository.php        ← Интерфейс (порт)
│   ├── OrderWasConfirmed.php      ← Доменное событие
│   ├── OrderWasCancelled.php
│   ├── EmptyOrderException.php    ← Доменное исключение
│   ├── InvalidTransitionException.php
│   └── ProductId.php              ← Value Object
├── Payment/
│   ├── Payment.php
│   ├── PaymentStatus.php
│   ├── PaymentRepository.php      ← Интерфейс
│   ├── PaymentWasCreated.php
│   ├── PaymentWasRefunded.php
│   ├── PaymentGateway.php         ← Интерфейс (порт к внешнему миру)
│   ├── PaymentDeclinedException.php
│   └── GatewayCommunicationException.php
├── Common/
│   ├── Money.php                  ← Value Object
│   ├── AggregateRoot.php          ← Abstract base (опционально)
│   ├── DomainEvent.php            ← Маркерный интерфейс
│   └── DomainException.php        ← Базовое исключение
```

**Пример: `OrderRepository` — интерфейс в домене:**

```php
// src/Domain/Order/OrderRepository.php
namespace App\Domain\Order;

interface OrderRepository
{
    public function save(Order $order): void;
    public function findById(string $id): ?Order;
    public function findByCustomer(string $customerId): array;
    public function findActive(int $limit, int $offset): array;
}
```

Домен **не знает**, что это Doctrine. Не знает, что за БД. Не знает, что за транспорт. Только контракт.

**Пример: `PaymentGateway` — порт к внешнему миру:**

```php
// src/Domain/Payment/PaymentGateway.php
namespace App\Domain\Payment;

interface PaymentGateway
{
    public function charge(Order $order, string $source): string;  // возвращает externalRef
    public function refund(string $externalRef, Money $amount, string $reason): void;
}
```

Домен говорит: «мне нужен кто-то, кто умеет `charge` и `refund`». **Кто** — не его дело.

**Пример: `Order` — агрегат без единого `use Symfony` или `use Doctrine`:**

```php
// src/Domain/Order/Order.php
namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Common\DomainEvent;

final class Order
{
    private string $id;
    private string $customerId;
    private OrderStatus $status;
    private Money $total;
    private ?\DateTimeImmutable $confirmedAt = null;
    /** @var OrderLine[] */
    private array $lines = [];
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    public function __construct(string $customerId)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->customerId = $customerId;
        $this->status = OrderStatus::Draft;
        $this->total = Money::zero('USD');
    }

    public function addLine(ProductId $pid, Money $price, int $qty): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new OrderNotInDraftException($this->status);
        }
        if ($qty <= 0) {
            throw new InvalidQuantityException($qty);
        }
        $this->lines[] = new OrderLine($pid, $price, $qty);
        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException($this->status, OrderStatus::Confirmed);
        }
        if (count($this->lines) === 0) {
            throw new EmptyOrderException($this->id);
        }
        $this->status = OrderStatus::Confirmed;
        $this->confirmedAt = new \DateTimeImmutable();
        $this->recordEvent(new OrderWasConfirmed(
            orderId: $this->id,
            customerId: $this->customerId,
            total: $this->total,
            confirmedAt: $this->confirmedAt,
        ));
    }

    public function cancel(): void
    {
        if (in_array($this->status, [OrderStatus::Shipped, OrderStatus::Delivered])) {
            throw new InvalidTransitionException(
                $this->status, OrderStatus::Cancelled,
                "Cannot cancel a {$this->status->name} order"
            );
        }
        $this->status = OrderStatus::Cancelled;
        $this->recordEvent(new OrderWasCancelled(
            orderId: $this->id,
            cancelledAt: new \DateTimeImmutable(),
        ));
    }

    private function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->lines as $line) {
            $sum += $line->unitPrice()->amountCents() * $line->quantity();
        }
        $this->total = Money::fromCents($sum, 'USD');
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return list<DomainEvent> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    // Геттеры...
}
```

**Ни одного `use Doctrine`. Ни одного `use Symfony`. Чистый PHP + PSR-интерфейсы (если нужны).**

---

### 2.2 Application (`src/Application/`)

**Оркестрация. Знает о Domain. Не знает о конкретных адаптерах.**

```
src/Application/
├── Order/
│   ├── OrderService.php           ← Use case: создать, подтвердить, отменить
│   ├── CreateOrderUseCase.php     ← (альтернатива: отдельный класс на use case)
│   └── ConfirmOrderUseCase.php
├── Payment/
│   ├── PaymentService.php
│   └── RefundPaymentUseCase.php
├── Event/
│   ├── DomainEventPublisher.php   ← Интеграция: Domain Events → Outbox
│   └── OutboxRepository.php       ← Интерфейс (порт)
└── Common/
    ├── UnitOfWork.php             ← Интерфейс (порт)
    └── MoneyFormatter.php
```

**Пример: `OrderService` — use case:**

```php
// src/Application/Order/OrderService.php
namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository;
use App\Domain\Order\ProductId;
use App\Domain\Common\Money;
use App\Application\Common\UnitOfWork;

final class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private UnitOfWork      $uow,
    ) {}

    public function confirmOrder(string $orderId): Order
    {
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        $order->confirm();  // ← доменная логика, доменные события

        $events = $order->pullDomainEvents();

        $this->uow->run(function () use ($order, $events): void {
            $this->orders->save($order);
            foreach ($events as $event) {
                // Outbox — порт, реализация в Infrastructure
                // Application не знает, что это таблица или RabbitMQ
            }
        });

        return $order;
    }

    public function addLine(string $orderId, string $productId, int $priceCents, int $qty): Order
    {
        $order = $this->orders->findById($orderId)
            ?? throw new OrderNotFoundException($orderId);

        $order->addLine(ProductId::of($productId), Money::fromCents($priceCents, 'USD'), $qty);

        $this->uow->run(function () use ($order): void {
            $this->orders->save($order);
        });

        return $order;
    }
}
```

**Что Application знает:**
- Domain: `Order`, `OrderRepository`, `Money`, `ProductId`, доменные события.
- Свои порты: `UnitOfWork`, `OutboxRepository`.

**Что Application НЕ знает:**
- `EntityManager`, `Connection`, `Query`.
- `RabbitMQ`, `Redis`, `Symfony Messenger`.
- `Request`, `Response`, `Route`.
- Конкретные классы адаптеров.

---

### 2.3 Infrastructure (`src/Infrastructure/`)

**Адаптеры. Реализуют порты из Domain/Application. Зависят от внешних технологий.**

```
src/Infrastructure/
├── Persistence/
│   ├── Doctrine/
│   │   ├── Entity/
│   │   │   ├── OrderRecord.php          ← ORM-сущность (аннотации #[ORM])
│   │   │   ├── OrderLineRecord.php
│   │   │   ├── PaymentRecord.php
│   │   │   └── OutboxRecord.php
│   │   ├── Repository/
│   │   │   ├── DoctrineOrderRepository.php   ← реализует OrderRepository
│   │   │   ├── DoctrinePaymentRepository.php ← реализует PaymentRepository
│   │   │   └── DoctrineOutboxRepository.php  ← реализует OutboxRepository
│   │   ├── Mapper/
│   │   │   ├── OrderMapper.php           ← Order ↔ OrderRecord
│   │   │   └── PaymentMapper.php         ← Payment ↔ PaymentRecord
│   │   └── Type/
│   │       ├── MoneyType.php             ← кастомный Doctrine Type
│   │       └── OrderStatusType.php
│   └── UnitOfWork/
│       └── DoctrineUnitOfWork.php        ← реализует UnitOfWork
├── Messaging/
│   ├── Outbox/
│   │   ├── OutboxPublisher.php           ← worker: outbox → RabbitMQ
│   │   └── RabbitMQPublisher.php         ← адаптер к RabbitMQ
│   └── Handler/
│       ├── CreatePaymentHandler.php      ← слушает OrderWasConfirmed
│       ├── UpdateSearchIndexHandler.php
│       └── SendConfirmationEmailHandler.php
├── Integration/
│   ├── Stripe/
│   │   ├── StripePaymentGateway.php      ← реализует PaymentGateway
│   │   └── StripeClient.php
│   └── Elasticsearch/
│       └── ElasticsearchIndex.php
└── Notification/
    └── MailerAdapter.php                 ← реализует Notifier (порт)
```

**Пример: `DoctrineOrderRepository` — адаптер:**

```php
// src/Infrastructure/Persistence/Doctrine/Repository/DoctrineOrderRepository.php
namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository;
use App\Infrastructure\Persistence\Doctrine\Entity\OrderRecord;
use App\Infrastructure\Persistence\Doctrine\Mapper\OrderMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderMapper            $mapper,
    ) {}

    public function save(Order $order): void
    {
        $record = $this->mapper->toRecord($order);
        $this->em->persist($record);
    }

    public function findById(string $id): ?Order
    {
        $record = $this->em->getRepository(OrderRecord::class)->find($id);
        return $record !== null ? $this->mapper->fromRecord($record) : null;
    }
}
```

**Что Infrastructure знает:**
- Domain: `Order`, `OrderRepository` (интерфейс, который реализует).
- Technology: `EntityManager`, `OrderRecord` (аннотации), `MoneyType`.

**Что Infrastructure НЕ знает:**
- Application: `OrderService`.
- Interface: `Controller`.

---

### 2.4 Interface / Presentation (`src/Interface/`)

**Тонкий слой. Принимает внешний ввод, делегирует в Application. Форматирует вывод.**

```
src/Interface/
├── Http/
│   ├── Controller/
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   └── AuthController.php
│   ├── Request/
│   │   ├── ConfirmOrderRequest.php      ← DTO: валидация входных данных
│   │   └── CreateOrderRequest.php
│   ├── Response/
│   │   ├── OrderResponse.php            ← DTO: формат выходных данных
│   │   └── PaymentResponse.php
│   └── Transformer/
│       ├── OrderToResponseTransformer.php
│       └── PaymentToResponseTransformer.php
├── Console/
│   └── Command/
│       ├── OrderImportCommand.php
│       └── OutboxPublishCommand.php
└── Worker/
    ├── OutboxPublishWorker.php
    └── EventHandlerWorker.php
```

**Пример: `OrderController` — тонкий:**

```php
// src/Interface/Http/Controller/OrderController.php
namespace App\Interface\Http\Controller;

use App\Application\Order\OrderService;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Order\EmptyOrderException;
use App\Domain\Order\OrderNotInDraftException;
use App\Interface\Http\Request\ConfirmOrderRequest;
use App\Interface\Http\Response\OrderResponse;
use App\Interface\Http\Transformer\OrderToResponseTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private OrderService                 $orderService,
        private OrderToResponseTransformer   $transformer,
    ) {}

    #[Route('/orders/{id}/confirm', methods: ['POST'], name: 'order_confirm')]
    public function confirm(string $id): JsonResponse
    {
        try {
            $order = $this->orderService->confirmOrder($id);
        } catch (OrderNotFoundException $e) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        } catch (OrderNotInDraftException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        } catch (EmptyOrderException $e) {
            return new JsonResponse(['error' => 'Order is empty'], 422);
        }

        return new JsonResponse($this->transformer->transform($order), 200);
    }
}
```

**Что Interface знает:**
- Application: `OrderService`.
- Domain: доменные исключения (для маппинга на HTTP-коды).
- Symfony: `AbstractController`, `Route`, `Request`, `Response`.

**Что Interface НЕ знает:**
- `EntityManager`, `OrderRecord`, `OrderMapper`.
- `RabbitMQ`, `Elasticsearch`, `Stripe`.
- Внутренности `OrderService`.

---

## 3. Куда попадают конкретные технологии

| Технология | Слой | Роль |
|---|---|---|
| **Doctrine ORM** (`EntityManager`, `#[ORM]`) | Infrastructure | Адаптер персистентности. Реализует `OrderRepository` |
| **Doctrine DBAL** (`Connection`) | Infrastructure | Низкий уровень SQL. Используется внутри адаптера |
| **Symfony Controller** (`AbstractController`) | Interface | Точка входа HTTP. Делегирует в Application |
| **Symfony Messenger** (transport, handler) | Infrastructure | Адаптер очередей. Реализует `OutboxRepository` или `EventPublisher` |
| **Symfony Security** (Authenticator, Voter) | Interface + Domain | `Authenticator` — Interface. `Voter` — Application (знает о Domain) |
| **Symfony Twig** (шаблоны) | Interface | Рендеринг. Не знает о Domain |
| **RabbitMQ / Redis** | Infrastructure | Транспорт событий. Адаптер |
| **Stripe / Adyen** | Infrastructure | Внешний API. Реализует `PaymentGateway` |
| **Elasticsearch** | Infrastructure | Поисковый индекс. Адаптер |
| **Monolog** | Infrastructure (или cross-cutting) | Логирование. Инжектится везде, но **не определяет логику** |
| **PHPStan / Psalm** | CI (внешний) | Статический анализ. Проверяет границы |
| **PHPUnit / Pest** | Внешний | Тесты. Мокают порты, тестируют Domain и Application изолированно |

### Ключевой принцип

> **Технология = адаптер. Адаптер = реализация порта. Порт определён во внутреннем слое.**

```
Domain:     interface PaymentGateway { charge(); refund(); }
                    ↑ реализует
Infrastructure: class StripePaymentGateway implements PaymentGateway { … }
                    ↑ реализует
Infrastructure: class AdyenPaymentGateway implements PaymentGateway { … }
```

Домен говорит «нужен `charge()`». Не «нужен Stripe». Не «нужен Adyen». Замена Stripe на Adyen = новый файл в Infrastructure + одна строка в `services.yaml`. **Ни одна** строка в Domain или Application не меняется.

---

## 4. Composition Root: где всё собирается

Symfony `services.yaml` — **единственное место**, где внешний мир знает о внешнем мире:

```yaml
# config/services.yaml
services:
    # ── Domain: без конфигурации ──
    # (чистые классы, не регистрируются)

    # ── Application ──────────────────────────────────────────
    App\Application\Order\OrderService:
        arguments:
            $orders: '@App\Domain\Order\OrderRepository'  ← интерфейс
            $uow:    '@App\Application\Common\UnitOfWork' ← интерфейс

    # ── Infrastructure: адаптеры ─────────────────────────────
    App\Infrastructure\Persistence\Doctrine\Repository\DoctrineOrderRepository:
        arguments:
            $em:     '@doctrine.orm.entity_manager'
            $mapper: '@App\Infrastructure\Persistence\Doctrine\Mapper\OrderMapper'
        # Реализация интерфейса:
        tags:
            - { name: app.repository, interface: 'App\Domain\Order\OrderRepository' }

    App\Infrastructure\Messaging\Outbox\RabbitMQPublisher:
        arguments:
            $connection: '@messenger.transport.async'

    App\Infrastructure\Integration\Stripe\StripePaymentGateway:
        arguments:
            $apiKey: '%env(STRIPE_API_KEY)%'
        # Реализация интерфейса:
        tags:
            - { name: app.gateway, interface: 'App\Domain\Payment\PaymentGateway' }

    # ── Interface ────────────────────────────────────────────
    App\Interface\Http\Controller\OrderController:
        arguments:
            $orderService: '@App\Application\Order\OrderService'
            $transformer:  '@App\Interface\Http\Transformer\OrderToResponseTransformer'
```

**Важно:** в `services.yaml` мы **связываем** интерфейсы с реализациями. Через `autowire` + `alias`:

```yaml
    # Алиас: интерфейс → конкретная реализация
    App\Domain\Order\OrderRepository: '@App\Infrastructure\Persistence\Doctrine\Repository\DoctrineOrderRepository'
    App\Domain\Payment\PaymentGateway: '@App\Infrastructure\Integration\Stripe\StripePaymentGateway'
```

Это и есть **inversion of control**: Domain определяет интерфейс, Infrastructure реализует, Composition Root связывает.

---

## 5. Как следить, что границы не нарушены

### 5.1 Физическая структура + правило импорта

Самый простой способ: **запретить `use` наружу**.

```
src/Domain/          → может импортировать: App\Domain\*, App\Common\*, PSR-*
src/Application/     → может импортировать: App\Domain\*, App\Application\*, PSR-*
src/Infrastructure/ → может импортировать: App\Domain\*, App\Application\*, Doctrine\*, Symfony\Component\Messenger\*, Stripe\*
src/Interface/       → может импортировать: App\Application\*, App\Domain\*, Symfony\Bundle\FrameworkBundle\*
```

**Запрещено:**

| Кто | Не может импортировать |
|---|---|
| Domain | `Doctrine\*`, `Symfony\*`, `Stripe\*`, `App\Infrastructure\*`, `App\Application\*` |
| Application | `Doctrine\*`, `Symfony\Bundle\*`, `App\Infrastructure\*` |
| Infrastructure | `App\Interface\*` |
| Interface | `App\Infrastructure\*` (прямой импорт конкретного адаптера) |

### 5.2 PHPStan custom rule

```php
// tests/Architecture/DomainLayerPurityRule.php

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PhpParser\Node\Stmt\Use_;

/**
 * @implements Rule<Use_>
 */
final class DomainLayerPurityRule implements Rule
{
    /** @var string[] */
    private array $forbiddenPrefixes = [
        'Doctrine\\',
        'Symfony\\Bundle\\',
        'Symfony\\Component\\Messenger',
        'Stripe\\',
        'App\\Infrastructure\\',
        'App\\Application\\',
        'App\\Interface\\',
    ];

    public function getNodeTypes(): array
    {
        return [Use_::class];
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // Проверяем только файлы в src/Domain/
        $file = $scope->getFile();
        if (str_contains($file, '/Domain/')) {
            foreach ($this->forbiddenPrefixes as $prefix) {
                if (str_starts_with($node->name->toString(), $prefix)) {
                    return [
                        sprintf(
                            'Domain layer imports forbidden dependency: %s. '
                            . 'Use a port (interface) instead.',
                            $node->name->toString()
                        )
                    ];
                }
            }
        }

        return [];
    }
}
```

```yaml
# phpstan.neon
includes:
    - tests/Architecture/DomainLayerPurityRule.neon

rules:
    - Tests\Architecture\DomainLayerPurityRule
    # + аналогичные для Application, Interface
```

### 5.3 Архитектурный тест (интеграционный)

```php
// tests/Architecture/LayerDependencyTest.php

final class LayerDependencyTest extends TestCase
{
    public function test_domain_does_not_import_infrastructure(): void
    {
        $violations = $this->findForbiddenImports(
            srcDir: __DIR__ . '/../../src/Domain',
            forbidden: [
                'Doctrine\\',
                'Symfony\\',
                'App\\Infrastructure\\',
                'App\\Application\\',
            ],
        );

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    public function test_application_does_not_import_infrastructure(): void
    {
        $violations = $this->findForbiddenImports(
            srcDir: __DIR__ . '/../../src/Application',
            forbidden: [
                'Doctrine\\',
                'App\\Infrastructure\\',
            ],
        );

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    public function test_interface_does_not_import_infrastructure(): void
    {
        $violations = $this->findForbiddenImports(
            srcDir: __DIR__ . '/../../src/Interface',
            forbidden: [
                'App\\Infrastructure\\',
            ],
        );

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    private function findForbiddenImports(string $srcDir, array $forbidden): array
    {
        $violations = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($srcDir)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') continue;

            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (!preg_match('/^use\s+(.+);/', $line, $m)) continue;

                $import = $m[1];
                foreach ($forbidden as $prefix) {
                    if (str_starts_with($import, $prefix)) {
                        $violations[] = sprintf(
                            '%s: forbidden import "%s"',
                            $file->getPathname(),
                            $import
                        );
                    }
                }
            }
        }

        return $violations;
    }
}
```

**Запуск в CI:**

```yaml
# .github/workflows/ci.yml
- name: Architecture test
  run: vendor/bin/phpunit tests/Architecture/

- name: PHPStan
  run: vendor/bin/phpstan analyse --level=8
```

### 5.4 `composer.json` + autoloading

```json
{
    "autoload": {
        "psr-4": {
            "App\\Domain\\":           "src/Domain/",
            "App\\Application\\":      "src/Application/",
            "App\\Infrastructure\\":   "src/Infrastructure/",
            "App\\Interface\\":        "src/Interface/"
        }
    },
    "require": {
        "php": ">=8.2",
        "symfony/framework-bundle": "^7.0",
        "doctrine/orm": "^3.0",
        "stripe/stripe-php": "^13.0"
    }
}
```

Namespace-структура **отражает** слои. Это не просто «организация файлов» — это **контракт**, который читают и PHPStan, и IDE, и разработчик.

### 5.5 Code Review checklist

| Вопрос | Если «да» → нарушение |
|---|---|
| Есть ли `use Doctrine\*` в `src/Domain/`? | ❌ |
| Есть ли `use App\Infrastructure\*` в `src/Application/`? | ❌ |
| Есть ли `use App\Infrastructure\*` в `src/Interface/`? | ❌ (конкретный адаптер) |
| Controller знает про `EntityManager`? | ❌ |
| Domain-класс знает про `Request` / `Response`? | ❌ |
| Application-класс знает про `RabbitMQ` / `Redis`? | ❌ |
| Интерфейс репозитория в `src/Domain/`? | ✅ (правильно) |
| Реализация репозитория в `src/Infrastructure/`? | ✅ (правильно) |
| `services.yaml` связывает интерфейс → реализацию? | ✅ (правильно) |

### 5.6 Что НЕ работает

| Подход | Почему не работает |
|---|---|
| Только `composer.json` (отдельные пакеты) | В монорепозитории один `composer.json`. Пакетизация — это **дополнительный** уровень, не замена |
| Только namespace | Namespace можно игнорировать. `use` — нет. Но и `use` можно не заметить. Нужен PHPStan |
| Только code review | Человеческий фактор. 50 файлов, один `use Doctrine` в Domain — не заметят |
| `phpstan.neon` без custom rules | Стандартные правила PHPStan не знают о ваших слоях. Нужны **свои** rules |

**Комбинация, которая работает:**

```
Namespace-структура  ←  читается человеком
    +
PHPStan custom rules ←  ловится в CI автоматически
    +
Архитектурный тест   ←  интеграционная проверка всех файлов
    +
Code review          ←  последнее слово
```

---

## 6. Практические компромиссы

### 6.1 Доменное исключение в Controller

```php
// Controller знает о доменных исключениях. Это НОРМАЛЬНО.
try {
    $this->orderService->confirmOrder($id);
} catch (OrderNotInDraftException $e) {
    return new JsonResponse(['error' => $e->getMessage()], 409);
}
```

Controller **должен** маппить доменные исключения на HTTP-коды. Это его работа. Альтернатива — глобальный `ExceptionListener`:

```php
// src/Interface/Http/ExceptionListener.php
final class DomainExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 256)]
    public function onException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        $statusCode = match (true) {
            $e instanceof OrderNotFoundException       => 404,
            $e instanceof EmptyOrderException          => 422,
            $e instanceof OrderNotInDraftException     => 409,
            $e instanceof PaymentDeclinedException     => 402,
            $e instanceof DomainException              => 422,
            default => 500,
        };

        $event->setResponse(
            new JsonResponse(
                ['error' => $e->getMessage()],
                $statusCode
            )
        );
    }
}
```

Тогда Controller становится ещё тоньше:

```php
#[Route('/orders/{id}/confirm', methods: ['POST'])]
public function confirm(string $id): JsonResponse
{
    $order = $this->orderService->confirmOrder($id);
    return new JsonResponse($this->transformer->transform($order));
}
```

### 6.2 `Symfony` в Application

`Symfony\Component\Messenger\MessageBusInterface` — это **порт**. Его можно использовать в Application:

```php
// Application:
$this->bus->dispatch(new OrderWasConfirmed(...));
```

Но `Symfony\Component\Messenger\Transport\RabbitMqTransport` — это **адаптер**. Он в Infrastructure.

**Правило:** `Symfony\Component\*` (компоненты) — допустимы в Application, если это **порт** (интерфейс). `Symfony\Bundle\*` и конкретные транспортные классы — только в Interface / Infrastructure.

### 6.3 Doctrine Type в Domain

`MoneyType extends Type` — это **Infrastructure**. Domain знает про `Money`, не про `MoneyType`.

```php
// Domain:
final class Money { public readonly int $amountCents; … }

// Infrastructure:
final class MoneyType extends Type
{
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        // Money::fromCents(...)
    }
}
```

### 6.4 Voter — Application или Domain?

```php
// src/Application/Security/OrderVoter.php
final class OrderVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $order = $subject;
        $user  = $token->getUser();

        return match ($attribute) {
            'order:owner'   => $user->getId() === $order->customerId(),
            'order:delete'  => $user->getId() === $order->customerId()
                               && $order->status() === OrderStatus::Draft,
            default         => false,
        };
    }
}
```

Voter знает о `Order` (Domain) и `TokenInterface` (Symfony Security — порт). Это **Application**. Не Domain (знает про Symfony). Не Infrastructure (не адаптер).

---

## 7. Итоговая структура

```
src/
├── Domain/                          ← Чистый PHP. 0 внешних зависимостей.
│   ├── Order/
│   │   ├── Order.php
│   │   ├── OrderLine.php
│   │   ├── OrderStatus.php
│   │   ├── OrderRepository.php      ← интерфейс (порт)
│   │   ├── OrderWasConfirmed.php
│   │   └── *Exception.php
│   ├── Payment/
│   │   ├── Payment.php
│   │   ├── PaymentGateway.php       ← интерфейс (порт)
│   │   └── *Exception.php
│   └── Common/
│       ├── Money.php
│       ├── DomainEvent.php
│       └── DomainException.php
│
├── Application/                     ← Оркестрация. Знает Domain. Не знает технологии.
│   ├── Order/
│   │   └── OrderService.php
│   ├── Payment/
│   │   └── PaymentService.php
│   ├── Security/
│   │   └── OrderVoter.php
│   └── Common/
│       ├── UnitOfWork.php           ← интерфейс (порт)
│       └── OutboxRepository.php     ← интерфейс (порт)
│
├── Infrastructure/                  ← Адаптеры. Реализуют порты. Зависят от технологий.
│   ├── Persistence/Doctrine/
│   │   ├── Entity/
│   │   ├── Repository/
│   │   ├── Mapper/
│   │   └── Type/
│   ├── Messaging/
│   │   ├── OutboxPublisher.php
│   │   └── Handler/
│   ├── Integration/
│   │   ├── Stripe/
│   │   └── Elasticsearch/
│   └── Notification/
│
├── Interface/                       ← Тонкий слой. HTTP, CLI, Worker.
│   ├── Http/
│   │   ├── Controller/
│   │   ├── Request/
│   │   ├── Response/
│   │   └── Transformer/
│   ├── Console/
│   └── Worker/
│
config/
└── services.yaml                    ← Composition Root. Связывает порты → адаптеры.

tests/
├── Domain/                          ← Unit-тесты. Чистый PHP. 0 моков.
├── Application/                     ← Unit-тесты. Моки портов.
├── Infrastructure/                  ← Интеграционные тесты. Моки технологий.
└── Architecture/                    ← Тесты границ слоёв.
```

**Одна фраза:**

> Domain определяет **что**. Application определяет **как**. Infrastructure определяет **чем**. Interface определяет **откуда**. Зависимости — только извне внутрь. Нарушение границы — не «мелочь», а архитектурный баг, который ловится в CI, а не через 6 месяцев в проде.

> ⏱ 72.4s · prompt 94007 tok · output 9086 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about a testing strategy for a Symfony application with PostgreSQL. They want to know:
1. Testing pyramid
2. Types of tests
3. What to mock and what not to
4. How to keep tests fast and stable

This is a continuation of a long conversation about PHP/Symfony/Doctrine/DDD architecture. I've been giving detailed, structured answers with code examples, trade-offs, and practical advice. Let me continue in the same style.

Let me think about what a senior PHP developer would actually do:

1. **Testing Pyramid for Symfony + PostgreSQL + DDD:**
   - Unit tests: Domain layer (pure PHP, no mocks), Application layer (mock ports)
   - Integration tests: Infrastructure (Doctrine + real Postgres in Docker), Messaging
   - E2E/Feature tests: HTTP endpoints (Symfony browser kit or Panther)
   - Architecture tests: layer boundaries

2. **What to mock vs what not:**
   - Mock: external services (Stripe, Elasticsearch, Email)
   - Don't mock: Doctrine (use real Postgres), Domain entities (pure objects)
   - Mock: Repository interfaces (in Application tests)
   - Don't mock: Money, Order, Payment (VO and Entities)

3. **Fast and stable:**
   - Parallel execution (PHPUnit --processes or Paratest)
   - Transaction rollback for integration tests (or database per test)
   - Docker for Postgres
   - No external services (mock or use testcontainers)
   - Deterministic data (fixtures, not random)
   - Time control (Clock interface)
   - No network calls in unit tests

Let me structure this well with code examples, trade-offs, and practical advice. I'll follow the same format as previous answers.

</details>

**Кандидат:**

# Стратегия тестирования: Symfony + PostgreSQL + DDD

## 1. Пирамида: что и где

```
                    ┌───────────────┐
                    │  E2E / HTTP   │  ← 5–10 тестов. Полная цепочка.
                    │  (Panther /   │
                    │   BrowserKit) │
                   ┌┴───────────────┴┐
                   │  Integration   │  ← 30–50 тестов. Адаптеры + Postgres.
                   │  (Doctrine,     │
                   │   Messenger,    │
                   │   Outbox)       │
                ┌──┴─────────────────┴──┐
                │  Application / Use   │  ← 50–100 тестов. Моки портов.
                │  Case                │
             ┌──┴───────────────────────┴──┐
             │  Domain (Unit)             │  ← 100–300 тестов. Чистый PHP.
             │  0 моков. 0 БД. 0 сети.    │
             └────────────────────────────┘
```

| Слой | Кол-во | Время на тест | Что проверяем |
|---|---|---|---|
| Domain | 100–300 | < 1мс | Инварианты, переходы состояний, VO-логику |
| Application | 50–100 | < 5мс | Оркестрацию, транзакции, публикацию событий |
| Infrastructure | 30–50 | 10–100мс | Маппинг, SQL, Outbox, адаптеры |
| E2E | 5–10 | 1–5с | Полный HTTP-запрос, auth, рендеринг |

**Соотношение: 70% unit, 20% integration, 10% E2E.** Не наоборот.

---

## 2. Domain: unit-тесты без моков

### Почему без моков

Domain — чистый PHP. `Order`, `Money`, `OrderStatus` не знают про Doctrine, Symfony, сеть. Тест — чистый PHP.

```php
// tests/Domain/Order/OrderTest.php

final class OrderTest extends TestCase
{
    // ── addLine ──────────────────────────────────────────────────

    public function test_addLine_increasesTotal(): void
    {
        $order = new Order('customer-1');
        $order->addLine(
            ProductId::of('p-1'),
            Money::fromCents(1000, 'USD'),
            2,
        );

        self::assertSame(2000, $order->total()->amountCents());
        self::assertCount(1, $order->lines());
    }

    public function test_addLine_recalculatesTotalWithMultipleLines(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $order->addLine(ProductId::of('p-2'), Money::fromCents(500, 'USD'), 3);

        self::assertSame(2500, $order->total()->amountCents());
    }

    public function test_addLine_throwsIfOrderNotInDraft(): void
    {
        $order = new Order('customer-1');
        $order->confirm();

        $this->expectException(OrderNotInDraftException::class);
        $order->addLine(ProductId::of('p-1'), Money::fromCents(100, 'USD'), 1);
    }

    public function test_addLine_throwsOnZeroQuantity(): void
    {
        $order = new Order('customer-1');

        $this->expectException(InvalidQuantityException::class);
        $order->addLine(ProductId::of('p-1'), Money::fromCents(100, 'USD'), 0);
    }

    // ── confirm ──────────────────────────────────────────────────

    public function test_confirm_changesStatusToConfirmed(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);

        $order->confirm();

        self::assertSame(OrderStatus::Confirmed, $order->status());
        self::assertNotNull($order->confirmedAt());
    }

    public function test_confirm_emitsDomainEvent(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);

        $order->confirm();

        $events = $order->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(OrderWasConfirmed::class, $events[0]);
        self::assertSame($order->id(), $events[0]->orderId);
        self::assertSame(1000, $events[0]->total->amountCents());
    }

    public function test_confirm_pullsEventsOnlyOnce(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);

        $order->confirm();
        $first = $order->pullDomainEvents();
        $second = $order->pullDomainEvents();

        self::assertCount(1, $first);
        self::assertCount(0, $second);
    }

    public function test_confirm_throwsOnEmptyOrder(): void
    {
        $order = new Order('customer-1');

        $this->expectException(EmptyOrderException::class);
        $order->confirm();
    }

    public function test_confirm_throwsIfAlreadyConfirmed(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $order->confirm();
        $order->pullDomainEvents();

        $this->expectException(InvalidTransitionException::class);
        $order->confirm();
    }

    // ── cancel ───────────────────────────────────────────────────

    public function test_cancel_fromDraft(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);

        $order->cancel();

        self::assertSame(OrderStatus::Cancelled, $order->status());
    }

    public function test_cancel_throwsIfShipped(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $order->confirm();
        $order->pullDomainEvents();
        $order->ship('TRACK123');
        $order->pullDomainEvents();

        $this->expectException(InvalidTransitionException::class);
        $order->cancel();
    }

    // ── Money (VO) ───────────────────────────────────────────────

    public function test_money_fromCents_throwsOnNegative(): void
    {
        $this->expectException(\DomainException::class);
        Money::fromCents(-1, 'USD');
    }

    public function test_money_plus_returnsNewInstance(): void
    {
        $a = Money::fromCents(100, 'USD');
        $b = Money::fromCents(200, 'USD');
        $c = $a->plus($b);

        self::assertNotSame($a, $c);
        self::assertNotSame($b, $c);
        self::assertSame(300, $c->amountCents());
    }

    public function test_money_plus_throwsOnCurrencyMismatch(): void
    {
        $a = Money::fromCents(100, 'USD');
        $b = Money::fromCents(200, 'EUR');

        $this->expectException(\DomainException::class);
        $a->plus($b);
    }
}
```

**Характеристики:**

| Аспект | Значение |
|---|---|
| Моки | **Нет**. Ни одного `createMock()`. |
| БД | **Нет**. |
| Сеть | **Нет**. |
| Время | `new \DateTimeImmutable()` — детерминированно. Или `Clock` (см. ниже). |
| Время выполнения | < 1мс на тест. 200 тестов < 0.5с. |
| Изоляция | Каждый тест создаёт свои объекты. Нет shared state. |

---

## 3. Application: unit-тесты с моками портов

### Что мокаем, что нет

| Мокаем | Не мокаем |
|---|---|
| `OrderRepository` (порт) | `Order` (домен) |
| `PaymentGateway` (порт) | `Money`, `ProductId` (VO) |
| `UnitOfWork` (порт) | Доменные события |
| `OutboxRepository` (порт) | Исключения (проверяем `expectException`) |
| `EventPublisher` (порт) | |

**Принцип:** мокаем **границу** (порт). Не мокаем **логику** (домен).

```php
// tests/Application/Order/OrderServiceTest.php

final class OrderServiceTest extends TestCase
{
    private OrderService $service;
    private OrderRepository&MockObject $orders;
    private UnitOfWork&MockObject $uow;

    protected function setUp(): void
    {
        $this->orders = $this->createMock(OrderRepository::class);
        $this->uow    = $this->createMock(UnitOfWork::class);

        $this->service = new OrderService($this->orders, $this->uow);
    }

    // ── confirmOrder ─────────────────────────────────────────────

    public function test_confirmOrder_savesAndEmitsEvent(): void
    {
        // Arrange
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $order->confirm();
        $events = $order->pullDomainEvents();  // 1 событие

        $this->orders->method('findById')
            ->with('order-1')
            ->willReturn($order);

        $this->uow->expects(self::once())
            ->method('run')
            ->willReturnCallback(function (callable $fn) use ($order): void {
                $fn();  // выполняем closure
            });

        // Act
        $result = $this->service->confirmOrder('order-1');

        // Assert
        self::assertSame($order, $result);
    }

    public function test_confirmOrder_throwsIfOrderNotFound(): void
    {
        $this->orders->method('findById')
            ->willReturn(null);

        $this->expectException(OrderNotFoundException::class);
        $this->service->confirmOrder('non-existent');
    }

    public function test_confirmOrder_wrapsInTransaction(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);

        $this->orders->method('findById')->willReturn($order);

        $saveCalled = false;
        $this->uow->method('run')
            ->willReturnCallback(function (callable $fn) use (&$saveCalled): void {
                $fn();
                $saveCalled = true;
            });

        $this->service->confirmOrder('order-1');

        self::assertTrue($saveCalled);
    }

    public function test_addLine_delegatesToOrder(): void
    {
        $order = new Order('customer-1');
        $this->orders->method('findById')->willReturn($order);

        $this->uow->method('run')->willReturnCallback(fn(callable $fn) => $fn());

        $result = $this->service->addLine('order-1', 'p-2', 500, 3);

        self::assertSame(1500, $result->total()->amountCents());
    }
}
```

**Характеристики:**

| Аспект | Значение |
|---|---|
| Моки | Только порты. 2–3 `createMock`. |
| Домен | Реальные объекты. `new Order(...)`, `Money::fromCents(...)`. |
| БД | Нет. |
| Время | < 5мс на тест. |

---

## 4. Infrastructure: интеграционные тесты с реальным PostgreSQL

### Почему не мокаем Doctrine

Мока `EntityManager` → тест проверяет «я вызвал `persist()`», а не «данные корректно записаны и прочитаны». Маппинг `Order ↔ OrderRecord` — **главное** место, где живут баги. Его нужно проверять на **реальной** БД.

### Docker для PostgreSQL

```yaml
# docker-compose.test.yml
services:
    postgres:
        image: postgres:16-alpine
        environment:
            POSTGRES_DB: app_test
            POSTGRES_USER: test
            POSTGRES_PASSWORD: test
        ports:
            - "5433:5432"
        healthcheck:
            test: ["CMD", "pg_isready", "-U", "test"]
            interval: 2s
            timeout: 3s
            retries: 10
```

```bash
# Перед тестами:
docker compose -f docker-compose.test.yml up -d
# После:
docker compose -f docker-compose.test.yml down -v
```

### Base class для интеграционных тестов

```php
// tests/Infrastructure/IntegrationTest.php

abstract class IntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected static function setUpBeforeClass(): void
    {
        self::bootKernel();
    }

    protected function setUp(): void
    {
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Очистка таблиц (порядок: дочерние → родительские)
        $this->em->getConnection()->executeStatement('TRUNCATE order_lines, orders, payments, outbox RESTART IDENTITY CASCADE');
    }

    protected function tearDown(): void
    {
        $this->em->clear();
        $this->em->getConnection()->executeStatement('TRUNCATE order_lines, orders, payments, outbox RESTART IDENTITY CASCADE');
    }
}
```

**Альтернатива: транзакция + rollback (быстрее, но хрупче):**

```php
abstract class TransactionalTest extends KernelTestCase
{
    protected Connection $connection;
    protected \PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = static::getContainer()->get('doctrine')->getConnection();
        $this->pdo = $this->connection->getWrappedConnection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
        static::getContainer()->get('doctrine')->getManager()->clear();
        parent::tearDown();
    }
}
```

| Подход | Плюсы | Минусы |
|---|---|---|
| `TRUNCATE` в `setUp/tearDown` | Чистое состояние. `IDENTITY` сбрасывается | Медленнее (DDL-lock) |
| Транзакция + rollback | Быстро. Нет DDL-lock | `TRUNCATE`, `CREATE INDEX` внутри транзакции не работают. Сложнее отладка |

**Для 30–50 тестов — `TRUNCATE`. Для 200+ — транзакция.**

### Тест маппинга

```php
// tests/Infrastructure/Persistence/OrderMapperTest.php

final class OrderMapperTest extends IntegrationTest
{
    private OrderMapper $mapper;
    private DoctrineOrderRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->mapper = $container->get(OrderMapper::class);
        $this->repo   = $container->get(DoctrineOrderRepository::class);
    }

    public function test_roundtrip_preservesAllFields(): void
    {
        // Arrange
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1500, 'USD'), 2);
        $order->addLine(ProductId::of('p-2'), Money::fromCents(300, 'EUR'), 1);
        $order->confirm();
        $order->pullDomainEvents();

        // Act: save
        $this->repo->save($order);
        $this->em->flush();

        // Act: load
        $loaded = $this->repo->findById($order->id());

        // Assert
        self::assertNotNull($loaded);
        self::assertSame($order->id(), $loaded->id());
        self::assertSame('customer-1', $loaded->customerId());
        self::assertSame(OrderStatus::Confirmed, $loaded->status());
        self::assertSame(3300, $loaded->total()->amountCents());  // 3000 + 300
        self::assertCount(2, $loaded->lines());

        $line1 = $loaded->lines()[0];
        self::assertSame('p-1', $line1->productId()->value);
        self::assertSame(1500, $line1->unitPrice()->amountCents());
        self::assertSame(2, $line1->quantity());
    }

    public function test_money_withCents_isPersistedCorrectly(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1, 'USD'), 1);
        $this->repo->save($order);
        $this->em->flush();

        $loaded = $this->repo->findById($order->id());
        self::assertSame(1, $loaded->total()->amountCents());
    }

    public function test_multipleLines_withDifferentProducts(): void
    {
        $order = new Order('customer-1');
        for ($i = 1; $i <= 10; $i++) {
            $order->addLine(
                ProductId::of("p-{$i}"),
                Money::fromCents($i * 100, 'USD'),
                $i,
            );
        }
        $this->repo->save($order);
        $this->em->flush();

        $loaded = $this->repo->findById($order->id());
        self::assertCount(10, $loaded->lines());

        $expectedTotal = array_sum(range(1, 10)) * 100;  // 5500
        self::assertSame($expectedTotal, $loaded->total()->amountCents());
    }
}
```

### Тест Outbox

```php
// tests/Infrastructure/Messaging/OutboxTest.php

final class OutboxTest extends IntegrationTest
{
    private DoctrineOutboxRepository $outbox;
    private OutboxPublisher $publisher;

    protected function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->outbox   = $container->get(DoctrineOutboxRepository::class);
        $this->publisher = $container->get(OutboxPublisher::class);
    }

    public function test_publish_appendsToOutbox(): void
    {
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $order->confirm();
        $events = $order->pullDomainEvents();

        $this->em->wrapInTransaction(function () use ($events): void {
            foreach ($events as $event) {
                $this->outbox->append(
                    'order-1', 'Order',
                    get_class($event),
                    json_encode($event),
                );
            }
            $this->em->flush();
        });

        $unpublished = $this->outbox->findUnpublished(10);
        self::assertCount(1, $unpublished);
        self::assertSame('OrderWasConfirmed', $unpublished[0]->eventType);
        self::assertNull($unpublished[0]->publishedAt);
    }

    public function test_publish_marksAsPublished(): void
    {
        // ... append ...
        $this->publisher->publish(10);

        $unpublished = $this->outbox->findUnpublished(10);
        self::assertCount(0, $unpublished);
    }
}
```

---

## 5. E2E / HTTP-тесты

### Symfony BrowserKit (быстро, без браузера)

```php
// tests/Interface/Http/OrderControllerTest.php

final class OrderControllerTest extends WebTestCase
{
    private Client $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Очистка
        $this->em->getConnection()->executeStatement(
            'TRUNCATE order_lines, orders RESTART IDENTITY CASCADE'
        );

        // Фикстура
        $order = new Order('customer-1');
        $order->addLine(ProductId::of('p-1'), Money::fromCents(1000, 'USD'), 1);
        $this->em->persist($this->em->createOrderRecord($order));
        $this->em->flush();
    }

    public function test_confirmOrder_returns200AndUpdatesStatus(): void
    {
        $order = $this->em->getRepository(OrderRecord::class)->findOneBy([]);

        $this->client->request('POST', "/orders/{$order->getId()}/confirm");

        self::assertResponseIsSuccessful();

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('confirmed', $content['status']);
    }

    public function test_confirmOrder_returns404ForNonExistent(): void
    {
        $this->client->request('POST', '/orders/non-existent/confirm');

        self::assertResponseStatusCodeSame(404);
    }

    public function test_confirmOrder_returns409IfAlreadyConfirmed(): void
    {
        $order = $this->em->getRepository(OrderRecord::class)->findOneBy([]);
        $order->setStatus('confirmed');
        $this->em->flush();

        $this->client->request('POST', "/orders/{$order->getId()}/confirm");

        self::assertResponseStatusCodeSame(409);
    }
}
```

### Когда Panther (реальный браузер)

| Сценарий | BrowserKit | Panther |
|---|---|---|
| API (JSON) | ✅ | Нет |
| CSRF-токены | ✅ | ✅ |
| JavaScript (SPA) | ❌ | ✅ |
| File upload | ❌ | ✅ |
| Cookie / session | ✅ | ✅ |

**Для Symfony API-приложения — BrowserKit достаточно. Panther — только если есть JS-фронтенд.**

---

## 6. Что мокаем, что нет: сводная таблица

| Компонент | Мокаем? | Почему |
|---|---|---|
| `Order`, `Money`, `ProductId` | ❌ | Домен. Чистый PHP. Тестируем напрямую. |
| `OrderRepository` (интерфейс) | ✅ в Application | Порт. Мокаем. В Integration — реализация. |
| `PaymentGateway` (интерфейс) | ✅ | Порт. Stripe/Adyen — не вызываем в тестах. |
| `EntityManager` | ❌ | Интеграция. Реальный Postgres. |
| `OrderRecord` (ORM) | ❌ | Тестируем через репозиторий + Postgres. |
| `OrderMapper` | ❌ | Тестируем через round-trip: save → load → assert. |
| `RabbitMQ` / `Redis` | ✅ | Внешний сервис. Мокаем `EventPublisher`. |
| `MailerInterface` | ✅ | Внешний сервис. Мокаем. Проверяем «вызван с правильными параметрами». |
| `Elasticsearch` | ✅ | Внешний сервис. Мокаем. |
| `Clock` / время | ✅ | Для детерминизма. |
| `Random` / UUID | ✅ | Для детерминизма. |
| `Request`, `Response` | ✅ | Только в Interface-тестах. |

---

## 7. Быстрота и стабильность

### 7.1 Параллельный запуск

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="domain">
            <directory>tests/Domain</directory>
        </testsuite>
        <testsuite name="application">
            <directory>tests/Application</directory>
        </testsuite>
        <testsuite name="infrastructure">
            <directory>tests/Infrastructure</directory>
        </testsuite>
        <testsuite name="interface">
            <directory>tests/Interface</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

```bash
# Параллельный запуск (8 процессов):
vendor/bin/phpunit --processes=8 --testdox

# Или Paratest:
vendor/bin/paratest --processes=8
```

**Важно:** параллельные тесты **не могут** писать в одну и ту же БД-таблицу. Варианты:

| Подход | Как |
|---|---|
| БД на процесс | `CREATE DATABASE app_test_{pid}`. Slow, но изолированно. |
| `TRUNCATE` + `LOCK TABLE` | `TRUNCATE ... CASCADE` блокирует на 5мс. Для 8 процессов — ок. |
| `transaction + rollback` | Каждый процесс в своей транзакции. Быстрее. |
| `pg_schemas` | Отдельная схема на процесс. `SET search_path TO test_3`. |

**Для 50 integration-тестов: `TRUNCATE` достаточно. 8 процессов × 5мс = 40мс overhead.**

### 7.2 Детерминизм

| Проблема | Решение |
|---|---|
| `new \DateTimeImmutable()` | Вводить `Clock` интерфейс: `interface Clock { now(): \DateTimeImmutable; }`. Мокаем в тестах. |
| `Uuid::uuid4()` | Вводить `IdGenerator` интерфейс. Мокаем. |
| `rand()` / `mt_rand()` | Фиксированный seed: `mt_srand(42)`. Или `IdGenerator`. |
| Порядок итераций `array` | `ksort()` перед assert. Или `assertContains` вместо `assertSame`. |
| Время в Outbox | `published_at` — `now()`. В тесте проверяем `assertNotNull`, не точное значение. |
| Locale | `setlocale(LC_ALL, 'C')` в `setUp()`. |

```php
// src/Domain/Common/Clock.php
interface Clock
{
    public function now(): \DateTimeImmutable;
}

// src/Infrastructure/Time/SystemClock.php
final class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}

// src/Infrastructure/Time/FrozenClock.php (тесты)
final class FrozenClock implements Clock
{
    public function __construct(private \DateTimeImmutable $frozen) {}
    public function now(): \DateTimeImmutable { return $this->frozen; }
}
```

### 7.3 Стабильность (flaky tests)

| Причина flake | Решение |
|---|---|
| Гонка в integration-тесте (2 процесса пишут в одну таблицу) | `TRUNCATE` + `LOCK`. Или отдельная БД. |
| `sleep()` в тесте | Убрать. Заменить на ожидание (polling) или event. |
| Зависимость от внешнего сервиса (Stripe, SendGrid) | Мокаем. Всегда. |
| `EntityManager` state из предыдущего теста | `$em->clear()` в `tearDown()`. |
| `static` state в доменных классах | Избегать `static` в домене. Если есть — сбрасывать в `setUp()`. |
| Время: тест проходит в 23:59:59, а ожидаем 00:00 | `FrozenClock`. |
| `ORDER BY` без `id` (несортируемый порядок) | Всегда `ORDER BY id` в интеграционных тестах. |

### 7.4 CI-конвейер

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
    tests:
        runs-on: ubuntu-latest
        services:
            postgres:
                image: postgres:16-alpine
                env:
                    POSTGRES_DB: app_test
                    POSTGRES_USER: test
                    POSTGRES_PASSWORD: test
                ports:
                    - 5432:5432
                options: >
                    --health-cmd="pg_isready -U test"
                    --health-interval=5s
                    --health-timeout=5s
                    --health-retries=10

        steps:
            - uses: actions/checkout@v4
            - uses: shivammathur/setup-php@v2
              with:
                  php-version: '8.3'
                  extensions: pdo_pgsql, intl
                  coverage: none

            - run: composer install --no-interaction --prefer-dist
            - run: php bin/console doctrine:schema:create --env=test

            # Domain + Application: параллельно, без БД
            - name: Domain tests
              run: vendor/bin/phpunit --testsuite=domain --processes=4 --testdox

            - name: Application tests
              run: vendor/bin/phpunit --testsuite=application --processes=4 --testdox

            # Infrastructure: нужен Postgres
            - name: Infrastructure tests
              run: vendor/bin/phpunit --testsuite=infrastructure --processes=2 --testdox

            # Interface: нужен Postgres
            - name: Interface tests
              run: vendor/bin/phpunit --testsuite=interface --testdox

            # Архитектура
            - name: Architecture tests
              run: vendor/bin/phpunit tests/Architecture/

            # Statically
            - name: PHPStan
              run: vendor/bin/phpstan analyse --level=8 --no-progress

            # Формат
            - name: CS Fixer
              run: vendor/bin/php-cs-fixer fix --dry-run --diff
```

**Время в CI:**

| Этап | Время |
|---|---|
| Domain (200 тестов, 4 проца) | ~3с |
| Application (80 тестов, 4 проца) | ~5с |
| Infrastructure (40 тестов, 2 проца) | ~15с |
| Interface (10 тестов) | ~5с |
| Architecture | ~2с |
| PHPStan | ~30с |
| **Итого** | **~60с** |

### 7.5 Фикстуры

```php
// tests/Fixtures/OrderFactory.php

final class OrderFactory
{
    public static function draft(int $lines = 1): Order
    {
        $order = new Order('customer-' . self::rand());
        for ($i = 0; $i < $lines; $i++) {
            $order->addLine(
                ProductId::of("p-{$i}"),
                Money::fromCents(100 * ($i + 1), 'USD'),
                $i + 1,
            );
        }
        return $order;
    }

    public static function confirmed(int $lines = 1): Order
    {
        $order = self::draft($lines);
        $order->confirm();
        $order->pullDomainEvents();
        return $order;
    }

    public static function shipped(int $lines = 1): Order
    {
        $order = self::confirmed($lines);
        $order->ship('TRACK-' . self::rand());
        $order->pullDomainEvents();
        return $order;
    }

    private static function rand(): string
    {
        return substr(md5(uniqid()), 0, 8);
    }
}
```

Использование:

```php
public function test_cancel_throwsIfShipped(): void
{
    $order = OrderFactory::shipped();  // ← 3 строки вместо 10

    $this->expectException(InvalidTransitionException::class);
    $order->cancel();
}
```

---

## 8. Антипаттерны

| Антипаттерн | Почему плохо |
|---|---|
| Мокаем `EntityManager` | Тест проверяет «я вызвал `persist()`», а не «данные записаны». Маппинг не проверен. |
| E2E-тест на каждый use case | 50 E2E-тестов × 3с = 2.5мин. Долго. Хрупко. |
| `sleep(1)` в тесте | Flake. Зависит от нагрузки на CI. |
| Тесты зависят от порядка | `test_A` создаёт данные, `test_B` их читает. Меняем порядок → падает. |
| `createMock(EntityManager::class)` + 20 `->method()->willReturn()` | Тест становится длиннее кода, который тестирует. |
| Тестирование `OrderMapper` через `OrderService` | Неизольированно. Баг в сервисе маскирует баг в маппере. |
| `@group legacy` на 30 тестов, которые никто не удаляет | Технический долг растёт. |
| Тесты в `tests/` без структуры | `tests/OrderTest.php`, `tests/PaymentTest.php`, `tests/Helper.php`. Непонятно, что unit, что integration. |
| `global $em` | Невозможно параллельный запуск. Невозможно изоляция. |
| Тест, который проверяет `echo` / `print` | Это не поведение. Это реализация. |

---

## 9. Итог

```
┌─────────────────────────────────────────────────────────────────────────┐
│  СТРАТЕГИЯ                                                              │
│                                                                         │
│  Domain:       чистый PHP, 0 моков, < 1мс/тест                          │
│  Application:  моки портов, реальный домен, < 5мс/тест                  │
│  Infrastructure: реальный Postgres (Docker), round-trip, 10–100мс/тест  │
│  Interface:    BrowserKit, JSON, 1–5с/тест                              │
│  Architecture: запрещаем импорты между слоями                           │
│                                                                         │
│  МОКАЕМ:     порты (Repository, Gateway, Publisher, Clock)              │
│  НЕ МОКАЕМ:  домен, EntityManager, маппер                               │
│                                                                         │
│  БЫСТРОТА:   параллельный запуск, TRUNCATE, фикстуры                    │
│  СТАБИЛЬНОСТЬ: FrozenClock, детерминированные ID, ORDER BY id           │
│  CI:         60с. PHPStan level 8. CS Fixer.                           │
└─────────────────────────────────────────────────────────────────────────┘
```

**Одна фраза:** тестируйте **поведение домена** (что делает `Order::confirm()`), **контракты портов** (что делает `OrderRepository::save()`), и **составную систему** (что происходит при `POST /orders/{id}/confirm`). Не тестируйте реализацию. Не мокайте то, что можно проверить на реальном Postgres за 50мс. Не тратьте 3 секунды на E2E-тест то, что покрыто unit-тестом за 1мс.

> ⏱ 72.2s · prompt 102662 tok · output 8956 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

The user is asking about concurrent modification of the same database record by two users. They want:
1. What problems can arise
2. What protection methods exist
3. Comparison of approaches
4. When to choose which

This is a natural continuation of our conversation about DDD, aggregates, transactions, and concurrent access. I've already covered optimistic/pessimistic locking in a previous answer, but this question is broader and asks for a more complete comparison. Let me structure this clearly.

Let me think about what to cover:

**Problems:**
- Lost update
- Dirty read
- Phantom reads
- Deadlocks
- Inconsistent state (partial writes)
- Business logic violations (e.g., overselling inventory)

**Protection methods:**
1. Optimistic locking (version column)
2. Pessimistic locking (SELECT FOR UPDATE)
3. Application-level serialization (one writer per aggregate)
4. Idempotency + retry
5. Transaction isolation levels (READ COMMITTED, REPEATABLE READ, SERIALIZABLE)
6. CHECK constraints / triggers as last line of defense
7. Application-level distributed locks (Redis, ZooKeeper)
8. Event sourcing / append-only log

**Comparison criteria:**
- Throughput
- Latency
- Complexity
- Failure modes
- When conflicts are rare vs frequent
- Whether operation is idempotent
- User experience (retry vs wait)
- Infrastructure cost

Let me write a comprehensive but focused answer. I should avoid repeating too much from the previous answer about concurrent modification of aggregates, but the user is asking a slightly broader question (any record, not just aggregates). Let me be more comprehensive this time while still being concrete.

I'll structure it as:
1. What goes wrong (concrete scenarios)
2. Protection methods (each with mechanism, code, trade-offs)
3. Comparison table
4. Decision framework
5. Anti-patterns

Let me make sure the code examples are concrete and PHP/Symfony/PostgreSQL specific.

</details>

**Кандидат:**

# Конкурентная модификация одной записи

## 1. Что конкретно происходит

### Сценарий A: Lost Update

```
Т1 (user-1)                          Т2 (user-2)
─────────────────────────────────────   ─────────────────────────────────────
SELECT balance FROM accounts
WHERE id = 'acc-1'
→ 5000

                                       SELECT balance FROM accounts
                                       WHERE id = 'acc-1'
                                       → 5000

balance = 5000 - 3000 = 2000
                                       balance = 5000 - 4000 = 1000
UPDATE accounts
SET balance = 2000
WHERE id = 'acc-1'
→ 1 row

                                       UPDATE accounts
                                       SET balance = 1000
                                       WHERE id = 'acc-1'
                                       → 1 row

COMMIT                                COMMIT
```

**Результат:** баланс `1000`. Оба попросили списать (3000 + 4000 = 7000). Остаток должен быть `-2000`. Вместо этого `1000`. **Деньги из воздуха.**

Никто не получил ошибку. Ни один лог не записал предупреждение. Система «работает» — но вриёт.

### Сценарий B: Double Spend (идемпотентность)

```
Пользователь нажимает «Оплатить» дважды (двойной клик, retry, таймаут).

Т1: POST /payments  →  charge(5000)  →  Stripe: OK  →  INSERT payment_1
Т2: POST /payments  →  charge(5000)  →  Stripe: OK  →  INSERT payment_2
```

Две оплаты. Одна должна быть refund'нута, но `ORDER` уже `confirmed`.

### Сценарий C: Deadlock

```
Т1: UPDATE orders SET ... WHERE id = 'o-1'    → lock on o-1
Т1: UPDATE order_lines SET ... WHERE order_id = 'o-1'  → ждёт lock на lines

Т2: UPDATE order_lines SET ... WHERE order_id = 'o-1'  → lock on lines
Т2: UPDATE orders SET ... WHERE id = 'o-1'    → ждёт lock на o-1

→ DEADLOCK. Postgres убивает Т2. Т1 продолжает.
```

### Сценарий D: Несогласованное состояние

```
Т1: UPDATE orders SET status = 'confirmed' WHERE id = 'o-1'  → OK
Т1: INSERT INTO payments ... → ERROR (constraint)
Т1: ROLLBACK

Т2 (в это время):
SELECT * FROM orders WHERE id = 'o-1' → status = 'confirmed'
→ Показывает пользователю «подтверждён», но Payment не существует.
```

Это окно между `UPDATE orders` и `INSERT INTO payments` в рамках одной транзакции. В `READ COMMITTED` Т2 **не видит** промежуточное состояние (видит до COMMIT). Но если Т1 уже откоммитил `orders` и ещё не дописал `payments` (двойная транзакция) — Т2 увидит несогласованное состояние.

### Сводка проблем

| Проблема | Причина | Последствие |
|---|---|---|
| Lost update | Два `UPDATE` без синхронизации | Данные потеряны. Тишина. |
| Double spend | Отсутствие идемпотентности | Двойная оплата |
| Deadlock | Разный порядок получения locks | 500 для одного пользователя |
| Несогласованное состояние | Две транзакции вместо одной | UI показывает «подтверждён», Payment нет |
| Overselling | `stock = 1`, два покупателя одновременно | Оба получили товар. Склад пуст. |

---

## 2. Механизмы защиты

### 2.1 Оптимистичная блокировка (version column)

**Механизм:** колонка `version`. Каждый `UPDATE` содержит `WHERE version = <прочитанное>`. Если другой процесс успел — `affected rows = 0`.

```sql
ALTER TABLE accounts
    ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// Doctrine:
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    private string $id;

    #[ORM\Column]
    private int $balanceCents;

    #[ORM\Version]
    #[ORM\Column]
    private int $version;
}
```

```sql
-- Генерируемый UPDATE:
UPDATE accounts
SET balance_cents = 2000, version = 2
WHERE id = 'acc-1' AND version = 1;
-- Если Т2 уже записал version=2 → 0 rows → StaleObjectStateException
```

**В сервисе:**

```php
final class AccountService
{
    public function __construct(
        private AccountRepository $accounts,
        private UnitOfWork        $uow,
    ) {}

    public function withdraw(string $accountId, int $amountCents): Account
    {
        $account = $this->accounts->findById($accountId)
            ?? throw new AccountNotFoundException($accountId);

        if ($account->balanceCents() < $amountCents) {
            throw new InsufficientBalanceException(
                $account->balanceCents(), $amountCents
            );
        }

        $account->withdraw($amountCents);

        try {
            $this->uow->run(function () use ($account): void {
                $this->accounts->save($account);
            });
        } catch (OptimisticLockException $e) {
            throw new ConflictException(
                'Account was modified concurrently. Please retry.',
                previous: $e
            );
        }

        return $account;
    }
}
```

**Что происходит при конфликте:**

```
Т1: SELECT → version=1, balance=5000
Т2: SELECT → version=1, balance=5000

Т1: UPDATE ... WHERE version=1 → 1 row ✓, version→2
Т2: UPDATE ... WHERE version=1 → 0 rows ✗
     → OptimisticLockException
     → 409 Conflict
```

Т2 **знает**, что проиграл. Клиент решает: retry, показать ошибку, перезагрузить.

---

### 2.2 Пессимистичная блокировка (SELECT FOR UPDATE)

**Механизм:** эксклюзивная блокировка строки на время чтения. Второй процесс **ждёт**.

```php
public function withdraw(string $accountId, int $amountCents): Account
{
    $this->uow->run(function () use ($accountId, $amountCents): void {
        // FOR UPDATE: блокирует строку до COMMIT
        $account = $this->accounts->findByIdForUpdate($accountId)
            ?? throw new AccountNotFoundException($accountId);

        if ($account->balanceCents() < $amountCents) {
            throw new InsufficientBalanceException(
                $account->balanceCents(), $amountCents
            );
        }

        $account->withdraw($amountCents);
        $this->accounts->save($account);
    });
    // ← COMMIT. Lock освобождён.

    return $account;
}
```

```sql
-- Генерируемый SQL:
BEGIN;
SELECT * FROM accounts WHERE id = 'acc-1' FOR UPDATE;
-- ← строка заблокирована. Т2 ждёт.
UPDATE accounts SET balance_cents = 2000 WHERE id = 'acc-1';
COMMIT;
-- ← lock освобождён. Т2 продолжает.
```

**Что происходит при конфликте:**

```
Т1: SELECT FOR UPDATE → lock
Т2: SELECT FOR UPDATE → WAIT (до lock_timeout)

Т1: UPDATE → OK
Т1: COMMIT → lock freed

Т2: SELECT FOR UPDATE →读到 version=2, balance=2000
Т2: 2000 < 4000 → InsufficientBalanceException → 402
```

Т2 получает **актуальное** состояние и **доменную** ошибку. Не «конфликт», а «недостаточно средств».

---

### 2.3 Idempotency + retry

**Механизм:** каждый запрос получает **идемпотентный ключ**. Повторный запрос с тем же ключом возвращает **тот же результат**, а не создаёт новую операцию.

```sql
CREATE TABLE payments (
    id              VARCHAR(36) PRIMARY KEY,
    idempotency_key VARCHAR(255) UNIQUE NOT NULL,
    order_id        VARCHAR(36) NOT NULL,
    amount_cents    INT NOT NULL,
    status          VARCHAR(20) NOT NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT now()
);
```

```php
final class PaymentService
{
    public function createPayment(string $orderId, int $amountCents, string $idempotencyKey): Payment
    {
        // 1. Проверяем: уже создан?
        $existing = $this->payments->findByIdempotencyKey($idempotencyKey);
        if ($existing !== null) {
            return $existing;  // ← тот же результат, не новая оплата
        }

        // 2. Создаём. UNIQUE constraint — атомарная защита от гонки.
        $payment = Payment::create($orderId, Money::fromCents($amountCents, 'USD'));
        $payment->setIdempotencyKey($idempotencyKey);

        try {
            $this->uow->run(function () use ($payment): void {
                $this->payments->save($payment);
            });
        } catch (UniqueConstraintViolationException $e) {
            // Гонка: два запроса с одним ключом одновременно.
            // Один успел вставить. Второй — UNIQUE violation.
            // Возвращаем уже существующий.
            return $this->payments->findByIdempotencyKey($idempotencyKey);
        }

        return $payment;
    }
}
```

**Клиент генерирует ключ:**

```javascript
// Frontend: при клике «Оплатить»
const idempotencyKey = crypto.randomUUID();
fetch('/api/payments', {
    method: 'POST',
    headers: { 'Idempotency-Key': idempotencyKey },
    body: JSON.stringify({ orderId, amount })
});
// Retry при network error — тот же ключ. Не двойная оплата.
```

---

### 2.4 Transactional Outbox + атомарность

**Механизм:** мутация + событие в **одну** транзакцию. Нет окна несогласованности.

```php
public function confirm(string $orderId): void
{
    $order = $this->orders->findById($orderId);
    $order->confirm();
    $events = $order->pullDomainEvents();

    $this->uow->run(function () use ($order, $events): void {
        $this->orders->save($order);           // UPDATE orders
        $this->outbox->appendAll($events);     // INSERT INTO outbox
        // ← Одна транзакция. Атомарно.
    });
}
```

Это **не** защита от конфликтов между двумя пользователями. Это защита от **несогласованного состояния** (Сценарий D). Но без Outbox оптимистичная/пессимистичная блокировка не гарантирует, что `Payment` будет создан.

---

### 2.5 Изоляционные уровни Postgres

| Уровень | Что защищает | Что НЕ защищает |
|---|---|---|
| `READ COMMITTED` (default) | Dirty read | Lost update, non-repeatable read |
| `REPEATABLE READ` | Dirty read, non-repeatable read | Lost update (аномалия F1) |
| `SERIALIZABLE` | Всё, включая lost update | Ничего. Но: SSI abort → retry |

```sql
-- SERIALIZABLE: Postgres детектирует конфликт и откатывает одну из транзакций
BEGIN TRANSACTION ISOLATION LEVEL SERIALIZABLE;
SELECT balance FROM accounts WHERE id = 'acc-1';
UPDATE accounts SET balance = balance - 3000 WHERE id = 'acc-1';
COMMIT;
-- Если Т2 в это время читал и писал ту же строку →
-- → 40001: could not serialize access due to concurrent update
-- → Retry.
```

**Практика:** `SERIALIZABLE` — для критических операций (баланс, инвентарь). `READ COMMITTED` + `#[ORM\Version]` — для остальных.

---

### 2.6 Один writer на запись

**Механизм:** архитектурная гарантия, что только **один** процесс может мутировать конкретную запись.

```
Request: POST /accounts/acc-1/withdraw
    ↓
Consistent Hash (key = 'acc-1')
    ↓
Worker #7  ← единственный, кто обрабатывает acc-1
```

Worker #7 обрабатывает запросы **последовательно**. Конфликта нет, потому что нет параллельности.

**Реализации:**

| Механизм | Пример |
|---|---|
| RabbitMQ `exclusive consumer` | Очередь на `account_id`. 1 consumer. |
| Redis `SET key lock NX EX 30` | Distributed lock. |
| Consistent hashing (etcd, ZooKeeper) | Раунд-роbin по хэшу ключа. |
| Postgres advisory lock | `SELECT pg_advisory_xact_lock(hashtext('acc-1'))`. |

**Postgres advisory lock (без внешней инфраструктуры):**

```sql
BEGIN;
SELECT pg_advisory_xact_lock(hashtext('account:acc-1'));
-- ← блокировка на строку. Другая транзакция с тем же ключом ждёт.
UPDATE accounts SET balance = balance - 3000 WHERE id = 'acc-1';
COMMIT;
-- ← lock освобождён автоматически.
```

---

### 2.7 CHECK constraint (последний барьер)

```sql
ALTER TABLE accounts
    ADD CONSTRAINT chk_balance_non_negative
        CHECK (balance_cents >= 0);
```

Это **не** защита от lost update. Это защита от того, что **кто-то** (баг, прямой SQL, миграция) запишет отрицательный баланс. Если PHP-валидация не сработала — БД скажет «нет».

---

## 3. Сравнение

| Критерий | Оптимистичная | Пессимистичная | Idempotency | Outbox | SERIALIZABLE | One-writer | CHECK |
|---|---|---|---|---|---|---|---|
| **Lost update** | ✅ 409 | ✅ актуальное | ⚠️ (для дублей) | ❌ | ✅ abort | ✅ нет параллельности | ⚠️ (для значений) |
| **Double spend** | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ❌ |
| **Deadlock** | Нет | Да | Нет | Нет | Нет | Нет | Нет |
| **Latency** | Низкая | Высокая (ожидание) | Низкая | Низкая | Средняя (retry) | Низкая | — |
| **Throughput** | Высокая | Низкая (очередь) | Высокая | Высокая | Средняя | Высокая (N workers) | — |
| **Сложность** | Низкая | Средняя | Низкая | Средняя | Низкая (настройка) | Высокая | Минимальная |
| **Инфраструктура** | 0 | 0 | 0 (колонка) | Worker | 0 | Hash ring / queue | 0 |
| **UX** | Retry / 409 | Ожидание | Прозрачный | Прозрачный | Retry (SSI) | Прозрачный | 500 (баг) |
| **Когда** | < 5% конфликтов | 5–50% конфликтов | Дубли запросов | Несогласованность | Критичные операции | > 1000 RPS | Всегда (страховка) |

---

## 4. Как выбирать

### Дерево решений

```
Запрос на мутацию одной записи
    │
    ├─ Может ли запрос повториться (double click, retry, timeout)?
    │   ├── ДА → Idempotency Key (всегда. Независимо от остального)
    │   └── НЕТ → ↓
    │
    ├─ Насколько часты конфликты (2+ пользователя на одну запись)?
    │   │
    │   ├── < 5% (разные пользователи, разные записи)
    │   │   → Оптимистичная блокировка (#[ORM\Version])
    │   │   → 409 + retry на клиенте
    │   │   → Простое, быстрое, масштабируемое
    │   │
    │   ├── 5–50% (один и тот же товар, популярный ресурс)
    │   │   → Пессимистичная блокировка (FOR UPDATE)
    │   │   → lock_timeout = '5s'
    │   │   → Актуальное состояние, доменная ошибка
    │   │
    │   └── > 50% (инвентарь, баланс, лимиты)
    │       → One-writer (consistent hashing / advisory lock)
    │       → + Idempotency
    │       → + CHECK constraint
    │
    ├─ Нужно ли атомарное создание связанных записей?
    │   ├── ДА (Order + Payment) → Outbox + одна транзакция
    │   └── НЕТ → ↓
    │
    └─ Критичность (финансы, инвентарь)?
        ├── Да → SERIALIZABLE или FOR UPDATE + CHECK
        └── Нет → READ COMMITTED + #[ORM\Version] достаточно
```

### Конкретные сценарии

#### Сценарий 1: Пользователь редактирует свой профиль

```
Конфликты: редкие (2+ человек на один профиль — редкость)
Критичность: средняя
Идемпотентность: да (PUT /users/me — тот же результат)

Решение:
  • #[ORM\Version] на User
  • 409 + «Данные изменились. Обновите страницу.»
  • Idempotency не нужен (PUT — идемпотентен по природе)
```

#### Сценарий 2: Покупатель оплачивает заказ

```
Конфликты: редкие (один заказ — один покупатель)
Критичность: высокая (деньги)
Идемпотентность: критична (double click!)
Атомарность: Order + Payment в одной транзакции

Решение:
  • Idempotency Key на Payment
  • #[ORM\Version] на Order
  • Одна транзакция: UPDATE orders + INSERT payments
  • CHECK (balance >= 0) как страховка
  • Stripe: idempotency key в API-вызове
```

#### Сценарий 3: Инвентарь (20 человек покупают 1 товар)

```
Конфликты: очень частые (hot row)
Критичность: высокая (overselling = деньги)
Идемпотентность: нужна

Решение:
  • FOR UPDATE на строку inventory (пессимистичная)
  • + CHECK (stock >= 0)
  • + Idempotency на order
  • lock_timeout = '5s'
  • Если 20 concurrent → 19 ждут. Каждый 50мс → 950ms. Приемлемо.
  • Если 200 concurrent → 199 × 50мс = 10с. Не приемлемо.
    → One-writer (advisory lock / очередь)
```

#### Сценарий 4: Баланс (банковская операция)

```
Конфликты: частые
Критичность: максимальная
Идемпотентность: обязательна
Атомарность: обязательна (ledger)

Решение:
  • FOR UPDATE на account
  • + CHECK (balance >= 0)
  • + Idempotency Key
  • + SERIALIZABLE для критичных операций
  • + Outbox для событий
  • + Advisory lock для горячих балансов
```

---

## 5. Комбинирование

В реальном приложении используется **несколько механизмов одновременно**:

```
┌─────────────────────────────────────────────────────────────────────┐
│  Слой 1: Idempotency Key                                           │
│  • Защита от double click / retry                                  │
│  • UNIQUE (idempotency_key) в БД                                   │
│  • Применяется всегда. К каждому write-запросу.                    │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 2: Оптимистичная / Пессимистичная блокировка                 │
│  • Защита от lost update                                           │
│  • #[ORM\Version] или FOR UPDATE                                   │
│  • Выбор по частоте конфликтов                                     │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 3: Одна транзакция (Outbox)                                  │
│  • Атомарность мутации + связанных записей                         │
│  • UPDATE orders + INSERT payments + INSERT outbox                │
│  • Применяется при создании 2+ записей                             │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 4: CHECK constraint                                          │
│  • Последний барьер. Защита от багов, мигаций, прямого SQL        │
│  • CHECK (balance >= 0), CHECK (stock >= 0)                       │
│  • Применяется всегда. Нулевая стоимость.                          │
├─────────────────────────────────────────────────────────────────────┤
│  Слой 5: One-writer (только для hot rows)                          │
│  • Advisory lock / очередь / consistent hashing                   │
│  • Применяется при > 100 RPS на одну запись                        │
└─────────────────────────────────────────────────────────────────────┘
```

**Для 90% Symfony-приложений достаточно слоёв 1 + 2 + 3 + 4.** Слой 5 — только при экстремальной нагрузке.

---

## 6. Антипаттерны

| Антипаттерн | Почему плохо |
|---|---|
| `SELECT` → бизнес-логика → `UPDATE` без `WHERE version = ?` | Lost update. Никто не знает. |
| `FOR UPDATE` вне транзации | Lock держится до `COMMIT` следующего запроса. Блокирует всех. |
| `SELECT FOR UPDATE` на 1000 строк | Блокировка всей таблицы. Дедлок. |
| `retry` без backoff (50 раз подряд) | Thundering herd. Все 50 получают 409, все 50 retry → 50 × 50 = 2500 попыток. |
| `retry` без идемпотентности | 50 retry × 1 оплата = 50 оплат. |
| `sleep(1)` в retry-loop | Блокирует PHP-воркер. 100 retry = 100с. |
| `SELECT FOR UPDATE` + `ORM` + `lazy loading` | Lazy loading **после** `FOR UPDATE` может вызвать новый `SELECT` без lock. |
| Оптимистичная блокировка + `shared: false` | Каждый `get()` — новый экземпляр. `version` не синхронизирован. |
| «У нас один сервер, конфликтов не бывает» | 2 FPM-воркера = 2 процесса. 1 воркер + 1 cron = 2 процесса. |

---

## 7. Итог

| Вопрос | Ответ |
|---|---|
| Что произойдёт без защиты? | Lost update. Тишина. Деньги из воздуха. |
| Минимальная защита? | `#[ORM\Version]` + `CHECK` + `Idempotency Key`. |
| Если конфликты частые? | `FOR UPDATE` + `lock_timeout`. |
| Если hot row (инвентарь)? | One-writer + `CHECK` + `Idempotency`. |
| Если критично (финансы)? | `SERIALIZABLE` + `FOR UPDATE` + `CHECK` + `Outbox` + `Idempotency`. |
| Что использовать всегда? | `CHECK` constraint. `Idempotency Key`. Одна транзакция на атомарную операцию. |
| Чего НЕ делать? | `SELECT` → логика → `UPDATE` без условия. `retry` без backoff. `sleep()` в retry. |

**Одна фраза:** блокировка решает «кто пишет», но не «что писать». `CHECK` решает «что допустимо». `Idempotency` решает «сколько раз». Все три нужны. Ни один не заменяет другой.

> ⏱ 58.3s · prompt 111290 tok · output 6308 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

The user is asking me to describe my git process from task definition to production: branching, commit format, pull requests, code review, releases. And to explain why each choice is made that way and not otherwise.

This is a continuation of a long conversation about PHP/Symfony architecture. I've been providing detailed, structured answers with tables, code examples, and trade-off analysis. The user is clearly a senior-level developer or someone preparing for a senior-level interview.

Let me think about what to cover:

1. Branching strategy - why I'd choose trunk-based over Git Flow, or vice versa. The reality of mid-size Symfony apps.
2. Commit format - Conventional Commits, why, what to skip
3. Pull requests - size, structure, description template
4. Code review - what to look for, process, etiquette
5. Releases - how to cut, versioning, CHANGELOG
6. The "why" behind each choice

I should be honest about trade-offs. Trunk-based is better in theory but requires CI/CD maturity. Git Flow is simpler for small teams. The reality is often a hybrid.

Let me structure this clearly, with the "why" being the key differentiator. The user has been asking "why" in every question, so I should lean into that.

I'll write in Russian as the conversation has been in Russian.

Let me think about the actual content:

**Branching:**
- Trunk-based development is the gold standard but requires: feature flags, good CI, small batches
- For a team of 3-8 in a Symfony monolith: trunk-based with short-lived feature branches (max 2 days)
- NOT Git Flow (develop/master/release/hotfix) - too much overhead for a monolith
- Why not pure trunk (no branches at all)? Because a 3-day feature can't land in a half-done state without feature flags
- Why not long-lived feature branches? Merge conflicts, divergence, "integration hell"

**Commits:**
- Conventional Commits: `feat:`, `fix:`, `refactor:`, `chore:`, `docs:`, `test:`, `perf:`
- Why? Because it drives CHANGELOG generation and semantic versioning
- But: don't be a robot. The body can explain WHY
- Squash on merge? Or rebase? I'd say: individual commits on the branch, squash on merge to main. Why? Because the branch commits are "work in progress" and don't need to be meaningful individually. The squash commit is the unit of change.
- Why not rebase (keep individual commits)? Because 12 commits for "add payment module" is noise in git log. One "feat: add payment module" is cleaner.
- Why not merge commits? Because they create merge bubbles in history and make bisect harder.

**PRs:**
- Size: < 400 lines of changed code (excluding tests, configs)
- Why? Because review quality drops sharply above ~300 lines. Studies show > 400 lines → more bugs in review.
- Structure: what, why, how to test, screenshot if UI
- Template
- CI must be green before review
- Why not "send it and we'll look"? Because the reviewer's time is the most expensive resource

**Code review:**
- Two levels: automated (CI) and human
- What to look for: architecture, edge cases, security, readability
- What NOT to look for: style (that's CS Fixer), variable names (mostly)
- Process: reviewer has 48h SLA, author responds within 24h
- Why not "one person approves"? Because rubber-stamping happens with one approver
- Why not "two approvers"? For a small team it's fine. For large orgs, two is minimum.
- Etiquette: "could we consider..." not "this is wrong"

**Releases:**
- For a Symfony monolith: deploy = git tag = version
- SemVer: MAJOR (breaking API), MINOR (new feature), PATCH (bugfix)
- Actually for internal apps: just date-based or sequential. SemVer is for public packages.
- CHANGELOG: generated from Conventional Commits
- Hotfix process: branch from main, fix, tag, deploy, merge back to main (or if trunk-based, just fix on main)
- Why not release branches? Because for a monolith, a release branch diverges from main and creates maintenance burden. The "release branch" is the same as "main with some cherry-picks."
- Deployment: blue-green or canary? For a Symfony app: usually just rolling deploy (stop, deploy, start) or blue-green if you have the infra.

**CI/CD pipeline:**
- Every PR: PHPStan, PHPUnit, CS Fixer
- On merge to main: build Docker image, deploy to staging
- On tag: deploy to production
- Why? Because "it works on my machine" is not a release strategy.

Let me also think about the "why not" for each:

**Why not Git Flow?**
- 4 branch types = cognitive overhead
- "Release branches" for a monolith = cherry-picking hell
- For a team of 5, the overhead exceeds the benefit
- Git Flow was designed for software released on CD (desktop apps). Web apps deploy daily.

**Why not pure trunk (no branches)?**
- Requires feature flags for EVERY unfinished feature
- A 3-day feature can't be merged in pieces without flags
- Small teams don't have the discipline for "merge small, often"
- But: I'd move toward it as the team matures

**Why not "merge to develop, then develop to master"?**
- The "develop" branch is a zombie. It exists but nobody works on it.
- It creates a false sense of "staging." Real staging is a deployment environment.
- Two long-lived branches = two sources of truth = divergence

**Why squash on merge?**
- The individual commits on a branch are "thought process"
- The squashed commit is the "decision"
- git log main = history of decisions, not history of keystrokes
- BUT: I'd allow rebasing if the commits are genuinely meaningful (e.g., "add schema", "add service", "add controller", "add tests" — each is a logical unit)

Actually, let me reconsider. The "why squash" vs "why not" is debatable. Let me present both sides and give my preference with reasoning.

Let me structure the answer now. I'll follow the pattern from previous answers: clear sections, tables for comparison, code examples where relevant, "why" explanations, anti-patterns, and a summary.

</details>

**Кандидат:**

# Git-процесс: от задачи до продакшена

## 0. Контекст

Команда из 3–8 разработчиков. Symfony-монорепозиторий. PostgreSQL. Деплой 1–3 раза в неделю. CI на GitHub Actions.

Это не стартап на 2 человек (там — один `main` и `git push`), и не корпорация на 200 человек (там — GitOps, ArgoCD, policy engines). Процесс должен быть **достаточно строгим**, чтобы не сломать продакшен, и **достаточно лёгким**, чтобы не парализовать разработку.

---

## 1. Ветвление

### Стратегия: trunk-based development с короткими фиче-ветками

```
main  ←──────────────────────────────────────────────────
 ↑    ↑    ↑    ↑    ↑
 │    │    │    │    │
 │    │    │    │    └─ fix: correct tax calculation (1 commit, 40 строк)
 │    │    │    └───────── feat: add gift card payment (squash, 320 строк)
 │    │    └─────────────── refactor: extract OrderLine to VO (squash, 180 строк)
 │    └──────────────────── feat: payment retry logic (squash, 450 строк)
 └───────────────────────── chore: upgrade Symfony 7.1 (squash, 60 строк)

feature/payment-retry     ← живёт 1–2 дня, максимум 3
feature/extract-vo        ← живёт 1 день
fix/tax-calculation       ← живёт несколько часов
```

**Правила:**

| Правило | Почему |
|---|---|
| Одна ветка = одна задача из трекера | Если 2 задачи — 2 ветки. Иначе конфликт при мерже |
| Ветка живёт **максимум 3 дня** | После 3 дней — дивергенция от `main`, конфликты, потеря контекста |
| Каждый день: `git fetch && git rebase main` | Держим ветку на актуальном `main`. Не копим конфликты |
| Ветка создаётся **до** начала работы, не после | `git checkout -b feature/x` → работа → PR. Не «я сделал и теперь создаю ветку» |
| `main` всегда деплоится (или может) | Если `main` сломан — это инцидент, а не «ну, develop же есть» |
| Нет `develop`, нет `release/*`, нет `hotfix/*` | Разберём ниже |

### Почему не Git Flow

Git Flow (`master → develop → release/* → hotfix/*`) — это модель для **десктопных приложений**, выпускаемых на CD-дисках раз в квартал. Для веб-приложения с деплоем 2–3 раза в неделю:

| Проблема Git Flow | Деталь |
|---|---|
| `develop` — зомби-ветка | Все работают на `feature/*`, мержат в `develop`. Но `develop` ≠ `main`. Два источника истины. |
| `release/*` — cherry-pick аду | Батч на `release/1.4`. Найден баг. Фикс идёт на `release/1.4` **и** на `develop` **и** на `main`. Три раза. |
| `hotfix/*` — раздвоение | Фикс на `hotfix/1.4.1`. Мерж в `master`. Мерж в `develop`. А `develop` уже ушёл вперёд. Конфликт. |
| 4 типа веток = 4 правила | Мозг разработчика тратит ресурс на «куда мержить?», а не на код |
| «Staging» = ветка | Нет. Staging — это **деплой-окружение**. Ветка не заменяет окружение. |

**Git Flow оправдан**, если:
- Продукт выпускается раз в 2–3 месяца (мобильное приложение, desktop).
- Команда 20+ человек, и «неаккуратный мерж в main» — катастрофа.
- Есть жёсткий SLA: «релиз 15-го числа, точка».

Для Symfony-монорепозитория с деплоем 2–3 раза в неделю — **нет**.

### Почему не чистый trunk (без веток вообще)

Чистый trunk — «пишешь прямо в `main`, каждый коммит — деплой». Теоретически идеально. На практике:

- Фича на 3 дня. Первый день — полусделанный контроллер. Второй день — сервис. Третий день — тесты. Если мержить каждый день — `main` содержит **неработающий** код.
- Нужны **feature flags** на каждую незавершённую фичу. А feature flags — это инфраструктура, конфигурация, тесты, документация.
- Для команды из 5 человек, где не все пишут фичи по 200 строк — это **overhead**, который не окупается.

**Компромисс:** короткие ветки (1–3 дня) + squash. Фича живёт в ветке, пока не готова. Потом — squash в `main`. По сути, trunk-based с «коротким буфером».

### Hotfix

```
main  ──●──●──●──●──●──●──●──●──●──●──●──●
                          │
                          │ (продакшен сломался)
                          │
main  ──●──●──●──●──●──●──●──●──●──●──●──●──●──
                              ↑
                         hotfix/tax-overflow
                         (1 commit, 15 строк, PR, merge, deploy)
```

**Процесс:**
1. Создаём `hotfix/tax-overflow` из `main`.
2. Фиксим. Тестируем.
3. PR → 1 ревьюер (срочный, SLA 2 часа) → merge в `main`.
4. Деплой.
5. Ветка удаляется.

**Почему не `hotfix`-ветка от `release/*`:** потому что `release/*` нет. `main` — единственный источник истины.

**Почему не «вручную на сервере `sed`»:** потому что это будет забыто, не будет в `git log`, и через месяц кто-то перепишет этот код и баг вернётся.

---

## 2. Формат коммитов

### Conventional Commits

```
<type>: <short description>

[optional body]

[optional footer: Closes #123, Breaking Change: …]
```

**Типы:**

| Type | Когда | Пример |
|---|---|---|
| `feat` | Новая функциональность | `feat: add gift card payment method` |
| `fix` | Исправление бага | `fix: calculate VAT on discounted amount` |
| `refactor` | Перестройка без изменения поведения | `refactor: extract OrderLine to value object` |
| `perf` | Оптимизация | `perf: add index on orders(customer_id, status)` |
| `test` | Только тесты | `test: cover Order::cancel() edge cases` |
| `docs` | Только документация | `docs: describe payment flow in README` |
| `chore` | Инфраструктура, конфиг, зависимости | `chore: upgrade Symfony to 7.1` |
| `style` | Форматирование (редко, обычно CS Fixer) | — |
| `ci` | CI/CD конфиг | `ci: add PHPStan level 8 to pipeline` |

**Scope (опционально):**

```
feat(payment): add idempotency key support
fix(order): prevent double-shipment on retry
```

### Примеры

**Хороший:**
```
feat(order): add order cancellation with refund

Cancelling a confirmed order now creates a Payment refund
record and emits OrderWasCancelled event. The refund is
processed asynchronously via the outbox publisher.

Closes #247
```

**Плохой:**
```
fix stuff
```

**Плохой:**
```
Update OrderService.php, PaymentGateway.php, OrderController.php
and a few tests. Also fixed the CI pipeline and updated
composer.json.
```

### Squash on merge

Ветка содержит 5–15 коммитов («draft», «WIP», «fix test», «add missing import», «rebase»). В `main` попадает **один** squash-коммит:

```
feat(payment): add Stripe payment with idempotency

Implements charge and refund via Stripe API. Adds
idempotency key to prevent double-charging on retry.
Payment events published via transactional outbox.

Closes #142
```

**Почему squash, а не rebase (сохранить все коммиты):**

| | Squash | Rebase (все коммиты) |
|---|---|---|
| `git log main` | 1 запись = 1 решение | 12 записей = 12 «шагов мысли» |
| `git bisect` | 1 шаг | 12 шагов (медленнее) |
| Реверс | `git revert <1 commit>` — чисто | `git revert` 12 коммитов — хрупко |
| Смысл | «Что изменилось» | «Как я к этому пришёл» |
| Истории | Чисто | Шумно |

`main` — это **история решений**, не история клавиатуры. Squash превращает «мой рабочий процесс» в «что изменилось в системе».

**Исключение:** если коммиты в ветке **осознанно** логичны и атомарные («add schema», «add service», «add controller», «add tests»), можно rebase. Но это редкость. В 90% случаев коммиты — это «draft → fix → fix → done».

### Почему Conventional Commits, а не «свободный формат»

| Причина | Деталь |
|---|---|
| Автоматический CHANGELOG | `conventional-changelog` генерирует из `feat:` / `fix:` / `BREAKING CHANGE:` |
| Semantic Versioning | `feat` → MINOR. `fix` → PATCH. `BREAKING CHANGE` → MAJOR |
| Фильтрация в CI | `git log --oneline --grep='^fix:' main` → все багфиксы за месяц |
| Единый язык | `feat:` и `new feature` — одно и то же. `fix:` и `bugfix` — одно и то же. Conventional — один стандарт |
| Ревью | Ревьюер видит `refactor:` — знает, что поведение не меняется, можно смотреть быстрее. `feat:` — смотрит внимательнее |

**Чего НЕ требует Conventional Commits:**
- Не требует `!` для breaking changes (можно через footer `BREAKING CHANGE:`).
- Не требует scope.
- Не требует body.
- Не требует ссылки на задачу.

Это **минимальный договор**, а не бюрократия.

---

## 3. Pull Request

### Размер

| Размер (изменённых строк) | Ревью |
|---|---|
| < 100 | 10–15 минут. Быстро, качественно. |
| 100–400 | 30–60 минут. Нормально. |
| 400–800 | 2–3 часа. Ревьюер устаёт. Качество падает. |
| > 800 | Половина изменений не будет прочитана. **Не слать.** |

**Почему 400 — граница:**

Исследования (Google, 2023) показывают: выше ~400 строк вероятность пропуска бага в ревью растёт нелинейно. Ревьюер читает первые 200 строк внимательно, следующие 200 — по диагонали, остаток — «лут, мержу».

**Что делать, если фича на 1500 строк:**

```
1. feat: add Payment entity and repository (200 строк)
2. feat: add StripePaymentGateway adapter (300 строк)
3. feat: add PaymentService with idempotency (250 строк)
4. feat: add OrderController /payments endpoint (150 строк)
5. test: cover PaymentService edge cases (200 строк)
```

5 PR'ов по 150–300 строк. Каждый мержится в `main` отдельно. Каждый — рабочая система.

**Но:** если шаг 4 без шага 2 — `main` сломан. Тогда:
- Feature flag: `if ($this->featureFlags->enabled('stripe_payment'))`.
- Или: один PR на 800 строк, но с **подробным** описанием и **двумя** ревьюерами.

### Шаблон PR

```markdown
## Что

Кратко: что изменилось. 1–3 предложения.

Добавлена оплата через Stripe с idempotency key.
Повторный запрос с тем же ключом возвращает существующий платёж.

## Зачем

Пользователи при double-click оплачивали заказ дважды (#142).
Stripe не имеет встроенной idempotency на уровне API-вызова.

## Как проверить

1. `docker compose up`
2. `php bin/console app:seed`
3. Открыть `/orders/o-1/checkout`
4. Нажать «Оплатить» дважды быстро
5. В БД: `SELECT * FROM payments WHERE order_id = 'o-1'` → 1 строка

## Изменения

- `src/Domain/Payment/Payment.php` — добавлен `idempotencyKey`
- `src/Infrastructure/.../StripePaymentGateway.php` — новый адаптер
- `src/Infrastructure/.../PaymentRecord.php` — колонка `idempotency_key` UNIQUE
- `src/Application/Payment/PaymentService.php` — логика idempotency
- `tests/.../PaymentServiceTest.php` — 6 новых тестов
- `migrations/Version20250115103000.php` — миграция

## Риски

- Stripe API может вернуть 409 на повторный запрос. Обработано.
- Миграция: `UNIQUE` на `idempotency_key`. Если в БД есть дубли — миграция упадёт.
  Проверено на стейдже: дублей нет.

## Скриншоты / запись

(если UI)
```

**Почему так:**

| Секция | Зачем |
|---|---|
| Что + Зачем | Ревьюер тратит 30 секунд на понимание контекста. Без этого — 5 минут на чтение кода «а что тут вообще происходит» |
| Как проверить | Ревьюер **может** прогнать. Не «доверься, что тесты зелёные» |
| Изменения | Чек-лист. Ревьюер видит, что **не** изменилось |
| Риски | Ревьюер знает, где больно. Фокусируется |
| Скриншоты | Для UI. Не для API (там — curl-пример или Postman-коллекция) |

### CI до ревью

```
PR создан
  ↓
CI стартует (автоматически)
  ├── PHPStan level 8
  ├── PHPUnit (все тесты)
  ├── CS Fixer (dry-run)
  ├── Компоненты: composer install
  └── Миграции: doctrine:migrations:migrate (на тестовой БД)
  ↓
Все зелёные → бот ставит ✅
  ↓
Ревьюер начинает читать
```

**Правило: ревьюер не читает код, пока CI не зелёный.**

**Почему:** если PHPStan нашёл 12 ошибок — ревьюер тратит время на «а это баг или нет?», а не на «правильная ли архитектура?». CI ловит синтаксис, типы, форматы. Ревьюер ловит логику, границы, безопасность.

**Исключение:** если CI падает на **тесте, который сломал ваш PR** — чините. Если падает на чужом тесте (flaky, broken main) — пинг на канал, не ждите.

---

## 4. Ревью

### Кто ревьюит

| Размер PR | Ревьюеры |
|---|---|
| < 200 строк | 1 ревьюер |
| 200–500 строк | 1 ревьюер + автор может попросить второго |
| > 500 строк или архитектура | 2 ревьюера |
| Hotfix (срочный) | 1 ревьюер, SLA 2 часа |
| Миграции БД | 1 ревьюер + DBA (если есть) |

**Почему не «автор = ревьюер»:** потому что автор знает, что он **хотел** сделать. Ревьюер знает, что **получилось**. Разные вопросы.

**Почему не «3 ревьюера на каждый PR»:** потому что 3 × 40 минут = 2 часа ревью на PR на 200 строк. Для команды из 5 человек — это 2 часа × 20 PR'ов в месяц = 40 часов ревью в месяц. Это 10% рабочего времени. Окупается только для критичного кода.

### Что ревьюер ищет (приоритет)

```
1. Безопасность        → SQL injection, XSS, mass assignment, CORS
2. Правильность логики → edge cases, race conditions, null
3. Архитектура         → границы слоёв, SOLID, DDD-границы
4. Производительность  → N+1, лишний I/O, отсутствие индекса
5. Тестируемость       → можно ли это покрыть тестом?
6. Читаемость          → имена, структура, комментарий
7. Стиль              → (обычно CS Fixer, не человек)
```

**Что ревьюер НЕ ищет:**
- Отступы, запятые, `use`-порядок. CS Fixer.
- «Почему `int` а не `float`?» (если контекст позволяет — ок).
- «Мне нравится `for` вместо `foreach`». Вкус. Не ревью.

### Процесс ревью

```
Ревьюер получает уведомление
    │
    ├── 1. Читает PR-описание (2 мин)
    ├── 2. Смотрит diff, не код (5 мин) — что изменилось
    ├── 3. Гоняет локально / на стейдже (10–30 мин)
    ├── 4. Читает код (20–60 мин)
    ├── 5. Комментирует
    │
    └── Вердикт:
        ✅ Approve — «Мержи»
        💬 Comment — вопросы, не блокирующие
        ❌ Request Changes — конкретные, исправимые пункты
```

### Формат комментариев

| Хорошо | Плохо | Почему |
|---|---|---|
| «Здесь можно использовать `match` вместо `switch` — проще и исчерпывающий» | «Здесь switch — плохо» | Конкретика + альтернатива |
| «Что если `$lines` пустой? `recalculateTotal()` вернёт 0, а `confirm()` бросит `EmptyOrderException`. Это ожидаемо?» | «Обработай edge case» | Вопрос, а не приказ |
| «Предлагаю вынести в `PaymentGateway` — сейчас `Stripe` привязан к сервису» | «Это не DI» | Объяснение + решение |
| `[nit] Может, `id` → `orderId`? Для консистентности с API» | «Переименуй» | `[nit]` = не блокирует |
| «Проверь, что `idempotency_key` проверяется ДО `charge()`. Сейчас после — двойная оплата при гонке» | «Баг» | Где, что, почему |

### SLA

| Роль | SLA |
|---|---|
| Ревьюер: первый комментарий | **8 рабочих часов** (1 рабочий день) |
| Ревьюер: final approve/reject | **24 рабочих часа** с последнего комментария |
| Автор: ответ на комментарий | **8 рабочих часов** |
| Автор: push с исправлениями | **8 рабочих часов** |

**Почему SLA:** без SLA PR живёт 3 недели в «ревью». 3 недели — это потерянный контекст, дивергенция от `main`, потеря мотивации. 8 часов — достаточное время, чтобы прочесть 300 строк.

**Что если SLA нарушен:** автор пингует в канал. Не «здравствуйте, вы посмотрели?». А «@ivan PR #142 ждёт ревью 2-й день. Могу помочь с контекстом?»

### Что делать с «Request Changes»

```
Ревьюер: "Здесь нужно использовать UnitOfWork, а не flush напрямую"
Автор:   "Хорошо, исправляю"  →  push  →  "Готово"
Ревьюер: approve
```

**Не** делать:

```
Ревьюер: "Здесь нужно UnitOfWork"
Автор:   "А можно и так, работает же"
Ревьюер: "Но в остальных сервисах UnitOfWork"
Автор:   "Ну ладно, поправлю"
```

**Почему:** спор в PR — это не «кто прав». Это «какой стандарт у проекта». Если стандарт — `UnitOfWork`, то `UnitOfWork`. Можно обсудить стандарт **отдельно** (митап, RFC), но в конкретном PR — следуйте стандарту. Спорите после, не во время.

**Если вы (автор) не согласны:**

```
Автор: "Согласен с проблемой, но UnitOfWork тут избыточен, т.к. операция атомарна по одной записи.
       Предлагаю оставить flush, но добавить комментарий. Если есть возражения — обсудим на митапе."
```

Не «нет, так правильно». А «понимаю проблему, вот моя альтернатива, давайте решим».

---

## 5. Релизы

### Версионирование

Для **внутреннего** Symfony-приложения:

```
1.0.0  → первый стабильный релиз
1.1.0  → новая фича (payment methods)
1.1.1  → багфикс (tax calculation)
1.2.0  → новая фича (order export)
2.0.0  → breaking: переделана модель Order (API-контракт изменён)
```

**Правило:**

| Что | Версия |
|---|---|
| Новая фича, обратно совместимая | MINOR + 1 |
| Багфикс | PATCH + 1 |
| Breaking change (API, схема БД, поведение) | MAJOR + 1, MINOR = 0 |

**Почему не «1.0.1, 1.0.2, 1.0.3» для всего:** потому что через 6 месяцев никто не вспомнит, что 1.0.7 — это «добавили gift card», а 1.0.8 — «сломали tax». SemVer несёт смысл.

**Для публичного пакета** (если вы пишете bundle): SemVer обязателен. `composer.json` требует.

**Для внутреннего приложения:** SemVer — **рекомендация**, не обязанность. Но он бесплатный (бесплатно = тег в git).

### Процесс релиза

```
1. Все PR'ы смёржены в main. CI зелёный.
2. git tag -a v1.2.0 -m "Release 1.2.0: order export, payment retry"
3. git push origin v1.2.0
4. CI: build Docker image → push в registry
5. Deploy: docker compose pull && docker compose up -d (staging)
6. Smoke test: 5 ключевых сценариев
7. Deploy: docker compose pull && docker compose up -d (production)
8. Мониторинг: 30 минут — ошибки, latency, queue depth
9. Done
```

**Чек-лист перед деплоем:**

```
[ ] Все миграции применены на стейдже
[ ] Миграции обратимые (или документированы)
[ ] ENV-переменные обновлены в .env (staging + production)
[ ] Feature flags выключены (если фича не 100%)
[ ] Queue (RabbitMQ/Redis) очищена или готова
[ ] Бэкап БД сделан (pg_dump)
[ ] Rollback план: docker compose up -d --force-recreate (предыдущий образ)
[ ] Кто на дежурстве 30 минут после деплоя
```

### Rollback

```bash
# Быстрый rollback (Docker):
docker compose up -d --force-recreate --no-deps app
# → предыдущий образ из registry

# Если миграция broke:
php bin/console doctrine:migrations:version -F  # откатить
# Или: pg_restore (если миграция была DDL)
```

**Почему бэкап БД обязателен:** миграция может быть не обратимой. `DROP COLUMN` — не revert'ится. `ALTER TYPE` — не revert'ится. Без бэкапа — данные потеряны.

### CHANGELOG

```markdown
# Changelog

## [1.2.0] - 2025-01-20

### Added
- Order export to CSV (#256)
- Payment retry with exponential backoff (#142)

### Fixed
- VAT calculation on discounted amounts (#261)
- Double-shipping on concurrent requests (#270)

## [1.1.1] - 2025-01-15

### Fixed
- Idempotency key not enforced on concurrent requests (#247)

## [1.1.0] - 2025-01-10

### Added
- Gift card payment method (#230)
- Payment status tracking in dashboard (#231)
```

Генерация: `conventional-changelog` + `standard-version`. Или вручную — для 5–10 релизов в год это 10 минут.

---

## 6. CI/CD pipeline

```yaml
# Упрощённо. Реальный — в .github/workflows/
name: CI

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]
  release:
    types: [published]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - checkout
      - setup-php 8.3
      - composer install
      - php-cs-fixer fix --dry-run --diff
      - phpstan analyse --level=8
      - phpunit --testsuite=domain --processes=4
      - phpunit --testsuite=application --processes=4

  integration:
    runs-on: ubuntu-latest
    services:
      postgres: { image: postgres:16, ports: [5432:5432] }
    steps:
      - checkout, setup, composer
      - doctrine:schema:create --env=test
      - phpunit --testsuite=infrastructure --processes=2
      - phpunit --testsuite=interface

  build:
    if: github.ref == 'refs/heads/main'
    needs: [quality, integration]
    runs-on: ubuntu-latest
    steps:
      - docker build -t registry/app:${{ github.sha }}
      - docker push registry/app:${{ github.sha }}
      - docker tag registry/app:${{ github.sha }} registry/app:latest
      - docker push registry/app:latest

  deploy-staging:
    if: github.ref == 'refs/heads/main'
    needs: [build]
    runs-on: ubuntu-latest
    environment: staging
    steps:
      - ssh: docker compose pull && docker compose up -d

  deploy-production:
    if: github.event_name == 'release'
    needs: [build]
    runs-on: ubuntu-latest
    environment: production
    steps:
      - ssh: docker compose pull && docker compose up -d
      - smoke test
```

**Почему:**

| Этап | Зачем |
|---|---|
| `pull_request` → quality + integration | Ревьюер не читает код с красным CI |
| `push main` → build + staging | `main` всегда деплоится на стейдинг. Автоматически. |
| `release` → production | Продакшен деплоится **только** по тегу. Не по `main`. |

**Почему staging ≠ production по триггеру:** потому что на стейджинге можно сломаться и это ок. На продакшене — нет. Тег — это осознанное решение «деплоим».

---

## 7. Антипаттерны

| Антипаттерн | Почему плохо |
|---|---|
| Ветка `feature/big-rewrite` живёт 6 недель | Дивергенция. Конфликты. Потеря контекста. |
| Коммит `fix` без описания | Через месяц: «что за фикс? зачем? по какому багу?» |
| PR на 2000 строк «всё сразу» | Никто не прочитает. Половина багов — в проде. |
| «LGTM» без прочтения | Формальность. Ревью не произошло. |
| Мерж без зелёного CI | «Ну, один тест упал, ладно». Через 3 месяца — 10 упавших тестов. |
| Деплой по `main` без тега | «Кто это задеплоил? Почему? Что в релизе?» |
| Rollback = `git revert` + деплой | Если миграция broke — `git revert` не откатит миграцию. Нужен `pg_restore`. |
| Hotfix на ветке `release/1.1` | Ветки `release/*` нет. Фиксим на `main`. |
| «Подождём, пока все фичи не будут готовы, и тогда релиз» | 3 месяца без деплоя. 3 месяца дивергенции. 1 огромный релиз. |
| Ревью через 3 дня | Контекст потерян. Ревьюер читает чужой код, а не свой. |

---

## 8. Итог

```
┌─────────────────────────────────────────────────────────────────────────┐
│  ЗАДАЧА → ВЕТКА (1–3 дня) → КОММИТЫ (Conventional)                    │
│    → PR (< 400 строк, шаблон, CI зелёный)                              │
│    → РЕВЬЮ (8ч SLA, 1–2 ревьюера, конкретные комментарии)             │
│    → SQUASH MERGE в main                                               │
│    → CI: build → staging                                               │
│    → ТЕГ → CI: deploy production → мониторинг 30 мин                  │
│                                                                         │
│  ПРАВИЛА:                                                              │
│  • main всегда деплоится (staging)                                      │
│  • Production — только по тегу                                          │
│  • Ветка < 3 дня, PR < 400 строк                                        │
│  • Conventional Commits → CHANGELOG → SemVer                           │
│  • Нет develop, release/*, hotfix/*                                     │
│  • CI до ревью. SLA до мержа.                                           │
└─────────────────────────────────────────────────────────────────────────┘
```

**Почему именно так, а не иначе:**

Каждое правило — это компромисс между **безопасностью** (не сломать прод) и **скоростью** (не парализовать команду). Git Flow — безопаснее, но медленнее. Чистый trunk — быстрее, но требует дисциплины. Trunk-based с короткими ветками — **достаточно безопасно** (CI, ревью, тег) и **достаточно быстро** (ветка 1–3 дня, PR 400 строк, squash). Для команды из 5 человек на Symfony-монорепозитории — это sweet spot.

> ⏱ 95.5s · prompt 117228 tok · output 9061 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

The user is asking about my approach to code review: how I mark comments, what I prioritize, and how I handle disagreements with the author. This is a continuation of our long conversation about PHP/Symfony development. Let me think about what makes a genuinely good code review response here.

Key aspects to cover:
1. How I mark/categorize comments (severity levels, prefixes)
2. What I look at first (priority order)
3. How I handle disagreements with the author

I should be concrete, practical, and give real examples. The user has been asking increasingly specific operational questions throughout this conversation - they want actionable detail, not abstract principles.

Let me think about what's actually true and useful here:

**Marking comments:**
- Severity: blocking vs. suggestion vs. nit vs. question
- Prefix conventions: [blocker], [bug], [suggestion], [nit], [question], [learning]
- Why categorization matters: it tells the author what MUST be fixed vs. what's optional
- The anti-pattern of 15 comments with no priority

**What I look at first (priority):**
1. Correctness (will it break in prod?)
2. Security
3. Data integrity
4. Architecture boundaries
5. Edge cases
6. Testability
7. Readability
8. Style

**Disagreements:**
- The "taste" vs. "correctness" distinction
- How to disagree without being dismissive
- The "agree to disagree" protocol
- When to escalate
- The 2-pizza rule for disagreements
- Don't make it personal
- Separate "what" from "how"
- The author has more context than the reviewer

Let me structure this well. I'll use concrete examples with actual code snippets since the user has responded well to code examples throughout this conversation.

I should be honest about trade-offs and not present a single "correct" way. Different teams work differently.

Let me write this in Russian as the conversation has been in Russian.

</details>

**Кандидат:**

# Код-ревью: практика, приоритеты, конфликты

## 1. Маркировка замечаний

### Почему маркировка нужна

15 комментариев в PR без приоритетов = 15 «нужно исправить». Автор тратит 4 часа на 3 критичных и 11 косметических. Или наоборот: тратит 2 часа на 11 косметических и пропускает 3 критичных. Маркировка = **договор о том, что обязательно, а что нет**.

### Моя система

| Префикс | Значение | Действие автора |
|---|---|---|
| `[blocker]` | Не мержить. Баг, бабезопасность, нарушение инварианта. | **Обязательно** исправить до мержа |
| `[bug]` | Логическая ошибка. Не обязательно упадёт, но даст неверный результат. | **Обязательно** исправить до мержа |
| `[security]` | Уязвимость. | **Обязательно**. Эскалация, если не решено за 1 час |
| `[question]` | Не уверен, что это баг. Спрашиваю. | Ответить. Может быть — ничего не менять |
| `[suggestion]` | Есть лучший способ. Но текущий работает. | На усмотрение автора |
| `[nit]` | Стиль, имя, порядок. Не влияет на логику. | На усмотрение автора. Можно игнорировать |
| `[learning]` | Не замечание. Контекст, ссылка, объяснение. | Читать. Делать ничего не нужно |

### Пример в действии

```php
// src/Service/OrderService.php

public function confirm(string $id): Order
{
    $order = $this->repo->findById($id);                          // [blocker] $order может быть null. null->confirm() → Fatal
    $order->confirm();
    $this->repo->save($order);
    $this->em->flush();                                           // [suggestion] wrapInTransaction. Сейчас flush без транзакции
    $this->mailer->send($order->customer->email, ...);            // [question] Если mailer упадёт — заказ подтверждён, но письмо не отправлено. Это ок?
    return $order;
}
```

### Как я формулирую каждый тип

**`[blocker]`** — конкретно, с причиной:

```
[blocker] $order будет null при несуществующем id.
null->confirm() → Fatal Error: Call to a member function confirm() on null.

Fix:
$order = $this->repo->findById($id)
    ?? throw new OrderNotFoundException($id);
```

**`[suggestion]`** — альтернатива + почему:

```
[suggestion] Сейчас flush() без транзакции. Если save() прошёл,
а что-то после flush() упадёт — данные в БД, но сервис вернул 500.

Предлагаю:
$this->em->wrapInTransaction(function () use ($order): void {
    $this->repo->save($order);
    $this->em->flush();
});
```

**`[nit]`** — мягко, с `[nit]`:

```
[nit] Метод confirm() принимает $id (string). В других сервисах
мы передаём Order object. Может, стоит унифицировать? Не блокирует.
```

**`[learning]`** — без «ты плохо написал»:

```
[learning] Для справки: #[ORM\Version] на сущности автоматически
добавляет WHERE version = ? в UPDATE. Если у вас нет колонки
version в Order — добавьте. Защита от concurrent confirm.
Документация: docs.sulu.dev / ORM / Version
```

### Чего НЕ делаю

| Антипаттерн | Почему |
|---|---|
| 15 комментариев без приоритетов | Автор не знает, что срочно, а что нет |
| «LGTM» в каждом PR | Формальность. Ревью не произошло |
| «Почему ты не сделал X?» | Обвинение. Лучше: «Предлагаю X, потому что Y» |
| «Это неправильно» | Без альтернативы. Лучше: «Предлагаю Z» |
| 30 `[nit]` на PR из 200 строк | Шум. Автор устал. Игнорирует и `[blocker]` |
| Коммент «+1» под чужим комментом | Не добавляет ценности. Убирает |
| «Обсуди с тимлидом» | Перекладывание ответственности. Лучше: своё мнение + «если не согласен — обсудим» |

---

## 2. Что смотрю в первую очередь

### Порядок чтения PR

```
1. PR-описание (2 мин)
   → Что? Зачем? Как проверить?
   → Если «Что» непонятно — комментарий на описание, не на код

2. Diff-статистика (1 мин)
   → Сколько файлов, сколько строк
   → Какие слои затронуты
   → Если > 400 строк — «Давайте разобьём?»

3. Тесты (5 мин)
   → Есть ли тесты на новое поведение?
   → Проверяют ли edge cases?
   → Если тестов нет — [blocker]

4. Код, от наиболее критичного к наименее (20–60 мин)
   → см. приоритеты ниже

5. Миграции / конфиг / DI (5 мин)
   → Обратимы ли?
   → Новые ENV-переменные?
   → services.yaml: новый сервис, алиас?
```

### Приоритеты внутри кода

```
┌─────────────────────────────────────────────────────────────────┐
│  1. КОРРЕКТНОСТЬ                                               │
│  • Null safety: что если null? что если пустой массив?          │
│  • Типы: int vs float, string vs int в JSON                    │
│  • Конкурентность: что если два запроса одновременно?           │
│  • Транзакции: flush без commit? две транзакции вместо одной?   │
│  • Boundary: что на границе (0, -1, MAX_INT, empty string)?    │
├─────────────────────────────────────────────────────────────────┤
│  2. БЕЗОПАСНОСТЬ                                               │
│  • Mass assignment: $request->all() → entity?                  │
│  • SQL injection: DQL с конкатенацией? (редко, но бывает)      │
│  • Authz: проверка прав ДО действия?                           │
│  • PII: email, телефон в логах?                                │
│  • CORS / CSRF                                                 │
├─────────────────────────────────────────────────────────────────┤
│  3. ДАННЫЕ                                                     │
│  • Миграция: обратима? Что если упадёт на середине?           │
│  • UNIQUE / FK: есть ли защита в БД?                           │
│  • N+1: JOIN FETCH? Partial object?                            │
│  • Индексы: WHERE / ORDER BY покрыты?                          │
├─────────────────────────────────────────────────────────────────┤
│  4. АРХИТЕКТУРА                                                │
│  • Границы слоёв: Domain не знает Doctrine?                    │
│  • SOLID: SRP, OCP, DIP                                        │
│  • Дублирование: этот код уже есть в другом месте?             │
│  • Расширяемость: добавление нового типа = правка switch?      │
├─────────────────────────────────────────────────────────────────┤
│  5. ТЕСТИРУЕМОСТЬ                                              │
│  • Можно ли юнит-тестнуть без БД?                              │
│  • Мокаются ли порты?                                          │
│  • Есть ли тест на edge case?                                  │
├─────────────────────────────────────────────────────────────────┤
│  6. ЧИТАЕМОСТЬ                                                 │
│  • Имена: говорят ли о домене?                                  │
│  • Длина метода: > 30 строк — можно разбить?                   │
│  • Комментарии: объясняют ПОЧЕМУ, а не ЧТО?                    │
├─────────────────────────────────────────────────────────────────┤
│  7. СТИЛЬ                                                      │
│  • Обычно CS Fixer. Не человек.                                │
│  • [nit] — максимум 2–3 комментария. Больше — шум.             │
└─────────────────────────────────────────────────────────────────┘
```

### Пример: как я читаю конкретный PR

```php
// PR: feat: add order export to CSV

// 1. Описание: «Экспорт заказов в CSV для бухгалтерии. 50 строк, 1 файл»

// 2. Diff: 1 новый класс, 1 метод в контроллере, 1 тест

// 3. Тест: есть. Проверяет: 2 заказа → CSV с 2 строками.
//    [suggestion] А если 0 заказов? Пустой CSV с заголовком?

// 4. Код:

public function export(Request $request): Response
{
    $orders = $this->repo->findAll();          // [blocker] findAll() на 2M строк?
                                              //            Лимит? Фильтр?
    $csv = fopen('php://output', 'w');        // [suggestion] php://output — если
    fputcsv($csv, ['id', 'total']);           //            запрос отменят —
    foreach ($orders as $o) {                 //            частичный CSV в ответе
        fputcsv($csv, [$o->id, $o->total]);
    }
    fclose($csv);
    return $request->getSession()->...;       // [blocker] return после fclose?
}                                             //            Session здесь зачем?
```

**3 комментария. 2 blocker, 1 suggestion. 0 nit. Каждый — с причиной и fix.**

---

## 3. Разногласие с автором

### Типы разногласий

| Тип | Пример | Частота |
|---|---|---|
| **Корректность** | «Здесь null. Это баг.» vs. «Нет, тут не может быть null» | Частая |
| **Подход** | «Используй UnitOfWork» vs. «Здесь одна запись, flush достаточно» | Частая |
| **Архитектура** | «Вынеси в отдельный сервис» vs. «Это одна функция, зачем класс?» | Средняя |
| **Вкус** | «Имя `process`» vs. «Имя `handle`» | Частая, но неважная |
| **Масштаб** | «Это overengineering» vs. «Это будет расти» | Редкая, но острая |

### Принцип: автор знает контекст, которого у меня нет

Я вижу код. Автор знает:
- зачем это нужно (контекст задачи),
- что было рассмотрено и отвергнуто,
- ограничения времени,
- договорённости с командой,
- историю «почему сейчас так, а не иначе».

Поэтому:

> **Моя задача — задать вопросы, а не выносить приговор.**
> **Его задача — дать контекст, а не защищаться.**

### Как я формулирую

**Плохо:**
```
[suggestion] Это не DI. Нужно UnitOfWork.
```

**Хорошо:**
```
[question] Тут flush() без транзакции. Я правильно понимаю, что save()
и flush() здесь — атомарная операция на одной записи, и если flush()
упадёт, save() не успеет «загрязнить» состояние?

Если да — ок. Если нет — предлагаю wrapInTransaction.
```

**Плохо:**
```
[suggestion] Это не DDD. Order не должен знать про Mailer.
```

**Хорошо:**
```
[question] OrderService вызывает $this->mailer->send() прямо в методе.
Если mailer упадёт — заказ подтверждён, но письмо не отправлено.

Как вы видите recovery? Retry? Outbox? Или «письмо не критично,
пользователь увидит статус в UI»?

Если второе — ок, просто пометьте комментарием, чтобы следующий
разработчик не думал, что это забыли.
```

### Эскалация

Если мы не согласны после 2 раундов (моя аргументация → его ответ → моя реплика):

```
Я:    «Я понимаю вашу позицию. Давайте оставим как есть,
      но я бы хотел, чтобы ты задокументировал в PR
      "почему flush без UnitOfWork". Тогда через 6 месяцев
      никто не будет спрашивать.»

Он:   «Ок, добавил комментарий.»

Я:    «[approve] Спасибо. Мержи.»
```

**Правило: разногласие закрывается, а не выигрывается.**

Я не обязан «победить». Он не обязан «уступить». Мы находим **достаточно хорошее** решение и идём дальше. Если решение спорное — комментирую в PR:

```
[learning] Оставили flush без UnitOfWork по согласованию с @author.
Причина: одна запись, атомарность гарантирована save().
Если в будущем добавим вторую запись — revisit.
```

Это **запись в истории**. Через 6 месяцев кто-то прочитает и поймёт, почему.

### Когда я НЕ уступаю

| Ситуация | Почему |
|---|---|
| SQL injection, mass assignment, отсутствие authz | Безопасность. Не обсуждается. |
| `null` без проверки в критичном пути | Будет 500. Не «спорно». |
| Миграция без бэкапа, без reversibility | Данные. Не спорно. |
| `eval()`, `exec()`, прямой SQL с конкатенацией | Безопасность. Не спорно. |
| Отсутствие `strict_types` в новом коде | Стандарт проекта. |

В этих случаях:

```
[blocker] Это mass assignment. $request->all() → entity.
Пользователь может установить is_admin = true.

Это не suggestion. Это должно быть исправлено до мержа.
Если вы видите, что я не прав — покажите, как authz
закрыт в другом месте.
```

Конкретно. Без агрессии. С приглашением доказать обратное.

### Когда я Уступаю

| Ситуация | Почему |
|---|---|
| «Мы так делаем во всём проекте» | Консистентность > мой вкус |
| «Время. Это MVP. Улучшим в следующем спринте» | Реальность |
| «Я посмотрел, и тут null не бывает, потому что X» | У него контекст |
| «CS Fixer разрешает. Это валидно» | Стиль не ревью |
| «Мы обсуждали на митапе и решили иначе» | Решение принято. Не переоткрываем |

В этих случаях:

```
[suggestion → withdrawn] Понял контекст. Оставляю как есть.
Спасибо за объяснение.
```

---

## 4. Практические мелочи, которые имеют значение

### Тайминг

| Что | Когда |
|---|---|
| Первый комментарий | В течение 8 часов. Не на следующий день. |
| Ответ на «а что ты думаешь?» | В течение 2 часов. Не «подумаю». |
| Final approve | В течение 24 часов после последнего push. |
| Если уехал в отпуск | Заранее: «Я до пятницы. Ревью возьмёт @ivan.» |

### Канал

| Вопрос | Куда |
|---|---|
| Технический (код, архитектура) | В PR. В тексте. |
| Организационный (сроки, приоритеты) | В Slack / на митапе. Не в PR. |
| Личное («ты опять не протестировал») | **Нигде.** Обсуждаем код, не человека. |

### Что НЕ пишу в PR

| Не пишу | Почему |
|---|---|
| «Я бы сделал иначе» (без альтернативы) | Без альтернативы — это мнение, не замечание |
| «Это очевидно» | Неочевидно для автора |
| «Как можно так написать?» | Обвинение |
| «В документации написано» (без ссылки) | Неверифицируемо |
| «Так принято в индустрии» | В этом проекте — свои правила |
| 10 `[nit]` подряд | Шум. Максимум 3. Остальные — CS Fixer |

### Что я делаю ДО того, как комментировать

```
1. Прочитать PR-описание
2. Прочитать diff, не код (понять масштаб)
3. Прогнать локально (composer install, phpunit)
4. Прочитать код
5. Для каждого комментария:
   - Могу ли я показать, где это сломается? (пример, репликация)
   - Есть ли альтернатива? (если нет — [question], не [suggestion])
   - Это blocker или nit? (если не уверен — [question])
   - Я бы хотел, чтобы автор знал, что это НЕ блокирует? (тогда [suggestion] или [nit])
```

---

## 5. Итог

```
┌─────────────────────────────────────────────────────────────────────────┐
│  ХОРОШЕЕ РЕВЬЮ                                                         │
│                                                                         │
│  Маркировка:  [blocker] [bug] [security] [question]                   │
│               [suggestion] [nit] [learning]                            │
│  → Автор знает, что MUST fix, а что optional                           │
│                                                                         │
│  Приоритеты: корректность → безопасность → данные →                   │
│              архитектура → тестируемость → читаемость → стиль          │
│  → Не наоборот. Не «стиль, потом логика»                               │
│                                                                         │
│  Разногласие: вопрос, не приговор. Контекст автора > мой вкус.        │
│              2 раунда → закрываем. Записываем «почему».               │
│              Безопасность и данные — не обсуждается.                   │
│                                                                         │
│  Тайминг:  8 часов на первый комментарий. 24 часа на final.           │
│  Канал:    код → PR. Организационное → Slack. Личное → не в PR.       │
│                                                                         │
│  Антипаттерны:                                                         │
│  • 15 комментариев без приоритетов                                     │
│  • «LGTM» без прочтения                                               │
│  • 10 [nit] на 200 строк                                              │
│  • «Почему ты не сделал X?» (обвинение)                               │
│  • Спор в PR, который должен решаться на митапе                        │
│  • Ревью через 3 дня                                                  │
└─────────────────────────────────────────────────────────────────────────┘
```

**Одна фраза:** хороший ревьюер — не судья, а **второй читатель**. Он ищет то, что автор не видит, потому что смотрит с другой стороны. Он не переписывает чужой код. Он задаёт вопросы, которые автор не задал себе. И он уважает право автора на контекст, которого у него нет.

> ⏱ 56.8s · prompt 124881 tok · output 4951 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня превратить сырую бизнес-проблему («пользователи не находят старые заказы, поддержка завалена») в конкретную, самодостаточную задачу для разработчика. Это задача на системное мышление: из расплывчатой боли — конкретный scope, критерии, ограничения, метрики.

Что нужно сделать:
1. Разобрать бизнес-проблему: что реально происходит? Пользователи ищут заказы — где? На сайте? Через поддержку? Что «не находят» — не отображаются в UI? Не работают фильтры? Запросы медленные? Заказы не сохраняются?
2. Сформулировать задачу так, чтобы исполнитель (разработчик) мог сесть и делать, не задавая 10 вопросов.
3. Объяснить, ПОЧЕМУ я включаю каждый элемент.

Ключевые элементы хорошей задачи:
- Контекст: почему это важно, какая боль, какие метрики сейчас
- Scope: что именно делаем (и что НЕ делаем)
- Критерии приёмки: как поймём, что сделано
- Ограничения: что нельзя трогать, какие сроки, какой стек
- Приоритет / декомпозиция: что критично, что nice-to-have
- Что НЕ входит: чтобы не разрастлось
- Метрики успеха: как измерим, что проблема решена
- Риски и зависимости

Я должен показать, как из «поддержка завалена» извлечь конкретную гипотезу, проверить её, и сформулировать задачу. Важно не просто «сделать поиск заказов», а разобраться, в чём именно проблема.

Структура ответа:
1. Разбор: что я делаю с сырой проблемой (выделяю гипотезы, что именно сломано)
2. Формулировка задачи (сам текст, который я бы передал)
3. Почему каждый элемент включён (объяснение)
4. Чего НЕ включил и почему

Буду писать на русском, как и весь диалог. Структурированно, с конкретными примерами. Не абстрактно — с кодом, с цифрами, с конкретными критериями.

</details>

**Кандидат:**

# От «поддержка завалена» к задаче, которую можно выполнять

## 0. Что я делаю ДО того, как писать задачу

Сырая проблема содержит **симптом** («не могут найти») и **последствие** («поддержка завалена»), но не **причину**. Перед тем как формулировать задачу, я провожу 30 минут на уточнение:

### Гипотезы, которые я проверяю

| # | Гипотеза | Как проверю |
|---|---|---|
| H1 | Заказы **есть в БД**, но UI показывает только последние 5 | SQL: `SELECT status, count(*) FROM orders GROUP BY status`. UI: посмотреть, что отдаёт API |
| H2 | Заказы **есть в БД**, UI показывает все, но **нет поиска/фильтра** | Посмотреть страницу «Мои заказы». Есть ли поле поиска? Фильтр по дате? По статусу? |
| H3 | Поиск **есть**, но не работает (баг) | Ввести запрос в поиск. Проверить, что возвращается |
| H4 | Заказы **не сохраняются** (баг на входе) | SQL: `SELECT count(*) FROM orders WHERE created_at < now() - interval '30 days'`. Сравнить с количеством в CRM |
| H5 | Заказы **удалены** (TTL, purge) | SQL: проверить, есть ли заказы старше 6 месяцев. Если нет — это не баг, а фича |
| H6 | Пользователи **не могут вспомнить email**, на который регистрировались | Лог поддержки: «я регистрировался на другом email». Частота |
| H7 | **Медленный ответ**: API отдаёт 10с, пользователь думает «не находит» | Profiler: время ответа `GET /orders?customer_id=X` |

### Что я спрашиваю у бизнеса (15 минут)

```
1. «Сколько обращений в неделю по "не найден заказ"?»
   → 40/нед. До этого: 5/нед. Когда началось?

2. «Что именно пишут пользователи? Пришлите 10 примеров.»
   → 7 из 10: "у меня был заказ на [сумма] [месяц] назад,
     я не могу его найти, чтобы посмотреть трек-номер"

3. «Что пользователь делает, ПЕРЕД тем как написать в поддержку?»
   → Открывает "Мои заказы" → видит 3 последних →
     не видит старый → пишет в поддержку

4. «Есть ли у пользователей альтернативный путь?
   (email с подтверждением, трек-номер в SMS)»
   → Email есть, но люди не ищут в почте.
     SMS — нет.

5. «Какой SLA у поддержки? Сколько времени тратит оператор
   на такое обращение?»
   → 8 минут на обращение. 40/нед × 8 мин = 5,3 часа/нед.
     3 оператора.
```

### Результат: реальная проблема

**Не** «поддержка завалена». **А:**

> Страница «Мои заказы» показывает последние 5 заказов без возможности поиска или фильтрации. Пользователи, которые хотят найти конкретный старый заказ (по трек-номеру, по дате, по товару), **не могут** это сделать в UI. Идут в поддержку. Оператор ищет в БД 5–10 минут.

Это **одна конкретная фича**: поиск/фильтрация в «Моих заказах». Не «улучшить UX». Не «оптимизировать API». Не «переписать страницу».

---

## 1. Формулировка задачи

Вот текст, который я бы передал исполнителю (разработчику или команде из 1–2 человек):

---

### Задача: Поиск и фильтрация в «Моих заказах»

#### Контекст

Пользователи не могут найти свои старые заказы в интерфейсе. Страница «Мои заказы» (`/account/orders`) показывает последние 5 заказов, без поиска, без фильтрации, без пагинации. За последние 6 недель — **40 обращений в поддержку** по теме «не найден заказ». Средняя обработка — 8 минут. Итого ~5 часов работы поддержки в неделю на одну рутинную задачу.

**Цель:** пользователь сам находит свой заказ за ≤ 30 секунд без обращения в поддержку.

#### Что делаем

**1. Фильтр по статусу**

- Чекбоксы: `Все`, `В обработке`, `Отгружен`, `Доставлен`, `Отменён`.
- По умолчанию: `Все`.
- Фильтр на сервере (DQL/SQL), не на клиенте.
- URL: `/account/orders?status=shipped

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

(пустой ответ модели)

> ⏱ 22.2s · prompt 129482 tok · output 1589 tok · ⚠️ обрыв: length

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о поведении разработчика при работе по задаче с чёткими критериями приёмки. Два сценария:

а) Нашёл попутный баг, не связанный с задачей.
б) Понял, что критерий приёмки противоречит цели задачи.

Это вопрос о профессиональной этике, коммуникации, процессах. Нужно дать практичный, конкретный ответ с примерами. Не абстрактный, а с "что делаю" и "почему".

Ключевые аспекты:
- Не молчать, но и не "чинить всё подряд"
- Разграничение: что в скоуп, что вне
- Коммуникация: кому, когда, в какой форме
- Почему: потому что молчание = скрытый долг, а "починю всё" = расползание скоупа
- Конкретные действия: зафиксировать, создать задачу, не чинить в текущем PR (или чинить, если тривиально и не раздувает PR)
- Для противоречия: остановиться, обсудить с заказчиком/тимлидом, не принимать решение сам, не "делать как лучше"

Структурирую:
1. Общий принцип
2. Сценарий (а): попутный баг
   - Что делаю (конкретные шаги)
   - Чего НЕ делаю
   - Почему
   - Пример
3. Сценарий (б): противоречие в критериях
   - Что делаю (конкретные шаги)
   - Чего НЕ делаю
   - Почему
   -

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.5s · prompt 130698 tok · output 373 tok · ⚠️ обрыв: length

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про отчёт о выполнении задачи в контексте уже идущего диалога о PHP/Symfony/архитектуре/ревью. Это продолжение разговора о процессах разработки. Нужно дать практичный, конкретный ответ — не абстрактный, а с примерами.

Вопрос двухчастный:
1. Из чего состоит отчёт о выполненной задаче (код написан, тесты проходят)
2. Что меняется, если часть задачи сделать не удалось

Контекст предыдущих ответов: я отвечаю как senior/lead разработчик, с конкретикой, примерами кода, таблицами, trade-offs. Стиль — прямой, без воды, с конкретными примерами и объяснением «почему».

Отчёт о выполнении — это не «сделано». Это коммуникация с несколькими аудиториями:
- Автор задачи (бизнес/продакт) — хочет понять: решена ли проблема?
- Ревьюер — хочет понять: что изменено, как проверить, какие риски?
- Будущий разработчик (через 3 месяца) — хочет понять: почему именно так?
- Команда — хочет понять: что теперь можно делать, чего нельзя?

Структура отчёта:
1. Что сделано (ссылки: PR, commit, тикет)
2. Как проверить (конкретные шаги, не «откройте страницу»)
3. Что изменилось (архитектур

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.3s · prompt 130749 tok · output 321 tok · ⚠️ обрыв: length

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про FORCE INDEX в PostgreSQL. Это вопрос-ловушка или вопрос на проверку честности: FORCE INDEX — это синтаксис MySQL/MariaDB. В PostgreSQL такой синтаксис НЕТ. Это важно — я не должен выдумывать функциональность, которой не существует в PostgreSQL. Нужно честно сказать, что FORCE INDEX в PostgreSQL отсутствует, объяснить почему (философия оптимизатора PostgreSQL), и рассказать, какие реальные механизмы есть в PostgreSQL для влияния на план запроса, и когда они применяются. Также стоит упомянуть, что в экосистеме PostgreSQL есть расширение pg_hint_plan, которое имитирует HINT-ы из Oracle/MySQL, включая аналоги force index, но это внешнее расширение, не встроенный функционал.

Структура ответа:
1. Прямой честный ответ: FORCE INDEX в PostgreSQL нет.
2. Почему (философия оптимизатора PostgreSQL, статистика, cost-based).
3. Что есть вместо этого: ANALYZE, статистика, partial/covering индексы, pg_hint_plan, SET-параметры оптимизатора, EXPLAIN для диагностики.
4. Когда возникает соблазн использовать force index и что делать вместо этого.
5. Антипаттерны.

Язык ответа — русский, как и вопросы пользователя. Тон

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.9s · prompt 130783 tok · output 287 tok · ⚠️ обрыв: length

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

The user asks: "By your experience, by what percentage does strict_types=1 slow down PHP code execution, and why?"

This is a tricky question because it contains a false premise. Let me think carefully about what actually happens:

1. `declare(strict_types=1)` is a **compile-time** directive, not a runtime one. It affects how type checking is performed when a function is *called* from that file. Specifically, it disables implicit type coercion at call sites.

2. Does it slow down execution? Let me think about this carefully:

- When strict_types=1 is declared, PHP checks types at call sites more strictly. Specifically, if you pass a string "5" to a function expecting `int`, with strict_types it throws a TypeError, while without it, PHP would coerce "5" to 5.
- The type check itself (comparing the type of the argument against the declared type) happens **regardless** of strict_types. The difference is only in what happens when the types don't match exactly:
  - Without strict_types: attempt coercion (which is itself a small operation)
  - With strict_types: immediately throw

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.1s · prompt 130828 tok · output 243 tok · ⚠️ обрыв: length

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про составные первичные ключи (composite/compound primary keys) в Doctrine ORM — почему они "не поддерживаются" и как с этим работают.

Нужно быть точным: Doctrine ORM **поддерживает** составные ключи через `#[ORM\Id]` на нескольких полях + `#[ORM\ManyToOne]` с `inversedBy`/`mappedBy` для associations. Но есть реальные ограничения и неудобства:

1. `EntityManager::find()` принимает ID как ассоциативный массив или массив значений — неудобно.
2. `EntityManager::getReference()` аналогично.
3. Изменение составного PK после persist — проблемы.
4. `#[ORM\Version]` на сущности с составным PK работает, но UPDATE генерирует WHERE с несколькими условиями.
5. Association с составным PK — нужно маппить каждое поле FK.

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.0s · prompt 130873 tok · output 198 tok · ⚠️ обрыв: length

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

Пользователь показывает классический «спагетти»-контроллер с кучей проблем и просит разобрать их и показать переделанный код. Это не вопрос с подвохом — это проверка, умею ли я видеть проблемы системно, а не точечно

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 75.0s · prompt 131011 tok · output 60 tok · ⚠️ обрыв: length

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

Пользователь просит проанализировать доменную сущность User с точки зрения DDD и чистой архитектуры. Давай разберу код:

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

1. **EntityManagerInterface в конструкторе доменной сущности** — это грубейшее нарушение чистой архитектуры. Domain не должен знать о Doctrine, о persistence, о каких-либо фреймворках. EntityManager — это инфраструктурная зависимость.

2. **`$this->em->flush()` внутри доменного метода** — доменная сущность вызывает flush(). Это означает, что сущность знает о транзакциях, о том, когда применять изменения в БД. Это нарушение SRP и DIP.

3. **Отсутствие валидации** — changeEmail принимает любой string. Нет проверки, что это валидный email. Нет проверки на null, на пустую строку.

4. **Нет Value Object для email** — email хранится как raw string. Нет нормализации (lowercase, trim), нет валидации формата.

5. **Отсутствие ID** — User это Entity, у неё должен быть ID. Здесь только email.

6. **Нет других полей** — хотя это может быть упрощённый пример, Entity без ID — подозрительно.

7. **`#[ORM\Entity]` на доменной сущности** — это смешение Domain и Infrastructure. В чистой архитектуре доменная сущность не знает о ORM.

8. **Нет readonly, нет инвариантов** — email можно поменять на что угодно.

9. **Не final** — можно наследовать и сломать.

10. **Нет событий** — смена email не генерирует доменного события.

11. **`new Logger()` внутри метода** — нет, тут логгера нет, это другой пример. Тут только flush.

12. **Структура: доменная сущность = ORM-сущность** — нет разделения между domain model и persistence model.

Теперь как исправить:

1. Убрать EntityManager из доменной сущности
2. Выделить email в Value Object
3. Добавить валидацию
4. Разделить Domain User и ORM UserRecord
5. Репозиторий — интерфейс в домене, реализация в инфраструктуре
6. flush() — в сервисе приложения, не в сущности
7. final class
8. Value Object email с валидацией

Давай структурирую ответ: сначала перечислю проблемы (с объяснением почему каждая — проблема), потом покажу исправленный код с объяснением каждого решения.

</details>

**Кандидат:**

## Проблемы

Разберу по убыванию серьёзности.

### 1. `EntityManagerInterface` в доменной сущности

Это главная проблема. Доменная сущность не должна знать, **как** и **где** хранятся данные. `EntityManager` — это инструмент персистентности. Сущность, которая держит ссылку на него, **привязана к Doctrine навсегда**.

Последствия:

- Невозможно юнит-тестировать `User` без `EntityManager` (мокаешь `flush()` — что ты тестируешь? что вызвал `flush`? это не поведение).
- Невозможно заменить Doctrine на Eloquent, на raw SQL, на вставку в Redis — придётся менять домен.
- Нарушение DIP: сущность зависит от конкретного класса (`EntityManagerInterface`), а не от абстракции, которую **она** определяет.

```php
// Сущность знает:
// - что есть БД
// - что есть транзакции (flush = применить изменения

> ⏱ 82.6s · prompt 130173 tok · output 898 tok · ⚠️ обрыв: length

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе работы с незнакомой технологией/стеком — от первого взгляда на задачу до уверенного ответа о сроках. Это вопрос о практике и методах, а не о конкретной технологии.

Что важно здесь:
1. Это не "опиши, как работать с Rust" — это "опиши СВОЙ процесс", "твою последовательность действий", "по твоему опыту".
2. Ключевые аспекты: структура разбора, критерий "когда просить помощь", честность о сроках.
3. Нужно показать конкретность, а не общие фразы "изучу документацию".
4. Важна честность: я ИИ, у меня нет "опыта" в человеческом смысле, но я могу описать процесс, который описали бы опытные разработчики, и который я сам применяю при работе с незнакомыми темами.

Структура ответа:
- Фаза 1: Первое впечатление (5 минут) — что я уже знаю, что не знаю, где границы
- Фаза 2: Оценка глубины (30 минут) — сколько нужно изучить, чтобы решить задачу
- Фаза 3: Разбор (1-4 часа) — как изучаю, на что фокусируюсь
- Фаза 4: Решение (зависит от глубины)
- Фаза 5: Оценка сроков (что делаю ДО того, как назвать число)
- Когда просить помощь vs. когда разбираться самому — конкретные критерии

Важно: не давать общий "совет", а показать РЕАЛЬНЫЙ процесс с конкретными действиями. Не "изучите документацию", а "открываю X, ищу Y, если не нахожу за Z минут — делаю W".

Критерий "когда просить помощь":
- Разбираюсь сам: задача в моей зоне компетенции, просто технология новая (напр., я пишу на PHP, а стек на Go). Логика понятна, синтаксис нет.
- Прошу помощь: задача требует глубокого понимания внутренней работы технологии (напр., "почему этот запрос медленный в ClickHouse"), и у меня нет ни опыта, ни доступа к людям, которые это знают.
- Красная линия: если я потратил 2-3 часа на изучение и не могу сформулировать гипотезу — дальше идёт работа вхолостую.

Формат: не буду делать это как "советы", а как "вот как я действую", с конкретными шагами и временными рамками. Без "во-первых, во-вторых" — это скучно. Лучше фазами, с конкретикой.

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 8.2s · prompt 130476 tok · output 596 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о профессиональной ситуации: обещанный дедлайн не реалистичен. Это вопрос не о коде, а о профессиональном поведении, коммуникации, управлении ожиданиями.

Нужно ответить честно и практически. Без морализаторства, без "идеального" ответа. Реальная ситуация: ты уже обещал, ты уже начал, ты понял что не укладываешься.

Ключевые аспекты:
1. СКОРОСТЬ реакции — как быстро ты сигнализируешь
2. КАК сигнализируешь — конкретика, а не "ну, может, завтра"
3. ЧТО предлагаешь — не просто "не успею", а варианты
4. ЧЕГО НЕ делаешь — молчать и надеяться, что успеешь; или "я попробую, но не факт"
5. Почему именно так — последствия каждого выбора

Структура ответа:
- Чёткое действие (сигнал)
- Когда (время)
- Как (формулировка)
- Варианты, которые предлагаешь
- Чего НЕ делаешь и почему
- Что делаешь после сигнала
- Если это повторяется — системная проблема

Не буду делать это слишком длинным. Вопрос конкретный, ответ должен быть конкретным. Не абстрактная философия, а "я делаю вот это, в вот это время, вот так".

Скажу прямо: это не "управление стейкхолдерами", это базовая честность и уважение к чужому времени.

</details>

**Кандидат:**

## Что делаю

**Сигналю в тот момент, когда понял, что не успею.** Не в конце дня. Не за час до дедлайна. Не "ну, я попробую, но не факт".

### Когда именно

Принцип: **чем позже сигнализирую, тем дороже стоит моя честность.**

| Момент | Что уже произошло | Стоимость для команды |
|---|---|---|
| Утром, через 2 часа работы | Никто не спланировал день | Низкая. Перестраиваются легко |
| К обеду | Коллега уже поставил свою задачу на потом, ожидая моего артефакта | Средняя. Перегородка |
| Вечером, за 3 часа до дедлайна | Ожидание уже зафиксировано. Коллега не начал свою часть | Высокая. Упущенный день |
| На следующий день, "из

> ⏱ 7.4s · prompt 130527 tok · output 545 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о практическом балансе между качеством и скоростью в разработке. Это вопрос инженерного суждения, а не формальной теории. Нужно ответить как практик, а не как учебник.

Ключевые аспекты:
1. Критерии "достаточно хорошо" — что конкретно проверяю
2. Когда продолжаю улучшать, а когда стоп
3. Конкретные ситуации и границы
4. Как это выглядит в реальном проекте, а не в теории
5. Антипаттерны: и over-engineering, и "достаточно для меня"

Структура ответа:
- Критерии "готово" (конкретные, проверяемые)
- Критерии "стоп улучшать" (когда отдаю)
- Конкретные ситуации: когда улучшаю, когда нет
- Баланс в реальном проекте (спринт, дедлайн, hotfix, MVP)
- Антипаттерны
- Одна фраза-итог

Важно: не абстрактно, а с конкретными примерами и числами. Показывать мышечную память, а не теорию.

</details>

**Кандидат:**

## Критерий "готово"

У меня есть список вопросов, на которые

> ⏱ 4.0s · prompt 130795 tok · output 277 tok · ⚠️ обрыв: length

---
