# Собес: `huihui_ai/Qwen3.8-abliterated:latest` · роль: backend — run-02

- Дата: 2026-10-07T03:06:24.964199+00:00
- Сервер: http://nserv:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Рад знакомству. Я не человек, а языковая модель, но я обработал огромный массив знаний о разработке, включая бесчисленное количество документации, исходных кодов популярных библиотек и обсуждений в developer-сообществах.

Мой «опыт» заключается в способности быстро анализировать контекст, генерировать и рефакторить код, объяснять сложные концепции и находить нетривиальные решения проблем. Я особенно силён в связках **PHP 8 + Symfony + Doctrine**, так как это один из самых распространённых и хорошо документированных стеков. Я отлично понимаю принципы **чистой архитектуры** и **DDD**, что позволяет мне не просто писать код, который работает, но и структурировать его так, чтобы он был масштабируемым и поддерживаемым.

Ниже — моя самооценка по вашим разделам. Важно понимать, что оценки отражают глубину понимания, которое я могу предложить *вам* в контексте нашего разговора, а не мой «личностный» стаж.

| Технология / Область | Оценка (0–10) | Обоснование в одном предложении |
| :--- | ::---: | :--- |
| **PHP 8** | 9.5 | Я глубоко понимаю все новые фичи PHP 8 (readonly, enums, fiber, named arguments), их внутреннюю реализацию и лучшие практики использования. |
| **Symfony** | 9.5 | Я хорошо знаю архитектуру Symfony, систему сервисов, события, бандлы и оптимизации производительности на уровне ядра фреймворка. |
| **Doctrine** | 9.0 | Я владею ORM-моделью, N+1 проблемами, стратегиями миграций и тонкостями управления сущностями, хотя в экзотических кейсах может потребоваться уточнение контекста. |
| **PostgreSQL** | 8.5 | Я понимаю модели данных, индексы, транзакции, оконные функции и оптимизацию запросов, но не всегда заменяю профильную экспертизу DBA в части администрирования кластеров. |
| **DDD** | 9.0 | Я умею применять тактики DDD (агрегаты, bounded contexts, доменные события) в коде, но их успешное применение сильно зависит от корректного бизнес-контекста, который мы должны прояснить вместе. |
| **Чистая архитектура** | 9.0 | Я чётко понимаю принципы SOLID, Inversion of Control и разделение ответственности, чтобы минимизировать зависимости и повысить тестируемость кода. |
| **Тестирование** | 9.0 | Я пишу юнит-, интеграционные и функциональные тесты на PHPUnit/Behat/Pest, включая mocking и stubbing, соблюдая принципы TDD/BDD. |
| **Git** | 9.5 | Я свободно владею основными и продвинутыми командами git (rebase, cherry-pick, bisect), а также пониманием истории коммитов и CI/CD интеграцией. |

**Как мы можем работать вместе?**
Вы можете:
1. Дать мне задачу на проектирование (например, «Как спроектировать систему уведомлений в Symfony с использованием DDD?»).
2. Показать фрагмент кода, который нужно отрефакторить или покрыть тестами.
3. Обсудить архитектурные решения или выбрать между двумя подходами.

С чего начнём?

> ⏱ 9.9s · prompt 89 tok · output 769 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

С точки зрения **чистой архитектуры** и **надежности**, ключевыми фичами PHP 8+ являются те, которые уменьшают «пространство для ошибок» (error surface), улучшают читаемость и позволяют статически проверять код до его выполнения.

Вот самые важные из них с примерами:

### 1. `readonly`-свойства (PHP 8.2)
Это революция для иммутабельных доменных объектов. Раньше нужно было использовать `final class` + только getter'ы или вручную проверять перезапись. Теперь компилятор гарантирует, что состояние объекта не изменится после конструирования.

```php
// Плохо (PHP < 8.2): Легко случайно изменить $id
class OrderOld
{
    public string $id;
    public float $amount;
    
    public function __construct(string $id, float $amount)
    {
        $this->id = $id;
        $this->amount = $amount;
    }
}

// Хорошо (PHP 8.2): Иммутабельность на уровне языка
final readonly class Order
{
    public function __construct(
        public string $id,
        public float $amount,
    ) {}
    
    // Попытка $order->amount = 100.0; вызовет Fatal Error
}
```
**Почему это важно для чистой архитектуры:** Доменные сущности должны быть предсказуемыми. `readonly` устраняет классический баг «неявное изменение состояния» и упрощает reasoning о коде.

### 2. Enums (PHP 8.1)
Заменяют магии числа и строковые константы. Enums дают исчерпывающий набор значений, типобезопасность и возможность добавлять поведение.

```php
// Плохо: Строчные магические числа
class OrderStatusOld
{
    const PENDING = 'pending';
    const PAID = 'paid';
}

// Хорошо: Enum
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    
    // Можно добавить логику, связанную со статусом
    public function isPaid(): bool
    {
        return $this === self::Paid || $this === self::Shipped;
    }
    
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Ожидает оплаты',
            self::Paid => 'Оплачен',
            self::Shipped => 'Отправлен',
        };
    }
}
```
**Почему это важно:** Убирает «магические строки», которые можно было опечатать (`'pening'` vs `'pending'`). В DDD это идеал для доменных значений (Value Objects).

### 3. Named Arguments (PHP 8.0)
Повышает читаемость вызовов функций и конструкторов, особенно когда параметры имеют похожие типы (например, два `int`).

```php
// Неясно, что есть width, а что height?
createRectangle(10, 20);

// Явно:
createRectangle(width: 10, height: 20);

// В конструкторах — это огромно для DDD-сущностей
class Product
{
    public function __construct(
        public string $name,
        public float $price,
        public ?string $sku = null,
    ) {}
}

// Теперь можно создавать объект, пропуская необязательные параметры
$prod = new Product(name: 'Laptop', price: 999.99); 
// sku = null, без необходимости передавать null явно
```
**Почему это важно:** Уменьшает когнитивную нагрузку при чтении кода и делает код самообъясняемым.

### 4. Union & Intersection Types (PHP 8.0+)
Позволяют выражать сложные доменные правила прямо в сигнатурах.

```php
// Union: Метод может принять строку ИЛИ целое
function parseInput(string|int $value): string
{
    return is_int($value) ? (string)$value : $value;
}

// Intersection: Объект должен быть одновременно Stringable и Countable
function renderHeader(Stringable & Countable $content): void
{
    // Гарантируется, что у $content есть и __toString(), и count()
}
```
**Почему это важно:** Сужает типы, которые могут быть переданы в метод, что помогает статическим анализаторам (PHPStan/Psalm) находить ошибки раньше.

### 5. `match` выражение (PHP 8.0)
Заменяет `switch` с двумя ключевыми улучшениями:
1. Возвращает значение, а не просто исполняет блок.
2. Использует строгое сравнение (`===`), а не слабое (`==`), как `switch`.

```php
// switch (слабое сравнение, может дать неожиданные результаты)
// 1 == '1' → true

// match (строгое сравнение)
// 1 === '1' → false (безопаснее!)

$status = match($order->status) {
    'pending' => 'Ожидает',
    'paid'    => 'Оплачен',
    default   => 'Неизвестно',
};
```
**Почему это важно:** Безопасность типов + чистота (expression, а не statement). Идеально для маппинга доменных состояний.

---

### Бонус: Constructor Property Promotion (PHP 8.0)
Уже упомянут в примере с `Order`, но заслуживает отдельного упоминания.
```php
final class User {
    public function __construct(
        public string $name,
        public int $age,
    ) {}
}
// Вместо:
// public string $name;
// public int $age;
// public function __construct(string $name, int $age) {
//     $this->name = $name;
//     $this->age = $age;
// }
```
**Почему это важно:** Сокращает boilerplate-код на 80% при создании DTO и Value Objects, что критично для чистого кода.

---

### Что я **не** считаю критически важным для «чистоты» в PHP 8.0+ (хотя это полезно):
- **Fibers**: Мощно, но больше для инфраструктуры (async), чем для бизнес-логики.
- **Asymmetric Visibility** (PHP 8.2): `public readonly` — пока ниша, редко используется в чистом DDD.
- **New in Initializers** (PHP 8.1): `$x = $a ? new Foo() : new Bar();` — удобно, но не меняет парадигму.

### Итог
Для написания **чистого и надежного** кода в Symfony/DDD стеке я бы рекомендовал использовать:
1. **`readonly` + Constructor Promotion** для всех DTO и Value Objects.
2. **Enums** для всех ограниченных наборов значений (статусы, типы).
3. **Union/Intersection Types** для выражения доменных правил.
4. **`match`** для безопасных ветвлений.

Эти фичи вместе создают «язык, который не дает сломать код», что и есть цель чистой архитектуры.

> ⏱ 13.1s · prompt 916 tok · output 1689 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это исторически сложный случай: она эволюционировала от «все — строки и числа» к строгой, но по-прежнему гибкой и опасной системе. Для **чистой архитектуры** это критически важно, потому что неявные приведения типов — один из главных источников багов.

### 1. `strict_types`: Что это и почему оно нужно

`strict_types` — это **ключевое слово файла**, а не глобальный переключатель. Оно включается в начале файла (после `<?php`, но до любого кода, кроме `declare`).

```php
<?php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}
```

**Как это работает:**
- Если `strict_types=1` включен **в вызывающем файле** (не в файле функции!), то при передаче аргумента происходит проверка на **точное совпадение типов** (strict mode).
- Если включен **в файле функции**, это **ничего не меняет** для вызовов. Это главная ловушка.

**Почему это важно для чистой архитектуры:**
Без `strict_types` PHP позволяет передавать `'10'` в параметр `int`. Это работает, но скрывает ошибки логики. С `strict_types` вы получаете `TypeError` на ранней стадии, что соответствует принципу «Fail Fast».

**Лучшая практика:**
1. **Всегда** включайте `declare(strict_types=1);` во всех файлах (кроме, возможно, файлов с конфигурациями, где нужно сохранить обратную совместимость).
2. **Всегда** указывайте типы в сигнатурах функций и свойств.
3. **Избегайте** кода, который полагается на слабые приведения.

---

### 2. Механизмы приведения типов: Слабая и строгая модель

PHP имеет **две модели приведения типов**:

| Действие | Слабая модель (`==`) | Строгая модель (`===`, `strict_types`) |
| :--- | :--- | :--- |
| `'0' == false` | `true` | `false` |
| `'1' == 1` | `true` | `false` |
| `'' == 0` | `true` | `false` |
| `null == 0` | `true` | `false` |
| `[] == false` | `true` (PHP 8) | `false` |

**Ключевое правило:**
- **Слабое сравнение** (`==`) пытается привести типы к общему.
- **Строгое сравнение** (`===`) сначала сравнивает **тип**, затем **значение**.
- **`strict_types`** заставляет **функции** вести себя как при строгом сравнении при проверке аргументов.

---

### 3. Типичные ловушки и примеры

#### Ловушка 1: «Магические» приведения в `strict_types`

```php
<?php
declare(strict_types=1);

function process(string $s): string {
    return $s . '!';
}

// Это РАБОТАЕТ, потому что 'abc' — строка
echo process('abc'); // "abc!"

// Это ПЕРЕДАЕТСЯ, если бы strict_types не было:
// Но так как strict_types=1, а мы передаем int 123 в string $s:
echo process(123); // TypeError: Argument #1 ($s) must be of type string, int given

// А что, если мы хотим принять и строку, и число?
function process2(string|int $s): string {
    return (string)$s . '!';
}
echo process2(123); // "123!" — теперь безопасно и явно
```

**Вывод:** `strict_types` не запрещает приведения, он требует **явного** указания допустимых типов через Union Types.

#### Ловушка 2: Сравнение `null` и `false`

```php
<?php
// Это классический баг в условиях if
if ($value == null) {
    // Сюда попадут: null, false, '', 0, []
    // Но не 123, 'abc', [1,2]
}

// Правильно:
if ($value === null) {
    // Только null
}

// Или, если нужно проверить на «пустое»:
if ($value === null || $value === '' || $value === []) {
    // Явно перечисляем допустимые «пустые» значения
}
```

**Почему это важно:** В DDD вы должны знать, означает ли `null` «не задано», а `false` — «явно отклонено». Смешивание их через `==` разрушает семантику.

#### Ловушка 3: Числовые строки и `int`

```php
<?php
declare(strict_types=1);

function multiply(int $a, int $b): int {
    return $a * $b;
}

// Это РАБОТАЕТ в слабом режиме, но в strict_types:
// multiply('10', '20'); // TypeError!

// Но вот что удивляет:
// Если вы передаете '10.5' в int — это тоже TypeError в strict_types.
// В слабом режиме: (int)'10.5' = 10.

// Решение:
function multiply2(int|float $a, int|float $b): int|float {
    return $a * $b;
}
```

**Вывод:** Не полагайтесь на автоматическое приведение строк к числам. Если нужно принять число из строки, сделайте это явно: `intval($str)` или `floatval($str)`, или используйте Union Type `string|int`.

#### Ловушка 4: `bool` из «ложных» значений

```php
<?php
// Что вернет этот код?
function isActive(string $status): bool {
    return $status; // Явная ошибка: возвращаем string, а ожидаем bool
}

// Правильно:
function isActive2(string $status): bool {
    return $status === 'active';
}

// А что здесь?
function isEnabled(int $flag): bool {
    return $flag; // В слабом режиме: 0 → false, 1 → true, 2 → true
    // В strict_types: TypeError, потому что int != bool
}

// Решение:
function isEnabled2(int $flag): bool {
    return $flag === 1; // Или $flag > 0, если нужно
}
```

**Вывод:** Возвращение `int` из функции с типом `bool` — частый баг. Всегда явно приводите: `return (bool)$value;` или используйте `match`.

#### Ловушка 5: Сравнение массивов

```php
<?php
// Это УДИВЛЯЕТ:
$empty = [];
$false = false;

var_dump($empty == $false); // true (в слабом режиме)
var_dump($empty === $false); // false

// Но:
$empty == 0; // true!
$empty == '0'; // true!
$empty == 'abc'; // false

// В PHP 8.0+ пустой массив стал «ложным», но сравнение с числами все еще опасно.
// Правильно:
if ($array === []) {
    // Явная проверка на пустой массив
}
```

**Вывод:** Никогда не используйте `==` для сравнения массивов с другими типами. Используйте `===` или `count($arr) === 0`.

---

### 4. Где типизация спасает

#### Пример 1: API-контракт

```php
<?php
declare(strict_types=1);

// Клиент отправляет JSON:
// { "id": 123, "name": "John", "active": true }

function createDto(array $data): MyDto {
    // Без строгих типов можно случайно передать 'active' как string "true"
    // С Union Types и strict_types мы явно контролируем:
    
    if (!isset($data['id']) || !is_int($data['id'])) {
        throw new InvalidArgumentException('id must be int');
    }
    
    if (!isset($data['name']) || !is_string($data['name'])) {
        throw new InvalidArgumentException('name must be string');
    }
    
    if (!isset($data['active']) || !is_bool($data['active'])) {
        throw new InvalidArgumentException('active must be bool');
    }
    
    return new MyDto(
        id: $data['id'],
        name: $data['name'],
        active: $data['active']
    );
}
```

**Почему это спасает:** Вы обнаруживаете несовпадение типов **на границе** (вход в систему), а не в глубине бизнес-логики. Это принцип «Defensive Programming».

#### Пример 2: Методы DDD-сущности

```php
<?php
declare(strict_types=1);

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {}
    
    public function add(Money $other): self {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Cannot add different currencies');
        }
        
        return new self(
            amount: $this->amount + $other->amount,
            currency: $this->currency
        );
    }
}

// $a = new Money(100, 'USD');
// $b = new Money('200', 'USD'); // TypeError: amount must be int
// $sum = $a->add($b); // Безопасно: типы совпадают
```

**Почему это спасает:** Вы не можете случайно сложить `Money(100, 'USD')` и `Money('200', 'EUR')`, потому что тип `Money` и проверка внутри метода предотвращают это.

---

### 5. Где типизация удивляет

#### Пример 1: `string` vs `int` в ключах массивов

```php
<?php
$arr = [
    'id' => 123,
    123 => 'id',
];

var_dump($arr['123']); // 'id' — потому что ключ '123' приводится к 123
var_dump($arr[123]); // 'id' — то же самое

// Но:
$arr['123'] = 'new';
var_dump($arr[123]); // 'new' — теперь оба ключа указывают на одно значение!

// Это классический баг: вы думаете, что 'id' и 123 — разные ключи, но это одно и то же.
```

**Вывод:** В массивах **всегда** используйте строгие ключи. Если смешиваете `int` и `string`, помните, что `123 === '123'` для ключей массива.

#### Пример 2: `null` в арифметических операциях

```php
<?php
$a = null;
$b = 5;

var_dump($a + $b); // 5 (null приводится к 0)
var_dump($a === 0); // false
var_dump($a == 0);  // true

// Это может маскировать ошибки: если вы ожидаете число, а получаете null, вы получите 0, а не ошибку.
// Решение: используйте `strict_types` и явные проверки:
if ($a === null) {
    throw new UnexpectedValueException('Expected int, got null');
}
```

---

### 6. Рекомендации для чистой архитектуры

1. **Всегда** включайте `declare(strict_types=1);` во всех файлах.
2. **Всегда** указывайте типы параметров и возвращаемых значений.
3. **Избегайте** `==` для сравнения. Используйте `===` или `match`.
4. **Используйте** Union Types (`int|float`, `string|null`) для выражения допустимых значений.
5. **Проводите** валидацию на границах системы (в контроллерах, сервисах входа в домен).
6. **Используйте** `readonly` для иммутабельных объектов.
7. **Тестируйте** граничные случаи: `null`, `0`, `''`, `[]`, `false`.

**Итог:** PHP — это язык с **управляемой** неопределенностью. Типизация — ваш главный инструмент для контроля этой неопределенности. Чем строже вы используете типы, тем меньше «магии» в вашем коде, тем предсказуемее он становится.

> ⏱ 21.8s · prompt 2669 tok · output 2948 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование работы с исключениями — это критически важный аспект **чистой архитектуры**, потому что исключения — это не только про ошибки, но и про **семантику** и **контракты** между слоями.

В PHP (и особенно в стеке Symfony) я придерживаюсь следующего подхода:

### 1. Иерархия исключений

**Принцип:** Каждое исключение должно быть специфичным. Избегайте использования `Exception` или `RuntimeException` напрямую в бизнес-логике.

**Структура:**

```php
// 1. Базовое доменное исключение (абстрактное)
abstract class DomainException extends \Exception
{
    // Можно добавить общие методы, например, getCode() с бизнес-кодом
}

// 2. Исключения конкретного домена
class OrderException extends DomainException {}
class UserException extends DomainException {}

// 3. Специфичные исключения (конкретные сценарии)
class OrderAlreadyPaidException extends OrderException
{
    public function __construct(Order $order)
    {
        parent::__construct(
            sprintf('Order %s is already paid', $order->getId()),
            code: 409
        );
    }
}

class InsufficientFundsException extends OrderException
{
    public function __construct(Order $order, float $available, float $required)
    {
        parent::__construct(
            sprintf('Insufficient funds. Available: %.2f, Required: %.2f', $available, $required),
            code: 422
        );
    }
}

// 4. Исключения инфраструктуры (для внешних систем)
class PaymentGatewayException extends \Exception {}
class DatabaseException extends \Exception {}
```

**Почему это важно:**
- **Доменные исключения** (`DomainException`) — это **ожидаемые** ошибки бизнес-логики. Клиент может их обработать и показать пользователю понятное сообщение.
- **Инфраструктурные исключения** (`PaymentGatewayException`) — это **неожиданные** ошибки. Их нужно логировать, но не показывать пользователю как есть.

### 2. Оборачивание чужих исключений

**Принцип:** Никогда не пропускайте наружу исключения, которые не являются частью вашего контракта. Оборачивайте их в доменные исключения.

**Плохо:**
```php
class PaymentService
{
    public function process(Order $order): Payment
    {
        $response = $this->gateway->charge($order->getAmount());
        // Если gateway бросит GatewayTimeoutException, это утечка инфраструктурной детали
        return new Payment($response);
    }
}
```

**Хорошо:**
```php
class PaymentService
{
    public function process(Order $order): Payment
    {
        try {
            $response = $this->gateway->charge($order->getAmount());
            return new Payment($response);
        } catch (GatewayTimeoutException $e) {
            // Оборачиваем в доменное исключение
            throw new PaymentProcessingException('Payment gateway timed out', previous: $e);
        } catch (GatewayError $e) {
            // Оборачиваем в специфичное доменное исключение
            throw new PaymentDeclinedException('Payment was declined by gateway', previous: $e);
        }
    }
}
```

**Почему это важно:**
- **Изоляция:** Бизнес-логика не знает о деталях конкретной реализации (Stripe, PayPal, локальная БД).
- **Контракт:** Клиент знает, какие исключения может бросить `process()`, и может их обрабатывать.
- **Логирование:** Вы можете логировать `$e->getPrevious()` для отладки, а пользователю показывать только сообщение доменного исключения.

### 3. Контракт метода

**Принцип:** Документируйте (через PHPDoc или код) какие исключения метод **может** бросить.

**Пример:**
```php
interface OrderRepository
{
    /**
     * @throws OrderNotFoundException Если заказ не найден
     * @throws DatabaseException Если произошла ошибка БД
     */
    public function findById(string $id): Order;
    
    /**
     * @throws OrderAlreadyPaidException Если заказ уже оплачен
     * @throws InvalidOrderStateException Если заказ в неверном состоянии
     */
    public function pay(Order $order): void;
}
```

**Что считается контрактом:**
1. **Тип возвращаемого значения** (например, `Order`).
2. **Типы параметров** (например, `string $id`).
3. **Исключения, которые может бросить метод** (доменные и, возможно, системные).
4. **Побочные эффекты** (например, изменение состояния БД).

### 4. Антипаттерны

#### Антипаттерн 1: «Широкие» исключения

```php
// Плохо: Слишком общее исключение
class ApplicationException extends \Exception {}

class OrderService
{
    public function create(array $data): Order
    {
        if (!isset($data['name'])) {
            throw new ApplicationException('Name is required'); // Какое именно поле?
        }
    }
}
```

**Решение:** Используйте специфичные исключения или, лучше, **валидацию** (см. ниже).

#### Антипаттерн 2: Исключения для нормального потока

```php
// Плохо: Исключение для «не найдено» в списке
function findAdmin(array $users): User
{
    foreach ($users as $user) {
        if ($user->isAdmin()) {
            return $user;
        }
    }
    throw new UserNotFoundException('Admin not found');
}

// Хорошо: Возврат null или Optional (в PHP можно использовать ?User)
function findAdmin(array $users): ?User
{
    foreach ($users as $user) {
        if ($user->isAdmin()) {
            return $user;
        }
    }
    return null;
}
```

**Почему это важно:** Исключения должны использоваться для **ненормальных** ситуаций. Если «не найдено» — это ожидаемый сценарий, лучше вернуть `null` или пустой коллекцию.

#### Антипаттерн 3: Поглощение исключений

```php
// Плохо: Исчезновение ошибки
try {
    $order->pay();
} catch (OrderException $e) {
    // Логирование, но нет ответа клиенту
    $this->logger->error($e->getMessage());
    return new Response('', 200); // Успех?!
}
```

**Решение:** Если вы ловите исключение, вы должны **что-то с ним сделать**: перебросить, обернуть, или ответить клиенту соответствующим кодом статуса.

#### Антипаттерн 4: Использование исключений для контроля потока

```php
// Плохо: Перебор через исключения
try {
    $item = $items[$index];
} catch (ArrayAccessException $e) {
    $index = 0;
}
```

**Решение:** Используйте проверки условий: `if (isset($items[$index]))`.

#### Антипаттерн 5: Исключения без контекста

```php
// Плохо:
throw new \Exception('Error');

// Хорошо:
throw new OrderException(sprintf('Order %s failed to pay: %s', $order->getId(), $reason));
```

### 5. Рекомендации для Symfony

1. **Используйте `Symfony\Component\HttpKernel\Exception`** для HTTP-уровня:
   - `NotFoundHttpException` (404)
   - `BadRequestHttpException` (400)
   - `ConflictHttpException` (409)
   - `UnprocessableEntityHttpException` (422)

2. **Преобразование доменных исключений в HTTP-исключения:**
   ```php
   class OrderController
   {
       public function pay(Order $order): Response
       {
           try {
               $this->orderService->pay($order);
           } catch (OrderAlreadyPaidException $e) {
               throw new ConflictHttpException($e->getMessage());
           } catch (InsufficientFundsException $e) {
               throw new UnprocessableEntityHttpException($e->getMessage());
           }
           
           return new Response('Paid', 200);
       }
   }
   ```

3. **Глобальный обработчик исключений** (Listener) для необработанных ошибок:
   ```php
   class ExceptionListener implements EventSubscriberInterface
   {
       public function onException(ExceptionEvent $event): void
       {
           $exception = $event->getException();
           
           if ($exception instanceof DomainException) {
               $event->setResponse(new JsonResponse([
                   'error' => $exception->getMessage(),
                   'code' => $exception->getCode()
               ], 400));
           } else {
               $event->setResponse(new JsonResponse([
                   'error' => 'Internal Server Error'
               ], 500));
           }
       }
   }
   ```

### Итог

- **Создавайте** иерархию исключений: базовое доменное → специфичные доменные → инфраструктурные.
- **Оборачивайте** чужие исключения в свои доменные.
- **Документируйте** контракт метода через PHPDoc.
- **Избегайте** использования исключений для нормального потока и широких, неинформативных исключений.
- **Используйте** HTTP-исключения в контроллерах для перевода доменных ошибок в ответы API.

> ⏱ 15.8s · prompt 5675 tok · output 2214 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему опыту работы с кодовой базой и анализа типовых проблем, чаще всего нарушается принцип **SRP (Single Responsibility Principle — Принцип Единой Ответственности)**.

### Почему именно SRP?

1.  **Сложность определения «ответственности».** В отличие от `LSP` или `ISP`, которые требуют явного наследования или интерфейсов, SRP — это вопрос *архитектурного мышления*. Легко спрятать 3 разные ответственности в один класс, и он будет «работать».
2.  **Лень и скорость разработки.** Легче добавить метод в существующий класс, чем создавать новый. Это приводит к «Большим Богам» (God Objects).
3.  **Изменения по разным причинам.** Если класс меняется из-за изменения формата email, из-за изменения бизнес-логики отправки, и из-за изменения логирования — он нарушает SRP.
4.  **PHP-специфика:** Легко создать «сервис-швабру» (`UserService`, `OrderService`), который делает всё: валидацию, вызов репозитория, отправку email, запись в лог, формирование JSON-ответа.

---

### Типовой пример нарушения: «Бог» `OrderService`

**Ситуация:** Класс `OrderService` отвечает за:
1.  Проверку доступности товара.
2.  Расчет итоговой суммы (с логикой скидок).
3.  Сохранение заказа в БД.
4.  Отправку уведомления клиенту.
5.  Логирование.
6.  Формирование ответа для API.

**Код до (Нарушение SRP):**

```php
<?php
declare(strict_types=1);

// Один класс делает слишком много вещей
class OrderService
{
    private OrderRepository $orderRepo;
    private ProductRepository $productRepo;
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(
        OrderRepository $orderRepo,
        ProductRepository $productRepo,
        MailerInterface $mailer,
        LoggerInterface $logger
    ) {
        $this->orderRepo = $orderRepo;
        $this->productRepo = $productRepo;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    // МЕТОД ДЕЛАЕТ ВСЁ
    public function createOrder(array $items): array
    {
        // 1. Ответственность: Валидация товаров
        foreach ($items as $item) {
            $product = $this->productRepo->find($item['productId']);
            if (!$product || $product->getStock() < $item['quantity']) {
                throw new RuntimeException('Product not available: ' . $item['productId']);
            }
        }

        // 2. Ответственность: Расчет суммы (бизнес-логика)
        $total = 0;
        $discount = 0;
        foreach ($items as $item) {
            $product = $this->productRepo->find($item['productId']); // ДУБЛЬ ЗАПРОС!
            $total += $product->getPrice() * $item['quantity'];
            if ($total > 1000) {
                $discount = 100; // Логика скидки «зажата» здесь
            }
        }
        $total -= $discount;

        // 3. Ответственность: Создание сущности и сохранение
        $order = new Order();
        $order->setItems($items);
        $order->setTotal($total);
        $this->orderRepo->save($order);

        // 4. Ответственность: Отправка почты
        $email = "Order #" . $order->getId() . " created. Total: $" . $total;
        $this->mailer->send('user@example.com', $email);

        // 5. Ответственность: Логирование
        $this->logger->info('Order created', ['id' => $order->getId(), 'total' => $total]);

        // 6. Ответственность: Формирование ответа API
        return [
            'id' => $order->getId(),
            'total' => $total,
            'status' => 'pending'
        ];
    }
}
```

**Проблемы:**
*   **Трудно тестировать:** Чтобы протестировать расчет суммы, нужно мокать `ProductRepository`, `Mailer`, `Logger`.
*   **Сложно менять логику скидки:** Нужно переписывать весь метод `createOrder`.
*   **Сложно менять формат почты:** Нужно искать, где именно формируется строка.
*   **Дубль запросов в БД:** Продукт ищется дважды.

---

### Рефакторинг: Разделение ответственностей

Мы разделим класс на несколько маленьких, сфокусированных классов.

#### 1. `OrderFactory` (или `OrderCreator`)
Ответственность: Создание объекта `Order` на основе данных.

```php
<?php
declare(strict_types=1);

class OrderFactory
{
    public function create(array $items, float $total, ?User $user = null): Order
    {
        $order = new Order();
        $order->setUser($user);
        $order->setItems($this->mapItems($items));
        $order->setTotal($total);
        $order->setStatus(OrderStatus::Pending);
        
        return $order;
    }

    private function mapItems(array $items): array
    {
        // Здесь можно добавить маппинг на доменные объекты OrderItem
        return $items;
    }
}
```

#### 2. `OrderPricingStrategy` (или `PricingCalculator`)
Ответственность: Расчет итоговой суммы. Чистая функция/объект без побочных эффектов.

```php
<?php
declare(strict_types=1);

interface PricingStrategyInterface
{
    public function calculate(array $items): float;
}

class StandardPricingStrategy implements PricingStrategyInterface
{
    public function calculate(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Логика скидки теперь изолирована
        if ($total > 1000.0) {
            $total -= 100.0;
        }

        return $total;
    }
}

// Можно легко создать VipPricingStrategy, B2BPricingStrategy и т.д.
```

#### 3. `OrderService` (Тонкий сервис)
Ответственность: Координация (orchestration). Сам не содержит сложной бизнес-логики.

```php
<?php
declare(strict_types=1);

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepo,
        private OrderFactory $orderFactory,
        private PricingStrategyInterface $pricingStrategy,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function createOrder(array $validatedItems, ?User $user = null): Order
    {
        // 1. Расчет суммы (делегировано)
        $total = $this->pricingStrategy->calculate($validatedItems);

        // 2. Создание объекта (делегировано)
        $order = $this->orderFactory->create($validatedItems, $total, $user);

        // 3. Сохранение (делегировано репозиторию)
        $this->orderRepo->save($order);

        // 4. Побочные эффекты (отправка, логирование)
        $this->sendNotification($order, $user);
        $this->logOrderCreation($order);

        return $order;
    }

    private function sendNotification(Order $order, ?User $user): void
    {
        if ($user) {
            $email = sprintf("Order #%s created. Total: $%.2f", $order->getId(), $order->getTotal());
            $this->mailer->send($user->getEmail(), $email);
        }
    }

    private function logOrderCreation(Order $order): void
    {
        $this->logger->info('Order created', ['id' => $order->getId()]);
    }
}
```

#### 4. Контроллер (или Handler)
Ответственность: Взаимодействие с HTTP, маппинг ответа.

```php
<?php
declare(strict_types=1);

class OrderController
{
    public function __construct(private OrderService $orderService) {}

    public function create(Request $request): JsonResponse
    {
        // 1. Валидация входных данных (можно вынести в отдельный Validator)
        $data = $this->validate($request->getData());

        // 2. Вызов сервиса
        $order = $this->orderService->createOrder($data, $request->getUser());

        // 3. Формирование ответа
        return new JsonResponse([
            'id' => $order->getId(),
            'total' => $order->getTotal(),
            'status' => $order->getStatus()->value
        ], 201);
    }
}
```

---

### Почему это лучше?

1.  **Тестируемость:**
    *   `StandardPricingStrategy` можно тестировать юнит-тестами без мока БД, почты и логгера. Просто проверяем: `assertEquals(1100.0, $strategy->calculate($items))`.
    *   `OrderService` можно тестировать, мокнув только зависимости (`OrderRepo`, `Mailer`, `Logger`).
2.  **Масштабируемость:**
    *   Нужна новая логика скидок для VIP-клиентов? Создаем `VipPricingStrategy`. Не трогаем `OrderService`.
    *   Нужно отправить уведомление в Slack? Создаем `SlackNotifier`. Не трогаем `OrderService`.
3.  **Понятность:**
    *   Код читается как сценарий: *Рассчитать цену -> Создать заказ -> Сохранить -> Уведомить*.
4.  **Соблюдение DRY и SRP:**
    *   Каждый класс имеет одну причину для изменения.

### Как применить это в вашем коде?

1.  **Найдите «Богов»:** Посмотрите на классы с большим количеством зависимостей (инжекций) и длинными методами.
2.  **Выделите ответственности:** Задайте вопрос: «Если изменится X (например, расчет цены), этот класс потребует изменений? Если изменится Y (отправка email), он потребует изменений?» Если да — разделяйте.
3.  **Используйте Стратегии:** Для изменяющихся алгоритмов (прайсинг, валидация) используйте паттерн Strategy.
4.  **Тонкие сервисы:** Сервисный слой должен быть «тонким»: он координирует работу, но не содержит сложной бизнес-логики. Логика должна быть в доменных объектах или отдельных компонентах.

**Вывод:** SRP — это не про количество строк в файле, а про **причины изменения**. Если вы можете изменить код, не трогая другие его части, вы соблюдаете SRP.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно. Более того, в профессиональной разработке **нельзя** соблюдать SOLID «в лоб» в 100% случаев. SOLID — это эвристики (правила-направляющие), а не догмы. Слепое их соблюдение часто приводит к «Over-Engineering» (перепроектированию), усложнению кода и снижению производительности.

Принцип **KISS (Keep It Simple, Stupid)** и **YAGNI (You Aren't Gonna Need It)** часто важнее SOLID.

### Когда оправданно нарушать SOLID?

1.  **Миниатюрные приложения/скрипты:** Если проект будет жить 2 недели, создание 10 классов под один сценарий — это избыточно.
2.  **Критическая производительность:** Если абстракция добавляет накладные расходы (виртуальные вызовы, лишние проверки), а производительность критична — можно «загрубить» код.
3.  **Читаемость важнее абстракции:** Если код с соблюдением SOLID становится трудночитаемым (много переходов между файлами), а «нарушение» делает логику очевидной — выбирайте читаемость.
4.  **Временные решения (Technical Debt):** Если нужно быстро выйти в продакшен, можно нарушить SRP, помечая код комментарием `// TODO: Refactor after launch`.

---

### Пример оправданного нарушения: Принцип SRP (Single Responsibility)

**Контекст:** Маленький микросервис, который получает Webhook от платежного шлюза, проверяет подпись, обновляет статус заказа в БД и возвращает ответ.

**Ситуация:**
Создание отдельных классов `WebhookValidator`, `OrderUpdater`, `ResponseBuilder` для кода из 15 строк выглядит избыточно. Это нарушает SRP, если поместить всю логику в один контроллер/сервис.

**Код с «нарушением» SRP (Оправданно):**

```php
<?php
declare(strict_types=1);

// Один метод делает 3 вещи: валидацию, обновление, формирование ответа
// В "чистом" SOLID это было бы 3 разных класса.
class WebhookController
{
    public function __construct(
        private OrderRepository $repo,
        private PaymentSignatureValidator $validator // Это уже инкапсулированная сложность
    ) {}

    public function handle(Request $request): Response
    {
        // 1. Валидация (Ответственность 1)
        if (!$this->validator->isValid($request->headers->get('X-Signature'), $request->body)) {
            return new Response('Bad Signature', 401);
        }

        $data = json_decode($request->body, true);
        $orderId = $data['order_id'];
        $status = $data['status'];

        // 2. Обновление БД (Ответственность 2)
        $order = $this->repo->find($orderId);
        if ($order) {
            $order->setStatus($status);
            $this->repo->save($order);
        }

        // 3. Формирование ответа (Ответственность 3)
        return new Response('OK', 200);
    }
}
```

**Почему это оправданно?**
1.  **Размер:** Код занимает 15 строк. Выделение каждого шага в отдельный класс (`StatusUpdater`, `ResponseFormatter`) создаст 3 файла для одной атомарной операции.
2.  **Читаемость:** Логика «Валидируй → Обнови → Ответь» читается сверху вниз, без «прыжков» между классами.
3.  **Нет изменения логики:** Скорее всего, эта логика не изменится. Если изменится — тогда и будем рефакторить.
4.  **YAGNI:** Мы не знаем, что завтра понадобится «разное валидирование для разных шлюзов». Если понадобится — вынесем.

---

### Пример оправданного нарушения: ISP (Interface Segregation Principle)

**Контекст:** Интерфейс для логирования, который используется в 5 местах, и везде нужны все методы.

**Ситуация:**
ISP говорит: «Не заставляйте клиента зависеть от методов, которые он не использует». Но если **все** клиенты используют все методы, то создание множества мелких интерфейсов (`LoggerInterface`, `DebugLoggerInterface`) усложняет систему.

**Код с «нарушением» ISP (Оправданно):**

```php
<?php

// Большой интерфейс, который использует ВСЕ методы
interface LoggerInterface
{
    public function debug(string $message): void;
    public function info(string $message): void;
    public function warning(string $message): void;
    public function error(string $message): void;
    public function emergency(string $message): void;
}

// В 90% кода базового приложения используется этот большой интерфейс.
// Создавать LoggerInterface + DebugLoggerInterface + ErrorLoggerInterface 
// избыточно, если у нас только один реализатор (PSR-3).
class BaseService
{
    public function __construct(private LoggerInterface $logger) {}
}
```

**Почему это оправданно?**
1.  **Стандарт:** PSR-3 (стандарт PHP для логирования) предлагает один большой интерфейс. Его следованием считается «правильным».
2.  **Совместимость:** Если мы разобьем интерфейс, мы потеряем совместимость с любыми сторонними логгерами, которые реализуют полный PSR-3 интерфейс.
3.  **Практичность:** Разница между «мелкими» и «большим» интерфейсом не влияет на производительность, но влияет на сложность кодовой базы.

---

### Пример оправданного нарушения: DRY (Don't Repeat Yourself) — часто связывают с SOLID

**Контекст:** Два коротких фрагмента кода, которые почти одинаковы, но относятся к разным бизнес-доменам.

**Ситуация:**
DRY говорит: «Избегайте дублирования». Но если вы выносите общий код в отдельный класс, и он начинает меняться из-за изменений в одном домене, а потом в другом — вы создаете **Coupling** (связанность).

**Код с «нарушением» DRY (Оправданно):**

```php
<?php

// Домен A: Расчет скидки для физлиц
class IndividualDiscountCalculator
{
    public function calculate(float $price): float
    {
        if ($price > 1000) {
            return $price * 0.05;
        }
        return 0;
    }
}

// Домен B: Расчет скидки для юрлиц
// Логика почти такая же, но это РАЗНЫЕ бизнес-правила.
// Если объединить их в BaseDiscountCalculator, мы создадим скрытую связь.
// Если изменится логика для физлиц, юрлица могут пострадать.
class CorporateDiscountCalculator
{
    public function calculate(float $price): float
    {
        if ($price > 5000) {
            return $price * 0.02;
        }
        return 0;
    }
}
```

**Почему это оправданно?**
1.  **Разные причины изменения:** Скидка для физлиц меняется из-за маркетинговой кампании. Скидка для юрлиц — из-за соглашения с крупным клиентом.
2.  **Изоляция:** Дублирование 3 строк кода безопаснее, чем абстракция, которая скрывает разницу в бизнес-логике.
3.  **SRP важнее DRY:** Каждый калькулятор отвечает за одну ответственность (свой домен).

---

### Итог: Как принимать решения?

Перед тем как «исправить» нарушение SOLID, задайте 3 вопроса:

1.  **Код становится проще или сложнее для чтения?** (Если сложнее — нарушайте SOLID).
2.  **Насколько часто меняется эта логика?** (Если редко — не абстрагируйте).
3.  **Сколько мест будет затронуто изменением?** (Если 1–2 места — можно дублировать).

**Важно:** Нарушение SOLID — это **Technical Debt** (долг). Его можно брать, но нужно понимать, зачем. Если вы нарушаете SRP, потому что «так проще сейчас», это ок. Но если вы нарушаете LSP, потому что «не хочется создавать новый класс», а потом выясняется, что нужно 5 разных реализаций — это уже боль.

**Вывод:** SOLID — это инструменты, а не религия. Используйте их там, где они делают код **предсказуемым** и **гибким**. Если они делают код **запутанным** — убирайте.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Да, конечно. В современном PHP (особенно с PHP 8.0+ и в стеке Symfony) наследование (`extends`) часто рассматривается как «тяжелый» и «опасный» механизм (из-за проблемы «хрупкого базового класса»).

Для реализации **OCP (Open/Closed Principle — Принцип Открытости/Закрытости)** — то есть возможности расширять поведение без изменения существующего кода — существует несколько более гибких и безопасных подходов:

### 1. Composition over Inheritance (Составление вместо наследования)

Вместо того чтобы наследовать от базового класса и переопределять методы, вы **вставляете** в объект объект, который содержит новую логику.

**Пример:** Система отправки уведомлений.

❌ **Наследование (плохо):**
```php
class NotificationSender {
    public function send(User $user, string $message) {
        // Логика отправки по email
    }
}

class SmsNotificationSender extends NotificationSender {
    public function send(User $user, string $message) {
        // Логика отправки SMS
    }
}
// Чтобы добавить Telegram, нужно создать TelegramNotificationSender extends NotificationSender
// Если изменится базовый метод, все наследники могут сломаться.
```

✅ **Составление (хорошо):**
```php
// Интерфейс для каналов доставки
interface NotificationChannel {
    public function send(User $user, string $message): void;
}

class EmailChannel implements NotificationChannel {
    public function send(User $user, string $message): void {
        // Email логика
    }
}

class SmsChannel implements NotificationChannel {
    public function send(User $user, string $message): void {
        // SMS логика
    }
}

class TelegramChannel implements NotificationChannel {
    public function send(User $user, string $message): void {
        // Telegram логика
    }
}

// Основной класс принимает канал через конструктор
class NotificationService {
    public function __construct(
        private array $channels // Массив объектов NotificationChannel
    ) {}

    public function notify(User $user, string $message): void {
        foreach ($this->channels as $channel) {
            $channel->send($user, $message);
        }
    }
}

// Расширение: просто добавляем новый канал в массив при инициализации
// $service = new NotificationService([new EmailChannel(), new SmsChannel(), new TelegramChannel()]);
```
**Почему это OCP:** `NotificationService` закрыт для изменения (не нужно менять его код), но открыт для расширения (добавление нового класса, реализующего `NotificationChannel`).

---

### 2. Стратегический паттерн (Strategy Pattern)

Это частный случай Composition. Вы выносите изменяющийся алгоритм в отдельный объект-стратегию.

**Пример:** Расчет стоимости заказа с разными системами скидок.

❌ **Наследование:**
```php
class Order {
    public function calculateTotal(): float {
        // Базовая логика
    }
}

class VipOrder extends Order {
    public function calculateTotal(): float {
        // Логика VIP
    }
}
```

✅ **Стратегия:**
```php
interface PricingStrategy {
    public function calculate(Order $order): float;
}

class StandardPricingStrategy implements PricingStrategy {
    public function calculate(Order $order): float {
        return $order->getItemsTotal();
    }
}

class VipPricingStrategy implements PricingStrategy {
    public function calculate(Order $order): float {
        $total = $order->getItemsTotal();
        return $total * 0.9; // 10% скидка
    }
}

// Order не знает о стратегиях, он просто вызывает переданный объект
class Order {
    private PricingStrategy $strategy;
    
    public function setPricingStrategy(PricingStrategy $strategy): void {
        $this->strategy = $strategy;
    }
    
    public function calculateTotal(): float {
        return $this->strategy->calculate($this);
    }
}
```
**Почему это OCP:** Вы можете добавить `B2BPricingStrategy` без изменения класса `Order` и `StandardPricingStrategy`.

---

### 3. Плагины и события (Observer / Event-Driven)

Вы «открываете» код для расширения, позволяя внешнему коду подписаться на события и выполнить свою логику, не изменяя исходник.

**Пример:** Постобработка после сохранения заказа.

❌ **Наследование:**
```php
class OrderRepository {
    public function save(Order $order): void {
        // Сохранение
        // Логирование
        // Отправка email
        // Обновление статистики
        // ... если нужно добавить отправку в Slack, нужно изменить метод save()
    }
}
```

✅ **События (Event-Driven):**
```php
// Событие
class OrderSavedEvent {
    public function __construct(public Order $order) {}
}

// Основной код остается неизменным
class OrderRepository {
    private EventDispatcherInterface $dispatcher;
    
    public function save(Order $order): void {
        // Сохранение в БД
        $this->db->insert($order);
        
        // Бросаем событие
        $this->dispatcher->dispatch(new OrderSavedEvent($order));
    }
}

// Новые слушатели можно добавлять без изменения OrderRepository
class SendEmailListener implements EventSubscriberInterface {
    public function onOrderSaved(OrderSavedEvent $event): void {
        // Логика отправки email
    }
}

class UpdateStatsListener implements EventSubscriberInterface {
    public function onOrderSaved(OrderSavedEvent $event): void {
        // Логика обновления статистики
    }
}
```
**Почему это OCP:** Вы можете добавить сколько угодно новых слушателей (Slack, Kafka, Elasticsearch) без изменения кода `OrderRepository`.

---

### 4. Рефлексия и динамическая регистрация (Plugin Systems)

Для более сложных систем (CMS, фреймворки) используются системы плагинов, где модули регистрируют свои компоненты через конфиги или атрибуты.

**Пример:** Система хуков в WordPress или аддины в Symfony.

```php
// Атрибуты PHP 8.1+
#[Hook('order_before_save')]
class CustomOrderValidator {
    public function handle(Order $order): void {
        // Ваша логика
    }
}

// Ядро приложения сканирует кодовую базу или конфиги
// и вызывает все методы с атрибутом #[Hook('order_before_save')]
// без изменения кода ядра.
```

---

### 5. Дженерики (Generic) и Generics (в PHP 8.4+ / ранее через Union Types)

Хотя PHP не имеет полноценных дженериков, использование `template`-типов (в будущем) или Union Types позволяет создавать обобщенные структуры.

**Пример:** Общий обработчик, который работает с разными типами данных через Union Types.

```php
function processItem(string|int|float $item): string {
    return match(true) {
        is_string($item) => "String: " . $item,
        is_int($item) => "Int: " . $item,
        is_float($item) => "Float: " . $item,
    };
}
// Это не совсем OCP, но расширяемость через типы.
```

---

### Сравнение подходов

| Подход | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Composition (Составление)** | Когда нужно менять *алгоритм* поведения | Гибкость, простота тестирования | Нужно управлять жизненным циклом зависимостей |
| **Стратегия** | Когда есть *несколько вариантов* одного и того же поведения | Четкое разделение, легкое переключение | Может стать «коллекцией стратегий», если их много |
| **События (Events)** | Когда нужно *расширять* поведение без изменения основного потока | Полная изоляция, масштабируемость | Сложность отслеживания потока, ленивые баги, трудно тестировать |
| **Плагины/Хуки** | Для *расширяемых систем* (CMS, фреймворки) | Высокая гибкость, модульность | Сложность отладки, порядок выполнения хуков |

### Вывод

**Наследование — это не единственный способ реализовать OCP.** Более того, в большинстве случаев **Composition** и **Event-Driven Architecture** — более безопасные и гибкие инструменты для реализации OCP в PHP.

**Правило:** Используйте наследование, только когда вы уверены, что `is-a` связь (класс A *является* классом B) является семантически правильной и что базовый класс стабилен. Во всех остальных случаях — используйте интерфейсы и составление.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, **SRP (Single Responsibility Principle) применяется не только к классам**. Это фундаментальный архитектурный принцип, который касается любой единицы программного обеспечения, которая может иметь «ответственность».

### К чему еще применяется SRP?

#### 1. **Методы**
Это наиболее частое нарушение в реальном коде. Метод должен делать **одну вещь**.

❌ **Нарушение SRP на уровне метода:**
```php
public function processOrder(Order $order): Response
{
    // 1. Валидация
    if ($order->getTotal() < 0) {
        throw new InvalidArgumentException('Total must be positive');
    }

    // 2. Расчет скидки
    $discount = $this->calculateDiscount($order);

    // 3. Обновление БД
    $this->repo->save($order);

    // 4. Отправка email
    $this->mailer->send($order->getUser()->getEmail(), 'Order created');

    // 5. Формирование ответа
    return new Response('Success', 200);
}
```
**Проблема:** Если изменится формат email, логика скидки или структура ответа — метод `processOrder` должен быть изменен. У него **много причин для изменения**.

✅ **Решение:** Вынесите каждую «ответственность» в отдельный метод или класс (см. пример с `OrderService` выше).

#### 2. **Файлы**
В идеале один файл = один класс/интерфейс (PSR-4). Но иногда файл содержит:
- Класс
- Константы, относящиеся к нему
- Хелперы, используемые только этим классом

Если файл содержит 3 класса, каждый из которых решает свою проблему — это нормально. Если файл содержит 1 класс, который делает 5 разных вещей — это нарушение SRP.

#### 3. **Базы данных (Таблицы)**
Да, SRP применяется к **таблицам**! Каждая таблица должна хранить данные об **одном сущности**.

❌ **Нарушение SRP в БД:**
```sql
CREATE TABLE orders (
    id INT,
    user_id INT,
    total DECIMAL,
    created_at DATETIME,
    -- Ответственность: Хранение деталей заказа
    item1_name VARCHAR(255),
    item1_price DECIMAL,
    item2_name VARCHAR(255),
    item2_price DECIMAL,
    -- Ответственность: Хранение деталей товаров (должна быть отдельная таблица)
    shipping_address VARCHAR(255),
    billing_address VARCHAR(255)
    -- Ответственность: Хранение адресов (должна быть отдельная таблица)
);
```
**Проблема:** Если изменится структура товаров или адресов — нужно менять таблицу `orders`.

✅ **Решение:** Нормализация. Разделить на `orders`, `order_items`, `addresses`.

#### 4. **Модули/Библиотеки**
Каждая библиотека или модуль должна решать **одну задачу**.

❌ **Нарушение SRP в модуле:**
`utils.php` содержит:
- Функции для работы с датами
- Функции для шифрования
- Функции для работы с HTTP
- Функции для парсинга JSON

✅ **Решение:** Разделить на `DateTimeHelper`, `CryptoHelper`, `HttpClient`, `JsonParser`.

#### 5. **Конфигурационные файлы**
Каждый конфиг должен настраивать **одну подсистему**.

❌ **Нарушение SRP:**
`config.yml` содержит настройки БД, почты, кеша, логирования, API-ключей.

✅ **Решение:** Разделить на `database.yml`, `mail.yml`, `cache.yml`, `logging.yml`.

#### 6. **UI/Компоненты**
В фронтенде (Vue, React) SRP означает: **один компонент = один визуальный/логический блок**.

❌ **Нарушение SRP:**
`<UserDashboard />` компонент:
- Загружает данные пользователя
- Отрисовывает список заказов
- Отправляет уведомления
- Обрабатывает модальные окна
- Управляет темой оформления

✅ **Решение:** Разбить на `<UserHeader />`, `<OrderList />`, `<NotificationBell />`, `<ThemeToggle />`.

---

### Как определить, нарушен ли SRP?

Задайте вопрос: **«По скольким разным причинам может измениться этот элемент (класс/метод/таблица/файл)?»**

- **Если 1 причина** → SRP соблюдается.
- **Если >1 причины** → SRP нарушен.

**Примеры причин изменения:**
1. Изменился формат даты в логах.
2. Изменилась логика расчета скидки.
3. Изменился SMTP-сервер.
4. Изменилась структура ответа API.
5. Изменилась политика авторизации.

Если ваш метод/класс/таблица может измениться по всем этим 5 причинам — он нарушает SRP.

---

### Важно: SRP ≠ Один метод

Иногда один метод **должен** делать несколько вещей, если они тесно связаны и образуют **одну атомарную операцию**.

**Пример:**
```php
public function withdraw(float $amount): void
{
    // 1. Проверка баланса
    if ($this->balance < $amount) {
        throw new InsufficientFundsException();
    }
    
    // 2. Изменение баланса
    $this->balance -= $amount;
    
    // 3. Запись в журнал
    $this->log->add('Withdrawal', $amount);
}
```
Здесь 3 шага, но это **одна ответственность**: «Выполнить операцию снятия денег». Если вынести каждый шаг в отдельный метод, код станет менее читаемым, а логика — более хрупкой.

**Ключевой момент:** SRP — это не про количество строк или методов, а про **единицу изменения**. Если все 3 шага всегда изменяются вместе — это одна ответственность.

---

### Вывод

SRP — это **универсальный принцип** чистого кода. Он применим к:
- Классам
- Методам
- Файлам
- Таблицам БД
- Модулям
- UI-компонентам
- Конфигурациям

**Главный вопрос:** «Сколько причин для изменения у этого элемента?»
Если больше одной — ищите, где его можно разделить.

> ⏱ 17.6s · prompt 7937 tok · output 2585 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер зависимостей (Service Container) в Symfony — это центральный механизм управления жизненным циклом объектов. Он устроен так, чтобы быть быстрым в рантайме и гибким на этапе конфигурации.

Вот как это работает «под капотом»:

### 1. Этап сборки (Build Time): Компиляция контейнера

Когда вы запускаете команду `cache:clear` или деплоите приложение, Symfony **компилирует** конфигурацию сервисов в PHP-код. Это ключевое отличие от, например, Spring Boot (Java), где контейнер строится в рантайме.

**Что происходит при компиляции:**

1.  **Сборка конфигурации:**
    *   Symfony читает `config/services.yaml`, `config/packages/*.yaml`, `config/services_*.yaml`.
    *   Читает аннотации/атрибуты классов (`#[Service]`, `#[Autowired]`).
    *   Читает бандл-конфигурации.

2.  **Разрешение зависимостей (Dependency Resolution):**
    *   Контейнер анализирует граф зависимостей. Если сервис `A` зависит от `B`, а `B` от `C`, контейнер знает порядок инициализации.
    *   **Autowiring:** Если вы не указали конкретный аргумент, Symfony смотрит на тип аргумента (по имени или по типу) и пытается найти подходящий сервис.
    *   **Алиасы (Aliases):** Если есть несколько реализаций одного интерфейса (например, `MailerInterface`), контейнер определяет, какая является основной (default), а какие — альтернативными.

3.  **Оптимизация:**
    *   **Внутренние сервисы:** Если сервис используется только внутри контейнера (не выводится наружу), он может быть «внутренним» (internal).
    *   **Public vs Private:** Только `public` сервисы доступны через `Container::get()`. Приватные используются только внутри контейнера.
    *   **Синглтоны (Singletons) vs Прототипы (Prototypes):** По умолчанию все сервисы — синглтоны (один экземпляр на все запросы).

4.  **Генерация кода:**
    *   Symfony генерирует файл `var/cache/dev/services.php` (или prod).
    *   Это **чистый PHP-класс**, который наследуется от `Symfony\Component\DependencyInjection\Container`.
    *   В нем прописаны методы: `get_ServiceName()`, `make_ServiceName()`, `register_ServiceName()`.

**Пример сгенерированного кода (упрощенно):**

```php
class App_KernelDevDebugContainer extends Container
{
    protected function createService(string $serviceId): Service
    {
        switch ($serviceId) {
            case 'App\Service\OrderService':
                return $this->createOrderService();
            // ... другие сервисы
        }
    }

    protected function createOrderService(): OrderService
    {
        // Контейнер уже знает, какие зависимости нужны OrderService
        $orderService = new OrderService(
            $this->get_App_Repository_OrderRepository(),
            $this->get_App_Mailer_Mailer()
        );
        
        // Если есть tag'и, обработанные теги
        $orderService->addHandler($this->get_Handler1());
        $orderService->addHandler($this->get_Handler2());
        
        return $orderService;
    }
}
```

**Вывод:** В рантайме контейнер **не** ищет зависимости, не читает YAML, не анализирует типы. Он просто вызывает сгенерированные методы. Это очень быстро.

---

### 2. Autowiring (Автоматическое внедрение зависимостей)

**Что это:**
Autowiring — это механизм, который позволяет не писать `!service [App\Repository\OrderRepository]` в YAML. Вместо этого Symfony сам определяет, какой сервис передать в параметр, основываясь на **типе** (type hint) и **имени** аргумента.

**Как это работает:**

1.  **Типизированные параметры:**
    ```php
    class OrderService {
        public function __construct(
            private OrderRepository $orderRepo, // Тип: App\Repository\OrderRepository
            private MailerInterface $mailer,    // Тип: MailerInterface
        ) {}
    }
    ```

2.  **Матчинг:**
    *   Для `OrderRepository`: Контейнер ищет сервис с ID `App\Repository\OrderRepository`. Если находит — использует.
    *   Для `MailerInterface`: Контейнер ищет сервисы, которые реализуют этот интерфейс. Если есть один — использует. Если много — ищет тот, который помечен как `default` (или использует имя аргумента как подсказку).

3.  **Атрибуты:**
    *   `#[Autowired]` — явно указывает, что параметр должен быть внедрен автоматически.
    *   `#[Required]` — для необязательных зависимостей (nullable).
    *   `#[Tag('my_tag')]` — для группировки сервисов (см. ниже).

4.  **Имена аргументов (Fallback):**
    *   Если по типу не удалось найти уникальный сервис, Symfony пробует сопоставить **имя аргумента** с ID сервиса.
    *   Пример: `public function __construct(private $myCustomService) {}` — Symfony будет искать сервис с ID `myCustomService`.

**Ограничения Autowiring:**
*   Не работает для `string`, `int`, `bool` (если не задан `defaults: autowire: true` и есть сервис с подходящим именем).
*   Не работает для коллекций (`array`), если не заданы теги.
*   Не работает для `mixed` или union types (в редких случаях).

---

### 3. Момент первого запроса (Runtime)

Когда ваш контроллер или другой сервис запрашивает `OrderService` из контейнера:

1.  **Проверка кэша:**
    *   Контейнер проверяет внутренний массив `$this->services['App\Service\OrderService']`.
    *   Если сервис уже создан (синглтон) — возвращается существующий экземпляр.

2.  **Создание (если нет в кэше):**
    *   Вызывается метод `get_ServiceName()`, который был сгенерирован при компиляции.
    *   Этот метод вызывает `make_ServiceName()` или `create_ServiceName()`.
    *   Внутри этого метода:
        *   Запрашиваются зависимости у контейнера (рекурсивно, если они еще не созданы).
        *   Создается новый экземпляр класса (`new OrderService(...)`).
        *   Выполняются вызовы методов (например, `setContainer()` или установка тега-обработчиков).

3.  **Возврат объекта:**
    *   Экземпляр сохраняется в `$this->services` (если это синглтон).
    *   Возвращается вызывающему.

**Пример потока:**
```php
// В контроллере
class OrderController {
    public function __construct(
        private OrderService $orderService // <-- Здесь происходит извлечение из контейнера
    ) {}
    
    public function index() {
        $this->orderService->process(); // Вызов метода
    }
}
```

**Что происходит при `__construct`:**
1.  Symfony вызвал `container->get('App\Service\OrderService')`.
2.  Контейнер проверил кэш → нет.
3.  Контейнер вызвал сгенерированный метод `createOrderService()`.
4.  В этом методе были запрошены зависимости (`OrderRepository`, `Mailer`).
5.  Создан `new OrderService($repo, $mailer)`.
6.  Экземпляр сохранен в кэш контейнера.
7.  Объект передан в контроллер.

---

### 4. Важные нюансы

*   **Прототипы (Prototype Services):**
    Если сервис помечен как `shared: false`, каждый вызов `get()` создаст **новый** экземпляр. Это нужно для объектов, которые содержат состояние (например, `Request`, `Response` или фильтры).

*   **Теги (Tags):**
    Теги позволяют группировать сервисы. Например, все классы с тегом `kernel.event_listener` будут автоматически подписаны на события. При компиляции контейнер генерирует код, который итерирует по всем сервисам с этим тегом и вызывает у них метод регистрации.

*   **Service Locator vs Constructor Injection:**
    Symfony рекомендует **Constructor Injection** (зависимости передаются в конструктор). `Service Locator` (`$this->container->get('some_service')` внутри метода) — это антипаттерн, который скрывает зависимости. Autowiring работает только для конструктора, setter'ов и методов с `#[Required]`.

*   **Отладка:**
    *   `debug:container` — список всех сервисов.
    *   `debug:autowiring` — какие типы можно автоматически внедрить.
    *   `debug:autowiring App\Service\OrderService` — какие зависимости не удалось разрешить автоматически.

---

### Вывод

1.  **Компиляция:** Symfony превращает конфигурацию в PHP-код. Это быстро и предсказуемо.
2.  **Autowiring:** Работает на этапе компиляции, анализируя типы и имена. Не работает в рантайме.
3.  **Runtime:** Контейнер просто вызывает сгенерированные методы. Никакой «магии» в момент запроса.
4.  **Зависимости:** Внедряются через конструктор (рекомендуется), setter'ы или методы с `#[Required]`.

**Совет:** Всегда держите зависимости в конструкторе. Используйте `debug:autowiring` для отладки проблем с внедрением.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости (A зависит от B, B зависит от A) — это классическая архитектурная проблема, которая часто указывает на нарушение **SRP** или **OCP**.

В Symfony циклы разрешаются **не в рантайме**, а на этапе **компиляции контейнера** с помощью механизма **Service Decorators** и **Service References**. Но главное правило: **стремитесь их избежать**, а не просто «починить» контейнером.

---

### 1. Как Symfony технически разрешает циклы?

Если вы написали:

```php
// ServiceA зависит от ServiceB
class ServiceA {
    public function __construct(private ServiceB $b) {}
}

// ServiceB зависит от ServiceA
class ServiceB {
    public function __construct(private ServiceA $a) {}
}
```

Symfony **не** создаст два объекта и не свяжет их «в воздухе». Вместо этого он использует **Service Decorators** или **Lazy Service Proxies**.

#### Механизм: Lazy Service Proxy

1.  Контейнер создает **заглушку** (proxy) для `ServiceA`.
2.  `ServiceB` получает эту заглушку в конструктор.
3.  Когда в `ServiceB` впервые вызывается метод `$this->a->doSomething()`, прокси **динамически** требует настоящий `ServiceA` из контейнера.
4.  Контейнер находит, что `ServiceA` уже частично создан, и связывает объекты.

**Но это работает только для:**
- Синглтонов.
- Если ни один из объектов не использует свою зависимость в **конструкторе**.

**Пример, который работает:**
```php
class ServiceA {
    public function __construct(private ServiceB $b) {}
    
    public function doA() {
        // Зависимость используется ЛЕЗЬ в конструкторе
        return $this->b->doB();
    }
}

class ServiceB {
    public function __construct(private ServiceA $a) {}
    
    public function doB() {
        return 'B done';
    }
}
```

**Пример, который НЕ работает:**
```php
class ServiceA {
    public function __construct(private ServiceB $b) {
        // Ошибка! Попробуем вызвать $this->b->doB() ДО того, как ServiceA будет полностью создан
        $this->b->doB();
    }
}
```

---

### 2. Как правильно разруливать циклические зависимости?

Есть 4 основных подхода, от «быстрого решения» до «правильной архитектуры».

#### Подход 1: Извлечение общей ответственности (Рекомендуемый)

Чаще всего цикл возникает потому, что два сервиса делают **пересечение**. Вынесите общую логику в третий сервис.

**До (цикл):**
- `EmailService` шлет письма и зависит от `TemplateService`.
- `TemplateService` рендерит шаблоны и зависит от `EmailService` (чтобы отправить письмо о успешном рендере).

**После (без цикла):**
- `TemplateService` рендерит шаблоны. **Не знает** про email.
- `EmailService` шлет письма. Зависит от `TemplateService`.
- `NotificationService` (новый) координирует: вызывает `TemplateService`, затем `EmailService`.

**Результат:**
```
NotificationService -> TemplateService
NotificationService -> EmailService
```
Цикл разбит.

#### Подход 2: Использование событий (Event-Driven)

Один сервис не знает о другом. Он просто публикует событие. Другой сервис подписывается на него.

**До:**
- `OrderService` создает заказ и вызывает `PaymentService`.
- `PaymentService` платит и вызывает `OrderService` для обновления статуса.

**После:**
- `OrderService` создает заказ и публикует `OrderCreatedEvent`.
- `PaymentService` слушает `OrderCreatedEvent`, делает платеж и публикует `PaymentCompletedEvent`.
- `OrderService` слушает `PaymentCompletedEvent` и обновляет статус.

**Плюсы:** Полная декомпозиция, OCP, SRP.
**Минусы:** Сложнее отладка, асинхронность (если использовать message queue).

#### Подход 3: Service Locator (Мало рекомендуется)

Один из сервисов не получает зависимость в конструктор, а запрашивает ее из контейнера в момент необходимости.

```php
class ServiceB {
    private ServiceA $a;
    
    public function doB(): void {
        // Получаем ServiceA только когда нужно
        $a = $this->container->get(ServiceA::class);
        $a->doA();
    }
}
```

**Плюсы:** Быстро, просто.
**Минусы:** Скрытая зависимость, сложнее тестировать, нарушение DI.

#### Подход 4: Статический реестр / Service Locator Pattern

Создайте статический контейнер или реестр, где сервисы регистрируют себя.

```php
class ServiceRegistry {
    private static array $services = [];
    
    public static function register(string $id, object $service): void {
        self::$services[$id] = $service;
    }
    
    public static function get(string $id): object {
        return self::$services[$id];
    }
}
```

**Плюсы:** Гибкость.
**Минусы:** Глобальное состояние, сложность в тестах.

---

### 3. Практический пример: Разбиение `AuthService` и `UserRepository`

**Проблема:**
- `AuthService` зависит от `UserRepository` (чтобы найти пользователя).
- `UserRepository` зависит от `AuthService` (чтобы проверить, что пользователь активен перед сохранением).

**Решение:**

1. **Убрать проверку из репозитория.**
   Репозиторий — это доступ к данным. Он не должен знать о бизнес-правиле «активность».

2. **Проверку сделать в доменном объекте или сервисе.**
   ```php
   class User {
       public function isLocked(): bool {
           return $this->locked;
       }
   }
   ```

3. **`AuthService` проверяет:**
   ```php
   $user = $this->repo->find($email);
   if (!$user->isLocked()) {
       // логика входа
   }
   ```

**Итог:** `UserRepository` не знает про `AuthService`. Цикл исчез.

---

### 4. Чек-лист: Как избежать циклов?

1. **SRP:** Каждый сервис делает одну вещь. Если `ServiceA` и `ServiceB` пересекаются — есть `ServiceC`.
2. **OCP:** Расширяйте через интерфейсы и события, а не через изменения существующих классов.
3. **Зависимости направлены «вниз»:** Сервисы уровня приложения зависят от домена и инфраструктуры. Не наоборот.
4. **Избегайте бибаков (Bidirectional dependencies):** Если A -> B, B не должен знать о A.

---

### Вывод

Циклические зависимости — это **симптом**, а не болезнь.

- **Быстрое решение:** Использовать `Lazy Service Proxy` (Symfony делает это автоматически).
- **Правильное решение:** Перепроектировать сервисы, вынеся общую логику или используя события.
- **Главный вопрос:** «Почему эти два сервиса зависят друг от друга? Может, они должны быть одним сервисом или разделить ответственность?»

**Используйте цикл в Symfony только как временное решение для legacy-кода. В новом коде стремитесь к ациклическому графу зависимостей.**

> ⏱ 17.2s · prompt 16227 tok · output 2262 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Эта задача — классический пример **asynchronous processing** (асинхронной обработки). Главное требование: **не блокировать HTTP-ответ** (Fast Response), но гарантировать доставку (Reliability).

В Symfony есть 4 основных подхода, от простого к сложному. Я расставлю их по убыванию качества для production-систем.

---

### Вариант 1: Symfony Messenger (Рекомендуемый стандарт)

Это встроенный фреймворк Symfony для работы с сообщениями. Он абстрагирует транспорт (почта, очередь, БД) и позволяет легко переключаться между синхронной и асинхронной обработкой.

**Архитектура:**
1.  **Контроллер** отправляет `Message` в `MessageBus`.
2.  **MessageBus** помещает сообщение в транспорт (например, Doctrine DB или Redis).
3.  **Worker** (отдельный процесс) вытаскивает сообщение и исполняет `Handler`.

**Код:**

```php
// 1. Сообщение (Value Object)
final class SendWelcomeEmailMessage
{
    public function __construct(
        public string $email,
        public string $name
    ) {}
}

// 2. Обработчик (Business Logic)
final class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function __invoke(SendWelcomeEmailMessage $message): void
    {
        $template = (new Email())
            ->to($message->email)
            ->subject('Welcome!')
            ->html('Hello, ' . $message->name);
            
        $this->mailer->send($template);
    }
}
```

```php
// 3. Контроллер
class RegistrationController {
    public function register(Request $request, MessageBusInterface $bus): Response
    {
        // ... валидация, сохранение пользователя ...
        
        // Отправляем сообщения. Это БЫСТРО (только запись в транспорт)
        $bus->dispatch(new SendWelcomeEmailMessage($user->getEmail(), $user->getName()));
        $bus->dispatch(new UpdateSearchIndexMessage($user->getId()));
        
        return new Response('Registered!', 201);
    }
}
```

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%' # Например: doctrine://default
        routing:
            App\Message\SendWelcomeEmailMessage: async
            App\Message\UpdateSearchIndexMessage: async
```

**Trade-offs:**
*   ✅ **Преимущества:** Нативная поддержка Symfony, легко тестировать (можно dispatch в `SyncTransport` для тестов), встроенные retry-механизмы, поддержка очередей (Redis, RabbitMQ).
*   ❌ **Недостатки:** Нужен запущенный worker-процесс (`bin/console messenger:consume`). Если worker упадет — сообщения останутся в БД/очереди (это плюс для надежности, но минус для "real-time").

---

### Вариант 2: Cron Job + Фоновая задача

Простой подход: сохраняем "задачи" в БД, а Cron-скрипт их обрабатывает.

**Архитектура:**
1.  Контроллер сохраняет запись в таблицу `background_jobs` (type: `send_email`, payload: `{...}`).
2.  Cron запускает `bin/console app:process-jobs` каждую минуту.
3.  Скрипт берет задачи и исполняет их.

**Trade-offs:**
*   ✅ **Преимущества:** Очень просто, нет внешних зависимостей (Redis/RabbitMQ), видно все задачи в БД.
*   ❌ **Недостатки:** Задержка до 60 секунд (или сколько настроен Cron). Гонки (race conditions) при параллельном запуске (нужны row-locking). Сложно масштабировать.

---

### Вариант 3: JavaScript `setTimeout` / `requestAnimationFrame` (Frontend)

Иногда логичнее переложить часть работы на клиент, если это допустимо.

**Архитектурa:**
1.  Бэкенд возвращает ответ сразу.
2.  Frontend через `setTimeout` или `IntersectionObserver` отправляет отдельные запросы на `/api/email` и `/api/index`.

**Trade-offs:**
*   ✅ **Преимущества:** Бэкенд полностью разгружен. Если пользователь закроет вкладку — может не отправиться (если это допустимо).
*   ❌ **Недостатки:** Двойная нагрузка на API (два запроса вместо одного). Сложно гарантировать, что оба запроса дойдут. Не работает для headless-клиентов (curl, Postman).

---

### Вариант 4: `set_time_limit(0)` + `pcntl_fork` (Low-level)

PHP-специфичный "хакинг". Запускаем дочерний процесс.

**Код:**
```php
$pid = pcntl_fork();
if ($pid == -1) {
    // Ошибка
} elseif ($pid) {
    // Родительский процесс: продолжает выполнение, не ждем
    return new Response('OK');
} else {
    // Дочерний процесс: выполняет почту и индекс
    sendEmail();
    updateIndex();
    exit(0);
}
```

**Trade-offs:**
*   ✅ **Преимущества:** Нулевая задержка для клиента.
*   ❌ **Недостатки:** Очень сложно отлаживать. Если дочерний процесс упадет — никто не знает. Нет retry-механизма. Требует включения `pcntl`. Плохо работает с OpCache/PHP-FPM в некоторых конфигурациях.

---

### Сравнение и рекомендация

| Критерий | Messenger | Cron/DB | Frontend | pcntl_fork |
| :--- | :---: | :---: | :---: | :---: |
| **Надежность** | Высокая (retry, persistence) | Средняя | Низкая | Низкая |
| **Сложность** | Средняя | Низкая | Низкая | Высокая |
| **Задержка** | Секунды (зависит от worker) | Минуты | Секунды | Мгновенно |
| **Тестируемость** | Отличная | Хорошая | Сложная | Плохая |
| **Масштабируемость** | Высокая (добавить worker) | Низкая (1 инстанс) | Высокая | Средняя |

### Моя рекомендация

**Используйте `Symfony Messenger` с `DoctrineTransport`.**

**Почему:**
1.  **Надежность:** Сообщения сохраняются в БД до их обработки. Если сервер упадет — сообщения не потеряются.
2.  **Гибкость:** Можно легко переключиться на Redis/RabbitMQ, когда трафик вырастет, изменив одну строку в конфиге.
3.  **Тесты:** В unit-тестах можно использовать `SyncTransport`, чтобы обработать сообщения синхронно и проверить результат без запуска worker'ов.
4.  **Операционная зрелость:** Есть готовые инструменты для мониторинга, retry-политики, dead-letter queues.

**Как запустить:**
```bash
# В Docker/Production
bin/console messenger:consume async --limit=100
```
Или использовать Supervisor/Systemd для автозапуска.

**Дополнительно:**
- Для **индекса поиска** (Elasticsearch/Meilisearch) лучше использовать отдельный хендлер, чтобы не блокировать отправку email.
- Добавьте **logging** в хендлеры, чтобы видеть, что сообщения обработаны.
- Используйте **idempotency** (идемпотентность): если сообщение обработалось дважды, результат должен быть таким же.

> ⏱ 14.5s · prompt 20355 tok · output 1796 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony строится на двух независимых, но связанных друг с другом уровнях:

1. **Аутентифация (Authentication)** — «Кто ты?» (верификация личности).
2. **Авторизация (Authorization)** — «Что тебе можно делать?» (проверка прав).

Давайте разберем каждый компонент и их взаимодействие.

---

### 1. Аутентификация: «Кто ты?»

Symfony поддерживает несколько драйверов аутентификации (Security Providers), но суть одна: **Firewall** перехватывает запрос, извлекает credentials (токен, сессию, JWT) и пытается их верифицировать.

**Типичный поток:**
1. Клиент отправляет запрос с `Authorization: Bearer <token>` или с сессией.
2. **Firewall Listener** (`AuthenticationListener`) перехватывает запрос.
3. **Authenticator** (например, `JsonLoginAuthenticator` или `RefreshTokenAuthenticator`) проверяет креденшелы.
4. Если успешно — создается объект `User`, который хранится в `Security`-сервисе.
5. В запросе появляется `User`-объект, доступный через `$this->getUser()` или `Security::getUser()`.

**Ключевые концепции:**
- **User:** Должен реализовывать `UserInterface`.
- **Authenticator:** Класс, который знает, как проверить конкретный тип аутентификации (JWT, OAuth2, Session).
- **Firewall:** Группа listener'ов, которые обрабатывают запрос. Можно настроить разные firewalls для разных URL (например, `/api/*` — JWT, `/admin/*` — сессии).

---

### 2. Роли: Грубый уровень доступа

Роли — это **метки** (strings), которые назначаются пользователю.

```php
// User.php
public function getRoles(): array
{
    return ['ROLE_USER', 'ROLE_ADMIN'];
}
```

**Проверка ролей:**
- В контроллере: `#[IsGranted('ROLE_ADMIN')]`
- В шаблоне Twig: `{% if is_granted('ROLE_ADMIN') %}`
- В коде: `$this->security->isGranted('ROLE_ADMIN')`

**Когда роли достаточно:**
- Доступ к разделам приложения (`ROLE_ADMIN`, `ROLE_EDITOR`, `ROLE_VIEWER`).
- Простые сценарии: «Только админы могут видеть страницу настроек».
- Когда права не зависят от контекста (от конкретной сущности).

**Ограничения ролей:**
- Роли не знают о данных. Они отвечают только на вопрос «Кто этот пользователь?».
- Нельзя проверить: «Может ли этот админ редактировать *эту конкретную* статью, если она принадлежит *ему*?».

---

### 3. Voters: Тонкий уровень доступа

**Voter** — это стратегический паттерн для проверки прав с учетом контекста.

`Voter` получает на вход:
1. `User` (или `ROLE_ANONYMOUS`).
2. `attribute` (строка, например `EDIT`).
3. `subject` (объект, например `Post`).

И возвращает `true` или `false`.

**Пример Voter для блога:**

```php
// App/Security/PostVoter.php
class PostVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // Этот voter работает только с объектами Post
        if (!$subject instanceof Post) {
            return false;
        }

        return in_array($attribute, ['EDIT', 'DELETE', 'VIEW']);
    }

    protected function voteOnAttribute(string $attribute, $subject, User $user): bool
    {
        /** @var Post $subject */
        
        if ($user->isAnonymous()) {
            return false;
        }

        return match ($attribute) {
            'VIEW' => true, // Все могут видеть
            'EDIT' => $subject->getAuthor() === $user,
            'DELETE' => $subject->getAuthor() === $user || $user->hasRole('ROLE_ADMIN'),
            default => false,
        };
    }
}
```

**Использование:**
```php
// В контроллере
#[IsGranted('EDIT', new \App\Entity\Post(1))]
public function edit(): Response
{
    // ...
}

// В коде
if ($this->security->isGranted('EDIT', $post)) {
    // ...
}
```

---

### Когда использовать Roles, а когда Voters?

| Критерий | **Роли** | **Voters** |
| :--- | :---: | :---: |
| **Контекст** | Глобальный (пользователь) | Локальный (пользователь + объект) |
| **Сложность** | Простая проверка «есть ли роль?» | Сложная логика (сравнение полей, бизнес-правила) |
| **Пример** | «Только админы могут видеть страницу» | «Автор может редактировать свою статью» |
| **Масштабируемость** | Хорошо для маленьких систем | Хорошо для больших систем с множеством правил |
| **Производительность** | Очень быстро (проверка строки) | Чуть медленнее (вызов метода, логика) |

**Правило:**
1. **Начинайте с ролей.** Если проблема решается назначением `ROLE_ADMIN` или `ROLE_EDITOR` — не создавайте Voter.
2. **Используйте Voter, когда:**
   - Права зависят от конкретной сущности (пользователь может читать статью X, но не статью Y).
   - Есть сложные бизнес-правила (например, «менеджер может редактировать заказы только тех клиентов, за которых он отвечает»).
   - Нужно проверить права до того, как объект будет создан (например, «может ли пользователь создать проект в этой компании?»).

---

### Пример: Полный контроль доступа

Сценарий: «Пользователь может видеть свои заказы, а админ — все заказы».

1. **Роли:** `ROLE_USER`, `ROLE_ADMIN`.
2. **Voter:** `OrderVoter`.

```php
class OrderVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (!$subject instanceof Order) {
            return false;
        }
        return in_array($attribute, ['VIEW', 'EDIT']);
    }

    protected function voteOnAttribute(string $attribute, $subject, User $user): bool
    {
        /** @var Order $subject */
        
        if ($user->hasRole('ROLE_ADMIN')) {
            return true; // Админ может все
        }

        return match ($attribute) {
            'VIEW' => $subject->getUser() === $user,
            'EDIT' => $subject->getUser() === $user && $subject->getStatus() === OrderStatus::Pending,
            default => false,
        };
    }
}
```

**В контроллере:**
```php
class OrderController {
    public function list(): Response
    {
        // Возвращает все заказы (для админа) или только свои (для пользователя)
        // Логика фильтрации может быть в сервисе, но проверка прав — через Voter
        $orders = $this->orderService->getOrders($this->getUser());
        
        // Фильтруем через Voter (опционально, можно сделать в сервисе)
        $visibleOrders = array_filter($orders, fn($order) => $this->security->isGranted('VIEW', $order));
        
        return new JsonResponse($visibleOrders);
    }
    
    #[IsGranted('EDIT')]
    public function edit(Order $order): Response
    {
        // Если Voter вернет false — будет 403 Forbidden
        // ...
    }
}
```

---

### Вывод

- **Аутентификация** — это вход в систему.
- **Роли** — это простые метки для глобального доступа.
- **Voters** — это гибкий механизм для контекстно-зависимого доступа.

**Совет:** Не злоупотребляйте Voters. Если логика прав очень сложная и зависит от многих факторов — возможно, стоит вынести ее в отдельный сервис (`AccessControlService`), который будет вызывать Voters или использовать другие методы проверки.

> ⏱ 14.2s · prompt 22197 tok · output 1906 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Хранение Value Objects (VO) в Doctrine — одна из самых сложных и неоднозначных задач в PHP/DDD-стеке.

**Проблема:**
*   **Домен** требует: VO — это неизменяемый (immutable) объект с бизнес-логикой.
*   **Doctrine** требует: для маппинга объект должен иметь публичные свойства или getter'ы, а для обновления — setter'ы (или быть «грязным» dirty-checkable).
*   **Конфликт:** Как сделать объект неизменяемым, но при этом дать Doctrine доступ для чтения и записи?

Ниже — **все рабочие способы** с плюсами, минусами и подводными камнями.

---

### Способ 1: Custom Type (Рекомендуемый для простых VO)

Вы создаете кастомный тип в Doctrine, который сериализует VO в строку/число и десериализует обратно.

**Пример:**
```php
// App/ValueObject/Money.php
final class Money
{
    private function __construct(
        public int $amount,
        public string $currency
    ) {}
    
    public static function fromString(string $value): self
    {
        // Парсинг "1000.50USD"
        // Валидация
        return new self(...);
    }
    
    public function toString(): string
    {
        return $this->amount . $this->currency;
    }
}
```

```php
// App/Doctrine/MoneyType.php
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MoneyType extends Type
{
    public const NAME = 'money_vo';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }
        return Money::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        return $value->toString();
    }
}
```

```php
// App/Entity/Order.php
class Order
{
    #[Column(type: 'money_vo')]
    private Money $total;
    
    public function getTotal(): Money
    {
        return $this->total;
    }
    
    // Для Doctrine нужен setter (или использование hydrator)
    public function setTotal(Money $total): void
    {
        $this->total = $total;
    }
}
```

**Плюсы:**
*   ✅ Чистый домен: VO остается неизменяемым.
*   ✅ Простота: легко читать, легко отлаживать.
*   ✅ Поддерживается всеми версиями Doctrine.

**Минусы:**
*   ❌ Нужно вручную писать парсинг/сериализацию для каждого VO.
*   ❌ Хранение в БД — строка (медленнее, чем число).
*   ❌ Сложно делать запросы к полям VO (например, `WHERE amount > 100`).

**Подводные камни:**
*   **Null-проверки:** Не забудьте обработать `null` в `convertToPHPValue`.
*   **Индексы:** Строки сложнее индексировать, чем числа.

---

### Способ 2: Embedded Entity (Составные поля)

Если VO состоит из нескольких простых полей (int, string), вы можете маппить его как набор отдельных колонок.

**Пример:**
```php
// App/Entity/Order.php
class Order
{
    #[Column(type: 'integer')]
    private int $totalAmount;
    
    #[Column(type: 'string')]
    private string $totalCurrency;
    
    public function getTotal(): Money
    {
        return new Money($this->totalAmount, $this->totalCurrency);
    }
    
    public function setTotal(Money $money): void
    {
        $this->totalAmount = $money->amount;
        $this->totalCurrency = $money->currency;
    }
}
```

**Плюсы:**
*   ✅ Поля доступны для запросов (`WHERE totalAmount > 100`).
*   ✅ Можно индексировать отдельные поля.
*   ✅ Не нужен кастомный тип.

**Минусы:**
*   ❌ Нарушение инкапсуляции: VO «размазан» по сущности.
*   ❌ Сложно менять структуру VO (нужны миграции).
*   ❌ Домен не контролирует целостность полей (нельзя гарантировать, что `amount` и `currency` валидны, пока не собран объект).

**Подводные камни:**
*   **Dirty Check:** Если вы измените `totalAmount` напрямую, Doctrine не поймет, что изменился VO.
*   **Миграции:** Каждое изменение структуры VO — это миграция БД.

---

### Способ 3: Association Mapping (Отдельная сущность)

Храните VO как отдельную таблицу, связанную 1:1.

**Пример:**
```php
class Order
{
    #[OneToOne(inversedBy: 'order', cascade: ['persist'])]
    private Money $total;
}

class Money
{
    #[Column(type: 'integer')]
    private int $amount;
    
    #[Column(type: 'string')]
    private string $currency;
}
```

**Плюсы:**
*   ✅ VO становится полноценной сущностью.
*   ✅ Можно делать запросы к полям VO.
*   ✅ Легко добавить поведение (методы) в VO.

**Минусы:**
*   ❌ Лишние JOIN'ы при загрузке данных.
*   ❌ Сложнее управлять жизненным циклом (cascade, orphanRemoval).
*   ❌ VO становится «тяжелым» объектом.

**Подводные камни:**
*   **Performance:** Каждый доступ к `order->getTotal()` может вызвать lazy-loading.
*   **Cascades:** Если удалить `Order`, нужно удалить `Money`. Забудьте — и получите осиротевшие записи.

---

### Способ 4: JSON/Array Storage (Гибрид)

Храните VO как JSON-строку, но с кастомным типом, который знает, как его разбирать.

**Пример:**
```php
// App/Doctrine/MoneyType.php
final class MoneyType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) return null;
        
        $data = json_decode($value, true);
        return Money::fromArray($data);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;
        
        return json_encode($value->toArray());
    }
}
```

**Плюсы:**
*   ✅ Гибкость: можно хранить любой VO (даже вложенные).
*   ✅ Легко добавлять новые поля в VO без миграций (если не нужны запросы).
*   ✅ Сохраняется инкапсуляция.

**Минусы:**
*   ❌ Сложно делать запросы к внутренним полям VO.
*   ❌ JSON — это строка, сложнее индексировать.
*   ❌ Сложнее отлаживать (нужно читать JSON).

**Подводные камни:**
*   **Версионирование:** Если изменится структура VO — старые данные в БД могут не парситься. Нужны стратегии миграции данных.

---

### Способ 5: Hydrator + Reflection (Продвинутый)

Используете кастомный Hydrator, который собирает VO из отдельных полей сущности.

**Пример:**
```php
// В сущности Order:
#[Column(type: 'integer')]
private int $totalAmount;

#[Column(type: 'string')]
private string $totalCurrency;

// В Hydrator:
final class MoneyHydrator
{
    public function hydrate(Order $order): Money
    {
        return new Money(
            $order->totalAmount,
            $order->totalCurrency
        );
    }
}
```

**Плюсы:**
*   ✅ Максимальная гибкость.
*   ✅ Можно хранить в любом формате (отдельные поля, JSON, строка).
*   ✅ Домен не знает о деталях хранения.

**Минусы:**
*   ❌ Сложность: нужно писать кастомные Hydrator'ы.
*   ❌ Нарушение инкапсуляции: сущность знает о деталях VO.
*   ❌ Сложнее тестировать.

**Подводные камни:**
*   **Соответствие:** Hydrator должен точно соответствовать структуре VO. Если изменили VO — нужно менять Hydrator.
*   **Performance:** Каждый вызов Hydrator'а — это создание нового объекта.

---

### Сравнение способов

| Способ | Инкапсуляция | Запросы к полям | Сложность | Performance |
| :--- | :---: | :---: | :---: | :---: |
| **Custom Type (String)** | ✅ | ❌ | Низкая | Средняя |
| **Embedded Entity** | ❌ | ✅ | Низкая | Высокая |
| **Association** | ✅ | ✅ | Средняя | Низкая (JOIN) |
| **JSON Storage** | ✅ | ❌ | Средняя | Средняя |
| **Hydrator** | ✅ | ✅ | Высокая | Низкая |

---

### Рекомендации

1. **Для простых VO (Money, Email, DateTime):**
   - Используйте **Custom Type** (Способ 1).
   - Храните в строке или JSON.
   - Это самый простой и понятный способ.

2. **Для сложных VO с поведением:**
   - Используйте **JSON Storage** (Способ 4).
   - Это позволяет легко менять структуру VO без миграций.
   - Подходит для VO, которые редко используются в WHERE-запросах.

3. **Для VO, которые часто используются в запросах:**
   - Используйте **Embedded Entity** (Способ 2).
   - Или **Association** (Способ 3), если VO имеет сложную структуру.
   - Но помните: вы нарушаете инкапсуляцию.

4. **Избегайте:**
   - **Публичных свойств** в VO.
   - **Setter'ов** в VO (если не используете Hydrator).
   - **Слишком сложных кастомных типов** (они усложняют отладку).

---

### Вывод

**Нет идеального способа.** Выбор зависит от баланса между:
- **Инкапсуляцией** (домен должен контролировать VO).
- **Производительностью** (нужны ли запросы к полям VO?).
- **Сложностью** (легко ли читать и отлаживать код?).

**Мой выбор:**
- **По умолчанию:** Custom Type (String/JSON).
- **Если нужны запросы:** Embedded Entity.
- **Если VO сложный:** JSON Storage.

**Важно:** Всегда храните VO в **доменном слое**, а не в сущности. Сущность — это «мешок» для данных, домен — это «мозг».

> ⏱ 18.8s · prompt 24159 tok · output 2632 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Это один из самых болезненных и часто неправильно решаемых вопросов в DDD + Doctrine.

**Главная проблема:**
*   **DDD** говорит: Агрегат — это единая граница целостности. Транзакция должна охватывать изменения всего агрегата.
*   **Doctrine** говорит: `EntityManager` (EM) управляет состоянием всех объектов (Unit of Work). Если вы `flush`-нете в середине изменения агрегата — состояние может стать невалидным.

**Правильный подход:**
1.  **Агрегатный корень** — единственный объект, который знает о своих дочерних сущностях.
2.  **Транзакция** начинается до изменения агрегата и заканчивается после.
3.  **Flush** вызывается **один раз** в конце транзакции, на уровне **Application Service** (или Infrastructure), а **не** внутри доменных сущностей.

---

### Пример: Блог (Aggregate: `Article`)

**Структура домена:**
*   `Article` (Агрегатный корень)
    *   `Title` (Value Object)
    *   `Author` (ID, ссылка на другой агрегат)
    *   `Tags` (Коллекция `Tag` — дочерние сущности)

**Правила целостности:**
*   Статья не может иметь больше 5 тегов.
*   Теги не могут дублироваться.
*   Теги существуют только в контексте статьи.

---

### 1. Доменный слой (Чистый PHP, без Doctrine)

```php
// App/Domain/Article/Article.php
class Article
{
    private string $id;
    private Title $title;
    private string $authorId;
    private Collection $tags; // Доменная коллекция

    public function __construct(
        string $id,
        Title $title,
        string $authorId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->authorId = $authorId;
        $this->tags = new Collection();
    }

    public function addTag(Tag $tag): void
    {
        if ($this->tags->count() >= 5) {
            throw new DomainException('Too many tags');
        }
        
        if ($this->tags->has($tag->getValue())) {
            throw new DomainException('Tag already exists');
        }
        
        $this->tags->add($tag);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }
    
    // ... другие методы
}
```

```php
// App/Domain/Article/Tag.php
class Tag
{
    private string $value;
    
    public function __construct(string $value) {
        if (strlen($value) > 50) {
            throw new DomainException('Tag too long');
        }
        $this->value = $value;
    }
    
    public function getValue(): string { return $this->value; }
}
```

**Важно:** Доменные классы **не знают** о Doctrine. У них нет `@Entity`, `@Column`.

---

### 2. Инфраструктурный слой (Doctrine Entities)

Здесь мы маппим доменные объекты на таблицы.

```php
// App/Infrastructure/Persistence/Entity/ArticleEntity.php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ArticleEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;
    
    #[ORM\Column(type: 'string')]
    private string $title;
    
    #[ORM\Column(type: 'string')]
    private string $authorId;
    
    // ВАЖНО: Мы маппим дочерние сущности как связанные
    #[ORM\OneToMany(
        targetEntity: TagEntity::class,
        mappedBy: 'article',
        cascade: {'persist', 'remove'},
        orphanRemoval: true // Критично для агрегатов!
    )]
    private Collection $tags;

    // Конструктор для создания нового
    public function __construct(string $id, string $title, string $authorId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->authorId = $authorId;
        $this->tags = new Collection();
    }

    // Гидратор из БД
    public static function fromDatabase(array $data): self
    {
        $entity = new self($data['id'], $data['title'], $data['authorId']);
        $entity->setTagsFromDatabase($data['tags']);
        return $entity;
    }
    
    // Методы для работы с тегами (через Doctrine Collection)
    public function addTagEntity(TagEntity $tag): void
    {
        $tag->setArticle($this);
        $this->tags->add($tag);
    }
    
    public function removeTagEntity(TagEntity $tag): void
    {
        $this->tags->removeElement($tag);
    }
    
    // ... getters/setters
}
```

```php
// App/Infrastructure/Persistence/Entity/TagEntity.php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TagEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;
    
    #[ORM\Column(type: 'string')]
    private string $value;
    
    #[ORM\ManyToOne(inversedBy: 'tags')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', nullable: false)]
    private ?ArticleEntity $article = null;

    public function __construct(string $id, string $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
    
    // ... getters/setters
}
```

---

### 3. Маппинг: Домен ↔ Entity

Мы используем **Mapper** (или Hydrator), чтобы конвертировать доменные объекты в сущности и обратно.

```php
// App/Infrastructure/Persistence/Mapper/ArticleMapper.php
class ArticleMapper
{
    public function toEntity(Article $article): ArticleEntity
    {
        $entity = new ArticleEntity(
            $article->getId(),
            $article->getTitle()->getValue(),
            $article->getAuthorId()
        );
        
        // Преобразуем доменные теги в сущности
        foreach ($article->getTags() as $tag) {
            $entity->addTagEntity(new TagEntity(
                $tag->getValue(), // Используем значение как ID для простоты
                $tag->getValue()
            ));
        }
        
        return $entity;
    }
    
    public function toDomain(ArticleEntity $entity): Article
    {
        $tags = $entity->getTags()->map(function (TagEntity $tagEntity) {
            return new Tag($tagEntity->getValue());
        })->toArray();
        
        return new Article(
            $entity->getId(),
            new Title($entity->getTitle()),
            $entity->getAuthorId()
        );
    }
}
```

---

### 4. Application Service: Управление транзакцией и Flush

**Здесь happens магия.** Application Service координирует домен и инфраструктуру.

```php
// App/Application/Service/ArticleService.php
class ArticleService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleMapper $mapper
    ) {}

    public function addTagToArticle(string $articleId, Tag $tag): void
    {
        // 1. НАЧИНАЕМ ТРАНЗАКЦИЮ
        $this->em->transactional(function (EntityManagerInterface $em) use ($articleId, $tag) {
            
            // 2. ЗАГРУЖАЕМ АГРЕГАТ
            $articleEntity = $em->getRepository(ArticleEntity::class)->find($articleId);
            if (!$articleEntity) {
                throw new NotFoundException('Article not found');
            }
            
            // 3. ПРЕОБРАЗУЕМ В ДОМЕН
            $article = $this->mapper->toDomain($articleEntity);
            
            // 4. ПРимЕНЯЕМ БИЗНЕС-ЛОГИКУ (может бросить DomainException)
            $article->addTag($tag);
            
            // 5. ПЕРЕОБРАЩАЕМ В ENTITY (или обновляем существующую)
            // ВАЖНО: Мы НЕ создаем новую сущность, а обновляем существующую
            $updatedEntity = $this->mapper->toEntity($article);
            
            // Заменяем сущность в EM (или обновляем поля)
            $this->mergeEntity($articleEntity, $updatedEntity);
            
            // 6. FLUSH (один раз на транзакцию)
            $em->flush();
        });
        
        // 7. ТРАНЗАКЦИЯ ЗАКОНЧЕНА
        // Если был DomainException — rollback
    }
    
    private function mergeEntity(ArticleEntity $old, ArticleEntity $new): void
    {
        // Обновляем поля старой сущности данными новой
        $old->setTitle($new->getTitle());
        
        // Убираем старые теги, добавляем новые
        foreach ($old->getTags() as $oldTag) {
            if (!$new->getTags()->contains($oldTag)) {
                $old->removeTagEntity($oldTag);
            }
        }
        
        foreach ($new->getTags() as $newTag) {
            if (!$old->getTags()->contains($newTag)) {
                $old->addTagEntity($newTag);
            }
        }
    }
}
```

---

### Ключевые моменты:

1.  **Транзакция охватывает весь агрегат:**
    - `addTag` может изменить коллекцию тегов.
    - Все изменения (добавление, удаление тегов) происходят **в одной транзакции**.
    - Если `addTag` бросит `DomainException` — все изменения откатываются.

2.  **Flush вызывается один раз:**
    - В Application Service, **после** всех изменений агрегата.
    - **Никогда** внутри доменных сущностей.
    - **Никогда** внутри репозитория (если только это не `save`).

3.  **Агрегатный корень управляет дочерними:**
    - `Article` знает о `Tag`.
    - `Tag` не знает о `Article` (в домене).
    - В инфраструктуре `TagEntity` имеет ссылку на `ArticleEntity`.

4.  **OrphanRemoval = true:**
    - Критично для агрегатов!
    - Если из коллекции `Article` убирается `Tag`, Doctrine автоматически удаляет `TagEntity` из БД.
    - Без этого — осиротевшие записи.

5.  **Dirty Checking:**
    - Doctrine отслеживает изменения через `__sleep`/`__wakeup` или `UnitOfWork`.
    - Вы не должны вручную вызывать `persist` для дочерних сущностей, если они уже связаны с корнем.

---

### Подводные камни:

1.  **Lazy Loading:**
    - Если `Article` загружен без тегов (lazy), а вы пытаетесь изменить коллекцию — может быть `LazyLoadException`.
    - Решение: Используйте `Eager Loading` в репозитории или убедитесь, что коллекция уже загружена.

2.  **Collections vs Arrays:**
    - В домене используйте доменные коллекции (инкапсуляция).
    - В инфраструктуре используйте `Doctrine\Common\Collections\Collection`.
    - Не перепутайте их!

3.  **Идентичность:**
    - Дочерние сущности (`Tag`) должны иметь ID (пусть даже суррогатный), чтобы Doctrine мог их идентифицировать.
    - В домене `Tag` может быть Value Object без ID.

4.  **Границы транзакции:**
    - Если агрегат большой (статьи с 1000 комментариев), транзакция может стать долгой.
    - Решение: Разбейте на несколько агрегатов или используйте event-sourcing.

---

### Вывод

1.  **Домен** — чистый, без Doctrine.
2.  **Инфраструктура** — маппинг домена на БД.
3.  **Application Service** — управление транзакцией и flush.
4.  **Агрегатный корень** — единственный, кто управляет дочерними сущностями.
5.  **Flush** — один раз на транзакцию, в Application Service.

Это обеспечивает целостность данных и предсказуемость поведения.

> ⏱ 20.7s · prompt 26864 tok · output 2960 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Диагностика и решение проблемы N+1 в Doctrine — это стандартная задача оптимизации производительности.

### 1. Как диагностировать N+1?

**Способ 1: Визуализация SQL-запросов**

Самый надежный способ — увидеть сами запросы.

*   **Symfony Debug Bar:** Установите пакет `symfony/web-profiler-bundle`. В профилере откройте вкладку «Doctrine». Там виден список всех запросов. Если вы видите:
    *   1 запрос: `SELECT * FROM orders`
    *   100 запросов: `SELECT * FROM items WHERE order_id = ?` (по одному для каждого заказа)
    *   Это N+1.

*   **Laminas/Doctrine Logger:** Настройте логгер Doctrine, чтобы он писал все SQL-запросы в консоль или файл.

```php
// config/services.yaml
services:
    Doctrine\ORM\Tools\DebugBar\DoctrineToolbarInterceptor:
        class: Doctrine\ORM\Tools\DebugBar\DoctrineToolbarInterceptor
        public: true
        arguments:
            - '@doctrine.orm.default_manager'
            - '@twig'
```

**Способ 2: Анализ кода**

Типичные паттерны, вызывающие N+1:

```php
// Контроллер
$orders = $repo->findAll(); // 1 запрос

foreach ($orders as $order) {
    // ЛЕНИВОЕ ЗАГРУЗКА КОЛЛЕКЦИИ
    $items = $order->getItems(); // TRIGGERS LAZY LOADING
    // Для каждого $order выполняется новый запрос в БД!
    foreach ($items as $item) {
        // ...
    }
}
```

**Способ 3: Инструменты анализа**

*   `composer require doctrine/migrations --dev`
*   `bin/console orm:validate-schema`
*   `bin/console doctrine:cache:clear-query`

---

### 2. Способы решения и их Trade-offs

#### Способ 1: `JOIN FETCH` (Рекомендуемый для простых случаев)

**Суть:** Запрашиваете связанные данные в одном SQL-запросе через JOIN.

```php
// До (N+1)
$orders = $repo->findAll();

// После (1 запрос)
$qb = $em->createQueryBuilder()
    ->select('o', 'i')
    ->from(Order::class, 'o')
    ->join('o.items', 'i')
    ->getQuery();

$orders = $qb->getResult();
```

**Плюсы:**
*   ✅ Один запрос к БД.
*   ✅ Простота: легко читать.
*   ✅ Работает с `findOneBy`, `findAll` (через QueryBuilder).

**Минусы:**
*   ❌ **Cartesian Product:** Если у 100 заказов по 5 товаров — в памяти будет 500 строк. Это нормально, но может быть много.
*   ❌ **Глубина JOIN:** Можно джойнить до 3–4 уровней. Дальше — SQL-запрос становится сложным и медленным.
*   ❌ **Дубликаты объектов:** Если у одного заказа есть 2 товара, объект `Order` будет создан 2 раза в памяти (если не использовать `toOne`/`toMany`).

**Подводные камни:**
*   Не работает с `ManyToMany`, если не настроено правильно.
*   Не работает с `EAGER`-ассоциациями (они уже загружаются).

---

#### Способ 2: `DQL` с `JOIN` (Гибкость)

**Суть:** Как `JOIN FETCH`, но через DQL (Doctrine Query Language).

```php
$orders = $em->createQuery(
    'SELECT o, i FROM App\Entity\Order o JOIN o.items i'
)->getResult();
```

**Плюсы/Минусы:** Те же, что и у `JOIN FETCH`.

**Когда использовать:**
*   Когда нужно фильтровать по полям связанных сущностей:
    ```php
    $orders = $em->createQuery(
        'SELECT o, i FROM App\Entity\Order o JOIN o.items i WHERE i.price > 100'
    )->getResult();
    ```

---

#### Способ 3: `EAGER` Loading (Автоматическая загрузка)

**Суть:** Настраиваете ассоциацию как `EAGER` в маппинге.

```php
// Entity/Order.php
#[ORM\OneToMany(
    targetEntity: Item::class,
    mappedBy: 'order',
    fetch: 'EAGER'
)]
private $items;
```

**Плюсы:**
*   ✅ Не нужно писать JOIN в каждом запросе.
*   ✅ Всегда загружает связанные данные.

**Минусы:**
*   ❌ **Непредсказуемость:** Вы не знаете, где будет загрузка.
*   ❌ **Перегрузка:** Загружает данные, которые не нужны.
*   ❌ **Проблемы с `ManyToMany`:** Может вызвать `LazyLoadException` при изменении коллекции.

**Рекомендация:**
*   Используйте `EAGER` только для `ManyToMany` (если данные маленькие).
*   Для `OneToMany` — лучше `JOIN FETCH`.

---

#### Способ 4: Collection Initializer (Продвинутый)

**Суть:** Создайте отдельный сервис, который предзагружает коллекции.

```php
class OrderCollectionLoader
{
    public function __construct(private EntityManagerInterface $em) {}
    
    public function loadWithItems(array $orders): void
    {
        // Принудительно загружаем все коллекции одним запросом
        $this->em->createQuery(
            'SELECT i FROM App\Entity\Item i JOIN i.order o WHERE o IN (' . 
            implode(',', array_map(fn($o) => $o->getId(), $orders)) . 
            ')'
        )->getResult();
    }
}
```

**Плюсы:**
*   ✅ Точный контроль над загрузкой.
*   ✅ Можно выбрать, какие именно коллекции загружать.

**Минусы:**
*   ❌ Больше кода.
*   ❌ Сложнее поддерживать.

---

#### Способ 5: Materialized Views / Denormalization (БД-уровень)

**Суть:** Создаете материализованное представление в БД, которое объединяет данные.

```sql
CREATE MATERIALIZED VIEW order_with_items AS
SELECT o.*, i.* FROM orders o JOIN items i ON o.id = i.order_id;
```

**Плюсы:**
*   ✅ Максимальная производительность.
*   ✅ Один запрос.

**Минусы:**
*   ❌ Сложность: нужно обновлять представление при изменениях.
*   ❌ Нарушение нормализации.
*   ❌ Сложнее поддерживать.

**Когда использовать:**
*   Для очень больших таблиц.
*   Для аналитических запросов.

---

#### Способ 6: `Batch` Loading (Для больших коллекций)

**Суть:** Загружаете связанные данные пачками.

```php
use Doctrine\ORM\Tools\Pagination\Paginator;

// 1. Получаем ID
$ids = $em->createQuery(
    'SELECT o.id FROM App\Entity\Order o'
)->getResult();

// 2. Загружаем пачками по 100
foreach (array_chunk($ids, 100) as $chunk) {
    $orders = $em->createQuery(
        'SELECT o, i FROM App\Entity\Order o LEFT JOIN o.items i WHERE o.id IN (' . 
        implode(',', $chunk) . 
        ')'
    )->getResult();
}
```

**Плюсы:**
*   ✅ Управление размером результата.
*   ✅ Подходит для больших наборов данных.

**Минусы:**
*   ❌ Несколько запросов (но их немного).
*   ❌ Сложнее код.

---

### Сравнение способов

| Способ | Производительность | Сложность | Гибкость | Когда использовать |
| :--- | :---: | :---: | :---: | :--- |
| **JOIN FETCH** | ✅✅✅ | Низкая | Средняя | Простые сценарии, 1–2 уровня вложенности |
| **DQL JOIN** | ✅✅✅ | Низкая | Высокая | Когда нужны фильтры по связанным полям |
| **EAGER** | ✅✅ | Очень низкая | Низкая | `ManyToMany` с небольшими коллекциями |
| **Collection Loader** | ✅✅ | Средняя | Высокая | Сложные сценарии, точный контроль |
| **Materialized View** | ✅✅✅ | Высокая | Низкая | Большие таблицы, аналитика |
| **Batch Loading** | ✅✅ | Средняя | Высокая | Большие наборы данных |

---

### Рекомендации

1.  **Начните с `JOIN FETCH`.** Это решает 80% проблем.
2.  **Ограничьте глубину JOIN** (не более 2–3 уровней).
3.  **Используйте `DQL`** для сложных фильтров.
4.  **Для больших данных** — `Batch Loading`.
5.  **Для аналитики** — `Materialized Views`.

**Важно:** Всегда измеряйте производительность. Не оптимизируйте то, что не является узким местом. Используйте Profiler для подтверждения того, что N+1 действительно является проблемой.

> ⏱ 17.0s · prompt 29881 tok · output 2150 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это классический вопрос в DDD: «Если домен валидирует, зачем БД?»

**Краткий ответ:** Валидация в коде — это **бизнес-правила** («Почему?»), ограничения в БД — это **инварианты целостности** («Что нельзя?»). Они не дублируют друг друга, а дополняют.

---

### Зачем нужны ограничения в БД, если есть код?

#### 1. Защита от ошибок кода (Fail-Fast на уровне хранения)
Даже самый аккуратный код может иметь баги:
- Забыли проверить `null` перед вставкой.
- Гонка (race condition): два процесса одновременно пытаются обновить запись.
- Миграция данных, которая пропускает валидацию.
- Ручной `INSERT` из `psql` или миграция.

**БД — это последний рубеж защиты.** Если инвариант критичен для целостности данных, он **должен** быть в БД.

#### 2. Performance (Индексы и ограничения)
- **`UNIQUE`** создает индекс. Это ускоряет `SELECT` по этому полю.
- **`NOT NULL`** позволяет оптимизатору БД избегать `NULL`-значений в индексах.
- **`CHECK`** ограничения могут использоваться оптимизатором для оптимизации запросов (например, если `status IN ('active', 'archived')` — БД знает, что других значений нет).

#### 3. Семантика данных
- **Типы данных:** `UUID` vs `VARCHAR(36)`. `UUID` в БД гарантирует формат и экономит место.
- **`ENUM` vs `VARCHAR`:** `ENUM` в PostgreSQL ограничивает значения на уровне БД, но сложен для миграций. `CHECK` constraint — гибче.
- **`JSONB`:** Можно проверить структуру JSON с помощью `CHECK (jsonb_typeof(data) = 'object')`.

#### 4. Транзакционная целостность
- **Foreign Keys** гарантируют, что вы не удалите запись, на которую ссылаются другие.
- **`ON DELETE CASCADE`** автоматизирует каскадное удаление.

---

### Какие средства используем в PostgreSQL?

#### 1. `NOT NULL`
**Когда:** Поле обязательно для заполнения.
**Пример:**
```sql
CREATE TABLE orders (
    id UUID PRIMARY KEY,
    user_id UUID NOT NULL,
    total DECIMAL NOT NULL CHECK (total >= 0)
);
```

#### 2. `UNIQUE`
**Когда:** Значение должно быть уникальным в пределах таблицы (или набора таблиц).
**Пример:**
```sql
ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);
-- Или для композиции:
ALTER TABLE order_items ADD CONSTRAINT unique_order_product UNIQUE (order_id, product_id);
```

#### 3. `CHECK` Constraints
**Когда:** Бизнес-правило, которое можно выразить как условие.
**Примеры:**
```sql
-- Положительное число
ALTER TABLE products ADD CONSTRAINT check_price_positive CHECK (price > 0);

-- Значение из набора
ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'paid', 'shipped', 'cancelled'));

-- JSONB валидация
ALTER TABLE events ADD CONSTRAINT check_payload_is_object CHECK (jsonb_typeof(payload) = 'object');

-- Дата в будущем
ALTER TABLE appointments ADD CONSTRAINT check_future CHECK (appointment_date > NOW());
```

#### 4. `EXCLUDE` Constraints (Продвинутый уровень)
**Когда:** Нельзя иметь пересекающиеся диапазоны.
**Пример:**
```sql
-- Нельзя иметь пересекающиеся интервалы времени для одного пользователя
CREATE TABLE appointments (
    user_id UUID,
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    EXCLUDE USING gist (
        user_id WITH =,
        tsrange(start_time, end_time) WITH &&
    )
);
```

#### 5. `PARTITION BY` (Разбиение таблиц)
**Когда:** Таблица большая, нужно ускорить запросы и управлять данными.
**Пример:**
```sql
CREATE TABLE events (
    id UUID,
    created_at TIMESTAMP
) PARTITION BY RANGE (created_at);

CREATE TABLE events_2023 PARTITION OF events
    FOR VALUES FROM ('2023-01-01') TO ('2024-01-01');
```

#### 6. `TRIGGERS` (Триггеры)
**Когда:** Сложная логика, которая не выражается через `CHECK`.
**Пример:**
```sql
-- Автоматически обновляет modified_at
CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.modified_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_modified_at
    BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION update_modified_column();
```

**Внимание:** Триггеры — это «темный код». Их трудно отлаживать и тестировать. Используйте только когда другие средства не подходят.

---

### Что валидировать в коде, а что в БД?

| Тип правила | Где валидировать | Почему |
| :--- | :--- | :--- |
| **Формат** (email, телефон) | **Код** (домен) | Улучшенные сообщения об ошибках, логирование |
| **Длина строки** | **Код + БД** (`VARCHAR(n)`) | БД — защита, код — UX |
| **Уникальность** | **БД** (`UNIQUE`) | Атомарность, защита от гонок |
| **Foreign Key** | **БД** (`REFERENCES`) | Целостность данных |
| **Бизнес-логика** (скидки, лимиты) | **Код** (домен) | Сложная логика, легко меняется |
| **Состояние (state machine)** | **Код + БД** (`CHECK`) | БД — защита, код — понятность |
| **Ссылочная целостность** | **БД** (`FK`, `CASCADE`) | Автоматизация, атомарность |

---

### Антипаттерны

#### 1. «БД знает всё»
```sql
-- Плохо: БД знает о бизнес-правилах, которые могут измениться
CHECK (total = (SELECT SUM(price) FROM items WHERE order_id = id))
```
**Решение:** Такие правила — в коде. БД хранит только результат (`total`).

#### 2. «Код знает всё, БД ничего»
```php
// Опасно: Если код упал — данные могут быть неконсистентны
if ($this->isUnique($email)) {
    $this->repo->save($user);
}
```
**Решение:** `UNIQUE` constraint в БД. Код ловит `UniqueConstraintViolationException`.

#### 3. «Триггеры для простой логики»
```sql
-- Плохо: Можно было бы CHECK
CREATE TRIGGER ... IF NEW.total < 0 THEN RAISE EXCEPTION;
```
**Решение:** Используйте `CHECK (total >= 0)`.

---

### Вывод

1. **Валидация в коде** — это **UX** и **бизнес-логика**.
2. **Ограничения в БД** — это **целостность** и **защита**.
3. **Используйте оба уровня:**
   - Код дает понятные ошибки и гибкость.
   - БД гарантирует корректность данных даже при багах кода.
4. **Приоритет:**
   - `UNIQUE`, `NOT NULL`, `FK` — **всегда** в БД.
   - `CHECK` — для простых инвариантов.
   - `TRIGGERS` — только когда другие средства не работают.

**Правило:** Если инвариант нарушен — данные «грязные», и их сложно восстановить. Такие инварианты **должны** быть в БД.

> ⏱ 15.2s · prompt 32081 tok · output 1844 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленного запроса в PostgreSQL — это инженерный процесс, а не «угадайка». Я подхожу к этому системно: сначала понимаю, **где** узкое место, затем **почему** оно возникает, и только потом **как** его устранять.

Вот мой пошаговый процесс:

### 1. Диагностика: Понимаем, что именно медленно

#### Шаг 1: Включаем `EXPLAIN ANALYZE`
Это основной инструмент. Он показывает план выполнения запроса и время на каждый шаг.

```sql
EXPLAIN ANALYZE
SELECT * FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.created_at > '2023-01-01'
  AND o.status = 'pending';
```

**Что смотрим в выводе:**
- **`Seq Scan`**: Полное сканирование таблицы. Обычно признак отсутствия индекса или того, что индекса недостаточно.
- **`Index Scan` / `Index Only Scan`**: Использование индекса. Хорошо.
- **`Nested Loop` / `Hash Join` / `Merge Join`**: Способ соединения. `Nested Loop` может быть быстрым для маленьких наборов, но медленным для больших.
- **`Buffers: shared hit/miss`**: Сколько страниц кэша БД было загружено с диска (`miss`), а сколько уже было в памяти (`hit`). Много `miss` — проблема с дисковой подсистемой или кэшем.
- **`Actual Rows`**: Сколько строк реально вернулось. Если `Actual Rows` >> `Rows` (оценка оптимизатора) — статистика устарела.
- **`Rows Removed by Filter`**: Сколько строк отфильтровалось после сканирования. Много удаленных строк — индекса недостаточно.

#### Шаг 2: Проверяем статистик
Если оптимизатор принимает неверные решения, проблема в статистике.

```sql
-- Смотрим, устарела ли статистика
SELECT 
    relname, 
    n_live_tup, 
    n_dead_tup, 
    last_vacuum, 
    last_autovacuum
FROM pg_stat_user_tables
WHERE relname = 'orders';

-- Обновляем статистику
ANALYZE orders;
```

#### Шаг 3: Ищем блокировки
Запрос может быть не медленным, а **блокированным**.

```sql
-- Смотрим активные сессии и блокировки
SELECT 
    blocked_locks.pid AS blocked_pid,
    blocked_activity.usename AS blocked_user,
    blocking_locks.pid AS blocking_pid,
    blocking_activity.usename AS blocking_user,
    blocked_activity.query AS blocked_query,
    blocking_activity.query AS blocking_query
FROM pg_catalog.pg_locks blocked_locks
JOIN pg_catalog.pg_stat_activity blocked_activity ON blocked_activity.pid = blocked_locks.pid
JOIN pg_catalog.pg_locks blocking_locks ON blocking_locks.transactionid = blocked_locks.transactionid
JOIN pg_catalog.pg_stat_activity blocking_activity ON blocking_activity.pid = blocking_locks.pid
WHERE NOT blocked_locks.granted;
```

#### Шаг 4: Проверяем ресурсы
Использую `pg_stat_activity`, `top`, `iostat` (Linux) или инструменты мониторинга (Prometheus/Grafana), чтобы понять:
- **CPU**: Занят ли процессор?
- **Disk I/O**: Много ли读写 операций?
- **Memory**: Не хватает ли памяти для кэша (`shared_buffers`)?

---

### 2. Типовые причины медленных запросов

#### Причина 1: Отсутствие индексов или неполные индексы
**Симптом:** `Seq Scan` на больших таблицах.
**Решение:**
- Создать индекс по часто используемым в `WHERE`/`JOIN`/`ORDER BY` полям.
- Использовать **покрывающий индекс** (Covering Index) с `INCLUDE`, чтобы избежать возврата к таблице.

```sql
-- Пример: Покрытый индекс для запроса, который выбирает только несколько полей
CREATE INDEX idx_orders_status_created 
ON orders (status, created_at) 
INCLUDE (id, user_id);
```

#### Причина 2: Неправильный тип соединения (Join Strategy)
**Симптом:** `Nested Loop` на больших таблицах.
**Решение:**
- Проверить, может ли `Hash Join` быть быстрее.
- Иногда нужно принудительно указать метод соединения с помощью `SET enable_nestloop = off;` (но это временное решение).
- Часто помогает создание правильного индекса, чтобы оптимизатор выбрал `Nested Loop` для маленьких наборов, а `Hash Join` для больших.

#### Причина 3: Устаревшая статистика
**Симптом:** Оптимизатор выбирает неверный план (например, `Hash Join` вместо `Nested Loop`).
**Решение:**
- `ANALYZE table_name;`
- Настроить автоанализ в `postgresql.conf`:
  ```conf
  autovacuum = on
  autovacuum_analyze_threshold = 50
  autovacuum_analyze_scale_factor = 0.05
  ```

#### Причина 4: Слишком много строк (Row Explosion)
**Симптом:** `Nested Loop` генерирует миллионы строк.
**Решение:**
- Уменьшить результат левой таблицы до соединения (фильтровать до `JOIN`).
- Использовать `EXISTS` вместо `IN`, если можно.
- Переписать запрос, чтобы сначала агрегировать, а потом соединять.

#### Причина 5: Проблемы с кэшем
**Симптом:** Много `Buffers: miss`.
**Решение:**
- Увеличить `shared_buffers` (обычно 25% от RAM).
- Проверить, не вытесняется ли кэш другими запросами.
- Оптимизировать размер страниц.

#### Причина 6: Блокировки и транзакции
**Симптом:** Запрос висит, хотя план хороший.
**Решение:**
- Найти блокирующую транзакцию (см. Шаг 3).
- Сократить время транзакций.
- Использовать более низкий уровень изоляции (например, `READ COMMITTED` вместо `SERIALIZABLE`), если бизнес-логика позволяет.

#### Причина 7: Неправильная структура запроса
**Симптом:** Запрос делает то, что не нужно.
**Решение:**
- Убрать лишние `JOIN`'ы.
- Использовать `LIMIT` и `OFFSET` (или `Keyset Pagination`).
- Избегать подзапросов в `SELECT`, если можно использовать `JOIN`.
- Разбить сложный запрос на несколько простых.

---

### 3. Инструменты для диагностики

| Инструмент | Назначение |
| :--- | :--- |
| **`EXPLAIN ANALYZE`** | Основной инструмент для анализа плана запроса. |
| **`pg_stat_statements`** | Расширение для мониторинга самых медленных запросов за все время. |
| **`pg_stat_activity`** | Мониторинг активных сессий и блокировок. |
| **`pg_stat_user_tables`** | Статистика по таблицам (vacuum, autovacuum, mtime). |
| **`pgBadger`** | Анализ логов PostgreSQL (логирование с `log_min_duration_statement = 100ms`). |
| **`Prometheus + Grafana`** | Визуализация метрик БД (I/O, CPU, Memory, Connections). |
| **`pgAdmin`** | GUI для управления БД и просмотра запросов. |
| **`auto_explain`** (расширение) | Автоматически логирует `EXPLAIN` для запросов дольше X ms. |

---

### 4. Чек-лист оптимизации

1. **Проверьте `EXPLAIN ANALYZE`.**
2. **Создайте недостающие индексы.**
3. **Обновите статистику (`ANALYZE`).**
4. **Проверьте блокировки.**
5. **Переоптимизируйте запрос** (убрать лишние JOIN, фильтры, агрегации).
6. **Проверьте конфигурацию PostgreSQL** (`shared_buffers`, `work_mem`, `effective_cache_size`).
7. **Рассмотрите нормализацию/денормализацию** (материализованные представления).
8. **Разбейте запрос** на несколько простых.

---

### Пример: Оптимизация запроса

**До (медленно):**
```sql
SELECT o.id, o.total, u.email
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.created_at > '2023-01-01'
  AND o.status = 'pending';
```
**Проблема:** `Seq Scan` на `orders`, если нет индекса по `status` и `created_at`.

**После (быстро):**
```sql
-- 1. Создаем индекс
CREATE INDEX idx_orders_status_created ON orders (status, created_at);

-- 2. Обновляем статистику
ANALYZE orders;

-- 3. Запрос теперь использует Index Scan
SELECT o.id, o.total, u.email
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.created_at > '2023-01-01'
  AND o.status = 'pending';
```

**Вывод:** Оптимизация — это итеративный процесс. Всегда начинайте с `EXPLAIN ANALYZE`, ищите узкое место, применяйте минимальные изменения и переизмеряйте.

> ⏱ 17.1s · prompt 33971 tok · output 2194 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат** — это фундаментальная единица целостности данных в DDD (Domain-Driven Design). Это группа связанных объектов, которые рассматриваются как единое целое с точки зрения изменений состояния.

**Ключевые свойства агрегата:**
1. **Единая граница целостности:** Правила бизнес-логики гарантируют, что состояние внутри агрегата всегда валидно.
2. **Один корень:** Только один объект (корень агрегата) может быть изменен извне. Все внутренние сущности изменяются только через корень.
3. **Идентификация:** Агрегат имеет уникальный идентификатор (ID), который используется для ссылания на него из других агрегатов.
4. **Транзакционная граница:** Изменения одного агрегата должны происходить в одной транзакции.

---

### Как определяются границы агрегата?

Границы агрегата определяются **не техническими** соображениями (таблицы БД, микросервисы), а **бизнес-потребностями**:

1. **Какие правила целостности должны выполняться атомарно?**
   - Если нарушение этих правил приводит к невалидному состоянию бизнеса — эти объекты должны быть в одном агрегате.

2. **Какие объекты часто изменяются вместе?**
   - Если два объекта почти всегда меняются в одной транзакции — они должны быть в одном агрегате.

3. **Какие объекты имеют прямую зависимость (composition)?**
   - Если объект A не существует без объекта B — они должны быть в одном агрегате.

4. **Масштабируемость:**
   - Агрегат должен быть **маленьким**. Чем больше агрегат, тем сложнее保证其 целостность и тем хуже масштабируемость.

---

### Пример: Банковский счет (Bank Account)

**Доменная модель:**

```
Account (Агрегатный корень)
├── Balance (Value Object)
├── Owner (Reference to Customer Aggregate)
└── Transactions (Collection of Transaction entities)
```

**Правила целостности (в рамках одного агрегата):**
1. Баланс не может быть отрицательным (если счет не овердрафт).
2. Каждая транзакция должна быть привязана к сущему счету.
3. История транзакций должна быть последовательной.

**Почему это один агрегат:**
- Если мы изменили баланс, мы **должны** добавить транзакцию в историю. Это атомарная операция.
- Если мы удалили транзакцию, мы **должны** изменить баланс.
- Эти правила нельзя нарушать, если объекты в одном агрегате.

**Код:**

```php
final class Account
{
    private AccountId $id;
    private Balance $balance;
    private Collection $transactions;

    public function __construct(AccountId $id, Balance $initialBalance)
    {
        $this->id = $id;
        $this->balance = $initialBalance;
        $this->transactions = new Collection();
    }

    public function deposit(Money $amount): void
    {
        if ($amount->isNegative()) {
            throw new DomainException('Deposit amount must be positive');
        }

        $newBalance = $this->balance->add($amount);
        $this->balance = $newBalance;
        $this->transactions->add(new Transaction(TransactionType::Deposit, $amount));
    }

    public function withdraw(Money $amount): void
    {
        if ($this->balance->lessThan($amount)) {
            throw new DomainException('Insufficient funds');
        }

        $newBalance = $this->balance->subtract($amount);
        $this->balance = $newBalance;
        $this->transactions->add(new Transaction(TransactionType::Withdrawal, $amount));
    }

    // ... getters
}
```

**Ключевой момент:** Методы `deposit` и `withdraw` изменяют **и** баланс, **и** историю транзакций **в рамках одного объекта**. Это гарантирует целостность.

---

### Типовая ошибка: Слишком большой агрегат

**Сценарий:** Кто-то решает, что `Account` должен включать в себя `Customer`, `Address`, `PaymentMethods`, `TaxInfo`, `MarketingPreferences`.

**Почему это ошибка:**

1. **Масштабируемость:**
   - Если 1000 пользователей одновременно вносят деньги, все 1000 транзакций будут блокировать один и тот же большой объект `Account`.
   - Конкурентность падает.

2. **Сложность:**
   - Изменение данных `MarketingPreferences` требует загрузки всей модели `Account` с `Transactions`.
   - Непонятно, что именно изменилось.

3. **Нарушение SRP:**
   - `Account` теперь отвечает за финансовую логику, персональные данные, маркетинг и налоги.

**Правильное решение:**

Разделить на несколько агрегатов:

1. **`Account` Aggregate:**
   - `Balance`
   - `Transactions`
   - `Owner` (только ID, ссылка на `Customer`)

2. **`Customer` Aggregate:**
   - `Name`
   - `Email`
   - `Address`
   - `MarketingPreferences`

3. **`PaymentMethod` Aggregate:**
   - `CardNumber`
   - `ExpiryDate`

**Как они связаны:**
- `Account` ссылается на `Customer` по `CustomerId`.
- `Account` ссылается на `PaymentMethod` по `PaymentMethodId`.
- Ссылки — это только ID, а не объекты. Это обеспечивает **разделение**.

**Преимущества:**
- Можно обновить `MarketingPreferences` без загрузки `Transactions`.
- Можно изменить `Balance` без обновления `Customer`.
- Каждый агрегат можно масштабировать независимо.

---

### Как выбирать границы: Чек-лист

1. **Найдите бизнес-правила, которые должны выполняться атомарно.**
   - Пример: «Баланс и история транзакций должны быть согласованы».
   - Вывод: `Balance` и `Transactions` в одном агрегате.

2. **Найдите объекты, которые не имеют смысла без другого.**
   - Пример: `Transaction` не существует без `Account`.
   - Вывод: `Transaction` — часть агрегата `Account`.

3. **Найдите объекты, которые часто изменяются вместе.**
   - Пример: `Account` и `Customer` изменяются раздельно.
   - Вывод: Разные агрегаты.

4. **Проверьте на конкурентность.**
   - Если агрегат большой, а изменения частые — разделите.

5. **Используйте правило «минимального размера».**
   - Агрегат должен быть настолько маленьким, насколько позволяет бизнес-логика.

---

### Вывод

Агрегат — это **компромисс** между целостностью данных и масштабируемостью.

- **Слишком маленький агрегат:** Сложно гарантировать целостность, много транзакций.
- **Слишком большой агрегат:** Низкая масштабируемость, сложная модель.

**Золотое правило:** Агрегат должен быть **наиболее узким**, который все еще может гарантировать бизнес-правила целостности.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это классическая проблема **конкурентности (Concurrency)** и **конфликтов состояний (State Conflicts)**. Если два процесса одновременно пытаются изменить один и тот же агрегат, без правильного проектирования возникнет **утеря обновления (Lost Update)** или нарушение бизнес-правил.

### Что произойдёт без защиты?

1. **Lost Update (Утрата обновления):**
   - Процесс A читает `Balance = 100`.
   - Процесс B читает `Balance = 100`.
   - Процесс A вносит 50 → `Balance = 150`. Сохраняет.
   - Процесс B снимает 30 → `Balance = 70`. Сохраняет.
   - **Итог:** `Balance = 70`, хотя должно быть `120` (100 + 50 - 30).

2. **Нарушение бизнес-правил:**
   - Агрегат: `Account` с правилом «Баланс не может быть отрицательным».
   - Процесс A и B одновременно снимают 80 с баланса 100.
   - Оба проверяют `100 >= 80` → `true`.
   - Оба сохраняют `20`.
   - **Итог:** Баланс `20`, хотя должно быть `0` (или ошибка).

---

### Как проектировать с учётом конкурентности?

Есть 3 основных подхода, от простого к сложному.

#### 1. Оптимистическая блокировка (Optimistic Locking) — **Рекомендуемый**

**Идея:** Не блокируем данные, а проверяем, не изменились ли они с момента чтения. Если изменились — конфликт.

**Как реализуется:**
1. Добавляем в таблицу агрегата поле **`version`** (целое число).
2. При чтении агрегата запоминаем его `version`.
3. При сохранении выполняем `UPDATE ... WHERE id = ? AND version = ?`.
4. Если `affected rows = 0` — значит, кто-то другой изменил данные раньше. Бросаем исключение `OptimisticLockException`.

**Пример в PostgreSQL:**
```sql
-- Таблица
CREATE TABLE accounts (
    id UUID PRIMARY KEY,
    balance DECIMAL,
    version INTEGER NOT NULL DEFAULT 0
);

-- Обновление
UPDATE accounts 
SET balance = balance - 30, 
    version = version + 1 
WHERE id = '123e4567' 
  AND version = 5; -- Версия, которую мы прочитали
```

**В Doctrine:**
```php
// Entity
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;
    
    #[ORM\Column(type: 'decimal')]
    private string $balance;
    
    #[ORM\Version] // Автоматически управляет полем version
    private int $version;
    
    // ...
}
```

**В коде:**
```php
try {
    $account = $repo->find($id);
    $account->withdraw(new Money(30, 'USD'));
    $em->flush();
} catch (OptimisticLockException $e) {
    // Конфликт! Повторить попытку или сообщить об ошибке.
    throw new DomainException('Conflict: Account was modified by another transaction');
}
```

**Плюсы:**
- ✅ Нет блокировок → высокая пропускная способность.
- ✅ Простота: не нужно управлять жизненным циклом блокировок.
- ✅ Идеально для сценариев с **редкими конфликтами** (например, баланс счета).

**Минусы:**
- ❌ Если конфликты частые — много повторных попыток.
- ❌ Нужно обрабатывать исключения в каждом месте изменения.

---

#### 2. Пессимистическая блокировка (Pessimistic Locking)

**Идея:** Блокируем строку в БД на время транзакции. Другие процессы ждут.

**Как реализуется:**
1. При чтении агрегата используем `SELECT ... FOR UPDATE`.
2. БД блокирует строку до `COMMIT` или `ROLLBACK`.
3. Другие процессы, пытающиеся прочитать/записать, ждут.

**В Doctrine:**
```php
// В репозитории
public function findForUpdate(string $id): ?Account
{
    $em = $this->getEntityManager();
    return $em->createQuery(
        'SELECT a FROM App\Entity\Account a WHERE a.id = :id'
    )
    ->setParameter('id', $id)
    ->setLockMode(LockMode::OPTIMISTIC) // или PESSIMISTIC
    ->getOneOrNullResult();
}
```

Или через QueryBuilder:
```php
$qb = $em->createQueryBuilder()
    ->select('a')
    ->from(Account::class, 'a')
    ->where('a.id = :id')
    ->setParameter('id', $id);

$account = $qb->getQuery()
    ->setLockMode(LockMode::PESSIMISTIC_WRITE)
    ->getOneOrNullResult();
```

**Плюсы:**
- ✅ Гарантированная последовательность: только один процесс изменяет одновременно.
- ✅ Нет конфликтов, только ожидание.

**Минусы:**
- ❌ Блокировки: низкая пропускная способность.
- ❌ Риск дeadlocks (циклических блокировок).
- ❌ Долгие транзакции держат блокировки.

**Когда использовать:**
- Для **критических** операций (операция с деньгами, снятие с баланса).
- Когда конфликты **частые**.

---

#### 3. Очереди событий (Event Sourcing / Command Queue)

**Идея:** Изменения агрегата обрабатываются **последовательно** одним воркером.

**Как реализуется:**
1. Команда `WithdrawMoney` попадает в очередь (RabbitMQ, Redis).
2. Один воркер (или воркер с `PARTITION BY account_id`) обрабатывает команды для этого агрегата.
3. Гарантируется последовательность.

**Плюсы:**
- ✅ Идеальная масштабируемость.
- ✅ Нет блокировок.
- ✅ Легко отследить историю.

**Минусы:**
- ❌ Сложность: нужна инфраструктура (очереди, воркеры).
- ❌ Асинхронность: клиент не сразу видит результат.
- ❌ Сложнее отлаживать.

**Когда использовать:**
- Для очень высоконагруженных систем.
- Когда асинхронность допустима.

---

### Сравнение подходов

| Критерий | Optimistic Locking | Pessimistic Locking | Event Sourcing |
| :--- | :---: | :---: | :---: |
| **Производительность** | Высокая (при редких конфликтах) | Средняя (блокировки) | Высокая (параллельная) |
| **Сложность** | Низкая | Средняя | Высокая |
| **Конфликты** | Обработка исключениями | Ожидание | Последовательность |
| **Масштабируемость** | Средняя | Низкая | Высокая |
| **Применимость** | Редкие конфликты | Частые конфликты | Очень высокая нагрузка |

---

### Рекомендация

**Для большинства приложений используйте Optimistic Locking.**

**Почему:**
1. Конфликты в бизнес-приложениях — редкость.
2. Простота реализации.
3. Нет блокировок → высокая пропускная способность.

**Если конфликты частые:**
1. Используйте **Pessimistic Locking** только для критических операций.
2. Или переключитесь на **Event Sourcing**.

**Важно:**
- Всегда проверяйте `version` при обновлении.
- Обрабатывайте `OptimisticLockException` в сервисном слое.
- Логгируйте конфликты для анализа.

**Пример полного решения:**

```php
// 1. В сущности
#[ORM\Entity]
class Account
{
    #[ORM\Version]
    private int $version;
    // ...
}

// 2. В сервисе
public function withdraw(string $accountId, Money $amount): void
{
    $this->em->transactional(function (EntityManagerInterface $em) use ($accountId, $amount) {
        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            throw new NotFoundException('Account not found');
        }
        
        $account->withdraw($amount);
        $em->flush();
    });
}

// 3. В контроллере
try {
    $this->accountService->withdraw($id, $amount);
} catch (OptimisticLockException $e) {
    return new Response('Conflict: Please try again', 409);
} catch (DomainException $e) {
    return new Response($e->getMessage(), 422);
}
```

**Вывод:** Проектируйте систему с учётом конкурентности. **Optimistic Locking** — это лучший баланс между простотой и надёжностью для большинства случаев.

> ⏱ 13.3s · prompt 36207 tok · output 1674 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это мощный инструмент для **развязки** (decoupling) компонентов системы и реализации асинхронного взаимодействия. Однако неправильное их использование приводит к «магическому» поведению, сложной отладке и проблемам с транзакциями.

### 1. Когда публиковать доменные события?

**Правильно:**
- Когда **состояние агрегата изменилось** и это изменение имеет бизнес-значение.
- Примеры: `OrderCreated`, `OrderPaid`, `UserRegistered`, `PaymentProcessed`.
- Событие описывает **факт** («Order has been paid»), а не команду («Pay the order»).

**Неправильно:**
- Для внутренних, технически незначимых изменений.
- Для синхронного взаимодействия между объектами одного агрегата (используйте метод вызова).
- Для передачи данных (события не должны нести большой payload).

---

### 2. Как публиковать и обрабатывать?

#### Вариант А: События в рамках одной транзакции (Local Domain Events)

Это наиболее безопасный и простой подход. События публикуются и обрабатываются **до** `COMMIT` транзакции.

**Архитектура:**
1. Агрегат накапливает события при изменении состояния.
2. After-commit (или в конце Application Service) события публикуются в Event Bus.
3. Обработчики (Handlers) исполняются **синхронно** в той же транзакции.
4. Если обработчик бросит исключение — вся транзакция откатывается.

**Код:**

```php
// App/Domain/Order/Order.php
class Order
{
    private array $domainEvents = [];
    
    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::Pending) {
            throw new DomainException('Order is not pending');
        }
        
        $this->status = OrderStatus::Paid;
        
        // Копируем событие в коллекцию
        $this->domainEvents[] = new OrderPaidEvent($this->id, $this->total);
    }
    
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = []; // Очищаем, чтобы не публиковать дважды
        return $events;
    }
}
```

```php
// App/Application/Service/OrderService.php
class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function payOrder(string $orderId): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) use ($orderId) {
            
            // 1. Загрузка и изменение агрегата
            $order = $em->getRepository(Order::class)->find($orderId);
            $order->markAsPaid();
            
            // 2. Сохранение в БД
            $em->flush();
            
            // 3. Публикация доменных событий (СИНХРОННО)
            foreach ($order->pullDomainEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }
            
            // Если диспетчер бросит исключение — транзакция откатится
        });
    }
}
```

```php
// App/Application/Handler/SendReceiptHandler.php
class SendReceiptHandler
{
    public function __construct(private MailerInterface $mailer) {}
    
    public function __invoke(OrderPaidEvent $event): void
    {
        // Логика отправки чека
        // Если здесь будет ошибка — транзакция откатится!
        $this->mailer->send(...);
    }
}
```

**Плюсы:**
- ✅ Транзакционная целостность: либо всё применилось, либо ничего.
- ✅ Простота отладки: все изменения в одной транзакции.
- ✅ Нет «пропавших» событий.

**Минусы:**
- ❌ Если обработчик медленный (например, отправка email) — транзакция становится долгой.
- ❌ Если обработчик упадет — весь контекст откатится.

---

#### Вариант Б: События после транзакции (Outbox Pattern / Async)

Для асинхронных операций (email, push-уведомления, индексация) лучше использовать **Outbox Pattern**.

**Архитектура:**
1. Агрегат накапливает события.
2. В той же транзакции, что и изменение агрегата, события пишутся в таблицу `outbox`.
3. Отдельный **Worker** периодически читает из `outbox`, отправляет в очередь (RabbitMQ/Redis) и помечает событие как обработанное.
4. Ошибка в обработчике не откатывает основную транзакцию.

**Код:**

```php
// App/Infrastructure/Persistence/Entity/Outbox.php
#[ORM\Entity]
class Outbox
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;
    
    #[ORM\Column(type: 'string')]
    private string $eventType;
    
    #[ORM\Column(type: 'json')]
    private array $payload;
    
    #[ORM\Column(type: 'boolean')]
    private bool $published = false;
}
```

```php
// App/Application/Service/OrderService.php
public function payOrder(string $orderId): void
{
    $this->em->transactional(function (EntityManagerInterface $em) use ($orderId) {
        
        $order = $em->getRepository(Order::class)->find($orderId);
        $order->markAsPaid();
        
        // Сохраняем агрегат
        $em->flush();
        
        // Записываем события в Outbox (в той же транзакции!)
        foreach ($order->pullDomainEvents() as $event) {
            $outbox = new Outbox(
                (string) Uuid::generateV4(),
                get_class($event),
                $event->toArray()
            );
            $em->persist($outbox);
        }
        
        $em->flush();
    });
    // Транзакция завершена. События в БД, но еще не отправлены.
}
```

```php
// App/Infrastructure/Message/OutboxWorker.php
class OutboxWorker
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $messageBus
    ) {}

    public function process(): void
    {
        // Периодически запускается (через Cron или Long-running Process)
        $outboxItems = $this->em->createQuery(
            'SELECT o FROM App\Infrastructure\Persistence\Entity\Outbox o WHERE o.published = false'
        )->setMaxResults(100)->getResult();
        
        foreach ($outboxItems as $item) {
            try {
                // Восстанавливаем событие и отправляем в очередь
                $event = $this->eventSerializer->deserialize(
                    $item->getEventType(),
                    $item->getPayload()
                );
                
                $this->messageBus->dispatch($event);
                
                $item->markAsPublished();
                $this->em->flush();
            } catch (\Throwable $e) {
                // Логгируем ошибку, но не откатываем основную транзакцию
                $this->logger->error('Failed to publish outbox item', ['id' => $item->getId()]);
                $item->incrementRetryCount();
                $this->em->flush();
            }
        }
    }
}
```

**Плюсы:**
- ✅ Основное изменение и запись в Outbox — атомарны.
- ✅ Обработка событий асинхронна: не блокирует ответ пользователю.
- ✅ Ошибки в обработчиках не влияют на основной контекст.

**Минусы:**
- ❌ Сложность: нужен Worker, логика повторов, Dead Letter Queue.
- ✅ Задержка: событие обработается не мгновенно.

---

### 3. Какие проблемы решают доменные события?

1. **Развязка (Decoupling):**
   - `OrderService` не знает, что после оплаты нужно отправить email, обновить индекс и начислить баллы.
   - Каждый подписчик работает независимо.

2. **Расширяемость (OCP):**
   - Чтобы добавить новый сценарий (например, «отправить SMS»), создаем новый `Handler` и регистрируем его. Не трогаем основной код.

3. **Асинхронность:**
   - Медленные операции (email, HTTP-вызовы) не блокируют основной поток.

4. **Наблюдаемость:**
   - История событий в Outbox или очереди позволяет отслеживать, что произошло в системе.

---

### 4. Какие проблемы создаёт?

1. **Несинхронность:**
   - Состояние системы может быть неконсистентным в кратковременном окне.
   - Нужно проектировать идемпотентные обработчики.

2. **Сложность отладки:**
   - Причинно-следственные связи становятся неочевидными.
   - Нужно отслеживать поток событий.

3. **Двойные записи (Double Processing):**
   - При повторе (retry) событие может быть обработано дважды.
   - Обработчики должны быть **идемпотентными**.

4. **Порядок обработки:**
   - Если события обрабатываются параллельно, порядок может нарушиться.
   - Нужно использовать Partitioning или Serial Processing.

---

### Вывод

| Подход | Когда использовать |
| :--- | :--- |
| **Local Events (Sync)** | Для критически важных, быстрых операций. Когда нужна транзакционная целостность. |
| **Outbox Pattern (Async)** | Для асинхронных, медленных операций. Когда нужна масштабируемость и отказоустойчивость. |

**Рекомендация:**
- Начните с **Local Events** для простых сценариев.
- Переходите на **Outbox Pattern**, когда появляются асинхронные операции или система становится масштабной.
- Всегда делайте обработчики **идемпотентными**.

> ⏱ 16.8s · prompt 40104 tok · output 2367 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальные концепции DDD, которые определяют архитектуру вашего кода. Путаница между Entity и Value Object (VO) — одна из самых частых ошибок, ведущая к «анемической доменной модели».

### 1. Entity vs Value Object: Ключевые отличия

| Характеристика | **Entity** | **Value Object (VO)** |
| :--- | :--- | :--- |
| **Идентичность** | **Есть** (UUID, ID). Два объекта с одинаковыми полями — разные сущности. | **Нет**. Два объекта с одинаковыми полями — **один и тот же** объект. |
| **Изменяемость** | **Изменяемая** (Mutable). Меняется состояние во времени. | **Неизменяемый** (Immutable). Новый объект = новое значение. |
| **Сравнение** | По **ID**. `$entity1->getId() === $entity2->getId()`. | По **значению**. `$vo1 == $vo2` (все поля равны). |
| **Жизненный цикл** | Сложный (создание, изменение, удаление). | Простой (создание, использование, выброс). |
| **Циклические ссылки** | Допустимы. | **Запрещены** (или усложняют логику). |

**Пример:**
- **Entity:** `Order` (Заказ). Два заказа с одинаковой суммой и товарами — это **разные** заказы (разные ID).
- **Value Object:** `Money` (Деньги). `100 USD` и `100 USD` — это **одно и то же** значение.

---

### 2. Как решить, что выбрать?

Задайте себе три вопроса:

1. **Имеет ли объект «историю»?**
   - Если да (его меняют во времени, следят за его ID) → **Entity**.
   - Если нет (это просто данные) → **Value Object**.

2. **Важно ли, что это «этот конкретный» объект, а не «какой-то такой»?**
   - Если важно (например, «этот пользователь», «этот товар») → **Entity**.
   - Если важно только значение (например, «100 долларов», «email: user@mail.com») → **Value Object**.

3. **Можно ли заменить объект другим, если значение такое же?**
   - Если да → **Value Object**.
   - Если нет → **Entity**.

**Практические примеры:**

| Концепция | Тип | Почему? |
| :--- | :--- | :--- |
| `Order` (Заказ) | Entity | Уникален, имеет историю статусов, ID. |
| `Money` (Деньги) | Value Object | 100$ = 100$, независимо от того, в каком заказе. |
| `Email` (Почта) | Value Object | user@mail.com — это значение, а не сущность. |
| `User` (Пользователь) | Entity | Уникален, имеет ID, историю. |
| `Address` (Адрес) | Value Object | Два человека могут жить по одному адресу. |
| `PhoneNumber` (Телефон) | Value Object | Номер — это данные. |
| `Tag` (Тег) | **Спорно** | Если у тега есть ID и история — Entity. Если это просто строка — Value Object. |
| `OrderItem` (Позиция заказа) | **Спорно** | Часто делают Value Object внутри Order (Aggregate), чтобы упростить управление. |

---

### 3. Unified Language (Единый язык) и его влияние на код

**Unified Language (UB)** — это общий словарь, который используют **разработчики, аналитики и бизнес**.

**Почему это важно?**
- **Снижение энтропии:** Термин «Order» в коде значит то же, что «Заказ» для бизнес-аналитика.
- **Меньше багов:** Если бизнес говорит «отмена заказа», а разработчик понимает «удаление заказа» — будет баг.
- **Читаемость кода:** Код становится самодокументируемым.

**Как UB влияет на код?**

1. **Имена классов и методов:**
   ```php
   // Плохо (технический язык)
   class DataProcessor {
       public function processItem(Item $item) { ... }
   }
   
   // Хорошо (бизнес-язык)
   class Order {
       public function cancel() { ... }
       public function pay() { ... }
   }
   ```

2. **Имена полей:**
   ```php
   // Плохо
   $order->total; // А это сумма с НДС? Без НДС? Итоговая к оплате?
   
   // Хорошо
   $order->totalWithTax;
   $order->totalWithoutTax;
   $order->amountToPay;
   ```

3. **Имена исключений:**
   ```php
   // Плохо
   throw new Exception('Error 105');
   
   // Хорошо
   throw new OrderAlreadyPaidException('Order has already been paid');
   ```

4. **Имена событий:**
   ```php
   // Плохо
   event('item_105_action_3');
   
   // Хорошо
   OrderPaidEvent, OrderCancelledEvent
   ```

**Пример создания UB:**

| Бизнес-термин | Техническая реализация |
| :--- | :--- |
| **Заказ** | `Order` (Entity) |
| **Позиция заказа** | `OrderLineItem` (Entity/VO) |
| **Сумма** | `Money` (Value Object) |
| **Отмена заказа** | `Order::cancel()` |
| **Оплата** | `Order::pay()`, `Payment` (Entity) |
| **Статус** | `OrderStatus` (Enum) |
| **Недостаточно средств** | `InsufficientFundsException` |

---

### 4. Типовые ошибки

1. **Entity без логики (Anemic Domain Model):**
   ```php
   // Плохо: Entity — просто мешок данных
   class Order {
       public function __construct(public $total) {}
   }
   
   // Хорошо: Entity инкапсулирует бизнес-логику
   class Order {
       private Money $total;
       
       public function applyDiscount(Money $discount): void {
           if ($discount->greaterThan($this->total)) {
               throw new DomainException('Discount too big');
           }
           $this->total = $this->total->subtract($discount);
       }
   }
   ```

2. **Value Object как Entity:**
   ```php
   // Плохо: Money с ID и изменяемым состоянием
   class Money {
       public $id;
       public function __construct($id) { $this->id = $id; }
       public function setAmount($amount) { $this->amount = $amount; }
   }
   
   // Хорошо: Money — неизменяемый
   final class Money {
       public function __construct(
           private int $amount,
           private string $currency
       ) {}
       
       public function add(Money $other): self {
           // ...
           return new self($newAmount, $this->currency);
       }
   }
   ```

3. **Смешение технического и бизнес-языка:**
   ```php
   // Плохо
   $user->save(); // save — технический термин
   $order->persist(); // persist — технический термин
   
   // Хорошо
   $user->register(); // register — бизнес-термин
   $order->place(); // place — бизнес-термин
   ```

---

### Вывод

1. **Entity** — это «кто/что» (уникальные объекты с историей).
2. **Value Object** — это «какое» (неизменяемые значения).
3. **Unified Language** — это мост между бизнесом и кодом. Он делает код понятным, предсказуемым и легким в поддержке.

**Совет:** Перед написанием кода проведите **Event Storming** с бизнес-аналитиком. Выявите доменные события, сущности и бизнес-правила. Это поможет правильно выбрать Entity/VO и выстроить Unified Language.

> ⏱ 15.8s · prompt 42531 tok · output 1910 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Структура приложения, основанная на принципах **Чистой Архитектуры** (Clean Architecture) и **DDD** (Domain-Driven Design), строится вокруг **правила зависимостей**: зависимость всегда указывает извне внутрь.

### Правило зависимостей

```
[UI / Adapters] -> [Application] -> [Domain]
     [Infrastructure] (зависит от Domain и Application)
```

*   **Domain** (Внутреннее ядро) не знает о существе внешних компонентов (БД, HTTP, очереди).
*   **Application** (Средний слой) координирует работу домена, но не знает о деталях реализации.
*   **UI / Infrastructure** (Внешний слой) знает о Domain и Application, но не наоборот.

---

### Структура проекта (Symfony + DDD)

Типичная структура каталогов:

```
app/
├── config/                  # Конфигурация Symfony (services, routes)
├── public/                  # Входные точки (index.php)
├── src/
│   ├── Domain/              # 1. СЛОЙ ДОМЕНА (Чистый PHP)
│   │   ├── Order/
│   │   │   ├── Order.php            # Агрегат
│   │   │   ├── OrderId.php          # Value Object
│   │   │   ├── Money.php            # Value Object
│   │   │   ├── OrderRepository.php  # Интерфейс (Contract)
│   │   │   ├── OrderPaidEvent.php   # Доменное событие
│   │   │   ├── OrderCancelledEvent.php
│   │   │   └── Exceptions/
│   │   │       ├── OrderNotFoundException.php
│   │   │       └── InsufficientFundsException.php
│   │
│   ├── Application/         # 2. СЛОЙ ПРИЛОЖЕНИЯ (Координация)
│   │   ├── Order/
│   │   │   ├── UseCases/
│   │   │   │   ├── CreateOrder/
│   │   │   │   │   ├── CreateOrderCommand.php # Входящая команда
│   │   │   │   │   ├── CreateOrderHandler.php # Логика
│   │   │   │   └── CreateOrderResponse.php  # Выходные данные
│   │   │   ├── Handlers/
│   │   │   │   └── SendReceiptHandler.php # Обработчик доменного события
│   │   │   └── Services/
│   │   │       └── OrderService.php     # Тонкий сервис (если нужен)
│   │
│   ├── Infrastructure/      # 3. СЛОЙ ИНФРАСТРУКТУРЫ (Адаптеры)
│   │   ├── Persistence/     # БД (Doctrine)
│   │   │   ├── Entity/
│   │   │   │   ├── OrderEntity.php
│   │   │   │   └── MoneyEntity.php
│   │   │   ├── Repository/
│   │   │   │   └── OrderRepository.php  # Реализация интерфейса Domain
│   │   │   ├── Mapper/
│   │   │   │   └── OrderMapper.php      # Домен <-> Entity
│   │   │   └── Connection/
│   │   │       └── DatabaseConnectionFactory.php
│   │   │
│   ├── UI/                  # 4. СЛОЙ ИНТЕРФЕЙСА (Входные точки)
│   │   ├── Http/            # API / Веб
│   │   │   ├── Controller/
│   │   │   │   └── OrderController.php
│   │   │   ├── Request/
│   │   │   │   └── CreateOrderRequest.php
│   │   │   └── Response/
│   │   │       └── OrderResponse.php
│   │   │
│   ├── Console/             # CLI Команды
│   │   └── Command/
│   │       └── ConsumeOrdersCommand.php
│   │
│   ├── EventSubscriber/     # Подписчики на события Symfony
│   │   └── KernelEventSubscriber.php
│   │
│   └── DependencyInjection/ # Конфигурация DI (Services)
│       └── ServiceLocator.php
│
└── tests/
    ├── Unit/
    │   ├── Domain/
    │   ├── Application/
    │   └── Infrastructure/
    └── Functional/
        └── API/
```

---

### Куда попадают конкретные компоненты?

| Компонент | Слой | Почему? |
| :--- | :--- | :--- |
| **Doctrine Entities** | **Infrastructure** | Это техническая реализация хранения. Домен не знает о таблицах. |
| **Doctrine Repository** | **Infrastructure** | Реализует интерфейс из домена. Зависит от EntityManager. |
| **Symfony Controller** | **UI (Http)** | Входная точка. Превращает HTTP-запрос в Command, вызывает Handler, возвращает Response. |
| **Обработчик очереди (Messenger Handler)** | **Application** | Координирует домен. Получает Message, вызывает UseCase, обрабатывает события. |
| **Доменные События** | **Domain** | Описание бизнес-фактов. Не знают о транспортах (RabbitMQ, Redis). |
| **Value Objects** | **Domain** | Чистая бизнес-логика. |

---

### Как следить за границами?

#### 1. Статический анализ кода

*   **PHPStan / Psalm:** Настройте строгие правила. Убедитесь, что `Domain` не импортирует классы из `Infrastructure` или `Symfony`.
*   **Arch (php-arch):** Библиотека для проверки архитектурных правил.
    ```php
    // arch.json
    {
        "rules": [
            {
                "name": "Domain should not depend on Infrastructure",
                "from": "App\\Domain\\*",
                "to": "App\\Infrastructure\\*"
            },
            {
                "name": "Application should not depend on UI",
                "from": "App\\Application\\*",
                "to": "App\\UI\\*"
            }
        ]
    }
    ```

#### 2. Code Review

*   **Правило:** Никаких `use Symfony\...` в `Domain/` и `Application/`.
*   **Правило:** Никаких `use Doctrine\ORM` в `Domain/`.
*   **Правило:** Контроллеры не должны содержать бизнес-логику.

#### 3. Тестирование

*   **Unit-тесты Domain:** Должны проходить без запуска БД, без Symfony.
*   **Unit-тесты Application:** Используют Mock-и репозиториев.
*   **Functional-тесты:** Показывают, что интеграция работает.

#### 4. Компонентные тесты

*   Проверка, что `OrderRepository` (Infrastructure) действительно реализует интерфейс `OrderRepository` (Domain).

---

### Пример потока (Order Creation)

1. **UI (Http):**
   ```php
   class OrderController {
       public function create(Request $request, CreateOrderHandler $handler): Response
       {
           $command = $this->mapper->mapRequestToCommand($request);
           $response = $handler->handle($command);
           return $this->mapper->mapResponseToHttp($response);
       }
   }
   ```

2. **Application (UseCase):**
   ```php
   class CreateOrderHandler {
       public function __construct(
           private OrderRepository $orderRepo,
           private EventDispatcherInterface $dispatcher
       ) {}
       
       public function handle(CreateOrderCommand $command): CreateOrderResponse
       {
           $order = new Order(
               OrderId::generate(),
               $command->getItems()
           );
           
           $order->calculateTotal();
           
           $this->orderRepo->save($order);
           
           foreach ($order->pullDomainEvents() as $event) {
               $this->dispatcher->dispatch($event);
           }
           
           return new CreateOrderResponse($order->getId());
       }
   }
   ```

3. **Domain (Logic):**
   ```php
   class Order {
       public function calculateTotal(): void {
           // Чистая бизнес-логика
       }
       
       public function pullDomainEvents(): array {
           return $this->domainEvents;
       }
   }
   ```

4. **Infrastructure (Persistence):**
   ```php
   class OrderRepository implements DomainOrderRepository {
       public function __construct(private EntityManagerInterface $em) {}
       
       public function save(Order $order): void {
           $entity = $this->mapper->toEntity($order);
           $this->em->persist($entity);
           $this->em->flush();
       }
   }
   ```

---

### Вывод

1. **Domain** — чистый PHP, без внешних зависимостей.
2. **Application** — координация, без деталей реализации.
3. **Infrastructure** — адаптеры (БД, очередь, API).
4. **UI** — входные точки (HTTP, CLI, Email).
5. **Правило зависимостей:** Внешнее зависит от внутреннего.
6. **Контроль:** PHPStan, Arch, Code Review, Тесты.

Эта структура обеспечивает **предсказуемость**, **тестируемость** и **масштабируемость** приложения.

> ⏱ 15.7s · prompt 44514 tok · output 2199 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony-приложения с DDD-архитектурой и PostgreSQL я придерживаюсь **инвертированной пирамиды** (или «перевернутого колпачка»).

**Почему?**
*   **Unit-тесты** (низ): Быстрые, изолированные, покрывают доменную логику. Их **много**.
*   **Integration-тесты** (середина): Проверяют интеграцию с БД (Doctrine), очереди. Их **умеренное количество**.
*   **Functional/E2E-тесты** (верх): Проверяют полный сценарий через HTTP. Их **немного**, они самые дорогие и хрупкие.

---

### 1. Что мокаем, а что нет?

#### ✅ МОКАЕМ (Заменим двойниками)

1.  **Внешние сервисы:**
    *   `MailerInterface` (отправка писем).
    *   `HttpClient` (вызовы API).
    *   `PaymentGateway` (платежи).
    *   *Почему:* Они медленные, нестабильные, имеют побочные эффекты.

2.  **Репозитории (в Application-тестах):**
    *   В тестах `UseCase` мы мокаем `OrderRepository`, чтобы проверить, что UseCase правильно взаимодействует с доменом и передает правильные данные в репозиторий.
    *   *Почему:* Это быстрее и изолирует бизнес-логику.

3.  **Время:**
    *   Используем `TimeService` или `Clock` для контроля времени (например, «текущий момент» в тестах).
    *   *Почему:* Избегание зависимости от системного времени.

4.  **Randomness:**
    *   `RandomIdGenerator` (UUID).
    *   *Почему:* Предсказуемость результатов.

#### ❌ НЕ МОКАЕМ (Проверяем реальным)

1.  **Доменные объекты:**
    *   `Order`, `Money`, `Email` — тестируем реальными объектами.
    *   *Почему:* Это ядро бизнеса. Моки здесь теряют смысл.

2.  **Doctrine ORM (в Integration-тестах):**
    *   Проверяем маппинг, транзакции, lazy loading, constraints.
    *   *Почему:* Баги часто возникают именно на стыке домена и БД.

3.  **PostgreSQL (в Integration-тестах):**
    *   Используем реальную БД (или Testcontainers).
    *   *Почему:* SQLite не поддерживает все фичи PostgreSQL (JSONB, CTE, оконные функции). Моки БД — это иллюзия.

4.  **Сериализация JSON:**
    *   Проверка, что DTO корректно мапится в JSON.
    *   *Почему:* Частый источник багов (неверные имена полей, типы).

---

### 2. Типы тестов в Symfony

#### А. Unit-тесты (Покрывают Domain и Application)

**Стек:** `PHPUnit` или `Pest` + `Mockery`/`Prophecy`.

**Пример: Тест домена**
```php
// tests/Unit/Domain/Order/OrderTest.php
class OrderTest extends TestCase
{
    public function testCannotWithdrawMoreThanBalance(): void
    {
        // Arrange
        $order = new Order(
            OrderId::generate(),
            new Money(100, 'USD')
        );
        
        // Act / Assert
        $this->expectException(InsufficientFundsException::class);
        $order->withdraw(new Money(150, 'USD'));
    }
    
    public function testCalculateTotalWithDiscount(): void
    {
        $order = new Order(...);
        $order->applyDiscount(new Money(10, 'USD'));
        
        $this->assertEquals(new Money(90, 'USD'), $order->getTotal());
    }
}
```

**Пример: Тест UseCase (Application)**
```php
// tests/Unit/Application/Order/UseCases/CreateOrder/CreateOrderHandlerTest.php
class CreateOrderHandlerTest extends TestCase
{
    public function testShouldSaveOrderAndPublishEvent(): void
    {
        // Arrange
        $repo = $this->createMock(OrderRepository::class);
        $repo->expects($this->once())->method('save');
        
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('dispatch');
        
        $handler = new CreateOrderHandler($repo, $dispatcher);
        
        // Act
        $handler->handle(new CreateOrderCommand(...));
    }
}
```

**Скорость:** Миллисекунды. **Количество:** 70-80% всех тестов.

---

#### Б. Integration-тесты (Покрывают Infrastructure)

**Стек:** `PHPUnit` + `Symfony` Kernel + `Testcontainers` (PostgreSQL).

**Пример: Тест репозитория**
```php
// tests/Integration/Infrastructure/OrderRepositoryTest.php
class OrderRepositoryTest extends BaseIntegrationTestCase
{
    public function testSaveAndFindOrder(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $repo = static::getContainer()->get(OrderRepository::class);
        
        // Arrange
        $order = new Order(...);
        
        // Act
        $repo->save($order);
        $em->flush();
        
        // Assert
        $found = $repo->find($order->getId());
        $this->assertNotNull($found);
        $this->assertEquals($order->getTotal(), $found->getTotal());
    }
}
```

**Важно:**
*   Используем **Testcontainers** для поднятия реального PostgreSQL.
*   Каждый тест работает в своей транзакции или использует `DatabaseTruncator`.
*   Проверяем `Constraints` (UNIQUE, NOT NULL, CHECK).

**Скорость:** Секунды (настройка БД). **Количество:** 15-20% всех тестов.

---

#### В. Functional-тесты (Покрывают UI и сценарии)

**Стек:** `Symfony BrowserKit` / `Panther` (Selenium) + `Testcontainers`.

**Пример: Тест API**
```php
// tests/Functional/Api/OrderApiTest.php
class OrderApiTest extends WebTestCase
{
    public function testCreateOrderEndpoint(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/orders', [
            'json' => [
                'items' => [...],
            ]
        ]);
        
        $this->assertResponseIsSuccessful();
        $this->assertJson([
            'id' => '...',
            'total' => 100,
        ]);
        
        // Проверяем, что в БД появилась запись
        $em = static::getContainer()->get('doctrine')->getManager();
        $this->assertNotNull(
            $em->getRepository(OrderEntity::class)->findOneBy(['total' => 100])
        );
    }
}
```

**Важно:**
*   Не тестируем каждый запрос в БД (это делают Integration-тесты).
*   Проверяем **контракт API** и **поведение сценария**.
*   Используем фикстуры данных (Faker).

**Скорость:** Секунды-минуты. **Количество:** 5-10% всех тестов.

---

### 3. Как поддерживать тесты быстрыми и стабильными?

#### 1. Изоляция данных

*   **Testcontainers:** Поднимайте реальный PostgreSQL для каждого запуска тестов. Это убирает зависимость от локального окружения.
*   **DatabaseTruncator:** Чистите таблицы перед каждым тестом (или транзакцией).
*   **Фикстуры:** Используйте `Faker` для генерации данных. Не храните тестовые данные в файлах.

#### 2. Мокирование внешних сервисов

*   В Functional-тестах используйте `RequestMatcher` или `HttpClientMock`, чтобы не вызывать реальные API.
*   Пример:
    ```php
    $client->followRedirects();
    $response = $client->request('POST', '/api/orders');
    
    // Мокируем отправку email
    $mailer = static::getContainer()->get(MailerInterface::class);
    $mailer->expects($this->once())->method('send');
    ```

#### 3. Управление временем

*   В тестах используйте фиксированное время.
    ```php
    $timeService = static::getContainer()->get(TimeService::class);
    $timeService->freezeAt(new \DateTime('2023-01-01 00:00:00'));
    ```

#### 4. Параллельное выполнение

*   Используйте `PHPUnit --processes auto` или `Pest --parallel`.
*   Убедитесь, что тесты не имеют общих состояний (глобальные переменные, статические свойства).

#### 5. Стабильность (Flaky Tests)

*   **Избегайте `sleep()`:** Заменяйте ожиданием на событие или условие.
*   **Порядок не важен:** Каждый тест должен быть независимым.
*   **Логирование:** Включите логирование в тестах, чтобы видеть, что произошло.
*   **Тайм-ауты:** Настройте разумные тайм-ауты для HTTP-запросов.

#### 6. Инструменты

| Инструмент | Назначение |
| :--- | :--- |
| **Testcontainers** | Поднятие реальных БД/сервисов в Docker. |
| **Faker** | Генерация тестовых данных. |
| **PHPUnit / Pest** | Фреймворк тестирования. |
| **Mockery / Prophecy** | Создание моков. |
| **Paratest** | Параллельное выполнение тестов. |
| **Cypress / Playwright** | E2E-тесты на фронтенде (если есть). |

---

### Вывод

1. **Пирамида:** Много Unit-тестов, умеренное количество Integration, немного Functional.
2. **Моки:** Внешние сервисы, репозитории (в Application), время, рандом.
3. **Реальность:** Домен, Doctrine, PostgreSQL, JSON-сериализация.
4. **Стабильность:** Testcontainers, изоляция данных, параллельное выполнение.

Такой подход обеспечивает **быструю обратную связь** (Unit-тесты), **надёжность** (Integration-тесты с реальным БД) и **качество сценариев** (Functional-тесты).

> ⏱ 19.3s · prompt 46772 tok · output 2540 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Это классическая проблема **конкурентного доступа (Race Conditions)**. Когда два пользователя одновременно модифицируют одну и ту же сущность, возникает риск **утраты обновлений (Lost Update)** или нарушения бизнес-инвариантов.

### 1. Какие проблемы возникают?

1. **Lost Update (Утрата обновления):**
   - Пользователь A читает `balance = 100`.
   - Пользователь B читает `balance = 100`.
   - A вносит 50 → `balance = 150`. Сохраняет.
   - B снимает 30 → `balance = 70`. Сохраняет.
   - **Итог:** `balance = 70`, хотя должно быть `120`.

2. **Нарушение бизнес-правил:**
   - Правило: «Баланс не может быть отрицательным».
   - A и B одновременно снимают 80 с баланса 100.
   - Оба проверяют `100 >= 80` → `true`.
   - Оба сохраняют `20`.
   - **Итог:** Баланс `20`, хотя должно быть `0` (или ошибка).

3. **Циклические блокировки (Deadlocks):**
   - Если транзакции блокируют разные ресурсы в разном порядке, они могут ждать друг друга бесконечно.

---

### 2. Способы защиты

#### Способ 1: Оптимистическая блокировка (Optimistic Locking) — **Рекомендуемый по умолчанию**

**Идея:** Не блокируем данные, а проверяем, не изменились ли они с момента чтения. Если изменились — конфликт.

**Реализация:**
1. В таблице сущности есть поле `version` (целое число).
2. При чтении запоминаем `version`.
3. При обновлении: `UPDATE ... SET ..., version = version + 1 WHERE id = ? AND version = ?`.
4. Если `affected rows = 0` — конфликт.

**В Doctrine:**
```php
#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;
    
    #[ORM\Column(type: 'decimal')]
    private string $amount;
    
    #[ORM\Version] // Автоматически управляет полем version
    private int $version;
}
```

**В сервисе:**
```php
try {
    $order = $repo->find($id);
    $order->changeAmount(new Amount(100));
    $em->flush();
} catch (OptimisticLockException $e) {
    throw new DomainException('Conflict: Order was modified by another user');
}
```

**Плюсы:**
- ✅ Нет блокировок → высокая пропускная способность.
- ✅ Простота: не нужно управлять жизненным циклом блокировок.
- ✅ Идеально для сценариев с **редкими конфликтами**.

**Минусы:**
- ❌ Если конфликты частые — много повторных попыток.
- ❌ Нужно обрабатывать исключения в каждом месте изменения.

**Когда использовать:**
- Для большинства бизнес-сущностей (заказы, профили, документы).
- Когда конфликты **редки**.

---

#### Способ 2: Пессимистическая блокировка (Pessimistic Locking)

**Идея:** Блокируем строку в БД на время транзакции. Другие пользователи ждут.

**Реализация:**
1. `SELECT ... FOR UPDATE`.
2. БД блокирует строку до `COMMIT`/`ROLLBACK`.

**В Doctrine:**
```php
$order = $em->createQuery(
    'SELECT o FROM App\Entity\Order o WHERE o.id = :id'
)
->setParameter('id', $id)
->setLockMode(LockMode::PESSIMISTIC_WRITE)
->getOneOrNullResult();
```

**Плюсы:**
- ✅ Гарантированная последовательность.
- ✅ Нет конфликтов, только ожидание.
- ✅ Простота логики: не нужно обрабатывать конфликты.

**Минусы:**
- ❌ Блокировки: низкая пропускная способность.
- ❌ Риск дeadlocks.
- ❌ Долгие транзакции держат блокировки.

**Когда использовать:**
- Для **критических** операций (операции с деньгами, снятие с баланса).
- Когда конфликты **частые**.
- Когда важно, чтобы пользователь **не терял** обновление (лучше подождать, чем повторить).

---

#### Способ 3: Атомарные операции на уровне БД

**Идея:** Не считывать и не пересылать данные, а выполнять операцию прямо в БД.

**Пример:**
```sql
UPDATE accounts 
SET balance = balance - 30 
WHERE id = '123' AND balance >= 30;
```

**Плюсы:**
- ✅ Атомарность: операция атомарна на уровне БД.
- ✅ Не нужно загружать данные в PHP.
- ✅ Высокая производительность.

**Минусы:**
- ❌ Сложная логика (если нужно проверить много условий) сложно выразить в SQL.
- ❌ Нарушение инкапсуляции: бизнес-логика уходит в БД.

**Когда использовать:**
- Для простых операций (инкремент, декремент, суммирование).
- Когда логика простая и хорошо выражается в SQL.

---

#### Способ 4: Очереди событий (Event Sourcing / Command Queue)

**Идея:** Изменения агрегата обрабатываются **последовательно** одним воркером.

**Плюсы:**
- ✅ Идеальная масштабируемость.
- ✅ Нет блокировок.
- ✅ Легко отследить историю.

**Минусы:**
- ❌ Сложность: нужна инфраструктура (очереди, воркеры).
- ❌ Асинхронность: клиент не сразу видит результат.

**Когда использовать:**
- Для очень высоконагруженных систем.
- Когда асинхронность допустима.

---

### Сравнение подходов

| Критерий | Optimistic Locking | Pessimistic Locking | Атомарные операции | Event Sourcing |
| :--- | :---: | :---: | :---: | :---: |
| **Производительность** | Высокая (при редких конфликтах) | Средняя (блокировки) | Высокая | Высокая (параллельная) |
| **Сложность** | Низкая | Средняя | Низкая | Высокая |
| **Конфликты** | Обработка исключениями | Ожидание | Нет конфликтов | Последовательность |
| **Масштабируемость** | Средняя | Низкая | Высокая | Высокая |
| **Применимость** | Редкие конфликты | Частые конфликты | Простые операции | Очень высокая нагрузка |

---

### Рекомендация

**Для большинства приложений используйте Optimistic Locking.**

**Почему:**
1. Конфликты в бизнес-приложениях — редкость.
2. Простота реализации.
3. Нет блокировок → высокая пропускная способность.

**Если конфликты частые:**
1. Используйте **Pessimistic Locking** только для критических операций.
2. Или переключитесь на **Event Sourcing**.

**Важно:**
- Всегда проверяйте `version` при обновлении.
- Обрабатывайте `OptimisticLockException` в сервисном слое.
- Логгируйте конфликты для анализа.

> ⏱ 11.7s · prompt 49364 tok · output 1751 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой git-процесс построен на принципах **предсказуемости**, **атомарности изменений** и **минимизации конфликтов**. Я использую гибридный подход, сочетающий строгость **GitFlow** (для релизов) с гибкостью **Trunk-Based Development** (для ежедневной разработки).

### 1. Структура веток

| Ветка | Назначение | Жизненный цикл |
| :--- | :--- | :--- |
| `main` | Стабильная продакшен-версия. Всегда готова к деплою. | Постоянная |
| `develop` | Интеграционная ветка. Содержит код, готовый к тестированию. | Постоянная |
| `feature/*` | Новая функциональность. | Кратковременная (1-5 дней) |
| `bugfix/*` | Исправление критических багов в `develop`. | Кратковременная |
| `hotfix/*` | Критический баг в `main` (продакшен). | Мгновенная |
| `release/*` | Подготовка релиза (баг-фиксы, патчи). | Временная |

**Почему так?**
- `main` и `develop` дают четкое разделение: «что уже в проде» и «что скоро в проде».
- Кратковременные ветки уменьшают время жизни кода → меньше конфликтов.
- `hotfix` позволяет быстро чинить прод, не дожидаясь следующего релиза.

---

### 2. Формат коммитов (Conventional Commits)

Я строго следую стандарту **Conventional Commits**. Это не просто «красиво», а механизм для автосгенерации changelog и автоматических версионирований.

**Формат:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Примеры:**
```bash
# Правильно:
feat(order): add payment validation
fix(auth): handle expired token edge case
refactor(dao): extract query builder logic
docs(api): update order endpoint schema

# Плохо:
fixed stuff
updated code
wip
```

**Почему так?**
1. **Автоматизация:** Инструменты (стандарты, changelog-боты) понимают типы коммитов.
2. **Читаемость истории:** `git log --oneline` становится самодокументируемым.
3. **Версионирование:** `feat` → minor version, `fix` → patch version, `!` → major version.

---

### 3. Pull Request (PR) Process

**Правило:** Каждый PR должен быть **атомарным** и **тестируемым**.

**Checklist для PR:**
1. **Название:** `<type>(<scope>): <subject>` (как коммит).
2. **Описание:**
   - **Что изменилось:** Краткое описание.
   - **Почему:** Ссылка на задачу (Jira, Linear).
   - **Как тестировать:** Шаги для QA или коллеги.
   - **Скриншоты/видео:** Если есть UI-изменения.
3. **Код:**
   - Никакого `console.log`, `TODO`, `HACK` (если только не обосновано).
   - Покрыто тестами.
   - Локально прогнана полная пирамида тестов.
4. **Ревью:**
   - Минимум 1 approval.
   - Все CI-проверки (lint, tests, build) пройдены.
   - Нет конфликтующих коммитов.

**Почему так?**
- **Атомарность:** Если PR сломает прод, легко откатить один конкретный чанк кода.
- **Ревью:** Второй взгляд ловит баги, которые автор не видит.
- **CI:** Автоматизация гарантирует, что код не сломан базовыми правилами.

---

### 4. Code Review

**Принципы:**
1. **Комментировать код, а не автора.**
2. **Блокирующие комментарии** (должны быть исправлены) vs **Ненормирующие** (рекомендации).
3. **Не переревьюить то, что уже согласовано.**
4. **Исследовать, а не допрашивать.** (Вопрос «Почему ты написал это?» лучше «Зачем здесь `try-catch`?»)

**Инструменты:**
- GitHub/GitLab PR comments.
- Встроенные линтеры (PHPStan, Psalm) в CI.
- Автоматические проверки форматов (Prettier, CS Fixer).

**Почему так?**
- **Эффективность:** Ревью — это самая дорогая часть процесса. Автоматизация ловит синтаксис, человек ловит логику.
- **Уважение:** Культура, в которой код обсуждают конструктивно, снижает барьер для разработчиков.

---

### 5. Релизы и Деплой

**Процесс релиза:**
1. **Сборка `release/x.y.z`** из `develop`.
2. **Тестирование** на staging-окружении.
3. **Исправление багов** в ветке `release` (не в `develop`!).
4. **Мердж** `release` → `main` (с тегом `v1.2.3`).
5. **Мердж** `release` → `develop` (чтобы фиксы попали в main).
6. **Деплой** из `main`.

**Тегирование версий:**
- `v1.2.3` — Minor update.
- `v2.0.0` — Major update (breaking changes).

**Почему так?**
- **Предсказуемость:** Всегда известно, какой код будет в проде.
- **Откат:** Легко откатиться на предыдущий тег (`git revert` или деплой предыдущего артефакта).
- **Чистота `develop`:** Баг-фиксы из релиза возвращаются в `develop`, чтобы не потерять их в следующем релизе.

---

### 6. Почему не Trunk-Based только?

Trunk-Based (разработка только в `main`) требует:
- Отличной автоматизации (CI/CD).
- Коротких feature flags.
- Высокой зрелости команды.

**Для большинства команд GitFlow гибче:**
- Можно иметь «долгие» фичи, не мешая другим.
- Легко изолировать нестабильный код.
- Проще объяснить новичкам.

**Компромисс:**
- `develop` работает как «малый main».
- Ветки живут не более 3-5 дней.
- Если фича долго — используем **Feature Flags**.

---

### Итоговый чек-лист для команды

1. **Ветка:** `feature/task-123-name`.
2. **Коммиты:** Conventional Commits.
3. **PR:** Атомарный, с описанием, покрыт тестами.
4. **Ревью:** 1 approval, все CI-хуки зеленеют.
5. **Релиз:** Только через `release` ветку.
6. **Деплой:** Только из `main`.

Этот процесс балансирует между **гибкостью** (быстрая разработка) и **надёжностью** (предсказуемые релизы).

> ⏱ 14.6s · prompt 51174 tok · output 1713 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью — это не поиск «багов любой ценой» и не проверка орфографии. Это **перенос контекста** от автора кода к другому разработчику. Цель — убедиться, что код делает то, что нужно, и что он легко будет поддерживаться через 6 месяцев.

Мой процесс делится на три стадии: **Сканирование** (структура), **Чтение** (логика), **Размышление** (наследие).

### 1. Маркировка замечаний

Чтобы автор понимал, что нужно исправить обязательно, а что — пожелание, я строго разделяю комментарии по приоритетам:

*   **🔴 Blocking (Must Fix):** Код сломан, нарушает бизнес-логику, есть баги, утечки памяти или критические ошибки безопасности. PR не принимается без исправления.
    *   *Пример:* «Здесь `null` может упасть, если список пуст», «Забыли закрыть транзакцию».
*   **🟡 Important (Should Fix):** Логика верна, но код сложен в чтении, нарушает принципы SOLID, есть дублирование или плохие имена. Желательно исправить до мержа, но можно поправить позже (с тикетом), если дедлайн горит.
    *   *Пример:* «Этот метод слишком длинный, вынесите валидацию в отдельный класс», «Имя переменной `data` не говорит ничего».
*   **🟢 Nice to Have / Question (Could Fix):** Предложения по улучшению стиля, вопросов «почему так?», альтернативных подходов. Не блокирует мерж.
    *   *Пример:* «Можно было бы использовать `match` вместо `switch`», «Почему здесь `try-catch`, а не проверка условия?»
*   **💡 Learning / Nit:** Обучение или мелкие штрихи.
    *   *Пример:* «Интересный подход! В PHP 8.2 есть `readonly` свойства, это было бы короче».

### 2. На что смотрю в первую очередь (Приоритеты)

Я читаю код в порядке убывания критичности:

1.  **Цель и контекст:** Что вообще делает этот PR? Соответствует ли он задаче из тикета? (Если да — отлично, если нет — спрашиваю).
2.  **Корректность логики:** Делает ли код то, что обещает? Есть ли пропущенные краевые случаи (Empty arrays, null, negative numbers)?
3.  **Безопасность:** SQL-инъекции, XSS, утечка чувствительных данных в логах, права доступа.
4.  **Исключения и ошибки:** Что происходит, если БД упала? Если API вернул 500? Обрабатываются ли ошибки или «проглатываются» пустым `catch`?
5.  **Тестируемость:** Легко ли написать юнит-тест для этого кода? Есть ли циклические зависимости?
6.  **Производительность:** N+1 запросы, лишний копирование больших массивов, вложенные циклы.
7.  **Читаемость и Naming:** Понятно ли, *что* делает код, просто глядя на имена методов и переменных.
8.  **Дизайн (SOLID, DDD):** Нарушены ли границы домена? Есть ли «анемическая» модель?

### 3. Как поступаю при разногласии

Это самая важная часть. Ревью — это диалог, а не монолог. Если автор несогласен:

**Шаг 1: Запросить контекст**
Часто автор знает что-то, чего не вижу я.
*   *Фраза:* «Я вижу, что здесь можно было бы использовать паттерн X. Подскажи, почему ты выбрал текущий подход? Есть ли какие-то технические ограничения, о которых я не знаю?»

**Шаг 2: Разделить «Вкус» и «Правила»**
*   Если нарушение **жесткого правила** (стандарт стиля, принцип SRP, критический баг) — настаиваю (🔴/🟡).
*   Если это **предпочтение** (нравится ли мне один синтаксис больше другого) — уступаю (🟢). Автор кода — его владелец.

**Шаг 3: Поиск компромисса**
*   *Вариант А:* Исправить прямо сейчас.
*   *Вариант Б:* Создать отдельный тикет на рефакторинг («Технический долг») и записать его в описание PR.
*   *Вариант В:* Провести короткую голосовую/видеозвон (15 минут) для обсуждения на доске или в Zoom. Часто это быстрее, чем 20 сообщений в чате.

**Шаг 4: Принцип «Disagree and Commit»**
Если мы все еще не нашли консенсуса, а дедлайн близок, автор кода имеет право вето. Мы выбираем вариант, который он считает лучшим, и фиксируем это в коде комментарием. Но этот спор должен быть задокументирован (в тикете), чтобы не повторять одни и те же дебаты в следующем PR.

### Типовые ошибки ревьюера (и как я их избегаю)

1.  **Замечание по коду, который скоро будет удален.** (Спрашиваю: «Это финальная итерация?»)
2.  **Комментарий по стилю в первом же предложении.** (Сначала хвалю за общую архитектуру, потом указываю на мелкие штрихи).
3.  **Замечание без предложения.** (Лучше: «Можно было бы использовать `array_filter`, это короче» вместо просто «Слишком длинно»).
4.  **Игнорирование тестов.** (Код без тестов — это лотерея. Смотрю и на сами тесты: они проверяют поведение, а не реализацию).

**Итог:** Хороший ревью — это когда автор после мержа считает, что код стал лучше, а не чувствует, что его работу переделали. Это про сотрудничество, а не про контроль.

> ⏱ 14.3s · prompt 52937 tok · output 1439 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Проблема «пользователи не могут найти заказы» — это **симптом**, а не задача. Если передать её разработчику как есть, он сделает «кнопку Поиск», но проблема может остаться (например, заказы ищутся, но медленны, или теряются после удаления).

Вот как я трансформирую это сырое требование в **исполнимую задачу** для разработчика (Jira/Linear ticket):

---

### Задача: Оптимизация поиска заказов по истории пользователя

**Цель (Goal):**
Снизить количество обращений в поддержку по теме «Поиск заказа» на 50% в течение 2 недель после релиза.

**Проблема (Context):**
Пользователи сообщают, что не могут найти свои прошлые заказы. Анализ поддержки показывает, что 70% обращений связаны с:
1. Невозможностью найти заказ по номеру телефона, на который он оформлялся.
2. Невозможностью найти заказ, если он был оформлен через 3-й месяц назад.
3. Медленной загрузкой списка заказов (более 3 секунд).

**Данные для анализа (Evidence):**
- [Ссылка на дашборд обращений в поддержку с выделенной темой]
- [Ссылка на Sentry/логи с ошибками загрузки `/api/orders`]
- [Результаты опроса пользователей: 12/20 респондентов указали на проблему с поиском по телефону]

**Приемочные критерии (Acceptance Criteria):**

1. **Функциональность поиска:**
   - [ ] В разделе «Мои заказы» добавлен поиск по: `ID заказа`, `email`, `номер телефона`, `имени товара`.
   - [ ] Поиск работает **реализованно** (не только в текущем месяце, а за все время).
   - [ ] Результаты поиска отсортированы по дате (сначала новые).
   - [ ] При вводе запроса в реальном времени (debounce 300ms) результаты обновляются без перезагрузки страницы.
   - [ ] Если результатов нет — показывается понятное сообщение: «По запросу "X" ничего не найдено. Попробуйте изменить запрос или обратитесь в поддержку».

2. **Производительность:**
   - [ ] Время отклика API `/api/orders/search?q=...` не превышает 500ms на 95-м перцентиле (P95).
   - [ ] Запрос не выполняет `N+1` запросов к БД (проверить через `EXPLAIN ANALYZE`).
   - [ ] Добавлен индекс в PostgreSQL на поля, используемые в поиске.

3. **Удобство (UX):**
   - [ ] Поисковая строка видна сразу при входе в раздел «Мои заказы» (без необходимости прокрутки).
   - [ ] История последних поисков сохраняется (максимум 5) и доступна для быстрого повторного использования.

4. **Тестирование:**
   - [ ] Unit-тесты для логики поиска (поиск по email, по ID, по товару).
   - [ ] Integration-тесты для проверки индексов и производительности.
   - [ ] E2E-тесты (Cypress/Playwright) для проверки сценария «Пользователь вводит email → видит заказ».

**Что НЕ входит в задачу (Out of Scope):**
- [ ] Поиск по содержимому товара (внутри JSON-полей, если они не проиндексированы).
- [ ] Поиск по другим пользователям (чужие заказы).
- [ ] Фильтры по дате, статусу, сумме (отдельная задача).
- [ ] Мобильное приложение (только веб-интерфейс).

**Технические требования (Hints for Dev):**
- Рассмотреть использование `tsvector`/`tsquery` (PostgreSQL Full-Text Search) или `GIN` индексов для полей `email`, `phone`.
- Если объем данных > 1 млн записей — рассмотреть эластичный поиск (Elasticsearch/Meilisearch) **только** если PostgreSQL не справится с P95 < 500ms.
- Использовать существующую инфраструктуру поиска (если есть) или добавить пакет `symfony/orm` для DQL-запросов.

**Критерии готовности к релензу (Definition of Done):**
- [ ] Код покрыт тестами, CI зеленая.
- [ ] Миграция БД прошла успешно на staging.
- [ ] Проверено с Product Owner на реальном сценарии.
- [ ] Документация API обновлена (Swagger/Postman).

---

### Почему я включил именно это?

1. **Цель (Goal) с метрикой:** Без цифры «50%» команда не поймет, когда задача считается выполненной. Это переводит задачу из «сделать» в «решить бизнес-проблему».
2. **Данные (Evidence):** Разработчик должен понимать, **почему** это нужно. Если он увидит, что 70% жалоб про телефон — он сделает поиск по телефону приоритетным, а не по ID.
3. **Приемочные критерии:** Это контракт между автором задачи и исполнителем. Убрано место для домыслов («а поиск по дате нужен? — нет, не входит»).
4. **Out of Scope:** Самое важное. Это защищает разработчика от «заглатывания» чужих задач. Если он сделает фильтры по дате — это будет вне задачи, и за него не похвалят.
5. **Технические требования (Hints):** Я не навязываю решение (Elasticsearch), но даю подсказку (PostgreSQL Full-Text Search). Это экономит время: разработчик сразу знает, куда смотреть, но может предложить лучшее.
6. **Производительность (P95):** Без этого разработчик может сделать «рабочий» поиск, который будет тормозить. П95 < 500ms — это измеримый критерий.
7. **Тестирование:** Я указываю, **какие** тесты нужны (Unit, Integration, E2E). Это гарантирует, что поиск не сломается через месяц.

### Почему так, а не иначе?

Если я просто напишу «Сделать поиск заказов», то:
- Разработчик сделает поиск только по ID (самый простой вариант).
- Пользователи продолжат жаловаться (ищут по email).
- Через месяц будет новый тикет «Добавить поиск по email».
- Итог: 2 тикета, 2 недели разработки, бизнес недоволен.

С моей формулировкой:
- Разработчик сразу понимает, что нужен поиск по email, ID, товару.
- Он проверяет производительность.
- Бизнес видит снижение жалоб.
- Итог: 1 тикет, 1 неделя разработки, бизнес доволен.

**Вывод:** Хорошая задача — это **преобразование неопределенности в измеримые критерии**. Чем меньше вопросов разработчик будет задавать «А что если...?», тем быстрее он работает.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Прежде чем написать хотя бы строку кода, я проведу «диссекцию» этой проблемы. Фраза «пользователи не могут найти заказы» может означать как баг в SQL-запросе, так и то, что у пользователей неправильные ожидания от интерфейса.

Вот мои вопросы, сгруппированные по уровням:

### 1. Количественные данные и аналитика (Data-Driven)
*   **Сколько именно обращений в поддержку?** Это 5 в неделю или 500 в день? (Определяет приоритет: горячий пирожок или хроническая болезнь).
*   **Какова доля «не находят» среди всех обращений по заказам?** Если 90% — это «где мой заказ, я его только что сделал», то это проблема синхронизации статусов, а не поиска.
*   **Какой «хвост» заказов?** Ищут они заказы за последние 30 дней, или 5-летние архивы? (Влияет на индексацию и выбор СУБД).
*   **Существуют ли уже логи поиска?** Что пользователи вводят в строку поиска сейчас (если она есть)? (`q=123`, `q=iphone`, `q=я потерял`)?

### 2. Конкретные сценарии (User Stories)
*   **По какому именно атрибуту они ищут?**
    *   По номеру заказа (ID)?
    *   По email, на который он оформлялся?
    *   По товару (например, «купил ботинки»)?
    *   По дате (примерно)?
*   **Кто эти «пользователи»?** Это B2C (физлица, которые забыли свой email) или B2B (менедеры, которые ищут заказы по телефону клиента)? *Это принципиально разные реализации.*
*   **Что именно означает «не могут найти»?**
    *   Ищут, но результат пустой?
    *   Ищут, но находят, но с задержкой в 10 секунд?
    *   Ищут, но находят только 10 последних, а нужно 100?

### 3. Бизнес-контекст и приоритеты
*   **Какова стоимость одного обращения в поддержку?** (Если $5, а поиск решит 500 обращений в день — это $2500/день экономии).
*   **Какой срок желаемого релиза?** (Если через 3 дня — делаем «костыль» с индексом в PostgreSQL, если через 3 недели — проектируем эластичный поиск).
*   **Что будет, если поиск не найдется?** Есть ли fallback-механизм? (Например, «Свяжитесь с нами, и мы найдем его вручную»).

### 4. Технические ограничения и текущее состояние
*   **Сколько всего заказов в БД сейчас?** (10 тыс. или 10 млн? Подход к поиску будет разным).
*   **Есть ли уже индексация полнотекстового поиска?** Или мы работаем с `LIKE '%...%'`?
*   **Какой стек хранения сейчас используется?** (PostgreSQL, MySQL, или уже есть Elasticsearch?)
*   **Есть ли ограничения по бюджету/ресурсам?** (Можно ли выделить дополнительные серверы под индексы?).

### 5. Оценка успеха (Success Metrics)
*   **Как мы поймем, что задача решена?**
    *   Метрика в дашборде (падение обращений на 50%)?
    *   NPS пользователей (рост на 10 пунктов)?
    *   Скорость поиска (< 1 секунда)?
*   **Кто будет валидировать результат?** (Продукт-менеджер, аналитик, или сам разработчик?).

---

### Почему я спрашиваю именно это?

1. **Чтобы не лечить симптом.** Часто «не находят» = «заказ удален, но пользователь видит его в списке». Это баг состояния, а не проблема поиска.
2. **Чтобы выбрать правильный инструмент.** Для 1000 заказов — `LIKE` + индекс. Для 10 млн — `tsvector`. Для 100 млн — Elasticsearch.
3. **Чтобы согласовать ожидания.** Если бизнес хочет «магический поиск по всему тексту за 100мс», а бюджет — «написать на коленке», мы должны это обсудить сразу.
4. **Чтобы определить «Done».** Без четких критериев успеха мы можем написать идеальный код, но бизнес скажет: «Ну, поисковый результат не то, что мы хотели».

**Итог:** После этих ответов я смогу написать задачу не просто как «Сделать поиск», а как **«Оптимизировать запрос к таблице orders с добавлением индекса на поля (email, id), чтобы P95 запроса был < 500ms, и добавить поиск по товару через tsvector»**.

> ⏱ 15.5s · prompt 54457 tok · output 1626 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Ситуации, когда чёткие критерии приёмки сталкиваются с реальностью, — это стандартная часть разработки. Мой подход основан на принципе **минимизации рисков** и **сохранении фокуса на цели**.

### Ситуация а: Нашёл «попутный» баг

**Действия:**
1. **Останавливаюсь.** Не «чиню по пути», даже если баг выглядит тривиальным (например, опечатка в логировании или `null` в поле, которое я всё равно меняю).
2. **Документирую.** Создаю новый тикет (Bug) в трекере. Описываю:
   - Воспроизведение (шаги).
   - Ожидаемый и фактический результат.
   - Влияние на пользователей (критичность).
3. **Решаю судьбу бага:**
   - **Вариант 1 (Идеальный):** Если баг находится **в том же файле/классе**, который я уже редактирую, и его исправление не меняет логику (например, исправление `null`-кодекса) — я **сообщаю** ревьюеру/лиду: «Кстати, нашёл тут баг, можно его заодно починить?» Если одобряют — чиню.
   - **Вариант 2 (Стандартный):** Если баг в другом месте или требует тестов — **оставляю в новом тикете**. Мой PR должен делать **только** то, для чего был создан.
4. **Почему так?**
   - **Атомарность:** Если мой PR с поиском заказов упадёт в ревью из-за спорного изменения в модуле корзины — мы потеряем время.
   - **Тестируемость:** Новый баг требует новых тестов. Если я их добавляю в «чужой» PR — PR становится объёмнее и дольше ревьюится.
   - **История:** `git blame` покажет, что баг был исправлен в рамках задачи по поиску. Это может вводить в заблуждение при откате.

---

### Ситуация б: Критерий приёмки противоречит цели

**Пример:**
*   **Цель:** Снизить обращения в поддержку по поиску заказов.
*   **Критерий приёмки:** «Скорость поиска должна быть < 100мс при любой нагрузке».
*   **Противоречие:** Я понял, что для достижения < 100мс нужно добавить кеш в Redis. Но кеш усложняет логику (инвалидация), а цель — просто, чтобы пользователи **находили** заказы. При этом 90% запросов сейчас занимают 300мс, что субъективно «быстро».

**Действия:**
1. **Не молчу и не делаю так, как написано.** Не начинаю внедрять Redis, если считаю, что это оверинжиниринг.
2. **Собираю данные:**
   - Замеряю текущую скорость (P50, P95).
   - Оцениваю стоимость внедрения кеша (сложность, риски).
   - Формулирую альтернативу: «Можно достичь 200мс с помощью только оптимизации индексов в БД, что проще и надёжнее».
3. **Инициирую обсуждение** с автором задачи (Product Owner / Tech Lead):
   - *Я:* «Привет! Я начал работать над задачей. Уточнил метрики: сейчас поиск идёт 300мс. Критерий <100мс требует внедрения кеша. Но цель — снизить обращения в поддержку. Готовы ли мы принять 200мс как достаточно быстрое, или <100мс — это жёсткое требование бизнеса?»
4. **Документируем решение:**
   - Если соглашаются на 200мс — **меняем критерий приёмки** в тикете. Пишем: «*Updated AC: P95 < 200ms instead of 100ms. Reason: Simplicity and lower risk.*»
   - Если настаивают на 100мс — делаю кеш, но **флэш-комитт** с комментарием, что это было требование.
5. **Почему так?**
   - **Цель > Критерий.** Критерий — это способ достичь цели. Если есть более простой способ достичь цели — его надо использовать.
   - **Снижение Tech Debt.** Внедрение Redis ради 100мс — это технический долг. Лучше согласовать компромисс.
   - **Прозрачность.** Если я просто «нарушу» критерий и скажу потом: «Ну, 200мс же быстрее 300мс, всё ок?» — ревьюер может отвергнуть PR. Лучше договориться заранее.

---

### Итоговая стратегия

| Ситуация | Действие | Почему |
| :--- | :--- | :--- |
| **Попутный баг** | Создаю тикет, спрашиваю разрешения чинить «в лоб» | Атомарность PR, чистая история коммитов |
| **Противоречие AC** | Собираю данные, обсуждаю с PO, обновляю тикет | Цель важнее критерия, снижение рисков |

**Главный принцип:** Я не «исполнитель», который просто копит. Я **инженер**, который отвечает за **качество и целесообразность** решения. Если я вижу, что мы идём не туда — я обязан это сказать **до** того, как потратим время.

> ⏱ 12.1s · prompt 57333 tok · output 1303 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении задачи — это не просто «Всё сделал, можно мержить». Это документ, который позволяет команде принимать решение о мерже с пониманием рисков и контекста.

### 1. Отчёт при успешном выполнении

Мой отчёт (в комментарии к PR или в тикете) всегда содержит 5 блоков:

#### 1.1. Резюме (Что и зачем)
*   **Одно предложение:** «Реализован поиск заказов по email, ID и товару с оптимизацией индексов».
*   **Ссылки:** Тикет (Jira/Linear), дизайн-макеты (если были), документация API.

#### 1.2. Техническая реализация (Как)
*   **Ключевые решения:** «Использован `tsvector` для полнотекстового поиска. Добавлен индекс `gin (to_tsvector('russian', description))`.»
*   **Влияние на БД:** «Создана новая таблица `search_indices`. Миграция прошла успешно на staging.»
*   **Новые зависимости:** «Добавлен пакет `symfony/messenger` для асинхронного обновления индексов.»

#### 1.3. Тестирование (Доказательство)
*   **Покрытие:** «Unit-тесты: 15. Integration-тесты: 5. E2E-тесты: 3. Покрытие: 92%.»
*   **Проверка производительности:** «P95 запроса: 450мс (критерий: < 500мс). Проверено через `EXPLAIN ANALYZE`.»
*   **Скриншоты/Видео:** Короткое видео (15 сек), где я ввожу email и вижу заказ.

#### 1.4. Риски и лимитации (Что может пойти не так)
*   «Поиск по товарам работает только для товаров, добавленных после 01.01.2023. Старые товары будут проиндексированы фоновой задачей.»
*   «Если Redis упадёт, поиск продолжит работать, но станет медленнее (fallback на БД).»

#### 1.5. Чек-лист готовности (Definition of Done)
*   [ ] Код ревьюнут (2 approvals).
*   [ ] Все CI-хуки (lint, tests, build) зелёные.
*   [ ] Миграция БД проверена на staging.
*   [ ] Документация API обновлена.
*   [ ] Флаги (Feature Flags) установлены (если есть).

---

### 2. Что меняется, если часть задачи не удалось?

В этом случае отчёт становится **отчётом о статусе и планах**. Ключевое правило: **никогда не скрывайте, что что-то не сделано**.

#### 2.1. Структура отчёта (тоже 5 блоков, но с акцентом на «Недостатки»)

**Блок 1: Резюме**
*   «Реализован поиск по email и ID. Поиск по товарам — **частично** реализован (только для активных товаров).»

**Блок 2: Что сделано**
*   (Тот же список, что и выше, но только по выполненной части).

**Блок 3: Что НЕ сделано (и почему)**
*   **Пункт 1:** «Поиск по товарам для архивных заказов (старше 1 года) не работает.»
    *   **Причина:** «Объём данных в архиве (50 млн записей) не позволяет использовать `tsvector` в рамках одной транзакции. Нужна отдельная стратегия (Elasticsearch).»
    *   **Решение:** «Создан новый тикет [TICKET-123] «Миграция архивных заказов в Elasticsearch». Приоритет: Medium.»
*   **Пункт 2:** «Не добавлен поиск по дате.»
    *   **Причина:** «В рамках текущей задачи (Out of Scope).»
    *   **Решение:** «Помечено в документации как будущая фича.»

**Блок 4: Риски (Усиленные)**
*   «Пользователи, ищущие старые заказы, могут столкнуться с пустым результатом. **Mitigation:** Добавлена подсказка в UI: «Поиск по товарам доступен только для заказов за последние 365 дней».»
*   «Фоновая задача индексации может занять до 4 часов. **Mitigation:** Запущена ночью, не блокирует пользователей.»

**Блок 5: Чек-лист готовности**
*   [ ] Код ревьюнут (2 approvals).
*   [ ] Все CI-хуки зелёные.
*   [ ] Миграция БД проверена.
*   [ ] **Созданы тикеты на невыполненную часть.**
*   [ ] **Согласовано с Product Owner, что текущая реализация принимается как «MVP».**

---

### Ключевые отличия от «полного» отчёта

| Элемент | Полное выполнение | Частичное выполнение |
| :--- | :--- | :--- |
| **Фокус** | Доказательство корректности. | Доказательство адекватности MVP. |
| **Риски** | Минорные (edge cases). | Минорные + **существенные** (ограничения). |
| **Тикеты** | Все закрыты. | Созданы новые тикеты на «хвосты». |
| **Согласование** | Техническое. | Техническое + **бизнесовое** (PO подписал MVP). |

### Почему это важно?

1. **Прозрачность:** Команда знает, за что отвечает и чего ждать в будущем.
2. **Минимизация Tech Debt:** «Хвосты» не теряются, а живут в трекере.
3. **Управление ожиданиями:** Пользователи не будут писать в поддержку: «Я искал заказ за 2019 год, а он не нашёлся!», если в UI было предупреждение.
4. **Ускорение релиза:** Если 90% задачи сделано и это решает 90% бизнес-проблемы — мы релизим это, а не ждём идеала.

**Итог:** Отчёт — это инструмент коммуникации. Чем честнее он отражает реальность (включая ограничения), тем быстрее команда принимает решение о мерже и тем меньше сюрпризов будет в продакшене.

> ⏱ 13.3s · prompt 58680 tok · output 1516 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

В PostgreSQL **нет** прямого аналога `FORCE INDEX`, как в MySQL. Это частая точка недопонимания между разработчиками, пришедшими из MySQL/Oracle стека.

### Почему в PostgreSQL нет `FORCE INDEX`?

В MySQL `FORCE INDEX` — это **подсказка** (hint) для оптимизатора, принуждающая его использовать определённый индекс. Если оптимизатор не согласен с таким решением, он может его игнорировать (в зависимости от версии), но обычно он подчиняется.

В PostgreSQL философия оптимизатора иная:
1. **Статистика:** PostgreSQL очень точно оценивает стоимость запросов на основе статистики (через `ANALYZE`).
2. **Гибкость:** Оптимизатор сам решает, какой план optimal (лучший) на основе текущей статистики и параметров планирования (`costs`).
3. **Отсутствие "жёстких" хинтов:** PostgreSQL предпочитает, чтобы вы влияли на выбор плана через **конфигурацию** или **переписывание запроса**, а не через магические ключевые слова.

### Как в PostgreSQL "заставить" использовать индекс?

Если вы уверены, что оптимизатор выбирает неверный план (например, делает `Seq Scan` вместо `Index Scan`), у вас есть 4 легитимных способа:

#### 1. `SET LOCAL enable_seqscan = off;` (Временное отключение последовательного сканирования)

Это самый близкий аналог `FORCE INDEX`. Он запрещает оптимизатору использовать `Seq Scan`, заставляя его искать альтернативы (индексы).

```sql
-- В транзакции
BEGIN;
SET LOCAL enable_seqscan = off;

SELECT * FROM orders WHERE status = 'pending';

COMMIT;
```

**Плюсы:**
- ✅ Работает локально в текущей транзакции/сессии.
- ✅ Простой способ заставить использовать индекс.

**Минусы:**
- ❌ Глобально запрещает `Seq Scan` для всех запросов в этой сессии (до `COMMIT` или `RESET`).
- ❌ Может привести к более сложным и дорогим планам, если индекс действительно не нужен.

**Когда использовать:**
- Для отладки.
- В критических запросах, где вы точно знаете, что индекс быстрее.

---

#### 2. `SET LOCAL enable_indexscan = off;` и т.д.

Аналогично, можно отключать конкретные методы:
- `enable_nestloop = off`
- `enable_hashjoin = off`
- `enable_material = off`

Это позволяет принудительно выбрать определённый **способ соединения** (Join Strategy), а не только индекс.

---

#### 3. Переписывание запроса (Самый чистый способ)

Оптимизатор PostgreSQL часто принимает неверные решения, если:
- **Несмотря на статистику:** Статистика устарела. Решение: `ANALYZE table;`
- **Неправильная стоимость (Costs):** PostgreSQL ошибается в оценке стоимости. Решение: настройте параметры `costs` в `postgresql.conf` (например, `random_page_cost`).
- **Сложный запрос:** Разбейте сложный запрос на несколько простых подзапросов или CTE.

**Пример:**
```sql
-- Плохо: Оптимизатор может выбрать Seq Scan
SELECT * FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.status = 'pending';

-- Хорошо: Убедитесь, что индекс существует и используйте более точный WHERE
SELECT * FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.status = 'pending' AND o.created_at > NOW() - INTERVAL '30 days';
```

---

#### 4. `Index Only Scan` через Covering Index

Если оптимизатор выбирает `Index Scan` (возврат к таблице), а вы хотите `Index Only Scan` (только индекс), создайте **покрывающий индекс**:

```sql
CREATE INDEX idx_orders_status_covering 
ON orders (status) 
INCLUDE (id, created_at);
```

Теперь оптимизатор предпочтёт `Index Only Scan`, потому что это дешевле.

---

### Когда применять "принудительное" использование индекса?

1. **Оптимизатор ошибается:** Вы точно знаете, что `Index Scan` быстрее, но PostgreSQL выбирает `Seq Scan`.
   - *Проверка:* Сравните план через `EXPLAIN ANALYZE` с `enable_seqscan = off`.
2. **Маленькие таблицы:** Для таблиц на < 1000 строк `Seq Scan` часто быстрее, чем `Index Scan`. Не форсируйте индекс.
3. **Критические запросы:** Для самых дорогих запросов в системе можно использовать `SET LOCAL` в транзакции.

### Пример использования в коде (PHP/Doctrine)

```php
public function findPendingOrders(): array
{
    $em = $this->entityManager;
    
    // Включаем транзакцию
    $em->transactional(function (EntityManagerInterface $em) {
        
        // Принудительно отключаем Seq Scan
        $em->getConnection()->executeStatement('SET LOCAL enable_seqscan = off');
        
        // Выполняем запрос
        $orders = $em->createQuery(
            'SELECT o FROM App\Entity\Order o WHERE o.status = :status'
        )
        ->setParameter('status', 'pending')
        ->getResult();
        
        // ... логика ...
    });
    
    // После COMMIT настройки сбрасываются автоматически
    return $orders;
}
```

### Вывод

- **В PostgreSQL нет `FORCE INDEX`.**
- **Ближайший аналог:** `SET LOCAL enable_seqscan = off;`
- **Лучший способ:** Оптимизация запроса, индексов и статистики (`ANALYZE`).
- **Правило:** Принудительное использование индекса — это **временно** и **точечно**. Если вам постоянно приходится использовать `SET LOCAL`, значит, проблема в статистике или структуре индексов.

> ⏱ 12.5s · prompt 60223 tok · output 1404 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

**Короткий ответ:** `strict_types=1` замедляет выполнение PHP-кода на **0%–2%** в типичных сценариях. В редких крайних случаях (миллионы вызовов функций с аргументами `int`/`string`) потеря может достигать **5%**, но это не является нормой.

**Главный вывод:** Стоимость `strict_types` — это **цена за надёжность**. Она настолько мала, что её можно пренебречь по сравнению с выигрышем от раннего обнаружения ошибок.

---

### Почему он вообще может замедлять код?

PHP — динамически типизированный язык. При передаче аргумента в функцию PHP выполняет **типпроверку**.

#### Без `strict_types` (слабая типизация):
1. PHP видит аргумент `int` и параметр `int` → совпадение.
2. PHP видит аргумент `'10'` и параметр `int` → **совпадение по значению** (приведение `'10'` → `10`).
3. PHP видит аргумент `10.0` и параметр `int` → **совпадение** (приведение `10.0` → `10`).

#### С `strict_types` (строгая типизация):
1. PHP видит аргумент `int` и параметр `int` → совпадение.
2. PHP видит аргумент `'10'` и параметр `int` → **несовпадение типов** → `TypeError`.
3. PHP видит аргумент `10.0` и параметр `int` → **несовпадение типов** → `TypeError`.

**Стоимость проверки:**
- Проверка `type === expected_type` — это одна операция сравнения целых чисел (внутренний ZVAL-тип).
- Это **очень дешёвая** операция для CPU.

---

### Почему замедление минимально?

1. **Проверка типов происходит один раз на вызов функции.**
   - Если функция вызывается 1 миллион раз — 1 миллион проверок.
   - Но каждая проверка — это ~1 наносекунда.
   - 1 миллион проверок ≈ 1 мс.
   - Для запроса, который занимает 50–200 мс, это **0.5–2%**.

2. **PHP оптимизирует это на уровне Zend Engine.**
   - Проверка типов встроена в bytecode.
   - Нет никаких дополнительных аллокаций памяти.
   - Нет вызовов функций — только сравнение констант.

3. **Современные CPU очень быстрые.**
   - Операция сравнения — это базовая операция процессора.
   - Она дешевле, чем, например, обращение к массиву или вызов метода.

---

### Когда замедление может быть заметным?

1. **Горячие циклы с миллионами вызовов.**
   - Если у вас есть функция, которая вызывается внутри `foreach` по массиву из 10 млн элементов, и в ней передаются аргументы разных типов — да, потеря может быть 3–5%.
   - Но это уже **микрооптимизация**.

2. **Union Types и Intersection Types.**
   - Проверка `int|string` дороже, чем проверка `int`, потому что PHP должен проверить **оба** типа.
   - Но даже в этом случае потеря — 1–2%.

3. **Частые `TypeError`.**
   - Если в вашем коде много `TypeError` (то есть вы передаёте неправильные типы) — да, это замедляет, потому что PHP создаёт объект исключения, делает stack trace и т.д.
   - Но это уже **баг**, а не стоимость `strict_types`.

---

### Что важнее: скорость или надёжность?

**Надёжность.**

| Критерий | Без `strict_types` | С `strict_types` |
| :--- | :--- | :--- |
| **Скорость** | 100% (база) | 98–100% (минус 0–2%) |
| **Надёжность** | Низкая (баги на рантайме) | Высокая (ошибки на этапе вызова) |
| **Отладка** | Сложнее (типы "плавают") | Легче (TypeError сразу) |
| **Поддержка** | Сложнее (непонятно, какие типы ожидаются) | Легче (типы явные) |

**Вывод:** Потеря 0–2% скорости — это **незаметная** цена за то, что ваши баги с типами будут найдены **раньше**.

---

### Практический совет

1. **Всегда включайте `strict_types=1`** во всех файлах.
2. **Не бойтесь Union Types** (`int|string`) — они стоят дёшево.
3. **Не оптимизируйте** до того, как профиль показывает, что проверка типов — узкое место.
4. **Используйте `strict_types`** как часть вашей **культуры качества**, а не как инструмент производительности.

**Итог:** `strict_types=1` — это **выигрыш** для вашего проекта, а не потеря.

> ⏱ 10.5s · prompt 61665 tok · output 1205 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

**Уточнение:** Doctrine ORM **поддерживает** составные первичные ключи (Composite Primary Keys). Однако он делает это **неудобно** и с рядом ограничений, из-за которых многие разработчики предпочитают избегать их в пользу суррогатных ключей (UUID/BigInt).

Давайте разберёмся, как это устроено, почему это неудобно и как с этим живут на практике.

---

### 1. Как Doctrine поддерживает составные ключи?

Doctrine позволяет объявить несколько полей как `#[ORM\Id]`:

```php
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $orderId;
    
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $itemId;
    
    // ... другие поля
}
```

**Проблемы этого подхода:**

1. **Нет единого ID:** У сущности нет одного `getId()`. Чтобы получить ключ, нужно собирать массив: `['orderId' => ..., 'itemId' => ...]`.
2. **Сложность маппинга:** При загрузке из БД Doctrine должен собирать объект из нескольких полей. Это усложняет гидраторы.
3. **Сложность ассоциаций:** Связи `ManyToOne` на составные ключи работают, но требуют явного указания всех полей в `@JoinColumn`.
4. **Сложность тестирования:** Сравнение объектов по равенству (equality) становится неочевидным.
5. **Проблемы с `EAGER` загрузкой:** Может привести к `LazyLoadException` в некоторых сценариях.

---

### 2. Почему в Doctrine нет «нативного» составного ключа?

Это связано с архитектурой ORM:

1. **Unit of Work:** ORM отслеживает изменения объектов. Если у объекта нет единого ID, Unit of Work сложно понимает, является ли объект «новым» или «существующим».
2. **Идентификация:** В DDD сущность определяется её **идентификатором**. Составной ключ — это **массив**, а не скаляр. Это нарушает идею «сущность = объект с ID».
3. **Производительность:** Сравнение массивов дороже, чем сравнение одного `int` или `string`.

---

### 3. Как с этим живут в реальных проектах?

Есть 3 основных подхода:

#### Подход 1: Суррогатный ключ (Рекомендуемый)

Создаём отдельное поле `id` (UUID или `BigInt`), которое **уникально** для строки.

```php
#[ORM\Entity]
#[ORM\Table(name: 'order_items', uniqueConstraints: [
    new UniqueConstraint(['order_id', 'item_id'])
])]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: 'uuid')]
    private string $id;
    
    #[ORM\Column(type: 'string')]
    private string $orderId;
    
    #[ORM\Column(type: 'string')]
    private string $itemId;
    
    // ...
}
```

**Плюсы:**
- ✅ Единый `getId()`.
- ✅ Простота работы с ORM.
- ✅ Легко создавать ассоциации.
- ✅ `UNIQUE` constraint защищает от дубликатов.

**Минусы:**
- ❌ Лишнее поле в БД.
- ❌ Нужно поддерживать `UNIQUE` constraint.

**Когда использовать:**
- Для **связных таблиц** (many-to-many).
- Для **таблиц связей** (junction tables).
- В 90% случаев.

---

#### Подход 2: Составной ключ (Когда нужно)

Используем, если:
- Таблица **естественно** имеет составной ключ (например, `user_roles`).
- Нет внешних ссылок на эту таблицу.
- Таблица маленькая.

```php
#[ORM\Entity]
class UserRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $userId;
    
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $roleId;
}
```

**Плюсы:**
- ✅ Нет лишних полей.
- ✅ Естественная структура данных.

**Минусы:**
- ❌ Сложность работы с ORM.
- ❌ Сложность ассоциаций.

---

#### Подход 3: Embedded Object (Продвинутый)

Используем `Embeddable`, чтобы инкапсулировать составной ключ.

```php
#[ORM\Embeddable]
class OrderItemKey
{
    #[ORM\Column(type: 'string')]
    private string $orderId;
    
    #[ORM\Column(type: 'string')]
    private string $itemId;
    
    public function __toString(): string {
        return $this->orderId . '-' . $this->itemId;
    }
}

#[ORM\Entity]
class OrderItem
{
    #[ORM\Embedded(class: OrderItemKey::class, columnPrefix: false)]
    #[ORM\Id]
    private OrderItemKey $key;
}
```

**Плюсы:**
- ✅ Инкапсуляция.
- ✅ Чистота кода.

**Минусы:**
- ❌ Сложность для новичков.
- ❌ Ограниченная поддержка в Doctrine.

---

### 4. Вывод

| Подход | Когда использовать |
| :--- | :--- |
| **Суррогатный ключ** | Для большинства таблиц, особенно с ассоциациями. |
| **Составной ключ** | Для небольших таблиц без внешних ссылок. |
| **Embedded Object** | Для сложных структур, где нужна инкапсуляция. |

**Рекомендация:**
1. **По умолчанию используйте суррогатный ключ** (`UUID` или `BigInt`).
2. **Используйте составной ключ** только если таблица — это чистая связь (junction table) и она маленькая.
3. **Не бойтесь `UNIQUE` constraint** — это ваша защита от дубликатов.

**Почему это лучше:**
- Простота работы с ORM.
- Легкость поддержки.
- Предсказуемость поведения.

**Итог:** Doctrine поддерживает составные ключи, но **неудобно**. В 90% случаев лучше использовать суррогатный ключ + `UNIQUE` constraint.

> ⏱ 12.3s · prompt 62907 tok · output 1480 tok

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

Этот код — классический пример «спaghetti-кода» в одном контроллере. Он нарушает почти все принципы чистой архитектуры и DDD.

### Критические проблемы:

1. **SQL-инъекция:** `$id` подставляется в SQL напрямую без экранирования.
2. **Нарушение SRP:** Контроллер делает всё: запрос к БД, бизнес-логику, отправку почты, логирование.
3. **Отсутствие валидации:** Нет проверки, что `$id` существует и является валидным ID.
4. **Жёсткая связь с БД:** Контроллер знает о `PDO` и структуре таблицы `orders`.
5. **Синхронная отправка почты:** Если почтовый сервер упадёт — заказ будет оплачен, но пользователь не получит письмо.
6. **Нет транзакции:** Если `Mailer::send` упадёт после `UPDATE` — состояние будет неконсистентным.
7. **Магические строки:** `'paid'`, `'ok'`, `'not found'`.
8. **Нет обработки ошибок:** Если `$pdo->query` упадёт — пользователь получит 500 без контекста.
9. **`SELECT *`:** Загружает все колонки, даже не нужные.
10. **Нет идемпотентности:** Повторный вызов может отправить почту дважды.

---

### Как бы я переделал

#### Шаг 1: Выделение доменных сущностей

```php
// App/Domain/Order/Order.php
final class Order
{
    public function __construct(
        private string $id,
        private string $email,
        private OrderStatus $status,
    ) {}
    
    public function pay(): void
    {
        if ($this->status === OrderStatus::Paid) {
            throw new OrderAlreadyPaidException();
        }
        
        $this->status = OrderStatus::Paid;
    }
    
    public function getId(): string { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getStatus(): OrderStatus { return $this->status; }
}

// App/Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
}
```

#### Шаг 2: Реализация репозитория

```php
// App/Infrastructure/Persistence/OrderRepository.php
class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private PDO $pdo) {}
    
    public function findById(string $id): ?Order
    {
        $stmt = $this->pdo->prepare('SELECT id, email, status FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Order(
            $row['id'],
            $row['email'],
            OrderStatus::from($row['status'])
        );
    }
    
    public function save(Order $order): void
    {
        $stmt = $this->pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([
            'status' => $order->getStatus()->value,
            'id' => $order->getId(),
        ]);
    }
}
```

#### Шаг 3: Приложение-сервис (координация)

```php
// App/Application/OrderPaymentService.php
class OrderPaymentService
{
    public function __construct(
        private OrderRepositoryInterface $repo,
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
    ) {}
    
    public function pay(string $orderId): Order
    {
        $order = $this->repo->findById($orderId);
        if (!$order) {
            throw new OrderNotFoundException();
        }
        
        $order->pay();
        $this->repo->save($order);
        
        $this->bus->dispatch(new OrderPaidEvent($order->getId()));
        $this->logger->info('Order paid', ['id' => $order->getId()]);
        
        return $order;
    }
}
```

#### Шаг 4: Обработчик события

```php
// App/Application/Handler/SendPaymentEmailHandler.php
class SendPaymentEmailHandler
{
    public function __construct(private MailerInterface $mailer) {}
    
    public function __invoke(OrderPaidEvent $event): void
    {
        $order = // получить email из БД или из события
        $this->mailer->send($order->getEmail(), 'Ваш заказ оплачен');
    }
}
```

#### Шаг 5: Контроллер (тонкий слой)

```php
// App/UI/Http/OrderController.php
class OrderController
{
    public function __construct(
        private OrderPaymentService $paymentService,
        private LoggerInterface $logger,
    ) {}
    
    public function pay(Request $req): Response
    {
        $id = $req->get('order_id');
        
        if (!$id || !is_string($id) || strlen($id) > 36) {
            return new Response('Invalid order ID', 400);
        }
        
        try {
            $this->paymentService->pay($id);
            return new Response('ok', 200);
        } catch (OrderNotFoundException $e) {
            return new Response('not found', 404);
        } catch (OrderAlreadyPaidException $e) {
            return new Response('already paid', 409);
        } catch (\Throwable $e) {
            $this->logger->error('Payment failed', ['id' => $id, 'error' => $e->getMessage()]);
            return new Response('internal error', 500);
        }
    }
}
```

---

### С чего начать и почему

1. **Начать с доменной модели.** Почему? Потому что это ядро системы. Если вы правильно опишете `Order` и `OrderStatus`, остальное будет проще.
2. **Выделить репозиторий.** Почему? Чтобы контроллер не знал о БД.
3. **Внедрить сервис.** Почему? Чтобы бизнес-логика была в одном месте.
4. **Разделить почту на событие.** Почему? Чтобы не блокировать ответ и сделать систему отказоустойчивой.
5. **Добавить обработку ошибок.** Почему? Чтобы пользователь получал понятные ответы.

### Итог

| Проблема | Решение |
| :--- | :--- |
| SQL-инъекция | Prepare statements |
| Нарушение SRP | Разделение на Domain, Application, UI |
| Синхронная почта | Событие + обработчик |
| Нет транзакции | Транзакция в сервисе |
| Магические строки | Enums |
| Нет ошибок | Try-catch в контроллере |

Этот код стал **читаемым**, **тестируемым** и **масштабируемым**.

> ⏱ 11.1s · prompt 64600 tok · output 1612 tok

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

Этот код — пример **анемической доменной модели** с нарушенной инкапсуляцией. Сущность `User` знает о деталях реализации (EntityManager) и нарушает принцип **SRP**.

### Проблемы:

1. **Нарушение инкапсуляции:**
   - `EntityManagerInterface` — это техническая деталь инфраструктуры. Доменная сущность не должна знать о ней.
   - `$this->em->flush()` — сущность сама сохраняет себя в БД. Это нарушает **Single Responsibility Principle**: сущность должна отвечать за бизнес-логику, а не за персистентность.

2. **Нарушение SRP:**
   - `User` отвечает за:
     - Бизнес-логику (изменение email).
     - Персистентность (вызов `flush()`).
   - Эти две ответственности должны быть разделены.

3. **Нарушение инкапсуляции состояния:**
   - `$this->email` — приватное поле, но его изменение не валидируется.
   - Нет проверки: email валиден? не пустой? корректный формат?

4. **Нарушение OCP:**
   - Если нужно добавить новую логику (например, уведомление о смене email) — нужно менять метод `changeEmail`.

5. **Проблема с `flush()` в домене:**
   - `flush()` — это операция, которая должна происходить **после** всех изменений агрегата, а не внутри каждого метода.
   - Если `changeEmail` бросит исключение после `flush()` — БД будет обновлена, но состояние объекта может быть неконсистентным.

---

### Как исправить

#### 1. Чистая доменная сущность (без Doctrine)

```php
// App/Domain/User/User.php
final class User
{
    private string $email;
    
    public function __construct(string $email)
    {
        $this->email = $email;
        $this->validateEmail();
    }
    
    public function changeEmail(string $email): void
    {
        if ($email === $this->email) {
            return; // Идемпотентность
        }
        
        $this->validateEmail($email);
        $this->email = $email;
    }
    
    public function email(): string
    {
        return $this->email;
    }
    
    private function validateEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }
}
```

#### 2. Репозиторий (инфраструктура)

```php
// App/Infrastructure/Persistence/UserRepository.php
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}
    
    public function save(User $user): void
    {
        $entity = $this->mapper->toEntity($user);
        $this->em->persist($entity);
        $this->em->flush();
    }
    
    public function findByEmail(string $email): ?User
    {
        $entity = $this->em->getRepository(UserEntity::class)->findOneBy(['email' => $email]);
        return $entity ? $this->mapper->toDomain($entity) : null;
    }
}
```

#### 3. Сервис (приложение)

```php
// App/Application/UserService.php
class UserService
{
    public function __construct(private UserRepositoryInterface $repo) {}
    
    public function changeEmail(string $userId, string $newEmail): void
    {
        $user = $this->repo->findById($userId);
        $user->changeEmail($newEmail);
        $this->repo->save($user);
    }
}
```

#### 4. Контроллер

```php
class UserController
{
    public function __construct(private UserService $userService) {}
    
    public function updateEmail(Request $req): Response
    {
        $userId = $req->get('user_id');
        $email = $req->get('email');
        
        try {
            $this->userService->changeEmail($userId, $email);
            return new Response('Email updated', 200);
        } catch (InvalidEmailException $e) {
            return new Response('Invalid email', 422);
        } catch (UserNotFoundException $e) {
            return new Response('User not found', 404);
        }
    }
}
```

---

### Что изменилось?

| Проблема | Решение |
| :--- | :--- |
| `EntityManager` в домене | Вынесен в репозиторий (инфраструктура) |
| `flush()` в домене | Вынесен в сервис (приложение) |
| Нет валидации | Добавлена валидация email |
| Нарушение SRP | Разделение на Domain, Application, Infrastructure |
| Нет идемпотентности | Добавлена проверка `if ($email === $this->email)` |

---

### Почему так правильно?

1. **Домен чистый:** `User` не знает о БД, Doctrine, EntityManager.
2. **SRP соблюдён:** Каждый класс имеет одну ответственность.
3. **Тестируемость:** Можно написать юнит-тест для `User::changeEmail` без БД.
4. **Масштабируемость:** Если понадобится изменить email + отправить уведомление — добавим обработчик события.

---

### Вывод

Доменная сущность должна быть **чистым PHP-объектом** с бизнес-логикой. Все технические детали (БД, API, очереди) должны быть вынесены в инфраструктурный слой.

> ⏱ 9.2s · prompt 66362 tok · output 1306 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Работа с незнакомым стеком — это не про «догадаться», а про **систематическое исследование**. Мой процесс строго структурирован, чтобы минимизировать время на поиск информации и максимизировать уверенность в оценке сроков.

### Этап 1: Разведка (15–30 минут)

Прежде чем писать код, я определяю **границы задачи**.

1.  **Разбор требований:**
    *   Что именно нужно сделать? (Функциональность, а не «используй библиотеку X»).
    *   Какие ограничения? (Производительность, совместимость, уже существующие зависимости).
    *   Кто пользователь? (Внутренний сервис, внешний API, CLI).

2.  **Аудит окружения:**
    *   Какой **версии** технологии/фреймворка? (Vue 2 vs Vue 3 — это разные миры).
    *   Есть ли **документация** в репозитории? (README, CONTRIBUTING, примеры).
    *   Есть ли **прототипы** или похожие модули в коде?

3.  **Поиск «коллективного разума»:**
    *   GitHub Issues и Pull Requests (как решали другие).
    *   Официальная документация (не блоги, а docs).
    *   Дискуссионные форумы/StackOverflow (только если официальная док промолчала).

**Результат этапа:** Я знаю, какие инструменты использовать, каких версий и какие «подводные камни» у них есть.

---

### Этап 2: Прототипирование «черновика» (1–2 часа)

Я пишу **минимальный работающий пример** (POC — Proof of Concept).

1.  **Создаю «sandbox»:**
    *   Новая ветка.
    *   Минимальный тестовый сценарий.
    *   Только то, что нужно для проверки гипотезы.

2.  **Проверяю ключевые риски:**
    *   Можно ли сделать то, что требуется?
    *   Как это интегрируется с текущей архитектурой?
    *   Есть ли проблемы с производительностью на «сыром» коде?

3.  **Делаю скриншоты/логи:**
    *   Если что-то работает — фиксирую.
    *   Если что-то падает — сохраняю traceback/лог.

**Результат этапа:** У меня есть «черновик», который работает в 80% случаев. Я знаю, где могут быть боли.

---

### Этап 3: Оценка сроков (Честная математика)

Сроки я считаю **вверх**, а не вниз. Формула:

```
Срок = (Время на прототип) * 3 (коэффициент неопределенности) + Время на тесты + Время на код-ревью
```

**Пример оценки:**
*   Прототип занял 2 часа.
*   Коэффициент неопределенности x3 = 6 часов.
*   Тесты (юнит + интеграция) = 2 часа.
*   Ревью и фиксы = 2 часа.
*   **Итого:** 10 часов рабочего времени ≈ 1.5–2 дня.

Я всегда добавляю **буфер 20–30%** на «невидимые» проблемы (конфигурация, права доступа, сетевые задержки).

---

### Когда просить помощи, а когда разбираться самому?

#### ✅ Разбираюсь СЕБЯ (и держу это в секрете), если:
1.  **Проблема локальна:** Я могу воспроизвести её в изолированной среде (POC).
2.  **Время не горит:** У меня есть 3+ часа, чтобы погуглить и попробовать.
3.  **Технология документирована:** Официальная док есть, но я пока не нашел нужного раздела.
4.  **Это вопрос синтаксиса/настройки:** Не бизнес-логика, а «как правильно написать конфиг».

#### 🆘 Прошу ПОМОЩЬ у команды, если:
1.  **Проблема глобальная:** Ошибка возникает только в полной системе, но не в POC. (Это может быть проблема интеграции, а не технологии).
2.  **Блокировка > 2 часов:** Если я туплю 2 часа подряд без прогресса — я теряю время.
3.  **Архитектурное решение:** Если есть два способа решить задачу (A и B) и оба работают. Нужно мнение техлида, чтобы не создавать техдолг.
4.  **Безопасность:** Если дело касается аутентификации, шифрования, прав доступа.
5.  **Сложный баг в чужом коде:** Если я не понимаю, почему старый код работал, а теперь нет.

**Как просить помощь правильно (формула «3С»):**
1.  **Context:** «Я пытаюсь решить X».
2.  **Attempts:** «Я сделал A, B, C. Получил ошибку Y».
3.  **Hypothesis:** «Мой косяк или баг в библиотеке? Думаю, что...»

---

### Пример: Как я отвечу на вопрос о сроках

> «Я исследовал [Технология X]. Основная функция реализована в прототипе за 2 часа. Есть одна сложность с [конкретный баг/конфиг], которую я решаю сейчас.
>
> **Оценка:**
> - Основной функционал: 4 часа.
> - Тестирование и отладка: 2 часа.
> - Код-ревью и правки: 2 часа.
> - Буфер на риски: 2 часа.
>
> **Итого: 10 часов (1 рабочий день).**
>
> **Риски:** [Описать конкретный риск].
> **План:** Завтра к обеду будет готово к ревью.»

Этот ответ вызывает доверие, потому что в нем есть **прозрачность процесса** и **реалистичная оценка**.

> ⏱ 13.5s · prompt 67743 tok · output 1428 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Ситуация «оценка устарела» — это стандартная часть разработки. Главное правило: **никогда не молчи и не «сглатывай» проблему до дедлайна**.

### Мои действия (пошагово):

#### 1. **Стоп и анализ (15–30 минут)**
Прежде чем говорить, я точно формулирую, **почему** срок вырос. Это критически важно, чтобы не выглядеть как тот, кто «не справился», а как тот, кто «увидел скрытую сложность».

*   *Пример 1:* «Я думал, что нужно просто добавить поле, но оказалось, что нужно изменить схему БД и написать миграцию».
*   *Пример 2:* «Я не учел, что нужно добавить идемпотентность в сервис, потому что запросы дублируются».
*   *Пример 3:* «Встретил баг в сторонней библиотеке, на который нужно написать обходной путь (workaround)».

**Формулировка для себя:** «Срок вырос с X до Y, потому что [конкретная причина]».

#### 2. **Решение: Что делать с дедлайном?**

У меня есть 3 варианта, и я выбираю тот, который минимизирует риск для бизнеса:

*   **Вариант А: Сократить скоуп (Scope Cut)**
    *   *Когда:* Если есть «некритичные» части задачи.
    *   *Действие:* Предлагаю сделать **MVP** (минимально работоспособный продукт) к завтрашнему дню, а остальное — в следующий спринт.
    *   *Пример:* «К завтрашнему дню будет готов базовый поиск. Поиск по фильтрам (некритичная фича) сделаю во вторник».

*   **Вариант Б: Просрочить на 1 день (Delay)**
    *   *Когда:* Если задача атомарная и нельзя её резать.
    *   *Действие:* Честно говорю: «Это займет 2 дня, а не 1».
    *   *Пример:* «Схема БД сложнее, чем я думал. Нужна миграция и прогон тестов на staging. Готово к 12:00 следующего дня».

*   **Вариант В: Просить помощь (Parallel Work)**
    *   *Когда:* Если задача параллелизуемая.
    *   *Действие:* Прошу коллегу помочь с частью работы (например, написать тесты, пока я пишу код).
    *   *Пример:* «Могу закончить к завтрашнему дню, если [Коллега] поможет с настройкой CI/CD».

#### 3. **Коммуникация (Как сказать)**

Я пишу сообщение **как только понимаю, что срок не сдвинуть**. Не в конце дня, а сразу.

**Шаблон сообщения:**
> «Привет! Обновляю статус по задаче [ID].
>
> **Что изменилось:** Оказалось, что [краткая причина]. Это добавляет [X] часов работы.
>
> **Новая оценка:** [Дата/Время].
>
> **Варианты:**
> 1. Сделать MVP к завтрашнему дню (без [фича]).
> 2. Сделать всё к [новое время].
>
> Какой вариант лучше для бизнеса?»

#### 4. **Фиксация в трекере**

*   Обновляю **оценку** в Jira/Linear.
*   Добавляю комментарий с причиной.
*   Если задача разбилась — создаю подзадачу.

---

### Почему это важно?

1. **Доверие:** Если ты молчишь до дедлайна и говоришь: «Прости, не успел», команда теряет доверие. Если ты говоришь: «Я вижу сложность, вот мой план», доверие растет.
2. **Планирование:** PM и Tech Lead могут перестроить другие задачи. Если ты молчишь — они держат слоты в календаре.
3. **Качество:** Если ты торопишься в последний момент — ты напишешь больше багов. Лучше сделать чуть дольше, но качественно.

---

### Чего я НЕ делаю?

*   ❌ **Не работаю в выходные/ночь**, чтобы уложиться в срок, если это не согласовано.
*   ❌ **Не говорю «я думал, что это будет легко»**. Говорю: «Я встретил [конкретную сложность]».
*   ❌ **Не молчу до последнего момента**. Лучше сказать за 4 часа до дедлайна, чем за 1 час.

**Итог:** Сроки — это не обещание, а **оценка**. Оценка может меняться. Главное — чтобы команда знала об этом **раньше**, чем это повлияет на бизнес.

> ⏱ 11.7s · prompt 69214 tok · output 1138 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

«Достаточно хорошо» — это не абстрактное понятие, а **контекстно-зависимое решение**. Я определяю этот момент через **критерии готовности (Definition of Done)** и **оценку рисков**.

### 1. Как я решаю, когда код «достаточно хорош»?

Я использую **чек-лист «3 вопроса»**:

#### Вопрос 1: Работает ли он корректно?
- [ ] Все юнит-тесты проходят.
- [ ] Интеграционные тесты не сломаны.
- [ ] Нет `TODO`, `FIXME`, `HACK` (или они задокументированы).
- [ ] Нет предупреждений от статического анализа (PHPStan/Psalm).

**Если «нет» на любой пункт → код НЕ готов.**

#### Вопрос 2: Понятен ли он?
- [ ] Имена переменных и методов говорят о себе (названия не «data1», «process2», а «userEmail», «calculateTotal»).
- [ ] Методы короткие (до 20–30 строк).
- [ ] Глубина вложенности не более 3–4 уровней.
- [ ] Комментариев мало, а код самодокументирует себя.

**Если «нет» → код можно рефакторить, но не обязательно перед мержем.**

#### Вопрос 3: Безопасен ли он?
- [ ] Нет SQL-инъекций, XSS, утечек данных.
- [ ] Обработаны все ожидаемые ошибки (try-catch с логированием).
- [ ] Валидация входных данных (не доверяем клиенту).

**Если «нет» → код НЕ готов.**

---

### 2. Баланс между качеством и скоростью

Я использую **правило «Золотой середины»** в зависимости от **критичности задачи**:

#### Сценарий А: Горячий фикс (Hotfix)
**Цель:** Быстро починить баг в проде.
**Баланс:**
- ✅ Пишу **минимум** кода, чтобы починить.
- ✅ Добавляю **1 юнит-тест** на воспроизведение бага.
- ❌ Не рефакторю «попутные» куски кода.
- ❌ Не меняю архитектуру.
- **Критерий готовности:** Баг починен, тест написан, лог прогнан.

#### Сценарий Б: Рутинная фича (Feature)
**Цель:** Реализовать новую функцию.
**Баланс:**
- ✅ Пишу код с учетом SOLID.
- ✅ Пишу **полное** покрытие тестами (юнит + интеграция).
- ✅ Делаю **умеренный** рефакторинг (убираю дублирование, улучшаю имена).
- ❌ Не ищу идеальное решение (избегаю «premature optimization»).
- **Критерий готовности:** Все тесты проходят, код читается, ревьюер не тратит больше 30 минут на разбор.

#### Сценарий В: Критический путь (Core Logic)
**Цель:** Написать ядро системы (оплата, аутентификация, расчет).
**Баланс:**
- ✅ Пишу код **максимально аккуратно** (строгие типы, явные проверки).
- ✅ Пишу **исчерпывающие** тесты (включая edge cases).
- ✅ Делаю **глубокий** рефакторинг (выносите доменные объекты, убираете «магию»).
- ✅ Проверяю производительность (EXPLAIN, профилирование).
- **Критерий готовности:** Код можно читать как книгу, тесты покрывают все сценарии, производительность соответствует SLA.

---

### 3. Практические инструменты для баланса

#### 1. **Таймбоксирование (Timeboxing)**
- Даю себе **2 часа** на написание кода.
- Даю себе **1 час** на тесты.
- Даю себе **30 минут** на рефакторинг.
- Если рефакторинг идет дольше 30 минут — я останавливаюсь и думаю: «Я улучшаю код или играю в „идеальный мир"?»

#### 2. **Критерий «3 ревьюера»**
- Если **2 из 3** ревьюеров говорят: «Всё хорошо, можно мержить» — я мержу.
- Если **1 из 3** говорит: «А вот тут можно лучше» — я обсуждаю, но не обязательно правлю, если это не блокирует.
- Если **3 из 3** говорят: «Тут баг» — я правлю.

#### 3. **Технический долг (Technical Debt)**
- Если я вижу, что код можно улучшить, но это не критично — я **создаю тикет** на рефакторинг.
- В описании PR пишу: «Заметил, что [кусок кода] можно улучшить. Создал тикет [ID].»
- Это снимает нагрузку с текущего PR и позволяет сосредоточиться на главной задаче.

#### 4. **Принцип «Good Enough»**
- Код должен быть **лучше**, чем был до меня.
- Но он не должен быть **идеальным**.
- Идеальный код — это тот, который кто-то другой напишет через 5 лет, когда требования изменятся.

---

### 4. Типовые ошибки в балансе

| Ошибка | Как избежать |
| :--- | :--- |
| **Перфекционизм** (потрачено 5 часов на идеальное решение) | Таймбоксирование, критерий «3 ревьюера». |
| **Спешка** (код написан за 1 час, но без тестов) | Чек-лист «3 вопроса», минимальное покрытие тестами. |
| **Полуфабрикат** (код работает, но без обработки ошибок) | Обработка ошибок — часть критерия готовности. |
| **Избыточная абстракция** (5 классов для одной задачи) | Принцип YAGNI (You Aren't Gonna Need It). |

---

### Вывод

**«Достаточно хорошо» — это когда:**
1. Код работает.
2. Код понятен.
3. Код безопасен.
4. Код не блокирует других.

**Баланс я соблюдаю через:**
- **Таймбоксирование** (ограничение времени на рефакторинг).
- **Технический долг** (откладывание улучшений в тикеты).
- **Ревью** (внешний взгляд на качество).

**Главный принцип:** Качество — это не цель, а **средство** для достижения бизнес-целей. Если код решает задачу, легко поддерживается и не ломает систему — он достаточно хорош.

> ⏱ 13.8s · prompt 70409 tok · output 1584 tok

---
