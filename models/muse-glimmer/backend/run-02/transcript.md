# Собес: `muse-glimmer` · роль: backend — run-02

- Дата: 2026-10-09T01:48:59.818928+00:00
- Сервер: http://nserv:11434 · keep_alive: 10m · think: off · num_ctx: 131072
- Вопросов: 35

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Я backend-разработчик с ~7 лет опытом, преимущественно на PHP. Основные проекты — высоконагруженные B2B сервисы и SaaS-платформы: e-commerce, биллинг, системы аналитики и интеграции.

**Опыт:**
С PHP работаю с версии 7.1 до 8.3, в продакшене. База — Symfony 5/6/7, иногда чистый PHP с собственной архитектурой. 
На проекте e-commerce на Symfony + Doctrine ORM + PostgreSQL делал рефакторинг монолита в модульную структуру, выносил доменную логику в DDD-ядро, оптимизировал запросы и кеширование через Redis. 
Вел команду из 3 бэкенд-разработчиков, писал ADR, настраивал CI/CD на GitLab CI, покрывал критичные пути unit и integration тестами на PHPUnit.
Смежные технологии: PostgreSQL, MySQL, Redis, RabbitMQ, Docker, Kubernetes, Nginx, Elasticsearch, API Platform, OpenAPI, JWT/OAuth2.
В целом стараюсь писать код по принципам чистой архитектуры: отделение домена от инфраструктуры, инверсия зависимостей, тестируемость.

**Самооценка:**

**PHP 8 — 9/10**
Уверенно использую union types, attributes, match, readonly-классы и fibers, но есть нюансы с JIT в редких случаях.

**Symfony — 8/10**
Ежедневно использую компоненты HttpKernel, Console, Validator, Messenger, Security, умею собирать свой bundle, но не помню все тонкости старого legacy.

**Doctrine — 7/10**
Хорошо знаю ORM, DQL, миграции, second level cache, но сложные оптимизации с N+1 и native queries приходится вспоминать.

**PostgreSQL — 8/10**
Пишу сложные запросы, индексы, CTE, транзакции, партиционирование, но глубокие тонкости planner-а изучаю по мере необходимости.

**DDD — 7/10**
Применяю Value Object, Aggregate, Domain Events и слои Application/Domain/Infrastructure в реальных проектах, но не всегда довожу до идеала с Bounded Contexts.

**Чистая архитектура — 7/10**
Соблюдаю разделение и зависимости внутрь, но иногда допускаю компромиссы ради сроков.

**Тестирование — 8/10**
Пишу unit, integration и e2e тесты на PHPUnit, использую mocks, fixture-ы, Testcontainers, но покрытие не всегда 100%.

**Git — 9/10**
Уверенно работаю с ветвлением, rebase, cherry-pick, resolve конфликтов и code review, но редко пользуюсь продвинутым plumbing.

Готов подробнее рассказать про любой из пунктов.

> ⏱ 12.9s · prompt 147 tok · output 669 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Отличные вопросы. Для чистого и надёжного кода в PHP 8+ я бы выделил 6 вещей, которые реально меняют качество.

### 1. Строгая типизация: union types, mixed и never

Явные типы уменьшают класс ошибок на этапе написания.

```php
function findUser(int|string $id): User|null
{
    // ...
}

function process(): never
{
    throw new RuntimeException('unreachable');
}
```
`never` помогает статическим анализаторам понять, что код после вызова недостижим.

### 2. Constructor property promotion + readonly

Меньше бойлерплейта, иммутабельность по умолчанию.

```php
final readonly class Money
{
    public function __construct(
        public MoneyAmount $amount,
        public Currency $currency
    ) {}

    public function add(self $other): self
    {
        // ...
    }
}
```
Объект нельзя изменить после создания — меньше сайд-эффектов.

### 3. Match expression

`match` строже `switch`: соответствие строгое, обязательное покрытие.

```php
$status = match ($order->state) {
    OrderState::New => 'new',
    OrderState::Paid => 'paid',
    OrderState::Cancelled => 'cancelled',
    default => throw new LogicException('Unknown state'),
};
```
Нет проблем с неявным приведением типов и забытым `break`.

### 4. Nullsafe operator и named arguments

Меньше защитного кода, читаемее вызовы.

```php
$city = $user?->profile?->address?->city; // nullsafe chain

$date = new DateTimeImmutable(
    timezone: new DateTimeZone('UTC'),
    // named args делают вызов самодокументируемым
);
```

### 5. Attributes для метаданных

Убираем «магические» строки, делаем код декларативным.

```php
#[Route('/users/{id}', methods: ['GET'])]
final class GetUserHandler
{
    public function __construct(
        #[Autowire(service: 'user.repository')]
        private UserRepository $users,
    ) {}
}
```
То же используется для валидации, сериализации, тестов.

### 6. Fiber в PHP 8.1+ для кооперативной многозадачности

Позволяет писать асинхронный код в процедурном стиле без колбэков.

```php
$fiber = new Fiber(function (): void {
    $a = Fiber::suspend('first');
    $b = Fiber::suspend('second');
});

$fiber->start(); // 'first'
$fiber->resume(); // 'second'
$fiber->resume(); // finished
```
На практике это база для асинхронных клиентов HTTP/DB.

**Итого:** типизация + иммутабельность + `match` + nullsafe + attributes + fibers дают меньше багов, более выразительный и поддерживаемый код.

> ⏱ 7.7s · prompt 782 tok · output 619 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Хороший вопрос, давай по порядку.

### 1. Как работает `strict_types`

PHP по умолчанию в режиме **слабых типов**: при вызове функции/метода аргументы неявно приводятся к объявленному типу.

```php
declare(strict_types=0); // default

function add(int $a, int $b): int { return $a + $b; }

add('2', '3'); // 5, строки приведены к int
```

`strict_types=1` делает проверки **строгими только в текущем файле**.

```php
declare(strict_types=1);

function add(int $a, int $b): int { return $a + $b; }

add('2', '3'); // TypeError
add(2, 3);     // 5
```

Важно: strictness проверяется **на границе вызова**. Если вызывающий файл без strict, а вызываемый со strict — приведение всё равно произойдёт.

### 2. Приведения типов: что происходит

* Скалярные типы: `int`, `float`, `string`, `bool` — приводятся по правилам PHP.
* `array` и `object` — приведение почти всегда в ошибку.
* `mixed` в PHP 8+ — отключает проверку.
* `null` — допустим только если в сигнатуре `?type` или `type|null`.

```php
function flip(bool $flag) {}

flip(1);    // true в weak mode, TypeError в strict
flip('');   // false в weak mode
```

### 3. Типичные ловушки

**A. Строка "0" vs int 0**  
В weak mode `'0'` → `0`, в strict — ошибка. Часто ломает парсинг ID из запроса.

**B. `==` vs `===`**  
`'0' == false` → true, `'0' === false` → false. Баг-классика.

**C. Возврат `null` без `?type`**  
```php
function find(): User { return null; } // TypeError
```

**D. Коллекции и generics**  
PHP не имеет generics в runtime. `array<int>` проверяется только статическими анализаторами.

**E. `iterable` и `callable`**  
`iterable` принимает array|Traversable, `callable` — очень широкий тип. Легко пропустить неожиданный тип.

### 4. Где типизация спасает

```php
declare(strict_types=1);

final class Money
{
    public function __construct(
        public int $cents,
        public string $currency
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->cents + $other->cents, $this->currency);
    }
}
```
Строгие типы не дадут случайно передать `'100'` вместо `100` и сломать расчёты.

### 5. Где удивляет

```php
declare(strict_types=0);

function isEmpty(string $value): bool
{
    return $value === '';
}

isEmpty(null); // PHP приведёт null к '' → true, хотя ожидаешь ошибку
```

Или:

```php
function ratio(float $a, float $b): float { return $a / $b; }

ratio(2, '2'); // работает в weak mode, в strict — TypeError
```

**Вывод:** `strict_types=1` + `===` + статический анализ Psalm/PHPStan — базовая гигиена. Она не убирает все проблемы, но делает многие баги невозможными уже на этапе вызова.

> ⏱ 10.0s · prompt 1453 tok · output 794 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Отличный вопрос. Исключения — это часть контракта. Я проектирую их осознанно.

### 1. Иерархия исключений

Делю на доменные и технические.

```
DomainException
 ├─ ValidationException
 ├─ NotFoundException
 └─ ConflictException

ApplicationException
 └─ ...

InfrastructureException
```

Доменные исключения — **бизнес-правила**, их можно показывать пользователю.  
Технические — проблемы инфраструктуры, их нужно логировать и мапить в 5xx.

В коде:

```php
final class UserNotFoundException extends DomainException
{
    public function __construct(public string $userId)
    {
        parent::__construct("User $userId not found");
    }
}
```

### 2. Контракт метода

Явный контракт: **что метод бросает**.

```php
/**
 * @throws UserNotFoundException
 * @throws PaymentGatewayException
 */
public function charge(UserId $id, Money $amount): Receipt
```
Если исключение — часть бизнес-логики, документирую. Если это programmer error — пусть будет `LogicException`/`TypeError`, не ловлю.

### 3. Оборачивание чужих исключений

Никогда не проглатываю. Оборачиваю в свою иерархию, сохраняя cause.

```php
try {
    $this->paymentGateway->charge($payload);
} catch (GuzzleException $e) {
    throw new PaymentGatewayException('Charge failed', previous: $e);
}
```

Так стек не теряется, а верхние слои видят только свои типы.

### 4. Глобальный обработчик

В Symfony — `EventListener` на `kernel.exception`.  
Маппинг:

* `ValidationException` → 422
* `NotFoundException` → 404
* `InfrastructureException` → 503 + alert

Логирую с контекстом, но не дублирую логи в каждом catch.

### 5. Антипаттерны

**A. Проброс всего подряд**  
`catch (Exception $e) { throw $e; }` — бессмысленно.

**B. Исключения как управление потоком**  
Не делаю `if` через `try/catch`. Исключение — исключительная ситуация.

**C. Скрытие причины**  
`throw new DomainException('Error')` без `previous`. Теряется стек.

**D. Слишком общая иерархия**  
Один `AppException` на всё. Потом невозможно различить 400 и 500.

**E. Молчаливый catch**  
Пустой `catch {}` — самый опасный антипаттерн.

**F. Возврат null вместо исключения**  
`findUser()` возвращает `null` — вызывающий забывает проверить. Лучше `UserNotFoundException` или `Optional`.

Итого: доменные исключения — явный контракт, технические — оборачиваются, глобальный обработчик переводит их в HTTP/лог. Никаких скрытых ошибок и исключений для управления логикой.

> ⏱ 8.0s · prompt 2296 tok · output 636 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему опыту, чаще всего нарушается **Single Responsibility Principle — SRP**.  
Класс берёт на себя и бизнес-логику, и доступ к БД, и формирование ответа, и логирование. Почему? Потому что быстрее «добавить ещё один метод сюда», чем выделять новый класс. В итоге получается God Object.

### Типовой пример нарушения

```php
final class UserService
{
    public function register(array $data): void
    {
        // 1. валидация
        if (empty($data['email'])) { throw new \Exception('...'); }

        // 2. работа с БД
        $pdo = new PDO(...);
        $stmt = $pdo->prepare('INSERT INTO users ...');
        $stmt->execute($data);

        // 3. отправка письма
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->send('Welcome!', $data['email']);

        // 4. логирование
        file_put_contents('/tmp/log.txt', 'user created');
    }
}
```
Один класс знает про валидацию, persistence, почту и логи. Изменение в одном месте ломает всё.

### Рефакторинг

Разделяем ответственность:

```php
final class UserRegistration
{
    public function __construct(
        private Validator $validator,
        private UserRepository $users,
        private Mailer $mailer,
        private Logger $logger,
    ) {}

    public function execute(array $data): void
    {
        $this->validator->assertValid($data);
        $user = User::create($data);

        $this->users->save($user);
        $this->mailer->sendWelcome($user);
        $this->logger->info('User created', ['id' => $user->id]);
    }
}
```

* `User` — доменная сущность, только инварианты.
* `UserRepository` — только persistence.
* `Mailer`, `Logger` — инфраструктура.
* `UserRegistration` — оркестратор use-case, без деталей реализации.

Теперь класс меняется по одной причине: логика регистрации. Тестировать легко моками, соблюдать Open/Closed и Dependency Inversion проще.

**Итого:** SRP нарушается чаще всего из-за спешки и «удобства». Лечится выносом ролей в отдельные классы и явными границами: Domain — Application — Infrastructure.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Можно, и иногда это оправдано. SOLID — это指南, а не догма. Главное — понимать, какой компромисс ты берёшь.

**Когда оправдано нарушать:**

* Прототип / MVP, где скорость важнее архитектуры.
* Производительность: иногда одно нарушение SRP экономит оверенж вызовов.
* Фреймворковые ограничения.

**Пример оправданного нарушения SRP**

В высоконагруженном API нужен сверхбыстрый health-check эндпоинт. Выносить проверку БД, Redis, очереди в отдельные сервисы и собирать через оркестратор — правильно архитектурно, но добавляет латентность на каждый вызов.

Оправданный компромисс:

```php
final class HealthCheck
{
    public function __construct(
        private PDO $db,
        private Redis $redis
    ) {}

    public function __invoke(): array
    {
        // одна ответственность "быстро проверить всё",
        // но внутри класс знает и про БД, и про Redis
        $dbOk = $this->db->query('SELECT 1')->fetchColumn() === '1';
        $redisOk = $this->redis->ping() === '+PONG';

        return ['db' => $dbOk, 'redis' => $redisOk];
    }
}
```
Нарушение SRP здесь приемлемо: класс маленький, стабильный, не растёт, и выигрыш в латентности важнее чистоты.

**Правило:** нарушай осознанно, документируй почему, и держи такой код изолированным. Как только компромисс начинает расти — рефакторь.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Да, OCP можно реализовать не только наследованием. Суть принципа — **расширять поведение, не меняя существующий код**. Наследование — лишь один из способов.

### 1. Стратегии / полиморфизм через композицию

Вместо наследования — подменяемые стратегии.

```php
interface PaymentStrategy { public function pay(Money $m): void; }

final class PaypalStrategy implements PaymentStrategy { /* ... */ }
final class StripeStrategy implements PaymentStrategy { /* ... */ }

final class Checkout
{
    public function __construct(private PaymentStrategy $strategy) {}
    public function checkout(Money $m): void { $this->strategy->pay($m); }
}
```
Новый способ оплаты добавляется новым классом, `Checkout` не меняется.

### 2. Плагины / Registry

Регистрация хендлеров во время bootstrap.

```php
final class HandlerRegistry
{
    /** @var array<string, Handler> */
    private array $handlers = [];

    public function register(string $type, Handler $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    public function handle(string $type, $data)
    {
        return $this->handlers[$type]->handle($data);
    }
}
```
Расширение без правки ядра.

### 3. Specification / Rule Objects

Набор правил, которые можно комбинировать.

```php
interface Spec { public function isSatisfiedBy(object $o): bool; }

final class AndSpec implements Spec
{
    public function __construct(private Spec $a, private Spec $b) {}
    public function isSatisfiedBy(object $o): bool
    {
        return $this->a->isSatisfiedBy($o) && $this->b->isSatisfiedBy($o);
    }
}
```

### 4. Event-driven / коллбеки

Ядро кидает события, новые слушатели подписываются.

```php
$dispatcher->addListener('order.placed', new SendEmailListener());
$dispatcher->addListener('order.placed', new GrantLoyaltyPointsListener());
```

### 5. Конфигурация + DI

Поведение меняется конфигурацией, а не кодом.

```yaml
services:
    app.notification:
        class: NotificationSender
        arguments:
            $channels: ['email', 'sms', 'push']
```

**Итого:** OCP про открытость к расширению через композицию, стратегии, плагины, события и DI — а не только через `extends`. Наследование часто приводит к хрупкой иерархии, композиция — гибче.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет. SRP — это про **причину изменения**, а не про классы.

Принцип гласит: модуль должен иметь **одну причину для изменения**. Модуль — это любой кусок кода с чёткими границами: функция, метод, класс, пакет, сервис, даже микросервис.

### Где ещё применяется SRP

**1. Функция / метод**
```php
// Нарушение
function processOrder(array $data) {
    validate($data);      // валидация
    saveToDb($data);      // персистентность
    sendEmail($data);     // сайд-эффект
}

// SRP
function processOrder(array $data) {
    $order = Order::fromArray($data);
    $this->handler->handle($order);
}
```
Функция должна делать одно — оркестрировать, а не и валидировать и писать в БД.

**2. Пакет / модуль**
Пакет `Infrastructure\Doctrine` не должен содержать бизнес-правила. Он меняется только если меняется способ хранения, а не домен.

**3. Микросервис**
Сервис `Billing` не должен отвечать и за `Reporting`. Иначе любое изменение в отчётах ломает биллинг.

**4. Класс — частный случай**
Класс `UserService`, который и валидирует, и сохраняет, и шлёт письма — классическое нарушение SRP на уровне класса.

Итого: SRP про границы ответственности. Класс — удобное место для его соблюдения, но не единственное.

> ⏱ 6.4s · prompt 2974 tok · output 507 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

В Symfony контейнер DI — это скомпилированный PHP-класс, а не рефлексия на каждый запрос.

### 1. Что происходит при компиляции контейнера

При `cache:warmup` Symfony собирает все определения сервисов из:

* `services.yaml` / атрибуты `#[AsService]`
* конфигураций bundle-ов
* авто-регистрации по PSR-4

Компилятор строит граф зависимостей и генерирует класс `ContainerXXXX.php` в `var/cache`.  
В нём каждый сервис — это приватный метод `getXService()` с уже «вшитой» логикой создания.

Пример упрощённо:
```php
class Container {
    private ?LoggerInterface $logger = null;

    public function getLogger(): LoggerInterface {
        if ($this->logger === null) {
            $this->logger = new Monolog\Logger('app');
        }
        return $this->logger;
    }

    public function getUserRepository(): UserRepository {
        return new UserRepository($this->getConnection(), $this->getLogger());
    }
}
```
Компиляция = резолв графа + удаление неиспользуемых сервисов + оптимизация. Поэтому в продакшене нет рефлексии на каждый запрос.

### 2. Как работает autowiring

Autowiring = автоматическое связывание по типам через рефлексию **на этапе компиляции**.

```php
class Checkout {
    public function __construct(
        private PaymentGateway $gateway,
        private LoggerInterface $logger
    ) {}
}
```
Компилятор смотрит на тип-хинты конструктора, ищет сервис с таким интерфейсом/классом и прописывает его в `getCheckout()`.

Если несколько кандидатов — нужен `#[Autowire]` или alias.  
`autoconfigure: true` включает авто-регистрацию классов с `#[AsService]` и автоматический wiring.

### 3. Что происходит при первом запросе сервиса

1. Запрос `$container->get(UserRepository::class)`
2. Контейнер вызывает `getUserRepository()`
3. Если сервис **shared** по умолчанию — создаётся один раз, результат кладётся в приватное поле и дальше отдаётся из кеша. Это синглтон в рамках запроса.
4. Если сервис `prototype` / `lazy` — оборачивается в прокси `LazyServiceInitializer`. Реальный объект создаётся только при первом обращении к методу.

Прокси выглядит так:
```php
$proxy = new class {
    public function __call($m, $a) {
        $real = $container->getRealService();
        return $real->$m(...$a);
    }
};
```

**Итого:** сборка — один раз при деплое, autowiring — статический анализ типов на этапе компиляции, а в рантайме сервис либо берётся из кеша, либо лениво проксируется.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это запах архитектуры. Контейнер Symfony их обнаружит и упадёт на компиляции:

```
Circular reference detected for service "A", ... involves "B"
```

Разруливать можно так, от лучшего к худшему:

### 1. Рефакторинг — убираем цикл
В 90% случаев цикл означает, что два сервиса знают слишком много друг о друге. Выносим общую логику в третий сервис.

```php
// Было: A -> B -> A
class A { public function __construct(B $b) {} }
class B { public function __construct(A $a) {} }

// Стало
class Coordinator { 
    public function __construct(A $a, B $b) {}
}
class A { public function __construct(Coordinator $c) {} }
class B { /* без A */ }
```

Или переносим одну из зависимостей в доменное событие.

### 2. Setter injection вместо constructor
Если один сервис реально нужен только иногда, убираем его из конструктора.

```php
class A {
    private ?B $b = null;
    public function setB(B $b): void { $this->b = $b; }
}
```
Symfony может внедрить через setter. Конструктор остаётся без цикла.

### 3. Lazy proxy — отложенная инициализация
Когда цикл неизбежен, делаем одну из сторон ленивой.

```yaml
services:
    app.b:
        lazy: true
```
или
```php
class A {
    public function __construct(private LazyServiceInterface $b) {}
}
```
Symfony создаст прокси для B. A создаётся без реального B, B создаётся позже, когда к нему реально обращаются. Цикл физически разрешается.

Пример:
```php
class OrderService {
    public function __construct(private PaymentService $payment) {}
    public function place() { /* ... */ }
}

class PaymentService {
    public function __construct(private OrderService $order) {}
}
```
Делаем `OrderService` lazy:
```php
#[AsService(lazy: true)]
class OrderService { }
```
Теперь `PaymentService` получает прокси, а не реальный объект.

### 4. События / Messenger
Сервис A не дергает B напрямую, а публикует событие. B подписывается.

```php
$dispatcher->dispatch(new OrderPlaced($order));
```
Никакой прямой зависимости между A и B.

### Что не делать
* `service_locator` ради цикла — это маскировка проблемы.
* Статическая инициализация внутри конструктора.

**Итого:** сначала рефакторинг и разделение ответственности, если нельзя — lazy proxy или setter injection, а события — самый чистый выход из цикла.

> ⏱ 8.1s · prompt 4809 tok · output 633 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

В регистрации важен быстрый ответ пользователю, а побочные эффекты — письмо и индекс — могут идти позже. Нужно отделить основной транзакционный путь от фоновых задач.

### Вариант 1. Синхронно в том же запросе
```php
$em->persist($user);
$em->flush();
$mailer->sendWelcome($user);
$search->index($user);
return 201;
```
**Плюсы:** просто, атомарно, легко дебажить.
**Минусы:** любой сбой в почте/Elasticsearch ломает регистрацию, время ответа растёт. Не подходит.

### Вариант 2. Symfony Messenger + очереди
Регистрация делает только то, что нужно для консистентности, остальное — через сообщения.

```php
// в транзакции
$em->persist($user);
$em->flush();

// публикуем после коммита
$this->eventDispatcher->dispatch(new UserRegistered($user->id));

// или сразу сообщение
$this->bus->dispatch(new SendWelcomeEmail($user->id));
$this->bus->dispatch(new IndexUser($user->id));
```

Worker'ы обрабатывают задачи асинхронно через RabbitMQ / Redis / Doctrine Transport.

**Плюсы:** ответ пользователю <100ms, отказоустойчиво, легко масштабировать.
**Минусы:** eventual consistency. Письмо может прийти с задержкой, индекс будет не сразу. Нужно обрабатывать DLQ и ретраи.

### Вариант 3. Transactional Outbox
Чтобы гарантировать, что сообщение не потеряется при откате транзакции.

1. Пишем `users` и запись `outbox` в одной транзакции.
2. Отдельный воркер читает outbox и публикует в Messenger / Kafka.
3. После успешной публикации — удаляем из outbox.

**Плюсы:** атомарность + асинхронность, нет риска дублей.
**Минусы:** больше инфраструктуры, нужна гарантия доставки.

### Вариант 4. Domain Events с отложенной диспетчеризацией
В Symfony можно хранить доменные события в агрегате и диспетчить их после `flush`.

```php
final class User {
    private array $events = [];
    public function register(): void { 
        $this->events[] = new UserRegistered($this->id);
    }
    public function pullEvents(): array { ... }
}
```
В `PostFlush` слушателе отправляем события в шину.

**Плюсы:** чистый DDD, бизнес-логика не знает про инфраструктуру.
**Минусы:** та же eventual consistency, сложнее тестировать.

### Рекомендация на практике

**Базовый:** Messenger с async транспортом, диспетч после `flush`. Для почты — `async`, для индекса — `async` с отдельным воркером и приоритетом.

**Критичный:** Outbox, если нельзя потерять событие.

Trade-offs сводятся к: **согласованность vs скорость vs сложность**. Для регистрации пользователя eventual consistency приемлема, поэтому асинхронность через Messenger — оптимальный баланс.

> ⏱ 9.3s · prompt 6064 tok · output 723 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

В Symfony контроль доступа — многослойный. Три основных механизма.

### 1. Аутентификация
Кто ты?  
`Security` компонент + `UserProviderInterface`.  
Пользователь попадает в `TokenStorage`, формируется `UserInterface` объект, и дальше система знает `getRoles()`.

```yaml
security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
```
`passport` — `UserBadge`, `PasswordCredentials`, `RememberMe`, JWT через `lexik/jwt-auth`.

### 2. Авторизация ролями
Что тебе можно делать «вообще».

```php
// security.yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```
Или в коде:
```php
if ($this->isGranted('ROLE_ADMIN')) { ... }
```
Роли — грубые, глобальные права: `ROLE_USER`, `ROLE_ADMIN`, `ROLE_BILLING_MANAGER`.  
Плюсы: быстро, декларативно.  
Минусы: не учитывают объект и контекст.

### 3. Voters — объектная авторизация
Кто ты **и** к какому объекту у тебя есть доступ.

```php
final class DocumentVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'VIEW' && $subject instanceof Document;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /** @var Document $doc */
        $doc = $subject;

        // владелец или член команды
        return $doc->getOwner() === $user || $doc->getTeam()->hasMember($user);
    }
}
```
Использование:
```php
if (!$this->isGranted('VIEW', $document)) {
    throw new AccessDeniedException();
}
```

### Когда что выбирать

**Роли достаточно**, когда:
* Доступ зависит только от должности/группы.
* `ROLE_ADMIN` может всё в админке, `ROLE_USER` — только профиль.
* Нет необходимости проверять владельца ресурса.

**Voter нужен**, когда:
* Доступ зависит от конкретного объекта: «может ли пользователь редактировать этот документ?»
* Нужны сложные условия: владелец, роль в проекте, статус, время.
* Комбинируешь несколько правил: `ROLE_EDITOR` И владелец проекта.

На практике: роли — для грубого контроля, voters — для тонкой, объект-ориентированной авторизации. Часто используют вместе: voter сначала проверяет роль, потом бизнес-правила.

**Дополнительно:** `ExpressionLanguage` в `access_control` для простых условий без voter:
```yaml
access_control:
    - { path: ^/doc, roles: 'is_granted("ROLE_USER") and request.get("_route") == "doc_show"' }
```

Итого: аутентификация → роли → voters. Чем ближе к домену, тем тоньше контроль.

> ⏱ 8.3s · prompt 6746 tok · output 641 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

В Doctrine нет нативной поддержки Value Object как отдельного агрегата. VO — неизменяемый, валидный всегда. Задача — сохранить его в БД без нарушения инвариантов и не превращать VO в сущность.

Есть 3 рабочих подхода.

### 1. `Embeddable` — классика для составных VO

VO маппится в колонки родительской сущности.

```php
#[ORM\Embeddable]
final class Money
{
    public function __construct(
        #[ORM\Column(type: 'integer')]
        private int $cents,
        #[ORM\Column(length: 3)]
        private string $currency,
    ) {
        if ($cents < 0) throw new \DomainException();
    }

    public function cents(): int { return $this->cents; }
    public function currency(): string { return $this->currency; }
}

#[ORM\Entity]
class Order
{
    #[ORM\Embedded(class: Money::class)]
    private Money $total;

    public function __construct(Money $total) { $this->total = $total; }
}
```

**Плюсы:** нативно, быстро, одна таблица, неизменяемость на уровне домена сохраняется.
**Минусы:** VO не может жить своей жизнью, нет отдельной таблицы. `Embeddable` по умолчанию mutable при гидратации — Doctrine создаёт объект через рефлексию, минуя конструктор. Поэтому инварианты проверяем только при создании через фабрику, а при чтении доверяем БД.

**Подводный камень:** нельзя сделать VO полностью immutable, потому что Doctrine будет менять свойства напрямую. Решение — `readonly` свойства в PHP 8.1+ и фабричный метод для создания.

### 2. Custom DBAL Type — VO <-> скаляр/JSON

Для простых VO храним как один столбец.

```php
final class MoneyType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) return null;
        [$cents, $currency] = explode('|', $value);
        return new Money((int)$cents, $currency); // конструктор проверяет инвариант
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof Money ? $value->cents().'|'.$value->currency() : null;
    }
}
```
Использование: `#[ORM\Column(type: MoneyType::class)]`

**Плюсы:** VO инкапсулирован полностью, в БД один столбец.
**Минусы:** сложный тип сложнее индексировать, миграции болезненны. При изменении формата — миграция данных.

### 3. Сериализация в JSON — быстрый, но грязный

```php
#[ORM\Column(type: 'json')]
private array $addressData;

public function getAddress(): Address
{
    return Address::fromArray($this->addressData);
}
```

**Плюсы:** быстро реализовать.
**Минусы:** нет валидации на уровне БД, запросы по полям VO невозможны, нарушается прозрачность.

### Как сохранить неизменяемость

1. Сделай VO `final readonly` и без сеттеров.
2. Создавай только через статические фабрики `Money::fromCents()`.
3. В `Embeddable` добавь `#[ORM\PostLoad]` хук, который проверяет инварианты после гидратации, если боишься «грязных» данных.
4. Никогда не мапь VO как `@ManyToOne`. Это сущность, а не VO.

**Рекомендация:** для составных значений — `Embeddable`. Для простых — Custom Type. Главное: VO не должен знать про Doctrine, маппинг живет в инфраструктурном слое.

> ⏱ 11.1s · prompt 7433 tok · output 876 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Агрегат в Doctrine — это **единица согласованности**. Сохраняем только через корень, внутри одного агрегата одна транзакция, один `flush`.

### Модель

Возьмём агрегат `Order`. Корень `Order`, дочерние сущности `OrderItem`. `Money` — Value Object.

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Embedded]
    private Money $total;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function addItem(Product $product, int $qty): void
    {
        // инварианты агрегата
        if ($qty <= 0) throw new DomainException();
        // не даем напрямую менять коллекцию извне
        $item = new OrderItem($this, $product, $qty);
        $this->items->add($item);
        $this->recalculateTotal();
    }

    public function removeItem(OrderItem $item): void
    {
        $this->items->removeElement($item);
        $this->recalculateTotal();
    }

    /** @return OrderItem[] */
    public function items(): array { return $this->items->toArray(); }
}
```

`OrderItem` — сущность, но её нельзя создать без корня.

```php
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

    public function __construct(Order $order, Product $product, int $qty)
    {
        $this->order = $order;
        $this->productId = $product->id();
        $this->qty = $qty;
    }
}
```

### Границы агрегата в Doctrine

* **mappedBy / inversedBy** только внутри агрегата. Нет прямых связей за пределы агрегата.
* `cascade: ['persist','remove']` и `orphanRemoval: true` — чтобы при сохранении корня Doctrine сам сохранил/удалил детей. Репозиторий грузит и сохраняет только корень.
* Коллекция **никогда не экспонируется наружу как `ArrayCollection`**. Возвращаем копию/iterator, иначе клиент может напрямую менять состояние агрегата.

### Транзакция и flush

Flush — **не** в домене. Домен не знает про Doctrine.

```php
final class PlaceOrder
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(CustomerId $customerId, array $lines): OrderId
    {
        $this->em->beginTransaction();
        try {
            $order = Order::create($customerId);
            foreach ($lines as $line) {
                $order->addItem($line->product, $line->qty);
            }

            $this->orders->save($order); // просто $em->persist($order)
            $this->em->flush();          // один flush на агрегат
            $this->em->commit();

            return $order->id();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
```

Репозиторий тонкий:
```php
final class OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}
    public function get(OrderId $id): Order { ... }
    public function save(Order $order): void { $this->em->persist($order); }
}
```

**Правила:**
* **Одна транзакция = один агрегат**. Не грузим два агрегата в одной транзакции, если они логически независимы.
* `flush()` вызывает Application Service после выполнения всех бизнес-операций над агрегатом. Не в сеттере, не в домене.
* `EntityManager` живёт в рамках одного запроса. Не храним ссылку на него в домене.
* Для чтения используем read-model / DTO, а не агрегат, чтобы не тащить весь граф.

**Подводные камни:**
* Lazy loading внутри транзакции — `items` подгружаются по требованию. Если обращаться после `flush` вне транзакции — `LazyInitializationException`.
* `orphanRemoval` делает `DELETE` при `removeElement`, но только после `flush`.
* Не делай `cascade: ['remove']` между агрегатами — это нарушение границ.

Итого: корень инкапсулирует коллекцию и инварианты, Doctrine маппит через `OneToMany` с каскадом, а граница транзакции и `flush` — ответственность Application Layer, не домена.

> ⏱ 15.6s · prompt 8289 tok · output 1227 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

N+1 в Doctrine — классика. На одной странице один запрос за корневые сущности + по одному запросу за каждую связанную сущность.

### Как диагностировать

**1. Symfony Profiler → Doctrine**
Вкладка Doctrine показывает все SQL запросы за запрос. Если видишь один `SELECT ... FROM order` и потом 50 раз `SELECT ... FROM order_item WHERE order_id = ?` — это N+1.

**2. Логирование**
```yaml
doctrine:
    dbal:
        logging: true
```
или в dev:
```php
$logger->queries
```
Считаем количество запросов и повторяемость.

**3. Blackfire / Xdebug profiler**
Показывает где именно происходит ленивая загрузка. Часто виновник — цикл в Twig:
```twig
{% for order in orders %}
  {{ order.items.first.product.name }}
{% endfor %}
```

**4. Статический анализ**
Ищи доступ к ленивым ассоциациям внутри циклов. Любой `$entity->getRelation()` вне `JOIN` = потенциальный N+1.

### Как лечить

**1. JOIN FETCH / fetch join**
Грузим сразу граф.

```php
$qb = $em->createQueryBuilder()
    ->select('o', 'i', 'p')
    ->from(Order::class, 'o')
    ->leftJoin('o.items', 'i')
    ->leftJoin('i.product', 'p')
    ->where('o.id = :id')
    ->setParameter('id', $id);
```
Doctrine выполнит один запрос и заполнит коллекции.

*Плюсы:* быстро, один раундтрип.
*Минусы:* дублирование данных, большая память, нельзя пагинацию по корню без `DISTINCT`. Не масштабируется на глубокие графы.

**2. EAGER loading**
```php
#[ORM\ManyToOne(fetch: 'EAGER')]
```
*Плюсы:* не забываешь.
*Минусы:* всегда грузится даже когда не нужен. Убивает гибкость.

**3. Batch fetching**
```php
#[ORM\ManyToOne]
#[ORM\BatchSize(size: 20)]
```
Doctrine при первом ленивом доступе соберёт ID и одним запросом `WHERE id IN (...)` подгрузит пачку.

*Плюсы:* сохраняет ленивость, уменьшает запросы с N до N/20.
*Минусы:* всё равно несколько запросов, не решает проблему полностью.

**4. Extra Lazy collections**
```php
#[ORM\OneToMany(fetch: 'EXTRA_LAZY')]
```
Операции `count`, `slice` не грузят всю коллекцию.

*Плюсы:* экономия когда нужна только часть.
*Минусы:* не убирает N+1 при итерации.

**5. DTO / Query Projection**
Когда нужен только read-model, не агрегат.

```php
$qb->select('NEW App\DTO\OrderItemDTO(o.id, p.name, i.qty)')
   ->from(Order::class, 'o')
   ->join('o.items', 'i')
   ->join('i.product', 'p');
```
*Плюсы:* минимальный SQL, нет гидратации сущностей, нет ленивой загрузки.
*Минусы:* дублирование маппинга, только для чтения.

**6. Second Level Cache**
Кэшируем часто читаемые ассоциации.

*Плюсы:* убирает запросы вообще.
*Минусы:* усложняет инвалидацию, актуально только для редкозменяемых данных.

### Рекомендация

* Для списков — DTO projection или `JOIN FETCH` с ограниченной глубиной.
* Для единичной сущности — `JOIN FETCH` в репозитории.
* Никогда не оставляй ленивую загрузку в Twig.
* Профилируй каждый критичный эндпоинт.

Итого: диагностируй профайлером, убирай ленивость там где она бьёт, читай через DTO когда нужен только показ.

> ⏱ 11.8s · prompt 9405 tok · output 910 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Бизнес-валидация в приложении — это про правила домена. Ограничения в БД — это про **последний рубеж защиты целостности данных**. Они нужны, потому что приложение не единственный писатель данных.

### Зачем БД-ограничения, если есть валидация в коде

1. **Параллельные писатели**. Микросервисы, скрипты импорта, админки, ручные правки. Приложение не может контролировать всё.
2. **Гонки**. Два запроса одновременно проходят валидацию в приложении, но в БД остаётся невалидное состояние. `UNIQUE` и `CHECK` спасут.
3. **Исторические данные**. Миграции, бэкапы, восстановление — данные могут обойти приложение.
4. **Явный контракт**. Схема БД документирует инварианты, которые нельзя нарушить никогда.

Приложение — первый уровень, БД — страховочный.

### Что использую в PostgreSQL

**1. NOT NULL, UNIQUE, FOREIGN KEY**
База. Без них никак.
```sql
ALTER TABLE orders 
  ADD CONSTRAINT orders_customer_id_fk 
  FOREIGN KEY (customer_id) REFERENCES customers(id);
```

**2. CHECK constraints**
Простые инварианты, не требующие кода.
```sql
ALTER TABLE money 
  ADD CONSTRAINT money_cents_nonnegative CHECK (cents >= 0);

ALTER TABLE orders 
  ADD CONSTRAINT orders_status_check 
  CHECK (status IN ('new','paid','cancelled'));
```

**3. Partial unique indexes**
Бизнес-правило «один активный кошелёк на пользователя».
```sql
CREATE UNIQUE INDEX one_active_wallet_per_user 
ON wallets(user_id) 
WHERE deleted_at IS NULL AND active = true;
```

**4. Exclusion constraints + GIST**
Непересекающиеся интервалы бронирований.
```sql
ALTER TABLE bookings
  ADD CONSTRAINT no_overlap
  EXCLUDE USING GIST (room_id WITH =, daterange(start, end) WITH &&);
```

**5. Triggers**
Когда нужна сложная проверка, которую нельзя выразить CHECK.
```sql
CREATE FUNCTION check_order_total() RETURNS trigger AS $$
BEGIN
  IF NEW.total < 0 THEN RAISE EXCEPTION 'negative total';
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER order_total_trigger
BEFORE INSERT OR UPDATE ON orders
FOR EACH ROW EXECUTE FUNCTION check_order_total();
```
Использую редко, предпочитаю логику в приложении, но для критичных инвариантов — ок.

**6. Domain types**
```sql
CREATE DOMAIN email AS TEXT CHECK (value ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');
```

### Когда что

* **Всегда**: `NOT NULL`, `FK`, `UNIQUE`.
* **Простые числовые/перечисляемые инварианты**: `CHECK`.
* **Условная уникальность**: partial index.
* **Сложные межстрочные правила**: `EXCLUDE` или триггер.
* **Никогда**: не делай всю бизнес-логику в триггерах. БД — для целостности, не для доменных правил.

Итого: валидация в приложении — для UX и скорости, ограничения в PostgreSQL — для гарантии, что данные останутся консистентными при любом способе записи.

> ⏱ 9.3s · prompt 10295 tok · output 720 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Медленный запрос в Postgres — системная задача. Мой процесс — от симптома к плану.

### 1. Фиксируем проблему
* `EXPLAIN ANALYZE` — реально выполненный план с временем.
* `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)` — детали буферов.
* `pg_stat_statements` — топ медленных запросов по общему времени.

```sql
SELECT query, calls, mean_time, total_time
FROM pg_stat_statements
ORDER BY mean_time DESC LIMIT 10;
```

### 2. Читаем план
Смотрю:
* **Seq Scan** вместо **Index Scan** — нет индекса или селективность плохая.
* **Nested Loop** с огромным количеством строк — проблема join.
* **Bitmap Heap Scan** с большим `Recheck Cond` — индекс есть, но много мертвых строк.
* **Sort** на больших наборах — нет индекса по ORDER BY.
* **Seq Scan on ... rows=10M** — запрос читает всю таблицу.

Типичные причины:
* Нет индекса по WHERE / JOIN / ORDER BY.
* Неправильная статистика — planner выбирает плохой план.
* `SELECT *` + большие `text/jsonb` колонки.
* `OFFSET` в пагинации — Postgres читает и пропускает строки.
* N+1 на уровне приложения, а не один запрос.

### 3. Инструменты
* **pgAdmin / DBeaver** — визуальный `EXPLAIN`.
* **pg_stat_statements** — агрегированная статистика.
* **auto_explain** — логирует планы медленных запросов.
* **hypopg** — тестируем гипотетические индексы без их создания.
* **EXPLAIN ANALYZE** на копии данных.

### 4. Оптимизация

**Индекды**
```sql
CREATE INDEX CONCURRENTLY idx_orders_customer_status
ON orders(customer_id) WHERE status = 'new';
```
Частичный индекс для горячей выборки. `CONCURRENTLY` — без блокировки.

**Статистика**
```sql
ANALYZE orders;
```
После массовых вставок/обновлений.

**Переписываем запрос**
* Убираем `SELECT *`, берём только нужные колонки.
* Пагинация через keyset:
```sql
WHERE id > :last_id ORDER BY id LIMIT 100
```
вместо `OFFSET 100000`.

* Избегаем `IN` с огромным списком — используем временную таблицу + `JOIN`.

**Конфигурация**
* `work_mem` для сортировок.
* `effective_cache_size` — подсказка planner'у.
* `random_page_cost` для SSD.

**Модель данных**
* Денормализация для чтения: материализованные представления.
* Партиционирование по дате для больших таблиц.
* `jsonb` индексируем по GIN:
```sql
CREATE INDEX idx_data_gin ON events USING GIN (data);
```

### 5. Проверка
После правки снова `EXPLAIN ANALYZE`, сравниваем `Execution Time` и `Buffers`. Меряем в проде через `pg_stat_statements`.

**Итого:** логируем медленные запросы, читаем план, ищем Seq Scan / Sort / OFFSET, добавляем целевые индексы, обновляем статистику, переписываем пагинацию и проекции.

> ⏱ 9.1s · prompt 11053 tok · output 714 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

Агрегат в DDD — это **единица согласованности**. Кластер связанных объектов, который рассматривается как единое целое. Изменения внутри агрегата должны сохранять инварианты агрегата, и все изменения происходят атомарно в рамках одной транзакции.

Ключ — **корень агрегата**. Только через него можно получить доступ к внутренним объектам. Внешний мир не знает про `OrderItem`, он знает про `Order`.

### Как определяю границы

Правило: **агрегат = то, что должно быть консистентно всегда**.

Критерии:
1. **Инварианты**. Что должно выполняться одновременно? Например, `Order.total == sum(items)`. Если это так, `Order` и `OrderItem` в одном агрегате.
2. **Жизненный цикл**. Дочерний объект не может жить без родителя. `OrderItem` без `Order` бессмыслен.
3. **Размер**. Агрегат должен быть маленьким. Если он растёт >~1000 объектов — граница неверная.
4. **Транзакция**. Всё, что меняется атомарно, в одном агрегате.

### Пример

```php
final class Order
{
    private OrderId $id;
    /** @var OrderItem[] */
    private array $items = [];
    private Money $total;

    private function __construct(OrderId $id) { $this->id = $id; }

    public static function create(OrderId $id): self
    {
        return new self($id);
    }

    public function addItem(ProductId $productId, int $qty): void
    {
        if ($qty <= 0) throw new DomainException();
        $this->items[] = new OrderItem($productId, $qty);
        $this->recalculateTotal();
    }

    public function removeItem(OrderItemId $itemId): void
    {
        // ...
        $this->recalculateTotal();
    }

    // нет геттера коллекции наружу, только итератор
}
```

Корень `Order` защищает инвариант `total = sum(items)`. Никакой внешний код не может сделать `order->items()->add(...)` — только через методы корня.

Репозиторий работает только с корнем:
```php
$order = $orderRepo->get($id);
$order->addItem($productId, 2);
$orderRepo->save($order); // один flush
```

### Типовая ошибка при выборе границ

**Слишком большой агрегат**. Пример: `Order` + `Customer` + `ProductCatalog` в одном агрегате.

Разработчик думает: "Заказ связан с клиентом и товаром, значит в одном агрегате". В итоге:

* Любое изменение клиента блокирует запись заказа.
* Загрузка заказа тащит всю историю клиента и каталог.
* Конфликты оптимистичной блокировки.

Правильно:
* `Order` — агрегат.
* `CustomerId`, `ProductId` — идентификаторы ссылок на другие агрегаты.
* Связи между агрегатами — только по ID, не по объектам.

То есть `Order` знает `customerId`, а не объект `Customer`. Если нужны данные клиента — читаем через отдельный read-model.

Итого: граница агрегата определяется инвариантами и транзакционной целостностью, а не связями в БД. Корень — единственный вход, дочерние сущности — приватные, репозиторий — только корень.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Если два процесса одновременно меняют один агрегат — получаем **race condition** и потерю данных. В Doctrine по умолчанию это приведёт к последнему победителю без предупреждения, если не включить оптимистичную блокировку.

### Что происходит без защиты

Процесс A читает агрегат, версия = 1.  
Процесс B читает агрегат, версия = 1.  
A меняет, сохраняет, версия → 2.  
B меняет на основе устаревших данных, сохраняет, версия → 2, изменения A теряются.

### Как проектирую

**1. Оптимистичная блокировка — базовый уровень**
Doctrine поддерживает `@Version`.

```php
#[ORM\Entity]
class Order
{
    #[ORM\Version, ORM\Column(type: 'integer')]
    private int $version;

    // ...
}
```
При `flush` Doctrine проверяет `WHERE id = ? AND version = ?`. Если версия изменилась — `OptimisticLockException`. Приложение ловит исключение, перезагружает агрегат и повторяет операцию.

Плюсы: без блокировок БД, масштабируется.  
Минусы: конфликт решается на уровне приложения, нужен retry.

**2. Пессимистичная блокировка — когда нужна сильная гарантия**
```php
$order = $em->find(Order::class, $id, LockMode::PESSIMISTIC_WRITE);
```
Блокируем строку до коммита. Подходит для редких, критичных операций, но создаёт contention.

**3. Дизайн агрегата — уменьшить область конфликта**
Агрегат должен быть маленьким. Если два процесса часто конфликтуют — граница слишком широкая. Разбиваем агрегат.

Пример: `Order` с большим списком `OrderItem`. Вместо изменения всего заказа — отдельные агрегаты `OrderLine`. Или используем event sourcing с CRDT.

**4. Идемпотентность операций**
Операция должна быть безопасной при повторе. `AddItem($orderId, $lineId, $qty)` с уникальным `requestId`. Если процесс упал после `flush`, повтор не создаст дубликат.

**5. Стратегия повтора**
```php
$attempt = 0;
while ($attempt < 3) {
    try {
        $order = $repo->get($id);
        $order->addItem(...);
        $repo->save($order);
        $em->flush();
        break;
    } catch (OptimisticLockException) {
        $attempt++;
        usleep(50 * $attempt * 1000);
    }
}
```

**Итого:** по умолчанию включаю `@Version` на всех агрегатах, проектирую агрегаты маленькими, операции делаю идемпотентными, а при конфликте — retry с экспоненциальной задержкой. Пессимистичная блокировка — только как исключение.

> ⏱ 9.8s · prompt 11803 tok · output 775 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события — это факт, что что-то важное произошло в домене. Они нужны чтобы развязать побочные эффекты от основной транзакции агрегата.

### Когда публиковать

**Никогда внутри доменной модели в синхронном режиме.** Доменное событие генерируется в агрегате, но публикуется **после успешного коммита транзакции**.

```php
final class Order
{
    private array $recordedEvents = [];

    public function pay(Money $amount): void
    {
        // инварианты
        $this->status = Status::Paid;
        $this->recordedEvents[] = new OrderPaid($this->id, $amount);
    }

    public function pullEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
```

Application Service:

```php
$order = $repo->get($id);
$order->pay($amount);
$repo->save($order);
$em->flush();          // 1. коммит в БД
$em->commit();

foreach ($order->pullEvents() as $event) {
    $bus->dispatch($event); // 2. публикация ПОСЛЕ коммита
}
```

### Как обрабатывать

Два варианта:

**1. In-process, синхронно после коммита**
Подходит для обязательных эффектов внутри одного bounded context.

**2. Асинхронно через Messenger / Outbox**
Для межсервисных эффектов: отправить письмо, обновить поиск, начислить бонусы.

Лучшая практика — **Transactional Outbox**:
* В одной транзакции пишем агрегат + запись в таблицу `outbox`.
* Отдельный воркер читает `outbox` и публикует в шину.
* Гарантия: событие не потеряется при откате.

### Какие проблемы решает

* **Развязывает агрегат от инфраструктуры.** `Order` не знает про почту и Elasticsearch.
* **Соблюдает границы транзакции.** Основная операция остаётся быстрой и атомарной.
* **Поддерживает eventual consistency** между сервисами.
* Позволяет добавлять новые реакции без изменения домена.

### Какие проблемы создаёт

* **Eventual consistency.** После оплаты пользователь может не сразу увидеть письмо/баллы.
* **Порядок и дубли.** Нужны идемпотентные обработчики.
* **Сложность отладки.** Поток выполнения размазан по времени.
* **Риск потери события** если публиковать до коммита.

### Связь с транзакцией БД

Золотое правило: **событие публикуется только после успешного коммита**.

Если публиковать до `flush/commit`:
* Транзакция откатится, а событие уже ушло → ложное событие.
* Если публиковать в `PostFlush` слушателе, но без Outbox — при падении воркера событие потеряется.

Поэтому варианты:
* **После коммита** — просто, но есть окно, когда данные в БД есть, а событие ещё не отправлено.
* **Outbox** — атомарно, надёжно, но сложнее.

Итого: доменные события генерируются в агрегате, хранятся внутри него, а публикуются в Application Layer только после коммита. Для критичных сценариев используй Outbox и делай обработчики идемпотентными.

> ⏱ 9.7s · prompt 13200 tok · output 764 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

В DDD разница между Entity и Value Object — фундаментальная.

### Entity vs Value Object

**Entity** — объект с **идентичностью**. Два объекта с одинаковыми полями — разные, если у них разные ID.
* Жизненный цикл, mutable.
* Пример: `User`, `Order`, `Customer`. `Order#123` всегда тот же заказ, даже если поменяли адрес.

```php
final class OrderId { /* value object */ }
final class Order
{
    public function __construct(
        private OrderId $id,
        private string $status
    ) {}
    public function changeStatus(string $s): void { $this->status = $s; }
}
```

**Value Object** — объект, определяемый **значением**. Два VO равны, если равны их поля. Нет identity.
* Immutable, всегда валиден.
* Пример: `Money`, `Email`, `Address`, `DateRange`.

```php
final readonly class Money
{
    public function __construct(
        public int $cents,
        public string $currency
    ) {}
}
$money1 = new Money(100, 'USD');
$money2 = new Money(100, 'USD');
// $money1 == $money2 по значению
```

### Как решаю, что к чему отнести

Вопросы:
1. **Нужен ли жизненный цикл?** Если объект создаётся, меняется, удаляется — Entity.
2. **Имеет ли смысл равенство по значению?** `Email('a@b.com')` == `Email('a@b.com')` — VO.
3. **Можно ли заменить новым экземпляром?** VO всегда можно заменить копией с новыми полями.

Правило: идентификатор, который ты будешь использовать в БД как PK/FK — это Entity. Всё остальное — кандидат в VO.

### Ubiquitous Language

Единый язык — общий словарь между доменными экспертами и разработчиками. Термины из бизнеса используются **везде**: в коде, в БД, в документации, в разговорах.

Пример: в бизнесе говорят «Бронирование» и «Слот». Не «record» и «row».

```php
// Плохо
class BookingEntity { ... }
$repo->save($entity);

// Хорошо
class Booking
{
    public function reserve(Slot $slot, Customer $customer): void { ... }
}
```

Ubiquitous Language влияет на код:
* Имена классов, методов, событий — это бизнес-термины.
* Меньше транзакляционного кода, больше выразительности.
* Новые разработчики быстрее понимают домен, читая код как спецификацию.

Если в коде `processData()` — язык размыт. Если `approveOrder()` — язык единый.

Итого: Entity — идентичность и жизненный цикл, VO — значение и иммутабельность. Выбор делается через ubiquitous language: слушай домен, называй вещи своими именами, и модель сама подскажет, что Entity, а что VO.

> ⏱ 8.2s · prompt 13952 tok · output 633 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Чистая архитектура — это концентрические слои. Зависимости идут только внутрь, никогда наружу.

### Слои и что в них лежит

**1. Domain / Core**
Самое внутреннее ядро. Не знает ни о Symfony, ни о БД.
* Entities и Value Objects
* Domain Services — чистая бизнес-логика без I/O
* Domain Events, Domain Exceptions, Domain Interfaces — `UserRepositoryInterface`, `MailerPort`

Правило: ноль внешних зависимостей. Только PHP stdlib.

**2. Application**
Use Cases / Application Services. Оркестрирует домен.
* `RegisterUser`, `PlaceOrder` — один класс = одна бизнес-операция
* DTO для входа/выхода
* Зависит от Domain, использует порты из Domain

Не знает про HTTP, Doctrine, RabbitMQ.

**3. Interface Adapters**
Адаптеры к внешнему миру. Переводят запросы фреймворка в язык Application.
* Controllers
* Command / Query Handlers для Messenger
* Repositories implementation
* Mappers Entity <-> DTO

Зависит от Application и Domain, реализует порты.

**4. Frameworks & Drivers**
Внешние технологии.
* Symfony HTTP Kernel, Routing, Security
* Doctrine ORM, DBAL, Migrations
* Messenger transport, Redis, RabbitMQ
* Twig, Logger, Config

Зависит от всех внутренних слоев, но внутренние его не знают.

### Правило зависимостей
Зависимости только внутрь. Domain не знает про Application, Application не знает про Frameworks. Можно рисовать как луковицу.

### Куда девается что

* **Doctrine** — Frameworks & Drivers + Interface Adapters. `OrderRepositoryDoctrine` реализует `OrderRepositoryInterface` из Domain и использует EntityManager.
* **Symfony-контроллер** — Interface Adapters. Тонкий: валидация запроса → DTO → вызов Application Use Case → маппинг ответа.
```php
#[Route('/orders')]
class OrderController
{
    public function __construct(private PlaceOrder $placeOrder) {}
    public function __invoke(Request $r) { ... $this->placeOrder(...) }
}
```
* **Обработчик очереди** — Interface Adapters. `SendWelcomeEmailHandler` получает сообщение из Messenger, вызывает Application Service.

### Как следить за границами

1. **Namespace + PSR-4**
```
Domain/
Application/
Infrastructure/
```
Никаких `use Doctrine\ORM` в `Domain`.

2. **Dependency Injection конфиг**
В `services.yaml` явно разделите слои. Domain сервисы — без аргументов, Application — получает порты, Infrastructure — реализует порты.

3. **Architecture Tests**
PHPStan + `deptrac` / `PHPStan-StrictRules` + `ergebnis/phpstan-rules`. Правило:
* Domain может зависеть только от себя
* Application может зависеть от Domain
* Infrastructure может зависеть от всех
Нарушение — CI падает.

4. **Контракты в Domain**
Репозитории, Mailer, Logger — интерфейсы в Domain, реализации в Infrastructure. Так Application не тянет Doctrine.

5. **Code Review чек-лист**
* Есть ли `use Symfony` в Domain?
* Контроллер делает бизнес-логику?
* Use Case знает про HTTP?

Итого: Domain — бизнес, Application — сценарии, Adapters — перевод, Frameworks — технологии. Doctrine и контроллер живут снаружи, а границы держат namespace + DI + архитектуро-тесты.

> ⏱ 11.1s · prompt 14649 tok · output 869 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony + PostgreSQL я выбираю классическую пирамиду, но с акцентом на интеграционные тесты, потому что домен завязан на БД.

### Пирамида

**Unit — 50-70%**
Тестируем чистый домен: Value Objects, Entities, Domain Services. Без фреймворка, без БД.
```php
$money = Money::fromCents(100);
$this->assertEquals(100, $money->cents());
```
Быстро, стабильно. Мокаем всё внешнее.

**Integration — 20-30%**
Тестируем репозитории, маппинги Doctrine, constraints БД, транзакции.
Используем реальный PostgreSQL в Docker + `Testcontainers` / `docker-compose`.  
`#[KernelTestCase]` или `Panther` для HTTP-слоя.

**E2E / Acceptance — 5-10%**
Критичные пользовательские сценарии через `Symfony Panther` / `Behat`. Один-два на flow: регистрация → оплата → письмо.

### Что мокать, а что нет

**Мокаем:**
* Внешние HTTP-клиенты, SMTP, внешние API, очереди.
* `Clock`, `MailerInterface`, `Psr\Log\LoggerInterface` — порты.

**Не мокаем:**
* Doctrine / БД. Для репозиториев делаем реальную БД в памяти / тестовый контейнер. Моки репозиториев убивают ценность теста.
* Доменную логику. Unit-тесты на домен без моков.

### Как держать тесты быстрыми и стабильными

**1. Test Database per test / per class**
* Используем `PRISMA`-подход: каждый тест получает чистую схему.
* `Doctrine\ORM\Tools\SchemaTool::createSchema()` в `setUp`.
* Для скорости — PostgreSQL в том же Docker host, не через сеть.

**2. Транзакции вместо truncate**
```php
$this->entityManager->beginTransaction();
...
$this->entityManager->rollback();
```
Тест не оставляет мусора, быстро.

**3. Разделяем read/write тесты**
Unit — без БД, integration — только нужные фикстуры. Не грузим весь датасет.

**4. Параллельность**
`paratest` для unit. Для integration — разные схемы `test_db_1`, `test_db_2`.

**5. Стабильность**
* Детерминированные данные: фабрики, не рандом.
* Не зависим от времени — внедряем `Clock`.
* Очищаем очередь сообщений после теста.

**6. Контракты**
Для внешних сервисов — contract tests. Мокаем адаптер, но проверяем маппинг.

Итого: много быстрых unit на домен, интеграционные на репозитории с реальной PostgreSQL в транзакциях, минимум E2E. Мокаем границы, не мокаем БД. Скорость — через транзакции и параллельность, стабильность — через детерминизм и изоляцию.

> ⏱ 8.3s · prompt 15432 tok · output 636 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Одновременное изменение одной записи — классическая проблема конкурентности. Без защиты получаем потерю обновлений, неконсистентные данные, нарушение инвариантов.

### Проблемы

* **Lost update**: A читает, B читает, A сохраняет, B сохраняет поверх A.
* **Dirty read / non-repeatable read**: процесс видит промежуточное состояние.
* **Нарушение бизнес-инвариантов**: два процесса уменьшают баланс, получая отрицательное значение.

### Способы защиты

**1. Оптимистичная блокировка**
`@Version` в Doctrine, `WHERE id=? AND version=?`.

Процесс A и B читают версию 1. A сохраняет → версия 2. B пытается сохранить → `OptimisticLockException`.

*Плюсы:* нет блокировок БД, высокая пропускная способность, подходит для редких конфликтов.
*Минусы:* конфликт решается в приложении, нужен retry.
*Когда:* по умолчанию для большинства агрегатов в веб-приложениях.

**2. Пессимистичная блокировка**
`SELECT ... FOR UPDATE`. Строка блокируется до коммита транзакции.

*Плюсы:* гарантия, что второй процесс ждёт.
*Минусы:* снижает параллелизм, риск deadlock, держит транзакцию открытой.
*Когда:* короткие критичные операции, финансы, инвентарь, где конфликт частый и недопустим.

**3. Compare-and-Swap на уровне приложения**
Проверяем условие перед записью:
```sql
UPDATE accounts SET balance = balance - 100 
WHERE id = ? AND balance >= 100
```
Если `rowCount() === 0` — конфликт.

*Плюсы:* атомарно в БД, без блокировок.
*Минусы:* логика утекает в SQL.
*Когда:* простые числовые инварианты, счетчики.

**4. Токены / ETag**
Клиент получает `ETag: "v1"` при чтении. При записи отправляет `If-Match: "v1"`. Сервер отклоняет, если версия изменилась → 409 Conflict.

*Плюсы:* прозрачно для пользователя, RESTful.
*Минусы:* требует поддержки на клиенте.
*Когда:* API, редактируемые документы.

**5. Разделение агрегата**
Уменьшаем область конфликта. Вместо одного большого `Order` — отдельные `OrderLine` агрегаты. Конфликты реже.

*Плюсы:* архитектурное решение.
*Минусы:* сложнее моделировать.
*Когда:* агрегат слишком большой, конфликты частые.

### Сравнение и выбор

* **По умолчанию:** оптимистичная блокировка + retry. Быстро, масштабируемо.
* **Финансы/инвентарь:** пессимистичная `FOR UPDATE` внутри короткой транзакции или compare-and-swap.
* **API:** ETag + оптимистичная.
* **Частые конфликты:** пересмотреть границы агрегата.

На практике комбинирую: оптимистичная блокировка на уровне Doctrine + идемпотентные операции + для критичных операций `SELECT ... FOR UPDATE` с короткой транзакцией.

> ⏱ 8.4s · prompt 16113 tok · output 654 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой git-процесс — это защита от хаоса и предсказуемый релиз.

### 1. Ветвление — trunk-based с feature-ветками
`main` — всегда деплоится. `develop` не использую.

* `main` — production-ready.
* `feature/XXX-описание` — от `main`.
* `hotfix/XXX` — срочный фикс от `main`.

Почему: минимум долгоживущих веток, меньше конфликтов, быстрее интеграция.

### 2. Формат коммитов — Conventional Commits
```
feat(order): add payment retry
fix(api): prevent N+1 on users list
chore(ci): bump phpstan
refactor(domain): extract Money VO
test(order): add optimistic lock test
```

Почему: автогенерация CHANGELOG, семантический релиз, понятная история.

### 3. Pull Request
* Одна задача — один PR.
* PR < 400 строк.
* Обязательные: описание задачи, acceptance criteria, скриншоты/линки, тесты.
* CI зелёный: `phpstan`, `phpunit`, `cs-fixer`, `tests`.
* Self-review перед запросом ревью.

### 4. Ревью
* Минимум 1 аппрув от команды.
* Фокус на архитектуру, не на запятые.
* Обсуждаем почему, а не как.
* Автор отвечает на комментарии, мержит сам после апрува.

### 5. Merge в main
* Squash & merge. История main — чистая, по задачам.
* Никаких `merge` коммитов, только squash.
* После merge — автоматический деплой в staging.

### 6. Релизы
* Теги `v1.2.3` по SemVer.
* `git tag -a v1.2.3 -m "Release"` после прохождения staging.
* CHANGELOG генерируется из conventional commits.
* Релиз в прод — через CI/CD, blue/green или canary.

### 7. Hotfix
* `hotfix/PROD-123` от `main`.
* Быстрый PR, тесты обязательны.
* После мержа в `main` — сразу релиз, потом cherry-pick в активные feature-ветки если нужно.

### Почему так
* **Trunk-based** — меньше дрейфа веток.
* **Conventional Commits** — автоматизация релизов.
* **Squash** — читаемая история.
* **CI-gated PR** — защита main.
* **Маленькие PR** — быстрое ревью, меньше риска.

Альтернативы вроде GitFlow слишком тяжёлые для небольших команд. Главное — дисциплина: main всегда зелёный, каждый коммит осмысленный, релиз — событие, а не ручная сборка.

> ⏱ 7.8s · prompt 16816 tok · output 606 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью — это не поиск ошибок, а защита качества архитектуры и снижение технического долга.

### Как маркирую замечания

Делю на три уровня:

* **Blocker** — ломает инварианты, безопасность, производительность. Мерж невозможен.
* **Must** — нарушение принципов, дублирование, плохая читаемость. Исправляем до мержа.
* **Suggestion** — улучшение стиля, нейминг, рефакторинг позже. Не блокируем.

Комментарии формулирую как вопросы, а не приказы: «А что будет если...?» вместо «Переделай».

### На что смотрю в первую очередь

1. **Архитектура и границы**  
   Код в правильном слое? Домен не знает про Symfony/Doctrine? Зависимости идут внутрь?
2. **Инварианты домена**  
   Можно ли создать невалидный объект? Нарушается ли SRP? Где граница агрегата?
3. **Тесты**  
   Есть ли покрытие критичного пути? Тесты проверяют поведение, а не реализацию?
4. **Безопасность и производительность**  
   SQL-инъекции, N+1, утечки данных, отсутствие валидации.
5. **Читаемость и нейминг**  
   Имена отражают ubiquitous language? Метод делает одно? Комментарии нужны?
6. **Обработка ошибок**  
   Исключения — часть контракта? Не глотаем ли ошибки?

Стиль и форматирование — на CI, не в ревью.

### При разногласии с автором

1. Слушаю аргументы. Часто автор знает контекст лучше.
2. Если спор про принцип — обсуждаем в треде PR, приводим примеры и trade-offs.
3. Если не приходим к консенсусу — привлекаем третьего, tech lead / архитектора.
4. Фиксируем решение в ADR или комментарием в коде, чтобы не спорить снова.

Ревью — диалог, а не проверка. Цель — общий рост, а не «поймать на ошибке».

> ⏱ 5.7s · prompt 17462 tok · output 435 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Бизнес-проблема → задача исполнителю. Нужна конкретная, проверяемая формулировка без домыслов.

### Формулировка задачи

**Название:** Поиск заказов прошлых периодов недоступен для пользователей

**Контекст:**  
Пользователи сообщают, что в личном кабинете не находят заказы старше 6 месяцев. Поддержка получает ~30 обращений в неделю по теме «где мой заказ».

**Цель:**  
Пользователь должен находить свои заказы за последние 24 месяца в личном кабинете за ≤2 клика, без обращения в поддержку.

**Текущее поведение:**  
В разделе «Мои заказы» отображаются только заказы за последние 6 месяцев. Фильтр по дате ограничен диапазоном «за последние 6 месяцев». Поиск по номеру заказа не работает для заказов старше 6 месяцев.

**Ожидаемое поведение:**  
1. Список заказов показывает заказы за последние 24 месяца по умолчанию, с пагинацией по 20 штук.  
2. Фильтр по дате позволяет выбрать любой период от регистрации пользователя до сегодня.  
3. Поиск по номеру заказа / email работает для всех заказов пользователя без ограничения по дате.  
4. При отсутствии заказов показывается пустое состояние с текстом «Заказов не найдено».

**Критерии приёмки:**
* [ ] Пользователь видит заказы старше 6 месяцев в списке.
* [ ] Поиск по номеру заказа возвращает заказ старше 6 месяцев.
* [ ] Нагрузочное тестирование: страница «Мои заказы» с 1000 заказов грузится < 500 мс p95.
* [ ] Поддержка фиксирует снижение обращений по теме на 80% за 2 недели после релиза.

**Ограничения / не входит:**
* Не менять логику архивации заказов в БД.
* Не показывать заказы других пользователей.
* Не менять дизайн, только логику и бэкенд.

**Данные для воспроизведения:**
* Тестовый пользователь `user_search_test@company.com`, заказы #10042 от 2023-03-15, #10088 от 2022-11-02.

**Метрики успеха:**
* Количество обращений в поддержку по теме < 6 в неделю.
* Время ответа страницы < 500 мс p95.

### Что обязательно включил и почему

* **Контекст и боль** — чтобы исполнитель понимал зачем.
* **Текущее vs ожидаемое поведение** — убирает догадки.
* **Критерии приёмки** — без них задача «сделана когда я так сказал».
* **Ограничения / out of scope** — не лезем в дизайн/архивацию.
* **Данные для воспроизведения** — можно сразу тестировать.
* **Метрики успеха** — понимаем, что проблема решена.

Без этого исполнитель будет уточнять «а сколько месяцев показывать?», «а поиск где?», «а как проверить?». С такой формулировкой можно сразу кодить.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Перед тем как писать код, нужно снять неопределённость. Вопросы, которые закрывают риски.

### Бизнес и контекст
1. **Как именно пользователи ищут заказы сейчас?** Через список, поиск по номеру, фильтр по дате?
2. **Почему ограничение в 6 месяцев появилось?** Технический долг, производительность, бизнес-правило?
3. **Какие заказы точно должны быть доступны?** Все, только оплаченные, только выполненные?
4. **Есть ли юридические ограничения на хранение/показ старых заказов?**

### Объём и приоритеты
5. **Какой горизонт истории критичен?** 12, 24, 36 месяцев? А через год?
6. **Что важнее: скорость или полнота?** Можно ли показывать старые заказы медленнее?
7. **Есть ли SLA на поиск?** Какое максимальное время отклика приемлемо?

### Данные и UX
8. **Как выглядит успешный поиск для пользователя?** Нужен ли экспорт списка, фильтры по статусу?
9. **Что показывать, если заказов нет?** Пустое состояние, подсказка?
10. **Нужна ли аналитика по использованию поиска?** Какие метрики собирать?

### Технические ограничения
11. **Где хранятся старые заказы?** В основной БД, в архиве, в S3?
12. **Есть ли ограничения по производительности?** Сколько заказов у одного пользователя максимум?
13. **Кто ещё читает эти данные?** Поддержка, бухгалтерия — нужны ли им те же права?

### Риски
14. **Можно ли сломать существующий поиск?** Нужен ли feature-flag?
15. **Как измерим успех?** Снижение тикетов в поддержку на сколько % и за какой срок?

Эти вопросы убирают догадки про «навсегда» и «для всех». После ответов задача становится конкретной и без сюрпризов в середине спринта.

> ⏱ 8.1s · prompt 17964 tok · output 632 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Разные ситуации — разные действия.

### а) Попутный баг, не связанный с задачей

**Делаю:** фиксирую, не чиню в рамках текущей задачи.

* Создаю отдельный тикет/баг-репорт с описанием, шагами воспроизведения, приоритетом.
* Если баг критичный и блокирует приёмку — поднимаю вопрос с тимлидом: чинить сейчас или отдельным релизом.
* Не смешиваю задачи в одном PR.

**Почему:** сохранение фокуса и предсказуемости. Попутный фикс расширяет scope, увеличивает риск регрессии и усложняет ревью. Чистый PR = быстрый релиз.

Исключение: баг настолько мелкий, что его исправление — одна строка и очевидно безопасно. Тогда можно включить, но с пометкой в описании PR.

### б) Критерий приёмки противоречит цели задачи

**Делаю:** останавливаюсь и уточняю до продолжения работы.

* Собираю факты: что написано в критерии, какая цель бизнеса, где конфликт.
* Пишу короткое сообщение заказчику/PO: «Критерий X требует показывать заказы за 36 месяцев, а цель — снизить нагрузку на поддержку, которая жалуется на 6+ месяцев. Расширение до 36 месяцев увеличит время ответа в 3 раза. Как поступаем?»
* Предлагаю варианты: изменить критерий, пересмотреть цель, сделать MVP.

**Почему:** выполнение противоречащего критерия = формально «сделано», но бизнес-ценности нет, а может быть даже вред. Лучше 10 минут уточнения, чем неделя работы впустую.

Принцип: **не гадай, уточняй**. Scope creep и конфликт требований ловятся на этапе планирования, а не после деплоя.

> ⏱ 5.0s · prompt 19097 tok · output 380 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении — это не «готово», а подтверждение, что задача закрыта и безопасна для релиза.

### Отчёт, если всё выполнено

**1. Что сделано**
Кратко по сути: реализован поиск заказов за 24 месяца, убран лимит 6 месяцев, добавлен фильтр по дате.

**2. Как проверить**
* Ссылка на стейджинг / dev
* Шаги воспроизведения с тестовыми данными
* Скриншоты / видео

**3. Критерии приёмки**
Чек-лист с галочками:
- [x] Список показывает заказы старше 6 мес
- [x] Поиск по номеру работает
- [x] p95 < 500 мс

**4. Тесты**
* Unit / integration покрыты
* Линки на PR с тестами
* Результаты CI зелёные

**5. Риски / ограничения**
Что сознательно не трогали, известные ограничения.

**6. Документация**
Обновлены README / API docs / runbook если нужно.

**7. Релиз-ноты**
Краткое описание для бизнеса и для релиза.

### Если часть задачи не выполнена

Структура та же, но добавляю честность:

**1. Что сделано / что не сделано**
Чётко разделяю. Например: «Поиск по номеру работает, фильтр по дате — нет».

**2. Почему не сделано**
Фактическая причина: нехватка времени, техническая сложность, изменились требования. Без оправданий.

**3. Влияние на бизнес**
Как это влияет на цель задачи. Критично или нет?

**4. План устранения**
* Отдельный тикет с приоритетом
* Оценка сроков
* Временное решение, если есть

**5. Критерии приёмки — частичные**
Какие критерии выполнены, какие отложены.

Пример:
- [x] Список показывает заказы старше 6 мес
- [ ] Фильтр по дате — отложено до спринта X, тикет PROD-1245

**Принцип:** отчёт должен позволять принять решение: релизить сейчас или ждать. Скрывать недовыполнение — худший вариант, потому что потом всплывёт в проде.

> ⏱ 6.2s · prompt 19512 tok · output 479 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

В PostgreSQL нет `FORCE INDEX` как в MySQL / Oracle. Постгрес не поддерживает хиты на выбор плана запроса. Планировщик сам решает какой индекс использовать на основе статистики, cost model и параметров.

Поэтому команды вида

```sql
SELECT * FROM orders FORCE INDEX (idx_customer)
```

в нативном PostgreSQL не работают и вызовут синтаксическую ошибку.

### Как в PostgreSQL "принудить" индекс

Это делается не в SQL, а через настройки планировщика или расширения:

**1. Расширение pg_hint_plan**
Единственный близкий аналог. Устанавливается отдельно.
```sql
/*+ IndexScan(orders idx_customer_id) */
SELECT * FROM orders WHERE customer_id = 123;
```
Работает только если расширение включено, и это всё равно антипаттерн.

**2. Грубые настройки сессии**
```sql
SET enable_seqscan = OFF; -- запретить seq scan, заставит использовать индекс
SET enable_indexscan = ON;
```
Влияет на все запросы в сессии, ломает планировщик глобально.

**3. Подсказки через стоимость**
```sql
ALTER INDEX idx_customer_id ALTER COLUMN SET (statistics = 1000);
```
или менять `random_page_cost`, `effective_cache_size` чтобы планировщик считал индекс дешевле.

### Зачем это вообще нужно и когда применяют

В нормальной эксплуатации **никогда**. Если планировщик выбирает не тот план, причина не в том что ему не подсказали, а в том что:

* Статистика устарела -> `ANALYZE table;`
* Неправильные параметры cost -> `effective_cache_size`
* Индекс не селективен -> нужен другой индекс / частичный индекс
* Запрос написан плохо -> `SELECT *`, `OFFSET`, функции в WHERE

`FORCE INDEX` применяют только в редких случаях:
* Обход бага планировщика в конкретной версии Postgres
* Временно для теста гипотезы с `hypopg` - создаёшь гипотетический индекс и смотришь план
* В legacy системах при миграции с MySQL где привыкли к хинтам

В продакшене правильный путь: обновить статистику, создать правильный индекс, переписать запрос, а не принуждать планировщик. Принуждение маскирует проблему и ломается при росте данных.

> ⏱ 8.6s · prompt 20012 tok · output 667 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

`strict_types=1` **не замедляет выполнение кода ни на сколько процентов**.

`declare(strict_types=1)` — это не runtime-опция и не дополнительный проход по коду. Это директива, которая меняет **правила проверки типов на границе вызова** в текущем файле.

В режиме `strict_types=0` PHP при вызове функции пытается неявно привести аргумент к объявленному типу:
```php
function foo(int $x) {}
foo('1'); // в weak mode строка приводится к int
```
В режиме `strict_types=1` приведение запрещено и сразу бросается `TypeError`:
```php
function foo(int $x) {}
foo('1'); // TypeError
```

Проверка типа и так происходит всегда. `strict_types` только убирает шаг неявного приведения. Это проверка на уровне Zend Engine при входе в функцию, которая выполняется за наносекунды. Разница в бенчмарках — в пределах шума измерений, <0.1% и практически неизмерима.

Почему нет замедления:
* Нет дополнительной рефлексии в рантайме
* Нет дополнительной аллокации
* Нет дополнительных обходов кода
* Типовая проверка уже выполняется для всех скалярных типов

Единственный «затратный» эффект — в weak mode ты получаешь скрытые баги, которые потом стоят часов дебага. В strict mode ты получаешь быстрый `TypeError` на этапе вызова.

Поэтому `strict_types=1` включается везде без вопросов.

> ⏱ 5.7s · prompt 20529 tok · output 425 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Doctrine ORM **поддерживает составные первичные ключи**, но делает это так неудобно, что на практике ими почти никто не пользуется. И именно поэтому кажется, что поддержки нет.

### Что реально умеет Doctrine

```php
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    private Order $order;

    #[ORM\Id]
    #[ORM\Column]
    private string $productId;

    // ...
}
```

Это валидный маппинг. Но есть ограничения:

* **Нет автоинкремента**. Составной PK не может быть `GENERATED`. Оба поля должны быть заданы вручную.
* **Репозиторий ломается**. `find($id)` ожидает один скалярный идентификатор. Для составного нужен массив `find(['order'=>..., 'productId'=>...])`. 
* **Ассоциации и DQL**. `JOIN`, `WHERE`, `IDENTITY()` работают криво. Особенно в `OneToMany`/`ManyToOne` связях.
* **Кеш, экстра-ленивые коллекции, Extra Lazy** — не работают корректно с составным PK.
* **Symfony Forms, API Platform** — почти везде предполагается один скалярный идентификатор.
* **Миграции и инструменты**. `doctrine:schema:update` и генераторы кода хуже справляются.

То есть поддерживается на уровне DBAL, но экосистема построена под суррогатный ключ.

### Как с этим живут в реальных проектах

**1. Не используют. Суррогатный ключ + уникальный constraint.**
Это 95% кейсов. Вместо `(order_id, product_id)` как PK делаем:

```php
#[ORM\Id, ORM\GeneratedValue, ORM\Column]
private int $id;

#[ORM\UniqueConstraint(columns: ['order_id','product_id'])]
```
Получаем простой PK для Doctrine и бизнес-ограничение на уровне БД.

**2. Если бизнес требует естественный составной ключ**
Делаем Value Object идентификатор и скрываем его за репозиторием.

```php
final class OrderItemId
{
    public function __construct(public OrderId $orderId, public string $productId) {}
}
```
Репозиторий принимает `OrderItemId`, а внутри сам раскладывает на два поля.

**3. Legacy таблицы**
Если наследуешься от старой схемы с составным PK, оставляем как есть, но:
* Не делаем на этих сущностях каскадные связи
* Используем DTO для чтения, а не сущности
* Ограничиваем использование только в узком месте

### Вывод

Doctrine технически поддерживает composite PK, но цена — усложнение репозиториев, DQL, интеграций и инструментов. Поэтому в реальном проекте выбирают суррогатный `id` + `UNIQUE` constraint. Составной PK оставляют только для легаси или там, где он действительно является бизнес-инвариантом и нельзя иначе.

> ⏱ 9.8s · prompt 20872 tok · output 751 tok

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

Код работает, но это набор антипаттернов. Начинать рефакторинг надо не с синтаксиса, а с границ ответственности.

### Что не так

**Безопасность**
* SQL-инъекция: `WHERE id = $id` — прямой интерполяция.
* Нет валидации входных данных.

**Архитектура / Чистота**
* Контроллер делает всё: HTTP, доступ к БД, бизнес-логику, отправку почты, логирование.
* Нарушение SRP, отсутствие слоёв.
* Нет транзакции: `SELECT` → `UPDATE` — race condition.

**Надёжность**
* Нет обработки ошибок БД/почты.
* `DB::connect()` каждый раз, нет пула.
* `Mailer::send` и `Logger` вызываются синхронно — ломают ответ пользователю.
* Нет доменной модели: статус `'paid'` — магическая строка.

**Тестируемость**
* Нельзя протестировать без реальной БД и почты.
* Жёсткая связность, нет DI.

### Как переделываю

Начинаю с **выделения домена и границ**, а не с правки SQL.

**1. Доменный слой**
```php
final class OrderId { public function __construct(public string $value) {} }

final class Order
{
    public function __construct(
        private OrderId $id,
        private string $email,
        private string $status
    ) {}
    public function pay(): void
    {
        if ($this->status !== 'new') throw new DomainException();
        $this->status = 'paid';
        $this->recordEvent(new OrderPaid($this->id));
    }
}
```

**2. Порты**
```php
interface OrderRepository { public function get(OrderId $id): Order; public function save(Order $o): void; }
interface PaymentMailer { public function sendPaid(Order $o): void; }
```

**3. Application Service**
```php
final class PayOrder
{
    public function __construct(
        private OrderRepository $repo,
        private EntityManagerInterface $em,
        private PaymentMailer $mailer
    ) {}
    public function __invoke(OrderId $id): void
    {
        $this->em->beginTransaction();
        try {
            $order = $this->repo->get($id);
            $order->pay();
            $this->repo->save($order);
            $this->em->flush();
            $this->em->commit();

            // асинхронно
            $this->bus->dispatch(new SendPaidEmail($id));
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
```

**4. Тонкий контроллер**
```php
final class OrderController
{
    public function __construct(private PayOrder $payOrder) {}
    public function pay(Request $req): Response
    {
        $id = new OrderId($req->get('order_id'));
        $this->payOrder->__invoke($id);
        return new Response('ok', 200);
    }
}
```

Репозиторий использует подготовленные запросы Doctrine / DBAL, маппинг и транзакции.

### С чего начинаю и почему

**С безопасности и транзакционности.** SQL-инъекция и потеря данных — критично. Сначала закрываю дыру подготовленным запросом и транзакцией.

Потом **выношу бизнес-логику из контроллера** в Application Service. Контроллер должен только переводить HTTP ↔ DTO.

Дальше **домен и репозиторий** — чтобы инварианты были защищены и код стал тестируемым.

Почта и логирование уходят в асинхронные обработчики, чтобы не замедлять ответ.

Итого: сначала стопим кровь, потом лечим архитектуру.

> ⏱ 11.5s · prompt 21676 tok · output 884 tok

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

Эта сущность нарушает базовые принципы DDD и чистой архитектуры. Она знает про инфраструктуру и сама себя сохраняет.

### Что не так

**1. Инверсия зависимостей**
`User` зависит от `EntityManagerInterface` — инфраструктурного компонента Doctrine. Домен не должен знать про ORM, БД, фреймворки. Это ломает чистую архитектуру: зависимость идёт наружу, а должна быть внутрь.

**2. Сущность управляет persistence**
`changeEmail` вызывает `$this->em->flush()`. Сущность не должна знать как и когда её сохранять. Сохранение — ответственность Application Layer / репозитория. Сущность должна только менять своё состояние и гарантировать инварианты.

**3. Скрытый сайд-эффект**
Вызов геттера `email()` выглядит безопасно, а `changeEmail()` делает запись в БД. Это непредсказуемо и не тестируемо.

**4. Нет инкапсуляции инвариантов**
Email можно поменять на пустую строку, невалидный формат. Нет Value Object `Email`.

**5. Конструктор с инфраструктурой**
Создание сущности требует `EntityManager`. Это мешает созданию объектов в тестах и в доменной логике.

### Как исправить

**Доменная сущность чистая, без зависимостей**
```php
final class User
{
    public function __construct(
        private UserId $id,
        private Email $email,
    ) {}

    public function changeEmail(Email $newEmail): void
    {
        $this->email = $newEmail;
        // только меняем состояние, никаких flush
    }

    public function email(): Email { return $this->email; }
}
```

**Value Object для Email**
```php
final readonly class Email
{
    public function __construct(public string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Invalid email');
        }
    }
}
```

**Репозиторий в Application Layer**
```php
final class ChangeUserEmail
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(UserId $id, Email $email): void
    {
        $user = $this->users->get($id);
        $user->changeEmail($email);
        $this->em->flush(); // сохранение здесь
    }
}
```

**Итого:** сущность содержит только бизнес-логику и инварианты. `EntityManager`, `flush`, HTTP, почта — всё снаружи. Домен остаётся чистым, тестируемым и независимым от фреймворка.

> ⏱ 7.6s · prompt 22609 tok · output 566 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Подход системный: сначала понять, потом действовать, а не сразу лезть в код.

### Последовательность действий

**1. Контекст и цель**
Читаю задачу, не код. Что должно получиться, какие критерии приёмки, какой бизнес-эффект. Смотрю связанный тикет, документацию, ADRs.

**2. Карта местности**
Собираю артефакты: репозиторий, README, docker-compose, CI пайплайн, конфиги. Запускаю локально, смотрю логи, health checks.

**3. Быстрый reconnaissance**
Ищу похожий код, тесты, примеры использования. `grep`, `git log --oneline --grep`, `openapi` спецификация. Понимаю как устроен поток данных.

**4. Минимальный воспроизводимый пример**
Пытаюсь воспроизвести проблему локально. Если баг — минимальный тест, который падает. Если фича — минимальный прототип.

**5. Гипотезы и план**
Формирую 2-3 гипотезы почему так происходит. План проверки: что смотреть, какие команды, какие метрики.

**6. Разбор**
Читаю код слоями: контроллер → application → domain → инфраструктура. Ставлю брейкпоинты, логи, `EXPLAIN ANALYZE`. Фиксирую находки.

**7. Решение и оценка**
После понимания проблемы оцениваю объём: трогаем ли один файл или меняем архитектуру. Даю срок с запасом.

**8. Проверка**
Тесты проходят, локально работает, PR готов.

### Когда просить помощь

**Разбираюсь сам, если:**
* Проблема локальная, понятный стек, есть тесты.
* Нужен 1-2 часа чтобы понять.
* Ошибка воспроизводится у меня.

**Прошу помощи, если:**
* Прошло >2 часа без прогресса, блуждаю.
* Проблема кросс-командная: инфраструктура, деплой, данные.
* Нужен контекст, которого нет в доках: «почему так сделали».
* Блокирующий вопрос по архитектуре/безопасности.

Правило: спрашиваю рано, но с контекстом. Не «у меня не работает», а «я сделал A, B, C, ожидаю X, получаю Y, логи вот». Так помощь приходит быстрее и я всё равно учусь.

Итого: сначала понять задачу, потом карту, потом воспроизвести, потом гипотезы. Помощь — когда застрял, а не когда лень читать.

> ⏱ 7.1s · prompt 23238 tok · output 538 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Сразу признаю ошибку и коммуницирую, не дожидаясь дедлайна.

**1. Стоп и честная оценка**
Останавливаюсь на 10 минут, фиксирую где именно недооценил: объём, зависимости, неизвестные технологии. Пересчитываю оставшееся в часах, а не «ещё чуть-чуть».

**2. Сообщаю немедленно, а не завтра утром**
Пишу тимлиду/PO прямо сейчас:
* Что было обещано и почему.
* Что выяснилось и почему оценка изменилась — факты, а не оправдания.
* Новая реалистичная оценка: 2 дня, с разбивкой по задачам.
* Влияние на зависимые задачи.

**3. Предлагаю варианты**
* Полный scope за 2 дня, сдвинуть дедлайн.
* Сокращённый MVP к завтра, остальное после.
* Поддержка/помощь от коллеги для параллелизации.

**4. Фиксую причину недооценки**
Чтобы не повторилось: был ли пробел в понимании требований, не учёл интеграцию, не проверил окружение. Делаю вывод и, если нужно, уточняю требования сейчас же.

**Почему так**
Скрыть и сдать костыль к завтра — хуже чем сдвинуть дедлайн. Доверие дороже одной задачи. Раннее оповещение даёт команде шанс перепланировать, а не ловить пожар в проде.

Правило: лучше сказать «я переоценил, нужно +1 день» сегодня, чем объяснять завтра почему задача не готова.

> ⏱ 5.8s · prompt 23810 tok · output 431 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

«Достаточно хорош» — это не про идеал, а про осознанный компромисс.

### Мой критерий отдачи

Код готов к отдаче, если:

1. **Бизнес-ценность доставлена.** Критерии приёмки выполнены, задача решает проблему.
2. **Безопасность и корректность.** Нет багов, гонок, SQL-инъекций, нарушенных инвариантов. Тесты покрывают критичный путь.
3. **Читаемость.** Другой разработчик поймёт код без объяснений. Имена, структура, границы соблюдены.
4. **Не создаёт технический долг с процентами.** Можно оставить, но не забыть. Если долг — создаю тикет в бэклог с контекстом.

Не готов, если:
* Есть known bug, который пользователь обязательно встретит.
* Нарушен инвариант домена.
* Код настолько запутан, что следующий PR сломает его.

### Баланс качество vs скорость

**1. Правило 80/20**
80% ценности за 20% усилий. Идеальный рефакторинг потом, если фича живёт.

**2. Зоны риска**
* Критичный путь денег/безопасности — качество превыше скорости, тесты обязательны.
* Внутренний админский скрипт — можно быстрее, с пометкой `// TODO`.

**3. Техдолг осознанно**
Если режу углы — оставляю комментарий `// TECHDEBT: ...` и тикет в бэклог. Не оставляю тишину.

**4. Итеративность**
Сначала MVP, который работает и покрыт тестами. Улучшения — в следующих итерациях, когда есть обратная связь.

**5. Code review как фильтр**
Если ревьюер спотыкается о код больше двух раз — это сигнал, что нужно упростить сейчас, а не потом.

**Итого:** качество — это не перфекционизм, а контроль рисков. Быстро, но без критичных ошибок и с понятным долгом. Когда сомневаюсь — отдаю, но фиксирую риски и план улучшения.

> ⏱ 6.0s · prompt 24196 tok · output 448 tok

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

**Кандидат:**

Собеседование было плотным и по делу — это плюс. Формат с постепенным углублением от общих принципов к конкретным кейсам работает хорошо, потому что видно как мыслишь, а не как заучиваешь определения.

**Что было полезным и точным**

* Вопросы про границы агрегата, N+1, оптимистичную блокировку, outbox и доменные события. Они сразу показывают реальный опыт, а не теорию. Ответ на них нельзя сгенерировать без практики.
* Вопросы про trade-offs: async vs sync, strict_types, SRP в реальном коде, выбор между voter и ролью. Там видно системное мышление.
* Кейсы с рефакторингом кода и нарушением чистоты архитектуры. Это лучший способ проверить, как человек отделяет домен от инфраструктуры.
* Вопрос про формулировку задачи из сырой проблемы бизнеса — очень сильный индикатор зрелости.

**Что было неудачным / вводящим в заблуждение**

* `FORCE INDEX` в PostgreSQL. Вопрос построен так, будто такая фича существует. В Postgres её нет, есть pg_hint_plan. Это проверка на знание специфики, но формулировка вводит в заблуждение.
* Вопрос про процент замедления от `strict_types=1`. Он звучит как ловушка на магическое мышление. Правильный ответ «0%», но вопрос сам по себе странный для оценки компетенций.
* Вопрос про Doctrine и составные первичные ключи «не поддерживает». Doctrine поддерживает, но плохо. Формулировка вводит в заблуждение и провоцирует на упрощённый ответ.

**Что бы изменил**

1. **Меньше фактографии, больше процесса.** Вместо «сколько процентов замедляет» лучше «как ты докажешь, что strict_types не влияет на перфоманс».
2. **Дать контекст.** На вопросы про архитектуру полезно давать небольшой сниппет проекта и ограничения, а не абстрактно.
3. **Пара вопросов на коммуникацию.** Уже были хорошие про оценку сроков и про конфликт с критериями приёмки, но можно добавить live разбор инцидента.
4. **Завершить практическим кейсом.** 10 минут парного рефакторинга кода в реальном времени даёт больше информации, чем 20 теоретических вопросов.

В целом формат сильный: ты держишь баланс между DDD, Symfony, PostgreSQL и soft skills. Если убрать 2-3 «ловушечных» вопроса про несуществующие фичи, получится очень чистое собеседование.

> ⏱ 7.6s · prompt 24712 tok · output 577 tok

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

**Кандидат:**

Спасибо за возможность рассказать это. За всеми вопросами про архитектуру и инструменты часто теряется главное — как человек работает в реальном проекте.

**Сильные стороны**
Я быстро вхожу в незнакомый код и умею видеть систему целиком, а не только свой кусок. Мне комфортно работать и с чистым доменом, и с грязной интеграцией. Умею писать код, который легко ревьюится и легко меняется потом.

**Подход к работе**
Сначала понимаю зачем, потом как. Любая задача начинается с контекста: какую боль бизнеса она закрывает и как мы поймём, что она решена. Я не люблю писать код в вакууме. Если требования расплывчаты — уточняю до старта, а не гадаю по ходу.

Я придерживаюсь принципа «сначала сделай правильно, потом быстро». Быстрый прототип — ок, но с пониманием где технический долг и с планом его закрыть.

**Коммуникация**
Мне важно держать команду в курсе. Если вижу, что оценка не сходится, говорю об этом сразу, а не в день дедлайна. В ревью я ищу не ошибки, а возможности сделать код понятнее для следующего человека.

**Принципы**
* Код — это сообщение коллегам.
* Домен важнее фреймворка.
* Тесты — это спецификация, а не галочка.
* Лучше один маленький рабочий PR, чем большой идеальный.

**Что важно знать**
Я люблю разбирать сложные кейсы и доводить их до простого решения. Мне комфортно в роли наставника для младших разработчиков и в роли исполнителя, когда нужна концентрация. И я умею останавливаться и пересматривать решение, если понимаю, что пошло не туда.

Если коротко: я делаю код, которым не стыдно пользоваться через год.

> ⏱ 5.4s · prompt 25283 tok · output 390 tok

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

**Кандидат:**

Мне ближе всего PHP-экосистема, и именно её я бы выбрал для нового бизнес-проекта со сложной доменной логикой.

### Выбор

**Backend:** PHP 8.3+ + Symfony 7 + Doctrine ORM + PostgreSQL  
**Async:** Symfony Messenger + RabbitMQ / Redis  
**Read side:** PostgreSQL + Elasticsearch для поиска  
**Infra:** Docker, Kubernetes, GitLab CI, PHPStan, Psalm, PHPUnit

### Почему именно так

**1. Скорость разработки бизнес-ценности**
Symfony даёт готовый каркас: DI, events, security, console, messenger. Не тратим месяцы на сборку инфраструктуры, сразу пишем домен. PHP 8+ с union types, readonly, attributes делает код выразительным.

**2. Зрелость экосистемы**
Doctrine + PostgreSQL — проверенное сочетание для сложных доменных моделей. Symfony Messenger решает асинхронность без изобретения велосипеда. Инструменты качества — Psalm, PHPStan, PHPUnit — позволяют держать высокий уровень.

**3. Команда и найм**
PHP-разработчиков много, входной порог ниже, чем у niche стеков. А значит быстрее собрать команду и поддерживать проект годами.

**4. Стоимость владения**
Хостинг дешёвый, деплой простой, нет проблем с сборкой как в Node. Для SaaS/B2B это критично.

**Когда бы не выбрал PHP**
Если нужен real-time стриминг, нативные мобильные перформанс-критичные сервисы, или команда уже экспертна в Go/Rust. Для high-frequency trading, ML пайплайнов — да.

Но для 90% веб-приложений с бизнес-логикой, админками, интеграциями и платёжными потоками — PHP + Symfony + PostgreSQL даёт лучший баланс: быстро писать, легко поддерживать, просто масштабировать.

Это стек, в котором я могу быстро выдать качественный результат и не тратить время на борьбу с инструментами.

> ⏱ 5.6s · prompt 25712 tok · output 412 tok

---
