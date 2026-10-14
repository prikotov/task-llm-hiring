# Собес: `qwen3.8:latest` · роль: backend — run-09

- Дата: 2026-10-06T19:21:58.770831+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Как искусственный интеллект, я не обладаю личным опытом работы, зарплатами или стажировками, однако я имею доступ к огромному массиву данных, включающий документацию, исходные код популярных проектов, Stack Overflow, GitHub и академические работы по разработке ПО. Мои «знания» в PHP-экосистеме формируются на основе анализа лучших практик, паттернов проектирования и актуальных стандартов (PSR), что позволяет мне генерировать код, отлаживать ошибки, объяснять архитектурные решения и оптимизировать производительность систем на PHP и смежных технологиях.

Вот моя оценка уровня знаний по запрошенным разделам (шкала 0–10), где 10 означает глубокое, экспертное понимание, включая внутренние механики и edge-cases, а 0 — полное отсутствие знаний:

*   **PHP 8 (10/10)**: Я детально знаком со всеми новыми возможностями PHP 8 (JIT, readonly properties, enums, named arguments, fiber, etc.), знаю их ограничения и лучшие практики использования.
*   **Symfony (10/10)**: Я понимаю внутреннюю архитектуру Symfony (Service Container, DI, Events, Routing), умею настраивать компоненты, писать bundle’ы и оптимизировать производительность приложения.
*   **Doctrine (10/10)**: Я хорошо владею ORM-слоем, знаю механизмы hydration, N+1 problem, lazy loading, DQL/SQL-оптимизации и управление сущностями.
*   **PostgreSQL (9/10)**: Я уверенно работаю с SQL, индексированием, планировщиком запросов, window functions, CTE и транзакциями, но не имею прямого опыта настройки и тюнинга кластеров PostgreSQL на уровне DevOps.
*   **DDD (8/10)**: Я понимаю концепции Bounded Context, Aggregates, Domain Events и т.д., умею моделировать доменную логику, но практическая реализация DDD сильно зависит от конкретной бизнес-логики, которую я анализирую в контексте запроса.
*   **Чистая архитектура (8/10)**: Я знаю принципы SOLID, Dependency Inversion, Hexagonal Architecture и могу структурировать код так, чтобы ядро было независимым от фреймворков, но реализация всегда балансирует между чистотой и практической полезностью.
*   **Тестирование (9/10)**: Я умею писать unit-тесты (PHPUnit, Pest), интеграционные, функциональные тесты, знаю паттерны (Mocking, Stubs, Test Doubles) и принципы TDD, но качество тестов зависит от четкости заданных сценариев.
*   **Git (10/10)**: Я знаю все основные команды, умею разрешать конфликты, создавать сложные коммиты, работать с branches, rebases, cherry-picks и писать понятные commit messages.

Если хотите, мы можем перейти к обсуждению конкретной задачи или архитектуры вашего проекта.

> ⏱ 8.5s · prompt 89 tok · output 676 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

В контексте написания **чистого и надёжного кода** (Clean Code) я выделяю четыре ключевые возможности PHP 8.0–8.3, которые напрямую влияют на читаемость, предсказуемость и отказоустойчивость системы:

### 1. `readonly`-свойства и immutable-объекты (PHP 8.1+)
Это, пожалуй, самый важный шаг к чистоте. Невозможность случайно изменить состояние объекта после создания устраняет целый класс багов (side effects), упрощает отладку и делает код безопасным для многопоточных/асинхронных сценариев.

**До (PHP 7.4):**
```php
class Money {
    public float $amount;
    public string $currency;

    public function __construct(float $amount, string $currency) {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    // Кто-то может случайно сделать $money->amount = 0; и сломать логику
}
```

**После (PHP 8.2 — `readonly` в конструкторе):**
```php
class Money {
    // Свойства могут быть изменены ТОЛЬКО один раз — в конструкторе
    public function __construct(
        public readonly float $amount,
        public readonly string $currency
    ) {}
    
    // Попытка $money->amount = 0; вызовет Error: Cannot modify readonly property
}
```
> **Почему это важно:** Объекты становятся предсказуемыми. Вы можете передавать их везде, не переживая, что другой компонент системы изменит их состояние.

### 2. Enums (PHP 8.1)
Замена «магических чисел» и строк реальными перечислимыми типами. Это улучшает типизацию, автодополнение в IDE и предотвращает передачу невалидных значений.

**До:**
```php
class OrderStatus {
    public const PENDING = 'pending';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
}

function canCancel(string $status): bool {
    // Легко передать 'PENDNING' с ошибкой, а функция просто вернет false или упадет
    return $status === OrderStatus::PENDING;
}
```

**После:**
```php
enum OrderStatus: string {
    case PENDING = 'pending';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public function canCancel(): bool {
        return $this === self::PENDING; // Читаемо и безопасно
    }

    public static function fromString(string $value): self {
        return self::from($value); // Автогенерация метода из бэкенда
    }
}

function canCancel(OrderStatus $status): bool {
    return $status->canCancel();
}
```
> **Почему это важно:** Ошибки обнаруживаются на этапе компиляции/типопроверки, а не в рантайме. Код самодокументирует допустимые значения.

### 3. First-class Callable Syntax (PHP 8.1)
Устраняет громоздкую синтаксическую конструкцию `fn() => $obj->method()`, что делает код более читаемым и ближе к идеалу «кода, который читается как проза».

**До:**
```php
$users = $userRepo->findMany();
$names = array_map(fn($user) => $user->getName(), $users);
$emails = array_map(fn($user) => $user->getEmail(), $users);
```

**После:**
```php
$users = $userRepo->findMany();

// Читаемо и компактно
$names = array_map(User::class.'getName', $users); // Ошибка! Так нельзя.
// Правильно:
$names = array_map($users->getName(...), $users); // Тоже странно.

// Реальный выигрыш при передаче методов как аргументов:
class PaymentService {
    public function __construct(private Logger $logger) {}

    public function charge(Payment $payment): void {
        $this->logger->info('Charging', ['payment' => $payment->id]);
        // ...
    }
}

$payments = [/* ... */];
// До: array_map(fn($p) => $service->charge($p), $payments);
// После: array_map($service->charge(...), $payments);
```
> **Почему это важно:** Уменьшение шумового кода (noise code). Меньше скобок и анонимных функций — легче воспринимать на глаз.

### 4. Attributes (PHP 8.0)
Замена аннотаций и магии рефлексии структурированными данными. Это особенно важно в связке с фреймворками (Symfony, Laravel) для декларативного описания поведения.

**До (аннотации/кастомные парсеры):**
```php
/** @Route("/orders", name="orders_index") */
class OrderController { ... }
```

**После:**
```php
#[Route('/orders', name: 'orders_index')]
class OrderController { ... }

// Или в своих доменных объектах:
#[Assert\Email]
#[Assert\NotBlank]
public ?string $email;
```
> **Почему это важно:** Атрибуты — это полноценные объекты PHP. Их можно типизировать, наследовать и использовать в IDE с автодополнением. Это убирает магию строк и делает код более безопасным.

---

### Бонус: `Never` type и `Fiber` (PHP 8.2/8.1)
*   **`never`**: Показывает, что функция никогда не возвращает значение (всегда выбрасывает исключение или завершает скрипт). Это помогает статическим анализаторам (Psalm, PHPStan) понимать поток выполнения.
*   **`Fiber`**: Позволяет писать асинхронный код синхронным стилем (без callback hell). Хотя в чистом PHP он редко используется напрямую, он лежит в основе Swoole, ReactPHP и современных async-фреймворков.

### Итог
Если выбирать **топ-3** для чистой архитектуры, я бы взял:
1. **`readonly` + Constructor Property Promotion** — для неизменяемости.
2. **Enums** — для типобезопасности доменных сущностей.
3. **Attributes** — для декларативности и снижения магического кода.

Хотите, чтобы я показал, как эти фичи можно совместить в одном доменном классе (например, `Money` или `User`), чтобы видеть полный пайплайн современной PHP-разработки?

> ⏱ 11.6s · prompt 824 tok · output 1559 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это одна из самых спорных частей языка. Она эволюционировала от «слабой, динамической, прощающей» к «строгой, статически анализируемой, но всё ещё гибкой». Понимание её внутренних механик критически важно для написания надёжного кода.

### 1. Что такое `strict_types` и как он работает?

`declare(strict_types=1);` — это **ключевое слово области видимости**, а не глобальный флаг.

**Важнейшее правило:** Оно влияет только на вызовы функций **из файла, где объявлено**, а не на сами функции.

```php
// file_a.php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// file_b.php (НЕ содержит strict_types)
require 'file_a.php';
add(1, '2'); // РАБОТАЕТ, так как strict_types объявлен в file_a.php, 
             // но вызов происходит из file_b.php? 
// НЕТ, это важный нюанс: strict_types=1 в file_a.php означает, 
// что ВЫЗОВЫ add() ИЗ file_a.php будут строгими.
// Но если мы вызываем add() из file_b.php (без strict), то приведение разрешено.
```

**Практический смысл:**
*   **`strict_types=1`**: Запрещает **принудительное приведение типов при передаче аргументов** в функции из этого файла.
*   **Не запрещает**: Приведение типов внутри тела функции, приведение в выражениях, приведение при возвращении значения (если оно совместимо).

> **Миф:** `strict_types` делает код «полностью статически типизированным». **Ложь.** PHP остаётся динамически типизированным языком. `strict_types` только ограничивает поведение при передаче аргументов.

### 2. Как ведут себя приведения типов (Coercion)?

PHP имеет две основные модели приведения:

#### A. Weak Type Mode (по умолчанию, если нет `strict_types`)
PHP пытается «угадать» намерение пользователя и приводит тип.
```php
echo "5" + 3;      // 8 (строка приводится к int)
echo "5" . 3;      // "53" (число приводится к строке)
echo true == "1";  // true (bool приводится к int)
echo "" == 0;      // true (строка приводится к int) — ХАРАКТЕРНАЯ ЛОВУШКА PHP 7
echo "" == false;  // true (строка приводится к bool)
```

#### B. Strict Type Mode (`strict_types=1`)
Запрещает приведение **только при передаче аргументов**.
```php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

add(1, '2'); // TypeError: add(): Argument #2 ($b) must be of type int, string given
add(1, 2);   // ОК
```

### 3. Типичные ловушки на практике

#### Ловушка 1: Сравнение строк с числами (PHP < 8.0 vs PHP >= 8.0)
В PHP 7 (и даже в начале PHP 8) сравнение нечисленных строк с нулём давало неожиданные результаты.

**PHP 7:**
```php
"" == 0;      // true ("" приводится к 0)
"a" == 0;     // true (в PHP 7! "a" приводится к 0, потому что начинается с буквы)
"123abc" == 123; // true ("123abc" приводится к 123)
```

**PHP 8 (изменено для безопасности):**
```php
"" == 0;      // false (сравнение строки с числом теперь строково-числовое)
"a" == 0;     // false
"123abc" == 123; // false (только если строка полностью числовая)
"123" == 123;    // true
```
> **Почему это важно:** Логика `if ($password)`, где `$password` может быть `"0"`, `"a"` или `""`, вела себя непредсказуемо в PHP 7.

#### Ловушка 2: `empty()` и строгость
```php
empty(0);       // true
empty("0");     // true
empty("");      // true
empty(false);   // true
empty(null);    // true
empty([]);      // true
```
Но `empty` **не** проверяет `strict_types`. Он всегда работает в weak-режиме.

#### Ловушка 3: Возврат типа не проверяется `strict_types` при `return`
```php
declare(strict_types=1);

function getNumber(): int {
    return "123"; // ОК! Строка приводится к int, так как strict_types 
                  // не применяется к возвращению значения из функции,
                  // где оно объявлено? 
                  // НЕТ, это ошибка. strict_types НЕ влияет на return.
}
```
**Правильно:** `strict_types` влияет ТОЛЬКО на передачу аргументов. Возвращение `"123"` из функции с типом `int` будет работать и в strict, и в weak режиме, так как приведение при возвращении всегда разрешено (если типы совместимы).

### 4. Где типизация спасает

#### Пример 1: Защита от невалидных данных
```php
class Money {
    public function __construct(
        private int $amount, // Строго int
        private string $currency // Строго string
    ) {}
}

// При создании:
new Money(100, "USD"); // ОК
new Money("100", "USD"); // TypeError в strict-файле, иначе работает
new Money(100, 5); // TypeError всегда (string vs int несовместимы)
```

#### Пример 2: Статический анализ (PHPStan/Psalm)
```php
function process(array $items): void {
    foreach ($items as $item) {
        // Если $items = ['a' => 'b'], то $item может быть mixed.
        // Но если мы знаем, что $item всегда User, можно типизировать:
    }
}

// С generics (PHPStan):
/**
 * @param array<int, User> $users
 */
function process(array $users): void {
    foreach ($users as $user) {
        $user->getName(); // PHPStan знает, что $user — User, а не string
    }
}
```

### 5. Где типизация удивляет

#### Пример 1: `true` == `1`
```php
true == 1;    // true
"1" == true;  // true (в PHP 7)
"abc" == true; // false (в PHP 7: "abc" -> 0, 0 == 1 -> false)
```

#### Пример 2: Нулевая строка `""`
```php
"" + 5;    // 5 ("" приводится к 0)
"" * 5;    // 0
"" / 1;    // 0
```

#### Пример 3: `null` и `false`
```php
null == false; // true
null == 0;     // true
null == "";    // true
```
> **Правило:** Всегда используйте `===` и `!==` для сравнения значений, если не уверены в типе.

### Рекомендации для чистого кода

1. **Всегда используйте `declare(strict_types=1);` в каждом PHP-файле.**
2. **Избегайте неявных приведений.** Явно приводите типы: `(int)$string`.
3. **Используйте `===` для сравнений.**
4. **Валидируйте входные данные** на границах системы (контроллеры, API-эндпоинты), а не внутри доменных объектов.
5. **Используйте статический анализатор** (PHPStan/Psalm) на высоком уровне — он поймает 90% проблем с типами до рантайма.
6. **Избегайте `mixed` там, где это возможно.** Используйте конкретные типы или union types (`string|int`).

Хотите, чтобы я показал, как настраивать PHPStan для максимальной строгости типов в вашем проекте?

> ⏱ 14.8s · prompt 2445 tok · output 1982 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование системы исключений — это не просто «полезла ошибка, кинули Exception». В чистой архитектуре исключения — это **язык коммуникации между слоями** и часть **публичного API** вашего кода. Если вы правильно спроектируете иерархию, ваш код станет предсказуемым, тестируемым и самодокументируемым.

Вот как я подхожу к проектированию исключений в современных PHP-приложениях (особенно с Symfony, DDD):

---

### 1. Философия: Исключения как часть контракта

**Ключевой принцип:** Исключение — это не «сбой работы», а **ожидаемое состояние**, которое нарушает поток выполнения.

**Контракт метода** включает:
1. **Входные данные** (аргументы).
2. **Выходные данные** (возвращаемое значение).
3. **Побочные эффекты** (запись в БД, HTTP-запрос).
4. **Возможные исключения** (какие именно, и в каких условиях).

> **Плохой контракт:** `function getUser(string $id): User` — кидает `Exception` при любой ошибке.
> **Хороший контракт:** `function getUser(string $id): User` — кидает `UserNotFoundException` если пользователя нет, `InvalidUserIdException` если id невалидный.

---

### 2. Проектирование иерархии исключений

Я разделяю исключения на **доменные** (Business Logic) и **системные** (Infrastructure).

#### А. Базовый класс
```php
// Common/Exception/DomainException.php
abstract class DomainException extends \Exception
{
    // Можно добавить контекст, user-friendly message, http status code
    protected array $context = [];

    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

#### Б. Специфические доменные исключения
```php
// Domain/Exception/UserNotFoundException.php
class UserNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self("User with id {$id} was not found.");
    }
}

// Domain/Exception/InsufficientFundsException.php
class InsufficientFundsException extends DomainException
{
    public function __construct(private float $available, private float $requested)
    {
        $msg = sprintf('Insufficient funds. Available: %.2f, Requested: %.2f', $available, $requested);
        parent::__construct($msg);
    }
}
```

#### В. Системные исключения (обёртки)
Никогда не кидайте `Doctrine\DBAL\Exception` или `GuzzleHttp\Exception` из доменного слоя.

```php
// Infrastructure/Exception/DatabaseException.php
class DatabaseException extends \RuntimeException
{
    // Обёртка над любыми БД-ошибками
}

// Infrastructure/Exception/ExternalApiException.php
class ExternalApiException extends \RuntimeException
{
    public function __construct(string $service, int $statusCode, string $message, ?\Throwable $previous = null)
    {
        $msg = sprintf('Error calling %s (HTTP %d): %s', $service, $statusCode, $message);
        parent::__construct($msg, $statusCode, $previous);
    }
}
```

---

### 3. Оборачивание чужих исключений (Wrapping)

**Правило:** Каждый слой знает только про исключения **своего** слоя.

#### Пример: Доменный слой
```php
class UserRepository
{
    public function __construct(private Connection $db) {} // Абстракция БД

    public function findById(string $id): User
    {
        // НО: Домен не знает про БД! Это нарушение DDD.
        // Правильно: UserRepository — это интерфейс в домене,
        // реализация в инфраструктуре.
    }
}
```

**Правильный подход (DDD):**

**Домен (Interface):**
```php
// Domain/Repository/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function findById(string $id): User; // Кидает UserNotFoundException
}
```

**Инфраструктура (Реализация):**
```php
// Infrastructure/Repository/DoctrineUserRepository.php
class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findById(string $id): User
    {
        try {
            $user = $this->em->find(User::class, $id);
        } catch (DoctrineException $e) {
            // Оборачиваем системное исключение в доменное? 
            // НЕТ. Это инфраструктурная ошибка, не доменная.
            throw new DatabaseException('Failed to load user', 0, $e);
        }

        if ($user === null) {
            throw UserNotFoundException::withId($id); // Доменное исключение
        }

        return $user;
    }
}
```

**Почему так?**
- `UserNotFoundException` — это **бизнес-правило**: «пользователь не найден».
- `DatabaseException` — это **техническая проблема**: «БД упала».
- Клиент (Service/Controller) может ловить `UserNotFoundException` и показать пользователю «Пользователь не найден», а `DatabaseException` — скрывать от пользователя и логировать как 500 Internal Server Error.

---

### 4. Антипаттерны в работе с исключениями

#### ❌ Антипаттерн 1: «Ловить и проглатывать» (Silent Failure)
```php
try {
    $this->externalApi->call();
} catch (\Exception $e) {
    // Ничего не делаем? Логгируем, но продолжаем?
    return null; 
}
```
**Почему плохо:** Скрытие ошибок приводит к каскадным сбоям. `null` вместо объекта нарушает контракт.

**Как исправить:** Либо кидать исключение дальше, либо возвращать **результат** (Result Pattern), либо явно обрабатывать с retry/log.

#### ❌ Антипаттерн 2: Использование исключений для обычного потока управления
```php
try {
    return $this->getPriceForProduct($id);
} catch (ProductNotFoundException $e) {
    return $this->getDefaultPrice();
}
```
**Почему плохо:** Исключения должны быть **редкими**. Если «товар не найден» — это нормальное состояние, а не ошибка.

**Как исправить:**
```php
// Вариант 1: Nullable
public function getPriceForProduct(string $id): ?Price
{
    $product = $this->repo->find($id);
    return $product?->price;
}

// Вариант 2: Result Object (Functional PHP)
public function getPriceForProduct(string $id): Result<Price, ProductNotFound>
{
    // ...
}
```

#### ❌ Антипаттерн 3: Кидание конкретных классов исключений из публичного API
```php
public function getUser(string $id): User
{
    // Внутри: Doctrine\DBAL\Exception\UniqueConstraintViolationException
    // Если вы кидаете его наружу, вы привязываете клиента к Doctrine!
}
```
**Почему плохо:** Нарушение инверсии зависимостей. Клиент должен знать только про **доменные** или **общие** исключения.

**Как исправить:** Оборачивать в `DomainException` или `ApiException`.

#### ❌ Антипаттерн 4: Отсутствие контекста в исключении
```php
throw new \Exception('Error');
```
**Почему плохо:** Невозможно отладить. Что за ошибка? Где?

**Как исправить:**
```php
throw new \Exception("Failed to load user '{$id}'. Cause: " . $previousException->getMessage(), 0, $previousException);
```

#### ❌ Антипаттерн 5: Ловля `\Throwable` или `\Exception` слишком высоко
```php
catch (\Exception $e) {
    // Логгируем и возвращаем 500
}
```
**Почему плохо:** Вы ловите и `UserNotFoundException`, и `DatabaseException` одинаково.

**Как исправить:** Ловить **конкретные** исключения там, где их можно осмысленно обработать (Controller, API Handler).

---

### 5. Лучшая практика: Структура обработки исключений в Symfony

1. **Доменные исключения** (`DomainException`, `UserNotFoundException`) — кидаются из доменного слоя.
2. **Инфраструктурные исключения** (`DatabaseException`, `ApiException`) — кидаются из инфраструктурного слоя, оборачивая системные ошибки.
3. **Обработчик в контроллере/ApiListener:**
   ```php
   try {
       $user = $this->userService->getUser($id);
   } catch (UserNotFoundException $e) {
       throw new NotFoundHttpException('User not found'); // 404
   } catch (DatabaseException $e) {
       throw new InternalServerErrorHttpException('Internal error', previous: $e); // 500
   }
   ```
4. **Глобальный ExceptionListener** (для непойманных исключений):
   - Логирует полный stack trace.
   - Возвращает JSON `{"error": "Internal Server Error"}` (без деталей).
   - Для `DomainException` — можно выдать более подробное сообщение.

---

### Итог: Чек-лист проектирования исключений

| Вопрос | Ответ |
|--------|-------|
| Исключение доменное или системное? | Доменное = бизнес-логика. Системное = БД, сеть, файловая система. |
| Клиент знает про этот класс исключений? | Должен знать только про **общие** и **доменные**. |
| Можно ли избежать исключения? | Если это нормальный поток (например, «товар не найден») — используйте `nullable` или `Result`. |
| Есть ли контекст? | Да, id, email, код ошибки. |
| Оборачиваете ли вы чужие исключения? | Да, в своем слое. |
| Ловите ли вы `\Exception` слишком высоко? | Нет, только в корне (HttpKernel/ExceptionListener). |

Хотите, чтобы я показал, как реализовать **Result Pattern** как альтернативу исключениям для бизнес-правил?

> ⏱ 18.0s · prompt 4486 tok · output 2427 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

Если отвечать честно, опираясь на анализ огромного количества кодовой базы, то принцип, который нарушается чаще всего, — это **S (Single Responsibility Principle, Принцип Единой Ответственности)**.

Почему именно он? Потому что:
1.  Он самый абстрактный. Легко понять «О» в SOLID, но трудно определить грань «одной ответственности».
2.  Он не дает мгновенного «болевых ощущения» (код работает), пока не станет катастрофическим.
3.  Нарушение SRP — это корень большинства других нарушений (LSP, ISP), так как классы становятся «богом» (God Object).

**Типовое нарушение SRP в PHP:**

### Сценарий: Класс `User` или `Order`
В реальном коде чаще всего страдает **доменная модель** или **контроллер/сервис**.

#### ❌ Антипаттерн: «Жирный» доменный объект или сервис

```php
class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private PaymentGateway $gateway,
        private LoggerInterface $logger
    ) {}

    public function checkout(array $items, string $userId, string $cardNumber): Order
    {
        // 1. Валидация данных
        if (empty($items)) {
            throw new \Exception('Cart is empty');
        }
        
        // 2. Проверка баланса/платежа
        $paymentResult = $this->gateway->charge($userId, $cardNumber, $this->calculateTotal($items));
        if (!$paymentResult->isSuccess()) {
            throw new \Exception('Payment failed: ' . $paymentResult->getMessage());
        }

        // 3. Создание сущности
        $order = new Order($userId);
        foreach ($items as $item) {
            $order->addProduct($item['id'], $item['qty']);
        }
        $order->setPaymentId($paymentResult->getId());
        $order->setStatus(OrderStatus::PAID);

        // 4. Сохранение в БД
        $this->em->persist($order);
        $this->em->flush();

        // 5. Отправка письма
        $this->mailer->send(
            new Email()
                ->from('noreply@shop.com')
                ->to($order->getUser()->getEmail())
                ->subject('Your order is paid')
                ->html('<h1>Thanks for shopping!</h1><p>Order: ' . $order->getId() . '</p>')
        );

        // 6. Логирование
        $this->logger->info('Order paid', ['order_id' => $order->getId(), 'user_id' => $userId]);

        return $order;
    }

    private function calculateTotal(array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}
```

### Почему это нарушение SRP?

`OrderService` несет на себе **хотя бы 5 разных ответственностей**:
1.  **Оркестрация бизнес-процесса** (checkout flow).
2.  **Валидация входных данных**.
3.  **Интеграция с платежным шлюзом** (знание протокола оплаты).
4.  **Создание и маппинг доменных сущностей** (знание структуры `Order`).
5.  **Интеграция с почтовым сервисом** (знание формата письма).
6.  **Логирование** (знание того, что логируем).

**Последствия:**
*   **Невозможно протестировать.** Чтобы проверить, что заказ создаётся, нужно мокировать `Mailer`, `Gateway`, `Logger`, `EntityManager`. Тест становится хрупким и сложным.
*   **Изменение шаблона письма** требует изменения `OrderService` и его пересборки.
*   **Смена платежного провайдера** требует правки в `OrderService`.
*   **Нарушение LSP и ISP:** Клиенты этого сервиса вынуждены зависеть от всех этих интерфейсов.

---

### ✅ Рефакторинг: Разделение ответственностей

Идея: Каждый класс делает **одну** вещь и делает её **хорошо**.

#### 1. Доменная логика (Чистый PHP, без зависимостей)

Создаем доменную сущность `Order`, которая сама знает, как считать сумму и валидировать себя.

```php
// Domain/Entity/Order.php
class Order
{
    private string $id;
    private string $userId;
    private array $products = [];
    private OrderStatus $status;
    private ?string $paymentId = null;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->status = OrderStatus::PENDING;
        $this->id = self::generateId();
    }

    public function addProduct(string $productId, int $qty, int $price): void
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
        $this->products[] = [
            'id' => $productId,
            'qty' => $qty,
            'price' => $price
        ];
    }

    public function markAsPaid(string $paymentId): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new \DomainException('Order is already processed');
        }
        $this->paymentId = $paymentId;
        $this->status = OrderStatus::PAID;
    }

    public function getTotal(): int
    {
        return array_sum(array_map(fn($p) => $p['price'] * $p['qty'], $this->products));
    }

    // Геттеры...
    public function getUserId(): string { return $this->userId; }
    public function getId(): string { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    
    public static function generateId(): string {
        return 'ORD-' . substr(md5(uniqid()), 0, 8);
    }
}
```

#### 2. Инфраструктурные сервисы (Отдельные классы)

```php
// Infrastructure/Payment/PaymentGatewayInterface.php
interface PaymentGatewayInterface
{
    public function charge(string $userId, string $cardNumber, int $amount): PaymentResult;
}

// Infrastructure/Mail/OrderEmailService.php
class OrderEmailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendConfirmation(Order $order, string $recipientEmail): void
    {
        $this->mailer->send(
            new Email()
                ->from('noreply@shop.com')
                ->to($recipientEmail)
                ->subject('Your order is paid')
                ->html('<h1>Thanks for shopping!</h1><p>Order: ' . $order->getId() . '</p>')
        );
    }
}
```

#### 3. Оркестратор (Тонкий слой)

`OrderService` теперь только **связывает** эти части. Он не знает, как считается сумма (делает `Order`), не знает, как отправляется письмо (делает `OrderEmailService`), не знает, как работает платеж (делает `PaymentGateway`).

```php
// Application/Service/OrderCheckoutService.php
class OrderCheckoutService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentGatewayInterface $paymentGateway,
        private OrderEmailService $emailService,
        private LoggerInterface $logger
    ) {}

    public function checkout(array $items, string $userId, string $cardNumber, string $userEmail): Order
    {
        // 1. Валидация входа (можно вынести в отдельный Validator)
        if (empty($items)) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        // 2. Создание доменной сущности
        $order = new Order($userId);
        foreach ($items as $item) {
            $order->addProduct($item['id'], $item['qty'], $item['price']);
        }

        // 3. Оплата (Внешний сервис)
        $amount = $order->getTotal();
        $result = $this->paymentGateway->charge($userId, $cardNumber, $amount);
        
        if (!$result->isSuccess()) {
            throw new PaymentFailedException($result->getMessage());
        }

        // 4. Обновление домена
        $order->markAsPaid($result->getPaymentId());

        // 5. Сохранение
        $this->em->persist($order);
        $this->em->flush();

        // 6. Письмо (Внешний сервис)
        $this->emailService->sendConfirmation($order, $userEmail);

        // 7. Логирование
        $this->logger->info('Order paid', ['order_id' => $order->getId()]);

        return $order;
    }
}
```

### Что изменилось?

| Критерий | До (SRP violation) | После (SRP adhered) |
| :--- | :--- | :--- |
| **Тестируемость** | Нужны моки для Mailer, Gateway, DB, Logger. | `Order` тестируется без зависимостей. `Service` тестируется с моками, но логика `Order` уже проверена отдельно. |
| **Изменение письма** | Правим `OrderService`. | Правим `OrderEmailService`. |
| **Изменение логики цены** | Правим `OrderService`. | Правим `Order` (домен). |
| **Смена провайдера** | Правим `OrderService`. | Пишем новый `PaymentGateway`. |
| **Читаемость** | Один большой метод на 50 строк. | Методы короткие, каждый делает одну вещь. |

### Ключевой вывод

**SRP не про «один метод = один класс».**  
SRP про **«одна причина для изменения»** (One Reason to Change).

*   Если меняется логика расчёта стоимости — меняется `Order`.
*   Если меняется шаблон письма — меняется `OrderEmailService`.
*   Если меняется провайдер оплаты — меняется `PaymentGateway`.
*   Если меняется порядок шагов checkout — меняется `OrderCheckoutService`.

Если эти изменения не требуют правки *одного и того же* класса, значит, SRP соблюдено.

Хотите рассмотреть, как этот же подход применим к **контроллерам** в Symfony, чтобы избежать «жирных» контроллеров?

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно и нужно. Более того, **неуместное следование SOLID в простых случаях само по себе является антипаттерном**, который часто называют «преждевременной оптимизацией» или «паразитной абстракцией».

СOLID — это **набор эвристик для решения конкретных проблем** (сложность, изменяемость, зависимость), а не догмы. Если проблема отсутствует, применение принципа увеличивает сложность кода, усложняет навигацию и отладку без какой-либо пользы.

### Главный критерий оправданного нарушения
Нарушение оправдано, если:
1. **Проблема, которую решает принцип, не существует** (например, у вас только одна реализация и она не будет меняться).
2. **Цена соблюдения принципа выше его пользы** (дополнительные классы, интерфейсы, уровни инкапсуляции, которые усложняют понимание кода).

---

### Пример 1: Нарушение ISP (Interface Segregation Principle)

**Контекст:** У вас есть простой консольный скрипт или внутренний микросервис, где нужно отправлять логи.

**«Идеальное» применение ISP:**
Создать интерфейс `Logger`, а затем `FileLogger`, `DbLogger`, `EmailLogger`, `SentryLogger`. Внедрять `Logger` через DI-контейнер.

**Оправданное нарушение:**
```php
// Logger.php
class Logger
{
    public function info(string $message): void
    {
        // Просто пишем в файл или echo
        file_put_contents('app.log', $message . PHP_EOL, FILE_APPEND);
    }
    
    public function error(string $message): void
    {
        file_put_contents('errors.log', $message . PHP_EOL, FILE_APPEND);
    }
}
```

**Почему это оправдано?**
*   **Нет смены реализации:** Если вы пишете скрипт, который должен работать «здесь и сейчас», и логирование в файл — единственное требование, то интерфейс `LoggerInterface` и абстракция `FileLogger` — это шум.
*   **Простота:** Один класс `Logger` легче прочитать и отлаживать.
*   **Rationale:** ISP требует, чтобы клиент не зависел от методов, которые он не использует. Но если у клиента (вашего скрипта) нет альтернативного логгера, то создание интерфейса не дает никакой гибкости, а только добавляет слой.

> **Когда это станет плохим?** Если завтра вам нужно логировать в Sentry, вы переписываете `Logger` или добавляете наследование. Если вы знаете, что логирование будет меняться, — тогда ISP обязателен. Если нет — нарушение оправдано.

---

### Пример 2: Нарушение D (Dependency Inversion Principle)

**Контекст:** Простой доменный объект, который использует конкретную утилиту.

**«Идеальное» применение D:**
```php
// Interface
interface RandomStringGeneratorInterface
{
    public function generate(int $length): string;
}

// Implementation
class RandomStringGenerator implements RandomStringGeneratorInterface
{
    public function generate(int $length): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, $length);
    }
}

// Consumer
class Order
{
    public function __construct(
        private RandomStringGeneratorInterface $generator
    ) {}

    public function generateId(): string
    {
        return 'ORD-' . $this->generator->generate(8);
    }
}
```

**Оправданное нарушение:**
```php
class Order
{
    public function __construct(
        private \Ramsey\Uuid\UuidFactory $uuidFactory // Конкретный класс
    ) {}

    public function generateId(): string
    {
        return 'ORD-' . (string) $this->uuidFactory->v4();
    }
}
```

**Почему это оправдано?**
*   **UuidFactory — это конкретная реализация, которая является стандартом де-факто.** Вы не будете менять её на `MyCustomUuidGenerator`, потому что UUID — это протокол, а не бизнес-логика.
*   **Инкапсуляция не нужна:** `UuidFactory` не содержит сложной бизнес-логики, которую нужно тестировать или заменять.
*   **Читаемость:** Разработчик видит `UuidFactory` и сразу понимает, что будет сгенерирован UUID. Если бы был `IdGeneratorInterface`, он бы задал вопрос: «А какой именно генератор?».

> **Когда это станет плохим?** Если вы хотите тестировать `Order` без внешнего вызова генерации UUID (например, чтобы гарантировать детерминированный ID в тестах). Тогда D обязателен.

---

### Пример 3: Нарушение SRP (Single Responsibility Principle)

**Контекст:** Внутренний `DTO` (Data Transfer Object) или `Value Object`.

**«Идеальное» применение SRP:**
Создать отдельный класс `EmailValidator`, `PasswordValidator`, `PhoneValidator`, а потом `UserValidator`, который их использует.

**Оправданное нарушение:**
```php
class UserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $name
    ) {
        // Валидация прямо в конструкторе
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        
        if (strlen($this->password) < 8) {
            throw new \InvalidArgumentException('Password too short');
        }
    }
}
```

**Почему это оправдано?**
*   **DTO не имеет поведения:** Валидация — это часть его «создания». Выделение валидаторов в отдельные классы для простого DTO — это переусложнение.
*   **Связность:** Валидация поля `email` логически связана с самим объектом `UserDTO`. Она должна быть рядом.
*   **Rationale:** SRP говорит, что у класса должна быть одна ответственность. Но «валидация своих полей» — это часть ответственности «быть валидным UserDTO». Это не две разные обязанности, а одна: «представлять корректные данные пользователя».

> **Когда это станет плохим?** Если правила валидации становятся сложными (например, «email должен быть @domain.com, кроме gmail.com, если возраст > 18»). Тогда валидаторы выносятся отдельно.

---

### Главный вывод: SOLID — это инструмент, а не закон

| Принцип | Когда соблюдать | Когда можно нарушать |
| :--- | :--- | :--- |
| **S** | Когда класс делает больше одной логической вещи (например, и сохраняет, и отсылает письма). | Когда класс — простой DTO или Value Object, и логика его создания/валидации естественна. |
| **O** | Когда вы знаете, что логика будет расширяться (например, новые типы оплат). | Когда у вас есть фиксированный набор вариантов и они не меняются (например, 2 типа валют). |
| **L** | Когда клиенты зависят от абстракции, и вы хотите подставлять разные реализации. | Когда реализация уникальна и не планируется замена (например, конкретный `UuidGenerator`). |
| **I** | Когда интерфейс большой, и клиенты используют только часть методов. | Когда интерфейс маленький (2-3 метода) и все методы используются. |
| **D** | Когда вы хотите тестировать код без внешних зависимостей или менять реализации. | Когда зависимость — это стандартная утилита (UUID, время, случайные числа), и вы не планируете её заменять. |

### Практический совет

**Начинайте с простого.** Пишите код без абстракций. Если вы обнаружите:
1.  Дублирование кода.
2.  Потребность в замене реализации.
3.  Сложность тестирования.
4.  Изменение кода по двум разным причинам.

Тогда применяйте SOLID **локально**, чтобы решить конкретную проблему. Не стройте архитектуру «впрок» — это почти всегда плохо.

Хотите, чтобы я показал, как распознать, когда абстракция становится «паразитной», а когда она оправдана?

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Конечно. ОCP (Open/Closed Principle) — «открыт для расширения, закрыт для модификации» — часто ошибочно ассоциируют только с наследованием (`extends`) и полиморфизмом. Но в современном PHP, особенно с развитием функциональных подходов и композиций, **наследование — самый хрупкий способ реализации OCP**.

Вот 5 мощных альтернатив, которые часто чище, гибче и проще в поддержке.

---

### 1. Стратегия с интерфейсом (Composition over Inheritance)
Это классический паттерн, но он не требует наследования, только **инъекцию реализации**.

**Проблема:** Нужно отправлять уведомления разными способами (Email, SMS, Push).

**❌ Наследование (хрупкое):**
```php
class Notification {
    public function send(User $user, string $message): void {
        // Логика выбора типа уведомления if-else или switch
        // Или наследование: EmailNotification extends Notification
    }
}
```

**✅ Композиция через интерфейс:**
```php
interface NotificationChannel {
    public function send(User $user, string $message): void;
}

class EmailChannel implements NotificationChannel {
    public function send(User $user, string $message): void {
        // Логика отправки на почту
    }
}

class SmsChannel implements NotificationChannel {
    public function send(User $user, string $message): void {
        // Логика отправки SMS
    }
}

class NotificationService {
    public function __construct(private array $channels) {} // Массив каналов

    public function send(User $user, string $message): void {
        foreach ($this->channels as $channel) {
            $channel->send($user, $message);
        }
    }
}
```
**Почему это OCP?**
*   **Закрыт для модификации:** `NotificationService` не меняется.
*   **Открыт для расширения:** Добавляете `TelegramChannel implements NotificationChannel` и добавляете его в массив `channels`. Никаких `if/else` в `NotificationService`.

---

### 2. Фабрика/Регистр (Factory/Registry Pattern)
Когда нужно создавать объекты разных типов на основе данных извне (например, типа заказа).

**Проблема:** Нужно обрабатывать разные типы платежей: `Card`, `BankTransfer`, `Crypto`.

**✅ Реализация через Фабрику:**
```php
interface PaymentHandler {
    public function process(Order $order): void;
}

class CardPaymentHandler implements PaymentHandler {
    public function process(Order $order): void { /* ... */ }
}

class CryptoPaymentHandler implements PaymentHandler {
    public function process(Order $order): void { /* ... */ }
}

class PaymentHandlerFactory {
    private array $handlers = [];

    public function register(string $type, PaymentHandler $handler): void {
        $this->handlers[$type] = $handler;
    }

    public function create(string $type): PaymentHandler {
        if (!isset($this->handlers[$type])) {
            throw new \InvalidArgumentException("Unknown payment type: {$type}");
        }
        return $this->handlers[$type];
    }
}

// Использование:
$factory = new PaymentHandlerFactory();
$factory->register('card', new CardPaymentHandler());
$factory->register('crypto', new CryptoPaymentHandler());

$handler = $factory->create($order->getPaymentType());
$handler->process($order);
```
**Почему это OCP?**
*   Новый тип платежа? Создайте новый класс `WalletPaymentHandler`, зарегистрируйте его в фабрике. Ключевой код не меняется.

---

### 3. Команда (Command Pattern) + Диспетчер
Отлично подходит для обработки событий, где конкретное действие неизвестно заранее.

**Проблема:** При создании заказа нужно: отправить письмо, начислить баллы, отправить webhook.

**✅ Реализация:**
```php
interface Command {
    public function handle(array $context): void;
}

class SendWelcomeEmailCommand implements Command {
    public function handle(array $context): void { /* ... */ }
}

class AwardPointsCommand implements Command {
    public function handle(array $context): void { /* ... */ }
}

class CommandDispatcher {
    private array $handlers = [];

    public function register(Command $command, string $event): void {
        $this->handlers[$event][] = $command;
    }

    public function dispatch(string $event, array $context): void {
        foreach ($this->handlers[$event] ?? [] as $handler) {
            $handler->handle($context);
        }
    }
}

// Подключение:
$dispatcher = new CommandDispatcher();
$dispatcher->register(new SendWelcomeEmailCommand(), 'order.created');
$dispatcher->register(new AwardPointsCommand(), 'order.created');

$dispatcher->dispatch('order.created', ['order' => $order]);
```
**Почему это OCP?**
*   Добавление нового действия при событии `order.created` не требует изменения `CommandDispatcher`.

---

### 4. Функциональный подход: Цепочки обработчиков (Pipeline)
Вместо классов с логикой — простые функции или closures.

**Проблема:** Обработка изображения: сжать, добавить водяной знак, конвертировать в WebP.

**✅ Реализация:**
```php
type ImageProcessor = \Closure(Image $image): Image;

function createPipeline(array $processors): ImageProcessor {
    return function (Image $image) use ($processors) {
        foreach ($processors as $processor) {
            $image = $processor($image);
        }
        return $image;
    };
}

// Конфигурация пайплайна:
$pipeline = createPipeline([
    fn(Image $img) => $img->resize(800, 600),
    fn(Image $img) => $img->addWatermark('shop.com'),
    fn(Image $img) => $img->convert('webp')
]);

// Новое действие? Добавляем closure в массив. Код пайплайна не меняется.
```
**Почему это OCP?**
*   Максимальная гибкость. Новые шаги — новые функции. Никаких классов, наследования, DI.

---

### 5. Атрибуты + Рефлексия (Attribute-Based)
Современный PHP 8 позволяет использовать атрибуты для декларативного расширения.

**Проблема:** Валидация сущностей разными правилами.

**✅ Реализация:**
```php
#[Attribute]
class Validate {
    public function __construct(private string $rule) {}
    public function getRule(): string { return $this->rule; }
}

class User {
    #[Validate('email')]
    public string $email;

    #[Validate('min:8')]
    public string $password;
}

// Валидатор читает атрибуты через рефлексию:
class Validator {
    public function validate(object $object): void {
        $ref = new \ReflectionObject($object);
        foreach ($ref->getProperties() as $prop) {
            $attributes = $prop->getAttributes(Validate::class);
            foreach ($attributes as $attr) {
                $rule = $attr->newInstance()->getRule();
                // Применяем правило $rule к значению $prop->getValue($object)
            }
        }
    }
}
```
**Почему это OCP?**
*   Добавление нового правила валидации (например, `#[Validate('unique')]`) не требует изменения класса `Validator` (если он достаточно универсален) или модели `User`.

---

### Сравнение подходов

| Подход | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Композиция (Стратегия)** | Когда нужно выбирать один из нескольких вариантов поведения. | Чисто, тестируемо, легко понимать. | Требует создания интерфейса и классов. |
| **Фабрика/Регистр** | Когда тип объекта определяется данными извне (БД, конфиг). | Гибко, легко добавлять новые типы. | Нужно управлять жизненным циклом фабрики. |
| **Команды/Диспетчер** | Обработка событий, pub/sub. | Слабая связь, легко добавлять подписчиков. | Может стать сложно отлаживать, если цепочка длинная. |
| **Пайплайны (Функции)** | Последовательная обработка данных. | Максимальная простота, нет классов. | Менее типобезопасно, сложнее масштабировать. |
| **Атрибуты** | Декларативные метаданные (валидация, роутинг). | Очень современно, декларативно. | Зависит от рефлексии (медленнее), сложнее дебажить. |

### Итог
**Наследование — это инструмент для иерархий.**
**Композиция и функции — это инструменты для вариативности.**

Для OCP **композиция почти всегда лучше наследования**, потому что:
1.  Она не нарушает LSP (нет риска «парадокса наследования»).
2.  Она проще в тестах (легко замочить зависимость).
3.  Она масштабируется лучше (можно комбинировать стратегии, а не только выбирать одну).

Хотите, чтобы я показал, как в Symfony использовать **Dependency Injection** для автоматической реализации любого из этих подходов (например, через Tagged Services)?

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, **SRP (Single Responsibility Principle) не ограничивается только классами**.

Хотя в объектно-ориентированном программировании мы чаще всего говорим о «одной ответственности» для класса, сам по себе принцип — это общая инженерная эвристика для **любой единицы кода**, которая несет в себе логику или данные.

Вот на какие сущности SRP распространяется и как он выглядит в каждом случае:

### 1. Функции и Методы
Это самый частый уровень нарушения SRP в реальном коде.
**Правило:** Одна функция должна делать одну вещь и иметь одну причину для изменения.

**Нарушение:**
```php
public function processOrder(Order $order): void
{
    // 1. Валидация
    if (empty($order->items)) throw new Exception('Empty');
    
    // 2. Расчет суммы
    $total = $this->calculateTotal($order);
    
    // 3. Отправка на почту
    $this->mailer->send($order->user->email, 'Order placed');
    
    // 4. Сохранение в БД
    $this->db->save($order);
}
```
**Почему это плохо?**
*   Если изменится шаблон письма — меняется эта функция.
*   Если изменится логика расчета суммы — меняется эта функция.
*   Если изменится схема БД — меняется эта функция.

**Исправление (SRP на уровне функций):**
```php
public function processOrder(Order $order): void
{
    $this->validator->validate($order);       // Отдельная ответственность
    $order->calculateTotal();                  // Ответственность самой сущности
    $this->notifier->notifyOrderPlaced($order);// Отдельная ответственность
    $this->repository->save($order);           // Отдельная ответственность
}
```
Теперь `processOrder` отвечает только за **оркестрацию** (порядок вызовов).

---

### 2. Модули / Файлы
Файл — это физическая единица кода. Один файл должен иметь одну ответственность.

**Нарушение:**
`app/utils.php`
```php
// В одном файле:
function formatDate($date) {}
function sendEmail($to, $body) {}
function calculateTax($amount) {}
function getDbConnection() {}
```
**Почему это плохо?**
*   Файл становится «свалкой» (God File).
*   Изменение логики формата даты требует пересмотра всего файла.
*   Невозможно легко импортировать только `formatDate` в другой проект.

**Исправление:**
*   `app/Domain/Order.php` — только логика заказа.
*   `app/Infrastructure/Email/EmailSender.php` — только почта.
*   `app/Domain/Date/DateFormatter.php` — только даты.

---

### 3. Базовые типы данных (Structs, DTOs, Enums)
Даже простые структуры данных могут нарушать SRP.

**Нарушение:**
```php
class UserResponse
{
    public string $id;
    public string $email;
    public string $passwordHash; // Осторожно!
    public string $firstName;
    public string $lastName;
    public float $balance; // Это уже финансовая сущность?
    public string $lastLoginIp; // Это лог-информация?
}
```
**Почему это плохо?**
*   `balance` и `lastLoginIp` имеют разный жизненный цикл и разные правила изменения.
*   Если API изменит формат отображения баланса (например, добавит валюту) — это одна причина изменения.
*   Если изменится логирование IP — это другая причина.

**Исправление:**
*   `UserDTO` — только основные данные пользователя.
*   `UserFinancialInfo` — балансы, транзакции.
*   `UserActivityLog` — IP, устройства, время входа.

---

### 4. Микросервисы и Системные компоненты
На архитектурном уровне SRP — это основа **микросервисов**.

**Нарушение:**
Микросервис `UserService`, который:
1.  Хранит данные пользователей.
2.  Отправляет письма.
3.  Интегрируется с CRM.
4.  Управляет сессиями.

**Почему это плохо?**
*   Если почтовый провайдер упадет — упадет весь `UserService`, и пользователи не смогут логиниться.
*   Разное масштабирование: письма отправляются редко, логин часто.

**Исправление:**
*   `UserAuthService` — только логин/регистрация.
*   `NotificationService` — только отправка уведомлений.
*   `ProfileService` — только CRUD профиля.

---

### 5. Переменные и Константы
Даже на самом низком уровне.

**Нарушение:**
```php
$max_value = 100; // Это лимит на страницу? Или лимит на размер файла?
```
**Почему это плохо?**
Если вы переиспользуете `$max_value` в двух разных контекстах, изменение одного контекста сломает другой.

**Исправление:**
```php
const MAX_ITEMS_PER_PAGE = 20;
const MAX_FILE_SIZE_MB = 100;
```

---

### Как формулировать «Ответственность» (Responsibility)?

Чтобы правильно применять SRP, используйте критерий **«Причина для изменения»** (Reason to Change):

> **У объекта (класса, функции, файла) должна быть ровно одна причина для изменения.**

**Вопросы для самопроверки:**
1.  Если изменится логика бизнес-процесса, этот код изменится? (Да/Нет)
2.  Если изменится внешний провайдер (почта, оплата), этот код изменится? (Да/Нет)
3.  Если изменится формат вывода (JSON vs XML), этот код изменится? (Да/Нет)

Если на **любые два** из этих вопросов ответ «Да», то у вас **нарушение SRP**. Этот элемент несет **несколько** причин для изменения.

### Итог
SRP — это **принцип декомпозиции**. Он применим к любой сущности, которая содержит состояние или логику:
*   **Классы** — самая известная цель.
*   **Функции** — самая частая цель в daily coding.
*   **Файлы** — цель для организации проекта.
*   **Сервисы** — цель для архитектуры.

Чистый код — это код, где каждая часть имеет **одну четкую роль**, которую легко объяснить в одном предложении.

> ⏱ 16.7s · prompt 6964 tok · output 2482 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер внедрения зависимостей (DIC) в Symfony — это одна из самых элегантных, но и самых сложных части фреймворка. Он работает на основе принципа **инверсии зависимостей** и использует механизм **компиляции** для создания производительного кода.

Давайте разберем этот процесс на три этапа: компиляция, autowiring и получение сервиса (runtime).

---

### 1. Комппиляция контейнера (Compile Time)

Symfony **не** создает контейнер на лету каждый раз, когда вы делаете запрос. Он создает его **один раз** при деплое или очистке кеша (`bin/console cache:clear`).

**Что происходит:**

1.  **Сканирование (Scanning):**
    *   Symfony находит все классы в вашем приложении (через `App\ServiceLocator`, атрибуты `#[Service]`, конфигурацию в `services.yaml` или `config/services.php`).
    *   Он также сканирует бандлы (Bundle).

2.  **Анализ зависимостей (Dependency Analysis):**
    *   Для каждого найденного класса Symfony читает его **конструктор** (и/или сеттеры).
    *   Он определяет типы аргументов конструктора (например, `EntityManagerInterface`, `MailerInterface`, `MyCustomService`).

3.  **Разрешение зависимостей (Resolution):**
    *   Symfony пытается сопоставить каждый тип аргумента с доступными службами.
    *   Если он находит ровно один кандидат — всё хорошо.
    *   Если находит несколько — использует **Алгоритм выбора** (см. Autowiring ниже).
    *   Если не находит — бросает ошибку компиляции (`Autowiring failed`).

4.  **Генерация кода (Code Generation):**
    *   Symfony генерирует **PHP-класс** `Container...` в кеше (`var/cache/dev/`).
    *   Этот класс содержит **методы-фабрики** для каждого сервиса.
    *   **Важно:** Это чистый PHP-код, без рефлексии в рантайме (для производительности).

**Пример сгенерированного кода (упрощенно):**

```php
// var/cache/dev/srcApp_KernelDevDebugContainer.php

public function getEmailService(): \App\Service\EmailService
{
    return $this->services['App\Service\EmailService'] ??= new \App\Service\EmailService(
        $this->getMailer(), // Зависимость разрешена
        $this->getLogger()  // Зависимость разрешена
    );
}
```

---

### 2. Autowiring (Автонастраивание)

**Autowiring** — это механизм, который позволяет Symfony автоматически находить правильные зависимости для конструкторов, если вы не указали их явно в конфигурации.

**Как это работает:**

1.  **Типизация:** Symfony смотрит на типы параметров конструктора.
    ```php
    class EmailService {
        public function __construct(
            private MailerInterface $mailer, // Ищет сервис типа MailerInterface
            private LoggerInterface $logger  // Ищет сервис типа LoggerInterface
        ) {}
    }
    ```

2.  **Сопоставление с бинами (Aliases):**
    *   Symfony имеет карту **аласов** (aliases). Например, `MailerInterface` может быть алиасом на `Symfony\Component\Mailer\Mailer`.
    *   Если вы имплементируете интерфейс, Symfony ищет сервис, который реализует этот интерфейс.

3.  **Разрешение конфликтов:**
    *   Если несколько сервисов реализуют `MailerInterface`, Symfony использует **приоритеты**:
        1.  Явно указанный `@service_id` в конфигурации.
        2.  Сервис с `public: true` (в Symfony 6+ сервисы private по умолчанию, но autowiring все равно работает).
        3.  Если все равно неясно — ошибка `Autowiring failed`.

4.  **Атрибуты (Attributes):**
    *   В Symfony 6+ можно использовать атрибуты для уточнения:
        ```php
        public function __construct(
            #[Autowire(service: 'special.mailer')]
            private MailerInterface $mailer
        ) {}
        ```

**Важно:** Autowiring работает **только для конструкторов** (и сеттеров). Он не работает для обычных свойств класса.

---

### 3. Момент запроса сервиса (Runtime)

Когда вы делаете `$this->get('App\Service\EmailService')` или `$this->mailer` в контроллере, происходит следующее:

1.  **Получение Container:**
    *   Symfony получает экземпляр контейнера (один на все приложение).

2.  **Поиск метода-фабрики:**
    *   Контейнер ищет метод `getEmailService()` (или `getApp_Service_EmailService()`).

3.  **Lazy Loading (Ленивое создание):**
    *   Если сервис **еще не создан** в этом запросе — вызывается метод-фабрика.
    *   Метод-фабрика создает новый экземпляр класса и передаст в конструктор **уже созданные** (или только что созданные) зависимости.

4.  **Кеширование в Request:**
    *   Созданный сервис хранится в памяти контейнера для текущего HTTP-запроса.
    *   Если вы запросите его снова в том же запросе — будет возвращен **тот же самый объект** (Singleton по умолчанию).

5.  **Private Services:**
    *   Если сервис объявлен как `private` (по умолчанию в Symfony 5+), он будет **удален из памяти** после того, как все зависимые от него сервисы будут уничтожены (или после запроса).
    *   Это экономит память.

**Пример жизненного цикла:**

```php
// 1. Контроллер запрашивает OrderService
$container->get(OrderService::class);

// 2. Container вызывает метод-фабрику:
// public function getOrderService(): OrderService
// {
//     return $this->services['OrderService'] ??= new OrderService(
//         $this->getUserRepository(), // Создано или взято из кеша
//         $this->getPaymentGateway()  // Создано или взято из кеша
//     );
// }

// 3. Если UserRepository еще не создан:
// public function getUserRepository(): UserRepository
// {
//     return $this->services['UserRepository'] ??= new UserRepository(
//         $this->getEntityManager() // ...
//     );
// }

// 4. Все созданные сервисы хранятся в массиве $this->services
//    до конца HTTP-запроса.
```

---

### Ключевые особенности Symfony DIC

| Характеристика | Описание |
| :--- | :--- |
| **Singleton по умолчанию** | Сервис создается один раз на HTTP-запрос. |
| **Private по умолчанию** | Сервисы не доступны из других сервисов, если не нужны (экономия памяти). |
| **Autowiring по умолчанию** | Не нужно явно указывать зависимости, если типы уникальны. |
| **Autoconfigure** | Symfony автоматически настраивает сервисы (например, подписывает их на события, если они реализуют `EventSubscriberInterface`). |
| **Код без рефлексии** | В рантайме рефлексия не используется (генерируется PHP-код). |
| **Lazy Loading** | Сервисы создаются только при первом запросе. |

---

### Типичные проблемы и решения

1.  **«Autowiring failed for type X»**
    *   **Причина:** Symfony не может найти сервис типа `X`.
    *   **Решение:** Убедитесь, что сервис зарегистрирован, или явно укажите его в конфигурации.

2.  **Циклические зависимости**
    *   **Причина:** `ServiceA` зависит от `ServiceB`, а `ServiceB` зависит от `ServiceA`.
    *   **Решение:** Измените архитектуру. Используйте **Event-ы** или **Service Locator** (редко).

3.  **Один сервис, несколько реализаций интерфейса**
    *   **Причина:** Два сервиса реализуют `LoggerInterface`.
    *   **Решение:** Используйте `#[Autowire(service: 'specific.logger')]` или назовите один сервис `default.logger`.

4.  **Изменение конфигурации**
    *   **Важно:** После изменения `services.yaml` или кода, который влияет на autowiring, **всегда** очищайте кеш:
        ```bash
        bin/console cache:clear
        ```

---

### Итог

*   **Компиляция:** Symfony анализирует код и генерирует PHP-классы для контейнера.
*   **Autowiring:** Symfony автоматически сопоставляет типы конструкторов с сервисами.
*   **Runtime:** Сервисы создаются лениво, кешируются в запросе и удаляются после запроса (если private).

Это делает Symfony очень производительным (нет рефлексии в рантайме) и удобным (минимум конфигурации).

Хотите, чтобы я показал, как вручную настроить сложную зависимость (например, с фабрикой) в `services.php`?

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это **архитектурная ошибка**, а не техническая. Symfony (и любой другой DI-контейнер) не должен «решать» циклы, потому что они означают, что у вас **нарушен принцип Single Responsibility** и **нарушена инверсия зависимостей**.

Если вы видите циклическую зависимость (`A -> B -> A`), значит, ваши классы слишком тесно связаны и несут на себе больше ответственности, чем должны.

### ❌ Почему нельзя «обманывать» Symfony?

Иногда предлагают использовать **Service Locator** или **Lazy Loading** для «разрыва» цикла:

```php
// Антипаттерн: Использование Service Locator
class ServiceA {
    public function __construct(
        private ServiceB $serviceB,
        private ServiceLocator $locator // ← Ловушка
    ) {}

    public function doSomething(): void
    {
        $this->serviceB->doWork();
        
        // Разрываем цикл, получая ServiceA из локации
        $serviceA = $this->locator->get(ServiceA::class);
        $serviceA->callBack(); 
    }
}
```

**Почему это плохо?**
1.  **Нарушение SRP:** `ServiceA` теперь знает о `ServiceLocator`, что делает его труднее тестируемым.
2.  **Скрытие проблемы:** Вы не исправили архитектуру, а просто спрятали зависимость.
3.  **Хрупкость:** Если порядок инициализации изменится, код сломается.

---

### ✅ Правильные способы разрыва циклических зависимостей

#### 1. Вынос общего поведения в отдельный сервис (Композиция)

**Проблема:**
`User` зависит от `Role`, а `Role` зависит от `User` (например, для проверки прав доступа).

**Решение:**
Создайте третий сервис `PermissionService`, который знает и о `User`, и о `Role`.

```php
// До (цикл)
class User {
    public function __construct(private Role $role) {}
    public function canAccess(string $resource): bool {
        return $this->role->hasPermission($resource, $this); // Зависит от User
    }
}

class Role {
    public function __construct(private User $user) {} // Цикл!
    public function hasPermission(string $resource, User $user): bool {
        // Логика
    }
}
```

```php
// После (без цикла)
class PermissionService {
    public function __construct(
        private RoleRepository $roleRepo,
        private User $user // Или получаем user из контекста
    ) {}

    public function canAccess(string $resource): bool
    {
        $role = $this->roleRepo->findRoleForUser($this->user->getId());
        return $role->hasPermission($resource);
    }
}

class User {
    // Больше не знает о Role напрямую
}
```

**Суть:** `User` и `Role` становятся «глупыми» (Data Objects), а вся логика взаимодействия выносится в `PermissionService`, который зависит от обоих. Цикл исчезает, потому что `User` и `Role` больше не зависят друг от друга напрямую.

---

#### 2. Использование событий (Event-Driven Architecture)

**Проблема:**
`OrderService` должен уведомить `InventoryService`, а `InventoryService` должен обновить `OrderService`.

**Решение:**
`OrderService` публикует событие `OrderPlacedEvent`. `InventoryService` подписывается на это событие и обновляет склад. `InventoryService` может опубликовать `InventoryUpdatedEvent`, на который подпишется `OrderService`, если нужно.

```php
// OrderService.php
class OrderService {
    public function __construct(
        private OrderRepository $repo,
        private EventDispatcherInterface $dispatcher // ← Только диспетчер
    ) {}

    public function placeOrder(Order $order): void
    {
        $this->repo->save($order);
        
        // Публикуем событие, а не вызываем InventoryService напрямую
        $event = new OrderPlacedEvent($order);
        $this->dispatcher->dispatch($event, OrderPlacedEvent::class);
    }
}

// InventoryService.php
class InventoryService implements EventSubscriberInterface {
    public function __construct(private InventoryRepository $repo) {}

    public static function getSubscribedEvents(): array
    {
        return [
            OrderPlacedEvent::class => 'onOrderPlaced'
        ];
    }

    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        $order = $event->getOrder();
        $this->repo->decrementStock($order->getProducts());
    }
}
```

**Почему это работает?**
*   `OrderService` не знает о `InventoryService`.
*   `InventoryService` не знает о `OrderService` (если ему не нужно обратное действие).
*   Связь **косвенная** через шину событий.
*   Если нужно обратное действие — `InventoryService` публикует `InventoryUpdatedEvent`, а `OrderService` подписывается на него.

---

#### 3. Внедрение интерфейса (Dependency Inversion)

**Проблема:**
`Logger` пишет в `Database`, а `Database` пишет логи в `Logger` (для отладки).

**Решение:**
`Logger` зависит от интерфейса `StorageInterface`. `Database` зависит от `LoggerInterface`.

```php
interface StorageInterface {
    public function save(LogEntry $entry): void;
}

interface LoggerInterface {
    public function log(string $message): void;
}

class DatabaseLogger implements LoggerInterface {
    public function __construct(private StorageInterface $storage) {}
    public function log(string $message): void {
        $this->storage->save(new LogEntry($message));
    }
}

class FileStorage implements StorageInterface {
    // Не зависит от LoggerInterface
}

class DatabaseService {
    public function __construct(
        private LoggerInterface $logger // ← Интерфейс, а не конкретный Logger
    ) {}
    
    public function execute(string $query): void {
        $this->logger->log('Executing: ' . $query);
        // ...
    }
}
```

**Суть:** `DatabaseService` не зависит от конкретного `DatabaseLogger`, а от абстракции `LoggerInterface`. `DatabaseLogger` не зависит от `DatabaseService`. Цикл разрушен через инверсию зависимостей.

---

#### 4. Передача данных, а не сервисов

**Проблема:**
`ServiceA` должен вызвать `ServiceB`, но `ServiceB` нужен только для получения одного значения.

**Решение:**
Передайте значение в конструктор `ServiceA`, а не весь сервис `ServiceB`.

```php
// До (цикл)
class ServiceA {
    public function __construct(private ServiceB $b) {}
}

class ServiceB {
    public function __construct(private ServiceA $a) {}
}

// После (без цикла)
class ServiceA {
    public function __construct(
        private int $someValue // ← Только данные
    ) {}
}

// Оркестратор (Controller/Command)
$serviceB = new ServiceB();
$someValue = $serviceB->calculateValue();
$serviceA = new ServiceA($someValue);
```

**Суть:** Если вам нужен только результат работы другого сервиса, передайте этот результат, а не сам сервис. Это превращает зависимость в **данные**.

---

### 🔍 Как диагностировать циклические зависимости?

1.  **Визуализация:** Используйте инструменты:
    *   `symfony/dependency-injection` имеет команду:
        ```bash
        bin/console debug:autowiring
        bin/console debug:container
        ```
    *   Специальные бандлы: `matt-schwabe/dependency-graph` (устанавливает Graphviz для визуализации графа зависимостей).
2.  **Статический анализ:**
    *   PHPStan/Psalm могут обнаруживать циклы.
    *   Напишите свой линтер, который парсит конструкторы и строит граф.

---

### Итог: Алгоритм разрушения цикла

1.  **Идентифицируйте** два сервиса, которые зависят друг от друга.
2.  **Спросите:** «Что общего у их ответственностей?»
3.  **Вынесите** общую логику в третий сервис (Композиция).
4.  **Или используйте** события, если одно действие должно приводить к другому (Event-Driven).
5.  **Или внедрите** интерфейсы, чтобы зависимости были направлены в одну сторону (Dependency Inversion).
6.  **Никогда** не используйте Service Locator для разрыва циклов — это заглушка, которая скрывает проблему.

Циклические зависимости — это **красный флаг** для рефакторинга. Они означают, что ваши границы между компонентами неочевидны.

> ⏱ 16.3s · prompt 15216 tok · output 2126 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Классическая задача **асинхронной обработки** в вебе. Ключевое требование: **ответить пользователю как можно быстрее**, отложив «тяжелые» или «медленные» операции (email, индексация) на потом.

В Symfony есть три основных подхода. Я рассмотрю их от наиболее современного/рекомендуемого к устаревшим.

---

### Вариант 1: Message Queue + Workers (Рекомендуемый)

Это «золотой стандарт» для production-систем. Мы используем **Message Queue** (очередь сообщений) и **консьюмеров** (workers), которые обрабатывают сообщения в фоне.

**Архитектура:**
1.  **Продюсер** (ваш код после регистрации) публикует сообщение в очередь.
2.  **Брокер** (RabbitMQ, Redis, SQS, Kafka) хранит сообщение.
3.  **Консьюмер** (отдельный процесс, запущенный через Docker/Supervisor) берет сообщение из очереди и обрабатывает его (отправляет письмо, обновляет индекс).

**Почему это лучше всего?**
*   **Надежность:** Если сервер упадет, сообщение останется в очереди и будет обработано при перезапуске.
*   **Масштабируемость:** Можно запустить 10 worker'ов для быстрой обработки.
*   **Отказоустойчивость:** Можно настроить retry-политики, dead-letter очереди.

**В Symfony это реализуется через `Symfony Messenger`** (компонент, встроенный с Symfony 4.3).

#### Как это выглядит в коде:

**1. Создаем Message (команду):**
```php
// App/Message/UserRegistered.php
class UserRegistered
{
    public function __construct(
        private int $userId
    ) {}

    public function getUserId(): int
    {
        return $this->userId;
    }
}
```

**2. Создаем Handler (обработчик):**
```php
// App/Messenger/Handler/UserRegisteredHandler.php
final class UserRegisteredHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexer $searchIndexer // Ваш сервис для поиска
    ) {}

    public function __invoke(UserRegistered $message): void
    {
        $userId = $message->getUserId();
        
        // 1. Отправляем письмо
        $this->sendWelcomeEmail($userId);
        
        // 2. Обновляем индекс
        $this->searchIndexer->addUser($userId);
        
        // Если выбросится исключение, Messenger повторит попытку (настраивается)
    }

    private function sendWelcomeEmail(int $userId): void
    {
        // Логика отправки письма
    }
}
```

**3. Публикуем сообщение в контроллере/сервисе:**
```php
// App/Service/UserRegistrationService.php
class UserRegistrationService
{
    public function __construct(
        private MessageBusInterface $bus,
        private EntityManagerInterface $em
    ) {}

    public function register(string $email, string $password): User
    {
        // ... валидация, создание User, хеширование пароля ...
        
        $this->em->persist($user);
        $this->em->flush();

        // Публикуем сообщение в очередь. 
        // Это быстрая операция (лишь записывает в Redis/RabbitMQ)
        $this->bus->dispatch(new UserRegistered($user->getId()));

        return $user;
    }
}
```

**4. Настройка `config/packages/messenger.yaml`:**
```yaml
framework:
    messenger:
        failure_transport: failed
        transports:
            async:
                dsn: '%env(MESSENGER_DSN)%' # Например: redis://localhost:6379
        routing:
            'App\Message\UserRegistered': async
        buses:
            default_bus:
                middleware:
                    - doctrine_open_transaction
                    - serializer_decode
                    - handle_message
```

**5. Запуск консьюмеров:**
```bash
bin/console messenger:consume async -v
```
(Обычно это отдельный сервис в `docker-compose.yml` или `supervisor.conf`)

---

### Вариант 2: Events + Synchronous Dispatch (Простой, но не асинхронный)

Если очередь — это «overhead» для вашего проекта, можно использовать **события (Events)**. Но важно: по умолчанию Symfony обрабатывает события **синхронно**.

Чтобы сделать это асинхронным, нужно либо:
1.  Использовать **Messenger** (см. выше).
2.  Использовать **отдельный процесс** (см. ниже).

**Если использовать события без Messenger:**
```php
// В контроллере
$event = new UserRegisteredEvent($user);
$dispatcher->dispatch($event, UserRegisteredEvent::NAME);
```

**Проблема:** Это **блокирует** выполнение запроса, пока подписчики не завершат работу. Если письмо отправляется 2 секунды, пользователь ждет 2 секунды. **Это не подходит под ваше требование.**

---

### Вариант 3: Cron Job + Таблица задач (База данных как очередь)

Альтернатива для проектов, где нельзя ставить RabbitMQ/Redis.

**Архитектура:**
1.  Создаем таблицу `async_tasks`:
    ```sql
    CREATE TABLE async_tasks (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(255) NOT NULL, -- 'user_registered', 'email_send'
        data JSON NOT NULL,
        status ENUM('pending', 'processing', 'done', 'failed') DEFAULT 'pending',
        attempts INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ```
2.  При регистрации вставляем запись в `async_tasks`.
3.  **Cron Job** (каждую минуту) берет `LIMIT 100` задач со статусом `pending` и обрабатывает их.

**Trade-offs:**
*   **Плюсы:** Не требует дополнительного брокера. Простое масштабирование.
*   **Минусы:** 
    *   Задержка: минимальная задержка 1 минута (можно чаще, но нагрузка на БД).
    *   Конкуренция: если несколько workers запущены, нужна блокировка (`SELECT ... FOR UPDATE`).
    *   Нет гибкости: сложно отложить задачу на 5 минут (нужна колонка `retry_at`).

---

### Сравнение вариантов

| Критерий | **Message Queue (Messenger)** | **Cron + DB** | **Синхронные Events** |
| :--- | :--- | :--- | :--- |
| **Скорость ответа** | 🔥 Максимальная (мс) | 🔥 Максимальная (мс) | ❌ Зависит от скорости email/индекса |
| **Надежность** | ✅ Отличная (retry, DLQ) | ✅ Хорошая (если есть блокировки) | ❌ Плохая (падает — теряется) |
| **Масштабируемость** | ✅ Отличная | ⚠️ Ограниченная (запросы к БД) | ❌ Плохая |
| **Сложность** | ⚠️ Высокая (настройка брокера) | ✅ Низкая | ✅ Минимальная |
| **Порядок** | ✅ Можно гарантировать | ❌ Сложно гарантировать | ✅ Гарантирован |

---

### Рекомендация

Для вашего случая (email + индексация после регистрации):

1.  **Используйте `Symfony Messenger`** с `Redis` или `RabbitMQ`.
2.  **Разделите задачи:** 
    *   `SendEmailMessage`
    *   `IndexUserMessage`
    *   Это позволит обработать их параллельно (если запустить несколько worker'ов).
3.  **Настройте retry:**
    ```yaml
    routing:
        'App\Message\SendEmailMessage': 
            transport: async
            options:
                attempt: 3
                delay: 5000 # 5 секунд между попытками
    ```
4.  **Добавьте мониторинг:** 
    *   Команда `bin/console messenger:stats` для просмотра очередей.
    *   Логируйте неудачные попытки.

### Пример полного потока

1.  Пользователь отправляет форму регистрации.
2.  Контроллер вызывает `UserRegistrationService::register()`.
3.  Сервис создает `User`, сохраняет в БД.
4.  Сервис диспатчит `UserRegistered` в Messenger (Redis).
5.  Контроллер возвращает `201 Created` пользователю (затраты ~10-50 мс).
6.  Worker (отдельный процесс) получает сообщение из Redis.
7.  Worker вызывает `UserRegisteredHandler`.
8.  Handler отправляет письмо и обновляет индекс (затраты ~1-5 секунд).
9.  Worker завершает задачу.

**Итог:** Пользователь получает мгновенный ответ, а фоновые задачи выполняются надежно и масштабируемо.

> ⏱ 15.6s · prompt 19507 tok · output 2090 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony — это многоуровневая система, которая позволяет гибко управлять тем, **кто** может **что** делать. Она состоит из трех основных компонентов:

1.  **Аутентификация** (Who are you?)
2.  **Авторизация** (What can you do?) — через **Роли** и **Voters**.
3.  **Security Firewall** (Как вы вошли?)

---

### 1. Аутентификация (Authentication)

Это процесс проверки **идентичности** пользователя. В Symfony это реализовано через **Firewall** (конфигурация в `config/packages/security.yaml`).

**Основные концепции:**
*   **Provider:** Откуда берем данные о пользователе (БД, API, LDAP).
*   **Authenticator:** Логика проверки (пароль, JWT, OAuth2).
*   **Entry Point:** Что делаем, если пользователь не аутентифицирован (редирект на логин, 401 Unauthorized).

**Пример конфигурации:**
```yaml
security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
            logout:
                path: app_logout
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER]
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_USER]
```

---

### 2. Авторизация: Роли (Roles)

**Роли** — это простые строки-теги, которые привязаны к пользователю или к ресурсу.

**Когда использовать роли:**
*   **Грубая гранулярность:** «Администраторы могут видеть панель», «Пользователи могут видеть профиль».
*   **Глобальные права:** Права, не зависящие от конкретного объекта.
*   **Быстрая проверка:** `is_granted('ROLE_ADMIN')`.

**Как проверить:**
```php
// В контроллере
#[IsGranted('ROLE_ADMIN')]
public function adminPanel(): Response
{
    // Только администраторы дойдут сюда
}

// В Twig
{% if is_granted('ROLE_ADMIN') %}
    <button>Удалить</button>
{% endif %}
```

**Ограничение ролей:**
Роли отвечают на вопрос: **«Имеет ли пользователь общее право на действие?»**, но **не** отвечают на вопрос: **«Имеет ли пользователь право на *этот конкретный объект*?»**.

Например:
*   ✅ `ROLE_ADMIN` может удалить **любой** пост.
*   ✅ `ROLE_USER` может удалить **только свой** пост.
*   ❌ Роли не могут выразить: «Удалить пост, если `post.author == currentUser` ИЛИ `post.status == 'draft'`».

Для таких случаев нужны **Voters**.

---

### 3. Voters (Голосующие)

**Voters** — это классы, которые принимают решение о доступе к **конкретному объекту**. Это самый мощный и гибкий инструмент контроля доступа в Symfony.

**Как это работает:**
1.  Вы создаете класс `PostVoter`, который реализует интерфейс `VoterInterface`.
2.  Вы определяете **Attribute** (действие, например, `EDIT`, `DELETE`) и **Subject** (объект, например, `Post`).
3.  Вы пишете логику: «Пользователь `A` имеет право `DELETE` объект `Post B`?».

**Пример: `PostVoter`**
```php
// src/Security/PostVoter.php
class PostVoter extends Voter
{
    public const EDIT = 'post:edit';
    public const DELETE = 'post:delete';

    protected function supports(string $attribute, $subject): bool
    {
        // Этот voter работает только для объектов Post
        // и только для атрибутов post:edit и post:delete
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, UserInterface $user): bool
    {
        /** @var Post $subject */
        $post = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($post, $user),
            self::DELETE => $this->canDelete($post, $user),
            default => false,
        };
    }

    private function canEdit(Post $post, UserInterface $user): bool
    {
        return $post->getAuthor() === $user;
    }

    private function canDelete(Post $post, UserInterface $user): bool
    {
        // Автор может удалить
        if ($post->getAuthor() === $user) {
            return true;
        }

        // Администратор может удалить любой пост
        return $user->hasRole('ROLE_ADMIN');
    }
}
```

**Как использовать Voter:**
```php
// В контроллере
public function edit(Post $post): Response
{
    // Проверяем право на редактирование конкретного поста
    $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
    
    // ... логика редактирования
}

// В Twig
{% if is_granted('post:edit', post) %}
    <a href="{{ path('post_edit', { id: post.id }) }}">Редактировать</a>
{% endif %}
```

---

### Когда использовать Roles, а когда Voters?

| Критерий | **Roles (Роли)** | **Voters (Голосующие)** |
| :--- | :--- | :--- |
| **Гранулярность** | Глобальная (на все объекты) | Объектная (на конкретный объект) |
| **Сложность логики** | Простая (есть/нет) | Сложная (зависит от состояния объекта) |
| **Пример** | `ROLE_ADMIN` может видеть все посты | Только автор может редактировать свой пост |
| **Производительность** | Высокая (просто проверка строки) | Ниже (вызывается метод `vote()`) |
| **Гибкость** | Низкая | Высокая (можно писать любую бизнес-логику) |
| **Использование в Twig** | `is_granted('ROLE_ADMIN')` | `is_granted('post:edit', post)` |

---

### Лучшая практика: Комбинация

В реальных проектах **роли и voters работают вместе**.

**Пример: Удаление поста**
1.  **Роли** определяют, кто вообще может иметь право на удаление (например, `ROLE_MODERATOR` или `ROLE_ADMIN`).
2.  **Voter** проверяет, имеет ли этот пользователь право на **этот конкретный** пост.

**Оптимизированный `PostVoter`:**
```php
protected function supports(string $attribute, $subject): bool
{
    return in_array($attribute, [self::DELETE]) && $subject instanceof Post;
}

protected function voteOnAttribute(string $attribute, $subject, UserInterface $user): bool
{
    $post = $subject;

    // 1. Быстрая проверка: есть ли у пользователя базовая роль?
    if (!$user->hasRole('ROLE_MODERATOR') && !$user->hasRole('ROLE_ADMIN')) {
        // Даже если он автор, он не имеет базовой роли на удаление? 
        // Зависит от бизнес-логики. Часто авторы не могут удалять, 
        // только модераторы. Или авторы могут удалять черновики.
    }

    // 2. Детальная проверка
    return match ($attribute) {
        self::DELETE => 
            $post->getAuthor() === $user || 
            $user->hasRole('ROLE_ADMIN'),
        default => false,
    };
}
```

**В контроллере:**
```php
#[IsGranted('ROLE_USER')] // Базовая проверка: пользователь должен быть залогинен
public function delete(Post $post): Response
{
    // Детальная проверка: может ли этот пользователь удалить этот пост?
    $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
    
    // ... удаляем
}
```

---

### Дополнительные возможности Symfony Security

1.  **Access Control List (ACL):**
    *   Для очень сложных сценариев, когда права назначаются динамически (например, «Пользователь X может читать документ Y, но не Z»).
    *   Использует таблицу `acl_entries` в БД.
    *   **Минус:** Медленнее, сложнее в настройке.
    *   **Плюс:** Очень гибко, права можно менять в админке без деплоя.

2.  **Channel Rules:**
    *   Управлять доступом по HTTP-каналу (HTTP/HTTPS).
    *   Например: «Все запросы к `/admin` должны быть по HTTPS».

3.  **CSRF Protection:**
    *   Защита от подделки межсайтовых запросов.
    *   Автоматически включена для форм.
    *   Важно для безопасности, но не для контроля доступа как такового.

---

### Итоговая рекомендация

1.  **Начните с ролей** для простых сценариев (Admin, User, Guest).
2.  **Введите Voters** для объектной безопасности (посты, заказы, профили).
3.  **Не дублируйте логику:** Если логика доступа сложная, вынесите её в Voter, а не в контроллер.
4.  **Тестируйте:** Напишите unit-тесты для Voters, так как они содержат бизнес-логику.
5.  **Используйте `#[IsGranted]`** в контроллерах и `is_granted()` в шаблонах — это стандартный способ.

Такой подход дает вам **гибкость, безопасность и читаемость** кода.

> ⏱ 17.2s · prompt 21642 tok · output 2308 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение неизменяемых Value Objects (VO) в Doctrine — классическая боль DDD-разработчиков.

**Ключевой конфликт:**
*   **Домен** требует: Объект неизменяем (`readonly`), всегда валиден (конструктор бросает исключения при невалидных данных).
*   **Doctrine** требует: Гибкость, частичное обновление, `null`-значения, прямую запись в базу.

**Решение:** Никогда не мапьте VO напрямую в БД. Мапьте **примитивы** (строки, int, datetime) и конвертируйте их в VO при загрузке/сохранении.

Вот 4 основных подхода, от простого к сложному.

---

### Вариант 1: Custom Type (Наиболее распространенный)

Создаем собственный Doctrine Type, который преобразует VO в строку/массив и обратно.

**Когда использовать:** VO имеет простую структуру (1-2 поля) или сериализуется в JSON.

#### Пример: `Money` (amount, currency)

**1. Создаем VO:**
```php
namespace App\Domain\ValueObject;

final class Money
{
    public function __construct(
        private readonly int $amount, // в копейках/центах
        private readonly string $currency
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        if (!in_array($currency, ['USD', 'EUR', 'RUB'])) {
            throw new \InvalidArgumentException('Invalid currency');
        }
    }

    public function getAmount(): int { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }

    public function toJson(): string
    {
        return json_encode(['amount' => $this->amount, 'currency' => $this->currency]);
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);
        return new self($data['amount'], $data['currency']);
    }
}
```

**2. Создаем Doctrine Type:**
```php
namespace App\Infrastructure\Doctrine\Type;

use App\Domain\ValueObject\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'JSON'; // Или VARCHAR(255), в зависимости от БД
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null || $value === '') {
            return null;
        }
        return Money::fromJson($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        
        if (!($value instanceof Money)) {
            throw new \UnexpectedValueException('Expected Money instance');
        }

        return $value->toJson();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

**3. Регистрация:**
```php
// config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            money: App\Infrastructure\Doctrine\Type\MoneyType
```

**4. Использование в сущности:**
```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'money', nullable: true)]
    private ?Money $total = null;

    public function getTotal(): ?Money
    {
        return $this->total;
    }

    public function setTotal(Money $total): void
    {
        // Валидация уже внутри Money
        $this->total = $total;
    }
}
```

**Плюсы:**
*   Чистый домен: `Order` работает только с `Money`.
*   Инкапсуляция: Валидация происходит в `Money`.
*   Универсальность: Можно использовать `Money` в любом сервисе.

**Минусы:**
*   **SQL-запросы:** Вы не можете делать `WHERE total.amount > 100` в DQL. Нужно парсить JSON в PHP или использовать raw SQL.
*   **Индексация:** Сложно индексировать составные значения.
*   **Производительность:** JSON-парсинг/сериализация на каждом запросе.

**Подводные камни:**
*   `convertToPHPValue` вызывается **каждый раз**, когда вы обращаетесь к геттеру. Если VO тяжелый, это дорого.
*   Если `Money` содержит ссылки на другие сущности — это сломается (циклические зависимости в JSON).

---

### Вариант 2: Маппинг примитивов + Конструктор валидации

Если VO состоит из простых типов (string, int, datetime), храните их как отдельные колонки, но **валидируйте** их в конструкторе сущности.

**Когда использовать:** VO имеет 2-5 простых полей, которые могут быть полезны для SQL-запросов.

#### Пример: `Address` (street, city, zip)

```php
final class Address
{
    public function __construct(
        private readonly string $street,
        private readonly string $city,
        private readonly string $zip
    ) {
        if (mb_strlen($zip) !== 5) {
            throw new \InvalidArgumentException('Zip must be 5 chars');
        }
    }
    
    // Геттеры...
}
```

**Сущность:**
```php
#[ORM\Entity]
class User
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $addressStreet;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $addressCity;

    #[ORM\Column(type: Types::STRING, length: 10)]
    private string $addressZip;

    // Геттер, который собирает VO
    public function getAddress(): Address
    {
        return new Address($this->addressStreet, $this->addressCity, $this->addressZip);
    }

    // Сеттер, который разбивает VO
    public function setAddress(Address $address): void
    {
        // VO гарантирует валидность
        $this->addressStreet = $address->getStreet();
        $this->addressCity = $address->getCity();
        $this->addressZip = $address->getZip();
    }
}
```

**Плюсы:**
*   **SQL-совместимость:** Можно делать `WHERE address_city = 'Moscow'`.
*   **Индексация:** Можно индексировать `address_city`.
*   **Простота:** Не нужно писать кастомный Type.

**Минусы:**
*   **Раздутость сущности:** 3 колонки вместо 1.
*   **Дублирование логики:** Валидация в `Address` и в сущности (если добавить проверки в сеттеры).
*   **Неизменяемость нарушена на уровне сущности:** Вы можете случайно поменять `$this->addressCity` напрямую, минуя `setAddress`.

**Подводные камни:**
*   Нужно тщательно следить, чтобы все поля VO были обновлены атомарно.

---

### Вариант 3: EAV-модель (Entity-Attribute-Value)

Хранить свойства VO в отдельной таблице с ключами-значениями.

**Когда использовать:** VO имеет динамическую структуру (разные типы, разное количество полей).

**Плюсы:**
*   Максимальная гибкость.

**Минусы:**
*   **Катастрофа для производительности:** N+1 запросы, сложные JOINы.
*   **Нет типобезопасности:** Значения хранятся как строки.
*   **Сложность работы с доменом:** Нужно постоянно парсить массивы.

**Рекомендация:** Избегайте, если только не работаете с CMS или динамическими формами.

---

### Вариант 4: Событийная модель (Event Sourcing / CQRS)

Не хранить VO в реляционной БД вовсе, а хранить изменения в виде событий.

**Когда использовать:** Высокая нагрузка, нужен аудит, сложные временные сценарии.

**Плюсы:**
*   Полная неизменяемость истории.
*   Гибкость при чтении.

**Минусы:**
*   Сложность реализации.
*   Сложность администрирования.

**Рекомендация:** Только для очень специфических случаев (финансы, логистика).

---

### Сравнение подходов

| Критерий | **Custom Type (JSON)** | **Примитивы (Columns)** | **EAV** |
| :--- | :--- | :--- | :--- |
| **SQL-запросы** | ❌ Ограничены (JSON-функции) | ✅ Полная поддержка | ❌ Очень сложны |
| **Индексация** | ❌ Сложно | ✅ Легко | ❌ Практически невозможно |
| **Производительность** | ⚠️ Средняя (JSON-парсинг) | ✅ Высокая | ❌ Низкая |
| **Чистота домена** | ✅ Высокая | ⚠️ Средняя | ❌ Низкая |
| **Сложность кода** | ✅ Низкая | ✅ Низкая | ❌ Высокая |
| **Гибкость структуры** | ✅ Высокая | ❌ Низкая (нужен миграции) | ✅ Максимальная |

---

### Лучшие практики и подводные камни

#### 1. Никогда не храните `null` в VO
Если поле в VO обязательное, не позволяйте ему быть `null` в сущности.
```php
// Плохо
#[ORM\Column(type: 'money', nullable: true)]
private ?Money $total = null;

// Хорошо (если total всегда есть)
#[ORM\Column(type: 'money')]
private Money $total;

// Хорошо (если total может отсутствовать, но VO неизменяем)
private ?Money $total = null; // Но валидируйте в конструкторе сущности
```

#### 2. Использование `#[ORM\Embeddable]`
Если VO состоит из простых полей, можно использовать `Embeddable`, но это **не** дает неизменяемости.
```php
#[ORM\Embeddable]
class MoneyEmbeddable
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;
}
```
**Минус:** Вы теряете возможность валидации в конструкторе VO, т.к. Doctrine создает объект через `Reflection`, минуя конструктор.

#### 3. Иммутабельность в сущности
Даже если VO неизменяем, сущность может меняться.
```php
class Order
{
    private ?Money $total = null;

    public function setTotal(Money $total): void
    {
        $this->total = $total; // Замена объекта, а не изменение полей
    }
}
```

#### 4. Подводный камень: Lazy Loading
Если VO хранится в JSON, он загружается вместе со сущностью. Это нормально.
Если VO хранится в отдельных колонках, они тоже загружаются.
**Проблема:** Если VO большой, вы таскаете много данных, которые могут не понадобиться.

**Решение:**
*   Используйте `select` в DQL, чтобы выбирать только нужные поля.
*   Или разбивайте VO на меньшие сущности.

#### 5. Подводный камень: Сравнение объектов
```php
if ($order->getTotal() === new Money(100, 'USD')) {
    // Никогда не будет true! Разные объекты.
}
```
**Решение:**
Сравните поля:
```php
if ($order->getTotal()->getAmount() === 100 && $order->getTotal()->getCurrency() === 'USD') {
    // true
}
```
Или добавьте метод `equals()`:
```php
public function equals(Money $other): bool
{
    return $this->amount === $other->getAmount() 
        && $this->currency === $other->getCurrency();
}
```

---

### Итоговая рекомендация

1.  **Если VO простой (1-3 поля, примитивы):** Используйте **примитивы в колонках** (Вариант 2). Это самый производительный и SQL-совместимый вариант.
2.  **Если VO сложный или нужен JSON:** Используйте **Custom Type** (Вариант 1). Это чище для домена.
3.  **Избегайте** хранения VO, которые содержат ссылки на другие сущности (например, `Address` с `Country` как объектом). Лучше храните `countryId` или `countryCode`.

**Главный принцип:** Домен знает только VO. Инфраструктура знает только примитивы. Слой маппинга (Type или Embeddable) — это мост между ними.

> ⏱ 21.3s · prompt 24004 tok · output 3086 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Хранение агрегатов в Doctrine — это баланс между **чистотой домена** и **механикой ORM**.

Ключевые правила DDD для агрегатов:
1.  **Корень агрегата** — единственная точка входа для внешних ссылок.
2.  **Внешний мир не ссылается напрямую на дочерние сущности**, только на корень.
3.  **Целостность агрегата** должна быть гарантирована внутри транзакции.

Рассмотрим классический пример: **`Order`** (корень) и **`OrderItem`** (дочерние).

---

### 1. Архитектура домена

```php
// Domain/Entity/Order.php
final class Order
{
    private string $id;
    private array $items = []; // Коллекция дочерних
    private string $status = 'pending';
    
    private bool $isNew = false; // Флаг для определения, нужно ли persist

    public function __construct(string $customerId)
    {
        $this->id = self::generateId();
        $this->isNew = true;
        // ...
    }

    /**
     * Только корень агрегата может изменять его состояние
     */
    public function addItem(string $productId, int $quantity, Money $price): void
    {
        if (in_array($this->status, ['shipped', 'delivered'])) {
            throw new \DomainException('Cannot modify completed order');
        }

        $this->items[] = new OrderItem($productId, $quantity, $price);
    }

    public function markAsShipped(): void
    {
        $this->status = 'shipped';
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
```

```php
// Domain/Entity/OrderItem.php
final class OrderItem
{
    public function __construct(
        private string $productId,
        private int $quantity,
        private Money $price
    ) {}
    
    // Геттеры...
}
```

**Важно:** `OrderItem` **не** имеет своего ID, который используется внешним миром. Он существует только внутри `Order`.

---

### 2. Маппинг в Doctrine

#### Вариант A: `OneToOne` + `Cascade` (Рекомендуемый для дочерних)

Если дочерние сущности **существуют только** внутри агрегата (Child Entity), мы маппим их как часть корня.

```php
#[ORM\Entity]
class OrderEntity
{
    #[ORM\Id, ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::STRING)]
    private string $customerId;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    /**
     * Cascade: persist, remove
     * OrphanRemoval: true — если элемент удаляется из коллекции, 
     * он будет удален из БД. Это критично для агрегатов!
     */
    #[ORM\OneToOne(
        targetEntity: OrderItemEntity::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    ), ORM\JoinColumn(name: 'order_item_id')]
    private ?OrderItemEntity $item = null; 
    // В реальном проекте: #[ORM\OneToMany] или ArrayCollection
}
```

> **Примечание:** В реальном проекте лучше использовать `OneToMany` с `JoinColumn` или `OneToOne` с `Cascade`, если `OrderItem` имеет свой ID для служебных целей.

#### Вариант B: `Embeddable` (Если Item прост)

Если `OrderItem` не имеет собственного жизненного цикла, можно маппировать его как `Embeddable` в `OrderEntity`. Но тогда вы теряете возможность делать запросы только к позициям.

#### Вариант C: `ManyToMany` (Не рекомендуется для агрегатов)

Это создает прямые ссылки из `Item` в `Order`, нарушая границу агрегата. Избегайте.

---

### 3. Границы транзакций и Flush

**Кто вызывает `flush`?**

**Правильный ответ:** **Никто из доменных слоев напрямую.**

Правильная архитектура:

1.  **Домен** возвращает **измененные сущности** или **события**.
2.  **Приложение (Service/Controller)** управляет транзакцией.
3.  **Утилита (EntityManager)** вызывает `flush`.

#### Паттерн: Unit of Work + Unit of Repository

```php
// Application/Service/OrderService.php
final class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function createOrder(string $customerId, array $items): Order
    {
        // 1. Создаем доменный объект (чистый PHP)
        $order = new Order($customerId);
        
        foreach ($items as $item) {
            $order->addItem($item['id'], $item['qty'], new Money($item['price'], 'USD'));
        }

        // 2. Сохраняем через репозиторий (абстракция)
        $this->orderRepository->save($order);

        return $order;
    }
}
```

```php
// Infrastructure/Repository/DoctrineOrderRepository.php
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function save(Order $order): void
    {
        $entity = $this->toEntity($order);
        
        // Если сущность новая — persist
        if ($order->isNew()) {
            $this->em->persist($entity);
        }

        // ВАЖНО: Здесь мы НЕ делаем flush!
        // Мы оставляем это на усмотрение вызывающего кода (Service),
        // чтобы можно было сгруппировать несколько операций в одну транзакцию.
    }

    private function toEntity(Order $order): OrderEntity
    {
        $entity = new OrderEntity();
        $entity->setId($order->getId());
        $entity->setCustomerId($order->getCustomerId());
        $entity->setStatus($order->getStatus());

        foreach ($order->getItems() as $item) {
            $itemEntity = new OrderItemEntity();
            $itemEntity->setProductId($item->getProductId());
            $itemEntity->setQuantity($item->getQuantity());
            $entity->setItem($itemEntity);
        }

        return $entity;
    }
}
```

#### Почему не `flush` в репозитории?

1.  **Группировка операций:** Если вы делаете `save` и `flush` сразу, то любая последующая операция в том же методе будет в новой транзакции (если не использовать `Doctrine\ORM\EntityManager::wrapInTransaction`).
2.  **Контроль ошибок:** Если после `save` вы получите ошибку валидации, вы хотите откатить **все** изменения, а не только часть.
3.  **Производительность:** Один `flush` на несколько операций быстрее, чем несколько.

#### Правильное место для `flush`

```php
// Application/Service/OrderService.php
final class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $em
    ) {}

    public function createOrder(string $customerId, array $items): Order
    {
        $order = new Order($customerId);
        foreach ($items as $item) {
            $order->addItem($item['id'], $item['qty'], new Money($item['price'], 'USD'));
        }

        // Сохраняем
        $this->orderRepository->save($order);

        // Финализируем транзакцию
        $this->em->flush();
        $this->em->clear(); // Опционально: освобождаем память

        return $order;
    }
}
```

Или лучше — использовать **Unit of Work** из `doctrine/orm`:

```php
$transaction = $this->em->wrapInTransaction(function() use ($order) {
    $this->orderRepository->save($order);
});
```

---

### 4. Подводные камни

#### 1. `OrphanRemoval` и `Cascade`
Если вы используете `OneToMany` или `OneToOne` с `orphanRemoval: true`:
*   Когда вы удаляете элемент из коллекции `Order->getItems()`, Doctrine автоматически удалит его из БД при `flush`.
*   **Опасно:** Если вы случайно создадите новый объект `OrderItem` и не добавите его в агрегат, он будет потерян.
*   **Решение:** Всегда проверяйте целостность агрегата в доменных методах.

#### 2. Наследование и `MappedSuperclass`
Не используйте наследование для дочерних сущностей внутри агрегата, если они не имеют общего интерфейса для внешних запросов.

#### 3. Запросы к дочерним
Если вам нужно сделать `SELECT * FROM order_items WHERE ...`, это **нарушает границу агрегата**.

**Правильный подход:**
*   Загрузите `Order` (корень).
*   Получите `items` через навигацию: `$order->getItems()`.
*   Фильтруйте в PHP.

**Исключение:** Если `OrderItem` используется во многих агрегатах и имеет самостоятельную ценность (например, `Product`), то он может быть отдельной сущностью, но тогда это уже **не дочерняя сущность**, а **зависимость**.

#### 4. `flush` в контроллере?
Никогда. Контроллер должен быть тонким. Вся логика — в сервисе.

#### 5. События
Если изменение агрегата должно публиковать событие, публикуйте его **после** `flush`, чтобы убедиться, что данные сохранены.

```php
public function createOrder(...): Order
{
    // ... создание ...
    $this->orderRepository->save($order);
    $this->em->flush();
    
    // Теперь безопасно публиковать событие
    $this->eventDispatcher->dispatch(new OrderCreatedEvent($order));
    
    return $order;
}
```

---

### Итог

1.  **Маппинг:** `Cascade: ['persist', 'remove']` + `orphanRemoval: true` для дочерних.
2.  **Граница транзакции:** Определяется **сервисом** (приложением), а не репозиторием.
3.  **Flush:** Вызывается в сервисе после всех операций с агрегатом.
4.  **Чистота домена:** Дочерние сущности не имеют собственных ID, доступных внешнему миру.
5.  **Запросы:** Всегда через корень агрегата.

> ⏱ 17.9s · prompt 27157 tok · output 2543 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика и решение проблемы N+1 в Doctrine — это рутинная, но критически важная часть оптимизации PHP-приложений. N+1 возникает, когда ORM выполняет 1 запрос на загрузку списка сущностей и N+1 запрос для загрузки связанных данных каждой из них.

---

### 1. Диагностика: Как найти N+1

#### А. Включите SQL-логи (Quick & Dirty)
В `config/packages/doctrine.yaml`:
```yaml
doctrine:
    dbal:
        logging:
            middlewares:
                - Doctrine\DebugBar\DBAL\Middleware
```
Или используйте `Doctrine\DBAL\Logging\SQLite\SQLiteDriver` для записи в файл.

**Симптом:** Вы видите в логе:
```sql
SELECT o.* FROM orders o LIMIT 20;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 1;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 2;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 3;
...
```
Это классический N+1.

#### Б. Инструменты
1.  **Symfony Debug Bundle:** Показывает количество запросов в профиле.
2.  **Blackfire:** Профилирует время выполнения каждого SQL-запроса.
3.  **Doctrine ORM Logger:** Кастомный логгер, который подсчитывает количество запросов по сущности.

#### В. Статический анализ
Используйте `doctrine/orm` вместе с **PHPStan** или **Psalm**. Они могут предупредить о потенциальных N+1, если вы используете `EAGER`-загрузку или не используете `fetch` в DQL.

---

### 2. Способы решения и их Trade-offs

#### Вариант 1: `JOIN` в DQL/QueryBuilder (Самый частый)

**Суть:** Загружаем связанные сущности одним запросом через `JOIN`.

**До (N+1):**
```php
// В контроллере или сервисе
$orders = $em->getRepository(Order::class)->findAll();
foreach ($orders as $order) {
    $items = $order->getItems(); // Ленивая загрузка: N+1 запрос
}
```

**После (JOIN):**
```php
$qb = $em->createQueryBuilder()
    ->select('o, i')
    ->from(Order::class, 'o')
    ->leftJoin('o.items', 'i');
$orders = $qb->getQuery()->getResult();
```

**Trade-offs:**
*   **Плюсы:**
    *   1 запрос вместо N+1.
    *   Данные уже загружены в объекты, нет дополнительных SQL-вызовов при обращении.
*   **Минусы:**
    *   **Кривое дерево данных:** Если `items` — это `OneToMany`, Doctrine создает один объект `Order` и N объектов `Item`, но при этом `Item` привязаны к одному `Order`. Если у вас `ManyToMany` или сложные связи, это может привести к дублированию строк в PHP.
    *   **Память:** Все данные загружаются сразу в память. Если `items` огромные, это может быть дорого.
    *   **Сложность DQL:** Связи могут быть сложными.

**Когда использовать:** Для простых связей `OneToMany`, `ManyToOne`, где размер связанной коллекции ограничен.

---

#### Вариант 2: `EAGER`-загрузка (Не рекомендуется)

**Суть:** Указание в маппинге `@ORM\OneToMany(fetch="EAGER")`.

**Пример:**
```php
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, fetch: 'EAGER')]
private array $items = [];
```

**Trade-offs:**
*   **Плюсы:**
    *   Простота: не нужно писать `JOIN` в каждом запросе.
*   **Минусы:**
    *   **Нарушение принципа "Load on demand":** Вы загружаете данные, которые может и не нужны.
    *   **Сложность контроля:** Не всегда можно отказаться от загрузки.
    *   **Масштабируемость:** Если у одного `Order` 1000 `Items`, вы загрузите все 1000, даже если вам нужно только 3.
    *   **Скрытые зависимости:** Трудно отследить, где именно выполняется загрузка.

**Когда использовать:** Почти никогда. Только если связанные данные всегда нужны и всегда малы по количеству.

---

#### Вариант 3: Разделение запросов (Data Transfer Objects / DTOs)

**Суть:** Вместо загрузки сущностей с ленивыми коллекциями, вы делаете один запрос, который возвращает плоские данные (DTO), и отдельно загружаете связанные данные.

**Пример:**
```php
// Запрос 1: Основные данные заказов
$orders = $em->createQuery(
    'SELECT o.id, o.status, o.total FROM Order o'
)->getResult();

// Запрос 2: Все позиции для этих заказов
$items = $em->createQuery(
    'SELECT oi.* FROM OrderItem oi WHERE oi.order_id IN (:ids)'
)->setParameter('ids', array_column($orders, 'id'))->getResult();

// В PHP группируем items по order_id
$groupedItems = array_group_by($items, 'order_id');
```

**Trade-offs:**
*   **Плюсы:**
    *   Полный контроль над тем, какие данные загружать.
    *   Можно пагинацию на связанных данных.
    *   Нет кривого дерева данных в ORM.
    *   Легко оптимизировать под конкретный запрос.
*   **Минусы:**
    *   Больше кода в приложении.
    *   Нужно вручную группировать данные.
    *   Потеря "магии" ORM.

**Когда использовать:** Для сложных запросов, списков, где нужно оптимизировать объем данных (API, списки в админке).

---

#### Вариант 4: `Partial Loading` (Частичная загрузка)

**Суть:** Загружаем только нужные поля сущности, а не всю сущность.

**Пример:**
```php
// Загружаем только id и название, не загружая lazy collections
$orders = $em->createQuery(
    'SELECT o.id, o.name FROM Order o'
)->getResult();
```

**Trade-offs:**
*   **Плюсы:**
    *   Меньше данных в памяти.
    *   Быстрее.
*   **Минусы:**
    *   Если вы потом попробуете получить `$order->getItems()`, вы получите ошибку, потому что поля не загружены.
    *   Нельзя использовать все методы сущности.

**Когда использовать:** Для списков, где нужна только часть полей.

---

#### Вариант 5: Кэширование (Second-Level Cache)

**Суть:** Используем второй уровень кэша Doctrine, чтобы избежать запросов к БД для связанных сущностей.

**Настройка:**
```yaml
doctrine:
    orm:
        second_level_cache:
            enabled: true
            cache:
                namespace: 'second_level_cache'
                driver:
                    type: redis
                    host: localhost
                    port: 6379
```

**Trade-offs:**
*   **Плюсы:**
    *   Снижение нагрузки на БД.
    *   Быстрее при повторных запросах.
*   **Минусы:**
    *   **Сложность:** Нужно настраивать, что кэшировать, как инвалидировать.
    *   **Консистентность:** Риск неактуальных данных.
    *   **Не решает N+1 полностью:** Если кэш промахивается, вы всё равно получите N+1 запросов.

**Когда использовать:** Для редко изменяемых данных (справочники, каталоги).

---

### 3. Рекомендуемый подход (Комбинация)

1.  **Диагностика:** Включите логи, найдите N+1.
2.  **Для простых случаев:** Используйте `JOIN` в DQL.
3.  **Для сложных случаев:** Используйте DTOs + разделение запросов.
4.  **Для справочников:** Используйте Second-Level Cache.
5.  **Никогда не используйте:** `EAGER`-загрузку, если только вы не уверены, что связанные данные всегда нужны и малы.

### 4. Пример рефакторинга

**До (N+1):**
```php
$orders = $repo->findAll();
foreach ($orders as $order) {
    echo $order->getName();
    foreach ($order->getItems() as $item) {
        echo $item->getName(); // N+1 запрос
    }
}
```

**После (JOIN):**
```php
$qb = $em->createQueryBuilder()
    ->select('o, i')
    ->from(Order::class, 'o')
    ->leftJoin('o.items', 'i');
$orders = $qb->getQuery()->getResult();
```

**После (DTO):**
```php
$orders = $em->createQuery(
    'SELECT o.id, o.name FROM Order o'
)->getResult();

$items = $em->createQuery(
    'SELECT oi.order_id, oi.name FROM OrderItem oi WHERE oi.order_id IN (:ids)'
)->setParameter('ids', array_column($orders, 'id'))->getResult();

$grouped = array_group_by($items, 'order_id');

foreach ($orders as $order) {
    echo $order['name'];
    foreach ($grouped[$order['id']] ?? [] as $item) {
        echo $item['name'];
    }
}
```

Выбирайте способ в зависимости от сложности сущностей и требований к производительности.

> ⏱ 17.6s · prompt 29754 tok · output 2241 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это вопрос, который часто возникает при переходе от «простых» CRUD-приложений к сложным системам. Короткий ответ: **ограничения БД — это последняя линия защиты от мусорных данных, а код приложения — первая и основная линия.**

Давайте разберем подробно.

---

### 1. Зачем нужны ограничения БД, если есть валидация в коде?

#### А. Защита от «обходных путей»
Ваше приложение — это не единственный способ писать в базу.
*   **Администраторы БД:** Могут сделать `INSERT` через `psql` или pgAdmin.
*   **Миграции/Скрипты:** Автоматизированные процессы, которые обновляют данные напрямую.
*   **Синхронизация:** Если у вас репликация, CDC (Change Data Capture) или интеграция с внешними системами, они могут писать в БД, минуя ваш код.
*   **Баги в коде:** Вы забыли отключить `strict_types`, пропустили проверку в новом методе, или кто-то другой написал код, который не проходит вашу валидацию.

**Ограничение БД гарантирует, что в базе **никогда** не будет невалидных данных, независимо от того, кто и как их записал.**

#### Б. Консистентность в конкурентных средах
Ваш код может проверять условие «осталось ли 10 товаров на складе», но между проверкой и записью (`UPDATE stock SET count = count - 1`) другой поток мог уже купить последний товар.
*   **Код:** `if (stock > 0) { save(); }` → **Уязвимо** к race condition.
*   **БД:** `CHECK (count >= 0)` или `UNIQUE` constraint на резервирование → **Гарантирует** целостность.

#### В. Производительность
Ограничения в БД (особенно индексные) часто быстрее проверяются движком БД, чем в PHP, потому что:
*   Нет сериализации/десериализации.
*   Нет контекста приложения.
*   PostgreSQL оптимизирует проверки `CHECK` и `UNIQUE` на уровне ядра.

#### Г. Самодокументируемость схемы
Ограничения в БД — это **контракт данных**. Если я смотрю на схему таблицы, я должен понять, какие данные в ней разрешены.
*   `email VARCHAR(255) NOT NULL UNIQUE` — это говорит: «Email обязателен и уникален».
*   Без этого ограничения я должен читать код приложения, чтобы понять, можно ли вставить `NULL` или дубликат.

---

### 2. Какие средства используем в PostgreSQL?

#### А. `CHECK` Constraints
Самые мощные и гибкие. Позволяют писать сложные условия.

```sql
-- Пользовательский код должен быть положительным и не больше 1000
ALTER TABLE orders ADD CONSTRAINT orders_price_check 
    CHECK (total_price > 0 AND total_price <= 1000000);

-- Статус заказа должен быть из разрешенного набора
ALTER TABLE orders ADD CONSTRAINT orders_status_check 
    CHECK (status IN ('pending', 'paid', 'shipped', 'delivered', 'cancelled'));

-- Дата окончания не может быть раньше даты начала
ALTER TABLE reservations ADD CONSTRAINT reservations_dates_check 
    CHECK (end_date >= start_date);
```

**Когда использовать:**
*   Валидация значений (диапазоны, перечисления).
*   Связанные поля (например, `if status = 'paid', then payment_id IS NOT NULL`).

#### Б. `UNIQUE` Constraints
Гарантируют уникальность комбинации полей.

```sql
-- Email должен быть уникальным
ALTER TABLE users ADD CONSTRAINT users_email_unique 
    UNIQUE (email);

-- Нельзя иметь два активных абонемента в одном месяце
ALTER TABLE subscriptions ADD CONSTRAINT subscriptions_active_unique 
    UNIQUE (user_id, start_month) 
    WHERE status = 'active'; -- Частичный индекс (PostgreSQL 11+)
```

**Когда использовать:**
*   Бизнес-правила «уникальность» (email, login, ИНН).
*   Предотвращение дубликатов транзакций (`idempotency_key`).

#### В. `FOREIGN KEY` Constraints
Гарантируют ссылочную целостность.

```sql
-- Order всегда ссылается на существующего User
ALTER TABLE orders ADD CONSTRAINT orders_user_fk 
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE; -- Если пользователь удален, заказы тоже
```

**Когда использовать:**
*   Всегда, если у вас есть связь между таблицами.
*   `ON DELETE` определяет поведение при удалении родителя.

#### Г. `NOT NULL` Constraints
Базовая гарантия наличия данных.

```sql
ALTER TABLE users ADD CONSTRAINT users_email_not_null 
    CHECK (email IS NOT NULL);
```

#### Д. Триггеры (Triggers)
Если `CHECK` не хватает (например, нужно вызвать функцию или логику).

```sql
-- Пример: Автоматическое обновление updated_at
CREATE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_updated_at
    BEFORE UPDATE ON orders
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

**Когда использовать:**
*   Автоматическое заполнение полей (`created_at`, `updated_at`).
*   Сложная логика, которую сложно выразить в `CHECK`.

**Минус триггеров:** Скрытая магия. Трудно отлаживать, сложно переносить между БД. Используйте только если нет другого выбора.

#### Е. Индексы с уникальностью (Partial Indexes)
Мощный инструмент PostgreSQL.

```sql
-- Уникальность только для активных пользователей
CREATE UNIQUE INDEX idx_active_users_email 
    ON users (email) 
    WHERE status = 'active';
```

**Когда использовать:**
*   Уникальность с условием (только для активных, только для определенных типов).
*   Это быстрее, чем `CHECK` + `UNIQUE`, потому что индекс используется для поиска.

---

### 3. В каких случаях ограничения БД **критичны**?

1.  **Финансовые операции:** `CHECK (amount > 0)`, `UNIQUE (transaction_id)`.
2.  **Справочники:** `UNIQUE (code)`, `CHECK (code IN ('USD', 'EUR'))`.
3.  **Ссылочная целостность:** `FOREIGN KEY` всегда.
4.  **Бизнес-правила, которые нельзя нарушать:** Например, «пользователь не может быть и клиентом, и поставщиком одновременно» → `CHECK` или `UNIQUE`.
5.  **Идентификаторы:** `UNIQUE` на `idempotency_key`, `request_id`.

---

### 4. В каких случаях ограничения БД **не нужны** или **вредны**?

1.  **Сложная бизнес-логика, которая часто меняется:**
    *   Пример: «Пользователь может иметь не более 3 активных подписок, но если он VIP — не более 5».
    *   Такое сложно выразить в `CHECK`. Лучше держать в коде.
    *   **Почему вредно:** При изменении логики нужно менять схему БД (миграции, downtime), а не просто код.

2.  **Валидация, которая зависит от контекста:**
    *   Пример: «Email должен быть корпоративным, если роль = 'EMPLOYEE'».
    *   `CHECK` не знает о ролях из других таблиц (или знает, но это сложно).
    *   Лучше валидировать в коде.

3.  **Производительность при массовых записях:**
    *   Если вы делаете bulk-insert миллиона строк, `CHECK` constraints замедляют процесс.
    *   **Решение:** Временно отключить ограничения (если это безопасно) или делать валидацию в коде и один раз проверять в конце.

4.  **Нет возможности наложить ограничение:**
    *   Например, уникальность зависит от времени (только в последний час). `CHECK` не умеет обращаться к `NOW()` в условии уникальности (индексы умеют, но это сложно).

---

### 5. Стратегия: Как балансировать?

#### Принцип: «Fail Fast»

1.  **Код приложения:** Делает **основную** валидацию. Дает пользователю понятные сообщения об ошибках («Введите корректный email»). Это **UX**.
2.  **БД:** Делает **защитную** валидацию. Бросает технические ошибки (`ConstraintViolationException`), которые логируются и считаются багами. Это **безопасность**.

#### Пример

**Код:**
```php
class User {
    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format'); // Понятное сообщение
        }
        $this->email = $email;
    }
}
```

**БД:**
```sql
ALTER TABLE users ADD CONSTRAINT users_email_check CHECK (email LIKE '%@%.%');
```

**Сценарий:**
*   Пользователь вводит `not-an-email` → Код бросает `InvalidArgumentException` → Пользователь видит «Invalid email format». **Нормально**.
*   Администратор делает `INSERT INTO users (email) VALUES ('not-an-email')` → БД бросает `ConstraintViolationException` → Вы видите баг в логах, но данные не записаны. **Хорошо**.
*   Ваш код имеет баг и пропускает валидацию → БД останавливает запись. **Отлично**.

---

### Итог

| Уровень | Задача | Средства | Когда использовать |
| :--- | :--- | :--- | :--- |
| **Код** | UX, понятные ошибки, сложная бизнес-логика | Validation (Symfony Validator, Zends Framework) | Всегда |
| **БД** | Безопасность, целостность, защита от багов | `CHECK`, `UNIQUE`, `FK`, `NOT NULL` | Всегда для базовых правил |
| **Триггеры** | Автоматика, сложная логика | Triggers | Редко, если нет другого выбора |

**Запомните:** Ограничения БД — это не замена валидации в коде, а **страховка** от ее отсутствия или ошибок.

> ⏱ 20.6s · prompt 32045 tok · output 2430 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленного запроса в PostgreSQL — это не магия, а системный инженерный процесс. Я подхожу к этому как детектив: сначала собираю улики (логи, план), потом строю гипотезы, а потом тестирую.

Вот мой пошаговый процесс.

---

### Шаг 1: Сбор фактов (Не угадываем, а измеряем)

#### 1.1. `EXPLAIN (ANALYZE, BUFFERS)`
Это **самый важный инструмент**.
*   `EXPLAIN` — показывает, как PostgreSQL *думает*, что будет делать (план).
*   `ANALYZE` — реально выполняет запрос и показывает **фактическое** время выполнения, количество обработанных строк и расход памяти/буферов.
*   `BUFFERS` — показывает, сколько данных было прочитано из RAM (shared_buffers) и сколько — с диска (I/O).

**Команда:**
```sql
EXPLAIN (ANALYZE, BUFFERS) SELECT ...
```

**На что смотрим:**
*   **`Total execution time`:** Сколько времени заняло.
*   **`Rows removed` / `Rows examined`:** Сколько строк просмотрели, а сколько вернули. Если просмотрели 100 000 строк, а вернули 1 — это плохо (отсутствие фильтрации).
*   **`Buffers: shared read` vs `shared hit`:** Если `shared read` большое — данные идут с диска (медленно). Если `shared hit` — из кэша (быстро).
*   **Типы нод:**
    *   `Seq Scan` (полное сканирование) — часто причина замедления на больших таблицах.
    *   `Index Scan` — хорошо.
    *   `Hash Join`, `Merge Join`, `Nested Loop` — разные стратегии соединения.

#### 1.2. `pg_stat_statements`
Расширение, которое хранит статистику по всем выполненным запросам.

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
```

**Запрос:**
```sql
SELECT 
    calls,
    round((total_exec_time / 1000)::numeric, 2) AS total_time_sec,
    round((mean_exec_time / 1000)::numeric, 2) AS mean_time_sec,
    rows,
    left(query, 100) AS query_text
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;
```

**Зачем:**
*   Находит **самые медленные** запросы по суммарному времени.
*   Находит **частые** запросы, которые могут быть быстрыми, но суммарно съедают ресурсы.
*   Помогает отличить «редкий медленный запрос» от «частого среднего запроса».

#### 1.3. `pg_stat_activity`
Показывает, что **прямо сейчас** делают сессии.

```sql
SELECT pid, user, state, query_start, query
FROM pg_stat_activity
WHERE state != 'idle'
ORDER BY query_start;
```

**Зачем:**
*   Видим блокировки (waiting on lock).
*   Видим долгие транзакции, которые могут удерживать блокировки.
*   Видим повторяющиеся запросы.

---

### Шаг 2: Анализ плана запроса (Чтение `EXPLAIN`)

#### Типовые причины замедления:

#### 2.1. `Seq Scan` на большой таблице
*   **Симптом:** `Seq Scan on orders` с `rows=1,000,000`.
*   **Причина:** Отсутствует индекс по фильтрам `WHERE` или `JOIN`.
*   **Решение:** Создать индекс.
    ```sql
    CREATE INDEX idx_orders_status ON orders (status);
    ```

#### 2.2. Плохой выбор индекса (Index Bloat)
*   **Симптом:** `Index Scan`, но `Rows removed` сильно больше ожидаемого.
*   **Причина:** Индекс устарел, статистика PostgreSQL устарела.
*   **Решение:**
    ```sql
    ANALYZE orders; -- Обновить статистику
    ```

#### 2.3. `Hash Join` вместо `Nested Loop`
*   **Симптом:** Большой `Hash` + `Hash Cond`.
*   **Причина:** PostgreSQL решил, что лучше построить хэш-таблицу по одной стороне, чем делать N+1 сканирование.
*   **Когда плохо:** Если одна сторона очень большая, а другая маленькая — `Nested Loop` может быть быстрее.
*   **Решение:** Проверить, можно ли заменить `Hash Join` на `Nested Loop` с хорошим индексом.

#### 2.4. `Sort` / `Materialize`
*   **Симптом:** `Sort` с большим `Sort Method: external merge disk`.
*   **Причина:** Не хватает памяти для сортировки в RAM, PostgreSQL пишет на диск.
*   **Решение:**
    *   Увеличить `work_mem` (но осторожно, это на сессию).
    *   Оптимизировать запрос, чтобы сортировка стала дешевле (например, добавить индекс по `ORDER BY`).

#### 2.5. `Function Call` / `Expression Index`
*   **Симптом:** `WHERE LOWER(email) = 'test@example.com'`.
*   **Причина:** PostgreSQL не может использовать обычный индекс на `email`, потому что применяется функция.
*   **Решение:** Создать функциональный индекс.
    ```sql
    CREATE INDEX idx_users_email_lower ON users (LOWER(email));
    ```

---

### Шаг 3: Оптимизация

#### 3.1. Индексы
*   **Композитные индексы:** Порядок важен!
    ```sql
    -- Для WHERE user_id = 5 AND created_at > '2023-01-01'
    CREATE INDEX idx_orders_user_date ON orders (user_id, created_at);
    ```
*   **Частичные индексы (Partial Indexes):** Только для нужных строк.
    ```sql
    -- Только для активных пользователей
    CREATE INDEX idx_active_users ON users (email) WHERE status = 'active';
    ```
*   **GIN/GiST:** Для полнотекстового поиска, JSON, геоданных.

#### 3.2. Переписывание запроса
*   **`EXISTS` вместо `IN`:**
    ```sql
    -- Плохо (если подзапрос большой)
    SELECT * FROM orders WHERE user_id IN (SELECT id FROM users WHERE ...);
    
    -- Лучше
    SELECT * FROM orders o WHERE EXISTS (
        SELECT 1 FROM users u WHERE u.id = o.user_id AND ...
    );
    ```
*   **`LIMIT` рано:**
    ```sql
    -- Плохо: Сортируем все 100 000 строк, потом берем 10
    SELECT * FROM orders ORDER BY created_at DESC LIMIT 10;
    
    -- Лучше: Если есть индекс по created_at, это ок.
    -- Но если сортировка сложная — лучше сначала отфильтровать.
    ```
*   **Избегание `SELECT *`:** Запрашивайте только нужные колонки. Это уменьшает объем данных и может позволить использовать **Covering Index**.

#### 3.3. Настройки PostgreSQL
*   `work_mem`: Память на сортировку и соединения. Увеличьте, если видите `external merge disk`.
*   `shared_buffers`: RAM для кэша. Обычно 25-40% от RAM сервера.
*   `effective_cache_size`: Сколько кэша PostgreSQL может использовать для планирования. Обычно 50-75% от RAM.
*   `random_page_cost`: Стоимость случайного чтения с диска. Если у вас SSD, уменьшите (по умолчанию 4, для SSD лучше 1.1-1.5).

#### 3.4. Параллелизм
*   `max_parallel_workers_per_gather`: Позволяет PostgreSQL использовать несколько CPU-ядер для одного запроса.
*   Включается автоматически, если запрос тяжелый. Проверьте, что он работает.

---

### Шаг 4: Мониторинг и профилактика

1.  **Алерты по `pg_stat_statements`:** Если `mean_exec_time` вырос — алерт.
2.  **Алерты по блокировкам:** `pg_locks`.
3.  **Регулярный `VACUUM`:** PostgreSQL использует MVCC, старые версии строк накапливаются. `VACUUM` их очищает.
4.  **Автоматический `ANALYZE`:** Чтобы статистика была актуальной.

---

### Пример полного цикла

**Проблема:** Запрос на список заказов пользователя тормозит.

**1. EXPLAIN:**
```
Limit  (cost=0.57..12.34 rows=10 width=100) (actual time=0.050..1500.200 rows=10 loops=1)
  Buffers: shared read=100000 shared hit=5000
  ->  Sort  (cost=10000.00..20000.00 rows=5000 width=100)
        Sort Key: created_at DESC
        Sort Method: external merge disk  Memory: 30kB  Disk: 10000kB
        ->  Seq Scan on orders  (cost=0.00..8000.00 rows=5000 width=100)
              Filter: (user_id = 5)
```

**Анализ:**
*   `Seq Scan` — плохо.
*   `Filter: user_id = 5` — PostgreSQL прочитал все строки, потом отфильтровал.
*   `external merge disk` — сортировка ушла на диск.

**Решение:**
1.  Создать индекс:
    ```sql
    CREATE INDEX idx_orders_user_date ON orders (user_id, created_at DESC);
    ```
2.  Перезапустить `EXPLAIN`:
    ```
    Limit  (cost=0.57..12.34 rows=10 width=100) (actual time=0.050..0.200 rows=10 loops=1)
      ->  Index Scan using idx_orders_user_date on orders  (cost=0.57..100.00 rows=5000 width=100)
            Index Cond: (user_id = 5)
    ```

**Результат:** С 1500 мс до 0.2 мс.

---

### Итог

1.  **Соберите данные:** `EXPLAIN ANALYZE`, `pg_stat_statements`.
2.  **Найдите узкое место:** `Seq Scan`, `Sort`, `Hash Join`.
3.  **Оптимизируйте:** Индексы, переписывание запроса, настройки.
4.  **Мониторьте:** Чтобы проблема не вернулась.

> ⏱ 19.8s · prompt 34521 tok · output 2569 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD — это кластер связанных сущностей и объектов-значений, который рассматривается как **единица целостности данных**.

### Ключевые принципы агрегата:

1.  **Корень (Root Aggregate):** Единственный сущность в агрегате, которую могут ссылаться внешние объекты. Все другие сущности внутри агрегата — «дочерние» и не должны иметь прямых ссылок извне.
2.  **Граница целостности:** Бизнес-правила и инварианты (invariants) гарантируются **только** в рамках одного агрегата. Если правило касается данных из двух разных агрегатов — это нарушение границы.
3.  **Маленький размер:** Агрегат должен быть как можно меньшим, чтобы избежать проблем с блокировками и сложностью транзакций.
4.  **Идентификаторы:** Внешние ссылки ведут только на **ID** агрегата, а не на объект.

---

### Как определять границы агрегата?

Я использую следующий алгоритм:

1.  **Что должно быть неизменным (Invariants)?**
    Какие бизнес-правила нельзя нарушать ни при каких обстоятельствах? Все сущности, участвующие в этих правилах, должны быть в **одном** агрегате.

2.  **Что меняется вместе?**
    Если два объекта почти всегда обновляются в одной транзакции — они, скорее всего, один агрегат.

3.  **Что может быть загружено отдельно?**
    Если объект нужен для разных сценариев независимо — это разные агрегаты.

4.  **Каков размер?**
    Если агрегат стал слишком большим (десятки сущностей) — его нужно разделить.

---

### Пример: Агрегат `Order`

#### Структура:

```
Order (Root)
├── OrderItem (Child Entity)
├── Money (Value Object)
├── Address (Value Object)
└── OrderStatus (Enum)
```

#### Инварианты (Bussiness Rules):

1.  Сумма заказа должна быть равна сумме всех позиций.
2.  Заказ нельзя изменить, если он уже отправлен.
3.  Позиция заказа не может существовать без заказа.

#### Почему это один агрегат?

*   **Invariants** нарушатся, если `Order` и `OrderItem` будут в разных агрегатах. Например, если `OrderItem` изменит цену, а `Order` не пересчитает сумму — нарушение инварианта.
*   **Изменяются вместе:** При изменении позиции меняется и сумма заказа.
*   **Корень:** Внешний мир ссылается только на `Order`, а не на `OrderItem`.

#### Код (упрощенно):

```php
class Order
{
    private string $id;
    private array $items;
    private Money $total;
    private OrderStatus $status;

    public function addItem(ProductId $productId, int $quantity, Money $price): void
    {
        if ($this->status === OrderStatus::SHIPPED) {
            throw new DomainException('Cannot modify shipped order');
        }

        $this->items[] = new OrderItem($productId, $quantity, $price);
        $this->recalculateTotal();
    }

    private function recalculateTotal(): void
    {
        $newTotal = Money::zero();
        foreach ($this->items as $item) {
            $newTotal = $newTotal->add($item->getTotal());
        }
        $this->total = $newTotal;
    }

    public function ship(): void
    {
        $this->status = OrderStatus::SHIPPED;
    }
}
```

**Важно:** `OrderItem` не имеет публичных сеттеров для изменения цены/количества после создания. Все изменения идут через `Order`.

---

### Типовая ошибка: Слишком большой агрегат («God Aggregate»)

#### Пример ошибки:

Агрегат `Shop` включает:
*   `User`
*   `Product`
*   `Order`
*   `Payment`
*   `Inventory`

#### Почему это плохо?

1.  **Проблемы с блокировками:** При изменении `Inventory` блокируется весь агрегат `Shop`, и другие пользователи не могут оформить заказ.
2.  **Сложность транзакций:** Одна транзакция должна быть короткой. Если в ней изменяется и `User`, и `Product`, и `Order` — транзакция долго живет, создавая конфликты.
3.  **Нарушение границ:** Правила `User` (например, «пароль должен быть сложным») ничего не имеют общего с правилами `Order`.

#### Как правильно разбить?

| Агрегат | Содержит | Инварианты |
| :--- | :--- | :--- |
| **Order** | `OrderItem`, `Money`, `Status` | Сумма, статус, неизменяемость после отправки |
| **Product** | `Name`, `Price`, `Stock` | Сток >= 0, цена > 0 |
| **User** | `Email`, `Password`, `Profile` | Валидность email, сложности пароля |
| **Payment** | `Amount`, `Method`, `Status` | Валидность платежа |

#### Как они связаны?

*   `Order` содержит `UserId` (ID, а не объект `User`).
*   `Order` содержит `ProductId` (ID, а не объект `Product`).
*   **Связь через ID:** Это ключевое правило DDD. Агрегаты ссылаются друг на друга только по идентификаторам.

#### А как проверить инвариант «Сток не может быть отрицательным» при оформлении заказа?

Здесь возникает **консистентность событий** ( eventual consistency).

1.  `Order` публикует событие `OrderPlaced`.
2.  `Product` подписывается на это событие и уменьшает сток.
3.  Если стока нет — `Product` публикует `StockInsufficient`.
4.  `Order` подписывается на `StockInsufficient` и отменяет заказ.

**Это не синхронная блокировка, а асинхронная согласованность.** Это нормально и часто даже предпочтительнее.

---

### Итог

**Агрегат** — это **граница сильной консистентности**.

*   **Внутри агрегата:** Данные всегда консистентны (сильная консистентность).
*   **Между агрегатами:** Данные могут быть временно неконсистентными (слабая/событийная консистентность).

**Главный принцип:** Делите агрегаты как можно **меньше**, чтобы:
1.  Уменьшить блокировки.
2.  Упростить транзакции.
3.  Избежать «бога» (God Object).

Если вы сомневаетесь, нужно ли включить сущность в агрегат — спросите себя: **«Если эта сущность изменится, могут ли нарушиться инварианты других сущностей внутри этого агрегата?»** Если да — включайте. Если нет — оставляйте отдельно.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это классическая проблема **конкурентного доступа (Concurrency)** в DDD. В реляционных БД с MVCC (Multi-Version Concurrency Control), таких как PostgreSQL или MySQL (InnoDB), два процесса могут одновременно прочитать данные, внести изменения и попытаться записать их.

### Что произойдет при столкновении?

1.  **Lost Update (Потерянное обновление):** Процесс A читает значение `10`. Процесс B читает значение `10`. A пишет `15`. B пишет `12`. Финальное значение `12`, но изменение A (`+5`) потеряно. Это **катастрофа** для бизнес-логики.
2.  **Deadlock (Зависание):** Процесс A блокирует строку 1 и ждет строку 2. Процесс B блокирует строку 2 и ждет строку 1. Оба зависают. БД убивает одного из них (`ERROR: deadlock detected`).
3.  **Dirty Read:** Один процесс видит незакоммиченные данные другого (зависит от уровня изоляции).

---

### Как проектировать с учетом этого?

В DDD мы используем **Оптимистичную блокировку (Optimistic Locking)** в сочетании с **версионированием**.

#### 1. Механизм: Версионирование (Versioning)

Добавляем в корень агрегата поле `version`.
*   При каждом изменении агрегата `version` увеличивается на 1.
*   При сохранении мы передаем ожидаемую версию.
*   Если версия в БД не совпадает с ожидаемой — значит, кто-то другой уже изменил данные.

**Пример в PHP (Doctrine):**

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 0;

    // ... другие поля
}
```

#### 2. Логика сохранения с проверкой версии

Мы не используем стандартный `em->flush()`, а делаем запрос с условием версии.

**Вариант А: Ручная реализация (SQL)**

```sql
UPDATE orders 
SET status = 'paid', version = version + 1 
WHERE id = 'order_123' AND version = 5;
```

*   Если `affected rows = 1` — успех.
*   Если `affected rows = 0` — конфликт.

**Вариант Б: Через Doctrine (Listener/Subscriber)**

Doctrine не имеет встроенной оптимистичной блокировки «из коробки», но можно использовать `PostFlushEvent` или кастомный репозиторий.

```php
class OrderRepository
{
    public function save(Order $order): void
    {
        $sql = 'UPDATE orders SET status = :status, version = version + 1 WHERE id = :id AND version = :version';
        
        $result = $this->connection->executeStatement(
            $sql,
            [
                'status' => $order->getStatus(),
                'id' => $order->getId(),
                'version' => $order->getVersion()
            ]
        );

        if ($result === 0) {
            throw new ConcurrencyException('Order was modified by another process');
        }
        
        // Увеличиваем версию в объекте
        $order->bumpVersion();
    }
}
```

#### 3. Что делать при конфликте?

Когда вы получаете `ConcurrencyException`, у вас есть три варианта:

| Стратегия | Описание | Когда использовать |
| :--- | :--- | :--- |
| **Retry (Повтор)** | Перечитать данные, переприменить изменения, попытаться сохранить снова. | Для коротких операций, где конфликт маловероятен. |
| **Fail Fast (Сбой)** | Бросить ошибку `409 Conflict` пользователю. | Для операций, где повтор невозможен (например, оплата). |
| **Merge (Слияние)** | Сложная логика слияния изменений. | Редко, только для специфических полей (например, комментирование). |

**Рекомендация:** Для большинства случаев — **Retry с ограничением попыток** (например, 3 раза с экспоненциальной задержкой) или **Fail Fast**.

---

### Почему не Пессимистичная блокировка (`SELECT FOR UPDATE`)?

Пессимистичная блокировка (`SELECT ... FOR UPDATE`) работает, но в DDD она **нежелательна**:

1.  **Масштабируемость:** Блокировки удерживаются на время транзакции. Если транзакция длинная (например, ожидает ответ от внешнего API), блокировка держится долго.
2.  **Deadlocks:** Высокий риск взаимных блокировок.
3.  **Нарушение принципа «Маленьких агрегатов»:** Если агрегат большой, блокировка держится дольше.

**Оптимистичная блокировка:**
*   Не блокирует данные при чтении.
*   Конфликт обнаруживается только при записи.
*   Лучше масштабируется при низкой частоте конфликтов.

---

### Специфичные сценарии

#### Сценарий 1: Инкремент (Очередь)
Два процесса делают `increment()` на одном счетчике.

**Решение:**
```sql
UPDATE counters SET value = value + 1 WHERE key = 'x';
```
Это **атомарная** операция на уровне БД. Здесь версионирование не нужно, потому что мы не читаем-пишем, а делаем атомарное изменение.

#### Сценарий 2: Смена статуса
Два процесса пытаются сменить статус `Order` с `pending` на `paid`.

**Решение:**
```sql
UPDATE orders SET status = 'paid' WHERE id = '123' AND status = 'pending';
```
*   Если `affected rows = 1` — один из процессов изменил статус.
*   Если `affected rows = 0` — статус уже изменился (конфликт).

Это **оптимистичная блокировка через состояние**.

#### Сценарий 3: Сложная бизнес-логика
Изменение цены, скидки, суммы.

**Решение:**
Версионирование + Retry.
1.  Читаем `Order` (version 5).
2.  Вычисляем новую сумму.
3.  Пытаемся записать с `WHERE version = 5`.
4.  Если конфликт — перечитываем `Order` (version 6), пересчитываем сумму, пытаемся записать с `WHERE version = 6`.

---

### Итог: Как проектировать

1.  **Добавьте `version`** в корень агрегата.
2.  **Используйте оптимистичную блокировку** при сохранении.
3.  **Обрабатывайте конфликты:**
    *   Для idempotent операций (можно повторять) — Retry.
    *   Для non-idempotent операций (нельзя повторять) — Fail Fast.
4.  **Избегайте пессимистичных блокировок**, если нет сильных оснований.
5.  **Делайте агрегаты маленькими**, чтобы сократить время жизни транзакции.
6.  **Используйте атомарные SQL-операции** для простых случаев (increment, status change).

Это позволит вашей системе быть **высоконагруженной** и **корректной** в условиях конкуренции.

> ⏱ 13.8s · prompt 37132 tok · output 1642 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это механизм **отложенной коммуникации** между частями системы. Они позволяют «развязать» бизнес-логику: одна часть системы не знает о существовании другой, но может реагировать на изменения состояния домена.

### 1. Когда публиковать доменные события?

Доменное событие — это **факт**, который уже произошел и изменил состояние домена.

**Правило:** Публикуйте событие **после** того, как состояние агрегата изменено и сохранено в базу данных.

**Пример:**
```php
class Order
{
    private array $events = [];

    public function place(): void
    {
        $this->status = 'placed';
        $this->events[] = new OrderPlacedEvent($this->getId());
    }

    public function collectEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
```

**Почему не внутри метода?**
Потому что событие должно быть «выбросом» из агрегата, а не его частью. Агрегат знает, что произошло. Диспетчер решает, что с этим делать.

---

### 2. Как обрабатывать события?

В Symfony это делается через **Messenger** и **Event Dispatcher**.

#### Архитектура:

1.  **Сбор событий:** Агрегат собирает события.
2.  **Публикация:** Сервис (Application Service) собирает события из агрегата и публикует их.
3.  **Диспетчеризация:** `MessageBus` отправляет события в очередь (или синхронно).
4.  **Обработка:** `Handler` (подписчик) выполняет действие (отправляет письмо, индексирует, уведомляет).

**Пример:**

**Сервис:**
```php
class OrderService
{
    public function __construct(
        private MessageBusInterface $bus,
        private EntityManagerInterface $em
    ) {}

    public function placeOrder(string $customerId, array $items): Order
    {
        $order = new Order($customerId);
        foreach ($items as $item) {
            $order->addItem($item['id'], $item['qty'], new Money($item['price'], 'USD'));
        }

        $this->em->persist($order);
        $this->em->flush(); // 1. Сохраняем состояние

        // 2. Публикуем события ПОСЛЕ сохранения
        foreach ($order->collectEvents() as $event) {
            $this->bus->dispatch($event);
        }

        return $order;
    }
}
```

**Обработчик:**
```php
class OrderPlacedHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexer $searcher
    ) {}

    public function __invoke(OrderPlacedEvent $event): void
    {
        $orderId = $event->getOrderId();
        
        // Логика: письмо, индексация, уведомление
        $this->mailer->send(...);
        $this->searcher->index($orderId);
    }
}
```

---

### 3. Связь с транзакциями БД (Критический момент)

Здесь самая частая ошибка: **«Я опубликовал событие, а потом БД упала»**.

#### Сценарий ошибки:
1.  `em->flush()` — успех.
2.  `bus->dispatch()` — событие отправлено в очередь.
3.  `em->commit()` — **ошибка** (например, нарушение `CHECK` constraint).
4.  **Результат:** Данные не закоммичены, но событие уже обработано. Письмо отправлено, индекс обновлен, хотя заказа не существует.

#### Правильное решение: Transactional Outbox Pattern

Это **единственный надежный способ** связать события с транзакциями.

**Идея:**
1.  Событие сохраняется **в ту же транзакцию**, что и данные.
2.  Отдельный процесс (Outbox Publisher) периодически читает таблицу `outbox` и публикует события в очередь.
3.  После успешной публикации — запись удаляется из `outbox`.

**Пример реализации:**

**Таблица:**
```sql
CREATE TABLE domain_events_outbox (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    aggregate_id VARCHAR(255) NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    published_at DATETIME NULL
);
```

**Сервис:**
```php
class OrderService
{
    public function placeOrder(string $customerId, array $items): Order
    {
        $order = new Order($customerId);
        // ... добавление позиций ...

        $this->em->persist($order);

        // Сохраняем событие В ТУЖЕ ТРАНСАКЦИЮ
        $eventEntity = new DomainEventOutbox(
            $order->getId(),
            'OrderPlacedEvent',
            json_encode(['orderId' => $order->getId()])
        );
        $this->em->persist($eventEntity);

        $this->em->flush();
        // Здесь коммит происходит атомарно: 
        // Либо оба (Order + Event) записываются, либо ни одного.
        
        return $order;
    }
}
```

**Outbox Publisher (отдельный процесс):**
```php
class OutboxPublisherWorker
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus
    ) {}

    public function run(): void
    {
        while (true) {
            $events = $this->em->getRepository(DomainEventOutbox::class)
                ->findUnpublished(100);

            foreach ($events as $event) {
                try {
                    $bus->dispatch($this->deserialize($event));
                    $event->markAsPublished();
                    $this->em->persist($event);
                    $this->em->flush();
                } catch (\Exception $e) {
                    // Логгируем и продолжаем (или ставим в dead-letter)
                }
            }

            sleep(1);
        }
    }
}
```

**Почему это лучше?**
*   **Атомарность:** Событие и данные записываются вместе.
*   **Надежность:** Если сервер упадет после `flush`, но до публикации — событие останется в `outbox` и будет опубликовано при перезапуске.
*   **Идемпотентность:** Обработчики должны быть идемпотентными (см. ниже).

---

### 4. Какие проблемы решает подход с событиями?

1.  **Развязка (Decoupling):** `Order` не знает, что кто-то отправляет письма.
2.  **Масштабируемость:** Обработчики можно запускать параллельно.
3.  **Отказоустойчивость:** Если почтовый сервис упал, заказ не упадет. Письмо будет отправлено позже (retry).
4.  **Аудит:** Все изменения состояния фиксируются в `outbox`.
5.  **Расширяемость:** Можно добавить нового подписчика, не меняя код агрегата.

---

### 5. Какие проблемы создает этот подход?

1.  **Сложность отладки:** Ошибка в обработчике не видна в основном потоке.
2.  **Задержка:** Пользователь может увидеть «заказ создан», а письмо придет через 5 минут.
3.  **Идемпотентность:** Если событие будет опубликовано дважды, обработчик выполнится дважды. Нужно делать обработчики идемпотентными (проверять, было ли уже обработано).
4.  **Порядок:** События могут обрабатываться не по порядку (если использовать очередь).
5.  **Сложность транзакций:** Нужно внедрять Outbox Pattern.

---

### 6. Идемпотентность обработчиков

Это **критическое требование**.

**Пример:**
```php
class OrderPlacedHandler
{
    public function __invoke(OrderPlacedEvent $event): void
    {
        $orderId = $event->getOrderId();
        
        // Проверяем, было ли уже отправлено письмо
        if ($this->wasEmailSent($orderId)) {
            return;
        }

        $this->mailer->send(...);
        $this->markEmailAsSent($orderId);
    }
}
```

**Как проверить?**
*   Таблица `processed_events` с хэшем события.
*   Или уникальный `event_id` в событии.

---

### Итог

| Компонент | Роль |
| :--- | :--- |
| **Агрегат** | Генерирует события, не зная о подписчиках. |
| **Сервис** | Сохраняет агрегат и событие в одной транзакции (Outbox). |
| **Outbox Publisher** | Публикует события из БД в очередь. |
| **Handler** | Обрабатывает события, идемпотентно. |

**Запомните:**
*   События — это **факты**, а не команды.
*   Публикация — **после** сохранения.
*   Используйте **Outbox Pattern** для надежности.
*   Делайте обработчики **идемпотентными**.

> ⏱ 16.0s · prompt 40607 tok · output 2186 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальный вопрос DDD, от которого зависит вся архитектура вашего домена. Многие разработчики путают эти концепции, что приводит к «анемичным доменным моделям» или, наоборот, к чрезмерной абстракции.

Давайте разберем по косточкам.

---

### 1. Чем Value Object (VO) отличается от Entity?

| Критерий | **Entity (Сущность)** | **Value Object (Объект-значение)** |
| :--- | :--- | :--- |
| **Идентичность** | Определена **ID** (например, `User #105`). | Определена **значением** (например, `Email: "test@mail.com"`). |
| **Изменяемость** | Изменяема во времени (статус меняется, данные обновляются). | **Неизменяема** (Immutable). Чтобы изменить значение — создаем новый объект. |
| **Равенство** | Два объекта равны, если имеют **один ID**. | Два объекта равны, если **все их поля совпадают**. |
| **Жизненный цикл** | Долгий (создание, обновление, удаление). | Короткий (создается на лету, мусоросборщик забирает). |
| **Примеры** | `User`, `Order`, `Product`, `BankAccount`. | `Money`, `Email`, `Address`, `Date`, `Dimension`. |

#### Ключевое различие на примере:

**Entity `User`:**
```php
$user1 = new User('id_123', 'John', 'john@mail.com');
$user2 = new User('id_123', 'John', 'john@mail.com');

$user1 == $user2; // TRUE (у них один ID)
```

**Value Object `Email`:**
```php
$email1 = new Email('john@mail.com');
$email2 = new Email('john@mail.com');

$email1 == $email2; // TRUE (значения одинаковые, идентичность не важна)
```

#### Почему VO неизменяемы?
Потому что они представляют **значение**, а не «контейнер для состояния».
*   Если у вас есть `Money(100, 'USD')` и вы хотите изменить сумму на `200` — вы не модифицируете старый объект, а создаете новый: `Money(200, 'USD')`.
*   Это делает код **безопасным в многопоточной среде** и упрощает отладку (объекты не меняются под ногами).

---

### 2. Как решать: Entity или Value Object?

Используйте следующие вопросы:

#### Вопрос 1: «Должен ли я отслеживать, как этот объект менялся во времени?»
*   **Да** → **Entity**. (Пример: История заказа, лог действий пользователя).
*   **Нет** → **Value Object**. (Пример: Текущая цена товара, email адрес).

#### Вопрос 2: «Есть ли у этого объекта уникальный идентификатор, который известен всему приложению?»
*   **Да** → **Entity**. (Пример: `Product ID`, `User ID`).
*   **Нет** → **Value Object**. (Пример: `Address`, `Money`).

#### Вопрос 3: «Можно ли заменить один объект другим с тем же значением?»
*   **Да** → **Value Object**. (Пример: Меняем email на email, это не «тот же email», это новый email, но для системы это просто значение).
*   **Нет** → **Entity**. (Пример: Менять `User #105` на `User #106` нельзя, это разные сущности).

#### Вопрос 4: «Является ли он частью агрегата или самостоятельной сущностью?»
*   Если объект **не имеет смысла вне родителя** и не имеет своего ID — он скорее **Entity (Child)** или **VO**.
*   Если объект **имеет смысл сам по себе** — он **Entity (Root)** или **VO**.

---

### 3. Единый язык (Ubiquitous Language)

**Ubiquitous Language** — это общее понимание терминов, которое используют **разработчики, аналитики и бизнес-пользователи**.

#### Зачем это нужно?
1.  **Устранение двусмысленности:** В бизнесе есть понятие «Клиент». В одной команде это `Customer`, в другой — `Client`, в третьей — `User`. Если все говорят об одном и том же, но разными словами — это баг.
2.  **Связь кода с бизнесом:** Код должен читаться как бизнес-правила.
3.  **Упрощение коммуникации:** Разработчик не должен «переводить» бизнес-требования в технический язык.

#### Как это влияет на код?

**❌ Плохой код (технический язык):**
```php
class User {
    public string $login;
    public string $mail;
    public float $cash;
    
    public function add_cash(float $amount): void
    {
        $this->cash += $amount;
    }
}
```

**✅ Хороший код (единый язык бизнеса):**
```php
class Customer { // Бизнес говорит "Клиент"
    public function __construct(
        private CustomerId $id,
        private EmailAddress $email // VO
    ) {}

    public function charge(Money $amount): void
    {
        // Бизнес говорит "Списываем средства"
    }

    public function hasEnoughBalance(Money $amount): bool
    {
        // Бизнес говорит "Достаточно ли средств?"
    }
}
```

#### Примеры изменений:

| Бизнес-термин | Техническая реализация |
| :--- | :--- |
| **Заказ** | `Order` (Entity) |
| **Позиция заказа** | `OrderLine` (Entity или VO) |
| **Сумма** | `Money` (Value Object) |
| **Скидка** | `Discount` (Value Object) |
| **Оплатить** | `pay()` (метод на `Order`) |
| **Отозвать** | `revoke()` (метод на `Order`) |

**Важно:** Методы и классы должны называться так, как их используют в бизнесе.

---

### 4. Типовые ошибки в выборе Entity vs VO

#### Ошибка 1: Сделали Entity из того, что должно быть VO
**Пример:** `Email`, `Phone`, `Address`.
**Последствие:**
*   Сложное сравнение (`$user1->email->getId() === $user2->email->getId()`).
*   Лишние сущности в БД (отдельные таблицы для email).
*   Изменение email требует обновления ID (что бессмысленно).

**Исправление:** Сделать `EmailAddress` как Value Object.

#### Ошибка 2: Сделали VO из того, что должно быть Entity
**Пример:** `Product` в каталоге.
**Последствие:**
*   Нельзя отслеживать историю изменений.
*   Нельзя ссылаться на `Product` из разных мест (у VO нет ID).
*   Дублирование объектов.

**Исправление:** Сделать `Product` как Entity.

#### Ошибка 3: Смешивание понятий
**Пример:** `User` (Entity) содержит `Address` (VO), но `Address` имеет свой ID.
**Последствие:** Нарушение инкапсуляции. Если `Address` имеет ID, он является Entity, и тогда он должен быть частью агрегата или отдельной сущностью.

**Исправление:** Если `Address` не имеет смысла вне `User` и не меняется часто — сделайте его VO. Если он меняется часто и имеет смысл (например, «адрес доставки» может быть разным для разных заказов) — сделайте его Entity или VO с ID.

---

### 5. Практический пример: Как проектировать домен «Магазин»

#### Концепции домена:
1.  **Customer** — покупатель.
2.  **Product** — товар.
3.  **Order** — заказ.
4.  **OrderLine** — позиция в заказе.
5.  **Money** — сумма.
6.  **ShippingAddress** — адрес доставки.

#### Классификация:

| Концепция | Тип | Почему? |
| :--- | :--- | :--- |
| **Customer** | **Entity** | Имеет ID, может менять email, адрес, баланс. |
| **Product** | **Entity** | Имеет ID, цена может меняться, сток меняется. |
| **Order** | **Entity** | Имеет ID, статус меняется, создается и завершается. |
| **OrderLine** | **Entity** (Child) | Часть `Order`, но может иметь количество, скидку. Может быть VO, если он неизменяем внутри заказа. |
| **Money** | **Value Object** | Значение. `Money(100, 'USD')` == `Money(100, 'USD')`. |
| **ShippingAddress** | **Value Object** | Значение. Если адрес меняется — создаем новый. |

#### Код:

```php
final class Order
{
    private OrderId $id;
    private CustomerId $customerId;
    private array $lines;
    private Money $total;
    private OrderStatus $status;

    public function addLine(ProductId $productId, int $quantity, Money $price): void
    {
        $this->lines[] = new OrderLine($productId, $quantity, $price);
        $this->recalculateTotal();
    }

    private function recalculateTotal(): void
    {
        $this->total = array_sum(array_map(fn($l) => $l->getTotal(), $this->lines));
    }

    public function ship(ShippingAddress $address): void
    {
        $this->status = OrderStatus::SHIPPED;
        $this->shippingAddress = $address; // VO
    }
}

final class Money
{
    private function __construct(
        private int $amount,
        private string $currency
    ) {}

    public static function from(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Currencies must match');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount 
            && $this->currency === $other->currency;
    }
}
```

---

### Итог

1.  **Entity** — это «кто» (идентичность, ID, изменяемость).
2.  **Value Object** — это «что» (значение, неизменяемость, равенство по полям).
3.  **Ubiquitous Language** — это мост между бизнесом и кодом. Код должен читать как бизнес-документация.
4.  **Как выбирать:** Спрашивайте «У этого есть ID? Меняется ли это во времени?». Если да — Entity. Если нет — Value Object.
5.  **Главное правило:** Не заставляйте Entity быть VO и наоборот. Это сломает архитектуру.

> ⏱ 19.8s · prompt 42851 tok · output 2619 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Структура «Чистой Архитектуры» (Clean Architecture) в PHP-приложении — это способ изолировать бизнес-логику от технологий (фреймворков, БД, HTTP). Это позволяет менять Symfony на Laravel, PostgreSQL на MySQL, или RabbitMQ на Kafka, не трогая ядро.

### 1. Слои и правило зависимостей

Правило железное: **Зависимости могут указывать только «внутрь»**.
Внешние слои зависят от внутренних, но внутренние **никогда** не знают о внешних.

```
[Framework & Drivers]  (Symfony, Doctrine, Queue)
         ⬇️ (зависит от)
[Application Use Cases] (Сервисы, Оркестрация)
         ⬇️ (зависит от)
[Domain]               (Сущности, Правила, Репозитории-интерфейсы)
```

---

### 2. Детальное описание слоёв

#### Уровень 1: Domain (Домен)
**Что это:** Чистый PHP. Никаких фреймворков. Только бизнес-логика.
**Что здесь лежит:**
*   **Entities & Value Objects:** `Order`, `Money`, `Address`.
*   **Domain Services:** Логика, которая не принадлежит конкретной сущности (например, `TaxCalculator`).
*   **Repository Interfaces:** Контракты на доступ к данным.
    ```php
    interface OrderRepositoryInterface {
        public function save(Order $order): void;
        public function findById(string $id): ?Order;
    }
    ```
*   **Domain Events:** События, которые генерируются агрегатами.
*   **Exceptions:** Доменные исключения (`InsufficientFundsException`).

**Важно:** Здесь **НЕТ** `use Symfony\...`, `use Doctrine\...`, `use PHPUnit\...` (кроме как в тестах).

#### Уровень 2: Application (Приложение)
**Что это:** Тонкий слой оркестрации. Связывает домен с внешним миром.
**Что здесь лежит:**
*   **Use Cases (Application Services):** Конкретные сценарии работы.
    ```php
    class PlaceOrderService {
        public function __construct(
            private OrderRepositoryInterface $orderRepo,
            private PaymentGatewayInterface $payment // Интерфейс из Application или Domain
        ) {}

        public function placeOrder(PlaceOrderCommand $cmd): OrderId
        {
            $order = new Order();
            // Логика
            $this->orderRepo->save($order);
            return $order->getId();
        }
    }
    ```
*   **DTOs (Commands & Query Results):** Объекты передачи данных.
    *   `PlaceOrderCommand` (входные данные).
    *   `OrderDTO` (выходные данные).
*   **Ports (Interfaces):** Интерфейсы для внешней интеграции (платежи, почта), если они не относятся к чистому домену.

**Важно:** Этот слой **знает** о домене, но не знает о Symfony или Doctrine. Он работает только с интерфейсами.

#### Уровень 3: Frameworks & Drivers (Инфраструктура)
**Что это:** Адаптеры к технологиям.
**Что здесь лежит:**
*   **Adapters (Implementation):** Реализации интерфейсов.
*   **Doctrine Entities & Repositories:** Конкретные реализации `OrderRepositoryInterface`.
*   **Symfony Controllers:** HTTP-эндпоинты.
*   **Symfony Commands:** CLI-команды.
*   **Queue Consumers:** Обработчики сообщений из очереди.
*   **Configuration:** `services.yaml`, `config/routes.yaml`.

**Важно:** Этот слой знает обо всех остальных, но остальные слои не знают о нем.

---

### 3. Куда попадает конкретный компонент?

#### 1. Doctrine (ORM)
*   **Где:** `src/Infrastructure/Doctrine/`
*   **Что именно:**
    *   `OrderEntity` (класс, помеченный `#[ORM\Entity]`).
    *   `DoctrineOrderRepository` (реализует `OrderRepositoryInterface` из Domain).
    *   `MoneyType` (кастомный Doctrine Type).
    *   `Migrations/`.
*   **Почему:** Doctrine — это технология хранения. Она зависит от интерфейсов, определенных в Domain.

#### 2. Symfony Controller
*   **Где:** `src/Controller/` (или `src/Infrastructure/Http/Controller/`)
*   **Что именно:**
    *   `OrderController`.
    *   Логика: Валидация входных данных (Form/DTO) → Создание `PlaceOrderCommand` → Вызов `PlaceOrderService` → Формирование ответа (JSON/HTML).
*   **Почему:** Контроллер — это адаптер HTTP. Он знает о Symfony (Attributes, Request, Response), но не знает о бизнес-правилах заказа.

#### 3. Обработчик очереди (Queue Consumer)
*   **Где:** `src/Messenger/` (или `src/Infrastructure/Messenger/`)
*   **Что именно:**
    *   `UserRegisteredHandler`.
    *   Логика: Десериализация сообщения → Вызов `Application Service` (например, `SendWelcomeEmailService`) → Обработка ошибок.
*   **Почему:** Обработчик — это адаптер к очереди. Он знает о Messenger, но не знает о бизнес-логике отправки писем (это делает сервис).

---

### 4. Пример структуры файлов

```
src/
├── Domain/
│   ├── Entity/
│   │   └── Order.php           # Чистая сущность
│   ├── ValueObject/
│   │   └── Money.php           # Чистый VO
│   ├── Repository/
│   │   └── OrderRepositoryInterface.php
│   ├── Service/
│   │   └── TaxCalculator.php   # Чистая логика
│   └── Event/
│       └── OrderPlaced.php
│
├── Application/
│   ├── Command/
│   │   └── PlaceOrderCommand.php # DTO входа
│   ├── Query/
│   │   └── GetOrderQuery.php     # DTO запроса
│   ├── Result/
│   │   └── OrderDTO.php          # DTO выхода
│   └── Service/
│       └── PlaceOrderService.php # Оркестрация (Use Case)
│
├── Infrastructure/
│   ├── Doctrine/
│   │   ├── Entity/
│   │   │   └── OrderEntity.php   # Маппинг для Doctrine
│   │   ├── Repository/
│   │   │   └── DoctrineOrderRepository.php
│   │   └── Type/
│   │       └── MoneyType.php
│   ├── Http/
│   │   └── Controller/
│   │       └── OrderController.php
│   ├── Messenger/
│   │   └── Handler/
│   │       └── UserRegisteredHandler.php
│   └── Security/
│       └── Voter/
│           └── OrderVoter.php
│
└── Shared/
    └── Kernel.php
```

---

### 5. Как следить, что границы не нарушены?

Это самый важный этап. Если домен начинает импортировать Symfony — архитектура сломана.

#### 1. Статический анализ (PHPStan/Psalm)
Настройте правила, которые запрещают импорты.
В `phpstan.neon` можно использовать кастомные правила, но проще — использовать **архитектурные тесты**.

#### 2. Архитектурные тесты (PHPUnit)
Напишите тесты, которые проверяют структуру кода.

```php
class ArchitectureTest extends TestCase
{
    public function test_domain_does_not_depend_on_symfony(): void
    {
        $domainFiles = $this->getFilesInDirectory('src/Domain');
        
        foreach ($domainFiles as $file) {
            $content = file_get_contents($file);
            
            // Запрещаем использование Symfony в домене
            $this->assertStringNotContainsString(
                'Symfony\\', 
                $content, 
                "File $file should not use Symfony"
            );
            
            // Запрещаем использование Doctrine в домене
            $this->assertStringNotContainsString(
                'Doctrine\\', 
                $content, 
                "File $file should not use Doctrine"
            );
            
            // Запрещаем использование App\Infrastructure
            $this->assertStringNotContainsString(
                'App\\Infrastructure', 
                $content, 
                "File $file should not use Infrastructure"
            );
        }
    }
    
    public function test_application_does_not_depend_on_infrastructure(): void
    {
        // Аналогично для Application слоя
    }
}
```

#### 3. Визуализация зависимостей
Используйте инструменты, которые строят граф зависимостей:
*   **Dependabot:** Не подходит для этого.
*   **PHP-Dependency-Graph:** Специальные скрипты, которые парсят `use` statements.
*   **IDE:** В VSCode/PhpStorm можно посмотреть, кто зависит от кого, но это вручную.

#### 4. Код-ревью
Чек-лист для ревью:
*   «Этот класс использует `EntityManager`? Где он находится?» (Должен быть в Infrastructure).
*   «Этот метод вызывает `Request` из домена?» (Нельзя).
*   «Этот интерфейс использует `Response`?» (Нельзя, только DTO).

---

### Итог

1.  **Domain:** Чистый PHP, бизнес-правила, интерфейсы репозиториев.
2.  **Application:** Сервисы (Use Cases), DTOs. Зависит только от Domain.
3.  **Infrastructure:** Doctrine, Symfony, Queue. Зависит от Domain и Application.
4.  **Правило:** Зависимости только «внутрь».
5.  **Контроль:** Архитектурные тесты + статический анализ + ревью.

Это не «магия», а дисциплина. Сначала сложно, потом — легко и понятно.

> ⏱ 17.9s · prompt 45543 tok · output 2370 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony-приложения с PostgreSQL я выбираю **Пирамиду Тестов (Test Pyramid)**, но с акцентом на **стабильность** и **скорость**. В PHP-экосистеме «широкий» слой интеграционных тестов часто становится причиной нестабильности и замедления, поэтому я делаю их целевыми.

Вот моя стратегия:

---

### 1. Структура: Что тестируем?

#### А. Unit-тесты (База пирамиды, ~70-80% тестов)
**Цель:** Тестировать бизнес-логику изолированно.
**Инструмент:** PHPUnit (или Pest).
**Что тестируем:**
*   **Доменные объекты:** `Money`, `Order`, `User`.
    *   Проверка инвариантов (напр., `Money` не может быть отрицательным).
    *   Проверка методов (напр., `Order->calculateTotal()`).
*   **Доменные сервисы:** `TaxCalculator`, `DiscountCalculator`.
*   **Чистые функции:** Валидаторы, мапперы DTO.

**Что мокаем (Mock):**
*   Всё, что зависит от внешнего мира (БД, HTTP, время).
*   *Пример:* Тестируем `PlaceOrderService`. Мокаем `OrderRepositoryInterface` и `PaymentGatewayInterface`.
*   *Важно:* Мы **не мокаем** сами бизнес-объекты, если они простые (Value Objects). Если `Money` — это чистая логика, тестируем её напрямую.

**Скорость:** Миллисекунды.
**Стабильность:** 100% (нет внешних зависимостей).

#### Б. Feature/Integration-тесты (Средняя часть, ~20-30% тестов)
**Цель:** Проверить, что все слои работают вместе: Controller → Service → Repository → DB.
**Инструмент:** Symfony WebTestCase + PHPUnit.
**Что тестируем:**
*   **HTTP-цикл:** Запрос → Валидация → Логика → Ответ.
*   **Интеграцию с БД:** Вставка данных через репозиторий, чтение обратно.
*   **Безопасность:** Проверка доступа (`#[IsGranted]`).
*   **Аутентификацию:** Логин, права.

**Что мокаем (Mock):**
*   **Внешние API:** Платежный шлюз, почтовый сервис, поисковый индекс.
*   **Время:** Для тестирования истекших сроков.
*   **Никак не мокаем:** БД (PostgreSQL), Doctrine, Symfony Kernel.

**Скорость:** Секунды (зависит от количества запросов).
**Стабильность:** Высокая (если правильно изолированы внешние сервисы).

#### В. End-to-End (E2E) тесты (Верхушка пирамиды, ~0-5% тестов)
**Цель:** Проверить критически важные сценарии (например, оплата картой).
**Инструмент:** Cypress/Playwright + Selenium (или просто API-тесты).
**Что тестируем:**
*   Критические пользовательские сценарии.
*   Интеграцию с реальными внешними сервисами (в стейджинге, не в CI).

**Скорость:** Долго.
**Стабильность:** Низкая (хрупкие тесты).
**Решение:** Держим их в отдельном рантме, запускаем не на каждом коммите.

---

### 2. Стратегия мока (Mocking)

#### Что **НУЖНО** мокать:
1.  **Внешние сервисы:**
    *   Платежные шлюзы (Stripe, PayPal).
    *   Почтовые сервисы (SendGrid, Mailgun).
    *   SMS-провайдеры.
    *   Поисковые движки (Elasticsearch, Meilisearch).
2.  **Асинхронные процессы:**
    *   Обработчики очереди (Messenger).
    *   Cron-задачи.
3.  **Время:**
    *   Используйте `Clock` из `symfony/stopwatch` или пакет `symfony/clock` для детерминированных тестов.
4.  **Случайные значения:**
    *   UUID, токены, случайные числа.

#### Что **НЕ МОКАТЬ** (или делать осторожно):
1.  **База данных (PostgreSQL):**
    *   *Почему:* Мок БД скрывает ошибки маппинга, индексов, транзакций.
    *   *Исключение:* Если тестировать только логику сервиса, можно мокнуть `EntityManagerInterface`.
2.  **Doctrine ORM:**
    *   *Почему:* Мок `EntityManager` скрывает проблемы N+1, lazy loading, flush.
    *   *Исключение:* Только в unit-тестах чистых сервисов, не зависящих от БД.
3.  **Внутренние сервисы:**
    *   *Почему:* Мок своих же сервисов создает хрупкие тесты, которые ломаются при изменении внутреннего API.
    *   *Правило:* Мокните только **границы** системы (внешние зависимости).

---

### 3. Как поддерживать тесты быстрыми?

#### 1. Параллельный запуск
Используйте `paratest` или `phpunit --processes`.
```xml
<!-- phpunit.xml -->
<phpunit processIsolation="false" stopOnFailure="true" processIsolation="false">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```
Запуск: `vendor/bin/paratest --processes 4`

#### 2. Изоляция тестов (Transactional Rollback)
Для интеграционных тестов с БД:
```php
class BaseIntegrationTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->isTransactionActive()) {
            $this->entityManager->rollBack();
        }
        
        parent::tearDown();
    }
}
```
*Почему:* Тесты не оставляют мусора в БД. БД остается чистой для следующего теста.

#### 3. Использование Testcontainers
Вместо локальной БД используйте **Docker** с изолированной БД для каждого запуска тестов.
*   `testcontainers-php` позволяет поднять PostgreSQL, Redis, RabbitMQ для тестов.
*   Это гарантирует чистую среду.

#### 4. Кэширование
*   Отключите кэши в тестах:
    ```yaml
    # config/packages/test/framework.yaml
    framework:
        test: true
        session:
            storage_factory_id: session.storage.mock_file
        validation:
            email_validation_mode: loose
    ```
*   Отключите `Doctrine` cache, чтобы тесты использовали реальные запросы (или включите, если тестирование не касается БД).

---

### 4. Как поддерживать тесты стабильными?

#### 1. Детерминированность
*   **Время:** Никогда не используйте `new \DateTime()` в тестах. Используйте фиксированное время.
*   **Случайность:** Фиксируйте сид генератора случайных чисел.
*   **Порядок:** Не зависите от порядка выполнения тестов. Каждый тест должен быть независимым.

#### 2. Изоляция от внешнего мира
*   **Сеть:** Все HTTP-запросы (кроме внутренних) должны быть смочаны.
*   **Файловая система:** Используйте временные каталоги (`sys_get_temp_dir()`), чистите их в `tearDown`.

#### 3. Явные ожидания
*   Избегайте «случайных» проверок. Проверяйте только то, что важно.
*   Используйте `assertDatabaseHas`, `assertResponseOk` вместо `assertStringContainsString`.

#### 4. Логи и отладка
*   Включите подробные логи в тестах, чтобы при падении видеть, что произошло.
*   Используйте `dump()` и `var_dump()` в тестах, но удаляйте перед коммитом.

---

### 5. Пример структуры тестов

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── MoneyTest.php
│   │   └── OrderTest.php
│   └── Application/
│       └── TaxCalculatorTest.php
│
├── Integration/
│   ├── Http/
│   │   └── OrderControllerTest.php
│   ├── Repository/
│   │   └── OrderRepositoryTest.php
│   └── Messenger/
│       └── OrderPlacedHandlerTest.php
│
└── E2E/
    └── CheckoutFlowTest.php
```

---

### Итог

| Тип тестов | % тестов | Что тестирует | Что мокает | Скорость |
| :--- | :--- | :--- | :--- | :--- |
| **Unit** | 70-80% | Бизнес-логику | Внешние API, время | Миллисекунды |
| **Integration** | 20-30% | Интеграцию слоёв | Внешние API | Секунды |
| **E2E** | 0-5% | Пользовательские сценарии | Ничего (кроме внешнего) | Минуты |

**Главное правило:** Тестируйте **поведение**, а не реализацию. Если вы тестируете, что `Order` может быть отправлен, а не то, что `OrderRepository` вызывает `em->flush()`, ваши тесты будут стабильными и быстрыми.

> ⏱ 18.7s · prompt 47973 tok · output 2372 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Ситуация, когда два процесса (пользователя) одновременно изменяют одну и ту же запись, в терминах СУБД называется **конкурентным доступом (concurrency)** и приводит к **гонке (race condition)**.

Если не управлять этим, возникают классические проблемы:
1.  **Lost Update (Потерянное обновление):** Пользователь A видит баланс `100`. Пользователь B видит баланс `100`. A снимает `50` (пишет `50`). B снимает `50` (пишет `50`). Финальный баланс `50`, хотя должен быть `0`. Деньги «сгорели».
2.  **Dirty Read:** Один процесс видит незакоммиченные изменения другого.
3.  **Deadlock:** Два процесса блокируют ресурсы и ждут друг друга.

В PostgreSQL (и других MVCC-базах) есть несколько инструментов для защиты. Я разделю их на **оптимистичные** и **пессимистичные** подходы.

---

### 1. Оптимистичная блокировка (Optimistic Locking)

**Идея:** «Мы предполагаем, что конфликты редки. Проверим целостность только в момент сохранения».

#### Как работает:
В таблице есть поле `version` (или `updated_at`).
1.  При чтении вы получаете запись и её текущую версию (напр., `version = 5`).
2.  При записи вы делаете `UPDATE ... WHERE id = X AND version = 5`.
3.  Если запись была изменена кем-то другим, `version` уже стал `6`, и ваше условие `WHERE` не сработает. Запись **не** обновится.
4.  Вы проверяете, сколько строк было затронуто (`affected rows = 0`), понимаете о конфликте и бросаете ошибку.

#### Пример в PostgreSQL:
```sql
-- При чтении
SELECT * FROM accounts WHERE id = 1; -- Вернули version = 5

-- При записи (A и B делают это одновременно)
UPDATE accounts 
SET balance = 50, version = 6 
WHERE id = 1 AND version = 5;
```

#### Как реализовать в PHP (Symfony/Doctrine):
```php
// В сущности
#[ORM\Column(type: Types::INTEGER)]
private int $version = 0;

// В репозитории или сервисе
public function withdraw(Account $account, float $amount): void
{
    $originalVersion = $account->getVersion();
    $account->withdraw($amount);
    $account->incrementVersion();

    // Кастомный SQL или QueryBuilder с условием версии
    $em = $this->entityManager;
    $result = $em->getConnection()->executeStatement(
        'UPDATE accounts SET balance = :balance, version = :version WHERE id = :id AND version = :original',
        [
            'balance' => $account->getBalance(),
            'version' => $account->getVersion(),
            'id' => $account->getId(),
            'original' => $originalVersion
        ]
    );

    if ($result === 0) {
        throw new ConcurrencyException('Account was modified by another user');
    }
}
```

#### Плюсы:
*   **Высокая производительность:** Нет блокировок на время чтения.
*   **Масштабируемость:** Хорошо работает при низкой частоте конфликтов.
*   **Простота:** Не нужно управлять блокировками вручную.

#### Минусы:
*   **Задержка:** Конфликт обнаруживается только в конце транзакции.
*   **Потеря работы:** Если конфликт обнаружен, вся работа процесса пропала (нужно переделывать).
*   **Не подходит для длинных транзакций:** Если транзакция длится 10 секунд, вероятность конфликта высока.

#### Когда использовать:
*   **CRUD-приложения** (редактирование статей, профилей).
*   **Сценарии, где конфликты редки** (99% пользователей не редактируют один и тот же товар одновременно).
*   **Виджеты, формы.**

---

### 2. Пессимистичная блокировка (Pessimistic Locking)

**Идея:** «Мы предполагаем, что конфликты возможны. Заблокируем запись на время обработки».

#### Как работает:
Используется конструкция `SELECT ... FOR UPDATE`.
1.  Процесс A делает `SELECT * FROM accounts WHERE id = 1 FOR UPDATE`.
2.  PostgreSQL ставит **эксклюзивную блокировку** на строку.
3.  Процесс B пытается сделать `SELECT * FROM accounts WHERE id = 1 FOR UPDATE` и **ждет**, пока A откоммитит или откатит транзакцию.
4.  После коммита A, процесс B получает данные и делает свою запись.

#### Пример в PostgreSQL:
```sql
BEGIN;

-- Блокируем запись
SELECT * FROM accounts WHERE id = 1 FOR UPDATE;

-- Делаем изменения
UPDATE accounts SET balance = balance - 50 WHERE id = 1;

COMMIT;
```

#### Как реализовать в PHP (Doctrine):
```php
public function withdraw(AccountId $id, float $amount): void
{
    $em = $this->entityManager;
    
    $em->transactional(function() use ($id, $amount, $em) {
        // setLockMode(LockMode::PESSIMISTIC_WRITE) блокирует строку
        $account = $em->getRepository(Account::class)
            ->createQueryBuilder('a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->setLockMode(QueryBuilder::LOCK_PESSIMISTIC_WRITE)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$account) {
            throw new AccountNotFoundException();
        }

        $account->withdraw($amount);
        // Flush внутри транзакции
        $em->flush();
    });
}
```

#### Плюсы:
*   **Гарантия целостности:** Никаких потерянных обновлений.
*   **Предсказуемость:** Порядок выполнения операций гарантирован.
*   **Простота логики:** Не нужно обрабатывать конфликты (кроме таймаутов).

#### Минусы:
*   **Замедление:** Другие процессы ждут блокировку.
*   **Deadlocks:** Риск взаимной блокировки (A ждет B, B ждет A).
*   **Масштабируемость:** Плохо масштабируется при высокой нагрузке.
*   **Удлинение транзакций:** Если внутри блокировки есть медленный код (HTTP-запрос, вычисление), блокировка держится долго.

#### Когда использовать:
*   **Финансовые операции** (снятие денег, переводы).
*   **Ограниченные ресурсы** (бронирование мест в театре, покупка билета).
*   **Длинные транзакции с критической важностью.**

---

### 3. Атомарные операции (Atomic Operations)

**Идея:** Не читать-модифицировать-писать, а делать изменение прямо в SQL.

#### Как работает:
Вместо того чтобы читать баланс, вычитать в PHP и писать обратно, вы делаете:
```sql
UPDATE accounts SET balance = balance - 50 WHERE id = 1 AND balance >= 50;
```

#### Пример:
```php
public function withdraw(AccountId $id, float $amount): void
{
    $result = $this->connection->executeStatement(
        'UPDATE accounts SET balance = balance - :amount WHERE id = :id AND balance >= :amount',
        [
            'amount' => $amount,
            'id' => $id
        ]
    );

    if ($result === 0) {
        throw new InsufficientFundsException();
    }
}
```

#### Плюсы:
*   **Максимальная производительность:** Одна команда, нет чтения.
*   **Атомарность:** Гарантирована на уровне БД.
*   **Нет конфликтов:** БД сама обрабатывает конкуренцию (через блокировку строки на время `UPDATE`).

#### Минусы:
*   **Сложность:** Невозможно применить сложную бизнес-логику (скидки, комиссии, правила).
*   **Жесткая связь с БД:** Сложнее мигрировать на другую СУБД.
*   **Ограниченная гибкость:** Можно делать только простые вычисления.

#### Когда использовать:
*   **Счетчики** (просмотры, лайки).
*   **Снятие денег** (простая операция).
*   **Инкременты** (сток товара).

---

### 4. Очереди и Idempotency (Идемпотентность)

**Идея:** Вместо прямого изменения состояния, отправляем команду в очередь.

#### Как работает:
1.  Пользователь A и B отправляют запросы.
2.  Оба запроса становятся сообщениями в очереди.
3.  Воркер обрабатывает сообщения **последовательно**.
4.  Каждое сообщение имеет уникальный ID (idempotency key).
5.  Если воркер уже обработал это ID, он пропускает повтор.

#### Плюсы:
*   **Высокая масштабируемость.**
*   **Отказоустойчивость.**
*   **Порядок гарантирован** (в рамках одного воркера).

#### Минусы:
*   **Задержка:** Пользователь не видит результат сразу.
*   **Сложность:** Нужна инфраструктура (очередь, воркеры).

#### Когда использовать:
*   **Высоконагруженные системы.**
*   **Асинхронные сценарии** (отправка писем, индексация).
*   **Сценарии, где задержка допустима.**

---

### Сравнение подходов

| Критерий | **Оптимистичная** | **Пессимистичная** | **Атомарная** | **Очередь** |
| :--- | :--- | :--- | :--- | :--- |
| **Производительность** | Высокая (если конфликтов мало) | Низкая (блокировки) | Максимальная | Высокая (асинхронно) |
| **Масштабируемость** | Хорошая | Плохая | Хорошая | Отличная |
| **Сложность** | Средняя | Средняя | Низкая | Высокая |
| **Гарантия** | Конфликт обнаружен позже | Конфликт предотвращен | Всегда корректно | Порядок гарантирован |
| **Гибкость** | Высокая | Высокая | Низкая | Высокая |
| **Когда использовать** | Формы, CRUD | Финансы, бронирование | Счетчики | Высокая нагрузка |

---

### Итоговая рекомендация

1.  **Для 90% бизнес-логики (редактирование профилей, статей):** Используйте **Оптимистичную блокировку** (версионирование). Это стандарт для CRUD.
2.  **Для финансовых операций (платежи, баланс):** Используйте **Атомарные SQL-операции** или **Пессимистичную блокировку** с короткими транзакциями.
3.  **Для счетчиков (лайки, просмотры):** Используйте **Атомарные SQL-операции** (`UPDATE ... SET count = count + 1`).
4.  **Для высоких нагрузок:** Используйте **Очереди** с идемпотентностью.

**Главное правило:** Не используйте пессимистичные блокировки внутри длинных транзакций, особенно если в них есть HTTP-запросы или медленные вычисления. Блокировки должны жить как можно короче.

> ⏱ 20.8s · prompt 50399 tok · output 2689 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой git-процесс построен на балансе **скорости** (не мешать разработке) и **надежности** (гарантировать качество на продакшене). Я использю гибрид **Git Flow** и **Trunk-Based Development** с элементами **Conventional Commits**.

Вот полный процесс от задачи до прода.

---

### 1. Постановка задачи и ветвление

#### Как я делаю:
1.  **Задача** появляется в трекере (Jira, Linear, GitHub Issues) с четким описанием (что, почему, критерии готовности).
2.  **Ветка** создается из `main` (или `develop`, если есть долгий фич-бранч, но я стараюсь избегать `develop`).
3.  **Именование:** `feature/short-description`, `bugfix/issue-number-description`, `chore/task-description`.
    *   Пример: `feature/user-profile-editing`, `bugfix/123-order-total-calculation`.

#### Почему так, а не иначе?
*   **Короткие имена:** Легко читать в `git log`, легко переключаться.
*   **Связь с задачей:** Если есть ID тикета, его легко найти.
*   **`main` как основа:** Избегаю долгоживущего `develop`, потому что он создает «растление» (drift) между ветками. Чем дольше живет фича, тем сложнее мердж. Лучше иметь много мелких, быстро мерженных веток.

---

### 2. Формат коммитов

#### Как я делаю:
Использую **Conventional Commits**.

```
<тип>(<область>): <короткое описание>

<опциональное подробное описание>

<опциональные футерные данные (Fixes #123, BREAKING CHANGE)>
```

**Типы:**
*   `feat`: Новая функциональность.
*   `fix`: Исправление бага.
*   `refactor`: Рефакторинг (не изменяет поведение).
*   `docs`: Обновление документации.
*   `test`: Добавление/изменение тестов.
*   `chore`: Технические работы (зависимости, конфиги).
*   `perf`: Улучшение производительности.

**Примеры:**
```
feat(order): add partial refund support
fix(auth): correct jwt token expiration check
refactor(user): extract profile validation to separate service
```

#### Почему так, а не иначе?
*   **Автоматизация:** Можно автоматически генерировать **Changelog** (изменения) на основе коммитов.
*   **Версионирование:** Можно автоматически увеличивать семантическую версию (Semantic Versioning).
*   **Читаемость:** `git log --oneline` становится понятным. Вместо `fix`, `update`, `wip` у тебя есть смысл.
*   **Почему не «Free-form»:** Свободный стиль (`Fix bug`, `Update`) делает историю невозможной для анализа. Ты не понимаешь, что изменилось.

---

### 3. Pull Request (Merge Request)

#### Как я делаю:
1.  **Push** ветки в удаленный репозиторий.
2.  **Создаю PR** в `main` (или `staging`).
3.  **Заполняю шаблон PR:**
    *   **Чо:** Краткое описание, что делает.
    *   **Почему:** Ссылка на задачу.
    *   **Как:** Краткое описание подхода (если неточно).
    *   **Тестирование:** Как проверял (ручное, автоматическое).
    *   **Скриншоты/Гифки:** Если есть UI-изменения.
    *   **Чеклист:** Тесты, лінт, мигратии, документация.
4.  **Запускаю CI:** Автотесты, линтер, сборка.

#### Почему так, а не иначе?
*   **PR как документация:** Через месяц, когда ты забудешь, зачем писал код, PR объяснит контекст.
*   **Шаблоны:** Унифицируют процесс. Никто не забудет упомянуть мигратии.
*   **CI как ворота:** Не допускаем к ревью код, который не проходит тесты. Экономит время ревьюера.

---

### 4. Ревью кода

#### Как я делаю:
1.  **Ревьюер** назначается автоматически (или вручную).
2.  **Правило:** Не больше 400-500 строк кода в одном PR. Если больше — разбить на несколько.
3.  **Фокус ревью:**
    *   **Правильность:** Логика соответствует требованиям.
    *   **Безопасность:** SQL-инъекции, XSS, аутентификация.
    *   **Читаемость:** Можно ли улучшить имена, структуру.
    *   **Архитектура:** Нарушение границ слоёв?
4.  **Комментарии:**
    *   `[Suggestion]`: Предложение (не обязательно).
    *   `[Required]`: Должно быть исправлено.
    *   `[Question]`: Вопрос.
5.  **Ответ:** Автор отвечает на каждый комментарий, даже если не согласен.

#### Почему так, а не иначе?
*   **Мелкие PR:** Ревью 300 строк занимает 15 минут. Ревью 3000 строк — 2 часа и приводит к пропуску багов.
*   **Фокус на бизнес-логику, а не на стиль:** Стиль проверяет линтер. Ревьюер тратит время на смысл.
*   **Диалог, а не диктат:** Ревью — это обсуждение. Автор знает контекст лучше ревьюера.

---

### 5. Слияние (Merge)

#### Как я делаю:
Использую **Squash and Merge**.

```bash
git pull --rebase
git add .
git commit -m "feat(order): add partial refund support"
git push
```

В GitHub/GitLab: **Squash and Merge**.

#### Результат:
В `main` появляется **один чистый коммит**, который объединяет все коммиты ветки.

#### Почему так, а не иначе?
*   **Чистая история:** В `main` нет мусорных коммитов (`fix typo`, `wip`, `revert`). Есть только законченные фичи/багфиксы.
*   **Revert-ability:** Если фича сломала продакшен, можно откатить **один** коммит, а не 20.
*   **Почему не Rebase + Merge:** Rebase переписывает историю, что опасно при совместной работе. Merge создает «лишние» коммиты.
*   **Почему не Fast-Forward:** Fast-forward сохраняет все коммиты ветки, что делает историю запутанной.

---

### 6. Релизы и Деплой

#### Как я делаю:
1.  **CI/CD пайплайн:**
    *   `main` → Собирается Docker-образ → Пушится в реестр (Docker Hub, ECR).
    *   `main` → Деплой на **Staging** (автоматически).
2.  **Релиз:**
    *   На Staging проводится финальное тестирование.
    *   Создается тег `v1.2.3` на `main`.
    *   Тег триггерит **Production Deploy**.
3.  **Откат:**
    *   Если ошибка на проде — деплоим предыдущий тег/образ.

#### Почему так, а не иначе?
*   **`main` всегда деплоится:** Никаких «специальных релизных веток». `main` — это продукт.
*   **Теги как релизы:** Тег — это снимок состояния. Легко откатиться.
*   **Staging как предпродакшн:** Позволяет проверить интеграцию перед продом.
*   **Почему не Release Branch:** Если релиз длится 2 недели, `main` уходит вперед. Merge back — это боль. Лучше короткие циклы.

---

### 7. Откаты (Rollbacks)

#### Как я делаю:
1.  **Кодовый откат:** Деплой предыдущего стабильного образа/тега.
2.  **Данные:** Если есть мигратии, которые сломали данные — пишем **откатную мигратацию** вручную.
3.  **Логирование:** Сохраняем логи, чтобы понять причину.

#### Почему так, а не иначе?
*   **Быстрый откат кода:** Docker-образы неизменяемы. Откат — это смена ссылки.
*   **Данные — отдельно:** Код можно откатить за минуту, данные — нет. Поэтому мигратии должны быть **обратимыми** или **аддитивными** (добавление новых колонок, а не изменение старых).

---

### Итоговая схема

```
[Задача]
   ↓
[Ветка feature/... из main]
   ↓
[Коммиты: Conventional Commits]
   ↓
[PR + CI (тесты, линт)]
   ↓
[Ревью (2 человека)]
   ↓
[Squash & Merge в main]
   ↓
[Staging Deploy (автоматически)]
   ↓
[Тестирование на Staging]
   ↓
[Тег v1.2.3]
   ↓
[Production Deploy (по тегу)]
   ↓
[Мониторинг + Логирование]
```

### Почему этот процесс?

1.  **Скорость:** Мелкие PR, Squash Merge, CI на каждом этапе — нет узких мест.
2.  **Надежность:** Статический анализ, тесты, ревью, staging — много барьеров.
3.  **Прозрачность:** Conventional Commits, чистый log в main, теги — история читаема.
4.  **Безопасность:** Откаты за минуты, изоляция staging, контроль мигратий.
5.  **Масштабируемость:** Процесс не зависит от количества разработчиков.

Это не догма. Если у вас 1 разработчик — можно упростить. Если 50 — усложнить. Но для команды 3-10 разработчиков это оптимальный баланс.

> ⏱ 22.5s · prompt 53149 tok · output 2435 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью — это не «поиск багов», а **совместное создание качественного кода** и **передача знаний**. Моя цель — не показать, что я умнее автора, а помочь улучшить решение, которое уже есть.

Вот как я организую процесс.

---

### 1. Маркировка замечаний

Я использую четкую систему тегов, чтобы автор понимал приоритет и природу комментария. Это предотвращает «войны» и экономит время.

| Тег | Смысл | Действие автора |
| :--- | :--- | :--- |
| **`[Required]`** | Критично для слияния. Баг, уязвимость, нарушение архитектуры. | **Обязательно** исправить или обосновать почему нет. |
| **`[Suggestion]`** | Предложение по улучшению (читаемость, стиль, альтернативный подход). | **Можно** принять, можно отклонить с аргументацией. |
| **`[Question]`** | Я не понимаю, что тут происходит. Объясните, пожалуйста. | **Обязательно** объяснить. Может быть, я что-то упускаю. |
| **`[Nit]`** | Мелочь (опечатка, имя переменной). Не критично. | Исправить, если не занимает много времени. |
| **`[Learn]`** | Полезное замечание, которое стоит запомнить (паттерн, ловушка PHP). | Просто прочитать. |

**Почему это важно?**
Если все комментарии «важные», автор начинает воспринимать ревью как атаку. Разделение приоритетов позволяет ему сфокусироваться на критичном и не чувствовать себя давленным мелочами.

---

### 2. На что смотрю в первую очередь (Приоритеты)

Я читаю код по убыванию важности:

#### Уровень 1: Безопасность и Корректность (Критично)
*   **SQL-инъекции:** Использование сырых запросов, отсутствие параметризации.
*   **XSS/CSRF:** Вывод пользовательских данных без экранирования, отсутствие токенов.
*   **Потерянные обновления:** Отсутствие версионирования или блокировок при изменении данных.
*   **Логические ошибки:** Неправильные условия (`if` vs `===`), дивизион на ноль, выход за массивы.
*   **Утечка данных:** Вывод чувствительных полей (пароли, токены) в ответ или логи.

#### Уровень 2: Архитектура и Чистота (Важно)
*   **Нарушение SRP:** Класс делает две вещи.
*   **Нарушение границ:** Домен зависит от Symfony, Repository знает о Controller.
*   **Анемичная модель:** Бизнес-логика в контроллере, а не в домене.
*   **Дублирование:** Код, который встречается в 3+ местах.
*   **Скрытие ошибок:** `catch (\Exception $e) { }` без логирования.

#### Уровень 3: Читаемость и Поддерживаемость (Желательно)
*   **Имена:** Смысловые имена переменных, методов, классов.
*   **Длина функций:** Методы должны быть короткими (20-30 строк).
*   **Комментарии:** Зачем, а не Что.
*   **Тесты:** Покрыты ли новые ветки логики?

#### Уровень 4: Стиль и Конвенции (Мелочи)
*   **PSR-12:** Отступы, пробелы, порядок импортов.
*   **Идеомы:** Использование современных фич PHP (PHP 8).
*   **Визуал:** Пустые строки, группировка кода.

**Почему так?**
Если код небезопасен или сломан, не имеет значения, какие там имена переменных. Сначала исправляем фундамент, потом украшаем.

---

### 3. Как поступаю при разногласии с автором

Разногласие — это нормально. Это признак того, что оба думают. Вот мой алгоритм:

#### Шаг 1: Убедиться, что я правильно понял
Часто разногласие возникает из-за моего непонимания контекста.
*   *Я:* `@author, я не совсем понял, почему тут используется именно массив, а не ArrayObject. Я правильно понимаю, что тут важна производительность?`
*   *Автор:* `Да, потому что мы обрабатываем 100 000 элементов, а ArrayObject создает лишние объекты.`
*   *Я:* `Ага, тогда это оправдано. Спасибо за объяснение.`

#### Шаг 2: Аргументация фактами, а не мнениями
Не пишите «Я считаю, что так лучше». Пишите:
*   ❌ «Мне кажется, этот метод слишком длинный».
*   ✅ `[Suggestion]` Этот метод выполняет 3 разные вещи (валидация, расчет, сохранение). Если мы разобьем его, будет проще тестировать каждую часть отдельно.

#### Шаг 3: Поиск компромисса
Если оба подхода рабочие, спрашиваю:
*   *Я:* «Какой вариант тебе нравится больше? Я готов поддержать твое решение, если объяснишь, почему оно лучше альтернативы из твоего комментария».

#### Шаг 4: Escalation (Эскалация)
Если мы не можем договориться (это бывает редко), я предлагаю:
1.  **Обсудить голосом** (Zoom/Slack) — часто 2 минуты разговора решают проблему, которая длится час в комментариях.
2.  **Привлечь третьего** (старшего разработчика или техлида).
3.  **Правило «Last Resort»:** Если ни один аргумент не перетягивает, автор может оставить свой вариант, **если** он добавил комментарий в коде, объясняющий, почему он выбрал именно этот путь (Document Your Code).

#### Шаг 5: Зафиксировать решение
После разногласия важно зафиксировать итог:
*   *Я:* «Согласны. Используем вариант с ArrayObject для производительности. Спасибо, что прояснил контекст».

**Почему так?**
Цель ревью — не «победа», а «лучший результат». Если автор знает свой код лучше меня, и я настаиваю на своем просто потому, что «я старший» — я вредю проекту.

---

### 4. Антипаттерны, которых я избегаю

1.  **Ревью через 3 дня:** Ревью должно быть в течение 24 часов. Чем дольше, тем хуже.
2.  **Сплошная критика без похвалы:** Если код хороший — скажите «Отличное решение, особенно то, как вы обработали edge case с нулевой суммой». Это мотивирует.
3.  **Переписывание кода ревьюером:** Я не коммичу свой код в чужую ветку. Я комментирую. Если мой вариант лучше — я предлагаю его, и автор вносит изменения.
4.  **Игнорирование PR из-за мелочей:** Если есть 1 критичный баг — я помечаю его `[Required]` и жду исправления. Остальные `[Nit]` я не держу.
5.  **Ревью «на авось»:** Если я не понимаю код — я спрашиваю, а не гадаю.

---

### Итог

Хороший код-ревью:
1.  **Структурирован** (теги).
2.  **Приоритизирован** (безопасность > архитектура > стиль).
3.  **Уважаем** (диалог, а не диктат).
4.  **Быстрый** (в течение 24 часов).
5.  **Учитывающий контекст** (вслушайтесь в автора).

Ревью — это инвестиция в команду. Если разработчики боятся ревью — это проблема культуры, а не кода.

> ⏱ 18.1s · prompt 55633 tok · output 1896 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Чтобы превратить жалобу «пользователи не находят заказы» в задачу, которую разработчик может закрыть без единого уточняющего вопроса, нужно выполнить **декомпозицию бизнес-боли в технические требования**.

Сырая проблема расплывчата. Она может означать: нет страницы истории, есть поиск, но он сломан, заказы пагинацией теряются, или просто UX ужасный.

Ниже — пример готовой задачи (Ticket), написанной от лица Product Manager / Tech Lead.

---

### 📋 Ticket: Улучшение поиска и отображения истории заказов клиента

**Priority:** High
**Type:** Feature / UX Improvement
**Sprint:** Current

#### 1. Context (Контекст)
Анализ обращений в поддержку (Jira #XYZ) показал, что 40% тикетов категории «Где мой заказ?» связаны с тем, что пользователи не могут найти заказы, созданные более 2 месяцев назад, или заказы с конкретными характеристиками (например, «тот самый синий диван»). Текущая реализация показывает только последние 10 заказов без возможности поиска.

#### 2. Goals (Цели)
1.  Дать пользователю возможность быстро найти любой свой заказ за последний год.
2.  Снизить количество обращений в поддержку по теме «Поиск заказа» на 50%.
3.  Обеспечить время загрузки результата поиска < 2 секунд (p95).

#### 3. User Story (Истории пользователей)
**As a** зарегистрированный пользователь,
**I want to** search my order history by date, status, or product name,
**So that** I can quickly find a specific past order to return it or re-order it.

#### 4. Functional Requirements (Функциональные требования)

**4.1. Новый компонент «История заказов»**
*   Замена текущего списка «Последние 5 заказов» на полноценный список с пагинацией.
*   **Пагинация:** 20 заказов на страницу.
*   **Сортировка по умолчанию:** Сначала новые (по дате создания `created_at` DESC).
*   **Действия по заказу:**
    *   «Подробнее» (переход на страницу заказа).
    *   «Оплатить» (если статус `pending_payment`).
    *   «Отследить» (если статус `shipped`).
    *   «Вернуть» (если статус `delivered` и в пределах 14 дней).

**4.2. Фильтры и Поиск**
*   **Поиск по ключевым словам:**
    *   Ищет по: Названию товара (`product_name`), ID заказа (`order_id`), Email отправки (если хранится).
    *   Минимальная длина запроса: 3 символа.
    *   Задержка перед запросом (debounce): 500 мс.
*   **Фильтр по статусу:**
    *   Multi-select: `All`, `Pending Payment`, `Processing`, `Shipped`, `Delivered`, `Cancelled`.
*   **Фильтр по дате:**
    *   Диапазон: `From` / `To`.
    *   Максимальная глубина поиска: 1 год назад (заказы старше 1 года не отображать, чтобы не деградировать производительность).

**4.3. Отображение карточки заказа в списке**
Каждая строка в списке должна содержать:
*   Дата заказа (формат: `DD.MM.YYYY`).
*   Статус (цветной бейдж).
*   Превью товаров (первые 3 товара: название + количество).
*   Сумма заказа (с валютой).
*   ID заказа (краткий, например, `#ORD-12345`).

#### 5. Non-Functional Requirements (Нефункциональные требования)

*   **Performance:**
    *   Запрос к БД должен использовать индекс по `(user_id, created_at DESC)`.
    *   Если поиск по тексту требует full-text — использовать Meilisearch/Algolia (интегрировать в текущий поиск товаров, не создавать новый движок).
    *   Время ответа API < 200 мс для пагинации, < 500 мс для поиска.
*   **Accessibility (a11y):**
    *   Все фильтры должны быть доступны с клавиатуры.
    *   Скринридер должен корректно озвучивать статус заказа.
*   **Mobile:**
    *   Фильтры сворачиваются в аккордеон.
    *   Поиск на первом месте.

#### 6. Acceptance Criteria (Критерии приёмки)

1.  **Scenario: Поиск по названию**
    *   *Given* пользователь залогинен.
    *   *When* он вводит "Nike" в поле поиска.
    *   *Then* он видит список заказов, содержащих товары Nike, отсортированных по дате.
    *   *And* время загрузки < 2 сек.

2.  **Scenario: Фильтр по дате**
    *   *Given* пользователь выбрал диапазон "Jan 2023 - Feb 2023".
    *   *When* он нажимает "Apply".
    *   *Then* он видит только заказы за этот период.
    *   *And* заказы за 2022 год отсутствуют.

3.  **Scenario: Пустой результат**
    *   *Given* пользователь ввел "xyzabc123".
    *   *When* поиск возвращает 0 результатов.
    *   *Then* отображается блок: "Заказы не найдены. Попробуйте изменить фильтры." + кнопка "Сбросить фильтры".

4.  **Scenario: Производительность**
    *   *Given* база содержит 1 млн заказов.
    *   *When* запрашивается первая страница списка.
    *   *Then* p95 latency < 200ms.

#### 7. Out of Scope (Что НЕ делаем в этой задаче)
*   Поиск по номеру карты оплаты.
*   Экспорт истории заказов в CSV/PDF.
*   Изменение структуры БД для хранения "тегов" заказов.
*   Уведомления о новых заказах (это отдельная фича).

#### 8. Technical Hints (Подсказки для разработки)
*   Текущая сущность: `App\Entity\Order`.
*   Репозиторий: `App\Repository\OrderRepository`.
*   Для поиска по названию товара, возможно, потребуется JOIN с таблицей `order_items` и `products`. Оцените производительность. Если JOIN медленный — рассмотрите деноормализацию поля `searchable_text` в таблице `orders` (обновляется при создании заказа).
*   Используйте `Doctrine QueryBuilder` для динамического построения WHERE-условий.

---

### Почему в формулировке есть именно эти блоки?

1.  **Context:** Без этого разработчик не понимает *зачем* он пишет код. Он видит "сделать поиск", но не знает, что это решит проблему поддержки. Это мотивирует думать о качестве.
2.  **Goals:** Измеримые цели (50% меньше тикетов). Если после релиза тикеты не упали — задача выполнена, но цель провалена.
3.  **Functional Requirements:** Детальная раскладка. Здесь убирается 80% вопросов "а как оно должно выглядеть?", "а что если...?". Я четко указал, какие поля нужны, какие фильтры, какая пагинация.
4.  **Non-Functional Requirements:** Это защита от "костылей". Если разработчик напишет поиск, который работает, но тормозит 10 секунд — это не решение. Я сразу обозначил SLA (Service Level Agreement).
5.  **Acceptance Criteria:** Это контракт. QA и разработчик знают, когда задача "готова". Если AC выполнены — задача принимается, даже если мне не нравится цвет кнопок (но цвет я должен был описать в UI-макете, который приложен к тикету).
6.  **Out of Scope:** Самое важное. Это предотвращает "scope creep" (расползание границ). Разработчик не будет тратить 3 часа на CSV-экспорт, если мы договорились, что это не входит в задачу.
7.  **Technical Hints:** Я уважаю время разработчика. Я уже сделал домашнее задание: посмотрел в коде, где лежит логика, какие таблицы задействованы. Я даю ему стартовую точку, а не отправляю "в поля".

### Что бы случилось, если бы я написал только: "Сделать поиск по заказам"?

1.  Разработчик бы спросил: "По каким полям искать?"
2.  Я бы ответил: "По всему".
3.  Он бы сделал: `SELECT * FROM orders WHERE ... ILIKE '%query%'` (полное сканирование таблицы).
4.  База упала бы под нагрузкой.
5.  Разработчик бы спросил: "А как фильтровать?"
6.  Я бы ответил: "А как тебе удобнее".
7.  Через неделю бы вышла фича, которая не решает проблему бизнеса, потому что никто не думал об UX и производительности.

**Вывод:** Хорошая задача для исполнителя — это **спецификация**, а не **пожелание**. Она переводит язык бизнеса (боли) на язык разработки (требования), закрывая все "а что если?".

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Если я получу задачу «Пользователи не могут найти свои заказы» без контекста, я не начну писать код. Я задам вопросы, которые разобьют эту расплывчатую боль на конкретные технические требования.

Вот список вопросов, разделенный по категориям. Ответы на них определят архитектуру решения.

### 1. Диагностика проблемы (Почему они не находят?)
Самое важное — понять, *в чем* именно проблема. «Не могут найти» может означать 10 разных вещей.

*   **В 1.** Вы анализировали обращения в поддержку? Какие конкретные фразы используют пользователи?
    *   *Например:* «Я заказывал диван в прошлом году, где он?» vs «Я не вижу свой текущий заказ в списке».
*   **В 2.** Как выглядит текущий интерфейс «Мои заказы»? Сколько заказов выводится? Есть ли пагинация?
*   **В 3.** Есть ли сейчас какая-либо возможность поиска или фильтрации?
    *   Если да — она сломана, неудобная или медленная?
    *   Если нет — пользователи ищут глазами в списке из 50 позиций и сдаются?
*   **В 4.** Сколько времени в среднем пользователь тратит на поиск нужного заказа до обращения в поддержку? (Если есть метрики).

### 2. Бизнес-метрики и цели (Как мы поймем, что фича работает?)
Нужны измеримые критерии успеха, а не просто «стало лучше».

*   **В 5.** Какую метрику мы хотим улучшить?
    *   Снижение кол-во тикетов «Где мой заказ» на X%?
    *   Увеличение повторных покупок (если пользователь легко находит старый заказ, он может «купить снова»)?
*   **В 6.** Какой окупаемый срок? Мы хотим снизить нагрузку на поддержку *сразу*, или это долгосрочное улучшение UX?

### 3. Функциональные требования (Что именно делать?)
Тут нужно превратить боль в фичи.

*   **В 7.** По каким параметрам пользователи хотят искать?
    *   По названию товара? (Самое частое: «тот самый синий диван»).
    *   По дате? («Тот, что в марте 2023»).
    *   По статусу? («Те, что еще не доставили»).
    *   По сумме? (Редко, но бывает).
    *   *Вопрос:* Какой параметр встречается в 80% жалоб? Начнем с него.
*   **В 8.** Какой период истории должен быть доступен?
    *   Только последние 6 месяцев?
    *   Год?
    *   Вся история (5+ лет)?
    *   *Почему это важно:* Чем глубже история — тем сложнее запрос к БД и тем больше данных.
*   **В 9.** Нужно ли поддерживать «Быстрые действия» прямо из списка поиска?
    *   Например: кнопка «Оплатить» для неоплаченных, «Отследить» для доставляемых, «Возврат» для доставленных.
    *   Или пользователь должен обязательно заходить в деталку заказа?

### 4. Технические ограничения и контекст (Чем мы ограничены?)
*   **В 10.** Сколько всего заказов у пользователей в среднем?
    *   Если у 90% пользователей по 2-3 заказа — поиск им не нужен, нужен хороший список.
    *   Если у B2B-клиентов по 500 заказов — нужен мощный фильтр.
*   **В 11.** Какая база данных используется? Есть ли индексы по `user_id` и `created_at`?
    *   *Почему:* Если база грязная, поиск по тексту будет тормозить. Нужно знать, можем ли мы добавить full-text поиск или только LIKE.
*   **В 12.** Есть ли у нас поисковый движок (Elasticsearch, Meilisearch)?
    *   Если да — использовать его для поиска по названиям товаров.
    *   Если нет — решать: вводить новый или делать SQL-поиск (медленнее, но проще).
*   **В 13.** Есть ли ограничения по производительности API?
    *   Должен ли поиск откликаться за 200мс?

### 5. UX и UX-дизайн (Как это должно выглядеть?)
*   **В 14.** Кто рисует макеты? Готовы ли они к этой задаче?
    *   Если нет, нам нужно обсудить wireframe прямо сейчас.
    *   Где будет поле поиска: сверху списка? В боковой панели?
*   **В 15.** Какое сообщение показывать, если ничего не найдено?
    *   «Попробуйте изменить фильтры»?
    *   «Заказа с таким названием нет. Возможно, вы ищете по артикулу?».

### 6. Безопасность и права доступа
*   **В 16.** Могут ли пользователи видеть заказы, которые были созданы, но отменены до оплаты?
    *   Обычно их прячут, чтобы не путать.
*   **В 17.** Есть ли ограничения по конфиденциальности?
    *   Например, нельзя показывать номера карт, даже в истории.

---

### Как я использую эти ответы?

| Ответ | Как это меняет задачу |
| :--- | :--- |
| «Ищут по названию товара, 80% жалоб» | Приоритет: поиск по тексту. Нужен Meilisearch или оптимизированный SQL. |
| «История за 1 год, B2C, 5 заказов в среднем» | Можно обойтись без сложного поиска, добавить фильтры по статусу и дате. |
| «История за 5 лет, B2B, 500 заказов» | Нужен полноценный поиск, пагинация, фильтры. Возможно, вынос в отдельный сервис. |
| «База маленькая, нет ES» | Делаем SQL-поиск с индексами. Дешевле и быстрее. |
| «Нет макетов» | Блокируем разработку, пока не будет согласованного wireframe. |

### Итог

Я не спрашиваю «Что делать?». Я спрашиваю:
1.  **Почему** пользователи страдают (диагностика).
2.  **Как** мы измерим успех (метрики).
3.  **Что** именно им нужно (функционал).
4.  **Чем** мы ограничены (технический стек).

После этого я смогу написать задачу так, как в предыдущем примере, с конкретными Acceptance Criteria и техническими подсказками.

> ⏱ 23.1s · prompt 57610 tok · output 2151 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это две классические ситуации, которые проверяют зрелость разработчика. Плохой разработчик либо «тихо» чинит баг (и потом ломает чужой код), либо игнорирует противоречие (и потом получает не то, что нужно бизнесу). Хороший разработчик останавливает процесс и выносит проблему на обсуждение.

Вот мой алгоритм действий.

---

### Случай А: Нашел попутный баг, не связанный с задачей

**Моя позиция:** «Fix it if you can, but don't mix concerns.»

#### 1. Оценить риск и стоимость
*   **Риск:** Если я исправлю баг, не разбив задачу, я могу:
    *   Внести регрессию (сломать то, что работало).
    *   Увеличить размер PR (Pull Request), что усложнит ревью.
    *   Сместить дедлайн текущей задачи.
*   **Стоимость:** Если баг чинится за 5 минут и не требует новых тестов — риск низкий. Если нужно писать миграции и менять логику — риск высокий.

#### 2. Решение
**В 90% случаев я НЕ чиню этот баг в текущей задаче.**

**Почему?**
*   **Одноразовость (Single Responsibility):** Текущая задача имеет четкие критерии приемки. Если я добавлю чин бага, я расширю scope.
*   **Прозрачность:** Ревьюер будет отвлекаться на проверку кода, который не относится к задаче.
*   **Откат:** Если текущая задача откатится, вместе с ней откатится и исправление бага, которое могло быть полезным.

**Что я делаю:**
1.  **Фиксирую баг:** Создаю новый Ticket (Jira/GitHub Issue) с подробным описанием: как воспроизвести, почему это баг, какой код задевает.
2.  **Отмечу в PR (комментарий):** Напишу в текущем PR: `// TODO: See issue #1234. Fixing in separate PR to keep this PR focused.`
3.  **Планирую исправление:** Добавлю этот баг в ближайший спринт или приоритизирую его отдельно.

**Исключение:**
Если баг критичный (блокирует работу текущей задачи или угрожает безопасности), я:
1.  Сообщаю команде.
2.  Чиню его **отдельным коммитом** в той же ветке, но **отдельным PR** (или отдельным squash-коммитом).
3.  Объясняю в описании PR: «Исправляю критичный баг #X, который блокирует текущую задачу».

---

### Случай Б: Критерий приемки противоречит цели задачи

**Моя позиция:** «Stop. This is a product problem, not a technical one.»

#### 1. Остановить работу
Не пытаться «обойти» противоречие хитростью. Не писать код, который удовлетворяет букве, но нарушает дух.

#### 2. Проанализировать противоречие
Найти конкретный пример.
*   *Цель:* «Пользователи должны быстро находить заказы».
*   *Критерий:* «Поиск должен выполняться только по точному совпадению ID заказа».
*   *Противоречие:* Пользователь не помнит ID. Он помнит «синий диван». Критерий не позволяет ему найти заказ. Цель не достигнута.

#### 3. Коммуникация
Не писать в чат: «Ребят, тут баг в ТЗ».
Написать в тикет или созвониться с Product Manager / Tech Lead:

> «Я остановился на реализации [критерий X]. Я обнаружил, что он противоречит основной цели задачи [цель Y].
>
> **Пример:** Если мы реализуем [критерий X], то пользователь [сценарий] не сможет [желаемый результат].
>
> **Варианты решения:**
> 1. Изменить критерий на [вариант А].
> 2. Изменить цель задачи на [вариант Б].
> 3. Разделить задачу на две.
>
> Какой вариант вы предпочитаете?»

#### 4. Зафиксировать решение
До получения ясного ответа **не продолжать работу**.

**Почему так?**
*   **Доверие:** Ты показываешь, что думаешь о результате, а не просто «пишешь код».
*   **Экономия ресурсов:** Лучше потратить 15 минут на обсуждение сейчас, чем 3 дня на написание кода, который придется переписать.
*   **Ответственность:** Если ты промолчишь и сделаешь «как в ТЗ», а потом фича не сработает — это твоя вина. Ты знал о проблеме, но не сказал.

---

### Итоговая таблица

| Ситуация | Действие | Почему |
| :--- | :--- | :--- |
| **Попутный баг** | Создать отдельный тикет. НЕ чинить в текущем PR. | Сохранить фокус, снизить риск, упростить ревью. |
| **Противоречие в ТЗ** | Остановить работу. Обсудить с PM/TL. Зафиксировать решение. | Избежать переделки. Показать ответственность за результат. |

### Ключевой принцип

**Ты не просто исполнитель кода, ты партнёр бизнеса.**
*   В случае А ты защищаешь **качество и стабильность** кодабазы.
*   В случае Б ты защищаешь **ценность продукта** для пользователя.

Если ты будешь действовать так, тебя будут уважать не только разработчики, но и продукт-менеджеры.

> ⏱ 12.4s · prompt 61418 tok · output 1362 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении задачи — это не просто «Сделано, можно мерджерить». Это **артефакт, который объясняет бизнесу и команде, *что именно* изменилось, *как* это проверить и *какие* риски есть**.

Мой отчёт состоит из 5 блоков.

---

### 1. Что сделано (Summary)
Краткое описание в 2-3 предложениях.
*   **Не:** «Рефакторинг UserService, добавление метода calculatePrice».
*   **Да:** «Реализована возможность для пользователей находить заказы по названию товара и статусу. Добавлен поиск и фильтры в раздел "Мои заказы"».

**Почему:** Чтобы менеджер, который не читает код, сразу понял, что изменится для пользователя.

---

### 2. Технические детали (Implementation Notes)
Здесь я объясняю *как* я решил проблему. Это важно для будущих разработчиков, которые будут читать этот код.

*   **Ключевые решения:**
    *   «Использовал `Meilisearch` для полнотекстового поиска, так как SQL-поиск (`LIKE`) был слишком медленным на таблице с 1 млн записей».
    *   «Добавил индекс `(user_id, created_at DESC)` для ускорения пагинации».
    *   «Вынес логику фильтрации в отдельный сервис `OrderFilterService` для повторного использования».
*   **Изменения в API:**
    *   Новый эндпоинт: `GET /api/orders/search`
    *   Измененный формат ответа: добавлено поле `searchable_text`.
*   **Миграции БД:**
    *   Создана миграция `AddSearchableTextToOrders`.
    *   Добавлен индекс `idx_orders_user_created`.

**Почему:** Если через полгода кто-то спросит «Почему тут Meilisearch?», ответ будет в PR. Это самодокументируемый код.

---

### 3. Тестирование (Testing)
Доказательство того, что код работает.

*   **Автоматические тесты:**
    *   `OrderSearchTest.php` — покрыты сценарии: поиск по названию, по статусу, пустой результат, пагинация.
    *   `OrderRepositoryTest.php` — проверены запросы к БД, индексы.
    *  _coverage_: 85% для новых классов.
*   **Ручное тестирование:**
    *   Проверено на Chrome, Safari, Mobile Safari.
    *   Проверены edge-cases: поиск по кириллице, по спецсимволам, пустой поиск.
    *   Проверена производительность: p95 = 180ms (в пределах SLA 200ms).
*   **Что НЕ тестировалось (честно):**
    *   Поиск по 10+ фильтрам одновременно (тесты были бы слишком сложными).
    *   Загрузка на 10k одновременных пользователей (нужна нагрузочная среда).

**Почему:** Честность в отчёте строит доверие. Если ты не тестировал что-то — скажи об этом. Это не слабость, а ответственность.

---

### 4. Риски и ограничения (Risks & Limitations)
Здесь я указываю на то, что может пойти не так.

*   **Риск 1:** Если Meilisearch упадет, поиск не будет работать.
    *   *Митигация:* Добавлен fallback на SQL-поиск (деградация производительности, но работа останется).
*   **Риск 2:** Пользователи могут вводить длинные запросы (> 100 символов).
    *   *Митигация:* Ограничение длины ввода на фронтенде (50 символов).
*   **Ограничение:** Поиск не учитывает товары, которые были удалены из каталога, но есть в заказах.
    *   *Причина:* Это было бы слишком дорого для B2B-сегмента, где каталог меняется редко.

**Почему:** Это защищает команду от «а почему оно так работает?». Ты заранее сказал: «Вот что мы решили, и вот почему».

---

### 5. Чеклист (Checklist)
Финальное подтверждение.

*   [x] Код покрыт тестами.
*   [x] Линтер и статический анализатор проходят.
*   [x] Миграции написаны и проверены на откат.
*   [x] Документация (Swagger/Postman) обновлена.
*   [x] Кодирование соответствует стандартам PSR-12.
*   [x] Нет hardcoded-значений (все настройки в `.env`).

---

### Что меняется, если часть задачи сделать не удалось?

Если я не смог выполнить 100% задачи, отчёт становится **отчётом о статусе**, а не отчётом о выполнении.

#### Структура отчета при частичном выполнении:

**1. Статус задачи**
*   `In Progress` или `Blocked`.
*   Кратко: «Реализован поиск по названию. НЕ реализован поиск по цене из-за [причина]».

**2. Что сделано (как выше)**

**3. Что НЕ сделано (Blocked Items)**
*   **Пункт:** «Поиск по цене».
*   **Причина:** «Выяснилось, что цены хранятся в разных валютах, и для корректного поиска нужен конвертер валют. Это вынесено в отдельную задачу #1234».
*   **Влияние:** «Пользователь не сможет искать по цене до релиза #1234».
*   **Решение:** «Создана новая задача. Предложен временный workaround: фильтр по статусу».

**4. Риски (обновленные)**
*   **Новый риск:** «Пользователи будут жаловаться на отсутствие поиска по цене».
*   **Митигация:** «Добавлено сообщение в UI: "Поиск по цене появится в следующем обновлении"».

**5. Следующие шаги**
*   «Задача #1234 назначена на следующий спринт».
*   «Нужен ревью от @architect по решению с валютами».

---

### Пример отчёта (краткая версия для Slack/Teams)

> **Задача #123: Поиск по заказам**
>
> **Статус:** ✅ Готово к релизу (90%) / ⚠️ Частично выполнено
>
> **Сделано:**
> *   Поиск по названию товара (Meilisearch).
> *   Фильтры по статусу и дате.
> *   Пагинация (20 на страницу).
>
> **Не сделано:**
> *   Поиск по цене (перенесено в #124 из-за сложности с валютами).
>
> **Тесты:**
> *   Unit: 15 тестов, 100% покрытие новых классов.
> *   Integration: 5 тестов, проверка API и БД.
> *   Rучное: проверено на Mobile и Desktop.
>
> **Риски:**
> *   Если Meilisearch упадет — поиск деградирует до SQL (медленнее).
>
> **Ссылки:**
> *   PR: #456
> *   Ticket: #123
> *   Документация: [ссылка]
>
> **Ревьюер:** @username
> **Тестер:** @username

---

### Почему это важно?

1.  **Прозрачность:** Никто не удивится, что поиск по цене не работает.
2.  **Доверие:** Ты не «вруешь», что всё сделано.
3.  **Эффективность:** Команда знает, что можно деплойть, а что нет.
4.  **Надежность:** Риски учтены, митигации предложены.

**Главное правило:** Отчёт — это не «защита диплома». Это **информационный продукт** для тех, кто будет использовать твой код.

> ⏱ 17.4s · prompt 62823 tok · output 1887 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

**В PostgreSQL нет механизма `FORCE INDEX` в той же форме, что и в MySQL.**

Это важный момент, который часто путают разработчики, переходящие с MySQL.

### Почему в PostgreSQL нет `FORCE INDEX`?

1.  **Оптимизатор PostgreSQL (Planner) считается очень умным.** Он использует **CBO (Cost-Based Optimizer)**, который оценивает стоимость каждого плана запроса на основе статистики таблицы (размер, количество строк, распределение данных) и выбирает оптимальный.
2.  **Статистика в реальном времени.** PostgreSQL регулярно обновляет статистику (`ANALYZE`), поэтому оптимизатор обычно знает, какой индекс будет быстрее.
3.  **Гибкость.** Вместо грубого принуждения использовать индекс, PostgreSQL позволяет более тонкие настройки.

### Однако, у PostgreSQL есть инструменты для «ручного управления» планом запроса:

#### 1. `ANALYZE` (Самое частое решение)
Если вы думаете, что оптимизатор выбрал плохой план (например, сделал `Seq Scan` вместо `Index Scan`), скорее всего, **статистика устарела**.

```sql
ANALYZE orders;
```

Это обновит информацию в `pg_statistic`, и оптимизатор, скорее всего, выберет правильный план. **Это 90% случаев, когда кажется, что нужен `FORCE INDEX`.**

#### 2. `set_enable_*` (Подключение/отключение типов узлов)
Вы можете временно отключить определенные типы операций в планировщике. Это «грубая» палка, но она работает.

Пример: Запретить `Seq Scan` и `Bitmap Scan`, чтобы принудить использование `Index Scan`.

```sql
BEGIN;
SET enable_seqscan = off;
SET enable_bitmapscan = off;

SELECT * FROM orders WHERE user_id = 123;
-- Оптимизатор будет вынужден использовать Index Scan (если он есть)

SET enable_seqscan = on;
SET enable_bitmapscan = on;
COMMIT;
```

**Важно:** Это влияет только на текущую сессию/транзакцию. **Не делайте это глобально в `postgresql.conf`**, кроме как в отладочных целях.

#### 3. `pg_hint_plan` (Расширение)
Это **настоящий аналог `FORCE INDEX`/`USE INDEX`** для PostgreSQL.

Вы устанавливаете расширение `pg_hint_plan`, и тогда можете добавлять хинты в запрос:

```sql
SELECT /*+ IndexScan(orders idx_orders_user_id) */ 
    * 
FROM orders 
WHERE user_id = 123;
```

Или:
```sql
SELECT /*+ NoSeqScan(orders) */ * FROM orders WHERE ...;
```

**Плюсы:**
*   Точное управление планом.
*   Можно применять к конкретным запросам.

**Минусы:**
*   Требуется установка расширения.
*   Сложность поддержки (хинты могут стать неактуальными при изменении данных).

#### 4. Материализованные представления (Materialized Views)
Если запрос всегда один и тот же и медленный, лучше создать материализованное представление с нужными индексами.

```sql
CREATE MATERIALIZED VIEW mv_user_orders AS
SELECT * FROM orders WHERE status = 'active';

CREATE INDEX idx_mv_user_id ON mv_user_orders(user_id);
```

Теперь оптимизатор будет использовать этот быстрый путь.

#### 5. Переписывание запроса
Иногда оптимизатор «тупит» на сложных запросах. Переписывание может помочь:
*   Заменить `IN` на `EXISTS`.
*   Заменить подзапросы на `JOIN`.
*   Убрать лишние `DISTINCT`.

---

### Когда применять «принуждение»?

| Ситуация | Решение |
| :--- | :--- |
| Оптимизатор выбирает `Seq Scan`, хотя есть подходящий индекс | `ANALYZE table;` (в 90% случаев этого достаточно) |
| Запрос работает медленно из-за неправильного выбора соединения (`Hash Join` vs `Nested Loop`) | Попробуйте `SET enable_hashjoin = off;` и посмотрите, изменится ли план |
| Критический бизнес-запрос, который нельзя менять | Используйте `pg_hint_plan` или материализованное представление |
| Вы настраиваете новый кластер | Никогда не используйте хинты «на всякий случай» |

### Итог

1.  **Никакого `FORCE INDEX` в нативном PostgreSQL.**
2.  **Сначала `ANALYZE`.**
3.  **Затем переписывание запроса.**
4.  **Потом `set_enable_*` для диагностики.**
5.  **Наконец, `pg_hint_plan` или материализованные представления для производства.**

Если вам кажется, что нужен `FORCE INDEX` — это почти всегда сигнал, что **нужен хороший индекс** или **правильная статистика**, а не хинт.

> ⏱ 11.0s · prompt 64736 tok · output 1155 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

Короткий ответ: **На 0–2% в реальных сценариях, и часто даже быстрее, чем код без `strict_types`.**

Если вы видите замедление на 10–20% — у вас **проблема с архитектурой**, а не с `strict_types`.

### Почему `strict_types` почти не замедляет?

#### 1. `strict_types` влияет только на **проверку типов при вызове функции**
Он не влияет на:
*   Операции внутри функции (`+`, `-`, `==`, `array_map`).
*   Приведение типов в выражениях.
*   Обратную связь (reflection).
*   Сериализацию.

`strict_types=1` добавляет **одну дополнительную проверку** в байткоде: «Совпадает ли тип аргумента с типом параметра?».

Если совпадает — проверка проходит мгновенно (операция в процессоре, наносекунды).
Если не совпадает — выбрасывается `TypeError`.

#### 2. PHP 8+ оптимизирован для строгих типов
В PHP 8.1+ введены **JIT (Just-In-Time)** и улучшенный оптимизатор. Оптимизатор знает, что при `strict_types=1` приведение типов **не произойдет**, и может:
*   Убрать проверки приведения типов из кода.
*   Скомпилировать более быстрый байткод.
*   Лучше оптимизировать виртуальный стек.

#### 3. Сравнение с кодом без `strict_types`
Без `strict_types` PHP **выполняет приведение типов**, что **дольше**, чем проверка:
*   Проверка: `is_int($arg)` → `true`/`false` (быстро).
*   Приведение: `(int)$arg` → создание нового значения (медленнее).

**Вывод:** Строгая проверка типов **быстрее**, чем неявное приведение.

---

### На что реально влияет `strict_types`?

#### 1. На время **ошибок**
Если код **корректен** — `strict_types` не замедляет.
Если код **некорректен** (передаются не те типы) — `strict_types` **ускоряет обнаружение ошибки** (быстрый `TypeError` вместо тихого бага).

#### 2. На читаемость и отладку
Это **основная «цена» `strict_types`**:
*   Вы можете потратить время на исправление типов, которые раньше «просто работали».
*   Вы можете потратить время на настройку статического анализатора (PHPStan/Psalm).

Но это **разовые затраты**, а не постоянные потери производительности.

---

### Когда `strict_types` может замедлять код?

#### 1. Частые вызовы функций с `mixed`-типами
Если вы часто вызываете функции с параметрами типа `mixed`, `strict_types` будет проверять каждый аргумент. Но это **миллисекунды**, а не секунды.

#### 2. Негативные сценарии (ошибки типов)
Если ваш код **полон багов типов** — `strict_types` будет часто выбрасывать `TypeError`. Это **затраты на отладку**, а не производительность.

#### 3. Старые версии PHP (7.x)
В PHP 7 `strict_types` работал менее эффективно, но даже там разница была < 5%.

---

### Что реально замедляет PHP-код?

1.  **N+1 запросы в БД** (в 100–1000 раз медленнее).
2.  **Неоптимизированный SQL** (в 10–100 раз медленнее).
3.  **Частая сериализация/десериализация** (JSON, XML).
4.  **Неоптимальные структуры данных** (массивы вместо объектов).
5.  **Частые вызовы внешних API** (в 1000 раз медленнее).
6.  **Отсутствие кэширования** (Redis, Memcached).

`strict_types` — это **наносекунды**, а вышеперечисленное — **миллисекунды-секунды**.

---

### Итог

| Фактор | Влияние на производительность |
| :--- | :--- |
| `strict_types=1` | 0–2% (часто 0%) |
| N+1 запросы | 100–1000x замедление |
| Неоптимизированный SQL | 10–100x замедление |
| Сериализация | 10–50x замедление |
| Внешние API | 1000x замедление |

**Заключение:**
`strict_types` — это **не жертва производительности**. Это **инвестиция в надежность и читаемость**, которая почти ничего не стоит в плане скорости.

Если ваш код замедлился после включения `strict_types` — ищите **реальные проблемы** (N+1, SQL, кэш), а не отключайте строгие типы.

> ⏱ 11.1s · prompt 65931 tok · output 1189 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

На самом деле, в этом утверждении есть небольшая неточность: **Doctrine ORM (на основе AbstractQuery) в своих стандартных сущностях (Entities) не поддерживает составные первичные ключи (Composite Keys) для основных сущностей**, но это не означает, что их нельзя использовать в базе данных.

### Почему Doctrine не поддерживает составные PK в Entity?

1.  **Сложность объектной модели.** Entity в Doctrine — это объект, который идентифицируется **единственным** ID. Это основа паттерна `Unit of Work` и кэширования сущностей в памяти (`EntityManager` хранит сущности по их ID).
2.  **Кэширование.** `EntityManager` использует массив, где ключ — это `className#id`. Если ID составной (`user_id + order_id`), то ключом должен стать строка `"User#1_Order#2"`, что усложняет логику, создает проблемы с коллизиями и усложняет работу с кэшем.
3.  **Ленивая загрузка (Lazy Loading).** Если сущность идентифицируется составным ключом, Doctrine не может однозначно определить, какой объект загрузить, если загрузить только часть ключа.
4.  **Фабрики и сравнение.** Сложно сравнивать два объекта с составным ключом (`==`), сложно клонировать, сложно сериализовать.

### Как с этим живут в реальных проектах?

Есть несколько подходов, от «обходных» до «правильных» с точки зрения DDD.

---

### 1. Подход: Связка через составной ключ (Join Table)

**Сценарий:** `ManyToMany` связь (например, `User` <-> `Role`).

В базе данных есть таблица `user_roles` со **составным** первичным ключом `(user_id, role_id)`.

**Решение Doctrine:**
Doctrine **не** создает Entity для таблицы `user_roles`. Вместо этого он использует `ManyToMany` маппинг:

```php
#[ORM\Entity]
class User
{
    #[ORM\Id, ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_roles')] // Составной PK формируется автоматически
    #[ORM\JoinColumn(name: 'user_id')]
    #[ORM\InverseJoinColumn(name: 'role_id')]
    private Collection $roles;
}
```

**Как это работает:**
*   В БД есть `user_roles` с составным PK.
*   В PHP нет Entity `UserRole`.
*   Вы работаете с `User` и `Role` отдельно.
*   Составной ключ используется **только на уровне БД**, PHP-код этого не видит.

**Плюсы:**
*   Простота.
*   Нет лишних сущностей.
*   Идеально для чистых `ManyToMany`.

**Минусы:**
*   Нельзя добавить доменную логику в «связку» (например, «пользователь получил роль в такой-то дату»).

---

### 2. Подход: Создание «Обертки» (Entity Wrapper)

**Сценарий:** У вас есть бизнес-объект, который логически является «связкой», но ему нужны доменные методы.
*   Пример: `OrderLine` (связка `Order` и `Product`) с полями `quantity`, `price`.
*   Пример: `Membership` (связка `User` и `Group`) с полями `status`, `joined_at`.

**Решение:**
Создать отдельную Entity с **своим** первичным ключом (например, `id` или `uuid`), а составной ключ сделать **уникальным индексом**.

```php
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'unique_order_product', columns: ['order_id', 'product_id'])]
class OrderLine
{
    #[ORM\Id, ORM\Column(type: Types::INTEGER)]
    private int $id; // Самостоятельный PK

    #[ORM\ManyToOne(targetEntity: Order::class)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    private int $quantity;
    private int $price;

    // Доменная логика
    public function getTotal(): int
    {
        return $this->quantity * $this->price;
    }
}
```

**Почему это лучше составного PK?**
1.  **Идентификатор:** У сущности есть уникальный ID (`id`), который можно использовать в URL, в JSON, в логах.
2.  **Гибкость:** Можно добавить поля `created_at`, `updated_at`, `metadata`.
3.  **Стандарт ORM:** Все ORM (Hibernate, Entity Framework, Doctrine) любят сущности с одним PK.

**Минусы:**
*   Лишняя колонка `id`.
*   Сложность удаления (нужно удалять строку из `order_lines`).

---

### 3. Подход: Использовать `Mapping` с составным ключом (Расширенный)

В Doctrine можно **в теории** описать составной ключ через `#[ORM\Id]` на нескольких полях, но **это не работает корректно** в большинстве случаев из-за проблем с кэшированием и ленивой загрузкой.

**Когда это может работать:**
*   Если вы **не** используете `EntityManager` для управления этими сущностями.
*   Если вы используете `Repository` только для чтения/записи.
*   Если у вас нет ленивой загрузки.

**Но это плохая практика.**

---

### 4. Подход: DDD Aggregate Root + Value Object

**Сценарий:** Составной ключ — это часть бизнес-логики.

**Решение:**
В домене (Pure PHP) вы можете иметь Value Object:

```php
final class OrderLineId
{
    public function __construct(
        private string $orderId,
        private string $productId
    ) {}
}
```

В инфраструктуре (Doctrine) вы мапите это в сущность с **своим** ID (как в подходе 2).

---

### Сравнение подходов

| Сценарий | Решение | Почему |
| :--- | :--- | :--- |
| **Чистая ManyToMany** (User-Role) | `ManyToMany` маппинг | Нет доменной логики в связке. Составной PK в БД. |
| **Бизнес-связка** (Order-Product) | Entity с `id` + `UniqueConstraint` | Нужен доменный ID, ленивая загрузка, простота ORM. |
| **Связка с историей** (Membership) | Entity с `id` | Нужно хранить метаданные (дата, статус). |
| **Сложный домен** (Payment) | Entity с `id` | Нужна целостность, транзакции, доменные события. |

---

### Итог

1.  **Doctrine не поддерживает составные PK в Entity**, потому что это ломает `Unit of Work` и кэширование.
2.  **В БД составные PK разрешены** и часто используются для `ManyToMany` связей.
3.  **В PHP-коде** всегда используйте **самостоятельный PK** (`id`) для сущностей, даже если в БД есть составной уникальный ключ.
4.  **Используйте `UniqueConstraint`** для бизнес-правил «уникальности связки».

**Пример миграции:**

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class AddUniqueConstraintToOrderLine extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX idx_order_product ON order_line (order_id, product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_order_product ON order_line');
    }
}
```

Это лучший компромисс между **чистотой домена** и **удобством ORM**.

> ⏱ 16.8s · prompt 67158 tok · output 1872 tok

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

Этот код — классический пример «спагетти-кода» (Spaghetti Code), который нарушает практически все принципы Clean Architecture, SOLID и безопасности. Если этот код в продакшене, система находится в зоне риска критических инцидентов.

Вот разбор проблем по степени критичности и пошаговый план рефакторинга.

---

### 1. Критические проблемы (Killer Features)

#### 1.1 SQL-инъекция (Уязвимость безопасности)
```php
$pdo->query("SELECT * FROM orders WHERE id = $id")->fetch();
```
В `$id` подставлено значение напрямую из запроса. Если пользователь передаст `1; DROP TABLE orders;`, он разрушит базу данных.
**Решение:** Только подготовленные выражения (Prepared Statements).

#### 1.2 Отсутствие транзакции и атомарности
```php
$pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id");
Mailer::send($order['email'], 'Ваш заказ оплачен');
```
Если `Mailer::send` упадет (сеть, баг), заказ останется с статусом `paid` в БД, но письмо не уйдет. Или наоборот: письмо ушло, а запись в БД не сохранилась (если UPDATE упадет).
**Решение:** Все изменения состояния должны быть атомарными. Если это бизнес-критичная операция — нужна транзакция.

#### 1.3 Нарушение SRP (Single Responsibility Principle)
Один метод `pay` выполняет 5 разных задач:
1.  Получение ID.
2.  Запрос к БД.
3.  Обработка ошибки 404.
4.  Обновление БД.
5.  Отправка письма.
6.  Логирование.
**Последствие:** Невозможно протестировать логику оплаты, не поднимая БД и не мокая `Mailer`.

---

### 2. Архитектурные проблемы

#### 2.1 Отсутствие доменного слоя
Здесь нет понятия «Order» как бизнес-объекта. Есть просто массив `$order`. Бизнес-правило «Заказ должен быть в статусе pending, чтобы его можно было оплатить» отсутствует.

#### 2.2 Жесткая зависимость от инфраструктурных классов
`DB::connect()`, `Mailer::send()`, `new Logger()` — все это инфраструктурные детали. Они должны внедряться через DI (Dependency Injection), а не создаваться внутри метода.

#### 2.3 Плохая обработка ошибок
Если `$pdo->query` упадет (ошибка соединения), вы получите `Fatal Error`, а пользователь увидит пустой экран или 500 с трейсом стека.

#### 2.4 Отсутствие валидации
Что, если `$req->get('order_id')` вернет `null`? Или `abc`? Код сломается.

---

### 3. Как бы я переделал этот код?

Я разобью этот метод на слои: **Controller (HTTP)** → **Service (Use Case)** → **Repository (Data Access)** → **Infrastructure (Email/Log)**.

#### Шаг 1: Доменная модель и интерфейс репозитория

```php
// Domain/Entity/Order.php
class Order
{
    private string $id;
    private string $email;
    private string $status;

    public function markAsPaid(): void
    {
        if ($this->status === 'paid') {
            throw new \DomainException('Order is already paid');
        }
        $this->status = 'paid';
    }

    // ... геттеры
}

// Domain/Repository/OrderRepositoryInterface.php
interface OrderRepositoryInterface
{
    public function findById(string $id): ?Order;
    public function save(Order $order): void;
}
```

#### Шаг 2: Сервис (Use Case) — бизнес-логика

```php
// Application/Service/PayOrderService.php
class PayOrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function payOrder(string $orderId): Order
    {
        // 1. Загружаем сущность
        $order = $this->orderRepo->findById($orderId);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Применяем бизнес-правило (изменение состояния)
        $order->markAsPaid();

        // 3. Сохраняем состояние (транзакция внутри репозитория или здесь)
        $this->orderRepo->save($order);

        // 4. Побочные эффекты (после успешного сохранения)
        $this->mailer->sendOrderConfirmation($order);
        $this->logger->info('Order paid', ['order_id' => $orderId]);

        return $order;
    }
}
```

#### Шаг 3: Инфраструктура (Репозиторий с Prepared Statements)

```php
// Infrastructure/Repository/DoctrineOrderRepository.php (или PDO)
class PdoOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private \PDO $pdo) {}

    public function findById(string $id): ?Order
    {
        // БЕРЕЗА ОТ SQL-ИНЕКЦИЙ
        $stmt = $this->pdo->prepare("SELECT id, email, status FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Order($row['id'], $row['email'], $row['status']);
    }

    public function save(Order $order): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$order->getStatus(), $order->getId()]);
    }
}
```

#### Шаг 4: Контроллер (Тонкий слой)

```php
// Controller/OrderController.php
class OrderController
{
    public function __construct(
        private PayOrderService $payOrderService,
        private LoggerInterface $logger
    ) {}

    public function pay(Request $req): Response
    {
        try {
            $orderId = (string) $req->get('order_id');
            
            // Валидация формата ID (опционально, но желательно)
            if (empty($orderId)) {
                throw new \InvalidArgumentException('Order ID is required');
            }

            $this->payOrderService->payOrder($orderId);

            return new Response('ok', 200);

        } catch (OrderNotFoundException $e) {
            return new Response('not found', 404);
        } catch (\DomainException $e) {
            // Например: "Order is already paid"
            return new Response($e->getMessage(), 422);
        } catch (\Exception $e) {
            // Логгируем ошибку, но не показываем детали пользователю
            $this->logger->error('Payment failed', ['error' => $e->getMessage()]);
            return new Response('Internal Server Error', 500);
        }
    }
}
```

---

### С чего начал бы я и почему?

1.  **Исправление безопасности (SQL-инъекция).** Это первоочередная задача. Без Prepared Statements код нельзя даже деплоить.
2.  **Вынос логики из контроллера.** Контроллер должен быть тонким. Логика оплаты — это бизнес-процесс, он должен жить в Сервисе.
3.  **Внедрение зависимостей (DI).** `Mailer`, `Logger`, `PDO` должны внедряться в конструктор. Это позволяет мокать их в тестах.
4.  **Добавление транзакции/атомарности.** Нужно гарантировать, что смена статуса и отправка письма происходят последовательно и без потерь.
5.  **Обработка ошибок.** Каждый тип ошибки (404, 422, 500) должен обрабатываться отдельно.

### Итоговый результат

*   **Безопасность:** SQL-инъекции исключены.
*   **Тестируемость:** Можно написать unit-тест для `PayOrderService`, замокав `OrderRepository` и `Mailer`.
*   **Читаемость:** Каждый слой делает свою работу.
*   **Надежность:** Ошибки логируются, а не проедаются.

Этот код стал сложнее в написании, но **значительно проще в поддержке**. Именно в этом суть Clean Architecture: платить сложность один раз сейчас, чтобы не платить за баги и переписывания годами.

> ⏱ 15.6s · prompt 69243 tok · output 2008 tok

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

Этот код — **антипаттерн «Тупой Доган»** (Dumb Entity / Anemic Domain Model), который нарушает фундаментальные принципы DDD и Clean Architecture.

Давайте разберем, что здесь не так, и как это исправить.

---

### 1. Что не так?

#### 1.1. Нарушение инкапсуляции и SRP (Single Responsibility Principle)
Сущность `User` знает о `EntityManagerInterface`. Это значит, что доменный слой **зависит от инфраструктурного слоя** (Doctrine).
*   **Проблема:** Вы не можете использовать этот класс `User` в тестах без подключения к БД. Вы не можете заменить Doctrine на Eloquent или любую другую ORM, не меняя бизнес-код.
*   **Суть:** Сущность отвечает не только за бизнес-логику (смену email), но и за **сохранение данных**. Это две разные ответственности.

#### 1.2. Нарушение границы транзакций
Метод `changeEmail` вызывает `$this->em->flush()`.
*   **Проблема:** `flush` коммитит **все** изменения в `EntityManager`, а не только изменения этого объекта. Если в другом месте кода была создана новая сущность `Order`, но не сохранена, `flush` внутри `User::changeEmail` сохранит и `Order`, и `User`.
*   **Последствие:** Непредсказуемое поведение транзакций. Нарушение атомарности.

#### 1.3. Отсутствие валидации (Бизнес-правил)
Метод `changeEmail(string $email)` принимает любой тип.
*   **Проблема:** Если передадут `''` или `'not-an-email'`, сущность попадет в **невалидное состояние**.
*   **Суть:** DDD требует, чтобы доменные объекты **всегда** были валидными. Невалидное состояние должно быть немыслимым (Unrepresentable).

#### 1.4. Отсутствие событий (Domain Events)
Смена email — это важное бизнес-событие.
*   **Проблема:** Система не может оповестить об этом других частей (например, отправить письмо «Ваш email изменен», обновить поисковый индекс).
*   **Суть:** Сущность должна сообщать о фактах, а не только менять данные.

---

### 2. Как бы я исправил?

Я разделил бы ответственности:
1.  **Доменная сущность `User`** — чистый PHP, валидация, бизнес-логика.
2.  **Сервис (Use Case)** — оркестрация: загрузка, изменение, сохранение.
3.  **Репозиторий** — сохранение данных.

#### Шаг 1: Чистая доменная сущность

```php
// Domain/Entity/User.php
namespace App\Domain\Entity;

use App\Domain\Exception\InvalidEmailException;
use App\Domain\Event\UserEmailChanged;

class User
{
    private string $id;
    private string $email;
    private array $domainEvents = []; // Собираем события

    public function __construct(
        string $id,
        string $email
    ) {
        $this->id = $id;
        $this->setEmail($email); // Валидация при создании
    }

    public function id(): string
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email): void
    {
        $oldEmail = $this->email;
        $this->setEmail($email);

        // Генерируем доменное событие
        $this->domainEvents[] = new UserEmailChanged($this->id, $oldEmail, $email);
    }

    private function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
        
        if ($email === $this->email) {
            return; // Не меняем, если email не изменился (опционально)
        }

        $this->email = $email;
    }

    public function collectDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = []; // Очищаем
        return $events;
    }
}
```

#### Шаг 2: Доменное событие

```php
// Domain/Event/UserEmailChanged.php
namespace App\Domain\Event;

class UserEmailChanged
{
    public function __construct(
        public readonly string $userId,
        public readonly string $oldEmail,
        public readonly string $newEmail
    ) {}
}
```

#### Шаг 3: Исключение

```php
// Domain/Exception/InvalidEmailException.php
namespace App\Domain\Exception;

class InvalidEmailException extends \DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email format: {$email}");
    }
}
```

#### Шаг 4: Сервис (Use Case) — здесь управление транзакцией

```php
// Application/Service/ChangeUserEmailService.php
namespace App\Application\Service;

use App\Domain\Entity\User;
use App\Domain\Event\UserEmailChanged;
use App\Domain\Exception\InvalidEmailException;
use App\Domain\Repository\UserRepositoryInterface;

class ChangeUserEmailService
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private EventDispatcherInterface $eventDispatcher, // Абстракция событий
        private EntityManagerInterface $em
    ) {}

    public function changeEmail(string $userId, string $newEmail): void
    {
        // 1. Загружаем сущность
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        // 2. Меняем состояние (валидация внутри сущности)
        $user->changeEmail($newEmail);

        // 3. Сохраняем (flush здесь, а не в сущности!)
        $this->em->persist($user);
        $this->em->flush();

        // 4. Публикуем события
        foreach ($user->collectDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
```

#### Шаг 5: Контроллер (Тонкий слой)

```php
// Controller/UserController.php
class UserController
{
    public function __construct(
        private ChangeUserEmailService $changeEmailService
    ) {}

    public function updateEmail(Request $request): Response
    {
        try {
            $userId = $request->getAttribute('id');
            $newEmail = $request->get('email');

            $this->changeEmailService->changeEmail($userId, $newEmail);

            return new Response('Email updated', 200);

        } catch (InvalidEmailException $e) {
            return new Response($e->getMessage(), 422);
        } catch (UserNotFoundException $e) {
            return new Response('User not found', 404);
        } catch (\Exception $e) {
            // Логирование
            return new Response('Internal Server Error', 500);
        }
    }
}
```

---

### Сравнение

| Критерий | Было (Старый код) | Стало (Новый код) |
| :--- | :--- | :--- |
| **Зависимости** | `User` знает о `EntityManager` | `User` чистый PHP |
| **Тестируемость** | Нужно мокать `EntityManager` | Тестируем `User` напрямую |
| **Валидация** | Отсутствует | В сущности (`setEmail`) |
| **Транзакции** | `flush` внутри сущности | `flush` в сервисе |
| **События** | Нет | Есть (`UserEmailChanged`) |
| **Слой** | Домен + Инфраструктура в одном | Раздельно |

### Почему так лучше?

1.  **Домен изолирован.** Вы можете взять класс `User` и использовать его в консольном скрипте, в микросервисе, в тесте — без подключения к БД.
2.  **Контроль транзакций.** Вы решаете, когда коммитить. Вы можете сгруппировать несколько операций в одну транзакцию.
3.  **Гибкость.** Если завтра нужно добавить проверку «email уже занят», вы делаете это в сервисе или в репозитории, не трогая сущность `User`.
4.  **Реактивность.** Система может реагировать на смену email (событие), отправляя уведомления, обновляя индексы.

**Главный принцип:** Сущность должна отвечать на вопрос **«Что такое User?»**, а не **«Как сохранить User в БД?»**.

> ⏱ 16.6s · prompt 71398 tok · output 2061 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Работа с незнакомой технологией — это не «позорище», а стандартная часть взрослой разработки. Моя цель в такой ситуации — **минимизировать время разговора (Time-to-Value)** и **максимизировать уверенность в решении**.

Вот мой алгоритм действий, разделенный на 5 фаз.

---

### Фаза 1: Контекст и Разведка (Первые 15–30 минут)

Прежде чем открывать документацию, я выясняю **границы задачи**.

1.  **Уточнение требований:**
    *   «Какой именно результат ожидается? API? Консольная утилита? Скрипт?»
    *   «Есть ли у нас уже опыт использования этой технологии в других проектах? (Спросить у команды/погуглить в репозиториях)».
    *   «Какие ограничения по производительности/безопасности/версии?»
2.  **Поиск «коллег по несчастью»:**
    *   Поиск в GitHub по названию технологии + «php integration», «api example», «docker».
    *   Чтение `README.md` и `CHANGELOG.md` (чтобы понять зрелость проекта).
    *   Просмотр `Issues` в GitHub: часто там уже описаны типичные грабли, которые я столкнусь.

**Вывод фазы:** Я понимаю, что именно мне нужно сделать и какие подводные камни могут быть.

---

### Фаза 2: Песочница и MVP (1–3 часа)

Я **никогда** не начинаю писать продакшен-код на незнакомом стеке «сразу в репозиторий».

1.  **Создание изолированной песочницы:**
    *   Отдельная папка или отдельный репозиторий.
    *   Минимальная конфигурация (Docker-контейнер, если технология серверная).
2.  **Написание «Hello World» на этой технологии:**
    *   Если это БД: создать таблицу, вставить строку, прочитать.
    *   Если это очередь: отправить сообщение, получить его.
    *   Если это UI-библиотека: отрисовать компонент.
3.  **Проверка «критичных» фич:**
    *   Если мне нужна интеграция с PHP: пробую сделать запрос, обработать ошибку, закрыть соединение.
    *   Если нужны асинхронные операции: проверяю, как они работают.

**Цель:** Понять **базовый синтаксис** и **модель ошибок**. Если я умею создать объект и вызвать метод — я могу двигаться дальше.

---

### Фаза 3: Проектирование решения (30–60 минут)

Сейчас я знаю достаточно, чтобы спроектировать архитектуру.

1.  **Выбор библиотек:**
    *   Какие официальные клиенты существуют?
    *   Какие неофициальные обертки популярны?
    *   Проверить лицензию и активность разработки.
2.  **Написание псевдокода:**
    *   Как я буду взаимодействовать с этой технологией?
    *   Где я буду перехватывать ошибки?
    *   Как я буду управлять жизненным циклом (соединения, сессии)?
3.  **Оценка рисков:**
    *   «Что будет, если технология упадет?» (Нужен ли fallback?)
    *   «Как я буду тестировать это?» (Нужен ли мок? Контейнер?)

**Вывод фазы:** У меня есть план и понимание, какие именно методы мне нужно будет знать для реализации.

---

### Фаза 4: Реализация (По объему задачи)

1.  **Постепенное усложнение:**
    *   Сначала база (создание соединения/клиента).
    *   Затем основная логика.
    *   Потом обработка ошибок.
    *   Наконец, интеграция в общее приложение.
2.  **Докладирование:**
    *   Если задача большая, я делаю промежуточные коммиты (даже если код «грязный»), чтобы показать прогресс.
    *   Если я застрял на 1 час — я формулирую вопрос конкретно (см. Фаза 5).

---

### Фаза 5: Когда просить помощи, а когда разбираться самому?

Это самый важный навык.

#### 🟢 Разбирайся САМОСТОЯТЕЛЬНОНО, если:
1.  **Время решения < 2–3 часов.** Если я могу решить это за 2 часа, стоит потратить это время, чтобы получить новый навык.
2.  **Это «узкое» знание.** Например, как настроить конкретный параметр в конфиге. Это лучше поискать в доке, чем спрашивать.
3.  **У команды нет экспертов.** Если я буду спрашивать, кто-то потратит 10 минут на объяснение, а я все равно не пойму.
4.  **Я уже нашел решение, но не уверен.** Лучше спросить: «Я сделал так: [код]. Это правильный подход для этой технологии?», чем «Как это сделать?».

#### 🔴 Проси ПОМОЩЬ, если:
1.  **Я потрачиил > 3–4 часов на поиск ответа.** Это сигнал, что проблема не в моей внимательности, а в сложности самой технологии или в том, что я упускаю ключевой момент.
2.  **Проблема критична для дедлайна.** Если дедлайн завтра, а я не могу решить за 2 часа — я должен использовать опыт коллег, чтобы не потерять день.
3.  **Архитектурное решение.** Если я не уверен, *как* интегрировать технологию в систему (например, синхронно или асинхронно), это решение должно принимать команда или тимлид.
4.  **Безопасность или производительность.** Если я не уверен, не нарушаю ли я лучшие практики этой технологии в части безопасности (например, управление сессиями, шифрование).
5.  **Проблема повторяется.** Если я столкнулся с ней уже во второй раз — значит, это системная проблема, и нужно либо обучиться, либо выбрать другую технологию.

---

### Как формулировать просьбу о помощи?

❌ **Плохо:** «Я не понимаю, как работает этот фреймворк, помогите».
✅ **Хорошо:**
> «Я пытаюсь реализовать [задача] с помощью [технология].
> Я уже сделал [попытки 1, 2].
> Я столкнулся с ошибкой [ошибка].
> Я думаю, что проблема в [гипотеза].
> Кто-то из команды работал с этой технологией? Или есть документация, которой я не заметил?»

---

### Итог: Моя стратегия

1.  **Не паниковать.** Незнакомая технология — это просто набор правил, которые нужно выучить.
2.  **Сначала MVP.** Не писать финальный код, пока не разобрались в «Hello World».
3.  **Изоляция.** Песочница, чтобы не сломать проект.
4.  **Лимит времени.** 3 часа на самостоятельный поиск — это разумный предел.
5.  **Конкретика в вопросах.** Всегда приносить к команде не «проблему», а «проблему с гипотезой и попытками решения».

Такой подход превращает «незнакомую технологию» из угрозы в **возможность для роста** команды и самого разработчика.

> ⏱ 17.3s · prompt 73534 tok · output 1734 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Я **немедленно** сообщаю об этом факте тем, кто ждет результат (тимлиду, менеджеру или заказчику).

**Я никогда не жду дедлайна, чтобы сказать, что опаздываю.**

Вот как я это делаю и почему:

### 1. Что я делаю сразу

Открываю мессенджер или иду на короткий созвон и говорю примерно следующее:

> «Слушай, я обещал задачу к завтрашнему дню, но в процессе реализации я понял, что она включает [конкретное сложное место, например, нестандартную интеграцию с API / сложную логику валидации], из-за чего объем работы оказывается больше. Мне потребуется еще один рабочий день, итого — к [дата]. Я уже сделал [прогресс: 80% кода, все тесты написаны, осталась интеграция]. Есть ли у тебя риски из-за этого сдвига? Если да, давай обсудим, что можно вынести из MVP, чтобы уложиться в сроки».

### 2. Почему именно так

**А. Честность — это валюта доверия.**
Если я промолчу и до утра буду гонять код, а в 10:00 скажу «не успел» — это один провал.
Если я скажу об этом сегодня в 14:00 — это нормальное рабочее обсуждение рисков.

**Б. У людей есть время адаптироваться.**
Если дедлайн был «к завтрашнему 10:00», а я говорю об этом сегодня, у менеджера есть возможность:
- Переставить демо.
- Отложить зависящие задачи.
- Привлести другого разработчика, если задача критична.

**В. Это показывает, что я управляю своим временем, а не оно мной.**
Я не «потерялся» в задаче. Я проанализировал ее, понял реальную сложность и честно докладываю.

### 3. Что я НЕ делаю

- ❌ **Не молча работаю до полуночи**, надеясь, что «вдруг успею».
- ❌ **Не жду утра** и не говорю «я не успел».
- ❌ **Не оправдываюсь** («я думал, что будет проще», «я не учел»). Просто факт: задача оказалась больше.

### 4. Как я защищаюсь на будущее

После такой ситуации я анализирую:
- Почему я не увидел эту сложность изначально?
- Что я могу добавить в процесс оценки (например, более детальную декомпозицию на подзадачи, более консервативный буфер).

Это превращает «провал сроков» в **урок для улучшения процессов**, а не в личный провал.

> ⏱ 7.1s · prompt 75312 tok · output 630 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это один из самых важных навыков инженера. Я решаю эту проблему не интуицией, а через **набор конкретных критериев** и **понимание контекста**.

Короткий ответ: **Код «достаточно хорош», когда он работает, безопасен, покрыт тестами, понятен и соответствует архитектурным правилам проекта. Всё остальное — это оптимизация, которая может подождать.**

Вот мой детальный алгоритм.

---

### 1. Чек-лист «Достаточно хорошо» (Definition of Done)

Прежде чем я нажимаю «Готово к ревью» или «Мерж», код должен пройти по всем пунктам. Если хотя бы один пункт не выполнен — код не готов.

| Критерий | Что проверяю | Почему это важно |
| :--- | :--- | :--- |
| **1. Работает** | Тесты проходят (Unit + Integration). | Если не работает — это не код. |
| **2. Безопасен** | Нет SQL-инъекций, XSS, утечек токенов. | База безопасности. |
| **3. Понятен** | Имена говорят сами за себя. Нет магии. | Будущий разработчик (в т.ч. я сам) должен понять код за 30 секунд. |
| **4. Тестируем** | Новые ветки логики покрыты тестами. | Гарантирует, что рефакторинг не сломает код. |
| **5. Архитектура** | Не нарушены границы слоёв (Domain не знает о Doctrine). | Сохраняет чистоту кодовой базы. |
| **6. Стиль** | Пройден линтер (PHP-CS-Fixer, PHPStan). | Убирает шум и единый стиль. |

**Если этот чек-лист пройден — код можно отдавать.**

---

### 2. Когда продолжать улучшать (Где проходит граница)?

Здесь я использую принцип **Goldilocks**: не слишком просто (грязно), не слишком сложно (переусложнено).

#### 🟢 Остановись и отдай код, если:
1.  **Критерии приемки выполнены.** Если бизнес-правила работают — фича готова.
2.  **Выполнено правило 80/20.** 80% ценности доставлено 20% усилий. Оставшиеся 20% сложности стоят 80% усилий. Если это не влияет на критичные метрики — отложи.
3.  **Код читается как английский язык.** Если я могу объяснить код коллеге за 2 минуты без указки на код — он достаточно хорош.
4.  **Нет «красных флагов».** Нет `TODO`, `HACK`, `FIXME`, комментариев «временно», жестко зашитых значений.

#### 🔴 Продолжай улучшать, если:
1.  **Критичная бизнес-логика.** Если это деньги, безопасность, данные — тут качество должно быть максимальным. Тестируй все edge-cases.
2.  **Высокая частота изменений.** Если модуль будет меняться часто — инвестируй в его качество сейчас (инкапсуляция, чистые интерфейсы).
3.  **Код «запах»** (Code Smell). Если есть дублирование, длинные методы, «магия» — это долг, который нужно оплатить. Лучше сейчас, чем потом.
4.  **Отсутствие тестов на критичном пути.** Если нет тестов на основной сценарий — код не готов.

---

### 3. Как соблюдать баланс между качеством и скоростью?

#### Правило 1: Качество — это не роскошь, это скорость
Многие думают: «Тесты замедляют разработку». Это ложь.
*   **Без тестов:** Написал код → Заметил баг через неделю → Трудно найти → Трудно исправить.
*   **С тестами:** Написал код → Тесты поймали баг сразу → Исправил за 5 минут.
*   **Вывод:** Тесты — это **самая быстрая** форма разработки в долгосрочной перспективе.

#### Правило 2: Не оптимизируй то, что не болит
Не пиши идеальный код для функции, которая будет удалена через месяц.
*   **Как отличать:** Спрашивай себя: «Будет ли этот код меняться часто?». Если да — делай качественно. Если нет — сделай достаточно.

#### Правило 3: Инвестирование в инфраструктуру
*   **PHPStan / Psalm:** Настрой на высоком уровне. Это **автоматический** контроль качества. Если код не проходит статический анализ — он не мержится.
*   **CI/CD:** Автоматический запуск тестов на каждом пуше. Это **быстрее**, чем запускать тесты локально.
*   **Рефакторинг:** Не пишите фичи поверх грязного кода. Сначала почините фундамент (правило «Бери и чини»).

#### Правило 4: Компромисс, а не выбор
Если дедлайн сдвинулся, **первое, что я режу** — это:
1.  Редкие edge-cases (если они не критичны).
2.  Дополнительную оптимизацию производительности (если текущая в пределах SLA).
3.  Идеальную архитектуру (если можно сделать «достаточно хорошим» модульным подходом).

**Никогда не режу:**
1.  Тесты на критичную бизнес-логику.
2.  Безопасность.
3.  Читаемость кода (имена, структура).

---

### 4. Практический пример

**Задача:** Сделать поиск по товарам.

**Вариант «Быстро» (Но не «достаточно хорошо»):**
```php
$products = $db->query("SELECT * FROM products WHERE name LIKE '%" . $q . "%'");
return $products;
```
*   ❌ SQL-инъекция.
*   ❌ Нет тестов.
*   ❌ Нет обработки пустого запроса.
*   ❌ Медленный (полное сканирование).

**Вариант «Достаточно хорошо» (Мой выбор):**
```php
public function search(string $query): array
{
    if (empty($query)) {
        return [];
    }

    // 1. Валидация (защита от слишком длинных запросов)
    $query = mb_substr($query, 0, 50);

    // 2. Безопасный запрос (Prepared Statements)
    $stmt = $this->db->prepare("SELECT id, name, price FROM products WHERE name LIKE :q LIMIT 50");
    $stmt->execute(['q' => '%' . $query . '%']);

    // 3. Форматирование (DTO)
    return $stmt->fetchAll(ResultSet::FETCH_CLASS);
}
```
*   ✅ Безопасен.
*   ✅ Обработаны edge-cases.
*   ✅ Ограничена нагрузка на БД (LIMIT).
*   ✅ Понятный код.

**Вариант «Перфекционизм» (Подождет):**
*   Интеграция с Elasticsearch.
*   Full-text search.
*   Синонимы, морфология.
*   Кэширование результатов.

**Почему третий вариант подождет?**
Потому что второй вариант решает проблему бизнеса («пользователь может найти товар») и при этом безопасен и понятен. Третий вариант — это оптимизация, которую стоит делать, когда пользовательской базы станет больше, и второй вариант станет бутылочным горлышком.

---

### Итог

**Код «достаточно хорош», когда:**
1.  Он решает задачу бизнеса.
2.  Он безопасен и стабилен.
3.  Он понятен команде.
4.  Он покрыт тестами.
5.  Он соответствует архитектурным правилам.

**Баланс между качеством и скоростью — это не выбор между ними. Это способность делать «достаточно хорошо» быстро, а «идеально» — тогда, когда это действительно нужно.**

> ⏱ 17.7s · prompt 75999 tok · output 1865 tok

---
