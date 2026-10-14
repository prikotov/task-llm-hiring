# Собес: `muse-glimmer` · роль: backend — run-01

- Дата: 2026-10-08T12:00:38.056870+00:00
- Сервер: http://nserv:11434 · keep_alive: 0 · think: off · num_ctx: 131072
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Здравствуйте! Я бэкенд-разработчик с ~6 лет опытом в PHP, последние 4 года фокусируюсь на enterprise-проектах на Symfony.

Основной стек: PHP 8.1-8.3, Symfony 5-7, Doctrine ORM, PostgreSQL, RabbitMQ, Redis, Docker, Kubernetes. Работал над high-load B2B-платформами с 1M+ пользователей, платежными системами и SaaS-продуктами.

В проектах активно применяю DDD и принципы чистой архитектуры. Делаю разделение на Domain, Application, Infrastructure слои, использую CQRS для чтения/записи, Value Objects, Domain Events. Покрываю код PHPUnit + Behat, пишу contract тесты для интеграций, CI/CD на GitLab CI с автоматическим деплоем.

Смежные технологии: API Platform, API Platform GraphQL, Elasticsearch, S3/MinIO, Prometheus + Grafana, PHPStan level 9, Rector.

**Самооценка:**

**PHP 8 — 9/10** - уверенно использую union types, match, attributes, fibers, JIT, знаю внутреннее устройство движка.
**Symfony — 9/10** - проектировал и поддерживал несколько крупных монолитов и микросервисов, глубоко знаю DI-контейнер, events, messenger.
**Doctrine — 8/10** - хорошо разбираюсь в mapping, DQL, Criteria, оптимизация запросов, но сложные tree-структуры и second level cache использую не ежедневно.
**PostgreSQL — 8/10** - пишу сложные запросы, индексы, partitions, CTE, оптимизирую EXPLAIN ANALYZE, но не администрирую кластера.
**DDD — 7/10** - применяю bounded contexts и агрегаты на практике, но не всегда идеально выдерживаю чистоту границ в legacy.
**Чистая архитектура — 7/10** - строю слоистую архитектуру и зависящие от правил, понимаю принципы SOLID/Clean Code, иногда жертвую идеальной чистотой ради сроков.
**Тестирование — 8/10** - пишу unit, integration и acceptance тесты, TDD в новых фичах, но покрытие в старых проектах не всегда 100%.
**Git — 9/10** - уверенно работаю с ветвлениями, rebase, cherry-pick, resolve конфликтов в командной разработке.

> ⏱ 11.0s · prompt 147 tok · output 575 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Отличный вопрос. Для чистого и надёжного кода я выделяю 4 ключевые группы возможностей PHP 8+.

**1. Строгая типизация и безопасность типов**
Named arguments + Union types + Nullsafe оператор позволяют писать самодокументируемый и безопасный код.

```php
function createUser(string $email, string $password, ?string $phone = null): User
{
    // ...
}

// PHP 8.0 Named arguments
createUser(email: 'test@test.com', password: '123');

// PHP 8.0 Union types
function findUser(int|string $id): User|User[]
{
    // ...
}

// PHP 8.0 Nullsafe operator
$city = $user?->getAddress()?->getCity(); // без каскада if
```

**2. Match expression и Constructor Property Promotion**
`match` заменяет error-prone switch, а property promotion убирает бойлерплейт.

```php
// PHP 8.0 match - строгий, возвращает значение, exhaustive
$status = match($order->getState()) {
    Order::NEW => 'Новый',
    Order::PAID => 'Оплачен',
    Order::CANCELLED => 'Отменен',
    default => throw new \LogicException('Unknown state'),
};

// PHP 8.0 Constructor Property Promotion
class OrderDto {
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public array $items = []
    ) {}
}
```

**3. Readonly свойства и строгие типы данных**
`readonly` и `DateTimeImmutable` делают модель предсказуемой.

```php
// PHP 8.1 Readonly
final class Money {
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {}
}

$money = new Money(100, 'USD');
// $money->amount = 200; // Fatal Error
```

**4. Современные инструменты качества**
PHP 8.1+ Enums и Fibers для сложной логики.

```php
// PHP 8.1 Backed Enum
enum OrderStatus: string {
    case NEW = 'new';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
}

function handle(OrderStatus $status) {
    match($status) {
        OrderStatus::NEW => ...
    }
}

// PHP 8.1 Intersection types для контрактов
function process(Logger&Cache $service) {}
```

Эти возможности позволяют строить код, который сложнее сломать: типы ловят ошибки на этапе компиляции, `readonly` и `match` убирают целый класс сайд-эффектов, а named arguments делают вызовы функций читаемыми.

> ⏱ 10.1s · prompt 700 tok · output 578 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP - гибридная. По умолчанию PHP **слабый и нестрогий**, с 7.0 появились скаляры, с 8.0+ - union, mixed, readonly. Но поведение зависит от директивы `declare(strict_types=1)`.

### 1. `strict_types` - где именно работает

`strict_types` работает **только внутри файла где объявлена**, и только для **приведения скалярных типов** при вызове функций/методов.

```php
// file A.php
declare(strict_types=1);

function sum(int $a, int $b): int {
    return $a + $b;
}

sum(1, 2);      // OK
sum(1.9, 2);    // TypeError! 1.9 не приводится к int
```

```php
// file B.php
declare(strict_types=0); // по умолчанию

function sum(int $a, int $b): int {
    return $a + $b;
}

sum(1.9, 2);    // OK! 1.9 -> 1, нестрогое приведение
sum("10", 2);   // OK! "10" -> 10
```

`strict_types` **не влияет** на:
* Возвращаемое значение - `return "123"` в `function(): int` всегда попытается привести, а с PHP 8.0+ будет TypeError только в strict? Нет, для возврата строгий режим не работает, только приведение.
* Объекты и классы - `function(User $u)` всегда требует объект, приведение не происходит.
* `mixed` и `?type` - всегда строгие.

### 2. Приведения типов - где спасает, где удивляет

**Спасает: Скалярная типизация**

```php
function setAge(int $age): void { /*...*/ }

setAge('25'); // strict_types=0 -> 25, strict_types=1 -> TypeError
// В строгом режиме ты сразу узнаешь что передал строку из БД вместо int
```

**Удивляет: Сравнение и приведение в условиях**

```php
// Ловушка 1: Строковое сравнение
"0" == false   // true
"0e123" == "0" // true - научная нотация!

// Ловушка 2: Пустой массив
[] == false    // true

// Решение: всегда === 
$status === 'active';
```

```php
// Ловушка 3: Union types + null
function find(int|string|null $id) {}

// PHP 8.0: можно передать null, но если ты забыл ?null - будет TypeError
function find(int|string $id) {}
find(null); // TypeError
```

**Спасает: Readonly + типизированные свойства**

```php
class User {
    public function __construct(
        public readonly string $email,
        public int $age
    ) {}
}

$user = new User('test', '30'); // strict_types=0 -> '30' приводится к 30
// Но с declare(strict_types=1) в том же файле -> TypeError

$user->email = 'new'; // Fatal Error - readonly спасает от случайной мутации
```

**Удивляет: Mixed и динамический код**

```php
function process(mixed $data): array { ... }

// PHP 8.0+ mixed разрешает всё, но ты теряешь защиту
// Поэтому в Domain слое я не использую mixed, только конкретные DTO
```

### 3. Типичные ловушки на практике

1. **Несоответствие strict_types между файлами**
   `A.php` strict, `B.php` не strict. Вызов `A::func('123')` из B пройдет приведение, а из C со strict - упадет. Всегда ставь `declare(strict_types=1)` во всех файлах проекта.

2. **Приведение float -> int**
   ```php
   function getPage(int $page) {}
   getPage(1.9); // strict=0 -> 1, молча теряешь данные
   ```

3. **Boolean из строк**
   ```php
   (bool) "false" // true! Любая непустая строка = true
   ```

4. **Default значения и nullable**
   ```php
   function save(?string $name = '') {} // Путаница: null vs ''
   ```

**Итог:** `strict_types=1` + скалярная типизация + `readonly` + `===` убирают ~80% "магических" багов PHP. Типизация спасает на границах системы - контроллер/DTO. Удивляет внутри ядра языка, где исторически живут нестрогие сравнения и приведения.

> ⏱ 16.7s · prompt 1330 tok · output 1102 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Отличный вопрос, обработка исключений - это про контракт и читаемость, а не про `try/catch` везде.

### 1. Моя философия: исключения - это контракт

Я делю исключения на 2 уровня:

**Domain / Application Exceptions** - часть бизнес-контракта, ожидаемые ошибки
**Infrastructure Exceptions** - неожиданные сбои, их нужно оборачивать

```php
// Domain Exceptions - checked по смыслу, но unchecked по PHP
namespace Domain\User;

class UserNotFoundException extends \DomainException {}
class EmailAlreadyUsedException extends \DomainException {}

// Application Service
final class RegisterUser {
    /**
     * @throws EmailAlreadyUsedException
     * @throws UserNotFoundException
     */
    public function __invoke(RegisterUserCommand $cmd): UserId
}
```

**Контракт метода** - это именно `@throws`. Если метод может не найти пользователя - это должно быть явным в докблоках, а не скрытым в `null`.

### 2. Иерархия исключений

Я не наследуюсь от `Exception` напрямую.

```php
namespace App;

abstract class AppDomainException extends \DomainException {}
abstract class AppApplicationException extends \RuntimeException {}
abstract class AppInfrastructureException extends \RuntimeException {}

// Использование
try {
    $this->pdo->execute();
} catch (\PDOException $e) {
    // Оборачиваем чужое исключение в своё доменное
    throw new AppInfrastructureException('DB connection failed', 0, $e);
}
```

Правило: **никогда не пробрасывай наружу фреймворковые/сторонние исключения**. `PDOException`, `GuzzleException` - это детали реализации.

### 3. Оборачивание чужих исключений

```php
final class UserRepository {
    public function find(UserId $id): User
    {
        try {
            $row = $this->db->fetch(....);
            if (!$row) {
                throw new UserNotFoundException($id);
            }
        } catch (\PDOException $e) {
            // Оборачиваем, сохраняем причину
            throw new UserRepositoryException('Cannot fetch user', 0, $e);
        }
    }
}
```

В верхнем слое - глобальный handler:
```php
// Symfony event listener
public function onKernelException(ExceptionEvent $event)
{
    $e = $event->getThrowable();
    
    if ($e instanceof AppDomainException) {
        // 400 Bad Request, понятный ответ пользователю
    }
    
    // Все остальное -> 500, логируем
    $this->logger->critical($e);
}
```

### 4. Антипаттерны

**1. Пустой catch**
```php
try { ... } catch (\Exception $e) {} // Тихий fail, самый опасный
```

**2. Слишком широкий catch**
```php
catch (\Exception $e) { ... } // Ловишь Error, TypeError, всё подряд
```
Лови только то, что можешь обработать.

**3. Исключения для управления потоком**
```php
try { $user = $repo->find($id); } 
catch (UserNotFoundException) { return null; }
// Лучше: if (!$user) return null; или метод findOneOrNull
```
Исключения - дорогие, не используй их как `if`.

**4. Потеря стектрейса**
```php
throw new MyException('error'); // без $previous
throw new MyException('error', 0, $previous); // правильно
```

**5. `throw new \Exception` без контекста**
Сообщение должно быть для лога, а не для пользователя. Для пользователя - отдельный DTO ошибки.

Итог: Я проектирую так, чтобы в Domain слое были только мои доменные исключения, в Infrastructure - оборачивание в AppInfrastructureException, а глобальный handler решает, что показывать наружу. Контракт метода описывается `@throws`, а не надеждой на `null`.

> ⏱ 14.0s · prompt 2405 tok · output 851 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

Чаще всего нарушается **Single Responsibility Principle - Принцип единственной ответственности**. 

Почему? Потому что SRP - это про границы ответственности, а не про количество методов. Он требует, чтобы класс имел только одну причину для изменения. На практике это самый размытый принцип, и в реальном коде он нарушается постоянно.

**Типовое нарушение: God Object / Service который делает всё**

Класс отвечает и за бизнес-логику, и за работу с БД, и за отправку email, и за логирование, и за формирование ответа API. Менять бизнес-правила -> трогаем класс, меняем формат письма -> трогаем тот же класс, меняем БД -> трогаем тот же класс. Одна причина меняется -> класс меняется по 10 причинам.

```php
// Нарушение SRP + Open/Closed + Dependency Inversion
class OrderService {
    private $db;
    private $mailer;
    private $logger;

    public function __construct(PDO $db, Mailer $mailer, Logger $logger) {
        $this->db = $db;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function createOrder(array $data): array {
        // 1. Валидация
        if (empty($data['email'])) throw new \Exception('No email');
        
        // 2. Бизнес-логика
        $total = $data['price'] * $data['qty'];
        if ($total > 10000) $total *= 0.9;

        // 3. Работа с БД
        $stmt = $this->db->prepare('INSERT...');
        $stmt->execute([...]);

        // 4. Отправка email
        $this->mailer->send($data['email'], 'Order created', '...');

        // 5. Логирование
        $this->logger->info('Order created');

        // 6. Формируем ответ
        return ['status' => 'ok', 'total' => $total];
    }
}
```
Одна причина для изменения - 6 разных причин. Тестировать невозможно без мока всего мира.

**Рефакторинг по SRP**

Разделяем ответственности: Domain, Application, Infrastructure.

```php
// 1. Domain - только бизнес-правила
final class Order {
    public function __construct(
        public readonly string $email,
        public readonly Money $total
    ) {}
    
    public static function create(string $email, Money $price, int $qty): self
    {
        if ($email === '') throw new \DomainException('No email');
        $total = $price->multiply($qty);
        if ($total->amount() > 10000) $total = $total->discount(0.9);
        return new self($email, $total);
    }
}

// 2. Application Service - оркестрация
final class CreateOrder {
    public function __construct(
        private OrderRepository $repo,
        private OrderCreatedNotifier $notifier
    ) {}
    
    public function __invoke(CreateOrderCommand $cmd): OrderId {
        $order = Order::create($cmd->email, $cmd->price, $cmd->qty);
        $id = $this->repo->save($order);
        $this->notifier->notify($order);
        return $id;
    }
}

// 3. Infrastructure
final class OrderRepository {
    public function __construct(private PDO $db) {}
    public function save(Order $order): OrderId { /* ... */ }
}

final class OrderCreatedNotifier {
    public function __construct(private Mailer $mailer, private Logger $logger) {}
    public function notify(Order $order): void {
        $this->mailer->send($order->email, ...);
        $this->logger->info('Order created');
    }
}
```

Теперь:
* `Order` меняется только при изменении бизнес-правил
* `CreateOrder` меняется только при изменении процесса
* `OrderRepository` меняется только при изменении хранилища
* `OrderCreatedNotifier` меняется только при изменении уведомлений

Тестировать можно изолированно, класс закрыт для модификации и открыт для расширения через интерфейсы. Это чистая реализация SRP, которая тянет за собой OCP и DIP.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно и нужно нарушать. SOLID - это не догма, а инструменты для управления сложностью. Принцип должен служить продукту, а не продукт - принципу.

**Когда нарушение оправдано: прагматизм > идеология**

Самый частый оправданный случай - **нарушение Single Responsibility ради производительности и простоты в скриптах / ад-hoc задачах**.

### Пример оправданного нарушения SRP

Миграция данных / one-time скрипт.

Тебе нужно за один раз пройтись по 10 млн строк в БД, пересчитать поле, обновить индекс и отправить уведомление. Делать для этого полноценную чистую архитектуру с Domain, Application, Repository, Notifier - это оверхед на 3 дня разработки и 2 дня ревью.

Оправданное нарушение:

```php
// scripts/migrate_prices.php
// SRP нарушен намеренно: валидация + бизнес + БД + лог в одном файле
final class MigratePrices {
    public function __construct(
        private PDO $db,
        private Logger $logger
    ) {}

    public function run(): void {
        $this->db->beginTransaction();
        try {
            $rows = $this->db->query('SELECT id, price FROM products WHERE price IS NOT NULL')->fetchAll();
            
            foreach ($rows as $row) {
                // Бизнес-логика прямо тут
                $newPrice = $row['price'] * 1.2;
                
                $this->db->prepare('UPDATE products SET price = ? WHERE id = ?')
                    ->execute([$newPrice, $row['id']]);
                
                $this->logger->info("Updated {$row['id']}");
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->error($e);
            throw $e;
        }
    }
}
```

**Почему это оправдано:**

1. **Срок жизни кода.** Скрипт выполнится один раз и будет удален. Затраты на чистую архитектуру не окупятся никогда.
2. **Сложность домена = 0.** Здесь нет бизнес-инвариантов, которые будут меняться. Формула `*1.2` - это разовая задача.
3. **Производительность.** Чистая архитектура с DTO, Entities, Unit of Work замедлит проход по 10 млн строк в 3-5 раз из-за лишних аллокаций и слоев.
4. **Риск.** Код изолирован в `scripts/`, не попадает в продовый кодбейз, покрыт ручным smoke-тестом.

Если бы это была фича "изменение цены с правилами скидок" - нарушение было бы недопустимо. Здесь же это **скрипт-инструмент**.

### Еще один кейс: нарушение Open/Closed ради YAGNI

Не расширять через полиморфизм, а просто if/switch, если вариантов 2 и они не будут расти.

```php
// Плохо по OCP, но оправдано
function getRenderer(string $type): Renderer {
    return match($type) {
        'pdf' => new PdfRenderer(),
        'html' => new HtmlRenderer(),
        // default -> ...
    };
}
```

Создавать фабрику, registry, plugin system ради двух рендереров - это преждевременная абстракция.

**Правило:** Нарушай SOLID осознанно и локально. Оправданное нарушение должно быть документировано комментарием `// Intentional SRP violation: one-time migration script`, изолировано в отдельном namespace и не заражать основной доменный код.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Да. Наследование — самый хрупкий способ реализовать OCP. Гораздо надёжнее закрывать класс от модификации через **композицию и внедрение поведения**.

OCP требует: класс открыт для расширения, закрыт для изменения. Ключ — расширять *что делает* объект, а не *что он есть*.

### 1. Strategy / Policy через композицию

Вместо `class PdfExporter extends Exporter` внедряем стратегию.

```php
interface ExportPolicy {
    public function export(array $data): string;
}

final class PdfPolicy implements ExportPolicy { /* ... */ }
final class CsvPolicy implements ExportPolicy { /* ... */ }

final class Exporter {
    public function __construct(private ExportPolicy $policy) {}
    
    public function run(array $data): string {
        // класс Exporter НЕ меняется при добавлении новой политики
        return $this->policy->export($data);
    }
}

// Использование
$exporter = new Exporter(new PdfPolicy());
// Добавили JsonPolicy — без изменения Exporter
```

### 2. Registry / Plugin System

Расширение через регистрацию, а не наследование.

```php
final class RendererRegistry {
    private array $handlers = [];

    public function register(string $type, callable $handler): void {
        $this->handlers[$type] = $handler;
    }

    public function render(string $type, mixed $data): string {
        return $this->handlers[$type]($data);
    }
}

$registry = new RendererRegistry();
$registry->register('pdf', fn($d) => (new PdfRenderer())->render($d));
$registry->register('html', fn($d) => (new HtmlRenderer())->render($d));
// Новый формат добавляется снаружи, класс Registry не трогаем
```

В Symfony это делается через tagged services.

### 3. Specification / Criteria

Расширяем правила, а не класс.

```php
interface OrderSpecification {
    public function isSatisfiedBy(Order $order): bool;
}

final class PaidSpecification implements OrderSpecification {}
final class HighValueSpecification implements OrderSpecification {}

final class OrderFilter {
    public function __construct(private iterable $specs) {}
    
    public function filter(array $orders): array {
        foreach ($orders as $order) {
            foreach ($this->specs as $spec) {
                if (!$spec->isSatisfiedBy($order)) continue 2;
            }
            yield $order;
        }
    }
}
```

### 4. Decorator / Pipeline

Добавляем поведение оборачиванием.

```php
interface Logger {
    public function log(string $msg): void;
}

final class FileLogger implements Logger { /* ... */ }

final class TimestampLogger implements Logger {
    public function __construct(private Logger $inner) {}
    public function log(string $msg): void {
        $this->inner->log('['.date('c').'] '.$msg);
    }
}

// Новый декоратор добавляется без изменения FileLogger
```

### 5. Configuration over Code

PHP 8.1 Enums + match — расширение через конфиг.

```php
enum ExportType: string {
    case PDF = 'pdf';
    case CSV = 'csv';
}

final class Exporter {
    public function export(ExportType $type, array $data): string {
        return match($type) {
            ExportType::PDF => $this->pdf($data),
            ExportType::CSV => $this->csv($data),
        };
    }
}
```

Новый тип = новый кейс в enum + новый private метод. Класс меняется, но это контролируемый рост, а не разрастание иерархии наследования.

**Вывод:** OCP через наследование ломается при изменении родителя. Через композицию ты расширяешь поведение, подменяя зависимости, регистрируя обработчики или добавляя декораторы. Класс остаётся неизменным, а система растёт снаружи.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет. SRP — это про **одну причину для изменения**, а не про классы. Класс — просто удобная единица агрегации в ООП, но принцип работает на любом уровне абстракции.

**SRP = One Reason to Change**

### 1. Функция / метод
Функция должна делать одну вещь и делать её хорошо.

Нарушение:
```php
function createUserAndSendEmailAndLog(array $data) {
    // валидация + создание + отправка почты + лог
}
```
Одна причина для изменения — 4.

Правильно:
```php
function createUser(array $data): User { /* только создание */ }
function sendWelcomeEmail(User $user): void { /* только почта */ }
```

### 2. Класс
Класс должен отвечать за одну область ответственности.

`Order` — инкапсулирует бизнес-правила заказа.
`OrderRepository` — отвечает за сохранение.
`OrderNotifier` — отвечает за уведомления.

### 3. Модуль / пакет / сервис
Модуль не должен смешивать домен и инфраструктуру.

Плохо: `app/` содержит и бизнес-логику, и запросы к БД, и HTTP-контроллеры в одном месте.

Хорошо: `Domain/`, `Application/`, `Infrastructure/` — каждый модуль меняется по своей причине.

### 4. Слой / команда
Даже на уровне команды: одна команда отвечает за платежи, другая — за каталог. Если один человек правит и оплату и поиск — это нарушение SRP на организационном уровне.

**Итог:** SRP — это принцип проектирования, а не синтаксический. Применяй его к функциям, классам, модулям, сервисам и даже к файлам. Чем меньше у единицы кода причин меняться, тем проще её тестировать и поддерживать.

> ⏱ 15.7s · prompt 3298 tok · output 970 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер Symfony - это не "магия", а скомпилированный PHP-код, который строится на этапе `cache:clear`.

### 1. Что происходит при компиляции контейнера

При первом запросе или `cache:clear` Symfony запускает **Compilation Pass**.

1. **Загрузка определений.** Читаются `services.yaml`, атрибуты `#[Autoconfigure]`, конфиги бандлов. Собирается список всех сервисов.
2. **Autowiring и Autoconfiguration.** Анализируются типы в `__construct`. Для каждого параметра контейнер ищет сервис с таким же типом через Reflection.
3. **Определение Public/Private.** По умолчанию все сервисы private. Публичными остаются только те, что нужны извне.
4. **Оптимизация.** Убираются неиспользуемые сервисы, делаются `lazy` прокси для тяжелых зависимостей, инлайнятся синглтоны.
5. **Генерация PHP-класса.** Symfony пишет файл `var/cache/dev/App_Kernel...Container.php` - это обычный PHP-класс с методами `getServiceId()`.

Пример сгенерированного кода упрощенно:
```php
class Container {
    private ?LoggerInterface $logger = null;

    public function getLogger(): LoggerInterface {
        if ($this->logger === null) {
            $this->logger = new Monolog\Logger('app', [new StreamHandler()]);
        }
        return $this->logger;
    }
}
```
Контейнер - это фабрика с ленивой инициализацией.

### 2. Как работает Autowiring

Autowiring = автоматическое подключение зависимостей по типу.

```php
class OrderService {
    public function __construct(
        private PaymentGateway $gateway,
        private LoggerInterface $logger
    ) {}
}
```

Symfony через Reflection видит типы параметров:
* `PaymentGateway` - ищет сервис с классом `App\Service\PaymentGateway`
* `LoggerInterface` - ищет сервис, реализующий интерфейс

Если найдено 1 совпадение - внедряет. Если 0 - ошибка. Если >1 - нужна подсказка через `#[Autowire]` или `services.yaml`.

Autoconfigure включает autowiring автоматически для сервисов в `src/`.

### 3. Что происходит при первом запросе сервиса

**Ленивая инициализация. Shared services.**

1. Ты вызываешь `$container->get(OrderService::class)`
2. Контейнер проверяет, есть ли уже экземпляр в `$this->services['OrderService']`
3. Если нет - вызывает приватный метод `doGetOrderService()` сгенерированный при компиляции.
4. Метод создает объект: `new OrderService($this->getPaymentGateway(), $this->getLogger())`
5. Рекурсивно резолвятся зависимости. `PaymentGateway` тоже создается лениво.
6. Готовый объект сохраняется в контейнер как singleton и больше не создается.

Для тяжелых сервисов Symfony создает **Proxy-объект**. Первый вызов реального сервиса происходит только при первом использовании метода.

```php
// Прокси
$logger = $container->get(LoggerInterface::class);
// На этом моменте реальный Monolog еще НЕ создан
$logger->info('test'); // <- тут происходит реальная инициализация
```

Итог: Компиляция = генерация оптимизированного PHP-класса-фабрики. Autowiring = рефлексия + сопоставление по типу. Первый запрос = ленивое создание singleton с проксированием для тяжелых зависимостей.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это запах архитектуры. В 95% случаев они говорят о нарушении SRP: два сервиса знают друг о друге слишком много и делают работу друг друга.

### 1. Сначала диагностика
Symfony на этапе компиляции кинет `Circular reference detected` для `OrderService -> PaymentService -> OrderService`.

### 2. Правильные способы разрулить

**A. Вынести общую логику в третий сервис**
Самый чистый вариант.

```php
// Было
class OrderService { public function __construct(PaymentService $payment) {} }
class PaymentService { public function __construct(OrderService $order) {} }

// Стало
class PricingCalculator { /* общая логика */ }

class OrderService { public function __construct(PricingCalculator $calc) {} }
class PaymentService { public function __construct(PricingCalculator $calc) {} }
```

**B. Использовать Setter Injection / Setter для обратной связи**
Если цикл действительно нужен, делаем одну зависимость через setter, а не конструктор.

```php
class A {
    public function __construct() {}
    public function setB(B $b): void { $this->b = $b; }
}
class B {
    public function __construct(A $a) {}
}
```
Symfony умеет это через `#[AutowireSetter]`. Сервис A создается первым, потом B внедряется через сеттер.

**C. Lazy Proxy**
Symfony автоматически создает прокси для lazy-сервисов. Если `A` нужен `B`, а `B` нужен `A`, можно сделать один из них lazy.

```php
class A {
    public function __construct(private Lazy\Proxy\B $b) {}
}
```
Прокси откладывает реальное создание `B` до первого использования, разрывая цикл на этапе конструирования.

**D. События / Domain Events**
Самый архитектурно правильный способ.

```php
class OrderService {
    public function create() {
        // ...
        $this->eventDispatcher->dispatch(new OrderCreated($order));
    }
}

class PaymentService {
    public function __construct(EventDispatcher $dispatcher) {
        $dispatcher->addListener(OrderCreated::class, [$this, 'onOrderCreated']);
    }
}
```
Сервисы не знают друг о друге вообще.

### 3. Что НЕ делать

* Не используй `$container->get()` внутри сервиса чтобы "обмануть" DI - это service locator антипаттерн.
* Не делай `if ($this->b === null)` проверки в конструкторе.

**Итог:** Цикл = проблема дизайна. Правильно - рефакторинг через выделение третьего актора или события. Как временная мера - Lazy Proxy или Setter Injection.

> ⏱ 13.8s · prompt 6270 tok · output 744 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

После регистрации пользователь должен получить 200 OK мгновенно. Письмо и индекс - это side effects, которые не блокируют основной поток.

Я бы не делал это синхронно. Есть 3 основных варианта в Symfony.

### 1. Symfony Messenger + Async Transport - рекомендуемый

**Суть:** Регистрация пишет в БД и диспатчит сообщение. Ответ пользователю сразу. Воркер обрабатывает письмо и индекс в фоне.

```php
// Domain Event
final class UserRegistered {
    public function __construct(public readonly UserId $id, public readonly string $email) {}
}

// Application Service
final class RegisterUser {
    public function __construct(
        private UserRepository $repo,
        private MessageBusInterface $bus
    ) {}
    
    public function __invoke(RegisterCommand $cmd): UserId
    {
        $user = User::register($cmd->email, $cmd->password);
        $this->repo->save($user);
        
        // Не блокируем
        $this->bus->dispatch(new UserRegistered($user->id, $user->email));
        
        return $user->id;
    }
}

// Handler
final class SendWelcomeEmailHandler {
    public function __invoke(UserRegistered $msg) {
        $this->mailer->send(...);
    }
}
```

Конфиг:
```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\UserRegistered': async
```

**Trade-offs:**
+ ✅ Ответ < 100мс
+ ✅ Надежность: retry, DLQ, failed messages
+ ✅ Масштабируемость: воркеры отдельно
- ❌ eventual consistency: письмо может прийти с задержкой
- ❌ Инфра: RabbitMQ/Redis + воркеры

### 2. Domain Events + Synchronous Handlers внутри транзакции

Если нужен строгий порядок и "все или ничего".

```php
$dispatcher->dispatch(new UserRegistered($user));
```

Хэндлеры вызываются сразу, но в рамках той же транзакции. Можно использовать `Transactional` messenger.

**Trade-offs:**
+ ✅ Простота, без очереди
+ ✅ Консистентность в рамках транзакции
- ❌ Замедляет ответ, если хэндлер тяжелый
- ❌ Ошибка в отправке письма откатывает регистрацию

### 3. Fire-and-forget через `kernel.terminate` или cron

**Вариант A - Terminate event:**
```php
public function onKernelTerminate() {
    // Выполнится после отправки ответа
}
```
**Вариант B - отложенный cron:** Пишем флаг `needs_index = true`, крон раз в минуту индексирует.

**Trade-offs:**
+ ✅ Нулевая задержка для пользователя
- ❌ Нет гарантии доставки
- ❌ Сложно ретраить, нет observability
- ❌ Terminate не работает при fatal error / 502

### Что выбрать на практике

* **Письмо + индекс поисковый** = не критично к задержке, но критично к доставке. **Messenger async + два отдельных хэндлера** с разными приоритетами. Email - retry 3 раза, индекс - idempotent.
* Если индекс должен быть актуален сразу для админки - используй **sync handler** для индекса и async для письма.
* Никогда не делай оба действия синхронно в контроллере.

Итог: Я проектирую с Domain Event `UserRegistered`, диспатчу его через Messenger в async транспорт. Это дает быстрый ответ, надежность и возможность отдельно масштабировать отправку писем и обновление индекса.

> ⏱ 15.6s · prompt 7650 tok · output 875 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

В Symfony безопасность построена слоями: аутентификация → авторизация → доступ к ресурсу.

### 1. Аутентификация
Кто ты? `security.yaml` + UserProvider + PasswordHasher.

```yaml
security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
```

`LoginFormAuthenticator`, `JWT`, `OAuth` - определяют пользователя.

### 2. Авторизация: Роли

Простая модель. Роли - это строки, которые навешиваются на пользователя.

```php
// User implements UserInterface
public function getRoles(): array {
    return array_unique([...$this->roles, 'ROLE_USER']);
}
```

Проверка:
```php
// Controller
#[IsGranted('ROLE_ADMIN')]

// или
if ($this->isGranted('ROLE_ADMIN')) {}
```

**Когда использовать роли:** Статичные, грубые уровни доступа.
`ROLE_ADMIN`, `ROLE_USER`, `ROLE_BILLING`. Это про "кто ты".

Роли дешевы, быстры, но не гибки.

### 3. Voters - тонкий контроль доступа

Voter отвечает на вопрос: *может ли конкретный пользователь сделать конкретное действие с конкретным объектом?*

```php
final class DocumentVoter extends Voter {
    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === 'EDIT' && $subject instanceof Document;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();
        /** @var Document $document */
        $document = $subject;

        // Владелец может редактировать
        if ($document->getOwner() === $user) {
            return true;
        }

        // Модератор может редактировать любой документ в своем отделе
        if ($this->isGranted('ROLE_MODERATOR') && $document->getDepartment() === $user->getDepartment()) {
            return true;
        }

        return false;
    }
}
```

Использование:
```php
#[IsGranted('EDIT', subject: 'document')]
public function edit(Document $document) {}
// или
$this->denyAccessUnlessGranted('EDIT', $document);
```

### Когда voter, когда роли?

**Ролей достаточно, когда:**
* Доступ зависит только от роли пользователя
* `ROLE_ADMIN` может всё, `ROLE_USER` - только своё
* Нет контекста объекта

**Нужен voter, когда:**
* Доступ зависит от **владельца ресурса**: `user === document.owner`
* Доступ зависит от **состояния объекта**: можно редактировать только черновик
* Доступ зависит от **отношений**: менеджер может видеть команду
* Нужна комбинация роли + бизнес-правила

**Правило:** Роли - это "может ли вообще", Voter - это "может ли с этим конкретным объектом".

В реальном проекте я использую оба: роли для грубого гейта `ROLE_ADMIN`, voters для всего остального - `VIEW`, `EDIT`, `DELETE` над сущностями. Это дает централизованную, тестируемую логику доступа.

> ⏱ 13.2s · prompt 8450 tok · output 668 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

В Doctrine Value Object это не Entity, у него нет identity. Хранить нужно **иммутабельно и всегда валидно**. Есть 3 рабочих подхода.

### 1. Embedded / `@Embeddable` - классика для простых VO

Подходит для VO из примитивов: Address, Money, Range.

```php
#[ORM\Embeddable]
final class Address
{
    public function __construct(
        #[ORM\Column(length: 100)]
        private string $street,
        #[ORM\Column(length: 50)]
        private string $city,
        #[ORM\Column(length: 10)]
        private string $zip,
    ) {
        if ($street === '') throw new \DomainException('Street required');
    }

    public function street(): string { return $this->street; }
    // без сеттеров - иммутабельность
}
```

Использование:
```php
#[ORM\Entity]
class User
{
    #[ORM\Embedded(class: Address::class)]
    private Address $address;
}
```

**Плюсы:** Нативно в Doctrine, один INSERT/UPDATE, валидация в конструкторе.
**Минусы:** VO нельзя переиспользовать как отдельную таблицу. Doctrine создает VO через reflection, обходит конструктор -> **нарушение инвариантов**.

Подводный камень: Doctrine при гидрации вызывает `__construct` без аргументов и потом сеттеры через reflection. Решение - `#[ORM\Embedded]` + `private` свойства и `__set_state`, или использовать `Attribute` + custom hydrator. На практике делают VO с nullable свойствами и валидацией в `postLoad` - компромисс.

### 2. Single Table + Custom Type - для сложных VO

Хранишь VO как JSON / сериализованную строку в одном столбце, но с валидацией.

```php
#[ORM\Entity]
class User
{
    #[ORM\Column(type: 'json')]
    private array $addressData;
    
    public function address(): Address
    {
        return Address::fromArray($this->addressData); // валидация!
    }
}
```

Или свой Doctrine Type:
```php
class AddressType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Address
    {
        $data = json_decode($value, true);
        return $data ? Address::fromArray($data) : null; // всегда валидный VO
    }
}
```

**Плюсы:** Полный контроль над созданием VO, иммутабельность гарантирована. Легко менять внутреннюю структуру VO без миграций схемы.
**Минусы:** Невозможно делать SQL-запросы по полям VO `WHERE address.city = ?`. Поиск только через full scan.

Подводный камень: `json` тип не проверяет схему. Сделай `Address::fromArray` строго типизированным и бросай исключение при невалидных данных.

### 3. Value Object как строка / нормализованные колонки через DTO

Самый прагматичный для продакшена.

`Money` храни как 2 колонки: `amount INT, currency VARCHAR`.

```php
#[ORM\Entity]
class Product
{
    #[ORM\Column]
    private int $priceAmount;
    
    #[ORM\Column(length: 3)]
    private string $priceCurrency;

    public function price(): Money
    {
        return new Money($this->priceAmount, $this->priceCurrency); // валидация в Money
    }
}
```

**Плюсы:** Полная поддержка SQL, индексы, иммутабельность.
**Минусы:** Б<|start|>но, маппинг руками.

### Что выбрать

* **Embedded** - для простых VO без логики, где нужна SQL-фильтрация по полям. Прими 1-2 компромисса с конструктором.
* **Custom Type / JSON** - для сложных VO с внутренней структурой, где важна инкапсуляция и иммутабельность. Теряешь SQL-запросы.
* **Нормализованные колонки** - для VO из 1-2 примитивов типа Money, DateRange. Лучший баланс.

**Золотое правило:** VO всегда создается через статический фабричный метод `Address::fromArray()` с валидацией, а Doctrine Type отвечает за вызов этой фабрики. Никогда не позволяй Doctrine создавать VO через пустой конструктор + сеттеры - инварианты умрут.<|eot|>

> ⏱ 17.0s · prompt 9164 tok · output 959 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Агрегат в Doctrine это не нативная концепция, Doctrine умеет в Entity и ассоциации. Поэтому мы делаем агрегат **дизайном, а не маппингом**.

### Модель: Order Aggregate

Корень `Order`, дочерние `OrderItem`. Правила: нельзя добавить Item с отрицательным qty, нельзя менять Item вне Order.

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct(
        #[ORM\Column]
        private string $customerId,
    ) {
        $this->items = new ArrayCollection();
    }

    public function addItem(ProductId $productId, int $qty): void
    {
        if ($qty <= 0) throw new \DomainException('qty>0');
        // инвариант агрегата
        $item = new OrderItem($this, $productId, $qty);
        $this->items->add($item);
    }

    public function items(): Collection { return $this->items; }
}

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private Order $order;

    #[ORM\Column]
    private string $productId;

    #[ORM\Column]
    private int $qty;

    public function __construct(Order $order, string $productId, int $qty)
    {
        $this->order = $order;
        $this->productId = $productId;
        $this->qty = $qty;
    }
}
```

**Маппинг коллекций:**
* `OneToMany` с `cascade: persist` - при сохранении Order Doctrine сохранит Items.
* `orphanRemoval: true` - если Item удален из коллекции, Doctrine удалит его из БД.
* Коллекция приватная, наружу отдаем только через методы корня `addItem/removeItem`. Никогда не даем внешний код делать `$order->items()->add()`.

### Границы транзакции и кто вызывает flush

Doctrine не знает про агрегат. Транзакция и flush живут в **Application слое**, а не в домене.

```php
final class OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Order $order): void
    {
        $this->em->persist($order);
        // flush НЕ вызываем здесь
    }

    public function get(OrderId $id): Order { ... }
}
```

Application Service - единственный владелец транзакции:

```php
final class PlaceOrder
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(PlaceOrderCommand $cmd): void
    {
        $this->em->beginTransaction();
        try {
            $order = Order::create($cmd->customerId);
            foreach ($cmd->lines as $line) {
                $order->addItem($line->productId, $line->qty);
            }

            $this->orders->save($order);
            $this->em->flush(); // один flush на весь агрегат
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
```

**Правила:**
* **flush вызывается один раз** за транзакцию, после всех изменений агрегата. Doctrine сам посчитает diff коллекции.
* Никогда не делай flush внутри доменного метода или репозитория - ты теряешь атомарность.
* Агрегат должен быть загружен целиком. Для больших коллекций используй `ExtraLazy` коллекции или разбивай агрегаты.

**Подводные камни:**
* Doctrine загружает всю коллекцию в память. Для Order с 10k items - смерть. Лимит агрегата 1-100 сущностей.
* `cascade persist` позволяет случайно сохранить дочерние сущности вне корня. Держи контроль через приватную коллекцию.
* `orphanRemoval` + `clear()` коллекции = массовые DELETE. Делай через доменные методы.

Итог: Корень инкапсулирует коллекцию и инварианты, репозиторий только persist, а flush и транзакция - ответственность Application Service, одного на весь агрегат.

> ⏱ 19.7s · prompt 10154 tok · output 1153 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

N+1 в Doctrine это классика: одна сущность загружена, а связанные коллекции/реляции подтягиваются по одному запросу на каждую.

### Диагностика

1. **Symfony Profiler / Doctrine Debug Toolbar**
   Количество запросов и время. Если видишь 1 запрос на `SELECT ... FROM order` и потом 87 запросов `SELECT ... FROM order_item WHERE order_id = ?` - это N+1.

2. **SQL Logger в dev**
   ```php
   $logger = new DebugStack();
   $em->getConfiguration()->setSQLLogger($logger);
   // после запроса
   count($logger->queries);
   ```

3. **Поиск в коде**
   Любой доступ к ленивой ассоциации в цикле:
   ```php
   foreach ($orders as $order) {
       echo $order->getCustomer()->getName(); // каждый раз запрос
   }
   ```

4. **Explain + профилирование**
   В production включи лог медленных запросов PostgreSQL. N+1 дает много одинаковых запросов с разными ID.

### Способы решения и trade-offs

**1. EAGER JOIN / FETCH JOIN в DQL**
```php
$qb->select('o', 'c', 'i')
   ->from(Order::class, 'o')
   ->leftJoin('o.customer', 'c')
   ->leftJoin('o.items', 'i')
   ->where('o.id = :id');
```
*+* Один запрос, быстро для небольшого графа.
*-* Картезианский взрыв. 1 заказ + 10 items + 1 customer = 10 строк, Doctrine дедуплицирует, но память растет. Не масштабируется на большие коллекции.

**2. `fetch="EAGER"` в маппинге**
Ставится на ассоциацию. 
*+* Просто.
*-* Глобально, невозможно отключить когда не нужно. Убивает производительность в других местах.

**3. `JOIN` + DTO / Partial Objects**
```php
$qb->select('NEW App\DTO\OrderDto(o.id, c.name, i.qty)')
   ->from(...);
```
*+* Точный набор данных, нет гидрации лишних полей, нет N+1.
*-* Ручное маппинг, не обновляешь сущности.

**4. Batch Size / Extra Lazy**
```php
#[ORM\OneToMany(..., fetch="LAZY")]
#[ORM\BatchSize(size: 20)]
```
Doctrine будет грузить items пачками по 20 ID за один запрос.
*+* Разрывает N+1 на N/20+1.
*-* Все равно много запросов, не решает проблему, только смягчает.

**5. `iterate()` + ручной fetch**
Для больших списков используй итератор и отдельный запрос на коллекцию.
*+* Контроль над памятью.
*-* Сложнее код.

**6. Предзагрузка через `Query::HINT_FORCE_PARTIAL_LOAD` / `MEMBER OF`**
```php
$orders = $repo->findByCustomer($customer);
$items = $itemRepo->findBy(['order' => $orders]); // один запрос на все
```
*+* Два запроса вместо N+1.
*-* Логика вынесена из репозитория.

**7. Пагинация + ограничение глубины**
Не грузи все items сразу. Пагинация на уровне агрегата.

### Что выбирать

* **Для чтения экрана деталей** - FETCH JOIN с ограничением глубины 1-2 уровня.
* **Для списка** - DTO + один запрос с join.
* **Для больших коллекций** - Extra Lazy + batch size или отдельный запрос в репозитории.
* Никогда не оставляй ленивую загрузку в цикле шаблона Twig.

Trade-off общий: **удобство гидрации сущностей vs контроль SQL**. Чистый DDD требует агрегат полностью в памяти, а это часто противоречит производительности. На практике читалки делают DTO, запись - через агрегат.

> ⏱ 17.2s · prompt 11184 tok · output 931 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Бизнес-валидация в коде защищает инварианты, но база данных — это **последний рубеж обороны**. Код ошибается, баги проходят в прод, кто-то подключится напрямую, миграция пойдет мимо приложения. БД должна гарантировать, что невалидное состояние физически невозможно.

Я использую оба уровня, но для разного.

### Зачем БД, если есть валидация в коде

1. **Race conditions.** Два запроса одновременно проходят валидацию в PHP, а потом пишут конфликтующие данные. `SELECT ... FOR UPDATE` помогает, но constraint гарантирует атомарность.
2. **Обход приложения.** Миграции, админка, скрипты, прямой SQL.
3. **Эволюция кода.** Завтра кто-то добавит новый endpoint и забудет про валидацию. Constraint не даст сохранить мусор.
4. **Документация.** Схема БД — источник правды о том, что разрешено.

### Средства PostgreSQL, которые использую

**1. NOT NULL, UNIQUE, CHECK - база**
```sql
ALTER TABLE orders ADD CONSTRAINT orders_total_check CHECK (total >= 0);
ALTER TABLE users ADD CONSTRAINT users_email_unique UNIQUE (email);
```
Использую всегда для простых инвариантов.

**2. Foreign Keys с ON DELETE RESTRICT/SET NULL**
Гарантируют связность. Никогда не делаю `ON DELETE CASCADE` в БД — каскад управляю в домене.

**3. Partial Indexes и Exclusion Constraints**
```sql
CREATE UNIQUE INDEX one_active_cart_per_user 
ON carts(user_id) WHERE status = 'active';
```
Бизнес-правило "у пользователя один активный корзины" невозможно выразить в PHP без race.

**4. Generated Columns**
```sql
ALTER TABLE order_items ADD total_price numeric 
GENERATED ALWAYS AS (qty * unit_price) STORED;
```
Не дублируем вычисления.

**5. Triggers / Functions для сложных инвариантов**
```sql
CREATE OR REPLACE FUNCTION check_order_total() RETURNS trigger AS $$
BEGIN
  IF NEW.total <> (SELECT SUM(qty*price) FROM order_items WHERE order_id = NEW.id) THEN
    RAISE EXCEPTION 'total mismatch';
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
```
Использую редко, только когда инвариант должен проверяться на уровне БД независимо от приложения.

**6. Domains / Custom Types**
```sql
CREATE DOMAIN email AS text CHECK (value ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');
```
Централизованная валидация типов.

### Где граница

* **Код:** Бизнес-инварианты, которые требуют чтения нескольких агрегатов, сложной логики, доменных правил. `Order::addItem` проверяет лимиты, скидки.
* **БД:** Структурные инварианты, уникальность, целостность ссылок, неотрицательность, невозможность NULL там где он запрещен.

Идеальный стек: Domain Validation в PHP для UX и быстрых ошибок + Constraints в PostgreSQL как safety net. Если constraint сработал — у тебя баг в приложении, который нужно фиксить немедленно.

> ⏱ 14.3s · prompt 12074 tok · output 683 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Медленный запрос в PostgreSQL - это не "сделай индекс", а системная диагностика.

### 1. Диагностика

**A. EXPLAIN ANALYZE - первый шаг**
```sql
EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)
SELECT ...;
```
Смотрю:
* `Seq Scan` вместо `Index Scan` - нет индекса или статистика плохая
* `Nested Loop` с миллионами строк
* `Buffers: shared hit/read` - много чтений с диска
* `Execution Time` - реальное время

В Symfony можно логировать через `pg_stat_statements`.

**B. Инструменты**
* `pg_stat_statements` - топ медленных запросов по total_time
* `pg_stat_user_tables` - `n_dead_tup` - бэкап? autovacuum?
* `EXPLAIN (ANALYZE, BUFFERS)` в `psql`
* `pgHero`, `Datadog`, `pgAdmin`

### 2. Типовые причины и решения

**1. Отсутствие / неправильный индекс**
```sql
-- Запрос
WHERE email LIKE 'test@%'
-- Индекс B-tree не поможет для префиксного LIKE
-- Решение: trigram индекс
CREATE INDEX users_email_trgm ON users USING GIN (email gin_trgm_ops);
```

**2. Неправильный оператор сравнения**
```php
// PHP
$qb->where('u.email = :email');
// SQL
WHERE lower(email) = lower(:email)
```
Функция над колонкой убивает индекс. Решение: `CITEXT` тип или хранить lowercased.

**3. N+1 и большие OFFSET**
```sql
SELECT * FROM orders ORDER BY id LIMIT 10 OFFSET 1000000;
```
OFFSET сканирует все 1M строк. Решение: keyset pagination:
```sql
WHERE id > :last_id ORDER BY id LIMIT 10
```

**4. Неправильная статистика**
После bulk insert статистика устарела. `ANALYZE table;`

**5. Seq Scan на малой таблице**
Нормально. Индекс медленнее для < 1000 строк.

**6. Блокировки**
```sql
SELECT * FROM pg_locks WHERE ...
```
Долгий `idle in transaction` держит lock.

**7. Неправильный тип данных**
Сравнение `timestamp` с `timestamptz`, `int` с `bigint` - implicit cast убивает индекс.

**8. JOIN без индекса на FK**
Каждая FK должна иметь индекс. `ON orders.user_id = users.id` - индекс на `orders.user_id` обязателен.

### 3. Мой процесс

1. `EXPLAIN ANALYZE` -> понять план
2. Проверить `pg_stat_statements` -> реальная частота
3. Проверить индексы `SELECT * FROM pg_indexes WHERE tablename = ...`
4. Проверить `VACUUM`/`ANALYZE` статус
5. Устранить проблему: индекс, переписать запрос, убрать функции с колонок
6. Бенчмарк до/после с `EXPLAIN ANALYZE`

**Золотое правило:** Индекс лечит симптом, а не причину. Сначала пойми план, потом добавляй индекс. И никогда не добавляй индекс на все колонки - каждый индекс замедляет INSERT/UPDATE.

> ⏱ 14.8s · prompt 12795 tok · output 706 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

Агрегат в DDD - это кластер объектов, который рассматривается как **одна транзакционная единица согласованности**. У него есть корень `Aggregate Root` через который происходит весь доступ, и инварианты агрегата должны поддерживаться атомарно.

### Что такое агрегат

* **Aggregate Root** - единственная точка входа. Все дочерние сущности и Value Objects живут внутри и недоступны извне напрямую.
* **Инварианты** - бизнес-правила, которые должны выполняться всегда внутри границы агрегата.
* **Граница транзакции** - агрегат сохраняется целиком одним `flush`. Нельзя сохранять часть агрегата отдельно.

Агрегат не равен таблице в БД и не равен сервисному слою. Это концептуальная граница.

### Как определяю границы

1. **Инвариант.** Какие объекты должны меняться атомарно?
2. **Жизненный цикл.** Кто "владеет" кем? Удаление родителя = удаление детей.
3. **Частота изменения.** Агрегат должен быть небольшим для производительности.

Правило: агрегат - это минимальная совокупность объектов, нарушение инварианта которой недопустимо.

### Пример: Order

Корректная граница:

```php
final class Order { // Aggregate Root
    private OrderId $id;
    private CustomerId $customerId;
    private Money $total;
    /** @var Collection<OrderItem> */
    private Collection $items;
    private OrderStatus $status;

    public function addItem(ProductId $productId, int $qty): void {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new \DomainException('Cannot modify paid order');
        }
        // инвариант: qty > 0
        // инвариант: сумма items = total
        $this->items->add(new OrderItem($productId, $qty));
        $this->recalculateTotal();
    }

    public function pay(Payment $payment): void {
        if ($this->total->isZero()) throw new \DomainException('Empty order');
        $this->status = OrderStatus::PAID;
        // ...
    }
}
```

*Граница:* `Order` + `OrderItem` + `Money` + `OrderStatus`.  
`Customer` **не входит** в агрегат, это отдельная сущность со ссылкой `customerId`. Иначе каждый заказ тащит весь Customer, и изменение Customer будет блокировать Order.

Репозиторий работает только с корнем:
```php
$order = $orderRepo->get($id);
$order->addItem(...);
$orderRepo->save($order); // flush один раз
```

### Типичная ошибка при выборе границ

**Агрегат слишком большой - God Aggregate**

Включать в `Order` сразу `Customer`, `Payment`, `Shipment`, `Invoice`. 
*Почему плохо:* 
* Агрегат становится тяжелым - загрузка 10k строк.
* Конфликты оптимистичной блокировки - два пользователя меняют Customer и Order одновременно.
* Невозможно изменить Payment не трогая Order.

Правильно: `Order` ссылается на `CustomerId`, `PaymentId`, `ShipmentId`. Эти агрегаты живут отдельно, связь через идентификаторы. События `OrderPaid` связывают их на уровне приложения.

**Агрегат слишком маленький - распыление инварианта**

Вынести `OrderItem` в отдельный агрегат. Тогда инвариант "сумма items = total" невозможно гарантировать атомарно - race condition.

**Итог:** Граница агрегата определяется инвариантом, а не удобством маппинга. Корень - единственный способ доступа, сохранение - целиком, размер - маленький.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Два параллельных процесса, меняющие один агрегат, это классический **Lost Update** и гонка за инвариантами.

Без защиты произойдёт так:

```
T1: читает Order v1, total = 100
T2: читает Order v1, total = 100
T1: addItem +50 -> total 150, сохраняет v2
T2: addItem +30 -> total 130, перезаписывает v2 -> потеря +50
```

В Doctrine по умолчанию будет **тихая перезапись**.

### Как проектирую защиту

**1. Optimistic Locking - мой дефолт для DDD**

Агрегат - малая единица, конфликты редки. Doctrine умеет `@Version`.

```php
#[ORM\Entity]
class Order
{
    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version = 1;

    // ...
}
```

При `flush` Doctrine генерирует:
```sql
UPDATE orders SET total = 150, version = 2 WHERE id = 1 AND version = 1
```
Если версия изменилась - `OptimisticLockException`.

В Application слое:
```php
try {
    $order = $repo->get($id);
    $order->addItem(...);
    $repo->save($order);
} catch (OptimisticLockException $e) {
    // retry или вернуть конфликт пользователю
    throw new ConflictException('Order was modified by another user');
}
```

Плюсы: нет блокировок БД, масштабируется. Минусы: конфликт решается на уровне приложения, нужен retry.

**2. Pessimistic Locking - когда конфликт критичен**

Для финансовых операций, где нельзя потерять ни одно изменение.

```php
$order = $em->createQuery(
    'SELECT o FROM App\Entity\Order o WHERE o.id = :id'
)
->setParameter('id', $id)
->setLockMode(LockMode::PESSIMISTIC_WRITE)
->getSingleResult();
```
`SELECT ... FOR UPDATE` - первый процесс держит блокировку, второй ждёт.

Плюсы: гарантия сериализации. Минусы: блокировки, дедлоки, снижение throughput.

**3. Дизайн агрегатов для минимизации конфликтов**

* Держи агрегат маленьким. Чем меньше агрегат, тем реже коллизии.
* Вынеси часто меняющиеся данные в отдельный агрегат. `Order` и `OrderItems` - лучше `Order` immutable после оплаты, а добавление items только в DRAFT.
* Используй Command Sourcing: все изменения идут через очередь, сериализованы.

**4. Idempotency и компенсирующие действия**

Для асинхронных обработчиков. Если хэндлер получил событие дважды, он не должен применить изменение дважды.

```php
if ($order->hasEventApplied($eventId)) return;
```

### Что выбираю на практике

* **По умолчанию Optimistic Locking с `@Version`** на всех агрегатах. Doctrine бросает исключение, Application Service делает retry 1-2 раза.
* **Pessimistic Write** только для критичных операций: списание средств, изменение статуса оплаты.
* **Агрегат иммутабелен после определённого состояния**. `Order` после `PAID` нельзя менять - конфликт невозможен по дизайну.
* В распределённой системе - **Saga / Outbox** вместо прямой конкуренции за запись.

Итог: Конкурентность - это не проблема БД, а проблема дизайна агрегата. Оптимистичная блокировка + маленькие агрегаты + иммутабельность после оплаты убирают 99% проблем.

> ⏱ 16.6s · prompt 13537 tok · output 835 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события в DDD - это способ сделать агрегат слаженным, но не раздутым. Агрегат публикует факт "что случилось", а не выполняет side effects сам.

### Когда и как публикую

Событие публикуется **внутри агрегата** как факт, а обрабатывается **после успешного коммита**.

```php
final class Order
{
    private array $domainEvents = [];

    public function pay(Payment $payment): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new DomainException('...');
        }
        $this->status = OrderStatus::PAID;
        $this->record(new OrderPaid($this->id, $payment->amount()));
    }

    private function record(DomainEvent $e): void
    {
        $this->domainEvents[] = $e;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

Application Service - единственный который знает про транзакцию:

```php
public function __invoke(PlaceOrderCommand $cmd): void
{
    $this->em->beginTransaction();
    try {
        $order = $this->repo->get($id);
        $order->pay($payment);

        $this->repo->save($order);
        $this->em->flush();
        $this->em->commit();

        // Публикуем ТОЛЬКО после commit
        foreach ($order->releaseEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    } catch (\Throwable $e) {
        $this->em->rollback();
        throw $e;
    }
}
```

В Symfony с Messenger это делается через Outbox pattern.

### Как обрабатываю

* **Синхронные доменные хэндлеры** - внутри той же транзакции, только если они меняют тот же агрегат. 
* **Асинхронные интеграционные события** - через Messenger + Outbox. Событие пишется в таблицу `outbox` в той же транзакции, что и агрегат, а воркер читает и публикует.

```sql
CREATE TABLE outbox (
  id UUID PRIMARY KEY,
  aggregate_type TEXT,
  aggregate_id TEXT,
  payload JSONB,
  published BOOLEAN DEFAULT FALSE
);
```
Outbox гарантирует **at-least-once** доставку.

### Какие проблемы решает

1. **SRP агрегата.** `Order` не знает про почту, индекс, аналитику.
2. **Расширяемость.** Добавить новый side effect = добавить новый хэндлер, без изменения агрегата.
3. **Консистентность границ.** Агрегат остаётся маленьким, а интеграция идёт через события.

### Какие проблемы создаёт

1. **Eventual Consistency.** Письмо придёт через секунду, индекс обновится позже. Пользователь может увидеть устаревшие данные.
2. **Сложность отладки.** Поток выполнения разбит во времени. Нужен correlation id, логирование.
3. **Транзакционность.** Опубликовать событие до commit = риск опубликовать отменённое событие. Публиковать после commit = риск потерять событие при краше между commit и publish.

### Связь с транзакцией БД

Золотое правило: **событие = часть транзакции агрегата**.

* Плохо: `$bus->dispatch()` до `flush()` - событие улетит, а транзакция откатится.
* Хорошо: запись в Outbox в той же транзакции, что и агрегат. `INSERT INTO orders ...; INSERT INTO outbox ...; COMMIT;` - атомарно.
* Идеально: Domain Events внутри агрегата хранятся в памяти, публикуются Application Service строго после `commit`.

Если нужен строгий порядок - используй Transactional Outbox + idempotent хэндлеры. Если нужен мгновенный эффект - делай синхронный хэндлер внутри транзакции, но понимай что ты увеличиваешь время ответа.

Итог: Доменные события развязывают агрегат от инфраструктуры, но требуют дисциплины с транзакциями и принятия eventual consistency.

> ⏱ 18.5s · prompt 15151 tok · output 941 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это три столпа DDD, которые определяют качество модели.

### Value Object vs Entity

**Entity** - объект с **идентичностью**. Два объекта с одинаковыми полями - это разные объекты, если у них разный ID.
* `User` с id=1 и `User` с id=1 - это один и тот же пользователь, даже если имя изменилось.
* Жизненный цикл важен. Entity мутирует во времени.
* Пример: `Order`, `Customer`, `Product`.

**Value Object** - объект с **значением**. Два объекта равны, если равны их поля. Идентичность не важна.
* `Money(100, 'USD')` равно `Money(100, 'USD')` независимо от того, где создан.
* Иммутабелен. Изменение = создание нового VO.
* Пример: `Money`, `Email`, `Address`, `DateRange`, `Color`.

Код:
```php
// Entity
class User {
    private UserId $id; // identity
    private string $name;
    public function changeName(string $name): void { $this->name = $name; }
}

// Value Object
final class Email {
    public function __construct(private string $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) throw ...
    }
    public function equals(self $other): bool { return $this->value === $other->value; }
}
```

### Как решаю, к чему отнести концепцию

Вопросы:
1. **Нужен ли жизненный цикл?** Если объект создаётся, меняется, удаляется - Entity.
2. **Есть ли естественный ID?** Если да - Entity. Если нет - VO.
3. **Может ли объект существовать без идентификатора?** `Address` - VO, `Order` - Entity.

Практический тест: если два объекта с одинаковыми данными - это один и тот же? Да -> Entity. Нет -> VO.

### Ubiquitous Language - единый язык

Это общий язык предметной области между доменными экспертами и разработчиками. Имена в коде = имена в бизнесе.

Влияет на код напрямую:

* Классы называются `Order`, а не `OrderModel` или `OrderDto`.
* Методы `pay()`, `cancel()`, `addItem()` - глаголы из бизнес-языка.
* Исключения `OrderAlreadyPaidException` - понятно бизнесу.
* Нет `getData()`, `process()`, `handle()` - это технический шум.

Ubiquitous Language заставляет модель быть честной. Если в бизнесе говорят "корзина", а ты называешь её `ShoppingCartEntity`, ты уже потерял связь с доменом.

Когда команда говорит на одном языке с клиентом, код сам становится документацией. Не нужны комментарии - имена говорят сами.

Итог: Entity = кто, Value Object = что. Ubiquitous Language - это клей, который делает модель понятной и живой.

> ⏱ 14.5s · prompt 16079 tok · output 614 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Чистая архитектура - это про направление зависимостей. Внутренние слои не знают про внешние. Бизнес-логика чистая, без фреймворков.

### Структура слоёв

```
src/
├── Domain/          # Чистый домен, без внешних зависимостей
│   ├── Model/       # Entities, Value Objects, Aggregates
│   ├── Service/     # Domain Services
│   └── Events/      # Domain Events
│
├── Application/     # Use Cases / Application Services
│   ├── Command/
│   ├── Query/
│   └── DTO/
│
├── Infrastructure/  # Реализация деталей
│   ├── Persistence/ # Doctrine Repositories
│   ├── Messaging/   # Messenger Handlers
│   ├── Http/        # Controllers, Forms, Serializers
│   └── ...
│
└── config/          # DI, Services
```

### Что в каждом слое

**Domain** - сердце. Только бизнес-правила, инварианты, Value Objects. Никаких `use Doctrine`, `use Symfony`. Чистый PHP.

**Application** - оркестрация. Use Case `CreateOrder` получает Command DTO, вызывает Domain, сохраняет через интерфейс Repository, диспатчит Domain Events. Знает про Domain, но не про то как сохраняется.

**Infrastructure** - "грязный" слой. Doctrine Entities, Symfony Controller, Messenger Handler, Mailer. Реализует интерфейсы из Application/Domain.

### Правило зависимостей

Зависимости идут **только внутрь**:

`Infrastructure -> Application -> Domain`

Domain ничего не знает ни о ком. Application знает Domain, но не знает Infrastructure. Infrastructure знает всех.

Пример:
```php
// Domain
interface OrderRepository {
    public function save(Order $order): void;
}

// Application
final class PlaceOrder {
    public function __construct(private OrderRepository $repo) {}
}

// Infrastructure
final class DoctrineOrderRepository implements OrderRepository {
    public function __construct(private EntityManagerInterface $em) {}
}
```

### Куда попадут детали

* **Doctrine** → `Infrastructure/Persistence/Doctrine`. Там `OrderDoctrineEntity`, `OrderRepositoryImpl`, маппинг.
* **Symfony-контроллер** → `Infrastructure/Http/Controller/Api`. Тонкий слой: валидация запроса → Command → Application Service → ответ.
* **Обработчик очереди** → `Infrastructure/Messaging/Handler`. Messenger Handler вызывает Application Service.

Ни один из них не попадает в Domain.

### Как следить за границами

1. **PHPStan + custom rules**. Запретить `use Doctrine` в `Domain/*`.
2. **Архитектурные тесты**. Библиотека `deptrac` или `archunit`.
```yaml
layers:
  Domain:
    - src/Domain/*
  Application:
    - src/Application/*
  Infrastructure:
    - src/Infrastructure/*
rules:
  Domain: [Application, Infrastructure] # Domain не может зависеть от них
```
3. **Code Review**. Любой `use Symfony` в Domain - красный флаг.
4. **Интерфейсы**. Application зависит от интерфейсов, реализованных в Infrastructure. Domain не знает об их существовании.

Итог: Domain - чистый, Application - тонкий оркестратор, Infrastructure - адаптеры. Зависимости только внутрь. Это позволяет менять Doctrine на MongoDB, Symfony на Slim, не трогая бизнес-логику.

> ⏱ 16.1s · prompt 16757 tok · output 711 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony + PostgreSQL я строю пирамиду: много быстрых Unit, меньше Integration, минимум E2E.

**Пирамида тестов**

1. **Unit - 70%**
   Домен, Value Objects, Domain Services, Application Services с моками.
   Тестируем чистую логику без I/O.
   ```php
   public function test_order_cannot_be_paid_twice(): void
   {
       $order = Order::create(...);
       $order->pay(...);
       $this->expectException(DomainException::class);
       $order->pay(...);
   }
   ```
   Быстро <10мс, стабильно 100%. Мокаем всё внешнее: репозитории, mailer, клиенты.

2. **Integration - 20%**
   Репозитории + Doctrine + реальная БД, Messenger handlers, Validators.
   Не мокаем БД. Использую `dama/doctrine-test-bundle` - каждый тест в транзакции, откат после.
   ```php
   #[TransactionalTest]
   public function test_order_persisted_with_items(): void
   {
       $order = Order::create(...);
       $this->orderRepo->save($order);
       $found = $this->orderRepo->get($id);
       self::assertCount(2, $found->items());
   }
   ```
   PostgreSQL в Docker via Testcontainers, или отдельный schema `test`. Быстро благодаря транзакциям.

3. **Acceptance / E2E - 10%**
   Behat / Symfony Panther. Только критичные сценарии: регистрация → оплата → письмо.
   Запускаю против поднятого app + реальный PostgreSQL в CI. Медленно, но мало.

### Что мокаю, а что нет

**Мокаю:**
* Внешние HTTP клиенты, Mailer, SMS, Payment Gateway
* Message Bus в unit тестах Application Service
* Время, Random

**Не мокаю:**
* Doctrine и репозитории в integration тестах - тестирую реальный SQL
* Валидаторы, сериализаторы
* Сам домен

### Как держать тесты быстрыми и стабильными

* **Транзакции вместо truncate.** `dama` откатывает, а не чистит БД.
* **In-memory SQLite - нет.** Для PostgreSQL-специфики нужен реальный PG. Использую один контейнер на suite.
* **Разделяй чтения и запись.** Read-only тесты можно кешировать.
* **Параллельный запуск.** `paratest` с изоляцией по schema.
* **Чистые фикстуры.** Factory Boy / `humbug/php-scoper` - создаю только нужные данные, без `fixtures:load`.
* **Стабильность:** никаких случайных данных, фиксированный `ClockInterface`, детерминированные UUID.
* **CI:** отдельный job для integration, кеширование `vendor/` и `var/cache`.

**Итог:** Unit тестирует домен, Integration тестирует маппинг Doctrine + PostgreSQL с транзакционным откатом, E2E покрывает только happy path. Мокаем границы, не мокаем БД. Так тесты остаются быстрыми ~ секунды, а не минуты, и стабильными.

> ⏱ 16.9s · prompt 17515 tok · output 778 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Два пользователя одновременно меняют одну запись = гонка за данными. В PostgreSQL под капотом MVCC, поэтому чтение не блокируется, но запись может привести к потере изменений.

**Какие проблемы**

* **Lost Update** - классика. T1 читает v1, T2 читает v1, оба меняют, последний перезаписывает первого.
* **Dirty Read / Non-repeatable Read** - читаешь данные, которые потом откатятся.
* **Inconsistent aggregate** - один процесс добавляет item в Order, второй меняет статус на Paid, инвариант нарушается.
* **Deadlock** - два процесса берут блокировки в разном порядке.

PostgreSQL сам по себе не защищает от Lost Update, он защищает от грязного чтения.

### Способы защиты

**1. Optimistic Locking - мой дефолт**

Идея: верь, что конфликтов мало, проверяй при записи.

```php
#[ORM\Version]
#[ORM\Column(type: 'integer')]
private int $version;
```
Doctrine делает `UPDATE ... WHERE id = ? AND version = ?`. Если версия изменилась - `OptimisticLockException`.

*+* Нет блокировок, высокая производительность, масштабируется.
*−* Конфликт решается в приложении: retry или ошибка пользователю.
*Когда:* DDD агрегаты, веб-формы, большинство бизнес-операций. Конфликты редки.

**2. Pessimistic Locking - блокировка сразу**

```php
$em->find(Order::class, $id, LockMode::PESSIMISTIC_WRITE);
// SELECT ... FOR UPDATE
```
Первый процесс держит строку, второй ждёт.

*+* Гарантия сериализации, нет потерянных обновлений.
*−* Блокировки, риск deadlock, снижает throughput, держит транзакцию открытой.
*Когда:* финансовые операции, списание средств, инвентаризация где важна строгая последовательность.

**3. Application-level Locking**

Redis `SETNX order:123:lock` с TTL, или `SELECT ... FOR UPDATE SKIP LOCKED` для очереди.

*+* Тонкий контроль, можно делать distributed lock.
*−* Добавляет инфраструктуру, риск забыть разблокировать.
*Когда:* распределённые воркеры, обработка одного заказа только одним воркером.

**4. Дизайн агрегата**

Сделать агрегат иммутабельным после состояния. `Order` после `PAID` нельзя менять - конфликт невозможен.

*+* Лучший способ, убирает проблему на уровне модели.
*−* Требует переосмысления домена.
*Когда:* всегда, как первый фильтр.

**5. Idempotent операции + Event Sourcing**

Операция `AddItem(orderId, itemId, requestId)` - повторный вызов с тем же `requestId` не меняет состояние.

*+* Безопасно для ретраев и асинхронности.
*−* Сложнее реализовать.
*Когда:* асинхронные обработчики, API с ретраями.

### Что выбираю

* **По умолчанию Optimistic Locking `@Version`** на всех агрегатах + retry 1-2 раза в Application Service.
* **Pessimistic `FOR UPDATE`** только для критичных финансовых участков, внутри короткой транзакции.
* **Redis lock** для очереди воркеров.
* **Дизайн** - делать агрегаты маленькими и иммутабельными в финальных состояниях.

Итог: Optimistic - быстро и масштабируемо для 95% случаев. Pessimistic - когда нельзя потерять ни одно обновление и нужна строгая сериализация.

> ⏱ 17.9s · prompt 18243 tok · output 841 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой процесс - это Git Flow упрощенный + trunk-based менталитет. Цель: безопасные релизы, быстрый фидбек, чистая история.

### 1. Ветвление

* `main` - production-ready, защищённая ветка. Деплой только из неё.
* `develop` - интеграционная ветка, nightly сборка.
* `feature/ABC-123-short-description` - от `develop`. Одна задача = одна ветка.
* `hotfix/ABC-999-critical` - от `main` для продакшена.

Почему не `feature/*` от `main`? Чтобы фичи не конфликтовали в проде раньше времени. Почему не чистый trunk-based? В enterprise с долгой интеграцией `develop` даёт буфер.

### 2. Формат коммитов - Conventional Commits

```
feat(auth): add OAuth2 login via Google
fix(order): prevent negative quantity
refactor(domain): extract Money value object
test(payment): add integration test for refund
chore(ci): bump phpstan to 1.12
```

Почему? Автоматическая генерация changelog, автоматический semver bump, понятная история. `git log --oneline` читается как changelog.

### 3. Pull Request

* PR из `feature/*` в `develop`.
* Обязательно: описание задачи, ссылка на тикет `ABC-123`, скриншоты для UI, тесты.
* CI должен проходить: `phpstan`, `phpunit`, `php-cs-fixer`, `deptrac`, `tests` на реальном PostgreSQL.
* PR не мержится сам, требуется 1 апрув от тиммейта.

### 4. Ревью

Ревью - не про запятые, а про архитектуру.
* Правильно ли выбраны границы агрегата?
* Нарушены ли слои чистой архитектуры?
* Покрыты ли инварианты тестами?
* Нет ли N+1?

Я делаю быстрый self-review перед запросом: `git diff`, прогон тестов локально.

### 5. Merge

Squash & merge в `develop`. История остаётся чистой: один коммит на фичу с сообщением `feat: ...`. Не делаю rebase в `main` - теряется история.

### 6. Релизы

Раз в неделю `develop` → `main` через релизный PR.

* Версия по semver: `git tag v1.4.2`
* Changelog генерируется из conventional commits
* `main` деплоится в staging → автотесты → прод

Hotfix: `hotfix/*` от `main` → `main` + `develop`. Параллельно.

### 7. Защита веток

* `main` и `develop` - require PR, require status checks, require linear history.
* Запрет force push.

### Почему так

* **Предсказуемость.** Любой разработчик понимает где что.
* **Безопасность.** `main` всегда деплоится. Никаких прямых пушей.
* **Скорость.** Feature ветки короткие, живут <2 дня. Длинные ветки = конфликты.
* **Автоматизация.** Conventional commits → автоматический релиз, changelog, version bump.

Альтернатива trunk-based хороша для маленьких команд с CI/CD на каждый коммит. В моём опыте с enterprise и PostgreSQL миграциями - `develop` + короткие feature ветки дают баланс скорости и стабильности.

> ⏱ 16.7s · prompt 19052 tok · output 734 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хорошее ревью - это не про "найди опечатку", а про защиту архитектуры и передачу знаний.

### На что смотрю в первую очередь

**1. Архитектура и границы**
* Нарушены ли слои чистой архитектуры? `Domain` не должен знать про `Doctrine`.
* Правильно ли выбран агрегат? Есть ли инварианты?
* Нарушен ли SRP? Метод делает 3 вещи?

**2. Безопасность и корректность**
* Гонки, N+1, SQL инъекции?
* Обработка исключений - оборачиваем чужие?
* Валидация на границах, `strict_types`?

**3. Тесты**
* Покрыты ли инварианты?
* Интеграционные тесты для репозиториев?
* Тест читается как спецификация?

**4. Читаемость**
* Имена говорят сами за себя? `pay()` а не `process()`.
* Сложная логика вынесена в методы с говорящими именами?
* Дублирование?

Кодстайл и `php-cs-fixer` - это работа автоинструментов, не ревьюера.

### Как маркирую замечания

Разделяю на блоки:

**🔴 Must fix - блокирует мердж**
Нарушение архитектуры, потеря данных, баг. Комментарий: "Это нарушает инвариант агрегата. Если..."

**🟡 Should fix - сильная рекомендация**
N+1, дублирование, непонятное имя. Комментарий: "Предлагаю вынести в Value Object, чтобы..."

**💡 Nit / Suggestion**
Стиль, мелочь. "Можно короче написать так".

Всегда объясняю **почему**, а не просто "переделай". Ревью - это менторство.

### При разногласии с автором

1. **Слушаю аргументы.** Может автор знает контекст, которого я не вижу.
2. **Спрашиваю про trade-offs.** "Я понимаю, что так быстрее, но мы получим проблему с..."
3. **Ссылаюсь на принципы, а не на вкус.** "Это нарушает SRP, потому что..."
4. **Если спор принципиальный** - обсуждаем в треде PR, при необходимости поднимаем на tech huddle. Не мержим через силу.
5. **Компромисс.** Если фича горит - делаем TODO комментарий с ссылкой на тикет рефакторинга.

Хорошее ревью оставляет код лучше и автора умнее. Плохое ревью - это список придирок.

Я всегда заканчиваю ревью вопросом автору: "Ты согласен с этим подходом?" - чтобы диалог продолжался.

> ⏱ 14.8s · prompt 19826 tok · output 565 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Сырая проблема — это не задача. Это боль бизнеса. Задача должна быть конкретной, проверяемой и без неоднозначностей.

**Я переформулирую её в задачу исполнителю:**

### Задача ABC-342: Пользователи не находят заказы в истории

**Контекст**
Пользователи жалуются в поддержку, что не могут найти заказы из прошлого. Поддержка завалена обращениями. Предположение - проблема в поиске / фильтрации / отображении.

**Цель**
Сделать так, чтобы пользователь мог за 1 действие найти любой свой заказ за последние 24 месяца.

**Что нужно сделать**

1. **Диагностика**
   * Проанализировать логи поддержки за последние 30 дней по тегу `order_not_found`.
   * Определить топ-3 сценариев, когда заказ "не находится".
   * Проверить метрики: время отклика страницы `/orders/history`, количество запросов, ошибки.

2. **Исправление**
   * Если проблема в поиске: реализовать поиск по номеру заказа, email, дате, статусу, сумме.
   * Если проблема в фильтрации: сделать дефолтный фильтр "все заказы" вместо "активные за последние 30 дней".
   * Если проблема в UX: добавить сортировку по дате DESC по умолчанию и пагинацию 20 шт.

**Критерии готовности - AC**

* [ ] Пользователь авторизован -> открывает /orders/history -> видит список всех своих заказов за последние 24 месяца, отсортированных по дате создания DESC
* [ ] Поиск по номеру заказа работает за <500мс, находит заказ за любой период
* [ ] Фильтр по статусу и диапазону дат работает корректно
* [ ] Поддержка подтверждает снижение обращений по теме `order_not_found` на 80% за 2 недели после релиза
* [ ] Добавлены тесты: unit на сервисы поиска, integration на репозиторий с реальным PostgreSQL

**Не входит в задачу**
* Изменение логики расчёта заказов
* Отправка писем

**Приоритет:** High
**Оценочное время:** 2 спринта

---

**Что обязательно включил и почему:**

* **Контекст и боль бизнеса** - чтобы исполнитель понимал зачем, а не просто "починить поиск".
* **Конкретная цель в терминах пользователя** - "найти за 1 действие" вместо "улучшить поиск".
* **Диагностика как часть задачи** - без понимания причины нельзя лечить симптом.
* **Приемочные критерии AC** - задача выполнена, когда чекбоксы отмечены. Без AC будет "готово, когда я скажу".
* **Метрики успеха** - снижение обращений на 80%. Можно проверить.
* **Границы задачи** - что не делаем, чтобы не раздувать скоуп.

Такая формулировка убирает уточнения: исполнитель знает что искать, как проверить, и когда закончить.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

До того как писать код, нужно понять причину, а не симптом. Иначе я починю поиск, а проблема будет в другом.

**Вопросы заказчику / аналитику / поддержке:**

**1. Про проблему**
* "Покажите 5 реальных обращений пользователей. Что именно они писали? Что вводили в поиск?"
* "Как пользователи пытаются найти заказ? По номеру? По дате? По товару?"
* "Это проблема только у новых пользователей или у всех?"

**2. Про данные**
* "Сколько заказов в среднем у одного пользователя за 24 месяца?"
* "Есть ли заказы старше 24 месяцев? Нужно ли их показывать?"
* "Как сейчас работает поиск - по каким полям?"

**3. Про UX**
* "Что пользователь видит сейчас на странице истории заказов? Скриншот?"
* "Какой дефолтный фильтр стоит сейчас? Активные/все?"
* "Есть ли пагинация? Сколько заказов на странице?"

**4. Про бизнес-ограничения**
* "Какой SLA по поиску? До какого времени отклика приемлемо?"
* "Нужен ли поиск по частичному номеру заказа?"
* "Можно ли показывать отмененные/возвратные заказы в общем списке?"

**5. Про успех**
* "Как вы поймете, что проблема решена? Снижение тикетов на сколько %?"
* "Кто будет тестировать? Есть ли список тестовых пользователей?"

Эти 5-7 вопросов дадут мне понимание: это баг в фильтре, медленный запрос, отсутствие индекса, или UX-проблема. Без них я могу потратить 2 дня на оптимизацию поиска, а окажется что пользователи просто не видят пагинацию.

> ⏱ 16.5s · prompt 20458 tok · output 687 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

В обоих случаях главное - не размывать текущую задачу и не делать сюрпризов заказчику.

### а) Попутный баг, не связанный с задачей

**Делаю: фиксирую, но не чиню в рамках задачи.**

* Создаю отдельный тикет `BUG-XXX` с описанием, шагами воспроизведения, влиянием.
* В текущем PR оставляю комментарий: "Обнаружил попутный баг X, вынес в BUG-XXX".
* Не чинить сейчас, потому что:
  * Расширяет скоуп без согласования
  * Увеличивает риск сломать основную задачу
  * Сложнее ревьюить и откатывать

Исключение: баг критичный и блокирует выполнение AC текущей задачи. Тогда останавливаюсь и поднимаю вопрос.

Почему так? Принцип "одна задача - один PR". Чистая история, предсказуемый релиз.

### б) Критерий приёмки противоречит цели задачи

**Делаю: останавливаюсь и эскалирую немедленно.**

Пример: цель - "пользователь может найти заказ за 1 действие", а AC требует "показывать только активные заказы за последние 30 дней". Противоречие.

* Не делаю работу "как написано".
* Пишу в PR / чат: "AC1 противоречит цели. Если показывать только 30 дней, пользователь не найдёт старый заказ. Подтвердите, пожалуйста, что правильно".
* Предлагаю варианты: убрать ограничение, изменить AC.

Почему? Потому что выполнение противоречащего AC - это формально "готово", но бизнес-проблема не решена. Я как исполнитель отвечаю за результат, а не за галочки.

**Итог:** Попутный баг - отдельно. Противоречие AC и цели - стоп и уточнение. Первое защищает скоуп, второе защищает ценность.

> ⏱ 13.2s · prompt 21518 tok · output 398 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении - это не "я сделал", а доказательство что бизнес-проблема решена.

**Мой отчёт о выполнении:**

1. **Что сделано**
   * Кратко: поиск по заказам расширен, пагинация добавлена, индекс создан.
   * Ссылка на PR, коммиты.

2. **Как проверить - Demo**
   * Видео/GIF или шаги: зайти на `/orders/history`, ввести номер заказа, увидеть результат <500мс.
   * Ссылка на staging.

3. **Метрики**
   * До: поиск по 24 месяцам = 4.2 сек, N+1 запросов 87 шт.
   * После: 320 мс, 1 запрос.
   * Тесты: 12 unit, 3 integration, покрытие 94%.

4. **Приёмка**
   * Чек-лист AC с галочками.
   * Скриншоты.

5. **Риски / Ограничения**
   * Поиск по частичному номеру пока не реализован - вне скоупа.
   * Индекс будет эффективен после `ANALYZE` через 24ч.

6. **Что дальше**
   * Мониторинг обращений в поддержку 2 недели.

**Если часть задачи не удалось:**

Отчёт меняется кардинально - честность важнее красивого отчёта.

1. **Что не сделано и почему**
   * Конкретно: "Поиск по сумме не реализован, потому что..."
   * Причина: неожиданная сложность, противоречие AC, нехватка времени.

2. **Влияние на бизнес**
   * Критично ли? Пользователь всё равно найдёт заказ? Или блокер?

3. **План remediation**
   * Вариант А: быстрый фикс за 0.5 дня
   * Вариант Б: отдельная задача `ABC-343`

4. **Обновлённый чек-лист AC**
   * Что выполнено - галочка
   * Что не выполнено - красный крест с комментарием

Я не прячу невыполненное. Лучше сказать "сделал 80%, вот почему" и получить решение, чем сдать "как будто всё готово" и получить баг в проде.

Итог: Отчёт - это про ценность и проверяемость, а не про код. Неудача - это тоже результат, который нужно коммуницировать с планом действий.

> ⏱ 14.7s · prompt 21951 tok · output 508 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

В PostgreSQL **нет команды `FORCE INDEX`** и никогда не было.

`FORCE INDEX`, `USE INDEX`, `IGNORE INDEX` - это хитеры оптимизатора из MySQL / Oracle / SQL Server. Они позволяют разработчику принудительно заставить планировщик использовать конкретный индекс.

PostgreSQL построен на принципиально другой философии: **планировщик умнее разработчика**. Вместо хитов он полагается на статистику, cost-based модель и правильную конфигурацию. Принудительно заставить использовать индекс нельзя, и это сделано намеренно.

### Почему PostgreSQL не даёт FORCE INDEX

1. Планировщик cost-based. Он выбирает план исходя из `pg_statistic`, размера таблицы, селективности, нагрузки. Принудительный индекс часто ухудшает план при изменении данных.
2. Если планировщик выбирает не тот план, проблема не в планировщике, а в:
   * устаревшей статистике `ANALYZE`
   * неправильных типах данных и неявных приведениях
   * отсутствии подходящего индекса
   * параметрах `random_page_cost`, `effective_cache_size`

Принудительный индекс скроет проблему на 2 недели, а потом взорвётся в проде.

### Что делают вместо FORCE INDEX в PostgreSQL

**1. Влияем на планировщик корректно**

```sql
-- временно отключить sequential scan для теста
SET enable_seqscan = OFF;
EXPLAIN ANALYZE ...;
SET enable_seqscan = ON;
```

Это только для диагностики, никогда в проде.

**2. Улучшаем статистику**
```sql
ANALYZE users;
ALTER TABLE users ALTER COLUMN email SET STATISTICS 1000;
```

**3. Правильные индексы**
```sql
-- частичный индекс под конкретный запрос
CREATE INDEX idx_active_orders ON orders(user_id) WHERE status = 'active';
```

**4. Переписываем запрос**
Убираем функции над индексируемой колонкой:
```sql
WHERE lower(email) = lower($1) -- убивает индекс
WHERE email = lower($1) -- использует индекс, если email хранится в lower case
```

**5. Параметры сессии**
```sql
SET local enable_indexscan = on;
```
Тоже антипаттерн, но работает для отладки.

**Вывод:** В PostgreSQL `FORCE INDEX` не нужен. Если кажется что планировщик ошибается, исправляй статистику, типы данных и запрос, а не заставляй планировщик. Принудительный выбор индекса - это технический долг, который обязательно вернётся.

> ⏱ 16.7s · prompt 22480 tok · output 662 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

`strict_types=1` **не замедляет выполнение PHP-кода ни на один процент**.

Это распространённый миф.

`declare(strict_types=1)` - это директива компиляции, а не runtime проверка.

Что происходит:

* На этапе компиляции PHP помнит, что файл строгий.
* При вызове функции/метода из строгого файла PHP **не выполняет приведение скалярных типов**.
* Без `strict_types` PHP делает неявное приведение `int` <-> `string` <-> `float`.
* С `strict_types` при несовпадении типов PHP сразу бросает `TypeError` **без приведения**.

Само бросание исключения - это работа, но она происходит только в случае ошибки типов, а не на каждом вызове. В нормальном успешном вызове разницы нет.

Бенчмарки показывают разницу в пределах погрешности измерений, < 0.1%. Накладные расходы на проверку типов и так есть в PHP 7+ благодаря скалярной типизации. `strict_types` просто меняет *правило* проверки с "привести если можно" на "сразу ошибка".

Почему люди думают что замедляет:
Потому что путают с проверкой типов в runtime. Но проверка типов в PHP и так происходит всегда. `strict_types` лишь убирает шаг неявного приведения, а не добавляет работу.

**Вывод:** Включай `declare(strict_types=1)` во всех файлах без опаски за производительность. Выигрыш - в безопасности и предсказуемости кода, проигрыша по скорости - ноль.

> ⏱ 13.5s · prompt 23050 tok · output 392 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Doctrine ORM *технически* поддерживает составные первичные ключи, но поддерживает их настолько плохо, что на практике это считается неподдерживаемым.

### Почему это больно

Doctrine построен вокруг идеи **одной сущности = один скалярный идентификатор**. Весь стек завязан на этом:

* **Identity Map** - `SplObjectStorage` внутри EntityManager хранит сущности по `id`. С составным ключом нужен сложный ключ, collisions и баги.
* **Прокси и lazy loading** - прокси генерируется по имени класса + идентификатор. С композитным ключом генерация прокси ломается.
* **Ассоциации** - `ManyToOne` / `OneToMany` ожидает один скалярный FK. С композитным FK тебе нужно маппить 2+ колонки вручную через `@JoinColumns`, это работает, но хрупко.
* **DQL / QueryBuilder** - `find($id)` ожидает один параметр. С composite PK ты передаёшь массив `['a'=>1,'b'=>2]`. `findBy`, `getReference` работают через костыли.
* **Second Level Cache, Extra Lazy Collections** - рассчитаны на скалярный идентификатор.
* **Migrations / Schema Tool** - часто генерирует некорректный DDL.

Поэтому в официальной документации это помечено как *legacy* и *discouraged*.

### Как с этим живут в реальных проектах

**1. Не живут. Делают суррогатный ключ**
Самый распространённый подход. Даже если в домене есть естественный составной ключ `order_id + item_no`, в БД добавляется `id BIGSERIAL PRIMARY KEY`, а естественный ключ выносится в `UNIQUE` constraint.

```php
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $orderId;

    #[ORM\Column]
    private int $itemNo;

    #[ORM\UniqueConstraint(columns: ['order_id','item_no'])]
}
```
Доменно ты всё ещё валидируешь уникальность `orderId + itemNo`, но Doctrine работает с одним скалярным `id`.

**2. Embeddable / Value Object как ключ**
Для редких случаев где composite PK неизбежен - `link` таблицы many-to-many с дополнительными полями. Делают отдельную сущность `OrderItemLink` с суррогатным `id`, а `order_id + product_id` - уникальным индексом.

**3. Нативный SQL**
Если очень нужен composite PK - пишут репозиторий на нативном SQL и обходят ORM.

**Итог:** Doctrine не запрещает composite PK, но экосистема его не любит. В реальном проекте всегда выбирают суррогатный `id` + `UNIQUE` на естественном ключе. Это даёт стабильный Identity Map, простые репозитории и нулевые сюрпризы при апгрейде Doctrine.

> ⏱ 18.5s · prompt 23414 tok · output 779 tok

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

Это классический "transaction script" с кучей проблем. Я бы начал с безопасности, потому что это прод.

### Проблемы

**Критичные**

1. **SQL Injection.** `WHERE id = $id` - прямая интерполяция. Хакер передаст `1; DROP TABLE orders;--`
2. **Нет транзакции.** `SELECT` + `UPDATE` без транзакции - race condition, потеря данных.
3. **Нет валидации.** `id` из запроса не проверяется на тип/существование.
4. **Бизнес-логика в контроллере.** Контроллер знает про SQL, почту, лог.

**Архитектурные**

5. **Нет доменной модели.** `$order` - массив, статус меняется строкой. Инварианты не защищены.
6. **Нарушение SRP.** Контроллер делает валидацию, доступ к БД, обновление, отправку письма, логирование.
7. **Синхронная отправка письма.** Ответ пользователю блокируется отправкой почты.
8. **Нет DI.** `DB::connect()` и `new Logger()` - невозможно тестировать, скрытые зависимости.
9. **Нет обработки ошибок.** Любой Exception упадёт с 500 и неотловленным трейсом.
10. **Нет аудита.** Кто оплатил? Когда? Почему?

### Как переделать

Начинаю с безопасности и разделения слоёв.

**1. Контроллер - тонкий**
```php
final class OrderController
{
    public function __construct(private PayOrder $payOrder) {}

    public function pay(Request $req): Response
    {
        $id = new OrderId($req->get('order_id')); // валидация
        $this->payOrder->__invoke($id);
        return new Response('ok', 202);
    }
}
```

**2. Application Service - оркестрация**
```php
final class PayOrder
{
    public function __construct(
        private OrderRepository $orders,
        private MessageBusInterface $bus,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(OrderId $id): void
    {
        $this->em->wrapInTransaction(function() use ($id) {
            $order = $this->orders->get($id);
            $order->pay(); // доменный метод
            $this->orders->save($order);
            $this->bus->dispatch(new OrderPaid($id)); // асинхронно
        });
    }
}
```

**3. Домен - инварианты**
```php
final class Order
{
    public function pay(): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new DomainException('Already paid');
        }
        $this->status = OrderStatus::PAID;
        $this->record(new OrderPaidDomainEvent($this->id));
    }
}
```

**4. Infrastructure**
* Repository на Doctrine с параметризованными запросами
* Messenger Handler для отправки письма
* Logger через PSR-3

### С чего начал бы

1. **SQL Injection и транзакция** - hotfix за 10 минут: prepared statements + транзакция.
2. **Вынести бизнес-логику в домен** - `Order::pay()` с инвариантами.
3. **Разделить ответственность** - контроллер → Application Service → Repository.
4. **Асинхронность** - письмо через Messenger.
5. **Тесты** - unit на домен, integration на репозиторий.

Почему так? Сначала закрываем дыру в безопасности, потом делаем код поддерживаемым. Иначе рефакторинг красивого кода с SQL injection бессмыслен.

> ⏱ 20.0s · prompt 24226 tok · output 878 tok

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

Эта сущность - антипаттерн. Она нарушает сразу несколько фундаментальных принципов.

### Что не так

**1. Сущность знает про инфраструктуру**
`EntityManagerInterface $em` внутри доменной сущности. Domain слой должен быть чистым, без зависимостей от Doctrine, БД, фреймворков. Это нарушение чистой архитектуры и Dependency Rule.

**2. Сущность сама себя сохраняет**
`$this->em->flush()` внутри `changeEmail()`. Сущность не должна знать как она сохраняется. Сохранение - это ответственность Application слоя / Repository. Сущность должна только менять своё состояние.

**3. Нет инкапсуляции и валидации**
`changeEmail` принимает строку без валидации email, без проверки уникальности. Инварианты домена не защищены.

**4. Нарушение SRP**
Сущность отвечает и за данные, и за персистентность, и за валидацию.

**5. Не тестируемая**
Нельзя протестировать `changeEmail` без поднятия EntityManager.

### Как исправить

**Чистая доменная сущность без инфраструктуры**

```php
// Domain/User.php - чистый PHP, без ORM
final class User
{
    private function __construct(
        private UserId $id,
        private Email $email,
    ) {}

    public static function register(UserId $id, Email $email): self
    {
        return new self($id, $email);
    }

    public function changeEmail(Email $email): void
    {
        // Инварианты, бизнес-правила
        if ($this->email->equals($email)) {
            return;
        }
        $this->email = $email;
        $this->record(new EmailChanged($this->id, $email));
    }

    public function email(): Email
    {
        return $this->email;
    }
}
```

**Value Object для Email**
```php
final class Email
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Invalid email');
        }
    }
}
```

**Сохранение вынесено в Application**
```php
final class ChangeUserEmail
{
    public function __construct(
        private UserRepository $repo,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(UserId $id, Email $email): void
    {
        $this->em->wrapInTransaction(function() use ($id, $email) {
            $user = $this->repo->get($id);
            $user->changeEmail($email);
            $this->repo->save($user);
            $this->em->flush();
        });
    }
}
```

**Repository - в Infrastructure**
```php
final class DoctrineUserRepository implements UserRepository
{
    public function __construct(private EntityManagerInterface $em) {}
    
    public function save(User $user): void
    {
        $this->em->persist($user);
    }
}
```

**Итог:** Сущность отвечает только за состояние и инварианты. Сохранение, валидация, инфраструктура - снаружи. Так домен остаётся чистым, тестируемым и независимым от Doctrine.

> ⏱ 17.7s · prompt 25133 tok · output 679 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Подход одинаковый для любой технологии: сначала понять, потом экспериментировать локально, потом масштабировать.

### Последовательность действий

**1. Контекст и ограничения - 15 минут**
Читаю задачу, SLA, риски. Что именно ломается? Какие метрики? Есть ли логи, ошибки, дашборды? Собираю факты, а не предположения.

**2. Карта неизвестного**
Выписываю что знаю и чего не знаю про стек:
* Знаю: это PostgreSQL
* Не знаю: как настроен connection pool, какие индексы

**3. Быстрый разведопрос**
Смотрю документацию, README, `docker-compose.yml`, `config/`, примеры. Ищу `/.env.example`, `Makefile`. Запускаю `php -m`, `psql --version`. Смотрю `git log --oneline -10` чтобы понять последние изменения.

**4. Локальная репродукция**
Поднимаю минимальный стенд. Не лезу сразу в прод. Если это баг - создаю минимальный reproducible case. Если это оптимизация - `EXPLAIN ANALYZE` на копии данных.

**5. Гипотезы и эксперименты**
Формирую 2-3 гипотезы. Проверяю самую дешёвую первой. Логирую, замеряю. Не делаю "на глаз".

**6. Оценка сроков**
После первого воспроизведения и понимания причины даю оценку:
* Точную если знаю как чинить
* Диапазон если есть неизвестные: 1-3 дня

**7. Коммуникация**
Пишу короткий статус: что понял, что проверяю, когда будет ясность.

### Когда просить помощи

**Прошу сразу, если:**
* Блокирующий фактор для бизнеса и я не понимаю контекст домена
* Нужно менять архитектуру / затрагивать другие команды
* Вижу риск сломать прод и нет времени разобраться

**Разбираюсь сам, если:**
* Это локальная техническая проблема в моём домене
* Есть время 2-4 часа на исследование
* Могу безопасно экспериментировать локально

Правило: **спрашивать рано, но с контекстом**. Не "что делать?", а "я проверил X и Y, получаю Z, предполагаю A, правильно ли?"

Это экономит время команды и показывает что я сделал домашку.

Итог: Сначала факты, потом гипотезы, потом эксперимент. Помощь прошу когда нужен доменный контекст или риск высок, а не когда лень читать доку.

> ⏱ 16.2s · prompt 25875 tok · output 541 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Сразу говорю правду. Чем раньше, тем меньше ущерб.

**Действия:**

1. **Стоп и оценка.** Не пытаюсь "дотянуть" за ночь. Подсчитываю реально: что уже готово, что осталось, риски.

2. **Сообщаю сейчас, а не завтра утром.** Пишу в чат/тикет: "Оценка изменилась. Планировал 1 день, по факту нужно 2 дня. Причина: [конкретно]."

3. **Предлагаю варианты:**
   * Вариант А: перенос дедлайна на послезавтра, делаем полностью
   * Вариант Б: делаем минимальный жизнеспособный вариант к завтра, остальное - потом
   * Вариант В: помогаю кто-то ещё

4. **Не обещаю "доделаю ночью".** Это путь к выгоранию и багам.

**Почему так:**
* Доверие дороже дедлайна. Лучше один раз сказать "не успеваю", чем постоянно срывать сроки.
* Менеджер может переприоритизировать, убрать часть скоупа, привлечь помощь.
* Тихий слив дедлайна = потеря контроля над ожиданиями.

Если я пообещал и понял что не успеваю - это моя ошибка в оценке. Признаю её, исправляю процесс оценки в следующий раз, но сейчас главное - честная коммуникация.

**Итог:** Сообщаю сегодня, предлагаю план Б, держу слово о качестве.

> ⏱ 13.5s · prompt 26450 tok · output 316 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Код никогда не бывает идеальным. Вопрос не "идеален ли", а "достаточно ли хорош для текущего контекста".

### Мой критерий "достаточно хорошо"

Код отдаётся когда выполнено **минимум**:

1. **Бизнес-ценность доставлена.** Задача работает, AC выполнены.
2. **Инварианты защищены.** Доменные правила не нарушаются, нет багов данных.
3. **Тесты покрывают критичные пути.** Unit на домен, integration на репозиторий.
4. **Код читаем.** Имена понятны, нет WTF-моментов.
5. **Нет критических рисков.** SQL injection, race condition, утечка данных - нет.

Остальное - это полировка.

### Баланс качество vs скорость

Я использую **контекст-зависимый подход**:

**Горячий фикс / прод горит**
Скорость > качество. Делаю минимальный фикс с комментарием `// HOTFIX`, покрываю тестом, создаю тикет на рефакторинг. Не рефакторю весь модуль ради одной строки.

**Новая фича в новом модуле**
Качество > скорость. Делаю по DDD, чистые слои, тесты. Потому что технический долг тут будет расти экспоненциально.

**Legacy код / старый модуль**
Прагматично. Не переписываю всё. Делаю минимальные изменения, покрываю тестом изменённый участок, оставляю `TODO` комментарии. Идеальный рефакторинг legacy - это отдельный проект.

**Правило "золотой середины"**

* Делай быстро, но не грязно.
* Делай хорошо, но не идеально.
* Оставляй код лучше, чем нашёл.

Если улучшение займёт 30 минут и предотвратит будущий баг - делаю сейчас. Если улучшение займёт 3 часа и даст только эстетику - откладываю.

**Как решаю на практике**

Спрашиваю себя:
* Будет ли этот код жить >6 месяцев?
* Будет ли его трогать >1 человек?
* Какой ущерб если код останется так?

Если да на все три - улучшаю. Если нет - оставляю рабочий минимум.

Итог: Достаточно хорошо = работает, безопасно, тестируемо, понятно. Идеально - враг хорошего. Баланс достигается пониманием контекста: где можно быстро, а где нужен фундамент.

> ⏱ 16.1s · prompt 26818 tok · output 505 tok

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

**Кандидат:**

Спасибо за плотное и последовательное интервью — оно было насыщенным и очень предметным. Формат мне понравился: от знакомства и самооценки к углублению в архитектуру, DDD, инфраструктуру и soft skills. Чувствовалось, что вопросы идут по нарастающей, а не просто набором абстракций.

**Что было полезным и точным**

* Вопросы про SOLID с примерами нарушения и рефакторинга, про OCP без наследования, про SRP на разных уровнях — показывают реальное понимание, а не заучивание определений.
* Тема циклических зависимостей, агрегатов в Doctrine, N+1, оптимизация PostgreSQL — это боль продакшена, а не теория.
* Вопросы про доменные события и связь с транзакцией, про value object vs entity, про границы агрегата — очень хорошо проверяют DDD на практике.
* Вопросы про git-процесс, code review, оценку сроков и коммуникацию при срыве дедлайна — показывают зрелость инженера, а не только код.

Эти вопросы были точными, потому что требовали торгового-offs, а не «правильного ответа».

**Что можно улучшить**

* Вопрос про `FORCE INDEX` в PostgreSQL — он немного вводящий в заблуждение. В PostgreSQL такой команды нет, и вопрос звучит как проверка знания MySQL. Лучше спросить «как вы влияете на план запроса, если планировщик выбирает неоптимальный план».
* Вопрос про «на сколько процентов strict_types замедляет код» — тоже ловушка на миф. Правильный ответ «ни на сколько», но формулировка провоцирует на выдумывание цифр.
* Не хватало вопросов про observability в проде: логирование, метрики, трейсинг, как дебажить инцидент ночью.
* Можно добавить практический кейс: «вот реальный план EXPLAIN, что не так?» — это быстрее показывает мышление, чем теоретические рассуждения.

**Что бы я изменил, если бы сам организовывал собеседование**

* Добавил бы один сквозной кейс. Дать небольшую проблемную кодовую базу и пройти с кандидатом полный цикл: найти N+1, нарушенный SRP, придумать границу агрегата, написать тест. Это лучше показывает связность мышления, чем 30 отдельных вопросов.
* Разделил бы технические вопросы и вопросы про процесс/коммуникацию в отдельные блоки с явным переключением.
* Дал бы кандидату возможность задавать уточняющие вопросы к задаче, а не только отвечать. Это тоже навык.

В целом интервью было сильным, глубоким и практико-ориентированным. Спасибо за возможность обсудить реальные проблемы, а не только определения.

> ⏱ 17.7s · prompt 27391 tok · output 624 tok

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

**Кандидат:**

Спасибо за возможность добавить контекст, который не влез в технические вопросы.

**Сильные стороны**

Я люблю работать с messy legacy и доводить его до состояния, где им приятно пользоваться. Мне нравится находить инварианты бизнеса, которые спрятаны в тикетах и переписке поддержки, и переводить их в код — в доменные модели, которые защищают данные сами по себе. Хорошо чувствую баланс между чистой архитектурой и дедлайнами: умею делать «достаточно хорошо» сейчас и оставлять понятные следы для рефакторинга потом.

**Подход к работе**

Я начинаю с понимания «зачем», а не «что сделать». Перед кодом — контекст, метрики, риски. В коде — сначала безопасность и корректность, потом читаемость, потом красота. Всегда оставляю тесты как документацию, а не как галочку. В команде ценю прямую коммуникацию: если оценка меняется — говорю сразу, если есть конфликт дизайна — обсуждаю, а не мержу через силу.

**Принципы**

* Код — это коммуникация с будущими коллегами. Имена важнее комментариев.
* Домен важнее фреймворка. Symfony/Doctrine — это детали, бизнес — это суть.
* Технический долг — это кредит, а не грязь. Беру его осознанно и отдаю с процентами.
* Производственный код должен быть скучным и предсказуемым. Креативность — в дизайне, а не в хаках.

**Что важно знать**

Я не ищу идеальный стек, я ищу интересные проблемы. Мне нравится, когда задача требует связать бизнес-домен, производительность БД и UX одновременно. И я быстро вхожу в незнакомый стек — сначала карта неизвестного, потом минимальный воспроизводимый пример, потом решение.

Если команда ценит честность в оценках, ревью как менторство и архитектуру, которая служит бизнесу, а не ради архитектуры — мы найдем общий язык.

> ⏱ 15.5s · prompt 28007 tok · output 420 tok

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

**Кандидат:**

Мой родной стек — **PHP 8.3 + Symfony 7 + Doctrine ORM + PostgreSQL + Messenger + API Platform**. Это тот стек, в котором я работаю последние годы и который позволяет быстро строить поддерживаемые enterprise-продукты.

Если бы я выбирал стек для нового greenfield проекта и нужно было защищать выбор перед командой, я бы защищал именно этот стек, но с уточнением ролей.

**Почему PHP/Symfony**

* **Скорость доставки.** Symfony — зрелый фреймворк с понятной структурой, DI-контейнером, бандлами. Мы быстрее стартуем, чем на чистых микрофреймворках.
* **Экосистема.** Doctrine, API Platform, Messenger, Validator, Security — всё интегрируется без трения. Не нужно собирать пазл из 20 библиотек.
* **DDD-friendly.** Symfony не навязывает архитектуру. Легко построить Clean Architecture с Domain/Application/Infrastructure.
* **Команда.** PHP-разработчиков много, найм проще, чем на экзотике. Код читаем, onboarding быстрый.
* **PostgreSQL как база.** ACID, JSONB, CTE, partial indexes, exclusion constraints — идеален для доменных инвариантов. С Doctrine он даёт баланс ORM + SQL.

**Стек в деталях**

* **Backend:** PHP 8.3, Symfony 7, strict_types=1, PHPStan level 9, Rector
* **DB:** PostgreSQL 16 + pg_stat_statements + Dama Doctrine Test Bundle
* **Async:** Symfony Messenger + RabbitMQ/Redis, Outbox pattern
* **API:** API Platform 4 для REST/GraphQL, OpenAPI
* **Infra:** Docker, Kubernetes, Prometheus + Grafana, Sentry
* **Тесты:** PHPUnit, Behat, Testcontainers для PG

**Когда бы я выбрал другое**

Если нужен real-time, stateful вычисления, или команда из Go/Node — выбор будет иным. Но для B2B SaaS, e-commerce, финтеха с сильной доменной логикой, где важны транзакции, долгосрочная поддержка и скорость команды — PHP/Symfony + PostgreSQL это оптимальный компромисс между скоростью разработки, надёжностью и наймом.

Я выбираю стек не потому что он «крутой», а потому что он позволяет делать чистый домен, быстрые релизы и спать спокойно в проде.

> ⏱ 17.4s · prompt 28466 tok · output 575 tok

---
