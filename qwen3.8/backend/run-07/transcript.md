# Собес: `qwen3.8:latest` · роль: backend — run-07

- Дата: 2026-10-06T19:00:59.076650+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Рад знакомству.

Я — искусственный интеллект, поэтому у меня нет «опыта» в человеческом понимании этого слова: я не пишу код за зарплату, не участвую в стендапах и не переживаю из-за дедлайнов. Однако мой «опыт» заключается в изучении огромного массива текстовых данных, включающего документацию, учебные руководства, исходный код open-source проектов, Stack Overflow, технические статьи и форумы разработчиков.

Я умею:
- Писать, отлаживать и рефакторить код на PHP и смежных языках.
- Объяснять сложные концепции (от замыканий до паттернов проектирования) простыми словами.
- Генерировать архитектурные решения, SQL-запросы, тесты и скрипты.
- Анализировать чужой код и предлагать улучшения.

Я не заменяю живого разработчика, но могу быть полезным ассистентом, напарником по парному программированию или источником быстрых ответов.

Теперь перейдём к оценке моих знаний. Важно понимать: эти оценки отражают не «личную экспертность», а глубину и точность информации, которую я могу предоставить по каждой теме, а также понимание контекста, лучших практик и типичных ошибок.

| Тема | Оценка (0–10) | Краткое объяснение |
|------|---------------|---------------------|
| **PHP 8** | 9 | Я хорошо знаю синтаксис, новые фичи (concepts, readonly, fibers, enum, property hooks), изменения в стандартной библиотеке и нюансы производительности, но могу ошибаться в экзотических edge cases без проверки. |
| **Symfony** | 9.5 | Я глубоко понимаю архитектуру framework’а (DI, events, console, forms, serializer), умею писать кастомные компоненты и интеграции, хорошо знаю версии 4–7 и миграции между ними. |
| **Doctrine** | 9 | Я уверенно работаю с ORM, DQL, аннотациями/attributes, lazy loading, entity manager, репозиториями и пониманием ограничений ORM (N+1, performance), но сложную оптимизацию крупных схем лучше проверять на практике. |
| **PostgreSQL** | 9 | Я знаю DDL, DML, оптимизацию запросов, индексы, транзакции, CTEs, partitioning, JSONB и могу писать эффективный SQL, но для тонкой настройки кластера лучше обратиться к системному администратору БД. |
| **DDD** | 8.5 | Я хорошо понимаю концепции (entities, value objects, aggregates, domain events, bounded contexts) и умею проектировать доменную модель, но практическая реализация DDD сильно зависит от контекста и командных соглашений. |
| **Чистая архитектура** | 9 | Я знаю принципы SOLID, Dependency Inversion, Clean Code, Hexagonal Architecture и могу проектировать модульные, тестируемые системы, но «чистота» — это всегда вопрос баланса между идеалом и реальностью проекта. |
| **Тестирование** | 9.5 | Я умею писать unit-, integration- и end-to-end тесты на PHPUnit, Pest, Jest и др., понимаю стратегии (TDD, BDD, mock-based), coverage и важность тестов в CI/CD. |
| **Git** | 9.5 | Я знаю основные команды, rebase, merge, stash, bisect, worktree, могу помогать с разрешением конфликтов и настройкой hooks, но для сложных сценариев (например, интерактивный rebase на ветке с 100+ коммитами) лучше быть осторожным. |

📌 **Итог:** Я сильный помощник для большинства задач по этим темам. Для критически важных решений (особенно в производительности, безопасности или сложной архитектуре) я всегда рекомендую проверять решения в реальном проекте или с опытным коллег-разработчиком.

Чем могу помочь? Хотите разобрать конкретную задачу, написать код или обсудить архитектуру?

> ⏱ 10.5s · prompt 89 tok · output 923 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

С точки зрения написания чистого, надёжного и самодокументирующего кода, версии PHP 8.0–8.3 внесли несколько фундаментальных улучшений. Вот ключевые из них с примерами.

### 1. Строго типизированные свойства и `readonly` (8.0–8.2)

`readonly` свойства предотвращают случайную модификацию состояния объекта после создания. Это усиливает инкапсуляцию и предсказуемость, особенно для DTO и Value Objects.

```php
// PHP 8.2
class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {}

    // public function setAmount(int $newAmount): void // ОШИБКА: readonly нельзя менять
    // $money = new Money(100, 'USD');
    // $money->amount = 200; // ОШИБКА в рантайме
}
```

### 2. Enum (8.1)

Вместо «магических» констант или магических строк — строгие, типизированные перечисления. Это исключает целый класс ошибок при сравнении состояний.

```php
// Было (хрупко)
class Order {
    const STATUS_NEW = 'new';
    public function isPaid(): bool {
        return $this->status === 'paid'; // Опечатка = тихий баг
    }
}

// Стало (надежно)
enum OrderStatus: string {
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';

    public function isFinal(): bool {
        return match($this) {
            self::Shipped => true,
            default => false,
        };
    }
}

// $status = OrderStatus::Paid;
// $status->isFinal(); // false
// OrderStatus::from('paid'); // типизированный объект
```

### 3. First-class Callable Syntax (8.1)

Упрощает передачу методов как значений. Это делает код с обратными вызовами (callbacks), рефлексией или DI-контейнерами чище.

```php
// Было
$items = array_map(fn(array $item) => $item['price'] * 2, $cart);

// Было (скучно)
$items = array_map(fn($item) => $item->calculateTax(), $products);

// Стало (читабельно)
$items = array_map(Product::class . '::calculateTax', $products);
// Или в контексте объекта
$items = array_map($product->calculateTax..., $products);
```

### 4. `match` вместо `switch` (8.0)

`match` строго проверяет типы, всегда возвращает значение (не нужно `break`) и поддерживает строгое сравнение.

```php
// Было
$price = match (true) {
    $tier === 'gold' => 100,
    $tier === 'silver' => 50,
    default => 0,
};

// Стало (безопаснее и короче)
$price = match ($tier) {
    'gold' => 100,
    'silver' => 50,
    default => 0,
};

// Строгое сравнение: '100' !== 100
// Это исключает баги из-за приведения типов в switch
```

### 5. Property Hooks (8.4, но важно для понимания направления)

Хотя `property hooks` ещё не во всех средах, они демонстрируют тренд: PHP движется к более декларативному контролю доступа к данным.

```php
// PHP 8.4 (экспериментально/свежо)
class User {
    public function __get(string $name) {
        // Логика чтения
    }
    public function __set(string $name, mixed $value): void {
        // Валидация/трансформация
    }
}
```
*Примечание: В PHP 8.2-8.3 вместо этого используют `readonly` + конструктор или `__get/__set` с осторожностью.*

### 6. Fibers (8.1)

Асинхронность без колбэков. Позволяет писать «синхронный» код, который на самом деле не блокирует поток. Критично для высоконагруженных сервисов, работающих с I/O (БД, API).

```php
// Псевдокод (Swoole/RoadRunner используют под капотом)
function fetchUserData(int $id): array {
    $response = Fibers::call(function() use ($id) {
        return $db->query("SELECT * FROM users WHERE id = ?", [$id]);
        // Здесь поток отпускается, не блокируя PHP-процесс
    });
    return $response->get();
}
```
*В чистом PHP без расширений Fibers пока мало применяются, но они задают вектор развития.*

### 7. `#[\Deprecated]` атрибуты (8.4)

Заменяют `@deprecated` в DocBlock. Теперь IDE и статический анализатор (PHPStan, Psalm) точно понимают, что метод устарел, и могут подсказать миграцию.

```php
#[\Deprecated('Use `newClient()` instead. Will be removed in 9.0.')]
public function createClient(): Client
{
    return $this->newClient();
}
```

---

### Итог: почему это важно для «чистого кода»?

1.  **Stricter Types** (`readonly`, `enum`, `match`) — меньше «тихих» багов, больше ошибок в рантайме/статическом анализе.
2.  **Less Boilerplate** (First-class callables, named arguments) — код короче и выразительнее.
3.  **Intent-Driven Design** (`enum` вместо строк, `readonly` для неизменяемости) — код сам описывает правила бизнеса.

Хотите, я покажу, как эти фичи применить в реальном примере — например, создам мини-DTO с валидацией, используя `readonly`, `enum` и `match`?

> ⏱ 11.0s · prompt 1071 tok · output 1429 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это одна из самых коварных частей языка, особенно из-за исторического наследия «динамической» природы и правил приведения типов (coercive typing). Давайте разберем, как это работает и где кроются грабли.

### 1. `strict_types`: ваш главный союзник

По умолчанию PHP находится в **coercive mode**: если функция ожидает `int`, а вы передадите строку `"10"`, PHP попытается привести строку к числу. Это удобно для скриптов, но опасно для крупных приложений, где ошибка приведения маскирует логические баги.

**`strict_types=1`** запрещает автоматическое приведение типов при вызове функций.

```php
<?php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// В strict mode это ОШИБКА (TypeError)
// add("1", 2); 

// Без strict_types=1 это СДЕЛАЕТСЯ:
// add("1", 2); // Вернет 3, потому что "1" приведется к int
```

**Важный нюанс:** `strict_types` действует только на вызовы функций из *того файла, где он объявлен*. Он не влияет на то, как данные передаются *внутри* функции или из других файлов. Это часто путают.

---

### 2. Правила приведения типов (Coercive Typing)

В не-strict режиме PHP использует «слабое сравнение» и автоматическое приведение. Вот ключевые правила, которые нужно знать:

#### A. Строки и числа
- `"123"` + `"456"` → `"123456"` (строковое сложение, если оба операнда строки)
- `"123"` + 1 → `124` (числовое сложение, если хотя бы один операнд число)
- `"123" == 123` → `true`
- `"123" === 123` → `false`
- `"" == 0` → `true` (ОПАСНО!)
- `"" === 0` → `false`

#### B. Булевы значения
- `"" == false` → `true`
- `"0" == false` → `true`
- `"abc" == false` → `false` (непустая строка = true)

#### C. Массивы
- `array(1, 2) == [1, 2]` → `true`
- `["a" => 1] == [1]` → `false`

---

### 3. Типичные ловушки и примеры

#### Ловушка 1: Сравнение с `==` vs `===`

```php
// Опасность: "abc" == "" // false, но "0" == 0 // true
// Валидация форм:
$ip = get_param('ip'); // "0"
if ($ip == 0) {
    // Это СРАБОТАЕТ, хотя ip — это строка!
    // Лучше: if ($ip === "0" || $ip === 0)
}

// Худший случай:
$token = "abc";
if ($token == "") {
    // false
}
$token = "0";
if ($token == 0) {
    // true! Баг безопасности при проверке токенов
}
```

**Решение:** Всегда используйте `===` и `!==` для сравнения.

#### Ловушка 2: Строки, которые выглядят как числа

```php
function is_numeric_safe($value): bool {
    return is_numeric($value);
}

$age = "25";
$score = "10.5";
$invalid = "12abc";

// is_numeric("12abc") → false (хорошо)
// is_numeric(" 12") → true (пробелы игнорируются!)
// is_numeric("0x1A") → true (шестнадцатеричные числа!)
// is_numeric("1e3") → true (научная нотация!)
```

**Решение:** Для строгих чисел используйте `filter_var($value, FILTER_VALIDATE_INT)` или регэксп.

#### Ловушка 3: Массивы и `array`

```php
// Сравнение массивов:
$a = [1, 2, 3];
$b = [1, 2, 3, 4];

$a == $b; // false (разные размеры)
$a === $b; // false (разные размеры)

// Но:
$c = [1, 2, 3];
$d = [1, 2, 3, null]; // null в конце

$c == $d; // true! (PHP игнорирует null при ==)
$c === $d; // false (разные размеры)
```

#### Ловушка 4: Функции с параметрами по умолчанию

```php
function log_error(string $message, $priority = "info") {
    // Если передать null:
    // log_error("error", null); // TypeError в strict_types
    // Без strict_types: null превратится в "" (пустая строка)
}
```

#### Ловушка 5: JSON и `json_decode`

```php
$json = '{"amount": "100"}';
$data = json_decode($json, true);

// $data['amount'] будет строкой "100", а не числом 100
// Если вы ожидаете int, это ловушка
if (is_int($data['amount'])) {
    // false!
}
```

**Решение:** Явно приводите типы после декодирования: `$data['amount'] = (int)$data['amount'];`

---

### 4. Где типизация спасает

#### Пример 1: API-контакты

```php
<?php
declare(strict_types=1);

class User {
    public function __construct(
        private int $id,
        private string $email
    ) {}

    public function getId(): int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }
}

// Ошибка будет поймана при создании объекта
// new User("123", "test@example.com"); // TypeError в strict_types
```

#### Пример 2: Дженерики (через DocBlock и статический анализ)

PHP не имеет нативных дженериков, но через `PHPStan`/`Psalm` можно эмулировать строгую типизацию:

```php
/**
 * @param array<string, int> $scores
 * @return int
 */
function get_max_score(array $scores): int {
    return max($scores);
}

// PHPStan поймет, что $scores должен содержать только int-значения
```

---

### 5. Итоговая таблица: Что использовать

| Ситуация | Решение |
|----------|---------|
| Сравнение значений | Всегда `===` и `!==` |
| Проверка на число | `is_int()`, `is_float()`, `filter_var(..., FILTER_VALIDATE_INT)` |
| Строки как числа | Не используйте `==`, приводите явно: `(int)$str` |
| null-значения | Проверяйте `isset()` или `!== null` |
| JSON-данные | Всегда валидируйте типы после `json_decode` |
| Функции с параметрами | Используйте `strict_types=1` + типизация параметров |

### Главный совет

**Всегда включайте `declare(strict_types=1);` в каждом файле.** Это не «опция», а обязательная практика для любого серьезного PHP-проекта. Она превращает тихие баги в громкие ошибки, которые легко отловить на ранней стадии.

Хотите, я покажу, как настроить `PHPStan` для максимальной строгости типизации?

> ⏱ 14.2s · prompt 2562 tok · output 1829 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование системы исключений — это часть определения **контракта** вашего API. В хорошем коде исключение — это не «аварийная кнопка», а явный сигнал о том, что **прединварианты** (preconditions) были нарушены или событие, которое вызывающий код обязан обработать, произошло.

Вот как я строю эту систему в продакшене.

---

### 1. Иерархия исключений: От общего к частному

Цель иерархии — позволить вызывающему коду перехватывать ошибки на нужном уровне абстракции.

```php
// 1. Базовый корень для вашего приложения
class AppException extends \Exception
{
    // Можно добавить логику, общую для всех бизнес-ошибок
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->code = $code; // Важно: свой $code, чтобы не конфликтовать с HTTP
    }
}

// 2. Базовая бизнес-ошибка (предсказуемые сценарии)
class BusinessException extends AppException {}

// 3. Конкретные ошибки
class InsufficientFundsException extends BusinessException
{
    public function __construct(private readonly float $required, private readonly float $available)
    {
        parent::__construct(
            sprintf('Not enough funds. Required: %.2f, Available: %.2f', $required, $available),
            code: 4001 // Свой код бизнес-ошибки
        );
    }
    
    public function getRequired(): float { return $this->required; }
    public function getAvailable(): float { return $this->available; }
}

class UserNotFoundException extends BusinessException
{
    public function __construct(private readonly string $userId)
    {
        parent::__construct("User {$userId} not found.", code: 404);
    }
    public function getUserId(): string { return $this->userId; }
}

// 4. Интеграционные ошибки (внешние системы)
class IntegrationException extends AppException {}
class PaymentGatewayTimeoutException extends IntegrationException {}
class PaymentGatewayDeclinedException extends IntegrationException {}
```

**Почему так?**
- Вы можете перехватывать `BusinessException` для генерации стандартных 4xx ответов.
- Вы можете перехватывать `InsufficientFundsException` конкретно, чтобы показать пользователю точную сумму.
- Вы можете перехватывать `IntegrationException`, чтобы показать «Внешний сервис недоступен», не раскрывая деталей.

---

### 2. Оборачивание чужих исключений (Wrapping)

Никогда не пропускайте «сырые» исключения (`\Exception`, `\RuntimeException`, `Doctrine\ORM\OptimisticLockException`) прямо в HTTP-слой.

**Правило:** Каждый слой переводит исключения «нижнего» слоя в исключения «своего» слоя.

```php
class PaymentService
{
    public function __construct(private PaymentGatewayClient $client) {}

    public function charge(int $amount): void
    {
        try {
            // Внешний API может выбросить GuzzleException, ConnectionException и т.д.
            $this->client->charge($amount);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // ПЕРЕВОДИМ техническую ошибку в бизнес-контекст
            throw new PaymentGatewayTimeoutException(
                message: 'Payment gateway is not responding',
                previous: $e // Сохраняем причину!
            ) from $e;
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 402) {
                throw new PaymentGatewayDeclinedException('Payment declined') from $e;
            }
            // Если код не 402, это непредвиденная ошибка
            throw new IntegrationException('Unexpected error from payment gateway') from $e;
        }
    }
}
```

**Ключевые моменты:**
1.  `from $e` (или `previous: $e`) сохраняет цепочку причин. Это критически важно для отладки: в логах вы увидите, что `PaymentGatewayTimeoutException` был вызван из-за `ConnectionException`.
2.  Мы **не** отдаем пользователю текст `ConnectionException: Connection refused by 10.0.0.5:8080`. Мы отдаем «Внешний сервис недоступен».

---

### 3. Контракт метода: Что считается нормой?

Метод должен иметь ясный контракт. Есть три типа поведения:

1.  **Успех:** Возвращает значение.
2.  **Предсказуемая бизнес-ошибка:** Выбрасывает конкретное `BusinessException`. Вызывающий код **обязан** это обрабатывать (или понимать, что оно будет перехвачено выше).
3.  **Непредвиденная ошибка:** Выбрасывает `AppException` или наследника. Это баг или внешняя проблема.

**Пример контракта:**

```php
/**
 * Вычитает деньги с баланса.
 *
 * @throws InsufficientFundsException Если денег не хватает.
 * @throws UserNotFoundException Если пользователь не существует.
 * @throws AppException Если произошла непредвиденная техническая ошибка.
 *
 * @return void
 */
public function withdraw(int $userId, float $amount): void
{
    $user = $this->userRepository->find($userId);
    if (!$user) {
        throw new UserNotFoundException($userId);
    }

    if ($user->getBalance() < $amount) {
        throw new InsufficientFundsException($amount, $user->getBalance());
    }

    $user->setBalance($user->getBalance() - $amount);
    $this->userRepository->save($user);
}
```

**Вывод:** Вызывающий код знает, что он **может** получить `InsufficientFundsException`. Он знает, что это нормальный сценарий. Он **не обязан** оборачивать вызов в `try-catch`, если он не хочет обрабатывать этот случай локально — выше в стеке будет общий обработчик.

---

### 4. Антипаттерны (Ошибки, которые я вижу часто)

#### ❌ Антипаттерн 1: Исключения для потока управления
```php
// ПЛОХО
function getUser(int $id) {
    $user = $this->repo->find($id);
    if (!$user) {
        throw new UserNotFoundException($id); // Мы используем исключение, чтобы "выйти" из функции
    }
    return $user;
}

// ЛУЧШЕ (если null — это валидное состояние)
function getUserOrNull(int $id): ?User {
    return $this->repo->find($id);
}
```
*Если «нет пользователя» — это нормальный результат запроса, возвращайте `null` или `Option` (в PHP — `?Type`).*

#### ❌ Антипаттерн 2: Глобальный `catch (\Exception)` в каждом слое
```php
// ПЛОХО: Каждый сервис ловит всё и логирует
class OrderService {
    public function create() {
        try {
            $this->paymentService->charge();
        } catch (\Exception $e) {
            Logger::error($e->getMessage()); // Теряем контекст, не знаем, какая ошибка
            throw new AppException('Error'); // Слишком общее
        }
    }
}
```
*Ловите только то, что можете обработать или перевести. Пусть исключения летят наверх, пока не достигнут уровня, который их понимает.*

#### ❌ Антипаттерн 3: Возврат `null` вместо исключения для ошибок
```php
// ПЛОХО
function processPayment(): ?array {
    // ...
    if (declined) return null; // Почему? Дебит? Таймаут?
    return $result;
}

// Вызывающий код
$result = $this->processPayment();
if ($result === null) {
    // Что делать? Откат? Повторить? Показать ошибку?
    // Мы не знаем причину.
}
```
*Исключения несут причину. `null` — это просто отсутствие значения.*

#### ❌ Антипаттерн 4: Отладочная информация в сообщении исключения
```php
// ПЛОХО
throw new AppException('DB Error: Connection refused to 192.168.1.105, user=admin, pass=secret');
```
*Сообщение исключения уходит в лог И в ответ клиенту (если не обработано). Никогда не включайте чувствительные данные или внутренние IP в `message`.*

#### ❌ Антипаттерн 5: Создание исключений внутри цикла
```php
// ПЛОХО
foreach ($items as $item) {
    try {
        process($item);
    } catch (Exception $e) {
        throw new AppException('Item failed: ' . $item->id) from $e;
    }
}
```
*Если вы собираете ошибки по каждому элементу, лучше собрать массив ошибок и выбросить одно исключение в конце, или возвращать результат с списком ошибок. Иначе вы потеряете первый же баг, а остальные не обработаете.*

---

### 5. Как я обрабатываю исключения в HTTP-слое (Controller/Kernel Event)

В Symfony или другом фреймворке есть **один** глобальный обработчик, который переводит `AppException` в HTTP-ответ.

```php
// Listener или Middleware
public function onException(Exception $e): Response
{
    if ($e instanceof InsufficientFundsException) {
        return $this->json([
            'error' => 'insufficient_funds',
            'required' => $e->getRequired(),
            'available' => $e->getAvailable()
        ], 422);
    }

    if ($e instanceof UserNotFoundException) {
        return $this->json(['error' => 'user_not_found'], 404);
    }

    if ($e instanceof PaymentGatewayTimeoutException) {
        return $this->json(['error' => 'external_service_unavailable'], 503);
    }

    if ($e instanceof AppException) {
        return $this->json(['error' => 'business_error'], 400);
    }

    // Всё остальное — это баг или непредвиденная ошибка
    // Логировать подробно, пользователю — стандартное сообщение
    $this->logger->critical('Unhandled exception', ['exception' => $e]);
    return $this->json(['error' => 'internal_server_error'], 500);
}
```

**Итог:**
1.  **Свои иерархии** — да, от общего к частному.
2.  **Оборачивание** — каждый слой переводит ошибки в свой контекст.
3.  **Контракт** — документирование `@throws` в DocBlock.
4.  **Антипаттерны** — не использовать для потока управления, не логировать чувствительные данные, не ловить всё подряд.

Хотите, я покажу, как это реализовать в реальном Symfony-приложении с `EventSubscriber`?

> ⏱ 18.4s · prompt 4451 tok · output 2614 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему «опыту» (а точнее, по анализам огромного массива кода, который я видел), чаще всего нарушается **SRP (Single Responsibility Principle — Принцип единственной ответственности)**.

### Почему именно он?

Потому что нарушение SRP часто **маскируется под логичность**. Разработка идет быстро, и «проще добавить проверку в тот же класс, чем создавать новый». Но со временем один класс начинает делать три вещи:
1.  **Логика бизнеса** (расчет, правила).
2.  **Работа с инфраструктурой** (отправка email, запись в БД, вызов API).
3.  **Форматирование вывода** (генерация HTML, JSON, PDF).

Это приводит к тому, что при изменении *любого* из этих трех аспектов вам приходится менять один и тот же файл, и вы рискуете сломать две другие функции.

---

### Типовой пример нарушения

**Задача:** Класс `User` (или `Account`), который должен:
1.  Хранить данные пользователя.
2.  Отправлять приветственное письмо при создании.
3.  Генерировать отчет о действиях пользователя (PDF).
4.  Валидировать пароль.

#### ❌ Плохой код (Нарушение SRP)

```php
class User
{
    private string $email;
    private string $password;
    private array $activities;
    
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $this->hashPassword($password);
        $this->sendWelcomeEmail(); // Инфраструктура: отправка письма
    }

    private function hashPassword(string $password): string
    {
        // Логика валидации и хеширования
        if (strlen($password) < 8) {
            throw new Exception('Password too short');
        }
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function sendWelcomeEmail(): void
    {
        // Инфраструктура: отправка email
        // Здесь спрятана зависимость от SMTP, шаблонов, очереди
        $mailer = new Mailer(); 
        $mailer->send($this->email, 'Welcome', 'Hello!');
    }

    public function generateReport(): string
    {
        // Логика генерации PDF/HTML отчета
        // Здесь спрятана зависимость от шаблонов, библиотек PDF
        $html = "<h1>Report for {$this->email}</h1>";
        foreach ($this->activities as $activity) {
            $html .= "<p>{$activity['action']}</p>";
        }
        return $this->renderPdf($html); // Инфраструктура: рендеринг
    }

    private function renderPdf(string $html): string
    {
        // Зависимость от конкретной библиотеки PDF
        return (new PdfRenderer())->render($html);
    }
}
```

**Проблемы:**
1.  **Трудно тестировать:** Чтобы протестировать логику пароля, вы должны мокализовать отправку email и генерацию PDF.
2.  **Трудно менять:** Хотите сменить SMTP-провайдера? Меняете `User`. Хотите изменить формат отчета? Меняете `User`.
3.  **Скрытые зависимости:** Класс знает о существовании `Mailer` и `PdfRenderer`, хотя ему это не нужно по своей сути (Entity).

---

### ✅ Рефакторинг (Соблюдение SRP)

Мы разбиваем ответственности на отдельные классы, каждый из которых имеет **одну причину меняться**.

#### 1. Модель (только данные и базовая бизнес-логика)

```php
class User
{
    private string $email;
    private string $password;
    private array $activities = [];

    public function __construct(
        private readonly string $email,
        private readonly string $hashedPassword
    ) {}

    public function getEmail(): string { return $this->email; }
    public function getHashedPassword(): string { return $this->hashedPassword; }
    
    public function addActivity(string $action): void
    {
        $this->activities[] = [
            'action' => $action,
            'time' => time()
        ];
    }

    public function getActivities(): array
    {
        return $this->activities;
    }
}
```
**Ответственность:** Хранение состояния пользователя. **Причина меняться:** Изменение структуры данных пользователя.

#### 2. Сервис создания (логика + оркестрация)

```php
class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private EventDispatcher $eventDispatcher
    ) {}

    public function createUser(string $email, string $plainPassword): User
    {
        // Валидация и хеширование (SRP: PasswordHasher отвечает за это)
        $hashedPassword = $this->passwordHasher->hash($plainPassword);

        $user = new User($email, $hashedPassword);
        $this->userRepository->save($user);

        // Отправляем событие (SRP: EventDispatcher решает, кто реагирует)
        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));

        return $user;
    }
}
```
**Ответственность:** Оркестрация процесса создания. **Причина меняться:** Изменение бизнес-процесса создания.

#### 3. Отдельные обработчики (инфраструктура)

```php
// Ответственность: Отправка писем. Причина меняться: Изменение SMTP, шаблонов.
class UserCreatedEmailListener
{
    public function __construct(private Mailer $mailer) {}

    public function onUserCreated(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $this->mailer->send($user->getEmail(), 'Welcome', 'Hello!');
    }
}

// Ответственность: Генерация отчетов. Причина меняться: Изменение формата PDF.
class UserReportGenerator
{
    public function __construct(private PdfRenderer $renderer) {}

    public function generate(User $user): string
    {
        $html = $this->buildHtml($user);
        return $this->renderer->render($html);
    }

    private function buildHtml(User $user): string
    {
        // ... логика сборки HTML
        return "<html>...</html>";
    }
}
```

---

### Почему это лучше?

1.  **Тестируемость:**
    *   `User` тестируется как чистая модель (без mock'ов).
    *   `UserService` тестируется с mock'ами репозитория и диспетчера событий.
    *   `UserCreatedEmailListener` тестируется с mock'ом `Mailer`.
    *   `UserReportGenerator` тестируется с mock'ом `PdfRenderer`.

2.  **Подмена инфраструктуры:**
    *   Хотите уйти от SMTP к Push-уведомлениям? Напишите новый `UserCreatedPushListener`. `User` и `UserService` не изменятся.
    *   Хотите сменить PDF-библиотеку? Поменяйте реализацию `PdfRenderer`. `UserReportGenerator` останется тем же.

3.  **Явные зависимости:**
    *   Зависимости теперь явны (через конструкторы), а не скрыты в теле методов.

### Резюме

**SRP** — это не про «один метод в классе». Это про **«одна причина для изменения»**. Если вы меняете код, потому что поменялась логика бизнеса, и в том же файле меняете код, потому что поменялся формат email — у вас нарушение SRP.

Нарушение SRP часто приводит к нарушению других принципов SOLID (DIP, OCP), потому что класс начинает зависеть от конкретных реализаций (Mailer, PdfRenderer), а не от абстракций.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно. Более того, в реальном продакшене принципы SOLID — это не заповеди, а **инструменты для борьбы с энтропией**. Их слепое следование часто приводит к избыточной сложности (over-engineering), которая дороже, чем сами проблемы, которые она решает.

SOLID — это эвристики (правила большого пальца), а не законы физики. Главное правило: **Code should be optimized for the reader, and the reader is a human developer.** (Код оптимизируется под читателя, а читатель — это человек).

### Пример оправданного нарушения: SRP (Single Responsibility Principle)

**Ситуация:** У нас есть внутренний сервис, который делает ровно одну вещь: считает сумму заказа с учетом налогов и скидок.

**«Чистое» нарушение SRP:**
Обычно мы бы разделили это на:
1.  `OrderTotalsCalculator` (бизнес-логика)
2.  `TaxService` (расчет налогов)
3.  `DiscountService` (расчет скидок)
4.  `CurrencyConverter` (конвертация валют)

Но в нашем случае:
*   Формула расчета проста: `сумма - скидка + налог`.
*   Формула **не меняется** годами.
*   Она **не используется** нигде больше.
*   Бизнес-правила налогов и скидок жестко связаны и часто меняются **одновременно** (например, новый тип скидок зависит от региона, а налог зависит от типа товара).

#### ❌ «Идеальный» SOLID-код (слишком сложный)

```php
class OrderTotal
{
    public function __construct(
        private float $subtotal,
        private float $discount,
        private float $tax
    ) {}
}

class OrderTotalsCalculator
{
    public function __construct(
        private TaxService $taxService,
        private DiscountService $discountService
    ) {}

    public function calculate(Order $order): OrderTotal
    {
        // 1. Получаем скидки (зависимость)
        $discount = $this->discountService->calculate($order);
        
        // 2. Получаем налоги (зависимость)
        $tax = $this->taxService->calculate($order);
        
        // 3. Считаем итог
        $total = $order->getSubtotal() - $discount + $tax;
        
        return new OrderTotal($total, $discount, $tax);
    }
}
```

**Почему это плохо здесь:**
*   Мы создали 3 новых класса для одной формулы.
*   Мы внедрили зависимости, которые в 90% случаев не нужно менять.
*   Новому разработчику нужно прочитать 4 файла, чтобы понять, как считается цена.
*   **Стоимость поддержки > Выгоды.**

#### ✅ Оправданное нарушение SRP

```php
class Order
{
    public function __construct(
        private float $subtotal,
        private float $discount,
        private float $taxRate, // Простое значение, а не сервис
        private float $regionId
    ) {}

    /**
     * Считает итоговую стоимость заказа.
     * 
     * Бизнес-правила:
     * - Скидка вычитается из подытога.
     * - Налог рассчитывается от (подытог - скидка).
     * - Специальные правила для региона 42: налог +1%.
     */
    public function calculateTotal(): float
    {
        $base = $this->subtotal - $this->discount;
        
        // Локальное бизнес-правило, которое редко меняется
        $effectiveTaxRate = ($this->regionId === 42) ? ($this->taxRate + 0.01) : $this->taxRate;
        
        $tax = $base * $effectiveTaxRate;
        
        return $base + $tax;
    }
}
```

**Почему это оправданно:**
1.  **Локальность контекста:** Все правила расчета находятся в одном месте. Чтобы понять, как считается цена, нужно прочитать один метод.
2.  **Нет абстракций ради абстракции:** `TaxService` и `DiscountService` не нужны, потому что логика тривиальна.
3.  **Изменение:** Если формула изменится (например, добавится новая скидка), вы измените **один** метод. Это проще, чем менять 3 класса и их связности.
4.  **Читаемость:** Код читается как документация.

---

### Другие примеры оправданных нарушений

#### 1. LSP (Liskov Substitution Principle)
**Нарушение:** Класс-наследник расширяет поведение, а не заменяет его.

**Когда это ОК:**
```php
class BaseNotification {}

class EmailNotification extends BaseNotification
{
    public function send(): void {
        // Отправка email
    }
    
    // ДОБАВЛЯЕТ новое поведение, которое нет в базовом классе
    public function trackOpen(): void {
        // Трекинг открытия
    }
}
```
**Почему это ОК:** Если `BaseNotification` — это просто маркер (тег) для системы рассылок, а `trackOpen()` используется только в контексте email, то это не нарушение LSP в классическом понимании, а **расширение возможностей**. Клиентский код знает, что у него `EmailNotification`, и вызывает `trackOpen()`. Он не пытается подставить `BaseNotification` туда, где ожидает только `send()`.

#### 2. DRY (Don't Repeat Yourself) — это НЕ SOLID, но часто путают
**Нарушение:** Намеренное дублирование кода.

**Когда это ОК:**
*   Дублирование **простых** констант (например, `const MAX_RETRY = 3;` в разных классах).
*   Дублирование **небольших** фрагментов логики (5–10 строк), которые могут разойтись по смыслу в будущем.
*   **Правило:** DRY (Don't Repeat Yourself) на самом деле означает **"Don't Repeat Yourself" (не повторяй себя в одном месте изменения)**. Если два кусок кода имеют **разные причины меняться**, их нельзя объединять!

**Пример:**
```php
// В OrderService
public function createOrder(): void {
    // ...
    $this->logger->info('Order created');
}

// В PaymentService
public function chargePayment(): void {
    // ...
    $this->logger->info('Payment charged');
}
```
Не стоит выносить `logger->info()` в общий `LogService`, потому что контексты разные. Это не DRY, это **DRY в голове** (намеренное повторение).

---

### Критерии: Когда нарушать SOLID?

Нарушение оправдано, если:

1.  **Код становится проще для понимания.** (Главный критерий!)
2.  **Изменения происходят редко.** (Если логика статична, абстракции не нужны).
3.  **Зависимости тривиальны.** (Если `TaxService` — это одна функция, лучше инлайнить её).
4.  **Вы избегаете «страницы и полстраницы».** (Когда для одной операции нужно открыть 5 файлов).

### Как проверить, что вы не переусердствовали?

**Тест разработчика:**
> «Если бы я завтра уволился, мог бы новый разработчик понять, как работает этот кусок кода, не читая другие файлы?»

Если да — значит, вы правильно сгруппировали ответственность, даже если нарушили формальный SRP.

**Вывод:**
SOLID — это не религия. Это набор инструментов. Используйте их, когда они снижают сложность. Не используйте, когда они **увеличивают** сложность. **Простота — главная добродетель.**

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Отличный вопрос. Открыто-закрытый принцип (Open/Closed Principle) гласит: **«Модули должны быть открыты для расширения, но закрыты для изменения»**.

Самый известный способ — наследование (подклассы, переопределение методов), но в современном PHP это часто антипаттерн (хрупкая база, глубина вложенности, `is-a` вместо `has-a`).

Вот альтернативные, часто более чистые способы реализации OCP в PHP:

---

### 1. Стратегия (Strategy Pattern) + DI
Вместо того чтобы менять родительский класс, вы **внедряете** разные реализации поведения.

**Сценарий:** Отправка уведомлений. Нужно добавить Push, SMS, Email — но не менять код, который их вызывает.

#### ❌ С наследованием (OCP через `is-a`)
```php
abstract class Notification {
    abstract public function send(User $user, string $message): void;
}

class EmailNotification extends Notification {
    public function send(User $user, string $message): void {
        // Email logic
    }
}

class PushNotification extends Notification {
    public function send(User $user, string $message): void {
        // Push logic
    }
}

class Notifier {
    public function notify(User $user, string $message): void {
        // ПЛОХО: Выбрасываем исключение или если-цифру, чтобы решить, что делать
        if ($user->getPreferredChannel() === 'email') {
            (new EmailNotification())->send($user, $message);
        } elseif ($user->getPreferredChannel() === 'push') {
            (new PushNotification())->send($user, $message);
        }
        // Каждый новый канал = изменение Notifier
    }
}
```

#### ✅ Через Strategy (OCP через `has-a`)
```php
// 1. Абстракция поведения
interface NotificationStrategy {
    public function send(User $user, string $message): void;
}

// 2. Конкретные реализации (новые каналы = новые классы)
class EmailStrategy implements NotificationStrategy {
    public function send(User $user, string $message): void {
        // Email logic
    }
}

class PushStrategy implements NotificationStrategy {
    public function send(User $user, string $message): void {
        // Push logic
    }
}

class SmsStrategy implements NotificationStrategy {
    public function send(User $user, string $message): void {
        // SMS logic
    }
}

// 3. Контейнер стратегий (открыт для расширения)
class NotificationStrategyFactory {
    private array $strategies = [];

    public function __construct(iterable $strategies) {
        foreach ($strategies as $strategy) {
            $this->register($strategy);
        }
    }

    private function register(NotificationStrategy $strategy): void {
        // Можно регистрировать по типу или по тегу
        $this->strategies[] = $strategy;
    }

    public function get(string $channel): NotificationStrategy {
        return match($channel) {
            'email' => $this->strategies['email'],
            'push' => $this->strategies['push'],
            'sms' => $this->strategies['sms'],
            default => throw new UnknownChannelException($channel)
        };
    }
}

// 4. Клиентский код (ЗАКРЫТ для изменения)
class Notifier {
    public function __construct(private NotificationStrategyFactory $factory) {}

    public function notify(User $user, string $message): void {
        $strategy = $this->factory->get($user->getPreferredChannel());
        $strategy->send($user, $message);
        // Чтобы добавить SMS, мы НЕ меняем Notifier.
        // Мы просто регистрируем SmsStrategy в фабрике (DI Container).
    }
}
```
**Почему это OCP?**
- Чтобы добавить новый канал (например, `Telegram`), вы создаёте `TelegramStrategy` и регистрируете его в контейнере.
- Код `Notifier` и `NotificationStrategyFactory` **не меняется**.

---

### 2. Реестры и Плагины (Registry / Plugin Architecture)
Это эволюция стратегии, где расширение происходит через **динамическую регистрацию**.

**Сценарий:** Правила валидации форм. Новые правила = новые классы, без изменения ядра.

```php
// Интерфейс правила
interface ValidationRule {
    public function validate(string $value): bool;
    public function getName(): string;
}

// Реестр правил (открыт для расширения)
class RuleRegistry {
    private array $rules = [];

    public function register(string $name, ValidationRule $rule): void {
        $this->rules[$name] = $rule;
    }

    public function getRule(string $name): ValidationRule {
        return $this->rules[$name] ?? throw new UnknownRuleException($name);
    }
}

// Ядро (закрыто для изменения)
class FormValidator {
    public function __construct(private RuleRegistry $registry) {}

    public function validate(string $field, string $value, array $rules): void {
        foreach ($rules as $ruleName) {
            $rule = $this->registry->getRule($ruleName);
            if (!$rule->validate($value)) {
                throw new ValidationException("Field {$field} failed rule {$ruleName}");
            }
        }
    }
}

// Расширение: добавляем новое правило
class PhoneRule implements ValidationRule {
    public function validate(string $value): bool {
        return preg_match('/^\+?[\d\s-]{10,15}$/', $value);
    }
    public function getName(): string { return 'phone'; }
}

// Регистрация (можно делать в конфиге, в DI-контейнере, или через атрибуты)
$registry = new RuleRegistry();
$registry->register('phone', new PhoneRule());
// Ядро FormValidator не меняется
```

---

### 3. События (Event-Driven)
Классический способ реализовать OCP: **выбрасывает событие, другие модули реагируют**.

**Сценарий:** При заказе нужно: отменить резерв, уведомить логистику, отправить email, добавить баллы.

#### ✅ Через события
```php
class Order {
    public function place(): void {
        // Бизнес-логика заказа
        $this->status = 'placed';
        
        // Издаём событие (ЗАКРЫТО для изменения: мы не знаем, кто слушает)
        event(new OrderPlacedEvent($this));
    }
}

// Слушатели (ОТКРЫТО для расширения: новые слушатели = новые файлы)
class CancelReservationListener {
    public function onOrderPlaced(OrderPlacedEvent $event): void {
        // Логика отмены резерва
    }
}

class SendOrderEmailListener {
    public function onOrderPlaced(OrderPlacedEvent $event): void {
        // Логика отправки email
    }
}

class AddLoyaltyPointsListener {
    public function onOrderPlaced(OrderPlacedEvent $event): void {
        // Логика начисления баллов
    }
}
```
**Почему это OCP?**
- Чтобы добавить «отправить SMS», создаёте `SendSmsListener`.
- Код `Order` **не меняется**.
- Вы полностью развязываете бизнес-логику от побочных эффектов.

---

### 4. Команды (Command Pattern)
Похоже на события, но более структурировано. Каждая операция — объект.

```php
interface Command {
    public function execute(): void;
}

class CreateOrderCommand implements Command {
    public function __construct(private Order $order) {}
    public function execute(): void {
        // Логика
    }
}

class OrderHandler {
    public function __construct(private Command $command) {}
    public function handle(): void {
        $this->command->execute();
    }
}
```
- Новый тип заказа = новый `Command`.
- `OrderHandler` не меняется.

---

### 5. Атрибуты (Attributes) + Рефлексия (PHP 8+)
Современный PHP позволяет расширять поведение через **метаданные**.

**Сценарий:** Маршализация DTO в JSON.

```php
// Атрибут
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SerializeAs {
    public function __construct(public string $alias) {}
}

// DTO
class UserDto {
    #[SerializeAs('user_id')]
    public int $id;

    #[SerializeAs('email_address')]
    public string $email;
}

// Универсальный сериализатор (ЗАКРЫТ для изменения)
class JsonSerializer {
    public function serialize(object $obj): array {
        $result = [];
        $reflection = new \ReflectionObject($obj);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(SerializeAs::class);
            $alias = $attributes[0]->newInstance()->alias ?? $property->getName();
            $result[$alias] = $property->getValue($obj);
        }

        return $result;
    }
}
```
- Чтобы добавить новое правило сериализации, вы создаёте новый атрибут (например, `#[Exclude]`) и меняете **один** метод в `JsonSerializer`.
- Но если у вас есть реестр **адаптеров сериализации**, то и это можно расширить без изменения ядра.

---

### 6. Настройки через Конфиг/Environment (Configuration over Code)
Иногда OCP — это просто **внешняя конфигурация**.

```php
class CacheDriver {
    private array $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function getDriver(): string {
        return $this->config['driver']; // 'redis', 'memcached', 'file'
    }
}
```
- Чтобы сменить драйвер, вы меняете `.env`, а не код.
- Это тривиальный OCP: система открыта для расширения (новых значений конфига), закрыта для изменения (логика выбора драйвера одна и та же).

---

### Итог: Как выбрать способ?

| Способ | Когда использовать |
|--------|-------------------|
| **Стратегия + DI** | Когда есть несколько вариантов **одного и того же** поведения (отправка, расчёт, валидация). |
| **Реестр/Плагины** | Когда набор расширений **открытый и неограниченный** (правила, хуки, обработчики). |
| **События** | Когда нужно **развязать** ядро от побочных эффектов (логирование, уведомления, интеграции). |
| **Команды** | Когда действия **структурированы** и требуют отмены/повтора (CQRS, очереди). |
| **Атрибуты** | Когда расширение связано с **метаданными** (маршализация, роутинг, валидация). |

**Главный вывод:** Наследование — один из способов OCP, но часто **не лучший**. В современном PHP чаще используют **составление (composition)**, **внедрение (DI)** и **события (events)**, потому что они лучше соответствуют принципу «Открыто для расширения, закрыто для изменения» без создания хрупких иерархий классов.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, **SRP (Single Responsibility Principle) применяется не только к классам**. Это одна из самых частых концептуальных ошибок при изучении SOLID.

В классической формулировке Бертрана Мейера (из книги «Object-Oriented Software Construction») SRP описывается так:

> «Класс должен иметь **одну причину** меняться» (A class should have only one reason to change).

Но давайте разберём это глубже.

### 1. SRP — это про «Причину для изменения», а не про «Количество методов»

**Неправильное понимание:**
«В классе должен быть только один метод» или «Одна ответственность = одна функция».

**Правильное понимание:**
«Если вы меняете код в этом модуле, вы должны изменять его **только по одной** логической причине».

Если в одном классе есть два куска кода, которые меняются **разными причинами**, то это нарушение SRP.

### 2. К чему ещё применяется SRP?

#### А. К методам (Functions/Methods)
Метод может нарушать SRP, если он делает две независимые вещи.

**Пример:**
```php
// ПЛОХО: Метод делает две разные вещи
public function processOrder(Order $order): void {
    // 1. Валидация данных заказа (Причина изменения: правила валидации)
    if ($order->getItems()->count() === 0) {
        throw new EmptyOrderException();
    }

    // 2. Расчёт суммы (Причина изменения: правила расчёта)
    $total = 0;
    foreach ($order->getItems() as $item) {
        $total += $item->getPrice() * $item->getQuantity();
    }

    // 3. Отправка на склад (Причина изменения: интеграция со складом)
    $this->warehouse->receive($order, $total);
}
```
Здесь три разные «причины изменения». Если изменится правило валидации — вы трогаете этот метод. Если изменится формула расчёта — вы трогаете этот метод. Если поменяется API склада — вы трогаете этот метод.

**Решение:** Вынести валидацию, расчёт и отправку в отдельные методы или классы.

#### Б. К модулям / файлам
Если вы используете файловую систему, один файл может нарушать SRP, если он содержит несколько независимых сущностей.

**Пример:**
`src/Services/UserService.php`
```php
class UserService {
    // Логика работы с пользователями
}

class AdminUserManager {
    // Логика работы с админами
}

class UserCache {
    // Логика работы с кэшем
}
```
Три разных класса в одном файле = три разные причины менять файл. Если вы меняете логику кэша, вы должны трогать файл, где лежат и `UserService`, и `AdminUserManager`. Это нарушение SRP на уровне модуля.

#### В. К функциям в скриптах (Procedural PHP)
Даже в процедурном коде SRP работает.

```php
// ПЛОХО: Одна функция делает три вещи
function processReport(Request $request): Response {
    // 1. Загрузка данных из БД
    $data = $db->query("SELECT * FROM reports");
    
    // 2. Форматирование в CSV
    $csv = formatToCsv($data);
    
    // 3. Отправка HTTP-ответа
    return new Response($csv, 'text/csv');
}
```
Здесь три разные «причины изменения». Лучше разбить на `loadData()`, `formatData()`, `sendResponse()`.

#### Г. К микросервисам / API-эндпоинтам
На уровне архитектуры SRP тоже работает.

**Пример:**
`POST /api/orders` эндпоинт, который:
1. Создаёт заказ.
2. Отправляет email пользователю.
3. Обновляет аналитику.

Если изменится формат email — вы меняете код эндпоинта. Если изменится логика аналитики — вы меняете код эндпоинта. Это нарушение SRP на уровне API.

**Решение:** Разделить на микросервисы или использовать события (Event-Driven), чтобы каждый модул отвечал только за свою часть.

### 3. Как определить, есть ли нарушение SRP?

Задайте вопрос: **«Если завтра изменится <X>, мне нужно будет менять этот код?»**

- Если ответ «Да, и для <X> и для <Y> и для <Z>» — скорее всего, нарушение SRP.
- Если ответ «Да, только для <X>» — SRP соблюдён.

### 4. Важное уточнение: «Single Responsibility» ≠ «Single Method»

Частый миф: «В классе должен быть один метод».
Это **неправда**.

Класс может иметь много методов, если они все связаны с **одной и той же ответственностью**.

**Пример:**
```php
class Money {
    public function __construct(private int $amount, private string $currency) {}
    
    public function add(Money $other): Money {
        // Логика сложения
    }
    
    public function subtract(Money $other): Money {
        // Логика вычитания
    }
    
    public function multiply(int $factor): Money {
        // Логика умножения
    }
    
    public function getAmount(): int {
        return $this->amount;
    }
    
    public function getCurrency(): string {
        return $this->currency;
    }
}
```
Здесь много методов, но **одна ответственность**: быть денежной величиной. Все методы связаны с одной «причиной изменения» — изменением правил работы с деньгами. Это **не** нарушение SRP.

### Итог

| Уровень | Пример нарушения SRP |
|---------|---------------------|
| **Класс** | `Order` содержит логику расчёта налогов, отправки email и сохранения в БД. |
| **Метод** | `processOrder()` валидирует, считает сумму и отправляет на склад. |
| **Файл** | `helpers.php` содержит утилиты для даты, хеширования и парсинга HTML. |
| **API** | Эндпоинт `/api/users` создаёт пользователя, отправляет email и запускает кампанию маркетинга. |
| **Модуль** | Библиотека `payment` содержит логику расчёта налогов и отправки push-уведомлений. |

**Вывод:** SRP — это принцип **разделения ответственностей** на любой уровне абстракции. Он помогает вам изолировать изменения, чтобы они не влияли на не связанные части системы.

> ⏱ 13.4s · prompt 7117 tok · output 1832 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Давайте разберём «кухню» DI-контейнера в Symfony. Это один из самых сложных и при этом самых эффективных механизмов фреймворка.

### Краткая суть
Контейнер Symfony — это **фабрика объектов**, которая использует **рефлексию** и **автоинъекцию (autowiring)**, чтобы собрать объект вместе со всеми его зависимостями без явного описания каждой связи.

---

### 1. Что такое контейнер?

Контейнер — это объект-реестр, который знает:
1. Какие сервисы (классы) зарегистрированы.
2. Какие у них аргументы (dependencies).
3. Как они связаны друг с другом.

В Symfony контейнер на 90% **генерируется автоматически** из конфигурации (YAML/XML/PHP) и атрибутов.

---

### 2. Три ключевых механизма

#### A. Autowiring (Автоинъекция)
Это «магия», которая позволяет не писать явные зависимости.

**Как это работает:**
1. Вы создаёте класс:
```php
class NotificationService
{
    public function __construct(
        private MailerInterface $mailer, // Типизированное свойство
        private LoggerInterface $logger  // Типизированное свойство
    ) {}
}
```
2. Symfony видит типы параметров (`MailerInterface`, `LoggerInterface`).
3. Он ищет в контейнере сервисы, которые реализуют эти интерфейсы.
4. Если находит один — инжектит его. Если находит несколько — нужна **тегированная инъекция** или **название аргумента**.

**Важно:** Autowiring работает только через **типы** (class, interface, alias). Он не может угадать, какой конкретный класс нужен, если реализаций несколько.

#### B. Autoconfiguration
Это «догадка» Symfony о том, как должен вести себя сервис.

**Пример:**
Если ваш класс реализует интерфейс `EventSubscriberInterface` (или имеет атрибут `#[AsEventListener]`), Symfony автоматически:
1. Зарегистрирует его как сервис.
2. Подпишет его на события.

Это экономит кучу конфигурации.

#### C. Binding (Привязка)
Механизм для явной подстановки значений без явного `arguments:`.

```yaml
services:
  _defaults:
    bind:
      string $envName: '%env(APP_ENV)%'
      array $config: '%kernel.config%'
```

Теперь любой сервис может принимать `string $envName` в конструкторе, и Symfony подставит правильное значение.

---

### 3. Компилляция контейнера (Compile Phase)

Это самое важное. Контейнер **не работает** как динамический реестр в рантайме (хотя и может, но это медленно).

**Процесс:**
1. **Загрузка конфигурации:** Symfony читает `services.yaml`, `config/services/*.yaml`, атрибуты классов.
2. **Разрешение зависимостей (Resolution):**
   - Для каждого сервиса Symfony строит **граф зависимостей**.
   - Проверяет, можно ли autowire все параметры.
   - Если есть циклические зависимости — ошибка.
3. **Генерация кода (Code Generation):**
   - Symfony **генерирует PHP-классы** в каталог `var/cache/dev/` (или `prod`).
   - Например, создаёт файл `App_KernelDevDebugContainer.php`.
   - В этом файле для каждого сервиса есть **фабричный метод**.

**Пример сгенерированного кода (упрощённо):**
```php
// var/cache/dev/App_KernelDevDebugContainer.php
public function getNotificationService(): NotificationService
{
    return $this->services['app.notification_service'] ??= new NotificationService(
        $this->getMailerInterface(), // Инжектим Mailer
        $this->getLoggerInterface()  // Инжектим Logger
    );
}
```

**Вывод:** На этапе рантайма Symfony **не ищет** зависимости. Он просто вызывает сгенерированные методы. Это **очень быстро**.

---

### 4. Момент, когда сервис запрошен впервые (Lazy Loading)

Когда вы вызываете `$this->container->get(NotificationService::class)` или autowiring в контроллере:

1. **Проверка кэша:** Контейнер проверяет, существует ли уже экземпляр сервиса в массиве `$this->services`.
2. **Если нет:** Вызывается сгенерированный фабричный метод (см. выше).
3. **Создание зависимостей:**
   - Если `MailerInterface` ещё не создан — создаётся он.
   - Если `LoggerInterface` ещё не создан — создаётся он.
4. **Возврат экземпляра:** Сервис сохраняется в кэше контейнера.

**Важно:** По умолчанию сервисы в Symfony — **синглтоны** (singleton). Если вы запросите `NotificationService` два раза, вы получите **один и тот же объект**.

Если вам нужен новый экземпляр каждый раз — используйте `shared: false` в конфигурации.

---

### 5. Что происходит при `Autowiring` в рантайме?

**Ничего.** Autowiring — это **compile-time** механизм.

В рантайме контейнер просто знает:
> «Для `NotificationService` нужны `MailerInterface` и `LoggerInterface`. Я уже знаю, как их создать, потому что я сгенерировал код на этапе компиляции».

**Исключение:** Если вы используете **dynamic registration** (регистрация сервисов в рантайме через `->register()`), то в этом случае autowiring работает через рефлексию и поиск по интерфейсам. Но это **медленно** и не рекомендуется для продакшена.

---

### 6. Типичные проблемы и их причины

#### 1. «Autowired service not found»
**Причина:** Symfony не может найти реализацию интерфейса.
**Решение:**
- Убедитесь, что класс реализует интерфейс и зарегистрирован (или может быть autoconfigured).
- Используйте `@Autowired` с явным алиасом, если реализаций несколько.
- Проверьте, нет ли опечаток в импорте.

#### 2. Циклическая зависимость (Circular Dependency)
```php
class A {
    public function __construct(private B $b) {}
}
class B {
    public function __construct(private A $a) {}
}
```
**Решение:**
- Разорвать цикл (перенести логику в третий класс).
- Использовать `lazy` (ленивое подключение) — Symfony создаст прокси, который не инициализирует B, пока не будет вызван метод A.

#### 3. Service not found for specific parameter
**Причина:** Autowiring не смог угадать, какой из двух сервисов, реализующих интерфейс, нужен.
**Решение:**
```yaml
services:
  my.service:
    arguments:
      $mailer: '@mailer.sendgrid' # Явное указание
```

---

### 7. Сравнение: Symfony vs. Laravel vs. Raw PHP

| Фича | Symfony | Laravel | Raw PHP |
|------|---------|---------|---------|
| Autowiring | ✅ (через DI) | ✅ (через DI) | ❌ (ручной) |
| Компилляция | ✅ (генерация кода) | ❌ (рефлексия в рантайме) | ❌ |
| Скорость | 🚀 (быстро) | 🐢 (медленнее) | 🐢 (медленно) |
| Гибкость | ⚙️ (высокая) | ⚙️ (средняя) | 🧩 (низкая) |

**Вывод:** Symfony использует **AOP (Aspect-Oriented Programming)** и **Code Generation** для максимальной производительности. Laravel использует **Reflexion** для гибкости. Symfony «запоминает» зависимости на этапе сборки, Laravel «запоминает» их в рантайме.

---

### Итог: Как это работает шаг за шагом

1. **Вы пишете код:**
```php
class OrderService {
    public function __construct(private PaymentService $payment) {}
}
```

2. **Сборка (Compile):**
   - Symfony видит `PaymentService`.
   - Ищет `PaymentService` в контейнере.
   - Находит его.
   - Генерирует метод:
   ```php
   public function getOrderService(): OrderService {
       return new OrderService($this->getPaymentService());
   }
   ```

3. **Рантайм (Request):**
   - Контроллер запрашивает `OrderService`.
   - Контейнер вызывает `getOrderService()`.
   - Тот вызывает `getPaymentService()`.
   - Если `PaymentService` ещё не создан — создаётся.
   - Возвращается готовый `OrderService`.

**Главное правило:** Контейнер Symfony — это **не «магия»**, а **оптимизированная фабрика**. Он работает быстро, потому что **не думает** в рантайме, а просто **делает то, что было заранее вычислено**.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это архитектурный красный флаг. Он говорит о том, что ваши сервисы нарушают принцип **единого направления потока данных** (Dependency Rule) и обычно — **SRP**.

Symfony *может* это решить техническими ухищрениями (lazy proxies), но **правильное** решение всегда заключается в **перепроектировании**. Ниже — от «плохих костылей» к «чистым решениям».

---

### ❌ Почему это плохо

```php
class OrderService {
    public function __construct(private InventoryService $inventory) {}
    public function create() { /* ... */ }
}

class InventoryService {
    public function __construct(private OrderService $orderService) {}
    public function check() { /* ... */ }
}
```

Здесь `OrderService` зависит от `InventoryService`, а тот в обратно от `OrderService`. Это **цикл**.

---

### 🛠 Техническое решение Symfony: `lazy` (Не рекомендую, но работает)

Symfony может создать **прокси** (Proxy), который откладывает инициализацию зависимого объекта до первого вызова метода.

```yaml
services:
  App\Service\OrderService:
    arguments:
      $inventory: '@!lazy!App\Service\InventoryService'
    
  App\Service\InventoryService:
    arguments:
      $orderService: '@!lazy!App\Service\OrderService'
```

**Как это работает:**
1. `OrderService` создаётся.
2. В его конструктор передаётся не `InventoryService`, а **прокси** (`__Proxy__InventoryService`).
3. Прокси не создаёт реальный объект, пока вы не вызовете метод `$this->inventory->check()`.
4. Если метод не вызывается — `InventoryService` вообще не создаётся, и цикл разрывается.

**Почему это плохая идея:**
- Скрывает архитектурный дефект.
- Сложно отлаживать (прокси добавляют накладные расходы).
- Если вы вызовете метод — цикл всё равно сработает, и вы получите `CircularReferenceException`.

---

### ✅ Чистые решения (Рефакторинг)

#### 1. Извлечение общей логики в третий сервис (Композиция)

Самый частый случай: два сервиса делят общую логику или данные.

**Было:**
- `A` вызывает `B` для проверки.
- `B` вызывает `A` для получения контекста.

**Решение:** Создайте `ContextProvider` или `DataResolver`.

```php
// Новый класс
class OrderContext {
    public function __construct(
        private OrderRepository $orderRepo,
        private InventoryService $inventory
    ) {}

    public function getValidationData(int $orderId): array {
        $order = $this->orderRepo->find($orderId);
        $stock = $this->inventory->check($order);
        return ['order' => $order, 'stock' => $stock];
    }
}

// Теперь оба сервиса зависят от OrderContext, а не друг от друга
class OrderService {
    public function __construct(private OrderContext $context) {}
}

class InventoryService {
    public function __construct(private OrderContext $context) {}
}
```
**Результат:** Цикл устранён. Зависимости направлены в одну сторону: `A` и `B` → `C`.

#### 2. Использование событий (Event-Driven)

Если `A` должен что-то сделать, когда `B` изменил состояние, не заставляйте `B` знать об `A`.

**Было:**
- `OrderService` создаёт заказ.
- `InventoryService` должен быть уведомлён, чтобы снять со склада.
- `InventoryService` должен проверить, есть ли на складе (и для этого знает `OrderService`).

**Решение:**
1. `OrderService` публикует событие `OrderCreatedEvent`.
2. `InventoryService` **слушает** это событие (Subscriber) и сам снимает со склада.
3. `InventoryService` больше не знает про `OrderService`.

```php
// OrderService
public function createOrder(): Order {
    $order = new Order();
    // ... логика
    $this->eventDispatcher->dispatch(new OrderCreatedEvent($order));
    return $order;
}

// InventorySubscriber
class InventorySubscriber implements EventSubscriberInterface {
    public function onOrderCreated(OrderCreatedEvent $event): void {
        $order = $event->getOrder();
        $this->inventory->reserveStock($order);
    }
}
```
**Результат:** Полное развязывание. `OrderService` не знает про `InventoryService`.

#### 3. Интерфейсы и стратегия (Strategy Pattern)

Если `A` должен использовать логику `B`, а `B` — логику `A`, скорее всего, они оба зависят от **абстракции**, а не от конкретных классов.

**Пример:** `AuthService` и `SessionService`.
- `AuthService` должен сохранить сессию после логина.
- `SessionService` должен проверить токен при каждом запросе (и для этого знает `AuthService`).

**Решение:**
Внедрите в `SessionService` интерфейс `TokenValidator`, а не `AuthService`.
В `AuthService` внедрите интерфейс `SessionStorage`, а не `SessionService`.

```php
interface TokenValidator { public function isValid(string $token): bool; }
interface SessionStorage { public function save(Session $session): void; }

class AuthService {
    public function __construct(private SessionStorage $storage) {} // Зависит от абстракции
}

class SessionService {
    public function __construct(private TokenValidator $validator) {} // Зависит от абстракции
}
```
Теперь вы можете внедрить **просто** `TokenValidator` (например, `JwtValidator`) в `SessionService`, и цикл исчезнет.

#### 4. Разделение на домены (Bounded Contexts)

Если циклы возникают часто, значит, вы смешали домены.

**Пример:** `Order` и `Inventory` — это разные Bounded Contexts (DDD).
- `OrderContext` не должен зависеть от `InventoryContext`.
- Они общаются через **события** или **интеграционный клиент** (API/Очередь).

Это решение на уровне архитектуры, но оно навсегда убирает циклы между крупными модулями.

---

### 🚫 Антипаттерны (Чего делать не стоит)

1. **Геттеры и сеттеры вместо DI:**
```php
class OrderService {
    private ?InventoryService $inventory = null;
    
    public function setInventory(InventoryService $inventory): void {
        $this->inventory = $inventory;
    }
}
```
Это нарушение инкапсуляции и делает код непредсказуемым. Сервис может быть использован в состоянии, когда зависимость ещё не установлена.

2. **Статические методы:**
```php
class OrderService {
    public function create() {
        InventoryService::check(); // Статическая связь = жёсткая зависимость
    }
}
```
Сложно тестировать, невозможно переопределить, часто приводит к циклам.

3. **Global Container (Service Locator):**
```php
$inventory = $this->container->get(InventoryService::class);
```
Вы "решаете" цикл, пряча зависимость внутрь метода. Это **антипаттерн**. Он скрывает реальные зависимости и делает код невозможным для тестирования.

---

### ✅ Алгоритм решения цикла

1. **Найдите цикл:** `A` → `B` → `A`.
2. **Спросите себя:** «Зачем `A` нужен `B`, и зачем `B` нужен `A`?»
3. **Определите направление:**
   - Если `A` инициирует действие, а `B` реагирует → **События** (Option 2).
   - Если они разделяют данные → **Извлечение в третий сервис** (Option 1).
   - Если они оба зависят от общего поведения → **Интерфейсы** (Option 3).
4. **Перепишите код:** Уберите прямую зависимость одного от другого.
5. **Проверьте:** Запустите `bin/console debug:autowiring`. Цикла не должно быть.

**Итог:** Циклические зависимости — это **архитектурная ошибка**. Не чините её `lazy`, а устраняйте рефакторингом.

> ⏱ 16.9s · prompt 15093 tok · output 2106 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Это классическая задача асинхронной обработки побочных эффектов. В Symfony есть несколько способов решить её, каждый со своими плюсами и минусами.

### Критерии оценки:
1. **Надёжность** (сообщение не потеряется).
2. **Производительность** (запрос завершается быстро).
3. **Сложность** (инфраструктура, отладка).
4. **Гибкость** (легко добавить новые обработчики).

---

### Вариант 1: Очередь сообщений (Message Queue) — **Рекомендуемый**

**Как работает:**
1. После сохранения пользователя в БД публикуется событие.
2. Слушатель события отправляет задачу в очередь (RabbitMQ, Redis Streams, Amazon SQS, или встроенная Doctrine Migrations Queue).
3. Запрос завершается.
4. Отдельный воркер (consumer) достаёт задачу из очереди и выполняет её (отправляет email, обновляет индекс).

**Реализация в Symfony:**

```php
// 1. Событие
class UserRegisteredEvent extends Event {
    public function __construct(private User $user) {}
    public function getUser(): User { return $this->user; }
}

// 2. Слушатель (публикатор в очередь)
class UserRegisteredListener {
    public function __construct(private MessagePublisher $publisher) {}

    public function onUserRegistered(UserRegisteredEvent $event): void {
        $this->publisher->publish(new SendEmailMessage($event->getUser()->getId()));
        $this->publisher->publish(new UpdateSearchIndexMessage($event->getUser()->getId()));
    }
}

// 3. Воркеры (отдельные процессы)
// bin/console messenger:consume async --limit=1
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ **Максимальная надёжность** (сообщение не потеряется, если воркер перезапустится). | ❌ **Сложность инфраструктуры** (нужен брокер, мониторинг, обработка dead-letter). |
| ✅ **Развязка** (email и индекс — независимые задачи). | ❌ **Задержка** (сообщение может прийти не мгновенно). |
| ✅ **Гибкость** (легко добавить новый обработчик). | ❌ **Отладка сложнее** (нужен логинг очереди, метрики). |
| ✅ **Масштабируемость** (можно поднять N воркеров). | |

**Когда использовать:**
- Высокая нагрузка.
- Критичная надёжность (письма не должны теряться).
- Разные SLA для задач (email можно отправить через 5 сек, индекс — через 1 мин).

---

### Вариант 2: Symfony Messenger + Async Transport (Лёгкая очередь)

Это **гибрид** варианта 1. Вы используете `Symfony Messenger` с транспортным механизмом `doctrine` (БД) или `amqp`.

**Как работает:**
1. Вы публикуете сообщение через `MessageBus`.
2. Транспорт (например, Doctrine DB) сохраняет сообщение в таблицу `messenger_messages`.
3. Воркер `messenger:consume` достаёт сообщение и обрабатывает его.

**Пример:**

```php
// config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: 'doctrine://default?queue_name=async'
            failed: 'doctrine://default?queue_name=failed'

        routing:
            'App\Message\SendEmailMessage': async
            'App\Message\UpdateSearchIndexMessage': async
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ **Встроенное в Symfony** (не нужен внешний брокер). | ❌ **БД — не очередь** (медленная при высокой нагрузке). |
| ✅ **Простая настройка**. | ❌ **Нет встроенной обработки dead-letter** (нужно писать свой). |
| ✅ **Сообщение не потеряется** (если БД работает). | ❌ **Ограниченная масштабируемость** (БД может стать бутылочным горлышком). |
| ✅ **Легко отладить** (можно посмотреть сообщения в БД). | |

**Когда использовать:**
- Средний трафик.
- Не хочется разворачивать RabbitMQ/Redis.
- Нужна надёжность без сложной инфраструктуры.

---

### Вариант 3: Fire-and-Forget через HTTP (внутренний API)

**Как работает:**
1. После регистрации пользователь вызывается внутренний endpoint `/api/internal/register-side-effects` с payload.
2. Запрос завершается.
3. Отдельный сервис (или другой контейнер) обрабатывает этот запрос.

**Пример:**

```php
// В контроллере регистрации
public function register(Request $request): Response {
    // ... логика регистрации
    
    $client = new HttpClient();
    $client->post('http://internal-service/api/side-effects', [
        'json' => ['user_id' => $user->getId()]
    ]);
    
    return new JsonResponse('Registered', 201);
}
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ **Простота** (HTTP — универсальный протокол). | ❌ **Нет гарантии доставки** (если внутренний сервис упал). |
| ✅ **Масштабируемость** (легко добавить новые инстансы). | ❌ **Сеть — ненадёжный канал** (таймауты, ретраи). |
| | ❌ **Сложность отладки** (HTTP-лог, трейсинг). |

**Когда использовать:**
- Микросервисная архитектура.
- Низкая нагрузка.
- Можно допустить потерю сообщений (или есть ретраи на клиенте).

---

### Вариант 4: Бэктрондж-джоб (Background Job) в БД

**Как работает:**
1. После регистрации создаётся запись в таблице `jobs` с типом `SendEmailJob`.
2. Отдельный cron-скрипт/воркер опрашивает таблицу каждые N секунд.

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ **Максимальная простота**. | ❌ **Поллинг** (задержка до N секунд). |
| ✅ **Надёжность** (БД). | ❌ **Неэффективно** (опрос БД каждые 5 сек — нагрузка). |
| | ❌ **Нет приоритетов** (все задачи в одной куче). |
| | ❌ **Сложность масштабирования** (один воркер). |

**Когда использовать:**
- Очень низкий трафик.
- Прототипирование.
- Нет доступа к брокеру.

---

### ❌ Антипаттерны (Чего делать не стоит)

#### 1. Синхронная обработка в контроллере
```php
public function register(Request $request): Response {
    $user = $this->createUser($request);
    $this->sendEmail($user); // Задерживает ответ на 2-5 сек
    $this->updateIndex($user); // Задерживает ответ на 1-3 сек
    return new JsonResponse('OK');
}
```
**Почему плохо:**
- Пользователь ждёт 5-10 секунд.
- Если email упдёт — весь запрос упадёт (откат).
- Невозможно масштабировать (каждый запрос — 5 сек работы).

#### 2. Статический вызов в другом процессе (pcntl_fork)
```php
pcntl_fork();
if (0 == $pid) {
    // Детский процесс
    $this->sendEmail($user);
    exit(0);
}
// Родительский процесс возвращается
```
**Почему плохо:**
- **Хрупко** (если родительский процесс упадёт — детский может стать сиротой).
- **Сложно отлаживать** (два процесса, раздельные логи).
- **Не работает в FPM** (Apache/Nginx не любят fork).
- **Нет ретраев**.

#### 3. Асинхронный HTTP-запрос из контроллера
```php
$client = new HttpClient();
$client->post('http://internal/api/side-effects', [...]);
// НЕ ЖДАМ ОТВЕТА, НО КОННЕКЦИЯ МОЖЕТ БЫТЬ ЗАКРЫТА ДО ОБРАБОТКИ
```
**Почему плохо:**
- Нет гарантии, что сообщение дошло.
- Нет ретраев.
- Сложно отследить, что произошло с сообщением.

---

### ✅ Рекомендация: Что выбрать?

| Ситуация | Рекомендация |
|----------|--------------|
| **Стартап, 1-10k req/day** | **Вариант 2** (Messenger + Doctrine). Простой, надёжный, без внешней инфраструктуры. |
| **Средний бизнес, 10-100k req/day** | **Вариант 1** (Messenger + RabbitMQ/Redis). Масштабируемый, надёжный. |
| **Enterprise, 100k+ req/day** | **Вариант 1** + **Kubernetes** + **Monitoring**. Полная асинхронность. |
| **Микросервисы** | **Вариант 1** (очередь) или **Вариант 3** (HTTP) с ретраями. |

### Итоговая архитектура (Рекомендуемая)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Browser    │────▶│   API       │────▶│   Database  │
│             │     │ (Symfony)   │     │             │
└─────────────┘     └──────┬──────┘     └─────────────┘
                           │
                           │ Publish
                           ▼
                    ┌─────────────┐
                    │  Message    │
                    │  Queue      │
                    │ (RabbitMQ)  │
                    └──────┬──────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
   ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
   │  Email      │  │  Search     │  │  Analytics  │
   │  Worker     │  │  Indexer    │  │  Worker     │
   └─────────────┘  └─────────────┘  └─────────────┘
```

**Ключевые моменты:**
1. **Событие** `UserRegisteredEvent` публикуется **после** сохранения в БД.
2. **Очередь** — единственный источник правды для побочных эффектов.
3. **Воркеры** — независимые процессы, которые можно масштабировать.
4. **Мониторинг** — обязательное условие (метрики очереди, dead-letter, логинг).

Хотите, я покажу полный код реализации с `Symfony Messenger` + `RabbitMQ`?

> ⏱ 18.9s · prompt 19215 tok · output 2569 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony — это многоуровневая система, которая разделяет **аутентификацию** («кто ты?») и **авторизацию** («что ты можешь делать?»).

Давайте разберём каждый слой и поймём, когда использовать что.

---

### 1. Аутентификация: «Кто ты?»

Это процесс проверки личности пользователя. Symfony предоставляет несколько способов:

#### A. Session-based (Форма логина)
- Пользователь отправляет логин/пароль.
- Symfony проверяет их, создаёт сессию (`_security`).
- При каждом запросе Symfony восстанавливает `SecurityUser` из сессии.
- **Когда использовать:** Классические веб-приложения, админки, личные кабинеты.

#### B. JWT / Token-based (API)
- Пользователь получает токен (JWT, Bearer Token).
- Токен передаётся в заголовке `Authorization`.
- Symfony проверяет токен и создаёт `SecurityUser`.
- **Когда использовать:** SPA, мобильные приложения, публичные API.

#### C. LDAP / SSO / OAuth2
- Аутентификация делегируется внешней системе.
- Symfony получает claims из внешнего токена.
- **Когда использовать:** Enterprise, корпоративные интеграции.

**Ключевой момент:** После успешной аутентификации Symfony создаёт объект `UserInterface`, который содержит:
- Идентификатор (`getId()`).
- Username (`getUserIdentifier()`).
- **Роли** (`getRoles()`).
- Метод `eraseCredentials()` (удаляет пароль из памяти).

---

### 2. Авторизация: «Что ты можешь делать?»

В Symfony есть три уровня авторизации, от простых к сложным:

#### A. Роли (Roles) — Простейший уровень

Роли — это строки, назначаемые пользователю.

```php
class User implements UserInterface {
    public function getRoles(): array {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }
}
```

**Проверка ролей:**

```php
// В контроллере
if ($this->isGranted('ROLE_ADMIN')) {
    // ...
}

// В шаблоне Twig
{% if is_granted('ROLE_ADMIN') %}
    <a href="/admin">Admin Panel</a>
{% endif %}

// В конфиге firewall
security:
    firewalls:
        admin:
            pattern: ^/admin
            access_control:
                - { path: ^/admin, roles: ROLE_ADMIN }
```

**Когда использовать роли:**
- Глобальные права доступа: «Админ», «Модератор», «Пользователь».
- Доступ к целым разделам сайта (админка, личка).
- Когда права **не зависят от данных** (например, «все админы могут видеть все заказы»).

**Ограничения ролей:**
- Не могут выражать сложную логику: «Пользователь может удалить **свой** заказ, но не чужой».
- Не могут учитывать контекст: «Модератор может банить пользователей только в своём регионе».

---

#### B. Permissions (Permissions) — Улучшение ролей

Symfony 5+ позволяет использовать строки-разрешения вместо жёстких ролей.

```php
class User {
    public function getRoles(): array {
        return [Permissions::Admin]; // 'admin'
    }
}
```

**Преимущества:**
- Семантически clearer: `is_granted('admin')` лучше, чем `is_granted('ROLE_ADMIN')`.
- Легче мигрировать на RBAC (Role-Based Access Control).

**Когда использовать:**
- Когда у вас много ролей и они не масштабируются.
- Когда вы хотите более чистый код.

---

#### C. Voters — Самый гибкий уровень

Voter — это класс, который реализует интерфейс `VoterInterface` и принимает решение о разрешении доступа **на основе контекста**.

```php
class OrderVoter implements Voter {
    public function supports(string $attribute, ?object $subject): bool {
        // Voter участвует только в проверках атрибутов 'ORDER_VIEW', 'ORDER_DELETE'
        return in_array($attribute, ['ORDER_VIEW', 'ORDER_DELETE']) && $subject instanceof Order;
    }

    public function vote(int $attribute, object $subject, TokenInterface $token): int {
        $user = $token->getUser();
        $order = $subject;

        if ($user instanceof User) {
            // Логика: Владелец может видеть свой заказ
            if ($order->getUserId() === $user->getId()) {
                return Vote::ACCESS_GRANTED;
            }
            
            // Логика: Админ может видеть все заказы
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return Vote::ACCESS_GRANTED;
            }
        }

        return Vote::ACCESS_ABSTAIN; // Не уверен, пусть решит другой voter
    }
}
```

**Проверка:**

```php
// В контроллере
if ($this->isGranted('ORDER_DELETE', $order)) {
    $this->delete($order);
}

// В шаблоне Twig
{% if is_granted('ORDER_DELETE', order) %}
    <button onclick="deleteOrder({{ order.id }})">Delete</button>
{% endif %}
```

**Механика:**
1. Symfony спрашивает всех зарегистрированных voters: «Можно ли `ORDER_DELETE` для этого `$order`?»
2. Если хотя бы один voter говорит `ACCESS_GRANTED` — доступ разрешён.
3. Если все говорят `ACCESS_ABSTAIN` — доступ запрещён (fail-closed).
4. Если кто-то говорит `ACCESS_DENIED` — доступ запрещён.

---

### 3. Сравнение: Роли vs Voters

| Критерий | Роли (Roles) | Voters |
|----------|-------------|--------|
| **Сложность** | Низкая | Средняя |
| **Гибкость** | Низкая (статические) | Высокая (динамические) |
| **Контекст** | Нет (только «кто ты») | Есть (контекст: объект, параметры, время) |
| **Производительность** | Быстро (проверка массива строк) | Медленнее (вызов методов voter'ов) |
| **Тестируемость** | Трудно (зависит от конфигурации) | Легко (юнит-тесты voter'ов) |
| **Используется для** | Глобальные права | Контекстные права |

---

### 4. Когда использовать что?

#### ✅ Используйте **Роли**, если:
- Права доступа **статичные** и не зависят от данных.
- Пример: «Админ может зайти в `/admin`», «Пользователь может зайти в `/profile`».
- Пример: «Модератор может видеть панель модерации».
- Пример: «Платящий подписчик может видеть контент».

#### ✅ Используйте **Voters**, если:
- Права зависят от **конкретного объекта**.
  - Пример: «Пользователь может удалить **свой** комментарий».
  - Пример: «Менеджер может редактировать **свои** сделки».
- Права зависят от **состояния объекта**.
  - Пример: «Автор может редактировать статью, пока она не опубликована».
- Права зависят от **времени** или **внешних условий**.
  - Пример: «Вашингтон может видеть отчет, если дата > 2024».
  - Пример: «Тестовый аккаунт может создавать заказы, только если баланс > 0».
- Вам нужна **сложная бизнес-логика** доступа.
  - Пример: «Пользователь может удалить заказ, если:
    1. Он владелец, ИЛИ
    2. Он админ, ИЛИ
    3. Заказ был создан > 30 дней назад и не отменён».

---

### 5. Бест-практики

#### A. Не переусложняйте с Voter'ами
Если логику доступа можно выразить ролью + условием в контроллере — сделайте так. Voter нужен, когда логика **сложная** и **многоразовая**.

#### B. Отдельный Voter на доменную сущность
Не создавайте `OneBigVoter`, который проверяет все. Создавайте:
- `UserVoter` (права на пользователей)
- `OrderVoter` (права на заказы)
- `PostVoter` (права на посты)

#### C. Используйте `ACCESS_ABSTAIN` правильно
Если ваш voter не знает, что делать — говорите `ABSTAIN`, а не `DENIED`. Это позволяет другим voters принять решение.

#### D. Тестируйте Voters
Voters — это чистые классы с логикой. Их легко юнит-тестировать:

```php
test('owner can delete own order'): void {
    $voter = new OrderVoter();
    $order = new Order();
    $order->setUserId(1);
    
    $token = $this->createToken(1, ['ROLE_USER']);
    
    $result = $voter->vote('ORDER_DELETE', $order, $token);
    $this->assertSame(Vote::ACCESS_GRANTED, $result);
}
```

#### E. Не используйте Voter для аутентификации
Voter — это авторизация. Аутентификация — это `UserProvider` + `Authenticator`. Не смешивайте.

---

### 6. Пример архитектуры

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Request       │────▶│  Firewall       │────▶│  Access Control │
│  (HTTP)         │     │  (Аутентификация)│     │  (Авторизация)  │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                          │
                                                          ▼
                                               ┌─────────────────┐
                                               │  Security Token │
                                               │  (User + Roles) │
                                               └────────┬────────┘
                                                          │
                        ┌────────────────────────────────┼────────────────────────────────┐
                        ▼                                ▼                                ▼
               ┌─────────────────┐             ┌─────────────────┐             ┌─────────────────┐
               │  Role Check     │             │  Voter 1        │             │  Voter 2        │
               │  (ROLE_ADMIN)   │             │  (OrderVoter)   │             │  (UserVoter)    │
               └────────┬────────┘             └────────┬────────┘             └────────┬────────┘
                        │                                │                                │
                        ▼                                ▼                                ▼
                  ┌──────────┐                    ┌──────────┐                    ┌──────────┐
                  │ GRANTED  │                    │ GRANTED  │                    │ ABSTAIN  │
                  └──────────┘                    └──────────┘                    └──────────┘
```

**Итог:**
- **Роли** — для глобальных, статических прав.
- **Voters** — для контекстных, динамических прав.
- **Аутентификация** — только через `UserProvider` + `Authenticator`.
- **Не смешивайте** аутентификацию и авторизацию.

> ⏱ 18.9s · prompt 21830 tok · output 2649 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение Value Objects (VO) в Doctrine — классический конфликт между **доменной моделью** (изменчивая, неизменяемая, валидная) и **ORM** (мутабельный граф сущностей, lazy-loading, hydration).

### Главный принцип
> **Value Object в домене — неизменяемый.**
> **Value Object в БД — набор колонок/JSON.**

Дома (в домене) VO живёт как объект. В БД он должен быть сериализован в примитивы.

---

## Способ 1: Custom Type (Рекомендуемый для простых VO)

Документация Doctrine официально поддерживает `Custom Type` — это самый чистый способ для простых VO (Money, Email, Phone).

### Реализация

```php
// 1. Value Object
class Money implements \JsonSerializable {
    private function __construct(private int $amount, private string $currency) {}

    public static function of(int $amount, string $currency): self {
        return new self($amount, $currency);
    }

    public function getAmount(): int { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }

    public function jsonSerialize(): array {
        return ['amount' => $this->amount, 'currency' => $this->currency];
    }
}

// 2. Custom Type
class MoneyType extends Type {
    public const NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return 'VARCHAR(20)'; // Или DECIMAL, зависит от реализации
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money {
        if ($value === null) return null;
        
        // $value — строка из БД, например "100|USD"
        [$amount, $currency] = explode('|', $value);
        return Money::of((int)$amount, $currency);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if ($value === null) return null;
        
        return $value->getAmount() . '|' . $value->getCurrency();
    }

    public function getName(): string { return self::NAME; }
}

// 3. Entity
class Order {
    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;

    public function getTotal(): Money { return $this->total; }
    
    public function setTotal(Money $total): void {
        $this->total = $total;
    }
}
```

### Плюсы
✅ Инкапсуляция: `Order->getTotal()->add(...)` — чистый доменный код.
✅ Простота: одна колонка в БД.
✅ Быстро: минимальный overhead.
✅ Типобезопасно: `setTotal(string $x)` — ошибка компиляции.

### Минусы
❌ **Медленный hydration:** Для каждой сущности вызывается `convertToPHPValue`. При выборке 1000 заказов — 1000 раз десериализация.
❌ **Сложный SQL:** Нельзя делать `WHERE total > 100` без кастомного DQL-парсера.
❌ **Indexing:** Невозможно создать индекс на `total.amount` (это строка в одной колонке).
❌ **Сложность миграции:** Изменение структуры VO (добавление полей) требует миграции данных.

---

## Способ 2: Embedded Entity (Для сложных VO)

Если VO содержит несколько полей (например, `Address { street, city, zip }`), лучше использовать **Embedded Entity**.

### Реализация

```php
// 1. VO как Embedded Entity
#[ORM\Embeddable]
class Address {
    #[ORM\Column(type: string)]
    private string $street;

    #[ORM\Column(type: string)]
    private string $city;

    #[ORM\Column(type: string)]
    private string $zip;

    // Конструктор с валидацией
    public function __construct(string $street, string $city, string $zip) {
        $this->validate();
        $this->street = $street;
        $this->city = $city;
        $this->zip = $zip;
    }

    private function validate(): void {
        if (strlen($this->zip) < 3) {
            throw new \InvalidArgumentException('Invalid zip code');
        }
    }

    // Геттеры...
}

// 2. Entity
class User {
    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    public function getAddress(): Address { return $this->address; }
    
    // ВАЖНО: Сеттер принимает только Address, не отдельные поля
    public function setAddress(Address $address): void {
        $this->address = $address;
    }
}
```

### Плюсы
✅ **Несколько колонок:** Можно индексировать `zip`, `city`.
✅ **Простой SQL:** `WHERE address_zip = '10100'` работает нативно.
✅ **Валидация в конструкторе:** Гибрид — DO создаётся валидным, но ORM видит отдельные поля.
✅ **Поддержка миграций:** Легко добавлять/удалять поля.

### Минусы
❌ **Не true Immutable:** Embedded entity — это мутабельный объект. Теоретически можно сделать `$user->getAddress()->city = 'New'`. **Нужно защитить геттеры/сеттеры.**
❌ **Сложность наследования:** Если VO наследуется от другого — проблемы.
❌ **Doctrine bug:** Ранние версии Doctrine имели баги с Embedded + `lazy=true`.

**Защита от мутации:**
```php
class Address {
    private function __construct() {} // Запретить создание вне конструктора? Нет, ORM создаёт.
    
    // Лучше: readonly свойства (PHP 8.1+)
    #[ORM\Column(type: string)]
    private readonly string $street;
    
    // Но тогда нужен конструктор с параметрами
    // И ORM должен его вызывать. Это работает в PHP 8.1+ + Doctrine 2.13+
}
```

---

## Способ 3: JSON Column (Для сложных, динамических VO)

Если VO — это «мешок данных» (настройки, профили, сложные структуры), используйте `JSON`-колонку.

### Реализация

```php
class User {
    #[ORM\Column(type: 'json')]
    private array $preferences; // ['theme' => 'dark', 'language' => 'ru']
}
```

### Плюсы
✅ Максимальная гибкость.
✅ Нет миграций при изменении структуры.
✅ Простота.

### Минусы
❌ **Нельзя индексировать** (в PostgreSQL — можно через GIN, но не портативно).
❌ **Нет валидации на уровне БД.**
❌ **Медленнее** (сериализация/десериализация JSON).
❌ **Сложный SQL** (`WHERE preferences->>'theme' = 'dark'`).

---

## Способ 4: Raw Columns + Manual Hydration (Максимальный контроль)

Разметить VO как отдельную таблицу или набор колонок, и вручную управлять маппингом через `Doctrine\ORM\Mapping\Driver`.

### Реализация

```php
// Entity
class Order {
    #[ORM\Column(type: int)]
    private int $totalAmount;

    #[ORM\Column(type: string)]
    private string $totalCurrency;

    // DO-обёртка
    public function getTotal(): Money {
        return Money::of($this->totalAmount, $this->totalCurrency);
    }

    public function setTotal(Money $total): void {
        $this->totalAmount = $total->getAmount();
        $this->totalCurrency = $total->getCurrency();
    }
}
```

### Плюсы
✅ **Полный контроль:** Можно индексировать, использовать в SQL.
✅ **Простота:** Нет кастомных типов.
✅ **Быстро:** Минимальный overhead.

### Минусы
❌ **Нарушение инкапсуляции:** DO-поля (`totalAmount`) видны извне. Нужно скрывать геттерами.
❌ **Риск несогласованности:** Кто-то может вызвать `setTotalAmount(100)` отдельно от `setTotalCurrency('USD')`.
❌ **Сложность:** Много boilerplate-кода (геттеры, сеттеры).

**Решение:** Скрыть поля через `#[ORM\Exclude]`? Нет, тогда ORM не будет их маппить. Лучше использовать **Wayfinder** или **PHP 8.2 readonly** + **Private Setters** (если поддержка).

---

## Способ 5: Event Listener + Repository (Максимальная изоляция)

VO хранится в отдельной таблице, и Entity ссылается на него через ID.

### Реализация

```php
class Money {
    // VO с ID
    public function __construct(private int $id, private int $amount, private string $currency) {}
}

class Order {
    #[ORM\ManyToOne(targetEntity: Money::class)]
    private Money $total;
}
```

### Плюсы
✅ **Идеальная нормализация.**
✅ **Кэширование:** Можно кэшировать `Money` объекты.
✅ **Аудит:** История изменений `Money`.

### Минусы
❌ **Нужна таблица для каждого VO.**
❌ **Сложность:** Транзакции, cascade, lazy-loading.
❌ **Overkill:** Для 95% случаев это избыточно.

---

## Сравнительная таблица

| Способ | Инкапсуляция | Индексы | Скорость | Сложность | Когда использовать |
|--------|-------------|---------|----------|-----------|-------------------|
| **Custom Type** | ⭐⭐⭐⭐⭐ | ❌ | ⭐⭐⭐ | ⭐⭐ | Простые VO (Money, Email) |
| **Embedded** | ⭐⭐⭐⭐ | ✅ | ⭐⭐⭐⭐ | ⭐⭐ | Структурированные VO (Address) |
| **JSON** | ⭐⭐ | ❌ | ⭐⭐ | ⭐ | Динамические настройки |
| **Raw Columns** | ⭐⭐ | ✅ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | Высокая нагрузка, простые VO |
| **Table per VO** | ⭐⭐⭐⭐⭐ | ✅ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | Частые изменения, кэширование |

---

## Рекомендации

### 1. Для 90% случаев: **Custom Type** или **Embedded**
- **Money, Email, UUID** → Custom Type.
- **Address, GeoLocation** → Embedded.

### 2. Защита от мутации
- Используйте **`readonly`** свойства (PHP 8.1+).
- **Не создавайте публичные сеттеры** для отдельных полей VO.
- **Валидируйте в конструкторе VO.**

### 3. Избегайте «голых» DO-полей
Никогда не делайте:
```php
// ПЛОХО
class Order {
    #[ORM\Column] private int $amount;
    #[ORM\Column] private string $currency;
    public function getAmount(): int { return $this->amount; }
    public function setAmount(int $x): void { $this->amount = $x; } // РАСПЛОХАЕТСЯ VO
}
```
Вместо этого:
```php
// ХОРОШО
class Order {
    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;
    
    public function setTotal(Money $total): void {
        $this->total = $total; // Только через VO
    }
}
```

### 4. Тестируйте
- Тестируйте **VO** отдельно (юнит-тесты).
- Тестируйте **Entity** отдельно (интеграционные тесты с БД).
- Тестируйте **Custom Type** отдельно (конвертация в обе стороны).

### 5. Документируйте
В DocBlock Entity указывайте:
```php
/**
 * @ORM\Column(type="money")
 * @var Money
 */
private Money $total;
```

---

## Итог

| Ситуация | Решение |
|----------|---------|
| Money, Email, Phone, UUID | **Custom Type** |
| Address, GeoLocation, Range | **Embedded Entity** |
| Settings, Metadata, Complex | **JSON Column** |
| Высокая нагрузка + простые VO | **Raw Columns + Incapsulation** |
| Частые изменения + кэширование | **Table per VO** |

**Главный совет:** Не пытайтесь сделать VO «идеальным» в ORM. Пусть в домене он будет чистым, а в БД — оптимизированным. Используйте **Custom Type** как мост между ними.

> ⏱ 23.1s · prompt 24532 tok · output 2998 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Это фундаментальный вопрос DDD + ORM. Главное правило: **Aggregate Root — единственная точка входа для изменения состояния агрегата и единственная точка, которую мы сохраняем в БД.**

### Архитектурные принципы

1. **Aggregate Root** знает о своих **Child Entities** (или Value Objects).
2. **Child Entities** не знают о Root (или знают, но не сохраняются напрямую).
3. **Никогда не сохраняйте Child Entity напрямую** через Repository. Всегда через Root.
4. **`$em->flush()`** вызывается **один раз** после всех изменений, обычно в **App Service** (Application Service), а не в Domain Service или Repository.

---

## Пример: `Order` (Агрегат)

**Состав:**
- `Order` (Aggregate Root)
- `OrderItem` (Child Entity)
- `Money` (Value Object)

### 1. Domain Layer (Чистая PHP, без Doctrine)

```php
// Value Object
class Money {
    private function __construct(private int $amount, private string $currency) {}

    public static function of(int $amount, string $currency): self {
        return new self($amount, $currency);
    }

    public function getAmount(): int { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }

    public function add(Money $other): self {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currency mismatch');
        }
        return self::of($this->amount + $other->amount, $this->currency);
    }

    public function __toString(): string {
        return $this->amount . ' ' . $this->currency;
    }
}

// Child Entity (ВНИМАНИЕ: это НЕ Aggregate Root)
class OrderItem {
    private ?int $id = null;
    private Product $product;
    private int $quantity;
    private Money $unitPrice;

    public function __construct(Product $product, int $quantity, Money $unitPrice) {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getProductId(): int { return $this->product->getId(); }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): Money { return $this->unitPrice; }
    public function getLineTotal(): Money {
        return $this->unitPrice->add($this->unitPrice); // Упрощённо
    }

    public function getId(): ?int { return $this->id; }
}

// Aggregate Root
class Order {
    private ?int $id = null;
    private string $number;
    private Customer $customer;
    private \SplObjectStorage $items; // Используется для уникальности
    private \DateTimeImmutable $createdAt;

    public function __construct(string $number, Customer $customer) {
        $this->number = $number;
        $this->customer = $customer;
        $this->items = new \SplObjectStorage();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array {
        return iterator_to_array($this->items);
    }

    public function addItem(Product $product, int $quantity, Money $unitPrice): void {
        // Проверка: такой товар уже есть?
        foreach ($this->items as $item) {
            if ($item->getProductId() === $product->getId()) {
                throw new \DomainException('Item already exists');
            }
        }

        $item = new OrderItem($product, $quantity, $unitPrice);
        $this->items->attach($item);
    }

    public function removeItem(OrderItem $item): void {
        $this->items->detach($item);
    }

    public function getTotal(): Money {
        $total = Money::of(0, $this->customer->getCurrency());
        foreach ($this->items as $item) {
            $total = $total->add($item->getLineTotal());
        }
        return $total;
    }

    public function getId(): ?int { return $this->id; }
    public function getNumber(): string { return $this->number; }
    public function getCustomer(): Customer { return $this->customer; }
}
```

### 2. Doctrine Mapping (Entity Layer)

```php
// src/Entity/Order.php
#[ORM\Entity]
class Order extends \App\Domain\Order {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: integer)]
    private int $id;

    #[ORM\Column(type: string)]
    private string $number;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\OneToMany(
        targetEntity: OrderItem::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $items;

    public function __construct(string $number, Customer $customer) {
        parent::__construct($number, $customer);
        $this->items = new ArrayCollection();
    }

    // ОVERRIDING getItems() чтобы вернуть Collection для Doctrine
    public function getItems(): Collection {
        return $this->items;
    }

    // ВАЖНО: addItem() теперь добавляет в Collection
    public function addItem(Product $product, int $quantity, Money $unitPrice): void {
        $item = new OrderItem($product, $quantity, $unitPrice);
        $item->setOrder($this); // Устанавливаем ссылку на Parent
        $this->items->add($item);
    }

    public function removeItem(OrderItem $item): void {
        $this->items->removeElement($item);
    }
}

// src/Entity/OrderItem.php
#[ORM\Entity]
class OrderItem extends \App\Domain\OrderItem {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: integer)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\Column(type: integer)]
    private int $quantity;

    #[ORM\Column(type: 'json')] // Или Custom Type
    private array $unitPrice;

    public function __construct(Product $product, int $quantity, Money $unitPrice) {
        parent::__construct($product, $quantity, $unitPrice);
        $this->unitPrice = ['amount' => $unitPrice->getAmount(), 'currency' => $unitPrice->getCurrency()];
    }

    public function setOrder(?Order $order): void {
        $this->order = $order;
    }

    public function getUnitPrice(): Money {
        return Money::of($this->unitPrice['amount'], $this->unitPrice['currency']);
    }
}
```

### 3. Repository (Только для Aggregates)

```php
// src/Repository/OrderRepository.php
interface OrderRepositoryInterface {
    public function save(Order $order): void;
    public function findById(int $id): Order;
    public function delete(Order $order): void;
}

// src/Repository/OrderRepository.php (Doctrine Implementation)
class OrderRepository implements OrderRepositoryInterface {
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Order $order): void {
        $this->em->persist($order);
        // ВНИМАНИЕ: flush() НЕ вызывается здесь!
        // Мы только говорим Doctrine: "Вот тебе новый/изменённый объект".
    }

    public function findById(int $id): Order {
        $order = $this->em->find(Order::class, $id);
        if (!$order) {
            throw new OrderNotFoundException($id);
        }
        return $order;
    }

    public function delete(Order $order): void {
        $this->em->remove($order);
        // flush() НЕ вызывается здесь!
    }
}
```

### 4. Application Service (Здесь вызывается flush)

```php
// src/Service/OrderService.php
class OrderService {
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $em
    ) {}

    public function createOrder(string $number, Customer $customer, array $itemsData): Order {
        $order = new Order($number, $customer);

        foreach ($itemsData as $itemData) {
            $product = $this->productRepository->findById($itemData['productId']);
            $price = Money::of($itemData['amount'], $itemData['currency']);
            $order->addItem($product, $itemData['quantity'], $price);
        }

        // Сохраняем АГРЕГАТ
        $this->orderRepository->save($order);

        // ЗАКРЫВАЕМ ТРАНСАКЦИЮ
        $this->em->flush();
        $this->em->clear(); // Опционально, для освобождения памяти

        return $order;
    }

    public function addItemToOrder(int $orderId, Product $product, int $quantity, Money $price): Order {
        $order = $this->orderRepository->findById($orderId);
        $order->addItem($product, $quantity, $price);
        $this->orderRepository->save($order);
        $this->em->flush();
        return $order;
    }
}
```

### 5. Key Points

| Вопрос | Ответ |
|--------|-------|
| **Кто вызывает `flush()`?** | **Application Service** (или Controller, если нет App Service). Никогда не Repository, не Domain Service. |
| **Когда вызывать `flush()`?** | После всех изменений агрегата. Один раз на операцию. |
| **Как маппятся Child Entities?** | Через `OneToMany` с `cascade: ['persist', 'remove']` и `orphanRemoval: true`. |
| **Можно ли сохранять Child напрямую?** | **Нет.** Только через Root. `$em->persist($orderItem)` — ПЛОХО. |
| **Что такое `orphanRemoval`?** | Если вы удаляете Item из коллекции `$order->items`, Doctrine автоматически удалит его из БД. |

### 6. Anti-Patterns

#### ❌ 1. Сохранение Child Entity напрямую
```php
// ПЛОХО
$item = new OrderItem($product, 1, $price);
$item->setOrder($order);
$this->em->persist($item); // Нарушает инкапсуляцию Aggregate
```
**Почему плохо:** Вы обходите Root. Если Root имеет инварианты (например, "максимум 10 товаров"), они не будут проверены.

#### ❌ 2. `flush()` в Repository
```php
// ПЛОХО
public function save(Order $order): void {
    $this->em->persist($order);
    $this->em->flush(); // ПЛОХО
}
```
**Почему плохо:** Вы не можете откатить транзакцию, если после `save()` происходит ошибка в другом методе.

#### ❌ 3. Отсутствие `orphanRemoval`
```php
// ПЛОХО
#[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order')]
private Collection $items;
```
**Почему плохо:** Если вы удалите Item из коллекции, в БД он останется "осиротевшим".

#### ❌ 4. Неправильный cascade
```php
// ПЛОХО
#[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist'])]
```
**Почему плохо:** Если вы удалите Order, Items останутся в БД (FK constraint error).

### 7. Best Practices

1. **`cascade: ['persist', 'remove']`** + **`orphanRemoval: true`** — стандарт для Child Entities.
2. **`$em->clear()`** после `flush()` — если операция завершена и агрегат не нужен.
3. **Транзакции** — одна транзакция на одну бизнес-операцию.
4. **Invariants** — все проверки инвариантов в Root, не в Repository.
5. **Lazy Loading** — будьте осторожны. Загрузка всех Items может быть дорогой. Используйте `fetch: 'EAGER'` только если нужно.

### Итог

```
Controller
    ↓
Application Service (flush здесь)
    ↓
OrderRepository (persist/remove, без flush)
    ↓
Order (Aggregate Root)
    ↓
OrderItem (Child Entity, cascade persist/remove)
```

**Главное:** Агрегат — это единая транзакционная единица. Всё, что внутри него, сохраняется и откатывается вместе.

> ⏱ 20.1s · prompt 27599 tok · output 3002 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика и лечение N+1 — это рутинная, но критически важная часть работы с Doctrine. Если страница «тормозит», а в логах вы видите сотни одинаковых SQL-запросов за 50 мс, проблема почти наверняка здесь.

### 1. Диагностика: Как поймать N+1 на живом теле

#### А. Включение SQL-логирующей (Dev-режим)
В `config/packages/doctrine.yaml`:
```yaml
doctrine:
    dbal:
        logging: true # Только в dev!
        types:
            # ...
    orm:
        auto_generate_proxy_classes: '%kernel.debug%'
        auto_generate_hydrator: true
```
Или через DSN: `?logging=1`.

**Симптом:** В логах вы увидите:
```sql
SELECT o.* FROM orders o WHERE o.id = 1;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 1;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 2;
SELECT oi.* FROM order_items oi WHERE oi.order_id = 3;
... (еще 500 строк)
```
Это классический N+1.

#### Б. Doctrine Profiler (Web Profiler)
В Symfony Web Profiler есть вкладка **Doctrine**. Она показывает:
- Список всех сущностей.
- Количество запросов на каждую.
- Время выполнения каждого запроса.
- **Кликните на сущность** — вы увидите SQL-запросы, которые были сделаны для её загрузки.

#### В. PHPStan / Doctrine Annotations
Используйте `phpstan` с плагинами для Doctrine. Они могут поймать некоторые случаи N+1 на этапе статики (например, если вы итерируете по коллекции, которая не загружена).

#### Г. APM-системы (Datadog, NewRelic, Blackfire)
Если у вас есть APM, они показывают **waterfall-диаграмму**. N+1 выглядит как длинный хвост маленьких синих блоков (SQL-запросы), идущих подряд.

---

### 2. Решения и Trade-offs

#### Вариант 1: Eager Loading через DQL/Query (`JOIN` + `SET`)

Самый частый способ. Вы явно говорите Doctrine: «Загрузи всё за один запрос».

```php
// Было (N+1)
$orders = $em->getRepository(Order::class)->findAll();
foreach ($orders as $order) {
    $items = $order->getItems(); // ЛАЗЫ: 1 запрос на каждый order
}

// Стало (Eager)
$query = $em->createQueryBuilder()
    ->select('o', 'oi')
    ->from(Order::class, 'o')
    ->leftJoin('o.items', 'oi')
    ->getQuery();
$orders = $query->getResult();
```

**SQL:**
```sql
SELECT o.*, oi.* FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id;
```

| Плюсы | Минусы |
|-------|--------|
| ✅ **Один запрос** вместо N+1. | ❌ **Перегруз данных:** Загружаются все Items для всех Orders. Если у вас 1000 заказов и 10 товаров каждый — 10 000 строк в памяти. |
| ✅ **Простота** реализации. | ❌ **Память:** PHP-процесс потребляет больше RAM. |
| ✅ **Предсказуемая производительность** (1 запрос). | ❌ **Сложность DQL:** Если структура сложная, DQL становится громоздким. |
| | ❌ **Нарушение инкапсуляции:** ORM знает, как загружать данные, а не домен. |

**Когда использовать:**
- Коллекции небольшие (до 50 элементов на сущность).
- Вы точно знаете, что вам нужны все связанные данные.
- Страница выдачи списка (пагинация).

---

#### Вариант 2: Fetch Join через Annotations/Attributes

Автоматическая загрузка при любом обращении к сущности.

```php
#[ORM\ManyToOne(targetEntity: Customer::class)]
#[ORM\JoinColumn(name: 'customer_id')]
private Customer $customer;

// В Collection:
#[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', fetch: 'EAGER')]
private Collection $items;
```

**ВНИМАНИЕ:** `fetch: 'EAGER'` в `OneToMany` **НЕ РАБОТАЕТ** как ожидается в Doctrine! Это известная «фича». Doctrine игнорирует `fetch: 'EAGER'` для `OneToMany` и `ManyToMany`. Он работает только для `ManyToOne` и `OneToOne`.

**Почему так?**
Потому что `EAGER` для коллекций — это всегда N+1 или огромная нагрузка. Doctrine не может знать, нужны ли вам все элементы коллекции или только один.

**Вывод:** Не используйте `fetch: 'EAGER'` для коллекций. Используйте явные `JOIN` в DQL.

---

#### Вариант 3: Data Fetching Strategy — `setFetchJoinCollection` (Скрытый N+1)

Иногда вы хотите загрузить коллекцию, но не все её элементы.

```php
$query = $em->createQueryBuilder()
    ->select('o')
    ->from(Order::class, 'o')
    ->getQuery();
    
// Если вы хотите загрузить только первые 5 элементов:
$query->setFirstResult(0)->setMaxResults(100);
```
Это **не решает** N+1. Это только ограничивает количество загруженных заказов.

---

#### Вариант 4: Partial Object (Частичная загрузка)

Загрузка только нужных полей сущности.

```php
$orders = $em->createQueryBuilder()
    ->select(['o.id', 'o.number', 'o.customerId']) // Только поля!
    ->from(Order::class, 'o')
    ->getQuery()
    ->getResult(AbstractQuery::HYDRATE_ARRAY); // Или CustomHydrator
```

| Плюсы | Минусы |
|-------|--------|
| ✅ **Меньше данных** в памяти. | ❌ **Нет lazy-loading:** Если вы попытаетесь получить `$order->getItems()`, будет выброшено исключение. |
| ✅ **Быстрее** загрузка. | ❌ **Сложнее код:** Нужно знать, какие поля нужны заранее. |
| | ❌ **Нарушение инкапсуляции:** Вы не получаете полноценный объект. |

**Когда использовать:**
- Списки/таблицы, где нужно показать только 3-4 поля.
- Вы уверены, что не будете трогать ленивые свойства.

---

#### Вариант 5: Repository Pattern + Query Builder (Рекомендуемый)

Вместо `findAll()` вы всегда пишете кастомный запрос с нужными JOIN.

```php
// В OrderRepository
public function findByCustomer(Customer $customer): array {
    return $this->createQueryBuilder('o')
        ->leftJoin('o.items', 'oi')
        ->where('o.customer = :customer')
        ->setParameter('customer', $customer)
        ->getQuery()
        ->getResult();
}
```

| Плюсы | Минусы |
|-------|--------|
| ✅ **Гибкость:** Можно контролировать, что загружать. | ❌ **Много кода:** Каждый запрос пишется вручную. |
| ✅ **Оптимизация:** Можно добавлять фильтры, пагинацию. | ❌ **Дублирование:** Если одна и та же логика нужна в 3 местах — дублируем JOIN. |

**Когда использовать:**
- **Всегда.** Это золотой стандарт.

---

#### Вариант 6: Caching (Second-Level Cache)

Если данные не меняются часто, можно кэшировать сущности.

```yaml
doctrine:
    orm:
        second_level_cache:
            enabled: true
            region:
                order:
                    driver: doctrine_cache_apc
                    lifetime: 3600
```

| Плюсы | Минусы |
|-------|--------|
| ✅ **Нулевые запросы** к БД при повторном обращении. | ❌ **Сложность:** Нужно управлять инвалидацией кэша. |
| ✅ **Высокая производительность** для статичных данных. | ❌ **Память:** Кэш занимает RAM. |
| | ❌ **Консистентность:** Если данные изменились, кэш может быть устаревшим. |

**Когда использовать:**
- Справочные данные (каталоги, валюта, категории).
- Не для изменяемых сущностей (заказы, платежи).

---

#### Вариант 7: Materialized Views (Материализованные представления)

Создание таблицы в БД, которая содержит «плоскую» структуру данных.

```sql
CREATE MATERIALIZED VIEW order_summary AS
SELECT o.id, o.number, c.name, oi.item_id, oi.quantity
FROM orders o
JOIN customers c ON c.id = o.customer_id
LEFT JOIN order_items oi ON oi.order_id = o.id;
```

| Плюсы | Минусы |
|-------|--------|
| ✅ **Максимальная скорость** (простой SELECT). | ❌ **Сложность:** Нужно поддерживать актуальность данных. |
| ✅ **Одна таблица** для запроса. | ❌ **Негибкость:** Любое изменение структуры = пересоздание представления. |
| | ❌ **Дублирование данных:** Хранение копиями данных. |

**Когда использовать:**
- Аналитика, отчёты.
- Очень сложные запросы с большим количеством JOIN.

---

### 3. Сравнительная таблица

| Способ | Скорость | Память | Сложность | Консистентность | Когда использовать |
|--------|----------|--------|-----------|-----------------|-------------------|
| **JOIN (DQL)** | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ | ✅ | Списки, страницы выдачи |
| **Partial Object** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ✅ | Таблицы, списки с мин. полями |
| **Second-Level Cache** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ | ⚠️ | Справочники, статичные данные |
| **Materialized View** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⚠️ | Аналитика, сложные отчёты |
| **EAGER (OneToMany)** | ❌ | ❌ | ⭐⭐⭐ | ✅ | **Никогда** (не работает) |

---

### 4. Алгоритм решения N+1

1. **Найдите проблему:** Web Profiler / SQL-логи.
2. **Определите, какие данные нужны:** Все? Или только часть?
3. **Выберите способ:**
   - Нужны все данные? → **JOIN (DQL)**.
   - Нужны только 3-4 поля? → **Partial Object**.
   - Данные не меняются? → **Cache**.
   - Сложный отчёт? → **Materialized View**.
4. **Проверьте:** Запустите тест, измерьте время.
5. **Оптимизируйте:** Добавьте пагинацию (`setFirstResult`, `setMaxResults`).

### 5. Антипаттерны

#### ❌ 1. `findAll()` без фильтров
```php
$orders = $em->getRepository(Order::class)->findAll(); // Миллионы записей!
```
**Решение:** Всегда используйте пагинацию.

#### ❌ 2. `EAGER` для коллекций
```php
#[ORM\OneToMany(targetEntity: OrderItem::class, fetch: 'EAGER')]
```
**Решение:** Используйте явные `JOIN` в DQL.

#### ❌ 3. Загрузка сущностей в цикле
```php
foreach ($orderIds as $id) {
    $order = $em->find(Order::class, $id); // N запросов!
}
```
**Решение:**
```php
$query = $em->createQueryBuilder()
    ->select('o')
    ->from(Order::class, 'o')
    ->where('o.id IN (:ids)')
    ->setParameter('ids', $orderIds)
    ->getQuery()
    ->getResult();
```

#### ❌ 4. Игнорирование `orphanRemoval` и `cascade`
Если вы не настроили `cascade: ['persist']`, Doctrine не знает, как сохранить связанные сущности, и может сделать дополнительные запросы.

---

### Итог

**N+1 — это не баг, это архитектурное решение.**

- Если вам нужны данные → загрузите их **одним запросом** (JOIN).
- Если вам нужны только поля → загрузите **только поля** (Partial Object).
- Если данные статичны → **кэшируйте**.
- Если запрос сложный → **материализуйте**.

**Главное правило:** Не позволяйте Doctrine делать то, что вы не просили. Явно указывайте, какие данные нужны, и как их загружать.

> ⏱ 24.3s · prompt 30656 tok · output 3018 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это классическая дилемма: **«Домен знает правила, а база данных — только строки»**.

Ответ: **Ограничения целостности на уровне БД (DB Constraints) — это последний рубеж обороны (Last Line of Defense).**

### Почему код приложения не гарантирует целостность?

1. **Конкурентность (Race Conditions):**
   - Два процесса одновременно проверяют `SELECT COUNT(*) FROM orders WHERE ...` и видят 0.
   - Оба создают запись.
   - В БД дубликат. Только `UNIQUE` constraint спасет.

2. **Обход приложения:**
   - Прямой SQL-запрос из `psql`.
   - Миграция, скрипт загрузки данных.
   - Another microservice, который не знает про ваши доменные правила.
   - Баг в коде приложения (всегда есть).

3. **Производительность:**
   - Проверка `if ($balance >= $amount)` в PHP + `UPDATE` в БД — это **2 операции**.
   - `CHECK (balance >= 0)` + `UPDATE` — это **1 операция** на уровне ядра БД.
   - Атомарность: БД гарантирует, что обновление и проверка происходят атомарно.

4. **Семантика:**
   - БД должна быть **самодостаточной**. Если вы откроете базу в pgAdmin и увидите `email: NULL` или `balance: -5000`, это должно быть **невозможно**.

---

### Какие средства PostgreSQL используем?

#### 1. `NOT NULL`
**Когда:** Поле обязательно для существования.
```sql
ALTER TABLE users ADD CONSTRAINT users_email_not_null CHECK (email IS NOT NULL);
```
**Почему:** Даже если ваш код забыл передать email, БД не даст создать пользователя без него.

#### 2. `UNIQUE`
**Когда:** Уникальность бизнес-сущности.
```sql
CREATE UNIQUE INDEX idx_users_email ON users (email);
```
**Почему:**
- Защита от дубликатов в конкурентных средах.
- Индексация: `UNIQUE` автоматически создает индекс, что ускоряет `SELECT ... WHERE email = ...`.

#### 3. `CHECK` Constraints
**Когда:** Инварианты, которые не зависят от других таблиц.
```sql
ALTER TABLE orders ADD CONSTRAINT orders_total_positive CHECK (total > 0);
ALTER TABLE users ADD CONSTRAINT users_age_valid CHECK (age >= 0 AND age < 150);
```
**Почему:**
- Дешевле, чем проверка в PHP (оптимизируется БД).
- Атомарность.
- Прочитываемость схемы: открыв DDL, вы видите правила.

**Ограничение:** `CHECK` не может обращаться к другим таблицам (без триггеров).

#### 4. `FOREIGN KEY` (FK)
**Когда:** Ссылки на другие сущности.
```sql
ALTER TABLE order_items ADD CONSTRAINT order_items_order_id_fk 
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE;
```
**Почему:**
- Гарантирует, что `order_items.order_id` всегда существует в `orders.id`.
- `ON DELETE CASCADE` автоматически удаляет дочерние записи.
- Блокировки: FK создает блокировки, которые предотвращают удаление родительской записи, пока есть дочерние (если не `CASCADE`).

#### 5. `EXCLUDE` Constraints (Специфика PostgreSQL)
**Когда:** Пространственные или временные ограничения.
```sql
-- Нельзя иметь пересекающиеся временные интервалы в таблице бронирований
ALTER TABLE bookings ADD CONSTRAINT no_overlapping_bookings
    EXCLUDE USING gist (
        room_id WITH =,
        tsrange(start_time, end_time) WITH &&
    );
```
**Почему:** Очень мощно для календарей, бронирований, временных периодов.

#### 6. Триггеры (Triggers)
**Когда:** Сложная логика, которая не подходит для `CHECK`.
```sql
CREATE FUNCTION update_order_total()
RETURNS TRIGGER AS $$
BEGIN
    NEW.total := NEW.subtotal + NEW.tax;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_order_total
BEFORE INSERT OR UPDATE ON orders
FOR EACH ROW EXECUTE FUNCTION update_order_total();
```
**Почему:**
- Автоматический расчет полей.
- Аудит (запись в историю).
- Синхронизация с другими таблицами.

**Осторожно:** Триггеры — это «магия». Они сложнее в отладке и тестировании. Используйте только если `CHECK` недостаточно.

---

### Матрица: Что где проверяем?

| Правило | Код приложения | БД (PostgreSQL) | Почему |
|---------|---------------|-----------------|--------|
| **Формат email** | ✅ (PHP/Laravel) | ✅ (`CHECK` или `UNIQUE`) | Код — для UX (быстрая ошибка), БД — для целостности. |
| **Длина строки** | ✅ | ✅ (`VARCHAR(255)`) | БД не даст вставить больше, чем разрешено. |
| **Диапазон чисел** | ✅ | ✅ (`CHECK`) | БД дешевле и атомарнее. |
| **Уникальность** | ✅ (проверка перед сохранением) | ✅ (`UNIQUE`) | Только `UNIQUE` спасает от race conditions. |
| **Ссылки** | ✅ (через ORM) | ✅ (`FOREIGN KEY`) | FK — фундамент реляционной модели. |
| **Бизнес-логика** (напр. «Скидка не может быть больше суммы») | ✅ | ⚠️ (`CHECK`) | Можно, но часто лучше в коде, если логика сложная. |
| **Расчетное поле** (напр. `total = subtotal + tax`) | ✅ | ✅ (Триггер или `CHECK`) | Триггер гарантирует актуальность даже при прямом SQL. |
| **Авторизация** (кто может удалить) | ✅ (Voters/Roles) | ❌ | БД не знает про роли. Это ответственность приложения. |

---

### Практические рекомендации

#### 1. Всегда добавляйте ограничения в миграции
Не полагайтесь на «код проверит». Миграция — это часть контракта БД.

```php
// Migration
public function up(): void {
    $this->addSql('ALTER TABLE orders ADD CONSTRAINT orders_total_positive CHECK (total > 0)');
    $this->addSql('CREATE UNIQUE INDEX idx_users_email ON users (email)');
}
```

#### 2. Обработайте ошибки от БД в коде
Когда БД отклонит запись, вы получите `QueryException` или `ConstraintViolationException`.

```php
try {
    $em->persist($order);
    $em->flush();
} catch (UniqueConstraintViolationException $e) {
    // Повернуть красивое сообщение: "Email уже занят"
    throw new EmailAlreadyExistsException();
} catch (ForeignKeyConstraintViolationException $e) {
    // Повернуть: "Некорректный ID заказчика"
    throw new InvalidReferenceException();
}
```

**Важно:** Не ловите все `DatabaseException` и не показывайте их пользователю. Переводите в бизнес-исключения.

#### 3. Индексируйте то, что фильтруете
`UNIQUE` и `FOREIGN KEY` автоматически создают индексы. Для остальных полей создавайте индексы вручную.

```sql
CREATE INDEX idx_orders_customer_id ON orders (customer_id);
CREATE INDEX idx_orders_created_at ON orders (created_at DESC);
```

#### 4. Не дублируйте сложную логику в триггерах
Если бизнес-правило сложное (напр. «Скидка зависит от региона, времени, уровня клиента»), **не пишите триггер**. Держите это в домене.
Триггеры — для простых, атомарных, технических правил (калькуляция, аудит, каскады).

#### 5. Документируйте ограничения
В DDL или в комментариях к миграциям:
```sql
-- Constraint: Balance cannot be negative
-- Rule: If withdrawal would make balance < 0, reject transaction
ALTER TABLE accounts ADD CONSTRAINT accounts_balance_non_negative CHECK (balance >= 0);
```

---

### Итог

| Уровень | Ответственность |
|---------|----------------|
| **Приложение (PHP)** | UX, бизнес-логика, авторизация, сложные правила, отправка ошибок пользователю. |
| **База данных (PG)** | Целостность данных, конкурентность, атомарность, защита от багов и обходов. |

**Философия:**
> **Приложение пытается сделать правильно. БД гарантирует, что невозможно сделать неправильно.**

Если вы доверитесь только коду приложения — вы создадите систему, которая **может** работать неправильно. Если добавите ограничения БД — вы создадите систему, которая **не может** работать неправильно на уровне данных.

> ⏱ 18.0s · prompt 33724 tok · output 2070 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленных запросов в PostgreSQL — это не гадание, а систематический процесс. Я подхожу к нему как инженер: сначала собираю факты, потом формирую гипотезу, потом проверяю ее, и только потом оптимизирую.

### 1. Инструменты диагностики

#### A. `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)`
Главный инструмент. Всегда использую `ANALYZE` (чтобы увидеть реальное время), `BUFFERS` (чтобы видеть кэш), `FORMAT JSON` (чтобы удобно читать).

```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)
SELECT * FROM orders WHERE customer_id = 123 AND created_at > '2023-01-01';
```

**Что смотрю в выводе:**
- **`actual time`** vs **`loops`**: Если `loops` большой (например, 1000), а `actual time` маленький (0.1ms), то суммарное время = 100ms. Если `loops = 1`, а `actual time` = 50ms, то это один медленный шаг.
- **`rows`**: Ожидаемое количество строк. Если `rows = 10` на самом деле, а `rows` в плане = 10000 — статистика устарела.
- **`Buffers`**: 
  - `shared hit`: данные были в кэше (быстро).
  - `shared read`: данные читались с диска (медленно).
- **`Cost`**: `startup cost` vs `total cost`. Если `startup cost` высокий — это плохо для запросов, которые прерываются или не читают все данные.
- **`Junk rows`**: Строки, которые были отброшены фильтром, но прочитаны из таблицы.

#### B. `pg_stat_statements`
Расширение, которое хранит статистику по каждому уникальному SQL-запросу.

```sql
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Топ-10 самых дорогих запросов по общему времени
SELECT 
    query,
    calls,
    total_exec_time,
    mean_exec_time,
    rows
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;
```
**Зачем:** Находит «тихих убийц» — запросы, которые вызываются миллион раз и тратят 10ms каждый. Суммарно это 10 000 секунд!

#### C. `pg_stat_activity`
Покажет, что происходит прямо сейчас.

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
**Зачем:** Видеть, на чем «висит» БД: на I/O (`IO`), на блокировке (`Lock`), на CPU (`CPU`).

#### D. `pg_locks`
Если запрос «завис» — скорее всего, блокировка.

```sql
SELECT 
    blocked_locks.pid AS blocked_pid,
    blocked_activity.query AS blocked_statement,
    blocking_locks.pid AS blocking_pid,
    blocking_activity.query AS blocking_statement
FROM pg_catalog.pg_locks blocked_locks
JOIN pg_catalog.pg_stat_activity blocked_activity ON blocked_activity.pid = blocked_locks.pid
JOIN pg_catalog.pg_locks blocking_locks ON blocking_locks.locktype = blocked_locks.locktype
    AND blocking_locks.database IS NOT DISTINCT FROM blocked_locks.database
    AND blocking_locks.relation IS NOT DISTINCT FROM blocked_locks.relation
    AND blocking_locks.transactionid IS NOT DISTINCT FROM blocked_locks.transactionid
    AND blocking_locks.classid IS NOT DISTINCT FROM blocked_locks.classid
    AND blocking_locks.objid IS NOT DISTINCT FROM blocked_locks.objid
    AND blocking_locks.objsubid IS NOT DISTINCT FROM blocked_locks.objsubid
    AND blocking_locks.pid != blocked_locks.pid
JOIN pg_catalog.pg_stat_activity blocking_activity ON blocking_activity.pid = blocking_locks.pid
WHERE NOT blocked_locks.granted;
```

#### E. `autovacuum` логи
Если статистика устарела — планы будут неверными.

```sql
SELECT * FROM pg_stat_user_tables WHERE last_autovacuum IS NULL OR last_autovacuum < now() - interval '1 day';
```

---

### 2. Типовые причины медленных запросов

#### 1. **Отсутствие индексов**
**Симптом:** `Seq Scan` (последовательное сканирование) на большой таблице.
**Решение:**
```sql
CREATE INDEX idx_orders_customer_id ON orders (customer_id);
```
**Нюанс:** Индекс не помогает, если выборка > 20-30% таблицы. Тогда `Seq Scan` быстрее.

#### 2. **Устаревшая статистика**
**Симптом:** План выглядит идиотски (например, `Nested Loop` вместо `Hash Join`).
**Решение:**
```sql
ANALYZE orders;
```
**Нюанс:** `ANALYZE` блокирует таблицу (read-only). Не делайте это в production-часы пик.

#### 3. **Неправильные JOIN**
**Симптом:** `Nested Loop` с большим количеством `loops`.
**Решение:**
- Добавить индексы на колонки JOIN.
- Проверить, не инвертирован ли порядок JOIN (PostgreSQL сам оптимизирует, но иногда помогает явно указать).
- Использовать `Hash Join` для больших таблиц (если индексов нет).

#### 4. **Выборка лишних данных**
**Симптом:** `SELECT *` из большой таблицы.
**Решение:**
```sql
-- Было
SELECT * FROM orders;

-- Стало
SELECT id, customer_id, total FROM orders;
```
**Нюанс:** Если нужен только один столбец, и есть covering index — будет `Index Only Scan`, что быстрее.

#### 5. **Нарушение нормализации / Денормализация без индексов**
**Симптом:** Много JOIN между небольшими таблицами.
**Решение:**
- Проверить, не можно ли заменить JOIN на `MATERIALIZED VIEW`.
- Или наоборот: денормализовать, если JOIN'ы дорогие.

#### 6. **Функции в WHERE**
**Симптом:** Индекс не используется, потому что `WHERE lower(email) = 'x'`.
**Решение:**
```sql
-- Было
WHERE lower(email) = 'test@example.com'

-- Стало
WHERE email ILIKE 'test@example.com'
-- Или создать выраженный индекс:
CREATE INDEX idx_email_lower ON orders (lower(email));
```

#### 7. **Массивы/JSONB без GIN индексов**
**Симптом:** Медленный поиск по JSONB.
**Решение:**
```sql
CREATE INDEX idx_order_meta ON orders USING GIN (meta jsonb_path_ops);
```

#### 8. **Блокировки (Locks)**
**Симptom:** Запрос ждет `Lock` от другого процесса.
**Решение:**
- Найти блокирующий запрос (`pg_locks`).
- Сократить время транзакций.
- Использовать `NOWAIT` или `SET LOCK_TIMEOUT`.

#### 9. **Маленький кэш (shared_buffers)**
**Симptom:** Много `shared read` (I/O).
**Решение:**
- Увеличить `shared_buffers` (обычно 25% RAM).
- Использовать более быстрый диск (SSD/NVMe).

#### 10. **N+1 на уровне приложения**
**Симptom:** Много одинаковых простых запросов.
**Решение:**
- Объединить в один запрос с JOIN или `IN`.
- Использовать `Batch` API.

---

### 3. Алгоритм оптимизации

1. **Повторить запрос** с `EXPLAIN ANALYZE`.
2. **Найти самый медленный узел** в дереве плана (по `actual time` * `loops`).
3. **Посмотреть на тип сканирования:**
   - `Seq Scan`? → Нужен индекс? Или выборка большая?
   - `Index Scan`? → Индекс не покрывает запрос?
   - `Nested Loop`? → Нет индекса на внутреннюю таблицу?
   - `Hash Join`? → Мало памяти?
4. **Проверить статистику:** `ANALYZE`.
5. **Проверить блокировки:** `pg_locks`.
6. **Проверить I/O:** `pg_stat_activity.wait_event_type`.
7. **Внедрить изменение:** Индекс, переписать запрос, добавить кэш.
8. **Замерить результат:** Сравнить `EXPLAIN ANALYZE` до/после.

### 4. Антипаттерны

#### ❌ 1. «Просто добавить индекс»
Не каждый `Seq Scan` — проблема. Если таблица маленькая (1000 строк) — `Seq Scan` быстрее `Index Scan`.

#### ❌ 2. «Переписать запрос на CTE»
CTE в PostgreSQL 12+ не оптимизируются (они материализуются). Это может **ухудшить** производительность.

#### ❌ 3. «Использовать `ORDER BY ... LIMIT 1`»
Это работает, только если есть индекс по `ORDER BY`. Иначе будет полный `Seq Scan` + сортировка.

#### ❌ 4. «Игнорировать `EXPLAIN`»
Без `EXPLAIN` вы гадаете.

#### ❌ 5. «Оптимизировать без нагрузки»
Запрос быстрый на 100 записях, но медленный на 10 000 000. Тестируйте на реальных объемах.

---

### 5. Чек-лист для production

- [ ] `pg_stat_statements` настроен и логится.
- [ ] Индексы на всех `WHERE`, `JOIN`, `ORDER BY` колонках.
- [ ] `ANALYZE` выполняется регулярно (autovacuum).
- [ ] `shared_buffers` настроен под объем RAM.
- [ ] `work_mem` настроен под сложные запросы (сортировка, хеширование).
- [ ] Блокировки мониторятся.
- [ ] I/O на SSD/NVMe.
- [ ] Приложения не делают N+1.

### Итог

Медленный запрос — это **симптом**, а не болезнь. Лечите причину:
- Нет индекса → добавляйте.
- Устаревшая статистика → `ANALYZE`.
- Плохой план → переписывайте запрос.
- Блокировки → сокращайте транзакции.
- I/O → улучшайте железо.

**Главное правило:** Сначала `EXPLAIN ANALYZE`, потом изменения.

> ⏱ 20.2s · prompt 35844 tok · output 2534 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD — это **кластер сущностей**, которые рассматриваются как **единое целое** с точки зрения целостности данных.

Это «транзакционная единица» домена. Правила DDD:
1.  **Только Aggregate Root** имеет идентификатор, который используется внешним миром.
2.  **Только Aggregate Root** можно сохранять/удалять через Repository.
3.  **Только Aggregate Root** валидирует инварианты (инвариантность) всего кластера.
4.  Внешний мир не может иметь ссылки на внутренние сущности агрегата (Child Entities) напрямую.

---

### Как определять границы агрегата?

Используйте три критерия:

#### 1. Инварианты (Invariants)
**Вопрос:** Какие правила должны выполняться *всегда* одновременно? Если изменение одной сущности требует изменения другой для сохранения целостности — они в одном агрегате.

*   *Пример:* `Order` и `OrderItem`. Если вы добавляете товар в заказ, сумма заказа должна пересчитываться. Если вы удаляете товар, сумма меняется. Это единое целое.

#### 2. Транзакционность (Transaction Boundary)
**Вопрос:** Какие изменения должны происходить **атомарно**? Если операция «либо сделай всё, либо не делай» — это один агрегат.

*   *Пример:* `Account` и `Transaction`. Вы не можете провести транзакцию, если она не изменила баланс счета. Они должны быть в одной транзакции.

#### 3. Ссылки (References)
**Вопрос:** Какие сущности должны быть **идентифицируемы** по ID, а какие должны быть **загружены как часть** родительского объекта?

*   *Пример:* `Order` ссылается на `Customer` по ID. `Order` содержит `OrderItem` как коллекцию.
*   *Правило:* Агрегаты **ссылаются** друг на друга по ID. Внутри агрегата сущности **вложены** (composition).

---

### Пример: E-Commerce (Заказ)

#### Правильная граница

**Агрегат: `Order`**
*   **Root:** `Order`
*   **Children:** `OrderItem` (1..N)
*   **Value Objects:** `Money`, `Address`, `Money`, `DateRange`

**Почему так?**
1.  **Инвариант:** Сумма заказа = сумма всех товаров. Это правило выполняется *внутри* `Order`.
2.  **Транзакция:** Добавление товара и пересчет суммы должны происходить атомарно.
3.  **Инкапсуляция:** Внешний код не знает про `OrderItem`. Он вызывает `order.addItem(product)`, а не `orderItemRepository.save(item)`.

**Агрегат: `Customer`**
*   **Root:** `Customer`
*   **Children:** `Address` (может быть 0..N, но обычно это VO или отдельный агрегат)

**Почему `Customer` — отдельный агрегат?**
1.  `Customer` может существовать без `Order`.
2.  `Order` ссылается на `Customer` по ID (`customerId`).
3.  Изменение данных клиента (например, смена email) не должно требовать пересохранения всех заказов клиента.

#### Как они связаны?
```php
class Order {
    private int $customerId; // Ссылка на другой агрегат по ID!
    private Collection $items; // Вложенные сущности (Child Entities)
}
```

---

### Типовая ошибка: «Супер-агрегат» (God Aggregate)

**Описание ошибки:**
Разработчики объединяют все связанные сущности в один огромный агрегат, потому что «они связаны по логике».

**Пример ошибки:**
Один агрегат `Shop`, который включает:
*   `Customer`
*   `Order`
*   `Product`
*   `Inventory`
*   `Payment`
*   `Shipping`

**Почему это плохо?**

1.  **Транзакционные проблемы:**
    *   Чтобы изменить `Customer` (сменить email), нужно загрузить **весь** `Shop` (все заказы, все товары, весь инвентарь).
    *   Сохранение `Customer` требует сохранения всех `Order` и `Product`.
    *   Это **огромная** транзакция. Если она упадет — откатится всё.

2.  **Конкурентность:**
    *   Два пользователя одновременно изменяют `Order` и `Customer`.
    *   Так как они в одном агрегате, возникает **конфликт блокировок** на весь объект `Shop`.
    *   Производительность падает до нуля.

3.  **Нарушение SRP:**
    *   Один класс/агрегат отвечает за логистику, платежи, каталог и CRM.
    *   При изменении правил логистики вы трогаете код платежей.

4.  **Сложность маппинга в ORM:**
    *   Doctrine пытается загрузить всё дерево.
    *   N+1 запросы становятся неизбежными, если вы не загрузите всё разом.

---

### Правильный подход: Малые агрегаты + Ссылки по ID

Разбейте «Супер-агрегат» на маленькие агрегаты:

1.  **`Customer` Aggregate**
    *   Содержит: `Customer`, `Addresses`.
    *   Сохраняет: Данные клиента.

2.  **`Order` Aggregate**
    *   Содержит: `Order`, `OrderItems`.
    *   Ссылка: `customerId` (ID, а не объект `Customer`).
    *   Сохраняет: Состояние заказа.

3.  **`Inventory` Aggregate**
    *   Содержит: `InventoryItem`.
    *   Ссылка: `productId` (ID).
    *   Сохраняет: Количество на складе.

4.  **`Payment` Aggregate**
    *   Содержит: `Payment`.
    *   Ссылка: `orderId` (ID).
    *   Сохраняет: Статус платежа.

#### Как они общаются?
Через **Domain Events**:
1.  `Order` меняется → публикует событие `OrderConfirmed`.
2.  `Inventory` слушает `OrderConfirmed` → уменьшает количество.
3.  `Payment` слушает `OrderConfirmed` → инициирует платеж.

**Плюсы:**
*   Каждая транзакция маленькая.
*   Конфликты блокировок локальны.
*   Легко тестировать и масштабировать.
*   Нарушение инкапсуляции устранено (каждый агрегат знает только свои правила).

---

### Итог: Чек-лист для определения границ

| Вопрос | Да | Нет |
|--------|----|-----|
| Должны ли эти сущности меняться **атомарно**? | **Один агрегат** | Разные агрегаты |
| Есть ли общий **инвариант**, который нарушится, если изменить одну без другой? | **Один агрегат** | Разные агрегаты |
| Можно ли изменить одну сущность, **не трогая** другую? | Разные агрегаты | **Один агрегат** |
| Нужен ли **ID** для ссылок из других агрегатов? | Разные агрегаты | Вложенная сущность |

**Золотое правило:**
> **Агрегаты должны быть как можно мельче.**
> Чем меньше агрегат, тем меньше транзакций, меньше конфликтов и проще тестирование.
> Связность между агрегатами достигается через **ID** и **события**, а не через вложенность.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это одна из самых сложных и опасных зон в проектировании систем с высокой нагрузкой. Если два процесса одновременно изменяют один и тот же агрегат (например, `Order` или `Account`), у вас возникает **конфликт состояний**.

Вот как это работает под капотом и как правильно это проектировать.

### 1. Что произойдет в PostgreSQL/ORM?

#### А. Optimistic Locking (Оптимистичная блокировка) — **Рекомендуемый подход**
Это «соглашение между приложениями». Мы полагаем, что конфликты редки, и проверяем их только при сохранении.

**Как работает:**
1.  В таблице есть колонка `version` (integer).
2.  При чтении: `SELECT * FROM orders WHERE id = 1` → возвращается `version = 5`.
3.  Процесс A меняет данные и пытается сохранить: `UPDATE orders SET total=100, version=6 WHERE id=1 AND version=5`.
4.  Процесс B **одновременно** меняет данные и пытается сохранить: `UPDATE orders SET status='paid', version=6 WHERE id=1 AND version=5`.
5.  **Результат:**
    *   Процесс A успешно обновил строку (1 запись изменена).
    *   Процесс B **не изменил ни одной записи** (потому что `version` уже стал 6).
    *   ORM (Doctrine) видит, что `rowCount = 0` и выбрасывает **`OptimisticLockException`**.

**Вывод:** Второй процесс узнает об ошибке **только в момент сохранения**. До этого он работает с устаревшими данными.

#### Б. Pessimistic Locking (Пессимистичная блокировка) — **Тяжелый подход**
Это «физическая блокировка». Мы блокируем строку в БД сразу при чтении.

**Как работает:**
1.  Процесс A выполняет: `SELECT * FROM orders WHERE id = 1 FOR UPDATE`.
2.  Процесс B выполняет: `SELECT * FROM orders WHERE id = 1 FOR UPDATE`.
3.  **Результат:**
    *   Процесс A получает блокировку.
    *   Процесс B **ждет** (hang), пока Процесс A не завершит транзакцию (`COMMIT` или `ROLLBACK`).
    *   После `COMMIT` от A, B получает строку с обновленными данными.

**Вывод:** Процесс B «зависает» и тратит ресурсы, ожидая. Это создает узкое место (bottleneck).

---

### 2. Как проектировать с учетом этого?

#### Вариант 1: Оптимистичная блокировка + Retries (Стандарт)

**Архитектура:**
1.  В сущности есть поле `version`.
2.  При сохранении ORM увеличивает `version`.
3.  Если возникает `OptimisticLockException`, приложение **повторяет операцию**.

**Реализация в PHP/Symfony:**

```php
class OrderService {
    public function addItemToOrder(int $orderId, Product $product): void {
        $maxRetries = 3;
        $attempt = 0;

        while (true) {
            try {
                $em = $this->entityManager;
                $order = $em->find(Order::class, $orderId);
                
                // Бизнес-логика
                $order->addItem($product);
                
                $em->flush(); // Здесь может быть OptimisticLockException
                return;
            } catch (OptimisticLockException $e) {
                $attempt++;
                if ($attempt > $maxRetries) {
                    throw new ConflictException('Could not update order due to concurrent modifications');
                }
                
                // Опционально: небольшая задержка (backoff)
                usleep(rand(1000, 5000));
                
                // ВАЖНО: Нужно очистить менеджер сущностей, чтобы перезагрузить свежие данные
                $em->clear(Order::class); 
            }
        }
    }
}
```

**Почему это хорошо?**
*   Нет блокировок на чтение.
*   Высокая пропускная способность (throughput).
*   Конфликты разрешаются автоматически.

**Риски:**
*   Если конфликты **очень частые** (горячая строка), у вас будет много повторных попыток, и производительность упадет.
*   Бизнес-логика должна быть **идемпотентной** (повторное выполнение не должно ломать состояние).

---

#### Вариант 2: Pessimistic Locking (Для «горячих» строк)

**Когда использовать:**
*   Если строка меняется **очень часто** (например, `Counter`, `Inventory` на популярном товаре).
*   Если бизнес-логика **длительная** (вызов внешнему API на 2 секунды).

**Реализация в Doctrine:**

```php
// В QueryBuilder или DQL
$query = $em->createQueryBuilder()
    ->select('o')
    ->from(Order::class, 'o')
    ->where('o.id = :id')
    ->setParameter('id', $orderId)
    ->setLockMode(LockMode::OPTIMISTIC, $version) // Или PESSIMISTIC_WRITE
    ->getQuery();

// Или в Entity (редко, но бывает)
#[ORM\LockMode(LockMode::OPTIMISTIC)]
class Order {
    #[ORM\Version]
    private int $version;
}
```

**Лучшая практика для Pessimistic:**
Используйте **короткие транзакции**.
1.  `SELECT FOR UPDATE`.
2.  Сделайте изменение.
3.  `COMMIT`.
4.  Не держите транзакцию открытой во время внешних вызовов (API, email).

**Пример:**
```php
$em = $this->entityManager;
$em->lock($order, LockMode::PESSIMISTIC_WRITE);

$order->addItem($product);

$em->flush();
$em->clear(); // Разблокировать
```

---

#### Вариант 3: Event Sourcing / CQRS (Для максимальной масштабируемости)

Если агрегат меняется очень часто и конфликты неизбежны, раздельте **письмо** и **чтение**.

1.  **Write Model:** Только добавление событий (`OrderItemAdded`, `OrderStatusChanged`).
2.  **Read Model:** Проекции для чтения.
3.  Конфликты решаются через **порядок событий** (event stream).

Это сложно, но решает проблему «горячих строк» полностью.

---

### 3. Типовые ошибки проектирования

#### ❌ Ошибка 1: Долгие транзакции
```php
// ПЛОХО
$em->lock($order, LockMode::PESSIMISTIC_WRITE);
$this->callExternalPaymentApi(); // 2 секунды блокировки!
$order->setStatus('paid');
$em->flush();
```
**Решение:** Внешние вызовы **после** commit. Или использование очереди.

#### ❌ Ошибка 2: Отсутствие `version`
Если у вас нет `@Version` или `version` колонки, вы не можете использовать Optimistic Locking.
**Решение:** Всегда добавляйте `version` поле для сущностей, которые меняются часто.

#### ❌ Ошибка 3: Игнорирование `OptimisticLockException`
```php
// ПЛОХО
try {
    $em->flush();
} catch (Exception $e) {
    // Пропускаем ошибку и молчим
}
```
**Решение:** Либо ретраем, либо показываем пользователю «Повторите попытку».

#### ❌ Ошибка 4: Глобальные блокировки
Блокировка всей таблицы `orders` вместо строки.
**Решение:** Всегда блокируйте конкретную строку (`WHERE id = X`).

---

### 4. Сравнительная таблица

| Критерий | Optimistic Locking | Pessimistic Locking | Event Sourcing |
|----------|--------------------|---------------------|----------------|
| **Производительность (чтение)** | 🚀 Высокая | 🐢 Низкая (блокировки) | 🚀 Высокая |
| **Производительность (письмо)** | 🚀 Высокая (если конфликтов мало) | 🐢 Низкая (если конфликтов много) | 🚀 Очень высокая |
| **Сложность** | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Надежность** | ⭐⭐⭐ (зависит от ретраев) | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Когда использовать** | 90% случаев | Горячие строки, длительные операции | Очень высокая нагрузка, аудит |

---

### 5. Рекомендация

1.  **Поставьте `@Version`** (или `version` колонку) на все сущности, которые могут меняться конкурентно.
2.  **Используйте Optimistic Locking** по умолчанию.
3.  **Реализуйте Retry Logic** в Application Service.
4.  **Делайте транзакции короткими** (менее 50 мс).
5.  **Если конфликты частые** — переосмыслите архитектуру (CQRS, Event Sourcing, разделение агрегатов).

**Главное правило:**
> **Конкуренция — это норма, а не баг.**
> Ваша система должна быть спроектирована так, чтобы корректно обрабатывать одновременные изменения, а не предотвращать их любой ценой.

> ⏱ 14.9s · prompt 38421 tok · output 1809 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это механизм **асинхронной коммуникации** внутри домена и с внешними системами. Они позволяют развязать модули, которые логически связаны, но технически должны работать независимо.

### 1. Когда публиковать доменные события?

**Правило:** Событие публикуется **после** успешного изменения состояния агрегата, но **до** завершения транзакции (или через механизм Outbox, см. ниже).

**Ключевые критерии:**
1.  **Состояние изменилось:** `Order` стал `Paid`, `User` был создан, `Inventory` уменьшилось.
2.  **Другие модули должны отреагировать:** Отправка email, обновление индекса, логирование, интеграция с CRM.
3.  **Это бизнес-факт:** «Заказ оплачен» — это факт, а не техническая операция.

**Когда НЕ использовать:**
- Для простых синхронных вызовов между сервисами одного модуля (используйте обычный вызов метода).
- Для передачи данных, которые нужны **сразу** в ответ пользователю (используйте обычный вызов).
- Для ошибок (ошибки — это исключения, а не события).

---

### 2. Как публиковать и обрабатывать?

#### А. Публикация (Producer)

В Symfony есть два основных способа:

**1. Через Event Dispatcher (синхронно/асинхронно)**
```php
// В Aggregate Root или Domain Service
class Order {
    private array $domainEvents = [];

    public function markAsPaid(): void {
        $this->status = 'paid';
        $this->recordEvent(new OrderPaidEvent($this->getId()));
    }

    public function recordEvent(object $event): void {
        $this->domainEvents[] = $event;
    }

    public function pullEvents(): array {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}

// В Application Service
class OrderService {
    public function __construct(
        private OrderRepository $repo,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $em
    ) {}

    public function payOrder(int $orderId): void {
        $order = $this->repo->findById($orderId);
        $order->markAsPaid();
        
        $this->repo->save($order);
        $this->em->flush(); // Сохраняем в БД

        // Публикуем события ПОСЛЕ commit
        foreach ($order->pullEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
```

**2. Через Symfony Messenger (асинхронно)**
```php
// Если нужно асинхронно
$this->bus->dispatch(new SendEmailMessage($order->getCustomer()->getEmail()));
```

#### Б. Обработка (Consumer)

```php
// Слушатель
#[AsEventListener(event: OrderPaidEvent::class)]
class SendOrderEmailListener {
    public function __construct(private Mailer $mailer) {}

    public function onOrderPaid(OrderPaidEvent $event): void {
        $orderId = $event->getOrderId();
        // Логика отправки email
        $this->mailer->send('Order paid', "Order {$orderId} is paid");
    }
}
```

**Важно:** Слушатели **не знают** про `Order`. Они знают только про `OrderPaidEvent`. Это инкапсуляция.

---

### 3. Связь с транзакциями БД (Critical Point)

Это самая сложная часть. **События не должны отправляться, если транзакция откатится.**

#### Проблема: «Ложные успехи»
Если вы делаете так:
```php
$em->persist($order);
$dispatcher->dispatch($event); // Email отправлен
$em->flush();                 // Ошибка в БД → ROLLBACK
```
**Результат:** Email улетел, но заказ не сохранен. **Несогласованность.**

#### Решение 1: Dispatch после Flush (Простое, но рискованное)
```php
$em->persist($order);
$em->flush(); // COMMIT
$em->commit(); // Если используется explicit transaction

// Теперь безопасно
$dispatcher->dispatch($event);
```
**Риски:**
- Если процесс упадет между `commit` и `dispatch` — событие потеряно.
- В Symfony `flush()` и `commit()` часто совпадают, но есть нюансы.

#### Решение 2: Transactional Outbox Pattern (Промышленный стандарт)
Это **золотой стандарт** для надежных событий.

**Как работает:**
1.  Событие сохраняется в таблицу `outbox` **в той же транзакции**, что и бизнес-данные.
2.  Отдельный воркер (poller) читает из таблицы `outbox` и отправляет события в очередь/слушателям.
3.  После успешной отправки строка удаляется из `outbox`.

**Преимущества:**
- **Атомарность:** Бизнес-данные и событие сохраняются вместе.
- **Надежность:** Если БД работает — событие не потеряется.
- **Идемпотентность:** Воркер может безопасно повторять отправку.

**Реализация в Symfony:**
1.  Создайте таблицу `outbox_messages`.
2.  В `OrderService` после `flush()` вставьте запись в `outbox`.
3.  Запустите воркер `bin/console outbox:dispatch`.

---

### 4. Какие проблемы решает этот подход?

| Проблема | Решение через события |
|----------|----------------------|
| **Жесткая связанность** | Модули не знают друг о друге. |
| **Сложность отладки** | Каждый слушатель тестируется отдельно. |
| **Масштабируемость** | Слушатели можно вынести в отдельные микросервисы. |
| **Гибкость** | Легко добавить нового слушателя, не меняя код производителя. |
| **Асинхронность** | Длительные операции (email, index) не блокируют основной запрос. |

---

### 5. Какие проблемы создает этот подход?

| Проблема | Описание |
|----------|----------|
| **Потеря событий** | Если нет Outbox, события могут потеряться при сбое. |
| **Несогласованность** | Отложенное состояние (Eventual Consistency). Клиент видит «оплачено», а email еще не отправлен. |
| **Сложность отладки** | Трудно проследить цепочку: `Order → Event → Email → DB`. |
| **Идемпотентность** | Слушатель должен быть идемпотентным (повторная обработка не должна ломать данные). |
| **Порядок событий** | События могут прийти в разном порядке (например, `OrderPaid` раньше `OrderCreated`). |

---

### 6. Бест-практики

#### 1. Никогда не публикуйте события из Aggregate Root напрямую в EventDispatcher
Аgregates не должны знать о инфраструктуре.
```php
// ПЛОХО
class Order {
    public function markAsPaid(): void {
        $this->status = 'paid';
        $this->eventDispatcher->dispatch(new OrderPaidEvent()); // Нарушение инкапсуляции
    }
}

// ХОРОШО
class Order {
    public function markAsPaid(): void {
        $this->status = 'paid';
        $this->recordEvent(new OrderPaidEvent());
    }
}
```
А `Application Service` извлекает и публикует события.

#### 2. Именуйте события как прошедшее время
`OrderPaid`, `UserRegistered`, `PaymentProcessed`.
Не `OrderPay`, `UserRegister`.

#### 3. Делайте слушателей идемпотентными
```php
class SendEmailListener {
    public function onOrderPaid(OrderPaidEvent $event): void {
        // Проверьте, уже отправляли ли мы email для этого заказа
        if ($this->emailSent($event->getOrderId())) {
            return;
        }
        
        $this->mailer->send(...);
        $this->markEmailSent($event->getOrderId());
    }
}
```

#### 4. Используйте Outbox Pattern для production
Если у вас есть критичные события (платежи, заказы) — обязательно используйте Outbox.

#### 5. Логгируйте события
Каждое событие должно быть залоггировано с ID транзакции и payload.

---

### 7. Итоговая архитектура

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Service   │────▶│   Aggregate │────▶│   Database   │
│  (App)      │     │   (Domain)  │     │  (Business)  │
└─────────────┘     └─────────────┘     └─────────────┘
       │
       │ 1. Save
       ▼
┌─────────────┐
│   Database  │
│   (Outbox)  │  <-- 2. Сохраняется в той же транзакции
└─────────────┘
       │
       │ 3. Poller читает
       ▼
┌─────────────┐     ┌─────────────┐
│   Queue     │────▶│  Listener   │
│  (RabbitMQ) │     │  (Email)    │
└─────────────┘     └─────────────┘
```

**Главное правило:**
> **Событие должно быть гарантированно доставлено только если бизнес-данные сохранены.**
> Используйте **Transactional Outbox** для гарантии.

> ⏱ 17.8s · prompt 42535 tok · output 2281 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальные вопросы DDD, от которых зависит качество всей доменной модели. Ошибки здесь приводят к «анемичным» сущностям (Entity без логики) или неправильному хранению данных.

### 1. Value Object vs Entity

Главное отличие: **Identity (Идентичность)** и **Immutability (Неизменяемость)**.

| Критерий | Entity (Сущность) | Value Object (Объект Значения) |
|----------|-------------------|--------------------------------|
| **Идентичность** | Определяется **ID** (например, `User#123`) | Определяется **значением** (например, `Email: "test@mail.com"`) |
| **Сравнение** | По ID (`$a->getId() === $b->getId()`) | По значениям всех полей (`$a->equals($b)`) |
| **Мутабельность** | Мутабельны (изменяют состояние) | **Неизменяемы** (Immutability) |
| **Жизненный цикл** | Долгий (создан, изменен, удален) | Короткий (создан, использован, выброшен) |
| **Примеры** | `User`, `Order`, `Product` | `Money`, `Email`, `Address`, `DateRange` |

#### Ключевой момент про неизменяемость
В Value Object **не должно быть сеттеров**. Если вам нужно «изменить» VO, вы создаете **новый** объект.

```php
// ПЛОХО (Entity-стиль)
class Money {
    private int $amount;
    public function add(int $x): void {
        $this->amount += $x;
    }
}

// ХОРОШО (Value Object-стиль)
class Money {
    private function __construct(private int $amount, private string $currency) {}

    public static function of(int $amount, string $currency): self {
        return new self($amount, $currency);
    }

    public function add(Money $other): self {
        if ($this->currency !== $other->currency) {
            throw new \Exception('Currency mismatch');
        }
        // Создаем НОВЫЙ объект
        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

**Почему это важно?**
1.  **Потокобезопасность:** Немыслимо, чтобы два процесса меняли один и тот же `Money`.
2.  **Кэширование:** VO можно безопасно кэшировать и передавать в любом месте.
3.  **Чистота:** Вы точно знаете, что объект не изменится под ногами.

---

### 2. Как решать: Entity или Value Object?

Используйте «Тест копирования» (Copy Test):

**Вопрос:** Если я скопирую этот объект, будет ли копия **отличаться** от оригинала по смыслу?

1.  **Если ДА** → **Entity**.
    *   *Пример:* Два `User` с одинаковым email, но разным ID — это **разные** пользователи (разные логины, разные пароли). Копия — это новый пользователь.
2.  **Если НЕТ** → **Value Object**.
    *   *Пример:* Два `Money` с `amount=100, currency=USD` — это **одни и те же** деньги. Копия — это не что-то новое, это та же самая ценность.

#### Пограничные случаи

| Концепция | Тип | Почему |
|-----------|-----|--------|
| `Address` | **VO** | Адрес «улица, город» — это значение. Если у двух пользователей одинаковый адрес, это один и тот же адрес. |
| `Customer` | **Entity** | Клиент имеет ID, историю, статус. Два клиента с одинаковым email — разные люди. |
| `Product` | **Entity** | Товар имеет ID, цену, наличие. Копия товара — это новый товар (складская единица). |
| `ProductCode` (SKU) | **VO** | Код товара — это значение. `SKU-123` всегда означает один и тот же товар. |
| `Order` | **Entity** | Заказ имеет ID, статус, историю. |
| `OrderNumber` | **VO** | Номер заказа — это значение (хотя часто бывает Entity, если есть история по нему). |

**Совет:** Если сомневаетесь — сделайте **Value Object**. Его проще тестировать, хранить и использовать. Преобразовать VO в Entity позже проще, чем наоборот (нужно добавлять ID, репозитории, логичу жизненного цикла).

---

### 3. Ubiquitous Language (Единый Язык)

**Ubiquitous Language (UL)** — это согласованная терминология, которую используют **все**: бизнес-аналитики, разработчики, тестировщики и пользователи.

**Ключевое правило:** Имена классов, методов и переменных в коде должны **совпадать** с терминами, которые использует бизнес.

#### Пример без UL (Плохо)

**Бизнес говорит:**
> «Пользователь должен пройти верификацию, прежде чем получить доступ к премиум-контенту».

**Код (без UL):**
```php
class User {
    private bool $isVerified; // Какой верификацией? Email? Телефон?
    private int $userLevel;   // Что такое level? 1, 2, 3?
    
    public function checkAccess(): bool {
        return $this->isVerified && $this->userLevel > 1;
    }
}
```
**Проблема:**
- `isVerified` — это верификация email или KYC?
- `userLevel` — это VIP-статус или количество покупок?
- Новому разработчику непонятно, что делает `checkAccess()`.

#### Пример с UL (Хорошо)

**Бизнес говорит:**
> «Клиент должен пройти KYC-верификацию, чтобы стать **Premium Client**. Только **Premium Client** может покупать **Exclusive Products**».

**Код (с UL):**
```php
// Имена классов совпадают с терминологией бизнеса
class Client {
    private KycStatus $kycStatus;
    private ClientTier $tier;

    public function isPremium(): bool {
        return $this->tier === ClientTier::Premium;
    }

    public function canPurchase(ExclusiveProduct $product): void {
        if (!$this->isPremium()) {
            throw new \DomainException('Only Premium Clients can purchase Exclusive Products');
        }
        // ...
    }
}

enum KycStatus {
    case NotVerified;
    case Verified;
    case Suspended;
}

enum ClientTier {
    case Basic;
    case Premium;
}
```
**Преимущества:**
1.  **Прочитываемость:** Код читается как документация.
2.  **Меньше багов:** Бизнес знает, что такое `KycStatus`, и может точно сформулировать требования.
3.  **Тестируемость:** Тесты называются понятными именами: `test_premium_client_can_purchase_exclusive_product()`.

#### Как внедрить UL?

1.  **Совместное моделирование (Event Storming / Domain Mapping):** Сядьте с бизнесом и нарисуйте процесс. Запищите все ключевые термины на стикерах.
2.  **Глоссарий:** Создайте файл `DOMAIN_LANGUAGE.md` в корне проекта.
3.  **Code Review:** Требуйте, чтобы имена методов и классов соответствовали глоссарию.
4.  **Refactoring:** Если в коде появилось `User` вместо `Client` — переименуйте. Это не «косметика», это выравнивание языков.

---

### Итог

| Понятие | Суть |
|---------|------|
| **Entity** | Уникальный объект с ID и жизненным циклом. Меняется. |
| **Value Object** | Объект, определяемый своими свойствами. Неизменяем. |
| **Ubiquitous Language** | Единая терминология для бизнеса и кода. |

**Главное правило DDD:**
> **Код должен отражать бизнес-правила.**
> Если бизнес говорит «Клиент», в коде должно быть `Client`, а не `User`.
> Если бизнес говорит «Деньги», в коде должно быть `Money`, а не `price` или `amount`.

> ⏱ 16.6s · prompt 44876 tok · output 1948 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Чистая архитектура в PHP-приложении — это не про конкретные папки, а про **направление зависимостей**. Зависимости должны указывать **только внутрь** (от инфраструктуры к домену).

### 1. Правило зависимостей (Dependency Rule)

> **Внутренний слой не может зависеть от внешнего.**
> Если внешний слой зависит от внутреннего — это нормально. Если внутренний зависит от внешнего — это нарушение.

```
┌─────────────────────────────────────────────────────────────┐
│  Interface Adapters (Controller, Repository, Queue Handler) │  <-- Зависят от Domain
├─────────────────────────────────────────────────────────────┤
│  Domain (Entities, Value Objects, Domain Services, Events)  │  <-- НЕ зависит от ничего
└─────────────────────────────────────────────────────────────┘
```

- **Domain** — сердце приложения. Чистый PHP, без фреймворков.
- **Application/Use Cases** — оркестрация домена. Зависит от Domain.
- **Interface Adapters** — адаптеры (HTTP, БД, очереди). Зависят от Application/Domain.
- **Frameworks & Drivers** — Symfony, Doctrine. Зависят от Interface Adapters.

---

### 2. Структура проекта (Пример)

```
app/
├── Domain/                 # Внутренний слой (Чистый PHP)
│   ├── Entity/             # Aggregate Roots и Child Entities
│   ├── ValueObject/        # Money, Email, DateRange
│   ├── Event/              # Domain Events (OrderPaid, UserCreated)
│   ├── Exception/          # Domain Exceptions
│   └── Service/            # Domain Services (если логика не в Entity)
│
├── Application/            # Слой использования (Use Cases)
│   ├── Command/            # Объекты команд (CreateOrderCommand)
│   ├── Query/              # Объекты запросов (GetOrderQuery)
│   ├── Service/            # Application Services (OrderService)
│   └── Port/               # Интерфейсы (Repository Interfaces)
│
├── Infrastructure/         # Адаптеры (Внешний слой)
│   ├── Persistence/        # Doctrine Entities и Repositories
│   │   ├── Entity/         # Сущности Doctrine (наследники Domain или обёртки)
│   │   └── Repository/     # Реализации интерфейсов из Application
│   ├── Messaging/          # Обработчики очередей (Consumers)
│   ├── Http/               # Контроллеры Symfony, Request/Response DTOs
│   └── Notification/       # Адаптеры отправки email/SMS
│
├── Config/                 # Конфигурация DI-контейнера
└── bin/                    # Консольные команды
```

---

### 3. Где что лежит?

#### A. Domain (Внутренний слой)
**Что здесь:**
- **Entities/Value Objects:** Чистые PHP-классы с бизнес-логикой.
- **Domain Events:** Классы событий (без наследования от Symfony Event).
- **Domain Exceptions:** Исключения бизнес-логики.
- **Domain Services:** Логика, которая не вписывается в одну сущность (например, `PriceCalculator`).

**Что здесь НЕ должно быть:**
- `use Symfony\...`
- `use Doctrine\...`
- Атрибуты `#[ORM\Entity]`
- Зависимости от `EntityManager`

**Пример:**
```php
// app/Domain/Entity/Order.php
class Order {
    private int $id;
    private array $items;
    private Money $total;

    public function addItem(Product $product, int $quantity, Money $price): void {
        $this->items[] = new OrderItem($product, $quantity, $price);
        $this->total = $this->calculateTotal();
    }

    private function calculateTotal(): Money {
        $total = Money::of(0, 'USD');
        foreach ($this->items as $item) {
            $total = $total->add($item->getPrice());
        }
        return $total;
    }
}
```

#### B. Application (Средний слой)
**Что здесь:**
- **Commands/Queries:** Объекты, содержащие данные для выполнения операции.
- **Services (Use Cases):** Оркестрация. Получают Command, вызывают Domain, сохраняют результат.
- **Interfaces (Ports):** Интерфейсы репозиториев, которые должны быть реализованы инфраструктурой.

**Пример:**
```php
// app/Application/Command/CreateOrderCommand.php
class CreateOrderCommand {
    public function __construct(
        public string $customerEmail,
        public array $items
    ) {}
}

// app/Application/Service/OrderService.php
class OrderService {
    public function __construct(
        private OrderRepositoryInterface $orderRepository, // Порт (интерфейс)
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function handleCreateOrder(CreateOrderCommand $command): int {
        // 1. Создание доменного объекта
        $order = new Order();
        foreach ($command->items as $itemData) {
            $order->addItem(...);
        }

        // 2. Сохранение через интерфейс (не через EntityManager напрямую)
        $orderId = $this->orderRepository->save($order);

        // 3. Публикация событий
        foreach ($order->pullEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $orderId;
    }
}
```

#### C. Infrastructure (Внешний слой)

**1. Http (Контроллеры Symfony)**
**Задача:** Преобразование HTTP-запроса в Command/Query и результата в HTTP-ответ.

```php
// app/Infrastructure/Http/Controller/OrderController.php
class OrderController extends AbstractController {
    public function create(Request $request, OrderService $orderService): JsonResponse {
        // 1. Валидация и маппинг из Request в Command
        $command = new CreateOrderCommand(
            customerEmail: $request->get('email'),
            items: json_decode($request->getContent(), true)['items']
        );

        // 2. Вызов Application Service
        $orderId = $orderService->handleCreateOrder($command);

        // 3. Формирование ответа
        return $this->json(['id' => $orderId], 201);
    }
}
```

**2. Persistence (Doctrine)**
**Задача:** Реализация интерфейсов из Application. Маппинг Domain-объектов в Doctrine Entities.

```php
// app/Infrastructure/Persistence/Repository/DoctrineOrderRepository.php
class DoctrineOrderRepository implements OrderRepositoryInterface {
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Order $order): int {
        // 1. Преобразование Domain Entity в Doctrine Entity
        $doctrineOrder = $this->toDoctrine($order);

        // 2. Сохранение
        $this->em->persist($doctrineOrder);
        $this->em->flush();

        return $doctrineOrder->getId();
    }

    private function toDoctrine(Order $order): DoctrineOrderEntity {
        $doctrineOrder = new DoctrineOrderEntity();
        $doctrineOrder->setTotal($order->getTotal()->jsonSerialize());
        // ... маппинг
        return $doctrineOrder;
    }
}
```

**3. Messaging (Обработчики очередей)**
**Задача:** Получение сообщения из очереди и вызов Application Service.

```php
// app/Infrastructure/Messaging/Handler/SendOrderEmailHandler.php
class SendOrderEmailHandler {
    public function __construct(private OrderService $orderService) {}

    public function __invoke(SendOrderEmailMessage $message): void {
        // Вызов Application Service (не Domain напрямую)
        $this->orderService->sendEmailForOrder($message->getOrderId());
    }
}
```

---

### 4. Куда попадает что?

| Компонент | Слой | Обоснование |
|-----------|------|-------------|
| **Doctrine Entity** | Infrastructure | Это техническая деталь. Domain не должен знать про ORM. |
| **Symfony Controller** | Infrastructure | Это адаптер HTTP-протокола. |
| **Queue Handler** | Infrastructure | Это адаптер очереди. |
| **Repository Interface** | Application (Port) | Это контракт, который Domain/Application определяет. |
| **Repository Implementation** | Infrastructure | Это реализация контракта. |
| **Domain Event** | Domain | Бизнес-событие. |
| **Event Handler** | Infrastructure | Адаптер, который реагирует на событие. |

---

### 5. Как следить, что границы не нарушаются?

#### A. Статический анализ (PHPStan + Arch)
Используйте **PHPStan** с правилом `arch`.

```php
// phpstan.neon
parameters:
    checkModelScope: true
    arch:
        rules:
            -
                class: App\Domain\*
                notContains:
                    - Symfony
                    - Doctrine
                    - Psr\EventDispatcher
```

Это правило **запретит** использование `Symfony`, `Doctrine` и `Psr` в классах из папки `Domain`. Если разработчик случайно импортирует `use Doctrine\ORM\EntityManagerInterface` в `Domain/Entity/Order.php` — PHPStan упадет.

#### B. Изоляция через Interfaces (Ports)
Domain и Application работают **только с интерфейсами**.

```php
// Application/Port/OrderRepositoryInterface.php
interface OrderRepositoryInterface {
    public function save(Order $order): int;
    public function findById(int $id): Order;
}
```

Infrastructure реализует этот интерфейс. Если Infrastructure захочет использовать `EntityManager` — это нормально. Если Domain захочет — PHPStan запретит.

#### C. Code Review
Требуйте, чтобы:
1. **Domain** не импортировал ничего, кроме PHP и своих же классов.
2. **Controllers** не содержали бизнес-логики (только маппинг).
3. **Repositories** не содержали бизнес-логики (только маппинг).

#### D. Отдельные Composer-пакеты (Монорепозиторий)
Самый строгий способ:
- `app/domain` — отдельный пакет.
- `app/application` — отдельный пакет, зависит от `app/domain`.
- `app/infrastructure` — отдельный пакет, зависит от `app/application`.

Если `app/domain` попытается использовать `Symfony` — composer не даст установить зависимость, так как она не объявлена.

---

### Итог

| Слой | Что делает | Зависит от |
|------|-----------|------------|
| **Domain** | Бизнес-логика | Ничего (чистый PHP) |
| **Application** | Оркестрация | Domain |
| **Infrastructure** | Адаптеры (HTTP, БД, очереди) | Application, Domain |

**Главное правило:**
> **Зависимости всегда указывают извне внутрь.**
> Controller → Service → Domain.
> Repository → Domain.
> Queue Handler → Service.

Никогда не должно быть:
> Domain → Controller.
> Domain → Doctrine.

> ⏱ 19.9s · prompt 46899 tok · output 2587 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Стратегия тестирования для Symfony + PostgreSQL строится вокруг **баланса скорости и реалистичности**. Мы используем **Пирамиду Тестов**, но с важной оговоркой для PHP: мы не пишем юнит-тесты для 100% кода, а фокусируемся на **доменной логике** и **критических интеграционных путях**.

### 1. Пирамида Тестов (Адаптированная для Symfony)

| Уровень | Тип | Доля | Скорость | Цель |
|---------|-----|------|----------|------|
| **Основа** | **Unit Tests** (Домен) | 60-70% | 🚀 Мгновенно | Проверка бизнес-логики (Entity, VO, Services). |
| **Середина** | **Integration Tests** | 20-30% | ⚡ Быстро | Проверка взаимодействия слоёв (Controller → Service → Repository) с **реальной БД** или **ин-мемориз**. |
| **Вершина** | **End-to-End (E2E)** | 5-10% | 🐢 Медленно | Проверка полного сценария (HTTP → БД → Ответ). |

**Важно:** В PHP/Doctrine «юнит-тест» часто включает проверку маппинга, поэтому мы разводим:
- **Чистые юниты:** Только доменный код (без БД).
- **Интеграционные:** С БД (PostgreSQL или SQLite).

---

### 2. Что мокаем, а что нет?

#### ✅ МОКАЕМ (Подменяем)

1. **Внешние API:**
   - Платежные шлюзы (Stripe, ЮMoney).
   - Email/SMS провайдеры (SendGrid, Twilio).
   - Уведомления (Push, Telegram).
   *Почему:* Медленно, ненадёжно, имеет побочные эффекты.

2. **Время (Clock):**
   - Все зависимости от `new DateTime()` или `time()`.
   *Почему:* Тесты должны быть детерминированными.

3. **Рандом (Random):**
   - Генерация токенов, ID (если не используется БД).
   *Почему:* Невозможно предсказать результат.

4. **Сложные доменные сервисы (в юнит-тестах Application Service):**
   - Если тестируете `OrderService`, мокайте `InventoryService` и `PaymentService`.
   *Почему:* Мы тестируем оркестрацию, а не логику зависимостей.

#### ❌ НЕ МОКАЕМ (Используем реальные)

1. **Доменный код (Entity, Value Objects):**
   - `Money`, `Order`, `User`.
   *Почему:* Это ядро бизнеса. Если оно сломано — всё сломано. Моки здесь бессмысленны.

2. **База данных (для интеграционных тестов):**
   - Используем **реальную БД** (PostgreSQL) или **SQLite**.
   *Почему:* Моки БД (через `createMock(EntityManagerInterface::class)`) скрывают ошибки маппинга, SQL-запросы, N+1. Мы хотим проверить, что ORM работает.

3. **Валидация Symfony (Form/Constraint):**
   *Почему:* Это часть контракта API.

4. **DI-контейнер:**
   *Почему:* Он обеспечивает связность. Тестируйте систему целиком.

---

### 3. Типы тестов в detail

#### A. Unit Tests (Домен)

**Цель:** Проверить, что бизнес-правила работают.
**Инструмент:** PHPUnit + Pest.
**БД:** Не используется.

```php
// tests/Unit/Domain/MoneyTest.php
test('money addition with different currencies throws exception') {
    $usd = Money::of(10, 'USD');
    $eur = Money::of(10, 'EUR');

    expect(fn() => $usd->add($eur))->toThrow(\InvalidArgumentException::class);
}

test('order total is calculated correctly') {
    $order = new Order();
    $order->addItem(Product::stub(), 2, Money::of(10, 'USD'));
    $order->addItem(Product::stub(), 1, Money::of(5, 'USD'));

    expect($order->getTotal())->toEqual(Money::of(25, 'USD'));
}
```

#### B. Integration Tests (Критические пути)

**Цель:** Проверить, что слои связаны правильно, SQL-запросы работают, транзакции не ломаются.
**Инструмент:** Symfony Panther (или HTTP-клиент) + PostgreSQL (или SQLite).
**БД:** Реальная.

```php
// tests/Integration/OrderControllerTest.php
class OrderControllerTest extends WebTestCase {
    /** @test */
    public function it_creates_order_with_valid_items() {
        $client = static::createClient();

        $client->request('POST', '/api/orders', [
            'json' => [
                'customer_email' => 'test@example.com',
                'items' => [
                    ['product_id' => 1, 'quantity' => 1]
                ]
            ]
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonPath('id', 'is int');

        // Проверка БД
        $em = $client->getContainer()->get('doctrine')->getManager();
        $order = $em->getRepository(Order::class)->findOneBy(['number' => 'TEST']);
        self::assertNotNull($order);
        self::assertSame(1, $order->getTotal()->getAmount());
    }
}
```

**Важно:** Для интеграционных тестов мы **не мокаем** `EntityManager`. Мы используем настоящую БД, но **очищаем** её между тестами.

#### C. End-to-End Tests (Критические сценарии)

**Цель:** Проверить полный пользовательский путь.
**Инструмент:** Panter, Dusk, или просто HTTP-клиент с реальными зависимостями.

**Когда писать:**
- Регистрация → Логин → Создание заказа → Оплата (мок платежки) → Подтверждение.
- Точка отказа: «Что будет, если платежка упадет?»

---

### 4. Как поддерживать тесты быстрыми и стабильными?

#### A. Быстрота

1. **Используйте SQLite для интеграционных тестов (если возможно):**
   - Если ваш код не использует специфичные фичи PostgreSQL (JSONB, CTE, специфичные индексы), используйте `sqlite` в `config/packages/doctrine.yaml` для тестов.
   - SQLite в 10-50 раз быстрее PostgreSQL при запуске.

2. **Параллельный запуск (PHPUnit --parallel):**
   - Запускайте 4-8 параллельных процессов.
   - Требует изолированных окружений (каждый процесс со своей БД).

3. **Минимизируйте загрузку DI-контейнера:**
   - Тестируйте только то, что нужно.
   - Используйте `createClient()` только когда нужны контроллеры.

4. **Кэшируйте зависимости:**
   - Используйте `opcache`.
   - Используйте `XDEBUG` только когда нужно.

#### B. Стабильность

1. **Изоляция тестов (Transaction Rollback):**
   - Каждый тест должен завершаться с откатом транзакции.
   - Symfony делает это автоматически через `BrowserKit` или `WebTestCase` с опцией `resetDatabase: true` (в новых версиях).
   - **Важно:** Не оставляйте данные в БД после теста.

2. **Определенность (Determinism):**
   - **Никакого `rand()`** без сидированного генератора.
   - **Никакого `time()`** без внедрения `Clock` интерфейса.
   - **Никаких внешних API** без моков.

3. **Явные ожидания:**
   - Не проверяйте «статус 200». Проверяйте **содержимое** ответа.
   - Не проверяйте «запись создана». Проверяйте **значения полей**.

4. **Уберите «случайные» задержки:**
   - Если тест зависит от времени (например, «через 1 секунду статус изменится») — **переделайте архитектуру**. Используйте события, а не таймеры.

5. **Логирование при провале:**
   - Настройте `symfony/monolog-bundle` так, чтобы при ошибке теста выводились SQL-запросы и контекст.
   - Используйте `panther` для E2E — он сохраняет скриншоты.

---

### 5. Чек-лист для новых тестов

| Вопрос | Ответ |
|--------|-------|
| Это тест доменной логики? | → **Unit Test** без БД. |
| Это тест взаимодействия с БД? | → **Integration Test** с реальной БД (SQLite/Postgres). |
| Это тест полного сценария? | → **E2E Test** (только для критичных путей). |
| Используется ли `rand()` или `time()`? | → **Замените** на внедренные зависимости. |
| Есть ли внешние API? | → **Замокайте**. |
| Тест проходит стабильно? | → **Проверьте изоляцию** (откат транзакции). |

### Итог

**Стратегия:**
1. **Домен** — чистые юнит-тесты (быстро, дешево).
2. **Интеграция** — тесты с реальной БД (средняя скорость, высокая ценность).
3. **E2E** — только для критичных сценариев (медленно, дорого).

**Главное правило:**
> **Если тест медленный — он не будет запущен.**
> **Если тест нестабильный — ему не будут доверять.**

Инвестируйте в **скорость** и **детерминированность** тестов больше, чем в их количество.

> ⏱ 19.3s · prompt 49546 tok · output 2357 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Ситуация, когда два процесса одновременно изменяют одну запись, называется **конкурентностью (Concurrency)**. В реляционных БД это приводит к **гонкам условий (Race Conditions)** и **потере данных**.

### 1. Какие проблемы возникают?

#### А. Lost Update (Потеря обновления)
Самая частая проблема.
1.  Процесс A читает `balance = 100`.
2.  Процесс B читает `balance = 100`.
3.  Процесс A вычисляет `100 + 50` и пишет `balance = 150`.
4.  Процесс B вычисляет `100 - 20` и пишет `balance = 80`.
5.  **Результат:** Ожидалось `130` (100+50-20), получено `80`. Обновление A потеряно.

#### Б. Dirty Read (Грязное чтение)
Процесс A читает данные, которые процесс B изменил, но еще **не закоммитил**.
- **Риск:** Если B откатится, A увидит несуществующие данные.
- PostgreSQL по умолчанию изолирует на уровне `READ COMMITTED`, поэтому dirty reads редки, но возможны на уровне `READ UNCOMMITTED` (не рекомендуется).

#### В. Non-repeatable Read (Несовместимое чтение)
Процесс A читает строку два раза. Между чтениями процесс B изменил эту строку и закоммитил.
- **Риск:** Логика A ломается, потому что данные изменились «под ногами».
- **Пример:** A проверяет `balance >= 100`, затем B списывает 100, затем A списывает 50. Итог: `-50`.

#### Г. Phantom Read (Фантомное чтение)
Между двумя запросами A появляются новые строки, удовлетворяющие условию.
- **Риск:** Итог `COUNT(*)` или `SUM()` меняется в процессе транзакции.

---

### 2. Способы защиты

#### Способ 1: Optimistic Locking (Оптимистичная блокировка)

**Принцип:** «Надеюсь, что конфликт не будет. Проверю в конце».

**Механизм:**
1.  В таблице есть колонка `version` (integer).
2.  При чтении: `SELECT * FROM accounts WHERE id = 1` → `version = 1`.
3.  При записи: `UPDATE accounts SET balance = 150, version = 2 WHERE id = 1 AND version = 1`.
4.  Если кто-то другой успел обновить `version` до 2 — `UPDATE` затронет **0 строк**.
5.  Приложение видит, что `rowCount = 0`, и понимает: «Конфликт! Повторить попытку».

**Реализация в Doctrine:**
```php
#[ORM\Entity]
class Account {
    #[ORM\Id]
    private int $id;

    #[ORM\Column]
    private float $balance;

    #[ORM\Version] // Магия Doctrine
    private int $version;
}
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ Нет блокировок на чтение. | ❌ Ошибка обнаруживается **только в конце** транзакции. |
| ✅ Высокая пропускная способность. | ❌ Требует **ретраев** (повторных попыток). |
| ✅ Простая реализация. | ❌ Если конфликты частые — производительность падает из-за ретраев. |
| ✅ Идемпотентность ретраев. | ❌ Бизнес-логика должна быть **повторяемой** (идемпотентной). |

**Когда использовать:**
- Конфликты **редки** (< 1%).
- Транзакции **короткие** (< 50 мс).
- Вы можете показать пользователю «Повторите попытку».

---

#### Способ 2: Pessimistic Locking (Пессимистичная блокировка)

**Принцип:** «Буду готов, что конфликт будет. Заблокирую сразу».

**Механизм:**
1.  `SELECT * FROM accounts WHERE id = 1 FOR UPDATE`.
2.  БД ставит **эксклюзивную блокировку** на строку.
3.  Второй процесс, пытающийся сделать `SELECT ... FOR UPDATE`, **ждет**.
4.  Первый процесс коммитит → блокировка снимается → второй процесс продолжает.

**Реализация в Doctrine:**
```php
$em = $this->entityManager;
$account = $em->find(Account::class, 1, null, LockMode::PESSIMISTIC_WRITE);

$account->setBalance(150);
$em->flush();
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ Конфликты **невозможны**. | ❌ **Узкое место (Bottleneck):** Очередь процессов. |
| ✅ Простая логика (нет ретраев). | ❌ **Deadlock:** Если блокировки берутся в разном порядке. |
| ✅ Гарантированное состояние. | ❌ **Таймауты:** Если процесс завис — остальные ждут. |
| ✅ Подходит для длительных операций. | ❌ Низкая пропускная способность при высокой конкуренции. |

**Когда использовать:**
- Конфликты **частые** (> 10%).
- Транзакции **длительные** (содержат внешние вызовы).
- Критичность данных (банковские операции, инвентарь).

---

#### Способ 3: Atomic Update (Атомарное обновление)

**Принцип:** «Не читай, а сразу изменяй».

**Механизм:**
Вместо `SELECT → Calculate → UPDATE` делаем **один SQL-запрос**:

```sql
UPDATE accounts 
SET balance = balance + 50 
WHERE id = 1 AND balance >= 50;
```

**Trade-offs:**
| Плюсы | Минусы |
|-------|--------|
| ✅ **Полная атомарность** на уровне БД. | ❌ **Сложность:** Невозможно сложную логику в SQL. |
| ✅ Нет блокировок (кроме самой строки). | ❌ **Ограниченная логика:** Только простые вычисления. |
| ✅ Максимальная производительность. | ❌ **Трудно отлаживать:** Логика скрыта в SQL. |
| ✅ Нет ретраев. | ❌ **Нарушение инкапсуляции:** Бизнес-логика ушла в БД. |

**Когда использовать:**
- Простые операции (инкремент, декремент, сумма).
- Высокая конкуренция.
- Логика укладывается в SQL (например, `SET stock = stock - 1 WHERE stock > 0`).

**Реализация:**
```php
$result = $em->createQuery(
    'UPDATE App\Entity\Account a SET a.balance = a.balance + :amount WHERE a.id = :id'
)
->setParameter('amount', 50)
->setParameter('id', 1)
->execute();

if ($result === 0) {
    throw new \Exception('Insufficient balance or account not found');
}
```

---

#### Способ 4: Isolation Levels (Уровни изоляции)

PostgreSQL поддерживает:
- `READ COMMITTED` (по умолчанию) — защищает от dirty reads.
- `REPEATABLE READ` — защищает от non-repeatable reads.
- `SERIALIZABLE` — защищает от всех аномалий, но **очень медленно** (конфликты транзакций).

**Рекомендация:**
- Для большинства случаев: `READ COMMITTED` + **Optimistic/Pessimistic Locking**.
- Для критичных банковских операций: `SERIALIZABLE` (но будьте готовы к `SerializationFailure` и ретраев).

---

### 3. Сравнительная таблица

| Критерий | Optimistic | Pessimistic | Atomic Update |
|----------|------------|-------------|---------------|
| **Производительность (чтение)** | 🚀 Высокая | 🐢 Низкая (блокировки) | 🚀 Высокая |
| **Производительность (письмо)** | 🚀 Высокая (если конфликтов мало) | 🐢 Низкая (очередь) | 🚀 Очень высокая |
| **Сложность реализации** | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Надежность** | ⭐⭐⭐ (зависит от ретраев) | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Гибкость логики** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Когда использовать** | Редкие конфликты | Частые конфликты, длительные операции | Простые инкременты, высокая нагрузка |

---

### 4. Алгоритм выбора

1.  **Логика простая?** (инкремент, декремент, сумма)
    - ✅ **Да** → **Atomic Update**.
    - ❌ **Нет** → Шаг 2.

2.  **Конфликты редкие?** (< 1% запросов)
    - ✅ **Да** → **Optimistic Locking** + Retries.
    - ❌ **Нет** → Шаг 3.

3.  **Транзакция длительная?** (> 100 мс, внешние вызовы)
    - ✅ **Да** → **Pessimistic Locking** (но сократите транзакцию!).
    - ❌ **Нет** → **Optimistic Locking** (даже при частых конфликтах, т.к. транзакции короткие).

4.  **Критическая целостность?** (банки, инвентарь)
    - ✅ **Да** → **Pessimistic Locking** или **SERIALIZABLE** + Retries.
    - ❌ **Нет** → **Optimistic Locking**.

---

### 5. Антипаттерны

#### ❌ 1. Отсутствие блокировок
```php
// ПЛОХО
$balance = $account->getBalance();
usleep(10000); // Имитация задержки
$account->setBalance($balance + 100);
$em->flush();
```
**Результат:** Lost Update.

#### ❌ 2. Длительная блокировка
```php
// ПЛОХО
$em->lock($account, LockMode::PESSIMISTIC_WRITE);
$this->callExternalApi(); // 2 секунды!
$account->setBalance(...);
$em->flush();
```
**Результат:** Очередь процессов, таймауты.
**Решение:** Внешние вызовы **после** commit.

#### ❌ 3. Ретраи без ограничения
```php
// ПЛОХО
while (true) {
    try {
        // операция
    } catch (OptimisticLockException $e) {
        // retry
    }
}
```
**Результат:** Бесконечный цикл при частых конфликтах.
**Решение:** Ограничьте количество ретраев (3-5) и используйте **backoff**.

#### ❌ 4. Сложная логика в Atomic Update
```sql
-- ПЛОХО
UPDATE orders SET total = (
    SELECT SUM(items.price * items.qty) 
    FROM items 
    JOIN products ON items.product_id = products.id
    WHERE items.order_id = orders.id
)
```
**Результат:** Нечитаемо, трудно поддерживать, может быть медленно.
**Решение:** Держите логику в коде, используйте Optimistic/Pessimistic.

---

### 6. Итоговая рекомендация

Для 90% случаев в Symfony/Doctrine:

1.  **Добавьте `#[ORM\Version]`** на сущности, которые могут меняться конкурентно.
2.  **Используйте Optimistic Locking** по умолчанию.
3.  **Реализуйте Retry Logic** в Application Service (3-5 попыток с backoff).
4.  **Делайте транзакции короткими** (менее 50 мс).
5.  **Для простых инкрементов** (счетчики, инвентарь) используйте **Atomic Update**.
6.  **Для критичных операций** с внешней интеграцией используйте **Pessimistic Locking**, но **минимизируйте время блокировки**.

**Главное правило:**
> **Конкуренция — это норма.**
> Ваша система должна быть спроектирована так, чтобы **корректно обрабатывать** одновременные изменения, а не предотвращать их любой ценой.

> ⏱ 20.8s · prompt 51957 tok · output 2978 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой git-процесс — это **Git Flow + Trunk-Based Development** с элементами **Feature Flags**. Он оптимизирован под баланс **стабильности продакшена** и **скорости доставки**.

### 1. Структура веток

| Ветка | Назначение | Живёт |
|-------|-----------|-------|
| `main` (или `master`) | **Только продакшен-код.** Всегда деплоится. | Постоянно |
| `develop` | **Интеграционная ветка.** Сборка фич для релиза. | Постоянно |
| `feature/*` | Отдельная функциональность. | До слияния в `develop` |
| `hotfix/*` | Критический багфикс для продакшена. | До слияния в `main` |
| `release/*` | Подготовка релиза (тестирование, фиксы). | До слияния в `main` |

**Почему так?**
- `main` — «золотая» ветка. Если она сломана — это авария.
- `develop` — «песочница». Здесь можно быть дерзкими.
- `feature` — изоляция. Один разработчик не мешает другому.
- `hotfix` — быстрый канал для аварий.

---

### 2. Формат коммитов

Я использую **Conventional Commits**:

```
<type>(<scope>): <subject>

<body>

<footer>
```

#### Типы (type):
- `feat`: Новая функциональность.
- `fix`: Исправление бага.
- `docs`: Изменения в документации.
- `style`: Форматирование, точка с запятой, пробелы (без изменения логики).
- `refactor`: Рефакторинг кода (не feat, не fix).
- `perf`: Оптимизация производительности.
- `test`: Добавление/изменение тестов.
- `chore`: Обновление конфигов, зависимостей.

#### Примеры:
```bash
# Хорошо
feat(order): add discount calculation
fix(auth): resolve session timeout on mobile
refactor(user): extract email validation to service
chore(deps): update doctrine from 2.14 to 2.15

# Плохо
fix bug
update
changed stuff
```

**Почему так?**
1. **Автогенерация Changelog:** Инструменты (commitizen, standard-version) сами пишут `CHANGELOG.md`.
2. **Semantic Versioning:** `feat` → minor, `fix` → patch.
3. **Поиск:** `git log --grep="feat(order)"` — мгновенно находишь все фичи по заказам.
4. **Понятность:** Ревьюер сразу видит, **что** изменено, а не **как**.

---

### 3. Pull Request (PR)

#### Структура PR:
```markdown
## 📋 Описание
Краткое описание, **зачем** нужен этот PR. Ссылка на задачу (JIRA/Trello).

## 🔍 Что изменено
- [ ] Добавлено валидацию email в форме регистрации.
- [ ] Создан сервис EmailValidator.
- [ ] Добавлены тесты.

## 🧪 Как тестировать
1. Зарегистрируйтесь на `/register`.
2. Введите невалидный email.
3. Убедитесь, что ошибка выводится.

## 📎 Скриншоты/Гифки
(Если есть UI)

## ✅ Checklist
- [ ] Код покрыт тестами.
- [ ] Документация обновлена.
- [ ] Миграции добавлены.
- [ ] Нет утечки секретов.
```

#### Правила PR:
1. **Один PR = одна логическая единица.** Не «всё сразу».
2. **Размер:** Идеально < 400 строк. Если больше — разбейте.
3. **Ветвление:** `feature/X` → `develop`.
4. **Слияние:** Только после ревью и зелёных CI.

**Почему так?**
- **Маленькие PR** ревьюются быстрее и с меньшим количеством ошибок.
- **Чеклист** предотвращает «забыл миграцию» или «забыл тесты».
- **Описание** помогает ревьюеру понять контекст, не читая код.

---

### 4. Ревью

#### Правила для ревьюера:
1. **Смотрите на архитектуру,** а не на стиль (стиль — за линтера).
2. **Ищите баги,** а не «красивее».
3. **Предлагайте,** а не требуйте. («Может, стоит вынести в метод?» vs «Перепиши!»).
4. **Отвечайте в течение 24 часов** (SLA).
5. **Не блокируйте** на спорных вкусовых моментах (используйте `suggestion`).

#### Правила для автора:
1. **Само-ревью** перед отправкой.
2. **Отвечайте на каждый комментарий.**
3. **Push-айте изменения** в ту же ветку (не создавайте новые коммиты).
4. **Резолвинг комментариев:** После правки — «Resolved».

**Почему так?**
- **Скорость:** SLA 24 часа предотвращает «зависание» PR.
- **Качество:** Архитектурные проблемы дешевле исправить сейчас, чем в продакшене.
- **Уважение:** Предложения, а не требования, снижают трение в команде.

---

### 5. CI/CD

#### Pipeline (GitHub Actions / GitLab CI):
```yaml
stages:
  - lint      # PHPStan, Rector, Pint
  - test      # PHPUnit (Unit + Integration)
  - build     # Composer install, Docker build
  - deploy    # Только для main/develop
```

#### Пайплайн для `develop`:
1. Линт.
2. Тесты.
3. Билд образа Docker.
4. Деплой на **Staging**.

#### Пайплайн для `main`:
1. Линт.
2. Тесты.
3. Билд образа Docker.
4. Деплой на **Production**.
5. Смерж `main` в `develop` (автоматически).

**Почему так?**
- **Staging** — «предпродакшен». Если тут не работает — не работает в проде.
- **Автоматический смерж** `main` → `develop` предотвращает «расхождение» веток.
- **CI блокирует** слияние: если тесты красные — PR не смержится.

---

### 6. Релизы

#### Стратегия:
- **SemVer:** `MAJOR.MINOR.PATCH`
  - `MAJOR`: Сломанные изменения (API, БД).
  - `MINOR`: Новые фичи.
  - `PATCH`: Багфиксы.

#### Процесс релиза:
1. **Подготовка:** Все фичи смержены в `develop`.
2. **Создание `release/X.Y.Z`:** От `develop`.
3. **Тестирование:** На Staging.
4. **Фиксы:** Только критичные баги.
5. **Смерж:** `release/X.Y.Z` → `main`.
6. **Тег:** `vX.Y.Z` на `main`.
7. **Смерж:** `main` → `develop`.
8. **Удаление:** `release/X.Y.Z`.

#### Hotfix:
1. **Создание `hotfix/X.Y.Z+1`:** От `main`.
2. **Фикс.**
3. **Смерж:** `hotfix` → `main`.
4. **Тег:** `vX.Y.Z+1`.
5. **Смерж:** `main` → `develop`.
6. **Удаление:** `hotfix`.

**Почему так?**
- **`release` ветка** изолирует подготовку релиза от новых фич.
- **Hotfix** не зависит от `develop` — можно чинить баг, даже если в `develop` много незавершённых фич.
- **SemVer** позволяет клиентам знать, что изменилось.

---

### 7. Почему не Trunk-Based Development (TBD)?

TBD (все мерджат в `main` сразу) — это **лучше**, если:
- Команда **маленькая** (1-3 человека).
- Есть **feature flags**.
- **CI очень быстрый** (< 2 мин).
- **Тесты покрывают** 100% критичных путей.

**Но для большинства PHP-команд TBD опасен:**
- Если в `main` сломали — продакшен сломан.
- Нет «буфера» для отладки.
- Трудно откатиться.

**Мой выбор:** Git Flow с `develop` — это **компромисс** между скоростью TBD и безопасностью Git Flow.

---

### 8. Инструменты

| Задача | Инструмент |
|--------|-----------|
| Формат коммитов | `commitlint`, `husky`, `commitizen` |
| Линт | `php-cs-fixer`, `rector`, `phpstan` |
| Тесты | `phpunit`, `pest` |
| CI/CD | `github-actions`, `gitlab-ci` |
| Релизы | `semantic-release`, `standard-version` |
| Безопасность | `composer audit`, `sonarqube` |

---

### 9. Итог

| Этап | Действие | Почему |
|------|----------|--------|
| **Задача** | Создаём `feature/X` от `develop` | Изоляция, параллельная разработка |
| **Код** | Пишем код, линтим, тестируем | Качество до ревью |
| **PR** | Открываем PR в `develop` | Обзор, обсуждение |
| **Ревью** | 1-2 ревьюера, SLA 24ч | Качество, скорость |
| **CI** | Линт + тесты + билд | Автоматическая проверка |
| **Смерж** | Squash merge в `develop` | Чистая история |
| **Релиз** | `release/X.Y.Z` → `main` → тег | Стабильный продакшен |
| **Hotfix** | `hotfix/X.Y.Z+1` → `main` | Быстрое исправление |

**Главный принцип:**
> **`main` — это продакшен. Никогда не нарушайте это правило.**
> Всё, что идёт в `main`, должно быть **готово, протестировано и стабильно**.

> ⏱ 22.3s · prompt 54995 tok · output 2504 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью — это не поиск ошибок ради ошибок, а **передача контекста** и **обеспечение качества** через совместное принятие решений. Это диалог двух инженеров, а не суд.

### 1. Маркировка замечаний (Severity Levels)

Чтобы не перегружать автора и не упускать критичное, я делю комментарии на 4 уровня. В GitHub/GitLab я использую эмодзи или префиксы:

| Уровень | Маркер | Что это значит | Пример |
|---------|--------|---------------|--------|
| **Critical** | 🚨 **BLOCKER** | Код **не может** быть смержен. Баг, безопасность, нарушение архитектуры. | `SQL Injection`, `XSS`, нарушена инкапсуляция агрегата. |
| **Major** | ⚠️ **SHOULD** | Код работает, но будет трудно поддерживать, тестировать или масштабировать. | Нарушение SRP, отсутствие тестов на критичный путь, магические числа. |
| **Minor** | 💡 **COULD** | Улучшение стиля, читаемости, минорная оптимизация. | Плохое название переменной, можно использовать `match` вместо `switch`. |
| **Question** | ❓ **ASAP** | Вопрос, а не претензия. «Я не понимаю, зачем это сделано так. Пояснишь?» | «Почему тут `catch (\Exception)`? Мы должны ловить только `SpecificException`?» |

**Правило:** Если я ставлю 🚨 **BLOCKER**, я **обязан** объяснить **почему** и **как** исправить. Просто «переписать» — неинформативно.

---

### 2. На что смотрю в первую очередь (Приоритеты)

#### 1. Архитектура и Дизайн (Максимальный приоритет)
- **Направление зависимостей:** Зависит ли домен от инфраструктуры? (Нельзя).
- **SRP:** Один класс делает много разных вещей?
- **Инкапсуляция:** Есть ли прямой доступ к внутренним полям?
- **Сложность:** Циклометрическая сложность методов. Если > 10 — нужен рефакторинг.

#### 2. Корректность и Безопасность
- **Edge cases:** Что будет при `null`, пустом массиве, отрицательных числах?
- **Конкурентность:** Есть ли race conditions?
- **Безопасность:** SQL/Command injection, утечка токенов, валидация на входе.
- **Транзакции:** Где открывается, где закрывается? Что будет при откате?

#### 3. Тестируемость и Тесты
- **Можно ли юнит-тестировать?** Если нужно мокавать 5 зависимостей — код сложный.
- **Покритичны ли пути?** Если добавлена бизнес-логика — есть ли тесты?
- **Качество тестов:** Тесты проверяют **поведение**, а не реализацию.

#### 4. Читаемость
- **Имена:** `data`, `info`, `tmp` — плохо. `orderStatus`, `userEmail` — хорошо.
- **Глубина вложенности:** Если `if` внутри `if` внутри `foreach` внутри `try` — вынесите в методы.
- **Комментарии:** Обоснование **почему**, а не **что**.

#### 5. Стиль (Минимальный приоритет)
- **Не трогаю**, если есть линтер (`php-cs-fixer`, `rector`).
- Если линтера нет — смотрю, но не блокирую на пробелах.

---

### 3. Как я формулирую замечания

#### ❌ Плохо:
> «Здесь должно быть иначе. Переписать.»
> «Это плохо.»
> «Ты забыл про null?»

#### ✅ Хорошо:
> **🚨 BLOCKER:** `SQL Injection`. 
> Строка 42: `$query = "SELECT * FROM users WHERE id = " . $id;`
> **Почему:** `$id` приходит из внешнего источника и не санитизируется.
> **Как исправить:** Используйте prepared statements: `$query = "SELECT * FROM users WHERE id = ?";`

> **⚠️ SHOULD:** Нарушение SRP.
> Метод `processOrder()` делает 3 вещи: валидация, расчет, отправка.
> **Почему:** При изменении правил расчета вам придется трогать код отправки.
> **Как исправить:** Вынесите `calculateTotal()` в `Calculator`, `sendEmail()` в `EmailService`.

> **❓ QUESTION:** 
> Я не понимаю, зачем здесь `catch (\Exception)`. Мы ожидаем только `PaymentDeclinedException`. 
> Если произойдет другая ошибка (например, `DBException`), мы ее проглотим. 
> **Предлагаю:** Ловить только конкретные исключения, а остальные пробрасывать наверх.

**Формула:**
1. **Что** (уровень).
2. **Где** (строка).
3. **Почему** (последствия).
4. **Как** (предложение решения).

---

### 4. Разногласие с автором

Это самая сложная часть. Разногласие — это **норма**. Это значит, что оба думают.

#### Принцип: «Disagree and Commit» (или «Disagree and Discuss»)

#### Шаг 1: Убедитесь, что вы слышите друг друга
- **Автор:** «Я сделал так, потому что нам нужна скорость, и эта задача одноразовая».
- **Ревьюер:** «Понял. Но если это одноразовая, то, может, лучше проще? А если нет — давай оставим».

#### Шаг 2: Аргументируйте данными, а не мнением
- ❌ «Мне кажется, так лучше».
- ✅ «Если мы оставим это здесь, то при добавлении второго типа скидок нам придется переписывать этот блок. Вот пример из Issue #123, где мы уже сталкивались».

#### Шаг 3: Ищите «Третий путь»
Часто оба варианта плохи. Есть ли вариант C?
- Автор хочет сложный код для flexibility.
- Ревьюер хочет простой код для стабильности.
- **Третий путь:** Простой код + interface для будущего расширения.

#### Шаг 4: Эскалация (если не договорились)
Если аргументы исчерпаны:
1. **Третье мнение:** Пригласите тимлида или другого сеньора.
2. **Правило команды:** Есть ли у вас документированные guidelines? Если нет — создайте.
3. **Решение:** Тот, кто **владеет кодом** (автор), в конечном итоге решает, если нет критичных блокирующих аргументов. Но решение должно быть **задокументировано** в PR.

#### ❌ Антипаттерны в конфликте:
- «А ты всегда так делаешь».
- «Ты не понимаешь контекста».
- Игнорирование комментариев.
- «Я закрою PR и перепишу по-своему».

#### ✅ Беседо-паттерны:
- «Я вижу, что мы думаем по-разному. Давай обсудим голосом/видео, чтобы быстрее согласоваться».
- «Мне важна именно эта деталь, потому что [причина]. Можешь объяснить, почему твой вариант безопаснее?»
- «Окей, если ты видишь риски, я готов переработать, но давай зафиксируем, что это будет сделано до смержа».

---

### 5. Чего я никогда не делаю в ревью

1. **Не читаю код по диагонали.** Если PR большой (> 500 строк) — прошу разбить или объясняю, почему ревью займет время.
2. **Не молчу, если вижу проблему.** «Молчание — знак согласия» — это миф. Если вы увидели баг и промолчали — вы соавтор этого бага.
3. **Не блокирую на вкусовых моментах.** Если код работает и читается — пусть будет так.
4. **Не жду идеального кода.** Код должен быть **лучше, чем был**. Не обязательно «идеальный».

---

### 6. Итоговый чеклист для себя как ревьюера

- [ ] Я понял, **зачем** этот PR?
- [ ] Я проверил **критичные пути** (баги, безопасность, производительность)?
- [ ] Я оставил хотя бы один **комплимент** или признание хорошего решения? (Важно для мотивации).
- [ ] Я предложил **решение**, а не только **проблему**?
- [ ] Я ответил на все **вопросы** автора?
- [ ] Я **уважаю** автора и его труд?

**Главное правило:**
> **Цель ревью — не показать, что вы умнее, а сделать код лучше.**
> Если после ревью автор становится лучше — вы молодец.

> ⏱ 21.1s · prompt 57545 tok · output 2101 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Проблема «Пользователи не могут найти заказы» — это **симптом**, а не задача. Если отдать её как есть, разработчик может сделать кнопку «Поддержка», что не решит проблему.

### Как я формулирую задачу (User Story + Acceptance Criteria)

**Заголовок:** Реализация фильтрации и поиска исторических заказов в личном кабинете.

**Описание:**
Как авторизованный пользователь, я хочу фильтровать и находить свои прошлые заказы по дате, статусу и номеру, чтобы быстро получить информацию о покупке и статусе доставки, не обращаясь в поддержку.

**Acceptance Criteria (Критерии приемки):**

1. **UI/UX:**
   - На странице «Мои заказы» появляется панель фильтров.
   - Доступные фильтры:
     - **Статус:** Все, Активные (ожидает, в пути), Завершённые (доставлен, отменён).
     - **Период:** За всё время, За последний месяц, За последний год, Свой диапазон дат.
     - **Поиск по номеру:** Поле ввода с placeholder «Например: ORD-12345».
   - Результаты обновляются **без перезагрузки страницы** (AJAX/Fetch).
   - Если заказов нет — показывается понятное сообщение: «Заказы не найдены. Попробуйте изменить фильтры».

2. **Логика:**
   - Фильтры работают **совместно** (AND).
   - **Пагинация:** 10 заказов на страницу.
   - **Сортировка:** По умолчанию — по дате создания (новые сверху).
   - **Поиск по номеру:** Частичное совпадение (например, ввели «12345», найдёт «ORD-123456»).
   - **Разрешение:** Только свои заказы (проверка `user_id`).

3. **Производительность:**
   - Загрузка результатов < 300 мс (на средней нагрузке).
   - Нет N+1 запросов к БД.

4. **Тестирование:**
   - Юнит-тесты на сервис поиска.
   - Интеграционный тест: создание заказов, фильтрация, проверка результата.
   - E2E-тест: сценарий «Войти → Открыть заказы → Фильтровать → Найти заказ».

5. **Метрики (Как поймём, что сработало):**
   - Количество обращений в поддержку с пометкой «не могу найти заказ» снизится на 50% в течение 2 недель.
   - Среднее время поиска заказа (время от открытия страницы до клика по заказу) — < 10 секунд.

---

### Что обязательно включил и почему?

| Элемент | Почему это важно |
|---------|-----------------|
| **Актер (Кто)** | «Авторизованный пользователь». Не гость, не админ. Границы доступа ясны. |
| **Цель (Зачем)** | «Чтобы не обращаться в поддержку». Это бизнес-ценность. Разработчик понимает, что «кнопка поддержки» — это **не** решение. |
| **Конкретные фильтры** | «Статус, период, номер». Без этого разработчик придумает свой UI, который не решит проблему (например, сделает только поиск по дате). |
| **Комбинирование фильтров** | «Работают совместно (AND)». Без этого пользователь будет искать «Завершённые заказы за 2023 год» и получать мусор. |
| **Пагинация** | «10 заказов на страницу». Без этого при 1000 заказов страница зависнет. |
| **Пустое состояние** | «Показывается сообщение». Без этого пользователь видит белый экран и думает, что «у меня нет заказов». |
| **Производительность** | «< 300 мс, нет N+1». Без этого задача выполнит «формально», но будет тормозить. |
| **Метрики успеха** | «Снижение обращений в поддержку на 50%». Без этого мы не поймём, **работает** ли решение. |

---

### Почему эта формулировка лучше?

1. **Не требует уточнений.** Разработчик знает **что** делать (UI + API), **как** тестировать (критерии) и **как**衡量 результат (метрики).
2. **Узкое фокусирование.** Мы не делаем «улучшение поиска заказов» (абстрактно), а делаем **конкретный функционал** с конкретными фильтрами.
3. **Измеримость.** Мы можем сказать: «Задача выполнена» или «Задача не выполнена» на основе метрик.
4. **Защита от scope creep.** Разработчик не будет «по пути» переделывать личный кабинет, потому что это не входит в AC.

---

### Что я **не** включил (и почему)?

- **Технологии:** Не указываю «используй ElasticSearch» или «напиши на Laravel». Это решение архитектора/разработчика. Если бизнес требует — это уже в AC.
- **Дизайн:** Не пишу «сделай красивую кнопку». Дизайн — за UX-дизайнером. В AC — только функциональные требования.
- **Внутреннюю архитектуру:** Не указываю «используй репозиторий» или «сделай через CQRS». Это решение команды.

---

### Итог

**Задача для исполнителя:**
> «Сделай фильтр заказов в личном кабинете с поиском по номеру, статусу и дате. Результаты — через AJAX, пагинация 10 шт. Напиши тесты. Убедись, что запрос быстрой (< 300 мс). После релиза следим за метриками поддержки».

Эта формулировка **исполнима без уточнений**, потому что она отвечает на вопросы: **Кто? Что? Где? Как проверить? Как измерить успех?**

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Этот этап критически важен. Если я начну писать код, не задав эти вопросы, я потрачу время на решение **неправильной** проблемы.

Вот список вопросов, сгруппированных по приоритету.

### 1. Диагностика (Почему они не находят?)

Перед тем как строить новый функционал, нужно понять, почему старый сломан.

*   **Q1: Что именно они пытаются найти?**
    *   По номеру заказа?
    *   По названию товара?
    *   По дате?
    *   По продавцу?
    *   *Почему это важно:* Если 80% жалоб — это «я потерял номер заказа, купил шапку 3 месяца назад», то поиск по товару важнее, чем фильтр по статусу.
*   **Q2: Есть ли аналитика по обращениям в поддержку?**
    *   Можете выгрузить топ-10 запросов с пометкой «не находит заказ»?
    *   *Почему это важно:* Чтобы не гадать, а опираться на данные.
*   **Q3: Как выглядит текущий поиск/фильтр?**
    *   Он вообще есть? Или там только «Все заказы» списком?
    *   Он работает, но медленно?
    *   Он работает, но результаты неверные?
    *   *Почему это важно:* Может быть, проблема не в отсутствии функции, а в баге существующей.

### 2. Scope (Границы задачи)

*   **Q4: Какие статусы заказов считаются «важными» для поиска?**
    *   Только активные (ожидает, в пути)?
    *   Или и завершённые (доставлен, отменён)?
    *   *Почему это важно:* Фильтрация по статусу — это отдельная бизнес-логика.
*   **Q5: Нужен ли поиск по содержимому (товары)?**
    *   Например: «Покажи заказы, где была куртка Nike».
    *   *Почему это важно:* Это **фундаментально** меняет архитектуру. Поиск по товарам требует индекса по детализации заказа, а не просто по полям `orders`.
*   **Q6: Какой максимальный период времени искать?**
    *   За всё время?
    *   Только за последние 12 месяцев?
    *   *Почему это важно:* Если 5 лет истории — нужна оптимизация/архивация. Если год — можно проще.

### 3. UX/UI (Поведение)

*   **Q7: Мобильная версия?**
    *   Как это будет выглядеть на телефоне? (Сворачиваемая панель? Отдельный экран?)
    *   *Почему это важно:* В PHP-бэкенде это влияет на DTO и размер ответа API.
*   **Q8: Как вести себя при пустом результате?**
    *   Просто «Ничего не найдено»?
    *   Или предложить «Сбросить фильтры» / «Написать в поддержку»?
    *   *Почему это важно:* Это часть UX, который снижает нагрузку на поддержку.

### 4. Техническое окружение

*   **Q9: Сколько данных?**
    *   Сколько всего заказов в базе? (10k? 1M? 100M?)
    *   Сколько заказов у среднего пользователя?
    *   *Почему это важно:* Для 10 заказов в базе достаточно `WHERE user_id = X ORDER BY created_at DESC`. Для 1M — нужны индексы, возможно, партиционирование.
*   **Q10: Есть ли ограничения по производительности?**
    *   Какой SLA на отклик API? (100мс? 1с?)
    *   *Почему это важно:* Определяет бюджет на оптимизацию.

### 5. Метрики успеха

*   **Q11: Как мы поймём, что задача выполнена?**
    *   Снижение обращений в поддержку на X%?
    *   Увеличение конверсии в повторную покупку?
    *   *Почему это важно:* Чтобы связать код с бизнес-результатом.

---

### Пример диалога (Скрипт)

> **Я:** «Прежде чем мы начнем проектировать фильтр, мне нужно понять контекст. Если вы выгрузите 20 последних обращений из поддержки с темой "не находит заказ", я смогу точнее подобрать фильтры. Часто пользователи не помнят номер, но помнят товар. Нужен ли нам поиск по названию товара, или достаточно номера заказа и даты?»
>
> **Заказчик:** «Ой, мы не думали про товары. Но да, многие пишут "купила платье 20 мая". Может, добавим поиск по названию?»
>
> **Я:** «Поиск по названию — это более сложная задача (нужен индекс по детализации). Давайте разделим:
> 1. **MVP (быстро):** Фильтр по дате и статусу + поиск по номеру заказа.
> 2. **Фаза 2:** Умный поиск по товарам.
>
> Согласны начать с MVP, чтобы снять нагрузку на поддержку уже на этой неделе?»

---

### Итог: Что я получу в итоге

После этих вопросов у меня будет не абстрактная «задача про поиск», а конкретный план:

1.  **Матрица запросов:** По каким полям ищут чаще всего.
2.  **Scope:** Какие поля фильтра (дата, статус, номер, товар).
3.  **Ограничения:** Объем данных, сроки, SLA.
4.  **Приоритет:** Что делаем сейчас (MVP), что потом (умный поиск).

**Без этих вопросов я бы начал писать `SELECT * FROM orders WHERE user_id = X`, а клиент бы ждал поиска по товару.**

> ⏱ 12.6s · prompt 59727 tok · output 1378 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это две классические ситуации, которые проверяют не только технические навыки, но и **коммуникационную зрелость**. Вот как я действую в каждом случае.

---

### Ситуация А: Нашёл попутный баг (не связанный с задачей)

**Принцип: «Не чиняй то, что не сломано (в контексте этой задачи)». (Don't fix what isn't broken)**

#### 1. Что я делаю
1. **Останавливаюсь.** Не начинаю писать фикс.
2. **Оцениваю баг:**
   - **Критичность:** Сломан ли продакшен? Потеря данных? Безопасность?
   - **Связь:** Используется ли этот код в моей задаче?
   - **Сложность:** 5 минут или 5 дней?
3. **Документирую:** Создаю задачу (Ticket) в Jira/Trello/GitHub с детальным описанием (шаги воспроизведения, лог, скриншоты).
4. **Сообщаю:** Пишу в чат команды / коммент в PR:
   > «Коллеги, при работе над задачей #123 я случайно наткнулся на баг в `PaymentService` (тизер: транзакции теряются при таймауте). Создал задачу #456. Предлагаю не включать его в текущий PR, чтобы не расширять скоуп и не замедлить релиз фичи. Согласны?»
5. **Продолжаю свою задачу.**

#### 2. Почему так?

| Причина | Объяснение |
|---------|-----------|
| **Scope Creep** | Если я начну фиксить баг, я потяну за собой его тесты, ревью, возможные откаты. Моя задача (фича) задержится. |
| **Риски** | Фикс «попутного» бага может сломать то, что работает. Я не знаю контекста этого кода на 100%. |
| **Ответственность** | Если я залезу в чужой модуль и что-то сломает, я стану виновником нового инцидента. |
| **Прозрачность** | Команда знает о проблеме. Это не «тихий саботаж», а осознанное решение. |

#### 3. Исключения (Когда я всё-таки фиксю)
- **Блокирующий для моей задачи:** Если баг мешает мне протестировать мою фичу.
- **Критичная безопасность:** Если я вижу SQL Injection в коде, который я трогаю — я **обязан** сообщить, но всё равно лучше через отдельную задачу (Hotfix), чтобы задокументировать.
- **Очень простой фикс (1 строка):** Если это очевидная опечатка в логе, я могу исправить и явно упомянуть в PR: `// fixed: typo in log message`.

---

### Ситуация Б: Критерий приёмки противоречит цели задачи

**Принцип: «Критерии приёмки — это контракт. Если контракт противоречит цели — нужно менять контракт, а не нарушать его».**

#### 1. Что я делаю
1. **Останавливаюсь.** Не пытаюсь «обойти» или «подогнать» код под противоречие.
2. **Анализирую:**
   - Где именно противоречие? (AC говорит: «Добавь поле email». Цель: «Ускорить регистрацию». Email — это лишнее поле, которое снижает конверсию).
   - Кто принял это решение? (Аналитик? Заказчик?)
3. **Формулирую проблему:** Готовлю короткий документ/сообщение:
   > «Обнаружил противоречие в задаче #123.
   > **Цель:** Ускорить регистрацию (снизить время заполнения формы).
   > **Критерий приёмки 3:** Добавить обязательное поле «Дату рождения».
   > **Противоречие:** Обязательное поле увеличивает время заполнения и снижает конверсию.
   > **Предложение:** Сделать поле необязательным или убрать из AC, чтобы сохранить цель задачи.»
4. **Организуем разговор** с Product Owner / Тимлидом / Аналитиком.
5. **Жду решения.** Не продолжаю работу, пока AC не будут скорректированы или подтверждена важность этого критерия.

#### 2. Почему так?

| Причина | Объяснение |
|---------|-----------|
| **Ответственность за качество** | Если я сделаю как в AC, но это сломает цель задачи — это моя ошибка как инженера. Я обязан заметить. |
| **Избежать переделок** | Если я сделаю по противоречивому AC, а через неделю PO скажет «а, давайте уберем» — я потрачу время зря. |
| **Коммуникация** | Это шанс показать, что я думаю не только о коде, но и о бизнес-результате. |
| **Юридическая/Процессная защита** | Если позже будет спор «почему сделано так?», у меня будет запись, что я предлагал альтернативу. |

#### 3. Чего я НЕ делаю
- ❌ Не делаю «как AC», но с комментарием в коде `// TODO: это неправильно, но по AC`.
- ❌ Не игнорирует AC, делая «по-своему».
- ❌ Не молчит, надеясь, что «никто не заметит».

---

### Итоговая таблица действий

| Ситуация | Моё действие | Кому сообщить | Риск, если молчать/действовать в лоб |
|----------|--------------|---------------|-------------------------------------|
| **Попутный баг** | Документировать → Создать задачу → Продолжить свою | Тимлид / Команда | Scope creep, задержка релиза, скрытые баги |
| **AC противоречит цели** | Остановиться → Аргументировать → Ждать решения | Product Owner / Аналитик | Сломанная бизнес-цель, переделка, недовольство заказчиком |

### Ключевой навык
В обоих случаях я выступаю не как «исполнитель», а как **партнёр**, который защищает интересы продукта. Это то, что отличает Junior от Middle/Senior.

> ⏱ 14.6s · prompt 62572 tok · output 1456 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении — это не просто «сделано», а **доказательство ценности** и **управление ожиданиями**. Это документ, который позволяет команде принять релиз, а заказчику — убедиться, что его проблема решена.

### 1. Структура отчёта при успешном выполнении

#### А. Резюме (Executive Summary)
*   **Статус:** Успешно ✅
*   **Что сделано:** Краткое описание (1-2 предложения).
*   **Бизнес-результат:** Как это решает исходную проблему (снижение обращений в поддержку).

#### Б. Что изменилось (Changes)
*   **Фичи:** Список реализованных критериев приёмки (AC).
*   **Технические детали (опционально):**
    *   Новые API endpoints.
    *   Изменения в БД (миграции).
    *   Новые зависимости.

#### В. Как тестировать (Verification)
*   **Пошаговая инструкция для QA/Заказчика:**
    1. Войти как пользователь.
    2. Перейти в «Мои заказы».
    3. Применить фильтр «За прошлый месяц».
    4. Убедиться, что список обновился.
*   **Скриншоты/Видео:** Краткая демо-запись (Gif).
*   **Результаты тестов:** Ссылка на CI-отчет (зелёные галочки).

#### Г. Риски и ограничения (Risks & Limitations)
*   **Производительность:** «Запрос занимает 150 мс, соответствует SLA 300 мс».
*   **Ограничения:** «Поиск работает только по последним 2 годам заказов (ограничение БД)».
*   **Известные баги:** Если они есть и не блокирующие (со ссылкой на тикет).

#### Д. Метрики успеха (Metrics)
*   **Цель:** Снижение обращений в поддержку на 50%.
*   **Метод измерения:** «Мы отслеживаем тикеты с тегом `search-issue`. Базовое значение: 120/нед. После релиза наблюдаем 6 дней.»

#### Е. Ссылки
*   **Тикет Jira/Trello:** `#123`
*   **PR:** `Link to PR`
*   **Документация API:** `Swagger/Postman`

---

### 2. Что меняется, если часть задачи НЕ удалось сделать?

Если часть задачи выполнена не полностью, отчёт становится **отчётом о статусе** (Status Report), а не просто о выполнении.

#### Ключевые изменения:

| Элемент | Было (100% готово) | Стало (Частичная готовность) |
|---------|-------------------|---------------------------|
| **Статус** | ✅ Успешно | ⚠️ Частично выполнено / Блокеры |
| **Резюме** | «Функция готова к релизу» | «Функция готова к бета-тестированию. Осталось N пунктов» |
| **Что сделано** | Все AC | Список выполненных AC ✅ |
| **Что НЕ сделано** | — | Список невыполненных AC ❌ + **Причины** |
| **Блокеры** | — | Описание блокеров (например, «Ждём доступ к ElasticSearch») |
| **План действий** | — | Что нужно сделать, чтобы закончить. Срок. |
| **Риски** | Минимальные | Высокие: «Если не решим блокер к X, релиз сдвигается» |
| **Решение для заказчика** | «Можно релизить» | «Можно ставить в стейдж, но не в прод» ИЛИ «Нужен план Б» |

#### Пример отчёта при неполном выполнении:

> **Статус:** ⚠️ Частично выполнено (80%)
>
> **Что готово:**
> - ✅ Фильтр по дате и статусу работает.
> - ✅ Поиск по номеру заказа работает.
> - ✅ Тесты проходят.
>
> **Что НЕ готово:**
> - ❌ **Поиск по названию товара.**
>   - **Причина:** Индекс в ElasticSearch ещё не настроен (задача #456, в работе у бэкенд-команды).
>   - **Влияние:** Пользователи не смогут найти заказ по товару, только по номеру/дате.
>   - **Решение:** Фича будет работать в ограниченной версии. Полный функционал — после подключения ES (срок: +3 дня).
>
> **Риски:**
> - Пользователи всё равно могут жаловаться на поиск по товару.
> - **Митигация:** В UI добавлена подсказка «Ищите по номеру заказа».
>
> **Решение:**
> Предлагаю выкатить текущую версию на Staging и собрать обратную связь от поддержки. Если они подтвердят, что этого достаточно — релизим. Если нет — ждём ES.
>
> **Дальнейшие шаги:**
> 1. QA тестирует текущую версию (сегодня).
> 2. PM обсуждает с поддержкой ограничения (завтра).
> 3. Релиз (завтра/послезавтра).

---

### 3. Почему отчёт так важен?

1. **Прозрачность:** Никто не удивляется, что часть не работает. Это было объявлено заранее.
2. **Управление ожиданиями:** Заказчик знает, чего ждать.
3. **Защита:** Если через месяц будет жалоба «а почему поиск по товару не работает?» — у вас есть отчёт, где это задокументировано как known limitation.
4. **Уверенность в команде:** Показывает, что вы контролируете процесс, а не «сбрасываете» задачу.

### 4. Частые ошибки в отчётах

| Ошибка | Почему плохо |
|--------|--------------|
| «Всё ок, смотрите PR» | Нет контекста, нет доказательств. |
| «Не успели сделать» | Нет причин, нет плана. Выглядит как лень. |
| «Это баг тестов» | Не объяснено, почему тесты «неправильные». |
| Отсутствие метрик | Невозможно измерить успех. |

### Итог

**Отчёт — это мост между кодом и бизнесом.**
*   Если всё готово — это **документ подтверждения ценности**.
*   Если есть проблемы — это **план спасения ситуации**.

В обоих случаях главное: **честность, конкретика и план действий**.

> ⏱ 14.1s · prompt 64072 tok · output 1556 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

Короткий ответ: **В PostgreSQL не существует команды `FORCE INDEX`.**

Это распространённая путаница, возникающая из-за опыта работы с **MySQL** (и MariaDB), где существуют хинты `FORCE INDEX`, `USE INDEX` и `IGNORE INDEX`.

### Почему в PostgreSQL этого нет?

В PostgreSQL **запрос планировщик (planner)** определяет, какой индекс использовать, на основе:
1. **Статистики таблицы** (`pg_statistic`).
2. **Стоимости запроса** (cost-based optimization).
3. **Настроек сервера** (`enable_seqscan`, `enable_indexscan` и т.д.).

PostgreSQL считается **более "умным"** в выборе плана, чем MySQL, поэтому необходимость в ручном вмешательстве (`FORCE INDEX`) возникает значительно реже.

---

### А что делать, если PostgreSQL "упорно" выбирает `Seq Scan` (полное сканирование) вместо индекса?

Вместо `FORCE INDEX` в PostgreSQL используют следующие методы:

#### 1. Обновление статистики (Самое частое решение)
Если статистика устарела, planner может выбрать неверный план.

```sql
ANALYZE table_name;
```
Это заставит PostgreSQL пересчитать статистику. После этого `EXPLAIN ANALYZE` может показать, что индекс теперь используется.

#### 2. Проверка `EXPLAIN ANALYZE`
Всегда смотрите, **почему** planner выбрал `Seq Scan`.

```sql
EXPLAIN (ANALYZE, BUFFERS) SELECT ... WHERE ...;
```
Если в плане написано:
```
Seq Scan on table  (cost=0.00..1000.00 rows=1000 width=100)
```
Это значит, что planner считает, что сканирование всей таблицы дешевле, чем использование индекса. Обычно это происходит, если:
- Выборка > 20–30% таблицы.
- Индекс не покрывает запрос (нужно возвращаться к таблице).
- Статистика неверная.

#### 3. Использование `SET` для отключения методов доступа (Временное решение)
Можно **запретить** `Seq Scan` для текущей сессии:

```sql
SET enable_seqscan = off;
SELECT ... WHERE ...;
RESET enable_seqscan;
```

**Важно:**
- Это **влияет только на текущую сессию**.
- Это **не "принуждение" к использованию индекса**, а **запрет использования Seq Scan**.
- Planner может выбрать `Index Scan`, `Bitmap Scan` или другой план.
- **Используйте только для диагностики**, а не в production-коде.

#### 4. Создание подходящего индекса
Часто проблема не в "принуждении", а в том, что **нет подходящего индекса**.

**Пример:**
```sql
-- Запрос
SELECT * FROM orders WHERE customer_id = 123 AND created_at > '2023-01-01';
```

Если нет индекса:
```sql
CREATE INDEX idx_orders_customer_created ON orders (customer_id, created_at);
```

После создания индекса и `ANALYZE` planner, скорее всего, выберет его автоматически.

#### 5. Использование `LIMIT` и `ORDER BY`
Если запрос содержит `ORDER BY` по индексируемой колонке и `LIMIT`, planner часто выбирает `Index Scan`, даже если выборка большая.

#### 6. Использование `PG_HINT_PLAN` (Расширение)
В PostgreSQL есть расширение **`pg_hint_plan`**, которое позволяет добавлять "хинты" в SQL-запрос (похожие на `FORCE INDEX`).

**Установка:**
```sql
CREATE EXTENSION pg_hint_plan;
```

**Использование:**
```sql
SELECT /*+ IndexScan(orders) */ * FROM orders WHERE customer_id = 123;
```

**Важно:**
- Это **расширение**, не часть ядра PostgreSQL.
- Требует настройки `shared_preload_libraries`.
- Используется в production-базах только в крайних случаях.

---

### Сравнение: MySQL vs PostgreSQL

| Функциональность | MySQL | PostgreSQL |
|------------------|-------|------------|
| **Принудительное использование индекса** | `FORCE INDEX (idx_name)` | ❌ Нет (только через `pg_hint_plan`) |
| **Запрет использования Seq Scan** | ❌ Нет | ✅ `SET enable_seqscan = off;` |
| **Обновление статистики** | `ANALYZE TABLE` | `ANALYZE table_name;` |
| **Основной способ оптимизации** | Ручные хинты + индексы | **Автоматический planner** + индексы + статистика |

---

### Что делать на практике?

1. **Посмотрите `EXPLAIN ANALYZE`.**
2. **Проверьте статистику:** `ANALYZE table_name;`
3. **Создайте подходящий индекс**, если его нет.
4. **Проверьте, не нарушен ли баланс** (например, выборка > 30% таблицы — `Seq Scan` может быть оптимален).
5. **Если ничего не помогает** — используйте `SET enable_seqscan = off;` для диагностики, а не в production.
6. **В крайнем случае** — используйте расширение `pg_hint_plan`.

**Главный вывод:**
В PostgreSQL **не нужно "принуждать" индекс**. Нужно **помочь planner'у** сделать правильный выбор: актуальная статистика, правильные индексы и адекватные запросы.

> ⏱ 11.8s · prompt 65653 tok · output 1290 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

**Короткий ответ:** На **0%** в большинстве реальных сценариев. В редких случаях — на **доли процента** (0.1–0.5%), что полностью теряется на фоне других факторов (БД, сеть, сериализация).

### Почему `strict_types=1` практически не замедляет код?

`strict_types` — это **не** проверка типов в рантайме на каждом вызове. Это **ограничение на автоматическое приведение типов** при вызовах функций.

#### Как это работает под капотом:

1. **Без `strict_types`:**
   - PHP проверяет тип аргумента.
   - Если тип не совпадает — PHP пытается **привести** его к ожидаемому.
   - Приведение (coercion) — это **операция**, которая **быстрее**, чем проверка на несовместимость.
   - Пример: `"123"` → `123` — это быстрая операция приведения строки к числу.

2. **С `strict_types`:**
   - PHP проверяет тип аргумента.
   - Если тип не совпадает — PHP **немедленно выбрасывает `TypeError`**.
   - **Нет операции приведения** — только проверка и выброс исключения.
   - **Если тип совпадает** (нормальный случай) — **никакой дополнительной проверки нет!**

#### Ключевой момент:
`strict_types` **не добавляет** никаких дополнительных проверок в "нормальном" сценарии (когда типы совпадают). Он просто **запрещает** автоматическое приведение, если типы не совпадают.

### Когда может быть разница?

**Сценарий:** Функция принимает `int`, вы передаёте `string`, и в режиме без `strict_types` приведение происходит успешно.

```php
// Без strict_types
function add(int $a, int $b): int { return $a + $b; }
add("1", "2"); // Приведение: "1" → 1, "2" → 2. Быстро.

// С strict_types
function add(int $a, int $b): int { return $a + $b; }
add("1", "2"); // TypeError! Ошибка.
```

**Разница:** В режиме без `strict_types` PHP выполняет **операцию приведения**. В режиме с `strict_types` PHP **выбрасывает исключение**.

**Но!** Выброс исключения — это **дорогая** операция. Однако, если у вас в коде **нет ошибок типов**, то:
- Без `strict_types`: быстрое приведение.
- С `strict_types`: **никаких проверок вообще** (типы совпадают).

**Вывод:** Если ваш код **правильно типизирован** (вы передаёте `int` в `int`, `string` в `string`), то `strict_types` **не делает ничего额外** и **не замедляет код**.

### Бэчмарк (пример)

```php
<?php
// benchmark.php

function slow_function(int $x): int {
    return $x * 2;
}

$iterations = 1_000_000;

// Без strict_types
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    slow_function((int)"123"); // Приведение
}
$end = microtime(true);
echo "Without strict_types: " . ($end - $start) . " sec\n";

// С strict_types
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    slow_function(123); // Правильный тип
}
$end = microtime(true);
echo "With strict_types: " . ($end - $start) . " sec\n";
```

**Результат:** Разница будет в пределах погрешности (< 0.1%).

### Почему это миф?

1. **Люди путают** `strict_types` с **типизированными свойствами** или **включённой проверкой типов** (type checking).
2. **PHP 8.x** уже имеет **быструю проверку типов** в JIT-компиляторе и opcache.
3. **Реальный мир:** 99% времени PHP-код тратит на **БД, сеть, сериализацию**, а не на проверку типов.

### Когда `strict_types` МОЖЕТ "замедлить"?

Только если у вас **много ошибок типов** и вы **ловите** `TypeError` в цикле:

```php
// Антипаттерн
for ($i = 0; $i < 1000000; $i++) {
    try {
        strict_function((string)$i); // TypeError каждый раз!
    } catch (TypeError $e) {
        // Обработка
    }
}
```

**Но это уже баг в коде**, а не "замедление из-за strict_types".

### Итог

| Сценарий | Влияние на производительность |
|----------|-------------------------------|
| Код типизирован правильно | **0%** (никакой разницы) |
| Код содержит ошибки типов | **Замедление** (но это баг, а не фича) |
| Высоконагруженный цикл с приведением | **0.1–0.5%** (но это редкость) |

**Рекомендация:**
Всегда используйте `declare(strict_types=1);`. Это **бесплатная** защита от багов. Производительность — не причина отказываться от неё.

> ⏱ 11.8s · prompt 66983 tok · output 1280 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Это частый миф: **Doctrine ORM поддерживает составные первичные ключи (Composite Primary Keys, CPK)**. Но поддерживает их **крайне неудобно**, поэтому в 95% случаев в реальных проектах от них **отказываются**.

Давайте разберём, почему так происходит и как с этим живут.

### 1. Почему Doctrine "не любит" составные ключи?

С технической точки зрения Doctrine умеет создавать сущности с CPK, но это ломает фундаментальные принципы ORM:

#### А. Отсутствие уникального идентификатора (Identity)
ORM строится на концепции: **1 запись = 1 объект с уникальным ID**.
- `EntityManager::find(Order::class, 123)` — работает, потому что `id` уникален.
- `EntityManager::find(OrderItem::class, [1, 'SKU-123'])` — **не работает** так же просто.
- Вы не можете легко отследить состояние сущности в памяти (Identity Map), если у неё нет одного скалярного ID.

#### Б. Нарушение инкапсуляции Aggregate Root
В DDD сущности внутри агрегата **не должны** идентифицироваться глобально.
- `OrderItem` идентифицируется **контекстом** (в каком заказе он находится).
- Если `OrderItem` имеет свой составной ключ `[order_id, product_id]`, это значит, что `order_id` дублируется в каждой строке.
- Это нарушает нормализацию (2NF) и логику DDD: `Order` — это агрегат, `OrderItem` — его часть.

#### В. Сложность маппинга
- `#[ORM\Id]` нужно применять к каждому полю составного ключа.
- Репозитории становятся сложнее: `find()` принимает массив, а не скаляр.
- Lazy Loading и каскадные операции работают некорректно или требуют ручной настройки.
- Сложно реализовать `Doctrine\Common\Collections\Collection` для сущностей с CPK.

#### Г. Производительность
- Составные ключи часто **длиннее** (2+ индекса вместо 1).
- Обновление составного ключа (если он мутабелен) требует обновления **всех** внешних ключей, ссылающихся на эту строку. Это дорого.

---

### 2. Как с этим живут в реальных проектах?

#### Вариант 1: Суррогатный ключ (Surrogate Key) — **Стандарт**

Самый популярный подход. Даже если бизнес-логика требует составного ключа, в БД создаётся отдельное поле `id` (UUID или Bigint).

**Пример: Таблица `order_items`**

| order_id | product_id | quantity | **id (UUID)** |
|----------|------------|----------|---------------|
| 1        | 5          | 2        | `a1b2c3...`   |
| 1        | 6          | 1        | `d4e5f6...`   |
| 2        | 5          | 1        | `g7h8i9...`   |

**Плюсы:**
- `id` уникален, прост, быстрый.
- `UNIQUE` constraint на `(order_id, product_id)` гарантирует бизнес-инвариант.
- ORM работает идеально.
- Легко делать `JOIN` и `WHERE id = ...`.

**Минусы:**
- Нормализация страдает (3NF нарушена).
- Нужно поддерживать уникальность через `UNIQUE` index.
- Миграции сложнее (нужно заполнять UUID).

**Реализация в Doctrine:**
```php
#[ORM\Entity]
class OrderItem {
    #[ORM\Id, ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'integer')]
    private int $orderId;

    #[ORM\Column(type: 'string')]
    private string $productId;

    public function __construct() {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
```

#### Вариант 2: Вложенные сущности (Embedded / Composition)

Если `OrderItem` не должен быть доступен напрямую (только через `Order`), его можно сделать **не сущностью**, а **Value Object** или **Embedded Entity**.

**Пример:**
- `Order` — Entity.
- `OrderItem` — **не** Entity, а массив или ArrayObject внутри `Order`.
- В БД это может быть `JSON`-колонка или отдельные строки в таблице, но ORM не знает про `OrderItem` как отдельную сущность.

**Плюсы:**
- Полная инкапсуляция.
- Нет проблем с CPK.
- Быстро.

**Минусы:**
- Сложно делать запросы к деталям заказа отдельно.
- Сложно масштабировать (ограничение размера JSON/массива).

#### Вариант 3: Настоящий CPK (Rare)

Используется, если:
- Таблица — это **связка** (мост) между двумя сущностями (Many-Many).
- В таблице нет других полей, кроме двух ключей.
- Пример: `user_roles` (`user_id`, `role_id`).

**Реализация в Doctrine:**
```php
#[ORM\Entity]
class UserRole {
    #[ORM\Id, ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Id, ORM\Column(type: 'integer')]
    private int $roleId;
}
```
**Проблема:** `find()` работает плохо, но для простых таблиц-мостов это допустимо.

#### Вариант 4: Нормализация (Разделение таблиц)

Вместо CPK в одной таблице — разнесите данные.

**Было (CPK):**
`order_items` (`order_id`, `product_id`, `quantity`)

**Стало (Нормализация):**
- `orders` (`id`, ...)
- `order_items` (`id`, `order_id`, `product_id`, `quantity`) — `id` суррогатный.
- `products` (`id`, ...)

---

### 3. Сравнительная таблица

| Подход | Используется в | Почему |
|--------|----------------|--------|
| **Суррогатный ключ (UUID/Int)** | 90% проектов | Лучший баланс производительности, простоты ORM и целостности. |
| **Embedded / JSON** | Микросервисы, CQRS | Максимальная инкапсуляция, скорость чтения. |
| **CPK (2+ поля)** | Таблицы-мосты (Many-Many) | Логичная структура, нет лишних полей. |
| **Нормализация** | Классические RDBMS | Теория баз данных, сложность ORM. |

---

### 4. Рекомендации

1. **Не используйте CPK для бизнес-сущностей** (`OrderItem`, `Payment`, `UserAddress`).
   - Используйте **суррогатный ключ** (`id`) + `UNIQUE` constraint на бизнес-поля.

2. **Используйте CPK только для таблиц-мостов** (`user_roles`, `product_categories`).
   - Там, где нет других данных.

3. **В DDD:**
   - Если сущность не имеет своего идентификатора — она **Value Object**.
   - Если сущность идентифицируется через контекст — она **вложена** в родительский агрегат.
   - **Не создавайте Entity с CPK** без крайней необходимости.

4. **Если всё-таки нужен CPK:**
   - Добавьте `#[ORM\Id]` к обоим полям.
   - Будьте готовы к сложностям с `find()` и `Identity Map`.
   - Используйте `Doctrine\DBAL\Types\CompositeType` (если нужно).

---

### Итог

Doctrine **умеет** CPK, но **не любит** их, потому что они ломают удобную модель "1 объект = 1 ID".

**Решение:**
- Для 95% случаев: **Суррогатный ключ + UNIQUE index**.
- Для таблиц-мостов: **CPK**.
- Для DDD: **Composition (Embedded)** или **Aggregate Root**.

**Главный принцип:**
> **Идентификатор — это техническая деталь.**
> Бизнес-логика использует **составные условия** (`UNIQUE`), а ORM — **скалярные ID**.

> ⏱ 17.6s · prompt 68303 tok · output 1933 tok

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

Этот код — классический пример «спагетти-кода», который часто встречается в легаси или быстрых прототипах. Он содержит сразу несколько критических уязвимостей и архитектурных ошибок.

Вот список проблем от самых критичных к косметическим:

### 🔴 Критические проблемы (Security & Data Integrity)

1.  **SQL Injection (SQLi):**
    ```php
    $pdo->query("SELECT * FROM orders WHERE id = $id")
    $pdo->exec("UPDATE orders SET status = 'paid' WHERE id = $id")
    ```
    Переменная `$id` подставляется в строку напрямую. Злоумышленник может передать `id = 1; DROP TABLE orders;` и удалить базу.

2.  **Race Condition (Гонка условий):**
    ```php
    $order = $pdo->query("SELECT...")->fetch();
    // ... время ...
    $pdo->exec("UPDATE...");
    ```
    Между `SELECT` и `UPDATE` другой процесс может изменить статус заказа. Вы можете оплатить заказ, который уже отменён, или оплатить один и тот же заказ дважды (если проверка статуса отсутствует).

3.  **Отсутствие проверки статуса:**
    Код ставит статус `'paid'` независимо от текущего статуса. Можно «оплатить» заказ, который уже оплачен, отменён или находится в обработке. Это бизнес-баг.

4.  **Нет транзакции:**
    Если `Mailer::send()` упадёт, но `UPDATE` уже закоммитился — заказ будет оплачен, но письма не будет. Если `UPDATE` упадёт, но `SELECT` прошёл — мы потеряем данные.

---

### 🟠 Архитектурные проблемы

5.  **Нарушение SRP (Single Responsibility Principle):**
    Контроллер делает всё: читает БД, пишет в БД, отправляет письма, пишет логи. Это нарушает инкапсуляцию.

6.  **Зависимость от глобального состояния:**
    `DB::connect()` и `new Logger()` внутри метода. Это делает код не тестируемым (моки невозможны).

7.  **Отсутствие доменной модели:**
    `$order` — это просто массив (array). Нет валидации, нет типов, нет бизнес-логики.

8.  **Грязный код:**
    - Магические числа (`404`).
    - Английский/русский смешаны (`'not found'`, `'Ваш заказ оплачен'`).
    - Нет обработки ошибок (`try-catch`).

---

### 🟡 Что бы я переделал?

Я бы разделил этот код на **4 слоя**:

1.  **Controller:** Принимает запрос, валидирует входные данные, вызывает сервис.
2.  **Service (Application):** Оркестрация бизнес-логики, управление транзакцией.
3.  **Domain (Entity/Value Object):** Бизнес-правила (можно ли платить?).
4.  **Repository:** Доступ к БД (через PDO или ORM).

#### Код после рефакторинга

**1. Domain Layer (Чистый PHP)**
```php
class Order {
    public function __construct(
        private int $id,
        private string $email,
        private string $status
    ) {}

    public function canBePaid(): bool {
        return $this->status === 'pending';
    }

    public function markAsPaid(): void {
        if (!$this->canBePaid()) {
            throw new OrderAlreadyProcessedException($this->id);
        }
        $this->status = 'paid';
    }

    // Геттеры...
}

class OrderRepositoryInterface {
    public function findById(int $id): ?Order;
    public function save(Order $order): void;
}
```

**2. Infrastructure (PDO Repository)**
```php
class PdoOrderRepository implements OrderRepositoryInterface {
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Order {
        // ✅ Подготовленный запрос (Prepared Statement)
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Order(
            (int)$row['id'],
            $row['email'],
            $row['status']
        );
    }

    public function save(Order $order): void {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = :status WHERE id = :id AND status = :expected_status");
        $stmt->execute([
            'status' => $order->getStatus(),
            'id' => $order->getId(),
            'expected_status' => 'pending' // Оптимистичная блокировка на уровне SQL
        ]);

        if ($stmt->rowCount() === 0) {
            throw new OrderAlreadyProcessedException($order->getId());
        }
    }
}
```

**3. Application Service (Оркестрация)**
```php
class OrderPaymentService {
    public function __construct(
        private OrderRepositoryInterface $repo,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function pay(int $orderId): void {
        $order = $this->repo->findById($orderId);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        // 1. Проверяем бизнес-правила
        if (!$order->canBePaid()) {
            throw new OrderAlreadyProcessedException($orderId);
        }

        // 2. Изменяем состояние в БД (атомарно)
        $order->markAsPaid();
        $this->repo->save($order);

        // 3. Побочные эффекты (после успешного сохранения)
        $this->mailer->send($order->getEmail(), 'Ваш заказ оплачен');
        $this->logger->info('Order paid', ['order_id' => $orderId]);
    }
}
```

**4. Controller (Входная точка)**
```php
class OrderController {
    public function __construct(private OrderPaymentService $service) {}

    public function pay(Request $req): Response {
        // 1. Валидация входа
        $id = $req->get('order_id');
        if (!is_numeric($id)) {
            return new Response('Invalid order ID', 400);
        }

        try {
            // 2. Вызов сервиса
            $this->service->pay((int)$id);
            return new Response('OK', 200);
        } catch (OrderNotFoundException $e) {
            return new Response('Not found', 404);
        } catch (OrderAlreadyProcessedException $e) {
            return new Response('Order already processed', 409);
        } catch (Exception $e) {
            // Логируем ошибку, но не раскрываем детали
            error_log($e->getMessage());
            return new Response('Internal Server Error', 500);
        }
    }
}
```

---

### С чего начать и почему?

1.  **Сначала Security (SQLi).**
    Это критично. Без prepared statements код **нельзя** выкатывать в прод.
    *Действие:* Заменить все `query` и `exec` на `prepare`/`execute`.

2.  **Затем Data Integrity (Race Condition + Transаctions).**
    Нужно гарантировать, что статус меняется атомарно и только если он был `'pending'`.
    *Действие:* Добавить условие `WHERE status = 'pending'` в UPDATE и проверять `rowCount`.

3.  **Потом Architecture (SRP + Testability).**
    Вынести логику из контроллера.
    *Действие:* Создать `OrderRepository` и `OrderPaymentService`.

4.  **Наконец, Code Quality.**
    Добавить типы, валидацию, обработку ошибок.
    *Действие:* Написать доменную модель `Order`, использовать интерфейсы для `Mailer` и `Logger`.

### Итог

| Проблема | Решение |
|----------|---------|
| SQL Injection | Prepared Statements |
| Race Condition | `UPDATE ... WHERE status = 'pending'` + проверка `rowCount` |
| Bad Architecture | Разделение на Controller / Service / Repository |
| Untestable | Внедрение зависимостей через интерфейсы |
| No Error Handling | try-catch с переводом в HTTP-статусы |

Такой код будет **безопасным**, **надежным** и **тестируемым**.

> ⏱ 15.4s · prompt 70449 tok · output 1993 tok

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

Этот код — классический пример **«Анемичной доменной модели» (Anemic Domain Model)**, где ORM и инфраструктура вторглись в сердце домена. Это грубое нарушение принципов DDD и чистой архитектуры.

Вот разбор ошибок от критических к менее критичным:

### 1. Критические нарушения

#### А. Зависимость от инфраструктуры в домене
```php
private EntityManagerInterface $em
```
**Почему это плохо:**
- **Нарушение Dependency Rule:** Домен не должен знать про `Doctrine`, `Symfony` или любую другую ORM/фреймворк.
- **Нарушение SRP:** Сущность `User` теперь отвечает не только за логику пользователя, но и за управление жизненным циклом данных в БД.
- **Невозможность тестирования:** Чтобы протестировать `changeEmail`, вы должны мокнуть `EntityManagerInterface`. Это сложно и хрупко.

#### Б. Нарушение инкапсуляции и инвариантов
```php
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();
}
```
**Почему это плохо:**
- **Нет валидации:** Можно установить `email = ""` или `"not-an-email"`. Домен не защищает себя.
- **Автоматическое сохранение:** Вызов `flush()` внутри доменной сущности — это «магия». Бизнес-логика не должна знать, когда данные записываются в БД. Сохранение — это ответственность **Application Service** или **Repository**.
- **Нарушение Single Responsibility:** Сущность меняет состояние И одновременно фиксирует его в персистентном хранилище.

#### В. Публичный доступ к `EntityManager`
Хотя здесь он `private`, сам факт его присутствия в доменной сущности — это архитектурная ошибка.

---

### 2. Как правильно? (Решение)

Мы разделим ответственности:
1. **Domain:** Валидация и изменение состояния.
2. **Application Service:** Оркестрация (вызов домена + сохранение).
3. **Repository:** Доступ к БД.

#### Шаг 1: Чистый Domain Object (без ORM-аннотаций)

```php
// src/Domain/User.php
class User
{
    private string $email;

    public function __construct(string $email)
    {
        $this->setEmail($email); // Валидация при создании
    }

    public function setEmail(string $email): void
    {
        $this->validateEmail($email);
        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email address');
        }
    }
}
```
**Что изменилось:**
- Убрали `EntityManagerInterface`.
- Добавили валидацию.
- Убрали `flush()`.
- Класс стал чистым PHP, который можно юнит-тестировать без БД.

#### Шаг 2: Doctrine Entity (Наследник или Обёртка)

В Doctrine мы создаем сущность, которая расширяет чистый доменный класс или дублирует его структуру.

```php
// src/Entity/User.php
#[ORM\Entity]
class User extends \App\Domain\User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: integer)]
    private ?int $id = null;

    #[ORM\Column(type: string, unique: true)]
    private string $email;

    // Если используем наследование, нужно маппить свойства
    // Лучше: сделать User Entity и хранить в нём данные, 
    // а доменный User — это отдельный класс.
    
    public function __construct(string $email)
    {
        parent::__construct($email);
        $this->email = $email;
    }

    // Переопределяем setEmail, чтобы синхронизировать с ORM-свойством
    public function setEmail(string $email): void
    {
        parent::setEmail($email); // Валидация в домене
        $this->email = $email;   // Сохранение в свойство ORM
    }
}
```

*Альтернатива (лучше):* Не наследовать, а использовать **Composition** или просто дублировать свойства. Но для простоты покажем наследование.

#### Шаг 3: Application Service (Оркестратор)

Здесь мы связываем домен и персистентность.

```php
// src/Service/UserService.php
class UserService
{
    public function __construct(
        private User $userRepository // Интерфейс репозитория
    ) {}

    public function changeEmail(int $userId, string $newEmail): void
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        // 1. Изменяем состояние (домен сам себя валидирует)
        $user->setEmail($newEmail);

        // 2. Сохраняем (инфраструктура знает, как сохранить)
        $this->userRepository->save($user);
    }
}
```

#### Шаг 4: Repository

```php
// src/Repository/UserRepository.php
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function save(User $user): void;
}

// src/Repository/DoctrineUserRepository.php
class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findById(int $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
```

---

### Сравнение

| Аспект | Было (Плохо) | Стало (Хорошо) |
|--------|-------------|----------------|
| **Зависимости** | Домен знает про `EntityManager` | Домен знает только про PHP |
| **Тестируемость** | Нужен мок `EntityManager` | Нужен только мок `Repository` (если тестируешь сервис) или нет (если тестируешь домен) |
| **Инкапсуляция** | Нет валидации | Валидация в домене |
| **Сохранение** | Автоматическое в домене | Явное в Application Service |
| **Гибкость** | Привязан к Doctrine | Можно заменить на любую другую БД, не трогая домен |

### Почему так лучше?

1. **Домен стабилен.** Вы можете менять БД, фреймворк, язык (в идеале), а доменная логика останется.
2. **Тестируемость.** `User` можно тестировать как чистый класс. `UserService` можно тестировать с мок-репозиторием.
3. **Безопасность.** Валидация гарантируется на уровне домена, а не надеждой на то, что разработчик не забудет добавить проверку.
4. **Читаемость.** Код читается как бизнес-процесс: «Измени email -> Сохрани».

**Главное правило:**
> **Домен не знает про БД. БД знает про домен.**

> ⏱ 15.2s · prompt 72592 tok · output 1741 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Решение технической задачи в незнакомом стеке — это не про «загуглить и написать код». Это про **систематическое снижение неопределенности**.

Мой алгоритм действий разделен на 5 фаз. В конце — критерии, когда просить помощь, а когда копать самому.

---

### Фаза 1: Квалификация задачи (15–30 минут)

**Цель:** Понять, что именно требуется, и оценить масштаб.

1.  **Чтение документации и кода (Passive Reading):**
    *   Что делает этот компонент/библиотека?
    *   Какие у неё зависимости?
    *   Есть ли примеры (examples) или тесты?
2.  **Поиск аналогов:**
    *   Решал ли кто-то в команде похожую задачу?
    *   Есть ли Stack Overflow / GitHub Issues с похожими симптомами?
3.  **Формулировка проблемы:**
    *   Пишу 1–2 предложения: *«Нам нужно [действие] в [технология], чтобы [результат]. Ограничения: [время, память, кроссплатформенность]».*

**Красный флаг:** Если за 30 минут я не могу сформулировать проблему простыми словами — я не понимаю контекст. **Стоп.** Иду к Phase 2.

---

### Фаза 2: Мини-прототип (Spike) (1–3 часа)

**Цель:** Убиться лицом о реальность. Проверить, работает ли это вообще.

1.  **Изоляция:**
    *   Создаю отдельный репозиторий или песочницу.
    *   Никакого продакшен-кода. Только «Hello World» для данной проблемы.
2.  **Минимальный путь:**
    *   Если задача «интегрировать оплату» — я не пишу всю логику. Я отправляю тестовый платёж.
    *   Если задача «починить рендеринг» — я пытаюсь отрендерить один элемент.
3.  **Замеры:**
    *   Сколько времени занимает сборка?
    *   Как быстро запускается?
    *   Какие ошибки вылетают в консоли?

**Результат фаза:** У меня есть **рабочий фрагмент** или **человек-часов, потраченных на «не работает»**.

**Красный флаг:** Если прототип не запускается после 3 часов активной работы — технология несовместима с задачей или я иду не в ту сторону. **Стоп.** Иду к Phase 3.

---

### Фаза 3: Оценка и выбор стратегии (30 минут)

Теперь у меня есть данные. Я выбираю одну из трёх стратегий:

| Стратегия | Когда использовать |
|-----------|-------------------|
| **A. Реализовать самому** | Я понимаю «как», осталось «сделать». |
| **B. Аутсорс / Помощь** | Задача узкоспециализированная, риск высокий. |
| **C. Отказ / Замена** | Технология не подходит, есть альтернатива. |

**Вопросы для выбора:**
1.  **Критичность:** Если я застряну, кто будет дежурить?
2.  **Время:** Сколько у меня есть в днях?
3.  **Знания команды:** Кто ещё знает этот стек?

---

### Фаза 4: Реализация с чекпоинтами

Не пишу 500 строк кода, а делаю **итерации по 20–30 минут**:

1.  Написал 30 строк.
2.  Запустил, проверил лог.
3.  Написал следующие 30 строк.
4.  **Каждый час** фиксирую прогресс в тикет/чат: *«Сделал X, сейчас делаю Y, ожидаю проблему Z».*

**Почему так?** Если я застряну, я потеряю только 1 час, а не весь день. Команда может вмешаться, не теряя контекст.

---

### Фаза 5: Валидация и передача

1.  **Тесты:** Написал юнит/интеграционные тесты.
2.  **Документация:** Написал README: «Как запустить», «Какие подводные камни».
3.  **Code Review:** Показываю код коллеге, который знает стек (если есть).
4.  **Отчёт:** *«Проблема решена. Риски: [X]. Рекомендации: [Y]».*

---

### Когда просить помощь, а когда разбираться самому?

#### ✅ Разбираюсь САМОСТОЯТЕЛЬНО, если:

1.  **Это «мелочь»:** Проблема решается за < 2 часов.
    *   *Пример:* Подключение библиотеки, настройка конфига, простой SQL-запрос.
2.  **У меня есть прототип:** Я уже знаю, что «это работает», осталось «сделать красиво».
3.  **Нет риска для продакшена:** Это фича, которую можно откатить.
4.  **Документация хорошая:** Я читаю мануал и понимаю, что делать.

#### 🆘 ПРОШУ ПОМОЩЬ, если:

1.  **Я потрачу > 3 часов на «привет мир»:**
    *   Если базовая интеграция не работает — я теряю время. Лучше спросить: *«Кто-нибудь уже сталкивался с [технология] в [контексте]?»*
2.  **Проблема неочевидная:**
    *   Ошибки в логике, которые не воспроизводятся в прототипе.
    *   *Пример:* Мемоизация не работает, память утекает, гонки в мультитреддинге.
3.  **Критическая бизнес-логика:**
    *   Если это платёжный модуль или аутентификация — я **обязан** показывать код коллеге до деплоя.
4.  **Я «ушёл» в тупик:**
    *   Я переписал код 3 раза, но ошибка та же. Это сигнал: *«Я не понимаю, как это работает».*
5.  **Нужно решение архитектурного уровня:**
    *   *Пример:* «Оставить ли эту технологию на 5 лет?» — это не вопрос кодера, это вопрос архитектурного комитета.

---

### Как просить помощь правильно?

Не пиши: *«Помогите, не работает».*

Пиши так:
> «Я работаю над [задача]. Технология [X].
> **Что сделал:** [1. Установил, 2. Написал прототип, 3. Получил ошибку Y].
> **Что пробовал:** [1. Поменял версию, 2. Перечитал docs].
> **Текущий статус:** [Застрял на Z].
> **Вопрос:** [Кто-нибудь может подсказать, как настроить W? / Могу ли я получить код-ревью?].
> **Скриншоты/Логи:** [Прикрепил].»

**Почему так?** Вы экономите время команды. Они видят, что вы не ленивы, а действительно ищете решение.

---

### Итог

| Этап | Действие | Время |
|------|----------|-------|
| 1 | Понять задачу | 30 мин |
| 2 | Прототип (Spike) | 1–3 часа |
| 3 | Выбор стратегии | 30 мин |
| 4 | Реализация с чекпоинтами | 2–8 часов |
| 5 | Валидация и передача | 1–2 часа |

**Главное правило:**
> **Неопределённость убивает. Прототип её убивает.**
> Всегда начинай с «Hello World» для конкретной проблемы, прежде чем строить «замок».

> ⏱ 19.7s · prompt 74409 tok · output 1871 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Это одна из самых важных ситуаций в работе инженера. Она проверяет не навыки, а **надежность** и **коммуникацию**.

Если вы молчите и в день дедлайна говорите «не успел» — вы подрываете доверие к себе навсегда. Если вы скажете о проблеме **сейчас** — вы управляете ожиданиями и сохраняете доверие.

Вот мой алгоритм действий.

### 1. Оценка (Немедленно)

Прежде чем открыть рот, я провожу холодную оценку:
1.  **Что именно являлось причиной?**
    *   Я недооценил сложность (был неправ).
    *   Появился новый, непредвиденный фактор (баг, изменение требований, зависимость от другого сервиса).
    *   Я застрял в «детских проблемах» (незнакомя технология, среда разработки).
2.  **Сколько точно нужно?**
    *   Я не гадальщик. Я смотрю на свои «чекпоинты».
    *   Если я сделал 50%, а осталось 50% сложного кода — это +1 день.
    *   Если я сделал 20%, а остальные 80% я еще не начинал — это +2 дня.
3.  **Что я могу сделать СЕГОДНЯ?**
    *   Могу ли я сдать «часть»?
    *   Могу ли я сделать «черновую версию» (MVP)?

### 2. Формулировка сообщения (Честно и конкретно)

Я **не пишу**: «Прости, не успею».
Я пишу: «Обновление по статусу задачи #123».

#### Пример сообщения:

> **Тема:** Статус задачи #123 (Интеграция с платёжным шлюзом)
>
> **Коллеги / PM / Тимлид,**
>
> Даю обновление по задаче, которую я планировал закрыть завтра.
>
> **Текущий статус:**
> - ✅ Логика создания заказа реализована.
> - ✅ Тесты основного потока написаны и проходят.
> - ⚠️ **Проблема:** В процессе интеграции выявили, что платёжный шлюз возвращает данные в формате, который мы не обрабатываем корректно. Это потребовало дополнительной разработки адаптера, которой не было в исходной оценке.
>
> **Влияние на сроки:**
> Изначальная оценка (1 день) не учитывала эту интеграционную сложность. Теперь на завершение, тестирование и код-ревью нужно **2 дня**.
>
> **Новый дедлайн:** [Дата] (вместо завтра).
>
> **Что я предлагаю:**
> 1. **Вариант А:** Дать мне 2 дня, чтобы сдать готовый, стабильный код с полным покрытием тестами.
> 2. **Вариант Б:** Если критично выпустить завтра, я могу сдать **MVP** к завтрашнему дню, но с упрощенной обработкой ошибок (риск: нестабильность при сбое платёжки). Полный вариант будет через 2 дня.
>
> Прошу подтвердить, какой вариант приоритетнее.
>
> Спасибо!

### 3. Почему именно так?

#### А. Я не прошу прощения. Я информирую.
«Прости» — это эмоция. «Обновление статуса» — это факт. Я не виноват в том, что код сложился иначе. Я виноват только в том, что вовремя не сказал.

#### Б. Я объясняю «Почему».
Я не говорю «мне надо больше времени». Я говорю «я нашел неизвестную проблему в интеграции». Это показывает, что я **работаю**, а не «ленюсь».

#### В. Я даю выбор.
Я не говорю «я не успеву». Я говорю «у нас есть два варианта: качество или скорость». Это превращает меня из «проблемы» в «решение».

#### Г. Я фиксирую новый срок.
«2 дня» — это конкретика. «Скоро» — это ничего.

### 4. Что делать, если это случилось не в первый раз?

Если вы **повторно** не укладываетесь в сроки, это проблема не в одной задаче, а в вашем процессе.

**Что я делаю:**
1.  **Анализирую причину:** Почему я опять ошибся?
    *   Я завышал свою скорость?
    *   Я не считываю контекст задачи?
    *   Мне мешают прерывания?
2.  **Разговор с Тимлидом:**
    > «Я заметил, что мои оценки по [тип задач] стабильно отклоняются в сторону задержки на 20-30%. Давай обсудим, как я могу лучше оценивать? Может, мне нужно оставлять больше буфера или разбивать задачи на меньшие части?»
3.  **Изменение процесса:**
    *   Начинаю с более сложных частей (рискованных) в начале задачи, а не в конце.
    *   Ввожу «буфер риска» (+20-30% к оценке).
    *   Ставлю себе промежуточные чекпоинты: «Если к полудню я не сделал X — я говорю о задержке».

### 5. Чего никогда не делать

| ❌ Антипаттерн | Почему это плохо |
|----------------|------------------|
| **Молчать до дедлайна** | Убивает доверие. Выглядит как «некомпетентность». |
| **Обвинять других** | «Баг из-за плохой документации». Даже если это так — вы не дали команде шанс помочь. |
| **Обещать, что «еще чуть-чуть»** | «Еще часик» превращается в «еще день». Это обман. |
| **Работать по ночам/выходным** | Это «героизм», который приводит к выгоранию и ошибкам. |
| **Скрывать сложность** | «Вроде всё просто, я завтра закончу». Если внутри вас есть сомнения — вы уже знаете, что не успеете. |

### Итог

**Правило одного звонка:**
Если вы понимаете, что не успеваете — **скажите об этом в тот же момент, когда вы это поняли.**

Не ждите конца дня. Не ждите завтра. Не ждите, пока «может, повезет».

**Раннее предупреждение** — это признак профессионализма.
**Позднее оправдание** — это признак непрофессионализма.

> ⏱ 16.6s · prompt 76322 tok · output 1529 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это классическая дилемма инженера: **Perfect is the enemy of the good**.

В реальном проекте я не ищу «идеальный код». Я ищу **код, который соответствует текущему уровню риска и ценности бизнес-задачи**.

Мой подход строится на трёх принципах: **Контекст**, **Риск** и **Стоимость изменений**.

---

### 1. Контекст: Что это за код?

Не весь код одинаков по важности. Я делю код на 3 категории:

| Категория | Примеры | Стандарт качества |
|-----------|---------|-------------------|
| **Критичный** | Платежи, аутентификация, валидация данных, шифрование | 🛡️ **Максимальный.** Полный покрытие тестами, ревью, строгая типизация, документация. |
| **Бизнес-критичный** | Логика расчётов, правила акций, интеграции | ⚖️ **Высокий.** Тесты на ключевые пути, ревью, ясные имена. |
| **Вспомогательный** | Внутренние утилиты, скрипты, временные заглушки, админ-панель | 🏃 **Достаточный.** Читаемость, базовые тесты (если есть). Не тратим время на рефакторинг. |

**Правило:** Чем выше цена ошибки, тем выше планка качества.

---

### 2. Риск: Что будет, если код сломается?

Я оцениваю **влияние** (Impact) и **вероятность** (Probability) ошибки.

- **Высокий риск:** Сломается → потеря денег, утечка данных, падение сервиса.
  - *Действие:* Код должен быть **надёжным**. Лучше потратить 2 часа на тесты, чем 2 недели на починку инцидента.
- **Низкий риск:** Сломается → неудобно пользователю, но сервис работает.
  - *Действие:* Код должен быть **быстрым**. Лучше выкатить, собрать фидбек и починить.

**Вопрос себе:** *«Если завтра этот код упадёт, что произойдёт?»*

---

### 3. Стоимость изменений: Легко ли это поменять?

Это ключевой критерий «достаточно хорошо».

| Ситуация | Решение |
|----------|---------|
| Код сложный, но **не меняется** (например, парсер старого формата) | Можно оставить «грязным», но **задокументировать**. |
| Код сложный и **часто меняется** (бизнес-логика) | **Обязателен рефакторинг**. Иначе каждая правка будет болезненной. |
| Код простой, но **сложная бизнес-логика** | **Обязательны тесты**. Простой код скрывает сложность. |
| Код простой и **простая логика** | Можно отпустить без тестов (кроме edge cases). |

**Правило:** Если код меняется чаще, чем раз в месяц — он **должен** быть чистым.

---

### 4. Мои чек-листы для «достаточно хорошо»

#### ✅ Минимальный стандарт (Baseline)
Для любого кода, который идёт в main:
- [ ] Код **читается** (имена, структура).
- [ ] Код **проходит линт** (php-cs-fixer, rector).
- [ ] Код **не ломает существующие тесты**.
- [ ] Нет **жестко закодированных** значений (конфиги, константы).
- [ ] Нет **мёртвого кода**.

#### ✅ Стандарт «Хорошо» (Good)
Для бизнес-логики:
- [ ] Есть **юнит-тесты** на ключевые сценарии.
- [ ] Есть **описание** сложной логики (комментарии или docs).
- [ ] Код **модулен** (одна ответственность на класс/метод).
- [ ] Ошибки **обрабатываются** (try-catch или исключения).

#### ✅ Стандарт «Отлично» (Great)
Для критичных путей:
- [ ] Есть **интеграционные тесты**.
- [ ] Код **безопасен** (SQLi, XSS, race conditions проверены).
- [ ] Код **масштабируем** (можно добавить новые сценарии без переписывания).
- [ ] Есть **метрики** (логирование, мониторинг).

---

### 5. Баланс между качеством и скоростью

Я использую правило **«20% для перфекционизма»**.

- **80% времени** — на реализацию и основные тесты.
- **20% времени** — на рефакторинг, улучшения, edge cases.

**Если я превысил 20%:**
- Я останавливаюсь.
- Я спрашиваю себя: *«Достоин ли этот код 2 часов улучшений?»*
- Если нет — я **закрываю задачу** и создаю тикет «Improvement» для будущего.

**Почему это работает:**
- Я не блокирую релиз.
- Я не забываю про качество.
- Я могу приоритизировать улучшения.

---

### 6. Как я принимаю решение (Алгоритм)

1. **Код работает?**
   - Нет → Чини.
   - Да → Шаг 2.

2. **Есть критичные баги/уязвимости?**
   - Да → Чини.
   - Нет → Шаг 3.

3. **Код читается?**
   - Нет → Рефакторинг (минимальный).
   - Да → Шаг 4.

4. **Есть тесты на ключевые пути?**
   - Нет → Напиши (минимальные).
   - Да → Шаг 5.

5. **Есть ли время на улучшения?**
   - Да → Улучши.
   - Нет → **Задокументируй** (комментарий, тикет) и **отдавай**.

---

### 7. Чего я никогда не делаю

| ❌ Антипаттерн | Почему плохо |
|----------------|--------------|
| **«А давай ещё чуть-чуть улучшим»** | Бесконечный рефакторинг. Релиз сдвигается. |
| **«Это просто временная штука»** | Временные штуки живут годами. |
| **«Тесты — это потом»** | Без тестов код становится «заброшенным». |
| **«Я знаю, что это правильно»** | Если это не задокументировано — это не правда. |
| **«Пусть ревьюер поправит»** | Ревьюер — не редактор. Вы несёте ответственность за качество. |

---

### Итог

**«Достаточно хорошо» — это:**
1. Код **работает** и **безопасен**.
2. Код **читается** и **тестируется** (на ключевые пути).
3. Код **не блокирует** другие задачи.
4. Улучшения **задокументированы** и **приоритизированы**.

**Главное правило:**
> **Качество — это не «написать красиво», а «написать так, чтобы не бояться менять».**

Если вы не боитесь менять код — он достаточно хорош.

> ⏱ 16.9s · prompt 77912 tok · output 1705 tok

---
