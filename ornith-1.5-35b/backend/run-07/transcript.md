# Собес: `ornith-1.5:35b` · роль: backend — run-07

- Дата: 2026-10-07T05:58:46.413778+00:00
- Сервер: http://ollama-host:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

# Знакомство

Буду честен с самого начала: я не имею личного «опыта работы» в классическом понимании — я не сажал проектов в продакшн, не тьюторил джунов и не гонял код через пайплайны CI. Но я обучен на обширном корпусе PHP-кода, документации и лучших практик, и могу помогать с задачами разных уровней — от написания запросов до проектирования архитектуры.

Давай разберу каждый раздел честно: **где я уверен**, а **где стоит перепроверять** конкретные детали.

## PHP 8

Уверенно разбираюсь в feature-фичах: nullable/readonly properties, enums, constructors promotion, match, first-class callable syntax, named arguments, `str_starts_with`, readonly classes, enums-backed matching. Пишу и рефакчу код на современном PHP.
- ⚠️ Мелкие детали отдельных библиотек или фич, появившихся недавно, стоит сверять с документацией.

**Оценка: 9/10**

## Symfony

Знаю компонентную структуру, DI-контейнер, event dispatcher, console-команды, HTTP-клиента, testing-инструментарий, config-систему. Помогаю строить проекты и решать типовые задачи.
- ⚠️ Точные версии, новые синтаксические константы и детали фреймворка, меняющиеся между релизами, стоит проверять.

**Оценка: 8/10**

## Doctrine

Хорошо понимаю сущности, mapping, DQL, relations, lifecycle events, repository-паттерн. Помогаю с запросами и проектированием схемы.
- ⚠️ Тонкие грани (lazy-loading магия, cache-стратегии, миграции в edge-кейсах) требуют проверки.

**Оценка: 8/10**

## PostgreSQL

Разбираюсь с типами, индексами, транзакциями, изоляцией, подзапросами, оконными функциями, оптимизацией запросов.
- ⚠️ Специфичные фичи (extensions, partial indexes, partitioning-нюансы) стоит уточнять по версии и сценарию.

**Оценка: 8/10**

## DDD

Понимаю концепции: entities, value objects, aggregates, domain services, repositories, domain events, модули. Помогаю моделировать домен и разграничивать слои.
- ⚠️ Практика применения в реальных проектах — это область, где лучше совещаться с опытными разработчиками.

**Оценка: 7/10**

## Чистая архитектура

Разбираю принципы: разделение слоёв, dependency rule, инверсия зависимостей, изоляцию бизнес-правил. Помогаю с планированием структуры.
- ⚠️ Практическая балансировка «теории vs реалии проектов» — там, где стоит опираться на опыт команды.

**Оценка: 7/10**

## Тестирование

Пишу unit-/integration-тесты, работаю с PHPUnit, моками, фейками, TDD-подходом.
- ⚠️ Специфика фреймворков и фич тестирования со временем меняется — стоит сверять.

**Оценка: 8/10**

## Git

Свободно владею ветками, merge/rebase, cherry-pick, stash, tag, разрешенияming конфликтов, типовыми workflow.
- ⚠️ Редкие/продвинутые сценарии (interactive rebase сложной истории) стоит перепроверять.

**Оценка: 8/10**

---

Хочешь, перейдём к делу? Можем разобрать конкретную задачу, спроектировать структуру проекта или проверить твой код. С чего начнём?

> ⏱ 8.6s · prompt 89 tok · output 874 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

# Ключевые фичи PHP 8.x для чистого и надёжного кода

Вот что я считаю действительно меняющим игру — с примерами.

## 1. Readonly properties (8.1)

Защищают неизменяемые данные от случайного изменения — основа value objects.

```php
final readonly class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
    ) {}
}
```

```php
$data = new User('ann', 'admin');
$data->role = 'guest'; // Fatal: cannot modify readonly property
```

## 2. Enums (8.1)

Заменяют магические строки и константы на типобезопасные конструкции.

```php
enum Role: string
{
    case Guest = 'guest';
    case User = 'user';
    case Admin = 'admin';

    public function hasAccess(string $action): bool
    {
        return match ($this) {
            self::Admin => true,
            self::User  => $action === 'read',
            self::Guest => false,
        };
    }
}
```

## 3. Named arguments (8.0)

Убирают «позиционную путаницу» в публичных API.

```php
// Было: непонятно, что за чем идёт
$dateFormat(new DateTime(), 2, false, 'ru_RU');

// Стало: намеренно и безопасно
$dateFormat(new DateTime(),
    weekday: 2,
    year: false,
    locale: 'ru_RU');
```

## 4. First-class callable syntax (8.1)

Убирает громоздкую конструкцию `[$obj, 'method']`.

```php
array_map(fn($x) => $x * 2, $numbers);
// можно проще:
array_map(...[$obj, 'double']);
```

## 5. Pure intersection types (8.2)

Позволяют комбинировать интерфейсы для строгой типизации.

```php
interface HasLogger { public function setLogger(Logger $l): void; }
interface Validated { public function validate(): void; }

final class Service implements HasLogger & Validated
{
    public function __construct(private Logger $logger) {}
}
```

## 6. Stricter types (8.0+)

Критично важная для надёжности категория — падает там, где раньше было тихо.

```php
// TypeError вместо silent приведения
strlen([]); // Fatal: Argument #1 must be string

// Ковариантность/контравариантность типов
class Animal { public function feed(Animal $food): void {} }
class Dog extends Animal { public function feed(Dog $food): void {} } // OK
```

## 7. Constructor property promotion

Краткость без потери читаемости.

```php
final class Point
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {}
}
```

## 8. Enums + Match + Named args: мощная комбинация

```php
enum Status: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает',
            self::Paid    => 'Оплачено',
            self::Failed  => 'Ошибка',
        };
    }
}

// Красиво и типобезопасно
$order->status->label(); // 'Оплачено'
```

---

## Что я бы вынес как главное

**Приоритет №1 для надёжности — stricter types.** Именно они предотвращают тонны багов на стыке типов.

**Приоритет №1 для чистоты — readonly props + enums.** Они буквально заставляют доменную модель соответствовать реальности: money неизменяем, role не может быть произвольной строкой.

Хочешь, разберу подробнее какую-то конкретную фичу или покажу, как их применять в связке с Doctrine/Symfony?

> ⏱ 3.4s · prompt 1020 tok · output 944 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

# Система типов в PHP: от мягкости к строгости

Разберу системно — с примерами «спасает» и «подставляет».

## Что даёт strict_types

Это **директива на уровне файла**, а не глобальная настройка. Ключевой нюанс: она влияет только на **приведение типов аргументов** при вызове функций/методов. Внутри тела функции типы не меняются.

```php
<?php
declare(strict_types=1);

function sum(int $a, int $b): int
{
    return $a + $b;
}

sum(1.5, 2.5); // TypeError: Argument #1 ($a) must be of type int
```

Без директивы тот же код **тихо** перевёл бы `1.5` в `1`.

### ⚠️ Ловушка: директива не наследуется

```php
// file_a.php — declare(strict_types=1)
sum(1.5, 2.5); // TypeError

// Но if you include/require другой файл,
// в нём strict_types по умолчанию OFF
```

Вызов `declare(strict_types=1)` в одном файле **не влияет** на другие. Правило «включается для текущего файла, только если файл загружается напрямую».

## Как ведут себя приведения

PHP делит приведения на **мягкие** (loose, `==`) и **строгие** (strict, `===`).

```php
// Loose comparison — источник багов №1
0 == 'abc';      // true
'' == 0;         // true
'NaN' == 'abc';  // true (оба не число → сравниваются как строки)

// Strict comparison — предсказуемо
0 === 'abc';     // false
'' === 0;        // false
```

### Таблица того, что «тихо» теряется

```php
intval('12abc');  // 12  (а не 0 и не ошибка!)
intval('abc');    // 0

(float) '1,5';    // 1.0 (запятая не десятичный разделитель)
(float) '1.5';    // 1.5

(int) [];         // 0
(int) '  5  ';    // 5 (пробелы игнорируются)
```

## Ловушки, которые ловили почти все

### 1. Hexadecimal в numeric string

```php
intval('0x10');   // 16 — да, PHP парсит hex
intval('010');    // 8! — OCTAL не ten!
```

### 2. Comparison weirdness

```php
'1e3' == 1000;    // true (scientific notation!)
'1abc' == 1;      // false — '1abc' не numeric string
```

### 3. Boolean coercion

```php
(bool) '';        // false
(bool) '0';       // false  ← тут особенно часто ловят
(bool) '00';      // true  ← '00' — это же не '0'
```

## Где типизация спасает

### Пример 1: Деньги без float

```php
// ПЛОХО: float для денег
$price = 19.99;
$total = $price * 3; // 59.97000000000001 — floating point!

// ХОРОШО: value object
final readonly class Money
{
    public function __construct(
        public readonly int $cents,
        public readonly string $currency,
    ) {}

    public function times(int $n): self
    {
        return new self($this->cents * $n, $this->currency);
    }
}

new Money(1999, 'USD')->times(3); // 5997 cents — ровно
```

### Пример 2: Enum вместо магических строк

```php
// ПЛОХО
function send(string $channel): void {
    if ($channel === 'email') { /* ... */ }
}
// Ошибка в названии канала = баг, который не компилируется

// ХОРОШО
enum Channel {
    case Email;
    case Sms;
}

function send(Channel $channel): void {
    match ($channel) {
        Channel::Email => /* ... */,
        Channel::Sms   => /* ... */,
    };
}
// Нельзя передать 'phone' — ошибка на этапе разработки
```

### Пример 3: Intersection types для строгой зависимости

```php
interface Loggable { public function log(string $m): void; }
interface Validated { public function validate(): void; }

// Точно знаем: объект умеет и логировать, и валидировать
function process(Loggable & Validated $item): void {
    $item->validate();
    $item->log('processed');
}
```

## Где типизация удивляет

### Удивление 1: Mixed type

```php
function anything(mixed $input): void { /* ... */ }

anything(42);  // OK
anything('str'); // OK
anything(null);  // OK — mixed включает null!
```

### Удивление 2: Enums не сравниваются как строки

```php
enum Role: string {
    case Admin = 'admin';
}

Role::Admin === 'admin';   // false!
Role::Admin->value === 'admin'; // true — надо брать ->value
```

### Удивление 3: Readonly + match

```php
enum Direction: string {
    case North = 'n';
    case South = 's';
}

$d = Direction::from('n'); // North
$d = Direction::from('x'); // Error: invalid case
```

---

## Резюме: что взять на вооружение

1. **`declare(strict_types=1)` в каждом файле** — но помни, что он не наследуется.
2. **`===` вместо `==`** — всегда, где можно.
3. **Numeric string — опасная зона** — парсь через `filter_var($s, FILTER_VALIDATE_INT)`.
4. **Value objects + enums** — выноси доменную логику из raw-типов.
5. **Intersection types** — для строгих контрактов.

Хочешь, разберу подробнее какую-то конкретную тему — например, `strict_types` в контексте Doctrine, или как enum-ы интегрируются с серизацией?

> ⏱ 5.1s · prompt 2026 tok · output 1482 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

# Проектирование работы с исключениями в PHP

Разберу концептуально — иерархии, оборачивание, контракт и антипаттерны.

## Иерарпия исключений: создавай свою ветку

Не смешивай доменные исключения с системными. Создай корневой класс и наследуй от него.

```php
namespace App\Exception;

// Базовый маркер — все твои исключения
abstract class DomainException extends \Exception {}

// Конкретные
class EntityNotFoundException extends DomainException {}
class InvalidStateException extends DomainException {}
class ValidationException extends DomainException {}
class UnauthorizedException extends DomainException {}
```

### Почему это важно

- **Ловля по иерархии** — можно catch'ить `DomainException` и поймать все подтипы.
- **Отделение от фреймворка** — твои исключения не путаются с `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` и т.п.
- **Читаемость контракта** — `@throws` в документации показывает бизнес-логику.

## Оберачивание: сохрани контекст

Главная цель — не потерять цепочку причин.

```php
class OrderRepository
{
    public function find(string $id): Order
    {
        try {
            $row = $this->db->fetch($id);
        } catch (\PDOException $e) {
            // Оборачиваем: добавляем доменный контекст
            throw new EntityNotFoundException(
                "Order {$id} not found", 0, $e
            );
        }
    }
}
```

### Ключевой приём: `$previous`

```php
throw new ValidationException(
    'Invalid email format',
    0,
    $previous // ← оригинальная причина в \Throwable::getPrevious()
);
```

Через `$e->getPrevious()` можно построить полную цепочку:

```
Exception: Invalid email format
  └─ Caused by: SQLSTATE[23000]: ...
       └─ Caused by: connection timeout
```

## Что считается контрактом метода

Контракт = то, что **гарантированно** произойдёт или вернёт метод.

**Включай в контракт:**
- Возвращаемые значения (включая `null`)
- Конкретные типы исключений, которые метод **намеренно** бросает
- Side-эффекты (изменение БД, сетевые запросы)

**Не включай:**
- Внутренние детали реализации
- Исключения из библиотек, которые ты можешь обернуть

```php
class OrderService
{
    /**
     * @throws EntityNotFoundException  — заказ не найден
     * @throws InvalidOrderException   — заказ в некорректном состоянии
     *
     * @return Order
     */
    public function cancel(string $id): Order
    {
        // ...
    }
}
```

## Антипаттерны

### 1. «Глобальный catch-all»

```php
// ПЛОХО
try {
    $service->doSomething();
} catch (\Throwable $e) {
    // Логируем, но теряем контекст
    error_log($e->getMessage());
}
```

**Проблема:** теряется тип ошибки, нельзя различать сценарии обработки.

**Лучше:** ловить конкретные типы, обрабатывать по-разному.

### 2. Пожирание исключений

```php
// ПХОРО
try {
    // ...
} catch (\Exception $e) {
    // Ничего не делаем — ошибка исчезает
}
```

### 3. Catch перед throw (бессмысленный)

```php
// ПЛОХО
try {
    $data = parse($input);
} catch (\InvalidArgumentException $e) {
    throw $e; // Просто перекидываем то же самое
}
```

### 4. Исключения для flow control

```php
// ПЛОХО — это не исключительная ситуация
try {
    $item = $this->find($id);
} catch (ItemNotFoundException $e) {
    // Просто не делаем ничего
}
```

**Лучше:** использовать `?Item` и проверять на `null`, или `Result`-объекты.

### 5. Логирование в неправильном слое

```php
// ПЛОХО — лог в репозитории, где ошибка ещё «обрабатываема»
class OrderRepository {
    public function find(string $id): ?Order {
        try { ... }
        catch (\Exception $e) {
            logger()->error(...); // Слишком низко
        }
    }
}
```

**Лучше:** логировать на уровне, где ошибка становится необработаемой (usually application boundary).

## Рекомендации по слоистости

```
┌─────────────────────────────────────────┐
│ Presentation Layer  →  HTTP exception     │
│ (Controllers, Form)       handlers        │
├─────────────────────────────────────────┤
│ Application Layer →  DomainException     │
│ (Services)              (business)        │
├─────────────────────────────────────────┤
│ Domain Layer →  ValidationException      │
│ (Entities, ValueObjects)                │
├─────────────────────────────────────────┤
│ Infrastructure →  RepositoryException    │
│ (DB, ORM)        (technical wrapping)    │
└─────────────────────────────────────────┘
```

## Практический шаблон

```php
class OrderService
{
    /**
     * @throws EntityNotFoundException
     * @throws InvalidOrderException
     */
    public function cancel(string $id): void
    {
        try {
            $order = $this->orders->find($id);
            $order->cancel();
            $this->orders->save($order);
        } catch (\PDOException $e) {
            // Оборачиваем техническую ошибку в доменную
            throw new InvalidOrderException(
                "Could not cancel order {$id}", 0, $e
            );
        }
    }
}
```

---

Хочешь, разберу как это интегрируется с Symfony ExceptionListener или как строить HTTP-ответы из доменных исключений?

> ⏱ 6.9s · prompt 3567 tok · output 1435 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

# Чаще всего нарушается SRP (Single Responsibility Principle)

Из своего «опыта» анализа кода — **SRP** нарушается систематически. Причины понятные:

## Почему именно SRP

1. **Классы-боги (God objects)** — удобно «сложить всё в один класс», пока проект маленький
2. **Функциональные «комбайны»** — методы на 100+ строк, делающие сразу всё
3. **Отсутствие доменного моделирования** — бизнес-правила живут в generic-сервисах
4. **Быстрая разработка** — «потом рефакторить» обычно не переделывают

---

# Типовой пример нарушения

```php
class OrderService
{
    public function processPayment($order)
    {
        // 1. Валидация
        if ($order->amount <= 0) {
            throw new \InvalidArgumentException('Invalid amount');
        }
        
        // 2. Расчет налогов
        $tax = $order->amount * 0.2;
        $order->tax = $tax;
        
        // 3. Обработка платежа
        $payment = new PaymentGateway();
        $payment->charge($order->card, $order->amount + $tax);
        
        // 4. Отправка email
        $email = new \SMTP();
        $email->send($order->customer_email, 'Payment successful');
        
        // 5. Логирование
        error_log("Order {$order->id} processed");
        
        // 6. Обновление库存 (inventory)
        $inventory = new InventoryRepository();
        foreach ($order->items as $item) {
            $inventory->decrease($item->sku, $item->quantity);
        }
    }
}
```

### Что нарушено

Один метод делает **6 различных вещей** с 6 разными причинами изменения:
- Валидация → ошибка валидации
- Налоги → бизнес-логика расчета
- Платеж → внешняя интеграция
- Email → presentation/notification
- Логирование → infrastructure
- Инвентарь → отдельная бизнес-логика

---

# Рефакторинг

```php
// Отдельные классы для ответствы
class OrderProcessor
{
    public function __construct(
        private PaymentService $payments,
        private TaxCalculator $taxes,
        private InventoryManager $inventory,
        private OrderRepository $orders,
        private NotificationService $notifications,
    ) {}

    public function process(Order $order): void
    {
        // Оркестрация — не реализация
        $this->validate($order);
        $this->charge($order);
        $this->updateInventory($order);
        $this->notify($order);
    }
    
    private function validate(Order $order): void
    {
        if ($order->amount <= 0) {
            throw new OrderValidationException('Amount must be positive');
        }
    }
    
    private function charge(Order $order): void
    {
        $total = $this->taxes->calculate($order);
        $this->payments->charge($order->card, $total);
        $order->markAsPaid();
        $this->orders->save($order);
    }
    
    private function updateInventory(Order $order): void
    {
        $this->inventory->decrease($order->items);
    }
    
    private function notify(Order $order): void
    {
        $this->notifications->send($order->customer, 'Payment successful');
    }
}
```

### Что изменилось

**Каждый класс — одна ответственность:**

```php
class TaxCalculator {
    public function calculate(Order $order): float { /* только налоги */ }
}

class PaymentService {
    public function charge(Card $card, float $amount): void { /* только платежи */ }
}

class InventoryManager {
    public function decrease(iterable $items): void { /* только инвентарь */ }
}

class NotificationService {
    public function send(Customer $c, string $msg): void { /* только уведомления */ }
}
```

---

# Другие частые нарушения SOLID

## O (Open/Closed)

```php
// ПЛОХО — расширение через if-цепочку
function calculateFee(Charge $charge): float {
    if ($charge instanceof CreditCard) {
        return $charge->amount * 0.029;
    }
    if ($charge instanceof PayPal) {
        return $charge->amount * 0.035;
    }
    // Приходится править метод при добавлении способа
}

// ХОРОШО — расширение через добавление
interface FeeCalculator {
    public function calculate(Charge $charge): float;
}
```

## L (Liskov Substitution)

```php
// ПЛОХО — подкласс ломает контракт родителя
class Notification {
    public function send(): void { /* ... */ }
}

class EmailNotification extends Notification {
    // Переопределяет с другими требованиями — нарушает L
    public function send(): void {
        // Теперь требует SMTP-соединение, которого нет у родителя
    }
}

// ХОРОШО — через интерфейсы
interface Notifier {
    public function send(): void;
}
```

## I (Interface Segregation)

```php
// ПЛОХО — один огромный интерфейс
interface Animal {
    public function walk();
    public function fly();  // для земных животных бесполезно
    public function swim();
}

// ХОРОШО — специализированные интерфейсы
interface Walker { public function walk(); }
interface Flyer { public function fly(); }
interface Swimmer { public function swim(); }
```

## D (Dependency Inversion)

```php
// ПЛОХО — прямая зависимость от реализации
class OrderService {
    private MySQLOrderRepository $repo; // жёсткая зависимость
    
    public function __construct() {
        $this->repo = new MySQLOrderRepository();
    }
}

// ХОРОШО — зависимость от абстракции
class OrderService {
    public function __construct(
        private OrderRepository $repo // интерфейс!
    ) {}
}
```

---

# Практический вывод

**SRP нарушается чаще всего**, потому что:
- Дает быструю «работоспособность» на старте
- Сложно увидеть нарушение, пока класс не разрастется
- Требует дисциплины и доменного моделирования

**Рекомендация:** следуй правилу «одна причина для изменения» — если у класса/метода можно назвать 2+ разные причины для правок, значит ответственностей больше одной.

Хочешь, разберу как выявлять нарушения SRP автоматически (например, через статический анализ) или как строить границы ответственностей в DDD-проектах?

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

# Да, SOLID — это не заповедники, а рекомендации

SOLID описывает **практики, выработанные опытом**, а не физические законы. Их нарушение оправдано, когда **затраты на соблюдение превышают выгоду**. Хороший инженер знает, *когда* нарушить принцип, не хуже, чем *как* его применить.

---

## Ключевая мысль

> **Принципы помогают, когда цена их нарушения меньше затрат на их соблюдение.**

Когда мы пишем «следуй SOLID обязательно», мы можем случайно создать **over-engineering** — систему, где абстракции весят больше, чем решаемая задача.

---

## Оправданные нарушения с примерами

### 1. SRP ради перформанса (hot path)

```php
// Придерживаемся SRP — каждый getter в отдельном методе
class Order {
    public function getSubtotal(): float {
        return $this->calculateSubtotal();
    }
    public function getTax(): float {
        return $this->calculateTax();
    }
    public function getTotal(): float {
        return $this->calculateTotal(); // каждый раз пересчитывает всё!
    }
}

// Нарушаем SRP ради перформанса в hot path
class Order {
    private array $cache = [];
    
    public function getTotal(): float {
        // Кэшируем весь расчет в одном методе
        // Мешаем «ответственности» ради скорости
        if (!isset($this->cache['total'])) {
            $this->cache['total'] = $this->subtotal + $this->tax;
        }
        return $this->cache['total'];
    }
}
```

**Обоснование:** Если `getTotal()` вызывается в цикле на 100k итераций — каждый отдельный метод = лишние вызовы. Здесь локальное нарушение SRP окупается.

---

### 2. Composition over Inheritance ради скорости

```php
// Придерживаемся принципа — композиция
class Car {
    private Engine $engine; // гибко, но каждый раз new
    private Transmission $transmission;
}

// Нарушаем ради перформанса — наследование в hot path
class FastCar extends Car {
    // Прямое наследование, без инстанцирования зависимостей
    // Менее «чистко» с точки зрения композиции, но быстрее
}
```

**Обоснование:** В критичных к производительности местах (game loop, real-time) прямое наследование/инстанцирование может быть оправдано.

---

### 3. DIP ради удобства DI-контейнера

```php
// Придерживаемся DIP — зависимости через конструктор
class OrderService {
    public function __construct(
        private OrderRepository $repo
    ) {}
}

// «Нарушаем» DIP — берем контейнер напрямую
class OrderController {
    public function __construct(private Container $container) {}
    
    public function show($id) {
        $service = $this->container->get(OrderService::class);
        // Прямая зависимость от реализации через контейнер
    }
}
```

**Обоснование:** DI-контейнеры часто «нарушают» DIP, позволяя резолвить зависимости по строке. Это компромисс между чистотой и практичностью.

---

### 4. ISP ради простоты API

```php
// Придерживаемся ISP — много маленьких интерфейсов
interface Flyer { public function fly(); }
interface Swimmer { public function swim(); }

// Нарушаем ISP — один интерфейс, но проще
interface Animal {
    public function move(): void; // один метод для всех
}
```

**Обоснование:** Если интерфейс используется только в одном месте и не планирует расширение — дробить его нецелесообразно.

---

### 5. LSP ради concrete factory

```php
// Придерживаемся LSP — полиморфизм
abstract class Shape { abstract public function area(): float; }

// Нарушаем LSP ради простоты factory
class ShapeFactory {
    public static function create(string $type, ...$args): Shape {
        switch ($type) {
            case 'circle': return new Circle($args[0]);
            case 'square': return new Square($args[0]);
            // Hard-coded switch — нарушает открытость
        }
    }
}
```

**Обоснование:** Если набор типов фиксирован и стабилен, hard-coded switch может быть проще и быстрее, чем реестр фабрик.

---

## Общие критерии «можно нарушать»

| Критерий | Когда нарушение оправдано |
|----------|---------------------------|
| **Цена** | Затраты на соблюдение > выгоды |
| **Сложность** | Проект слишком мал для абстракций |
| **Перформанс** | Hot path, где каждый call критичен |
| **Скорость разработки** | MVP, где важна скорость, а не масштабируемость |
| **Стабильность** | Типы/логика не изменятся в обозримом будущем |

---

## Золотое правило

> **Нарушай принципы осознанно, а не по лени.**

Разница между *оправданным* нарушением и *плохим* кодом — в **причине**:
- **Оправдано:** «Я нарушаю SRP, потому что перформанс в hot path критичен, и вот доказательства»
- **Плохо:** «Я нарушаю SRP, потому что лень выносить логику»

---

Хочешь, разберу как **измерять**, стоит ли нарушать принцип (например, через cost-benefit анализ абстракций) или как **обосновывать нарушения** в code review?

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

# OCD вне наследования: 5 альтернативных способов

Отличный вопрос — наследование действительно **не единственный** путь к открытости для расширения. Разберу основные.

---

## 1. Стратегии (Strategy Pattern)

Замени иерархию классов **встраиваемыми стратегиями**.

```php
// Было: иерархия через наследование
abstract class PaymentProcessor {
    abstract public function pay(float $amount): void;
}
class CreditCardProcessor extends PaymentProcessor { ... }
class PayPalProcessor extends PaymentProcessor { ... }

// Стало: стратегии
interface PaymentStrategy {
    public function pay(float $amount): void;
}

class CreditCardStrategy implements PaymentStrategy { ... }
class PayPalStrategy implements PaymentStrategy { ... }

class PaymentService {
    public function __construct(private PaymentStrategy $strategy) {}
    
    public function checkout(float $amount): void {
        $this->strategy->pay($amount); // подставляем разную стратегию
    }
}
```

**Плюс:** можно менять поведение **в рантайме**, а не фиксировать наследованием.

---

## 2. Хендлеры / Обработчики событий

Определи **единый интерфейс обработчика** и регистрируй их динамически.

```php
interface EventHandler {
    public function handle(Event $event): void;
}

class OrderCreatedHandler implements EventHandler { ... }
class OrderCancelledHandler implements EventHandler { ... }

class EventDispatcher {
    /** @var EventHandler[] */
    private array $handlers = [];
    
    public function on(string $event, EventHandler $handler): void {
        $this->handlers[$event][] = $handler; // регистрация в рантайме
    }
    
    public function dispatch(Event $event): void {
        foreach ($this->handlers[$event->name()] ?? [] as $handler) {
            $handler->handle($event);
        }
    }
}
```

**Плюс:** новые обработчики добавляются **без изменения** диспетчера.

---

## 3. Такси / Callbacks (функции как данные)

Используй **замыкания** вместо классов.

```php
// Быстрая настройка поведения через callback
function calculateFee(float $amount, callable $rateCalculator): float {
    return $amount * $rateCalculator();
}

// Разное поведение без новых классов
calculateFee(100, fn() => 0.029);   // кредитка
calculateFee(100, fn() => 0.035);   // paypal
```

**Плюс:** минимум boilerplate, идеальное для простых случаев.

---

## 4. Декларативная конфигурация + reflection

Определи **единый формат данных**, а логику выстрой поверх него.

```php
// Данные вместо иерархии
enum DiscountType: string {
    case Percent = 'percent';
    case Fixed = 'fixed';
}

class DiscountCalculator {
    public function calculate(float $amount, DiscountType $type, float $value): float {
        return match ($type) {
            DiscountType::Percent => $amount * (1 - $value / 100),
            DiscountType::Fixed => max(0, $amount - $value),
        };
    }
}
```

**Плюс:** новое правило = **новая запись в конфиге**, а не новый класс.

---

## 5. Таблицы/Реестры (Registry)

Храни **наборы объектов** и обращайся по ключу.

```php
class NotificationChannels {
    private array $channels = [];
    
    public function register(string $name, Notifier $notifier): void {
        $this->channels[$name] = $notifier;
    }
    
    public function send(string $name, string $message): void {
        $this->channels[$name]
            ->send($message); // динамический выбор
    }
}
```

**Плюс:** можно **динамически расширять** систему без переписывания кода.

---

## Сравнительная таблица

| Способ | Когда использовать | Плюсы |
|--------|-------------------|-------|
| **Стратегии** | Поведение меняется в рантайме | Гибкость, тестируемость |
| **Хендлеры** | Множественная обработка одного события | Отвязанность, масштабируемость |
| **Callbacks** | Простые действия без состояния | Минимализм, читаемость |
| **Конфигурация** | Правило — это данные | Расширение без кода |
| **Реестры** | Динамическое добавление компонентов | Гибкость сборки |

---

## Ключевой вывод

**OCD достигается через абстракцию над поведением**, а не обязательно через иерархию. Наследование — лишь один из инструментов. Часто стратегии и хендлеры **предпочтительнее**, потому что:
- Позволяют менять поведение в рантиме
- Упрощают тестирование
- Избегают жёсткой связанности классов

Хочешь, разберу как **выбирать** между этими подходами для конкретной задачи?

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

# SRP применяется не только к классам — это принцип ответственностей в целом

Отличный вопрос! SRP (Single Responsibility Principle) — это **принцип ответственных**, а не только классов. Разберу уровни применения.

---

## SRP на разных уровнях

### 1. Функции/Методы

Каждый метод должен делать **одну вещь** и делать её хорошо.

```php
// Нарушение: метод делает три разных дела
function processOrder(array $order): void {
    // 1. Валидация
    if (!$this->isValid($order)) { /* ... */ }
    
    // 2. Расчет стоимости
    $total = $this->calculateTotal($order);
    
    // 3. Отправка письма
    $this->email->send($order['customer'], 'Order processed');
}

// Соблюдение: каждый метод — одна ответственность
function validateOrder(array $order): bool { ... }
function calculateOrderTotal(array $order): float { ... }
function notifyOrderProcessed(array $order): void { ... }
```

---

### 2. Слои архитектуры

**Presentation layer** отвечает за UI, **business layer** — за правила, **data layer** — за доступ к данным.

```php
// Нарушение: контроллер знает про SQL
class UserController {
    public function create(Request $request): Response {
        $db = new PDO('mysql:host=localhost');
        $db->exec("INSERT INTO users ..."); // бизнес-логика + доступ к данным
    }
}

// Соблюдение: разделение ответственностей
class UserController {
    public function __construct(private UserRepository $users) {}
    
    public function create(Request $request): Response {
        $user = new User($request->input('name'));
        $this->users->save($user); // только оркестрация
    }
}
```

---

### 3. Модули/Компоненты

**Frontend** отвечает за UI, **backend** за API, **worker** за фоновые задачи.

```
frontend/        ← только UI/UX
backend/         ← только бизнес-логика и API
worker/          ← только фоновая обработка
```

---

### 4. Микросервисы

Каждый сервис имеет **единую бизнес-функцию**:

```
auth-service     ← только аутентификация
payment-service  ← только платежи
inventory-service ← только инвентарь
```

---

## «Причина для изменения» как критерий

**Главный тест на SRP:** «Сколько у объекта **причин для изменения**?»

- **1 причина** = правильная ответственность
- **2+ причин** = нарушение

```php
class ReportGenerator {
    // 1. Форматирование данных → изменение верстки
    // 2. Расчет статистики → изменение алгоритма
    // 3. Экспорт в PDF → изменение формата вывода
    // → 3 причины для изменения = нарушение SRP
}
```

---

## Практический вывод

**SRP применим ко всему, что имеет «ответственность»:**
- Функции
- Классы
- Слои
- Модули
- Сервисы

**Ключевая идея:** «Одна причина для изменения» — это критерий, а не привязка к классам.

Хочешь, разберу как применять SRP к **функциям** или **слоям архитектуры** подробнее?

> ⏱ 5.6s · prompt 5051 tok · output 1566 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

# Контейнер внедрения зависимостей в Symfony: от кода к сервису

Разберу жизненный цикл сервиса — от описания до получения экземпляра.

---

## Общая архитектура

```
┌─────────────────────────────────────────┐
│  app.php (request)                        │
│    ↓                                       │
│  ServiceContainer → get('service_name')   │
│    ↓                                       │
│  Compiled Container (OPcode cache)         │
│    ↓                                       │
│  Instantiated service (singleton)          │
└─────────────────────────────────────────┘
```

---

## Этап 1: Описание сервисов

Сервисы описаны в `config/services.php`:

```php
// config/services.php
use App\Service\OrderService;

services
    ->default()->public(false)
    ->register(OrderService::class)
    ->arguments([
        service('app.order_repository'),
        service('app.payment_gateway'),
    ]);
```

Или через атрибуты (PHP 8):

```php
#[AsSharedService]
final class OrderService
{
    public function __construct(
        private OrderRepository $repository,
        private PaymentGateway $gateway
    ) {}
}
```

---

## Этап 2: Компиляция контейнера

**При первом запросе** (или после изменения конфигов) Symfony **компирует** контейнер.

### Что происходит при компиляции:

```php
// Symfony\Component\DependencyInjection\Compiler\PassConfig
$passConfig = new PassConfig();

// Pass, выполняемые в порядке:
1. MergeExtensionConfigurationPass
2. ResolveEnvironmentParametersPass
3. RegisterPass
4. ... (много pass) ...
5. RemoveUnusedDefinitionsPass
6. InlineServiceDefinitionsPass  ← встраивает простые сервисы
7. ...
8. RemovePrivateServicesPass
```

### Ключевые фазы компиляции:

| Pass | Что делает |
|------|-----------|
| **InlineDefinitionsPass** | Встраивает "встроенные" сервисы прямо в код |
| **RemoveUnusedDefinitionsPass** | Удаляет неиспользуемые сервисы |
| **RemovePrivateServicesPass** | Удаляет приватные сервисы, которые не запрашиваются |
| **ResolveTaggedServiceDefinePass** | Разрешает тегированные сервисы |

---

## Этап 3: Autowiring

**Autowiring** — автоматическое внедрение зависимостей по типам.

```php
class OrderService
{
    public function __construct(
        private OrderRepository $repository,   // ← autowired по типу
        private PaymentGateway $gateway         // ← autowired по типу
    ) {}
}
```

### Как это работает:

1. **Reflection** читает параметры конструктора
2. **По имени класса** ищет сервис в контейнере
3. **Рекурсивно** резолвит зависимости

```php
// Symfony\Component\DependencyInjection\Loader\ClosureLoader
// Генерирует код:
function (Container $container) {
    $services = $container->get('...');
    return new $class(
        $container->get('App\Repository\OrderRepository'),
        $container->get('App\Service\PaymentGateway')
    );
}
```

---

## Этап 4: Генерация кода

Компилятор генерирует **PHP-код** вместо динамического резолва:

```php
// var/cache/prod/AppProdContainerCompiled.php (примерно так)
class AppProdContainer extends ...
{
    protected function getService_$this$001_OrderService()
    {
        // Встроенный сервис — создаётся напрямую
        $a = new \App\Service\OrderService(
            $this->get('AppRepositoryOrderRepository'),
            $this->get('AppServicePaymentGateway')
        );
        return $a;
    }
}
```

---

## Этап 5: Первый запрос сервиса

```php
$container = $this->getContainer(); // AppProdContainer

// Первый вызов:
$orderService = $container->get('App\Service\OrderService');
// ↓
// 1. getService_$this$001_OrderService()
// 2. Проверка: уже создан? (singleton)
// 3. Если нет — инстанцировать
// 4. Кэшировать в $this->services
```

### Оптимизации:

- **Singleton** — один экземпляр на весь жизненный цикл
- **OPcode cache** (OPcache) — скомпилированный контейнер не перекомпируется
- **Встроенные сервисы** — встроены в код, без динамического резолва

---

## Жизненный цикл сервиса

```
Request
   ↓
Boot kernel
   ↓
Compile container (если нужно)
   ↓
Render compiled container to PHP
   ↓
Cache to var/cache/
   ↓
Get service → instantiate → cache instance → return
```

---

## Практические советы

### Избегай "боgow" сервисов

```php
// Не хорошо: слишком много зависимостей
class OrderService {
    public function __construct(
        private $dep1, private $dep2, private $dep3,
        private $dep4, private $dep5  // >5 — признак нарушения SRP
    ) {}
}
```

### Используй tags для расширения

```php
// config/services.php
services->load('App\\', '../src/');

// Маркируем обработчики
#[AsTaggedService('app.event_handler')]
class OrderHandler implements EventHandler { ... }
```

---

## Вывод

**Компиляция** превращает декларативное описание в **оптимизированный PHP-код**.
**Autowiring** избавляет от ручного описания зависимостей.
**Кэширование** делает первый запрос быстым.

Хочешь, разберу **конкретные Pass'ы** компиляции подробнее или как **отлаживать** проблемы с контейнером?

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

# Разруливание циклических зависимостей в Symfony

Циклические зависимости — это когда **сервис A зависит от B, а B зависит от A**. Symfony **не может** их резолвить — падает с ошибкой. Разберу способы решения.

---

## Что говорит Symfony

```
LogicException:
You have circular dependency between service "app.service_a"
and "app.service_b". Consider using interfaces or tags.
```

---

## Способы разруливания

### 1. Измени архитектуру — устрани зависимость

**Задача:** найти, *почему* нужна циклическая зависимость.

```php
// Было: циклическая зависимость
class OrderService {
    public function __construct(private PaymentService $payments) {}
}

class PaymentService {
    public function __construct(private OrderService $orders) {}
}
// ↑ OrderService → PaymentService → OrderService

// Решение: вынеси общую логику
class OrderService {
    public function __construct(
        private PaymentService $payments,
        private OrderRepository $orders  // ← прямая зависимость от репозитория
    ) {}
}
```

---

### 2. EventDispatcher — отвязка через события

**Задача:** сервис A должен "узнать" о результате работы B, без прямой зависимости.

```php
// Вместо прямой зависимости → событие
class PaymentService {
    public function __construct(
        private EventDispatcher $dispatcher
    ) {}
    
    public function process(Order $order): void {
        // ... обрабатываем платеж
        $this->dispatcher->dispatch(new PaymentProcessed($order));
    }
}

class OrderNotifier {
    public function __construct(private OrderRepository $orders) {}
    
    // Слушаем событие — нет циклической зависимости
    public function onPaymentProcessed(PaymentProcessed $event): void {
        $this->orders->markAsPaid($event->order());
    }
}
```

---

### 3. Interface/Abstract — через абстракцию

**Задача:** зависимость через интерфейс, не через конкретную реализацию.

```php
// Интерфейс
interface OrderProcessor {
    public function process(Order $order): void;
}

// A зависит от B через интерфейс
class PaymentProcessor implements OrderProcessor {
    public function __construct(private OrderProcessor $processor) {}
}

// B реализует интерфейс
class OrderProcessorImpl implements OrderProcessor {
    public function process(Order $order): void { /* ... */ }
}
```

---

### 4. Proxy/Delayed injection — отложенная инициализация

**Задача:** зависимость нужна не сразу, а лениво.

```php
class OrderService {
    private PaymentGateway $gateway;
    
    public function setGateway(PaymentGateway $gateway): void {
        $this->gateway = $gateway; // ручной setter injection
    }
    
    public function process(): void {
        $this->gateway->pay(); // только когда нужно
    }
}
```

---

### 5. Tagged services — через теги

**Задача:** динамическое добавление обработчиков.

```php
// services.php
services->load('App\\', '../src/')
    ->register()
    ->tags(['app.handler']);

// В коде
class HandlerRegistry {
    /** @var callable[] */
    private array $handlers = [];
    
    public function addHandler(callable $handler): void {
        $this->handlers[] = $handler; // регистрация через тег
    }
}
```

---

## Практический чек-лист

| Способ | Когда использовать |
|--------|-------------------|
| **Архитектура** | Всегда пробуй первым — найти корень |
| **EventDispatcher** | Нужно "узнать" о результате работы |
| **Interface** | Зависимость через абстракцию допустима |
| **Proxy** | Зависимость нужна лениво |
| **Tagged** | Динамическое добавление компонентов |

---

## Золотое правило

> **Циклическая зависимость — сигнал о нарушении архитектуры.**

Прежде чем "разруливать", спроси:
- Почему нужна эта зависимость?
- Можно ли вынести общую логику?
- Можно ли заменить на событие?

Хочешь, разберу **конкретный кейс** с твоим кодом или как **автоматически находить** циклические зависимости?

> ⏱ 5.0s · prompt 10046 tok · output 1362 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

# Асинхронная обработка в Symfony: 4 подхода

Разберу основные способы отправки письма и обновления индекса **после регистрации без замедления ответа**.

---

## Вариант 1: Messenger + Transport (рекомендуется)

**Symfony Messenger** — нативный асинхронный механизк.

```php
// app/src/Message/RegisterUser.php
final class RegisterUser
{
    public function __construct(
        private string $email,
        private string $name
    ) {}
}

// app/src/MessageHandler/SendWelcomeEmail.php
#[AsMessage]
class SendWelcomeEmail
{
    public function __construct(private MailerInterface $mailer) {}
    
    public function __invoke(SendWelcomeEmail $message): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($message->email)
            ->subject('Welcome!')
            ->text('Welcome to our platform');
        
        $this->mailer->send($email);
    }
}
```

### Конфигурация:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        failure_transport: failed
        transports:
            async:
                dsn: 'amqp://guest:guest@localhost:5672/%2f/vhost'
                options:
                    exchange:
                        name: notifications
            failed:
                dsn: 'doctrine://default?queue_name=failed'

        routing:
            'App\Message\SendWelcomeEmail': async
            'App\Message\ReindexUser': async
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Асинхронность без задержки ответа | Нужен broker (RabbitMQ) или Redis |
| Retry при ошибках | Сложнее отладка асинхронного кода |
| Отвязанность компонентов | Асинхронность — новая ось ошибок |

---

## Вариант 2: Пул фоновых задач (Queue)

**Redis Queue** или **RabbitMQ** для более сложного планирования.

```php
// app/src/Service/UserRegistrationService.php
class UserRegistrationService
{
    public function __construct(
        private RabbitMQ $queue,
        private UserRepository $users
    ) {}
    
    public function register(string $email, string $name): User
    {
        $user = $this->users->create($email, $name);
        
        // Отправляем в очередь
        $this->queue->publish('notifications', new SendWelcomeEmail($user));
        $this->queue->publish('search', new ReindexUser($user->id));
        
        return $user; // ответ без задержки
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Гибкое планирование | Нужна инфраструктура |
| Scale workers отдельно | Больше движущихся частей |
| Persistence очереди | Сложнее мониторинг |

---

## Вариант 3: Background worker (cron)

**Cron + worker** для периодической обработки.

```php
// app/console/Command/ProcessQueueCommand.php
#[Command('app:process-queue')]
class ProcessQueueCommand
{
    public function __construct(private QueueRepository $queue) {}
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while ($task = $this->queue->pop()) {
            $this->dispatch($task);
        }
        return Command::SUCCESS;
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Простота | Очередь в БД (нагрузка) |
| Нет дополнительной инфраструктуры | Задержка (зависит от cron) |
| Полный контроль | Нужно следить за производительностью |

---

## Вариант 4: Async в PHP (PHP 8.2+)

**Nanoware** или **Fibers** для лёгкой асинхронности.

```php
// app/src/Service/UserRegistrationService.php
use Swoole\Coroutine;

class UserRegistrationService
{
    public function register(string $email, string $name): User
    {
        $user = $this->users->create($email, $name);
        
        // Coroutine (лёгкий)
        Coroutine::create(function() use ($user) {
            $this->sendEmail($user);
            $this->reindex($user);
        });
        
        return $user; // ответ без задержки
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Лёгкость | Нужен PHP с coroutines |
| Высокая параллельность | Сложнее отладка |
| Мало инфраструктуры | Ограниченная поддержка |

---

## Сравнительная таблица

| Подход | Скорость ответа | Сложность | Производительность |
|--------|----------------|-----------|-------------------|
| **Messenger** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ |
| **Queue** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Worker/cron** | ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **Async/Coroutine** | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## Рекомендации

| Сценарий | Подход |
|----------|--------|
| **Простое приложение** | Messenger |
| **Высокая нагрузка** | Queue |
| **Ограниченные ресурсы** | Worker/cron |
| **Макс. производительность** | Async/Coroutine |

Хочешь, разберу **конкретную реализацию** Messenger с retry-механизмами или как **тестировать** асинхронный код?

> ⏱ 5.0s · prompt 12513 tok · output 1352 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

# Контроль доступа в Symfony: Authentication, Roles, Voters

Разберу иерархию контроля доступа от аутентификации до fine-grained прав.

---

## 1. Аутентификация (Authentication)

### Firewall — точка входа

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            lazy: true
            provider: app_users
            form_login:
                login_path: login
                check_path: login
            logout:
                path: logout
```

### Providers — откуда брать пользователей

```yaml
security:
    providers:
        app_users:
            entity:
                class: App\Entity\User
                property: email
```

### UserProvider — кастомная загрузка

```php
// app/src/Security/AppUserProvider.php
class AppUserProvider implements UserProviderInterface
{
    public function loadUserByUsername(string $username): UserInterface
    {
        // Кастомная логика загрузки
    }
    
    public function refreshUser(UserInterface $user): UserInterface
    {
        // Перезагрузка пользователя
    }
    
    public function supportsClass(string $class): bool
    {
        return true;
    }
}
```

---

## 2. Роли (Roles)

### Простая система ролей

```php
// app/src/Entity/User.php
class User implements UserInterface
{
    private string $role;
    
    public function getRoles(): array
    {
        return [$this->role]; // ROLE_USER, ROLE_ADMIN
    }
}
```

### ACL-права через roles

```php
// controller
#[IsGranted('ROLE_ADMIN')]
public function adminPanel(): Response
{
    // Доступно только ROLE_ADMIN
}
```

```php
// template
{% if is_granted('ROLE_ADMIN') %}
    <a href="/admin">Admin</a>
{% endif %}
```

### Trade-offs ролей:

| Плюсы | Минусы |
|-------|--------|
| Простота | Грубая гранулярность |
| Легко понять | Сложно комбинировать |
| Быстро | Не для сложных правил |

---

## 3. Voters (Fine-grained контроль)

**Voter** — кастомная логика проверки доступа.

### Базовый Voter

```php
// app/src/Security/UserVoter.php
class UserVoter extends Voter
{
    public function supports(string $attribute, mixed $subject): bool
    {
        // Проверка атрибута и типа субъекта
        return str_starts_with($attribute, 'VIEW_USER')
            && $subject instanceof User;
    }
    
    protected function grant(Request $request, mixed $subject, array $roles): bool
    {
        // Логика доступа
        return $subject->getOwner() === $request->attributes->get('user');
    }
}
```

### Регистрация Voter

```yaml
# config/packages/security.yaml
services:
    App\Security\UserVoter:
        tags: ['security.voter']
```

### Использование

```php
#[Route('/users/{id}')]
public function show(int $id): Response
{
    $user = $this->userRepository->find($id);
    
    // Проверка доступа
    if (!$this->isGranted('VIEW_USER', $user)) {
        throw $this->createAccessDeniedException();
    }
    // ...
}
```

---

## Когда использовать Voter vs Roles

| Сценарий | Role | Voter |
|----------|------|-------|
| Простая роль (ROLE_USER) | ✅ | |
| Доступ по ownership | | ✅ |
| Доступ по времени | | ✅ |
| Доступ по статусу | | ✅ |
| Доступ по роли + контекст | | ✅ |
| Статические права | ✅ | |

---

## Практический пример

```php
// app/src/Security/PostVoter.php
class PostVoter extends Voter
{
    public function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'POST_')
            && $subject instanceof Post;
    }
    
    protected function grant(Request $request, mixed $subject, array $roles): bool
    {
        if ($subject->isPublished() || in_array('ROLE_ADMIN', $roles)) {
            return true;
        }
        
        $currentUser = $request->attributes->get('user');
        return $subject->getAuthor() === $currentUser;
    }
}
```

---

## Рекомендации

| Сложность правил | Подход |
|------------------|--------|
| **Простые роли** | Role |
| **Контекстные проверки** | Voter |
| **Динамические правила** | Voter |
| **Комбинации** | Role + Voter |

Хочешь, разберу **конкретный кейс** с твоей аутентификацией или как **оптимизировать** работу Voter?

> ⏱ 4.1s · prompt 13911 tok · output 1138 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

# Хранение Value Objects в Doctrine: 4 подхода

Разберу основные способы маппинга Value Objects и их trade-offs.

---

## Вариант 1: embeddable (встраиваемый класс)

**Doctrine Embeddables** — Value Object как часть сущности.

```php
// app/src/Domain/Value/Money.php
#[Embeddable]
class Money
{
    public function __construct(
        private int $cents,
        private string $currency
    ) {
        // Валидация при создании
        if ($cents < 0) {
            throw new InvalidArgumentException('Money must be non-negative');
        }
    }
    
    public static function fromCents(int $cents, string $currency): self
    {
        return new self($cents, $currency);
    }
    
    public function cents(): int { return $this->cents; }
    public function currency(): string { return $this->currency; }
}

// app/src/Entity/Product.php
class Product
{
    #[Embedded(property: 'money')]
    private Money $price;
    
    public function __construct(Money $price)
    {
        $this->price = $price; // immutable
    }
}
```

### Плюсы:
- Immutable Value Object
- Валидация в конструкторе
- Логическая группировка

### Минусы:
- Хранится в одной таблице (лишенные гибкости)
- Ограничения на вложенность

---

## Вариант 2: One Column = One Field

**Одно поле = одно значение.**

```php
// app/src/Entity/Order.php
class Order
{
    #[ORM\Column(type: 'money')]
    private Money $amount;
    
    // Doctrine мапит Money → столбец money (JSON)
}
```

### Плюсы:
- Простой маппинг
- Один столбец
- JSON-формат

### Минусы:
- Требует кастомного типа
- Ограничения на структуру

---

## Вариант 3: JSON column

**Хранение Value Object как JSON.**

```php
// app/src/Entity/User.php
class User
{
    #[ORM\Column(type: 'json')]
    private array $address;
    
    public function getAddress(): Address
    {
        return Address::fromArray($this->address);
    }
}
```

### Плюсы:
- Гибкая структура
- Не нужно менять схему
- Легко масштабировать

### Минусы:
- Нет валидации на уровне БД
- Сложнее запросы к полям

---

## Вариант 4: Custom Type

**Кастомный Doctrine Type.**

```php
// app/src/DBAL/Types/MoneyType.php
class MoneyType extends Type
{
    public function getSQLDeclaration(array $column, Platform $platform): string
    {
        return 'JSON'; // или DECIMAL
    }
    
    public function convertToDatabaseValue($value, Platform $platform): ?string
    {
        return $value?->toJson();
    }
    
    public function convertToPHPValue($value, Platform $platform): ?Money
    {
        return $value ? Money::fromJson($value) : null;
    }
}

// Регистрация
Types::addType('money', MoneyType::class);
```

### Плюсы:
- Чистый API
- Повторное использование
- Контроль маппинга

### Минусы:
- Больше кода
- Сложнее отладка

---

## Сравнительная таблица

| Подход | Immutable | Валидация | Гибкость | Сложность |
|--------|-----------|-----------|----------|-----------|
| **Embeddable** | ✅ | ✅ | ⭐⭐ | ⭐⭐ |
| **One Column** | ✅ | ✅ | ⭐⭐⭐ | ⭐⭐⭐ |
| **JSON** | ✅ | ❌ | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Custom Type** | ✅ | ✅ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |

---

## Подводные камни

### 1. Неизменяемость и кэш Doctrine

```php
// ПЛОХО: Doctrine кэширует экземпляры
$product = $repository->find(1);
$product->getPrice()->change(); // mutation!

// РЕШЕНИЕ: всегда new
$product->getPrice()->withCents(100); // immutable
```

### 2. Валидация в БД

```php
// JSON не валидирует на уровне БД
// → добавь валидацию в Value Object
class Money {
    public function __construct(int $cents, string $currency) {
        // validation
    }
}
```

### 3. Маппинг и null

```php
// null в JSON
#[ORM\Column(type: 'json')]
private ?Money $price; // nullable

// или
#[ORM\Column(type: 'json')]
private Money $price; // required
```

---

## Рекомендации

| Сценарий | Подход |
|----------|--------|
| **Простой VO** | Embeddable |
| **Гибкая структура** | JSON |
| **Сложная логика** | Custom Type |
| **Валидация в БД** | Custom Type (DECIMAL) |

Хочешь, разберу **конкретный Value Object** с твоими требованиями или как **тестировать** маппинг?

> ⏱ 4.6s · prompt 15103 tok · output 1264 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

# Агрегат в Doctrine: маппинг коллекции и управление транзакциями

Разберу на примере агрегата **Order** с коллекцией **OrderItem**.

---

## Модель агрегата

```php
// app/src/Domain/Order/OrderItem.php
class OrderItem
{
    public function __construct(
        private string $productId,
        private int $quantity,
        private Money $price
    ) {}
    
    public function getTotal(): Money
    {
        return Money::fromCents($this->price->cents() * $this->quantity);
    }
}

// app/src/Domain/Order/Order.php
class Order
{
    /** @var OrderItem[] */
    private array $items = [];
    
    public function __construct(
        private string $id,
        private Customer $customer,
        private array $items = []
    ) {
        $this->items = $items;
    }
    
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
        // инвариант агрегата
        $this->validate();
    }
    
    public function removeItem(string $productId): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            fn($i) => $i->productId() !== $productId
        ));
        $this->validate();
    }
    
    public function total(): Money
    {
        return array_reduce($this->items, function($carry, $item) {
            return $carry->add($item->getTotal());
        }, Money::zero());
    }
    
    private function validate(): void
    {
        if (count($this->items) > 100) {
            throw new \LogicException('Too many items');
        }
    }
}
```

---

## Маппинг Doctrine

### Entity с коллекцией

```php
// app/src/Entity/OrderEntity.php
#[Entity]
class OrderEntity
{
    #[Id]
    #[GeneratedValue(strategy: 'UUID')]
    #[Column(type: 'string')]
    private string $id;
    
    #[ManyToOne(targetEntity: CustomerEntity::class)]
    #[JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private CustomerEntity $customer;
    
    /** @var OrderItemEntity[] */
    #[OneToMany(
        targetEntity: OrderItemEntity::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private \Doctrine\ORM\Mapping\Collection $items;
    
    public function __construct(string $id, CustomerEntity $customer)
    {
        $this->id = $id;
        $this->customer = $customer;
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    // маппинг Domain → Entity
    public static function fromDomain(Order $order): self
    {
        $entity = new self($order->id(), new CustomerEntity($order->customer()->id()));
        
        foreach ($order->items() as $item) {
            $entity->items->add(OrderItemEntity::fromDomain($item));
        }
        
        return $entity;
    }
    
    public function toDomain(): Order
    {
        $items = array_map(
            fn(OrderItemEntity $i) => $i->toDomain(),
            $this->items->toArray()
        );
        
        return new Order($this->id, $this->customer->toDomain(), $items);
    }
}

// app/src/Entity/OrderItemEntity.php
#[Entity]
class OrderItemEntity
{
    #[Id]
    #[GeneratedValue(strategy: 'UUID')]
    #[Column(type: 'string')]
    private string $id;
    
    #[ManyToOne(targetEntity: OrderEntity::class)]
    #[JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private OrderEntity $order;
    
    #[Column(type: 'string')]
    private string $productId;
    
    #[Column(type: 'integer')]
    private int $quantity;
    
    #[Column(type: 'money')]
    private Money $price;
    
    public function __construct(OrderEntity $order, string $productId, int $quantity, Money $price)
    {
        $this->order = $order;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
    }
    
    public static function fromDomain(OrderItem $item): self
    {
        return new self(null, $item->productId(), $item->quantity(), $item->price());
    }
    
    public function toDomain(): OrderItem
    {
        return new OrderItem($this->productId, $this->quantity, $this->price);
    }
}
```

---

## Границы транзакции

### Repository с транзацией

```php
// app/src/Repository/OrderRepository.php
class OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em,
        private Transactional $transactional
    ) {}
    
    public function save(Order $order): void
    {
        $this->transactional->transaction(function() use ($order) {
            $entity = $order instanceof OrderEntity 
                ? $order 
                : OrderEntity::fromDomain($order);
            
            $this->em->persist($entity);
            $this->em->flush();
        });
    }
    
    public function find(string $id): ?Order
    {
        $entity = $this->em->getRepository(OrderEntity::class)->find($id);
        return $entity?->toDomain();
    }
}
```

### Transactional helper

```php
// app/src/Database/Transactional.php
class Transactional
{
    public function __construct(private EntityManagerInterface $em) {}
    
    public function transaction(callable $callback): mixed
    {
        return $this->em->transactional(function() use ($callback) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                $this->em->rollback();
                throw $e;
            }
        });
    }
}
```

---

## Кто и когда вызывает flush

### Pattern 1: Flush в repository

```php
// Приложение вызывает save
$orderRepository->save($order); // flush здесь

// Или
$this->orderRepository->save($order);
$this->orderRepository->save($anotherOrder);
// каждый save = отдельная транзация
```

### Pattern 2: Explicit flush

```php
// Приложение управляет транзацией
$this->transactional->transaction(function() {
    $this->orderRepository->persist($order);
    $this->orderRepository->persist($anotherOrder);
    $this->em->flush(); // один flush для всех
});
```

---

## Практический пример

```php
// app/src/Application/OrderService.php
class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private InventoryRepository $inventory
    ) {}
    
    public function createOrder(array $items): Order
    {
        return $this->transactional->transaction(function() use ($items) {
            $order = new Order(UUID::v4()->toString(), current_customer());
            
            foreach ($items as $item) {
                $order->addItem(new OrderItem(
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ));
            }
            
            // reserve inventory concurrently
            $this->inventory->reserve($order->items());
            
            // single flush
            $this->orders->save($order);
            
            return $order;
        });
    }
}
```

---

## Trade-offs

| Подход | Плюсы | Минусы |
|--------|-------|--------|
| **Flush в repository** | Простота | Много транзаций |
| **Explicit flush** | Контроль | Сложнее |
| **Cascade** | Автоматизация | Меньше контроля |

---

## Рекомендации

| Сценарий | Подход |
|----------|--------|
| **Операция** | Explicit flush в транзации |
| **Одиночный save** | Flush в repository |
| **Множественные save** | Single transaction |

Хочешь, разберу **конкретный агрегат** с твоими требованиями или как **тестировать** граничные случаи?

> ⏱ 7.3s · prompt 16433 tok · output 1941 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

# Диагностика и решение проблемы N+1 в Doctrine

Разберу диагностику и способы решения с trade-offs.

---

## Диагностика N+1

### 1. Doctrine Debug Bar

```yaml
# config/packages/dev/debug.yaml
monolog:
    channels:
        - doctrine

# twig:
#     components:
#         _debug: true
```

### 2. Profiler Symfony

```php
// app/src/Controller/BugController.php
public function profile(): void
{
    // Symfony Profiler показывает запросы
    // / _profiler
}
```

### 3. Doctrine Debug

```php
class BugController
{
    public function __construct(private EntityManagerInterface $em) {}
    
    public function dump(): void
    {
        // Включаем debug-режим
        $this->em->getConnection()->getConfiguration()
            ->setSQLLogger(new \Doctrine\DBAL\Logging\DebugSQLLogger());
        
        $orders = $this->em->getRepository(Order::class)->findAll();
        foreach ($orders as $order) {
            // N+1: каждый заказ → новый запрос к клиенту
            $order->customer()->name();
        }
    }
}
```

---

## Проблема N+1

```php
// ПХОРО: N+1 запросов
$orders = $em->getRepository(Order::class)->findAll();
foreach ($orders as $order) {
    // Каждый цикл → SELECT FROM customers WHERE id = ?
    $customer = $order->customer();
}
```

**Проблема:** Doctrine делает N запросов вместо 1.

---

## Способы решения

### 1. JOIN (fetch=JOIN)

```php
// repository
class OrderRepository
{
    public function findAllWithCustomers(): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.customer', 'c') // JOIN
            ->select('o', 'c') // загрузить и заказ и клиента
            ->getQuery()
            ->getResult();
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| 1 запрос | Большой результат |
| Данные сразу | JOIN может быть медленным |
| Память эффективна | Сложнее чтение кода |

---

### 2. DQL с fetch join

```php
// DQL
$dql = 'SELECT o, c FROM App\Order o JOIN o.customer c';
$result = $em->createQuery($dql)->getResult();
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Контроль запроса | Ручной DQL |
| Оптимизация | Сложнее для сложных случаев |

---

### 3. Lazy Loading с Collection

```php
// Entity
class Order
{
    #[OneToMany(targetEntity: Customer::class)]
    private Customer $customer; // lazy loaded
}

// Application
$orders = $em->getRepository(Order::class)->findAll();
// customer → lazy loaded, но всё равно N+1
```

**Проблема:** lazy loading всё равно N+1.

---

### 4. EntityRepository с eager loading

```php
// config/packages/doctrine.yaml
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    App:
                        type: attribute
                        eager: true // загрузить всех сразу
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Автоматизация | Всегда полный загрузка |
| Нет N+1 | Тратит память |

---

### 5. Partial Select

```php
// partial select
$dql = 'SELECT o, c.name, c.email FROM App\Order o JOIN o.customer c';
$result = $em->createQuery($dql)->getResult();
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Оптимизация памяти | Частичная загрузка |
| Быстрее | Сложнее код |

---

## Сравнительная таблица

| Способ | N+1 | Память | Сложность |
|--------|-----|--------|-----------|
| **JOIN** | ✅ | ⭐⭐⭐ | ⭐⭐ |
| **DQL** | ✅ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Lazy** | ❌ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Eager** | ✅ | ⭐⭐ | ⭐⭐⭐⭐ |
| **Partial** | ✅ | ⭐⭐⭐⭐ | ⭐⭐⭐ |

---

## Рекомендации

| Сценарий | Подход |
|----------|--------|
| **ОдинToOne** | JOIN |
| **OneToMany** | DQL + JOIN |
| **Large collections** | Pagination |
| **Memory limit** | Partial select |

Хочешь, разберу **конкретный пример** с твоим кодом или как **профилировать** производительность?

> ⏱ 4.6s · prompt 18425 tok · output 1134 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

# Ограничения целостности в PostgreSQL: зачем и когда

Бизнес-валидация в коде важна, но **ограничения БД добавляют критически важные гарантии**. Разберу почему.

---

## Почему нужны ограничения БД

### 1. Защита от race conditions

```sql
-- Приложение: проверка + вставка
-- 1. SELECT ... WHERE email = ?
-- 2. INSERT ...
-- Между шагами другой запрос может вставить тот же email!

-- Ограничение БД: UNIQUE
CREATE TABLE users (
    email TEXT UNIQUE NOT NULL
);
-- Двух запросов не существует — БД гарантирует уникальность
```

### 2. Единый источник истины

```sql
-- Валюта только USD, EUR, RUB
ALTER TABLE orders
    ADD CONSTRAINT valid_currency
    CHECK (currency IN ('USD', 'EUR', 'RUB'));
-- Даже если приложение пошлет 'USD ', БД отклонит
```

### 3. Защита от багов в приложении

```php
// Если в коде ошибка — БД всё равно защитит
$orderRepository->save($order); // $order->amount = -100
// CHECK (amount >= 0) — БД не даст сохранить
```

---

## Средства PostgreSQL

### 1. CHECK constraints

```sql
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    amount DECIMAL(10,2) CHECK (amount >= 0),
    status TEXT CHECK (status IN ('pending', 'paid', 'shipped')),
    order_date DATE CHECK (order_date <= CURRENT_DATE)
);
```

**Когда:** простыe бизнес-правила, которые должны быть в БД всегда.

---

### 2. UNIQUE constraints

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email TEXT UNIQUE NOT NULL,
    username TEXT UNIQUE NOT NULL
);
```

**Когда:** уникальность критична и защищает от race conditions.

---

### 3. NOT NULL

```sql
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT
);
```

**Когда:** обязательные поля в домене.

---

### 4. Foreign keys

```sql
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id)
);
```

**Когда:** отношения между сущностями, каскадное удаление.

---

### 5. Partial unique

```sql
-- Уникальный только для активного пользователя
CREATE TABLE user_settings (
    user_id INTEGER,
    setting_key TEXT,
    setting_value TEXT,
    UNIQUE (user_id, setting_key)
) WHERE is_active = true;
```

**Когда:** условная уникальность.

---

### 6. Enum types

```sql
CREATE TYPE payment_status AS ENUM ('pending', 'paid', 'failed', 'refunded');

CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    status payment_status NOT NULL
);
```

**Когда:** фиксированный набор значений.

---

## Trade-offs ограничений БД

| Средство | Плюсы | Минусы |
|----------|-------|--------|
| **CHECK** | Гарантия | Жёсткая схема |
| **UNIQUE** | Защита от race | Трудно менять |
| **FK** | Целостность отношений | Сложное изменение |
| **Enum** | Типобезопасность | Трудно эволюционировать |

---

## Подходы к валидации

### Подход 1: Двойная защита

```php
// Приложение: валидация
class Order {
    public function __construct(int $amount) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }
    }
}

// БД: ALSO enforce
// CHECK (amount >= 0)
```

### Подход 2: Разделение ответственности

| Тип правила | Где |
|-------------|-----|
| **Формат** (валидация) | Приложение |
| **Целостность** (гарантия) | БД |
| **Бизнес-логика** | Приложение |

---

## Практический пример

```sql
-- Типизированная схема
CREATE TABLE payments (
    id UUID PRIMARY KEY,
    amount_cents INTEGER NOT NULL CHECK (amount_cents > 0),
    payment_method TEXT CHECK (payment_method IN ('card', 'paypal', 'crypto')),
    status payment_status NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    order_id UUID REFERENCES orders(id) ON DELETE CASCADE
);

-- Индекс для производительности
CREATE INDEX idx_payments_order ON payments(order_id);
```

---

## Рекомендации

| Тип правила | Где разместить |
|-------------|----------------|
| **Валидация ввода** | Приложение |
| **Гарантия целостности** | БД |
| **Критичные гарантии** | БД (UNIQUE, FK) |
| **Сложная логика** | Приложение |

Хочешь, разберу **конкретную схему** с твоими таблицами или как **управлять миграциями** ограничений?

> ⏱ 4.4s · prompt 19607 tok · output 1178 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

# Диагностика и оптимизация медленных запросов в PostgreSQL

Разберу системный процесс диагностики от анализа плана выполнения до оптимизации.

---

## Этап 1: Анализ плана выполнения

### EXPLAIN ANALYZE — основной инструмент

```sql
EXPLAIN (ANALYZE, BUFFERS, VERBOSE, COSTS)
SELECT o.id, o.amount, c.name
FROM orders o
JOIN customers c ON c.id = o.customer_id
WHERE o.status = 'paid'
  AND o.amount > 1000;
```

### Что смотреть в выводе:

```
Seq Scan on orders  (cost=0.00..2531.00 rows=1 width=52)
  Filter: ((status)::text = 'paid'::text)
Execution Time: 1234.567 ms
```

**Ключевые метрики:**
- **Execution Time** — суммарное время
- **rows** — реальноеvs ожидаемое количество
- **Cost** — оценка стоимости
- **actual time** — фактическое время

---

## Этап 2: Типовые проблемы

### 1. Sequential Scan вместо Index Scan

```sql
-- Плохо: полный скан таблицы
Seq Scan on orders  (cost=0.00..2531.00 rows=1)

-- Индекс помогает
CREATE INDEX idx_orders_status ON orders(status);

-- Становится
Index Scan using idx_orders_status on orders
```

### 2. Неправильный выбор плана

```sql
-- PostgreSQL выбрал плохой план
Hash Join  (cost=...)
  Hash Cond: (c.id = o.customer_id)

-- Force index scan
SET enable_hashjoin = off;
```

### 3. Bad cardinality estimation

```sql
-- PostgreSQL неверно оценил количество строк
rows=1 width=52  (но реально 10000)

-- Обновить статистику
ANALYZE orders;
```

---

## Этап 3: Инструменты диагностики

### pg_stat_statements — анализ частых запросов

```sql
-- Самые затратные запросы
SELECT query, calls, total_exec_time, mean_exec_time
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;
```

### pg_stat_activity — активные сессии

```sql
SELECT pid, usename, client_addr, state, query_time, query
FROM pg_stat_activity
WHERE state = 'active';
```

### pg_locks — дедлоки

```sql
SELECT * FROM pg_locks WHERE NOT granted;
```

---

## Типовые причины медленных запросов

### 1. Отсутствие индексов

```sql
-- Запрос с JOIN без индекса
SELECT ... FROM orders o JOIN customers c ON c.id = o.customer_id
-- Индекс на customer_id
CREATE INDEX idx_orders_customer_id ON orders(customer_id);
```

### 2. Плохие индексы

```sql
-- Индекс не используется
CREATE INDEX idx_status ON orders(status);
-- Но запрос фильтрует по combined condition
CREATE INDEX idx_status_amount ON orders(status, amount);
```

### 3. Неправильные типы данных

```sql
-- Сравнение text с numeric
WHERE amount > 1000  -- amount TEXT!
-- Правильно:
ALTER TABLE orders ALTER COLUMN amount TYPE NUMERIC;
```

### 4. Функции в WHERE

```sql
-- Плохо: функция в WHERE
WHERE to_date(date_string) > '2024-01-01'
-- Индекс на date_string не поможет

-- Хорошо:
WHERE date_string > '2024-01-01'
```

---

## Этап 4: Оптимизация

### 1. Оптимизация запросов

```sql
-- Разбить сложный запрос
-- Было: один запрос с 5 JOIN
SELECT a, b, c, d, e
FROM t1 a JOIN t2 b ON ... JOIN t3 c ON ...

-- Стало: кэширование промежуточных результатов
WITH filtered AS (
    SELECT * FROM orders WHERE status = 'paid'
)
SELECT f.id, c.name
FROM filtered f JOIN customers c ON c.id = f.customer_id;
```

### 2. Partial indexes

```sql
-- Индекс только для активных заказов
CREATE INDEX idx_active_orders ON orders(amount)
WHERE status = 'paid';
```

### 3. Covering indexes

```sql
-- Индекс включает все нужные поля
CREATE INDEX idx_orders_covering ON orders(status, amount)
INCLUDE (customer_id);
```

---

## Этап 5: Мониторинг

### pg_stat_user_tables

```sql
SELECT schemaname, tablename, seq_scan, idx_scan
FROM pg_stat_user_tables
WHERE seq_scan > 1000;  -- много seq scan
```

### Проверка использования индексов

```sql
SELECT indexrelname, idx_scan
FROM pg_stat_user_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan DESC;
```

---

## Практический чек-лист

| Шаг | Что проверить |
|-----|---------------|
| **1** | EXPLAIN ANALYZE — план выполнения |
| **2** | Seq Scan vs Index Scan |
| **3** | Cardinality estimation |
| **4** | Наличие индексов |
| **5** | stale statistics |

---

## Рекомендации

| Проблема | Решение |
|----------|---------|
| **Seq Scan** | Создать индекс |
| **Bad plan** | ANALYZE, пересмотр запроса |
| **Несколько JOIN** | Разбить запрос |
| **Функции** | Индекс на выражении |

Хочешь, разберу **конкретный запрос** с твоими данными или как **работать с large tables**?

> ⏱ 5.0s · prompt 20832 tok · output 1323 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

# Агрегат в DDD: границы и типовые ошибки

Разберу концепцию агрегата и как определять его границы.

---

## Что такое агрегат

**Агрегат** — это кластер доменных сущностей и value objects, которые behandelt как единое целое. Все изменения внутри агрегата должны сохранять **инварианты** (неизменяемые правила).

### Ключевые элементы:

- **Root Entity** — точка входа, через которую получаем доступ к агрегату
- **Internal Entities** — сущности внутри агрегата
- **Value Objects** — неизменяемые объекты
- **Invariant** — правило, которое должно выполняться всегда

---

## Как определять границы агрегата

### Критерии:

1. **Транзакционная целостность**
   - Все изменения должны быть атомарными
   
2. **Инварианты**
   - Где бизнес-правила требуют согласованности
   
3. **Частота изменений**
   - Часто изменяемые вместе → в один агрегат
   
4. **Доступ через root**
   - Доступ к internal entities только через root

---

## Пример: Система заказов

```php
// app/src/Domain/Order/Order.php
class Order  // ← Root Entity
{
    private Customer $customer;         // internal entity
    private array $items = [];          // internal entities
    private Money $total;               // value object
    
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
        // инвариант: общее количество товаров ограничено
        $this->validateItems();
        $this->recalculateTotal();
    }
    
    private function validateItems(): void
    {
        // инвариант: не более 100 товаров
        if (count($this->items) > 100) {
            throw new \LogicException('Too many items');
        }
    }
    
    private function recalculateTotal(): void
    {
        // инвариант: total = sum(items)
        $this->total = array_reduce(
            $this->items,
            fn($carry, $item) => $carry->add($item->getTotal()),
            Money::zero()
        );
    }
}
```

### Почему это один агрегат:

- **Транзакционная целостность** — добавление товара и пересуммирование total должны быть атомарными
- **Инварианты** — max 100 items, total = sum(items)
- **Доступ через root** — к items можно только через Order

---

## Типовая ошибка: Слишком большие или малые границы

### Ошибка 1: Слишком большой агрегат

```php
// ПХОРО: Order + Customer + Product + Payment в одном агрегате
class Order
{
    private Customer $customer;   // ← слишком большой
    private Product $product;     // ← слишком большой
    private Payment $payment;     // ← слишком большой
}
```

**Проблема:**
- Большая транзация
- Низкая масштабируемость
- Сложность управления

### Ошибка 2: Слишком маленькие агрегаты

```php
// ПХОРО: items отдельно от Order
class Order
{
    private array $items; // ← items не в агрегате!
}

class OrderItem
{
    // ← отдельный агрегат
}
```

**Проблема:**
- Нарушаются инварианты
- Сложность сохранения целостности
- N+1 проблемы

---

## Практический пример: границы агрегата

### Сценарий: Заказ с товарами

```php
// app/src/Domain/Order/Order.php
class Order
{
    private Customer $customer;       // internal entity
    private array $items = [];        // internal entities
    
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
        $this->validateLimit();        // инвариант
        $this->recalculate();          // инвариант
    }
    
    private function validateLimit(): void
    {
        if (count($this->items) > 100) {
            throw new \LogicException('Order limited to 100 items');
        }
    }
}

// app/src/Domain/Order/OrderItem.php
class OrderItem  // ← internal entity, NOT a separate aggregate
{
    public function __construct(
        private string $productId,
        private int $quantity,
        private Money $price
    ) {}
    
    public function getTotal(): Money
    {
        return $this->price->multiply($this->quantity);
    }
}
```

### Границы:

```
┌─────────────────────────────────┐
│  Aggregate: Order                 │
│  ┌─────────────────────────┐    │
│  │ Customer (internal)      │    │
│  ├─────────────────────────┤    │
│  │ OrderItem[] (internal)   │    │
│  └─────────────────────────┘    │
└─────────────────────────────────┘
```

---

## Правила определения границ

| Сценарий | Границы |
|----------|---------|
| **Транзакционная целостность** | В один агрегат |
| **Совместные изменения** | В один агрегат |
| **Разные инварианты** | Разные агрегаты |
| **Разная частота** | Разные агрегаты |
| **Большая нагрузка** | Разные агрегаты |

---

## Проверка границ

**Вопросы для проверки:**

1. **Все ли изменения в агрегате должны быть атомарными?**
   - Если да → один агрегат
   
2. **Есть ли инварианты между сущностями?**
   - Если да → один агрегат
   
3. **Часто ли изменяются вместе?**
   - Если да → один агрегат

---

## Рекомендации

| Критерий | В один агрегат |
|----------|----------------|
| Транзация | ✅ |
| Инварианты | ✅ |
| Совместные изменения | ✅ |
| Большая нагрузка | ❌ |

Хочешь, разберу **конкретный агрегат** с твоими доменными правилами или как **тестировать** целостность агрегата?

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

# Одновременное изменение одного агрегата: конфликты и стратегии

Разберу, что происходит при конкурентных изменениях и как это проектировать.

---

## Что произойдёт без защиты

### Race condition

```
Процесс A:  1. Загрузить Order
Процесс B:  1. Загрузить Order
Процесс A:  2. Изменить Order (add item)
Процесс B:  2. Изменить Order (add item)
Процесс A:  3. Save → save()
Процесс B:  3. Save → save() ← перезаписывает!
```

**Результат:** последнее изменение побеждает, первое теряется.

---

## Стратегии разрешения конфликтов

### 1. Optimistic Locking (Оптимистичный замок)

**Принцип:** разрешаем конфликты на уровне приложения.

```php
// app/src/Entity/Order.php
class Order
{
    #[ORM\Column(type: 'integer')]
    private int $version; // ← для optimistic locking
    
    public function bumpVersion(): void
    {
        $this->version++;
    }
    
    public function getVersion(): int
    {
        return $this->version;
    }
}

// app/src/Repository/OrderRepository.php
class OrderRepository
{
    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
        $order->bumpVersion();
    }
    
    public function saveWithOptimisticLock(Order $order): void
    {
        $originalVersion = $order->getVersion();
        
        $this->em->persist($order);
        $this->em->flush();
        
        // Проверка версии
        if ($order->getVersion() !== $originalVersion) {
            throw new ConcurrencyException('Order was modified concurrently');
        }
        
        $order->bumpVersion();
    }
}
```

**Как работает Doctrine:**

```php
// С @Version
#[ORM\Column(type: 'integer')]
#[ORM\Version]
private int $version;
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Простота | Требует обработки конфликтов |
| Нет блокировки БД | Потенциальные retry |
| Высокая параллельность | Сложнее код |

---

### 2. Pessimistic Locking (Пессимистичный замок)

**Принцип:** блокируем запись на уровне БД.

```php
// Pessimistic lock
$order = $this->em->getRepository(Order::class)
    ->find($id, LockMode::PESSIMISTIC_WRITE);
// ← LOCK IN SHARE MODE / FOR UPDATE

// Либо transaction level
$connection->begin();
$order = $connection->fetchAssociative(
    'SELECT * FROM orders WHERE id = ? FOR UPDATE',
    [$id]
);
$connection->commit();
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Гарантия | Блокировка ресурсов |
| Простота | Возможные deadlocks |
| Предсказуемость | Низкая параллельность |

---

### 3. Versioning + Retry (Оптимистичный + повтор)

**Принцип:** оптимистичный замок с повтором.

```php
class OrderRepository
{
    public function save(Order $order, int $maxRetries = 3): void
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $this->saveWithOptimisticLock($order);
                return;
            } catch (ConcurrencyException $e) {
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
                // перезагрузить и повторить
                $order = $this->find($order->id());
            }
        }
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Высокая параллельность | Возможные infinite loop |
| Простота | Retry логика |
| Масштабируемость | Сложнее отладка |

---

### 4. Single Writer (Один писатель)

**Принцип:** все изменения через один процесс.

```php
// app/src/Domain/Order/OrderLock.php
final class OrderLock
{
    public function __construct(private string $orderId) {}
    
    public static function for(Order $order): self
    {
        return new self($order->id());
    }
    
    // Redis lock
    public function acquire(): bool
    {
        return Redis::set("lock:{$this->orderId}", true, ['NX', 'EX' => 30]);
    }
    
    public function release(): void
    {
        Redis::del("lock:{$this->orderId}");
    }
}

// app/src/Application/OrderService.php
class OrderService
{
    public function update(Order $order, callable $operation): void
    {
        $lock = OrderLock::for($order);
        if (!$lock->acquire()) {
            throw new ConcurrencyException('Order is being modified');
        }
        
        try {
            $result = $operation($order);
            $this->orderRepository->save($order);
            return $result;
        } finally {
            $lock->release();
        }
    }
}
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Гарантия | Точка отказа |
| Контроль | Сложность |
| Предсказуемость | Нагрузка на Redis |

---

## Сравнительная таблица

| Стратегия | Гарантии | Параллельность | Сложность |
|-----------|----------|----------------|-----------|
| **Optimistic** | ✅ | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Pessimistic** | ✅⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Optimistic+Retry** | ✅ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Single Writer** | ✅⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |

---

## Рекомендации по выбору

| Сценарий | Стратегия |
|----------|-----------|
| **Низкая конкуренция** | Optimistic |
| **Высокая конкуренция** | Pessimistic |
| **Retry допустим** | Optimistic+Retry |
| **Критичная целостность** | Single Writer |
| **Масштабируемость** | Optimistic |

---

## Практический пример

```php
// app/src/Application/OrderService.php
class OrderService
{
    public function addItem(Order $order, OrderItem $item): Order
    {
        return $this->orderRepository->transaction(function() use ($order, $item) {
            // Pessimistic lock через транзацию
            $em = $this->orderRepository->getEntityManager();
            $locked = $em->getRepository(Order::class)
                ->find($order->id(), LockMode::PESSIMISTIC_WRITE);
            
            // или Optimistic
            $locked = $this->orderRepository->find($order->id());
            $locked->addItem($item);
            $this->orderRepository->save($locked);
            
            return $locked;
        });
    }
}
```

---

## Проверка правильности

**Вопросы:**

1. **Насколько критична целостность?**
   - Высокая → Pessimistic/Single Writer
   
2. **Допустимы ли потери изменений?**
   - Нет → Pessimistic
   
3. **Масштабируемость важна?**
   - Да → Optimistic

Хочешь, разберу **конкретный сценарий** с твоими требованиями или как **тестировать** конкурентные изменения?

> ⏱ 5.6s · prompt 22197 tok · output 1465 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

# Доменные события в DDD: публикация, обработка и проблемы

Разберу доменные события, их применение и связь с транзакциями.

---

## Что такое доменные события

**Доменное событие** — это констатация факта, произошедшего в домене.

```php
// app/src/Domain/Order/OrderRegistered.php
final class OrderRegistered
{
    public function __construct(
        private string $orderId,
        private string $customerEmail,
        private \DateTimeImmutable $registeredAt
    ) {}
    
    public function orderId(): string { return $this->orderId; }
    public function customerEmail(): string { return $this->customerEmail; }
    public function registeredAt(): \DateTimeImmutable { return $this->registeredAt; }
}
```

---

## Когда публиковать доменные события

### Публикация в конце доменной операции

```php
// app/src/Domain/Order/Order.php
class Order
{
    private array $events = [];
    
    public function register(): void
    {
        // ... доменная логика
        
        // Записываем событие (не публикуем!)
        $this->events[] = new OrderRegistered(
            $this->id,
            $this->customerEmail(),
            new \DateTimeImmutable()
        );
    }
    
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
```

### Ключевой принцип: отложенная публикация

```php
// Application Layer
class OrderRegistrationService
{
    public function __construct(
        private OrderRepository $orders,
        private EventDispatcher $events
    ) {}
    
    public function register(string $email, string $name): Order
    {
        $order = new Order(...);
        $order->register(); // доменное событие записано
        
        $this->orders->save($order); // сохранение в БД
        
        // Публикация после сохранения
        foreach ($order->popEvents() as $event) {
            $this->events->dispatch($event);
        }
    }
}
```

---

## Как обрабатывать доменные события

### Event Dispatcher

```php
// app/src/Event/SendWelcomeEmailHandler.php
class SendWelcomeEmailHandler
{
    public function __construct(private MailerInterface $mailer) {}
    
    public function handle(OrderRegistered $event): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($event->customerEmail())
            ->subject('Welcome!')
            ->text('Welcome!');
        
        $this->mailer->send($email);
    }
}

// app/src/Event/ReindexUserHandler.php
class ReindexUserHandler
{
    public function __construct(private SearchIndex $index) {}
    
    public function handle(OrderRegistered $event): void
    {
        $this->index->add($event->orderId());
    }
}
```

### Регистрация handlers

```yaml
# config/packages.yaml
services:
    App\Event\SendWelcomeEmailHandler:
        arguments:
            $mailer: '@mailer'
        tags:
            - { name: 'event.handler' }
```

---

## Проблемы, которые решают доменные события

### 1. Отвязанность (Decoupling)

```php
// Было: прямой вызов
class OrderService {
    public function register() {
        $this->mailer->send(); // прямая зависимость
        $this->index->add();  // прямая зависимость
    }
}

// Стало: через событие
class OrderService {
    public function register() {
        $this->events->dispatch(new OrderRegistered(...));
        // никаких прямых зависимостей
    }
}
```

### 2. Расширяемость

```php
// Новый handler без изменения OrderService
class OrderRegisteredHandler implements EventHandler
{
    public function handle(OrderRegistered $event): void { /* ... */ }
}
```

---

## Проблемы, которые создают доменные события

### 1. Асинхронность сложности

```php
// Сложнее отладка
// Сложнее понимание flow
// Сложнее тестирование
```

### 2. Consistency issues

```php
// Если handler падает — приложение в несогласованном состоянии
class SendWelcomeEmailHandler {
    public function handle(OrderRegistered $event): void {
        $this->mailer->send($email); // если упадёт → несогласованность
    }
}
```

---

## Доменные события и транзации БД

### Проблема: расхождение

```php
// Application Layer
public function register() {
    $order->register();
    $this->orders->save($order);   // ← в транзации БД
    // ...
    $this->events->dispatch(...);  // ← вне транзации БД!
}
```

**Проблема:** события могут быть опубликованы **после** фиксации транзации.

---

## Решения

### 1. События в рамках транзации

```php
// app/src/Application/OrderRegistrationService.php
class OrderRegistrationService
{
    public function register(string $email, string $name): Order
    {
        return $this->em->transactional(function() use ($email, $name) {
            $order = new Order(...);
            $order->register();
            $this->orders->save($order);
            
            // Публикация внутри транзации
            foreach ($order->popEvents() as $event) {
                $this->events->dispatch($event);
            }
            
            return $order;
        });
    }
}
```

### 2. Event Sourcing

```php
// app/src/Domain/Order/Order.php
class Order
{
    private array $events = []; // доменные события
    
    public function getEvents(): array
    {
        return $this->events;
    }
    
    public function recordEvent(OrderEvent $event): void
    {
        $this->events[] = $event;
    }
    
    public function applyEvent(OrderEvent $event): void
    {
        // apply event to state
        $this->apply($event);
    }
}
```

---

## Trade-offs

| Подход | Плюсы | Минусы |
|--------|-------|--------|
| **Async dispatch** | Отвязанность | Сложность |
| **Sync dispatch** | Согласованность | Связанность |
| **Event Sourcing** | История изменений | Сложность |
| **CQRS** | Масштабируемость | Сложность |

---

## Рекомендации

| Сценарий | Подход |
|----------|--------|
| **Точная последовательность** | Sync |
| **Высокая нагрузка** | Async |
| **История изменений** | Event Sourcing |
| **Согласованность критична** | Sync + транзация |

Хочешь, разберу **конкретный сценарий** с_DOМЕННЫМИ событиями или как **тестировать** доменные события?

> ⏱ 6.3s · prompt 25607 tok · output 1653 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

# Value Object vs Entity в DDD: различия и единый язык

Разберу различия, критерии классификации и роль ubiquitous language.

---

## Value Object vs Entity: ключевые различия

| Характеристика | Value Object | Entity |
|----------------|--------------|--------|
| **Идентичность** | По значению | По ID |
| **Неизменяемость** | Immutable | Mutable |
| **Сравнение** | По значению | По ID |
| **Назначение** | Описание аспекта | Жизненный цикл |
| **Хранение** | Обычно inline | Обычно таблица |

---

## Value Object

**Пример: Money, Email, Quantity**

```php
// app/src/Domain/Value/Money.php
final class Money
{
    public function __construct(
        private int $cents,
        private string $currency
    ) {
        if ($cents < 0) {
            throw new \InvalidArgumentException('Money must be non-negative');
        }
    }
    
    // Immutable — mutation невозможен
    public function multiply(int $factor): self
    {
        return new self($this->cents * $factor, $this->currency);
    }
    
    // Сравнение по значению
    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents 
            && $this->currency === $other->currency;
    }
    
    public static function fromCents(int $cents, string $currency): self
    {
        return new self($cents, $currency);
    }
}
```

**Признаки Value Object:**
- Неизменяем
- Описывает атрибут
- Не имеет жизненного цикла
- Описывает «что», а не «кто»

---

## Entity

**Пример: User, Order, Product**

```php
// app/src/Domain/Core/Order.php
final class Order
{
    private array $events = [];
    
    public function __construct(
        private string $id,
        private array $items = []
    ) {}
    
    // Mutable — состояние меняется
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }
    
    public function removeItem(string $productId): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            fn($i) => $i->productId() !== $productId
        ));
    }
    
    // Идентичность по ID
    public function id(): string
    {
        return $this->id;
    }
}
```

**Признаки Entity:**
- Имеет ID
- Изменяемо
- Имеет жизненный цикл
- Идентифицируется по ID

---

## Критерии классификации

### 1. Идентичность

```php
// Money — Value Object (не важно, который именно экземпляр)
$a = new Money(100, 'USD');
$b = new Money(100, 'USD');
$a->equals($b); // true — одинаковые по значению

// Order — Entity (важен конкретный экземпляр)
$order1 = new Order('123');
$order2 = new Order('123');
$order1 === $order2; // false — разные экземпляры
```

### 2. Неизменяемость

```php
// Value Object — immutable
class Money {
    private int $cents;
    
    public function __construct(int $cents) {
        $this->cents = $cents; // readonly
    }
    
    // Нет setter — нельзя изменить
}

// Entity — mutable
class Order {
    private array $items;
    
    public function addItem(OrderItem $item): void {
        $this->items[] = $item; // можно изменять
    }
}
```

### 3. Жизненный цикл

```php
// Money — нет жизненного цикла
// Порядок оплаты не меняет Money

// Order — имеет жизненный цикл
class Order {
    public function pay(): void { /* ... */ }
    public function cancel(): void { /* ... */ }
}
```

---

## Практический пример классификации

```php
// app/src/Domain/Value/CustomerAddress.php
final class CustomerAddress  // ← Value Object
{
    public function __construct(
        private string $street,
        private string $city,
        private string $zip
    ) {}
    
    public function equals(CustomerAddress $other): bool {
        return $this->street === $other->street 
            && $this->city === $other->city
            && $this->zip === $other->zip;
    }
}

// app/src/Domain/Core/Customer.php
final class Customer  // ← Entity
{
    public function __construct(
        private string $id,
        private CustomerAddress $address
    ) {}
    
    public function changeAddress(CustomerAddress $newAddress): void
    {
        $this->address = $newAddress; // Entity изменяется
    }
}
```

---

## Единый язык (Ubiquitous Language)

**Ubiquitous Language** — единый словарь, используемый всеми участниками проекта: разработчиками, бизнес-экспертами, аналитиками.

### Пример

```php
// Плохо: «Запись о пользователе»
class UserRecord {
    // ...
}

// Хорошо: «Пользователь» (доменный термин)
class User {
    // ...
}

// Хорошо: «Заказ» (доменный термин)
class Order {
    // ...
}
```

---

## Как ubiquitous language влияет на код

### 1. Единая терминология

```php
// Доменные термины в коде
class Order {
    public function addItem(OrderItem $item): void { /* ... */ }
    public function getTotal(): Money { /* ... */ }
    public function isCancelled(): bool { /* ... */ }
}
```

### 2.映射 доменных концепций

```php
// Доменная концепция «Оплачено»
class Order {
    public function isPaid(): bool {
        return $this->status === self::PAID;
    }
}
```

---

## Практический пример

```php
// app/src/Domain/Value/Money.php
final class Money  // ← Value Object
{
    public function __construct(
        private int $cents,
        private string $currency
    ) {}
    
    public static function fromCents(int $cents, string $currency): self
    {
        return new self($cents, $currency);
    }
    
    // Единый язык: «стоимость»
    public static function price(int $amount): self
    {
        return new self($amount * 100, 'USD');
    }
}

// app/src/Domain/Core/Order.php
final class Order  // ← Entity
{
    public function __construct(
        private string $id,
        private array $items = []
    ) {}
    
    // Единый язык: «добавить товар»
    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }
}
```

---

## Критерии классификации: чек-лист

| Критерий | Value Object | Entity |
|----------|--------------|--------|
| **Идентичность по ID** | ❌ | ✅ |
| **Неизменяемость** | ✅ | ❌ |
| **Жизненный цикл** | ❌ | ✅ |
| **Описывает атрибут** | ✅ | ❌ |
| **Сравнение по значению** | ✅ | ❌ |

---

## Рекомендации

| Признак | Value Object | Entity |
|---------|--------------|--------|
| **Идентичность** | По значению | По ID |
| **Изменение** | Immutable | Mutable |
| **Хранение** | inline | таблица |

Хочешь, разберу **конкретный домен** и вместе классифицируем концепции?

> ⏱ 7.4s · prompt 27317 tok · output 1866 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

# Структура PHP-приложения по чистой архитектуре

Разберу слои, правило зависимостей и расположение технологий.

---

## Правило зависимостей

```
┌─────────────────────────────────────────┐
│  Presentation Layer (controllers, forms) │
│  ↓                                        │
│  Application Layer (services, use cases)  │
│  ↓                                        │
│  Domain Layer (entities, value objects)   │
│  ↓                                        │
│  Infrastructure Layer (Doctrine, etc.)    │
└─────────────────────────────────────────┘

Внешние слои зависят от внутренних.
Внутренние слои НЕ знают о внешних.
```

---

## Слои приложения

### 1. Domain Layer (самый внутренний)

```php
// app/src/Domain/
// Здесь лежит чистый бизнес-код, без фреймверка

// Entities
class Order { /* доменная логика */ }
class Customer { /* доменная логика */ }

// Value Objects
class Money { /* ... */ }
class Email { /* ... */ }

// Domain Services
class OrderCalculator { /* ... */ }

// Repository Interfaces
interface OrderRepository {
    public function find(string $id): ?Order;
}
```

**Что здесь:**
- Сущности и value objects
- Доменные сервисы
- Интерфейсы репозиториев
- Бизнес-правила

**Запрещено:**
- Нет Doctrine, Symfony
- Нет зависимостей от внешних библиотек

---

### 2. Application Layer

```php
// app/src/Application/
// Use cases, orchestration

class OrderRegistrationService {
    public function __construct(
        private OrderRepository $orders, // ← интерфейс
        private EventDispatcher $events  // ← интерфейс
    ) {}
    
    public function register(OrderData $data): Order {
        // orchestrate domain logic
    }
}
```

**Что здесь:**
- Use cases / Application services
- DTO
- Interface definitions

**Зависит от:** Domain Layer
**Определяет:** интерфейсы для Infrastructure

---

### 3. Infrastructure Layer

```php
// app/src/Infrastructure/
// Реализации, интеграции

// Doctrine
class DoctrineOrderRepository implements OrderRepository {
    public function __construct(private EntityManagerInterface $em) {}
    
    public function find(string $id): ?Order {
        $entity = $this->em->getRepository(Order::class)->find($id);
        return $entity?->toDomain();
    }
}

// Queue
class OrderQueueMessage {
    // ...
}
```

**Что здесь:**
- Doctrine репозитории
- Внешние интеграции
- Реализации интерфейсов

**Зависит от:** Domain + Application

---

### 4. Presentation Layer

```php
// app/src/Controller/
// HTTP endpoints

class OrderController {
    public function __construct(
        private OrderRegistrationService $service
    ) {}
    
    #[Route('/orders', methods: ['POST'])]
    public function create(CreateOrderRequest $request): JsonResponse {
        $data = CreateOrderData::fromRequest($request);
        $order = $this->service->register($data);
        return JsonResponse::from($order);
    }
}
```

**Что здесь:**
- Контроллеры
- Request/Response DTO
- HTTP специфика

**Зависит от:** Application Layer

---

## Где что располагается

| Компонент | слой | Пояснение |
|-----------|------|-----------|
| **Doctrine** | Infrastructure | Реализация репозиториев |
| **Контроллер** | Presentation | HTTP endpoints |
| **Обработчик очереди** | Application | Use case / handler |
| **Сущности** | Domain | Чистый бизнес-код |
| **Value Objects** | Domain | Чистый бизнес-код |
| **Интерфейсы репозиториев** | Domain/Application | Определено в домене |

---

## Правило зависимостей

```php
// ХОРОШО: внешний слой зависит от внутреннего
class DoctrineOrderRepository implements OrderRepository {
    // Infrastructure → Domain (interface)
}

// ПХОРО: внутренний слой зависит от внешнего
class Order {
    public function __construct(private EntityManagerInterface $em) {
        // Domain → Infrastructure (запрещено!)
    }
}
```

---

## Типовая структура проекта

```
app/
├── Domain/
│   ├── Order/
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── OrderRepository.php  ← интерфейс
│   ├── Value/
│   │   ├── Money.php
│   │   └── Email.php
│   └── ...
├── Application/
│   ├── Order/
│   │   ├── OrderRegistrationService.php
│   │   └── CreateOrderData.php
│   └── ...
├── Infrastructure/
│   ├── Doctrine/
│   │   └── DoctrineOrderRepository.php
│   └── ...
├── Controller/
│   └── OrderController.php
└── ...
```

---

## Контроль нарушения границ

### 1. Static analysis

```php
// phpstan
// config/extension.neon
parameters:
    level: 8
    paths:
        - src/Domain
        - src/Application
        - src/Infrastructure
```

### 2. Custom rules

```php
// phpstan/rules
class NoDoctrineInDomainRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassProperty::class;
    }
    
    public function processNode(Node $node, Scope $scope): array
    {
        // запретить Doctrine в Domain
    }
}
```

### 3. PSR-4 namespaces

```php
// composer.json
"autoload": {
    "psr-4": {
        "App\\Domain\\": "src/Domain/",
        "App\\Application\\": "src/Application/",
        "App\\Infrastructure\\": "src/Infrastructure/"
    }
}
```

---

## Практический пример

```php
// app/src/Domain/Order/OrderRepository.php
namespace App\Domain\Order;

interface OrderRepository
{
    public function find(string $id): ?Order;
    public function save(Order $order): void;
}

// app/src/Infrastructure/Doctrine/DoctrineOrderRepository.php
namespace App\Infrastructure\Doctrine;

class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}
    
    public function find(string $id): ?Order
    {
        $entity = $this->em->getRepository(Order::class)->find($id);
        return $entity?->toDomain();
    }
}
```

---

## Зависимости между слоями

| Слой | Зависит от |
|------|-----------|
| **Domain** | Ничего |
| **Application** | Domain |
| **Infrastructure** | Domain, Application |
| **Presentation** | Application |

---

## Рекомендации

| Правило | Где |
|---------|-----|
| **Нет фреймверка** | Domain |
| **Интерфейсы** | Domain/Application |
| **Реализации** | Infrastructure |
| **HTTP** | Presentation |

Хочешь, разберу **конкретный пример** с твоим доменом или как **настроить static analysis**?

> ⏱ 6.6s · prompt 29256 tok · output 1709 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

# Стратегия тестирования Symfony-приложения

Разберу пирамду тестов, типы, моккирование и производительность.

---

## Пирамида тестов

```
        /      \
       /  E2E   \          ← Мало (5%)
      /__________\_
     /   Integration \    ← Средне (20%)
    /________________\
   /    Unit Tests     \  ← Много (75%)
  /______________________\
```

---

## Типы тестов

### 1. Unit Tests (Быстрые)

```php
// app/tests/Domain/Value/MoneyTest.php
final class MoneyTest extends TestCase
{
    public function testCreate(): void
    {
        $money = new Money(100, 'USD');
        self::assertSame(100, $money->cents());
    }
    
    public function testCannotCreateNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-100, 'USD');
    }
    
    public function testMultiply(): void
    {
        $money = new Money(100, 'USD');
        $result = $money->multiply(3);
        self::assertSame(300, $result->cents());
    }
}
```

**Что тестируем:**
- Value Objects
- Доменные сервисы
- Чистую бизнес-логику

---

### 2. Integration Tests (Средние)

```php
// app/tests/Application/OrderRegistrationTest.php
final class OrderRegistrationTest extends KernelTestCase
{
    public function testRegisterOrder(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        
        $service = $container->get(OrderRegistrationService::class);
        $order = $service->register(new OrderData(...));
        
        self::assertInstanceOf(Order::class, $order);
    }
}
```

**Что тестируем:**
- Use cases
- Интеграцию с Doctrine
- Контейнер сервисов

---

### 3. End-to-End Tests (Много)

```php
// app/tests/Controller/OrderControllerTest.php
final class OrderControllerTest extends WebTestCase
{
    public function testCreateOrder(): void
    {
        $client = static::createClient();
        $client->request('POST', '/orders', [
            'data' => ['amount' => 100]
        ]);
        
        self::assertResponseStatusCodeSame(201);
    }
}
```

**Что тестируем:**
- HTTP endpoints
- Полный flow
- Валидацию

---

## Что мокать, а что нет

### Мокать

```php
// 1. Внешние зависимости
class OrderServiceTest extends TestCase
{
    private OrderRepository $repository;
    
    protected function setUp(): void
    {
        $this->repository = $this->mockFor(
            OrderRepository::class
        );
    }
}

// 2. Сетевые вызовы
$mailer = $this->createMock(MailerInterface::class);
$mailer->expects($this->once())
    ->method('send')
    ->with(anyOf(...));
```

**Что мокать:**
- Внешние API
- Сетевые вызовы
- Файлы, окружение

---

### Не мокать

```php
// 1. Доменные классы
$this->repository = new DoctrineOrderRepository($em);

// 2. Зависимости Symfony
$service = $container->get(OrderService::class);

// 3. Value Objects
$money = new Money(100, 'USD');
```

**Что НЕ мокать:**
- Доменные классы
- Doctrine (в integration)
- Symfony контейнер

---

## Стратегия тестирования по слоям

| Слой | Тип теста | Мокать |
|------|-----------|--------|
| **Domain** | Unit | Ничего |
| **Application** | Integration | Внешние зависимости |
| **Infrastructure** | Integration | Внешние API |
| **Presentation** | E2E | Ничего |

---

## Поддержание тестов быстрыми

### 1. Изоляция баз данных

```php
// phpunit.xml
<env name="DATABASE_URL" value="sqlite:///%kernel.project_dir%/var/test.db"/>

// При интеграционных тестах
#[Fixture]
public function testWithDatabase(): void
{
    // SQLite — быстрее PostgreSQL
}
```

### 2. Мокание внешних вызовов

```php
// app/tests/Infrastructure/ExternalApiServiceTest.php
class ExternalApiServiceTest extends TestCase
{
    public function testApiCall(): void
    {
        // Мокать HTTP
        $mock = Mockery::mock('Http\Client\HttpClient');
        $mock->shouldReceive('request')
            ->andReturn($response);
    }
}
```

---

## Поддержание тестов стабильными

### 1. Deterministic behavior

```php
// Плохо: временные метки
public function testTimestamp(): void
{
    $this->assertSame(time(), $result->timestamp());
}

// Хорошо: injected time
class TimeProvider {
    public function now(): \DateTimeImmutable;
}
```

### 2. Cleanup

```php
// app/tests/AbstractTestCase.php
abstract class AbstractTestCase extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        // Очистка БД
        $this->getEntityManager()->clear();
    }
}
```

---

## Практический пример

```php
// app/tests/Application/OrderRegistrationTest.php
final class OrderRegistrationTest extends AbstractTestCase
{
    private OrderRegistrationService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->getContainer()->get(
            OrderRegistrationService::class
        );
    }
    
    public function testRegisterOrderSuccess(): void
    {
        $order = $this->service->register(new OrderData(
            email: 'user@example.com',
            items: []
        ));
        
        self::assertNotNull($order->id());
        self::assertCount(0, $order->items());
    }
    
    public function testRegisterOrderInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);
        $this->service->register(new OrderData(
            email: 'invalid',
            items: []
        ));
    }
}
```

---

## Сравнительная таблица

| Тип | Скорость | Охват | Сложность |
|-----|----------|-------|-----------|
| **Unit** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Integration** | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **E2E** | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ |

---

## Рекомендации

| Сценарий | Тип теста |
|----------|-----------|
| **Чистая логика** | Unit |
| **Интеграция** | Integration |
| **HTTP** | E2E |

Хочешь, разберу **конкретный тест-кейс** с твоим кодом или как **настроить phpunit**?

> ⏱ 6.9s · prompt 31025 tok · output 1690 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

# Одновременное изменение записи в базе: проблемы и решения

Разберу проблемы конкурентного доступа и способы защиты.

---

## Возможные проблемы

### 1. Lost Update (Потерянное обновление)

```
Пользователь A: 1. Прочитать запись
Пользователь B: 1. Прочитать запись
Пользователь A: 2. Изменить (value = 10)
Пользователь B: 2. Изменить (value = 20)
Пользователь A: 3. Save → value = 10
Пользователь B: 4. Save → value = 20  ← A потерял!
```

### 2. Dirty Read (Грязное чтение)

```sql
BEGIN;
UPDATE orders SET status = 'paid' WHERE id = 1;
-- B видит status = 'paid', но A ещё не закоммитил
-- Если A отменит транзацию → B увидел неверные данные
```

### 3. Non-repeatable Read

```sql
-- A прочитал запись
SELECT * FROM orders WHERE id = 1;  -- status = 'pending'

-- B изменил и закоммитил
UPDATE orders SET status = 'paid' WHERE id = 1;

-- A снова прочитал
SELECT * FROM orders WHERE id = 1;  -- status = 'paid'  ← другое значение
```

### 4. Phantom Read

```sql
-- A: SELECT COUNT(*) FROM orders WHERE status = 'pending';  -- 5
-- B: INSERT ... (добавил ещё 1 pending)
-- A: SELECT COUNT(*) FROM orders WHERE status = 'pending';  -- 6  ← фантом
```

---

## Способы защиты

### 1. Isolation Levels PostgreSQL

| Уровень | Dirty Read | Non-repeatable | Phantom |
|---------|-----------|----------------|---------|
| **Read Committed** | ✅ | ❌ | ❌ |
| **Repeatable Read** | ✅ | ✅ | ❌ |
| **Serializable** | ✅ | ✅ | ✅ |

```sql
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
-- или
START TRANSACTION ISOLATION LEVEL READ COMMITTED;
```

### Trade-offs:

| Уровень | Плюсы | Минусы |
|---------|-------|--------|
| **Read Committed** | Быстро | Возможны конфликты |
| **Repeatable Read** | Консистентность | Меньше параллельность |
| **Serializable** | Полная защита | Дедлоки, медленно |

---

### 2. Pessimistic Locking (FOR UPDATE)

```sql
-- Заблокировать запись для обновления
BEGIN;
SELECT * FROM orders WHERE id = 1 FOR UPDATE;
-- Блокировка на всё время транзации
UPDATE orders SET status = 'paid' WHERE id = 1;
COMMIT;
-- Другой ждёт unlock
```

**Как работает:**
- `FOR UPDATE` блокирует строку
- Другой процесс ждёт release
- Гарантирует последовательность

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Гарантия | Блокировка |
| Предсказуемость | Deadlocks |
| Простота | Низкая параллельность |

---

### 3. Optimistic Locking (Version)

```sql
-- Add version column
ALTER TABLE orders ADD COLUMN version INTEGER DEFAULT 0;

-- Update with version check
UPDATE orders 
SET status = 'paid', version = version + 1 
WHERE id = 1 AND version = 0;  -- check version

-- Если affected rows = 0 → conflict
```

**В Doctrine:**

```php
// app/src/Entity/Order.php
#[ORM\Column(type: 'integer')]
#[ORM\Version]
private int $version = 0;

// Doctrine автоматически проверяет версию
```

### Trade-offs:

| Плюсы | Минусы |
|-------|--------|
| Высокая параллельность | Нужна обработка конфликтов |
| Нет блокировки | Retry логика |
| Быстро | Потенциальные потери |

---

### 4. SELECT ... FOR UPDATE + Application Logic

```php
// app/src/Application/OrderService.php
class OrderService
{
    public function updateStatus(string $id, string $status): void
    {
        $connection = $this->em->getConnection();
        $connection->begin();
        
        try {
            // Lock
            $order = $connection->fetchAssociative(
                'SELECT * FROM orders WHERE id = ? FOR UPDATE',
                [$id]
            );
            
            // Update
            $connection->update('orders', [
                'status' => $status,
                'version' => $order['version'] + 1
            ], ['id' => $id]);
            
            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollback();
            throw $e;
        }
    }
}
```

---

### 5. Compare and Swap (CAS)

```sql
-- atomic update
UPDATE orders 
SET status = 'paid' 
WHERE id = 1 AND status = 'pending';
-- affected rows = 1 → success
-- affected rows = 0 → conflict
```

**Trade-offs:**

| Плюсы | Минусы |
|-------|--------|
| Atomic | Limited use cases |
| Fast | No complex logic |

---

## Сравнительная таблица

| Подход | Гарантия | Параллельность | Сложность |
|--------|----------|----------------|-----------|
| **Read Committed** | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Pessimistic** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Optimistic** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Serializable** | ⭐⭐⭐⭐⭐ | ⭐ | ⭐⭐⭐⭐ |
| **CAS** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |

---

## Когда что выбирать

| Сценарий | Подход |
|----------|--------|
| **Низкая конкуренция** | Read Committed |
| **Высокая конкуренция** | Optimistic |
| **Критичная целостность** | Serializable |
| **Очереди** | Pessimistic |
| **Простые проверки** | CAS |

---

## Практический пример

```php
// app/src/Infrastructure/Doctrine/LockRepository.php
class LockRepository
{
    public function withLock(callable $callback): mixed
    {
        $connection = $this->em->getConnection();
        $connection->begin();
        
        try {
            // Lock
            $row = $connection->fetchAssociative(
                'SELECT * FROM orders WHERE id = ? FOR UPDATE',
                [$this->currentId]
            );
            
            $result = $callback($row);
            
            $connection->commit();
            return $result;
        } catch (\Throwable $e) {
            $connection->rollback();
            throw $e;
        }
    }
}
```

---

## Рекомендации

| Критерий | Подход |
|----------|--------|
| **Скорость** | Read Committed |
| **Гарантия** | Serializable |
| **Параллельность** | Optimistic |
| **Очереди** | Pessimistic |

Хочешь, разберу **конкретный кейс** с твоими требованиями или как **тестировать** конкурентные ситуации?

> ⏱ 6.9s · prompt 32769 tok · output 1748 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

# Git-процесс: от задачи до продакшена

Разберу весь lifecycle с обоснованием каждого решения.

---

## Общая архитектура

```
feature → develop → release → main → production
    ↓         ↓         ↓         ↓          ↓
  локально  разработка  релизы    prod      прода
```

---

## 1. Ветвление (Branching)

### Git Flow (классический)

```
main (master)     ← production-ready
   ↑
develop          ← integration
   ↑
feature/*        ← разработка
```

**Прочие модели:**

```
GitHub Flow:
main ← develop ← feature
   (feature прямо в main)

GitLab Flow:
main → environment branches
   (main → staging → production)
```

### Почему Git Flow?

| Плюс | Минус |
|------|-------|
| Изоляция разработки | Сложнее |
| Стабильный main | Больше веток |
| Clear staging | Сложнее merge |

---

## 2. Формат коммитов

### Conventional Commits

```
feat: add user registration
fix: resolve N+1 query in order list
docs: update README
chore: update dependencies
refactor: extract order validation
test: add money tests
style: format code
```

### Структура сообщения

```
<type>(<optional scope>): <short summary>

<optional body>

<optional footer>
```

### Почему так?

| Плюс | Минус |
|------|-------|
| Автоматические changelog | Ограничения формата |
| Читемость истории | Требует дисциплины |
| Semantic versioning | Мелочность |

---

## 3. Pull Request

### Процесс

```
feature/feature-x ──┐
                    ├─→ PR ──→ Review ──→ Merge → develop
develop             │
                    │
release/* ──────────┘
```

### Требования к PR

```yaml
# PR Template
## Changes
- What changed
- Why

## Testing
- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] Manual verification

## Checklist
- [ ] Code follows style guide
- [ ] Documentation updated
- [ ] No debug code
```

---

## 4. Code Review

### Чек-лист ревью

```markdown
## Code Quality
- [ ] Code is readable
- [ ] No magic numbers
- [ ] Error handling present
- [ ] No hardcoded values

## Security
- [ ] Input validation
- [ ] No SQL injection
- [ ] Auth checks

## Performance
- [ ] No N+1
- [ ] Efficient queries
```

### Кто ревьюит

```
1. Automated checks (CI)
2. Peer review (1-2 reviewers)
3. Senior review (critical changes)
```

---

## 5. Релизы

### Semantic Versioning

```
MAJOR.MINOR.PATCH
- MAJOR: breaking changes
- MINOR: new features (backwards compatible)
- PATCH: bug fixes
```

### Git Tag для релиза

```bash
git tag -a v1.2.0 -m "Release 1.2.0"
git push origin v1.2.0
```

### Changelog автоматизация

```bash
# conventional-changelog
npx conventional-changelog -p angular -i CHANGELOG.md -s
```

---

## Полный процесс

```
1. Setup
   git checkout -b feature/feature-x develop

2. Разработка
   git add .
   git commit -m "feat: ..."

3. Push
   git push -u origin feature/feature-x

4. Pull Request
   - Create PR → develop
   - CI runs
   - Review
   - Merge

5. Release
   - Merge → main
   - Tag v1.0.0
   - Deploy
```

---

## Почему именно так?

| Решение | Почему |
|---------|--------|
| **Git Flow** | Изоляция dev/prod |
| **Conventional Commits** | Автоматизация |
| **PR для всех** | Контроль качества |
| **Semantic Versioning** | Предсказуемость |

---

## Инструменты

| Этап | Инструмент |
|------|-----------|
| **CI/CD** | GitHub Actions |
| **Ревью** | Codecov |
| **Тесты** | PHPUnit |
| **Релиз** | Git Tags |

---

## Практический пример

```bash
# 1. Новая ветка
git checkout -b feature/user-auth develop

# 2. Разработка и коммиты
git add src/
git commit -m "feat(auth): add JWT authentication"

# 3. Push
git push -u origin feature/user-auth

# 4. PR
# (через UI)

# 5. после merge
git checkout main
git pull
git tag -a v1.0.0 -m "Auth feature release"
```

---

## Чек-лист перед деплоем

- [ ] Все тесты прошли
- [ ] Code review выполнен
- [ ] Documentation обновлена
- [ ] Version tagged
- [ ] Changelog updated

Хочешь, разберу **конкретную модель ветвления** или как **настроить CI/CD**?

> ⏱ 4.8s · prompt 34576 tok · output 1202 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

# Хороший code review: принципы и практика

Разберу, что делает ревью эффективным, как категоризировать замечания и решать разногласия.

---

## Что делает ревью хорошим

### 1. Конструктивность, а не критика

```
❌ "Это неправильно"
✅ "Здесь есть потенциальная утечка памяти. 
     Давай заменим на X, потому что Y"
```

### 2. Фокус на коде, не на человеке

```
❌ "Ты опять забыл обработку ошибок"
✅ "Отсутствует обработка ошибок в этом блоке"
```

### 3. Быстро, но внимательно

| Плюс | Минус |
|------|-------|
| Backlog не накапливается | Риск поверхностного review |

---

## На что смотрю в первую очередь

### Приоритеты review

| Уровень | Что проверяю |
|---------|-------------|
| **P0** | Безопасность, целостность данных |
| **P1** | Бизнес-логика, инварианты |
| **P2** | Читаемость, дизайн |
| **P3** | Стиль, детали |

### Чек-лист

```markdown
## Security
- [ ] Input validation
- [ ] No SQL/NoJS injection
- [ ] Auth checks

## Correctness
- [ ] Logic matches requirements
- [ ] Edge cases handled
- [ ] Error handling

## Design
- [ ] SOLID principles
- [ ] No code duplication
- [ ] Appropriate abstractions

## Performance
- [ ] No N+1 queries
- [ ] Efficient algorithms
```

---

## Маркировка замечаний

### Severity Levels

```
🔴 CRITICAL — must fix before merge
🟠 MAJOR — should fix
🟡 MINOR — nice to fix
🔵 INFO — question/suggestion
```

### Примеры

```
🔴 CRITICAL: SQL injection vulnerability
   Line 42: $query = "SELECT * FROM users WHERE name = '$name'";
   Fix: Use parameterized query

🟠 MAJOR: No error handling
   Line 88: $mailer->send($email);
   Consider try/catch

🟡 MINOR: Magic number
   Line 15: return $amount * 1.15;  // 1.15?
   
🔵 INFO: Naming
   Line 3: function calculate() → calculateTotal()?
```

---

## Категоризация замечаний

| Тип | Пример | Как помечать |
|-----|--------|-------------|
| **Bug** | Ошибка в логике | 🔴 |
| **Security** | Уязвимость | 🔴 |
| **Design** | Плохой API | 🟠 |
| **Style** | Форматирование | 🟡 |
| **Question** | Не понятно | 🔵 |

---

## При разногласиях с автором

### 1. Задаю вопросы, а не претензии

```
❌ "Почему ты так написал?"
✅ "Объясни, пожалуйста, почему выбран этот подход?"
```

### 2. Аргументирую фактами

```
🔴 "Это вызовет N+1 при 10k записях. 
    Есть EXPLAIN ANALYZE, смотри."
```

### 3. Предлаю решения

```
🟠 "Вместо X давай попробуем Y — вот пример"
```

### 4. Если не можем договориться

```
1. Документируем обе точки зрения
2. Эскалируем senior/лиду
3. Записываем решение
```

---

## Процесс review

```
Author creates PR
        ↓
Automated checks (CI)
        ↓
Manual review (reviewers)
        ↓
Comments with severity
        ↓
Author addresses comments
        ↓
Re-review (if needed)
        ↓
Merge
```

---

## Практические советы

### Для автора PR

```
- Небольшие PR (до 400 строк)
- Само-ревью перед отправкой
- Описание изменений
- Тесты
```

### Для ревьюера

```
- Не более 400 строк за раз
- 1-2 ревьюера
- Сроки review
```

---

## Чек-лист ревью

```markdown
## Обязательно
- [ ] Security проверено
- [ ] Correctness проверено
- [ ] Tests есть

## Рекомендуется
- [ ] Design review
- [ ] Performance review
- [ ] Readability

## Опционально
- [ ] Style suggestions
- [ ] Refactoring ideas
```

---

## Anti-паттерны review

| Анти-паттерн | Правильно |
|-------------|-----------|
|Nitpicking | Focus on substance |
|Silent merge | Give feedback |
|Too broad | Prioritize |
|Personal attacks | Code focus |

---

Хочешь, разберу **конкретный пример** review с реальным кодом или как **настроить процессы** в команде?

> ⏱ 4.8s · prompt 35829 tok · output 1159 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

# Формулировка задачи из сырой бизнес-проблемы

Разберу, как превратить «Пользователи жалуются» в конкретную, выполнимую задачу.

---

## Сырая проблема

```
«Пользователи жалуются, что не могут найти свои заказы из прошлого,
поддержка завалена обращениями»
```

---

## Проблема формулировки

**Сырая проблема не выполняет критерий:**
- ❌ Неясно, что именно «не могут найти»
- ❌ Неизвестно, «из прошлого» = сколько времени
- ❌ Не указано, где именно (какая функция)
- ❌ Неизвестно, как «завалена» (сколько tickets)

---

## Как сформулировать задачу

### Формулировка задачи

```
Задача: Реализовать поиск заказов пользователя за последние 12 месяцев

Цель: Снизить количество обращений в поддержку по теме "не могу найти заказ"

Условия:
- Поиск по ID заказа, дате, статусу
- Заказ за последние 12 месяцев
- Пагинация (20 записей на странице)
- Сортировка по дате (новые первые)

Критерии приемки:
- [ ] Пользователь видит список своих заказов
- [ ] Поиск по ID работает
- [ ] Поиск по датeworks
- [ ] Пагинация работает
- [ ] Тесты覆盖率 > 80%

Отказоустойчивость:
- Поиск за > 12 месяцев показывает сообщение "Заказы старше недоступны"

Срок: 5 дней
```

---

## Что обязательно включишь в формулировку

### 1. Конкретная цель (не «сделать хорошо»)

```
❌ «Сделать поиск заказов лучше»
✅ «Реализовать поиск заказов за 12 месяцев»
```

### 2. Измеримые критерии

```
- Поисковая функция
- Пагинация (20/стр)
- Сортировка
- Coverage > 80%
```

### 3. Четкие ограничения

```
- 12 месяцев
- ID/дата/статус
- "заказы старше недоступны"
```

---

## Почему именно так?

| Элемент | Зачем |
|---------|-------|
| **Конкретность** | Исполнитель понимает задачу |
| **Измеримость** | Можно проверить результат |
| **Ограничения** | Понятен scope |
| **Критерии** | Можно оценить качество |

---

## Как не сделать (анти-паттерны)

```
❌ «Сделайте нормальный поиск» (неконкретно)
❌ «Улучшите функцию поиска заказов» (нет критериев)
❌ «Быстро найдите заказы» (нет ограничений)
```

---

## Метрики успеха

```
Метрики для проверки:
- Снижение tickets по "не могу найти заказ"
- Время поиска (< 100ms)
- Conversion rate (поиск → заказ)
```

---

## Проверка формулировки

**Хорошая формулировка:**
- ✅ Можно выполнить без уточнений
- ✅ Есть критерии приемки
- ✅ Понятен scope
- ✅ Измерим результат

---

Хочешь, разберу **как собрать требования** от бизнеса или как **оценить задачу**?

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

# Вопросы заказчику до начала работы

Разберу, какие вопросы задать, чтобы понять истинные потребности и сузить проблему.

---

## Приоритетные вопросы

### 1. Проблема и контекст

```
1. Как именно пользователи "не могут найти" заказы?
   - Через какой интерфейс? (мобильное app, web, поддержка)
   - Что они пытаются сделать?

2. "Из прошлого" - сколько времени?
   - Месяц? Год? Все время?

3. Сколько обращений в поддержку?
   - Точное число в неделю?
   - Был ли рост со временем?
```

### 2. Существующая функциональность

```
4. Есть ли уже функция поиска?
   - Что она делает?
   - Почему не работает?

5. Где находятся заказы?
   - В какой системе/БД?
   - Есть ли доступ к данным?
```

---

## Технические вопросы

### 3. Данные

```
6. Как долго хранятся заказы?
   - Есть ли ретайн-политика?
   - Архивирование?

7. Откуда берются данные?
   - Из какой БД?
   - Есть ли доступ к данным?
```

### 4. Производительность

```
8. Сколько пользователей ищут?
   - Пиковая нагрузка?

9. Сколько заказов в системе?
   - Размер таблицы?
```

---

## Бизнес-вопросы

### 5. Мотивация

```
10. Почему это важно сейчас?
    - Есть ли дедлайн?
    - Бизнес-приоритет?

11. Какова стоимость проблемы?
    - Сколько стоит одно обращение?
    - Потерянные продажи?
```

### 6. Успех

```
12. Как измерить успех?
    - Какой % снижения tickets?
    - Какой UX должен быть?
```

---

## Вопросы о решении

### 7. Ожидаемый UX

```
13. Какой интерфейс ожидается?
    - Список? Таблица? Фильтры?

14. Нужна ли история поиска?
    - Недавние запросы?
```

### 8. Интеграции

```
15. Нужна ли интеграция с другими системами?
    - CRM?
    - Аналитика?
```

---

## Пример структурированных вопросов

```
## Discovery Questions

### Problem Understanding
- Как именно проявляется проблема? (5W)
- Кто конкретно жалуется? (persona)
- Как часто? (frequency)

### Current State
- Что уже есть?
- Что не работает?

### Requirements
- Что обязательно должно работать?
- Что желательно?

### Constraints
- Термины? (бюджет, сроки)
- Технические ограничения?

### Success Metrics
- Как поймём, что решили проблему?
```

---

## Почему эти вопросы

| Тип вопроса | Зачем |
|-------------|-------|
| **Контекст** | Понять истину проблему |
| **Данные** | Оценить техническую сложность |
| **Мотивация** | Понять приоритет |
| **Успех** | Определить критерии |

---

## Anti-паттерны вопросов

| Не делать | Делать |
|-----------|--------|
| Слишком много вопросов | Приоритетные |
| Общие вопросы | Конкретные |
| Технические детали | Бизнес-ценность |

---

Хочешь, разберу **как проводить discovery** или как **ранжировать вопросы**?

> ⏱ 3.2s · prompt 37069 tok · output 777 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

# Действия при обнаружении проблем во время работы

Разберу две ситуации и стратегию реагирования.

---

## Ситуация A: Попутный баг

### Что делаю

```
1. Зафиксирую баг
   - Create issue / ticket
   - Document reproduction steps
   - Add severity

2. Продолжаю основную задачу
   - Не отклоняюсь без необходимости

3. Сообщаю
   - "Нашёл баг X, не связанный с задачей"
   - "Создал issue #123"
```

### Почему

| Причина | Обоснование |
|---------|-------------|
| **Фиксация** | Баг не потеряется |
| **Фокус** | Основная задача не раздувается |
| **Прозрачность** | Команда знает о проблеме |

---

## Ситуация B: Противоречие критериев

### Что делаю

```
1. Останавливаюсь
   - Не предполагаю намерения

2. Исследую
   - Почему критерий противоречит цели?
   - Что будет, если выполнить критерий?

3. Задаю вопросы
   - "Критерий X противоречит цели Y. 
     Поясни, пожалуйста?"

4. Предлаю решение
   - "Может, критерий должен быть Z?"
```

### Почему

| Причина | Обоснование |
|---------|-------------|
| **Не предполагаю** | Могу неправильно понять |
| **Проверка** | Лучше спросить, чем делать не то |
| **Решение** | Предложить вариант |

---

## Примеры

### Ситуация A

```
Задача: "Реализовать список заказов"
Нашёл: При пагинации дублируются записи

Действия:
1. Create issue: BUG-456 (pagination duplicates)
2. Продолжаю основную задачу
3. В конце: "Обрати внимание на BUG-456"
```

### Ситуация B

```
Задача: "Реализовать поиск заказов за 12 месяцев"
Критерий: "Показывать 100 заказов на странице"
Цель: "Удобный поиск"

Противоречие: 100 записей неудобно!

Действия:
1. "Критерий (100) противоречит цели (удобство)"
2. "Предлагаю 20 записей — это standard UX"
3. Жду подтверждения
```

---

## Чек-лист действий

### Ситуация A (баг)

- [ ] Зафиксировал баг
- [ ] Продолжил основную задачу
- [ ] Сообщил о баге

### Ситуация B (противоречие)

- [ ] Не предполагал намерение
- [ ] Исследовал противоречие
- [ ] Задал вопрос
- [ ] Предложил решение

---

## Анти-паттерны

| Не делать | Делать |
|-----------|--------|
| Игнорировать баг | Фиксировать |
| Игнорировать противоречие | Спросить |
| Делать по-своему | Согласовать |

---

Хочешь, разберу **как документировать** эти ситуации или как **эскалировать**?

> ⏱ 3.3s · prompt 38764 tok · output 780 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

# Отчёт о выполнении задачи

Разберу структуру отчёта и что менять при частичном результате.

---

## Структура отчёта

### 1. Краткое резюме

```
Готово: Реализован поиск заказов за 12 месяцев

Кратко:
- Поисковая функция
- Пагинация
- Сортировка
- Тесты pass
```

### 2. Что сделано

```
## Done
- [x] Поиск по ID
- [x] Поиск по дате
- [x] Пагинация (20/стр)
- [x] Сортировка (новые первые)
- [x] Тесты (coverage 85%)
```

### 3. Критерии приемки

```
## Acceptance Criteria
- [x] Поиск по ID работает
- [x] Поиск по дате работает
- [x] Пагинация работает
- [x] Coverage > 80%
```

### 4. Изменения

```
## Changes
### Files
- src/Controller/OrderController.php
- src/Repository/OrderRepository.php
- tests/Application/OrderSearchTest.php

### Database
- Added index idx_orders_created_at

### Config
- No changes
```

---

## Что меняется при частичном результате

### Ситуация: не всё сделано

```
## Done
- [x] Поиск по ID
- [x] Пагинация
- [ ] Поиск по дате ← НЕ СДЕЛАНО

## Blocked
- Поиск по дате: ждём ответа по индексу

## Next
- Реализовать поиск по дате
- Добавить тесты
```

---

## Пример отчёта

```
## Report: Order Search

### Status: Partial

### Done
- [x] Search by ID
- [x] Pagination
- [x] Sorting

### Not Done
- [ ] Search by date (blocked: index missing)

### Acceptance
- [x] Search by ID
- [x] Pagination
- [ ] Search by date ← BLOCKED

### Changes
- Modified: OrderController.php
- Modified: OrderRepository.php

### Next Steps
- Add index for date search
- Implement date search
```

---

## Что обязательно включить

| Раздел | Зачем |
|--------|-------|
| **Status** | Ясность результата |
| **Done** | Что сделано |
| **Not Done** | Прозрачность |
| **Blocked** | Почему не всё |
| **Next** | План действий |

---

## Анти-паттерны

| Не делать | Делать |
|-----------|--------|
| «Всё готово» при частичном | Честно указать |
| Скрыть блок | Указать blocked |
| Без Next步骤 | Показать план |

---

## Чек-литет

```
## Report Checklist
- [ ] Status clear
- [ ] Done listed
- [ ] Not Done listed
- [ ] Blocked explained
- [ ] Next steps
- [ ] Changes documented
```

Хочешь, разберу **как сообщать о blocked** или как **документировать**?

> ⏱ 3.1s · prompt 39587 tok · output 705 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

# FORCE INDEX в PostgreSQL

Важное уточнение: **`FORCE INDEX` — это MySQL-синтаксис**. В PostgreSQL такого оператора нет. Разберу, что есть в Postgres.

---

## Что есть в PostgreSQL вместо FORCE INDEX

### 1. `SET` для контроля планов

```sql
-- Отключить использование определённого типа скана
SET enable_seqscan = off;
SET enable_bitmapscan = off;

-- Отключить конкретный план
SET enable_hashjoin = off;
SET enable_mergejoin = off;
```

### 2. Хинты через SET LOCAL

```sql
BEGIN;
SET LOCAL enable_seqscan = off;
-- ... запрос ...
COMMIT;
```

---

## Почему FORCE INDEX не нужен в PostgreSQL

### Разница с MySQL

| MySQL | PostgreSQL |
|-------|-----------|
| `FORCE INDEX` | Нет такого синтаксиса |
| Драйвер может игнорировать хинты | Костинг-оптимизатор решает |
| Жёсткий выбор индекса | Гибкий выбор |

### Философия Postgres

```
Оптимизатор видит статистику и сам выбирает лучший план.
FORCE INDEX ломал бы эту логику.
```

---

## Когда применять (аналоги FORCE INDEX)

### 1. Плохой план запроса

```sql
-- Оптимизатор выбрал плохой план
-- Проверить через EXPLAIN ANALYZE

-- Принудительно отключить плохой метод
SET enable_mergejoin = off;
```

### 2. Девизиация статистики

```sql
-- Обновить статистику
ANALYZE orders;

-- Если всё равно плохо
SET LOCAL enable_bitmapscan = off;
```

---

## Практический пример

```sql
-- Вместо FORCE INDEX (MySQL)
-- SELECT ... FROM orders FORCE INDEX(idx)

-- В PostgreSQL
SET LOCAL enable_seqscan = off;
EXPLAIN ANALYZE
SELECT * FROM orders WHERE created_at > '2024-01-01';
```

---

## Рекомендации

| Сценарий | Решение |
|----------|---------|
| **Плохой план** | EXPLAIN ANALYZE → понять почему |
| **Wrong index** | ANALYZE → переучить статистику |
| **Временно** | SET LOCAL |

---

Хочешь, разберу **как читать EXPLAIN ANALYZE** подробно или как **работать с планами запросов**?

> ⏱ 2.4s · prompt 40319 tok · output 561 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

# strict_types=1 и производительность

Важное уточнение: **`declare(strict_types=1)` НЕ замедляет выполнение кода.**

---

## Почему это не влияет на скорость

### Что делает strict_types

```php
declare(strict_types=1);

function sum(int $a, int $b): int {
    return $a + $b;
}
```

- Проверяет типы **в момент вызова** функции
- Проверка происходит на уровне **Zend Engine**
- Это **не** runtime-проверки в теле функции

### Где происходит проверка

```
PHP код → Zend Engine → Проверка типов аргументов → execution
                              ↑
                         Одноразовая операция
```

---

## Измерения

### Реальные цифры

| Метрика | Разница |
|---------|---------|
| **Single call** | < 1% (в пределах погрешности) |
| **1000 calls** | < 0.5% |
| **Hot path** | Практически 0 |

### Почему почти 0

```
1. Проверка типов — нативный C-код
2. Не в теле функции
3. Компилируется в opcode cache
```

---

## Что действительно влияет на скорость

### 1. Типизация return

```php
// С strict_types
function calculate(float $x): float { return $x * 2; }

// Без strict_types — то же, но тишее приведение
```

### 2. Runtime проверки (в коде)

```php
// Вот это замедляет
if (!is_int($x)) { /* проверка в коде */ }
```

---

## Практический вывод

| Параметр | Влияние |
|----------|---------|
| **strict_types** | ~0% (незначительно) |
| **OPcache** | >90% ускорение |
| **Типизация return** | Зависит от кода |

---

## Рекомендации

| Сценарий | Совет |
|----------|-------|
| **Производительность** | strict_types НЕ влияет |
| **Надёжность** | Использовать strict_types обязательно |
| **Скорость** | OPcache важнее |

---

Хочешь, разберу **как OPcache влияет на скорость** или как **профилировать** PHP-код?

> ⏱ 2.3s · prompt 40918 tok · output 532 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

# Составные первичные ключи в Doctrine ORM

Важная поправка: **Doctrine ORM частично поддерживает** составные ключи, но с серьёзными ограничениями.

---

## Почему Doctrine плохо составляет ключи

### Исторические причины

```
Doctrine ORM проектировался для 1-to-1 маппинга
Entity → Table. Composition key ломал эти допущения.
```

### Техническая проблема

```php
// Составной ключ = несколько полей
// ORM ожидает single identifier для identity management

class OrderItem {
    #[Id] private string $orderId;
    #[Id] private string $productId; // ← два @Id
}
```

---

## Как Doctrine поддерживает (частично)

### embeddable + composite

```php
// CompositeKey в DQL
#[Id]
#[Column(type: 'string')]
private string $id;  // лучше: один ID

// Или
#[Id]
#[ManyToOne(...)]
private $parent;
```

---

## Реальные решения

### 1. Генерируемый ID (recommended)

```php
class OrderItem {
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private int $id;
    
    // Составной ключ через unique constraint
    #[Column(type: 'string')]
    private string $orderId;
    
    #[Column(type: 'string')]
    private string $productId;
    
    // Unique constraint
    // UNIQUE(order_id, product_id)
}
```

### 2. Embeddable key

```php
// app/src/Entity/OrderItemKey.php
#[Embeddable]
class OrderItemKey {
    #[Column(type: 'string')]
    private string $orderId;
    
    #[Column(type: 'string')]
    private string $productId;
}
```

---

## Почему генерируемый ID лучше

| Параметр | Composition Key | Generated ID |
|----------|----------------|--------------|
| **ORM support** | Ограниченный | Полный |
| **JOIN** | Сложно | Просто |
| **Performance** | Медленно | Быстро |
| **Doctrine** | Ограниченно | Полностью |

---

## Практический пример

```php
// app/src/Entity/Product.php
class Product {
    #[Id]
    #[GeneratedValue(strategy: 'UUID')]
    #[Column(type: 'string')]
    private string $id;
}

// app/src/Entity/OrderItem.php
class OrderItem {
    #[Id]
    #[GeneratedValue(strategy: 'UUID')]
    #[Column(type: 'string')]
    private string $id;
    
    #[ManyToOne(targetEntity: Product::class)]
    #[JoinColumn(referencedColumnName: 'id')]
    private Product $product;
    
    #[Column(type: 'integer')]
    private int $quantity;
}
```

---

## Рекомендации

| Сценарий | Решение |
|----------|---------|
| **Simple key** | Generated ID |
| **Composition** | UUID + unique constraint |
| **Legacy DB** | Embeddable key |

---

Хочешь, разберу **как мапить legacy таблицы** с composition key или как **работать с composite unique**?

> ⏱ 3.0s · prompt 41488 tok · output 728 tok

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

# Анализ и рефакторинг кода

Разберу проблемы и перепишу код с объяснением.

---

## Найденные проблемы

### 🔴 КРИТИЧЕСКИЕ

```php
// 1. SQL Injection
$id = $req->get('order_id');  // user input
$query = "SELECT * FROM orders WHERE id = $id";  // interpolation!
// Если $id = "1 OR 1=1" → data leak
```

```php
// 2. XSS в Mailer
Mailer::send($order['email'], 'Ваш заказ оплачен');  // $order из БД
// Если email в БД содержит XSS → проблема
```

### 🟠 MAJOR

```php
// 3. No error handling
$pdo->query(...);  // упадёт?
$pdo->exec(...);   // упадёт?

// 4. Raw PDO — нет Doctrine
// Сложно поддерживать, нет unit-test

// 5. Связанность
// Контроллер делает: select, update, mail, log
```

---

## Переписанный код

### Новая архитектура

```php
// app/src/Domain/Order/Order.php
class Order {
    public function __construct(
        private string $id,
        private string $status,
        private string $email
    ) {}
    
    public function markAsPaid(): void {
        $this->status = 'paid';
    }
    
    public function id(): string { return $this->id; }
    public function status(): string { return $this->status; }
    public function email(): string { return $this->email; }
}

// app/src/Domain/Order/OrderRepository.php
interface OrderRepository {
    public function find(string $id): ?Order;
    public function save(Order $order): void;
}
```

### Infrastructure

```php
// app/src/Infrastructure/Doctrine/DoctrineOrderRepository.php
class DoctrineOrderRepository implements OrderRepository {
    public function __construct(private EntityManagerInterface $em) {}
    
    public function find(string $id): ?Order {
        $entity = $this->em->getRepository(Order::class)->find($id);
        return $entity?->toDomain();
    }
    
    public function save(Order $order): void {
        $this->em->persist($order);
        $this->em->flush();
    }
}
```

### Application

```php
// app/src/Application/OrderPaymentService.php
class OrderPaymentService {
    public function __construct(
        private OrderRepository $orders,
        private MailerInterface $mailer
    ) {}
    
    public function pay(string $orderId): void {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }
        
        $order->markAsPaid();
        $this->orders->save($order);
        
        $this->mailer->send(
            $order->email(),
            'Ваш заказ оплачен'
        );
    }
}
```

### Presentation

```php
// app/src/Controller/PayController.php
#[Route('/orders/{id}/pay', methods: ['POST'])]
public function pay(string $id): JsonResponse
{
    try {
        $this->paymentService->pay($id);
        return JsonResponse::success('paid');
    } catch (OrderNotFoundException $e) {
        return JsonResponse::error('not found', 404);
    }
}
```

---

## Почему переписывал так?

| Проблема | Решение |
|----------|---------|
| **SQL Injection** | Parameterized queries (Doctrine) |
| **No error handling** | Exceptions |
| **Связанность** | Разделение слоёв |
| **Raw PDO** | Doctrine ORM |
| **No testing** | Interface-driven |

---

## Что улучшилось

### Before → After

```
Before:
- SQL injection 🕳️
- No error handling
-耦合 (耦合耦合耦合)
- Untestable

After:
- Secure (Doctrine)
- Exceptions
- Clean architecture
- Testable
```

---

## Практический вывод

| Приоритет | Что сделать |
|-----------|-------------|
| **1** | Fix SQL injection |
| **2** | Add error handling |
| **3** | Refactor to layers |

---

Хочешь, разберу **как тестировать** новый код или как **настроить Doctrine**?

> ⏱ 4.4s · prompt 42428 tok · output 1022 tok

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

# Анализ доменной сущности User

Разберу проблемы с точки зрения DDD и чистой архитектуры.

---

## Найденные проблемы

### 🔴 КРИТИЧЕСКИЕ

```php
// 1. Entity хранит EntityManager (Infrastructure)
public function __construct(
    private EntityManagerInterface $em,  // ← Infrastructure!
)
```
**Проблема:** Доменная сущность знает о Doctrine. Это нарушает чистую архитектуру.

```php
// 2. changeEmail делает flush
public function changeEmail(string $email): void
{
    $this->email = $email;
    $this->em->flush();  // ← Infrastructure concern!
}
```
**Проблема:** Сущность управляет транзацией. Это бизнес-логика не её уровня.

### 🟠 MAJOR

```php
// 3. Email — примитив вместо Value Object
private string $email;  // ← должно быть Value Object
```
**Проблема:** Нет валидации email, нет инвариантов.

```php
// 4. changeEmail не валидирует
public function changeEmail(string $email): void { /* ... */ }
```
**Проблема:** Можно установить любой строку.

---

## Исправленная версия

### Domain Layer

```php
// app/src/Domain/Value/Email.php
final class Email
{
    private function __construct(private string $value) {}
    
    public static function from(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail($email);
        }
        return new self($email);
    }
    
    public function toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}

// app/src/Domain/Event/UserEmailChanged.php
final class UserEmailChanged
{
    public function __construct(
        private string $userId,
        private Email $oldEmail,
        private Email $newEmail,
    ) {}
}
```

### Domain Entity

```php
// app/src/Domain/User/User.php
final class User
{
    private array $events = [];
    
    public function __construct(
        private string $id,
        private Email $email,
    ) {}
    
    public function changeEmail(string $newEmail): void
    {
        $email = Email::from($newEmail);  // валидация
        
        $oldEmail = $this->email;
        $this->email = $email;
        
        // Записываем событие (не публикуем!)
        $this->events[] = new UserEmailChanged(
            $this->id, $oldEmail, $email
        );
    }
    
    public function email(): Email { return $this->email; }
    public function popEvents(): array { /* ... */ }
    public function id(): string { return $this->id; }
}
```

### Infrastructure

```php
// app/src/Infrastructure/Doctrine/DoctrineUserRepository.php
final class DoctrineUserRepository implements UserRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}
    
    public function save(User $user): void
    {
        $entity = $this->map($user);
        $this->em->persist($entity);
        $this->em->flush();
    }
    
    public function find(string $id): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->find($id);
        return $entity?->toDomain();
    }
}
```

### Application

```php
// app/src/Application/User/UserChangeEmailService.php
final class UserChangeEmailService
{
    public function __construct(
        private UserRepository $users,
        private UserEmailChangedHandler $handler,
    ) {}
    
    public function __invoke(string $userId, string $newEmail): void
    {
        $user = $this->users->find($userId);
        if (!$user) {
            throw new UserNotFound($userId);
        }
        
        $user->changeEmail($newEmail);  // доменная логика
        
        $this->users->save($user);      // инфраструктура
        
        // Публикация после сохранения
        foreach ($user->popEvents() as $event) {
            $this->handler->handle($event);
        }
    }
}
```

---

## Что изменилось и почему

| Проблема | Решение |
|----------|---------|
| **EntityManager в Entity** | Убран, Repository управляет |
| **flush в Entity** | Repository делает flush |
| **string email** | Value Object Email |
| **No validation** | Email::from валидирует |
| **No event** | Запись доменного события |

---

## Архитектурные принципы

### Чистая архитектура

```
User (Domain) — чистый, без Doctrine
        ↓
Repository Interface (Domain)
        ↓
DoctrineUserRepository (Infrastructure)
        ↓
UserChangeEmailService (Application)
```

### DDD

```
- Value Object для email
- Доменная логика в Entity
- События для отвязанности
- Repository для данных
```

---

## Практический вывод

| Приоритет | Что сделать |
|-----------|-------------|
| **1** | Убрать Doctrine из Entity |
| **2** | Email → Value Object |
| **3** | Repository управляет flush |
| **4** | Doменное событие |

Хочешь, разберу **как мапить Entity ↔ Domain** или как **тестировать** домен?

> ⏱ 5.4s · prompt 43600 tok · output 1302 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

# Решение технических проблем на незнакомых технологиях

Разберу последовательность действий от непонимания до оценки сроков.

---

## Последовательность действий

### 1. Определение проблемы

```
1. Что именно не работает?
   - Symptom (симптом) vs Root cause (причина)

2. Как повторить?
   - Reproduction steps

3. Что ожидалось vs что есть?
```

### 2. Исследование

```
1. Документация (официальная)
2. GitHub Issues / StackOverflow
3. Исходный код (если open source)
4. CHANGELOG / release notes
```

### 3. Гипотезы

```
1. Сформулировать 2-3 гипотезы
2. Оценить вероятность каждой
3. Проверить самую вероятную первой
```

---

## Метод исследования

| Шаг | Что делать |
|-----|-----------|
| **Reproduce** | Сделать минимум для повтора |
| **Isolate** | Убрать лишнее |
| **Hypothesize** | Предположить причину |
| **Test** | Проверить гипотезу |
| **Verify** | Убедиться что решено |

---

## Пример

```
Проблема: "Библиотека X падает на PHP 8.3"

1. Reproduce
   composer require x/y
   ./test.php  ← падает

2. Research
   - docs/library
   - issues GitHub
   - CHANGELOG

3. Hypothesis
   - H1: Несовместимость с PHP 8.3
   - H2: Bug в версии 2.1.0

4. Test
   - downgrade to 2.0.0 → OK
   - H2 confirmed

5. Solve
   - patch / wait release / workaround
```

---

## Когда просить помощь

| Ситуация | Помощь |
|----------|--------|
| **> 30 min без движения** | Помощь |
| **Блокирующая команда** | Эскалация |
| **Риск потери данных** | Помощь |
| **Нет экспертизы** | Эксперт |
| **Огромный scope** | Обсудить |

---

## Когда разбираться самому

| Сценарий | Само |
|----------|------|
| **Обучение** | Само (с таймером) |
| **Небольшая проблема** | Само |
| **Любопыствие** | Само |
| **Есть время** | Само |

---

## Оценка сроков

```
После решения:

1. Что сделано
2. Что ещё нужно
3. Риски
4. Оценка:
   - Optimistic (всё просто)
   - Realistic (с учётом тестов)
   - Pessimistic (риски)

Формула:
Realistic = Solution × (1.5-2x для testing + code review)
```

---

## Чек-лист

| Этап | Статус |
|------|--------|
| **Reproduce** | [ ] |
| **Research** | [ ] |
| **Hypothesize** | [ ] |
| **Solve** | [ ] |
| **Test** | [ ] |
| **Estimate** | [ ] |

---

## Практический совет

**Правило 25 минут:**
- 25 min на исследование
- Если нет результата → помощь

---

Хочешь, разберу **как писать issue** или как **оценивать риски**?

> ⏱ 3.7s · prompt 44976 tok · output 820 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

# Что делать, если задача не успевается

Разберу честную стратегию коммуникации.

---

## Главное правило

```
СООБСИ ПРЕЖДЕ, чем дедлайн, а не после.
Позднее предупреждение = потеря доверия.
Раннее = планирование, а не проблема.
```

---

## Последовательность

### 1. Предупредить сразу

```
«Ребята, задача займёт 2 дня вместо 1.
Вот почему — [объяснение].
Вот мой план — [варианты].»
```

### 2. Объяснить причину

```
- Что обнаружил
- Почему это добавило времени
- Насколько уверен в оценке
```

### 3. Предложить варианты

| Вариант | Плюсы | Минусы |
|---------|-------|--------|
| **Сдвинуть срок** | Качество | Дедлайн перенос |
| **Сократить scope** | В срок | Частичная функция |
| **Доджить** | В срок | Перегрузка |

---

## Пример коммуникации

```
Джентльмены, форс-мажор с задачей X.

Что ожидал: 1 день
Что есть: ~2 дня
Почему: 
- Обнаружил баг в [X] (см. issue #123)
- Нужно добавить валидацию
- Тесты требуют доработки

Предлагаю:
A) Сдвинуть на {день+1} — рекомендую
B) Сделать MVP до завтра, остальное — послезавтра
C) Джинуть сегодня, но рискую качеством

Что выбираем?
```

---

## Что НЕ делать

| Анти-паттерн | Почему плохо |
|-------------|--------------|
| **Молчать** | Потеря времени командой |
| **Доджить в ущерб качеству** | Баги в будущем |
| **Скрыть проблему** | Хуже потом |
| **Обещать ещё раз** | Потеря доверия |

---

## Оценка риска

| Сценарий | Реакция |
|----------|---------|
| **Ещё 2 часа до дедлайна** | Срочно сообщить |
| **Дедлайн завтра утро** | Сообщить сейчас |
| **Гибкий дедлайн** | Предложить варианты |

---

## Практический совет

**Честность > Идеальность.**
Команда ценит предупреждение больше, чем идеальное выполнение.

---

Хочешь, разберу **как оценивать задачи** точнее или как **работать с гибкими дедлайнами**?

> ⏱ 2.7s · prompt 45838 tok · output 603 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

# Баланс между качеством и скоростью

Разберу, как принимать решения о «достаточно хорошем» коде.

---

## Факторы для принятия решения

### 1. Стоимость ошибки

| Тип | Качество |
|-----|---------|
| **Критичный** (деньги, безопасность) | Всегда высокое |
| **Внутренний** (внутренние инструменты) | «Достаточно хорошо» |
| **Эксперимент** (MVP) | Скорость > качество |

### 2. Жизненный цикл кода

| Стадия | Приоритет |
|--------|-----------|
| **MVP** | Скорость |
| **Стабильность** | Качество |
| **Поддержка** | Качество |

---

## Модель принятия решений

```
                 ВЫСОКОЕ КАЧЕСТВО
                    |
        критичный  |  ядро системы
        код         |  production
                    |
    НЕЗАКОНЧЕННОЕ ——+—— ЗАВЕРШЁННОЕ
                    |
        experiment  |  legacy
                    |
                 НИЗКОЕ КАЧЕСТВО
```

---

## Критерии «достаточно хорошо»

### Можно отдавать, если:

```
✅ Базовая функциональность работает
✅ Критичные пути покрыты тестами
✅ Нет security-дыр
✅ Читаемо и поддерживаемо
✅ Можно улучшить позже (спец. тех.долг)
```

### Нужно улучшать, если:

```
⚠️ Критичный код (платежи, безопасность)
⚠️ Частые изменения
⚠️ Сложно поддерживать
⚠️ Высокая нагрузка
```

---

## Практический пример

```php
// MVP (M скорость):
function process(array $orders): array {
    $result = [];
    foreach ($orders as $order) {  // простой цикл
        $result[] = processOrder($order);
    }
    return $result;
}

// Production (высокое качество):
final class OrderProcessor {
    public function __construct(
        private Logger $logger,
        private Validator $validator
    ) {}
    
    public function process(iterable $orders): array {
        // обработка ошибок, логирование, валидация
    }
}
```

---

## Техдолг: осознанный vsслучайный

| Тип | Что делать |
|-----|-----------|
| **Осознанный** (временно) | Зафиксировать, запланировать |
| **Случайный** (незамеченный) | Рефактить сразу |

---

## Правило 80/20 для качества

```
80% качества за 20% усилий
Оставшиеся 20% качества = 80% усилий

Решение:
- Дости 80% → отдавать
- 100% только для критичного
```

---

## Чек-лист «готово»

| Критерий | Критичный | Обычный |
|----------|-----------|---------|
| **Тесты** | Обязательно | Базовые |
| **Логирование** | Обязательно | Опционально |
| **Документация** | Обязательно | Редко |
| **Рефакторинг** | Желательно | Опционально |

---

## Практический совет

**«Code is like: clean now, clean later, or dirty forever»**

Выбирайте осознанно, когда «позже».

---

Хочешь, разберу **как оценивать техдолг** или как **работать с техниками**?

> ⏱ 3.6s · prompt 46498 tok · output 846 tok

---
