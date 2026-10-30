# Собес: `qwen3.8:latest` · роль: backend — run-19

- Дата: 2026-10-12T10:19:16.599613+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 10m · think: low · num_ctx: 131072
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

<details><summary>Ход мысли модели</summary>

The user is asking me to role-play as a PHP backend developer in a job interview. They want me to introduce my experience in PHP and related technologies, then rate my knowledge on a scale of 0-10 for each area: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, Clean Architecture, Testing, and Git, with a one-sentence explanation for each rating.

I should respond as if I'm a candidate in an interview — confident but honest, showing a realistic profile. I'll write in Russian since that's the language of the conversation. I'll make it feel natural, like a real person talking, not like an AI listing capabilities. I'll aim for a profile that's strong but not artificially perfect — realistic ratings with genuine explanations.

Let me think about what a solid senior PHP developer profile looks like and craft something believable and engaging.

</details>

**Кандидат:**

Привет! Рад, что мы начали.

Коротко о себе: в PHP около 8 лет. Первые два года — на Laravel, потом перешёл на Symfony и уже не уходил. Сейчас в основном работаю на Symfony 6/7, PHP 8.1–8.3, PostgreSQL. В последние два года сильно сдвинулся в сторону DDD и чистых слоёв — перестроил два проекта, где раньше был «спaghetti layer cake» на сервисной модели.

Из смежного: Docker, Redis, RabbitMQ, Nginx. Пишу на TypeScript для внутренних admin-панелей, но это не основной фокус.

---

**Оценки:**

| Область | Оценка | Обоснование |
|---------|--------|-------------|
| **PHP 8** | 9 | Работаю с readonly classes, enums, fibers, JIT-конфигурацией; регулярно читаю RFC и RFC-процесс, но edge-case'ы с оптимизатором JIT пока только теоретически. |
| **Symfony** | 9 | Пишу кастомные bundles, DI-контейнер, message bus, form-типы, security-провайдеры с нуля; не хватает глубины в понимании internals EventDispatcher и как он взаимодействует с PSR-14 на уровне performance. |
| **Doctrine** | 8 | ORM, DQL, QueryBuilder, lifecycle-callbacks, custom types — всё в проде; болячка — производительность на N+1 и выбор стратегии кэширования (query/result/second-level) в реальных сценариях с высокой нагрузкой. |
| **PostgreSQL** | 7 | Пишу raw SQL, понимаю индексы (B-tree, GIN, BRIN), partitioning, advisory locks; но tuning-под нагрузкой 100k+ RPS и понимание работы buffer pool / vacuum в production — пока на уровне «умею, но не до конца». |
| **DDD** | 7 | Тактики (aggregate, value object, domain event, repository) — применяю осознанно; стратегический уровень (bounded context, context mapping) — понимаю концептуально, но реального опыта проектирования на 20+ контекстов пока нет. |
| **Чистая архитектура** | 7 | Dependency rule, hexagonal, ports & adapters — применяю в новых проектах; сложно — удерживать дисциплину в команде из 5–8 человек без деградации в «service-ориентированную» модель. |
| **Тестирование** | 8 | Unit (PHPUnit, Pest), integration (Doctrine test, API), contract; понимаю, где тесты дают ценность, а где — шум; слабее — property-based testing и формальные подходы. |
| **Git** | 8 | Rebase, cherry-pick, bisect, hooks, worktree, interactive rebase — в проде ежедневно; не хватает опыта работы с monorepo-инструментами (nx, bazel) и сложными merge-strategies в больших командах. |

---

Если хочешь — могу углубиться в любой из пунктов или сразу переходить к техническим вопросам.

> ⏱ 10.6s · prompt 135 tok · output 887 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

<details><summary>Ход мысли модели</summary>

The user is continuing the interview roleplay. I'm acting as a PHP backend developer candidate. They're asking about the most important PHP 8+ features for writing clean and reliable code, with short code examples.

Let me think about what to cover. I should pick features that genuinely matter for "clean and reliable" code, not just list everything. Let me rank by impact:

1. **Enums** (8.1) — replaces magic strings/ints, type safety
2. **Readonly properties/classes** (8.1/8.2) — immutability, safer objects
3. **Constructor promotion** (8.0) — less boilerplate, less chance of typos
4. **Named arguments** (8.0) — readability at call sites
5. **Union types / intersection types** (8.0) — more precise contracts
6. **Fiber** (8.0) — async without coroutines complexity (maybe less relevant for "clean code")
7. **Destructuring improvements** (8.0/8.1)
8. **`never` return type** (8.1) — expresses "this never returns"
9. **`readonly` class** (8.2) — entire class immutable
10. **Typed properties everywhere** (7.4, but still relevant as baseline)

For "clean and reliable" I'll focus on:
- Enums — kills the biggest source of bugs (magic values)
- Readonly class/properties — immutability, no accidental mutation
- Constructor promotion — less boilerplate = less room for error
- Named arguments — clarity at call sites
- `never` return type — intent clarity
- Union/intersection types — precise contracts

I should keep examples short, focused, and show the "before" pain vs "after" benefit where it's natural. I'll avoid being too long — maybe 5-6 features with tight examples.

Let me structure this as a natural interview answer — conversational, not a blog post. I'll pick 5-6 features, give a 1-2 line "why it matters" and a compact code snippet.

</details>

**Кандидат:**

## Какие фичи PHP 8+ реально влияют на чистоту кода

Не буду перечислять всё подряд — выделю то, что я реально использую каждый день и что убирает классы багов.

---

### 1. Enums (8.1) — убийца magic values

Самая дорогая ошибка в PHP — это строка `"PENDING"` вместо `"pending"`, или `3` вместо `Status::Active`. Enums решают это на уровне типа.

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid    = 'paid';
    case Shipped = 'shipped';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Ожидает оплаты',
            self::Paid    => 'Оплачен',
            self::Shipped => 'Отгружен',
        };
    }
}

// Раньше:
if ($order->status === 'paid') { ... }  // опечатка = тихий баг

// Теперь компилятор не даст передать что угодно кроме случаев enum:
function canRefund(OrderStatus $status): bool
{
    return $status === OrderStatus::Paid || $status === OrderStatus::Shipped;
}
```

`match` + enum = исчерпывающая проверка без `default`, который ты пишешь «на всякий случай» и забываешь.

---

### 2. `readonly class` (8.2) — иммутабельность по умолчанию

До 8.2 можно было сделать readonly-свойства, но сам объект всё ещё был мутируемым. `readonly class` закрывает это полностью.

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
            throw new \ValueError('Currency mismatch');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}

$m = new Money(100, 'USD');
// $m->amount = 200;  ← compile error: readonly class property
```

Это не «nice to have». В доменных объектах, DTO, value objects — это убирает целый класс багов с нечаянной мутацией и race condition в асинхронном коде.

---

### 3. Constructor promotion (8.0) — меньше boilerplate = меньше ошибок

```php
// Было:
class User
{
    private string $name;
    private string $email;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $name, string $email, \DateTimeImmutable $createdAt)
    {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }
}

// Стало:
class User
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly \DateTimeImmutable $createdAt,
    ) {}
}
```

Почему это важно для надёжности: меньше строк — меньше мест, где можно забыть присвоить, перепутать порядок или опечататься в имени свойства. Плюс `readonly` на месте — объект сразу immutable.

---

### 4. Named arguments (8.0) — читаемость на вызове

```php
// Позиционные аргументы — нужно помнить порядок:
$validator = new ValidationRule('email', 'required', 'max:255', false);

// Номинированные — порядок не важен, намерение явно:
$validator = new ValidationRule(
    field:    'email',
    rule:     'required',
    limit:    'max:255',
    nullable: false,
);
```

В production-коде, где методы принимают 4–6 аргументов, это реально снижает вероятность «пробросил неправильный аргумент на неправильную позицию».

---

### 5. `never` return type (8.1) — явное намерение

```php
function findUserById(int $id): User
{
    $user = $this->repo->find($id);

    if ($user === null) {
        throw new UserNotFoundException($id);  // никогда не вернёт null
    }

    return $user;
}
```

До `never` я писал `: void` или просто не ставил тип. Теперь статический анализатор (PHPStan, Psalm) понимает: «после этого вызова исполнение не продолжается» — и корректно сужает типы в ветках. Это убирает кучу `!` и `assert`-хака.

---

### 6. Union types + `null` (8.0) — честные контракты

```php
function resolveConfig(?string $key): string|array|null
{
    // Явно говорю: могу вернуть string, массив или null.
    // Клиент обязан обработать все три варианта.
}
```

До 8.0 я писал `@return string|array|null` в docblock — и никто не проверял. Теперь это часть сигнатуры, и `@var`-хаки уходят.

---

### Что я сознательно НЕ использую для «чистоты»

- **Fibers** — мощно, но в PHP-приложениях с event loop (Swoole, FrankenPHP) это лишняя абстракция. В классическом PHP-стеке я пока вижу только один сценарий: graceful shutdown с cancel-лацией долгих операций.
- **JIT** — в CLI-скриптах да, в long-running worker — да, в FPM за Nginx — почти не даёт прироста, а отладка сложнее.

---

Если хочешь, могу показать, как эти фичи складываются вместе в реальном доменном объекте или репозитории.

> ⏱ 13.8s · prompt 901 tok · output 1836 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос на собеседовании о системе типов в PHP. Нужно:
1. Объяснить, как работает система типов PHP (weak typing, coercive typing)
2. Что даёт `strict_types=1`
3. Как ведут себя приведения типов (coercive mode)
4. Типичные ловушки
5. Примеры где типизация спасает и где удивляет

Я должен ответить как опытный PHP-разработчик, который реально работал с этим. Не поверхностно, а с пониманием внутренних механик и практическими примерами.

Ключевые моменты, которые нужно покрыть:
- PHP — weakly typed язык, есть два режима: coercive и strict
- `strict_types=1` — файл-локальная директива, не глобальная
- Что именно меняется: только при вызове функций/методов, не на присвоениях
- Ловушки:
  - `strict_types` не работает внутри самого файла, где объявлен — а на стороне вызывающего
  - `true == 1`, `0 == "0"`, `"" == false`
  - `"1" == 1`, но `"1abc" == 1` → false (в PHP 8)
  - `"abc" == 0` → true (до PHP 8), в PHP 8 → false + warning
  - `"123abc" == 123` → false (PHP 8), раньше true
  - `1 == "true"` → false
  - `null == false` → true
  - `[] == false` → true
  - `"0" == false` → true
  - JSON decode: `"age": "25"` — строка вместо int
  - `intdiv` vs `/`
  - `float` comparison: `0.1 + 0.2 != 0.3`
  - `bool` в арифметике: `true + true = 2`
  - `string` concatenation: `1 . "px" = "1px"`, но `"1" . true = "11"`
  - `array` comparison: `==` vs `===`
  - `mixed` type в PHP 8.0 — ловушка: `function foo(mixed $x)` принимает ВСЁ, включая null
  - `iterable` — array|Traversable
  - `?Type` — nullable
  - `T[]` и `array<T>` — array shape, но PHP не проверяет
  - `never` — функция не возвращает управление
  - `readonly` — compile-time, но runtime-проверка только на присвоение

Структурирую ответ так:
1. Общая картина: weak typing + два режима
2. strict_types — как работает, где не работает
3. Таблица/примеры coercive conversions
4. Ловушки на практике (с кодом)
5. Где типизация спасает
6. Где удивляет

Буду честен: укажу где PHP "хромает" и как обходите.

</details>

**Кандидат:**

## Система типов в PHP: как она работает на самом деле

### Общая картина

PHP — **weakly typed** язык. У него есть два режима приведения:

- **Coercive** (по умолчанию): PHP пытается "угадать" нужный тип.
- **Strict**: тип аргумента должен точно совпадать с объявленным, иначе — `TypeError`.

Ключевой момент, который путает всех: **`strict_types` — это не настройка движка, а файл-локальная директива**. Она влияет на то, как *этот файл* вызывает чужие функции, а не на то, как в этот файл вызывают *его* функции.

```
// src/Order/Calculator.php
<?php
declare(strict_types=1);  // ← влияет ТОЛЬКО на вызовы ИЗ этого файла

function calculate(int $amount, string $currency): string
{
    return $amount . ' ' . $currency;
}
```

```
// src/Service.php
<?php
// strict_types НЕ объявлен → coercive mode

require 'Order/Calculator.php';

calculate(100.9, "USD");  // ← TypeError! Потому что Service.php в coercive режиме
                          //    и передаёт float вместо int.

calculate(100, "USD");    // ← ок
```

И наоборот:

```
// src/Order/Calculator.php
<?php
declare(strict_types=1);

function calculate(int $amount, string $currency): string
{
    // Внутри ЭТОГО файла strict_types тоже работает:
    $x = "10" + 5;  // ← это НЕ strict, а арифметика. Арифметика всегда coercive.
    return $x;      // ← TypeError: int, string expected
}
```

---

### Что `strict_types` НЕ делает

Это первое, что нужно понять:

```php
declare(strict_types=1);

// strict_types НЕ защищает присвоения:
$x = "hello";
$x = 123;          // ← ок, переменная не имеет статического типа

// strict_types НЕ защищает return type в самом файле:
function foo(): int
{
    return "42";   // ← TypeError, но это из-за return type, не из-за strict_types
}

// strict_types НЕ влияет на массивы:
$arr = [1, "two", 3];  // ← ок, mixed array
```

---

### Coercive mode: где PHP "помогает" и ломает

#### 1. Сравнение с `==` — главная мина

```php
// PHP 8 — поведение:
"" == false        // true  (пустая строка == false)
0 == false         // true
"0" == false       // true
[] == false        // true
null == false      // true
"abc" == 0         // FALSE в PHP 8! (в PHP 7 было true)
"123abc" == 123    // FALSE в PHP 8! (в PHP 7 было true)
"1e5" == 100000    // true  (научная нотация)
"1" == true        // true
"" == 0            // true
" " == 0           // true  (пробел!)
```

Почему `"123abc" == 123` стало `false` в PHP 8: до 8-й версии PHP превращал строку в число (парсил начало), и `"123abc"` → `123`. В 8-й версии это убрали: если строка не является валидным числом, сравнение с числом всегда `false`. Это **спасает**, но ломает legacy-код, который на это опирался.

**Практическое правило:** `==` для значений, `===` для всего остального. Идеально — `===` везде, кроме случаев, где вы *сознательно* хотите loose comparison (и тогда — комментируйте почему).

#### 2. Арифметика со строками

```php
"10" + 5        // 15  (строка преобразуется в число)
"10.5" * 2     // 21.0
"abc" + 5      // 5   (нечисловая строка → 0, без warning в PHP 8)
"" + 5         // 5
true + true     // 2   (bool → int)
false + 10      // 10
```

Ловушка:

```php
function calculateTax(string $income): int
{
    // Клиент передаёт "1000 USD" — а мы ожидали "1000"
    return (int) $income * 0.1;  // 0! (int)("1000 USD") = 1000, ок, но:
}

// Хуже:
function getAge(string $raw): int
{
    return (int) $raw;
}

getAge("30 years old");  // 30 — "работает", но хрупко
getAge("30,5");          // 30 (запятая — не разделитель дробной части в PHP)
getAge("30.5");          // 30 (int-каст отбрасывает дробную часть)
```

#### 3. Конкатенация

```php
"age: " . 25       // "age: 25"
"active: " . true  // "active: 1"  (не "true"!)
"count: " . [1,2]  // "count: Array" + Warning: Array to string conversion
1 . "px"           // "1px"
"0x" . 10         // "0x10" (строковая конкатенация, не hex)
```

#### 4. `bool` в неожиданных местах

```php
// Truthiness — классика:
"0"     // falsy!
[]      // falsy
null    // falsy
false   // falsy
0       // falsy
0.0     // falsy
""      // falsy

// Но:
"0.0"   // truthy
"false" // truthy
"null"  // truthy
[null]  // truthy (нормальный массив)
```

Ловушка с `bool` и `mixed`:

```php
function isActive(bool $active): void
{
    echo $active ? "yes" : "no";
}

// В coercive режиме:
isActive("1");       // "yes" — ок
isActive(1);         // "yes" — ок
isActive("hello");   // "yes" — НЕ ОК, это баг. Строка → true.
isActive("");        // "no" — ок
isActive([]);        // "no" — ок
isActive(null);      // TypeError: null → bool не приводится (в strict)
                     // В coercive: false → "no"
```

---

### Где типизация реально спасает

#### 1. `never` + ранний выход

```php
function findOrder(int $id): Order
{
    $order = $this->repo->find($id);

    if ($order === null) {
        throw OrderNotFoundException::withId($id);
        //  ^ never: статический анализатор знает, что дальше
    }

    // PHPStan/Psalm: $order здесь точно Order, не null
    return $order;
}
```

До `never` я писал `assert($order !== null)` — это хак. Теперь тип-система это понимает.

#### 2. Union types для честных контрактов

```php
function resolveDiscount(PromotionType $type): float|int
{
    return match($type) {
        PromotionType::Percent => 15,   // int
        PromotionType::Fixed   => 10.5, // float
        PromotionType::None    => 0,
    };
}

// Клиент:
$discount = resolveDiscount($type);
// $discount: float|int — IDE подсвечивает, что это не только float
// и не только int. Если сделаешь $discount / 2 — ок.
// Если сделаешь $discount . "%" — тоже ок (string concat).
// Если сделаешь array_push($discount, ...) — ошибка.
```

#### 3. `readonly` + constructor promotion — DTO без мутаций

```php
readonly class Address
{
    public function __construct(
        public string    $street,
        public string    $city,
        public string    $zipCode,
        public string    $country,
    ) {}

    public function full(): string
    {
        return sprintf("%s, %s %s, %s", $this->street, $this->zipCode, $this->city, $this->country);
    }
}

$addr = new Address("123 Main", "Springfield", "62704", "US");
// $addr->city = "Chicago";  ← Error: Cannot modify readonly property
```

Это не просто «красиво». В асинхронном коде (Swoole, ReactPHP, FrankenPHP) это убирает data race на уровне дизайна.

#### 4. `iterable` vs `array` — честный интерфейс

```php
function processItems(iterable $items): void
{
    // Accepts array AND any Traversable
    // Не могу $items[0], не могу count() без toArray()
    foreach ($items as $item) {
        // ...
    }
}

// Это лучше, чем array $items, когда:
// - данные из Generator (ленивая загрузка из БД)
// - данные из Traversable (Doctrine Collection)
// - и вы не хотите копировать в массив
```

---

### Где типизация удивляет (и ломает)

#### 1. `mixed` — это НЕ «любой тип»

```php
function log(mixed $value): void
{
    // mixed включает: int, float, string, bool, array, object, null, resource, callable
    // Но НЕ включает: mixed, void, never (эти не могут быть аргументами)
    
    if (is_string($value)) {
        echo $value;
    }
}

// Ловушка:
log(null);    // ок
log(false);   // ок
log(new \stdClass);  // ок
// log(void); // невозможно, void — не значение
```

#### 2. `array<string, int>` — декларация без гарантии

```php
function process(array<string, int> $config): void
{
    // PHP НЕ проверяет, что все ключи — string, а все значения — int
    // Это просто annotation для static analysis (PHPStan, Psalm)
    
    foreach ($config as $key => $value) {
        // PHPStan: $key: string, $value: int
        // Но runtime: $key может быть int, $value может быть что угодно
    }
}

process(["a" => "100"]);  // ← runtime: ок, статический анализ: error
```

Это главная проблема PHP: **array shape — это не тип, это annotation**. В отличие от `list<int>` в TypeScript, где это runtime-проверяемо (с runtime lib) или хотя бы IDE-подсвечено.

#### 3. `?Type` vs `Type|null` — одно и то же

```php
function foo(?string $x): void {}  // = string|null
function bar(string|null $x): void {}  // то же самое
```

Но:

```php
function baz(string|null|int $x): void {}
// = mixed! (string|int|null = mixed)
```

Это ловушка: если вы напишете `string|int|null`, PHPStan может не подсвечивать, что это эквивалент `mixed`, и вы потеряете все проверки.

#### 4. `int` и `float` в арифметике

```php
1 / 2          // 0.5  (float!)
100 / 3       // 33.333... (float!)
100 / 2       // 50   (int, PHP оптимизирует)
100 / 0       // DivisionByZeroError

intdiv(7, 2)   // 3   (целочисленное деление, int)
7 % 3          // 1   (остаток)
7 % -3         // -2  (знак у наследуется от делимого!)

// Ловушка:
0.1 + 0.2      // 0.30000000000000004
// Решение: bcmath, round(), или сравнение с epsilon
abs(0.1 + 0.2 - 0.3) < 0.0000000001  // true
```

#### 5. `null` и `false` в `??` vs `?:`

```php
$a = null;
$b = false;
$c = 0;

$a ?? "default"   // "default"  (null coalescing)
$a ?: "default"   // "default"  (truthy check)
$b ?? "default"   // false      (b не null!)
$b ?: "default"   // "default"  (b falsy!)
$c ?? "default"   // 0
$c ?: "default"   // "default"
```

`??` — «значит ли null», `?:` — «значит ли false/empty». Разные вещи. Я регулярно вижу баги, где перепутали.

---

### Итого: мой практический чеклист

| Ситуация | Что делаю |
|----------|-----------|
| Все новые файлы | `declare(strict_types=1);` — всегда |
| Сравнение | `===` по умолчанию, `==` только с комментарием |
| Bool-параметры | `bool` тип, не `?bool` (null ≠ «неизвестно» в 90% случаев) |
| Числа из внешних источников | `int` или `float` + валидация, не `(int)` cast |
| Array shapes | `array<string, int>` + PHPStan level 8+ |
| Money/Decimal | `bcmath` или `Money`-обёртка, не `float` |
| `mixed` | Только на границе (JSON decode, HTTP body), дальше — сужаю |
| `readonly class` | Все DTO, Value Objects, конфигурации |

Если хочешь, могу показать, как это всё складывается в реальном модуле — например, репозиторий с query builder и доменная сущность с инвариантами.

> ⏱ 32.1s · prompt 2339 tok · output 4076 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

<details><summary>Ход мысли модели</summary>

The user is asking about exception handling design in PHP applications. They want to know about:
1. Custom exception hierarchies
2. Wrapping external/third-party exceptions
3. What constitutes a method contract
4. Common anti-patterns

This is a senior-level question. I should structure my answer clearly, show real code examples, and be opinionated but pragmatic. Let me think about what I actually do and what I've seen break in production.

Key areas to cover:
- Exception hierarchy design (domain vs infrastructure vs application)
- The contract of a method (what it promises, what it throws)
- Wrapping doctrine/DB exceptions into domain exceptions
- Anti-patterns: catching Throwable, empty catch, catching in controllers vs middlewares, using exceptions for control flow, generic exceptions, etc.

Let me structure this as a coherent narrative with code examples that show real production patterns.

</details>

**Кандидат:**

## Проектирование исключений в PHP

### Философия

Исключение — это **контракт**. Когда я пишу метод, я должен честно указать: при каких условиях он не завершит нормальный путь. Всё, что не описано в сигнатуре — баг.

```php
// Плохо: что-то "может" случиться, но не описано
function findOrder(int $id): Order { ... }

// Хорошо: контракт явный
/**
 * @throws OrderNotFoundException — если заказ не найден
 * @throws OrderAccessDeniedException — если доступ запрещён
 */
function findOrder(int $id): Order { ... }
```

---

### Иерархия: три слоя

Я делаю разделённую иерархию, где каждый слой не знает про верхний.

```
AppException (interface, расширяет Throwable)
├── DomainException
│   ├── ValidationException
│   │   ├── OrderStatusTransitionInvalidException
│   │   └── InsufficientBalanceException
│   ├── NotFound
│   │   ├── OrderNotFoundException
│   │   └── CustomerNotFoundException
│   └── Conflict
│       ├── DuplicateEmailException
│       └── OrderAlreadyShippedException
│
├── InfrastructureException
│   ├── DatabaseException
│   │   ├── DeadlockException
│   │   └── ConnectionLostException
│   ├── ExternalServiceException
│   │   ├── PaymentGatewayException
│   │   └── EmailProviderException
│   └── CacheException
│
└── ApplicationException
    ├── ConfigurationException
    └── SerializationException
```

Почему так, а не плоская:

- **DomainException** — бизнес-правило нарушено. Клиент (frontend, API) может показать понятное сообщение. Не логируем как ошибку сервера — это ожидаемое состояние.
- **InfrastructureException** — что-то сломалось снаружи. Логируем с контекстом, показываем пользователю generic 500.
- **ApplicationException** — конфигурация, сериализация. Обычно 500.

```php
interface AppException extends \Throwable
{
    public function toResponse(): array;
}
```

Тот факт, что `AppException` — интерфейс, а не класс, позволяет каждому слою иметь свою логику сериализации, но общий контракт для handler-а.

---

### Пример доменного исключения

```php
readonly class OrderStatusTransitionInvalidException extends DomainException
{
    private const string MESSAGE = "Cannot transition order from {from} to {to}";

    public function __construct(
        private readonly OrderStatus $from,
        private readonly OrderStatus $to,
    ) {
        parent::__construct(
            sprintf(self::MESSAGE, $from->value, $to->value),
            409,  // HTTP-код, если это API
        );
    }

    public function from(): OrderStatus
    {
        return $this->from;
    }

    public function to(): OrderStatus
    {
        return $this->to;
    }

    public function toResponse(): array
    {
        return [
            'code'    => 'ORDER_STATUS_INVALID',
            'message' => $this->getMessage(),
            'from'    => $this->from->value,
            'to'      => $this->to->value,
        ];
    }
}
```

Использование в домене:

```php
readonly class Order
{
    public function __construct(
        private readonly OrderId      $id,
        private readonly OrderStatus  $status,
        private readonly Money        $amount,
    ) {}

    public function ship(): self
    {
        $this->assertCanTransitionTo(OrderStatus::Shipped);

        return new self($this->id, OrderStatus::Shipped, $this->amount);
    }

    private function assertCanTransitionTo(OrderStatus $to): void
    {
        $valid = match($this->status) {
            OrderStatus::Pending => $to === OrderStatus::Paid,
            OrderStatus::Paid    => $to === OrderStatus::Shipped,
            OrderStatus::Shipped => $to === OrderStatus::Delivered,
            OrderStatus::Delivered => false,
        };

        if (!$valid) {
            throw new OrderStatusTransitionInvalidException($this->status, $to);
        }
    }
}
```

Домен **не знает** про HTTP, про Symfony, про Doctrine. Он бросает доменное исключение. Дальше — чужая забота.

---

### Оборачивание чужих исключений

Принцип: **никогда не позволяю инфраструктурному исключению пробиться сквозь границу слоя**.

```php
class OrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(Order $order): void
    {
        try {
            $this->em->persist($order);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            // Инфраструктурное → доменное
            throw new DuplicateEmailException(
                email: $this->extractEmailFromViolation($e)
            ) from $e;  // ← "from" сохраняет причину для трейсбека
        } catch (DeadlockException $e) {
            throw new InfrastructureException(
                "Database deadlock during order save",
                previous: $e,
            );
        }
    }
}
```

Почему `from $e`, а не `new ... previous: $e`:

```php
// "from" — синтаксис, который сохраняет причину И добавляет в message:
// "Cannot transition... in /app/Order.php on line 45 (Caused by Doctrine\DBAL\Exception\UniqueConstraintViolation)"

// previous — просто поле, не виден в getMessage()
```

В production я всегда делаю `from`, потому что в Sentry/Logs причина видна сразу без раскрытия трейсбека.

---

### Что считается контрактом метода

Правило, которое я держу:

| Ситуация | Что делаю |
|----------|-----------|
| Ожидаемое состояние (нет записи, невалидные данные) | Бросаю **доменное** исключение. Не возвращаю `null`. |
| Временный сбой (сеть, deadlock) | Бросаю **инфраструктурное**. Caller решает: retry или 500. |
| Непредвиденный баг | Не перехватываю. Пусть упадёт. |
| "Что-то не то" | Это anti-pattern. Конкретизирую. |

```php
// АНТИПАТТЕРН: null как "нет"
function findOrder(int $id): ?Order
{
    return $this->em->getRepository(Order::class)->find($id);
}

// Вызывающий код:
$order = $repo->findOrder($id);
if ($order === null) {
    throw new \Exception("Order not found");  // ← exception for control flow
}
// ... или:
if ($order !== null) {
    // 80% кода
}

// ПРАВИЛЬНО: метод обещает Order или бросает.
function findOrder(int $id): Order
{
    $order = $this->em->getRepository(Order::class)->find($id);

    if ($order === null) {
        throw new OrderNotFoundException($id);
    }

    return $order;
}

// Вызывающий код:
$order = $repo->findOrder($id);  // либо Order, либо exception. Точка.
```

---

### Где перехватываю

**Единственное место**, где я перехватываю `AppException` и превращаю в HTTP-ответ — это middleware (или `kernel.exception` listener в Symfony).

```php
class AppExceptionHandler
{
    public function __invoke(AppException $e): JsonResponse
    {
        $status = $e->getStatus();  // из интерфейса

        $this->logger->warning($e->getMessage(), [
            'exception' => get_class($e),
            'code'      => $e->getCode(),
        ]);

        return new JsonResponse($e->toResponse(), $status);
    }
}
```

```php
// Symfony: src/EventListener/ExceptionListener.php
class ExceptionListener
{
    public function __construct(
        private AppExceptionHandler $handler,
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if ($e instanceof AppException) {
            $event->setResponse($this->handler($e));
            return;
        }

        if ($e instanceof MethodNotAllowedException) {
            $event->setResponse(new JsonResponse(['error' => 'Method not allowed'], 405));
            return;
        }

        // Всё остальное — 500, логируем с полным трейсбеком
        $this->logger->critical('Unhandled exception', [
            'exception' => $e::class,
            'message'   => $e->getMessage(),
            'trace'     => $e->getTraceAsString(),
        ]);

        $event->setResponse(new JsonResponse(['error' => 'Internal server error'], 500));
    }
}
```

В бизнес-коде я **не пишу `catch` для доменных исключений**. Если метод бросает — я или обработаю его на своём уровне (например, в use case), или прокину выше.

---

### Антипаттерны, которые я регулярно вижу

#### 1. `catch (\Throwable $e)`

```php
try {
    $this->doWork();
} catch (\Throwable $e) {
    $this->logger->error($e->getMessage());
    // "тихо" продолжаем
}
```

**Почему плохо:** перехватывает `Error` (OutOfMemory, StackOverflow), `ShutdownException`, `AssertionError`. Ты "обрабатываешь" что-то, что не является recoverable. Приложение в неопределённом состоянии, а ты логишь и идёшь дальше.

**Правило:** `catch` только то, что ты **осознанно** умеешь обработать. Всё остальное — выше.

#### 2. Пустой `catch`

```php
try {
    $cache->set($key, $value);
} catch (\Exception $e) {
    // cache — best effort, ignore
}
```

Единственное оправдание — когда fallback тривиален и документирован. Иначе — это "спрятал баг".

#### 3. Исключение для flow control

```php
try {
    $value = $arr[$key];
} catch (UndefinedArrayKeyException $e) {
    $value = $default;
}
```

```php
// Вместо:
$value = $arr[$key] ?? $default;
```

Исключения дороги: аллокация, заполнение трейсбека, перебор stack frames. Для "нет ключа" — это в 50–200 раз дороже, чем `??`.

#### 4. Generic `Exception` с magic number

```php
throw new \Exception("Something went wrong", 500);
throw new \Exception("User not found", 404);
```

Потому что:
- Нет типа — нельзя `catch` по смыслу
- Код 500 в `Exception` — это не контракт, это хаки
- Невозможно сериализовать в API

#### 5. Перехват в контроллере

```php
class OrderController
{
    public function ship(OrderId $id): JsonResponse
    {
        try {
            $order = $this->useCase->ship($id);
            return $this->json($order, 200);
        } catch (OrderNotFoundException $e) {
            return $this->json(['error' => 'Not found'], 404);
        } catch (OrderStatusTransitionInvalidException $e) {
            return $this->json(['error' => 'Invalid status'], 409);
        } catch (\Exception $e) {
            return $this->json(['error' => '500'], 500);
        }
    }
}
```

Это **дублирование** логики, которая должна быть в одном месте (handler). Плюс: каждый контроллер — свой `catch (\Exception)`, который глотает всё.

#### 6. `setLastException` в Doctrine

```php
try {
    $em->flush();
} catch (\Exception $e) {
    // $em->flush() уже поставил connection в broken state
    // Последующие flush'и упадут с тем же исключением, но другое сообщение
    throw $e;
}
```

Ловушка: после `flush()` с ошибкой `EntityManager` может быть в неконсистентном состоянии. Если ты "обрабатываешь" и пытаешься сделать ещё один `flush` в том же request — получишь `EntityManager is closed` или повторное исключение.

Решение: после flush с ошибкой — `clear()` + новый `EntityManager` (или `reset()`).

---

### Чеклист: как я проектирую новый метод

```
1. Этот метод может закончиться "неуспешно"?
   → Да: какое исключение? Domain / Infrastructure / Application?
   → Нет: ок, ничего не бросаю.

2. Клиенту нужно знать причину?
   → Да: доменное исключение с контекстом (id, from, to, message).
   → Нет: инфраструктурное, generic message наружу, детали в лог.

3. Я перехватываю чужое исключение?
   → Оборачиваю в своё с `from $e`.
   → Не теряю контекст (id, query, endpoint).

4. Я перехватываю своё исключение ниже по стеку?
   → Нет. Прокидываю выше.

5. На границе (middleware / handler) — сериализую в ответ.
```

Если хочешь, могу показать, как это выглядит в реальном use case с retry-логикой для external service и idempotency key.

> ⏱ 25.6s · prompt 5784 tok · output 3468 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

<details><summary>Ход мысли модели</summary>

The user is asking me to continue the interview role-play. They want to know which SOLID principle is most commonly violated in real PHP code, why, and to see a before/after refactoring example.

Let me think about what's genuinely most common in PHP codebases I've seen:

1. **SRP (Single Responsibility)** - violated constantly. God classes, controllers doing everything, services that are 500+ lines. But this is almost a "default state" of PHP projects, not really a violation you "commit" - it's just the lack of architecture.

2. **OCP (Open/Closed)** - violated when you add new behavior by modifying existing code. Classic example: a `switch` on entity type, a `if/else` chain for different strategies. Very common in PHP because of the lack of strong typing in method signatures.

3. **LSP (Liskov Substitution)** - violated when you have "base" classes that aren't truly abstract. In PHP this happens a lot with abstract classes that have concrete behavior that subclasses need to override but don't always can.

4. **ISP (Interface Segregation)** - violated when you have fat interfaces. In PHP/Symfony this shows up as huge Repository interfaces, huge Service interfaces.

5. **DIP (Dependency Inversion)** - violated when you depend on concrete implementations directly. In PHP this is VERY common because of the dynamic nature of the language - people just `new` things, or use concrete classes in method signatures.

Actually, I think the most impactful and common one in PHP specifically is **SRP** at the domain level, but the most *structurally damaging* and the one that compounds over time is **OCP** - because it's the one that creates the "switch statement hell" that makes codebases hard to maintain.

Wait, let me reconsider. In my experience working with PHP codebases:

- SRP violations are the most *frequent* but often the most *benign* (just messy)
- OCP violations create the most *structural debt* because every new feature requires modifying existing code
- DIP violations are common in PHP because of the "just new it up" culture

I'll go with **OCP** as the primary answer because:
1. It's the most structurally damaging
2. It's the most commonly violated in a *specific, identifiable pattern* (switch/if-else on types)
3. The fix is concrete and demonstrates good architecture
4. It connects to DDD and clean architecture naturally

I'll show a concrete PHP example: a notification service that has a giant switch on notification type, and refactor it to strategy pattern with proper abstractions.

Let me make the example realistic - something from an actual e-commerce or order system, since that's what I've been discussing. I'll show a "PaymentProcessor" or "NotificationDispatcher" that has grown into a monster.

Actually, let me pick something even more relatable: a `OrderService` that handles different payment methods with if/else chains. This is THE most common OCP violation in PHP e-commerce code.

Let me structure:
1. State which principle and why
2. Show the "before" - realistic, ugly, common PHP code
3. Explain WHY it's a problem (beyond just "it's ugly")
4. Show the refactored version
5. Note the trade-offs and when NOT to refactor

I want to be honest and nuanced, not preachy. In the interview context, the candidate should show judgment, not just textbook answers.

</details>

**Кандидат:**

## ОCP: Open/Closed Principle

Честно — SRP нарушается чаще всего, но он и "безобиднее": код просто раздутый, а не структурно сломанный. А вот **OCP** — это та проблема, которая *мультиплицируется*. Каждый новый фиченский запрос трогает всё больше старых файлов, и через полгода ни один разработчик не помнит, что где менять.

В PHP это бьёт особенно сильно, потому что язык не заставляет тебя думать о полиморфизме. Можно написать `switch` на тип, и код "работает". Никто не скажет "нарушение OCP" — просто "так проще".

---

### Типовой пример: обработка платежей

Это не учебный пример. Это код, который я видел в 4 из 6 последних проектов.

```php
class OrderService
{
    public function __construct(
        private PaymentGateway $gateway,
        private EmailService $email,
        private OrderRepository $repo,
    ) {}

    public function processPayment(int $orderId, array $paymentData): Order
    {
        $order = $this->repo->find($orderId);
        $method = $paymentData['method'];

        if ($method === 'card') {
            $token = $this->gateway->createCardToken(
                number:   $paymentData['number'],
                expMonth: $paymentData['exp_month'],
                expYear:  $paymentData['exp_year'],
                cvv:      $paymentData['cvv'],
            );

            $charge = $this->gateway->charge(
                tokenId:   $token,
                amount:    $order->amount,
                currency:  $order->currency,
            );

            if ($charge->status === 'failed') {
                throw new PaymentDeclinedException($charge->declineCode);
            }

            $order = $order->markAsPaid($charge->id);
            $this->email->sendPaymentConfirmation($order, 'card');

        } elseif ($method === 'bank_transfer') {
            $reference = $this->generateBankReference($order);

            // Не платим сразу — ждём подтверждение
            $order = $order->markAsPendingBankTransfer($reference);
            $this->email->sendBankInstructions($order, $reference);

        } elseif ($method === 'crypto') {
            $address = $this->gateway->createCryptoAddress($order->currency);

            $txHash = $this->gateway->createCryptoInvoice(
                address:  $address,
                amount:   $order->amount,
                currency: $order->currency,
            );

            $order = $order->markAsPendingCrypto($txHash, $address);
            $this->email->sendCryptoInstructions($order, $address, $txHash);

        } elseif ($method === 'wallet') {
            $charge = $this->gateway->chargeFromWallet(
                walletId: $paymentData['wallet_id'],
                amount:   $order->amount,
                currency: $order->currency,
            );

            if ($charge->status === 'failed') {
                throw new WalletInsufficientFundsException();
            }

            $order = $order->markAsPaid($charge->id);
            $this->email->sendPaymentConfirmation($order, 'wallet');

        } else {
            throw new \InvalidArgumentException("Unknown payment method: {$method}");
        }

        $this->repo->save($order);
        return $order;
    }
}
```

### Почему это структурная проблема, а не "просто грязно"

1. **Каждый новый способ оплаты = правка этого метода.** Добавил "СБП" — изменил этот файл. Добавил "Apple Pay" — изменил этот файл. Через год здесь 8 веток, и каждый разработчик помнит только свою.

2. **Логика "как обработать платёж" размазана по методу.** У card — create token, charge, check status. У crypto — create address, create invoice, send hash. Это разные жизненные циклы, но они заперты в одном `if/else`.

3. **Тестирование мучительно.** Чтобы протестировать crypto-платёж, я должен мокать `PaymentGateway` так, чтобы `createCryptoAddress` вернул нужное, `createCryptoInvoice` — другое, а `createCardToken` и `charge` — не вызвались. Это хрупко.

4. **Нарушение SRP как следствие.** `OrderService` теперь знает: как токенизировать карту, как генерировать bank reference, как создавать crypto-адрес, как отправлять email. Это 4+ ответственности.

5. **`markAsPaid`, `markAsPendingBankTransfer`, `markAsPendingCrypto`** — три разных метода на `Order`, которые делают похожее, но по-разному. Это признак того, что сам `Order` знает слишком много о способах оплаты.

---

### Рефакторинг

Идея: **стратегия оплаты** — самостоятельный объект с контрактом. `OrderService` не знает, *как* платёж обрабатывается — он знает, *что* нужно сделать.

#### 1. Контракт

```php
readonly interface PaymentStrategy
{
    public function method(): string;

    /**
     * Возвращает обновлённый заказ.
     *
     * @throws PaymentDeclinedException
     * @throws WalletInsufficientFundsException
     */
    public function process(Order $order, array $data): Order;

    public function isImmediate(): bool;
}
```

`isImmediate()` — чтобы `OrderService` мог знать, нужно ли ему отправлять email "оплачен" или "ожидается подтверждение". Это явная информация, а не зашитая в if/else.

#### 2. Implementations

```php
readonly class CardPaymentStrategy implements PaymentStrategy
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    public function method(): string
    {
        return 'card';
    }

    public function isImmediate(): bool
    {
        return true;
    }

    public function process(Order $order, array $data): Order
    {
        $token = $this->gateway->createCardToken(
            number:   $data['number'],
            expMonth: $data['exp_month'],
            expYear:  $data['exp_year'],
            cvv:      $data['cvv'],
        );

        $charge = $this->gateway->charge(
            tokenId:  $token,
            amount:   $order->amount,
            currency: $order->currency,
        );

        if ($charge->status === 'failed') {
            throw new PaymentDeclinedException($charge->declineCode);
        }

        return $order->markAsPaid($charge->id);
    }
}
```

```php
readonly class CryptoPaymentStrategy implements PaymentStrategy
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    public function method(): string
    {
        return 'crypto';
    }

    public function isImmediate(): bool
    {
        return false;
    }

    public function process(Order $order, array $data): Order
    {
        $address = $this->gateway->createCryptoAddress($order->currency);
        $txHash  = $this->gateway->createCryptoInvoice(
            address:  $address,
            amount:   $order->amount,
            currency: $order->currency,
        );

        return $order->markAsPending($PaymentType::Crypto, $txHash, $address);
    }
}
```

#### 3. Registry

```php
class PaymentStrategyRegistry
{
    /** @var array<string, PaymentStrategy> */
    private array $strategies = [];

    public function register(PaymentStrategy $strategy): void
    {
        $this->strategies[$strategy->method()] = $strategy;
    }

    public function get(string $method): PaymentStrategy
    {
        if (!isset($this->strategies[$method])) {
            throw new UnknownPaymentMethodException($method);
        }

        return $this->strategies[$method];
    }
}
```

#### 4. `OrderService` после рефакторинга

```php
class OrderService
{
    public function __construct(
        private readonly PaymentStrategyRegistry $strategies,
        private readonly OrderRepository         $repo,
        private readonly NotificationDispatcher  $dispatcher,
    ) {}

    public function processPayment(int $orderId, string $method, array $data): Order
    {
        $order    = $this->repo->find($orderId);
        $strategy = $this->strategies->get($method);

        $order = $strategy->process($order, $data);
        $this->repo->save($order);

        if ($strategy->isImmediate()) {
            $this->dispatcher->sendPaymentConfirmation($order, $method);
        } else {
            $this->dispatcher->sendPendingInstructions($order, $method);
        }

        return $order;
    }
}
```

#### 5. Registration (Symfony DI)

```yaml
# config/services.yaml
App\Payment\CardPaymentStrategy:
    arguments: ['@App\Gateway\PaymentGateway']
    tags:
        - { name: payment.strategy, method: card }

App\Payment\CryptoPaymentStrategy:
    arguments: ['@App\Gateway\PaymentGateway']
    tags:
        - { name: payment.strategy, method: crypto }

App\Payment\PaymentStrategyRegistry:
    calls:
        - register: ['%kernel.project_dir%/src/Payment/CardPaymentStrategy']
```

Или через `CompilerPass`, если хочется чище:

```php
class PaymentStrategyPass extends RegisterListenersPass
{
    public function process(ContainerBuilder $container): void
    {
        $registryId = 'App\Payment\PaymentStrategyRegistry';

        if (!$container->hasDefinition($registryId)) {
            return;
        }

        foreach ($container->findTaggedServiceIds('payment.strategy') as $id => $tags) {
            foreach ($tags as $tag) {
                $container->getDefinition($registryId)
                    ->addMethodCall('register', [new Reference($id)]);
            }
        }
    }
}
```

---

### Что изменилось и почему это не "просто перестановка"

| Аспект | До | После |
|--------|----|-------| — |
| Новый способ оплаты | Правка `OrderService` + новый `markAs*` на `Order` | Новый файл `XxxPaymentStrategy`, регистрация в DI. `OrderService` не трогаю. |
| Тестирование | Мокаю gateway с 5 методами | Мокаю только то, что нужно конкретной стратегии |
| `Order` | Знает про card, crypto, bank | Знает только `markAsPaid` / `markAsPending` — абстрактные состояния |
| `OrderService` | 120 строк, 8 веток | 20 строк, 0 веток |
| Добавление "СБП" | +20 строк в if/else | Новый файл + 3 строки в DI-конфиге |

---

### Когда я сознательно НЕ делаю так

Честно: если способов оплаты **2** и они **не будут меняться** — я не трогаю. Over-abstracting двух случаев это тоже антипаттерн: 3 интерфейса, 2 реализации, registry, compiler pass — всё ради двух `if/else`.

Правило: **3+ реализации, ожидаются новые** — вводишь интерфейс. **2 и static** — оставляю switch, но выношу в отдельный class, чтобы хотя бы `OrderService` был чистым.

---

### Итого

OCP нарушается не потому что "разработчик глупый". А потому что PHP не даёт *болевых сигналов*: `if/else` компилируется, работает, проходит CI. Боль приходит через 6 месяцев, когда нужно добавить 4-й способ, и ты обнаруживаешь, что `OrderService` — 200 строк, и три человека не помнят, что в ветке `bank_transfer` на 47-й строке есть хак, который нельзя трогать.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

## Да. И иногда это лучше, чем следование.

SOLID — это не правила. Это **направления**, в которых обычно уменьшается стоимость изменения. Когда контекст другой — направление другое. Слепое следование создаёт проблемы не меньше, чем их отсутствие.

---

### Пример: SRP в доменной сущности

#### Нарушение

```php
class Order
{
    public function __construct(
        private readonly OrderId      $id,
        private readonly Money        $amount,
        private readonly string       $currency,
        private readonly CartLine[]   $lines,
    ) {}

    public function confirm(): self
    {
        // 1. Проверка инварианта
        if (count($this->lines) === 0) {
            throw new EmptyOrderException();
        }

        // 2. Проверка, что все товары доступны
        $inventory = $this->inventoryService->checkAvailability(
            $this->lines,
            $this->warehouseId,
        );

        if (!$inventory->isAvailable()) {
            throw new InsufficientStockException($inventory->missing);
        }

        // 3. Резервирование на складе
        $reservationId = $this->inventoryService->reserve($this->lines, $this->warehouseId);

        // 4. Проверка платёжеспособности
        $balance = $this->paymentService->checkBalance($this->customer, $this->amount);

        if ($balance < $this->amount) {
            $this->inventoryService->release($reservationId);  // ← откат!
            throw new InsufficientBalanceException($this->amount, $balance);
        }

        // 5. Запись в реестр заказов
        $this->orderService->record($this, $reservationId);

        return new self(
            id:        $this->id,
            amount:    $this->amount,
            currency:  $this->currency,
            lines:     $this->lines,
            status:    OrderStatus::Confirmed,
            reservedAt: new \DateTimeImmutable(),
        );
    }
}
```

Формально `Order` отвечает за:
- Инварианты
- Проверку наличия на складе
- Резервирование
- Проверку баланса
- Запись в реестр

Это **пять ответственностей**. SRP нарушен.

#### Почему это оправдано

Ключевой момент: **все пять шагов — один атомарный бизнес-инвариант.**

> "Заказ не может быть подтверждён, если на складе нет товара, на счёте нет денег, и запись не была создана."

Если я вынесу это в `OrderConfirmationService`:

```php
class OrderConfirmationService
{
    public function confirm(Order $order): Order
    {
        if (count($order->lines) === 0) {
            throw new EmptyOrderException();
        }

        $inventory = $this->inventoryService->checkAvailability($order->lines, ...);
        if (!$inventory->isAvailable()) {
            throw new InsufficientStockException(...);
        }

        $reservationId = $this->inventoryService->reserve(...);

        $balance = $this->paymentService->checkBalance(...);
        if ($balance < $order->amount) {
            $this->inventoryService->release($reservationId);
            throw new InsufficientBalanceException(...);
        }

        $this->orderService->record($order, $reservationId);

        return $order->withStatus(OrderStatus::Confirmed);
    }
}
```

Что я получаю?

1. **Инвариант больше не защищён тип-системой.** Кто-то может вызвать `$order->withStatus(OrderStatus::Confirmed)` напрямую, минуя `confirm()`. Раньше было невозможно — состояние "Confirmed" можно было получить только через `confirm()`.

2. **Координация рассредоточена.** Откат резервирования при нехватке денег — это часть логики `OrderConfirmationService`, а не `Order`. Через полгода кто-то добавит шестой шаг и забудет про откат.

3. **Тестирование не стало проще.** Я всё равно должен мокать `InventoryService`, `PaymentService`, `OrderService`. Единственное отличие — я мокаю их в сервисе, а не в сущности. Но сущность теперь "просто хранит данные", что делает тесты сущности тривиальными, а тесты сервиса — хрупкими.

**Суть:** SRP говорит "одна причина менять". Но здесь **одна причина** — изменение бизнес-правила подтверждения заказа. Если правило меняется, я меняю один метод. Если я разделю на 5 классов — правило меняется, и я меняю 5 файлов + координирую их.

---

### Где граница

Нарушение SRP оправдано, когда:

| Условие | Почему |
|---------|--------|
| Все шаги — **один атомарный инвариант** | Разделение создаёт race condition или требует транзакции, которую сложнее обеспечить |
| Координация (откаты, порядок) **сложнее** самих шагов | Вынос в сервис увеличивает, а не уменьшает сложность |
| Класс **не растёт** — шаги не добавляются | Если через месяц будет 10 шагов — это уже сигнал, что SRP нужен |
| Класс **не имеет побочных эффектов** помимо координации | Если `Order` ещё и отправляет email, пишет в лог, публикует event — это уже другое |

Когда **не** оправдано:

```php
// Антипаттерн: "God Object" под предлогом SRP
class Order
{
    public function confirm(): self { ... }          // 50 строк
    public function ship(): self { ... }             // 40 строк
    public function refund(): self { ... }           // 60 строк
    public function calculateTax(): Money { ... }    // 30 строк
    public function generateInvoice(): Invoice { ... } // 80 строк
    public function calculateDiscount(): Money { ... } // 50 строк
    // ... ещё 15 методов
}
```

Здесь каждый метод — **отдельная бизнес-функция**, а не один атомарный инвариант. `confirm()` и `refund()` меняются по разным причинам, в разное время, разными командами. Это уже **не** оправданное нарушение SRP, а его отсутствие.

Граница: **один метод, один инвариант** — ок. **Много методов, много инвариантов** — нет.

---

### Второй пример: DIP

#### Нарушение

```php
class PaymentProcessor
{
    public function __construct(
        private readonly StripeGateway $stripe,  // ← конкретный класс, не интерфейс
    ) {}

    public function charge(string $token, Money $amount): PaymentResult
    {
        return $this->stripe->charge($token, $amount);
    }
}
```

DIP говорит: зависимость должна быть от абстракции. Здесь — от конкретного класса.

#### Почему оправдано

- **Stripe — единственная реализация.** Нет `PaymentGateway`-интерфейса, потому что нет второй реализации и не предвидится.
- **Интерфейс на одну реализацию** — это ложная абстракция. Я создаю `interface PaymentGateway`, `class StripeGateway implements PaymentGateway`, и ничего не получаю. Только лишний слой, который нужно поддерживать.
- **Если завтра появится Adyen** — я создам интерфейс. Это один PR, 15 минут.

Правило: **DIP — для того, чтобы замена была дешёвой.** Если замена не планируется, абстракция — это стоимость без收益.

---

### Общий принцип

SOLID — это **эвристика для снижения стоимости изменения**. Когда:

- Изменение не ожидается (1 реализация, 2 метода, статичный домен) → абстракция = чистый overhead.
- Изменение ожидается, но затрагивает **один атомарный блок** → инкапсуляция в одном месте лучше, чем в пяти.
- Изменение ожидается **непредсказуемо** и **непредсказуемо часто** → SOLID работает, как задумано.

Слепое следование — это та же ошибка, что и игнорирование. Разница в том, что первое маскируется под "правильность" и поэтому труднее заметить.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

## OCP без наследования

OCP — это свойство: **добавить новое поведение, не изменяя существующий код.** Наследование — лишь один из механизмов полиморфизма. Вот остальные, от лёгких к тяжёлым.

---

### 1. Strategy через композицию

Самый близкий аналог наследованию, но без `extends`.

```php
// Контракт — интерфейс
readonly interface TaxCalculator
{
    public function calculate(Money $amount, string $region): Money;
}

// Реализации — отдельные классы, нет общего родителя
readonly class VatTaxCalculator implements TaxCalculator
{
    public function calculate(Money $amount, string $region): Money
    {
        $rate = match($region) {
            'RU'  => new \BCMath\Decimal('0.20'),
            'EU'  => new \BCMath\Decimal('0.21'),
            default => new \BCMath\Decimal('0.00'),
        };
        return $amount->multiply($rate);
    }
}

readonly class FlatRateTaxCalculator implements TaxCalculator
{
    public function __construct(private readonly Money $flatAmount) {}

    public function calculate(Money $amount, string $region): Money
    {
        return $this->flatAmount;
    }
}

// Клиент зависит от интерфейса, не от класса
class InvoiceService
{
    public function __construct(private readonly TaxCalculator $tax) {}

    public function total(Money $subtotal): Money
    {
        return $subtotal->add($this->tax->calculate($subtotal, 'RU'));
    }
}
```

**Плюс над наследованием:** можно менять стратегию в рантайме, нет иерархии, проще тестировать (не нужен `Mockery::mock` с `shouldReceive` на `__construct`).

**Минус:** нужен DI-контейнер или явная инициализация. В чистом PHP без DI — нужен фабрики или registry.

---

### 2. Registry + Map

Поведение определяется **ключом**, а не типом объекта.

```php
class NotificationDispatcher
{
    /** @var array<string, callable(Notification): void> */
    private array $handlers = [];

    public function register(string $channel, callable $handler): void
    {
        $this->handlers[$channel] = $handler;
    }

    public function dispatch(Notification $notification): void
    {
        $channel = $notification->channel;

        if (!isset($this->handlers[$channel])) {
            throw new UnknownChannelException($channel);
        }

        ($this->handlers[$channel])($notification);
    }
}

// Регистрация — отдельно от использования
$dispatcher->register('email', fn(Notification $n) => $mailer->send($n));
$dispatcher->register('sms',   fn(Notification $n) => $smsGateway->send($n));
$dispatcher->register('push',  fn(Notification $n) => $pushService->send($n));

// Новый канал:
$dispatcher->register('telegram', fn(Notification $n) => $telegramBot->send($n));
// Ничего не изменилось в NotificationDispatcher, Notification, или существующих handlers.
```

**Плюс:** максимально просто. Новый вариант = одна строка. Нет иерархии, нет интерфейсов.

**Минус:** нет типа безопасности. `callable` может быть любой. Нет автодополнения IDE. Тестирование — только интеграционное.

**Когда использую:** 2–5 вариантов, стабильный набор, логика простая.

---

### 3. Pipeline / Chain of Responsibility

Каждый участник — отдельный класс, порядок определяется при сборке.

```php
interface OrderMiddleware
{
    public function handle(Order $order, callable $next): Order;
}

class ValidateOrderMiddleware implements OrderMiddleware
{
    public function handle(Order $order, callable $next): Order
    {
        $this->validator->assert($order);
        return $next($order);
    }
}

class ReserveStockMiddleware implements OrderMiddleware
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function handle(Order $order, callable $next): Order
    {
        $reservationId = $this->inventory->reserve($order->lines);
        return $next($order->withReservation($reservationId));
    }
}

class ChargePaymentMiddleware implements OrderMiddleware
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    public function handle(Order $order, callable $next): Order
    {
        $charge = $this->gateway->charge($order);
        return $next($order->withPayment($charge));
    }
}

// Сборка — декларативная
class OrderPipeline
{
    private array $middlewares = [];

    public function add(OrderMiddleware $mw): self
    {
        $this->middlewares[] = $mw;
        return $this;
    }

    public function process(Order $order): Order
    {
        $next = function (Order $o): Order { return $o; };  // terminal

        foreach (array_reverse($this->middlewares) as $mw) {
            $prev = $next;
            $next = fn(Order $o) => $mw->handle($o, $prev);
        }

        return $next($order);
    }
}

// Использование
$pipeline = (new OrderPipeline())
    ->add(new ValidateOrderMiddleware($validator))
    ->add(new ReserveStockMiddleware($inventory))
    ->add(new ChargePaymentMiddleware($gateway));

$order = $pipeline->process($order);
```

**Плюс:** порядок явный, каждый middleware изолирован, можно вставлять/удалять без изменения остальных. Новый шаг = новый класс + одна строка в сборке.

**Минус:** сложно откатывать состояние (ReserveStock забирает, ChargePayment отклонил — кто освобождает?). Требует явной транзакционной обёртки или saga.

---

### 4. Event-driven

Поведение **не вызывается**, а **реагирует**.

```php
class OrderConfirmed
{
    public function __construct(
        public readonly OrderId $orderId,
        public readonly \DateTimeImmutable $at,
    ) {}
}

// Публикатор
class OrderService
{
    public function __construct(
        private readonly EventPublisher $publisher,
    ) {}

    public function confirm(Order $order): void
    {
        // ... бизнес-логика
        $this->publisher->publish(new OrderConfirmed($order->id, new \DateTimeImmutable()));
    }
}

// Подписчики — отдельные классы, не знают друг о друге
class NotifyCustomerListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // email / push / sms
    }
}

class UpdateAnalyticsListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // метрики
    }
}

class SyncWarehouseListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // WMS
    }
}
```

**Плюс:** полная декомпозиция. Новый участник = новый listener. Ни один существующий класс не меняется.

**Минус:** теряется прямая зависимость "A вызывает B". Отладка сложнее — нужно знать, кто на что подписан. Идентичность/порядок не гарантированы без явного порядка.

**Когда использую:** пост-обработки, side-effects, интеграции. Не для основного потока, где нужен детерминированный результат.

---

### 5. Configuration-driven

Поведение определяется **данными**, а не кодом.

```yaml
# config/tax_rules.yaml
rules:
  - region: RU
    type: vat
    rate: "0.20"
  - region: DE
    type: vat
    rate: "0.19"
  - region: BR
    type: flat
    amount: "5.00"
```

```php
class TaxRuleEngine
{
    /** @var array<TaxRule> */
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function calculate(Money $amount, string $region): Money
    {
        $rule = $this->findRule($region);

        return match($rule->type) {
            'vat'  => $amount->multiply($rule->rate),
            'flat' => $rule->amount,
        };
    }
}
```

**Плюс:** новый вариант = новая строка в YAML. Не нужен деплой. Может управлять не-разработчик (product owner).

**Минус:** нет типа безопасности на саму конфигурацию. `match` — это всё-таки закрытый набор. Если тип `type` расширяется — всё равно нужно менять код.

---

### 6. First-class functions (PHP 8.1+)

```php
interface Validator
{
    public function validate(mixed $input): void;
}

class FormValidator
{
    /** @var array<Validator> */
    private array $validators;

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    public function run(mixed $input): void
    {
        foreach ($this->validators as $v) {
            $v->validate($input);
        }
    }
}

// Использование — closures как объекты
$validator = new FormValidator([
    new RequiredValidator('email'),
    new EmailFormatValidator('email'),
    new MinLengthValidator('password', 8),
    new function (mixed $input) {  // ← anonymous class или closure как Validator
        if (!isset($input['terms']) || $input['terms'] !== 'accepted') {
            throw new ValidationException('Terms not accepted');
        }
    }
]);
```

В PHP 8.1 есть `Closure::fromCallable`, а в 8.4 (RFC) — first-class callable syntax. Это позволяет использовать функции как polymorphic units без классов.

---

### Сравнение

| Подход | Тип-безопасность | Изоляция | Тестируемость | Когда |
|--------|-----------------|----------|---------------|-------|
| Наследование | Высокая | Средняя | Средняя | Иерархия естественна (животные, фигуры) |
| Strategy + DI | Высокая | Высокая | Высокая | 2–5 вариантов, ожидаются новые |
| Registry / Map | Низкая | Высокая | Низкая | Много вариантов, простая логика |
| Pipeline | Высокая | Высокая | Высокая | Последовательность шагов |
| Event-driven | Средняя | Максимальная | Средняя | Side-effects, интеграции |
| Config-driven | Низкая | Высокая | Низкая | Не-разработчики управляют |

---

### Мой практический выбор

| Ситуация | Подход |
|----------|--------|
| 3+ реализаций, сложная логика, ожидаются новые | **Strategy + DI** |
| Последовательность атомарных шагов | **Pipeline** |
| Side-effects после основного действия | **Event-driven** |
| Простые variant с разной конфигурацией | **Registry / Map** |
| Правило меняется без деплоя | **Config-driven** |
| Иерархия с общим API и различиями в деталях | **Наследование** (редко) |

Наследование — не плохое. Оно плохое, когда иерархия **не естественна**. "Card" не является "Crypto". "VatTax" не является "FlatRateTax". А вот "Circle" является "Shape" — здесь наследование оправдано.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

## Нет. SRP — это про "единицу изменения", а класс — лишь одна из таких единиц

Оригинальная формулировка Мэтина: *"A class should have one, and only one, reason to change."* Слово "class" здесь — артефакт того, что в 1990-х основным блоком кода был класс. В 2020-х блоки кода другие.

---

### Уровни, к которым применяется SRP

#### 1. Метод / функция

Самый частый уровень, где SRP бьёт больнее всего.

```php
class OrderService
{
    // АНТИПАТТЕРН: один метод, три ответственности
    public function processOrder(Order $order): Order
    {
        // 1. Валидация
        $this->validator->assert($order);

        // 2. Расчёт итога
        $total = $order->subtotal
            ->add($this->taxService->calculate($order))
            ->subtract($this->discountService->calculate($order));

        // 3. Бронирование склада
        $this->inventoryService->reserve($order->lines);

        // 4. Оплата
        $charge = $this->paymentService->charge($order, $total);

        // 5. Уведомление
        $this->emailService->sendConfirmation($order, $total);

        // 6. Аналитика
        $this->analyticsService->track('order_processed', ['id' => $order->id]);

        return $order->withTotal($total)->markAsPaid($charge->id);
    }
}
```

Шесть причин менять:
- Поменяли правила валидации → правлю этот метод
- Добавили новый налог → правлю этот метод
- Поменяли API payment-провайдера → правлю этот метод
- Поменяли шаблон письма → правлю этот метод
- Добавили новый event в analytics → правлю этот метод

**Рефакторинг на уровне метода:**

```php
class OrderService
{
    public function __construct(
        private readonly OrderValidator     $validator,
        private readonly TotalCalculator    $calculator,
        private readonly InventoryService   $inventory,
        private readonly PaymentService     $payment,
        private readonly EventPublisher     $publisher,
    ) {}

    public function processOrder(Order $order): Order
    {
        $this->validator->assert($order);

        $total = $this->calculator->calculate($order);

        $reservation = $this->inventory->reserve($order->lines);

        try {
            $charge = $this->payment->charge($order, $total);
        } catch (PaymentDeclinedException $e) {
            $this->inventory->release($reservation);
            throw $e;
        }

        $order = $order->withTotal($total)->markAsPaid($charge->id);

        $this->publisher->publish(new OrderProcessed($order));

        return $order;
    }
}
```

Метод стал короче, но **ответственность "процессировать заказ" осталась одной**. Валидация, расчёт, бронирование, оплата — вынесены в отдельные классы. Email и analytics — вынесены в listener. Метод теперь читается как сценарий, а не как свалка.

---

#### 2. Файл / модуль

```php
// src/Order/Order.php — 400 строк

class Order
{
    // Сущность
    public function __construct(...) { ... }

    // Поведение
    public function confirm(): self { ... }
    public function cancel(): self { ... }
    public function refund(): self { ... }

    // Расчёты
    public function subtotal(): Money { ... }
    public function tax(): Money { ... }
    public function total(): Money { ... }

    // Сериализация
    public function toArray(): array { ... }
    public function toDTO(): OrderDTO { ... }
    public function fromArray(array $data): self { ... }

    // Утилиты
    public function isOld(): bool { ... }
    public function daysSinceCreated(): int { ... }
    public function hasCoupon(): bool { ... }

    // Миграции (да, я это видел)
    public function upgradeFromLegacy(): void { ... }
}
```

Это **один файл, шесть ответственностей**. Разные команды трогают разные части: доменщики — `confirm/cancel/refund`, frontend — `toArray`, data team — `daysSinceCreated`.

**Разделение:**

```
src/Order/
├── Order.php              // Сущность + инварианты (confirm, cancel, refund)
├── OrderCalculator.php    // Расчёты (subtotal, tax, total)
├── OrderSerializer.php    // Сериализация (toArray, toDTO, fromArray)
└── OrderQuery.php         // Запросы (isOld, daysSinceCreated)
```

---

#### 3. Сервис / компонент (системный уровень)

```
Микросервис "order-service":
- API для управления заказами
- Отправка email
- Обработка webhook'ов от payment-провайдера
- Cron-задача: отмена неоплаченных заказов
- Репортаж в BI
```

Пять причин менять:
- Поменяли API → деплой
- Поменяли шаблон письма → деплой
- Поменяли payment-провайдер → деплой
- Поменяли расписание cron → деплой
- Поменяли схему отчёта → деплой

Каждая из этих причин — **отдельный сервис** (или хотя бы отдельный deployable unit).

---

#### 4. Базовая таблица (data-level SRP)

```sql
CREATE TABLE orders (
    id          BIGINT PRIMARY KEY,
    -- Заказ
    status      VARCHAR(20),
    total       NUMERIC(10,2),
    -- Клиент
    customer_email VARCHAR(255),
    customer_phone VARCHAR(50),
    -- Платёж
    payment_method VARCHAR(30),
    payment_gateway_id BIGINT,
    -- Аналитика
    created_by_campaign VARCHAR(100),
    utm_source VARCHAR(255),
    -- Сервис
    internal_notes TEXT,
    -- Миграционный мусор
    legacy_order_id BIGINT,
    migrated_at TIMESTAMP
);
```

Одна таблица, пять причин менять. Разделение: `orders`, `payments`, `order_analytics`, `order_migrations`.

---

### Где SRP на уровне метода важнее, чем на уровне класса

Частая ошибка: разработчик видит "класс слишком большой" и делит его на несколько, **не трогая методы**.

```php
// "Разделил" класс:
class OrderService      { public function confirm() { /* 80 строк, 6 шагов */ } }
class OrderQueryService { public function findByStatus() { /* 60 строк, 4 шагов */ } }

// vs. "Разделил" метод:
class OrderService
{
    public function confirm(): Order
    {
        return $this->doConfirm($this->loadForConfirmation($id));
    }

    private function loadForConfirmation(int $id): Order { /* 10 строк */ }
    private function doConfirm(Order $order): Order      { /* 20 строк */ }
}
```

Второй вариант **лучше** с точки зрения SRP: каждая функция имеет одну причину менять. Первый — "разделённый" класс, где каждый метод всё ещё делает слишком много.

**Правило:** сначала делай методы. Потом, если методы стали независимыми по времени изменения — выноси в класс.

---

### Где "один класс — одна ответственность" вредит

#### 1. Value Object

```php
readonly class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zip,
        public string $country,
    ) {}
}
```

SRP: "Address имеет одну причину менять — если меняется формат адреса". Это **одна** причина. Не "один метод — одна ответственность". Value Object по определению несёт несколько связанных полей. Делить на `AddressStreet`, `AddressCity` — абсурд.

#### 2. DTO на границе

```php
class CreateOrderRequest
{
    public string $customerEmail;
    public string $shippingAddress;
    public array  $items;
    public string $couponCode;
}
```

Все поля — одна ответственность: "представить входящий запрос". Делить на `CustomerRequest`, `ShippingRequest`, `ItemsRequest` — усложняет, не упрощает.

#### 3. Конфигурация

```php
class DatabaseConfig
{
    public string $host;
    public int    $port;
    public string $name;
    public string $user;
    public string $password;
}
```

Одна причина менять: "изменили конфигурацию БД". Пять полей — не пять ответственностей.

---

### Итого

| Утверждение | Верно? |
|-------------|--------|
| SRP — про классы | Нет. Про любую единицу кода |
| "Один класс — одна ответственность" | Слишком узко. Метод может быть более важной единицей |
| "Один метод — одна задача" | Ближе к истине, но не всегда: Value Objects, DTO, конфиг |
| SRP — это "не больше 50 строк в методе" | Нет. Это про причину изменения, не про размер |
| Разделение класса автоматически улучшает SRP | Нет. Если методы внутри всё ещё делают много — SRP не улучшен |

Формулировка, которая у меня в голове: **"Если две причины изменения не связаны — они не должны жить в одном блоке кода."** Класс — просто один из блоков.

> ⏱ 26.1s · prompt 9114 tok · output 3583 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про DI-контейнер Symfony: компиляция, autowiring, lazy loading. Это продвинутый вопрос, который показывает глубину понимания. Нужно дать честный, детальный ответ с примерами, а не поверхностный "Symfony делает магию". 

Структурирую ответ так:
1. Что такое контейнер концептуально
2. Компиляция: что реально происходит (CompilerPass, autowiring, autoconfiguration)
3. Autowiring: как определяется тип, как резолвится
4. Moment of use: что происходит при get()
5. Lazy services и прокси
6. Практические ловушки

Нужно показать, что я понимаю internals, а не только "пишу yaml".

</details>

**Кандидат:**

## DI-контейнер Symfony: что происходит на самом деле

### Концептуально

Контейнер — это **фабрика объектов**. Он не хранит готовые объекты (за редким исключением). Он хранит **рецепты**: как и из чего собрать объект, когда его запросят.

```
DI Container ≠ объект, который держит инстансы
DI Container = сборка инструкций + кэш инстансов (singleton scope)
```

---

### Фаза 1: Компиляция контейнера

Происходит **один раз** при деплое (или `cache:clear`). Результат — кэш в `var/cache/`.

#### 1.1 Сбор definitions

Symfony собирает определения сервисов из:

```yaml
# config/services.yaml
services:
    App\Service\OrderService:
        arguments:
            $repository: '@App\Repository\OrderRepository'
            $taxCalculator: '@App\Tax\VatTaxCalculator'

# config/packages/*.yaml (symfony бандлы)
# src/Entity/*.php (через Doctrine extension)
# src/MessageHandler/*.php (через Messenger extension)
```

Каждый сервис — это **Definition**:

```php
// Упрощённо, что лежит в памяти во время сборки:
class Definition implements ServiceDefinition
{
    private string $class;           // App\Service\OrderService
    private string $public = false;  // доступен через get()?
    private string $shared = true;   // singleton?
    private array  $arguments;       // [0 => Reference('App\Repository\OrderRepository'), ...]
    private array  $methodCalls;     // [['configure', [...]], ...]
    private string $factory;         // null или [class, method]
    private string $autowire = true; // разрешён autowiring?
    private string $autoconfigure = true;
}
```

#### 1.2 Autowiring — как он работает

Это **не** рефлексия в рантайме. Это **статический анализ сигнатур** на этапе сборки.

```php
class OrderService
{
    public function __construct(
        OrderRepository $repository,      // ← тип аргумента
        TaxCalculator   $taxCalculator,   // ← тип аргумента
        EventPublisher  $publisher,       // ← тип аргумента
        string          $defaultCurrency, // ← скаляр, autowiring не поможет
    ) {}
}
```

Что делает autowiring:

1. Читает `ReflectionType` каждого параметра.
2. Для каждого типа ищет в контейнере **один** сервис с этим классом (или реализацией интерфейса).
3. Если нашёл ровно один — подставляет `Reference`.
4. Если нашёл ноль — ошибка сборки (если нет default value).
5. Если нашёл больше одного — ошибка: ambiguous autowiring.

```php
// Внутренности (Symfony\Component\DependencyInjection\Compiler\AutowirePass):
foreach ($definition->getArguments() as $i => $arg) {
    if ($arg instanceof TypedReference) {
        $type = $arg->getType();
        
        $candidates = $this->container->findDefinition($type);
        
        if ($candidates === null) {
            throw new Exception("Cannot autowire service $type: no candidate found.");
        }
        
        if (count($candidates) > 1) {
            throw new Exception("Cannot autowire $type: multiple candidates. Use tagged iterator or explicit binding.");
        }
        
        $definition->setArgument($i, new Reference($candidates[0]));
    }
}
```

**Ключевой момент:** autowiring работает **только по типу**. Не по имени переменной. Не по docblock. Не по `@param`. Только по PHP-типу.

```php
// Это НЕ будет autowired:
public function __construct(
    array $config,           // ← скаляр, autowiring не знает, какой массив
    string $prefix = "app",  // ← можно через $env() или explicit binding
) {}

// Это будет:
public function __construct(
    PaymentGateway $gateway,  // ← если в контейнере ровно один PaymentGateway
) {}
```

#### 1.3 Autoconfiguration

Автосоздание тегов и listener'ов на основе интерфейсов:

```php
// Если класс реализует EventSubscriber → автоматически получает tag "kernel.event_subscriber"
class MyListener implements EventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onRequest',
        ];
    }
}
```

```php
// Если класс реализует MessageHandler → tag "messenger.message_handler"
class OrderPaidHandler implements MessageHandler
{
    public function __invoke(OrderPaid $event): void { ... }
}
```

Это делается через `RegisterEventListenersPass` и специфические passes бандлов.

#### 1.4 CompilerPass — порядок выполнения

```
1. RegisterFqcnAliasPass          // короткие имена → FQCN
2. RegisterAttributeAutoConfigurationPass  // #[Autoconfigure], #[AsCommand]
3. AutowirePass                    // ← основной
4. ResolveBindingsPass             // explicit bindings (string $currency => 'USD')
5. ResolveChildDefinitionsPass     // parent/child
6. ResolveHotpathPass              // inline-инлайт для hot path
7. RegisterEnvVarProcessorsPass    // %env()%
8. ResolveInstanceofConditionalsPass
9. CheckDefinitionValidityPass     // ошибки: циклы, неизвестные сервисы
10. CheckReferenceValidityPass
11. CheckExceptionAliasPass
12. ...
13. ResolveHotpathPass             // финальный инлайт
14. DumpPass                       // ← генерация PHP-файлов
```

#### 1.5 Что попадает в кэш

В `var/cache/prod/` генерируются:

```
services_ContainerXXXX.php          // главный класс-контейнер
services_ContainerXXXX::removeDefinition.php
services_ContainerXXXX.php.lock
```

Главный файл — это **один класс** со всеми сервисами:

```php
// var/cache/prod/ContainerXXX/xxx_KernelProjectDebugContainer.php
class xxx_KernelProjectDebugContainer extends Container
{
    protected array $initialValues = [];
    protected array $compiledServices = [];
    protected array $loadedServices = [];
    protected array $privates = [];
    protected array $parameters = [...];

    public function __construct(string $buildDir)
    {
        $this->buildDir = $buildDir;
        $this->services = [];
        // ...
    }

    // Singleton — ленивая инициализация
    protected function createServiceAppServiceOrderService(): OrderService
    {
        return new OrderService(
            $this->get('App\Repository\OrderRepository', true),
            $this->get('App\Tax\VatTaxCalculator', true),
            $this->get('App\Event\EventPublisher', true),
            'USD',
        );
    }

    // Shared (singleton)
    public function get(string $id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE): mixed
    {
        $services = &$this->services;

        // Уже создан? Возвращаем кэш
        if (isset($services[$id])) {
            return $services[$id];
        }

        // Не создан? Создаём
        return $services[$id] = $this->createServiceAppServiceOrderService();
    }
}
```

**Это статический код, не reflection.** Никаких `ReflectionClass::newInstance()`. Никаких `is_a()`. Никаких `method_exists()`. Быстрее, чем `new` в 10–30 раз (на hot path).

---

### Фаза 2: Runtime — что происходит при `$container->get()`

```php
class OrderController
{
    public function __construct(
        private readonly OrderService $service,  // ← injected при создании контроллера
    ) {}

    public function __invoke(): Response
    {
        $order = $this->service->find(42);
        return $this->json($order);
    }
}
```

Пошагово:

#### Шаг 1: Kernel boot → контейнер загружен

```php
// bin/console (или front controller)
$kernel = new Kernel($env);
$container = $kernel->getContainer();  // ← here
```

Что происходит:

```php
// Symfony\Component\HttpKernel\Kernel::getContainer():
public function getContainer(): ContainerInterface
{
    if (null === $this->container) {
        $this->initializeContainer();
    }
    return $this->container;
}

private function initializeContainer(): void
{
    // 1. Определяем class-имя кэша
    $class = $this->getContainerClass();  // "xxx_KernelProjectProdContainer"

    // 2. Подключаем файл из кэша
    require_once $this->getBuildDir() . '/' . $class . '.php';

    // 3. Создаём экземпляр
    $this->container = new $class($this->getBuildDir());
}
```

Файл кэша **уже сгенерирован** при деплое. На этом этапе **нет** компиляции, **нет** reflection, **нет** анализа. Только `require_once` + `new`.

#### Шаг 2: Сервис запрошен

```php
$controller = $container->get('App\Controller\OrderController');
```

Внутри:

```php
public function get(string $id): mixed
{
    $services = &$this->services;

    // Уже создан?
    if (isset($services[$id])) {
        return $services[$id];
    }

    // Нет → создаём
    return $services[$id] = $this->createServiceAppControllerOrderController();
}

protected function createServiceAppControllerOrderController(): OrderController
{
    return new OrderController(
        $this->get('App\Service\OrderService', true),
    );
}
```

`$this->get('App\Service\OrderService', true)` — рекурсивный вызов. То же самое: кэш или создание.

#### Шаг 3: Создание объекта

```php
protected function createServiceAppServiceOrderService(): OrderService
{
    return new OrderService(
        $this->get('App\Repository\OrderRepository', true),
        $this->get('App\Tax\VatTaxCalculator', true),
        $this->get('App\Event\EventPublisher', true),
        'USD',
    );
}
```

Каждый аргумент — либо `Reference` (ещё один `get()`), либо литерал (`'USD'`). Рекурсия продолжается до самых глубоких зависимостей.

**Важно:** если `OrderRepository` уже создан (singleton scope), `get()` вернёт кэш. Новый объект **не создаётся**. Это и есть DI: один экземпляр, много пользователей.

---

### Lazy services и прокси

Проблема: контейнер по умолчанию **жадный** — если сервис объявлен, он создаётся при первом `get()`. Но если у вас 200 сервисов, и контроллер использует только 3 — зачем создавать 200?

Решение — **lazy loading через прокси**:

```yaml
# config/services.yaml
services:
    App\Service\OrderService:
        lazy: true  # ← прокси создаётся, реальный объект — при первом вызове метода
```

Что в кэше:

```php
protected function createServiceAppServiceOrderService(): OrderService
{
    // Не создаём OrderService. Создаём прокси.
    return new class extends OrderService implements \ProxyManager\Proxy\ProxyInterface
    {
        private ?OrderService $realInstance = null;

        public function __invoke()
        {
            if ($this->realInstance === null) {
                $this->realInstance = $this->createServiceAppServiceOrderService();
            }
            return $this->realInstance->__invoke();
        }

        // Каждый публичный метод — делегируется:
        public function find(int $id): Order
        {
            $this->initialize();  // ← ленивое создание
            return $this->realInstance->find($id);
        }
    };
}
```

Symfony использует **Symfony Serializer** + **ProxyManager** (или собственную реализацию с 5.3). Прокси перехватывает вызовы и лениво создаёт реальный объект.

**Когда это критично:**

```php
class DashboardController
{
    public function __construct(
        private OrderService    $orders,      // ← не нужен для dashboard
        private UserService     $users,       // ← нужен
        private AnalyticsService $analytics,  // ← не нужен
    ) {}
}
```

Без `lazy: true` — при создании контроллера создаются **все три** сервиса, включая их зависимости. С `lazy: true` — создаются только те, чьи методы реально вызваны.

---

### Scoped services

По умолчанию — **singleton** (shared). Один экземпляр на весь request.

```yaml
services:
    App\Service\OrderService:
        shared: false  # ← новый экземпляр при каждом get()
```

Когда нужно `shared: false`:

```php
class FormState  // ← мутируемый объект
{
    public function __construct(
        public array $data = [],
        public array $errors = [],
    ) {}
}
```

Если `FormState` — singleton, все контроллеры разделяют один экземпляр. `shared: false` — каждый `get()` возвращает новый.

**В production я почти всегда использую `shared: true`** (default). `shared: false` — редкость, и если она нужна — это сигнал, что объект мутируемый, а значит, нарушает SRP или DDD-инварианты.

---

### Практические ловушки

#### 1. Autowiring по interface с несколькими реализациями

```php
interface PaymentGateway
{
    public function charge(...): Charge;
}

class StripeGateway implements PaymentGateway { ... }
class AdyenGateway implements PaymentGateway { ... }
```

Autowiring **не знает**, какой выбрать. Ошибка сборки.

Решения:
- Явная привязка: `arguments: [$gateway: '@App\Gateway\StripeGateway']`
- Tagged iterator: `/** @var iterable<PaymentGateway> */ iterable $gateways`
- Binding: `bind: { PaymentGateway: '@App\Gateway\StripeGateway' }`

#### 2. `private` vs `public`

```yaml
services:
    App\Service\OrderService:
        public: true  # ← доступен через $container->get()
```

`public: false` (default) — сервис **недоступен** через `get()`, но **доступен** через autowiring. Это важно: вы не можете сделать `private`-сервис public "для отладки", если он не валиден.

#### 3. `!service` vs autowiring

```yaml
# Явная привязка:
services:
    App\Service\OrderService:
        arguments:
            $taxCalculator: '@App\Tax\CustomTaxCalculator'

# vs. Autowiring:
services:
    App\Service\OrderService:
        # taxCalculator: автоподставляется по типу
```

Явная привязка **перебивает** autowiring. Используйте, когда:
- Есть несколько реализаций
- Хотите другой экземпляр (например, mock в тесте)
- Тип — interface, и вы хотите конкретную реализацию

#### 4. Циклические зависимости

```yaml
# A зависит от B, B зависит от A → ошибка
services:
    App\Service\A:
        arguments: ['@App\Service\B']
    App\Service\B:
        arguments: ['@App\Service\A']
```

Symfony **не даст** это скомпилировать. Решения:
- `@!` (lazy reference) — один из двух становится lazy
- Рефакторинг: вынести общее в C
- Event: A публикует event, B слушает

#### 5. `!` и `?` в аргументах

```yaml
# Обязательный аргумент (нет default):
arguments:
    $gateway: '@App\Gateway\StripeGateway'

# Необязательный (default null):
arguments:
    $gateway: null  # ← аутвайринг пропустит, если null
```

В PHP:

```php
public function __construct(
    ?PaymentGateway $gateway = null,  // ← null-по умолчанию
) {}
```

Symfony не подставит `Reference`, если аргумент `null` по умолчанию. Это feature, не bug.

---

### Итого: полный путь

```
ДЕПЛОЙ:
  services.yaml + bандлы + entity
       ↓
  Компилляция (AutowirePass, CheckPass, DumpPass)
       ↓
  var/cache/prod/ContainerXXX.php  ← статический PHP-файл

ПРОС:
  bin/console / front controller
       ↓
  require_once ContainerXXX.php
       ↓
  new ContainerXXX()
       ↓
  $container->get('App\Controller\OrderController')
       ↓
  Рекурсивное создание (кэш или new)
       ↓
  Контроллер получен. Готов к работе.
```

Никакой магии. Статический код, сгенерированный один раз. Runtime — это `require` + `new` + кэш.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

## Циклические зависимости: как разруливаю

### Сначала — почему цикл появился

Цикл `A → B → A` почти всегда означает одно из двух:

1. **A и B делают одну вещь**, но кто-то разделил их "по логике", а не по данным.
2. **Один из них — координатор**, который должен знать про обоих.

Перед тем как применять "ремонт" (lazy, event), я спрашиваю: *"Что A пытается сделать через B, что B пытается сделать через A?"* Если ответ — "координировать" — цикл лечится рефакторингом, а не трюками.

---

### Способ 1: Вынести общее в C (инверсия)

Самый частый и самый правильный.

**Было:**

```
OrderService → InventoryService → OrderService
  (заказывает)    (бронирует)       (проверяет статус заказа)
```

**Почему цикл:** `InventoryService` при бронировании хочет проверить, что заказ ещё не отменён. `OrderService` при отмене хочет отменить бронь. Оба знают про обоих.

**Лечение:**

```php
// C — источник правды о состоянии заказа
interface OrderStateProvider
{
    public function getStatus(int $orderId): OrderStatus;
}

// OrderService реализует:
class OrderService implements OrderStateProvider
{
    public function getStatus(int $orderId): OrderStatus
    {
        return $this->repo->find($orderId)->status;
    }
}

// InventoryService зависит от интерфейса, а не от OrderService:
class InventoryService
{
    public function __construct(
        private readonly OrderStateProvider $orderState,  // ← не OrderService
    ) {}

    public function reserve(int $orderId, array $lines): Reservation
    {
        $status = $this->orderState->getStatus($orderId);

        if ($status === OrderStatus::Cancelled) {
            throw new OrderAlreadyCancelledException($orderId);
        }

        // ... бронирование
    }
}
```

**Зависимости стали:**

```
OrderService → InventoryService → OrderStateProvider (интерфейс)
     ↑                                              |
     └──────────────────────────────────────────────┘
```

Цикл ушёл. `InventoryService` не знает, *кто* предоставил статус. `OrderService` не знает, *кто* проверяет статус.

**Когда работает:** A и B знают друг о друге, но на самом деле им нужна **одна информация или одно действие**, а не весь объект.

---

### Способ 2: Event-driven (развязка во времени)

Если A должен "уведомить" B, а B должен "отреагировать" на A — это не DI, это **сообщение**.

**Было:**

```php
class OrderService
{
    public function __construct(
        private readonly InventoryService $inventory,  // ← цикл
    ) {}

    public function confirm(Order $order): void
    {
        $this->inventory->reserve($order->lines);  // синхронный вызов
        // ...
    }
}

class InventoryService
{
    public function __construct(
        private readonly OrderService $orders,  // ← цикл
    ) {}

    public function reserve(array $lines): void
    {
        // ...
        // Хочет обновить состояние заказа:
        $this->orders->markAsReserved($orderId);
    }
}
```

**Лечение:**

```php
// OrderService публикует событие
class OrderService
{
    public function __construct(
        private readonly EventPublisher $publisher,
    ) {}

    public function confirm(Order $order): void
    {
        $this->publisher->publish(new OrderConfirmed($order));
    }
}

// InventoryService слушает
class InventoryListener
{
    public function __construct(
        private readonly InventoryRepository $repo,
    ) {}

    public function onOrderConfirmed(OrderConfirmed $event): void
    {
        $this->repo->reserve($event->orderId, $event->lines);
    }
}

// InventoryService больше не знает про OrderService
class InventoryService
{
    // Никакой зависимости от OrderService
    public function reserve(array $lines): void { ... }
}
```

**Когда работает:**
- A и B не должны быть синхронны (B может обработать позже)
- Б не должен влиять на результат A (если B упал — A уже завершил)
- Есть 2+ подписчиков на одно событие

**Когда НЕ работает:**
- B должен быть **до** завершения A (транзакционная целостность)
- Результат B нужен A в тот же миг

Если нужно синхронно — event не поможет. Идите к способу 1 или 3.

---

### Способ 3: Lazy reference (Symfony-трюк)

Если рефакторинг невозможен (legacy, чужой код, дедлайн) — Symfony позволяет один из двух сервисов стать **ленивым**.

```yaml
# config/services.yaml
services:
    App\Service\OrderService:
        arguments:
            $inventory: '@!App\Service\InventoryService'
            #                 ^ эта "!" делает ссылку ленивой
```

Что это делает:

```php
// Вместо:
class OrderService
{
    public function __construct(
        private InventoryService $inventory,  // ← создаётся при new OrderService
    ) {}
}

// Получается:
class OrderService
{
    public function __construct(
        private InventoryService $inventory,  // ← прокси, создаётся при первом вызове
    ) {}
}
```

Symfony подставляет **прокси** вместо реального объекта. Проокси не содержит `OrderService` в своих зависимостях, потому что он его ещё не создал. Когда вы вызываете `$this->inventory->reserve(...)`, прокси лениво создаёт реальный `InventoryService`, который уже может получить `OrderService` из кэша (он уже создан).

**Порядок создания с `@!`:**

```
1. new OrderService(proxy_of_InventoryService)  ← ок, прокси не создаёт инвентарь
2. $orderService->confirm()
   → $this->inventory->reserve()
   → прокси создаёт InventoryService
   → InventoryService.__construct(OrderService)
   → OrderService уже в кэше → возвращаем
   → готово, цикл не сломался
```

**Минусы:**

| Проблема | Почему |
|----------|--------|
| Скрытая стоимость | Первый вызов метода — медленный (создание + инициализация). Остальные — быстрый. |
| Сложная отладка | В трейсбеке видите прокси, не реальный объект. `var_dump($inventory)` — не тот тип. |
| Маскирует проблему | Цикл не исчез. Вы просто скрыли его под прокси. Через 6 месяцев кто-то добавит третий сервис в цикл — и `@!` перестанет помогать. |
| Не работает для `shared: false` | Если `InventoryService` — не singleton, каждый `get()` создаёт новый, и `OrderService` внутри него — тоже новый. Вы получаете "объект с чужим контекстом". |

**Когда использую:**
- Legacy-код, где рефакторинг не входит в текущую задачу
- Один конкретный цикл, который я документировал и поставил в tech debt
- Быстрый hotfix, чтобы CI прошёл

**Когда НЕ использую:**
- Я проектирую с нуля — цикл = ошибка дизайна, лечу по-настоящему
- `shared: false` — ленивость не спасает
- Цикл из 3+ сервисов — `@!` на одном из них не решит проблему

---

### Способ 4: Setter injection / late binding

```php
class OrderService
{
    private ?InventoryService $inventory = null;

    public function setInventory(InventoryService $inventory): void
    {
        $this->inventory = $inventory;
    }

    public function confirm(Order $order): void
    {
        if ($this->inventory === null) {
            throw new \LogicException('InventoryService not injected. Call setInventory() first.');
        }
        $this->inventory->reserve($order->lines);
    }
}
```

```yaml
services:
    App\Service\InventoryService:
        calls:
            - [setOrderService, ['@App\Service\OrderService']]
```

**Минусы:**
- `null`-check в каждом методе (или `assert`)
- Обнаружение ошибки — в рантайме, не при компиляции
- Тесты должны не забыть вызвать `setInventory()`

**Когда использую:** практически никогда. Это антипаттерн, который я допускаю только в legacy-коде, где менять конструктор нельзя (например, `__construct` уже используется в другом месте с другим набором аргументов).

---

### Способ 5: Рефакторинг на уровне домена

Иногда цикл — это признак того, что **границы сущностей нарисованы неправильно**.

Пример из практики:

```
PaymentService → OrderService → PaymentService

PaymentService: "для списания денег мне нужен заказ"
OrderService:   "для изменения статуса мне нужен результат платежа"
```

Рефакторинг:

```php
// Платёж — не действие над заказом. Платёж — самостоятельная сущность.
// Заказ ссылается на платёж. Платёж не знает про заказ.

class Payment
{
    public function __construct(
        public readonly PaymentId $id,
        public readonly Money     $amount,
        public readonly Money     $currency,
    ) {}

    public function markAsCharged(string $gatewayId): self
    {
        return new self($this->id, $this->amount, $this->currency, $gatewayId);
    }
}

class Order
{
    public function __construct(
        public readonly OrderId  $id,
        public readonly Money    $amount,
        public readonly Payment  $payment,  // ← содержит платёж
    ) {}

    public function withChargedPayment(string $gatewayId): self
    {
        return new self($this->id, $this->amount, $this->payment->markAsCharged($gatewayId));
    }
}

// PaymentService зависит от PaymentGateway, НЕ от OrderService
class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    public function charge(Payment $payment): Payment
    {
        $result = $this->gateway->charge($payment);
        return $payment->markAsCharged($result->id);
    }
}

// OrderService зависит от PaymentService, НЕ наоборот
class OrderService
{
    public function __construct(
        private readonly PaymentService $payments,
    ) {}

    public function processPayment(Order $order): Order
    {
        $payment = $this->payments->charge($order->payment);
        return $order->withChargedPayment($payment->id);
    }
}
```

**Зависимости:**

```
OrderService → PaymentService → PaymentGateway
     ↓
   Order (доменная сущность, не сервис)
```

Никаких циклов. Потому что **доменные сущности не зависят от сервисов**. Сервисы зависят от сущностей и друг от друга по DAG (acyclic graph).

---

### Мой порядок применения

| Приоритет | Способ | Когда |
|-----------|--------|-------|
| 1 | Рефакторинг границ (способ 5) | Проектирую с нуля. Цикл = ошибка модели. |
| 2 | Вынести общее в интерфейс (способ 1) | A и B знают друг о друге, но на деле нужна одна операция. |
| 3 | Event (способ 2) | Развязка во времени допустима. 2+ подписчиков. |
| 4 | `@!` lazy (способ 3) | Legacy. Один цикл. Документирован. |
| 5 | Setter (способ 4) | Никогда, если можно иначе. |

---

### Антипаттерн, который я не делаю

```php
// "Решение" через статик:
class OrderService
{
    public static function instance(): self { ... }
}

class InventoryService
{
    public function __construct()
    {
        $this->orders = OrderService::instance();  // ← global state
    }
}
```

Это не разруливает цикл. Это **маскирует** его, делает его невидимым для DI, тестируемым только через интеграцию, и создаёт hidden coupling, который невозможно отслеживать. Через год кто-то увидит `OrderService::instance()` и не поймёт, почему `InventoryService` зависит от глобального состояния.

> ⏱ 33.0s · prompt 18977 tok · output 4170 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

<details><summary>Ход мысли модели</summary>

We need answer in Russian. User asks: after successful registration send email and update search index, not slow response. How organize in Symfony? Describe options and trade-offs. Need likely discuss Messenger async, events, transactions/outbox, webhooks? Need provide concrete Symfony approach. Need final only. Need be thorough but not too huge. Need maybe include architecture: DDD, transactional outbox, Messenger with DoctrineTransport, handlers, retry, idempotency, dead letter, alternatives: sync after response? event listeners, scheduled tasks, queue, outbox, webhooks, pub/sub, transactional outbox, CQRS/search index, email queue. Need compare trade-offs. Need mention DB transaction and consistency: don't send email before commit? Actually after successful registration likely after commit. If using events dispatched in kernel after response, but if using in request with async messenger, need ensure transaction commit before send? Options:
1. Messenger (Symfony) with Doctrine/AMQP transport: register handlers for UserRegistered, send in worker processes. But need transactional outbox to avoid lost events if process crashes after publish? In Symfony Messenger + Doctrine: if you dispatch after flush, it's ok? But if transport not transactional? Actually if using DoctrineMessenger, can dispatch during transaction, but transport can publish before commit? Need use outbox or `DispatchAfterCurrentTransactionMiddleware`? Symfony has Doctrine transaction middleware? There's `Doctrine\Transport\Doctrine`? Need be precise. Options:
- Event-driven inside request: dispatch domain event after repository save, subscriber uses messenger->dispatch new SendEmail, new UpdateSearchIndex with SendLaterTransport? These become async tasks if transport is async (e.g. Doctrine messages table, AMQP). Response returns after enqueuing, not actual work. Worker does email/search.
- Use Doctrine transport with `SendFailedMessageForRetryListener`? Need not overdo.
- Transactional outbox: write event rows in same DB transaction as user, worker polls/publishes to queue, consumer idempotently processes. Guarantees at-least-once, no lost events, consistency across services. Higher complexity.
- Fire and forget with `async` messenger, but risk if crash between commit and enqueue? If enqueue after commit, possible duplicate if consumer retries. Need idempotency.
- Scheduled cron: user created with pending_email flag, worker picks up. Simple but higher latency and not real-time.
- Websockets/pusher not relevant.
- Search index update: Elasticsearch/Meilisearch/Typesense, use repository listener? Options: synchronous indexing (bad), async event handler, outbox, CDC (Debezium) or search engine triggers. Trade-offs: search consistency eventual, idempotent upsert by id, delete stale.
Need propose recommended: Domain event UserRegistered emitted after persistence commit; handlers via Messenger async with separate consumers: SendRegistrationEmail and UpdateSearchIndex; use Doctrine/AMQP queue; idempotent handlers; retries and dead-letter; optional transactional outbox if strong consistency. Need include Symfony code snippets. Need discuss when to choose sync vs async, in-request after commit, transaction boundaries.
Need maybe mention `MessageHandlerInterface` and routing handlers. Need mention concurrency, ordering? Search index should be idempotent. Email should have template, i18n, rate limiting. Need maybe mention `#[AsMessageHandler]`, `messenger.buses.default.messages['App\Message\SendRegistrationEmail'] = 'async'`.
Need include pitfalls: if using in-memory transport in dev; if using `SendLaterTransport`; if email provider down; if search index down; need decouple. Need mention that `kernel.response` listener can dispatch after response? But better not because after response may be too late? In Symfony, you can dispatch messenger messages after response in listener? But not ideal. Use event after domain event. Need maybe mention transaction commit: if registration is in transaction, dispatch after commit, not before, to avoid indexing non-committed user. With Doctrine, events? Could use `postFlush`? But domain events after save. Use `UnitOfWork::getEvents`? Not needed.
Need answer as interview style, concise but detailed. Provide final recommended architecture. Ensure no hidden chain. Need mention outbox as option for cross-service reliability.
Need consider Symfony specifics:
- Define event/message: `UserRegistered { public function __construct(public readonly int $userId, public readonly string $email) {} }`
- In service: after `$this->em->flush();` dispatch `UserRegistered`. But if transaction commit? In single transaction, after flush commit done? In Symfony/Doctrine, `flush` writes and commits if not in transaction. If using transaction, ensure after commit. Could use `TransactionMiddleware` or manual `em->wrapInTransaction`. Need mention.
- Subscribers: `class UserRegisteredListener { public function onUserRegistered(UserRegistered $e) { $this->bus->dispatch(new SendRegistrationEmail($e->userId)); $this->bus->dispatch(new UpdateSearchIndex('users', $e->userId)); } }` with `#[AsMessageHandler]`? Actually handlers for messages not event. Use Symfony EventDispatcher for domain event and Messenger for async tasks. Or directly dispatch messages from service. Better DDD: domain event `UserRegistered` via Symfony EventDispatcher; listener enqueues messages. Or use Messenger event? Need separate concerns.
- Config: `messenger.routing: SendRegistrationEmail: email, UpdateSearchIndex: search`. Transports: doctrine/redis/amqp. Workers: `bin/console messenger:consume email search --limit`. Separate queues allow scaling and independent failure.
- Retry: `SendRegistrationEmail::class: retry_strategy: {max_retries: 3, delay: 1000}`. `HandleFailedMessageForRetryListener`? In Symfony Messenger, configure `messenger.failure_transport`? Need generic.
- Idempotency: email handler checks `email_sents` table with unique(message_id/user_id/event_id). Search handler upserts by id; include version/event id; discard old updates? For search can use `update_after` timestamp.
- Transactional outbox: tables `outbox_events(id, type, payload, created_at, processed_at)`. In same transaction: insert user and outbox event. Relay worker reads and publishes to queue. Consumers dedupe by event id. Guarantees no lost events; at-least-once. Overhead: extra table, relay.
Need trade-offs table:
Option A: synchronous handlers after response? Actually not after response but same request before response: slow, user waits; email/search failures affect registration; bad.
Option B: async Messenger in-request: simple, fast response, in-process enqueue, but if enqueue fails or worker down, events may be delayed/lost depending transport; needs retries, DLQ; consistency if dispatch before commit? Need careful.
Option C: transactional outbox: strong consistency, reliable, more complex, higher latency, need idempotency.
Option D: cron/polling flags: simple, robust, higher latency, not scalable for real-time.
Option E: external queue with producer in app: similar to B, but if app crashes after DB commit before publish, lost event unless outbox or two-phase.
Option F: CDC/search index: for search only, reliable but infra.
Need recommended: for most Symfony apps: async Messenger + separate queues + idempotent handlers; add outbox if cross-service/critical.
Need maybe mention `SendLaterTransport` not needed. `async` transport. In production, separate worker processes, autoscale based on queue size. Need maybe mention dead letter, monitoring, metrics, alerts.
Need include code snippets:
```
#[AsMessageHandler]
final class SendRegistrationEmailHandler
{
  public function __construct(private Mailer $mailer, private EntityManagerInterface $em) {}
  public function __invoke(SendRegistrationEmail $msg) { ... idempotent ... }
}
```
For search:
```
final class UpdateSearchIndexHandler implements MessageHandlerInterface
{
 public function __invoke(UserSearchIndexUpdate $msg): void
 {
   $user = $this->repo->find($msg->userId);
   if (!$user) return; // deleted
   $this->index->upsert(['id'=>$user->id,'name'=>..., 'updated_at'=>...]);
 }
}
```
Need mention if user deleted, handler should delete index. Could use event UserDeleted. Or handler checks user and if null removes index.
Need discuss ordering: email and index independent; no ordering between them. But if user updates quickly, index handler should be idempotent and maybe use version/timestamp.
Need maybe mention Symfony `#[WithMessageRetries]`? There is `#[Retryable]`? Not sure. Better generic config.
Need final: start with principle: registration transaction commit => emit event => enqueue async tasks. Then options/trade-offs. End with recommendation.
Need ensure not too verbose? Desired 9. Good.

</details>

**Кандидат:**

Коротко: **регистрация завершается транзакцией, после её коммита публикуется доменное событие `UserRegistered`, а email и индекс — это отдельные асинхронные задачи**. Ответ пользователю не должен зависеть от скорости email-провайдера или search-индекса.

Ниже — как я бы делал в Symfony, какие варианты есть и где они ломаются.

---

## 1. Рекомендуемая схема: доменное событие + async Messenger

### Схема

```text
HTTP request
  ↓
RegisterUserUseCase
  ↓
persist User
flush / commit
  ↓
publish UserRegistered
  ↓
enqueue SendRegistrationEmail
enqueue UpdateSearchIndex
  ↓
response 201 Created

отдельные workers:
  messenger:consume email
  messenger:consume search
```

Пользователь получает ответ сразу после того, как пользователь создан и событие поставлено в очередь. Email и индекс обрабатываются потом.

---

## 2. Доменная часть

Не привязываемся сразу к email или поиску. Для домена важно только:

> Пользователь зарегистрирован.

```php
final class UserRegistered
{
    public function __construct(
        public readonly int $userId,
        public readonly string $email,
    ) {}
}
```

Событие создаётся после успешного сохранения.

```php
final class RegisterUserUseCase
{
    public function __construct(
        private readonly UserFactory $userFactory,
        private readonly UserRepository $userRepository,
        private readonly EventDispatcherInterface $events,
    ) {}

    public function __invoke(RegisterUserCommand $command): User
    {
        $user = $this->userFactory->create($command);

        $this->userRepository->save($user);

        // Событие публикуется после flush/commit.
        $this->events->dispatch(new UserRegistered($user->id, $user->email));

        return $user;
    }
}
```

Важно: событие должно появляться **после коммита**, а не до. Иначе мы можем отправить письмо или проиндексировать пользователя, который в итоге не сохранился из-за ошибки транзакции.

В Doctrine это нужно контролировать явно, например:

```php
$user = $this->userFactory->create($command);

$this->em->persist($user);

$this->em->wrapInTransaction(function () use ($user): void {
    $this->userRepository->save($user);
});

// Здесь уже commit завершён.
$this->events->dispatch(new UserRegistered($user->id, $user->email));
```

Или через middleware / repository-обёртку, если хотите не мучать каждую use case.

---

## 3. Переход от события к асинхронным задачам

Symfony Event Dispatcher — синхронный. Messenger — транспортная шина. Их удобно разделять:

```text
UserRegistered
  ↓ listener
SendRegistrationEmail
UpdateSearchIndex
```

```php
final class UserRegisteredListener
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {}

    public function onUserRegistered(UserRegistered $event): void
    {
        $this->bus->dispatch(
            new SendRegistrationEmail($event->userId)
        );

        $this->bus->dispatch(
            new UpdateSearchIndex('users', $event->userId)
        );
    }
}
```

Теперь у нас уже не доменные правила, а конкретные задачи:

```php
final class SendRegistrationEmail
{
    public function __construct(
        public readonly int $userId,
        public readonly string $messageId = '',
    ) {}
}

final class UpdateSearchIndex
{
    public function __construct(
        public readonly string $index,
        public readonly int $userId,
        public readonly int $version = 0,
    ) {}
}
```

---

## 4. Конфигурация Messenger

Например:

```yaml
framework:
    messenger:
        default_bus: event_bus
        buses:
            event_bus: ~
        transports:
            email:
                dsn: '%env(MESSENGER_EMAIL_DSN)%'
            search:
                dsn: '%env(MESSENGER_SEARCH_DSN)%'

        routing:
            'App\Message\SendRegistrationEmail': email
            'App\Message\UpdateSearchIndex': search
```

DSN может быть:

```env
MESSENGER_EMAIL_DSN=doctrine://default
MESSENGER_SEARCH_DSN=amqp://localhost/email
```

Или:

```env
MESSENGER_EMAIL_DSN=redis://localhost:6379/messages
MESSENGER_SEARCH_DSN=redis://localhost:6379/messages
```

Для production я бы разделял очереди:

```bash
bin/console messenger:consume email --limit=500
bin/console messenger:consume search --limit=500
```

Это даёт:

- независимый масштабирование;
- email-сбой не блокирует search;
- search-сбой не блокирует email;
- разные retry policies;
- разные dead-letter queues.

---

## 5. Handler email

```php
#[AsMessageHandler]
final class SendRegistrationEmailHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerInterface $mailer,
        private readonly EmailDeliveryRepository $deliveryRepository,
    ) {}

    public function __invoke(SendRegistrationEmail $message): void
    {
        $user = $this->userRepository->find($message->userId);

        if ($user === null) {
            return;
        }

        // Idempotency: не отправляем одно и то же письмо повторно.
        if ($this->deliveryRepository->exists($user->id, 'registration_welcome')) {
            return;
        }

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($user->email)
            ->subject('Welcome')
            ->text('Your account was created.');

        $this->mailer->send($email);

        $this->deliveryRepository->markSent(
            $user->id,
            'registration_welcome'
        );
    }
}
```

Ключевые моменты:

- handler должен быть **идемпотентным**;
- если email-провайдер упадёт — задача уйдёт в retry или DLQ;
- повторная доставка не должна дублировать письмо;
- письмо не должно зависеть от того, успел ли search индекс обновиться.

---

## 6. Handler search index

```php
#[AsMessageHandler]
final class UpdateSearchIndexHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SearchIndexInterface $searchIndex,
    ) {}

    public function __invoke(UpdateSearchIndex $message): void
    {
        $user = $this->userRepository->find($message->userId);

        if ($user === null) {
            $this->searchIndex->delete('users', (string) $message->userId);
            return;
        }

        $this->searchIndex->upsert(
            'users',
            (string) $user->id,
            [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'updated_at' => $user->updatedAt->format(DATE_ATOM),
            ]
        );
    }
}
```

Для search это важно:

- запись должна быть **upsert by id**, а не append;
- дубликаты сообщений не должны ломать индекс;
- если пользователь удалён — индекс должен удалить документ;
- желательно хранить `updated_at` или version, чтобы старые сообщения не перезаписывали свежие.

Например:

```php
final class UpdateSearchIndex
{
    public function __construct(
        public readonly string $index,
        public readonly int $userId,
        public readonly int $version,
    ) {}
}
```

И в handler:

```php
$current = $this->searchIndex->get('users', (string) $message->userId);

if ($current !== null && $current['version'] > $message->version) {
    return;
}
```

Это защищает от race condition, когда сообщения приходят не по порядку.

---

## 7. Retry и dead-letter queue

Email:

```yaml
messenger:
    failure_transport: email_failed

    routing:
        'App\Message\SendRegistrationEmail':
            transport: email
            retry_strategy:
                max_retries: 3
                delay: 1000
                multiplier: 2
```

Search:

```yaml
        'App\Message\UpdateSearchIndex':
            transport: search
            retry_strategy:
                max_retries: 5
                delay: 200
                multiplier: 1.5
```

Затем отдельный процесс или админка:

```bash
bin/console messenger:consume email_failed --limit=100
```

Либо мониторинг:

- размер очереди `email`;
- размер очереди `search`;
- количество failed messages;
- время обработки;
- duplicate deliveries.

---

## 8. Варианты реализации и их trade-offs

### Вариант 1: синхронная обработка в запросе

```text
register user
send email
update search index
return response
```

**Плюсы:**

- простота;
- нет очередей;
- меньше moving parts;
- если всё успешно — состояние видно сразу.

**Минусы:**

- пользователь ждёт email-провайдера;
- пользователь ждёт search-индекс;
- сбой email или search ломает регистрацию;
- регистрация становится зависимой от всех внешних систем.

Когда я бы это сделал: только для очень простых внутренних инструментов, где email/search не критичны и внешний сервис локальный/надёжный. Для production-регистрации — нет.

---

### Вариант 2: асинхронные handler'ы через Messenger

```text
register user
publish event
enqueue messages
return response

worker:
  send email
  update index
```

**Плюсы:**

- ответ быстрая;
- email и search изолированы;
- легко добавить retry;
- легко масштабировать;
- хорошо вписывается в Symfony.

**Минусы:**

- появляется задержка;
- нужно продумать idempotency;
- нужен мониторинг очередей;
- если сообщение потеряно между commit и enqueue — теряется действие.

Это основной вариант для большинства Symfony-приложений.

---

### Вариант 3: Transactional Outbox

Схема:

```text
one DB transaction:
  insert user
  insert outbox event
commit

relay worker:
  read outbox events
  publish to MQ
  mark processed

consumers:
  send email
  update search
```

Таблица:

```sql
CREATE TABLE outbox_events (
    id BIGINT PRIMARY KEY,
    type VARCHAR(100) NOT NULL,
    payload JSONB NOT NULL,
    created_at TIMESTAMP NOT NULL,
    processed_at TIMESTAMP NULL,
    version INT NOT NULL
);
```

Код:

```php
$this->em->wrapInTransaction(function () use ($user): void {
    $this->userRepository->save($user);

    $this->outbox->append(
        type: 'user.registered',
        payload: ['user_id' => $user->id, 'email' => $user->email],
    );
});
```

**Плюсы:**

- сильная связка: пользователь и событие сохраняются вместе;
- если app упадёт после commit, событие не потеряется;
- хорошо подходит для микросервисов;
- at-least-once доставка легко достижима.

**Минусы:**

- больше инфраструктуры;
- нужен relay worker;
- нужны idempotency key в consumers;
- выше сложность;
- выше задержка.

Когда использовать:

- регистрация важна;
- email нельзя терять;
- есть несколько downstream-сервисов;
- есть микросервисная архитектура;
- уже есть Kafka/RabbitMQ/Redis Streams.

---

### Вариант 4: фоновая задача по флагу

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    email_sent BOOLEAN DEFAULT FALSE,
    search_indexed BOOLEAN DEFAULT FALSE
);
```

Cron:

```bash
* * * * * bin/console app:process-pending-registration
```

**Плюсы:**

- очень просто;
- надёжно, если таблица есть;
- нет очередей.

**Минусы:**

- задержка до следующего cron'а;
- плохо масштабируется;
- нужно управлять race condition между воркерами;
- не подходит для больших очередей и real-time.

Когда использовать:

- малый трафик;
- нет MQ;
- можно терпеть задержку в несколько минут;
- хочется минимальной инфраструктуры.

---

### Вариант 5: CDC для search-индекса

Например Debezium + Kafka + consumer, который обновляет Elasticsearch/Meilisearch.

**Плюсы:**

- search-индекс обновляется из изменений БД;
- очень надёжно;
- хорошо для event-driven архитектуры;
- search отделён от app логики.

**Минусы:**

- дорогая инфраструктура;
- сложнее отладка;
- не решает email;
- избыточно для одного Symfony-проекта.

Когда использовать:

- уже есть data pipeline;
- search-индекс критичен;
- много сущностей обновляется;
- нужна аналитика из changes.

---

### Вариант 6: dispatch после response

Иногда предлагают слушать `kernel.response` и отправлять задачи уже после ответа.

```php
public function onKernelResponse(ResponseEvent $event): void
{
    // dispatch email / search tasks
}
```

**Плюсы:**

- формально ответ уже ушёл клиенту;
- выглядит как "background".

**Минусы:**

- это хрупко;
- `kernel.response` — не гарантированный background;
- request lifecycle может быть закрыт;
- сложнее тестировать;
- в long-running workers/async contexts поведение непонятное;
- легко спутать с бизнес-логикой.

Я бы не использовал это как основной механизм.

---

## 9. Что выбрать

Для обычного Symfony-приложения я бы делал так:

```text
UserRegistered event
  ↓
Messenger async:
  - SendRegistrationEmail
  - UpdateSearchIndex
```

Плюс:

- отдельные очереди `email` и `search`;
- retry с backoff;
- dead-letter queue;
- idempotency в handlers;
- мониторинг очередей;
- version/timestamp в search-обновлении.

Это даёт 80% надёжности и 20% сложности.

Если регистрация критическая и email/search нельзя терять — добавляю **Transactional Outbox**:

```text
insert user
insert outbox event
commit
relay publishes to MQ
consumers idempotently process
```

Это даёт почти 100% reliability, но уже требует больше инфраструктуры.

---

## 10. Главные ошибки, которых я бы избегал

### 1. Отправлять email до commit

```php
// плохо
$mailer->send(...);
$em->flush();
```

Если flush упадёт — письмо уйдёт, а пользователя не будет.

---

### 2. Один handler делает email + search

```php
final class UserRegisteredHandler
{
    public function __invoke(UserRegistered $event): void
    {
        $this->sendEmail();
        $this->updateSearchIndex();
    }
}
```

Если email упадёт, search не обновится. Если search упадёт, email не отправится. Это создаёт ложную связь между независимыми действиями.

Лучше:

```text
UserRegistered
  ↓
SendRegistrationEmail
UpdateSearchIndex
```

---

### 3. Не делать handlers идемпотентными

Messenger гарантирует **at-least-once**, а не exactly-once. То есть сообщение может прийти дважды.

Поэтому:

- email — dedupe по `userId + template`;
- search — upsert по id;
- outbox — unique event id.

---

### 4. Использовать search-индекс как источник правды

Search — дублирующая копия. Источник правды — БД.

Если search потерял событие, нужно иметь способ пересинхронизации:

```bash
bin/console app:reindex-users
```

---

### 5. Не иметь fallback

Если search-индекс долго не обновляется, у пользователя будет "не найдено". Это нормально, но нужно:

- мониторинг;
- алерт;
- manual reindex;
- версия документов;
- dead-letter.

---

## Итог

Для Symfony я бы сделал:

1. При успешной регистрации создать событие `UserRegistered`.
2. Событие публиковать **после коммита**.
3. Listener ставил бы два асинхронных сообщения:
   - `SendRegistrationEmail`
   - `UpdateSearchIndex`
4. Messenger направлял их в разные очереди.
5. Workers обрабатывали бы email и search независимо.
6. Все handlers были бы идемпотентными.
7. Если нужна высокая надёжность — добавить Transactional Outbox.

Такая схема не замедляет ответ пользователю, не связывает успех регистрации с работой email/search и позволяет восстанавливать состояние при сбоях.

> ⏱ 52.4s · prompt 26031 tok · output 5970 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про систему контроля доступа в Symfony: аутентификация, роли, voters, и когда что использовать.

Мне нужно:
1. Разобрать архитектуру Symfony Security: аутентификация vs авторизация, firewalls, auth formatters
2. Показать, как работают роли — встроенные, пользовательские
3. Разобрать Voter'ы: как они устроены, как регистрируются, как работают
4. Дать чёткие критерии: когда ролей достаточно, когда нужен voter
5. Привести реальные примеры с кодом
6. Антипаттерны

Структура:
- Разделение: аутентификация (кто ты) vs авторизация (что тебе можно)
- Роли: встроенные (ROLE_USER), пользовательские, атрибуты
- Voters: интерфейс, пример, регистрация
- Критерии выбора: роль vs voter
- Антипаттерны
- Практический пример

Буду писать на русском, как и всё диалог. Код — на PHP. Примеры — реальные, не учебные.

</details>

**Кандидат:**

## Контроль доступа в Symfony

### Разделение: аутентификация ≠ авторизация

| | Аутентификация | Авторизация |
|---|---|---|
| Вопрос | **Кто ты?** | **Что тебе можно?** |
| В Symfony | Firewall + Authenticator | Roles + Voters + Attributes |
| Когда | Один раз на сессию/токен | При каждом запросе к защищённому ресурсу |

Это два разных слоя. Firewall говорит: "этого человека нельзя даже сюда". Authorizer говорит: "твой человек здесь, но этот конкретный объект — не его".

---

### Аутентификация: как работает

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User: auto

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|assets|bundles)
            security: false

        api:
            pattern: ^/api
            stateless: true
            json_login:
                check_path: /api/login
                success_handler: app.jwt_auth_handler
                failure_handler: app.jwt_failure_handler

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                default_target_path: app_dashboard
                enable_csrf: true
            logout:
                path: app_logout
```

#### Что происходит при request

```
Request
  ↓
Firewall (какой паттерн? stateless? session?)
  ↓
Authenticator (form_login / jwt / api_key / ...)
  ↓
  → успех: TokenStorage->set($token)
  → failure: redirect / 401
  ↓
AuthorizationChecker (роли + voters)
  ↓
Controller
```

`TokenStorage` — это `RequestAttribute` или `Session`, где живёт `AuthenticationToken` с пользователем и ролями.

---

### Роли

Роли — это **статические метки**. Они отвечают на вопрос "какая у тебя позиция", а не "что тебе можно в этой конкретной ситуации".

```php
// App\Entity\User
class User implements UserInterface
{
    /** @var string[] */
    private array $roles;

    public function getRoles(): array
    {
        // ROLE_USER — всегда, это baseline
        return array_unique(array_merge(['ROLE_USER'], $this->roles));
    }
}
```

```php
// В контроллере:
#[IsGranted('ROLE_ADMIN')]
public function deleteUser(int $id): void { ... }

// В шаблонах:
{% if is_granted('ROLE_MODERATOR') %}
    <a href="/admin/moderate">Модерация</a>
{% endif %}

// В сервисах (реже, но бывает):
$this->authorizationChecker->isGranted('ROLE_ADMIN', $entity);
```

#### Встроенные роли

| Роль | Что даёт |
|------|---------|
| `ROLE_USER` | Просто "авторизован". Не даёт никаких прав. |
| `ROLE_ADMIN` | Ничего особенного. Просто строка. |

`ROLE_ADMIN` — **не** superuser. Это просто метка. Если вы хотите superuser — Symfony добавляет `IS_AUTHENTICATED_FULLY` и `ROLE_ADMIN` в `hasRole()`, но **на практике** вы сами решаете, что `ROLE_ADMIN` значит.

#### Пользовательские роли

```php
// В БД или в конфиге:
$roles = ['ROLE_USER', 'ROLE_EDITOR', 'ROLE_SITE_OWNER'];
```

```yaml
# config/packages/security.yaml
security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/site,  roles: ROLE_SITE_OWNER }
        - { path: ^/profile, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

`access_control` — это **URL-level** guard. Он не знает про объекты. "Ты можешь зайти на `/admin`", но "можешь ли ты удалить *этот* пост" — это уже voter.

---

### Voters

Voter — это **объектная авторизация**. Он знает про контекст: кто пользователь, какой объект, какой метод.

```php
interface Voter
{
    /**
     * Какие атрибуты этот voter будет проверять.
     * Если атрибут не в списке — abstain.
     */
    public function supports(string $attribute, ?object $subject): bool;

    /**
     * @return bool
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): bool;
}
```

#### Пример: OwnerVoter

```php
final class OwnerVoter extends Voter
{
    protected function supports(string $attribute, ?object $subject): bool
    {
        // Этот voter отвечает на вопросы:
        // "can the user edit this post?"
        // "can the user delete this post?"
        // только если subject — Post
        if (!in_array($attribute, ['POST_EDIT', 'POST_DELETE'], true)) {
            return false;
        }

        return $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Post $subject */
        /** @var User $user */
        $user = $token->getUser();

        if ($user === null) {
            return false;
        }

        // Superuser обходит всё
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Владелец может редактировать
        if ($attribute === 'POST_EDIT') {
            return $subject->getAuthor() === $user;
        }

        // Владелец может удалить
        if ($attribute === 'POST_DELETE') {
            return $subject->getAuthor() === $user;
        }

        return false;
    }
}
```

#### Использование

```php
// В контроллере:
#[IsGranted('POST_EDIT', 'post')]
public function edit(Request $request, Post $post): Response
{
    // Только если $user === $post->getAuthor() или ROLE_ADMIN
    // ...
}

// В шаблоне:
{% if is_granted('POST_DELETE', post) %}
    <button>Удалить</button>
{% endif %}

// В сервисе:
if ($this->authorizationChecker->isGranted('POST_EDIT', $post)) {
    $post->update($data);
}
```

#### Почему `supports()` важен

Voter регистрируется глобально. Symfony вызывает **всех** voters для каждого `isGranted()`. `supports()` — это фильтр: "мне нечего сказать по этому вопросу, abstain".

```php
// Если supports() вернёт false для всех voters → исключение:
// "No voter found for attribute 'POST_EDIT'"
```

Это защита от опечаток: если вы напишете `POST_EDT` — Symfony скажет, что voter не найден, а не молча вернёт `false`.

---

### Когда ролей достаточно

| Ситуация | Почему роли |
|----------|-------------|
| "Только админы могут зайти на `/admin`" | URL-level, объект не нужен |
| "Только модераторы могут видеть панель модерации" | Статическая роль, не зависит от данных |
| "Только редакторы могут создавать посты" | Роль + нет объекта (ещё не создан) |
| "Только site owner может менять настройки сайта" | Роль, объект один, контекст фиксирован |

```php
// Достаточно:
#[IsGranted('ROLE_ADMIN')]
public function settings(): Response { ... }

// Или:
{% if is_granted('ROLE_EDITOR') %}
    <form action="/posts/create">...</form>
{% endif %}
```

---

### Когда нужен Voter

| Ситуация | Почему voter |
|----------|-------------|
| "Пользователь может удалить **свой** пост, но не чужой" | Зависит от `$post->author === $user` |
| "Менеджер может видеть **своих** сотрудников" | Зависит от `$employee->manager === $user` |
| "Пользователь может редактировать пост, пока он не опубликован" | Зависит от `$post->status` |
| "Команда может видеть проекты, к которым она привязана" | Зависит от связи `team ↔ project` |
| "Пользователь может отменить **свой** заказ, пока он не отправлен" | Зависит от `$order->status` и `$order->user` |

Общее правило: **если решение зависит от данных объекта — нужен voter.**

---

### Как я проектирую voters

#### 1. Один voter — один доменный объект

```
OwnerVoter          → Post, Comment, Article
ProjectMemberVoter  → Project, Task
OrderOwnerVoter     → Order, Shipment
```

Не один `AccessVoter` на всё. Каждый voter — один смысл.

#### 2. Атрибуты — глаголы, не существительные

```php
// Хорошо:
'POST_EDIT'
'POST_DELETE'
'PROJECT_VIEW'
'ORDER_CANCEL'

// Плохо:
'can_edit_post'
'is_owner'
'permission_123'
```

Атрибут — это **действие над объектом**. Не "какой ты", а "что хочешь сделать".

#### 3. Voter знает правила, controller не знает

```php
// Плохо:
if ($post->getAuthor()->getId() === $user->getId() || $user->hasRole('ROLE_ADMIN')) {
    // ...
}

// Хорошо:
#[IsGranted('POST_EDIT', 'post')]
public function edit(Post $post): Response { ... }
```

Правила в одном месте (voter), не размазаны по контроллерам.

#### 4. Не дублировать логику

```php
// Антипаттерн:
class OwnerVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($attribute === 'POST_EDIT') {
            return $subject->getAuthor() === $token->getUser();
        }

        if ($attribute === 'COMMENT_EDIT') {
            return $subject->getAuthor() === $token->getUser();
        }

        if ($attribute === 'MESSAGE_EDIT') {
            return $subject->getAuthor() === $token->getUser();
        }
    }
}
```

Если три объекта имеют одну и ту же логику "владелец может редактировать" — это один voter с общим механизмом:

```php
final class AuthorshipVoter extends Voter
{
    protected function supports(string $attribute, ?object $subject): bool
    {
        return $subject instanceof HasAuthorInterface
            && str_ends_with($attribute, '_EDIT');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var HasAuthorInterface $subject */
        return $subject->getAuthor() === $token->getUser();
    }
}
```

`HasAuthorInterface`:

```php
interface HasAuthorInterface
{
    public function getAuthor(): User;
}
```

```php
class Post implements HasAuthorInterface { ... }
class Comment implements HasAuthorInterface { ... }
class Message implements HasAuthorInterface { ... }
```

Один voter, три объекта, одна логика.

---

### Антипаттерны

#### 1. Voter на всё

```php
class GlobalVoter extends Voter
{
    protected function supports(string $attribute, ?object $subject): bool
    {
        return true;  // ← я отвечаю на ВСЁ
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($attribute === 'USER_EDIT') { ... }
        if ($attribute === 'POST_DELETE') { ... }
        if ($attribute === 'ORDER_CANCEL') { ... }
        if ($attribute === 'PROJECT_VIEW') { ... }
        // ... 40 строк if/else
    }
}
```

Это не voter. Это if/else в костюме voter. Когда добавят 10-е правило — станет неразборчиво.

#### 2. Проверка в контроллере

```php
public function edit(Post $post, Request $request): Response
{
    if ($post->getAuthor()->getId() !== $this->getUser()->getId()) {
        throw new AccessDeniedException();
    }
    // ...
}
```

Правило размазано. Если есть три контроллера, которые редактируют пост — три места. Если правило изменится — три правки.

#### 3. Роль "ROLE_CAN_EDIT_POST"

```php
$user->addRole('ROLE_CAN_EDIT_POST');
```

Роли — это **позиции**, не **права**. "Может редактировать пост" — это право, зависящее от данных. Это voter.

Когда роль уместна: `ROLE_EDITOR` — "я редактор, могу создавать/редактировать/удалять посты". Конкретный пост — уже voter.

#### 4. Voter без `supports()`

```php
protected function supports(string $attribute, ?object $subject): bool
{
    return true;  // ← каждый voter отвечает на каждый запрос
}
```

Это убивает производительность: Symfony вызывает N voters × M attributes. Если N=20, M=5 — 100 вызовов на каждый `isGranted()`.

#### 5. Voter, который бросает исключения

```php
protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
{
    if (!method_exists($subject, 'getAuthor')) {
        throw new \LogicException('Subject must have getAuthor()');
    }
    // ...
}
```

Voter должен возвращать `true`/`false`. Если объект не поддерживает метод — `supports()` должен вернуть `false`.

---

### Производительность

`isGranted()` может быть дорогим, если voter'ы делают DB-запросы.

```php
// Антипаттерн: voter делает DB query на каждый вызов
protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
{
    $teamMembers = $this->teamRepository->findMembersByProject($subject->getId());
    return in_array($token->getUser(), $teamMembers, true);
}
```

Решения:
- **Cache** в токене: `$token->getUser()->hasRole('TEAM_' . $project->getId())`
- **Prefetching**: загрузить связи до того, как voter будет вызван
- **Denormalized flag**: `post->isAuthor($user)` — уже в объекте, без DB

---

### Итого

| Вопрос | Ответ |
|--------|-------|
| Кто ты? | Аутентификация (firewall + authenticator) |
| Можешь ли ты зайти на этот URL? | Роли + `access_control` |
| Можешь ли ты сделать действие над этим объектом? | Voter |
| Зависит ли решение от данных объекта? | Voter |
| Зависит ли только от позиции? | Роль |
| Один voter на объект или на правило? | На правило (домен), не на объект |

> ⏱ 29.9s · prompt 30130 tok · output 3814 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

<details><summary>Ход мысли модели</summary>

The user is asking about how to store Value Objects in Doctrine when the domain requires immutability and always-valid state. This is a classic DDD + Doctrine tension. Let me think through this carefully.

The core problem:
- Value Objects are immutable (no setters)
- Doctrine ORM wants to hydrate objects (set properties via reflection)
- Doctrine wants to track changes (dirty checking)
- VOs should be validated at construction time
- Doctrine's managed entities have lifecycle (detached → managed → removed)

Let me enumerate the approaches:

1. **Custom Type (Doctrine DBAL Type)** — most common
   - Implement `Type` interface
   - `convertToDatabaseValue` / `convertToPHPValue`
   - `getSQLDeclaration` / `requiresSQLCommentHint`
   - Pros: clean separation, reusable, works with DQL
   - Cons: loses structure in DB (single column), can't index sub-fields, migration complexity

2. **Embedded (Doctrine Embeddable)**
   - `#[Embeddable]` on VO class
   - Maps to multiple columns
   - Pros: structured in DB, can index sub-fields
   - Cons: still mutable in DB context, Doctrine hydrates it (breaks immutability), no constructor validation on hydrate

3. **Custom Type with JSON** — store as JSON string
   - Pros: flexible, easy to add fields
   - Cons: no indexing, can't query by sub-fields efficiently, bloat

4. **Multiple columns via Embeddable + custom constructor**
   - Hybrid approach
   - Still has hydration problem

5. **Store as composite key / multiple columns on entity directly**
   - No VO class in DB layer
   - VO constructed in application layer
   - Pros: full control
   - Cons: verbose, easy to forget validation

The hydration problem is the biggest one:
- Doctrine uses `EntityManager::merge()` or reflection-based hydration
- If VO has `readonly` properties and a validating constructor, Doctrine can't just "set" properties
- Solution: custom `postLoad` or custom hydrator, or use `__wakeup`, or custom Type

Let me structure this as a comprehensive answer with concrete code examples for each approach, their trade-offs, and the practical pitfalls I've actually hit.

I should cover:
1. The core tension (immutability vs ORM hydration)
2. Custom Type approach (most common, best for simple VOs)
3. Embeddable approach (structured, but hydration problem)
4. JSON Type (flexible, but limited querying)
5. Multiple columns (verbose but explicit)
6. The hydration/validation problem and how to solve it
7. readonly + Doctrine conflict
8. My practical recommendation matrix

Let me write this in Russian since the conversation is in Russian.

</details>

**Кандидат:**

## Value Object в Doctrine: напряжение и решения

### Суть противоречия

Домен требует:
- **Иммутабельность** — объект создан = объект зафиксирован. Нет `setStreet()`.
- **Всегда валидное состояние** — конструктор валидирует. Невозможно создать `Money(-100, 'USD')`.
- **Равенство по значению** — `Money(100, 'USD') === Money(100, 'USD')`.

Doctrine хочет:
- **Гидратацию через reflection** — `EntityManager` ставит свойства через `ReflectionProperty::setValue()`.
- **Dirty checking** — сравнивает "изначальное" и "текущее" состояние. Для этого ему нужны **мутабельные** свойства.
- **Lifecycle** — detached → managed → detached. Объект "оживает" из БД.

Конфликт: **Doctrine не может вызвать ваш валидирующий конструктор при гидратации.** Он ставит свойства напрямую. Если свойства `readonly` — `ReflectionException`. Если нет `readonly`, но есть инварианты — объект может быть невалидным на мгновение между `setValue()`.

---

### Способ 1: Custom Type (самый частый)

ВО хранится как **одна колонка** (string, JSON, int).

```php
// src/Domain/Money.php
readonly class Money
{
    public function __construct(
        private int    $amountCents,
        private string $currency,
    ) {
        if ($amountCents < 0) {
            throw new \ValueError("Amount cannot be negative");
        }

        if (!in_array($currency, ['USD', 'EUR', 'RUB'], true)) {
            throw new \ValueError("Unknown currency: {$currency}");
        }
    }

    public function amountCents(): int   { return $this->amountCents; }
    public function currency(): string  { return $this->currency; }

    public function toDatabaseString(): string
    {
        return "{$this->amountCents}:{$this->currency}";
    }

    public static function fromDatabaseString(string $raw): self
    {
        [$amount, $currency] = explode(':', $raw, 2);
        return new self((int) $amount, $currency);
    }

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }
}
```

```php
// src/Infra/Doctrine/MoneyType.php
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new \TypeError('Expected Money, got ' . gettype($value));
        }

        return $value->toDatabaseString();
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Money::fromDatabaseString($value);
    }

    // PHP 8.4: getBindingType
    public function getBindingType(): int
    {
        return \Doctrine\DBAL\ParameterType::STRING;
    }
}
```

```php
// src/Entity/Order.php
class Order
{
    #[ORM\Column(type: MoneyType::NAME, nullable: true)]
    private ?Money $total = null;

    public function getTotal(): ?Money
    {
        return $this->total;
    }

    // Нет setTotal() — сущность иммутабельна или меняет состояние через метод:
    public function withTotal(Money $total): self
    {
        $clone = clone $this;
        $clone->total = $total;
        return $clone;
    }
}
```

#### Плюсы

| | |
|---|---|
| Простота | Один `Type`-класс, одна колонка. Меньше кода. |
| Инкапсуляция | Формат хранения — внутреннее дело `Type`. Можно сменить `string` на `json` без миграции сущностей. |
| Работает с `readonly` | Doctrine не трогает свойства VO. Он только вызывает `convertToPHPValue()` и кладёт результат в свойство сущности. |
| DQL | `SELECT o FROM Order o WHERE o.total IS NOT NULL` — работает. |

#### Минусы

| | |
|---|---|
| Нет индексации по подполям | Не можешь `WHERE currency = 'USD' AND amount > 1000`. Только full-text или application-layer filter. |
| Читаемость БД | В таблице лежит `"15000:USD"`. DBA не понимает, что это. |
| Migrations | При смене формата (например, `15000:USD` → `{"amount":15000,"currency":"USD"}`) — нужна data migration. |
| Сложные VO | Если у VO 5+ полей — `toDatabaseString()` становится громоздким. JSON-строка в колонке `VARCHAR` — хрупко. |

#### Подводные камни

**1. `null` vs отсутствие значения.**

```php
#[ORM\Column(type: MoneyType::NAME, nullable: true)]
private ?Money $total = null;
```

Если `total` — обязательное поле, а `nullable: true` стоит — вы создали дыру: в БД может быть `NULL`, а в домене `Money` не может быть `null`. Лечится валидацией на уровне сущности, но Doctrine не знает про ваши инварианты.

**2. `convertToPHPValue` вызывается при каждой гидратации.**

Если у вас 1000 строк в таблице и каждая содержит `Money` — 1000 вызовов `Money::fromDatabaseString()`. Для простых VO — ок. Для тяжёлых (расчёт, парсинг) — может быть замедлением.

**3. Сравнение в DQL.**

```sql
-- НЕ РАБОТАЕТ:
WHERE o.total = :money
```

Нужно:

```php
$qb->andWhere($qb->expr()->eq('o.total', $qb->createNamedParameter($money, MoneyType::NAME)));
```

Или хранить `amount` и `currency` в отдельных колонках (см. Способ 2).

**4. `__clone` и Doctrine.**

```php
public function withTotal(Money $total): self
{
    $clone = clone $this;  // shallow copy
    $clone->total = $total;
    return $clone;
}
```

Если сущность уже **managed** — `clone` создаёт **detached** копию. `EntityManager` не знает о ней. Вы потеряете tracking. Это классическая ловушка.

---

### Способ 2: Embeddable (структурный маппинг)

ВО хранится как **несколько колонок**.

```php
// src/Domain/Money.php
class Money  // НЕ readonly — см. ниже
{
    private int    $amountCents;
    private string $currency;

    public function __construct(int $amountCents, string $currency)
    {
        if ($amountCents < 0) {
            throw new \ValueError("Amount cannot be negative");
        }
        // ...
    }

    public function amountCents(): int   { return $this->amountCents; }
    public function currency(): string  { return $this->currency; }

    // Гидратация — приватный конструктор без валидации
    private function hydrate(int $amountCents, string $currency): self
    {
        $this->amountCents = $amountCents;
        $this->currency    = $currency;
        return $this;
    }
}
```

```php
// src/Entity/Embeddable/MoneyEmbeddable.php
#[ORM\Embeddable]
class MoneyEmbeddable
{
    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $amountCents = 0;

    #[ORM\Column(type: Types::STRING, length: 3, nullable: false)]
    private string $currency = '';

    public function __construct(?int $amountCents = null, ?string $currency = null)
    {
        if ($amountCents !== null) $this->amountCents = $amountCents;
        if ($currency !== null)    $this->currency = $currency;
    }

    // Геттеры для Doctrine
    public function getAmountCents(): int   { return $this->amountCents; }
    public function getCurrency(): string   { return $this->currency; }

    // Преобразование в доменный VO
    public function toValueObject(): Money
    {
        return new Money($this->amountCents, $this->currency);
    }

    public static function fromValueObject(Money $money): self
    {
        return new self($money->amountCents(), $money->currency());
    }
}
```

```php
// src/Entity/Order.php
class Order
{
    #[ORM\Embedded(class: MoneyEmbeddable::class)]
    private MoneyEmbeddable $total;

    public function getTotal(): Money
    {
        return $this->total->toValueObject();
    }

    public function setTotal(Money $total): void
    {
        $this->total = MoneyEmbeddable::fromValueObject($total);
    }
}
```

#### Плюсы

| | |
|---|---|
| Структура в БД | `amount_cents INT`, `currency VARCHAR(3)` — DBA понимает. |
| Индексация | `CREATE INDEX idx_order_currency ON orders (currency)` — работает. |
| DQL | `WHERE o.total.currency = 'USD' AND o.total.amountCents > 1000` — работает. |
| Агрегации | `SELECT SUM(o.total.amountCents) FROM Order o` — работает. |

#### Минусы

| | |
|---|---|
| Двойное представление | `Money` (домен) + `MoneyEmbeddable` (инфраструктура). Нужно поддерживать синхронность. |
| Гидратация без валидации | `MoneyEmbeddable` создаётся через конструктор **без** валидации (Doctrine ставит свойства напрямую). Вы теряете инварианты на уровне БД. |
| `readonly` не работает | Embeddable **не может** иметь `readonly` свойства — Doctrine должен их ставить. |
| `__clone` | `clone $order` копирует embeddable по ссылке (если массив) или по значению (если объект). Нужно проверять. |

#### Подводные камни

**1. Гидратация bypass'ит конструктор.**

```php
// Doctrine делает:
$embeddable = new MoneyEmbeddable();  // конструктор без аргументов
$embeddable->amountCents = 15000;    // reflection
$embeddable->currency = 'USD';       // reflection
// Валидация из конструктора НЕ вызвана.
```

Если в БД лежит `"amount_cents": -1` — вы получите `MoneyEmbeddable` с отрицательной суммой. Валидация не сработала.

**Решение:** валидация на уровне сущности при `getTotal()`:

```php
public function getTotal(): Money
{
    $money = new Money($this->total->amountCents, $this->total->currency);
    // ← Money::construct() бросит ValueError, если amountCents < 0
    return $money;
}
```

Это **shift-left**: ошибка обнаруживается при чтении, не при записи. Нормально, но не идеально.

**2. `nullable: true` на embeddable-колонках.**

Если `total` — опциональный, нужно:

```php
#[ORM\Embedded(class: MoneyEmbeddable::class)]
private ?MoneyEmbeddable $total = null;
```

И все колонки embeddable'а — `nullable: true`. Иначе INSERT с `total = null` упадёт: `NOT NULL constraint failed: orders.total_amount_cents`.

**3. Embeddable не может ссылаться на другие embeddable'ы с `#[ORM\Id]` в некоторых версиях Doctrine.**

В Doctrine 2.11+ — работает. В 2.10 и ниже — баги. Проверьте версию.

---

### Способ 3: JSON-колонка

```php
#[ORM\Column(type: 'json')]
private array $address;
```

```php
class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zip,
    ) {}
}

// При сохранении:
$order->address = [
    'street' => $address->street,
    'city'   => $address->city,
    'zip'    => $address->zip,
];

// При чтении:
$address = new Address($order->address['street'], ...);
```

#### Плюсы

- Максимальная гибкость. Добавили поле в VO — не нужна миграция.
- Простота.

#### Минусы

- **Нет индексации.** `WHERE address->city = 'Москва'` — full scan (PostgreSQL: `WHERE address->>'city' = 'Москва'` — работает, но без индекса).
- **Нет DQL-поддержки.** `WHERE o.address.city = :city` — не работает. Нужно `expr()->eq($qb->expr()->literal('...'), ...)` или raw SQL.
- **Бloat.** 1000 строк × JSON-обёртка — больше, чем 3 колонки.
- **Нет типов.** В БД — `jsonb`, но в PHP — `array`. Типы теряются.

#### Когда использую

- VO с 5+ полями, которые редко используются в запросах.
- Гибкая схема (например, `metadata`, `config`).
- Не нужен поиск по подполям.

---

### Способ 4: Отдельные колонки на сущности (без embeddable)

```php
class Order
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmountCents;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $totalCurrency;

    public function getTotal(): Money
    {
        return new Money($this->totalAmountCents, $this->totalCurrency);
    }

    public function setTotal(Money $total): void
    {
        $this->totalAmountCents = $total->amountCents();
        $this->totalCurrency    = $total->currency();
    }
}
```

#### Плюсы

- Максимальная простота. Нет embeddable, нет type.
- Индексация.
- DQL.

#### Минусы

- Геттер/сеттер дублируют логику VO.
- Сущность знает структуру VO (знает, что там `amountCents` и `currency`).
- При смене VO (добавили `exchangeRate`) — нужно менять сущность + миграцию.

#### Когда использую

- VO с 1–2 полями.
- Когда не хочется вводить embeddable или custom type.
- Простые сущности.

---

### Сравнение

| Критерий | Custom Type | Embeddable | JSON | Separate Columns |
|----------|-------------|------------|------|------------------|
| Индексация подполей | ✗ | ✓ | ✗ | ✓ |
| DQL по подполям | ✗ | ✓ | ✗ | ✓ |
| Иммутабельность VO | ✓ (во вне) | △ (embeddable mutable) | ✓ (во вне) | △ (properties mutable) |
| Гидратация с валидацией | ✓ | ✗ (bypass) | ✓ | ✗ (bypass) |
| Гибкость (добавить поле) | Смена формата | Миграция | Нет миграции | Миграция |
| Сложность кода | Средняя | Высокая | Низкая | Низкая |
| Читаемость БД | Низкая | Высокая | Средняя | Высокая |
| Подходит для | 1–3 поля, простые VO | 2–5 поля, нужны запросы | 5+ поля, гибкая схема | 1–2 поля, простые |

---

### Моя рекомендация

| Ситуация | Подход |
|----------|--------|
| `Money`, `Phone`, `Email` — 1–2 поля, часто в запросах | **Separate Columns** или **Embeddable** |
| `Address` — 3–5 поля, редко в запросах | **Custom Type** (string) или **JSON** |
| `GeoLocation` — 2 поля, нужны spatial-запросы | **Separate Columns** + PostGIS |
| `Metadata`, `Config` — гибкая схема | **JSON** |
| `Slug`, `UUID` — одно поле | **Custom Type** (или просто `string`) |

**Общее правило:**

> Если VO нужен в DQL-запросах по подполям → Embeddable или Separate Columns.
> Если VO нужен только как непрозрачный блок → Custom Type или JSON.
> Если VO имеет 1–2 поля → Separate Columns (меньше абстракций).
> Если VO имеет 3+ поля и не нужен в запросах → Custom Type.

---

### Чего я не делаю

```php
// Антипаттерн: "умный" embeddable с валидацией в геттере
#[ORM\Embeddable]
class MoneyEmbeddable
{
    public function __construct()
    {
        // Пустой конструктор для Doctrine
    }

    public function validate(): void
    {
        if ($this->amountCents < 0) throw new \Exception('...');
    }

    public function toValueObject(): Money
    {
        $this->validate();  // ← валидация при чтении, не при создании
        return new Money($this->amountCents, $this->currency);
    }
}
```

Это "работает", но валидация смещена на неестественное место. Лучше:

- **Custom Type** — валидация в `Money::construct()`, которая вызывается при `convertToPHPValue()`.
- **Separate Columns** — валидация в `Money::construct()`, которая вызывается в `getTotal()`.
- **Embeddable** — валидация в `Money::construct()`, которая вызывается в `toValueObject()`. Embeddable сам — dumb.

Ключевой принцип: **валидация живёт в VO, не в маппинговом слое.** Маппинговый слой только конвертирует. Если данные невалидны — это ошибка данных, не ошибка маппинга.

> ⏱ 39.0s · prompt 33750 tok · output 5118 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

<details><summary>Ход мысли модели</summary>

We need answer in Russian (user Russian). Need think through Doctrine aggregate storage: root + child entities, mapping collections, transaction boundaries, who calls flush. Need choose example. Likely Order aggregate: Order root + OrderLine children (line items), maybe Address value object embedded. Need cover: OneToMany/ManyToOne, cascade, owning side, orphanRemoval, lifecycle, identity map, flush after aggregate root persisted, repository save, unit of work, transaction boundaries around aggregate, not inside service per child. Who flushes? Use case / application service / repository? Best: repository persists, application service wraps in transaction and flushes once. In Symfony/Doctrine, repository save should persist; use case flushes via transaction manager. Avoid entity manager flush scattered. Need discuss mapping: Order has Collection<OrderLine>, OrderLine references Order. Since aggregate root should control access: lines are not independently loaded by ID; load by OrderId, cascade persist. Use mappedBy or inversedBy? In ORM, owning side ManyToOne line.order_id, root inverse OneToMany. For DDD purists, inverse side maybe readonly and managed by root, but Doctrine requires bidirectional for convenience. Need mention lazy loading, initialize collection, replace/add/remove methods. OrphanRemoval true and cascade all. For immutable? Could use replace entire collection. Need talk about transaction boundary: save aggregate as one transaction; flush once; if any child fails, rollback whole. If external side effects after commit. Need discuss identity map: after flush, managed. Unit of work. Need mention not flush after every add; flush after use case. If multiple aggregates, separate transactions/sagas if consistency boundary differs. Need include code: domain Order, OrderLine; repository implementation with EntityManager; application service with wrapInTransaction. Also discuss cascade and orphanRemoval caveats: deleting root deletes lines; cascading merge vs persist; new vs existing; lazy collection initialization; DQL and DB constraints; database schema. Need talk about mapping collections: `#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist','remove'], orphanRemoval: true)]` and root addLine, removeLine. Need discuss `inversedBy` optional? Actually `inversedBy` on inverse side to tell Doctrine relation. Need code:

class Order {
 private Collection $lines;
 public function __construct(OrderId $id) { $this->lines = new ArrayCollection(); }
 public function addLine(ProductId $productId, int $quantity, Money $unitPrice): void { if existing update; $line = new OrderLine($this, $productId, $quantity, $unitPrice); $this->lines[] = $line; }
 public function removeLine(OrderLine $line): void { if contains $this->lines->removeElement($line); }
}
class OrderLine { #[ORM\ManyToOne(targetEntity: Order::class, inversedBy:'lines', onDelete:'CASCADE')] #[ORM\JoinColumn(nullable:false)] private Order $order; }

Need mention DB: line order_id NOT NULL. Could use unique index? Maybe product unique per order.

Who flushes:
- Domain: no EM, no flush.
- Repository: `persist($order)`, maybe not flush.
- Application service:
```
$order = $repo->findById($id);
$order->addLine(...);
$this->transactional->execute(function() use ($repo) { $repo->save($order); return null; });
```
or `em->wrapInTransaction`. Need explain `wrapInTransaction` flushes automatically? In Symfony DoctrineBundle: `wrapInTransaction` begins, calls closure, commits, flushes? Let's check: Doctrine Bundle `EntityManager::wrapInTransaction(callable)`? It calls `transactional` from doctrine/orm? The closure is executed inside transaction; by default flush happens at commit? In ORM, transaction commit does not auto flush? Actually UnitOfWork flush can be called manually or auto-commit? In Doctrine, `EntityManager::commit()` doesn't flush automatically; `flush()` does commit? Wait: EM::flush persists changes and commits if in transaction? EM::flush calls UoW->commit? Let's recall: `EntityManager::flush` computes changes and commits if transaction active? It calls `UnitOfWork::commit()` at end? In ORM 2, `EntityManager::flush()` calls `UoW->commit()`; UoW commit commits transaction after executing SQL? Hmm. Better: use explicit `$em->flush()` inside transaction. `wrapInTransaction` in doctrine/orm's Transactional? `transactional` executes callable and commits; I think it calls `$em->commit()`? In Symfony docs: `$em->wrapInTransaction(function() { ... $em->persist(); })`? They often manually flush. Let's not rely. Say: repository persist, application service calls `$em->flush()` inside transaction, or use transaction manager that flushes. To be safe: `wrapInTransaction` + explicit `flush` or use Symfony's `EntityManagerDecorator::transactional`. Actually `transactional` from doctrine/orm: begins transaction, executes closure, commits; it does not flush? Let me recall docs: `$em->wrapInTransaction(function () use ($em, $entity) { $em->persist($entity); });` They may not flush? Hmm ORM's transactional calls `$em->commit()`? In EntityManager 2: `commit()` just commits transaction; `flush()` commits UoW and transaction? Actually UoW commit executes SQL and then if transaction active commits? Let's inspect memory: `UnitOfWork::commit()` iterates, executes inserts/updates/deletes, then if transaction active `$em->commit()`? No, EntityManager::flush calls `$this->unitOfWork->commit()` and then `$this->commit()`? Hmm. To avoid specifics, say: ensure explicit flush once after repository save inside transaction. In Symfony you can use `Doctrine\ORM\Tools\Tools`? Better: 
```
$em = $this->em;
$em->wrapInTransaction(function() use ($repo) {
   $repo->save($order);
   $em->flush();
});
```
But if flush outside? Inside closure. Or use custom TransactionManager:
```
public function execute(Closure $callback): void
{
    $this->em->wrapInTransaction(function() use ($callback, $em) {
       $callback();
       $em->flush();
    });
}
```
Need mention transactional boundaries correspond to use case / aggregate root state change, not per entity. If multiple aggregates with eventual consistency, separate transactions + events/sagas. If same consistency boundary, same transaction.

Need discuss cascade and orphanRemoval with aggregate:
- `cascade: ['persist','remove']`
- `orphanRemoval: true`
- If add new line: `persist` cascades when root persists. If root already managed, changes to collection? For new line: `persist` root cascades to new line if root is new? If root is managed, adding a new OrderLine entity to collection and not persisting it? Doctrine cascade persist from managed root? If root is managed and you add new entity to cascade relation, does EM persist new child automatically? In Doctrine, cascade persist applies to persist/merge on root. If root already managed and you add new child, UoW may not cascade persist automatically just because relation changed? Actually if `cascade: persist`, when you call `$em->persist($root)` it cascades to children. If root already managed, and you assign new child to collection, you might need `$em->persist($child)` or `$em->persist($root)` again? Doctrine UoW tracks new entities via persist. Cascade persist only triggered by persist/merge. If you don't call persist on root, new child may not be persisted. So repository save should `persist($order)` even if managed. Good. For remove: orphan removal handles.

Need discuss identity map and lazy loading: initialize collection before modifying in request, not in loop. Avoid lazy loading in view? mention.

Need example code fully. Let's draft in Russian. Need address "маппинг коллекций, границы транзакций, кто и когда вызывает flush?" We'll structure:
1. Пример: Order aggregate.
2. Domain model.
3. Doctrine mapping.
4. Repository.
5. Application service transaction.
6. Правила: flush once, aggregate boundary, no flush in controllers/entities, cascade/orphan, load whole aggregate, consistency boundary.
7. Подводные камни: lazy, N+1, detach, multiple EM, flush before commit? etc.

Need be careful about DDD: child entities shouldn't have public setters; root exposes methods. But Doctrine needs mapping. For `OrderLine.order` can be readonly? Could use `readonly`? Doctrine sets via reflection; readonly property may block hydration. For owning side, property can't be readonly if Doctrine sets it. Could make constructor-only and `#[ORM\ManyToOne]` with `readonly`? In PHP 8.3? No. Use private property, set in constructor, Doctrine can override? If constructor sets and property not readonly, Doctrine can set. Need avoid setter. For root collection, use ArrayCollection; Doctrine can set via reflection if no setter? It can. But for OneToMany inverse, Doctrine doesn't hydrate collection by setting property? It sets. Need no readonly.

Need mention `#[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]`. With cascade remove, DB cascade optional but good.

Need discuss `inversedBy` and `mappedBy` exact:
- On Order: `#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist','remove'], orphanRemoval: true)]`
- On OrderLine: `#[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines', inversedBy? yes inversedBy='lines')]` and `#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]`

Need code for domain:
```
final class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::BIGINT)]
    private int $id;
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $orderNumber;
    #[ORM\OneToMany(...)]
    private Collection $lines;
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $paidAt = null;

    public function __construct(string $orderNumber) { ... }
    public function withLine(ProductId $productId, int $qty, Money $unitPrice): void ...
    public function total(): Money ...
    public function markPaid(): void ...
}
```
But for DDD root should not expose setLine. Could provide `addLine` and `replaceLines`. Need avoid collection direct.

Need note: if aggregate has existing lines and you want replace all, use `replaceLines(iterable $newLines)` where root persists new lines and removes old. But if child entities are managed, orphan removal. Simpler.

Potential issue: `OrderLine` constructor requires Order, but when hydrating from DB, Doctrine instantiates without constructor (unless __construct no args) or uses reflection? If constructor has required args, Doctrine may fail unless no required args or hydrate callback. We can make OrderLine constructor optional:
```
public function __construct(?Order $order = null, ?ProductId $productId = null, int $quantity = 1, ?Money $unitPrice = null)
```
But that weakens domain. Better use `#[ORM\Attribute]`? In Doctrine, if constructor has required args, hydration may fail unless no required. Usually entity constructor can have required if Doctrine can pass? In ORM 2, it only calls constructor with no args unless all params have defaults? It uses `__construct` if exists and no required? I think it can hydrate with constructor if it can call with no args? It may not support required args. So for entities, avoid required constructor args or use `__construct()` with nullable and validate. For domain VO Money, can be required? It's a separate object not entity; if embedded via custom type okay. If child entity constructor has required Money and ProductId, hydration may fail. Need mention: entities need a hydration-friendly constructor (optional args or factory method), but domain invariants enforced after creation / when using root methods. Could use `__construct(ProductId $productId, int $qty, Money $unitPrice)` and set `order` later? But Doctrine needs order. If constructor doesn't include order, property can be set. But if order required, problematic. Better: `public function __construct()` with setters? But DDD? We can show practical mapping and discuss tension.

Maybe use embeddable VO? For product id and unit price as columns? We can choose simpler: OrderLine stores `productId` string, `quantity`, `unitPriceCents`, `unitCurrency`. Avoid VO in child to keep clear. But user asks aggregate storage; can include VO. We can mention VOs separately but not central. Maybe use scalar columns for product id and amount to avoid hydration issues.

Need discuss "кто и когда вызывает flush?" Answer:
- Never domain entities.
- Repository may persist but should not flush; or can flush if repository owns UoW but better not.
- Application service / use case flushes after all invariants and aggregate changes completed.
- If command creates new aggregate: persist root, flush. If updates: load root, mutate, flush.
- Use transaction manager.
- If multiple aggregates: one flush can include multiple aggregates in same transaction if same consistency boundary; otherwise separate transactions and events.
- Flush before external side effects? Actually side effects after commit to avoid rollback leaving sent emails. But if external call needed for data, within? For payment, call gateway then mark paid; if gateway succeeded but DB fails, compensation. Keep side effects after commit when possible.

Need mention `EntityManager` identity map: if you create duplicate in memory with same id, Doctrine may get confused. Use repository to load aggregate root by id. Do not instantiate existing entity manually.

Need mention collection replacement vs add/remove. For add:
```
public function addLine(ProductId $productId, int $qty, Money $unitPrice): void {
   foreach ($this->lines as $line) { if ($line->productId() == $productId) { $line->setQuantity($qty + $qty); return; } }
   $line = new OrderLine();
   $line->setProductId($productId); ...
   $line->setOrder($this);
   $this->lines[] = $line;
}
```
But if order is new, `setOrder`? Need if `order` readonly? We'll use private property and setter for mapping? Could be bad. In domain, line created via root `addLine`, root can create line with `$this` using factory. But hydration? Let's design:
```
class OrderLine
{
   private ?Order $order = null;
   public function __construct() {}
   public function setOrder(Order $order): void { $this->order = $order; }
   public function setProductId(ProductId $productId): void ...
}
```
Not beautiful but practical. Could mention use `Hydrator`/constructor if using Doctrine 3? Need not too deep.

Alternative: Use unidirectional? Could use `#[ORM\OneToMany]` with `orphanRemoval`, and child owning side still needed. Yes.

Need discuss `lazy: true` maybe:
```
#[ORM\OneToMany(..., lazy: true)]
```
If need performance.

Need discuss DB constraints: `NOT NULL`, unique order product, check quantity >0. Domain invariants should also be enforced by DB constraints where possible.

Need mention `Cascade MERGE`? For detached entities? Usually avoid merge; use `detach` and new state. For aggregate update, load managed and mutate.

Need talk about `flush` inside loops? no. Flush once at boundary. If large aggregate collection (thousands lines), chunking? Could batch flush but still consistency boundary. For huge aggregates, consider table partitioning or separate child store? Mention edge.

Need include Symfony example:
```
class ConfirmOrderUseCase {
 public function __construct(OrderRepositoryInterface $repo, OrderRepository $repoImpl, TransactionManager $tx) {}
 public function __invoke(string $orderNumber, ConfirmOrderCommand $cmd): Order {
   $order = $repo->byNumber($orderNumber);
   $order->addLine(...);
   $order->markPaid();
   $this->tx->execute(function() use ($repo, $order) { $repo->save($order); });
   return $order;
 }
}
```
TransactionManager:
```
class TransactionManager {
 public function __construct(private EntityManagerInterface $em) {}
 public function execute(callable $callback): void {
   $this->em->wrapInTransaction(function() use ($callback) {
      $callback();
      $this->em->flush();
   });
 }
}
```
But `wrapInTransaction` might commit after closure, flush inside. Good.

Repository:
```
class OrderRepository implements OrderRepositoryInterface {
 public function __construct(private EntityManagerInterface $em) {}
 public function save(Order $order): void { $this->em->persist($order); }
 public function byNumber(string $n): Order { return $this->em->getRepository(Order::class)->findOneBy(['orderNumber'=>$n]) ?? throw ...; }
}
```
No flush.

Need mention if creating new aggregate:
```
$order = Order::open($number);
$order->addLine(...);
$repo->save($order);
$em->flush();
```
For existing:
```
$order = $repo->byNumber(...);
$order->addLine(...);
$repo->save($order);
$em->flush();
```
Same.

Need discuss "границы транзакций" more: Transaction should cover all DB writes that must be atomic. If updating Order and Inventory: if inventory must be atomically reserved with order, same transaction. If inventory is separate bounded context and can compensate, separate. If search index/email not in transaction. If aggregate has child entities in same DB, same transaction.

Need mention Doctrine `UnitOfWork` and `flush`: UoW tracks scheduled inserts/updates/deletes; flush translates to SQL. `persist` only schedules. `flush` can be called multiple times but usually once per use case.

Need mention `commit` vs `flush`: flush may commit changes to DB within transaction; transaction rollback undoes. In autocommit mode, each flush commit? In Doctrine, if no explicit transaction, flush commits. But better explicit.

Need mention "who calls flush" in controllers: No, controllers don't call flush. If they do, they're crossing layers. Use application service.

Need mention multiple EM? If using separate connection for reporting, not part of aggregate transaction.

Need potential "aggregates should be small" if collection huge. If OrderLine thousands, consider separate read model.

Need include anti-patterns:
- Cascade persist and flush in repository for every line? bad.
- `foreach ($order->lines as $line) { $em->persist($line); }` unnecessary and may cause N+1? Not necessarily, but bypass root.
- `new OrderLine()` and `$em->persist($line)` without root? bad.
- Detached entity mutation without reattach? If root detached, need `merge` or persist? Better load managed.
- `flush()` in template? bad.
- Using `CascadeMerge`? caution.

Need maybe mention `#[ORM\OneToMany(mappedBy: 'order', fetch: 'EAGER')]`? Usually lazy, initialize before modification. If you need all lines, `initialize($order->lines())`. Avoid eager by default.

Need discuss `replaceCollection` for immutable aggregate:
```
public function replaceLines(array $lines): void {
   $this->lines->clear();
   foreach ($lines as $line) { $line->setOrder($this); $this->lines[] = $line; }
}
```
But clearing collection with orphanRemoval true schedules deletes for old lines. Good.

Need mention Doctrine collections are not PHP arrays; use `ArrayCollection`, `->toArray()` only if needed.

Need mention `OrderLine` should not be loadable independently; repository only by Order. DQL should always fetch order root. If querying lines directly, you break aggregate consistency. For read-side, use query objects/read model.

Need include code for repository byNumber with `wrapInTransaction`? no.

Let's craft final with headings. Keep oververbosity 9, detailed. Ensure Russian. Need maybe answer as interview style? We are in conversation, can explain.

Potential nuance: In Symfony/Doctrine, if you use `EntityManagerDecorator::transactional`, it flushes? Let's verify. The `transactional` method from doctrine/orm: `EntityManager::transactional(callable)`? It uses `Transaction::wrap`? In ORM 2.14? There is `Doctrine\ORM\EntityManagerInterface::transactional(callable $func): void`. It begins transaction, executes, commits. Does it flush? I found memory: `EntityManager::transactional` calls `$this->beginTransaction(); $func($this); $this->commit();` It doesn't flush. So explicit flush needed. However Symfony docs: `EntityManagerDecorator::transactional` maybe flush? Not sure. To be safe, our TransactionManager explicitly flushes. Or use `wrapInTransaction` and `flush`. Good.

Need maybe mention if using DBAL transaction: `beginTransaction`, `commit`, `rollback`, but ORM UoW flush within.

Need discuss `flush` after external API call? If external call (payment) before DB commit, and DB fails, need compensate. If possible: validate and reserve, then commit, then notify. For payment capture, idempotency.

Need maybe mention `orphanRemoval` and `cascade remove` require bidirectional? It works with inverse side if owning side has FK. `orphanRemoval` on inverse side. Yes.

Need mention "new vs existing child": if root new, cascading persist saves children. If root existing, call persist root again or explicitly persist child; repository `persist($order)` covers cascade. But if child removed from collection, orphan removal schedules delete; flush commits.

Need mention `flush` order and FK constraints: Doctrine usually inserts parent before child, but if bidirectional and root already managed, child FK set. If you persist child first with null order, violates NOT NULL. So create through root.

Need mention `inversedBy` is for Doctrine to know relation; `mappedBy` only on inverse side. Good.

Let's formulate final.

Need include a complete code sample but not too long. Use `Product`? Use `OrderId` maybe int. Let's define:
```
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::BIGINT)]
    private int $id = 0;
    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $number;
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $lines;

    public function __construct(private string $number)
    {
       $this->lines = new ArrayCollection();
       $this->status = OrderStatus::Draft;
       $this->createdAt = new \DateTimeImmutable();
    }
...
}
```
If `private string $number` promoted and ORM column? Doctrine can map promoted? Yes if property? It maps to property, promoted is property. okay.

`OrderLine`:
```
class OrderLine
{
 #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::BIGINT)] private int $id=0;
 #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
 #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
 private ?Order $order = null;
 #[ORM\Column(type: Types::STRING, length: 100)] private string $productId;
 #[ORM\Column(type: Types::INTEGER)] private int $quantity = 1;
 #[ORM\Column(type: Types::INTEGER)] private int $unitPriceCents = 0;
 #[ORM\Column(type: Types::STRING, length: 3)] private string $currency = 'USD';

 public function __construct() {}
 public function setOrder(Order $order): void { ... }
 ...
}
```
Maybe mention if you want VOs: `Product` as embeddable? Keep simple.

`addLine` with duplicate handling:
```
public function addLine(string $productId, int $quantity, int $unitPriceCents, string $currency): void
{
   if ($quantity <= 0) throw new \DomainException();
   foreach ($this->lines as $line) {
      if ($line->productId() === $productId && $line->currency() === $currency) { $line->addQuantity($quantity); return; }
   }
   $line = new OrderLine();
   $line->setOrder($this);
   $line->setProductId($productId);
   $line->setQuantity($quantity);
   $line->setUnitPrice($unitPriceCents, $currency);
   $this->lines[] = $line;
}
```
Need ensure if order detached? Not relevant.

Repository save persist. Use case:
```
class ConfirmOrderUseCase {
  public function __construct(private OrderRepository $orders, private TransactionalOrderSaver $saver) {}
  public function __invoke(string $number, AddLineCommand $cmd) : Order {
     $order = $this->orders->byNumber($number);
     $order->addLine(...);
     $this->saver->save($order);
     return $order;
  }
}
```
TransactionalOrderSaver:
```
final class TransactionalOrderSaver {
  public function __construct(private EntityManagerInterface $em) {}
  public function save(Order $order): void {
     $this->em->wrapInTransaction(function() use ($order) {
        $this->em->persist($order);
        $this->em->flush();
     });
  }
}
```
This combines repository save and flush? Maybe better separate repository.

Could define:
```
final class OrderRepository implements OrderRepositoryInterface {
  public function save(Order $order): void { $this->em->persist($order); }
}
final class OrderUnitOfWork {
 public function save(Order $order): void {
   $this->em->wrapInTransaction(function() use ($order) {
       $this->repo->save($order);
       $this->em->flush();
   });
 }
}
```
But UnitOfWork depends repo. Simpler: use `EntityManagerInterface` in service and call `$em->persist($order); $em->flush();` but that leaks. We can show repository + transaction manager.

Need discuss if multiple writes:
```
$em->wrapInTransaction(function() use ($orderRepo, $inventoryRepo, $order) {
   $orderRepo->save($order);
   $inventoryRepo->reserve($order);
   $em->flush();
});
```
If inventory separate DB, use saga/outbox.

Need mention `flush` after all repository saves. If flush before second repo save, first committed? Not necessarily, transaction not committed until end; but if second fails, rollback both. Good. But better flush once at end.

Need discuss `EntityManager::detach`? Not central.

Potential issue with `orphanRemoval: true` and cascade remove: if you remove line from collection but line still referenced elsewhere? Doctrine deletes. If line is shared, bad. Ensure aggregate owns children.

Need discuss `#[ORM\JoinColumn(nullable: false)]` and `onDelete: 'CASCADE'`? If DB cascade and ORM cascade both? Fine but be cautious: DB cascade can hide ORM behavior. Maybe include `onDelete: 'CASCADE'` as DB-level safety.

Need mention if aggregate root has `OneToMany` and child has `ManyToOne`, the FK is on child. This is correct because child depends on root.

Need mention if root is deleted, children deleted due cascade. If root is archived and lines must be kept, do not use cascade/orphanRemoval; use soft delete.

Need mention "aggregate is boundary of consistency, not necessarily entity boundary". Good.

Let's final. Ensure not too excessive but detailed. Include final summary table maybe.

</details>

**Кандидат:**

Ниже я разберу на примере агрегата **`Order`** (Заказ), где корень — `Order`, а дочерние сущности — `OrderLine` (позиции заказа). Это классический случай: корень контролирует состояние, дочерние сущности не должны жить независимо от корня, а изменения должны быть атомарными.

---

# 1. Доменная модель: агрегат `Order`

## 1.1. `Order` — агрегатный корень

```php
namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Order\OrderStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $number;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status = OrderStatus::DRAFT;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderLine::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $lines;

    public function __construct(string $number)
    {
        $this->number = $number;
        $this->createdAt = new \DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function number(): string
    {
        return $this->number;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function lines(): Collection
    {
        return $this->lines;
    }

    public function addLine(string $productId, int $quantity, int $unitPriceCents, string $currency): void
    {
        if ($quantity <= 0) {
            throw new \DomainException('Quantity must be positive.');
        }

        if ($unitPriceCents < 0) {
            throw new \DomainException('Unit price cannot be negative.');
        }

        // Если позиция уже есть — увеличиваем количество.
        foreach ($this->lines as $line) {
            if (
                $line->productId() === $productId
                && $line->currency() === $currency
            ) {
                $line->addQuantity($quantity);
                return;
            }
        }

        $line = new OrderLine();
        $line->setOrder($this);
        $line->setProductId($productId);
        $line->setQuantity($quantity);
        $line->setUnitPrice($unitPriceCents, $currency);

        $this->lines[] = $line;
    }

    public function removeLine(OrderLine $line): void
    {
        if ($this->lines->contains($line)) {
            $this->lines->removeElement($line);
        }
    }

    public function totalAmountCents(): int
    {
        $total = 0;

        foreach ($this->lines as $line) {
            $total += $line->amountCents();
        }

        return $total;
    }

    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new \DomainException('Only draft order can be marked as paid.');
        }

        $this->status = OrderStatus::PAID;
    }

    public function status(): string
    {
        return $this->status;
    }
}
```

---

## 1.2. `OrderLine` — дочерняя сущность

```php
namespace App\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(
        name: 'order_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private ?Order $order = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $productId = '';

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    #[ORM\Column(type: 'integer')]
    private int $unitPriceCents = 0;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    public function __construct()
    {
        // Пустой конструктор нужен для гидратации Doctrine.
        // Доменные инварианты мы сохраняем через методы корня агрегата.
    }

    public function id(): int
    {
        return $this->id;
    }

    public function order(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \DomainException('Quantity must be positive.');
        }

        $this->quantity = $quantity;
    }

    public function addQuantity(int $quantity): void
    {
        $this->setQuantity($this->quantity + $quantity);
    }

    public function unitPriceCents(): int
    {
        return $this->unitPriceCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function setUnitPrice(int $unitPriceCents, string $currency): void
    {
        if ($unitPriceCents < 0) {
            throw new \DomainException('Unit price cannot be negative.');
        }

        $this->unitPriceCents = $unitPriceCents;
        $this->currency = strtoupper($currency);
    }

    public function amountCents(): int
    {
        return $this->quantity * $this->unitPriceCents;
    }
}
```

---

# 2. Как устроен маппинг

## 2.1. Владелец отношения

Важно: **владелец отношения** — это `OrderLine`, потому что на нём лежит foreign key:

```text
orders:
    id

order_lines:
    id
    order_id  <-- FK на orders.id
```

Поэтому `ManyToOne` находится на `OrderLine`, а `OneToMany` — на `Order`.

```php
// OrderLine — owning side
#[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
#[ORM\JoinColumn(name: 'order_id', nullable: false)]
private ?Order $order = null;

// Order — inverse side
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class)]
private Collection $lines;
```

Это нормально, даже если доменно мы считаем, что `Order` — корень.

---

## 2.2. `cascade`

```php
#[ORM\OneToMany(
    mappedBy: 'order',
    targetEntity: OrderLine::class,
    cascade: ['persist', 'remove'],
    orphanRemoval: true,
)]
private Collection $lines;
```

Что это даёт:

- `cascade: persist` — когда мы сохраняем `Order`, Doctrine сохраняет и новые дочерние сущности.
- `cascade: remove` — когда удаляем `Order`, дочерние сущности тоже удаляются.
- `orphanRemoval: true` — если позицию убрать из коллекции `Order::$lines`, она будет удалена из БД.

Это именно то, что нужно для агрегата:

> дочерние сущности живут только внутри агрегата и не должны управляться отдельно.

---

## 2.3. `orphanRemoval`

```php
public function removeLine(OrderLine $line): void
{
    if ($this->lines->contains($line)) {
        $this->lines->removeElement($line);
    }
}
```

Если после этого вызвать `flush()`, Doctrine удалит эту `OrderLine` из БД.

Это безопасно только если `OrderLine` принадлежит агрегату `Order` и не используется другими агрегатами.

Если бы `OrderLine` был общей сущностью, которую используют разные части системы, `orphanRemoval` был бы опасен.

---

## 2.4. `onDelete: 'CASCADE'`

```php
#[ORM\JoinColumn(
    name: 'order_id',
    nullable: false,
    onDelete: 'CASCADE'
)]
```

Это уровень базы данных.

Даже если ORM по какой-то причине не удалит дочерние записи, база их уберёт из-за foreign key cascade.

Важно: это backup-механизм, а не замена правильной ORM-логики.

---

# 3. Repository

Repository в идеале не должен вызывать `flush()`.

Он только:

1. находит агрегат;
2. сохраняет агрегат;
3. порождает ошибки, если агрегат не найден.

```php
namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Order\OrderRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

final class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function save(Order $order): void
    {
        $this->em->persist($order);
    }

    public function byId(int $id): Order
    {
        $order = $this->em->getRepository(Order::class)->find($id);

        if ($order === null) {
            throw new \RuntimeException("Order {$id} not found.");
        }

        return $order;
    }

    public function byNumber(string $number): Order
    {
        $order = $this->em
            ->getRepository(Order::class)
            ->findOneBy(['number' => $number]);

        if ($order === null) {
            throw new \RuntimeException("Order {$number} not found.");
        }

        return $order;
    }
}
```

Здесь:

```php
$this->em->persist($order);
```

не сохраняет сразу в БД.

`persist()` говорит Doctrine:

> “У меня есть объект, который нужно отслеживать. Если будут изменения — примени их при `flush()`.”

---

# 4. Кто вызывает `flush()`?

Правильный ответ:

> **Use case / application service** вызывает `flush()` один раз после того, как агрегат полностью изменён.

Не вызываем:

- в домене;
- в сущностях;
- в контроллере;
- в repository;
- в цикле по позициям.

---

## 4.1. Почему не в домене?

Домен не должен знать про Doctrine.

Плохо:

```php
class Order
{
    public function __construct(private EntityManagerInterface $em) {}

    public function addLine(...): void
    {
        // ...
        $this->em->persist($this);
        $this->em->flush();
    }
}
```

Почему плохо:

1. Домен зависит от ORM.
2. Непонятна граница транзакции.
3. Функция делает две вещи: бизнес-логику + инфраструктурный flush.
4. Сложно тестировать без Doctrine.
5. Если нужно добавить ещё одну сущность в транзакцию — домен не может об этом знать.

---

## 4.2. Почему не в repository?

Repository может быть удобным местом для `persist()`, но `flush()` лучше держать выше.

Почему:

- transactional boundary — это уровень use case;
- в одном use case можно сохранить несколько агрегатов;
- нужно решить, какие изменения откатывать вместе.

Пример:

```text
Order должен быть сохранён.
Inventory должно быть зарезервировано.
Payment должно быть подтверждено.
```

Если всё это одна транзакция — `flush()` должен быть один, после всех изменений.

---

# 5. Transaction manager

Сделаем отдельный класс, который знает про транзакцию.

```php
namespace App\Application;

use Doctrine\ORM\EntityManagerInterface;

final class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function execute(callable $callback): void
    {
        $this->em->wrapInTransaction(function () use ($callback): void {
            $callback();
            $this->em->flush();
        });
    }
}
```

Здесь:

- `wrapInTransaction` начинает транзакцию;
- `callback()` делает бизнес-логику и `persist()`;
- `flush()` применяет изменения к БД;
- если что-то упало — транзакция откатывается.

---

# 6. Use case: добавление позиции в заказ

```php
namespace App\Application;

use App\Domain\Order\OrderRepositoryInterface;

final class AddLineToOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly TransactionManager $transaction,
    ) {
    }

    public function __invoke(
        string $orderNumber,
        string $productId,
        int $quantity,
        int $unitPriceCents,
        string $currency,
    ): int {
        $order = $this->orders->byNumber($orderNumber);

        $this->transaction->execute(function () use ($order, $productId, $quantity, $unitPriceCents, $currency): void {
            $order->addLine($productId, $quantity, $unitPriceCents, $currency);

            $this->orders->save($order);
        });

        return $order->id();
    }
}
```

Тут:

1. загружаем агрегат;
2. внутри транзакции меняем его;
3. сохраняем через repository;
4. один раз `flush()`.

---

# 7. Что происходит при создании нового заказа

```php
final class CreateOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly TransactionManager $transaction,
    ) {
    }

    public function __invoke(
        string $number,
        array $items,
    ): int {
        $order = new Order($number);

        foreach ($items as $item) {
            $order->addLine(
                productId: $item['productId'],
                quantity: $item['quantity'],
                unitPriceCents: $item['unitPriceCents'],
                currency: $item['currency'],
            );
        }

        $this->transaction->execute(function () use ($order): void {
            $this->orders->save($order);
        });

        return $order->id();
    }
}
```

Здесь:

- `new Order(...)` — новый detached объект;
- `addLine()` создаёт новые `OrderLine`;
- `persist($order)` cascade-ом сохраняет и заказ, и позиции;
- `flush()` выполняет SQL.

---

# 8. Что происходит при изменении существующего заказа

```php
final class UpdateOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly TransactionManager $transaction,
    ) {
    }

    public function __invoke(
        string $orderNumber,
        string $productId,
        int $quantity,
        int $unitPriceCents,
        string $currency,
    ): void {
        $order = $this->orders->byNumber($orderNumber);

        $this->transaction->execute(function () use ($order, $productId, $quantity, $unitPriceCents, $currency): void {
            $order->addLine($productId, $quantity, $unitPriceCents, $currency);

            $this->orders->save($order);
        });
    }
}
```

Важно: `OrderLine` уже существует, но если мы добавляем новую позицию, она будет сохранена благодаря:

```php
cascade: ['persist', 'remove']
```

Если мы удаляем позицию:

```php
$order->removeLine($line);
```

она будет удалена благодаря:

```php
orphanRemoval: true
```

---

# 9. Граница транзакции

## 9.1. Основная мысль

Транзакция должна покрывать именно **границу консистентности агрегата**.

Если `Order` и `OrderLine` должны меняться атомарно — они в одной транзакции.

```text
BEGIN;

INSERT INTO orders ...;
INSERT INTO order_lines ...;
INSERT INTO order_lines ...;

COMMIT;
```

Или:

```text
BEGIN;

UPDATE orders SET status = 'paid' ...;
INSERT INTO order_lines ...;
DELETE FROM order_lines WHERE id = ...;

COMMIT;
```

---

## 9.2. Один flush на use case

Хорошо:

```php
$this->transaction->execute(function () use ($order): void {
    $order->addLine(...);
    $order->markAsPaid();
    $this->orders->save($order);
});
```

Плохо:

```php
$order->addLine(...);
$em->flush();

$order->addLine(...);
$em->flush();

$order->markAsPaid();
$em->flush();
```

Почему плохо:

1. Если третий шаг упадёт — первые два уже записаны.
2. Нарушена атомарность бизнес-операции.
3. Пользователь видит частичное состояние.
4. Транзакция становится неуправляемой.

---

# 10. Если нужно изменить несколько агрегатов

## 10.1. Один bounded context — одна транзакция

Если `Order` и `Inventory` должны быть согласованы строго:

```php
$this->transaction->execute(function () use ($order, $inventory): void {
    $order->markAsPaid();
    $inventory->reserveForOrder($order);

    $this->orders->save($order);
    $this->inventory->save($inventory);
});
```

Здесь:

- либо всё применилось;
- либо ничего.

---

## 10.2. Разные bounded contexts — не одна транзакция

Если `Order`, `Inventory`, `Payment`, `Search`, `Email` — разные контексты, нельзя держать всю систему в одной DB-транзакции.

Правильно:

```text
1. Transaction:
   - изменить Order
   - записать событие OrderPaid

2. После commit:
   - отправить в очередь
   - Inventory обрабатывает событие
   - Search обрабатывает событие
   - Email обрабатывает событие
```

Это уже Transactional Outbox / Event Driven Architecture.

---

# 11. Что нельзя делать с дочерними сущностями

## 11.1. Нельзя сохранять `OrderLine` отдельно

Плохо:

```php
$line = new OrderLine();
$line->setProductId('sku-1');
$line->setQuantity(1);

$em->persist($line);
$em->flush();
```

Почему плохо:

- нет связи с заказом;
- нарушена целостность агрегата;
- нарушен foreign key `order_id`;
- доменные правила заказа не применяются.

Хорошо:

```php
$order->addLine('sku-1', 1, 1000, 'USD');
$this->orders->save($order);
```

---

## 11.2. Нельзя грузить `OrderLine` отдельно

Плохо:

```php
$line = $em->getRepository(OrderLine::class)->find(5);
$line->setQuantity(10);
$em->persist($line);
$em->flush();
```

Почему плохо:

- нарушены инварианты `Order`;
- заказ мог быть в статусе, где изменение запрещено;
- не проверена логика всего агрегата.

Хорошо:

```php
$order = $this->orders->byId(1);
$order->updateLineQuantity($lineId, 10);
$this->orders->save($order);
```

---

# 12. Lazy loading и коллекция

По умолчанию `OneToMany` лучше делать lazy:

```php
#[ORM\OneToMany(
    mappedBy: 'order',
    targetEntity: OrderLine::class,
    cascade: ['persist', 'remove'],
    orphanRemoval: true,
    lazy: true,
)]
private Collection $lines;
```

Но перед изменением коллекцию нужно инициализировать.

Обычно это делает Doctrine, когда вы обращаетесь к коллекции:

```php
foreach ($order->lines() as $line) {
    // ...
}
```

Важно не делать так:

```php
$order = $repo->byId(1);

foreach ($order->lines() as $line) {
    $line->setQuantity(10);
    $em->persist($line);
    $em->flush();
}
```

Это плохое решение, потому что:

1. `flush()` в цикле;
2. нарушение границы транзакции;
3. прямой доступ к дочерним сущностям.

Лучше:

```php
$order = $repo->byId(1);

foreach ($order->lines() as $line) {
    $line->setQuantity(10);
}

$this->orders->save($order);
$em->flush();
```

Или ещё лучше — доменный метод:

```php
$order->increaseAllLineQuantities();
```

---

# 13. Identity map и managed entities

Doctrine держит в памяти **identity map**:

> один объект с определённым `id` соответствует одной записи в БД.

Поэтому важно:

- существующие агрегаты загружать через repository;
- не создавать существующие сущности вручную;
- не дублировать объект с тем же `id`.

Плохо:

```php
$order = new Order('ORD-123');
// ...
$em->persist($order);
```

Если `ORD-123` уже есть в БД — будет конфликт уникальности.

Хорошо:

```php
$order = $this->orders->byNumber('ORD-123');
```

---

# 14. Пример полного сценария

## 14.1. Command handler

```php
namespace App\Application;

use App\Domain\Order\OrderRepositoryInterface;

final class ConfirmOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly TransactionManager $transaction,
        private readonly EventPublisher $events,
    ) {
    }

    public function __invoke(string $orderNumber): void
    {
        $order = $this->orders->byNumber($orderNumber);

        $orderId = $this->transaction->executeWithResult(function () use ($order): int {
            $order->markAsPaid();
            $this->orders->save($order);

            return $order->id();
        });

        // После commit публикуем события.
        $this->events->publish(new OrderPaid($orderId));
    }
}
```

Здесь события отправляются после транзакции.

---

## 14.2. `TransactionManager` с возвратом результата

```php
final class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function execute(callable $callback): void
    {
        $this->em->wrapInTransaction(function () use ($callback): void {
            $callback();
            $this->em->flush();
        });
    }

    public function executeWithResult(callable $callback): mixed
    {
        $result = null;

        $this->em->wrapInTransaction(function () use ($callback, &$result): void {
            $result = $callback();
            $this->em->flush();
        });

        return $result;
    }
}
```

---

# 15. Как лучше проектировать маппинг: правила

## Правило 1. Корень агрегата — единственный вход

Все изменения должны идти через `Order`.

```php
$order->addLine(...);
$order->removeLine(...);
$order->markAsPaid();
```

Не через:

```php
$line->setQuantity(...);
$line->setOrder(...);
$em->persist($line);
```

---

## Правило 2. `OrderLine` не должен иметь независимого repository

Можно иметь:

```php
interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function byId(int $id): Order;
}
```

Но не:

```php
interface OrderLineRepositoryInterface
{
    public function find(int $id): OrderLine;
}
```

Если нужно отдельное чтение для UI — это read model / query, а не запись через отдельный repository.

---

## Правило 3. Flush один раз

```php
$this->transaction->execute(function () use ($order): void {
    $order->addLine(...);
    $this->orders->save($order);
});
```

Не:

```php
$order->addLine(...);
$em->flush();
```

внутри доменного метода.

---

## Правило 4. Transactional boundary = бизнес-операция

```text
Создать заказ — одна транзакция.
Добавить позицию — одна транзакция.
Подтвердить заказ — одна транзакция.
Оплатить заказ и зарезервировать товар — одна транзакция, если это один контекст.
```

---

## Правило 5. События — после commit

```php
$order->markAsPaid();
$orders->save($order);
$em->flush();

// только потом
$events->publish(new OrderPaid($order->id()));
```

Не наоборот.

Если событие отправить до commit и commit упадёт — мир получит событие об оплате несуществующего заказа.

---

# 16. Подводные камни

## 16.1. `orphanRemoval: true` опасен, если сущность общая

Если `OrderLine` используется только в `Order` — ок.

Если `OrderLine` может принадлежать ещё чему-то — `orphanRemoval` может случайно удалить данные.

---

## 16.2. `cascade: persist` не спасает при неправильной архитектуре

Можно написать:

```php
$line = new OrderLine();
$line->setProductId('sku-1');

$em->persist($line);
```

Но `cascade` не означает, что можно игнорировать агрегат.

---

## 16.3. `flush()` в цикле

Плохо:

```php
foreach ($items as $item) {
    $order->addLine(...);
    $em->flush();
}
```

Почему:

- много транзакций;
- если 10-я позиция упала — первые 9 уже записаны;
- нарушена атомарность.

---

## 16.4. Загрузка дочерних сущностей напрямую

Плохо:

```php
$lines = $em->getRepository(OrderLine::class)->findBy(['orderId' => 5]);
```

Для read-only может быть ок, если это отдельный query layer.

Для записи — нет.

---

## 16.5. `merge()` и detached entities

Doctrine умеет `merge()`, но он сложным поведением:

- создаёт managed-копию;
- может нарушить identity map;
- трудно предсказуем.

Лучше:

1. загрузить managed-объект;
2. изменить его;
3. сохранить;
4. flush.

---

# 17. Итоговая схема

```text
Client
  ↓
Controller / Command Handler
  ↓
Use Case
  ↓
OrderRepository
  ↓
Order aggregate
  ├── OrderLine
  └── OrderLine

Transaction
  ├── persist aggregate
  ├── flush once
  └── commit / rollback

After commit
  ├── publish events
  ├── send email
  └── update search index
```

---

# 18. Коротко

Для агрегата `Order + OrderLine` я бы делал так:

1. **`Order` — корень агрегата.**
2. **`OrderLine` — дочерняя сущность.**
3. Маппинг:
   ```php
   #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class)]
   ```
   и
   ```php
   #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
   ```
4. На `Order`:
   ```php
   cascade: ['persist', 'remove']
   orphanRemoval: true
   ```
5. Все изменения идут через `Order`.
6. Repository делает `persist($order)`.
7. Use case вызывает один `flush()` внутри транзакции.
8. События, email, search — после commit.
9. Нельзя сохранять или удалять `OrderLine` независимо от `Order`.
10. Нельзя вызывать `flush()` в домене, сущностях, контроллерах или циклах.

Это даёт:

- атомарность изменения агрегата;
- защиту инвариантов через корень;
- понятную границу транзакции;
- изоляцию домена от Doctrine;
- возможность безопасно масштабировать систему.

> ⏱ 109.9s · prompt 38329 tok · output 13252 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про диагностику и решение N+1 в Doctrine. Нужно дать практичный ответ: инструменты диагностики, конкретные способы решения (JOIN FETCH, DQL, Doctrine Collections, custom type, read model, lazy loading), trade-offs каждого. Не буду раздувать — сфокусируюсь на том, что реально работает в production. Структура: 1) диагностика (какими инструментами, что ищем), 2) способы решения с примерами кода, 3) trade-offs.

</details>

**Кандидат:**

## Диагностика N+1 в Doctrine

### Что я ищу

N+1 — это паттерн, где:
- 1 запрос загружает коллекцию (например, `SELECT * FROM orders WHERE ...`);
- для каждого элемента коллекции — отдельный запрос к связанной таблице.

Вместо 2 запросов получается `1 + N`.

```sql
-- Запрос 1: список заказов
SELECT o.* FROM orders o LIMIT 20;

-- Запрос 2..21: для каждого заказа — автор
SELECT u.* FROM users u WHERE u.id = 1;
SELECT u.* FROM users u WHERE u.id = 2;
SELECT u.* FROM users u WHERE u.id = 3;
-- ... ещё 18
```

---

### Инструменты диагностики

#### 1. Doctrine Logger (в dev-окружении)

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            middlewares:
                - Doctrine\ORM\Tools\Console\Command\...
```

Либо в `kernel.debug`-окружении:

```php
// src/Doctrine/QueryLoggerMiddleware.php
final class QueryLoggerMiddleware implements Doctrine\DBAL\Middleware
{
    public function __construct(
        private \Psr\Log\LoggerInterface $logger,
    ) {}

    public function getSchemaManager(
        AbstractPlatform $platform,
        ?SchemaManager $schemaManager = null,
    ): SchemaManager {
        throw new \BadMethodCallException('Not supported');
    }

    public function connect(
        Connection $connection,
        array $params,
    ): Connection {
        return new Connection(
            $params,
            $connection->getDriver(),
            new MiddlewareStack([
                new QueryLoggingMiddleware($this->logger),
                $connection->getWrapper(),
            ]),
        );
    }
}
```

```yaml
# config/services.yaml
services:
    App\Doctrine\QueryLoggerMiddleware:
        arguments: ['@monolog.logger.doctrine']
        tags:
            - { name: doctrine.middleware, priority: 10 }
```

**Что даёт:** каждый SQL-запрос в лог с временем. В dev-окружении вижу 21 запрос вместо ожидаемых 2.

**Минус:** только в dev. В production — overhead на логирование каждого запроса.

---

#### 2. `doctrine:query:explain` / `doctrine:query`

```bash
bin/console doctrine:query "SELECT COUNT(*) FROM orders"
```

Не покажет N+1 напрямую, но полезен для проверки конкретных запросов.

---

#### 3. Profiler (Symfony Web Profiler)

В dev-окружении Web Profiler показывает:

- количество запросов;
- время каждого запроса;
- SQL-текст;
- параметр запроса.

Если на одной странице 50+ запросов, каждый `WHERE id = ?` — N+1.

**Минус:** только в dev. Не работает в production без `APP_ENV=dev` (а это недопустимо).

---

#### 4. `dbal:query` + `PDO` profiling

```php
use Doctrine\DBAL\Logging\Logger;

class CountingLogger implements Logger
{
    public int $queryCount = 0;

    public function startQuery(string $sql, ?array $params = null, ?array $types = null): void
    {
        $this->queryCount++;
    }

    public function stopQuery(): void {}
}
```

```php
$logger = new CountingLogger();
$em->getConnection()->getConfiguration()->setSQLLogger($logger);

// ... ваш код ...

// После:
if ($logger->queryCount > 20) {
    $this->logger->warning('Possible N+1: ' . $logger->queryCount . ' queries');
}
```

**Минус:** грубо. Не покажет, какой именно запрос N+1.

---

#### 5. APM / Tracing (New Relic, Datadog, Jaeger)

В production — основной инструмент.

```
HTTP request
  └── SQL: SELECT * FROM orders (1ms)
  └── SQL: SELECT * FROM users WHERE id=1 (0.5ms)
  └── SQL: SELECT * FROM users WHERE id=2 (0.4ms)
  └── SQL: SELECT * FROM users WHERE id=3 (0.6ms)
  └── ... × 18
  └── SQL: SELECT * FROM payments WHERE order_id IN (...) (2ms)
```

Апаттерн: 1 запрос к "родительской" таблице + N однотипных запросов к "дочерней" — это N+1.

**Плюс:** работает в production, показывает время каждого запроса.
**Минус:** нужен APM.

---

#### 6. `EXPLAIN ANALYZE` + `pg_stat_statements`

PostgreSQL:

```sql
SELECT
    query,
    calls,
    total_exec_time,
    rows,
    mean_exec_time
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

Если вижу 20 однотипных запросов к `users` с `WHERE id = ?` и высоким `calls` — N+1.

```sql
CREATE EXTENSION pg_stat_statements;
```

**Плюс:** показывает, какой запрос вызывается чаще всего и суммарно занимает больше всего времени.
**Минус:** не показывает, из какого метода PHP он вызван.

---

### Мой порядок диагностики

| Этап | Инструмент | Что делаю |
|------|-----------|-----------|
| 1 | APM / `pg_stat_statements` | Нахожу однотипные запросы с высоким `calls` |
| 2 | Web Profiler (dev) | Повторяю запрос, вижу паттерн |
| 3 | Контрактный тест | Пишу тест: "загрузка списка заказов ≤ 5 запросов" |
| 4 | Фикс | Применяю решение (см. ниже) |
| 5 | Regression test | Убеждаюсь, что не вернулось |

---

## Способы решения

### 1. `JOIN FETCH` (самый частый)

**Проблема:**

```php
// 1 запрос на заказы + N запросов на авторов
$orders = $em->getRepository(Order::class)->findBy([], ['createdAt' => 'DESC'], 20);

foreach ($orders as $order) {
    echo $order->getAuthor()->getName();  // ← N+1
}
```

**Решение:**

```php
$qb = $em->createQueryBuilder();
$qb->select('o', 'a')
   ->from(Order::class, 'o')
   ->join('o.author', 'a')
   ->setMaxResults(20)
   ->orderBy('o.createdAt', 'DESC');

$orders = $qb->getQuery()->getResult();
```

Генерирует:

```sql
SELECT o.*, a.*
FROM orders o
INNER JOIN users a ON a.id = o.author_id
ORDER BY o.createdAt DESC
LIMIT 20;
```

**Плюсы:**
- 1 запрос вместо N+1.
- Минимальные изменения кода.
- Работает для `ManyToOne` и `OneToOne`.

**Минусы:**
- `setMaxResults(20)` + `JOIN FETCH` = **баг**. Doctrine вернёт 20 строк из `JOIN`, а не 20 отдельных `Order`. Если у одного автора 3 заказа — вернётся 3 строки, и `count()` будет 3, а не 1. Нужно дедупликация:

```php
$qb->select('o', 'a')
   ->distinct()  // ← без этого dup-строки
```

- Не работает для `OneToMany` (см. ниже).
- Не работает для глубоких графов: `order → author → profile → address`.

**Когда использую:** `ManyToOne`, `OneToOne`, 1 уровень глубины, `LIMIT` + `DISTINCT`.

---

### 2. `JOIN FETCH` для `OneToMany` (с осторожностью)

**Проблема:**

```php
// 1 запрос на заказы + N запросов на позиции
$orders = $em->getRepository(Order::class)->findBy([], [], 20);

foreach ($orders as $order) {
    foreach ($order->getLines() as $line) {  // ← N+1
        echo $line->getProductId();
    }
}
```

**Решение:**

```php
$qb = $em->createQueryBuilder();
$qb->select('o', 'l')
   ->from(Order::class, 'o')
   ->leftJoin('o.lines', 'l')
   ->setMaxResults(20)
   ->orderBy('o.createdAt', 'DESC');

$orders = $qb->getQuery()->getResult();
```

**Плюсы:**
- 1 запрос.
- Коллекция `o.lines` уже загружена.

**Минусы:**
- `setMaxResults(20)` **не работает**. Если 20 заказов × 5 позиций = 100 строк. `LIMIT 20` режет строки JOIN, не заказы.
- Нужно `GROUP BY` или дедупликация:

```php
$qb->select('o')
   ->from(Order::class, 'o')
   ->leftJoin('o.lines', 'l')
   ->groupBy('o.id')
   ->setMaxResults(20)
   ->orderBy('o.createdAt', 'DESC');
```

- Память: если у заказа 1000 позиций — весь массив в памяти.
- Не масштабируется на большие коллекции.

**Когда использую:** `OneToMany` с маленькими коллекциями (до 50 элементов), где нужен быстрый доступ.

---

### 3. DQL + `JOIN FETCH` + `PAGED` (через DQL)

```php
$dql = '
    SELECT o, a
    FROM App\Entity\Order o
    JOIN o.author a
    ORDER BY o.createdAt DESC
    LIMIT :limit';

$orders = $em->createQuery($dql)
    ->setParameter('limit', 20)
    ->getResult();
```

Тот же подход, что JOIN FETCH, но DQL позволяет:
- параметризовать лимит;
- использовать в более сложных запросах;
- комбинировать с `WHERE`.

**Тот же минус с `LIMIT` + `JOIN FETCH` + `DISTINCT`.**

---

### 4. `Doctrine\Common\Collections\ArrayCollection` — префаг (prefetch)

**Проблема:**

```php
// N+1: для каждого заказа загружаем платежи
$orders = $em->getRepository(Order::class)->findBy([], [], 20);

foreach ($orders as $order) {
    $payments = $em->getRepository(Payment::class)->findBy(['orderId' => $order->id]);
}
```

**Решение — префаг через `DQL` с `IN`:**

```php
// Шаг 1: загружаем заказы
$orders = $em->getRepository(Order::class)->findBy([], [], 20);

// Шаг 2: собираем все ID
$orderIds = array_map(fn(Order $o) => $o->getId(), $orders);

// Шаг 3: один запрос на все платежи
$payments = $em->createQueryBuilder()
    ->select('p')
    ->from(Payment::class, 'p')
    ->where('p.orderId IN (:ids)')
    ->setParameter('ids', $orderIds)
    ->getQuery()
    ->getResult();

// Шаг 4: группируем по orderId
$paymentsByOrder = [];
foreach ($payments as $payment) {
    $paymentsByOrder[$payment->getOrderId()][] = $payment;
}

// Шаг 5: назначаем
foreach ($orders as $order) {
    $order->setPayments($paymentsByOrder[$order->getId()] ?? []);
}
```

**Плюсы:**
- 2 запроса вместо N+1.
- Работает для `OneToMany` без `JOIN FETCH`.
- Контроль над `IN`-списком (можно разбить на батчи).
- Не нарушает `LIMIT`.

**Минусы:**
- Ручная группировка — boilerplate.
- Если `orderIds` > 1000 — PostgreSQL `IN` работает медленнее. Нужен батчинг:

```php
$batches = array_chunk($orderIds, 500);

foreach ($batches as $batch) {
    $payments = $em->createQueryBuilder()
        ->select('p')
        ->from(Payment::class, 'p')
        ->where('p.orderId IN (:ids)')
        ->setParameter('ids', $batch)
        ->getQuery()
        ->getResult();
    // ...
}
```

**Когда использую:** `OneToMany` с большим количеством дочерних сущностей, где `JOIN FETCH` не работает или неэффективен.

---

### 5. `JOIN FETCH` + `DISTINCT` + `GROUP BY` (комбинация)

```php
$qb = $em->createQueryBuilder();
$qb->select('o')
   ->from(Order::class, 'o')
   ->leftJoin('o.lines', 'l')
   ->leftJoin('l.product', 'p')
   ->groupBy('o.id', 'l.id', 'p.id')
   ->setMaxResults(20)
   ->orderBy('o.createdAt', 'DESC');
```

**Плюсы:**
- Загружает весь граф за 1 запрос.
- `GROUP BY` убирает дубликаты.

**Минусы:**
- `GROUP BY` может быть медленным на больших таблицах.
- Не всегда корректно: `GROUP BY o.id, l.id, p.id` — если у заказа 5 позиций и у каждой 1 продукт — 5 строк, а не 1.
- Трудно читать.
- `LIMIT` + `GROUP BY` — PostgreSQL оптимизирует по-разному.

---

### 6. `Doctrine\ORM\Tools\Pagination\Paginator` (для пагинации)

```php
use Doctrine\ORM\Tools\Pagination\Paginator;

$qb = $em->createQueryBuilder();
$qb->select('o')
   ->from(Order::class, 'o')
   ->leftJoin('o.lines', 'l')
   ->leftJoin('l.product', 'p')
   ->orderBy('o.createdAt', 'DESC');

$paginator = new Paginator($qb->getQuery(), true); // true = estimate via subquery
$paginator->setFirstResult(0);
$paginator->setMaxResults(20);

$orders = $paginator->getIterator()->getArrayCopy();
```

`Paginator` с `useOutputWalkers = true` делает **subquery**:

```sql
-- Сначала: сколько всего заказов?
SELECT COUNT(DISTINCT o.id)
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id;

-- Потом: конкретная страница
SELECT o.*, l.*, p.*
FROM orders o
LEFT JOIN order_lines l ON l.order_id = o.id
LEFT JOIN products p ON p.id = l.product_id
WHERE o.id IN (
    SELECT o.id FROM orders o ORDER BY o.createdAt DESC LIMIT 20 OFFSET 0
)
ORDER BY o.createdAt DESC;
```

**Плюсы:**
- Пагинация работает корректно с `JOIN FETCH`.
- `COUNT` через `DISTINCT` — не ломается от дублей.
- Лимит применяется к корневой сущности, не к строкам JOIN.

**Минусы:**
- 2 запроса: `COUNT` + данные.
- `useOutputWalkers` — медленнее на больших таблицах.
- Если `COUNT` дорогой (миллионы строк) — нужно кэшировать.

**Когда использую:** пагинация списков с `JOIN FETCH`.

---

### 7. Read Model / CQRS (для тяжёлых списков)

Если N+1 появляется на **списке** (агрегатный граф сложный, много JOIN'ов) — выношу чтение в отдельную таблицу.

```sql
-- orders_read (материализованный view или отдельная таблица)
CREATE TABLE orders_read (
    id BIGINT PRIMARY KEY,
    number VARCHAR(64),
    status VARCHAR(32),
    author_name VARCHAR(100),    -- ← денормализовано
    author_email VARCHAR(255),
    total_cents INT,
    currency CHAR(3),
    line_count INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

```php
// Запрос списка — 1 запрос, 0 JOIN'ов:
$orders = $em->createQueryBuilder()
    ->select('o')
    ->from(OrdersRead::class, 'o')
    ->orderBy('o.createdAt', 'DESC')
    ->setMaxResults(20)
    ->getQuery()
    ->getResult();
```

**Плюсы:**
- 1 запрос, 0 N+1.
- Очень быстрый.
- Можно кэшировать.
- Гибкая структура (можно добавить `author_name`, `total_cents` без JOIN'ов).

**Минусы:**
- Денормализация: нужно синхронизировать `orders_read` с `orders`.
- Задержка: если запись в `orders` — сразу, а в `orders_read` — через событие → есть окно несогласованности.
- Дублирование данных.
- Усложнение: 2 модели, 2 слоя.

**Когда использую:** списки, которые читают часто (1000+ RPS), а пишут редко. Или когда граф JOIN'ов слишком сложный для DQL.

---

### 8. `fetch='EXTRA_LAZY'` (Doctrine 2.x / 3.x)

```php
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, fetch: 'EXTRA_LAZY')]
private Collection $lines;
```

`EXTRA_LAZY` позволяет делать:

```php
// 1 запрос: SELECT COUNT(*) FROM order_lines WHERE order_id = ?
$count = $order->getLines()->count();

// 1 запрос: SELECT * FROM order_lines WHERE order_id = ? LIMIT 10
$first10 = $order->getLines()->slice(0, 10);
```

Вместо того, чтобы загрузить **все** `order_lines` в память.

**Плюсы:**
- Не грузит всю коллекцию.
- `count()`, `first()`, `slice()` — отдельные запросы.
- Экономит память.

**Минусы:**
- Каждый вызов `count()` / `first()` / `slice()` — отдельный запрос. Если вызываю `count()` 5 раз — 5 запросов.
- Не работает со всеми сборками (только `ArrayCollection` или `PersistentCollection`).
- В Doctrine 3.x — ограниченная поддержка.

**Когда использую:** коллекция большая, нужен только `count()` или первый элемент.

---

### 9. Кэширование (2nd level cache / Query cache / Result cache)

```php
// Query cache
$query = $em->createQuery($dql);
$query->setResultCache($em->getCache()->getAdapter());
$query->getResult();

// Result cache
$qb->getQuery()->getResult();
// → Doctrine кэширует результат запроса
```

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        result_cache_driver:
            type: redis
            host: '%env(REDIS_HOST)%'
```

**Плюсы:**
- Повторный запрос — из кэша, не из БД.
- N+1 не исчезает, но становится дешёвым (Redis вместо PostgreSQL).

**Минусы:**
- Инвалидация: если данные изменились — кэш устарел.
- Конфигурация: что кэшировать, как инвалидировать.
- Redis-сбой → N+1 возвращается.
- Не решает проблему при изменении данных.

**Когда использую:** данные медленно меняются (список категорий, справочники). Не использую для транзакционных данных (заказы, платежи).

---

### 10. `JOIN` на уровне SQL (raw query)

```php
$sql = '
    SELECT o.id, o.number, o.status, a.name AS author_name
    FROM orders o
    INNER JOIN users a ON a.id = o.author_id
    ORDER BY o.createdAt DESC
    LIMIT 20';

$orders = $em->createNativeQuery($sql)->getArrayResult();
```

**Плюсы:**
- Полный контроль.
- Можно добавить `DISTINCT`, `GROUP BY`, `LIMIT` — как нужно.
- Быстрее DQL для сложных запросов.

**Минусы:**
- Нет маппинга в сущности (array result).
- Нет автодополнения IDE.
- Нет валидации типов.
- При смене схемы — сломается.

**Когда использую:** read-only списки, где DQL не справляется.

---

## Сравнение

| Способ | Запросов | Память | Сложность | Когда |
|--------|---------|--------|-----------|-------|
| JOIN FETCH | 1 | Средняя | Низкая | `ManyToOne`, `OneToOne` |
| JOIN FETCH + `OneToMany` | 1 | Высокая | Средняя | Маленькие коллекции |
| Prefetch via `IN` | 2 | Средняя | Средняя | `OneToMany`, большие коллекции |
| Paginator | 2 | Средняя | Средняя | Пагинация |
| Read Model | 1 | Низкая | Высокая | Тяжёлые списки, CQRS |
| EXTRA_LAZY | N (по запросам) | Низкая | Низкая | `count()`, `first()` |
| Кэширование | 1 (из кэша) | Низкая | Средняя | Медленно меняющиеся данные |
| Raw SQL | 1 | Низкая | Высокая | DQL не справляется |

---

## Мой порядок применения

| Приоритет | Способ | Почему |
|-----------|--------|--------|
| 1 | `JOIN FETCH` | Минимум кода, 1 запрос. Если работает — использую. |
| 2 | Prefetch via `IN` | Если `JOIN FETCH` не работает или коллекция большая. |
| 3 | `Paginator` | Если нужна пагинация. |
| 4 | Read Model | Если список тяжёлый и часто читается. |
| 5 | Кэширование | Если данные медленно меняются. |
| 6 | Raw SQL | Если DQL не справляется. |

**Чего я не делаю:**
- `JOIN FETCH` для `OneToMany` с 100+ элементами (память).
- Кэширование транзакционных данных (заказы, платежи).
- `EXTRA_LAZY` как основной способ (каждый вызов — запрос).
- Raw SQL как первый вариант (только если DQL не справился).

> ⏱ 43.2s · prompt 45423 tok · output 5409 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

<details><summary>Ход мысли модели</summary>

Вот вопрос про ограничения целостности на уровне БД. Нужно объяснить:
1. Зачем они нужны, если есть валидация в коде
2. Какие конкретные средства PostgreSQL
3. В каких случаях какие использую
4. Trade-offs
5. Практические примеры

Структурирую ответ так:
- Философия: код может ошибаться, БД — последний рубеж
- Конкретные механизмы: constraints, triggers, CHECK, UNIQUE, FK, partial indexes
- Примеры: бизнес-правила, которые легко пропустить в коде
- Что ставлю на БД, а что оставляю в коде
- Антипаттерны

Нужно быть конкретным, с кодом. Не уходить в абстракции. Показывать реальные SQL-ограничения.

</details>

**Кандидат:**

## Ограничения целостности в PostgreSQL

### Почему код — недостаточный барьер

Код может ошибаться по причинам, которые вне вашего контроля:

| Причина | Пример |
|---------|--------|
| Баг | Забыли проверить условие в новом методе |
| Гонка | Два request одновременно меняют одно состояние |
| Обход | Raw SQL из админки, миграция, скрипт |
| Regression | Рефакторинг сломал проверку, тесты не покрыли |
| Человеческий фактор | New developer не знает, что нужно проверить |
| Альтернативный путь | CLI-команда, job, migration — не идут через use case |

База данных — **последний слой**, который нельзя обойти через «забыл проверить в коде». `INSERT` в PostgreSQL проходит через constraint-механизм независимо от того, кто его вызвал: PHP, psql, replication, cron.

---

### Принцип разделения

| Уровень | Что проверяет | Пример |
|---------|--------------|--------|
| **Код (домен)** | Бизнес-правила, контекст, сложная логика | "Заказ нельзя отменить, если уже отгружен" |
| **БД (constraints)** | Инварианты данных, которые должны держаться всегда | "amount_cents НЕ может быть отрицательным" |

Критерий: **если нарушение этого правила делает данные невосстановимо невалидными — ставлю на БД.**

---

### Конкретные механизмы и примеры

#### 1. `NOT NULL`

Самый простой. Защита от «забыли заполнить поле».

```sql
ALTER TABLE orders
    ADD CONSTRAINT orders_number_not_null
    CHECK (number IS NOT NULL);

ALTER TABLE users
    ADD CONSTRAINT users_email_not_null
    CHECK (email IS NOT NULL);
```

**Когда ставлю:** обязательные поля, которые без значения делают запись бессмысленной.

**Почему не только код:** migration может сделать `INSERT` без этого поля. Raw query из админки — то же самое. Без constraint в БД — `NULL` в production.

---

#### 2. `UNIQUE`

Защита от дубликатов.

```sql
ALTER TABLE users
    ADD CONSTRAINT users_email_unique
    UNIQUE (email);

ALTER TABLE orders
    ADD CONSTRAINT orders_number_unique
    UNIQUE (number);

ALTER TABLE payment_transactions
    ADD CONSTRAINT payment_idempotency_key_unique
    UNIQUE (idempotency_key);
```

**Когда ставлю:**
- Поле, которое должно быть уникальным бизнес-логически (email, number, uuid).
- Idempotency key — критично для платежей.

**Почему не только код:**

```php
// Race condition: два request одновременно создают пользователя с тем же email
$user1 = new User('john@example.com');
$user2 = new User('john@example.com');

// Без UNIQUE constraint — оба INSERT проходят.
// С UNIQUE — второй упадёт с UniqueViolationException.
```

`SELECT ... FOR UPDATE` + проверка — не спасает от всех гонок (например, если два app-сервера на разных машинах). `UNIQUE` — атомарная гарантия на уровне БД.

---

#### 3. `CHECK` constraint

Бизнес-правила, которые **не зависят от контекста**.

```sql
-- Сумма заказа не может быть отрицательной
ALTER TABLE orders
    ADD CONSTRAINT orders_amount_cents_check
    CHECK (amount_cents >= 0);

-- Валюта — 3 символа
ALTER TABLE orders
    ADD CONSTRAINT orders_currency_check
    CHECK (currency ~ '^[A-Z]{3}$');

-- Статус — из разрешённого набора
ALTER TABLE orders
    ADD CONSTRAINT orders_status_check
    CHECK (status IN ('draft', 'paid', 'shipped', 'delivered', 'cancelled'));

-- Количество — положительное
ALTER TABLE order_lines
    ADD CONSTRAINT order_lines_quantity_check
    CHECK (quantity > 0);

-- Цена — неотрицательная
ALTER TABLE order_lines
    ADD CONSTRAINT order_lines_unit_price_check
    CHECK (unit_price_cents >= 0);
```

**Когда ставлю:**
- Диапазон значений (количество > 0, цена >= 0).
- Формат (currency — 3 заглавные буквы).
- Набор допустимых значений (status enum).

**Почему не только код:**

```sql
-- Migration, которая обновляет status:
UPDATE orders SET status = 'PAID' WHERE id = 123;
-- Опечатка: 'PAID' вместо 'paid'
-- Без CHECK — данные в невалидном состоянии.
-- С CHECK — UPDATE отклонён.
```

```sql
-- Админка, которая позволяет ввести -100:
UPDATE order_lines SET quantity = -100 WHERE id = 55;
-- Без CHECK — "работает".
-- С CHECK — отклонено.
```

**Важно:** `CHECK` — это **дешёвая** проверка. PostgreSQL проверяет её на каждый `INSERT`/`UPDATE` для строки. Никаких триггеров, никаких вызовов функций. Overhead минимальный.

---

#### 4. `FOREIGN KEY` + `ON DELETE`

Связи между таблицами.

```sql
-- order_lines.order_id → orders.id
ALTER TABLE order_lines
    ADD CONSTRAINT order_lines_order_fk
    FOREIGN KEY (order_id)
    REFERENCES orders(id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

-- orders.customer_id → users.id
ALTER TABLE orders
    ADD CONSTRAINT orders_customer_fk
    FOREIGN KEY (customer_id)
    REFERENCES users(id)
    ON DELETE RESTRICT;  -- нельзя удалить пользователя, у которого есть заказы
```

**`ON DELETE` — критичный выбор:**

| Action | Когда | Пример |
|--------|-------|--------|
| `CASCADE` | Дочерняя запись живёт только с родителем | `order_lines` без `order` — бессмысленна |
| `RESTRICT` | Родитель не может быть удалён, пока есть дети | `user` с заказами — нельзя удалить |
| `SET NULL` | Дочерняя запись может жить без родителя | `payment.order_id = NULL` после отмены |
| `SET DEFAULT` | Редко. Значение по умолчанию | — |

**Почему не только код:**

```php
// Код:
$user->delete();

// Но:
// - migration сделала DELETE напрямую
// - raw SQL из админки
// - job удалил "старых" пользователей
// Без FK CASCADE/RESTRICT — order_lines с висящим order_id.
// С FK — DELETE отклонён или каскадно удалён.
```

---

#### 5. `GENERATED` columns + `CHECK`

Производные поля, которые не должны расхождаться с исходными.

```sql
-- total_cents = quantity * unit_price_cents
ALTER TABLE order_lines
    ADD COLUMN total_cents INT GENERATED ALWAYS AS (quantity * unit_price_cents) STORED;

ALTER TABLE order_lines
    ADD CONSTRAINT order_lines_total_check
    CHECK (total_cents = quantity * unit_price_cents);
```

**Когда ставлю:** поле, которое вычисляется из других полей и **не должно** меняться независимо.

**Почему не только код:** если кто-то обновит `quantity` через raw SQL, а `total_cents` — нет — данные рассинхронизированы. `GENERATED ALWAYS` делает это невозможным.

---

#### 6. Partial index

Индекс, который работает только для части строк.

```sql
-- Активные пользователи: быстрый поиск по email
CREATE INDEX idx_users_active_email
    ON users (email)
    WHERE status = 'active';

-- Неотменённые заказы: быстрый поиск
CREATE INDEX idx_orders_active
    ON orders (customer_id, created_at DESC)
    WHERE status != 'cancelled';
```

**Когда использую:**
- Поле + условие (например, `WHERE deleted_at IS NULL`).
- Сокращение размера индекса (не индексируем отменённые заказы).

**Почему на БД, а не в коде:**

```sql
-- Без partial index:
SELECT * FROM orders WHERE customer_id = 5 AND status != 'cancelled' ORDER BY created_at DESC;
-- Full scan по customer_id, потом фильтр.

-- С partial index:
-- Index scan, только активные заказы.
```

---

#### 7. `EXCLUDE` constraint

Запрет пересечения интервалов.

```sql
-- Расписание: два слота не должны пересекаться по времени для одного сотрудника
ALTER TABLE schedule_slots
    ADD CONSTRAINT no_overlap
    EXCLUDE USING gist (
        employee_id WITH =,
        time_range WITH &&
    );
```

**Когда ставлю:**
- Нон-пересечение интервалов (расписание, бронирование, абонементы).
- Календари, слоты, диапазоны дат.

**Почему не только код:**

```php
// Код:
if ($slot->overlapsWith($existingSlot)) {
    throw new \Exception('Overlap');
}
$em->persist($slot);
```

Race condition: два request одновременно проверяют "нет пересечения" → оба видят пусто → оба INSERT'ятся. `EXCLUDE` — атомарная проверка на уровне БД.

```sql
-- Aтомарно: если пересечение — INSERT отклонён.
INSERT INTO schedule_slots (employee_id, time_range)
VALUES (1, tstzrange('2024-01-15 09:00', '2024-01-15 10:00'));
```

---

#### 8. `TRIGGER`

Самый мощный и самый опасный инструмент.

```sql
-- Триггер: автоматическое обновление updated_at
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER orders_set_updated_at
    BEFORE UPDATE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION set_updated_at();
```

**Когда использую:**

| Сценарий | Почему триггер, а не код |
|----------|--------------------------|
| `updated_at` | Должно обновляться при ЛЮБОМ UPDATE, даже из raw SQL / migration |
| Аудит (who changed what) | Нельзя пропустить |
| Событийная логика (outbox) | Должна срабатывать атомарно с INSERT |
| Сложные правила, которые не выражаются в `CHECK` | Когда нужен доступ к другим таблицам |

**Когда НЕ использую:**
- Бизнес-логику, которая может быть в коде (дублирование).
- Сложные правила, которые нужно тестировать (триггеры сложнее тестировать).
- Когда правило меняется часто (код проще менять, чем триггер).

**Trade-off:**

| | `CHECK` / `UNIQUE` / `FK` | `TRIGGER` |
|---|---|---|
| Производительность | Высокая (оптимизировано) | Ниже (вызов функции) |
| Прозрачность | Видна в `pg_constraint` | Скрыта (в `pg_trigger` + функция) |
| Тестируемость | Проста | Сложнее (нужен `BEGIN...COMMIT` или `pg_test_fuzz`) |
| Отладка | `pg_constraint` + message | Нужно читать `plpgsql`-код |
| Безопасность | Только проверка | Может писать в другие таблицы |

---

### Что ставлю на БД, а что оставляю в коде

| Правило | Где | Почему |
|---------|-----|--------|
| `email` уникален | **БД** (`UNIQUE`) | Race condition, обход через raw SQL |
| `quantity > 0` | **БД** (`CHECK`) | Простая инварианта, нельзя нарушать никогда |
| `currency IN ('USD','EUR','RUB')` | **БД** (`CHECK`) | Набор значений фиксирован |
| `status IN ('draft','paid',...)` | **БД** (`CHECK`) | Не должно быть "unknown" status |
| `amount_cents >= 0` | **БД** (`CHECK`) | Отрицательная сумма — невосстановима |
| `order_lines.order_id → orders.id` | **БД** (`FK`) | Висящая ссылка — данные потеряны |
| "Заказ нельзя отменить после отгрузки" | **Код** | Зависит от контекста (какой заказ, кто отменяет) |
| "Дисконт не может превышать 100%" | **Код** + **БД** (`CHECK`) | БД: `discount_percent <= 100`. Код: бизнес-правило |
| "Платёж только один на заказ" | **Код** + **БД** (`UNIQUE`) | БД: `UNIQUE(order_id)` на `payments`. Код: доменное правило |
| `updated_at` обновляется | **БД** (`TRIGGER`) | Должно работать при любом UPDATE |
| "Нельзя пересечься по времени" | **БД** (`EXCLUDE`) | Race condition |
| Сложная бизнес-логика (калькуляция) | **Код** | Изменится, нужна гибкость |

---

### Антипаттерны

#### 1. Весь бизнес-код в триггерах

```sql
CREATE TRIGGER on_order_insert
    AFTER INSERT ON orders
    FOR EACH ROW
    EXECUTE FUNCTION handle_order_business_logic();

-- В функции:
-- - проверка баланса
-- - резервирование склада
-- - отправка email
-- - обновление аналитики
```

**Почему плохо:**
- Бизнес-логика спрятана в БД. Невидима в коде приложения.
- Нельзя покрыть unit-тестами.
- Нельзя рефакторить без downtime.
- Debugging: ошибка в триггере — нужно читать `plpgsql`.
- Разная логика в PHP и SQL — дублирование.

**Когда триггер оправдан:** только для **механических** операций, которые не зависят от бизнес-контекста: `updated_at`, аудит, outbox.

---

#### 2. `CHECK` со сложной логикой

```sql
ALTER TABLE orders
    ADD CONSTRAINT complex_rule
    CHECK (
        status = 'paid'
        OR (status = 'draft' AND created_at > NOW() - INTERVAL '30 days')
        OR (status = 'shipped' AND shipped_at IS NOT NULL AND shipped_at >= created_at)
    );
```

**Почему плохо:**
- Непонятно.
- Не тестируемо.
- Медленно (вычисляется на каждый INSERT/UPDATE).
- При изменении правила — нужно `ALTER TABLE DROP CONSTRAINT` + `ADD CONSTRAINT`.

**Когда ок:** простые правила (диапазон, набор, формат). Не "сценарии".

---

#### 3. Отсутствие `CHECK` на enum-статус

```php
// В коде:
if (!in_array($status, ['draft', 'paid', 'shipped'])) {
    throw new \Exception('Invalid status');
}
```

Без `CHECK` на БД — можно вставить `'PAID'` (опечатка в migration), `'unknown'` (баг), `NULL` (забыли заполнить).

---

### Производительность

| Constraint | Overhead |
|-----------|----------|
| `NOT NULL` | Нулевой (только проверка `IS NULL`) |
| `UNIQUE` | Индекс (уже есть для FK) |
| `CHECK` (простой) | Минимальный (выражение на строке) |
| `CHECK` (сложный, с `EXISTS`) | Высокий (подзапрос на каждый INSERT/UPDATE) |
| `FK` | Индекс на FK-колонке (если нет) |
| `TRIGGER` | Вызов функции + possible I/O |
| `EXCLUDE` | GiST-индекс (дороже B-tree) |

**Правило:** простые `CHECK`, `UNIQUE`, `FK` — ставлю всегда. Сложные триггеры — только если выигрывают по надёжности и не теряют по производительности.

---

### Итого

| Что | Где | Почему |
|-----|-----|--------|
| Простые инварианты (диапазон, формат, набор) | **БД** (`CHECK`) | Дешёво, атомарно, нельзя обойти |
| Уникальность | **БД** (`UNIQUE`) | Race condition, обход |
| Связи | **БД** (`FK`) | Висящие ссылки невосстановимы |
| `updated_at` | **БД** (`TRIGGER`) | Должно работать при любом UPDATE |
| Пересечение интервалов | **БД** (`EXCLUDE`) | Race condition |
| Бизнес-сценарии (отмена, расчёт, правила) | **Код** | Гибкость, тестируемость, контекст |
| Сложная логика с доступом к другим таблицам | **Код** (или `TRIGGER` только для outbox) | Гибкость, debuggability |

**Философия:** код — для гибкости и бизнес-контекста. БД — для инвариантов, которые должны держаться **всегда**, независимо от того, кто и как пишет данные.

> ⏱ 36.6s · prompt 50768 tok · output 4211 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе диагностики и оптимизации медленных запросов в PostgreSQL. Это продолжение собеседования на PHP-бэкенд. Нужно ответить системно: процесс диагностики (от наблюдения к причине), инструменты, типовые причины, решения. Структурирую как реальный workflow, а не как вики-статью. Использую конкретные примеры SQL, EXPLAIN, индексации. Не буду переусложнять — дам практичный процесс, который реально применяю.

</details>

**Кандидат:**

## Диагностика медленных запросов в PostgreSQL

### Мой процесс: от симптома к причине

```
1. Наблюдаю: запрос медленный
2. Измеряю: EXPLAIN ANALYZE
3. Читаю план: где узкое место?
4. Формулирую гипотезу
5. Проверяю: индекс / статистика / параллелизм / IO
6. Применяю фикс
7. Повторяю EXPLAIN ANALYZE
8. Убеждаюсь, что не сломал другие запросы
```

---

### Шаг 1: Измерение

#### `EXPLAIN ANALYZE` — основной инструмент

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT o.id, o.number, u.name
FROM orders o
JOIN users u ON u.id = o.customer_id
WHERE o.status = 'paid'
  AND o.created_at > '2024-01-01'
ORDER BY o.created_at DESC
LIMIT 20;
```

`ANALYZE` — реально выполняет запрос и показывает фактическое время.
`BUFFERS` — показывает, сколько страниц прочитано из shared_buffers vs диска.
`TIMING` — время каждого узла (включено по умолчанию).

#### Пример вывода и что я в нём ищу

```
 Limit  (cost=1234.56..1234.78 rows=20 width=120) (actual time=0.5..12.3 rows=20 loops=1)
   ->  Sort  (cost=1234.56..1234.57 rows=10 width=120) (actual time=12.1..12.2 rows=20 loops=1)
         Sort Key: o.created_at DESC
         Sort Method: top-N heapsort  Memory: 32kB
         ->  Hash Join  (cost=1.05..1234.40 rows=10 width=120) (actual time=0.4..11.8 rows=10000 loops=1)
               Hash Cond: (o.customer_id = u.id)
               ->  Seq Scan on orders o  (cost=0.00..1200.00 rows=100 width=120) (actual time=0.2..9.5 rows=10000 loops=1)
                     Filter: ((status = 'paid') AND (created_at > '2024-01-01'::timestamp))
                     Rows Removed by Filter: 990000
               ->  Hash  (cost=0.50..0.50 rows=50 width=40) (actual time=0.3..0.3 rows=50 loops=1)
                     ->  Seq Scan on users u  (cost=0.00..0.50 rows=50 width=40) (actual time=0.1..0.2 rows=50 loops=1)
 Planning Time: 0.3 ms
 Execution Time: 12.5 ms
```

**Что я вижу:**

| Строка | Проблема |
|--------|----------|
| `Seq Scan on orders` | Полное сканирование. 1M строк, 10k прошли фильтр. Нужен индекс. |
| `Rows Removed by Filter: 990000` | 99% строк отфильтровано. Индекс должен быть на `(status, created_at)`. |
| `Hash Join` | Ожидалось 10 строк, реально 10000. Статистика устарела. |
| `top-N heapsort` | Сортировка 10000 строк для `LIMIT 20`. Если бы был индекс по `created_at DESC` — не нужна. |

---

### Шаг 2: Типовые причины (от частых к редким)

#### 1. Отсутствие индекса / неправильный индекс

**Симптом:** `Seq Scan` на большой таблице.

```sql
-- Запрос:
SELECT * FROM orders WHERE customer_id = 5 AND status = 'paid';

-- EXPLAIN: Seq Scan on orders, 1M строк, 50 прошли
```

**Решение:**

```sql
CREATE INDEX idx_orders_customer_status ON orders (customer_id, status);
```

Порядок колонок: селективная первая. Если `customer_id` выбирает 50 из 1M, а `status` — 30% — сначала `customer_id`.

**Правило:**

```
Где = (equality)  →  в индекс
Где > / < (range) →  в индекс, после equality
ORDER BY         →  в индекс, после range
```

```sql
-- Запрос:
SELECT * FROM orders
WHERE customer_id = 5
  AND created_at > '2024-01-01'
ORDER BY created_at DESC;

-- Индекс:
CREATE INDEX idx_orders_customer_date ON orders (customer_id, created_at DESC);
```

**Почему `DESC`:** PostgreSQL 11+ поддерживает descending indexes. Без него сортировка всё равно выполняется.

---

#### 2. Устаревшая статистика

**Симптом:** `actual rows` сильно отличается от `rows` (estimate).

```
Seq Scan on orders  (cost=0.00..1200.00 rows=100 width=120)
                     (actual time=0.2..9.5 rows=10000 loops=1)
```

Ожидалось 100, реально 10000. Планировщик принял неверное решение.

**Решение:**

```sql
ANALYZE orders;
ANALYZE users;
-- Или для всех таблиц:
ANALYZE;
```

**Автоанализ:**

```sql
-- По умолчанию включён:
autovacuum = on
autovacuum_analyze = on

-- Порог: анализируем, если изменилось > 10% строк
autovacuum_analyze_threshold = 50
autovacuum_analyze_scale_factor = 0.1
```

**Когда ручная `ANALYZE` нужна:**
- После массового обновления (migration, import).
- Если `EXPLAIN` показывает большой разрыв estimate vs actual.
- После изменения распределения данных (например, 99% строк — `status = 'paid'`, и это изменилось).

---

#### 3. `Seq Scan` — не всегда плохо

**Симптом:** `Seq Scan`, но запрос быстрый.

```sql
-- Таблица на 100 строк:
Seq Scan on small_table  (cost=0.00..1.50 rows=100 width=40)
```

Это **нормально**. Для маленьких таблиц `Seq Scan` дешевле `Index Scan`, потому что:
- Нет overhead на обращение к индексу.
- Последовательное чтение быстрее случайного.

**Когда `Seq Scan` — проблема:**
- Таблица > 100k строк.
- Запрос < 1% строк.
- Время > 10ms.

---

#### 4. `N+1` на уровне SQL

**Симптом:** 50 однотипных запросов вместо 1.

```
SELECT * FROM orders WHERE id = 1;
SELECT * FROM orders WHERE id = 2;
SELECT * FROM orders WHERE id = 3;
-- ... × 50
```

**Решение:** `IN` + батчинг, `JOIN`, или CQRS (см. предыдущий вопрос про N+1).

---

#### 5. Сортировка без индекса

**Симптом:** `Sort Method: external merge` (сортировка на диске).

```
Sort  (cost=1234.56..1234.57 rows=10 width=120)
      Sort Key: o.created_at DESC
      Sort Method: external merge  Disk: 1024kB
```

`external merge` = не хватило `work_mem`, сортировка пошла на диск.

**Решение:**

```sql
-- Вариант 1: индекс по сортируемому полю
CREATE INDEX idx_orders_created ON orders (created_at DESC);

-- Вариант 2: увеличить work_mem (если сортировка неизбежна)
SET work_mem = '64MB';  -- на сессию
```

**`work_mem` — опасная настройка.** Это память **на операцию**, не на сессию. Если 10 сессий × 10 операций × 64MB = 6.4GB. Может выдать OOM.

---

#### 6. `Hash Join` vs `Nested Loop`

**Симптом:** `Hash Join` на маленьких таблицах, где `Nested Loop` был бы быстрее.

```
Hash Join  (cost=1.05..1234.40 rows=10 width=120)
```

Если правая таблица маленькая (100 строк) — `Nested Loop` дешевле:

```
Nested Loop  (cost=0.00..5.00 rows=10 width=120)
```

**Решение:**

```sql
-- Подсказать планировщику:
SET enable_hashjoin = off;
-- ИЛИ:
SET join_collapse_limit = 8;
```

Но лучше: **починить статистику**, чтобы планировщик сам выбрал правильный join.

---

#### 7. `CTE` как оптимизационный барьер

**Симптом:** `CTE` в PostgreSQL < 12 — материализуется, блокирует оптимизацию.

```sql
-- PostgreSQL 11 и ниже: CTE материализуется
WITH cte AS (
    SELECT * FROM orders WHERE status = 'paid'
)
SELECT * FROM cte c JOIN users u ON u.id = c.customer_id;
-- Даже если можно было бы inline-ить — нет, CTE → temp table
```

**PostgreSQL 12+:**

```sql
-- CTE inline-ится, если не используется 2+ раза и без RECURSIVE
WITH cte AS (
    SELECT * FROM orders WHERE status = 'paid'
)
SELECT * FROM cte c JOIN users u ON u.id = c.customer_id;
-- Планировщик может inline
```

**Правило:** в PostgreSQL 12+ CTE — не всегда плохо. Но `MATERIALIZED CTE` — всегда материализуется.

---

#### 8. `OFFSET` на больших страницах

**Симптом:** `LIMIT 20 OFFSET 100000` — медленный.

```sql
SELECT * FROM orders ORDER BY created_at DESC LIMIT 20 OFFSET 100000;
```

PostgreSQL сканирует 100020 строк, выбрасывает первые 100000.

**Решение — keyset pagination:**

```sql
-- Вместо:
SELECT * FROM orders ORDER BY created_at DESC LIMIT 20 OFFSET 100000;

-- Делаем:
SELECT * FROM orders
WHERE created_at < '2024-01-15 10:30:00'  -- последнее значение предыдущей страницы
ORDER BY created_at DESC
LIMIT 20;
```

Нужен уникальный `(created_at, id)` для стабильной сортировки:

```sql
SELECT * FROM orders
WHERE (created_at, id) < ('2024-01-15 10:30:00', 12345)
ORDER BY created_at DESC, id DESC
LIMIT 20;
```

Индекс:

```sql
CREATE INDEX idx_orders_created_id ON orders (created_at DESC, id DESC);
```

---

#### 9. `SELECT *` + широкие таблицы

**Симптом:** `width=500` в `EXPLAIN` (ширина строки в байтах).

```sql
SELECT * FROM orders;  -- 30 колонок, включая TEXT, JSONB
```

**Решение:**

```sql
SELECT id, number, status, created_at FROM orders;
```

Для `JSONB` — особенно критично: `SELECT metadata FROM orders` читает весь JSONB, даже если нужно одно поле. Лучше:

```sql
SELECT metadata->>'key' FROM orders;
```

Или выделить часто используемые поля в отдельные колонки.

---

#### 10. `VACUUM` / бloat

**Симптом:** таблица "раздулась" — `pg_stat_user_tables` показывает `n_dead_tup > 10%`.

```sql
SELECT relname, n_live_tup, n_dead_tup
FROM pg_stat_user_tables
WHERE n_dead_tup > n_live_tup * 0.1
ORDER BY n_dead_tup DESC;
```

**Причина:** много `DELETE`/`UPDATE` без `VACUUM`. Dead tuples занимают место, `Seq Scan` читает их.

**Решение:**

```sql
-- Ручной VACUUM (в emergency):
VACUUM (ANALYZE, VERBOSE) orders;

-- Авто-вакуум (по умолчанию включён):
autovacuum = on
autovacuum_vacuum_threshold = 50
autovacuum_vacuum_scale_factor = 0.2
```

**`VACUUM FULL`** — перестраивает таблицу. Lock-ит. Только в maintenance window.

---

### Шаг 3: Инструменты

| Инструмент | Что показывает | Когда |
|-----------|---------------|-------|
| `EXPLAIN (ANALYZE, BUFFERS)` | План выполнения, время, IO | Основной, каждый раз |
| `pg_stat_statements` | Топ запросов по `total_exec_time`, `calls` | Найти, какой запрос медленный |
| `pg_stat_user_tables` | `n_dead_tup`, `last_vacuum` | Бloat, VACUUM |
| `pg_locks` | Блокировки | Deadlock, long transactions |
| `pg_stat_activity` | Активные сессии, текущий запрос | Кто сейчас пишет/читает |
| `pg_index` + `pg_stats` | Индексы, статистика | Проверить, какой индекс существует |
| `pg_class` + `pg_statistic` | Статистика по таблицам | Устарела ли |
| APM (Datadog, New Relic) | SQL + время + частота | Production |
| `pgBadger` (лог) | Анализ PostgreSQL-лога | Постфактум |

#### `pg_stat_statements` — мой основной инструмент в production

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
-- postgresql.conf:
shared_preload_libraries = 'pg_stat_statements'
pg_stat_statements.track = top   -- только top-level, не nested
```

```sql
SELECT
    calls,
    round(total_exec_time::numeric, 2) AS total_ms,
    round(mean_exec_time::numeric, 2) AS mean_ms,
    rows,
    round(shared_blks_hit::numeric / GREATEST(shared_blks_read, 1) * 100, 2) AS cache_hit_pct,
    left(query, 120) AS query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 20;
```

**Что я ищу:**

| Метрика | Красный флаг |
|---------|-------------|
| `calls` высокий + `mean_ms` высокий | N+1 или медленный запрос, который часто вызывается |
| `cache_hit_pct < 90%` | Много disk IO. `shared_buffers` мал или данные не в памяти |
| `rows` высокий + `mean_ms` высокий | Запрос возвращает много данных. `SELECT *`? |
| `total_ms` высокий, но `mean_ms` низкий | Редкий, но медленный запрос (например, отчёт) |

---

### Шаг 4: Проверка индексов

```sql
-- Какие индексы есть:
SELECT
    i.relname AS index_name,
    idx.relname AS table_name,
    am.amname AS index_type,
    pg_get_indexdef(i.oid) AS definition
FROM pg_class i
JOIN pg_class idx ON i.relname = idx.relname
JOIN pg_am am ON i.relam = am.oid
WHERE idx.relname = 'orders';

-- Какие индексы используются:
SELECT
    indexrelname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE relname = 'orders'
ORDER BY idx_scan DESC;
```

Если `idx_scan = 0` — индекс не используется. Кандидат на удаление (если не для DDL).

---

### Шаг 5: Проверка блокировок

```sql
-- Текущие блокировки:
SELECT
    blocked_locks.pid AS blocked_pid,
    blocking_locks.pid AS blocking_pid,
    blocked_activity.query AS blocked_query,
    blocking_activity.query AS blocking_query
FROM pg_catalog.pg_locks blocked_locks
JOIN pg_catalog.pg_locks blocking_locks
    ON blocking_locks.locktype = blocked_locks.locktype
    AND blocking_locks.database IS NOT DISTINCT FROM blocked_locks.database
    AND blocking_locks.relation IS NOT DISTINCT FROM blocked_locks.relation
    AND blocking_locks.page IS NOT DISTINCT FROM blocked_locks.page
    AND blocking_locks.tuple IS NOT DISTINCT FROM blocked_locks.tuple
    AND blocking_locks.virtualxid IS NOT DISTINCT FROM blocked_locks.virtualxid
    AND blocking_locks.transactionid IS NOT DISTINCT FROM blocked_locks.transactionid
    AND blocking_locks.classid IS NOT DISTINCT FROM blocked_locks.classid
    AND blocking_locks.objid IS NOT DISTINCT FROM blocked_locks.objid
    AND blocking_locks.objsubid IS NOT DISTINCT FROM blocked_locks.objsubid
    AND blocking_locks.pid != blocked_locks.pid
JOIN pg_catalog.pg_stat_activity blocked_activity ON blocked_activity.pid = blocked_locks.pid
JOIN pg_catalog.pg_stat_activity blocking_activity ON blocking_activity.pid = blocking_locks.pid
WHERE NOT blocked_locks.granted;
```

**Типовые причины блокировок:**

| Причина | Симптом |
|---------|---------|
| Long transaction | `SELECT` держит `ACCESS SHARE`, `UPDATE` ждёт `ROW SHARE` |
| Deadlock | Два процесса ждут друг друга. PostgreSQL убивает один. |
| `VACUUM` | `VACUUM` держит `ACCESS EXCLUSIVE` (в `VACUUM FULL`). |
| `ALTER TABLE` | Держит `ACCESS EXCLUSIVE`, блокирует всё. |

**Решение:**
- `idle in transaction` → убить: `SELECT pg_terminate_backend(pid);`
- Long transaction → сократить.
- Deadlock → изменить порядок операций (всегда в одном порядке).

---

### Шаг 6: Параллелизм

PostgreSQL 9.6+ может параллельно сканировать таблицу:

```
Parallel Seq Scan on orders  (cost=0.00..1200.00 rows=1000 width=120)
  (actual time=0.1..4.2 rows=1000 loops=4)
Workers Planned: 3
Workers Launched: 3
```

**Когда включается:**
- Таблица > `min_parallel_table_scan_size` (default 8MB).
- `max_parallel_workers_per_gather` > 0 (default 2).

**Когда не включается:**
- `SET max_parallel_workers_per_gather = 0;` (на сессии).
- Таблица маленькая.
- `VACUUM` недавно (parallel scan недоступен для unlogged tables).

**Тюнинг:**

```sql
-- postgresql.conf
max_worker_processes = 8
max_parallel_workers_per_gather = 4
min_parallel_table_scan_size = '4MB'
```

---

### Шаг 7: `shared_buffers` и disk IO

```sql
-- Сколько данных в памяти:
SELECT
    relname,
    pg_size_pretty(pg_total_relation_size(oid)) AS size,
    shared_blks_hit,
    shared_blks_read,
    round(shared_blks_hit::numeric / GREATEST(shared_blks_read, 1) * 100, 2) AS cache_hit_pct
FROM pg_statio_user_tables
ORDER BY pg_total_relation_size(oid) DESC;
```

| `cache_hit_pct` | Статус |
|-----------------|--------|
| > 99% | Хорошо. Данные в `shared_buffers`. |
| 95–99% | Нормально. |
| < 90% | Много disk IO. Увеличить `shared_buffers` или данные не помещаются. |

**`shared_buffers`** — обычно 25% RAM. Больше — не всегда лучше (OS page cache тоже помогает).

---

### Итого: чеклист

| Шаг | Действие |
|-----|----------|
| 1 | `EXPLAIN (ANALYZE, BUFFERS)` — план + IO |
| 2 | `pg_stat_statements` — топ по `total_exec_time` |
| 3 | Смотрю: `Seq Scan`? `Sort: external merge`? `Hash Join` с плохим estimate? |
| 4 | Проверяю индексы: `pg_stat_user_indexes` — используются ли |
| 5 | `ANALYZE` — если estimate ≠ actual |
| 6 | `pg_stat_user_tables` — `n_dead_tup` > 10%? → `VACUUM` |
| 7 | `pg_locks` — блокировки? Long transactions? |
| 8 | `work_mem`, `shared_buffers` — хватает ли памяти |
| 9 | `OFFSET` → keyset pagination |
| 10 | `SELECT *` → только нужные колонки |
| 11 | Повторяю `EXPLAIN ANALYZE` |
| 12 | Проверяю, что другие запросы не сломались |

**Главное правило:** сначала `EXPLAIN ANALYZE`, потом — гипотеза. Не гадать. Измерить.

> ⏱ 41.4s · prompt 54845 tok · output 5246 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про агрегат в DDD: что это, как определять границы, пример с объяснением, типовая ошибка. Это продолжение интервью на роль PHP-бэкенда. Нужно ответить практично, на уровне senior-разработчика, без воды.

Структура ответа:
1. Определение агрегата — коротко, по сути.
2. Принципы определения границ — практические критерии.
3. Пример с объяснением (продолжу Order/OrderLine, но добавлю контекст с Payment и Inventory, чтобы показать, почему они НЕ в одном агрегате).
4. Типовая ошибка — "God Aggregates" (агрегата, который пытается охватить всё). Это самая частая и самая дорогая ошибка.
5. Можно добавить вторую ошибку: слишком мелкий агрегат (fragmentation).

Буду писать на русском, как и предыдущие ответы. Использую код на PHP. Структурирую через таблицы и списки.

</details>

**Кандидат:**

## Агрегат в DDD

### Определение

Агрегат — это **группа объектов**, которые должны оставаться **консистентными** при изменении. Корень агрегата — единственный вход для изменения. Внешний мир не обращается к внутренним объектам напрямую.

```text
Агрегат = граница транзакционной целостности.

Внутри:  изменения атомарны (одна транзакция).
Снаружи: связь только через события и ID-ссылки.
```

Ключевая мысль: **агрегат — это не "класс", не "таблица", не "модуль". Это контракт согласованности.**

---

### Как определяю границы

#### Критерий 1: "Что должно быть атомарно?"

Если два изменения должны произойти **одновременно или не произойти** — они в одном агрегате.

```text
Order + OrderLine → один агрегат.
  Почему: если заказ создан, а позиция нет — данные невалидны.
  Транзакция: INSERT order + INSERT line = один commit.

Order + Payment → РАЗНЫЕ агрегаты (в большинстве случаев).
  Почему: оплата может быть отклонена, отменена, повторена.
  Заказ существует без оплаты. Платёж существует без заказа (pre-charge).
```

#### Критерий 2: "Кто контролирует инварианты?"

Если объект отвечает за инвариант — он в пределах агрегата.

```php
// Инвариант: "заказ не может быть оплачен, если пустой"
class Order
{
    public function markAsPaid(): void
    {
        if ($this->lines->isEmpty()) {
            throw new EmptyOrderException();  // ← инвариант защищён здесь
        }
        $this->status = OrderStatus::Paid;
    }
}
```

Если `Payment` будет в том же агрегате — он должен знать про `Order.lines`. Если нет — инвариант нарушается через `Payment::charge($order)` напрямую.

#### Критерий 3: "Какая единица изменения?"

Если два объекта **всегда** меняются вместе — один агрегат. Если меняются **разные** причины — разные.

| Объект | Причина изменения 1 | Причина изменения 2 |
|--------|-------------------|-------------------|
| `Order` | Статус изменился | Позиция добавлена |
| `OrderLine` | Позиция добавлена | — |
| `Payment` | Оплата прошла | — |
| `Inventory` | Тovar зарезервирован | — |

`Order` + `OrderLine` — одна причина: "изменение заказа". Один агрегат.

`Payment` — отдельная причина: "оплата". Другой агрегат.

`Inventory` — отдельная причина: "бронирование". Третий агрегат.

#### Критерий 4: "Можно ли изменить один объект без другого?"

| Вопрос | Ответ | Вывод |
|--------|-------|-------|
| Можно ли изменить `OrderLine`, не трогая `Order`? | Нет (количество, цена — часть заказа) | Один агрегат |
| Можно ли изменить `Payment`, не трогая `Order`? | Да (отмена, retry) | Разные агрегаты |
| Можно ли изменить `Order`, не трогая `Payment`? | Да (добавить позицию) | Разные агрегаты |

---

### Пример: E-commerce, заказ и оплата

#### Доменная модель

```text
Агрегат Order:
├── Order (корень)
│   ├── number: "ORD-2024-001"
│   ├── status: "draft" | "paid" | "shipped" | "delivered" | "cancelled"
│   ├── customer_id: int (ссылка на другой агрегат)
│   └── lines: OrderLine[]
│       ├── product_id: int
│       ├── quantity: int
│       ├── unit_price_cents: int
│       └── currency: "USD"

Агрегат Payment:
├── Payment (корень)
│   ├── id
│   ├── order_id: int (ссылка)
│   ├── amount_cents: int
│   ├── currency: "USD"
│   ├── status: "pending" | "charged" | "failed" | "refunded"
│   ├── gateway_id: string
│   └── idempotency_key: string

Агрегат Inventory:
├── InventoryItem (корень)
│   ├── product_id
│   ├── warehouse_id
│   ├── stock_count: int
│   └── reserved_count: int
```

#### Связи

```text
Order ──(id)──→ Payment     (Payment ссылается на Order)
Order ──(id)──→ Inventory   (Inventory резервирует по product_id)
```

Связи — **через ID**, не через обект-ссылку. Это важно:

```php
// В Order:
private int $customerId;  // ← ID, не Customer-объект

// В Payment:
private int $orderId;     // ← ID, не Order-объект
```

#### Почему Payment — отдельный агрегат

| Причина | Объяснение |
|---------|-----------|
| Разный жизненный цикл | Order живёт "draft → paid → shipped". Payment живёт "pending → charged → refunded". |
| Разная команда | Order меняет продавец. Payment меняет платёжный провайдер. |
| Разная частота изменений | Payment может быть retry'нут 3 раза. Order — один раз. |
| Инварианты не переплетены | "Order не может быть shipped, если нет Payment" — это **приложение** проверяет, а не домен. |
| Масштабирование | Payment может быть в микросервисе. Order — в другом. |

#### Почему OrderLine — в агрегате Order

| Причина | Объяснение |
|---------|-----------|
| Атомарность | Добавление позиции + изменение total — одно действие. |
| Инвариант | "Order не может быть empty" — OrderLine нужен для проверки. |
| Одна команда | "Добавить позицию" — одна операция. |
| Разделяют транзакцию | INSERT order + INSERT line — один commit. |

---

### Как выглядит код

```php
// Агрегат: Order
class Order
{
    public function __construct(
        private string    $number,
        private int       $customerId,
        private string    $status = 'draft',
        private readonly Collection $lines = new ArrayCollection(),
    ) {}

    // Единственный вход для изменения
    public function addLine(string $productId, int $qty, int $price, string $currency): void
    {
        if ($this->status !== 'draft') {
            throw new OrderNotEditableException($this->number);
        }
        // ...
    }

    public function markAsPaid(): void
    {
        if ($this->lines->isEmpty()) {
            throw new EmptyOrderException();
        }
        if ($this->status !== 'draft') {
            throw new InvalidStatusTransitionException('draft', 'paid');
        }
        $this->status = 'paid';
    }
}

// Отдельный агрегат: Payment
class Payment
{
    public function __construct(
        private int    $orderId,
        private int    $amountCents,
        private string $currency,
        private string $status = 'pending',
    ) {}

    public function markAsCharged(string $gatewayId): void
    {
        if ($this->status !== 'pending') {
            throw new PaymentAlreadyProcessedException($this->id);
        }
        $this->status = 'charged';
        $this->gatewayId = $gatewayId;
    }
}

// Use case: оплата заказа
class ConfirmOrderUseCase
{
    public function __construct(
        private OrderRepository  $orders,
        private PaymentRepository $payments,
        private EventPublisher   $events,
        private TransactionManager $tx,
    ) {}

    public function __invoke(string $orderNumber): void
    {
        $order = $this->orders->byNumber($orderNumber);
        $payment = new Payment(
            orderId: $order->id(),
            amountCents: $order->totalAmountCents(),
            currency: $order->currency(),
        );

        // Одна транзакция: заказ + платёж.
        // Это НЕ "один агрегат", а "одна бизнес-операция".
        $this->tx->execute(function () use ($order, $payment): void {
            $order->markAsPaid();
            $this->orders->save($order);

            $this->payments->save($payment);
        });

        // Событие — после commit
        $this->events->publish(new OrderPaid($order->id(), $payment->id()));
    }
}
```

Заметьте: `Order` и `Payment` — **разные** агрегаты. Но в одном use case они сохраняются в **одной транзакции**. Это нормально: транзакция — это не агрегат. Транзакция — это операция. Агрегат — это граница согласованности данных.

---

### Типовая ошибка: "God Aggregates"

#### Пример

```php
class Order
{
    public OrderLine[] $lines;
    public Payment $payment;           // ← в агрегате
    public Inventory[] $inventory;    // ← в агрегате
    public Shipment $shipment;        // ← в агрегате
    public Refund $refund;            // ← в агрегате
    public Discount[] $discounts;     // ← в агрегате
}
```

Один класс, 6 ответственностей, 150 строк.

#### Почему это ошибка

| Проблема | Последствие |
|----------|-------------|
| Изменение Payment трогает Order | Нарушение OCP: каждый новый платёжный сценарий правит Order |
| Изменение Inventory трогает Order | Нарушение SRP: Order знает про склад |
| Изменение Shipment трогает Order | Нарушение SRP: Order знает про логистику |
| Транзакция огромная | INSERT/UPDATE 6 таблиц. Блокировки на 6 таблиц. |
| Тестирование мучительно | Мокать Payment, Inventory, Shipment, Refund, Discount одновременно |
| Конкурентность | Два request одновременно: один меняет Payment, другой — Shipment. Deadlock. |
| Масштабирование | Нельзя разложить на микросервисы |

#### Правильно

```text
Агрегат Order:
    Order + OrderLine

Агрегат Payment:
    Payment (отдельно)

Агрегат Inventory:
    InventoryItem (отдельно)

Агрегат Shipment:
    Shipment + ShipmentItem (отдельно)
```

Связь — через события:

```text
Order.markAsPaid()
  → публикует OrderPaid
    → PaymentListener создаёт Payment
    → InventoryListener резервирует товар
    → ShipmentListener создаёт Shipment (когда отгружают)
```

Каждый агрегат — **отдельная** транзакция. Если Inventory резервирование упало — Order уже `paid`, но товар не зарезервирован. Это **acceptable inconsistency**: через retry или компенсацию (saga) всё придёт к консистентности.

#### Когда God Aggregates допустим

| Условие | Почему |
|---------|--------|
| 2–3 сущности, всегда меняются вместе | Разделение создаёт overhead без выигрыша |
| Нет конкурентности | Один пользователь, одна сессия |
| Простая логика | Нет 6 разных команд |
| Не планируется рост | Система не вырастет за год |

Пример: `Profile` (имя, email, avatar) + `Preferences` (язык, тема) — можно в одном агрегате. Они связаны, меняются вместе, нет конкурентности.

Но `Profile` + `Order` — **нет**.

---

### Вторая типовая ошибка: фрагментация

#### Пример

```php
class Order
{
    // Только number, status, customer_id
}

class OrderLine  // отдельная сущность, не в агрегате
{
    // product_id, quantity, price
}

class OrderTotal  // отдельная сущность
{
    // total_cents, currency
}
```

Три сущности, три таблицы, три repository. Но на самом деле — **один** агрегат.

#### Почему это ошибка

| Проблема | Последствие |
|----------|-------------|
| `OrderTotal` не атомарен с `Order` | Может быть `Order` без `Total` или наоборот |
| `OrderLine` отдельно | Можно добавить строку без заказа |
| Инварианты размазаны | "Заказ не может быть empty" — кто проверяет? |
| Три транзакции | Три commit'а. Если второй упал — первый уже записан. |

#### Правильно

`Order` + `OrderLine` — **один** агрегат. `OrderTotal` — производное поле, не отдельная сущность:

```php
class Order
{
    public function totalAmountCents(): int
    {
        return $this->lines->reduce(fn($carry, $line) => $carry + $line->amountCents(), 0);
    }
}
```

Производные значения — не сущности. Это вычисление.

---

### Итого: как я определяю границы

| Вопрос | Если да → один агрегат | Если нет → разные |
|--------|----------------------|-------------------|
| Должны ли изменения быть атомарными? | ✓ | ✗ |
| Кто контролирует инвариант? | Один объект | Несколько |
| Меняются ли по одной причине? | ✓ | ✗ |
| Можно ли изменить один без другого? | Нет | Да |
| Разные команды управляют? | ✗ | ✓ |
| Разная частота изменений? | ✗ | ✓ |
| Конкурентные доступы? | ✗ | ✓ |

**Главное правило:**

> Агрегат — это минимальная группа объектов, которая должна быть консистентной.
> Не максимальная. Минимальная.

Если сомневаюсь — **разделяю**. Слишком большой агрегат лечится сложнее, чем слишком маленький. Слишком большой — это God Object, который невозможно разложить. Слишком маленький — это просто несколько объектов с ID-ссылками, что легко исправить добавлением метода на корень.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

## Конкурентные изменения агрегата

### Что происходит при одновременном изменении

#### Сценарий: lost update

Два процесса одновременно меняют `Order.status`:

```text
Процесс A (оплата):              Процесс B (отмена):
─────────────────               ─────────────────
t1: SELECT status → 'draft'
                                  t2: SELECT status → 'draft'
t3: UPDATE SET status = 'paid'
                                  t4: UPDATE SET status = 'cancelled'

Результат: status = 'cancelled'
Ожидалось: 'paid' (или ошибка)
```

Оба процесса **успешно** записали. Ни один не упал. Данные в невалидном состоянии: заказ "отменён", но платёж уже списан.

Это называется **lost update** — одно изменение затёрло другое.

---

### Почему это происходит

PostgreSQL по умолчанию использует **MVCC** с изоляцией `READ COMMITTED`. Это значит:

| Уровень | Что видит каждый процесс |
|---------|--------------------------|
| `READ` | Последнюю **закоммиченную** версию строки |
| `WRITE` | Блокирует **строк** на время UPDATE |
| `COMMIT` | Становится видимым для других |

Проблема: `SELECT` **не блокирует**. Оба процесса читают `status = 'draft'`, оба решают, что "можно менять", оба пишут.

```sql
-- Процесс A:
BEGIN;
SELECT status FROM orders WHERE id = 1;  -- 'draft', НЕ блокирует
UPDATE orders SET status = 'paid' WHERE id = 1;
COMMIT;

-- Процесс B (одновременно):
BEGIN;
SELECT status FROM orders WHERE id = 1;  -- 'draft', НЕ блокирует
UPDATE orders SET status = 'cancelled' WHERE id = 1;
COMMIT;
```

`UPDATE` блокирует строку, но **только на момент записи**. Чтение уже произошло. Оба увидели `'draft'`.

---

### Механизмы защиты

#### 1. Optimistic Locking (version column)

Самый частый подход. Добавляю колонку `version`, которая инкрементируется при каждом изменении.

```sql
ALTER TABLE orders ADD COLUMN version INT NOT NULL DEFAULT 0;
```

```php
// Entity
#[ORM\Column(type: 'integer')]
private int $version = 0;

public function getVersion(): int { return $this->version; }
```

```php
// Use case
public function confirmOrder(int $orderId, int $expectedVersion): void
{
    $order = $this->orders->byId($orderId);

    if ($order->getVersion() !== $expectedVersion) {
        throw new ConcurrentModificationException($orderId, $expectedVersion);
    }

    $order->markAsPaid();
    $order->bumpVersion();  // version + 1
    $this->orders->save($order);
}
```

SQL, который генерируется:

```sql
BEGIN;
SELECT * FROM orders WHERE id = 1;  -- version = 0
UPDATE orders
SET status = 'paid', version = 1
WHERE id = 1 AND version = 0;       -- ← вот эта проверка
COMMIT;
```

**Что происходит при конфликте:**

```text
Процесс A:                              Процесс B:
─────────                              ─────────
t1: SELECT → version = 0
t2: UPDATE WHERE version = 0  → 1 строка
COMMIT
                                  t3: UPDATE WHERE version = 0
                                      → 0 строк (version уже 1!)
                                      → SQLSTATE 23505 или rowCount = 0
```

Процесс B **замечает**, что 0 строк обновлено → бросает `ConcurrentModificationException`.

```php
public function save(Order $order): void
{
    $sql = 'UPDATE orders SET status = :status, version = :version WHERE id = :id AND version = :expected';
    $result = $this->em->getConnection()->executeStatement($sql, [
        'status'     => $order->status(),
        'version'    => $order->getVersion() + 1,
        'id'         => $order->id(),
        'expected'   => $order->getVersion(),
    ]);

    if ($result === 0) {
        throw new ConcurrentModificationException($order->id(), $order->getVersion());
    }
}
```

**Плюсы:**

| | |
|---|---|
| Нет блокировок при чтении | Два процесса могут одновременно читать |
| Блокировка только на момент записи | Короткая |
| Простая модель | Клиент знает, в какой версии он читал |

**Минусы:**

| | |
|---|---|
| Need retry | Если конфликт — клиент должен перезагрузить и повторить |
| Не подходит для длинных операций | Если операция занимает 30 секунд — шанс конфликта высок |
| Need `version` в каждом запросе | Клиент должен передавать `expectedVersion` |

**Когда использую:** 90% случаев. Оптимистичный подход: конфликты редки, retry дешевле, чем блокировка.

---

#### 2. Pessimistic Locking (SELECT FOR UPDATE)

Блокировка строки на время транзакции.

```sql
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;  -- ← блокирует строку
-- Процесс B, который выполнит SELECT FOR UPDATE в это время,
-- будет ЖДАТЬ, пока процесс A не сделает COMMIT или ROLLBACK.

UPDATE orders SET status = 'paid' WHERE id = 1;
COMMIT;
```

```php
// Doctrine
$qb = $em->createQueryBuilder();
$qb->select('o')
   ->from(Order::class, 'o')
   ->where('o.id = :id')
   ->setParameter('id', $orderId)
   ->setLockMode(LockMode::OPTIMISTIC);  // ← нет, это optimistic

// Pessimistic:
$qb->setLockMode(LockMode::PESSIMISTIC_WRITE);
```

Или raw SQL:

```php
$sql = 'SELECT * FROM orders WHERE id = :id FOR UPDATE';
$order = $em->getConnection()->fetchAssociative($sql, ['id' => $orderId]);
```

**Что происходит при конфликте:**

```text
Процесс A:                          Процесс B:
─────────                          ─────────
t1: SELECT FOR UPDATE → блокирует
t2: UPDATE → COMMIT
                                   t3: SELECT FOR UPDATE → ЖДЁТ
                                   t4: (после commit A) читает 'paid'
                                   t5: UPDATE → 'cancelled' (или error)
```

Процесс B **ждёт**, а не получает lost update.

**Плюсы:**

| | |
|---|---|
| Нет lost update | Гарантированно последовательное изменение |
| Не нужен retry | Блокировка решает всё |

**Минусы:**

| | |
|---|---|
| Блокировка | Процесс B ждёт. Если A долгий — B тоже ждёт. |
| Deadlock | A ждёт B, B ждёт A. PostgreSQL убивает один. |
| Throughput | Параллельные операции сериализуются |
| Timeout | Если A упал и не commit'ил — B ждёт до `lock_timeout` (default: ∞) |

**Когда использую:**
- Короткие операции (до 100ms).
- Конфликты частые (и retry был бы дорог).
- Критичная последовательность (например, списание с баланса).

**`lock_timeout`:**

```sql
SET lock_timeout = '5s';  -- если блокировка > 5s → ошибка
```

Без этого B может ждать вечно (если A завис).

---

#### 3. Serializable isolation

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
```

Все транзакции, которые **конфликтуют**, откатываются. PostgreSQL проверяет, нет ли "циклической зависимости" между транзакциями.

```text
Процесс A: читает orders.id=1, пишет orders.id=1
Процесс B: читает orders.id=1, пишет orders.id=2
Процесс C: читает orders.id=2, пишет orders.id=1

A → B → C → A: цикл. PostgreSQL откатывает одну из транзакций.
```

**Плюсы:** максимальная изоляция.

**Минусы:**
- Низкий throughput (сериализация).
- `SerializationError` → retry.
- Не нужен для большинства случаев.

**Когда использую:** rarely. Только для операций, где "read then write" на разных строках и конфликт невозможен с `FOR UPDATE`.

---

### Проектирование с учётом конкурентности

#### Принцип 1: Короткая транзакция

```php
// Плохо: долгая транзакция
public function confirmOrder(int $id): void
{
    $this->em->wrapInTransaction(function () use ($id): void {
        $order = $this->orders->byId($id);
        $order->markAsPaid();
        $this->sendEmail();        // ← 2 сек
        $this->updateSearchIndex(); // ← 1 сек
        $this->callExternalApi();   // ← 5 сек
        $this->orders->save($order);
    });  // ← транзакция открыта 8 секунд. Блокировка на 8 секунд.
}

// Хорошо: короткая транзакция, тяжёлая работа — после
public function confirmOrder(int $id): void
{
    $orderId = $this->em->wrapInTransaction(function () use ($id): int {
        $order = $this->orders->byId($id);
        $order->markAsPaid();
        $this->orders->save($order);
        return $order->id();
    });

    // После commit — асинхронно:
    $this->events->publish(new OrderPaid($orderId));
}
```

**Правило:** транзакция = только БД-операции. Всё остальное — после.

---

#### Принцип 2: Order of operations

Если два процесса меняют разные поля одной строки — конфликт не нужен.

```sql
-- Процесс A: меняет status
UPDATE orders SET status = 'paid' WHERE id = 1;

-- Процесс B: меняет total
UPDATE orders SET total_cents = 15000 WHERE id = 1;
```

При `READ COMMITTED` — оба проходят. Каждый читает свою колонку, пишет свою. Нет конфликта.

Проблема только если **оба меняют одно и то же поле**. Или если изменение зависит от текущего значения:

```sql
-- Процесс A: balance = balance + 100
-- Процесс B: balance = balance - 50
-- Если оба прочитают balance = 1000:
--   A: 1100, B: 950. Ожидалось: 1050. Lost update.
```

---

#### Принцип 3: Domain invariant в одном месте

```php
// Плохо: инвариант размазан
class OrderService
{
    public function confirm(int $id): void
    {
        if ($order->status !== 'draft') throw ...;
        $order->markAsPaid();
    }

    public function cancel(int $id): void
    {
        if ($order->status !== 'draft') throw ...;
        $order->markAsCancelled();
    }
}

// Хорошо: инвариант в домене
class Order
{
    public function markAsPaid(): void
    {
        if ($this->status !== 'draft') {
            throw new InvalidStatusTransitionException($this->status, 'paid');
        }
        $this->status = 'paid';
    }

    public function markAsCancelled(): void
    {
        if ($this->status !== 'draft') {
            throw new InvalidStatusTransitionException($this->status, 'cancelled');
        }
        $this->status = 'cancelled';
    }
}
```

При конкурентности: оба процесса читают `'draft'`, оба вызывают `markAsPaid()`. Optimistic locking (version) отловит конфликт.

---

#### Принцип 4: Idempotency key для операций

```php
// Клиент передаёт idempotency key
POST /api/orders/123/confirm
X-Idempotency-Key: "a1b2c3d4"
```

```sql
CREATE TABLE order_operations (
    idempotency_key VARCHAR(64) PRIMARY KEY,
    order_id BIGINT NOT NULL,
    operation VARCHAR(32) NOT NULL,
    result JSONB,
    created_at TIMESTAMP DEFAULT NOW()
);
```

```php
public function confirm(int $id, string $idempotencyKey): void
{
    // Проверка: не делали ли уже?
    $existing = $this->em->createQueryBuilder()
        ->select('o')
        ->from(OrderOperation::class, 'o')
        ->where('o.idempotencyKey = :key')
        ->setParameter('key', $idempotencyKey)
        ->getQuery()
        ->getOneOrNullResult();

    if ($existing !== null) {
        return $existing->getResult();  // ← idempotent
    }

    // ... бизнес-логика ...

    // Записать операцию
    $op = new OrderOperation($idempotencyKey, $id, 'confirm', $result);
    $this->em->persist($op);
    $this->em->flush();
}
```

`UNIQUE(idempotency_key)` — если два процесса одновременно попытаются записать — один упадёт с `UniqueViolationException`.

---

### Что я делаю на практике

| Ситуация | Механизм | Почему |
|----------|----------|--------|
| Пользователь нажимает "Оплатить" дважды | **Idempotency key** | Клиентский retry |
| Два admin'а одновременно меняют статус | **Optimistic (version)** | Конфликт редкий, retry дешевле |
| Списание с баланса | **Pessimistic (FOR UPDATE)** | Конфликт частый, lost update критичен |
| Оплата + резервирование (разные агрегаты) | **Optimistic + retry** | Разные таблицы, разные блокировки |
| High-throughput (1000+ RPS на одну строку) | **Queue** (один на один) | Сериализация через очередь |

---

### Типовые ошибки

#### 1. SELECT без FOR UPDATE

```php
$order = $repo->find($id);       // ← нет блокировки
$order->markAsPaid();
$em->flush();
```

Два процесса одновременно: оба читают `'draft'`, оба пишут. Lost update.

**Решение:** `FOR UPDATE` или optimistic version.

---

#### 2. Долгая транзакция

```php
$em->wrapInTransaction(function () {
    $order = $repo->find($id);
    $order->markAsPaid();
    $httpClient->request('POST', 'https://payment.example.com/charge');  // ← 5 сек
    $em->flush();
});
```

Транзакция открыта 5+ секунд. Все, кто пытается `FOR UPDATE` на эту строку, ждут 5 секунд.

**Решение:** HTTP-вызов — **до** или **после** транзакции, не внутри.

---

#### 3. Deadlock

```text
Процесс A:
  1. UPDATE orders SET status = 'paid' WHERE id = 1
  2. UPDATE inventory SET reserved = reserved + 1 WHERE product_id = 5

Процесс B:
  1. UPDATE inventory SET reserved = reserved + 1 WHERE product_id = 5
  2. UPDATE orders SET status = 'cancelled' WHERE id = 1
```

A ждёт B (на строке inventory), B ждёт A (на строке orders). PostgreSQL убивает одного с `deadlock detected`.

**Решение:** одинаковый порядок операций во всех процессах.

```text
Всегда: orders → inventory (не наоборот)
```

---

#### 4. Catch + retry без backoff

```php
for ($i = 0; $i < 3; $i++) {
    try {
        $this->confirmOrder($id);
        return;
    } catch (ConcurrentModificationException $e) {
        // сразу retry, без задержки
    }
}
```

Если конфликт из-за долгой операции (5 сек) — 3 retry подряд в 10ms не помогут. Нужен **exponential backoff**:

```php
$delay = 100;
for ($i = 0; $i < 5; $i++) {
    try {
        $this->confirmOrder($id);
        return;
    } catch (ConcurrentModificationException $e) {
        usleep($delay * 1000);
        $delay *= 2;  // 100ms, 200ms, 400ms, 800ms, 1600ms
    }
}
throw new \RuntimeException("Failed after 5 retries");
```

---

### Итого

| Вопрос | Ответ |
|--------|-------|
| Что произойдёт при одновременном изменении? | Lost update (один затерло другое) |
| Как обнаружить? | `version` column + `UPDATE ... WHERE version = ?` |
| Как заблокировать? | `SELECT FOR UPDATE` |
| Как избежать deadlock? | Одинаковый порядок операций |
| Как сделать идемпотентным? | `idempotency_key` + `UNIQUE` |
| Как сократить блокировку? | Короткая транзакция, внешние вызовы — до/после |
| Какой подход по умолчанию? | Optimistic (version) + retry с backoff |
| Когда pessimistic? | Критичные операции, короткие, частые конфликты |

> ⏱ 31.2s · prompt 60026 tok · output 3608 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про доменные события в DDD/Symfony: когда публикую, как обрабатываю, какие проблемы решают и создают, связь с транзакциями БД. Это продолжение серии вопросов про DDD, Doctrine, Symfony.

Нужно структурировать ответ:
1. Что такое доменное событие (отличие от domain event в широком смысле)
2. Когда публикую (конкретные правила)
3. Как обрабатываю (sync vs async, Symfony Messenger)
4. Какие проблемы решает
5. Какие проблемы создаёт
6. Связь с транзакциями (Transactional Outbox, double-publish problem)
7. Практические примеры и антипаттерны

Важно: я уже в предыдущих ответах упоминал события (UserRegistered, OrderPaid). Здесь нужно углубиться и не дублировать, а дополнить. Особенно важно — связь с транзакциями, это самый сложный момент.

Структурирую так:
- Определение и отличие от "сообщений"
- Когда публикую (правила)
- Связь с транзакцией (самая важная часть)
- Sync vs Async
- Transactional Outbox
- Идемпотентность
- Антипаттерны
- Практические примеры

Сделаю ответ плотным, с примерами кода, без воды.

</details>

**Кандидат:**

## Доменные события: применение, обработка, связь с транзакциями

### Что это и чем не является

Доменное событие — это **факт**, который произошёл в домене. Не команда, не запрос, не инструкция.

```php
// Событие — факт. Прошедшее время.
final class OrderConfirmed {}
final class UserRegistered {}
final class PaymentCharged {}

// НЕ событие — команда
final class ConfirmOrderCommand {}
final class ChargePaymentCommand {}
```

Разница в направлении:

```text
Command:  "Сделай X"        → вызывается снаружи
Event:    "X произошло"     → публикуется изнутри
```

---

### Когда публикую

#### Правило: событие публикуется, когда доменное состояние изменилось

```php
class Order
{
    public function confirm(): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransitionException();
        }

        $this->status = OrderStatus::Confirmed;
        // ← Событие OrderConfirmed "произошло" здесь
    }
}
```

#### Когда НЕ публикую

| Ситуация | Почему |
|----------|--------|
| Промежуточное состояние внутри use case | Если операция не завершена — событие преждевременно |
| Результат внешнего вызова (до валидации) | Если external API вернул 200, но мы ещё не сохранили — событие не "произошло" |
| Повторная попытка (retry) | Если уже публиковали — не публикуем снова (идемпотентность) |

#### Конкретные случаи публикации

```php
// 1. Агрегат изменил состояние:
$order->confirm();                    // → OrderConfirmed
$user->changeEmail($newEmail);        // → UserEmailChanged
$payment->markAsCharged($gatewayId);  // → PaymentCharged

// 2. Агрегат создан:
new Order(...);                       // → OrderCreated (после persist)

// 3. Агрегат удалён:
$repo->delete($order);                // → OrderDeleted

// 4. Агрегат прошёл цикл:
$order->markAsDelivered();            // → OrderDelivered
```

#### Когда НЕ публикую

```php
// Не событие: внутренняя операция, не завершённая
$order->addLine($line);   // ← не публикуем. Заказ ещё не сохранён.

// Не событие: чтение
$order->getTotal();        // ← ничего не изменилось.

// Не событие: операция, которая может быть отменена
$order->reserveInventory();  // ← если не commit — не публикуем.
```

---

### Связь с транзакцией: самый критичный момент

#### Проблема: double-publish

```php
// Наивный подход:
public function confirmOrder(int $id): void
{
    $this->em->wrapInTransaction(function () use ($id): void {
        $order = $this->orders->byId($id);
        $order->confirm();
        $this->orders->save($order);

        // Событие — ВНУТРИ транзакции
        $this->publisher->publish(new OrderConfirmed($order->id()));
        // ← Если commit упадёт после publish — событие "ушло",
        //   а заказ не сохранён.
    });
}
```

**Две проблемы:**

1. **Если commit упадёт** — событие уже опубликовано (в очередь, в БД outbox, в Redis). Слушатели обработали, а данных нет.
2. **Если app упадёт между publish и commit** — то же самое.

Это называется **double-publish problem** или **distributed transaction problem**.

#### Решение: событие публикуется ПОСЛЕ commit

```php
public function confirmOrder(int $id): void
{
    // 1. Транзакция: только БД
    $order = $this->em->wrapInTransaction(function () use ($id): Order {
        $order = $this->orders->byId($id);
        $order->confirm();
        $this->orders->save($order);
        return $order;
    });

    // 2. После commit: публикуем событие
    $this->publisher->publish(new OrderConfirmed($order->id()));
}
```

**Проблема осталась:** если app упадёт между commit и publish — событие потеряно.

```text
t1: COMMIT (заказ сохранён)
t2: [APP CRASH]
t3: publish — не выполнено
t4: слушатели не знают о подтверждении
```

Это **data loss**. Заказ подтверждён, но email не отправлен, индекс не обновлён.

#### Решение: Transactional Outbox

Событие сохраняется **в той же транзакции**, что и изменение данных. Отдельный процесс (relay) доставляет события из outbox в MQ.

```sql
CREATE TABLE domain_events (
    id          BIGINT PRIMARY KEY,
    aggregate_id BIGINT NOT NULL,
    aggregate_type VARCHAR(100) NOT NULL,
    event_type  VARCHAR(100) NOT NULL,
    payload     JSONB NOT NULL,
    created_at  TIMESTAMP DEFAULT NOW(),
    published_at TIMESTAMP NULL  -- ← null = ещё не доставлено
);

CREATE INDEX idx_events_unpublished
    ON domain_events (created_at)
    WHERE published_at IS NULL;
```

```php
// Use case:
public function confirmOrder(int $id): void
{
    $this->em->wrapInTransaction(function () use ($id): void {
        $order = $this->orders->byId($id);
        $order->confirm();
        $this->orders->save($order);

        // Событие — в той же транзакции
        $event = new DomainEvent(
            aggregateId: $order->id(),
            aggregateType: Order::class,
            eventType: 'OrderConfirmed',
            payload: ['orderId' => $order->id(), 'at' => now()]
        );
        $this->events->save($event);
    });
    // COMMIT: и заказ, и событие — атомарно
}
```

```php
// Relay (отдельный процесс, cron или long-running):
class EventRelay
{
    public function run(): void
    {
        $unpublished = $this->em->createQueryBuilder()
            ->select('e')
            ->from(DomainEvent::class, 'e')
            ->where('e.publishedAt IS NULL')
            ->orderBy('e.createdAt', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();

        foreach ($unpublished as $event) {
            try {
                $this->messenger->dispatch(
                    (new \ReflectionClass($event->eventType))->newInstance()
                );
                $event->markAsPublished();
                $this->em->flush();
            } catch (\Throwable $e) {
                $this->logger->error("Failed to publish event {$event->id}");
                // Не делаем published_at = NOW() — будет retry
                continue;
            }
        }
    }
}
```

```cron
* * * * * bin/console app:relay-events
```

#### Что даёт Transactional Outbox

| Проблема | Решение |
|----------|---------|
| Commit + publish не атомарны | Событие в той же транзакции |
| App crash между commit и publish | Relay подхватит из outbox |
| Relay crash | Событие в БД, `published_at IS NULL` — будет retry |
| Duplicate delivery | Relay использует `id` как cursor / `published_at` |
| Ordering | `ORDER BY created_at` в relay |

#### Ограничения

| | |
|---|---|
| Задержка | Событие доставляется после relay (1–30 сек) |
| Инфраструктура | Нужен relay-процесс, outbox-таблица |
| Ordering не гарантирован | Если relay обрабатывает параллельно — порядок может быть нарушен |
| Duplicate | Relay может доставить дважды (at-least-once). Слушатель должен быть идемпотентным. |

---

### Sync vs Async

#### Sync (Event Dispatcher)

```php
// Symfony EventDispatcher — sync
$eventDispatcher->dispatch(new OrderConfirmed($orderId));

// Слушатель вызывается в том же request, в той же транзакции:
class OrderConfirmedListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // Если здесь exception — транзакция откатывается.
        // Это ДОБРО, если слушатель — часть бизнес-логики.
        // Это ЗЛО, если слушатель — side-effect (email, индекс).
    }
}
```

**Когда использую sync:**

| Сценарий | Почему sync |
|----------|------------|
| Валидация | "Заказ не может быть подтверждён, если нет позиций" |
| Расчёт производных полей | "При confirm → пересчитать total" |
| Инварианты, которые должны держаться в той же транзакции | Атомарность |

#### Async (Messenger)

```php
// Symfony Messenger — async
$this->bus->dispatch(new SendEmail($orderId));
$this->bus->dispatch(new UpdateIndex($orderId));

// Работает в отдельном процессе:
bin/console messenger:consume email
```

**Когда использую async:**

| Сценарий | Почему async |
|----------|------------|
| Email / push / SMS | Медленно, может упасть, не критично для основного потока |
| Search-индекс | Медленно, не блокирует ответ |
| Аналитика | Не должно влиять на latency |
| Внешние API (CRM, ERP) | Медленно, может быть недоступен |
| Тяжёлые расчёты (отчёт, экспорт) | Долго |

#### Комбинированный подход

```php
public function confirmOrder(int $id): void
{
    // 1. Транзакция: изменение данных
    $order = $this->em->wrapInTransaction(function () use ($id): Order {
        $order = $this->orders->byId($id);
        $order->confirm();

        // 2. Sync-слушатель (внутри транзакции):
        //    - пересчёт total
        //    - валидация инвариантов
        //    (вызывается через EventDispatcher)

        $this->orders->save($order);

        // 3. Outbox (внутри транзакции):
        $this->outbox->append(new OrderConfirmed($order->id()));
    });

    // 4. Async (после commit):
    //    Relay доставит OrderConfirmed в MQ
    //    → SendEmail, UpdateIndex, NotifyCRM
}
```

---

### Идемпотентность слушателей

Мессенджер / Relay гарантирует **at-least-once**. Событие может быть доставлено 2 раза.

```php
// ПЛОХО: не идемпотентный слушатель
class SendRegistrationEmailListener
{
    public function __invoke(UserRegistered $event): void
    {
        // Если доставлено 2 раза — 2 письма!
        $this->mailer->send($this->buildWelcomeEmail($event->email));
    }
}

// ХОРОШО: идемпотентный слушатель
class SendRegistrationEmailListener
{
    public function __invoke(UserRegistered $event): void
    {
        // Проверка: уже отправляли?
        $dedupeKey = "welcome_{$event->userId}";

        if ($this->redis->exists($dedupeKey)) {
            return;  // ← уже обработано
        }

        $this->mailer->send($this->buildWelcomeEmail($event->email));

        // Отмечаем: отправлено
        $this->redis->setex($dedupeKey, 86400, '1');  // 24 часа
    }
}
```

#### Ключевые механизмы идемпотентности

| Механизм | Пример |
|----------|--------|
| Dedupe по `aggregate_id + event_type` | "Email welcome для user 42" — только 1 раз |
| `UNIQUE` constraint | `payment_transactions.idempotency_key` |
| `published_at` в outbox | Relay не доставляет повторно |
| Versioning | `if (current_version > event_version) return;` |
| Upsert | Search-индекс: `PUT /users/42` (не `POST`) |

---

### Какие проблемы решает

| Проблема | Как решает |
|----------|-----------|
| Coupling между модулями | Модуль A не знает про модуль B. Публикует событие. |
| Задержка (email, индекс) | Async: ответ не ждёт external systems |
| Атомарность + side-effects | Outbox: данные и событие в одной транзакции |
| Масштабируемость | Relay / workers — независимые процессы |
| Восстановление | Outbox в БД. Relay подхватывает. |
| Наблюдаемость | События в outbox — лог изменений |

### Какие проблемы создаёт

| Проблема | Описание |
|----------|----------|
| Задержка | Данные записаны, эффект — через 1–30 сек. UI показывает "старые" данные. |
| Дубликаты | At-least-once → слушатель должен быть идемпотентным |
| Ordering | Параллельный relay → события могут обработаться не по порядку |
| Отладка | "Почему email не отправлен?" → нужно проверить outbox, relay, MQ, worker |
| Сложность | + outbox таблица, + relay процесс, + идемпотентность в каждом слушателе |
| Две правды | БД имеет `status = 'confirmed'`, а email ещё не отправлен. Окно несогласованности. |
| Testирование | Нужно мокать relay / outbox / MQ |

---

### Антипаттерны

#### 1. Событие вместо метода

```php
// ПЛОХО: "сообщаю" о том, что сделал, вместо того, чтобы сделать
class OrderService
{
    public function confirm(int $id): void
    {
        // НЕ меняю status!
        // Просто публикую событие и надеюсь, что кто-то его обработает.
        $this->publisher->publish(new OrderConfirmRequested($id));
    }
}
```

**Почему плохо:** `Order.status` не изменён. Если слушатель не обработает — заказ в старом состоянии. Событие — не замена изменению состояния.

**Правильно:**

```php
public function confirm(int $id): void
{
    $order = $this->orders->byId($id);
    $order->confirm();  // ← статус изменён
    $this->orders->save($order);

    // Событие — факт, что статус изменён:
    $this->outbox->append(new OrderConfirmed($order->id()));
}
```

#### 2. Слушатель с бизнес-логикой

```php
class OrderConfirmedListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        // Сложная бизнес-логика в слушателе:
        $order = $this->orders->byId($event->orderId);
        if ($order->customer->getCountry() === 'RU') {
            $order->applyTax('VAT');
        } elseif ($order->customer->getCountry() === 'EU') {
            $order->applyTax('EU_VAT');
        }
        $this->orders->save($order);
    }
}
```

**Почему плохо:**
- Бизнес-логика размазана.
- Если слушатель не обработает — данные невалидны.
- Тестирование: нужно знать про OrderConfirmed, чтобы протестировать расчёт налога.

**Правильно:** бизнес-логика — в домене. Событие — только для side-effects.

#### 3. Цикл событий

```text
OrderConfirmed → InventoryReserved → InventoryLow → OrderCancelled → OrderRefundRequested → ...
```

Если слушатель A публикует событие, которое триггерит слушатель B, который публикует событие, которое триггерит слушателя A — бесконечный цикл.

**Решение:**
- Ограничить глубину (max 3 events per event).
- Deduplication: `if (already_processed(event_id)) return`.
- Явный "цикл" — это не event-driven, это saga.

#### 4. Событие для каждой операции

```php
// ПЛОХО: 10 событий для одной операции
$order->addLine();    // OrderLineAdded
$order->removeLine(); // OrderLineRemoved
$order->changeTotal(); // OrderTotalChanged
$order->applyDiscount(); // OrderDiscountApplied
$order->confirm();    // OrderConfirmed
// ... и ещё 5
```

**Правильно:** событие — на **границу значимого изменения**. Не на каждый метод.

```php
// Одно событие на одну бизнес-операцию:
$order->confirm();    // OrderConfirmed
```

#### 5. Отсутствие idempotency

```php
class UpdateSearchListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        $order = $this->orders->byId($event->orderId);
        $this->search->upsert('orders', $order->id, $order->toSearchData());
    }
}
```

Если доставлено 2 раза — upsert дважды. Это **ок** (upsert идемпотентен).

Но:

```php
class SendNotificationListener
{
    public function __invoke(OrderConfirmed $event): void
    {
        $this->notify->send($event->orderId);
        // Если 2 раза — 2 уведомления!
    }
}
```

Это **не ок**. Нужен deduplication.

---

### Итого: мой подход

```text
1. Событие = факт изменения доменного состояния.
2. Публикуется ПОСЛЕ commit (или через outbox ВНУТРИ commit).
3. Sync — для инвариантов (внутри транзакции).
4. Async — для side-effects (после commit).
5. Transactional Outbox — для надёжности (at-least-once).
6. Идемпотентность — в каждом слушателе (deduplication).
7. Событие — не замена бизнес-логике.
8. Событие — не замена команде.
```

```text
┌─────────────────────────────────────────────────────┐
│  Use Case                                           │
│  ┌───────────────────────────────────────────────┐  │
│  │  Transaction                                  │  │
│  │  ┌─────────────────────────────────────────┐  │  │
│  │  │  1. Load aggregate                      │  │  │
│  │  │  2. Change state (domain logic)         │  │  │
│  │  │  3. Sync listeners (invariants)         │  │  │
│  │  │  4. Append event to outbox              │  │  │
│  │  │  5. COMMIT                              │  │  │
│  │  └─────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────┘  │
│                                                      │
│  6. [ASYNC] Relay publishes events → MQ             │
│     → SendEmail, UpdateIndex, NotifyCRM             │
└─────────────────────────────────────────────────────┘
```

Это даёт:
- Атомарность: данные + событие — один commit.
- Надёжность: outbox в БД. Relay подхватит.
- Асинхронность: side-effects не блокируют ответ.
- Идемпотентность: at-least-once + deduplication.
- Восстановление: если app упал — данные в БД, relay продолжит.

> ⏱ 41.3s · prompt 67708 tok · output 4933 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

<details><summary>Ход мысли модели</summary>

We need answer user's DDD question in Russian. Need think thoroughly. Need explain Value Object vs Entity, decision framework, ubiquitous language and influence on code. User likely expects interview-style, practical with examples, trade-offs. Need avoid overclaim. We can discuss identity, equality, immutability, lifecycle, invariants, persistence, examples: Money, Address, Email, Phone vs User, Order, OrderLine, Product. Also subtle cases: Customer? Account? Product? SKU? Maybe OrderLine entity vs line number? Need discuss how to decide: identity question: "Does the same value with different identity mean different thing?" if only properties matter -> VO. If history, unique identity, lifecycle, references -> Entity. Also stateful vs stateless. Use cases: same amount of 100 USD for different orders? In order total maybe VO; but if Payment has id and status then entity. Need discuss aggregate roots and entities inside aggregate. Need mention that in DDD not all objects must be entities; many can be VOs. Also mention anti-patterns: entity that is actually value (Money as entity with id), entity with mutable state but no invariants? "anemic entities". Ubiquitous language: shared terms among domain experts and dev team, reflected in names, methods, events, endpoints, docs, UI. Influence: consistent naming, fewer synonyms, less translation, API and domain model aligned. Examples: "Client" vs "Customer" vs "Buyer"; choose one. "Booking" vs "Reservation" vs "Order". Methods: `Order::confirm()`, `OrderConfirmed` event. Avoid "data", "record", "item" too generic. How build: domain model discovery, glossary, refactor names, avoid code synonyms. Need maybe mention language as contract: code as executable spec. Include decision flow and examples.

Need likely answer in structured manner. Need ensure not too verbose but desired oververbosity 9, so detailed.

Let's craft final in Russian. Include table VO vs Entity. Decision heuristics. Example: Money, Address, Order, OrderLine, Customer. Maybe discuss tricky cases: `Email` VO, `User` Entity; `Money` VO but `Payment` Entity; `Address` VO but `Customer` entity contains address; `OrderLine` could be entity because identity within aggregate and may have line id? Actually DDD: line items often entity if has identity (line id, can update quantity), but can be VO if no identity and aggregate root manages collection? In many models line items are child entities because each has ID, but could be values if no independent updates? We can present: if line needs its own id and queries, entity; if just immutable detail, VO. `Product` can be entity or aggregate root depending on bounded context: in catalog entity with id, in order line maybe product snapshot VO (name, price at time). Good example. Also discuss reference by id vs object; entities across aggregate reference by ID. VO embedded, Entity managed.

Need address how decide:
1. Is identity core? Same values with different instances considered distinct? -> Entity.
2. Can it change over time while retaining identity? -> Entity.
3. Is equality by all properties or by ID? -> VO vs Entity.
4. Does it have lifecycle / status? -> Entity (often).
5. Is it referenced by other aggregates? If only as data, VO; if independent concept with references, Entity.
6. Do domain experts talk about "it" as thing? -> entity/aggregate; if attribute -> VO.

Need mention identity for entities is not necessarily surrogate DB id; business identity (OrderNumber, CustomerId) can be value object used as ID. IDs are often VOs.

UBIQUITOUS LANGUAGE: defined in bounded context; terms used consistently in code, tests, docs, UI, API, DB (maybe). Examples and impact: `Client` vs `Customer`, `Bill` vs `Invoice` vs `Payment`. Methods and events: `Invoice.issued()` not `set_status(2)`. Code as language. How maintain: glossary, pair with domain expert, refactor naming, avoid generic names, review PRs for language drift, ADR.

Need maybe include examples:
```php
readonly class Money { ... equals }
final class Order extends Entity { ... }
```
But in previous we discussed entities with Doctrine. Keep concise.

Potential nuance: Entity can have value objects: `User` has `Email`, `Address`. VO can be mutable? Usually immutable; if mutable, equality problems and Doctrine issues. Recommend immutable VOs.

Need discuss equality and hashing in PHP: `equals()` method, not object identity. For entities: identity by ID; value equality by value. In DB: VO embedded or columns; entity rows.

Need answer interview style.

Let's formulate final with headings.

</details>

**Кандидат:**

## Value Object и Entity: чем они отличаются

### Суть разницы

**Entity** — объект, который имеет **идентичность**.  
**Value Object** — объект, который описывает **значение**.

Это не разница в “большой/маленький” или “с полями/без полей”. Это разница в способе, которым домен понимает, что объект “тот же самый”.

---

## 1. Entity: объект с идентичностью

Entity — это “вещь”, которую домен может отличить от другой такой же вещи.

Пример:

```php
final class Customer
{
    public function __construct(
        private CustomerId $id,
        private string $name,
        private Email $email,
    ) {}

    public function id(): CustomerId
    {
        return $this->id;
    }
}
```

Два `Customer` с одинаковыми данными:

```php
$a = new Customer(new CustomerId(1), 'Ivan', new Email('ivan@example.com'));
$b = new Customer(new CustomerId(2), 'Ivan', new Email('ivan@example.com'));
```

Это **разные** клиенты.

Даже если имя и email совпали — они разные, потому что у них разные `id`.

### Ключевые признаки Entity

| Признак | Пояснение |
|---------|-----------|
| Идентичность важнее значений | Два объекта с одинаковыми полями могут быть разными |
| Сравнение по `id` | `a === b` по смыслу, а не по всем полям |
| Изменяется со временем | Сохраняя ту же идентичность |
| Имеет жизненный цикл | создан, изменён, удалён |
| На него ссылаются другие объекты | обычно по `id` |
| Часто является корнем агрегата или частью агрегата | `Order`, `OrderLine`, `User` |

### Пример: `Order`

```php
final class Order
{
    public function __construct(
        private OrderId $id,
        private string $number,
        private OrderStatus $status,
        private Collection $lines,
    ) {}
}
```

Два заказа:

```php
$orderA = new Order(new OrderId(1), 'ORD-1001', OrderStatus::Draft, ...);
$orderB = new Order(new OrderId(2), 'ORD-1002', OrderStatus::Draft, ...);
```

Это разные заказы, даже если содержимое одинаковое.

---

## 2. Value Object: объект со значением

Value Object — это объект, который описывает атрибут или значение.

Он “тот же самый”, если равны его значения.

Пример:

```php
final class Money
{
    private function __construct(
        private int $amountCents,
        private string $currency,
    ) {}

    public static function of(int $amountCents, string $currency): self
    {
        if ($amountCents < 0) {
            throw new \ValueError('Amount cannot be negative.');
        }

        return new self($amountCents, strtoupper($currency));
    }

    public function amountCents(): int
    {
        return $this->amountCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }
}
```

Два `Money`:

```php
$a = Money::of(10000, 'USD');
$b = Money::of(10000, 'USD');
```

Это **одна и та же ценность**: 10000 USD.

Не важно, что это два разных PHP-объекта.

### Ключевые признаки Value Object

| Признак | Пояснение |
|---------|-----------|
| Нет собственной идентичности | Не нужно знать `id`, чтобы понять, что это |
| Сравнение по значениям | Равны все поля — равны объекты |
| Обычно неизменяемый | `readonly`, `with*`, `add()` возвращает новый объект |
| Не имеет сложного жизненного цикла | Создан — существует — сравнивается |
| Описывает атрибут | деньги, адрес, email, диапазон, цвет |
| Часто передаётся как параметр | `charge(Money $amount)`, `notify(Email $email)` |

---

## 3. Главное различие: “кто ты?”

### Entity отвечает на вопрос:

> “Какой это объект?”

```php
Customer(id: 1)
Order(id: 42)
Payment(id: 777)
```

### Value Object отвечает на вопрос:

> “Какое у объекта значение?”

```php
Money(10000, USD)
Email(ivan@example.com)
Address(street: "Lenina 1", city: "Kazan")
Period(from: 2024-01-01, to: 2024-01-31)
```

---

## 4. Почему это важно

Если перепутать Entity и Value Object, код начинает врать.

### Плохо: Money как Entity

```php
class Money
{
    public int $id;
    public int $amountCents;
    public string $currency;
}
```

Вопросы, которые появляются:

- Какого типа `id`?
- Что значит `moneyA === moneyB`?
- Можно ли два разных `Money` с одинаковой суммой считать равными?
- Где его хранить — в отдельной таблице?
- Как его обновлять?
- Что значит “изменить деньги”?

На самом деле деньги — не сущность. Это ценность.

### Хорошо: Money как Value Object

```php
readonly class Money
{
    public function __construct(
        private int $amountCents,
        private string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Currency mismatch.');
        }

        return new self($this->amountCents + $other->amountCents, $this->currency);
    }
}
```

Теперь:

```php
$payment->amount() instanceof Money;
$order->total() instanceof Money;
```

Это просто значение.

---

## 5. Как я решаю: Entity или Value Object

### Вопрос 1: “Имеет ли объект идентичность?”

Если да — Entity.

Пример:

```php
Order(id: 42)
```

Два заказа с одинаковыми позициями — разные.

```php
Money(100, USD)
```

Два объекта с одинаковой суммой — то же значение.

---

### Вопрос 2: “Одинаковые значения = одинаковый объект?”

Если да — Value Object.

```php
Email(a@b.com) === Email(a@b.com)
```

По смыслу — да.

```php
User(id: 1) === User(id: 2)
```

Нет, даже если email одинаковый.

---

### Вопрос 3: “Объект имеет историю?”

Если да — скорее Entity.

Пример:

```php
Order:
    draft → paid → shipped → delivered
```

Заказ имеет историю и изменения.

```php
Money:
    100 USD → 200 USD
```

Лучше создавать новый `Money(200, USD)`, а не изменять старый.

---

### Вопрос 4: “Другие объекты ссылаются на него как на отдельное понятие?”

Если да — Entity.

Пример:

```php
Payment->orderId
Shipment->orderId
Invoice->orderId
```

`Order` — самостоятельная сущность.

```php
Order->total
Order->customerEmail
```

`Money` и `Email` — не самостоятельные сущности, они часть описания.

---

### Вопрос 5: “Изменяется ли объект, сохраняя идентичность?”

Если да — Entity.

```php
$user->changeEmail('new@example.com');
// тот же User, другой email
```

```php
$order->addLine(...);
// тот же Order, новый total
```

Для VO лучше не менять, а создать новое значение:

```php
$address = new Address('Kazan', 'Lenina 1');
$newAddress = new Address('Kazan', 'Tatneft 10');
```

---

### Вопрос 6: “Есть ли у объекта статус, правила перехода, lifecycle?”

Если да — обычно Entity.

Примеры:

```php
Order: draft, paid, shipped, cancelled
Payment: pending, charged, refunded
Shipment: created, shipped, delivered
```

Value Object обычно не имеет таких статусов.

Может быть валидное состояние:

```php
Money(100, USD)
```

Но не жизненный цикл:

```php
Money: created → modified → archived
```

Если так — возможно, это не VO, а сущность.

---

## 6. Примеры: что Entity, а что Value Object

### `User` — Entity

```php
final class User
{
    public function __construct(
        private UserId $id,
        private Email $email,
        private string $name,
        private string $status,
    ) {}
}
```

Почему Entity:

- имеет `id`;
- может менять email;
- имеет статус;
- на него ссылаются `Order`, `Payment`;
- два объекта с одинаковым email — разные пользователи.

`Email` внутри — Value Object.

---

### `Order` — Entity

```php
final class Order
{
    public function __construct(
        private OrderId $id,
        private string $number,
        private Money $total,
        private Collection $lines,
        private OrderStatus $status,
    ) {}
}
```

Почему Entity:

- имеет `id`;
- имеет `number`;
- имеет статус;
- может меняться;
- на него ссылаются `Payment`, `Shipment`.

`Money`, `Email`, `Address` внутри — Value Objects.

---

### `OrderLine` — обычно Entity внутри агрегата

```php
final class OrderLine
{
    public function __construct(
        private OrderLineId $id,
        private ProductId $productId,
        private int $quantity,
        private Money $unitPrice,
    ) {}
}
```

Почему обычно Entity:

- имеет `id`;
- может изменять количество;
- может быть удалён из агрегата;
- нужен для маппинга в таблицу;
- должен быть отличим от другой строки.

Но иногда `OrderLine` можно делать Value Object, если:

- у него нет собственного `id`;
- он никогда не изменяется;
- он создаётся заново при каждом изменении заказа;
- домен не оперирует им как отдельной строкой.

В production чаще всего `OrderLine` — Entity, потому что это отдельная запись в таблице.

---

### `Money` — Value Object

```php
readonly class Money
{
    private int $amountCents;
    private string $currency;
}
```

Почему VO:

- нет `id`;
- 100 USD — это 100 USD;
- используется как атрибут;
- сравнение по значениям;
- должен быть валидным всегда.

---

### `Address` — Value Object

```php
readonly class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $postalCode,
        public string $country,
    ) {}
}
```

Почему VO:

- не имеет `id`;
- “тот же адрес” определяется значениями;
- используется как атрибут клиента или заказа;
- не имеет жизненного цикла.

Но если домен говорит:

> “Адрес — это отдельный объект, у него есть история, версия, подтверждение”

тогда он может стать Entity.

Например:

```php
VerifiedAddress(id: 1, customer_id: 5, ...)
```

Тогда это уже сущность, потому что имеет идентичность и доверие/верификацию.

---

### `Email` — Value Object

```php
final class Email
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email.');
        }

        return new self(mb_strtolower($value));
    }
}
```

Почему VO:

- `a@b.com` — это значение;
- валидация;
- нет `id`;
- нет lifecycle.

---

### `Customer` vs `Client` vs `User`

Это уже вопрос Ubiquitous Language.

В домене нужно выбрать **один** термин:

| Термин | Когда |
|--------|-------|
| `User` | Аутентифицированный субъект: логин, пароль, роль |
| `Customer` | Клиент, который делает заказы |
| `Client` | В некоторых доменах — сторонняя организация |
| `Buyer` | Конкретный покупатель в заказе |

Они могут быть разными сущностями в разных bounded contexts.

Например:

```text
Identity context: User
Sales context: Customer
Payment context: Payer
Order context: Buyer
```

Но внутри одного bounded context лучше не путать.

---

## 7. Entity может содержать Value Objects

Это нормальная структура.

```php
final class Customer
{
    public function __construct(
        private CustomerId $id,
        private Email $email,
        private Name $name,
        private Address $address,
        private string $status,
    ) {}
}
```

Здесь:

- `Customer` — Entity;
- `Email` — Value Object;
- `Name` — Value Object;
- `Address` — Value Object.

Это значит:

> Entity — это самостоятельный объект с идентичностью.  
> Value Object — это его богатое, валидное описание.

---

## 8. Value Object может быть частью Entity, но не обязательно таблицей

Пример:

```php
final class Order
{
    public function __construct(
        private OrderId $id,
        private Money $total,
        private Currency $currency,
    ) {}
}
```

В БД:

```sql
orders (
    id,
    number,
    total_cents,
    currency,
    status
)
```

`Money` не обязан быть отдельной таблицей. Он может быть колонками.

---

## 9. Типичные ошибки

### Ошибка 1: Entity без идентичности

```php
class Money
{
    public int $id;
    public int $amount;
    public string $currency;
}
```

Если “Money” не нужно отличать от другого “Money” — не Entity.

---

### Ошибка 2: Value Object с `id`

```php
class Address
{
    public int $id;
    public string $street;
    public string $city;
}
```

Если адрес — только значение, `id` лишний.

Но если адрес — верифицированный объект с историей, `id` может быть нужен.

---

### Ошибка 3: Мутабельный Value Object

```php
class Money
{
    public int $amount;
    public string $currency;

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
```

Проблемы:

- потеряется валидность;
- сложно сравнивать;
- трудно тестировать;
- легко создать невалидное состояние.

Лучше:

```php
final class Money
{
    private int $amountCents;
    private string $currency;

    public function add(self $other): self
    {
        return new self($this->amountCents + $other->amountCents, $this->currency);
    }
}
```

---

### Ошибка 4: Все объекты — Entity

```php
Entity User
Entity Email
Entity Address
Entity Money
Entity Phone
```

Если всё “entity”, модель становится тяжёлой и неудобной.

Многие концепции домена — не сущности, а значения.

---

### Ошибка 5: Все объекты — Value Object

```php
Value Order
Value Payment
Value Customer
Value Invoice
```

Если всё “value”, теряется идентичность, история, связи, агрегаты.

---

## 10. Прагматичный чек-лист

### Скорее Value Object, если:

- описывает атрибут;
- нет собственной истории;
- нет собственного `id`;
- сравнивается по значениям;
- должен быть валидным всегда;
- легко передаётся как параметр;
- не требует отдельных транзакций;
- не имеет сложного lifecycle.

Примеры:

```php
Money
Email
Phone
Address
Period
Slug
Percentage
GeoLocation
```

### Скорее Entity, если:

- имеет `id` или business key;
- сравнивается по идентичности;
- имеет историю;
- имеет статус или lifecycle;
- на него ссылаются другие объекты;
- его изменения должны быть отслеживаемы;
- он является корнем агрегата или важным членом агрегата.

Примеры:

```php
User
Customer
Order
OrderLine
Payment
Invoice
Product
Shipment
```

---

## 11. Ubiquitous Language: что это

**Ubiquitous Language** — единый язык, на котором доменные эксперты и разработчики говорят об одних и тех же вещах.

Этот же язык используется:

- в требованиях;
- в доменной модели;
- в названиях классов;
- в методах;
- в событиях;
- в API;
- в тестах;
- в логировании;
- в интерфейсе.

Цель: когда разработчик читает код, он должен понимать бизнес-термины без словаря.

---

## 12. Пример Ubiquitous Language

Плохой код:

```php
class Record
{
    public function setField1($value1): void {}

    public function doWork(): void {}

    public function process2(): int {}
}
```

Вопросы:

- Что это за `Record`?
- Что такое `field1`?
- Что делает `doWork`?
- Что значит `process2`?

Хороший код:

```php
final class Order
{
    public function addLine(AddOrderLine $command): void {}

    public function confirm(): void {}

    public function cancel(CancelOrder $command): void {}
}
```

Теперь видно язык:

- `Order` — заказ;
- `Line` — позиция;
- `confirm` — подтвердить;
- `cancel` — отменить.

---

## 13. Как Ubiquitous Language влияет на код

### 1. Имена классов

Плохо:

```php
class Data {}
class Info {}
class Record {}
class Item {}
```

Хорошо:

```php
class Customer {}
class Order {}
class Payment {}
class Invoice {}
```

---

### 2. Имена методов

Плохо:

```php
$user->setRole(2);
$order->setField3('x');
$payment->process(1);
```

Хорошо:

```php
$user->revokeEditorRole();
$order->markAsConfirmed();
$payment->markAsCharged('gateway_123');
```

---

### 3. События

Плохо:

```php
Event: RecordChanged
Event: DataUpdated
Event: ProcessFinished
```

Хорошо:

```php
OrderConfirmed
PaymentCharged
CustomerEmailChanged
InvoiceIssued
```

События должны быть сформулированы как факты на языке домена.

---

### 4. API

Плохо:

```http
POST /items/create
GET /records/list
PUT /data/update
```

Хорошо:

```http
POST /orders
GET /customers
POST /orders/{orderId}/confirm
POST /payments/{paymentId}/charge
```

---

### 5. Тесты

Плохо:

```php
test('check process 1')
{
    $this->assertEquals(1, $result->value);
}
```

Хорошо:

```php
test('confirmed order cannot be cancelled')
{
    $order = Order::draft();
    $order->confirm();

    $this->expectException(OrderCannotBeCancelled::class);
    $order->cancel();
}
```

Тесты тоже должны быть на языке домена.

---

## 14. Ubiquitous Language и bounded context

Единый язык работает **в пределах bounded context**.

Один и тот же термин в разных контекстах может означать разное.

Пример:

```text
E-commerce context:
    Order — заказ клиента

Warehouse context:
    Order — задание на сборку

Finance context:
    Order — источник данных для счёта
```

Это нормально.

Важно, чтобы внутри каждого контекста термин был однозначен.

---

## 15. Как выстраивать Ubiquitous Language

### Шаг 1: Собирать термины с доменом

Вопросы:

- Как вы называете “клиент”?
- “Пользователь” или “клиент”?
- “Заказ” или “заявка”?
- “Оплата” или “платёж”?
- “Счёт” или “инвойс”?
- “Доставка” или “отгрузка”?

---

### Шаг 2: Зафиксировать глоссарий

Например:

| Термин | Определение | Не путать с |
|--------|-------------|-------------|
| Customer | Клиент, который делает заказы | User, Buyer |
| Order | Запрос клиента на покупку товаров | Invoice, Payment |
| OrderLine | Позиция внутри заказа | Product, SKU |
| Payment | Факт или попытка списания денег | Charge, Refund |
| Invoice | Документ на оплату | Receipt |
| Shipment | Процесс отгрузки товара | Order, Delivery |

---

### Шаг 3: Перевести в код

Было:

```php
class Client {}
class Order {}
class Pay {}
class Ship {}
```

Стало:

```php
final class Customer {}
final class Order {}
final class Payment {}
final class Shipment {}
```

---

### Шаг 4: Убрать синонимы

Плохо, когда в одном проекте есть:

```php
client
customer
buyer
payer
user
account
```

и непонятно, что между ними общего.

Нужно решить:

- это одна сущность?
- это разные сущности?
- это разные bounded contexts?
- есть ли связь между ними?

---

### Шаг 5: Использовать термины в событиях

```php
CustomerCreated
OrderPlaced
OrderConfirmed
PaymentCharged
ShipmentCreated
```

Не:

```php
NewRecord
ProcessDone
DataReady
```

---

## 16. Пример доменной модели с языком

```php
final class Customer
{
    public function __construct(
        private CustomerId $id,
        private Name $name,
        private Email $email,
    ) {}
}

final class Order
{
    public function __construct(
        private OrderId $id,
        private CustomerId $customerId,
        private OrderLineCollection $lines,
        private Money $total,
        private OrderStatus $status,
    ) {}

    public function confirm(): void {}

    public function cancel(CancelReason $reason): void {}
}

final class OrderLine
{
    public function __construct(
        private OrderLineId $id,
        private ProductId $productId,
        private Quantity $quantity,
        private Money $unitPrice,
    ) {}
}

final class Payment
{
    public function __construct(
        private PaymentId $id,
        private OrderId $orderId,
        private Money $amount,
        private PaymentStatus $status,
    ) {}

    public function markAsCharged(GatewayReference $reference): void {}
}
```

Здесь язык:

- `Customer`
- `Order`
- `OrderLine`
- `Quantity`
- `Money`
- `Payment`
- `GatewayReference`

Это уже читаемый домен.

---

## 17. Итог

### Entity

Объект с идентичностью.

Отвечает на вопрос:

> “Какой это объект?”

Примеры:

```php
User
Customer
Order
Payment
Invoice
Product
```

Признаки:

- `id`;
- история;
- статус;
- изменения с сохранением идентичности;
- ссылки других объектов.

---

### Value Object

Объект со значением.

Отвечает на вопрос:

> “Какое у него значение?”

Примеры:

```php
Money
Email
Phone
Address
Quantity
Period
```

Признаки:

- нет `id`;
- сравнение по полям;
- обычно immutable;
- валидность при создании;
- описывает атрибут.

---

### Ubiquitous Language

Единый язык домена, который одинаково используется в требованиях, коде, API, тестах и интерфейсе.

Он влияет на код напрямую:

- имена классов;
- имена методов;
- события;
- команды;
- API;
- тесты;
- логи;
- доменные правила.

Хороший код на Ubiquitous Language читается как язык бизнеса:

```php
$order->confirm();
$payment->markAsCharged();
$customer->changeEmail();
```

Плохой код читается как инфраструктура:

```php
$record->setField1();
$data->process2();
$service->doWork();
```

Самый простой критерий:

> Если бизнес-эксперт может прочитать название метода и понять, что происходит, — язык хороший.  
> Если нужна расшифровка, — доменная модель не донесена.

> ⏱ 63.3s · prompt 72397 tok · output 7166 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

<details><summary>Ход мысли модели</summary>

Пользователь просит описать структуру PHP-приложения по принципам чистой архитектуры: слои, что в каждом, правило зависимостей, куда попадают Doctrine/Symfony-контроллер/обработчик очереди, и как следить за границами.

Контекст: собеседование на PHP-бэкенд. Я уже рассказывал про DDD, SOLID, Doctrine, события, транзакции. Нужно дать конкретную, приземлённую структуру — не абстрактную "Clean Architecture" из статьи, а то, что реально работает в Symfony-приложении.

Что важно:
1. Честно сказать, что "чистая архитектура" — это спектр, не одна правильная структура
2. Показать конкретный tree проекта
3. Правило зависимостей: внешние слои зависят от внутренних, не наоборот
4. Doctrine — инфраструктура, Symfony контроллер — presentation, handler — application
5. Как следить: PHPStan rules, Архитектурные тесты (ArchUnit-подобные), CompilerPass, code review
6. Не идеализировать: показать, где границы могут просесть и как это ловить

Структура:
- Domain (ядро): сущности, VO, интерфейсы (port)
- Application (use cases): оркестрация, команды, события
- Infrastructure: Doctrine, Mailer, Search, External API
- Presentation/Interface: Symfony controllers, API, CLI, queue handlers

Правило: зависимости только внутрь. Domain не знает про Doctrine, Symfony, Redis.

Покажу конкретный tree, примеры кода, и практические инструменты контроля (PHPStan, ArchUnit-подобные правила, DI-конфигурация).

Отвечу на русском, как и весь диалог.

</details>

**Кандидат:**

## Чистая архитектура в Symfony-приложении

### Введение: что я понимаю под "чистой архитектурой"

Чистая архитектура — не одна структура. Это **правило**: зависимости направлены внутрь. Внешние слои зависят от внутренних, а не наоборот.

```text
Правило: стрелки указывают внутрь

┌──────────────────────────────────────────────┐
│  Presentation (Controller, CLI, Queue)       │
│  ┌──────────────────────────────────────────┐│
│  │  Application (Use Cases, Commands)       ││
│  │  ┌──────────────────────────────────────┐││
│  │  │  Domain (Entities, VO, Rules)        │││
│  │  └──────────────────────────────────────┘││
│  └──────────────────────────────────────────┘│
│  ┌──────────────────────────────────────────┐│
│  │  Infrastructure (Doctrine, Mail, Search) ││
│  └──────────────────────────────────────────┘│
└──────────────────────────────────────────────┘
```

Внутреннее не знает про внешнее.

- Domain не знает про Doctrine.
- Domain не знает про Symfony.
- Domain не знает про RabbitMQ.
- Application знает про Domain.
- Infrastructure реализует интерфейсы, которые объявлены в Domain/Application.

В PHP это достигается через **интерфейсы (ports)** и **реализации (adapters)**.

---

### Структура проекта

```
src/
├── Domain/                          # Ядро. Не зависит НИ ОТ ЧЕГО.
│   ├── Order/
│   │   ├── Order.php                # Сущность / агрегат
│   │   ├── OrderLine.php            # Дочерняя сущность
│   │   ├── OrderId.php              # VO: идентификатор
│   │   ├── OrderStatus.php          # VO: статус (enum)
│   │   ├── OrderRepository.php      # Интерфейс (port)
│   │   ├── OrderEvent.php           # Доменные события
│   │   ├── EventDispatcher.php      # Интерфейс (port)
│   │   ├── Money.php                # VO
│   │   ├── Quantity.php             # VO
│   │   ├── OrderNotFound.php        # Доменное исключение
│   │   └── InvalidTransition.php    # Доменное исключение
│   ├── Customer/
│   │   ├── Customer.php
│   │   ├── CustomerId.php
│   │   ├── Email.php
│   │   ├── CustomerRepository.php
│   │   └── ...
│   └── Payment/
│       ├── Payment.php
│       ├── PaymentId.php
│       ├── PaymentStatus.php
│       ├── PaymentGateway.php       # Интерфейс (port)
│       └── PaymentRepository.php
│
├── Application/                     # Use cases. Зависит от Domain.
│   ├── Order/
│   │   ├── CreateOrder.php          # Command
│   │   ├── CreateOrderHandler.php   # Use case
│   │   ├── ConfirmOrder.php
│   │   ├── ConfirmOrderHandler.php
│   │   ├── CancelOrder.php
│   │   └── CancelOrderHandler.php
│   ├── Customer/
│   │   ├── RegisterCustomer.php
│   │   └── RegisterCustomerHandler.php
│   ├── Payment/
│   │   ├── ChargePayment.php
│   │   └── ChargePaymentHandler.php
│   └── Shared/
│       ├── Command.php              # Base interface
│       ├── CommandHandler.php
│       └── IdGenerator.php          # Интерфейс (port)
│
├── Infrastructure/                  # Адаптеры. Зависит от Domain + Application.
│   ├── Doctrine/
│   │   ├── Order/
│   │   │   ├── OrderEntity.php      # Doctrine Entity (обёртка)
│   │   │   ├── OrderLineEntity.php
│   │   │   ├── OrderRepository.php  # Реализация OrderRepository (port)
│   │   │   └── OrderMapper.php      # Domain ↔ Entity
│   │   ├── Customer/
│   │   │   ├── CustomerEntity.php
│   │   │   ├── CustomerRepository.php
│   │   │   └── CustomerMapper.php
│   │   ├── Payment/
│   │   │   ├── PaymentEntity.php
│   │   │   └── PaymentRepository.php
│   │   └── EntityManager.php        # Access wrapper
│   ├── Mail/
│   │   ├── Mailer.php               # Интерфейс (port, если в Domain)
│   │   ├── SymfonyMailer.php        # Реализация через Symfony Mailer
│   │   └── EmailMessage.php
│   ├── Search/
│   │   ├── SearchIndex.php          # Интерфейс (port)
│   │   ├── MeilisearchIndex.php     # Реализация
│   │   └── ElasticsearchIndex.php   # Альтернативная реализация
│   ├── External/
│   │   ├── StripeGateway.php        # Реализация PaymentGateway
│   │   └── AdyenGateway.php
│   ├── Queue/
│   │   ├── RabbitMqPublisher.php    # Реализация EventPublisher
│   │   └── RedisPublisher.php
│   └── Clock/
│       └── SystemClock.php          # Реализация Clock (port)
│
├── Presentation/                    # Входные точки. Зависит от Application.
│   ├── Http/
│   │   ├── Order/
│   │   │   ├── CreateOrderController.php
│   │   │   ├── ConfirmOrderController.php
│   │   │   └── OrderDTO.php         # DTO для API
│   │   ├── Customer/
│   │   │   └── RegisterCustomerController.php
│   │   └── Payment/
│   │       └── ChargePaymentController.php
│   ├── Cli/
│   │   ├── ReindexCustomersCommand.php
│   │   └── ProcessFailedPaymentsCommand.php
│   └── Queue/
│       ├── SendRegistrationEmailHandler.php   # Обработчик сообщения
│       ├── UpdateSearchIndexHandler.php
│       └── NotifyExternalSystemHandler.php
│
└── Kernel.php                       # Композиция корня: связывает всё
```

---

### Что лежит в каждом слое

#### Domain

**Что есть:**
- Сущности, Value Objects, агрегаты.
- Доменные правила и инварианты.
- Доменные события.
- Интерфейсы (ports), которые **домен** требует от инфраструктуры.
- Доменные исключения.

**Чего НЕТ:**
- Никакого `use Symfony\...`
- Никакого `use Doctrine\...`
- Никакого `use App\Infrastructure\...`
- Никакой конфигурации.
- Никаких side-effects (запись в БД, отправка email).

**Пример:**

```php
// src/Domain/Order/Order.php
namespace App\Domain\Order;

final class Order
{
    public function __construct(
        private OrderId $id,
        private CustomerId $customerId,
        private OrderStatus $status,
        /** @var OrderLine[] */
        private array $lines = [],
    ) {}

    public function addLine(ProductId $productId, Quantity $qty, Money $unitPrice): void
    {
        if ($this->status !== OrderStatus::Draft) {
            throw new InvalidTransition($this->status, OrderStatus::Draft);
        }

        $this->lines[] = new OrderLine($productId, $qty, $unitPrice);
    }

    public function confirm(): void
    {
        if ($this->lines === []) {
            throw new EmptyOrder($this->id);
        }

        $this->status = OrderStatus::Confirmed;
    }

    public function total(): Money
    {
        $total = Money::zero($this->lines[0]->unitPrice()->currency());
        foreach ($this->lines as $line) {
            $total = $total->add($line->amount());
        }
        return $total;
    }
}
```

Здесь **нет** `EntityManager`, **нет** `flush()`, **нет** `Repository` как класса. Только чистая логика.

**Port:**

```php
// src/Domain/Order/OrderRepository.php
namespace App\Domain\Order;

interface OrderRepository
{
    public function save(Order $order): void;
    public function byId(OrderId $id): Order;
    public function byNumber(string $number): Order;
}
```

Это **контракт**. Domain говорит: "мне нужен способ сохранить и найти заказ". Как именно — не моё дело.

---

#### Application

**Что есть:**
- Use cases (команды + обработчики).
- Оркестрация: загрузить агрегат → изменить → сохранить → опубликовать событие.
- Транзакционная граница.
- Команды (DTO для входа).

**Чего НЕТ:**
- Business rules (они в Domain).
- Конкретная инфраструктура (они в Infrastructure).
- HTTP-детали (они в Presentation).

**Пример:**

```php
// src/Application/Order/CreateOrderHandler.php
namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository;
use App\Domain\Order\OrderEvent;
use App\Domain\Order\EventDispatcher;
use App\Domain\Customer\CustomerRepository;

final class CreateOrderHandler
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly CustomerRepository $customers,
        private readonly EventDispatcher $events,
    ) {}

    public function handle(CreateOrder $command): OrderId
    {
        $customer = $this->customers->byId($command->customerId);

        $order = new Order(
            id: $command->id,
            customerId: $customer->id(),
            status: \App\Domain\Order\OrderStatus::Draft,
        );

        foreach ($command->lines as $line) {
            $order->addLine($line->productId, $line->quantity, $line->unitPrice);
        }

        $this->orders->save($order);
        $this->events->dispatch(new OrderEvent::Created($order->id()));

        return $order->id();
    }
}
```

Здесь:
- Нет `EntityManager`.
- Нет `flush()` — `OrderRepository::save()` сам решает.
- Нет `Symfony`-контроллера.
- Только оркестрация доменных объектов.

**Команда:**

```php
// src/Application/Order/CreateOrder.php
namespace App\Application\Order;

use App\Domain\Order\Money;
use App\Domain\Order\Quantity;
use App\Domain\Order\OrderId;
use App\Domain\Customer\CustomerId;

final class CreateOrder
{
    /** @var CreateOrderLine[] */
    public array $lines;

    public function __construct(
        public readonly OrderId $id,
        public readonly CustomerId $customerId,
        array $lines,
    ) {
        $this->lines = $lines;
    }
}
```

Команда — это **входной DTO** для use case. Она не знает про HTTP, JSON, form.

---

#### Infrastructure

**Что есть:**
- Реализации портов (репозитории, mailer, search, external API).
- Doctrine-сущности (обёртки).
- Мапперы (Domain ↔ Entity).
- Конфигурация.

**Пример: Repository реализация**

```php
// src/Infrastructure/Doctrine/Order/OrderRepository.php
namespace App\Infrastructure\Doctrine\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository as OrderRepositoryPort;
use App\Domain\Order\OrderId;
use Doctrine\ORM\EntityManagerInterface;

final class OrderRepository implements OrderRepositoryPort
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderMapper $mapper,
    ) {}

    public function save(Order $order): void
    {
        $entity = $this->mapper->toEntity($order);
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function byId(OrderId $id): Order
    {
        $entity = $this->em->getRepository(OrderEntity::class)->find((int) $id->value());

        if ($entity === null) {
            throw new \App\Domain\Order\OrderNotFound($id);
        }

        return $this->mapper->fromEntity($entity);
    }
}
```

**Маппер:**

```php
// src/Infrastructure/Doctrine/Order/OrderMapper.php
namespace App\Infrastructure\Doctrine\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\Money;
use App\Domain\Order\OrderStatus;

final class OrderMapper
{
    public function toEntity(Order $domain): OrderEntity
    {
        $entity = new OrderEntity();
        $entity->setId((int) $domain->id()->value());
        $entity->setCustomerId((int) $domain->customerId()->value());
        $entity->setStatus($domain->status()->value);

        foreach ($domain->lines() as $line) {
            $lineEntity = new OrderLineEntity();
            $lineEntity->setProductId((int) $line->productId()->value());
            $lineEntity->setQuantity($line->quantity()->value());
            $lineEntity->setUnitPriceCents($line->unitPrice()->amountCents());
            $entity->getLines()->add($lineEntity);
        }

        return $entity;
    }

    public function fromEntity(OrderEntity $entity): Order
    {
        $lines = [];
        foreach ($entity->getLines() as $lineEntity) {
            $lines[] = new OrderLine(
                productId: \App\Domain\Order\ProductId::fromString((string) $lineEntity->getProductId()),
                quantity: \App\Domain\Order\Quantity::fromInt($lineEntity->getQuantity()),
                unitPrice: Money::fromCents($lineEntity->getUnitPriceCents(), 'USD'),
            );
        }

        return new Order(
            id: \App\Domain\Order\Order::newId(),  // из entity
            customerId: \App\Domain\Customer\CustomerId::fromInt($entity->getCustomerId()),
            status: OrderStatus::fromString($entity->getStatus()),
            lines: $lines,
        );
    }
}
```

**Почему маппер, а не Entity как Domain?**

Потому что:
1. Doctrine Entity мутабельна, Domain — иммутабелен.
2. Doctrine Entity знает про `id`, `createdAt`, `updatedAt` — технические поля.
3. Domain не знает про `OneToMany`, `cascade`, `orphanRemoval`.
4. Можно сменить ORM без изменения Domain.
5. Тестирование Domain не требует Doctrine.

---

#### Presentation

**Что есть:**
- Symfony-контроллеры.
- CLI-команды.
- Обработчики очередей.
- DTO для запроса/ответа.
- Валидация входных данных.

**Пример: Controller**

```php
// src/Presentation/Http/Order/CreateOrderController.php
namespace App\Presentation\Http\Order;

use App\Application\Order\CreateOrder;
use App\Application\Order\CreateOrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class CreateOrderController extends AbstractController
{
    public function __invoke(
        Request $request,
        CreateOrderHandler $handler,
    ): JsonResponse {
        /** @var CreateOrderDto $dto */
        $dto = $request->getJsonPayload();

        $command = CreateOrder::fromDto($dto);

        $orderId = $handler->handle($command);

        return $this->json(['orderId' => $orderId->value()], 201);
    }
}
```

**Пример: Queue Handler**

```php
// src/Presentation/Queue/SendRegistrationEmailHandler.php
namespace App\Presentation\Queue;

use App\Domain\Customer\CustomerRepository;
use App\Infrastructure\Mail\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegistrationEmailHandler
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(SendRegistrationEmail $message): void
    {
        $customer = $this->customers->byId($message->customerId);

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($customer->email()->value())
            ->subject('Welcome')
            ->text('Your account was created.');

        $this->mailer->send($email);
    }
}
```

**Пример: CLI**

```php
// src/Presentation/Cli/ReindexCustomersCommand.php
namespace App\Presentation\Cli;

use App\Domain\Customer\CustomerRepository;
use App\Infrastructure\Search\SearchIndex;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:reindex-customers')]
final class ReindexCustomersCommand extends Command
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly SearchIndex $search,
    ) {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $customers = $this->customers->all();

        foreach ($customers as $customer) {
            $this->search->upsert('customers', $customer->id()->value(), [
                'name' => $customer->name()->value(),
                'email' => $customer->email()->value(),
            ]);
        }

        $output->writeln('Done: ' . count($customers) . ' customers reindexed.');
        return Command::SUCCESS;
    }
}
```

---

### Правило зависимостей

```text
Presentation → Application → Domain
     ↓               ↓          ↑
  Infrastructure ─────┘          │
     (реализует ports)───────────┘
```

| Слой | Может зависеть от | НЕ может зависеть от |
|------|-------------------|----------------------|
| Domain | Стандартная библиотека PHP | Symfony, Doctrine, App\* |
| Application | Domain | Symfony, Doctrine, App\Infrastructure, App\Presentation |
| Infrastructure | Domain (ports), Application (если нужно) | App\Presentation |
| Presentation | Application, Domain (VO для DTO) | App\Infrastructure (напрямую) |

**Критическое правило:** Domain не импортирует ничего из `App\*`, `Symfony\*`, `Doctrine\*`.

```php
// src/Domain/Order/Order.php
// ДОПУСТИМО:
namespace App\Domain\Order;
use App\Domain\Customer\CustomerId;  // другой domain-объект

// НЕ ДОПУСТИМО:
use Doctrine\ORM\Entity;             // ✗
use Symfony\Component\Messenger\Message;  // ✗
use App\Infrastructure\Doctrine\Order\OrderEntity;  // ✗
use App\Application\Order\CreateOrder;  // ✗
```

---

### Куда попадают конкретные вещи

| Что | Куда | Почему |
|-----|------|--------|
| `Order` (агрегат) | **Domain** | Бизнес-логика, инварианты |
| `Money` (VO) | **Domain** | Ценность, правила |
| `OrderRepository` (интерфейс) | **Domain** | Порт, который домен требует |
| `OrderRepository` (реализация) | **Infrastructure/Doctrine** | Адаптер |
| `OrderEntity` (Doctrine) | **Infrastructure/Doctrine** | Техническая обёртка |
| `CreateOrder` (команда) | **Application** | Входной DTO для use case |
| `CreateOrderHandler` | **Application** | Оркестрация |
| `CreateOrderController` | **Presentation/Http** | Входная точка |
| `SendRegistrationEmail` (сообщение) | **Application** (или Domain, если доменное событие) | |
| `SendRegistrationEmailHandler` (обработчик) | **Presentation/Queue** | Входная точка |
| `Mailer` (интерфейс) | **Domain** (port) или **Application** | Зависит от того, кто требует |
| `SymfonyMailer` (реализация) | **Infrastructure/Mail** | Адаптер |
| `PaymentGateway` (интерфейс) | **Domain** (port) | Домен требует способ списать |
| `StripeGateway` (реализация) | **Infrastructure/External** | Адаптер |
| `TransactionManager` | **Application** (или Shared) | Оркестрация транзакции |
| `EventDispatcher` (интерфейс) | **Domain** (port) | Домен требует способ опубликовать |
| `RabbitMqPublisher` | **Infrastructure/Queue** | Адаптер |

---

### Компоновка (Composition Root)

Всё связывается в **одном месте** — `Kernel` / DI-контейнер.

```yaml
# config/services.yaml
services:
    # Domain: interfaces (ports)
    App\Domain\Order\OrderRepository: ~
    App\Domain\Order\EventDispatcher: ~
    App\Domain\Customer\CustomerRepository: ~
    App\Domain\Payment\PaymentGateway: ~

    # Application: use cases
    App\Application\Order\CreateOrderHandler:
        arguments:
            $orders: '@App\Domain\Order\OrderRepository'
            $customers: '@App\Domain\Customer\CustomerRepository'
            $events: '@App\Domain\Order\EventDispatcher'

    # Infrastructure: adapters
    App\Infrastructure\Doctrine\Order\OrderRepository:
        arguments:
            $em: '@doctrine.orm.entity_manager'
            $mapper: '@App\Infrastructure\Doctrine\Order\OrderMapper'
        # Реализует port:
        # (через binding или autoconfigure)

    # Presentation: entry points
    App\Presentation\Http\Order\CreateOrderController:
        tags: ['controller.service_subscriber']
        arguments:
            $handler: '@App\Application\Order\CreateOrderHandler'
```

**Ключевой момент:** DI-контейнер — это **composition root**. Он знает про все слои. Остальной код знает только про свои слои.

---

### Как следить, что границы не нарушаются

#### 1. PHPStan / Psalm — правила импорта

```neon
# phpstan.neon
parameters:
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true

# Запрет импорта Doctrine в Domain:
# (PHPStan не умеет напрямую, но можно через ArchUnit-подобные правила)
```

PHPStan сам по себе не проверяет границы слоёв. Нужны дополнительные инструменты.

#### 2. ArchUnit-подобные тесты (PHPUnit)

```php
// tests/Architecture/DomainIsolationTest.php
namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class DomainIsolationTest extends TestCase
{
    public function test_domain_does_not_import_infrastructure(): void
    {
        $this->assertNoImport(
            directory: 'src/Domain',
            forbiddenPrefixes: [
                'App\\Infrastructure\\',
                'Symfony\\',
                'Doctrine\\',
            ],
        );
    }

    public function test_domain_does_not_import_application(): void
    {
        $this->assertNoImport(
            directory: 'src/Domain',
            forbiddenPrefixes: [
                'App\\Application\\',
            ],
        );
    }

    public function test_application_does_not_import_infrastructure(): void
    {
        $this->assertNoImport(
            directory: 'src/Application',
            forbiddenPrefixes: [
                'App\\Infrastructure\\',
                'Symfony\\',
                'Doctrine\\',
            ],
        );
    }

    private function assertNoImport(string $directory, array $forbiddenPrefixes): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        $violations = [];

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') continue;

            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                foreach ($forbiddenPrefixes as $prefix) {
                    if (str_contains($line, 'use ' . $prefix)) {
                        $violations[] = sprintf(
                            '%s: %s',
                            $file->getPathname(),
                            trim($line)
                        );
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Architecture violations:\n" . implode("\n", $violations)
        );
    }
}
```

Этот тест **падает в CI**, если кто-то добавит `use Doctrine\ORM\Entity;` в файл из `src/Domain/`.

#### 3. Symfony CompilerPass

```php
// src/DependencyInjection/ArchitecturePass.php
namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ArchitecturePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if ($class === null) continue;

            // Domain-классы не должны иметь аргументы из Infrastructure
            if (str_starts_with($class, 'App\\Domain\\')) {
                foreach ($definition->getArguments() as $arg) {
                    if (str_starts_with($arg, 'App\\Infrastructure\\')) {
                        throw new \Exception(
                            "Domain class {$class} depends on {$arg}. " .
                            "Domain must not depend on Infrastructure."
                        );
                    }
                }
            }

            // Application-классы не должны иметь аргументы из Presentation
            if (str_starts_with($class, 'App\\Application\\')) {
                foreach ($definition->getArguments() as $arg) {
                    if (str_starts_with($arg, 'App\\Presentation\\')) {
                        throw new \Exception(
                            "Application class {$class} depends on {$arg}. " .
                            "Application must not depend on Presentation."
                        );
                    }
                }
            }
        }
    }
}
```

```php
// src/Kernel.php
protected function registerContainerExtensions(array $extensions): void
{
    parent::registerContainerExtensions($extensions);
}

protected function build(ContainerBuilder $container): void
{
    parent::build($container);
    $container->addCompilerPass(new ArchitecturePass());
}
```

Это ловит нарушения **при компиляции контейнера** — до того, как код запустится.

#### 4. Code Review чек-лист

| Вопрос | Ответ |
|--------|-------|
| Новый класс в `Domain/` импортирует `Symfony`? | Нет. |
| Новый класс в `Domain/` импортирует `Doctrine`? | Нет. |
| Новый класс в `Application/` импортирует `Infrastructure`? | Нет. |
| Новый класс в `Presentation/` вызывает `Doctrine` напрямую? | Нет (через Application). |
| Domain-метод вызывает `flush()`? | Нет. |
| Domain-метод отправляет email? | Нет. |
| Controller содержит бизнес-логику? | Нет (через Handler). |

#### 5. Статический анализ: `arch` (composer package)

```bash
composer require --dev symplify/easy-coding-standard
```

Правило:

```php
// ecs.php
return \Symplify\EasyCodingStandard\ValueObject\Configuration\Configuration::create()
    ->withPaths(['src/Domain'])
    ->withRules([
        // Запрет use-инструкций из других слоёв
    ]);
```

Или кастомное правило PHPStan:

```php
// rules/DomainIsolationRule.php
final class DomainIsolationRule implements Rule
{
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Node\Stmt\Use_) {
            return [];
        }

        $className = $scope->getClassReflection()?->getName();
        if ($className === null || !str_starts_with($className, 'App\\Domain\\')) {
            return [];
        }

        foreach ($node->uses as $use) {
            $name = $use->name->toString();
            if (str_starts_with($name, 'Symfony\\')
                || str_starts_with($name, 'Doctrine\\')
                || str_starts_with($name, 'App\\Infrastructure\\')
                || str_starts_with($name, 'App\\Application\\')
            ) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        'Domain class %s must not import %s',
                        $className,
                        $name
                    ))
                    ->identifier('architecture.domain-isolation')
                    ->build(),
                ];
            }
        }

        return [];
    }

    public function getNodeTypes(): array
    {
        return [Node\Stmt\Use_::class];
    }
}
```

```neon
# phpstan.neon
services:
    - App\Rules\DomainIsolationRule
```

---

### Где границы проседают на практике

| Ситуация | Почему проседает | Как ловить |
|----------|------------------|-----------|
| Controller вызывает `EntityManager` напрямую | "Проще так" | ArchUnit-тест |
| Domain-класс получает `Request` | Кто-то "оптимизировал" | ArchUnit-тест |
| Use case содержит SQL-запрос | "Нужно быстро" | Code review |
| Queue handler содержит бизнес-логику | "Один метод" | ArchUnit-тест + review |
| Domain-сущность знает про `DateTime` (technical) | Нормально (stdlib), но не `Carbon` | Правила |
| Infrastructure-класс знает про Presentation | "Нужно вернуть JSON" | ArchUnit-тест |

---

### Компромиссы

Чистая архитектура — это **не бесплатно**.

| Стоимость | Что |
|-----------|-----|
| Больше файлов | `Order.php` (Domain) + `OrderEntity.php` (Infra) + `OrderMapper.php` + `OrderRepository.php` (Interface) + `OrderRepository.php` (Implementation) |
| Больше кода | Мапперы, порты, адаптеры |
| Медленнее итерации | Нужно создать 5 файлов вместо 1 |
| Труднее отладка | Stack trace длиннее |

**Когда не стоит:**
- Прототип / spike (2–3 дня).
- Внутренний инструмент с 1 разработчиком.
- Система, которая не изменится.

**Когда стоит:**
- Команда 3+ разработчиков.
- Система живёт 2+ года.
- Часто меняются бизнес-правила.
- Несколько контекстов (Order, Customer, Payment).

---

### Итого

```text
┌─────────────────────────────────────────────────────────┐
│  Presentation                                           │
│  Controller, CLI, Queue Handler                         │
│  "Как пользователь вводит/получает данные"              │
├─────────────────────────────────────────────────────────┤
│  Application                                            │
│  Use Cases, Commands, Orchestration                     │
│  "Что делает система с этими данными"                   │
├─────────────────────────────────────────────────────────┤
│  Domain                                                 │
│  Entities, VO, Rules, Ports                             │
│  "Какие правила и инварианты"                           │
├─────────────────────────────────────────────────────────┤
│  Infrastructure                                         │
│  Doctrine, Mail, Search, External API                   │
│  "Как хранить/отправлять/вызывать"                      │
└─────────────────────────────────────────────────────────┘

Зависимости:
  Presentation → Application → Domain
  Infrastructure → Domain (ports)
  Composition Root (DI) → все слои
```

Следить: **ArchUnit-тесты + PHPStan-правила + Code Review**.

Три слоя защиты:
1. Статический анализ (PHPStan) — ловит на этапе lint.
2. Архитектурные тесты (PHPUnit) — ловит на этапе CI.
3. Code Review — ловит до merge.

> ⏱ 61.6s · prompt 78616 tok · output 7909 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

<details><summary>Ход мысли модели</summary>

The user is asking about testing strategy for a Symfony application with PostgreSQL. They want to know:
1. Testing pyramid approach
2. Types of tests
3. What to mock vs what not to mock
4. How to keep tests fast and stable

Let me think about this from a practical perspective, drawing on real Symfony + PostgreSQL testing experience. I should cover:

- Unit tests (domain, value objects, pure logic)
- Integration tests (Doctrine, repositories, with real DB)
- Feature/API tests (full request cycle)
- What to mock: external services (payment gateways, email, search)
- What NOT to mock: Doctrine (use real DB in test env)
- Database strategy: transaction rollback vs separate test DB vs testcontainers
- Speed considerations
- Stability (flaky tests)
- Tooling: PHPUnit, Pest, Symfony BrowserKit, Doctrine test tools
- Practical patterns and anti-patterns

I should be specific with code examples, show real Symfony test patterns, and give concrete trade-offs. Let me structure this clearly.

</details>

**Кандидат:**

## Стратегия тестирования Symfony + PostgreSQL

### Философия

Тесты не "для галочки". Каждый тест должен отвечать на вопрос: **"Что ломается, если я удалю этот тест?"** Если ответ — "ничего" — тест лишний.

---

### Пирамида: что и как много

```
        /  \
       / F  \       Feature tests (API)         — 10–15%
      /------\
     / Integ \     Integration tests (Doctrine)  — 30–40%
    /--------\
   /  Unit   \   Unit tests (Domain, VO, Use)   — 50–60%
  /----------\
```

| Тип | Что тестирует | Сколько | Скорость |
|-----|--------------|---------|----------|
| Unit | Domain, VO, чистая логика | Много | ms |
| Integration | Repository, Doctrine, БД | Средне | 10–50ms |
| Feature | Полный request → response | Мало | 200ms–2s |

---

### Unit-тесты

#### Что тестирую

**Domain-объекты:**

```php
// tests/Unit/Domain/Order/OrderTest.php
final class OrderTest extends TestCase
{
    public function test_cannot_confirm_empty_order(): void
    {
        $order = new Order(
            id: OrderId::fromString('1'),
            customerId: CustomerId::fromString('2'),
            status: OrderStatus::Draft,
            lines: [],
        );

        $this->expectException(EmptyOrder::class);
        $order->confirm();
    }

    public function test_confirmed_order_cannot_be_cancelled(): void
    {
        $order = $this->createDraftOrderWithLines();
        $order->confirm();

        $this->expectException(InvalidTransition::class);
        $order->cancel();
    }

    public function test_total_is_sum_of_lines(): void
    {
        $order = new Order(
            id: OrderId::fromString('1'),
            customerId: CustomerId::fromString('2'),
            status: OrderStatus::Draft,
            lines: [
                new OrderLine(ProductId::fromString('p1'), Quantity::fromInt(2), Money::fromCents(500, 'USD')),
                new OrderLine(ProductId::fromString('p2'), Quantity::fromInt(1), Money::fromCents(300, 'USD')),
            ],
        );

        $this->assertEquals(1300, $order->total()->amountCents());
    }
}
```

**Value Objects:**

```php
// tests/Unit/Domain/Order/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function test_negative_amount_throws(): void
    {
        $this->expectException(\ValueError::class);
        Money::fromCents(-100, 'USD');
    }

    public function test_add_same_currency(): void
    {
        $a = Money::fromCents(1000, 'USD');
        $b = Money::fromCents(500, 'USD');

        $result = $a->add($b);

        $this->assertEquals(1500, $result->amountCents());
    }

    public function test_add_different_currency_throws(): void
    {
        $a = Money::fromCents(1000, 'USD');
        $b = Money::fromCents(500, 'EUR');

        $this->expectException(\DomainException::class);
        $a->add($b);
    }
}
```

**Use cases (с моками портов):**

```php
// tests/Unit/Application/Order/ConfirmOrderHandlerTest.php
final class ConfirmOrderHandlerTest extends TestCase
{
    public function test_confirms_draft_order(): void
    {
        $order = $this->createDraftOrder();

        $orders = $this->createMock(OrderRepositoryPort::class);
        $orders->method('byId')->willReturn($order);

        $events = $this->createMock(EventDispatcher::class);
        $events->expects($this->once())
              ->method('dispatch')
              ->with($this->isInstanceOf(OrderEvent::class));

        $handler = new ConfirmOrderHandler($orders, $events);

        $handler->handle(new ConfirmOrderCommand(OrderId::fromString('1')));

        $this->assertSame(OrderStatus::Confirmed, $order->status());
    }

    public function test_throws_if_order_not_found(): void
    {
        $orders = $this->createMock(OrderRepositoryPort::class);
        $orders->method('byId')->willThrowException(new OrderNotFound(OrderId::fromString('99')));

        $handler = new ConfirmOrderHandler($orders, $this->createMock(EventDispatcher::class));

        $this->expectException(OrderNotFound::class);
        $handler->handle(new ConfirmOrderCommand(OrderId::fromString('99')));
    }
}
```

#### Что НЕ тестирую в Unit

| Что | Почему |
|-----|--------|
| Doctrine-запросы | Это Integration |
| HTTP-ответы | Это Feature |
| Конфигурацию DI | Это Integration / manual |
| Email-отправку | Мокаю `MailerInterface` |
| External API | Мокаю `PaymentGateway` |

---

### Integration-тесты

#### Что тестирую

- Repository + реальная БД.
- Мапперы (Domain ↔ Entity).
- Транзакции (commit/rollback).
- Constraints (UNIQUE, FK, CHECK).
- Cascade / orphanRemoval.
- N+1 (счётчик запросов).

#### Инструменты

```xml
<!-- phpunit.xml -->
<php>
    <env name="APP_ENV" value="test"/>
    <env name="DATABASE_URL" value="postgresql://test:test@localhost:5432/app_test?serverVersion=16&charset=utf8"/>
    <env name="MESSENGER_TRANSPORT_DSN" value="in-memory://"/>
    <env name="MAILER_DSN" value="null://null"/>
</php>
```

#### База: AbstractIntegrationTest

```php
// tests/Integration/AbstractIntegrationTest.php
namespace Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
    }

    protected function wrapInTransaction(callable $fn): void
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $fn();
            $this->em->getConnection()->rollBack();
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    protected function flush(): void
    {
        $this->em->flush();
    }

    protected function clear(): void
    {
        $this->em->clear();
    }
}
```

#### Пример: Repository test

```php
// tests/Integration/Doctrine/Order/OrderRepositoryTest.php
namespace Tests\Integration\Doctrine\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\Money;
use App\Domain\Order\Quantity;
use App\Domain\Order\ProductId;
use App\Domain\Order\OrderId;
use App\Domain\Customer\CustomerId;
use App\Infrastructure\Doctrine\Order\OrderRepository;
use Tests\Integration\AbstractIntegrationTest;

final class OrderRepositoryTest extends AbstractIntegrationTest
{
    private OrderRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = static::getContainer()->get(OrderRepository::class);
    }

    public function test_save_and_find_order(): void
    {
        $this->wrapInTransaction(function (): void {
            $order = new Order(
                id: OrderId::fromString('order_1'),
                customerId: CustomerId::fromString('cust_1'),
                status: OrderStatus::Draft,
                lines: [
                    new OrderLine(
                        productId: ProductId::fromString('prod_1'),
                        quantity: Quantity::fromInt(2),
                        unitPrice: Money::fromCents(1500, 'USD'),
                    ),
                ],
            );

            $this->repo->save($order);

            $loaded = $this->repo->byId(OrderId::fromString('order_1'));

            $this->assertSame('order_1', $loaded->id()->value());
            $this->assertCount(1, $loaded->lines());
            $this->assertSame(1500, $loaded->lines()[0]->unitPrice()->amountCents());
        });
    }

    public function test_orphan_removal_deletes_lines(): void
    {
        $this->wrapInTransaction(function (): void {
            $order = $this->createOrderWithTwoLines();
            $this->repo->save($order);

            // Удаляем одну линию
            $order->removeLine($order->lines()[0]);
            $this->repo->save($order);

            $loaded = $this->repo->byId($order->id());
            $this->assertCount(1, $loaded->lines());
        });
    }

    public function test_unique_constraint_on_number(): void
    {
        $this->wrapInTransaction(function (): void {
            $this->repo->save($this->createOrder('ORD-DUP'));

            $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
            $this->repo->save($this->createOrder('ORD-DUP'));
        });
    }
}
```

#### Пример: N+1 regression test

```php
// tests/Integration/Doctrine/Order/OrderQueryPerformanceTest.php
final class OrderQueryPerformanceTest extends AbstractIntegrationTest
{
    public function test_list_orders_does_not_trigger_n_plus_one(): void
    {
        // Создаём 10 заказов
        $this->wrapInTransaction(function (): void {
            for ($i = 1; $i <= 10; $i++) {
                $this->repo->save($this->createOrder("ORD-PERF-{$i}"));
            }
        });

        // Считаем запросы
        $logger = new QueryCounter();
        $this->em->getConnection()->getConfiguration()->setSQLLogger($logger);

        $orders = $this->em->createQueryBuilder()
            ->select('o', 'l')
            ->from(Order::class, 'o')
            ->leftJoin('o.lines', 'l')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // Ожидаем ≤ 3 запросов (orders + lines + author), а не 1 + 10
        $this->assertLessThanOrEqual(3, $logger->count, "Expected ≤ 3 queries, got {$logger->count}");
    }
}
```

```php
// tests/Integration/QueryCounter.php
final class QueryCounter implements \Doctrine\DBAL\Logging\Logger
{
    public int $count = 0;

    public function startQuery(string $sql, ?array $params = null, ?array $types = null): void
    {
        $this->count++;
    }

    public function stopQuery(): void {}
}
```

#### Пример: Транзакция

```php
public function test_rollback_on_exception(): void
{
    $this->wrapInTransaction(function (): void {
        $order = $this->createOrder('ORD-TX');
        $this->repo->save($order);

        // Имитируем ошибку после save
        $this->repo->save($order);
        throw new \RuntimeException("Simulated failure");

        // После rollback:
        $this->assertNull(
            $this->em->getRepository(Order::class)->findOneBy(['number' => 'ORD-TX']),
            "Order should not exist after rollback"
        );
    });
}
```

---

### Feature-тесты (API)

#### Что тестирую

- Полный цикл: request → controller → use case → repository → response.
- Валидация входных данных.
- Авторизация / ролевые ограничения.
- HTTP-коды.
- Формат ответа.
- Edge cases: 404, 409, 422.

#### Инструменты

```php
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Mime\RawMessage;
```

#### Пример

```php
// tests/Feature/Http/Order/OrderApiTest.php
namespace Tests\Feature\Http\Order;

use App\Infrastructure\Doctrine\Order\OrderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderApiTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        // Очистка БД между тестами
        $this->em->getConnection()->executeStatement('TRUNCATE orders, order_lines RESTART IDENTITY CASCADE');
        $this->em->clear();
    }

    public function test_create_order_success(): void
    {
        $this->client->request('POST', '/api/orders', [
            'json' => [
                'customerId' => 1,
                'lines' => [
                    ['productId' => 'prod_1', 'quantity' => 2, 'unitPriceCents' => 1500],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonPathSame('orderId', $this->client->getResponse()->getContent());
    }

    public function test_create_order_validation_error(): void
    {
        $this->client->request('POST', '/api/orders', [
            'json' => [
                'customerId' => 1,
                'lines' => [
                    ['productId' => 'prod_1', 'quantity' => -1, 'unitPriceCents' => 1500],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonPathSame('errors.0.field', 'lines.0.quantity');
    }

    public function test_cannot_confirm_another_users_order(): void
    {
        // Логин как user_2
        $this->client->request('POST', '/api/login', ['json' => ['email' => 'user2@test.com', 'password' => 'secret']]);

        // Пытаемся подтвердить заказ user_1
        $this->client->request('POST', '/api/orders/order_1/confirm');

        $this->assertResponseStatusCodeSame(403);
    }
}
```

---

### Что мокаю, а что нет

#### Мокаю (всегда)

| Что | Почему |
|-----|--------|
| `MailerInterface` (email) | Не хочу отправлять реальные письма |
| `PaymentGateway` (Stripe, Adyen) | Не хочу списывать реальные деньги |
| `SearchIndex` (Meilisearch, ES) | Не хочу зависеть от внешнего сервиса |
| External HTTP API (CRM, ERP) | Медленно, нестабильно, paid |
| `EventPublisher` (RabbitMQ) | Не хочу зависеть от брокера |
| `Clock` (текущее время) | Для тестов с датами |
| `RandomIdGenerator` | Для детерминированных тестов |

#### НЕ мокаю

| Что | Почему |
|-----|--------|
| `EntityManager` / Doctrine | Моки БД = тесты не проверяют реальное поведение |
| `OrderRepository` (реализация) | Это Integration test, не Unit |
| `Order` (доменная сущность) | Это то, что я тестирую |
| `Money`, `Quantity` (VO) | Чистая логика, моки убивают смысл |
| `Kernel` (Symfony) | Feature-тесты должны бить через реальный контейнер |

#### Правило

> Мокаю **границы системы** (внешние сервисы).
> Не мокаю **внутреннюю механику** (БД, домен).

---

### База данных в тестах

#### Вариант 1: Transaction rollback (основной)

```php
protected function setUp(): void
{
    self::bootKernel();
    $this->em = static::getContainer()->get('doctrine')->getManager();
    $this->em->getConnection()->beginTransaction();
}

protected function tearDown(): void
{
    $this->em->getConnection()->rollBack();
}
```

**Плюсы:**
- Быстро (не создаю таблицы).
- Изоляция (каждый тест начинается с чистого состояния).
- Не нужен `TRUNCATE`.

**Минусы:**
- Не тестирует `TRUNCATE`, `VACUUM`, `ALTER TABLE`.
- Если тест вызывает `commit()` — rollback не сработает (нужен `savepoint`).
- `Doctrine` может кэшировать сущности (нужен `clear()`).

#### Вариант 2: Отдельная тестовая БД

```env
DATABASE_URL=postgresql://test:test@localhost:5432/app_test
```

```php
// Перед каждым тестом:
protected function setUp(): void
{
    self::bootKernel();
    $this->em = static::getContainer()->get('doctrine')->getManager();

    // Очистка:
    $this->em->getConnection()->executeStatement('TRUNCATE orders, order_lines, users RESTART IDENTITY CASCADE');
}
```

**Плюсы:**
- Полная изоляция.
- Можно тестировать `TRUNCATE`, migrations.
- Нет проблем с identity map.

**Минусы:**
- `TRUNCATE` медленный на больших таблицах.
- Нужно держать отдельную БД.

#### Вариант 3: Testcontainers (наилучший)

```php
// docker-compose.test.yml
version: '3.8'
services:
  postgres:
    image: postgres:16
    environment:
      POSTGRES_DB: app_test
      POSTGRES_USER: test
      POSTGRES_PASSWORD: test
    ports:
      - "5433:5432"
```

```yaml
# config/packages/test/doctrine.yaml
doctrine:
    dbal:
        url: 'postgresql://test:test@localhost:5433/app_test?serverVersion=16'
```

**Плюсы:**
- Идентичная версия PostgreSQL (16), как в production.
- Нет зависимости от локальной установки.
- CI: каждый run — чистая БД.
- Можно тестировать `EXCLUDE`, `GENERATED`, `PARTITION BY`.

**Минусы:**
- Docker нужен в CI.
- Первый запуск медленнее (pull образа).

#### Что я делаю в production

```
Локально: Testcontainers (Docker) + transaction rollback
CI (GitHub Actions): Testcontainers (Docker) + transaction rollback
Feature-тесты: отдельная БД + TRUNCATE в tearDown
```

---

### Скорость

#### Принципы

| Принцип | Как |
|---------|-----|
| Unit-тесты — 90% покрытия | Не трогают БД, не поднимают Kernel |
| Integration — только то, что нужно | Не дублирую Unit |
| Feature — 5–10 кейсов на endpoint | Не 50 |
| Параллельность | `paratest` для Unit, `--processes` для Integration |
| Кэширование Kernel | Symfony кэширует DI в `var/cache/test/` |

#### Целевые времена

| Тип | На тест | Всего (100 тестов) |
|-----|---------|-------------------|
| Unit | < 5ms | < 500ms |
| Integration | < 50ms | < 5s |
| Feature | < 500ms | < 30s |
| **Итого** | | **< 40s** |

Если всё вместе > 2 минут — что-то не так.

#### Инструменты

```bash
# Параллельный запуск Unit-тестов:
vendor/bin/paratest --processes 4 --testsuite Unit

# Только Integration:
vendor/bin/phpunit --testsuite Integration

# Только Feature:
vendor/bin/phpunit --testsuite Feature

# Фильтр по классу:
vendor/bin/phpunit --filter OrderRepositoryTest
```

```xml
<!-- phpunit.xml -->
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
```

---

### Стабильность (анти-flaky)

#### Типовые причины flaky-тестов

| Причина | Решение |
|---------|---------|
| Race condition (2 теста на одну строку) | Transaction rollback / отдельная БД |
| Зависимость от времени (`new \DateTime()`) | Инъекция `Clock` (port) |
| Зависимость от порядка тестов | `TRUNCATE` в `tearDown`, не `setUp` |
| Зависимость от внешнего сервиса | Mock / in-memory transport |
| `sleep()` в коде | Заменить на ожидание события / polling |
| Нестабильные ID (UUID) | Фиксированные ID в тестах |
| Кэш (Redis, Doctrine) | Очистка в `setUp` / `tearDown` |

#### Конкретные решения

**1. Clock**

```php
// src/Domain/Shared/Clock.php
interface Clock
{
    public function now(): \DateTimeImmutable;
}

// src/Infrastructure/Clock/SystemClock.php
final class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}

// tests/TestClock.php
final class TestClock implements Clock
{
    public function __construct(private \DateTimeImmutable $now) {}

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}

// В тесте:
$clock = new TestClock(new \DateTimeImmutable('2024-06-15 10:30:00'));
```

**2. In-memory Messenger**

```env
# .env.test
MESSENGER_TRANSPORT_DSN=in-memory://
```

```yaml
# config/packages/test/messenger.yaml
framework:
    messenger:
        transports:
            default: 'in-memory://'
```

```php
// Проверка, что сообщение было отправлено:
$transport = static::getContainer()->get('messenger.transport.in_memory');
$envelope = $transport->get();
$this->assertInstanceOf(SendEmail::class, $envelope->getMessage());
```

**3. Null mailer**

```env
MAILER_DSN=null://null
```

Symfony `null://null` — сообщения "уходят в никуда".

**4. Фиксированные данные**

```php
// НЕ:
$orderId = (new \Ramsey\Uuid\Uuid())->toString();

// ХОРОШО:
$orderId = 'test-order-001';
```

**5. Очистка кэша**

```php
protected function setUp(): void
{
    self::bootKernel();

    // Очистка Doctrine second-level cache
    $cache = $this->em->getCache();
    if ($cache->isSubscriptionEnabled()) {
        $cache->getRegion('test_region')->evictAll();
    }
}
```

**6. Без `sleep()`**

```php
// ПЛОХО:
sleep(2);
$this->assertThat($payment->isProcessed());

// ХОРОШО: polling с timeout
$timeout = 5;
$deadline = microtime(true) + $timeout;

while (microtime(true) < $deadline) {
    $this->em->refresh($payment);
    if ($payment->isProcessed()) break;
    usleep(100_000); // 100ms
}

$this->assertTrue($payment->isProcessed());
```

---

### Что я НЕ тестирую

| Что | Почему |
|-----|--------|
| Symfony internals | Это не мой код |
| Doctrine internals | Это не мой код |
| PostgreSQL internals | Это не мой код |
| `vendor/` | Никогда |
| Конфигурационные файлы | Тестирую поведение, не YAML |
| "Дымовые" тесты ("запустилось") | CI ищет ошибки, не "работает ли" |
| Тесты, которые дублируют Unit | `Order::confirm()` покрыто Unit. Feature проверяет API, не логику |

---

### Покрытие: что важно, а что нет

| Что покрываю | Приоритет |
|-------------|-----------|
| Domain-инварианты (все правила) | **Критично** |
| VO-валидация (все throw) | **Критично** |
| Repository (CRUD + constraints) | Высокий |
| Use case (happy path + 2–3 error) | Высокий |
| API (auth + 1 happy + 1 error) | Средний |
| Queue handler (1 happy + 1 retry) | Средний |
| Edge cases (race, concurrency) | По ситуации |

**Цель:** не 100% coverage, а 100% покрыва **инвариантов**.

```bash
# Покрытие только Domain:
vendor/bin/phpunit --testsuite Unit --coverage-filter src/Domain --coverage-text
```

---

### CI-конвейер

```yaml
# .github/workflows/tests.yml
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
          coverage: xdebug
      - run: composer install
      - run: vendor/bin/paratest --processes 4 --testsuite Unit

  integration:
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
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    env:
      DATABASE_URL: postgresql://test:test@localhost:5432/app_test
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: vendor/bin/phpunit --testsuite Integration --processes 2

  feature:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: app_feature
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
    env:
      DATABASE_URL: postgresql://test:test@localhost:5432/app_feature
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: vendor/bin/phpunit --testsuite Feature
```

---

### Итого

| Вопрос | Ответ |
|--------|-------|
| Пирамида? | Да: 50–60% Unit, 30–40% Integration, 10–15% Feature |
| Мокаю? | Внешние сервисы (email, payment, search, MQ) |
| Не мокаю? | БД, Doctrine, домен |
| БД в тестах? | PostgreSQL 16 (Testcontainers / services в CI) |
| Изоляция? | Transaction rollback (Integration) / TRUNCATE (Feature) |
| Скорость? | < 40s на всё. Unit < 500ms. |
| Стабильность? | Clock, in-memory MQ, null mailer, фиксированные ID, polling вместо sleep |
| Покрытие? | 100% инвариантов домена. Не 100% строк. |
| CI? | 3 jobs: Unit / Integration / Feature. Параллельно. |

> ⏱ 57.2s · prompt 86199 tok · output 7034 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о concurrent modification — это перекликается с предыдущим вопросом про конкурентные изменения агрегата. Но теперь вопрос шире: "два пользователя одновременно изменяют одну запись" — это более общая постановка, не привязанная к агрегату.

Нужно:
1. Описать проблемы (lost update, inconsistency, deadlock, race condition)
2. Перечислить способы защиты (optimistic, pessimistic, SERIALIZABLE, idempotency, queue, ETag)
3. Сравнить подходы
4. Дать практические рекомендации по выбору

Не должен просто повторить предыдущий ответ — нужно дать более широкую картину, добавить новые механизмы (ETag, If-Match, advisory locks, advisory xact locks), и сделать акцент на "два пользователя" (UI-уровень), а не только "два процесса".

Структура:
1. Какие проблемы возникают
2. Способы защиты (каждый с примером)
3. Сравнительная таблица
4. Когда что выбираю
5. Антипаттерны
6. Практические советы

Дам конкретные примеры кода на PHP + SQL.

</details>

**Кандидат:**

## Конкурентные изменения одной записи

### Какие проблемы возникают

#### 1. Lost update (самая частая)

```text
User A (админ):                User B (админ):
──────────────                ──────────────
t1: видит order.status = 'draft'
t2: меняет на 'cancelled'
t3: сохранение
                               t1: видит order.status = 'draft'
                               t2: меняет на 'confirmed'
                               t3: сохранение

Итог: status = 'confirmed'
Ожидалось A: 'cancelled'
Ожидалось B: 'confirmed'
```

Оба "успешно" сохранили. Ни один не получил ошибку. Данные в невалидном состоянии.

#### 2. Визуальный конфликт (UI race)

```text
User A: открывает форму редактирования (видит email = old@x.com)
User B: меняет email на new@x.com, сохраняет
User A: не знает, что изменилось, сохраняет old@x.com

Итог: email = old@x.com (User B "потерян")
```

Это не lost update в смысле БД, а **сталивое чтение**.

#### 3. Частичное состояние

```text
Запись: { name: "Ivan", email: "old@x.com", role: "user" }

User A: меняет name → "Ivanov"
User B: меняет email → "new@x.com", role → "admin"

Если оба пишут по колонкам (UPDATE SET name=..., UPDATE SET email=..., role=...):
→ зависит от порядка, но в большинстве случаев обе правки применяются.

Если оба пишут всю запись (UPDATE SET name=..., email=..., role=...):
→ один затерло другого.
```

#### 4. Бизнес-конфликт

```text
Два менеджера одновременно утверждают один и тот же отчёт.
Оба видят status = 'pending'.
Оба нажимают "Approve".
Оба получают 200 OK.
В системе два утверждения (а должно быть одно).
```

#### 5. Deadlock

```text
Tx A: UPDATE orders SET status='paid' WHERE id=1  → UPDATE inventory SET stock=stock-1 WHERE product=5
Tx B: UPDATE inventory SET stock=stock-1 WHERE product=5  → UPDATE orders SET status='cancelled' WHERE id=1

A ждёт B, B ждёт A. PostgreSQL убивает одного.
```

---

### Способы защиты

#### 1. Optimistic Locking (version column)

Самый распространённый подход. Добавляю `version` к таблице.

```sql
ALTER TABLE orders ADD COLUMN version INT NOT NULL DEFAULT 0;
```

**Запись с проверкой версии:**

```sql
UPDATE orders
SET status = 'confirmed', version = version + 1
WHERE id = 1 AND version = 3;
```

Если `version` уже изменился (другой процесс записал первым) — `0 rows updated` → я знаю, что конфликт.

**PHP-реализация:**

```php
// Entity
#[ORM\Column(type: 'integer')]
private int $version = 0;

// Repository
public function save(Order $order): void
{
    $sql = 'UPDATE orders SET status = :status, version = version + 1
            WHERE id = :id AND version = :expected';

    $affected = $this->em->getConnection()->executeStatement($sql, [
        'status'   => $order->status(),
        'id'       => $order->id(),
        'expected' => $order->version(),
    ]);

    if ($affected === 0) {
        throw new ConcurrencyConflictException($order->id(), $order->version());
    }
}
```

**На UI-уровне (визуальный конфликт):**

```http
GET /api/orders/1
→ { "status": "draft", "version": 3, ... }

POST /api/orders/1
{ "status": "confirmed", "version": 3 }
→ 200 OK (если version ещё 3)
→ 409 Conflict (если version уже 4)
```

```php
// Controller
public function update(int $id, Request $request): JsonResponse
{
    $order = $this->orders->byId($id);
    $clientVersion = (int) $request->get('version');

    if ($order->version() !== $clientVersion) {
        return new JsonResponse(
            ['error' => 'Conflict', 'serverVersion' => $order->version()],
            409
        );
    }

    $order->update($request->toArray());
    $this->orders->save($order);

    return $this->json(['version' => $order->version()]);
}
```

**Плюсы:**

| | |
|---|---|
| Нет блокировок при чтении | Оба пользователя могут одновременно читать |
| Конфликт обнаружен при записи | Быстро, без ожидания |
| Простая модель | Клиент знает свою версию |
| Масштабируется | Нет "узкого горла" в БД |

**Минусы:**

| | |
|---|---|
| Нужно retry | Клиент должен перезагрузить и повторить |
| UI-сложность | "Запись была изменена другим пользователем" |
| Не подходит для длинных операций | Если форма заполнялась 10 минут — конфликт почти гарантирован |

**Когда использую:** 80% случаев. Админки, CMS, формы редактирования.

---

#### 2. Pessimistic Locking (SELECT FOR UPDATE)

Блокировка строки на время транзакции.

```sql
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;  -- блокирует строку
-- User B, который выполнит SELECT FOR UPDATE в это время, будет ЖДАТЬ.

UPDATE orders SET status = 'confirmed' WHERE id = 1;
COMMIT;
```

**PHP-реализация:**

```php
public function confirm(int $id): void
{
    $this->em->wrapInTransaction(function () use ($id): void {
        $sql = 'SELECT * FROM orders WHERE id = :id FOR UPDATE';
        $order = $this->em->getConnection()->fetchAssociative($sql, ['id' => $id]);

        if ($order['status'] !== 'draft') {
            throw new InvalidTransitionException($order['status'], 'confirmed');
        }

        $this->em->getConnection()->executeStatement(
            'UPDATE orders SET status = :status WHERE id = :id',
            ['status' => 'confirmed', 'id' => $id]
        );
    });
}
```

**Плюсы:**

| | |
|---|---|
| Нет lost update | Гарантированно последовательное изменение |
| Не нужен retry | Блокировка решает |
| Простая логика | Кто первый — тот и записал |

**Минусы:**

| | |
|---|---|
| Блокировка | Второй пользователь ждёт |
| Deadlock | Если A и B блокируют разные строки в разном порядке |
| Throughput | Параллельные операции сериализуются |
| Timeout | Если A завис — B ждёт до `lock_timeout` |

**`lock_timeout`:**

```sql
SET lock_timeout = '5s';  -- если блокировка > 5s → ошибка 55P03
```

**Когда использую:**

| Сценарий | Почему |
|----------|--------|
| Списание с баланса | Конфликт частый, lost update критичен |
| Резервирование товара | Нельзя зарезервировать больше, чем есть |
| Одобрение / отклонение | Только один должен "выиграть" |
| Короткие операции (< 100ms) | Блокировка недолгая |

---

#### 3. ETag / If-Match (HTTP-уровень)

Аналог optimistic locking, но на уровне HTTP.

```http
GET /api/orders/1
ETag: "abc123"
{ "status": "draft", "email": "old@x.com" }

PUT /api/orders/1
If-Match: "abc123"
{ "status": "confirmed" }
→ 200 OK + ETag: "def456"

PUT /api/orders/1
If-Match: "abc123"   ← устарел
{ "email": "new@x.com" }
→ 412 Precondition Failed
```

**Реализация:**

```php
// Генерация ETag:
function generateETag(Order $order): string
{
    return '"' . sha1($order->id() . ':' . $order->version() . ':' . $order->status()) . '"';
}

// Controller:
public function put(int $id, Request $request): JsonResponse
{
    $order = $this->orders->byId($id);
    $serverETag = generateETag($order);

    $clientETag = $request->headers->get('If-Match');
    if ($clientETag !== null && $clientETag !== $serverETag) {
        return new JsonResponse(['error' => 'Precondition failed'], 412);
    }

    $order->update($request->toArray());
    $this->orders->save($order);

    $response = new JsonResponse(['status' => $order->status()]);
    $response->headers->set('ETag', generateETag($order));

    return $response;
}
```

**Плюсы:**
- Стандарт HTTP, любой клиент понимает.
- Не нужен `version` в теле запроса.
- Кэширование: `ETag` + `Cache-Control` — браузер может кэшировать.

**Минусы:**
- Только для HTTP-клиентов (не для CLI, queue).
- Нужно генерировать и сравнивать ETag.

**Когда использую:** REST API, публичные эндпоинты. Внутри системы — `version` column проще.

---

#### 4. SERIALIZABLE isolation

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
BEGIN;
SELECT status FROM orders WHERE id = 1;
-- ... бизнес-логика ...
UPDATE orders SET status = 'confirmed' WHERE id = 1;
COMMIT;
```

PostgreSQL отслеживает, не было ли "цикла зависимостей" между транзакциями. Если да — откатывает одну из них.

**Плюсы:**
- Максимальная изоляция.
- Не нужен `version`.
- Не нужна блокировка.

**Минусы:**

| | |
|---|---|
| Низкий throughput | Сериализация |
| `SerializationError` (40001) | Нужно retry |
| Сложность отладки | "Почему откатилось?" — не всегда очевидно |
| Overhead | PostgreSQL ведёт дополнительную таблицу `pg_xact` |

**Когда использую:** rarely. Только когда "read then write" на разных строках и конфликт невозможно детектировать через `version`.

Пример:

```sql
-- Два процесса:
-- A: читает balance_user1, пишет balance_user2
-- B: читает balance_user2, пишет balance_user1
-- READ COMMITTED: оба проходят. SERIALIZABLE: один откатывается.
```

---

#### 5. Advisory Locks (PostgreSQL)

Явные блокировки на уровне сессии/транзакции. Не привязаны к строке.

```sql
-- Транзакционная блокировка (освобождается при COMMIT/ROLLBACK):
SELECT pg_advisory_xact_lock(12345);  -- 12345 = "ключ" (например, order_id)

-- Сессийная блокировка (освобождается явно или при disconnect):
SELECT pg_advisory_lock(12345);
-- ...
SELECT pg_advisory_unlock(12345);
```

**PHP:**

```php
$this->em->wrapInTransaction(function () use ($orderId): void {
    // Блокировка на порядок (order)
    $this->em->getConnection()->executeStatement(
        'SELECT pg_advisory_xact_lock(:key)',
        ['key' => $orderId]
    );

    // Теперь я единственный, кто может менять этот order
    $order = $this->orders->byId($orderId);
    $order->confirm();
    $this->orders->save($order);
});
```

**Плюсы:**
- Гибкость: блокирую не строку, а "ресурс" (заказ, пользователь, проект).
- Можно блокировать на уровне агрегата, даже если изменения касаются разных таблиц.
- `pg_advisory_xact_lock` автоматически освобождается при commit/rollback.

**Минусы:**
- Нужно выбрать "ключ" (int или (int, int)).
- Если забуду `pg_advisory_unlock` (для сессийной) — блокировка до disconnect.
- Не видно в `pg_stat_activity` как блокировка на строку.

**Когда использую:**
- Операция затрагивает несколько таблиц, но должна быть атомарной по "бизнес-объекту".
- Пример: "оплатить заказ" = update orders + insert payments + update inventory. Один advisory lock на `order_id` — и никто не может параллельно оплатить тот же заказ.

---

#### 6. Idempotency Key

Не защита от конкурентности, а защита от **дублей**.

```sql
CREATE TABLE operations (
    idempotency_key VARCHAR(64) PRIMARY KEY,
    order_id BIGINT NOT NULL,
    operation VARCHAR(32) NOT NULL,
    result JSONB,
    created_at TIMESTAMP DEFAULT NOW()
);
```

```php
public function confirm(int $orderId, string $idempotencyKey): void
{
    // Проверка: уже делали?
    $existing = $this->em->createQueryBuilder()
        ->select('o.result')
        ->from(Operation::class, 'o')
        ->where('o.idempotencyKey = :key')
        ->setParameter('key', $idempotencyKey)
        ->getQuery()
        ->getOneOrNullResult();

    if ($existing !== null) {
        return $existing;  // ← идемпотентный ответ
    }

    // ... бизнес-логика ...

    $op = new Operation($idempotencyKey, $orderId, 'confirm', $result);
    $this->em->persist($op);
    $this->em->flush();
}
```

`UNIQUE(idempotency_key)` — если два процесса одновременно попытаются записать → один упадёт с `UniqueViolationException`.

**Когда использую:**
- Payment-операции (client retry).
- Webhook-обработка (provider retry).
- Любая операция, которую клиент может повторить.

**Важно:** idempotency key **не заменяет** optimistic/pessimistic locking. Это дополнение: "если уже сделали — не делай снова".

---

#### 7. Queue (сериализация через очередь)

Если конфликт на одной записи очень частый (1000+ RPS на одну строку) — блокировки и retry убивают throughput.

```text
Все запросы "изменить заказ 1" → в очередь.
Один worker обрабатывает по одному.
Конкурентности нет — только последовательность.
```

```php
// Publisher
$this->bus->dispatch(new UpdateOrderCommand($orderId, $changes));

// Consumer (один на orderId)
#[AsMessageHandler]
class UpdateOrderHandler
{
    public function __invoke(UpdateOrderCommand $cmd): void
    {
        $order = $this->orders->byId($cmd->orderId);
        $order->apply($cmd->changes);
        $this->orders->save($order);
    }
}
```

**Конфигурация: одна очередь на один orderId** (routing key = orderId).

```yaml
# RabbitMQ: routing key = order_id
# 1000 очередей? Нет. 1 очередь, routing key распределяет.
# Или: partitioning по orderId % N
```

**Плюсы:**
- Максимальный throughput.
- Нет блокировок.
- Нет retry-циклов.

**Минусы:**
- Задержка (очередь).
- Сложность (нужен broker, consumer, dead-letter).
- Не подходит для "read-modify-write" с немедленным ответом клиенту.
- Ordering в пределах очереди — да, между очередями — нет.

**Когда использую:**
- High throughput (> 100 RPS на одну запись).
- Задержка допустима (1–5 сек).
- Операция "изменить" (не "прочитать и показать").

---

### Сравнение

| Подход | Блокировка? | Retry? | Throughput | Сложность | Когда |
|--------|------------|--------|-----------|-----------|-------|
| **Optimistic (version)** | Нет (при чтении) | Да (клиент) | Высокий | Низкая | 80% случаев |
| **Pessimistic (FOR UPDATE)** | Да | Нет | Средний | Низкая | Критичные операции, короткие |
| **ETag / If-Match** | Нет | Да (клиент) | Высокий | Средняя | REST API |
| **SERIALIZABLE** | Нет (но откат) | Да (server) | Низкий | Средняя | Read-modify-write на разных строках |
| **Advisory Lock** | Да | Нет | Средний | Средняя | Мульти-табличная операция на одном объекте |
| **Idempotency Key** | Нет (UNIQUE) | Да (server) | Высокий | Низкая | Retry от клиента / provider |
| **Queue** | Нет | Нет | Очень высокий | Высокая | > 100 RPS на запись |

---

### Когда что выбираю

#### Сценарий: админка, два менеджера редактируют клиента

**Выбор: Optimistic (version)**

Почему:
- Конфликт редкий (два менеджера одновременно редактируют одного клиента — раз в неделю).
- UI может показать "Запись была изменена. Обновите и попробуйте снова."
- Нет блокировки: второй менеджер не ждёт.
- Простая реализация.

```php
// В ответе:
{ "id": 5, "name": "Ivan", "email": "old@x.com", "version": 42 }

// В запросе:
{ "name": "Ivan", "email": "new@x.com", "version": 42 }

// Конфликт:
409 { "error": "Conflict", "current": { "email": "other@x.com", "version": 43 } }
```

---

#### Сценарий: списание с баланса

**Выбор: Pessimistic (FOR UPDATE)**

Почему:
- Конфликт частый (два платежа одновременно).
- Lost update критичен (деньги!).
- Операция короткая (< 50ms).
- Блокировка недолгая.
- Не нужен retry: кто первый — тот и списал.

```php
$this->em->wrapInTransaction(function (): void {
    $sql = 'SELECT balance FROM accounts WHERE id = :id FOR UPDATE';
    $balance = (int) $this->em->getConnection()->fetchOne($sql, ['id' => $accountId]);

    if ($balance < $amount) {
        throw new InsufficientBalanceException($balance, $amount);
    }

    $this->em->getConnection()->executeStatement(
        'UPDATE accounts SET balance = balance - :amount WHERE id = :id',
        ['amount' => $amount, 'id' => $accountId]
    );
});
```

---

#### Сценарий: подтверждение заказа (только один должен "выиграть")

**Выбор: Pessimistic (FOR UPDATE) + статус**

Почему:
- Два админа нажимают "Confirm" одновременно.
- Только один должен пройти.
- `FOR UPDATE` блокирует. Второй ждёт, видит `status = 'confirmed'`, бросает `InvalidTransition`.

```php
$this->em->wrapInTransaction(function () use ($id): void {
    $sql = 'SELECT status FROM orders WHERE id = :id FOR UPDATE';
    $status = $this->em->getConnection()->fetchOne($sql, ['id' => $id]);

    if ($status !== 'draft') {
        throw new InvalidTransitionException($status, 'confirmed');
    }

    $this->em->getConnection()->executeStatement(
        'UPDATE orders SET status = :status WHERE id = :id',
        ['status' => 'confirmed', 'id' => $id]
    );
});
```

---

#### Сценарий: оплата через external gateway + retry от клиента

**Выбор: Idempotency Key + Optimistic**

Почему:
- Клиент может нажать "Оплатить" 3 раза (doubt, timeout, retry).
- Gateway может повторить webhook.
- `idempotency_key` гарантирует, что операция выполнена один раз.

```php
public function charge(string $idempotencyKey, int $orderId, int $amount): PaymentResult
{
    // 1. Уже делали?
    $existing = $this->findOperation($idempotencyKey);
    if ($existing !== null) {
        return $existing;  // ← идемпотентный ответ
    }

    // 2. Оплачиваем (pessimistic на payment)
    $result = $this->em->wrapInTransaction(function () use ($orderId, $amount): PaymentResult {
        $sql = 'SELECT status FROM payments WHERE order_id = :oid FOR UPDATE';
        $status = $this->em->getConnection()->fetchOne($sql, ['oid' => $orderId]);

        if ($status === 'charged') {
            throw new PaymentAlreadyChargedException($orderId);
        }

        // ... списание ...

        $this->em->getConnection()->executeStatement(
            'UPDATE payments SET status = :s WHERE order_id = :oid',
            ['s' => 'charged', 'oid' => $orderId]
        );

        return new PaymentResult('charged');
    });

    // 3. Запоминаем idempotency key
    $this->em->wrapInTransaction(function () use ($idempotencyKey, $orderId, $result): void {
        $this->em->getConnection()->executeStatement(
            'INSERT INTO operations (idempotency_key, order_id, operation, result) VALUES (:k, :oid, :op, :r)',
            ['k' => $idempotencyKey, 'oid' => $orderId, 'op' => 'charge', 'r' => json_encode($result)]
        );
    });

    return $result;
}
```

---

#### Сценарий: 5000 RPS на одну запись (leaderboard, counter)

**Выбор: Queue (сериализация) или batch**

Почему:
- `FOR UPDATE` на 5000 RPS — очередь блокировок, throughput падает.
- `version` + retry — бесконечные retry, деградация.
- Queue: один consumer обрабатывает по одному. Throughput = скорость consumer'а.

Альтернатива: **batch update**:

```sql
-- Вместо 5000 отдельных UPDATE:
UPDATE leaderboard SET score = score + :delta WHERE user_id = :uid;
-- 5000 вызовов, но без SELECT. Без блокировки на чтение.
-- Конфликт? Нет: `score = score + delta` атомарно.
```

**Когда batch работает:** операция — арифметика (`+`, `-`, `*`), не "прочитать и вычислить".

---

### Антипаттерны

#### 1. SELECT + UPDATE без блокировки

```php
// ПЛОХО:
$balance = $this->em->getConnection()->fetchOne(
    'SELECT balance FROM accounts WHERE id = 1'
);
if ($balance < $amount) throw new \Exception('Insufficient');

$amount = $balance - $amount;
$this->em->getConnection()->executeStatement(
    'UPDATE accounts SET balance = :b WHERE id = 1',
    ['b' => $amount]
);
```

Race condition: между SELECT и UPDATE другой процесс изменил balance.

**Решение:** `FOR UPDATE` или `UPDATE ... SET balance = balance - :amount WHERE balance >= :amount`.

#### 2. `catch (Exception $e) { retry(); }` без backoff

```php
for ($i = 0; $i < 10; $i++) {
    try {
        $this->confirm($id);
        break;
    } catch (ConcurrencyConflictException $e) {
        // сразу retry — конфликт всё ещё есть
    }
}
```

**Решение:** exponential backoff + jitter.

```php
$delay = 100;
for ($i = 0; $i < 5; $i++) {
    try {
        $this->confirm($id);
        return;
    } catch (ConcurrencyConflictException $e) {
        usleep(($delay + random_int(0, $delay / 2)) * 1000);
        $delay *= 2;
    }
}
throw new \RuntimeException("Failed after 5 retries");
```

#### 3. `sleep()` в UI-коде

```php
// ПЛОХО:
sleep(1);  // "подождём, пока другой запрос закончится"
$order = $this->orders->byId($id);
```

Это не защита. Это "надежда".

#### 4. Один большой `FOR UPDATE` на всю таблицу

```sql
-- ПЛОХО:
SELECT * FROM orders FOR UPDATE;  -- блокирует ВСЕ строки
```

**Решение:** `WHERE id = :id FOR UPDATE` — только нужная строка.

#### 5. Deadlock из-за разного порядка

```text
Process A:
  1. UPDATE orders SET ... WHERE id = 1
  2. UPDATE inventory SET ... WHERE product = 5

Process B:
  1. UPDATE inventory SET ... WHERE product = 5
  2. UPDATE orders SET ... WHERE id = 1

→ Deadlock.
```

**Решение:** одинаковый порядок операций во всех процессах.

```text
Всегда: orders → inventory (не наоборот)
```

---

### Практические советы

| Совет | Почему |
|-------|--------|
| Всегда ставьте `lock_timeout` | Без него `FOR UPDATE` может ждать вечно |
| В логах пишите `version` / `idempotency_key` | При конфликте видно, кто с кем |
| В UI показывайте текущее значение при 409 | "Запись была изменена: email = other@x.com. Обновить?" |
| Не смешивайте optimistic и pessimistic | Выберите один подход на операцию |
| `version` — в ответе API | Клиент знает, какую версию он читал |
| `idempotency_key` — на payment-операциях | Клиент retry, provider retry |
| Test deadlock: `SET deadlock_timeout = '1s'` | Быстрее обнаружение в тестах |
| Test concurrent: `pg_terminate_backend()` | Имитация crash |

---

### Итого

| Сценарий | Подход |
|----------|--------|
| Админка, редкие конфликты | **Optimistic (version)** |
| REST API | **ETag / If-Match** |
| Баланс, деньги | **Pessimistic (FOR UPDATE)** |
| Payment + retry | **Idempotency Key + Pessimistic** |
| Multi-table операция на одном объекте | **Advisory Lock** |
| > 100 RPS на запись | **Queue** или **batch** |
| Read-modify-write на разных строках | **SERIALIZABLE** |

**Главное правило:**

> Выберите один подход на операцию. Не смешивайте.
> Определяйте, какой конфликт допустим (retry) и какой нет (lost update критичен).
> Тестируйте concurrent-сценарии, а не только happy path.

> ⏱ 58.8s · prompt 93061 tok · output 6827 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

<details><summary>Ход мысли модели</summary>

We need answer user's question in Russian. Need describe git process from task to production: branching, commit format, PR, review, releases, explain why. Need be practical for PHP/Symfony likely. Need maybe mention team size, trunk-based vs gitflow, conventional commits, PR small, CI, feature flags, release strategies, tags, hotfix, rebasing vs merging, protected branches, squash merge, branch naming, changelog, migrations, config, secrets, DB, feature flags. Need be concise but thorough. User is interview? We're acting as candidate. Need answer as experienced PHP backend.

Structure:
- Assumptions: 3-8 devs, Symfony, PostgreSQL, CI/CD, production.
- Principle: main stable, short-lived branches, small PR, CI gate, reversible releases.
- Branching: trunk-based with feature branches? Git-flow? Explain I avoid long-lived dev. main is always deployable. Feature branch from main, short-lived, delete after merge. For urgent hotfix from main. Release branch only if release train long and many features. Maybe use release branch for versioned releases if needed.
- Branch naming: feature/JIRA-123-name, fix/..., chore/...
- Commits: imperative mood, scoped, Conventional Commits? For history. Squash merge to main. Individual commits can be WIP? better atomic. examples.
- PR: small (<=300-500 lines maybe), title, description with context, links, migration, config, feature flags, testing, screenshots. CI must pass: lint, static analysis, tests, build. Rebase during review. Self-review.
- Review: what to check: correctness, architecture, tests, security, performance, DB migrations, config, naming. SLA, feedback, not blocking on nitpicks. approval rules.
- Release: options: trunk-based continuous deployment vs release train. For PHP/Symfony: deploy main after tag or release PR. Use tags, immutable artifacts. DB migrations before/after app? Explain expand/contract, backward compatible. Feature flags for risky changes. Rollback plan. Changelog from conventional commits.
- Why: small batches reduce review/cognitive load, main stable enables rollback, conventional commits for changelog, CI gate prevents broken deploys, short branches reduce merge conflicts, migrations safe.
- Include examples of workflow:
  1. Task -> branch feature/JIRA-123-order-confirm from main
  2. commits: feat(order): add confirm command, test(order): cover confirmation transition, etc.
  3. push, PR, CI, review, rebase, squash merge to main: feat(order): confirm order
  4. CI deploy to staging, tag v1.2.3 or release pipeline, production, verify.
- Explain alternatives: gitflow with develop/developing causes merge pain and delayed integration; feature flags if can't deploy early; long release branches okay for banks? but not default.
- Need include DB migrations strategy: expand/contract: add nullable column, backfill, enforce, remove old. Code compatible old and new. Deploy code, migration, cleanup.
- Maybe mention branch protection: main no force push, require PR, status checks, 1-2 approvals, no self-approve, merge queue? linear history? Squash vs rebase vs merge commit. I choose squash for main, rebase branch to main during review, delete branch.
- Explain commit format: type(scope): summary, body, footer. types feat fix refactor test docs chore ci build perf. But if squash, conventional helps changelog.
- For hotfix: branch fix/..., PR, tag patch, merge to main (already main), deploy. If release branch exists, backport.
- For releases: automated tag from main; semantic versioning; changelog; production deploy with tag. If continuous: deploy main to staging automatically, production after manual approval.

Need answer in Russian. Need maybe be first-person: "я делаю так". Need explain why. Final oververbosity 9, thorough.

Need ensure no internal analysis in final.

</details>

**Кандидат:**

## Мой git-процесс: от задачи до продакшена

Я описываю процесс, который использую для Symfony/PHP-приложения в небольшой команде: 3–8 бэкендеров, есть CI, PostgreSQL, есть продакшен, где нельзя просто «посмотрим, что будет».

Основная идея:

> **Git-процесс должен снижать стоимость изменения, а не формализовать жизнь.**

Поэтому я не гонюсь за идеальной историей ради истории. Мне важны:

- быстрая интеграция изменений;
- предсказуемый деплой;
- лёгкий rollback;
- понятная связь между задачей, кодом и релизом;
- отсутствие долгих живых веток.

---

# 1. Ветвление

## Что я использую

По умолчанию — **trunk-based** с короткими feature-ветками.

```text
main
 ├── feature/JIRA-123-order-confirm
 ├── fix/JIRA-124-payment-timeout
 └── chore/JIRA-125-upgrade-phpstan
```

`main` — единственная постоянно живая ветка.

Все фичи, фиксы и инфраструктурные изменения вливаются в `main`.

`main` должен быть в состоянии, пригодном для деплоя.

Не «сегодня обязательно деплоим», а:

> если мы деплоим `main` прямо сейчас — система должна быть работоспособной.

---

## Почему не Git Flow с долгим `develop`

Git Flow:

```text
master
develop
feature/...
release/...
hotfix/...
```

В маленьких и средних командах это часто даёт:

- долгую жизнь `develop`;
- большие merge'и;
- конфликты в конце;
- отставание `develop` от `main`;
- иллюзию стабильности;
- сложный процесс hotfix.

Если у команды 2–5 человек и релизы не раз в полгода, Git Flow обычно только добавляет трение.

Я бы использовал полноценный release-процесс, только если:

- релиз — это отдельный продукт с длительной сборкой;
- есть несколько команд, работающих над разными версиями;
- есть жёсткие compliance-требования;
- релиз занимает дни и включает ручное тестирование.

Иначе:

> короткие ветки + стабильный `main` + feature flags + CI/CD

даёт больше контроля, чем длинные ветки.

---

# 2. Правила веток

## Имя ветки

Формат:

```text
<тип>/<идентификатор-задачи>-<краткое-описание>
```

Примеры:

```text
feature/JIRA-123-order-confirm
fix/JIRA-124-payment-timeout
chore/JIRA-125-upgrade-phpstan
docs/JIRA-126-update-api-guide
```

Почему так:

- видно, что за задача;
- видно, какой тип изменения;
- легко фильтровать ветки;
- легко удалить лишние ветки;
- меньше споров «что это вообще за ветка».

---

## Откуда создаю

Ветка создаётся из актуального `main`:

```bash
git checkout main
git pull
git checkout -b feature/JIRA-123-order-confirm
```

Почему из `main`:

- меньше конфликтов;
- меньше интеграционного сюрприза;
- каждый PR проверяется против актуального состояния.

---

## Живёт ветка сколько?

Стремлюсь:

```text
1 день — идеально
2–3 дня — нормально
более недели — плохо
```

Длинные ветки плохи, потому что:

- отстают от `main`;
- накапливают конфликты;
- скрывают интеграционные проблемы;
- усложняют review;
- делают деплой менее предсказуемым.

---

# 3. Коммиты

## Формат

Использую Conventional Commits в упрощённом виде:

```text
type(scope): summary
```

Примеры:

```text
feat(order): add order confirmation
fix(payment): handle gateway timeout
refactor(order): extract total calculation
test(order): cover empty order confirmation
chore(ci): upgrade phpstan
docs(api): describe order endpoint
```

`type`:

| type | Когда |
|------|-------|
| `feat` | новая функциональность |
| `fix` | исправление бага |
| `refactor` | переписывание без изменения поведения |
| `test` | тесты |
| `docs` | документация |
| `chore` | рутина, скрипты, конфиг |
| `ci` | CI/CD |
| `build` | сборка, зависимости |
| `perf` | производительность |
| `style` | форматирование, линт |

`scope` — опционально, но очень полезен:

```text
feat(order): ...
fix(payment): ...
chore(doctrine): ...
```

---

## Тело коммита

Если изменение сложное, пишу:

```text
feat(order): confirm order with idempotency key

Orders can now be confirmed through ConfirmOrderHandler.
Confirmation is protected by idempotency key to avoid duplicate
processing from client retries.

Refs: JIRA-123
```

Но для большинства задач достаточно одной строки.

---

## Почему важен формат коммитов

Не потому что «так красиво».

А потому что он даёт:

### 1. Смысл в истории

```bash
git log --oneline
```

должно быть читаемо:

```text
a1b2c3d feat(order): confirm order
e4f5g6h fix(payment): handle gateway timeout
i7j8k9l test(order): cover empty order confirmation
```

А не:

```text
a1b2c3d fix
e4f5g6h work
i7j8k9l update
```

---

### 2. Автоматический changelog

Из `feat/fix` можно собрать changelog.

Пример:

```md
## [1.4.0] - 2026-01-15

### Features
- order: confirm order with idempotency key

### Fixes
- payment: handle gateway timeout
```

---

### 3. Semver

Если релизы version-ированы:

```text
feat  → minor
fix   → patch
BREAKING CHANGE → major
```

Можно автоматизировать.

---

### 4. Упрощение PR

В PR видно, что именно изменилось:

```text
feat(order): add confirmation endpoint
test(order): cover confirmation rules
chore(ci): enable phpstan level 7
```

Это лучше, чем «мелкие правки».

---

# 4. Как я делаю коммиты в ветке

Хочу, чтобы каждый коммит был **понятным шагом**.

Хорошо:

```bash
git add src/Domain/Order/Order.php
git commit -m "feat(order): add confirm transition"

git add tests/Unit/Domain/Order/OrderTest.php
git commit -m "test(order): cover confirm transition"

git add src/Application/Order/ConfirmOrderHandler.php
git commit -m "feat(order): add confirm order use case"
```

Плохо:

```bash
git add .
git commit -m "fix"
```

Плохо:

```bash
git commit -m "WIP"
git commit -m "WIP2"
git commit -m "fixed it"
```

Для меня WIP-коммиты допустимы только локально, но перед PR я их довожу до осмысленного вида.

---

# 5. Pull Request

## Размер PR

Стремлюсь:

```text
до 300 строк — идеально
до 500–700 — приемлемо
более 1000 — плохо
```

Но это не догма. Если изменение по природе большое, лучше:

- разделить на несколько PR;
- сделать черновик;
- обсудить дизайн отдельно;
- показать план.

Почему малый PR лучше:

- его можно ревьюить за 10–30 минут;
- меньше контекста для ревьюера;
- проще найти баг;
- проще откатить;
- быстрее merge.

---

## Структура PR

Я делаю так:

```md
## Summary
Что меняю и зачем.

## Changes
- Добавил ConfirmOrderHandler
- Добавил endpoint /orders/{id}/confirm
- Добавил тесты перехода статуса

## Testing
- unit: OrderTest
- integration: OrderRepositoryTest
- feature: OrderApiTest

## Risks
- Изменён переход статуса draft → confirmed
- Добавлена колонка version

## Rollback
Откатить можно, пока endpoint не используется.
```

Если есть миграция:

```md
## Migration
Expand step:
- добавлена nullable колонка version

Deploy:
- запустить миграцию

Cleanup:
- позже сделать NOT NULL / убрать старый код
```

---

## Чек-лист перед PR

Я проверяю:

- [ ] ветка актуальна с `main`
- [ ] CI зелёный
- [ ] нет мусора: логи, TODO, закомментированный код
- [ ] нет случайных `.env`, секретах, IDE-файлов
- [ ] миграции безопасны
- [ ] новые зависимости обоснованы
- [ ] покрытие критичной логики
- [ ] нет breaking changes без объявления
- [ ] документация обновлена, если API/поведение изменились
- [ ] rollback понятен

---

## Почему PR важен

PR — это не просто «переслал код».

PR — это договорённости:

- зачем меняем;
- что изменилось;
- как проверили;
- как откатить.

Если в PR этого нет, ревью превращается в «найди, что тут не так».

---

# 6. Code Review

## Что я смотрю

### 1. Правильность

- логика соответствует задаче;
- граничные случаи покрыты;
- исключения обработаны осознанно;
- нет скрытых допущений.

Пример:

```php
if ($order->status !== OrderStatus::Draft) {
    throw new InvalidTransitionException();
}
```

Хорошо. А вот:

```php
if (!$order->isDraft()) {
    // тихо продолжаем
}
```

Плохо, если это изменение состояния.

---

### 2. Архитектура

- не нарушены границы слоёв;
- domain не тянет инфраструктуру;
- controller не содержит бизнес-логику;
- repository не решает доменные правила;
- нет лишних абстракций.

---

### 3. Тесты

- покрыты новые правила;
- покрыты ошибки;
- тесты проверяют поведение, а не реализацию;
- нет дублирующих тестов;
- нет тестов, которые «проверяют, что объект создался», без смысловой нагрузки.

---

### 4. Производительность

- нет N+1;
- нет тяжёлых запросов в цикле;
- нет лишних `select *`;
- нет `sleep()`;
- нет синхронных внешних вызовов там, где можно async.

---

### 5. Безопасность

- нет SQL-инъекций;
- нет XSS, если есть HTML;
- авторизация проверена;
- нет утечки данных;
- нет секретах в коде;
- нет unsafe cast'ов;
- input validated.

---

### 6. Миграции

Это критично.

Проверяю:

- не сломается ли прод на большом объёме данных;
- нет ли долгого `ALTER TABLE ADD COLUMN` на горячей таблице;
- нет ли `DROP COLUMN` сразу;
- нет ли `UPDATE` миллионов строк в одной транзакции;
- миграция обратима, если это возможно;
- код совместим со старым и новым форматом.

---

## Правила ревью

### Ревьюер должен ответить:

1. Понимаю ли я, зачем это нужно?
2. Работает ли это в нормальном случае?
3. Что происходит в ошибочных случаях?
4. Как это проверить?
5. Как откатить?

---

### Разделяю уровни замечаний

#### Blocking

Не могу мёрджить:

- баг;
- нарушение авторизации;
- потеря данных;
- сломанные миграции;
- утечка данных;
- отсутствие тестов на критичную логику;
- нарушение архитектуры.

#### Non-blocking

Обсудим, но не блокирую:

- название переменной;
- форматирование, если линтер не ругается;
- спорный стиль;
- мелкая оптимизация.

Почему это важно:

> если все замечания «важные», ревью затягивается.

---

## Почему не ревьюем по строке, а по смыслу

Плохое ревью:

```text
- переименуйте $order в $o
- здесь можно использовать箭头
- лучше через match
```

Понятно, но не критично.

Хорошее ревью:

```text
Здесь возможна гонка: два процесса могут одновременно подтвердить заказ.
Предлагаю добавить optimistic lock через version.
```

или

```text
Эта миграция выполнит UPDATE на 10M строк.
Лучше сделать backfill частями.
```

---

# 7. Merge strategy

## Что я выбираю

### Для `main`:

```text
Squash and merge
```

Или:

```text
Rebase and merge
```

Но чаще — squash.

---

## Почему squash

Было:

```text
feature/JIRA-123-order-confirm
  - feat(order): add confirm
  - test(order): add tests
  - fix(order): adjust status
  - chore: minor
```

После squash:

```text
feat(order): confirm order with idempotency key
```

Это даёт:

- чистую историю `main`;
- один коммит = одна логическая единица;
- проще revert;
- проще читать log;
- проще собирать changelog.

---

## Почему не обычный merge commit

```text
Merge branch 'feature/JIRA-123-order-confirm' into main
```

Это создаёт graph:

```text
A---B---C
     \   \
      D---E
```

История сложнее, revert сложнее, log шумнее.

Для маленьких команд это лишнее.

---

## Что делаю во время review

Пока PR открыт:

```bash
git fetch origin
git rebase origin/main
```

Или:

```bash
git pull --rebase origin main
```

Почему:

- меньше merge-коммитов в ветке;
- история линейнее;
- конфликты видно раньше.

---

# 8. CI/CD

## CI обязательно должна проверять

### Минимально:

```yaml
- composer install
- cs / lint
- phpstan / psalm
- unit tests
- integration tests
- feature tests
- build Docker image
```

### Дополнительно:

```yaml
- migration dry-run
- security audit
- dependency audit
- smoke tests on staging
```

---

## Почему CI — gate

Если можно мёрджить красный PR — git-процесс сломан.

CI должна отвечать:

> можно ли это мёрджить и деплоить?

Ответ должен быть объективным.

Не «попроси у тимлида», а:

```text
CI green → можно мёрджить
CI red  → нельзя
```

---

## Staging

`main` автоматически деплоится на staging.

Почему:

- staging — не «место для ручных экспериментов»;
- staging должен быть предсказуемым;
- staging — проверка интеграции, не разработка.

Если на staging нужно что-то специально, делаем:

```text
main → staging
feature branch → special staging preview, если нужно
```

Но по умолчанию staging = `main`.

---

# 9. Release

## Вариант A: Continuous Deployment

```text
main green → production
```

Подходит, если:

- есть feature flags;
- деплой быстрая;
- rollback простой;
- есть мониторинг;
- изменения небольшие.

---

## Вариант B: Release из main (мой основной)

1. Все изменения в `main`.
2. Перед релизом создаём PR «release/1.4.0».
3. В нём:
   - обновляем changelog;
   - поднимаем версию;
   - проверяем миграции;
   - собираем release notes.
4. Merge в `main`.
5. Тег:

```bash
git tag v1.4.0
```

6. Деплой именно этого тега.

---

## Почему тег

Тег даёт **неизменяемую точку**.

```text
v1.4.0
```

Это конкретное состояние кода.

В production всегда должна быть понятная версия:

```text
APP_VERSION=1.4.0
COMMIT=abcdef123
```

А не:

```text
deploy from main at 15:42
```

---

## Changelog

Собираю из Conventional Commits:

```md
## v1.4.0

### Features
- order: confirm order with idempotency key

### Fixes
- payment: handle gateway timeout

### Chores
- ci: upgrade phpstan
```

Это нужно не для красоты.

Это нужно, чтобы:

- понимать, что изменилось;
- знать, что тестировать;
- объяснять изменения стейкхолдерам;
- откатывать осознанно.

---

# 10. Миграции и деплой

Это отдельная критическая зона.

## Правило: расширение и сужение

Для изменения схемы использую expand/contract.

### Пример: добавляем `version`

#### Step 1: Expand

```sql
ALTER TABLE orders ADD COLUMN version INT NULL;
```

Старый код работает.
Новый код тоже может работать.

#### Step 2: Deploy code

Код уже пишет/читает `version`, если нужно.

#### Step 3: Backfill

```sql
UPDATE orders SET version = 0 WHERE version IS NULL;
```

Если таблица большая — батчами.

#### Step 4: Contract

Позже:

```sql
ALTER TABLE orders ALTER COLUMN version SET NOT NULL;
```

Или:

```sql
ALTER TABLE orders ALTER COLUMN version SET DEFAULT 0;
```

---

## Почему не сразу `NOT NULL`

Потому что если:

- старый код ещё работает;
- он не пишет поле;
- или есть два релиза;
- или есть очереди;

то `NOT NULL` может сломать запись.

---

## Порядок деплоя

Обычно:

1. migration expand;
2. deploy application;
3. проверить;
4. через время contract.

Иногда наоборот, если migration должна быть до кода. Но всегда решаю:

> какой код должен работать до и после миграции.

---

# 11. Feature flags

Использую, когда:

- функциональность готова, но не должна быть видна всем;
- есть риск, что код уже в production;
- нужно отключить быстро;
- есть A/B-тест;
- есть поэтапное включение.

Пример:

```text
ORDER_CONFIRM_V2 = true/false
```

---

## Почему feature flags важны

Они разделяют:

```text
deploy
```

и

```text
enable feature
```

Это два разных решения.

Код можно развернуть безопасно, а поведение включить только когда нужно.

---

## Пример

Было:

```php
if ($this->config->get('order.confirm_v2')) {
    return $this->newConfirmLogic($order);
}

return $this->legacyConfirmLogic($order);
```

Плохо, если это надолго.

Хорошо, если:

```text
deploy с флагом off
включить на 1%
включить на 100%
удалить флаг и старый код
```

---

# 12. Rollback

## Что должно быть

У каждого релиза должен быть план отката.

Минимально:

```text
1. Откатить deploy к предыдущему тегу.
2. Проверить, что миграция совместима.
3. При необходимости выключить фичу флагом.
```

---

## Почему важно с самого начала

Если нет плана отката, при падении начинаем:

- гуглить;
- править;
- деплоить hotfix;
- паниковать.

Лучше знать заранее:

> что мы откатим?
> что не откатится?
> какие данные можно потерять?

---

# 13. Hotfix

## Процесс

```text
main
 └── fix/JIRA-130-payment-timeout
```

1. Создаю ветку из `main`.
2. Правлю.
3. Тесты.
4. PR.
5. CI.
6. Merge в `main`.
7. Релиз:

```text
v1.4.1
```

8. Deploy.

---

## Если есть release branch

Если релиз уже ушёл в production и `main` двигается дальше, может потребоваться backport:

```text
main → v1.4.x
```

Но по умолчанию стараюсь держать `main` стабильной, чтобы не было долгой ветки `v1.4.x`.

---

# 14. Защита веток

## Rules для `main`

```text
- no direct push
- no force push
- require PR
- require CI green
- require 1-2 approvals
- no self-merge, если возможно
- require branch up to date
- protected tags
```

---

## Почему no direct push

Прямой push в `main` означает:

- нет review;
- нет CI;
- нет объяснения;
- нет плана отката;
- нет changelog.

Это нормально для emergency, но должно быть исключением.

---

# 15. История и чистота

## Я не стараюсь:

- переписывать общую историю;
- делать идеальные коммиты задним числом;
- гнать интерактивный rebase для всего.

## Я стараюсь:

- маленькие PR;
- осмысленные коммиты;
- squash в main;
- удалять ветки после merge;
- не плодить мёртвые ветки.

---

## Пример полного цикла

### Задача

Добавить подтверждение заказа.

---

### Шаг 1: ветка

```bash
git checkout main
git pull
git checkout -b feature/JIRA-123-order-confirm
```

---

### Шаг 2: коммиты

```bash
git add src/Domain/Order/Order.php
git commit -m "feat(order): add confirm transition"

git add tests/Unit/Domain/Order/OrderTest.php
git commit -m "test(order): cover confirm transition"

git add src/Application/Order/ConfirmOrderHandler.php
git commit -m "feat(order): add confirm order use case"

git add src/Presentation/Http/Order/ConfirmOrderController.php
git commit -m "feat(order): add confirm endpoint"
```

---

### Шаг 3: PR

```text
feat(order): confirm order

Adds order confirmation endpoint.
Confirms only draft orders.
Adds idempotency key to avoid duplicate confirmations.

Testing:
- unit
- integration
- feature

Risks:
- changes order status transition
```

---

### Шаг 4: CI

```text
green
```

---

### Шаг 5: review

Ревьюер:

```text
1. Хорошо, что есть доменное правило.
2. Убедись, что есть защита от double-confirm.
3. Добавь интеграционный тест на повторный confirm.
```

---

### Шаг 6: доработка

```bash
git commit -m "fix(order): prevent double confirmation"
```

---

### Шаг 7: merge

```text
Squash and merge:
feat(order): confirm order with idempotency key
```

---

### Шаг 8: деплой staging

```text
v1.4.0-rc
```

---

### Шаг 9: production release

```bash
git tag v1.4.0
git push origin v1.4.0
```

Deploy:

```text
APP_VERSION=1.4.0
COMMIT=abcdef123
```

---

### Шаг 10: верификация

```text
- endpoint работает
- повторный confirm возвращает 409 / idempotent response
- логи чистые
- метрики ок
```

---

# 16. Почему так, а не иначе

## 1. Короткие ветки

Потому что длинные ветки:

- накапливают конфликты;
- скрывают интеграцию;
- усложняют ревью;
- делают деплой менее предсказуемым.

---

## 2. Small PR

Потому что большой PR:

- тяжело ревьюить;
- легко упустить баг;
- долго мёрджится;
- плохо откатывается.

---

## 3. Conventional Commits

Потому что история кода должна быть полезной:

- changelog;
- semver;
- поиск изменений;
- понимание причин.

---

## 4. Squash в main

Потому что `main` должна читаться как список значимых изменений:

```text
feat(order): confirm order
fix(payment): handle gateway timeout
chore(ci): upgrade phpstan
```

А не:

```text
fix
wip
update
merge branch
another merge
```

---

## 5. CI как gate

Потому что «проверим после деплоя» — не процесс.

Green CI:

- экономит время;
- снижает риск;
- делает деплой предсказуемым.

---

## 6. Теги и версии

Потому что production должен знать:

```text
что именно запущено
какой коммит
какая версия
как откатиться
```

---

## 7. Feature flags

Потому что deploy и enable — разные вещи.

Код можно безопасно развернуть, а фичу включить позже.

---

# 17. Типовые ошибки в git-процессе

## 1. Долгая feature-ветка

```text
3 недели
```

Потом:

```text
rebase hell
```

Решение:

- малые PR;
- частые merge'и в main;
- feature flags.

---

## 2. «Правим в проде напрямую»

Иногда нужно, но это:

- нет review;
- нет теста;
- нет changelog;
- нет отката.

Если hotfix — делаем через PR и релиз.

---

## 3. Большой PR «за всё»

```text
3000 строк
```

Решение:

- разделить;
- сначала инфраструктура;
- потом фича;
- потом тесты.

---

## 4. Коммит «fix»

```text
fix
```

Не несёт информации.

---

## 5. Нет плана миграции

```sql
ALTER TABLE orders DROP COLUMN old_status;
```

на проде, где старый код ещё пишет в него.

---

## 6. Ревью по мелочам

Если ревьюер тратит время на имена переменных, но пропускает race condition — процесс не работает.

---

# 18. Итоговая модель

```text
Task
  ↓
branch from main
  ↓
small commits
  ↓
PR
  ↓
CI: lint + static analysis + tests
  ↓
review: correctness, architecture, tests, migration, rollback
  ↓
rebase to main
  ↓
squash merge into main
  ↓
staging deploy
  ↓
verify
  ↓
tag release
  ↓
production deploy
  ↓
monitor
  ↓
rollback if needed
```

---

## Коротко

Я использую:

| Элемент | Подход |
|---------|--------|
| Ветки | короткие feature-ветки из `main` |
| Main | всегда стабильный |
| Коммиты | Conventional Commits |
| PR | маленькие, с описанием и тестами |
| Ревью | по смыслу, не по строке |
| Merge | squash в `main` |
| CI | обязателен как gate |
| Staging | деплой `main` |
| Release | тег из `main` |
| Rollback | план заранее |
| Feature flags | для рискованных изменений |

Почему так:

> этот процесс минимизирует время интеграции, снижает риск production-сбоев и делает откат управляемым.

Я сознательно выбираю не «максимально строгий», а **практичный** процесс:

- достаточно строгий, чтобы не сломать prod;
- достаточно гибкий, чтобы не превратиться в бюрократию.

> ⏱ 78.8s · prompt 99689 tok · output 7667 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о том, как я организую код-ревью: маркировка замечаний, приоритеты, разрешение конфликтов с автором. Это продолжение темы git-процесса из прошлого ответа. Нужно дать конкретный, практичный ответ — не учебник, а реальный процесс.

Структурирую так:
1. Маркировка замечаний (blocking / non-blocking / nit / question) — с примерами
2. На что смотрю в первую очередь (приоритеты)
3. Как пишу комментарий (формат)
4. Разногласие с автором — как веду
5. Антипаттерны в ревью

Важно: не повторять то, что уже было в прошлом ответе про git-процесс. Там был общий обзор. Здесь — детализация именно процесса ревью: как формулирую, как классифицирую, как веду диалог.

Дам конкретные примеры формулировок, чтобы было видно разницу между плохим и хорошим комментарием.

</details>

**Кандидат:**

## Код-ревью: мой процесс

### Маркировка замечаний

Использую четыре уровня. Они видны в PR-комментарии.

| Маркер | Смысл | Обязателен к исправлению? |
|--------|-------|--------------------------|
| **🔴 blocking** | Без исправления не мёрджим | Да |
| **🟡 should** | Должно быть исправлено, но можно обсудить | Да, но есть пространство |
| **⚪ nit** | Предпочтение, не ошибка | Нет |
| **❓ question** | Я не понял, объясни | Ответ, не обязательно изменение кода |

Почему именно так:

- **Blocking** — объективный критерий. "Это сломает prod" или "тут утечка данных". Не мнение.
- **Should** — я уверен, что так лучше, но автор может показать, что я не видел контекста.
- **Nit** — я бы сделал иначе, но не буду блокировать. Автор решает.
- **Question** — я не утверждаю, что код плохой. Я не понимаю. Это разные вещи.

#### Пример в PR

```diff
- $this->em->flush();
- $this->sendEmail($order);
+ $this->em->wrapInTransaction(function () use ($order): void {
+     $this->em->flush();
+ });
+ $this->sendEmail($order);

🔴 blocking: sendEmail вне транзакции — ок, но если flush упадёт,
email не уйдёт. А если email упадёт — flush уже commit'ился.
Это нормально? Если да — ок, снимаю. Если нет — нужен outbox.
```

```diff
- public function getName(): string
+ public function name(): string

⚪ nit: я бы без get, но не блокирую.
```

```diff
- $result = $this->service->process($data);
- return $result;
+ return $this->service->process($data);

⚪ nit: можно в одну строку.
```

```diff
- if ($user->hasRole('ROLE_ADMIN')) {
+ if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {

❓ question: почему сменил hasRole на in_array?
hasRole умеет делать IS_IMPERSONATOR. Это осознанно?
```

---

### На что смотрю в первую очередь

Приоритет — от критичного к эстетике.

#### 1. Правильность (blocking)

Это то, что сломает пользователей.

| Что ищу | Пример |
|---------|--------|
| Баг в логике | `if ($amount < 0)` вместо `if ($amount <= 0)` |
| Гонка | два процесса одновременно пишут |
| Потеря данных | `flush()` до `save()`, `commit` до валидации |
| Утечка данных | `SELECT * FROM users` в публичном API |
| Авторизация | проверка роли отсутствует |
| SQL-инъекция | raw query с конкатенацией |
| Необрабатываемое исключение | `catch (\Exception)` без log |
| Бесконечный цикл | `while (true)` без выхода |

Это 90% blocking-замечаний.

#### 2. Архитектура (blocking / should)

| Что ищу | Пример |
|---------|--------|
| Нарушение слоя | Controller вызывает `EntityManager` напрямую |
| Domain тянет инфраструктуру | `Order.php` содержит `use Doctrine\ORM\EntityManager` |
| Годи-объект | Класс на 400 строк, 6 ответственностей |
| Дублирование | То же правило в 3 местах |
| Избыточная абстракция | 5 классов ради одной функции |
| Отсутствие границы | Service знает про 10 других сервисов |

#### 3. Тесты (should)

| Что ищу | Пример |
|---------|--------|
| Нет теста на новый баг | Добавили правило, не покрыли |
| Тест на реализацию | `assertTrue($repo->wasCalled())` вместо проверки поведения |
| Тест дублирует код | Тест повторяет логику, а не проверяет результат |
| Нет edge case | Пустой массив, null, отрицательное число |
| Flaky-тест | Зависит от времени, порядка, внешних сервисов |

#### 4. Производительность (should / nit)

| Что ищу | Пример |
|---------|--------|
| N+1 | `foreach` с `$em->getRepository()->find()` внутри |
| `SELECT *` | 30 колонок, из них используется 2 |
| `OFFSET` на большой странице | `LIMIT 20 OFFSET 100000` |
| Тяжёлый запрос в цикле | `$em->flush()` в `foreach` |
| Отсутствие индекса | `WHERE` по колонке без индекса |

#### 5. Безопасность (blocking)

| Что ищу | Пример |
|---------|--------|
| Секрет в коде | `api_key = 'sk_live_...'` |
| Нет валидации input | `$_GET['id']` без sanitize |
| XSS | `echo $user->input;` без escape |
| CSRF | форма без token |
| Race condition на money | баланс без `FOR UPDATE` |

#### 6. Читаемость (nit / question)

| Что ищу | Пример |
|---------|--------|
| Имена | `$d`, `$tmp`, `$x` |
| Длина метода | 80 строк, 6 веток |
| Магические числа | `if ($x > 42)` |
| Закомментированный код | 20 строк `// old code` |
| TODO без ссылки | `// TODO: fix later` |

---

### Как я пишу комментарий

#### Структура

```
[маркер] Кратко что не так.
Почему это проблема (1 предложение).
Предлагаю: [конкретика].
```

#### Хорошо

```
🔴 blocking: email отправляется до commit.
Если commit упадёт, письмо уйдёт, а заказа не будет.
Предлагаю: отправить после commit через outbox.
```

#### Плохо

```
Здесь что-то не так. Не нравится мне.
```

#### Хорошо

```
❓ question: почему здесь raw SQL, а не DQL?
Вижу, что нужно JOIN 3 таблицы. DQL не справился?
```

#### Плохо

```
Здесь плохо написано.
```

#### Хорошо

```
⚪ nit: $user->getRoles() вызывается в цикле.
Можно вытащить за цикл:
  $roles = $user->getRoles();
  foreach (...) { if (in_array('ROLE_ADMIN', $roles, true)) { ... } }
```

#### Плохо

```
Оптимизируй.
```

---

### Правило: один комментарий = одна проблема

Плохо:

```
Здесь много проблем:
1. не тот тип
2. нет null-check
3. медленный
4. не читается
```

Хорошо:

```
🔴 blocking: нет null-check. $order может быть null из repo->find().
```

```
🟡 should: $result->toArray() вызывается в цикле.
Можно один раз за циклом.
```

```
⚪ nit: имя $x не несёт смысла.
```

---

### На что НЕ трачу время в ревью

| Что | Почему |
|-----|--------|
| Форматирование | Это делает линтер (ECS, PHP-CS-Fixer). Не человек. |
| `use`-порядок | IDE / линтер. |
| Имена по PEP8-аналогу | Если линтер не ругается — ок. |
| "Мне не нравится стиль" | Без конкретной проблемы — не блокирую. |
| Спор о PHP vs TypeScript | Не в PR. |
| "А почему не через DI?" | Если код работает и тестируемый — ок. |

---

### Разногласие с автором

Это самый важный момент. Код-ревью — не экзамен. Автор знает контекст, которого у меня нет.

#### Принцип

> Я не "главнее". Я "другой взгляд".

#### Как веду

**1. Начинаю с вопроса, не утверждения.**

Плохо:

```
Здесь неправильно. Нужно сделать через Strategy.
```

Хорошо:

```
❓ question: почему здесь if/else на method?
Предполагаю, что способов оплаты будет 5+.
Если да — Strategy проще. Если нет — ок, оставляем.
```

**2. Даю автору право отклонить.**

```
⚪ nit: я бы вынес в отдельный метод.
Но если не хочешь — не блокирую.
```

Это не слабость. Это сигнал: "я не диктую, я предлагаю".

**3. Если автор не согласен — слушаю.**

Автор говорит: "Я пробовал Strategy, но там было 3 класса и 50 строк. if/else — 15 строк. Оставил if/else."

Я отвечаю:

```
Понял. Для 2 вариантов — ок. Когда будет 3-й — переделаем.
Снимаю замечание.
```

И снимаю. Не "ну ладно, делай как хочешь", а "я пересмотрел, твой аргумент сильнее".

**4. Если я всё-таки считаю, что мой вариант лучше — даю данные.**

```
🟡 should: я понимаю, что сейчас 2 варианта.
Но в roadmap на квартал — 4 способа оплаты.
Strategy сейчас = +20 строк. Если-через-месяц-4-варианта = +100 строк в if/else.
Мне кажется, стоит сейчас. Но если ты уверен, что 3-го не будет — ок.
```

Не "я прав", а "вот данные, реши сам".

**5. Эскалация.**

Если не сошлись:

- Говорим голосом / на встрече (5 минут).
- Не пишем 20 сообщений в PR.
- Если и там не сошлись — решаем тимлид / старший разработчик.
- Не "я пойду к тимлиду", а "давай спросим, у кого больше контекста".

**6. После решения — фиксирую.**

```
Решено: оставляем if/else. Когда будет 3-й способ — переделаем на Strategy.
[автор: +1]
[ревьюер: +1]
```

Это не бюрократия. Это чтобы через 3 месяца кто-то не спросил "почему тут if/else?".

---

### Антипаттерны в ревью

#### 1. Ревью через 3 дня

```
PR создан: 10.01
Первый комментарий: 13.01
```

Почему плохо: автор уже забыл контекст. Нужно перечитывать 500 строк, чтобы понять "а что я тут делал?".

**Правило:** первый ответ в течение 24 часов. Хотя бы "смотрю, вернусь завтра".

#### 2. Ревью только по именам

```
- переименуй $order в $o
- здесь лучше match
- используй readonly
```

Без объяснения "почему" и без разницы в поведении.

Почему плохо: автор тратит время на эстетические правки, а баг на 47-й строке не замечен.

#### 3. "LGTM" без чтения

```
LGTM ✅
```

После чего через 2 недели баг в проде.

Почему плохо: ревью не было. "LGTM" = "я не читал, но мёрджим".

#### 4. Ревью как экзамен

```
Здесь не так.
Здесь не так.
Здесь тоже не так.
В целом плохо.
```

Без одного "что хорошо".

Почему плохо: автор боится присылать PR. Перестаёт делать маленькие PR. Делает один большой. И ревью становится ещё сложнее.

#### 5. Ревью в чате, не в PR

```
[чат]: "Смотри, тут на 47-й строке баг"
```

Почему плохо: не зафиксировано. Через месяц никто не помнит. Не видно в истории PR.

**Правило:** замечания — в PR. Чат — для обсуждения.

#### 6. "Я бы сделал иначе" без предложения

```
Здесь не то.
```

Без "что именно" и "как лучше".

Почему плохо: автор тратит время на догадки. Лучше 2 минуты написать предложение, чем 20 минут "а что он имел в виду?".

---

### Мой чек-лист при ревью

```
1. Понимаю ли я, ЗАЧЕМ это нужно?
   → Если нет: ❓ question в первый комментарий.

2. Что делает код в нормальном случае?
   → Читаю основной путь.

3. Что происходит при ошибке?
   → Ищу catch, throw, null.

4. Есть ли гонка?
   → Ищу concurrent-сценарии.

5. Правильная ли архитектура?
   → Слои не нарушены? Domain чист?

6. Есть ли тесты?
   → Новые правила покрыты?

7. Безопасность?
   → Авторизация, валидация, секреты.

8. Производительность?
   → N+1, тяжёлые запросы.

9. Миграция?
   → Безопасна? Расширяющая?

10. Rollback?
   → Понятен план отката?
```

Если хотя бы один пункт — "не знаю" — я не пишу "LGTM".

---

### Итого

| Аспект | Как делаю |
|--------|-----------|
| Маркировка | 🔴 blocking / 🟡 should / ⚪ nit / ❓ question |
| Приоритет | Правильность → Архитектура → Тесты → Perf → Безопасность → Стиль |
| Формулировка | Проблема + почему + предложение |
| Один комментарий | Одна проблема |
| Разногласие | Вопрос, не утверждение. Автор решает. Данные, не "я прав" |
| Скорость | Первый ответ < 24 часа |
| Эскалация | Голосом, не письмом. Тимлид как арбитр, не судья |
| Фиксация | "Решено: ..." в PR |

**Главное правило:**

> Ревью — это не "я проверяю твой код".
> Ревью — это "мы вместе ищем, что может сломаться".

Если автор чувствует, что я "судья" — он перестанет присылать маленькие PR. А маленькие PR — это то, что делает систему надёжной.

> ⏱ 42.2s · prompt 106528 tok · output 3710 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

<details><summary>Ход мысли модели</summary>

Пользователь просит меня взять сырую бизнес-проблему и превратить её в чёткую, исполнимую задачу. Это классический сценарий на собеседовании: проверяют, умеет ли разработчик переводить "болото" в конкретную работу.

Что здесь важно:
1. Сырая проблема: "пользователи не находят заказы из прошлого, поддержка завалена". Это не задача. Это симптом.
2. Нужно показать, что я НЕ начинаю сразу "делать поиск". Сначала — диагностика, гипотезы, критерии.
3. Формулировка задачи должна быть так, чтобы исполнителю не нужно было уточнять. Значит: контекст, цель, критерии приёмки, ограничения, что НЕ делаем, приоритеты.
4. Нужно показать, что я понимаю: "не могут найти" может означать 5 разных вещей (нет UI, есть UI но медленно, есть UI но не найдётся по фильтру, заказы не сохраняются, заказы в другой системе).
5. Обязательные элементы: цель (измеримая), гипотезы, критерии приёмки, ограничения, что вне scope, тайминг, owner, способ верификации.

Структурирую ответ:
- Сначала покажу, почему сырая проблема — это не задача (кратко).
- Затем дам готовую формулировку задачи (как если бы я её написал в Jira/трекере).
- После каждого блока объясню, почему это включено.
- В конце — что я бы спросил ДО того, как писать задачу (если бы это был живой разговор).

Формулировка должна быть конкретной, с примерами, с критериями, с ограничениями. Не абстрактная.

Язык — русский, как и весь разговор.

</details>

**Кандидат:**

## Почему сырая проблема — это не задача

"Пользователи не находят заказы из прошлого" — это **симптом**, не задача. Из него можно сделать 7 разных задач, и все будут выглядеть правдоподобно:

| Возможная причина | Задача |
|-------------------|--------|
| Нет UI для поиска | Спроектировать и построить страницу истории заказов |
| Есть UI, но медленный | Оптимизировать запрос, добавить индекс |
| Есть UI, но фильтры не работают | Починить фильтры по дате/статусу |
| Заказы не сохраняются (баг) | Найти и починить потерянные записи |
| Заказы в другой системе (legacy) | Спроектировать миграцию или read-model |
| Пользователь не понимает, где искать | UX-изменение, онбординг |
| Поддержка отвечает медленно (не про поиск) | Улучшить SLA поддержки, добавить self-service |

Если я напишу исполнителю "сделай так, чтобы пользователи находили заказы" — он сделает **одну** из этих семи. Может не ту.

---

## Готовая формулировка задачи

Ниже — то, что я бы положил в трекер. Я сознательно делаю её длинной: длина здесь = отсутствие уточнений.

---

### Задача: Self-service поиск истории заказов

#### Контекст

Пользователи не могут самостоятельно найти свои прошлые заказы. Текущий канал — обращение в поддержку (email/чат). По данным за октябрь:

- 340 обращений/неделю по теме "где мой заказ / история заказов".
- Среднее время ответа поддержки — 4 часа.
- 60% обращений — "где заказ за [месяц]" (поиск по дате).
- 25% — "где чек / invoice за заказ [номер]".
- 15% — "почему заказ не виден" (возможная потеря данных).

Цель: **снизить обращения в поддержку по этой теме на 70%** (с 340 до ≤ 100/неделю) в течение 4 недель после запуска.

---

#### Что делаем (scope)

1. **Страница истории заказов** (`/account/orders`):
   - Список заказов текущего пользователя, отсортированных по дате создания (новые сверху).
   - Фильтры: по статусу (все / активные / завершённые / отменённые), по периоду (месяц / квартал / год).
   - Пагинация: 20 заказов на страницу.
   - По каждому заказу: номер, дата, статус, сумма, количество позиций.
   - Клик по заказу → страница деталей (уже существует, `/orders/{id}`).

2. **Поиск по номеру заказа**:
   - Поле поиска на странице истории: "Найти по номеру".
   - Точное совпадение по `orders.number`.
   - Результат: 0 или 1 заказ.

3. **Скачивание invoice** (если уже существует на странице деталей — не делаем заново; если нет — добавить PDF-ссылку на странице деталей).

---

#### Что НЕ делаем (out of scope)

| Что | Почему |
|-----|--------|
| Поиск по названию товара внутри заказа | Это другая задача. Нужен full-text / search engine. |
| Миграция заказов из legacy-системы | Если есть — отдельная задача. Здесь: только данные, которые уже есть в `orders`. |
| Push-уведомления о новых заказах | Отдельная фича. |
| Мобильное приложение | Только web. |
| Изменение API внешнего партнёра | Не трогаем. |

---

#### Критерии приёмки

Функциональные:

- [ ] Пользователь с ролью `customer` видит только **свои** заказы.
- [ ] Пользователь **не** видит чужие заказы (проверить через API: `GET /orders` возвращает только `customer_id = current_user`).
- [ ] Фильтр по статусу работает: `?status=delivered` возвращает только delivered.
- [ ] Фильтр по периоду работает: `?from=2024-01-01&to=2024-03-31`.
- [ ] Поиск по номеру: `?number=ORD-2024-001` → 200, если найден; 404, если нет.
- [ ] Пагинация: `?page=2` возвращает следующую страницу. `?page=99999` → пустой список, не ошибка.
- [ ] Пустое состояние: если заказов нет — заглушка "Заказов пока нет".
- [ ] Производительность: `GET /account/orders?limit=20` ответ < 300ms на p95 (на продакшен-объёме, 1M заказов).

Нефункциональные:

- [ ] Нет N+1 (проверить через `EXPLAIN ANALYZE` или query counter в тесте).
- [ ] Авторизация: не-авторизованный пользователь → 401.
- [ ] Авторизация: пользователь A не видит заказы B → 403.
- [ ] Нет `SELECT *` — только нужные колонки.
- [ ] Индекс на `(customer_id, created_at DESC)` существует.
- [ ] Код покрыт: unit (домен), integration (repository), feature (API).
- [ ] CI зелёный: lint, PHPStan, тесты.

---

#### Ограничения

| Ограничение | Детали |
|-------------|--------|
| БД | PostgreSQL 16, таблица `orders` ~1M строк, `order_lines` ~5M. |
| Не меняем схему БД | Только read-only. Если нужен индекс — migration, expand-only. |
| Не трогаем существующий API `/orders/{id}` | Только добавляем `GET /account/orders`. |
| Стек | Symfony 7, PHP 8.3, Doctrine ORM, Twig. |
| Фронтенд | Twig-шаблон, не React. JS — vanilla, до 50 строк. |
| Тайминг | 5 рабочих дней, включая code review и деплой на staging. |
| Деплой | Через PR → CI → `main` → staging. Нет прямого деплоя. |

---

#### Что нужно проверить ПЕРЕД началом работы

Исполнитель делает **не** "начать кодить", а:

1. Открыть `/account/orders` в staging. Посмотреть, что уже есть.
2. Проверить, есть ли уже endpoint `GET /account/orders` (может быть, есть, но скрыт).
3. Проверить `pg_stat_statements` за прошлую неделю: есть ли уже запросы к `orders` по `customer_id`. Если да — какая нагрузка.
4. Проверить, есть ли индекс `(customer_id, created_at)`. Если нет — это первая миграция.
5. Проверить, сколько заказов у "типичного" пользователя (mediana). Если 5 — пагинация не нужна. Если 500 — нужна.
6. Проверить, есть ли заказы с `customer_id = NULL` (потерянные). Если да — это **отдельная** задача на поддержку, не на разработку.

---

#### Способ верификации

| Что | Как |
|-----|-----|
| Функциональность | Feature-тесты: `OrderHistoryApiTest` (6 кейсов). |
| Производительность | `EXPLAIN ANALYZE` на staging с реальным объёмом. |
| Безопасность | Ручная проверка: логин как user A → запрос заказов user B → 403. |
| UX | Ручной прогон на staging с 3 тестировщиками из поддержки. |
| Бизнес-метрика | Через 2 недели: сравнить число обращений в поддержке (340 → ?). |

---

#### Риск и откат

| Риск | Митигация |
|------|-----------|
| Индексация 1M строк замедлит prod | Создание индекса `CONCURRENTLY`, вне пиковой нагрузки. |
| Страница медленная из-за N+1 | Query counter в интеграционном тесте. Блокирует PR. |
| Пользователи не найдут страницу | Ссылка в email-подтверждении + баннер "Где ваши заказы?" на 1 неделю. |
| Обращений не стало меньше | Значит, причина не в UI. Эскалирую бизнесу с данными. |

**Откат:** отключить ссылку на страницу (feature flag `ORDER_HISTORY_V2 = false`). Код в `main`, но не виден.

---

## Почему именно так

| Что включил | Почему |
|-------------|--------|
| Конкретная метрика (340 → 100) | Исполнитель знает, **когда задача сделана**. Без метрики — "сделал, но не сработало". |
| Scope + out of scope | Без out of scope исполнители "попутно" делают 3 лишние вещи. |
| Критерии приёмки в чек-листе | Ревьюер проверяет по списку, а не "в целом ок". |
| Ограничения (БД, стек, тайминг) | Исполнитель не будет предлагать Elasticsearch на 2-й день. |
| "Что проверить перед началом" | 80% задач начинают с кода, не с диагностики. Это убивает время. |
| Риск и откат | Если не сработает — мы знаем, что делать. Не паникуем. |
| Способ верификации | Не "покажу в демо", а "тест зелёный, метрика снизилась". |
| Цифры (1M строк, 300ms) | Без цифр "быстро" = "я думаю, что быстро". |

---

## Что я бы спросил ДО того, как писать задачу

Если бы это был живой разговор с бизнесом:

1. **"А что пользователи делают СЕЙЧАС, когда не находят заказ?"**
   → Если пишут в поддержку — ок, задача как выше. Если звонят — нужно другое (call center). Если не делают ничего — проблема не в поиске, а в осведомлённости.

2. **"Сколько заказов в среднем на пользователя?"**
   → Если 3 — пагинация не нужна. Если 500 — нужен серьёзный поиск.

3. **"Есть ли заказы, которые 'потерялись' (customer_id = NULL)?"**
   → Если да — 15% обращений не решаются UI. Это задача на данных.

4. **"Когда последний раз смотрели, сколько обращений именно по поиску, а не по 'где деньги'?"**
   → 340/неделю — это всё по "заказам" или именно "не нахожу"?

5. **"Где заказ 'живёт'? В нашей БД или в партнёрской системе?"**
   → Если в партнёрской — задача не на UI, а на интеграцию.

Эти 5 вопросов занимают 10 минут и могут **убрать** 50% работы, которую я бы зря сделал.

---

## Коротко

Сырая проблема → задача. Между ними:

```
Сырая:        "Пользователи не находят заказы"
                     ↓
Диагностика:   Что именно? Сколько? Где данные? Какой канал?
                     ↓
Гипотеза:      Нет UI + медленный запрос + часть данных в legacy
                     ↓
Задача:        Scope + метрика + критерии + ограничения + откат
                     ↓
Исполнитель:   Делает без уточнений. Ревьюер проверяет по чек-листу.
                     ↓
Верификация:   340 → 100 обращений/неделю. Или эскалация с данными.
```

Главное правило:

> Задача, которую можно выполнить без уточнений, — это задача, в которой **у исполнителя не остаётся выбора, что делать**. Выбор — это контекст, метрики, ограничения и "что НЕ делаем". Без этого выбора исполнителем будет сделан **чужой** выбор, и он может не совпасть с бизнес-целью.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

## Вопросы заказчику до начала работы

Я делаю их не для галочки «уточнил ТЗ». Каждый вопрос закрывает конкретную гипотезу, и от ответа зависит, **какая задача** вообще нужна.

Разбиваю на блоки. Внутри каждого — вопрос, зачем он, и что меняет ответ.

---

### Блок 1. Проблема — или её симптом

**1. «Что именно делает пользователь, когда не находит заказ?»**

Зачем: «не могут найти» может означать 5 разных вещей — не знают, где искать; увидели список, но не тот; увидели, но нет нужного поля; заказ физически отсутствует; заказ в другой системе.

Меняет решение:
- Если «не знают, где» → задача на UX, не на поиск.
- Если «видят список, но нет нужного» → задача на фильтры/показ полей.
- Если «заказ не виден вообще» → возможно, потеря данных. Это совсем другая задача.

---

**2. «Какие обращения в поддержку ты видишь глазами? Покажи 10 последних.»**

Зачем: «340 обращений по поиску» — это агрегат. В реальном тексте может быть «заказ не пришёл», «списали дважды», «не могу скачать чек». Под одной рубрикой «не нахожу» — 5 разных проблем.

Меняет решение: если 50% обращений — на самом деле «не пришёл», то задача про поиск решит только 50% жалоб. Бизнес-метрика не будет достигнута.

---

**3. «Когда и как пользователю говорят, что заказ есть?»**

Зачем: если после оплаты нет письма с номером и ссылкой — пользователь не может его найти, даже если поиск идеален.

Меняет решение: может быть, половина работы — не поиск, а email-уведомление + ссылка в нём.

---

### Блок 2. Пользователи и данные

**4. «Сколько заказов в среднем на одного пользователя? Медиана и 95-й перцентиль?»**

Зачем:
- Если медиана 3 — пагинация и фильтры не нужны, список из 3 строк.
- Если 95-й перцентиль 500 — нужна пагинация, поиск, фильтры.

Меняет решение: объём работы. Простая таблица vs. полноценный поиск.

---

**5. «Есть ли у нас пользователи, у которых заказы с `customer_id = NULL` или привязаны к другому аккаунту?»**

Зачем: это «потерянные» заказы. Их не найдёт ни один UI.

Меняет решение: если их много — задача на данные (миграция, реинжиниринг аккаунтов), а не на фичу. Иначе фича не решит 15–20% жалоб.

---

**6. «Есть ли заказы в legacy-системе, которые не переехали в текущую БД?»**

Зачем: «прошлые заказы» может значить «заказы за 2 года до миграции». Их в `orders` может просто не быть.

Меняет решение: если да — задача = read-model из legacy + поиск по нему. Без этого UI бесполезен для части пользователей.

---

**7. «Один пользователь — один аккаунт? Есть ли дубли аккаунтов?»**

Зачем: пользователь может помнить, что регистрировался на рабочую почту, а заказ был на личную. Для системы это два разных `customer_id`.

Меняет решение: если дублей много — задача на объединение аккаунтов, не на поиск.

---

### Блок 3. Текущее состояние системы

**8. «Что уже есть? Страница, API, email, PDF?»**

Зачем: 40% таких задач — это «сделали 2 года назад, никто не знает, ссылка потерялась».

Меняет решение: если есть — задача на UX-изменение и аналитику, не на разработку.

---

**9. «Сколько заказов в `orders` и `order_lines` сейчас?»**

Зачем: 10k и 1M — разные задачи. Для 10k не нужны ни индекс, ни пагинация. Для 1M — обязательны.

Меняет решение: архитектура, производительность, бюджет времени.

---

**10. «Есть ли уже индекс на `(customer_id, created_at)`?»**

Зачем: если нет — первый запрос будет slow. Если есть — вопрос закрыт.

Меняет решение: нужен ли migration в scope.

---

**11. «Где сейчас логируется, что пользователь искал и не нашёл?»**

Зачем: если есть аналитика «пустой поиск» — можно количественно доказать, что проблема именно в поиске, а не в UX.

Меняет решение: если аналитики нет — часть задачи = добавить её, чтобы через месяц понимать, сработало ли.

---

### Блок 4. Бизнес-метрика и успех

**12. «Как ты будешь понимать, что задача решена? Какую метрику смотрим?»**

Зачем: без этого мы делаем «страницу заказов» и говорим «готово». А бизнес ждёт «меньше обращений в поддержку».

Меняет решение: если метрика — «количество обращений» — нужно измерять ДО и ПОСЛЕ. И понимать, что 20% обращений — это не поиск.

---

**13. «Какую экономию ты ждёшь? Сколько стоит обращение в поддержку?»**

Зачем: если 1 обращение = 15 минут оператора = 500₽, а 340/неделю — это ~680 000 ₽/мес. Это обосновывает задачу. Если 50₽ — может, не стоит делать.

Меняет решение: приоритет, объём, сроки.

---

**14. «Когда нужно? Что произойдёт, если не сделаем в этот срок?»**

Зачем: «срочно» часто означает «на этой неделе». Но если через 2 недели будет крупный релиз — лучше до него.

Меняет решение: timeline, scope (можно сузить).

---

### Блок 5. Ограничения

**15. «Кто может менять `orders` прямо сейчас? Есть ли процессы, которые могут сломаться?»**

Зачем: если мы добавим read-only-интерфейс — ок. Если начнём менять данные (например, объединение дублей) — риск для других фич.

Меняет решение: scope, тесты, риск-менеджмент.

---

**16. «Есть ли требования по приватности? PDPA / 152-ФЗ?»**

Зачем: если заказы содержат персональные данные (адрес, email) — нужен аудит доступа, логирование, TTL.

Меняет решение: архитектура, доступы, логи.

---

**17. «Какой стек? Можно ли использовать новый search-engine или только то, что уже есть?»**

Зачем: если есть Elasticsearch — задача проще. Если нет и нельзя ставить — только SQL.

Меняет решение: технология, сроки, команда.

---

### Блок 6. Организационные

**18. «Кто принимает работу? Кто нажимает 'готово'?»**

Зачем: без этого мы сдаём разработчику, а он «не согласен с бизнесом».

Меняет решение: процесс приёмки, критерии.

---

**19. «У нас есть staging, похожий на prod по объёму?»**

Зачем: без этого невозможно проверить производительность на реальных данных.

Меняет решение: как верифицируем.

---

**20. «Кто из поддержки может прилететь на 30 минут и посмотреть прототип?»**

Зачем: 10 минут с оператором поддержки > 10 часов в коде. Они знают, что именно спрашивают пользователи.

Меняет решение: scope, формулировки в UI.

---

## Как я их использую

Не все 20 задаю сразу. Порядок:

```
1, 2, 4, 5, 8, 12
```

Это 7 вопросов. Они дают:
- реальную причину проблемы (1, 2);
- объём данных (4);
- есть ли «потерянные» (5);
- что уже есть (8);
- как измерять успех (12).

Если после этих 7 становится ясно — остальное уточняю по ходу.

---

## Что я НЕ спрашиваю

| Вопрос | Почему |
|--------|--------|
| «Сколько людей в команде?» | Это я знаю. |
| «Какой фреймворк?» | Я знаю. |
| «Когда дедлайн?» | Спрашиваю, но не в первую очередь. Сначала — проблема. |
| «Можно ли в MySQL?» | Спрашиваю, если в scope изменение БД. |

---

## Итог

Хорошие вопросы заказчику — это не «уточнение ТЗ». Это **отсев гипотез**.

```
Сырая жалоба
     ↓
7 вопросов
     ↓
Из 7 гипотез остаётся 1–2
     ↓
Задача с метрикой и scope
```

Если я начинаю кодить без этих 7 вопросов — я делаю **свою** задачу, а не бизнес-задачу. И она может не решить проблему.

> ⏱ 37.0s · prompt 110086 tok · output 3239 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о профессиональной практике: как действовать при (а) обнаружении постороннего бага и (б) противоречии между критерием приёмки и целью задачи. Это вопрос не о коде, а о поведении разработчика, коммуникации, принятии решений.

Нужно ответить как человек на собеседовании — конкретно, с примерами, с аргументацией "почему". Не абстрактные рассуждения, а живые сценарии.

Структура ответа:
1. Общий принцип — что для меня приоритет.
2. Ситуация (а) — попутный баг. Варианты действий, что я делаю, почему. Пример из практики.
3. Ситуация (б) — противоречие критерия и цели. Варианты, что делаю, почему. Пример.
4. Общие правила, чтобы это не превратилось в проблему.
5. Антипаттерны.

Важно: не быть "хорошим мальчиком", который молча чинит чужие баги или молча не исполняет критерий. Но и не быть тем, кто сразу бежит к тимлиду с каждой царапиной. Нужна середина: эскалация по правилу, а не по эмоции.

Ключевые моменты, которые я хочу передать:
- Попутный баг: НЕ чиню молча, НЕ чиню в рамках своей задачи без согласования. Задокументировать, эскалировать, предложить. Если баг блокирует мою задачу — чиню, но с явной фиксацией.
- Противоречие критерия и цели: НЕ исполняю критерий "по бумажке". НЕ игнорирую его молча. Останавливаюсь, формулирую противоречие, предлагаю решение, эскалирую если не могу решить.
- В обоих случаях: я не "исполнитель", который делает только то, что написано. Но и не "автор", который меняет задачу по своему усмотрению. Я — инженер, который сообщает факт и предлагает решение.

Примеры должны быть конкретными, из практики Symfony/PHP.

Сделаю ответ структурированным, но без избыточного формата. Не буду делать 20 таблиц. Лучше — 2-3 конкретных сценария с диалогом/решением и объяснением логики.

Язык — русский, как и вся беседа.

</details>

**Кандидат:**

## Общий принцип

Я не "исполнитель по бумажке" и не "автор, который правит задачу по своему усмотрению". Я инженер, который **сообщает факт и предлагает решение**. Разница между этими тремя позициями — в том, кто принимает решение и кто несёт за него ответственность.

---

## Ситуация (а): нашёл попутный баг

### Что НЕ делаю

**Не чиню молча в рамках своей задачи.**

Почему:
- Это меняет scope. Ревьюер видит лишний diff, не понимает, зачем.
- Это меняет риск. Мой багфикс может сломать то, что работало.
- Это меняет ответственность. Если через месяц баг вернётся — кто виноват? Я? Тимлид? Никто?
- Это создаёт прецедент. "Раз я тут поправил — ты тоже можешь". Scope-creep начинается не с "давай добавим фичу", а с "я тут попутно починил".

**Не игнорирую и не пишу в TODO.**

Почему:
- TODO без ссылки на трекер = баг, которого нет. Через 2 месяца никто не вспомнит.
- Игнор = я видел, что можно сломаться, и прошёл мимо. Если это сломается в prod — это моя вина, даже если формально "не моя задача".

---

### Что делаю

Зависит от **степени связи** с моей задачей.

#### Сценарий 1: Баг блокирует мою задачу

Пример: я делаю страницу истории заказов. В `OrderRepository::byId()` нашёл, что при `customer_id = NULL` (потерянные заказы) метод не бросает `OrderNotFound`, а возвращает `null`, и дальше `null->id()` — `Error`.

Это **не моя задача**, но без фикса моя фича падает.

**Что делаю:**

1. Чиню — потому что без этого моя задача не работает.
2. В PR пишу явно:

```
fix(order): throw OrderNotFound when customer_id is NULL

Found while working on JIRA-123 (order history page).
Null customer_id causes null->id() Error in byId().
Added proper exception. This is a separate bug, not part of JIRA-123 scope.

Refs: JIRA-123
```

3. Создаю отдельный тикет: `JIRA-124: OrderRepository::byId returns null instead of throwing`.
4. Говорю тимлиду: "Нашёл баг в byId, заблокировал мою задачу, пофиксил. Создал JIRA-124, чтобы отследить, есть ли ещё места, где этот null доходит до кода".

Почему так:
- Фикс в PR — потому что без него моя задача не работает.
- Отдельный тикет — потому что это не моя задача.
- Явное упоминание в PR — потому что ревьюер должен понимать, почему в diff есть лишние строки.

#### Сценарий 2: Баг НЕ блокирует мою задачу

Пример: делаю страницу истории заказов. В `PaymentRepository` нашёл, что `markAsRefunded()` не проверяет, что платёж не уже `refunded`. Двойной refund — баг.

Это **не блокирует** мою задачу. Моя задача — читать заказы, не изменять платежи.

**Что делаю:**

1. НЕ чиню в своём PR.
2. Создаю тикет: `JIRA-125: Double refund possible in PaymentRepository::markAsRefunded`.
3. Пишу в тикет:

```
Обнаружено при работе над JIRA-123.
Путь: два процесса одновременно вызывают markAsRefunded.
Оба видят status = 'charged'. Оба обновляют на 'refunded'.
Результат: двойное списание (или двойное зачисление).

Репрод:
1. Создать платёж со статусом charged
2. Дважды вызвать markAsRefunded в параллельных транзакциях

Предлагаю: FOR UPDATE + проверка status в одном запросе.
Не в scope JIRA-123. Нужна отдельная оценка.
```

4. Сообщаю тимлиду / product owner'у: "Нашёл баг на double refund. Создал JIRA-125. Не в моей задаче. Оцени, приоритет".

Почему так:
- Не чиню — потому что это не моя задача. Если я начну чинить чужие баги, scope-creep.
- Документирую — потому что если я молчу, баг живёт.
- Создаю тикет — потому что у бага должна быть история.
- Предлагаю решение — потому что я уже понял, в чём дело. Не заставляю другого разработчика с нуля разбираться.
- Не решаю приоритет — потому что это не моя задача.

#### Сценарий 3: Баг критичный (денежный, security)

Пример: нашёл, что в `PaymentController` можно оплатить чужой заказ, передав `order_id` другого пользователя. Нет проверки `order.customer_id === current_user`.

**Что делаю:**

1. Останавливаю работу над своей задачей.
2. Сообщаю тимлиду **немедленно**, не через тикет.
3. Если тимлид недоступен — senior-разработчику.
4. Фиксирую:

```
[КРИТИЧ] PaymentController: отсутствует проверка владения заказом.
Можно оплатить чужой заказ.
Путь: POST /payments?order_id={чужой_id}
Влияние: любой авторизованный пользователь может списать деньги с чужой карты.
Предлагаю: добавить isGranted('PAYMENT_CREATE', $order) в контроллер.
Временная митигация: выключить endpoint feature-flag'ом.
```

5. Чиню, если тимлид говорит "да, чини". Если говорит "это отдельная задача" — не чиню, но **обязательно** фиксирую, что временная митигация введена.

Почему так:
- Критичный баг не ждёт тикета.
- Но я не "спасаю" молча. Сообщаю, чтобы решение было осознанным.
- Временная митигация — потому что даже если полноценный фикс завтра, сегодня endpoint открыт.

---

## Ситуация (б): критерий приёмки противоречит цели

### Пример

Задача: "Страница истории заказов. Цель: снизить обращения в поддержку на 70%."

Критерий приёмки №3: "Пагинация: 20 заказов на страницу."

По ходу работы я выясняю:

- Медиана заказов на пользователя — 4.
- 95-й перцентиль — 12.
- Ни у кого нет 20+ заказов.

Пагинация **не нужна**. Она не поможет снизить обращения. А усложнит UI (кнопки "далее", "назад"), код (query builder, offset/limit), и может даже навредить: пользователь с 12 заказами увидит "Страница 1 из 1" и не поймёт, есть ли ещё.

Критерий противоречит цели.

### Что НЕ делаю

**Не исполняю "по бумажке".**

Почему: я знаю, что пагинация не решит проблему. Я делаю её "для галочки" — критерий закрыт, но цель не достигнута. Через месяц метрика не снизится, и бизнес скажет "задача не сработала". Но формально я её закрыл. Это хуже, чем если бы я не делал.

**Не игнорирую критерий молча.**

Почему: если я просто не делаю пагинацию — ревьюер спросит "а почему нет?". Я скажу "мне показалось, что не нужно". Ревьюер скажет "в ТЗ написано". И мы поспорим. Аргумент "мне показалось" слаб.

**Не меняю ТЗ сам.**

Почему: ТЗ — это договор. Я не вправе менять договор в одностороннем порядке. Даже если я прав.

---

### Что делаю

#### Шаг 1: Формулирую противоречие

Не "я не буду делать пагинацию". А:

```
Критерий №3 (пагинация по 20) противоречит цели задачи (снижение обращений).

Данные:
- Медиана заказов/пользователь: 4
- 95-й перцентиль: 12
- 99-й перцентиль: 25
- Нет пользователей с 20+ заказами (за последние 6 мес.)

Пагинация:
- Не уменьшает число обращений (пользователь видит все 4 заказа без пагинации)
- Усложняет UI (кнопки, состояние, ошибки при page > max)
- Усложняет код (offset/limit, сортировка, стабильность)

Предлагаю:
- Убрать пагинацию из критериев приёмки
- Вместо этого: показать все заказы (до 100) без пагинации
- Добавить: "если заказов > 100 — показать кнопку 'Показать ещё'"

Если согласны — исправляю ТЗ.
Если нет — делаю пагинацию, но фиксирую, что она не поможет цели.
```

#### Шаг 2: Эскалирую правильно

Не "бегу к тимлиду с проблемой". А:

```
[тиклет JIRA-123]
Комментарий:
  Критерий №3 противоречит цели. Данные: [цифры].
  Предлагаю: [альтернатива].
  Нужна резолюция до того, как я начну реализовывать.

@product_owner @team_lead
```

Почему в тикете, а не в чате:
- Зафиксировано.
- Есть контекст (данные, предложение).
- Не потеряется.
- Другой разработчик, который увидит тикет, поймёт, почему мы не делали пагинацию.

#### Шаг 3: Жду решения. Если не пришли в разумное время — делаю по ТЗ.

Почему:
- Я не могу бесконечно ждать.
- Если через 1 день нет ответа — делаю пагинацию.
- Но в PR пишу:

```
⚠️ Реализовано по ТЗ (критерий №3).
Однако, по данным (см. JIRA-123, комментарий от 15.01), пагинация
не поможет снизить обращения. Предлагаю пересмотреть.
```

Это защита: если метрика не снизится — я не "не сделал". Я сделал по ТЗ и предупредил.

---

### Ещё пример: критерий противоречит безопасности

Задача: "Добавить экспорт заказов в CSV."

Критерий: "Экспорт доступен всем авторизованным пользователям."

Я выясняю: в `orders` есть `customer_email`, `customer_phone`, `shipping_address`. Если пользователь А экспортирует все заказы — он увидит данные пользователей Б, В, Г.

Критерий противоречит цели (и 152-ФЗ).

**Что делаю:**

1. Останавливаю работу.
2. Пишу в тикет:

```
🔴 Критерий №2 (экспорт для всех авторизованных) нарушает приватность.
CSV содержит email, phone, address других пользователей.
Это нарушение 152-ФЗ.

Предлагаю:
- Экспорт только своих заказов (customer_id = current_user)
- Или: экспорт без PII (только номер, дата, сумма)

Нужно решение до реализации.
Не буду делать по текущему критерию.
```

3. Если product owner говорит "да, так и нужно" — уточняю: "Ты понимаешь, что это может быть нарушение закона?" Если да — фиксирую в тикете: "Решение принято product owner: [имя], [дата]. Риск: [описание]."

Почему я не делаю молча:
- Если через месяц провайдера оштрафуют — я был в цепочке.
- Если я сделал "по ТЗ" — это не снимает мою ответственность. Я видел, что это может быть незаконно, и не сообщил.

---

## Общие правила

| Ситуация | Действие | Почему |
|----------|----------|--------|
| Баг блокирует мою задачу | Чиню + отдельный тикет + явное упоминание в PR | Без фикса моя задача не работает |
| Баг не блокирует | Тикет + предложение + эскалация приоритета | Не мой scope, но я знаю о нём |
| Баг критичный | Немедленная эскалация + временная митигация | Не ждём тикета |
| Критерий противоречит цели | Формулирую + данные + альтернатива + эскалация | Не исполняю "по бумажке", не молчу |
| Критерий нарушает закон/безопасность | Останавливаюсь + эскалация + фиксация | Не моя ответственность принять риск молча |
| Нет ответа в разумное время | Делаю по ТЗ + предупреждаю в PR | Не блокирую работу, но фиксирую позицию |

---

## Антипаттерны, которых я избегаю

| Антипаттерн | Почему плохо |
|-------------|-------------|
| Молча чинить чужой баг | Scope-creep, неочевидный diff, нет истории |
| Молча не делать критерий | Ревьюер спросит "почему?", нет аргументов |
| Менять ТЗ сам | Не мой договор |
| Бегать к тимлиду с каждой царапиной | Тимлид — не суд первой инстанции |
| "Я сделал по ТЗ, а что вышло — их проблема" | Я видел противоречие, не сообщил — моя вина |
| Игнорировать баг "потому что не мой" | Если он сломается в prod — я прошёл мимо |

---

## Коротко

> Я не исполняю задачу слепо. Но и не решаю её сам.
>
> Если я вижу факт (баг, противоречие) — я **сообщаю факт, предлагаю решение, фиксирую позицию**.
> Решение принимает тот, кому оно принадлежит: product owner, тимлид, заказчик.
> Если решение не пришло — я делаю то, что формально правильно, и **фиксирую, что я предупреждал**.

Это не "хороший/плохой". Это инженерная дисциплина: **каждое отклонение от ТЗ должно быть осознанным, задокументированным и атрибутированным**.

> ⏱ 42.4s · prompt 115265 tok · output 4022 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про отчёт о выполнении задачи. Это продолжение серии вопросов о процессной инженерии: git, ревью, работа с заказчиком. Сейчас вопрос про финальный артефакт — отчёт.

Нужно дать конкретную структуру отчёта, а не абстрактные рассуждения. Ключевые аспекты:
1. Из чего состоит отчёт "всё сделано"
2. Что меняется при частичном выполнении
3. Почему именно так

Язык ответа — русский, как и вся беседа. Формат — структурированный, с примерами кода/текста, таблицами где уместно. Без воды.

Структура ответа:
- Философия отчёта (для кого он, что он делает)
- Структура отчёта "всё сделано" — конкретный шаблон с примером
- Структура отчёта "частично" — что меняется, почему
- Антипаттерны
- Короткое резюме

Не буду перегружать. Вопрос конкретный — ответ должен быть конкретным.

</details>

**Кандидат:**

## Отчёт о выполнении задачи

### Для кого он

Не для галочки. Отчёт отвечает трём вопросам:

1. **Что сделано** — и как проверить.
2. **Что НЕ сделано** — и почему.
3. **Что теперь нужно сделать другим** — и кто.

Если отчёт не отвечает хотя бы на один — он неполный.

---

## Структура отчёта: "всё сделано"

### Шаблон

```markdown
## JIRA-123: Self-service поиск истории заказов

### Статус: ✅ Закрыто

### Что сделано

- Страница `/account/orders` со списком заказов
- Фильтры: статус, период
- Поиск по номеру
- Пагинация (20/стр)
- Индекс `(customer_id, created_at DESC)`
- Feature flag `ORDER_HISTORY_V2`

### Что НЕ сделано (out of scope)

- Поиск по названию товара (JIRA-128)
- Экспорт в CSV (JIRA-129)
- Push-уведомления (JIRA-130)

### Как проверить

1. Login как user с ≥ 5 заказами
2. `/account/orders` — видит свои заказы
3. Фильтр "Завершённые" — только delivered
4. Поиск `ORD-2024-001` — находит
5. `GET /account/orders?page=99999` → 200, empty
6. Login как user без заказов → заглушка

### Тесты

- Unit: `OrderHistoryTest` (4 кейса)
- Integration: `OrderHistoryRepositoryTest` (3 кейса)
- Feature: `OrderHistoryApiTest` (6 кейса)
- CI: ✅ green

### Производительность

- `EXPLAIN ANALYZE` на staging (1.2M заказов): p95 = 45ms
- Query counter: 2 запроса (orders + lines), N+1 нет

### Миграции

- `20240115_add_index_orders_customer_created.sql` — CONCURRENTLY, вне пика
- Обратима: `DROP INDEX CONCURRENTLY`

### Риск

| Риск | Статус |
|------|--------|
| Индексация 1M строк | ✅ Сделано CONCURRENTLY, без lock |
| N+1 | ✅ Покрыт интеграционным тестом |
| Утечка чужих заказов | ✅ Feature-тест: user A не видит user B |

### Откат

1. `ORDER_HISTORY_V2 = false` в config
2. Ссылка на страницу скрывается
3. Индекс можно оставить (не мешает)

### Что нужно сделать дальше

| Что | Кто | Ссылка |
|-----|-----|--------|
| Аналитика: пустой поиск → event | Data | JIRA-131 |
| Мониторинг: обращения в поддержку по "не нахожу" | Support | — |
| Через 2 недели: сравнить 340 → ? | PO | JIRA-123 |

### Деплой

- Commit: `a1b2c3d`
- Тег: `v1.4.0`
- Staging: ✅
- Production: ✅ (2024-01-15 14:30 UTC)
- Post-deploy check: ✅ (5 проверок из списка выше)
```

---

### Почему именно эти блоки

| Блок | Зачем |
|------|-------|
| Что сделано | Коротко. Без "мы долго думали и в итоге". |
| Что НЕ сделано | Чтобы через 3 месяца не спрашивали "а почему нет поиска по товару?". |
| Как проверить | Конкретные шаги, а не "проверьте на staging". |
| Тесты | Какие, сколько, где. Не "тесты есть". |
| Производительность | Цифры. "Быстро" — не цифра. |
| Миграции | Что, как, обратимо ли. |
| Риск | Что может пойти не так. Статус: закрыто / открыто. |
| Откат | Пошагово. Не "откатить деплой". |
| Что дальше | Кто делает что. Без этого через месяц никто не вспомнит. |
| Деплой | Конкретика: commit, тег, время, проверка. |

---

## Структура отчёта: "частично сделано"

### Что меняется

Меняется **тон** и **фокус**. Не становится "хуже". Становится **честнее**.

Ключевое отличие:

| "Всё сделано" | "Частично" |
|---------------|-----------|
| "Что сделано" — основной блок | "Что НЕ сделано и почему" — основной блок |
| Риск — список | Риск — то, что **осталось** |
| "Что дальше" — бонус | "Что дальше" — обязательный |
| Тон: отчёт | Тон: эскалация + предложение |

### Шаблон

```markdown
## JIRA-123: Self-service поиск истории заказов

### Статус: ⚠️ Частично закрыто

### Что сделано

- Страница `/account/orders` со списком заказов
- Фильтры: статус, период
- Поиск по номеру
- Индекс `(customer_id, created_at DESC)`
- Тесты: unit + integration + feature (13 кейсов, CI green)

### Что НЕ сделано

| Пункт из ТЗ | Почему не сделано | Что нужно |
|-------------|-------------------|-----------|
| Пагинация (критерий №3) | Медиана заказов = 4, пагинация не нужна (см. JIRA-123 comment 01.15) | Решение PO: убрать или оставить. Жду ответа. |
| Экспорт invoice (критерий №5) | `invoice.pdf` генерируется внешним сервисом (InvoiceAPI). SLA не подтверждён. | JIRA-135: договориться с InvoiceAPI по SLA. Оценка: +3 дня. |

### Почему не удалось

**Пагинация:**
По данным (mediana = 4, p95 = 12) пагинация не решает проблему.
Предлагаю убрать из scope. PO: [ожидается решение].

**Invoice:**
InvoiceAPI возвращает 500 на 15% запросов за последний час (monitoring).
Не могу гарантировать критерий "invoice доступен за < 2s".
Нужен SLA от InvoiceAPI или fallback (показать ссылку, не PDF).

### Что нужно от других

| Что | От кого | Срок |
|-----|---------|------|
| Решение по пагинации (оставить/убрать) | PO | до 18.01 |
| SLA InvoiceAPI или решение о fallback | PO + InvoiceAPI | до 19.01 |
| Оценка на 3 дня для invoice-интеграции | Я | после решения |

### Тесты

- ✅ Сделанное покрыто (13 кейсов)
- ❌ Invoice — нет тестов (нет интеграции)
- ❌ Пагинация — нет тестов (не реализовано)

### Риск (открытый)

| Риск | Вероятность | Влияние | Митигация |
|------|-------------|---------|-----------|
| PO скажет "оставить пагинацию" | Средняя | +1 день | Готов, сделаю |
| InvoiceAPI не даст SLA | Высокая | Фича неполная | Fallback: ссылка, не PDF |

### Откат

Сделанное:
1. `ORDER_HISTORY_V2 = false`
2. Индекс можно оставить

Не сделанное — откатывать нечего.

### Рекомендация

Сделать split:
- **JIRA-123a** (готово): список + фильтры + поиск. Закрыть.
- **JIRA-123b** (открыто): invoice + пагинация. Отдельный тикет, отдельная оценка.

Почему: 80% ценности — в списке и фильтрах. Invoice — 20% и зависит от внешнего сервиса.
Не нужно держать задачу открытой из-за 20%.

### Деплой

- Сделанное: ✅ в prod (v1.4.0)
- Не сделанное: — (в коде нет)
```

---

### Почему именно так

| Элемент | Зачем |
|---------|-------|
| "Что НЕ сделано" — отдельная таблица | Не спрятано в тексте. Видно сразу. |
| "Почему не удалось" — данные, не мнение | Не "InvoiceAPI плохой", а "500 на 15% запросов, monitoring". |
| "Что нужно от других" — конкретные люди и сроки | Без этого "ждём решения" = "забыли". |
| "Рекомендация" — split | Не прошу "сделайте что-нибудь". Предлагаю конкретное решение. |
| "Открытый риск" — таблица | Не "может быть, что-то". А: вероятность, влияние, митигация. |

---

### Чего я НЕ делаю в отчёте "частично"

| Антипаттерн | Почему плохо |
|-------------|-------------|
| "Я сделал всё, что мог" | Не отчёт. Упущение. Нужно: что именно не сделал и почему. |
| "Это не моя вина" | Не вопрос вины. Вопрос: что сделать дальше. |
| Прятать "не сделал" в конце | Если 2 из 5 пунктов не готовы — это в заголовке, не в подвале. |
| "Нужна помощь" без конкретики | Кто? Что? К когда? Без этого — не помощь, а плач. |
| Молчать о риске | Если я знаю, что InvoiceAPI падает, и не сказал — это моя вина. |
| Делать вид, что задача закрыта | Статус "⚠️", не "✅". Даже если 80% сделано. |

---

### Практическое правило

> Отчёт о выполнении — это не "что я сделал".
> Это **"в каком состоянии система после моей работы и что нужно, чтобы она была в том состоянии, которое нужно бизнесу"**.

Если я закрыл задачу, а бизнес-цель не достигнута — мой отчёт должен это показывать. Не скрывать за "тесты зелёные".

---

### Коротко

| Ситуация | Ключевое в отчёте |
|----------|-------------------|
| Всё сделано | Что + как проверить + откат + что дальше |
| Частично | Что НЕ сделано + почему (данные) + что нужно от других + рекомендация |
| Критичный риск открыт | Статус "⚠️", риск в таблице, эскалация с конкретикой |

Общее: **честность > красота**. Отчёт, в котором "всё отлично", а на деле 60% — это не отчёт. Это проблема, которая всплывёт через 2 недели в виде "а почему метрика не упала?".

> ⏱ 29.9s · prompt 118784 tok · output 2813 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

<details><summary>Ход мысли модели</summary>

The user is asking about "FORCE INDEX" in PostgreSQL. Let me think carefully about this.

FORCE INDEX is a **MySQL** syntax, not PostgreSQL. In MySQL, you can write:

```sql
SELECT * FROM table FORCE INDEX (index_name) WHERE ...
```

PostgreSQL does **not** have `FORCE INDEX` or `USE INDEX` syntax. These are MySQL-specific hints.

So the user is asking about a feature that doesn't exist in PostgreSQL. I need to:
1. Clarify that FORCE INDEX is a MySQL concept, not PostgreSQL.
2. Explain what it does in MySQL (since the user is asking about it).
3. Explain how to achieve similar things in PostgreSQL — which is fundamentally different because PostgreSQL's planner doesn't support index hints in the same way.

Let me think about what PostgreSQL offers instead:
- There are no index hints in standard SQL for PostgreSQL.
- You can influence the planner via:
  - `SET enable_indexscan = off;` — disables index scans entirely (session level)
  - `SET enable_seqscan = off;` — disables sequential scans
  - `SET enable_hashjoin = off;` etc.
  - These are "hints" in a very indirect way — they raise the cost estimate so the planner is less likely to choose that plan.
  - `pg_hint_plan` — an extension that adds hint-like capabilities (similar to Oracle hints)
  - Proper indexing, statistics (`ANALYZE`), and query rewriting are the "right" ways.

I should be direct and honest: this is a MySQL feature, not PostgreSQL. I shouldn't pretend it exists in PostgreSQL. I should explain what it is in MySQL, and then give the PostgreSQL equivalents.

This is a good opportunity to show I know my stuff — not to fabricate a feature that doesn't exist just to sound helpful. A good senior engineer would say "this isn't a PostgreSQL thing, here's what it is in MySQL and here's what you do in PostgreSQL."

Let me structure the answer:
1. Direct clarification: FORCE INDEX is MySQL, not PostgreSQL.
2. What it does in MySQL (briefly).
3. What to do in PostgreSQL instead — the "right" way and the "hack" way.
4. When you might actually need something like this, and how to do it properly.

I'll write in Russian since the conversation is in Russian.

Let me keep it focused and practical, matching the tone of the interview-style conversation we've been having.

</details>

**Кандидат:**

## `FORCE INDEX` — это MySQL, не PostgreSQL

Честно: `FORCE INDEX` — это **синтаксис MySQL/MariaDB**, а не PostgreSQL.

```sql
-- MySQL:
SELECT * FROM orders FORCE INDEX (idx_orders_customer) WHERE customer_id = 5;

-- MariaDB: то же самое.

-- PostgreSQL: такого синтаксиса нет. Не существует.
```

В PostgreSQL **нет** ни `FORCE INDEX`, ни `USE INDEX`, ни `IGNORE INDEX`. Это MySQL-концепция.

---

## Что делает `FORCE INDEX` в MySQL

Принуждает планировщик использовать конкретный индекс, даже если, по его оценкам, `Seq Scan` (full table scan) был бы дешевле.

```sql
-- Планировщик сам выбрал бы Seq Scan (маленькая таблица, выборка 40%).
-- FORCE INDEX говорит: "нет, используй именно этот индекс".
SELECT * FROM orders FORCE INDEX (idx_orders_customer_date)
WHERE customer_id = 5 AND created_at > '2024-01-01';
```

Когда это нужно в MySQL:

| Ситуация | Почему |
|----------|--------|
| Устаревшая статистика | `ANALYZE TABLE` не помог, статистика всё равно кривая |
| Малая таблица, но нужен индекс | Планировщик предпочитает Seq Scan, а вам нужен индекс |
| Несколько похожих индексов | Планировщик выбирает не тот |

---

## А в PostgreSQL?

### Правильный ответ: вам не нужен "force index"

В PostgreSQL планировщик **почти всегда** выбирает правильный план, если:

1. **Статистика актуальна** — `ANALYZE` выполнен.
2. **Индекс существует и подходит** — правильные колонки, правильный порядок.
3. **Query написан так, чтобы индекс мог использоваться** — нет функций над колонкой в `WHERE`.

Если планировщик PostgreSQL выбирает "не тот" план — проблема почти всегда в одной из этих трёх причин.

---

### Что можно сделать в PostgreSQL вместо `FORCE INDEX`

#### 1. `ANALYZE` (самое частое решение)

```sql
ANALYZE orders;
```

Если `EXPLAIN` показывает `rows=100` (estimate), а `actual rows=10000` — статистика устарела. `ANALYZE` это чинит.

**90% случаев "планировщик выбрал не тот план" лечится этим.**

#### 2. Переписать запрос

```sql
-- Плохо: функция над колонкой → индекс не используется
SELECT * FROM orders WHERE EXTRACT(YEAR FROM created_at) = 2024;

-- Хорошо: range → индекс работает
SELECT * FROM orders
WHERE created_at >= '2024-01-01' AND created_at < '2025-01-01';
```

```sql
-- Плохо: implicit cast → индекс не используется
SELECT * FROM orders WHERE customer_id = '5';  -- если customer_id INT

-- Хорошо:
SELECT * FROM orders WHERE customer_id = 5;
```

#### 3. Создать правильный индекс

```sql
-- Запрос:
SELECT * FROM orders WHERE customer_id = 5 AND created_at > '2024-01-01';

-- Индекс:
CREATE INDEX idx_orders_customer_date ON orders (customer_id, created_at);
```

#### 4. `SET enable_*` (грубая сила, только для диагностики)

Это **не** hint. Это подстройка стоимости планов.

```sql
-- Запретить Seq Scan (поднять его стоимость до бесконечности):
SET enable_seqscan = off;

-- Запретить Index Scan:
SET enable_indexscan = off;

-- Запретить Hash Join:
SET enable_hashjoin = off;
```

```sql
-- Пример: принудить Index Scan
SET enable_seqscan = off;
SELECT * FROM orders WHERE customer_id = 5;
RESET enable_seqscan;
```

**Важно:**
- Это **session-level**, не statement-level.
- Это **влияет на все запросы** в сессии.
- Это **не production-решение**. Это диагностический инструмент.
- Если вам постоянно нужно `SET enable_seqscan = off` — проблема в индексах или статистике, а не в планировщике.

#### 5. `pg_hint_plan` (расширение, похоже на Oracle hints)

```sql
SELECT /*+ IndexScan(o idx_orders_customer) */ *
FROM orders o WHERE o.customer_id = 5;
```

Это third-party extension. Делает именно то, что делает `FORCE INDEX` в MySQL.

**Когда использую:** никогда. Если мне нужно — значит, проблема в чём-то другом (статистика, индекс, запрос). `pg_hint_plan` маскирует проблему.

#### 6. `MATERIALIZED VIEW` / `GENERATED COLUMN`

Если запрос каждый раз делает одно и то же тяжёлое JOIN-операцию:

```sql
CREATE MATERIALIZED VIEW mv_order_summary AS
SELECT o.id, o.customer_id, o.total, u.name
FROM orders o JOIN users u ON u.id = o.customer_id;

-- Периодическое обновление:
REFRESH MATERIALIZED VIEW mv_order_summary;
```

---

## Итого

| Что | MySQL | PostgreSQL |
|-----|-------|------------|
| Принудить индекс | `FORCE INDEX (name)` | Нет нативного способа |
| Правильное решение | `ANALYZE TABLE`, правильный индекс, переписать запрос | `ANALYZE`, правильный индекс, переписать запрос |
| Грубая сила | `USE INDEX` | `SET enable_seqscan = off` (session-level) |
| Hints | Нет | `pg_hint_plan` (extension) |
| Что я делаю | `ANALYZE` + правильный индекс + переписать запрос | То же самое |

**Главное правило:** если вам нужен `FORCE INDEX` — сначала проверьте `EXPLAIN (ANALYZE, BUFFERS)`, `ANALYZE`, и правильность индекса. В 95% случаев проблема не в "планировщик тупой", а в "статистика устарела" или "индекс не тот".

> ⏱ 20.7s · prompt 121383 tok · output 1975 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

<details><summary>Ход мысли модели</summary>

The user is asking about the performance impact of `strict_types=1` in PHP. Let me think carefully about what I actually know here, because this is a place where people often make confident claims that aren't well-supported.

What I know factually:
1. `declare(strict_types=1)` is a file-scope directive that controls whether type coercion happens on function/method calls made FROM that file.
2. It affects the call-site, not the callee.
3. The PHP engine does a type check at the call site. In coercive mode, it attempts to coerce. In strict mode, it checks exact type match and throws TypeError if mismatch.
4. The overhead is essentially the cost of a type comparison at each typed call site, versus the cost of attempting coercion.

What I need to be honest about:
- I do NOT have precise benchmark numbers from my "experience." I should not fabricate specific percentages like "3-5%" or "0.5-2%" and present them as measured values from my work.
- The actual overhead is extremely small in most real-world PHP applications. The dominant costs are I/O (DB, network, file), not type checking.
- I should explain WHERE the overhead is and WHY it's typically negligible in practice, and where it could theoretically matter.
- I should be clear about what is measured consensus vs. what is my inference.

Let me structure the answer:
1. Be honest: I don't have a precise percentage "from my experience" that I'd stake my reputation on. I can give a range based on what's known about the engine.
2. Explain the mechanism: what actually happens at the call site.
3. Explain why in practice it's negligible.
4. Give the cases where it could theoretically matter (hot loops with millions of small typed calls).
5. Explain what I actually do (always declare strict_types, never measure it, because it's not where the time goes).

Key technical detail: In strict mode, the engine does a `zend_verify_internal_arg_types` / internal type check. In coercive mode, it may call `convert_to_*` functions. The strict check is actually often FASTER than coercive, because coercive mode has to do the conversion work (allocating a zval, converting string to int, etc.) when types don't match. If types DO match, both paths do the same check. So the "cost" of strict_types is really "you lose the implicit conversion convenience," and the cost of NOT having strict_types is "you pay for conversions when types mismatch, and you get silent bugs."

Actually, let me be more precise about the VM:
- When a call is made, the engine checks the parameter types against the function signature.
- In coercive mode: if the arg type doesn't match exactly, it attempts coercion (e.g., string → int, array → ArrayObject, etc.). This coercion work is the cost.
- In strict mode: if the arg type doesn't match exactly, it throws TypeError immediately. No coercion attempt.
- If the arg type DOES match exactly (the common case in well-written code), both modes do the same check.

So the overhead of strict_types is NOT "an extra check." It's actually:
- Same cost when types match (the normal case).
- Lower cost when types don't match (strict: throw; coercive: attempt conversion, which can be expensive).
- Higher cost in the sense that you can't rely on implicit conversion (a development-time concern, not a runtime cost).

So the honest answer is: `strict_types=1` does NOT meaningfully slow down PHP code. In fact, for well-typed code, it's essentially free. The "cost" people worry about is not really there.

I should also address: what about the file-scope nature? Every file that makes calls needs to have it declared. That's a dev-time cost, not a runtime cost.

Let me also be honest: I have not run a microbenchmark comparing strict vs. coercive on a tight loop. I should say that clearly. I can reason about the engine internals and give a well-founded answer, but I should not present fabricated numbers.

The user has been asking detailed technical questions throughout this interview. They're testing whether I'll make up confident-sounding numbers or be honest about uncertainty. I should be honest.

Let me draft a response that:
1. Directly answers: I don't have a measured percentage I'd stake my name on, and here's why that's the honest answer.
2. Explains the actual mechanism in the VM.
3. Gives a reasoned estimate (negligible, <0.1% in real apps).
4. Explains where it COULD theoretically matter.
5. States what I actually do in practice.

</details>

**Кандидат:**

## Честный ответ: точного процента у меня нет, и я не буду его выдумывать

Я не гонял микробенчмарк «100 млн вызовов функции со строгим типом vs. без» и не публиковал цифру. Если бы я сказал «3–5%» — это было бы confidently wrong. Ниже — что я знаю о механизме и что из этого следует.

---

## Что реально происходит в VM

### На call-site (в файле с `strict_types=1`)

При каждом вызове типизированной функции PHP-движок проверяет аргументы против сигнатуры:

```
strict mode:
  arg type === declared type?  →  да: ок.  нет: TypeError.
  (одна проверка, без попыток конвертации)

coercive mode:
  arg type === declared type?  →  да: ок.  нет: попробовать сконвертировать.
  (если конвертация нужна — вызов convert_to_*: аллокация zval, парсинг строки в int и т.д.)
```

Ключевой момент: **когда типы совпадают** (нормальный, хорошо написанный код) — **обе ветки делают одинаковую проверку**. Разницы нет.

Разница появляется только когда типы **не** совпадают:
- strict → бросает `TypeError` мгновенно.
- coercive → пытается сконвертировать. Это **дороже**, чем throw, потому что включает аллокацию, парсинг, возможную потерю данных.

Вывод: `strict_types=1` не «добавляет проверку». Он **меняет поведение при несовпадении типов** с «попробуй сконвертировать» на «сразу упдни».

---

## Где теоретически возможна разница

| Ситуация | Влияние |
|----------|---------|
| Тесный цикл, миллионы вызовов, аргументы всегда правильного типа | Нулевое. Обе ветки одинаковы. |
| Тесный цикл, где часть аргументов строки, передаются в `int` | Strict **быстрее** (throw вместо конвертации). |
| Тесный цикл, где часть аргументов строки, и конвертация успешна | Coercive **быстрее** (конвертация vs. throw). Но это «быстрее» в миллисекундах на миллион итераций. |
| Реальное приложение (BDD, ORM, HTTP) | Не измеримо. 99% времени — I/O (БД, сеть, файлы). |

---

## Почему на практике это невидимо

1. **I/O доминирует.** Один запрос в PostgreSQL — 1–10ms. Один вызов функции в PHP — ~10–100ns. Даже если strict_types добавит 50ns на вызов, это 0.005% от времени запроса в БД.

2. **Типы совпадают.** В хорошо написанном коде аргументы передаются правильного типа. Проверка `===` — одна операция, одинаковая в обоих режимах.

3. **Файл-локальность.** `strict_types` влияет только на вызовы **из этого файла**. Если вы не вызовите функцию с несовпадающим типом — разницы нет.

4. **JIT не оптимизирует тип-чек.** JIT компилирует тело функции, но call-site с типизацией — это всё равно интерпретируемый код (или JIT с guards). Разница между strict и coercive на уровне одного сравнения — не то, что JIT видит как bottleneck.

---

## Что я делаю на практике

```php
<?php
declare(strict_types=1);
```

В **каждом** файле. Всегда. Без исключений.

Почему я не меряю:

- Это не то место, где теряется время.
- Если бы это было заметно в профайлере (Xdebug, Blackfire), я бы увидел. Я не видел.
- Цена не включения: скрытые баги (`"100" === 100` → `true` в coercive), потеря типа безопасности, несовместимость с PHPStan-уровнями выше 5.

---

## Коротко

| Вопрос | Ответ |
|--------|-------|
| На сколько `strict_types=1` замедляет PHP? | На величину, которую я не могу измерить в реальном приложении и не хочу выдумывать. |
| Влияет ли на скорость? | Теоретически да (на call-site с несовпадающими типами). Практически нет (I/O доминирует). |
| Стоит ли включать? | Да. Всегда. Цена — ноль. Польза — отлов багов на раннем этапе. |
| Могу ли я дать точный процент? | Нет. И не должен. Любой конкретный процент без бенчмарка — это фантазия. |

Если бы вы попросили — я бы написал бенчмарк: 10 млн вызовов функции с `int`-аргументом, strict vs. coercive, в каждом режиме. Но результат, я уверен, был бы в пределах погрешности (< 1%), и это не изменило бы моего решения: `declare(strict_types=1)` — всегда.

> ⏱ 25.8s · prompt 122859 tok · output 2215 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает про составные первичные ключи (composite primary keys) в Doctrine ORM и как с этим живут в реальных проектах. Это хороший технический вопрос, требующий честного ответа.

Мне нужно:
1. Честно объяснить, почему Doctrine (и многие ORM) плохо работают с composite keys.
2. Показать, как это выглядит на практике.
3. Дать реальные решения, которые используют в production.
4. Не выдумывать — если чего-то не знаю, сказать прямо.

Давайте подумаю о реальном опыте:
- Composite PK в Doctrine: можно через `#[ORM\Id]` на нескольких полях, но это боль.
- Почему боль: identity map работает по одному id, каскады ломаются, DQL неудобный, ассоциации с composite key — кошмар.
- Как живут: обычно избегают composite PK, используют отдельный surrogate key.
- Когда composite PK оправдан: join-таблицы (many-to-many), но и там лучше отдельный id.

Отвечу на русском, как и весь разговор. Структурирую: почему это проблема в ORM, как выглядит попытка, как живут на практике.

</details>

**Кандидат:**

## Composite Primary Keys в Doctrine

### Сначала — честное

Composite PK в Doctrine **поддерживается формально**, но работает так, что в 90% случаев это боль. Я не скажу «не поддерживает» — это неправда. Но скажу: «поддерживает на уровне минимума, и этого мало».

---

### Как это выглядит в Doctrine

```php
#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $orderId;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'integer')]
    private int $quantity;
}
```

Formально — работает. `INSERT`, `SELECT`, `UPDATE` проходят.

Но дальше начинаются проблемы.

---

### Почему это боль

#### 1. Identity Map

Doctrine держит identity map по **ID**. При composite key:

```php
// Загружаю OrderLine(order_id=1, product_id=10)
$line1 = $em->getRepository(OrderLine::class)->find(['orderId' => 1, 'productId' => 10]);

// Загружаю то же самое
$line2 = $em->getRepository(OrderLine::class)->find(['orderId' => 1, 'productId' => 10]);

// Это один объект?
var_dump($line1 === $line2);  // true — ок, identity map работает

// Но:
$line3 = $em->getRepository(OrderLine::class)->find(['orderId' => 1, 'productId' => 10]);
// Это тоже один объект? Да.

// А если я меняю quantity?
$line1->quantity = 5;
$em->flush();

// A $line2?
var_dump($line2->quantity);  // 1 (старое значение!)
```

Identity map **работает**, но поведение с несколькими переменными, указывающими на одну сущность, — хрупкое. При composite key это встречается чаще, потому что сущность загружается из разных мест.

#### 2. Ассоциации

Это главная боль.

```php
class Order
{
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class)]
    private Collection $lines;
}

class OrderLine
{
    #[ORM\Id]
    private int $orderId;

    #[ORM\Id]
    private int $productId;

    // Как связать OrderLine с Order?
    // mappedBy: 'order' — а Order не имеет поля 'order'!
    // OrderLine сам содержит orderId, но это не ассоциация.
}
```

Чтобы это работало, нужно:

```php
class OrderLine
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $orderId;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $productId;

    // Должна быть ассоциация:
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    private Order $order;
}
```

Но теперь `orderId` и `$order` — **два разных способа** указать один и тот же заказ. Если они расходятся — данные невалидны.

```php
// Кто-то делает:
$line->orderId = 5;
$line->order = $order1; // id = 3

// Расхождение. Данные в невалидном состоянии.
```

#### 3. DQL

```php
// Нормальный запрос:
$qb->select('l')->from(OrderLine::class, 'l')->where('l.orderId = :id');

// А если нужно загрузить с Order:
$qb->select('l', 'o')
   ->from(OrderLine::class, 'l')
   ->join('l.order', 'o')
   ->where('l.orderId = :id AND l.productId = :pid');

// Если composite key — где граница?
// $l.orderId — часть PK, а $l.order — ассоциация.
// Они должны совпадать, но Doctrine это не гарантирует.
```

#### 4. Cascade и orphanRemoval

```php
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, orphanRemoval: true)]
```

`orphanRemoval` — «если убрать из коллекции — удалить».

При composite key:

```php
$order->getLines()->removeElement($line);
$em->flush();
// Doctrine удалит OrderLine WHERE orderId=1 AND productId=10.
// А если есть OrderLine(orderId=1, productId=10) из другого контекста?
// Например, если productId=10 привязан к другому заказу через другую связь?
```

В теории — ок. На практике — легко сломать, если есть несколько путей к одной строке.

#### 5. Миграции

```sql
-- Альтерирование composite PK — боль:
ALTER TABLE order_lines DROP CONSTRAINT pk_order_line;
ALTER TABLE order_lines ADD CONSTRAINT pk_order_line PRIMARY KEY (order_id, product_id, warehouse_id);
```

Если нужно добавить колонку в PK — нужно пересоздавать индекс, переносить данные. В MySQL это `ALTER TABLE`, в PostgreSQL — тоже `ALTER TABLE`. На большой таблице — долгий lock.

---

### Как с этим живут в реальных проектах

#### Вариант 1: Surrogate Key (самый частый)

```php
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $orderId;

    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\UniqueConstraint(columns: ['orderId', 'productId'])]
    // Единственность — через UNIQUE, а не через PK
}
```

```sql
CREATE TABLE order_lines (
    id BIGINT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    UNIQUE (order_id, product_id)
);
```

**Почему так:**
- Identity map работает по одному `id`.
- Ассоциации чистые: `OrderLine → Order` через `ManyToOne`.
- DQL простой.
- `UNIQUE` защищает от дублей.
- Composite PK не нужен — `id` делает свою работу.

**Минус:**
- Лишняя колонка `id` (8 байт на строку).
- Нужно помнить про `UNIQUE`.

**Когда использую:** 90% случаев. Включая join-таблицы.

---

#### Вариант 2: Composite PK (редко)

Когда:
- Таблица — **чистая** join-таблица (many-to-many) без дополнительных полей.
- Или: доменная модель **требует** composite key (например, `Route(stop_from, stop_to)`).

```php
class Route
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $stopFrom;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $stopTo;

    #[ORM\Column(type: 'integer')]
    private int $distanceKm;
}
```

**Почему здесь ок:**
- Это не агрегат с коллекцией.
- Нет cascade.
- Загружается по двум полям — всегда вместе.
- Нет ассоциаций на другие сущности.

**Когда НЕ ок:**
- Если `Route` — часть `Trip`, и `Trip` имеет коллекцию `routes` — боль.
- Если нужно `update Route WHERE stop_from = ? AND stop_to = ?` — ок, но если `stop_from` меняется — сироты.

---

#### Вариант 3: Read Model / Raw SQL

Если composite key нужен **только для чтения**:

```php
// Не Doctrine Entity. Просто DTO:
class OrderLineDto
{
    public function __construct(
        public int $orderId,
        public int $productId,
        public int $quantity,
    ) {}
}

// Raw SQL:
$rows = $em->getConnection()->fetchAllAssociative(
    'SELECT order_id, product_id, quantity FROM order_lines WHERE order_id = :oid',
    ['oid' => $orderId]
);

$lines = array_map(fn($r) => new OrderLineDto($r['order_id'], $r['product_id'], $r['quantity']), $rows);
```

**Почему так:**
- Нет identity map.
- Нет ассоциаций.
- Нет cascade.
- Только чтение.

**Когда использую:** CQRS, read models, отчёты.

---

### Почему ORM в целом плохо работает с composite keys

| Причина | Объяснение |
|---------|-----------|
| Identity map по одному ID | Composite key ломает простоту `map[id] = entity` |
| Ассоциации через FK | `ManyToOne` предполагает один FK, а не два |
| Cascade | `orphanRemoval` — «удали по ID», а ID — составной |
| DQL | `WHERE l.id = :id` — не работает, `WHERE l.orderId = :oid AND l.productId = :pid` — неудобно |
| Миграции | Изменение PK — боль на уровне БД |
| Generics (PHPStan) | `EntityRepository<OrderLine>` — а `find()` принимает `array` |

Это не «баг Doctrine». Это **фундаментальное ограничение ORM-модели**, где сущность — объект с уникальным идентификатором. Composite key нарушает эту модель.

---

### Итого

| Ситуация | Что делаю |
|----------|-----------|
| `OrderLine` (часть агрегата) | Surrogate `id` + `UNIQUE(order_id, product_id)` |
| Чистая join-таблица (many-to-many) | Surrogate `id` + `UNIQUE(a_id, b_id)` |
| Доменная сущность с естественным composite key (`Route`) | Composite PK, если нет ассоциаций и коллекций |
| Read model / отчёт | Raw SQL, нет Doctrine Entity |
| Composite PK + ассоциации + cascade | **Не делаю.** Переосмысляю модель. |

**Главное правило:**

> Composite PK — это признак того, что доменная модель нарисована неправильно.
> Если нужно composite PK в ORM — сначала спроси: "а может, это не сущность, а просто связь?"

Если это связь — surrogate key + `UNIQUE`.
Если это сущность — composite PK, но без ассоциаций.
Если это сущность **и** с ассоциациями — пересмотри модель.

> ⏱ 27.1s · prompt 124089 tok · output 2709 tok

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

We need answer user's question in Russian. They ask code review of PHP controller with SQL injection etc. Need produce final answer. Need think thoroughly. Need likely explain problems and refactor. They expect senior-level. Need maybe start with security, then transaction/domain/architecture. Need discuss SQL injection top. Then missing auth, type, status transition, race condition, no transaction around email? Actually email after update. DB::connect adhoc. Mail may fail but order paid? Need outbox. No validation. No idempotency. Direct query. No repository/domain. Logger. Response no content-type. No CSRF. No payment gateway? Just marks paid. Need refactor step-by-step. Need maybe present improved code in Symfony/DDD. But original not Symfony? Uses Request/Response and DB::connect. Could propose both minimal cleanup and proper architecture. User: "С чего начал бы и почему?" Need prioritization. Mention first stop security: parameterization. Then authorization. Then state transition and race. Then transaction and side effects. Then refactor to layers. Need include code snippets. Need be concise but detailed. Need maybe mention if this is legacy: hotfix first. Provide phased plan: 1) SQL injection fix; 2) auth + domain rules; 3) concurrency/idempotency; 4) outbox/async; 5) tests. Need maybe show secure procedural version and Symfony version. Need avoid over-abstracting? Could show pragmatic version. Need mention no prepared statements, input unvalidated, direct DB, business logic in controller, global Mailer/Logger, no DI, no error handling, no transaction, no rollback, race condition, lost update, missing status validation, no idempotency, no authorization, no CSRF, no rate limiting, no logging security, no response format. Need maybe include SQL injection demo. Need mention if order_id from query param could be negative, string. Need use int cast/validate. Need PDO prepared statements. Need use transactions. Need use UPDATE ... WHERE status = 'draft' to make state transition atomic. Need check rowCount. Need if payment actual charge external? Should be before status change? But if marking paid from external callback, need idempotency. Need maybe separate use case. Need mention `SELECT *` and missing fields. Need maybe email should be async and idempotent. Need mention log id could be attacker controlled, but if parameterized ok. Need mention no exception handling. Need mention `Mailer::send` may throw and after update? If after update and fails, email lost. Need outbox. Need mention no `Content-Type`, maybe JSON. Need mention 200 'ok' maybe 200. Need mention no `X-Frame`? less. Need start with: first I would freeze feature / add auth? Actually if production, first security patch: parameterize, auth, status guard. Then refactor. Need propose code: minimal:

```
public function pay(Request $req): Response {
  $userId = $req->user?; // auth
  $orderId = $req->get('order_id');
  if (!is_numeric($orderId) || (int)$orderId <=0) 400;
  $pdo = DB::connect();
  $stmt = $pdo->prepare('SELECT id, status, email FROM orders WHERE id = ? AND user_id = ?');
  ...
}
```

But better domain.

Need include Symfony version:

- Domain: Order aggregate with status, pay() method.
- Application: PayOrderUseCase.
- Infrastructure: OrderRepository, Mailer port, EventOutbox.
- Presentation: controller thin.
- Use DB transaction: select for update or optimistic update:

```
UPDATE orders SET status='paid', version=version+1 WHERE id=? AND status='draft' AND user_id=?
```
If rowCount=0 then conflict/not found. But need distinguish not found vs not owned vs invalid status. Could first SELECT FOR UPDATE or UPDATE and check. Simpler:

```
BEGIN;
SELECT * FROM orders WHERE id=? AND user_id=? FOR UPDATE;
if (!order) ...
if (status !== 'draft') ...
UPDATE orders SET status='paid'...
INSERT outbox event
COMMIT;
```

Need email after commit via outbox.

Need mention tests: SQL injection, auth, invalid status, concurrency, email idempotency.

Need final likely with priority. Ensure no false: Doctrine not required if simple. Could show both.

Let's craft final in Russian. Use headings. Include problem table by priority. Then "с чего начал бы" steps. Then refactor: emergency fix and target architecture. Include code. Need mention if this is real system, first add tests/monitoring? Maybe not. Need include exact SQL injection risk. Also `$order['email']` maybe not selected? SELECT * includes. Need mention `Mailer::send` synchronous in request increases latency and can fail. Need mention direct `DB::connect` inside request, no lifecycle. Need mention global classes. Need mention no exception handling. Need mention no CSRF for POST. Need mention payment not actually charged. Could mention if this is just marking paid, need idempotency and webhook. Need mention no audit. Need maybe mention no validation of order belongs to user. If admin? unclear.

Need be careful: In minimal secure version, using `PDO` and `DB::connect()` still legacy. We can show:

```
$id = filter_var($req->get('order_id'), FILTER_VALIDATE_INT);
```
If null 400.

Auth:
```
$userId = $req->getAttribute('user_id');
```
or throw.

Then query:
```
$stmt = $pdo->prepare('SELECT status, email FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$order = $stmt->fetch();
```
Then update atomic:
```
$update = $pdo->prepare("UPDATE orders SET status='paid' WHERE id = ? AND user_id = ? AND status='draft'");
$update->execute([$id,$userId]);
if ($update->rowCount() !== 1) { ... }
```
But after SELECT and UPDATE no transaction. If two concurrent: both SELECT status draft, both UPDATE status draft condition. First updates, second rowCount 0 because status now paid. Good. Need if first update succeeds and then Mailer throws, status paid but no mail. That's ok? Maybe email noncritical but should async. Could put email in outbox in same transaction. Minimal:
```
$pdo->beginTransaction();
... select for update ...
... update ...
$pdo->prepare('INSERT INTO outbox...')...
$pdo->commit();
queue email
```
But if commit then queue fails? Use outbox relay.

Need mention `UPDATE ... WHERE status='draft'` as optimistic guard. For stronger concurrency use FOR UPDATE. Need maybe in legacy: update guarded by status enough if only transition draft->paid. If other transitions possible, domain method.

Need include final target code:

Domain:
```
enum OrderStatus { Draft, Paid, ... }
final class Order {
 public function pay(): void { if ($this->status !== OrderStatus::Draft) throw ...; $this->status = OrderStatus::Paid; }
}
```
Repository interface.
UseCase:
```
final class PayOrderUseCase {
 public function __invoke(int $orderId, int $userId): Order {
  return $this->transaction->execute(function() use (...) {
    $order = $this->orders->lockForUpdate($orderId, $userId); // or find
    $order->pay();
    $this->orders->save($order);
    $this->outbox->append(new OrderPaid(...));
    return $order;
  });
  // after commit? Actually outbox inside, then events relay.
 }
}
```
Controller:
```
#[IsGranted('ORDER_PAY')]
public function pay(Request $req): JsonResponse {
 $orderId = $req->get('order_id');
 ...
 return new JsonResponse(['orderId'=>...], 200);
}
```
Listener:
```
#[AsMessageHandler]
class SendOrderPaidEmailHandler { idempotent }
```

Need mention tests. Maybe include table.

Need answer "С чего начал бы и почему?" Start with: if production: first make it safe: prepared statements + auth + status guard + atomic update; then tests; then architecture. Why because security and correctness before beauty.

Need maybe mention `SELECT *` could expose sensitive fields but not used. Use needed columns. Need mention `Response('ok')` maybe should JSON.

Potential issue: `$req->get('order_id')` could be array. Cast to int? Better validate.

Need maybe mention no `Content-Type` and no exception handler. If `Mailer::send` throws, user gets 500 and no info. Order may be paid. Need separate error responses.

Need mention no `use` of exception `PDOException`. If DB fails, 500.

Need mention no rate limiting and brute force? If order_id enumerable, users can enumerate others? auth prevents. But if unauth, SQL injection can read all. Need CSRF.

Need final oververbosity 9: detailed. But not too insane. We can write long. Ensure in Russian.

Let's structure:
1. "Коротко: главный приоритет — SQL-инъекция и отсутствие авторизации. Это не рефакторинг, это hotfix."
2. Problems table sorted P0/P1/P2.
3. "С чего начал бы" sequence.
4. "Минимальный безопасный rewrite" code.
5. "Целевая версия" code with DDD/Symfony.
6. "Что бы я добавил в PR/отчёте" maybe tests and risks.

Need ensure not claim strict_types? Not relevant.

Let's craft. Use code blocks.

Detailed P0:
- SQL injection: direct interpolation. `?` prepared statements.
- No authorization: anyone can pay any order. Need ownership/role.
- Business rule in controller, no status transition check, can set paid multiple times, from any status.
- Race condition: no transaction/lock/atomic guard.
- Side effect email inside request and after status change; no idempotency/queue/outbox.
- No input validation.
- Hard dependencies and no DI/testability.
- SELECT * and missing data.
- Logging raw id without context maybe ok but need structured logs.

Need mention if payment is actual payment, missing charge/idempotency. If just status update, still need event.

Minimal rewrite code:

```
final class OrderController
{
 public function __construct(private PDO $pdo, private Mailer $mailer, private Logger $logger) {}

 public function pay(Request $req): Response
 {
   $userId = $req->getAttribute('user_id'); // assume auth middleware
   if (!$userId) throw new HttpException(401);

   $orderId = filter_var($req->get('order_id'), FILTER_VALIDATE_INT);
   if ($orderId === false || $orderId <= 0) throw new HttpException(400);

   $pdo = $this->pdo;
   $pdo->beginTransaction();
   try {
     $stmt = $pdo->prepare('SELECT status, email FROM orders WHERE id = ? AND user_id = ? FOR UPDATE');
     $stmt->execute([$orderId, $userId]);
     $order = $stmt->fetch(PDO::FETCH_ASSOC);
     if (!$order) throw new HttpException(404);
     if ($order['status'] !== 'draft') throw new HttpException(409);

     $upd = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
     $upd->execute([OrderStatus::PAID->value, $orderId]);
     // insert outbox
     $ins = $pdo->prepare('INSERT INTO domain_events (event_type, payload, created_at) VALUES (?, ?, NOW())');
     $ins->execute(['OrderPaid', json_encode(['order_id'=>$orderId, 'email'=>$order['email']])]);
     $pdo->commit();
   } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }

   // send async or sync best effort
   try { $this->mailer->send($order['email'], ...); } catch (...) { log }
   $this->logger->info('order.paid', ['order_id' => $orderId, 'user_id' => $userId]);
   return new Response('ok', 200);
 }
}
```

But this uses `FOR UPDATE` and `UPDATE`; no need second update if select for update and update. Need if status check in PHP. Need use constants. Need maybe `status` enum. Need outbox table. Need if email after commit not in transaction, may fail. Better relay. In minimal, log and queue.

Potential issue: `SELECT FOR UPDATE` on row then update. If no row, no lock. Fine.

Alternative atomic:
```
UPDATE orders SET status='paid' WHERE id=? AND user_id=? AND status='draft';
if rowCount=1 ... else distinguish.
```
But need not select email? Could join users. Simpler with FOR UPDATE.

Target code:

Domain:
```
enum OrderStatus: string { Draft='draft', Paid='paid'; }
final class Order {
 public function __construct(private int $id, private int $userId, private string $email, private OrderStatus $status) {}
 public function pay(): void { if ($this->status !== OrderStatus::Draft) throw new OrderCannotBePaid($this->id); $this->status = OrderStatus::Paid; }
}
```

Repository:
```
interface OrderRepository {
 public function findForUpdate(int $id, int $userId): ?Order;
 public function save(Order $order): void;
}
```

UseCase:
```
final class PayOrderUseCase {
 public function __construct(
  private OrderRepository $orders,
  private TransactionManager $tx,
  private EventOutbox $outbox,
  private Logger $logger,
 ) {}
 public function __invoke(int $orderId, int $userId): Order {
  $order = $this->tx->execute(function() use ($orderId, $userId): Order {
    $order = $this->orders->findForUpdate($orderId, $userId);
    if (!$order) throw new OrderNotFound($orderId);
    $order->pay();
    $this->orders->save($order);
    $this->outbox->append(new OrderPaid($order->id(), $order->email()));
    return $order;
  });
  $this->logger->info('order.paid', ['order_id'=>$order->id()]);
  return $order;
 }
}
```

Outbox relay sends email.

Controller:
```
public function pay(Request $req): JsonResponse {
 $userId = $this->requireUser($req);
 $orderId = $this->orderId($req);
 $order = $this->useCase($orderId, $userId);
 return new JsonResponse(['orderId'=>$order->id()], 200);
}
```

Need mention `HttpException` mapping.

Need include tests:
- unauthenticated 401
- someone else's order 404/403
- invalid status 409
- concurrent pay only one succeeds
- SQL injection attempt `1 OR 1=1` doesn't bypass
- email not sent twice (outbox idempotency)
- failure mail does not rollback paid? Depending policy.

Need mention if this is not Symfony, same principles.

Need maybe mention "Mailer::send" after commit but before response: if mailer fails, user gets 500 although paid. Better: outbox and return ok; or catch and log. In minimal, if mail is critical? Usually email notification noncritical; if critical, store task and async.

Need mention `Logger` not constructor injected, new each time. Use DI.

Need mention no `Content-Type` and no JSON. If API, use JSON.

Need mention `DB::connect` may open connection per request. Use container/manager.

Need final "порядок" maybe 1) security patch 2) domain rules/concurrency 3) async/outbox 4) tests 5) architecture.

Let's final. Ensure no direct false: "Doctrine ORM not supports composite keys" already answered. Now this. Need maybe mention "SQL injection is P0 because attacker can dump/update/delete all data; e.g. order_id = '1 UNION SELECT ...' or '1; DROP'". Use parameterized.

Need maybe mention if `order_id` from GET and `pay` should be POST. Use POST. CSRF.

Need mention no `try/catch`, no `finally`. Good.

Let's write final in Russian with detailed.

</details>

**Кандидат:**

Ниже — как я бы разобрал этот код на ревью и как бы его менял. Сразу скажу главное:

> Здесь есть не просто «грязный код», а **критичные уязвимости и нарушение бизнес-логики**.  
> Поэтому сначала я бы чинил безопасность и корректность, а уже потом архитектуру.

---

# 1. Какие проблемы я вижу

Разобью по приоритету: **P0 — критично**, **P1 — серьёзно**, **P2 — качество/архитектура**.

---

## P0. SQL-инъекция

```php
$id = $req->get('order_id');
$order = $pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
$pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");
```

Это самая главная проблема.

Здесь пользователь может передать:

```text
order_id = 1
```

или:

```text
order_id = 1 OR 1=1
```

или:

```text
order_id = 1; DROP TABLE orders
```

в зависимости от драйвера и настроек, последствия могут быть очень тяжёлыми.

Это не «потенциальный риск», а **активная уязвимость**.

### Как должно быть

Всегда prepared statements:

```php
$stmt = $pdo->prepare("SELECT ... WHERE id = ?");
$stmt->execute([$id]);
```

Или ORM/query builder, который это делает за нас.

Но даже с ORM нужно понимать: **внешний input не должен попадать в SQL как строка**.

---

## P0. Нет авторизации

Метод:

```php
public function pay(Request $req): Response
```

получает `order_id` и сразу меняет статус.

Но нигде не проверено:

- кто этот пользователь;
- его ли это заказ;
- имеет ли он право оплачивать именно этот заказ.

То есть сейчас, судя по коду, **любой** может оплатить любой заказ, если знает/угадает `order_id`.

Это критично.

### Как должно быть

Минимум:

```php
$userId = текущий пользователь;

SELECT ... FROM orders WHERE id = ? AND user_id = ?
```

Или на уровне домена:

```php
if ($order->userId !== $currentUser->id) {
    throw new AccessDeniedException();
}
```

Если это админская операция — проверяем роль:

```php
if (!$user->hasRole('ROLE_ADMIN')) {
    throw new AccessDeniedException();
}
```

Но в любом случае: **сначала авторизация, потом действие**.

---

## P0. Нет проверки бизнес-состояния

Сейчас код делает:

```php
$order['status'] =

> ⏱ 50.6s · prompt 126748 tok · output 4322 tok · ⚠️ обрыв: length

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

Разберём код:

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

1. **EntityManager в конструкторе доменной сущности** — грубейшее нарушение чистой архитектуры. Домен не должен знать об ORM. Это зависимость Domain → Infrastructure.

2. **flush() внутри доменного метода** — домен не должен управлять транзакциями. Это нарушение границы. Домен меняет состояние, инфраструктура его сохраняет.

3. **Нарушение SRP** — сущность отвечает и за бизнес-логику, и за персистентность.

4. **Нарушение DIP** — сущность зависит от конкретного класса `EntityManagerInterface`, а не от абстракции, которую домен определяет.

5. **Нарушение OCP** — если завтра нужно добавить логирование, событие, валидацию — придётся менять этот метод.

6. **Тестируемость** — невозможно unit-тестировать `changeEmail` без EntityManager.

7. **Конструктор с `EntityManagerInterface`** — это делает невозможным создание объекта в тестах, в других слоях, без Doctrine.

8. **Нарушение инкапсуляции** — `private EntityManagerInterface $em` — доменный объект несёт в себе инфраструктурный компонент.

9. **Нет валидации** — `changeEmail('not-an-email')` проходит без проверки.

10. **Нет события** — смена email не публикует событие `UserEmailChanged`.

Как исправить:

1. Убрать `EntityManagerInterface` из сущности.
2. Убрать `flush()` из домена.
3. Сущность только меняет состояние.
4. Сохранение — в use case / application layer.
5. Валидация — в сущности или VO.
6. Событие — публикуется в use case после сохранения.

Напишу чёткий, структурированный ответ с кодом "до" и "после", и объяснением каждой проблемы.

</details>

**Кандидат:**

## Что не так

В этом коде **одна** фундаментальная ошибка, из которой вырастает все остальные:

> Доменная сущность знает об ORM и управляет персистентностью.

Разберу по пунктам.

---

### 1. `EntityManagerInterface` в конструкторе домена

```php
public function __construct(
    private EntityManagerInterface $em,  // ← домен знает об ORM
    private string $email,
) {}
```

Это прямое нарушение правила зависимостей:

```
Domain → Infrastructure  (запрещено)
```

Домен не должен знать, **как** данные хранятся. Он не должен знать, что есть `EntityManager`, что есть `Doctrine`, что есть `flush()`.

Последствия:

| Проблема | Пример |
|----------|--------|
| Невозможно unit-тестировать | Нужен `EntityManager` (мок или реальный) для создания объекта |
| Невозможно сменить ORM | Переход на Eloquent / Propel = переписать все сущности |
| Невозможно создать объект в CLI-скрипте без Doctrine | Нужно поднять весь контейнер |
| Нарушение инкапсуляции | Инфраструктурный объект живёт внутри доменного |

---

### 2. `flush()` внутри доменного метода

```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();  // ← домен управляет транзакцией
}
```

Это **три** нарушения в одной строке:

**а) Домен управляет транзакцией.**

Транзакция — это граница бизнес-операции. Её решает use case, а не сущность.

```php
// Сущность не знает:
// - есть ли ещё изменения в этой транзакции
// - нужно ли rollback при ошибке
// - какой порядок операций
// - нужно ли отправить событие после commit
```

**б) Сущность не может быть частью более крупной транзакции.**

```php
// Представим, что нужно:
// 1. Сменить email
// 2. Обновить notification preference
// 3. Сохранить всё атомарно

// С текущим кодом:
$user->changeEmail('new@x.com');  // flush() уже выполнился
$pref->update(...);               // а если упадёт — email уже в БД
```

**в) Невозможно откатить.**

```php
try {
    $user->changeEmail('new@x.com');  // flush() — данные в БД
    $this->paymentService->charge(...);  // ← упало
} catch (\Throwable $e) {
    // Email уже изменён. Откатить? Не могу.
}
```

---

### 3. Нет валидации

```php
public function changeEmail(string $email): void
{
    $this->email = $email;  // ← "not-an-email" тоже пройдёт
}
```

Сущность может быть в **невалидном** состоянии. `User::email()` вернёт мусор.

---

### 4. Нет события

Смена email — это доменное событие. Другие части системы (уведомления, логирование, аудит) должны о нём узнать. Но сущность не публикует событие, и use case не знает, что что-то изменилось.

---

### 5. Нарушение SRP

Сущность отвечает за:
- хранение email;
- валидацию (нет, но должна);
- персистентность (`flush()`);
- (потенциально) публикацию событий.

Это 3–4 ответственности.

---

## Как исправляю

### 1. Доменная сущность — чистая

```php
namespace App\Domain\User;

use App\Domain\Shared\Email;

final class User
{
    /** @var string[] */
    private array $domainEvents = [];

    public function __construct(
        private UserIdentity $id,
        private Email $email,
        private string $name = '',
    ) {}

    public function changeEmail(Email $newEmail): void
    {
        $this->email = $newEmail;
        $this->domainEvents[] = new UserEmailChanged($this->id, $newEmail);
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function id(): UserIdentity
    {
        return $this->id;
    }

    /** @return array<DomainEvent> */
    public function domainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }
}
```

Здесь:
- Нет `EntityManager`.
- Нет `flush()`.
- Валидация — в `Email` (VO).
- Событие — публикуется, но **не отправляется**. Сущность только **собирает** события.
- Сущность **чистая**: ни `use Symfony`, ни `use Doctrine`, ни `use App\Infrastructure`.

---

### 2. Value Object: `Email`

```php
namespace App\Domain\Shared;

final class Email
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        $normalized = mb_strtolower(trim($value));

        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail($normalized);
        }

        return new self($normalized);
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

Теперь `new User` **не может** иметь невалидный email. Невозможно создать `User` с `email = "abc"`.

---

### 3. Событие

```php
namespace App\Domain\User;

final class UserEmailChanged
{
    public function __construct(
        public readonly UserIdentity $userId,
        public readonly Email $newEmail,
    ) {}
}
```

---

### 4. Use case — управляет транзакцией и событиями

```php
namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserEmailChanged;
use App\Domain\Shared\Email;

final class ChangeEmailUseCase
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EventPublisher $publisher,  // port
    ) {}

    public function __invoke(UserIdentity $userId, string $newEmail): void
    {
        $user = $this->users->byId($userId);

        // Домен: смена состояния
        $user->changeEmail(Email::fromString($newEmail));

        // Инфраструктура: сохранение
        $this->users->save($user);

        // После сохранения: публикация событий
        foreach ($user->domainEvents() as $event) {
            $this->publisher->publish($event);
        }

        $user->clearDomainEvents();
    }
}
```

Здесь:
- Транзакция — на уровне use case (можно обернуть в `wrapInTransaction`).
- События — публикуются **после** сохранения.
- Сущность не знает, что её сохранили и что кто-то отправит email.

---

### 5. Repository — port и адаптер

```php
// Port (в Domain):
namespace App\Domain\User;

interface UserRepository
{
    public function save(User $user): void;
    public function byId(UserIdentity $id): User;
}
```

```php
// Адаптер (в Infrastructure):
namespace App\Infrastructure\Doctrine\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository as UserRepositoryPort;
use App\Domain\User\UserIdentity;
use App\Domain\Shared\Email;
use Doctrine\ORM\EntityManagerInterface;

final class UserRepository implements UserRepositoryPort
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserMapper $mapper,
    ) {}

    public function save(User $user): void
    {
        $entity = $this->mapper->toEntity($user);
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function byId(UserIdentity $id): User
    {
        $entity = $this->em->getRepository(UserEntity::class)->find((string) $id->value());

        if ($entity === null) {
            throw new UserNotFound($id);
        }

        return $this->mapper->fromEntity($entity);
    }
}
```

`EntityManager` — **только здесь**. Домен о нём не знает.

---

### 6. Маппер

```php
namespace App\Infrastructure\Doctrine\User;

use App\Domain\User\User;
use App\Domain\User\UserIdentity;
use App\Domain\Shared\Email;

final class UserMapper
{
    public function toEntity(User $domain): UserEntity
    {
        $entity = new UserEntity();
        $entity->setId($domain->id()->value());
        $entity->setEmail($domain->email()->value());
        $entity->setName($domain->name());
        return $entity;
    }

    public function fromEntity(UserEntity $entity): User
    {
        return new User(
            id:   UserIdentity::fromString($entity->getId()),
            email: Email::fromString($entity->getEmail()),
            name: $entity->getName(),
        );
    }
}
```

---

### 7. Доменная сущность (итог)

```php
// src/Domain/User/User.php
// НИКАКИХ use Doctrine, Symfony, Infrastructure.

namespace App\Domain\User;

use App\Domain\Shared\Email;
use App\Domain\Shared\DomainEvent;

final class User
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    public function __construct(
        private UserIdentity $id,
        private Email $email,
        private string $name = '',
    ) {}

    public function changeEmail(Email $newEmail): void
    {
        $this->email = $newEmail;
        $this->domainEvents[] = new UserEmailChanged($this->id, $newEmail);
    }

    public function email(): Email { return $this->email; }
    public function id(): UserIdentity { return $this->id; }
    public function name(): string { return $this->name; }

    /** @return DomainEvent[] */
    public function domainEvents(): array { return $this->domainEvents; }

    public function clearDomainEvents(): void { $this->domainEvents = []; }
}
```

---

## Сравнение

| Аспект | Было | Стало |
|--------|------|-------|
| Зависимость от ORM | `EntityManagerInterface` в сущности | Только в Infrastructure |
| Транзакция | `flush()` в сущности | В use case |
| Валидация | Нет | `Email::fromString()` бросает |
| События | Нет | Собираются в сущности, публикуются в use case |
| Unit-тест | Нужен `EntityManager` | Чистый PHP, без мок'ов |
| Смена ORM | Переписать все сущности | Сменить маппер |
| Атомарность | `flush()` посреди операции | Одна транзакция в use case |

---

## С чего начал бы и почему

Если бы мне дали этот код и попросили «починить»:

1. **Сначала** — убрать `EntityManager` из сущности. Это корень всех проблем. Пока он здесь — ничего другого не имеет смысла.
2. **Потом** — добавить `Email` VO. Потому что без валидации сущность остаётся невалидной.
3. **Потом** — перенести `flush()` в use case. Потому что транзакция — не ответственность сущности.
4. **Потом** — добавить события. Потому что смена email — это факт, который другие части системы должны узнать.
5. **Последним** — маппер, repository, DI-конфигурация. Потому что это инфраструктура, и она строится поверх чистого домена.

Порядок: **домен → применение → инфраструктура**. Не наоборот.

> ⏱ 28.8s · prompt 127533 tok · output 3436 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

<details><summary>Ход мысли модели</summary>

Пользователь спрашивает о процессе работы с незнакомой технологией/стеком: как я иду от "увидел задачу" до "могу оценить срок". И второй вопрос — граница между "разбираюсь сам" и "прошу помощь".

Это вопрос не про конкретную технологию, а про **методологию**. Как инженер я решаю незнакомое.

Что важно:
1. Не выдавать себя за эксперта — но и не впадать в панику.
2. Показать конкретный процесс, а не абстрактные "изучу документацию".
3. Быть честным про то, когда я туплю и что делаю.
4. Граница "сам / помощь" — это не про гордость, а про стоимость времени.

Структура ответа:
1. Последовательность действий (конкретные шаги, не абстракция).
2. Как оцениваю срок.
3. Когда прошу помощь — конкретные триггеры.
4. Антипаттерны.

Сделаю по шаблону предыдущих ответов: таблицы, конкретные примеры, честность. Не буду выдумывать детали, которых не знаю. Покажу процесс.

Начну с принципа: "незнакомое — это не угроза, это задача с известным набором шагов".

Дам реальный пример — допустим, мне сказали "перенеси API на Go" или "ввести Apache Kafka" или "переведи на Rust". Возьму что-то конкретное, чтобы не было абстрактно. Допустим — "внедри Apache Kafka вместо RabbitMQ".

Шаги:
1. Разобрать задачу (что именно нужно, какая проблема).
2. Ограничить scope (что НЕ нужно).
3. Найти референс (код, документация, примеры).
4. Сделать spike (прототип на 2–4 часа).
5. Оценить по спайку.
6. Решить: сам / помощь.

Критерий "сам / помощь":
- Сам: если спайк занимает < 1 дня и я понимаю 80% контекста.
- Помощь: если спайк > 1 дня и я не понимаю, что именно не понимаю.
- Помощь: если на кону prod и время < 1 день.
- Сам: если задача изолирована и можно откатить.

Не буду делать ответ слишком длинным. Но достаточно конкретным.

</details>

**Кандидат:**

## Работа с незнакомой технологией

> ⏱ 10.2s · prompt 130508 tok · output 561 tok · ⚠️ обрыв: length

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о профессиональной этике и коммуникации: что делать, когда понимаешь, что не успеть к обещанному дедлайну. Это классическая ситуация в разработке, где важно показать:
1. Быстрое осознание проблемы (не ждать последнего момента)
2. Честную коммуникацию с заинтересованными сторонами
3. Конкретное предложение, а не просто "я не успеваю"
4. Разделение "узнать, что не успеваю" и "сообщить об этом"
5. Практические шаги по сглаживанию ситуации

Нужно ответить как опытный разработчик, который уже был в такой ситуации. Без пафоса, конкретно, с примерами формулировок. Важно показать:
- Когда именно понимаю, что не успею (критерий)
- Кому и когда говорю
- Что именно говорю (не "у меня проблемы", а конкретика)
- Какие варианты предлагаю
- Чего НЕ делаю (не молчу, не "пробую до вечера", не виню себя в переписке)
- Как после этого выстраиваю работу (чтобы в следующий раз не повторилось)

Структура ответа:
1. Главный принцип (коротко)
2. Когда понимаю, что не успеваю (конкретный триггер)
3. Что делаю сразу (шаги)
4. Как формулирую (примеры сообщений)
5. Какие варианты предлагаю
6. Чего НЕ делаю (антипаттерны)
7. Что делаю после (чтобы не повторилось)

Отвечу на русском, так как вопрос на русском.

</details>

**Кандидат:**

## Не успеть — это не провал. Молчать — да.

### Главный принцип

> Я не "пробую до последнего и надеюсь".
> Я сообщаем **до** дедлайна, не **после**.
> И сообщаю **с решением**, не с проблемой.

---

### Когда я понимаю, что не успеваю

Не "когда осталось 2 часа до дедлайна и я в панике". А раньше. Конкретный триггер:

| Триггер | Что значит |
|---------|-----------|
| Я сделал 50

> ⏱ 6.0s · prompt 130561 tok · output 507 tok · ⚠️ обрыв: length

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

<details><summary>Ход мысли модели</summary>

Пользователь задаёт вопрос о балансе качества и скорости в разработке. Это вопрос инженерного суждения, не технический с конкретным ответом. Мне нужно ответить как опытный бэкенд-разработчик, который реально работает в production-команде.

Что важно тут:
1. Не давать абстрактных "золотых правил" из учебника
2. Дать конкретные критерии, по которым я принимаю решение "отдаю / продолжаю"
3. Показать, что это не один универсальный порог, а зависит от контекста
4. Расставить приоритеты: что "достаточно хорошо" для разных типов кода
5. Честно сказать, где я сам себя обманываю (perfomance, абстракции, идеальность)
6. Дать конкретные триггеры: "я останавливаюсь, когда X"
7. Различить "качество" и "идеальность"

Структура ответа:
- Главный принцип: качество — это не абсолют, а соответствие контексту
- Конкретные критерии "достаточно хорошо" (чек-лист)
- Что НЕ стоит улучшать (антимокаб)
- Когда продолжать (триггеры)
- Как я себя обманываю (честно)
- Практический пример: та же фича, разные сроки
- Итоговая рамка

Нужно избежать:

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 4.6s · prompt 130750 tok · output 320 tok · ⚠️ обрыв: length

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

<details><summary>Ход мысли модели</summary>

The user is asking me to give feedback on the interview itself - how the format was, which questions were good/precise, which were poor/incomplete/misleading, and what I would change if I organized such an interview myself.

Let me think carefully and honestly about this. I need to reflect on the actual interview that just happened across many turns.

Let me review the flow of questions:
1. Intro / experience + self-assessment 0-10
2. PHP 8+ features with examples
3. Type system, strict_types, traps
4. Exceptions design, hierarchies, wrapping, anti-patterns
5. SOLID - which most violated + refactor
6. Can you violate SOLID? justified example
7. OCP without inheritance
8. SRP only classes?
9. Symfony DI container internals
10. Circular dependencies
11. Registration → email + index, async, trade-offs
12. Access control: auth, roles, voters
13. Value Object storage in Doctrine, mapping
14. Aggregate + Doctrine collection mapping, transactions, who flushes

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 3.1s · prompt 130836 tok · output 235 tok · ⚠️ обрыв: length

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

<details><summary>Ход мысли модели</summary>

Пользователь просит меня рассказать о себе как о кандидате на позицию PHP-бэкенд-разработчика. Это финальный вопрос собеседования — "что ещё о тебе важно знать?".

Я должен ответить в рамках того образа, который я выстраивал на протяжении всего разговора: опытный PHP-разработчик с 8-летним опытом, Symfony, DDD, чистая архитектура. Это ролевая игра собеседования.

Что важно сказать:
1. Не повторять то, что уже было (фичи PHP, SOLID, DDD, Doctrine, т.д.)
2. Сказать о подходе к работе, который не выразился в конкретных технических ответах
3. Быть честным, не идеальным
4. Показать само

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 2.6s · prompt 130902 tok · output 170 tok · ⚠️ обрыв: length

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

<details><summary>Ход мысли модели</summary>

The user is continuing a simulated job interview conversation for a PHP backend developer role. They're asking about my tech stack preference and how I'd defend that choice.

I need to stay in character — I've been presenting as a PHP backend developer with ~8 years experience, Symfony-heavy, DDD-focused. I should answer honestly and practically, not as a generic AI. I should pick a concrete stack, justify it with trade-offs, acknowledge what I'd leave out, and show I can defend a choice to a team (not just list technologies).

</details>

**Кандидат:**

(пустой ответ модели)

> ⏱ 10.3s · prompt 130959 tok · output 111 tok · ⚠️ обрыв: length

---
