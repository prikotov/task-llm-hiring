# Собес: `orcarouter/Qwen3.8-27B-Uncensored:latest` · роль: backend — run-03

- Дата: 2026-10-07T02:09:50.364146+00:00
- Сервер: http://nserv:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Здравствуйте! Рад приветствовать вас.

Как языковая модель искусственного интеллекта, мой «опыт» в разработке на PHP и смежных технологиях строится не на годами прожитой рабочей практике, а на анализе огромного массива данных: документации, исходных кодов популярных проектов (включая ядро Symfony и Laravel), обсуждений на форумах, статей о лучших практиках (Clean Code, SOLID) и реальных примеров решений архитектурных задач.

Моя «экспертиза» проявляется в способности:
*   Синтезировать информацию из разных источников и предлагать решения проблем.
*   Понимать контекст: от простых скриптов до сложных микросервисных архитектур.
*   Объяснять сложные концепции (например, DDD или Clean Architecture) на понятных языках с примерами кода.
*   Рефакторить код, опираясь на актуальные стандарты (PSR) и современные паттерны проектирования.

Я не «чувствую» код интуитивно, как человек, но я могу анализировать его структуру, находить антипаттерны, предлагать улучшений и писать новые модули, следуя заданным требованиям.

---

### Оценка моих знаний по шкале 0–10

| Раздел | Оценка | Обоснование (одним предложением) |
| :--- | ::--- | :--- |
| **PHP 8** | **9.5** | Я глубоко понимаю синтаксис, внутренние механизмы и новые фичи PHP 8.x (enums, readonly, fiber, JIT-компилятор) и могу писать идиоматичный код с соблюдением PSR-стандартов. |
| **Symfony** | **9** | Я хорошо знаю архитектуру Symfony, его компоненты (HttpClient, Messenger, Security, Form), экосистему пакетов и способы кастомизации ядра. |
| **Doctrine** | **9** | Я уверенно владею ORM-апрентацией, DQL, маппингом сущностей, оптимизацией запросов и работой с репозиториями в контексте PHP-приложений. |
| **PostgreSQL** | **8.5** | Я хорошо понимаю SQL, особенности PostgreSQL (JSONB, полнотекстовый поиск, хранимые процедуры) и умею эффективно взаимодействовать с ним через PDO или ORM. |
| **DDD** | **8** | Я понимаю принципы доменно-ориентированного проектирования, разграничение слоёв, агрегаты и тактики внедрения DDD, но практическая глубина зависит от контекста и деталей вашего домена. |
| **Чистая архитектура** | **8.5** | Я знаком с принципами Clean Architecture, Dependency Inversion Rule и умею проектировать системы, где бизнес-логика отделена от фреймворков и инфраструктуры. |
| **Тестирование** | **9** | Я отлично понимаю стратегии unit-, integration- и e2e-тестирования, работаю с PHPUnit/Pest, мокирую зависимости и следую принципам тест-драйвированной разработки (TDD). |
| **Git** | **9.5** | Я уверенно владели Git: ветвление, разрешение конфликтов, рефакторинг истории, работа с submodules и настройка CI/CD-пайплайнов, интегрированных с репозиторием. |

---

**Что дальше?**  
Вы можете задать конкретный вопрос, показать фрагмент кода для анализа или описать архитектурную задачу — я помогу найти лучшее решение.

> ⏱ 12.8s · prompt 89 tok · output 803 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Для написания чистого и надёжного кода в PHP 8.x ключевым является переход от «динамической гибкости» к «статической строгости». Ниже — самые важные фичи, которые реально повышают качество кода, с краткими примерами.

### 1. Типизация аргументов и возвращаемых значений (в т.ч. union types)
Точные типы предотвращают runtime-ошибки и делают код самодокументируемым.

```php
// PHP 8.0+: Union types
function calculateTotal(string|int $amount, bool $hasDiscount): int
{
    if (is_int($amount)) {
        return $amount;
    }
    $numericAmount = (int)$amount;
    return $hasDiscount ? intdiv($numericAmount, 1) : $numericAmount;
}

// Теперь вызов с ошибочным типом упадёт сразу:
calculateTotal(100.5, true); // TypeError: must be of type string|int
```

### 2. Named Arguments
Повышает читаемость при работе с функциями, имеющими много аргументов. Особенно полезно при конструировании объектов или вызове API.

```php
// До PHP 8: трудно читать, легко перепутать порядок
$user = new User('John', 30, true, 'admin', '2023-01-01');

// PHP 8: имена аргументов делают намерение явным
$user = new User(
    name: 'John',
    age: 30,
    isActive: true,
    role: 'admin',
    createdAt: '2023-01-01'
);
```

### 3. Readonly Properties (PHP 8.1+)
Гарантирует неизменяемость объектов после конструирования. Это фундамент для создания чистых функций и предсказуемых моделей (особенно в DDD).

```php
class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {}

    // Попытка изменения вызовет Error:
    // $money->amount = 100; 
}
```

### 4. Enums (PHP 8.1+)
Заменяют магические константы и строковые сравнения. Enums строгие: невозможно создать enum с неопределённым значением.

```php
enum OrderStatus: string
{
    case New = 'new';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
}

// Сравнение через ===, а не через строки
if ($order->status === OrderStatus::Delivered) {
    // ...
}

// Легко перебрать все значения
foreach (OrderStatus::cases() as $status) {
    echo $status->value;
}
```

### 5. Constructor Property Promotion (PHP 8.0+)
Сокращает шаблонный код, убирая дублирование свойств в конструкторе.

```php
// До PHP 8.0
class Article
{
    private string $title;
    private string $author;

    public function __construct(string $title, string $author)
    {
        $this->title = $title;
        $this->author = $author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

// PHP 8.0+
class Article
{
    public function __construct(
        public readonly string $title,
        public readonly string $author
    ) {}
}
```

### 6. First-class Callable Syntax (PHP 8.1+)
Упрощает передачу методов как значений, особенно полезно для higher-order функций (map, filter, etc.).

```php
class User
{
    public function getName(): string { return 'Alice'; }
}

$users = [new User(), new User()];

// До PHP 8.1: 
$names = array_map(fn($u) => $u->getName(), $users);

// PHP 8.1+: cleaner syntax
$names = array_map(User::getName(...), $users);
```

### 7. Nullsafe Operator (PHP 8.0+)
Убирает «пирамиду из `if`» при работе с глубоко вложенными объектами.

```php
// До PHP 8.0
$city = null;
if ($user && $user->getProfile() && $user->getProfile()->getAddress()) {
    $city = $user->getProfile()->getAddress()->getCity();
}

// PHP 8.0+
$city = $user?->getProfile()?->getAddress()?->getCity() ?? 'Unknown';
```

---

### Итог
Эти фичи позволяют писать код, который:
- **Читаемее** (named args, enums).
- **Надёжнее** (strict types, readonly, enums).
- **Короче и чище** (constructor promotion, nullsafe operator).

Если вы используете **PHP 8.2+**, также обратите внимание на **Attributes** (аналог аннотаций) и **Readonly Classes**, которые ещё сильнее упрощают создание неизменяемых DTO и Value Objects.

> ⏱ 15.4s · prompt 945 tok · output 1205 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP часто называют «гибкой», но на деле это «сложной». PHP — язык динамической типизации со статической опцией строгости. Понимание того, как именно PHP приводит типы и где эта механика ломается, — ключ к написанию надёжного кода.

### 1. `declare(strict_types=1)`

Это не просто флаг, а переключатель режима взаимодействия с системой типов.

*   **Без strict_types (weak typing):** PHP пытается «угадать» намерения разработчика и автоматически приводит типы (casting) в большинстве контекстов.
*   **С strict_types (strong typing):** PHP требует точного совпадения типов. Если вы передали `int` в параметр, ожидающий `float`, вы получите `TypeError`, а не молчаливое приведение.

> **Важно:** `strict_types` действует только на функции и методы, **определённые в том же файле**, где стоит `declare`. Она не влияет на вызовы функций из других файлов (если в них не стоит свой `declare`), и не влияет на операторы сравнения (`==`) и присвоения (`=`).

```php
declare(strict_types=1);

function doMath(float $amount): float {
    return $amount * 2;
}

// 1) Работает: 10.5 — это float
doMath(10.5);

// 2) Ошибка (TypeError): 10 — это int, а не float
doMath(10); 

// 3) Но... в других файлах без strict_types:
// function otherFunc(string $s) {} 
// otherFunc(123); // Приведёт к "123", если otherFunc не в strict-файле
```

### 2. Механика приведений типов (Type Juggling)

PHP имеет несколько режимов приведения:
1.  **Молчаливое (Weak):** При присвоении, аргументах функций (без strict), результатах операций.
2.  **Строгое (Strict):** Только при `strict_types=1` и только для аргументов функций.
3.  **Явное (Casting):** `(int)`, `(string)`, `(array)` и т.д.

#### Ключевые правила приведения (Weak Mode):

| Тип | Как приводит к | Пример |
| :--- | :--- | :--- |
| **string** | Если начинается с числа → `int`/`float` | `"123abc"` → `123` (в арифметике) |
| **string** | Если не числовое → `0` | `"hello"` → `0` |
| **null** | В большинстве контекстов → `false` или `""` | `null + 5` → `5` |
| **bool** | `true` → `1`, `false` → `0` | `true + 10` → `11` |
| **array** | Приведение к `int`/`float`/`string` | `[]` → `0`, `["a"]` → `1` (размер) |

### 3. Типичные ловушки и примеры

#### Ловушка 1: Сравнение строк и чисел с `==`

```php
declare(strict_types=1);

$a = "10";
$b = 10;

var_dump($a == $b);   // bool(true) — Weak comparison, PHP приведёт строку к числу
var_dump($a === $b);  // bool(false) — Strict comparison, типы разные

// Опасность с hex/октальными:
var_dump("0x1F" == 31); // bool(true) — PHP 8+ может интерпретировать hex
var_dump("08" == 8);    // bool(true) — В PHP < 8 "08" было 0, в PHP 8 это 8
```

**Совет:** Всегда используйте `===` для сравнения, если не хотите, чтобы PHP угадывал типы.

#### Ловушка 2: Приведение в арифметических операциях

```php
declare(strict_types=1);

// Здесь strict_types НЕ применяется к операциям, только к аргументам функций
$result = "100" * 2; 
var_dump($result); // int(200) — строка приведена к числу

$result = "abc" + 5; 
var_dump($result); // int(5) — "abc" приведён к 0

// Но:
$result = "0x1F" + 0; // В PHP 8: int(31), в старых версиях могло быть 0
```

#### Ловушка 3: `strict_types` не спасает от присвоения

```php
declare(strict_types=1);

function getUserAge(int $age): int {
    return $age;
}

$age = "25"; // Это string в памяти

// ОШИБКА: TypeError, потому что getUserAge ожидает int
echo getUserAge($age);

// Но:
$newAge = "25" + 0; // Приведение при операции: int(25)
echo getUserAge($newAge); // Работает
```

#### Ловушка 4: `null` и ложные значения

```php
declare(strict_types=1);

function processValue(string $value): string {
    return $value;
}

// null НЕ является string, даже в weak mode (для аргументов)
// Но:
$result = null ?? "default"; // "default"
$result = null + 10;         // 10 (null приведён к 0)
$result = null * "10";       // 0 (null -> 0, "10" -> 10)

// Ловушка с bool:
if (0) {
    echo "Это выполнится? НЕТ, 0 — false в контексте if";
}

if ("0") {
    echo "Это выполнится? НЕТ, '0' — тоже false в контексте if";
}

if ("00") {
    echo "Это выполнится? ДА, не-пустая строка, не '0'";
}
```

### 4. Где типизация спасает, а где удивляет

#### ✅ Спасает:

1.  **Идемпотентность и предсказуемость:**
    ```php
    declare(strict_types=1);
    
    function calculateTax(float $income, float $rate): float {
        return $income * $rate;
    }
    
    calculateTax(1000, 0.2); // 200.0
    calculateTax(1000, "0.2"); // TypeError! Вы сразу видите проблему.
    ```
    Без `strict_types` вы бы получили `200.0` и могли бы задуматься, был ли это float или строка.

2.  **Работа с API и базами данных:**
    ```php
    // PDO возвращает строки по умолчанию
    $id = $stmt->fetchColumn(); // string("42")
    
    // Если функция ожидает int, strict_types поймает ошибку на входе,
    // а не в глубине логики, где int используется в массивах или циклах
    function saveRecord(int $id): void { /* ... */ }
    
    saveRecord($id); // TypeError — нужно явно (int)$id
    ```

3.  **Иммутабельность через readonly:**
    ```php
    class Config {
        public function __construct(
            public readonly int $maxRetries
        ) {}
    }
    
    $config = new Config(3);
    $config->maxRetries = 5; // Error — тип (int) и значение зафиксированы
    ```

#### ⚠️ Удивляет:

1.  **Размер массива при приведении к числу:**
    ```php
    $arr = [1, 2, 3];
    var_dump((int)$arr); // 3 — длина массива!
    var_dump((string)$arr); // "Array"
    ```

2.  **`false` в числовых контекстах:**
    ```php
    var_dump(false + 5); // 5 — false = 0
    var_dump(true * "10"); // 10 — true = 1
    ```

3.  **Поведение `declare(strict_types=1)` в наследовании:**
    ```php
    // file_a.php (без strict_types)
    class Base {
        public function add(int $a, int $b): int {
            return $a + $b;
        }
    }
    
    // file_b.php (с strict_types=1)
    declare(strict_types=1);
    class Child extends Base {
        // Если вы переопределяете add() здесь, strict_types из file_b
        // будет применяться к этому конкретному методу.
    }
    
    // Но если вы вызываете Base::add() из file_b, строгость не гарантирована
    // для аргументов, переданных извне (хотя в PHP 8 это стало строже).
    ```

### 5. Рекомендации для чистого кода

1.  **Всегда используйте `declare(strict_types=1);`** в каждом файле. Это стандарт де-факто в современных PHP-проектах (Laravel 9+, Symfony 6+).
2.  **Избегайте implicit conversions.** Если вам нужно привести тип, делайте это явно: `(int)$str`, `(float)$num`.
3.  **Используйте `===` и `!==`** для всех сравнений, кроме случаев, когда вы точно знаете, что хотите сравнить значения, игнорируя типы (редко).
4.  **Не полагайтесь на `bool` для хранения состояния.** Используйте `enum` или константы.
5.  **Проверяйте типы входных данных из внешних источников** (HTTP, JSON, DB) на границе системы, а не в глубине бизнес-логики.

```php
// Пример «границы»
public function fromArray(array $data): self
{
    return new self(
        id: (int)($data['id'] ?? 0),
        name: (string)($data['name'] ?? ''),
        isActive: (bool)($data['is_active'] ?? false)
    );
}
```

Такой подход делает систему типов вашим союзником, а не источником скрытых багов.

> ⏱ 31.0s · prompt 2208 tok · output 2417 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование системы исключений — это не просто выбор классов `try-catch`. Это вопрос **контракта взаимодействия** между слоями архитектуры. В чистом коде (Clean Architecture) или DDD исключения должны двигаться извне внутрь (для обработки) и изнутри наружу (для сигнализации), но не наоборот.

Вот как я проектирую работу с ошибками в современных PHP-приложениях (Symfony/Doctrine контекст).

### 1. Философия: Исключение — это «Красный Флаг»

Исключение должно бросаться только в тех случаях, когда:
1.  Метод **не может** завершить работу успешно (precondition failed).
2.  Ситуация является **непредвиденной** для текущего контекста (например, файл не найден в сервисе, который ожидает его наличие).
3.  Нужно прервать текущий поток исполнения и передать управление более высокому слою (контроллеру, глобальному обработчику), который знает, как ответить пользователю (404, 500, 503).

**Главное правило:** *Никогда не ловите исключения, которые вы не можете обработать (convert to something else).*

---

### 2. Проектирование иерархии исключений

Я делю исключения на три категории:

#### A. Domain Exceptions (Бизнес-логика)
Эти ошибки живут в доменном слое. Они не знают о Symfony, Doctrine или HTTP.

```php
namespace App\Domain\Payment;

// Базовый класс для всех ошибок домена "Payment"
class PaymentException extends \RuntimeException
{
    // Можно добавить контекст: $orderID, $amount
}

// Конкретные ошибки
class InsufficientFundsException extends PaymentException {}
class CurrencyMismatchException extends PaymentException {}
```

*Почему `RuntimeException`?* Потому что это ошибки, которые не являются ошибками синтаксиса или среды выполнения (как `TypeError`), а логическими противоречиями в бизнесе.

#### B. Infrastructure Exceptions (Внешние зависимости)
Эти ошибки возникают при работе с БД, API, фреймворком. Мы **оборачиваем** чужие исключения в свои, чтобы не тянуть `Doctrine\DBAL\Exception` в сервисы.

```php
namespace App\Infrastructure\Persistence;

class UserNotFoundException extends \App\Domain\User\UserException {}

// Внутри репозитория:
public function find(int $id): User
{
    try {
        $user = $this->connection->get(User::class)->find($id);
        if (!$user) {
            throw new UserNotFoundException("User with id {$id} not found");
        }
        return $user;
    } catch (DoctrineException $e) {
        // Оборачиваем техническую ошибку в доменную или инфраструктурную
        throw new PersistenceException("Failed to fetch user", $e);
    }
}
```

#### C. Application/Controller Exceptions (UI/HTTP)
Обычно их не делают отдельными классами, а используют стандартные Symfony (`NotFoundHttpException`, `AccessDeniedException`) или маппят доменные исключения в HTTP-ответы.

---

### 3. Контракт метода: Что бросать, а что возвращать?

Это самый спорный момент. Я придерживаюсь принципа **«Fail Fast»** и **JIT (Just In Time) errors**.

#### Правило 1: Если метод возвращает `null`, значит, отсутствие значения — это нормальное состояние.
```php
// ХОРОШО: Возврат null означает "не найдено", но это ожидаемое состояние
public function findByEmail(string $email): ?User
{
    return $this->repository->findOneBy(['email' => $email]);
}

// ХОРОШО: Явная проверка на существование
public function get(int $id): User 
{
    $user = $this->repository->find($id);
    if (!$user) {
        throw new UserNotFoundException($id);
    }
    return $user;
}
```
*Почему?* `findByEmail` может возвращать `null`, если пользователя просто нет. Это не ошибка. Но `get()` предполагает, что пользователь *должен* существовать. Если его нет — это нарушение контракта (precondition).

#### Правило 2: Если ошибка влияет на транзакцию или состояние мира — бросаем исключение.
Если операция мутабельна (изменяет БД, отправляет письмо), и она завершилась с ошибкой, мы должны бросить исключение, чтобы откатить транзакцию.

```php
public function chargeCard(CreditCard $card, Money $amount): void
{
    // 1. Проверка бизнес-правил (домен)
    if ($amount->getAmount() <= 0) {
        throw new InvalidArgumentException("Amount must be positive");
    }
    
    // 2. Внешний вызов (инфраструктура)
    try {
        $response = $this->stripeClient->charge($card->getNumber(), $amount);
    } catch (StripeException $e) {
        // Оборачиваем в доменную ошибку, чтобы UI не знал про Stripe
        throw new PaymentDeclinedException($e->getMessage(), $e);
    }
    
    // 3. Сохранение результата
    $card->markAsCharged($amount);
    $this->em->persist($card);
    $this->em->flush();
}
```

#### Правило 3: Не бросайте исключения для контроля потока (Flow Control)
Исключения должны быть для **ошибок**, а не для ветвления логики.

```php
// ПЛОХО
public function getCurrency(): string
{
    try {
        return $this->context->getCurrency();
    } catch (ContextException $e) {
        return 'USD'; // Fallback
    }
}

// ХОРОШО
public function getCurrency(): string
{
    return $this->context->getCurrency() ?? 'USD';
    // Или явная проверка: if (isset($this->context->currency)) ...
}
```

---

### 4. Оборачивание чужих исключений (Wrapping)

Никогда не пробрасывайте `Doctrine\DBAL\Exception` или `GuzzleHttp\Exception\RequestException` в контроллер. Это нарушает принцип инверсии зависимостей.

**Стратегия Wrapping:**

1.  **На границе инфраструктуры:** Ловим внешнее исключение.
2.  **Преобразуем** в свое, добавляя контекст (что пытались сделать, какие ID).
3.  **Храним оригинал** в `previousException` (через `$e` в конструкторе), чтобы при отладке видеть трейкстек.

```php
namespace App\Infrastructure\Api;

class ExternalApiException extends \RuntimeException
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

class PaymentGatewayClient
{
    public function processPayment(Order $order): PaymentResult
    {
        try {
            $response = $this->httpClient->post('/charge', [...]);
            return PaymentResult::fromResponse($response);
        } catch (HttpExceptionInterface $e) {
            // Логируем техническую деталь
            $this->logger->error('Payment gateway error', [
                'status' => $e->getStatusCode(),
                'body' => $e->getResponseBody()
            ]);
            
            // Бросаем бизнес-исключение
            throw new ExternalApiException(
                "Payment gateway returned {$e->getStatusCode()} for order #{$order->getId()}", 
                $e // Ссылка на оригинал
            );
        }
    }
}
```

---

### 5. Антипаттерны

#### ❌ 1. "Swallowing Exceptions" (Поглощение)
```php
try {
    $this->service->doSomething();
} catch (\Throwable $e) {
    // Пустой блок! Ошибка потеряна.
}
```
*Последствие:* Баг скрыт, пользователь видит успешный ответ, но данные не сохранены.

#### ❌ 2. Catching Too Broadly
```php
try {
    $user = $this->repo->find($id);
} catch (Exception $e) {
    return null; // А что если БД упала? Мы тоже вернем null!
}
```
*Последствие:* Вы не различаете "пользователя нет" (ожидаемо) и "БД отвалилась" (авария).

#### ❌ 3. Using Exceptions for Validation (Микро-валидация)
```php
public function setPrice(float $price): void
{
    if ($price < 0) {
        throw new InvalidArgumentException("Price must be positive");
    }
    $this->price = $price;
}
```
*Вопрос:* Это нормально? **Да**, если это **Value Object** или **доменная сущность**. Нет, если это простой DTO или getter/setter в форме. В формах лучше использовать `Symfony\Form` validation. В домене — исключения.

#### ❌ 4. Rethrowing Without Context
```php
try {
    $this->sendEmail($user);
} catch (EmailException $e) {
    throw $e; // Просто перекидывает, теряя контекст "какого именно пользователя"
}
```
*Хорошо:*
```php
throw new UserNotificationFailedException("Failed to notify user #{$user->getId()}", $e);
```

#### ❌ 5. Mixing HTTP and Domain in Exceptions
```php
// Плохо: Домен знает про HTTP
class User {
    public function delete(): void {
        if (!$this->canBeDeleted()) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("User is locked");
        }
    }
}
```
*Хорошо:*
```php
// Домен
class User {
    public function delete(): void {
        if (!$this->canBeDeleted()) {
            throw new UserLockedException();
        }
    }
}

// Контроллер
public function delete(User $user): JsonResponse
{
    try {
        $user->delete();
        $this->em->remove($user);
        $this->em->flush();
        return new JsonResponse(null, 204);
    } catch (UserLockedException $e) {
        return new JsonResponse(['error' => 'User is locked'], 400);
    }
}
```

---

### 6. Глобальная обработка (Symfony Event Subscribers)

В контроллерах мы избегаем `try-catch` для типовых ошибок (404, 403). Мы используем **Event Subscribers**.

```php
class GlobalExceptionHandler implements EventSubscriber
{
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // 1. Доменные ошибки
        if ($exception instanceof UserNotFoundException) {
            $event->setResponse(new JsonResponse(['error' => 'Not found'], 404));
        }
        
        // 2. Аутентификация
        if ($exception instanceof AccessDeniedException) {
            $event->setResponse(new JsonResponse(['error' => 'Forbidden'], 403));
        }
        
        // 3. Неизвестные ошибки — логирование и 500
        if ($event->getResponse() instanceof EmptyResponseInterface) {
            $this->logger->critical($exception);
            $event->setResponse(new JsonResponse(['error' => 'Internal Server Error'], 500));
        }
    }
}
```

### Итог: Мой подход

1.  **Домен** бросает свои исключения (`Domain\Exception`).
2.  **Инфраструктура** оборачивает внешние ошибки в доменные или свои `Infrastructure\Exception`.
3.  **Аппликационный слой/Контроллер** либо мапит их в HTTP-ответы, либо полагается на глобальный обработчик.
4.  **Никогда** не ловим исключения, если не можем их обработать (преобразовать в значение или другой тип).
5.  **Всегда** храним оригинальное исключение в `$previous`, чтобы не терять стектрейс при отладке.

Такой подход делает код предсказуемым, легким в тестировании (mocking domain exceptions) и устойчивым к изменениям в инфраструктуре.

> ⏱ 37.4s · prompt 4681 tok · output 2869 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему «опыту» анализа огромного количества реальных PHP-проектов, самым часто нарушаемым принципом является **SRP (Single Responsibility Principle — Принцип единой обязанности)**.

Почему он?
Потому что SRP — это не про «о класс — одна функция» (это миф). SRP про **причины изменения**. Если класс меняется по двум или более причинам, он нарушает SRP.
В реальном коде это выглядит как **«God Object»**: класс `OrderService`, который валидирует данные, считает налоги, отправляет email, сохраняет в БД и логирует операции. Он меняется, когда:
1. Меняется логика расчёта налогов.
2. Меняется шаблон письма.
3. Меняется схема БД.
4. Меняется логика валидации.

Это классическое нарушение. Давайте разберём на примере.

---

### ❌ Антипаттерн: Нарушение SRP

Представьте типовой сценарий оформления заказа.

```php
class OrderService
{
    private UserRepo $userRepo;
    private PaymentGateway $payment;
    private EmailSender $mailer;
    private TaxCalculator $taxer;
    private Logger $logger;
    private EntityManagerInterface $em;

    // Класс зависит от всего на свете.
    public function __construct(
        UserRepo $userRepo,
        PaymentGateway $payment,
        EmailSender $mailer,
        TaxCalculator $taxer,
        Logger $logger,
        EntityManagerInterface $em
    ) {
        $this->userRepo = $userRepo;
        $this->payment = $payment;
        $this->mailer = $mailer;
        $this->taxer = $taxer;
        $this->logger = $logger;
        $this->em = $em;
    }

    public function placeOrder(User $user, Cart $cart): Order
    {
        // 1. Валидация (Причина изменения 1: Правила валидации)
        if ($cart->isEmpty()) {
            throw new InvalidArgumentException('Cart is empty');
        }
        if ($user->isBlocked()) {
            throw new DomainException('User is blocked');
        }

        // 2. Расчёт налогов (Причина изменения 2: Логика налогов)
        $subtotal = $cart->getSubtotal();
        $tax = $this->taxer->calculate($subtotal);
        $total = $subtotal + $tax;

        // 3. Создание сущности (Причина изменения 3: Структура данных БД)
        $order = new Order($user, $cart, $total, $tax);
        $this->em->persist($order);
        $this->em->flush();

        // 4. Оплата (Причина изменения 4: Интеграция с платёжкой)
        try {
            $this->payment->charge($user->getPaymentMethod(), $total);
        } catch (PaymentException $e) {
            $this->logger->error('Payment failed', ['order_id' => $order->getId()]);
            $order->markAsFailed();
            $this->em->flush();
            throw $e;
        }

        // 5. Уведомление (Причина изменения 5: Шаблоны писем)
        $this->mailer->send($user->getEmail(), 'Your order #'.$order->getId().' is confirmed', 'Details...');

        // 6. Логирование успешности (Причина изменения 6: Формат логов)
        $this->logger->info('Order placed successfully', ['id' => $order->getId()]);

        return $order;
    }
}
```

**Проблемы:**
1.  **Тестирование:** Чтобы протестировать только расчёт налогов, вам нужно мокировать `UserRepo`, `PaymentGateway`, `EmailSender`, `Logger`, `EntityManager`. Это дорого и хрупко.
2.  **Изменения:** Чтобы изменить шаблон письма, вы обязаны трогать `OrderService`, хотя логика заказа не менялась.
3.  **Читаемость:** Метод `placeOrder` делает 6 разных вещей.

---

### ✅ Рефакторинг: Применение SRP (и косвенно DIP)

Мы разбиваем ответственности на отдельные классы, каждый из которых имеет **одну причину для изменения**.

#### 1. Доменная логика (Бизнес-правила)
*Причина изменения: Правила бизнеса (блокировка пользователя, пустая корзина).*

```php
class Order
{
    public function __construct(
        private User $user,
        private Cart $cart,
        private Money $total
    ) {}

    // Бизнес-логика внутри сущности
    public static function create(User $user, Cart $cart): self
    {
        if ($cart->isEmpty()) {
            throw new CartEmptyException();
        }
        if ($user->isBlocked()) {
            throw new UserBlockedException();
        }

        // Расчёт суммы происходит в домене, если он знает правила, 
        // или делегируется в Value Object Money
        $subtotal = $cart->getSubtotal();
        // Tax logic лучше вынести в отдельный доменный сервис или Value Object
        return new self($user, $cart, $subtotal); 
    }

    public function markAsPaid(): void { /* ... */ }
}
```

#### 2. Инфраструктурный сервис для налогов
*Причина изменения: Изменение формул налогов или интеграция с внешним налоговым сервисом.*

```php
interface TaxCalculatorInterface
{
    public function calculate(Money $subtotal): Money;
}

class StandardTaxCalculator implements TaxCalculatorInterface
{
    public function calculate(Money $subtotal): Money
    {
        return $subtotal->multiply(0.21);
    }
}
```

#### 3. Инфраструктурный сервис для платежей
*Причина изменения: Смена платёжного провайдера (Stripe -> PayPal).*

```php
interface PaymentProcessorInterface
{
    public function charge(PaymentMethod $method, Money $amount): void;
}

class StripePaymentProcessor implements PaymentProcessorInterface
{
    public function charge(PaymentMethod $method, Money $amount): void
    {
        // Интеграция со Stripe
    }
}
```

#### 4. Инфраструктурный сервис для уведомлений
*Причина изменения: Изменение шаблонов писем или добавление SMS.*

```php
interface NotificationSender
{
    public function sendOrderConfirmation(User $user, Order $order): void;
}
```

#### 5. Аппликационный сервис (Координатор)
*Его единственная обязанность — **оркестрировать** процесс. Он не знает деталей.*
*Причина изменения: Изменение порядка шагов в процессе оформления заказа.*

```php
class OrderPlacementService
{
    public function __construct(
        private TaxCalculatorInterface $taxCalculator,
        private PaymentProcessorInterface $paymentProcessor,
        private NotificationSender $notifier,
        private EntityManagerInterface $em
    ) {}

    public function placeOrder(User $user, Cart $cart): Order
    {
        // 1. Создаём доменную сущность (бизнес-валидация внутри)
        $order = Order::create($user, $cart);

        // 2. Считаем налоги (отдельная ответственность)
        $tax = $this->taxCalculator->calculate($order->getSubtotal());
        $order->addTax($tax); // Или пересчёт суммы внутри Order

        // 3. Сохраняем
        $this->em->persist($order);
        $this->em->flush();

        // 4. Оплачиваем
        $this->paymentProcessor->charge($user->getPaymentMethod(), $order->getTotal());
        $order->markAsPaid();
        $this->em->flush();

        // 5. Уведомляем
        $this->notifier->sendOrderConfirmation($user, $order);

        return $order;
    }
}
```

---

### Что изменилось?

1.  **`OrderPlacementService`** стал тонким. Он только координирует. Если мы захотим добавить шагов (например, "проверить промокод"), мы просто добавим вызов в этот сервис. Но сама логика промокода будет в отдельном классе.
2.  **`TaxCalculator`** можно заменить на `VATTaxCalculator` без изменения `OrderService`.
3.  **`StripePaymentProcessor`** можно заменить на `PayPalProcessor`.
4.  **Тестирование:**
    *   Тест `StandardTaxCalculator`: простой, без моков.
    *   Тест `OrderPlacementService`: мы мокеем интерфейсы (`TaxCalculatorInterface`, `PaymentProcessorInterface`), но не конкретные реализации. Мы проверяем, что сервис правильно *вызывает* эти компоненты.

### Вывод

**SRP** — это главный ключ к поддерживаемой архитектуре.
*   **Нарушение:** Один класс делает всё.
*   **Рефакторинг:** Разделение на "Что делает" (Домен) и "Как делает" (Инфраструктура), а затем оркестрация этих действий в Аппликационном слое.

Именно из-за нарушения SRP возникают "магические" классы, которые невозможно прочитать и обновить без риска сломать всё остальное.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, принципы SOLID — это не закон Божий, а набор эвристики (правил большого пальца). Они описывают, *как лучше* строить систему, но не всегда говорят, *нужно ли* строить ее именно так в данном контексте.

В инженерии, как и в жизни, **нет решений без компромиссов (trade-offs)**.

### Почему можно нарушать SOLID?

1.  **Простота (KISS):** Иногда «красивая» архитектура с 5 интерфейсами, 3 сервисами и 2 репозиториями сложнее для понимания, чем один «грязный» класс, который читается за 10 секунд.
2.  **Производительность:** Излишняя абстракция (DIP/SRP) может создавать накладные расходы на память и время (дополнительные объекты, виртуальные вызовы, косвенность).
3.  **Скорость разработки:** Если проект живет 2 недели и будет выключен, не стоит тратить день на проектирование интерфейсов.
4.  **Неопределенность требований:** Если вы не знаете, что изменится, создание широких абстракций (ISP) может быть ошибкой. Вы создадите интерфейсы под гипотетические сценарии, которые никогда не случатся.

---

### Пример оправданного нарушения: **Принцип Инверсии Зависимостей (DIP) + Сегрегации Интерфейсов (ISP)**

**Контекст:** Небольшой, но критичный скрипт обработки данных (Batch Job) или внутренний админ-инструмент, который используется редко.

Задача: Нужно отправить отчет о продажах за месяц.

#### ❌ «Книжная» архитектура (SOLID-подход)

Мы хотим следовать DIP (зависимости на абстракции) и ISP (интерфейсы должны быть маленькие).

```php
// 1. Интерфейс для чтения данных (ISP: маленький интерфейс)
interface SalesDataReaderInterface {
    public function getSalesForMonth(int $month, int $year): array;
}

// 2. Реализация (зависит от БД)
class DbSalesReader implements SalesDataReaderInterface {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function getSalesForMonth(int $month, int $year): array {
        // SQL query...
    }
}

// 3. Интерфейс для формирования отчета
interface ReportGeneratorInterface {
    public function generate(array $salesData): string;
}

// 4. Реализация
class HtmlReportGenerator implements ReportGeneratorInterface {
    public function generate(array $salesData): string {
        // HTML template...
    }
}

// 5. Интерфейс для отправки
interface ReportSenderInterface {
    public function send(string $report, string $email): void;
}

// 6. Реализация
class SmtpReportSender implements ReportSenderInterface {
    public function send(string $report, string $email): void {
        // Mail logic...
    }
}

// 7. Оркестратор (Aplikacion Service)
class SalesReportService {
    public function __construct(
        private SalesDataReaderInterface $reader,
        private ReportGeneratorInterface $generator,
        private ReportSenderInterface $sender
    ) {}

    public function sendReport(int $month, int $year, string $email): void {
        $data = $this->reader->getSalesForMonth($month, $year);
        $report = $this->generator->generate($data);
        $this->sender->send($report, $email);
    }
}
```

**Проблема:** Для одной простой задачи мы создали **7 классов/интерфейсов**.
*   Если логика чтения данных изменится, нужно менять `DbSalesReader`.
*   Если изменится формат HTML, нужно менять `HtmlReportGenerator`.
*   Но если нам нужно *временно* отладить процесс, добавив `var_dump()` между шагами, нам нужно менять `SalesReportService` или писать отдельный скрипт для запуска этих зависимостей.
*   Для теста нам нужно создать 3 мока или фикстуры.

#### ✅ Оправданное нарушение (Pragmatic Approach)

Мы сознательно нарушаем **DIP** (зависимости на конкретные реализации) и **SRP/ISP** (объединяем обязанности в один класс), потому что **простота и скорость отладки важнее здесь**.

```php
class SendSalesReportJob
{
    private PDO $pdo;
    private string $smtpHost;
    private int $smtpPort;

    public function __construct(PDO $pdo, string $smtpHost = 'localhost', int $smtpPort = 25)
    {
        $this->pdo = $pdo;
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
    }

    /**
     * Единственный метод: делает все три шага.
     * Мы нарушаем SRP: этот класс читает, формирует и отправляет.
     * Мы нарушаем DIP: мы знаем про PDO и SMTP конкретно.
     */
    public function run(int $month, int $year, string $email): void
    {
        // 1. Чтение (внутри метода, без отдельного интерфейса)
        $sql = "SELECT * FROM sales WHERE month = :m AND year = :y";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['m' => $month, 'y' => $year]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            throw new RuntimeException("No data for report");
        }

        // 2. Формирование (внутри метода, простой шаблон)
        $html = "<h1>Sales Report</h1><table>";
        foreach ($rows as $row) {
            $html .= "<tr><td>{$row['date']}</td><td>{$row['amount']}</td></tr>";
        }
        $html .= "</table>";

        // 3. Отправка (внутри метода, простой mail function)
        $headers = "From: no-reply@myshop.com";
        $subject = "Sales Report {$month}-{$year}";
        
        if (!mail($email, $subject, $html, $headers)) {
            throw new RuntimeException("Failed to send email");
        }
    }
}
```

### Почему это оправданно?

1.  **Легче читать:** Разработчик видит весь процесс в одном месте. Не нужно прыгать по 7 файлам, чтобы понять, что происходит.
2.  **Легче отлаживать:** Можно поставить брейкпоинт в один метод.
3.  **Меньше кода:** Нет «пустых» интерфейсов.
4.  **Контекст:** Это внутренний джоб, который не планируется к расширению. Если завтра потребуются PDF отчёты, мы **перепроектируем** этот класс, разбив его на интерфейс `ReportGenerator`. Но сейчас мы тратим ресурсы зря, если создадим эту абстракцию заранее (Over-design).

### Когда нарушение НЕ оправдано?

Если этот код находится в **ядре бизнес-логики** (например, расчёт стоимости заказа), и вы знаете, что:
*   Способы чтения данных могут меняться (БД, API, файл).
*   Формат отчета может меняться (HTML, PDF, CSV).
*   Способ отправки может меняться (Email, Slack, Webhook).

...тогда нарушение SRP/DIP будет стоить вам дорого при каждом изменении. В этом случае «книжная» архитектура оправдана.

### Итог

**SOLID — это не цель, а инструмент.**
*   Используйте SOLID, когда система **растет**, **сложна** и **долговечна**.
*   Нарушайте SOLID (упрощайте), когда система **маленькая**, **стабильная** или **временная**.

Лучший тест: **«Если бы я завтра переписывал этот код с нуля, я бы сделал его проще или сложнее?»**
Если проще — значит, текущая SOLID-архитектура избыточна, и ее упрощение (нарушение принципов) будет правильным решением.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Принцип открытости для расширения и закрытости для модификации (OCP — Open/Closed Principle) часто ассоциируют с наследованием и интерфейсами в рамках ООП. Однако это лишь один из способов.

В функциональном программировании и современных подходах к дизайну (Clean Architecture, Hexagonal Architecture) OCP реализуется через **инъекцию зависимостей**, **стратегии**, **композицию** и даже **конфигурацию**.

Вот основные способы реализации OCP, **не использующие классическое наследование**:

---

### 1. Стратегия через Инъекцию (Dependency Injection)

Это самый чистый и рекомендуемый способ в PHP/Symfony. Вы не меняете код клиента (тот, кто использует функциональность), а подменяете ему реализацию (объект) в момент создания.

**Суть:** Клиент знает только об общем поведении (интерфейс или тип), но не знает, *кто* его выполняет. Расширение = создание нового объекта и внедрение его в клиенте.

```php
// Интерфейс определяет контракт, но не иерархию
interface DiscountStrategy
{
    public function apply(Money $price): Money;
}

// Клиент (закрыт для модификации)
class PriceCalculator
{
    public function __construct(private DiscountStrategy $strategy) {}

    public function calculateFinalPrice(Money $basePrice): Money
    {
        return $this->strategy->apply($basePrice);
    }
}

// --- Расширение (Open for extension) ---
// Мы НЕ трогаем PriceCalculator.
// Мы создаем новый класс:
class BlackFridayDiscount implements DiscountStrategy
{
    public function apply(Money $price): Money
    {
        return $price->multiply(0.5);
    }
}

// Внедрение нового поведения:
// $calculator = new PriceCalculator(new BlackFridayDiscount());
```

**Почему это OCP без наследования?**
*   Мы не наследуем `PriceCalculator`.
*   Мы не меняем код `PriceCalculator`.
*   Мы расширяем систему, добавляя новый объект `BlackFridayDiscount`.

---

### 2. Композиция (Composition over Inheritance)

Вместо того чтобы наследовать классы с разной логикой, мы собираем поведение из мелких компонентов (коллабораторов).

**Пример:** Система отправки уведомлений.

❌ **Наследование (хрупкое):**
```php
class AbstractNotifier { /* ... */ }
class EmailNotifier extends AbstractNotifier { /* ... */ }
class SmsNotifier extends AbstractNotifier { /* ... */ }
class PushNotifier extends AbstractNotifier { /* ... */ }

// Если нужно добавить "SMS + Email", нужно создать новый класс:
class EmailAndSmsNotifier extends AbstractNotifier { /* ... */ }
// И каждый раз менять код приложения, который выбирает нотификатор.
```

✅ **Композиция (OCP-friendly):**
```php
interface Channel {
    public function send(Message $message): void;
}

class EmailChannel implements Channel { /* ... */ }
class SmsChannel implements Channel { /* ... */ }

// Клиент принимает список каналов
class NotificationService
{
    public function __construct(private array $channels) {} // array<Channel>

    public function notify(Message $message): void
    {
        foreach ($this->channels as $channel) {
            $channel->send($message);
        }
    }
}

// Расширение: просто добавляем новый канал в массив
// $service = new NotificationService([
//     new EmailChannel(),
//     new SmsChannel(),
//     new TelegramChannel() // Новый канал, не требует изменения NotificationService
// ]);
```

**Почему это OCP?**
*   `NotificationService` закрыт для модификации: он просто итерирует массив.
*   Открыт для расширения: мы можем добавить `TelegramChannel` без изменения `NotificationService`.

---

### 3. Стратегия через Функции (First-Class Callable)

В PHP 8 мы можем передавать замыкания (closures) как объекты. Это позволяет менять поведение без создания отдельных классов.

```php
class TaxCalculator
{
    // Вместо интерфейса принимаем callable
    public function __construct(private \Closure $taxFunction) {}

    public function calculate(Money $amount): Money
    {
        return $this->taxFunction($amount);
    }
}

// Использование:
// Стратегия 1: Простой налог
$calc1 = new TaxCalculator(fn(Money $m) => $m->multiply(0.1));

// Стратегия 2: Сложный налог (расширение)
$complexTax = function(Money $m): Money {
    if ($m->getAmount() > 1000) {
        return $m->multiply(0.2);
    }
    return $m->multiply(0.15);
};
$calc2 = new TaxCalculator($complexTax);
```

**Плюсы:**
*   Нет лишних файлов и классов для простых случаев.
*   Легко менять логику на лету (например, из конфигурации).

**Минусы:**
*   Сложно тестировать и переиспользовать сложные стратегии (лучше вынести в классы, если логика большая).

---

### 4. Конфигурация и Директивы (Configuration-Driven)

Иногда расширение происходит не через новый код, а через новые данные (конфиг).

**Пример:** Фильтры в админке.

```php
class FilterBuilder
{
    public function __construct(private array $availableFilters) {}

    // availableFilters = ['status', 'date', 'price']

    public function build(array $requestParams): array
    {
        $filters = [];
        foreach ($requestParams as $key => $value) {
            // OCP: мы не меняем FilterBuilder, когда добавляем новый фильтр в БД/конфиг
            if (in_array($key, $this->availableFilters)) {
                $filters[$key] = $value;
            }
        }
        return $filters;
    }
}

// Расширение:
// 1. Добавляем 'category' в конфиг availableFilters.
// 2. Добавляем маппинг 'category' -> колонка БД в отдельном маппере.
// 3. Код FilterBuilder не изменился.
```

Здесь расширение происходит через **данные**, а не через код. `FilterBuilder` остается неизменным, но поддерживает новые типы фильтров, если они описаны в конфиге.

---

### 5. Шаблонный метод через Композицию (Behavior Delegation)

Классический паттерн "Шаблонный метод" часто реализуется через наследование. Но его можно реализовать через делегирование.

```php
interface PaymentProcessor
{
    public function process(Payment $payment): void;
}

// Общий алгоритм (закрыт для модификации)
class BasePaymentFlow
{
    public function __construct(private PaymentProcessor $processor) {}

    public function run(Payment $payment): void
    {
        $this->validate($payment);           // Шаг 1: Общий
        $this->processor->process($payment); // Шаг 2: Переменный (расширяемый)
        $this->log($payment);                // Шаг 3: Общий
    }

    private function validate(Payment $p): void { /* ... */ }
    private function log(Payment $p): void { /* ... */ }
}

// Расширение:
class StripeProcessor implements PaymentProcessor { /* ... */ }
class PayPalProcessor implements PaymentProcessor { /* ... */ }

// Клиент:
$stripeFlow = new BasePaymentFlow(new StripeProcessor());
$paypalFlow = new BasePaymentFlow(new PayPalProcessor());
```

**Разница с наследованием:**
*   В наследовании: `StripeFlow extends BaseFlow`, `PayPalFlow extends BaseFlow`. Мы меняем иерархию.
*   В композиции: `BasePaymentFlow` не знает, кто ему делегирует. Мы просто внедряем другой объект.

---

### Итог: Какие способы лучше?

| Способ | Когда использовать | Преимущества |
| :--- | :--- | :--- |
| **Инъекция зависимостей (DI)** | Самый универсальный. Любые сложные системы. | Чистая архитектура, легкое тестирование, полное соответствие DIP/OCP. |
| **Композиция** | Когда нужно комбинировать несколько стратегий (напр., "отправить и по email, и по sms"). | Гибкость, нет "взрывного роста" подклассов. |
| **Замыкания (Closures)** | Простые, одноразовые стратегии, не требующие переиспользования. | Меньше кода, нет лишних файлов. |
| **Конфигурация** | Когда набор вариантов известен заранее или может меняться без деплоя кода. | Динамичность, нет перекомпиляции. |

**Главное правило:**
OCP — это не про *как* вы расширяете (наследование vs интерфейс), а про *что* вы не меняете. Если вы можете добавить новую функциональность, **не изменяя существующий код клиента** (даже если вы используете интерфейсы, замыкания или конфигурацию), — значит, вы соблюдаете OCP.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Короткий ответ: **Нет, SRP (Single Responsibility Principle) не привязан исключительно к классам.**

Хотя классы — это самая частая жертва нарушения SRP в ООП-языках (как PHP, Java, C#), суть принципа применима к **любому компоненту системы**, который имеет «причины для изменения».

Вот к каким сущностям можно и нужно применять SRP:

### 1. Функции и Методы
Это самый базовый уровень.
*   **Нарушение:** Функция `processOrder()`, которая валидирует данные, считает налоги, отправляет email и пишет в БД.
*   **Причины изменения:** Изменение логики налогов, изменение шаблона письма, смена схемы БД.
*   **Решение:** Разбить на `validateOrder()`, `calculateTax()`, `sendEmail()`, `saveOrder()`.

> **Правило:** Одна функция должна делать одну вещь и иметь одну причину меняться. Если вы меняете логику отправки писем, вы не должны переписывать логику расчёта налогов.

### 2. Модули и Пакеты (Namespaces)
В PHP namespace — это логическая группа классов.
*   **Нарушение:** Namespace `App\Services` содержит `EmailSender`, `PaymentGateway` и `OrderCalculator`.
*   **Причины изменения:** Изменение SMTP-провайдера, смена платёжной системы, изменение формулы расчёта заказа.
*   **Решение:** Разделить на `App\Infrastructure\Messaging`, `App\Infrastructure\Payment`, `App\Domain\Order`.

Если вы меняете SMTP, вы не должны трогать файлы в `Payment` или `Order`.

### 3. Базы Данных (Таблицы)
Да, SRP применим к структуре данных (хотя здесь спорнее, но важно для нормализации).
*   **Нарушение:** Таблица `users`, содержащая `email`, `password_hash`, `last_login_ip`, `marketing_opt_in`, `balance`.
*   **Причины изменения:** Изменение политики паролей (требуются новые поля), изменение логики маркетинга (новые флаги), изменения финансового учёта.
*   **Решение:** Вынести `marketing_profile` в отдельную таблицу, `financial_account` в другую.
*   **Почему?** Если вы меняете требования к маркетингу, вы не должны мигрировать таблицу с паролями и IP-адресами.

### 4. Конфигурационные файлы
*   **Нарушение:** Один файл `config.php` содержит настройки БД, настройки SMTP, параметры кэша, права доступа.
*   **Причины изменения:** Смена версии БД, смена SMTP-провайдера, изменение политики кэширования.
*   **Решение:** `database.php`, `mail.php`, `cache.php`.
*   **Плюс:** Это облегчает переопределение настроек в разных окружениях (dev/stage/prod) и снижает риск «забудем поменять один параметр».

### 5. Frontend Компоненты (React/Vue/Angular)
Хотя PHP — бэкенд, важно понимать, что SRP — общий архитектурный принцип.
*   **Нарушение:** Компонент `UserDashboard`, который получает данные из API, форматирует даты, рисует график и управляет состоянием модального окна.
*   **Причины изменения:** Изменение API, изменение стиля графика, изменение UX модального окна.
*   **Решение:** Вынести логику API в хук/сервис, граф в отдельный компонент, модалку в отдельный компонент.

---

### Как определить, нарушен ли SRP у *любого* сущности?

Используйте вопрос **«Причина изменения» (Reason for Change)**:

1.  Возьмите сущность (класс, функцию, таблицу, файл).
2.  Представьте, что завтра изменится требование.
3.  Сколько *разных* причин может потребовать изменения *этой* сущности?

*   **1 причина** → SRP соблюдён.
*   **2+ независимые причины** → SRP нарушен.

**Пример с таблицей:**
*   Таблица `orders`.
*   Причина 1: Изменилась структура заказа (добавили поле `discount_code`).
*   Причина 2: Изменились требования к аудиту (добавили `created_by`, `updated_at`).
*   **Вывод:** Таблица `orders` нарушает SRP. Лучше разделить на `orders` (бизнес-данные) и `order_audit` (системные метаданные), хотя на практике их часто держат вместе из-за нормализации 3NF. Но в DDD-подходе это аргумент в пользу выделения отдельных сущностей.

---

### Важное уточнение: Границы ответственности

SRP говорит о том, что **каждая единица кода должна иметь одну и только одну причину меняться**. Но что является «единицей кода»?

*   Для класса — это класс.
*   Для функции — это функция.
*   Для модуля — это модуль.

Однако, есть нюанс: **Слишком мелкое разделение (Over-decomposition)** тоже вредно.
*   Если вы создадите класс `Get`, `Set`, `Is` для каждого свойства, вы нарушите **KISS** (Keep It Simple, Stupid) и **ISP** (Interface Segregation), и сделаете систему труднообслуживаемой.

**Баланс:**
SRP — это про *логические* границы, а не про *физические* (о класс = одна строка кода).

### Итог

SRP — это **универсальный принцип модульности**, а не просто правило для ООП-классов.

*   **Классы:** Часто нарушают SRP, будучи «богами».
*   **Функции:** Часто нарушают SRP, будучи слишком длинными.
*   **Модули:** Нарушают SRP, если смешивают домен и инфраструктуру.
*   **Таблицы:** Нарушают SRP, если смешивают данные разных сущностей.
*   **Конфиги:** Нарушают SRP, если смешивают разнородные настройки.

Поэтому, когда вы рефакторите, смотрите не только на классы, но и на функции, файлы, таблицы и зависимости.

> ⏱ 28.5s · prompt 7596 tok · output 2149 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер внедрения зависимостей (Dependency Injection Container, DI Container) в Symfony — это не «магия» во время выполнения (runtime), а результат **статического анализа** на этапе сборки (build time).

Чтобы понять, как это работает, нужно разделить процесс на два этапа:
1.  **Компиляция (Build Time):** Когда вы запускаете `composer install`, `bin/console cache:clear` или при первом запросе после изменения кода.
2.  **Выполнение (Runtime):** Когда ваш код фактически работает и запрашивает сервисы.

---

### 1. Что происходит при компиляции контейнера?

Когда Symfony загружается впервые (или кэш очищен), он запускает **Compiler Passes**. Это последовательность алгоритмов, которые анализируют ваш код и конфигурацию, чтобы создать оптимизированный PHP-файл (обычно `Container.php` в `var/cache/dev/Container.../`).

#### Ключевые шаги компиляции:

1.  **Сбор метаданных:**
    *   Анализ `services.yaml` (или `services.php`), где вы объявляете сервисы.
    *   Анализ PHP-атрибутов (Attributes) и аннотаций в классах.
    *   Анализ интерфейсов и реализаций (для autowiring).

2.  **Разрешение зависимостей (Wiring):**
    *   Контейнер смотрит: «Какие аргументы нужны этому сервису?»
    *   Если аргумент — конкретный класс, и он зарегистрирован в контейнере, он создает ссылку.
    *   Если аргумент — интерфейс, а реализаций несколько, он ищет, какой из них отмечен как `@Service` или имеет приоритет (через `autowire_alias` или атрибуты).

3.  **Autowiring (Автоматическое внедрение):**
    *   Symfony использует **типовизацию** (type hints).
    *   Если у метода или свойства есть тип `EmailSenderInterface`, контейнер ищет в своем реестре все классы, которые реализуют этот интерфейс.
    *   Если находит ровно один кандидат — он автоматически внедряет его.
    *   Если находит несколько — он пытается найти «лучший» (через атрибуты `@service` или порядок загрузки), либо выбрасывает ошибку компиляции, если не может решить неоднозначность.

4.  **Оптимизация и Минимизация:**
    *   **Removal of unused services:** Если сервис объявлен, но ни к чему не привязан и не используется, он может быть удален (в debug-режиме он остается, но помечается).
    *   **Inlining (Разворачивание):** Для простых сервисов (без циклических зависимостей) Symfony может «встроить» код создания сервиса прямо в код сервиса, который его использует, чтобы избежать вызова метода `get()` из контейнера. Это ускоряет выполнение.
    *   **Lazy Loading:** Сервисы, которые используются редко, оборачиваются в Proxy (прокси-объекты), чтобы не создавать их при загрузке приложения, а только при первом фактическом вызове метода.

5.  **Генерация PHP-кода:**
    *   В результате компиляции генерируется файл `Container.php`, который содержит:
        *   Метод `public function get_ServiceName(): ServiceName`.
        *   Внутри метода — инструкции по созданию объекта и его зависимостей.
        *   Это **чистый PHP-код**, не сериализация, не YAML, а именно код.

---

### 2. Как работает Autowiring?

**Autowiring** — это механизм, который позволяет не вручную указывать зависимости в `services.yaml`, если они могут быть определены автоматически.

#### Условия работы:
1.  **Строгая типизация:** Параметры конструктора или методы должны иметь `type hint` (класс или интерфейс).
2.  **Реестр сервисов:** Реализации должны быть известны контейнеру (либо через `services.yaml`, либо через `autoconfigure`).

#### Пример:

```php
// services.yaml
services:
    App\Service\OrderService:
        autowire: true
        autoconfigure: true
        # Не нужно указывать аргументы!
```

```php
class OrderService
{
    // Контейнер увидит тип PaymentGatewayInterface
    // и сам найдет реализацию, например, StripeGateway
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private LoggerInterface $logger // Встроенный сервис Symfony
    ) {}
}
```

**Что делает контейнер при компиляции:**
1.  Видит, что `OrderService` требует `PaymentGatewayInterface`.
2.  Смотрит в реестр: «Кто реализует `PaymentGatewayInterface`?»
3.  Находит `StripeGateway` (зарегистрированный как сервис).
4.  В генерируемом коде для `OrderService` пишет:
    ```php
    public function get_OrderService(): OrderService
    {
        $paymentGateway = $this->get_StripeGateway();
        $logger = $this->get_Psr_Log_LoggerInterface();
        return new OrderService($paymentGateway, $logger);
    }
    ```
5.  Если реализаций две (`StripeGateway` и `PayPalGateway`), контейнер выбросит ошибку при компиляции, если вы не укажете явно, какой использовать (через `!service` или атрибуты).

---

### 3. Что происходит в момент первого запроса сервиса?

Теперь, когда контейнер скомпилирован (файл `Container.php` существует и загружен в память), давайте посмотрим, что происходит в runtime.

#### Сценарий 1: Простой сервис (без оптимизаций)

```php
// В контроллере:
class SomeController extends AbstractController
{
    public function action(OrderService $orderService): Response
    {
        $orderService->placeOrder();
    }
}
```

1.  **Запрос в контейнер:** Контроллер (или его фабрика) вызывает `$container->get(OrderService::class)`.
2.  **Поиск в кэше:** Контейнер проверяет внутренний массив `$this->services` (или `$this->loading`), есть ли уже экземпляр `OrderService`.
3.  **Создание (если нет):**
    *   Если сервиса еще нет, контейнер вызывает сгенерированный метод: `$this->get_OrderService()`.
    *   Этот метод рекурсивно создает зависимые сервисы (`PaymentGateway`, `Logger`).
    *   Создается новый объект `OrderService`.
4.  **Кэширование:** Новый объект сохраняется в `$this->services['order_service']`.
5.  **Возврат:** Объект возвращается контроллеру.

> **Важно:** По умолчанию Symfony использует **Singleton-паттерн**. Сервисы создаются один раз и переиспользуются в рамках одного запроса.

#### Сценарий 2: Lazy-сервис (Proxies)

Если сервис тяжелый (например, подключается к БД или внешнему API) и используется редко, Symfony может создать **Proxy**.

1.  **Запрос:** `$container->get(HeavyService::class)`.
2.  **Создание Proxy:** Вместо реального объекта создается легкий прокси-объект, который наследует от `HeavyService`.
3.  **Первый вызов метода:** Когда вы вызываете `$heavyService->doWork()`:
    *   Магия PHP (`__call` или трейт `ProxyTrait`) перехватывает вызов.
    *   Прокси запрашивает у контейнера **реальный** объект `HeavyService`.
    *   Контейнер создает реальный объект (загружает БД и т.д.).
    *   Прокси делегирует вызов метода реальному объекту.
4.  **Результат:** Вы не заметили разницы, но реальный объект был создан только тогда, когда он понадобился.

#### Сценарий 3: Shared vs Non-shared

*   **Shared (по умолчанию):** Все запросы к сервису в рамках одного запроса возвращают **один и тот же объект**.
*   **Non-shared:** Каждый вызов `$container->get()` возвращает **новый объект**. Используется редко, например, для сервисов, которые должны быть независимыми (stateless) или для тестирования.

---

### Ключевые отличия от «ручного» создания объектов

| Характеристика | Ручное создание (`new`) | Symfony DI Container |
| :--- | :--- | :--- |
| **Зависимости** | Вы должны знать, как создать каждую зависимость. | Контейнер знает, как создать все, включая вложенные. |
| **Кэширование** | Нет (если не реализовать вручную). | Singleton по умолчанию. |
| **Ленивая загрузка** | Нет (все создается сразу). | Да, через Proxies. |
| **Изменение поведения** | Нужно менять код. | Можно подменить сервис через конфиг (`!alias`, decorators). |
| **Производительность** | Быстрее для простых случаев (нет overhead). | Чуть медленнее из-за проверки в контейнере, но быстрее в сложных системах за счет кэша и оптимизации. |

---

### Что важно помнить для разработчика

1.  **Компиляция — это статический анализ.** Если вы добавите новый сервис или измените типизацию, но не очистите кэш (`bin/console cache:clear`), Symfony может использовать старый, скомпилированный код, где ваши изменения еще нет.
2.  **Autowiring не работает с абстрактными классами и интерфейсами без реализации.** Нужно, чтобы была конкретная реализация, зарегистрированная как сервис.
3.  **Порядок регистрации не важен** для autowiring, но важен для alias'ов (если есть несколько реализаций, тот, который зарегистрирован позже, может переопределить предыдущий, в зависимости от конфигурации).
4.  **Инъекция в контроллеры:** Symfony использует «Attribute-based Injection» (через атрибут `#[Value]`, `#[CurrentUser]` и т.д.) или стандартное внедрение через конструктор. Контейнер автоматически понимает, что нужно внедрить в контроллер, если он зарегистрирован как сервис (что по умолчанию и так).

### Итог

Symfony DI Container — это **компилятор**, который превращает декларативные описания зависимостей в эффективный императивный PHP-код.
*   **Компиляция:** Анализ типов, поиск реализаций, генерация методов создания объектов.
*   **Autowiring:** Маппинг интерфейсов на реализации через type hints.
*   **Runtime:** Создание объектов по запросу (с кэшированием и ленивой загрузкой).

Это делает код более предсказуемым, тестируемым и легким для изменения (замены реализаций без изменения клиента).

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости (Circular Dependencies) в контексте Dependency Injection (DI) — это ситуация, где сервис `A` зависит от `B`, а `B` в свою очередь зависит от `A`.

В PHP (и Symfony) это чаще всего приводит к одной из двух проблем:
1.  **Ошибка компиляции:** `CircularReferenceException` — контейнер не может решить, кого создавать первым.
2.  **Бесконечная рекурсия (Stack Overflow):** Если вы обойдете ошибку с помощью `@required` или ленивого внедрения, но объекты создаются синхронно в конструкторах, память может иссякнуть.

Давайте разберем, **почему** это происходит и **как** это правильно разруливать, от простых решений к архитектурным.

---

### 1. Почему это плохо?

Циклическая зависимость почти всегда является признаком **нарушения SRP (Single Responsibility Principle)**.
Если `A` знает о `B`, а `B` знает о `A`, значит, они слишком тесно связаны и, вероятно, делают слишком много работы за друг друга.

---

### Методы решения (от частного к общему)

#### Метод 1: Внедрение через свойства (Setter Injection) + `@required`

Если обе сущности нужны друг другу *только* для вызова методов, а не для создания (т.е. `A` не нужен `B` для инициализации своих полей, а только для последующих действий), можно использовать setter injection.

**Как это работает:**
1.  `A` создается первым (без `B`).
2.  `B` создается (без `A`).
3.  Symfony внедряет `B` в `A` через setter.
4.  Symfony внедряет `A` в `B` через setter.
5.  Теперь оба готовы к работе.

**Пример:**

```php
use Symfony\Component\DependencyInjection\Attribute\Required;

class ServiceA
{
    private ?ServiceB $b = null;

    // Не конструктор, а setter
    #[Required]
    public function setServiceB(ServiceB $b): void
    {
        $this->b = $b;
    }

    public function doA(): void
    {
        // Используем $this->b только здесь, не при инициализации
        $this->b->doB();
    }
}

class ServiceB
{
    private ?ServiceA $a = null;

    #[Required]
    public function setServiceA(ServiceA $a): void
    {
        $this->a = $a;
    }

    public function doB(): void
    {
        $this->a->doA();
    }
}
```

**Когда использовать:**
*   Когда `A` и `B` не зависят друг от друга в конструкторах.
*   Когда вы уверены, что вызов методов происходит после полной инициализации объектов.
*   **Минус:** Код становится менее предсказуемым (те не знаете, инициализирован ли сервис `B` в начале метода `ServiceA::__construct`).

---

#### Метод 2: Использование Proxy (Lazy Loading)

Если один из сервисов тяжелый или используется редко, Symfony может создать **Proxy** (прокси-объект). Это легкий «двойник», который не требует полной инициализации зависимостей, пока вы не вызовете у него реальный метод.

**Настройка в `services.yaml`:**
```yaml
services:
    App\Service\ServiceA:
        # Указываем, что ServiceB нужно внедрить лениво
        calls:
            - [setServiceB, ['@service_b', 'by_lazy']]
            # Или используйте атрибут #[AutoconfigureLazy] на свойстве/параметре
```

Или более современный подход с атрибутом (Symfony 5.3+):
```php
use Symfony\Contracts\Service\ServiceSubscriberInterface;
// Или просто используйте @required + @autoconfigure_lazy

class ServiceA
{
    #[Required]
    private ServiceB $b;
    
    // ...
}
```

**Как это работает:**
1.  При создании `ServiceA` в него внедряется *Proxy* на `ServiceB`.
2.  `ServiceB` может создаваться позже, когда `ServiceA` впервые вызовет метод на `ServiceB`.
3.  К этому моменту `ServiceA` уже существует, поэтому цикл разорван.

**Когда использовать:**
*   Если одна из зависимостей действительно тяжелая (подключается к БД, внешнему API).
*   Если вы хотите минимизировать изменение кода.

---

#### Метод 3: Вынос общей логики в третий сервис (Mediator / Facade)

Это **лучшее архитектурное решение**.
Если `A` и `B` знают друг о друге, значит, есть логика, которую они должны разделять или координировать. Вынесите эту логику в третий сервис `C`.

**Было:**
*   `A` -> `B`
*   `B` -> `A`

**Стало:**
*   `C` -> `A`
*   `C` -> `B`
*   `A` знает только про `C` (или вообще не знает)
*   `B` знает только про `C` (или вообще не знает)

**Пример:**
Допустим, `OrderService` вызывает `InventoryService`, а `InventoryService` возвращает результат в `OrderService` для сохранения.

*   **Плохо:** `OrderService` имеет зависимость от `InventoryService`, а `InventoryService` имеет обратную ссылку на `OrderRepository` или `OrderService` для обновления статуса.
*   **Хорошо:**
    1.  `InventoryService` просто проверяет наличие и возвращает `bool` или `StockItem`.
    2.  `OrderService` получает этот результат, обновляет статус заказа и сохраняет.
    3.  Никто не знает про «другого» как сервис, все знают только про данные.

Если же взаимодействие сложно (событийный поток), используйте **Менеджер событий (Event Dispatcher)**.

---

#### Метод 4: Публикация событий (Event Dispatcher)

Если `A` выполняет действие, которое должно повлиять на `B`, но `A` не должен знать, *кто именно* реагирует — используйте события.

**Было:**
`A` -> `B` (прямой вызов)
`B` -> `A` (обратный вызов для обновления состояния)

**Стало:**
1.  `A` выполняет свою работу и публикует событие `OrderCompletedEvent`.
2.  `B` слушает это событие (`OrderCompletedEventSubscriber`) и реагирует (обновляет инвентарь).
3.  `B` публикует `InventoryUpdatedEvent`.
4.  `A` (или другой слушатель) обновляет состояние заказа.

**Плюсы:**
*   Полное разделение (Loose Coupling).
*   `A` не знает о `B` вообще.
*   Легко добавить новых слушателей (C, D) без изменения `A` и `B`.

**Минусы:**
*   Сложнее отладка (потоки нелинейные).
*   Нужно следить за тем, чтобы события не были «скрытой» зависимостью (если `A` критично зависит от реакции `B`, лучше использовать синхронные вызовы или Mediator).

---

### Итоговая таблица: Что выбрать?

| Ситуация | Рекомендуемое решение |
| :--- | :--- |
| **Простая взаимная зависимость, методы вызываются не в конструкторе** | **Setter Injection + `@required`** (Самый быстрый фикс) |
| **Один сервис тяжелый, другой легкий** | **Lazy Loading (Proxy)** |
| **Сложное взаимодействие, много логики «туда-сюда»** | **Вынос в Mediator (Третий сервис)** или **Event Dispatcher** |
| **Две сущности должны знать о состояниях друг друга (например, A и B — части одного агрегата)** | **Объедините их в одну доменную сущность** (Aggregate Root). Если `A` и `B` тесно связаны бизнес-правилами, они должны быть одним объектом. |

### Главный совет

Перед тем как применять хитрые трюки с DI (proxies, setters), задайте вопрос:
> **«А должны ли эти два сервиса вообще существовать отдельно?»**

Если `A` и `B` всегда используются вместе и знают о состоянии друг друга — скорее всего, это **одна сущность** (или Агрегат в DDD), а не два отдельных сервиса. Объединение их в один класс часто решает проблему циклических зависимостей и делает код понятнее.

> ⏱ 33.4s · prompt 15387 tok · output 2490 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

В классическом синхронном подходе (вызвать `Mailer->send()` и `SearchIndex->update()` прямо в контроллере или сервисе) ответ пользователю будет задерживаться на время выполнения этих операций. Если почтовый сервер медленный или индексация занимает секунды, пользователь это почувствует.

В Symfony для решения этой задачи есть три основных паттерна, выстроенных от простого к сложному.

### Вариант 1: Async Messages (Symfony Messenger) — *Рекомендуемый стандарт*

Это «родной» способ в экосистеме Symfony. Мы отправляем сообщение (Message) в очередь, а обработчик (Handler) выполняет работу в фоновом процессе.

#### Как это работает:
1.  Вы создаете объект сообщения: `new SendRegistrationEmailCommand($user)`.
2.  Вызываете `$bus->dispatch($message)`.
3.  Конвейер (Bus) передает сообщение в транспорт (Transport).
4.  **Транспорт** (например, `doctrine`/Redis/AMQP) кладет сообщение в очередь и сразу возвращает управление приложению.
5.  Отдельный воркер (`bin/console messenger:consume`) забирает сообщения из очереди и передает их обработчику (Handler).

#### Пример кода:

**1. Сообщение (DTO):**
```php
namespace App\Message;

class UserRegistered
{
    public function __construct(
        private int $userId,
        private string $email
    ) {}

    // Getters...
}
```

**2. Обработчик (Handler):**
```php
namespace App\Worker;

use App\Message\UserRegistered;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserRegisteredHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private SearchIndexerInterface $indexer
    ) {}

    public function __invoke(UserRegistered $message): void
    {
        // 1. Отправка письма
        $email = (new Email())
            ->from('no-reply@site.com')
            ->to($message->getEmail())
            ->subject('Welcome!')
            ->html('<p>Welcome to our site!</p>');

        $this->mailer->send($email);

        // 2. Обновление индекса
        // Предположим, мы загружаем пользователя по ID
        $user = $this->userRepository->find($message->getUserId());
        $this->indexer->addUser($user);
    }
}
```

**3. Диспетчеризация (в сервисе регистрации):**
```php
class RegistrationService
{
    public function __construct(
        private MessageBusInterface $bus,
        private EntityManagerInterface $em
    ) {}

    public function register(string $email, string $password): User
    {
        // ... создание и сохранение пользователя ...
        $this->em->persist($user);
        $this->em->flush();

        // Асинхронная отправка в очередь
        $this->bus->dispatch(new UserRegistered($user->getId(), $user->getEmail()));

        return $user; // Возвращаем ответ клиенту МГНОВЕННО
    }
}
```

**4. Настройка транспорта (`config/packages/messenger.yaml`):**
```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%' # redis://localhost:6379 или amqp://...
        routing:
            'App\Message\UserRegistered': async
```

**Trade-offs:**
*   ✅ **Простота:** Встроенный механизм, отличная интеграция с DI.
*   ✅ **Надежность:** Если приложение упадет, сообщения не потеряются (в Redis/RabbitMQ).
*   ✅ **Гибкость:** Легко изменить порядок операций или добавить логирование.
*   ❌ **Сложность инфраструктуры:** Нужно запускать отдельный процесс воркера (`messenger:consume`).
*   ❌ **Задержка:** Ответ приходит немедленно, но письмо может прийти через 1-5 секунд (зависит от частоты опроса очереди).

---

### Вариант 2: Event Dispatcher (Событийная модель)

Используем встроенный `EventDispatcher`. Это похоже на Messenger, но события обычно обрабатываются синхронно, если не настроено иначе. Однако, мы можем использовать `EventDispatcher` в связке с **Async** или просто сделать обработчики быстрыми.

Если мы хотим именно асинхронность, `EventDispatcher` сам по себе не решает проблему «медленных IO». Но он идеально подходит для **развязки**.

**Подход:**
1.  После регистрации dispatch-им событие `UserRegisteredEvent`.
2.  Слушатель 1: Отправляет письмо.
3.  Слушатель 2: Обновляет индекс.

Чтобы это было асинхронным, слушатели должны либо:
*   Быть быстрыми (не ждать ответа от внешних сервисов).
*   Или внутри слушателя использовать `Messenger` (см. Вариант 1).

**Trade-offs:**
*   ✅ Очень гибкая модель, легко добавить новые обработчики без изменения основного кода.
*   ❌ Если обработчики синхронные, они все равно заблокируют ответ.
*   ❌ Нужно внимательно следить за приоритетами событий.

---

### Вариант 3: Queue-based Architecture (Классические очереди: RabbitMQ/Kafka)

Если у вас высокая нагрузка, Symfony Messenger становится «узким местом» или недостаточно масштабируемым. Тогда используют прямую интеграцию с брокером сообщений.

#### Как это работает:
1.  В коде регистрации мы сериализуем данные пользователя.
2.  Публикуем сообщение в топик `user.registered` в RabbitMQ/Kafka.
3.  Ответ клиенту улетает.
4.  Отдельный микросервис (или воркер) подписан на этот топик, берет данные и делает работу.

**Trade-offs:**
*   ✅ **Максимальное масштабирование:** Можно добавить сколько угодно консьюмеров.
*   ✅ **Отказоустойчивость:** Брокеры (RabbitMQ/Kafka) очень надежны.
*   ❌ **Сложность:** Нужна отдельная инфраструктура, настройка брокера, сериализация/десериализация, отслеживание ошибок (dead letter queues).
*   ❌ **Слепота:** Труднее отладить, чем с Symfony Messenger, если нет хорошего мониторинга.

---

### Вариант 4: «Fire and Forget» через HTTP-запрос (Антипаттерн, но иногда используют)

Вы вызываете внешний API для отправки письма или индексации, но не ждете ответа (или используете `curl` с `Connection: close`).

**Trade-offs:**
*   ✅ Простота реализации.
*   ❌ Хрупкость: Если внешний сервис упадет, письмо потеряется.
*   ❌ Нет retries, нет логов внутри вашего приложения.
*   ❌ Засоряет код.

---

### Сравнение и выбор

| Критерий | Symfony Messenger | Event Dispatcher | RabbitMQ/Kafka | Fire & Forget |
| :--- | :---: | :---: | :---: | :---: |
| **Сложность внедрения** | Низкая | Низкая | Высокая | Очень низкая |
| **Надежность доставки** | Высокая | Средняя | Очень высокая | Низкая |
| **Масштабируемость** | Средняя | Средняя | Очень высокая | Низкая |
| **Отказоустойчивость** | Высокая (retries) | Низкая | Очень высокая | Низкая |
| **Наблюдаемость** | Хорошая | Хорошая | Отличная | Плохая |

### Рекомендация

Для типичного Symfony-приложения среднего и крупного размера **Symfony Messenger** — это лучший выбор.

**Почему?**
1.  **Единый интерфейс:** Вам не нужно менять код, если завтра вы решите перейти с Redis на RabbitMQ. Вы просто меняете DSN в `.env`.
2.  **Встроенные фичи:** Retries (повторные попытки при ошибке), Scheduling (отложенные сообщения), Dead Letter Queue (очередь для «отравленных» сообщений).
3.  **Тестируемость:** Вы можете протестировать сервис регистрации, мокав `MessageBusInterface`, и не думать о том, как тестировать почтовый сервер или поисковую машину.

**Дополнительный совет по производительности:**
Если письмо и индексация критически важны, но должны быть быстрыми, убедитесь, что:
1.  Трансформация (сериализация) сообщения занимает минимум времени.
2.  Воркер (`messenger:consume`) запущен с достаточным числом параллельных процессов (`-w 4`), чтобы обрабатывать нагрузку.
3.  Индексация и отправка почты выполняются атомарно в одном обработчике, чтобы не было состояния «письмо ушло, а индекс не обновился» (если это критично, лучше разбить на два сообщения с зависимостью).

**Архитектурная деталь:**
Не пишите в обработчике:
```php
$this->em->flush(); // Внутри обработчика лучше не делать flush, если возможно
```
Лучше передавайте ID сущности в сообщение, а в обработчике загружайте её по ID. Это обеспечивает изоляцию транзакций и избегает проблем с lazy-loading из другой сессии/транзакции.

> ⏱ 29.6s · prompt 19975 tok · output 2163 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony — это многослойная система, где каждый слой решает свою задачу. Чтобы правильно проектировать безопасность, нужно четко разделить понятия **Аутентификации** (кто ты?) и **Авторизации** (что ты можешь делать?).

Давайте разберем архитектуру от входного запроса до конкретной бизнес-логики.

### 1. Аутентификация (Authentication): «Кто ты?»

Это процесс проверки идентичности пользователя. В Symfony это работает через **Firewall** (Огнеупорную стену).

#### Как это устроено:
1.  **Запрос приходит** (например, на `/profile`).
2.  **Firewall** проверяет, подходит ли URL под правила безопасности (например, требует ли логина).
3.  Если логин требуется, но сессия пустая, **SecurityFactory** создает объект `User` (из БД по login/email) и проверяет пароль (через `PasswordHasher`).
4.  Если пароль верен, пользователь добавляется в **SecurityContext** (сессия/токен JWT).
5.  Теперь в текущем запросе у нас есть `User` с набором **ролей** (Roles).

> **Важно:** Роли — это метки, привязанные к пользоватю. Они не знают ничего о конкретных действиях, только о принадлежности к группе («ADMIN», "EDITOR", "USER").

---

### 2. Авторизация (Authorization): «Что ты можешь делать?»

Здесь начинается проверка прав. Symfony предлагает три уровня абстракции:

#### А. Глобальные атрибуты (`@Security`)
Используются для проверки ролей на уровне контроллера или метода.

```php
// config/routes.yaml или аннотация/атрибут в контроллере
#[Route('/admin', name: 'admin_dashboard')]
#[IsGranted('ROLE_ADMIN')] // Только администраторы
public function dashboard(): Response
{
    return $this->render('admin/dashboard.html.twig');
}
```

*   **Когда использовать:** Для простых проверок «Админ или не админ», «Пользователь залогинен или нет».
*   **Минус:** Не знает о данных. Она не может сказать, может ли *этот* админ управлять *конкретным* отделом.

#### Б. Роли и Владельцы (Ownership)
Роли часто недостаточны. Например, пользователь `ROLE_USER` может редактировать *свой* пост, но не чужой.

Здесь вступает **Security Token** и **Resource Owner**. Symfony умеет проверять владельца сущности:

```php
// В контроллере
public function edit(Post $post): Response
{
    // Проверяем, является ли текущий пользователь владельцем поста
    // Или имеет роль ROLE_ADMIN (которая может редактировать любой пост)
    $this->denyAccessUnlessGranted('EDIT', $post);
    
    // ... логика редактирования
}
```

*   `denyAccessUnlessGranted` принимает два аргумента: `privilege` (действие) и `subject` (объект).
*   Если вы не настроили Voter, Symfony пытается найти метаданные или владельца автоматически (в некоторых конфигурациях), но лучше явно определять логику через Voters.

#### В. Voters (Голосующие) — *Сердце гибкой авторизации*
**Voter** — это класс, который принимает решение «Разрешить / Отклонить» для конкретного действия над конкретным объектом.

### 3. Как устроен Voter?

Voter реализует интерфейс `VoterInterface`. Он получает:
1.  **Attribute:** Название действия (например, `EDIT`, `DELETE`, `VIEW`).
2.  **Subject:** Объект, над которым выполняется действие (например, `Post`, `User`, `Array`).
3.  **Token:** Текущий пользователь (`UserInterface`).

```php
namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    // 1. Определяем, над какими типами объектов мы голосуем
    protected function supports(string $attribute, $subject): bool
    {
        // Голосуем только для Post и только для атрибутов EDIT/DELETE/VIEW
        return in_array($attribute, ['EDIT', 'DELETE', 'VIEW']) 
               && $subject instanceof Post;
    }

    // 2. Логика проверки
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $post = $subject; // Это Post, так как supports() вернул true

        // 1. Администратор может все
        if ($user && $user->getRoles() === ['ROLE_ADMIN']) {
            return true;
        }

        // 2. Владелец поста может редактировать и удалять
        if ($attribute === 'EDIT' || $attribute === 'DELETE') {
            return $user === $post->getAuthor();
        }

        // 3. Любой залогиненный пользователь может смотреть опубликованный пост
        if ($attribute === 'VIEW') {
            return $post->isPublished();
        }

        return false;
    }
}
```

#### Почему это мощно?
*   **Изоляция логики:** Все правила о том, что можно делать с `Post`, находятся в `PostVoter`.
*   **Переиспользование:** Вы можете вызывать `$this->isGranted('EDIT', $post)` из любого места (контроллер, тви-шаблон, сервис), и логика будет одной и той же.
*   **Гибкость:** Легко добавить новые условия (например, «Только если пост создан сегодня»).

---

### 4. Когда использовать Роли, а когда Voter?

Это ключевой архитектурный вопрос.

| Критерий | **Роли (Role-based)** | **Voters (Attribute-based)** |
| :--- | :--- | :--- |
| **Вопрос** | «Кем ты являешься в системе?» | «Что ты можешь сделать с этим конкретным объектом?» |
| **Контекст** | Глобальный, статический. | Динамический, зависит от данных. |
| **Пример** | Админ видит меню «Настройки». | Пользователь видит кнопку «Удалить» только под *своими* комментариями. |
| **Сложность** | Низкая. Одна строка в конфиге или аннотации. | Средняя. Нужно писать класс Voter. |
| **Масштабируемость** | Плохо масштабируется (роль `MANAGER` должна знать про все отделы). | Отлично масштабируется (логика отделов изолирована в Voter). |
| **Производительность** | Быстро (проверка строки в массиве ролей). | Чуть медленнее (вызов метода `voteOnAttribute`), но оптимизируемо. |

#### Примеры выбора:

1.  **Используйте Роли, когда:**
    *   Доступ к разделу системы зависит только от статуса пользователя.
    *   Пример: `ROLE_ADMIN` может видеть раздел `/admin`. `ROLE_USER` — нет.
    *   Пример: `ROLE_EDITOR` может создавать статьи. `ROLE_USER` — нет.

2.  **Используйте Voter, когда:**
    *   Доступ зависит от **данных** объекта.
    *   Пример: Можно ли удалить комментарий? (Только если `comment->getAuthor() === $currentUser` ИЛИ `$currentUser` имеет роль `MODERATOR`).
    *   Пример: Можно ли читать документ? (Только если `document->getDepartment() === $user->getDepartment()`).
    *   Пример: Можно ли редактировать настройки профиля? (Только если профиль принадлежит текущему пользователю).

3.  **Используйте Сочетание:**
    *   Сначала проверяется роль (быстрая фильтрация).
    *   Если роль позволяет (например, `ROLE_ADMIN` всегда проходит), Voter может сразу вернуть `true`.
    *   Если роль недостаточна, Voter проверяет детали.

---

### 5. Где применять проверки?

#### А. В Контроллере (Наиболее часто)
```php
public function edit(Post $post): Response
{
    // Отклоняет запрос с 403 Forbidden, если нет права
    $this->denyAccessUnlessGranted('EDIT', $post);
    
    // ... логика
}
```

#### Б. В Twig-шаблонах (Управление UI)
```twig
{% if is_granted('EDIT', post) %}
    <a href="{{ path('edit_post', {id: post.id}) }}">Редактировать</a>
{% endif %}
```
*   Это улучшает UX: пользователь не увидит кнопку, на которую у него нет права (хотя защита на бэкенде все равно обязательна!).

#### В. В Доменном Слое (Глубокая интеграция)
Если вы используете DDD, проверки прав могут быть частью методов сущности:
```php
class Post
{
    public function edit(User $user, string $content): void
    {
        if (!$this->isAuthor($user)) {
            throw new AccessDeniedException('Only author can edit');
        }
        // ...
    }
}
```
*   **Нюанс:** Это смешивает бизнес-логику и безопасность. Часто лучше держать проверку прав в контроллере/Voter, а в сущности — бизнес-правила.

---

### 6. Best Practices

1.  **Fail Fast:** Проверяйте права как можно раньше. Не загружайте из БД 1000 записей, если пользователь не имеет права их видеть.
2.  **Разделяйте Роли и Привилегии:**
    *   Роли: `ADMIN`, `USER`.
    *   Привилегии (Атрибуты): `CREATE_POST`, `DELETE_ANY_COMMENT`.
    *   Маппинг: `ADMIN` => все привилегии. `USER` => `CREATE_POST` (свой).
3.  **Не дублируйте логику:** Если вы проверяете права в контроллере, не проверяйте их повторно в сервисе, если сервис вызывается только из того контроллера. Но если сервис используется в нескольких местах с разными контекстами — проверьте в каждом.
4.  **Используйте `isGranted` в шаблонах только для видимости элементов.** Никогда не полагайтесь только на скрытие элементов в UI как на защиту.
5.  **Логирование:** Настройте `security.log`, чтобы видеть, кто и где получил `AccessDenied`.

### Итог

*   **Роли** — для глобальных ограничений (кто ты).
*   **Voters** — для точечных, контекстно-зависимых ограничений (что ты можешь сделать с этим).
*   **Symfony** объединяет их: сначала проверяются роли (если они заданы как атрибут), затем вызывается Voter для детальной проверки объекта.

Такой подход обеспечивает безопасность (защита на бэкенде) и удобство (правильный UI), при этом позволяя легко масштабировать бизнес-правила без изменения архитектуры безопасности.

> ⏱ 35.5s · prompt 22180 tok · output 2579 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение Value Objects (VO) в Doctrine ORM — одна из самых частых и сложных задач в DDD-приложениях на PHP.

**Проблема:**
*   **Домен** требует: неизменяемость (`readonly`), инкапсуляцию, валидацию состояния, сравнение по значению (`equals()`), отсутствие ID.
*   **Doctrine** требует: изменяемый объект (Entity), связь через ID (FK), понимание того, как сериализовать объект в колонку БД.

Давайте разберем 4 основных подхода, от «грязных» к чистым.

---

### Подход 1: Приведение к базовому типу (String/Int) через `Custom Type`

Это самый распространенный способ. Мы храним VO как `String` или `Integer` в БД, а при загрузке конвертируем обратно.

#### Реализация:

1.  **Value Object:**
    ```php
    namespace App\Domain\Shared;

    final class Money
    {
        public function __construct(
            private int $amount,
            private string $currency
        ) {
            if ($amount < 0) throw new \InvalidArgumentException('Amount must be positive');
        }

        public function __toString(): string
        {
            return "{$this->amount}-{$this->currency}"; // "1000-EUR"
        }

        public static function fromString(string $str): self
        {
            [$amount, $currency] = explode('-', $str);
            return new self((int)$amount, $currency);
        }
    }
    ```

2.  **Custom Type:**
    ```php
    namespace App\Infrastructure\Doctrine;

    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use Doctrine\DBAL\Types\ConversionException;
    use Doctrine\DBAL\Types\Type;
    use App\Domain\Shared\Money;

    class MoneyType extends Type
    {
        public const NAME = 'money';

        public function getSQLDeclaration(int $length, AbstractPlatform $platform): string
        {
            return $platform->getStringTypeDeclarationSQL([]);
        }

        public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
        {
            if (null === $value) return null;
            return Money::fromString($value);
        }

        public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
        {
            if (null === $value) return null;
            return $value->__toString();
        }

        public function getName(): string
        {
            return self::NAME;
        }
    }
    ```

3.  **Entity:**
    ```php
    #[ORM\Column(type: MoneyType::NAME)]
    private Money $price;
    ```

#### Плюсы:
*   **Простота:** Минимум кода.
*   **Производительность:** Не создает лишних запросов к БД (JOIN'ов нет).
*   **Сложный поиск:** Можно использовать DQL, если тип поддерживает сравнение (хотя часто это больно).

#### Минусы:
*   **Формат хранения зависит от реализации:** Если вы измените `__toString()`, старые данные в БД могут «сломаться» или стать нечитаемыми.
*   **Сложная валидация:** Валидация происходит только в конструкторе VO. Если строка в БД повреждена, ошибка всплывет только при загрузке в память.
*   **Негибкость:** Если VO имеет сложную структуру (например, `Address` с улицей, городом, индексом), сериализация в одну строку становится хрупкой (`"123 Street, City, 12345"`). Разделение запятыми — плохая идея.

---

### Подход 2: Embedded Entities (Встроенные сущности)

Это **золотой стандарт** для сложных VO (адреса, банковские реквизиты).
Мы создаем отдельную сущность (или класс), который маппится на несколько колонок, но не имеет своего первичного ключа.

#### Реализация:

1.  **Value Object (Domain):**
    ```php
    final class Address
    {
        public function __construct(
            public readonly string $street,
            public readonly string $city,
            public readonly string $zipCode
        ) {}
    }
    ```

2.  **Embeddable (Infrastructure):**
    ```php
    namespace App\Infrastructure\Persistence;

    use Doctrine\ORM\Mapping as ORM;
    use App\Domain\Address; // Или копируем структуру

    /**
     * @ORM\Embeddable
     */
    class AddressEmbedded
    {
        #[ORM\Column(type: 'string')]
        private string $street;

        #[ORM\Column(type: 'string')]
        private string $city;

        #[ORM\Column(type: 'string')]
        private string $zipCode;

        // Getters/Setters для работы с Doctrine
        public function getStreet(): string { return $this->street; }
        // ... и т.д.
    }
    ```

3.  **Entity:**
    ```php
    class User
    {
        #[ORM\Embedded(class: AddressEmbedded::class)]
        private AddressEmbedded $address;
    }
    ```

#### Плюсы:
*   **Читаемость схемы БД:** Отдельные колонки `user.address_street`, `user.address_city`.
*   **Гибкость:** Можно фильтровать по части данных (`WHERE address_city = 'Moscow'`).
*   **Нет JOIN'ов:** Данные хранятся в той же таблице.

#### Минусы:
*   **Дублирование кода:** Вам нужно поддерживать синхронизацию между Domain VO (`App\Domain\Address`) и Infra Embeddable (`AddressEmbedded`).
*   **Инкапсуляция нарушена:** Чтобы изменить VO, вы должны менять Embeddable, а затем обновлять Entity.
*   **Сложность валидации:** Валидация VO (конструктор) не срабатывает автоматически при маппинге Embeddable. Вам нужно писать кастомные валидаторы или конвертировать Embeddable -> VO вручную.

**Как связать Domain VO и Embeddable?**
Обычно делают адаптер в Entity:
```php
class User
{
    #[ORM\Embedded]
    private AddressEmbedded $addressEmbedded;

    public function getAddress(): Address
    {
        // Конвертация Infra -> Domain
        return new Address(
            $this->addressEmbedded->getStreet(),
            $this->addressEmbedded->getCity(),
            $this->addressEmbedded->getZipCode()
        );
    }

    public function setAddress(Address $address): void
    {
        // Конвертация Domain -> Infra
        $this->addressEmbedded->setStreet($address->street);
        // ...
    }
}
```

---

### Подход 3: JSONB (PostgreSQL) / JSON Column

Если у вас PostgreSQL (или MariaDB), это часто **лучший выбор** для VO.

#### Реализация:

1.  **Value Object:**
    ```php
    final class Money
    {
        // ... логика ...
        
        public function toArray(): array
        {
            return ['amount' => $this->amount, 'currency' => $this->currency];
        }

        public static function fromArray(array $data): self
        {
            return new self($data['amount'], $data['currency']);
        }
    }
    ```

2.  **Entity:**
    ```php
    #[ORM\Column(type: 'json')] // Для PostgreSQL это будет jsonb
    private array $priceData; // Храним массив, который сериализуется в JSON
    ```
    *Или лучше, используя кастомный тип `json` от Doctrine:*
    ```php
    #[ORM\Column(type: 'json')]
    private Money $price; // Если Doctrine умеет сериализовать объект в JSON (нужен serializer или кастомный тип)
    ```

    *Стандартный Doctrine `json` тип хранит ассоциативный массив (`array`). Поэтому чаще делают так:*
    
    ```php
    class User
    {
        #[ORM\Column(type: 'json')]
        private array $price = [];

        public function getPrice(): Money
        {
            return Money::fromArray($this->price);
        }

        public function setPrice(Money $money): void
        {
            $this->price = $money->toArray();
        }
    }
    ```

#### Плюсы:
*   **Гибкость:** Можно хранить любые сложные структуры без изменения схемы БД.
*   **Изменяемость:** Легко добавить новое поле в VO (просто добавить ключ в JSON).
*   **Производительность чтения:** Быстрая загрузка всего объекта.

#### Минусы:
*   **Сложность индексов и запросов:** В PostgreSQL можно строить GIN-индексы для JSONB, но сложные запросы к вложенным ключам сложнее, чем к обычным колонкам.
*   **Валидация:** В БД нет ограничений (UNIQUE, NOT NULL) на внутренние поля JSON. Нужно полагаться на приложение.
*   **Версии MySQL/MariaDB:** Поддержка JSONB и полнотекстового поиска по JSON хуже, чем в PostgreSQL.

---

### Подход 4: Composition (Связь One-To-One)

Иногда VO настолько сложный, что его выделяют в отдельную Entity с внешним ключом.

*   **User** (1) -> **UserProfile** (1)
*   `UserProfile` содержит `address`, `phone` и т.д.

#### Плюсы:
*   Строгая нормализация.
*   Легко менять поля без изменения основной таблицы `users`.

#### Минусы:
*   **Нарушение инкапсуляции:** VO перестает быть «значением», он становится «сущностью» со своей идентичностью (ID), даже если бизнес-логически он — значение.
*   **Сложность транзакций:** Нужно синхронно сохранять/удалять обе сущности.
*   **Лишние JOIN'ы.**

---

### Сравнение и Рекомендации

| Критерий | Custom Type (String) | Embedded | JSONB |
| :--- | :---: | :---: | :---: |
| **Сложность VO** | Простые (1-2 поля) | Средние (3-5 полей) | Сложные (глубокие вложения) |
| **Производительность** | Высокая | Высокая | Средняя (зависит от БД) |
| **Гибкость изменения схемы** | Низкая | Средняя | Высокая |
| **Поиск/Фильтрация** | Средняя | Высокая | Низкая/Средняя |
| **Код (Clean Architecture)** | Средний | Высокий (адаптеры) | Средний |

### Что выбрать?

1.  **Для простых скалярных VO** (Money, Email, Phone, ISBN):
    *   Используйте **Custom Type** (если формат прост и стабилен).
    *   Или просто **String** в Entity, если вам не нужна инкапсуляция в домене (антипаттерн, но часто).
    *   *Лайфхак:* Для Email лучше использовать встроенный тип `email` в Doctrine.

2.  **Для структурных VO** (Address, BankAccount):
    *   **Embedded Entities** — если нужна нормализация и фильтрация по частям.
    *   **JSONB** — если структура часто меняется или очень сложная, и вы используете PostgreSQL.

3.  **Для неизменяемости:**
    *   Вне зависимости от способа маппинга, **Value Object в домене должен быть `readonly`** (в PHP 8.1+) или иметь только конструктор и без сеттеров.
    *   Entity должна иметь сеттер, который принимает VO и сохраняет его состояние.
    *   При загрузке из БД (через `__construct` или magic method) вы восстанавливаете VO.

### Подводные камни

1.  **Lazy Loading VO:** Если VO является частью Entity, он загружается вместе с ней. Это хорошо. Но если VO зависит от другой сущности (например, `Address` знает `Country`), и `Country` — это Entity, вы получите N+1 проблему или ошибку Lazy Loading. *Решение:* Упрощать VO до примитивов или использовать `EAGER` fetch (осторожно с производительностью).
2.  **Equality:** VO сравниваются по значению (`equals`). Если вы храните их в Set'ах или как ключи массивов, убедитесь, что `hashCode`/`equals` работают корректно.
3.  **Serialization:** Если вы используете API (Symfony Serializer), убедитесь, что VO сериализуется в JSON корректно (через `toArray()`).

### Итоговая рекомендация

Для большинства PHP/Symfony проектов с PostgreSQL:
*   **Money/Email/Date:** Custom Types или встроенные типы.
*   **Address:** Embedded (если важна фильтрация по городу) или JSONB (если адрес — это просто кусок текста).
*   **Сложные конфигурации:** JSONB.

Всегда держите **инкапсуляцию в домене**: Entity — это «мост» между БД и Доменом, но бизнес-логика должна работать с VO, а не с массивами или строками.

> ⏱ 42.6s · prompt 24809 tok · output 3074 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Это классическая и самая коварная часть DDD-реализации в PHP. Главная ошибка, которую совершают 90% разработчиков, — попытка сделать из каждой дочерней сущности самостоятельную сущность с собственным жизненным циклом, забывая, что **Агрегат** — это граница инкапсуляции.

Если вы нарушаете инкапсуляцию агрегата (даете прямой доступ к коллекции извне или позволяете внешнему коду менять состояние дочерних элементов без участия Корня), вы получаете «раздутое» состояние и ошибки конкурентности.

Давайте разберем на примере агрегата **`Order` (Заказ)**.

### 1. Структура Агрегата

*   **Корень (Aggregate Root):** `Order`. Единственный объект, который имеет ID и который вы можете передавать за границу агрегата.
*   **Дочерние сущности (Child Entities):** `OrderLine` (Позиция заказа). Они существуют только внутри `Order`. У них есть ID (внутренний или внешний, зависит от требования), но они не должны быть доступны для манипуляций снаружи, пока не будут связаны с Заказом.

#### Domain Model (Чистый PHP, без Doctrine)

```php
namespace App\Domain\Order;

final class Order
{
    private OrderId $id;
    private array $lines = []; // Коллекция дочерних сущностей
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;

    // Приватный конструктор: создание только через фабричный метод или репозиторий
    private function __construct(
        OrderId $id,
        OrderStatus $status,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    // === Бизнес-методы (инкапсуляция логики) ===

    public function addLine(Product $product, int $quantity): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new OrderStateException('Cannot modify completed order');
        }

        // Ищем, есть ли уже такой товар
        foreach ($this->lines as $line) {
            if ($line->isSameProduct($product)) {
                $line->increaseQuantity($quantity);
                return;
            }
        }

        // Создаем новую позицию
        $newLine = OrderLine::create($product, $quantity);
        $this->lines[] = $newLine;
    }

    public function complete(): void
    {
        $this->status = OrderStatus::Completed;
    }

    // === Accessors (для внедрения/сериализации) ===
    public function getId(): OrderId { return $this->id; }
    public function getStatus(): OrderStatus { return $this->status; }
    
    /**
     * Возвращаем коллекцию как неизменяемый вид (array или Collection)
     * Если нужно менять, вызываем бизнес-методы.
     */
    public function getLines(): array
    {
        return $this->lines;
    }
    
    // Метод для внутреннего использования Doctrine (Hydration)
    public static function fromArray(array $data): self
    {
        $instance = new self(
            OrderId::fromInt($data['id']),
            OrderStatus::fromString($data['status']),
            new \DateTimeImmutable($data['created_at'])
        );
        // Восстанавливаем строки (в реальном коде через маппинг)
        return $instance;
    }
}

final class OrderLine
{
    private int $id; // Если есть ID в БД
    private Product $product;
    private int $quantity;

    private function __construct(int $id, Product $product, int $quantity)
    {
        $this->id = $id;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public static function create(Product $product, int $quantity): self
    {
        if ($quantity <= 0) throw new \InvalidArgumentException('Qty must be > 0');
        // ID генерируется при сохранении (Transient)
        return new self(0, $product, $quantity); 
    }

    public function increaseQuantity(int $amount): void
    {
        $this->quantity += $amount;
    }

    public function isSameProduct(Product $p): bool
    {
        return $this->product->equals($p);
    }

    // Getters для маппинга...
    public function getProductId(): int { return $this->product->getId(); }
    public function getQuantity(): int { return $this->quantity; }
}
```

---

### 2. Маппинг в Doctrine

Ключевой момент: **Корень агрегата — это Entity**. **Дочерние сущности** могут быть `Embedded` (если у них нет собственного ID/цикла жизни) или `OneToMany` (если у них есть ID и они могут существовать отдельно после удаления корня, что редкость для строк заказа).

В примере с заказом `OrderLine` — это типичный `OneToMany` с каскадным удалением.

#### Entity (Infrastructure Layer)

```php
namespace App\Infrastructure\Persistence;

use App\Domain\Order\Order as DomainOrder;
use App\Domain\Order\OrderLine as DomainOrderLine;
use App\Domain\Order\OrderId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    // === КЛЮЧЕВОЙ МОМЕНТ ===
    // Коллекция дочерних сущностей
    #[ORM\OneToMany(
        targetEntity: OrderLineEntity::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'], // Каскадное сохранение и удаление
        orphanRemoval: true             // Удаление сирот (строк, отключенных от заказа)
    )]
    private \Doctrine\Common\Collections\Collection $lines;

    // Конструктор для Hydration (загрузки из БД)
    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // === Адаптер: Entity -> Domain ===
    public function toDomain(): DomainOrder
    {
        $domainLines = array_map(
            fn($lineEntity) => $lineEntity->toDomain(), 
            $this->lines->toArray()
        );
        
        // Упрощенно: в реальном коде передаем коллекцию в домен через фабрику
        return DomainOrder::fromEntityData(
            id: $this->id,
            status: $this->status,
            createdAt: $this->createdAt,
            lines: $domainLines
        );
    }

    // === Адаптер: Domain -> Entity (для сохранения) ===
    public static function fromDomain(DomainOrder $domainOrder): self
    {
        $entity = new self();
        $entity->id = $domainOrder->getId()->getValue();
        $entity->status = $domainOrder->getStatus()->value;
        $entity->createdAt = $domainOrder->getCreatedAt();

        // Синхронизируем коллекцию строк
        foreach ($domainOrder->getLines() as $domainLine) {
            $entityLine = OrderLineEntity::fromDomain($domainLine);
            $entityLine->setOrder($entity); // Устанавливаем обратную ссылку
            $entity->addLine($entityLine);
        }

        return $entity;
    }

    // Методы для работы с коллекцией Doctrine
    public function addLine(OrderLineEntity $line): void
    {
        $this->lines->add($line);
    }

    // Getters для Doctrine
    public function getId(): int { return $this->id; }
    // ...
}
```

> **Примечание про `orphanRemoval`:** Это критически важно для агрегатов. Если вы удалили строку из доменной коллекции `Order` (через `$order->removeLine()`), и затем сохраните `Order`, Doctrine увидит, что сущность `OrderLine` больше не ссылается на `Order` (стала "сиротой") и удалит ее из БД. Без этого строки могли бы остаться висящими.

---

### 3. Границы транзакций и Flush

**Правило:** Один Агрегат = Одна Транзакция.

Вы **не должны** сохранять `OrderLine` отдельно от `Order`. Вы сохраняете Корень (`Order`), а Doctrine благодаря `cascade: persist/remove` автоматически сохраняет/удаляет связанные строки.

#### Кто вызывает `flush()`?

Ответ зависит от архитектурного подхода, но в чистом DDD это делает **Прикладной Сервис (Application Service)**.

1.  **Доменный слой (Domain):** Не знает о БД. Методы `addLine()`, `complete()` просто изменяют состояние объекта в памяти. Они **никогда** не вызывают `flush()`.
2.  **Инфраструктурный слой (Repository):** Реализует интерфейс. Метод `save()` вызывает `$em->persist()` (если новый) или `$em->flush()`? Нет, лучше оставить `flush()` на уровне сервиса.
3.  **Прикладной слой (Application Service):** Оркестратор. Он знает, что нужно загрузить, изменить и сохранить.

#### Пример Прикладного Сервиса

```php
namespace App\Application;

use App\Domain\Order\OrderId;
use App\Domain\Product\ProductRepositoryInterface; // Доменный интерфейс
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo, // Доменный репозиторий
        private ProductRepositoryInterface $productRepo,
        private EntityManagerInterface $em // Инфраструктурная зависимость
    ) {}

    public function addLineToOrder(int $orderId, int $productId, int $qty): void
    {
        // 1. Загрузка Агрегата (Корня)
        $order = $this->orderRepo->find(OrderId::fromInt($orderId));
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        $product = $this->productRepo->find($productId);
        if (!$product) {
            throw new ProductNotFoundException($productId);
        }

        // 2. Изменение состояния (Бизнес-логика внутри Домена)
        $order->addLine($product, $qty);

        // 3. Сохранение (Инфраструктурная операция)
        // Репозиторий знает, как сохранить Корень, каскадно сохраняя строки
        $this->orderRepo->save($order); 
    }
}
```

#### Реализация Репозитория (Infrastructure)

```php
namespace App\Infrastructure\Persistence;

use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Order\Order as DomainOrder;
use App\Domain\Order\OrderId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function find(OrderId $id): ?DomainOrder
    {
        $entity = $this->em->find(OrderEntity::class, $id->getValue());
        if (!$entity) return null;
        
        // Гидратация: Entity -> Domain
        return $entity->toDomain();
    }

    public function save(DomainOrder $domainOrder): void
    {
        // 1. Преобразование Domain -> Entity
        $entity = OrderEntity::fromDomain($domainOrder);

        // 2. Если сущность новая (Transient), персистим
        if ($this->em->getUnitOfWork()->isEntityManagerRegistered($entity)) {
            $this->em->persist($entity);
        }

        // 3. FLUSH!
        // Здесь происходит запись в БД.
        // Если возникает ошибка (UNIQUE constraint, DB down), 
        // транзакция откатывается, и состояние Домена не испорчено 
        // (до тех пор, пока не изменится, но объект в памяти изменен).
        // Важно: flush() должен быть вызван один раз на операцию.
        $this->em->flush();
    }
}
```

### Почему так? (Trade-offs)

1.  **Консистентность:** Если `addLine()` изменяет состояние `Order` в памяти, а `flush()` падает с ошибкой, у нас в памяти "грязный" объект, а в БД — старый. Это ожидаемое поведение. Прикладной сервис может поймать исключение и решить, что делать (откатить транзакцию, отправить алерт).
2.  **Атомарность:** Благодаря `cascade` и одному `flush()`, изменения в `Order` и `OrderLine` применяются к БД атомарно. Либо все сохранилось, либо ничего.
3.  **Инкапсуляция:** Доменный слой `Order` не знает про `EntityManager`. Он знает только, что его состояние изменилось. Инфраструктурный слой знает, как материализовать это изменение.

### Подводные камни

1.  **Detached Entities:**
    *   Если вы загрузили `Order` в одном запросе, а в следующем (через `find()`) получили новый экземпляр, изменения в первом не отразятся во втором.
    *   *Решение:* Всегда получайте актуальный экземпляр из `$em` (Identity Map) или используйте `merge()` (осторожно!). Лучше всего: один запрос = один агрегат в памяти.

2.  **N+1 проблема при загрузке строк:**
    *   Если вы используете ленивую загрузку (`lazy: true`) для коллекции `lines`, то при первом доступе к `$order->getLines()` в доменном слое Doctrine выполнит доп. SQL-запрос.
    *   *Решение:* Если вы знаете, что вам всегда нужны строки, используйте `EAGER` (осторожно с памятью) или загрузите их явно через DQL при чтении в репозитории.

3.  **Сериализация DOM-объектов в мессенджере:**
    *   Если вы отправляете событие `OrderCompleted` в очередь (RabbitMQ/Redis), **не сериализуйте весь агрегат** (все строки, продукты, цены).
    *   Сериализуйте только **факт** (ID заказа, сумма, дата) и, если нужно, **снимок** данных, необходимых для потребителя. Агрегат — это живая структура, а событие — это исторический факт.

### Итог

1.  **Маппинг:** Корень — `Entity` с `OneToMany` к дочерним. `cascade: persist, remove`. `orphanRemoval: true`.
2.  **Границы:** Агрегат — это граница транзакции. Не сохраняйте части агрегата отдельно.
3.  **Flush:** Вызывается один раз в Прикладном Сервисе (или Репозитории) после изменения состояния агрегата.
4.  **Инкапсуляция:** Доменные методы изменяют состояние коллекции внутри Корня. Внешний код не должен напрямую манипулировать коллекцией дочерних сущностей (если только не через методы Корня).

> ⏱ 50.1s · prompt 27946 tok · output 3566 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика и устранение проблемы N+1 — это стандартная процедура оптимизации PHP-приложений. Это не только вопрос скорости, но и нагрузки на базу данных.

### Часть 1: Диагностика

Как я понимаю, что проблема именно в N+1?

#### 1. Логирование SQL-запросов (SQL Logging)
Самый надежный способ — увидеть сами запросы.

*   **Включить логирование:** В `config/services.yaml` или через Symfony Profiler.
*   **Что искать:**
    *   Один запрос `SELECT ... FROM parent_table`.
    *   За ним следует **много** запросов `SELECT ... FROM child_table WHERE parent_id = ?`.
    *   Количество дочерних запросов равно количеству родительских записей.

```text
1. SELECT * FROM orders LIMIT 10 OFFSET 0;
2. SELECT * FROM order_lines WHERE order_id = 1;
3. SELECT * FROM order_lines WHERE order_id = 2;
4. SELECT * FROM order_lines WHERE order_id = 3;
...
11. SELECT * FROM order_lines WHERE order_id = 10;
```
*Вместо 10 дочерних запросов, должно быть 1: `SELECT * FROM order_lines WHERE order_id IN (1, 2, ..., 10);`*

#### 2. Symfony Profiler
*   Перейдите в раздел **Database**.
*   Посмотрите на количество запросов (`Query Count`) и общее время выполнения.
*   Отсортируйте запросы по времени. Если видно много одинаковых по структуре запросов с разными параметрами — это N+1.

#### 3. Инструменты мониторинга
*   Если у вас включен APM (New Relic, Datadog, Sentry), ищите паттерны `high frequency of similar SQL queries`.

---

### Часть 2: Способы решения и их Trade-offs

#### Способ 1: `JOIN FETCH` в DQL / Doctrine Query

Это «родной» способ Doctrine. Вы явно указываете, какие связанные таблицы нужно загрузить вместе с главной.

```php
// Было (N+1):
$qb->select('o')
   ->from(Order::class, 'o');

// Стало (1 запрос):
$qb->select('o', 'l')
   ->from(Order::class, 'o')
   ->leftJoin('o.lines', 'l')
   ->groupBy('o.id') // Если нужно
   ->addGroupBy('l.id'); 
// Или проще через DQL string:
// SELECT o, l FROM App:Order o LEFT JOIN o.lines l
```

**Trade-offs:**
*   ✅ **Простота:** Минимум кода.
*   ✅ **Один запрос:** SQL-двигатель делает `JOIN`, что быстрее N отдельных `SELECT`.
*   ❌ **Память:** Все связанные данные загружаются в память сразу. Если у 100 заказов по 50 позиций, вы загружаете 5000 строк в PHP-память, даже если вам нужны только первые 10 позиций.
*   ❌ **Сложность DQL:** При глубоких вложенностях (`order -> lines -> product -> category`) DQL становится трудночитаемым и хрупким.
*   ❌ **Дублирование строк:** Если `JOIN` создает дубликаты родительских строк (из-за нескольких дочерних записей), нужно аккуратно использовать `DISTINCT` или `GROUP BY`, что может усложнить сортировку и пагинацию.

---

#### Способ 2: `partial` Fetch (Частичная загрузка)

Вы загружаете сущность, но **не всю**, а только нужные поля.

```php
// Загружаем только ID и Название товара, но НЕ все его характеристики
$qb->select('NEW App\Dto\SimpleProduct(p.id, p.name)')
   ->from(Product::class, 'p');
```
*Примечание: `partial` в DQL работает чуть иначе, обычно используют конструкторные выражения (Constructor Initialization) или `fetchJoin` с проекцией.*

Более частый случай: вы знаете, что вам нужны только определенные поля дочерних сущностей.

```php
// DQL
SELECT l.id, l.quantity 
FROM App:OrderLine l 
WHERE l.orderId = 123
```
Но это все равно N+1, если вы итерируете заказы.

**Лучше комбинировать:**
1.  Загрузить заказы.
2.  Загрузить все нужные позиции одним запросом через `IN (...)`.
3.  Присвоить их в PHP-массиве.

```php
$orders = $this->orderRepo->findAll();
$ids = array_map(fn($o) => $o->getId(), $orders);
$allLines = $this->lineRepo->findByOrderIdIn($ids); // 1 запрос
$linesByOrderId = array_reduce($allLines, fn($map, $line) => $map[$line->getOrderId()][] = $line, []);

foreach ($orders as $order) {
    $order->setLines($linesByOrderId[$order->getId()] ?? []);
}
```

**Trade-offs:**
*   ✅ **Контроль данных:** Вы точно знаете, какие поля грузите.
*   ✅ **Производительность:** Часто быстрее, чем `JOIN`, если данных много, а нужно мало.
*   ❌ **Ручная работа:** Нужно писать код связывания в PHP.
*   ❌ **Сложность:** Если структура динамическая, этот подход менее масштабируем.

---

#### Способ 3: `Eager` Loading (Автоматическое жадное чтение)

Настройка маппинга: `#[ORM\OneToMany(..., fetch: 'EAGER')]`.

**Trade-offs:**
*   ✅ **Легкость:** Не нужно ничего писать в DQL.
*   ❌ **Непредсказуемость:** Вы не знаете, когда именно Doctrine сделает `JOIN`. Это может замедлить простые запросы, где вам не нужны дочерние сущности.
*   ❌ **Сложность отладки:** Невидимая зависимость.
*   ❌ **Проблема с фильтрацией:** Если вы фильтруете по дочерней сущности, `Eager` может не сработать как ожидается или создать лишние `JOIN`.

> **Рекомендация:** `Eager` loading — это антипаттерн в большинстве случаев. Используйте только, если **99% запросов** к этой сущности всегда требуют дочерние данные.

---

#### Способ 4: Денормализация (Denormalization)

Если N+1 возникает часто, возможно, вам не нужна связь.

*   *Пример:* У товара есть `category_name`. Вместо того, чтобы `JOIN` с таблицей `categories`, храните `category_name` в таблице `products`.
*   *Пример:* В `orders` храните `total_amount`. Считайте его в PHP при создании, а не через `SUM()` в SQL с `JOIN` по `order_lines`.

**Trade-offs:**
*   ✅ **Максимальная скорость:** Один запрос к одной таблице.
*   ✅ **Простота:** Нет сложной логики загрузки.
*   ❌ **Дублирование данных:** Если название категории меняется, нужно обновлять все товары.
*   ❌ **Сложность миграций:** Изменение схемы сложнее.

---

#### Способ 5: Кэширование (Result Cache / Query Cache)

*   **Query Cache:** Кэширует результаты запросов в Redis/Memcached.
*   **Second-Level Cache (SLC):** В Doctrine 2.x есть экспериментальный SLC.

**Trade-offs:**
*   ✅ **Скорость после первого запроса:** Если данные не меняются, они берутся из кэша.
*   ❌ **Сложность инвалидации:** Как понять, что данные устарели? Нужно писать логику очистки кэша при каждом изменении данных.
*   ❌ **Память:** Кэш занимает место.
*   ❌ **Не решит проблему всегда:** При высокой частоте изменений или уникальных запросах (пагинация) кэш может не помочь.

---

### Итоговая стратегия (Как я решаю)

1.  **Диагностика:** Проверяю Profiler. Убеждаюсь, что это именно N+1, а не медленный `JOIN` или неоптимизированный SQL.
2.  **Быстрое решение:** Если запрос простой — добавляю `JOIN FETCH` в DQL.
3.  **Если данных много:** Переписываю запрос через **Constructor Initialization** (загрузка только нужных полей) или делаю **2 шага**:
    1.  `SELECT id FROM parents`
    2.  `SELECT * FROM children WHERE parent_id IN (ids)`
4.  **Если это критичный горячий путь:** Рассматриваю **Денормализацию** или **Кэширование**.
5.  **Если структура сложная:** Перевожу чтение на **Data Access Object (DAO)** с ручным управлением запросами, избегая ORM-магии для этого конкретного случая.

### Главный совет

**Не бойтесь вытаскивать сложные чтения из ORM.**
ORM (Doctrine) — это excellent инструмент для **записи** и **простых чтений**. Для сложных аналитических или списочных отчётов часто эффективнее написать чистый SQL или DQL с проекцией в DTO, чем пытаться уговорить ORM сделать «волшебный» `JOIN` с пагинацией и фильтрацией.

> ⏱ 31.5s · prompt 31564 tok · output 2180 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это классический вопрос о границе ответственности между приложением и базой данных.

**Короткий ответ:** Ограничения в БД — это **последний рубеж обороны**. Они не заменяют валидацию в коде, но гарантируют целостность данных даже если код «сломался», если данные пришли из другого источника (скрипт, миграция, внешний API) или если в коде есть гонка (race condition).

Давайте разберем, почему это необходимо и какие инструменты PostgreSQL мы используем.

### 1. Почему валидации в коде недостаточно?

1.  **Race Conditions (Гонки):**
    *   *Сценарий:* Два пользователя одновременно пытаются забронировать последний билет на рейс.
    *   *Код:* Оба читывают `count = 1`, оба считают, что место свободно, оба пишут `count = 0`.
    *   *Результат:* Продано 2 билета на 1 место.
    *   *Решение:* Уникальные индексы или `FOR UPDATE` в транзакции. Но проще всего — уникальность бизнес-ключа в БД.

2.  **Обход приложения:**
    *   Данные могут быть изменены напрямую через SQL-консоль, через миграции, через инструменты ETL или из другого микросервиса. В этих случаях ваш PHP-код с проверками не участвует.

3.  **Производительность (Fail Fast):**
    *   Если нарушено ограничение на уровне БД (например, `NOT NULL`), ошибка возникает мгновенно на уровне драйвера, без необходимости прогонять данные через весь стек ORM, сериализации и бизнес-логики.

4.  **Гарантия ACID:**
    *   БД — это единственная система, которая может атомарно гарантировать, что состояние данных валидно на момент коммита транзакции.

---

### 2. Какие средства целостности используем в PostgreSQL?

#### А. Первичные и Внешние ключи (PK/FK)
*   **Что делают:** Гарантируют, что у каждого объекта есть уникальный ID, и что связи между таблицами валидны (нет «сирот»).
*   **Когда использовать:** Всегда. Это фундамент реляционной модели.
*   **Пример:** `order_id` в таблице `order_lines` должен существовать в таблице `orders`.

#### Б. Уникальные индексы (Unique Constraints)
*   **Что делают:** Гарантируют уникальность значений (email, username, документ-номер).
*   **Когда использовать:** Для бизнес-ключей.
*   **Пример:**
    ```sql
    ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);
    ```
    *Ловушка:* Если два запроса приходят одновременно, второй упадет с `UniqueConstraintViolationException`. Это нормально — мы ловим его и показываем пользователю «Email занят».

#### В. CHECK Constraints
*   **Что делают:** Проверяют условие для конкретного ряда.
*   **Когда использовать:** Для простых, статических правил, которые сложно изменить в коде или которые критичны для структуры данных.
*   **Примеры:**
    ```sql
    -- Значение должно быть положительным
    ALTER TABLE products ADD CONSTRAINT check_price_positive CHECK (price > 0);

    -- Статус должен быть из разрешенного списка
    -- (Вместо ENUM, который в PG ограничен)
    ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('new', 'paid', 'shipped', 'delivered'));
    ```
    *Примечание:* `CHECK` может быть «тяжелым», если условие сложное. Для простых условий — отлично.

#### Г. Partial Indexes (Частичные индексы)
*   **Что делают:** Индексируют только часть строк, удовлетворяющих условию.
*   **Когда использовать:** Когда уникальность нужна только для части записей.
*   **Пример:** Уникальный email только для активных пользователей.
    ```sql
    CREATE UNIQUE INDEX unique_active_email 
    ON users (email) 
    WHERE is_deleted = FALSE;
    ```
    Это позволяет иметь несколько удаленных пользователей с одним email, но только один активный.

#### Д. Trigger Functions (Триггеры)
*   **Что делают:** Выполняют код на стороне БД перед/после INSERT/UPDATE/DELETE.
*   **Когда использовать:** Для сложных бизнес-правил, которые должны выполняться **строго атомарно** с записью данных.
*   **Пример:** Автоматическое обновление `updated_at` или пересчет суммы заказа.
    ```sql
    CREATE FUNCTION update_updated_at_column()
    RETURNS TRIGGER AS $$
    BEGIN
        NEW.updated_at = NOW();
        RETURN NEW;
    END;
    $$ language 'plpgsql';

    CREATE TRIGGER set_updated_at
    BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();
    ```

    *Осторожно:* Триггеры — это «магия». Их сложно тестировать и отлаживать. Используйте их только там, где логика не должна зависеть от приложения (например, аудит, счетчики).

#### Е. Views with CHECK OPTION
*   **Что делают:** Позволяют ограничивать данные, видимые через представление, и проверять их при записи.
*   **Когда использовать:** Для ролевой безопасности или упрощения сложных условий.

---

### 3. Сравнительная таблица: Приложение vs База

| Характеристика | Валидация в Приложении (PHP/Symfony) | Ограничения в БД (PostgreSQL) |
| :--- | :--- | :--- |
| **Гибкость** | Высокая (можно менять логику без миграций) | Низкая (нужна миграция, деплой) |
| **Производительность** | Низкая (передача данных на сервер, обработка в PHP) | Высокая (проверка на уровне ядра БД) |
| **Сообщение об ошибке** | Гибкое, локальное, понятное пользователю | Сырое, техническое (`SQLSTATE[23505]: Unique violation`) |
| **Тестируемость** | Высокая (Unit-тесты) | Средняя (Integration-тесты, сложнее моки) |
| **Защита от конкурентности** | Средняя (нужны транзакции, блокировки) | Высокая (атомарные операции на уровне индексов) |
| **Интеграция с ORM** | Прямая | Требует маппинга исключений |

---

### 4. Практические рекомендации: Что куда ставить?

#### ✅ Ставьте в БД:
1.  **Структурные ограничения:** `NOT NULL`, `PRIMARY KEY`, `FOREIGN KEY`.
2.  **Уникальность бизнес-ключей:** `UNIQUE (email)`, `UNIQUE (tenant_id, user_id)`.
3.  **Диапазонные ограничения:** `CHECK (amount >= 0)`, `CHECK (start_date < end_date)`.
4.  **Списки допустимых значений:** `CHECK (status IN (...))` (если список мал и стабилен).
5.  **Автоматическое обновление системных полей:** `updated_at` через триггер (спорно, но часто полезно).

#### ✅ Ставьте в Приложении:
1.  **Сложная бизнес-логика:** «Пользователь может создать заказ только если его баланс > 0 И у него нет заблокированных товаров в корзине».
2.  **Валидация формата:** Email, телефон, URL (регулярные выражения).
3.  **Длинные сообщения об ошибках:** «Пожалуйста, выберите хотя бы один товар».
4.  **Взаимодействие с другими сервисами:** «Проверить наличие на складе через API» (БД этого не знает).

---

### 5. Подводные камни

1.  **Переусложнение БД:**
    *   Если вы перенесете всю бизнес-логику в триггеры, ваша БД превратится в «спящую» бизнес-систему, которую сложно масштабировать, тестировать и читать. Код БД (PL/pgSQL) часто хуже, чем PHP.
    *   *Правило:* БД хранит данные и гарантирует их целостность. Приложение управляет данными.

2.  **Миграции:**
    *   Добавление `CHECK` constraint к таблице с миллионами строк блокирует запись (`LOCK MODE`) или долго сканирует данные.
    *   *Решение:* В PostgreSQL 9.5+ `ADD CONSTRAINT` не блокирует запись (uses speculative insertion lock), но все равно может быть медленным. Планируйте миграции во время простоя или используйте `NOT VALID` + валидацию позже.

3.  **Скрытие ошибок:**
    *   Если вы полагаетесь только на `CHECK` constraint, то при ошибке вы получите `PDOException` или `DBAL\Exception`.
    *   *Решение:* Оборачивайте такие ошибки в доменные исключения в вашем коде (`catch (UniqueConstraintViolationException $e) { throw new DuplicateEmailException($e); }`).

### Итог

**Валидация в коде — это «удобство» и «контроль».**
**Ограничения в БД — это «безопасность» и «гарантия».**

Идеальный стек:
1.  Symfony Form/Validator перехватывает 95% ошибок на входе.
2.  Business Logic в доменных объектах проверяет контекстные правила.
3.  PostgreSQL `UNIQUE` и `CHECK` ловят оставшиеся 5% (гонки, ошибки кода, внешние изменения).

Не дублируйте всю логику, но **никогда не упускайте** уникальность и базовые диапазоны из виду на уровне БД.

> ⏱ 32.1s · prompt 33788 tok · output 2240 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленного запроса в PostgreSQL — это не гадание, а алгоритмический процесс. Я подхожу к этому как к следователю: сначала собираю улики (что именно медленно), потом ищу мотив (почему медленно) и только затем применяю лечение (как исправить).

Вот мой пошаговый процесс, от «больно» до «быстро».

---

### Этап 1: Диагностика (Что именно медленно?)

Нельзя оптимизировать то, что не измерено. Мне нужно увидеть сам запрос и план его выполнения.

#### 1.1. Включение логирования SQL
Если запрос идет из приложения (Symfony/Doctrine), я включаю логирование:
*   **Symfony Profiler:** Вкладка «Database». Здесь я вижу время выполнения каждого конкретного SQL-запроса.
*   **PostgreSQL Logs:** Настраиваю `pg_stat_statements` (расширение, которое агрегирует статистику по запросам) или просто логирую в файл с `log_min_duration_statement = 100ms` (логируем запросы дольше 100 мс).

#### 1.2. План выполнения (EXPLAIN ANALYZE)
Это самый важный инструмент. Для медленного запроса я запускаю:

```sql
EXPLAIN (ANALYZE, BUFFERS, TIMING) 
SELECT * FROM orders 
WHERE customer_id = 123 
ORDER BY created_at DESC 
LIMIT 10;
```

**На что я смотрю в плане:**

1.  **Total Time vs Execution Time:**
    *   Если `Execution Time` маленький, а `Planning Time` большой — проблема в сложности плана (слишком много джойнов, оптимизатор тратит время на выбор).
    *   Если `Execution Time` большой — проблема в выполнении (чтение с диска, сортировка).

2.  **Seq Scan vs Index Scan:**
    *   `Seq Scan` (Sequential Scan) на большой таблице — **красный флаг**. Это значит, что PostgreSQL перебирает все строки.
    *   *Вопрос:* А должен ли он? Если выборка занимает >5-10% таблицы, `Seq Scan` может быть даже быстрее индекса из-за random I/O. Но если выборка мала — нужен индекс.

3.  **Sort Method:**
    *   `Sort Method: quicksort Disk: 123456 kB` — **Очень плохо**. Это значит, что память (`work_mem`) не хватила, и PostgreSQL начинал писать промежуточные результаты на диск (temp files).
    *   *Решение:* Увеличить `work_mem` для сессии или оптимизировать запрос (меньше данных для сортировки).

4.  **Nested Loop vs Hash Join:**
    *   `Nested Loop` хорош, если одна из таблиц очень мала.
    *   `Hash Join` или `Merge Join` лучше для больших таблиц.
    *   Если `Nested Loop` выполняется много раз и медленно — обычно проблема в отсутствии индекса во внутренней таблице цикла.

5.  **Rows vs Rows Removed by Filter:**
    *   Если PostgreSQL прочитал 1,000,000 строк, но вернул 10 (`Rows: 10`, `Rows Removed by Filter: 999,990`) — значит, фильтр неэффективен, и нужен индекс по условию `WHERE`.

---

### Этап 2: Типовые причины и их решения

#### Причина 1: Отсутствие индекса или неэффективный индекс

**Симптом:** `Seq Scan` на большой таблице при фильтре по редкому значению.

**Решение:**
1.  Создать индекс по колонке(ам) из `WHERE` или `JOIN`.
2.  **Покрытие индекса (Covering Index):** Если запрос выбирает только пару полей (`SELECT id, name FROM users WHERE email = ?`), создайте индекс, содержащий эти поля:
    ```sql
    CREATE INDEX idx_users_email_name ON users (email) INCLUDE (name);
    ```
    Теперь PostgreSQL не будет ходить в основную таблицу (Heap Fetch), а достанет данные прямо из индекса (Index-Only Scan).

**Trade-off:** Индексы ускоряют чтение, но замедляют запись (INSERT/UPDATE/DELETE). Не создавайте индексы «на всякий случай».

---

#### Причина 2: Неправильная сортировка (Sorting) и память

**Симптом:** `Sort Method: external merge disk` (данные пишутся на диск).

**Решение:**
1.  **Увеличить `work_mem`** (на время сессии или глобально):
    ```sql
    SET work_mem = '256MB';
    ```
    *Внимание:* Это память на сессию. Если у вас 100 одновременных запросов, это 100 * 256MB = 25GB RAM. Убедитесь, что хватает физической памяти.
2.  **Оптимизировать запрос:** Убедиться, что сортировка происходит по проиндексированному полю.
    ```sql
    -- Плохо: сортировка по created_at, индекс по customer_id
    SELECT * FROM orders WHERE customer_id = 1 ORDER BY created_at;
    
    -- Хорошо: составной индекс
    CREATE INDEX idx_orders_cust_date ON orders (customer_id, created_at);
    -- Теперь сортировка делается индексованном порядком, без дополнительной сортировки.
    ```

---

#### Причина 3: Проблемы с `JOIN` (Соединениями)

**Симптом:** `Hash Join` или `Nested Loop` с большим количеством строк во внутренней таблице.

**Решения:**
1.  **Порядок JOIN:** Иногда порядок таблиц в `JOIN` влияет на план. PostgreSQL обычно оптимизирует сам, но при 10+ джойнах стоит проверить.
2.  **Материализация подзапросов:**
    Если подзапрос считается многократно, вынесите его в CTE (Common Table Expression) или временную таблицу.
    ```sql
    WITH expensive_calc AS (
        SELECT ... -- сложная логика
    )
    SELECT ... FROM main_table JOIN expensive_calc ON ...
    ```
    *Нюанс:* В PG 12+ CTE оптимизируются inline (как обычный подзапрос). В PG 11 и ниже CTE — это оптимизируемая единица, которая материализуется. Для повторного использования CTE лучше подходят временные таблицы (`CREATE TEMP TABLE`).
3.  **Типы данных в JOIN:**
    *   **Классика:** `VARCHAR(255)` в одной таблице и `CHAR(10)` или `TEXT` в другой.
    *   Если типы не совпадают, PostgreSQL может не использовать индекс, выполняя неявное приведение типа (implicit cast).
    *   *Решение:* Приведите типы к единому стандарту.

---

#### Причина 4: `OFFSET` для пагинации

**Симптом:** Запрос с большим `OFFSET` (например, `LIMIT 20 OFFSET 100000`) выполняется медленно.

**Почему:** PostgreSQL должен просканировать первые 100 000 строк, чтобы отбросить их.

**Решение: Keyset Pagination (Поясная пагинация)**
Вместо:
```sql
SELECT * FROM orders ORDER BY id DESC LIMIT 20 OFFSET 100000;
```
Используйте:
```sql
-- Допустим, последний увиденный пользователем id был 5000
SELECT * FROM orders 
WHERE id < 5000 
ORDER BY id DESC 
LIMIT 20;
```
*   Это работает только если есть уникальный, стабильный ключ (ID, timestamp + uuid).
*   Скорость константная, независимо от глубины пагинации.

---

#### Причина 5: «Тупые» запросы в ORM (N+1 и лишние JOIN'ы)

**Симптом:** Запрос быстрый, но возвращается огромное количество данных (Blob'ы, большие JSON), которые не используются в приложении.

**Решение:**
1.  **SELECT конкретных полей:** Не `SELECT *`, а только нужные колонки.
2.  **Lazy Loading:** Убедитесь, что вы не грузите связанные сущности, если они не нужны сейчас.
3.  **DTO/Projection:** Если вам нужны только данные для списка, мапьте результат запроса прямо в DTO (Data Transfer Object), минуя тяжелые сущности.
    ```php
    // DQL
    SELECT NEW App\DTO\OrderListItem(o.id, o.total, o.status) 
    FROM App\Entity\Order o 
    WHERE ...
    ```

---

#### Причина 6: Блокировки (Locks)

**Симптом:** Запрос ждет (`waiting on lock`), хотя сам по себе может быть быстрым.

**Диагностика:**
```sql
SELECT * FROM pg_locks l 
JOIN pg_stat_activity s ON l.pid = s.pid 
WHERE NOT l.granted;
```

**Решение:**
1.  Короткие транзакции: Держите транзакции открытыми минимальное время.
2.  Избегайте блокировок на чтение: Используйте `FOR UPDATE` только там, где нужна оптимистическая/пессимистическая блокировка. Для чтения используйте обычный `SELECT` (MVCC в PG позволяет читать без блокировок).
3.  `NOWAIT` или `SKIP LOCKED`: Если вы обрабатываете очереди задач, используйте `SELECT ... FOR UPDATE SKIP LOCKED`, чтобы процессы не ждали друг друга, а просто пропускали блокированные строки.

---

### Итоговый чек-лист оптимизации

1.  **EXPLAIN ANALYZE:** Посмотреть план. Искать `Seq Scan`, `Disk Sort`, `Large Hash`.
2.  **Индексы:** Есть ли индекс по `WHERE`, `ORDER BY`, `JOIN`?
3.  **Память:** Хватает ли `work_mem`? Нет ли `temp files`?
4.  **Типы данных:** Совпадают ли типы в JOIN'ах?
5.  **Пагинация:** Используется ли `OFFSET`? Можно ли заменить на Keyset?
6.  **Кэш:** Можно ли кэшировать результат этого запроса (Redis/Memcached)?
7.  **Аппликационный слой:** Не перегружаем ли мы запрос лишними данными?

### Инструменты

*   **`EXPLAIN ANALYZE`:** Основной инструмент.
*   **`pg_stat_statements`:** Расширение для статистики самых дорогих запросов.
*   **`pg_stat_activity`:** Видит текущие активные сессии и их состояние (waiting, active).
*   **`auto_explain`:** Расширение, которое логит `EXPLAIN` для запросов, превышающих порог времени. Очень полезно для продакшена.

Оптимизация — это итеративный процесс. Почините один узкий узел, замерьте результат, и переходите к следующему.

> ⏱ 36.8s · prompt 36073 tok · output 2555 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD (Domain-Driven Design) — это кластер сущностей и объектов-значений, который рассматривается как единая единица для целей изменения состояния (transactions) и целостности данных.

Агрегат имеет **Корень (Aggregate Root)**. Корень — это единственная сущность в агрегате, к которой разрешен прямой доступ извне (из других агрегатов или прикладного слоя). Все остальные сущности внутри агрегата являются «детями» и существуют только в контексте Корня.

### Зачем нужны агрегаты?
1.  **Инкапсуляция инвариантов:** Гарантируется, что бизнес-правила (инварианты) соблюдаются *внутри* агрегата. Внешний код не может нарушить состояние, изменяя «детей» напрямую.
2.  **Граница транзакции:** Все изменения внутри одного агрегата происходят атомарно. Если что-то пошло не так, откатывается весь агрегат.
3.  **Упрощение модели:** Мы не управляем каждым битом состояния, а оперируем крупными блоками смыслов.

---

### Как определять границы агрегата?

По Эрику Эвансу (автор DDD), границы агрегата определяются **инвариантами** (правилами целостности).

**Правило:** Всё, что должно быть согласовано мгновенно и атомарно, находится в пределах одного агрегата. Всё, что может быть согласовано асинхронно (событиями), находится в разных агрегатах.

#### Алгоритм определения границ:
1.  **Найдите бизнес-правила (инварианты):** Какие условия должны быть истинными всегда? (Например: «Сумма позиций заказа должна равняться итоговой сумме»).
2.  **Сгруппируйте сущности:** Какие сущности нужно менять *вместе*, чтобы не нарушить инвариант?
3.  **Определите Корень:** Какая сущность управляет этой группой и имеет идентичность?
4.  **Проверьте на «раздутость»:** Если агрегат стал слишком большим (много сущностей, сложная логика), разбейте его, используя события для согласования между новыми агрегатами.

---

### Пример: Система заказов и инвентаря

#### Контекст:
Пользователь оформляет заказ. Система должна:
1. Создать заказ.
2. Убедиться, что товаров на складе достаточно.
3. Снять количество с остатков.

#### Плохая интуиция (Типовая ошибка):
«Давайте сделаем один огромный агрегат `OrderWithInventory`, внутри которого будут `Order`, `OrderLine` и `ProductInventory`. Тогда мы гарантируем консистентность».

#### Правильный подход (Разделение на агрегаты):

**Агрегат 1: `Order` (Заказ)**
*   **Корень:** `Order`
*   **Дети:** `OrderLine` (позиции заказа)
*   **Инкапсулированные инварианты:**
    *   Заказ не может быть пустым.
    *   Сумма позиций должна соответствовать итогу.
    *   Статус заказа меняется по определенным правилам (New -> Paid -> Shipped).

**Агрегат 2: `ProductInventory` (Склад/Инвентарь)**
*   **Корень:** `ProductInventory` (или просто `Product` с полем `stockCount`)
*   **Инкапсулированные инварианты:**
    *   Количество остатка не может быть отрицательным.
    *   Списание должно быть атомарным (если двое снимают одновременно, не должно быть гонок).

#### Как они взаимодействуют?
Они **не знают** друг о друге напрямую.
1.  Прикладной сервис загружает `Order`.
2.  Сервис вызывает метод на `ProductInventory`: `$inventory->reserve($productId, $qty)`.
    *   Внутри этого метода инвентарь проверяет остатки и резервирует их (в пределах своего агрегата).
    *   Если успех — возвращает подтверждение.
    *   Если нехватка — бросает исключение `InsufficientStockException`.
3.  Сервис создает/модифицирует `Order`.
4.  Сервис сохраняет **оба** агрегата.

> **Важный нюанс:** Если мы хотим, чтобы «резервирование» было строго синхронным и атомарным с созданием заказа, мы можем хранить их в одной транзакции, но логически они остаются разными агрегатами с разными инвариантами. Если же система распределенная, мы используем события: `OrderPlaced` -> `Inventory` слушает событие и резервирует товар.

---

### Типовая ошибка при выборе границ: «Космолет» (God Aggregate)

**Ситуация:**
Разработчик решает объединить в один агрегат:
*   `User` (Пользователь)
*   `Profile` (Профиль)
*   `Wallet` (Кошелёк)
*   `History` (История действий)
*   `Settings` (Настройки)

**Почему это ошибка?**
1.  **Нарушение SRP:** Этот «Космолет» меняется по 5 разным причинам (изменились правила баланса, изменился шаблон профиля, изменилась схема настроек).
2.  **Сложность инкапсуляции:** Чтобы изменить баланс (`Wallet`), вам нужно загрузить весь огромный объект `UserWithEverything`, даже если вам нужны только деньги.
3.  **Конкурентность:** Если один пользователь меняет настройки, а другой пытается купить товар (меняет баланс), они могут блокировать друг друга, потому что работают с одним большим объектом в БД.

**Как правильно:**
*   `User` (Агрегат: базовые данные, пароль, статус).
*   `Wallet` (Агрегат: баланс, транзакции. Инвариант: баланс >= 0).
*   `Profile` (Агрегат: имя, аватар. Инвариант: имя не пустое).
*   `Settings` (Агрегат: предпочтения).

Связь между ними — через ID пользователя. Изменение `Wallet` не требует перезагрузки `Profile`.

---

### Ещё одна типичная ошибка: «Хрупкие границы» (Тесная связь через ссылки)

**Ситуация:**
Агрегат `Order` содержит ссылку на агрегат `Product` и пытается менять состояние `Product` (например, уменьшать остаток) напрямую внутри метода `Order->addLine()`.

```php
class Order {
    public function addLine(Product $product, int $qty) {
        $product->decreaseStock($qty); // Гибель! Order управляет состоянием другого агрегата
        $line = new OrderLine($product, $qty);
        $this->lines[] = $line;
    }
}
```

**Почему это ошибка?**
1.  **Нарушение инкапсуляции:** `Order` знает, как управлять `Product`. Если логика инвентаря изменится (например, добавится логика «резерв на 15 минут»), `Order` тоже придется менять.
2.  **Сложность согласования:** Если `Order` и `Product` в разных транзакциях или микросервисах, такой код сломается.

**Как правильно:**
`Order` не меняет `Product`. `Order` просто фиксирует факт: «Я хочу 5 штук товара X».
Решение о том, достаточно ли на складе, принимает `Product` (через свой метод `canReserve()` или `reserve()`), который вызывается **из прикладного сервиса**, который управляет жизненным циклом обоих агрегатов.

### Итог

*   **Агрегат** — это граница инкапсуляции инвариантов.
*   **Граница** определяется тем, что должно быть **атомарным**.
*   **Ошибка:** Слишком большие агрегаты (нарушение SRP) или слишком тесная связь (нарушение инкапсуляции).
*   **Совет:** Держите агрегаты маленькими. Лучше иметь два маленьких агрегата, согласованных событием, чем один большой, который сложно тестировать и масштабировать.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это классическая проблема **конкурентности (Concurrency)**. Если два процесса одновременно читают, изменяют и записывают один и тот же агрегат, вы рискуете потерять данные (Lost Update Problem) или нарушить бизнес-инварианты.

Вот что происходит «под капотом» и как я проектирую систему, чтобы это предотвратить.

---

### 1. Что произойдет без защиты?

Допустим, есть кошелёк с балансом `100`.
*   **Процесс A:** Читает баланс (`100`), вычитает `50` (новый баланс `50`).
*   **Процесс B:** Читает баланс (`100`), вычитает `30` (новый баланс `70`).
*   **Запись A:** Сохраняет `50`.
*   **Запись B:** Сохраняет `70`.

**Результат:** В базе `70`, хотя должно быть `100 - 50 - 30 = 20`.
Процесс A потерял свое изменение, потому что он переписал старое значение, а не применил свою дельту к текущему.

---

### 2. Стратегии проектирования

Есть два основных подхода: **Optimistic Locking** (Оптимистичная блокировка) и **Pessimistic Locking** (Пессимистичная блокировка).

#### А. Optimistic Locking (Оптимистичная) — *Рекомендуемый стандарт*

**Философия:** Мы предполагаем, что конфликты редки. Мы не блокируем строку при чтении. Конфликт обнаруживается только при сохранении.

**Как это работает:**
1.  В таблице агрегата есть колонка `version` (целое число, изначально 0).
2.  **Чтение:** Процесс A читает строку с `version=10` и балансом `100`.
3.  **Изменение:** Процесс A меняет баланс на `50`.
4.  **Запись:** Процесс A выполняет UPDATE с условием:
    ```sql
    UPDATE wallets 
    SET balance = 50, version = 11 
    WHERE id = 1 AND version = 10; 
    ```
5.  **Конфликт:** Если между чтением и записью Процесс B уже сохранил данные и увеличил `version` до `11`, условие `WHERE version = 10` не найдёт строк.
6.  **Результат:** `affected rows = 0`. ORM (Doctrine) бросает исключение `OptimisticLockException`.

**Реализация в Doctrine:**
```php
#[ORM\Entity]
class Wallet
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $balance;

    #[ORM\Version] // Магия: Doctrine автоматически управляет этой колонкой
    private int $version;

    public function deposit(int $amount): void
    {
        $this->balance += $amount;
    }
}
```
*   При `find()` Doctrine загружает `version=10`.
*   При `flush()` Doctrine генерирует `UPDATE ... SET balance=?, version=? WHERE id=? AND version=?`.
*   Если строка изменена кем-то другим — ошибка.

**Trade-offs:**
*   ✅ **Простота:** Минимум кода, нет блокировок при чтении.
*   ✅ **Масштабируемость:** Высокая пропускная способность, так как чтение не блокирует запись.
*   ❌ **Обработка ошибок:** Нужно ловить `OptimisticLockException` и решать, что делать (повторить попытку? показать ошибку пользователю?).
*   ❌ **Не适合 для долгой обработки:** Если пользователь заполняет форму 5 минут, а за это время кто-то изменил данные, сохранение упадет.

---

#### Б. Pessimistic Locking (Пессимистичная)

**Философия:** Мы предполагаем, что конфликты вероятны или данные критичны. Мы блокируем строку в момент чтения, пока транзакция не завершится.

**Как это работает:**
1.  Процесс A выполняет `SELECT ... FOR UPDATE`.
2.  PostgreSQL ставит **эксклюзивную блокировку** на строку.
3.  Процесс B пытается выполнить `SELECT ... FOR UPDATE` на ту же строку — он **ждет** (блокируется), пока Процесс A не сделает `COMMIT` или `ROLLBACK`.
4.  Процесс A изменяет данные и коммитит.
5.  Процесс B освобождается от блокировки, читает актуальные данные и продолжает работу.

**Реализация в Doctrine:**
```php
// При загрузке сущности
$wallet = $this->entityManager
    ->getRepository(Wallet::class)
    ->find($id, LockMode::OPTIMISTIC); // Или PESSIMISTIC_WRITE
// Для пессимистичной:
// ->find($id, LockMode::PESSIMISTIC_WRITE);
```
*Или через DQL:*
```php
$qb->select('w')
   ->from(Wallet::class, 'w')
   ->where('w.id = :id')
   ->setLockMode(LockMode::PESSIMISTIC_WRITE);
```

**Trade-offs:**
*   ✅ **Предсказуемость:** Гарантированно нет потери данных. Конфликт разрешается последовательностью.
*   ❌ **Пропускная способность:** Если многие процессы читают одни и те же данные, они выстраиваются в очередь. Риск Deadlock'а (взаимной блокировки).
*   ❌ **Сложность:** Нужно следить за длительностью транзакций. Если транзакция зависла, все остальные будут ждать.

---

### 3. Как я проектирую систему? (Мой выбор)

Большинство бизнес-сценариев я решаю через **Optimistic Locking**, потому что оно проще и масштабируме. Но я добавляю слой обработки конфликтов.

#### Архитектура решения:

1.  **Уровень БД:** Колонка `version` во всех изменяемых агрегатах.
2.  **Уровень Doctrine:** Атрибут `#[ORM\Version]`.
3.  **Уровень Прикладного Сервиса:** Обработка исключений.

```php
class WalletService
{
    public function __construct(private WalletRepository $walletRepo) {}

    public function deposit(int $walletId, int $amount): void
    {
        $wallet = $this->walletRepo->find($walletId);
        if (!$wallet) throw new WalletNotFoundException($walletId);

        // Бизнес-логика внутри агрегата
        $wallet->deposit($amount);

        // Сохранение
        try {
            $this->walletRepo->save($wallet);
        } catch (OptimisticLockException $e) {
            // Конфликт! Кто-то другой изменил баланс.
            
            // Стратегия 1: Бросить ошибку пользователю
            throw new ConcurrencyConflictException('Data was modified by another user. Please refresh and try again.');
            
            // Стратегия 2 (для фоновых задач): Повторить попытку с новым чтением
            // $this->retryDeposit($walletId, $amount, maxRetries: 3);
        }
    }
}
```

#### Когда использовать Pessimistic?

Только в случаях **критической последовательности** или **ограниченных ресурсов**:
1.  **Резервирование мест на рейсе:** Если мест мало и конкуренция высокая, лучше блокировать строку `flight_seat`, чем постоянно конфликтовать и повторять.
2.  **Генерация уникальных серийных номеров:** `SELECT ... FOR UPDATE` гарантирует, что два процесса не получат один и тот же номер.
3.  **Очереди задач:** `SELECT ... FOR UPDATE SKIP LOCKED` — особый случай пессимистичной блокировки, где процессы не ждут друг друга, а пропускают занятые строки.

---

### 4. Типовые ошибки

1.  **Пессимистичная блокировка на все случаи жизни:**
    *   *Ошибка:* Блокировка всей таблицы `users` при любом изменении профиля.
    *   *Последствие:* Сервер падает при нагрузке.
    *   *Решение:* Использовать Optimistic, если конфликты редки.

2.  **Долгая транзакция с блокировкой:**
    *   *Ошибка:* Пессимистичная блокировка -> вызов внешнего API (медленный, 2 секунды) -> коммит.
    *   *Последствие:* Все другие запросы ждут 2 секунды.
    *   *Решение:* Вынести внешние вызовы из транзакции с блокировкой или использовать Optimistic.

3.  **Игнорирование `version`:**
    *   *Ошибка:* Есть колонка `version`, но разработчик вручную обновляет её в коде, а не полагается на ORM.
    *   *Последствие:* Конфликты не ловятся.
    *   *Решение:* Доверяйте `#[ORM\Version]` и не трогайте это поле в бизнес-методах.

### Итог

*   **По умолчанию:** Оптимистичная блокировка (`version`). Проще, быстрее, масштабируется лучше.
*   **При высокой конкуренции/критичности:** Пессимистичная блокировка (`FOR UPDATE`). Надёжнее, но медленнее.
*   **Всегда:** Ловите и обрабатывайте исключения от конкурентности, не позволяйте им упасть как необработанная ошибка 500.

> ⏱ 28.3s · prompt 38664 tok · output 1925 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это один из самых мощных, но и самых коварных инструментов в DDD. Они позволяют развязать модули (модули), реализовать «Event Sourcing» или просто улучшить масштабируемость.

Однако, если применить их неправильно, вы получите систему, которая выглядит как микросервисная, но ведет себя как монолит с непредсказуемой последовательностью действий.

Давайте разберем это системно.

---

### 1. Когда использовать доменные события?

**Используйте, если:**
1.  **Развязка модулей:** Модуль A (Заказы) должен сообщить о событии, но Модуль B (Бухгалтерия) не должен быть прямой зависимостью А.
2.  **Асинхронность:** Действие после события (отправка email, обновление поиска, начисление баллов) не должно блокировать ответ пользователю.
3.  **Аудит и аналитика:** Нужно записать факт «Заказ оформлен» для истории, независимо от того, успешно ли прошла оплата.
4.  **Event-Driven Architecture (EDA):** Вы строите систему из микросервисов или отдельных рабочих процессов.

**НЕ используйте (или используйте осторожно), если:**
1.  **Строгая синхронная зависимость:** Если Модуль B нужен для успешного завершения действия А в *том же* транзакционном контексте (например, проверка баланса перед списанием), лучше вызвать метод напрямую (через Сервис или Агрегат), чем ждать событие.
2.  **Простота прежде всего:** Если логика простая («сохранил заказ — отправил письмо»), и нет риска потери письма, иногда проще синхронный вызов, чем очередь событий.

---

### 2. Как это устроено в Symfony/PHP?

В Symfony есть два уровня событий:
1.  **Kernel Events:** Для HTTP-цикла (Request, Response). Не для бизнеса.
2.  **Domain Events:** Бизнес-события. В Symfony они часто реализуются через **Symfony Messenger** (как сообщения) или через **Symfony Event Dispatcher** (как синхронные события).

**Мой подход:** Я трактую доменные события как **сообщения (Messages)** в очереди. Это дает асинхронность и надежность.

#### Архитектура:

1.  **Сборка событий (Capture):** Во время изменения агрегата (внутри `Order::complete()`) мы «фиксируем» событие в коллекции.
    ```php
    class Order {
        private array $domainEvents = [];

        public function complete(): void {
            if ($this->status !== OrderStatus::Paid) {
                throw new \LogicException('Order is not paid');
            }
            
            $this->status = OrderStatus::Completed;
            
            // 1. Создаем событие
            $event = new OrderCompletedEvent($this->id, $this->total);
            
            // 2. Сохраняем его внутри агрегата (Dirty Tracking)
            $this->domainEvents[] = $event;
        }

        /**
         * Метод для извлечения событий (обычно вызывается репозиторием или сервисом)
         */
        public function pullDomainEvents(): array
        {
            $events = $this->domainEvents;
            $this->domainEvents = []; // Очищаем, чтобы не отправлять дважды
            return $events;
        }
    }
    ```

2.  **Публикация (Publish):** После сохранения агрегата в БД мы извлекаем события и отправляем их в шину (Bus).
    ```php
    class OrderService {
        public function __construct(
            private OrderRepositoryInterface $repo,
            private EventDispatcherInterface $eventDispatcher // Symfony DI
        ) {}

        public function completeOrder(int $orderId): void
        {
            $order = $this->repo->find($orderId);
            $order->complete(); // Изменяем состояние, фиксируем событие

            // Сохраняем агрегат
            $this->repo->save($order); 
            // Здесь внутри save() происходит flush(), и данные попадают в БД.

            // 1. Извлекаем события из агрегата
            $events = $order->pullDomainEvents();

            // 2. Публикуем их
            foreach ($events as $event) {
                // Если используем Messenger:
                $this->eventDispatcher->dispatch($event); 
                // Или напрямую в MessageBus для асинхронности
            }
        }
    }
    ```

3.  **Обработка (Handle):**
    *   **Синхронный (EventDispatcher):** Слушатель выполняется сразу в том же процессе. Подойдет для аудита или быстрых проверок.
    *   **Асинхронный (Messenger):** Сообщение уходит в очередь (Redis/RabbitMQ). Отдельный воркер (`messenger:consume`) забирает его и вызывает `__invoke()` обработчика.

---

### 3. Критический вопрос: Связь с транзакциями БД

Это самое важное место, где разработчики ошибаются.

**Правило:** **Событие должно быть опубликовано ТОЛЬКО ПОСЛЕ успешного коммита транзакции.**

#### Сценарий 1: Событие опубликовано ДО flush()
1.  Агрегат изменен.
2.  Событие отправлено в очередь.
3.  `flush()` падает с ошибкой (например, нарушено уникальное ограничение).
4.  Транзакция откатывается. В БД ничего не изменилось.
5.  **НО!** Воркер уже забрал событие и начал его обработку. Бухгалтерия получила запись об оплате заказа, которого не существует. **Данные рассинхронизированы.**

#### Сценарий 2: Событие опубликовано ПОСЛЕ flush()
1.  Агрегат изменен.
2.  `flush()` проходит успешно. Данные записаны в БД.
3.  Событие отправлено в очередь.
4.  Воркер обрабатывает событие.
5.  Если воркер упадет, событие останется в очереди (при использовании reliable transport) и будет повторено. **Консистентность сохранена.**

**Проблема:** Как гарантировать, что `flush()` завершился, но событие еще не ушло, если между ними упадет процесс?
*   Если процесс упадет между `flush()` и `dispatch()` — данные в БД есть, событие потеряно.
*   Если использовать **Transactional Outbox Pattern** (см. ниже), мы решаем эту проблему.

---

### 4. Transacted Outbox Pattern (Паттерн «Отправлено в БД»)

Это золотой стандарт для обеспечения консистентности между БД и очередью сообщений.

**Как это работает:**
1.  В той же транзакции, где вы сохраняете агрегат, вы сохраняете запись в таблицу `outbox` (или `events`).
2.  Таблица `outbox` содержит: `id`, `event_name`, `payload` (JSON), `created_at`, `sent` (bool).
3.  Отдельный процесс (Outbox Relay/Dispatcher) периодически сканирует таблицу `outbox` в поисках новых записей (`sent = false`).
4.  Он отправляет их в брокер сообщений (RabbitMQ/Kafka) или локальный Event Dispatcher.
5.  После успешной отправки помечает запись как `sent = true`.

**Почему это хорошо?**
*   **Атомарность:** Запись агрегата и запись события в `outbox` происходят в одной транзакции БД. Либо оба записались, либо ни одного.
*   **Надежность:** Даже если брокер сообщений упадет, событие не потеряется. Оно лежит в БД.
*   **Идемпотентность:** Воркер должен уметь обрабатывать событие повторно (если брокер отправил его дважды), так как доставка «at-least-once».

**Реализация в PHP:**
1.  Создайте сущность `EventOutbox`.
2.  В сервисе:
    ```php
    $em->beginTransaction();
    try {
        $order->complete();
        $em->persist($order);
        
        // Создаем запись в outbox
        $outboxEntry = new EventOutbox(new OrderCompletedEvent($order->getId()));
        $em->persist($outboxEntry);
        
        $em->flush(); // Атомарная запись
        $em->commit();
    } catch (\Exception $e) {
        $em->rollback();
        throw $e;
    }
    ```
3.  Запустите консольную команду (через Cron или Messenger):
    ```bash
    php bin/console app:send-outbox-events
    ```
    Эта команда читает новые записи из `outbox` и диспатчит их.

---

### 5. Какие проблемы создает этот подход?

1.  **Сложность отладки:**
    *   Поток данных стал нелинейным. Чтобы понять, почему «Бухгалтерия» не получила данные, нужно проверить:
        1.  Было ли сгенерировано событие?
        2.  Попало ли оно в `outbox`?
        3.  Отправлено ли оно брокером?
        4.  Обработано ли оно воркером?
        5.  Упало ли оно в dead-letter queue?

2.  **Идемпотентность:**
    *   Брокеры сообщений (RabbitMQ, Kafka) гарантируют доставку, но не единственность. Сообщение может прийти дважды.
    *   **Решение:** Ваши обработчики событий должны быть идемпотентными.
    *   *Пример:* Перед начислением баллов проверьте: «Были ли уже начислены баллы за этот конкретный `orderId`?» Если да — пропустите.

3.  **Порядок событий:**
    *   Если у вас есть события `OrderPlaced` и `OrderCancelled`, они должны быть обработаны в правильном порядке.
    *   Очередь гарантирует порядок в рамках одного partition/queue, но если у вас много консьюмеров, порядок может нарушиться.
    *   **Решение:** Группируйте события по сущности (например, все события заказа с `id=123` попадают в один и тот же partition Kafka).

4.  **Версионирование событий:**
    *   Если вы измените структуру события (добавите поле), старые сообщения в очереди (или в outbox) могут сломать новые обработчики.
    *   **Решение:** Версионируйте события (`v1.OrderCompleted`, `v2.OrderCompleted`) или используйте схему (Avro/JSON Schema) с обратной совместимостью.

---

### 6. Резюме: Мой чек-лист применения доменных событий

1.  **Генерация:** События генерируются внутри Агрегата (Dirty Tracking).
2.  **Сохранение:** Агрегат и запись о событии (Outbox) сохраняются в одной транзакции БД.
3.  **Отправка:** Отдельный процесс (Relay) отправляет события из Outbox в брокер/шину.
4.  **Обработка:** Воркеры обрабатывают события. Они должны быть идемпотентными.
5.  **Исключения:** Если событие не удалось обработать — оно уходит в Dead Letter Queue (DLQ) для ручной обработки или повторных попыток.

Этот подход гарантирует, что бизнес-данные и факт произошедшего события всегда согласованы, даже в условиях высокой нагрузки и сбоев инфраструктуры.

> ⏱ 39.1s · prompt 42921 tok · output 2662 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это три фундаментальных вопроса DDD, которые часто путают. Давайте разберем их по порядку, от абстракции к практике.

### 1. Value Object (VO) vs Entity (Сущность)

Главное различие не в наличии или отсутствии ID, а в **идентичности** и **поведении во времени**.

| Критерий | **Entity (Сущность)** | **Value Object (Объект-значение)** |
| :--- | :--- | :--- |
| **Идентичность** | Определяется **ID** (UUID, Primary Key). Два объекта с разными ID — это разные объекты, даже если их поля идентичны. | Определяется **значением полей**. Два объекта с одинаковыми полями — это **одни и те же** объекты. ID не нужен. |
| **Изменяемость** | Может изменяться во времени (статус, баланс, история). | **Неизменяем (Immutable)**. Если значение меняется — создается новый объект. Старый «погиб». |
| **Жизненный цикл** | Долгий. Создается, меняется, удаляется. | Кратковременный. Создается как результат расчета или чтения, используется, и больше нигде не фигурирует. |
| **Сравнение** | `===` (сравнение по ссылке/ID). | `==` (сравнение по значению, `equals()`). |
| **Примеры** | `User`, `Order`, `Account`, `Product`. | `Money`, `Email`, `Address`, `DateRange`, `PhoneNumber`, `Name`. |

#### Ключевой ментальный тест:
> **«Мне все равно, КАКОЙ именно адрес у пользователя, мне важно, ЧТО в нем написано (улица, город). Если я заменю старый адрес на новый с теми же данными, будет ли это ошибкой? Нет. Значит, это Value Object.»**

> **«Мне важно, КАКОЙ именно заказ я обработал. Заказ #123 и Заказ #124 с одинаковой суммой — это разные заказы. Значит, это Entity.»**

---

### 2. Как решать: VO или Entity?

Когда вы моделируете домен, задайте себе три вопроса о конкретной концепции (например, «Адрес», «Пароль», «Деньги»):

1.  **Есть ли у него уникальный идентификатор, который должен сохраняться между транзакциями?**
    *   Если да — скорее всего, **Entity**.
    *   Если нет (или ID генерируется на лету и не имеет смысла вне текущего контекста) — **Value Object**.

2.  **Должен ли он храниться в БД как отдельная строка с `PRIMARY KEY`?**
    *   Если да — **Entity** (или Embedded Entity, но логически сущность).
    *   Если он хранится в колонках (строка, массив, JSON) внутри другой сущности — **Value Object**.

3.  **Важна ли его «история» или «идентичность» для бизнес-логики?**
    *   *Пример:* Пользователь сменил email.
        *   Если мы должны помнить, *какой* email был раньше, для аудита или восстановления доступа — это может быть **Entity** (таблица `user_emails` с ID, `created_at`, `is_active`).
        *   Если мы просто храним *текущий* email для отправки писем, и прошлый нам не важен — это **Value Object** (поле `email` в таблице `users`).

**Практическое правило:**
*   **Деньги (`Money`)**: Всегда **Value Object**. `new Money(100, 'USD')` и `new Money(100, 'USD')` — это одно и то же значение.
*   **Корзина (`Cart`)**: Обычно **Entity** (если она сохраняется с ID и может быть пустой/заполненной), но иногда **Aggregate Root**, если она самостоятельна.
*   **Товар в заказе (`OrderLine`)**: Это **Entity** (потому что у него есть ID, чтобы отслеживать изменения количества в рамках конкретного заказа), но он **Child Entity** (существует только внутри `Order`).
*   **Адрес отправителя**: Если он просто данные для печати на чеке — **Value Object**. Если это отдельная сущность с ID, которую можно переиспользовать между заказами — **Entity**.

---

### 3. Ubiquitous Language (Единый язык)

**Ubiquitous Language** — это общий словарь, который используют и бизнес-эксперты (аналитики, менеджеры), и разработчики.

**Цель:** Устранить двусмысленность.
*   *Бизнес говорит:* «Убери это из корзины».
*   *Разработчик думает:* `remove_item_from_list()`.
*   *В коде:* `delete_product_from_cart()`.
*   *В БД:* `DELETE FROM cart_items`.

Если в коде есть `delete`, а в бизнесе говорят «убрать», это разные слова для одной и той же операции. Ubiquitous Language заставляет всех использовать **одно и то же слово**.

#### Как это влияет на код?

1.  **Имена классов и методов отражают бизнес-правила:**
    *   ❌ Плохо: `processData()`, `handleInput()`, `executeAction()`.
    *   ✅ Хорошо: `calculateTaxForOrder()`, `reserveInventoryForProduct()`, `notifyUserAboutShipment()`.

2.  **Упрощение коммуникации:**
    *   Когда вы обсуждаете баг с менеджером, вы не говорите: «Есть проблема в методе `updateStatus` сервиса `OrderService`».
    *   Вы говорите: «Заказ не переходит в статус *Оплачен* после подтверждения платежа».
    *   Менеджер сразу понимает контекст.

3.  **Автодокументация:**
    *   Код читается как документация. Если вы видите класс `Order` и метод `markAsPaid()`, вы понимаете бизнес-смысл без чтения комментариев.

4.  **Поиск ошибок:**
    *   Если в системе есть три разных слова для «клиента»: `Customer`, `User`, `Client`, — это признак того, что Ubiquitous Language нарушен. Нужно выбрать один термин (например, `Customer` для тех, кто покупает, и `User` для тех, кто заходит в личный кабинет, если это разные понятия) и использовать его везде.

#### Пример применения:

Допустим, в домене «Банковские счета»:
*   Бизнес говорит: «Пополнение счета».
*   Не «Deposit», не «AddFunds», не «IncreaseBalance». А именно **Пополнение**.
*   Метод: `Account::deposit(amount)`.
*   Событие: `AccountDepositedEvent`.
*   Таблица в БД: `account_deposits` (а не `transactions` или `movements`).

Если вы находитесь в другой команде, работающей над «Счета», они тоже будут использовать слово `deposit`, потому что это часть Ubiquitous Language всего предприятия.

---

### Итог: Как связать все вместе?

1.  **Моделируйте домен**, используя **Ubiquitous Language**. Называйте классы так, как их называют бизнес-эксперты.
2.  **Определяйте тип объекта:**
    *   Если это «кто» или «что» с идентификатором и историей — **Entity**.
    *   Если это «какие данные» или «какие характеристики» без идентичности — **Value Object**.
3.  **Группируйте в Агрегаты:**
    *   Корень (Entity) управляет своими детьми (Entities или Value Objects).
    *   Инварианты (правила) охраняются внутри Агрегата.
4.  **Внедряйте зависимости через интерфейсы**, чтобы домен не знал о базе данных, а база данных не знала о бизнес-правиллах.

**Важный нюанс:** Value Object — это не просто «структура данных». Он должен иметь **поведение**.
*   ❌ `class Address { public string $street; public string $city; }`
*   ✅ `class Address { public function __construct(public readonly string $street, ...) {} public function validate(): void { ... } public function equals(Address $other): bool { ... } }`

Эта связка (VO + Entity + Ubiquitous Language) и есть ядро Domain-Driven Design.

> ⏱ 29.1s · prompt 45641 tok · output 1935 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Структура типичного PHP-приложения по принципам Чистой Архитектуры (Clean Architecture) или Гексагональной архитектуры (Hexagonal Architecture) строится вокруг концепции **«Вращающихся колес»** (Concentric Circles).

Цель — изолировать **бизнес-логику (Ядро)** от всего внешнего мира (фреймворки, БД, UI).

### 1. Структура каталогов (Пример для Symfony)

```text
app/
├── Domain/              # 🟢 ЯДРО (Чистый PHP, без зависимостей)
│   ├── Order/
│   │   ├── Order.php            # Агрегат / Сущность
│   │   ├── OrderId.php          # Value Object (ID)
│   │   ├── OrderStatus.php      # Value Object (Enum)
│   │   ├── Money.php            # Value Object
│   │   ├── OrderRepositoryInterface.php # Интерфейс репозитория
│   │   ├── OrderNotFoundException.php   # Доменное исключение
│   │   ├── Events/
│   │   │   └── OrderCompleted.php       # Доменное событие
│   │   └── Validators/
│   │       └── OrderValidator.php       # Бизнес-валидация
│   └── User/
│       ├── User.php
│       └── UserRepositoryInterface.php
│
├── Application/         # 🔵 ПРИКЛАДНОЙ СЛОЙ (Оркестрация)
│   ├── Order/
│   │   ├── CreateOrderCommand.php     # DTO (входящий)
│   │   ├── OrderCreatedResponse.php   # DTO (исходящий)
│   │   ├── CreateOrderHandler.php     # Use Case / Application Service
│   │   └── CancelOrderHandler.php
│   └── User/
│       ├── RegisterUserCommand.php
│       └── RegisterUserHandler.php
│
├── Infrastructure/      # 🟠 ИНФРАСТРУКТУРА (Внешний мир)
│   ├── Persistence/           # Работа с БД
│   │   ├── Doctrine/
│   │   │   ├── Entity/
│   │   │   │   └── OrderEntity.php    # ORM-сущность (маппинг)
│   │   │   └── Repository/
│   │   │       └── DoctrineOrderRepository.php # Реализация интерфейса из Domain
│   │   └── Migrations/
│   ├── Messaging/             # Работа с очередями
│   │   ├── Publisher/
│   │   │   └── MessengerPublisher.php # Публикатор событий
│   │   └── Consumer/
│   │       └── OrderCompletedConsumer.php # Обработчик сообщения
│   └── Api/                   # Клиенты внешних сервисов
│       └── StripeClient.php
│
├── Presentation/        # 🔴 ПРЕЗЕНТАЦИЯ (UI / API)
│   ├── Http/                # Web-слой
│   │   ├── Controller/
│   │   │   └── OrderController.php    # Symfony Controller
│   │   ├── Form/
│   │   │   └── OrderType.php          # Symfony Form
│   │   └── Serializer/
│   │       └── OrderDto.php           # DTO для JSON
│   └── Cli/                 # Console-слой
│       └── Command/
│           └── ImportOrdersCommand.php
```

---

### 2. Что лежит в каждом слое?

#### 🟢 Domain (Ядро)
*   **Сущности и Агрегаты:** Логика бизнеса (`Order`, `User`).
*   **Value Objects:** `Money`, `Date`, `Email`.
*   **Интерфейсы:** Определения контрактов для внешних зависимостей (`OrderRepositoryInterface`, `PaymentGatewayInterface`).
*   **Доменные события:** Классы данных, описывающие произошедшие события (`OrderCompleted`).
*   **Доменные исключения:** `OrderNotFoundException`.
*   **Что НЕ должно быть здесь:** Symfony, Doctrine, Laravel, Eloquent, HTTP-заголовки, SQL, JSON. Только чистый PHP.

#### 🔵 Application (Прикладной слой)
*   **Use Cases (Хендлеры/Сервисы):** Координируют выполнение сценария. Они загружают данные через репозитории, вызывают методы домена и сохраняют результат.
    *   *Пример:* `CreateOrderHandler` берет `CreateOrderCommand`, вызывает `Order::create()`, сохраняет через `OrderRepository`, публикует событие `OrderCreated`.
*   **DTO (Data Transfer Objects):** Структуры данных для передачи между слоями (входные команды и выходные ответы).
*   **Что НЕ должно быть здесь:** Детальная бизнес-логика (она в Domain), SQL-запросы (они в Infrastructure), HTML (они в Presentation).

#### 🟠 Infrastructure (Инфраструктура)
*   **ORM Entities:** Классы, привязанные к табличкам БД (через Doctrine Annotations/Attributes). Они «мостят» доменную модель к схеме БД.
*   **Реализации репозиториев:** `DoctrineOrderRepository implements OrderRepositoryInterface`. Здесь происходит преобразование Domain Object <-> ORM Entity.
*   **Мессенджеры:** Конкретные реализации отправки сообщений (RabbitMQ, Redis, Doctrine Outbox).
*   **Внешние клиенты:** HTTP-клиенты для Stripe, Google Maps и т.д.
*   **Конфигурация:** YAML/PHP файлы конфигурации драйверов.

#### 🔴 Presentation (Презентация)
*   **Контроллеры:** Принимают HTTP-запрос, валидируют базовый формат (Form), вызывают Application Layer, возвращают HTTP-ответ (JSON/HTML).
*   **Шаблоны:** Twig (HTML), JSON-схемы.
*   **Middleware:** Аутентификация, логирование запросов.
*   **CLI Команды:** Если у вас есть консольные скрипты, они тоже здесь (или в отдельном слое, но по сути это "Presentation" для терминала).

---

### 3. Правило зависимостей (Dependency Rule)

Зависимости должны направляться **только внутрь**, к ядру.

```
Presentation -> Application -> Domain
       ^           ^            ^
       |           |            |
Infrastructure ----------------+
```

*   **Domain** не знает о ни об одном другом слое. Он не импортирует Symfony, Doctrine и т.д.
*   **Application** знает о Domain (использует его сущности и интерфейсы). Не знает о Presentation и Infrastructure (напрямую).
*   **Infrastructure** знает о Domain (реализует его интерфейсы) и Application (если нужно опубликовать события).
*   **Presentation** знает о Application (вызывает хендлеры) и Domain (если нужно валидировать входные данные через доменные VO).

**Ключевой момент:** Symfony (фреймворк) находится в **Presentation** и **Infrastructure**. Он НЕ знает про Domain напрямую (через интерфейсы и адаптеры).

---

### 4. Куда попадают конкретные вещи?

1.  **Doctrine (ORM, Entities, Repositories):**
    *   **Infrastructure/Persistence/Doctrine**.
    *   `OrderEntity` (ORM) лежит здесь.
    *   `DoctrineOrderRepository` (реализация) лежит здесь.
    *   `OrderRepositoryInterface` (контракт) лежит в **Domain**.

2.  **Symfony Controller:**
    *   **Presentation/Http/Controller**.
    *   Он принимает `Request`, мапит его в `Command` (Application DTO), вызывает `ApplicationHandler`, получает `Response` (Application DTO) и конвертирует в `JsonResponse`.

3.  **Обработчик очереди (Messenger Handler):**
    *   Это интересный случай.
    *   **Сами сообщения (Messages/DTO):** Часто их держат в **Application** (так как они описывают "Use Case", например, `SendOrderEmailCommand`).
    *   **Хендлер (Handler):** Логически это **Application** Service, который выполняет сценарий. Но технически он вызывается инфраструктурой (Messenger).
    *   **Клиент очереди (Publisher/Consumer):** **Infrastructure/Messaging**.
    *   *Пример:*
        1.  Application Handler публикует событие `OrderCompleted`.
        2.  Infrastructure `MessengerPublisher` кладет его в Redis.
        3.  Infrastructure `OrderEmailConsumer` (Symfony Messenger Handler) забирает его из Redis, десериализует в DTO и вызывает `ApplicationHandler::sendEmail()`.

---

### 5. Как следить, что границы слоёв не нарушаются?

Если разработчики начнут импортировать `Doctrine\ORM\Entity` в `Domain` слой, чистая архитектура погибнет.

#### Методы контроля:

1.  **Статический анализ кода (Architest / PHPStan Custom Rules):**
    *   Настройте `PHPStan` или `Arch` (библиотека для PHP) так, чтобы правила зависимостей были жесткими.
    *   *Правило:* Классы в `src/Domain` не должны импортировать что-либо из `src/Infrastructure`, `vendor/symfony`, `vendor/doctrine`.
    *   *Правило:* Классы в `src/Application` не должны импортировать `src/Presentation`.

    Пример конфига `phpstan.neon`:
    ```yaml
    parameters:
        ignoreErrors:
            - '#Domain layer must not depend on Infrastructure#'
    ```
    Или используйте пакет `brianium/paratest` или специализированный `arch-php/arch`.

2.  **Code Review:**
    *   Ревьюер спрашивает: «Почему этот метод в `Order` знает про `EntityManager`?»
    *   Ответ должен быть: «Он не знает, он принимает `OrderRepositoryInterface`».

3.  **Тестирование:**
    *   **Unit-тесты для Domain:** Должны проходить без мокирования БД. Если вам нужно мокнуть `EntityManager`, значит, логика попала не туда.
    *   **Integration-тесты:** Здесь уже подключаются Doctrine и Symfony.

4.  **Разделение пакетов (Composer):**
    *   В крупных системах вынесите `Domain` в отдельный пакет (library).
    *   `composer.json` пакета `app/domain` НЕ имеет зависимостей от `symfony/console` или `doctrine/orm`.
    *   `composer.json` пакета `app/infrastructure` имеет зависимость от `app/domain`.
    *   Если вы попытаетесь добавить `doctrine/orm` в `domain`, композер упадет с ошибкой или линтер ругнется.

### Итог

Чистая архитектура в PHP/Symfony — это не про «разложить по папкам», а про **направление зависимостей**.
*   **Domain** — это ваш бизнес, он вечен и не зависит от технологий.
*   **Symfony** — это «обвязка», которая может быть заменена (Laravel, Silex, Raw PHP) без переписывания бизнес-логики.
*   **Doctrine** — это «переводчик» между вашим бизнесом и таблицами в БД.

Следите за чистотой границ с помощью статического анализа, и ваша система останется гибкой даже через 5 лет.

> ⏱ 38.9s · prompt 47645 tok · output 2616 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony-приложения с PostgreSQL я выбираю **модифицированную пирамиду тестов**, смещённую в сторону интеграционных тестов, но с жёстким контролем скорости. Чистые unit-тесты для доменной логики и «тяжёлые» e2e-тесты для критических сценариев.

Вот моя стратегия.

### 1. Пирамида тестов: Что и где тестируем

#### 🟢 Уровень 1: Unit-тесты (База)
**Цель:** Проверить чистую бизнес-логику (Domain Layer) и изолированные алгоритмы.
**Объём:** 60-70% всех тестов.
**Скорость:** Миллисекунды.

*   **Что тестируем:**
    *   Доменные сущности и Value Objects (`Money`, `Order`, `User`).
    *   Бизнес-правила внутри методов (`order->addLine()`, `money->add()`).
    *   Валидацию состояния (например, «Заказ не может быть отменен, если он уже доставлен»).
    *   Сложные алгоритмы расчётов.
*   **Что мокаем:**
    *   **ВСЕ внешние зависимости.** Если метод принимает `PaymentGatewayInterface` — мы мокаем его.
    *   Никогда не подключаем БД, не отправляем HTTP-запросы.
*   **Инструменты:** PHPUnit или Pest.

```php
// Пример: Тест доменной сущности
test('order cannot be completed if not paid', function () {
    $order = Order::create(new OrderId(1));
    
    expect(fn() => $order->complete())
        ->toThrow(OrderStateException::class, 'Order is not paid');
});
```

#### 🔵 Уровень 2: Integration-тесты (Ствол)
**Цель:** Проверить, что компоненты (Слой Application, Инфраструктура, БД) работают вместе.
**Объём:** 20-30% всех тестов.
**Скорость:** Секунды (зависит от БД).

*   **Что тестируем:**
    *   **Repository:** Работают ли запросы к БД? Сохраняется ли объект? Возвращаются ли правильные данные?
    *   **Application Handlers:** Выполняется ли сценарий целиком? (Загрузка -> Логика -> Сохранение -> Событие).
    *   **Voters (Security):** Проверяются ли права доступа?
    *   **Связь с БД:** Транзакции, каскадные удаления, индексы.
*   **Что мокаем:**
    *   **НЕТ БД.** Мы используем реальную PostgreSQL (или H2/SQLite для быстрых проверок, но лучше Postgres).
    *   **ВНЕШНИЕ АПИ:** Stripe, Email-сервисы, Search Engine. Мы мокаем их на уровне Infrastructure (через `HttpClientMock` или кастомные транспортные механизмы).
*   **Инструменты:** `Symfony\Bundle\FrameworkBundle\Test\KernelTestCase`, `DatabaseTrait`.

```php
// Пример: Интеграционный тест репозитория
class DoctrineOrderRepositoryTest extends BaseDatabaseTest 
{
    public function test_it_saves_order_and_lines(): void
    {
        $order = Order::create(...);
        $order->addLine($product, 2);

        $this->repo->save($order);

        // Проверяем БД напрямую или через find
        $found = $this->repo->find($order->getId());
        
        self::assertCount(1, $found->getLines());
        self::assertEquals(2, $found->getLines()[0]->getQuantity());
    }
}
```

#### 🔴 Уровень 3: E2E / Feature-тесты (Виньетка)
**Цель:** Проверить критические сценарии пользователя от начала до конца.
**Объём:** 10-15% всех тестов.
**Скорость:** Десятки секунд.

*   **Что тестируем:**
    *   Полный поток: «Регистрация -> Вход -> Добавление в корзину -> Оплата -> Подтверждение».
    *   Интерфейс (если есть Web-UI): `Cypress` или `Selenium`.
    *   API Contract: `Pest`/`PHPUnit` + `HttpKernelBrowser`.
*   **Что мокаем:**
    *   Только самые дорогие внешние сервисы (например, платёжный шлюз заменяется тестовым режимом или моком).
    *   Всё остальное (БД, Очередь, Кэш) работает в реальном режиме.

---

### 2. Что мокаем, а что нет? (Принцип «Низкий уровень»)

**Золотое правило:** Мокай только то, что находится **за границей твоего модуля** или то, что **медленное/нестабильное**.

| Компонент | Мокаем? | Почему? |
| :--- | :--- | :--- |
| **БД (PostgreSQL)** | **НЕТ** (в Integration/E2E) | БД — это часть системы. Если запрос сломан, тест должен упасть. |
| **БД (в Unit-тестах)** | **ДА** | В Unit-тесте мы проверяем логику, а не SQL. |
| **External API (Stripe)** | **ДА** | Медленный, нестабильный, требует ключей, может менять статус. |
| **Email/SMS Gateway** | **ДА** | Не хотим засорять почту коллег, нет гарантии доставки. |
| **Time/Date** | **ДА** | Логика зависит от «сегодняшнего дня». Мокаем `ClockInterface`. |
| **Random/ID Generator** | **ДА** | Для детерминированных тестов. |
| **Internal Services** | **НЕТ** (если они быстрые) | Если `TaxCalculator` — чистая функция, зовите её реальную. |

**Лайфхак для Symfony:**
Используйте `Symfony\Component\HttpClient\MockHttpClient` для мока API. Он позволяет контролировать ответы и задержки.

---

### 3. Как сделать тесты быстрыми и стабильными?

#### 1. Изоляция данных (Транзакции + Rollback)
Для Integration-тестов с БД используйте трейт `DatabaseTrait` из Symfony или кастомный подход:
1.  Начать транзакцию (`$em->beginTransaction()`).
2.  Выполнить тест.
3.  Откатить транзакцию (`$em->rollBack()`).
*   **Результат:** База данных возвращается в состояние «до теста». Тесты не зависят друг от друга (Order Independent).
*   **Важно:** Это работает только для однопоточных тестов. Если запускаете в параллели (`Paratest`), используйте **Separate Databases per Process** или `transactional` режим с осторожностью.

#### 2. Параллельное выполнение
Один из самых мощных ускорителей — **Paratest**.
*   Запускайте тесты в нескольких процессах.
*   Для Unit-тестов: идеально.
*   Для Integration-тестов: каждый процесс получает свою копию БД (через `CREATE DATABASE template_test` или Docker-композицию с несколькими инстансами Postgres).

#### 3. Изоляция окружения
*   **Очередь (Messenger):** В тестах используйте транспорт `in-memory` или `doctrine` (если нужна проверка персиста). Для быстрых проверок — `in-memory`.
*   **Кэш (Redis/Memcached):** Используйте `array` или `null` кэш.
*   **Файловая система:** Используйте `vfsStream` (виртуальную файловую систему), чтобы не писать файлы в реальный диск.

#### 4. Детерминизм
*   **Время:** Никогда не используйте `new \DateTime()` в коде, если логика зависит от времени. Внедряйте `ClockInterface`. В тестах передавайте фиксированную дату.
*   **Случайность:** Если есть `random_int()` или `uuid()`, внедрите генератор как зависимость, чтобы в тестах передавать фиксированный seed или ID.

#### 5. Чистота кода (Refactoring for Testability)
*   **Тонкие контроллеры:** Контроллеры должны только читать вход, вызывать сервис и писать выход. Логика — в Application/Domain.
*   **Чистые функции:** Избегайте глобального состояния. Все зависимости через конструктор.
*   **Дробные методы:** Если тест сложный, вынесите логику в отдельный метод и тестируйте её отдельно.

---

### 4. Пример структуры тестов

```text
tests/
├── Unit/
│   ├── Domain/
│   │   ├── OrderTest.php          # Тестирование инвариантов Order
│   │   └── MoneyTest.php          # Тестирование арифметики Money
│   └── Application/
│       └── CreateOrderHandlerTest.php # Мокируем Repo и EventDispatcher
│
├── Integration/
│   ├── Repository/
│   │   └── OrderRepositoryTest.php # Реальная БД (Postgres)
│   ├── Service/
│   │   └── PaymentServiceTest.php  # Мокаем Stripe API
│   └── Security/
│       └── OrderVoterTest.php      # Проверка прав доступа
│
└── Feature/
    └── CheckoutFlowTest.php        # Полный сценарий (API + БД)
```

### 5. Итоговый чек-лист для скорости

1.  **Есть ли у Unit-тестов зависимости от БД?** -> Уберите.
2.  **Мокаем ли мы внутренние сервисы, которые можно запустить реально?** -> Запускайте реально (быстрее и надежнее).
3.  **Используем ли мы `sleep()` в тестах?** -> Замените на ожидание событий или мокайте время.
4.  **Запускаем ли мы тесты последовательно?** -> Подключите Paratest.
5.  **Очищаем ли мы БД перед каждым тестом?** -> Нет, откатываем транзакцию.

Такая стратегия позволяет держать время прогона тестов в пределах **5-15 минут** даже для большого приложения, при этом обеспечивая высокую покрытость критической бизнес-логики.

> ⏱ 36.8s · prompt 50316 tok · output 2434 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Это классическая проблема **конкурентности (Race Conditions)**. Когда два процесса (или потока) одновременно читают, изменяют и записывают одну и ту же строку, результат зависит от того, кто пишет последним.

В PostgreSQL (MVCC) это не приводит к блокировкам при чтении, но при записи возникает конфликт.

### 1. Типовые проблемы

1.  **Lost Update (Потерянное обновление):**
    *   Пользователь A читает баланс `100`.
    *   Пользователь B читает баланс `100`.
    *   A вычитает `50` и пишет `50`.
    *   B вычитает `30` и пишет `70`.
    *   **Итог:** `70`. Операция A (`-50`) потеряна. Должно быть `20`.

2.  **Нарушение бизнес-инвариантов:**
    *   Два процесса одновременно бронирование последнего места на рейсе.
    *   Оба видят `seats_available = 1`.
    *   Оба пишут `seats_available = 0`.
    *   **Итог:** Продано 2 билета на 1 место.

3.  **Несогласованное состояние (Inconsistency):**
    *   Если изменение затрагивает несколько сущностей (например, `Order` и `Inventory`), и один процесс доходит до `flush()`, а другой — нет, состояние системы может стать хаотичным, если транзакции не атомарны.

---

### 2. Способы защиты

#### Способ 1: Оптимистичная блокировка (Optimistic Locking)
*«Мы предполагаем, что конфликтов не будет. Если будут — сообщим об этом».*

**Механизм:**
1.  В таблице есть колонка `version` (int).
2.  При чтении мы запоминаем текущее значение `version` (например, `5`).
3.  При обновлении SQL-запрос содержит условие: `UPDATE ... SET version = 6 WHERE id = X AND version = 5`.
4.  Если между чтением и записью кто-то другой уже изменил строку (`version` стал `6`), условие `WHERE` не сработает.
5.  В результате `affected rows = 0`. ORM (Doctrine) бросает `OptimisticLockException`.

**Реализация в Doctrine:**
```php
#[ORM\Version]
private int $version;
```

**Trade-offs:**
*   ✅ **Высокая пропускная способность:** Нет блокировок при чтении. Идеально для систем с высокой нагрузкой, где конфликты редки (например, пользователь меняет свой профиль).
*   ✅ **Простота:** Минимум кода.
*   ❌ **Ошибочность:** При конфликте операция прерывается. Нужно писать логику повторной попытки (retry) или показа ошибки пользователю.
*   ❌ **Не подходит для «долгого» сценария:** Если пользователь заполняет форму 5 минут, за это время данные могли измениться. Сохранение упадет.

**Когда выбирать:**
*   Личные кабинеты (профиль, настройки).
*   Корзины товаров (если корзина привязана к сессии, а не к БД).
*   Ситуации, где конфликт — это редкость.

---

#### Способ 2: Пессимистичная блокировка (Pessimistic Locking)
*«Мы предполагаем, что конфликт возможен. Забираем строку и никому не даем, пока не закончим».*

**Механизм:**
1.  Процесс A выполняет `SELECT ... FOR UPDATE`.
2.  PostgreSQL ставит **эксклюзивную блокировку (Exclusive Lock)** на строку.
3.  Процесс B пытается выполнить `SELECT ... FOR UPDATE` на ту же строку — он **ждет** (блокируется), пока Процесс A не сделает `COMMIT` или `ROLLBACK`.
4.  Процесс A меняет данные и коммитит.
5.  Процесс B освобождается, читает **уже измененные** данные и продолжает.

**Реализация в Doctrine:**
```php
use Doctrine\ORM\LockMode;

$wallet = $this->em
    ->getRepository(Wallet::class)
    ->find($id, LockMode::PESSIMISTIC_WRITE);
```

**Trade-offs:**
*   ✅ **Предсказуемость:** Гарантированно нет потери данных. Операции выполняются строго последовательно.
*   ✅ **Подходит для критичных ресурсов:** Баланс, запасы на складе, уникальные номера.
*   ❌ **Низкая пропускная способность:** Все, кто пытается изменить эту строку, встают в очередь.
*   ❌ **Риск Deadlock:** Если Процесс A блокирует строку 1 и ждет строку 2, а Процесс B блокирует строку 2 и ждет строку 1 — они оба зависнут.
*   ❌ **Долгие транзакции убивают систему:** Если после `FOR UPDATE` вы делаете медленный вызов внешнего API, все остальные будут ждать.

**Когда выбирать:**
*   Финансовые транзакции (перевод денег).
*   Резервирование ограниченных ресурсов (места в кино, билеты).
*   Генерация уникальных серийных номеров.

---

#### Способ 3: Атомарные обновления (Atomic Updates)
*«Не читай-модифицируй-пиши, а сделай все одним SQL-запросом».*

**Механизм:**
Вместо того чтобы загружать сущность в PHP, изменять поле и сохранять, вы используете SQL-выражения, которые PostgreSQL выполняет атомарно.

```sql
-- Вычет из баланса
UPDATE wallets 
SET balance = balance - 50 
WHERE id = 1 AND balance >= 50; 
-- Если баланс меньше 50, affected rows = 0
```

**Реализация в Doctrine (Query Builder):**
```php
$qb = $this->em->createQueryBuilder();
$qb->update(Wallet::class, 'w')
   ->set('w.balance', 'w.balance - :amount')
   ->where('w.id = :id')
   ->andWhere('w.balance >= :amount')
   ->setParameter('id', $walletId)
   ->setParameter('amount', 50);

$result = $qb->getQuery()->getResult(); // или execute()

if (empty($result)) {
    throw new InsufficientFundsException();
}
```

**Trade-offs:**
*   ✅ **Максимальная эффективность:** Один запрос к БД, никаких блокировок на PHP-уровне.
*   ✅ **Гарантия целостности:** Проверка `balance >= 50` происходит внутри БД, атомарно.
*   ❌ **Обход ORM:** Вы теряете магию Doctrine (ленивая загрузка, события). Нужно явно управлять транзакцией, если есть побочные эффекты.
*   ❌ **Сложность при сложной логике:** Если нужно проверить 5 условий из разных таблиц, SQL-запрос становится огромным.

**Когда выбирать:**
*   Простые арифметические операции (списания, начисления).
*   Высоконагруженные горячие пути.

---

#### Способ 4: Уникальные индексы (Unique Constraints)
*«Пусть БД не даст сохранить дубликаты».*

**Механизм:**
Если два процесса пытаются создать запись с одним и тем же бизнес-ключом (например, `email` или `idempotency_key`), один из них упадет с ошибкой `UniqueConstraintViolationException`.

**Применение:**
*   Для защиты от двойных отправок (Idempotency Keys в API).
*   Для защиты от дублей в очередях.

**Trade-offs:**
*   ✅ **Надежность:** Железная защита на уровне БД.
*   ❌ **Не решает проблему «потери обновления»:** Он решает проблему «дублирования».
*   ❌ **Обработка ошибки:** Нужно ловить исключение и показывать пользователю «Пользователь уже существует».

---

### 3. Сравнительная таблица

| Критерий | Optimistic | Pessimistic | Atomic Update | Unique Index |
| :--- | :--- | :--- | :--- | :--- |
| **Производительность (Read)** | Высокая | Средняя (если много writers) | Высокая | Высокая |
| **Производительность (Write)** | Высокая (если конфликтов мало) | Низкая (очередь) | Очень высокая | Средняя |
| **Сложность кода** | Низкая | Низкая | Средняя | Низкая |
| **Обработка ошибок** | Retry / User Error | Нет ошибок (ждут) | Проверка `affected rows` | Catch Exception |
| **Риск Deadlock** | Нет | Да | Нет | Нет |
| **Подходит для** | Профили, настройки | Баланс, инвентарь | Счетчики, списания | Уникальные ключи |

---

### 4. Стратегия выбора (Мой подход)

1.  **Анализ сценария:**
    *   *Что изменять?* (Баланс? Профиль? Заказы?)
    *   *Насколько часты конфликты?* (1 из 1000 операций или 1 из 10?)
    *   *Насколько критична потеря данных?*

2.  **Выбор инструмента:**

    *   **Если это пользовательские данные (профиль, настройки):**
        *   → **Optimistic Locking**. Конфликты редки. Если случатся — пусть пользователь сохранит форму заново.

    *   **Если это деньги или ограниченный ресурс (баланс, места):**
        *   → **Atomic Update** (если логика простая: `balance = balance - X`).
        *   → **Pessimistic Locking** (если логика сложная: нужно проверить 5 правил, вызвать внешний API, и только потом списать).

    *   **Если это создание сущности:**
        *   → **Unique Index** на бизнес-ключе. Ловите `UniqueConstraintViolationException`.

    *   **Если это очередь задач:**
        *   → **Pessimistic Locking with `SKIP LOCKED`**: `SELECT ... FOR UPDATE SKIP LOCKED`. Позволяет воркерам брать разные задачи без ожидания друг друга.

3.  **Комбинирование:**
    Часто используют **связку**:
    1.  **Optimistic** для защиты от случайных конфликтов.
    2.  **Unique Index** для гарантии уникальности.
    3.  **Atomic Update** для критических списаний.

### Итог

Нет «лучшего» метода. Есть **правильный для конкретного контекста**.
*   **Optimistic** — для «мягких» конфликтов.
*   **Pessimistic** — для «жестких» ресурсов.
*   **Atomic** — для максимальной скорости простых операций.

Всегда начинайте с **Optimistic**, и переходите на **Pessimistic** только тогда, когда статистика покажет высокий процент конфликтов и стоимость ретраев становится выше стоимости блокировок.

> ⏱ 39.3s · prompt 52798 tok · output 2581 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой git-процесс построен на балансе между **скоростью доставки** (Agile) и **предсказуемостью стабильности** (Engineering Discipline). Я использую адаптированную модель **GitFlow** или **Trunk-Based Development** (в зависимости от зрелости команды и системы).

Для типичного Symfony-проекта среднего размера я выберу гибрид: **main** (стабильный) + **feature** ветки + **PR** + **Automated Testing**.

Вот полный цикл.

---

### 1. Структура веток (Branching Strategy)

Я придерживаюсь простоты. Сложные схемы (GitFlow с `develop`, `release`, `hotfix`) часто усложняют жизнь при быстрой итерации.

| Ветка | Назначение | Жизненный цикл | Кто создает |
| :--- | :--- | :--- | :--- |
| **`main`** (или `master`) | Всегда готово к деплою в прод. Тегировано. | Бессрочно. | CI/CD Pipeline (авто-merge) |
| **`feature/JIRA-123-desc`** | Разработка новой фичи или фикс бага. | Пока фича не готова к ревью. | Разработчик |
| **`hotfix/JIRA-456-desc`** | Критический багфикс, который нужно внести в прод *сразу*, минуя другие фичи. | До мержа в `main` и бекпорта в `release` (если есть). | Разработчик/Tech Lead |

**Почему так?**
*   **Нет ветки `develop`:** В небольших командах `develop` часто становится «свалкой», где код долго живёт без тестирования. Прямой мерж в `main` (через PR) заставляет каждый коммит быть более законченным и тестируемым.
*   **Префикс `JIRA-123`:** Связывает код с задачей. Легко найти PR по тикету и наоборот.

---

### 2. Формат коммитов (Commit Message Convention)

Я использую **Conventional Commits**. Это не просто «красиво», это позволяет автоматизировать релизы (semantic versioning).

**Формат:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Примеры:**
*   `feat(order): add discount calculation logic`
*   `fix(payment): correct currency rounding error in invoice`
*   `refactor(repo): split large OrderRepository for SRP`
*   `docs: update setup instructions for Docker`

**Типы (`type`):**
*   `feat`: Новая функциональность.
*   `fix`: Исправление бага.
*   `refactor`: Изменение кода, не влияющее на внешний интерфейс/поведение.
*   `docs`: Изменения только в документации.
*   `style`: Форматирование, точки с запятой (не меняет логику).
*   `perf`: Улучшение производительности.
*   `test`: Добавление тестов.
*   `chore`: Обновление зависимостей, конфигурации.

**Почему так?**
1.  **Автоматизация CHANGELOG:** Инструменты (как `standard-version` или `release-please`) генерируют `CHANGELOG.md` на основе коммитов.
2.  **Семантическое версионирование:** `feat` -> минорная версия, `fix` -> патч-версия. Это критично для библиотек и API.
3.  **Читаемость `git log --oneline`:** Можно быстро понять, что произошло в проекте, не открывая каждый PR.

**Правило:** Один коммит = одна логическая мысль. Не смешивайте `feat` и `refactor` в одном коммите.

---

### 3. Pull Request (PR) Process

PR — это не только про код. Это **контракт** между автором и ревьюером.

#### Чек-лист для автора PR:
1.  **Краткое описание:**
    *   **Что:** Какой тикет закрывается?
    *   **Почему:** Какую бизнес-задачу решает?
    *   **Как:** Кратко описан подход (если неточный).
2.  **Проверки:**
    *   [ ] Локально все тесты проходят.
    *   [ ] Код отформатирован (`php-cs-fixer` / `Prettier`).
    *   [ ] Статический анализ чистый (`PHPStan` / `Rector`).
    *   [ ] Добавлены/обновлены тесты.
3.  **Сcreenshots/Gif:** Если затрагивает UI.
4.  **Миграции:** Если менялась схема БД, упомянуто.

#### Правила мержа (Merge Strategy):
*   **Prefer Squash and Merge:** При мерже в `main` все коммиты фичи схлопываются в **один** чистый коммит с хорошим описанием (из заголовка PR).
    *   *Почему:* История `main` остаётся линейной и чистой. Нет мусора типа «fix typo», «wip», «try again».
*   **Merge Commit:** Используйте, только если нужно сохранить полную историю ветки (редко нужно в веб-приложениях, чаще в ядрах библиотек).
*   **Rebase:** Используйте осторожно. Перезапись истории после того, как PR открыт и ревьюеры начали его читать — плохая практика.

---

### 4. Code Review (Кодовое ревью)

Ревью — это не поиск опечаток. Это проверка **дизайна** и **намерений**.

**На что я смотрю как ревьюер:**
1.  **SRP & Clean Architecture:** Не нарушены ли границы слоёв? (Вспоминаем предыдущий вопрос).
2.  **Исключения и ошибки:** Обрабатываются ли ошибки? Не «проглатываются» ли они?
3.  **Безопасность:** SQL-injection, XSS, Mass Assignment (через Form/DTO).
4.  **Производительность:** N+1 запросы, лишние вычисления в циклах.
5.  **Тесты:** Покрыты ли граничные случаи (edge cases)?

**Культура ревью:**
*   **Конструктивная критика:** Не «Ты написал плохо», а «Здесь может быть баг, потому что...».
*   **Время:** Ответ на PR — в течение 24 часов. Если ревьюер занят — делегирует или просит помощи.
*   **Обязательный второй глаз:** Мерж в `main` без ревью (кроме `hotfix` с согласованием TL) запрещён.

---

### 5. Автоматизация (CI/CD)

Чтобы ревьюеры не тратили время на «а почему тесты красные?», все проверки должны быть автоматическими.

**Пайплайн (например, GitHub Actions/GitLab CI):**

1.  **Trigger:** Открывается PR.
2.  **Static Analysis:**
    *   `composer install`
    *   `vendor/bin/phpstan analyse` (строгий уровень)
    *   `vendor/bin/php-cs-fixer fix --dry-run` (проверка стиля)
3.  **Testing:**
    *   Unit tests (быстрые, без БД).
    *   Integration tests (с PostgreSQL в Docker-контейнере).
    *   *Опционально:* Frontend tests (если есть).
4.  **Build:**
    *   Сборка фронтенда (webpack/vite).
    *   Сборка Docker-образа приложения.
5.  **Deployment (Auto-deploy on Main):**
    *   Если всё зелёное, мерж в `main`.
    *   CI берёт код из `main`, деплоит в **Staging**.
    *   *Ручной шаг:* QA тестирует в Staging.
    *   *Ручной шаг:* TL одобряет деплой в **Production**.

**Почему Staging?**
Продакшен — не место для экспериментов. Staging должен быть зеркалом продакшена (та же версия PG, Redis, окружение).

---

### 6. Релизы и Hotfixes

#### Плановый релиз (Feature Release)
1.  Фичи разрабатываются и мержатся в `main`.
2.  На `main` тегруется версия, например, `v1.4.0`.
3.  CI/CD смотрит на тег и деплоит в прод.
4.  `CHANGELOG.md` генерируется автоматически.

#### Hotfix (Горячая починка)
1.  Создаём ветку `hotfix/urgent-fix` из `main`.
2.  Чиним баг.
3.  Мержим в `main`.
4.  Тегим `v1.4.1`.
5.  Деплоим в прод.
6.  *(Если есть ветка `release/1.3`) Бекпортируем фикс туда, чтобы старые клиенты тоже получили патч.*

---

### 7. Почему именно так? (Итоговая философия)

1.  **Маленькие PR:** Если PR больше 400-500 строк (diff), я требую его разбить. Маленькие PR легче ревьюить, легче откатывать, легче понимать.
2.  **Автоматизация доверия:** Если CI зелёный, я доверяю, что код не ломает базовые сценарии. Ревьюеры сосредотачиваются на архитектуре, а не на синтаксисе.
3.  **Скорость через предсказуемость:** Когда все знают, как называть ветки, как писать коммиты и как проходит ревью, трение между разработчиками снижается.
4.  **Чистая история `main`:** Благотворное Squash-merge позволяет в будущем легко искать, когда была добавлена фича, и откатывать её одним коммитом, если она вызывает проблемы.

### Типовые антипаттерны, которых я избегаю:

*   **❌ Мерж без тестов:** Тесты — не опция.
*   **❌ «Быстрая фиксация» в проде:** Всегда через ветку `hotfix`.
*   **❌ Один гигантский PR на 3 месяца:** Это бомба замедленного действия для ревью.
*   **❌ Ручное деплоирование:** «Захожу в SSH и копирую файлы» — это путь к катастрофе. Только CI/CD.

Этот процесс позволяет держать код чистым, команду синхронизированной, а релизы — предсказуемыми.

> ⏱ 38.6s · prompt 55438 tok · output 2510 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью — это не поиск опечаток (это работа линтера) и не демонстрация эго. Это **совместный процесс улучшения продукта**. Моя цель как ревьюера — убедиться, что код решает задачу правильно, безопасно и предсказуемо, а как автора — чтобы его намерения были понятны.

Вот мой подход, разбитый на ключевые аспекты.

---

### 1. Маркировка замечаний (Severity Levels)

Чтобы автор PR понимал приоритеты, я разделяю комментарии на три уровня. Это помогает избежать ситуации, когда автор игнорирует «мелочи», которые на самом деле критичны, или тратит час на спор о точке с запятой, пропуска критичный баг.

#### 🔴 **Blocking / Critical (Критическое)**
*   **Что это:** Баг, ошибка безопасности, нарушение бизнес-логики, сломанные тесты, нарушение архитектуры (Dependency Rule).
*   **Формулировка:** «Данный момент должен быть исправлен перед мержем».
*   **Пример:**
    > `@author` Здесь мы сохраняем заказ до проверки наличия товара на складе. Если товара нет, мы получим 500 ошибку после транзакции. Предлагаю сначала валидацию через `InventoryService`.

#### 🟡 **Improvement / Suggestion (Рекомендация)**
*   **Что это:** Код работает, но его можно сделать чище, быстрее или понятнее. Частичное нарушение SRP/Dry.
*   **Формулировка:** «Считаю, что так будет лучше. Можете обсудить или оставить как есть, если есть веская причина».
*   **Пример:**
    > `@author` Этот метод стал очень длинным (50 строк). Возможно, стоит вынести логику расчёта налогов в отдельный `TaxCalculator`? Это улучшит читаемость.

#### 🟢 **Nit / Nitpick (Деталь)**
*   **Что это:** Стиль именования, форматирование, предпочтение личного вкуса.
*   **Формулировка:** Предложено в начале комментария тег `[nit]`, чтобы автор мог пропустить.
*   **Пример:**
    > `[nit]` Я бы назвал переменную `maxRetries` вместо `count`, чтобы было сразу понятно, что это ограничитель попыток.

---

### 2. На что смотрю в первую очередь (Priority List)

Когда я открываю PR, я не читаю код построчно сверху вниз. Я использую «метод воронки»:

1.  **Зачем этот код? (Context)**
    *   Читаю описание PR.
    *   Связан ли он с тикетом в Jira/Trello?
    *   Есть ли ссылка на макет или спецификацию?
    *   *Если контекст неясен — запрашиваю уточнение.*

2.  **Архитектура и Границы (High-Level)**
    *   Нарушены ли слои? (Например, `Entity` знает про `Symfony Form`).
    *   Есть ли новые зависимости, которые не нужны?
    *   Соответствует ли структура существующему паттерну проекта?

3.  **Логика и Корректность (Logic)**
    *   Работает ли код так, как заявлено?
    *   Обрабатываются ли граничные случаи (`null`, пустые массивы, отрицательные числа)?
    *   Что будет, если внешняя зависимость упадет?

4.  **Безопасность (Security)**
    *   Уязвимость к SQL-инъекции, XSS?
    *   Авторизация/Аутентификация проверена для этого эндпоинта?
    *   Есть ли Mass Assignment (запись полей, которые пользователь не должен менять)?

5.  **Тесты (Testing)**
    *   Покрыты ли новые пути?
    *   Тесты тестируют *поведение*, а не реализацию?
    *   Проходят ли тесты локально (я проверяю статус CI)?

6.  **Производительность (Performance)**
    *   N+1 запросы?
    *   Тяжелые вычисления в циклах?
    *   Лишние объекты в памяти?

7.  **Читаемость и Стиль (Readability)** — *Последним*
    *   Имена понятны?
    *   Код интуитивно читаем?
    *   *Здесь я часто молчу, если линтер (PHP-CS-Fixer) должен был это починить.*

---

### 3. Как формулирую комментарии

Я стараюсь быть **объективным** и **конструктивным**.

**Плохо:**
> «Ты опять использовал `eval()`, это же плохо».
> «Почему ты не использовал SOLID?»

**Хорошо:**
> `@author` Использование `eval()` может привести к выполнению произвольного кода, если в данные попадет мусорный ввод. Предлагаю заменить на парсер JSON или маппинг через массив.
>
> `@author` Этот класс отвечает и за валидацию, и за отправку email, и за сохранение в БД (нарушение SRP). Если завтра изменится логика email, придется трогать этот метод. Возможно, стоит разбить на два класса?

**Формула хорошего комментария:**
1.  **Наблюдение:** Что я вижу?
2.  **Риск/Проблема:** Почему это может быть плохо?
3.  **Решение:** Как я бы сделал? (Опционально)

---

### 4. Что делать при разногласии (Disagreement)

Разногласия — это нормально. Важно, чтобы они не превращались в спор «кто прав», а в обсуждение «что лучше для проекта».

#### Шаг 1: Уточнить намерение
> `@author` Я вижу, что ты хранишь все данные в одной таблице. Я бы разбил их, чтобы изолировать инварианты. Но, возможно, я не понимаю контекста — почему ты выбрал такой подход? Есть ли ограничения по времени или производительности?

Часто автор объясняет, что «так проще сделать к дедлайну», и вы оба соглашаетесь, что для MVP это приемлемо.

#### Шаг 2: Обосновать свою позицию фактами
Не «мне так нравится», а:
> «В прошлый раз, когда мы хранили это вместе, мы столкнулись с проблемой гонки при обновлении. Чтобы этого избежать, я предлагаю...»

#### Шаг 3: Компромисс или Escalation
*   Если есть веские доводы с обеих сторон — ищем третий путь.
*   Если аргументы исчерпаны, а мнения диаметрально противоположны — обращаемся к **Tech Lead** или **Team Lead**.
*   **Правило:** Решение принимает лидер команды, но автору кода дано слово. После принятия решения автор implements решение, даже если он с ним не полностью согласен (или мы оставляем его вариант, если лидер принял его).

**Важно:** Никогда не пишите «Это неправильно». Лучше: «Я боюсь, что это может привести к [последствие]».

---

### 5. Мой чек-лист перед тем, как отправить ревью

1.  Я прочитал описание PR.
2.  Я запустил тесты локально (или вижу, что они зелёные в CI).
3.  Я проверил, что нет новых уязвимостей в зависимостях (Composer/Audit).
4.  Я оставил хотя бы один позитивный комментарий (комплимент за хорошую идею или чистый код). Это важно для психологического климата!
5.  Я помечил комментарии по уровню важности.
6.  Я ответил на вопросы автора (если были).

### Итог

Хорошее ревью — это **мост** между «кодом, который написали» и «кодом, который мы хотим иметь».
*   Оно **быстрое** (не задерживает релиз без веской причины).
*   Оно **полезное** (обучает автора).
*   Оно **честное** (не прячет проблем за молчанием).

Если ревью проходит болезненно или долго — значит, либо код слишком сложный (нужно разбить PR), либо коммуникация в команде хромает (нужно обсудить стандарты).

> ⏱ 30.2s · prompt 57993 tok · output 1936 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Чтобы превратить размытую боль бизнеса в техническую задачу, которую можно оценить, спланировать и написать под неё тесты, нужно провести перевод с «человеческого» на «системный».

Вот как я сформулирую эту задачу для исполнителя (Senior PHP Developer).

---

### 📄 Task: Optimization of Order Search and UX for Historical Orders

**Priority:** High
**Estimate:** [M/D] (оценивается после анализа текущей реализации)
**Related Tickets:** [Ссылка на ticket поддержки, если есть]

#### 1. Context (Контекст)
Пользователи не могут быстро найти свои исторические заказы. Это приводит к росту обращений в поддержку (примеры: [ссылки на 2-3 тикета]). Текущая реализация поиска/фильтрации по списку заказов в ЛК работает медленно/неудобно/не находит нужное.

**Цель:** Снизить количество обращений в поддержку по теме «Где мой заказ?» на 50% в течение месяца после релиза.

#### 2. Acceptance Criteria (Критерии приемки)

**A. Функциональность (Feature):**
1.  **Расширенный поиск:** В разделе «Мои заказы» реализовать поиск по:
    *   Номеру заказа (полный или частичный, например, `ORD-123`).
    *   Товару (по названию или артикулу).
    *   Диапазону дат (с даты / по дату).
    *   Статусу заказа (например, «Оплачено», «Доставлено», «Возврат»).
2.  **Сортировка:** Убедиться, что по умолчанию заказы отсортированы по дате создания (новые сверху). Добавить явную кнопку сортировки по дате/стоимости, если её нет.
3.  **Пагинация/Бесконечная прокрутка:** Список должен корректно обрабатывать большое количество заказов (100+). При использовании SQL `OFFSET` проверить производительность (см. техническое задание ниже).

**B. Non-functional Requirements (НФР):**
1.  **Производительность:** Время отклика API/страницы при поиске по 1000+ заказам пользователя должно быть < 200ms (p95).
2.  **UX:** Поле поиска должно автофокусироваться при открытии фильтра. Ошибка поиска (ничего не найдено) должна показывать понятное сообщение с кнопкой «Сбросить фильтры».

**C. Analytics (Метрики):**
1.  Добавить события в метрику (Sentry/Amplitude/Mixpanel):
    *   `order_search_initiated` (пользователь начал искать).
    *   `order_search_result_found` (найден хотя бы 1 результат).
    *   `order_search_no_results` (результат пуст).

#### 3. Technical Constraints & Hints (Технические ограничения)

*   **База данных:** PostgreSQL.
*   **ORM:** Doctrine.
*   **Важно:** Избегайте N+1 проблем при загрузке списка заказов. Используйте `JOIN FETCH` или DTO-проекции.
*   **Поиск по товарам:** Товары хранятся в связанной таблице `order_lines`. Если поиск по названию товара выполняется медленно, рассмотрите использование **PostgreSQL Full-Text Search** (tsvector) или **GIN-индекса**, если это не реализовано.
*   **Бэкенд:** Symfony. Обновите `QueryBuilder` в `OrderRepository`.
*   **Фронтенд:** [Vue/React/Twig] — добавьте поля в форму фильтра.

#### 4. Definition of Done (Что входит в «Готово»?)

1.  Код отформатирован (`php-cs-fixer`).
2.  Статический анализ (`phpstan`) без ошибок.
3.  **Unit-тесты** для новой логики валидации поиска (если есть).
4.  **Integration-тесты** для репозитория:
    *   Тест: поиск по номеру заказа работает.
    *   Тест: поиск по товару работает.
    *   Тест: поиск по дате работает.
    *   Тест: комбинация фильтров работает.
5.  Обновлена документация API (Swagger/OpenAPI), если изменилась сигнатура эндпоинта.
6.  Ревью пройдено.

---

### Почему я включил именно эти пункты?

#### 1. Измеримая цель (Context + Metrics)
*   **Проблема:** «Пользователи жалуются» — это субъективно.
*   **Решение:** Я перевел это в метрику («Снизить обращения на 50%») и добавил события аналитики. Это позволит бизнесу объективно оценить успех задачи после релиза, а не по эмоциям.

#### 2. Конкретные критерии приемки (Acceptance Criteria)
*   **Проблема:** Разработчик может сделать поиск только по номеру, а бизнес ожидал поиск по товару.
*   **Решение:** Я явно перечислил, по *чему* искать (номер, товар, даты, статус). Теперь нет места для догадок.

#### 3. Технические подсказки (Technical Hints)
*   **Проблема:** Разработчик может решить задачу самым быстрым способом (например, `LIKE %string%` без индекса), который станет бутылочным горлышком через год.
*   **Решение:** Я указал на потенциальные узкие места (N+1, Full-Text Search). Это не диктатура («ты обязан использовать FT Search»), а подсказка («подумай об этом, если будет медленно»). Это ускоряет разработку, так как разработчик не тратит время на поиск правильного пути сам.

#### 4. Definition of Done (DoD)
*   **Проблема:** Разработчик говорит «Сделал!», но тесты не покрыты новые фильтры.
*   **Решение:** Я чек-листом определил, что считать завершением работы. Если нет тестов — задача не готова. Это снимает споры на этапе ревью.

#### 5. Разделение на Feature и Non-Functional
*   **Проблема:** Часто забывают про производительность. Код работает, но «тормозит».
*   **Решение:** Отдельный блок НФР делает акцент на скорости (< 200ms). Это заставляет разработчика думать об индексах и SQL, а не только о PHP-логике.

---

### Что я НЕ включил (и почему)?

*   **Дизайн:** Я не описывал, как должен выглядеть кнопочный интерфейс, если это стандартные формы. Для этого нужен дизайнер. Если интерфейс уникальный — я бы добавил макет Figma.
*   **Миграции:** Если мы добавляем новые индексы в БД, я бы добавил пункт «Создать миграцию для добавления GIN-индекса на `order_lines.product_name`». Но это техническая деталь, которая вытекает из технического решения.

Такая формулировка позволяет разработчику начать работу сразу, не тратя 2 часа на вопросы «А точно нужен поиск по товару?», а QA — написать тест-план на основе критериев приемки.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Прежде чем открыть задачу в Jira и писать код, я проведу короткую встречу (15–20 минут) с Product Owner (или Lead Developer, если PO занят). Цель — уточнить **контекст**, **ограничения** и **определить границы MVP**.

Вот мой список вопросов, сгруппированный по смыслу:

### 1. Диагностика текущей проблемы (Root Cause)
*Чтобы не лечить симптом, а причину.*

1.  **В каком именно моменте пользователи теряют трек?**
    *   Им не хватает фильтров (не могут найти по товару/дате)?
    *   Или поиск есть, но он не находит то, что нужно (опечатка, разные артикулы)?
    *   Или список настолько длинный, что они просто прокручивают вниз и сдаются?
    *   *Зачем:* Если проблема в длине списка, нам нужна пагинация/бесконечная прокрутка. Если в поиске — нужна оптимизация запросов и расширение критериев.

2.  **Какие данные пользователь пытается ввести в поиск?**
    *   Примеры из тикетов поддержки: они ищут по номеру заказа, по названию товара, по сумме?
    *   *Зачем:* Понять, какие поля являются критичными для поиска.

3.  **Есть ли сейчас какие-то метрики по поддержке?**
    *   Сколько обращений в день/неделю по теме «Где мой заказ»?
    *   Есть ли классификация этих обращений?
    *   *Зачем:* Чтобы после релиза мы могли измерить эффект (Baseline vs Current).

### 2. Технические ограничения и контекст
*Чтобы не изобретать велосипед и не сломать архитектуру.*

4.  **Какой стек фронтенда используется в ЛК?**
    *   React/Vue/Twig? Есть ли готовые UI-киты (Material UI, Ant Design)?
    *   *Зачем:* Понять, как быстро можно сделать удобный интерфейс фильтра (слайдеры дат, выпадающие списки статусов).

5.  **Какова средняя/максимальная глубина истории заказов у активных пользователей?**
    *   10 заказов? 100? 1000?
    *   *Зачем:* Если у большинства 5 заказов — может быть, проблема не в поиске, а в UX (кнопка слишком маленькая). Если у VIP-клиентов 1000+ — обязательно нужно решать проблему производительности SQL.

6.  **Есть ли у нас уже Full-Text Search или Elasticsearch?**
    *   Или только SQL?
    *   *Зачем:* Решим ли мы поиск по товару через `LIKE` (медленно на больших данных), `tsvector` (Postgres) или выносим в поиск (Elasticsearch/Manticore)? Это влияет на оценку сложности.

7.  **Каковы SLA по времени отклика этого экрана?**
    *   Мы ориентируемся на mobile-first? Тогда 200ms — это потолок.
    *   *Зачем:* Поставить техническое ограничение для разработчика.

### 3. Приоритизация и MVP
*Чтобы не делать «идеальный» поиск за 2 месяца, если дедлайн через неделю.*

8.  **Что является критичным для первого релиза (MVP)?**
    *   Вариант А: Только фильтрация по дате и статусу (быстро, просто, закрывает 80% проблем).
    *   Вариант Б: Полноценный поиск по названию товара (сложнее, требует индексов).
    *   *Зачем:* Разделить задачу на части. Если дедлайн жесткий, делаем Вариант А, а Вариант Б — в бэклог.

9.  **Нужен ли поиск по внешним данным (например, номеру накладной из 1С или номеру доставки из СДЭК)?**
    *   *Зачем:* Иногда пользователи ищут не «заказ», а «документ». Если эти ID хранятся в базе — добавим их в поисковую строку.

10. **Есть ли ограничения по лицензиям или бюджету на инфраструктуру?**
    *   Можно ли поднять Elasticsearch/Manticore Search, или мы обязаны оставаться на PostgreSQL?
    *   *Зачем:* Ограничивает технические решения.

### 4. UX и Анализ
11. **Кто будет использовать этот функционал чаще всего?**
    *   Обычные покупатели?
    *   Юрлица (бухгалтеры, которые ищут счета)?
    *   *Зачем:* Юрлица могут искать по диапазону сумм или счетам. Обычные пользователи — по товару. Это разные сценарии.

12. **Что мы покажем, если ничего не найдено?**
    *   Просто «Нет результатов»?
    *   Или подсказки: «Попробуйте изменить период», «Поиск по артикулу»?
    *   *Зачем:* Чтобы разработчик сразу знал, какой UI рисовать в случае пустого ответа.

---

### Итог: Что я выношу из этой встречи?

После ответов я пересматриваю задачу:

1.  **Если проблема в UX:** Задача меняется на «Улучшение интерфейса списка заказов» (Frontend-heavy).
2.  **Если проблема в поиске:** Задача остается «Оптимизация и расширение поиска», но я добавляю техническое ограничение (например, «Использовать PostgreSQL `tsvector`»).
3.  **Если проблема в производительности:** Задача смещается в сторону Backend-оптимизации (индексы, пагинация через Keyset).

**Почему это важно?**
Если я начну писать код, не спросив вопрос №1 (Root Cause), я могу написать отличный поиск по товарам, а окажется, что пользователи просто не видели кнопки «Показать старые заказы», спрятанную под сворачивающимся списком. В таком случае код будет написан зря.

> ⏱ 26.3s · prompt 60006 tok · output 1677 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это две классические ситуации, которые проверяют зрелость разработчика. Главная ошибка в обоих случаях — либо молча проигнорировать проблему, либо бесцеремонно перебивать процесс «спонтанными» предложениями.

Вот мой алгоритм действий для каждого случая.

---

### Случай А: Нашёл попутный баг (не связанный с задачей)

**Принцип:** «Не лети за золотой рыбой, но отметь, что вода мутная».

#### 1. Что делаю?
1.  **Останавливаюсь.** Не начинаю чинить баг прямо сейчас (если он не блокирует текущую задачу).
2.  **Оцениваю критичность:**
    *   *Критичный (Critical/Blocker):* Сайт падает, деньги теряются, данные ломаются.
    *   *Средний (Major):* Функция работает некорректно, но есть обходной путь.
    *   *Низкий (Minor):* Опечатка, стилистика, логгер пишет лишнее.
3.  **Создаю тикет (Bug Report).**
    *   Заполняю шаблон: Шаги воспроизведения, ожидаемое/фактическое поведение, лог ошибок.
    *   Устанавливаю приоритет (на основе шага 2).
    *   **Ключевой момент:** Связываю тикет с текущей задачей или просто оставляю в Backlog.
4.  **Сообщаю PO/Lead-у:** «Я нашел баг [название], который выглядит как [краткое описание]. Я создал тикет [ID]. Он не блокирует текущую задачу, но [краткая оценка критичности]. Предлагаю запланировать его на [ближайший спринт / этот релиз / позже].»

#### 2. Почему так?

*   **Чистота Pull Request (PR):**
    *   Если я исправлю этот баг в *текущем* PR, я нарушу **SRP (Single Responsibility Principle)** на уровне изменений.
    *   Ревьюеру будет сложно отличить, что из-за новой фичи, а что из-за чинения старого бага.
    *   При откате (revert) текущего PR может откатиться и фикс этого «постороннего» бага, что вернет проблемы в прод.
*   **Контроль оценки (Estimation):**
    *   Чинение бага требует времени и тестов. Если добавить это в текущую задачу, ее оценка (story points) вырастет, что исказит скорость команды (velocity) и план спринта.
*   **Прозрачность:**
    *   PO видит «технический долг», который мы обнаруживаем в процессе работы. Это показывает, что команда активна и заботится о качестве, а не просто «отбывает срок».

**Исключение:**
Если баг лежит *в том же файле*, который я правлю, и его исправление занимает < 5 минут (например, опечатка в переменной), я могу исправить его, но **обязательно** упомянуть это в описании PR:
> *«P.S. По пути исправил опечатку в методологии `calculateTax`, которая приводила к неверному округлению (фикс в том же коммите, но логически отдельный баг)».*

---

### Случай Б: Критерий приемки противоречит цели задачи

**Принцип:** «Цель важнее средства. Но решение принимается бизнесом».

#### 1. Что делаю?
1.  **Не начинаю писать код**, пока не утрясу вопрос.
2.  **Формулирую противоречие.**
    *   *Пример:*
        *   *Цель:* «Пользователи должны быстро находить старые заказы».
        *   *Критерий:* «Реализовать поиск только по точному совпадению номера заказа».
    *   *Аргумент:* «Но пользователи часто помнят название товара, а не номер. Точное совпадение по номеру закроет только 10% сценариев. Цель (удобство поиска) не достигается заданным критерием (сложность ввода)».
3.  **Предлагаю альтернативы.**
    *   *Вариант 1:* Расширить критерий до «поиск по номеру ИЛИ названию товара» (сложнее, но достигает цели).
    *   *Вариант 2:* Оставить поиск только по номеру, но добавить автоподсказки (быстрее реализовать).
    *   *Вариант 3:* Упростить цель: «Добавить фильтр по дате» (самое быстрое решение).
4.  **Провожу короткую синк-сессию (15 мин)** с PO и Lead-ом.
    *   Обсуждаем варианты.
    *   Принимается решение: либо меняем критерий приемки в тикете, либо меняем понимание цели.
5.  **Обновляю тикет.**
    *   Если критерий меняется — обновляю Acceptance Criteria в Jira/Trello.
    *   Пишу комментарий: «Уточнили с PO: вместо точного поиска по номеру делаем поиск по товару. Причина: [аргумент из шага 3]».

#### 2. Почему так?

*   **Защита от «Golden Path»:**
    *   Разработчик часто видит «идеальное техническое решение», а бизнес видит «идеальный пользовательский сценарий». Противоречие возникает из-за разницы в фокусе.
    *   Если я просто скажу «Так сделать нельзя», это воспримут как негатив. Если я скажу «Это технически возможно, но не решит проблему X, давайте обсудим Y», это воспримут как экспертизу.
*   **Избегание переделок (Rework):**
    *   Если я напишу код под противоречивый критерий, PO примет его, но через неделю скажет: «Пользователи все равно не находят заказы, потому что им нужен поиск по товару». Придется переделывать. Лучше обсудить до кода.
*   **Уважение к контракту:**
    *   Критерии приемки — это договор. Если один из членов договора (разработчик) видит несоответствие договора с целью (задачей бизнеса), он обязан это заявить, чтобы договор был справедливым.

**Риски, если промолчать:**
*   **Scope Creep:** Вы сделаете «минимум» по критериям, но «максимум» по сложности, потому что критерий был странным.
*   **Недовольство:** Клиент/PO получит функционал, который работает (по критериям), но не решает бизнес-проблему (по цели). Вина ляжет на разработчика: «Почему вы не сказали, что это неудобно?»

---

### Резюме: Чек-лист действий

| Ситуация | Действие | Коммуникация | Результат |
| :--- | :--- | :--- | :--- |
| **Попутный баг** | Создать отдельный тикет. Не чинить в текущем PR (кроме мелочей). | «Найдено, зафиксировал, приоритет [X], не блокирует текущую работу». | Чистый PR, прозрачный Backlog, корректная оценка. |
| **Противоречие цели** | Остановить работу. Провести 15-минутный синк с PO. | «Критерий A не достигает цели B. Предлагаю варианты 1, 2, 3. Какой выбрать?» | Обновленный тикет, согласованное понимание, отсутствие переделок. |

Главное правило: **Не принимайте технические решения за бизнес-логические, и не принимайте бизнес-решения за технические.** Ваша роль — быть мостом и экспертом по последствиям.

> ⏱ 28.9s · prompt 63167 tok · output 1851 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчет о выполнении — это не просто «Done». Это **документирование того, *как* была достигнута цель**, и предоставление доказательств. Для заказчика или лида это гарантия того, что «проходят тесты» — это не самоцель, а средство.

### 1. Структура стандартного отчета (когда все получилось)

Мой отчет (в комментариях Jira, в PR или в мессенджере) состоит из 5 блоков:

#### А. Ссылки и контекст
*   **Ticket:** `[JIRA-123]`
*   **PR/Commit:** `[Ссылка на PR]`
*   **Релиз:** `v1.4.0` (если уже задеплоен) или `staging` (если на тестировании).

#### Б. Что сделано (Summary)
Краткое описание в 3–5 пунктах. Не пересказ кода, а пересказ *реализованных требований*.
*   ✅ Реализован поиск заказов по номеру, товару и дате.
*   ✅ Добавлены фильтры по статусу.
*   ✅ Оптимизирован SQL-запрос (добавлен индекс, убран N+1).
*   ✅ Добавлены интеграционные тесты для сценариев поиска.

#### В. Доказательства (Evidence)
Это самый важный блок. «Слова» не стоят ничего, «доказательства» стоят дорого.
1.  **Скриншоты/Gif:**
    *   Скриншот нового интерфейса фильтра.
    *   Скриншот консоли браузера с сетевым запросом (виден быстрый ответ, `200 OK`).
    *   Скриншот лога Postgres (показывает, что использовался индекс, а не Seq Scan).
2.  **Метрики (если применимо):**
    *   *До:* Время запроса 450ms (p95).
    *   *После:* Время запроса 85ms (p95).
3.  **Тесты:**
    *   Ссылка на CI/CD пайплайн с зелеными галочками.
    *   Краткое описание, какие именно граничные случаи покрыты тестами (например, «поиск по не-existing товару возвращает пустой список, а не ошибку 500»).

#### Г. Технические детали для других разработчиков (Technical Notes)
Здесь я объясняю нюансы, которые важны для future-me или коллег.
*   **БД:** Добавлен миграция `V123__add_index_orders.sql`. Индекс: `idx_orders_user_status_date`.
*   **Апдейты:** Нет. (Или: «Обновил версию Laravel до 9.x из-за бага в пакетном менеджере»).
*   **Ловушки:** «Поиск по товару использует `ILIKE` для регистронезависимости. Если база станет очень большой (>10M строк), может потребоваться переход на Full-Text Search».

#### Д. Остались ли вопросы / Что проверить QA
*   «Проверьте, пожалуйста, сценарий поиска по товару с кириллицей и латиницей (модификация в `collation`)».
*   «Проверьте мобильную версию (UX-элементы могут съезжать)».

---

### 2. Что меняется, если часть задачи сделать не удалось?

В этом случае отчет должен быть **честным и конкретным**. Главное — не скрывать провал, а четко разграничить «Сделано» и «Не сделано».

Структура остается той же, но появляются два новых блока:

#### Блок 1: Что не получилось (The Gap)
Здесь нужно указать:
1.  **Какой именно критерий приемки не выполнен?**
    *   *Пример:* «Критерий: "Поиск по названию товара должен работать за < 200ms". Фактически: 450ms на нагрузке».
2.  **Почему?** (Честная причина, а не отговорка).
    *   *Причина:* «После глубокого анализа выяснилось, что текущая структура БД (нормализация 3NF) не позволяет сделать быстрый поиск без денирмализации или выноса в отдельный поисковый сервис (Elasticsearch). Реализация FTS в рамках текущего спринта невозможна без риска регрессии других модулей».
3.  **Какой план Б (Workaround)?**
    *   «На данный момент поиск работает корректно, но медленнее. Для пользователя это не критично (время ответа 450ms допустимо на desktop). Мы добавили асинхронную загрузку результатов, чтобы интерфейс не зависал».

#### Блок 2: Рекомендации по следующему шагу
1.  **Новый тикет:** Создан тикет `[JIRA-130]` «Оптимизация поиска через Elasticsearch/Redesign DB Schema».
2.  **Оценка:** «Для полной реализации критерия < 200ms требуется 3-5 дней работы (настройка ES, миграция данных, адаптация кода)».
3.  **Риск:** «Если не сделать, через 6 месяцев нагрузка вырастет, и поиск упадет».

---

### Пример отчета с частичным успехом

> **Status:** 🟡 Partially Done
> **Ticket:** `[JIRA-123]`
> **PR:** `#452`
>
> **Сделано:**
> *   ✅ Реализован поиск по номеру заказа и дате.
> *   ✅ Добавлены фильтры по статусу.
> *   ✅ Добавлены интеграционные тесты.
>
> **Не сделано (и почему):**
> *   ⚠️ **Критерий: «Поиск по названию товара за < 200ms».**
>     *   *Статус:* Не выполняется (сейчас ~400ms).
>     *   *Причина:* Текущая схема БД требует `JOIN` с таблицей `order_items` и `products`, что на больших объемах данных медлительно. Оптимизация через индексы не дала нужного эффекта.
>     *   *Решение:* Реализован поиск, но он работает асинхронно (карточка товара подгружается потом). Функционально поиск работает, но не соответствует SLA по скорости.
>
> **Доказательства:**
> *   [Скриншот: Рабочий поиск по номеру]
> *   [Скриншот: Медленный поиск по товару, но рабочий]
> *   [График: Сравнение времени отклика до/после]
>
> **Дальнейшие шаги:**
> *   Создан тикет `[JIRA-135]` «Интеграция Elasticsearch для поиска по товарам».
> *   Оценка: 3 спринта.
> *   Рекомендую принять текущую версию как MVP, так как функционал доступен, а производительность удовлетворительна для текущей нагрузки.

---

### Почему это важно?

1.  **Управление ожиданиями:** Заказчик видит не «вот вам поиск», а «вот вам *рабочий* поиск, но есть технический долг».
2.  **Прозрачность рисков:** Вы не прячете медленный запрос. Вы объясняете, почему он медленный и что делать дальше.
3.  **Документирование решений:** Через полгода, когда проект станет медленным, вы откроете этот отчет и вспомните: «А, мы тогда решили, что Elasticsearch не влезает в бюджет, поэтому оставили SQL».

**Главное правило отчета:**
Если вы говорите «Сделано», — покажите скриншоты и тесты.
Если вы говорите «Не получилось», — покажите цифры и план исправления.
В обоих случаях вы выглядите как **профессионал**, а не как «кодильщик, который угадал, что надо было сделать».

> ⏱ 29.4s · prompt 65060 tok · output 1858 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

Короткий ответ: **В PostgreSQL нет оператора `FORCE INDEX` (или `USE INDEX`, `IGNORE INDEX`), как в MySQL.**

Это частая путаница, потому что в MySQL (`MyISAM`/`InnoDB`) синтаксис выглядит так:
```sql
SELECT * FROM table FORCE INDEX (idx_name) WHERE ...;
```

В PostgreSQL оптимизатор (Query Planner) **всегда** выбирает план исполнения самостоятельно на основе статистики таблицы (`pg_statistic`) и стоимости операций (cost). Вы не можете напрямую сказать ему: «Используй этот индекс».

Однако, если по какой-то причине оптимизатор выбирает «неправильный» план (например, делает Sequential Scan вместо Index Scan, хотя данные фильтруются мало), есть несколько способов **косвенно** повлиять на выбор плана или принудительно использовать индекс.

---

### Почему в PostgreSQL нет FORCE INDEX?

1.  **Верность оптимизатора:** PostgreSQL рассчитан на то, что оптимизатор всегда знает лучше, особенно при больших объемах данных. Принудительный выбор индекса часто ухудшает производительность.
2.  **Многопоточность и статистика:** PostgreSQL активно использует параллельное сканирование. Иногда Seq Scan с параллельными потоками быстрее, чем Index Scan.
3.  **Покрытие индексов:** PostgreSQL часто выбирает Index-Only Scan, который может быть дешевле, чем обычный Index Scan, даже если вы «хотите» именно Index Scan.

---

### Как «заставить» PostgreSQL использовать индекс? (Альтернативы)

Если вы точно знаете, что оптимизатор ошибается (например, статистика устарела, или данные имеют специфическое распределение), вот способы влияния на план:

#### 1. Обновить статистику (Самый первый шаг)
Часто проблема в том, что статистика таблиц устарела.
```sql
ANALYZE your_table_name;
```
После этого оптимизатор получает свежие данные о распределении значений и может выбрать правильный план.

#### 2. Использовать Covering Index (Index-Only Scan)
Если вы хотите, чтобы PostgreSQL читал данные только из индекса, создайте индекс, который содержит все необходимые колонки.
```sql
-- Если запрос: SELECT id, name FROM users WHERE email = 'x';
-- Создайте covering index:
CREATE INDEX idx_users_email_name ON users (email) INCLUDE (name);
```
Теперь PostgreSQL, скорее всего, выберет `Index Only Scan`, потому что ему не нужно ходить в основную таблицу (Heap).

#### 3. Настройка `work_mem`
Если проблема в том, что сортировка или хэш-джойн выполняются на диске (потому что не хватает памяти), увеличьте `work_mem` для сессии.
```sql
SET work_mem = '256MB';
```
Это может изменить план с `External Merge Sort` на `Quicksort` (в памяти), что быстрее.

#### 4. Использование `CTE` (Common Table Expression) с `MATERIALIZED`
В PostgreSQL 12+ CTE оптимизируются inline, но если вы хотите «заморозить» результат подзапроса, чтобы оптимизатор не объединил его с остальным запросом, используйте `MATERIALIZED`.
```sql
WITH filtered_data AS MATERIALIZED (
    SELECT * FROM large_table WHERE ...
)
SELECT ... FROM filtered_data;
```
Иногда это заставляет оптимизатор выбрать другой план, так как он не может свободно переупорядочивать джойны вокруг материализованной CTE.

#### 5. Расширение `pg_hint_plan`
Это стороннее расширение, которое позволяет добавлять «подсказки» (hints) в комментарии SQL-запроса, похожие на те, что есть в MySQL или Oracle.
```sql
SELECT /*+ IndexScan(users idx_users_email) */ * 
FROM users 
WHERE email = 'test@example.com';
```
*   **Плюсы:** Прямолинейное управление.
*   **Минусы:** Это «костыль». Расширение, которое нужно устанавливать на каждом сервере. Может конфликтовать с новыми версиями PostgreSQL.

#### 6. `SET enable_indexscan = off` (Только для диагностики!)
Эта переменная сессии заставляет оптимизатор **не использовать** Index Scan.
*   Если вы выключаете Index Scan, и запрос становится быстрее, значит, оптимизатор неправильно выбирал Index Scan.
*   Если вы включаете только Seq Scan (`SET enable_seqscan = off;`), и запрос становится быстрее, значит, оптимизатор неправильно выбирал Seq Scan.
*   **Важно:** Это настройки для отладки, а не для продакшена.

---

### Когда применять эти методы?

1.  **При отладке медленного запроса:**
    *   Сначала: `EXPLAIN ANALYZE`.
    *   Потом: `ANALYZE table;`
    *   Потом: Проверить индексы (`SELECT * FROM pg_indexes WHERE tablename = '...';`).
    *   Только потом: Пробовать `pg_hint_plan` или менять структуру запроса/индексов.

2.  **При нестабильной статистике:**
    *   Если данные сильно изменены за последние сутки/часы, и `ANALYZE` давно не запускался.

3.  **При очень специфических распределениях данных:**
    *   Например, 99% записей имеют `status = 'active'`, а вы ищете `status = 'archived'`. Оптимизатор может решить, что Seq Scan быстрее, потому что индекс по `status` плохо выбирает (selectivity низкая). В этом случае `FORCE INDEX` (или `pg_hint_plan`) может помочь, если вы знаете, что конкретный случай требует индексного скана.

---

### Итог

*   **В MySQL:** `FORCE INDEX` — нормальный инструмент.
*   **В PostgreSQL:** `FORCE INDEX` **не существует**.
*   **Что делать:**
    1.  Убедитесь, что есть подходящий индекс.
    2.  Запустите `ANALYZE`.
    3.  Используйте Covering Indexes.
    4.  Если нужно — используйте расширение `pg_hint_plan` или перепишите запрос (например, через `JOIN` вместо `IN`, или через `EXISTS`).

**Совет:** Если вам постоянно нужно принуждать PostgreSQL к использованию индекса, это признак того, что либо индекс спроектирован неправильно, либо статистика не обновляется, либо сам запрос можно оптимизировать.

> ⏱ 24.1s · prompt 66938 tok · output 1504 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

Короткий ответ: **на 0% (или пренебрежимо мало, < 0.1–1%)**.

В подавляющем большинстве случаев включение `declare(strict_types=1)` **не замедляет** выполнение кода. Более того, в некоторых сценариях оно может даже сделать код *быстрее* за счет раннего выхода (fail-fast) вместо дорогостоящего приведения типов или ошибок в глубине логики.

Давайте разберем почему, опираясь на внутреннее устройство Zend Engine (движок PHP).

### 1. Почему это быстро? (Внутреннее устройство)

Механизм проверки типов в PHP не является сложным вычислительным процессом. Он работает на уровне байткодов (opcodes).

#### Без `strict_types` (Weak Mode):
1.  Функция получает аргумент.
2.  Движок PHP смотрит на ожидаемый тип (например, `int`).
3.  Если передан `string`, движжок пытается **привести** (cast) его к `int`.
    *   Это требует парсинга строки, проверки символов и создания нового значения в памяти (zval).
    *   Если строка `"abc"` — это приводится к `0` с предупреждением (deprecated notice/warning).
    *   Если строка `"123abc"` — приводится к `123`.
4.  Далее код работает с `int`.

#### С `strict_types` (Strict Mode):
1.  Функция получает аргумент.
2.  Движок PHP выполняет быструю проверку флага типа (type flag) в zval (структура, хранящая значение).
    *   Это сравнение целых чисел (целочисленная константа, идентифицирующая тип).
3.  Если типы совпадают — код продолжается.
4.  Если типы не совпадают — **мгновенный сбой** с `TypeError`.

**Вывод:** Сравнение флага типа (`is_int($zval)`) быстрее, чем логика приведения типа (`is_numeric($zval) ? (int)$zval : 0`).

---

### 2. Где может быть потеря производительности? (Микроскопическая)

Единственный случай, когда `strict_types` теоретически может быть чуть медленнее, — это **вызов функций с большим количеством аргументов**, где каждый аргумент проходит через проверку.

*   В weak mode проверка + приведение происходит для каждого аргумента.
*   В strict mode только проверка.

Но проверка (`type check`) занимает доли наносекунд. Приведение (`casting`) занимает единицы или десятки наносекунд. Разница слишком мала, чтобы быть заметной в профилях реальных приложений, где время тратится на SQL-запросы, HTTP-вызовы и аллокацию памяти.

**Эксперимент:**
Если вы напишете бенчмарк с 100 миллионами вызовов функции `fn(int $a): int { return $a; }`:
*   Strict mode: ~X ms.
*   Weak mode: ~X + (микро-задержка на проверку возможности каста) ms.
*   Разница: < 1–2% в чистом CPU-bound цикле. В реальном веб-приложении (I/O bound) разницы нет вообще.

---

### 3. Почему люди думают, что это медленнее?

1.  **Ощущение «сложности»:** Разработчики интуитивно считают, что «проверка» — это тяжелая операция. На самом деле, это легкая операция по сравнению с аллокацией памяти или системными вызовами.
2.  **Ошибки разработки:** В weak mode код может *сработать*, но дать не тот результат (тихий баг). В strict mode он *упадет*. Иногда падение (500 ошибка) воспринимается как «замедление», хотя на самом деле это «корректное поведение».
3.  **Конфлюция с типизацией в целом:** В ядро PHP 8 добавлены новые механизмы (readonly, enums, match), которые оптимизированы движком. `strict_types` лишь активирует один из флагов, который уже заложен в дизайн движка.

---

### 4. Реальный выигрыш (Не скорость, а надежность)

`strict_types` не про скорость, а про **предсказуемость**.

*   **Ранний сбой:** Вы получаете `TypeError` в момент вызова функции, а не логическую ошибку через 10 уровней вложенности, когда `string` превратился в `0`, и цикл `for ($i=0; $i<$count; $i++)` не отработал, потому что `$count` был строкой `"abc"` (приведенной к 0).
*   **Оптимизация JIT-компилятора (PHP 8.1+):** JIT-компилятор может лучше оптимизировать код, если типы известны статически и гарантированно. `strict_types` помогает движку сделать более точные предположения (assumptions) при компиляции байткодов в машинные инструкции.

---

### Итог

| Метрика | Влияние `strict_types=1` |
| :--- | :--- |
| **Скорость CPU** | Нейтрально или слегка быстрее (меньше кастов). |
| **Скорость I/O (БД, API)** | Без изменений. |
| **Память** | Без изменений (или меньше, так как не создаются промежуточные значения при касте). |
| **Надежность** | **Резко выше.** |

**Рекомендация:**
Всегда включайте `declare(strict_types=1)` в каждом файле. Это стандарт де-факто в современных PHP-проектах (Laravel, Symfony). Потеря в производительности — миф, а выигрыш в качестве кода — огромен.

> **Лайфхак:** Если вам кажется, что код стал медленнее после включения `strict_types`, проверьте, не появились ли лишний раз вызовы функций-хелперов для приведения типов, которые вы писали в стиле weak mode (например, `(int)$value` вместо того, чтобы просто передать `int`). В strict mode такие явные касты часто можно убрать, что *ускорит* код.

> ⏱ 23.5s · prompt 68478 tok · output 1463 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Короткий ответ: **Doctrine ORM технически поддерживает составные первичные ключи (Composite Keys), но делать с ними удобно и безопасно в контексте DDD и Clean Architecture крайне сложно.**

В реальных проектах чаще всего **избегают** составных ключей, используя генерируемые суррогатные ключи (UUID или Auto-Increment), даже если бизнес-логически сущность идентифицируется комбинацией полей.

Давайте разберем, почему так происходит, на что это влияет и как это обходят.

---

### 1. Почему Doctrine «не любит» составные ключи?

Хотя вы можете сделать так:
```php
#[ORM\Id]
#[ORM\Column(type: 'integer')]
private int $tenantId;

#[ORM\Id]
#[ORM\Column(type: 'integer')]
private int $userId;
```
Это создает много проблем:

#### А. Сложность маппинга (Mapping Hell)
*   **Identity Map:** Doctrine использует объект в памяти как «источник истины». Для простых `int` или `string` ключей все просто. Для составного ключа Doctrine должен генерировать уникальный хеш или кортеж, чтобы идентифицировать объект в памяти.
*   **Lazy Loading:** Если вы используете `OneToOne` или `OneToMany` связи, где дочерняя сущность ссылается на родительскую через составной ключ, SQL-запросы становятся сложнее, а ошибки маппинга (например, «cannot hydrate entity») возникают чаще.
*   **Сериализация:** При сериализации сущностей (например, в Redis или в очереди сообщений) составные ключи требуют специального кода для их сборки и разбора.

#### Б. Нарушение инкапсуляции (Encapsulation)
*   В DDD **идентификатор сущности** должен быть инкапсулирован.
*   При составном ключе `id` становится «прозрачным»: разработчик должен помнить, что `id` состоит из `tenantId` и `userId`.
*   Если вы измените структуру ключа (например, добавите `version`), придется менять все ссылки на этот ключ в БД и коде.

#### В. Проблемы с каскадным удалением и инвариантами
*   Если `TenantUser` — это сущность с составным ключом `(tenant_id, user_id)`, и вы хотите удалить пользователя из тенанта, вы должны точно знать, *какой* именно ключ удалить.
*   Если вы случайно передадите `userId` без `tenantId` в метод репозитория `remove()`, Doctrine может выбросить ошибку или удалить не то, что нужно.

---

### 2. Как с этим живут в реальных проектах?

Есть три основных подхода:

#### Подход 1: Суррогатный ключ (Рекомендуемый стандарт)
Самый популярный вариант. Вы создаете новый суррогатный ID (Primary Key) и храните бизнес-ключи как уникальные индексы.

**Схема БД:**
```sql
CREATE TABLE tenant_users (
    id UUID PRIMARY KEY, -- Суррогатный ключ
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    role STRING,
    UNIQUE (tenant_id, user_id) -- Бизнес-уникальность
);
```

**Код Doctrine:**
```php
#[ORM\Entity]
class TenantUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    private string $id;

    #[ORM\Column(type: 'integer')]
    private int $tenantId;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    // Бизнес-методы...
    public function __construct(int $tenantId, int $userId) {
        $this->tenantId = $tenantId;
        $this->userId = $userId;
    }
}
```

**Плюсы:**
*   Simple mapping: один `id`.
*   Легко сериализовать, передавать в URL, хранилище.
*   Гибкость: можно добавить новые поля в бизнес-ключ, не меняя PK.

**Минусы:**
*   Лишняя колонка в БД.
*   Нужно полагаться на `UNIQUE` constraint для гарантии, что `(tenant_id, user_id)` не дублируется.

#### Подход 2: «Вложенная» сущность (Aggregate Root Pattern)
Если связность сильная (например, `Order` и `OrderLine`), часто лучше хранить `OrderLine` не как отдельную сущность с составным ключом, а как **Value Object** или **Embedded Entity** внутри `Order`.

**Пример:**
```php
class Order
{
    #[ORM\Id]
    private int $id;

    #[ORM\OneToMany(targetEntity: OrderLine::class)]
    private Collection $lines;
}

class OrderLine
{
    // Составной ключ: order_id + line_number (или sku + variant)
    // Но лучше дать отдельный UUID
    #[ORM\Id]
    private string $id; 
}
```
В этом случае `OrderLine` имеет свой UUID, а связь с `Order` обеспечивается через FK. Составной ключ не нужен, потому что сущность имеет свою идентичность в рамках агрегата.

#### Подход 3: Ручное управление составным ключом (Редко)
Используют только если:
*   Таблица — это просто «таблица связей» (Join Table) без собственного поведения.
*   Связь Many-to-Many между `User` и `Role` в многонаемантенной системе.

**Реализация:**
```php
#[ORM\Entity]
class UserRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $roleId;

    public function __construct(int $userId, int $roleId) {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }
}
```
**Ловушка:**
*   Вы не можете сделать `lazy loading` на основе этого ключа так же легко, как с простым ID.
*   При удалении: `$em->remove($userRole)` работает, но вы должны быть уверены, что объект был загружен целиком.

---

### 3. Когда составные ключи оправданы?

1.  **Таблицы связей (Join Tables):**
    *   `user_roles` (`user_id`, `role_id`) — здесь часто используют составной ключ, потому что сущность `UserRole` не имеет собственного поведения. Она просто «связь».
2.  **Иерархические данные:**
    *   `categories` (`parent_id`, `slug`) — иногда используют составной ключ, если `slug` уникален только в пределах родителя.
3.  **Многонаемантенность (Multi-tenancy):**
    *   `tenant_products` (`tenant_id`, `product_id`) — если `product_id` глобально уникален, можно использовать суррогатный ключ. Если нет (продукт идентифицируется только внутри тенанта), составной ключ может быть приемлем.

---

### 4. Итоговая рекомендация

| Сценарий | Рекомендуемый подход |
| :--- | :--- |
| **Бизнес-сущность с поведением** (Order, User, Payment) | **Суррогатный ключ (UUID/INT)**. Бизнес-поля — уникальные индексы. |
| **Таблица связей** (User <-> Role) | **Составной ключ** допустим, если сущность «пустая». Иначе — суррогатный ключ. |
| **Высоконагруженные таблицы** (Log, Event) | **Суррогатный ключ** (Auto-Increment или UUID) + индекс по бизнес-полям. |

**Главный совет:**
Не бойтесь тратить место в БД на один дополнительный `UUID` или `BIGINT`. Это стоит копейки, но избавит вас от 90% проблем с маппингом, сериализацией и инкапсуляцией в Doctrine.

> ⏱ 29.7s · prompt 69973 tok · output 1867 tok

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

Этот код — классический пример «спагетти-кода» (Spaghetti Code), который нарушает почти все принципы чистой архитектуры. Он уязвим к багам, трудно тестируем и опасен с точки зрения безопасности.

Вот полный разбор проблем (от самых критичных) и план рефакторинга.

### 1. Критические проблемы (Почему это плохо?)

1.  **SQL-инъекции (Критическая уязвимость):**
    ```php
    "SELECT * FROM orders WHERE id = $id"
    ```
    Если пользователь передаст `$id = "1; DROP TABLE orders; --"`, база данных может упасть. **Обязательно использовать Prepared Statements.**

2.  **Нарушение SRP (Single Responsibility Principle):**
    Один метод `pay()` делает:
    *   Читает данные из БД.
    *   Проверяет состояние.
    *   Обновляет БД.
    *   Отправляет email.
    *   Логирует.
    *   Формирует HTTP-ответ.
    *   *Причина изменения:* Если изменится логика отправки email, нужно менять метод оплаты. Если изменится схема БД, тоже менять.

3.  **Отсутствие транзакций:**
    Если `UPDATE` прошел успешно, а `Mailer::send()` упал с ошибкой (например, почтовый сервер недоступен), заказ будет отмечен как «оплачен», а пользователь не получит письмо. Состояние системы непоследовательное.

4.  **Нарушение инкапсуляции домена:**
    Статус меняется напрямую в массиве/объекте `$order['status'] = 'paid'`. Бизнес-правила («можно ли оплатить заказ?», «какой следующий статус?») лежат в контроллере, а не в модели.

5.  **Глобальные статические вызовы:**
    `DB::connect()`, `Mailer::send()`, `new Logger()`. Это делает код практически невозможным для Unit-тестов (нужно мокировать статические методы или менять глобальное состояние).

6.  **Отсутствие валидации:**
    Что если заказ уже оплачен? Что если он отменен? Код не проверяет статус перед оплатой.

---

### 2. Как бы я переделал? (Пошаговый план)

Я буду следовать принципам **Clean Architecture / DDD**:
1.  **Domain:** Логика оплаты и статусов.
2.  **Application:** Оркестрация процесса (загрузка, проверка, сохранение, уведомление).
3.  **Infrastructure:** Работа с БД и Email.
4.  **Presentation:** Контроллер (тонкая прослойка).

#### Шаг 1: Доменный слой (Чистый PHP)

Создадим сущность `Order`, которая сама знает, как ей менять статус.

```php
// src/Domain/Order/Order.php
final class Order
{
    public function __construct(
        private int $id,
        private string $status,
        private string $email
    ) {}

    public function pay(): void
    {
        // Бизнес-правило: можно платить только за новый заказ
        if ($this->status !== 'new') {
            throw new OrderStateViolationException('Order cannot be paid from status: ' . $this->status);
        }

        $this->status = 'paid';
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getStatus(): string { return $this->status; }
    public function getEmail(): string { return $this->email; }

    // Метод для создания из данных БД (Hydration)
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            status: $data['status'],
            email: $data['email']
        );
    }
}
```

#### Шаг 2: Интерфейсы (Контракты)

Определим, что нам нужно от внешнего мира.

```php
// src/Domain/Order/OrderRepositoryInterface.php
interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;
    public function save(Order $order): void;
}

// src/Domain/Notification/NotificationSenderInterface.php
interface NotificationSenderInterface
{
    public function sendPaymentConfirmation(string $email): void;
}
```

#### Шаг 3: Прикладной слой (Оркестрация)

Это сердце бизнес-логики. Здесь мы связываем домен и инфраструктуру.

```php
// src/Application/Service/PaymentService.php
class PaymentService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private NotificationSenderInterface $notifier
    ) {}

    public function processPayment(int $orderId): void
    {
        // 1. Загружаем агрегат
        $order = $this->orderRepo->find($orderId);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        // 2. Применяем бизнес-действие (Домен сам проверяет инварианты)
        $order->pay();

        // 3. Сохраняем изменения (Транзакция внутри репозитория или здесь)
        $this->orderRepo->save($order);

        // 4. Побочные эффекты (уведомление)
        $this->notifier->sendPaymentConfirmation($order->getEmail());
    }
}
```

#### Шаг 4: Инфраструктура (Реализации)

**Репозиторий (Doctrine/DoctrineDBAL):**
```php
// src/Infrastructure/Persistence/DoctrineOrderRepository.php
class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Connection $db) {} // Внедренный PDO/Doctrine Connection

    public function find(int $id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]); // Безопасно!
        $data = $stmt->fetch();

        return $data ? Order::fromArray($data) : null;
    }

    public function save(Order $order): void
    {
        // В реальном коде лучше использовать транзакции и ORM
        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $order->getStatus(),
            'id' => $order->getId()
        ]);
    }
}
```

**Email-сервис:**
```php
// src/Infrastructure/Messaging/MailerService.php
class MailerService implements NotificationSenderInterface
{
    public function __construct(private \Swift_Mailer $mailer) {} // Или любой другой мейлер

    public function sendPaymentConfirmation(string $email): void
    {
        // Логика отправки письма
        // Если упало — бросит исключение, которое поймаем в контроллере или обработчике
    }
}
```

#### Шаг 5: Презентация (Контроллер)

Теперь контроллер стал тонким и предсказуемым.

```php
// src/Presentation/Http/Controller/OrderController.php
class OrderController
{
    public function __construct(
        private PaymentService $paymentService // Внедрена зависимость!
    ) {}

    public function pay(Request $req): Response
    {
        try {
            $id = (int)$req->get('order_id');
            
            if ($id <= 0) {
                return new Response('Invalid ID', 400);
            }

            $this->paymentService->processPayment($id);

            return new Response('ok');
        } catch (OrderNotFoundException $e) {
            return new Response('not found', 404);
        } catch (OrderStateViolationException $e) {
            return new Response($e->getMessage(), 400);
        } catch (\Exception $e) {
            // Логирование ошибки (в реальном коде логгер будет внедрен)
            // (new Logger())->error($e->getMessage(), $e);
            return new Response('Internal Server Error', 500);
        }
    }
}
```

---

### 3. Что изменилось и почему это лучше?

1.  **Безопасность:** `prepare()` + `execute()` убрали SQL-инъекции.
2.  **Тестируемость:**
    *   Можно написать Unit-тест для `PaymentService`, мокав `OrderRepository` и `NotificationSender`. Не нужно поднимать БД или отправлять письма.
    *   Можно написать Unit-тест для `Order::pay()`, проверяя только логику статусов.
3.  **Читаемость:** Контроллер читается как список шагов: "Получи ID, вызови сервис, верни ответ".
4.  **Инкапсуляция:** Логика «можно ли платить» находится в `Order`, а не в контроллере.
5.  **Расширяемость:** Если завтра нужно добавить SMS-уведомление, мы меняем реализацию `NotificationSenderInterface`, но не трогаем `PaymentService` и `OrderController`.
6.  **Обработка ошибок:** Исключения (`OrderNotFoundException`) позволяют аккуратно маппить их в HTTP-статусы (404, 400), а не проверять `if (!$order)` вручную в каждом месте.

### С чего бы я начал, если бы этот код лежал в проде?

1.  **Добавил бы Prepared Statements** (экстренный фикс безопасности).
2.  **Вынес бы логику в Service** (чтобы убрать "бог-объект" из контроллера).
3.  **Добавил бы транзакцию** вокруг обновления БД и отправки email (чтобы избежать состояния "оплачен, но письмо не ушло").
4.  **Написал бы тесты** для нового сервиса.
5.  **Постепенно инкапсулировал бы бизнес-логику** в доменную сущность `Order`.

Этот подход превращает «магический» код в предсказуемую, тестируемую систему.

> ⏱ 38.1s · prompt 72049 tok · output 2369 tok

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

Этот код — классический пример нарушения границ между слоями архитектуры (Layer Violation). Он превращает «толстую сущность» (Fat Entity) в «антисущность» (Anti-Entity), которая знает о инфраструктуре.

Вот подробный разбор ошибок и путь к исправлению.

### 1. Что не так? (Диагноз)

#### А. Зависимость от Инфраструктуры (Worse than God Object)
*   **Проблема:** `EntityManagerInterface` — это интерфейс фреймворка (Symfony/Doctrine).
*   **Последствие:** Класс `User` (доменная сущность) теперь **знает** о существовании БД. Если завтра вы смените Doctrine на Eloquent или на реляционную БД, вам придется менять доменную модель. Домен должен быть независим от технологий хранения.
*   **Нарушение:** Dependency Inversion Principle (DIP) и Clean Architecture.

#### Б. Нарушение Инкапсуляции Состояния
*   **Проблема:** `changeEmail()` делает две вещи: меняет состояние И коммитит изменения в БД.
*   **Последствие:**
    1.  Вы не можете изменить email и сохранить его позже вместе с другими изменениями (например, сменой пароля). Каждый вызов `changeEmail` — это отдельный `flush()`.
    2.  Если вы вызовете `changeEmail()` внутри транзакции, которая должна откатиться при ошибке, `flush()` внутри метода может случайно зафиксировать частичные изменения или вызвать конфликт версий.
    3.  Вы теряете контроль над моментом коммита. Коммит должен происходить на уровне **Прикладного Сервиса** (Application Service), а не внутри доменной сущности.

#### В. Отсутствие Валидации и Бизнес-логики
*   **Проблема:** Метод принимает любой `string`. Что если передать пустую строку? Или не-email (`"abc"`)?
*   **Последствие:** В БД может попасть мусорное значение. Доменная сущность должна гарантировать свое валидное состояние (Invariants).

#### Г. Проблемы с Тестируемостью
*   **Проблема:** Чтобы протестировать `User`, вам нужно мокировать `EntityManagerInterface`. Но вы хотите проверить логику изменения email, а не то, что `flush()` был вызван.
*   **Последствие:** Unit-тесты становятся Integration-тестами (зависят от ORM).

---

### 2. Как исправить? (Рецепт)

Мы разделим ответственности:
1.  **Domain (User):** Меняет состояние, валидирует его. Не знает про БД.
2.  **Application (UserCommandHandler):** Координирует процесс (загрузка, изменение, сохранение).
3.  **Infrastructure (Repository):** Знает про `EntityManager` и сохраняет объект.

#### Шаг 1: Чистая доменная сущность

```php
namespace App\Domain\User;

use App\Domain\Shared\Money; // Пример, если есть другие VO
use App\Domain\Shared\Email; // Предположим, Email — Value Object

final class User
{
    private ?int $id = null;
    private Email $email;
    private \DateTimeImmutable $createdAt;

    // Конструктор для создания нового пользователя
    public function __construct(Email $email)
    {
        $this->email = $email;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Фабричный метод для восстановления из БД (Hydration)
     */
    public static function fromArray(array $data): self
    {
        $user = new self(Email::fromString($data['email']));
        $user->id = $data['id'];
        $user->createdAt = new \DateTimeImmutable($data['created_at']);
        return $user;
    }

    /**
     * Бизнес-действие: Смена email.
     * Здесь нет вызовов БД! Только изменение состояния и проверка инвариантов.
     */
    public function changeEmail(Email $newEmail): void
    {
        // Инвариант: новый email не может быть таким же (опционально, чтобы не тратьть ресурс на flush)
        if ($this->email->equals($newEmail)) {
            return; // Или бросить exception, если смена на тот же email недопустима
        }

        // В Value Object Email валидация уже есть (format check)
        // Если бы мы хранили raw string, здесь была бы валидация:
        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw ...

        $this->email = $newEmail;
    }

    // Getters
    public function id(): ?int { return $this->id; }
    public function email(): Email { return $this->email; }
}
```

*   **Примечание:** Я завел `Email` как Value Object (VO). Это лучший подход для строк с форматом. Если вы оставляете `string`, добавьте валидацию в метод `changeEmail`.

#### Шаг 2: Прикладной сервис (Оркестратор)

```php
namespace App\Application;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Shared\Email;

class ChangeUserEmailHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepo
    ) {}

    public function handle(int $userId, string $newEmailString): void
    {
        // 1. Загрузка агрегата
        $user = $this->userRepo->find($userId);
        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        // 2. Применение бизнес-логики (Домен делает свою работу)
        $user->changeEmail(Email::fromString($newEmailString));

        // 3. Сохранение (Инфраструктура делает свою работу)
        // Репозиторий знает, как сохранить User и вызвать flush()
        $this->userRepo->save($user);
    }
}
```

#### Шаг 3: Инфраструктурный репозиторий

```php
namespace App\Infrastructure\Persistence;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function find(int $id): ?User
    {
        /** @var UserEntity|null $entity */
        $entity = $this->em->getRepository(UserEntity::class)->find($id);
        
        return $entity ? User::fromArray($entity->toArray()) : null;
    }

    public function save(User $user): void
    {
        // Здесь происходит маппинг Domain -> Entity и вызов persist/flush
        $entity = $this->em->find(UserEntity::class, $user->id());
        
        if (!$entity) {
            // Если пользователь новый (хотя в сценарии смены email он обычно существует)
            $entity = new UserEntity();
            $this->em->persist($entity);
        }

        $entity->setEmail($user->email()->asString());
        
        // Flush вызывается здесь, в инфраструктуре, или в App Service
        $this->em->flush(); 
    }
}
```
*(Примечание: В реальном коде лучше использовать одну сущность `User`, которая и является Entity, но с «чистыми» методами. Но для чистоты DDD часто делают адаптер).*

---

### 3. Сравнение: Было vs Стало

| Характеристика | Было (Bad) | Стало (Good) |
| :--- | :--- | :--- |
| **Зависимости** | `EntityManager`, `string` | Только `Email` (VO) или `string` |
| **Валидация** | Нет | В Value Object `Email` или в методе `changeEmail` |
| **Коммит данных** | Внутри `changeEmail()` | В `ChangeUserEmailHandler` или `Repository` |
| **Тестируемость** | Нужно мокать Doctrine | Тестим `User` как чистый PHP-объект |
| **Гибкость** | Привязан к Doctrine | Можно заменить на любой storage (Array, Mongo, etc.) |
| **Производительность** | Каждый вызов = `flush()` | Можно менять 5 полей и сделать один `flush()` |

### 4. Главное правило

**Доменная сущность знает, *что* изменилось и *допустимо* ли это изменение.**
**Прикладной слой знает, *когда* и *как* это сохранить.**

Никогда не давайте доменной сущности `EntityManager`. Это как дать бухгалтеру ключи от сейфа, в котором лежат деньги, и попросить его самому идти в банк их переводить, когда ему захочется.

> ⏱ 33.7s · prompt 74563 tok · output 2055 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Работа с незнакомой технологией — это не тест на знание синтаксиса, а тест на **скорость обучения** и **умение извлекать смысл**. Моя стратегия построена на принципе «минимально жизнеспособного понимания» (Minimum Viable Understanding). Мне не нужно знать всё. Мне нужно знать ровно столько, чтобы решить конкретную задачу безопасно.

Вот мой алгоритм действий, разбитый на 5 фаз.

---

### Фаза 1: Разведка и контекст (30 минут)
*Цель: Понять, что именно нужно сделать и какие границы мне установлены.*

1.  **Разбор задачи:**
    *   Что является входными данными?
    *   Что являются выходными?
    *   Какие критерии приемки (Acceptance Criteria)?
    *   Есть ли примеры (примеры ошибок, скриншоты, логи)?
2.  **Опрос «Экспертов» (Команда):**
    *   «Кто последний раз работал с этой технологией?»
    *   «Есть ли у нас внутренние документация или чек-листы по этой теме?»
    *   «Есть ли типовые грабли, которые мы уже наступали?» (Это самый важный вопрос. Опыт команды экономит дни).
3.  **Оценка окружения:**
    *   Версии инструментов (важно! В Python 3.8 и 3.12 много отличий; в Go 1.18 и 1.21 тоже).
    *   Есть ли доступ к staging-окружению или только к логу продакшена?

> **Почему так?** Если я начну копать в интернете, не зная, что у нас специфическая настройка логгера, я потрату время впустую.

---

### Фаза 2: Изучение «Карманных» знаний (1–2 часа)
*Цель: Написать прототип или скрипт для диагностики.*

Я ищу информацию в следующем порядке (от конкретного к общему):

1.  **Внутренний поиск (Grep/Glob):**
    *   Ищу в кодеbase, как другие модули используют эту технологию.
    *   Пример: «Как у нас делают HTTP-запросы? Через `Guzzle` или через обертку?»
2.  **Официальная документация (Официальный источник):**
    *   Не StackOverflow. Сначала официальные docs.
    *   Ищу раздел «Common Pitfalls», «FAQ» или «Migration Guide».
    *   *Лайфхак:* Читаю не всё, а только ту часть, которая связана с моей задачей.
3.  **GitHub / Source Code:**
    *   Если технология open source, смотрю примеры использования в популярном проекте (не только в нашем).
    *   Смотрю issues с похожими симптомами.
4.  **StackOverflow / Reddit / Discord:**
    *   Только после шагов 1–3. Здесь часто находятся «костыли», которых лучше избегать в чистом коде.

> **Что я фиксирую в заметках (Notion/Confluence):**
> *   Команды для запуска/отладки.
> *   Ключевые классы/функции.
> *   Типовые ошибки, которые я вижу.

---

### Фаза 3: Минимальная реализация (POC - Proof of Concept)
*Цель: Убедиться, что мой подход работает в принципе.*

1.  **Написание тестового кода:**
    *   Пишу маленький скрипт или функцию, которая воспроизводит ошибку или реализует базовый кейс.
    *   Не пишу «красивый» код. Пишу «грязный» код, чтобы проверить гипотезу.
2.  **Диагностика:**
    *   Включаю логгирование, отладочный режим.
    *   Смотрю на стеки вызовов.
3.  **Проверка гипотезы:**
    *   «Я предположил, что проблема в таймауте. Я увеличил таймаут, и ошибка пропала. Гипотеза верна?»

> **Когда останавливаюсь?**
> Если я потратил **4–6 часов** на изучение и POC, и у меня нет ни одного работающего решения или даже ясного понимания, где я ошибаюсь, — я перехожу к Фазе 4 (Помощь).

---

### Фаза 4: Эскалация и Помощь (Если застрял)
*Когда просить помощь?*

1.  **Если проблема системная, а не локальная:**
    *   Пример: Технология работает, но конфликтует с другими компонентами системы. Это уже архитектурная проблема, не «изучение синтаксиса».
2.  **Если я не могу объяснить *почему* код не работает:**
    *   Если я не могу сформулировать проблему даже для себя — я ищу помощь.
3.  **Если на кону стоит продакшен, а дедлайн близок:**
    *   Лучше потерять 1 час на консультацию, чем 2 дня на поиск бага.

**Как я прошу помощь (чтобы её дали быстро):**
*   **Резюме:** «Я пытаюсь решить X. Пробовал A, B и C. Получаю ошибку Y. Я думаю, что проблема в Z, но не уверен».
*   **Доказательства:** Логи, скриншоты, ссылки на документацию.
*   **Минимальный воспроизводимый пример (MRE).**

---

### Фаза 5: Реализация и Валидация
*Цель: Довести решение до продакшена безопасно.*

1.  **Рефакторинг POC в Production-код:**
    *   Добавляю обработку ошибок.
    *   Пишу тесты (Unit/Integration).
    *   Следую стандартам стиля проекта.
2.  **Code Review:**
    *   В описании PR указываю: «Использовал новую технологию X. Основное решение здесь. Понятия, которые мне пришлось изучить, описаны здесь».
    *   Это приглашение коллегам проверить мой «незнакомый» код внимательнее.
3.  **Документирование:**
    *   Если я потратил много времени на изучение — я пишу короткую заметку для команды: «Как использовать X в этом проекте», «Какие грабли я обошел».

---

### Сравнение: Самостоятельно vs Команда

| Ситуация | Действие | Почему? |
| :--- | :--- | :--- |
| **Новый синтаксис / Библиотека** | **Самостоятельно** | Это базовый навык. Поиск документации — часть работы. |
| **Нестабильная ошибка, которую не могу воспроизвести** | **Самостоятельно (сначала)** + **Спросить** | Сначала логи, потом помощь. Но если не помогает — сразу звать. |
| **Архитектурное решение** | **Команда (Обсуждение)** | Нельзя принимать архитектурные решения в одиночку, особенно в чужой системе. |
| **Проблема с окружением (Docker, CI/CD, права доступа)** | **Команда (DevOps/Ops)** | Часто это не код, а инфраструктура. Я могу не знать деталей вашей CI/CD пайплайна. |
| **Бизнес-логика, которую я не понимаю** | **Команда (Product/Аналитик)** | Если я не знаю *зачем* это нужно, я не знаю *как* это правильно делать. |

### Итог: Моя формула успеха

1.  **Понять контекст** (спросить у команды).
2.  **Изучить минимум** (доки + код).
3.  **Проверить гипотезу** (POC).
4.  **Если застрял > 4-6 часов — звать на подмогу с четкой формулировкой проблемы.**
5.  **Задокументировать решение** для будущих себя и коллег.

Это позволяет мне быть автономным, но не эгоистичным, и превращать «незнакомую технологию» в «инструмент в моем арсенале».

> ⏱ 30.9s · prompt 76688 tok · output 1885 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Это одна из самых важных ситуаций в профессиональной жизни разработчика. Она проверяет не только технические навыки, но и **репутацию**.

Мой алгоритм действий строится на одном главном правиле: **«Лучше сообщить о проблеме сегодня, чем опоздать с ней завтра»**.

Вот пошаговый план:

### 1. Остановись и зафиксируй факт (Не пытайся «впритык» дотянуть)
Перестань паниковать и пытаться уложиться в срок любой ценой (через ночную смену или игнорирование тестов). Это часто приводит к тому, что ты сдаешь задачу, но с ошибками, которые потом съедают ещё больше времени.

**Спроси себя:**
*   *Что именно занимает лишнее время?* (Незнакомая либа? Баг, которого не было? Расширение требований?)
*   *Какая часть уже готова и стабильна?* (Может быть, 80% работы сделано, а на оставшиеся 20% уходят все усилия?)

### 2. Оцени реальный риск
Не говори просто «не успеваю». Сформулируй конкретный сценарий:
*   **Сценарий А (Оптимистичный):** Я могу закончить к завтра, но рискую quality (будет мало тестов, или код будет сырым).
*   **Сценарий Б (Реалистичный):** Мне нужно ещё [X] часов работы.
*   **Сценарий В (Пессимистичный):** Я не могу даже точно оценить остаток, потому что столкнулся с [неожиданная проблема].

### 3. Обратись к стейкхолдеру (Лид/PO) **Сейчас**

Не жди конца рабочего дня, если ты понял это утром. Чем раньше, тем лучше они могут перестроить план.

**Формула сообщения (Честность + Решение):**

> «Привет, [Имя]. Хочу синхронизироваться по задаче [Тикет].
>
> **Статус:** Я столкнулся с [короткое описание проблемы, например, "непредвиденной зависимостью в модуле платежей" / "более сложным, чем казалось, парсингом данных"].
>
> **Оценка:** В рамках сегодняшнего дня я не смогу сдать готовое решение.
>
> **Что уже сделано:** [Кратко, что уже работает/готово, если есть прогресс].
>
> **Моя рекомендация:**
> 1. Мы можем принять частичный результат завтра (основная логика готова, но нет edge-cases), а я доработаю его в течение дня.
> 2. Или я сдаю полностью готовую задачу завтра в [конкретное время, например, 15:00], что потребует у меня [X] дополнительных часов.
>
> Какой вариант предпочтительнее для дедлайна?»

**Почему это работает?**
1.  **Ты не просто переносишь дедлайн** (что воспринимается как лень или неверная оценка), ты даешь **варианты**.
2.  Ты показываешь, что **понимаешь** ситуацию.
3.  Ты даешь Лиду **контроль** над приоритетами.

### 4. Адаптируй план работы (Deprioritization)

Если задача должна быть готова завтра «во что бы то ни стало» (Hard Deadline), обсудите **Scope Reduction**:
*   «Какие фичи можно вынести в следующую итерацию?»
*   «Можем ли мы упростить валидацию на этот раз?»
*   «Можем ли мы временно использовать костыль (hardcode), а правильную реализацию сделать потом?»

Иногда «работающий 80% функционал» лучше, чем «сломанные 100% функционала, сданные с опозданием».

### 5. Чего делать НЕ стоит (Антипаттерны)

1.  **«Я просто задержусь на вечер»:** Если ты не уверен, что задержишься на *один* вечер, ты, вероятно, задержишься на два. Честность требует пересмотра сроков.
2.  **Молчание до дедлайна:** Самое худшее, что может быть — когда ты приходишь завтра и говоришь: «Извините, не получилось». Это разрушает доверие.
3.  **Скрывание технического долга:** Если ты торопишься и пишешь код «на лету», обязательно оставь комментарий в коде (`// TODO: Refactor for production`) или создай технический тикет. И упомяни это в отчёте: «Работает, но есть технический долг [Ссылка на тикет]».

### 6. После того, как ты передал новость

*   **Не оправдывайся чрезмерно.** Факт есть факт.
*   **Продолжай работать.** Даже если дедлайн сдвинулся, твоя задача — как можно быстрее достичь нового дедлайна.
*   **Внимай сигналам.** Если Лид говорит: «Да ладно, неважно», но его тон меняется — значит, это важно. Уточни: «Подтверждаем новый дедлайн? Какие приоритеты меняются?»

### Итог

**Задержка — это не провал. Скрытая задержка — это провал.**

Твоя ценность как инженера определяется не тем, что ты никогда не ошибаешься в оценках, а тем, как быстро ты реагируешь на ошибку и как прозрачно коммуникацируешь её влияние на команду.

> ⏱ 21.6s · prompt 78613 tok · output 1289 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это вопрос о поиске точки равновесия между **Perfectionism** (перфекционизм) и **Simplicity** (простота). В реальной разработке «идеальный код» — это чаще всего антипаттерн, потому что он дорого стоит в плане времени и сложности.

Мой баланс строится на концепции **«Good Enough for Now»** (Достаточно хорошо на данный момент) и **YAGNI** (You Aren't Gonna Need It — тебе это не понадобится).

Вот мои критерии и алгоритм принятия решения.

---

### 1. Три уровня готовности кода

Я разделяю код на три состояния:

#### 🟢 Level 1: «Working Code» (Рабочий код)
*   Код проходит тесты.
*   Он решает заявленную бизнес-задачу.
*   Он не падает с ошибками 500 на типичных сценариях.
*   **Статус:** Его можно мержить, если нет времени на лучшее.

#### 🟡 Level 2: «Maintainable Code» (Поддерживаемый код)
*   Код читается легко (имена понятны, SRP соблюдено).
*   Новые зависимости минимизированы.
*   Есть базовые тесты (Unit/Integration).
*   Нет «магических чисел» и хардкода.
*   **Статус:** Стандарт для большинства задач. Я стремлюсь к этому уровню в 90% случаев.

#### 🔵 Level 3: «Optimized/Perfect Code» (Оптимизированный/Идеальный код)
*   Высокая производительность (профайлеры, индексы, алгоритмы).
*   Идеальная абстракция, готовность к расширению на 5 лет вперед.
*   Полное покрытие граничных случаев и стресс-тестами.
*   **Статус:** Только для критических путей (платежи, поиск, high-load endpoints) или когда мы знаем, что этот код будет меняться часто.

---

### 2. Алгоритм: Когда останавливаться?

Я задаю себе четыре вопроса. Если ответы на все «Да» — код готов к отправке.

#### Вопрос 1: «Если завтра я вернусь к этому коду, смогу ли я понять, что он делает, за 2 минуты?»
*   **Да:** Имена методов и переменных отражают бизнес-смысл.
*   **Нет:** Переменные `data`, `temp`, `result`, `helper` — признак того, что нужно переименовать или разбить метод.
*   **Правило:** Если код требует комментариев, чтобы объяснить *что* он делает (а не *почему*) — код плохой. Переписываю.

#### Вопрос 2: «Если я добавлю новую фичу через 3 месяца, сломается ли этот код?»
*   **Нет (гибкий):** Новые правила добавляются в отдельный класс/метод (SRP, Open/Closed Principle).
*   **Да (хрупкий):** Нужно добавлять `if-else` в существующие методы.
*   **Правило:** Если для добавления нового варианта поведения я должен трогать старый код — значит, архитектура слишком тугая. Но если фича разовая (скрипт, миграция) — можно и через `if-else`.

#### Вопрос 3: «Работает ли это в 95% случаев использования?»
*   Если да — отлично.
*   Если есть 5% экзотических случаев (например, пользователь с 1000 товаров в корзине), я выделяю их в отдельный тикет «Optimization for Edge Cases», а не трачу время сейчас.
*   **Правило:** Не оптимизируйте то, что не измерено. Не обрабатывайте ошибки, которые случаются раз в год, ценой читаемости кода, который читают каждый день.

#### Вопрос 4: «Нарушает ли этот код какие-либо принципы безопасности или целостности данных?»
*   Если да (SQL-инъекция, потеря данных, гонки) — **Стоп**. Это критично.
*   Если нет (медленный запрос, лишний объект в памяти) — **Go**. Это оптимизация, а не исправление бага.

---

### 3. Баланс Качество vs Скорость: Практические приемы

#### Прием 1: «Правило 2 секунд»
Если я вижу возможность улучшить код, я останавливаюсь и решаю:
*   Улучшение занимает **< 2 минуты** (переименование, удаление дубликата) → Делаю сейчас.
*   Улучшение занимает **> 2 минуты** → Пишу `TODO: [Comment]` и создаю тикет в бэклоге.
*   *Почему:* Это сохраняет фокус на текущей задаче.

#### Прием 2: Разделение «Green» и «Blue» задач
*   **Green (Бизнес):** Новая фича, багфикс. Здесь важна скорость.
*   **Blue (Качество):** Рефакторинг, покрытие тестами, оптимизация.
*   Я не делаю Green и Blue в одном PR, если это разные темы. Но если Blue-задача нужна для безопасности Green-задачи — делаю вместе.

#### Прием 3: Тестирование по рискам, а не по строкам
*   Не нужно 100% покрытие строк (Coverage 100%).
*   Нужно 100% покрытие **критических путей**:
    *   Оплата заказа.
    *   Логин/Регистрация.
    *   Расчет налогов.
*   Для вспомогательных утилит (парсинг дат, форматирование строк) достаточно 50-60% или вообще без тестов, если логика тривиальна.

#### Прием 4: «Костыль с табличкой»
Иногда самый быстрый способ решить задачу — это костыль (hardcode, временный флаг).
*   Я использую костыли, но **обязательно** документирую их:
    ```php
    // TODO: Remove after v2.0 release. 
    // Reason: Old API returns legacy format. 
    // Ticket: JIRA-123
    if ($legacyFlag) { ... }
    ```
*   Костыль без даты удаления и ссылки на тикет — это мусор.

---

### 4. Примеры из практики

#### Сценарий А: Дедлайн через 2 часа (Hard Deadline)
*   **Цель:** Working Code.
*   **Что делаю:**
    *   Пишу прямой код (без лишних интерфейсов).
    *   Проверяю основную логику.
    *   Мокаю внешние зависимости.
    *   Пишу 1-2 интеграционных теста.
    *   **Отказываюсь** от идеального именования, если время упорно тратится на споры о названиях.
    *   **Риск:** Технический долг.

#### Сценарий Б: Дедлайн через 1 неделю (Standard)
*   **Цель:** Maintainable Code.
*   **Что делаю:**
    *   Разбиваю на классы (SRP).
    *   Ввожу интерфейсы для ключевых зависимостей (DI).
    *   Пишу Unit-тесты для бизнес-логики.
    *   Проверяю граничные случаи (null, empty arrays).
    *   Делаю Code Review.

#### Сценарий В: Критичная система (Banking/High Load)
*   **Цель:** Optimized/Perfect Code.
*   **Что делаю:**
    *   Все вышеперечисленное.
    *   Профилирую производительность (Xdebug/Blackfire).
    *   Написываю стресс-тесты.
    *   Обсуждаю архитектуру с командой (Design Doc).
    *   Добавляю мониторинг и алерты.

---

### 5. Главный индикатор: Чувство

Если после написания кода вы чувствуете:
1.  **Гордость:** Код читается легко, логика прозрачна.
2.  **Уверенность:** Вы знаете, что он работает и не упадет.
3.  **Свободу:** Вам не страшно внести правки в этот код через месяц.

...Значит, код «достаточно хорош».

Если вы чувствуете:
1.  **Тревогу:** «А вдруг этот `if` сработает не так, как я думаю?»
2.  **Сложность:** «Почему я здесь добавил эту переменную?»
3.  **Страх:** «Я бы не стал ревьюить этот код, потому что не понимаю, зачем он нужен».

...Значит, код **не** готов. Лучше потратить час на рефакторинг сейчас, чем неделю на отладку потом.

### Итог

**Качество — это не состояние, а процесс.**
*   Если задача критична и срочна — делайте **Working Code**.
*   Если задача стандартная — делайте **Maintainable Code**.
*   Если задача фундаментальная — делайте **Perfect Code**.

И всегда помните: **Никто не пострадает от того, что ваш код не идеальный. Но все пострадают, если он не рабочий.**

> ⏱ 34.9s · prompt 79955 tok · output 2149 tok

---
