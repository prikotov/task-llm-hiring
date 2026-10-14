# Собес: `orcarouter/Qwen3.8-27B-Uncensored:latest` · роль: backend — run-01

- Дата: 2026-10-07T01:31:40.630606+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Рад знакомству.

Я — языковая модель, обученная на огромном корпусе кода, документации, Stack Overflow, GitHub-репозиториев и технической литературы. Мой «опыт» — это синтез знаний миллионов разработчиков. Я не пишу код «от интуиции», а опираюсь на устоявшиеся паттерны, бести-практики и строгие стандарты (PSR, SOLID, Clean Architecture и др.).

Вот моя само-оценка знаний по указанным разделам:

| Технология / Раздел | Оценка (0–10) | Обоснование в одном предложении |
| :--- | ::--- | :--- |
| **PHP 8** | 9.5 | Глубокое знание внутренних механизмов, новых фич (readonly properties, enums, fibers, JIT-компилятор) и их практического применения. |
| **Symfony** | 9.0 | Уверенное владение ядром, сервисной системой, events, DI-контейнером, таксономией пакетов и настройкой производительности. |
| **Doctrine** | 8.5 | Хорошо понимаю ORM-механики, DQL, hydration, lazy-loading, но осознаю границы, где Raw SQL эффективнее, и типичные подводные камни 2-й стадии. |
| **PostgreSQL** | 8.5 | Сильное знание SQL, индексов, транзакций, оконных функций и оптимизации запросов, но без глубинного уровня тюнинга на уровне планировщика. |
| **DDD** | 8.0 | Умею проектировать доменные модели, aggregate roots, валидации и события, но признание сложности корректной реализации в реальном проекте требует практики. |
| **Чистая архитектура** | 8.5 | Умею строить слои (API → Application → Domain → Infrastructure), изолировать зависимости и обеспечивать тестируемость. |
| **Тестирование** | 8.5 | Владение PHPUnit, Mocking, BDD-стилем, покрытием单元测试 и интеграционных тестов, но знание специфических фреймворков (Pest, Behat) может требовать уточнения. |
| **Git** | 9.0 | Уверенное знание ветвления, rebase/merge, конфликтов, hooks, workflow-стандартов (GitFlow/Trunk-based) и инструментов типа `git bisect`. |

> **Примечание:** Оценки отражают глубину понимания концепций и способность объяснять/реализовать их. Я могу писать код, отлаживать, рефакторить и объяснять, почему один подход лучше другого.

Готов помочь: от простого скрипта на PHP 8 до проектирования микросервиса на Symfony + DDD. С чего начнём?

> ⏱ 12.6s · prompt 89 tok · output 637 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Для написания чистого, строгого и предсказуемого кода я выделяю пять ключевых фич, которые пришли с PHP 8.0 и 8.1+. Они позволяют вытеснить «магию» и явные ошибки из рантайма, перенеся их на время компиляции или выполнения с понятными ошибками.

### 1. Strict Types (`declare(strict_types=1);`) + Typed Properties (с 7.4, но критично с 8.x)

Хотя типизация переменных пришла в 7.4, именно сочетание её с `strict_types` и коварными фичами 8.0 (например, `mixed`) делает код надёжным.

**Почему важно:** Исключает неявное приведение типов (`"1" == 1`), которое было источником десятков багов.

```php
<?php
declare(strict_types=1);

// Ошибка компиляции: несовместимые типы
function processId(string $id): int {
    return (int) $id; // Явное приведение вместо неявного
}

// В PHP 8.0+ можно использовать mixed, но лучше быть конкретным
function createEntity(mixed $data): array {
    if (!is_array($data)) {
        throw new \InvalidArgumentException("Data must be an array");
    }
    return ['id' => 1, 'data' => $data];
}
```

### 2. Enums (PHP 8.1)

**Почему важно:** Заменяет «магические константы» (`class Status { const ACTIVE = 'active'; }`) на типы данных с методами и строгой типизацией. Это основа DDD-сущностей.

```php
<?php

enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    // Методы, связанные со статусом
    public function isFinal(): bool
    {
        return match($this) {
            self::Delivered => true,
            default => false
        };
    }

    public function toColor(): string
    {
        return match($this) {
            self::Pending => '#ff9800',
            self::Shipped => '#2196f3',
            self::Delivered => '#4caf50'
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value); // Встроенный метод, бросает ValueError если нет такого кейса
    }
}

// Использование:
$status = OrderStatus::Shipped;
if ($status->isFinal()) { ... } // Читаемо и типобезопасно
```

### 3. Read-Only Properties (PHP 8.1)

**Почему важно:** Позволяет создавать имутабельные DTO и Value Objects. Свойство может быть записано только один раз в конструкторе класса. После этого попытка перезаписи бросает `Error`.

```php
<?php

class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {
        // Всё инициализируется в конструкторе
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException("Currencies must match");
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

$money = new Money(100, 'EUR');
// $money->amount = 200; // ❌ Error: Cannot modify read-only property Money::$amount
```

### 4. Named Arguments и New in Initializers (PHP 8.0)

**Почему важно:** Улучшает читаемость при вызове функций с большим количеством параметров и позволяет использовать новые свойства в выражениях конструкторов.

```php
<?php

class User
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public int $age,
        public string $email
    ) {}
}

// До PHP 8.0: new User("John", "Doe", 30, "john@example.com") — сложно читать
// PHP 8.0+:
$user = new User(
    firstName: "John",
    lastName: "Doe",
    age: 30,
    email: "john@example.com"
);

// Также можно переставить порядок аргументов
$user2 = new User(
    email: "john@example.com",
    firstName: "John"
);
```

### 5. Match Expression (PHP 8.0)

**Почему важно:** Более безопасный и мощный аналог `switch`. Всегда возвращает значение (может быть использован как выражение), поддерживает строгое сравнение типов и может возвращать объекты.

```php
<?php

function getDiscountPercent(string $customerType): float
{
    // Match — это выражение, его можно присвоить
    return match ($customerType) {
        'gold' => 25.0,
        'silver' => 10.0,
        'bronze' => 5.0,
        default => 0.0
    };
}

// Использование в условии
if (getDiscountPercent('gold') > 0) {
    echo "Discount applied!";
}
```

### 6. Constructor Property Promotion (PHP 8.0)

**Почему важно:** Сокращает код, объединяя объявление свойства, параметр конструктора и его присвоение. Основа для чистых DTO.

```php
<?php

// До PHP 8.0:
// private string $name;
// public function __construct(string $name) { $this->name = $name; }

// PHP 8.0+:
class Product
{
    public function __construct(
        private readonly string $name,
        private readonly float $price,
        private string $sku
    ) {
        // Тело конструктора можно оставить пустым или добавить логику валидации
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

---

### Итог

Эти фичи позволяют:
1. **Убрать ошибки времени выполнения**, связанные с типами (`strict_types`, `readonly`, `enums`).
2. **Сделать код самодокументируемым** (Named Arguments, Enum с методами).
3. **Облегчить создание имутабельных объектов** (`readonly`, `final`).
4. **Упростить логику ветвления** (`match`).

Для чистого кода я рекомендую:
- Всегда использовать `declare(strict_types=1);`.
- Предпочитать `readonly` для свойств DTO и Value Objects.
- Использовать `enum` вместо константных классов.
- Применять `match` для логики, зависящей от состояния.
- Использовать Named Arguments при вызове конструкторов с >2 параметрами.

> ⏱ 20.6s · prompt 780 tok · output 1623 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это «гибрид» статической типизации (в сигнатурах функций и классах) и динамической типизации (в теле функций и переменных). Это фундаментальная разница с такими языками, как Java или C#, но понимание механики критично для предсказуемости.

### 1. Как устроена система типов?

PHP использует **динамическую типизацию с коварными неявными приведениями (coercive typing)**.
*   **Типы данных:** `int`, `float`, `string`, `bool`, `array`, `object`, `null`, `resource`, `mixed` (с 8.0).
*   **Типизация в сигнатурах:** `public function save(int $id): bool {}` — проверка происходит **в момент вызова** функции, а не при компиляции файла (в отличие от PHP 8.0+ с `readonly` и некоторыми оптимизациями, но в целом проверка рантаймовая).
*   **Типизация переменных:** `$x = 10; $x = "hello";` — допустимо.

---

### 2. Что даёт `declare(strict_types=1);`?

Это **ключевой инструмент** для надёжности. Он влияет **только на вызовы функций, объявленные в том же файле, где объявлена директива**, или на аргументы, передаваемые из этого файла в другие.

#### Без `strict_types` (по умолчанию):
PHP пытается «угадать», что вы имели в виду, и выполняет **неявное приведение типов**.

```php
// Файл A.php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// Файл B.php
declare(strict_types=1); // Если нет, то приведения работают

// Вызов из другого файла (если там strict_types=0):
$result = add("10", 5); 
// Результат: 15. PHP приводит "10" к 10. 
// Если бы это было "abc", ошибка!
```

#### С `strict_types=1` (в файле вызывающего кода):
```php
// Файл B.php
declare(strict_types=1);

$result = add("10", 5); 
// Результат: TypeError: Argument #1 ($a) must be of type int, string given
```

> **Важно:** `strict_types` влияет на **файл, из которого производится вызов**. Это часто путают. Если в `Main.php` нет `strict_types`, но вы вызываете функцию из `Utils.php` (где `strict_types=1`), строгие проверки **не сработают** для аргументов, передаваемых из `Main.php`.

**Рекомендация:** Всегда добавляйте `declare(strict_types=1);` в **каждый** PHP-файл.

---

### 3. Как ведут себя приведения типов (Coercive Type Juggling)?

PHP имеет сложную таблицу приведений. Вот самые частые сценарии:

#### А. String → Number
*   `"123"` → `123` (int)
*   `"123.45"` → `123.45` (float)
*   `"123abc"` → `123` (int, обрезает хвост)
*   `"abc123"` → `0` (int, если не начинается с цифры)
*   `"1e3"` → `1000` (float, экспоненциальная запись)

#### Б. Number → String
*   `123` → `"123"`
*   `1.5` → `"1.5"`
*   `1.2e+20` → `"1.2E+20"` (может быть неочевидно)

#### В. Bool → Other
*   `true` → `1` (int), `"1"` (string)
*   `false` → `0` (int), `""` (string)

#### Г. Array → Other (часто источник багов)
*   `[1, 2, 3]` → `"Array"` (string, если в контексте строки)
*   `[1, 2, 3]` → `1` (int, если в контексте числа? Нет, обычно `1` для первого элемента в некоторых контекстах, но чаще ошибка или `0`)
    *   *Примечание:* Приведение массива к числу или строке редко бывает полезным и обычно даёт `"Array"` или `0`.

---

### 4. Типичные ловушки на практике

#### Ловушка 1: Сравнение с `==` vs `===`
```php
"1" == 1;      // true  (неявное приведение)
"1" === 1;     // false (разные типы)
"" == 0;       // true  (пустая строка приводит к 0)
"abc" == 0;    // true  (в PHP 8+ false, в PHP <8.0 true!)
[] == 0;       // true  (пустой массив приводит к 0)
false == 0;    // true
null == 0;     // true
```
**Решение:** Всегда используйте `===` и `!==` для сравнения, если не планируете приведение.

#### Ловушка 2: `is_numeric` и приведение
```php
$value = "123abc";
if (is_numeric($value)) { // false, потому что "123abc" не чистое число
    // ...
}
// Но:
(int) $value; // 123 (тихо отбрасывает хвост)
```

#### Ловушка 3: Массивы и строки
```php
$arr = ['a', 'b'];
echo $arr; // "Array" (не "a,b"!)
echo implode(',', $arr); // "a,b" (правильно)
```

#### Ловушка 4: `bool` и пустые значения
```php
$value = "0";
if ($value) { // false! Потому что "0" → 0 → false
    echo "True";
}
```

---

### 5. Примеры, где типизация **спасает**

#### Пример 1: Избегание ошибки при сложении
```php
// Без strict_types
function calculateTotal(string $price, int $quantity): int {
    return (int) $price * $quantity; // Явное приведение нужно
}

// С strict_types и строгими типами
declare(strict_types=1);

function calculateTotal(int $price, int $quantity): int {
    return $price * $quantity; // Чистая арифметика
}

// Вызов:
calculateTotal(100, 5); // OK
calculateTotal("100", 5); // TypeError (если вызов из файла с strict_types=1)
```

#### Пример 2: Безопасная работа с JSON
```php
declare(strict_types=1);

function decodeJson(string $json): array {
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    
    if (!is_array($data)) {
        throw new \TypeError("JSON must decode to an array");
    }
    
    return $data;
}

// Теперь мы уверены, что $data — это массив, а не null или объект
```

---

### 6. Примеры, где типизация **удивляет**

#### Удивление 1: `float` и точность
```php
$a = 0.1 + 0.2;
$b = 0.3;
var_dump($a === $b); // false!
// Почему? Из-за представления чисел с плавающей точкой (IEEE 754)
// $a = 0.30000000000000004
```
**Решение:** Для денег используйте `int` в минимальных единицах (центы) или библиотеки типа `bcmath` / `brick/math`.

#### Удивление 2: `int` → `float` при переполнении
```php
$a = PHP_INT_MAX; // 9223372036854775807
$b = $a + 1;
var_dump($b); // float(9.2233720368548E+18)
// Int превратился в float! Это может сломать логику, ожидающую int.
```

#### Удивление 3: Строки с кириллицей и `strlen`
```php
$str = "Привет";
var_dump(strlen($str)); // 12 (байт, UTF-8: каждая буква 2 байта)
var_dump(mb_strlen($str, 'UTF-8')); // 6 (символы)
```

#### Удивление 4: `array` как ключ
```php
$array = [1, 2, 3];
$arr = [$array => "value"];
// Ошибка! Массивы нельзя использовать как ключи массивов.
// Только int, string, null, bool (но null и bool превращаются в строки "1" и "")
```

---

### 7. Best Practices для чистого кода

1.  **Всегда** используйте `declare(strict_types=1);` в каждом файле.
2.  **Типизируйте** все параметры функций и возвращаемые значения (`: string`, `: ?int`, `: array<int, string>`).
3.  **Избегайте** `==` в пользу `===`.
4.  **Не полагайтесь** на неявные приведения. Если нужно привести тип — делайте это явно: `(int) $str`, `floatval($str)`.
5.  **Используйте** `mixed` только когда у вас действительно нет выбора (например, входящий JSON из внешних API).
6.  **Валидируйте** данные на границах системы (контроллеры, репозитории) и доверяйте внутренним слоям (Domain).
7.  **Для денег** используйте `int` в минимальных единицах (центы) и избегайте `float`.

### Итог

PHP — язык, который прощает ошибки, но может «показать зубы» позже. `strict_types` + явная типизация + `===` + внимательность к `float` — это ваш щит. В современном PHP (8.x) с `readonly`, `enum` и `match` можно написать код, который по надёжности близок к статически типизированным языкам, сохраняя при этом гибкость динамической типизации.

> ⏱ 31.9s · prompt 2460 tok · output 2481 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Работа с исключениями — это не просто «поймать ошибку», а часть архитектуры. В чистом коде исключения должны отвечать на вопрос: **«Почему действие не произошло?»** и предоставлять достаточно контекста для дебаггинга, но не раскрывают внутренние детали реализации наружу.

Вот мой подход к проектированию исключений в PHP-приложениях (особенно в контексте DDD/Symfony).

---

### 1. Иерархия исключений (Exception Hierarchy)

Я строю дерево исключений, основанное на **доменных концепциях**, а не на технических деталях.

```
Exception (PHP)
└── AppException (Базовое исключение приложения)
    ├── DomainException (Ошибки бизнес-логики)
    │   ├── InvalidOrderState (Нарушение инвариантов домена)
    │   └── CustomerNotRegistered
    ├── ServiceException (Ошибки внешних сервисов)
    │   ├── PaymentGatewayTimeout
    │   └── EmailProviderAuthError
    └── InfrastructureException (Ошибки инфраструктуры)
        ├── DatabaseConnectionFailed
        └── FileStorageError
```

**Почему так?**
*   **DomainException** — это предсказуемые ошибки, которые можно обработать в UI (например, показать пользователю «Остаток на складе недостаточен»).
*   **Service/Infrastructure** — это непредвиденные ошибки. Обычно они логируются и возвращают генерический `500 Internal Server Error` или `Retry` для клиентских SDK.

#### Пример кода:

```php
abstract class AppException extends \RuntimeException
{
    protected array $context = [];

    public function __construct(string $message, array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}

// Конкретное доменное исключение
class InsufficientStockException extends AppException
{
    public function __construct(string $productId, int $requested, int $available)
    {
        $message = sprintf(
            "Insufficient stock for product [%s]. Requested: %d, Available: %d",
            $productId,
            $requested,
            $available
        );
        parent::__construct(
            $message,
            ['product_id' => $productId, 'requested' => $requested, 'available' => $available],
            previous: null // Или передаем оригинальное исключение
        );
    }
}
```

> **Важно:** Контекст (`context`) — это структурированные данные (ID, суммы, сроки), которые помогут логгеру или API-клиенту понять причину без парсинга строки.

---

### 2. Оборачивание чужих исключений (Wrapping)

Никогда не пробрасывайте «голые» технические исключения (SQL, HTTP, Filesystem) за пределы границы слоя.

#### Правило:
1.  **Catch** в нижнем слое (Repository/Service).
2.  **Wrap** в доменное или сервисное исключение.
3.  **Log** оригинальное исключение (trace).
4.  **Throw** новое исключение.

#### Пример:

```php
class OrderRepository
{
    public function __construct(private \PDO $db) {}

    public function findById(int $orderId): Order
    {
        try {
            // 1. Вызываем «чужой» код (PDO, который бросает PDOException)
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute(['id' => $orderId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                throw new OrderNotFoundException($orderId); // Доменное исключение
            }

            return Order::fromArray($row);

        } catch (\PDOException $e) {
            // 2. Оборачиваем техническую ошибку
            // Логируем детальную ошибку (trace), но наружу бросаем общее
            \Monolog\Logger::getLogger()->error('Database error in OrderRepository', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $orderId
            ]);

            throw new DatabaseConnectionFailedException(
                message: 'Failed to load order data',
                previous: $e // Сохраняем оригинал для стека вызовов
            );
        }
    }
}
```

**Контракт метода:**
*   Метод `findById` гарантирует: либо вернет объект `Order`, либо бросит `OrderNotFoundException` (предсказуемо) или `DatabaseConnectionFailedException` (непредсказуемо, но типизировано).
*   Клиенту не нужно знать, что внутри стоит MySQL или PDO.

---

### 3. Что считается контрактом метода?

Контракт метода в части исключений должен быть **явным** в документации (DocBlock) или очевиден из сигнатуры.

| Исключение | Тип | Когда использовать | Пример |
|------------|-----|-------------------|--------|
| `InvalidArgumentException` | Standard | Некорректные аргументы. Клиентская ошибка. | `new Money(-10)` |
| `DomainException` | Domain | Нарушение бизнес-правил. Клиентская ошибка (часто). | `Order::ship()` при статусе `Cancelled` |
| `NotFoundException` | Domain | Объект не найден. Клиентская ошибка. | `getUser(999)` |
| `ServiceException` | App | Внешний сервис недоступен. Серверная ошибка. | `PaymentGateway::charge()` timeout |
| `Exception` / `Throwable` | Generic | Непредвиденная ошибка. Серверная ошибка. | Только в самом верхнем логере |

**Документация:**
```php
/**
 * @throws InsufficientStockException Если остатка на складе недостаточно.
 * @throws DatabaseConnectionFailedException Если база данных недоступна.
 */
public function reserveStock(ProductId $productId, int $qty): void
```

---

### 4. Антипаттерны

#### ❌ 1. «Голые» catch (Swallowing Exceptions)
```php
try {
    $this->emailService->send($email);
} catch (\Exception $e) {
    // Просто игнорируем. Отправлено ли письмо? Нет.
    // Письмо не отправлено. Почему? Неизвестно.
}
```
**Правильно:** Либо логируем, либо пробрасываем, либо возвращаем статус.

#### ❌ 2. Бросание исключений для нормального потока
```php
// Антоним:
function findProduct(string $code): Product {
    $product = $this->repo->findOneBy(['code' => $code]);
    if (!$product) {
        throw new ProductNotFoundException(); // Нормальное состояние!
    }
    return $product;
}
```
**Решение:** Если «не найдено» — это часть бизнеса (например, «товар закончился»), лучше вернуть `null` или Optional (в PHP — `?Product` или `Product|null`). Исключение — для **необычных** ситуаций.

#### ❌ 3. Catching `Exception` на верхнем уровне и логирование всего подряд
```php
// В контроллере
try {
    $this->orderService->placeOrder($data);
} catch (\Exception $e) {
    $this->logger->error('Error', ['exception' => $e]);
    return new Response('Oops', 500);
}
```
**Проблема:** Вы теряете различие между «Пользователь ввел неверный email» (400) и «База упала» (500).
**Решение:** Специализированные обработчики исключений (например, Symfony `EventListener` на `kernel.exception`), которые мапят конкретные классы исключений в HTTP-коды.

#### ❌ 4. Использование исключений для контроля потока (Go-To Exception)
```php
// Плохо:
try {
    parseString($str);
} catch (ParseError $e) {
    return null; // И мы каждый раз лезем в catch, хотя это частый случай
}
```
**Решение:** Если ошибка ожидаема и часта — возвращайте результат или статус, а не исключение. Исключения должны быть **редкими** и **дорогими**.

#### ❌ 5. Неиспользование `previous`
```php
catch (PDOException $e) {
    throw new DatabaseException('DB Error'); // Где оригинальная ошибка?
}
```
**Решение:** Всегда передавайте `previous: $e`. Это сохраняет цепочку причин.

---

### 5. Best Practices: Как я пишу

1.  **Тонкие граничные слои:**
    *   Контроллеры/Handlers ловят `AppException` и мапят в HTTP.
    *   Доменные сервисы бросают `DomainException`.
    *   Инфраструктура бросает `InfrastructureException`.

2.  **Структурированный контекст:**
    ```php
    throw new PaymentFailedException(
        message: 'Payment declined',
        context: [
            'provider' => 'Stripe',
            'code' => $e->getCode(),
            'amount' => $order->getTotal()
        ],
        previous: $e
    );
    ```

3.  **Логирование:**
    *   Логируем **все** необработанные исключения (top-level).
    *   Логируем **предупреждения** для ожидаемых доменных ошибок (если они частые).
    *   Используем `previous` для получения полного стека.

4.  **Тестирование:**
    ```php
    public function testReserveStockThrowsOnInsufficientStock(): void
    {
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Insufficient stock');
        
        $this->inventoryService->reserveStock($productId, 100);
    }
    ```

### Итог

Исключения в PHP — это **механизм передачи ответственности**.
*   **Домен** говорит: «Я не могу это сделать, потому что...» (DomainException).
*   **Инфраструктура** говорит: «Я не смог обратиться к ресурсу» (InfrastructureException).
*   **API-слой** говорит: «Вот HTTP-код и сообщение для клиента».

Антипаттерн — это когда слой инфраструктуры пытается говорить на языке домена (или наоборот), или когда исключения игнорируются, теряя контекст.

> ⏱ 32.7s · prompt 4997 tok · output 2491 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему опыту, самым часто нарушаемым принципом в реальном коде является **SRP (Single Responsibility Principle — Принцип единой ответственности)**.

Почему? Потому что он кажется «мягким» и не имеет жёстких статических проверок (в отличие от OCP или ISP, которые можно частично проверить линтерами). Разработчик часто говорит: *«Но это одна функция, зачем её разбивать?»*. На самом деле, нарушение SRP проявляется в **базуировании на разных причинах для изменения** (Reason for Change), а не просто в размере класса.

Если код меняется при каждом новом требовании бизнеса, при каждой новой интеграции с API или при каждом изменении формата вывода — значит, он несёт в себе несколько ответственностей.

---

### Типовое нарушение: «Универсальный» сервис

Допустим, у нас есть класс `OrderService`. В нём находятся методы:
1.  Создание заказа (бизнес-логика: валидация, расчёт цены, применение скидок).
2.  Сохранение в базу (техническая деталь: SQL/ORM).
3.  Отправка уведомления (внешняя зависимость: Email/Telegram).
4.  Логирование отладочных данных.

#### Пример нарушения (Anti-pattern)

```php
<?php

class OrderService
{
    public function __construct(
        private DatabaseConnection $db, // Зависимость на инфраструктуру
        private EmailClient $email,     // Зависимость на внешний сервис
        private Logger $logger          // Зависимость на логгер
    ) {}

    public function createOrder(Customer $customer, array $items): string
    {
        // 1. БИЗНЕС-ЛОГИКА: Валидация и расчёт
        if (empty($items)) {
            throw new EmptyCartException();
        }
        
        $total = 0;
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        
        // Применение скидок (бизнес-правило)
        if ($customer->isVip()) {
            $total *= 0.9;
        }

        // 2. ИНФРАСТРУКТУРА: Сохранение в БД
        $this->logger->info('Saving order', ['total' => $total]);
        $orderId = $this->db->insert('orders', [
            'customer_id' => $customer->id,
            'total' => $total,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 3. ИНФРАСТРУКТУРА: Отправка письма
        try {
            $this->email->send($customer->email, 'Order Confirmed', "Your order $orderId is processing.");
        } catch (\Exception $e) {
            // Что делать? Логируем и продолжаем? Или откатываем заказ?
            $this->logger->error('Email failed', ['exception' => $e->getMessage()]);
        }

        return $orderId;
    }
}
```

**Почему это плохо (нарушение SRP)?**
1.  **Сложность тестирования:** Чтобы проверить логику расчёта цены, нужно мокировать `DatabaseConnection`, `EmailClient` и `Logger`. Мы тестируем бизнес-логику, но нас отвлекают технические детали.
2.  **Несколько причин для изменения:**
    *   Изменилась логика скидок → меняем `OrderService`.
    *   Поменяли провайдера email (на Telegram) → меняем `OrderService` (или его зависимости).
    *   Поменяли структуру таблицы в БД → меняем `OrderService`.
    *   Нужно добавить SMS вместо Email → снова `OrderService`.
3.  **Запутанность потоков:** Если письмо не отправилось, должен ли заказ быть сохранён? В текущем коде это зависит от реализации catch, а не от бизнес-требования.

---

### Рефакторинг: Разделение ответственностей

Мы разобьём логику на три слоя (или компонента), у каждого из которых **одна** причина для изменения.

1.  **Domain Layer:** `OrderFactory` (или `Order` аггрегат) — отвечает за создание корректного объекта заказа и расчёт стоимости.
2.  **Application Service:** `CreateOrderHandler` — оркестрирует процесс: вызывает домен, сохраняет, отправляет уведомления.
3.  **Infrastructure:** `OrderRepository` и `NotificationSender` — технические реализации.

#### 1. Доменный слой (Чистый PHP, без зависимостей)

```php
<?php

final class Order
{
    public function __construct(
        private readonly string $id,
        private readonly Customer $customer,
        private readonly array $items,
        private readonly int $totalAmount
    ) {}

    // Статический фабрика-метод: отвечает за бизнес-логику создания
    public static function create(string $id, Customer $customer, array $items): self
    {
        if (empty($items)) {
            throw new EmptyCartException('Cart cannot be empty');
        }

        $total = 0;
        foreach ($items as $item) {
            if (!$item instanceof OrderItem) {
                throw new InvalidItemException();
            }
            $total += $item->getPrice() * $item->getQuantity();
        }

        // Бизнес-правило: VIP-скидка
        if ($customer->isVip()) {
            $total = (int) ($total * 0.9);
        }

        return new self($id, $customer, $items, $total);
    }

    // Геттеры...
    public function getId(): string { return $this->id; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getTotalAmount(): int { return $this->totalAmount; }
}
```

**Ответственность:** Только бизнес-правила создания заказа. Нет БД, нет почты, нет логгера. Легко тестируется.

#### 2. Инфраструктурный слой (Адаптеры)

```php
<?php

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
}

class PDOOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private \PDO $db) {}

    public function save(Order $order): void
    {
        // Логика работы с БД здесь
        // Если БД упала, бросаем конкретное инфраструктурное исключение
    }
}

interface NotificationSenderInterface
{
    public function sendOrderConfirmation(Customer $customer, string $orderId): void;
}

class EmailNotificationSender implements NotificationSenderInterface
{
    // Логика отправки письма
}

class TelegramNotificationSender implements NotificationSenderInterface
{
    // Логика отправки в Telegram
}
```

**Ответственность:** Только техническая реализация «как сохранить» и «как уведомить». Можно легко переключиться с Email на Telegram, не трогая бизнес-логику.

#### 3. Прикладной сервис (Оркестратор)

```php
<?php

class CreateOrderService
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private NotificationSenderInterface $notifier,
        private LoggerInterface $logger
    ) {}

    public function handle(Customer $customer, array $items): string
    {
        // 1. Создаем доменный объект (Бизнес-логика)
        $orderId = uniqid('ord_');
        $order = Order::create($orderId, $customer, $items);

        // 2. Сохраняем (Инфраструктура)
        // Если БД упадет, исключение проберется наверх, транзакция откатится
        $this->repository->save($order);

        // 3. Уведомляем (Инфраструктура)
        // Уведомление — это side-effect. Если оно упадет, заказ уже сохранен.
        try {
            $this->notifier->sendOrderConfirmation($customer, $orderId);
        } catch (\Throwable $e) {
            // Логируем, но НЕ откатываем заказ. 
            // Бизнес-требование: «Заказ считается успешным, если он сохранен».
            $this->logger->warning('Failed to notify customer', ['order' => $orderId, 'error' => $e->getMessage()]);
        }

        return $orderId;
    }
}
```

**Ответственность:** Только последовательность действий. Он знает, *что* делать, но не *как* именно сохраняется данные или отправляется письмо.

---

### Что мы выиграли от рефакторинга?

1.  **Тестируемость:**
    *   Тест `Order::create()` — чистый юнит-тест без моков.
    *   Тест `CreateOrderService::handle()` — мокируем только интерфейсы (`OrderRepositoryInterface`, `NotificationSenderInterface`). Мы проверяем, что сервис вызывает сохранение, а потом уведомление.

2.  **Заменяемость (OCP + DIP):**
    *   Хочешь отправить уведомление в Telegram? Создай `TelegramNotificationSender` и проинъектируй его в `CreateOrderService`. Бизнес-код не меняется.
    *   Хочешь хранить заказы в MongoDB? Создай `MongoOrderRepository`.

3.  **Четкость причин изменений (SRP):**
    *   Изменилась логика скидок → меняем `Order.php`.
    *   Изменился формат письма → меняем `EmailNotificationSender.php`.
    *   Изменилась таблица БД → меняем `PDOOrderRepository.php`.
    *   Изменилась последовательность (например, сначала уведомление, потом сохранение) → меняем `CreateOrderService.php`.

### Вывод

SRP — это не «один класс = одна функция». Это **«один класс = одна причина для изменения»**.

В PHP это особенно важно, потому что язык динамический, и соблазн «сунуть всё в один класс» очень велик. Границы ответственностей (Domain / Application / Infrastructure) и явные интерфейсы — это ваш главный инструмент для соблюдения SRP.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Короткий ответ: **Да, можно и нужно.**

SOLID — это не догмы, а **heuristics** (эвристики, или правила большого пальца). Они описывают компромисс между **гибкостью** (удобство изменения кода в будущем) и **простотой** (удобство чтения и написания кода сейчас).

Если нарушение SOLID делает код **понятнее**, **короче** или **быстрее** для решения конкретной, ограниченной по времени задачи, то это **оправданно**. В软件开发 (разработке) всегда есть trade-off: *Cost of Change* vs *Cost of Complexity*.

---

### Классический пример: Нарушение SRP ради простоты (YAGNI)

Представьте, что вам нужно написать скрипт, который раз в день отчитывается о продажах за последние 7 дней.

#### ❌ «Чистое» решение (Соблюдение SRP + OCP + DIP)

Чтобы соблюсти все принципы, вы создадите:
1.  `SalesReportInterface` (контракт).
2.  `SalesReport` (доменная модель).
3.  `SalesRepository` (инфраструктура, чтение из БД).
4.  `SalesReportService` (прикладной слой, агрегация).
5.  `EmailSender` / `TelegramSender` (отчет).
6.  `CronJobHandler` (оркестратор).

**Итог:** 5–7 файлов, 200+ строк кода, интерфейсы, инъекции зависимостей.

**Проблема:** Это избыточная абстракция для разового скрипта или скрипта, который будет жить 3 недели. Вы тратите время на создание архитектуры, которая может измениться завтра.

#### ✅ Оправданное нарушение (Один класс-«бог»)

```php
<?php

// CronJob: report_daily_sales.php

use PDO;

// Один класс делает всё: читает БД, считает, отправляет email.
class DailySalesReport
{
    private PDO $db;
    private string $toEmail;

    public function __construct(PDO $db, string $toEmail)
    {
        $this->db = $db;
        $this->toEmail = $toEmail;
    }

    public function run(): void
    {
        // 1. Чтение из БД (Инфраструктура внутри SRP-нарушения)
        $sql = "SELECT SUM(amount) as total, COUNT(*) as cnt FROM sales WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Расчет (Домен внутри SRP-нарушения)
        $avg = $row['cnt'] > 0 ? $row['total'] / $row['cnt'] : 0;
        $summary = sprintf("Total: %d, Orders: %d, Avg: %.2f", $row['total'], $row['cnt'], $avg);

        // 3. Отправка (Инфраструктура внутри SRP-нарушения)
        $subject = 'Daily Sales Report';
        $headers = 'From: bot@myshop.com';
        mail($this->toEmail, $subject, $summary, $headers);

        // 4. Логирование
        error_log('Report sent at ' . date('Y-m-d H:i:s'));
    }
}

// Исполнение
$pdo = new PDO('sqlite:/var/data/shop.db');
$report = new DailySalesReport($pdo, 'manager@myshop.com');
$report->run();
```

**Почему это оправдано?**
1.  **Контекст:** Это временный скрипт (или скрипт с низкой частотой изменений).
2.  **Понятность:** Разработчик открывает файл и сразу видит, *что* происходит от начала до конца. Не нужно прыгать между интерфейсами и реализациями.
3.  **Стоимость:** Написание «чистого» решения заняло бы 1 час, этого — 10 минут.
4.  **Гибкость:** Если через месяц требования сильно изменятся (например, нужно добавить Excel-отчет и график), тогда мы **экстрагируем** логику в отдельные классы.

> **Вывод:** SRP говорит: *«Разбей класс, если у него есть разные причины меняться»*. В данном случае причин меняться мало (только смена SQL или формата письма), и они тесно связаны. Нарушение SRP здесь — это осознанный выбор в пользу скорости.

---

### Другие примеры оправданных нарушений

#### 1. Нарушение OCP (Open/Closed Principle) ради читаемости
*   **OCP говорит:** Расширяй класс, не модифицируя его (через полиморфизм).
*   **Реальность:** Если у вас есть 3 способа оплаты (Card, PayPal, Crypto), создание абстракции `PaymentStrategy` с 3 классами — хорошо.
*   **Но:** Если вы знаете, что 4-го способа не будет (например, внутренние биллинг системы), проще написать:
    ```php
    if ($method === 'card') { ... }
    elseif ($method === 'paypal') { ... }
    elseif ($method === 'crypto') { ... }
    ```
    Полиморфизм здесь добавил бы 3 файла, интерфейсы и наследование ради 3 веток `if/else`.

#### 2. Нарушение ISP (Interface Segregation Principle)
*   **ISP говорит:** Клиент не должен зависеть от методов, которые он не использует.
*   **Реальность:** Если у вас внутренний класс `UserService`, который использует `UserRepository`, а `UserRepository` имеет 20 методов, но сервису нужен только 1 (`findById`), то разбивать интерфейс на 20 мелких (`UserFinder`, `UserSaver`, `UserDeleter`) — это **шум**.
*   **Решение:** Один интерфейс `UserRepositoryInterface` с 20 методами. Для внутреннего кода это нормально, так как мы управляем обеими сторонами (реализация и клиент). ISP важен при взаимодействии с **внешними** API.

#### 3. Нарушение LSP (Liskov Substitution Principle)
*   **LSP говорит:** Дочерний класс должен замещать родительский без потери функциональности.
*   **Реальность:** Иногда наследование используется просто для **кода-реюза** (например, `BaseEntity` с общими полями `id`, `createdAt`), а не для полиморфизма. Если вы не планируете заменять `Cat` на `Dog` в runtime, но хотите общее хранение ID, то «техническое» наследование допустимо, даже если нарушает строгую LSP-смысловую замену. (Лучше использовать composition, но наследование — не синтетический).

---

### Когда нарушение SOLID — это плохо?

Нарушение оправдано, если:
1.  **Контекст ограничен** (скрипт, прототип, разовая задача).
2.  **Количество вариантов небольшое и стабильное** (3 способа оплаты, не 100).
3.  **Команда маленькая** (1–3 разработчика) и понимает контекст.
4.  **Вы осознанно выбираете простоту** (YAGNI - You Aren't Gonna Need It).

Нарушение **плохо**, если:
1.  Вы нарушаете принцип **неосознанно** (просто лень думать).
2.  Код начинает «раздуваться» при каждом новом требовании (God Class).
3.  Другие разработчики не могут понять, где что находится (отсутствие границ).
4.  Изменения в одной части системы случайно ломают другую (слабая связанность).

### Итог

**SOLID — это не закон физики, а инструмент.**
*   Если нарушая SRP, вы получаете код на 100 строк вместо 500, и он будет жить 2 недели — **нарушайте**.
*   Если нарушая SRP, вы получаете God Class на 2000 строк, который меняют 5 человек и который лежит в ядре системы 5 лет — **рефакторьте**.

Вопрос не «Как нарушить SOLID?», а **«Какова цена этого нарушения в текущем контексте?»**.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Верно, наследование — лишь один из паттернов реализации OCP (Open/Closed Principle). Часто оно даже является самым «слабым» вариантом, так как создает жесткие связи и нарушает LSP (Principle of Substitution).

В современном PHP (особенно с использованием DDD и Clean Architecture) OCP реализуется через **инверсию зависимостей** и **композитную архитектуру**. Вот основные способы, от простейших к самым гибким:

### 1. Стратегия через Интерфейс (Composition over Inheritance)
Это классический подход. Мы определяем контракт (интерфейс), а разные реализации этого контракта вставляем в место, где нужна конкретная логика.

**Суть:** Код зависит от абстракции (интерфейса), а не от конкретной реализации.

```php
<?php

// 1. Определяем контракт (Абстракция)
interface PaymentProcessor
{
    public function charge(Money $amount): TransactionResult;
}

// 2. Реализации (Закрытые классы)
class StripeProcessor implements PaymentProcessor
{
    public function charge(Money $amount): TransactionResult
    {
        // Логика работы с API Stripe
        return new TransactionResult('stripe_id', true);
    }
}

class PayPalProcessor implements PaymentProcessor
{
    public function charge(Money $amount): TransactionResult
    {
        // Логика работы с API PayPal
        return new TransactionResult('paypal_id', true);
    }
}

// 3. Клиентский код (Открыт для расширения, закрыт для изменения)
class OrderService
{
    public function __construct(private PaymentProcessor $processor)
    {
        // Мы НЕ знаем, какой именно процессор внутри.
        // Мы знаем только, что он умеет charge().
    }

    public function pay(Money $amount): void
    {
        // Чтобы добавить новый способ оплаты (например, Crypto),
        // мы НЕ меняем этот класс.
        // Мы создаем новый класс CryptoProcessor implements PaymentProcessor
        // и проинъектируем его сюда при создании сервиса.
        $result = $this->processor->charge($amount);
    }
}
```
**Плюсы:** Простота, отсутствие «древа наследования».
**Минусы:** Если у вас много методов в интерфейсе, он может стать «жирным» (нарушение ISP).

---

### 2. Команда (Command Pattern) + Диспетчер
Этот подход хорош, когда действия имеют сложную логику обработки, валидацию или логирование, которое должно быть единым для всех команд.

**Суть:** Каждое действие инкапсулируется в объект-команду. Диспетчер определяет, как его выполнить.

```php
<?php

// 1. Абстракция команды
interface Command
{
    public function getName(): string;
}

// 2. Конкретные команды (Закрыты для изменения)
class CreateOrderCommand implements Command
{
    public function __construct(private array $data) {}
    
    public function getName(): string { return 'order.create'; }
    
    public function getData(): array { return $this->data; }
}

class CancelOrderCommand implements Command
{
    public function __construct(private int $orderId) {}
    
    public function getName(): string { return 'order.cancel'; }
    
    public function getOrderId(): int { return $this->orderId; }
}

// 3. Диспетчер (Открыт для расширения)
class CommandDispatcher
{
    private array $handlers = [];

    public function register(string $commandName, callable $handler): void
    {
        $this->handlers[$commandName] = $handler;
    }

    public function dispatch(Command $command): void
    {
        $name = $command->getName();
        if (isset($this->handlers[$name])) {
            call_user_func($this->handlers[$name], $command);
        } else {
            throw new UnknownCommandException($name);
        }
    }
}

// 4. Регистрация (Расширение без изменения ядра)
$dispatcher = new CommandDispatcher();

// Ручная регистрация (или через DI-контейнер)
$dispatcher->register('order.create', function (CreateOrderCommand $cmd) {
    // Бизнес-логика создания
});

$dispatcher->register('order.cancel', function (CancelOrderCommand $cmd) {
    // Бизнес-логика отмены
});

// Использование
$dispatcher->dispatch(new CreateOrderCommand(['item' => 'Book']));
```
**Плюсы:** Легко добавлять новые типы команд. Единое место для поперечных сечений (logging, validation).
**Минусы:** Потеря типобезопасности (если не использовать строгие проверки типов в хендлерах).

---

### 3. Правила (Rule / Policy Objects)
Отлично подходит для валидаций или бизнес-правил, которые могут меняться независимо друг от друга.

**Суть:** Вместо большого `if-else` или методов в одном классе, создаются отдельные объекты-правила.

```php
<?php

// 1. Интерфейс правила
interface ValidationRule
{
    public function validate(ValidationContext $context): RuleResult;
}

// 2. Конкретные правила
class EmailFormatRule implements ValidationRule
{
    public function validate(ValidationContext $context): RuleResult
    {
        $email = $context->getInput('email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return RuleResult::ok();
        }
        return RuleResult::fail('Invalid email format');
    }
}

class UniqueUsernameRule implements ValidationRule
{
    public function __construct(private UserRepository $users) {}

    public function validate(ValidationContext $context): RuleResult
    {
        $username = $context->getInput('username');
        if ($this->users->existsByUsername($username)) {
            return RuleResult::fail('Username taken');
        }
        return RuleResult::ok();
    }
}

// 3. Валидатор (Открыт для расширения)
class Validator
{
    private array $rules = [];

    public function addRule(ValidationRule $rule): void
    {
        $this->rules[] = $rule;
    }

    public function run(ValidationContext $context): void
    {
        foreach ($this->rules as $rule) {
            $result = $rule->validate($context);
            if (!$result->isSuccess()) {
                throw new ValidationException($result->getMessage());
            }
        }
    }
}

// 4. Использование (Расширение)
$validator = new Validator();
$validator->addRule(new EmailFormatRule());
$validator->addRule(new UniqueUsernameRule($userRepo));

// Чтобы добавить новое правило "PasswordStrength",
// создаем класс PasswordStrengthRule implements ValidationRule
// и вызываем $validator->addRule(new PasswordStrengthRule());
// Класс Validator не меняем.
```

---

### 4. Зарегистрированные Служебные События (Event Listeners)
Если «расширение» означает реакцию на событие, а не замену логики, то события — идеальный инструмент OCP.

**Суть:** Ядро отправляет событие. Слушатели (Listeners) реагируют. Вы можете добавлять новые слушатели, не трогая ядро.

```php
// Ядро (Закрыто для изменения)
class OrderService
{
    private EventDispatcher $events;

    public function placeOrder(array $data): void
    {
        // Логика заказа
        $this->events->dispatch(new OrderPlacedEvent($orderId));
        // Мы не знаем, кто слушает это событие.
    }
}

// Расширения (Открыты для добавления)
// Файл A.php
class SendEmailListener
{
    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        // Отправить письмо
    }
}

// Файл B.php
class UpdateInventoryListener
{
    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        // Уменьшить остатки на складе
    }
}

// Файл C.php (Новое расширение, добавленное позже)
class AnalyticsListener
{
    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        // Отправить данные в GA4
    }
}
```
В Symfony или Laravel это делается через декораторы `#[EventSubscriber]` или конфиги.

---

### 5. Конфигурируемое Поведение (Configuration)
Простейший способ OCP: вместо кода — конфигурация.

**Суть:** Алгоритм фиксирован, но параметры меняются.

```php
// Класс алгоритма (Закрыт)
class ReportGenerator
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function generate(): string
    {
        // Логика генерации отчета
        // Может зависеть от $this->settings['format'], $this->settings['language'] и т.д.
        return "Report generated in " . $this->settings['format'];
    }
}

// Расширение через конфиг (Открыто)
$generator = new ReportGenerator([
    'format' => 'PDF', // Было 'Excel', стало 'PDF'. Код класса не менялся.
    'language' => 'ru'
]);
```
Это работает, если варианты изменения конечны и известны заранее.

---

### Сравнение подходов

| Способ | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Стратегия (Interface)** | Когда нужна замена алгоритма (A/B тесты, провайдеры). | Простота, типобезопасность. | Интерфейс может стать слишком широким. |
| **Команды** | Когда действия разные, но обрабатываются единообразно (валидация, логирование). | Гибкость, разделение ответственности. | Много мелких классов, потеря статической типизации в диспетчере. |
| **Правила (Rules)** | Валидация, трансформация данных. | Легко добавлять/удалять правила. | Сложнее отладка, так как правила независимы. |
| **События** | Реакция на факт, а не замена логики. Полное разделение. | Максимальная гибкость, loose coupling. | Сложность отладки (асинхронность), потеря порядка выполнения. |
| **Конфигурация** | Невеликие вариации (форматы, флаги). | Самая простая реализация. | Не масштабируется на сложную логику. |

### Итог

**OCP не обязательно требует наследования.**
В PHP чаще всего используют **Стратегию** (интерфейсы) и **События** (listeners). Это позволяет добавить новую функциональность (новый способ оплаты, новый слушатель событий), не изменяя код уже работающей системы, что и есть суть принципа Открытого/Закрытого.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Короткий ответ: **Нет, SRP (Single Responsibility Principle) — это не только про классы.** Это принцип, описывающий **границы ответственности** любой сущности в коде.

В классической формулировке Берт Бада «Один класс = одна причина для изменения» действительно звучит про классы, потому что классы — основной строительный блок ООП. Однако в широком смысле SRP применим к:

### 1. Функциям / Методам
Это, пожалуй, самый частый уровень нарушения SRP в реальном коде.

*   **Плохо:** Метод `saveOrder()`, который одновременно:
    1.  Валидирует данные.
    2.  Рассчитывает итоговую сумму.
    3.  Сохраняет в БД.
    4.  Отправляет email.
    5.  Логирует действие.
    *Причина для изменения здесь минимум 5.*

*   **Хорошо:**
    *   `OrderValidator::validate()`
    *   `OrderPricingService::calculate()`
    *   `OrderRepository::save()`
    *   `NotificationService::send()`
    *   `Logger::log()`

    Если вы измените логику скидок, вы трогаете только `OrderPricingService`. Если поменяете провайдера email — только `NotificationService`.

### 2. Модулям / Пакетам (Namespaces)
В PHP (и других языках) пакет (namespace) — это логическая граница.

*   **Плохо:** Пакет `App\Services` содержит:
    *   `PaymentService` (бизнес-логика оплаты)
    *   `EmailService` (инфраструктура отправки писем)
    *   `DashboardController` (API слой)
    *   *Причины для изменения: бизнес-требования, технические изменения API почтового провайдера, изменения в REST-спецификации.* Это три разных причины.

*   **Хорошо:**
    *   `App\Domain\Order` (чистая бизнес-логика, зависит от ничего)
    *   `App\Application\Order` (оркестрация, зависит от Domain)
    *   `App\Infrastructure\Persistence` (репозитории, БД)
    *   `App\Interface\Api` (контроллеры, DTO)

    Если вы перепишете БД из MySQL в PostgreSQL, вы трогаете только `App\Infrastructure`. Бизнес-логика в `App\Domain` не пострадает.

### 3. Компонентам UI (в фронте, но аналогично)
В React/Vue/Blade-компонентах.

*   **Плохо:** Компонент `<Dashboard />`, который:
    1.  Загружает данные из API.
    2.  Отрисовывает графики.
    3.  Обрабатывает клики по кнопкам.
    4.  Управляет модальными окнами.
    *Сложно тестировать, сложно переиспользовать части.*

*   **Хорошо:**
    *   `<DashboardDataLoader />` (только данные)
    *   `<ChartComponent />` (только отрисовка)
    *   `<ActionButtons />` (только UX-действия)

### 4. Файлам
Даже в рамках одного класса.

*   **Плохо:** Один файл `User.php` содержит:
    *   Класс `User` (домен).
    *   Класс `UserFormRequest` (валидация HTTP).
    *   Класс `UserResponse` (сериализация API).
    *   *Это разные контексты: домен, входные данные, выходные данные.*

*   **Хорошо:**
    *   `Domain/User/User.php`
    *   `Http/Requests/UserFormRequest.php`
    *   `Http/Resources/UserResource.php`

---

### Как определить нарушение SRP?

Вместо вопроса *«Сколько методов в классе?»*, задайте вопрос:

> **«Если завтра изменится требование X, сколько файлов/функций/пакетов мне нужно будет переписывать?»**

Если ответ: **«Слишком много, и по разным причинам»** — значит, SRP нарушен, и нужно разбивать ответственность.

### Важный нюанс: «Слишком мелко» — тоже ошибка

Нарушение SRP не всегда означает, что нужно создавать новые классы. Иногда это означает, что нужно **выделить логику в отдельную функцию** внутри того же класса или перенести в другой пакет.

Пример:
```php
class Order
{
    public function calculateTotal(): int
    {
        // Логика расчёта (10 строк)
    }

    public function applyDiscounts(Discount $discount): void
    {
        // Логика скидок (10 строк)
    }
    
    // Это нормально! Один класс, но две разные ответственности?
    // Нет, это одна ответственность: «Быть корректным заказом».
    // Расчёт и скидки — части этой ответственности.
}
```

Но если вы вынесете `applyDiscounts` в отдельный класс `DiscountCalculator`, который знает о заказе, но не является частью домена заказа, — это уже вопрос архитектуры. Обычно `DiscountCalculator` лучше быть статической утилитой или частью домена `Discount`.

### Итог

SRP — это **принцип модульности**. Он применяется к:
*   Классам
*   Методам/функциям
*   Модулям/пакетам
*   Файлам
*   Компонентам UI
*   Даже к таблицам БД (одна таблица = одна сущность, не «таблица_для_всего»)

Главный критерий: **Одна причина для изменения.** Где бы вы это ни применяли — в коде, в БД или в UI — принцип один.

> ⏱ 30.7s · prompt 7535 tok · output 2333 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер внедрения зависимостей (DI Container) в Symfony — это не просто «магическая коробка», а сложный механизм, который превращает декларативную конфигурацию в оптимизированный граф объектов.

Давайте разберем этот процесс по шагам: от компиляции до момента получения объекта.

### 1. Что происходит при компиляции контейнера?

Компиляция — это этап, когда Symfony превращает ваши XML/YAML/PHP/Attributes-конфиги в код (классы PHP). Это происходит один раз при деплое или при первом запуске (кэшируется в `var/cache/`).

Основные шаги компиляции:

1.  **Сборка метаданных (Discovery):**
    *   Symfony сканирует ваши сервисы (через `services.yaml`, attributes `#[Service]`, автонахождение).
    *   Определяет зависимости каждого сервиса (кто кого вызывает).

2.  **Построение графа зависимостей:**
    *   Создается дерево: Сервис A зависит от B, B зависит от C.
    *   Определяются циклические зависимости.
    *   Определяются синглтонс (Singletons) и прототипы (Prototypes).

3.  **Анализ и оптимизация:**
    *   **Изоляция приватных сервисов:** Если сервис используется только внутри другого сервиса (вложенный объект), он может быть объявлен `private`. Это позволяет оптимизатору «встроить» его код прямо в родительский сервис.
    *   **Удаление неиспользуемых:** Если сервис объявлен, но никогда не вводится нигде, он может быть вырезан из итогового кода.
    *   **Inline-инжекция (Inlining):** Вместо создания отдельного объекта для зависимостей, код может быть сгенерирован прямо в конструктор родителя.

4.  **Генерация класса контейнера:**
    *   В папке кэша создается файл, например, `ContainerXXXxx.php`.
    *   В нем находится один главный класс `Container` и множество вложенных функций/методов.
    *   Пример сгенерированного кода (упрощенно):

```php
class ContainerXyz extends \Symfony\Component\DependencyInjection\Container
{
    // ... другие методы ...

    // Этот метод будет вызван, когда кто-то запросит 'App\Service\UserService'
    protected function createUserService(): UserService
    {
        // Вместо создания отдельного объекта EmailService, 
        // он может быть вlined сюда, если он private.
        $emailService = new \App\Service\EmailService(); 
        
        $repo = $this->get('App\Repository\UserRepository');

        return new UserService($emailService, $repo);
    }
}
```

> **Важно:** Компиляция — это *статический* анализ. На этом этапе еще нет никакого создания объектов (no instantiation). Мы просто готовим «рецепты».

---

### 2. Как работает Autowiring?

Autowiring — это механизм, который избавляет от необходимости явно указывать типы зависимостей в конфигурации. Он работает **на этапе компиляции**.

**Алгоритм Autowiring:**

1.  **Чтение сигнатуры:** Symfony смотрит на конструктор вашего сервиса (или методы `__construct`).
    ```php
    class OrderService {
        public function __construct(
            private PaymentInterface $payment,
            private NotificationSender $sender
        ) {}
    }
    ```

2.  **Поиск реализаций:**
    *   Для каждого параметра с типом (`PaymentInterface`, `NotificationSender`) контейнер ищет:
        *   Явно проинъектированные сервисы этого типа.
        *   Если тип — интерфейс: ищет сервисы, которые его реализуют.
        *   Если тип — конкретный класс: ищет этот класс.

3.  **Разрешение неоднозначности (Tags/Abstract Services):**
    *   Если найдено **один** кандидат → он автоматически привязывается.
    *   Если найдено **несколько** → Symfony использует **Service Tags**.
        *   Пример: `NotificationSender` имеет два варианта: `EmailSender` и `SmsSender`.
        *   Если в `OrderService` нужен только Email, вы можете разметить параметр:
            ```php
            /** @var EmailSender|\App\Service\NotificationSender */
            private NotificationSender $sender;
            ```
            Или использовать автонахождение через тег `kernel.event_listener` или явную конфигурацию `autowire` с приоритетом.
        *   Чаще всего, если неоднозначность не разрешена, компиляция упадет с ошибкой: `Cannot autowire service: argument $sender of method __construct() references class "App\Service\NotificationSender" but no such service exists.`

4.  **Запись в граф:**
    *   После того как контейнер определил, какой именно сервис нужно подставить, он добавляет это в граф зависимостей.

> **Итог:** Autowiring — это не магия в рантайме. Это статический анализ типов в compile-time, который заполняет пропуски в графе зависимостей.

---

### 3. Что происходит в момент запроса сервиса (Runtime)?

Допустим, в контроллере вы делаете:
```php
public function index(Request $request): Response
{
    $service = $this->container->get(OrderService::class);
    // или через внедрение в конструктор контроллера
}
```

Вот последовательность событий:

#### Шаг 1: Проверка кэша в памяти (Runtime Cache)
Контейнер хранит уже созданные объекты в приватном массиве `protected array $services = [];`.

*   **Если сервис уже создан** (например, он Singleton и запрашивался ранее):
    *   Symfony просто берет объект из `$this->services['App\Service\OrderService']` и возвращает его.
    *   **Это самый быстрый путь.**

*   **Если сервиса нет в кэше:**
    *   Переходим к генерации.

#### Шаг 2: Вызов сгенерированного метода
Контейнер вызывает метод, сгенерированный на этапе компиляции (см. раздел 1):
```php
$service = $this->createOrderService();
```

#### Шаг 3: Рекурсивное создание зависимостей
Внутри `createOrderService()` контейнер начинает создавать зависимости:

1.  **Зависимость 1: `PaymentInterface`**
    *   Контейнер проверяет: есть ли `PaymentService` в кэше?
    *   Если нет → вызывает `createPaymentService()`.
    *   Внутри `createPaymentService()` могут быть свои зависимости (например, `HttpClient`).
    *   Этот процесс рекурсивный.

2.  **Зависимость 2: `NotificationSender`**
    *   Аналогично, создается конкретная реализация (например, `EmailSender`).

#### Шаг 4: Инстанциация и Внедрение
Когда все зависимости готовы:
```php
// Внутри createOrderService():
$payment = $this->createPaymentService();
$sender = $this->createEmailSender();

$orderService = new OrderService($payment, $sender);
```

#### Шаг 5: Сохранение в кэш и Возврат
```php
// В самом конце метода createOrderService():
$this->services['App\Service\OrderService'] = $orderService;
return $orderService;
```

---

### Ключевые моменты и нюансы

1.  **Singletons по умолчанию:**
    *   Большинство сервисов в Symfony — это Singletons (один экземпляр на запрос).
    *   Если вы объявите сервис как `shared: false` (Prototype), то каждый раз при `get()` будет создаваться новый объект. Это дороже, но нужно, если сервис хранит состояние (stateful), например, пользовательские сессии или данные формы.

2.  **Private vs Public:**
    *   **Public:** Доступен через `$container->get()`.
    *   **Private:** Доступен только внутри контейнера (для других сервисов).
    *   Приватные сервисы можно оптимизировать (inline), публичные — нет (их нужно хранить в общем кэше).

3.  **Autowire vs Autoconfigure:**
    *   **Autowire:** Решает, *какой сервис* внедрить (по типу).
    *   **Autoconfigure:** Решает, *какие теги* добавить. Например, если класс реализует `EventListenerInterface`, Symfony автоматически добавит тег `kernel.event_listener`, и слушатель зарегистрируется без ручной настройки.

4.  **Ошибки в рантайме:**
    *   Если в момент запроса сервиса не хватает зависимостей (например, внешняя база данных не подключена), ошибка будет брошена именно в момент `new OrderService(...)`, а не при компиляции.

### Итоговая схема

```
Конфигурация (YAML/Attributes)
        ↓
[Компиляция] (Compile-time)
   - Анализ типов (Autowiring)
   - Построение графа
   - Оптимизация (Inlining, Pruning)
   - Генерация PHP-кода (var/cache/...)
        ↓
[Код] (Generated Container Class)
        ↓
[Запрос] (Runtime)
   1. Проверка кэша ($this->services)
   2. Если нет → вызов сгенерированного метода (createXxx)
   3. Рекурсивное создание зависимостей
   4. Инстанциация (new)
   5. Сохранение в кэш
   6. Возврат объекта
```

Таким образом, Symfony использует **комплексный подход**:
*   **Статический анализ** (compile-time) для определения структуры и типов.
*   **Динамическое создание** (runtime) для instantiation, кэширование и управление жизненным циклом.

Это дает высокую производительность (благодаря кэшу и inlining) и безопасность типов (благодаря autowiring), сохраняя при этом гибкость конфигурации.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это классический сигнал о том, что нарушен принцип **SRP** (Single Responsibility Principle) или **DIP** (Dependency Inversion Principle). Если сервис A зависит от B, а B зависит от A, они, скорее всего, делают одну и ту же работу или их границы ответственности размыты.

В Symfony и PHP есть три основных способа решения этой проблемы. Я расставлю их по убыванию предпочтительности.

---

### 1. Извлечение общего поведения в третий сервис (Лучшее решение)

Это самый «чистый» архитектурный подход. Если A и B вызывают друг друга, обычно это значит, что у них есть общая логика, которая должна находиться в третьем, независимом месте.

**Ситуация:**
*   `UserService` вызывает `EmailService` (отправить письмо).
*   `EmailService` вызывает `UserService` (получить email пользователя).

**Решение:**
Создаем `UserRepository` или `UserReader`.
*   `UserService` зависит от `UserRepository` (для чтения) и `EmailService`.
*   `EmailService` зависит от `UserRepository` (для чтения).
*   Цикл исчезает: оба зависят от репозитория, но не друг от друга.

```php
// Было:
// UserService -> EmailService
// EmailService -> UserService (ЦИКЛ)

// Стало:
// UserService -> UserRepository, EmailService
// EmailService -> UserRepository
// (У UserRepository нет зависимостей от сервисов)
```

---

### 2. Инверсия через События (Event-Driven)

Если зависимость возникает не для получения данных, а для **реакции** на действие, используйте систему событий Symfony (`EventDispatcher`).

**Ситуация:**
*   `OrderService` создает заказ и хочет отправить уведомление.
*   `NotificationService` отправляет уведомление и хочет обновить статус в `OrderService`.

**Решение:**
1.  `OrderService` только создает заказ и диспатчит событие `OrderCreatedEvent`.
2.  `NotificationService` слушает это событие, отправляет письмо.
3.  Если нужно обновить статус, `NotificationService` может диспатчить `NotificationSentEvent`, который слушает `OrderService` (или репозиторий).

**Почему это работает:**
*   `OrderService` не знает о `NotificationService`.
*   `NotificationService` не знает о `OrderService` (он работает со сущностью `Order`, полученной из события).
*   Зависимости становятся **односторонними** или **отсутствуют** (через DI в слушатели).

```php
// В OrderService
public function createOrder(): void {
    // ... логика ...
    $this->eventDispatcher->dispatch(new OrderCreatedEvent($order));
}

// В NotificationService (отдельный класс, слушатель)
public function onOrderCreated(OrderCreatedEvent $event): void {
    // Логика отправки
}
```

> **Важно:** События должны использоваться для **разделения** логики, а не для маскировки плохой архитектуры (если вы просто бросаете событие, чтобы не думать о структуре).

---

### 3. Использование `ServiceLocator` (Или `@\Symfony\Contracts\Service\Attribute\Required`)

Если вы не можете разбить логику или использовать события (например, обе сущности тесно связаны и должны работать синхронно), можно «разорвать» цикл, сделав одну из зависимостей **ленивой** (lazy).

В Symfony это делается через `ServiceLocator` или аннотации `#[Required]`.

#### Вариант A: `ServiceLocator`
Вместо прямого внедрения в конструктор, вы получаете зависимость «по требованию» внутри метода.

```php
use Symfony\Contracts\Service\ServiceLocatorInterface;

class UserService
{
    // Вместо: private EmailService $email;
    // Мы берем локатор и забираем сервис только когда нужен.
    
    public function __construct(private ServiceLocatorInterface $services) {}

    public function registerUser(string $email): void {
        // ... логика ...
        
        // Сервис создается только в этот момент
        $emailService = $this->services->get(EmailService::class);
        $emailService->sendWelcome($email);
    }
}

class EmailService
{
    public function __construct(private UserService $userService) {}
    
    public function sendWelcome(string $email): void {
        // Здесь мы можем вызвать метод UserService, 
        // но не создаем новый экземпляр, а используем существующий из контейнера
    }
}
```

**Как это разрывает цикл:**
*   `UserService` не имеет *прямой* зависимости на `EmailService` в момент конструирования.
*   Контейнер может создать `UserService` первым (без проблем).
*   Когда `UserService` запрашивает `EmailService`, контейнер видит, что `EmailService` уже существует (или создает его, внедрив `UserService`, который уже создан).

#### Вариант B: Атрибут `#[Required]` (Новый стиль Symfony 6.3+)
Это более современный и «магический» способ. Поля с `#[Required]` заполняются контейнером после создания объекта, а не через конструктор.

```php
use Symfony\Contracts\Service\Attribute\Required;

class UserService
{
    #[Required]
    private EmailService $emailService; // Внедряется позже

    public function sendEmail(): void {
        $this->emailService->send();
    }
}

class EmailService
{
    public function __construct(private UserService $userService) {}
    
    public function send(): void {
        // Цикла нет, потому что UserService был создан первым,
        // а emailService подключен к нему пост-фактум.
    }
}
```

**Плюсы:**
*   Не нужно вводить `ServiceLocator` вручную.
*   Код выглядит как обычный DI.

**Минусы:**
*   Скрытие зависимостей (сложнее увидеть их при чтении конструктора).
*   Нарушение «чистого» DI (зависимость не видна в сигнатуре).

---

### 4. Антипаттерн: Использование глобального контейнера (Avoid!)

```php
$container = $GLOBALS['container']; // Или статический доступ
$emailService = $container->get(EmailService::class);
```

**Почему плохо:**
*   Невозможно протестировать без моков глобального состояния.
*   Потеря прозрачности зависимостей.
*   Использование только как крайняя мера, если ни один из вышеперечисленных способов не подходит.

---

### Итоговая таблица решений

| Метод | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Извлечение общего сервиса** | Если A и B имеют общую логику. | Чистая архитектура, легко тестировать. | Нужно думать о структуре. |
| **События** | Если B реагирует на действие A. | Полное разделение, loose coupling. | Асинхронность, сложнее отладка. |
| **`#[Required]` / `ServiceLocator`** | Если зависимость редкая или синхронная. | Быстро решается, не нужно менять архитектуру. | Скрытие зависимостей, нарушает «чистый» DI. |
| **Глобальный контейнер** | В крайних случаях (скрипты, CLI). | Максимальная гибкость. | Непредсказуемое поведение, сложно тестировать. |

### Мой совет

1.  Сначала спросите себя: **«Почему они зависят друг от друга?»**
    *   Если для получения данных → **Репозиторий**.
    *   Если для реакции → **События**.
2.  Если это действительно тесная связка, которую нельзя разделить → **`#[Required]`**.
3.  Избегайте `ServiceLocator` в конструкторах, если можно использовать `#[Required]`.

Циклическая зависимость — это всегда **code smell**, указывающий на необходимость рефакторинга границ ответственности.

> ⏱ 31.2s · prompt 15843 tok · output 2320 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Это классическая задача разделения критического пути (Critical Path) и фоновых задач. Цель: пользователь получает ответ `201 Created` как можно быстрее, а «шумные» операции (отправка email, обновление ElasticSearch/MongoDB) выполняются асинхронно.

В Symfony есть три основных архитектурных подхода к решению этой задачи. Я разберу их от простейшего к наиболее масштабируемому.

---

### Вариант 1: Synchronous Events (Синхронные события) — ❌ Не подходит для вашей задачи
Если вы просто используете `EventDispatcher` и слушатели выполняются синхронно:
1.  Контроллер создает пользователя.
2.  Диспатчит событие `UserRegisteredEvent`.
3.  Слушатель `SendEmailListener` шлет письмо (задержка 1–5 сек).
4.  Слушатель `UpdateSearchIndexListener` обновляет индекс (задержка 0.5–2 сек).
5.  Только после этого контроллер возвращает ответ.

**Итог:** Пользователь ждет 3+ секунды. **Это нарушает требование.**

---

### Вариант 2: Message Bus + Asynchronous Messenger (Symfony Messenger) — ✅ Рекомендуемый стандарт

Symfony Messenger — это встроенный механизм для обработки команд и сообщений, который позволяет легко сделать их асинхронными с помощью транспорта (Transport).

#### Архитектура:
1.  **Handler:** Логика обработки сообщения (отправка email, обновление индекса).
2.  **Transport:** Очередь сообщений (Redis, RabbitMQ, SQLite, PostgreSQL).
3.  **Worker:** Фоновый процесс, который достает сообщения из очереди и вызывает хендлеры.

#### Реализация:

**1. Определение сообщения (Message)**
```php
// src/Message/UserRegistered.php
class UserRegistered
{
    public function __construct(
        private readonly int $userId
    ) {}

    public function getUserId(): int
    {
        return $this->userId;
    }
}
```

**2. Хендлеры (Handlers)**
Каждый хендлер выполняет одну задачу.

```php
// src/MessageHandler/SendWelcomeEmailHandler.php
#[AsMessageHandler(messenger-bus-name: 'async')] // Указываем, что это асинхронная шина
class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(UserRegistered $message): void
    {
        $user = $this->userRepository->find($message->getUserId());
        
        $email = (new Email())
            ->from('no-reply@myapp.com')
            ->to($user->getEmail())
            ->subject('Welcome!')
            ->html('<h1>Hello, ' . $user->getName() . '</h1>');

        $this->mailer->send($email);
    }
}

// src/MessageHandler/UpdateSearchIndexHandler.php
#[AsMessageHandler(messenger-bus-name: 'async')]
class UpdateSearchIndexHandler
{
    public function __construct(
        private SearchClientInterface $searchClient,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(UserRegistered $message): void
    {
        $user = $this->userRepository->find($message->getUserId());
        $this->searchClient->indexUser($user);
    }
}
```

**3. Диспатч в сервисе/контроллере**
```php
// src/Service/UserRegistrationService.php
class UserRegistrationService
{
    public function __construct(
        private MessageBusInterface $bus,
        private UserRepositoryInterface $repository
    ) {}

    public function register(array $data): User
    {
        $user = new User(...);
        $this->repository->save($user); // Синхронно: критично для бизнеса

        // Асинхронно: отправляем сообщения в очередь
        $this->bus->dispatch(new UserRegistered($user->getId()));

        return $user; // Ответ уходит мгновенно
    }
}
```

**4. Конфигурация (`config/packages/messenger.yaml`)**
```yaml
framework:
    messenger:
        buses:
            async:
                middleware:
                    - 'messenger.retry_strategy'
        
        transports:
            async:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%' # Например: redis://localhost:6379 или amqp://localhost/
```

**5. Запуск воркера (CLI)**
В терминале (или через Supervisor/Systemd) запускается процесс, который слушает очередь:
```bash
bin/console messenger:consume async -vv
```

#### Trade-offs:
| Плюсы | Минусы |
| :--- | :--- |
| **Отделение:** Логика отправки email отделена от регистрации. | **Сложность:** Нужен брокер (Redis/RabbitMQ). |
| **Надежность:** Если email-сервер упал, сообщение не потеряется (retry strategy). | **Латентность:** Есть задержка (миллисекунды-секунды) перед обработкой. |
| **Масштабируемость:** Можно запустить 10 воркеров для параллельной обработки. | **Дублирование:** Нужно следить за идемпотентностью хендлеров (если сообщение пришло дважды). |
| **Гибкость:** Легко добавить новый эффект (например, аналитику) — просто новый хендлер. | **Отладка:** Сложнее отследить поток данных, чем в синхронном коде. |

---

### Вариант 3: Job Queue (Laravel-style) или специализированные воркеры

Если вы не хотите использовать Messenger для всего, но нужны тяжелые фоновые задачи, часто используют библиотеки типа **Predis + PHP-Script** или интеграции с **Sidekiq (Ruby)** через HTTP API. Но в экосистеме Symfony Messenger — это «родной» путь.

Однако, есть нюанс: **Messenger — это не замена очередей задач (Job Queues) для очень тяжелых операций.**
*   Если «обновление поискового индекса» занимает 10 минут (пересчет всего индекса), лучше вынести это в отдельную фоновую задачу через **AmqpQueue** или **Redis Lists**, где можно управлять приоритетами и дедупликацией легче, чем в стандартном Messenger.

---

### Вариант 4: Direct API Calls to Microservices (Микросервисный подход)

Если у вас микросервисы:
1.  Сервис Users создает пользователя.
2.  По завершении транзакции отправляет HTTP-запрос (или Kafka-сообщение) сервису Email и сервису Search.
3.  Эти сервисы обрабатывают запросы асинхронно (у них свои очереди).

Это по сути то же самое, что и Messenger, но с более высокой степенью распределенности.

#### Trade-offs:
*   **Плюсы:** Полное изолирование ресурсов. Если Search упал, Email продолжает работать.
*   **Минусы:** Сетевые накладные расходы, сложность распределенных транзакций (Eventual Consistency).

---

### Сравнение вариантов

| Критерий | Синхронные события | Symfony Messenger (Async) | Direct API/Kafka |
| :--- | :--- | :--- | :--- |
| **Скорость ответа** | Медленно | **Быстро** | Быстро |
| **Надежность** | Средняя (ошибка = потерян эффект) | **Высокая** (очередь + retry) | Высокая (зависит от брокера) |
| **Сложность внедрения** | Низкая | **Средняя** | Высокая |
| **Масштабируемость** | Низкая | **Высокая** | Очень высокая |
| **Где использовать** | Лайтвей-эффекты (логи) | **Стандарт для монолитов** | Микросервисы, тяжелые нагрузки |

---

### Рекомендуемая стратегия (Best Practice)

Для вашего случая (email + search index) я бы выбрал **Symfony Messenger с асинхронным транспортом**.

#### Важные детали реализации:

1.  **Идемпотентность:**
    Так как сообщения могут быть обработаны дважды (на пример, если воркер упал после обработки, но до подтверждения), хендлеры должны быть идемпотентными.
    ```php
    // В UpdateSearchIndexHandler
    public function __invoke(UserRegistered $message): void
    {
        // Если пользователь уже в индексе, replace работает корректно.
        // Если бы мы делали "increment count", нужно было бы проверять, не было ли уже обработки.
        $this->searchClient->upsertUser($message->getUserId());
    }
    ```

2.  **Retry Strategy:**
    Настройте `messenger.retry_strategy`, чтобы при ошибке (например, таймаут email-сервера) сообщение возвращалось в очередь с экспоненциальной задержкой.
    ```yaml
    framework:
        messenger:
            failure_transport: failed
            buses:
                async:
                    middleware:
                        - 'messenger.retry_strategy'
                    default_retries: 3
                    default_delay: 1000
                    max_retries: 5
    ```

3.  **Handling Failed Messages:**
    Сообщения, которые не удалось обработать после всех ретраев, попадают в транспорт `failed`. Их нужно периодически мониторить (через админку или скрипт), чтобы ничего не пропало без вести.

4.  **Тестирование:**
    *   Юнит-тесты: Мокируем `MessageBusInterface` и проверяем, что `dispatch` был вызван с правильными аргументами.
    *   Интеграционные тесты: Имитируем задержку в хендлере и проверяем, что ответ контроллера пришел мгновенно, а хендлер отработал позже.

### Итог

1.  **Контроллер/Сервис:** Сохраняет пользователя в БД (синхронно) → Диспатчит `UserRegistered` сообщение → Возвращает ответ.
2.  **Очередь (Redis/RabbitMQ):** Хранит сообщение.
3.  **Воркер (Messenger:consume):** Берет сообщение → Запускает `SendWelcomeEmailHandler` и `UpdateSearchIndexHandler` параллельно (если они независимы) или последовательно.
4.  **Результат:** Пользователь не ждет отправки email или обновления индекса. Система устойчива к сбоям внешних сервисов.

Это стандартный, проверенный временем подход в Symfony-приложениях средней и высокой нагрузки.

> ⏱ 33.8s · prompt 20119 tok · output 2477 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony устроен по модели **MVC + Middleware**, где ответственность за безопасность разделена на три четких слоя:

1.  **Aутентификация (Authentication):** «Кто вы?» (Идентификация пользователя).
2.  **Авторизация (Authorization):** «Что вы можете делать?» (Права доступа).
    *   **Роли (Roles):** Грубый контроль (группы).
    *   **Voters:** Тонкий контроль (контекстные правила).

Давайте разберем каждый слой детально.

---

### 1. Аутентификация: «Кто вы?»

Это процесс проверки учетных данных (логин/пароль, JWT, OAuth) и создания **Security Token**.

*   **Механизм:** `UserProviderInterface` находит пользователя по логину, а `PasswordHasherInterface` проверяет хеш пароля.
*   **Результат:** В сессии (или токене) создается объект `User`, который содержит метод `getRoles()`.
*   **Контекст:** После аутентификации объект `User` доступен через `$this->getUser()` в контроллерах и через сервис `Security` в любом месте приложения.

> **Важно:** Аутентификация происходит **один раз** (например, при логине или каждом HTTP-запросе, если используется Stateless JWT). Далее, для каждого запроса, Symfony просто проверяет наличие валидного токена.

---

### 2. Авторизация: «Что вы можете делать?»

Здесь Symfony предлагает два уровня детализации: **Роли** и **Voters**.

#### А. Роли (Roles) — «Грубая» фильтрация

Роли — это простые строки (например, `ROLE_ADMIN`, `ROLE_EDITOR`). Они хранятся в профиле пользователя в БД.

**Как работает:**
1.  Вы объявляете права на уровне маршрута или метода.
2.  Symfony проверяет, есть ли у текущего пользователя одна из указанных ролей.

**Примеры использования:**

*   **Атрибуты (Attribute-based):**
    ```php
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users')]
    public function listUsers(): Response { ... }
    ```

*   **Аннотации/Конфиги (Legacy/YAML):**
    ```yaml
    security:
        access_control:
            - { path: ^/admin, roles: ROLE_ADMIN }
            - { path: ^/profile, roles: IS_AUTHENTICATED_FULLY }
    ```

*   **Проверка в коде:**
    ```php
    if (!$this->isGranted('ROLE_ADMIN')) {
        throw new AccessDeniedException();
    }
    ```

**Когда использовать роли:**
*   Когда права зависят **только от типа пользователя**, а не от данных.
*   Пример: «Администраторы могут видеть страницу `/admin`», «Редакторы могут создавать статьи».
*   Это **статичное** правило.

#### Б. Voters — «Тонкая» фильтрация (Контекстная авторизация)

Voter — это класс-эксперт, который знает правила доступа к **конкретному объекту** или **действию**. Он получает на вход пользователя и объект (или аргументы) и принимает решение: `GRANT`, `DENY` или `ABSTAIN`.

**Когда использовать Voters:**
*   Когда права зависят от **отношения пользователя к объекту**.
*   Примеры:
    *   «Пользователь может удалить **свою** статью, но не чужую».
    *   «Менеджер может видеть заказы **своего отдела**».
    *   «Автор может редактировать пост, пока он не был опубликован и не истек дедлайн».
*   Это **динамическое**, контекстное правило.

**Пример Voter'а:**

```php
class ArticleVoter extends Voter
{
    // Определяем, какие действия мы поддерживаем
    protected function supports(string $attribute, ?object $subject): bool
    {
        // $attribute — это то, что мы просим проверить (например, 'EDIT')
        // $subject — это объект, к которому применяем правило (Article)
        return in_array($attribute, ['EDIT', 'DELETE']) 
               && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, object $subject, UserInterface $user): bool
    {
        /** @var Article $article */
        $article = $subject;

        switch ($attribute) {
            case 'EDIT':
                // Правило: Только автор может редактировать
                return $article->getAuthor()->getId() === $user->getId();
            
            case 'DELETE':
                // Правило: Автор может удалить только черновики, Админ — любые
                if ($user->hasRole('ROLE_ADMIN')) {
                    return true;
                }
                return $article->getAuthor()->getId() === $user->getId()
                       && $article->getStatus() === 'DRAFT';
        }

        return false;
    }
}
```

**Использование Voter'а:**

*   **В контроллере:**
    ```php
    #[Route('/article/{id}/edit')]
    public function edit(Article $article): Response
    {
        // $article загружен через Route Param Converter
        if (!$this->isGranted('EDIT', $article)) {
            throw new AccessDeniedException('You can only edit your own drafts.');
        }
        // ...
    }
    ```

*   **В шаблонах Twig:**
    ```twig
    {% if is_granted('EDIT', article) %}
        <a href="{{ path('edit_article', {id: article.id}) }}">Edit</a>
    {% endif %}
    ```

*   **В API Platform (если используется):**
    ```php
    #[ApiResource(
        operations: [
            'edit' => [
                'controller' => ...,
                'extra_properties' => [
                    'openapi_context' => ['x-permission' => 'EDIT']
                ]
            ]
        ]
    )]
    class Article { ... }
    ```

---

### Сравнение: Роли vs Voters

| Критерий | Роли (Roles) | Voters |
| :--- | :--- | :--- |
| **Уровень детализации** | Низкий (Группа пользователей) | Высокий (Конкретный объект/действие) |
| **Зависимость от данных** | Нет (статично) | Да (динамично, зависит от полей объекта) |
| **Сложность** | Простая | Средняя (требует написания логики) |
| **Производительность** | Очень высокая (быстрая проверка строк) | Ниже (вызов PHP-кода, возможные запросы в БД) |
| **Гибкость** | Низкая | Высокая |
| **Пример вопроса** | «Вы администратор?» | «Это ваша статья? И она черновик?» |

---

### Стратегия выбора: Когда что применять?

#### Правило «Сначала роли, потом Voters»

1.  **Начните с ролей.**
    *   Если действие доступно всем пользователям с ролью `ROLE_USER`, используйте `#[IsGranted('ROLE_USER')]`.
    *   Если действие доступно только админам, используйте `ROLE_ADMIN`.
    *   Это дешево и быстро.

2.  **Переходите к Voter'ам, когда появляются условия «Свой/Чужой».**
    *   Если правило звучит как *«Пользователь может сделать X, если объект принадлежит ему»* или *«если объект находится в определенном состоянии»*, это территория Voter'ов.
    *   Роли не могут выразить «принадлежность». Для этого нужны данные объекта.

3.  **Комбинация.**
    *   Часто Voter сначала проверяет роль (например, админ обходит проверки), а потом проверяет данные.
    *   Пример из кода выше: `if ($user->hasRole('ROLE_ADMIN')) return true;` внутри `voteOnAttribute`.

---

### Лучшие практики (Best Practices)

1.  **Не логируйте чувствительные данные в Voter'ах.**
    *   Voter'ы вызываются при каждой проверке права. Если внутри вы делаете запрос к БД для получения имени пользователя или email, убедитесь, что это кэшируется или не критично. Лучше передавать объект целиком, если данные уже загружены.

2.  **Используйте `ABSTAIN`.**
    *   Если Voter не понимает контекст (например, объект другого типа), он должен вернуть `ABSTAIN`, чтобы позволить другим Voter'ам (или ролям) принять решение.
    *   `supports()` уже отфильтровывает нерелевантные вызовы, но `ABSTAIN` полезен, если один Voter отвечает за несколько типов объектов.

3.  **Логируйте отказы (Optional).**
    *   Symfony имеет встроенный `AccessDeniedException`. Вы можете добавить listener, который логирует, *почему* пользователю отказано (какой Voter, какая причина). Это критично для отладки сложных прав доступа.

4.  **Идемпотентность и кэширование.**
    *   Если Voter делает дорогие вычисления (например, проверку прав в другом микросервисе через HTTP), рассмотрите кэширование результата проверки на время запроса.

5.  **Разделяйте ответственность.**
    *   Один Voter — один тип объектов/действий.
    *   `ArticleVoter` — для статей.
    *   `OrderVoter` — для заказов.
    *   Не делайте `UniversalVoter`, который знает всё.

### Итог

*   **Аутентификация** определяет **кто** вы.
*   **Роли** отвечают на вопрос **«Кто ты по должности?»**.
*   **Voters** отвечают на вопрос **«Имеешь ли ты право на ЭТО конкретное действие над ЭТИМ объектом?»**.

Для регистрации пользователя (`POST /register`) обычно хватает простой проверки, что пользователь не авторизован. Для просмотра профиля (`GET /profile/{id}`) нужны роли (если админ) + Voter (если это твой профиль). Для удаления пользователя (`DELETE /user/{id}`) точно нужен Voter, потому что нужно проверить, не удаляет ли пользователь самого себя (или запретить это) и имеет ли он право удалять именно этого конкретного пользователя.

> ⏱ 33.1s · prompt 22638 tok · output 2396 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение Value Objects (VO) в Doctrine — одна из самых частых и болезненных точек в DDD-проектировании на PHP.

**Проблема:**
1.  **Immutability:** VO должен быть неизменяемым (`readonly` в PHP 8.1+).
2.  **Validation:** VO должен быть создан только в валидном состоянии (инварианты проверяются в конструкторе).
3.  **Doctrine:** ORM работает с «сырыми» данными (массивы, строки, числа), а не с объектами домена.

Если вы просто маппите VO как обычное поле (`private Money $amount;`), вы сталкиваетесь с проблемой:
*   При загрузке из БД Doctrine создает объект через `new`, но **не вызывает ваш валидирующий конструктор** (если только вы не используете магические методы, что ломает инварианты).
*   При сохранении Doctrine должен уметь «развернуть» объект обратно в примитивы.

Вот три основных способа решения, от самого «правильного» в теории до самого практичного.

---

### Способ 1: Custom Types (Рекомендуемый для большинства случаев)

Вы создаете кастомный тип в Doctrine, который знает, как конвертировать строку/число из БД в объект VO и обратно.

#### Архитектура:
1.  **VO:** `Money` (содержит `int $amount`, `string $currency`).
2.  **Custom Type:** `MoneyType` наследуется от `Doctrine\DBAL\Types\Type`.
3.  **Mapping:** Поле в сущности аннотируется/маркапится этим типом.

#### Код:

**1. Value Object (Неизменяемый, валидирующий)**
```php
final class Money
{
    public function __construct(
        private readonly int $amount,      // Храним в минимальных единицах (центы/копейки)
        private readonly string $currency
    ) {
        if ($amount < 0) {
            throw new \DomainException("Amount cannot be negative");
        }
    }

    // Геттеры...
    public function getAmount(): int { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }

    // Методы для сериализации (используются в Type)
    public function toSqlString(): string
    {
        // Формат хранения: "100:USD"
        return $this->amount . ':' . $this->currency;
    }

    public static function fromSqlString(string $sql): self
    {
        $parts = explode(':', $sql, 2);
        if (count($parts) !== 2) {
            throw new \DomainException("Invalid money format");
        }
        
        $amount = (int) $parts[0];
        $currency = $parts[1];
        
        // Конструктор автоматически валидирует инварианты!
        return new self($amount, $currency);
    }
}
```

**2. Custom Doctrine Type**
```php
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MoneyType extends Type
{
    private const NAME = 'money';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Обычно храним как VARCHAR, потому что формат может меняться
        return 'VARCHAR(255)'; 
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }
        return Money::fromSqlString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!($value instanceof Money)) {
            throw new \InvalidArgumentException('Expected Money instance');
        }
        return $value->toSqlString();
    }

    // Важно для Doctrine 2.x, чтобы кешить типы
    public function requiresSQLConversion(): bool
    {
        return true;
    }
}
```

**3. Регистрация типа**
В `services.yaml` или через автоконфигурацию:
```yaml
doctrine:
    dbal:
        types:
            money: App\Domain\ValueObject\MoneyType
```

**4. Использование в сущности**
```php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'money')] // Используем наш кастомный тип
    private Money $totalAmount;

    public function getTotalAmount(): Money
    {
        return $this->totalAmount;
    }
    
    public function setTotalAmount(Money $amount): void
    {
        $this->totalAmount = $amount;
    }
}
```

#### Плюсы:
*   **Инварианты сохраняются:** Каждый раз, когда объект `Money` создается из БД, он проходит через `Money::fromSqlString()` → `new Money()`, где проверяются правила.
*   **Чистый домен:** В доменных моделях нет следов ORM (ни `array`, ни `string`).
*   **Простота:** Не нужно писать репозиторий для каждого VO.

#### Минусы:
*   **Хранение в одной колонке:** Мы склеиваем `amount` и `currency` в одну строку (`"100:USD"`). Если вы захотите индексировать по валюте или делать SQL-запрос `WHERE currency = 'USD'`, вам придется парсить строку в SQL (что дорого и грязно) или использовать Full-Text Search.
*   **Несколько колонок:** Если VO имеет много полей, кастомный тип становится громоздким.

---

### Способ 2: Embedded Objects (Встроенные объекты)

Doctrine позволяет маппить VO как набор отдельных колонок.

#### Архитектура:
1.  **VO:** `Address` (содержит `string $city`, `string $zip`).
2.  **Mapping:** `#[ORM\Embedded(class: Address::class)]`.

#### Код:

**1. Value Object**
```php
final class Address
{
    public function __construct(
        public readonly string $city,
        public readonly string $zip
    ) {
        if (strlen($zip) !== 5) {
            throw new \DomainException("Zip must be 5 chars");
        }
    }
}
```

**2. Сущность**
```php
#[ORM\Entity]
class User
{
    #[ORM\Id, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;
}
```

#### Что произойдет в БД:
Таблица `users` будет иметь колонки:
*   `id`
*   `city`
*   `zip`

#### Плюсы:
*   **Нормализация:** Каждое поле VO — отдельная колонка. Можно индексировать `city`, фильтровать по `zip`.
*   **Стандартный SQL:** Можно писать нативные SQL-запросы к этим колонкам.

#### Минусы (Критичные!):
1.  **Ломает Immutability:**
    *   Когда Doctrine hydrates объект, он сначала создает `Address`, затем **присваивает** поля `city` и `zip` через магию (или сеттеры).
    *   Если свойства `Address` объявлены `readonly`, Doctrine может упасть с ошибкой, так как он пытается перезаписать их после создания.
    *   *Рабочий лайфхак:* Делать свойства в VO **не readonly**, а использовать геттеры и один сеттер, или использовать `protected` и магию `__set` (что грязно).
2.  **Скрытые зависимости:**
    *   Если вы измените структуру VO (добавите поле `country`), вам нужно будет менять схему БД и маппинг.
    *   Сложнее тестировать, так как VO тесно связан с хранилищем.
3.  **Нарушение инкапсуляции:**
    *   Technically, вы даете доступ к «инсайдам» VO через ORM-маппинг.

---

### Способ 3: Manual Mapping in Repository (Ручной маппинг)

Самый «чистый» по DDD, но самый трудоемкий.

#### Архитектура:
1.  **VO:** `PhoneNumber`.
2.  **Сущность:** Хранит VO как объект (без маппинга к колонкам через аннотации, либо маппит в `array`/`string` явно).
3.  **Repository:** Сам вручную конвертирует строки из БД в объекты VO.

#### Код:

```php
#[ORM\Entity]
class User
{
    // Храним как string, но в домене работаем с VO
    #[ORM\Column(type: 'string')]
    private string $phoneRaw; // "79111234567"

    public function getPhoneNumber(): PhoneNumber
    {
        // Ленивая сборка? Нет, лучше сразу
        return PhoneNumber::fromRawString($this->phoneRaw);
    }

    public function setPhoneNumber(PhoneNumber $phone): void
    {
        $this->phoneRaw = $phone->toRawString();
    }
}
```

Или, если вы хотите полную изоляцию:
```php
#[ORM\Entity]
class User
{
    // Храним как JSON-строку или массив
    #[ORM\Column(type: 'json')]
    private array $phoneData; // ["country": "+7", "number": "123"]

    public function getPhoneNumber(): PhoneNumber
    {
        return PhoneNumber::fromArray($this->phoneData);
    }

    public function setPhoneNumber(PhoneNumber $phone): void
    {
        $this->phoneData = $phone->toArray();
    }
}
```

#### Плюсы:
*   **Полный контроль:** Вы сами решаете, как данные хранятся и как превращаются в VO.
*   **Гибкость:** Можно менять формат хранения без изменения VO (через миграции репозитория).

#### Минусы:
*   **Репозиторий-«мост»:** Вы тратите код на конвертацию в каждом репозитории.
*   **Проблема с Embedding:** Если у вас сложная структура (VO внутри VO), ручной маппинг становится кошмаром.

---

### Сравнение и Выбор

| Критерий | Custom Types | Embedded Objects | Manual Mapping |
| :--- | :--- | :--- | :--- |
| **Immutability** | ✅ Отлично | ❌ Сложно/Ломается | ✅ Отлично |
| **Инварианты** | ✅ Проверяются | ⚠️ Зависит от реализации | ✅ Проверяются |
| **SQL-запросы** | ❌ Сложно (1 колонка) | ✅ Отлично (отдельные колонки) | ⚠️ Зависит от формата |
| **Читаемость кода** | ✅ Хорошо | ⚠️ Магия ORM | ✅ Явно |
| **Сложность** | Средняя | Низкая | Высокая |

### Что выбрать?

1.  **Для простых VO (1-2 поля):**
    *   Используйте **Custom Types**. Это баланс между чистотой и производительностью. Храните в одной колонке (VARCHAR). Для простых поисков это приемлемо.

2.  **Для сложных VO (много полей, нужна фильтрация по подполям):**
    *   Используйте **Embedded Objects**. Но будьте готовы работать вокруг ограничений `readonly`.
    *   *Совет:* В PHP 8.1 можно использовать `readonly` только для свойств, которые устанавливаются в конструкторе. Если Doctrine использует магию `__set`, это может конфликтовать. Часто проще делать свойства `protected` и не `readonly`, но обеспечивать инкапсуляцию геттерами.

3.  **Для критически важных бизнес-объектов:**
    *   Используйте **Manual Mapping**. Это самый надежный способ гарантировать, что инварианты домена не будут нарушены из-за специфики ORM.

### Подводные камни

1.  **Кэширование:** Если вы используете Custom Types, убедитесь, что они зарегистрированы. Иначе Doctrine упадет при старте.
2.  **Индексы:** Если вы храните VO в одной строке (Custom Type), вы не сможете эффективно индексировать отдельные части.
3.  **Сравнение:** `Money::equals()` должен учитывать все поля. При маппинге через Embedded Objects, Doctrine может сравнивать объекты по ссылке или полям, что нужно проверить в ваших юнит-тестах.

**Мой совет:** Начните с **Custom Types**. Это самый быстрый путь к «чистому» коду в домене. Если столкнетесь с проблемами производительности или необходимости сложных SQL-фильтров по частям VO, тогда переходите на Embedded Objects или Manual Mapping.

> ⏱ 42.7s · prompt 25083 tok · output 3075 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Это один из самых сложных и спорных моментов в DDD на PHP. В чистом DDD Агрегат — это **единица консистентности**. Это означает, что изменение состояния агрегата (добавление/удаление элементов) и его сохранение должны происходить атомарно.

Doctrine (ORM) работает с инстанциями (экземплярами) объектов. Если вы «забудете» сохранить дочернюю сущность, она не попадет в БД, хотя логически она часть целого.

Вот как я организую это на примере домена **Order (Заказ)**.

---

### 1. Модель домена (Domain Layer)

Здесь нет ни `Doctrine`, ни `Symfony`. Только бизнес-логика.

**Order (Aggregate Root)**
Корень агрегата. Он управляет жизненным циклом своих элементов.
```php
final class Order
{
    private ?int $id = null;
    private string $customerEmail;
    /** @var Collection<int, OrderItem> */
    private Collection $items;

    public function __construct(string $customerEmail)
    {
        $this->customerEmail = $customerEmail;
        // Инициализируем коллекцию пустой
        $this->items = new Collection(); 
    }

    // Фабрика для создания нового заказа
    public static function create(string $customerEmail): self
    {
        return new self($customerEmail);
    }

    // Гидратация (восстановление из БД)
    public static function hydrate(int $id, string $customerEmail, Collection $items): self
    {
        $order = new self($customerEmail);
        $order->id = $id;
        $order->items = $items; // Заменяем коллекцию на извлеченную из БД
        return $order;
    }

    // Бизнес-логика: Добавление товара
    public function addItem(ProductId $productId, int $quantity): void
    {
        // Инвариант: нельзя добавить больше 100 единиц
        $currentTotal = $this->items->reduce(function ($carry, OrderItem $item) use ($productId) {
            if ($item->getProductId() === $productId) {
                return $carry + $item->getQuantity();
            }
            return $carry;
        }, 0);

        if ($currentTotal + $quantity > 100) {
            throw new \DomainException("Max 100 units per item");
        }

        $item = new OrderItem($productId, $quantity);
        $this->items->add($item);
    }

    // Геттеры...
    public function getItems(): Collection { return $this->items; }
    public function getCustomerId(): ?int { return $this->id; }
}
```

**OrderItem (Child Entity)**
Дочерняя сущность. Она не может существовать вне `Order`.
```php
final class OrderItem
{
    private ?int $id = null;
    private ProductId $productId;
    private int $quantity;

    public function __construct(ProductId $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    // Геттеры...
    public function getProductId(): ProductId { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getId(): ?int { return $this->id; }
}
```

---

### 2. Инфраструктура: Маппинг в Doctrine

Здесь возникает вопрос: **Как маппить коллекцию?**

#### Способ A: `ORM\OneToMany` (Рекомендуемый для простых случаев)
Мы маппим связь «Один ко многим».

```php
#[ORM\Entity]
class Order
{
    // ... остальные поля ...

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $customerEmail;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private Collection $items;
    
    // ...
}

#[ORM\Entity]
class OrderItem
{
    // ... остальные поля ...

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity=Order::class, inversedBy="items")]
    #[ORM\JoinColumn(nullable=false)]
    private ?Order $order = null;

    // ...
}
```

**Ключевые моменты маппинга:**
1.  **`mappedBy="items"`**: Связь установлена с корня агрегата. Это правильно, так как корень владеет данными.
2.  **`cascade={"persist", "remove"}`**: Это критично. Когда вы сохраняете `Order`, Doctrine должен автоматически сохранить новые `OrderItem`, добавленные через `addItem()`.
3.  **`orphanRemoval=true`** (опционально, но рекомендую): Если вы удаляете элемент из коллекции `$order->items` в PHP, Doctrine также удалит эту запись из БД.

#### Способ B: `Embedded` (Для простых VO/Items)
Если `OrderItem` очень прост (2-3 поля) и не имеет собственной логики, можно хранить его как встроенный объект. Но тогда вы теряете возможность иметь ID у товара (если он нужен для бизнес-логики) и сложнее управлять каскадами. Для сущностей (Entity) лучше использовать `OneToMany`.

---

### 3. Репозиторий (Repository)

Репозиторий — это порт между доменом и инфраструктурой.

```php
interface OrderRepository
{
    public function save(Order $order): void;
    public function findById(int $id): ?Order;
}

class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Order $order): void
    {
        $this->em->persist($order);
        // ВАЖНО: flush НЕ здесь! (См. раздел 4)
    }

    public function findById(int $id): ?Order
    {
        return $this->em->getRepository(Order::class)->find($id);
    }
}
```

> **Вопрос:** Почему `flush` не в репозитории?
> Потому что `save()` может вызываться несколько раз в рамках одного бизнес-процесса (например, добавление 5 товаров подряд). Если мы будем делать `flush` после каждого `save()`, мы получим 5 HTTP-запросов к БД вместо 1.

---

### 4. Приложение: Управление транзакциями (Application Service)

Это сердце системы. Именно здесь решается, **кто вызывает flush** и **какова граница транзакции**.

Правило: **Один Unit of Work = Одна бизнес-операция.**

```php
class AddItemsToOrderService
{
    public function __construct(
        private OrderRepository $orderRepo,
        private ProductRepository $productRepo,
        private EntityManagerInterface $em // Нужен для управления транзакциями
    ) {}

    public function handle(int $orderId, array $itemsToAdd): void
    {
        // 1. Начинаем транзакцию (Unit of Work)
        $this->em->transactional(function () use ($orderId, $itemsToAdd) {
            
            // 2. Загружаем агрегат (корень)
            $order = $this->orderRepo->findById($orderId);
            
            if (!$order) {
                throw new OrderNotFoundException($orderId);
            }

            // 3. Бизнес-логика: Модифицируем агрегат
            foreach ($itemsToAdd as $itemData) {
                $product = $this->productRepo->findById($itemData['product_id']);
                $order->addItem($product->getId(), $itemData['qty']);
            }

            // 4. Сохраняем агрегат (persist)
            // Здесь мы говорим Doctrine: "Вот этот объект изменился, помни это"
            $this->orderRepo->save($order);
            
            // 5. FLUSH выполняется ВНУТРИ транзакции, после замыкания
            // Если что-то пошло не так (exception), все изменения откатятся.
            
        }, maxRetries: 0, rollbackOnException: true);
        
        // Если код дошел сюда, транзакция успешно закомиттена.
    }
}
```

#### Как работает `transactional()`?
1.  Создает новую транзакцию.
2.  Выполняет замыкание (ваш код).
3.  Внутри замыкания вы вызываете `persist`.
4.  После выполнения замыкания (если не было исключений) автоматически вызывается `$em->flush()`.
5.  Если внутри было исключение → `$em->rollback()`.

**Почему это правильно?**
*   **Атомарность:** Все изменения в `Order` и `OrderItem` сохраняются одновременно или не сохраняются вовсе.
*   **Контроль:** Вы явно видите границы бизнес-действия.
*   **Производительность:** `flush` вызывается только один раз в конце, а не после каждой операции.

---

### 5. Подводные камни и нюансы

#### А. Проблема «Lost Update» (Потеря обновления)
Если два пользователя одновременно редактируют один заказ:
1.  Пользователь A загрузил `Order #1` (Items: 1, 2).
2.  Пользователь B загрузил `Order #1` (Items: 1, 2).
3.  Пользователь A добавил Item 3, сохранил.
4.  Пользователь B добавил Item 4, сохранил.

**Что будет?** Зависит от реализации. В `Doctrine` с `Cascade Persist`, если вы делаете `persist($order)`, он пересоздаст или обновит связанные сущности. Но если B перезаписал коллекцию, Item 3 может быть удален (orphanRemoval).
**Решение:** Использовать **Optimistic Locking** (`#[ORM\Version]`).
```php
#[ORM\Version]
#[ORM\Column(type: 'integer')]
private int $version;
```
Тогда при сохранении B, Doctrine увидит, что версия в БД изменилась, и бросит `OptimisticLockException`.

#### Б. Ленивая загрузка (Lazy Loading) и N+1
Если вы получаете список заказов и хотите показать товары:
```php
$orders = $this->em->getRepository(Order::class)->findAll();
foreach ($orders as $order) {
    echo $order->getItems()->count(); // N+1 запрос! Каждый вызов count может триггерить запрос
}
```
**Решение:** Использовать `DQL` с `JOIN FETCH`:
```php
$dql = 'SELECT o FROM Order o JOIN o.items i WHERE ...';
```
Или использовать `Paginator` из Doctrine.

#### В. Создание дочерних сущностей
Важно: **Никогда не создавайте `OrderItem` напрямую вне `Order` и не присваивайте его `Order`**, если вы не уверены, что он уже связан.
Лучший паттерн:
```php
$order = new Order(...);
$order->addItem($product, 5); // Внутри addItem создается новый OrderItem и добавляется в коллекцию
```
Это гарантирует, что `OrderItem` всегда имеет ссылку на свой `Order` (через `mappedBy` или явное присваивание `setItemOrder($order)`).

---

### Итоговая архитектура

1.  **Domain:** `Order` (Root) владеет коллекцией `OrderItem` (Child). Методы модификации меняют состояние коллекции.
2.  **Mapping:** `OneToMany` + `cascade={"persist", "remove"}`.
3.  **Application Service:** Оборачивает бизнес-логику в `$em->transactional()`.
4.  **Flush:** Вызывается автоматически в конце транзакции. Репозиторий только `persist`-ит.

Это обеспечивает:
*   **Инварианты:** Неисполнимые состояния агрегата не попадут в БД.
*   **Производительность:** Минимум запросов к БД.
*   **Надежность:** Атомарность операций.

> ⏱ 40.4s · prompt 28220 tok · output 2864 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика N+1 — это классическая задача оптимизации. В Symfony/Doctrine «N+1» означает: 1 запрос на загрузку списка сущностей (например, 100 заказов) и N (100) дополнительных запросов на загрузку их связанных данных (например, товаров внутри каждого заказа или автора каждой статьи).

Вот мой пошаговый процесс диагностики и методы решения с анализом trade-offs.

---

### Шаг 1: Диагностика (Как найти проблему?)

Не угадывайте. Собирайте данные.

#### 1. Log-проверка (Быстрый способ)
Включите SQL-логирование в окружении `dev` или `test`.
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        logging:
            sql: true
            logger: dbal.logger
```
Или используйте команду:
```bash
bin/console doctrine:query:sql "SELECT * FROM orders LIMIT 10"
```
Но лучше всего посмотреть в браузерные инструменты (Chrome DevTools > Network > XHR/Fetch) или в логи веб-сервера, если вы выводите SQL-логи. Вы увидите паттерн:
1.  `SELECT * FROM orders ...`
2.  `SELECT * FROM order_items WHERE order_id = 1`
3.  `SELECT * FROM order_items WHERE order_id = 2`
4.  ... и так 100 раз.

#### 2. Doctrine Debug Bar (Symfony Web Debug Toolbar)
Если вы используете `web-profiler-bundle` или аналог, на странице с профилером есть вкладка **DBAL**. Там видно количество запросов и их время. Если вы видите 100+ одинаковых по структуре запросов, это N+1.

#### 3. Скрипт для автоматической детекции (Продвинутый)
Можно написать небольшой скрипт, который прогоняет типичные сценарии и логирует количество запросов через `Doctrine\ORM\Tools\Debug` или просто подсчетом вызовов `execute()` в декорированном Connection.

> **Совет:** В продакшене часто включают `doctrine.dbal.connection.event_manager` или используют middleware, которое логирует количество SQL-запросов на страницу. Если на странице списка товаров их 150, а должно быть 2–3 — проблема найдена.

---

### Шаг 2: Способы решения и Trade-offs

Есть 4 основных подхода. Их выбор зависит от объема данных, частоты изменений и сложности запроса.

#### Способ 1: `JOIN FETCH` в DQL (Стандарт)

Самый популярный способ. Мы просим Doctrine загрузить связанные данные в одном запросе, используя SQL `JOIN`.

**Пример:**
```php
// Было (N+1):
$orders = $em->getRepository(Order::class)->findAll();
foreach ($orders as $order) {
    $items = $order->getItems(); // Триггерит lazy load (N запросов)
}

// Стало (1 запрос):
$dql = 'SELECT o FROM App\Entity\Order o JOIN FETCH o.items';
$orders = $em->createQuery($dql)->getResult();
```

**Trade-offs:**
| Плюсы | Минусы |
| :--- | :--- |
| **Простота:** Один запрос вместо N+1. | **Рост размера ответа:** Если у каждого из 100 заказов по 50 товаров, в памяти будет 5000 объектов. |
| **Контроль:** Вы явно указываете, какие отношения подгружать. | **Сложные JOIN-ы:** Если нужно подгрузить 3 уровня вложенности, запрос становится огромным и медленным на стороне БД. |
| **Безопасность:** Работает с любыми отношениями (ManyToMany, OneToMany). | **Гибкость:** Сложно динамически менять набор подгружаемых полей в зависимости от контекста без написания разных DQL-запросов. |

> **Важно:** `JOIN FETCH` работает только для **коллекций** и **прямых связей**. Для глубокого дерева он начинает страдать от «эффекта Cartesian Product» (декартового произведения), если не использовать `WITH` или правильную структуру DQL.

---

#### Способ 2: `EntityGraph` (Flexibility)

EntityGraph позволяет описывать структуру загрузки данных отдельно от запроса. Полезно, когда вы хотите переиспользовать граф загрузки или строить его динамически.

**Пример:**
```php
$em = $this->getEntityManager();
$graph = $em->createEntityGraph(Order::class);
$graph->addFetch(Order::class, 'items'); // Подгружаем только items

// Применяем граф к существующему запросу
$orders = $em->getRepository(Order::class)->findAll();
// Или используем вместе с Query
$query = $em->createQuery('SELECT o FROM Order o');
$graph->apply($query, Order::class);
$orders = $query->getResult();
```

**Trade-offs:**
| Плюсы | Минусы |
| :--- | :--- |
| **Динамичность:** Можно строить граф на лету (например, подгружать `items` только если есть флаг в запросе). | **Сложность:** Больше кода, чем в `JOIN FETCH`. |
| **Переиспользование:** Можно сохранить граф в константу и использовать в разных сервисах. | **Нечитаемость:** Новичкам сложнее понять, что именно подгружается, если граф строится программно. |
| **Глубина:** Легче управлять глубиной вложенности. | |

> **Когда использовать:** Когда у вас есть несколько сценариев отображения одного сущности (например, «Краткий список заказов» vs «Детальная карточка заказа»), и вы хотите переиспользовать логику загрузки данных.

---

#### Способ 3: DTO (Data Transfer Objects) + Data Mapper (Чистый DDD подход)

Вместо того чтобы заставлять ORM подгружать все сущности, вы пишете **ручной Data Mapper**, который извлекает только нужные данные и мапит их в плоские структуры (массивы или DTO), которые затем превращаются в доменные объекты или сразу отдаются в API.

**Пример:**
```php
class OrderListRepository
{
    public function __construct(private \PDO $db) {}

    /** @return OrderListItem[] */
    public function findAll(): array
    {
        $sql = "
            SELECT 
                o.id, o.created_at,
                COUNT(oi.id) as item_count,
                SUM(oi.price * oi.qty) as total
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            GROUP BY o.id
        ";
        
        $rows = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => OrderListItem::fromArray($row), $rows);
    }
}
```

**Trade-offs:**
| Плюсы | Минусы |
| :--- | :--- |
| **Производительность:** Вы контролируете каждый столбец. Нет лишнего дублирования данных (как при JOIN FETCH с большими коллекциями). | **Ручная работа:** Нужно писать SQL/DQL вручную для каждого вида представления. |
| **Гибкость:** Можно делать `GROUP BY`, агрегации, вычисляемые поля, которые сложно получить через ORM. | **Нарушение инкапсуляции:** Доменные сущности могут знать о структуре данных, если DTO не изолированы. |
| **Масштабируемость:** Лучше всего работает для списков (List Views), где нужно только несколько полей. | **Поддержка:** Если структура БД меняется, нужно обновлять все ручные мапперы. |

> **Когда использовать:** Для **списков** (list views), **дашбордов** и **API-ответов**, где не нужна полная функциональность доменной сущности (например, не нужно вызывать методы `cancel()` на каждом элементе списка).

---

#### Способ 4: Ленивая загрузка с кэшированием (Lazy Loading + Cache)

Иногда N+1 не критичен, если связанные данные кэшируются.

**Механизм:**
1.  Вы используете обычный `findAll()` (1 запрос).
2.  Когда вы обращаетесь к `$order->getItems()`, Doctrine делает запрос.
3.  Но если вы включили **2nd Level Cache** (или используйте Redis для кэширования сущностей), последующие обращения к этим же сущностям не будут ходить в БД.

**Trade-offs:**
| Плюсы | Минусы |
| :--- | :--- |
| **Простота кода:** Не нужно писать DQL с JOIN. | **Сложность настройки:** Настройка 2nd Level Cache в Doctrine — это боль (нужно настраивать region'ы, expiry). |
| **Автоматичность:** Если данные не меняются, вы экономите запросы. | **Проблема инвалидации:** Как только данные меняются, кэш должен сбрасываться. Если нет — устаревшие данные. |
| | **Память:** Кэшированные объекты занимают память в PHP-процессе или Redis. |

> **Когда использовать:** Когда данные **редко меняются** (например, справочники, категории товаров) и **часто читаются**. Для пользовательских заказов (частые изменения) этот метод менее эффективен.

---

### Сравнительная таблица

| Метод | Производительность | Сложность кода | Гибкость | Когда выбирать |
| :--- | :--- | :--- | :--- | :--- |
| **JOIN FETCH** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | Стандартный выбор для простых списков с 1–2 уровнями вложенности. |
| **EntityGraph** | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | Когда нужно переиспользовать графы или строить их динамически. |
| **DTO + Manual Mapper** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ | Для сложных списков, агрегаций, API-ответов, где нужна максимальная скорость. |
| **Cache (2LC)** | ⭐⭐⭐ | ⭐⭐ | ⭐⭐ | Для рединационных данных, справочников. |

---

### Мой алгоритм решения N+1:

1.  **Идентифицируйте:** Какой именно список тормозит? Какой именно JOIN вызывает проблему?
2.  **Если список простой (1 уровень):** Используйте **`JOIN FETCH`**.
    ```php
    $dql = 'SELECT o FROM Order o JOIN FETCH o.items';
    ```
3.  **Если список сложный (3+ уровня) или нужна агрегация:** Напишите **Data Mapper** (ручной SQL/DQL), который возвращает DTO.
    *   Это самый быстрый путь, но требует больше кода.
4.  **Если данные часто читаются и редко меняются:** Рассмотрите **кэширование** конкретных запросов или сущностей.
5.  **Проверьте:** Убедитесь, что после оптимизации вы не перегружаете память (если `JOIN FETCH` загружает 10 000 вложенных объектов, это может быть медленнее, чем N+1 с кэшированием).

**Частая ошибка:** Использование `JOIN FETCH` для бесконечного дерева (Recurring relations). В этом случае лучше использовать рекурсивный SQL или разбить на два запроса (сначала загрузить родительские, потом дочерние по списку ID).

> ⏱ 37.8s · prompt 31135 tok · output 2653 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это отличный вопрос, который затрагивает фундаментальный принцип проектирования систем: **«Не доверяй клиенту»** (и в данном случае — не доверяй только одному слою защиты).

Короткий ответ: **Бизнес-валидация в коде отвечает на вопрос «Правда ли это с точки зрения бизнеса?», а ограничения в БД отвечают на вопрос «Возможно ли вообще такое состояние данных в таблице?»**

Даже если ваша бизнес-логика идеальна, в системе всегда есть «дыры», через которые может просочиться «грязное» или «битое» состояние. БД — это **последний рубеж обороны** (Last Line of Defense).

---

### Почему бизнес-валидации в коде недостаточно?

1.  **Разные пути записи:**
    *   Основная запись идет через API (Symfony) → есть валидация.
    *   Но есть админка (Laravel/React), CLI-скрипты, микросервисы, прямой доступ к БД (DBA, аналитики), миграции, репликация. Не все эти пути проходят через ваш `Service`.
2.  **Race Conditions (Гонки состояний):**
    *   Два пользователя одновременно меняют статус заказа. В коде проверка `if ($order->isPaid())` может пройти, но между проверкой и сохранением (`UPDATE`) другой поток уже поменял статус. Без `CHECK` или `UNIQUE` в БД состояние может стать неконсистентным.
3.  **Атомарность и инварианты данных:**
    *   БД гарантирует, что данные внутри таблицы (и между таблицами) всегда находятся в валидном состоянии *на уровне структуры*. Это дешевле и быстрее, чем пересчитывать инварианты в PHP-коде при каждом чтении.
4.  **Производительность:**
    *   Проверка `UNIQUE` или `FOREIGN KEY` выполняется движком БД (PostgreSQL) на уровне индексов, что часто быстрее, чем выгрузка данных в PHP, их сравнение и логирование ошибки.

---

### Какие средства PostgreSQL используем и зачем?

#### 1. `UNIQUE Constraints` (Уникальность)
**Защита от:** Дубликатов.

*   **Пример:** Email пользователя, телефон, артикул товара.
*   **Почему в БД:** Даже если фронтенд скрывает дубли, два параллельных запроса `INSERT` могут создать два записи с одним email, если в коде нет блокировки (`FOR UPDATE`). `UNIQUE` гарантирует уникальность на уровне записи.
*   **Тонкость:** В PostgreSQL можно сделать **Partial Unique Index** (частичный индекс), который уникализирует значение только при определенных условиях (например, `UNIQUE (email) WHERE deleted_at IS NULL`). Это мощно для soft-delete.

#### 2. `FOREIGN KEY Constraints` (Целостность связей)
**Защита от:** «Сирот» (orphan records).

*   **Пример:** Заказ (`orders`) ссылается на пользователя (`users`). Если пользователь удален, заказ не должен остаться с несуществующим `user_id`.
*   **Почему в БД:** Если в коде при удалении пользователя забыть удалить/обнулить заказы, БД сработает как предохранитель: `ERROR: insert or update on table "orders" violates foreign key constraint`.
*   **Тонкость:** `ON DELETE CASCADE` vs `ON DELETE SET NULL`. Выбор стратегии зависит от бизнеса, но сам факт ограничения должен быть в БД.

#### 3. `CHECK Constraints` (Проверка допустимых значений)
**Защита от:** Невалидных значений, которые не описаны типом данных.

*   **Пример:**
    *   `age` должно быть `> 0 AND < 150`.
    *   `status` должен быть одним из значений `('pending', 'paid', 'shipped')` (хотя лучше использовать ENUM или Lookup table, но CHECK тоже работает).
    *   `price` должно быть `>= 0`.
*   **Почему в БД:** Это самые дешевые проверки. Они выполняются при записи. Если кто-то из скрипта напишет `UPDATE users SET age = -5`, БД отклонит запись.
*   **Ограничение:** В PostgreSQL нельзя ссылаться на другие таблицы внутри `CHECK` (нельзя проверить, что `parent_id` существует, если это не FK).

#### 4. `NOT NULL`
**Защита от:** Отсутствие критически важных данных.

*   **Пример:** `created_at`, `customer_id` в заказе.
*   **Почему в БД:** Гарантирует, что вы никогда не получите `null` в местах, где логически она невозможна. Это упрощает код в приложении (не нужно постоянно проверять `if ($order->getCustomer() !== null)`).

#### 5. `Default Values`
**Защита от:** Отсутствие дефолтных значений.

*   **Пример:** `status` по умолчанию `'pending'`, `is_active` по умолчанию `true`.
*   **Почему в БД:** Если в коде забыли указать эти поля, БД поставит безопасные значения по умолчанию, а не `null` или `0`, которые могут сломать логику.

---

### В каких случаях можно НЕ ставить ограничения в БД?

1.  **Вычисленные поля (Generated Columns):**
    *   Если поле вычисляется на лету (например, `total = price * quantity`), и его значение всегда валидно по определению, дополнительные `CHECK` могут быть избыточны, если логика вычисления не менялась.
    *   *Но лучше хранить как `GENERATED ALWAYS AS (...)`*, тогда БД сама гарантирует целостность.

2.  **Сложная бизнес-логика, которая не выражается простым SQL-выражением:**
    *   Пример: «Пользователь может создать не более 5 заказов в день».
    *   Сделать это в `CHECK` очень сложно (нужно подзапрос в ограничение). Чаще такую валидацию делают в коде + `UNIQUE` на временную таблицу или триггер.
    *   *Компромисс:* Используйте `UNIQUE` на `user_id + date` для простого случая, а сложные логики оставляйте в коде.

3.  **Мигрируемые данные с исторической «грязью»:**
    *   Иногда в старой БД есть дубликаты или `null` в `NOT NULL` полях. Тогда сначала нужно почистить данные, а потом наложить ограничения. Иначе миграция упадет.

---

### Как это выглядит на практике (Symfony + PostgreSQL)?

#### 1. Домен (PHP)
```php
class User
{
    private string $email;

    public function setEmail(string $email): void
    {
        // Бизнес-валидация: формат email, длина
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }
}
```

#### 2. Маппинг Doctrine
```php
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)] // UNIQUE в БД
    private string $email;

    #[ORM\Column(type: 'integer', options: ['default' => 0])] // NOT NULL + DEFAULT
    private int $loginAttempts;

    // ...
}
```

#### 3. Migrations (Dbal)
```php
public function up(Schema $schema): void
{
    // Добавление UNIQUE
    $this->addSql('CREATE UNIQUE INDEX user_email_unique ON users (email)');
    
    // Добавление CHECK
    $this->addSql('ALTER TABLE orders ADD CONSTRAINT price_positive CHECK (price > 0)');
    
    // Добавление FK
    $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
}
```

#### 4. Обработка ошибок в коде
Когда БД бросает ошибку (например, `UniqueConstraintViolationException`), Symfony/Doctrine превращает её в конкретное PHP-исключение.

```php
try {
    $em->persist($user);
    $em->flush();
} catch (UniqueConstraintViolationException $e) {
    // Логируем и показываем пользователю "Email уже зарегистрирован"
    throw new \DomainException('Email already in use');
}
```

> **Важно:** Всегда обрабатывайте `UniqueConstraintViolationException` в коде, потому что это **предсказуемая** ошибка бизнес-логики, а не баг системы.

---

### Итог: Стратегия «Defense in Depth»

1.  **Фронтенд/Forms:** Базовая проверка (UX).
2.  **API/Symfony Request Validation:** Проверка формата и обязательности (UX + Безопасность).
3.  **Доменные сущности (PHP):** Проверка бизнес-правил (Логика).
4.  **База данных (PostgreSQL):** Проверка структуры и целостности (Гарантия).

**Не убирайте ограничения из БД ради «чистоты» кода.** Они ваша страховка от багов, гонок состояний и внешнего вмешательства.

> ⏱ 30.9s · prompt 33831 tok · output 2140 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленных запросов в PostgreSQL — это не «попробуй-и-угадай», а системный процесс. Я подхожу к этому как инженер-детектив: сначала собираю улики (логи), потом анализирую алиби (планы запросов), и только потом выдвигаю вердикт (индексы, структура, конфиг).

Вот мой пошаговый процесс оптимизации.

---

### Шаг 1: Сбор данных (Что именно тормозит?)

Прежде чем менять код, нужно точно знать, какой запрос и как долго выполняется.

#### 1.1. Включение логирования медленных запросов
В `postgresql.conf` (или через `ALTER SYSTEM`) включаю логирование:
```conf
log_min_duration_statement = 1s;      # Логировать все запросы дольше 1 секунды
log_statement = 'mod';               # Логировать изменения данных (INSERT/UPDATE/DELETE)
log_destination = 'stderr';
```
*Альтернатива:* Если это приложение на PHP/Doctrine, я включаю SQL-логирование в Doctrine (см. предыдущий ответ про N+1), чтобы увидеть, **какой именно** DQL/SQL генерируется и сколько времени занимает каждый.

#### 1.2. Использование `EXPLAIN ANALYZE`
Это главный инструмент. Для подозрительного запроса я запускаю:
```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING) 
SELECT ...;
```
*   `ANALYZE`: Реально выполняет запрос (для получения времени).
*   `BUFFERS`: Показывает, сколько данных было прочитано из памяти (`shared buffers`) vs с диска (`temp files`, `read/write`). Это критично: чтение с диска в 1000 раз медленнее, чем из RAM.
*   `TIMING`: Показывает время на каждом этапе.

**Что я ищу в выводе:**
*   **`Seq Scan` (Sequential Scan):** Полное сканирование таблицы. Если таблица большая (>100k строк) и выборка маленькая — это плохо.
*   **`Sort Method: external merge` / `temp file:`:** Значит, PostgreSQL не поместил данные в оперативную память для сортировки и ушел на диск.
*   **`Nested Loop` vs `Hash Join`:** Если `Nested Loop` выполняется миллионы раз и вложенный цикл медленный — проблема.

---

### Шаг 2: Анализ типовых причин (Чек-лист)

Вот топ-5 причин, почему запрос медленный, и как я их решаю.

#### Причина 1: Отсутствие или неверный индекс (Самая частая)

**Симптом в EXPLAIN:**
```
Seq Scan on users  (cost=...)
Filter: (email = 'test@example.com')
```
БД сканирует всю таблицу, чтобы найти одного пользователя.

**Решение:**
Создать индекс по колонке фильтра:
```sql
CREATE INDEX idx_users_email ON users (email);
```

**Нюанс:** Если фильтр сложный, например `WHERE created_at > NOW() - '1 day' AND status = 'active'`, простой индекс по `created_at` может быть не оптимален. Лучше **Композитный индекс**:
```sql
CREATE INDEX idx_users_status_created ON users (status, created_at);
```
*Правило:* Сначала колонки с `EQ` (равенство), потом колонки с `Range` (диапазон).

---

#### Причина 2: «Жирные» SELECT-ы (SELECT *)

**Симптом:**
Запрос возвращает все колонки таблицы, включая большие текстовые поля (`jsonb`, `text`), которые не нужны в текущем контексте (например, список на главной).

**Решение:**
1.  В коде (Doctrine/SQL) указывать только нужные поля: `SELECT id, name, email FROM users`.
2.  Если таблица очень «широкая», рассмотреть **Vertical Partitioning** (разделение широкой таблицы на две: узкая основная и широкая детальная) или выделение частых полей в отдельную таблицу (Denormalization).

---

#### Причина 3: Неправильный тип JOIN-а или структура данных

**Симптом:**
Медленный `Nested Loop` Join, где вложенная таблица сканируется много раз.

**Решение:**
1.  Проверить, есть ли индексы на внешних ключах (`foreign key`). **FK всегда должен иметь индекс!**
2.  Проверить кардинальность. Если одна сторона JOIN-а содержит миллионы дублей, а другая — уникальные ID, постарайтесь переписать JOIN или использовать `Hash Join` (PostgreSQL обычно выбирает сам, но иногда помогает статистика).
3.  **Subqueries vs JOINs:** Иногда подзапрос (`IN (SELECT ...)`) работает медленнее, чем `JOIN`, и наоборот. Проверяйте `EXPLAIN`.

---

#### Причина 4: Устаревшие статистики (Stale Statistics)

**Симптом:**
План запроса выглядит абсурдно (например, использует `Hash Join` для 2 строк, или `Index Scan` для 90% таблицы).

**Причина:**
Оптимизатор PostgreSQL (Planner) принимает решения на основе статистики в `pg_stats`. Если данные сильно изменились, а статистика не обновилась, план может быть неоптимальным.

**Решение:**
```sql
ANALYZE table_name;
```
Это пересчитает статистики. В автоматизации это должно происходить регулярно (через `autovacuum` или cron).

---

#### Причина 5: Блокировки (Locks)

**Симптом:**
Запрос висит, не возвращая ошибку, а просто ожидает. В `pg_stat_activity` видно, что процесс ждет `tup_update` или `tuple`.

**Причина:**
Длинная транзакция (например, кто-то начал `BEGIN; SELECT ...; -- и забыл COMMIT`) держит блокировки на строках или таблицы.

**Диагностика:**
```sql
SELECT * FROM pg_locks 
JOIN pg_stat_activity ON pg_locks.pid = pg_stat_activity.pid
WHERE NOT pg_locks.granted;
```
**Решение:**
1.  Найти PID процесса-виновника и, если нужно, убить его: `SELECT pg_cancel_backend(pid);`.
2.  В коде: **Короткие транзакции**. Не делать HTTP-запросы или внешние API-вызовы внутри `BEGIN...COMMIT`. Только работа с БД.

---

### Шаг 3: Продвинутые методы оптимизации

Если базовые индексы не помогли:

#### 1. Partial Indexes (Частичные индексы)
Если 90% записей имеют `status = 'active'`, а мы чаще ищем `status = 'archived'`:
```sql
CREATE INDEX idx_users_archived ON users (created_at) WHERE status = 'archived';
```
Этот индекс будет очень маленьким и быстрым.

#### 2. Expression Indexes (Индексы по выражениям)
Если вы часто ищете по `LOWER(email)`:
```sql
-- Обычный индекс не поможет для LOWER()
CREATE INDEX idx_users_email_lower ON users (LOWER(email));
```

#### 3. Generated Columns (Вычисляемые поля)
Если вы часто фильтруете по году создания:
```sql
ALTER TABLE orders ADD COLUMN year SMALLINT GENERATED ALWAYS AS (EXTRACT(YEAR FROM created_at)) STORED;
CREATE INDEX idx_orders_year ON orders (year);
```

#### 4. Vacuum и Dead Tuple
Со временем таблица заполняется «мертвыми» записями (от UPDATE/DELETE). Они занимают место, но не используются.
*   **Диагностика:** `SELECT n_dead_tup FROM pg_stat_user_tables WHERE relname = 'orders';`
*   **Решение:** Убедиться, что `autovacuum` включен и настроен адекватно (`autovacuum_vacuum_scale_factor`). Для критичных таблиц можно настроить `autovacuum_vacuum_cost_limit` выше.

---

### Итоговый чек-лист для диагностики

1.  **Получить медленный SQL** из логов приложения.
2.  **Запустить `EXPLAIN ANALYZE BUFFERS`** в psql.
3.  **Искать Seq Scan** на больших таблицах → добавить индекс.
4.  **Искать Temp Files** в сортировке/агрегации → увеличить `work_mem` или оптимизировать запрос (меньше данных).
5.  **Проверить блокировки** через `pg_stat_activity` и `pg_locks`.
6.  **Проверить SELECT \*** → выбрать только нужные поля.
7.  **Проверить статистику** → `ANALYZE`.
8.  **Если ничего не помогло** → рассмотреть переопределение структуры БД (Partitioning, Denormalization) или изменение бизнес-логики (кэширование результатов).

**Главный совет:** Не гонитесь за «идеальным» планом запроса. Цель — **стабильное и предсказуемое** время ответа для 95-го перцентиля запросов. Иногда проще закэшировать результат запроса на 1 минуту в Redis, чем доводить SQL до совершенства.

> ⏱ 31.1s · prompt 36024 tok · output 2153 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD — это кластер сущностей и объектов значений, которые рассматриваются как **единица целостности данных**.

Ключевое слово здесь — **целостность**. Это означает, что любые изменения внутри агрегата должны быть атомарными: либо все изменения сохраняются, либо ни одно.

### 1. Как определяются границы агрегата?

Границы агрегата определяются не количеством таблиц в БД, а **бизнес-инвариантами** (бизнес-правилами, которые должны всегда выполняться).

Вот алгоритм определения границ:

1.  **Найдите инварианты:** Какие правила бизнеса нельзя нарушать ни при каких обстоятельствах?
    *   *Пример:* «Сумма всех позиций заказа должна равняться итоговой сумме заказа».
    *   *Пример:* «Пользователь не может иметь два аккаунта с одним email».
2.  **Определите корень (Aggregate Root):** Какая сущность владеет этими правилами и управляет доступом к внутренним сущностям?
    *   В примере с заказом — это `Order`.
    *   В примере с пользователем — это `User`.
3.  **Закройте всё, что защищают эти инварианты, внутри агрегата.**
    *   `Order` владеет `OrderItem`s. Если бы `OrderItem` был отдельным агрегатом, кто бы гарантировал, что сумма позиций не разойдется с итогом заказа при обновлении цены товара? Никто, если бы они были разнесены. Значит, `OrderItem` — часть агрегата `Order`.

**Принцип:** Агрегат должен быть **наименьшим**, который может гарантировать выполнение своих инвариантов.

---

### 2. Пример: Агрегат «Заказ» (Order)

Давайте возьмем типичную ситуацию интернет-магазина.

#### Структура:
*   **Aggregate Root:** `Order` (Заказ).
*   **Child Entities:** `OrderItem` (Позиция заказа).
*   **Value Objects:** `Money` (Сумма), `Address` (Адрес), `OrderId`.

#### Инварианты (Бизнес-правила), которые защищает этот агрегат:
1.  Заказ не может быть пустым (должен быть хотя бы один товар).
2.  Итоговая сумма заказа (`total_amount`) должна всегда равняться сумме всех позиций (`sum(item.price * item.qty)`).
3.  Нельзя добавить позицию, если товар уже есть в заказе (или нужно объединить количество), чтобы избежать дублей.
4.  Статус заказа должен меняться в определенном порядке (Draft -> Paid -> Shipped).

#### Код (упрощенно):

```php
class Order
{
    private OrderId $id;
    private Money $total;
    private Collection $items; // Ссылка на коллекцию OrderItem

    // Корень агрегата управляет изменениями
    public function addItem(Product $product, int $quantity): void
    {
        // Инвариант: Нельзя добавить 0 или отрицательное количество
        if ($quantity <= 0) {
            throw new \DomainException("Quantity must be positive");
        }

        // Инвариант: Проверяем, нет ли уже такого товара
        $existingItem = $this->items->first(
            fn(OrderItem $item) => $item->getProduct()->getId() === $product->getId()
        );

        if ($existingItem) {
            $existingItem->increaseQuantity($quantity);
        } else {
            $item = new OrderItem($product, $quantity);
            $this->items->add($item);
        }

        // Инвариант: Пересчитываем итоговую сумму ВСЕГДА
        $this->recalculateTotal();
    }

    private function recalculateTotal(): void
    {
        $sum = $this->items->reduce(
            fn($carry, OrderItem $item) => $carry + $item->getPrice(),
            Money::zero()
        );
        $this->total = $sum;
    }
}

class OrderItem
{
    private Product $product;
    private int $quantity;
    private Money $price; // Фиксируем цену на момент заказа

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        // Цена фиксируется на момент добавления, чтобы при изменении цены товара
        // старые заказы не меняли свою стоимость
        $this->price = $product->getPrice()->multiply($quantity);
    }
    
    // ... методы изменения количества
}
```

**Почему это один агрегат?**
Если `OrderItem` был бы отдельным агрегатом, и мы бы меняли количество в нем, мы бы не могли сразу пересчитать `Order.total`. А если мы изменим цену товара `Product` (который тоже может быть отдельным агрегатом), нам нужно было бы обновить `OrderItem`. Если бы эти агрегаты были независимы, между ними мог бы возникнуть период неконсистентности (Order говорит, что сумма 100$, а сумма позиций дает 95$). Внутри одного агрегата `Order` мы гарантируем, что эти данные всегда синхронизированы.

---

### 3. Типовая ошибка: «Слишком большой агрегат» (God Aggregate)

Самая частая ошибка новичков — попытка сделать **все** одним агрегатом, чтобы «было удобнее».

#### Пример ошибки:
Разработчик решает, что `Order` должен зависеть от `Inventory` (Остатки на складе) и `Payment` (Оплата), поэтому объединяет их в один огромный агрегат `SuperOrder`.

```php
class SuperOrder
{
    public function addItem() {
        // Логика заказа
    }
    
    public function checkStock() {
        // Запрос к складской БД / сервису
        // Проверка остатков
        // Уменьшение остатков
    }
    
    public function processPayment() {
        // Вызов платежного шлюза
        // Обновление статуса оплаты
    }
}
```

**Последствия:**
1.  **Проблема с блокировками:** Чтобы проверить остаток на складе, вам нужно заблокировать строку товара на складе. Если вы делаете это внутри агрегата `Order`, то при создании заказа вы держите блокировку на товаре долго (пока идет оплата). Это снижает производительность системы.
2.  **Нарушение инкапсуляции:** Агрегат `Order` знает о деталях реализации склада (как считать остатки) и платежей (как общаться со Stripe). Это нарушение SRP.
3.  **Трудности масштабирования:** Если складской сервис падает, падает весь заказ, даже если логика создания заказа корректна.

**Правильное решение (Границы агрегатов):**
*   **Агрегат `Order`:** Отвечает за структуру заказа и его стоимость.
*   **Агрегат `Inventory`:** Отвечает за остатки.
*   **Агрегат `Payment`:** Отвечает за транзакцию.

Как они взаимодействуют?
1.  Сервис приложения (`OrderService`) создает `Order` (Draft).
2.  Он вызывает `InventoryService::reserveStock(...)` (отдельный агрегат/сервис). Если остатки есть — ок, если нет — бросает исключение.
3.  Он вызывает `PaymentService::charge(...)` (отдельный агрегат/сервис).
4.  Только после успешного выполнения этих внешних шагов, `OrderService` меняет статус `Order` на `Confirmed` и сохраняет его.

**Вывод:** Агрегаты должны быть **маленькими** и **специализированными**. Они общаются друг с другом через сообщения (события) или вызовы сервисов, а не через прямые ссылки на объекты друг друга.

---

### 4. Типовая ошибка: «Слишком маленький агрегат» (Разделение инварианта)

Еще одна ошибка — разделение сущностей, которые должны защищать одно и то же правило.

#### Пример:
`User` (Пользователь) и `UserRole` (Роль) как отдельные агрегаты.

Инвариант: «У пользователя должна быть хотя бы одна роль».

Если `User` и `UserRole` — отдельные агрегаты:
1.  Создаем `User`.
2.  Сохраняем `User` в БД.
3.  Создаем `UserRole`.
4.  Сохраняем `UserRole` в БД.
5.  **Между шагами 2 и 4 в БД есть пользователь без ролей.** Если приложение упадет в этот момент, инвариант нарушен.

**Правильное решение:**
`UserRole` должен быть частью агрегата `User` (как вложенная сущность или значение), чтобы сохранение пользователя и его ролей происходило атомарно.

---

### Итог: Критерии хорошего агрегата

1.  **Маленький размер:** Обычно 1–3 сущности.
2.  **Один корень:** Четкий владелец (Root).
3.  **Защита инвариантов:** Гарантирует, что бизнес-правила не нарушаются при изменении.
4.  **Атомарность:** Сохранение происходит одним транзакционным блоком.
5.  **Общение через сообщения:** Агрегаты не должны напрямую ссылаться на объекты других агрегатов (используют ID или события).

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Когда два процесса одновременно изменяют один и тот же агрегат, в системе возникает **конфликт версий** (Race Condition). Если не предпринять меры, произойдет одно из двух:
1.  **Lost Update (Потеря обновления):** Один процесс записывает свои изменения поверх изменений другого. Бизнес-правило нарушено, данные испорчены.
2.  **Blocking (Блокировка):** Второй процесс ждет, пока первый завершит транзакцию. Это медленно, но безопасно (если первая транзакция успешна).

В DDD мы проектируем защиту от этого на трех уровнях: **Атомарность**, **Оптимистичная блокировка** и **Обработка конфликтов**.

---

### 1. Уровень БД: Атомарность и Транзакции

Первое и главное правило: **Изменение агрегата должно происходить в одной транзакции.**

*   Если процесс A меняет `Order.total`, а процесс B меняет `Order.status`, эти изменения должны быть либо видны обоим сразу, либо вообще не видны.
*   В PostgreSQL/MySQL это гарантируется транзакцией (`BEGIN ... COMMIT`).
*   **Проблема:** Транзакции по умолчанию используют изоляцию `READ COMMITTED`. Это значит, что процесс B может прочитать старое состояние `Order` до того, как процесс A завершит запись. Если B запишет свои изменения после A, он перезапишет данные A (Lost Update).

---

### 2. Решение: Оптимистичная блокировка (Optimistic Locking)

Это стандартный подход в DDD для защиты целостности агрегата. Мы предполагаем, что конфликты редки, и проверяем их **в момент сохранения**, а не в момент загрузки.

#### Механизм:
1.  Добавляем в таблицу агрегата поле `version` (или `revision`).
2.  При каждом `UPDATE` версия увеличивается на 1.
3.  Условие `UPDATE` включает проверку версии: `WHERE id = 1 AND version = 5`.
4.  Если другой процесс уже изменил строку (версия стала 6), условие не выполнится, и `UPDATE` затронет 0 строк.
5.  ORM (Doctrine) видит, что изменений 0, и бросает исключение `OptimisticLockException`.

#### Реализация в Doctrine:

**1. Маппинг версии:**
```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $totalAmount;

    // Ключевое поле
    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version;
}
```

**2. Логика в Приложении (Application Service):**
```php
class UpdateOrderService
{
    public function __construct(
        private OrderRepository $orderRepo,
        private EntityManagerInterface $em
    ) {}

    public function update(int $orderId, int $newTotal): void
    {
        // 1. Загружаем агрегат (читаем текущую версию, например, version = 5)
        $order = $this->orderRepo->findById($orderId);
        
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Применяем бизнес-логику
        $order->setTotalAmount($newTotal);

        // 3. Сохраняем
        $this->em->persist($order);
        
        try {
            // 4. Flush выполняет UPDATE ... WHERE version = 5
            $this->em->flush();
            
        } catch (OptimisticLockException $e) {
            // Если кто-то изменил Order раньше нас (version уже 6),
            // здесь упадет исключение.
            throw new \RuntimeException("Order was modified by another user. Please reload and try again.", previous: $e);
        }
    }
}
```

#### Почему это работает?
*   **Процесс A:** Читает `version=5`, меняет данные, пытается обновить с условием `version=5`. Успех. Версия становится `6`.
*   **Процесс B:** Читал `version=5` раньше. Меньяет данные, пытается обновить с условием `version=5`. Но в БД уже `version=6`. `UPDATE` находит 0 строк. Exception.

---

### 3. Что делать, если возник конфликт? (Стратегии)

Когда вы ловите `OptimisticLockException`, у вас есть три пути:

#### Стратегия A: «Попробуй снова» (Retry)
Если изменения идиоматичны (например, инкремент счетчика, добавление товара в корзину), можно автоматически повторить попытку.

```php
$maxRetries = 3;
$retryCount = 0;

while (true) {
    try {
        $this->em->clear(); // ВАЖНО: сбрасываем Unit of Work, чтобы перезагрузить свежие данные из БД
        $this->updateOrderService->update($orderId, $newTotal);
        break; // Успех
    } catch (OptimisticLockException $e) {
        if (++$retryCount > $maxRetries) {
            throw new \RuntimeException('Conflict too frequent', previous: $e);
        }
        // Можно добавить небольшой sleep() перед retry, чтобы развести процессы
    }
}
```
> **Важно:** `$this->em->clear()` критично, иначе Doctrine продолжит использовать старые (грязные) объекты из памяти.

#### Стратегия B: «Вернуть ошибку пользователю» (Manual Merge)
Если изменения сложные (например, редактирование текста статьи), автоматический merge опасен. Лучше сообщить пользователю: *«Статья была изменена другим пользователем. Пожалуйста, перезагрузите страницу и введите изменения снова»*.

#### Стратегия C: «Пересчитать состояние» (Re-apply)
Если логика позволяет, можно перезагрузить агрегат, применить свои изменения к новому состоянию и сохранить снова.
*Пример:* Корзина покупок.
1.  Пользователь A добавил товар X.
2.  Пользователь B добавил товар Y.
3.  Конфликт при сохранении.
4.  Загружаем свежий Order.
5.  Проверяем: есть ли товар X? Да. Есть ли товар Y? Нет.
6.  Добавляем товар Y к свежему агрегату.
7.  Сохраняем.

---

### 4. Альтернатива: Пессимистичная блокировка (Pessimistic Locking)

Если конфликты **очень частые** (например, изменение остатка на складе при высокой нагрузке), оптимистичная блокировка дает много ошибок и ретраев. Тогда используют **блокировку на уровне БД**.

#### Механизм:
При загрузке сущности мы сразу блокируем строку в БД.

```php
// В Doctrine
$order = $em->getRepository(Order::class)->find($orderId, [], [], 1, [], LockMode::OPTIMISTIC);
// Или для пессимистичной:
$order = $em->getRepository(Order::class)->find($orderId, [], [], 1, [], LockMode::PESSIMISTIC_WRITE);
```

В SQL это превращается в:
```sql
SELECT * FROM orders WHERE id = 1 FOR UPDATE;
```

**Trade-offs:**
| Критерий | Оптимистичная (Optimistic) | Пессимистичная (Pessimistic) |
| :--- | :--- | :--- |
| **Производительность при редких конфликтах** | Высокая (нет блокировок при чтении) | Средняя (блокировка при чтении) |
| **Производительность при частых конфликтах** | Низкая (много ретраев) | Высокая (очередь из процессов) |
| **Сложность кода** | Простая (catch exception) | Средняя (долгий удержание соединения) |
| **Риск deadlock'ов** | Низкий | Высокий (если блокировать несколько таблиц в разном порядке) |

**Когда использовать Pessimistic?**
*   Финансовые операции, где потеря данных недопустима, а производительность уступает надежности.
*   Когда логика изменения зависит от текущего состояния (например, `balance -= amount`), и вы не можете просто пересчитать ее в PHP, не читая свежие данные.

---

### 5. Архитектурные рекомендации

1.  **Минимизируйте время жизни транзакций:**
    *   Не делайте HTTP-вызовы к внешним сервисам (Stripe, Email) внутри транзакции, если это можно сделать после коммита.
    *   Транзакция должна быть короче, чем время, за которое второй процесс успевает загрузить те же данные.

2.  **Используйте Aggregate Root как точку входа:**
    *   Оба процесса должны модифицировать данные через методы корня агрегата (`$order->addItem(...)`), а не напрямую менять поля. Это позволяет корню проверять инварианты на основе **свежего** состояния.

3.  **Логируйте конфликты:**
    *   Если `OptimisticLockException` происходит часто, это сигнал, что ваши агрегаты слишком большие или логика конкуренции не учтена. Возможно, стоит разделить агрегаты или переключиться на событийную модель (CQRS), где чтение и запись отделены.

### Итог

*   **Что произойдет без защиты?** Потеря данных (Lost Update).
*   **Как проектировать?** Добавлять `#[ORM\Version]` в агрегат и ловить `OptimisticLockException` в слое приложения.
*   **Что делать при конфликте?** Либо повторять попытку (retry с clear'ом EM), либо сообщать пользователю об ошибке.
*   **Когда блокировать?** Если конфликты неизбежны и часты — используйте `LockMode::PESSIMISTIC_WRITE` на этапе загрузки.

> ⏱ 32.1s · prompt 38215 tok · output 2208 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это механизм, который позволяет сообщать другим частям системы о том, что в домене произошло значимое изменение, не создавая жестких зависимостей между ними.

Вот как я проектирую их работу, от генерации до обработки, и как они связаны с транзакциями.

---

### 1. Когда публикуем доменные события?

Событие генерируется, когда происходит **состояние** или **факт**, который имеет бизнес-ценность для других участников.

**Хорошие примеры:**
*   `OrderPlaced` (Заказ оформлен).
*   `PaymentProcessed` (Оплата проведена).
*   `UserRegistered` (Пользователь зарегистрирован).
*   `StockDepleted` (Склад закончился).

**Плохие примеры (антисоветы):**
*   `DatabaseSaved` (Это техническая деталь, не бизнес-факт).
*   `EntityCreated` (Слишком общее, не несет конкретного смысла).
*   `MethodCalled` (Событие должно быть именем существительным в прошедшем времени, описывающим факт).

**Кто генерирует?**
Обычно **Агрегат** или **Доменная Сущность**. Именно они знают, когда правило было нарушено или состояние изменено.

```php
class Order
{
    private array $domainEvents = [];

    public function pay(PaymentMethod $method): void
    {
        if ($this->status === OrderStatus::Paid) {
            throw new \DomainException("Order already paid");
        }

        $this->status = OrderStatus::Paid;
        
        // Генерируем событие
        $this->domainEvents[] = new OrderPaidEvent($this->getId(), $method);
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }
}
```

---

### 2. Как обрабатываем?

Есть два основных сценария: **синхронная** и **асинхронная** обработка.

#### А. Синхронная обработка (In-Process)
Используется, когда действие критически важно для завершения текущей операции (например, обновление индекса поиска должно произойти до того, как пользователь увидит результат).

В Symfony это реализуется через `EventDispatcher` и `Doctrine` Event Listeners (`onFlush`).

**Поток:**
1.  Вы вызываете `$em->flush()`.
2.  Doctrine срабатывает событие `onFlush`.
3.  Ваш Listener собирает все `domainEvents` из сущностей в Unit of Work.
4.  Listener диспатчит их в `EventDispatcher`.
5.  Слушатели (Handlers) выполняются **внутри той же транзакции**.
6.  Если Handler бросает исключение → транзакция откатывается.

```php
class DomainEventsSubscriber extends EventSubscriber
{
    public function onFlush(EventArgs $eventArgs): void
    {
        /** @var \Doctrine\Common\EventArgs $args */
        $em = $eventArgs->getArgument('eventManager');
        $uow = $em->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (method_exists($entity, 'getDomainEvents')) {
                $events = $entity->getDomainEvents();
                foreach ($events as $event) {
                    $this->eventDispatcher->dispatch($event); // Синхронный вызов
                }
                $entity->clearDomainEvents();
            }
        }
    }
}
```

#### Б. Асинхронная обработка (Out-of-Process)
Используется для задач, которые можно выполнить позже (отправка email, обновление аналитики).

**Поток:**
1.  Handler получает событие.
2.  Вместо выполнения логики сразу, он помещает событие в очередь (Redis/RabbitMQ) через `Symfony Messenger`.
3.  Фоновый воркер (`messenger:consume`) подбирает событие и выполняет логику.
4.  Если воркер упадет, событие не потеряется (retry strategy).

---

### 3. Связь с транзакциями БД (Критический момент)

Здесь скрывается самая большая ловушка: **Гарантия доставки событий.**

#### Проблема «Призрачных событий» (Ghost Events)
Если вы публикуете событие **до** сохранения данных в БД:
1.  Вы диспатчите `OrderPlaced`.
2.  Email-сервис шлет письмо.
3.  `$em->flush()` падает с ошибкой (например, violation constraint).
4.  Транзакция откатывается.
5.  **Итог:** Письмо ушло, но заказа в БД нет. Пользователь получит письмо о несуществующем заказе.

Если вы публикуете событие **после** сохранения данных в БД:
1.  `$em->flush()` успешен. Данные закоммичены.
2.  Вы диспатчите `OrderPlaced`.
3.  Приложение падает (краш PHP) **до** того, как обработчик обработает событие.
4.  **Итог:** Заказ есть, а письма нет.

#### Решение: «Transactional Outbox Pattern» (Шаблон транзакционного исходящего пульта)

Это золотой стандарт для обеспечения консистентности.

**Идея:** События хранятся в **таблице БД** в той же транзакции, что и сами данные.

1.  **Step 1 (Inside Transaction):**
    *   Вы меняете агрегат `Order`.
    *   Вы сохраняете его в БД (`INSERT INTO orders ...`).
    *   Вы вставляете событие в таблицу `outbox_events` (`INSERT INTO outbox_events ...`) **в той же транзакции**.
    *   Коммит транзакции.
    *   *Гарантия:* Если заказа нет, нет и события. Если событие есть, заказ точно есть.

2.  **Step 2 (Outside Transaction):**
    *   Отдельный процесс (Poller) регулярно читает таблицу `outbox_events`.
    *   Он берет необработанные события.
    *   Отправляет их в брокер (RabbitMQ/Kafka) или напрямую в обработчики.
    *   После успешной отправки удаляет запись из `outbox_events` (или меняет статус на `processed`).

**Преимущества:**
*   **ACID-совместимость:** Событие и данные пишутся атомарно.
*   **Надежность:** Даже если брокер падает, событие безопасно лежит в БД.

**Реализация в Symfony/Doctrine:**
Можно использовать пакет `messenger` с транспортом `doctrine` (он автоматически делает outbox pattern), или написать свой listener, который на этапе `onFlush` сохраняет события в таблицу `events`, а отдельная консольная задача `bin/console app:process-events` читает их и отправляет в MQ.

---

### 4. Какие проблемы решает этот подход?

1.  **Loose Coupling (Слабая связанность):** Модуль «Заказы» не знает о модуле «Email» и «Search Index». Он просто говорит: «Заказ создан». Другие модули подписываются на этот факт.
2.  **Расширяемость:** Чтобы добавить новую логику (например, «Отправить уведомление в Telegram»), вы просто пишете новый Handler. Вы не трогаете код создания заказа.
3.  **Асинхронность:** Тяжелые задачи (аналитика, поиск) не тормозят быстрый ответ пользователю.
4.  **Аудит:** Таблица событий (если используется Outbox) становится журналом аудита всех изменений в системе.

---

### 5. Какие проблемы этот подход создает?

1.  **Eventual Consistency (Последовательная консистентность):**
    *   Между созданием заказа и отправкой письма есть задержка (даже миллисекунды). Система временно находится в несогласованном состоянии.
    *   *Решение:* Это нормальная цена за масштабируемость. Клиент должен быть готов к тому, что данные могут обновиться с небольшой задержкой.

2.  **Сложность отладки:**
    *   Поток данных становится распределенным. Чтобы понять, почему не пришло письмо, нужно проверить:
        1.  Создался ли заказ?
        2.  Сохранилось ли событие в Outbox?
        3.  Вытащил ли Poller событие из Outbox?
        4.  Принял ли брокер событие?
        5.  Обработал ли Handler событие?
        6.  Не упало ли исключение в Handler?
    *   *Решение:* Худой трейсинг (Correlation ID / Trace ID), который пробрасывается через все слои (Order ID -> Event ID -> Email Log ID).

3.  **Дублирование событий (At Least Once):**
    *   Если воркер обрабатывает событие, но падает **до** того, как отметит его как обработанное, при рестарте он обработает его снова.
    *   *Решение:* **Идемпотентность** обработчиков. Логика внутри Handler должна быть такой, что выполнение ее два раза не даст двойного эффекта (например, `INSERT ... ON CONFLICT DO NOTHING` или проверка `WHERE status != 'paid'`).

4.  **Порядок событий:**
    *   Если вы отправите `OrderPlaced` и `OrderShipped`, а брокер доставит их в обратном порядке, это сломает логику.
    *   *Решение:* Использование Ordered Partitions в Kafka/RabbitMQ или гарантия, что критичные события обрабатываются синхронно внутри одной транзакции/очереди.

### Итог

*   **Генерация:** В доменных сущностях, как результат изменения состояния.
*   **Хранение:** В таблице Outbox для гарантии атомарности с данными (Transactional Outbox Pattern).
*   **Доставка:** Через брокер сообщений (RabbitMQ/Kafka/Redis) для асинхронности.
*   **Обработка:** Идемпотентные хендлеры, которые реагируют на факт.

Это превращает вашу систему из «монолитной связки классов» в «сетку независимых, но согласованных сервисов», что является целью современной микросервисной или модульной монолитной архитектуры.

> ⏱ 36.0s · prompt 42819 tok · output 2450 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальные вопросы DDD. Если здесь есть путаница, вся архитектура страдает. Давайте разберем разницу, алгоритм принятия решений и роль Ubiquitous Language.

---

### 1. Entity vs Value Object: Ключевые отличия

Главная разница не в «сложности» кода, а в **идентичности** и **жизненном цикле**.

| Характеристика | **Entity (Сущность)** | **Value Object (Объект Значения)** |
| :--- | :--- | :--- |
| **Идентичность** | Определена **ID** (например, `User #123`). | Определена **значением** (например, `Email "a@b.com"`). |
| **Равенство** | Два объекта равны, только если их ID одинаковы. | Два объекта равны, если **все** их свойства одинаковы. |
| **Изменяемость** | **Mutatable** (изменяемый). Меняет состояние со временем. | **Immutable** (неизменяемый). Нельзя поменять, можно только заменить. |
| **Жизненный цикл** | Длинный. Создан, изменен, удален. | Короткий. Создан, использован, выброшен (GC). |
| **Зависимость** | Могут ссылаться на другие Entity/VO. | Ссылаются только на другие VO или примитивы. |
| **Хранение** | Требует первичного ключа (Primary Key) в БД. | Часто хранится как колонка (строка, число) или JSON. |

#### Пример на пальцах:

*   **Entity:** `User`. Даже если он поменял имя, email и пароль, он остается *тем же самым* пользователем, потому что его ID (или email как уникальный логин) не меняется.
*   **Value Object:** `Email`. Если у вас есть два объекта `Email` с адресом `john@doe.com`, они **эквивалентны**. Для системы не важно, в каком порядке они были созданы или кто их создал. Если вы меняете email, вы не «модифицируете» старый объект, а создаете новый и заменяете ссылку в сущности `User`.

```php
// Entity
class User {
    private int $id; // Идентификатор
    private string $name;
    private Email $email; // Ссылка на VO

    public function changeName(string $newName): void {
        $this->name = $newName; // Изменение состояния
    }
    
    public function changeEmail(Email $newEmail): void {
        $this->email = $newEmail; // Замена ссылки (VO неизменяем)
    }
}

// Value Object
final class Email {
    public function __construct(
        private readonly string $address
    ) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }
    
    public function __toString(): string {
        return $this->address;
    }
}
```

---

### 2. Как решать: Entity или Value Object?

Используйте этот алгоритм (вопросы-фильтры):

1.  **Важна ли история изменений?**
    *   *Да:* Это **Entity**. (Например, `Order`, `Product`, `User`). Нам важно, *кто* этот объект, а не только *что* он содержит.
    *   *Нет:* Вероятно, это **Value Object**. (Например, `Money`, `PhoneNumber`, `Address`).

2.  **Можно ли заменить один экземпляр другим без потери смысла, если их значения равны?**
    *   *Да:* **Value Object**. (Мне все равно, какая именно «копилка денег» хранит мои 100 рублей, важна сама сумма).
    *   *Нет:* **Entity**. (Если я заменю «Заказ #1» на «Заказ #2» с той же суммой, это будет другой бизнес-процесс, другая отправка, другая история).

3.  **Нужен ли этот объект для аудита/истории?**
    *   *Да:* **Entity**.
    *   *Нет:* **Value Object**.

4.  **Несет ли объект в себе бизнес-правила, которые зависят от его состояния во времени?**
    *   *Да:* **Entity**.
    *   *Нет (просто набор данных с валидацией):* **Value Object**.

#### Спорные случаи:
*   **`Address`:** Часто делают **Value Object**, если адрес не имеет собственного ID и истории (просто «куда доставить»). Но если адрес имеет ID и может редактироваться с историей изменений («старый адрес», «новый адрес»), это **Entity**.
*   **`Money`:** Почти всегда **Value Object**. Мы оперируем суммой, а не «конкретным куском купюры».

---

### 3. Ubiquitous Language (Единый Язык)

**Ubiquitous Language** — это общее лексиконы, на котором говорят и разработчики, и бизнес-заказчики. Это «контракт» между доменной моделью и реальным миром.

#### Почему это важно?
Если бизнес говорит «клиент оформил сделку», а в коде у вас `createTransaction()`, `processOrder()` и `finalizePurchase()` — вы теряете смысл. Единый язык устраняет этот разрыв.

#### Как это влияет на код?

1.  **Имена сущностей:**
    *   ❌ Плохо: `User`, `Client`, `Person`, `Actor`.
    *   ✅ Хорошо: `Customer` (если бизнес так называет), `AccountHolder`.
    *   *Правило:* Имя класса = Термин бизнеса.

2.  **Имена методов:**
    *   ❌ Плохо: `setPrice()`, `updateValue()`.
    *   ✅ Хорошо: `applyDiscount()`, `recalculateTotal()`, `charge()`.
    *   *Правило:* Метод описывает *бизнес-действие*, а не техническую операцию чтения/записи.

3.  **Имена аргументов и полей:**
    *   ❌ Плохо: `amt`, `val`, `data`.
    *   ✅ Хорошо: `grossAmount`, `netAmount`, `shippingAddress`.

4.  **Исключения:**
    *   ❌ Плохо: `Exception 5001: Validation failed`.
    *   ✅ Хорошо: `InsufficientFundsException: Balance is less than requested amount`.

#### Пример построения Ubiquitous Language:

1.  **Этюд моделей (Event Storming):** Собираетесь с бизнес-аналитиком, рисуете пост-иты.
    *   Бизнес: «Клиент кладет товар в корзину».
    *   Разработчик: «Ок, метод `addToCart`».
    *   Бизнес: «Но если товара нет на складе, мы должны сказать ему об этом».
    *   Разработчик: «Хорошо, исключение `OutOfStockException`».
    *   Бизнес: «Мы называем это "Ожидание поставки"».
    *   Разработчик: «Окей, переименуем исключение в `StockBackorderException`, а метод добавления проверит статус товара».

2.  **Результат:**
    *   В коде появится класс `Cart`, метод `addProduct()`, проверка `product.isAvailable()` и событие `StockBackordered`.
    *   Когда тестировщик видит ошибку `StockBackorderedException`, он сразу понимает, что это не баг, а ожидаемое поведение бизнеса.

### Итог

1.  **Entity** — это «кто/что» с уникальным ID и историей.
2.  **Value Object** — это «какой» с уникальным набором значений и неизменяемостью.
3.  **Ubiquitous Language** — это ваш главный инструмент общения с бизнесом. Код должен читаться как документация к бизнес-процессу. Если разработчик должен переводить с «программного» на «бизнесовый» язык, чтобы понять, что происходит, — модель спроектирована плохо.

**Совет:** Если вы не уверены, Entity это или VO, начните с **Value Object**. Его проще изменить в Entity, чем распылить Entity по всему коду, если окажется, что ей не нужна история.

> ⏱ 28.9s · prompt 45330 tok · output 1927 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Структура приложения по принципам чистой архитектуры (Clean Architecture) в PHP — это попытка изолировать **бизнес-логику** от всех внешних зависимостей (фреймворков, БД, HTTP).

Цель: **Домен не знает, что такое `Symfony`, `Doctrine` или `HTTP`.**

---

### 1. Структура слоёв (Изнутри наружу)

Мы движемся от самого ядра (Домен) к периферии (Интерфейсы). Зависимости всегда указывают **к центру**.

```text
┌─────────────────────────────────────────────────────────────┐
│  Interface / Presentation (API, CLI, Console)               │  ← Внешний мир
│  - Symfony Controllers                                       │
│  - Request/Response DTOs                                     │
└───────────────────────────┬─────────────────────────────────┘
                            │ (Зависит от)
┌───────────────────────────▼─────────────────────────────────┐
│  Application / Use Cases (Сервисы приложения)               │  ← Оркестрация
│  - Command Handlers                                            │
│  - Query Handlers                                              │
│  - Service Interfaces (порты)                                 │
└───────────────────────────┬─────────────────────────────────┘
                            │ (Зависит от)
┌───────────────────────────▼─────────────────────────────────┐
│  Domain (Ядро)                                               │  ← Бизнес-правила
│  - Entities, Value Objects                                     │
│  - Domain Services                                             │
│  - Domain Events                                               │
│  - Repository Interfaces (порты)                              │
└─────────────────────────────────────────────────────────────┘
                            ▲
                            │ (Реализует)
┌───────────────────────────┴─────────────────────────────────┐
│  Infrastructure (Инфраструктура)                             │  ← Техническая реализация
│  - Doctrine Entities/Repositories (ORM)                      │
│  - Mailers, Payment Gateways, External APIs                  │
│  - Config, Logging, Cache                                    │
└─────────────────────────────────────────────────────────────┘
```

> **Важно:** В классической чистой архитектуре (Uncle Bob) слой `Infrastructure` не зависит от `Application`. Но в современных подходах (особенно в Symfony) часто делают **Dependency Injection** так, что `Application` знает только интерфейсы, а `Infrastructure` их реализует.

---

### 2. Что лежит в каждом слое?

#### А. Domain Layer (Домен)
*Чистый PHP. Никаких `use Symfony\...`, `use Doctrine\...`.*

1.  **Aggregate Roots & Entities:** `Order`, `Customer`, `Product`.
2.  **Value Objects:** `Money`, `Email`, `DateInterval`.
3.  **Domain Services:** Логика, которая не принадлежит одной сущности, но важна для бизнеса. Например, `PriceCalculator` (если расчет цены зависит от множества факторов, не хранящихся в одном заказе).
4.  **Repository Interfaces:** Контракты для получения/сохранения агрегатов.
    ```php
    interface OrderRepository
    {
        public function save(Order $order): void;
        public function findById(OrderId $id): ?Order;
    }
    ```
5.  **Domain Events:** `OrderPlaced`, `PaymentFailed`.

**Правило:** Если вы удалили из проекта Symfony и Doctrine, слой Domain должен остаться нетронутым и рабочим.

#### Б. Application Layer (Приложение / Use Cases)
*Оркестрация. Связывает Домен с внешним миром.*

1.  **Handlers (Command Handlers):** Принимают команду (данные от UI), вызывают доменные методы, сохраняют результат.
    ```php
    class CreateOrderHandler
    {
        public function __construct(
            private OrderRepository $orderRepo, // Интерфейс из Domain
            private EventDispatcher $events     // Интерфейс из Symfony/PSR
        ) {}

        public function handle(CreateOrderCommand $cmd): void
        {
            // 1. Создаем доменный объект
            $order = Order::create($cmd->customerId, $cmd->items);
            
            // 2. Сохраняем через репозиторий
            $this->orderRepo->save($order);
            
            // 3. Публикация событий (если нужно)
            // ...
        }
    }
    ```
2.  **DTO (Data Transfer Objects):** Объекты для передачи данных между UI и Приложением. `CreateOrderRequest`, `OrderResponse`.
3.  **Service Interfaces (Ports):** Если нужно обратиться во внешний сервис (например, платежный шлюз), здесь описывается интерфейс `PaymentGatewayInterface`.

**Правило:** Слой Application не знает, как работает БД или HTTP. Он знает только *что* нужно сделать (интерфейсы).

#### В. Infrastructure Layer (Инфраструктура)
*Технические детали. Зависит от Domain и Application.*

1.  **Doctrine Implementation:**
    *   `DoctrineOrderRepository implements OrderRepository`.
    *   `OrderEntity` (Doctrine Entity, которая маппится на таблицу). *Примечание: Часто лучше разделять `Domain Order` и `Doctrine OrderEntity`, чтобы не тащить аннотации в домен.*
2.  **External API Clients:** Реализация `PaymentGatewayInterface` (например, `StripeGateway`).
3.  **Mailers:** Реализация отправки писем.
4.  **Config:** YAML/PHP конфиги.

#### Г. Interface / Presentation Layer (Интерфейс)
*Вход в систему.*

1.  **Symfony Controllers:** Принимают HTTP-запрос, валидируют входные данные, создают DTO/Command, вызывают Handler из Application Layer.
    ```php
    class OrderController extends AbstractController
    {
        public function __construct(private CreateOrderHandler $handler) {}

        #[Route('/orders', methods: ['POST'])]
        public function create(CreateOrderRequest $request): Response
        {
            // 1. Валидация и маппинг в Command
            $command = $request->toCommand();
            
            // 2. Вызов бизнес-логики
            $this->handler->handle($command);
            
            // 3. Ответ
            return new Response(null, 201);
        }
    }
    ```
2.  **Forms/Requests:** Валидация входных данных (Symfony Form или Attribute Validation).
3.  **View/Templates:** Twig (если есть UI).
4.  **Console Commands:** Для CLI-скриптов.

---

### 3. Куда попадают конкретные компоненты?

| Компонент | Слой | Почему? |
| :--- | :--- | :--- |
| **Symfony Controller** | **Interface** | Это входной порт. Он превращает HTTP в Command. |
| **Doctrine Entity** | **Infrastructure** | Это техническая модель для БД. |
| **Doctrine Repository** | **Infrastructure** | Реализация интерфейса `Repository` из Domain. |
| **Handler (Command)** | **Application** | Оркестрация бизнес-логики. |
| **Value Object (Money)** | **Domain** | Чистая бизнес-концепция. |
| **Request DTO** | **Interface** или **Application** | Обычно в Interface, если он сильно связан с HTTP/Form. |

> **Примечание про Doctrine Entity vs Domain Entity:**
> Многие разработчики смешивают их (делают одну сущность и ставят аннотации). Это удобно, но нарушает чистоту.
> **Чистый подход:**
> 1. `Domain/Order` (чистый класс, логика).
> 2. `Infrastructure/Doctrine/OrderEntity` (класс с `#[ORM\Entity]`).
> 3. `Infrastructure/Doctrine/OrderRepository` (переводит `Domain/Order` <-> `OrderEntity`).
>
> Это сложнее, но позволяет менять БД или ORM, не трогая бизнес-логику.

---

### 4. Правило зависимостей (Dependency Rule)

Зависимости (импорты, ссылки) могут быть направлены только **к центру**.

*   `Interface` зависит от `Application`.
*   `Application` зависит от `Domain`.
*   `Infrastructure` зависит от `Domain` (реализует его интерфейсы) и может зависеть от `Application` (для доступа к сервисам), но **никогда не наоборот**.
*   `Domain` зависит от **ничего** (кроме стандартной библиотеки PHP).

**Запрещено:**
*   `Domain` импортировать `Symfony\Component\HttpFoundation\Request`.
*   `Domain` импортировать `Doctrine\ORM\EntityManagerInterface`.
*   `Application` импортировать конкретный `StripeClient` (должен использовать интерфейс `PaymentGateway`).

---

### 5. Как следить, что границы не нарушены?

Человеческий фактор часто приводит к тому, что кто-то "для удобства" вставляет `use Doctrine\ORM\EntityManagerInterface` в доменную сущность. Чтобы этого избежать:

#### 1. Архитектурные тесты (Architectural Tests)
Напишите PHPUnit-тесты, которые сканируют файлы и проверяют правила импорта.

```php
public function testDomainLayerShouldNotDependOnInfrastructure(): void
{
    $domainFiles = glob('src/Domain/**/*.php', GLOB_RECURSIVE);
    
    foreach ($domainFiles as $file) {
        $content = file_get_contents($file);
        
        // Простая проверка на наличие запрещенных пространств имён
        $this->assertStringNotContainsString('Doctrine\ORM', $content, "Domain layer should not import Doctrine: $file");
        $this->assertStringNotContainsString('Symfony\Component', $content, "Domain layer should not import Symfony: $file");
        $this->assertStringNotContainsString('App\Infrastructure', $content, "Domain layer should not import Infrastructure: $file");
    }
}
```
Это самый надежный способ.

#### 2. Структура папок
```
src/
├── Domain/           # Чистый PHP
│   ├── Order/
│   └── Money/
├── Application/      # Сервисы, Handler'ы
│   ├── Order/
│   └── Money/
├── Infrastructure/   # Doctrine, External APIs
│   ├── Doctrine/
│   ├── Stripe/
│   └── Mailer/
└── Interface/        # Controllers, Requests
    ├── Http/
    └── Console/
```
Ясная структура подсказывает разработчику, куда класть код.

#### 3. Code Sniffer / Linter
Настройте `PHP_CodeSniffer` или `Rector`, чтобы они предупреждали о неправильных импортах.

#### 4. Code Review
В ревью проверяйте: "Зачем этот `EntityManagerInterface` в классе `Order`? Почему бы не передать его в `OrderRepository`?"

### Итог

Чистая архитектура в PHP — это не самоцель, а способ **сохранить бизнес-логику независимой от технических деталей**.
1.  **Домен** отвечает за *что* и *почему*.
2.  **Приложение** отвечает за *как* (последовательность шагов).
3.  **Инфраструктура** отвечает за *инструменты* (БД, HTTP).
4.  **Интерфейс** отвечает за *канал* (Web, CLI).

Следование этому разделению делает систему тестируемой (домен тестируется без БД), масштабируемой (можно заменить Stripe на PayPal, не трогая домен) и понятной (новый разработчик видит четкие границы).

> ⏱ 39.6s · prompt 47325 tok · output 2673 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony-приложения с PostgreSQL я выбираю **модифицированную пирамиду тестов** с акцентом на **интеграционные тесты**.

Почему?
*   **Unit-тесты (Юниты)** в PHP часто оказываются «висящими в воздухе», потому что основная логика в Symfony завязана на сервисы и БД. Чистые юниты хороши только для Domain Layer (сущности, VO, простые сервисы).
*   **Интеграционные тесты** — это сердце тестирования в Symfony. Они проверяют реальное взаимодействие: контроллер → сервис → БД. Это ловит 80% багов (N+1, ошибки маппинга, транзакции).
*   **E2E (End-to-End)** — самые медленные и хрупкие. Их нужно минимизировать, оставляя только для критических сценариев (например, «Пользователь смог зарегистрироваться и создать заказ»).

---

### 1. Пирамида: Что и сколько пишем?

| Тип теста | Доля | Что тестирует | Инструменты |
| :--- | :--- | :--- | :--- |
| **Unit** | ~40% | Чистая бизнес-логика (Domain). | PHPUnit |
| **Integration** | ~50% | Взаимодействие сервисов, БД, API. | Symfony KernelTestCase + DatabaseTruncator / Migrations |
| **E2E** | ~10% | Критические пути пользователя (Cypress/Codeception). | Cypress / Playwright |

---

### 2. Что мокаем, а что нет?

Здесь главное правило: **Не мокайте то, что является частью системы, которую вы тестируете.**

#### ✅ НЕ МОКАЕМ (Используем реальные объекты):
1.  **База данных (PostgreSQL):**
    *   В интеграционных тестах мы хотим увидеть реальный SQL, реальные индексы, реальные ограничения (Unique, FK).
    *   *Почему:* Моки БД скрывают проблемы с транзакциями, lazy loading и схемами.
    *   *Как:* Используем отдельную тестовую БД (см. ниже).
2.  **Doctrine EntityManager:**
    *   Он должен реально сохранять и читать данные, чтобы проверить маппинг.
3.  **Доменные сущности:**
    *   Они должны быть реальными, чтобы проверить инварианты.

#### ✅ МОКАЕМ (Имитируем внешние системы):
1.  **Внешние API:**
    *   Платежные шлюзы (Stripe, PayPal).
    *   Почтовые сервисы (SendGrid, Mailgun).
    *   Почему: Они медленные, платные и нестабильные.
    *   *Как:* Реализуйте `PaymentGatewayInterface` и в тестах внедряйте `MockPaymentGateway`, который возвращает предсказуемый ответ.
2.  **Время (Time):**
    *   Если логика зависит от текущего времени («активная акция до 20:00»), используйте Dependency Injection для сервиса `Clock`, который в тестах возвращает фиксированное время.
3.  **Сложные расчеты (редко):**
    *   Если вы тестируете Service A, который вызывает Service B, а Service B содержит сложную математику, которую вы уже покрыли своими юнит-тестами, можно смозовать B, чтобы изолировать логику A. Но лучше тестировать их вместе (интеграционно), если они тесно связаны.

---

### 3. Стратегия интеграционных тестов (Symfony)

В Symfony есть два основных подхода к работе с БД в тестах: `DatabaseTruncator` и `Migrations`. Я рекомендую **Migrations** или **Fixtures** для детерминированного состояния.

#### Пример интеграционного теста:

```php
namespace App\Tests\Integration;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Factory; // Библиотека для создания фикстур

class OrderCreationTest extends WebTestCase
{
    private KernelBrowser $client;
    private OrderRepository $orderRepo;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->orderRepo = static::getContainer()->get(OrderRepository::class);
        
        // Важный момент: Сброс состояния БД перед каждым тестом
        // Zenstruck Foundry автоматически чистит таблицы, связанные с фабриками
    }

    public function test_user_can_create_order(): void
    {
        // 1. Подготовка данных (Fixtures)
        $user = UserFactory::createOne(['email' => 'test@example.com']);
        $product = ProductFactory::createOne(['name' => 'Laptop', 'price' => 1000]);

        // 2. Имитация действия пользователя
        $this->client->request('POST', '/api/orders', [
            'json' => [
                'product_id' => $product->getId(),
                'quantity' => 2
            ]
        ]);
        
        // 3. Проверка HTTP ответа
        $this->assertResponseStatusCodeSame(201);

        // 4. Проверка состояния БД (Интеграционная часть!)
        $order = $this->orderRepo->findOneBy(['customer' => $user]);
        $this->assertNotNull($order);
        $this->assertEquals(2000, $order->getTotalAmount()); // 1000 * 2
    }
}
```

**Почему это лучше моков?**
Мы видим, что заказ реально сохранился, что сумма рассчитана правильно и что нет ошибок SQL при сохранении.

---

### 4. Как сделать тесты быстрыми и стабильными?

Это самое важное. Медленные тесты — это невыполненные тесты.

#### A. Изоляция тестовой БД
*   Используйте отдельную БД для тестов (`test_database`), отличную от `dev` и `prod`.
*   **Параллелизм:** Настройте PHPUnit так, чтобы он запускал тесты параллельно (через `paratest`). Каждый параллельный процесс должен использовать свою базу (или свой schema/namespace в PostgreSQL).
    ```xml
    <!-- phpunit.xml -->
    <php>
        <env name="DATABASE_URL" value="postgresql://user:pass@localhost:5432/test_1"/>
    </php>
    ```

#### B. Транзакции vs Truncation
1.  **Транзакции (Быстро, но сложно):**
    *   Symfony `WebTestCase` может обернуть каждый тест в транзакцию и откатить её.
    *   *Плюс:* Очень быстро, БД не очищается.
    *   *Минус:* Сложно тестировать миграции, `COMMIT` внутри транзакции (например, при сохранении событий), и некоторые функции PostgreSQL (как `LISTEN/NOTIFY`) могут работать некорректно.
2.  **Truncation / Migrations (Медленнее, но реалистично):**
    *   Использовать библиотеку **Zenstruck Foundry** или **Symfony Fixtures**.
    *   Перед каждым тестом чистить таблицы (`TRUNCATE`) и закладывать только нужные данные.
    *   Это дает реальную картину работы с БД (без скрытых связей в памяти).

*Моя рекомендация:* Для 90% случаев используйте **Truncation + Fixtures**. Это стабильнее.

#### C. Моки внешних сервисов
*   Никогда не ходите в реальный Stripe или SendGrid в тестах.
*   Создайте `MockHttpClient` или конкретные моки интерфейсов.
*   Используйте `Mockery` или `PHPUnit Mocks` для инъекций в конструкторы сервисов.

#### D. Асинхронность (Messenger)
*   Если вы используете Symfony Messenger для асинхронных задач (отправка email), в тестах **отключите транспорт** или используйте транспорт `in-memory` / `doctrine` (outbox).
*   В тесте вы проверяете, что событие попало в очередь (Outbox), а не обязательно, что письмо ушло (это можно проверить в другом тесте хендлера).

#### E. Детерминированность
*   **Время:** Используйте `Clock` сервис.
*   **Random:** Если в коде есть `random_int()`, вынесите его за интерфейс или используйте seed.
*   **Порядок:** Убедитесь, что тесты не зависят от порядка выполнения (каждый тест сам создает свои данные).

---

### 5. Итоговый чек-лист для проекта

1.  **Домен:**
    *   Юнит-тесты для всех Entity/VO/Domain Services.
    *   Тестируем инварианты (`Order::addItem` бросает исключение, если qty > 100).
    *   **Нет** моков БД.
2.  **Приложение/Сервисы:**
    *   Интеграционные тесты.
    *   Реальная БД (чистится перед тестом).
    *   Моки внешних API.
    *   Проверяем логику оркестрации.
3.  **API (Контроллеры):**
    *   Интеграционные тесты (`WebTestCase`).
    *   Проверяем статус коды, JSON-структуру, валидацию.
    *   Проверяем, что данные реально записались в БД.
4.  **E2E:**
    *   Только для критических сценариев (Login, Checkout).
    *   Cypress/Playwright.
    *   Работают против запущенного приложения (не против кода).

**Совет:** Начните с покрытии **домена** юнит-тестами. Затем напишите **3-5 ключевых интеграционных тестов** для основных сценариев (создание, чтение, обновление). Они станут «якорями», которые не дадут сломаться архитектуре. Остальное добавляйте по мере написания новой функциональности.

> ⏱ 35.6s · prompt 50058 tok · output 2351 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Ситуация, когда два процесса одновременно пытаются изменить одну и ту же запись, называется **конкуренцией (concurrency)**. Если не предусмотреть защиту, возникнет одна из двух классических проблем:

1.  **Lost Update (Потеря обновления):** Процесс A и B читают значение `100`. A записывает `100 + 50 = 150`. B записывает `100 + 20 = 120`. Итог: `120`, хотя ожидалось `150`. Изменение A потерялось.
2.  **Dirty Read / Non-repeated Read:** Процесс B видит «середину» транзакции A (например, часть данных изменена, часть нет), если изоляция слабая.

Ниже я опишу три основных способа защиты, их реализацию в PHP/Symfony/PostgreSQL и сравню их.

---

### Способ 1: Оптимистичная блокировка (Optimistic Locking)
**«Предположим, что конфликтов нет. Если они случились — сообщим об ошибке».**

Это наиболее распространенный подход в DDD и веб-приложениях с высокой частотой чтения, но низкой частотой конфликтов записи.

#### Механизм:
1.  В таблице есть версияльная колонка `version` (int).
2.  При чтении вы получаете `value=100, version=1`.
3.  При обновлении вы отправляете `UPDATE ... SET value=150, version=2 WHERE id=1 AND version=1`.
4.  Если процесс B успел обновить запись раньше, его версия стала `2`. Ваше условие `WHERE version=1` не находит строк (affected rows = 0).
5.  Вы получаете ошибку, понимаете, что данные устарели, и (опционально) повторяете попытку.

#### Реализация в Doctrine:
```php
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column]
    private int $balance;

    #[ORM\Version] // Магический атрибут Doctrine
    #[ORM\Column]
    private int $version;

    // ... методы
}
```

При вызове `$em->flush()` Doctrine автоматически генерирует SQL с проверкой версии. Если конфликт возникает, бросается `OptimisticLockException`.

#### Стратегия обработки:
```php
try {
    $account->withdraw(50);
    $em->flush();
} catch (OptimisticLockException $e) {
    // Вариант 1: Бросить ошибку пользователю "Конфликт, попробуйте снова"
    // Вариант 2: Автоматический Retry (см. ниже)
}
```

#### Trade-offs:
*   ✅ **Высокая производительность:** Нет блокировок при чтении. Много пользователей могут читать одновременно.
*   ✅ **Простота:** Не нужно управлять блокировками.
*   ❌ **Штраф при частых конфликтах:** Если конфликты частые, вы получите много ошибок и ретраев (повторных попыток), что дорого.
*   ❌ **Сложность логики Retry:** Нужно сбрасывать `EntityManager` (`$em->clear()`) перед повторной попыткой, чтобы загрузить свежие данные.

---

### Способ 2: Пессимистичная блокировка (Pessimistic Locking)
**«Предположим, что конфликт будет. Забронируй запись, пока работаешь».**

Используется, когда конфликты **очень частые**, и цена ошибки (потеря данных или долгий retry) выше, чем цена ожидания.

#### Механизм:
При загрузке сущности вы просите БД поставить блокировку на строку. Пока ваша транзакция не завершится (`COMMIT`/`ROLLBACK`), другие процессы не смогут изменить эту строку.

#### Реализация в Doctrine:
```php
use Doctrine\ORM\LockMode;

// 1. Пессимистичная чтение (FOR UPDATE)
$account = $em->getRepository(Account::class)
              ->find($id, [], [], null, [], LockMode::PESSIMISTIC_READ);

// 2. Изменение
$account->withdraw(50);

// 3. Сохранение
$em->flush(); // Внутри этой транзакции блокировка держится
```

SQL под капотом: `SELECT * FROM accounts WHERE id = 1 FOR UPDATE;`

#### Trade-offs:
*   ✅ **Гарантия отсутствия Lost Update:** Второй процесс будет ждать первого.
*   ✅ **Предсказуемость:** Нет неожиданных исключений, просто ожидание.
*   ❌ **Замедление при высокой нагрузке:** Если много пользователей пишут в одну таблицу, они будут выстраиваться в очередь.
*   ❌ **Риск Deadlock (Взаимной блокировки):** Если процесс A блокирует строку 1 и ждет строку 2, а процесс B блокирует строку 2 и ждет строку 1 — оба зависнут. БД убьет один из них (PostgreSQL выбросит `Deadlock detected`).
*   ❌ **Удержание ресурсов:** Блокировка держится все время выполнения транзакции. Если транзакция долгая (медленный код), система деградирует.

---

### Способ 3: Атомарные обновления в SQL (Atomic Updates)
**«Не читай и не пиши по отдельности. Сделай изменение атомарным на стороне БД».**

Идеально подходит для простых операций вроде «инкремент счетчика», «деcrement баланса», где логика изменения не зависит от сложного состояния других полей.

#### Механизм:
Вместо того чтобы загружать объект в PHP, менять его и сохранять, вы отправляете один SQL-запрос, который сам изменяет данные.

#### Реализация (через QueryBuilder или Native Query):
```php
// Вместо:
// $account = $repo->find($id);
// $account->addMoney(50);
// $em->flush();

// Используем атомарное обновление:
$em->createQuery('
    UPDATE App\Entity\Account a 
    SET a.balance = a.balance + :amount 
    WHERE a.id = :id
')->setParameter('amount', 50)
  ->setParameter('id', $id)
->execute();
```

#### Trade-offs:
*   ✅ **Максимальная производительность:** Один запрос, нет чтения в PHP, нет round-trip для чтения.
*   ✅ **Атомарность:** PostgreSQL гарантированно выполнит изменение атомарно.
*   ❌ **Потеря бизнес-логики:** Вы не можете проверить инварианты домена (например, «баланс не должен уходить в минус» или «лимит на списания»).
*   ❌ **Сложная логика:** Если изменение зависит от нескольких условий или требует сложной математики, SQL становится грязным и трудночитаемым.

---

### Сравнение подходов

| Критерий | Оптимистичная (Optimistic) | Пессимистичная (Pessimistic) | Атомарные SQL |
| :--- | :--- | :--- | :--- |
| **Когда конфликты редки** | ✅ Идеально | ⚠️ Избыточно | ✅ Хорошо |
| **Когда конфликты часты** | ❌ Много ретраев | ✅ Идеально | ✅ Хорошо |
| **Производительность (Read)** | ⚡ Очень высокая | 🐢 Замедляет при записи | ⚡ Высокая |
| **Производительность (Write)** | ⚡ Высокая (если нет конфликтов) | 🐢 Низкая (очередь) | ⚡ Высокая |
| **Сложность кода** | Средняя (нужен retry-логик) | Низкая | Средняя (нужен SQL) |
| **Гибкость бизнес-логики** | Высокая (логика в PHP) | Высокая (логика в PHP) | Низкая (логика в SQL) |
| **Риск Deadlock** | Низкий | Высокий | Низкий (если одна таблица) |

---

### Мой алгоритм выбора

1.  **Это простой инкремент/декремент?**
    *   *Да:* Используйте **Атомарные SQL** (`UPDATE ... SET x = x + 1`). Это самый быстрый и безопасный способ для счетчиков, балансов (при простых правилах), просмотров.

2.  **Это сложная бизнес-логика (много проверок, валидаций)?**
    *   *Да:* Используйте **Оптимистичную блокировку** (`#[ORM\Version]`).
    *   *Обоснование:* Большинство пользователей читают. Конфликты записи редки. Вы получаете высокую пропускную способность. Если конфликт случился — попросите пользователя «обновить данные и повторить» или сделайте 1-2 автоматических ретрая.

3.  **Конфликты неизбежны (например, аукцион, горячие товары, фин. операции)?**
    *   *Да:* Используйте **Пессимистичную блокировку**.
    *   *Обоснование:* Лучше заставить пользователя ждать 100мс, чем показать ему ошибку «Конфликт данных» или потерять его ставку.
    *   *Внимание:* Держите транзакцию короткой! Не делайте внутри `sleep()` или HTTP-запросов.

4.  **Гибрид (Продвинутый уровень):**
    *   Используйте **Оптимистичную** блокировку для загрузки и изменений в памяти.
    *   Перед `flush()` выполните **Атомарную** SQL-проверку критического условия.
    *   *Пример:* При списании денег:
        1.  Загрузить объект (Optimistic).
        2.  Проверить `if ($balance < $amount) throw ...` (Бизнес-логика).
        3.  `UPDATE accounts SET balance = balance - :amt WHERE id = :id AND balance >= :amt`.
        4.  Если `affected rows == 0` — значит, кто-то уменьшил баланс раньше нас. Бросить исключение.
        Это сочетает гибкость PHP с атомарностью SQL.

### Итог

*   **По умолчанию:** Оптимистичная блокировка (Version Column).
*   **Для счетчиков:** Атомарные SQL.
*   **Для критичных горячих узлов:** Пессимистичная блокировка (осторожно!).

В Symfony/Doctrine самым «бесшовным» способом является **Оптимистичная блокировка**, так как она требует минимум кода (один атрибут `#[ORM\Version]`) и прозрачно обрабатывает конфликты через стандартный поток исключений.

> ⏱ 37.5s · prompt 52457 tok · output 2473 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой Git-процесс выстроен вокруг двух главных целей: **предсказуемости** (каждый знает, что происходит в коде) и **быстроты выпуска** (от идеи до продакшена минимальное время, но без потери качества).

В основе лежит гибридная модель: **Trunk-Based Development** (разработка от общего ствола) + **Feature Flags** (флаги фич) + **Conventional Commits**.

Отказ от классического GitFlow (с долгоживущими `dev`, `release`, `hotfix`) объясняется простому факту: в современных CI/CD пайплайнах ветка `master`/`main` всегда должна быть деплоябельна. Длинные ветки создают «мусор» (merge conflicts), который накапливается неделями.

---

### 1. Ветвление (Branching Strategy)

Я придерживаюсь **Trunk-Based Development**.

*   **`main` (или `master`):** Единственная долгоживущая ветка. Она всегда в валидном состоянии и готова к деплою на продакшен.
*   **`feature/short-name`:** Короткоживущие ветки (1–3 дня жизни). Создается под каждую задачу (баг, фича, рефакторинг).
*   **`hotfix/issue-123`:** Ветки для срочных исправлений на продакшене. Живут пару часов.
*   **Нет ветки `develop` или `staging`.** Стейджинг — это среда (environment), а не ветка. Мы деплоим `main` на стейджинг автоматически при каждом пуше.

**Почему так?**
1.  **Минимизация конфликтов:** Чем короче живет ветка, тем меньше код в ней меняется. Меньше шансов, что он пересечется с чужими изменениями.
2.  **Быстрый фидбек:** Вы видите результат своей работы в общем окружении (стеджинге) сразу, а не через неделю, когда будете мерджиться в `dev`.
3.  **Упрощение истории:** История `main` читается как лог изменений продукта, а не как археологические раскопки веток.

*Исключение:* Если фича очень большая (более 1–2 недель) и опасна для прода, она может существовать в отдельной ветке, но в этом случае обязательно используются **Feature Flags**, чтобы скрыть её от пользователей до готовности.

---

### 2. Формат коммитов (Commit Messages)

Использую стандарт **Conventional Commits**.

**Формат:** `<type>(<scope>): <description>`

*   **Type:**
    *   `feat`: Новая функциональность.
    *   `fix`: Исправление бага.
    *   `refactor`: Рефакторинг (изменение кода без изменения логики).
    *   `docs`: Изменение документации.
    *   `test`: Добавление/изменение тестов.
    *   `chore`: Рутинные задачи (обновление зависимостей, конфиги).
*   **Scope (необязательно):** Модуль или файл (например, `auth`, `ui`, `db`).
*   **Description:** Краткое описание в императиве (что делает изменение), первая буква прописная, без точки в конце.

**Примеры:**
*   ✅ `feat(auth): add email verification step`
*   ✅ `fix(order): handle null payment response`
*   ✅ `refactor: extract order validation to separate service`
*   ❌ `update code`
*   ❌ `fix bug`

**Почему так?**
1.  **Автоматическое генерирование CHANGELOG:** Инструменты (например, `conventional-changelog`) могут автоматически собирать эти коммиты и создавать файл `CHANGELOG.md` или описание релиза.
2.  **Читаемость истории:** Команда может быстро понять, что произошло, просматривая `git log`.
3.  **Семантическое версионирование:** Можно автоматически определять, нужно ли бить минор (если есть `feat`), патч (если есть `fix`) или просто патч (chore/docs).

---

### 3. Pull Request (Merge Request)

PR — это не просто «отправь код на проверку». Это **документ** и **контракт**.

#### Правила создания PR:
1.  **Один PR = Одна логическая единица работы.** Не смешивайте `feat: add login` с `fix: typo in css`.
2.  **Заголовок:** Соответствует формату Conventional Commits (например, `feat: add user profile page`).
3.  **Описание (Body):**
    *   **What:** Что делает эта фича?
    *   **Why:** Зачем это нужно? (Ссылка на Trello/Jira/Linear задачу).
    *   **How:** Краткое описание технического решения.
    *   **Checklist:**
        *   [ ] Написаны тесты.
        *   [ ] Пройден локальный запуск.
        *   [ ] Нет логированных секретов.
        *   [ ] Добавлены миграции БД (если есть).
4.  **Демо:** Скриншоты (для UI) или запись экрана (для сложных фич), или ссылка на деплой на стейджинге.

**Почему так?**
*   Ревьюер тратит меньше времени на чтение кода, если понимает контекст.
*   Описание помогает в будущем, когда кто-то будет искать, *почему* была принята такая архитектура.

---

### 4. Code Review (Ревью)

Ревью — это **передача знаний** и **проверка качества**, а не просто поиск опечаток.

#### Правила ревью:
1.  **Время ответа:** Максимум 24–48 часов. Долгий ревью убивает мотивацию автора.
2.  **Фокус на бизнес-логике и архитектуре:**
    *   *Ищем:* Нарушение принципов SOLID, плохое назовение переменных, скрытые зависимости, ошибки в бизнес-правелах.
    *   *Не ищем (руками):* Отступы, точки с запятой (это работа линтера/форматтера).
3.  **Тон коммуникации:**
    *   Используйте `Must` (обязательно исправить), `Should` (хотелось бы, но можно обсудить), `Could` (предложение альтернативы).
    *   ❌ «Почему ты написал это так глупо?»
    *   ✅ «Здесь есть риск, что NPE будет сгенерирован, если `user` будет null. Предлагаю добавить проверку.»
4.  **Проверка тестов:**
    *   Код, который не покрыт тестами, считается «сырым».
    *   Ревьюер должен спросить: «А как мы тестируем этот новый путь?».

**Почему так?**
*   Линтеры (PHP-CS-Fixer, Rector) делают форматирование автоматически. Ревьюер не должен тратить время на косметические вещи.
*   Тесты — это доказательство того, что код работает. Без них ревью бессмысленно.

---

### 5. CI/CD Пайплайн (Автоматизация)

Никаких ручных проверок. Всё автоматизируется.

#### Этапы пайплайна (GitHub Actions / GitLab CI):
1.  **Lint & Format Check:**
    *   Запуск `phpcs`, `php-cs-fixer --dry-run`.
    *   Если формат отличается — падение пайплайна. Автор должен запустить `php-cs-fixer` локально.
2.  **Static Analysis:**
    *   Запуск `Psalm` или `PHPStan` (уровень 8 или выше).
    *   Проверяет типы, потенциальные логические ошибки.
3.  **Unit & Integration Tests:**
    *   Запуск `phpunit`.
    *   Должно быть 100% покрытие критических модулей (или хотя бы высокий порог, заданный командой).
4.  **Build & Security Scan:**
    *   Сборка образа (если Docker).
    *   Проверка зависимостей на уязвимости (`composer audit` или Snyk).
5.  **Deploy to Staging:**
    *   Автоматический деплой на стейджинг при успешном прохождении предыдущих шагов.
6.  **E2E Tests (опционально):**
    *   Прогон базовых сценариев (Cypress/Codeception) на стейджинге.

**Почему так?**
*   Если пайплайн падает — код не попадает в `main`. Это гарантирует, что `main` всегда зеленый.
*   Статический анализ ловит 80% ошибок типов, которые иначе упали бы в рантайме.

---

### 6. Релизы (Releases)

В Trunk-Based Development релиз — это не «сборка всех фич за месяц», а **регулярный деплой**.

#### Стратегия:
1.  **Continuous Deployment (CD):**
    *   Каждый успешный деплой в `main` автоматически публикуется в `staging`.
    *   После ручного smoke-теста (5–10 минут) на стейджинге, нажимаем кнопку «Deploy to Production».
    *   Или полностью автоматически, если покрытие тестов высокое и система стабильна.
2.  **Semantic Versioning:**
    *   Версии генерируются автоматически на основе Conventional Commits.
    *   `feat` -> Minor (1.1.0)
    *   `fix` -> Patch (1.0.1)
    *   `feat!` (breaking change) -> Major (2.0.0)
3.  **Hotfix:**
    *   Если на прода упало что-то критичное:
        1.  Создаем ветку `hotfix/urgent-fix` от `main`.
        2.  Фиксим баг.
        3.  Пушим в `main` (через PR с приоритетом).
        4.  Деплоим на прода.
        5.  Убираем ветку.
    *   Никаких отдельных `release/1.0.2` веток, которые потом нужно мерджить обратно.

**Почему так?**
*   **Частые, маленькие релизы** безопаснее, чем редкие, большие. Меньше риск сломать много всего сразу.
*   **Отсутствие веток релизов** упрощает поддержку. Если нужно применить патч к старой версии, мы просто делаем cherry-pick нужных коммитов в новую ветку (или используем tag-based deployment), но чаще всего мы деплоим `main`.

---

### Итоговая схема процесса

1.  **Задача** -> Создан Ticket в Trello/Jira.
2.  **Код** -> Создана ветка `feature/ticket-123`.
3.  **Коммиты** -> `feat: implement login logic`.
4.  **Локальные проверки** -> `composer run test`, `composer run lint`.
5.  **PR** -> Открыт, описание заполнено.
6.  **CI** -> Зеленый пайплайн (Lint, Static Analysis, Tests).
7.  **Review** -> Утверждено 1–2 ревьюерами.
8.  **Merge** -> Squash Merge в `main`. (Один PR = один коммит в истории).
9.  **Deploy** -> Автоматический деплой на Staging -> Проверка -> Деплой на Prod.

Этот процесс требует дисциплины от команды, но в итоге дает **прозрачность**, **быстроту** и **надежность**.

> ⏱ 41.8s · prompt 54991 tok · output 2747 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Для меня хороший код-ревью — это не «поисковая операция на ошибки», а **диалог инженера с инженером**, цель которого — улучшить качество системы и передать знания.

Если автор получил только список красных крестиков, он воспринимает ревью как критику личности. Если он получает контекст и альтернативы, он воспринимает его как помощь в росте.

Вот мой подход, разбитый на механику, приоритеты и коммуникацию.

---

### 1. Маркировка замечаний (Taxonomy of Comments)

Чтобы автор сразу понимал, что можно обсудить, а что нужно исправить обязательно, я использую три уровня приоритета. Это можно делать через эмодзи или специальные теги в начале комментария.

#### 🔴 **Blocking (Blocker) — «Не сметь мержить»**
Критические замечания, которые ломают логику, безопасность или производительность. Без их исправления PR не должен быть закоммичен.
*   *Примеры:* Ошибка логики, утечка памяти, SQL-инъекция, нарушение инвариантов домена, отсутствие тестов на новый критичный путь.
*   *Формат:* `🔴 **Blocker**: Здесь `user` может быть `null`, что вызовет NPE. Добавьте проверку или сделайте тип `?User`.`

#### 🟡 **Major (Should fix) — «Надо подумать/исправить»**
Замечания о чистоте кода, производительности или читаемости. Я хочу, чтобы они были исправлены, но если время горит, можем оставить их на потом (Tech Debt).
*   *Примеры:* Нарушение SRP, неоптимальный алгоритм (O(n^2) вместо O(n)), плохое назовение, дублирование кода.
*   *Формат:* `🟡 **Major**: Этот метод делает три разные вещи (валидация, сохранение, email). Предлагаю вынести отправку письма в отдельный сервис.`

#### 🟢 **Minor (Nit / Suggestion) — «Хорошо бы»**
Косметические моменты, личные предпочтения, альтернативные стили. Автор **не обязан** их исправлять. Это приглашение к обсуждению.
*   *Примеры:* Отступы, использование `match` вместо `switch` (если оба работают), предпочтение одного библиотечного метода над другим.
*   *Формат:* `🟢 **Nit**: Можно использовать `array_filter` здесь, чтобы было короче, но текущий вариант тоже читается хорошо.`

#### 💡 **Question / FYI — «Вопрос / К сведению»**
Вопросы, чтобы понять логику, или информация, которая полезна для будущего.
*   *Пример:* `💡 **Q**: Почему здесь используется `array` вместо `Collection`? Я думал, что это доменная сущность.`
*   *Пример:* `💡 **FYI**: В будущем, если мы добавим поддержку мультитенаси, этот код нужно будет переписать.`

---

### 2. На что смотрю в первую очередь (Priority Order)

Когда я открываю PR, я читаю код в следующем порядке важности:

1.  **Безопасность (Security):**
    *   Есть ли инжекты?
    *   Корректно ли обрабатываются права доступа (Voters, Roles)?
    *   Не логируются ли чувствительные данные (пароли, токены)?
    *   *Почему первое:* Исправление безопасности после релиза стоит дорого.

2.  **Правильность бизнес-логики (Correctness):**
    *   Соответствует ли код заявленной задаче в тикете?
    *   Нарушены ли инварианты домена?
    *   Обработаны ли edge cases (пустые массивы, `null`, negative numbers)?
    *   *Почему второе:* Ломаный код — это баг.

3.  **Тесты (Testability & Coverage):**
    *   Есть ли тесты на новую логику?
    *   Тестируется ли поведение, а не реализация (mocking vs integration)?
    *   *Почему третье:* Тесты — это контракт. Если их нет, код считается «сырым».

4.  **Читаемость и Архитектура (Readability & Design):**
    *   Названия классов/методов отражают их суть?
    *   Нарушает ли код принципы SOLID (особенно SRP)?
    *   Сложно ли понять, что делает метод, без чтения документации?
    *   *Почему четвертое:* Код читают чаще, чем пишут.

5.  **Производительность (Performance):**
    *   Есть ли N+1 запросы?
    *   Есть ли утечки памяти в циклах?
    *   *Почему пятое:* Часто можно оптимизировать позже, если логика верна. Но если N+1 на главной странице — это 🔴 Blocker.

---

### 3. Как формулирую замечания

Я избегаю фраз в стиле «Ты написал плохо». Вместо этого я использую модель **«Я + Факт + Предложение»**.

❌ **Плохо (Лично):**
> «Ты опять забыл сделать strict types. Ты же знаешь, что мы так делаем уже полгода.»

✅ **Хорошо (По существу):**
> `🟡 **Major**: В этом файле отсутствует `declare(strict_types=1);`. Я помню, что мы обсуждали это на стандартах. Давай добавим его, чтобы избежать неявных приведений типов.»

❌ **Плохо (Слишком сухо):**
> «Сделай лучше.»

✅ **Хорошо (С контекстом):**
> `🟡 **Major**: Здесь сложно понять, зачем мы вызываем `calculateTax()`. Предлагаю переименовать в `calculateVAT()` и добавить комментарий, почему именно этот расчет. Или вынести в отдельный сервис `TaxCalculator`?»

---

### 4. Что делать при разногласии с автором

Разногласие в ревью — это нормально и даже полезно. Это значит, что вы оба думаете.

#### Шаг 1: Вернитесь к источнику истины
Спросите: **«Какой бизнес-требование или технический стандарт мы пытаемся удовлетворить?»**
*   Если я говорю: «Нужно сделать через Event», а автор: «Но у нас нет очереди!» — тогда я не прав, и нужно использовать синхронный вызов.
*   Если я говорю: «Нарушает SRP», а автор: «Но это только одна функция!» — тогда спор о размере. Здесь можно уступить, если функция маленькая и когезивная.

#### Шаг 2: Обсудите Trade-offs
Предложите автору альтернативное решение и спросите его мнение о недостатках вашего и его вариантов.
> «Я предпочитаю вариант A, потому что он проще для тестирования. Ты предложил вариант B, который быстрее, но сложнее в поддержке. Какой риск мы готовы принять?»

#### Шаг 3: Правило «Disagree and Commit» (или наоборот)
Если мы спорим более 15 минут и не сходимся:
1.  Если вопрос **технический и спорный** (например, стиль кода) — автор может выбрать свое решение, если он готов нести за него ответственность. (В конце концов, его имя в коммите).
2.  Если вопрос **архитектурный или критичный** — зовем третьего человека (Tech Lead, Senior Dev) для арбитража.

#### Шаг 4: Не спорьте в комментах, если спор стал личным
Переведите обсуждение в голосовой чат, видеозвонок или личную встречу. В текстовой переписке легко впасть в недопонимание.

---

### 5. Что я НЕ смотрю (или делаю автоматически)

1.  **Форматирование:** Отступы, пробелы, кавычки. Это работа `PHP-CS-Fixer`. Если пайплайн зеленый — значит, формат ок.
2.  **Назовение переменных (в малых скоупах):** `$i`, `$j` в цикле — нормально. `$arr` в глобальном контексте — плохо.
3.  **Выбор библиотеки:** Если я предпочитаю `Laravel`, а автор `Symfony`, но оба решения работают — я не буду мешать. Это решение архитектора.

### Итог

Хорошее ревью — это **команда, которая делает код лучше вместе**.
*   Используйте эмодзи/теги для приоритетов.
*   Фокус на бизнес-логике и тестах, а не на отступах.
*   Формулируйте замечания как предложения, а не приказы.
*   При споре возвращайтесь к требованиям и trade-offs.

Ваша цель как ревьюера: **Сделать так, чтобы в следующем PR автор не допустил эту ошибку сам.**

> ⏱ 32.2s · prompt 57786 tok · output 2063 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Чтобы превратить жалобу бизнеса в инженерную задачу, которую можно выполнить «слепым» разработчиком, нужно убрать субъективность («жалуются», «завалена») и добавить объективные критерии, контекст и границы.

Вот как я сформулирую эту задачу (например, в Jira/Linear) и объясню, почему каждая часть важна.

---

### 📝 Задача: «Оптимизация поиска исторических заказов для пользователя»

**1. Контекст и Проблема (Why)**
> **Ситуация:** Пользователи тратят в среднем 3-4 минуты на поиск конкретного заказа за последние 3 месяца. Поддержка получает ~50 обращений в неделю по теме «Не нахожу заказ в личном кабинете».
>
> **Гипотеза:** Текущая реализация списков заказов не позволяет быстро отфильтровать данные по дате или статусу, либо список обрезается пагинацией слишком агрессивно (например, показывает только последние 5 заказов, а старые нужно листать вручную).

**2. Цель (Goal)**
> Уменьшить время поиска конкретного заказа до 30 секунд.
> Обеспечить возможность быстрого перехода к конкретному заказу из списка «Мои заказы».

**3. Детальное ТЗ (What)**

**A. Функциональные требования:**
1.  **Расширение фильтров в ЛК:**
    *   Добавить фильтр по **Периоду** (Дата от — Дата до).
    *   Добавить фильтр по **Статусу** (Все, Ожидают оплаты, В обработке, Доставлены, Отменены).
    *   Добавить строку **Поиска** по номеру заказа (если он известен пользователю).
2.  **Сортировка:**
    *   По умолчанию: `Date DESC` (новые сверху).
    *   Возможность переключить на `Date ASC` (старые сверху) — *опционально, если UX одобрит*.
3.  **Пагинация/Бесконечный скролл:**
    *   Убедиться, что при прокрутке вниз (или нажатии «Показать еще») загружаются старые заказы без потери данных.
    *   **Критично:** Запрос к БД должен быть оптимизирован (см. Tech Notes), чтобы не тормозить страницу при большом количестве заказов (N+1 проблема).

**B. Нетехнические требования:**
1.  **Производительность:**
    *   Время ответа API `/api/orders` при наличии 1000+ заказов у пользователя должно быть < 500ms (P95).
    *   Добавить индекс в БД, если его нет, по `(user_id, created_at DESC)`.
2.  **UI/UX:**
    *   Визуально выделить фильтр и поиск (mockup приложен, см. ссылку).
    *   Пустое состояние: если заказов за выбранный период нет, показывать сообщение «Заказы за выбранный период не найдены» с кнопкой «Сбросить фильтры».

**C. Tech Notes (Для бэкенда):**
1.  **БД:**
    *   Проверить план запроса текущей выдачи списка.
    *   Если используется `JOIN` со связанными сущностями (товары, адреса), убедиться, что используется `JOIN FETCH` или оптимизированный `DQL`, чтобы избежать N+1.
    *   Добавить/проверить индекс: `CREATE INDEX idx_orders_user_date ON orders (user_id, created_at DESC);`
2.  **API:**
    *   Принять параметры: `start_date`, `end_date`, `status`, `order_number`, `page`, `limit`.
    *   Валидация дат: `start_date` не может быть позже `end_date`.
3.  **Фронтенд:**
    *   Использовать debouncing для поля поиска (300мс).
    *   Не перезагружать весь список при каждом изменении фильтра, если возможно (или делать это через оптимистичный UI).

**4. Критерии приемки (Definition of Done)**
*   [ ] Пользователь может ввести диапазон дат и увидеть только заказы за этот период.
*   [ ] Пользователь может найти заказ по номеру.
*   [ ] Статусы заказов корректно фильтруются.
*   [ ] API отвечает < 500ms на аккаунте с 1000 заказами (проверено в DevTools/Profiler).
*   [ ] Написаны интеграционные тесты на новые параметры запроса.
*   [ ] Нет ошибок в консоли браузера при фильтрации.

**5. Риски и вопросы**
*   *Риск:* Если пользователь имеет >10,000 заказов, даже оптимизированный запрос может быть медленным.
    *   *Решение:* Ввести ограничение на выборку (например, последние 5 лет) или вынести старые заказы в архивное хранилище (отложить на следующий спринт).

---

### 🔍 Почему я включил именно эти пункты?

1.  **Количественные метрики (Цель):**
    *   *Без этого:* «Улучшить поиск».
    *   *С этим:* «Уменьшить время до 30 сек», «< 500ms».
    *   *Зачем:* Это позволяет мерить успех. Если после релиза время поиска стало 29 секунд, задача выполнена. Если осталась та же — нет.

2.  **Конкретные фильтры (Что делать):**
    *   *Без этого:* Разработчик может добавить только «Поиск по названию товара», что не решит проблему поиска *заказа*.
    *   *С этим:* Четко прописано: Дата, Статус, Номер. Это покрывает 90% сценариев, когда человек помнит *когда* покупал или *что* купил.

3.  **Tech Notes (Как делать):**
    *   *Без этого:* Разработчик может написать простой SQL, который упадет под нагрузкой (N+1).
    *   *С этим:* Я даю подсказку про индексы и оптимизацию. Это экономит время ревью и баг-фиксов после релиза.

4.  **Критерии приемки (Как проверить):**
    *   *Без этого:* QA скажет: «Поиск работает». Но как он знает, что он быстрый?
    *   *С этим:* Чек-лист. QA может объективно закрыть тикет только если все пункты выполнены.

5.  **Пустое состояние и ошибки (Edge cases):**
    *   *Без этого:* Пользователь выберет даты, в которых нет заказов, и увидит пустую страницу без объяснений. Он снова напишет в поддержку.
    *   *С этим:* Проведен пользовательский сценарий «Ничего не найдено».

6.  **Границы ответственности (Риски):**
    *   *Без этого:* Разработчик может начать писать сложный микросервис архивации заказов.
    *   *С этим:* Я зафиксировал, что архивация — это отдельная задача (отложить). Это ограничивает scope (объем) работы.

### Итог

Сырая проблема: *«Жалуются, что не могут найти».*
Инженерная задача: *«Добавить фильтры по дате/статусу/номеру, оптимизировать SQL-запрос до <500ms, покрыть тестами, обеспечить UX-обратную связь при пустом результате».*

Такая задача не оставляет места для интерпретации. Разработчик знает **что** делать, **как** проверять результат и **где** находятся границы его ответственности.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Эти вопросы помогают убрать неопределенность, определить реальный объем работы и избежать ситуации, когда мы решаем не ту проблему. Их можно разделить на 4 категории: контекст, данные, UI/UX и технические ограничения.

### 1. Вопросы о контексте и метриках (Чтобы решить правильную проблему)

1.  **«Как именно пользователи ищут заказы сейчас?»**
    *   *Зачем:* Понять, что именно ломает процесс. Они не могут набрать дату? Не видят старый статус? Или просто не знают, куда нажать?
    *   *Ожидаемый ответ:* «Они прокручивают список вниз до упора», «Ищут по названию товара, но поиск не работает», «Не понимают, что отмененный заказ исчез из основного списка».
2.  **«Какова доля обращений в поддержку по каждой из этих причин?»**
    *   *Зачем:* Расставить приоритеты. Если 80% жалуются на «не нахожу по дате», а 20% на «сломался поиск» — мы сначала чиним фильтры по дате, а не пишем сложный поисковый движок.
3.  **«Сколько заказов в среднем у активного пользователя?»**
    *   *Зачем:* Оценить сложность UI и нагрузки на БД. Если у 99% пользователей 10 заказов, им нужен красивый список. Если у топ-5% пользователей 5000 заказов — им нужен мощный фильтр и пагинация, иначе страница будет тормозить.

### 2. Вопросы о данных и домене (Что мы храним и как)

4.  **«Что считается "номером заказа" для пользователя?»**
    *   *Зачем:* В БД может быть внутренний `id` (123456), а в интерфейсе — `order_ref` (ORD-2023-XYZ). Нужно знать, по какому полю искать, если мы добавляем поиск.
5.  **«Как долго хранятся "старые" заказы?»**
    *   *Зачем:* Есть ли у нас политика хранения данных (например, архивация через 2 года)? Если да, то поиск должен искать и в основном списке, и в архиве (или хотя бы показывать кнопку «Искать в архиве»).
6.  **«Какие статусы заказа интересны пользователю для фильтрации?»**
    *   *Зачем:* В БД может быть 20 технических статусов (`pending_payment`, `payment_verified`, `picking_started`), а пользователь понимает только 3: «Ожидает», «Доставляется», «Завершен». Нужно маппинг между техническими и бизнес-статусами.

### 3. Вопросы о UX/UI (Что именно делаем на экране)

7.  **«Есть ли уже дизайн-макеты (mockups) этой фичи?»**
    *   *Зачем:* Если нет — это задача на «дискуссии с дизайнером», которая может занять 2–3 дня. Если есть — мы сразу видим, какие поля нужны в форме фильтра.
8.  **«Что происходит, если результатов поиска нет?»**
    *   *Зачем:* Прояснить edge case. Показываем ли мы кнопку «Сбросить фильтры»? Предлагаем ли связаться с поддержкой? Это влияет на бэкенд (нужен ли отдельный endpoint для подсказок) и фронтенд.
9.  **«Нужен ли поиск по тексту товара внутри заказа?»**
    *   *Зачем:* Это кардинально разные задачи.
        *   *Легко:* Фильтр по ID/Номеру/Дате (SQL `WHERE`).
        *   *Сложно:* Полнотекстовый поиск по названиям товаров в связанных таблицах (нужен Full-Text Index или Elasticsearch). Если бизнес этого хочет — объем задачи вырастает в разы.

### 4. Технические ограничения и интеграции

10. **«Есть ли ограничение на количество возвращаемых записей за один запрос?»**
    *   *Зачем:* Если API должен возвращать максимум 50 заказов, а пользователь хочет увидеть 500, нам нужна правильная пагинация (Cursor-based или Offset-based). Offset-пагинация на больших данных медленная, возможно, понадобится курсорная.
11. **«Должна ли эта фича работать с мобильного приложения?»**
    *   *Зачем:* Если да, API должен быть универсальным. Если только для веб-сайта — можно оптимизировать под браузер (например, использовать более сложную логику фильтрации на клиенте, чтобы не перегружать сервер).

---

### Что я сделаю с ответами?

На основе ответов я скорректирую ТЗ:
*   Если «поиск по товарам» не нужен → убираю его из задачи, оставляем только фильтры по дате/статусу.
*   Если заказов много → добавляю требование к оптимизации SQL (индексы) и выбор типа пагинации.
*   Если нет макетов → добавляю задачу «Согласовать UX с дизайнером» перед началом разработки, чтобы не переделывать фронтенд.

**Итог:** Эти вопросы превращают размытое «сделайте поиск» в конкретную инженерную задачу с понятными границами, которая займет не 2 месяца, а 2 недели.

> ⏱ 27.8s · prompt 59929 tok · output 1775 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это классические ситуации, которые проверяют инженерную зрелость и коммуникационные навыки. Правильное поведение здесь напрямую влияет на то, доверят ли вам следующие задачи.

Вот мой алгоритм действий в каждом случае.

---

### Случай А: Нашел попутный баг, не связанный с задачей

**Главный принцип:** Не чинить чужой код без разрешения, но и не упускать его из виду.

#### Шаг 1: Оцените критичность
1.  **Critical (Блокирующий):** Баг ломает текущую фичу или делает приложение нестабильным.
2.  **High (Важный):** Баг затрагивает бизнес-логику, но не текущий модуль (например, вы нашли, что отправка email падает при наличии кириллицы в имени, хотя вы работали с заказами).
3.  **Low (Косметический):** Опечатка в логах, лишний лог, незначительная ошибка в UI другого раздела.

#### Шаг 2: Действие

*   **Если баг критичный и блокирует ваш текущий код:**
    *   *Действие:* Чините его.
    *   *Почему:* Вы не можете пройти CI/CD или тестирование, пока система сломана. Это «дорожная разметка», которую нужно подправить, чтобы проехать дальше.
    *   *Как сообщить:* В PR опишите отдельно: `🔴 Fixed critical bug in [Module] that was blocking [Current Task]`.

*   **Если баг не блокирует вашу работу (90% случаев):**
    *   *Действие:* **НЕ чините его в этом PR.** Создайте отдельный Ticket (баг-репорт) в трекере задач.
    *   *Почему:*
        1.  **Scope Creep (Раздувание объема):** Ревьюер ожидает только реализацию задачи X. Внесение неочевидных изменений в чужой код требует отдельного анализа рисков.
        2.  **Простота отката:** Если ваш PR с новой фичей сломает что-то, откатить нужно только фичу. Если вы еще и чинили баг Y, откат усложняется (а может быть, баг Y был намеренной временной костыль-фиксой для другой ситуации?).
        3.  **Фокус ревью:** Ревьюеру проще проверить 200 строк новой логики, чем 200 строк новой логики + 50 строк чужого кода, который он не знает.

    *   *Как сообщить:*
        *   Создайте тикет: `BUG: Error in payment module when using promo code (discovered while working on ORDER-123)`.
        *   В комментариях к своему PR или в чате команды напишите: *«Кстати, заметил баг в платежном модуле (см. тикет BUG-456). Не вписал его сюда, чтобы не раздувать PR».*

*   **Если баг очень простой (1-2 строки) и очевиден:**
    *   *Действие:* Можно исправить, но только если вы уверены, что понимаете контекст.
    *   *Как сообщить:* `🟢 Nit: Fixed typo in log message found along the way`. (Это принято, если это действительно тривиально).

---

### Случай Б: Критерий приемки противоречит цели задачи

**Главный принцип:** Остановиться, не копать дальше, эскалировать проблему.

Здесь вы не «разработчик», который должен найти способ втиснуть слона в холодильник. Вы «инженер», который сообщает, что холодильник не подходит для слона.

#### Шаг 1: Зафиксируйте противоречие (Докажи факт)
Не говорите: «Мне кажется, это ошибка».
Покажите: «Требование A говорит `X`, Требование B говорит `Y`. Технически это невозможно одновременно, потому что `Z`».

*Пример:*
*   *Цель:* «Сделать поиск быстрым (<200ms)».
*   *Критерий:* «Поиск должен искать по полному тексту во всех связанных таблицах (товары, комментарии, история изменений)».
*   *Противоречие:* Полнотекстовый поиск по 5 таблицам без спец. индексов/движка будет занимать >1 сек. Чтобы сделать <200ms, нужно либо убрать связанные таблицы, либо использовать Elasticsearch (что выходит за рамки текущей архитектуры).

#### Шаг 2: Предложите варианты решения (Не просто «нельзя», а «можно, если...»)
Заказчик/Продакт-менеджер любит выбирать из вариантов.

1.  **Вариант 1 (Рекомендуемый):** Изменить критерий. «Убрать поиск по комментариям, оставить только по названию товара. Тогда время будет <200ms».
2.  **Вариант 2 (Дорогой/Долгий):** Изменить цель. «Оставить полный поиск, но время ответа будет 1–2 сек. Согласны на такую UX-затяжку?»
3.  **Вариант 3 (Архитектурный):** «Ввести кэш результатов поиска. Тогда первое обращение 1 сек, последующие <200ms».

#### Шаг 3: Эскалация
Если продакт-менеджер не может принять решение на месте:
1.  Остановите работу над кодом (или продолжайте с другим функционалом).
2.  Создайте короткий документ или заметку в чате:
    > **Блокер по задаче ORDER-123:**
    > Выявлено противоречие между критерием «Скорость <200ms» и функциональностью «Полнотекстовый поиск по всем полям».
    >
    > **Варианты:**
    > 1. Урезаем функциональность (быстрее).
    > 2. Увеличиваем время ответа (полнее).
    >
    > **Решение:** Жду вашего ответа, чтобы не тратит время на переделку архитектуры.

#### Почему так?
1.  **Избежание «Golden Path»:** Если вы начнете кодить, пытаясь угодить обоим критериям, вы напишете сложный, хрупкий код, который через месяц придется выкидывать, потому что бизнес передумает.
2.  **Экономия времени команды:** Лучше 1 час обсуждения на этапе планирования, чем 3 дня переделывания кода на этапе ревью.
3.  **Показ профессионализма:** Вы думаете не только «как написать», но и «имеет ли смысл это писать».

---

### Итоговая таблица действий

| Ситуация | Действие | Почему |
| :--- | :--- | :--- |
| **Баг критичный, блокирует меня** | Чиню, комментирую в PR | Чтобы двигаться дальше. |
| **Баг некритичный, не мой модуль** | Создаю тикет, не чиню в PR | Чистота PR, простота отката, фокус ревьюера. |
| **Критерий противоречит цели** | Стоп. Фиксирую конфликт. Предлагаю варианты. Жду решения. | Избегаем создания неэффективного или не нужного кода. |

**Главный секрет:** В обоих случаях ключевое слово — **коммуникация до действия**. Разработчик, который молча чинит чужой баг или молча переписывает логику из-за противоречивых ТЗ, создает проблемы для команды. Разработчик, который останавливается и спрашивает, экономит время всем.

> ⏱ 27.8s · prompt 63033 tok · output 1785 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении задачи — это не просто «Сделано ✅». Это **документ передачи ответственности** от разработчика к команде (QA, PM, другим разработчикам). Его цель: доказать, что работа сделана качественно, и дать всем необходимые данные для дальнейших шагов.

Вот структура моего идеального отчёта.

---

### Часть 1: Что обязательно входит в отчёт (Базовый минимум)

Если задача выполнена полностью, отчёт содержит 5 блоков:

#### 1. Ссылки и контекст
*   **Ticket ID:** `ORDER-123`
*   **Pull Request / Commit:** Ссылка на PR в GitHub/GitLab или хэш коммита (если squash merge).
*   **Ветка:** `feature/order-search-optimization`

#### 2. Что было сделано (Change Summary)
Краткое описание изменений человеческим языком, а не техническим.
*   *«Добавлены фильтры поиска заказов по дате, статусу и номеру.»*
*   *«Оптимизирован SQL-запрос: добавлен индекс `(user_id, created_at)», время ответа снизилось с 1.2s до 300ms.»*
*   *«Обновлены интеграционные тесты: добавлено покрытие новых параметров API.»*

#### 3. Результаты тестирования (Evidence)
*   **CI/CD Status:** Скриншот или ссылка на зеленый пайплайн (Lint, Static Analysis, Unit Tests).
*   **Метрики:**
    *   *«П95 время ответа API: 280ms (требование: <500ms) — ✅»*
    *   *«Покрытие тестами нового модуля: 92%»*
*   **Демо (если есть UI):**
    *   Скриншоты нового интерфейса.
    *   Или ссылка на деплой на стейджинге, где это можно пощупать (`staging.myapp.com/orders?date_from=...`).

#### 4. Риски и зависимости (Risk Assessment)
*   *«Зависит от миграции БД `2023_10_15_0001_add_order_index`. Миграция должна быть применена перед деплоем.»*
*   *«Нет критических рисков. Изменения изолированы в модуле `OrderSearch`.»*

#### 5. Чек-лист готовности (Definition of Done)
Галочки по критериям приемки из ТЗ:
*   [x] Фильтры по дате работают.
*   [x] Поиск по номеру работает.
*   [x] Время ответа < 500ms.
*   [x] Написаны тесты.

---

### Часть 2: Что меняется, если часть задачи НЕ удалось сделать?

Если вы не закрыли 100% требований, статус задачи меняется на **«Partially Completed»** или **«Blocked»**, но чаще — **«Done with Technical Debt»**.

Главное правило: **Не скрывайте несоответствие.** Лучше честно указать, что сделано, а что нет, чем получить баг в проде через неделю.

В отчёт добавляются следующие блоки:

#### 1. Что НЕ сделано (Out of Scope / Blocked Items)
Явно перечисляете пункты из ТЗ, которые не выполнены, и **почему**.

*   *Пример:*
    *   ❌ *«Полнотекстовый поиск по названию товара внутри заказа — **НЕ ГОТОВ**.»*
    *   **Причина:** «Для реализации требуется интеграция с Elasticsearch, что выходит за рамки текущего спринта и требует изменения архитектуры (см. оценку в тикете PERF-45).»
    *   **Временное решение:** «Поиск пока идет только по номеру заказа и дате. UX-команда согласовала временное ограничение до релиза v2.0.»

#### 2. Обоснование решения (Trade-off Explanation)
Вы должны доказать, что это осознанный выбор, а не лень.

*   *«Мы выбрали оптимизацию SQL-запроса (индексы), потому что это дало 80% эффекта на скорости. Интеграция с Elasticsearch дала бы дополнительные 10%, но увеличила бы время разработки на 2 недели и сложность поддержки. Рекомендую вынести в бэклог на следующий квартал.»*

#### 3. Технический долг (Technical Debt)
Создаете новый тикет в трекере (или ссылаетесь на существующий) с меткой `Tech-Debt`.

*   **Новый тикет:** `SEARCH-101: Integrate Elasticsearch for full-text order search`
*   **Привязка:** Ссылаете этот тикет в отчете по выполненной задаче, чтобы связь была прослеживаемой.

#### 4. Изменение критериев приемки (DoD Adjustment)
Если первоначальные критерии приемки (DoD) были идеалистичными, вы предлагаете поправить их **до закрытия задачи**.

*   *«Предлагаю изменить критерий 'Скорость поиска < 200ms' на 'Скорость поиска < 500ms для базовых фильтров', так как полнотекстовый поиск отложен. Прошу подтвердить у PM.»*

---

### Пример готового отчёта (в чат или в описание PR)

> **Задача:** ORDER-123 (Оптимизация поиска заказов)
>
> **Статус:** ✅ Готово к ревью (с одним исключением)
>
> **Что сделано:**
> 1. Добавлены фильтры по дате, статусу, номеру заказа.
> 2. Оптимизирован запрос к БД (добавлен композитный индекс).
> 3. Написаны интеграционные тесты (10 новых сценариев).
> 4. **Результат:** Время ответа снизилось с 1.5s до 350ms (требование было <500ms). ✅
>
> **Что НЕ сделано:**
> ❌ Полнотекстовый поиск по названиям товаров.
> *Причина:* Требует внедрения Elasticsearch, что выходит за scope текущего спринта. Согласовано с тимлидом откладывать до v2.1.
> *Временный вариант:* Поле поиска ищет только по ID заказа.
> *Новый тикет:* [SEARCH-101](link-to-jira)
>
> **Риски:**
> - Нет критических.
> - Миграция БД должна быть проапленед первой.
>
> **Ссылки:**
> - PR: [GH-456](link-to-pr)
> - Скриншоты UI: [Figma](link)
> - Логи тестов: [CI-Log](link)

---

### Почему это важно?

1.  **Для PM/Бизнеса:** Они видят, что вы не «провалились», а приняли обоснованное инженерное решение. Это вызывает доверие.
2.  **Для QA:** Они знают, на что тестировать, а на что нет. Они не будут тратить время на поиск по товару, если вы сказали, что его там нет.
3.  **Для будущих разработчиков:** Они видят, где находится «костыль» или «долг», и понимают, почему код выглядит именно так.
4.  **Для вас:** Если через 2 месяца кто-то спросит: «Почему поиск работает медленно?», вы укажете на тикет и отчёт, где всё задокументировано. Вы не обязаны помнить всё вечно, но система должна помнить за вас.

> ⏱ 27.8s · prompt 64858 tok · output 1756 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

Короткий ответ: **В PostgreSQL нет ключевого слова `FORCE INDEX`**.

Это распространённая ошибка разработчиков, перешедших из мира MySQL/MariaDB. В MySQL вы можете писать:
```sql
SELECT * FROM orders FORCE INDEX (idx_order_date) WHERE ...
```
В PostgreSQL такого синтаксиса **не существует**. Если вы вставите это в запрос, получите синтаксическую ошибку.

---

### Так как же «заставить» PostgreSQL использовать конкретный индекс?

Постgresql использует **cost-based optimizer** (оптимизатор на основе стоимости). Он выбирает план запроса (включая использование конкретного индекса или сканирование всей таблицы), который, по его расчётам, будет самым быстрым.

Если вы хотите повлиять на выбор индекса, у вас есть несколько инструментов:

#### 1. Убедиться, что оптимизатор видит правильную статистику (Самый частый случай)
Часто оптимизатор «не видит» пользу от индекса, потому что устаревшая статистика заставляет его думать, что сканирование всей таблицы будет дешевле.

**Решение:**
```sql
ANALYZE table_name;
```
Эта команда пересчитывает статистики в `pg_stats`. После этого оптимизатор может выбрать ваш индекс.

#### 2. Использование `GUC` параметров для отладки (Временное решение)
Вы можете временно отключить использование определённых методов сканирования, чтобы «заставить» PostgreSQL использовать индекс.

Например, чтобы отключить последовательное сканирование (`Seq Scan`) и заставить использовать индекс:
```sql
SET enable_seqscan = off;
SELECT ...; -- Теперь он будет пытаться использовать индекс
RESET enable_seqscan;
```
> ⚠️ **Важно:** Это влияет на весь сеанс соединения. Используйте только для отладки или в очень специфических случаях через конфигурацию сессии в приложении.

Другие параметры:
*   `enable_indexscan = off`
*   `enable_bitmapscan = off`
*   `enable_hashjoin = off`

#### 3. Создание специализированных индексов (Лучший подход)
Если стандартный индекс не используется, возможно, он не оптимален для вашего запроса.

**Пример:**
У вас есть индекс по `created_at`, но вы часто ищете по `status = 'active' AND created_at > ...`.
Оптимизатор может решить, что лучше отфильтровать по `created_at` (крупный индекс) и потом проверить `status` (дорого), или наоборот.

**Решение:** Создайте композитный индекс, который точно соответствует запросу:
```sql
CREATE INDEX idx_orders_status_date ON orders (status, created_at);
```
Теперь оптимизатор с высокой вероятностью выберет этот индекс, так как он покрывает оба условия фильтрации.

#### 4. Использование `pg_stat_user_indexes` и `pg_stat_activity`
Проверьте, какие индексы вообще используются:
```sql
SELECT * FROM pg_stat_user_indexes 
WHERE relname = 'orders' 
ORDER BY idx_scan DESC;
```
Если `idx_scan` у вашего индекса равен 0 — он не используется. Возможно, он не нужен или не оптимален.

---

### Почему в PostgreSQL нет `FORCE INDEX`?

1.  **Доктрина "Trusting the Optimizer":** Команда PostgreSQL считает, что их оптимизатор умнее разработчика в большинстве случаев. Если оптимизатор выбрал `Seq Scan`, значит, он считает, что индекс будет медленнее (например, если выборка большая часть таблицы).
2.  **Гибкость:** PostgreSQL поддерживает множество типов индексов (B-Tree, GIN, GiST, BRIN) и методы сканирования. Силить один тип индекса — это упрощение, которое не всегда работает.
3.  **Стабильность:** Заставив оптимизатор использовать «неправильный» индекс, вы можете сделать запрос медленнее на разных объёмах данных. PostgreSQL стремится к предсказуемой производительности на основе статистики.

---

### Итог

| Действие | MySQL | PostgreSQL |
| :--- | :--- | :--- |
| Принудительное использование индекса | `FORCE INDEX (name)` | **Нет прямого аналога** |
| Влияние на выбор плана | Хинты | `ANALYZE`, композитные индексы, `SET enable_seqscan = off` (для отладки) |
| Рекомендуемый подход | Использовать хинты только при крайней необходимости | Оптимизировать индексы и статистику |

**Совет:** Если вы перешли из MySQL и видите медленный запрос в PostgreSQL:
1.  Запустите `EXPLAIN ANALYZE` на запросе.
2.  Посмотрите, какой индекс он хочет использовать.
3.  Если он использует `Seq Scan`, а вы ожидаете `Index Scan`:
    *   Проверьте, есть ли индекс по нужным колонкам.
    *   Запустите `ANALYZE`.
    *   Подумайте, не нужен ли вам композитный индекс или частичный индекс (`WHERE status = 'active'`).

Использование `SET enable_seqscan = off` в продакшене — это крайняя мера, которая может ухудшить производительность других запросов в том же соединении.

> ⏱ 20.0s · prompt 66635 tok · output 1230 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

Короткий ответ: **Практически на 0%.**

В современных версиях PHP (8.0+) включение `declare(strict_types=1)` **не замедляет** выполнение кода в заметной степени. Разница в производительности настолько мала, что она теряется в погрешности измерений (бенчмарков) и полностью затмевается стоимостью операций с базами данных, сетью или сложной бизнес-логикой.

Давайте разберем это подробнее, чтобы понять, откуда берется миф о «замедлении» и почему на практике он не подтверждается.

### 1. Почему возникает миф о замедлении?

Миф базируется на логике: *«Проверка типов — это дополнительное сравнение, а сравнение требует времени процессора»*.

Это верно на уровне микро-операций:
*   Без `strict_types`: PHP может пропустить неявные приведения (например, `string` -> `int`) на этапе вызова функции.
*   С `strict_types`: PHP вынужден строго проверять, что переданный тип совпадает с ожидаемым.

Но вот что важно:
1.  **Эта проверка происходит один раз на аргумент при вызове функции**, а не на каждой строке кода внутри функции.
2.  **Она очень廉价ая (дешевая) для CPU.** Проверка тегa типа в внутренней структуре PHP (zval) занимает нанosekundy (наноcекунды).
3.  **Сравнение с реальными операциями:**
    *   Проверка типа: ~1–5 нс.
    *   Вызов функции: ~50–100 нс.
    *   Операция с массивом: ~10–50 нс.
    *   Запрос к БД: ~1–100 мс (миллион раз больше).

### 2. Что говорит практика и бенчмарки?

Если вы прогоните один и тот же код с `strict_types=1` и `strict_types=0` через инструмент бенчмаркинга (например, `php-bench` или простой `microtime()` в цикле из 10 миллионов итераций простых операций), вы увидите разницу:

*   **В некоторых синтетических тестах:** Разница может составить **0.1% – 1%**.
    *   *Пример:* Функция, которая просто возвращает число.
    *   *Результат:* С `strict` на 0.5% медленнее.
*   **В реальном приложении (Symfony, Laravel, DDD):** Разница **не измеряется**.
    *   *Причина:* Время выполнения определяется I/O (вход/выход данных) и сложностью алгоритмов, а не проверкой типов аргументов.

### 3. Когда `strict_types` может «показаться» медленнее?

Единственный сценарий, где это может иметь значение — это **массивы очень больших размеров**, которые передаются по ссылке или по значению, и где происходит много неявных преобразований. Но даже тогда:

*   Если вы передаете массив из 1 миллиона элементов, время копирования/сравнения самого массива будет на порядки выше, чем время проверки, что массив передан как массив.
*   В 99% случаев ваши данные — это структуры из 5–50 полей. Проверка типов на них — ничтожно мала.

### 4. Почему же тогда его включают?

Не из-за скорости, а из-за **надежности и читаемости**.

*   **Локализация ошибок:** С `strict_types` ошибка типов падает сразу в момент передачи аргумента, а не глубоко внутри функции, когда данные уже приведены к «неправильному» виду и логика сломалась.
*   **Идемпотентность и предсказуемость:** Вы точно знаете, какой тип данных получит функция.
*   **Поддержка IDE и Static Analysis:** Инструменты (PhpStan, Psalm) лучше работают с кодом, где типы явны.

### 5. Исключение: `mixed` и Union Types

Если вы используете новые фичи PHP 8.0+, такие как `mixed`, `readonly`, `enums`, — это может *немного* повлиять на оптимизацию JIT-компилятора (если он включен). Но даже в этом случае:

*   JIT-компилятор оптимизирует горячие пути кода.
*   Проверка типов — это не горячий путь в большинстве веб-приложений (каждый запрос уникален, мало повторяющихся циклов).

### Итог

**`declare(strict_types=1)` не замедляет ваш PHP-код в реальном мире.**

*   **Наценка стоимости:** ~0% (в пределах погрешности).
*   **Плюс:** +100% к надежности, +50% к скорости отладки, +∞ к спокойствию.

**Рекомендация:** Включайте `strict_types=1` в каждом файле без сомнения. Вы не потеряете производительность, но выиграете в качестве кода и снизите количество багов, связанных с неявными приведениями типов.

> **Совет:** Если вы беспокоитесь о производительности, измеряйте *реальные* операции (БД, внешние API, сложные расчеты). Проверка типов аргументов — это не та вещь, которую стоит оптимизировать.

> ⏱ 19.7s · prompt 67899 tok · output 1229 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

На самом деле, здесь есть важная неточность: **Doctrine ORM (через DBAL и Unit of Work) поддерживает составные первичные ключи**, но поддерживает их **крайне неудобно** и с большими оговорками.

Фраза «не поддерживает» обычно означает: *«Поддерживает технически, но ломает эргономику, производительность и удобство работы с ассоциациями»*.

Давайте разберем, что происходит «под капотом», почему это больно и как с этим живут.

---

### 1. Как это выглядит технически?

В Doctrine вы объявляете составной ключ так:
```php
#[ORM\Entity]
class JoinTable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $roleId;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;
    
    // ...
}
```

#### Что Doctrine делает с этим?
1.  **Identifier:** Вместо одного ID, у сущности появляется массив или объект, содержащий все части ключа.
2.  **Unit of Work:** При отслеживании изменений (`dirty checking`) Doctrine должен сравнивать **все** части ключа, чтобы понять, изменилась ли сущность.
3.  **Ассоциации:**
    *   Если другая сущность ссылается на эту (`Many-to-One` на `JoinTable`), то `JoinColumn` должен содержать **все** части составного ключа.
    *   Это делает маппинг громоздким и трудным для чтения.

---

### 2. Почему это «не поддерживается» в бытовом смысле? (Проблемы)

#### А. Проблемы с ассоциациями (Самая большая боль)
Если у вас есть сущность `User`, а она связана с `Role` через промежуточную таблицу с составным ключом `(user_id, role_id)`:

*   **Простой случай (1 поле в ключе):** Легко маппить.
*   **Составной ключ (2+ поля в ключе):**
    *   Чтобы `User` ссылался на `Role`, `JoinColumn` в `User` должен бы содержать оба ID? Нет, обычно это Many-to-Many через JoinTable.
    *   Но если вы хотите, чтобы `Order` ссылался на `OrderItem`, а `OrderItem` имел составной ключ `(order_id, position)`, то маппинг становится кошмаром.
    *   **Lazy Loading:** Загрузка связанных сущностей по составному ключу часто требует нескольких запросов или сложного `JOIN FETCH`, который Doctrine может сгенерировать неоптимально.

#### Б. Нарушение принципа «Сущность = Сущность»
В DDD сущность идентифицируется по **одному** уникальному идентификатору. Составной ключ — это признак того, что вы моделируете не сущность, а **связь** (relationship) или **значение** (value object).
*   Если у объекта нет независимого жизненного цикла (его нельзя удалить без удаления связи, его нельзя изменить отдельно от другой части ключа), то он, скорее всего, не сущность, а часть другой сущности или Join-таблица.

#### В. Проблемы с Versioning и Locking
*   `#[ORM\Version]` на сущности с составным ключом работает, но требует осторожности при генерации SQL `UPDATE ... WHERE part1 = :x AND part2 = :y AND version = :z`.

#### Г. Сложность миграций
*   При изменении структуры (добавление третьего поля в ключ) миграция сложнее: нужно пересоздать индекс, проверить целостность данных.

---

### 3. Как с этим живут в реальных проектах?

Есть три основных подхода, от «терпим» до «избегаем».

#### Подход 1: Используйте это только для Join-таблиц (Many-to-Many)
Это самый правильный случай.
*   Таблица `user_roles` имеет составной ключ `(user_id, role_id)`.
*   Вы **не создаете** отдельную сущность `UserRole` с бизнес-логикой (например, права доступа, дата выдачи).
*   Вы просто маппите связь `Many-to-Many` между `User` и `Role`.
*   **Плюс:** Компактно, стандарт.
*   **Минус:** Нет гибкости (нельзя добавить к связи дополнительные поля без создания отдельной сущности).

#### Подход 2: Сущность с составным ключом как «Агрегат» (Редко)
Иногда бизнес требует, чтобы сущность определялась комбинацией полей.
*   *Пример:* `ExchangeRate` (Курс валюты), где ключ — `(from_currency, to_currency)`.
*   *Решение:* Создаете сущность `ExchangeRate` с двумя `@Id`.
*   *Как живут:*
    *   Репозиторий ищет по обоим параметрам: `$repo->findOneBy(['from' => 'USD', 'to' => 'EUR'])`.
    *   Избегают прямых ссылок на эту сущность из других сущностей (чтобы не усложнять маппинг).
    *   Используют ее только в сервисах/доменных методах.

#### Подход 3: Замена на «Суррогатный ключ» (Surrogate Key) (Рекомендуемый)
Это самый популярный подход в крупных проектах.

*   Даже если бизнес-логически ключ составной, в БД вы добавляете автоинкрементный `id` (BIGINT).
*   Составные поля (например, `user_id`, `role_id`) объявляются как `UNIQUE INDEX`, а не как `PRIMARY KEY`.
*   **Плюсы:**
    *   Удобный маппинг в Doctrine (стандартный 1 ID).
    *   Легко добавлять новые поля в связь.
    *   Легко делать ассоциации (Many-to-One на `id`).
    *   Быстрее индексы (одно целое число вместо двух).
*   **Минусы:**
    *   Нужно помнить, что `id` — это технический, а не бизнес-ключ.
    *   Нужно поддерживать `UNIQUE INDEX` для предотвращения дублей.

**Пример:**
```php
#[ORM\Entity]
class UserRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id; // Суррогатный ключ

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer')]
    private int $roleId;

    #[ORM\Index(columns: ['userId', 'roleId'], unique: true)] // Бизнес-уникальность
    public function __construct(int $userId, int $roleId) {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }
}
```

---

### 4. Что выбрать?

| Ситуация | Рекомендация |
| :--- | :--- |
| Простая Many-to-Many связь без доп. полей | **Составной ключ** (стандартный маппинг `ManyToMany`). |
| Many-to-Many с доп. полями (дата, статус) | **Суррогатный ключ** + `UNIQUE INDEX` на составные поля. |
| Сущность, которая всегда определяется парой (напр., курс валюты) | Можно использовать **составной ключ**, но будьте готовы к неудобствам в маппинге ассоциаций. Чаще все равно используют **суррогатный**. |
| Сложный доменный агрегат, где ID критичен для логики | **Суррогатный ключ**. Это безопаснее для ORM. |

### Итог

Doctrine **может** работать с составными ключами, но:
1.  Это усложняет маппинг ассоциаций.
2.  Это затрудняет работу с Unit of Work.
3.  Это нарушает эргономику DDD (сущность должна иметь один ID).

**В 90% случаев в реальных проектах с составными ключами используют подход «Суррогатный ID + Unique Index на составные поля»**, чтобы сохранить простоту работы с ORM и гибкость схемы БД.

> ⏱ 30.6s · prompt 69162 tok · output 1934 tok

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

Это классический пример «спагетти-кода» из эпохи PHP 5, который пытается сделать всё сразу. В нем есть проблемы на каждом уровне: от безопасности до архитектуры.

Давай разберем проблемы от самых критичных (красные флаги) до архитектурных (желтые флаги), а потом я покажу, как это выглядит в современном Symfony/PHP 8.

### 1. Список проблем (Code Smells & Bugs)

#### 🔴 Критические (Безопасность и Логика)
1.  **SQL Injection:**
    ```php
    $pdo->query("SELECT * FROM orders WHERE id = $id")
    ```
    Переменная `$id` подставляется в строку напрямую. Если пользователь пришлет `1; DROP TABLE orders;` — База Данных будет разрушена.
2.  **Race Condition (Гонка состояний) при оплате:**
    ```php
    $order = ... fetch();
    if (!$order) ...
    $pdo->exec("UPDATE ...");
    ```
    Между `SELECT` и `UPDATE` другой процесс может уже оплатить этот заказ или изменить его состояние. Мы перезаписываем статус, не проверив, а может он уже `paid`? Или `cancelled`?
3.  **Отсутствие проверки текущего статуса:**
    Мы ставим статус `paid` даже если заказ был отменен (`cancelled`) или уже оплачен. Бизнес-правило «Оплачен только Draft/Pending» нарушено.
4.  **Отсутствие транзакции:**
    Если `Mailer::send()` упадет с ошибкой (почтовый сервер недоступен), заказ в БД уже станет `paid`, но пользователь не получит письмо. Или наоборот: письмо ушло, но UPDATE не закоммитился (если `exec` откатился, а `send` уже выполнен). Нет атомарности.

#### 🟡 Важные (Архитектура и Качество)
5.  **Нарушение SRP (Single Responsibility Principle):**
    Контроллер делает 5 вещей: парсит запрос, ходит в БД, валидирует статус, шлет почту, пишет лог.
6.  **Hardcoded зависимости:**
    `DB::connect()`, `Mailer::send()`, `new Logger()`. Это статические методы и новые объекты внутри метода. Их невозможно замоткать в юнит-тестах.
7.  **Magic Strings:**
    `'paid'`, `'Ваш заказ оплачен'`. Если завтра статус переименуют в `PAID`, сломается код.
8.  **Отсутствие валидации входных данных:**
    Что, если `$id` = "abc"? `WHERE id = "abc"` может привести к неявному приведению в некоторых СУБД или просто вернет null, но лучше валидировать сразу.
9.  **Глобальное пространство имён:**
    Нет `namespace`.

---

### 2. Как бы я это переделал? (Пошаговый план)

Я бы начал с **разделения ответственности**.

**План действий:**
1.  **Вынести бизнес-логику в Service/Domain.** Контроллер должен только принимать запрос и отдавать ответ.
2.  **Ввести доменную сущность (Aggregate Root).** Проверка статуса и изменение статуса — это логика сущности `Order`.
3.  **Использовать Repository.** Абстрагироваться от SQL.
4.  **Обернуть операции в транзакцию.**
5.  **Использовать Events или Async Job для отправки почты.** Отправка почты не должна блокировать ответ пользователю и не должна быть частью критической бизнес-операции (если она не критична для статуса заказа).
6.  **Добавить типизацию и строгие типы.**

---

### 3. Рефакторинг (Symfony + PHP 8 + DDD подход)

#### Шаг 1: Доменная модель (Чистый PHP, без фреймворков)

```php
// src/Domain/Order.php
namespace App\Domain;

use App\Domain\Exceptions\OrderAlreadyPaidException;
use App\Domain\Exceptions\OrderInvalidStateForPaymentException;

final class Order
{
    private string $status;
    private string $email;

    public function __construct(
        private readonly int $id,
        string $status,
        string $email
    ) {
        $this->status = $status;
        $this->email = $email;
    }

    // Бизнес-логика: Только корень агрегата меняет статус
    public function pay(): void
    {
        if ($this->status === 'paid') {
            throw new OrderAlreadyPaidException();
        }
        if (!in_array($this->status, ['pending', 'unpaid'])) {
            throw new OrderInvalidStateForPaymentException();
        }

        $this->status = 'paid';
        
        // Опционально: генерация доменного события OrderPaid
        // $this->recordEvent(new OrderPaidEvent($this->id));
    }

    public function getStatus(): string { return $this->status; }
    public function getEmail(): string { return $this->email; }
    public function getId(): int { return $this->id; }
}
```

#### Шаг 2: Репозиторий (Инфраструктура)

```php
// src/Infrastructure/OrderRepositoryInterface.php
namespace App\Domain;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(int $id): ?Order;
}

// src/Infrastructure/PdoOrderRepository.php
namespace App\Infrastructure;

use App\Domain\Order;
use Doctrine\DBAL\Connection; // Используем Doctrine DBAL или PDO

class PdoOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Connection $db) {}

    public function findById(int $id): ?Order
    {
        $sql = "SELECT id, status, email FROM orders WHERE id = :id";
        $stmt = $this->db->executeQuery($sql, ['id' => $id]);
        $row = $stmt->fetchAssociative();

        if (!$row) {
            return null;
        }

        return new Order(
            (int) $row['id'],
            $row['status'],
            $row['email']
        );
    }

    public function save(Order $order): void
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $this->db->executeStatement($sql, [
            'status' => $order->getStatus(),
            'id' => $order->getId()
        ]);
    }
}
```

#### Шаг 3: Прикладной сервис (Оркестрация)

```php
// src/Application/PayOrderService.php
namespace App\Application;

use App\Domain\Order;
use App\Domain\OrderRepositoryInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PayOrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MailerInterface $mailer // Инъекция зависимости, легко мокается
    ) {}

    /**
     * @throws \App\Domain\Exceptions\OrderNotFoundException
     * @throws \App\Domain\Exceptions\OrderAlreadyPaidException
     */
    public function payOrder(int $orderId): void
    {
        // 1. Загрузка
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Бизнес-логика (Внутри здесь должна быть транзакция! См. ниже)
        // Для простоты покажем логику, но в реальном коде лучше использовать 
        // EntityManager::transactional или похожий механизм
        
        $order->pay(); // Метод сам проверит статус и бросит исключение, если нельзя платить

        // 3. Сохранение
        $this->orderRepository->save($order);

        // 4. Side-effect: Отправка письма
        // Если почта важна для UX, но не для целостности БД, 
        // лучше делать это асинхронно (через Messenger). 
        // Но если оставляем синхронно, ловим ошибки отдельно, 
        // чтобы не откатывать заказ из-за проблем с почтой.
        try {
            $email = (new Email())
                ->to($order->getEmail())
                ->subject('Ваш заказ оплачен')
                ->html('<h1>Thank you!</h1>');
            
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Логируем, но НЕ кидаем исключение наружу, 
            // так как заказ уже сохранен и оплачен.
            \Monolog\Logger::getLogger()->warning('Failed to send confirmation email', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
```

#### Шаг 4: Контроллер (Тонкий слой)

```php
// src/Controller/OrderController.php
namespace App\Controller;

use App\Application\PayOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/{id}/pay', name: 'order_pay', methods: ['POST'])]
    public function pay(int $id, Request $request, PayOrderService $payOrderService): JsonResponse
    {
        try {
            // Валидация ID обычно происходит на уровне маршрута/типов, 
            // но можно добавить дополнительную проверку
            
            $payOrderService->payOrder($id);

            return new JsonResponse(['status' => 'ok', 'message' => 'Order paid']);

        } catch (\App\Domain\Exceptions\OrderNotFoundException $e) {
            return new JsonResponse(['error' => 'Not Found'], 404);
        } catch (\App\Domain\Exceptions\OrderAlreadyPaidException $e) {
            return new JsonResponse(['error' => 'Already Paid'], 409);
        } catch (\App\Domain\Exceptions\OrderInvalidStateForPaymentException $e) {
            return new JsonResponse(['error' => 'Invalid Status'], 400);
        } catch (\Exception $e) {
            // Неопределенная ошибка
            \Monolog\Logger::getLogger()->error('Unexpected error paying order', ['exception' => $e]);
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }
}
```

#### Шаг 5: Обработка транзакций (Критически важно!)

В `PayOrderService` у нас есть риск: если `save()` выполнился, а потом `Mailer` упал, это ок (мы это учли). Но если `save()` не выполнен из-за ошибки в БД?

Лучший способ — использовать **Unit of Work** и транзакции. В Symfony с Doctrine это делается через `$em->transactional()`.

Если мы хотим строго атомарное действие (Заказ + Письмо в одной транзакции, если письмо критично), логика меняется. Но обычно письмо — это отдельная ответственность.

**Улучшенный вариант с транзакцией для БД:**

```php
use Doctrine\ORM\EntityManagerInterface;

class PayOrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MailerInterface $mailer,
        private EntityManagerInterface $em // Для управления транзакцией
    ) {}

    public function payOrder(int $orderId): void
    {
        // Оборачиваем работу с БД в транзакцию
        $this->em->transactional(function () use ($orderId) {
            $order = $this->orderRepository->findById($orderId);
            if (!$order) {
                throw new OrderNotFoundException($orderId);
            }

            $order->pay(); // Бросит исключение при некорректном статусе
            $this->orderRepository->save($order);
        }, maxRetries: 0, rollbackOnException: true);

        // Письмо отправляем ВНЕ транзакции, чтобы не держать соединение с БД открытым
        $order = $this->orderRepository->findById($orderId); // Перечитаем для актуальных данных
        $this->sendEmail($order);
    }
    
    // ... метод sendEmail
}
```

### Итоговое сравнение

| Аспект | Было (Спагетти) | Стало (Clean Architecture) |
| :--- | :--- | :--- |
| **Безопасность** | SQL Injection | Prepared Statements (`:id`) |
| **Логика** | В контроллере | В доменной сущности и сервисе |
| **Тестируемость** | Сложно (статические методы) | Легко (инъекции интерфейсов) |
| **Ошибки** | Не обработаны | Специфические исключения + HTTP коды |
| **Гибкость** | Только этот один способ оплаты | Легко добавить новые правила в `Order::pay()` |
| **Читаемость** | 10 строк "магии" | 3 ясных слоя (Controller -> Service -> Domain) |

### С чего начал бы и почему?

1.  **С безопасности (SQL Injection).** Это быстро исправить и критично.
2.  **С вынесения бизнес-логики в класс `Order`.** Чтобы понять, *что* именно запрещено менять (статус).
3.  **С внедрения зависимости.** Чтобы код можно было запустить в тесте без реальной БД и почты.
4.  **С транзакций.** Чтобы гарантировать целостность данных.

Этот рефакторинг превращает код из «сборки по частям» в **промышленное приложение**, которое можно масштабировать, тестировать и поддерживать.

> ⏱ 50.2s · prompt 71306 tok · output 3167 tok

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

Этот код — классический пример нарушения **Clean Architecture** и **DDD**, который часто встречается у разработчиков, перешедших из «классического» Object-Oriented программирования или из Laravel/PHP без глубокого погружения в доменное моделирование.

Вот список проблем, разбитый по степени критичности, и вариант исправления.

---

### 🚨 Основные проблемы

#### 1. Зависимость домена от инфраструктуры (Нарушение Dependency Rule)
```php
private EntityManagerInterface $em
```
*   **Проблема:** `EntityManagerInterface` — это часть Symfony/Doctrine (Инфраструктура). Доменная сущность (`User`) не должна знать о том, как она хранится (в БД, в памяти, в Redis или в JSON-файле).
*   **Последствие:** Вы не можете тестировать доменную логику без загрузки всего Symfony-керна. Если вы решите заменить Doctrine на Eloquent или чистый SQL, вам придется переписывать все доменные сущности.

#### 2. Нарушение инкапсуляции и контракта (Side Effects)
```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush(); // <-- Боковой эффект (Side Effect)
}
```
*   **Проблема:** Метод меняет состояние объекта **и** пишет данные в базу данных.
*   **Последствие:**
    *   **Трудность тестирования:** Чтобы протестировать, что email поменялся, нужно мокнуть `EntityManager` и проверять, вызывался ли `flush()`.
    *   **Непредсказуемость:** Разработчик, вызывающий `user->changeEmail(...)`, может ожидать, что данные только изменились в памяти, а не сразу записались в БД. Это ломает концепцию **Unit of Work** (где `flush` происходит в одном месте — в сервисе или контроллере).
    *   **Ошибки транзакций:** Если после смены email нужно также сменить пароль, и при смене пароля возникнет ошибка — будет ли `flush` откатывать изменение email? Ситуация мутная.

#### 3. Отсутствие валидации (Invariants)
*   **Проблема:** Метод принимает любой `string`.
*   **Последствие:** В домене может оказаться `User` с email `"not-an-email"` или `""`. Доменные сущности должны быть всегда валидными (Invariant). Валидация должна происходить **внутри** домена, а не в контроллере (хотя контроллер тоже должен валидировать входные данные, «последний рубеж» — домен).

#### 4. Отсутствие проверки уникальности (Business Rule)
*   **Проблема:** Сущность `User` сама по себе не может гарантировать, что новый email не занят другим пользователем (если только не проверяет это через `em`, что опять же тянет инфраструктуру в домен).
*   **Решение:** Уникальность — это правило целостности БД, но бизнес-логика «можно ли сменить email» должна быть в домене.

---

### ✅ Как правильно? (Clean Architecture + DDD)

#### Шаг 1: Чистая доменная сущность

Сущность отвечает только за **логику изменения состояния** и **валидацию**.

```php
namespace App\Domain\User;

final class User
{
    private string $email;

    public function __construct(string $email)
    {
        // Инвариант: email всегда должен быть валидным при создании
        if (!self::isValidEmail($email)) {
            throw new \InvalidArgumentException("Email format is invalid");
        }
        $this->email = $email;
    }

    public function changeEmail(string $newEmail): void
    {
        // Инвариант: новый email должен быть валидным
        if (!self::isValidEmail($newEmail)) {
            throw new \InvalidArgumentException("Email format is invalid");
        }

        // Бизнес-логика (опционально): можно ли менять email?
        // Например, если пользователь уже верифицирован, может быть нужна дополнительная проверка.

        $this->email = $newEmail;
    }

    public function email(): string
    {
        return $this->email;
    }

    private static function isValidEmail(string $email): bool
    {
        // Здесь чистая PHP-логика, без внешних зависимостей
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

**Что изменилось:**
1.  Убрали `EntityManagerInterface`.
2.  `changeEmail` теперь только меняет внутреннее состояние.
3.  Добавлена валидация формата email.

---

#### Шаг 2: Репозиторий (Инфраструктура)

Репозиторий знает, как сохранять объекты, но домен не знает о нем.

```php
namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    // Для проверки уникальности (если нужно)
    public function existsByEmail(string $email): bool;
}
```

Реализация в `App\Infrastructure\UserRepository` будет маппить `User` на Doctrine Entity и вызывать `$em->persist()` / `flush()`.

---

#### Шаг 3: Прикладной сервис (Application Layer)

Здесь мы оркестрируем процесс. Сервис знает о домене и репозитории, но не знает о контроллерах.

```php
namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class UpdateUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private EntityManagerInterface $em // Используем только для управления транзакцией
    ) {}

    public function updateEmail(int $userId, string $newEmail): void
    {
        // 1. Начинаем транзакцию (Unit of Work)
        $this->em->transactional(function () use ($userId, $newEmail) {
            
            // 2. Загружаем агрегат
            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new UserNotFoundException($userId);
            }

            // 3. (Опционально) Проверяем уникальность через репозиторий
            if ($this->userRepo->existsByEmail($newEmail)) {
                throw new EmailAlreadyTakenException();
            }

            // 4. Применяем бизнес-логику (домен сама валидирует формат)
            $user->changeEmail($newEmail);

            // 5. Сохраняем (persist)
            $this->userRepo->save($user);
            
            // FLUSH произойдет автоматически в конце замыкания transactional()
        });
    }
}
```

**Почему так хорошо:**
1.  **Атомарность:** Все изменения внутри `transactional()` выполнятся или не выполнятся вовсе.
2.  **Отсутствие Side Effects в домене:** Домен просто меняет данные в памяти.
3.  **Контроль flush:** Вы явно видите, когда данные уходят в БД (в конце транзакции).
4.  **Тестируемость:**
    *   Тест `User::changeEmail` — чистый юнит-тест, без моков БД.
    *   Тест `UpdateUserService` — мокируем только `UserRepositoryInterface`.

---

#### Шаг 4: Контроллер (Interface Layer)

Контроллер остается тонким. Он валидирует входные данные (формат) и вызывает сервис.

```php
namespace App\Controller;

use App\Application\User\UpdateUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function __construct(private UpdateUserService $userService) {}

    public function updateEmail(Request $request, int $id): Response
    {
        $newEmail = $request->get('email');
        
        // Базовая валидация на уровне API (быстрый отсев мусора)
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return new Response('Invalid email format', 400);
        }

        try {
            $this->userService->updateEmail($id, $newEmail);
            return new Response('Email updated', 200);
        } catch (\App\Domain\Exception\EmailAlreadyTakenException $e) {
            return new Response('Email already in use', 409);
        }
    }
}
```

---

### Итоговое сравнение

| Характеристика | Было (Грязный код) | Стало (Clean Architecture) |
| :--- | :--- | :--- |
| **Зависимости домена** | `EntityManagerInterface` | Ни на что (кроме PHP core) |
| **Где происходит валидация?** | Везде / нигде | В доменной сущности (формат) + в сервисе (уникальность) |
| **Где происходит flush?** | Внутри метода смены email | В конце транзакции сервиса |
| **Тестируемость** | Нужен мок EntityManager | Юнит-тест без моков + Интеграционный тест с моком репозитория |
| **Заменяемость БД** | Сложная | Легкая (меняем только Infrastructure) |

**Главный вывод:** Доменная сущность должна быть «глупым» контейнером бизнес-правил, который не знает, где он находится (в памяти, в БД или в облаке). Вся «магия» хранения и оркестрации выносится наружу, в слои Application и Infrastructure.

> ⏱ 37.0s · prompt 74619 tok · output 2245 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Работа с незнакомым стеком — это не тест на эрудицию, а тест на **скорость адаптации** и **управление рисками**. Моя цель — не «знать всё», а «достаточно понять, чтобы решить задачу с минимальным ущербом для общего процесса».

Вот мой алгоритм действий, разбитый на фазы.

---

### Фаза 1: Разведка и Калькуляция (0–1 час)
**Цель:** Понять масштаб проблемы и найти «якорь» (опытного человека или документацию).

1.  **Чтение ТЗ и логов:**
    *   Что именно сломано? Есть ли ошибка в логах? Какой статус-код?
    *   *Действие:* Скопирую текст ошибки, поищу его в Google. 90% проблем с незнакомыми фреймворками (Next.js, NestJS, Rust) имеют готовые решения на Stack Overflow или в документации.
2.  **Анализ кодовой базы (Code Search):**
    *   Посмотрю, есть ли в проекте другие места, где используется аналогичная логика.
    *   *Пример:* Если нужно добавить новую эндпоинтную точку в GraphQL-шеме, я посмотрю, как написаны старые. Это мой лучший справочник.
3.  **Оценка «температуры» проблемы:**
    *   **Low Risk:** Баг в UI, настройка логирования, добавление простой функции. -> *Разбираюсь сам.*
    *   **High Risk:** Смена архитектуры, миграция БД, проблемы с производительностью, безопасность. -> *Привлекаю команду.*

---

### Фаза 2: Изолированное экспериментирование (1–4 часа)
**Цель:** Понять «как это работает здесь», а не «как это работает в теории».

1.  **Создание песочницы (Sandbox):**
    *   Не трогаю `main`/`develop`. Создаю отдельную ветку или локальный sandbox.
    *   Пишу минимальный воспроизводимый пример (MRE - Minimal Reproducible Example).
    *   *Почему:* Если я буду экспериментировать в общем коде, я могу случайно сломать чужие фичи.
2.  **Чтение документации «снизу вверх»:**
    *   Не читаю «Введение». Иду сразу в раздел «Configuration» или «API Reference».
    *   Ищу примеры в репозитории проекта (examples, test files). Тесты — это лучшие примеры того, как авторы фреймворка ждут, что их код будет использоваться.
3.  **Локализация зависимости:**
    *   Если проблема связана с конкретной библиотекой, я изучаю её README и issues.
    *   Часто проблема кроется не во фреймворке, а в версии зависимости (package.json / composer.json).

---

### Фаза 3: Решение и Валидация (4–8 часов)
**Цель:** Написать рабочее решение и доказать, что оно работает.

1.  **Реализация:**
    *   Пишу код, опираясь на найденные паттерны.
    *   Если нужно что-то «хакнуть» (например, обойти баг версии), я пишу комментарий: `// Hack for [Library] v1.2: workaround for issue #123`.
2.  **Написание теста (Критически важно!):**
    *   Для нового кода я *обязан* написать тест.
    *   *Почему:* Если я не уверен в понимании технологии, тест — это мой страховочный якорь. Если завтра кто-то обновит зависимость и сломает мой код, тест упадет, и будет понятно, где проблема.
3.  **Проверка побочных эффектов:**
    *   Запускаю весь набор тестов проекта.
    *   Проверяю, не изменился ли вывод API (если это бэкенд) или поведение UI (если это фронтенд).

---

### Фаза 4: Коммуникация и Рефакторинг (До сдачи)

1.  **Документирование «Зачем так»:**
    *   В PR пишу: «Использовал метод X вместо Y, потому что в версии Z этого фреймворка есть баг...».
    *   Это экономит время ревьюеру, который знает фреймворк лучше меня.
2.  **Предложение по улучшению (Optional):**
    *   Если я заметил, что конфигурация проекта неудобная для этой технологии, я пишу в чат команды: «Ребята, может, стоит вынести это в `.env`?».

---

### Когда разбираться самому, а когда звать на помощь?

Это ключевой вопрос. Я использую правило **«2 x 2»**:

#### 🟢 Разбираюсь СЕБЯ, если:
1.  **Время позволяет:** У меня есть 1–2 дня, а задача не блокирует других.
2.  **Проблема изолирована:** Она затронет 2–3 файла, которые я сам написал или которые легко заменить.
3.  **Документация отличная:** Если у технологии есть четкая, актуальная документация (например, Vue, React, Django), я справлюсь сам за час-два.
4.  **Я могу откатить изменения:** Если код легко удалить, я могу позволить себе экспериментировать.

#### 🔴 Зову ПОМОЩЬ (или делегирую), если:
1.  **Время горит:** Дедлайн через 2 часа, а я только начал читать документацию. Лучше попросить сеньора за 15 минут, чем сидеть 4 часа.
2.  **Проблема «глубокая»:** Ошибка происходит в нод-модулях, сборщике (Webpack/Vite/Rollup), или ядро СУБД. Здесь нужны знания «под капотом», которые обычно есть только у архитекторов или мидл-сеньоров, специализирующихся на этом стеке.
3.  **Нет документации или она устарела:** Если я 30 минут не могу найти ответ на базовый вопрос — это плохой знак. Значит, технология сложная или кривая. Лучше спросить того, кто с ней работал.
4.  **Влияет на производительность/безопасность:** Если задача касается кэширования, транзакций или прав доступа, ошибка стоит дорого. Тут лучше «переупаковать» знания команды, чем гадать.
5.  **Я застрял более чем на 30–45 минут:** Если я читаю документацию, пробую вариант A, B, C, и ничего не работает, а моя уверенность падает — это сигнал остановки. Я тратю время на *поиск*, а не на *решение*.

---

### Пример диалога с командой (если зову на помощь)

Я не приду с фразой «У меня не работает Vue Router».

Я приду с сообщением:
> «Коллеги, мне нужно решить задачу [LINK]. Я изучил документацию Vue Router v4 и попробовал варианты A и B (пример кода). Проблема в том, что при [условие] возникает ошибка [ошибка]. По моему мнению, дело в [гипотеза]. Кто-то из вас сталкивался с подобным в нашем стеке? Могу ли я заглянуть к [Имя коллеги] на 10 минут, чтобы прояснить этот момент?»

### Итог

Мой подход — это **агрессивная изоляция проблемы**.
1.  Не паникую.
2.  Нахожу минимальный кусок, который можно поломать/починить.
3.  Пишу код на основе примеров, а не теории.
4.  Покрываю тестами.
5.  Честно оцениваю, где я «на грани» знаний, и не боюсь попросить подержать.

В современном мире скорость поиска информации важнее скорости написания кода. Тот, кто умеет быстро ориентироваться в незнакомом стеке, ценится выше того, кто «знает все наизусть», но не умеет искать.

> ⏱ 30.9s · prompt 76935 tok · output 1878 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Главное правило в такой ситуации: **сообщить о переносе до дедлайна, а не после**.

«Упущенный» дедлайн, о котором вы узнали в момент сдачи, разрушает доверие. Дедлайн, который был скорректирован заранее, — это просто изменение плана.

Вот мой алгоритм действий:

### 1. Остановись и оцени текущий прогресс (Самодиагностика)
Прежде чем писать сообщение, четко осознай:
*   **Сколько уже сделано?** (В процентах или в конкретных фичах).
*   **Почему затягивается?** (Неизвестная сложность, баг в зависимости, отсутствие документации, недооценка объема).
*   **Что осталось?**
*   **Когда реально будет готово?** (Добавь буфер 10–20%, чтобы не повторить ситуацию завтра).

> **Частая ошибка:** Разработчик думает: «Я почти закончил, осталось чуть-чуть». Если вы чувствуете, что осталось «чуть-чуть», но уже уперлись в стену — этого «чуть-чуть» может быть больше, чем весь путь назад.

### 2. Составь сообщение (Формула: Факт + Причина + Новое время + План)

Не пиши просто: «Не успеваю».
Пиши: «Ситуация изменилась, и я знаю, что делать».

**Пример сообщения:**

> **Привет, [Имя PM/Тимлида]!**
>
> Хочу предупредить заранее: задачу [Название/ID] я не успею закончить к завтрашнему дню, как планировал.
>
> **Почему:**
> По ходу работы выяснилось, что [краткая причина: например, «библиотека X ведет себя не так, как описано в доках, и потребовалось время на поиск рабочего обходного пути» / «сложнее, чем казалось, интеграция с API Y имеет непредвиденные ограничения»].
>
> **Текущий статус:**
> *   ✅ Готово: [Основная логика, 70% кода].
> *   ⏳ В работе: [Настройка тестов / Обработка edge cases].
>
> **Новый дедлайн:**
> Я планирую сдать готовый код и тесты в **[День недели], к [Время, например, 12:00]**. Это даст мне [X] часов дополнительного времени, чтобы убедиться в стабильности.
>
> **Риск:**
> Если к [Время] я не смогу закрыть последний пункт, я вынесу его в отдельный баг-фикс, чтобы не задерживать общий релиз (если это применимо).
>
> Есть вопросы?

### 3. Почему такая структура работает?

1.  **«Хочу предупредить заранее»:** Показывает, что вы управляете процессом, а не процесс управляет вами.
2.  **Конкретная причина:** Вы не говорите «мне было трудно». Вы говорите, *что* именно было трудно. Это помогает команде понять, нужно ли им помогать или это чисто ваша задача.
3.  **Текущий статус:** PM видит, что работа ведется, вы не «в тумане». Это снижает тревогу.
4.  **Новый реалистичный дедлайн:** Вы не просите «еще часок». Вы дайте конкретное время, к которому будете готовы.
5.  **Управление рисками:** Вы предлагаете план B (вынести часть в баг), что показывает вашу ответственность за общий релиз.

### 4. Что НЕ делать?

*   **Не молчи.** Худшее, что можно сделать — промолчать и сказать об этом в момент, когда начнутся вопросы «Где код?».
*   **Не извиняйся слишком много.** «Простите, прости, прости» — это сигнал слабости. Лучше сухо и по делу: «Ситуация изменилась, вот новый план».
*   **Не обещай «к концу дня», если не уверен.** Лучше скажи: «К 12:00», и если в 11:50 все готово — это бонус. Если скажешь «к концу дня», а в 23:50 все еще не готово — ты снова уплываешь.
*   **Не валидируй себя лично.** Это не твоя вина, что задача оказалась сложнее. Это особенность разработки.

### 5. После сообщения

1.  **Работай до конца (или до нового дедлайна).**
2.  **В день сдачи:** Если сроки соблюдены — просто отправь сообщение: «Готово, PR здесь: [ссылка]».
3.  **Retrospective (опционально):** На ретро-митинге можно коротко упомянуть: «В задаче X я ошибся в оценке из-за [причина]. В будущем для подобных задач буду закладывать +X% времени». Это показывает, что вы учитесь.

### Итог

**Залог доверия — не точное попадание в первый дедлайн, а предсказуемость.**
Коллеги могут простить вам опоздание, если они были предупреждены заранее и знают новый реалистичный срок. Они не простят вам, если вы исчезли на день, а потом сказали: «Извините, я не знал, что будет так сложно».

> ⏱ 20.9s · prompt 78852 tok · output 1241 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это классическая дилемма инженерии: **«Good Enough» (Достаточно хорошо) vs «Perfect» (Идеально)**.

Ответ на этот вопрос зависит не от вашего личного вкуса, а от **контекста** (бизнес-цели, сроки, риски). Я использую набор фильтров, которые помогают принять решение быстро и обоснованно.

---

### 1. Три вопроса-фильтра (Перед тем как отдать код)

Прежде чем нажать «Send PR», я задаю себе эти три вопроса. Если на любой из них ответ «Нет» — код нужно дорабатывать или пересматривать.

1.  **Безопасность и Правильность (Correctness):**
    *   Работает ли код так, как ожидается в 99% сценариев?
    *   Обработаны ли критические ошибки (null, timeout, неверные типы)?
    *   *Если нет:* Это 🔴 Blocker. Отдавать рано.
2.  **Читаемость (Readability) для другого человека:**
    *   Если колледа откроет этот файл завтра, сможет ли он понять логику за 2 минуты без комментариев?
    *   Есть ли магические числа, непонятные имена, дублирование логики?
    *   *Если нет:* Это 🟡 Major. Лучше потратить 15 минут на рефакторинг сейчас, чем тратить час на объяснение/багфикс потом.
3.  **Тестируемость (Testability):**
    *   Есть ли тесты, которые доказывают, что код работает?
    *   Если код сложно тестировать (много зависимостей, статических методов) — это сигнал, что архитектура может быть плохой.
    *   *Если нет:* Код может работать, но он хрупкий. Нужно либо исправить архитектуру, либо добавить интеграционный тест, который покрывает сценарий.

---

### 2. Матрица решений: Качество vs Скорость

Я не работаю по принципу «всегда идеально» или «всегда быстро». Я выбираю режим в зависимости от типа задачи.

| Тип задачи | Приоритет | Критерий «Достаточно хорошо» | Что можно пропустить |
| :--- | :--- | :--- | :--- |
| **Hotfix (Критичный баг)** | ⚡️ Скорость | Код не ломает то, что работало. Баг исправлен. | Идеальное назовение, полная переписываемость, оптимизация. |
| **Прототип / Proof of Concept** | ⚡️ Скорость | Демонстрирует возможность. Работает локально. | Тесты, обработка ошибок, архитектура, безопасность. |
| **Внутренний инструмент (CLI, скрипт)** | ⚖️ Баланс | Работает, понятен автору и 1-2 коллегам. | Абсолютная изоляция зависимостей, полная документация. |
| **Бизнес-логика (Ядро)** | 🛡️ Качество | Чистая архитектура, покрытие тестами, понятные инварианты. | Ничего существенного. Здесь ошибки стоят дорого. |
| **API / Внешний контракт** | 🛡️ Качество | Стабильная сигнатура, валидация, идемпотентность. | Внутренняя «красота» реализации (главное, что контракт соблюдается). |

---

### 3. Практические правила (Heuristics)

#### Правило «20 минут»
Если рефакторинг или улучшение кода займет **менее 20 минут**, делай это сейчас.
Если больше — создай тикет `Tech-Debt` и отдай текущий код (если он рабочий).
*Почему:* Мелкие улучшения снижают когнитивную нагрузку и риск регрессии. Большие — требуют планирования.

#### Правило «Не изобретай велосипед»
Если вы написали код на 100 строк, который можно заменить 3 строчками вызова стандартной библиотеки или популярного пакета (с проверенной лицензией) — **замените**.
*Почему:* Поддержка вашего кода в будущем будет стоить дороже, чем риск использования стороннего пакета (который обычно стабилен).

#### Правило «YAGNI» (You Aren't Gonna Need It)
Не добавляйте абстракции «на случай, если нам когда-нибудь понадобятся».
*   ❌ Плохо: Создали интерфейс `PaymentProcessor` и 3 реализации, хотя сейчас нужен только один.
*   ✅ Хорошо: Написали конкретный класс `StripeProcessor`. Если завтра понадобится PayPal, тогда и будем создавать интерфейс (Refactoring в момент потребности).

#### Правило «Документируйте 'Почему', а не 'Что'»
Код должен говорить сам за себя. Комментарий нужен только если логика **неочевидна** или есть **костыль** под конкретный баг.
*   ❌ `// Устанавливаем статус в Paid`
*   ✅ `// Stripe возвращает latency в мс, а мы ожидаем с. Умножаем на 1000.`

---

### 4. Как я принимаю решение в реальном времени?

Ситуация: Я написал метод `calculatePrice()`. Он работает, но немного громоздкий. Есть сомнения: рефакторить или нет?

**Алгоритм:**
1.  **Посмотрел на тесты:** Они зеленые. Значит, логика работает.
2.  **Оценил читаемость:** Метод на 50 строк, есть вложенные `if-else`. Для меня это утомительно. Для ревьюера — тоже.
3.  **Оценил сложность рефакторинга:** Нужно вынести расчет скидки в отдельный класс `DiscountCalculator`. Оцениваю: 30–40 минут работы + написание 2-3 тестов для нового класса.
4.  **Проверил контекст:**
    *   Дедлайн через 2 часа? -> **Отдаю как есть.** Создаю тикет «Refactor price calculation».
    *   Дедлайн через неделю? -> **Рефакторю сейчас.** Это улучшит качество ядра системы, на котором будет строиться другая логика.

### 5. Итог: Что значит «Достаточно хорошо»?

Для меня «Достаточно хороший код» — это код, который:
1.  **Работает** (проходят тесты).
2.  **Понятен** (ревьюер не спрашивает «а зачем ты это так сделал?» более 2 раз).
3.  **Не создает новых рисков** (нет глобальных переменных, нет скрытых side-effects).

Всё, что сверх этого (микроскопическая оптимизация скорости на 0.001ms, идеальное назовение всех переменных) — это дело вкуса и технического долга, который нужно отслеживать, а не решать немедленно, если он не блокирует бизнес.

**Главный враг — это не «плохой код», а «медленный релиз» из-за погони за перфекционизмом в неправильном месте.**

> ⏱ 26.8s · prompt 80146 tok · output 1639 tok

---
