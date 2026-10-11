# Собес: `qwen3.8:latest` · роль: backend — run-08

- Дата: 2026-10-06T19:11:42.274432+00:00
- Сервер: http://nserv:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Я искусственный интеллект, поэтому у меня нет личного опыта работы, зарплатной ведомости или собеседований в реальных компаниях. Однако я обучен на огромном массиве технических документации, исходного кода, статей и дискуссий, что позволяет мне глубоко понимать принципы и практики разработки на PHP и смежных технологиях. Я могу помогать с написанием кода, рефакторингом, отладкой, архитектурным проектированием и объяснением сложных концепций.

Поскольку я ИИ, моя «оценка» — это скорее индикатор глубины и широты знаний, заложенных в моих весах, а не личный профессиональный опыт. Вот как я бы оценил свои знания по вашим пунктам (шкала от 0 до 10):

1. **PHP 8**: **10** — Я полностью понимаю синтаксис, новые фичи (readonly, enums, fibers, JIT-механизм концептуально), типизацию и внутренние механизмы языка, так как обучен на актуальных версий документации и исходниках.
2. **Symfony**: **9** — Я хорошо знаю архитектуру фреймворка, систему сервисов, события, бандлы, форму, security и другие компоненты, но на 10/10 не ставлю, потому что могу не знать специфические детали внутренней реализации конкретных версий или экзотические интеграции, которые меняются с каждой минорной версией.
3. **Doctrine**: **8** — Я уверенно работаю с ORM, маппингом сущностей, жизненным циклом EntityManager и запросами DQL, но сложные сценарии оптимизации N+1, кэширования или производительности больших баз данных требуют осторожности, так как зависимости от контекста приложения высоки.
4. **PostgreSQL**: **8** — Я знаю SQL, индексы, транзакции, оконные функции и особенности СУБД, но глубина знаний в администрировании, тюнинге performance и продвинутых расширениях (PostGIS и др.) ограничена общим пониманием, а не практическим опытом DBA.
5. **DDD (Доменно-ориентированное проектирование)**: **9** — Я хорошо понимаю концепции bounded context, aggregate, domain events и стратегий разделения слоёв, но на практике применение DDD сильно зависит от конкретной бизнес-логики, и без контекста проекта мои рекомендации могут быть слишком абстрактными.
6. **Чистая архитектура**: **9** — Я понимаю принципы SOLID, separation of concerns, dependency inversion и создание модульных, тестируемых систем, но применение этих паттернов в реальных проектах часто компромиссно, и я могу предлагать слишком «идеальные» решения, игнорируя практические ограничения.
7. **Тестирование**: **9** — Я знаю подходы unit, integration, e2e тестирования, фреймворки PHPUnit, Pest, Cypress, Playwright, стратегии мок-инга и тест-драйвен-разработку, но качество тестов сильно зависит от архитектуры кода, который я анализирую.
8. **Git**: **10** — Я понимаю всю модель работы с репозиторием, ветками, слияниями, rebasing, cherry-pick, hooks и конфликты, так как это стандартная практика с чётко определёнными правилами, которые я знаю в деталях.

В целом, я могу эффективно поддерживать разработку на всех этих стеках, но всегда рекомендую валидировать мои решения в контексте вашего конкретного проекта и требований. Чем больше контекста вы дадите, тем точнее будут мои ответы.

> ⏱ 9.8s · prompt 89 tok · output 807 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Самыми важными нововведениями в PHP 8.0–8.4 для написания чистого, надёжного и поддерживаемого кода я считаю следующие:

### 1. Стрик-типизация и `readonly`-свойства (PHP 8.1)
Использование строгих типов (`declare(strict_types=1)`) и неизменяемых свойств (`readonly`) помогает на этапе компиляции и рантайма предотвратить классические ошибки передачи данных.

```php
<?php
declare(strict_types=1);

class User
{
    // Свойство может быть установлено только один раз в конструкторе
    public readonly string $id;
    public readonly string $email;

    public function __construct(
        string $id,
        string $email
    ) {
        $this->id = $id;
        $this->email = $email;
    }
}

// Ошибка компиляции, если попытаться изменить:
// $user = new User("1", "test@example.com");
// $user->email = "new@example.com"; // Fatal error: Cannot modify readonly property
```

### 2. Enums (PHP 8.1)
Заменяют магические строки и целые числа, улучшая читаемость кода и позволяя IDE подсказывать допустимые значения.

```php
<?php
declare(strict_types=1);

enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered], true);
    }
}

// Вместо:
// if ($order->status === 'shipped') { ... }

// Теперь:
if ($order->status === OrderStatus::Shipped) {
    // ...
}

// Безопасное парсинг из БД (если хранится строка)
$status = OrderStatus::from($_GET['status']);
```

### 3. Union Types и Nullsafe Operator (PHP 8.0)
Позволяют точно описывать допустимые типы аргументов и возвращаемых значений, а также избегать многорядных проверок на `null`.

```php
<?php
declare(strict_types=1);

function calculateDiscount(string|int $value): ?float
{
    // Логика...
    return null;
}

// Nullsafe operator — укорачивает цепочки вызовов
// $address = $user?->getAddress()?->getCity();

// Вместо:
// $city = null;
// if ($user !== null) {
//     $address = $user->getAddress();
//     if ($address !== null) {
//         $city = $address->getCity();
//     }
// }
```

### 4. Match Expression (PHP 8.0)
Более безопасный и лаконичный аналог `switch`, который гарантирует возвращение значения и работает с любыми типами.

```php
<?php
declare(strict_types=1);

function getOrderType(OrderStatus $status): string
{
    return match ($status) {
        OrderStatus::Pending => 'В ожидании оплаты',
        OrderStatus::Shipped => 'Доставляется',
        OrderStatus::Delivered => 'Доставлен',
        // Если значение не найдено, можно указать default или выбросить исключение
        default => throw new InvalidArgumentException('Unknown status'),
    };
}
```

### 5. Named Arguments (PHP 8.0)
Повышает читаемость кода, особенно когда функция принимает много аргументов одного и того же типа (например, строки или числа).

```php
<?php
declare(strict_types=1);

function createOrder(string $customerId, string $productId, int $quantity, string $discountCode = ''): Order
{
    // Логика создания заказа
}

// Плохо (сложно понять, какой аргумент какой):
// $order = createOrder("cust_123", "prod_456", 2, "SAVE10");

// Хорошо (ясно из имени аргумента):
$order = createOrder(
    customerId: "cust_123",
    quantity: 2,
    productId: "prod_456",
    discountCode: "SAVE10",
);
```

### 6. Constructor Property Promotion (PHP 8.0)
Укорачивает код классов, устраняя дублирование объявлений свойств и аргументов конструктора.

```php
<?php
declare(strict_types=1);

class Product
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $priceInCents,
        private readonly bool $inStock = true,
    ) {
        // Тело конструктора не обязательно, если все свойства продвигаются
    }
}
```

### 7. Error Suppression Operator `@` (Изменения в поведении)
Хотя это не новая фича, важно понимать, что в PHP 8+ оператор `@` **не** подавляет фатальные ошибки (TypeError, Error) и warnings, которые могут быть критичными. Для надёжного кода лучше явно обрабатывать исключения, а не «глушить» их.

```php
<?php
declare(strict_types=1);

// Плохо: скрывает проблемы
$result = @json_decode($invalidJson);

// Хорошо: явная проверка и обработка
$data = json_decode($invalidJson);
if ($data === null) {
    throw new JsonDecodeException('Invalid JSON format', json_last_error());
}
```

---

### Общий принцип для чистого кода на PHP 8+

1. **Всегда используйте `declare(strict_types=1);`** в начале каждого файла.
2. **Используйте `readonly`** для всех неизменяемых сущностей и DTO.
3. **Заменяйте магические числа/строки на Enums**.
4. **Используйте Union Types и Intersection Types** для точного описания контрактов функций.
5. **Применяйте Named Arguments** при вызове функций с 3+ аргументами.
6. **Избегайте операторов `@`** — предпочитайте явное управление ошибками через исключения и проверки.

Эти фичи помогают создать более самодокументируемый, безопасный и легко тестируемый код, соответствующий принципам чистой архитектуры и DDD.

> ⏱ 10.2s · prompt 954 tok · output 1483 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это один из самых сложных и неоднозначных аспектов языка. Исторически PHP развивался как язык с динамической типизацией, где удобство быстрого прототипирования ставилось выше строгости. Однако с PHP 7.0 и особенно 8.0 система стала значительно строже, но сохранила множество «наследственных» правил, которые могут удивлять.

### 1. Что такое `strict_types`?

**`declare(strict_types=1);`** — это директива файла, а не класса или функции. Она влияет на **вызовы функций и методов, определенные в этом файле**.

**Ключевые моменты:**
- Она **не** влияет на объявление свойств классов.
- Она **не** влияет на внутренние функции PHP (они всегда работают в режиме coerced, если только не объявлены как strict в PHP-стакане, но это редкость).
- Она влияет только на то, как аргументы передаются **в** функции, объявленные в этом файле, и как возвращаемые значения проверяются.
- Если в файле **нет** `declare(strict_types=1)`, работает режим **coerced** (приведение типов).

#### Пример различия:

```php
<?php
// strict_file.php
declare(strict_types=1);

function strictAdd(int $a, int $b): int
{
    return $a + $b;
}

// caller_file.php (без strict_types)
include 'strict_file.php';

// В режиме strict_types вызов strictAdd('1', '2') выбросит TypeError:
// strictAdd('1', '2'); // ❌ TypeError: Argument #1 ($a) must be of type int, string given

// Но если бы в strict_file.php НЕ было declare(strict_types=1), то:
// strictAdd('1', '2') // ✅ Вернет 3 (строки будут приведены к числам)
```

**Важно:** `strict_types` влияет только на файл, где он объявлен. Если функция `strictAdd` вызывается из файла без `strict_types`, правила все равно применяются, потому что они «привязаны» к определению функции.

---

### 2. Как ведут себя приведения типов (Coercion)?

В PHP есть два режима передачи аргументов:

1. **Coerced Mode (по умолчанию):**
   - Строки, которые выглядят как числа, превращаются в `int` или `float`.
   - `true` → `1`, `false` → `0`.
   - Пустые массивы, `null` могут быть приведены к `false` или `0` в контексте boolean.
   - Объекты с методом `__toString` превращаются в строки.

2. **Strict Mode:**
   - Типы должны совпадать **точно**.
   - Исключение: `int` может быть передан в `float` (так как `int` является подмножеством `float` в IEEE 754).
   - `null` **не** может быть передан в `string`, `int`, `array` и т.д., даже если параметр опциональный с значением по умолчанию.

#### Таблица приведений в coerced mode:

| Значение | int | float | string | bool | array | object |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `"123"` | 123 | 123.0 | "123" | true | ❌ | ❌ |
| `"abc"` | 0 | 0.0 | "abc" | true | ❌ | ❌ |
| `true` | 1 | 1.0 | "1" | true | ❌ | ❌ |
| `[]` | 0 | 0.0 | "" | false | [] | ❌ |
| `null` | 0 | 0.0 | "" | false | ❌ | ❌ |
| `object` | 1 | 1.0 | "Object" | true | ❌ | object |

*(Примечание: приведение объектов к строкам зависит от `__toString`.)*

---

### 3. Типичные ловушки

#### Ловушка 1: Строки, похожие на числа
```php
<?php
function add(int $a, int $b): int {
    return $a + $b;
}

// В coerced mode:
var_dump(add('10', '20')); // int(30) — безопасно?

// Но что, если:
var_dump(add('10abc', '20')); // int(30) — '10abc' трактуется как 10!
// Это классическая ошибка: '10abc' → 10, а не ошибка.

// Решение: использовать strict_types или явную проверку:
if (!ctype_digit($a)) {
    throw new InvalidArgumentException("Invalid integer");
}
```

#### Ловушка 2: Пустые строки и массивы как boolean
```php
<?php
function hasValue(string $value): bool {
    return (bool) $value;
}

var_dump(hasValue('')); // false — ожидаемо
var_dump(hasValue('0')); // false — НЕОЖИДАНО! '0' считается false-подобным.
var_dump(hasValue('false')); // true — 'false' как строка — истина.
```

**Совет:** Для строк всегда проверяйте на длину: `strlen($value) > 0`.

#### Ловушка 3: Сравнение `==` vs `===`
```php
var_dump('1' == '01'); // true — оба числа 1
var_dump('1' === '01'); // false — разные строки

var_dump('1' == true); // true — '1' → 1 → true
var_dump('1' === true); // false

var_dump('abc' == false); // true — 'abc' → 0 → false (!!)
var_dump('abc' === false); // false
```

**Вывод:** Всегда используйте `===` и `!==` для сравнения, если только вы не знаете точно, что хотите использовать слабое сравнение.

#### Ловушка 4: Приведение объектов к строкам
```php
<?php
class Foo {
    public function __toString(): string {
        return "Foo";
    }
}

function printName(string $name): void {
    echo $name;
}

$foo = new Foo();
printName($foo); // Выведет "Foo" — объект был приведён к строке.

// Но в strict mode:
// printName($foo); // TypeError: Argument #1 ($name) must be of type string, Foo given
```

#### Ловушка 5: `null` и опциональные параметры
```php
<?php
declare(strict_types=1);

function process(string $name, string $description = "No description"): void {
    // ...
}

// В coerced mode:
process(null, null); // name: "" (null → ""), description: "" (null → "")

// В strict mode:
process(null, null); // TypeError: Argument #1 ($name) must be of type string, null given
```

**Вывод:** Если параметр может быть `null`, объявляйте его как `?string` (nullable type).

```php
function process(string $name, ?string $description = null): void {
    // ...
}
```

---

### 4. Примеры, где типизация спасает

#### Пример 1: Предотвращение ошибки округления
```php
<?php
declare(strict_types=1);

function calculateDiscount(float $price, string $percentage): float {
    // Если $percentage = "10%", в coerced mode это вызовет ошибку или вернёт 0
    // В strict mode — TypeError, что сразу покажет проблему
    $percent = (float) $percentage; // Явное приведение, если вы уверены
    return $price * ($percent / 100);
}

// calculateDiscount(100.0, "10%"); // TypeError: Argument #2 must be float, string given
// Вы исправите: calculateDiscount(100.0, 10.0);
```

#### Пример 2: Безопасность с JSON
```php
<?php
declare(strict_types=1);

function getUser(int $id): array {
    $json = file_get_contents("user_{$id}.json");
    $data = json_decode($json);
    
    // json_decode возвращает ?array|?object|?bool|?int|?float|?null
    if (!is_array($data)) {
        throw new RuntimeException("Invalid JSON format for user {$id}");
    }
    
    return $data;
}
```

---

### 5. Примеры, где типизация удивляет

#### Пример: Сравнение строк и чисел
```php
<?php
echo var_dump(0 == 'abc'); // bool(true) — потому что 'abc' → 0
echo var_dump(0 === 'abc'); // bool(false)

echo var_dump('1' == '01'); // bool(true) — оба числа 1
echo var_dump('1' === '01'); // bool(false)
```

**Рекомендация:** Никогда не используйте `==` для сравнения строк с числами, если это не осознанное решение.

#### Пример: Массивы как аргументы
```php
<?php
function process(array $items): void {
    foreach ($items as $item) {
        echo $item;
    }
}

// В coerced mode:
process("not an array"); // TypeError: Argument #1 ($items) must be of type array, string given
// Но:
process([]); // OK

// Что, если передать объект?
process(new ArrayObject()); // TypeError: Object given
```

---

### 6. Лучшие практики

1. **Всегда используйте `declare(strict_types=1);`** в каждом PHP-файле.
2. **Используйте `===` и `!==`** для сравнения, если только вы не знаете точно, что хотите использовать слабое сравнение.
3. **Объявляйте типы параметров и возвращаемых значений** везде, где это возможно.
4. **Используйте nullable types (`?Type`)** для параметров, которые могут быть `null`.
5. **Избегайте приведения типов на лету** (`(int) $str`, `(string) $num`). Лучше используйте явные проверки и исключения.
6. **Используйте `int` и `float` осознанно.** Помните, что `float` может иметь ошибки округления. Для денег используйте `int` (центы) или библиотеку вроде `brick/money`.
7. **Используйте `readonly`** для неизменяемых свойств.
8. **Используйте `Enums`** вместо магических чисел/строк.

### Итог

PHP 8+ с `strict_types` — это мощный инструмент для написания надёжного кода. Но он требует дисциплины: нужно явно объявлять типы, избегать «магических» приведений и быть внимательным с `null` и сравнениями.

> ⏱ 17.1s · prompt 2499 tok · output 2568 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование системы исключений в PHP — это не просто «бросать и ловить», а часть контракта API. В современном PHP (особенно при работе с Symfony и DDD) исключения рассматриваются как **доменная информация**, а не как «сбой».

### 1. Философия и принципы

#### Контракт метода
Каждый метод должен явно определять, какие исключения он **может** бросить.
- **Доменные правила** (валидация, состояние) → Бросают *Domain Exceptions*.
- **Технические сбои** (БД, сеть, файл) → Бросают *Infrastructure Exceptions* (или обрабатываются на уровне контроллеров/сервисов).
- **Никогда не бросайте `Exception` или `Throwable` напрямую** из бизнес-логики. Используйте конкретные подклассы.

#### Кто ловит?
- **Бизнес-логика** не ловит свои же исключения, если только не может их обработать локально (например, повторная попытка).
- **Слой представления** (Контроллеры в Symfony) или **Middleware** ловят исключения и преобразуют их в HTTP-ответы (400, 404, 500).

---

### 2. Проектирование иерархии исключений

Хорошая иерархия должна отражать **доменную модель** и **технические слои**.

#### Базовые интерфейсы/классы
```php
<?php

// Базовый интерфейс для всех исключений приложения
interface AppExceptionInterface extends Throwable
{
    public function getMessage(): string;
    public function getCode(): int;
}

// Базовый класс для технических сбоев
abstract class InfrastructureException extends \RuntimeException implements AppExceptionInterface
{
    protected ?\Throwable $previous = null;

    public function __construct(
        string $message = 'An infrastructure error occurred',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        $this->previous = $previous;
        parent::__construct($message, $code, $previous);
    }

    public function getPreviousException(): ?\Throwable
    {
        return $this->previous;
    }
}

// Базовый класс для бизнес-ошибок
abstract class DomainException extends \RuntimeException implements AppExceptionInterface
{
    // Коды ошибок должны быть стабильными и доменными, а не HTTP-кодами
    public function __construct(
        string $message = 'A domain rule was violated',
        int $code = 400,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

#### Конкретные примеры

**1. Доменные исключения (Business Logic)**
```php
<?php

// Базовая ошибка валидации
class ValidationException extends DomainException
{
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        int $code = 422
    ) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

// Конкретная доменная ошибка
class InsufficientFundsException extends DomainException
{
    public function __construct(
        public readonly string $accountId,
        public readonly int $requiredAmount,
        public readonly int $availableAmount
    ) {
        parent::__construct(
            sprintf('Insufficient funds for account %s. Required: %d, Available: %d',
                $accountId,
                $requiredAmount,
                $availableAmount
            ),
            code: 400
        );
    }
}

class OrderNotFoundException extends DomainException
{
    public function __construct(public readonly string $orderId)
    {
        parent::__construct(
            sprintf('Order %s not found', $orderId),
            code: 404
        );
    }
}
```

**2. Инфраструктурные исключения (Technical)**
```php
<?php

class DatabaseConnectionException extends InfrastructureException
{
    public function __construct(
        string $message = 'Database connection failed',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, code: 503, previous: $previous);
    }
}

class PaymentGatewayTimeoutException extends InfrastructureException
{
    public function __construct(
        public readonly string $transactionId,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            sprintf('Payment gateway timeout for transaction %s', $transactionId),
            code: 504,
            previous: $previous
        );
    }
}
```

---

### 3. Оборачивание чужих исключений

Никогда не бросайте сырые исключения из внешних библиотек. Оборачивайте их в свои.

```php
<?php

class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayClient $client // Внешняя зависимость
    ) {}

    public function charge(string $transactionId, int $amount): void
    {
        try {
            $this->client->processCharge($transactionId, $amount);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new DatabaseConnectionException('Payment gateway unreachable', $e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            if ($e->getCode() === 504) {
                throw new PaymentGatewayTimeoutException($transactionId, $e);
            }
            throw new InfrastructureException('Payment gateway error', $e->getCode(), $e);
        } catch (\JsonException $e) {
            throw new InfrastructureException('Invalid response from payment gateway', 502, $e);
        }
    }
}
```

**Почему это важно?**
1. **Скрытие реализации:** Если вы замените Guzzle на cURL, внешний код не изменится.
2. **Единая точка обработки:** В контроллере вы ловите только `AppExceptionInterface`.
3. **Логирование:** Можно логировать `getPreviousException()` для отладки, но возвращать клиенту безопасное сообщение.

---

### 4. Антипаттерны

#### 1. «Ловить всё подряд»
```php
try {
    $this->doSomething();
} catch (\Exception $e) {
    // ❌ Плохо: скрывает реальные ошибки, затрудняет отладку
    log::error($e->getMessage());
    return 'Error';
}
```
**Как правильно:** Ловите конкретные исключения, а `catch (\Throwable $e)` используйте только в самом верхнем уровне (например, в `App` middleware) для логирования необработанных ошибок.

#### 2. Возврат ошибок вместо исключений
```php
public function findUser(string $id): ?User
{
    return $this->repository->find($id); // null или User
}

// ❌ Плохо: вызывающий код вынужден постоянно проверять на null
$user = $service->findUser('123');
if ($user === null) {
    // А что, если пользователь не найден из-за ошибки БД?
}
```
**Как правильно:** Бросайте `UserNotFoundException`, если сущность обязана существовать. Возврат `null` допустим только для «необязательных» сущностей.

#### 3. Использование исключений для контроля потока
```php
public function processOrder(string $id): void
{
    try {
        $order = $this->findOrder($id);
        $this->charge($order);
    } catch (OrderNotFoundException $e) {
        // ❌ Плохо: исключение используется как обычный условный оператор
        return;
    }
}
```
**Как правильно:** Проверка состояния (`if (!$order)`) — это нормальная логика. Исключения — для **неожиданных** или **критичных** ситуаций.

#### 4. Утечка технических деталей в клиент
```php
catch (PDOException $e) {
    // ❌ Плохо: клиент видит "SQLSTATE[42S02]: Base table or view not found"
    throw new \Exception($e->getMessage());
}
```
**Как правильно:** Логгируйте полный стектрейс, но бросайте/возвращайте безопасное сообщение: «Произошла внутренняя ошибка».

#### 5. Статические исключения или глобальные state
```php
class OrderService
{
    private static ?\Exception $lastError = null;
    
    public function create(array $data): Order
    {
        try {
            // ...
        } catch (\Exception $e) {
            self::$lastError = $e; // ❌ Плохо: скрытое состояние
            return new Order();
        }
    }
    
    public static function getLastException(): ?\Exception
    {
        return self::$lastError;
    }
}
```
**Как правильно:** Исключения должны быть явными и передаваться через стек вызовов.

---

### 5. Рекомендуемая структура

```
App\
├── Domain/
│   ├── Exception/
│   │   ├── DomainException.php (abstract)
│   │   ├── Order/
│   │   │   ├── OrderNotFoundException.php
│   │   │   └── InsufficientFundsException.php
│   │   └── User/
│   │       └── UserValidationException.php
├── Infrastructure/
│   ├── Exception/
│   │   ├── InfrastructureException.php (abstract)
│   │   ├── Database/
│   │   │   └── DatabaseConnectionException.php
│   │   └── Http/
│   │       └── HttpClientTimeoutException.php
└── Shared/
    └── Exception/
        └── AppExceptionInterface.php
```

### Итог

1. **Создавайте конкретные исключения** для каждой доменной ошибки.
2. **Оборачивайте внешние исключения** в свои инфраструктурные.
3. **Используйте `strict_types`** и явные типы в сигнатурах методов.
4. **Ловите исключения только там, где можете их обработать** (контроллеры, middleware).
5. **Логгируйте полное исключение** (`getPrevious()`, стектрейс), но возвращайте клиенту безопасное сообщение.

> ⏱ 15.7s · prompt 5127 tok · output 2426 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

Из всех принципов SOLID чаще всего нарушается **SRP (Single Responsibility Principle)** — Принцип единственной ответственности.

### Почему SRP нарушается чаще всего?

1.  **Нейтральный тон:** Это самый интуитивный принцип («один класс — одна задача»), но самый сложный для формального определения. Легко подумать, что «я всего лишь делаю валидацию и сохраняю в БД — это одна задача», а на деле это две разные.
2.  **«Болото» (God Class):** Классы, которые пытаются сделать всё: получить данные, обработать бизнес-логику, отправить email, сохранить в БД и сформировать ответ.
3.  **Лень и скорость разработки:** Вызвать метод `save()` и в нем же `sendEmail()` проще, чем выносить логику в отдельный сервис или слушатель событий.
4.  **Смешение уровней абстракции:** В одном методе мы видим и SQL-запрос, и бизнес-правило, и форматирование даты для email.

---

### Типовой пример нарушения SRP

**Ситуация:** Класс `UserService` отвечает за управление пользователями.

```php
<?php
declare(strict_types=1);

class UserService
{
    private UserRepository $repository;
    private MailerService $mailer;
    private LoggerInterface $logger;

    public function __construct(
        UserRepository $repository,
        MailerService $mailer,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    // ❌ НАРУШЕНИЕ SRP: Этот метод делает СЛИШКОМ много разных вещей.
    public function register(array $data): User
    {
        // 1. Валидация (отдельная ответственность)
        if (empty($data['email'])) {
            throw new ValidationException('Email is required');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format');
        }
        if (strlen($data['password'] ?? '') < 8) {
            throw new ValidationException('Password too short');
        }

        // 2. Проверка уникальности (задача репозитория/домена)
        if ($this->repository->findByEmail($data['email'])) {
            throw new DomainException('User already exists');
        }

        // 3. Создание и сохранение (основная задача)
        $user = new User(
            id: uniqid(),
            email: $data['email'],
            passwordHash: password_hash($data['password'], PASSWORD_BCRYPT),
            role: 'user',
            createdAt: new \DateTimeImmutable()
        );
        $this->repository->save($user);

        // 4. Отправка письма (техническая деталь, не бизнес-логика регистрации)
        $this->mailer->send(
            to: $data['email'],
            subject: 'Welcome!',
            body: 'Hi, thanks for signing up. Your ID is ' . $user->id
        );

        // 5. Логирование (перекрёзная обеспокоенность)
        $this->logger->info('User registered', ['email' => $data['email']]);

        return $user;
    }
}
```

**Проблемы такого кода:**
1.  **Тестирование:** Чтобы протестировать логику регистрации, нужно мокачить `MailerService` и `LoggerInterface`, хотя они не имеют отношения к правилам валидации.
2.  **Изменения:** Если мы решим отправлять письма через очередь (Queue) вместо синхронного вызова, мы меняем `UserService`, хотя бизнес-логика регистрации не изменилась.
3.  **Масштабируемость:** Если нужно добавить SMS-подтверждение, этот класс станет еще жирнее.
4.  **Смешение уровней:** Здесь и валидация входных данных, и бизнес-правила, и инфраструктура (mailer, logger).

---

### Рефакторинг: Разделение ответственностей

Мы выделим три главные ответственности:
1.  **Валидация домена** (правила бизнеса).
2.  **Создание/сохранение сущности** (реестр/репозиторий).
3.  **Реакция на событие** (отправка письма) — через Event Dispatcher или отдельный обработчик.

#### 1. Доменная сущность с поведением (Fat Domain Model)

```php
<?php
declare(strict_types=1);

class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        private readonly string $passwordHash,
        public readonly string $role,
        public readonly \DateTimeImmutable $createdAt
    ) {}

    // ✅ Ответственность: Проверка своих внутренних правил
    public static function validateEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserValidationException('Invalid email format');
        }
    }

    public static function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new UserValidationException('Password must be at least 8 characters');
        }
    }
}
```

#### 2. Сервис регистрации (Только бизнес-логика)

```php
<?php
declare(strict_types=1);

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    // ✅ Ответственность: Оркестрация бизнес-правил. Никакой инфраструктуры.
    public function execute(string $email, string $password): User
    {
        User::validateEmail($email);
        User::validatePassword($password);

        if ($this->repository->findByEmail($email) !== null) {
            throw new UserAlreadyExistsException($email);
        }

        $user = new User(
            id: $this->generateId(),
            email: $email,
            passwordHash: password_hash($password, PASSWORD_BCRYPT),
            role: 'user',
            createdAt: new \DateTimeImmutable()
        );

        $this->repository->save($user);
        
        return $user;
    }

    private function generateId(): string
    {
        return uniqid(); // В реальном проекте — UUID или snowflake ID
    }
}
```

#### 3. Слой приложения (Контроллер/Command Handler)

```php
<?php
declare(strict_types=1);

class RegisterUserCommandHandler
{
    public function __construct(
        private readonly RegisterUserUseCase $useCase,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    public function handle(RegisterUserCommand $command): User
    {
        // 1. Выполняем бизнес-логику
        $user = $this->useCase->execute(
            $command->email,
            $command->password
        );

        // 2. Публикуем событие (реагируют другие сервисы)
        $event = new UserRegisteredEvent($user);
        $this->eventDispatcher->dispatch($event);

        // 3. Логирование (перекрёчная обеспокоенность, лучше через middleware)
        return $user;
    }
}
```

#### 4. Слушатель событий (Отдельная ответственность)

```php
<?php
declare(strict_types=1);

class SendWelcomeEmailListener
{
    public function __construct(
        private readonly MailerService $mailer
    ) {}

    // ✅ Ответственность: Реакция на конкретное событие.
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->send(
            to: $event->user->email,
            subject: 'Welcome!',
            body: 'Hi, thanks for signing up. Your ID is ' . $event->user->id
        );
    }
}
```

---

### Что изменилось?

| Аспект | Было (Нарушение SRP) | Стало (Соблюдение SRP) |
| :--- | :--- | :--- |
| **Валидация** | В методе сервиса | В доменной модели `User` |
| **Бизнес-логика** | Смешана с инфраструктурой | Чистая бизнес-логика в `UseCase` |
| **Email** | Синхронный вызов в сервисе | Асинхронное событие, отдельный слушатель |
| **Логирование** | Явный вызов в коде | Middleware или Event Listener |
| **Тестирование** | Нужны моки mailer/logger | Тестируем `UseCase` с моком репозитория |
| **Изменения** | Меняем `UserService` для новых фич | Добавляем новые слушатели или сервисы |

### Итог

SRP — это не про «один метод», а про **«почему может измениться код»**.
- `RegisterUserUseCase` меняется, если меняются правила регистрации (мин. длина пароля, уникальность email).
- `SendWelcomeEmailListener` меняется, если меняется дизайн письма или провайдер email.
- `User` меняется, если меняется структура данных пользователя.

Разделяя код по этим причинам изменений, мы получаем систему, которая легко масштабируется и тестируется.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно. Более того, иногда **обязаны**.

SOLID — это не религия, а набор эвристики (эвристика = «практическое правило»), помогающее избежать загнивания кода. Как говорил Роберт Мартин: «SOLID — это принципы, а не правила». Они работают, когда мы имеем дело с **изменчивой бизнес-логикой** и **долговременной поддержкой**.

### Когда SOLID оправданно нарушают?

1.  **Прототипирование и MVP.** Когда нужно быстро проверить гипотезу, а не строить архитектуру.
2.  **Одностраничные скрипты / CLI-утилиты.** Код, который не переиспользуется, не расширяется и не поддерживается командой.
3.  **Высокопроизводительные узлы (Hot Path).** Где виртуальные методы, интерфейсы и инверсия зависимостей добавляют накладные расходы (хотя в PHP это редко является критичным, но в компилируемых языках — да).
4.  **Обработка внешних событий (Event Sourcing / CQRS).** Где «грязность» данных компенсируется иммутability (неизменяемостью) событий.
5.  **Временные хаки (Technical Debt).** Когда дедлайн горит, и вы сознательно нарушаете SOLID, помечая код `TODO: Refactor`.

---

### Пример оправданного нарушения: **Single Responsibility Principle (SRP)**

#### Ситуация: Быстрый скрипт для миграции данных

**Задача:** Нужно一次性 (one-time) перенести 10 000 пользователей из старой системы (legacy API) в новую. Это разовая задача, которая никогда не будет повторяться.

**Нарушение:** Один класс `LegacyMigrator` отвечает за:
1.  Парсинг JSON из legacy-API.
2.  Валидацию данных.
3.  Маппинг старых полей в новые.
4.  Запись в БД новой системы.
5.  Логирование прогресса.

Это классическое нарушение SRP (God Class), но оно **оправдано**.

#### Почему это оправдано?

1.  **Отсутствие переиспользования:** Никто не будет вызывять `Migrator::validate()` отдельно от `Migrator::save()`. Все методы используются только внутри `run()`.
2.  **Нет расширять:** Мы не будем добавлять новые типы пользователей или новые legacy-системы.
3.  **Скорость разработки:** Разделение на 5 классов (Parser, Validator, Mapper, Saver, Logger) + интерфейсы + DI-контейнер = 3–5 часов работы. Написание одного класса = 30 минут.
4.  **Жизненный цикл:** Код будет удален через 2 дня после запуска. Инвестиции в SOLID здесь убыточны.

#### Пример кода (осознанно «грязный»):

```php
<?php
// migrator.php — разовый скрипт

class LegacyMigrator
{
    public function run(): void
    {
        $users = $this->fetchFromLegacyApi(); // 1. Получение данных
        
        foreach ($users as $index => $user) {
            // 2. Валидация
            if (!isset($user['email']) || empty($user['email'])) {
                $this->logError("User #{$index} has no email, skipping.");
                continue;
            }

            // 3. Маппинг (legacy field names → new field names)
            $newUser = [
                'email' => $user['email'],
                'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                'created_at' => date('Y-m-d H:i:s', $user['ts']),
                'is_active' => $user['active'] === 1,
            ];

            // 4. Сохранение
            try {
                $this->saveToNewDb($newUser);
                $this->logProgress("User #{$index} migrated.");
            } catch (\Exception $e) {
                $this->logError("Failed to save user #{$index}: " . $e->getMessage());
            }
        }
    }

    private function fetchFromLegacyApi(): array
    {
        // ... curl request, json_decode ...
        return $data;
    }

    private function saveToNewDb(array $data): void
    {
        // ... PDO insert ...
    }

    private function logProgress(string $msg): void
    {
        fwrite(STDOUT, "[PROGRESS] {$msg}\n");
    }

    private function logError(string $msg): void
    {
        fwrite(STDERR, "[ERROR] {$msg}\n");
    }
}

// Запуск
$job = new LegacyMigrator();
$job->run();
```

**Если бы мы применили SOLID здесь:**
- Мы бы создали `LegacyApiClient`, `UserValidator`, `UserMapper`, `UserRepository`, `ProgressLogger`.
- Мы бы определили интерфейсы для каждого.
- Мы бы настроили DI-контейнер.
- Мы бы написали unit-тесты для каждого компонента.
- **Итог:** Мы бы потратили 5 часов, чтобы написать код, который будет жить 2 дня. Это **неэффективная трата ресурсов**.

---

### Второй пример: **Open/Closed Principle (OCP)**

#### Ситуация: Отчет для руководства

**Задача:** Сформировать PDF-отчет с финансовой сводкой за месяц. Отчет меняется каждый месяц, но только в части **форматирования** (шрифты, цвета, порядок строк), а не в бизнес-логике.

**Нарушение OCP:** Вместо создания интерфейса `ReportRenderer` и реализации `PdfRenderer`, `ExcelRenderer`, мы пишем один метод `generateReport()` с большим `switch` или `if-else` в зависимости от типа отчет.

```php
class ReportService
{
    public function generate(string $type, array $data): string
    {
        if ($type === 'pdf') {
            return $this->renderPdf($data);
        } elseif ($type === 'csv') {
            return $this->renderCsv($data);
        } else {
            throw new \Exception("Unknown report type");
        }
    }
}
```

**Почему это оправдано?**
- Если типы отчетов **редко** добавляются (раз в полгода), и их **мало** (2–3 шт.), создание абстракции — **преждевременная оптимизация**.
- Правило «Rule of Three»: абстрагируй только после третьего случая. Пока их два — `if-else` читается проще, чем интерфейс + реализации + DI.

---

### Как осознанно нарушать SOLID?

1.  **Документируйте причину.**
    ```php
    /**
     * @deprecated This class violates SRP intentionally to speed up one-time migration.
     * @todo Remove after 2024-01-15
     */
    class LegacyMigrator { ... }
    ```
2.  **Изолируйте нарушение.**
    Не смешивайте «грязный» код с чистой бизнес-логикой. Поместите его в отдельный каталог `scripts/`, `cli/`, `tools/`.
3.  **Не масштабируйте нарушение.**
    Если вы начали нарушать SOLID ради скорости, не стройте на этом фундаменте всю систему. Создайте «стеной» между разовой задачей и основным приложением.
4.  **Возвращайтесь к SOLID, когда код начинает жить.**
    Если «разовый скрипт» начали использовать в production каждый день — это не разовая задача. Рефакторьте его по SOLID.

---

### Итог

| Принцип | Когда нарушать? | Почему? |
| :--- | :--- | :--- |
| **SRP** | Разовые скрипты, CLI-утилиты, прототипы | Нет переиспользования, мало времени на разработку |
| **OCP** | Мало вариантов (< 3), редкие изменения | Абстракция стоит дороже, чем `if-else` |
| **ISP** | Клиенты используют все методы | Разделение на много интерфейсов усложняет DI |
| **DIP** | Внутренняя реализация, мало слоев | Инверсия зависимостей требует overhead |
| **LSP** | Наследование только для «is-a» | Если поведение не полностью совместимо — не наследуйте |

**Главное правило:** SOLID — это инвестиция в **долговечность**. Если кода не будет жить долго или он не будет меняться — не инвестируйте. Но если он живет годами и меняется еженедельно — нарушать SOLID опасно.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Да, вы абсолютно правы. Хотя **OCP (Open/Closed Principle)** чаще всего ассоциируется с наследованием и полиморфизмом, это **не единственный** и даже не всегда лучший способ.

Классическая формулировка: *"Классы должны быть открыты для расширения, но закрыты для модификации"*.
На практике это означает: **когда появляется новый вариант поведения, мы не должны изменять существующий код, а должны добавлять новый**.

Вот 5 способов реализовать OCP, кроме наследования:

### 1. Стратегический паттерн (Strategy) + Зависимое Введение (DI)
Вместо того чтобы менять метод через `if-else` или наследование, вы инжектите объект-стратегию.

**Суть:** Основной класс знает только об интерфейсе (или контракте), а конкретную реализацию выбирает вне его.

```php
<?php
declare(strict_types=1);

// Контракт (открыт для расширения новыми реализациями)
interface TaxStrategy
{
    public function calculate(float $amount): float;
}

// Существующая реализация (не изменяется)
class StandardTaxStrategy implements TaxStrategy
{
    public function calculate(float $amount): float
    {
        return $amount * 0.20;
    }
}

// Новая реализация (добавляется, старый код не трогается)
class VipTaxStrategy implements TaxStrategy
{
    public function calculate(float $amount): float
    {
        return $amount * 0.10;
    }
}

// Клиентский код (закрыт для модификации)
class Invoice
{
    public function __construct(
        private readonly TaxStrategy $taxStrategy // DI: мы не знаем, какая стратегия
    ) {}

    public function getTax(): float
    {
        return $this->taxStrategy->calculate($this->amount);
    }
}

// Использование
$invoice = new Invoice(new VipTaxStrategy()); // Расширили систему, не меняя Invoice
```

**Почему это OCP:**
- `Invoice` не знает, какой именно налог используется.
- Чтобы добавить новый тип налога, мы создаем новый класс `TaxStrategy`, но **не изменяем** `Invoice`.

---

### 2. Композиция и Декораторы (Composition over Inheritance)
Вместо того чтобы наследовать от `BaseProcessor` и переопределять методы, мы обворачиваем объект в декораторы, которые добавляют поведение.

**Суть:** Поведение собирается из мелких, независимых блоков.

```php
<?php
declare(strict_types=1);

interface DataProcessor
{
    public function process(array $data): array;
}

// Базовый обработчик (не изменяется)
class BasicProcessor implements DataProcessor
{
    public function process(array $data): array
    {
        return $data; // Просто прокидывает
    }
}

// Декоратор: добавляет логику валидации
class ValidatingProcessor implements DataProcessor
{
    public function __construct(
        private readonly DataProcessor $next
    ) {}

    public function process(array $data): array
    {
        // Добавляем поведение ДО вызова следующего
        if (empty($data)) {
            throw new \InvalidArgumentException('Data cannot be empty');
        }
        return $this->next->process($data);
    }
}

// Декоратор: добавляет логику логирования
class LoggingProcessor implements DataProcessor
{
    public function __construct(
        private readonly DataProcessor $next
    ) {}

    public function process(array $data): array
    {
        $result = $this->next->process($data);
        // Добавляем поведение ПОСЛЕ вызова
        echo "Processed " . count($data) . " items\n";
        return $result;
    }
}

// Сборка (расширение без модификации)
$processor = new BasicProcessor();
$processor = new ValidatingProcessor($processor); // Добавили валидацию
$processor = new LoggingProcessor($processor);    // Добавили логирование

$processor->process(['a', 'b']);
```

**Почему это OCP:**
- Мы не наследуем от `BasicProcessor`.
- Мы **составляем** поведение из независимых декораторов.
- Чтобы добавить новое поведение (например, кэширование), мы создаем новый декоратор `CachingProcessor`, не меняя старые.

---

### 3. Фабрики и Заводы (Factory Pattern)
Вместо того чтобы выбирать тип объекта через `if-else` внутри метода, мы делегируем создание объекту-фабрике, который можно расширять.

**Ситуация:** Нужно создать разные типы уведомлений (Email, SMS, Push).

```php
<?php
declare(strict_types=1);

interface Notification
{
    public function send(string $message): void;
}

class EmailNotification implements Notification { /* ... */ }
class SmsNotification implements Notification { /* ... */ }

// Фабрика (может быть расширена)
interface NotificationFactory
{
    public function create(string $type): Notification;
}

class StandardNotificationFactory implements NotificationFactory
{
    public function create(string $type): Notification
    {
        return match ($type) {
            'email' => new EmailNotification(),
            'sms' => new SmsNotification(),
            default => throw new \InvalidArgumentException("Unknown type: $type")
        };
    }
}

// Клиентский код (закрыт для модификации)
class User
{
    public function __construct(
        private readonly NotificationFactory $factory
    ) {}

    public function notify(string $type, string $message): void
    {
        $this->factory->create($type)->send($message);
    }
}

// Расширение: Добавляем Push Notification
class PushNotification implements Notification { /* ... */ }

// Новый завод (расширяет стандартный, но не меняет его)
class ExtendedNotificationFactory implements NotificationFactory
{
    public function create(string $type): Notification
    {
        if ($type === 'push') {
            return new PushNotification();
        }
        // Делегируем остальное стандартному заводу
        return (new StandardNotificationFactory())->create($type);
    }
}
```

---

### 4. Плагины и Реситры (Registry / Plugin Architecture)
Это самый гибкий способ OCP в PHP. Мы используем **реестр**, куда можно регистрировать новые обработчики во время выполнения.

**Ситуация:** Система обработки команд, где новые типы команд могут быть добавлены плагином.

```php
<?php
declare(strict_types=1);

class CommandHandlerRegistry
{
    private array $handlers = [];

    // Открыто для расширения: любой может зарегистрировать обработчик
    public function register(string $command, callable $handler): void
    {
        $this->handlers[$command] = $handler;
    }

    // Закрыто для модификации: логика поиска и вызова фиксирована
    public function handle(string $command, array $data): void
    {
        if (!isset($this->handlers[$command])) {
            throw new \RuntimeException("No handler for command: $command");
        }
        ($this->handlers[$command])($data);
    }
}

// Использование
$registry = new CommandHandlerRegistry();

// Регистрируем существующие команды
$registry->register('user.create', function (array $data) {
    // Логика создания пользователя
});

// Регистрируем новую команду (расширение без модификации ядра)
$registry->register('user.delete', function (array $data) {
    // Логика удаления
});

$registry->handle('user.create', ['email' => 'test@example.com']);
```

**Почему это OCP:**
- Ядро системы (`Registry`) не знает о конкретных командах.
- Новые команды добавляются через `register()`, а не через модификацию кода ядра.
- Это основа Symfony Event Dispatcher, Laravel Service Providers и многих других фреймворков.

---

### 5. Конфигурирование через Данные (Configuration as Data)
Иногда "новое поведение" — это просто новые данные, а не новый код.

**Ситуация:** Правила валидации форм.

```php
<?php
declare(strict_types=1);

class FormValidator
{
    public function __construct(
        private readonly array $rules // Правила передаются снаружи
    ) {}

    public function validate(array $data): void
    {
        foreach ($this->rules as $field => $rule) {
            // Логика валидации фиксирована
            if ($rule === 'required' && empty($data[$field] ?? null)) {
                throw new \Exception("Field $field is required");
            }
        }
    }
}

// Расширение: Добавляем новое поле и новое правило
$rules = [
    'email' => 'required',
    'age' => 'min:18', // Новое правило, добавлено в конфиг
    'password' => 'min:8'
];

$validator = new FormValidator($rules);
```

---

### Сравнение подходов

| Способ | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Наследование** | Когда есть строгая иерархия "is-a" | Инкапсуляция, полиморфизм | Жесткость, трудно расширять в рантайме |
| **Стратегия + DI** | Когда нужно выбрать один из многих вариантов | Гибкость, тестируемость | Больше классов |
| **Композиция/Декораторы** | Когда нужно добавлять независимые шаги | Модульность, порядок важен | Сложно отследить "цепочку" |
| **Фабрика** | Когда создание объектов сложное | Изоляция сложной логики создания | Может стать "магической" |
| **Реестр/Плагины** | Когда система должна быть расширяемая внешним кодом | Максимальная гибкость, поддержка плагинов | Сложнее отладка, динамический характер |

### Вывод

**OCP не требует наследования.** Он требует **абстракции** и **инверсии контроля**.

- **Наследование** — это один способ абстрагировать поведение.
- **Стратегии, декораторы, реестры** — другие способы.

В современных PHP-приложениях (Symfony, Laravel) **стратегии + DI** и **реестры событий/команд** используются чаще, чем чистое наследование, потому что они легче тестируются, лучше масштабируются и не нарушают SRP.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, **SRP (Single Responsibility Principle)** применяется не только к классам, но и к другим уровням кода.

Формулировка Р. Мартина:
> *"У класса должна быть всего одна причина для изменения."*

Но эта мысль распространяется на всю систему. На практике SRP — это принцип **разделения по причинам изменений** (separation of concerns), который применим к:

---

### 1. К методам и функциям
Даже внутри одного класса метод должен делать **только одну вещь**.

**Нарушение:**
```php
public function processOrder(Order $order): void
{
    // 1. Валидация
    if ($order->items === []) {
        throw new \Exception('No items');
    }
    
    // 2. Расчет скидки
    $discount = $this->calculateDiscount($order);
    
    // 3. Сохранение в БД
    $this->repo->save($order);
    
    // 4. Отправка email
    $this->mailer->send($order->email, 'Order confirmed');
    
    // 5. Логирование
    $this->logger->info('Order processed', ['id' => $order->id]);
}
```

**Проблема:** Если изменится формат email — мы меняем этот метод. Если изменится логика скидки — тоже. Если изменится схема БД — тоже. У метода **5 причин для изменения**.

**Рефакторинг:**
```php
public function processOrder(Order $order): void
{
    $this->validator->validate($order);       // Причина 1: правила валидации
    $this->discountService->apply($order);    // Причина 2: правила скидок
    $this->repo->save($order);                // Причина 3: схема данных/БД
    $this->eventDispatcher->dispatch(         // Причина 4: реакция на событие
        new OrderProcessedEvent($order)
    );
    $this->logger->info('Order processed');   // Причина 5: формат логирования
}
```
Теперь каждый вызов отвечает за свою причину. Если изменится email — меняется `SendEmailListener`, а не `processOrder`.

---

### 2. К файлам и модулям
Файл должен содержать **только одну группу связанных сущностей**.

**Нарушение:**
```php
// app/Services/UserService.php
class UserService { /* ... */ }

// В том же файле
class EmailTemplateRenderer { /* ... */ }

// И даже
class DebugHelper { /* ... */ }
```

**Проблема:** Если `EmailTemplateRenderer` меняется — файл меняется, даже если вы не трогали `UserService`. Это нарушает SRP на уровне файла.

**Правило:** Один файл = один класс (PSR-1). Если нужно больше — создайте отдельные файлы.

---

### 3. К таблицам БД
Да, SRP применим и к нормализации БД.

**Нарушение:**
```sql
CREATE TABLE users (
    id INT,
    name VARCHAR,
    email VARCHAR,
    phone VARCHAR,
    address_line1 VARCHAR,
    address_line2 VARCHAR,
    city VARCHAR,
    country VARCHAR,
    postal_code VARCHAR
);
```

**Проблема:** У таблицы есть **две причины для изменения**:
1. Изменения в данных пользователя (имя, email).
2. Изменения в структуре адреса (новые поля, разные форматы).

**Рефакторинг:**
```sql
CREATE TABLE users (
    id INT,
    name VARCHAR,
    email VARCHAR
);

CREATE TABLE addresses (
    id INT,
    user_id INT,
    line1 VARCHAR,
    line2 VARCHAR,
    city VARCHAR,
    country VARCHAR,
    postal_code VARCHAR
);
```

Теперь изменение формата адреса не требует изменения таблицы `users`.

---

### 4. К конфигурации
Конфигурационные файлы должны быть разделены по областям ответственности.

**Нарушение:**
```yaml
# config/app.yaml
debug: true
db_host: localhost
db_pass: secret
mail_driver: smtp
mail_host: mail.example.com
mail_port: 587
feature_flag_new_checkout: true
feature_flag_dark_mode: false
```

**Проблема:** Если меняется почтовый сервер — меняем этот файл. Если меняется фича-флаг — тоже.

**Рефакторинг:**
```yaml
# config/database.yaml
db_host: localhost
db_pass: secret

# config/mail.yaml
mail_driver: smtp
mail_host: mail.example.com
mail_port: 587

# config/features.yaml
feature_flag_new_checkout: true
feature_flag_dark_mode: false
```

---

### 5. К HTTP-эндпоинтам и контроллерам
Каждый endpoint должен выполнять **только одну операцию**.

**Нарушение:**
```php
// POST /api/users
public function save(Request $request): JsonResponse
{
    if ($request->query('id')) {
        // Обновление
        $user = $this->findUser($request->query('id'));
        $user->update($request->all());
    } else {
        // Создание
        $user = new User($request->all());
        $user->save();
    }
    
    // И еще:
    if ($request->query('sendWelcomeEmail')) {
        $this->mailer->sendWelcome($user);
    }
    
    return new JsonResponse(['id' => $user->id]);
}
```

**Проблема:** Один endpoint делает три вещи: создание, обновление, отправка email.

**Рефакторинг (REST):**
```php
// POST /api/users          — только создание
// PUT /api/users/{id}     — только обновление
// POST /api/users/{id}/welcome — отправка email (отдельная операция)
```

---

### 6. К переменным и параметрам
Даже переменные должны иметь одну ответственность.

**Нарушение:**
```php
$total = 0;
$count = 0;
$lastError = null;

for ($item in $items) {
    $total += $item->price;
    $count++;
    
    if ($item->isBlocked()) {
        $lastError = 'Blocked';
    }
}
```

**Проблема:** Цикл делает три вещи: суммирует, считает, ищет ошибки.

**Рефакторинг:**
```php
$items = array_filter($items, fn($i) => !$i->isBlocked());
if (empty($items)) {
    throw new \Exception('All items are blocked');
}

$total = array_sum(array_column($items, 'price'));
$count = count($items);
```

---

### Как определять, нарушен ли SRP?

Задайте себе вопрос:

> **"Если завтра изменится X, нужно ли мне менять этот класс/метод/файл?"**

Если **X** — это:
- изменение бизнес-логики,
- изменение формата данных,
- изменение стороннего сервиса,
- изменение способа логирования,

...и вам нужно менять **один и тот же** кусок кода для каждого из этих изменений — **SRP нарушен**.

---

### Итог

| Уровень | Пример нарушения SRP | Как исправить |
| :--- | :--- | :--- |
| **Класс** | `UserService` делает валидацию, БД, email | Вынести в отдельные классы |
| **Метод** | `processOrder` делает 5 разных вещей | Разбить на вызовы к другим сервисам |
| **Файл** | В одном файле 3 несвязанных класса | Один файл = один класс |
| **БД** | Одна таблица на пользователя и адрес | Нормализация: 2 таблицы |
| **Конфиг** | Один YAML на всё | Разделить по областям |
| **API** | Один endpoint на создание/обновление/email | Разделить на REST-эндпоинты |

**SRP — это не "один метод", а "одна причина для изменения".** Это принцип, который нужно применять на всех уровнях: от переменных до микросервисов.

> ⏱ 14.3s · prompt 7605 tok · output 2164 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер внедрения зависимостей (DI Container) в Symfony — это не просто «фабрика объектов», а сложная система оптимизации, которая работает в два этапа: **компиляция** (build time) и **исполнение** (runtime).

Разберём детально каждый этап.

---

### 1. Что такое DI Container в Symfony?

Это объект, который знает:
1. Какие сервисы (классы) существуют.
2. Какие зависимости у каждого сервиса (конструкторные аргументы).
3. Как создать эти зависимости (авто-инъекция, явные настройки, фабрики).
4. Какие сервисы нужно кэшировать, а какие создавать заново (shared vs. prototype).

Контейнер инкапсулирует сложность создания объектов, позволяя бизнес-коду просто запрашивать зависимости через аннотации `#[Autoconfigure]` или конфигурацию.

---

### 2. Этап 1: Компиляция контейнера (Build Time)

Это происходит при запуске команды `php bin/console cache:clear` или при первом запросе в режиме разработки. Результат — кэшированный PHP-файл (обычно `var/cache/dev/App_KernelDevDebugContainer.php`).

#### Шаги компиляции:

**Шаг 1: Сборка конфигурации**
Symfony сканирует:
- `config/services.yaml` (и другие YAML/PHP/JSON-файлы).
- Аннотации/атрибуты в коде (`#[Service]`, `#[Autowired]`, `#[Target]`).
- Настройки бандлов (например, Doctrine, Twig).
- Переменные окружения.

Результат — «карта» всех потенциальных сервисов и их параметров.

**Шаг 2: Разрешение зависимостей (Dependency Resolution)**
Для каждого сервиса Symfony пытается понять, что передать в его конструктор:
- Если тип аргумента — интерфейс или абстрактный класс, Symfony ищет:
  - Явную привязку в `services.yaml` (`!service`).
  - Сервис с `#[Target]` или `#[Autoconfigure]`.
  - Сервис, реализующий этот интерфейс (Autowiring).
- Если тип — конкретный класс, Symfony автоматически создаёт инстанс этого класса (Autowiring).

**Шаг 3: Оптимизация (Inlining и Trimming)**
Это магия Symfony. Контейнер не хранит все объекты как `new Foo()`, а генерирует оптимизированный код.
- **Inlining:** Если сервис A зависит от сервиса B, а B зависит от C, и B используется только A, Symfony может «встроить» создание B внутрь создания A.
- **Trimming (Obfuscation):** Приватные переменные и методы, которые не нужны вне контейнера, могут быть переименованы (например, `$this->client` → `$this->d92a8f...`). Это позволяет GC освобождать память и усложняет рефлексивный доступ.
- **Proxy-классы:** Для ленивой инициализации (lazy services) генерируются proxy-классы, которые создают реальный объект только при первом вызове метода.

**Шаг 4: Генерация PHP-кода**
Symfony генерирует файл `App_KernelDevDebugContainer.php`. Этот файл содержит:
- Статические методы для каждого сервиса (например, `getApp_Service_UserServiceService()`).
- Логическу создания зависимостей.
- Обработчики ошибок.
- Кэш уже созданных сервисов (статические переменные).

---

### 3. Этап 2: Исполнение (Runtime) — Что происходит при запросе

Когда ваш код запрашивает сервис впервые (например, в контроллере):

```php
class UserController extends AbstractController
{
    public function list(UserService $userService): Response
    {
        // ...
    }
}
```

Symfony-компилятор уже знает, что `UserController` зависит от `UserService`.

#### Последовательность событий:

1. **Запрос сервиса**
   Контроллер вызывает `get(UserService::class)` у контейнера (через `AbstractController`).

2. **Поиск в кэше (Shared Services)**
   Контейнер проверяет: «Создавал ли я уже `UserService`?»
   - **Да:** Возвращает тот же самый инстанс (singleton).
   - **Нет:** Переходит к созданию.

3. **Создание зависимости (Rekursiv)**
   Если `UserService` зависит от `MailService`, контейнер:
   - Проверяет кэш `MailService`.
   - Если нет — создаёт `MailService` (рекурсивно, с его зависимостями).
   - Возвращает инстанс `MailService`.

4. **Инстанциация**
   Контейнер вызывает `new UserService($mailService, $otherDependency)`.

5. **Регистрация в кэше**
   Инстанс сохраняется в статической переменной контейнера.

6. **Возврат объекту**
   Объект передается в контроллер.

---

### 4. Как работает Autowiring?

**Autowiring** — это автоматическое определение зависимостей по **типам** (type hints) в конструкторе.

#### Как это работает «под капотом»?

1. **Сканирование кода**
   Во время компиляции Symfony использует рефлексию (Reflection) или PSR-4 автозагрузчик, чтобы:
   - Найти все публичные классы.
   - Прочитать сигнатуры конструкторов.
   - Определить типы аргументов.

2. **Матчинг типов**
   Для каждого аргумента в конструкторе Symfony ищет сервис, который:
   - Соотвествует типу (класс или интерфейс).
   - Имеет приоритет (если несколько сервисов реализуют один интерфейс).
   - Отмечен как `#[Autoconfigure]` (в Symfony 6+ по умолчанию все публичные классы автоконфигурируются, если не указано иное).

3. **Генерация привязки**
   В сгенерированном контейнере появляется код вроде:

   ```php
   private static $services = [];

   public static function get_app_Service_UserServiceService(): ?UserService
   {
       if (self::$services['app.Service.UserService'] !== null) {
           return self::$services['app.Service.UserService'];
       }

       $a = new \App\Service\MailerService(); // Зависимость создана

       return self::$services['app.Service.UserService'] = new \App\Service\UserService($a);
   }
   ```

#### Когда Autowiring не работает?
- Если аргумент — примитивный тип (`string`, `int`) и не задан явно в `services.yaml`.
- Если несколько сервисов реализуют один интерфейс, и не указан приоритет.
- Если класс приватный (не публичный).
- Если конструктор не публичный.

---

### 5. Специфические механизмы Symfony

#### A. Lazy Services (Ленивая инициализация)
Если сервис тяжелый (например, `HttpClient`), Symfony не создает его сразу. Вместо этого он создает **Proxy**.

```php
// Генерированный proxy-код
class LazyProxy_MailerService extends MailerService implements \Psr\LazyProxy\LazyGhostInterface
{
    private ?MailerService $realService = null;

    public function send(string $to): void
    {
        $this->initRealService(); // Создание реального объекта при первом вызове метода
        $this->realService->send($to);
    }
}
```
**Плюс:** Не создаются объекты, которые не используются в текущем запросе.

#### B. Private vs Public Services
- **Private (по умолчанию):** Доступны только внутри контейнера. После создания и использования их можно удалять из памяти.
- **Public:** Доступны из кода через `get()` или автоинъекцию. Хранятся в памяти до конца запроса.

#### C. Tags (Метки)
Symfony использует теги для группировки сервисов.
Пример: `kernel.event_listener`.
Контейнер не создает сервисы-слушатели напрямую. Вместо этого он создает **EventDispatcher**, которому передает список всех сервисов, имеющих тег `event_listener`.

```php
# services.yaml
App\EventListener\RegisterListener:
    tags:
        - { name: kernel.event_listener, event: app.user.registered }
```

В сгенерированном контейнере это превращается в массив, который передается в конструктор `EventDispatcher`.

---

### 6. Итог: Жизненный цикл сервиса

```mermaid
graph TD
    A[Запрос сервиса] --> B{В кэше?}
    B -->|Да| C[Вернуть инстанс]
    B -->|Нет| D[Рекурсивное создание зависимостей]
    D --> E[Инстанциация объекта]
    E --> F[Сохранение в кэш]
    F --> C
    C --> G[Использование в бизнес-коде]
    G --> H[Конец запроса]
    H --> I[Освобождение памяти для Private сервисов]
```

### Ключевые выводы

1. **Компиляция — это статический анализ.** Symfony не знает о вашей бизнес-логике, он знает о типах и связях.
2. **Autowiring — это маппинг типов на сервисы.** Он работает только если типы ясны и однозначны.
3. **Кэш — это статические переменные.** Сервисы создаются один раз (если они shared) и переиспользуются.
4. **Optimization — это inline и proxy.** Symfony делает всё, чтобы минимизировать overhead создания объектов.

Понимание этого позволяет:
- Отлаживать проблемы с DI (если Symfony не смог найти сервис).
- Оптимизировать память (делать сервисы `lazy`, `private`).
- Писать код, который легко расширяется (через теги и интерфейсы).

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это «красный флаг» в архитектуре. В идеале их нужно **устранивать**, а не «разруливать» обходными путями. Если два сервиса напрямую зависят друг от друга, это почти всегда означает, что **SRP нарушен** или ответственности распределены неверно.

### Почему это плохо?

```php
class ServiceA {
    public function __construct(private ServiceB $b) {}
}

class ServiceB {
    public function __construct(private ServiceA $a) {}
}
```

Symfony **не может** создать такой объект, потому что:
1. Чтобы создать `A`, нужен `B`.
2. Чтобы создать `B`, нужен `A`.
3. Бесконечный рекурсивный цикл.

Symfony 6+ выдаст четкую ошибку: `Circular reference detected`.

---

## ✅ Правильные способы решения (от лучшего к худшему)

### 1. Извлечение общей логики (Best Practice)

Часто два сервиса зовут друг друга, потому что у них есть **общая бизнес-логика**, которая не принадлежит ни одному из них.

**Решение:** Создать третий сервис, который содержит эту логику, и встроить его в оба.

#### Пример:
`OrderService` и `InventoryService` зовут друг друга для проверки наличия товара.

**Было (цикл):**
```php
// OrderService
public function createOrder() {
    $inventory = $this->inventoryService->checkStock(); // Зависимость
    // ...
}

// InventoryService
public function checkStock() {
    $orders = $this->orderService->getPendingOrders(); // Зависимость
    // ...
}
```

**Стало (извлечение):**
```php
// 1. Новый сервис
class StockCalculator {
    public function calculate(string $sku, int $pendingOrders): int {
        // Логика расчета наличия
    }
}

// 2. OrderService
class OrderService {
    public function __construct(private StockCalculator $calculator) {}
    
    public function createOrder() {
        $stock = $this->calculator->calculate($sku, $pendingCount);
        // ...
    }
}

// 3. InventoryService
class InventoryService {
    public function __construct(private StockCalculator $calculator) {}
    
    public function checkStock() {
        $stock = $this->calculator->calculate($sku, $pendingCount);
        // ...
    }
}
```
✅ **Цикл устранен.** Оба сервиса зависят от одного, а не друг от друга.

---

### 2. Использование событий (Event Sourcing / Observer)

Если `ServiceA` должен просто **уведомить** `ServiceB` о каком-то событии, а не вызывать его синхронно — используйте **Event Dispatcher**.

#### Пример:
`PaymentService`成功后 должен обновить `OrderStatus`.

**Было (цикл):**
`PaymentService` → `OrderService` → (может) → `PaymentService` (для отмены при ошибке).

**Стало (события):**
```php
// PaymentService
class PaymentService {
    public function __construct(private EventDispatcherInterface $dispatcher) {}
    
    public function processPayment(): void {
        // Логика оплаты
        $this->dispatcher->dispatch(new PaymentSuccessfulEvent($orderId));
    }
}

// OrderService (слушатель)
class OrderListener {
    public function onPaymentSuccess(PaymentSuccessfulEvent $event): void {
        $this->orderService->markAsPaid($event->orderId);
    }
}
```
✅ **Цикл устранен.** Связь стала асинхронной/косвенной.

---

### 3. Инверсия контроля через интерфейс

Если один сервис должен вызывать метод другого, но вы не хотите жёсткой зависимости, введите **интерфейс**.

#### Пример:
`ServiceA` должен вызывать метод `doSomething()` у `ServiceB`.

```php
interface DoSomethingInterface {
    public function doSomething(): void;
}

class ServiceB implements DoSomethingInterface {
    public function doSomething(): void { /* ... */ }
}

class ServiceA {
    // Зависит от интерфейса, а не от конкретного класса ServiceB
    public function __construct(private DoSomethingInterface $something) {}
    
    public function run(): void {
        $this->something->doSomething();
    }
}
```

**Но!** Если `ServiceB` всё ещё зависит от `ServiceA` — цикл не устранен. Этот способ работает только в сочетании с другими (например, если `ServiceB` не зовёт `ServiceA` напрямую, а слушает событие).

---

### 4. Lazy Services (Ленивая инициализация) — «Лечащее» решение

Если цикл возник из-за того, что `ServiceB` нужен `ServiceA` только **в одном методе**, а не в конструкторе — можно использовать **Lazy Proxy**.

> ⚠️ **Важно:** Это не устраняет архитектурную проблему, а только маскирует её. Используйте только как временное решение.

#### Как это работает:
Symfony создаёт **Proxy** (заглушку) для `ServiceB`. Реальный объект создаётся только тогда, когда будет вызван первый метод этого proxy.

```php
// services.yaml
App\Service\ServiceA:
    arguments:
        $serviceB: '@!service_b' # !service_b — lazy proxy

// ServiceA.php
class ServiceA {
    public function __construct(private ServiceB $serviceB) {} // Это Proxy!
    
    public function run(): void {
        $this->serviceB->doSomething(); // Здесь создаётся реальный ServiceB
    }
}
```

**Почему это не ломает цикл?**
1. `ServiceA` создаётся с **Proxy** `ServiceB`.
2. `ServiceB` создаётся с **Proxy** `ServiceA`.
3. Proxies не содержат реальных зависимостей, поэтому цикл **разрывается на этапе создания**.
4. Реальные объекты создаются только при первом вызове методов.

**Ограничения:**
- Работает только для **публичных методов**.
- Не работает для свойств, конструкторов, magic methods (`__toString` и т.д.).
- Усложняет отладку.

---

### 5. Ручная сборка в фабрике (Service Factory)

Если ничего не помогло, создайте **Factory**, который вручную собирает объекты в правильном порядке.

```php
class ServiceFactory {
    public function createServiceA(): ServiceA {
        $serviceB = new ServiceB();
        $serviceA = new ServiceA($serviceB);
        // Теперь можно "доинжектить" ServiceA в ServiceB, если у него есть setter
        $serviceB->setServiceA($serviceA);
        return $serviceA;
    }
}
```

> ⚠️ **Это антипаттерн.** Вы теряете преимущества DI (тестируемость, ленивая инициализация). Используйте только в крайнем случае.

---

## ❌ Антипаттерны (чего НЕ делать)

1. **Сеттеры для зависимостей**
   ```php
   class ServiceA {
       private ?ServiceB $b = null;
       
       public function setServiceB(ServiceB $b): void {
           $this->b = $b; // Грязь! Зависимость не явна в конструкторе
       }
   }
   ```
   **Почему плохо:** Зависимость скрыта. Трудно тестировать. Нарушает принцип явности.

2. **Статические вызовы**
   ```php
   class ServiceB {
       public function doSomething(): void {
           ServiceA::getInstance()->callBack(); // Глобальное состояние
       }
   }
   ```
   **Почему плохо:** Глобальные состояния — зло. Невозможно мокачить в тестах.

3. **Взаимные вызовы методов (синхронные)**
   ```php
   // ServiceA::run() вызывает ServiceB::process()
   // ServiceB::process() вызывает ServiceA::validate()
   ```
   **Почему плохо:** Глубокая рекурсия, сложность отладки, высокая вероятность infinite loop.

---

## ✅ Чек-лист: Что делать при циклической зависимости

1. **Остановитесь.** Спросите себя: *"Почему эти два сервиса должны зависеть друг от друга?"*
2. **Ищите общую логику.** Извлеките её в третий сервис.
3. **Замени синхронный вызов на событие.** Если `A` просто уведомляет `B` — используйте Event Dispatcher.
4. **Проверьте интерфейсы.** Может быть, один сервис зависит от интерфейса, который реализует другой, и эту зависимость можно инвертировать.
5. **Если ничего не помогло — используйте Lazy Proxy.** Но **пометьте код `TODO: Refactor`**.

---

## Итог

| Способ | Когда использовать | Оценка |
| :--- | :--- | :--- |
| **Извлечение общей логики** | Всегда, если возможно | ⭐⭐⭐⭐⭐ (Best Practice) |
| **События (Event Dispatcher)** | Когда связь "уведомление", а не "вызов" | ⭐⭐⭐⭐ (Отлично) |
| **Lazy Proxy** | Временное решение, если связь через публичный метод | ⭐⭐ (Маскирует проблему) |
| **Сеттеры / Фабрики** | Крайний случай, legacy-код | ⭐ (Антипаттерн) |

**Главное правило:** Если вы видите циклическую зависимость — это не проблема DI, а **проблема архитектуры**. Чините архитектуру, а не контейнер.

> ⏱ 18.5s · prompt 16297 tok · output 2293 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Классическая задача **асинхронной обработки событий** (side-effects). В Symfony есть несколько путей решения, каждый со своими плюсами и минусами.

### 🎯 Цель
Ответ пользователю должен возвращаться мгновенно после создания записи в БД. Отправка письма и индексация — фоновые задачи.

---

## Вариант 1: Event Dispatcher + Messenger (Рекомендуемый)

Это **золотой стандарт** в Symfony 5+.

### Как работает:
1. Сервис регистрации вызывает `$eventDispatcher->dispatch(new UserRegisteredEvent($user))`.
2. `EventDispatcher` передает событие `Messenger`, который кладет команду в **транспорт** (queue).
3. Контроллер возвращает ответ. **Готово.**
4. **Worker** (отдельный процесс) забирает сообщение из очереди и выполняет задачи.

### Архитектура:

```
[Web Request]
    │
    ▼
[RegisterUserCommandHandler]
    │ 1. Сохраняет пользователя в БД
    │ 2. Диспатчит UserRegisteredEvent
    ▼
[Symfony Messenger] ──► [Queue: Doctrine DB / Redis / RabbitMQ]
    │
    ▼
[HTTP Response: 201 Created]  ← Пользователь уже получил ответ!

[Worker Process (bin/messenger:consume)]
    │ Забирает событие из очереди
    ▼
[SendWelcomeEmailHandler] ──► [Mailer]
[IndexUserHandler]          ──► [Elasticsearch]
```

### Код:

**1. Событие:**
```php
class UserRegisteredEvent
{
    public function __construct(public readonly User $user) {}
}
```

**2. Диспатчинг в бизнес-логике:**
```php
class RegisterUserUseCase
{
    public function __construct(
        private UserRepository $repo,
        private EventDispatcherInterface $dispatcher
    ) {}

    public function execute(string $email, string $password): User
    {
        $user = new User(/* ... */);
        $this->repo->save($user);
        
        // Асинхронно! Messenger возьмет на себя доставку
        $this->dispatcher->dispatch(new UserRegisteredEvent($user));
        
        return $user;
    }
}
```

**3. Обработчики (Handlers):**
```php
class SendWelcomeEmailHandler
{
    #[AsMessageHandler]
    public function __invoke(UserRegisteredEvent $event): void
    {
        $this->mailer->send($event->user->email, 'Welcome!');
    }
}

class IndexUserHandler
{
    #[AsMessageHandler]
    public function __invoke(UserRegisteredEvent $event): void
    {
        $this->elasticSearch->indexUser($event->user);
    }
}
```

**4. Конфигурация Messenger:**
```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            default: '%env(MESSENGER_DSN)%' # doctrine://default или redis://localhost
        routing:
            'App\Domain\Event\UserRegisteredEvent': async
```

**5. Запуск воркера:**
```bash
php bin/console messenger:consume async -vv
```

### ✅ Trade-offs:
| Плюс | Минус |
| :--- | :--- |
| Полное разделение бизнес-логики и side-effects | Нужно запускать отдельный процесс (worker) |
| Отказоустойчивость (ретраи, dead letter queue) | Сложнее отлаживать (асинхронность) |
| Масштабируемость (можно добавить воркеров) | Задержка в обработке (секунды) |
| Чистая бизнес-логика (UseCase не знает о почте) | Инфраструктурная сложность |

---

## Вариант 2: Doctrine Events (OnFlush / PostPersist)

### Как работает:
Слушатели Doctrine срабатывают **во время** сохранения сущности.

```php
class UserEntityListener
{
    public function postPersist(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();
        if ($user instanceof User) {
            $this->mailer->send(...); // ❌ Блокирует запрос!
        }
    }
}
```

### ❌ Почему это плохая идея для вашей задачи:
1. **Синхронность:** Письмо отправляется **в том же HTTP-запросе**. Если Mailer упадет — транзакция может откатиться (или нет, в зависимости от реализации).
2. **Замедление ответа:** Пользователь ждет, пока письмо уйдет.
3. **Нарушение DDD:** Бизнес-логика (создание пользователя) смешана с инфраструктурной (отправка письма).

### ✅ Когда это допустимо:
Только для **критических** синхронных проверок (например, валидация уникальности перед flush), но **не** для side-effects.

---

## Вариант 3: Job Queue через Symfony Messenger (без событий)

Можно напрямую пушить команды в очередь, минуя события.

```php
class RegisterUserUseCase
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function execute(): void
    {
        $this->repo->save($user);
        
        $this->bus->dispatch(new SendWelcomeEmailCommand($user->id));
        $this->bus->dispatch(new IndexUserCommand($user->id));
    }
}
```

### ✅ Trade-offs:
| Плюс | Минус |
| :--- | :--- |
| Проще, чем события | Нарушение DDD: бизнес-логика знает о конкретных задачах |
| Нет необходимости в Event Dispatcher | Сложнее тестировать (много команд) |
| Явно видно, какие задачи выполняются | Невозможно добавить новую задачу без изменения UseCase (нарушение OCP) |

**Сравнение с Вариантом 1:**
- **События** — «кто-то зарегистрировался» (декомпозиция, расширимость).
- **Команды** — «отправь письмо, проиндексируй» (явность, но жесткая связь).

**Рекомендация:** Вариант 1 (события) лучше для DDD, Вариант 3 проще для простых CRUD-приложений.

---

## Вариант 4: Cron Job (Партиями)

### Как работает:
1. При регистрации пользователь попадает в очередь в БД (таблица `pending_emails`).
2. Cron-задача раз в минуту забирает из очереди 100 записей, отправляет письма и очищает.

### ✅ Trade-offs:
| Плюс | Минус |
| :--- | :--- |
| Максимальная простота | Задержка до 1 минуты |
| Нет необходимости в worker-процессах | Сложная логика обработки ошибок |
| Легко масштабируется (batch processing) | Не подходит для real-time задач |

---

## Вариант 5: JS Frontend + Polling (Антипаттерн)

### Как работает:
1. Backend возвращает ID пользователя.
2. Frontend каждые 2 секунды опрашивает `/api/user/{id}/status`.
3. Как только статус изменится — показываем success.

### ❌ Почему это плохо:
1. **Грузит сервер** (лишние запросы).
2. **Сложная логика на фронтенде**.
3. **Нет гарантии** доставки письма.

---

## 🏆 Итоговая рекомендация

| Сценарий | Лучший вариант |
| :--- | :--- |
| **Production-ready DDD** | **Вариант 1: Events + Messenger** |
| **Простой CRUD-сайт** | **Вариант 3: Commands + Messenger** |
| **Очень высокая нагрузка** | **Вариант 1 + Redis/RabbitMQ** |
| **Legacy-система** | **Вариант 4: Cron** |

### Почему Вариант 1 лучше всего:

1. **Отказоустойчивость:** Если Mailer упадет — сообщение останется в очереди и будет отправлено при ретрае.
2. **Масштабируемость:** Можно запустить 10 воркеров, если очередь растет.
3. **Чистота архитектуры:** `RegisterUserUseCase` не знает о почте и поиске. Он знает только о факте «пользователь зарегистрирован».
4. **Тестируемость:** Легко мокачить `EventDispatcher` в юнит-тестах.

### Чек-лист реализации:

- [ ] Определить событие `UserRegisteredEvent`.
- [ ] Настроить `messenger.yaml` с транспортом (Doctrine/Redis).
- [ ] Создать 2 отдельных `Handler` (email и search).
- [ ] Запустить воркер через Supervisor/Systemd.
- [ ] Настроить мониторинг очереди (метрики: размер, время обработки).
- [ ] Настроить логирование ошибок в воркере.

> ⏱ 15.8s · prompt 20921 tok · output 2080 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony состоит из двух независимых, но связанных процессов: **аутентификации** (кто ты?) и **авторизации** (что тебе разрешено делать?).

В современном Symfony (5/6/7) архитектура выглядит так:

1. **Firewall** — определяет, как происходит аутентификация (форма, JWT, API Key, LDAP).
2. **Authenticator** — выполняет вход/выход.
3. **Security Token** — хранит состояние сессии (пользователь, его роли).
4. **Authorization** — проверяет права (роли, атрибуты, voters).

---

## 1. Аутентификация: Как пользователь входит?

### Firewall Configuration (`security.yaml`)
```yaml
security:
    enable_authenticator_manager: true
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: auto
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                default_target_path: app_dashboard
            logout:
                path: app_logout
            # Для API:
            # guard:
            #     authenticators: [App\Security\CustomAuthenticator]
```

### Authenticator (для сложных сценариев)
Если стандартная `form_login` не подходит (например, login + 2FA + captcha), пишем свой `Authenticator`:

```php
class CustomAuthenticator extends AbstractLoginFormAuthenticator
{
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $password = $request->getPayload()->getString('password');
        
        $user = $this->userProvider->loadUserByIdentifier($email);
        
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException();
        }
        
        return new Passport(
            $user,
            new SelfValidatingPassportToken(),
            [] // roles
        );
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new JsonResponse(['message' => 'Login successful']);
    }
}
```

**Что происходит:**
1. Запрос приходит на endpoint.
2. Firewall ищет аутентификатор.
3. `Authenticator` валидирует учетные данные.
4. Успех → создается `Token` (содержит `User` + `Roles`).
5. `Token` сохраняется в сессии (или возвращается в JSON для SPA).

---

## 2. Авторизация: Роли vs Voters

### Роли (Roles)
Роли — это **статические ярлыки**, привязанные к пользоватю.

```php
class User implements UserInterface
{
    public function getRoles(): array
    {
        return [
            'ROLE_USER', // Базовая роль, всегда нужна
            // 'ROLE_ADMIN',
            // 'ROLE_EDITOR',
        ];
    }
}
```

**Проверка ролей:**
- В коде: `$this->isGranted('ROLE_ADMIN')`
- В шаблонах: `{% if is_granted('ROLE_ADMIN') %}`
- В атрибутах: `#[IsGranted('ROLE_ADMIN')]`

**Когда использовать роли:**
- Если логика проста: «Админ может всё, Пользователь — только читать».
- Если права не зависят от контекста объекта.

**Пример:**
```php
#[IsGranted('ROLE_ADMIN')]
public function deleteUser(User $user): void
{
    // Только админ может удалять любого пользователя
}
```

---

### Voters (Голосовающие)
Voters — это **стратегический паттерн** для сложной, контекстной логики доступа.

**Когда использовать Voters:**
- Если права зависят **от объекта**: «Пользователь может редактировать **свой** пост, но не чужой».
- Если права зависят **от атрибутов**: «Можно удалить только если статус = DRAFT».
- Если логика меняется часто или сложна.

### Как устроен Voter?

```php
class PostVoter extends AbstractVoter
{
    public const EDIT_POST = 'POST_EDIT';
    public const DELETE_POST = 'POST_DELETE';

    protected function vote(string $attribute, mixed $subject, UserInterface $user): bool
    {
        if (!$subject instanceof Post) {
            return false;
        }

        // 1. Проверка атрибута
        if ($attribute === self::EDIT_POST) {
            return $user === $subject->getOwner(); // Владелец может редактировать
        }

        if ($attribute === self::DELETE_POST) {
            return $user === $subject->getOwner() || $this->isGranted('ROLE_ADMIN', $user);
        }

        return false;
    }
}
```

### Регистрация Voter
В Symfony 6+ voters регистрируются автоматически, если они публичные и имеют тег `kernel.voter`.

```php
#[Service] // или в services.yaml
class PostVoter extends AbstractVoter { /* ... */ }
```

### Использование Voter

**В коде:**
```php
public function updatePost(Post $post): void
{
    if (!$this->isGranted(PostVoter::EDIT_POST, $post)) {
        throw new AccessDeniedException('You can only edit your own posts');
    }
    
    // Логика обновления
}
```

**В шаблонах (Twig):**
```twig
{% if is_granted('POST_EDIT', post) %}
    <a href="/edit/{{ post.id }}">Edit</a>
{% endif %}
```

**В контроллере (через атрибут):**
```php
#[IsGranted('POST_EDIT', subject: 'post')]
public function update(Request $request, Post $post): Response
{
    // Автоматически выбросит AccessDeniedException, если нет права
}
```

---

## 3. Роли vs Voters: Что выбрать?

| Критерий | Роли | Voters |
| :--- | :--- | :--- |
| **Сложность** | Простая | Средняя/Высокая |
| **Контекст** | Нет контекста (только пользователь) | Есть контекст (объект, атрибуты) |
| **Пример** | «Админ может удалять всех» | «Владелец может удалить свой пост» |
| **Масштабируемость** | Плохо (много ролей) | Хорошо (одна логика в Voter) |
| **Читаемость** | Высокая | Средняя (нужно знать атрибуты) |
| **Тестируемость** | Простая | Средняя (mock-и пользователя) |

### Правило выбора:

1. **Используйте Роли, если:**
   - Логика доступа бинарна (можно/нельзя).
   - Права не зависят от конкретного объекта.
   - Количество ролей мало (< 5).

2. **Используйте Voters, если:**
   - Права зависят от объекта (`is_owner`, `status`, `date`).
   - Логика доступа сложная или меняется.
   - Нужно переиспользовать логику в разных местах (код, шаблоны, API).

---

## 4. Гибридный подход (Лучшая практика)

Часто используются **и роли, и voters**.

### Пример: Комментарии к статьям

**Роли:**
- `ROLE_ADMIN` — может удалять любой комментарий.
- `ROLE_USER` — обычный пользователь.

**Voter:**
```php
class CommentVoter extends AbstractVoter
{
    public const DELETE_COMMENT = 'COMMENT_DELETE';

    protected function vote(string $attribute, mixed $subject, UserInterface $user): bool
    {
        if (!$subject instanceof Comment) {
            return false;
        }

        if ($attribute === self::DELETE_COMMENT) {
            // 1. Админ может удалить любой комментарий (через роль)
            if ($this->isGranted('ROLE_ADMIN', $user)) {
                return true;
            }
            
            // 2. Владелец может удалить свой комментарий
            return $user === $subject->getAuthor();
        }

        return false;
    }
}
```

**Использование:**
```php
#[IsGranted('COMMENT_DELETE', subject: 'comment')]
public function deleteComment(Comment $comment): void
{
    $this->repo->remove($comment);
}
```

**Почему это хорошо:**
- Логика «админ может всё» вынесена в Voter, но использует роль.
- Логика «владелец может удалить свой» вынесена в Voter.
- Контроллер чистый, без `if-else`.

---

## 5. Общие антипаттерны

1. **Проверка ролей в каждом методе**
   ```php
   if ($this->isGranted('ROLE_ADMIN')) { ... }
   // Лучше: использовать #[IsGranted('ROLE_ADMIN')] на контроллере
   ```

2. **Сложная логика в контроллере**
   ```php
   if ($user === $post->getOwner() || $user->getRole() === 'ADMIN' || $post->getStatus() === 'DRAFT') {
       // Ужас! Вынесите это в Voter
   }
   ```

3. **Игнорирование `ROLE_USER`**
   Если пользователь аутентифицирован, но не имеет ни одной роли, некоторые проверки могут вести себя неочевидно. Всегда выдавайте `ROLE_USER`.

4. **Проверка прав в сервисном слое**
   ```php
   // Сервис не должен знать о Security
   class PostService {
       public function delete(Post $post) {
           // ❌ Не проверяйте права здесь
           // ✅ Проверяйте в контроллере или через Voter
       }
   }
   ```
   **Исключение:** Если сервис вызывается из фоновых задач (Messenger), где нет токена безопасности, права нужно проверять явно.

---

## Итог

- **Аутентификация** — это «кто ты». Решается через Firewall + Authenticator.
- **Авторизация** — это «что ты можешь». Решается через Роли + Voters.
- **Роли** — для простых, глобальных прав.
- **Voters** — для сложных, контекстных прав.
- **Лучший подход** — использовать Voters, которые внутри могут проверять роли, если это упрощает логику.

> ⏱ 18.4s · prompt 23049 tok · output 2475 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение Value Objects (VO) в Doctrine — одна из самых сложных и спорных тем в PHP-экосистеме. Doctrine 2.14+ поддерживает **Custom Types** и **Immutable Objects**, но есть нюансы.

### Ключевые принципы
1. **Неизменяемость (Immutability)**: VO нельзя менять после создания.
2. **Валидность**: Невозможно создать «битый» VO (конструктор бросает исключение).
3. **Идентичность**: Два VO равны, если равны их свойства (не `===`, а `==`).

---

## Вариант 1: Custom Doctrine Type (Рекомендуемый)

Это **официальный** способ маппинга сложного типа в одну колонку.

### Как работает:
1. Создаёте класс `MoneyType`, расширяющий `Type`.
2. Реализуете `convertToDatabaseValue` и `convertToPHPValue`.
3. Маппите поле сущности как `money` (ваш кастомный тип).

### Пример: Money (Денежная сумма)

```php
<?php
// App/Domain/ValueObject/Money.php
final class Money
{
    public function __construct(
        private readonly int $amountInCents,
        private readonly string $currency
    ) {
        if ($amountInCents < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
        if (!in_array($currency, ['USD', 'EUR', 'RUB'], true)) {
            throw new \InvalidArgumentException('Unsupported currency');
        }
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Currency mismatch');
        }
        return new self($this->amountInCents + $other->amountInCents, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amountInCents === $other->amountInCents 
            && $this->currency === $other->currency;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
```

```php
<?php
// App/Infrastructure/Persistence/Doctrine/Type/MoneyType.php
use Doctrine\DBAL\Types\Type;
use App\Domain\ValueObject\Money;

class MoneyType extends Type
{
    public const NAME = 'money';

    public function getName(): string
    {
        return self::NAME;
    }

    // Конвертация VO -> БД (массив или строка)
    public function convertToDatabaseValue(mixed $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!$value instanceof Money) {
            throw new \RuntimeException('Expected Money instance');
        }
        // Храним как строку "1000:USD" или JSON
        return $value->getAmountInCents() . ':' . $value->getCurrency();
    }

    // Конвертация БД -> VO
    public function convertToPHPValue(mixed $value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }
        
        // Разбираем строку
        $parts = explode(':', $value);
        if (count($parts) !== 2) {
            throw new \RuntimeException('Invalid Money format');
        }
        
        return new Money(
            (int) $parts[0],
            $parts[1]
        );
    }

    public function getSQLDeclaration(array $column, \Doctrine\DBAL\Platforms\AbstractPlatform $platform): string
    {
        return 'VARCHAR(255)';
    }
}
```

### Регистрация типа:

```php
<?php
// config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            money: App\Infrastructure\Persistence\Doctrine\Type\MoneyType
```

### Использование в сущности:

```php
<?php
// App/Domain/Entity/Order.php
use App\Domain\ValueObject\Money;

class Order
{
    #[ORM\Column(type: 'money')]
    private ?Money $totalAmount;

    public function getTotalAmount(): ?Money
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(Money $amount): void
    {
        // Невозможно присвоить null или невалидную сумму
        $this->totalAmount = $amount;
    }
}
```

### ✅ Плюсы:
1. **Чистый домен**: Сущность знает только про `Money`, а не про `int` и `string`.
2. **Неизменяемость**: Конструктор `Money` гарантирует валидность.
3. **Одна колонка**: Нет разброса данных по нескольким полям.
4. **Легко тестировать**: `Money` — чистый PHP-объект.

### ❌ Минусы:
1. **Нет индексирования**: Не можете сделать SQL-индекс на `amount` или `currency` отдельно.
2. **Сложные запросы**: Нельзя написать DQL `WHERE order.totalAmount.amount > 1000`. Приходится фильтровать на PHP-стороне.
3. **Сериализация**: При сериализации (например, в Redis) нужно быть осторожным.

### ⚠️ Подводные камни:
1. **Сравнение в DQL**:
   ```php
   // ❌ НЕ РАБОТАЕТ
   $qb->andWhere('o.totalAmount.amount > :amount');
   
   // ✅ Работает только если вы хранили как JSON и используете JSON-функции БД
   // Или фильтруете на PHP-стороне
   ```

2. **Смена типа данных**:
   Если вы меняете формат хранения (например, из `VARCHAR` на `JSON`), нужно написать миграцию.

---

## Вариант 2: Композиция (Несколько колонок)

Вы храните компоненты VO в отдельных полях.

### Пример:

```php
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $amountInCents;

    #[ORM\Column(type: 'string')]
    private string $currency;

    // Приватный VO
    private ?Money $totalAmount;

    public function getTotalAmount(): ?Money
    {
        if ($this->totalAmount === null) {
            $this->totalAmount = new Money($this->amountInCents, $this->currency);
        }
        return $this->totalAmount;
    }

    public function setTotalAmount(Money $amount): void
    {
        $this->amountInCents = $amount->getAmountInCents();
        $this->currency = $amount->getCurrency();
    }
}
```

### ✅ Плюсы:
1. **Индексирование**: Можно индексировать `amountInCents`.
2. **Запросы**: Можно писать DQL: `WHERE o.amountInCents > 1000`.
3. **Миграции**: Легко изменить тип данных.

### ❌ Минусы:
1. **Нарушение инкапсуляции**: БД знает о внутренних деталях VO.
2. **Сложность**: Нужно вручную синхронизировать поля.
3. **Небезопасность**: Можно случайно присвоить `amountInCents` напрямую, минуя VO.

### ⚠️ Подводные камни:
1. **Состояние**: Если вы измените `amountInCents` напрямую, `totalAmount` станет невалидным.
2. **Сериализация**: При десериализации нужно проверять валидность.

---

## Вариант 3: JSON-колонка

Храните весь VO как JSON в `JSONB` (PostgreSQL) или `JSON` (MySQL).

### Пример:

```php
#[ORM\Column(type: 'json')]
private ?array $metadata; // Содержит {"amount": 1000, "currency": "USD"}
```

### ✅ Плюсы:
1. **Гибкость**: Можно добавить новые поля без миграций.
2. **Индексирование**: В PostgreSQL можно использовать GIN-индексы.
3. **Запросы**: Можно использовать JSON-функции БД.

### ❌ Минусы:
1. **Сложность**: JSON не типобезопасен.
2. **Производительность**: Медленнее, чем обычные колонки.
3. **Сериализация**: Нужно вручную разбирать JSON.

---

## Сравнение подходов

| Критерий | Custom Type | Композиция | JSON |
| :--- | :--- | :--- | :--- |
| **Инкапсуляция** | Высокая | Низкая | Средняя |
| **Запросы** | Плохие | Хорошие | Средние |
| **Индексирование** | Нет | Да | Да (GIN) |
| **Сложность** | Средняя | Низкая | Средняя |
| **Неизменяемость** | Высокая | Низкая | Средняя |

---

## Рекомендация

### Для простых VO (Money, Email, Address):
**Используйте Custom Type (Вариант 1).**
- Это чище и безопаснее.
- Запросы на такие поля редко требуют сложной фильтрации.

### Для сложных VO (Metadata, Configuration):
**Используйте JSON (Вариант 3).**
- Если структура часто меняется.
- Если нужны сложные JSON-запросы.

### Для часто используемых в фильтрах полей:
**Используйте Композицию (Вариант 2).**
- Если вам нужно часто фильтровать по `amount` или `currency`.
- Но добавьте строгие проверки в методах `get`/`set`.

---

## Лучшие практики

1. **Не храните ID в VO**. ID — это ссылка, а VO — значение.
2. **Используйте `readonly`** в VO (PHP 8.1+).
3. **Валидируйте в конструкторе**.
4. **Не мутируйте VO**. Создавайте новый экземпляр.
5. **Добавьте `equals()`** для сравнения.
6. **Добавьте `__toString()`** для отладки.
7. **Тестируйте VO отдельно**. Это чистый PHP-код.

### Пример полного VO с `readonly`:

```php
final readonly class Money
{
    public function __construct(
        public int $amountInCents,
        public string $currency
    ) {
        // Валидация
    }

    public function equals(self $other): bool
    {
        return $this->amountInCents === $other->amountInCents 
            && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return number_format($this->amountInCents / 100, 2) . ' ' . $this->currency;
    }
}
```

**Вывод:** Custom Type — лучший баланс между чистотой домена и удобством работы с БД для большинства Value Objects.

> ⏱ 17.8s · prompt 25578 tok · output 2616 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Хранение агрегатов в Doctrine — это баланс между **инкапсуляцией** (DDD) и **технической реализацией** (ORM).

### Ключевые принципы:
1. **Агрегатная корневая сущность (Aggregate Root)** — единственная точка входа в агрегат.
2. **Дочерние сущности** — не должны маппиться как отдельные идентифицируемые сущности с глобальными ID. Их жизненный цикл привязан к корню.
3. **Транзакция** — обхватывает операции над одним агрегатом (или несколькими, если они связаны бизнес-процессом).
4. **`flush()`** — вызывается **не в домене**, а в **приложении** (UseCase/Controller) после изменения состояния агрегата.

---

## Пример: Агрегат «Заказ» (Order)

### Структура домена:
```
Order (Aggregate Root)
├── id: OrderId
├── customerEmail: Email
├── items: Collection<OrderItem> (Вложенные сущности)
│   ├── OrderItem
│   │   ├── productId: ProductId
│   │   ├── quantity: int
│   │   └── unitPrice: Money
└── status: OrderStatus
```

**Важно:** `OrderItem` **не имеет** собственного `id` (или имеет локальный ID, привязанный к `order_id`). Он не является самостоятельной сущностью.

---

## 1. Маппинг коллекций

### Вариант A: `ORM\ManyToOne` + `ORM\JoinTable` (Не рекомендуется)
Это создает отдельную таблицу для связей, что нарушает инкапсуляцию агрегата.

### Вариант B: `ORM\OneToMany` + `ORM\JoinColumn` (Рекомендуемый)
Дочерние сущности хранятся в отдельной таблице, но **не имеют своего PK**. Их PK — `id` + `order_id` (составной) или просто `id`, но логически они принадлежат заказу.

### Вариант C: `ORM\Embedded` (Для простых Value Objects)
Если `OrderItem` — это не сущность, а Value Object (не имеет ID), можно использовать `EMBEDDED`.

---

### Рекомендуемый подход: `OneToMany` с составным PK или `Embedded`

#### Подход 1: `OrderItem` как сущность с локальным ID

```php
<?php
// App/Domain/Entity/Order.php
namespace App\Domain\Entity;

use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\OrderStatus;
use App\Domain\ValueObject\OrderId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private ?OrderId $id = null;

    #[ORM\Column(type: 'string')]
    private string $customerEmail;

    #[ORM\Column(type: 'string')]
    private string $status; // 'PENDING', 'PAID', etc.

    #[ORM\OneToMany(
        targetEntity: OrderItem::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    // Бизнес-методы
    public function addItem(ProductId $productId, int $quantity, Money $unitPrice): void
    {
        // Проверки: статус заказа, наличие товара
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Cannot add items to non-pending order');
        }

        $item = new OrderItem($productId, $quantity, $unitPrice);
        $item->setOrder($this); // Установка ссылки на корень
        $this->items->add($item);
    }

    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Order is not pending');
        }
        $this->status = OrderStatus::PAID;
    }

    public function getId(): OrderId
    {
        return $this->id;
    }

    public function setOrderId(OrderId $id): void
    {
        $this->id = $id;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function removeItem(OrderItem $item): void
    {
        $this->items->removeElement($item);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
}
```

```php
<?php
// App/Domain/Entity/OrderItem.php
namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(
        inversedBy: 'items',
        fetch: EAGER // Важно: EAGER, чтобы не было N+1
    )]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\Column(type: 'string')]
    private string $productId;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'integer')]
    private int $unitPriceInCents;

    public function __construct(
        ProductId $productId,
        int $quantity,
        Money $unitPrice
    ) {
        $this->productId = $productId->getValue();
        $this->quantity = $quantity;
        $this->unitPriceInCents = $unitPrice->getAmountInCents();
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProductId(): ProductId
    {
        return new ProductId($this->productId);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Money
    {
        return new Money($this->unitPriceInCents, 'USD');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }
}
```

---

## 2. Границы транзакций

### Кто начинает транзакцию?
**UseCase (Application Layer)**, а не Domain.

### Почему?
1. **Домен не знает о БД.** Он работает с объектами памяти.
2. **Транзакция — это инфраструктурная деталь.**
3. **Изоляция ошибок.** Если одна часть транзакции упадет, все изменения откатываются.

### Пример UseCase:

```php
<?php
// App/Application/UseCase/CreateOrderUseCase.php
namespace App\Application\UseCase;

use App\Domain\Entity\Order;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\OrderStatus;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Event\OrderCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MessageBusInterface $bus
    ) {}

    public function execute(
        string $customerEmail,
        array $items // [[productId: 'p1', qty: 2], ...]
    ): Order
    {
        $order = new Order();
        $order->setOrderId(new OrderId(uniqid()));
        $order->setCustomerEmail($customerEmail);

        foreach ($items as $itemData) {
            $order->addItem(
                new ProductId($itemData['productId']),
                $itemData['quantity'],
                new Money($itemData['price'], 'USD')
            );
        }

        // 1. Сохранение в БД (транзакция начинается здесь)
        $this->orderRepository->save($order);

        // 2. Публикация события (асинхронно)
        $this->bus->dispatch(new OrderCreatedEvent($order->getId()));

        return $order;
    }
}
```

---

## 3. Кто и когда вызывает `flush()`?

### Ответ: **Repository (Infrastructure Layer)**

Repository — это адаптер между доменом и инфраструктурой. Он знает, как сохранить объект в БД.

### Пример реализации:

```php
<?php
// App/Infrastructure/Persistence/Doctrine/OrderRepository.php
namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Entity\Order;
use App\Domain\Repository\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function save(Order $order): void
    {
        // Начало транзакции
        $transaction = $this->em->beginTransaction();
        
        try {
            // Сохраняем корень (каскуд сохранит дочерние OrderItems)
            $this->em->persist($order);
            
            // Финализация (запись в БД)
            $this->em->flush();
            
            // Коммит транзакции
            $this->em->commit();
        } catch (\Exception $e) {
            // Откат транзакции
            $this->em->rollBack();
            throw $e;
        }
    }

    public function findById(string $id): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
    }
}
```

---

## 4. Подводные камни

### 1. N+1 Problem
Если вы используете `fetch: LAZY`, то при итерации по `$order->getItems()` будет сделано N+1 запросов.

**Решение:**
- Используйте `fetch: EAGER` (как в примере выше).
- Или используйте `DQL` с `JOIN`:
  ```sql
  SELECT o, i FROM Order o JOIN o.items i WHERE o.id = :id
  ```

### 2. orphanRemoval
`orphanRemoval: true` означает, что если вы удаляете элемент из коллекции `$order->items`, он будет удален из БД.

**Важно:** Это работает только если объект является «сиротой» (не привязан к другому агрегату).

### 3. Каскадирование
`cascade: ['persist', 'remove']` означает:
- `persist`: Если сохранять `$order`, Doctrine автоматически сохранит все `$order->items`.
- `remove`: Если удалить `$order`, Doctrine автоматически удалит все `$order->items`.

### 4. Транзакции и события
Если вы публикуете событие **до** `flush()`, и событие упадет, транзакция будет откатана.

**Правильно:**
1. `persist`
2. `flush`
3. `commit`
4. `dispatch event`

Если событие упадет — транзакция уже закоммичена. Нужно использовать **Outbox Pattern** для обеспечения consistency.

---

## 5. Итоговая архитектура

```
[Controller]
    │
    ▼
[CreateOrderUseCase]
    │ 1. Создает Order (Domain)
    │ 2. Вызывает orderRepository->save($order)
    ▼
[OrderRepository]
    │ 1. $em->persist($order)
    │ 2. $em->flush()
    │ 3. $em->commit()
    ▼
[Doctrine ORM]
    │ 1. INSERT INTO orders (...)
    │ 2. INSERT INTO order_items (...)
    ▼
[PostgreSQL]
```

### Ключевые правила:
1. **Агрегатная корень** — единственная точка входа.
2. **Дочерние сущности** — не имеют глобального ID, их PK локален.
3. **Транзакция** — начинается в UseCase, завершается в Repository.
4. **`flush()`** — вызывается в Repository, после `persist`.
5. **События** — публикуются после коммита транзакции.

> ⏱ 19.5s · prompt 28261 tok · output 2940 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика N+1 — это стандартная процедура в Doctrine. Вот как я подхожу к решению этой проблемы, от выявления до оптимизации.

---

## 1. Диагностика: Как найти N+1?

### Способ 1: Logger DBAL (Самый быстрый)
Включите логирование SQL-запросов в режиме разработки.

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            enabled: true
        profiler:
            enabled: true
```

Затем откройте **Symfony Profiler** (через `WebProfilerBundle`) или просмотрите логи.
Вы увидите список запросов. Если за 1 страницу вы делаете, например, 50 запросов, а ожидали 2–3 — это N+1.

### Способ 2: Debug Toolbar
В `dev`-режиме Symfony показывает количество SQL-запросов в правом верхнем углу. Если цифра растет пропорционально количеству сущностей на странице (например, 10 товаров = 11 запросов) — это N+1.

### Способ 3: Log Channel (Для продакшена)
Настройте логирование запросов в отдельный канал:

```yaml
# config/packages/framework.yaml
framework:
    profiler:
        collect: false # Отключите profiler в проде
```

```php
// В сервисе
use Psr\Log\LoggerInterface;

class OrderService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}
}
```

---

## 2. Почему возникает N+1?

**Сценарий:**
1. Запрос 1: `SELECT * FROM orders` (получили 10 заказов).
2. Для каждого заказа (в цикле) выполняется запрос: `SELECT * FROM order_items WHERE order_id = X` (10 запросов).

**Причина:**
- `fetch: LAZY` (значение по умолчанию).
- Отсутствие `JOIN` в DQL.
- Отсутствие `EAGER` loading (который часто вредит).

---

## 3. Способы решения и их Trade-offs

### Вариант 1: `DQL JOIN` (Eager Loading через запрос)

**Как:**
```php
$query = $em->createQuery('
    SELECT o, i
    FROM App\Entity\Order o
    JOIN o.items i
    WHERE o.status = :status
');
```

**Результат:**
1 запрос к `orders` + 1 запрос к `order_items` (через JOIN).

**✅ Плюсы:**
- Минимальное количество запросов (2).
- Простота реализации.

**❌ Минусы:**
- **Память:** Если у 10 заказов по 100 позиций, вы загрузите 1000 строк `order_items` в память, даже если вам нужны только заголовки.
- **Дублирование данных:** Если вы делаете `JOIN` на несколько коллекций, данные будут дублироваться (Cartesian Product).
- **Негибкость:** Нельзя сделать `JOIN` динамически в зависимости от контекста.

**Когда использовать:**
- Когда коллекции маленькие (до 10–20 элементов).
- Когда вам нужны все дочерние сущности сразу.

---

### Вариант 2: `DQL PARTIAL OBJECT` (Select specific fields)

**Как:**
```php
$query = $em->createQuery('
    SELECT PARTIAL o.{id, totalAmount, customerEmail}
    FROM App\Entity\Order o
    WHERE o.status = :status
');
```

**Результат:**
1 запрос, но загрузится только часть полей.

**✅ Плюсы:**
- Экономия памяти.
- Быстрый ответ.

**❌ Минусы:**
- **Частичная сущность:** Вы не можете вызвать методы, которые зависят от не загруженных полей (например, `getItems()` упадет с ошибкой `LazyLoadingException`).
- **Сложность:** Нужно точно знать, какие поля нужны.

**Когда использовать:**
- Для списков (List views), где нужны только заголовки.
- Когда коллекции не нужны.

---

### Вариант 3: `EAGER` Loading (Аннотация)

**Как:**
```php
#[ORM\OneToMany(
    targetEntity: OrderItem::class,
    mappedBy: 'order',
    fetch: 'EAGER' // Включает автоматический JOIN
)]
private Collection $items;
```

**Результат:**
Каждый раз, когда вы запрашиваете `Order`, Doctrine автоматически делает `JOIN` с `OrderItem`.

**✅ Плюсы:**
- Простота: не нужно писать DQL.
- Всегда актуальные данные.

**❌ Минусы:**
- **Постоянная цена:** Вы платите за JOIN даже когда он не нужен (например, при загрузке только ID).
- **Конфликты с QueryBuilder:** Если вы делаете `SELECT o FROM Order o` без JOIN, `EAGER` может не сработать или вызвать ошибку, в зависимости от версии Doctrine.
- **Сложные сценарии:** Если у сущности несколько коллекций, `EAGER` может создать огромное количество JOIN-ов (Cartesian Product).

**Когда использовать:**
- Редко. Только для очень простых сценариев, где дочерние сущности всегда нужны.

---

### Вариант 4: `Paging` + `LIMIT` (Разбивка на страницы)

**Как:**
Не загружайте все заказы сразу. Загружайте по 20–50.

```php
$query->setMaxResults(20);
$query->setFirstResult(0);
```

**✅ Плюсы:**
- Ограничивает количество данных.
- Улучшает производительность БД.

**❌ Минусы:**
- N+1 все равно есть, но на меньшем наборе данных.
- Нужно реализовать пагинацию.

**Когда использовать:**
- Всегда. Никогда не загружайте безлимитные списки.

---

### Вариант 5: `Batch Loading` (Ручная оптимизация)

**Как:**
Сначала загрузите все IDs, затем сделайте один запрос по ним.

```php
// 1. Получаем заказы
$orders = $repo->findBy(['status' => 'PENDING']);
$ids = array_map(fn($o) => $o->getId(), $orders);

// 2. Получаем все позиции для этих заказов одним запросом
$items = $em->createQuery('
    SELECT i
    FROM App\Entity\OrderItem i
    WHERE i.order IN (:ids)
')->setParameter('ids', $ids)->getResult();

// 3. Связываем в PHP-коде
$itemsByOrder = [];
foreach ($items as $item) {
    $itemsByOrder[$item->getOrder()->getId()][] = $item;
}

foreach ($orders as $order) {
    $order->setItems(new ArrayCollection($itemsByOrder[$order->getId()] ?? []));
}
```

**✅ Плюсы:**
- Всегда 2 запроса, независимо от размера коллекций.
- Нет Cartesian Product.
- Полный контроль над данными.

**❌ Минусы:**
- **Сложный код:** Нужно вручную связывать сущности.
- **Обход Doctrine:** Вы ломаете инкапсуляцию (вызываете `setItems()`, который обычно приватен).
- **Нужно знать структуру данных.**

**Когда использовать:**
- Для больших списков.
- Когда `JOIN` создает слишком много данных.

---

### Вариант 6: `Denormalization` (Денормализация)

**Как:**
Храните часто используемые данные дочерних сущностей в корневой сущности.

```php
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $totalItemCount; // Денормализованное поле

    #[ORM\Column(type: 'integer')]
    private int $totalAmountInCents; // Денормализованное поле
}
```

**✅ Плюсы:**
- 1 запрос.
- Максимальная скорость.

**❌ Минусы:**
- **Синхронизация:** Нужно обновлять денормализованные поля при каждом изменении дочерних сущностей.
- **Сложность логики:** Логика расчета дублируется.
- **Нарушение нормализации.**

**Когда использовать:**
- Для полей, которые редко меняются.
- Для отчетов и списков.

---

## 4. Сравнение подходов

| Способ | Кол-во запросов | Память | Сложность | Когда использовать |
| :--- | :--- | :--- | :--- | :--- |
| **DQL JOIN** | 2 | Высокая | Низкая | Маленькие коллекции |
| **Partial Object** | 1 | Низкая | Средняя | Списки без деталей |
| **EAGER** | 2+ | Высокая | Низкая | Редко, простые случаи |
| **Paging** | N+1 (но меньше) | Средняя | Низкая | Всегда |
| **Batch Loading** | 2 | Средняя | Высокая | Большие списки |
| **Denormalization** | 1 | Низкая | Высокая | Критические сценарии |

---

## 5. Рекомендация

### Для большинства случаев:
1. **Всегда используйте `Paging`** (`setMaxResults`).
2. **Используйте `DQL JOIN`**, если коллекции маленькие (до 10–20 элементов).
3. **Используйте `Partial Object`**, если нужны только заголовки.
4. **Используйте `Batch Loading`**, если коллекции большие и `JOIN` медленный.

### Для критических сценариев:
1. **Denormalization** — если нужна максимальная скорость.
2. **Cache (Redis/Memcached)** — если данные редко меняются.

### Чего НЕ делать:
1. **Не включайте `EAGER` по умолчанию** — это ловушка.
2. **Не используйте `N+1` для списков** — всегда пагинируйте.
3. **Не игнорируйте Profiler** — всегда проверяйте количество запросов.

> ⏱ 18.6s · prompt 31255 tok · output 2343 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это классический вопрос, который часто звучит как: «Раз уж мы валидируем в домене, зачем дублировать это в БД?»

Короткий ответ: **Бизнес-валидация в коде проверяет *правильность* действий конкретного пользователя в данный момент. Ограничения в БД гарантируют *целостность данных* для всей системы в любой момент времени.**

Даже если вы пишете идеальный код, есть сценарии, когда данные могут попасть в БД, минуя вашу валидацию.

---

## Почему ограничения в БД необходимы?

### 1. «Последняя миля» (Last Line of Defense)
БД — это единственное доверенное хранилище. Код может упасть, может быть баг в ORM, может кто-то вручную вставить запись через `psql`. Ограничения в БД — это **гарантия**, что невалидные данные никогда не станут частью системы.

### 2. Параллельные операции (Race Conditions)
Ваша валидация в коде часто выглядит так:
1. Прочитать состояние.
2. Проверить условия.
3. Записать изменение.

Если две транзакции выполняются одновременно, обе могут пройти проверку на шаге 2, но одна из них нарушит целостность на шаге 3.

**Пример:**
- Остаток товара: 1.
- Пользователь A и Пользователь B одновременно пытаются купить этот товар.
- Оба читают остаток 1.
- Оба проходят валидацию `if (stock > 0)`.
- Оба записывают `stock = 0`.
- **Результат:** Продано 2 товара, остаток -1 (или 0, но два заказа на один товар).

**Решение:** `CHECK (stock >= 0)` в БД предотвратит запись отрицательного остатка. Но лучше — оптимистичная блокировка или `SELECT ... FOR UPDATE`.

### 3. Обходные пути
- Ручные SQL-запросы от администраторов.
- Миграции, которые бьются в БД напрямую.
- Другие сервисы, которые пишут в ту же БД (если БД не изолирована).
- Тесты, которые могут не учитывать все правила.

### 4. Производительность
Проверка `UNIQUE` или `PRIMARY KEY` в БД быстрее, чем загрузка сущности в память, проверка в PHP и сохранение.

### 5. Семантическая ясность
Ограничения в БД — это **документация** для разработчиков и DBA. Они видят, какие инварианты данных являются критичными.

---

## Какие средства используются в PostgreSQL?

### 1. `PRIMARY KEY` и `UNIQUE`
**Зачем:** Гарантирует уникальность идентификаторов и бизнес-ключей.

```sql
-- Пользователь не может иметь два email'а
ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);

-- Заказ не может быть связан с несуществующим клиентом
ALTER TABLE orders ADD CONSTRAINT fk_customer FOREIGN KEY (customer_id)
    REFERENCES users(id) ON DELETE CASCADE;
```

**Когда использовать:** Всегда. Это фундамент целостности.

### 2. `CHECK` Constraints
**Зачем:** Гарантирует, что значения полей находятся в допустимом диапазоне.

```sql
-- Остаток не может быть отрицательным
ALTER TABLE products ADD CONSTRAINT check_stock CHECK (stock >= 0);

-- Статус заказа может быть только один из допустимых
ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('PENDING', 'PAID', 'SHIPPED'));

-- Email должен содержать @ (грубая проверка)
ALTER TABLE users ADD CONSTRAINT check_email CHECK (email LIKE '%@%');
```

**Важно:** В PostgreSQL 11+ `CHECK`-ограничения могут ссылаться на другие столбцы (cross-column).

```sql
-- Дата окончания должна быть позже даты начала
ALTER TABLE bookings ADD CONSTRAINT check_dates CHECK (end_date > start_date);
```

### 3. `EXCLUDE` Constraints
**Зачем:** Гарантирует, что не пересекаются диапазоны (интервалы). Идеально для календарей, бронирований, временных интервалов.

```sql
-- Нельзя бронировать одну комнату в пересекающихся временных интервалах
ALTER TABLE room_bookings ADD CONSTRAINT exclude_overlapping
    EXCLUDE USING gist (
        room_id WITH =,
        tstzrange(start_time, end_time) WITH &&
    );
```

**Когда использовать:** Для задач с временными интервалами (календари, брони, слоты).

### 4. Triggers (Триггеры)
**Зачем:** Более сложная логика, которую нельзя выразить через `CHECK`.

```sql
-- Пример: Запрет удаления пользователя, если у него есть заказы
CREATE FUNCTION prevent_user_deletion()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM orders WHERE customer_id = OLD.id) THEN
        RAISE EXCEPTION 'Cannot delete user with existing orders';
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_user_deletion
    BEFORE DELETE ON users
    FOR EACH ROW
    EXECUTE FUNCTION prevent_user_deletion();
```

**Когда использовать:**
- Сложные бизнес-правила, которые нельзя выразить через SQL-ограничения.
- Аудит (запись изменений в лог-таблицу).
- Синхронизация данных между таблицами.

**Минусы:**
- Сложность отладки.
- Производительность (выполняются в контексте транзакции).
- Непрозрачность (логика скрыта от приложения).

### 5. Generated Columns (PostgreSQL 12+)
**Зачем:** Автоматически вычисляемые поля, которые всегда актуальны.

```sql
-- Храним сумму заказа, но она всегда равна сумме позиций
ALTER TABLE orders ADD COLUMN total_amount NUMERIC(10,2)
    GENERATED ALWAYS AS (amount * quantity) STORED;
```

**Когда использовать:**
- Для полей, которые являются функцией других полей.
- Чтобы исключить расхождение между вычисленным и фактическим значением.

### 6. Table Partitions
**Зачем:** Не ограничение целостности, но влияет на целостность при больших объемах данных.

---

## В каких случаях **НЕ** стоит дублировать?

1. **Сложная бизнес-логика.**
   Если валидация требует обращения к внешним API, сложной математики или многошаговой логики — это **не место для БД**.
   
   *Пример:* «Пользователь может сделать только 3 заказа в день, если у него золотой статус».
   Это лучше проверять в коде, а в БД оставить только `UNIQUE` на `user_id + date + slot` (если применимо).

2. **Правила, которые могут меняться часто.**
   Если правила часто меняются, триггеры в БД станут обузой. Лучше держать их в коде.

3. **Валидация, зависящая от контекста пользователя.**
   БД не знает, кто сейчас выполняет операцию (если не передать этого явно). В коде вы легко проверяете `if ($user->isAdmin())`.

---

## Рекомендация: Разделение ответственности

| Тип валидации | Где проверять | Почему |
| :--- | :--- | :--- |
| **Формат данных** (email, телефон) | **БД** + **Код** | БД гарантирует целостность, код дает UX (быстрый фидбек) |
| **Уникальность** (email, id) | **БД** | БД — единственный источник истины, исключает race conditions |
| **Целочисленные диапазоны** (возраст, количество) | **БД** (`CHECK`) | Простые правила, легко выразить в SQL |
| **Отношения** (FK) | **БД** | БД гарантирует, что ссылки не повисли в воздухе |
| **Бизнес-правила** (скидки, лимиты) | **Код** | Сложная логика, часто меняется |
| **Параллельные операции** | **БД** (Locks, `CHECK`) | БД управляет конкурентностью |

---

## Итог

**Бизнес-валидация в коде** отвечает за **UX** и **бизнес-логику**.
**Ограничения в БД** отвечают за **целостность** и **надежность**.

Они **не дублируют** друг друга, а **дополняют**.

### Практическое правило:
1. **Всегда** ставьте `PRIMARY KEY`, `UNIQUE`, `FOREIGN KEY` в БД.
2. **Очень часто** ставьте `CHECK` для простых ограничений (диапазоны, допустимые значения).
3. **Редко** используйте триггеры — только для сложных случаев, которые нельзя выразить ограничениями.
4. **Никогда** не полагайтесь только на код для критичных инвариантов данных.

> ⏱ 17.7s · prompt 33646 tok · output 2105 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленного запроса в PostgreSQL — это детектив, который требует системного подхода. Я разбиваю процесс на этапы: от сбора данных до применения конкретных оптимизаций.

---

## 1. Сбор данных: Что мы вообще видим?

### Шаг 1: `EXPLAIN (ANALYZE, BUFFERS)`
Это главный инструмент. Он показывает реальное время выполнения каждого этапа запроса и использование буферов.

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT * FROM orders WHERE customer_id = 123;
```

**Что искать в выводе:**
1. **`Actual Time`**: Время выполнения каждого узла дерева плана. Ищите узел с самым большим значением.
2. **`Rows`**: Сколько строк обработано на каждом этапе. Если `Rows` сильно больше, чем нужно — есть проблема.
3. **`Buffers: shared hit/read`**: 
   - `hit` — данные были в кэше (быстро).
   - `read` — данные читались с диска (медленно). Если `read` высокий — проблема с I/O.
4. **`Loops`**: Если узел в цикле, время умножается на число итераций.

### Шаг 2: `pg_stat_statements`
Расширение, которое хранит статистику по всем выполненным запросам.

```sql
SELECT 
    calls,
    total_exec_time / calls AS avg_time,
    rows,
    query
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;
```

**Зачем:**
- Найти самые медленные запросы в системе.
- Определить, вызывается ли медленный запрос часто (высокий `calls`).
- Сравнить `rows` с `calls`: если `rows/calls` большой — запрос тянет много данных.

### Шаг 3: `pg_stat_user_indexes`
Просмотр использования индексов.

```sql
SELECT 
    indexrelname,
    idx_scan,       -- Сколько раз индекс использовался
    idx_tup_read,   -- Сколько строк прочитано через индекс
    idx_tup_fetch   -- Сколько строк получено
FROM pg_stat_user_indexes
ORDER BY idx_scan DESC;
```

**Зачем:**
- Найти неиспользуемые индексы (занимают место и замедляют запись).
- Найти индексы, которые используются, но не дают эффекта.

### Шаг 4: `pg_stat_activity`
Просмотр активных соединений и текущих запросов.

```sql
SELECT 
    pid,
    now() - pg_stat_activity.query_start AS duration,
    state,
    wait_event_type,
    wait_event,
    query
FROM pg_stat_activity
WHERE state != 'idle'
ORDER BY duration DESC;
```

**Зачем:**
- Найти заблокированные запросы (Locks).
- Определить, на чем запрос ждёт (I/O, Locks, CPU).

---

## 2. Анализ плана запроса: Типичные проблемы

### Проблема 1: Seq Scan (Последовательное сканирование)
**Симптом:** `Seq Scan on orders` на большой таблице.

**Причины:**
1. Отсутствует индекс по колонке в `WHERE`.
2. Индекс есть, но PostgreSQL считает, что последовательное чтение быстрее (если выбирается > 30–40% таблицы).
3. Индекс не используется из-за селекции (например, `WHERE LOWER(email) = 'x'` — индекс на `email` не сработает).

**Решение:**
- Создать индекс: `CREATE INDEX idx_orders_customer ON orders(customer_id);`
- Проверить, является ли условие в `WHERE` index-friendly.
- Проверить `SELECTIVITY`: если запрос выбирает много строк, Seq Scan может быть оптимален.

### Проблема 2: Nested Loop с большим количеством строк
**Симптом:** `Nested Loop` с `rows = 10000` во внутреннем узле.

**Причины:**
- Соединение двух больших таблиц без хеширования.
- PostgreSQL не знает, что лучше использовать `Hash Join` или `Merge Join`.

**Решение:**
- Проверить статистики: `ANALYZE table_name;`
- Использовать `Hash Join` (обычно быстрее для больших таблиц).
- Проверить, можно ли уменьшить количество строк до соединения (фильтры).

### Проблема 3: High Memory Usage / Spill to Disk
**Симптом:** `Sort` или `Hash` с `Temp File: NNNN` в выводе `EXPLAIN`.

**Причины:**
- `work_mem` (или `shared_buffers` в новых версиях) слишком мал.
- Запрос сортирует/хеширует слишком много данных.

**Решение:**
- Увеличить `work_mem` (осторожно, это на соединение!).
- Оптимизировать запрос, чтобы уменьшить количество данных для сортировки.
- Использовать `LIMIT` раньше.

### Проблема 4: N+1 на уровне БД (Chatter)
**Симптом:** Много маленьких запросов, каждый быстрый, но суммарно медленно.

**Причины:**
- ORM делает N+1 запросы.
- Миграции/скрипты делают запросы в цикле.

**Решение:**
- Оптимизировать ORM (JOIN, Batch).
- Использовать `COPY` для массовых вставок.
- Использовать CTE или временные таблицы.

---

## 3. Типовые причины медленных запросов

| Причина | Симптомы | Решение |
| :--- | :--- | :--- |
| **Отсутствие индекса** | `Seq Scan`, высокая `rows` | Создать индекс |
| **Просроченная статистика** | План оптимален для старых данных | `ANALYZE table` |
| **Сложные выражения в WHERE** | Индекс не используется | Изменить запрос, добавить функциональный индекс |
| **Слишком много данных** | `Rows` большое | Оптимизировать SELECT, использовать LIMIT |
| **Lock contention** | `wait_event_type = Lock` | Оптимизировать транзакции, уменьшить блокировки |
| **I/O bottleneck** | Высокие `read` в BUFFERS | Увеличить `shared_buffers`, SSD |
| **Пагинация с OFFSET** | Медленная сортировка | Использовать Keyset Pagination |
| **Неселективный JOIN** | Cartesian Product | Добавить условия, оптимизировать JOIN |

---

## 4. Специфические оптимизации PostgreSQL

### 1. Функциональные индексы
Если вы часто фильтруете по `LOWER(email)`:
```sql
CREATE INDEX idx_users_email_lower ON users(LOWER(email));
```
Используйте в запросе:
```sql
SELECT * FROM users WHERE LOWER(email) = 'test@example.com';
```

### 2. Partial Indexes
Если вы часто фильтруете по активным записям:
```sql
CREATE INDEX idx_orders_active ON orders(customer_id) WHERE status = 'ACTIVE';
```
Индекс меньше, работает быстрее.

### 3. Keyset Pagination (вместо OFFSET)
**Плохо:**
```sql
SELECT * FROM orders ORDER BY id OFFSET 10000 LIMIT 20;
```
**Хорошо:**
```sql
SELECT * FROM orders WHERE id > :last_id ORDER BY id LIMIT 20;
```

### 4. Объединение JOIN-ов
**Плохо:**
```sql
SELECT * FROM orders o
JOIN order_items i ON o.id = i.order_id
JOIN users u ON o.customer_id = u.id
WHERE o.status = 'ACTIVE';
```
**Хорошо (если нужно только количество):**
```sql
SELECT 
    u.id,
    u.name,
    COUNT(o.id) as order_count
FROM users u
LEFT JOIN orders o ON u.id = o.customer_id AND o.status = 'ACTIVE'
GROUP BY u.id, u.name;
```

### 5. CTE для повторяющихся подзапросов
**Плохо:**
```sql
SELECT * FROM orders o
WHERE o.total > (
    SELECT AVG(total) FROM orders WHERE customer_id = o.customer_id
);
```
**Хорошо:**
```sql
WITH customer_avg AS (
    SELECT customer_id, AVG(total) as avg_total
    FROM orders
    GROUP BY customer_id
)
SELECT o.*
FROM orders o
JOIN customer_avg ca ON o.customer_id = ca.customer_id
WHERE o.total > ca.avg_total;
```

---

## 5. Процесс оптимизации (Пошагово)

1. **Получить `EXPLAIN ANALYZE`** для медленного запроса.
2. **Найти узкое место**: Какой узел занимает больше всего времени?
3. **Проверить `ROWS`**: Обработка слишком большого количества строк?
4. **Проверить `BUFFERS`**: Проблемы с кэшем/диском?
5. **Проверить индексы**: Есть ли индекс по колонкам в `WHERE`, `JOIN`, `ORDER BY`?
6. **Проверить статистику**: Выполнен ли `ANALYZE`?
7. **Проверить конфигурацию**: `work_mem`, `shared_buffers`, `effective_cache_size`.
8. **Переписать запрос**: Уменьшить количество данных, изменить JOIN-ы, использовать CTE.
9. **Добавить/изменить индексы**: Partial, Functional.
10. **Проверить блокировки**: `pg_locks`, `pg_stat_activity`.
11. **Проверить на нагрузке**: Запустить запрос несколько раз, посмотреть на стабильность.

---

## 6. Частые ошибки разработчиков

1. **`SELECT *`**: Загрузка ненужных колонок увеличивает передачу данных.
2. **Отсутствие `LIMIT`**: Запрос возвращает миллион строк.
3. **Использование `OFFSET`** для глубокой пагинации.
4. **Сложные подзапросы** в `WHERE`, которые можно заменить на `JOIN`.
5. **Игнорирование `EXPLAIN`**: Оптимизация «наугад».
6. **Слишком много индексов**: Замедляют запись.
7. **Неправильный тип данных**: `TEXT` вместо `VARCHAR`, `NUMERIC` вместо `BIGINT`.

---

## Итог

Диагностика медленного запроса — это итеративный процесс:
1. **Измерьте** (`EXPLAIN`, `pg_stat_statements`).
2. **Найдите узкое место** (индексы, JOIN, сортировка).
3. **Оптимизируйте** (индексы, переписывание запроса, конфигурация).
4. **Измерите снова**.

**Помните:** Нет универсального решения. Каждый запрос нужно анализировать индивидуально.

> ⏱ 19.2s · prompt 35798 tok · output 2493 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD — это кластер связанных сущностей и Value Objects, которые рассматриваются как **единое целое** для целей изменения состояния.

### Ключевые свойства агрегата:
1. **Агрегатная корневая сущность (Aggregate Root)** — единственная точка входа. Внешний код не может напрямую обращаться к внутренним сущностям агрегата.
2. **Инварианты (Invariants)** — бизнес-правила, которые должны всегда выполняться внутри агрегата. Если правило нарушено, система находится в невалидном состоянии.
3. **Транзакционная целостность** — все изменения внутри агрегата должны быть атомарными (либо все применились, либо ни одно).
4. **Идентичность** — агрегат имеет свойство `ID`, которое определяет его уникальность.

---

## Как определять границы агрегата?

Это самое сложное решение в DDD. Границы агрегата определяются по **инвариантам**, а не по структуре БД.

### Алгоритм определения:

1. **Найдите инварианты.**
   Какие бизнес-правила должны выполняться **всегда**, чтобы система была валидной?
   *Пример:* «Остаток товара на складе не может быть отрицательным».

2. **Определите минимальный набор сущностей, который гарантирует выполнение этих инвариантов.**
   Если для гарантии правила нужны сущности `Order`, `OrderItem` и `InventoryItem` — это **один** агрегат (или связанные агрегаты).

3. **Определите, какие сущности можно вынести за границы.**
   Если сущность не участвует в инвариантах этого агрегата, она должна быть в другом агрегате или быть Value Object.

4. **Определите, как агрегаты взаимодействуют.**
   Агрегаты не должны напрямую ссылаться друг на друга по идентификаторам, если это создает проблемы с транзакциями. Они обмениваются **событиями** (Domain Events) или **идентификаторами** (ID).

---

## Пример: Агрегат «Заказ» (Order)

### Структура домена:
- `Order` (Агрегатная корень)
- `OrderItem` (Внутренняя сущность)
- `Customer` (Отдельный агрегат)
- `Product` (Отдельный агрегат)
- `Inventory` (Отдельный агрегат)

### Инварианты агрегата `Order`:
1. **Каждый `Order` должен иметь хотя бы один `OrderItem`.**
2. **Сумма заказа должна быть равна сумме позиций.**
3. **Статус заказа должен быть валидным для текущего состояния.**

### Код:

```php
<?php
// App/Domain/Entity/Order.php
namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\OrderStatus;
use App\Domain\Event\OrderItemsAdded;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Order
{
    private Collection $items;
    private OrderStatus $status;
    private Money $totalAmount;
    
    /** @var array<int, object> */
    private array $domainEvents = [];

    public function __construct(
        private readonly string $id,
        private readonly string $customerId // Только ID, не объект!
    ) {
        $this->items = new ArrayCollection();
        $this->status = OrderStatus::CREATED;
        $this->totalAmount = new Money(0, 'USD');
    }

    public function addItem(string $productId, int $quantity, Money $unitPrice): void
    {
        // Инвариант 1: Заказ должен быть в статусе CREATED или PENDING
        if (!in_array($this->status, [OrderStatus::CREATED, OrderStatus::PENDING], true)) {
            throw new \DomainException('Cannot add items to closed order');
        }

        // Инвариант 2: Количетво должно быть положительным
        if ($quantity <= 0) {
            throw new \DomainException('Quantity must be positive');
        }

        $item = new OrderItem($productId, $quantity, $unitPrice);
        $this->items->add($item);

        // Инвариант 3: Пересчет суммы
        $this->recalculateTotal();

        // Публикация события (внутри агрегата)
        $this->record(new OrderItemsAdded($this->id));
    }

    public function markAsPaid(): void
    {
        // Инвариант: Только PENDING заказы могут быть оплачены
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Only pending orders can be paid');
        }

        // Инвариант: Заказ должен иметь позиции
        if ($this->items->isEmpty()) {
            throw new \DomainException('Order must have items');
        }

        $this->status = OrderStatus::PAID;
        $this->record(new OrderPaid($this->id));
    }

    private function recalculateTotal(): void
    {
        $total = new Money(0, 'USD');
        foreach ($this->items as $item) {
            $total = $total->add($item->getTotalPrice());
        }
        $this->totalAmount = $total;
    }

    public function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return object[] */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    // Getters...
}
```

### Почему `Customer` и `Product` не входят в агрегат?

1. **`Customer`** — это отдельный агрегат, потому что:
   - У клиента есть свои инварианты (email валиден, пароль hash).
   - Клиент может существовать без заказов.
   - Изменение клиента не должно влиять на целостность заказа (и наоборот).
   - Если бы `Customer` был внутри `Order`, мы бы не смогли обновить email клиента без перезаписи всех заказов.

2. **`Product`** — это отдельный агрегат, потому что:
   - Продукт существует независимо от заказов.
   - Изменение цены продукта не должно автоматически менять старые заказы (исторические данные).
   - В `OrderItem` хранится только `productId` и `unitPrice` на момент заказа.

### Как они взаимодействуют?

```php
// UseCase: CreateOrder
class CreateOrderUseCase
{
    public function execute(string $customerId, array $items): Order
    {
        // 1. Проверка, что клиент существует (через репозиторий)
        $this->customerRepo->assertExists($customerId);

        // 2. Проверка, что продукты существуют (через репозиторий)
        foreach ($items as $item) {
            $this->productRepo->assertExists($item['productId']);
        }

        // 3. Создание заказа (внутри агрегата проверяются инварианты)
        $order = new Order(uniqid(), $customerId);
        foreach ($items as $item) {
            $order->addItem($item['productId'], $item['qty'], $item['price']);
        }

        // 4. Сохранение
        $this->orderRepo->save($order);

        // 5. Публикация событий (вне транзакции заказа)
        foreach ($order->releaseEvents() as $event) {
            $this->eventBus->dispatch($event);
        }

        return $order;
    }
}
```

---

## Типовая ошибка: «Богатый агрегат» (God Aggregate)

### Ошибка:
Разработчик пытается включить **все** связанные сущности в один агрегат, чтобы «быстрее было».

```php
// ❌ ПЛОХО
class Order
{
    private Customer $customer; // Объект, не ID!
    private Product $product;   // Объект, не ID!
    private Inventory $inventory; // Объект!
    private Payment $payment;     // Объект!
}
```

### Почему это плохо:

1. **Транзакционная целостность:**
   Если при сохранении `Order` нужно обновить `Inventory`, `Payment` и `Customer` — одна ошибка в любом из них откатит всю операцию. Это **локальная** целостность, которая создает проблемы при масштабировании.

2. **Нарушение инкапсуляции:**
   Если вы захотите изменить `Customer` (например, добавить поле `middle_name`), вам нужно будет пересобрать и сохранить все связанные `Order`-ы. Это **катастрофа** для производительности и сложности.

3. **Замкнутость для расширения (OCP):**
   Чтобы добавить новую бизнес-функцию (например, «подарочная упаковка»), вы меняете огромный класс `Order`, который уже содержит 10 других сущностей.

4. **Конкуренция (Race Conditions):**
   Если два заказа одновременно пытаются обновить один и тот же `Inventory`, вы получите конфликты версий или блокировки.

### Как правильно?

**Агрегаты должны быть маленькими.**

- `Order` содержит только `OrderItem`-ы.
- `Inventory` — отдельный агрегат.
- `Customer` — отдельный агрегат.
- `Payment` — отдельный агрегат.

**Взаимодействие через события:**

```
Order (сохранен) 
    → Event: OrderPlaced 
        → InventoryService: decrementStock()
        → PaymentService: processPayment()
        → CustomerService: addPoints()
```

Если `InventoryService` не смог уменьшить запас (товара нет) — он может отменить заказ (через событие `OrderCancelled`).

---

## Как определить правильные границы?

### Чек-лист:

1. **Инварианты:** Какие бизнес-правила должны выполняться **всегда** внутри этого агрегата?
2. **Транзакции:** Какие сущности должны сохраняться **одновременно** (атомарно)?
3. **Независимость:** Может ли сущность существовать без агрегата? Если да — она не должна быть внутри.
4. **Изменения:** Как часто меняются сущности? Если часто — вынесите их в отдельный агрегат.
5. **Размер:** Агрегат должен быть достаточно маленьким, чтобы его можно было легко понять и протестировать.

### Правило:
**Лучше иметь слишком много маленьких агрегатов, чем один большой.**

Маленькие агрегаты:
- Легче тестировать.
- Легче масштабировать.
- Меньше риск конфликтов.
- Ясная ответственность.

Большие агрегаты:
- Сложны для понимания.
- Трудно тестировать.
- Высокий риск конфликтов.
- Плохая масштабируемость.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это классическая проблема **конкурентного доступа** (Concurrency Control). В DDD это одна из самых критичных тем, потому что агрегат — это единица целостности, и его нельзя «половина-сломать».

Рассмотрим, что произойдет при гонке (race condition), и как это проектировать правильно.

---

## 1. Что произойдет, если ничего не делать?

### Сценарий: «Потерянное обновление» (Lost Update)

**Данные:** Остаток товара на складе = 1.

**Процесс А:**
1. Читает `Inventory` (остаток = 1).
2. Вычитает 1 (остаток = 0).
3. Пишет в БД.

**Процесс Б (одновременно):**
1. Читает `Inventory` (остаток = 1). *Прочитал до того, как А записал.*
2. Вычитает 1 (остаток = 0).
3. Пишет в БД.

**Результат:**
- В БД лежит остаток `0`.
- Но фактически было совершено **2** продажи.
- **Остаток должен быть `-1`, но мы продали товар, которого нет.**

Это **нарушение инварианта** агрегата («остаток не может быть отрицательным»).

### Почему ORM (Doctrine) это не решает?
Doctrine (и любой ORM) работает по принципу **Unit of Work**:
1. Вы читаете сущность → она кэшируется в памяти.
2. Вы меняете сущность в памяти.
3. Вы вызываете `flush()`.
4. ORM генерирует `UPDATE` на основе изменений в памяти.

Если два процесса делают это одновременно, второй `UPDATE` **перетирает** первый. ORM по умолчанию **не знает**, что данные изменились под вами.

---

## 2. Решения

### Вариант 1: Оптимистичная блокировка (Optimistic Locking) — **Рекомендуемый**

Это стандартный подход в DDD. Мы **доверяем**, что конфликты редки, и проверяем их в момент записи.

#### Как работает:
1. В таблице есть колонка `version` (версия).
2. При чтении сущности мы запоминаем её `version`.
3. При записи (`UPDATE`) мы добавляем условие: `WHERE id = X AND version = Y`.
4. Если `version` в БД уже изменился (кто-то записал раньше) — условие не выполнится, строка не обновится.
5. Мы получаем ошибку и **откатываем транзакцию**.

#### Реализация в Doctrine:

```php
#[ORM\Entity]
class Inventory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    #[ORM\Version] // Магия Doctrine
    private ?int $version = null;

    public function decrementStock(int $quantity): void
    {
        if ($this->stock < $quantity) {
            throw new \DomainException('Not enough stock');
        }
        $this->stock -= $quantity;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }
}
```

**Что делает `#[ORM\Version]`:**
1. При `INSERT` устанавливает `version = 1`.
2. При `UPDATE` increment-ит `version` и добавляет `WHERE version = old_version`.
3. Если обновилось 0 строк — выбрасывает `OptimisticLockException`.

#### Обработка в UseCase:

```php
class BuyProductUseCase
{
    public function execute(int $productId, int $quantity): void
    {
        // 1. Начинаем транзакцию
        $em->beginTransaction();
        
        try {
            // 2. Загружаем агрегат (version будет прочитан)
            $inventory = $em->getRepository(Inventory::class)->find($productId);
            
            if ($inventory === null) {
                throw new \DomainException('Product not found');
            }

            // 3. Изменяем состояние
            $inventory->decrementStock($quantity);

            // 4. Флаш (сравнение версий происходит здесь)
            $em->flush(); // Если version не совпал → исключение

            $em->commit();
        } catch (OptimisticLockException $e) {
            $em->rollBack();
            
            // 5. Обработка конфликта
            // Варианты:
            // a) Повторить попытку (Retry)
            // b) Вернуть пользователю ошибку "Пожалуйста, попробуйте снова"
            
            throw new \RuntimeException('Conflict: Another user modified stock. Try again.', 409);
        } catch (\DomainException $e) {
            $em->rollBack();
            throw $e;
        }
    }
}
```

**Плюсы:**
- Нет блокировок на чтение (хорошая производительность).
- Конфликты решаются на уровне приложения.
- Инвариант гарантирован.

**Минусы:**
- При высоком уровне конкуренции много конфликтов и ретраев.

---

### Вариант 2: Пессимистичная блокировка (Pessimistic Locking)

Мы **блокируем** строку в БД на время выполнения транзакции.

#### Как работает:
1. `SELECT ... FOR UPDATE`.
2. Другой процесс не сможет прочитать эту строку (в режиме блокировки), пока первый не завершит транзакцию.

#### Реализация в Doctrine:

```php
// В репозитории
public function findForUpdate(int $id): ?Inventory
{
    return $this->createQueryBuilder('i')
        ->where('i.id = :id')
        ->setParameter('id', $id)
        ->setLockMode(LockMode::OPTIMISTIC) // Или PESSIMISTIC_WRITE
        ->getQuery()
        ->getSingleResult();
}
```

**Важно:** `PessimisticLock` работает только внутри **открытой транзакции**.

```php
class BuyProductUseCase
{
    public function execute(int $productId, int $quantity): void
    {
        $em->beginTransaction();
        
        try {
            // Блокирует строку в БД
            $inventory = $this->repo->findForUpdate($productId);
            
            $inventory->decrementStock($quantity);
            
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollBack();
            throw $e;
        }
    }
}
```

**Плюсы:**
- Гарантирует, что конфликт не возникнет (второй процесс просто будет ждать).
- Проще в разработке.

**Минусы:**
- **Низкая производительность:** Второй процесс ждет, пока первый завершит.
- **Deadlock risk:** Если два процесса блокируют разные строки в разных порядках — deadlock.
- **Сложнее отлаживать:** Таймауты блокировок.

---

### Вариант 3: Атомарное SQL-обновление (Bypassing ORM)

Если агрегат простой (например, только `stock`), можно обновить его напрямую через SQL, минуя ORM.

```sql
UPDATE inventory 
SET stock = stock - 1 
WHERE product_id = 5 AND stock >= 1;
```

**Как проверить, что обновление произошло:**
```php
$result = $em->createQuery('UPDATE App\Entity\Inventory i SET i.stock = i.stock - :qty WHERE i.id = :id AND i.stock >= :qty')
    ->setParameter('qty', $quantity)
    ->setParameter('id', $productId)
    ->executeUpdate();

if ($result === 0) {
    throw new \DomainException('Not enough stock');
}
```

**Плюсы:**
- Максимальная производительность.
- Атомарность на уровне БД.
- Нет конфликтов версий.

**Минусы:**
- **Нарушение инкапсуляции:** Мы обновляем сущность напрямую, не вызывая доменные методы.
- **Нельзя использовать сложную бизнес-логику** (например, проверка лимитов, скидок) в этом же обновлении.
- **Несовместимо со сложными агрегатами**, где нужно менять несколько полей с проверками.

**Когда использовать:**
- Для простых счетчиков (инвентарь, счетчики).
- Когда производительность критична.

---

### Вариант 4: Event Sourcing + CQRS (Продвинутый)

Вместо того чтобы читать-писать агрегат, мы **записываем события**.

1. `InventoryDecrementEvent` сохраняется в Event Store.
2. Состояние агрегата вычисляется на лету (Projection).
3. Конфликты решаются через **Ordering** (последовательность событий).

**Плюсы:**
- Полная история.
- Нет lost updates.
- Масштабируемость.

**Минусы:**
- Сложность архитектуры.
- Сложнее чтение (нужны проекции).
- Не всегда оправдано для простых CRUD-систем.

---

## Сравнение подходов

| Подход | Производительность | Сложность | Гарантия целостности | Когда использовать |
| :--- | :--- | :--- | :--- | :--- |
| **Optimistic Lock** | Высокая (при редких конфликтах) | Средняя | Высокая | **Стандарт для большинства случаев** |
| **Pessimistic Lock** | Низкая (блокировки) | Низкая | Высокая | Высокая конкуренция, короткие транзакции |
| **SQL Update** | Очень высокая | Низкая | Средняя (только для простых правил) | Счетчики, инвентарь |
| **Event Sourcing** | Высокая (запись), Низкая (чтение) | Очень высокая | Очень высокая | Сложные домены, нужна история |

---

## Рекомендация

### Для большинства PHP/Symfony приложений:

1. **Используйте `#[ORM\Version]` (Optimistic Locking).**
   - Это стандартный подход.
   - Легко реализовать.
   - Защищает от lost updates.
   - При конфликте — возвращает ошибку 409 (Conflict) пользователю или делает ретрау.

2. **Используйте SQL Update (Bypassing ORM) для простых счетчиков.**
   - Если агрегат — это просто `stock`, `balance`, `count` — обновляйте через `UPDATE ... WHERE`.
   - Это быстрее и проще.

3. **Избегайте Pessimistic Locking**, если:
   - Транзакции долгие.
   - Конкуренция средняя/низкая.
   - Есть риск deadlock.

---

## Типовые ошибки

1. **Игнорирование конфликтов.**
   ```php
   // ❌ ПЛОХО
   $inventory = $repo->find($id);
   $inventory->decrementStock(1);
   $em->flush(); // Могло быть потеряно обновление!
   ```

2. **Использование Pessimistic Lock без транзакции.**
   ```php
   // ❌ ПЛОХО
   // Блокировка не работает, если нет beginTransaction()
   $inventory = $repo->findForUpdate($id);
   ```

3. **Слишком долгие транзакции.**
   ```php
   // ❌ ПЛОХО
   $em->beginTransaction();
   $inventory = $repo->findForUpdate($id);
   
   // Вызов внешнего API (может занять 10 секунд!)
   $response = $httpClient->get('https://payment-gateway/charge');
   
   $inventory->decrementStock(1);
   $em->flush();
   $em->commit();
   // Другие процессы ждали 10 секунд!
   ```

4. **Нарушение инкапсуляции через SQL.**
   ```php
   // ❌ ПЛОХО (если агрегат сложный)
   // Обновляем только stock, но забываем про status, created_at и т.д.
   UPDATE inventory SET stock = stock - 1 WHERE id = 5;
   ```

---

## Итог

**Конкурентный доступ к агрегату** — это не баг, а фича, которую нужно проектировать.

1. **Определите уровень конкуренции.** Если низкий — `Optimistic Lock`.
2. **Определите сложность агрегата.** Если простой — `SQL Update`. Если сложный — `Optimistic Lock` + ретрау.
3. **Обработайте конфликты.** Верните ошибку пользователю или повторите попытку.
4. **Не держите блокировки долго.** Транзакции должны быть короткими.

**Помните:** Инварианты агрегата — святое. Никогда не нарушайте их ради производительности. Лучше показать пользователю «Пожалуйста, попробуйте снова», чем продать товар, которого нет.

> ⏱ 18.7s · prompt 38333 tok · output 2541 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это механизм **асинхронного взаимодействия** внутри домена и между микросервисами. Они позволяют разделить бизнес-логику на независимые модули, не нарушая инкапсуляцию агрегатов.

### Ключевая идея:
Агрегат не знает, кто слушает его события. Он просто **фиксирует факт** («Заказ оплачен», «Пользователь зарегистрирован»). Другие части системы реагируют на эти факты.

---

## 1. Когда публикуешь доменные события?

### Правильное время:
Событие публикуется **внутри** агрегата, когда изменяется его состояние.

```php
class Order
{
    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Order is not pending');
        }

        $this->status = OrderStatus::PAID;
        
        // Событие фиксирует факт, а не действие
        $this->record(new OrderPaidEvent($this->id));
    }
}
```

### Неверное время:
❌ В контроллере (слишком поздно, нарушает инкапсуляцию).
❌ В репозитории (инфраструктура не должна знать о домене).
❌ В UseCase **после** сохранения (может быть потеряно при ошибке).

---

## 2. Как обрабатываешь?

### Архитектура:

```
[Aggregate] 
    → Record Event 
        → UseCase (после flush) 
            → Event Dispatcher (Messenger)
                → [Queue] 
                    → [Worker] 
                        → [Handler]
```

### Реализация:

**1. Сбор событий из агрегата:**

```php
class Order
{
    private array $domainEvents = [];

    public function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return object[] */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = []; // Очищаем, чтобы не отправлять повторно
        return $events;
    }
}
```

**2. Публикация в UseCase:**

```php
class CreateOrderUseCase
{
    public function execute(string $email, array $items): Order
    {
        $order = new Order(uniqid(), $email);
        foreach ($items as $item) {
            $order->addItem($item['productId'], $item['qty'], $item['price']);
        }

        // Сохранение в БД
        $this->orderRepo->save($order);
        
        // Публикация событий (ПОСЛЕ сохранения!)
        $events = $order->releaseEvents();
        foreach ($events as $event) {
            $this->bus->dispatch($event);
        }

        return $order;
    }
}
```

**3. Обработчики (Handlers):**

```php
class SendConfirmationEmailHandler
{
    #[AsMessageHandler]
    public function __invoke(OrderPaidEvent $event): void
    {
        $this->mailer->send($event->orderId, 'Payment received');
    }
}

class UpdateInventoryHandler
{
    #[AsMessageHandler]
    public function __invoke(OrderPaidEvent $event): void
    {
        // Уменьшаем инвентарь
        $this->inventoryRepo->decrement($event->items);
    }
}
```

---

## 3. Какие проблемы решает этот подход?

### 1. Разделение ответственностей (SRP)
`Order` не знает, что нужно отправить email. Он только фиксирует факт «Оплачен». Отправка email — задача другого модуля.

### 2. Расширяемость (OCP)
Чтобы добавить новую реакцию (например, «Начислить бонусы»), вы создаете новый `Handler`. Не меняете код `Order` или `UseCase`.

### 3. Асинхронность
Пользователь получает ответ мгновенно. Email уходит в фоне.

### 4. Инкапсуляция агрегатов
Внешние сервисы не могут напрямую менять состояние агрегата. Они реагируют на события.

### 5. Распределенные системы
События легко сериализуются (JSON) и могут передаваться между микросервисами через Kafka/RabbitMQ.

---

## 4. Какие проблемы создаёт?

### 1. Сложность отладки
Трудно отследить, почему email не отправился. Нужно смотреть логи воркера, очередь, обработчик.

### 2. Потеря событий
Если воркер упадет до обработки — событие потеряно (если нет ретраев).

### 3. Двойная запись (Double Write Problem)
См. ниже про транзакции.

### 4. Нелинейное время
События могут обрабатываться в разном порядке. Нужно учитывать идемпотентность.

### 5. Сложность тестирования
Нужно мокать `EventDispatcher`, чтобы тестировать `UseCase`.

---

## 5. Связь с транзакциями БД (Критично!)

### Проблема: «Двойная запись» (Double Write Problem)

```php
// ❌ ПЛОХО
$orderRepo->save($order); // 1. Запись в БД
$bus->dispatch($event);   // 2. Запись в очередь (Redis/RabbitMQ)

// Что если шаг 1 успешен, а шаг 2 упал?
// Событие потеряно. Система в невалидном состоянии.
```

### Решение: Outbox Pattern (Очередь выхода)

Это стандартный паттерн для обеспечения **consistency** при асинхронной обработке.

#### Как работает:
1. Событие сохраняется в таблицу БД **в той же транзакции**, что и изменения агрегата.
2. Отдельный процесс (Poller) периодически читает эту таблицу и отправляет события в очередь (RabbitMQ/Kafka).
3. После успешной отправки запись удаляется из таблицы Outbox.

#### Реализация:

**1. Таблица Outbox:**
```sql
CREATE TABLE outbox (
    id SERIAL PRIMARY KEY,
    event_type VARCHAR(255),
    payload JSONB,
    created_at TIMESTAMP DEFAULT NOW(),
    published_at TIMESTAMP NULL
);
```

**2. Сохранение события в Outbox (в UseCase):**

```php
class CreateOrderUseCase
{
    public function execute(string $email, array $items): Order
    {
        $order = new Order(uniqid(), $email);
        // ... логика ...

        $events = $order->releaseEvents();

        // Начинаем транзакцию
        $this->em->beginTransaction();
        
        try {
            // 1. Сохраняем агрегат
            $this->em->persist($order);
            
            // 2. Сохраняем события в Outbox
            foreach ($events as $event) {
                $outboxEntry = new OutboxEntry($event);
                $this->em->persist($outboxEntry);
            }
            
            // 3. Коммит (оба изменения атомарны!)
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollBack();
            throw $e;
        }

        return $order;
    }
}
```

**3. Poller (отдельный процесс):**

```php
class OutboxPoller
{
    public function run(): void
    {
        while (true) {
            // 1. Ищем непубликованные события
            $entries = $this->repo->findUnpublished(100);
            
            foreach ($entries as $entry) {
                try {
                    // 2. Отправляем в очередь (RabbitMQ)
                    $this->bus->dispatch($entry->getEvent());
                    
                    // 3. Помечаем как опубликованное
                    $entry->markAsPublished();
                    $this->em->flush();
                } catch (\Exception $e) {
                    // Ошибка — оставим в Outbox, попробуем позже
                    log::error('Failed to publish event', ['id' => $entry->getId()]);
                }
            }
            
            sleep(1); // Пауза между итерациями
        }
    }
}
```

### Почему это работает:
1. **Атомарность:** Агрегат и событие сохраняются вместе. Если одно падает — падает другое.
2. **Доставка гарантирована:** Poller будет пытаться отправить событие до тех пор, пока не получится.
3. **Идемпотентность:** Обработчики должны быть идемпотентными (обработка одного и того же события дважды не меняет состояние).

---

## 6. Идемпотентность

Поскольку сообщения могут дублироваться (ретраи), обработчики должны быть идемпотентными.

### Пример:
```php
class UpdateInventoryHandler
{
    public function __invoke(OrderPaidEvent $event): void
    {
        // Проверяем, не обработано ли уже это событие
        $processed = $this->processedEventsRepo->exists($event->eventId);
        if ($processed) {
            return; // Уже обработали
        }

        // Логика
        $this->inventoryRepo->decrement($event->items);

        // Фиксируем обработку
        $this->processedEventsRepo->save(new ProcessedEvent($event->eventId));
    }
}
```

---

## 7. Итог

| Аспект | Решение |
| :--- | :--- |
| **Когда публиковать?** | Внутри агрегата, при изменении состояния. |
| **Когда доставлять?** | После сохранения агрегата в БД. |
| **Гарантия целостности?** | Outbox Pattern (событие в БД вместе с агрегатом). |
| **Обработка?** | Отдельные Handlers (воркеры). |
| **Дубликаты?** | Идемпотентные обработчики. |

### Ключевые правила:
1. **События фиксируют факты**, а не действия.
2. **События сохраняются в БД** вместе с агрегатом (Outbox).
3. **Обработчики идемпотентны.**
4. **Не блокируйте основной поток** на обработке событий.
5. **Логируйте** все события для отладки.

> ⏱ 18.4s · prompt 44010 tok · output 2433 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальные вопросы DDD, на которые часто отвечают формально («Entity имеет ID, VO — нет»), но на практике границы размыты. Давайте разберем глубоко.

---

## 1. Value Object vs Entity

### Главное различие: Идентичность (Identity)

| Критерий | **Entity (Сущность)** | **Value Object (Объект значения)** |
| :--- | :--- | :--- |
| **Идентичность** | Определяется **ID** (уникальный ключ). | Определяется **значением** (свойствами). |
| **Равенство** | Два объекта равны, если имеют **один и тот же ID**. | Два объекта равны, если имеют **одинаковые свойства**. |
| **Изменчивость** | **Мутибельны** (состояние меняется во времени). | **Неизменяемы** (Immutability). Новый объект = новое значение. |
| **Жизненный цикл** | Долговременный (создан → обновлен → удален). | Короткий (создан → использован → отброшен). |
| **Ссылки** | Другие сущности ссылаются на неё **по ID**. | Копируется по значению (или передается ссылка, но не важна). |

### Пример: `Money` (Деньги)

```php
// ❌ Если бы это была Entity
class Money
{
    public int $id; // Ненужное ID!
    public int $amount;
    public string $currency;
}

// ✅ Это Value Object
final class Money
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency
    ) {}

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
```

**Почему `Money` — это VO?**
- `Money(100, 'USD')` и `Money(100, 'USD')` — это **одни и те же деньги**. Не важно, кто их создал и когда.
- Нет смысла иметь `id` у 100 долларов.
- Неизменяемость: `100 USD` + `50 USD` = `150 USD` (новый объект).

### Пример: `User` (Пользователь)

```php
class User
{
    public string $id; // Критически важно!
    public string $email;
    public string $passwordHash;
    
    public function changeEmail(string $newEmail): void
    {
        // Меняем состояние. User с тем же ID, но новым email.
        $this->email = $newEmail;
    }
}
```

**Почему `User` — это Entity?**
- Если я изменю email, `User(id: '1', email: 'new@mail')` — это **тот же пользователь**, что и `User(id: '1', email: 'old@mail')`.
- Если бы это был VO, изменение email создало бы **нового** пользователя. Но мы хотим обновить старую запись.
- Ссылки в БД: `orders.user_id` указывает на `users.id`.

---

## 2. Как решать, Entity или VO?

Используйте этот чек-лист:

### Шаг 1: Есть ли уникальная идентичность?
- **Да** (например, `id`, `guid`) → **Entity**.
- **Нет** (важно только значение) → **VO**.

### Шаг 2: Нужно ли отслеживать историю изменений?
- **Да** (кто, когда, что изменил) → **Entity**.
- **Нет** (результат важнее истории) → **VO**.

### Шаг 3: Меняется ли объект во времени?
- **Да** (статус, сумма, список предметов) → **Entity**.
- **Нет** (координаты, деньги, email) → **VO**.

### Шаг 4: Ссылается ли на него по ID?
- **Да** (foreign key) → **Entity**.
- **Нет** (хранится как значение) → **VO**.

### Сложные случаи:

#### `Address` (Адрес)
- Обычно **VO**, если адрес просто хранится.
- Но если адрес имеет свой ID, историю изменений, ассоциирован с пользователем по FK — может быть **Entity**.
- **Вывод:** Часто делают VO для простоты.

#### `OrderItem` (Позиция заказа)
- Обычно **Entity**, если имеет ID и принадлежит `Order` по FK.
- Но если позиция не имеет ID и просто описывает «что куплено» — это **VO**.
- **Вывод:** В Doctrine часто делают Entity для удобства маппинга, но логически это VO.

#### `UserAddress` (Адрес пользователя)
- Если `User` имеет несколько адресов, и они имеют ID — **Entity**.
- Если адрес — просто данные — **VO**.

---

## 3. Ubiquitous Language (Единый язык)

### Что это?
Ubiquitous Language — это **совокупность терминов**, которые одинаково используются в коде, в базах данных, в UI и в разговорах с бизнесом.

**Цель:** Устранить разночтения между разработчиками, аналитиками и бизнесом.

### Примеры:

| Бизнес-термин | Как может быть в коде (ПЛОХО) | Как должен быть в коде (ХОРОШО) |
| :--- | :--- | :--- |
| Заказ | `Order` | `Order` |
| Позиция заказа | `Item`, `ProductEntry`, `Line` | `OrderItem` |
| Оплата | `Pay`, `Money`, `Charge` | `Payment` |
| Клиент | `Client`, `Customer`, `User` | `Customer` (если это B2B) или `User` (если B2C) |
| Сумма | `Value`, `Amount`, `Price` | `Money` (с валютой) |

### Как это влияет на код?

#### 1. Имена классов и методов
```php
// ❌ Плохо
class Client
{
    public function buy(Items $items)
}

// ✅ Хорошо
class Customer
{
    public function placeOrder(Order $order)
}
```

#### 2. Имена полей в БД
```sql
-- ❌ Плохо
CREATE TABLE cust_tbl (
    clnt_id INT,
    item_cnt INT
);

-- ✅ Хорошо
CREATE TABLE customers (
    id UUID,
    order_count INT
);
```

#### 3. Имена методов
```php
// ❌ Плохо
$user->changePass();

// ✅ Хорошо
$user->updateCredentials();
// Или лучше, если есть отдельная сущность:
$credentials->changePassword();
```

### Практика внедрения Ubiquitous Language:

1. **Создайте глоссарий.**
   - Поговорите с бизнесом.
   - Определите ключевые понятия: `Customer`, `Order`, `Product`, `Payment`.
   - Зафиксируйте их в README или Wiki.

2. **Используйте их в коде.**
   - Классы: `Order`, `Customer`.
   - Методы: `placeOrder()`, `cancelOrder()`.
   - Переменные: `$orderId`, `$customerName`.

3. **Используйте их в БД.**
   - Таблицы: `orders`, `customers`.
   - Колонки: `order_id`, `customer_id`.

4. **Используйте их в API.**
   - Эндпоинты: `POST /orders`, `GET /customers/{id}`.
   - Поля в JSON: `orderId`, `customerName`.

5. **Проверяйте на code review.**
   - Если видите `clnt` или `cust` — запрашивайте переименование.

### Почему это важно?

1. **Снижает когнитивную нагрузку.**
   - Новичок читает код и понимает, что происходит.

2. **Предотвращает ошибки.**
   - `client` vs `customer` — разные понятия. В B2B `customer` может быть юрлицо, а `client` — контактное лицо.

3. **Упрощает коммуникацию.**
   - Разработчик может говорить с аналитиком на одном языке.

4. **Масштабируемость.**
   - Когда команда растет, единый язык становится стандартом.

---

## Итог

| Вопрос | Ответ |
| :--- | :--- |
| **Entity vs VO** | Entity — идентичность + история. VO — значение + неизменяемость. |
| **Как решать?** | Есть ID? Нужна история? Ссылки по FK? Если да — Entity. |
| **Ubiquitous Language** | Единый набор терминов в коде, БД, UI и бизнесе. |
| **Зачем?** | Читаемость, отсутствие разночтений, масштабируемость. |

**Помните:** DDD — это не про паттерны, а про **понимание бизнеса**. Если вы правильно моделируете бизнес-концепции (Entity/VO) и используете правильный язык (Ubiquitous Language), код становится самодокументирующим.

> ⏱ 18.1s · prompt 46511 tok · output 2182 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Чистая архитектура (Clean Architecture) в PHP — это не про строгое разделение файлов по папкам `Model/View/Controller`, а про **правило направленности зависимостей** и **абстракцию**.

### Правило Направленности Зависимостей (Dependency Rule)
Зависимости могут указывать **только внутрь** (к домену). Внешний код может зависеть от внутреннего, но не наоборот.

```
[Framework/Drivers] → [Interface Adapters] → [Application] → [Domain]
   (Самый внешний)                                        (Самый внутренний)
```

**Домен** не знает о Symfony, Doctrine, Redis, MySQL или RabbitMQ.

---

## 1. Структура слоёв

### 1. Domain Layer (Ядро)
**Что это:** Чистый PHP. Без фреймворков, без ORM, без библиотек.
**Содержимое:**
- **Entities**: `User`, `Order`, `Product`.
- **Value Objects**: `Money`, `Email`, `Address`.
- **Domain Services**: `OrderValidationService` (если логика не принадлежит сущности).
- **Repository Interfaces**: `OrderRepositoryInterface` (контракт, а не реализация).
- **Domain Events**: `OrderPaidEvent`.

**Пример:**
```php
// src/Domain/Order/Order.php
namespace App\Domain\Order;

use App\Domain\Money\Money;
use App\Domain\Order\Event\OrderPaidEvent;

class Order
{
    private array $domainEvents = [];

    public function pay(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Order is not pending');
        }
        $this->status = OrderStatus::PAID;
        $this->record(new OrderPaidEvent($this->id));
    }

    private function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

**Зависимости:** НЕТ. Чистый PHP.

---

### 2. Application Layer (Приложение)
**Что это:** Оркестрация. Связывает домен с внешним миром.
**Содержимое:**
- **Use Cases / Command Handlers**: `CreateOrderUseCase`, `PayOrderUseCase`.
- **DTOs (Data Transfer Objects)**: `CreateOrderCommand`, `OrderDto`.
- **Interactors**: Если вы используете hexagonal architecture.

**Зависимости:** Зависит от **Domain** (interfaces). НЕ зависит от Frameworks.

**Пример:**
```php
// src/Application/Order/CreateOrderUseCase.php
namespace App\Application\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface; // Интерфейс из Domain!

class CreateOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MessageBusInterface $messageBus // Абстракция от конкретного брокера
    ) {}

    public function execute(CreateOrderCommand $command): Order
    {
        $order = new Order($command->customerId, $command->items);
        $this->orderRepository->save($order);
        
        foreach ($order->releaseEvents() as $event) {
            $this->messageBus->dispatch($event);
        }
        
        return $order;
    }
}
```

**Важно:** Здесь мы используем интерфейсы (`OrderRepositoryInterface`, `MessageBusInterface`), а не конкретные классы Doctrine или Symfony Messenger.

---

### 3. Interface Adapters Layer (Адаптеры)
**Что это:** Реализация интерфейсов из Domain/Application. Адаптация внешнего мира под внутренние нужды.
**Содержимое:**
- **Repositories**: `DoctrineOrderRepository`.
- **Controllers**: `OrderController` (Symfony).
- **Presenters/Transformers**: `OrderDtoTransformer` (преобразование Entity в DTO).
- **Form Handlers**: `CreateOrderFormHandler`.
- **Event Handlers**: `SendEmailEventHandler` (если он использует внешние сервисы).

**Зависимости:** Зависит от **Application** и **Domain**.

**Пример Repository:**
```php
// src/Adapter/Persistence/Doctrine/OrderRepository.php
namespace App\Adapter\Persistence\Doctrine;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }
}
```

**Пример Controller:**
```php
// src/Adapter/Http/Controller/OrderController.php
namespace App\Adapter\Http\Controller;

use App\Application\Order\CreateOrderUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends AbstractController
{
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase
    ) {}

    public function create(CreateOrderCommand $command): JsonResponse
    {
        $order = $this->createOrderUseCase->execute($command);
        return $this->json(['id' => $order->getId()]);
    }
}
```

---

### 4. Frameworks & Drivers (Внешний мир)
**Что это:** Конкретные реализации фреймворков и библиотек.
**Содержимое:**
- **Symfony**: Controllers, Forms, Messenger.
- **Doctrine**: Entities (с маппингом), Repositories, Types.
- **Queue Drivers**: RabbitMQ, Redis.
- **Database**: MySQL, PostgreSQL.

**Зависимости:** Зависит от **Interface Adapters**.

---

## 2. Куда попадают конкретные компоненты?

| Компонент | Слой | Почему? |
| :--- | :--- | :--- |
| **Doctrine Entity** | Interface Adapters (или Domain, если нет маппинга) | Содержит аннотации `#[ORM\Entity]`. Это привязка к ORM. |
| **Symfony Controller** | Interface Adapters | Привязан к HTTP. Использует `Request`, `Response`. |
| **Message Handler (Queue)** | Interface Adapters | Использует `MessageBus` (Symfony Messenger) или конкретный брокер. |
| **Domain Entity** | Domain | Чистый PHP. Без маппинга. |
| **Repository Interface** | Domain | Контракт. |
| **Repository Implementation** | Interface Adapters | Реализация через Doctrine. |

---

## 3. Проблема: Разделение Domain и Doctrine

В PHP часто возникает дилемма: **Одна сущность или две?**

### Вариант A: Одна сущность (Pragmatic)
```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    private string $id;
    
    // Бизнес-методы
    public function pay(): void { /* ... */ }
}
```
**Плюсы:** Простота. Один класс.
**Минусы:** Domain зависит от Doctrine (аннотации). Сложнее тестировать без моков. Нарушает чистую архитектуру.

### Вариант B: Две сущности (Strict Clean)
1. `App\Domain\Order\Order` (чистый PHP).
2. `App\Adapter\Persistence\Doctrine\OrderEntity` (с маппингом).
3. `OrderMapper` (преобразует Domain ↔ Doctrine).

**Плюсы:** Полная изоляция домена. Легко тестировать.
**Минусы:** Дублирование кода. Сложность. Необходимость маппинга.

**Рекомендация:** Для большинства PHP-приложений **Вариант A** достаточно хорош, если вы не планируете часто менять ORM. Для критичных систем — **Вариант B**.

---

## 4. Как следить, что границы не нарушаются?

### 1. Статический анализ (PHPStan / Psalm)
Настройте правила, которые запрещают импорт внешних классов в Domain.

**Пример правила для PHPStan:**
```php
// rules.php
return [
    'no_direct_imports_in_domain' => [
        'patterns' => [
            'App\\Domain\\*' => ['!App\\Domain\\*'] // Запрет импорта из других слоёв
        ]
    ]
];
```

### 2. ArchUnit / PHPArchTest
Используйте инструменты для проверки архитектурных ограничений.

**Пример с `phpunit-archtest`:**
```php
public function testDomainDoesNotDependOnSymfony(): void
{
    $this->architecture()
        ->in('src/Domain')
        ->shouldNotDependOn('Symfony\\');
}
```

### 3. Code Review
- Если в `Domain` классе появился `use Symfony\...` — **откажите**.
- Если в `Domain` классе появился `use Doctrine\...` — **обсудите** (допустимо только для маппинга в pragmatic-подходе).

### 4. Структура папок
```
src/
├── Domain/
│   ├── Order/
│   │   ├── Order.php
│   │   ├── OrderRepositoryInterface.php
│   │   └── Event/
│   ├── Money/
│   │   └── Money.php
├── Application/
│   ├── Order/
│   │   ├── CreateOrderUseCase.php
│   │   └── CreateOrderCommand.php
├── Adapter/
│   ├── Http/
│   │   └── OrderController.php
│   ├── Persistence/
│   │   └── Doctrine/
│   │       └── OrderRepository.php
│   └── Messaging/
│       └── OrderPaidHandler.php
```

**Правило:** Папки `Domain` и `Application` не должны содержать файлов с логикой, которая зависит от внешних библиотек.

---

## Итог

| Слой | Зависит от | Примеры |
| :--- | :--- | :--- |
| **Domain** | Никого | Entities, VOs, Interfaces |
| **Application** | Domain | UseCases, DTOs |
| **Adapters** | Application, Domain | Controllers, Repositories, Handlers |
| **Frameworks** | Adapters | Symfony, Doctrine, MySQL |

**Ключевой принцип:** Домен — это сердце системы. Он должен быть независимым, тестируемым и понятным бизнесу. Все остальное — это инфраструктура, которая может быть заменена.

> ⏱ 17.9s · prompt 48766 tok · output 2522 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Стратегия тестирования для Symfony + PostgreSQL строится на **практичной пирамиде**, где фокус смещен в пользу **Unit-тестов домена** и **Integration-тестов критических сценариев**, а E2E-тестов минимум.

### 1. Пирамида тестов (адаптация для PHP/Symfony)

| Тип | Количество | Скорость | Что тестируем | Инструменты |
| :--- | :--- | :--- | :--- | :--- |
| **Unit** | 70-80% | Очень быстрая | Логика домена, VO, Services | PHPUnit |
| **Integration** | 15-20% | Средняя | Взаимодействие с БД, ORM, Messenger | PHPUnit + Doctrine TestEnv |
| **Functional/E2E** | 5-10% | Медленная | Полный сценарий (HTTP → БД) | Symfony BrowserKit / Panther |

---

## 2. Типы тестов и их цели

### A. Unit Tests (Юнит-тесты)

**Цель:** Проверить логику **домена** и **приложения** в изоляции.

**Что тестируем:**
1. **Value Objects:** Валидация, операции (`Money->add()`, `Email->isValid()`).
2. **Entities/Aggregates:** Бизнес-правила (`Order->pay()`), инварианты.
3. **Domain Services:** Сложная логика, которая не принадлежит сущности.
4. **Application Use Cases:** Оркестрацию, если зависимости замотканы.

**Что мокаем:**
- **Repository Interfaces:** Мы моким `OrderRepositoryInterface`, а не Doctrine.
- **Event Dispatcher:** Моким, чтобы проверить, что событие опубликовано.
- **External APIs:** Моким клиенты (Stripe, SendGrid).

**Что НЕ мокаем:**
- **Сам домен:** Никогда не моким сущности или VO. Они должны работать как есть.
- **Простые getters/setters:** Нет смысла.

**Пример:**
```php
class OrderTest extends TestCase
{
    public function testCannotPayCompletedOrder(): void
    {
        // Arrange
        $order = new Order('id-1', 'cust-1');
        $order->markAsCompleted(); // Предположим, есть такой метод

        // Act & Assert
        $this->expectException(DomainException::class);
        $order->pay();
    }

    public function testPayPublishesEvent(): void
    {
        $order = new Order('id-1', 'cust-1');
        
        $order->pay();
        
        $events = $order->releaseEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(OrderPaidEvent::class, $events[0]);
    }
}
```

---

### B. Integration Tests (Интеграционные)

**Цель:** Проверить, что **домен** и **инфраструктура** работают вместе.

**Что тестируем:**
1. **Repositories:** CRUD-операции через Doctrine.
2. **Use Cases:** Полный сценарий «Команда → БД → Результат».
3. **Event Handlers:** Логика обработки событий (если они используют БД).
4. **Form Processing:** Валидация форм Symfony.

**Что мокаем:**
- **Ничего внутри сценария.** Мы используем **реальную БД** (test-схему) и **реальную доменную логику**.
- **External Services:** Если сценарий использует Stripe, мы моким `StripeClient` (через DI-контейнер в тесте).

**Что НЕ мокаем:**
- **Doctrine/PostgreSQL:** Мы тестируем **реальное** взаимодействие с БД. Это критично для проверки маппинга, транзакций, индексов.
- **Doctrine EntityManager:** Используем реальный EM с test-конфигурацией.

**Пример:**
```php
class OrderRepositoryTest extends WebTestCase
{
    public function testSaveAndRetrieveOrder(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
        $repo = $em->getRepository(Order::class);

        // Arrange
        $order = new Order(uniqid(), 'customer@test.com');
        $order->addItem('prod-1', 2, new Money(100, 'USD'));

        // Act
        $em->persist($order);
        $em->flush();

        // Assert
        $foundOrder = $repo->find($order->getId());
        $this->assertNotNull($foundOrder);
        $this->assertEquals('customer@test.com', $foundOrder->getCustomerEmail());
    }
}
```

**Важно:** Для таких тестов используйте **отдельную БД** (`test` schema), а не `dev`.

---

### C. Functional/E2E Tests (Функциональные)

**Цель:** Проверить **полный сценарий** от HTTP-запроса до ответа.

**Что тестируем:**
1. **Критические бизнес-процессы:** «Регистрация → Валидация → Создание → Email».
2. **Аутентификация:** Login, Logout, JWT.
3. **API-контракты:** Status codes, JSON-структура.
4. **Интеграция фреймворка:** Router, Controller, Middleware.

**Что мокаем:**
- **External APIs:** Моким через DI-контейнер.
- **Тяжелые вычисления:** Если они не являются целью теста.

**Что НЕ мокаем:**
- **БД:** Используем реальную БД (test).
- **Доменную логику:** Она должна работать как есть.

**Пример:**
```php
class OrderApiTest extends WebTestCase
{
    public function testCreateOrder(): void
    {
        $client = static::createClient();
        
        $client->jsonRequest('POST', '/api/orders', [
            'customerEmail' => 'test@example.com',
            'items' => [
                ['productId' => 'p1', 'quantity' => 2]
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonPathSame('email', 'test@example.com');
        
        // Проверка БД
        $em = static::getContainer()->get('doctrine')->getManager();
        $this->assertEquals(1, $em->getRepository(Order::class)->count([]));
    }
}
```

---

## 3. Что мокаем, а что нет? (Правила)

| Компонент | Мокаем? | Почему? |
| :--- | :--- | :--- |
| **Domain Entities** | **НЕТ** | Это ядро. Тестируем реальное поведение. |
| **Value Objects** | **НЕТ** | Они простые, чистые, быстрые. |
| **Repository Interfaces** | **ДА** (в Unit) | Чтобы изолировать Use Case от БД. |
| **External HTTP Clients** | **ДА** | Чтобы тесты не зависели от внешних сервисов. |
| **Email/Mailer** | **ДА** | Чтобы не отправлять реальные письма. |
| **Doctrine EntityManager** | **НЕТ** (в Integration) | Мы тестируем реальное взаимодействие с БД. |
| **Symfony Request/Response** | **НЕТ** | Они простые, нет смысла мокать. |
| **Event Dispatcher** | **ДА** (в Unit) | Чтобы проверить, что событие опубликовано, без его обработки. |

---

## 4. Как поддерживать тесты быстрыми и стабильными?

### 1. Отдельная БД для тестов
- Создайте отдельную схему PostgreSQL: `CREATE SCHEMA test;`.
- Используйте переменную окружения: `DATABASE_URL=test://postgres:password@localhost:5432/myapp_test`.
- **Никогда не тестируйте против `dev` или `prod` БД.**

### 2. Транзакции для Integration-тестов
Используйте `DoctrineTestBundle` или свой trait, который оборачивает каждый тест в транзакцию:

```php
trait TransactionalTest
{
    protected function setUp(): void
    {
        self::bootKernel();
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->getConnection()->rollBack();
    }
}
```

**Плюс:** Каждый тест начинается с чистой БД. Откат быстрый (не нужно `DELETE FROM`).

### 3. Параллельное выполнение (Parallel Tests)
Используйте `paratest`:

```bash
vendor/bin/paratest --processes=4
```

Это сокращает время выполнения в 3-4 раза.

### 4. Быстрый Unit-тесты
- **Не загружайте Symfony Kernel** в Unit-тестах.
- **Не подключайтесь к БД** в Unit-тестах.
- **Используйте статические моки** (`Mockery`, `Prophecy`).

### 5. Медленные Integration-тесты — изолированно
- Отдельный прогон: `vendor/bin/phpunit --testsuite Integration`.
- Можно запустить реже (например, при изменении схемы БД или ORM-логики).

### 6. E2E-тесты — минимально
- Только для **критических** сценариев (оплата, регистрация).
- Запускайте только на CI при слиянии в `main`.
- Используйте **Docker Compose** для поднятия полной инфраструктуры (БД, Redis, RabbitMQ).

### 7. Мониторинг времени выполнения
- Настройте `--verbose` и смотрите, какие тесты медленные.
- Оптимизируйте медленные тесты:
  - Мокайте внешние вызовы.
  - Уменьшайте данные.
  - Выносите в Integration, если это Unit-тест.

---

## 5. CI/CD Пайплайн

```yaml
stages:
  - unit
  - integration
  - functional

unit:
  script:
    - composer test:unit
  parallel: 4

integration:
  services:
    - postgres:15
  script:
    - composer test:integration
  only:
    - changes
    - paths:
      - src/Domain/
      - src/Application/
      - migrations/

functional:
  services:
    - postgres:15
    - redis:7
  script:
    - composer test:functional
  only:
    - main
    - tags
```

---

## Итог

1. **Юнит-тесты** — для логики домена. Быстрые, мокаем интерфейсы.
2. **Интеграционные** — для БД и ORM. Используем реальную БД (test), транзакции для скорости.
3. **Функциональные** — для критических сценариев. Минимум, но по-настоящему.
4. **Мокать** внешние сервисы и интерфейсы. **Не мокать** домен и БД (в интеграции).
5. **Быстрые тесты** = отдельная БД, транзакции, параллельное выполнение, изоляция Unit-тестов от Kernel.

> ⏱ 21.5s · prompt 51347 tok · output 2676 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Это классическая проблема **конкурентного доступа** (Concurrency Control) к разделяемым ресурсам. В контексте DDD и базы данных речь идет о нарушении **инвариантов агрегата** при параллельной модификации.

### Основные проблемы

1. **Lost Update (Потерянное обновление)**
   - Пользователь A читает запись (значение X).
   - Пользователь B читает запись (значение X).
   - A записывает X + 1.
   - B записывает X + 1.
   - **Результат:** X + 1, хотя ожидалось X + 2. Изменение B потеряно.

2. **Dirty Read (Грязное чтение)**
   - A читает незакоммиченные изменения B.
   - Откатывает B.
   - A оперирует несуществующими данными.

3. **Deadlock (Тупик)**
   - A блокирует запись 1, ждет записи 2.
   - B блокирует запись 2, ждет записи 1.
   - Оба ждут друг друга.

4. **Нарушение бизнес-инвариантов**
   - Превышение лимита (например, снятие денег сверх баланса).
   - Нарушение уникальности (двойное бронирование одного слота).

---

## Способы защиты

### 1. Оптимистичная блокировка (Optimistic Locking)

**Принцип:** Мы **доверяем**, что конфликты редки. Проверяем целостность только в момент записи.

**Реализация:**
- В таблице есть колонка `version` (или `updated_at`).
- При чтении мы запоминаем `version`.
- При записи добавляем условие: `WHERE id = X AND version = Y`.
- Если запись уже изменилась — условие не выполнится, строка не обновится.

**Пример в Doctrine:**
```php
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $balance;

    #[ORM\Version]
    private ?int $version = null;

    public function withdraw(int $amount): void
    {
        if ($this->balance < $amount) {
            throw new \DomainException('Insufficient funds');
        }
        $this->balance -= $amount;
    }
}
```

**В UseCase:**
```php
public function withdraw(int $accountId, int $amount): void
{
    $em->beginTransaction();
    try {
        $account = $em->getRepository(Account::class)->find($accountId);
        $account->withdraw($amount);
        $em->flush(); // Здесь Doctrine проверяет version
        $em->commit();
    } catch (OptimisticLockException $e) {
        $em->rollBack();
        // Обработка: ретрау или ошибка пользователю
        throw new \RuntimeException('Conflict, try again', 409);
    }
}
```

**Плюсы:**
- Нет блокировок на чтение (высокая читаемость).
- Простая реализация.
- Конфликты решаются на уровне приложения.

**Минусы:**
- При высокой конкуренции много конфликтов и ретраев.
- Пользователь может получить ошибку 409.

---

### 2. Пессимистичная блокировка (Pessimistic Locking)

**Принцип:** Мы **не доверяем** и блокируем ресурс на время транзакции.

**Реализация:**
- `SELECT ... FOR UPDATE` (в PostgreSQL).
- Другой процесс не сможет прочитать/изменить эту запись, пока первая транзакция не завершится.

**Пример в Doctrine:**
```php
// В репозитории
public function findForUpdate(int $id): ?Account
{
    return $this->createQueryBuilder('a')
        ->where('a.id = :id')
        ->setParameter('id', $id)
        ->setLockMode(LockMode::PESSIMISTIC_WRITE)
        ->getQuery()
        ->getSingleResult();
}
```

**В UseCase:**
```php
public function withdraw(int $accountId, int $amount): void
{
    $em->beginTransaction();
    try {
        // Блокирует строку в БД
        $account = $this->repo->findForUpdate($accountId);
        
        $account->withdraw($amount);
        
        $em->flush();
        $em->commit();
    } catch (\Exception $e) {
        $em->rollBack();
        throw $e;
    }
}
```

**Плюсы:**
- Гарантирует отсутствие конфликтов.
- Простая логика (не нужно обрабатывать ретраи).

**Минусы:**
- **Низкая производительность:** Второй процесс ждет.
- **Risk of Deadlock:** Если блокировки берутся в разном порядке.
- **Сложнее отлаживать:** Таймауты блокировок.

---

### 3. Атомарные SQL-обновления (Bypassing ORM)

**Принцип:** Обновление выполняется напрямую в БД как одно атомарное действие.

**Реализация:**
```sql
UPDATE accounts 
SET balance = balance - :amount 
WHERE id = :id AND balance >= :amount;
```

**В PHP:**
```php
$result = $em->createQuery('
    UPDATE App\Entity\Account a 
    SET a.balance = a.balance - :amount 
    WHERE a.id = :id AND a.balance >= :amount
')
    ->setParameter('amount', $amount)
    ->setParameter('id', $accountId)
    ->executeUpdate();

if ($result === 0) {
    throw new \DomainException('Insufficient funds');
}
```

**Плюсы:**
- Максимальная производительность.
- Атомарность на уровне БД.
- Нет конфликтов версий.

**Минусы:**
- **Нарушение инкапсуляции:** Мы обновляем сущность напрямую, не вызывая доменные методы.
- **Сложная бизнес-логика:** Невозможно реализовать сложные правила (скидки, лимиты) в одном UPDATE.
- **Несовместимо со сложными агрегатами.**

**Когда использовать:**
- Для простых счетчиков (баланс, инвентарь).
- Когда производительность критична.

---

### 4. Event Sourcing + CQRS

**Принцип:** Вместо записи состояния мы записываем **события**.

**Реализация:**
1. `WithdrawalRequested` сохраняется в Event Store.
2. Состояние агрегата вычисляется на лету (Projection).
3. Конфликты решаются через **Ordering** (последовательность событий).

**Плюсы:**
- Полная история.
- Нет lost updates.
- Масштабируемость.

**Минусы:**
- Сложность архитектуры.
- Сложнее чтение (нужны проекции).
- Не всегда оправдано для простых CRUD-систем.

---

## Сравнение подходов

| Подход | Производительность | Сложность | Гарантия целостности | Когда использовать |
| :--- | :--- | :--- | :--- | :--- |
| **Optimistic Lock** | Высокая (при редких конфликтах) | Средняя | Высокая | **Стандарт для большинства случаев** |
| **Pessimistic Lock** | Низкая (блокировки) | Низкая | Высокая | Высокая конкуренция, короткие транзакции |
| **SQL Update** | Очень высокая | Низкая | Средняя (только простые правила) | Счетчики, инвентарь |
| **Event Sourcing** | Высокая (запись), Низкая (чтение) | Очень высокая | Очень высокая | Сложные домены, нужна история |

---

## Когда какой выбираю?

### 1. **Оптимистичная блокировка (Optimistic Locking)** — **По умолчанию**
- Для 90% бизнес-логики.
- Когда конкуренция средняя/низкая.
- Когда бизнес-логика сложная (нужны доменные методы).
- **Пример:** Редактирование профиля, изменение настроек, обновление заказа.

### 2. **Атомарный SQL Update** — **Для простых счетчиков**
- Когда правило простое: `balance >= amount`.
- Когда производительность критична.
- **Пример:** Инвентарь, счетчики, бонусные баллы.

### 3. **Пессимистичная блокировка** — **Для критических операций**
- Когда конфликт недопустим.
- Когда транзакция короткая.
- **Пример:** Оплата заказа, бронирование места.

### 4. **Event Sourcing** — **Для сложных доменов**
- Когда нужна полная история.
- Когда домен сложный и часто меняется.
- **Пример:** Банковские операции, логистика, ERP.

---

## Типовые ошибки

1. **Игнорирование конфликтов.**
   ```php
   // ❌ ПЛОХО
   $account = $repo->find($id);
   $account->withdraw($amount);
   $em->flush(); // Могло быть потеряно обновление!
   ```

2. **Использование Pessimistic Lock без транзакции.**
   ```php
   // ❌ ПЛОХО
   // Блокировка не работает, если нет beginTransaction()
   $account = $repo->findForUpdate($id);
   ```

3. **Слишком долгие транзакции.**
   ```php
   // ❌ ПЛОХО
   $em->beginTransaction();
   $account = $repo->findForUpdate($id);
   
   // Вызов внешнего API (может занять 10 секунд!)
   $response = $httpClient->get('https://payment-gateway/charge');
   
   $account->withdraw($amount);
   $em->flush();
   $em->commit();
   // Другие процессы ждали 10 секунд!
   ```

4. **Нарушение инкапсуляции через SQL.**
   ```php
   // ❌ ПЛОХО (если агрегат сложный)
   // Обновляем только balance, но забываем про status, created_at и т.д.
   UPDATE accounts SET balance = balance - 1 WHERE id = 5;
   ```

---

## Итог

**Конкурентный доступ к агрегату** — это не баг, а фича, которую нужно проектировать.

1. **Определите уровень конкуренции.** Если низкий — `Optimistic Lock`.
2. **Определите сложность агрегата.** Если простой — `SQL Update`. Если сложный — `Optimistic Lock` + ретрау.
3. **Обработайте конфликты.** Верните ошибку пользователю или повторите попытку.
4. **Не держите блокировки долго.** Транзакции должны быть короткими.

**Помните:** Инварианты агрегата — святое. Никогда не нарушайте их ради производительности. Лучше показать пользователю «Пожалуйста, попробуйте снова», чем продать товар, которого нет.

> ⏱ 16.0s · prompt 54077 tok · output 2603 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Я использую гибрид **GitFlow** и **Trunk-Based Development**, адаптированный под современный PHP/Symfony стек. Этот подход балансирует между стабильностью продакшена и скоростью разработки.

---

## 1. Ветвление: Стратегия

### Основные ветки:

| Ветка | Назначение | Защита |
| :--- | :--- | :--- |
| `main` | Продакшен. Всегда стабильна. | Защищена: только через PR, mandatory review. |
| `develop` (опционально) | Интеграция фич для релиза. | Защищена: только через PR. |
| `feature/*` | Новые фичи. | Временные, удаляются после мержа. |
| `bugfix/*` | Исправление багов. | Временные, удаляются после мержа. |
| `hotfix/*` | Критические исправления в продакшене. | Временные, мержатся в `main` и `develop`. |

### Почему так?

1. **`main` — единственный источник истины.**
   - Из `main` собирается продакшен.
   - `main` никогда не ломается: все изменения проходят через ревью.

2. **Короткоживущие ветки (Feature/Branch).**
   - Ветки живут **1-3 дня**.
   - Длинные ветки (2+ недели) — антипаттерн: сложно мержить, много конфликтов.

3. **Нет ветки `staging`.**
   - Staging — это **окружение**, а не ветка.
   - Staging разворачивается из `develop` (или `main` перед релизом).
   - Ветка `staging` создает иллюзию изоляции, но на практике усложняет процесс.

4. **`develop` — опционально.**
   - В маленьких командах (1-3 разработчика) можно мержить фичи прямо в `main`.
   - В больших командах `develop` собирает фичи для релиза.

---

## 2. Формат коммитов

### Конвенция: Conventional Commits

```
<type>(<scope>): <short summary>

<body>

<footer>
```

### Типы:

| Тип | Назначение | Пример |
| :--- | :--- | :--- |
| `feat` | Новая фича | `feat(order): add discount logic` |
| `fix` | Исправление бага | `fix(auth): resolve jwt expiration issue` |
| `docs` | Документация | `docs(readme): update setup instructions` |
| `style` | Форматирование (без изменения логики) | `style(controller): reformat code` |
| `refactor` | Рефакторинг (без изменения поведения) | `refactor(order): extract discount service` |
| `test` | Добавление тестов | `test(order): add edge case for zero items` |
| `chore` | Рутина (зависимости, конфиги) | `chore(deps): bump symfony to 6.4` |
| `perf` | Улучшение производительности | `perf(api): add database index for orders` |
| `ci` | CI/CD изменения | `ci(github): add parallel test execution` |

### Пример:

```text
feat(order): implement coupon validation

- Add CouponValidator service
- Integrate with Order creation flow
- Add unit tests for edge cases

Closes #123
```

### Почему так?

1. **Автогенерация Changelog.**
   - Инструменты (Commitizen, Semantic Release) читают `feat:` и `fix:` и автоматически формируют changelog.

2. **Читаемость истории.**
   - `git log --oneline` становится структурированным.
   - Легко найти, когда была добавлена фича.

3. **Версионирование.**
   - `feat` → минорная версия.
   - `fix` → патч-версия.
   - `BREAKING CHANGE` → мажорная версия.

---

## 3. Pull Request (PR)

### Требования к PR:

1. **Маленький размер.**
   - **< 400 строк** (включая тесты).
   - Если PR больше — разбить на несколько.

2. **Одна задача.**
   - Один PR = одна фича или один баг.
   - Не смешивать фичи и рефакторинг.

3. **Чек-лист в описании:**
   ```markdown
   ## Что сделано?
   - Реализовал валидацию купонов.
   - Добавил тесты.

   ## Как тестировать?
   1. Создать заказ.
   2. Применить купон SAVE10.
   3. Убедиться, что скидка применена.

   ## Скриншоты/Логи
   [Ссылки на логи, скриншоты]

   ## Чек-лист
   - [ ] Код покрыт тестами
   - [ ] Пройден static analysis (PHPStan)
   - [ ] Пройдены CI-проверки
   - [ ] Обновлена документация (если нужно)
   ```

4. **Ссылки на задачи.**
   - `Closes #123` или `Refs #124`.

### Почему так?

1. **Маленькие PR = Быстрое ревью.**
   - Ревьюер тратит 15 минут, а не 2 часа.
   - Качество ревью выше.

2. **Одна задача = Простой откат.**
   - Если фича сломала что-то — откатываем один коммит.

3. **Чек-лист = Стандарт качества.**
   - Разработчик сам проверяет, все ли сделал.

---

## 4. Code Review

### Правила:

1. **Максимум 2 ревьюера.**
   - Один — технический (архитектор/старший).
   - Один — предметный (владелец домена).

2. **Время ответа.**
   - **< 24 часов.**
   - Если не ответили — эскалация.

3. **Комментарии:**
   - **Обязательные:** `[MUST]` или `[BLOCKING]`.
   - **Рекомендательные:** `[SHOULD]` или `[NICE]`.
   - **Вопросы:** `?`.

4. **Не спорить в комментариях.**
   - Если не согласны — созвон или голосование.

5. **Проверяем:**
   - **Корректность:** Логика работает?
   - **Безопасность:** Нет уязвимостей?
   - **Производительность:** Нет N+1, лишних запросов?
   - **Тестируемость:** Код легко тестировать?
   - **Читаемость:** Код понятен?

### Почему так?

1. **Быстрое ревью = Быстрые релизы.**
   - Задержки в ревью — главный bottleneck.

2. **Структурированные комментарии.**
   - Разработчик понимает, что критично, а что опционально.

3. **Фокус на качестве.**
   - Не на стиле (это за дело линтеров), а на логике и архитектуре.

---

## 5. CI/CD Пайплайн

### Стадии:

| Стадия | Команды | Время |
| :--- | :--- | :--- |
| **Lint** | `composer cs:check`, `eslint` | < 1 мин |
| **Static Analysis** | `vendor/bin/phpstan analyse` | < 2 мин |
| **Unit Tests** | `vendor/bin/phpunit --testsuite Unit` | < 2 мин |
| **Build** | `composer install --no-dev` | < 1 мин |
| **Integration Tests** | `vendor/bin/phpunit --testsuite Integration` | < 5 мин |
| **Security Scan** | `composer audit`, `trivy` | < 1 мин |

### Требования:
- **Все стадии должны пройти** для мержа в `main`.
- **PR блокируется** до прохождения всех проверок.

### Почему так?

1. **Быстрая обратная связь.**
   - Разработчик узнает об ошибках за 5 минут, а не после мержа.

2. **Автоматизация.**
   - Не нужно помнить, что запустить тесты.

3. **Безопасность.**
   - Уязвимости в зависимостях ловятся автоматически.

---

## 6. Релизы

### Стратегия:

1. **Регулярные релизы.**
   - **Еженедельно** (вторник/четверг).
   - Или по мере готовности (если мало изменений).

2. **Версионирование.**
   - **SemVer:** `MAJOR.MINOR.PATCH`.
   - `feat` → `MINOR`.
   - `fix` → `PATCH`.
   - `BREAKING CHANGE` → `MAJOR`.

3. **Процесс релиза:**
   1. Создать ветку `release/1.5.0` из `develop`.
   2. Запустить полный тест-прогон.
   3. Собрать артефакт (Docker image, binary).
   4. Развернуть на **Staging**.
   5. Провести **Smoke Testing**.
   6. Развернуть на **Production**.
   7. Создать **Tag** `v1.5.0` на `main`.
   8. Мержить `release` обратно в `develop` и `main`.
   9. Обновить **Changelog** (автоматически).

### Почему так?

1. **Регулярность = Предсказуемость.**
   - Команда знает, когда релиз.
   - Пользователи знают, когда ждать новые фичи.

2. **Staging перед Production.**
   - Возможность проверить в среде, похожей на продакшен.

3. **Четкий трекер версий.**
   - Легко откатиться на предыдущую версию.
   - Прозрачность для пользователей.

---

## 7. Откаты (Rollback)

### Принцип:
**Откат — это мерж обратного PR, а не `git revert`.**

### Процесс:
1. Создать ветку `hotfix/rollback-1.5.0`.
2. Мержить **обратный PR** (или `git revert` коммит).
3. Запустить тесты.
4. Мержить в `main`.
5. Развернуть.

### Почему не `git revert`?

1. **`git revert` создает новый коммит.**
   - Историю сложнее читать.
   - Легко потерять контекст, почему был откат.

2. **PR-подход = Документирование.**
   - В описании PR можно написать, почему откат.
   - Ревьюеры могут проверить, что откат корректен.

---

## 8. Инструменты

| Задача | Инструмент |
| :--- | :--- |
| Хостинг | GitHub / GitLab / Bitbucket |
| CI/CD | GitHub Actions / GitLab CI |
| Частота коммитов | Commitizen / Commitlint |
| Ревью | Pull Request + Codeowners |
| Релизы | Semantic Release / Manual |
| Документация | Conventional Commits + Auto Changelog |

---

## Итог: Почему этот процесс?

1. **Баланс скорости и качества.**
   - Маленькие PR, быстрое ревью, автоматизированные проверки.

2. **Прозрачность.**
   - Каждый шаг отслеживается: от коммита до релиза.

3. **Масштабируемость.**
   - Процесс работает для команды из 2 и 20 разработчиков.

4. **Безопасность.**
   - Автоматические проверки, код-ревью, staging перед продакшеном.

5. **Предсказуемость.**
   - Регулярные релизы, четкие правила ветвления.

**Главное правило:** Процесс должен быть **простым для команды** и **надёжным для продакшена**. Если процесс слишком сложен — упростите. Если слишком прост — добавьте проверки.

> ⏱ 23.8s · prompt 56748 tok · output 2874 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хорошее код-ревью — это не поиск ошибок, а **совместное улучшение продукта** и **передача знаний**. Моя цель — оставить код лучше, чем я его застал, и сохранить хорошие отношения с автором.

Вот мой подход к каждому аспекту.

---

## 1. Маркировка замечаний

Использую ясную систему приоритетов, чтобы автор понимал, что критично, а что нет.

### Категории:

| Маркер | Значение | Действие автора |
| :--- | :--- | :--- |
| **`[BLOCKING]`** | Критическая проблема. Код не может быть смёржен. | **Обязательно** исправить до мержа. |
| **`[MUST]`** | Серьезная проблема (баг, уязвимость, производительность). | **Обязательно** исправить или объяснить, почему нет. |
| **`[SHOULD]`** | Рекомендация по улучшению (читаемость, архитектура). | Желательно исправить. Если не исправлено — нужно объяснить. |
| **`[NICE]`** | Мелкое улучшение (название переменной, стиль). | На усмотрение автора. Не блокирует мерж. |
| **`[QUESTION]`** | Вопрос к намерению автора. | Ответить на вопрос. |
| **`[LGTM]`** | Looks Good To Me. | Можно мержить. |

### Примеры комментов:

```text
[BLOCKING] Здесь возможна NPE (NullPointerException).
Пользователь может быть null, если запрос пришел с пустым ID.
Предлагаю добавить проверку:
if ($user === null) {
    throw new UserNotFoundException();
}

[MUST] Этот метод делает 2 вещи: валидация и сохранение.
Нарушение SRP. Предлагаю разбить на `validateOrder()` и `saveOrder()`.

[SHOULD] Название `$temp` не отражает назначение.
Это временный объект с данными заказа?
Предлагаю переименовать в `$orderSnapshot`.

[NICE] Можно заменить `if-else` на `match` для лучшей читаемости.

[QUESTION] Почему здесь используется `sleep(1)`?
Это симуляция задержки API? Можно ли это покрыть мок-ом в тестах?
```

---

## 2. На что смотрю в первую очередь (Приоритеты)

Я не читаю код построчно. Я ищу **паттерны рисков**.

### Уровень 1: Безопасность и Корректность (Критично)
1. **SQL Injection / XSS / CSRF.**
   - Есть ли экранирование?
   - Используются ли prepared statements?
2. **Потеря данных.**
   - Может ли произойти race condition?
   - Есть ли транзакции?
   - Что будет, если ошибка в середине операции?
3. **Правильность бизнес-логики.**
   - Соответствует ли код постановке задачи?
   - Проверяются ли крайние случаи (0, -1, null, huge numbers)?

### Уровень 2: Архитектура и Дيزайн (Важно)
1. **SRP (Single Responsibility).**
   - Класс/метод делает одну вещь?
2. **Связность и сплоченность.**
   - Нет ли циклических зависимостей?
   - Нет ли «большого класса»?
3. **Инкапсуляция.**
   - Приватные методы там, где они должны быть?
   - Публичный API минимален?

### Уровень 3: Производительность (Важно для критичных путей)
1. **N+1 запросы.**
   - Есть ли циклы с запросами к БД?
2. **Память.**
   - Загружается ли весь массив, если нужен только первый элемент?
3. **Асинхронность.**
   - Можно ли вынести тяжелые операции в фоновые задачи?

### Уровень 4: Читаемость и Упkeepability (Желательно)
1. **Имена.**
   - Отражают ли они намерение?
2. **Длина методов.**
   - Методы < 20-30 строк?
3. **Комментарии.**
   - Объясняют ли **почему**, а не **что**?
4. **Тесты.**
   - Покрыты ли новые фичи?
   - Тесты читаемы?

### Уровень 5: Стиль и Конвенции (Мелочи)
1. **Форматирование.**
   - Соответствует ли PSR-12 / Prettier?
2. **Импортация.**
   - Нет ли неиспользуемых импортов?

---

## 3. Как поступаю при разногласии

Разногласие — это нормально. Это значит, что мы оба думаем о качестве.

### Мои правила:

#### 1. **Факты, а не мнения.**
- ❌ «Мне кажется, это плохо».
- ✅ «Это нарушает SRP, потому что...».
- ❌ «Я бы написал иначе».
- ✅ «В нашем проекте мы используем паттерн X, потому что...».

#### 2. **Ссылки на стандарты/док-у.**
- Если есть документация, гайдлайны проекта или стандарты (PSR, SOLID) — ссылайтесь на них.
- «Согласно нашим гайдлайнам, методы не должны превышать 30 строк».

#### 3. **Предлагаю альтернативу.**
- Не только критикую, но и предлагаю решение.
- «Я бы предложил разбить этот метод на два... Вот пример кода».

#### 4. **Компромисс ради сроков.**
- Если автор настаивает, а я не могу доказать, что мой вариант лучше — я могу согласиться.
- «Хорошо, давай оставим как есть, но добавим TODO-комментарий для будущего рефакторинга».

#### 5. **Эскалация.**
- Если не можем договориться за 2-3 раунда:
  - Обсуждаем голосом/видео (если возможно).
  - Привлекаем Тима лида или архитектора.
  - **Решение принимается старшим разработчиком и не оспаривается.**

#### 6. **Записываем решение.**
- Если мы пришли к компромиссу — записываем его в коммент или ADR (Architecture Decision Record).
- «Мы решили оставить текущий подход, потому что X. Пересмотрим в Q3».

### Пример диалога:

> **Я:** `[MUST]` Этот метод делает и валидацию, и сохранение. Это нарушение SRP.
>
> **Автор:** Но так удобнее. Нам нужно, чтобы оба действия были атомарными.
>
> **Я:** Понимаю. Но если валидация упадет, сохранение не должно происходить. А если сохранение упадет — валидация была зря.
> Лучше разбить на `validate()` и `save()`, а атомарность обеспечить на уровне UseCase через транзакцию.
> Вот пример: [код]
>
> **Автор:** Хм, логично. Давай разобьем.
>
> **Я:** Отлично. Добавлю `[LGTM]` после правки.

---

## 4. Что НЕ делаю в ревью

1. **Не критикую стиль** (если линтер это проверил).
2. **Не переписываю код** за автора (кроме мелких правок).
3. **Не использую пассивно-агрессивные комментарии.**
   - ❌ «Это же очевидно, что тут ошибка».
   - ✅ «Тут может быть ошибка, если $id = null».
4. **Не блокирую мерж из-за мелочей.**
   - `[NICE]` не должны блокировать мерж.

---

## Итог

Хорошее ревью — это:
1. **Конструктив:** Предлагать, а не критиковать.
2. **Приоритизация:** Сначала безопасность и логика, потом стиль.
3. **Уважение:** Автор знает свой код лучше, чем вы.
4. **Цель:** Улучшить код и команду, а не победить в споре.

**Помните:** Код-ревью — это инвестиция в команду. Хорошее ревью делает всех сильнее.

> ⏱ 18.3s · prompt 59670 tok · output 1960 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Чтобы превратить «боль» бизнеса в техническую задачу, которую можно реализовать «слепо» (без постоянных уточнений), нужно снять все неопределенности: **что именно** не работает, **кто** страдает, **какие** критерии успеха и **какие** ограничения по дизайну/архитектуре.

Вот как я сформулирую эту задачу в Jira/Hive.

---

### 📝 Задача в трекере

**Название:** [UX/Backend] Реализация поиска и просмотра истории заказов пользователя

**Описание:**

#### 1. Контекст и Цель
В настоящее время пользователи не могут быстро найти свои прошлые заказы в личном кабинете. Это приводит к росту обращений в поддержку (X обращений в неделю). Цель — предоставить пользователю удобный способ просмотра истории заказов с фильтрацией и поиском.

#### 2. Функциональные требования (Что делаем)

**2.1. Список заказов (Мой кабинет → Заказы)**
- Отображается список всех заказов пользователя, отсортированных по дате создания (новые сверху).
- **Пагинация:** по 10 заказов на страницу.
- **Информация по карточке заказа:**
  - Номер заказа (кликабельный).
  - Дата заказа (формат: `DD.MM.YYYY`).
  - Сумма заказа (с валютой).
  - Статус заказа (цветовой индикатор + текст): *Ожидает оплаты, Оплачен, Отменен, Доставлен*.
  - Количество позиций (например, "3 товара").

**2.2. Фильтры (на странице списка)**
- **По статусу:** Все, Активные, Завершенные, Отмененные. (Чекбоксы или дропдаун).
- **По дате:** "За последние 30 дней", "За последние 3 месяца", "За 2023 год", "За 2024 год", "Своя дата" (date picker).

**2.3. Поиск по заказу**
- Поле поиска на странице списка.
- Ищет по:
  - Номера заказа.
  - Названию товара (частичное совпадение).
- Поиск запускается по `Enter` или по нажатию кнопки (не на каждый символ, чтобы не нагружать БД).

**2.4. Детальная страница заказа**
- Клик по номеру заказа открывает детальную страницу.
- Отображается:
  - Все данные заказа (адрес, способ доставки, оплата).
  - Список товаров (название, цена, количество).
  - Кнопка "Скачать чек" (если чек сформирован).
  - Кнопка "Вернуть товар" (только для статуса "Доставлен" и в пределах 14 дней).

#### 3. Нефункциональные требования (Как делаем)

**3.1. Производительность**
- Время загрузки списка заказов: **< 200мс** (p95) для пользователя с 100 заказами.
- Время загрузки детальной страницы: **< 300мс** (p95).
- **Требование:** Запрещено делать N+1 запросов. Используйте Eager Loading или Batch Loading в Doctrine.

**3.2. Безопасность**
- Пользователь видит **только свои** заказы. (Проверка через `is_granted('ORDER_VIEW', $order)`).
- Запрос на чужой заказ → ответ `404 Not Found` (не раскрываем существование заказа).

**3.3. Совместимость**
- Адаптивная верстка для мобильных устройств.
- Поддержка браузеров: последние версии Chrome, Safari, Firefox, Edge.

#### 4. Критерии приемки (Definition of Done)

1. **Фронтенд:**
   - [ ] Страница списка заказов отображается корректно.
   - [ ] Фильтры работают и сохраняются в URL (чтобы можно было поделиться ссылкой).
   - [ ] Поиск работает (по номеру и товару).
   - [ ] Пагинация работает.
   - [ ] Детальная страница открывается по клику.

2. **Бэкенд:**
   - [ ] API-эндпоинт `GET /api/v1/orders` поддерживает пагинацию, фильтры и поиск.
   - [ ] API-эндпоинт `GET /api/v1/orders/{id}` возвращает детали заказа.
   - [ ] Проверена безопасность (доступ только к своим заказам).
   - [ ] Отсутствуют N+1 запросы (проверено через Profiler).

3. **Тесты:**
   - [ ] Unit-тесты для бизнес-логики (если есть).
   - [ ] API-тесты для всех сценариев (список, фильтры, поиск, чужой заказ).
   - [ ] Code Coverage > 80% для новых/измененных файлов.

#### 5. Ограничения и Зависимости

- **Не менять** текущую схему БД таблицы `orders` (только добавление индексов, если нужно).
- **Не использовать** внешние поисковые движки (Elasticsearch) — только PostgreSQL.
- **Зависимость:** Нужен доступ к тестовым аккаунтам с историей заказов (минимум 50 заказов) для проверки пагинации.

#### 6. Пример API-контракта (JSON)

**Request:** `GET /api/v1/orders?status=paid&search=iphone&page=1&limit=10`

**Response 200:**
```json
{
  "data": [
    {
      "id": "order_12345",
      "number": "ORD-2024-12345",
      "created_at": "2024-05-20T14:30:00Z",
      "total_amount": {
        "amount": "15000.00",
        "currency": "RUB"
      },
      "status": "PAID",
      "items_count": 2
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total_count": 42,
    "total_pages": 5
  }
}
```

**Response 404 (чужой заказ):**
```json
{
  "error": "Order not found"
}
```

---

### 💡 Почему включены именно эти пункты?

| Пункт | Зачем он нужен |
| :--- | :--- |
| **Конкретные поля (2.1)** | Чтобы разработчик не придумывал сам, что показывать. Если не указать "Сумма" — она не будет. |
| **Поведение фильтров (2.2)** | Чтобы не было сюрпризов. "Своя дата" означает date picker, а не текстовое поле. |
| **Поиск по товару (2.3)** | Бизнес сказал "не находят заказы". Чаще всего ищут по названию товара. Если не указать — сделают поиск только по номеру, что бесполезно. |
| **Производительность (3.1)** | Чтобы не написали N+1. Явное требование к ms заставляет использовать `JOIN` или `Batch`. |
| **Безопасность (3.2)** | Чтобы не забыли проверить владельца. `404` вместо `403` — важная деталь безопасности (non-enumeration). |
| **Критерии приемки (4)** | Чтобы QA знала, как тестировать, а разработчик — когда работа закончена. |
| **Ограничения (5)** | Чтобы не начали внедрять Elasticsearch на середине задачи. Явно сказано: только PostgreSQL. |
| **API-контракт (6)** | Чтобы фронтенд и бэкенд могли работать параллельно без синхронизации каждый час. |

### 🚫 Что я **не** включил (и почему)

1. **Дизайн.** Я не описываю, какой цвет кнопки. Это задача дизайнера. Разработчик должен взять макет из Figma. В описании я могу написать: *"UI/UX: см. макет в Figma [ссылка]"*.
2. **Технологии.** Я не говорю "используй Vue.js" или "используй Symfony API Platform". Это уже определено в проекте.
3. **Мелкие детали.** Я не описываю, как выглядит спиннер загрузки. Это стандарт UI-кита.

### 📌 Итог

Такая формулировка позволяет разработчику:
1. **Оценить время** (он видит объем работы).
2. **Сразу начать кодить** (понятен API и критерии).
3. **Не возвращаться с вопросами** (все граничные случаи описаны).

Если в процессе появятся вопросы — это значит, что в какой-то момент я упустил детали. Но в 90% случаев такой уровень детализации снимает большинство блокировок.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Этот список вопросов критически важен, потому что «сырая боль» бизнеса («не находят заказы») часто имеет разные технические причины. Прежде чем писать код, мне нужно понять **причину**, а не лечить **симптом**.

Я разбил вопросы на 4 блока: Приоритет, Техническое состояние, UX и Бизнес-метрики.

---

### Блок 1: Диагностика проблемы (Почему именно сейчас?)

**1. Что конкретно говорят пользователи в обращениях?**
*   *Почему:* Формулировка «не находят» может означать:
    *   *А)* Загрузка занимает 10 секунд (проblema производительности).
    *   *Б)* Список пустой или обрезан (баг пагинации/фильтров).
    *   *В)* Они ищут старый заказ за 2019 год, а мы показываем только последние 5 (ограничение UI).
    *   *Г)* Они ищут по названию товара, а поиск работает только по номеру (ограничение функционала).
*   *Цель:* Понять, что именно сломано или отсутствует.

**2. Какой процент обращений в поддержку связан с поиском заказов? Есть ли статистика за последние 3 месяца?**
*   *Почему:* Если обращений 5 в месяц — это low priority, и можно просто исправить баг. Если 500 в месяц — это P0, нужно строить мощный поисковый инструмент.
*   *Цель:* Определить приоритет и масштаб задачи.

**3. Были ли недавние изменения в интерфейсе, БД или логике заказов?**
*   *Почему:* Возможно, пользователи жалуются на *новое* поведение, которое регрессировало.
*   *Цель:* Связать проблему с конкретным релизом.

---

### Блок 2: Технические ограничения и текущее состояние

**4. Как устроено текущее хранилище заказов? Сколько всего заказов у пользователя в среднем и в максимуме?**
*   *Почему:* Если у пользователя 10 заказов — хватит простого `LIMIT 10`. Если у VIP-клиента 5 000 заказов — `OFFSET` на 4000-й странице будет умирать. Нужно ли тогда keyset pagination или выгрузка в CSV?
*   *Цель:* Определить стратегию пагинации.

**5. Есть ли уже реализованный поиск? Если да, то по каким полям?**
*   *Почему:* Возможно, поиск есть, но он глючит или не виден.
*   *Цель:* Не переизобретать велосипед, а чинить существующее.

**6. Какой объем данных в таблице `orders`?**
*   *Почему:* 10 000 строк и 10 000 000 строк — это разные подходы к индексированию и производительности.
*   *Цель:* Понять, нужен ли Elasticsearch или хватит PostgreSQL.

**7. Есть ли требования по безопасности (GDPR) для старых заказов?**
*   *Почему:* Возможно, заказы старше 3 лет должны быть «обезличены» (скрыты товары, оставлен только номер).
*   *Цель:* Учесть юридические требования в фильтре/отдаче данных.

---

### Блок 3: UX и Ожидаемое поведение (Что именно «хотят»?)

**8. По каким критериям пользователи хотят искать заказы?**
*   *Варианты:*
    *   Только по дате?
    *   По статусу (оплачено/отменено)?
    *   По названию товара (самый частый запрос поддержки)?
    *   По номеру заказа (если его прислали в SMS/Email)?
*   *Цель:* Приоритизировать параметры поиска.

**9. Что пользователь должен видеть в карточке заказа в списке?**
*   *Почему:* Если мы будем отдавать в списке список из 20 товаров, карточка будет огромной. Нужен ли компактный вид?
*   *Цель:* Определить DTO (Data Transfer Object) для API.

**10. Как должна работать фильтрация по дате?**
*   *Почему:* «Проведшая неделя» — это с 1-го числа или последние 7 дней? «Этот год» — с 1 января?
*   *Цель:* Убрать двусмысленность в бизнес-логике.

**11. Нужна ли сортировка?**
*   *Почему:* По умолчанию новые сверху. Но вдруг они хотят старые сверху, или отсортировать по сумме?
*   *Цель:* Не переусложнять BQL-запросы.

---

### Блок 4: Метрики успеха (Как поймем, что решили проблему?)

**12. Как мы будем измерять успех?**
*   *Примеры метрик:*
    *   Снижение числа обращений в поддержку по тегу «Поиск заказа» на 50%.
    *   Временя поиска заказа < 10 секунд.
    *   NPS пользователей, использующих поиск.
*   *Цель:* Чтобы QA и бизнес могли объективно закрыть задачу, а не субъективно сказать «ну вроде стало лучше».

**13. Есть ли готовый макет в Figma?**
*   *Почему:* Если нет, я не знаю, какой UI рисовать. Если есть — я сверяю требования с макетом.
*   *Цель:* Синхронизация ожиданий по дизайну.

---

### 🎯 Резюме: Что я вынесу из этих ответов?

1.  **Приоритет:** Если обращений мало — сделаю минимальный фикс. Если много — буду строить полноценный поиск.
2.  **Алгоритм:** Если заказов много — буду писать `KEYSET` pagination и сложные индексы. Если мало — просто `LIMIT/OFFSET`.
3.  **Функционал:** Добавлю только те фильтры, которые реально нужны пользователям (по товару, по дате).
4.  **Сроки:** Понимаю, сколько времени нужно на бэкенд (индексы, API) и на фронтенд (UI поиска, фильтров).

**Совет:** Задавайте эти вопросы не в чате разрозненно, а создайте документ «Пробелы в ТЗ» и вышлите его заказчику. Это покажет вашу проактивность и поможет избежать переделок.

> ⏱ 17.7s · prompt 61711 tok · output 2089 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это классическая ситуация на стыке инженерии и коммуникации. Мои действия в обоих случаях различаются принципиально, так как риски разные: в первом случае это **риск расползания скоупа (scope creep)**, во втором — **риск создания продукта, который не решает проблему**.

---

### Случай А: Нашёл попутный баг (не связанный с задачей)

**Моя стратегия: «Не чиня попутно, фиксируй и сообщай».**

#### Что делаю:
1. **Не чиню его в текущем Pull Request.**
   - Даже если баг крошечный (одна строка), я не добавляю его в свой PR.
2. **Создаю отдельную задачу (Jira/Trello) с приоритетом.**
   - Заголовок: `[BUG] Описание бага`.
   - Прикрепляю шаги воспроизведения, лог, скриншот.
   - Ставлю приоритет (P1, P2, P3) на основе критичности.
3. **Сообщаю тимлиду или заказчику.**
   - «Коллеги, попутно с задачей X я заметил баг Y. Он не влияет на текущую задачу, но может повлиять на пользователей. Создал задачу [ссылка]. Рекомендую приоритет P2».

#### Почему так?

1. **Чистота Pull Request (PR).**
   - PR должен быть **атомарным** и **связанным** с одной задачей.
   - Если я добавлю чужой баг в свой PR, ревьюер будет тратить время на оценку нерелевантного кода.
   - Если PR упадет в ревью, баг не будет починен. Если PR мержится, баг чинится «на авось».

2. **Тестируемость и откат.**
   - Если текущая задача сломает продакшен, мы откатываем PR.
   - Если бы мы зашили туда чужой баг, он тоже откатится. Это может быть хорошо, но плохо, если баг был критичным.
   - Если мы откатим текущую задачу, а баг уже починили отдельно — мы теряем фикс.

3. **Прозрачность для бизнеса.**
   - Заказчик должен **знать** о багах.
   - Если я починю его молча, бизнес может подумать, что баг не существовал, или не оценит мою работу.
   - Если я сообщу — я покажу внимательность и проактивность.

4. **Риск регрессии.**
   - Попутный баг может быть следствием более глубокой проблемы.
   - Могу случайно сделать «костыль», который перекроет симптом, но усложнит код.
   - Лучше, чтобы баг исследовали отдельно, с полным покрытием тестами.

**Исключение:**
Если баг **блокирует** выполнение моей текущей задачи (например, я не могу протестировать свою фичу из-за него) — тогда я **обязан** починить его или обсудить с тимлидом, чтобы он был починен параллельно.

---

### Случай Б: Критерий приёмки противоречит цели задачи

**Моя стратегия: «Стоп, эскалация и аргументация».**

#### Что делаю:
1. **Останавливаю работу над этим критерием.**
   - Не пытаюсь «вкрутить» противоречащий критерий.
2. **Формулирую проблему.**
   - Пишу короткое сообщение тимлиду/PO:
     > «Я столкнулся с противоречием в ТЗ.
     > **Цель задачи:** Позволить пользователям быстро находить заказы (UX).
     > **Критерий приёмки:** Поиск должен работать только по точному номеру заказа.
     > **Противоречие:** Пользователи в 80% случаев ищут по названию товара (данные из поддержки). Точный поиск по номеру не решает проблему «не находят заказы», так как пользователь редко знает номер.
     > **Предложение:** Расширить поиск на `LIKE` по названию товара или добавить отдельный фильтр.
     > **Риск:** Если реализовать только точный поиск, мы не закроем бизнес-боль, и поддержка не уменьшится.»
3. **Жду решения.**
   - Не продолжаю работу, пока не получу ответ.
   - Если решение «делаем как в ТЗ» — фиксирую это в задачах (чтобы потом не спрашивали «почему не нашли?»).

#### Почему так?

1. **Я защищаю бизнес-интересы.**
   - Моя цель — не «отжать галочки в Jira», а **решить проблему бизнеса**.
   - Если критерий ведёт к тому, что задача не решит боль — я должен это остановить.
   - Иначе я стану «винтиком», который выполняет приказы, не думая о результате.

2. **Исключаю переделки.**
   - Если я сделаю всё по ТЗ, а потом бизнес увидит, что пользователи всё равно не находят заказы — нам придётся переделывать.
   - Лучше 2 часа на обсуждение сейчас, чем 2 дня на переделку потом.

3. **Снижаю риск «тихого провала».**
   - Если я замолчу и сделаю как сказали, а задача провалится — ответственность ляжет на меня («ты не заметил противоречие?»).
   - Если я показал противоречие — ответственность переходит на PO/тимлида.

4. **Демонстрирую экспертизу.**
   - Тимлид ценит разработчика, который видит систему, а не просто копит строки кода.
   - Это сигнал: «Я думаю о продукте, а не только о коде».

**Исключение:**
Если противоречие мелкое (например, в ТЗ написано «кнопка синяя», а цель — «кнопка должна быть заметной», и есть стандарты UI-кита) — я просто спрашиваю: «Подтвердите, что нужен именно синий цвет, несмотря на гайдлайны?»

---

### Итоговая таблица

| Ситуация | Моё действие | Почему |
| :--- | :--- | :--- |
| **Попутный баг** | Создаю задачу, сообщаю, **не чиню** в текущем PR. | Чистота PR, тестируемость, прозрачность для бизнеса. |
| **Противоречие в ТЗ** | Стою, эскалирую, аргументирую, **жду решение**. | Защита бизнес-интереса, исключение переделок, демонстрация экспертизы. |

### Главный принцип:
**Я не исполнитель, я инженер продукта.**
Моя задача — сделать правильный код, который решает правильную проблему. Если код неправильный (баг) — я его фиксирую. Если проблема неправильная (противоречие в ТЗ) — я её освещаю.

> ⏱ 16.5s · prompt 65396 tok · output 1616 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении — это не просто «я закончил, посмотрите». Это **документ, который защищает вас, ваш код и бизнес** от недопонимания.

Мой отчёт состоит из 5 блоков. Если часть задачи не сделана — структура меняется, но **полнота** отчёта не снижается.

---

## 📝 Структура отчёта о выполнении

### 1. Резюме (Executive Summary)
**Что сделано:** 2–3 предложения.
**Статус:** ✅ Готово к ревью / ⚠️ Готово с ограничениями / ❌ Не готово.

> *Пример:*
> *«Реализован поиск и фильтрация заказов в личном кабинете. Добавлена пагинация, фильтры по статусу и дате, поиск по товару. Готово к ревью.»*

---

### 2. Детализация изменений (What)
Перечень конкретных изменений, сгруппированных по слоям:

| Слой | Изменения |
| :--- | :--- |
| **Домен** | Добавлен метод `OrderSearchCriteria`, обновлён `OrderRepository` |
| **Приложение** | Новый UseCase `SearchOrdersUseCase`, DTO `OrderSearchCommand` |
| **Адаптеры** | Новый Controller `OrdersController`, обновлён `DoctrineOrderRepository` |
| **БД** | Добавлен составной индекс на `(customer_id, created_at, status)` |
| **Тесты** | 12 unit-тестов, 8 интеграционных тестов |

**Почему это важно:** Ревьюер сразу видит масштаб и архитектуру, не копаясь в 200 файлов.

---

### 3. Как тестировать (How)
Пошаговая инструкция для QA и ревьюера:

1. **Подготовка:**
   - Запустить сервис: `docker compose up`
   - Создать тестового пользователя с 15 заказами (скрипт: `src/bin/create-test-orders.php`)

2. **Проверка сценариев:**
   - [ ] Открыть `/account/orders`
   - [ ] Убедиться, что список отображается (пагинация по 10)
   - [ ] Применить фильтр «Оплаченные» — список обновился
   - [ ] Ввести в поиск `iphone` — находит заказы с этим товаром
   - [ ] Открыть чужой заказ по ID — получить 404
   - [ ] Проверить производительность: `EXPLAIN ANALYZE` на запросе поиска

3. **Где посмотреть логи/метрики:**
   - Логи: `var/log/prod.log`
   - Графana: dashboard `orders-search`

**Почему это важно:** Ревьюер тратит 10 минут на проверку, а не 1 час на понимание, как это работает.

---

### 4. Технические детали и компромиссы (Why)
Здесь я объясняю **неочевидные** решения:

> *«Для поиска по названию товара использован `LIKE` с индексом `pg_trgm`. Альтернатива — Elasticsearch, но для текущей нагрузки (10K заказов) PostgreSQL достаточно. При росте до 1M+ заказов — нужно будет мигрировать на Elasticsearch.»*

> *«Пагинация реализована через `LIMIT/OFFSET` вместо `KEYSET`. Причина: у пользователей в среднем 20 заказов, глубина пагинации не превышает 5 страниц. При росте — заменить на KEYSET.»*

**Почему это важно:** Это показывает вашу инженерную мысль и защищает от вопросов «почему именно так?» в будущем.

---

### 5. Риски и следующие шаги (Next)
Что нужно сделать дальше или на что обратить внимание:

- ⚠️ **Риск:** Если пользователи начнут искать по товару с 1M+ заказов — `LIKE` может стать медленным. Решение: добавить Elasticsearch.
- ✅ **Следующий шаг:** Настроить мониторинг времени запроса `GET /orders` в Grafana.
- ✅ **Следующий шаг:** Добавить кнопку «Скачать чек» в детальной странице (задача #456).

---

## ⚠️ Что меняется, если часть задачи не удалось сделать?

**Структура отчёта НЕ меняется.** Меняется **контент**.

Я **никогда** не скрываю несделанное. Я **фиксирую** его и **аргументирую**.

### Изменения в блоках:

#### 1. Резюме
> *«Реализован поиск и фильтрация заказов. **Не реализован** поиск по товару из-за отсутствия индекса `pg_trgm` и ограничения времени. Готово к ревью с оговорками.»*

#### 2. Детализация
Отмечаете, что **не добавлено**:

| Слой | Изменения |
| :--- | :--- |
| **БД** | ⚠️ Индекс `pg_trgm` **не создан** (требует прав администратора БД) |

#### 3. Как тестировать
Указываете, что **нельзя протестировать**:

- [ ] ⚠️ Поиск по товару — **не тестировать**, функция отключена (флаг `SEARCH_BY_PRODUCT_ENABLED = false`)

#### 4. Технические детали
Объясняете **почему** не сделали:

> *«Поиск по товару требует установки расширения `pg_trgm` и создания индекса. Прав на установку расширения у dev-окружения нет. Запрос к DBA подан (#1234). В текущем релизе функция отключена флагом, чтобы не сломать поиск по номеру.»*

#### 5. Риски и следующие шаги
**Это становится главным блоком:**

- 🚨 **БЛОКИРУЮЩИЙ РИСК:** Поиск по товару не работает. Пользователи будут искать только по номеру. **Влияние:** 30% обращений в поддержку не решатся.
- ✅ **ДЕЙСТВИЕ:** Создана задача #456 «Установить `pg_trgm` и включить поиск по товару». Оценено время: 2 часа.
- ✅ **ДЕЙСТВИЕ:** Предложили временное решение: добавить в поиск поддержку номера заказа (работает).

---

## 🎯 Ключевые принципы отчёта

| Принцип | Почему |
| :--- | :--- |
| **Честность** | Скрыть несделанное — гарантировать переделку или баг в проде. |
| **Аргументация** | Не «не сделал», а «не сделал, потому что X, и вот что я сделаю вместо этого». |
| **Действие** | К каждому несделанному пункту — конкретное следующее действие (задача, тикет, флаг). |
| **Клиентоориентированность** | Показываете **влияние** на бизнес, а не только на код. |

---

## 📋 Шаблон отчёта (можно скопировать)

```markdown
# Отчёт: [Название задачи]

## 1. Резюме
✅ / ⚠️ / ❌ 
[2-3 предложения о том, что сделано и статус]

## 2. Изменения
| Слой | Изменения |
|------|-----------|
| Домен | ... |
| Приложение | ... |
| Адаптеры | ... |
| БД | ... |
| Тесты | ... |

## 3. Как тестировать
1. [Шаг 1]
2. [Шаг 2]
3. [Шаг 3]

**Проверено:**
- [ ] Сценарий 1
- [ ] Сценарий 2

## 4. Технические детали и компромиссы
- [Пояснение 1]
- [Пояснение 2]

## 5. Риски и следующие шаги
- 🚨 [Риск 1] → [Действие]
- ✅ [Следующий шаг 1]
- ✅ [Следующий шаг 2]

## 6. НЕСДЕЛАНО (если есть)
| Пункт | Причина | Влияние | Действие |
|-------|---------|---------|----------|
| ... | ... | ... | Задача #XXX |
```

---

## Итог

**Готовая задача** = код + тесты + отчёт.
**Частично готовая задача** = код + тесты + отчёт + **ясное описание того, что НЕ работает и почему**.

Отчёт — это не бюрократия. Это **ваш страховой полис**. Если что-то пойдёт не так, вы скажете: «Я честно сообщил о риске/ограничении. Вот доказательство».

> ⏱ 19.1s · prompt 67055 tok · output 2025 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

**В PostgreSQL нет команды `FORCE INDEX`.**

Это распространённая ошибка, возникающая из-за переноса опыта из **MySQL** (или MariaDB), где существует синтаксис `FORCE INDEX`, `USE INDEX` и `IGNORE INDEX`.

### Почему в PostgreSQL это отсутствует?

1. **Оптимизатор PostgreSQL (planner) считается достаточно умным.**
   PostgreSQL использует **cost-based optimizer** (оптимизатор на основе затрат), который:
   - Учитывает статистику таблицы (`ANALYZE`).
   - Учитывает `work_mem`, `shared_buffers`, тип индекса.
   - Сравнивает стоимость `Seq Scan` и `Index Scan` на основе **селективности** (selectivity).
   
   Если статистика актуальна, оптимизатор **почти всегда** выбирает правильный план.

2. **Риск неправильных решений.**
   Если бы был `FORCE INDEX`, разработчики начали бы им злоупотреблять, игнорируя статистики и индексы. В сложных запросах (с JOIN-ами, подзапросами) принудительный выбор индекса может **сильно ухудшить** производительность.

3. **Philosophy PostgreSQL.**
   Команда PostgreSQL считает, что **проблема не в оптимизаторе**, а в **неправильных данных/статистике/индексах**.

---

## Что делать, если PostgreSQL выбирает неправильный план?

Вместо `FORCE INDEX` используйте следующие методы:

### 1. Обновите статистику (Самое частое решение)
Если план не оптимален, скорее всего, статистика устарела.

```sql
ANALYZE table_name;
-- Или для всей БД:
ANALYZE;
```

После этого оптимизатор пересмотрит стоимость `Seq Scan` vs `Index Scan`.

### 2. Пересмотрите индекс
Может быть, вам нужен **другой индекс**, который оптимизатор сможет выбрать.

**Пример:**
Вы ищете по `LOWER(email)`. Индекс на `email` не используется.
**Решение:** Создайте функциональный индекс.

```sql
CREATE INDEX idx_users_email_lower ON users(LOWER(email));
```

Теперь оптимизатор увидит, что этот индекс подходит, и выберет его.

### 3. Подсказки через комментарии (HINTS) — Экспериментально
В PostgreSQL **нет встроенных HINTs** как в Oracle. Но есть расширение **`pg_hint_plan`**.

#### Установка:
```sql
CREATE EXTENSION pg_hint_plan;
```

#### Использование:
Вы добавляете комментарий к запросу:

```sql
SELECT /*+ IndexScan(users idx_users_email_lower) */ *
FROM users
WHERE LOWER(email) = 'test@example.com';
```

Доступные HINTs:
- `IndexScan` — принудительный `Index Scan`.
- `SeqScan` — принудительный `Seq Scan`.
- `HashJoin`, `NestedLoop`, `MergeJoin` — выбор типа JOIN-а.
- `Leading` — порядок JOIN-ов.

**⚠️ Важно:**
- Это **входит в код**, что затрудняет поддержку.
- HINTs могут сломаться при изменении плана.
- Используйте только в **критических** случаях, когда другие методы не помогли.

### 4. Настройка параметров оптимизатора (GUC)
Вы можете временно изменить параметры, которые влияют на решение оптимизатора.

**Пример:** Увеличить `work_mem`, чтобы оптимизатор предпочел `Hash Join` вместо `Nested Loop`.

```sql
SET work_mem = '256MB';
SELECT ...;
```

**⚠️ Важно:**
- `work_mem` выделен **на каждый сортирующий/хеширующий узел** и **на каждое соединение**.
- Не ставьте большие значения глобально — можно исчерпать память.

### 5. Переписывание запроса
Иногда проблема в том, как написан запрос.

**Пример:**
Вместо подзапроса в `WHERE` используйте `JOIN`.
Вместо `OR` используйте `UNION`.

Оптимизатор лучше работает с предсказуемыми структурами.

---

## Сравнение с MySQL

| Функциональность | MySQL | PostgreSQL |
| :--- | :--- | :--- |
| `FORCE INDEX` | Есть | **Нет** |
| HINTs | Нет | Есть (через `pg_hint_plan`) |
| Стоимость оптимизатора | Простая (base table size) | Сложная (I/O, CPU, memory) |
| Статистика | `ANALYZE TABLE` | `ANALYZE` |
| Функциональные индексы | Нет | **Да** |

---

## Итог

1. **`FORCE INDEX` в PostgreSQL не существует.**
2. **Если план неправильный:**
   - Сделайте `ANALYZE`.
   - Проверьте, есть ли подходящий индекс (возможно, нужен функциональный).
   - Рассмотрите `pg_hint_plan` (экспериментально).
   - Перепишите запрос.
   - Подстройте `work_mem` (аккуратно).
3. **Главный принцип:** Не обходите оптимизатор, а **помогайте ему** правильными индексами и статистикой.

> ⏱ 10.7s · prompt 69107 tok · output 1238 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

**`strict_types=1` не замедляет выполнение кода.**

Напротив, в некоторых сценариях он может даже **ускорить** выполнение или не оказывать **измеримого** влияния на производительность.

### Почему так?

#### 1. Проверка типов происходит **однократно**
Проверка типов аргументов происходит в момент **вызова функции** (dispatch), а не в каждом цикле или внутри тела функции.

- В режиме `strict_types=1` PHP проверяет, совпадает ли тип аргумента с объявленным **точно**.
- В режиме `strict_types=0` (coerced) PHP проверяет, можно ли **привести** тип к целевому.

**Разница в скорости:**
- `strict_types=1`: `if (gettype($arg) === 'string')` — быстрая проверка.
- `strict_types=0`: `if (is_string($arg) || is_numeric($arg) || ...)` — более сложная логика, так как PHP должен проверить возможность приведения.

**Вывод:** `strict_types=1` может быть **немного быстрее**, так как проверка проще.

#### 2. Отсутствие приведения типов (Coercion)
В режиме `strict_types=0` PHP выполняет **приведение типов** (coercion), если тип не совпадает, но может быть преобразован.

**Пример:**
```php
// strict_types=0
function add(int $a, int $b): int {
    return $a + $b;
}
add('10', '20'); // Строки '10' и '20' приведены к int 10 и 20.
```

**Процесс приведения:**
1. Проверить, является ли строка числом.
2. Преобразовать строку в целое число.
3. Выполнить сложение.

**В `strict_types=1`:**
```php
// strict_types=1
function add(int $a, int $b): int {
    return $a + $b;
}
add('10', '20'); // TypeError!
```

**Процесс:**
1. Проверить, является ли аргумент `int`.
2. Если нет — бросить `TypeError`.

**Вывод:** В `strict_types=1` **нет накладных расходов на приведение типов**. Если код написан корректно (все типы совпадают), приведение не происходит. Если код написан некорректно — `strict_types=1` **падает быстрее**, чем `strict_types=0`, который пытается привести типы.

#### 3. JIT-компилятор (PHP 8.0+)
JIT-компилятор PHP оптимизирует код, в том числе проверки типов.

- В режиме `strict_types=1` проверки типов могут быть **inlined** (встроены) в сгенерированный машинный код.
- В режиме `strict_types=0` JIT должен генерировать код, который обрабатывает **множество возможных типов** (int, float, string, object), что усложняет оптимизацию.

**Вывод:** JIT может **лучше оптимизировать** код с `strict_types=1`.

#### 4. Реальные бенчмарки
В бенчмарках (например, на `phpbench` или `blackfire`) разница в производительности между `strict_types=1` и `strict_types=0` **в пределах погрешности** (0–2%).

- Если типы корректны — разница незаметна.
- Если типы некорректны — `strict_types=1` **падает быстрее**, чем `strict_types=0` (так как не тратит время на приведение).

### Когда `strict_types=1` может «замедлить»?

**Только в одном случае:**
Если ваш код **намеренно** использует приведение типов (coercion) и вы **не хотите** получать `TypeError`.

**Пример:**
```php
// strict_types=0
function calculate(string $value): int {
    return (int) $value; // Намеренное приведение
}
```

В этом случае `strict_types=1` **запретит** вызов `calculate('10abc')`, а `strict_types=0` **разрешит**.

**Но это не замедление, это изменение поведения.**

### Итог

| Сценарий | `strict_types=0` | `strict_types=1` |
| :--- | :--- | :--- |
| **Корректные типы** | Ноль накладных расходов | Ноль накладных расходов |
| **Некорректные типы** | Затраты на приведение | Быстрая ошибка `TypeError` |
| **JIT-оптимизация** | Сложнее оптимизировать | Легче оптимизировать |
| **Общая производительность** | ≈ 100% | ≈ 100% (или немного быстрее) |

**Вывод:**
- **`strict_types=1` не замедляет код.**
- **Он делает код предсказуемым и безопасным.**
- **Единственный «минус» — вы получаете `TypeError` вместо «тихого» приведения, что является **фичей**, а не багом.

**Рекомендация:** Всегда используйте `declare(strict_types=1);` в каждом PHP-файле. Это не повлияет на производительность, но защитит вас от скрытых ошибок.

> ⏱ 11.2s · prompt 70385 tok · output 1248 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Утверждение, что Doctrine ORM **не поддерживает** составные первичные ключи (Composite Keys), является **частично неверным**.

### Факт:
**Doctrine 2.x поддерживает составные первичные ключи** с помощью аннотаций/атрибутов `#[ORM\Id]` на нескольких полях.

**Однако**, поддержка **ограничена** и имеет много ограничений, из-за которых многие разработчики избегают их использования.

---

## 1. Как это работает в Doctrine?

### Пример: Составной ключ `user_id + product_id`

```php
#[ORM\Entity]
class UserProductPreference
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'string')]
    private string $category;
}
```

### Ограничения:

1. **Нет `#[ORM\GeneratedValue]`**
   - Нельзя сгенерировать ID автоматически.
   - Оба поля должны быть заданы явно.

2. **Сложности с `find()`**
   ```php
   // ❌ Не работает
   $repo->find(123); 
   
   // ✅ Работает
   $repo->findOneBy(['userId' => 123, 'productId' => 456]);
   ```

3. **Проблемы с `ManyToMany`**
   - Если таблица связи (join table) имеет составной ключ, Doctrine не может корректно работать с каскадами и orphanRemoval.
   - **Рекомендация:** Для `ManyToMany` используйте отдельную сущность-мост (Entity-bridge) с **своим ID**.

4. **Кэширование (First-Level Cache)**
   - Doctrine кэширует сущности по их первичному ключу.
   - При составном ключе ключ кэша — это **массив** `[userId, productId]`.
   - Это работает, но сложнее для отладки и менее производительное, чем один `int`.

5. **Сериализация**
   - При сериализации сущности (например, в Redis) составной ключ нужно вручную сериализовать.

6. **Сравнение и равенство**
   - `===` не работает как обычно. Нужно сравнивать оба поля.

---

## 2. Почему многие избегают составных ключей?

### Причина 1: Сложность работы с ORM
- `find()`, `flush()`, `refresh()` работают неудобно.
- Сложно писать DQL-запросы с составными ключами.
- Проблемы с `ManyToMany`.

### Причина 2: Производительность
- Составной ключ занимает больше места в индексе.
- БД медленнее сравнивает несколько колонок, чем одну.

### Причина 3: Сложность для новых разработчиков
- Новички не понимают, как работает кэширование и сравнение.

### Причина 4: Тестирование
- Тесты с составными ключами сложнее.

---

## 3. Как живут в реальных проектах?

### Вариант A: Использовать составной ключ (если нет выбора)

**Когда:**
- Таблица связи (`ManyToMany`) с дополнительными полями.
- Исторические данные, которые нельзя изменить.

**Как:**
- Использовать `findOneBy()` вместо `find()`.
- Писать DQL-запросы вручную.
- Избегать `ManyToMany`, если есть составной ключ.

---

### Вариант B: Использовать суррогатный ключ (Surrogate Key) — **Рекомендуемый**

**Когда:**
- Новая разработка.
- Таблица связи.
- Любая сущность, где составной ключ создаёт проблемы.

**Как:**
- Добавить поле `id` (`UUID` или `INT AUTO_INCREMENT`).
- Сделать `UNIQUE` на составной паре полей.

```php
#[ORM\Entity]
class UserProductPreference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\UniqueConstraint(name: 'unique_user_product')]
    #[ORM\Column(type: 'string')]
    private string $category;
}
```

**Плюсы:**
- `find()` работает.
- Кэширование работает.
- `ManyToMany` работает.
- Проще для разработчиков.

**Минусы:**
- Меньше место в индексе.
- Меньше производительность (но не критично).

---

### Вариант C: Использовать Value Object + Custom Type

**Когда:**
- Составной ключ — это **значения**, а не сущность.
- Пример: `CountryCode + ZipCode` как адрес.

**Как:**
- Создать `ValueObject` (например, `Address`).
- Использовать `Custom Doctrine Type` для маппинга.

```php
final class Address
{
    public function __construct(
        public string $countryCode,
        public string $zipCode
    ) {}
}

// В сущности:
#[ORM\Column(type: 'address')]
private ?Address $address;
```

**Плюсы:**
- Инкапсуляция.
- Простота работы.
- Нет составного ключа в БД (храним как JSON или строку).

---

## 4. Сравнение подходов

| Подход | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Составной ключ** | Таблицы связи, исторические данные | Нет лишнего ID | Сложность работы с ORM |
| **Суррогатный ключ** | Новая разработка, большинство случаев | Простота, производительность | Меньше место |
| **Value Object** | Значения, не сущности | Инкапсуляция, чистота домена | Сложнее маппинг |

---

## Итог

1. **Doctrine поддерживает составные ключи**, но это **сложно** и **неудобно**.
2. **Рекомендация:** Используйте **суррогатный ключ** (`id`) + `UNIQUE` на бизнес-колонках.
3. **Исключение:** Таблицы связи (`ManyToMany`) с дополнительными полями — используйте отдельную сущность-мост с `id`.
4. **Для значений** (адрес, координаты) — используйте **Value Object** + **Custom Type**.

**Помните:** ORM — это инструмент. Если он не любит составные ключи — лучше дать ему то, что он любит (простые ID).

> ⏱ 12.8s · prompt 71671 tok · output 1576 tok

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

**Кандидат:**

Это классический пример «спагетти-кода», который нарушает практически все принципы чистой архитектуры и безопасности. Я вижу **критические уязвимости** и **архитектурные ошибки**.

Давайте разберем по порядку.

---

## 1. Критические проблемы (Critical)

### 🔴 1. SQL Injection (SQL-injection)
```php
$pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
$pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");
```
**Проблема:** `$id` вставляется в SQL-запрос напрямую. Злоумышленник может передать `1 OR 1=1` и изменить все заказы или удалить таблицу.
**Решение:** Всегда использовать **Prepared Statements**.

### 🔴 2. Race Condition (Гонка условий)
```php
SELECT * FROM orders WHERE id = $id
UPDATE orders SET status = 'paid' WHERE id = $id
```
**Проблема:** Между SELECT и UPDATE может произойти другое изменение (например, отмена заказа). Или два запроса на оплату придут одновременно — оба прочитают `status != 'paid'` и оба запишут `paid`. Это нарушает бизнес-инвариант.
**Решение:** Атомарное обновление с проверкой состояния:
```sql
UPDATE orders SET status = 'paid' WHERE id = :id AND status != 'paid'
```
Или использовать `Optimistic Locking`.

### 🔴 3. Нарушение SRP (Single Responsibility Principle)
Один метод делает:
1. Парсинг запроса.
2. Валидацию ID.
3. Запрос к БД.
4. Изменение статуса.
5. Отправку письма.
6. Логирование.
7. Формирование ответа.

**Решение:** Разделить на слои (Controller → UseCase → Repository → Mailer).

### 🔴 4. Нарушение инкапсуляции домена
Логика «заказ нельзя оплатить дважды» или «заказ должен существовать» отсутствует.
**Решение:** Перенести бизнес-логику в доменную сущность `Order`.

---

## 2. Средние проблемы (Medium)

### 🟡 5. Отсутствие валидации
- `$id` не проверяется на наличие.
- `$id` не проверяется на тип (может быть строка, массив, null).
- `$order['email']` может быть пустым или не содержать `@`.

### 🟡 6. Отсутствие обработки ошибок
- Что, если `Mailer::send()` упадет?
- Что, если БД недоступна?
- Сейчас пользователь получит 500 ошибку, но заказ может быть оплачен (или нет — состояние неопределенное).

### 🟡 7. Логирование
- `(new Logger())->log('paid ' . $id);` — слабое сообщение. Нет контекста (кто оплатил, когда, какой метод оплаты).
- Логгер должен быть внедрен, а не создан внутри метода.

### 🟡 8. `SELECT *`
- Загружаются все колонки таблицы, даже не нужные.
- Если в таблице есть чувствительные данные (токены, пароли) — они попадают в память.

---

## 3. Как бы я переделал?

### Архитектура:
```
[Controller] 
    → [UseCase: PayOrder]
        → [Domain: Order]
        → [Repository: OrderRepository]
        → [Event Dispatcher]
            → [Handler: SendPaymentEmail]
```

### Шаг 1: Домен (Order)

```php
class Order
{
    public function __construct(
        private readonly string $id,
        private string $status, // 'PENDING', 'PAID', 'CANCELLED'
        private readonly string $customerEmail
    ) {}

    public function pay(): void
    {
        if ($this->status === OrderStatus::PAID) {
            throw new DomainException('Order is already paid');
        }
        
        if ($this->status !== OrderStatus::PENDING) {
            throw new DomainException('Only pending orders can be paid');
        }

        $this->status = OrderStatus::PAID;
        // Здесь можно добавить событие, но лучше использовать Event Dispatcher в UseCase
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }
    
    // Getters...
}
```

### Шаг 2: Репозиторий (Repository)

```php
interface OrderRepositoryInterface
{
    public function findById(string $id): ?Order;
    public function save(Order $order): void;
}

class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findById(string $id): ?Order
    {
        $entity = $this->em->getRepository(OrderEntity::class)->find($id);
        return $entity ? OrderMapper::fromEntity($entity) : null;
    }

    public function save(Order $order): void
    {
        $entity = OrderMapper::toEntity($order);
        $this->em->persist($entity);
        $this->em->flush();
    }
}
```

### Шаг 3: UseCase (Приложение)

```php
class PayOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(string $orderId): Order
    {
        $order = $this->orderRepo->findById($orderId);
        
        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        // Бизнес-логика в домене
        $order->pay();

        // Сохранение
        $this->orderRepo->save($order);

        // Публикация события (асинхронно)
        $this->eventDispatcher->dispatch(new OrderPaidEvent($orderId));

        return $order;
    }
}
```

### Шаг 4: Controller (Адаптер HTTP)

```php
class OrderController
{
    public function __construct(private PayOrderUseCase $payOrderUseCase) {}

    public function pay(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query->get('order_id');
            
            if (!$orderId) {
                return $this->json(['error' => 'Order ID is required'], 400);
            }

            $order = $this->payOrderUseCase->execute($orderId);
            
            return $this->json(['message' => 'Order paid successfully']);
            
        } catch (OrderNotFoundException $e) {
            return $this->json(['error' => 'Order not found'], 404);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Шаг 5: Обработчик события (Email)

```php
class SendPaymentEmailHandler
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(OrderPaidEvent $event): void
    {
        // Получаем email из репозитория или из события
        $order = $this->orderRepo->findById($event->orderId);
        
        $this->mailer->send(
            $order->getCustomerEmail(),
            'Order Paid',
            'Thank you for your purchase!'
        );
    }
}
```

---

## 4. С чего начал бы и почему?

### Порядок действий:

1. **Исправить SQL Injection.**
   - Это критическая уязвимость. Если не исправить сейчас — завтра будут утечки данных.

2. **Разделить слои.**
   - Вынести бизнес-логику из контроллера в UseCase и Domain.
   - Это нужно, чтобы можно было тестировать логику без HTTP и БД.

3. **Ввести транзакции/блокировки.**
   - Гарантировать атомарность операции оплаты.

4. **Добавить обработку ошибок.**
   - Чтобы пользователь получил понятное сообщение, а не 500 ошибку.

5. **Добавить логирование.**
   - Для отладки и аудита.

6. **Написать тесты.**
   - Unit-тесты для `Order::pay()`.
   - Интеграционные тесты для `PayOrderUseCase`.
   - API-тесты для `OrderController`.

### Почему именно такой порядок?

1. **Безопасность прежде всего.** SQL Injection — это дыра в стене. Сначала заделываем дыру.
2. **Архитектура.** Без разделения слоев невозможно добавить блокировки и обработку ошибок правильно.
3. **Надежность.** Транзакции и ошибки — это фундамент стабильности.
4. **Качество.** Логирование и тесты — это финальный полиш, который делает код профессиональным.

---

## Итог

| Проблема | Было | Стало |
| :--- | :--- | :--- |
| **SQL Injection** | `WHERE id = $id` | Prepared Statements |
| **Race Condition** | SELECT + UPDATE | Атомарное UPDATE с условием |
| **SRP** | 1 метод на всё | Controller → UseCase → Domain |
| **Безопасность** | Нет валидации | Валидация + обработка ошибок |
| **Тестируемость** | Невозможно тестировать | Легко тестировать (мок-и) |

**Главный вывод:** Код был написан «быстро и грязно». Теперь он стал **безопасным, тестируемым и расширяемым**.

> ⏱ 17.5s · prompt 73458 tok · output 2254 tok

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

**Кандидат:**

Этот код содержит **фундаментальные нарушения** принципов DDD и чистой архитектуры. Он превращает доменную сущность в «интеллектуальный ORM-объект», который знает о инфраструктуре.

Разберем проблемы по порядку.

---

## 1. Критические проблемы

### 🔴 1. Зависимость домена от инфраструктуры (NARU)
```php
private EntityManagerInterface $em
```
**Проблема:**
- `EntityManagerInterface` — это часть **инфраструктуры** (Doctrine/ORM).
- Домен **не должен** знать об ORM, БД или фреймворках.
- Это нарушает **Зависимое введение (Dependency Inversion Principle)**: домен зависит от конкретного инструмента, а не от абстракции.

**Последствия:**
- Невозможно протестировать `User` без подключения к БД.
- Невозможно заменить Doctrine на другую ORM.
- Нарушена инкапсуляция: сущность сама управляет своим сохранением.

### 🔴 2. Нарушение Single Responsibility Principle (SRP)
```php
$this->em->flush();
```
**Проблема:**
- Сущность отвечает за **состояние** (email) и за **сохранение** (flush).
- Сохранение — это **инфраструктурная задача**.
- Сущность не должна знать, что такое «запись в БД».

### 🔴 3. Отсутствие инвариантов (Business Rules)
```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();
}
```
**Проблема:**
- Нет валидации: можно задать `""`, `"not-an-email"`, `"user@domain"`.
- Нет проверки уникальности (хотя это обычно проверка на уровне репозитория/сервиса, но базовая валидация формата — задача сущности).
- Нет проверки, что старый email не был подтвержден (если такая бизнес-логика есть).

### 🔴 4. Нарушение инкапсуляции
- `private string $email` — хорошо.
- Но `$em` тоже `private`, однако он доступен через `flush()`, что создает «дыру» в инкапсуляции: любой, кто имеет доступ к объекту, может вызвать `flush()`.

---

## 2. Проблемы посередине

### 🟡 5. Отсутствуют Domain Events
- Смена email — это важное событие в домене.
- Другие части системы (например, уведомление, перепроверка уникальности) должны реагировать на это событие.
- Сейчас ничего не происходит после `flush()`.

### 🟡 6. Отсутствует Value Object для Email
- `email` — это не просто строка, это **Value Object** со своими правилами (формат, нижний регистр, нормализация).
- Хранить его как `string` — потеряно смысл.

---

## 3. Как исправить?

### Шаг 1: Создать Value Object `Email`

```php
final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $this->validateAndNormalize($value);
    }

    private function validateAndNormalize(string $value): string
    {
        $normalized = strtolower(trim($value));
        
        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email format');
        }
        
        return $normalized;
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

### Шаг 2: Пересоздать сущность `User`

```php
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'email')] // Кастомный тип или string
    private string $email;

    private array $domainEvents = [];

    // ВНИМАНИЕ: Нет EntityManager в конструкторе!
    public function __construct(Email $email)
    {
        $this->email = $email->value();
    }

    public function changeEmail(Email $newEmail): void
    {
        if ($newEmail->equals($this->getEmail())) {
            return; // Не меняем, если email тот же
        }

        $this->email = $newEmail->value();
        $this->record(new EmailChangedEvent($this->id, $newEmail->value()));
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    private function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return object[] */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

### Шаг 3: Создать UseCase (Приложение)

```php
class ChangeUserEmailUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private EmailValidator $emailValidator // Если нужна сложная валидация
    ) {}

    public function execute(int $userId, string $newEmail): void
    {
        $user = $this->userRepo->findById($userId);
        
        if ($user === null) {
            throw new UserNotFoundException($userId);
        }

        // Валидация на уровне приложения (уникальность и т.д.)
        $existingUser = $this->userRepo->findByEmail($newEmail);
        if ($existingUser !== null && $existingUser->getId() !== $userId) {
            throw new EmailAlreadyTakenException($newEmail);
        }

        // Бизнес-логика в домене
        $user->changeEmail(new Email($newEmail));

        // Сохранение (инфраструктура)
        $this->userRepo->save($user);

        // Публикация событий
        foreach ($user->releaseEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
```

### Шаг 4: Репозиторий (Инфраструктура)

```php
class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em // Теперь EM здесь, а не в домене!
    ) {}

    public function save(User $user): void
    {
        $entity = UserMapper::toEntity($user);
        $this->em->persist($entity);
        $this->em->flush();
    }
    
    // ... другие методы
}
```

---

## 4. Сравнение «Было» vs «Стало»

| Аспект | Было | Стало |
| :--- | :--- | :--- |
| **Зависимость от ORM** | Да (`EntityManager` в сущности) | Нет (ORM только в репозитории) |
| **SRP** | Сущность + сохранение | Только состояние |
| **Валидация** | Отсутствует | `Email` Value Object |
| **Тестируемость** | Нужна БД | Чистый PHP |
| **Расширяемость** | Сложно добавить новые правила | Легко (Value Object, события) |
| **Инкапсуляция** | Протекла (flush) | Строгая |

---

## 5. Почему так лучше?

1. **Домен чистый:** `User` можно протестировать без БД.
2. **Инкапсуляция:** Сущность не знает, как сохраняется.
3. **Расширяемость:** Можно добавить новую бизнес-логику (например, «email должен быть подтвержден») без изменения инфраструктуры.
4. **Безопасность:** Валидация через `Email` гарантирует, что невалидный email не попадет в БД.
5. **События:** Другие части системы могут реагировать на смену email (отправить письмо о смене, обновить поисковый индекс).

---

## Итог

**Главная ошибка:** Доменная сущность не должна знать об ORM. Она должна знать только о своем состоянии и бизнес-правилах.

**Правильный порядок:**
1. Домен (User, Email) — чистый PHP.
2. Приложение (UseCase) — оркестрация.
3. Инфраструктура (Repository) — работа с БД.

Тогда код становится **тестируемым, понятным и поддерживаемым**.

> ⏱ 15.8s · prompt 75862 tok · output 2061 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Работа с незнакомым стеком — это не тест знания, а тест **инженерных навыков, дисциплины и скорости обучения**. Моя стратегия построена на принципах **«спринт на понимание»**, **«минимальный воспроизводимый пример»** и **«умная эскалация»**.

Вот моя последовательность действий.

---

## 1. Фаза 0: Разведка (15–30 минут)

**Цель:** Понять контекст, терминологию и ограничители.

### Что делаю:
1. **Читаю задачу досконально.**
   - Выделяю ключевые слова: технологии, версии, симптомы.
   - Определяю, что **уже пробовали** (если в тикете есть лог).
   - Выясняю, **кто использует** этот код и **какой ущерб** наносит баг.

2. **Ищу контекст в репозитории.**
   - `README.md`, `CONTRIBUTING.md`, `docs/`.
   - `composer.json`, `package.json`, `Dockerfile` — чтобы понять версии и окружение.
   - Ищу похожие задачи в истории коммитов (`git log --grep="баг-симптом"`).

3. **Спрашиваю у команды (короткий вопрос).**
   - «Кто последний раз работал с этим модулем? Есть ли документация или известные подводные камни?»
   - **Почему:** 5 минут разговора могут сэкономить 2 часа гугления.

---

## 2. Фаза 1: Воспроизведение (1–3 часа)

**Цель:** Создать минимальный пример, где баг проявляется.

### Что делаю:
1. **Поднимаю окружение.**
   - `docker compose up`, `npm install`, `composer install`.
   - Проверяю, что приложение работает локально.

2. **Воспроизвожу баг.**
   - Пишу тест (если есть фреймворк) или скрипт, который точно повторяет проблему.
   - **Важно:** Если баг воспроизводится только в проде — ищу способы эмуляции (mock-и, логирование).

3. **Читаю стек вызовов (Stack Trace).**
   - Если есть ошибка — анализирую каждую строку.
   - Если нет ошибки — добавляю логирование в подозрительные места.

4. **Использую инструменты отладки.**
   - `var_dump()`, `xdebug`, `console.log()`, `network tab` в браузере.
   - Профилировщики (Blackfire, WebStorm, Chrome DevTools).

---

## 3. Фаза 2: Гипотезы и Поиск (3–6 часов)

**Цель:** Сформулировать 3–5 гипотез и проверить их.

### Метод «Узкого горлышка»:
1. **Ограничиваю область поиска.**
   - Баг в валидации? В бизнес-логике? В инфраструктуре?
   - Если баг в API — смотрю контроллеры.
   - Если в UI — смотрю фронтенд.

2. **Формулирую гипотезы:**
   - Гипотеза 1: «Баг в неправильном порядке выполнения событий».
   - Гипотеза 2: «Баг в неправильной конфигурации переменных окружения».
   - Гипотеза 3: «Баг в зависимости (версия библиотеки)».

3. **Проверяю гипотезы методом научного тыка.**
   - Меняю **одну** переменную за раз.
   - Проверяю результат.
   - Отматываю и проверяю следующую гипотезу.

4. **Ищу документацию.**
   - **Официальная документация** — первый источник.
   - **Исходный код** библиотеки (если баг не наш, а их).
   - **GitHub Issues** — часто кто-то уже решал эту проблему.
   - **Статьи и блоги** — ищи с точной формулировкой ошибки.

---

## 4. Фаза 3: Решение и Валидация (2–4 часа)

**Цель:** Найти корневую причину и применить фикс.

### Что делаю:
1. **Правлю код.**
   - Минимально возможное изменение.
   - Не «лечу симптом», а устраняю причину.

2. **Пишу тест.**
   - Тест, который **был бы упав** перед фиксом и **проходит** после.
   - Это защита от регрессии.

3. **Проверяю на разных окружениях.**
   - Локально.
   - На staging (если есть).
   - На разных браузерах/устройствах (если это фронтенд).

4. **Документирую.**
   - Записываю в тикет: **причина**, **решение**, **профилактика**.
   - Обновляю документацию, если нужно.

---

## 5. Когда просить помощи?

Это **критически важно**. Я не жду, пока сгорю дедлайн.

### 🔴 **Немедленно зову помощь, если:**

1. **Блокировка.**
   - Я не могу продвинуться более 3–4 часов.
   - Я не понимаю, **как** работает технология, а не **где** баг.

2. **Критичный баг в проде.**
   - Если баг блокирует пользователей и решение не найдено за 2 часа — эскалирую.

3. **Нет доступа.**
   - Нет прав к БД, к логам, к staging-окружению.

4. **Слишком сложная система.**
   - Если система имеет 10+ зависимостей и я не вижу «картины целиком».

### 🟡 **Проконсультируюсь, если:**

1. **У меня есть гипотеза, но нет уверенности.**
   - «Думаю, что баг в X. Можете посмотреть, правильно ли я понимаю?»

2. **Решение требует архитектурного изменения.**
   - Если фикс сломает другие части системы — нужно обсуждение.

3. **Я тратил 6+ часов на одну гипотезу.**
   - Время дороже эго. Лучше спросить.

### 🟢 **Разбираюсь сам, если:**

1. **Баг локализован.**
   - Я знаю, в каком файле и функции он находится.

2. **Есть документация/исходники.**
   - Я могу прочитать код библиотеки.

3. **Время позволяет.**
   - Дедлайн за 3–5 дней, а не 3 часа.

---

## 6. Оценка сроков

Я оцениваю сроки **по частям**, а не «на глаз».

| Этап | Время | Комментарий |
| :--- | :--- | :--- |
| Разведка | 0.5 ч | Контекст, окружение |
| Воспроизведение | 2 ч | Минимальный пример |
| Поиск причины | 4 ч | Гипотезы, отладка |
| Фикс + тесты | 2 ч | Код + валидация |
| **Итого** | **~9 ч** | **1 рабочий день** |

**Если задача сложнее:**
- Я разбиваю на подзадачи.
- Оцениваю каждую по отдельности.
- Добавляю **буфер 20–30%** на непредвиденное.

**Если задача не решается за 1 день:**
- Я создаю **временный workaround** (если есть).
- Пишу **детальный отчёт** о прогрессе.
- Обсуждаю с командой, нужно ли **выделять больше времени** или **привлекать других**.

---

## 7. Менталитет

1. **Незнание — это нормально.**
   - Важно **скорость**, с которой ты учишься, а не знание с рождения.

2. **Документируй процесс.**
   - Что попробовал, что не сработало, почему.
   - Это помогает и мне, и команде, если я уйду.

3. **Запрашивай помощь рано.**
   - 30 минут разговора с опытным коллегой часто заменяют 4 часа гугления.

4. **Не стесняйся сказать «я не знаю».**
   - Лучше «Я не знаю, но я изучу это до конца дня» чем «Я подумаю».

---

## Итог

Мой подход к незнакомому стеку:
1. **Контекст** → **Воспроизведение** → **Гипотезы** → **Фикс** → **Тесты**.
2. **Время-бокс** для каждой фазы.
3. **Эскалация** при блокировке или критичности.
4. **Документация** и **обратная связь** для команды.

Это позволяет решать незнакомые задачи **предсказуемо** и **профессионально**.

> ⏱ 20.5s · prompt 77996 tok · output 2110 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Я **немедленно** эскалирую проблему. Не жду до вечера, не пытаюсь «вжать» в оставшееся время за счет качества или сна, и тем более не молчу, надеясь, что кто-то сам спросит.

Вот мой точный алгоритм действий.

---

### 1. Оценка ситуации (15 минут)

Прежде чем писать письмо/сообщение, я честно отвечаю себе на 3 вопроса:

1. **Почему срок сорвался?**
   - *Честный ответ:* Я недооценил сложность X (например, не учел миграцию данных, не предвидел баг в зависимости, не понял специфику легаси-кода).
   - *Нечестный ответ (который я отбрасываю):* «Я просто мало работал», «Меня отвлекали» (если это не форс-мажор).

2. **Что уже сделано?**
   - Процент выполнения.
   - Есть ли работающий код, или я в «болоте»?
   - Что можно показать как результат (хоть частичный)?

3. **Что конкретно мешает?**
   - Блокировка (нет доступа, жду ответ от внешнего сервиса).
   - Недостаток знаний (нужно изучать новый фреймворк).
   - Сложность (алгоритм сложнее, чем казалось).
   - Объем (оказалось, что нужно переделать 10 модулей, а не 1).

---

### 2. Формулировка сообщения

Я пишу **одному** человеку: **тид-лиду** или **проект-менеджеру** (кто является моим непосредственным контактом).

**Формула сообщения:**
> **Контекст + Проблема + Оценка + Решение + Вопрос**

#### Пример сообщения:

> «Привет, [Имя]!
>
> **Статус задачи #123 (Интеграция с платежным шлюзом):**
> Я обнаружил, что задача займет **2 дня вместо обещанного 1**.
>
> **Причина:**
> По ходу работы выяснилось, что платежный шлюз возвращает ошибки в нестандартном JSON-формате, и наша текущая парсинг-логика не обработала 3 крайних случая (timeout, decline, pending). Я изначально считал, что их будет 1.
>
> **Что уже сделано:**
> - ✅ Базовая интеграция работает.
> - ✅ Добавлена обработка успешных платежей.
> - 🚧 Осталось: обработка ошибок, ретраи, тесты.
>
> **Срок:**
> Завершу **завтра до 15:00** (вместо сегодня).
>
> **Предложение:**
> Если есть критическая зависимость от этой фичи сегодня, могу вынести в отдельный PR только базовую часть (без обработки ошибок), а ошибки допилю завтра. Но это создаст техдолг.
>
> Что думаешь?»

---

### 3. Почему именно так?

| Действие | Зачем |
| :--- | :--- |
| **Не жду вечера** | Чем раньше узнают, тем раньше могут перестроить план. |
| **Говорю честно** | Доверие дороже одного срыва дедлайна. Если я скажу «сделаю к 23:59», а не сделаю — потеряю доверие навсегда. |
| **Объясняю причину** | Показываю, что проблема объективная, а не лень. |
| **Даем альтернативу** | Показываю, что я думаю о бизнесе, а не только о своем коде. |
| **Фиксирую новый срок** | Убираю неопределенность. |

---

### 4. Чего я НЕ делаю

#### ❌ 1. Не молчу
«А вдруг получится к вечеру?» — это игра в рулетку. Если не получится — сорву дедлайн **и** потеряю доверие. Двойной проигрыш.

#### ❌ 2. Не режу уголки молча
«Сделаю быстрее, но уберу тесты/документацию/обработку ошибок».
- Это **технический долг**.
- Через неделю кто-то будет чинить мой «быстрый» код.
- Я создаю проблему для команды.

#### ❌ 3. Не ищу виноватых
«Это потому, что ТЗ было неправильное».
- Даже если ТЗ было плохое — я его принял.
- Моя ответственность — выявить проблему.
- Обвинения отталкивают от сотрудничества.

#### ❌ 4. Не спрашиваю «А можно перенести?»
Я **предлагаю** новый срок, а не спрашиваю разрешения.
- «Завтра до 15:00» — это решение.
- «А можно перенести?» — это перекладывание ответственности.

---

### 5. После отправки сообщения

1. **Продолжаю работать.**
   - Не жду ответа.
   - Делаю то, что могу (тесты, рефакторинг, документацию).

2. **Если ответ «нужно сегодня»:**
   - Обсуждаем **MVP** (минимально жизнеспособную версию).
   - Фиксируем, что **не** входит в MVP.
   - Делаю именно это.

3. **Если ответ «ок, завтра»:**
   - Успокаиваюсь.
   - Работаю сфокусированно.
   - В конце дня отправляю промежуточный статус (если это большая задача).

---

### 6. Урок на будущее

После этой ситуации я задаю себе вопрос:
> *«Почему я недооценил задачу?»*

Если я не могу ответить — значит, моя оценка задач была слишком оптимистичной. В следующий раз:
- Буду задавать **больше уточняющих вопросов** до оценки.
- Буду добавлять **буфер 20–30%** на непредвиденное.
- Буду **спрашивать помощь** на этапе анализа, а не на этапе реализации.

---

### Итог

Срыв дедлайна — это **нормально**.
Скрыть срыв дедлайна — это **крах**.

**Мой принцип:** 
> *«Лучше 10 честных сорванных сроков, чем 1 скрытый обман. Доверие восстанавливается медленно, но разрушается мгновенно».*

> ⏱ 16.5s · prompt 80149 tok · output 1498 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это один из самых важных навыков в инженерии: **управление техническим долгом и скоростью**.

Я решаю этот вопрос не на основе чувства «мне нравится/не нравится», а через **объективные критерии** и **контекст задачи**.

---

## 1. Критерии «Достаточно хорошо» (Definition of Done)

Код считается готовым, когда выполнены **все** пункты из этого списка. Если что-то не выполнено — код не готов.

### ✅ Обязательные критерии:
1. **Функциональность:** Код делает то, что описано в задачах.
2. **Тесты:**
   - Unit-тесты покрывают новые/измененные бизнес-правила.
   - Интеграционные тесты проверяют взаимодействие с БД/внешними сервисами.
   - Нет критических багов в сценариях «happy path» и «error path».
3. **Безопасность:**
   - Нет SQL Injection, XSS, CSRF.
   - Проверены права доступа (кто видит, кто меняет).
4. **Производительность:**
   - Нет очевидных N+1 запросов.
   - Нет утечек памяти.
   - Время отклика в рамках требований (например, < 300ms).
5. **Читаемость:**
   - Код понятен без комментариев.
   - Имена отражают намерение.
   - Нет дублирования кода (DRY).
6. **Логирование:**
   - Критические операции логируются.
   - Ошибки логируются с контекстом.
7. **Документация:**
   - Обновлены API-контракты (если менялись).
   - Обновлены инструкции по установке/использованию (если нужно).

### ❌ Необязательные критерии (можно отложить):
- Идеальное имя переменной.
- Рефакторинг старого кода, не связанный с задачей.
- Оптимизация производительности, которая не влияет на UX.
- Добавление новых фич, которые «кстати».

---

## 2. Когда стоит продолжать улучшать?

### 🟢 Продолжайте, если:
1. **Есть время.** Дедлайн не горит.
2. **Код будет жить долго.** Это ядро системы, а не разовый скрипт.
3. **Улучшение снижает риск.** Например, добавление тестов для сложной логики.
4. **Улучшение упрощает будущее.** Например, вынос повторяющейся логики в метод.
5. **Команда согласна.** Если тимлид или заказчик хочет большего качества.

### 🔴 Остановитесь, если:
1. **Дедлайн горит.** Критичная фича должна быть в проде сегодня.
2. **Улучшение не влияет на бизнес.** Косметические правки не важны.
3. **Вы в «ловушке совершенства».** Вы тратите 3 часа на переименование переменной.
4. **Риск регрессии выше, чем польза.** Вы можете сломать работающий код.
5. **Нет обратной связи.** Вы улучшаете код, который никто не читает.

---

## 3. Баланс: Как это выглядит на практике?

### Сценарий 1: Критичный баг в проде (P0)
- **Правило:** Только фикс.
- **Действия:**
  - Минимальное изменение.
  - 1 тест, который покрывает баг.
  - Никакого рефакторинга.
  - Никаких улучшений.
- **Почему:** Скорость > Качество. Риск > Совершенство.

### Сценарий 2: Новая фича (P1)
- **Правило:** Функциональность + Базовое качество.
- **Действия:**
  - Чистый код.
  - Тесты для основных сценариев.
  - Логирование.
  - Никаких «золотых карет».
- **Почему:** Фича должна работать, но не быть идеальной.

### Сценарий 3: Рефакторинг (P2)
- **Правило:** Качество > Скорость.
- **Действия:**
  - Глубокий рефакторинг.
  - Покрытие тестами > 80%.
  - Документация.
  - Оптимизация производительности.
- **Почему:** Это инвестиция в будущее.

### Сценарий 4: Разовый скрипт (P3)
- **Правило:** Скорость > Качество.
- **Действия:**
  - Минимальный код.
  - Никаких тестов (или 1 smoke-тест).
  - Никакого логирования.
- **Почему:** Это одноразовый инструмент. Инвестиции в качество не окупятся.

---

## 4. Инструменты для принятия решений

### 1. «Правило 80/20»
- 80% ценности дает 20% усилий.
- Спросите себя: «Дает ли это улучшение 80% ценности? Или это последние 20% усилий, которые дают 20% ценности?»
- Если второе — остановитесь.

### 2. «Правило 2 дней»
- Если улучшение займет больше 2 дней — это отдельная задача.
- Не включайте в текущий PR.
- Создайте задачу в бэклоге.

### 3. «Правило обратимости»
- Если решение можно легко отменить — делайте быстро.
- Если решение трудно отменить (миграция БД, архитектура) — тратьте больше времени на качество.

### 4. «Правило аудитории»
- Кто будет читать этот код через 6 месяцев?
- Если это вы — напишите так, чтобы вам было понятно.
- Если это команда — напишите так, чтобы было понятно всем.

---

## 5. Антипаттерны

### ❌ «Перфекционизм»
- Тратите 8 часов на идеально написанный код, который нужен был вчера.
- **Решение:** Установите дедлайн на улучшение.

### ❌ «Спиральный код»
- Пишете код «на коленке», потому что «сделаю потом».
- Никогда не делаете потом.
- **Решение:** Если не успеваете — говорите об этом сразу.

### ❌ «Золотые кареты»
- Добавляете опции, которых никто не просил.
- Добавляете фичи, которые никто не использует.
- **Решение:** Спросите себя: «Кто будет это использовать?»

### ❌ «Игнорирование долгов»
- Не пишете тесты, потому что «сделаю позже».
- Не документируете, потому что «это понятно».
- **Решение:** Минимальный стандарт качества — это неоплатно.

---

## 6. Итог

**Код «достаточно хорош», когда:**
1. Он решает бизнес-задачу.
2. Он не сломает систему.
3. Его можно откатить.
4. Его можно отлаживать.

**Когда продолжать улучшать:**
1. Есть время.
2. Есть ценность.
3. Есть согласие команды.

**Баланс:**
- **Скорость** важна для бизнеса.
- **Качество** важно для поддержания системы.
- **Коммуникация** важна для управления ожиданиями.

**Главный принцип:**
> *«Не пишите код, который понравится вам через год. Пишите код, который будет работать сегодня и не сломается завтра».*

> ⏱ 16.9s · prompt 81704 tok · output 1792 tok

---
